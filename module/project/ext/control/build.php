<?php
include '../../control.php';
class myProject extends project
{
    /**
     * Browse builds of a project.
     *
     * @param  string $type      all|product|bysearch
     * @param  int    $param
     * @access public
     * @return void
     */
    public function build($projectID = 0, $type = 'all', $param = 0,$orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        /* Load module and get project. */
        $this->loadModel('build');
        $this->app->loadLang('build');
        $project = $this->project->getByID($projectID);
        $this->project->setMenu($projectID);

        $this->session->set('buildList', $this->app->getURI(true), 'project');

        /* Get products' list. */
        $products = $this->project->getProducts($projectID, false);
        $products = array('' => '','99999' => '无', '0'=>'全部产品') + $products;
        $plans = array("" => "",'1'=>'无') + $this->loadModel('productplan')->getPairs(array_keys(array_filter($products)), 0);
        $this->config->build->search['params']['version']['values'] = $plans;

        $apps       = array('0' => '') + $this->loadModel('application')->getapplicationNameCodePairs();
        $this->config->build->search['params']['app']['values'] = $apps;
        /* Build the search form. */
        $type      = strtolower($type);
        $queryID   = ($type == 'bysearch') ? (int)$param : 0;
        $actionURL = $this->createLink('project', 'build', "projectID=$projectID&type=bysearch&queryID=myQueryID");

        $this->project->buildProjectBuildSearchForm($products, $queryID, $actionURL, 'project');

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);
        if($type == 'bysearch')
        {
            $builds = $this->build->getProjectBuildsBySearch((int)$projectID, (int)$param,$orderBy, $pager);
        }
        else
        {
            $builds = $this->build->getProjectBuilds((int)$projectID, $type, $param,$orderBy, $pager);
        }

        /* Set project builds. */
        /*$projectBuilds = array();
        if(!empty($builds))
        {
            foreach($builds as $build) $projectBuilds[$build->product][] = $build;
        }*/




        $this->view->orderBy    = $orderBy;
        $this->view->pager      = $pager;
        $this->view->pageID      = $pageID;
        $this->view->param      = $param;

        /* Header and position. */
        $this->view->title      = $project->name . $this->lang->colon . $this->lang->execution->build;
        $this->view->position[] = $this->lang->execution->build;

        $this->view->users         = $this->loadModel('user')->getPairs('noletter');
        $this->view->buildsTotal   = count($builds);
        $this->view->projectBuilds = $builds;//$projectBuilds;
        $this->view->product       = $type == 'product' ? $param : 'all';
        $this->view->projectID     = $projectID;
        $this->view->project       = $project;
        $this->view->products      = $products;
        $this->view->type          = $type;
        $this->view->status        = $this->lang->build->statusList;
        $this->view->versions      = $plans;
        $this->display();
    }
}
