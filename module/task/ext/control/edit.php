<?php
include '../../control.php';
class myTask extends task
{
    public function edit($taskID, $comment = false)
    {
        $this->commonAction($taskID);

        if(!empty($_POST))
        {
            $this->loadModel('action');
            $changes = array();
            $files   = array();
            if($comment == false)
            {
                $changes = $this->task->update2($taskID);
                if(dao::isError()) die(js::error(dao::getError()));
                $files = $this->loadModel('file')->saveUpload('task', $taskID);
            }

            $task = $this->task->getById($taskID);
            if($this->post->comment != '' or !empty($changes) or !empty($files))
            {
                $action = (!empty($changes) or !empty($files)) ? 'Edited' : 'Commented';
                $fileAction = !empty($files) ? $this->lang->addFiles . join(',', $files) . "\n" : '';
                $actionID = $this->action->create('task', $taskID, $action, $fileAction . $this->post->comment);
                if(!empty($changes)) $this->action->logHistory($actionID, $changes);
            }

            $this->executeHooks($taskID);

            if($task->fromBug != 0)
            {
                foreach($changes as $change)
                {
                    if($change['field'] == 'status')
                    {
                        $confirmURL = $this->createLink('bug', 'view', "id=$task->fromBug");
                        $cancelURL  = $this->server->HTTP_REFERER;
                        die(js::confirm(sprintf($this->lang->task->remindBug, $task->fromBug), $confirmURL, $cancelURL, 'parent', 'parent'));
                    }
                }
            }
            if(defined('RUN_MODE') && RUN_MODE == 'api')
            {
                die(array('status' => 'success', 'data' => $taskID));
            }
            else
            {
                die(js::locate($this->createLink('task', 'view', "taskID=$taskID"), 'parent'));
            }
        }

        $tasks = $this->task->getParentTaskPairs($this->view->execution->id, $this->view->task->parent);
        if(isset($tasks[$taskID])) unset($tasks[$taskID]);

        if(!isset($this->view->members[$this->view->task->assignedTo])) $this->view->members[$this->view->task->assignedTo] = $this->view->task->assignedTo;
        if(isset($this->view->members['closed']) or $this->view->task->status == 'closed') $this->view->members['closed']  = 'Closed';

        //if($this->app->openApp == 'project') $this->project->setMenu($this->view->execution->project);
        //$this->execution->setMenu($this->view->task->execution);
        //a($this->view->task->execution);
        //a($this->lang->waterfall->menu->task['subMenu']);

        $this->view->title         = $this->lang->task->edit . 'TASK' . $this->lang->colon . $this->view->task->name;
        $this->view->position[]    = $this->lang->task->common;
        $this->view->position[]    = $this->lang->task->edit;
        $this->view->stories       = $this->story->getExecutionStoryPairs($this->view->task->project);
        $this->view->tasks         = $tasks;
        $this->view->users         = $this->loadModel('user')->getPairs('nodeleted', "{$this->view->task->openedBy},{$this->view->task->canceledBy},{$this->view->task->closedBy}");
        $this->view->showAllModule = isset($this->config->execution->task->allModule) ? $this->config->execution->task->allModule : '';
        $this->view->modules       = $this->tree->getTaskOptionMenu($this->view->task->execution, 0, 0, $this->view->showAllModule ? 'allModule' : '');
        $this->view->executions    = $this->project->getConditionStagePairsByProject($this->view->task->project); 
        $this->display();
    }
}
