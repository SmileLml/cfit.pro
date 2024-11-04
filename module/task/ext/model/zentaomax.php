<?php
/**
 * Process a task, judge it's status.
 * Extend for php warning.
 *
 * @param  object    $task
 * @access private
 * @return object
 */
public function processTask($task)
{
    $today = helper::today();

    /* Delayed or not?. */
    if($task->status !== 'done' and $task->status !== 'cancel' and $task->status != 'closed')
    {
        if($task->deadline != '0000-00-00')
        {
            $delay = helper::diffDate($today, $task->deadline);
            if($delay > 0) $task->delay = $delay;
        }
    }

    /* Story changed or not. */
    $task->needConfirm = false;
    if(!empty($task->storyStatus) and $task->storyStatus == 'active' and $task->latestStoryVersion > $task->storyVersion) $task->needConfirm = true;

    /* Set product type for task. */
    if(isset($task->product) and $task->product)
    {
        $product = $this->loadModel('product')->getById($task->product);
        $task->productType = $product->type;
    }

    /* Set closed realname. */
    if($task->assignedTo == 'closed') $task->assignedToRealName = 'Closed';

    /* Compute task progress. */
   /* if($task->consumed == 0 and $task->left == 0)
    {
        $task->progress = 0;
    }
    elseif($task->consumed != 0 and $task->left == 0)
    {
        $task->progress = 100;
    }
    else
    {
        $task->progress = round($task->consumed / ($task->consumed + $task->left), 2) * 100;
    }*/
    //任务进度只有0和1
    if($task->status != 'done' && $task->status != 'closed')
    {
        $task->progress = 0;
    }
    else
    {
        $task->progress = 1 * 100;
    }


    return $task;
}

/**
 * Get tasks of a execution.
 *
 * @param int    $executionID
 * @param int    $productID
 * @param string $type
 * @param string $modules
 * @param string $orderBy
 * @param null   $pager
 *
 * @access public
 * @return array|void
 */
public function getExecutionTasks($executionID, $productID = 0, $type = 'all', $modules = 0, $orderBy = 'status_asc, id_desc', $pager = null)
{
    $orderBy = 'id_desc,' . $orderBy;
    if(is_string($type)) $type = strtolower($type);

    // 判断是否为特殊查询。
    $assignIDList = array();
    if(!is_array($type) and strpos(',normal,delaystart,delayfinish,overflow,nooverflow,', ",$type,") !== false)
    {
        $tasks = $this->dao->select('id,estStarted,finishedDate,deadline,realStarted,finishedDate,consumed,estimate')->from(TABLE_TASK)->where('execution')->eq($executionID)->andWhere('deleted')->eq('0')->fetchAll();
        foreach($tasks as $task)
        {
            $realStarted = substr($task->realStarted, 0, 10);
            if($type == 'normal' and $task->estStarted <= $task->finishedDate and $task->finishedDate <= $task->deadline) $assignIDList[] = $task->id;
            if($type == 'delaystart' and $task->estStarted < $realStarted) $assignIDList[] = $task->id;
            if($type == 'delayfinish' and $task->deadline < $task->finishedDate) $assignIDList[] = $task->id;
            if($type == 'overflow' and $task->estimate < $task->consumed) $assignIDList[] = $task->id;
            if($type == 'nooverflow' and $task->consumed < $task->estimate) $assignIDList[] = $task->id;
        }
    }

    $tasks = $this->dao->select('DISTINCT t1.*, t2.id AS storyID, t2.title AS storyTitle, t2.product, t2.branch, t2.version AS latestStoryVersion, t2.status AS storyStatus, t3.realname AS assignedToRealName, t6.name as designName, t6.version as latestDesignVersion')
        ->from(TABLE_TASK)->alias('t1')
        ->leftJoin(TABLE_STORY)->alias('t2')->on('t1.story = t2.id')
        ->leftJoin(TABLE_USER)->alias('t3')->on('t1.assignedTo = t3.account')
        ->leftJoin(TABLE_TEAM)->alias('t4')->on('t4.root = t1.id')
        ->leftJoin(TABLE_MODULE)->alias('t5')->on('t1.module = t5.id')
        ->leftJoin(TABLE_DESIGN)->alias('t6')->on('t1.design= t6.id')
        ->where('t1.execution')->eq((int)$executionID)
        ->beginIF(!is_array($type) and strpos(',normal,delaystart,delayfinish,overflow,nooverflow,', ",$type,") !== false)->andWhere('t1.id')->in($assignIDList)->fi()
        ->beginIF($type == 'myinvolved')
        ->andWhere("((t4.`account` = '{$this->app->user->account}' AND t4.`type` = 'task') OR t1.`assignedTo` = '{$this->app->user->account}' OR t1.`finishedby` = '{$this->app->user->account}')")
        ->fi()
        ->beginIF($productID)->andWhere("((t5.root=" . (int)$productID . " and t5.type='story') OR t2.product=" . (int)$productID . ")")->fi()
        ->beginIF($type == 'undone')->andWhere('t1.status')->notIN('done,closed')->fi()
        ->beginIF($type == 'needconfirm')->andWhere('t2.version > t1.storyVersion')->andWhere("t2.status = 'active'")->fi()
        ->beginIF($type == 'assignedtome')->andWhere('t1.assignedTo')->eq($this->app->user->account)->fi()
        ->beginIF($type == 'finishedbyme')
        ->andWhere('t1.finishedby', 1)->eq($this->app->user->account)
        ->orWhere('t1.finishedList')->like("%,{$this->app->user->account},%")
        ->markRight(1)
        ->fi()
        ->beginIF($type == 'delayed')->andWhere('t1.deadline')->gt('1970-1-1')->andWhere('t1.deadline')->lt(date(DT_DATE1))->andWhere('t1.status')->in('wait,doing')->fi()
        ->beginIF(is_array($type) or strpos(',all,undone,needconfirm,assignedtome,delayed,finishedbyme,myinvolved,normal,delaystart,delayfinish,overflow,nooverflow,', ",$type,") === false)->andWhere('t1.status')->in($type)->fi()
        ->beginIF($modules)->andWhere('t1.module')->in($modules)->fi()
        ->andWhere('t1.deleted')->eq(0)
        ->orderBy($orderBy)
        ->page($pager, 't1.id')
        ->fetchAll('id');

    $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'task', ($productID or in_array($type, array('myinvolved', 'needconfirm'))) ? false : true);

    if(empty($tasks)) return array();

    $taskList = array_keys($tasks);
    $taskTeam = $this->dao->select('*')->from(TABLE_TEAM)->where('root')->in($taskList)->andWhere('type')->eq('task')->fetchGroup('root');
    if(!empty($taskTeam))
    {
        foreach($taskTeam as $taskID => $team) $tasks[$taskID]->team = $team;
    }

    $parents = array();
    foreach($tasks as $task)
    {
        if($task->parent > 0) $parents[$task->parent] = $task->parent;
    }
    $parents = $this->dao->select('*')->from(TABLE_TASK)->where('id')->in($parents)->fetchAll('id');

    foreach($tasks as $task)
    {
        if($task->parent > 0)
        {
            if(isset($tasks[$task->parent]))
            {
                $tasks[$task->parent]->children[$task->id] = $task;
                unset($tasks[$task->id]);
            }
            else
            {
                $parent = $parents[$task->parent];
                $task->parentName = $parent->name;
            }
        }
    }

    return $this->processTasks($tasks);
}

public function printCell($col, $task, $users, $browseType, $branchGroups, $modulePairs = array(), $mode = 'datatable', $child = false, $canRecordEstimate = false, $projectClosed = false)
{
    $disabled             = $projectClosed ? ' disabled style="pointer-events: none;" ' : '';
    $canBatchEdit         = common::hasPriv('task', 'batchEdit', !empty($task) ? $task : null);
    $canBatchClose        = (common::hasPriv('task', 'batchClose', !empty($task) ? $task : null) && strtolower($browseType) != 'closedBy');
    $canBatchCancel       = common::hasPriv('task', 'batchCancel', !empty($task) ? $task : null);
    $canBatchChangeModule = common::hasPriv('task', 'batchChangeModule', !empty($task) ? $task : null);
    $canBatchAssignTo     = common::hasPriv('task', 'batchAssignTo', !empty($task) ? $task : null);

    $canBatchAction = (!$projectClosed and ($canBatchEdit or $canBatchClose or $canBatchCancel or $canBatchChangeModule or $canBatchAssignTo));
    $storyChanged   = (!empty($task->storyStatus) and $task->storyStatus == 'active' and $task->latestStoryVersion > $task->storyVersion and !in_array($task->status, array('cancel', 'closed')));

    $designChange = ($task->designName && $task->latestDesignVersion > $task->designVersion);
    $canView      = common::hasPriv('task', 'view');
    $taskLink     = helper::createLink('task', 'view', "taskID=$task->id");
    $account      = $this->app->user->account;
    $id           = $col->id;

    $demandOrProOrSecond = $this->loadModel('task')->getTaskDemandProblem($task->id);
    $taskName = $task->name;
    if(!empty($demandOrProOrSecond)){
        if(!empty($demandOrProOrSecond['problem'])){
            foreach($demandOrProOrSecond['problem'] as $problem){
                $taskName .= '&#10;'.$this->lang->task->problem.'：'.$problem->code.' '.$this->lang->task->desc.'：'.$this->clearHtml(strval(htmlspecialchars_decode($problem->desc,ENT_QUOTES)));
            }
        }
        if(!empty($demandOrProOrSecond['demand'])){
            foreach($demandOrProOrSecond['demand'] as $demand){
                $taskName .= '&#10;'.$this->lang->task->demand.'：'.$demand->code.' '.$this->lang->task->desc.'：'.$this->clearHtml(strval(htmlspecialchars_decode($demand->reason,ENT_QUOTES)));
            }
        }
        if(!empty($demandOrProOrSecond['second'])){
            foreach($demandOrProOrSecond['second'] as $second){
                $taskName .= '&#10;'.$this->lang->task->second.'：'.$second->code.' '.$this->lang->task->desc.'：'.$this->clearHtml(strval(htmlspecialchars_decode($second->summary,ENT_QUOTES)));
            }
        }
    }

    if($col->show)
    {
        $class = "c-{$id}";
        if($id == 'status') $class .= ' task-' . $task->status;
        if($id == 'id')     $class .= ' cell-id';
        if($id == 'name')   $class .= ' text-left';
        if($id == 'deadline' and isset($task->delay)) $class .= ' text-center delayed';
        if($id == 'assignedTo') $class .= ' has-btn text-left';
        if(strpos('progress', $id) !== false) $class .= ' text-right';

        $title = '';
        if($id == 'name')
        {
            $leftPx = ($task->grade - 1) * 20;
            $paddingLeft = "style='padding-left: {$leftPx}px;'";

            $title = " title='{$taskName}' $paddingLeft";
            if(!empty($task->children)) $class .= ' has-child';
        }
        if($id == 'story') $title = " title='{$task->storyTitle}'";
        if($id == 'estimate' || $id == 'consumed' || $id == 'left')
        {
            $value = round($task->$id, 1);
            $title = " title='{$value} {$this->lang->execution->workHour}'";
        }

        echo "<td class='" . $class . "'" . $title . ">";
        if(isset($this->config->bizVersion)) $this->loadModel('flow')->printFlowCell('task', $task, $id);
        switch($id)
        {
        case 'id':
            if($canBatchAction)
            {
                echo html::checkbox('taskIDList', array($task->id => '')) . html::a(helper::createLink('task', 'view', "taskID=$task->id"), sprintf('%03d', $task->id));
            }
            else
            {
                printf('%03d', $task->id);
            }
            break;
        case 'pri':
            echo "<span class='label-pri label-pri-" . $task->pri . "' title='" . zget($this->lang->task->priList, $task->pri, $task->pri) . "'>";
            echo zget($this->lang->task->priList, $task->pri, $task->pri);
            echo "</span>";
            break;
        case 'name':
            if($task->parent > 0 and isset($task->parentName)) $task->name = "{$task->parentName} / {$task->name}";
            if(!empty($task->product) && isset($branchGroups[$task->product][$task->branch])) echo "<span class='label label-info label-outline'>" . $branchGroups[$task->product][$task->branch] . '</span> ';
            if($task->module and isset($modulePairs[$task->module])) echo "<span class='label label-gray label-badge'>" . $modulePairs[$task->module] . '</span> ';
            if($task->parent > 0) echo '<span class="label label-badge label-light" title="' . $this->lang->task->children . '">' . $this->lang->task->childrenAB . '</span> ';
            if(!empty($task->team)) echo '<span class="label label-badge label-light" title="' . $this->lang->task->multiple . '">' . $this->lang->task->multipleAB . '</span> ';
            echo $canView ? html::a($taskLink, $task->name, null, "style='color: $task->color'") : "<span style='color: $task->color'>$task->name</span>";
            if(!empty($task->children)) echo '<a class="task-toggle" data-id="' . $task->id . '"><i class="icon icon-angle-double-right"></i></a>';
            if($task->fromBug) echo html::a(helper::createLink('bug', 'view', "id=$task->fromBug", '', true), "[BUG#$task->fromBug]", '', "class='bug iframe' data-width='80%'");
            break;
        case 'type':
            echo $this->lang->task->typeList[$task->type];
            break;
        case 'status':
            if($storyChanged)
            {
                print("<span class='status-story status-changed'>{$this->lang->story->changed}</span>");
            }
            elseif($designChange)
            {
                print("<span class='status-design status-changed'>{$this->lang->task->designChanged}</span>");
            }
            else
            {
                print("<span class='status-task status-{$task->status}'> " . $this->processStatus('task', $task) . "</span>");
            }
            break;
       /* case 'estimate':
            echo round($task->estimate, 1) . ' ' . $this->lang->execution->workHourUnit;
            break;*/
        case 'consumed':
            echo round($task->consumed, 1) . ' ' . $this->lang->execution->workHourUnit;
            break;
       /* case 'left':
            echo round($task->left, 1)     . ' ' . $this->lang->execution->workHourUnit;
            break;*/
        case 'design':
            echo $task->designName ? html::a(helper::createLink('design', 'view', "id=$task->design"), $task->designName) : '';
            break;
        case 'progress':
            echo "{$task->progress}%";
            break;
        case 'deadline':
            if(substr($task->deadline, 0, 4) > 0) echo substr($task->deadline, 5, 6);
            break;
        case 'openedBy':
            echo zget($users, $task->openedBy);
            break;
        case 'openedDate':
            echo substr($task->openedDate, 5, 11);
            break;
        case 'estStarted':
            echo $task->estStarted;
            break;
        case 'realStarted':
            echo $task->realStarted;
            break;
        case 'assignedTo':
            $this->printAssignedHtml($task, $users, $projectClosed);
            break;
        case 'assignedDate':
            echo substr($task->assignedDate, 5, 11);
            break;
        case 'finishedBy':
            echo zget($users, $task->finishedBy);
            break;
        case 'finishedDate':
            echo substr($task->finishedDate, 5, 11);
            break;
        case 'canceledBy':
            echo zget($users, $task->canceledBy);
            break;
        case 'canceledDate':
            echo substr($task->canceledDate, 5, 11);
            break;
        case 'closedBy':
            echo zget($users, $task->closedBy);
            break;
        case 'closedDate':
            echo substr($task->closedDate, 5, 11);
            break;
        case 'closedReason':
            echo $this->lang->task->reasonList[$task->closedReason];
            break;
        case 'story':
            if(!empty($task->storyID))
            {
                if(common::hasPriv('story', 'view'))
                {
                    echo html::a(helper::createLink('story', 'view', "storyid=$task->storyID", 'html', true), "<i class='icon icon-{$this->lang->icons['story']}'></i>", '', "class='iframe' title='{$task->storyTitle}'");
                }
                else
                {
                    echo "<i class='icon icon-{$this->lang->icons['story']}' title='{$task->storyTitle}'></i>";
                }
            }
            break;
        case 'mailto':
            $mailto = explode(',', $task->mailto);
            foreach($mailto as $account)
            {
                $account = trim($account);
                if(empty($account)) continue;
                echo zget($users, $account) . ' &nbsp;';
            }
            break;
        case 'lastEditedBy':
            echo zget($users, $task->lastEditedBy);
            break;
        case 'lastEditedDate':
            echo substr($task->lastEditedDate, 5, 11);
            break;
        case 'actions':
          /*  if($storyChanged)
            {
                common::printIcon('task', 'confirmStoryChange', "taskid=$task->id", '', 'list', '', 'hiddenwin', '', '', $disabled);
                break;
            }
            if($designChange)
            {
                common::printIcon('task', 'confirmDesignChange', "taskid=$task->id", '', 'list', 'search', 'hiddenwin', '', '', $disabled);
                break;
            }*/

            if(empty($task->children))
            {
                //if($task->status != 'pause') common::printIcon('task', 'start', "taskID=$task->id", $task, 'list', '', '', 'iframe', true, $disabled);
               // if($task->status == 'pause') common::printIcon('task', 'restart', "taskID=$task->id", $task, 'list', '', '', 'iframe', true, $disabled);
                //common::printIcon('task', 'close', "taskID=$task->id", $task, 'list', '', '', 'iframe', true, $disabled);

                if($canRecordEstimate)
                {
                   common::printIcon('task', 'editTask', "taskID=$task->id", $task, 'list', 'edit', '', 'iframe', true, $disabled);
                   common::printIcon('task', 'finish', "taskID=$task->id&flag=2", $task, 'list', '', '', 'iframe', true, $disabled);
                }
            }

            //common::printIcon('task', 'edit', "taskID=$task->id", $task, 'list', '', '', '', '', $disabled);
            //common::printIcon('task', 'editTask', "taskID=$task->id", $task, 'list', 'edit', '', 'iframe', true, $disabled);

            // 将批量拆分修改为单个拆分子任务。
            //common::printIcon('task', 'create', "execution=$task->execution&storyID=$task->story&moduleID=$task->module&taskID=$task->id&ifame=0", $task, 'list', 'split', '', '', '', '', $this->lang->task->children);
           // common::printIcon('task', 'batchCreate', "execution=$task->execution&storyID=$task->story&moduleID=$task->module&taskID=$task->id&ifame=0", $task, 'list', 'split', '', '', '', $disabled, $this->lang->task->children);

            break;
        }
        echo '</td>';
    }
}

/**
 * 清除字符串特殊符号
 * @param $str
 * @return void
 */
public function clearHtml($str){
    $str = trim($str); //清除字符串两边的空格
    $str = strip_tags($str,""); //利用php自带的函数清除html格式
    $str = preg_replace("/\t/","",$str); //使用正则表达式替换内容，如：空格，换行，并将替换为空。
    //$str = preg_replace("/\r\n/","",$str);
    $str = preg_replace("/\r/","",$str);
    //$str = preg_replace("/\n/","",$str);
    return trim($str); //返回字符串
}

/**
 * Gets the version record of the task.
 *
 * @param $taskID
 * @param $version
 * @access public
 * @return void
 */
public function getTaskSpec($taskID, $version)
{
    return $this->dao->select('*')->from(TABLE_TASKSPEC)
        ->where('task')->eq($taskID)
        ->andWhere('version')->eq($version)
        ->fetch();
}

public function activate($taskID)
{
    $changes = parent::activate($taskID);
    $today   = helper::today();

    $this->dao->update(TABLE_TASK)->set('activatedDate')->eq($today)->where('id')->eq($taskID)->exec();
    return $changes;
}

public function update($taskID)
{
    $result = parent::update($taskID);

    /* Update planDuration. */
    if($result)
    {
        $estStarted   = $this->post->estStarted;
        $deadline     = $this->post->deadline;
        $planDuration = $this->loadModel('holiday')->getActualWorkingDays($estStarted, $deadline);
        $planDuration = count($planDuration);

        $this->dao->update(TABLE_TASK)->set('planDuration')->eq($planDuration)->where('id')->eq($taskID)->exec();
    }

    return $result;
}
