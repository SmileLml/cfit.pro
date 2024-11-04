<?php
include '../../control.php';
class myReport extends report
{
    public function buildWorkLoad($projectID = 0)
    {
        $this->app->loadLang('build');

        $this->loadModel('project')->setMenu($projectID);
        // 获取搜索条件。
        $appName           = $this->post->appName   ? $this->post->appName : '';
        $verifyActionDate  = $this->post->verifyActionDate     ? $this->post->verifyActionDate   : '';
        $verifyDealUser    = $this->post->verifyDealUser ? $this->post->verifyDealUser   : '';
        $build             = $this->report->getBuildWorkLoad($projectID, $appName, $verifyActionDate, $verifyDealUser);
        $this->view->title      = $this->lang->report->buildWorkload;
        $this->view->position[] = $this->lang->report->buildWorkload;
        $this->view->submenu   = 'program';
        $this->view->projectID = $projectID;
        $this->view->build     = $build;
        $this->view->appName   = $appName;
        $this->view->verifyActionDate = $verifyActionDate;
        $this->view->verifyDealUser   = $verifyDealUser;


        $this->view->users      = $this->loadModel('user')->getPairs('noletter|noclosed');
        $this->view->apps       = array('0' => '') + $this->loadModel('application')->getApps($projectID);
        $param = json_encode(array('appName' => $appName, 'verifyActionDate' => $verifyActionDate, 'verifyDealUser' => $verifyDealUser));
        $this->view->param        = helper::safe64Encode($param);
        $this->display();
    }
}
