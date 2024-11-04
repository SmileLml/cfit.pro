<?php
/**
 * The model file of reviewissue module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2021 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Qiyu Xie <xieqiyu@easycorp.ltd>
 * @package     reviewissue
 * @version     $Id: model.php 5107 2020-09-09 09:46:12Z xieqiyu@easycorp.ltd $
 * @link        https://www.zentao.net
 */
class reviewissueModel extends model
{
    /**
     * Get all issue for review.
     *
     * @param  int    $projectID
     * @param  int    $reviewID
     * @param  string $browseType
     * @param  string $orderBy
     * @param  object $pager
     * @access public
     * @return object
     */
    public function getList($projectID, $reviewID, $browseType, $queryID, $orderBy, $pager)
    {
        /* 获取搜索条件的查询SQL。*/
        $reviewissueQuery = '';
        if($browseType == 'bysearch')
        {
            $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : '';
            if($query)
            {
                $this->session->set('reviewissueQuery', $query->sql);
                $this->session->set('reviewissueForm', $query->form);
            }
            if($this->session->reviewIssueQuery == false) $this->session->set('reviewIssueQuery', ' 1 = 1');
            $reviewissueQuery = $this->session->reviewissueQuery;
            //关联表相同字段歧义修改  提出阶段
            $reviewissueQuery = $this->dealSqlAmbiguous($reviewissueQuery,'t1','title');
            $reviewissueQuery = $this->dealSqlAmbiguous($reviewissueQuery,'t1','status');
            $reviewissueQuery = $this->dealSqlAmbiguous($reviewissueQuery,'t1','type');
            $reviewissueQuery = $this->dealSqlAmbiguous($reviewissueQuery,'t1','createdBy');
            $reviewissueQuery = $this->dealSqlAmbiguous($reviewissueQuery,'t1','createdDate');
            $reviewissueQuery = $this->dealSqlAmbiguous($reviewissueQuery,'t1','editBy');
            $reviewissueQuery = $this->dealSqlAmbiguous($reviewissueQuery,'t1','editDate');
            $reviewissueQuery = $this->dealSqlAmbiguous($reviewissueQuery,'t1','dealUser');
            $reviewissueQuery = $this->dealSqlAmbiguous($reviewissueQuery,'t1','meetingCode');
        }
        $reviewStatus =  'all';
        if($browseType == 'noclose'){
            $reviewStatus = 'noclose';
        }
        //$reviewIds = $this->loadModel('reviewproblem')->getReviewIdsByReviewManage($reviewStatus,0,'id_desc',0);
        $statusArray = $this->lang->reviewissue->browseStatus;
        foreach ($statusArray as $key =>$value){
            unset($statusArray['all']);
            unset($statusArray['closed']);//将已处理和已关闭归属为已验证
        }
        //数据库字段为desc等特殊字符，需要增加``进行处理，无法识别
        $order = explode('_',$orderBy);
        $first = $order[0] = "`". $order[0]."`";
        $orderBy = $first ."_".$order[1];
        unset($statusArray['noclosed']);
        $issueLists =  $this->dao->select('t1.*,t2.title as reviewtitle')->from(TABLE_REVIEWISSUE)->alias('t1')
            ->leftJoin(TABLE_REVIEW)->alias('t2')
            ->on('t1.review=t2.id')
            ->Where('t1.deleted')->eq('0')
            ->andWhere('t2.project')->eq($projectID)
            ->beginIF($browseType == 'bysearch')->andWhere($reviewissueQuery)->fi()
            ->beginIF($reviewID)->andWhere('t2.id')->eq($reviewID)->fi()
            ->beginIF($browseType == 'closed')->andWhere('t1.status')->in(['closed','resolved'])->fi()
           // ->beginIF($browseType == 'noclosed')->andWhere('t1.review')->in($reviewIds)->fi()
            ->beginIF($browseType == 'noclosed')->andWhere('t2.closeTime')->eq('0000-00-00 00:00:00')->fi()
            ->beginIF(in_array($browseType,array_keys($statusArray)))->andWhere('t1.status')->eq($browseType)->fi()
            ->beginIF($browseType == 'createdBy')->andWhere('t1.createdBy')->eq($this->app->user->account)->fi()
            ->beginIF($browseType == 'review' || $browseType == 'audit')->andWhere('t1.type')->eq($browseType)->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll();
        return $issueLists;
    }

    /**
     * Desc:处理连表sql查询时共有字段无法识别的问题
     * Date: 2022/6/16
     * Time: 17:08
     *
     * @param string $query where条件
     * @param string $alias 别名
     * @param string $field 字段
     * @return string
     *
     */
    public function dealSqlAmbiguous($query ='',$alias='',$field = '')
    {
        if(strpos($query,  "`".$field."`") !== false)
        {
            $query = str_replace("`".$field."`", $alias.".`".$field."`", $query);
        }
        return $query;
    }

    /**
     * Desc: 获取reviewissue数据
     * Date: 2022/4/26
     * Time: 14:58
     *
     * @param $projectID
     * @param $reviewID
     * @param $status
     * @param $orderBy
     * @param $pager
     * @return mixed
     *
     */
    public function getReviewissueList($projectID, $reviewID, $status, $orderBy)
    {
        $statusArray = $this->lang->reviewissue->browseStatus;
        foreach ($statusArray as $key =>$value){
            unset($statusArray['all']);
            unset($statusArray['closed']);//将已处理和已关闭归属为已验证
        }
        $reviewissueQuery = '';
        if($status == 'bysearch'){
            $reviewissueQuery =$this->session->reviewissueQuery;
        }

        //数据库字段为desc等特殊字符，需要增加``进行处理，无法识别
        $order = explode('_',$orderBy);
        $first = $order[0] = "`". $order[0]."`";
        $orderBy = $first ."_".$order[1];
        return $this->dao->select('t1.*')->from(TABLE_REVIEWISSUE)->alias('t1')
            ->leftJoin(TABLE_REVIEW)->alias('t2')
            ->on('t1.review=t2.id')
            ->Where('t1.deleted')->eq('0')
            ->andWhere('t1.project')->eq($projectID)
            ->beginIF($status == 'bysearch')->andWhere($reviewissueQuery)->fi()
            ->beginIF($reviewID == 0)->andWhere('t1.review')->ne($reviewID)->fi()
            ->beginIF($reviewID != 0)->andWhere('t1.review')->eq($reviewID)->fi()
            ->beginIF($status == 'closed')->andWhere('t1.status')->in(['closed','resolved'])->fi()
            ->beginIF($status == 'noclosed')->andWhere('t2.closeTime')->eq('0000-00-00 00:00:00')->fi()
            ->beginIF($status != 'noclosed' && in_array($status,array_keys($statusArray)))->andWhere('t1.status')->eq($status)->fi()
            ->beginIF($status == 'review' || $status == 'audit')->andWhere('t1.type')->eq($status)->fi()
            ->orderBy($orderBy)
            ->fetchAll();
    }

    /**
     * Desc: 根据项目代号获取评审标题列表
     * Date: 2022/4/26
     * Time: 15:11
     *
     * @param $mark
     * @return mixed
     *
     */
    public function getReviewListByCode($mark)
    {
        return $this->dao->select('t2.id,t2.project,t2.title')->from(TABLE_PROJECTPLAN)->alias('t1')
            ->leftJoin(TABLE_REVIEW)->alias('t2')
            ->on('t1.project=t2.project')
            ->Where('t2.deleted')->eq('0')
            ->andWhere('t1.mark')->eq($mark)
            ->andWhere('t2.status')->notin('pass,fail,drop,reviewpass')
            ->orderBy('t2.id_desc')
            ->fetchPairs();
    }

    /**
     * Desc:处理问题
     * Date: 2022/4/18
     * Time: 16:46
     *
     *
     */
    public function updateResolved($issueID = 0)
    {
        $oldIssue = $this->getByID($issueID);
        $data = fixer::input('post')
            ->stripTags($this->config->reviewissue->editor->resolved['id'], $this->config->allowedTags)
            ->get();
        foreach(explode(',', $this->config->reviewissue->resolved->requiredFields) as $requiredField)
        {
            if(!isset($_POST[$requiredField]) or strlen(trim($_POST[$requiredField])) == 0)
            {
                $fieldName = $requiredField;
                if(isset($this->lang->reviewissue->$requiredField)) $fieldName = $this->lang->reviewissue->$requiredField;

                dao::$errors[] = sprintf($this->lang->error->notempty, $fieldName);
                if(dao::isError()) return false;
            }
        }
        $activeStatusArr = $this->config->reviewissue->activeStatusArr;//已采纳、部分采纳
        $repeatStatusArr = $this->config->reviewissue->repeatStatusArr;//已重复、未采纳、无需修改
        $closedStatusArr = $this->config->reviewissue->closedStatusArr;//已验证
        $failedStatusArr = $this->config->reviewissue->failedStatusArr;//验证未通过
        $createStatusArr = $this->config->reviewissue->createStatusArr;//已新建

        if(in_array($data->status,$activeStatusArr) && empty($data->validation)){
            dao::$errors[] = sprintf($this->lang->error->notempty, "验证人员");
            if(dao::isError()) return false;
        }
        if(in_array($data->status,$failedStatusArr) && empty($data->resolutionBy)){
            dao::$errors[] = sprintf($this->lang->error->notempty, "解决人员");
            if(dao::isError()) return false;
        }

        $today = helper::today();
        $this->dao->update(TABLE_REVIEWISSUE)
            ->set('status')->eq($data->status)
            ->set('dealDesc')->eq($data->dealDesc)
            ->set('changelog')->eq($data->changelog)
            ->set('dealOwner')->eq($this->app->user->account)
            ->set('dealDate')->eq($today)
            ->beginIF(in_array($data->status,$activeStatusArr))->set('dealUser')->eq($data->validation)->set('resolutionBy')->eq($this->app->user->account)->set('resolutionDate')->eq($today)->fi()
            ->beginIF(in_array($data->status,$repeatStatusArr))->set('dealUser')->eq('')->set('resolutionBy')->eq($this->app->user->account)->set('resolutionDate')->eq($today)->fi()
            ->beginIF(in_array($data->status,$closedStatusArr))->set('dealUser')->eq('')->set('validation')->eq($this->app->user->account)->set('verifyDate')->eq($today)->fi()
            ->beginIF(in_array($data->status,$failedStatusArr))->set('dealUser')->eq($data->resolutionBy)->set('validation')->eq($this->app->user->account)->set('verifyDate')->eq($today)->fi()
            ->where('id')->eq($issueID)
            ->exec();

        // 查询待验证人员
        $reviewInfo = $this->loadModel('review')->getByID($oldIssue->review);
        $reviewers = $this->review->getReviewVerifyPendingUsers($reviewInfo->id, $reviewInfo->version);

        // 当问题提出人在待验证人员中时流转主流程
        $isEditReview = false; //是否需要修改审核主流程
        if(in_array($oldIssue->raiseBy,explode(',',$reviewers))){
            $count = new stdClass();$last = 0;
            // 如果有验证人员且非兜底(reviewers为多个说明非兜底)
            if(!empty($reviewers) && $reviewers != $oldIssue->raiseBy){
                // 非兜底人员查询自己提出的问题
                $count = $this->dao->select('count(1) as count')->from(TABLE_REVIEWISSUE)
                    ->where('review')->eq($reviewInfo->id)
                    ->andWhere('raiseBy')->eq($oldIssue->raiseBy)
                    ->andWhere('status')->in($this->lang->reviewissue->checkPassArr)
                    ->andWhere('deleted')->eq('0')
                    ->fetch();
            }elseif(!empty($reviewers) && $reviewers == $oldIssue->raiseBy){
                //兜底人员(查询该评审所有未处理问题)
                $count = $this->dao->select('count(1) as count')->from(TABLE_REVIEWISSUE)
                    ->where('review')->eq($reviewInfo->id)
                    ->andWhere('status')->in($this->lang->reviewissue->checkPassArr)
                    ->andWhere('deleted')->eq('0')
                    ->fetch();
                $last = 1; // 兜底人员
            }

            // 当所有问题都已处理时
            if($count->count == 0){
                // 根据是否有验证未通过的问题判断是通过还是不通过
                $failed = $this->loadModel('reviewproblem')->getReviewIssueCountByUser($oldIssue->review, $oldIssue->raiseBy, $last,'failed');
                $result = $failed == 0 ? 'pass' : 'reject';

//                //流转主流程当前验证人处理结果
//                $this->loadModel('review')->reviewVerify($reviewInfo->id, $result, $oldIssue->raiseBy);
                $isEditReview = true;
            }
        }

        if(in_array($data->status,$activeStatusArr)){
            $data->dealUser = $data->validation;
            $data->resolutionBy = $this->app->user->account;
            $data->resolutionDate = $today;
            unset($data->validation);
        }
        if(in_array($data->status,$failedStatusArr)){
            $data->dealUser = $data->resolutionBy;
            $data->validation = $this->app->user->account;
            $data->verifyDate = $today;
            unset($data->resolutionBy);
        }
        if(in_array($data->status,$repeatStatusArr)){
            unset($data->validation);unset($data->resolutionBy);
            $data->dealUser = '';
            $data->resolutionBy = $this->app->user->account;
            $data->resolutionDate = $today;
        }
        if(in_array($data->status,$closedStatusArr)){
            unset($data->validation);unset($data->resolutionBy);
            $data->dealUser = '';
            $data->validation = $this->app->user->account;
            $data->verifyDate = $today;
        }
        if(in_array($data->status,$createStatusArr)){
            unset($data->resolutionBy);
        }

        if(!dao::isError())
        {
            $this->file->updateObjectID($this->post->uid, $issueID, 'reviewissue');
            $logChange = common::createChanges($oldIssue, $data);
            $actionID = $this->loadModel('action')->create('reviewissue', $issueID, 'Resolved');
            $this->action->logHistory($actionID, $logChange);

            if($isEditReview){
                //流转主流程当前验证人处理结果
                $this->loadModel('review')->reviewVerify($reviewInfo->id, $result, $oldIssue->raiseBy);
            }
            return $logChange;
        }
        return false;
    }

    /**
     * Solve the issue.
     *
     * @param  int    $issueID
     * @param  string $status
     * @param  string $resolution
     * @access public
     * @return void
     */
    public function updateStatus($issueID = 0, $status, $resolution = '', $changelog = '')
    {
        if($status == 'resolved')
        {
            foreach(explode(',', $this->config->reviewissue->resolved->requiredFields) as $requiredField)
            {
                if(!isset($_POST[$requiredField]) or strlen(trim($_POST[$requiredField])) == 0)
                {
                    $fieldName = $requiredField;
                    if(isset($this->lang->reviewissue->$requiredField)) $fieldName = $this->lang->reviewissue->$requiredField;

                    dao::$errors[] = sprintf($this->lang->error->notempty, $fieldName);
                    if(dao::isError()) return false;
                }
            }
        }

        $this->dao->update(TABLE_REVIEWISSUE)
            ->set('status')->eq($status)
            ->beginIF($status == 'resolved')->set('resolution')->eq($resolution)->set('resolutionDate')->eq(helper::today())->set('resolutionBy')->eq($this->app->user->account)->fi()
            ->beginIF($status == 'resolved')->set('changelog')->eq($changelog)->set('resolutionDate')->eq(helper::today())->set('resolutionBy')->eq($this->app->user->account)->fi()
            ->where('id')->eq($issueID)
            ->exec();
    }

    /**
     * Get information by id.
     *
     * @param  int   $issueID
     * @return array
     */
    public function getByID($issueID)
    {
        $issue = $this->dao->select('t1.*, t2.id as reviewID, t2.title as reviewTitle, t2.type as reviewType, t2.status as reviewStatus, t2.createdBy as reviewCreatedBy')->from(TABLE_REVIEWISSUE)->alias('t1')
            ->leftJoin(TABLE_REVIEW)->alias('t2')
            ->on('t1.review=t2.id')
            ->where('t1.id')->eq($issueID)
            ->andWhere('t1.deleted')->eq(0)
            ->fetch();

        $issue = $this->loadModel('file')->replaceImgURL($issue, 'desc');
        $issue->files = $this->loadModel('file')->getByObject('reviewissue', $issue->id);
        return $issue;
    }

    /**
     * Get all issue for the assigned review.
     *
     * @param  int    $reviewID
     * @param  int    $projectID
     * @param  string $type
     * @param  string $status
     * @param  string $scope
     * @access public
     * @return object
     */
    public function getIssueByReview($reviewID)
    {
        return $this->dao->select('*')->from(TABLE_REVIEWISSUE)
            ->where('review')->eq($reviewID)
            ->andWhere('deleted')->eq(0)
            ->fetchAll('id');
    }

    /**
     * 获得用户提出评审问题的数量
     *
     * @param $reviewID
     * @param $account
     * @return mixed
     */
    public function getIssueCountByUser($reviewID, $account){
        $count = 0;
        if(!($reviewID && $account)){
            return $count;
        }

        $ret = $this->dao->select('count(id) as count')->from(TABLE_REVIEWISSUE)
            ->where('review')->eq($reviewID)
            ->andWhere('deleted')->eq('0')
            ->andWhere("(createdBy ='{$account}' OR raiseBy ='{$account}')")
            ->fetch();
        if($ret){
            $count = $ret->count;
        }
        return $count;
    }

    /**
     * Add a issue to the review.
     *
     * @access public
     * @return int|bool
     */
    public function create($projectID)
    {
        $data = fixer::input('post')
            ->add('status', 'create')
            ->add('createdBy', $this->app->user->account)
            ->add('createdDate', date('Y-m-d'))
            //->add('raiseBy', $this->app->user->account)
            //->add('raiseDate', date('Y-m-d'))
            ->add('project', $projectID)
            ->remove('uid,files')
            ->stripTags($this->config->reviewissue->editor->create['id'], $this->config->allowedTags)
            ->get();
        $data->dealUser = '';
        $reviewInfo = $this->loadModel('review')->getById($data->review);
        if($reviewInfo){
            $data->dealUser = $reviewInfo->createdBy ?? '';
            $data->meetingCode = $reviewInfo->meetingCode ?? '';
            $data->project = $reviewInfo->project  ?? 0;
        }
        if(!isset($data->type)){
            $type = $this->dealType($data->review);
            $data->type = $type;
        }
        $data = $this->loadModel('file')->processImgURL($data, $this->config->reviewissue->editor->create['id'], $this->post->uid);
        $this->dao->insert(TABLE_REVIEWISSUE)->data($data)
            ->batchCheck($this->config->reviewissue->create->requiredFields, 'notempty')
            ->autoCheck()
            ->exec();

        if(!dao::isError()) 
        {
            $issueID = $this->dao->lastInsertID();
            $this->loadModel('file')->updateObjectID($this->post->uid, $issueID, 'reviewissue');

            //是否允许上传附件
            $isAllowUploadFile = $this->getIsAllowUploadFile($reviewInfo->type);
            if($isAllowUploadFile){
                $this->file->saveUpload('reviewissue', $issueID);
            }
            return $issueID;
        }
        return false;
    }

    /**
     * 是否允许上传附件
     *
     * @param $reviewType
     * @return bool
     */
    public function getIsAllowUploadFile($reviewType){
        $isAllowUploadFile = false;
        if($reviewType == 'dept'){
            $isAllowUploadFile = true;
        }
        return $isAllowUploadFile;
    }

    /**
     * Desc:；流转状态为在线平时也能中和会议评审的处理
     * Date: 2022/7/21
     * Time: 16:13
     *
     *
     */
    public function dealType($reviewID){
        $type = '';
        $reviewStatus = $this->loadModel('review')->getStatusById($reviewID);
        if(in_array($reviewStatus->status,["waitFormalReview","formalReviewing"])){
            $type = 'online';
        }
        if(in_array($reviewStatus->status,["waitMeetingReview","meetingReviewing"])){
            $type = 'meeting';
        }
        return $type;
    }


    /**
     * Update a issue.
     *
     * @access public
     * @return array|bool
     */
    public function update($issueID)
    {
        $oldIssue = $this->getByID($issueID);
        $data = fixer::input('post')
            ->remove('uid,files')
            ->stripTags($this->config->reviewissue->editor->edit['id'], $this->config->allowedTags)
            ->get();

        //已验证
        if($data->status == 'closed'){
            $data->verifyDate = date('Y-m-d');
        }

        $meetingCode = $this->loadModel('review')->getMeetingById($data->review);
        if($meetingCode){
            $data->meetingCode = $meetingCode[$data->review] ?? '';
        }

        $data = $this->loadModel('file')->processImgURL($data, $this->config->reviewissue->editor->create['id'], $this->post->uid);
        $data->editBy = $this->app->user->account;
        $data->editDate = date('Y-m-d');
        $this->dao->update(TABLE_REVIEWISSUE)->data($data)->where('id')->eq($issueID)->batchCheck($this->config->reviewissue->edit->requiredFields, 'notempty')->autoCheck()->exec();

        if(!dao::isError()) 
        {
            $this->file->updateObjectID($this->post->uid, $issueID, 'reviewissue');
            $issueInfo = $this->getByID($issueID);
            $isAllowUploadFile = $this->getIsAllowUploadFile($issueInfo->reviewType);
            if($isAllowUploadFile){
                $this->file->saveUpload('reviewissue', $issueID);
            }
            return common::createChanges($oldIssue, $data);
        }
        return false;
    }

    /**
     * Access to review data based on status and results.
     *
     * @param  int    $projectID
     * @param  array  $status
     * @param  array  $result
     * @param  string $orderBy
     * @param  object $pager
     * @access public
     * @return object
     */
    public function getReviewList($projectID, $status, $result, $orderBy, $pager = null)
    {
        return $this->dao->select('t1.*,t2.category')->from(TABLE_REVIEW)->alias('t1')
            ->leftJoin(TABLE_OBJECT)->alias('t2')
            ->on('t1.object=t2.id')
            ->where('t1.deleted')->eq(0)
            ->andWhere('t1.project')->eq($projectID)
            ->andWhere('t1.status')->in($status)
            ->andWhere('t1.result')->in($result)
            ->page($pager)
            ->orderBy($orderBy)
            ->fetchAll();
    }

    /**
     * Project: chengfangjinke
     * Method: getReviewPairs
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/9
     * Time: 7:48
     * Desc: This is the code comment. This method is called getReviewPairs.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $projectID
     * @return mixed
     */
    public function getReviewPairs($projectID)
    {
        return $this->dao->select('id, title')->from(TABLE_REVIEW)
//            ->where('deleted')->eq(0)
            ->where('project')->eq($projectID)
            ->orderBy('id_desc')
            ->fetchPairs();
    }

    /**
     * Desc:批量新建去除已删除评审
     * Date: 2022/5/7
     * Time: 10:15
     *
     * @param $projectID
     * @return mixed
     *
     */
    public function getReviewBatchCreate($projectID)
    {
        return $this->dao->select('id, title')->from(TABLE_REVIEW)->where('deleted')->eq(0)->andWhere('project')->eq($projectID)->orderBy('id_desc')->fetchPairs();
    }

    /**
     * Desc:批量新建去除已删除评审
     * Date: 2022/5/7
     * Time: 10:15
     *
     * @param $projectID
     * @return mixed
     *
     */
    public function getReviewBatchCreatess($meetingCode)
    {
        return $this->dao->select('id, title')->from(TABLE_REVIEW)->where('deleted')->eq(0)->andWhere('meetingCode')->eq($meetingCode)->orderBy('id_desc')->fetchPairs();

    }

    /**
     * Stage of project review.
     *
     * @param  int    $reviewID
     * @access public
     * @return array
     */
    public function getReviewStage($reviewID)
    {
        $review = $this->loadModel('review')->getByID($reviewID);
        $stages = array();
        if(!empty($review))
        {
            $this->loadModel('project');
            $project       = $this->project->getByID($review->project);
            $executionList = $this->loadModel('execution')->getByProject($project->id);
            foreach($executionList as $item) $stages[$item->id] = $item->name;
        }
        return $stages;
    }

    /**
     * Access to review category.
     *
     * @param  int    $reviewID
     * @access public
     * @return array
     */
    public function getReviewCategory($reviewID)
    {
        $reviews = $this->loadModel('review')->getByID($reviewID);
        $category = array();
        if(!empty($reviews))
        {
            $object       = $reviews->category;
            $checkData    = $this->loadModel('reviewcl')->getList($object);
            $categoryList = $this->lang->reviewcl->categoryList;
            foreach($checkData as $object => $check) $category[$object] = $categoryList->$object;
        }
        return $category;
    }

    /**
     * Access to review checklists.
     *
     * @param  int    $reviewID
     * @param  string $type
     * @access public
     * @return array
     */
    public function getReviewCheck($reviewID, $type)
    {
        $checkList = array();
        $reviews = $this->loadModel('review')->getByID($reviewID);
        if(!empty($reviews))
        {
            $checkData = $this->loadModel('reviewcl')->getList($reviews->category);
            foreach ($checkData as $category => $check)
            {
                if($category == $type) foreach ($check as $item) $checkList[$item->id] = $item->title;
            }
        }
        return $checkList;
    }

    /**
     * Get issue created by the specified user.
     *
     * @param  int    $reviewID
     * @param  string $createdBy
     * @access public
     * @return object
     */
    public function getUserIssue($reviewID, $createdBy)
    {
        return $this->dao->select('*')->from(TABLE_REVIEWISSUE)
            ->where('deleted')->eq(0)
            ->andWhere('review')->eq($reviewID)
            ->andWhere('createdBy')->eq($createdBy)
            ->orderBy('id_desc')
            ->fetchAll();
    }

    /**
     * Get review records.
     *
     * @param  int    $projectID
     * @param  string $reviewID
     * @param  string $browseType
     * @access public
     * @return string
     */
    public function getReviewRecord($projectID, $reviewID, $browseType)
    {
        $reviewList = $this->loadModel('review')->getReviewByProjectId($projectID);
        $allLink = helper::createLink('reviewissue', 'issue', "project=$projectID&reviewID=0&status=$browseType");

        $listLink   = '';
        foreach($reviewList as $key => $review)
        {
            $reviewLink = helper::createLink('reviewissue', 'issue', "project=$projectID&reviewID=$key&status=$browseType");
            $listLink .= html::a(sprintf($reviewLink), '<i class="icon icon-folder-outline"></i>' . $review->title);
        }
        $html  = '<div class="table-row"><div class="table-col col-left"><div class="list-group">' . $listLink . '</div>';
        $html .= '<div class="col-footer">';
        $html .= html::a(sprintf($allLink,''), '<i class="icon icon-cards-view muted"></i>' . $this->lang->exportTypeList['all'], '', 'class="not-list-item"');
        $html .= '</div></div>';
        $html .= '<div class="table-col col-right"><div class="list-group"></div>';

        return $html;
    }

    /**
     * Capture all issues reviewed in the project.
     *
     * @param  int    $projectID
     * @param  string $type
     * @param  string $orderBy
     * @access public
     * @return object
     */
    public function getProjectIssue($projectID, $type, $orderBy)
    {
        return $this->dao->select('t1.id as reviewID,t1.title as reviewTitle,t2.*')->from(TABLE_REVIEW)->alias('t1')
            ->leftJoin(TABLE_REVIEWISSUE)->alias('t2')->on('t1.id=t2.review')
            ->where('t1.project')->eq($projectID)
            ->andWhere('t2.type')->eq($type)
            ->andWhere('t2.deleted')->eq(0)
            ->orderBy($orderBy)
            ->fetchAll();
    }
    /**
     * Judge button if can clickable.
     *
     * @param object $review
     * @param string $action
     * @access public
     * @return void
     */
    public static function isClickable($issue, $action)
    {
        global $app;

        $action = strtolower($action);
        if($action == 'edit'){
            if($issue->createdBy!=$app->user->account && $issue->raiseBy!=$app->user->account){
                return false;
            }
        }
        return true;
    }
    /**
     * Desc: 固定列表构建td
     * Date: 2022/4/19
     * Time: 15:08
     *
     * @param $col
     * @param $issue
     * @param $users
     * @param $reviews
     * @param $projectID
     *
     */
    public function printCell($col, $issue,$reviewID, $users,$reviews,$projectID,$status,$orderBy,$pager)
    {
        $id = $col->id;
        $params = "project=$projectID"."&issudID=$issue->id"."&reviewId=$reviewID"."&statusNew=$status"."&orderBy=$orderBy"."&recTotal=$pager->recTotal"."&recPerPage=$pager->recPerPage"."&pageID=$pager->pageID";
        if($col->show)
        {
            $class = "c-$id";
            $title  = '';
            if($id == 'title')
            {
                $class .= ' text-left';
                $title  = "title='{$issue->title}'";
            }
            if($id == 'review')
            {
                $class .= ' text-left';
                $title  = "title='{$issue->reviewtitle}'";
            }
            echo "<td class='" . $class . "' $title>";
            switch($id)
            {
                case 'id':
                    echo $issue->id;
                    break;
                case 'review':
                    echo html::a(helper::createLink('review', 'view', "reviewID=$issue->review"),'<div class="reviewTitle" title="' . $issue->reviewtitle . '">' . $issue->reviewtitle .'</div>');
                    break;
                case 'title':
                    echo html::a(helper::createLink('reviewissue', 'view', $params),'<div class="problemTitle" title="' . $issue->title . '">' . $issue->title .'</div>');
                    break;
                case 'desc':
                    echo '<div class="change" title="' . strip_tags($issue->desc) . '">' . $issue->desc .'</div>';
                    break;
                case 'type':
                    echo zget($this->lang->reviewissue->typeList, $issue->type);
                    break;
                case 'raiseBy':
                    echo zget($users, $issue->raiseBy);
                    break;
                case 'raiseDate':
                    echo $issue->raiseDate;
                    break;
                case 'status':
                    echo zget($this->lang->reviewissue->statusList, $issue->status);
                    break;
                case 'resolutionBy':
                    echo zget($users, $issue->resolutionBy);
                    break;
                case 'resolutionDate':
                    echo $issue->resolutionDate;
                    break;
                case 'dealDesc':
                    echo $issue->dealDesc;
                    break;
                case 'validation':
                    echo zget($users, $issue->validation);
                    break;
                case 'verifyDate':
                    echo $issue->verifyDate;
                    break;
                case 'meetingCode':
                    echo '<div class="meetingCode" title="' . strip_tags($issue->meetingCode) . '">' . $issue->meetingCode .'</div>';
                    break;
                case 'editBy':
                    echo zget($users, $issue->editBy);
                    break;
                case 'editDate':
                    echo $issue->editDate;
                    break;
                case 'createdBy':
                    echo zget($users, $issue->createdBy);
                    break;
                case 'createdDate':
                    echo $issue->createdDate;
                    break;
                case 'dealUser':
                    echo zget($users, $issue->dealUser);
                    break;
                case 'actions':
                    $recTotal = $pager->recTotal;
                    $recPerPage = $pager->recPerPage;
                    $pageID = $pager->pageID;
                    $param = "project=$projectID&issueID=$issue->id&source=list&review=$reviewID&status=$status&orderBy=$orderBy&recTotal=$recTotal&recPerPage=$recPerPage&pageID=$pageID";

                    $ids = $this->getAllReviewId($projectID);
                    //评审已关闭，评审问题按钮置灰
                    if(in_array($issue->review,$ids)) {
                        common::hasPriv('reviewissue','edit') ? common::printIcon('reviewissue', 'edit', $param, $issue, 'list','','','disabled','','') : '';
                        common::hasPriv('reviewissue','resolved') ? common::printIcon('reviewissue', 'resolved', $param, $issue, 'list','checked','', 'disabled','', "data-width=50%") :'';
                        common::hasPriv('reviewissue','delete') ? common::printIcon('reviewissue', 'delete',$param , $issue, 'list', 'trash', '', 'disabled', true,'', '') : '';

                    }else{
                        common::hasPriv('reviewissue','edit') ? common::printIcon('reviewissue', 'edit', $param, $issue, 'list') : '';
                        common::hasPriv('reviewissue','resolved') ? common::printIcon('reviewissue', 'resolved', $param, $issue, 'list','checked','', '','', "data-width=50%") :'';
                        common::hasPriv('reviewissue','delete') ? common::printIcon('reviewissue', 'delete',$param , $issue, 'list', 'trash', '', 'iframe', true,'', '') : '';
                    }
                }


            echo '</td>';
        }

    }

    /**
     * Desc:导出模板配置
     * Date: 2022/4/19
     * Time: 15:51
     *
     */
    public function setListValue($projectID)
    {
        $this->app->loadLang('opinion');
        $statusList        = $this->lang->reviewissue->statusList;//状态
        $typeList          = $this->lang->reviewissue->typeList;//提出阶段
        $reviewList        = $this->loadModel('review')->getReviewListByProjectId($projectID);//评审标题
        foreach ($reviewList as $value){
            $reviewArray[$value->title] = $value->title;
        }

        foreach (array_unique($reviewArray) as $id=>$value){
            $reviewArray[$id] .= "(#$id)";
        }

        $typeArray = [];
        foreach ($typeList as $id=>$value){
            $typeArray[$id] .= "$value(#$id)";
        }
        $this->post->set('reviewList',      array_values($reviewArray));
        $this->post->set('statusList',       join(',', $statusList));
        $this->post->set('typeList',       array_values($typeArray));
        $this->post->set('width', 60);

        $this->post->set('listStyle',      $this->config->reviewissue->export->listFields);
        $this->post->set('extraNum', 0);
    }

    /**
     * Desc: 构造会议编号
     * Date: 2022/7/26
     * Time: 16:44
     *
     * @return array
     *
     */
    public function getMeetingCodeList()
    {
        $getMeetingCodeList = $this->loadModel('reviewMeeting')->getMeetingCode();
        $codeList = [];
        foreach ($getMeetingCodeList as $meetingCode){
            $codeList[$meetingCode->meetingCode] = $meetingCode->meetingCode ?? '';
        }
        return $codeList;
    }

    /**
     * Desc: 获取评审标题下的会议编号
     * Date: 2022/8/22
     * Time: 16:48
     *
     * @param $reviewID
     * @return array
     *
     */
    public function getMeetingCodeByReviewID($reviewID)
    {
        $getMeetingCodeList = $this->loadModel('reviewMeeting')->getMeetingCodeByReviewID($reviewID);
        $codeList = [];
        foreach ($getMeetingCodeList as $meetingCode){
            $codeList[$meetingCode->meetingCode] = $meetingCode->meetingCode ?? '';
        }
        return $codeList;
    }

    /**
     * Desc: 导入数据
     * Date: 2022/7/27
     * Time: 17:46
     *
     *
     */
    public function createFromImport()
    {
        $this->loadModel('action');
        $this->loadModel('reviewissue');
        $this->loadModel('file');
        $data = fixer::input('post')->get();
        $this->app->loadClass('purifier', true);
        $reviewList= array();
        $line = 1;
        foreach($data->review as $key => $value)
        {
            if($value != 0) {
                $meetingCodeInfo = $this->loadModel('review')->getMeetingById($data->review[$key]);
                if($meetingCodeInfo){
                    $meetingCode = $meetingCodeInfo[$data->review[$key]] ?? '';
                }
                $reviewData = new stdclass();
                $reviewData->code = $data->code[$key];
                $reviewData->review = $data->review[$key];
                $reviewData->title = $data->title[$key];
                $reviewData->meetingCode = $meetingCode;
                $reviewData->desc = $data->desc[$key];
                $reviewData->type = $data->type[$key];
                $reviewData->raiseBy = $data->raiseBy[$key];
                $reviewData->raiseDate = $data->raiseDate[$key];
                $reviewData->status = $data->status[$key];
                $reviewData->resolutionBy = $data->resolutionBy[$key];
                $reviewData->resolutionDate = $data->resolutionDate[$key];
                $reviewData->dealDesc = $data->dealDesc[$key];
                $reviewData->validation = $data->validation[$key];
                $reviewData->verifyDate = $data->verifyDate[$key];
                $reviewData->createdBy = $this->app->user->account;
                $reviewData->createdDate = date('Y-m-d');
                //待处理人数据处理
                $createStatusArr = $this->config->reviewissue->createStatusArr;//已新建
                $activeStatusArr = $this->config->reviewissue->activeStatusArr;//已采纳、部分采纳
                $repeatStatusArr = $this->config->reviewissue->repeatStatusArr;//已重复、未采纳、无需修改
                $closedStatusArr = $this->config->reviewissue->closedStatusArr;//已验证
                $failedStatusArr = $this->config->reviewissue->failedStatusArr;//验证未通过


                $projectList = $this->loadModel('review')->getByID($data->review[$key]);
                if(in_array($data->status[$key],$createStatusArr)) $reviewData->dealUser = $projectList->createdBy;
                if(in_array($data->status[$key],$activeStatusArr)) $reviewData->dealUser = $data->raiseBy[$key];
                if(in_array($data->status[$key],$repeatStatusArr)) $reviewData->dealUser = '';
                if(in_array($data->status[$key],$closedStatusArr)) $reviewData->dealUser = '';
                if(in_array($data->status[$key],$failedStatusArr)) $reviewData->dealUser = $reviewData->resolutionBy;

                $reviewData->project = $projectList->project ?? 0;

                if (isset($this->config->reviewissue->import->requiredFields)) {
                    $requiredFields = explode(',', $this->config->reviewissue->import->requiredFields);
                    foreach ($requiredFields as $requiredField) {
                        $requiredField = trim($requiredField);
                        if (empty($reviewData->$requiredField))
                            dao::$errors[] = sprintf($this->lang->reviewissue->noRequire, $line, $this->lang->reviewissue->$requiredField);
                    }
                }
                unset($reviewData->code);
                $reviewList[] = $reviewData;
                $line++;
            }
            /*多行数据第一行评审标题未填提醒*/
            if($key == 1 && $value == 0){
                dao::$errors[] = sprintf($this->lang->reviewissue->firstRequire, $line, '评审标题');
            }
        }
        if(empty($reviewList))  die(js::alert($this->lang->reviewissue->emptyReviewMsg,true));

        if(dao::isError()) die(js::error(dao::getError()));
        foreach ($reviewList as $insertData){
            $this->dao->insert(TABLE_REVIEWISSUE)->data($insertData)->autoCheck()->exec();
            if(!dao::isError())
            {
                $reviewIssueId = $this->dao->lastInsertID();
                $this->action->create('reviewissue', $reviewIssueId, 'import', '');
            }
            if(dao::isError()) die(js::error(dao::getError()));
        }
        if($this->post->isEndPage)
        {
            unlink($this->session->fileImport);
            unset($_SESSION['fileImport']);
        }
    }

    /**
     * Desc: 批量新建问题数据
     * Date: 2022/7/27
     * Time: 17:46
     *
     * @param $projectID
     *
     */
    public function batchCreate($projectID)
    {
        $data = fixer::input('post')->get();
        $this->app->loadClass('purifier', true);
        $addDataTips= array();
        $addData= array();
        $line = [];
        //第1行必须创建
        if(empty($data->title[0])){
            die(js::alert($this->lang->reviewissue->emptyData,true));
        }else{ 
            //只填写文件名/位置，不填写判断
            foreach($data->title as $key => $value)
            {
                if(!empty($value)){
                    $titleData = $this->reviewData($data,$key,$projectID);
                    $addDataTips[$titleData['line']] = $titleData['reviewData'];
                }
                //构造数据
                $titleData = $this->reviewData($data,$key,$projectID);
                $addData[] = $titleData['reviewData'];
            }
            //只填写描述，不填写文件名/位置判断
            foreach ($data->desc as $k=>$v)
            {
                if(!empty($v)){
                    $descData = $this->reviewData($data,$k,$projectID);
                    $addDataTips[$descData['line']] = $descData['reviewData'];
                }
            }
        }
        //去除中间未填写项数据，只保存有效数据
        foreach ($addData as $i=>$item){
            if(empty($item->title)){
                unset($addData[$i]);
            }
        }

        ksort($addDataTips);

        if(!empty($addDataTips)) {
            foreach ($addDataTips as $item => $dataValue) {
                $requiredFields = explode(',', $this->config->reviewissue->beatchCreate->requiredFields);

                foreach ($requiredFields as $requiredField) {
                    $requiredField = trim($requiredField);
                    if (empty($dataValue->$requiredField)) {
                            dao::$errors[] = sprintf($this->lang->reviewissue->noRequire, $item, $this->lang->reviewissue->$requiredField);
                    }
                }
            }
        }
        if(dao::isError()) die(js::error(dao::getError()));
        foreach ($addData as $insertData){
            $this->dao->insert(TABLE_REVIEWISSUE)->data($insertData)->exec();
            if(!dao::isError())
            {
                $reviewIssueId = $this->dao->lastInsertID();
                $this->loadModel('action')->create('reviewissue', $reviewIssueId, 'Created');
            }
            if(dao::isError()) die(js::error(dao::getError()));
        }
    }


    /**
     * Desc:批量添加构造数据
     * Date: 2022/7/27
     * Time: 17:41
     *
     * @param $data
     * @param $i
     * @param $projectID
     * @return array
     *
     */
    public function reviewData($data,$i,$projectID)
    {
        $reviewData = new stdClass();
        $line = [];
        $review = $data->review[$i];
        if($review == 'ditto'){
            $review = $data->review[$i] = $data->review[$i-1];
        }
        $reviewInfo = $this->loadModel('review')->getById($review);
        $reviewData->review = $review;
        $type = $data->type[$i];
        if($type == 'ditto'){
            $type = $data->type[$i] = $data->type[$i-1];
        }
        $reviewData->type          = $type;
        $reviewData->status        = 'create';
        $reviewData->title         = $data->title[$i];
        $reviewData->meetingCode   = $reviewInfo->meetingCode;
        $reviewData->desc          = $data->desc[$i];
        $reviewData->createdBy     = $this->app->user->account;
        $reviewData->createdDate   = date('Y-m-d');
        $reviewData->raiseBy       = $data->raiseBy[$i];
        $reviewData->raiseDate     = $data->raiseDate[$i];
        $reviewData->project       = $projectID;
        $reviewData->dealUser      = $reviewInfo->createdBy ?? '';

        if(isset($this->config->reviewissue->beatchCreate->requiredFields))
        {
            $requiredFields = explode(',', $this->config->reviewissue->beatchCreate->requiredFields);
            foreach($requiredFields as $requiredField)
            {
                $requiredField = trim($requiredField);
                if(empty($reviewData->$requiredField)){
                    $line = $i+1;
                }
            }
        }
        $returnData = [
            'line'=>$line,
            'reviewData'=>$reviewData
        ];
        return $returnData;
    }

    /**
     * 获取关闭评审id
     * @param $projectid
     * @return mixed
     */
    public function getAllReviewId($projectid){
        return $this->dao->select('id')->from(TABLE_REVIEW)
            ->Where('deleted')->eq('0')
            ->andWhere('project')->eq($projectid)
            ->andWhere('status')->in('fail,drop,reviewpass')
            ->orderBy('id_desc')
            ->fetchPairs();

    }


    /**
     * Desc:搜索
     * Date: 2022/6/15
     * Time: 14:41
     *
     * @param $queryID
     * @param $actionURL
     *
     */
    public function buildSearchForm($queryID, $actionURL)
    {
        $this->config->reviewissue->search['actionURL'] = $actionURL;
        $this->config->review->search['queryID']   = $queryID;

        $this->loadModel('search')->setSearchParams($this->config->reviewissue->search);
    }

    /*
     *获得评审会议数量
     *
     *
     * @param $reviewID
     * @params $statusArray
     * @return int
     */
    public function getReviewIssueCount($reviewID, $statusArray = []){
        $count = 0;
        $ret = $this->dao->select('count(id) as total')->from(TABLE_REVIEWISSUE)
            ->where('review')->eq($reviewID)
            ->andWhere('deleted')->eq('0')
            ->beginIF(!empty($statusArray))->andWhere('status')->in($statusArray)->fi()
            ->fetch();
        if($ret){
            $count = $ret->total;
        }
        return $count;
    }

    /**
     * 获得需要处理问题的数量
     *
     * @param $reviewID
     * @return int
     */
    public function getNeedDealReviewIssueCount($reviewID){
        $count = 0;
        if(!$reviewID){
            return $count;
        }
        //无需处理问题状态
        $statusArray = $this->lang->reviewissue->noNeedDealStatusArray;
        $ret = $this->dao->select('count(id) as total')->from(TABLE_REVIEWISSUE)
            ->where('review')->eq($reviewID)
            ->andWhere('deleted')->eq('0')
            ->andWhere('status')->notin($statusArray)
            ->fetch();
        if($ret){
            $count = $ret->total;
        }
        return $count;
    }

    /**
     *获得为解决问题的用户
     *
     * @param $reviewIds
     * @return array
     */
    public function getUnDealReviewIssueUsers($reviewIds){
        $data = [];
        if(!$reviewIds){
            return $data;
        }
        //无需处理问题状态
        $statusArray = $this->lang->reviewissue->noNeedDealStatusArray;
        $ret = $this->dao->select('review, raiseBy')->from(TABLE_REVIEWISSUE)
            ->where('review')->in($reviewIds)
            ->andWhere('deleted')->eq('0')
            ->andWhere('status')->notin($statusArray)
            ->groupBy('review,raiseBy')
            ->fetchAll();
        if($ret){
            foreach ($ret as $val){
                $review   = $val->review;
                $raiseBy  = $val->raiseBy;
                $data[$review][] = $raiseBy;
            }
        }
        return $data;
    }

    /**
     * 根据评审id获得未处理问题提出人
     *
     * @param $reviewId
     * @return array
     */
    public function getUnDealIssueUsersByReviewId($reviewId){
        $data = [];
        if(!$reviewId){
            return $data;
        }
        //无需处理问题状态
        $statusArray = $this->lang->reviewissue->noNeedDealStatusArray;
        $ret = $this->dao->select('distinct raiseBy')->from(TABLE_REVIEWISSUE)
            ->where('review')->eq($reviewId)
            ->andWhere('deleted')->eq('0')
            ->andWhere('status')->notin($statusArray)
            ->groupBy('review,raiseBy')
            ->fetchAll();
        if($ret){
            $data = array_column($ret, 'raiseBy');
        }
        return $data;
    }

}
