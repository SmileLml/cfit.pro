<?php
helper::import(dirname(dirname(dirname(__FILE__))) . "/control.php");
class myProject extends project
{
    /**
     * Import case from lib.
     *
     * @param  int      $projectID
     * @param  int      $applicationID
     * @param  int      $productID
     * @param  int      $branch
     * @param  int      $libID
     * @param  string   $orderBy
     * @param  string   $browseType
     * @param  int      $queryID
     * @param  int      $recTotal
     * @param  int      $recPerPage
     * @param  int      $pageID
     * @access public
     * @return void
     */
    public function importFromLib($projectID, $applicationID, $productID, $branch = 0, $libID = 0, $orderBy = 'id_desc', $browseType = '', $queryID = 0, $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $browseType = strtolower($browseType);
        $queryID    = (int)$queryID;
        $this->project->setMenu($projectID);
        if($_POST)
        {
            $this->project->importFromLib($projectID, $applicationID, $productID);
            die(js::reload('parent'));
        }
        $libraries = $this->loadModel('caselib')->getLibraries();

        /* Build the search form. */
        $actionURL = $this->createLink('project', 'importFromLib', "projectID=$projectID&applicationID=$applicationID&productID=$productID&branch=$branch&libID=$libID&orderBy=$orderBy&browseType=bySearch&queryID=myQueryID");

        $this->loadModel('testcase');
        $this->config->testcase->search['module']    = 'testsuite';
        $this->config->testcase->search['onMenuBar'] = 'no';
        $this->config->testcase->search['actionURL'] = $actionURL;
        $this->config->testcase->search['queryID']   = $queryID;
        $this->config->testcase->search['fields']['lib'] = $this->lang->testcase->lib;
        $this->config->testcase->search['params']['lib'] = array('operator' => '=', 'control' => 'select', 'values' => array('' => '', $libID => $libraries[$libID], 'all' => $this->lang->caselib->all));
        $this->config->testcase->search['params']['module']['values'] = $this->loadModel('tree')->getOptionMenu($libID, $viewType = 'caselib');
        unset($this->config->testcase->search['fields']['product']);
        unset($this->config->testcase->search['fields']['branch']);
        $this->loadModel('search')->setSearchParams($this->config->testcase->search);

        $modules = array();
        if(is_numeric($productID)) $modules = $this->loadModel('tree')->getOptionMenu($productID, 'case', 0, $branch);

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = pager::init(0, $recPerPage, $pageID);

        $this->view->title         = $this->lang->project->common . $this->lang->colon . $this->lang->project->importFromLib;
        $this->view->libraries     = $libraries;
        $this->view->libID         = $libID;
        $this->view->projectID     = $projectID;
        $this->view->applicationID = $applicationID;
        $this->view->productID     = $productID;
        $this->view->branch        = $branch;
        $this->view->cases         = $this->loadModel('testsuite')->getNotImportedCases($productID, $libID, $orderBy, $pager, $browseType, $queryID);
        $this->view->modules       = $modules;
        $this->view->libModules    = $this->tree->getOptionMenu($libID, 'caselib');
        $this->view->pager         = $pager;
        $this->view->orderBy       = $orderBy;
        $this->view->branches      = array();
        $this->view->browseType    = $browseType;
        $this->view->queryID       = $queryID;
        $this->display();
    }
}
