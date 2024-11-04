<?php
include '../../control.php';
class myProject extends project
{
    /**
     * Project: chengfangjinke
     * Method: execution
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:14
     * Desc: This is the code comment. This method is called execution.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param string $status
     * @param int $projectID
     * @param string $orderBy
     * @param int $productID
     * @param int $recTotal
     * @param int $recPerPage
     * @param int $pageID
     */
    public function execution($status = 'all', $projectID = 0, $orderBy = 'order_asc', $productID = 0, $recTotal = 0, $recPerPage = 9999, $pageID = 1)
    {
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

        /* Load pager and get tasks. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);
        $this->lang->waterfall->menu->programplan['subModule'] = 'project,newexecution,implementionplan';
        $this->view->title      = $this->lang->project->plan;
        $this->view->position[] = $this->lang->project->plan;

        $this->view->executionStats = $this->project->getStats($projectID, $status, $productID, 0, 50, $orderBy, $pager);
        $this->view->taskStats      = $this->project->getTaskStats($projectID, $status);

        $this->view->productID      = $productID;
        $this->view->projectID      = $projectID;
        $this->view->projects       = $this->project->getPairsByModel();
        $this->view->pager          = $pager;
        $this->view->orderBy        = $orderBy;
        $this->view->users          = $this->loadModel('user')->getPairs('noletter');
        $this->view->status         = $status;
        $this->view->projectStatus  = $project->status;
        $this->view->from           = $from;
        $this->view->projectname    = $project->name;

        $this->display();
    }
}
