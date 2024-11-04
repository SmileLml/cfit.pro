<?php
/**
 * Project: chengfangjinke
 * Method: getWorkloadToday
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 13:27
 * Desc: This is the code comment. This method is called getWorkloadToday.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param $account
 * @param int $hours
 * @param string $action
 * @param string $date
 * @param int $effortID
 * @return float|int|mixed|void
 */
public function getWorkloadToday($account, $hours = 0, $action = 'insert', $date = '', $effortID = 0)
{
    if(empty($hours)) $hours = 0;
    $hours = round($hours,2);
    $threshold = $this->config->task->workThreshold;
    if($action == 'edit')
    {
        $editConsumed = $this->dao->select('consumed')->from(TABLE_EFFORT)->where('id')->eq($effortID)->fetch('consumed');
        $consumed = $this->dao->select('sum(consumed) as consumed')->from(TABLE_EFFORT)
            ->where('account')->eq($account)
//    取zt_effort 中任何所有objectType的累计，即所有类型的工作量单人一天累计。
//            ->andWhere('objectType')->eq('task')
            ->andWhere('date')->eq($date)
            ->andWhere('deleted')->eq('0')
            ->fetch('consumed');

        if(empty($consumed)) $consumed = 0;
        if(empty($editConsumed)) $editConsumed = 0;
        $availableHours = ($consumed - $editConsumed) + $hours;
        $overWorkload   = sprintf($this->lang->effort->overWorkload, $date, $threshold);
        if($availableHours > $threshold) die(js::alert($overWorkload));
    }
    else
    {
        if(empty($date)) $date = date('Y-m-d');
        $consumed = $this->dao->select('sum(consumed) as consumed')->from(TABLE_EFFORT)
            ->where('account')->eq($account)
//            ->andWhere('objectType')->eq('task')
            ->andWhere('date')->eq($date)
            ->andWhere('deleted')->eq('0')
            ->fetch('consumed');

        if(empty($consumed)) $consumed = 0;
        $consumed = round($consumed,2);
        $overWorkload = sprintf($this->lang->effort->overWorkload, $date, $threshold);

        if($consumed > $threshold) die(js::alert($overWorkload));

        $availableHours = round($threshold - round($consumed, 1),2);
        if($availableHours == 0) die(js::alert($overWorkload));

        $zeroWorkload = sprintf($this->lang->effort->zeroWorkload, $date, $availableHours, $availableHours);
        if($availableHours < 0 or $availableHours < $hours) die(js::alert($zeroWorkload));
    }

    return $availableHours;
}

public function deleteRecord($objectType = '', $objectID = 0)
{
    if(empty($objectType) or empty($objectID)) return false;
    $this->dao->update(TABLE_EFFORT)->set('deleted')->eq('1')->where('objectType')->eq($objectType)->andWhere('objectID')->eq($objectID)->exec();
}
/**
 * Get actions.
 *
 * @param  int    $date
 * @param  int    $account
 * @param  string $objectType
 * @param  int    $objectID
 * @access public
 * @return array
 */
public function getActions($date, $account, $objectType = '', $objectID = '')
{
    $projects = $this->dao->select('id,status')->from(TABLE_PROJECT)->where('type')->eq('project')->fetchPairs('id', 'status');
    /* Get all actions. */
    $date = is_numeric($date) ? substr($date, 0, 4) . '-' . substr($date, 4, 2) . '-' . substr($date, 6, 2) : $date;
    $dateLength = strlen($date);
    $allActions = $this->dao->select('*')->from(TABLE_ACTION)
        ->where('actor')->eq($account)
        ->andWhere("(LEFT(`date`, $dateLength) = '$date')")
        ->beginIF(!empty($objectType))->andWhere('objectType')->eq($objectType)->fi()
        ->beginIF(!empty($objectID))->andWhere('objectID')->eq($objectID)->fi()
        ->andWhere('efforted')->eq(0)
        ->orderBy('id_desc')
        ->limit(30)
        ->fetchAll('id');

    /* Init vars. */
    $taskIdList = array();
    foreach($allActions as $id => $action)
    {
        if($action->objectType == 'task')
        {
            $task = $this->dao->select('deleted,project')->from(TABLE_TASK)->where('`id`')->eq($action->objectID)->fetch();
            if(empty($task) or $task->deleted == 1 or $projects[$task->project] == 'closed')
            {
                unset($allActions[$id]);
                continue;
            }

            $taskIdList[$action->objectID] = $action->objectID;
        }
    }
    $teams       = $this->dao->select('id,root,type,account')->from(TABLE_TEAM)->where('root')->in($taskIdList)->andWhere('type')->eq('task')->fetchGroup('root', 'id');
    $parentTasks = $this->dao->select('id,name')->from(TABLE_TASK)->where('`id`')->in($taskIdList)->andWhere('parent')->eq(-1)->fetchGroup('id', 'name');

    $actions     = array();
    $executions  = array();
    $beforeID    = 0;
    $dealActions = array();

    foreach($allActions as $id => $action)
    {
        /* Remove started or finished or multiple or parent task. */
        if($action->objectType == 'task' and ($action->action == 'started' or $action->action == 'finished')) continue;
        if($action->objectType == 'task' and isset($teams[$action->objectID])) continue;
        if($action->objectType == 'task' and isset($parentTasks[$action->objectID])) continue;

        if(isset($dealActions[$action->objectType][$action->objectID])) continue;

        if(isset($this->lang->effort->objectTypeList[$action->objectType]))
        {
            $work = $this->getWork($action->objectType, $action->objectID);

            $key      = $action->objectType . '_' . $action->objectID;
            $objectID = $action->objectID;
            if(!isset($work[$objectID])) continue;
            $typeList[$key] = '[' . zget($this->lang->effort->objectTypeList, $action->objectType, $action->objectType) . ']' . $objectID . ':' . $work[$objectID];
            $action->work   = $this->lang->effort->deal . $this->lang->effort->objectTypeList[$action->objectType] . ' : ' . $work[$objectID];

            $beforeID = $id;
            unset($action->product);

            $actions[$id] = $action;
            $executions[$action->execution] = $action->execution;
            if($action->objectType == 'task') $executionTask[$key] = $action->execution; // Fix bug #1581.
            $dealActions[$action->objectType][$action->objectID] = true;
        }
    }

    $stories = $this->dao->select('id,title')->from(TABLE_STORY)->where('assignedTo')->eq($this->app->user->account)->andWhere('deleted')->eq('0')->fetchAll();
    foreach($stories as $story)
    {
        $key = 'story_' . $story->id;
        $typeList[$key] = "[{$this->lang->effort->objectTypeList['story']}]" . $story->id . ':' . $story->title;
    }

    /* Get tasks and remove multiple or parent tasks. */
    $tasks = $this->dao->select('id,execution,name,parent,project')->from(TABLE_TASK)->where('assignedTo')->eq($this->app->user->account)->andWhere('deleted')->eq('0')->fetchAll();
    foreach($tasks as $task)
    {
        if(isset($teams[$task->id])) continue;
        if($task->parent < 0) continue;
        if($projects[$task->project] == 'closed') continue; //项目关闭的去掉

        $key = 'task_' . $task->id;
        $typeList[$key]               = "[{$this->lang->effort->objectTypeList['task']}]" . $task->id . ':' . $task->name;
        $executionTask[$key]          = $task->execution;
        $executions[$task->execution] = $task->execution;
    }

    $bugs = $this->dao->select('id,title')->from(TABLE_BUG)->where('assignedTo')->eq($this->app->user->account)->andWhere('deleted')->eq(0)->fetchAll();
    foreach($bugs as $bug)
    {
        $key = 'bug_' . $bug->id;
        $typeList[$key] = "[{$this->lang->effort->objectTypeList['bug']}]" . $bug->id . ':' . $bug->title;
    }

    $actions['typeList'] = isset($typeList) ? $typeList : array();
    $executions = $this->loadModel('execution')->getByIdList($executions);
    foreach($executions as $execution) $actions['executions'][$execution->id] = $execution->name;

    if(isset($executionTask)) $actions['executionTask'] = $executionTask;
    return $actions;
}

public function getRealConsumedByTaskID($taskID = 0, $account = '')
{
    $consumed = $this->dao->select('sum(consumed) as consumed')->from(TABLE_EFFORT)
        ->where('objectType')->eq('task')
        ->andWhere('objectID')->eq($taskID)
        ->beginIF(!empty($account))->andWhere('account')->eq($account)->fi()
        ->andWhere('deleted')->eq('0')
        ->fetch('consumed');
    return empty($consumed) ? 0 : $consumed;
}

public function getRealConsumedAndLeftByTaskID($taskID = 0, $account = '', $objectType='task')
{
    $realData = $this->dao->select('sum(consumed) as consumed, sum(`left`) as `left`')->from(TABLE_EFFORT)
        ->where('objectType')->eq($objectType)
        ->andWhere('objectID')->eq($taskID)
        ->beginIF(!empty($account))->andWhere('account')->eq($account)->fi()
        ->andWhere('deleted')->eq('0')
        ->fetch();

    $realData->consumed = empty($realData->consumed) ? 0 : $realData->consumed;
    $realData->left     = empty($realData->left) ? 0 : $realData->left;
    return $realData;
}
