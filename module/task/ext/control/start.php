<?php
include '../../control.php';
class myTask extends task
{
    public function start($taskID)
    {
        $this->commonAction($taskID);

        $task = $this->task->getById($taskID);

        if(!empty($_POST))
        {
            $this->loadModel('action');
            $changes = $this->task->start2($taskID);
            if(dao::isError()) die(js::error(dao::getError()));

            if($this->post->comment != '' or !empty($changes))
            {    
                $act = $this->post->left == 0 ? 'Finished' : 'Started';
                $actionID = $this->action->create('task', $taskID, $act, $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }    

            $this->task->createEffort($taskID, 0, $task->left, '');

            /* Remind whether to update status of the bug, if task which from that bug has been finished. */
            if($changes and $this->task->needUpdateBugStatus($task))
            {    
                foreach($changes as $change)
                {    
                    if($change['field'] == 'status' and $change['new'] == 'done')
                    {    
                        $confirmURL = $this->createLink('bug', 'view', "id=$task->fromBug");
                        unset($_GET['onlybody']);
                        $cancelURL  = $this->createLink('task', 'view', "taskID=$taskID");
                        die(js::confirm(sprintf($this->lang->task->remindBug, $task->fromBug), $confirmURL, $cancelURL, 'parent', 'parent.parent'));
                    }    
                }    
            }    

            if(isonlybody()) die(js::closeModal('parent.parent', 'this'));
            die(js::locate($this->createLink('task', 'view', "taskID=$taskID"), 'parent'));
        }    

        $this->view->title      = $this->view->execution->name . $this->lang->colon .$this->lang->task->start;
        $this->view->position[] = $this->lang->task->start;

        $this->view->users      = $this->loadModel('user')->getPairs('noletter');
        $this->view->members    = $this->loadModel('user')->getTeamMemberPairs($task->project, 'project', 'nodeleted');
        $this->view->assignedTo = $task->assignedTo == '' ? $this->app->user->account : $task->assignedTo;
        $this->view->subStatus  = $task->status;
        $this->display();

    }
}
