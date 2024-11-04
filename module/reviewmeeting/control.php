<?php
/**
 * The control file of reviewmeeting module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2021 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      wangjiurong <t_wangjiurong@cfit.cn>
 * @package     reviewmeeting
 * @version     $Id: control.php 5107 2022-07-09 09:46:12Z t_wangjiurong@cfit.cn $
 * @link        https://www.zentao.net
 */
class reviewmeeting extends control
{
    //处理会议评审
    public function review($meetingID){
        if($_POST) {
            $logChanges = $this->reviewmeeting->review($meetingID);
            if(dao::isError()) {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $meetingInfo = $this->reviewmeeting->getMeetingById($meetingID);
            $status = $meetingInfo->status;
            $actionType = $this->reviewmeeting->getOpReviewActionType($status);

            //日志扩展信息
            $actionID = $this->loadModel('action')->create('reviewmeeting', $meetingID, $actionType, $this->post->comment, '', '', true);

            if($logChanges) {
                $this->action->logHistory($actionID, $logChanges);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }
        //会议信息
        $meetingInfo = $this->reviewmeeting->getMeetingById($meetingID);

        //是否允许审批
        $checkRes = $this->reviewmeeting->checkIsAllowReview($meetingInfo, $this->app->user->account);
        if($checkRes['result']){ //允许评审
            $reviewList = $checkRes['data']['reviewList'];
            $status = $meetingInfo->status; //会议状态

            //审核试图
            $reviewView = zget($this->lang->reviewmeeting->reviewViewList, $status);
            $users = $this->loadModel('user')->getPairs('noletter');
            if($status == $this->lang->reviewmeeting->statusList['waitMeetingOwnerReview']){ //待确定会议评审结论
                //$reviewList = $this->loadModel('review')->getReviewListByMeetingCodeOnlyWait($meetingInfo->meetingCode, 'id, title, expert, reviewedBy, outside,project');
                //获得每个项目评审的验证人
                $reviewIds = array_column($reviewList, 'id');
                $verifyUserList = $this->loadModel('review')->getVerifyUserList($reviewIds);

                $defEditDeadLine   = helper::getWorkDate(2);
                $defVerifyDeadline = helper::getWorkDate(3);
                $this->view->verifyUserList  = $verifyUserList;
                $this->view->defEditDeadLine  = $defEditDeadLine;
                $this->view->defVerifyDeadline  = $defVerifyDeadline;

                $users = $this->loadModel('user')->getPairs('noletter');
                $userList = array();
                foreach ($verifyUserList as $key=>$value){
                    $userList[$key] = zmget($users,$value);
                }
                $this->view->preliminaryReviewer = $userList;
                $issueUsers = array();
                $reviewIds = [];
                foreach ($reviewList as $review){
                    $reviewIds[] = $review->id;
                    $issueList = $this->review->getReviewIssue($review->project, $review->id);
                    $issueUserList = array();
                    foreach ($issueList as $issue){
                        array_push($issueUserList, zget($users,$issue->raiseBy,''));
                    }
                    $issueUsers[$review->id] = implode(',', array_unique($issueUserList));
                }
                $this->view->questionReviewer = $issueUsers;
                //提出评审问题但未解决的人员
                $unDealIssueUsers = $this->loadModel('reviewissue')->getUnDealReviewIssueUsers($reviewIds);
                $this->view->unDealIssueUsers = $unDealIssueUsers;
            }else{//待填写会议纪要
                //$reviewList = $this->loadModel('review')->getReviewListByMeetingCode($meetingInfo->meetingCode, 'id, title, expert, reviewedBy, outside', $this->lang->reviewmeeting->statusList['waitMeetingReview']);
                foreach ($reviewList as $key => $val){
                    $reviewList[$key]->realExportUsers = $this->reviewmeeting->getReviewRealExportUsers($val->expert, $val->reviewedBy, $val->outside);
                }
                $outsideList1 = $this->loadModel('user')->getUsersNameByType('outsideExpertType');
                $outsideList2 = $this->loadModel('user')->getUsersNameByType('outside');
                $users = array_merge($users, $outsideList1, $outsideList2);
            }

            $this->view->users = $users;
            $this->view->reviewList  = $reviewList;
            $this->view->reviewView  = $reviewView;
        }
        $this->view->meetingInfo  = $meetingInfo;
        $this->view->checkRes     = $checkRes;
        $this->display();
    }

    /**
     * 获得会议列表
     *
     * @param $type
     * @param int $reviewId
     * @param $isReNew 是否恢复时关联会议
     */
    public function ajaxAllowBindMeetingList($type, $reviewId = 0, $isReNew = false){
        $minTime = helper::today();
        $meetingCode = '';
        if($reviewId){
            $reviewInfo = $this->loadModel('review')->getReviewMainInfo($reviewId, 'meetingCode');
            if($reviewInfo && $reviewInfo->meetingCode){
                $meetingCode = $reviewInfo->meetingCode;
            }
        }
        $data = $this->reviewmeeting->getAllowBindMeetingList($type, $minTime, $meetingCode, $isReNew);
        if(empty($data)){
            $data = array('0' => '暂无会议日程');
        }else{
            $data = array('0' => '') + $data;
        }
        echo html::select('meetingCode', $data, $meetingCode, "class='form-control chosen' onchange='setSelectOwnerList();' required");
    }

    /**
     * 根据会议号获得评审主席
     *
     * @param $meetingCode
     */
    public function ajaxGetOwner($meetingCode){
        $owner = '';
        if(!$meetingCode){
            echo $owner;
        }
        $data = $this->reviewmeeting->getMeetingByMeetingCode($meetingCode, 'owner');
        $owner = isset($data->owner) ? $data->owner: '';
        echo $owner;
    }

    /**
     * View a review.
     *
     * @param int $meetingID
     * @access public
     * @return void
     */
    public function meetingview($meetingID,$flag = 0)
    {
        $this->view->title        = $this->lang->reviewmeeting->meetingview ;
        $this->view->actions  = $this->loadModel('action')->getList('reviewmeeting', $meetingID);
        $meetingInfo = $this->reviewmeeting->getById($meetingID);
        $allmeetingInfo = $this->reviewmeeting->getMeetingInfoByMeetingCode($meetingInfo->meetingCode);

        $reviewProjects = $this->reviewmeeting->getReviewDetailListByMeetingCode($meetingInfo->meetingCode);
        $this->view->reviewProjects =   $reviewProjects;
        $issueLists=$this->reviewmeeting->getReviewIssue($meetingInfo->meetingCode);
        $this->view->meetingExperts=$this->reviewmeeting->getReviewResult($meetingInfo->meetingCode,'meetingReview');
        $this->view->meetingOwners=$this->reviewmeeting->getReviewResult($meetingInfo->meetingCode,'meetingOwnerReview');
        $this->view->titleAndResult = $this->reviewmeeting->getResult($meetingInfo->meetingCode,'meetingOwnerReview');
        $this->view->issueLists = $issueLists ;
        $this->view->meetingInfo = $meetingInfo;
        $this->view->allmeetingInfo =  $allmeetingInfo;
        $this->view->basisList = $this->lang->reviewmeeting->basisList;

        $users = $this->loadModel('user')->getPairs('noletter');
        $outsideList1 = $this->loadModel('user')->getUsersNameByType('outsideExpertType');
        $outsideList2 = $this->loadModel('user')->getUsersNameByType('outside');
        $users = array_merge($users, $outsideList1, $outsideList2);
        $this->view->outsideList1 = $outsideList1;
        $this->view->outsideList2 = $outsideList2;
        $this->view->users = $users;
        //获取一个评审会议的项目个数
        $this->view->reviewMeetingDetailCount = $this->reviewmeeting->getReviewMeetingValidDetailCount($meetingInfo->meetingCode);
        $projects = $this->loadModel('project')->getByID('outside');

        $this->app->loadLang('reviewissue');
        $this->view->relatedUsers = $this->loadModel('user')->getPairs('noletter');
        $this->view->depts   = $this->loadModel('dept')->getOptionMenu();
        $this->view->gradeList = $this->loadModel('review')->getReviewAllGradeList();
        $this->view->allstatus = $this->lang->review->statusLabelList + $this->lang->review->statusFile;
        $this->view->allstatus = $this->lang->review->statusLabelList ;
        $this->view->flag = $flag;
        $this->display();
    }

    /**
     * Desc:删除
     * Date: 2022/4/27
     * Time: 11:05
     *
     * @param $projectID
     * @param int $issueID
     *
     */
    public function deleteissue($meetingId = 0, $issueID = 0, $confirm = 'no')
    {
        $this->view->actions  = $this->loadModel('action')->getList('reviewmeeting', $meetingId);
        //$delDesc = $_POST['delDesc'];
        if($confirm == 'no')
        {
            die(js::confirm($this->lang->reviewmeeting->confirmDelete, $this->createLink('reviewmeeting', 'deleteissue', "meetingId=$meetingId&issueID=$issueID&confirm=yes")));
        }
        else
        {
              $this->dao->update(TABLE_REVIEWISSUE)
                ->set('deleted')->eq('1')
                ->where('id')->eq($issueID)->exec();
            //$issueInfo = $this->loadModel('reviewproblem')->getByID($issueID);
            $issueInfo = $this->dao->select('title')->from(TABLE_REVIEWISSUE)->where('id')->eq($issueID)->fetch();
            $this->loadModel('action')->create('reviewmeeting', $meetingId, 'deletedissue', $extra=$issueInfo->title);
            die(js::locate($this->createLink('reviewmeeting','meetingview', "reviewId=$meetingId&flag=1"), 'parent'));

        }

    }


    /**
     * Reviewing edit issue.
     *
     * @param  int    $projectID
     * @param  int    $issueID
     * @access public
     * @return void
     */
    public function editissue($meetingId = 0, $projectID=0, $issueID = 0,$source = 'list',$reviewID = 0)
    {
        $review = $this->loadModel('review')->getByID($reviewID);

        $this->loadModel('project')->setMenu($projectID);
        if($_POST)
        {
            $changes = $this->reviewmeeting->editissue($issueID);
            if($changes)
            {
                $issueInfo = $this->loadModel('reviewproblem')->getByID($issueID);
                $this->loadModel('action')->create('reviewmeeting', $meetingId, 'editissue', $extra=$issueInfo->reviewTitle);
            }
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

            if($source == 'detail') $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'parent'));
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess,'locate' => 'parent'));
        }
        $issue = $this->loadModel('reviewissue')->getByID($issueID);
        $this->view->title       = $this->lang->reviewissue->edit;
        $this->view->issue       = $issue;
        $this->view->review       = $review;
        $this->view->stages      = $this->reviewissue->getReviewStage($issue->review);
        $this->view->reviewPairs = $this->reviewissue->getReviewPairs($projectID);
        $this->view->users       = $this->loadModel('user')->getPairs('noclosed');
        $this->display();
    }

    /**
     *  会议评审
     * @param string $status
     * @param string $orderBy
     * @param int $recTotal
     * @param int $recPerPage
     * @param int $pageID
     */
    public function meetingReview($status = 'all',$param = 0,$orderBy = 'meetingPlanTime_desc', $recTotal = 0, $recPerPage = 15, $pageID = 1){
        $this->view->title        = $this->lang->reviewmeeting->meetingreview ;
        $this->app->loadLang('review');
        $this->loadModel('datatable');
        $status = strtolower($status);
        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = pager::init($recTotal, $recPerPage, $pageID);

        $this->view->meetCount = $this->reviewmeeting->meetCount();

        $this->config->reviewmeet->search['params']['status']['values']  =  $this->lang->reviewmeet->statusLabelList ;

        $depts           = array('0'=>'') + $this->loadModel('dept')->getOptionMenu();
        $this->config->reviewmeet->search['params']['createdDept']['values']  = $depts;
        $users = $this->loadModel('user')->getPairs('noclosed|noletter');;
        $outsideList1 = $this->loadModel('user')->getUsersNameByType('outsideExpertType');
        $outsideList2 = $this->loadModel('user')->getUsersNameByType('outside');
        $users = array_merge($users, $outsideList1, $outsideList2);
        $this->view->users = $users;

        $this->view->outsideList1 = array(''=>'') + $outsideList1;
        $this->view->outsideList2 = array(''=>'') + $outsideList2;
        $this->view->users = $users;
        $this->config->reviewmeet->search['params']['reviewedBy']['values']  = $this->view->outsideList1;
        $this->config->reviewmeet->search['params']['outside']['values']  =  $this->view->outsideList2;
        $this->config->reviewmeet->search['params']['meetingPlanExport']['values']  =  $users;

        /* By search. */
        $queryID = ($status == 'bysearch')  ? (int)$param : 0;
        $actionURL = $this->createLink('reviewmeeting', 'meetingReview', "$status=bySearch&param=myQueryID");
        $this->reviewmeeting->buildSearchForm($queryID, $actionURL);
        /* 设置详情页面返回的url连接。*/
        $this->session->set('reviewmeetingList', $this->app->getURI(true), 'meetingReview');

        $reviewList = $this->reviewmeeting->meetList( $status, $queryID, $orderBy, $pager);

        $manageClass = new stdClass();
        $manageClass->meetingCode = $this->lang->reviewmeeting->typeList['manage'];
        $manageClass->isCode = false;
        $manageList = array($manageClass);
        $proClass = new stdClass();
        $proClass->isCode = false;
        $proClass->meetingCode = $this->lang->reviewmeeting->typeList['pro'];
        $proList = array($proClass);
        $pmoClass = new stdClass();
        $pmoClass->meetingCode = $this->lang->reviewmeeting->typeList['pmo'];
        $pmoClass->isCode = false;
        $pmoList = array($pmoClass);
        $deptClass = new stdClass();
        $deptClass->meetingCode = $this->lang->reviewmeeting->typeList['dept'];
        $deptClass->isCode = false;
        $deptList = array($deptClass);
        $cbpClass = new stdClass();
        $cbpClass->meetingCode = $this->lang->reviewmeeting->typeList['cbp'];
        $cbpClass->isCode = false;
        $cbpList = array($deptClass);

        foreach ($reviewList as $review){
            $review->isCode = true;
            if($review->type == 'manage'){
                array_push($manageList, $review);
            }else if($review->type == 'pro'){
                array_push($proList, $review);
            }else if($review->type == 'pmo'){
                array_push($pmoList, $review);
            }else if($review->type == 'dept'){
                array_push($deptList, $review);
            }else if($review->type == 'cbp'){
                array_push($cbpList, $review);
            }
        }
        $allReviewList = array();
        if(count($manageList) > 1){
            foreach ($manageList as $manage){
                array_push($allReviewList, $manage);
            }
        }
        if(count($proList) > 1){
            foreach ($proList as $pro){
                array_push($allReviewList, $pro);
            }
        }
        if(count($pmoList) > 1){
            foreach ($pmoList as $pmo){
                array_push($allReviewList, $pmo);
            }
        }
        if(count($deptList) > 1){
            foreach ($deptList as $dept){
                array_push($allReviewList, $dept);
            }
        }
        if(count($cbpList) > 1){
            foreach ($cbpList as $cbp){
                array_push($allReviewList, $cbp);
            }
        }

        $this->view->reviewList = $allReviewList;

        $this->view->status     = $status;
        $this->view->pager      = $pager;
        $this->view->recTotal   = $recTotal;
        $this->view->recPerPage = $recPerPage;
        $this->view->pageID     = $pageID;
        $this->view->orderBy    = $orderBy;
        $this->view->queryID    = $queryID;
        $this->view->products   = $this->loadModel('product')->getPairs(0);
        $this->session->set('common_back_url', $this->app->getURI(true),'backlog');
        $this->view->title      = $this->lang->reviewmeeting->meetingreview ;
        $this->display();
    }
   //会议日程
    public function suremeeting($status = 'suremeet',$year = ''){
        $this->view->meetCount = $this->reviewmeeting->meetCount();
        $this->view->title      = $this->lang->reviewmeeting->suremeeting ;
        $this->view->status     = $status;
        $this->display();
    }
    public function ajaxGetCounts(){
        $allCounts = $this->reviewmeeting->meetCount();
        $newConuts = array();
        $newConuts['all'] = count($this->reviewmeeting->meetList( 'wait', 0, '', 0));
        $newConuts['suremeet']  = $this->loadModel('reviewmanage')->meetingCount()['waitjoin'];
        $newConuts['wait']= count($this->reviewmeeting->noMeetList('all',0 ,'',0)) ;
        // $newConuts['wait']  =$this->reviewmeeting->meetCount()['wait'];

        echo json_encode($newConuts);

    }

    /**
     * 未排会议
     * @param string $status
     * @param int $param
     * @param string $orderBy
     * @param int $recTotal
     * @param int $recPerPage
     * @param int $pageID
     */
    public function nomeet($status = 'all',$param = 0, $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 15, $pageID = 1){
        $this->app->loadLang('review');
        $this->loadModel('datatable');
        $status = strtolower($status);
        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = pager::init($recTotal, $recPerPage, $pageID);

        $users = $this->loadModel('user')->getPairs('noclosed|noletter');;
        $outsideList1 = $this->loadModel('user')->getUsersNameByType('outsideExpertType');
        $outsideList2 = $this->loadModel('user')->getUsersNameByType('outside');
        $users = array_merge($users, $outsideList1, $outsideList2);
        $this->view->users = $users;

        $this->view->outsideList1 = array(''=>'') + $outsideList1;
        $this->view->outsideList2 = array(''=>'') + $outsideList2;
        $this->config->reviewnomeet->search['params']['reviewedBy']['values']  = $this->view->outsideList1;
        $this->config->reviewnomeet->search['params']['outside']['values']  =  $this->view->outsideList2;
        $this->config->reviewnomeet->search['params']['meetingPlanExport']['values']  =  $users;
        $depts           = array('0'=>'') + $this->loadModel('dept')->getOptionMenu();
        $this->config->reviewnomeet->search['params']['createdDept']['values']  = $depts;
        //项目类型
        $this->app->loadLang('projectplan');
        $projectTypeList = $this->lang->projectplan->typeList;
        $this->config->reviewnomeet->search['params']['projectType']['values'] = $projectTypeList;
        /* By search. */
        $queryID = ($status == 'bysearch')  ? (int)$param : 0;
        $actionURL = $this->createLink('reviewmeeting', 'nomeet', "$status=bySearch&param=myQueryID");
        $this->reviewmeeting->buildSearchFormNo($queryID, $actionURL);

        /* 设置详情页面返回的url连接。*/
        $this->session->set('nomeetList', $this->app->getURI(true), 'nomeet');

        $this->view->reviewList = $this->reviewmeeting->noMeetList($status,$queryID ,$orderBy, $pager);
        $this->view->status     = $status;
        $this->view->pager      = $pager;
        $this->view->recTotal   = $recTotal;
        $this->view->recPerPage = $recPerPage;
        $this->view->pageID     = $pageID;
        $this->view->orderBy    = $orderBy;
        $this->view->queryID    = $queryID;
        $this->view->products   = $this->loadModel('product')->getPairs(0);
        $this->view->title      = $this->lang->reviewmeeting->nomeet ;
        $this->display();
    }

    /**
     * Ajax get meet list.
     *
     * @param  string $year
     * @access public
     * @return void
     */
    public function ajaxGetMeetList($year = '')
    {
        die($this->reviewmeeting->getMeetCalendar($year));
    }

    /**
     * Desc: 批量新建
     * Date: 2022/7/28
     * Time: 9:58
     *
     * @param $projectID
     * @param int $reviewID
     * @param string $source
     * @param string $statusNew
     * @param string $orderBy
     * @param int $recTotal
     * @param int $recPerPage
     * @param int $pageID
     *
     */
    public function batchCreate($meetingID, $source = 'reviewmeeting',$statusNew='all', $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 10, $pageID = 1)
    {

        $meetingInfo = $this->reviewmeeting->getMeetingById($meetingID);
        $reviewInfos = $this->reviewmeeting->getReviewBatchCreatess($meetingInfo->meetingCode);

            if(!empty($_POST))
            {
                $this->reviewmeeting->batchCreate($meetingInfo->meetingCode);
                $this->loadModel('action')->create('reviewmeeting', $meetingID, 'createissues');
                if($source == 'reviewmeeting') die(js::locate($this->createLink('reviewmeeting','meetingview', "reviewId=$meetingID&flag=1"), 'parent'));


                die(js::locate($this->createLink('reviewmeeting','meetingview', "reviewId=$meetingID&flag=1"), 'parent'));
            }

        //处理提出阶段
        $showFields = [];
        $emptyArr = array('0'=>'');
        $ditto = array('ditto'=>$this->lang->reviewmeeting->ditto);
        $typeList = $this->lang->reviewmeeting->reviewTypeList;
        $meetingCodeList  = $this->loadModel('reviewissue')->getMeetingCodeList();

        //todo
        $reviewPairs = array();


        $this->view->meetingCodeList = $emptyArr + $meetingCodeList;
        $this->view->typeList = $ditto + $typeList;
        $this->view->typeListNoDitto =$typeList;
        $this->view->showFields   = join(',', $showFields);
        $this->view->title       = $this->lang->reviewmeeting->createIssue;
        $this->view->reviewPairsNoditto = $emptyArr + $reviewPairs;
        $this->view->meetingCode =$meetingInfo->meetingCode;
        $this->view->meetingCodeList = $emptyArr + $meetingCodeList;
        $this->view->users     = $this->loadModel('user')->getPairs('noclosed');

        $this->view->reviewInfos = $emptyArr+$reviewInfos;
        $this->display();
    }


    /**
     * View a review.
     *
     * @param  int  $reviewID
     * @access public
     * @return void
     */
    public function reviewview($reviewID, $flag = 0)
    {
        $this->loadModel('review');
        $this->app->loadLang('review');
        $review = $this->review->getByID($reviewID);
        //$this->commonAction($review->project);

        $users = $this->loadModel('user')->getPairs('noletter');
        $outsideList1 = $this->loadModel('user')->getUsersNameByType('outsideExpertType');
        $outsideList2 = $this->loadModel('user')->getUsersNameByType('outside');
        $users = array_merge($users, $outsideList1, $outsideList2);
        $this->view->outsideList1 = $outsideList1;
        $this->view->outsideList2 =  $outsideList2;
        $this->view->users = $users;

        $dataTrial = $this->review->getTrial($reviewID, $review->version, $users, 2);
        $review->trialDept = $dataTrial['deptid'];
        $review->trialDeptLiasisonOfficer = $dataTrial['deptjkr'];
        $review->trialAdjudicatingOfficer = $dataTrial['deptzs'];
        $review->trialJoinOfficer = $dataTrial['deptjoin'];

        //查询允许编辑审核人员的节点
        $review->allowEditNodes = $this->review->getAllowEditNodes('review', $reviewID, $review->version);

        $this->app->loadLang('reviewissue');
        $stakeholder = $this->loadModel('stakeholder')->getStakeholders($review->project, 'outside');
        $stakeList = array();
        foreach ($stakeholder as $s) {
            $stakeList[$s->user] = $s->companyName . '/' . $s->name;
        }

        //有打基线节点，关闭的结果展示
        $reviewNodeReviewerList = $this->review->getReviewNodeFormatReviewerList($reviewID);
        $closeType = 'nopass';
        if (in_array('baseline', array_keys($reviewNodeReviewerList))) {
            $closeType = 'pass';
        }
        //会议评审
        if ($review->meetingCode) {
            $meetingInfo = $this->loadModel('reviewmeeting')->getMeetingByMeetingCode($review->meetingCode);
            $meetingDetailInfo = $this->review->getMeetingDetailInfo($review->meetingCode, $reviewID);
            $meetingDetailInfo = $this->loadModel('file')->replaceImgURL($meetingDetailInfo, 'meetingContent,meetingSummary');
            if($meetingInfo->meetingSummaryCode){
                $meetingSummary = $meetingDetailInfo->meetingSummary;
                if(!$meetingSummary){
                    $meetingSummaryArray = $this->loadModel('reviewmeeting')->getMeetingSummaryListByReviewId($reviewID, $meetingInfo->owner);
                    $meetingDetailInfo->meetingSummaryArray = $meetingSummaryArray;
                }
            }
            $review->meetingInfo = $meetingInfo;
            $review->meetingDetailInfo = $meetingDetailInfo;
        }
        //评审归档信息
        $archiveList = $this->loadModel('archive')->getMaxVersionArchiveList('review', $reviewID);
        //评审打基线信息
        $baseLineList = $this->review->getBaseLineInfo($review);
        if(!empty($baseLineList)){
            $this->app->loadLang('cm');
            $this->view->baseLineTypelist = $this->lang->cm->typeList;
        }
        $this->view->archiveList = $archiveList;
        $this->view->baseLineList = $baseLineList;

        $this->view->title = $this->lang->review->view;
        $this->view->position[] = $this->lang->review->view;
        $this->view->review = $review;
        $this->view->actions = $this->loadModel('action')->getList('review', $reviewID);
        $this->view->companies   = $this->loadModel('company')->getOutsideCompanies();


        $this->view->relatedUsers = $this->loadModel('user')->getPairs('noletter');
        $this->view->deptMap = $this->loadModel('dept')->getOptionMenu();
        $this->view->issueList = $this->review->getReviewIssue($review->project, $reviewID);
        $this->view->gradeList = $this->review->getReviewAllGradeList();
        $this->view->reviewNodeReviewerList = $reviewNodeReviewerList;
        $this->view->closeType = $closeType;
        $this->view->allstatus = $this->lang->review->statusLabelList + $this->lang->review->statusFile;
        $this->app->loadLang('projectplan');
        $this->view->flag = $flag;
        //是否可以操作附件
        $isAllowOperateFile = $this->review->isAllowOperateFile($review);
        $this->view->isAllowOperateFile = $isAllowOperateFile;
        $this->display();
    }

    /**
     * 设置会议排期
     * @param string $ids
     */
    /**
     * 设置会议排期
     * @param string $ids
     */
    public function setmeeting($ids,$types = 0){
        if ($_POST){
            $res = $this->reviewmeeting->setmeetingNew();
            if (!$res['result']){
                $this->send($res);
                exit;
            }
            if(!dao::isError()){
                $response['result']  = 'success';
                $response['message'] = $this->lang->saveSuccess;
                $response['locate']  = 'parent';
                $this->send($response);
            }
            $response['result']  = 'fail';
            $response['message'] = dao::getError();
            $this->send($response);
        }
        $this->app->loadLang("review");
        $ids = rtrim($ids,',');

        $data = $this->dao->select("id,title,type, status,owner,expert,outside,reviewedBy,reviewer")->from(TABLE_REVIEW)->where("id")->in($ids)->fetchall();
        $type = [];
        $expert = "";
        $owner = "";
        $notAllowSetMeetingIds = [];
        foreach ($data as $k => $v) {
            $type[] = $v->type;
            $expert .= $v->expert.','.$v->outside.','.$v->reviewedBy.',';
//            $owner .= $v->owner.',';
            $owner = $v->owner;
            $reviewer = $v->reviewer;
            if(!in_array($v->status, $this->lang->review->allowBindMeetingLastStatusList)){
                $notAllowSetMeetingIds[] = $v->id;
            }
        }

        if(!empty($notAllowSetMeetingIds)){
            echo '评审id'.implode(',', $notAllowSetMeetingIds) .'当前状态不允许排期，请重新选择';
            exit;
        }

        if ($types == 2){
            a($expert);exit;
        }
        $this->view->expert = implode(',',array_unique(explode(',',trim($expert,','))));
        $this->view->owner  = implode(',',array_unique(explode(',',trim($owner,','))));
        $this->view->reviewer = $reviewer;
        $type_length = count(array_unique($type));

        $users = $this->loadModel('user')->getPairs('noletter');
        $outsideList1 = $this->loadModel('user')->getUsersNameByType('outsideExpertType');
        $outsideList2 = $this->loadModel('user')->getUsersNameByType('outside');
        $users = array_merge($users, $outsideList1, $outsideList2);
        $this->view->users = $users;
        if ($types == 1 && $type_length > 1){
            echo "fail";
            exit;
        }
        $this->view->ids = $ids;
        $this->view->reviewList = $data;
        $this->display();
    }

    /**
     *编辑会议评审
     *
     * @param $meetingID
     */
    function edit($meetingID){
        if($_POST) {
            $changes = $this->reviewmeeting->update($meetingID);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes || $this->post->comment)
            {
                $actionID = $this->loadModel('action')->create('reviewmeeting', $meetingID, 'edited', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }
        //会议信息
        $meetingInfo = $this->reviewmeeting->getMeetingById($meetingID);
        //是否允许审批
        $checkRes = $this->reviewmeeting->checkIsAllowEdit($meetingInfo, $this->app->user->account);
        if($checkRes['result']){ //
            $users = $this->loadModel('user')->getPairs('noletter');
            $outsideList1 = $this->loadModel('user')->getUsersNameByType('outsideExpertType');
            $outsideList2 = $this->loadModel('user')->getUsersNameByType('outside');
            $users = array_merge($users, $outsideList1, $outsideList2);
            $bindReviewList = $this->loadModel('review')->getReviewListByMeetingCode($meetingInfo->meetingCode, 'id, title');
            $allowBindReviewList = $this->loadModel('review')->getAllowBindReviewListByType($meetingInfo->type);
            $reviewIds  = array_column($bindReviewList, 'id');
            $this->view->reviewIds  = $reviewIds;
            $this->view->reviewList = $allowBindReviewList;
            $this->view->users  = $users;
            //判断是否允许修改评审专员
            $isAllowEditReviewer = $this->reviewmeeting->getIsAllowEditReviewer($meetingInfo->status);
            $this->view->isAllowEditReviewer = $isAllowEditReviewer;
        }
        $this->view->meetingInfo  = $meetingInfo;
        $this->view->checkRes     = $checkRes;
        $this->display();
    }


    /**
     * 批量打包压缩下载
     * @param $meetingId
     */
    public function downloadfiles($meetingId,$mouse = '')
    {
        $filesIds = $this->reviewmeeting->getReviewFiles($meetingId);
        $meetingInfo = $this->reviewmeeting->getMeetingById($meetingId);
        $fileArr = array();
        $newPath = "/tmp";
        $date = date('Y-m-d ',time());
        $fileNameZip = $newPath."/".$meetingInfo->meetingCode . '_'. '评审材料' . '_'. $date.'.zip';
        $name = $meetingInfo->meetingCode . '_'. '评审材料' . '_'. $date.'.zip';

        $id = 0;
        foreach ($filesIds as $filesId) {
            $id = $id +1;
            $fileID = $filesId->id;
            $file = $this->loadModel('file')->getById($fileID);
            $realPath = $file->realPath;
            $title = $file->title;
            $fileArr[] = array('file_path' => $realPath, 'down_path' => $id.$title);

        }
        $zip = new ZipArchive();
        if ($zip->open($fileNameZip, ZIPARCHIVE::CREATE) !== TRUE) {
            die();
        }
        foreach ($fileArr as $value){
            $zip->addFile ( $value ['file_path'], $value ['down_path'] );
        }
        $this->loadModel('action')->create('reviewmeeting', $meetingId, 'downloadfiles',$extra = $name);
        $zip->close ();
        header ( "Content-Type: application/zip" );
        header ( "Content-Transfer-Encoding: Binary" );
        header ( "Content-Length: " . filesize ( $fileNameZip ) );
        header ( "Content-Disposition: attachment; filename=\"" . basename ( $fileNameZip ) . "\"" );
        readfile ( $fileNameZip );
        //如不删除，则在服务器上会有 $zipname 这个zip文件
        unlink ( $fileNameZip );
        die();
    }



    /**
     * @param $ids 多条评审ID ,拼接
     * 判断用户是否可以设置排期
     */
    public function ajaxcheckSetmeeting($ids){
        $ids = rtrim($ids,',');
        $data = $this->dao->select("title,type,owner,expert,outside,reviewedBy,status,reviewer")->from(TABLE_REVIEW)->where("id")->in($ids)->fetchall();
        $res = 1;
        $account = $this->app->user->account;
        foreach ($data as $v) {
            if (!in_array($v->status,$this->lang->reviewmeeting->allowBindStatusArrayNew)){
                $res = 0;
                continue;
            }
            if (!in_array($account,explode(',',rtrim($v->owner,','))) && !in_array($account,explode(',',rtrim($v->reviewer,',')))){
                $res = 0;
                continue;
            }
        }
        echo $res;
        exit;
    }

    /**
     * review a review 审核.
     *
     * @param  int  $reviewID
     * @param sting $nodeId
     * @access public
     * @return void
     */
    public function editfiles($reviewID)
    {
        $this->loadModel('review');
        $this->view->title      = $this->lang->review->editNodeUsers;
        $this->view->position[] = $this->lang->review->editNodeUsers;
        $review = $this->review->getByID($reviewID);
        if($_POST){
            $changes =  $this->review->editFilesByID($reviewID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            if($changes or $this->post->currentComment)
            {

                $actionID = $this->loadModel('action')->create('review', $reviewID, 'renewfile', $this->post->currentComment);
                $this->action->logHistory($actionID, $changes);

            }
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }

        $this->view->review     = $review;
        $this->display();
    }

    /**
     * review a review 审核.
     *
     * @param  int  $reviewID
     * @param sting $nodeId
     * @access public
     * @return void
     */
    public function editNodeUsers($reviewID, $nodeId){
        $this->loadModel('review');
        $this->app->loadLang('review');
        if($_POST) {
            $logChanges = $this->review->updateReviewNodeUsers($reviewID);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $actionID = $this->loadModel('action')->create('review', $reviewID, 'editReviewNodeUsers', $this->post->comment);
            if($logChanges) {
                $this->action->logHistory($actionID, $logChanges);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }
        $this->view->title      = $this->lang->review->editNodeUsers;
        $this->view->position[] = $this->lang->review->editNodeUsers;
        $reviewInfo = $this->review->getByID($reviewID);

        //是否允许编辑审核节点用户信息
        $checkRes = $this->review->checkReviewNodeIsAllowEdit($reviewInfo, $nodeId);
        if($checkRes['result']){
            $data = $checkRes['data'];
            $reviewNode = $data->reviewNode;
            $reviewNode->statusStageName    = zget($this->lang->review->nodeCodeNameList, $reviewNode->nodeCode);
            $this->view->reviewNode         = $reviewNode;
            $this->view->allowEditReviewers = $data->allowEditReviewers;
            $users = $this->loadModel('user')->getPairs('noletter');
            if($reviewNode->nodeCode == 'formalReview'){ //专家评审
                $outsideList1 = $this->loadModel('user')->getUsersNameByType('outsideExpertType');
                //$outsideList2 = $this->loadModel('user')->getUsersNameByType('outside');
                $users = array_merge($users, $outsideList1);
            }
            $this->view->users  = $users;
        }
        $this->view->review     = $reviewInfo;
        $this->view->checkRes   = $checkRes;
        $this->display();
    }
    //根据会议编号获取预计参会专家
    public function ajaxGetMeetExpert(){
        $expert = '';
        if ($_POST['meetingCode']){
            $meetingInfo = $this->dao->select('meetingPlanExport,owner,reviewer')->from(TABLE_REVIEW_MEETING)->where('meetingCode')->eq($_POST['meetingCode'])->fetch();
            $expert = trim($meetingInfo->meetingPlanExport,',');
            $reviewer = $meetingInfo->reviewer;
            $owner = $meetingInfo->owner;
        }
        $data = $this->dao->select("id,title,type,owner,expert,outside,reviewedBy,reviewer")->from(TABLE_REVIEW)->where("id")->in($_POST['ids'])->fetchall();
        $expert2 = "";
        foreach ($data as $k=>$v) {
            $expert2 .= $v->expert.','.$v->outside.','.$v->reviewedBy.',';
            if (in_array($_POST['meetingPlanType'],[1,2]) && !$_POST['meetingCode'] ){
                $reviewer = $v->reviewer;
                $owner = $v->owner;
            }
        }
        $expert = $expert.','.trim($expert2, ',');
        $expert = implode(',', array_unique(explode(',', $expert)));
        $users = $this->loadModel('user')->getPairs('noletter');
        $outsideList1 = $this->loadModel('user')->getUsersNameByType('outsideExpertType');
        $outsideList2 = $this->loadModel('user')->getUsersNameByType('outside');
        $users = array_merge($users, $outsideList1, $outsideList2);
        $expert = html::select('expert[]', $users, $expert, "class='form-control chosen' multiple required");
        $reviewer = html::select("reviewer",$users,$reviewer,"class='form-control chosen' ");
        $owner = html::select("owner",$users,$owner,"class='form-control chosen' ");

        echo json_encode(['expert'=>$expert,'owner'=>$owner,'reviewer'=>$reviewer]);
    }
    //确认开会
    public function confirmmeeting ($id){
        if ($_POST){
            $res = $this->reviewmeeting->confirmmeeting($id);
            if (!$res['result']){
                $this->send($res);
                exit;
            }
            if(!dao::isError()){
                $response['result']  = 'success';
                $response['message'] = $this->lang->reviewmeeting->confirmmeetingOK;
                $response['locate']  = 'parent';
                $this->send($response);
            }
            $response['result']  = 'fail';
            $response['message'] = dao::getError();
            $this->send($response);
        }
        $this->app->loadLang('review');
        $meetingInfo = $this->reviewmeeting->getByID($id);
        $users = $this->loadModel('user')->getPairs('noletter');
        $outsideList1 = $this->loadModel('user')->getUsersNameByType('outsideExpertType');
        $outsideList2 = $this->loadModel('user')->getUsersNameByType('outside');
        $users = array_merge($users, $outsideList1, $outsideList2);
        $this->view->users = $users;
        $this->view->meetingInfo = $meetingInfo;

        $this->display();
    }
    //邮件通知评审专家
    public function notice($meetingID){
        if ($_POST){
            $res = $this->reviewmeeting->notice($meetingID);
            if (!$res['result']){
                $this->send($res);
                exit;
            }
            if(!dao::isError()){
                $response['result']  = 'success';
                $response['message'] = $this->lang->reviewmeeting->noticeSuccess;
                $response['locate']  = 'parent';
                $this->send($response);
            }
            $response['result']  = 'fail';
            $response['message'] = dao::getError();
            $this->send($response);
        }
        $users = $this->loadModel('user')->getPairs('noletter');
        $outsideList1 = $this->loadModel('user')->getUsersNameByType('outsideExpertType');
        $outsideList2 = $this->loadModel('user')->getUsersNameByType('outside');
        $users = array_merge($users, $outsideList1, $outsideList2);
        //会议详情
        $meetingInfo = $this->reviewmeeting->getByID($meetingID);
        //查询该会议是否存在邮件通知记录
        $mailInfo = $this->reviewmeeting->getNoticeMailOne($meetingID);
        if (!empty($mailInfo)){
            $this->view->mailContent = $mailInfo->content;
            $this->view->created = $mailInfo->mailto;
            $this->view->reviewer = $mailInfo->addressee;
            $this->view->mailContent = $mailInfo->content;
            $this->view->mailTitle = $mailInfo->title;

        }else{
            $meetingDetails = $this->reviewmeeting->getMeetingDetail($meetingID,'review_id');
            $reviewIds = "";
            foreach ($meetingDetails as $detail) {
                $reviewIds .= $detail->review_id.',';
            }
            $reviewIds = rtrim($reviewIds,',');
            $review = $this->reviewmeeting->getByIds($reviewIds);
            $reviewer = "";
            $created = "";
            $reviewTitle = "";
            $project = "";//项目id
            $expert = "";//评审专家
            $this->loadModel('review');
            $trialDept = "";
            foreach ($review as $k=>$v) {
                //初审部门
                $dataTrial = $this->review->getTrial($v->id, $v->version, $users, 2);
                $review[$k]->trialDept = $dataTrial['deptid'];
                $trialDept .= $dataTrial['deptid'].'、';

                $project .= $v->project.',';
                $reviewer .= $v->expert.','.$v->outside.','.$v->reviewedBy.','.$v->owner.',';
                $created .= $v->createdBy.','.$v->relatedUsers.',';
                $reviewTitle .= $v->title.'、';
                $expert .= $v->expert.','.$v->outside.','.$v->reviewedBy.',';
            }
            $trialDept = str_replace("、、",'、',trim($trialDept,'、'));
            if ($trialDept == ''){
                $trialDept = "【XXX】";
            }
            $expert = array_unique(explode(',',str_replace(",,",',',trim($expert,','))));
            $expert = array_values($expert);
            $expertStr = "";
            for ($e = 0;$e < count($expert);$e++){
                $expertStr .= $users[$expert[$e]].'，';
            }
            $expertStr = rtrim($expertStr,'，');
            $project = rtrim($project,',');
            //获取项目列表
            $projectList = $this->dao->select('id,name,PM')->from(TABLE_PROJECT)->where('id')->in($project)->fetchall();
            $reviewTitle = mb_substr($reviewTitle,0,-1,'utf-8');

            //部门领导
            $createdDeptIds = array_column($review,'createdDept');
            $deptList = $this->loadModel('dept')->getDeptListByIds($createdDeptIds, 'manager,name,id');
            $created .= implode(array_column($deptList,'manager'),',');
            $manageer = $this->dao->select('*')->from(TABLE_USER)
                ->where('deleted')->eq(0)
                ->andWhere('type')->eq('inside')
                ->andWhere("dept")->eq(3)
                ->fetchall();
            $newManage = [];
            foreach ($manageer as $v2) {
                if (!in_array($v2->realname,['禅道厂商','admin','系统演示'])){
                    $created .= $v2->account.',';
                }
            }
            $this->app->loadLang('custommail');
            $mailConf = isset($this->config->global->setNoticeMail) ? $this->config->global->setNoticeMail : '{"mailTitle":"","variables":[],"mailContent":""}';
            $mailConf = json_decode($mailConf);
            $mailTitle = vsprintf($mailConf->mailTitle, [$meetingInfo->meetingCode,$reviewTitle]);
            $week = "星期".helper::getWeek($meetingInfo->meetingPlanTime);
            $mailContent = vsprintf($mailConf->mailContent,$meetingInfo->meetingPlanTime."（".$week."）");
            $this->view->mailTitle = $mailTitle;
            $tableHead = ['序号','评审标题','项目名称','项目类型','部门','项目经理','总中心专家','成方金信专家'];

            $tableBody = [];
            foreach ($review as $rk=>$rv) {
                foreach ($projectList as $pv) {
                    if ($rv->project == $pv->id){
                        $review[$rk]->projectName = $pv->name;
                        $review[$rk]->PM = $users[$pv->PM];

                    }
                }
                foreach ($deptList as $dv) {
                    if ($rv->createdDept == $dv->id){
                        $review[$rk]->deptName = $dv->name;
                    }

                }
                $type = zget($this->lang->review->typeList, $rv->type,'');
                $tableBody[] = [$rk+1,$rv->title,$rv->projectName,$type,$rv->deptName,$rv->PM,$users[$rv->reviewedBy],$users[$rv->outside]];
            }
            //拼接邮件内容
            $htmlTagStart = '<br/><p class="p" align="justify" style="margin-left:35.25pt;text-indent:-19.5pt;text-align:justify;">';
            $htmlTagEnd = '</p>';
            $mailTable = $this->reviewmeeting->getMailtable($tableHead,$tableBody);
            $mailOwner = $htmlTagStart.'2、&nbsp;&nbsp;'.$this->lang->reviewmeeting->reviewOwner.'：'.$users[$meetingInfo->owner].$htmlTagEnd;
            $mailExpert = $htmlTagStart.'3、&nbsp;&nbsp;评审专家：'.str_replace("，，",'，',$expertStr).$htmlTagStart;
            $mailMsg = $htmlTagStart.$this->lang->reviewmeeting->noticeArray->mailCon4.$htmlTagStart;
            $mailRequire = $htmlTagStart.vsprintf($this->lang->reviewmeeting->noticeArray->mailCon5,$trialDept).$htmlTagStart;
            $mailStr = $htmlTagStart.$this->lang->reviewmeeting->noticeArray->mailCon6.$htmlTagEnd;
            $mailStr .= $htmlTagStart.$this->lang->reviewmeeting->noticeArray->mailCon7.$htmlTagEnd;
            $mailStr .= $htmlTagStart.$this->lang->reviewmeeting->noticeArray->mailCon8.$htmlTagEnd;
            $mailStr .= "<br/>".$htmlTagStart.$this->lang->reviewmeeting->noticeArray->mailCon9.$htmlTagEnd;

            $this->view->mailContent = $mailContent.$mailTable.$mailOwner.$mailExpert.$mailMsg.$mailRequire.$mailStr;

            $this->view->created = str_replace(",,",',',implode(',',array_unique(explode(',',trim($created,',')))));
            $this->view->reviewer = str_replace(",,",',',implode(',',array_unique(explode(',',trim($reviewer,',')))));
        }


        $this->view->users = $users;
        $this->view->meetingInfo = $meetingInfo;

        $this->display();
    }

    // 变更会议纪要
    public function change($meetingID){
        //会议信息
        $meetingInfo = $this->reviewmeeting->getMeetingById($meetingID);

        $reviewList = $this->loadModel('review')->getReviewListByMeetingCode($meetingInfo->meetingCode, 'id, title', $this->lang->reviewmeeting->statusList['waitMeetingOwnerReview']);
        $reviewIds = array_column($reviewList,'id');
        $versions = $this->dao->select('id,version')->from(TABLE_REVIEW)->where('id')->in($reviewIds)->orderBy('id_desc')->fetchPairs();

        if ($_POST){
            $logChanges = $this->reviewmeeting->change($meetingID, $versions);
            if(!dao::isError()){
                $actionID = $this->loadModel('action')->create('reviewmeeting', $meetingID, 'changemeetingsummary', $this->post->comment);
                if($logChanges) {
                    $this->action->logHistory($actionID, $logChanges);
                }
                $response['result']  = 'success';
                $response['message'] = $this->lang->reviewmeeting->changeSuccess;
                $response['locate']  = 'parent';
                $this->send($response);
            }
            $response['result']  = 'fail';
            $response['message'] = dao::getError();
            $this->send($response);
        }

        $users = $this->loadModel('user')->getPairs('noletter');

        foreach($reviewList as $review){
            $commonList[$review->id] = $this->loadModel('review')->getReviewResultList($review->id, $versions[$review->id], 'meetingReview', 'pass', 'comment');
        }

        $outsideList1 = $this->loadModel('user')->getUsersNameByType('outsideExpertType');
        $outsideList2 = $this->loadModel('user')->getUsersNameByType('outside');
        $users = array_merge($users, $outsideList1, $outsideList2);

        // 返回数据
        $this->view->users = $users;
        $this->view->reviewList   = $reviewList;
        $this->view->meetingInfo  = $meetingInfo;
        $this->view->commonList   = $commonList;
        $this->display();
    }

    function ajaxChange($meetingID){
        //会议信息
        $meetingInfo = $this->reviewmeeting->getMeetingById($meetingID);

        $reviewList = $this->loadModel('review')->getReviewListByMeetingCode($meetingInfo->meetingCode, 'id, title', $this->lang->reviewmeeting->statusList['waitMeetingOwnerReview']);
        $reviewIds = array_column($reviewList,'id');
        $versions = $this->dao->select('id,version')->from(TABLE_REVIEW)->where('id')->in($reviewIds)->orderBy('id_desc')->fetchPairs();

        if ($_POST){
            $logChanges = $this->reviewmeeting->change($meetingID, $versions);
            if(!dao::isError()){
                $actionID = $this->loadModel('action')->create('reviewmeeting', $meetingID, 'changemeetingsummary', $this->post->comment);
                if($logChanges) {
                    $this->action->logHistory($actionID, $logChanges);
                }
                $response['result']  = 'success';
                $response['message'] = $this->lang->reviewmeeting->changeSuccess;
                $response['locate']  = 'parent';
                $this->send($response);
            }
            $response['result']  = 'fail';
            $response['message'] = dao::getError();
            $this->send($response);
        }

        $users = $this->loadModel('user')->getPairs('noletter');

        foreach($reviewList as $review){
            $commonList[$review->id] = $this->loadModel('review')->getReviewResultList($review->id, $versions[$review->id], 'meetingReview', 'pass', 'comment');
        }

        $outsideList1 = $this->loadModel('user')->getUsersNameByType('outsideExpertType');
        $outsideList2 = $this->loadModel('user')->getUsersNameByType('outside');
        $users = array_merge($users, $outsideList1, $outsideList2);

        // 返回数据
        $this->view->users = $users;
        $this->view->reviewList   = $reviewList;
        $this->view->meetingInfo  = $meetingInfo;
        $this->view->commonList   = $commonList;
        $this->display();
    }
}
