<?php
include '../../control.php';
class myProgramPlan extends programplan
{
    /**
     * Browse program plans.
     *
     * @param  int     $projectID
     * @param  int     $productID
     * @param  string  $type
     * @param  string  $orderBy
     * @param  int     $baselineID
     * @access public
     * @return void
     */
    public function browse($projectID = 0, $productID = 0, $type = 'gantt', $orderBy = 'id_asc', $baselineID = 0)
    {
        $this->app->loadLang('stage');
        $this->commonAction($projectID, $productID, $type);
        $this->session->set('projectPlanList', $this->app->getURI(true), 'project');

        if(common::hasPriv('programplan', 'import')) $this->lang->TRActions  = html::a($this->createLink('programplan', 'import', "projectID=$projectID"), "<i class='icon icon-sm icon-import'></i> " . $this->lang->programplan->import, '', "class='btn btn-secondary importExcel'");
        // if(common::hasPriv('programplan', 'create')) $this->lang->TRActions .= html::a($this->createLink('programplan', 'create', "projectID=$projectID"), "<i class='icon icon-sm icon-plus'></i> " . $this->lang->programplan->create, '', "class='btn btn-primary'");

        $selectCustom = 0; // Display date and task settings.
        $dateDetails  = 1; // Gantt chart detail date display.
        if($type == 'gantt')
        {
            $owner        = $this->app->user->account;
            $module       = 'programplan';
            $section      = 'browse';
            $object       = 'stageCustom';
            $selectCustom = $this->loadModel('setting')->getItem("owner={$owner}&module={$module}&section={$section}&key={$object}");
;
            if(strpos($selectCustom, 'date') !== false) $dateDetails = 0;

            $plans = $this->programplan->getDataForGantt($projectID, $this->productID, $baselineID);
        }

        if($type == 'lists')
        {
            $sort  = $this->loadModel('common')->appendOrder($orderBy);
            $this->loadModel('datatable');
            $plans = $this->programplan->getPlans($projectID, $this->productID, $sort);
        }

        $this->view->title        = $this->lang->programplan->browse;
        $this->view->position[]   = $this->lang->programplan->browse;
        $this->view->projectID    = $projectID;
        $this->view->productID    = $this->productID;
        $this->view->type         = $type;
        $this->view->plans        = $plans;
        $this->view->orderBy      = $orderBy;
        $this->view->selectCustom = $selectCustom;
        $this->view->dateDetails  = $dateDetails;
        $this->view->users        = $this->loadModel('user')->getPairs('noletter');

        $this->display();
    }
}
