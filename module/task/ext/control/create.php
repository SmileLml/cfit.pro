<?php
include '../../control.php';
class myTask extends task
{
    public function create($executionID = 0, $storyID = 0, $moduleID = 0, $taskID = 0, $todoID = 0)
    {
        $executions  = $this->project->getConditionStagePairsByProject($this->session->project);
        $executionID = $this->execution->saveState($executionID, $executions);
        $execution   = $this->execution->getById($executionID);
        $this->loadModel('project')->setMenu($execution->project);

        $this->execution->getLimitedExecution();
        $limitedExecutions = !empty($_SESSION['limitedExecutions']) ? $_SESSION['limitedExecutions'] : '';
        if(strpos(",{$limitedExecutions},", ",$executionID,") !== false)
        {
            echo js::alert($this->lang->task->createDenied);
            die(js::locate($this->createLink('project', 'execution', "projectID=$execution->project")));
        }

        $task = new stdClass();
        $task->module     = $moduleID;
        $task->assignedTo = '';
        $task->name       = '';
        $task->story      = $storyID;
        $task->type       = '';
        $task->pri        = '3';
        $task->estimate   = '';
        $task->desc       = '';
        $task->estStarted = '';
        $task->deadline   = '';
        $task->mailto     = '';
        $task->color      = '';
        if($taskID > 0)
        {
            $task        = $this->task->getByID($taskID);
            $executionID = $task->execution;
        }

        if($todoID > 0)
        {
            $todo = $this->loadModel('todo')->getById($todoID);
            $task->name = $todo->name;
            $task->pri  = $todo->pri;
            $task->desc = $todo->desc;
        }

        $execution = $this->execution->getById($executionID);
        $taskLink  = $this->createLink('execution', 'browse', "executionID=$executionID&tab=task");
       // $storyLink = $this->session->storyList ? $this->session->storyList : $this->createLink('execution', 'story', "executionID=$executionID");
        $storyLink =  $this->createLink('projectstory', 'story', "projectID=$execution->project"); //20220705 修复跳转页面出错问题

        /* Set menu. */
        $this->execution->setMenu($execution->id);

        if(!empty($_POST))
        {
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;

            setcookie('lastTaskModule', (int)$this->post->module, $this->config->cookieLife, $this->config->webRoot, '', $this->config->cookieSecure, false);
            if($this->post->execution) $executionID = (int)$this->post->execution;
            $tasksID = $this->task->create2($executionID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            foreach($tasksID as $t) $this->task->computeConsumed($t['id']);

            /* if the count of tasksID is 1 then check exists. */
            if(count($tasksID) == 1)
            {
                $taskID = current($tasksID);
                if($taskID['status'] == 'exists')
                {
                    $response['locate']  = $this->createLink('task', 'view', "taskID={$taskID['id']}");
                    $response['message'] = sprintf($this->lang->duplicate, $this->lang->task->common);
                    $this->send($response);
                }
            }

            /* Create actions. */
            $this->loadModel('action');
            foreach($tasksID as $taskID)
            {
                /* if status is exists then this task has exists not new create. */
                if($taskID['status'] == 'exists') continue;

                $taskID   = $taskID['id'];
                $this->action->create('task', $taskID, 'Opened', '');
            }

            if($todoID > 0)
            {
                $this->dao->update(TABLE_TODO)->set('status')->eq('done')->where('id')->eq($todoID)->exec();
                $this->action->create('todo', $todoID, 'finished', '', "TASK:$taskID");
            }

            $this->executeHooks($taskID);

            /* If link from no head then reload. */
            if(isonlybody()) $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'parent'));

            /* Locate the browser. */
            if($this->app->getViewType() == 'xhtml')
            {
                $taskLink  = $this->createLink('task', 'view', "taskID=$taskID");
                $response['locate'] = $taskLink;
                $this->send($response);
            }

            if($this->post->after == 'continueAdding')
            {
                $response['message'] = $this->lang->task->successSaved . $this->lang->task->afterChoices['continueAdding'];
                $response['locate']  = $this->createLink('task', 'create', "executionID=$executionID&storyID={$this->post->story}&moduleID=$moduleID");
                $this->send($response);
            }
            elseif($this->post->after == 'toTaskList')
            {
                setcookie('moduleBrowseParam',  0, 0, $this->config->webRoot, '', $this->config->cookieSecure, false);
                //$taskLink  = $this->createLink('project', 'execution', "browseType=all&projectID=$execution->project");
                $taskLink  = $this->createLink('execution', 'task', "executionID=$executionID&status=unclosed&param=0&orderBy=id_desc");
                $response['locate'] = $taskLink;
                $this->send($response);
            }
            elseif($this->post->after == 'toStoryList')
            {
                $response['locate'] = $storyLink;
                $this->send($response);
            }
            else
            {
                $response['locate'] = $taskLink;
                $this->send($response);
            }
        }

        $users            = $this->loadModel('user')->getPairs('noclosed|nodeleted');

        // 换成获取项目团队成员。
        //$members          = $this->loadModel('user')->getTeamMemberPairs($executionID, 'execution', 'nodeleted');
        $members          = $this->loadModel('user')->getTeamMemberPairs($execution->project, 'project', 'nodeleted');

        $showAllModule    = isset($this->config->execution->task->allModule) ? $this->config->execution->task->allModule : '';
        $moduleOptionMenu = $this->tree->getTaskOptionMenu($executionID, 0, 0, $showAllModule ? 'allModule' : '');

        /* Fix bug #3381. When the story module is the root module. */
        if($storyID)
        {
            $task->module = $this->dao->findByID($storyID)->from(TABLE_STORY)->fetch('module');
        }
        else
        {
            $task->module = $task->module ? $task->module : (int)$this->cookie->lastTaskModule;
        }

        /* Fix bug #2737. When moduleID is not story module. */
        $moduleIdList = array();
        if($task->module)
        {
            $moduleID     = $this->tree->getStoryModule($task->module);
            $moduleIdList = $this->tree->getAllChildID($moduleID);
        }

        // 从项目获取需求。
        //$stories = $this->story->getExecutionStoryPairs($executionID, 0, 0, $moduleIdList, 'full', 'unclosed');
        $stories = $this->story->getProjectStoryPairs($execution->project, 0, 0, $moduleIdList, 'full', 'unclosed');

        /* Get block id of assinge to me. */
        $blockID = 0;
        if(isonlybody())
        {
            $blockID = $this->dao->select('id')->from(TABLE_BLOCK)
                ->where('block')->eq('assingtome')
                ->andWhere('module')->eq('my')
                ->andWhere('account')->eq($this->app->user->account)
                ->orderBy('order_desc')
                ->fetch('id');
        }

        $title      = $execution->name . $this->lang->colon . $this->lang->task->create;
        $position[] = html::a($taskLink, $execution->name);
        $position[] = $this->lang->task->common;
        $position[] = $this->lang->task->create;

        /* Set Custom*/
        foreach(explode(',', $this->config->task->customCreateFields) as $field) $customFields[$field] = $this->lang->task->$field;
        if($execution->type == 'ops') unset($customFields['story']);

        $this->view->customFields  = $customFields;
        $this->view->showFields    = $this->config->task->custom->createFields;
        $this->view->showAllModule = $showAllModule;

        $this->view->title            = $title;
        $this->view->position         = $position;
        $this->view->execution        = $execution;
        $this->view->executions       = $executions;
        $this->view->task             = $task;
        $this->view->users            = $users;
        $this->view->stories          = $stories;
        $this->view->testStoryIdList  = $this->loadModel('story')->getTestStories(array_keys($stories), $execution->id);
        $this->view->members          = $members;
        $this->view->blockID          = $blockID;
        $this->view->moduleOptionMenu = $moduleOptionMenu;
        $this->display();
    }
}
