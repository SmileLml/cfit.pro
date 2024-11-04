<?php
public function start($taskID)
{
    return $this->loadExtension('gantt')->start($taskID);
}

public function finish($taskID)
{
/* Update realDuration. */
    $finishedDate = $this->post->finishedDate;
    $taskInfo     = $this->dao->select('realStarted')->from(TABLE_TASK)->where('id')->eq($taskID)->fetch();
    $realStarted  = $taskInfo->realStarted;

    if(!empty($realStarted) and $realStarted != '0000-00-00 00:00:00')
    {
        $realDuration = $this->loadModel('holiday')->getActualWorkingDays($realStarted, $finishedDate);
        $realDuration = count($realDuration);

        $this->dao->update(TABLE_TASK)->set('realDuration')->eq($realDuration)->where('id')->eq($taskID)->exec();
    }

    return $this->loadExtension('gantt')->finish($taskID);
}

public function checkDepend($taskID, $action = 'begin')
{
    return $this->loadExtension('gantt')->checkDepend($taskID, $action);
}
