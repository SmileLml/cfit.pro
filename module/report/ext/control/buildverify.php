<?php
include '../../control.php';
class myReport extends report
{
    public function buildVerify()
    {
        $this->app->loadLang('build');
        $this->app->loadLang('project');

        //$this->loadModel('project')->setMenu($projectID);
        // 获取搜索条件。
        $appName           = $this->post->appName   ? $this->post->appName : '';
        $verifyActionDate  = $this->post->verifyActionDate     ? $this->post->verifyActionDate   : '';
        $verifyDealUser    = $this->post->verifyDealUser ? $this->post->verifyDealUser   : '';


        $build             = $this->report->getBuildWorkLoad(0, $appName, $verifyActionDate, $verifyDealUser);

        $this->view->title      = $this->lang->report->buildVerify;
        $this->view->position[] = $this->lang->report->buildVerify;
        $this->view->submenu   = 'staff';
        $this->view->projectID = 0;
        $this->view->build     = $build;
        $this->view->appName   = $appName;
        $this->view->verifyActionDate = $verifyActionDate;
        $this->view->verifyDealUser   = $verifyDealUser;

        $this->view->users      = $this->loadModel('user')->getPairs('noletter|noclosed');
        $this->view->apps       = array('0' => '') + $this->loadModel('application')->getapplicationNameCodePairs();
        $param = json_encode(array('appName' => $appName, 'verifyActionDate' => $verifyActionDate, 'verifyDealUser' => $verifyDealUser));
        $this->view->param        = helper::safe64Encode($param);
        $this->display();
    }
}
