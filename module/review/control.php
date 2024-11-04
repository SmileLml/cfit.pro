<?php
/**
 * The control file of review module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2020 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Qiyu Xie <xieqiyu@easycorp.ltd>
 * @package     review
 * @version     $Id: control.php 5107 2020-09-09 09:46:12Z xieqiyu@easycorp.ltd $
 * @link        http://www.zentao.net
 */
class review extends control
{
    /**
     * Review Common action.
     *
     * @param  int    $projectID
     * @access public
     * @return void
     */
    public function commonAction($projectID)
    {
        $this->loadModel('project')->setMenu($projectID);
    }

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

        /* By search. */
        $queryID = ($browseType == 'bysearch')  ? (int)$param : 0;  
        $actionURL = $this->createLink('review', 'browse', "projectID=$projectID&browseType=bySearch&param=myQueryID");
        $this->review->buildSearchForm($queryID, $actionURL);

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = pager::init($recTotal, $recPerPage, $pageID);

        $this->view->title      = $this->lang->review->browse;
        $this->view->position[] = $this->lang->review->browse;

        $this->view->reviewList = $this->review->getList($projectID, $browseType, $queryID, $orderBy, $pager);
        $this->view->users      = $this->loadModel('user')->getPairs('noclosed|noletter');
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

    /**
     * Assess a review.
     *
     * @param  int    $reviewID
     * @param  string $from  work|contribute
     * @access public
     * @return void
     */
    public function assess($reviewID = 0, $from = '')
    {
        $review = $this->review->getByID($reviewID);
        $this->commonAction($review->project);

        if($_POST)
        {
            $result = $this->review->assess($reviewID);

            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $method = $result['method'];
            $action = $method . 'review';
            $this->loadModel('action')->create('review', $reviewID, $action, '', $result['result']);
            if(isonlybody()) die(js::closeModal('parent.parent', 'this'));
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => inlink('browse', "project=$review->project")));
        }

        if($this->app->openApp == 'my')
        {
            if($from == 'work')       $this->lang->my->menu->work['subModule']       = 'review';
            if($from == 'contribute') $this->lang->my->menu->contribute['subModule'] = 'review';
        }

        $methods = $this->dao->select('id, method')->from(TABLE_REVIEWRESULT)
            ->where('review')->eq($reviewID)
            ->andWhere('result')->eq('pass')
            ->fetchPairs();
        foreach($this->lang->review->methodList as $key => $method)
        {
            if(in_array($key, $methods)) unset($this->lang->review->methodList[$key]);
        }

        $this->view->title      = $this->lang->review->common;
        $this->view->position[] = $this->lang->review->common;

        $this->view->review       = $review;
        $this->view->projectID    = $review->project;

        $this->display();
    }

    /**
     * Set data to review page.
     *
     * @param  object $review
     * @access public
     * @return void
     */
    public function setViewData($review)
    {
        if($review->category == 'PP')
        {
            $selectCustom = 0;
            $dateDetails  = 1;
            if($review->category == 'PP')
            {
                $owner        = $this->app->user->account;
                $module       = 'programplan';
                $section      = 'browse';
                $object       = 'stageCustom';
                $setting      = $this->loadModel('setting');
                $selectCustom = $setting->getItem("owner={$owner}&module={$module}&section={$section}&key={$object}");

                if(strpos($selectCustom, 'date') !== false) $dateDetails = 0;
            }

            $this->view->plans = $this->loadModel('programplan')->getDataForGantt($review->project, $review->product);
            $this->view->selectCustom = $selectCustom;
            $this->view->dateDetails  = $dateDetails;
        }
        elseif(in_array($review->category, array('SRS', 'URS', 'HLDS', 'DDS', 'DBDS','ADS')))
        {
            if($review->range == 'all')
            {
                $this->view->bookID = $this->review->getBookID($review);
                $this->view->book   = $this->loadModel('doc')->getByID($this->view->bookID);
            }

            $this->view->tree = ($review->category == 'SRS' || $review->category == 'URS') ? $this->review->getNoModuleStory($review) : $this->review->getDesignTree($review);
        }
        elseif($review->category == 'ITTC' || $review->category == 'STTC')
        {
            $this->view->tree = $this->review->getNoModuleCase($review);
        }
    }

    /**
     * Create a review.
     *
     * @param  int     $projectID
     * @param  string  $object
     * @param  int     $productID
     * @param  string  $reviewRange
     * @param  string  $checkedItem
     * @access public
     * @return void
     */
    public function create($projectID = 0, $object = '', $productID = 0, $reviewRange = 'all', $checkedItem = '')
    {
        $this->commonAction($projectID);

        if($_POST)
        {
            $reviewID = $this->review->create($projectID, $reviewRange, $checkedItem);

            if(!dao::isError())
            {
                $this->loadModel('action')->create('review', $reviewID, 'Opened', $this->post->comment);
                $response['result']  = 'success';
                $response['message'] = $this->lang->saveSuccess;
                $response['locate']  = inlink('browse', "project=$projectID");
                $this->send($response);
            }

            $response['result']  = 'fail';
            $response['message'] = dao::getError();
            $this->send($response);
        }

        $this->view->title      = $this->lang->review->create;
        $this->view->position[] = $this->lang->review->create;

        $stakeholder = $this->loadModel('stakeholder')->getStakeholders($projectID, 'outside');
        $stakeList   = array();
        foreach($stakeholder as $s)
        {
            $stakeList[$s->user] = $s->companyName . '/' . $s->name;
        }
        $this->view->object    = $object;
        $this->view->projectID = $projectID;
        $this->view->productID = $productID;
        $this->view->products  = $this->loadModel('product')->getProductPairsByProject($projectID);
        $this->view->users     = $this->loadModel('user')->getPairs('noclosed');
        $this->view->outside   = $stakeList;
        $this->display();
    }

    /**
     * Edit a review.
     *
     * @param  int  $reviewID
     * @access public
     * @return void
     */
    public function edit($reviewID)
    {
        $review = $this->review->getByID($reviewID);
        $this->commonAction($review->project);

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
            $response['locate']  = inlink('browse', "project=$review->project");
            $this->send($response);

        }

        $stakeholder = $this->loadModel('stakeholder')->getStakeholders($review->project, 'outside');
        $stakeList   = array();
        foreach($stakeholder as $s)
        {
            $stakeList[$s->user] = $s->companyName . '/' . $s->name;
        }

        $this->view->title      = $this->lang->review->edit;
        $this->view->position[] = $this->lang->review->edit;
        $this->view->review     = $review;
        $this->view->project    = $this->loadModel('project')->getByID($review->project);
        $this->view->products   = $this->loadModel('product')->getPairs($review->project);
        $this->view->users      = $this->loadModel('user')->getPairs('noclosed');
        $this->view->relatedUsers  = $this->loadModel('user')->getPairs('noclosed');
        $this->view->outside    = $stakeList;
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
        $review = $this->review->getByID($reviewID);
        $this->commonAction($review->project);

        $stakeholder = $this->loadModel('stakeholder')->getStakeholders($review->project, 'outside');
        $stakeList   = array();
        foreach($stakeholder as $s)
        {
            $stakeList[$s->user] = $s->companyName . '/' . $s->name;
        }

        $this->view->title      = $this->lang->review->view;
        $this->view->position[] = $this->lang->review->view;
        $this->view->review     = $review;
        $this->view->actions    = $this->loadModel('action')->getList('review', $reviewID);
        $this->view->users      = $this->loadModel('user')->getPairs('noletter');
        $this->view->outside    = $stakeList;
        $this->display();
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
        $review = $this->review->getByID($reviewID);

        if($_POST)
        {
            $changes = $this->review->submit($reviewID);
            if(dao::isError()) die(js::error(dao::getError()));

            $actionID = $this->loadModel('action')->create('review', $reviewID, 'reloadSubmit', $this->post->comment);
            if($changes) $this->action->logHistory($actionID, $changes);
            die(js::closeModal('parent.parent'));
        }

        $this->view->title      = $this->lang->review->submit;
        $this->view->position[] = $this->lang->review->submit;
        $this->view->review     = $review;
        $this->view->actions    = $this->loadModel('action')->getList('review', $reviewID);
        $this->view->users      = $this->loadModel('user')->getPairs('noletter');
        $this->display();
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
        $this->dao->update(TABLE_REVIEW)->set('status')->eq('draft')->where('id')->eq($reviewID)->exec();
        $this->loadModel('action')->create('review', $reviewID, 'Recall');

        die(js::reload('parent.parent'));
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
     * Ajax get role for review.
     *
     * @param  int     $projectID
     * @param  string  $object
     * @param  string  $reviewedBy
     * @access public
     * @return void
     */
    public function ajaxGetRole($projectID = 0, $object = '', $reviewedBy = '')
    {
        $reviewers = $this->review->getReviewerByObject($projectID, $object);
        die(html::select('reviewedBy[]', $reviewers, array_keys($reviewers), "class='form-control chosen' multiple"));
    }

    /**
     * AJAX: return reviews of a user in html select.
     *
     * @param  int    $userID
     * @param  string $id
     * @param  string $status
     * @access public
     * @return void
     */
    public function ajaxGetUserReviews($userID = '', $id = '', $status = 'all')
    {
        if($userID == '') $userID = $this->app->user->id;
        $user    = $this->loadModel('user')->getById($userID, 'id');
        $account = $user->account;

        $reviews = $this->review->getUserReviewPairs($account, 0, $status);

        if($id) die(html::select("reviews[$id]", $reviews, '', 'class="form-control"'));
        die(html::select('review', $reviews, '', 'class=form-control'));
    }

    /**
     * Review result.
     *
     * @param  int    $projectID
     * @param  int    $reviewID
     * @access public
     * @return void
     */
    public function result($projectID, $reviewID)
    {
        $this->commonAction($projectID);
        $this->app->loadLang('reviewissue');
        $review     = $this->review->getByID($reviewID);
        $reviewUser = explode(',', $review->reviewedBy);

        $resultList = array();
        if($reviewUser)
        {
            $users  = $this->loadModel('user')->getPairs('noletter');
            $result = $this->review->getResultByUserList($reviewID, false);

            foreach ($reviewUser as $user)
            {
                $resultList[$user] = new stdClass();
                if(isset($result[$user]))
                {
                    $reviewResult = $result[$user];
                    $resultList[$user]->username    = $users[$user];
                    $resultList[$user]->result      = $reviewResult->result;
                    $resultList[$user]->opinion     = $reviewResult->opinion;
                    $resultList[$user]->createdDate = $reviewResult->createdDate;
                    $resultList[$user]->consumed    = $reviewResult->consumed;
                    $resultList[$user]->issue       = $this->loadModel('reviewissue')->getUserIssue($reviewID, $user);
                }
                else
                {
                    $resultList[$user]->username    = $users[$user];
                    $resultList[$user]->result      = '';
                    $resultList[$user]->opinion     = '';
                    $resultList[$user]->createdDate = '';
                    $resultList[$user]->consumed    = '';
                    $resultList[$user]->issue       = $this->loadModel('reviewissue')->getUserIssue($reviewID, $user);
                }
            }
        }

        $this->view->title      = $this->lang->review->result;
        $this->view->resultList = $resultList;
        $this->view->review     = $review;
        $this->view->projectID  = $projectID;
        $this->display();
    }

    /**
     * Set review auditer.
     *
     * @param  int     $reviewID
     * @access public
     * @return void
     */
    public function toAudit($reviewID)
    {
        $review = $this->review->getByID($reviewID);
        if($_POST)
        {
            $this->review->toAudit($reviewID);
            if(dao::isError()) die(js::error(dao::getError()));

            $this->loadModel('action')->create('review', $reviewID, 'Toaudit', $this->post->comment, $this->post->auditedBy);
            die(js::closeModal('parent.parent'));
        }

        $this->view->title      = $this->lang->review->toAudit;
        $this->view->position[] = $this->lang->review->toAudit;
        $this->view->review     = $review;
        $this->view->users      = $this->loadModel('user')->getPairs('noclosed');
        $this->view->actions    = $this->loadModel('action')->getList('review', $reviewID);
        $this->display();
    }

    /**
     * Audit a review.
     *
     * @param  int   $reviewID
     * @access public
     * @return void
     */
    public function audit($reviewID)
    {
        $review = $this->review->getByID($reviewID);
        $this->commonAction($review->project);

        if($_POST)
        {
            $this->review->saveResult($reviewID, 'audit');

            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

            if(isset($requirementID)) $this->loadModel('action')->create('review', $requirementID, 'audited');
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => inlink('browse', "project=$review->project")));
        }

        $this->app->loadLang('reviewissue');
        $reviewer = explode(',', $review->reviewedBy);

        $resultList = array();
        if($reviewer)
        {
            $users  = $this->loadModel('user')->getPairs('noletter');
            $result = $this->review->getResultByUserList($reviewID, false);

            foreach ($reviewer as $user)
            {
                $resultList[$user] = new stdClass();
                if(isset($result[$user]))
                {
                    $reviewResult = $result[$user];
                    $resultList[$user]->username    = $users[$user];
                    $resultList[$user]->result      = $reviewResult->result;
                    $resultList[$user]->opinion     = $reviewResult->opinion;
                    $resultList[$user]->createdDate = $reviewResult->createdDate;
                    $resultList[$user]->consumed    = $reviewResult->consumed;
                    $resultList[$user]->issue       = $this->loadModel('reviewissue')->getUserIssue($reviewID, $user);
                }
                else
                {
                    $resultList[$user]->username    = $users[$user];
                    $resultList[$user]->result      = '';
                    $resultList[$user]->opinion     = '';
                    $resultList[$user]->createdDate = '';
                    $resultList[$user]->consumed    = '';
                    $resultList[$user]->issue       = $this->loadModel('reviewissue')->getUserIssue($reviewID, $user);
                }
            }
        }

        $this->setViewData($review);

        $this->view->title      = $this->lang->review->audit;
        $this->view->resultList = $resultList;
        $this->view->review     = $review;
        $this->view->result     = $this->review->getResultByUser($reviewID, 'audit');
        $this->view->issues     = $this->loadModel('reviewissue')->getIssueByReview($reviewID, $review->project, 'audit', 'all', 'all');
        $this->view->cmcl       = $this->loadModel('cmcl')->getList();
        $this->view->typeList   = $this->lang->cmcl->typeList;
        $this->view->items      = $this->lang->cmcl->titleList;
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
    public function setEditNodeUsers($reviewID, $nodeId){
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

    public function test(){
        $nextStatus = 'waitFormalAssignReviewer';
        $begin =  helper::now();
        $reviewInfo = new stdClass();
        $endDate = $this->review->getEndDate($nextStatus,$begin, $reviewInfo);
        echo $endDate;
    }

}
