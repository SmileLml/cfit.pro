<?php
include '../../control.php';
class myTask extends task
{
    public function editTask($taskID)
    {
        $this->commonAction($taskID);

        if(!empty($_POST))
        {

            $this->loadModel('action');
            $changes = $this->task->editTaskDate($taskID);
            if(dao::isError()) die(js::error(dao::getError()));

            if($changes)
            {
                $actionID = $this->action->create('task', $taskID, 'edited',  '');
                $this->action->logHistory($actionID, $changes);
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
        $this->view->users = $this->view->users   = $this->loadModel('user')->getPairs();
        $this->view->title      = $this->view->execution->name . $this->lang->colon .$this->lang->task->finish;
        $endDate                = $this->loadModel('review')->getCloseDate($task->project);//��ѯ����ر�ʱ��
        $this->view->beginAndEnd = $this->loadModel('task')->getBeginAndEnd($task->project, isset($endDate->closeDate) ? $endDate->closeDate : '' );//��ѯ�ɱ���ʱ��
        $this->view->projectType = $this->dao->select('secondLine')->from(TABLE_PROJECTPLAN)->where('project')->eq($task->project)->fetch();

        $this->display();
    }
}
