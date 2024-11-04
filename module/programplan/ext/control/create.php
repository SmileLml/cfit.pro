<?php
include '../../control.php';
class myProgramPlan extends programplan
{
    /**
     * Create  project plan.
     *
     * @param  int    $projectID
     * @param  int    $productID
     * @param  int    $planID
     * @access public
     * @return void
     */
    public function create($projectID = 0, $productID = 0, $planID = 0 ,$flag = null)
    {
        $this->loadModel('project')->setMenu($projectID);
        $this->app->loadLang('project');
        $this->app->loadLang('stage');

        if($_POST)
        {
            $this->programplan->createSubStage($projectID, $planID,$flag);
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $url = $flag ? $this->createLink('newexecution', 'execution', "browseType=all&project=$projectID") : $this->createLink('project', 'execution', "browseType=all&project=$projectID");
            $locate = $this->session->projectPlanList ? $this->session->projectPlanList : $url;
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $locate));
        }

        $this->view->title               = $this->lang->programplan->create;
        $this->view->programPlan         = $this->project->getById($planID, 'stage');
        $this->view->planID              = $planID;
        $this->lang->stage->typeList[''] = '';

        $this->display();
    }
}
