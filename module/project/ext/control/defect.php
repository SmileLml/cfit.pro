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

    public function defect($projectID = 0, $browseType = 'all', $param = 0, $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        /* Load module and get project. */
        $this->loadModel('datatable');
        $this->loadModel('defect');
        $this->app->loadLang('defect');
        $project = $this->project->getByID($projectID);
        $this->project->setMenu($projectID);


        $browseType = strtolower($browseType);

        /* 获取固定排序字段。 */
        if(isset($this->config->project->defect->fixedSort)) $orderBy = $this->config->project->defect->fixedSort;

        /* By search. */

//        $apps = $this->application->getPairs();
//        $this->config->defect->search['params']['app']['values'] = $apps;
//
        $this->app->loadLang('bug');
        $queryID = ($browseType == 'bysearch')  ? (int)$param : 0;
        $actionURL = $this->createLink('project', 'defect', "projectID=$projectID&browseType=bySearch&param=myQueryID");

        $this->defect->buildSearchForm($queryID, $actionURL);
        $this->session->set('defectList', $this->app->getURI(true), 'project');

        /* 设置详情页面返回的url连接。*/
        $this->session->set('common_back_url', $this->app->getURI(true),'backlog');

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);
        $defects = $this->defect->getList($projectID, $browseType, $queryID, $orderBy, $pager);
        $this->view->title      = $this->lang->defect->common;
        $products  = $this->loadModel('product')->getSimplePairs();
        $this->view->products       =  array('0'=>'') + $products;
        $this->view->orderBy    = $orderBy;
        $this->view->pager      = $pager;
        $this->view->param      = $param;
        $this->view->browseType = $browseType;
        $this->view->projects      =  array('0'=>'') + $this->loadModel('project')->getProjects();
        $this->view->users      = $this->loadModel('user')->getPairs('noletter|noclosed');
        $this->view->defects = $defects;
        $this->view->projectID     = $projectID;
        $this->display();
    }

}
