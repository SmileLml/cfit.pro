<?php

class reviewmanage extends control
{
    /**
     * 仪表盘
     */
    public function board($status = 'waitFormalReview',$browseType = 'all', $param = 0, $orderBy = 't1.id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1){

        $this->view->title        = $this->lang->reviewmanage->board ;
        $this->app->loadLang('review');
        $this->loadModel('datatable');
        $this->loadModel('reviewproblem');
        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $recTotal1 = 0;$recPerPage1 = 20; $pageID1 = 1;
        $recTotal2 = 0;$recPerPage2 = 20; $pageID2 = 1;
        $recTotal3 = 0;$recPerPage3 = 20; $pageID3 = 1;
        $recTotal4 = 0;$recPerPage4 = 20; $pageID4 = 4;
        $recTotal5 = 0;$recPerPage5 = 20; $pageID5 = 1;
        $pager = pager::init($recTotal, $recPerPage, $pageID);
        $pager1 = pager::init($recTotal1, $recPerPage1, $pageID1);
        $pager2 = pager::init($recTotal2, $recPerPage2, $pageID2);
        $pager3 = pager::init($recTotal3, $recPerPage3, $pageID3);
        $pager4 = pager::init($recTotal4, $recPerPage4, $pageID4);
        $pager5 = pager::init($recTotal5, $recPerPage5, $pageID5);

        $this->view->meetCount = $this->reviewmanage->meetingCount();

        $users = $this->loadModel('user')->getPairs('noclosed|noletter');
        $outsideList1 = $this->loadModel('user')->getUsersNameByType('outsideExpertType');
        $outsideList2 = $this->loadModel('user')->getUsersNameByType('outside');
        $users = array_merge($users, $outsideList1, $outsideList2);
        $this->view->users = $users;
        $this->view->reviewList = $this->reviewmanage->reviewBoardList($status,$browseType, $orderBy, $pager);
        $this->view->reviewList1 = $this->reviewmanage->reviewList('wait',$browseType, $orderBy, $pager);
        $this->view->reviewList2 = $reviewList2 = $this->reviewmanage->reviewBoardList('waitMeetingReview',$browseType, $orderBy, $pager2);
        $this->view->reviewList3 = $reviewList3 = $this->reviewmanage->reviewBoardList('waitjoin',$browseType, $orderBy, $pager3);
        $this->view->reviewList4 = $reviewList4 = $this->reviewproblem->getList($projectID = 0, $reviewID = 0, 'myNoclose', $param = 0, $orderBy = "id_desc", $pager4);
        $this->view->reviewList5 = $reviewList5 = $this->loadModel('reviewqz')->reviewList('wait', '0',$orderBy , $pager5);

        // 数据少于等于3条时 列表高度显示最低高度
        $this->view->maxHeight1 = '400px';
        $this->view->maxHeight2 = count($reviewList2) > 3?'400px':'200px';
        $this->view->maxHeight3 = count($reviewList3) > 3?'400px':'200px';
        $this->view->maxHeight4 = count($reviewList4) > 3?'400px':'200px';
        $this->view->maxHeight5 = count($reviewList5) > 3?'400px':'200px';

        $this->view->status     = $status;
        $this->view->pager      = $pager;
        $this->view->recTotal   = $recTotal;
        $this->view->recPerPage = $recPerPage;
        $this->view->pageID     = $pageID;
        $this->view->pager1      = $pager1;
        $this->view->recTotal1   = $recTotal1;
        $this->view->recPerPage1 = $recPerPage1;
        $this->view->pageID1     = $pageID1;
        $this->view->pager2      = $pager2;
        $this->view->recTotal2   = $recTotal2;
        $this->view->recPerPage2 = $recPerPage2;
        $this->view->pageID2     = $pageID2;
        $this->view->pager3      = $pager3;
        $this->view->recTotal3   = $recTotal3;
        $this->view->recPerPage3 = $recPerPage3;
        $this->view->pageID3     = $pageID3;
        $this->view->pager4      = $pager4;
        $this->view->recTotal4   = $recTotal4;
        $this->view->recPerPage4 = $recPerPage4;
        $this->view->pageID4     = $pageID4;
        $this->view->orderBy    = $orderBy;
        $this->view->products   = $this->loadModel('product')->getPairs(0);
        $this->session->set('reviewmeetingList', $this->app->getURI(true),'board');
        $this->session->set('reviewmanageList', $this->app->getURI(true),'board');
        $this->session->set('common_back_url', $this->app->getURI(true),'backlog');
        $this->session->set('reviewproblemList', $this->app->getURI(true), 'backlog');
        $this->session->set('reviewqzList', $this->app->getURI(true), 'backlog');
        $this->display();
    }

    /**
     * 评审列表
     * @param string $status
     * @param string $orderBy
     * @param int $recTotal
     * @param int $recPerPage
     * @param int $pageID
     */
    public function browse($status = 'noclose',$param = 0,$orderBy = 'id_desc', $recTotal = 0, $recPerPage = 15, $pageID = 1)
    {
        $this->app->loadLang('review');
        $this->loadModel('datatable');
        $this->loadModel('review');
        $depts           = array('0'=>'') + $this->loadModel('dept')->getOptionMenu();
        $this->config->reviewmanage->search['params']['createdDept']['values']  = $depts;
        $status = strtolower($status);
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
        $this->config->reviewmanage->search['params']['status']['values']  =  array_flip($arr);

        $users = $this->loadModel('user')->getPairs('noclosed|noletter');
        $outsideList1 = $this->loadModel('user')->getUsersNameByType('outsideExpertType');
        $outsideList2 = $this->loadModel('user')->getUsersNameByType('outside');
        $users = array_merge($users, $outsideList1, $outsideList2);

        $this->view->outsideList1 = array(''=>'') + $outsideList1;
        $this->view->outsideList2 = array(''=>'') + $outsideList2;
        $this->view->users = $users;
        $this->config->reviewmanage->search['params']['reviewedBy']['values']  = $this->view->outsideList1;
        $this->config->reviewmanage->search['params']['outside']['values']  =  $this->view->outsideList2;
        $this->config->reviewmanage->search['params']['meetingPlanExport']['values']  =  $users;
        $this->config->reviewmanage->search['params']['object']['values']  = $this->lang->review->objectList;
        //项目类型
        $this->app->loadLang('projectplan');
        $projectTypeList = $this->lang->projectplan->typeList;
        $this->config->reviewmanage->search['params']['projectType']['values'] = $projectTypeList;

        /* By search. */
        $queryID = ($status == 'bysearch')  ? (int)$param : 0;
        $actionURL = $this->createLink('reviewmanage', 'browse', "$status=bySearch&param=myQueryID");
        $this->reviewmanage->buildSearchForm($queryID, $actionURL);

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = pager::init($recTotal, $recPerPage, $pageID);

        $this->view->title      = $this->lang->review->browse ;
        $this->view->position[] = $this->lang->review->browse;

        $this->view->reviewList = $this->reviewmanage->reviewList( $status, $queryID, $orderBy, $pager);


        /* 设置详情页面返回的url连接。*/
        $this->session->set('reviewmanageList', $this->app->getURI(true), 'backlog');

        $this->view->relatedUsers  = $this->loadModel('user')->getPairs('noclosed|noletter');
        $this->view->pager      = $pager;
        $this->view->recTotal   = $recTotal;
        $this->view->recPerPage = $recPerPage;
        $this->view->pageID     = $pageID;
        $this->view->orderBy    = $orderBy;
        $this->view->status     = $status;
        $this->view->queryID    = $queryID;
        $this->view->products   = $this->loadModel('product')->getPairs(0);
        $this->display();
    }
    /**
     * 评审列表
     * @param string $status
     * @param string $orderBy
     * @param int $recTotal
     * @param int $recPerPage
     * @param int $pageID
     */
    public function deptjoin($status = 'all',$param = 0,$orderBy = 'id_desc', $recTotal = 0, $recPerPage = 15, $pageID = 1)
    {
        $this->app->loadLang('review');
        $this->loadModel('datatable');
        $this->loadModel('review');
        $depts           = array('0'=>'') + $this->loadModel('dept')->getOptionMenu();
        $this->config->reviewmanage->search['params']['createdDept']['values']  = $depts;
        $status = strtolower($status);
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
        $this->config->reviewmanage->search['params']['status']['values']  =  array_flip($arr);

        $users = $this->loadModel('user')->getPairs('noclosed|noletter');
        $outsideList1 = $this->loadModel('user')->getUsersNameByType('outsideExpertType');
        $outsideList2 = $this->loadModel('user')->getUsersNameByType('outside');
        $users = array_merge($users, $outsideList1, $outsideList2);

        $this->view->outsideList1 = array(''=>'') + $outsideList1;
        $this->view->outsideList2 = array(''=>'') + $outsideList2;
        $this->view->users = $users;
        $this->view->isDeptJoin = 5;
        $this->config->reviewmanage->search['params']['reviewedBy']['values']  = $this->view->outsideList1;
        $this->config->reviewmanage->search['params']['outside']['values']  =  $this->view->outsideList2;
        $this->config->reviewmanage->search['params']['meetingPlanExport']['values']  =  $users;
        //项目类型
        $this->app->loadLang('projectplan');
        $projectTypeList = $this->lang->projectplan->typeList;
        $this->config->reviewmanage->search['params']['projectType']['values'] = $projectTypeList;

        /* By search. */
        $queryID = ($status == 'bysearch')  ? (int)$param : 0;
        $actionURL = $this->createLink('reviewmanage', 'deptjoin', "$status=bySearch&param=myQueryID");
        $this->reviewmanage->buildSearchForm($queryID, $actionURL);

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = pager::init($recTotal, $recPerPage, $pageID);

        $this->view->title      = $this->lang->review->browse ;
        $this->view->position[] = $this->lang->review->browse;

        $this->view->reviewList = $this->reviewmanage->deptJoinList($status, $queryID, $orderBy, $pager);


        /* 设置详情页面返回的url连接。*/
        $this->session->set('reviewmanageList', $this->app->getURI(true), 'backlog');

        $this->view->relatedUsers  = $this->loadModel('user')->getPairs('noclosed|noletter');
        $this->view->pager      = $pager;
        $this->view->recTotal   = $recTotal;
        $this->view->recPerPage = $recPerPage;
        $this->view->pageID     = $pageID;
        $this->view->orderBy    = $orderBy;
        $this->view->status     = $status;
        $this->view->queryID    = $queryID;
        $this->view->products   = $this->loadModel('product')->getPairs(0);
        $this->display();
    }


    /**
     * 设置会议排期
     * @param string $ids
     */
    public function setmeeting($ids=""){

        if ($_POST){
            $res = $this->reviewmanage->setmeetingNew();

            if (!$res['result']){
                $this->send($res);
            }
            if(!dao::isError()){
                $response['result']  = 'success';
                $response['message'] = $this->lang->saveSuccess;
                $this->send($response);
            }
            $response['result']  = 'fail';
            $response['message'] = dao::getError();
            $this->send($response);


        }
        $ids = rtrim($ids,',');

        $data = $this->dao->select("title,type,owner,expert")->from(TABLE_REVIEW)->where("id")->in($ids)->fetchall();
        $type = [];
        $expert = "";
        $owner = "";
        foreach ($data as $k=>$v) {
            $type[] = $v->type;
            $expert .= $v->expert.',';
//            $owner .= $v->owner.',';
            $owner = $v->owner;
        }
        $this->view->expert = implode(',',array_unique(explode(',',trim($expert,','))));
        $this->view->owner  = implode(',',array_unique(explode(',',trim($owner,','))));
        $type_length = count(array_unique($type));

        $this->view->users      = array(''=>'') + $this->loadModel('user')->getPairs('noclosed');

        if ($type_length > 1){
            die(js::alert("评审类型不一致"));
        }
        $this->view->ids = $ids;
        $this->view->reviewList = $data;
        $this->display();
    }



    /**
     * View a review.
     *
     * @param  int  $reviewID
     * @access public
     * @return void
     */
    public function view($reviewID)
    {

        $this->loadModel('review');
        $review = $this->review->getByID($reviewID);
        
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

        //由打基线节点，关闭的结果展示
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

        //项目主从关系
        $planID = $this->dao->select('id')->from(TABLE_PROJECTPLAN)->where('project')->eq($review->project)->fetch('id');
        $this->view->mainRelationInfo = $mainRelationInfo = $this->loadModel("projectplanmsrelation")->getByMainPlanID($planID);
        $this->view->slaveRelationInfo = $slaveRelationInfo = $this->loadModel("projectplanmsrelation")->getBySlavePlanID($planID);
        $this->view->relationProjectplanList = [];
        if($mainRelationInfo || $slaveRelationInfo){
            $planArr = [$planID];
            if($mainRelationInfo){
                $planArr = array_merge($planArr,explode(',',$mainRelationInfo->slavePlanID));
            }
            if($slaveRelationInfo){
                foreach ($slaveRelationInfo as $slave){
                    $planArr[] = $slave->mainPlanID;
                }
            }
            $this->view->relationProjectplanList = array_column($this->loadModel("projectplan")->getByIDMultipleList(array_unique($planArr),"id,mark"),'mark','id');
        }

        $this->view->archiveList = $archiveList;
        $this->view->baseLineList = $baseLineList;
        $this->view->typeList   = $this->lang->reviewissue->typeList;
        $this->view->statusList = $this->lang->reviewissue->statusList;
        $this->view->title = $this->lang->review->view;
        $this->view->position[] = $this->lang->review->view;
        $this->view->review = $review;
        $this->view->actions = $this->loadModel('action')->getList('review', $reviewID);

        $this->view->relatedUsers = $this->loadModel('user')->getPairs('noletter');
        $this->view->deptMap = $this->loadModel('dept')->getOptionMenu();
        $this->view->issueList = $this->review->getReviewIssue($review->project, $reviewID);
        $this->view->gradeList = $this->review->getReviewAllGradeList();
        $this->view->reviewNodeReviewerList = $reviewNodeReviewerList;
        $this->view->companies   = $this->loadModel('company')->getOutsideCompanies();
        $this->view->closeType = $closeType;
        $this->view->allstatus = $this->lang->review->statusLabelList + $this->lang->review->statusFile + $this->lang->review->statusReject;
        //为公用问题模块判断标记添加
        $flag = 1;
        $this->view->flag = $flag;
        $this->app->loadLang('projectplan');
        $isAllowOperateFile = $this->review->isAllowOperateFile($review);
        $this->view->isAllowOperateFile = $isAllowOperateFile;
        $this->display();
    }

    /**
     * View a review.
     *
     * @param  int  $reviewID
     * @access public
     * @return void
     */
    public function deptview($reviewID)
    {
        $this->loadModel('review');
        $review = $this->review->getByID($reviewID);

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

        //由打基线节点，关闭的结果展示
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
        $this->view->title = $this->lang->review->view;
        $this->view->position[] = $this->lang->review->view;
        $this->view->review = $review;
        $this->view->actions = $this->loadModel('action')->getList('review', $reviewID);

        $this->view->relatedUsers = $this->loadModel('user')->getPairs('noletter');
        $this->view->deptMap = $this->loadModel('dept')->getOptionMenu();
        $this->view->issueList = $this->review->getReviewIssue($review->project, $reviewID);
        $this->view->gradeList = $this->review->getReviewAllGradeList();
        $this->view->reviewNodeReviewerList = $reviewNodeReviewerList;
        $this->view->companies   = $this->loadModel('company')->getOutsideCompanies();
        $this->view->closeType = $closeType;
        $this->view->allstatus = $this->lang->review->statusLabelList + $this->lang->review->statusFile;
        $this->app->loadLang('projectplan');
        $this->display();

    }


    /**
     * Edit a review.
     *
     * @param  int  $reviewID
     * @access public
     * @return void
     */
    public function edit($reviewID,$flag = 0,$source = 0)
    {
        $this->app->loadLang('review');
        $this->loadModel('review');
        $review = $this->review->getByID($reviewID);
      //  $this->commonAction($review->project);

        if($_POST)
        {
            $changes = $this->review->update($reviewID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes or $this->post->comment)
            {
                $actionID = $this->loadModel('action')->create('review', $reviewID, 'Edited', $this->post->comment);
                $this->action->logHistory($actionID, $changes);

            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            if($source ==1){
                $response['locate']  =  $this->createLink('reviewmanage', 'board', "");
            }else{
                if($flag){
                    $response['locate']  = inlink('view', "project=$review->id");
                }else{
                    $response['locate']  = inlink('browse', "project=$review->project");
                }
            }


            $this->send($response);

        }

        $stakeholder = $this->loadModel('stakeholder')->getStakeholders($review->project, 'outside');
        $stakeList   = array();
        foreach($stakeholder as $s)
        {
            $stakeList[$s->user] = $s->companyName . '/' . $s->name;
        }

        //QA预审
        $depts = $this->loadModel('dept')->getByID($this->app->user->dept);
        //质量部CM
        $cmList = $this->loadModel('dept')->getRenameListByAccountStr($depts->cm);

        $this->view->title      = $this->lang->review->edit;
        $this->view->position[] = $this->lang->review->edit;
        $this->view->review     = $review;
        $this->view->project    = $this->loadModel('project')->getByID($review->project);
        $this->view->products   = $this->loadModel('product')->getPairs($review->project);
        $this->view->users      = array(''=>'') + $this->loadModel('user')->getPairs('noclosed');
        $this->view->outsideList1 =array('' => '') +$this->loadModel('user')->getUsersNameByType('outsideExpertType');
        $this->view->outsideList2 =array('' => '') +$this->loadModel('user')->getUsersNameByType('outside');
        $this->view->relatedUsers  = $this->loadModel('user')->getPairs('noclosed');
        $this->view->qapre     = $depts;
        $this->view->cmList     = array('' => '') + $cmList;
        $project = $this->dao->select('project,mark,id')->from(TABLE_PROJECTPLAN)->where('project')->in($review->project)->fetch();
        $this->view->mark = isset($project->mark) ? $project->mark : '';
        $this->view->source = $source;
        //项目主从关系
        //查询多条
        $this->view->mainRelationInfo  = $mainRelationInfo  = $this->loadModel("projectplanmsrelation")->getBySlavePlanID($project->id);
        //查询单条
        $this->view->slaveRelationInfo = $slaveRelationInfo = $this->loadModel("projectplanmsrelation")->getByMainPlanID($project->id);
        $this->view->relationProjectplanList = [];
        if($mainRelationInfo || $slaveRelationInfo){
            $planArr = [$project->id];
            if($mainRelationInfo){ //该项目是从项目，获取主项目
                foreach ($mainRelationInfo as $slave){
                    $planArr[] = $slave->mainPlanID;
                }
            }
            if($slaveRelationInfo){ //该项目是主项目，获取从项目
                $planArr = array_merge($planArr,explode(',',$slaveRelationInfo->slavePlanID));
            }

            //$relationProjectplanList =  array_column($this->loadModel('projectplan')->getByIDMultipleList(array_unique($planArr),"id,mark"),'mark','id');
            $planIds = array_flip(array_flip($planArr));
            $relationProjectplanList = $this->loadModel('projectplan')->getCodeListByPlanIds($planIds);
            $this->view->relationProjectplanList = $relationProjectplanList;
        }
        //项目承担部门
        $projectPlanInfo = $this->loadModel('projectplan')->getProjectPlanInfo($review->project, 'bearDept');
        $bearDept = $projectPlanInfo->bearDept;
        $this->view->bearDept = $bearDept;
        $this->display();
    }

    /**
     *  根据状态 设置 主席
     * @param $type
     * @param $deptId
     * @param $selectUser
     */
    /*
    public function ajaxGetOwner($type, $deptId = 0, $selectUser = '')
    {
        $this->app->loadLang('review');
        global $app;
        $users  = $this->loadModel('user')->getPairs('noclosed');
        $reviewer = '';
        if($type) {
            if ($type == 'manage') {
                $reviewer = $this->lang->review->managereviewer ;
            }else if ($type == 'pro') {
                $reviewer = $this->lang->review->proreviewer;
            }else if($type == 'pmo'){
                $reviewer = $this->lang->review->pmoreviewer;
            }
            else {
                if(!$deptId){
                    $deptId =  $app->user->dept;
                }
                $rev = $this->loadModel('dept')->getByID($deptId);
                $reviewer = $rev->manager1 ? $rev->manager1: '';
            }
        }
        if($selectUser){
            $reviewer = $selectUser;
        }
        echo html::select('owner', $users, $reviewer, " class='form-control chosen' onchange='ajaxgetmailto(this.value)' ");
    }
    */

    /**
     *  根据状态 设置 专员
     * @param $type
     * @param $deptId
     * @param $selectUser
     */
    /*
    public function ajaxGetReviewer($type, $deptId = 0, $selectUser = '')
    {
        $this->app->loadLang('review');
        global $app;
        $users      = $this->loadModel('user')->getPairs('noclosed');
        $reviewer = '';
        if($type) {
            if ($type == 'dept') {
                if(!$deptId){
                    $deptId = $app->user->dept;
                }
                $rev = $this->loadModel('dept')->getByID($deptId);
                $reviewer = $rev->reviewer ? $rev->reviewer: '';
            } else if ($type == 'manage' || $type == 'pro' || $type == 'pmo') {
                $list = substr( implode(',',$this->lang->review->reviewerList),1);
                if(strpos($list,',') !== false){
                    $reviewer = substr($list,0,strpos($list,','));
                }else{
                    $reviewer = $list;
                }

            } else {
                $reviewer = $this->lang->review->otherreviewer;
            }
        }
        if($selectUser){
            $reviewer = $selectUser;
        }
        echo html::select('reviewer', $users, $reviewer , 'class="form-control chosen" ');
    }
    */

    /**
     * 评审主席变化时，抄送人跟着一起变化
     *
     * @param $reviewId
     */
    public function ajaxgetmailto($reviewId)
    {

        $this->loadModel('review');
        $review = $this->review->getByID($reviewId);
        $users  = $this->loadModel('user')->getPairs('noclosed');
        $deptInfo = $this->loadModel('dept')->getByID( $review->createdDept);
        $mailtos = $deptInfo->manager1.','.$review->createdBy;
        $this->view->mailto = $mailtos;
        echo html::select('mailto[]', $users, $mailtos , 'class="form-control chosen" multiple');
    }

    /**
     * Submit a review.
     *
     * @param  int  $reviewID
     * @access public
     * @return void
     */
    public function submit($reviewID)
    {
        $this->loadModel('review');
        if($_POST)
        {
            $logChanges = $this->review->submit($reviewID);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $actionID = $this->loadModel('action')->create('review', $reviewID, 'applyreview', $this->post->comment);
            if($logChanges) {
                $this->action->logHistory($actionID, $logChanges);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }

        $review = $this->review->getByID($reviewID);
        $checkRes = $this->review->checkReviewIsAllowApply($review, $this->app->user->account);
        $this->view->title      = $this->lang->review->submit;
        $this->view->position[] = $this->lang->review->submit;
        if($checkRes['result']){
            $this->view->actions    = $this->loadModel('action')->getList('review', $reviewID);
            $this->view->users      = $this->loadModel('user')->getPairs('noletter');
            //下一状态
            $nextStatus  = $this->review->getSubmitNextStatus($review);
            $viewSuffix = $this->review->getSubmitViewSuffix($nextStatus);
            $this->view->view = 'submit'. $viewSuffix.'.html.php';
            if($review->status =='firstPassButEdit' and $review->type =='cbp'){
                $this->view->view = 'submit'. 'Verify'.'.html.php';
                $nextStatus = 'waitVerify';
            }
            $maxVersion =  $this->review->getReviewNodeMaxVersion($reviewID);
            if($nextStatus == $this->lang->review->statusList['waitFirstReview']){ //驳回到初审
                //获得评审验证人员默认为初审人员
                $nodeCode = 'firstReview';
                $review->firstReviewers = $this->review->getReviewersByNodeCode('review', $reviewID, $maxVersion, $nodeCode);
            }else if($nextStatus == $this->lang->review->statusList['waitVerify']){ //流转到验证材料
                //获得评审验证人员默认为验证人员
                //$nodeCode = $this->lang->review->nodeCodeList['verify'];
                $verifyReviewers = $this->review->getReviewVerifyUsers($reviewID, $maxVersion);
                if(empty($verifyReviewers)){
                    $nodeCode = 'firstReview';
                    $verifyReviewers  = $this->review->getReviewersByNodeCode('review', $reviewID, $maxVersion, $nodeCode);
                }
                if(!is_array($verifyReviewers)){
                    $verifyReviewers = explode(',', $verifyReviewers);
                }

                //提出问题还未验证的人员
                $unDealReviewIssueUsers =  $this->loadModel('reviewissue')->getUnDealIssueUsersByReviewId($reviewID);
                $type  = $review->type;
                $owner = $review->owner ;
                if(in_array($type, $this->lang->review->organizationTypeList)){ //组织级评审,去掉评审主席
                    $unDealReviewIssueUsers = array_diff($unDealReviewIssueUsers, [$owner]);
                }
                if($type == 'dept' && $unDealReviewIssueUsers){ //部门级评审，待审核人中添加提出问题还未验证的提出人
                    $verifyReviewers = array_merge($verifyReviewers, $unDealReviewIssueUsers); //待审核人中添加提出问题还未验证的提出人
                    $unDealReviewIssueUsers = [];
                }
                $review->verifyReviewers  = $verifyReviewers; //验证人
                $this->view->unDealReviewIssueUsers = $unDealReviewIssueUsers; //提出问题还未验证的提出人

                $users = $this->loadModel('user')->getPairs('noclosed');
                $otherUsersAccount = array_merge($verifyReviewers, $unDealReviewIssueUsers);
                $otherUsers = $this->loadModel('user')->getUserListByAccounts($otherUsersAccount);
                $users = $otherUsers + $users;
                $this->view->users = $users;
            }
        }
        $this->view->review     = $review;
        $this->view->checkRes   = $checkRes;
        $this->display();
    }

    /**
     *  验证人
     */
    public function ajaxGetVerifyReviewer($reviewer)
    {
        $users      = $this->loadModel('user')->getPairs('noclosed');
        $reviewer = explode(',',$reviewer);
        $res = [];
        foreach ($reviewer as $value){
            foreach ($users as $key=>$user) {
                if($key == $value){
                    $res[$key] = $user;
                    unset($users[$key]);
                }
            }
        }
        $users =array_merge($res,$users);
        // $reviewer = '';
        echo html::select('verifyReviewers[]', $users, $reviewer , 'class="form-control chosen " multiple');
    }

    /**
     * Recall a review.
     *
     * @param  int 	   $reviewID
     * @access public
     * @return void
     */
    public function recall($reviewID)
    {
        $this->loadModel('review');
        $review = $this->review->getByID($reviewID);
        $status = $review->status;
        $rejectStage = $this->review->getRecallRejectStage($status);

        //撤回的记录一下撤回状态
        $params = new stdClass();
        $params->status = 'recall';
        $params->rejectStage = $rejectStage;
        $params->dealUser    = $review->createdBy;

        $this->dao->update(TABLE_REVIEW)->data($params)->where('id')->eq($reviewID)->exec();
        //有会议号
        if($review->meetingCode){
            if(in_array($review->status, $this->lang->review->inMeetingReviewStatusList)){
                $meetingDetailInfo = $this->loadModel('reviewmeeting')->getMeetingDetailInfoByReviewId($reviewID);
                if($meetingDetailInfo){
                    $this->loadModel('reviewmeeting')->cancelBindReviewMeeting($review->meetingCode, $reviewID);
                }
            }
        }
        $this->loadModel('action')->create('review', $reviewID, 'Recall');

        die(js::reload('parent.parent'));
    }

    /**
     * review a review 审核.
     *
     * @param  int  $reviewID
     * @param sting $status
     * @access public
     * @return void
     */
    public function assign($reviewID, $status = '')
    {
        $this->loadModel('review');
        $this->app->loadLang('review');
        if($_POST) {
            $logChanges = $this->review->assign($reviewID);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $extra = '';
            $updateFields = array_column($logChanges, 'field');
            if(in_array('meetingCode', $updateFields)){
                $meetingCode = $this->review->getMeetingCodeInLogChanges($logChanges);
                if($meetingCode){
                    $extra = '绑定会议，会议单号：'.$meetingCode;
                }
            }

            $actionID = $this->loadModel('action')->create('review', $reviewID, 'assigning', $this->post->comment, $extra);
            if($logChanges) {
                $this->action->logHistory($actionID, $logChanges);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }
        $review = $this->review->getByID($reviewID);
        $isRenew = 0;
        if(!empty($review->renewTime)){
            $isRenew = 1;
        }
        $this->view->isRenew = $isRenew;

        //是否包含不需要初审的对象
        $isIncludeNotNeedFirstReviewObject = $this->review->getIsIncludeNotNeedFirstReviewObject($review->object);
        $this->view->isIncludeNotNeedFirstReviewObject = $isIncludeNotNeedFirstReviewObject;
        //是否允许指派
        $checkRes = $this->review->checkReviewIsAllowAssign($review, $this->app->user->account);

        $this->view->title      = $this->lang->review->assign;
        $this->view->position[] = $this->lang->review->assign;
        $this->view->review     = $review;
        ///会议评审主席列表
        $meetingOwnerList = [];
        if($checkRes['result']){
            $this->view->actions    = $this->loadModel('action')->getList('review', $reviewID);
            $this->view->users      = $this->loadModel('user')->getPairs('noletter');
            $this->view->outsideList1 = array('' => '') + $this->loadModel('user')->getUsersNameByType('outsideExpertType');
            $this->view->outsideList2 = array('' => '') + $this->loadModel('user')->getUsersNameByType('outside');
            $status = $review->status;
            $viewSuffix = $this->lang->review->assignViewSuffixList[$status];
            $this->view->view = 'assign'. $viewSuffix.'.html.php';
            //评审方式
            $this->view->gradeList = $this->review->getReviewLangList('gradeList', true);
            $mailto = $review->createdBy;
            $deptInfo = $this->loadModel('dept')->getByID( $review->createdDept);
            if($deptInfo){
                $mailto .= ','.$deptInfo->manager1;
            }
            if($review->status == $this->lang->review->statusList['waitFirstAssignDept']){ //待指派初审部门
                $this->view->depts = $this->loadModel('projectplan')->getTopDepts();
                //关联项目信息
                $this->app->loadLang('projectplan');
                $projectId = $review->project;
                $planSelect = 'project,isImportant, type';
                $projectPlanType = 0;
                $isImportant = 2;
                $projectPlan = $this->loadModel('projectplan')->getPlanMainInfoByProjectID($projectId, $planSelect);
                if(isset($projectPlan->project)){
                    $projectPlanType = $projectPlan->type;
                    $isImportant     =  $projectPlan->isImportant;
                }
                $defGrade = $this->review->getDefGrade($review->objects, $projectPlanType, $isImportant);
                $this->view->projectPlan = $projectPlan;
                $this->view->defGrade = $defGrade;
            }elseif ($review->status ==  $this->lang->review->statusList['waitFormalAssignReviewer']){
                $minTime = helper::today();
                $meetingOwnerList = $this->loadModel('reviewmeeting')->getAllowBindMeetingOwnerList($minTime);
                $mailto .= ','.$review->reviewer;
                $mailto .= ','.$review->relatedUsers;
            }
            $this->view->mailto = $mailto;

        }
        $this->view->checkRes = $checkRes;
        $this->view->meetingOwnerList = $meetingOwnerList;

        //项目承担部门
        $projectPlanInfo = $this->loadModel('projectplan')->getProjectPlanInfo($review->project, 'bearDept');
        $bearDept = $projectPlanInfo->bearDept;
        $this->view->bearDept = $bearDept;
        $this->display();
    }
    /**
     *  获取所有部门负责人(多人)
     *
     */
    public function ajaxgetmanagers($deptId)
    {
        $this->loadModel('review');
        $users      = $this->loadModel('user')->getPairs('noclosed');
        $manager = $this->review->getAllManager($deptId);
        echo html::select('mailto[]', $users, $manager , 'class="form-control chosen" multiple');
    }
    /**
     * review a review 审核.
     *
     * @param  int  $reviewID
     * @access public
     * @return void
     */
    public function review($reviewID)
    {
        $this->loadModel('review');
        $this->app->loadLang('review');
        if($_POST) {
            $logChanges = $this->review->review($reviewID);
            if(dao::isError()) {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            //审核记录日志去掉，已经在审核方法中记录
//            $extra = '';
//            $updateFields = array_column($logChanges, 'field');
//            if(in_array('meetingCode', $updateFields)){
//                $meetingCode = $this->review->getMeetingCodeInLogChanges($logChanges);
//                if($meetingCode){
//                    $extra = '绑定会议，会议单号：'.$meetingCode;
//                }
//            }
//            //日志扩展信息
//            $isSetHistory = true;
//            $actionID = $this->loadModel('action')->create('review', $reviewID, 'reviewed', $this->post->comment, $extra, '', true, $isSetHistory, $logChanges);

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }
        $userAccount = $this->app->user->account;
        $review = $this->review->getByID($reviewID);
        $allowReviewStatusList = $this->lang->review->allowReviewStatusList;
        $allowAssignStatusList = $this->lang->review->allowAssignStatusList;
        $allowAssignVerifyStatusList = $this->lang->review->allowAssignVerifyStatusList;
        $status = $review->status;
        if(in_array($status, $allowReviewStatusList) || in_array($status, $allowAssignStatusList)){
            $review->dealUser = $review->reviewers;
        }

        $dealUsers = $review->dealUser;
        $dealUser = explode(',', str_replace(' ', '', $dealUsers));

        //取出最后一个评审人
        //判断当前用户是否是最后一个验证人
        $this->view->lastVerifyer ='';
        if(count($dealUser) == 1){
            $this->view->lastVerifyer = 1;
        }
        //是否允许审批
        $checkRes = $this->review->checkReviewIsAllowReview($review, $this->app->user->account);
        //评审验证判断
        if($review->status == 'waitVerify' or $review->status == 'verifying' ){
            $issueCount = $this->loadModel('reviewproblem')->getReviewIssueCount2($review->id,'createAndAccept');
            if($issueCount!=0 and $this->view->lastVerifyer ==1){
                die(js::alert('该评审存在'.$issueCount.'个问题未验证，请先验证评审问题') . js::reload('parent'));
            }
        }
        $this->view->title      = $this->lang->review->review;
        $this->view->position[] = $this->lang->review->review;
        if($checkRes['result']){
            $this->view->actions    = $this->loadModel('action')->getList('review', $reviewID);
            $this->view->users      = $this->loadModel('user')->getPairs('noletter');
            $review->verifyReviewers = '';
            //获取评审方式
            $gradeList = $this->review->getReviewLangList('gradeList', true);
            //初审参与人员审核时同时判断是否时主审人员
            $isSetAdviceGrade =  $this->review->isSetAdviceGrade($review, $userAccount);
            if($isSetAdviceGrade){
                //获得建议评审方式
                $review->adviceGrade = $this->review->getReviewAdviceGrade($review->type, $review->grade);
                $adviceGradeList = $this->review->getReviewAdviceGradeList($review->adviceGrade, true);
                $this->view->adviceGradeList  = $adviceGradeList;
            }else if(in_array($review->status, $this->lang->review->allowAssignVerifyersStatusList)){ //确定评审结论
                if($review->isFirstReview == '2'){ //当跳过初审时
                    $nodeCode = 'formalReview';
                }else{//获得评审验证人员默认为初审人员
                    $nodeCode = 'firstReview';
                }
                $verifyReviewers = $this->review->getReviewersByNodeCode('review', $reviewID, $review->version, $nodeCode);
                $review->verifyReviewers = $verifyReviewers;
            }
            //待打基线
            if($review->status ==  $this->lang->review->statusList['archive']){
                $archiveList = $this->loadModel('archive')->getMaxVersionArchiveList('review', $reviewID);
                //是否需要显示安全测试和性能测试
                $isShowSafetyTest = $this->review->isShowSafetyTest($review->object, $review->type);
                $this->view->archiveList = $archiveList;
                $this->view->isShowSafetyTest = $isShowSafetyTest;
            }
            $this->view->gradeList = $gradeList;
            $this->view->isSetAdviceGrade = $isSetAdviceGrade;
        }

        //评审会主席给结论和会议评审确认评审会议纪要，查出初审人员和提出问题人员
        if($review->status == 'waitFormalOwnerReview'){
            $users = $this->loadModel('user')->getPairs('noletter');
            $verifyReviewers = $this->review->getReviewersByNodeCode('review', $reviewID, $review->version, $nodeCode);
            $this->view->preliminaryReviewer = zmget($users, $verifyReviewers);

            $issueList = $this->review->getReviewIssue($review->project, $reviewID);
            $issueUsers = array();
            foreach ($issueList as $issue){
                array_push($issueUsers, zget($users,$issue->raiseBy,''));
            }
            $this->view->questionReviewer = implode(',', array_unique($issueUsers));
        }
        $this->app->loadLang('cm');
        $this->view->typelist = array(''=>'') + $this->lang->cm->typeList;
        $this->view->review  = $review;
        $this->view->checkRes = $checkRes;
        //抄送人
        $mailto = '';
        if(in_array($review->status, $this->lang->review->allowMeetingReviewStatusList)){
            $mailto = $review->createdBy; //创建人
        }else if($review->status == 'waitFormalOwnerReview' ||  $review->status == 'waitMeetingOwnerReview'){ //会议评审结论，线上评审结论
            $mailto = $review->createdBy .','.$review->relatedUsers; //创建人,相关人员
            $rev = $this->loadModel('dept')->getByID( $review->createdDept);
            if($rev){
                $mailto .= ',' . $rev->manager1;
            }
        }
        $this->view->mailto = $mailto;
        $this->display();
    }
    /**
     * Recall a review.
     *
     * @param  int 	   $reviewID
     * @access public
     * @return void
     */
    public function close($reviewID)
    {
        $this->loadModel('review');
        if($_POST)
        {
            $logChanges = $this->review->close($reviewID); //关闭后，需要根据标志位删除临时白名单人员
            if(!dao::isError())
            {
                $actionID = $this->loadModel('action')->create('review', $reviewID, 'closed', $this->post->comment);
                if($logChanges) {
                    $this->action->logHistory($actionID, $logChanges);
                }
                $response['result']  = 'success';
                $response['message'] = $this->lang->saveSuccess;
                $response['locate']  = 'parent';
                $this->send($response);
            }
            $response['result']  = 'fail';
            $response['message'] = dao::getError();
            $this->send($response);
        }


        $users   = $this->loadModel('user')->getPairs();
        $review  = $this->review->getByID($reviewID);
        //关闭时收件人信息
        $mailUsersInfo = $this->review->getCloseMailUsersInfo($review);
        $this->view->mailUsersInfo = $mailUsersInfo;

        $this->view->actions = $this->loadModel('action')->getList('review', $reviewID);
        $this->view->review  = $review;
        $this->view->users   = $users;
        $this->view->closestatus = $this->lang->review->closeList;



        $this->display();
    }

    /**
     * 删除评审
     * @param $reviewID
     */
    public function delete($reviewID)
    {
        $this->loadModel('review');
        if(!empty($_POST))
        {
            $review = $this->review->getByID($reviewID);
            $this->dao->update(TABLE_REVIEW)->set('deleted')->eq('1')->where('id')->eq($reviewID)->exec();
            //有会议号
            if($review->meetingCode){
                if(in_array($review->status, $this->lang->review->inMeetingReviewStatusList)){
                    $meetingDetailInfo = $this->loadModel('reviewmeeting')->getMeetingDetailInfoByReviewId($reviewID);
                    if($meetingDetailInfo){
                        $this->loadModel('reviewmeeting')->cancelBindReviewMeeting($review->meetingCode, $reviewID);
                    }
                }
            }
            $this->loadModel('action')->create('review', $reviewID, 'delete', $this->post->comment);
            $reason = '1002';//代表评审加入
            $this->review->deleteWhiteList($reviewID,$reason);//删除白名单

            if(isonlybody()) die(js::closeModal('parent.parent', 'this'));
            die(js::reload('parent'));
        }

        $review = $this->review->getByID($reviewID);
        $this->view->actions = $this->loadModel('action')->getList('review', $reviewID);
        $this->view->review = $review;
        $this->view->users   = $this->loadModel('user')->getPairs();
        $this->display();
    }
    /**
     * Review report.
     *
     * @param  int  $reviewID
     * @access public
     * @return void
     */
    public function reviewreport($reviewID)
    {
        $this->loadModel('review');
        $this->app->loadLang('review');
        $review = $this->review->getByID($reviewID);
        $this->loadModel('project')->setMenu($review->project);

        $this->view->title      = $this->lang->review->submit;
        $this->view->position[] = $this->lang->review->submit;
        $this->view->review     = $review;
        $this->view->results    = $this->review->getResultByReview($reviewID);
        $this->view->issues     = $this->loadModel('reviewissue')->getIssueByReview($reviewID);
        $this->view->users      = $this->loadModel('user')->getPairs('noclosed|noletter');
        $this->view->outsideList1 =array('' => '') +$this->loadModel('user')->getUsersNameByType('outsideExpertType');
        $this->view->outsideList2 =array('' => '') +$this->loadModel('user')->getUsersNameByType('outside');
        $this->view->relatedUsers  = $this->loadModel('user')->getPairs('noclosed');
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
     * review a review 只用来做判断权限.
     *
     * @param  int  $reviewID
     * @param sting $nodeId
     * @access public
     * @return void
     */
    public function editNodeUsers($reviewID, $nodeId){
        $this->setEditNodeUsers($reviewID, $nodeId);
        $this->display();
    }
    public function ajaxEditNodeUsers($reviewID, $nodeId){
        $this->setEditNodeUsers($reviewID, $nodeId);
        $this->display();
    }

    private function setEditNodeUsers($reviewID, $nodeId){
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
    }

    // 修改评审栏内容
    public function editNodeInfos($reviewID, $nodeId){
        $this->setEditNodeInfos($reviewID, $nodeId);
        $this->display();
    }

    // 修改评审栏内容
    public function ajaxEditNodeInfos($reviewID, $nodeId){
        $this->setEditNodeInfos($reviewID, $nodeId);
        $this->display();
    }

    private function setEditNodeInfos($reviewID, $nodeId){
        $this->loadModel('review');
        $this->app->loadLang('review');
        if($_POST) {
            $logChanges = $this->review->updateReviewNodeInfos($reviewID, $nodeId);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            if(empty($logChanges))
            {
                $response['result']  = 'fail';
                $response['message'] = $this->lang->reviewmanage->noDataChange;
                $this->send($response);
            }
            if($logChanges) {
                $actionID = $this->loadModel('action')->create('review', $reviewID, 'editNodeInfos', $logChanges);
                $this->action->logHistory($actionID, $logChanges);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }
        $this->view->title      = $this->lang->review->editNodeUsers;
        $this->view->position[] = $this->lang->review->editNodeUsers;
        // 评审信息
        $reviewInfo = $this->review->getByID($reviewID);
        $users = $this->loadModel('user')->getPairs('noletter');
        $select = [];$reviewerInfos = [];$multiple = '';
        if(!empty($nodeId)){
            // 节点处理信息
            $reviewNode = $this->loadModel('review')->getReviewNodeById($nodeId);
            $reviewNode->statusStageName    = zget($this->lang->review->nodeCodeNameList, $reviewNode->nodeCode);
            $this->view->reviewNode         = $reviewNode;
            $reviewerInfos = $this->reviewmanage->getReviewerInfoByNode($nodeId);

            foreach($reviewerInfos as $v){
                $extraInfo = json_decode($v->extra,true);
                if($v->nodeCode == 'firstAssignReviewer'){//指派初审人员
                    $str = '';
                    $trial = $this->review->getTrial($reviewID, $reviewInfo->version, $users,1);
                    //正式人员
                    $deptzs = array_filter(explode(' ',$trial['deptzs']));
                    //参与人员
                    $deptjoin = array_filter(explode(' ',$trial['deptjoin']));
                    $dept = array_merge($deptzs,$deptjoin);
                    if(!empty($dept)){
                        $dept = array_flip(array_flip($dept));
                    }
                    foreach ($dept as $item) {
                        $str .= $item .",";
                    }
                    $str = substr($str,0,-1);
                    $v->status  =  $str;
                    $select = $users;
                    $multiple = 'multiple';
                }elseif($v->nodeCode == 'baseline'){//打基线
                    $v->status = $reviewInfo->baseLineCondition;
                    $select = $this->lang->review->condition;
                    //$select['wait'] = $this->lang->review->needBaseline;
                }elseif($v->nodeCode == 'firstAssignDept'){//指派初审部门
                    $trial = $this->review->getTrial($reviewID, $reviewInfo->version, $users,1,1);
                    $dept = array_filter(explode(' ',$trial['deptid']));
                    if(isset($extraInfo['skipfirstreview']) && $dept){
                        //是否跳过
                        $select = $extraInfo['skipfirstreview'];
                    }else{
                        if(isset($extraInfo['skipfirstreview']) && $reviewInfo->type != 'pmo'){
                            $select = $extraInfo['skipfirstreview'];
                        }else if(isset($extraInfo['skipfirstreview']) && $reviewInfo->type == 'pmo'){
                            $select = 'PMO咨询无需初审';
                        } else{
                            //部门
                            if(count($dept) > 0){
                                $str = '';
                                foreach ($dept as $key=>$item) {
                                    $str .= $item .",";
                                }
                                $str = substr($str,0,-1);
                                $v->status = $str;
                                $select = $this->loadModel('dept')->getOptionMenu();
                            }else{
                                $select = $this->lang->reviewmanage->confirmResultList;
                            }
                        }
                    }
                }elseif(in_array($v->nodeCode, $this->lang->review->assignExpertNodeCodeList)){//指派评审专家
                    if(!isset($extraInfo['expert']) && !isset($extraInfo['outside'])){
                        $select = $this->lang->reviewmanage->confirmResultList;
                        $v->status = $v->reviewerStatus;
                    }else {
                        if(isset($extraInfo['appoint']) && $extraInfo['appoint'] == 1){
                            $select = '已委托';
                        }else{
                            $str = '';
                            if (isset($extraInfo['expert'])) {
                                $expert = explode(',', $extraInfo['expert']);
                                foreach ($expert as $item) {
                                    $str .= $item .",";
                                }
                            }
                            if (!empty($extraInfo['reviewedBy'])) {
                                $reviewedBy = explode(',', $extraInfo['reviewedBy']);
                                foreach ($reviewedBy as $item1) {
                                    $str .= $item1 .",";
                                }
                            }
                            if (!empty($extraInfo['outside'])) {
                                $outside = explode(',', $extraInfo['outside']);
                                foreach ($outside as $item2) {
                                    $str .= $item2 .",";
                                }
                            }
                            $str = substr($str,0,-1);
                            $v->status = $str;
                            $select = $users;
                            $multiple = 'multiple';
                        }
                    }
                }elseif(($v->nodeCode == 'formalOwnerReview') && isset($extraInfo['grade'])){//确定评审结论
                    $select = $this->lang->reviewmanage->gradeList;
                    $v->status = $extraInfo['grade'];
                }elseif($v->nodeCode == 'meetingReview' && $v->status == 'pass'){//专家会议评审
                    $select = [];
                }elseif($v->nodeCode == 'archive'){//归档
                    $select['pass'] = '已归档';
                    $arr = $this->lang->reviewmanage->confirmResultList;
                    unset($arr['pass']);
                    $select = array_merge($select,$arr);
                    $v->status = $v->reviewerStatus;
                }elseif(in_array($v->nodeCode, $this->lang->review->passButEditnodeCodeList) || $v->nodeCode == 'rejectVerifyButEdit'){//所有的需修改
                    $select = $this->lang->reviewmanage->confirmResultList;
                    unset($select['pass']);
                    $select['pass'] = '已修改';
                    $v->status = $v->reviewerStatus;
                }else{
                    //if(isset($extraInfo['appointUser']))
                    if(!empty($extraInfo['isEditInfo'])){
                        if($extraInfo['isEditInfo'] == 1){
                            $v->status = 'passEdit';
                        }else{
                            $v->status = 'pass';
                        }
                    }else{
                        $v->status = $v->reviewerStatus;
                    }
                    $select['pass'] = '通过（无需修改）';
                    $select['passEdit'] = '通过（需修改）';
                    $arr = $this->lang->reviewmanage->confirmResultList;
                    unset($arr['pass']);
                    $select = array_merge($select,$arr);
//                    }else{
//                        $select = $this->lang->reviewmanage->confirmResultList;
//                    }
                }
                unset($extraInfo);
            }
        }else{
            //节点id为0说明是关闭
            $select = $this->lang->reviewmanage->closeList;
            $closeInfos = new stdClass();
            $closeInfos->reviewer = $reviewInfo->closePerson;
            $closeInfos->status = 'reviewpass';
            $closeInfos->comment = $reviewInfo->comment;
            $reviewerInfos[0] = $closeInfos;
        }
        $reviewInfo->adviceGrade = $this->review->getReviewAdviceGrade($reviewInfo->type, $reviewInfo->grade);
        $adviceGradeList = $this->review->getReviewAdviceGradeList($reviewInfo->adviceGrade, true);
        $this->view->adviceGradeList  = $adviceGradeList;

        $this->view->select             = $select;
        $this->view->users              = $users;
        $this->view->reviewerInfos      = $reviewerInfos;
        $this->view->review             = $reviewInfo;
        $this->view->multiple             = $multiple;

    }

    // 编辑评审类型和评审会主席
    public function editTypeandOwner($reviewID){
        $this->loadModel('review');
        $this->app->loadLang('review');
        if($_POST) {
            $logChanges = $this->review->updateReviewInfos($reviewID);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $actionID = $this->loadModel('action')->create('review', $reviewID, 'edittypeandowner', $this->post->comment);
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

        $users = $this->loadModel('user')->getPairs('noletter');
        $this->view->type   = $this->lang->reviewmanage->changetypeList;
        $this->view->users  = $users;

        $this->view->review     = $reviewInfo;
        $this->display();
    }
    /**
     *  验证人
     */
    public function ajaxGetFirstReviewer($reviewer)
    {
        $users      = $this->loadModel('user')->getPairs('noclosed');
        $reviewer = explode(',',$reviewer);
        $res = [];
        foreach ($reviewer as $value){
            foreach ($users as $key=>$user) {
                if($key == $value){
                    $res[$key] = $user;
                    unset($users[$key]);
                }
            }
        }
        $users =array_merge($res,$users);
        // $reviewer = '';
        echo html::select('firstReviewers[]', $users, $reviewer , 'class="form-control chosen " multiple required');
    }

    /**
     * suspend 挂起.
     *
     * @param  int  $reviewID
     * @access public
     * @return void
     */
    public function suspend($reviewID,$source = 0){
        $this->loadModel('review');
        $this->app->loadLang('review');
        if($_POST)
        {
            $logChanges = $this->review->suspend($reviewID);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $actionID = $this->loadModel('action')->create('review', $reviewID, 'suspend', $this->post->comment);
            if($logChanges) {
                $this->action->logHistory($actionID, $logChanges);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }

        $review = $this->review->getByID($reviewID);
        $checkRes = $this->review->checkReviewIsAllowSuspend($review);
        $this->view->title      = $this->lang->review->suspend;
        $this->view->position[] = $this->lang->review->suspend;

        $this->view->review     = $review;
        $this->view->checkRes   = $checkRes;
        $this->view->source = $source;
        $this->display();
    }

    /**
     * renew 恢复.
     *
     * @param  int  $reviewID
     * @access public
     * @return void
     */
    public function renew($reviewID,$source =0)
    {
        $this->loadModel('review');
        $this->app->loadLang('review');
        if($_POST)
        {
            $logChanges = $this->review->renew($reviewID);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $extra = '';
            $updateFields = array_column($logChanges, 'field');
            if(in_array('meetingCode', $updateFields)){
                $meetingCode = $this->review->getMeetingCodeInLogChanges($logChanges);
                if($meetingCode){
                    $extra = '绑定会议，会议单号：'.$meetingCode;
                }
            }
            $actionID = $this->loadModel('action')->create('review', $reviewID, 'renew', $this->post->comment,$extra);
            if($logChanges) {
                $this->action->logHistory($actionID, $logChanges);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }
        $this->view->title      = $this->lang->review->renew;
        $this->view->position[] = $this->lang->review->renew;
        $review = $this->review->getByID($reviewID);
        $checkRes = $this->review->checkReviewIsAllowRenew($review, $this->app->user->account);
        if($checkRes['result']){ //允许恢复
            $lastStatus = $review->lastStatus;
            $version    = $review->version;
            $nodeCode = $this->review->getReviewNodeCodeByStatus($lastStatus, $lastStatus);
            if($nodeCode == $this->lang->review->nodeCodeList['formalAssignReviewerAppoint']){
                $nodeCode = $this->lang->review->nodeCodeList['formalAssignReviewer']; //评审主席指派专家
            }
            $exWhere =  "nodeCode != 'formalAssignReviewerAppoint'";
            $historyReviewStageList = $this->review->getHistoryReviewStageList('review', $reviewID, $version, $nodeCode, $exWhere);
            $historyReviewStageList = array_column($historyReviewStageList, 'nodeCodeName', 'nodeCode');
            if($lastStatus == 'pass'){
                $historyReviewStageList['close'] = '待关闭';
            }
            $this->view->historyReviewStageList = array('' => '') + $historyReviewStageList;
        }

        $this->view->review     = $review;
        $this->view->checkRes   = $checkRes;
        $this->view->source = $source;
        //项目承担部门
        $projectPlanInfo = $this->loadModel('projectplan')->getProjectPlanInfo($review->project, 'bearDept');
        $bearDept = $projectPlanInfo->bearDept;
        $this->view->bearDept = $bearDept;
        $this->display();
    }

    /**
     * 判断有没有部门参与权限(非空方法,勿删)
     */
    public function judgepermission(){
    }

    /**
     * 修改评审用户信息
     *
     * @param $reviewID
     * @param $field
     */
    public function editUsersByField($reviewID, $field){
        $this->loadModel('review');
        $this->app->loadLang('review');
        if($_POST)
        {
            $logChanges = $this->review->editUsersByField($reviewID, $field);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $actionID = $this->loadModel('action')->create('review', $reviewID, 'Edited');
            if($logChanges) {
                $this->action->logHistory($actionID, $logChanges);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }
        $title =  $title = sprintf($this->lang->review->editField, $this->lang->review->$field);
        $review = $this->review->getByID($reviewID);
        $checkRes = $this->review->checkIsAllowEditUsersByField($review, $this->app->user->account);
        if($checkRes['result']){ //允许操作
            if($field == 'reviewedBy'){
                $users = array('' => '') + $this->loadModel('user')->getUsersNameByType('outsideExpertType');
            }elseif ($field == 'outside'){
                $users = array('' => '') + $this->loadModel('user')->getUsersNameByType('outside');
            }else{
                $users = array('' => '') + $this->loadModel('user')->getPairs('noclosed');
            }
            $this->view->users  = $users;
        }
        $this->view->title      = $title;
        $this->view->position[] = $title;
        $this->view->review     = $review;
        $this->view->checkRes   = $checkRes;
        $this->view->reviewID = $reviewID;
        $this->view->field = $field;
        $this->display();
    }

    /**
     * View a review.
     *
     * @param int $reviewID
     * @access public
     * @return void
     */
    public function checkhistoryadvice($reviewID)
    {
        $this->loadModel('review');
        $this->app->loadLang('review');
        $review = $this->review->getByID($reviewID);
        $users = $this->loadModel('user')->getPairs('noletter');
        $outsideList1 = $this->loadModel('user')->getUsersNameByType('outsideExpertType');
        $outsideList2 = $this->loadModel('user')->getUsersNameByType('outside');
        $users = array_merge($users, $outsideList1, $outsideList2);
        $this->view->outsideList1 = $outsideList1;
        $this->view->outsideList2 =  $outsideList2;
        $this->view->users = $users;
        $this->view->maxVersion =  $this->review->getReviewNodeMaxVersion($reviewID);
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

        //由打基线节点，关闭的结果展示
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
        $this->view->title = $this->lang->review->view;
        $this->view->position[] = $this->lang->review->view;
        $this->view->review = $review;
        $this->view->actions = $this->loadModel('action')->getList('review', $reviewID);

        $this->view->relatedUsers = $this->loadModel('user')->getPairs('noletter');
        $this->view->deptMap = $this->loadModel('dept')->getOptionMenu();
        $this->view->issueList = $this->review->getReviewIssue($review->project, $reviewID);
        $this->view->gradeList = $this->review->getReviewAllGradeList();

        $this->view->companies   = $this->loadModel('company')->getOutsideCompanies();
        $this->view->reviewNodeReviewerList = $reviewNodeReviewerList;
        $this->view->closeType = $closeType;
        $this->view->allstatus = $this->lang->review->statusLabelList + $this->lang->review->statusFile;
        $this->app->loadLang('projectplan');
        $this->display();
    }

    //给出验证结论
    function setVerifyResult($reviewID){
        $this->loadModel('review');
        $this->app->loadLang('review');
        if($_POST) {
            $ret = $this->review->setVerifyResult($reviewID);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }
        $this->view->title      = $this->lang->review->setVerifyResult;
        $this->view->position[] = $this->lang->review->setVerifyResult;
        $review = $this->review->getByID($reviewID);
        $checkRes = $this->review->checkReviewIsAllowSetVerifyResult($review, $this->app->user->account);
        $this->view->review     = $review;
        $this->view->checkRes   = $checkRes;
        $this->display();
    }

    //手动发送验证邮件
    function sendUnDealIssueUsersMail($reviewID){
        $this->loadModel('review');
        $this->app->loadLang('review');
        if($_POST) {
            $ret = $this->review->sendUnDealIssueUsersMail($reviewID);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $objectType = 'review';
            $actionID = $this->loadModel('action')->create($objectType, $reviewID, 'sendundealissueusersmail');
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }
        $this->view->title      = $this->lang->review->sendUnDealIssueUsersMail;
        $this->view->position[] = $this->lang->review->sendUnDealIssueUsersMail;
        $review = $this->review->getByID($reviewID);
        $checkRes = $this->review->checkIsAllowSendUnDealIssueUsersMail($review, $this->app->user->account);
        if($checkRes['result']){
            $users = $this->loadModel('user')->getPairs('noletter');
            $this->view->users = $users;
        }
        $this->view->review     = $review;
        $this->view->checkRes   = $checkRes;
        $this->display();
    }
}