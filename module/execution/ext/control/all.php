<?php
include '../../control.php';
class myExecution extends execution
{
    /**
     * Project: chengfangjinke
     * Method: all
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:28
     * Desc: This is the code comment. This method is called all.
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
     public function all($status = 'all', $projectID = 0, $orderBy = 'id_asc', $productID = 0, $recTotal = 0, $recPerPage = 10, $pageID = 1)
     {
         $this->app->loadLang('my');
         $this->app->loadLang('product');
         $this->app->loadLang('programplan');

         if(common::hasPriv('programplan', 'import')) $this->lang->TRActions  = html::a($this->createLink('programplan', 'import', "projectID=$projectID"), "<i class='icon icon-sm icon-import'></i> " . $this->lang->programplan->import, '', "class='btn btn-secondary importExcel'");

         $from = $this->app->openApp;
         if($from == 'execution') $this->session->set('executionList', $this->app->getURI(true), 'execution');
         $this->session->set('taskList', $this->app->getURI(true), 'execution');

         if($from == 'project')
         {
             $projects  = $this->project->getPairsByProgram();
             $projectID = $this->project->saveState($projectID, $projects);
             $this->project->setMenu($projectID);
         }

         /* Load pager and get tasks. */
         $this->app->loadClass('pager', $static = true);
         $pager = new pager($recTotal, $recPerPage, $pageID);

         $this->view->title      = $this->lang->execution->all;
         $this->view->position[] = $this->lang->execution->all;

         $this->view->executionStats = $this->project->getStats($projectID, $status, $productID, 0, 30, $orderBy, $pager);
         $this->view->taskStats      = $this->project->getTaskStats($projectID, $status);

         $this->view->productID      = $productID;
         $this->view->projectID      = $projectID;
         $this->view->projects       = $this->project->getPairsByModel();
         $this->view->pager          = $pager;
         $this->view->orderBy        = $orderBy;
         $this->view->users          = $this->loadModel('user')->getPairs('noletter');
         $this->view->status         = $status;
         $this->view->from           = $from;

         $this->display();
     }
}
