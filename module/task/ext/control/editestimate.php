<?php
include '../../control.php';
class myTask extends task
{
    public function editEstimate($effortID)
    {
        $this->loadModel('effort');
        $effort = $this->dao->findById((int)$effortID)->from(TABLE_EFFORT)->fetch();
        $taskID = $effort->objectID;

        if(!empty($_POST))
        {
            $consumedToday = $this->loadModel('effort')->getWorkloadToday($this->app->user->account, $this->post->consumed, 'edit', $effort->date, $effortID);

            $this->dao->update(TABLE_EFFORT)->set('consumed')->eq($this->post->consumed)
                ->set('date')->eq($this->post->date)
                ->set('work')->eq($this->post->work)
                ->where('id')->eq($effortID)
                ->exec();
            $this->loadModel('action')->create('effort', $effortID, 'edited', '');
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

            $this->task->computeTask($taskID);
            $this->task->computeConsumed($taskID);
            if(dao::isError()) die(js::error(dao::getError()));

            if(isonlybody()) echo js::closeModal('parent.parent', 'this');
            die(js::reload('parent'));
        }

        $this->view->title      = $this->lang->my->common . $this->lang->colon . $this->lang->effort->edit;
        $this->view->position[] = $this->lang->effort->edit;

        $this->view->effort     = $effort;
        $this->view->users      = $this->loadModel('user')->getPairs('noletter');
        $endDate                = $this->loadModel('review')->getCloseDate($effort->project);//查询评审关闭时间
        $this->view->beginAndEnd = $this->loadModel('task')->getBeginAndEnd($effort->project,isset($endDate->closeDate) ? $endDate->closeDate : '');//查询可报工时间
        $this->view->projectType = $this->dao->select('secondLine')->from(TABLE_PROJECTPLAN)->where('project')->eq($effort->project)->fetch();
        $this->display();
    }
}
