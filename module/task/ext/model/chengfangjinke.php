<?php
public function batchCreateEffort($taskID, $efforts)
{
    return $this->loadExtension('chengfangjinke')->batchCreateEffort($taskID, $efforts);
}

public function createEffort($taskID, $consumed, $left, $date)
{
    return $this->loadExtension('chengfangjinke')->createEffort($taskID, $consumed, $left, $date);
}

public function computeConsumed($taskID)
{
    return $this->loadExtension('chengfangjinke')->computeConsumed($taskID);
}

public function computeTask($taskID)
{
    return $this->loadExtension('chengfangjinke')->computeTask($taskID);
}

public function create2($executionID, $auto = false)
{
    return $this->loadExtension('chengfangjinke')->create($executionID, $auto);
}

public function start2($taskID)
{
    return $this->loadExtension('chengfangjinke')->start($taskID);
}

public function finish2($taskID)
{
    return $this->loadExtension('chengfangjinke')->finish($taskID);
}

public function update2($taskID, $auto = false)
{
    return $this->loadExtension('chengfangjinke')->update($taskID, $auto);
}

public function close2($taskID)
{
    return $this->loadExtension('chengfangjinke')->close($taskID);
}

public function getByID($taskID, $setImgSize = false)
{
    return $this->loadExtension('chengfangjinke')->getById($taskID, $setImgSize);
}

public function processTask2NOUSE($task)
{
    $today = helper::today();

    /* Delayed or not?. */
    if($task->status !== 'done' and $task->status !== 'cancel' and $task->status != 'closed')
    {
        if(!helper::isZeroDate($task->deadline))
        {
            $delay = helper::diffDate($today, $task->deadline);
            if($delay > 0) $task->delay = $delay;
        }
    }

    /* Story changed or not. */
    $task->needConfirm = false;
    if(!empty($task->storyStatus) and $task->storyStatus == 'active' and $task->latestStoryVersion > $task->storyVersion) $task->needConfirm = true;

    /* Set product type for task. */
    if(!empty($task->product))
    {
        $product = $this->loadModel('product')->getById($task->product);
        if($product) $task->productType = $product->type;
    }

    /* Set closed realname. */
    if($task->assignedTo == 'closed') $task->assignedToRealName = 'Closed';

    /* Compute task progress. */
    if($task->consumed == 0 and $task->left == 0)
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
    }

    return $task;
}

/**
 * Create a batch task.
 *
 * @param  int    $executionID
 * @access public
 * @return void
 */
public function batchCreate($executionID ,$dataVersion)
{
    $this->loadModel('action');
    $now      = helper::now();
    $mails    = array();
    $tasks    = fixer::input('post')->get();

    $storyIDs  = array();
    $taskNames = array();
    $preStory  = 0;

    /* Judge whether the current task is a parent. */
    $parentID = !empty($this->post->parent[0]) ? $this->post->parent[0] : 0;
    $oldParentTask = $this->dao->select('*')->from(TABLE_TASK)->where('id')->eq((int)$parentID)->fetch();

    foreach($tasks->story as $key => $storyID)
    {
        if(empty($tasks->name[$key])) continue;
        if($tasks->type[$key] == 'affair') continue;
        if($tasks->type[$key] == 'ditto' && isset($tasks->type[$key - 1]) && $tasks->type[$key - 1] == 'affair') continue;

        if($storyID == 'ditto') $storyID = $preStory;
        $preStory = $storyID;

        $inNames = in_array($tasks->name[$key], $taskNames);
        if(!$inNames || ($inNames && !in_array($storyID, $storyIDs)))
        {
            $storyIDs[]  = $storyID;
            $taskNames[] = $tasks->name[$key];
        }
        else
        {
            dao::$errors['message'][] = sprintf($this->lang->duplicate, $this->lang->task->common) . ' ' . $tasks->name[$key];
            return false;
        }
    }

    $result = $this->loadModel('common')->removeDuplicate('task', $tasks, "execution=$executionID and story " . helper::dbIN($storyIDs));
    $tasks  = $result['data'];

    $story      = 0;
    $module     = 0;
    $type       = '';
    $assignedTo = '';

    /* Get task data. */
    $extendFields = $this->getFlowExtendFields();
    $projectID    = $this->getProjectID($executionID);
    $data         = array();
    $dataNumber   = 0;
    foreach($tasks->name as $i => $name)
    {
        $story      = !isset($tasks->story[$i]) || $tasks->story[$i]           == 'ditto' ? $story     : $tasks->story[$i];
        $module     = !isset($tasks->module[$i]) || $tasks->module[$i]         == 'ditto' ? $module    : $tasks->module[$i];
        $type       = !isset($tasks->type[$i]) || $tasks->type[$i]             == 'ditto' ? $type      : $tasks->type[$i];
        $assignedTo = !isset($tasks->assignedTo[$i]) || $tasks->assignedTo[$i] == 'ditto' ? $assignedTo: $tasks->assignedTo[$i];

        if(empty($tasks->name[$i])) continue;
        $dataNumber++;

        $data[$i]             = new stdclass();
        $data[$i]->story      = (int)$story;
        $data[$i]->type       = $type;
        $data[$i]->module     = (int)$module;
        $data[$i]->assignedTo = $assignedTo;
        $data[$i]->color      = $tasks->color[$i];
        $data[$i]->name       = $tasks->name[$i];
        $data[$i]->desc       = nl2br($tasks->desc[$i]);
        $data[$i]->pri        = $tasks->pri[$i];
        $data[$i]->estimate   = $tasks->estimate[$i];
        $data[$i]->left       = $tasks->estimate[$i];
        $data[$i]->project    = $this->config->systemMode == 'new' ? $projectID : 0;
        $data[$i]->execution  = $executionID;
        $data[$i]->estStarted = empty($tasks->estStarted[$i]) ? '0000-00-00' : $tasks->estStarted[$i];
        $data[$i]->deadline   = empty($tasks->deadline[$i]) ? '0000-00-00' : $tasks->deadline[$i];
        $data[$i]->status     = 'wait';
        $data[$i]->openedBy   = $this->app->user->account;
        $data[$i]->openedDate = $now;
        $data[$i]->parent     = $tasks->parent[$i];
        $data[$i]->dataVersion     = $dataVersion;

        // 计划工期计算。
        $data[$i]->planDuration = (strtotime(substr($data[$i]->deadline, 0, 10)) - strtotime(substr($data[$i]->estStarted, 0, 10))) / 86400 + 1;

        if($story) $data[$i]->storyVersion = $this->loadModel('story')->getVersion($data[$i]->story);
        if($assignedTo) $data[$i]->assignedDate = $now;
        if(strpos($this->config->task->create->requiredFields, 'estStarted') !== false and empty($tasks->estStarted[$i])) $data[$i]->estStarted = '';
        if(strpos($this->config->task->create->requiredFields, 'deadline') !== false and empty($tasks->deadline[$i]))     $data[$i]->deadline   = '';

        foreach($extendFields as $extendField)
        {
            $data[$i]->{$extendField->field} = htmlspecialchars($this->post->{$extendField->field}[$i]);
            $message = $this->checkFlowRule($extendField, $data[$i]->{$extendField->field});
            if($message)
            {
                dao::$errors['message'][] = sprintf($message);
                return false;
            }
        }
    }

    if($dataNumber == 0)
    {
        dao::$errors['message'][] = $this->lang->task->dataVolumeEmpty;
        return false;
    }

    /* Fix bug #1525*/
    $executionType  = $this->dao->select('*')->from(TABLE_PROJECT)->where('id')->eq($executionID)->fetch('type');
    $requiredFields = ',' . $this->config->task->create->requiredFields . ',';
    if($executionType == 'ops') $requiredFields = str_replace(',story,', ',', $requiredFields);
    $requiredFields = trim($requiredFields, ',');

    /* check data. */
    foreach($data as $i => $task)
    {
        if(mb_strlen($task->name) > 80)
        {
            dao::$errors['message'][] = $this->lang->task->taskNameError;
            return false;
        }

        if(!helper::isZeroDate($task->deadline) and $task->deadline < $task->estStarted)
        {
            dao::$errors['message'][] = $this->lang->task->error->deadlineSmall;
            return false;
        }

        if($task->estimate and !preg_match("/^[0-9]+(.[0-9]{1,3})?$/", $task->estimate))
        {
            dao::$errors['message'][] = $this->lang->task->error->estimateNumber;
            return false;
        }

        foreach(explode(',', $requiredFields) as $field)
        {
            $field = trim($field);
            if(empty($field)) continue;

            if(!isset($task->$field)) continue;
            if(!empty($task->$field)) continue;
            if($field == 'estimate' and strlen(trim($task->estimate)) != 0) continue;

            dao::$errors['message'][] = sprintf($this->lang->error->notempty, $this->lang->task->$field);
            return false;
        }
        if($task->estimate) $task->estimate = (float)$task->estimate;
    }

    // 已消耗父任务创建同名的子任务(之前在后面，现在挪到前面来了)。
    if($oldParentTask)
    {
        /* When common task are child tasks and the common task has consumption, create a child task. */
        // 判断该任务是否有工时消耗，如果有则需要创建同名子任务。
        // 判断如果有消耗，再判断一下是否创建了同名子任务，创建了就不用再创建了。
        $oldTaskID = $this->dao->select('id')->from(TABLE_TASK)->where('parent')->eq($oldParentTask->id)->andWhere('name')->eq($oldParentTask->name)->fetch('id');

        $taskConsumed = $this->dao->select('cast(sum(consumed) as decimal(11, 2)) as consumed')->from(TABLE_EFFORT)
            ->where('objectID')->eq($oldParentTask->id)
            ->andWhere('objectType')->eq('task')
            ->andWhere('deleted')->eq('0')
            ->fetch('consumed');

        if(empty($taskConsumed)) $taskConsumed = 0;

        // 根据任务实际的消耗来判断是否创建子任务。
        //if($oldParentTask->consumed > 0 and empty($oldTaskID))
        if($taskConsumed > 0 and empty($oldTaskID))
        {
            $clonedTask = clone $oldParentTask;
            unset($clonedTask->id);
            $clonedTask->parent = $parentID;
            $this->dao->insert(TABLE_TASK)->data($clonedTask)->autoCheck()->exec();

            $clonedTaskID = $this->dao->lastInsertID();

            // 计算父任务状态和所属阶段状态及完成度。
            $this->computeConsumed($clonedTaskID);

            // 计算任务path和grade字段。
            if($clonedTask->parent)
            {
                $newPath = $this->getById($clonedTask->parent)->path;
                $newPath = $newPath . ',' . $clonedTaskID;
                $grade   = $this->getById($clonedTask->parent)->grade;
                $grade   = $grade +1;
                $this->dao->update(TABLE_TASK)->set('path')->eq($newPath)->set('grade')->eq($grade)->where('id')->eq($clonedTaskID)->exec();
            }
            else
            {
                $newPath = $clonedTaskID;
                $this->dao->update(TABLE_TASK)->set('path')->eq($newPath)->set('grade')->eq(1)->where('id')->eq($clonedTaskID)->exec();
            }

            /* ZenTao Pro and ZenTao Biz update TABLE_EFFORT.  将父任务的工时消耗修改到子任务下。*/
            $this->dao->update(TABLE_EFFORT)->set('objectID')->eq($clonedTaskID)->where('objectID')->eq($oldParentTask->id)->exec();
        }

        $this->computeBeginAndEnd($parentID);

        $task = new stdclass();
        //$task->parent         = '0';
        $task->lastEditedBy   = $this->app->user->account;
        $task->lastEditedDate = $now;
        $this->dao->update(TABLE_TASK)->data($task)->where('id')->eq($parentID)->exec();
    }

    $childTasks = null;
    foreach($data as $i => $task)
    {
        $task->version = 0;//1;新建变更次数设为0
        $this->dao->insert(TABLE_TASK)->data($task)
            ->autoCheck()
            ->checkIF($task->estimate != '', 'estimate', 'float')
            ->exec();

        if(dao::isError()) return false;

        $taskID   = $this->dao->lastInsertID();
        $taskSpec = new stdClass();
        $taskSpec->task       = $taskID;
        $taskSpec->version    = $task->version;
        $taskSpec->name       = $task->name;
        $taskSpec->estStarted = $task->estStarted;
        $taskSpec->deadline   = $task->deadline;

        $this->dao->insert(TABLE_TASKSPEC)->data($taskSpec)->autoCheck()->exec();
        if(dao::isError()) return false;

        $childTasks .= $taskID . ',';
        if($story) $this->story->setStage($task->story);

        $this->executeHooks($taskID);

        $actionID = $this->action->create('task', $taskID, 'Opened', '');
        if(!dao::isError()) $this->loadModel('score')->create('task', 'create', $taskID);

        $mails[$i] = new stdclass();
        $mails[$i]->taskID   = $taskID;
        $mails[$i]->actionID = $actionID;

        // 计算父任务状态和所属阶段状态及完成度。
        $this->computeConsumed($taskID);

        // 计算任务path和grade字段。
        if($task->parent)
        {
            $newPath = $this->getById($task->parent)->path;
            $newPath = $newPath . ',' . $taskID;
            $grade   = $this->getById($task->parent)->grade;
            $grade   = $grade +1;
            $this->dao->update(TABLE_TASK)->set('path')->eq($newPath)->set('grade')->eq($grade)->where('id')->eq($taskID)->exec();
        }
        else
        {
            $newPath = $taskID;
            $this->dao->update(TABLE_TASK)->set('path')->eq($newPath)->set('grade')->eq(1)->where('id')->eq($taskID)->exec();
        }
    }

    if($oldParentTask)
    {
        $newParentTask = $this->dao->select('*')->from(TABLE_TASK)->where('id')->eq((int)$parentID)->fetch();
        $changes       = common::createChanges($oldParentTask, $newParentTask);
        $actionID      = $this->action->create('task', $parentID, 'createChildren', '', trim($childTasks, ','));
        if(!empty($changes)) $this->action->logHistory($actionID, $changes);
    }

    if(!dao::isError()) $this->loadModel('score')->create('ajax', 'batchCreate');

    return $mails;
}

/* 删除任务时，也需要删除所有底层子任务和任务工时消耗表记录。*/
public function deleteChildTask($taskID)
{
    $tasks = $this->dao->select('id')->from(TABLE_TASK)->where('parent')->in($taskID)->fetchAll();
    if(empty($tasks)) return false;

    $idList = array();
    $this->loadModel('effort');
    foreach($tasks as $task)
    {
        $idList[] = $task->id;
        $this->dao->update(TABLE_TASK)->set('deleted')->eq('1')->where('id')->eq($task->id)->exec();

        // 删除任务相应的工时消耗记录表。
        $this->effort->deleteRecord('task', $task->id);
    }
    $this->deleteChildTask($idList);
}
//创建计划一级阶段（二线工单管理 、二线研发管理）
public function createTopStage($taskID, $setImgSize = false)
{
    return $this->loadExtension('chengfangjinke')->createTopStage($taskID, $setImgSize);
}
/**
 * 获取关联表 关联项
 * @param $taskid
 */
public function getTaskDemandProblem($taskid){
    return $this->loadExtension('chengfangjinke')->getTaskDemandProblem($taskid);
}
/**二线工单管理 、二线研发管理 阶段任务自动创建程序入口
 *
 * 检查阶段和任务（二线工单管理 、二线研发管理）
 * @param $projectID 项目id
 * @param $app       应用系统
 * @param $source    来源（问题池 需求池 二线工单）
 * @param $data      数据
 */
public function checkStageAndTask($projectID, $app,$source,$data,$soureType)
{
    return $this->loadExtension('chengfangjinke')->checkStageAndTask($projectID, $app,$source,$data,$soureType);
}

public function newTask($project,$executionID = 0, $storyID = 0, $moduleID = 0, $taskID = 0, $todoID = 0, $auto = false){
    return $this->loadExtension('chengfangjinke')->newTask($project,$executionID , $storyID , $moduleID , $taskID , $todoID, $auto );
}
/**
 * 编辑任务
 * @param $assignto
 * @param $name
 * @param $stage
 * @param $taskfour
 * @param $id
 * @param $flag
 */
public function editTaskObject($assignto,$name,$stage,$taskfour,$id,$flag, $type, $data, $edit = false){
    return $this->loadExtension('chengfangjinke')->editTaskObject($assignto,$name,$stage,$taskfour,$id,$flag, $type, $data, $edit);
}
/**
 * 问题池、需求池、二线工单删除联动任务名称更新
 * @param $data
 * @param $code
 */
public function  deleteCodeUpdateTask($data,$code){
    return $this->loadExtension('chengfangjinke')->deleteCodeUpdateTask($data,$code);
}

/**
 * 项目管理、二线管理、部门管理 年度计划立项自动生成一二级阶段 、三级任务
 * @param $projectID
 * @return mixed
 */
public function approvalAutoCreateStageAndTask($projectID)
{
    return $this->loadExtension('chengfangjinke')->approvalAutoCreateStageAndTask($projectID);
}

/**
 * 分析生成任务
 * @param $projectID
 * @param $sourceType
 * @param $app
 * @param $code
 * @return mixed
 */
public function assignedAutoCreateStageTask($projectID,$sourceType,$app,$code,$data){
    return $this->loadExtension('chengfangjinke')->assignedAutoCreateStageTask($projectID,$sourceType,$app,$code,$data);
}

/**
 * 检查任务单号是否存在
 * @param $projectID
 * @param $app
 * @param $sourceType
 * @param $code
 * @param $data
 * @param null $del
 * @return mixed
 */
public function checkCodeExist($projectID, $app, $sourceType, $code, $data, $del = null){
    return $this->loadExtension('chengfangjinke')->checkCodeExist($projectID, $app, $sourceType, $code, $data, $del);
}

/**
 * 编辑任务
 * @param $assignto
 * @param $name
 * @param $stage
 * @param $taskfour
 * @param $id
 * @param $flag
 */
public function assignededitTaskObject($assignto,$name,$stage,$taskfour,$id,$flag, $type, $data){
    return $this->loadExtension('chengfangjinke')->assignededitTaskObject($assignto,$name,$stage,$taskfour,$id,$flag, $type, $data);
}

public function getTaskDemandProblemDesc($taskid)
{
    //查询任务-问题-需求 关联表
    $codes = $this->dao->select('typeid ,`type`')->from(TABLE_TASK_DEMAND_PROBLEM)->where('taskid')->eq($taskid)
        ->fetch();

    $desc = '';
    if($codes){
        $field = $this->lang->task->descName[$codes->type];
        $desc = $this->dao->select(" $field as summary")
            ->from($this->lang->task->tableName[$codes->type])
            ->where('id')->eq($codes->typeid)
            ->fetch();
    }

    return $desc ;
}

public function getTaskDemandProblemDescAll($taskid)
{
    //查询任务-问题-需求 关联表
    $codes = $this->dao->select('typeid ,`type`,taskid')->from(TABLE_TASK_DEMAND_PROBLEM)->where('taskid')->in($taskid)
        ->fetchAll();
    $descAll = array();
    if($codes){
        foreach ($codes as $code) {
            $field = $this->lang->task->descName[$code->type];
            $desc = $this->dao->select(" $field as summary")
                ->from($this->lang->task->tableName[$code->type])
                ->where('id')->eq($code->typeid)
                ->fetch();
            $descAll[$code->taskid] = $desc;
        }
    }
    return $descAll ;
}

/**
 * 编辑任务计划开始 计划结束
 * @param $taskID
 * @return mixed
 */
public function editTaskDate($taskID){
    return $this->loadExtension('chengfangjinke')->editTaskDate($taskID);
}