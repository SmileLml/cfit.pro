<?php
/**
 * The control file of reviewqz module of ZenTaoPMS.
 *
 * Created by PhpStorm.
 * User: t_wangjiurong
 * Date: 2023/2/20
 * Time: 9:43
 */
class reviewissueqz extends control{

    // 清总评审问题列表
    public function issue($reviewID = 0, $browseType = 'all', $param = 0, $orderBy = "id_desc", $recTotal = 0, $recPerPage = 15, $pageID = 1){
        $this->loadModel('datatable');
        $queryID = ($browseType == 'bySearch') ? (int)$param : 0;
        $issueParams =  "&reviewID=$reviewID" . "&browseType=bySearch&param=myQueryID&orderBy=$orderBy";
        $actionURL = $this->createLink('reviewissueqz', 'issue', $issueParams);
        $this->reviewissueqz->buildSearchForm($queryID, $actionURL);

        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);
        //清总评审信息
        $reviewInfo = empty($reviewID) ? array() : $this->loadModel('reviewqz')->getByID($reviewID);
        $issueList = $this->reviewissueqz->getList($reviewID, $browseType, $queryID, $orderBy, $pager);

        $users = $this->loadModel('user')->getAllUsers();
        $this->view->title      = $this->lang->reviewissueqz->issue;
        $this->view->issueList  = $issueList;
        $this->view->reviewInfo = $reviewInfo;
        $this->view->users      = $users;
        $this->view->pager      = $pager;
        $this->view->reviewID   = $reviewID;
        $this->view->status     = $browseType;
        $this->view->orderBy    = $orderBy;
        $this->view->browseType = $browseType;
        $this->display();
    }


    /**
     * 清总评审问题详情
     *
     * @param $issueId
     * @param int $reviewID
     * @param string $statusNew
     * @param string $orderBy
     * @param int $recTotal
     * @param int $recPerPage
     * @param int $pageID
     */
    public function view($issueId, $reviewID = 0,$statusNew ='all', $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 10, $pageID = 1){

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);
        $issueInfo = $this->reviewissueqz->getByID($issueId);
        $this->view->title     = $this->lang->reviewissueqz->view;
        $this->view->issue     = $issueInfo;
        $this->view->issueID   = $issueId;
        $this->view->reviewID  = $reviewID;
        $this->view->pager   = $pager;
        $this->view->status   = $statusNew;
        $this->view->orderBy   = $orderBy;
        $this->view->actions   = $this->loadModel('action')->getList($this->lang->reviewissueqz->objectType, $issueId);
        $this->view->users     = $this->loadModel('user')->getAllUsers();
        $this->display();
    }

    // 新建问题
    public function create($reviewID = 0, $statusNew = 'all', $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 10, $pageID = 1)
    {
        $this->loadModel('review');
        $this->loadModel('reviewissue');
        $reviewPairs = [];
        $params = "&review=$reviewID" . "&statusNew=$statusNew&param=0&orderBy=$orderBy" . "&recTotal=$recTotal" . "&recPerPage=$recPerPage" . "&pageID=$pageID";
        if ($_POST) {
            $issueID = $this->reviewissueqz->create($reviewID);
            if (dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $this->loadModel('action')->create('reviewissueqz', $issueID, 'opened', $this->post->opinion);
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $this->createLink('reviewissueqz', 'issue', $params)));
        }

        // 查询可见评审标题
        $reviewList = $this->loadModel('reviewqz')->reviewList($statusNew, 0, $orderBy, 0);
        foreach($reviewList as $info){
            $reviewPairs[$info->id] = $info->id . '_' . $info->title;
        }

        $emptyArr = array('0' => '');
        $this->view->title = $this->lang->reviewissueqz->create;
        $this->view->reviewPairs = $emptyArr + $reviewPairs;
        $this->view->reviewID = $reviewID;
        $this->view->users = $this->loadModel('user')->getAllUsers();
        $this->display();
    }

    //批量新建
    public function batchCreate($reviewID = 0, $statusNew = 'all', $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 10, $pageID = 1)
    {
        if (!empty($_POST)) {
            $params = "&review=$reviewID" . "&statusNew=$statusNew" . "&param=0" . "&orderBy=$orderBy" . "&recTotal=$recTotal" . "&recPerPage=$recPerPage" . "&pageID=$pageID";
            $this->reviewissueqz->batchCreate($reviewID);
            die(js::locate($this->createLink('reviewissueqz', 'issue', $params), 'parent'));
        }

        //处理提出阶段
        $reviewPairs = [];
        $emptyArr = array('0' => '');
//        $this->app->loadClass('pager', $static = true);
//        $pager = new pager($recTotal, $recPerPage, $pageID);

        // 查询可见评审标题
        //$issueList = $this->reviewissueqz->getList($reviewID, $statusNew, 0, $orderBy, $pager);
        //$reviewQzIds = array_column($issueList, 'reviewId');
        $reviewList = $this->loadModel('reviewqz')->reviewList($statusNew, 0, $orderBy, 0, 'issue');
        foreach($reviewList as $info){
            $reviewPairs[$info->id] = $info->title;
        }

        $this->view->title = $this->lang->reviewissueqz->create;
        $this->view->reviewPairs = $emptyArr + $reviewPairs;
        $this->view->reviewID = $reviewID;
        $this->view->users = $this->loadModel('user')->getAllUsers();

        $this->display();
    }


    /**
     *清总评审问题编辑
     *
     * @param $issueId
     * @param int $reviewID
     * @param string $statusNew
     * @param string $orderBy
     * @param int $recTotal
     * @param int $recPerPage
     * @param int $pageID
     */
    function edit($issueId, $reviewID = 0, $statusNew ='all', $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 10, $pageID = 1){
        $this->loadModel('reviewqz');
        $reviewPairs = [];
        if ($_POST) {
            $changes = $this->reviewissueqz->update($issueId);
            if ($changes) {
                $actionID = $this->loadModel('action')->create($this->lang->reviewissueqz->objectType, $issueId, 'Edited');
                $this->action->logHistory($actionID, $changes);
            }

            if (dao::isError()) {
                $this->send(array('result' => 'fail', 'message' => dao::getError()));
            }

            $paramsIssue = "review=$reviewID" . "&statusNew=$statusNew" . "&param=0" . "&orderBy=$orderBy" . "&recTotal=$recTotal" . "&recPerPage=$recPerPage" . "&pageID=$pageID";
            $this->send(
                array(
                    'result' => 'success',
                    'message' => $this->lang->saveSuccess,
                    'locate' => $this->createLink('reviewissueqz', 'issue', $paramsIssue)
                )
            );
        }
        $this->view->title = $this->lang->reviewissueqz->edit;
        $issue = $this->reviewissueqz->getByID($issueId);
//        $searchQuery = ''; //暂时自定义空
//        $reviewList = $this->reviewqz->getListBySearchQuery($searchQuery, 'id,title');
//
//        if($reviewList){
//            $reviewList = array_column($reviewList, 'title', 'id');
//        }
        $reviewList = $this->loadModel('reviewqz')->reviewList($statusNew, 0, $orderBy, 0);
        foreach($reviewList as $info){
            $reviewPairs[$info->id] = $info->id . '_' . $info->title;
        }

        $this->view->issue = $issue;
        $this->view->reviewList = $reviewPairs;
        $this->view->users = $this->loadModel('user')->getAllUsers();
        $this->display();
    }


    /**
     *删除
     *
     * @param $issueId
     * @param int $reviewID
     * @param string $statusNew
     * @param string $orderBy
     * @param int $recTotal
     * @param int $recPerPage
     * @param int $pageID
     */
    public function delete($issueId, $reviewID = 0, $statusNew ='all', $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 10, $pageID = 1){
        if (!empty($_POST)) {
            $ret = $this->reviewissueqz->deleteOp($issueId);
            if(dao::isError()) {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $actionID = $this->loadModel('action')->create($this->lang->reviewissueqz->objectType, $issueId, 'deleted', $this->post->delDesc);
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }

        $this->view->title = $this->lang->reviewissueqz->delete;
        $issue = $this->reviewissueqz->getByID($issueId); //清总评审问题信息
        $this->view->issue = $issue;
        $this->view->reviewInfo = $this->loadModel('reviewqz')->getByID($issue->reviewId);  //清总评审信息
        $this->view->actions    = $this->loadModel('action')->getList($this->lang->reviewissueqz->objectType, $issueId);
        $this->view->users      = $this->loadModel('user')->getAllUsers();
        $this->display();
    }

}
