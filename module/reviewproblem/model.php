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
class reviewproblemModel extends model
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
    public function getList($projectID,$reviewID, $browseType,$queryID,$orderBy, $pager)
    {
        /* 获取搜索条件的查询SQL。*/
        $reviewproblemQuery = '';
        if($browseType == 'bysearch')
        {
            $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : '';
            if($query)
            {
                $this->session->set('reviewproblemQuery', $query->sql);
                $this->session->set('reviewproblemForm', $query->form);
            }
            if($this->session->reviewproblemQuery == false) $this->session->set('reviewproblemQuery', ' 1 = 1');
            $reviewproblemQuery = $this->session->reviewproblemQuery;
            //关联表相同字段歧义修改  提出阶段
            $reviewproblemQuery = $this->dealSqlAmbiguous($reviewproblemQuery,'t1','title');
            $reviewproblemQuery = $this->dealSqlAmbiguous($reviewproblemQuery,'t1','status');
            $reviewproblemQuery = $this->dealSqlAmbiguous($reviewproblemQuery,'t1','type');
            $reviewproblemQuery = $this->dealSqlAmbiguous($reviewproblemQuery,'t1','createdBy');
            $reviewproblemQuery = $this->dealSqlAmbiguous($reviewproblemQuery,'t1','createdDate');
            $reviewproblemQuery = $this->dealSqlAmbiguous($reviewproblemQuery,'t1','editBy');
            $reviewproblemQuery = $this->dealSqlAmbiguous($reviewproblemQuery,'t1','editDate');
            $reviewproblemQuery = $this->dealSqlAmbiguous($reviewproblemQuery,'t1','dealUser');
            $reviewproblemQuery = $this->dealSqlAmbiguous($reviewproblemQuery,'t1','meetingCode');
        }
        $reviewStatus =  'all';
        if($browseType == 'noclose'){
            $reviewStatus = 'noclose';
        }
        if($browseType == 'myNoclose'){
            $reviewStatus = 'myNoclose';
        }
        $reviewIds = $this->getReviewIdsByReviewManage($reviewStatus,0,'id_desc',0);
        //数据库字段为desc等特殊字符，需要增加``进行处理，无法识别
        $order = explode('_',$orderBy);
        $first = $order[0] = "`". $order[0]."`";
        $orderBy = $first ."_".$order[1];
        return $this->dao->select('t1.*,t2.title as reviewtitle')->from(TABLE_REVIEWISSUE)->alias('t1')
            ->leftJoin(TABLE_REVIEW)->alias('t2')
            ->on('t1.review=t2.id')
            ->where('t1.deleted')->eq('0')
            ->andWhere('t1.review')->in($reviewIds)
            ->beginIF($browseType == 'bysearch')->andWhere($reviewproblemQuery)->fi()
            ->beginIF($reviewID)->andWhere('t2.id')->eq($reviewID)->fi()
//            ->beginIF($browseType == 'noclose')->andWhere('t1.status')->in(['closed', 'resolved'])->fi()
            ->beginIF($browseType == 'wait' || $browseType == 'myNoclose')->andWhere('t1.dealUser')->eq($this->app->user->account)->fi()
            ->beginIF($browseType == 'created')->andWhere('t1.createdBy')->eq($this->app->user->account)->fi()
            ->beginIF($browseType == 'resolved')->andWhere('t1.resolutionBy')->eq($this->app->user->account)->fi()
            ->beginIF($browseType == 'verification')->andWhere('t1.validation')->eq($this->app->user->account)->fi()
            ->orderBy($orderBy)
            ->beginIF($browseType != 'myNoclose')->page($pager)
            ->fetchAll();
    }
    public function getMeetingList($meetingCode,$projectID, $reviewID, $browseType,$queryID,$orderBy, $pager)
    {
        /* 获取搜索条件的查询SQL。*/
        $reviewproblemQuery = '';
        if($browseType == 'bysearch')
        {
            $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : '';
            if($query)
            {
                $this->session->set('reviewproblemQuery', $query->sql);
                $this->session->set('reviewproblemForm', $query->form);
            }
            if($this->session->reviewproblemQuery == false) $this->session->set('reviewproblemQuery', ' 1 = 1');
            $reviewproblemQuery = $this->session->reviewproblemQuery;
            //关联表相同字段歧义修改  提出阶段
            $reviewproblemQuery = $this->dealSqlAmbiguous($reviewproblemQuery,'t1','title');
            $reviewproblemQuery = $this->dealSqlAmbiguous($reviewproblemQuery,'t1','status');
            $reviewproblemQuery = $this->dealSqlAmbiguous($reviewproblemQuery,'t1','type');
            $reviewproblemQuery = $this->dealSqlAmbiguous($reviewproblemQuery,'t1','createdBy');
            $reviewproblemQuery = $this->dealSqlAmbiguous($reviewproblemQuery,'t1','createdDate');
            $reviewproblemQuery = $this->dealSqlAmbiguous($reviewproblemQuery,'t1','editBy');
            $reviewproblemQuery = $this->dealSqlAmbiguous($reviewproblemQuery,'t1','editDate');
            $reviewproblemQuery = $this->dealSqlAmbiguous($reviewproblemQuery,'t1','dealUser');
            $reviewproblemQuery = $this->dealSqlAmbiguous($reviewproblemQuery,'t1','meetingCode');
        }
        $statusArray = $this->lang->reviewproblem->newbrowseStatus;
        foreach ($statusArray as $key =>$value){
            unset($statusArray['all']);
            unset($statusArray['closed']);//将已处理和已关闭归属为已验证
        }
        //数据库字段为desc等特殊字符，需要增加``进行处理，无法识别
        $order = explode('_',$orderBy);
        $first = $order[0] = "`". $order[0]."`";
        $orderBy = $first ."_".$order[1];
        $list= $this->dao->select('t1.*,t2.title as reviewtitle')->from(TABLE_REVIEWISSUE)->alias('t1')
            ->leftJoin(TABLE_REVIEW)->alias('t2')
            ->on('t1.review=t2.id')
            ->Where('t1.deleted')->eq('0')
            ->andWhere('t1.meetingCode')->eq($meetingCode)
/*            ->beginIF($browseType == 'bysearch')->andWhere($reviewproblemQuery)->fi()
            ->beginIF($browseType == 'noclose')->andWhere('t1.status')->in(['closed', 'resolved'])->fi()
            ->beginIF($browseType == 'wait')->andWhere('t1.dealUser')->eq($this->app->user->account)->fi()
            ->beginIF($browseType == 'created')->andWhere('t1.createdBy')->eq($this->app->user->account)->fi()
            ->beginIF($browseType == 'resolved')->andWhere('t1.resolutionBy')->eq($this->app->user->account)->fi()
            ->beginIF($browseType == 'verification')->andWhere('t1.validation')->eq($this->app->user->account)->fi()*/
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll();
        return $list;
    }

    /**
     * Desc: 获取评审管理，评审列表中有权限的列表数据
     * Date: 2022/8/1
     * Time: 18:05
     *
     *
     */
    public function getReviewIdsByReviewManage($status = 'all',$queryID,$orderBy,$pager)
    {
        $reviewManageList = $this->loadModel('reviewmanage')->reviewList($status, $queryID, $orderBy, $pager);
        $reviewIds = [];
        foreach ($reviewManageList as $id){
            $reviewIds[] = $id->id;
        }
        return $reviewIds;
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
    public function getReviewissueList($projectID, $reviewID, $browseType, $orderBy)
    {
        $reviewproblemQuery = '';
        if($browseType == 'bysearch'){
            $reviewproblemQuery =$this->session->reviewproblemQuery;
        }
        $reviewIds = $this->getReviewIdsByReviewManage('all',0,'id_desc',0);
        //数据库字段为desc等特殊字符，需要增加``进行处理，无法识别
        $order = explode('_',$orderBy);
        $first = $order[0] = "`". $order[0]."`";
        $orderBy = $first ."_".$order[1];
        return $this->dao->select('*')->from(TABLE_REVIEWISSUE)
            ->where('deleted')->eq('0')
            ->andWhere('review')->in($reviewIds)
            ->beginIF($browseType == 'bysearch')->andWhere($reviewproblemQuery)->fi()
            ->beginIF($reviewID)->andWhere('review')->eq($reviewID)->fi()
            ->beginIF($browseType == 'wait')->andWhere('dealUser')->eq($this->app->user->account)->fi()
            ->beginIF($browseType == 'created')->andWhere('createdBy')->eq($this->app->user->account)->fi()
            ->beginIF($browseType == 'resolved')->andWhere('resolutionBy')->eq($this->app->user->account)->fi()
            ->beginIF($browseType == 'verification')->andWhere('validation')->eq($this->app->user->account)->fi()
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
            ->stripTags($this->config->reviewproblem->editor->resolved['id'], $this->config->allowedTags)
            ->get();
        foreach(explode(',', $this->config->reviewproblem->resolved->requiredFields) as $requiredField)
        {
            if(!isset($_POST[$requiredField]) or strlen(trim($_POST[$requiredField])) == 0)
            {
                $fieldName = $requiredField;
                if(isset($this->lang->reviewproblem->$requiredField)) $fieldName = $this->lang->reviewproblem->$requiredField;

                dao::$errors[] = sprintf($this->lang->error->notempty, $fieldName);
                if(dao::isError()) return false;
            }
        }
        $activeStatusArr = $this->config->reviewproblem->activeStatusArr;//已采纳、部分采纳
        $repeatStatusArr = $this->config->reviewproblem->repeatStatusArr;//已重复、未采纳、无需修改
        $closedStatusArr = $this->config->reviewproblem->closedStatusArr;//已验证
        $failedStatusArr = $this->config->reviewproblem->failedStatusArr;//验证未通过
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
            $count = new stdClass();
            $last = 0;
            // 如果有验证人员且非兜底(reviewers为多个说明非兜底)
            if(!empty($reviewers) && $reviewers != $oldIssue->raiseBy){
                // 非兜底人员查询自己提出的问题
                $count = $this->dao->select('count(1) as count')->from(TABLE_REVIEWISSUE)
                    ->where('review')->eq($reviewInfo->id)
                    ->andWhere('raiseBy')->eq($oldIssue->raiseBy)
                    ->andWhere('status')->in($this->lang->reviewproblem->checkPassArr)
                    ->andWhere('deleted')->eq('0')
                    ->fetch();
            }elseif(!empty($reviewers) && $reviewers == $oldIssue->raiseBy){
                //兜底人员(查询该评审所有未处理问题)
                $count = $this->dao->select('count(1) as count')->from(TABLE_REVIEWISSUE)
                    ->where('review')->eq($reviewInfo->id)
                    ->andWhere('status')->in($this->lang->reviewproblem->checkPassArr)
                    ->andWhere('deleted')->eq('0')
                    ->fetch();
                $last = 1; // 兜底人员
            }
            // 当所有问题都已处理时
            if($count->count == 0){
                $failed = $this->getReviewIssueCountByUser($oldIssue->review, $oldIssue->raiseBy, $last,'failed');
                $result = $failed == 0 ? 'pass' : 'reject';

                $isEditReview = true;

//                //流转主流程当前验证人处理结果(放到后面因为日志写入顺序颠倒)
//                $this->loadModel('review')->reviewVerify($reviewInfo->id, $result, $oldIssue->raiseBy);
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
            $this->file->updateObjectID($this->post->uid, $issueID, 'reviewproblem');
            //记录日志
            $logChange =  common::createChanges($oldIssue, $data);
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
            foreach(explode(',', $this->config->reviewproblem->resolved->requiredFields) as $requiredField)
            {
                if(!isset($_POST[$requiredField]) or strlen(trim($_POST[$requiredField])) == 0)
                {
                    $fieldName = $requiredField;
                    if(isset($this->lang->reviewproblem->$requiredField)) $fieldName = $this->lang->reviewproblem->$requiredField;

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
        $issue = $this->dao->select('t1.*, t2.id as reviewID, t2.title as reviewTitle,t2.status as reviewStatus,t3.mark, t2.createdBy as reviewCreatedBy')->from(TABLE_REVIEWISSUE)->alias('t1')
            ->leftJoin(TABLE_REVIEW)->alias('t2')->on('t1.review=t2.id')
            ->leftJoin(TABLE_PROJECTPLAN)->alias('t3')->on('t1.project=t3.project')
            ->where('t1.id')->eq($issueID)
            ->andWhere('t1.deleted')->eq(0)
            ->fetch();

        $issue = $this->loadModel('file')->replaceImgURL($issue, 'desc');
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
            ->stripTags($this->config->reviewproblem->editor->create['id'], $this->config->allowedTags)
            ->get();
        $data->dealUser = '';
        $reviewInfo = $this->loadModel('review')->getById($data->review);
        if(!empty($reviewInfo)){
            $data->dealUser = $reviewInfo->createdBy ?? '';
            $data->meetingCode = $reviewInfo->meetingCode ?? '';
            $data->project = $reviewInfo->project  ?? 0;
        }
        if(!isset($data->type)){
            $type = $this->dealType($data->review);
            $data->type = $type;
        }
        $data = $this->loadModel('file')->processImgURL($data, $this->config->reviewproblem->editor->create['id'], $this->post->uid);
        $this->dao->insert(TABLE_REVIEWISSUE)->data($data)
            ->batchCheck($this->config->reviewproblem->create->requiredFields, 'notempty')
            ->autoCheck()
            ->exec();

        if(!dao::isError()) 
        {
            $issueID = $this->dao->lastInsertID();
            $this->file->updateObjectID($this->post->uid, $issueID, 'reviewproblem');

            //是否允许上传附件(尝试和reviewissue用同一套代码维护)
            $isAllowUploadFile = $this->loadModel('reviewissue')->getIsAllowUploadFile($reviewInfo->type);
            if($isAllowUploadFile){
                $this->file->saveUpload('reviewissue', $issueID);
            }
            return $issueID;
        }
        return false;
    }

    /**
     * Desc:；流转状态为在线评审和会议评审的处理
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
            ->stripTags($this->config->reviewproblem->editor->edit['id'], $this->config->allowedTags)
            ->get();
        //已验证
        if($data->status == 'closed'){
            $data->verifyDate = date('Y-m-d');
        }
        $meetingCode = $this->loadModel('review')->getMeetingById($data->review);
        if($meetingCode){
            $data->meetingCode = $meetingCode[$data->review] ?? '';
        }
        $data = $this->loadModel('file')->processImgURL($data, $this->config->reviewproblem->editor->create['id'], $this->post->uid);
        $data->editBy = $this->app->user->account;
        $data->editDate = date('Y-m-d');
        $this->dao->update(TABLE_REVIEWISSUE)->data($data)->where('id')->eq($issueID)->batchCheck($this->config->reviewproblem->edit->requiredFields, 'notempty')->autoCheck()->exec();

        if(!dao::isError()) 
        {
            $this->file->updateObjectID($this->post->uid, $issueID, 'reviewproblem');
            $issueInfo         = $this->loadModel('reviewissue')->getByID($issueID);
            $isAllowUploadFile = $this->loadModel('reviewissue')->getIsAllowUploadFile($issueInfo->reviewType);
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
     * Desc:获取评审标题数据
     * Date: 2022/5/7
     * Time: 10:15
     *
     * @param $reviewID
     * @return mixed
     *
     */
    public function getReviewBatchCreate()
    {
        $reviewIds = $this->getReviewIdsByReviewManage('all',0,'id_desc',0);
        return $this->dao->select('id,title')
            ->from(TABLE_REVIEW)
            ->where('deleted')->eq(0)
            ->andWhere('id')->in($reviewIds)
            ->orderBy('id_desc')
            ->fetchPairs();
    }


    /**
     * Desc: 选中评审标题导出模板时的方法处理
     * Date: 2022/8/9
     * Time: 15:47
     *
     * @param $reviewID
     * @return mixed
     *
     */
    public function getExportTempate($reviewID)
    {
        return $this->dao->select('id,title')
            ->from(TABLE_REVIEW)
            ->where('deleted')->eq(0)
            ->andWhere('id')->eq($reviewID)
            ->orderBy('id_desc')
            ->fetchPairs();
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
//        $reviewList = $this->loadModel('review')->getReviewByProjectId($projectID);
        $reviewList = $this->getReviewInfo();
        $allLink = helper::createLink('reviewproblem', 'issue', "project=$projectID&reviewID=0&status=$browseType");

        $listLink   = '';
        foreach($reviewList as $key => $review)
        {
            $reviewLink = helper::createLink('reviewproblem', 'issue', "project=$projectID&reviewID=$key&status=$browseType");
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
    public static function isClickable($issue, $action)
    {
        global $app;

        $action = strtolower($action);

        $dealUsers  = [];
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
                    echo html::a(helper::createLink('reviewmanage', 'view', "reviewID=$issue->review"),'<div class="reviewTitle" title="' . $issue->reviewtitle . '">' . $issue->reviewtitle .'</div>');
                    break;

                case 'title':
                    echo html::a(helper::createLink('reviewproblem', 'view', $params),'<div class="problemTitle" title="' . $issue->title . '">' . $issue->title .'</div>');
                    break;
                case 'desc':
                    echo '<div class="change" title="' . strip_tags($issue->desc) . '">' . $issue->desc .'</div>';
                    break;
                case 'type':
                    echo zget($this->lang->reviewproblem->typeList, $issue->type);
                    break;
                case 'raiseBy':
                    echo zget($users, $issue->raiseBy);
                    break;
                case 'raiseDate':
                    echo $issue->raiseDate;
                    break;
                case 'status':
                    echo zget($this->lang->reviewproblem->statusList, $issue->status);
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

                    $ids = $this->getAllReviewId();
                    //评审已关闭，评审问题按钮置灰
                    if(in_array($issue->review,$ids)) {
                        common::hasPriv('reviewproblem','edit') ? common::printIcon('reviewproblem', 'edit', $param, $issue, 'list','','','disabled','','') : '';
                        common::hasPriv('reviewproblem','resolved') ? common::printIcon('reviewproblem', 'resolved', $param, $issue, 'list','checked','', 'disabled','', "data-width=50%") :'';
                        common::hasPriv('reviewproblem','delete') ? common::printIcon('reviewproblem', 'delete',$param , $issue, 'list', 'trash', '', 'disabled', true,'', '') : '';

                    }else{
                        common::hasPriv('reviewproblem','edit') ? common::printIcon('reviewproblem', 'edit', $param, $issue, 'list') : '';
                        common::hasPriv('reviewproblem','resolved') ? common::printIcon('reviewproblem', 'resolved', $param, $issue, 'list','checked','', '','', "data-width=50%") :'';
                        common::hasPriv('reviewproblem','delete') ? common::printIcon('reviewproblem', 'delete',$param , $issue, 'list', 'trash', '', 'iframe', true,'', '') : '';
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
    public function setListValue($reviewID)
    {
        $this->app->loadLang('opinion');
        $statusList        = $this->lang->reviewproblem->statusList;//状态
        $typeList          = $this->lang->reviewproblem->typeList;//提出阶段
        $reviewList        = $this->getReviewBatchCreate();//评审标题
        if(!empty($reviewID)){
            $reviewList   = $this->getExportTempate($reviewID);//评审标题
        }
        $reviewArr = [];
        foreach ($reviewList as $value){
            $reviewArr[$value] = $value;
        }
        foreach (array_unique($reviewArr) as $id=>$value){
            $reviewArr[$id] .= "(#$id)";
        }
        $typeArray = [];
        foreach ($typeList as $id=>$value){
            $typeArray[$id] .= "$value(#$id)";
        }
        $this->post->set('reviewList',      array_values($reviewArr));
        $this->post->set('statusList',       join(',', $statusList));
        $this->post->set('typeList',      array_values($typeArray));
        $this->post->set('width', 60);

        $this->post->set('listStyle',      $this->config->reviewproblem->export->listFields);
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
    public function getMeetingCodeList(){
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

                if (isset($this->config->reviewproblem->import->requiredFields)) {
                    $requiredFields = explode(',', $this->config->reviewproblem->import->requiredFields);
                    foreach ($requiredFields as $requiredField) {
                        $requiredField = trim($requiredField);
                        if (empty($reviewData->$requiredField))
                            dao::$errors[] = sprintf($this->lang->reviewproblem->noRequire, $line, $this->lang->reviewproblem->$requiredField);
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
        if(empty($reviewList))  die(js::alert($this->lang->reviewproblem->emptyReviewMsg,true));

        if(dao::isError()) die(js::error(dao::getError()));
        foreach ($reviewList as $insertData){
            $this->dao->insert(TABLE_REVIEWISSUE)->data($insertData)->autoCheck()->exec();
            if(!dao::isError())
            {
                $reviewIssueId = $this->dao->lastInsertID();
                $this->action->create('reviewproblem', $reviewIssueId, 'import', '');
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
            die(js::alert($this->lang->reviewproblem->emptyData,true));
        }else{
            //只填写文件名/位置，不填写判断
            foreach($data->title as $key => $value)
            {
                if(!empty($value)){
                    $titleData = $this->reviewData($data,$key,$projectID);
                    if(!empty($titleData['line']))
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
                    if(!empty($descData['line']))
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
                $requiredFields = explode(',', $this->config->reviewproblem->beatchCreate->requiredFields);

                foreach ($requiredFields as $requiredField) {
                    $requiredField = trim($requiredField);
                    if (empty($dataValue->$requiredField)) {
                            dao::$errors[] = sprintf($this->lang->reviewproblem->noRequire, $item, $this->lang->reviewproblem->$requiredField);
                    }
                }

            }
        }
        if(dao::isError()) die(js::error(dao::getError()));
        foreach ($addData as $insertData){
            if(empty($insertData->meetingCode)) $insertData->meetingCode = '';
            $this->dao->insert(TABLE_REVIEWISSUE)->data($insertData)->exec();
            if(!dao::isError())
            {
                $reviewIssueId = $this->dao->lastInsertID();
                $this->loadModel('action')->create('reviewproblem', $reviewIssueId, 'Created');
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
        $reviewData->project       = $reviewInfo->project;
        $reviewData->dealUser      = $reviewInfo->createdBy ?? '';

        if(isset($this->config->reviewproblem->beatchCreate->requiredFields))
        {
            $requiredFields = explode(',', $this->config->reviewproblem->beatchCreate->requiredFields);
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
     * @return mixed
     */
    public function getAllReviewId(){
        return $this->dao->select('id')->from(TABLE_REVIEW)
            ->Where('deleted')->eq('0')
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
        $this->config->reviewproblem->search['actionURL'] = $actionURL;
        $this->config->review->search['queryID']   = $queryID;

        $this->loadModel('search')->setSearchParams($this->config->reviewproblem->search);
    }

    /*
     *获得评审会议数量
     *
     *
     * @param $reviewID
     * @return int
     */
    public function getReviewIssueCount($reviewID){
        $count = 0;
        $ret = $this->dao->select('count(id) as total')->from(TABLE_REVIEWISSUE)
            ->where('review')->eq($reviewID)
            ->andWhere('deleted')->eq('0')
            ->fetch();
        if($ret){
            $count = $ret->total;
        }
        return $count;
    }
    /*
    *获得评审问题已新建和已采纳的个数
    *
    *
    * @param $reviewID
    * @return int
    */
    public function getReviewIssueCount2($reviewID,$type){
        $count = 0;
        $ret = $this->dao->select('count(id) as total')->from(TABLE_REVIEWISSUE)
            ->where('review')->eq($reviewID)
            ->andWhere('deleted')->eq('0')
            ->beginIF($type == 'failed')->andWhere('status')->eq('failed')->fi()
            ->beginIF($type == 'createAndAccept')->andWhere('status')->in('create,active')->fi()
            ->fetch();
        if($ret){
            $count = $ret->total;
        }
        return $count;
    }

    /*
    *获得该用户所提出的评审问题非已验证的个数
    * @param $reviewID
    * @param $user
    * @param $last
    * @return int
    */
    public function getReviewIssueCountByUser($reviewID, $user, $last, $type = ''){
        $count = 0;
        $ret = $this->dao->select('count(id) as total')->from(TABLE_REVIEWISSUE)
            ->where('review')->eq($reviewID)
            ->beginIF($type == 'failed')->andWhere('status')->eq('failed')->fi()
            ->beginIF(empty($type))->andWhere('status')->in($this->lang->reviewproblem->checkPassArr)->fi()
            ->beginIF($last != '1')->andWhere('raiseBy')->eq($user)->fi()
            ->andWhere('deleted')->eq('0')
            ->fetch();
        if($ret){
            $count = $ret->total;
        }
        return $count;
    }

    /**
     * Desc:获取我能看到的评审标题
     * Date: 2022/7/29
     * Time: 9:31
     *
     * @return mixed
     *
     */
    public function getReviewInfo($reviewID = 0)
    {
        $reviewIds = $this->getReviewIdsByReviewManage('all',0,'id_desc',0);
        return $this->dao->select('id,title')
            ->from(TABLE_REVIEW)
            ->where('deleted')->eq(0)
            ->andWhere('id')->in($reviewIds)
            ->beginIF($reviewID)->andWhere('id')->eq($reviewID)->fi()
            ->orderBy('id_desc')
            ->fetchAll('id');
    }
}
