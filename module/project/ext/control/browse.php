<?php
include '../../control.php';
class myProject extends project
{
    /**
     * Project: chengfangjinke
     * Method: browse
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:14
     * Desc: This is the code comment. This method is called browse.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $programID
     * @param string $browseType
     * @param int $param
     * @param string $orderBy
     * @param int $recTotal
     * @param int $recPerPage
     * @param int $pageID
     */
    public function browse($programID = 0, $browseType = 'all', $param = 0, $orderBy = 'order,id_asc', $recTotal = 0, $recPerPage = 15, $pageID = 1)
    {
        $this->loadModel('datatable');
        $this->loadModel('execution');
        $this->session->set('projectList', $this->app->getURI(true), 'project');

        /* Load pager and get tasks. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $browseType = strtolower($browseType);
        $queryID = ($browseType == 'bysearch')  ? (int)$param : 0;
        $actionURL = $this->createLink('product', 'all', "browseType=bySearch&param=myQueryID");
        $this->loadModel('secondline')->buildSearchForm($queryID, $actionURL);

        /* Build the search form. */
        $browseType = strtolower($browseType);
        $queryID    = ($browseType == 'bysearch') ? (int)$param : 0;
        $actionURL  = $this->createLink('project', 'browse', "programID=$programID&browseType=bysearch&queryID=myQueryID");
        $this->project->buildSearchForm($actionURL, $queryID);

        $queryID = ($browseType == 'bysearch') ? (int)$param : 0;
        $programTitle = $this->loadModel('setting')->getItem('owner=' . $this->app->user->account . '&module=project&key=programTitle');
        $projectStats = $this->project->getProjectStats($programID, $browseType, $queryID, $orderBy, $pager, $programTitle);

        $this->view->title      = $this->lang->project->browse;
        $this->view->position[] = $this->lang->project->browse;

        $this->view->projectStats = $projectStats;
        $this->view->pager        = $pager;
        $this->view->programID    = $programID;
        $this->view->program      = $this->loadModel('program')->getByID($programID);
        $this->view->programTree  = $this->project->getTreeMenu(0, array('projectmodel', 'createManageLink'), 0, 'list');
        $this->view->users        = $this->loadModel('user')->getPairs('noletter|pofirst|nodeleted');
        $this->view->browseType   = $browseType;
        $this->view->param        = $param;
        $this->view->orderBy      = $orderBy;

        $this->display('project', 'browsetable');
    }
}
