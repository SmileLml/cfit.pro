<?php
class chengfangjinkeProject extends projectModel
{
    /**
     * Project: chengfangjinke
     * Method: getStats
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:14
     * Desc: This is the code comment. This method is called getStats.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $projectID
     * @param string $status
     * @param int $productID
     * @param int $branch
     * @param int $itemCounts
     * @param string $orderBy
     * @param null $pager
     * @return array
     */
    public function getStats($projectID = 0, $status = 'undone', $productID = 0, $branch = 0, $itemCounts = 30, $orderBy = 'id_asc', $pager = null,$flag = 0)
    {
        if(empty($productID))
        {
            $myExecutionIDList = array();
            if($status == 'involved')
            {
                $myExecutionIDList = $this->dao->select('root')->from(TABLE_TEAM)
                    ->where('account')->eq($this->app->user->account)
                    ->andWhere('type')->eq('execution')
                    ->fetchPairs();
            }

            $executions = $this->dao->select('*')->from(TABLE_EXECUTION)
                ->where('type')->in('sprint,stage')
                ->beginIF($projectID != 0)->andWhere('project')->eq($projectID)->fi()
                ->beginIF(!empty($myExecutionIDList))->andWhere('id')->in(array_keys($myExecutionIDList))->fi()
                ->beginIF($status == 'undone')->andWhere('status')->notIN('done,closed')->fi()
                ->beginIF($status != 'all' and $status != 'undone' and $status != 'involved')->andWhere('status')->eq($status)->fi()
                ->andWhere('deleted')->eq('0')
                ->beginIF(!$flag)->andWhere('dataVersion')->eq('1')->fi() // 历史数据
                ->beginIF($flag)->andWhere('dataVersion')->eq('2')->fi() //新数据
                ->orderBy($orderBy)
                ->page($pager)
                ->fetchAll('id');
        }
        else
        {
            $executions = $this->dao->select('t2.*')->from(TABLE_PROJECTPRODUCT)->alias('t1')
                ->leftJoin(TABLE_EXECUTION)->alias('t2')->on('t1.execution=t2.id')
                ->where('t1.product')->eq($productID)
                ->beginIF($projectID)->andWhere('t2.project')->eq($projectID)->fi()
                ->beginIF($status == 'undone')->andWhere('t2.status')->notIN('done,closed')->fi()
                ->beginIF($status != 'all' and $status != 'undone')->andWhere('t2.status')->eq($status)->fi()
                ->andWhere('t2.deleted')->eq('0')
                ->beginIF(!$flag)->andWhere('dataVersion')->eq('1')->fi() // 历史数据
                ->beginIF($flag)->andWhere('dataVersion')->eq('2')->fi() //新数据
                ->orderBy($orderBy)
                ->page($pager)
                ->fetchAll('id');
        }

        $hours     = array();
        $emptyHour = array('totalEstimate' => 0, 'totalConsumed' => 0, 'computerLeft' => 0, 'totalLeft' => 0, 'progress' => 0);

        /* Get all tasks and compute totalEstimate, totalConsumed, totalLeft, progress according to them. */
        $tasks = $this->dao->select('id, execution, estimate, consumed, `left`, status, closedReason')
            ->from(TABLE_TASK)
            ->where('execution')->in(array_keys($executions))
            ->andWhere('parent')->lt(1)
            ->andWhere('deleted')->eq(0)
            ->fetchGroup('execution', 'id');

        /* Compute totalEstimate, totalConsumed, totalLeft. */
        foreach($tasks as $executionID => $executionTasks)
        {
            $hour = (object)$emptyHour;
            foreach($executionTasks as $task)
            {
                if($task->status != 'cancel')
                {
                    $hour->totalEstimate += $task->estimate;
                    $hour->totalConsumed += $task->consumed;

                    // 用来在计划列表展示阶段工作量偏差。
                    $hour->computerLeft  += round($task->consumed - $task->estimate,1);

                    // 用来计算实际的完成百分比。
                    $hour->totalLeft     += $task->left;
                }
            }
            $hours[$executionID] = $hour;
        }

        /* Compute totalReal and progress. */
        foreach($hours as $hour)
        {
            $hour->totalEstimate = round($hour->totalEstimate, 1) ;
            $hour->totalConsumed = round($hour->totalConsumed, 1);
            $hour->totalLeft     = round($hour->totalLeft, 1);
            $hour->totalReal     = $hour->totalConsumed + $hour->totalLeft;
            $hour->progress      = $hour->totalReal ? round($hour->totalConsumed / $hour->totalReal, 3) * 100 : 0;
        }

        /* Process executions. */
        $parents  = array();
        $children = array();
        $this->loadModel('execution');
        foreach($executions as $key => $execution)
        {

            /* Process the burns. */
            //$execution->burns = array();
            //$burnData = isset($burns[$execution->id]) ? $burns[$execution->id] : array();
            //foreach($burnData as $data) $execution->burns[] = $data->value;

            /* Process the hours. */
            $execution->hours = isset($hours[$execution->id]) ? $hours[$execution->id] : (object)$emptyHour;

            $execution->children = array();
            $execution->grade == 1 ? $parents[$execution->id] = $execution : $children[$execution->parent][] = $execution;
            if($execution->grade == 2)
            {
                $execution->taskCount = $this->execution->getExecutionTaskCount($execution->id);
                $execution->parentName = $this->execution->getTaskExecutionName($execution->parent);
            }
        }

        /* In the case of the waterfall model, calculate the sub-stage. */
        $project = $this->getByID($projectID);
        if($project and $project->model == 'waterfall')
        {
            foreach($parents as $id => $execution)
            {
                $taskCount = 0;
                $execution->children = isset($children[$id]) ? $children[$id] : array();
                foreach($execution->children as $childrenExecution)
                {
                    $taskCount +=$childrenExecution->taskCount;
                    $execution->hours->totalEstimate += $childrenExecution->hours->totalEstimate;
                    $execution->hours->totalConsumed += $childrenExecution->hours->totalConsumed;
                    $execution->hours->computerLeft  += $childrenExecution->hours->computerLeft;
                }
                $execution->taskCount = $taskCount;
                unset($children[$id]);
            }
        }
        $orphan = array();
        foreach($children as $child) $orphan = array_merge($child, $orphan);

        return array_merge($parents, $orphan);
    }

    /**
     * Project: chengfangjinke
     * Method: getTaskStats
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:14
     * Desc: This is the code comment. This method is called getTaskStats.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $projectID
     * @param string $status
     * @return array
     */
    public function getTaskStats($projectID = 0, $status = 'undone')
    {
        $tasks = $this->dao->select('*')->from(TABLE_TASK)->where('project')->eq($projectID)
            ->andWhere('deleted')->eq(0)
            ->beginIF($status != 'all')->andWhere('status')->eq($status)->fi()
            ->orderBy('grade DESC, id ASC')
            ->fetchAll();
        $stats = array();
        foreach($tasks as $task)
        {
            if(!isset($stats[$task->execution]))
            {
                $stats[$task->execution] = array();
            }

            if(!isset($stats[$task->execution][$task->parent]))
            {
                $stats[$task->execution][$task->parent] = array();
            }

            if(isset($stats[$task->execution][$task->id]))
            {
                $task->children = $stats[$task->execution][$task->id];
            }
            else
            {
                $task->children = array();
            }

            $stats[$task->execution][$task->parent][] = $task;
        }

        $executionTasks = array();
        foreach($stats as $execution => $stat)
        {
            $executionTasks[$execution] = $stat[0];
        }

        return $executionTasks;
    }

    /**
     * Project: chengfangjinke
     * Method: printCellNew
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:14
     * Desc: This is the code comment. This method is called printCellNew.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $col
     * @param $project
     * @param $users
     * @param int $programID
     */
    public function printCellNew($col, $project, $users, $programID = 0)
    {
        $canOrder     = common::hasPriv('project', 'updateOrder');
        $canBatchEdit = common::hasPriv('project', 'batchEdit');
        $projectLink  = $this->config->systemMode == 'new' ? helper::createLink('project', 'index', "projectID=$project->id", '', '', $project->id) : helper::createLink('execution', 'task', "projectID=$project->id");
        $account      = $this->app->user->account;
        $id           = $col->id;
        $this->loadModel('projectplan');

        if($col->show)
        {
            $title = '';
            $class = "c-$id" . (in_array($id, array('budget', 'teamCount', 'estimate', 'consume')) ? ' c-number' : '');

            if($id == 'id') $class .= ' cell-id';

            if($id == 'name')
            {
                $class .= ' text-left';
                $title  = "title='{$project->name}'";
            }

            if($id == 'code')
            {
                $class .= ' text-left text-ellipsis';
                $title  = "title='{$project->code}'";
            }
            if($id == 'PM')
            {
                $PM = zget($users,$project->PM);
                $class .= ' text-left text-ellipsis';
                $title  = "title='{$PM}'";
            }

            if($id == 'estimate') $title = "title='{$project->hours->totalEstimate} {$this->lang->execution->workHour}'";
            if($id == 'consume')  $title = "title='{$project->hours->totalConsumed} {$this->lang->execution->workHour}'";
            if($id == 'surplus')  $title = "title='{$project->hours->totalLeft} {$this->lang->execution->workHour}'";

            echo "<td class='$class' $title>";
            switch($id)
            {
                case 'id':
                    if($canBatchEdit)
                    {
                        echo html::checkbox('projectIdList', array($project->id => '')) . html::a($projectLink, sprintf('%03d', $project->id));
                    }
                    else
                    {
                        printf('%03d', $project->id);
                    }
                    break;
                case 'name':
                    /*
                    if(isset($this->config->maxVersion))
                    {
                        if($project->model === 'waterfall') echo "<span class='project-type-label label label-outline label-warning'>{$this->lang->project->waterfall}</span> ";
                        if($project->model === 'scrum')     echo "<span class='project-type-label label label-outline label-info'>{$this->lang->project->scrum}</span> ";
                    }
                     */
                    echo html::a($projectLink, $project->name);
                    break;
                case 'code':
                    echo $project->code;
                    break;
                case 'PM':
                    $user   = $this->loadModel('user')->getByID($project->PM, 'account');
                    $userID = !empty($user) ? $user->id : '';
                    $PMLink = helper::createLink('user', 'profile', "userID=$userID", '', true);
                    echo empty($project->PM) ? '' : html::a($PMLink, zget($users, $project->PM), '', "data-toggle='modal' data-type='iframe' data-width='600'");
                    break;
                case 'begin':
                    echo $project->begin;
                    break;
                case 'end':
                    echo $project->end == LONG_TIME ? $this->lang->project->longTime : $project->end;
                    break;
                case 'planDuration':
                    echo $project->planDuration;
                    break;
                case 'workload':
                    echo $project->workload;
                    break;
                case 'realBegan':
                    echo $project->realBegan == '0000-00-00' ? '' : $project->realBegan;
                    break;
                case 'realEnd':
                    echo $project->realEnd == '0000-00-00' ? '' : $project->realEnd;
                    break;
                case 'realDuration':
                    echo ($project->realEnd == '0000-00-00' || $project->realBegan =='0000-00-00') ? '' : $project->realDuration;
                    break;
                case 'diffDuration':
                    echo ($project->realEnd == '0000-00-00' || $project->realBegan =='0000-00-00' || !$project->planDuration) ? '' : $project->realDuration - $project->planDuration;
                    break;
                case 'planHour':
                    echo   number_format($project->estimate/(8*$project->workHours), 1);;
                    break;
                case 'realHour':
                    echo number_format($project->consumed/(8*$project->workHours), 1);
                    break;
                case 'diffHour':
                    $diff = number_format($project->consumed - $project->estimate,1);
                    echo $diff;
                    break;
                case 'complete':
                    //echo "<div class='progress-pie' data-doughnut-size='90' data-color='#00da88' data-value='{$project->progress}' data-width='24' data-height='24' data-back-color='#e8edf3'><div class='progress-info'>{$project->progress}</div></div>";
                    echo empty($project->progress) ? '0%' : $project->progress . '%';
                    break;
                case 'status':
                    echo zget($this->lang->project->featureBar, $project->status, '');
                    break;
                case 'insideStatus':
                    echo zget($this->lang->projectplan->insideStatusList, $project->insideStatus, '');
                    break;
                case 'actions':
                    if($project->status == 'wait' || $project->status == 'suspended') common::printIcon('project', 'start', "projectID=$project->id", $project, 'list', 'play', '', 'iframe', true);
                    if($project->status == 'doing') common::printIcon('project', 'close', "projectID=$project->id", $project, 'list', 'off', '', 'iframe', true);
                    if($project->status == 'closed') common::printIcon('project', 'activate', "projectID=$project->id", $project, 'list', 'magic', '', 'iframe', true);

                    if(common::hasPriv('project','suspend') || (common::hasPriv('project','close') && $project->status != 'doing') || (common::hasPriv('project','activate') && $project->status != 'closed'))
                    {
                        echo "<div class='btn-group'>";
                        echo "<button type='button' class='btn icon-caret-down dropdown-toggle' data-toggle='context-dropdown' title='{$this->lang->more}' style='width: 16px; padding-left: 0px; border-radius: 4px;'></button>";
                        echo "<ul class='dropdown-menu pull-right text-center' role='menu' style='position: unset; min-width: auto; padding: 5px 6px;'>";
                        common::printIcon('project', 'suspend', "projectID=$project->id", $project, 'list', 'pause', '', 'iframe btn-action', true);
                        if($project->status != 'doing') common::printIcon('project', 'close', "projectID=$project->id", $project, 'list', 'off', '', 'iframe btn-action', true);
                        if($project->status != 'closed') common::printIcon('project', 'activate', "projectID=$project->id", $project, 'list', 'magic', '', 'iframe btn-action', true);
                        echo "</ul>";
                        echo "</div>";
                    }

                    $from = 'project';
                    common::printIcon('project', 'edit', "projectID=$project->id&from=$from", $project, 'list', 'edit', '', '', '', "data-app=project", '', $project->id);
                    common::printIcon('project', 'manageMembers', "projectID=$project->id", $project, 'list', 'group', '', '', '', "data-app=project", $this->lang->execution->team, $project->id);
                    if($this->config->systemMode == 'new') common::printIcon('project', 'group', "projectID=$project->id&programID=$programID", $project, 'list', 'lock', '', '', '', "data-app=project", '', $project->id);

                    if(common::hasPriv('project','manageProducts') || common::hasPriv('project', 'whitelist') || common::hasPriv('project', 'delete'))
                    {
                        echo "<div class='btn-group'>";
                        echo "<button type='button' class='btn dropdown-toggle' data-toggle='context-dropdown' title='{$this->lang->more}'><i class='icon-more-alt'></i></button>";
                        echo "<ul class='dropdown-menu pull-right text-center' role='menu'>";
                        common::printIcon('project', 'manageProducts', "projectID=$project->id", $project, 'list', 'link', '', 'btn-action', '', "data-app=project", $this->lang->project->manageProducts, $project->id);
                        if($this->config->systemMode == 'new') common::printIcon('project', 'whitelist', "projectID=$project->id&module=project&from=$from", $project, 'list', 'shield-check', '', 'btn-action', '', "data-app=project", '', $project->id);
                        if(common::hasPriv('project','delete')) echo html::a(inLink("delete", "projectID=$project->id"), "<i class='icon-trash'></i>", 'hiddenwin', "class='btn btn-action' title='{$this->lang->project->delete}'");
                        echo "</ul>";
                        echo "</div>";
                    }
                    break;
            }
            echo '</td>';
        }
    }

    /**
     * Project: chengfangjinke
     * Method: printCell
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:14
     * Desc: This is the code comment. This method is called printCell.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $col
     * @param $project
     * @param $users
     * @param int $programID
     */
    public function printCell($col, $project, $users, $programID = 0)
    {
        $canOrder     = common::hasPriv('project', 'updateOrder');
        $canBatchEdit = common::hasPriv('project', 'batchEdit');
        $projectLink  = $this->config->systemMode == 'new' ? helper::createLink('project', 'index', "projectID=$project->id", '', '', $project->id) : helper::createLink('execution', 'task', "projectID=$project->id");
        $account      = $this->app->user->account;
        $id           = $col->id;

        if($col->show)
        {
            $title = '';
            $class = "c-$id" . (in_array($id, array('budget', 'teamCount', 'estimate', 'consume')) ? ' c-number' : '');

            if($id == 'id') $class .= ' cell-id';

            if($id == 'name')
            {
                $class .= ' text-left';
                $title  = "title='{$project->name}'";
            }

            if($id == 'budget')
            {
                $projectBudget = in_array($this->app->getClientLang(), ['zh-cn','zh-tw']) ? round((float)$project->budget / 10000, 2) . $this->lang->project->tenThousand : round((float)$project->budget, 2);
                $budgetTitle   = $project->budget != 0 ? zget($this->lang->project->currencySymbol, $project->budgetUnit) . ' ' . $projectBudget : $this->lang->project->future;

                $title = "title='$budgetTitle'";
            }

            if($id == 'estimate') $title = "title='{$project->hours->totalEstimate} {$this->lang->execution->workHour}'";
            if($id == 'consume')  $title = "title='{$project->hours->totalConsumed} {$this->lang->execution->workHour}'";
            if($id == 'surplus')  $title = "title='{$project->hours->totalLeft} {$this->lang->execution->workHour}'";

            echo "<td class='$class' $title>";
            switch($id)
            {
                case 'id':
                    if($canBatchEdit)
                    {
                        echo html::checkbox('projectIdList', array($project->id => '')) . html::a($projectLink, sprintf('%03d', $project->id));
                    }
                    else
                    {
                        printf('%03d', $project->id);
                    }
                    break;
                case 'name':
                    echo html::a($projectLink, $project->name);
                    break;
                case 'PM':
                    $user   = $this->loadModel('user')->getByID($project->PM, 'account');
                    $userID = !empty($user) ? $user->id : '';
                    $PMLink = helper::createLink('user', 'profile', "userID=$userID", '', true);
                    echo empty($project->PM) ? '' : html::a($PMLink, zget($users, $project->PM), '', "data-toggle='modal' data-type='iframe' data-width='600'");
                    break;
                case 'begin':
                    echo $project->begin;
                    break;
                case 'end':
                    echo $project->end == LONG_TIME ? $this->lang->project->longTime : $project->end;
                    break;
                case 'status':
                    echo "<span class='status-task status-{$project->status}'> " . zget($this->lang->project->statusList, $project->status) . "</span>";
                    break;
                case 'budget':
                    echo $budgetTitle;
                    break;
                case 'teamCount':
                    echo $project->teamCount;
                    break;
                case 'estimate':
                    echo $project->hours->totalEstimate . $this->lang->execution->workHourUnit;
                    break;
                case 'consume':
                    echo $project->hours->totalConsumed . $this->lang->execution->workHourUnit;
                    break;
                case 'surplus':
                    echo $project->hours->totalLeft     . $this->lang->execution->workHourUnit;
                    break;
                case 'progress':
                    echo "<div class='progress-pie' data-doughnut-size='90' data-color='#00da88' data-value='{$project->hours->progress}' data-width='24' data-height='24' data-back-color='#e8edf3'><div class='progress-info'>{$project->hours->progress}</div></div>";
                    break;
                case 'actions':
                    if($project->status == 'wait' || $project->status == 'suspended') common::printIcon('project', 'start', "projectID=$project->id", $project, 'list', 'play', '', 'iframe', true);
                    if($project->status == 'doing') common::printIcon('project', 'close', "projectID=$project->id", $project, 'list', 'off', '', 'iframe', true);
                    if($project->status == 'closed') common::printIcon('project', 'activate', "projectID=$project->id", $project, 'list', 'magic', '', 'iframe', true);

                    if(common::hasPriv('project','suspend') || (common::hasPriv('project','close') && $project->status != 'doing') || (common::hasPriv('project','activate') && $project->status != 'closed'))
                    {
                        echo "<div class='btn-group'>";
                        echo "<button type='button' class='btn icon-caret-down dropdown-toggle' data-toggle='context-dropdown' title='{$this->lang->more}' style='width: 16px; padding-left: 0px; border-radius: 4px;'></button>";
                        echo "<ul class='dropdown-menu pull-right text-center' role='menu' style='position: unset; min-width: auto; padding: 5px 6px;'>";
                        common::printIcon('project', 'suspend', "projectID=$project->id", $project, 'list', 'pause', '', 'iframe btn-action', true);
                        if($project->status != 'doing') common::printIcon('project', 'close', "projectID=$project->id", $project, 'list', 'off', '', 'iframe btn-action', true);
                        if($project->status != 'closed') common::printIcon('project', 'activate', "projectID=$project->id", $project, 'list', 'magic', '', 'iframe btn-action', true);
                        echo "</ul>";
                        echo "</div>";
                    }

                    $from    = $project->from == 'project' ? 'project' : 'pgmproject';
                    common::printIcon('project', 'edit', "projectID=$project->id&from=$from", $project, 'list', 'edit', '', '', '', "data-app=project", '', $project->id);
                    common::printIcon('project', 'manageMembers', "projectID=$project->id", $project, 'list', 'group', '', '', '', "data-app=project", $this->lang->execution->team, $project->id);
                    if($this->config->systemMode == 'new') common::printIcon('project', 'group', "projectID=$project->id&programID=$programID", $project, 'list', 'lock', '', '', '', "data-app=project", '', $project->id);

                    if(common::hasPriv('project','manageProducts') || common::hasPriv('project', 'whitelist') || common::hasPriv('project', 'delete'))
                    {
                        echo "<div class='btn-group'>";
                        echo "<button type='button' class='btn dropdown-toggle' data-toggle='context-dropdown' title='{$this->lang->more}'><i class='icon-more-alt'></i></button>";
                        echo "<ul class='dropdown-menu pull-right text-center' role='menu'>";
                        common::printIcon('project', 'manageProducts', "projectID=$project->id", $project, 'list', 'link', '', 'btn-action', '', "data-app=project", $this->lang->project->manageProducts, $project->id);
                        if($this->config->systemMode == 'new') common::printIcon('project', 'whitelist', "projectID=$project->id&module=project&from=$from", $project, 'list', 'shield-check', '', 'btn-action', '', "data-app=project", '', $project->id);
                        if(common::hasPriv('project','delete')) echo html::a(inLink("delete", "projectID=$project->id"), "<i class='icon-trash'></i>", 'hiddenwin', "class='btn btn-action' title='{$this->lang->project->delete}'");
                        echo "</ul>";
                        echo "</div>";
                    }
                    break;
            }
            echo '</td>';
        }
    }

    /**
     * Project: chengfangjinke
     * Method: getPairs
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:14
     * Desc: This is the code comment. This method is called getPairs.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @return mixed
     */
    public function getPairs()
    {
        return $this->dao->select('id,name')->from(TABLE_PROJECT)
            ->where('deleted')->eq(0)
            ->andWhere('type')->eq('project')
            ->orderBy('id_desc')
            ->fetchPairs();
    }

    /**
     * Project: chengfangjinke
     * Method: getReleases
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:14
     * Desc: This is the code comment. This method is called getReleases.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $projects
     * @return array
     */
    public function getReleases($projects)
    {
        $projects = trim($projects, ',');
        if(!$projects) return array();

        return $this->dao->select('id,name')->from(TABLE_RELEASE)
            ->where('deleted')->eq(0)
            ->andWhere('project')->in($projects)
            ->orderBy('id_desc')
            ->fetchPairs();
    }

    /**
     * Project: chengfangjinke
     * Method: getReleasesList
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:14
     * Desc: This is the code comment. This method is called getReleasesList.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $projects
     * @return array
     */
    public function getReleasesList($projects)
    {
        $projects = trim($projects, ',');
        if(!$projects) return array();

        return $this->dao->select('*')->from(TABLE_RELEASE)
            ->where('deleted')->eq(0)
            ->andWhere('project')->in($projects)
            ->orderBy('id_desc')
            ->fetchAll('id');
    }

    /**
     * Project: chengfangjinke
     * Method: printStage
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:14
     * Desc: This is the code comment. This method is called printStage.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $executions
     * @param $tasks
     * @param int $grade
     * @return array|void
     */
    public function printStage($executions, $tasks, $grade = 1, $projectStatus = '')
    {
        if(empty($executions)) return array();

        $disabled = $projectStatus == 'closed' ? ' disabled style="pointer-events: none;" ' : '';
        // 判断用户必须是项目团队成员或超级管理员才可以提交工时。
        $canRecordEstimate = $this->loadModel('project')->isCanRecordEstimate($this->session->project);
        $canExecutionView  = common::hasPriv('execution', 'view');
        $canTaskView       = common::hasPriv('task', 'view');

        foreach($executions as $execution)
        {
            echo "<tr data-id='$execution->id' data-order='$execution->order'>";

            echo "<td class='text-left item' " . (!empty($execution->children) ? 'has-child' : '') .  " data-path='" . $execution->path . "' style='padding-left:" . ($grade-1)*40 . "px'>";
            if(!empty($execution->children) || !empty($tasks[$execution->id]))
            {
                $isOpen = $grade == 2 ? 'collapsed plan-toggle-show' : '';
                echo '<span class="table-nest-icon icon table-nest-toggle plan-toggle ' . $isOpen . ($execution->type !== 'stage' ? 'collapsed' : ''). '" data-path="' . $execution->path . '" data-id="' . $execution->id . '"></span>';
            }
            else
            {
                echo '<span class="table-nest-icon icon table-nest-toggle plan-toggle ' . ($execution->type !== 'stage' ? 'collapsed' : ''). '" data-path="' . $execution->path . '" data-id="' . $execution->id . '"></span>';
            }

            if($execution->milestone) echo "<icon class='icon icon-flag icon-sm red' title='{$this->lang->project->milestone}'></icon>";
            if($canExecutionView)
            {
                echo "<a href='" . helper::createLink('execution', 'view', 'execution=' . $execution->id) . "' title = '{$execution->name}'>{$execution->name}</a> ";
            }
            else
            {
                echo "<span  title = '{$execution->name}'>{$execution->name}</span> ";
            }
            if(isset($execution->delay)) echo "<span class='label label-danger label-badge'>{$this->lang->execution->delayed}</span> ";
            echo "</td>";

            echo "<td>" . $execution->begin . "</td>";
            echo "<td>" . $execution->end . "</td>";
            echo "<td class='hours' title='{$execution->planDuration}'>{$execution->planDuration}</td>";

            echo "<td>" . $execution->realBegan . "</td>";
            echo "<td>" . $execution->realEnd . "</td>";
            echo "<td class='hours' title='{$execution->realDuration}'>" . ($execution->realDuration ? $execution->realDuration : '0') . "</td>";
            $diff = $execution->realDuration == 0 ? '0' : $execution->realDuration-$execution->planDuration;
            echo "<td class='hours' title='$diff'>$diff</td>";
          //20240528 根据修改列表实际工作量内容，将此项隐藏
          /*  echo "<td class='hours' title='{$execution->hours->totalEstimate} {$this->lang->execution->workHour}'>{$execution->hours->totalEstimate}{$this->lang->execution->workHourUnit}</td>";
            echo "<td class='hours' title='{$execution->hours->totalConsumed} {$this->lang->execution->workHour}'>";
            echo html::a(helper::createLink('execution', 'view', array('id' => $execution->id)), $execution->hours->totalConsumed . $this->lang->execution->workHourUnit);
            echo "</td>";
            echo "<td class='hours' title='{$execution->hours->computerLeft} {$this->lang->execution->workHour}'>{$execution->hours->computerLeft}{$this->lang->execution->workHourUnit}</td>";*/

            $changedTimes = $execution->version <=1 ? '' : $execution->version - 1;
            echo "<td class='text-right' title='{$changedTimes}'>" . $changedTimes . "</td>";
            echo "<td class='text-right' title='$execution->taskCount'>" . html::a(helper::createLink('execution', 'task', 'execution=' . $execution->id.'&type=all'), $execution->taskCount) . "</td>";

            echo "<td class='text-center'>" . round($execution->progress, 1) . "%</td>";
            $executionStatus = $this->processStatus('execution', $execution);
            echo "<td class='c-status text-center' title='$executionStatus'>";
            echo "<span class='status-execution status-{$execution->status}'>$executionStatus</span>";
            echo "</td>";

            echo "<td class='c-actions'>";
            common::printIcon('execution', 'edit', "executionID=$execution->id", $execution, 'list', 'edit', '', 'iframe', true, $disabled);
            if($execution->grade == 2)
            {
                $priv =  commonModel::hasPriv('task', 'batchCreate');
                if($priv and empty($disabled))
                {
                    echo html::a(helper::createLink('task', 'batchCreate', "executionID=$execution->id"), "<i class='icon-task-batcheCreate icon-split'></i>", '', "class='btn ' title='{$this->lang->task->subdivide}'", false);
                }
                else
                {
                    echo "<button type='button' class='disabled btn '><i class='icon-task-batcheCreate icon-split' title='{$this->lang->task->subdivide}' ></i></button>";
                }
            }
            else if($execution->grade == 1)
            {
                common::printIcon('programplan', 'create', "program=$execution->project&productID=0&planID=$execution->id", $execution, 'list', 'split', '', '', '', $disabled, $this->lang->programplan->createSubPlan);
            }
            echo "</td>";
            echo "</tr>";

            $this->printStage($execution->children, $tasks, $grade + 1, $projectStatus);
            if(isset($tasks[$execution->id])) $this->printTask($tasks[$execution->id], $grade + 1, $execution->path, $canRecordEstimate, $canTaskView, $projectStatus);
        }
    }

    /**
     * Project: chengfangjinke
     * Method: printTask
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:15
     * Desc: This is the code comment. This method is called printTask.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $tasks
     * @param int $grade
     * @param string $path
     * @return array|void
     */
    public function printTask($tasks, $grade = 1, $path = '', $canRecordEstimate = 0, $canTaskView = false, $projectStatus = '')
    {
        $disabled = $projectStatus == 'closed' ? ' disabled style="pointer-events: none;" ' : '';
        foreach($tasks as $task)
        {
            // 默认所有的任务一开始进行隐藏。
            echo "<tr class='hidden' data-id='$task->id'>";

            $taskPath = $path . '-' . $task->id; // 分隔符不能和阶段一致，防止混淆
            $hasChild = !empty($task->children);
            echo "<td class='text-left item' " . (!empty($task->children) ? 'has-child' : '') . " data-path='" . $taskPath . "' title='$task->name' style='padding-left:" . (($grade-1)*40 - ($hasChild ? 25 : 0)) . "px'>";
            if($hasChild)
            {
                echo '<span class="table-nest-icon icon table-nest-toggle plan-toggle" data-path="' . $taskPath . '" data-id="' . $task->id . '"></span>';
            }
            //echo !empty($task->children) ? $task->name : html::a(helper::createLink('task', 'view', 'task=' . $task->id), $task->name);
            $taskPri   = "<span class='label-pri label-pri-{$task->pri}'>";
            $taskPri  .= zget($this->lang->task->priList, $task->pri, $task->pri);
            $taskPri  .= "</span> ";
            $extraLogo = '[ T ] ' . $taskPri;

            if($canTaskView)
            {
                echo html::a(helper::createLink('task', 'view', 'task=' . $task->id), $extraLogo . $task->name);
            }
            else
            {
                echo $extraLogo . $task->name;
            }

            $delay = helper::diffDate(helper::today(), $task->deadline);
            if($task->status != 'done' and $task->status != 'closed' and $task->status != 'pause' and $task->status != 'cancel')
            {
                if($delay > 0) echo "<span class='label label-danger label-badge'>{$this->lang->task->delayed}</span> ";
            }
            echo "</td>";

            echo "<td>" . $task->estStarted . "</td>";
            echo "<td>" . $task->deadline . "</td>";
            echo "<td class='hours' title='{$task->planDuration}'>{$task->planDuration}</td>";
            //echo "<td class='hours' title='{$task->estimate} {$this->lang->execution->workHour}'>{$task->estimate}{$this->lang->execution->workHourUnit}</td>";

            echo "<td>" . substr($task->realStarted, 0, 10) . "</td>";
            echo "<td>" . substr($task->finishedDate, 0, 10) . "</td>";
            echo "<td class='hours' title='{$task->realDuration}'>" . ($task->realDuration ? $task->realDuration : '0') . "</td>";
            $diff = $task->realDuration == 0 ? '0' : $task->realDuration - $task->planDuration;
            echo "<td class='hours' title='{$diff}'>{$diff}</td>";
            //echo "<td class='hours' title='{$task->consumed} {$this->lang->execution->workHour}'>{$task->consumed}{$this->lang->execution->workHourUnit}</td>";
            //echo "<td class='hours' title='{$task->left} {$this->lang->execution->workHour}'>{$task->left}{$this->lang->execution->workHourUnit}</td>";

            //任务涉及到到工作量偏差等保留一位小数
            //20240528 根据修改列表实际工作量内容，将此项隐藏
           /* $estimate = round($task->estimate,1);
            echo "<td class='hours' title='{$estimate} {$this->lang->execution->workHour}'>{$estimate}{$this->lang->execution->workHourUnit}</td>";
            $consumed = round($task->consumed,1);
            echo "<td class='hours' title='{$consumed} {$this->lang->execution->workHour}'>{$consumed}{$this->lang->execution->workHourUnit}</td>";
            $diff = round($task->consumed - $task->estimate,1);
            echo "<td class='hours' title='{$diff} {$this->lang->execution->workHour}'>{$diff}{$this->lang->execution->workHourUnit}</td>";*/

            //$taskStatus = zget($this->lang->task->statusList, $task->status, '');
            //echo "<td class='c-status text-center' title='$taskStatus'>";
            //echo "<span class='status-task status-{$task->status}'>$taskStatus</span>";
            //echo "</td>";

            //echo "<td title='$task->resource'>" . $task->resource . "</td>";
            $changedTimes = $task->version ? $task->version : '';
            echo "<td class='text-right' title='{$changedTimes}'>" . $changedTimes . "</td>";
            echo "<td class='text-right'>0</td>";

            /*
            echo "<td class='c-progress'>";
            $progress = $task->consumed == 0 ? 0 : round($task->consumed/($task->consumed+$task->left)*100);
            echo "<div class='progress-pie' data-doughnut-size='90' data-color='#00da88' data-value='{$progress}' data-width='24' data-height='24' data-back-color='#e8edf3'>";
            echo "<div class='progress-info'>{$progress}</div>";
            echo "</div>";
            echo "</td>";
             */
            echo "<td class='text-center'>" . round($task->progress, 1) . "%</td>";
            $taskStatus = zget($this->lang->task->statusList, $task->status, '');
            echo "<td class='c-status text-center' title='$taskStatus'>";
            echo "<span class='status-task status-{$task->status}'>$taskStatus</span>";
            echo "</td>";

            echo "<td class='c-actions'>";
            common::printIcon('task', 'edit',   "taskID=$task->id", $task, 'list', '', '', '', '', $disabled);

            if(empty($task->children))
            {
                if($task->status != 'pause') common::printIcon('task', 'start', "taskID=$task->id", $task, 'list', '', '', 'iframe', true, $disabled);
                if($task->status == 'pause') common::printIcon('task', 'restart', "taskID=$task->id", $task, 'list', '', '', 'iframe', true, $disabled);

                common::printIcon('task', 'close',  "taskID=$task->id", $task, 'list', '', '', 'iframe', true, $disabled);
                if($canRecordEstimate)
                {
                    common::printIcon('task', 'finish', "taskID=$task->id", $task, 'list', '', '', 'iframe', true, $disabled);
                    common::printIcon('task', 'recordEstimate', array('taskID' => $task->id, 'random' => time()), $task, 'list', 'time', '', 'iframe', true, $disabled);
                }
            }

            common::printIcon('task', 'batchCreate', "execution=$task->execution&storyID=$task->story&moduleID=$task->module&taskID=$task->id&ifame=0", $task, 'list', 'split', '', '', '', $disabled, $this->lang->task->subdivide);
            echo "</td>";
            echo "</tr>";

            if(!empty($task->children)) $this->printTask($task->children, $grade + 1, $taskPath, $canRecordEstimate, $canTaskView, $projectStatus);
        }
    }

    /**
     * 新的项目计划 生成阶段
     * @param $executions
     * @param $tasks
     * @param int $grade
     * @param string $projectStatus
     * @return array
     */
    public function printStageNew($executions, $tasks, $grade = 1, $projectStatus = '',$projectType)
    {
        if(empty($executions)) return array();

        $disabled = $projectStatus == 'closed' ? ' disabled style="pointer-events: none;" ' : '';
        // 判断用户必须是项目团队成员或超级管理员才可以提交工时。
        $canRecordEstimate = $this->loadModel('project')->isCanRecordEstimate($this->session->project);
        $canExecutionView  = common::hasPriv('execution', 'view');
        $canTaskView       = common::hasPriv('task', 'view');

        foreach($executions as $execution)
        {
            echo "<tr data-id='$execution->id' data-order='$execution->order'>";

            echo "<td class='text-left item' " . (!empty($execution->children) ? 'has-child' : '') .  " data-path='" . $execution->path . "' style='padding-left:" . ($grade-1)*40 . "px'>";
            if(!empty($execution->children) || !empty($tasks[$execution->id]))
            {
                $isOpen = $grade == 2 ? 'collapsed plan-toggle-show' : '';
                echo '<span class="table-nest-icon icon table-nest-toggle plan-toggle ' . $isOpen . ($execution->type !== 'stage' ? 'collapsed' : ''). '" data-path="' . $execution->path . '" data-id="' . $execution->id . '"></span>';
            }
            else
            {
                echo '<span class="table-nest-icon icon table-nest-toggle plan-toggle ' . ($execution->type !== 'stage' ? 'collapsed' : ''). '" data-path="' . $execution->path . '" data-id="' . $execution->id . '"></span>';
            }

            if($execution->milestone) echo "<icon class='icon icon-flag icon-sm red' title='{$this->lang->project->milestone}'></icon>";
            if($canExecutionView)
            {
                echo "<a href='" . helper::createLink('newexecution', 'view', 'execution=' . $execution->id) . "' title = '{$execution->name}'>{$execution->name}</a> ";
            }
            else
            {
                echo "<span  title = '{$execution->name}'>{$execution->name}</span> ";
            }
            if(isset($execution->delay)) echo "<span class='label label-danger label-badge'>{$this->lang->execution->delayed}</span> ";
            echo "</td>";

           /* echo "<td>" . $execution->begin . "</td>";
            echo "<td>" . $execution->end . "</td>";
            $plan = $execution->planDuration == 0 ? '-' : $execution->planDuration;
            echo "<td class='hours' title='{$plan}'>{$plan}</td>";*/
            //echo "<td class='hours' title='{$execution->planDuration}'>{$execution->planDuration}</td>";

          /*  echo "<td>" . $execution->realBegan . "</td>";
            echo "<td>" . $execution->realEnd . "</td>";
            $realDuration = $execution->realDuration ? $execution->realDuration : '-';
            echo "<td class='hours' title='{$realDuration}'>" . $realDuration . "</td>";*/
            //echo "<td class='hours' title='{$execution->realDuration}'>" . ($execution->realDuration ? $execution->realDuration : '0') . "</td>";
           /* $diff = $execution->realDuration == 0 ? '0' : $execution->realDuration-$execution->planDuration;
            echo "<td class='hours' title='$diff'>$diff</td>"; *///报工改造去掉

         /*   echo "<td class='hours' title='{$execution->hours->totalEstimate} {$this->lang->execution->workHour}'>{$execution->hours->totalEstimate}{$this->lang->execution->workHourUnit}</td>";*/
         //20240528 更新项目列表实际工作量，将此内容隐藏
            /*echo "<td class='hours' title='{$execution->hours->totalConsumed} {$this->lang->execution->workHour}'>";
            echo html::a(helper::createLink('newExecution', 'view', array('id' => $execution->id)), $execution->hours->totalConsumed . $this->lang->execution->workHourUnit);
            echo "</td>";*/
            /*echo "<td class='hours' title='{$execution->hours->computerLeft} {$this->lang->execution->workHour}'>{$execution->hours->computerLeft}{$this->lang->execution->workHourUnit}</td>";*/

            $changedTimes = $execution->version <=1 ? '' : $execution->version - 1;
          /*  echo "<td class='text-right' title='{$changedTimes}'>" . $changedTimes . "</td>";*/ //报工改造去掉
            echo "<td class='' title='$execution->taskCount'>" . html::a(helper::createLink('execution', 'task', 'execution=' . $execution->id.'&type=all'), $execution->taskCount) . "</td>";

           /* echo "<td class='text-right'>" . round($execution->progress, 1) . "%</td>";*/ //报工改造去掉
         /*   $executionStatus = $this->processStatus('execution', $execution);
            echo "<td class='c-status text-center' title='$executionStatus'>";
            echo "<span class='status-execution status-{$execution->status}'>$executionStatus</span>";*/
            echo "</td>";

            if($this->app->user->account =='admin'){
                echo "<td class='c-actions'>";
                common::printIcon('newexecution', 'edit', "executionID=$execution->id", $execution, 'list', 'edit', '', 'iframe', true, $disabled);
                if($execution->grade == 2)
                {

                    $priv =  commonModel::hasPriv('task', 'batchCreate');
                    if($priv and empty($disabled))
                    {
                        echo html::a(helper::createLink('task', 'batchCreate', "executionID=$execution->id"), "<i class='icon-task-batcheCreate icon-split'></i>", '', "class='btn ' title='{$this->lang->task->subdivide}'", false);
                    }
                    else
                    {
                        echo "<button type='button' class='disabled btn '><i class='icon-task-batcheCreate icon-split' title='{$this->lang->task->subdivide}' ></i></button>";
                    }
                }
                else if($execution->grade == 1 )
                {
                    common::printIcon('programplan', 'create', "program=$execution->project&productID=0&planID=$execution->id&flag=new", $execution, 'list', 'split', '', '', '', $disabled, $this->lang->programplan->createSubPlan);
                }
                echo "</td>";
            }else{
                if(strpos($projectType,'DEP') !== false){
                    echo "<td class='c-actions'>";
                    if( strpos($execution->name,'部门实现') === false && ($execution->grade == 2 && strpos($execution->parentName,'部门实现')  === false)){
                        common::printIcon('newexecution', 'edit', "executionID=$execution->id", $execution, 'list', 'edit', '', 'iframe', true, $disabled);
                    }
                    if($execution->grade == 2 && strpos($execution->parentName,'部门实现') === false)
                    {

                        $priv =  commonModel::hasPriv('task', 'batchCreate');
                        if($priv and empty($disabled))
                        {
                            echo html::a(helper::createLink('task', 'batchCreate', "executionID=$execution->id"), "<i class='icon-task-batcheCreate icon-split'></i>", '', "class='btn ' title='{$this->lang->task->subdivide}'", false);
                        }
                        else
                        {
                            echo "<button type='button' class='disabled btn '><i class='icon-task-batcheCreate icon-split' title='{$this->lang->task->subdivide}' ></i></button>";
                        }
                    }
                    else if($execution->grade == 1 && strpos($execution->name,'部门实现') === false)
                    {
                        common::printIcon('programplan', 'create', "program=$execution->project&productID=0&planID=$execution->id&flag=new", $execution, 'list', 'split', '', '', '', $disabled, $this->lang->programplan->createSubPlan);
                    }
                    echo "</td>";
                }else{
                    echo "<td class='c-actions'></td>";
                }
            }

            echo "</tr>";

            $this->printStageNew($execution->children, $tasks, $grade + 1, $projectStatus,$projectType);
            if(isset($tasks[$execution->id])) $this->printTaskNew($tasks[$execution->id], $grade + 1, $execution->path, $canRecordEstimate, $canTaskView, $projectStatus,$projectType);
        }
    }

    /**
     * 新的项目计划 生成任务
     * @param $tasks
     * @param int $grade
     * @param string $path
     * @param int $canRecordEstimate
     * @param bool $canTaskView
     * @param string $projectStatus
     */
    public function printTaskNew($tasks, $grade = 1, $path = '', $canRecordEstimate = 0, $canTaskView = false, $projectStatus = '' ,$projectType)
    {
        $disabled = $projectStatus == 'closed' ? ' disabled style="pointer-events: none;" ' : '';
        foreach($tasks as $task)
        {
            // 默认所有的任务一开始进行隐藏。
            echo "<tr class='hidden' data-id='$task->id'>";

            $taskPath = $path . '-' . $task->id; // 分隔符不能和阶段一致，防止混淆
            $hasChild = !empty($task->children);
            $demandOrProOrSecond =  $this->loadModel('task')->getTaskDemandProblemDesc($task->id); // 关联的问题单、需求单、二线工单

            $desc = isset($demandOrProOrSecond->desc) ? strip_tags($demandOrProOrSecond->desc) : (isset($demandOrProOrSecond->summary) ?strip_tags( $demandOrProOrSecond->summary ): '');
            $taskName = $task->grade == 1 ? (strpos($projectType,'DEP') !== false ? $desc : $task->name) : $desc ;

            echo "<td class='text-left item' " . (!empty($task->children) ? 'has-child' : '') . " data-path='" . $taskPath . "' title='$taskName' style='padding-left:" . (($grade-1)*40 - ($hasChild ? 25 : 0)) . "px'>";
            if($hasChild)
            {
                echo '<span class="table-nest-icon icon table-nest-toggle plan-toggle" data-path="' . $taskPath . '" data-id="' . $task->id . '"></span>';
            }
            //echo !empty($task->children) ? $task->name : html::a(helper::createLink('task', 'view', 'task=' . $task->id), $task->name);
            $taskPri   = "<span class='label-pri label-pri-{$task->pri}'>";
            $taskPri  .= zget($this->lang->task->priList, $task->pri, $task->pri);
            $taskPri  .= "</span> ";
            $extraLogo = '[ T ] ' . $taskPri;

            if($canTaskView)
            {
                //为了任务来自不同tab 增加version flag区分来源
                echo html::a(helper::createLink('task', 'view', 'task=' . $task->id."&version=$task->version&flag=2"), $extraLogo . $task->name);
            }
            else
            {
                echo $extraLogo . $task->name;
            }

            $delay = helper::diffDate(helper::today(), $task->deadline);
            if($task->status != 'done' and $task->status != 'closed' and $task->status != 'pause' and $task->status != 'cancel')
            {
                if($delay > 0) echo "<span class='label label-danger label-badge'>{$this->lang->task->delayed}</span> ";
            }
            echo "</td>";

          /*  echo "<td>" . $task->estStarted . "</td>";
            echo "<td>" . $task->deadline . "</td>";
            $plan = $task->planDuration == 0 ? '-' : $task->planDuration;
            echo "<td class='hours' title='{$plan}'>{$plan}</td>";*/
            //echo "<td class='hours' title='{$task->planDuration}'>{$task->planDuration}</td>";//报工改造去掉
            //echo "<td class='hours' title='{$task->estimate} {$this->lang->execution->workHour}'>{$task->estimate}{$this->lang->execution->workHourUnit}</td>";

           /* echo "<td>" . substr($task->realStarted, 0, 10) . "</td>";
            echo "<td>" . substr($task->finishedDate, 0, 10) . "</td>";
            $realDuration = $task->realDuration ? $task->realDuration : '-';
            echo "<td class='hours' title='{$realDuration}'>" . $realDuration. "</td>";*/
            //echo "<td class='hours' title='{$task->realDuration}'>" . ($task->realDuration ? $task->realDuration : '0') . "</td>";
           /*$diff = $task->realDuration == 0 ? '0' : $task->realDuration - $task->planDuration;
            echo "<td class='hours' title='{$diff}'>{$diff}</td>";*/  //报工改造去掉
            //echo "<td class='hours' title='{$task->consumed} {$this->lang->execution->workHour}'>{$task->consumed}{$this->lang->execution->workHourUnit}</td>";
            //echo "<td class='hours' title='{$task->left} {$this->lang->execution->workHour}'>{$task->left}{$this->lang->execution->workHourUnit}</td>";

            //任务涉及到到工作量偏差等保留一位小数
            $estimate = round($task->estimate,1);
           /* echo "<td class='hours' title='{$estimate} {$this->lang->execution->workHour}'>{$estimate}{$this->lang->execution->workHourUnit}</td>";*/
            //20240528 根据修改列表实际工作量内容，将此项隐藏
            /* $consumed = round($task->consumed,1);
            echo "<td class='hours' title='{$consumed} {$this->lang->execution->workHour}'>{$consumed}{$this->lang->execution->workHourUnit}</td>";*/
            /*$diff = round($task->consumed - $task->estimate,1);
            echo "<td class='hours' title='{$diff} {$this->lang->execution->workHour}'>{$diff}{$this->lang->execution->workHourUnit}</td>";*/

            //$taskStatus = zget($this->lang->task->statusList, $task->status, '');
            //echo "<td class='c-status text-center' title='$taskStatus'>";
            //echo "<span class='status-task status-{$task->status}'>$taskStatus</span>";
            //echo "</td>";

            //echo "<td title='$task->resource'>" . $task->resource . "</td>";
            $changedTimes = $task->version ? $task->version : '';
           /* echo "<td class='text-right' title='{$changedTimes}'>" . $changedTimes . "</td>";*/ //报工改造去掉
            echo "<td class=''></td>"; //任务的任务数不再显示0

            /*
            echo "<td class='c-progress'>";
            $progress = $task->consumed == 0 ? 0 : round($task->consumed/($task->consumed+$task->left)*100);
            echo "<div class='progress-pie' data-doughnut-size='90' data-color='#00da88' data-value='{$progress}' data-width='24' data-height='24' data-back-color='#e8edf3'>";
            echo "<div class='progress-info'>{$progress}</div>";
            echo "</div>";
            echo "</td>";
             */
            /*echo "<td class='text-right'>" . round($task->progress, 1) . "%</td>";*/ //报工改造去掉
          /*  $taskStatus = zget($this->lang->task->statusList, $task->status, '');
            echo "<td class='c-status text-center' title='$taskStatus'>";
            echo "<span class='status-task status-{$task->status}'>$taskStatus</span>";*/
            echo "</td>";

            echo "<td class='c-actions'>";

            if($this->app->user->account == 'admin'){
                if($task->parent){
                    common::printIcon('task', 'editTask',   "taskID=$task->id", $task, 'list', 'edit', '', 'iframe', 'true', $disabled);
                }
                if( $task->grade == '1'){
                    common::printIcon('task', 'editTask',   "taskID=$task->id", $task, 'list', 'edit', '', 'iframe', 'true', $disabled);
                    common::printIcon('task', 'finish', "taskID=$task->id&source=new", $task, 'list', '', '', 'iframe', true, $disabled);

                }
                if(empty($task->children) && $task->parent)
                {
                    //新项目计划 只保留 编辑 报工 完成(只存在具体单号任务上)
                    /*if($task->status != 'pause') common::printIcon('task', 'start', "taskID=$task->id", $task, 'list', '', '', 'iframe', true, $disabled);
                    if($task->status == 'pause') common::printIcon('task', 'restart', "taskID=$task->id", $task, 'list', '', '', 'iframe', true, $disabled);

                    common::printIcon('task', 'close',  "taskID=$task->id", $task, 'list', '', '', 'iframe', true, $disabled);*/
                    if($canRecordEstimate)
                    {
                        common::printIcon('task', 'finish', "taskID=$task->id&source=new", $task, 'list', '', '', 'iframe', true, $disabled);
                        // common::printIcon('task', 'recordEstimate', array('taskID' => $task->id, 'random' => time()), $task, 'list', 'time', '', 'iframe', true, $disabled);
                    }
                }

                if(strpos($projectType,'DEP') === false && $task->grade == '1'){
                    common::printIcon('task', 'batchCreate', "execution=$task->execution&storyID=$task->story&moduleID=$task->module&taskID=$task->id&ifame=0", $task, 'list', 'split', '', '', '', $disabled, $this->lang->task->subdivide);
                }
            }else{
                if(strpos($projectType,'二线') === false && strpos($projectType,'EX') === false && strpos($projectType,'DEP') === false && $task->grade == '1' && strpos( $task->name,'任务') !== false){
                    common::printIcon('task', 'editTask',   "taskID=$task->id", $task, 'list', 'edit', '', 'iframe', 'true', $disabled);
                    common::printIcon('task', 'finish', "taskID=$task->id&source=new", $task, 'list', '', '', 'iframe', true, $disabled);

                }
                if($task->parent){
                    common::printIcon('task', 'editTask',   "taskID=$task->id", $task, 'list', 'edit', '', 'iframe', 'true', $disabled);
                }
                if(strpos($projectType,'DEP') !== false && $task->grade == '1'){
                    common::printIcon('task', 'editTask',   "taskID=$task->id", $task, 'list', 'edit', '', 'iframe', 'true', $disabled);
                    common::printIcon('task', 'finish', "taskID=$task->id&source=new", $task, 'list', '', '', 'iframe', true, $disabled);

                }
                if(empty($task->children) && $task->parent)
                {
                    //新项目计划 只保留 编辑 报工 完成(只存在具体单号任务上)
                    /*if($task->status != 'pause') common::printIcon('task', 'start', "taskID=$task->id", $task, 'list', '', '', 'iframe', true, $disabled);
                    if($task->status == 'pause') common::printIcon('task', 'restart', "taskID=$task->id", $task, 'list', '', '', 'iframe', true, $disabled);

                    common::printIcon('task', 'close',  "taskID=$task->id", $task, 'list', '', '', 'iframe', true, $disabled);*/
                   if($canRecordEstimate)
                   {
                      common::printIcon('task', 'finish', "taskID=$task->id&source=new", $task, 'list', '', '', 'iframe', true, $disabled);
                            // common::printIcon('task', 'recordEstimate', array('taskID' => $task->id, 'random' => time()), $task, 'list', 'time', '', 'iframe', true, $disabled);
                   }
                }
            }
           /* if(strpos($projectType,'DEP') !== false && $task->grade == '1' && $task->dataVersion == '1'){
                common::printIcon('task', 'batchCreate', "execution=$task->execution&storyID=$task->story&moduleID=$task->module&taskID=$task->id&ifame=0", $task, 'list', 'split', '', '', '', $disabled, $this->lang->task->subdivide);
            }*/
            echo "</td>";
            echo "</tr>";

            if(!empty($task->children)) $this->printTaskNew($task->children, $grade + 1, $taskPath, $canRecordEstimate, $canTaskView, $projectStatus, $projectType);
        }
    }
    /**
     * Project: chengfangjinke
     * Method: getProjectStats
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:15
     * Desc: This is the code comment. This method is called getProjectStats.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $programID
     * @param string $browseType
     * @param int $queryID
     * @param string $orderBy
     * @param null $pager
     * @param int $programTitle
     * @param int $involved
     * @param false $queryAll
     * @return array
     */
    public function getProjectStats($programID = 0, $browseType = 'undone', $queryID = 0, $orderBy = 'id_desc', $pager = null, $programTitle = 0, $involved = 0, $queryAll = false)
    {
        /* Init vars. */
        $projects = $this->loadModel('program')->getProjectList($programID, $browseType, $queryID, $orderBy, $pager, $programTitle, $involved, $queryAll);

        if(empty($projects)) return array();
        $projectCode = $this->dao->select('project,mark')->from(TABLE_PROJECTPLAN)->where('project')->in(array_keys($projects))->fetchPairs();

        $projectKeys = array_keys($projects);
        $stats       = array();
        $hours       = array();
        $emptyHour   = array('totalEstimate' => 0, 'totalConsumed' => 0, 'totalLeft' => 0, 'progress' => 0);
        $leftTasks   = array();
        $teamMembers = array();

        $taskMap = array();
        $tasks = $this->dao->select('*')->from(TABLE_TASK)
            ->where('deleted')->eq(0)
            ->andWhere('project')->in($projectKeys)
            ->andWhere('parent' )->eq(0)
            //->andWhere('(parent != 0  or name like "%任务")' ) //20240528去掉此计算工时逻辑
           // ->andWhere('name')->notLike('%已%')
            ->fetchAll('id');
        foreach($tasks as $task)
        {
            if(!isset($taskMap[$task->project])) $taskMap[$task->project] = array('estimate' => 0, 'consumed' => 0, 'left' => 0, 'progress' => 0,'progresstFinsh' => 0, 'progresstTotal' => 0,'dataVersion' => '0');
            //$taskMap[$task->project]['estimate'] += $task->estimate;
            //$taskMap[$task->project]['consumed'] += $task->consumed; //20240528去掉此计算工时逻辑
            $taskMap[$task->project]['consumed'] = 0;
            $taskMap[$task->project]['left'] += $task->left;
            //$taskMap[$task->project]['progress'] += $task->progress * $task->estimate;
            //完成百分比  已完成任务/所有任务
            if($task->status == 'done' || ($task->status == 'closed')){
                    $taskMap[$task->project]['progresstFinsh'] += 1;
            }
            $taskMap[$task->project]['progresstTotal'] += 1;
            $taskMap[$task->project]['dataVersion'] = $task->dataVersion;
        }

        //获取年度计划立项计划工作量
        $projectcreation = $this->dao->select('plan,workload')->from(TABLE_PROJECTCREATION)->where('deleted')->eq(0)->fetchPairs('plan','workload');
        $projectplan = $this->dao->select('project,id')->from(TABLE_PROJECTPLAN)->where('deleted')->eq(0)->andWhere('status')->eq('projected')->fetchPairs('project','id');
        $projectplaninsideStatus = $this->dao->select('project,insideStatus')->from(TABLE_PROJECTPLAN)->where('deleted')->eq(0)->fetchPairs('project','insideStatus');
        $projectplanworkload = $this->dao->select('project,workload')->from(TABLE_PROJECTPLAN)->where('deleted')->eq(0)->fetchPairs('project','workload');
        //20240528 更新计算工时逻辑，从基准工时表获取
       $projectConsumed = $this->getEffortConsumed();
        /* Process projects. */
        foreach($projects as $key => $project)
        {
            $project->code = isset($projectCode[$project->id]) ? $projectCode[$project->id] : '';
            if($project->begin == '0000-00-00') $project->begin = '';
            if($project->end == '0000-00-00') $project->end = '';

            /* Judge whether the project is delayed. */
            if($project->status != 'done' and $project->status != 'closed' and $project->status != 'suspended')
            {
                $delay = helper::diffDate(helper::today(), $project->end);
                if($delay > 0) $project->delay = $delay;
            }
            $project->planDuration = !empty(helper::diffDate($project->end, $project->begin) ) ? helper::diffDate($project->end, $project->begin) + 1 : '';
            $project->realDuration = ($project->realEnd != '0000-00-00' and $project->realBegan != '0000-00-00') ? helper::diffDate($project->realEnd, $project->realBegan)+1 : 0;

            /* Process the hours. */
            $project->estimate = isset($projectcreation[$projectplan[$project->id]]) ? intval($projectcreation[$projectplan[$project->id]]) * $project->workHours * 8 : 0;//获取年度计划立项计划工作量 //isset($taskMap[$project->id]) ? $taskMap[$project->id]['estimate'] : 0;
            //迭代28-如果修改了计划工时，以修改的计划工时为准
            if(!empty($project->planWorkload)){
                $project->estimate = intval($project->planWorkload) * $project->workHours * 8;
            }else{
               /* $projectPlan = $this->dao->select("*")->from(TABLE_PROJECTPLAN)->where('project')->eq($project->id)->andWhere('deleted')->eq(0)->fetch();
                $creation = $this->loadModel('projectplan')->getCreationByID($projectPlan->id);
                $project->estimate = isset($creation->workload) ? intval($creation->workload) * $project->workHours * 8 : 0;*/
                $project->estimate  = 0;
            }
            //$project->consumed = isset($taskMap[$project->id]) ? $taskMap[$project->id]['consumed'] : 0; //20240528去掉此计算工时逻辑
            //20240528 更新计算工时逻辑，从基准工时表获取
            $project->consumed  = isset($projectConsumed[$project->id]) ? $projectConsumed[$project->id]->consumed : 0;
            $project->left     = isset($taskMap[$project->id]) ? $taskMap[$project->id]['left'] : 0;
            if($project->status == 'closed'){
                $project->progress = (isset($taskMap[$project->id]) and ($taskMap[$project->id]['progresstTotal'] != 0)) ? round($taskMap[$project->id]['progresstFinsh']/$taskMap[$project->id]['progresstTotal'],2)*100 : 0;//(isset($taskMap[$project->id]) and ($taskMap[$project->id]['estimate'] != 0)) ? round($taskMap[$project->id]['progress']/$taskMap[$project->id]['estimate']) : 0;
            }else{
                if($taskMap[$task->project]['dataVersion'] == '2'){
                    $project->progress = (isset($taskMap[$project->id]) and ($taskMap[$project->id]['progresstTotal'] != 0)) ? round($taskMap[$project->id]['progresstFinsh']/$taskMap[$project->id]['progresstTotal'],2)*100 : 0;//(isset($taskMap[$project->id]) and ($taskMap[$project->id]['estimate'] != 0)) ? round($taskMap[$project->id]['progress']/$taskMap[$project->id]['estimate']) : 0;
                }else{
                    $project->progress = 0;
                }
            }

            $project->teamCount   = isset($teams[$project->id]) ? $teams[$project->id]->teams : 0;
            $project->leftTasks   = isset($leftTasks[$project->id]) ? $leftTasks[$project->id]->tasks : '—';
            $project->teamMembers = isset($teamMembers[$project->id]) ? array_keys($teamMembers[$project->id]) : array();
            $project->insideStatus = isset($projectplaninsideStatus[$project->id]) ? $projectplaninsideStatus[$project->id] : '';
            $project->workload = isset($projectplanworkload[$project->id]) ? $projectplanworkload[$project->id] : '';
            $stats[$key] = $project;
        }
        return $stats;
    }

    /**
     * 查询项目的基准工时表 ，获取所有项目的所有工时，列表-实际工作量
     * @param $project
     * @return int
     */
    public function getEffortConsumed(){
        $allConsumed = $this->dao->select('project,cast(sum(consumed) as decimal(10, 2)) as consumed')->from(TABLE_EFFORT)
            ->where('deleted')->eq('0')
            ->andWhere('objectType')->eq('task')
            ->groupBy('project')
            ->fetchAll('project');

        return $allConsumed ? $allConsumed : array();
    }
    /**
     * Start project.
     *
     * @param  int    $projectID
     * @param  string $type
     * @access public
     * @return array
     */
    public function start($projectID, $type = 'project')
    {
        $oldProject = $this->getById($projectID, $type);
        $now        = helper::now();

        $project = fixer::input('post')
            ->setDefault('realBegan', $now)
            ->setDefault('status', 'doing')
            ->setDefault('lastEditedBy', $this->app->user->account)
            ->setDefault('lastEditedDate', $now)
            ->setDefault('switch', '1')
            ->remove('comment')->get();

        $this->dao->update(TABLE_PROJECT)->data($project)->autoCheck()->where('id')->eq((int)$projectID)->exec();

        if(!dao::isError()) return common::createChanges($oldProject, $project);
    }

    /**
     * Get user requirement entries.
     *
     * @param  int    $projectID
     * @access public
     * @return array
     */
    public function getUserRequirementList($projectID)
    {
        $requirementList = $this->dao->select('id,name')->from(TABLE_REQUIREMENT)->where('project')->eq($projectID)->andwhere('status')->ne('deleted')->fetchPairs();
        return $requirementList;
    }

    /**
     * Project: chengfangjinke
     * Method: buildSearchForm
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:15
     * Desc: This is the code comment. This method is called buildSearchForm.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $actionURL
     * @param $queryID
     */
    public function buildSearchForm($actionURL, $queryID)
    {
        $this->config->project->search['actionURL'] = $actionURL;
        $this->config->project->search['queryID']   = $queryID;

        $this->loadModel('search')->setSearchParams($this->config->project->search);
    }

    /**
     * 检查用户是否拥有项目权限
     *
     * @param $projectId
     * @param $account
     * @param $subObjectID
     * @param $reason
     * @return false|void
     */
    public function checkOwnProjectPermission($projectId,  $account, $subObjectID = 0, $reason = 0){

        $res = false;
        if(!($projectId && $account)){
            return $res;
        }
        //先查项目团队
        $info = $this->dao->select('id')->from(TABLE_TEAM)
            ->where('type')->eq('project')
            ->andWhere('root')->eq($projectId)
            ->andWhere('account')->eq($account)
            ->fetch();
        if(!empty($info)){
            $res = true;
            return $res;
        }
        //查询项目白名单(默认类型)
        $info = $this->dao->select('id')->from(TABLE_ACL)
            ->where('objectType')->eq('project')
            ->andWhere('objectID')->eq($projectId)
            ->andWhere('type')->eq('whitelist')
            ->andWhere('account')->eq($account)
            ->beginIF($reason > 0)->andWhere('reason')->ne($reason)->fi()
            ->fetch();
        if(!empty($info)){
            $res = true;
        }
        if($reason > 0){
            //查询项目白名单(本类型)
            $info = $this->dao->select('id')->from(TABLE_ACL)
                ->where('objectType')->eq('project')
                ->andWhere('objectID')->eq($projectId)
                ->andWhere('subObjectID')->eq($subObjectID)
                ->andWhere('type')->eq('whitelist')
                ->andWhere('account')->eq($account)
                ->andWhere('reason')->eq($reason)
                ->fetch();
            if(!empty($info)){
                $res = true;
            }
        }
        return $res;
    }


    /**
     * 添加项目白名单
     *
     * @param $projectId
     * @param $account
     * @param $subObjectID
     * @param int $reason
     */
    public function addProjectWhitelistInfo($projectId, $account, $subObjectID = 0, $reason = 0){
        if(!($projectId && $account)){
            return  false;
        }
        $addParams = new stdClass();
        $addParams->account    = $account;
        $addParams->objectType = 'project';
        $addParams->objectID   = $projectId;
        $addParams->subObjectID = $subObjectID;
        $addParams->type       = 'whitelist';
        $addParams->source     = 'add';
        $addParams->reason     = $reason;
        $this->dao->insert(TABLE_ACL)->data($addParams)
            ->autoCheck()
            ->exec();
        if(dao::isError()){
            return false;
        }
        $this->loadModel('user')->updateUserView($projectId, 'project', array($account));
        return true;
    }

    /**
     * import testcase from library
     *
     * @param int $projectID
     * @param int applicationID
     * @param int productID
     * @return void
     */
    public function importFromLib($projectID, $applicationID, $productID)
    {
        $data = fixer::input('post')->get();

        $prevModule = 0;
        foreach($data->module as $i => $module)
        {
            if($module != 'ditto') $prevModule = $module;
            if($module == 'ditto') $data->module[$i] = $prevModule;
        }

        $libCases = $this->dao->select('*')->from(TABLE_CASE)->where('deleted')->eq(0)->andWhere('id')->in($data->caseIdList)->fetchAll('id');
        $libSteps = $this->dao->select('*')->from(TABLE_CASESTEP)->where('`case`')->in($data->caseIdList)->orderBy('id')->fetchGroup('case');
        $libFiles = $this->dao->select('*')->from(TABLE_FILE)->where('objectID')->in($data->caseIdList)->andWhere('objectType')->eq('testcase')->fetchGroup('objectID', 'id');
        foreach($libCases as $libCaseID => $case)
        {
            $case->project         = $projectID;
            $case->fromCaseID      = $case->id;
            $case->fromCaseVersion = $case->version;
            $case->product         = $productID;
            $case->applicationID   = $applicationID;
            if(isset($data->module[$case->id])) $case->module = $data->module[$case->id];
            if(isset($data->branch[$case->id])) $case->branch = $data->branch[$case->id];
            unset($case->id);

            $this->dao->insert(TABLE_CASE)->data($case)->autoCheck()->exec();

            if(!dao::isError())
            {
                $caseID = $this->dao->lastInsertID();
                if(isset($libSteps[$libCaseID]))
                {
                    foreach($libSteps[$libCaseID] as $step)
                    {
                        $step->case = $caseID;
                        unset($step->id);
                        $this->dao->insert(TABLE_CASESTEP)->data($step)->exec();
                    }
                }

                /* If under the project module, the cases is imported need linking to the project. */
                if($this->app->openApp == 'project')
                {
                    $lastOrder = (int)$this->dao->select('*')->from(TABLE_PROJECTCASE)->where('project')->eq($this->session->project)->orderBy('order_desc')->limit(1)->fetch('order');

                    $this->dao->insert(TABLE_PROJECTCASE)
                        ->set('project')->eq($this->session->project)
                        ->set('product')->eq($case->product)
                        ->set('case')->eq($caseID)
                        ->set('version')->eq($case->version)
                        ->set('order')->eq(++ $lastOrder)
                        ->exec();
                }

                /* Fix bug #1518. */
                $oldFiles = zget($libFiles, $libCaseID, array());
                foreach($oldFiles as $fileID => $file)
                {
                    $file->objectID  = $caseID;
                    $file->addedBy   = $this->app->user->account;
                    $file->addedDate = helper::now();
                    $file->downloads = 0;
                    unset($file->id);
                    $this->dao->insert(TABLE_FILE)->data($file)->exec();
                }
                $this->loadModel('action')->create('case', $caseID, 'fromlib', '', $case->lib);
            }
        }
    }
}
