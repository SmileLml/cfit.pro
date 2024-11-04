<?php

include '../../control.php';
class myProjectplan extends projectplan
{
    /**
     *  新增  查看关联项目名称明细
     */
    public  function  planview($planID = 0){

        $this->app->loadLang('opinion');

        $plan = $this->loadModel('projectplan')->getByID($planID);

        $this->view->plan  = $plan;
        $this->view->title = $this->lang->projectplan->view;

        $this->view->actions  = $this->loadModel('action')->getList('projectplan', $planID);
        $this->view->users    = $this->loadModel('user')->getPairs('noclosed|noletter');
        $this->view->lines    = array('' => '') + $this->loadModel('product')->getLinePairs();
        $this->view->depts    = $this->loadModel('dept')->getOptionMenu();

        $this->view->apps     = array('' => '') + $this->loadmodel('application')->getapplicationNameCodePairs();
        $this->view->plans    = $this->projectplan->getPairs();

        $this->view->outsideproject  = $this->loadModel('outsideplan')->getPairs();
        $this->view->requirementList = $this->loadModel('project')->getUserRequirementList($plan->id);
        $this->view->relatedObject   = $this->loadModel('secondline')->getByID($plan->project, 'project');

        $this->view->bookNodes = $this->loadModel('review')->getNodes('projectplan', $planID, $this->view->plan->version);
        $this->view->yearNodes = $this->review->getNodes('projectplanyear', $planID, $this->view->plan->yearVersion);
        $this->view->products  = $this->projectplan->getProducts($plan->project);

        $this->display();
    }

}
