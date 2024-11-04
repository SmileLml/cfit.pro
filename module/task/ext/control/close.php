<?php
include '../../control.php';
class myTask extends task
{
    public function close($taskID)
    {
        $this->commonAction($taskID);

        if(!empty($_POST))
        {
            $this->loadModel('action');
            $changes = $this->task->close2($taskID);

            if(dao::isError()) die(js::error(dao::getError()));

            if($this->post->comment != '' or !empty($changes))
            {
                $actionID = $this->action->create('task', $taskID, 'Closed', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }

            // $this->createEffort($taskID, $consumed, $left);

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

        $this->view->title      = $this->view->execution->name . $this->lang->colon .$this->lang->task->finish;
        $this->view->position[] = $this->lang->task->finish;
        $this->view->users      = $this->loadModel('user')->getPairs('noletter');
        $this->view->subStatus  = '';

        $this->display();
    }
}
