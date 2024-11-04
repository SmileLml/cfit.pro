<?php
include '../../control.php';
class myTask extends task
{
    public function recordEstimate($taskID, $random = '')
    {
       // if($_SERVER['REQUEST_METHOD'] == 'GET' and $random != $this->session->estimateRandom) echo js::alert($this->lang->task->workReportPrompt);

        $this->session->set('estimateRandom', $random);

        $this->loadModel('effort');

        $task = $this->task->getByID($taskID);
        $left = $task->left;

        if(!empty($_POST))
        {
            // 成方金科剩余工时=estimate-consumed
            $efforts = array();
            $totalConsumed = array();
            foreach($_POST['consumed'] as $key => $c)
            {
                if(!$c) continue;
                if(empty($_POST['dates'][$key])) continue;

                $left -= $c;

                $row = new stdclass();
                $row->date     = $_POST['dates'][$key];
                $row->consumed = $c;
                $row->left     = $left;
                $row->work     = $_POST['work'][$key];
                //$row->progress = $_POST['progress'][$key];

                $efforts[] = $row;
                if(!isset($totalConsumed[$row->date])) $totalConsumed[$row->date] = 0;
                $totalConsumed[$row->date] += $c;
            }
            foreach($totalConsumed as $consumedDate => $consumed)
            {
                $consumedToday = $this->loadModel('effort')->getWorkloadToday($this->app->user->account, $consumed, 'insert', $consumedDate);
            }

            $this->task->batchCreateEffort($taskID, $efforts);
            if(dao::isError()) die(js::error(dao::getError()));
            if($this->app->viewType == 'mhtml')
            {
                die(js::locate($this->createLink('task', 'view', "taskID=$taskID"), 'parent'));
            }

            if(isonlybody()) echo js::closeModal('parent.parent', 'this');
            //die(js::locate($this->createLink('project', 'execution', "projectID =$task->project"), 'parent'));
            die(js::reload('parent'));
        }

        $date    = date(DT_DATE1);
        $efforts = $this->effort->getByObject('task', $taskID);

        if(isset($efforts['typeList'])) $this->view->typeList = $efforts['typeList'];
        unset($efforts['typeList']);

        $this->session->set('effortList', $this->app->getURI(true));

        $this->view->title      = $this->lang->my->common . $this->lang->colon . $this->lang->effort->create;
        $this->view->position[] = $this->lang->effort->create;

        $this->view->task       = $this->loadModel('task')->getById($taskID);
        $this->view->users      = $this->loadModel('user')->getPairs('noletter');
        $this->view->date       = $date;
        $this->view->efforts    = $efforts;
        $this->view->objectType = 'task';
        $this->view->taskID     = $taskID;
        $endDate                = $this->loadModel('review')->getCloseDate($task->project);//查询评审关闭时间
        $this->view->beginAndEnd = $this->loadModel('task')->getBeginAndEnd($task->project, isset($endDate->closeDate) ? $endDate->closeDate : '' );//查询可报工时间
        $this->view->projectType = $this->dao->select('secondLine')->from(TABLE_PROJECTPLAN)->where('project')->eq($task->project)->fetch();
        $this->display();
    }
}
