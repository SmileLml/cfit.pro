<?php
include '../../control.php';
class myReview extends review
{
    /**
     * Browse reviews.
     *
     * @param  int    $projectID
     * @param  string $browseType
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function browse($projectID, $browseType = 'all', $param = 0, $orderBy = 't1.id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $this->commonAction($projectID);
        $this->loadModel('datatable');
        $this->session->set('reviewList', $this->app->getURI(true));
        $browseType = strtolower($browseType);

        $depts           = array('0'=>'') + $this->loadModel('dept')->getOptionMenu();
        $this->config->review->search['params']['createdDept']['values']  = $depts;

        foreach($this->lang->review->statusLabelList  as $label => $labelName)
        {
            if(!isset($arr[$labelName])){
                if($label == 'all'){
                    $arr[''] = '';
                }else{
                    $label = strtolower($label);
                    $arr[$labelName] = $label;
                }
            }
        }
        $statusArray =  array_flip($arr);
        $this->config->review->search['params']['status']['values']  =  $statusArray;
        $users = $this->loadModel('user')->getPairs('noclosed|noletter');
        $outsideList1 = $this->loadModel('user')->getUsersNameByType('outsideExpertType');
        $outsideList2 = $this->loadModel('user')->getUsersNameByType('outside');
        $users = array_merge($users, $outsideList1, $outsideList2);

        $this->view->outsideList1 = array(''=>'') + $outsideList1;
        $this->view->outsideList2 = array(''=>'') + $outsideList2;
        $this->view->users = $users;
        $this->config->review->search['params']['reviewedBy']['values']  = $this->view->outsideList1;
        $this->config->review->search['params']['outside']['values']  =  $this->view->outsideList2;
        $this->config->review->search['params']['meetingPlanExport']['values']  =  $users;

        //项目类型
        $this->app->loadLang('projectplan');
        $projectTypeList = $this->lang->projectplan->typeList;
        $this->config->review->search['params']['projectType']['values'] = $projectTypeList;
        //$this->view->projectTypeList = $projectTypeList;

        /* By search. */
        $queryID = ($browseType == 'bysearch')  ? (int)$param : 0;
        $actionURL = $this->createLink('review', 'browse', "projectID=$projectID&browseType=bySearch&param=myQueryID");
        $this->review->buildSearchForm($queryID, $actionURL);

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = pager::init($recTotal, $recPerPage, $pageID);

        $this->view->title      = $this->lang->review->browse ;
        $this->view->position[] = $this->lang->review->browse;
        
        $this->view->reviewList = $this->review->getList($projectID, $browseType, $queryID, $orderBy, $pager);

        $this->view->relatedUsers  = $this->loadModel('user')->getPairs('noclosed|noletter');
        $this->view->queryID    = $queryID;
        $this->view->pager      = $pager;
        $this->view->recTotal   = $recTotal;
        $this->view->recPerPage = $recPerPage;
        $this->view->pageID     = $pageID;
        $this->view->orderBy    = $orderBy;
        $this->view->browseType = $browseType;
        $this->view->products   = $this->loadModel('product')->getPairs($projectID);
        $this->view->projectID  = $projectID;
        $this->display();
    }

}