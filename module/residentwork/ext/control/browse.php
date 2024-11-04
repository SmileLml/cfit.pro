<?php
include '../../control.php';
class myResidentwork extends residentwork
{
    /**
     * Browse reviews.
     *
     * @param  string $browseType
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function browse($browseType = '', $param = 0, $orderBy = 't1.createdDate_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1){
        $depts = $this->loadModel('dept')->getOptionMenu();
        $users = $this->loadModel('user')->getPairs('noletter');
        $outsideList1 = $this->loadModel('user')->getUsersNameByType('outsideExpertType');
        $outsideList2 = $this->loadModel('user')->getUsersNameByType('outside');
        $users = array_merge($users, $outsideList1, $outsideList2);
        $this->app->loadLang("residentsupport");
        $browseType = strtolower($browseType);
        $queryID = ($browseType == 'bysearch')  ? (int)$param : 0;

        $emptyArr = array('0'=>'');
        $this->config->residentwork->search['params']['realDutyuserDept']['values']  = $emptyArr + $depts;//值班部门
        $this->config->residentwork->search['params']['type']['values']      =  $emptyArr + $this->lang->residentsupport->typeList;
        $this->config->residentwork->search['params']['dateType']['values']      =  $emptyArr + $this->lang->residentsupport->dateTypeList;
        $this->config->residentwork->search['params']['dutyPlace']['values']     =  $emptyArr + $this->lang->residentsupport->areaList;
        $this->config->residentwork->search['params']['isEmergency']['values']   =  $emptyArr + $this->lang->residentwork->importantTimeList;
        $actionURL = $this->createLink('residentwork', 'browse', "browseType=bysearch&param=myQueryID");
        $this->loadModel('residentwork')->buildSearchForm($queryID, $actionURL);

        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);
        $residentSupports = $this->residentwork->getWorkList($browseType,$queryID, $orderBy, $pager);
        $this->view->title = $this->lang->residentwork->common;
        /* 设置需求详情页面返回的url连接。*/
        $this->session->set('residentworkList', $this->app->getURI(true), 'backlog');
        $this->view->residentsupports = $residentSupports;
        $this->view->users = $users;
        $this->view->depts = $depts;
        $this->view->queryID    = $queryID;
        $this->view->pager     = $pager;
        $this->view->recTotal   = $recTotal;
        $this->view->recPerPage = $recPerPage;
        $this->view->pageID     = $pageID;
        $this->view->orderBy    = $orderBy;
        $this->view->browseType    = $browseType;
        $this->view->param      = $param;
        $this->view->title = $this->lang->residentsupport->common;
        $this->display();
    }

}