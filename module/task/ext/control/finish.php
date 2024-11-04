<?php
include '../../control.php';
class myTask extends task
{
    public function finish($taskID,$source = null)
    {
        $this->commonAction($taskID);

        if(!empty($_POST))
        {
            if(empty(substr($this->post->finishedDate, 0, 11))) die(js::alert('实际完成日期不能为空'));
            //$consumedToday = $this->loadModel('effort')->getWorkloadToday($this->app->user->account, $this->post->currentConsumed, 'insert', substr($this->post->finishedDate, 0, 11));

            $this->loadModel('action');
            $changes = $this->task->finish2($taskID);
            if(dao::isError()) die(js::error(dao::getError()));
            $files = $this->loadModel('file')->saveUpload('task', $taskID);
            $task = $this->task->getById($taskID);

            if($this->post->comment != '' or !empty($changes))
            {
                $fileAction = !empty($files) ? $this->lang->addFiles . join(',', $files) . "\n" : '';
                $actionID = $this->action->create('task', $taskID, 'Finished', $fileAction . $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }

            $this->task->createEffort($taskID, $this->post->currentConsumed, 0, substr($this->post->finishedDate, 0, 11));

            if($this->task->needUpdateBugStatus($task))
            {
                foreach($changes as $change)
                {
                    if($change['field'] == 'status')
                    {
                        $confirmURL = $this->createLink('bug', 'view', "id=$task->fromBug", '', true);
                        unset($_GET['onlybody']);
                        $cancelURL  = $this->createLink('task', 'view', "taskID=$taskID");
                        die(js::confirm(sprintf($this->lang->task->remindBug, $task->fromBug), $confirmURL, $cancelURL, 'parent', 'parent.parent'));
                    }
                }
            }
            if(isonlybody()) die(js::closeModal('parent.parent', 'this'));
            if(defined('RUN_MODE') && RUN_MODE == 'api')
            {
                die(array('status' => 'success', 'data' => $taskID));
            }
            else
            {
                die(js::locate($this->createLink('task', 'view', "taskID=$taskID"), 'parent'));
            }
        }

        $task         = $this->view->task;
        $members      = $this->loadModel('user')->getTeamMemberPairs($task->project, 'project', 'nodeleted');
        $task->nextBy = $task->openedBy;

        $this->view->users = $members;
        if(!empty($task->team))
        {
            $teams = array_keys($task->team);

            $task->nextBy     = $this->task->getNextUser($teams, $task->assignedTo);
            $task->myConsumed = $task->team[$task->assignedTo]->consumed;

            $lastAccount = end($teams);
            if($lastAccount != $task->assignedTo)
            {
                $members = $this->task->getMemberPairs($task);
            }
            else
            {
                $task->nextBy = $task->openedBy;
            }
        }

        $this->view->title      = $this->view->execution->name . $this->lang->colon .$this->lang->task->finish;
        $this->view->position[] = $this->lang->task->finish;
        $this->view->members    = $members;
        $this->view->subStatus  = $task->status;
        $endDate                = $this->loadModel('review')->getCloseDate($task->project);//查询评审关闭时间
        $this->view->beginAndEnd = $this->loadModel('task')->getBeginAndEnd($task->project, isset($endDate->closeDate) ? $endDate->closeDate : '' );//查询可报工时间
        $this->view->projectType = $this->dao->select('secondLine')->from(TABLE_PROJECTPLAN)->where('project')->eq($task->project)->fetch();
        $this->view->source      = $source;
        $this->display();
    }
}
