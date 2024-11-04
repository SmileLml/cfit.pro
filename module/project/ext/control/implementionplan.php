<?php
include '../../control.php';
class myProject extends project
{
    /**
     * @param string $status
     * @param int $projectID
     * @param string $orderBy
     * @param int $productID
     * @param int $recTotal
     * @param int $recPerPage
     * @param int $pageID
     */
    public function implementionPlan($status = 'all', $projectID = 0, $orderBy = 'id_desc', $productID = 0, $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        //if(common::hasPriv('doc', 'create')) $this->lang->TRActions = html::a(helper::createLink('doclib', 'create'), "<i class='icon icon-plus'></i> " . $this->lang->doclib->create, '', "class='btn btn-primary'");

        $this->loadModel('project');
        $this->loadModel('implementionplan');
        $uri = $this->app->getURI(true);
        $this->app->session->set('executionList', $uri, 'project');
        $this->session->set('common_back_url', $this->app->getURI(true),'backlog');
        $this->app->loadLang('my');
        $this->app->loadLang('product');
        $this->app->loadLang('programplan');
        $project  = $this->project->getById($projectID);
        $disabled = $project->status == 'closed' ? 'disabled' : '';

        $this->lang->TRActions = '';
        if(common::hasPriv('programplan', 'import')) $this->lang->TRActions .= html::a($this->createLink('programplan', 'import', "projectID=$projectID"), "<i class='icon icon-sm icon-import'></i> " . $this->lang->programplan->import, '', "class='btn btn-secondary importExcel {$disabled}'");
        if(common::hasPriv('programplan', 'batchChange')) $this->lang->TRActions .= html::a($this->createLink('programplan', 'batchChange', "projectID=$projectID"), "<i class='icon icon-sm icon-plus'></i> " . $this->lang->programplan->batchChange, '', "class='btn btn-primary {$disabled}'");

        $from = $this->app->openApp;
        $this->session->set('taskList', $this->app->getURI(true), $from);
        if($from == 'execution') $this->session->set('executionList', $this->app->getURI(true), 'execution');
        if($from == 'project')
        {
            $projects  = $this->project->getPairsByProgram();
            $projectID = $this->project->saveState($projectID, $projects);
            $this->project->setMenu($projectID);
        }


        /* Pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $libList = $this->implementionplan->getLibList($orderBy, $pager);

        $this->view->title      = $this->lang->implementionplan->common . $this->lang->colon . $this->lang->implementionplan->maintain;
        $this->view->position[] = $this->lang->implementionplan->maintain;

        $this->view->orderBy  = $orderBy;
        $this->view->pager    = $pager;
        $this->view->libList  = $libList;
        $this->display();
    }
}
