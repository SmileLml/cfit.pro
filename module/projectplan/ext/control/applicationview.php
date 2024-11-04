<?php

include '../../control.php';
class myProjectplan extends projectplan
{
    /**
     *  新增  查看关联项目名称明细
     */
    public  function  applicationview($appID = 0){

        $this->app->loadLang('application');

        $this->view->title       = $this->lang->application->view;
        $plan = $this->loadModel('projectplan')->getByID($appID);
        $this->view->plan  = $plan;
        $this->view->users       = $this->loadModel('user')->getPairs('noclosed');
        $this->view->actions     = $this->loadModel('action')->getList('application', $appID);
        $this->view->application = $this->loadModel('application')->getByID($appID);

        $this->display();
    }

}
