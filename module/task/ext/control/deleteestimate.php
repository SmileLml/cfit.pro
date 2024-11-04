<?php
include '../../control.php';
class myTask extends task
{
    public function deleteEstimate($effortID, $confirm = 'no')
    {
        $this->loadModel('effort');
        if($confirm == 'no')
        {
            die(js::confirm($this->lang->effort->confirmDelete, $this->createLink('effort', 'delete', "effortID=$effortID&confirm=yes")));
        }
        else
        {
            $effort = $this->effort->getByID($effortID);
            $this->effort->delete(TABLE_EFFORT, $effortID);

            // 判断是否为多人任务，如果是多人任务，则算一下团队参与人员工时信息。
            $task = $this->task->getByID($taskID);
            if(!empty($task->team))
            {
                $realData = $this->loadModel('effort')->getRealConsumedAndLeftByTaskID($taskID, $effort->account);
                if(isset($task->team[$effort->account]))
                {
                    $teamUserLeft     = $realData->left;
                    $teamUserConsumed = $realData->consumed;
                    $this->dao->update(TABLE_TEAM)->set('left')->eq($teamUserLeft)->set('consumed')->eq($teamUserConsumed)
                         ->where('root')->eq((int)$taskID)
                         ->andWhere('type')->eq('task')
                         ->andWhere('account')->eq($effort->account)->exec();
                }
            }

            $this->task->computeTask($effort->objectID);
            $this->task->computeConsumed($effort->objectID);

            die(js::reload('parent'));
        }
    }
}
