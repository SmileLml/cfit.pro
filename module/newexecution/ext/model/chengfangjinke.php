<?php
/**
 * Project: chengfangjinke
 * Method: update
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 13:29
 * Desc: This is the code comment. This method is called update.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param $executionID
 * @return mixed
 */
public function update($executionID)
{
    return $this->loadExtension('chengfangjinke')->update($executionID);
}

/**
 * Project: chengfangjinke
 * Method: edit
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 13:29
 * Desc: This is the code comment. This method is called edit.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param $executionID
 * @return array
 */
public function edit($executionID)
{
    $oldExe = $this->getByID($executionID);
    $oldExe->realBegan = $oldExe->realBegan == '0000-00-00' ? '' : $oldExe->realBegan;
    $oldExe->realEnd   = $oldExe->realEnd == '0000-00-00' ? '' : $oldExe->realEnd;

    $now    = helper::now();
    $execution = fixer::input('post')
        ->setDefault('lastEditedBy', $this->app->user->account)
        ->setDefault('lastEditedDate', $now)
        ->stripTags($this->config->execution->editor->edit['id'], $this->config->allowedTags)
        ->remove('comment,labels,uid')
        ->get();

    // 判断日期合理性。
    if(strtotime(substr($_POST['realBegan'], 0, 10)) > strtotime(substr($_POST['realEnd'], 0, 10)))
    {
        dao::$errors[] = $this->lang->execution->finishedDateReasonable;
        return false;
    }

    $changed = ($oldExe->name    != $execution->name    || $oldExe->code != $execution->code || $oldExe->realBegan != $execution->realBegan ||
        $oldExe->realEnd != $execution->realEnd || $oldExe->desc != $execution->desc);
    if($changed)  $execution->version = $oldExe->version + 1;

    /* Change the error notice lang. */
    $this->lang->project->code = $this->lang->execution->code;
    $this->lang->project->attribute = $this->lang->execution->attribute;
    $this->lang->project->desc = $this->lang->execution->desc;

    $execution = $this->loadModel('file')->processImgURL($execution, $this->config->execution->editor->edit['id'], $this->post->uid);

    $execution->realDuration = helper::diffDate3($execution->realEnd, $execution->realBegan);
    $this->dao->update(TABLE_EXECUTION)->data($execution)->autoCheck()
        ->batchCheck($this->config->execution->edit->requiredFields, 'notempty')
        ->checkIF($execution->end != '', 'end', 'gt', $execution->begin)
        ->where('id')->eq($executionID)
        ->exec();

    if($changed)
    {
        $spec               = new stdclass();
        $spec->execution    = $executionID;
        $spec->name         = $execution->name;
        $spec->code         = $execution->code;
        $spec->realBegan    = $execution->realBegan;
        $spec->realEnd      = $execution->realEnd;
        $spec->realEnd      = $execution->realEnd;
        $spec->desc         = $execution->desc;
        $spec->begin        = $oldExe->begin;//此处未编辑该字段
        $spec->end          = $oldExe->end;//此处未编辑该字段
        $spec->planDuration = $oldExe->planDuration;//此处未编辑该字段
        $spec->version      = $execution->version;
        $this->dao->insert(TABLE_EXECUTIONSPEC)->data($spec)->exec();
    }

    $this->file->updateObjectID($this->post->uid, $executionID, 'execution');
    return common::createChanges($oldExe, $execution);
}
/**
 * Project: chengfangjinke
 * Method: getByIDAndVersion
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 13:29
 * Desc: This is the code comment. This method is called update.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param $executionID
 * @return mixed
 */
public function getByIDAndVersion($executionID, $setImgSize = false,  $version = 0)
{
    return $this->loadExtension('chengfangjinke')->getByIDAndVersion($executionID, $setImgSize, $version);
}

public function getTaskStatisticsByExecution($executionID)
{
    // 判断是否是父阶段，如果是则查询该阶段下所有子阶段进行数据统计。
    $execution = $this->dao->select('id,parent')->from(TABLE_EXECUTION)->where('id')->eq($executionID)->fetch();
    if(!$execution->parent)
    {
        $childrens = $this->dao->select('id,parent')->from(TABLE_EXECUTION)->where('`type`')->eq('stage')->andWhere('parent')->eq($execution->id)->fetchPairs();

        $data = array('total' => 0, 'normal' => 0, 'delayStart' => 0, 'delayFinish' => 0, 'overflow' => 0, 'noOverflow' => 0);
        foreach($childrens as $id => $children)
        {
            $result              = $this->getTaskStatistics($id);
            $data['total']       += $result['total'];
            $data['normal']      += $result['normal'];
            $data['delayStart']  += $result['delayStart'];
            $data['delayFinish'] += $result['delayFinish'];
            $data['overflow']    += $result['overflow'];
            $data['noOverflow']  += $result['noOverflow'];
        }
        return $data;
    }
    else
    {
        return $this->getTaskStatistics($executionID);
    }
}

public function getTaskStatistics($executionID)
{
    $tasks = $this->getExecutionTaskByID($executionID);

    $total       = count($tasks);
    $normal      = 0;
    $delayStart  = 0;
    $delayFinish = 0;
    $overflow    = 0;
    $noOverflow  = 0;
    foreach($tasks as $task)
    {
        // 实际完成时间在预计开始和结束时间。
        if($task->estStarted <= $task->finishedDate and $task->finishedDate <= $task->deadline) $normal ++;

        // 计算延期开始。
        if($task->estStarted < $task->realStarted) $delayStart ++;

        // 计算延期完成。
        if($task->deadline < $task->finishedDate) $delayFinish ++;

        // 实际工作量大于计划工作量。
        if($task->estimate < $task->consumed) $overflow ++;

        // 实际工作量小于计划工作量。
        if($task->consumed < $task->estimate) $noOverflow ++;
    }
    return array('total' => $total, 'normal' => $normal, 'delayStart' => $delayStart, 'delayFinish' => $delayFinish, 'overflow' => $overflow, 'noOverflow' => $noOverflow);
}

public function getExecutionTaskByID($executionID, $browseType = 'all', $accounts = array())
{
    $tasks = $this->dao->select('id, parent')->from(TABLE_TASK)->where('execution')->eq($executionID)->andWhere('deleted')->eq('0')->fetchPairs();
    foreach($tasks as $taskID => $parentID) unset($tasks[$parentID]);
    $taskIDList = array_keys($tasks);

    $today = date('Y-m-d');
    $list = $this->dao->select('*')->from(TABLE_TASK)->where('id')->in($taskIDList)
        ->beginIF($browseType == 'delay')->andWhere('deadline')->lt($today)->andWhere('status')->in(array('doing', 'wait'))->fi()
        ->beginIF($browseType == 'assign')->andWhere('assignedTo')->in($accounts)->andWhere('status')->ne('cancel')->fi()
        ->fetchAll();
    return $list;
}


public function getExecutionTaskCount($executionID)
{
    $tasks = $this->dao->select('id, parent')->from(TABLE_TASK)->where('execution')->eq($executionID)->andWhere('deleted')->eq('0')->fetchPairs();
    foreach($tasks as $taskID => $parentID) unset($tasks[$parentID]);
    return count($tasks);
}

public function getParticipantsByExecution($executionID)
{
    // 判断是否是父阶段，如果是则查询该阶段下所有子阶段进行数据统计。
    $execution = $this->dao->select('id,parent')->from(TABLE_EXECUTION)->where('id')->eq($executionID)->fetch();
    if(!$execution->parent)
    {
        $childrens = $this->dao->select('id,parent')->from(TABLE_EXECUTION)->where('`type`')->eq('stage')->andWhere('parent')->eq($execution->id)->fetchPairs();

        // 如果是父阶段则把每个子阶段下的用户按组分配到一起。
        $userData = array();
        foreach($childrens as $id => $children)
        {
            $result = $this->getParticipantsByExecutionID($id);
            foreach($result as $users)
            {
                foreach($users as $user)
                {
                    $userData[$user->account][] = $user;
                }
            }
        }

        $data = array();
        foreach($userData as $account => $users)
        {
            foreach($users as $index => $user)
            {
                if($index == 0)
                {
                    $data[$account] = $user;
                }
                else
                {
                    $data[$account]->total             += $user->total;
                    $data[$account]->planDuration      += $user->planDuration;
                    $data[$account]->realDuration      += $user->realDuration;
                    $data[$account]->durationDeviation += $user->durationDeviation;
                    $data[$account]->estimate          += $user->estimate;
                    $data[$account]->consumed          += $user->consumed;
                    $data[$account]->workloadDeviation += $user->workloadDeviation;
                }
            }
        }

        $deptGroup = array();
        foreach($data as $user)
        {
            $deptGroup[$user->deptID][] = $user;
        }

        return $deptGroup;
    }
    else
    {
        return $this->getParticipantsByExecutionID($executionID);
    }
}

public function getParticipantsByExecutionID($executionID)
{
    // 先查询出有工时消耗的人。
    $userWorkload = $this->dao->select('account, cast(sum(consumed) as decimal(11,2)) as total')->from(TABLE_EFFORT)->where('objectType')->eq('task')
        ->andWhere('execution')->eq($executionID)
        ->andWhere('deleted')->eq('0')
        ->groupBy('account')
        ->fetchAll('account');
    if(empty($userWorkload)) return array();

    // 查询出指派给这个用户身上的任务。
    $accounts = array();
    foreach($userWorkload as $workload)
    {
        $accounts[]                  = $workload->account;
        $workload->planDuration      = 0;
        $workload->realDuration      = 0;
        $workload->durationDeviation = 0;
        $workload->estimate          = 0;
        $workload->consumed          = 0;
        $workload->workloadDeviation = 0;
    }

    $userDeptList = $this->dao->select('account,realname,dept')->from(TABLE_USER)->where('account')->in($accounts)->fetchAll('account');

    $tasks = $this->getExecutionTaskByID($executionID, 'assign', $accounts);
    foreach($tasks as $task)
    {
        $userWorkload[$task->assignedTo]->planDuration += $task->planDuration;
        $userWorkload[$task->assignedTo]->realDuration += $task->realDuration;

        $userWorkload[$task->assignedTo]->durationDeviation = 0;

        $userWorkload[$task->assignedTo]->estimate += $task->estimate;
        $userWorkload[$task->assignedTo]->consumed += $task->consumed;

        $userWorkload[$task->assignedTo]->workloadDeviation = 0;
    }

    // 为用户按部门分组。
    $workloadGroup = array();
    foreach($userWorkload as $workload)
    {
        $workload->realname = $userDeptList[$workload->account]->realname;
        $workload->deptID   = $userDeptList[$workload->account]->dept;

        $workload->estimate = number_format($workload->estimate, 2);
        $workload->consumed = number_format($workload->consumed, 2);

        $workload->durationDeviation = $workload->realDuration - $workload->planDuration;
        $workload->workloadDeviation = $workload->total - $workload->estimate;
        $workload->durationDeviation = number_format($workload->workloadDeviation, 2);
        $workload->workloadDeviation = number_format($workload->durationDeviation, 2);

        $workloadGroup[$workload->deptID][] = $workload;
    }

    return $workloadGroup;
}

public function deleteTasks($executionID, $taskID)
{
    $task = $this->loadModel('task')->getById($taskID);

    $this->task->delete(TABLE_TASK, $taskID);

    if($task->parent > 0)   $this->loadModel('action')->create('task', $task->parent, 'deleteChildrenTask', '', $taskID);
    if($task->fromBug != 0) $this->dao->update(TABLE_BUG)->set('toTask')->eq(0)->where('id')->eq($task->fromBug)->exec();
    if($task->story)        $this->loadModel('story')->setStage($task->story);

    // 删除任务相应的工时消耗记录表。
    $this->loadModel('effort')->deleteRecord('task', $taskID);

    // 删除所有底层子任务和任务工时消耗表记录。
    $this->task->deleteChildTask($taskID);

    $this->task->computeConsumed($taskID);
}
