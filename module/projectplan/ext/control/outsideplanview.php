<?php

include '../../control.php';
class myProjectplan extends projectplan
{
    /**
     *  新增  查看(外部)项目/任务名称明细
     */
    public  function  outsideplanview($planID = 0){

        $this->app->loadLang('outsideplan');

        $this->app->loadLang('opinion');

        $this->view->title    = $this->lang->outsideplan->view;
        $this->view->actions  = $this->loadModel('action')->getList('outsideplan', $planID);
        $this->view->plan     = $this->loadModel('outsideplan')->getByID($planID);
        $this->view->users    = $this->loadModel('user')->getPairs('noclosed|noletter');
        $this->view->lines    = array('' => '') + $this->loadModel('product')->getLinePairs();
        $this->view->products = $this->product->getPairs();
        $this->view->depts    = $this->loadModel('dept')->getOptionMenu();
        $this->view->apps     = array('' => '') + $this->loadmodel('application')->getapplicationNameCodePairs();
        $this->view->plans    = $this->loadModel('projectplan')->getPairs();
        $this->display();
    }

}