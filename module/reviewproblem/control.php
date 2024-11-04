<?php

/**
 * The control file of reviewproblem module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2021 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Qiyu Xie <xieqiyu@easycorp.ltd>
 * @package     reviewproblem
 * @version     $Id: control.php 5107 2020-09-09 09:46:12Z xieqiyu@easycorp.ltd $
 * @link        https://www.zentao.net
 */
class reviewproblem extends control
{

    /**
     * Get list of issues.
     *
     * @param int $projectID
     * @param int $reviewID
     * @param string $browseType
     * @param string $orderBy
     * @param int $recTotal
     * @param int $recPerPage
     * @param int $pageID
     * @access public
     * @return void
     */
    public function issue($projectID = 0, $reviewID = 0, $browseType = 'noclose', $param = 0, $orderBy = "id_desc", $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        if ($orderBy == '0') {
            $orderBy = 'id_desc';
        }
//        $this->loadModel('project')->setMenu($projectID);
        $this->loadModel('datatable');
        $this->loadModel('review');
        $browseType = strtolower($browseType);
        /* By search. */
        $emptyArr = array('0' => '');
        $meetingCodeList = $this->loadModel('reviewproblem')->getMeetingCodeList();
        $this->config->reviewproblem->search['params']['meetingCode']['values'] = $emptyArr + $meetingCodeList;

        $queryID = ($browseType == 'bysearch') ? (int)$param : 0;
        $issueParams = "project=$projectID" . "&reviewID=$reviewID" . "&browseType=bySearch&param=myQueryID&orderBy=$orderBy";
        $actionURL = $this->createLink('reviewproblem', 'issue', $issueParams);
        $this->reviewproblem->buildSearchForm($queryID, $actionURL);

        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        /* Get issueList and reviewInfo. */

        if ($orderBy == 'position_asc') {
            $orderBy = 'title_asc';
        }
        if ($orderBy == 'desc_asc') {
            $orderBy = 'desc_asc';
        }

        $issueList = $this->reviewproblem->getList($projectID, $reviewID, $browseType, $queryID, $orderBy, $pager);

        $reviewInfo = empty($reviewID) ? array() : $this->loadModel('review')->getByID($reviewID);
        foreach ($issueList as $value) {
            $this->extracted($value);
        }
        $users = $this->loadModel('user')->getAllUsers();
        $this->view->title = $this->lang->reviewproblem->issueBrowse;
        $this->view->issueList = $issueList;
        $this->view->reviewInfo = $reviewInfo;
        $this->view->users = $users;
        $this->view->pager = $pager;
        $this->view->projectID = $projectID;
        $this->view->reviewID = $reviewID;
        $this->view->status = $browseType;
        $this->view->orderBy = $orderBy;
        $this->view->browseType = $browseType;
        $this->view->reviews = $this->reviewproblem->getReviewPairs($projectID);
        $this->session->set('common_back_url', $this->app->getURI(true),'backlog');
        $this->session->set('reviewproblemList', $this->app->getURI(true), 'backlog');

        $this->display();
    }

    public function issueMeeting($meetingID, $projectID = 0, $reviewID = 0, $browseType = 'all', $param = 0, $orderBy = "id_desc", $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
//        $this->loadModel('project')->setMenu($projectID);
        $this->loadModel('datatable');
        $browseType = strtolower($browseType);
        /* By search. */
        $emptyArr = array('0' => '');
        $meetingCodeList = $this->loadModel('reviewproblem')->getMeetingCodeList();
        $this->config->reviewproblem->search['params']['meetingCode']['values'] = $emptyArr + $meetingCodeList;

        $queryID = ($browseType == 'bysearch') ? (int)$param : 0;
        $issueParams = "project=$projectID" . "&reviewID=$reviewID" . "&browseType=bySearch&param=myQueryID&orderBy=$orderBy";
        $actionURL = $this->createLink('reviewproblem', 'issue', $issueParams);
        $this->reviewproblem->buildSearchForm($queryID, $actionURL);

        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        /* Get issueList and reviewInfo. */
        if ($orderBy == 'position_asc') {
            $orderBy = 'title_asc';
        }
        if ($orderBy == 'desc_asc') {
            $orderBy = 'desc_asc';
        }
        $meetingInfo = $this->loadModel('reviewmeeting')->getById($meetingID);
        $meetingCode = $meetingInfo->meetingCode;
        $issueMeetingList = $this->reviewproblem->getMeetingList($meetingCode, $projectID, $reviewID, $browseType, $queryID, $orderBy, $pager);
        $reviewInfo = empty($reviewID) ? array() : $this->loadModel('review')->getByID($reviewID);
        foreach ($issueMeetingList as $value) {
            $this->extracted($value);
        }
        $this->view->title = $this->lang->reviewproblem->issueBrowse;
        $this->view->issueMeetingList = $issueMeetingList;
        $this->view->reviewInfo = $reviewInfo;
        $this->view->users = $this->loadModel('user')->getPairs('noclosed|noletter');
        $this->view->pager = $pager;
        $this->view->projectID = $projectID;
        $this->view->reviewID = $reviewID;
        $this->view->status = $browseType;
        $this->view->orderBy = $orderBy;
        $this->view->browseType = $browseType;
        $this->view->reviews = $this->reviewproblem->getReviewPairs($projectID);
        $this->display();
    }

    /**
     * Confirm the problem is resolved.
     *
     * @param int $issueID
     * @param string $status
     * @access public
     * @return void
     */
    public function updateStatus($issueID, $status)
    {
        $this->reviewproblem->updateStatus($issueID, $status);
        if (dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

        $this->saveStatusAction($issueID, $status);
    }

    /**
     *  Keep a record of the problem.
     *
     * @param int $issueID
     * @param string $status
     * @param bool $send
     * @access public
     * @return void
     */
    public function saveStatusAction($issueID, $status, $send = true)
    {
        /* Set action and get issue. */
        if ($status == 'active') $action = 'activated';
        if ($status == 'resolved') $action = 'resolved';
        if ($status == 'closed') $action = 'closed';
        $issue = $this->loadModel('reviewissue')->getByID($issueID);

        $this->loadModel('action')->create('reviewissue', $issueID, $action, $issue->title);
        if ($send) $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $this->createLink('reviewproblem', 'issue')));
    }

    /**
     *  Choose a solution to the problem.
     *
     * @param int $projectID
     * @param int $issueID
     * @param string $source 来源，用于完成后的跳转 list:列表 detail:详情
     * @access public
     * @return void
     */
    public function resolved($projectID, $issueID = 0, $source = 'list', $reviewID = 0, $statusNew = 'all', $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 10, $pageID = 1)
    {
//        $this->loadModel('project')->setMenu($projectID);
        if ($_POST) {
            $data = $this->reviewproblem->updateResolved($issueID);
//            $actionID = $this->loadModel('action')->create('reviewissue', $issueID, 'Resolved');
//            $this->action->logHistory($actionID, $data);

            if (dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $paramIssue = "project=$projectID" . "&review=$reviewID" . "&statusNew=$statusNew" . "&param=0" . "&orderBy=$orderBy" . "&recTotal=$recTotal" . "&recPerPage=$recPerPage" . "&pageID=$pageID";
            $params = "project=$projectID" . "&issue=$issueID" . "&review=$reviewID" . "&statusNew=$statusNew" . "&orderBy=$orderBy" . "&recTotal=$recTotal" . "&recPerPage=$recPerPage" . "&pageID=$pageID";

            if ($source == 'detail') $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $this->createLink('reviewproblem', 'view', $params)));

            $locate = $source == 'issue'?'parent':$this->createLink('reviewproblem', 'issue', $paramIssue);
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $locate));
        }

        $issue = $this->loadModel('reviewissue')->getByID($issueID);
        $dealOwner = $this->app->user->account;
        $dealDate = date("Y-m-d");
        if (!empty($issue->dealOwner)) {
            $dealOwner = $issue->dealOwner;
        }
        $users = $this->loadModel('user')->getAllUsers();

        $this->view->title = $this->lang->reviewproblem->resolved;
        $this->view->issue = $issue;
        $this->view->source = $source;
        $this->view->dealOwner = $dealOwner;
        $this->view->raiseBy = $issue->raiseBy;
        $this->view->dealDate = $dealDate;
        $this->view->issueId = $issueID;
        $this->view->resolutionBy = empty($issue->resolutionBy) ? $issue->resolutionBy : '';
        $this->view->stages = $this->reviewproblem->getReviewStage($issue->review);
        $this->view->reviewPairs = $this->reviewproblem->getReviewPairs($projectID);
        $this->view->users = $users;
        $this->display();
    }

    /**
     * Reviewing add issue.
     *
     * @param int $projectID
     * @access public
     * @return void
     */
    public function create($projectID = 0, $reviewID = 0, $statusNew = 'all', $param=0, $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 10, $pageID = 1)
    {
        $this->loadModel('review');
        $this->loadModel('reviewissue');
        $params = "project=$projectID" . "&review=$reviewID" . "&statusNew=$statusNew&param=$param" . "&orderBy=$orderBy" . "&recTotal=$recTotal" . "&recPerPage=$recPerPage" . "&pageID=$pageID";
        if ($_POST) {
            $issueID = $this->reviewproblem->create($projectID);
            if (dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $this->loadModel('action')->create('reviewissue', $issueID, 'opened', $this->post->opinion);

            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $this->createLink('reviewproblem', 'issue', $params)));
        }
        $grade = '';
        $reviewType = '';
        if($reviewID != 0){
            $issue = $this->loadModel('review')->getByID($reviewID);
            $status = isset($issue->status) ? $issue->status : '';
            $grade = '';
            if(!empty($status)){
                $grade = $this->dealStatusToGrade($issue->status,$issue->grade);
            }
            $reviewType = $issue->type;
        }
        $reviewPairs = $this->reviewproblem->getReviewBatchCreate();
        $emptyArr = array('0' => '');
        $this->view->title = $this->lang->reviewproblem->create;
        $this->view->reviewPairs = $emptyArr + $reviewPairs;
        $this->view->reviewID = $reviewID;
        $this->view->grade = $grade;
        $this->view->reviewType = $reviewType; //评审类型
        $this->view->users = $this->loadModel('user')->getAllUsers();
        $this->display();
    }

    //批量新建
    public function batchCreate($projectID, $reviewID = 0, $source = 'reviewproblem', $statusNew = 'all', $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 10, $pageID = 1)
    {
//        $this->loadModel('project')->setMenu($projectID);
        if (!empty($_POST)) {
            $params = "project=$projectID" . "&review=$reviewID" . "&statusNew=$statusNew" . "&param=0" . "&orderBy=$orderBy" . "&recTotal=$recTotal" . "&recPerPage=$recPerPage" . "&pageID=$pageID";
            $this->reviewproblem->batchCreate($projectID);
            if ($source == 'review') die(js::locate($this->createLink('review', 'view', "reviewId=$reviewID"), 'parent'));
            die(js::locate($this->createLink('reviewproblem', 'issue', $params), 'parent'));
        }
        $issue = $this->loadModel('review')->getByID($reviewID);
        $status = isset($issue->status) ? $issue->status : '';
        $grade = '';
        if(!empty($status)){
            $grade = $this->dealStatusToGrade($issue->status,$issue->grade);
        }
        //处理提出阶段
        $showFields = [];
        $emptyArr = array('0' => '');
        $ditto = array('ditto' => $this->lang->reviewproblem->ditto);
        $reviewPairs = $this->reviewproblem->getReviewBatchCreate();
        $typeList = $this->lang->reviewproblem->typeList;
        $meetingCodeList = $this->loadModel('reviewproblem')->getMeetingCodeList();

        $this->view->meetingCodeList = $emptyArr + $meetingCodeList;
        $this->view->typeList = $ditto + $typeList;
        $this->view->typeListNoDitto = $emptyArr + $typeList;
        $this->view->showFields = join(',', $showFields);
        $this->view->title = $this->lang->reviewproblem->create;
        $this->view->reviewPairs = $ditto + $reviewPairs;
        $this->view->reviewPairsNoditto = $emptyArr + $reviewPairs;
        $this->view->reviewID = $reviewID;
        $this->view->grade = $grade;
        $this->view->meetingCodeList = $emptyArr + $meetingCodeList;
        $this->view->users = $this->loadModel('user')->getAllUsers();

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
    public function delete($projectID = 0, $issueID = 0, $source = 'list', $reviewID = 0, $statusNew = 'all', $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 10, $pageID = 1)
    {
//        $this->loadModel('project')->setMenu($projectID);
        if (!empty($_POST)) {
            $delDesc = $_POST['delDesc'];
            $this->dao->update(TABLE_REVIEWISSUE)
                ->set('deleted')->eq('1')
                ->set('delDesc')->eq($delDesc)
                ->where('id')->eq($issueID)->exec();
            $this->loadModel('action')->create('reviewissue', $issueID, 'deleted', $this->post->delDesc);

            if (isonlybody()) {
                die(js::closeModal('parent.parent', $this->session->common_back_url));
            }

            if ($source == 'detail') {
                $params = "project=$projectID" . "&review=$reviewID" . "&statusNew=$statusNew" . "&orderBy=$orderBy" . "&recTotal=$recTotal" . "&recPerPage=$recPerPage" . "&pageID=$pageID";
                die(js::locate(inlink('issue', $params), 'parent'));
            }
            die(js::reload('parent'));
        }
        $issue = $this->loadModel('reviewissue')->getByID($issueID);
        $this->view->title = $this->lang->reviewproblem->edit;
        $this->view->issue = $issue;
        $this->view->projectId = $projectID;
        $this->view->actions = $this->loadModel('action')->getList('reviewissue', $issueID);
        $this->view->reviewName = $this->loadModel('review')->getByID($issue->review);
        $this->view->users = $this->loadModel('user')->getAllUsers();

        $this->display();
    }


    /**
     * Reviewing edit issue.
     *
     * @param int $projectID
     * @param int $issueID
     * @access public
     * @return void
     */
    public function edit($projectID, $issueID = 0, $source = 'list', $reviewID = 0, $statusNew = 'all', $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 10, $pageID = 1)
    {
//        $this->loadModel('project')->setMenu($projectID);
        $this->loadModel('review');
        $this->loadModel('reviewissue');
        if ($_POST) {
            $changes = $this->reviewproblem->update($issueID);
            if ($changes) {
                $actionID = $this->loadModel('action')->create('reviewissue', $issueID, 'Edited');
                $this->action->logHistory($actionID, $changes);
            }

            if (dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $paramsIssue = "project=$projectID" . "&review=$reviewID" . "&statusNew=$statusNew" . "&param=0" . "&orderBy=$orderBy" . "&recTotal=$recTotal" . "&recPerPage=$recPerPage" . "&pageID=$pageID";
            $params = "project=$projectID" . "&issue=$issueID" . "&review=$reviewID" . "&statusNew=$statusNew" . "&orderBy=$orderBy" . "&recTotal=$recTotal" . "&recPerPage=$recPerPage" . "&pageID=$pageID";
            if ($source == 'detail') $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $this->createLink('reviewproblem', 'view', $params)));
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $this->createLink('reviewproblem', 'issue', $paramsIssue)));
        }
        $emptyArr = array('0' => '');
        $issue = $this->loadModel('reviewissue')->getByID($issueID);
        $meetingCodeList  = $this->loadModel('reviewproblem')->getMeetingCodeByReviewID($issue->review);
        $this->view->title = $this->lang->reviewproblem->edit;
        $this->view->issue = $issue;
        $this->view->stages = $this->reviewproblem->getReviewStage($issue->review);
        $this->view->reviewPairs = $this->reviewproblem->getReviewBatchCreate();
        $this->view->users = $this->loadModel('user')->getAllUsers();
        $this->view->meetingCodeList = $emptyArr + $meetingCodeList;
        //关联的评审类型
        $reviewType = $issue->reviewType;
        $this->view->reviewType = $reviewType; //评审类型
        $this->display();
    }

    /**
     * Stage of project review.
     *
     * @param int $reviewID
     * @access public
     * @return void
     */
    public function ajaxGetInjection($reviewID)
    {
        $stages = $this->reviewproblem->getReviewStage($reviewID);
        die(html::select('injection', $stages, '', "class='form-control chosen'"));
    }

    /**
     * Desc: 处理问题当前处理人回填
     * Date: 2022/5/12
     * Time: 10:12
     *
     * @param $status
     *
     */
    public function ajaxGetDealOwner($status)
    {
        $dealOwner = '';
        $dealDate = '';
        $resolvedStatusArr = $this->config->reviewproblem->resolvedStatusArr;
        $browseStatusArr = $this->config->reviewproblem->browseStatusArr;
        $issue = $this->loadModel('reviewissue')->getByID($issueID);

        if (in_array($status, $resolvedStatusArr)) {
            $dealOwner = $issue->validation;
            $dealDate = $issue->verifyDate;
        }
        //其他 处理人员取解决人员
        if (in_array($status, $browseStatusArr)) {
            $dealOwner = $issue->resolutionBy;
            $dealDate = $issue->resolutionDate;
        }
        $data = [];
        $data['dealOwner'] = $dealOwner;
        $data['dealDate'] = $dealDate;
        echo json_encode($data, true);
    }


    /**
     * Access to review category.
     *
     * @param int $reviewID
     * @access public
     * @return void
     */
    public function ajaxGetCategory($reviewID)
    {
        $category = $this->reviewproblem->getReviewCategory($reviewID);
        die(html::select('category', $category, '', "class='form-control chosen' onchange='findCheck()'"));
    }

    /**
     * DESC:提出阶段、解决人员与评审标题联动
     * Date: 2022/4/13
     * Time: 15:59
     *
     * @param $reviewID
     *
     */
    public function ajaxGetType($reviewID)
    {
        $data = [];
        //解决人员处理
        $issue = $this->loadModel('review')->getByID($reviewID);

        $data['issue'] = $issue ? (isset($issue->createdBy) ? $issue->createdBy : '') : '';
        //处理提出阶段
        $status = isset($issue->status) ? $issue->status : '';
        $grade = '';
        if(!empty($status)){
            $grade = $this->dealStatusToGrade($status,$grade);
        }
        $data['grade'] = $grade;
        $data['status'] = $status;
        echo json_encode($data, true);
    }

    /**
     * Desc:根据评审状态和评审方式处理提出阶段
     * 预审中 -> 预审
     * 初审中 -> 初审
     * 正式评审中 && 评审方式是在线评审 -> 在线评审
     * 正式评审中 && 评审方式是会议评审-> 会议评审
     * 外部评审中 -> 外部评审
     *
     * 迭代12升级优化
     * 当评审流转状态为“在线评审中”时，提出阶段总为“在线评审”
     * 当评审流转状态为“会议评审中”时，提出阶段总为“会议评审”
     * Date: 2022/5/7
     * Time: 11:02
     *
     *
     */
    public function dealStatusToGrade($status = '',  $grade = ''): string
    {
        $stage = '';
        $trialArr = array('waitFirstAssignDept','waitFirstAssignReviewer','firstAssigning','waitFirstReview','firstReviewing','waitFirstMainReview','firstMainReviewing');
//        $formalArr = array('waitFormalAssignReviewer','waitFormalReview','formalReviewing','waitFormalOwnerReview','waitVerify','verifying');
        $onlineArr = array('waitFormalAssignReviewer','waitFormalReview','waitFormalOwnerReview','formalReviewing');
        $meetArr = array('waitMeetingReview','meetingReviewing','waitMeetingOwnerReview');
        $outArr = array('waitOutReview','outReviewing');

        if($status == 'waitPreReview')   $stage = 'pre'; //预审
        if(in_array($status,$trialArr))  $stage = 'trial'; //初审
        if(in_array($status,$onlineArr)) $stage = 'online'; //在线评审
        if(in_array($status,$meetArr))   $stage = 'meeting'; //会议评审
        if(in_array($status,$outArr))    $stage = 'out'; //外部评审
        return $stage;
    }



    /**
     * Desc: 改变项目代号获取对应的评审标题对应的列表
     * Date: 2022/4/26
     * Time: 16:35
     *
     * @param string $code
     *
     */
    public function getAjaxReviewListByCode($code, $classId)
    {
        $code = urldecode($code);
        $reviewList = [];
        $emptyArr = array('0' => '');
        if ($code) {
            $reviewList = $this->reviewproblem->getReviewListByCode($code);
        }
        die(html::select("review" . "[" . $classId . "]", $emptyArr + $reviewList, '', "class='form-control chosen'"));
    }

    /**
     * Desc: 只获取评审标题对应的会议编号
     * Date: 2022/8/22
     * Time: 17:16
     *
     * @param $reviewID
     *
     */
    public function ajaxGetMeetingByReviewID($reviewID)
    {
        $code = array(''=>'') + $this->loadModel('reviewissue')->getMeetingCodeByReviewID($reviewID);
        die(html::select('meetingCode',  $code, '', 'class="form-control chosen"'));
    }

    /**
     * Desc: 导入数据根据项目代号获取评审标题列表
     * Date: 2022/4/26
     * Time: 15:25
     *
     * @param string $code
     * @return array
     *
     */
    public function getReviewListByCode($code = '')
    {
        $reviewList = [];
        if ($code) {
            $reviewList = $this->reviewproblem->getReviewListByCode($code);
        }
        return $reviewList;
    }

    /**
     * Access to review checklists.
     *
     * @param int $reviewID
     * @param string $type
     * @access public
     * @return void
     */
    public function ajaxGetCheck($reviewID = 0, $type = '')
    {
        $checkList = $this->reviewproblem->getReviewCheck($reviewID, $type);
        die(html::select('review', $checkList, '', "class='form-control chosen'"));
    }

    /**
     * Issue details.
     *
     * @param int $projectID
     * @param int $issueID
     * @access public
     * @return void
     */
    public function view($projectID, $issueID = 0, $reviewID = 0, $statusNew = 'all', $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 10, $pageID = 1)
    {
//        $this->loadModel('project')->setMenu($projectID);
        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);
        $issueInfo = $this->loadModel('reviewissue')->getByID($issueID);
        //已关闭评审id集合
        $reviewIds = $this->loadModel('reviewproblem')->getAllReviewId($projectID);
        $this->extracted($issueInfo);//处理空日期展示

        $this->view->title = $this->lang->reviewproblem->issueInfo;
        $this->view->issue = $issueInfo;
        $this->view->projectID = $projectID;
        $this->view->issueID = $issueID;
        $this->view->reviewID = $reviewID;
        $this->view->reviewIds = $reviewIds;
        $this->view->pager = $pager;
        $this->view->status = $statusNew;
        $this->view->orderBy = $orderBy;
        $this->view->actions = $this->loadModel('action')->getList('reviewissue', $issueID);
        $this->view->users = $this->loadModel('user')->getAllUsers();
        $this->display();
    }

    /**
     * Get review records
     *
     * @param int $projectID
     * @param string $reviewID
     * @param string $browseType
     * @access public
     * @return void
     */
    public function ajaxGetReview($projectID, $reviewID, $browseType)
    {
        $this->loadModel('review');
        echo $this->reviewproblem->getReviewRecord($projectID, $reviewID, $browseType);
    }

    /**
     * Desc: 导出数据
     * Date: 2022/7/28
     * Time: 9:56
     *
     * @param int $projectID
     * @param $reviewID
     * @param string $orderBy
     * @param string $browseType
     *
     */
    public function export($projectID = 0, $reviewID, $orderBy = 'id_desc', $browseType = 'all')
    {
        $this->app->loadLang('opinion');
        $this->loadModel('review');
        if ($_POST) {
            $reviewIssueLang = $this->lang->reviewproblem;
            $reviewIssueConfig = $this->config->reviewproblem;
            $fields = $this->post->exportFields ? $this->post->exportFields : explode(',', $reviewIssueConfig->list->exportFields);
            foreach ($fields as $key => $fieldName) {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = $reviewIssueLang->$fieldName ?? $fieldName;
                unset($fields[$key]);
            }
            $data = $this->reviewproblem->getReviewissueList($projectID, $reviewID, $browseType, $orderBy);
            $statusList = $reviewIssueLang->statusList;
            $typeList = $reviewIssueLang->typeList;
            $dateString = '0000-00-00';
            $reviewList = $this->reviewproblem->getReviewBatchCreate($reviewID);
            $users = $this->loadModel('user')->getPairs('noletter');

            foreach ($data as $value) {

                $mark = $this->loadModel('projectplan')->getPlanByProjectID($value->project);
                $value->code = $mark ? $mark->mark : '';

                $value->desc = strip_tags($value->desc);
                $value->dealDesc = strip_tags($value->dealDesc);
                $value->changelog = strip_tags($value->changelog);
                if ($value->status == 'resolved') {
                    $value->status = $statusList['closed'];
                } else {
                    $value->status = $statusList[$value->status];
                }
                $value->type = $typeList[$value->type];

                $value->createDate = $value->createDate != $dateString ? $value->createDate : '';
                $value->resolutionDate = $value->resolutionDate != $dateString ? $value->resolutionDate : '';
                $value->raiseDate = $value->raiseDate != $dateString ? $value->raiseDate : '';
                $value->editDate = $value->editDate != $dateString ? $value->editDate : '';
                $value->verifyDate = $value->verifyDate != $dateString ? $value->verifyDate : '';

                $value->createdBy = !empty($users[$value->createdBy]) ? $users[$value->createdBy] : '';
                $value->resolutionBy = !empty($users[$value->resolutionBy]) ? $users[$value->resolutionBy] : '';
                $value->raiseBy = !empty($users[$value->raiseBy]) ? $users[$value->raiseBy] : '';
                $value->validation = !empty($users[$value->validation]) ? $users[$value->validation] : '';
                $value->editBy = !empty($users[$value->editBy]) ? $users[$value->editBy] : '';
                $value->review = !empty($reviewList) ? $reviewList[$value->review] : '';
                $value->dealUser = !empty($users[$value->dealUser]) ? $users[$value->dealUser] : '';
            }
            $this->post->set('fields', $fields);
            $this->post->set('rows', $data);
            $this->post->set('kind', '问题列表数据');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        $this->view->fileName = $this->lang->reviewproblem->common;
        $this->view->allExportFields = $this->config->reviewproblem->list->exportFields;
        $this->view->customExport = true;
        $this->display();
    }

    /**
     * Desc: 导出模板 ①选中评审标题时导出模板   ②不选择时直接导出模板
     * Date: 2022/7/28
     * Time: 9:53
     *
     * @param $projectID
     * @param int $reviewID
     *
     */
    public function exportTemplate($projectID, $reviewID = 0)
    {
        if ($_POST) {
            $this->reviewproblem->setListValue($reviewID);
            $fields = [];
            $templateFields = $this->config->reviewproblem->export->templateFields;
            foreach ($templateFields as $field) {
                $fields[$field] = $this->lang->reviewproblem->$field;
            }
            $num = $this->post->num;
            $rows = array();
            $dealArray = [];
            $review = $this->loadModel('review')->getByID($reviewID);
            //根据选择的评审标题回填项目代号
            $mark = '';
            if($reviewID && $review){
                $markInfo = $this->loadModel('projectplan')->getPlanByProjectID($review->project);
                $mark = $markInfo ? $markInfo->mark : '';
            }
            $users = $this->loadModel('user')->getPairs('noclosed|noletter');
            $date = date('Y-m-d');
            for ($i = 0; $i < $num; $i++) {
                foreach ($templateFields as $v) {
                    $dealArray[$v] = '';
                    if(!empty($mark)){
                        $dealArray['code'] = $mark;
                    }
                    switch ($v) {
                        case 'raiseBy':
                            $dealArray[$v] = $this->app->user->realname;
                            break;
                        case 'raiseDate':
                            $dealArray[$v] = $date;
                            break;
                        case 'status':
                            $dealArray[$v] = '已新建';
                            break;
                        case 'review':
                            $dealArray[$v] = isset($review) ? $review->title : '';
                            break;
                        case 'resolutionBy':
                            $dealArray[$v] = $users;
                            break;
                        case 'resolutionDate':
                        case 'verifyDate':
                            $dealArray[$v] = '0000-00-00';
                            break;
                        default:
                            break;
                    }

                }
                $rows[$i] = (object)$dealArray;
            }
            $this->post->set('fields', $fields);
            $this->post->set('kind', 'reviewproblem');
            $this->post->set('rows', $rows);
            $this->post->set('fileName', 'problemListTemplate');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        $this->display();
    }

    /**
     * Desc: 导入数据
     * Date: 2022/7/28
     * Time: 9:54
     *
     * @param int $projectId
     *
     */
    public function import($projectId = 0)
    {
        if ($_FILES) {
            $file = $this->loadModel('file')->getUpload('file');
            $file = $file[0];
            if ($file['extension'] != 'xlsx') die(js::alert($this->lang->file->onlySupportXLSX));

            $fileName = $this->file->savePath . $this->file->getSaveName($file['pathname']);

            move_uploaded_file($file['tmpname'], $fileName);

            $phpExcel = $this->app->loadClass('phpexcel');
            $phpReader = new PHPExcel_Reader_Excel2007();
            if (!$phpReader->canRead($fileName)) {
                $phpReader = new PHPExcel_Reader_Excel5();
                if (!$phpReader->canRead($fileName)) die(js::alert($this->lang->reviewproblem->emptyReviewMsg, true));
            }
            $this->session->set('fileImport', $fileName);
            die(js::locate(inlink('showImport', "projectId=$projectId"), 'parent.parent'));
        }

        $this->display();
    }

    /**
     * Desc: 导入确认页面
     * Date: 2022/7/28
     * Time: 9:54
     *
     * @param int $projectId
     * @param int $pagerID
     * @param int $maxImport
     * @param int $insert
     *
     */
    public function showImport($projectId = 0, $pagerID = 1, $maxImport = 0, $insert = 1)
    {
        $backUrl = $this->session->common_back_url;
        $this->app->loadLang('opinion');
        $this->lang->reviewproblem->categoryList = $this->lang->opinion->categoryList;
        $file = $this->session->fileImport;
        $tmpPath = $this->loadModel('file')->getPathOfImportedFile();
        $tmpFile = $tmpPath . DS . md5(basename($file));
        $emptyArr = array('0' => '');
        if ($_POST) {
            $this->reviewproblem->createFromImport();
            if ($this->post->isEndPage) {
                unlink($tmpFile);
                die(js::locate($backUrl, 'parent'));
            } else {
                die(js::locate(inlink('showImport', "pagerID=" . ($this->post->pagerID + 1) . "&maxImport=$maxImport&insert=" . zget($_POST, 'insert', '')), 'parent'));
            }
        }
        if (!empty($maxImport) and file_exists($tmpFile)) {
            $reviewIssueData = unserialize(file_get_contents($tmpFile));
        } else {
            $pagerID = 1;
            $reviewIssueLang = $this->lang->reviewproblem;
            $reviewIssueConfig = $this->config->reviewproblem;
            $fields = explode(',', $reviewIssueConfig->list->exportFields);
            foreach ($fields as $key => $fieldName) {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = $reviewIssueLang->$fieldName ?? $fieldName;
                unset($fields[$key]);
            }
            $rows = $this->file->getRowsFromExcel($file);
            foreach ($rows as $currentRow => $row) {

                $reviewIssue = new stdclass();
                foreach ($row as $currentColumn => $cellValue) {
                    if ($currentRow == 1) {
                        $field = array_search($cellValue, $fields);
                        $columnKey[$currentColumn] = $field ? $field : '';
                        continue;
                    }
                    if (empty($columnKey[$currentColumn])) {
                        $currentColumn++;
                        continue;
                    }
                    $field = $columnKey[$currentColumn];
                    $currentColumn++;
                    // check empty data.
                    if (empty($cellValue)) {
                        $reviewIssue->$field = '';
                        continue;
                    }


                    if (in_array($field, $reviewIssueConfig->export->listFields)) {
                        if (strrpos($cellValue, '(#') === false) {
                            $reviewIssue->$field = $cellValue;
                            if (!isset($reviewIssueLang->{$field . 'List'}) or !is_array($reviewIssueLang->{$field . 'List'})) continue;

                            /* when the cell value is key of list then eq the key. */
                            $listKey = array_keys($reviewIssueLang->{$field . 'List'});
                            unset($listKey[0]);
                            unset($listKey['']);
                            $fieldKey = array_search($cellValue, $reviewIssueLang->{$field . 'List'});
                            if ($fieldKey) $reviewIssue->$field = $fieldKey;
                        } else {
                            $id = trim(substr($cellValue, strrpos($cellValue, '(#') + 2), ')');
                            // $reviewIssueLang->$field = $id;
                            $reviewIssue->$field = $id;
                        }
                    } else {
                        $reviewIssue->$field = $cellValue;
                    }
                }
                if (empty($reviewIssue->code)) continue;
                $review = $this->loadModel('review')->getReviewIdByReview($reviewIssue->review);
                $reviewIssue->raiseBy = $this->pinyin($reviewIssue->raiseBy);
                $reviewIssue->validation = $this->pinyin($reviewIssue->validation);
                $reviewIssue->resolutionBy = $this->pinyin($reviewIssue->resolutionBy);
                $reviewIssue->review = !empty($review) ? $review : 0;
                $reviewIssueData[$currentRow] = $reviewIssue;
                $reviewList[] = $emptyArr + $this->getReviewListByCode($reviewIssue->code);
                unset($reviewIssue);
            }
            file_put_contents($tmpFile, serialize($reviewIssueData));
        }
        if (empty($reviewIssueData)) {
            unlink($this->session->fileImport);
            unset($_SESSION['fileImport']);
            echo js::alert($this->lang->reviewproblem->emptyReviewMsg, true);
            die(js::locate($backUrl, 'parent'));
        }

        $allCount = count($reviewIssueData);
        $allPager = 1;
        if ($allCount > $this->config->file->maxImport) {
            if (empty($maxImport)) {
                $this->view->allCount = $allCount;
                $this->view->maxImport = $maxImport;
                die($this->display());
            }

            $allPager = ceil($allCount / $maxImport);
            $reviewIssueData = array_slice($reviewIssueData, ($pagerID - 1) * $maxImport, $maxImport, true);
        }
        if (empty($reviewIssueData)) die(js::locate($backUrl, 'parent'));

        /* Judge whether the editedStories is too large and set session. */
        $countInputVars = count($reviewIssueData) * 11;
        $showSuhosinInfo = common::judgeSuhosinSetting($countInputVars);
        if ($showSuhosinInfo) $this->view->suhosinInfo = extension_loaded('suhosin') ? sprintf($this->lang->suhosinInfo, $countInputVars) : sprintf($this->lang->maxVarsInfo, $countInputVars);

        $newReviewList = [];
        foreach ($reviewList as $k => $v) {
            $newReviewList[$k + 1] = $v;
        }

        $meetingCodeList = $this->loadModel('reviewproblem')->getMeetingCodeList();
        $this->view->title = $this->lang->reviewproblem->common . $this->lang->colon . $this->lang->reviewproblem->showImport;
        $this->view->position[] = $this->lang->reviewproblem->showImport;
        $this->view->reviewList = $newReviewList;
        $this->view->reviewIssueData = $reviewIssueData;
        $this->view->allCount = $allCount;
        $this->view->allPager = $allPager;
        $this->view->pagerID = $pagerID;
        $this->view->isEndPage = $pagerID >= $allPager;
        $this->view->maxImport = $maxImport;
        $this->view->meetingCodeList = $emptyArr + $meetingCodeList;
        $this->view->dataInsert = $insert;
        $this->view->users = $this->loadModel('user')->getPairs('noclosed');
        $this->display();
    }


    /**
     * Desc: 将中文转换为拼音
     * Date: 2022/4/26
     * Time: 11:22
     *
     * @param string $string
     * @return string
     *
     */
    public function pinyin($string = ''): string
    {
        //将中文转换为拼音
        $this->app->loadClass('pinyin');
        $pinyin = new pinyin();
        $pinyinString = $pinyin->convert($string);
        $prefixArray = ['t','c', 'cj'];
        if (isset($pinyinString[0]) && in_array($pinyinString[0], $prefixArray)) {
            $pinyinString[0] = 't_';
        }
        return implode('', $pinyinString);
    }

    /**
     * Desc:处理空日期展示
     * Date: 2022/4/26
     * Time: 11:19
     *
     * @param $issueInfo
     *
     */
    public function extracted($issueInfo)
    {
        if ($issueInfo->editDate == '0000-00-00') $issueInfo->editDate = '';
        if ($issueInfo->raiseDate == '0000-00-00') $issueInfo->raiseDate = '';
        if ($issueInfo->verifyDate == '0000-00-00') $issueInfo->verifyDate = '';
        if ($issueInfo->resolutionDate == '0000-00-00') $issueInfo->resolutionDate = '';
        if ($issueInfo->dealDate == '0000-00-00') $issueInfo->dealDate = '';
        if ($issueInfo->status == 'resolved') $issueInfo->status = 'closed';
    }

    /**
     * Desc: 获取公共数据
     * Date: 2022/7/28
     * Time: 9:55
     *
     * @param $id
     *
     */
    public function ajaxGetCommonList($id)
    {
        $users = $this->loadModel('user')->getPairs('noclosed');
        echo html::select($id, $users, '', 'class="form-control chosen" required');

    }

    /**
     * Desc:根据评审列表获取评审数据
     * Date: 2022/7/29
     * Time: 17:17
     *
     *
     */
    public function reviewIdsByReviewManage($status = 'all', $orderBy, $page)
    {
        $reviewList = $this->loadModel('reviewmanage')->reviewList($status, $orderBy, $page);
        //var_dump(count($reviewList));
    }
}
