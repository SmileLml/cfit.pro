<?php
include '../../control.php';
class myTask extends task
{
    public function view($taskID, $version = '',$flag = 0)
    {
        $version = $version == 'workWaitList' ? ''  : $version;
        $version ? $taskSpec  = $this->task->getTaskSpec($taskID, $version) : $taskSpec = '';
        $this->view->taskSpec = $taskSpec;
        $this->view->version  = $version;
        $this->view->designs  = $this->dao->select('id, name')->from(TABLE_DESIGN)->where('deleted')->eq(0)->fetchPairs();

        $taskID = (int)$taskID;
        $task   = $this->task->getById($taskID, true);
        if(!$task) die(js::error($this->lang->notFound) . js::locate('back'));
        $this->session->project = $task->project;

        if($task->fromBug != 0)
        {
            $bug = $this->loadModel('bug')->getById($task->fromBug);
            $task->bugSteps = '';
            if($bug)
            {
                $task->bugSteps = $this->loadModel('file')->setImgSize($bug->steps);
                foreach($bug->files as $file) $task->files[] = $file;
            }
            $this->view->fromBug = $bug;
        }
        else
        {
            $story = $this->story->getById($task->story, $task->storyVersion);
            $task->storySpec   = empty($story) ? '' : $this->loadModel('file')->setImgSize($story->spec);
            $task->storyVerify = empty($story) ? '' : $this->loadModel('file')->setImgSize($story->verify);
            $task->storyFiles  = $this->loadModel('file')->getByObject('story', $task->story);
        }

        if($task->team) $this->lang->task->assign = $this->lang->task->transfer;

        /* Update action. */
        if($task->assignedTo == $this->app->user->account) $this->loadModel('action')->read('task', $taskID);

        /* Set menu. */
        $execution = $this->execution->getById($task->execution);
        $this->execution->setMenu($execution->id);
        //if($this->app->openApp == 'project') $this->project->setMenu($execution->project);

        // 判断当前用户是否为项目团队成员。
        $this->view->canRecordEstimate = $this->project->isCanRecordEstimate($task->project);

        $this->executeHooks($taskID);

        $this->project->setMenu($task->project);//20220627 新增解决地盘点任务详情显示项目不匹配问题

        $title      = "TASK#$task->id $task->name / $execution->name";
        $position[] = html::a($this->createLink('execution', 'browse', "executionID=$task->execution"), $execution->name);
        $position[] = $this->lang->task->common;
        $position[] = $this->lang->task->view;

        $this->loadModel('effort');
        $efforts = $this->effort->getByObject('task', $taskID); //历史任务工作量
        if(isset($efforts['typeList'])) $this->view->typeList = $efforts['typeList'];
        unset($efforts['typeList']);
        $this->view->efforts    = $efforts;
       // $this->app->loadLang($lang);
        $this->app->loadLang('problem');
        $this->app->loadLang('demand');
        $this->app->loadLang('secondorder');
        $this->app->loadLang('deptorder');

        $this->view->demandOrProOrSecond = $this->task->getTaskDemandProblem($taskID); // 关联的问题单、需求单、二线工单

        $this->view->title      = $title;
        $this->view->position   = $position;
        $this->view->execution  = $execution;
        $this->view->task       = $task;
        $this->view->project    = $this->loadModel('project')->getById($task->project);
        $this->view->actions    = $this->loadModel('action')->getList('task', $taskID);
        $this->view->users      = $this->loadModel('user')->getPairs('noletter');
        $this->view->preAndNext = $this->loadModel('common')->getPreAndNextObject('task', $taskID);
        $this->view->product    = $this->tree->getProduct($task->module);
        $this->view->modulePath = $this->tree->getParents($task->module);
        $this->view->flag       = $flag;
        $this->view->oldTask    = $this->task->getByTaskToOldTask($taskID);
        $this->display();
    }
}
