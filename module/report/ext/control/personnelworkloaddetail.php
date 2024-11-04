<?php
include '../../control.php';
class myReport extends report
{
    public function personnelWorkloadDetail($projectID = 0)
    {
        $this->app->loadLang('execution');
        $this->loadModel('project')->setMenu($projectID);

        // 获取搜索条件。
        $begin   = $this->post->begin   ? $this->post->begin : '';
        $end     = $this->post->end     ? $this->post->end   : '';
        $account = $this->post->account ? $this->post->account   : array();
        $depts = $this->loadModel('dept')->getTopPairs();

        $this->view->title      = $this->lang->report->personnelWorkloadDetail;
        $this->view->position[] = $this->lang->report->personnelWorkloadDetail;

        $this->view->submenu   = 'program';
        $this->view->projectID = $projectID;

        $this->view->begin   = $begin;
        $this->view->end     = $end;
        $this->view->account = $account;
        $this->view->depts = $depts;

        $this->view->participants = $this->report->getReportWorkloadUserPairs($projectID);
        $this->view->workloadList = $this->report->getPersonnelWorkloadDetail($projectID, $begin, $end, $account);

        $param = json_encode(array('begin' => $begin, 'end' => $end, 'account' => $account));
        $this->view->param        = helper::safe64Encode($param);

        $this->display();
    }
}
