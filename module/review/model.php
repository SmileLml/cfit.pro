<?php
/**
 * The model file of review module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2020 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Qiyu Xie <xieqiyu@easycorp.ltd>
 * @package     model
 * @version     $Id: control.php 5107 2020-09-09 09:46:12Z xieqiyu@easycorp.ltd $
 * @link        http://www.zentao.net
 */
class reviewModel extends model
{

    /**
     * Get review list.
     *
     * @param  int    $projectID
     * @param  string $browseType
     * @param  string $orderBy
     * @param  object $pager
     * @access public
     * @return void
     */
    public function getList($projectID = 0, $browseType, $queryID = 0, $orderBy, $pager = null)
    {
        $reviewQuery = '';
        if($browseType == 'bysearch')
        {
            $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : ''; 
            if($query)
            {
                $this->session->set('reviewQuery', $query->sql);
                $this->session->set('reviewForm', $query->form);
            }

            if($this->session->reviewQuery == false) $this->session->set('reviewQuery', ' 1 = 1');

            $reviewQuery = $this->session->reviewQuery;
        }
        $reviewQuery = str_replace('AND `', ' AND `t1.', $reviewQuery);
        $reviewQuery = str_replace('`', '', $reviewQuery);

        return $this->dao->select('t1.*, t2.version, t2.category, t2.product')->from(TABLE_REVIEW)->alias('t1')
            ->leftJoin(TABLE_OBJECT)->alias('t2')
            ->on('t1.object=t2.id')
            ->where('t1.deleted')->eq(0)
            ->beginIF($browseType == 'bysearch')->andWhere($reviewQuery)->fi()
            ->beginIF($projectID)->andWhere('t1.project')->eq($projectID)->fi()
            ->beginIF($browseType == 'reviewing')->andWhere('t1.status')->eq('reviewing')->fi()
            ->beginIF($browseType == 'done')->andWhere('t1.status')->eq('done')->fi()
            ->beginIF($browseType == 'wait')
            ->andWhere('t1.status')->eq('wait')
            ->andWhere("CONCAT(',', t1.reviewedBy, ',')")->like("%,{$this->app->user->account},%")
            ->fi()
            ->beginIF($browseType == 'reviewedbyme')
            ->andWhere("CONCAT(',', t1.reviewedBy, ',')")->like("%,{$this->app->user->account},%")
            ->fi()
            ->beginIF($browseType == 'createdbyme')
            ->andWhere('t1.createdBy')->eq($this->app->user->account)
            ->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll();
    }

    /**
     * Get review pairs.
     *
     * @param  int    $projectID
     * @param  string $browseType
     * @param  bool   $withVersion true|false
     * @access public
     * @return void
     */
    public function getPairs($projectID, $productID, $withVersion = false)
    {
        $reviews = $this->dao->select('t1.id, t1.title, t2.version')->from(TABLE_REVIEW)->alias('t1')
            ->leftJoin(TABLE_OBJECT)->alias('t2')
            ->on('t1.object=t2.id')
            ->where('t1.deleted')->eq(0)
            ->beginIF($projectID)->andWhere('t1.project')->eq($projectID)->fi()
            ->beginIF($productID)->andWhere('t2.product')->eq($productID)->fi()
            ->orderBy('t1.id asc')
            ->fetchAll();

        $pairs = array();
        foreach($reviews as $id => $review) $pairs[$review->id] = $withVersion ? $review->title . '-' . $review->version : $review->title;

        return $pairs;
    }

    /**
     * Desc:根据projectId获取评审标题数据
     * Date: 2022/4/19
     * Time: 16:40
     *
     * @param $projectID
     * @return mixed
     *
     */
    public function getReviewListByProjectId($projectID)
    {
        return $this->dao->select('id,title')->from(TABLE_REVIEW)
            ->where('project')->eq($projectID)
            ->andWhere('status')->notin('pass,fail,drop,reviewpass')
            ->andWhere('deleted')->eq(0)
            ->orderBy('id_desc')
            ->fetchAll('id');
    }

    /**
     * Desc:根据projectId获取评审标题数据
     * Date: 2022/5/6
     * Time: 16:40
     *
     * @param $projectID
     * @return mixed
     *
     */
    public function getReviewByProjectId($projectID)
    {
        return $this->dao->select('id,title')->from(TABLE_REVIEW)
            ->where('project')->eq($projectID)
            ->andWhere('deleted')->eq(0)
            ->orderBy('id_desc')
            ->fetchAll('id');
    }

    /**
     * Desc:根据评审标题获取评审标题id
     * Date: 2022/4/26
     * Time: 16:31
     *
     * @param $review
     *
     */
    public function  getReviewIdByReview($review)
    {
        if(!empty($review)){
            return $this->dao->select('id,title')->from(TABLE_REVIEW)
                ->where('title')->eq($review)
                ->andWhere('deleted')->eq(0)
                ->fetch('id');
        }
    }

    /**
     * Get review by id.
     *
     * @param  int    $reviewID
     * @access public
     * @return void
     */
    public function getByID($reviewID)
    {
        if(!$reviewID) return new stdclass();
        $review = $this->dao->select('*')->from(TABLE_REVIEW)->where('id')->eq($reviewID)->fetch();
        $review->objects = $this->dao->select('*')->from(TABLE_REVIEWOBJECT)->where('review')->eq($reviewID)->fetchAll();
        return $review;
    }

    /**
     * Get user review.
     *
     * @param  string $browseType
     * @param  string $orderBy
     * @param  object $pager
     * @access public
     * @return void
     */
    public function getUserReviews($browseType, $orderBy, $pager = null)
    {
        $reviews = $this->dao->select('t1.*, t2.version, t2.category, t2.product')->from(TABLE_REVIEW)->alias('t1')
            ->leftJoin(TABLE_OBJECT)->alias('t2')
            ->on('t1.object=t2.id')
            ->where('t1.deleted')->eq(0)
            ->beginIF($browseType == 'reviewing')->andWhere('t1.status')->eq('reviewing')->fi()
            ->beginIF($browseType == 'done')->andWhere('t1.status')->eq('done')->fi()
            ->beginIF($browseType == 'wait')
            ->andWhere('t1.status')->eq('wait')
            ->andWhere("CONCAT(',', t1.reviewedBy, ',')")->like("%,{$this->app->user->account},%")
            ->fi()
            ->beginIF($browseType == 'reviewedbyme')
            ->andWhere("CONCAT(',', t1.reviewedBy, ',')")->like("%,{$this->app->user->account},%")
            ->fi()
            ->beginIF($browseType == 'createdbyme')
            ->andWhere('t1.createdBy')->eq($this->app->user->account)
            ->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll();

        // 其他评审，成方金科定制内容
        $rs = $this->dao->select('t1.objectType,t1.objectID,t1.createdBy,t1.createdDate')->from(TABLE_REVIEWNODE)->alias('t1')
            ->leftJoin(TABLE_REVIEWER)->alias('t2')
            ->on('t1.id = t2.node')
            ->where('t2.reviewer')->eq($this->app->user->account)
            ->andWhere('t2.status')->eq('pending')
            ->fetchAll();
        $map = [];
        foreach($rs as $r)
        {
            if(!isset($map[$r->objectType])) $map[$r->objectType] = array();
            $map[$r->objectType][$r->objectID] = $r;
        }

        $projectplans = array();
        if(isset($map['projectplan']))
        {
            $projectplans = $this->dao->select('*')->from(TABLE_PROJECTPLAN)
                ->where('id')->in(array_keys($map['projectplan']))
                ->fetchAll();
            foreach($projectplans as $p)
            {
                $review = $map['projectplan'][$p->id];
                $review->id         = $review->objectID;
                $review->project    = 0;
                $review->title      = $p->name;
                $review->status     = 'wait';
                $review->reviewedBy = $this->app->user->account;
                $reviews[] = $review;
            }
        }

        return $reviews;
    }

    public function getResultByReview($reviewID)
    {
        return $this->dao->select('*')->from(TABLE_REVIEWRESULT)
           ->where('review')->eq($reviewID)
           ->fetchAl('id');
    }

    /**
     * Get review result by user.
     *
     * @param  int    $reviewID
     * @param  string $type
     * @access public
     * @return void
     */
    public function getResultByUser($reviewID, $type = 'review')
    {
        return $this->dao->select('*')->from(TABLE_REVIEWRESULT)
           ->where('review')->eq($reviewID)
           ->andWhere('reviewer')->eq($this->app->user->account)
           ->andWhere('type')->eq($type)
           ->fetch();
    }

    /**
     * Get review result by user list.
     *
     * @param  int    $reviewID
     * @access public
     * @return void
     */
    public function getResultByUserList($reviewID)
    {
        return $this->dao->select('*')->from(TABLE_REVIEWRESULT)
            ->where('review')->eq($reviewID)
            ->andWhere('type')->eq('review')
            ->fetchAll('reviewer');
    }

    /**
     * Get review pairs of a user.
     *
     * @param  string $account
     * @param  int    $limit
     * @param  string $status all|draft|wait|reviewing|pass|fail|auditing|done
     * @param  array  $skipProjectIDList
     * @access public
     * @return array
     */
    public function getUserReviewPairs($account, $limit = 0, $status = 'all', $skipProjectIDList = array())
    {
        $stmt = $this->dao->select('t1.id, t1.title, t2.name as project')
            ->from(TABLE_REVIEW)->alias('t1')
            ->leftjoin(TABLE_PROJECT)->alias('t2')->on('t1.project = t2.id')
            ->where("CONCAT(',', t1.reviewedBy, ',')")->like("%,{$account},%")
            ->andWhere('t1.deleted')->eq(0)
            ->beginIF($status != 'all')->andWhere('t1.status')->in($status)->fi()
            ->beginIF(!empty($skipProjectIDList))->andWhere('t1.project')->notin($skipProjectIDList)->fi()
            ->beginIF($limit)->limit($limit)->fi()
            ->query();

        $reviews = array();
        while($review = $stmt->fetch())
        {
            $reviews[$review->id] = $review->project . ' / ' . $review->title;
        }
        return $reviews;
    }

    /**
     * Get book id.
     *
     * @param  object $review
     * @access public
     * @return void
     */
    public function getBookID($review)
    {
        return $this->dao->select('id')->from(TABLE_DOC)
            ->where('product')->eq($review->product)
            ->andWhere('templateType')->eq($review->category)
            ->andWhere('lib')->ne('')->fetch('id');
    }

    /**
     * Get object scale.
     *
     * @param  object $review
     * @access public
     * @return void
     */
    public function getObjectScale($review)
    {
        $productID   = $review->product;
        $objectScale = $this->dao->select('sum(estimate) as objectScale')->from(TABLE_STORY)
            ->where('product')->eq($productID)
            ->andWhere('type')->eq('requirement')
            ->andWhere('deleted')->eq(0)
            ->fetch('objectScale');

        return $objectScale;
    }

    /**
     * Judge review if can assess.
     *
     * @param  object $review
     * @param  string $account
     * @access public
     * @return void
     */
    public function judgeIfCanAssess($review, $account)
    {
        $result = $this->dao->select('result')->from(TABLE_REVIEWRESULT)
            ->where('review')->eq($review->id)
            ->andWhere('reviewer')->eq($account)
            ->andWhere('type')->eq('review')
            ->fetch('result');

        if($result == 'pass' || $result == 'needfix') return false;
        if($result == 'fail')
        {
            $activeIssue = $this->dao->select('count(*) as count')->from(TABLE_REVIEWISSUE)
                ->where('review')->eq($review->id)
                ->andWhere('createdBy')->eq($account)
                ->andWhere('status')->eq('active')
                ->andWhere('deleted')->eq(0)
                ->fetch('count');

            if($activeIssue) return false;
        }

        return true;
    }

    /**
     * Create a review.
     *
     * @param  int    $projectID
     * @param  string $reviewRange
     * @param  string $checkedItem
     * @access public
     * @return void
     */
    public function create($projectID = 0, $reviewRange = 'all', $checkedItem = '')
    {
        $today = helper::today();
        $data  = fixer::input('post')
            ->add('status', 'wait')
            ->add('createdBy', $this->app->user->account)
            ->add('createdDate', $today)
            ->add('project', $projectID)
            ->join('owner', ',')
            ->join('expert', ',')
            ->join('outside', ',')
            ->join('reviewedBy', ',')
            ->join('relatedUsers', ',')
            ->remove('comment,uid,object,url')
            ->get();

        $this->dao->insert(TABLE_REVIEW)->data($data)
            ->autoCheck()
            ->batchCheck($this->config->review->create->requiredFields, 'notempty')
            ->exec();

        $reviewID = $this->dao->lastInsertID();
        foreach($this->post->object as $key => $obj)
        {
            if(!$obj) continue;

            $url = $this->post->url[$key];

            $data = new stdClass();
            $data->object      = $obj;
            $data->url         = $url;
            $data->review      = $reviewID;
            $data->createdBy   = $this->app->user->account;
            $data->createdDate = helper::today();
            $this->dao->insert(TABLE_REVIEWOBJECT)->data($data)->exec();
        }

        if(!dao::isError()) return $reviewID;

        return false;
    }

    /**
     * Edit a review.
     *
     * @param  int    $projectID
     * @access public
     * @return void
     */
    public function update($reviewID)
    {
        $oldReview = $this->getByID($reviewID);
        $today  = helper::today();
        $review = fixer::input('post')
            ->join('owner', ',')
            ->join('expert', ',')
            ->join('reviewedBy', ',')
            ->join('outside', ',')
            ->join('relatedUsers', ',')
            ->add('lastEditedBy', $this->app->user->account)
            ->add('lastEditedDate', $today)
            ->remove('comment,uid,object,url')
            ->get();

        if($oldReview->status == 'reject' || $oldReview->status == 'recall'){ //驳回或者撤回编辑
            $review->status = $this->lang->review->statusList['waitApply']; //待审核
            $review->version = $oldReview->version + 1;
        }


        $this->dao->update(TABLE_REVIEW)->data($review)
            ->autoCheck()
            ->batchCheck($this->config->review->create->requiredFields, 'notempty')
            ->where('id')->eq($reviewID)
            ->exec();

        $this->dao->delete()->from(TABLE_REVIEWOBJECT)->where('review')->eq($reviewID)->exec();
        foreach($this->post->object as $key => $obj)
        {
            if(!$obj) continue;

            $url = $this->post->url[$key];

            $data = new stdClass();
            $data->object      = $obj;
            $data->url         = $url;
            $data->review      = $reviewID;
            $data->createdBy   = $this->app->user->account;
            $data->createdDate = helper::today();
            $this->dao->insert(TABLE_REVIEWOBJECT)->data($data)->exec();
        }


        if(!dao::isError()) return common::createChanges($oldReview, $review);

        return false;
    }

    /**
     * Submit a review.
     *
     * @param  int    $reviewID
     * @access public
     * @return void
     */
    public function submit($reviewID)
    {
        $oldReview = $this->getByID($reviewID);
        $today     = helper::today();
        $review    = fixer::input('post')
            ->add('status', 'wait')
            ->setIF($oldReview->status == 'fail', 'status', 'reviewing')
            ->join('owner', ',')
            ->join('expert', ',')
            ->join('reviewedBy', ',')
            ->remove('comment,uid')
            ->get();

        $this->dao->update(TABLE_REVIEW)->data($review)->where('id')->eq($reviewID)->exec();

        if(!dao::isError()) return common::createChanges($oldReview, $review);
        return false;
    }

    /**
     * Set review to audit.
     *
     * @param  int    $reviewID
     * @access public
     * @return void
     */
    public function toAudit($reviewID)
    {
        $auditedBy = $this->post->auditedBy;
        if(!$auditedBy) die(js::alert($this->lang->review->auditedByEmpty));

        $this->dao->update(TABLE_REVIEW)
            ->set('auditedBy')->eq($auditedBy)
            ->set('status')->eq('auditing')
            ->where('id')->eq($reviewID)->exec();

        return !dao::isError();
    }

    /**
     * Project: chengfangjinke
     * Method: assess
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/9
     * Time: 7:48
     * Desc: This is the code comment. This method is called assess.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $reviewID
     * @return array|void
     */
    public function assess($reviewID)
    {
        $data = fixer::input('post')
            ->add('review', $reviewID)
            ->setDefault('reviewer', $this->app->user->account)
            ->setIF(is_numeric($this->post->consumed), 'consumed', (float)$this->post->consumed)
            ->stripTags($this->config->review->editor->assess['id'], $this->config->allowedTags)
            ->get();

        // $this->dao->replace(TABLE_REVIEWRESULT)->data($data)->autoCheck()->exec();
        $res = $this->dao->select('id')->from(TABLE_REVIEWRESULT)->where('review')->eq($reviewID)->andWhere('reviewer')->eq($this->app->user->account)->fetch('id');
        if($res)
        {
            $this->dao->update(TABLE_REVIEWRESULT)->data($data)->autoCheck()->where('id')->eq($res)->exec();
        }
        else
        {
            $this->dao->insert(TABLE_REVIEWRESULT)->data($data)->autoCheck()->exec();
        }

        $this->dao->update(TABLE_REVIEW)->set('lastReviewedBy')->eq($this->app->user->account)->set('lastReviewedDate')->eq($this->post->createdDate)->where('id')->eq($reviewID)->exec();
        $results = $this->dao->select('*')->from(TABLE_REVIEWRESULT)->where('review')->eq($reviewID)->fetchAll('id');

        $finalResult = 'pass';
        foreach($results as $result)
        {
            if($result->result != 'pass')
            {
                $finalResult = 'fail'; 
                continue;
            }
        }

        if($finalResult == 'fail') $this->dao->update(TABLE_REVIEW)->set('status')->eq('fail')->set('result')->eq('fail')->where('id')->eq($reviewID)->exec();
        if($finalResult == 'pass') $this->dao->update(TABLE_REVIEW)->set('status')->eq('pass')->set('result')->eq('pass')->where('id')->eq($reviewID)->exec();

        if(!dao::isError()) return array('result' => $data->result, 'method' => $data->method);
    }

    /**
     * Save review result.
     *
     * @param  int    $reviewID
     * @param  string $type
     * @access public
     * @return void
     */
    public function saveResult($reviewID, $type = 'review')
    {
        $data = fixer::input('post')
            ->setDefault('reviewer', $this->app->user->account)
            ->setIF(is_numeric($this->post->consumed), 'consumed', (float)$this->post->consumed)
            ->get();

        $result = new stdclass();
        $result->review      = $reviewID;
        $result->type        = $type;
        $result->result      = $data->result;
        $result->opinion     = $data->opinion;
        $result->reviewer    = $data->reviewer;
        $result->createdDate = $data->createdDate ? $data->createdDate : helper::today();
        $result->consumed    = $data->consumed;

        $this->dao->replace(TABLE_REVIEWRESULT)->data($result)->autoCheck()->exec();

        $action = $type == 'review' ? 'Reviewed' : 'Audited';
        $this->loadModel('action')->create('review', $reviewID, $action, '', ucfirst($result->result));
        $this->computeResult($reviewID, $result->result, $type);

        /* If exist resolved issue.*/
        if(!isset($data->resolved))
        {
            foreach($data->resolved as $issueID => $resolved)
            {
                $status  = $resolved ? 'closed' : 'active';
                $opinion = $status == 'active' ? $data->opinion[$issueID] : '';
                $this->dao->update(TABLE_REVIEWISSUE)
                    ->set('status')->eq($status)
                    ->beginIF($opinion)->set('opinion')->eq($opinion)->fi()
                    ->where('id')->eq($issueID)
                    ->exec();
            }
        }
        else
        {
            $issueResult = isset($data->issueResult) ? $data->issueResult : array();
            $listPairs   = $type == 'review' ? $this->loadModel('reviewcl')->getByList($issueResult) : $this->loadModel('cmcl')->getByList($issueResult);
            $remainIssue = 0;
            foreach($issueResult as $id => $result)
            {
                if($result != 0) continue;
                $issue = new stdclass();
                $issue->title       = zget($listPairs, $id, $data->issueOpinion[$id]);
                $issue->type        = $type;
                $issue->review      = $reviewID;
                $issue->listID      = $id;
                $issue->status      = 'active';
                $issue->opinion     = $data->issueOpinion[$id];
                $issue->createdBy   = $this->app->user->account;
                $issue->createdDate = helper::today();
                $issue->opinionDate = isset($data->opinionDate[$id]) ? $data->opinionDate[$id] : '';

                $this->dao->insert(TABLE_REVIEWISSUE)->data($issue)->autoCheck()->exec();
                $issueID = $this->dao->lastInsertID();
                $this->loadModel('action')->create('reviewissue', $issueID, 'opened', $issue->opinion);
                $remainIssue = 1;
            }

            /* Record review remained issues, for judge action clickable.*/
            if($remainIssue) $this->dao->update(TABLE_REVIEWRESULT)->set('remainIssue')->eq($remainIssue)->where('review')->eq($reviewID)->exec();
        }
    }

    /**
     * Compute review result.
     *
     * @param  int    $reviewID
     * @param  string $result
     * @param  string $type
     * @access public
     * @return void
     */
    public function computeResult($reviewID, $result, $type = 'review')
    {
        if($type == 'review')
        {
            $review    = $this->getByID($reviewID);
            $results   = $this->dao->select('id, result')->from(TABLE_REVIEWRESULT)->where('review')->eq($reviewID)->andWhere('type')->eq('review')->fetchPairs();
            $reviewers = explode(',', trim($review->reviewedBy, ','));

            /* Is final reviewer. */
            $isFinalReviewer = (count($results) == count($reviewers));

            $finalResult = '';
            if($isFinalReviewer)
            {
                $finalResult = 'pass';
                foreach($results as $id => $result)
                {
                    if($result == 'fail')
                    {
                        $finalResult = 'fail';
                        break;
                    }

                    if($result == 'needfix') $finalResult = 'needfix';
                }
            }

            $finalStatus = 'reviewing';
            if($isFinalReviewer) $finalStatus = ($finalResult == 'pass') ? 'pass' : 'fail';

            $this->dao->update(TABLE_REVIEW)
                ->beginIF($finalResult)->set('result')->eq($finalResult)->fi()
                ->set('status')->eq($finalStatus)
                ->set('lastReviewedBy')->eq($this->app->user->account)
                ->set('lastReviewedDate')->eq(date('Y-m-d'))
                ->where('id')->eq($reviewID)->exec();
        }
        else
        {
            $audit = new stdclass();
            $audit->auditResult     = $result;
            $audit->lastAuditedBy   = $this->app->user->account;
            $audit->lastAuditedDate = helper::today();
            if($result == 'pass')
            {
                $audit->status = 'done';
                $this->dao->update(TABLE_REVIEW)->data($audit)->where('id')->eq($reviewID)->exec();
            }

            if($result == 'fail')
            {
                $audit->status    = 'wait';
                $audit->result    = '';
                $audit->auditedBy = '';
                $this->dao->update(TABLE_REVIEW)->data($audit)->where('id')->eq($reviewID)->exec();
                $this->dao->delete()->from(TABLE_REVIEWRESULT)->where('review')->eq($reviewID)->andWhere('type')->eq('review')->exec();
            }

            if($result == 'needfix') $this->dao->update(TABLE_REVIEW)->data($audit)->where('id')->eq($reviewID)->exec();
        }
    }

    /**
     * Get object data.
     *
     * @param  int     $projectID
     * @param  string  $objectType
     * @param  int     $productID
     * @param  string  $reviewRange
     * @param  string  $checkedItem
     * @access public
     * @return void
     */
    public function getDataByObject($projectID, $objectType, $productID, $reviewRange, $checkedItem)
    {
        $data = array();
        if($objectType == 'PP') $data = $this->getDataFromPP($projectID, $objectType, $productID);
        if($objectType == 'SRS'  || $objectType == 'URS')  $data = $this->getDataFromStory($projectID, $objectType, $productID, $reviewRange, $checkedItem);
        if($objectType == 'HLDS' || $objectType == 'DDS' || $objectType == 'DBDS' || $objectType == 'ADS') $data = $this->getDataFromDesign($projectID, $objectType, $productID, $reviewRange, $checkedItem);
        if($objectType == 'ITTC' || $objectType == 'STTC') $data = $this->getDataFromCase($projectID, $objectType, $productID, $reviewRange, $checkedItem);

        return $data;
    }

    /**
     * Get data from story.
     *
     * @param  int     $projectID
     * @param  string  $objectType
     * @param  int     $productID
     * @param  string  $reviewRange
     * @param  string  $checkedItem
     * @access public
     * @return void
     */
    public function getDataFromStory($projectID, $objectType, $productID, $reviewRange, $checkedItem)
    {
        $data = array();
        $type = $objectType == 'SRS' ? 'story' : 'requirement';
        $stories = $this->dao->select('t1.module, t1.estimate, t2.*')->from(TABLE_STORY)->alias('t1')
            ->leftJoin(TABLE_STORYSPEC)->alias('t2')->on('t1.id=t2.story and t1.version=t2.version')
            ->where('t1.product')->eq($productID)
            ->andWhere('t1.type')->eq($type)
            ->andWhere('t1.deleted')->eq(0)
            ->beginIF($reviewRange != 'all')->andWhere('t1.id')->in($checkedItem)->fi()
            ->fetchAll('story');
        $storyEst= $this->dao->select('sum(estimate) as storyEst')->from(TABLE_STORY)
            ->where('product')->eq($productID)
            ->andWhere('type')->eq($type)
            ->andWhere('deleted')->eq(0)
            ->beginIF($reviewRange != 'all')->andWhere('id')->in($checkedItem)->fi()
            ->fetch('storyEst');

        $data['story']    = $stories;
        $data['storyEst'] = $storyEst;
        return $data;
    }

    /**
     * Get data from design.
     *
     * @param  int     $projectID
     * @param  string  $objectType
     * @param  int     $productID
     * @param  string  $reviewRange
     * @param  string  $checkedItem
     * @access public
     * @return void
     */
    public function getDataFromDesign($projectID, $objectType, $productID, $reviewRange, $checkedItem)
    {
        $data = array();
        $designs = $this->dao->select('t2.*')->from(TABLE_DESIGN)->alias('t1')
            ->leftJoin(TABLE_DESIGNSPEC)->alias('t2')
            ->on('t1.id=t2.design and t1.version=t2.version')
            ->where('t1.product')->eq($productID)
            ->andWhere('t1.type')->eq($objectType)
            ->andWhere('t1.deleted')->eq(0)
            ->beginIF($reviewRange != 'all')->andWhere('t1.id')->in($checkedItem)->fi()
            ->orderBy('version_desc')
            ->fetchAll('design');

        $data['design'] = $designs;
        return $data;
    }

    /**
     * Get data from case.
     *
     * @param  int     $projectID
     * @param  string  $objectType
     * @param  int     $productID
     * @param  string  $reviewRange
     * @param  string  $checkedItem
     * @access public
     * @return void
     */
    public function getDataFromCase($projectID, $objectType, $productID, $reviewRange, $checkedItem)
    {
        $data  = array();
        $stage = $objectType == 'ITTC' ? 'intergrate' : 'system';
        $cases = $this->dao->select('t1.id as caseID, t1.module, t1.title, t2.*')->from(TABLE_CASE)->alias('t1')
            ->leftJoin(TABLE_CASESTEP)->alias('t2')
            ->on('t1.id=t2.case')
            ->where('t1.product')->eq($productID)
            ->andWhere('t1.stage')->like("%$stage%")
            ->andWhere('t1.deleted')->eq(0)
            ->beginIF($reviewRange != 'all')->andWhere('t1.id')->in($checkedItem)->fi()
            ->fetchAll('case');

        $data['case'] = $cases;
        return $data;
    }

    /**
     * Get data from project plan.
     *
     * @param  int    $projectID
     * @param  string $objectType
     * @param  int    $productID
     * @access public
     * @return void
     */
    public function getDataFromPP($projectID, $objectType, $productID)
    {
        $data   = array();
        $stages = $this->dao->select('t1.*')->from(TABLE_PROJECTSPEC)->alias('t1')
            ->leftJoin(TABLE_PROJECT)->alias('t2')
            ->on('t1.project=t2.id and t1.version=t2.version')
            ->leftJoin(TABLE_PROJECTPRODUCT)->alias('t3')
            ->on('t2.id=t3.project')
            ->where('t2.deleted')->eq(0)
            ->andWhere('t2.project')->eq($projectID)
            ->andWhere('t3.product')->eq($productID)
            ->fetchAll('project');

        $data['stage'] = $stages;

        $projects = $this->dao->select('t1.id')->from(TABLE_PROJECT)->alias('t1')
            ->leftJoin(TABLE_PROJECTPRODUCT)->alias('t2')
            ->on('t1.id=t2.project')
            ->where('t1.project')->eq($projectID)
            ->andWhere('t1.type')->eq('stage')
            ->andWhere('t2.product')->eq($productID)
            ->fetchPairs();

        $tasks = $this->dao->select('t1.*, t2.estimate, t2.type')->from(TABLE_TASKSPEC)->alias('t1')
            ->leftJoin(TABLE_TASK)->alias('t2')
            ->on('t1.task=t2.id and t1.version=t2.version')
            ->where('t2.deleted')->eq(0)
            ->andWhere('t2.status')->ne('cancel')
            ->andWhere('t2.parent')->le(0)
            ->andWhere('t2.project')->in($projects)
            ->fetchAll('task');

        /* Sum estimate by type.*/
        $taskEst = $requestEst = $testEst = $devEst = $designEst = 0;
        foreach($tasks as $task)
        {
            $taskEst += $task->estimate;
            if($task->type == 'request') $requestEst += $task->estimate;
            if($task->type == 'devel')   $devEst     += $task->estimate;
            if($task->type == 'test')    $testEst    += $task->estimate;
            if($task->type == 'design')  $designEst  += $task->estimate;
        }

        $data['task']        = $tasks;
        $data['taskEst']     = $taskEst;
        $data['requestEst']  = $requestEst;
        $data['devEst']      = $devEst;
        $data['testEst']     = $testEst;
        $data['designEst']   = $designEst;
        return $data;
    }

    /**
     * Get reviewer by object.
     *
     * @param  int    $projectID
     * @param  string $object
     * @access public
     * @return void
     */
    public function getReviewerByObject($projectID, $object = '')
    {
        $this->app->loadConfig('reviewsetting');
        $roleList = isset($this->config->reviewsetting->reviewer->$object) ? $this->config->reviewsetting->reviewer->$object : array();

        $users = $this->dao->select('t1.account, t1.realname')->from(TABLE_USER)->alias('t1')
            ->leftJoin(TABLE_TEAM)->alias('t2')->on('t1.account=t2.account')
            ->where('t1.deleted')->eq(0)
            ->andWhere('t1.role')->in($roleList)
            ->andWhere('t2.root')->eq($projectID)
            ->fetchPairs();

        return !empty($users) ? $users : array('' => '');
    }

    /**
     * Get no module story.
     *
     * @param  object $review
     * @access public
     * @return void
     */
    public function getNoModuleStory($review)
    {
        $type = $review->category == 'SRS' ? 'story' : 'requirement';
        $modules = $this->dao->select('*')->from(TABLE_MODULE)
            ->where('root')->eq($review->product)
            ->andWhere('type')->eq('story')
            ->andWhere('deleted')->eq(0)
            ->andWhere('grade')->eq(1)
            ->orderBy('`order` asc')
            ->fetchAll();

        $data    = json_decode($review->data);
        $stories = $data->story;
        if(empty($stories)) return;

        $tree  = '';
        $tree .= '<ul>';
        $storyIdList = array();
        foreach($stories as $id => $story)
        {
            if($story->module == 0)
            {
                $tree .= "<li class='story' data-id={$id}><span class='item'>" . html::a(helper::createLink('story', 'view', "storyID=$id", '', true), "#$id" . '：' . $story->title, '', "class='iframe'") . "</span></li>";
                unset($stories->$id);
            }
            $storyIdList[] = $id;
        }

        $moduleIDList = $this->dao->select('module')->from(TABLE_STORY)
            ->where('product')->eq($review->product)
            ->andWhere('type')->eq($type)
            ->andWhere('deleted')->eq(0)
            ->andWhere('id')->in($storyIdList)
            ->orderBy('id_desc')
            ->fetchPairs();

        $tree .= '</ul>';
        $tree .= $this->getStoryTree($review, $modules, $stories, $moduleIDList);
        return $tree;
    }

    /**
     * Get story tree.
     *
     * @param  object $review
     * @param  array  $modules
     * @param  array  $stories
     * @param  array  $moduleIDList
     * @access public
     * @return void
     */
    public function getStoryTree($review, $modules, $stories, $moduleIDList)
    {
        $tree  = '';
        $tree .= '<ul>';
        foreach($modules as $module)
        {
            $tree .= "<li class='module'>" . $module->name;
            if(in_array($module->id, $moduleIDList)) $tree .= '<ul>';
            foreach($stories as $id => $story)
            {
                if($story->module == $module->id)
                {
                    $tree .= "<li class='story' data-id={$id}><span class='item'>" . html::a(helper::createLink('story', 'view', "storyID=$id", '', true), "#$id" . '：' . $story->title, '', "class='iframe'") . "</span></li>";
                    unset($stories->$id);
                }
            }
            if(in_array($module->id, $moduleIDList)) $tree .= '</ul>';

            $childModules = $this->loadModel('doc')->getChildModules($module->id);
            if($childModules) $tree .= $this->getStoryTree($review, $childModules, $stories, $moduleIDList);
            $tree .= '</li>';
        }

        $tree .= '</ul>';
        return $tree;
    }

    /**
     * Get no module case.
     *
     * @param  object $review
     * @access public
     * @return void
     */
    public function getNoModuleCase($review)
    {
        $stage = $review->category == 'STTC' ? 'system' : 'intergrate';
        $modules = $this->dao->select('*')->from(TABLE_MODULE)
            ->where('root')->eq($review->product)
            ->andWhere('grade')->eq(1)
            ->andWhere('type')->in('story,case')
            ->andWhere('deleted')->eq(0)
            ->orderBy('`order` asc')
            ->fetchAll();

        $data  = json_decode($review->data);
        $cases = $data->case;
        if(empty($cases)) return;

        $tree   = '';
        $tree  .= '<ul>';
        foreach($cases as $id => $case)
        {
            if($case->module == 0)
            {
                $tree .= "<li class='case' data-id={$id}><span class='item'>" . html::a(helper::createLink('testcase', 'view', "caseID=$id", '', true), "#$id" . '：' . $case->title, '', "class='iframe'") . "</span></li>";
                unset($cases->$id);
            }
        }

        $moduleIDList = $this->dao->select('module')->from(TABLE_CASE)
            ->where('product')->eq($review->product)
            ->andWhere('deleted')->eq(0)
            ->andWhere('stage')->like("%$stage%")
            ->andWhere('id')->in(array_keys($cases))
            ->orderBy('id_desc')
            ->fetchPairs();

        $tree .= '</ul>';
        $tree .= $this->getCaseTree($review, $modules, $cases, $moduleIDList);
        return $tree;
    }

    /**
     * Get case tree.
     *
     * @param  object $review
     * @param  array  $modules
     * @param  array  $cases
     * @param  array  $moduleIDList
     * @access public
     * @return void
     */
    public function getCaseTree($review, $modules, $cases, $moduleIDList)
    {
        $tree  = '';
        $tree .= '<ul>';
        foreach($modules as $module)
        {
            $tree .= "<li class='module'>" . $module->name;
            if(in_array($module->id, $moduleIDList)) $tree .= '<ul>';
            foreach($cases as $id => $case)
            {
                if($case->module == $module->id)
                {
                    $tree .= "<li class='case' data-id={$case->id}><span class='item'>" . html::a(helper::createLink('testcase', 'view', "caseID=$id", '', true), "#$id" . '：' . $case->title, '', "class='iframe'") . "</span></li>";
                    unset($cases->$id);
                }
            }
            if(in_array($module->id, $moduleIDList)) $tree .= '</ul>';

            $childModules = $this->loadModel('doc')->getChildModules($module->id);
            if($childModules) $tree .= $this->getCaseTree($review, $childModules, $cases, $moduleIDList);
            $tree .= '</li>';
        }

        $tree .= '</ul>';
        return $tree;
    }

    /**
     * Get design tree.
     *
     * @param  object $review
     * @access public
     * @return void
     */
    public function getDesignTree($review)
    {
        $data    = json_decode($review->data);
        $designs = $data->design;

        $tree  = '';
        $tree .= '<ul>';
        foreach($designs as $id => $design)
        {
            $tree .= "<li class='design' data-id={$id}><span class='item'>" . html::a(helper::createLink('design', 'view', "id=$id", '', true), "#$id" . '：' . $design->name, '', "class='iframe'") . "</span></li>";
        }

        $tree .= '</ul>';
        return $tree;
    }

    /**
     * Project: chengfangjinke
     * Method: buildSearchForm
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/9
     * Time: 7:48
     * Desc: This is the code comment. This method is called buildSearchForm.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $queryID
     * @param $actionURL
     */
    public function buildSearchForm($queryID, $actionURL)
    {   
        $this->config->review->search['actionURL'] = $actionURL;
        $this->config->review->search['queryID']   = $queryID;

        $this->loadModel('search')->setSearchParams($this->config->review->search);
    }

    /**
     * Judge button if can clickable.
     *
     * @param  object $review
     * @param  string $action
     * @access public
     * @return void
     */
    public static function isClickable($review, $action)
    {
        global $app;

        $action = strtolower($action);

        $dealUsers  = [];
        if($review->dealUser){
            $dealUsers = explode(',', $review->dealUser);
        }
        $reviewers = [];
        if(isset($review->reviewers)){
            $reviewers = explode(',', $review->reviewers);
        }
        $reviewer = [];
        if(isset($review->reviewer)){
            $reviewer = explode(',', $review->reviewer);
        }
        $qas = [];
        if(isset($review->qa)){
            $qas = explode(',', $review->qa);
        }
        $cms = [];
        if(isset($review->qualityCm)){
            $cms = explode(',', $review->qualityCm);
        }
        $allClose = array_merge($qas,$cms,$reviewer);

        $reviewModel = new reviewModel();

        if($action == 'edit') {
            return (in_array($review->status, $reviewModel->lang->review->allowEditStatusList) && (in_array($app->user->account, $dealUsers)));
        }

        //申请审批
        if($action == 'submit')  {
            return (in_array($review->status, $reviewModel->lang->review->allowSubmitStatusList) && ($review->createdBy == $app->user->account || in_array($app->user->account, $dealUsers)));
        }

        if($action == 'recall') { //撤销
            $notAllowRecallStatusList = $reviewModel->lang->review->notAllowRecallStatusList;
            return (!in_array($review->status, $notAllowRecallStatusList) && ($review->createdBy == $app->user->account));
        }

        //指派
        if($action == 'assign'){
            return (in_array($review->status,$reviewModel->lang->review->allowAssignStatusList) && (in_array($app->user->account, $reviewers)));
        }
        //审核操作
        if($action == 'review'){
            return (in_array($review->status, $reviewModel->lang->review->allowReviewStatusList) && (in_array($app->user->account, $reviewers)));
        }
        if($action == 'reviewreport')  return $review->status == 'reviewpass';

        if($action == 'close') {
            return $review->status != 'close' && !in_array($review->status, $reviewModel->lang->review->notCloseStatusList) &&(in_array($app->user->account, $allClose));;
        }
        if($action == 'delete'){
			return (!in_array($review->status, $reviewModel->lang->review->notAllowDeleteStatusList));
		}
        if($action == 'editnodeusers'){ //是否允许编辑
            if(!in_array($review->currentSubNode, $review->allowEditNodes)){
                return false;
            }
            $res = $reviewModel->getIsAllowEditNodeUsers($review->status);
            return  $res;
        }
        if($action == 'editfiles'){ //是否允许编辑附件
            return  (!in_array($review->status, $reviewModel->lang->review->notAllowEditFileStatusList) && ($review->createdBy == $app->user->account));
        }

        if($action == 'suspend'){ //挂起
            $res = $reviewModel->checkReviewIsAllowSuspend($review);
            return $res['result'];
        }
        if($action == 'renew'){//恢复
            $res =  $reviewModel->checkReviewIsAllowRenew($review, $app->user->account);
            return $res['result'];
        }
        if($action == 'editenddate'){//恢复
            return ((in_array($app->user->account, $reviewer)));
        }
        if($action == 'singlereviewdeal'){//恢复
            return ((in_array($app->user->account, $reviewer)));
        }
        return true;
    }

    /**
     * 获得是否允许编辑节点
     *
     * @param $status
     * @return bool
     */
    public function getIsAllowEditNodeUsers($status){
        $res = true;
        $statusArray = $this->lang->review->notAllowEditNodeUsersStatusList;
        if(in_array($status, $statusArray)){
            $res = false;
        }
        return $res;
    }

    /**
     * Send mail.
     *
     * @param  int    $reviewID
     * @param  int    $actionID
     * @access public
     * @return void
     */
    public function sendmail($reviewID, $actionID,$isAutoSendMail = 0,$dealUser = '',$realReview1 = '',$realReview2 = '',$realReview3 = '')
    {
        $this->loadModel('mail');

        if($isAutoSendMail ==1){
            $isAutoSendMail =1;
            $dealUser = $dealUser;
            $realReview3 = $realReview3;
            $data['toList'] = $dealUser;
            $data['ccList'] = '';
            $sendUsers = $data;
            $users = $this->loadModel('user')->getPairs('noletter');
            $outsideList1 = $this->loadModel('user')->getUsersNameByType('outsideExpertType');
            $outsideList2 = $this->loadModel('user')->getUsersNameByType('outside');
            $users = array_merge($users, $outsideList1, $outsideList2);
            $deptMap = $this->loadModel('dept')->getOptionMenu();

            /* 获取后台通知中配置的邮件发信。*/
            $this->app->loadLang('custommail');
            $mailConf = isset($this->config->global->setReviewMail) ? $this->config->global->setReviewMail : '{"mailTitle":"","variables":[],"mailContent":""}';
            $mailConf = json_decode($mailConf);
            $browseType = 'review';
            if (!empty($realReview1)) {
                $reviewListSend = $realReview1;
                $mailConf->mailTitle = "【待办】您有【%s】个【项目评审】快超时，请尽快登录研发过程管理平台进行处理";
                $mailTitle = vsprintf($mailConf->mailTitle, count($reviewListSend));
                /* 处理邮件标题。*/
                $subject = $mailTitle;
                //$this->sendMailCommon($data, $subject);
                $sendUsers = $data;
                $toList = $sendUsers['toList'];
                $ccList = $sendUsers['ccList'];

                /* Get mail content. */
                $oldcwd     = getcwd();
                $modulePath = $this->app->getModulePath($appName = '', 'review');
                $viewFile   = $modulePath . 'view/sendmail.html.php';
                chdir($modulePath . 'view');

                if(file_exists($modulePath . 'ext/view/sendmail.html.php'))
                {
                    $viewFile = $modulePath . 'ext/view/sendmail.html.php';
                    chdir($modulePath . 'ext/view');
                }
                ob_start();
                include $viewFile;
                foreach(glob($modulePath . 'ext/view/sendmail.*.html.hook.php') as $hookFile) include $hookFile;
                $mailContent = ob_get_contents();
                ob_end_clean();
                /* Send emails. */
                if(empty($toList)) return false;
                $this->mail->send($toList, $subject, $mailContent,$ccList);

                if($this->mail->isError()) trigger_error(join("\n", $this->mail->getError()));
            }
            if (!empty($realReview2)) {
                $reviewListSend = $realReview2;
                $mailConf->mailTitle = "【待办】您有【%s】个【项目评审】已超时，请尽快登录研发过程管理平台进行处理";
                $mailTitle = vsprintf($mailConf->mailTitle, count($reviewListSend));
                /* 处理邮件标题。*/
                $subject = $mailTitle;
                // $this->sendMailCommon($data, $subject);
                $sendUsers = $data;
                $toList = $sendUsers['toList'];
                $ccList = $sendUsers['ccList'];

                /* Get mail content. */
                $oldcwd     = getcwd();
                $modulePath = $this->app->getModulePath($appName = '', 'review');
                $viewFile   = $modulePath . 'view/sendmail.html.php';
                chdir($modulePath . 'view');

                if(file_exists($modulePath . 'ext/view/sendmail.html.php'))
                {
                    $viewFile = $modulePath . 'ext/view/sendmail.html.php';
                    chdir($modulePath . 'ext/view');
                }
                ob_start();
                include $viewFile;
                foreach(glob($modulePath . 'ext/view/sendmail.*.html.hook.php') as $hookFile) include $hookFile;
                $mailContent = ob_get_contents();
                ob_end_clean();
                /* Send emails. */
                if(empty($toList)) return false;
                $this->mail->send($toList, $subject, $mailContent,$ccList);

                if($this->mail->isError()) trigger_error(join("\n", $this->mail->getError()));
            }
            if (!empty($realReview3)) {
                $emilAlertLevel = $this->lang->review->emilAlert;
                $reviewListSend = $realReview3;
                $mailConf->mailTitle = "【通知】您有【%s】个【项目评审】已逾期【%s】天，系统已自动处理，请登录研发过程管理平台查看";
                $mailTitle = vsprintf($mailConf->mailTitle, array(count($reviewListSend), $emilAlertLevel['level2']));
                /* 处理邮件标题。*/
                $subject = $mailTitle;
                $this->sendMailCommon($data, $subject);
                $sendUsers = $data;
                $toList = $sendUsers['toList'];
                $ccList = $sendUsers['ccList'];

                /* Get mail content. */
                $oldcwd     = getcwd();
                $modulePath = $this->app->getModulePath($appName = '', 'review');
                $viewFile   = $modulePath . 'view/sendmail.html.php';
                chdir($modulePath . 'view');

                if(file_exists($modulePath . 'ext/view/sendmail.html.php'))
                {
                    $viewFile = $modulePath . 'ext/view/sendmail.html.php';
                    chdir($modulePath . 'ext/view');
                }
                ob_start();
                include $viewFile;
                foreach(glob($modulePath . 'ext/view/sendmail.*.html.hook.php') as $hookFile) include $hookFile;
                $mailContent = ob_get_contents();
                ob_end_clean();
                /* Send emails. */
                if(empty($toList)) return false;
                $this->mail->send($toList, $subject, $mailContent,$ccList);

                if($this->mail->isError()) trigger_error(join("\n", $this->mail->getError()));
            }
        }else{
            $isAutoSendMail = 0;
            $review = $this->getById($reviewID);
            $companies   = $this->loadModel('company')->getOutsideCompanies();
            $users  = $this->loadModel('user')->getPairs('noletter');
            $outsideList1 = array('' => '') +$this->loadModel('user')->getUsersNameByType('outsideExpertType');
            $outsideList2 = array('' => '') +$this->loadModel('user')->getUsersNameByType('outside');

            $projectPlan = $this->dao->select('id,mark, name, basis, type')->from(TABLE_PROJECTPLAN)->where('project')->eq($review->project)->fetch();
            if($projectPlan){
                $this->app->loadLang('projectplan');
            }
            /*获取初审相关数据*/
            $dataTrial = $this->getTrial($reviewID, $review->version, $users,0);
            $review->trialDept = $dataTrial['deptid'];
            $review->trialDeptLiasisonOfficer = $dataTrial['deptjkr'];
            $review->trialAdjudicatingOfficer = $dataTrial['deptzs'];
            $review->trialJoinOfficer = $dataTrial['deptjoin'];

            /* Get action info. */
            $action          = $this->loadModel('action')->getById($actionID);
            $history         = $this->action->getHistory($actionID);
            $action->history = isset($history[$actionID]) ? $history[$actionID] : array();

            /*获取邮件 收抄件人 主要为了解决（待处理人）获取错误的问题*/
            $sendUsers = $this->getPendingToAndCcList($review, $action);
            //重新设置历史详情为空
            $action->history = array();

            $toList = $sendUsers['toList'];
            //$review->dealUser = $toList;
            $ccList = $sendUsers['ccList'];

            /* 获取后台通知中配置的邮件发信。*/
            $this->app->loadLang('custommail');
            $mailConf   = isset($this->config->global->setReviewMail) ? $this->config->global->setReviewMail : '{"mailTitle":"","variables":[],"mailContent":""}';
            $mailConf   = json_decode($mailConf);
            $browseType = 'review';

            /* 处理邮件发信的标题和日期。*/
            $bestDate  = empty($review->deadline) ? '' : $review->deadline;
            if(in_array($review->status, $this->lang->review->closeStatusList)){
                $review->dealUser = '';
                if($review->status == 'baseline'){
                    $review->dealUser = $review->qualityCm;
                }
                $closeReasonDesc = zget($this->lang->review->closeReasonList, $review->status);
                $mailConf->mailTitle = "【通知】您有一个【%s】已关闭（{$closeReasonDesc}），请及时登录研发过程管理平台查看";
            }else if($review->status == $this->lang->review->statusList['suspend']){  //挂起
                $mailConf->mailTitle = "【通知】您有一个【%s】已挂起，请及时登录研发过程管理平台查看";
            }
            $mailTitle = vsprintf($mailConf->mailTitle, $mailConf->variables);

            $deptMap = $this->loadModel('dept')->getOptionMenu();
            /* Get mail content. */
            $oldcwd     = getcwd();
            $modulePath = $this->app->getModulePath($appName = '', 'review');
            $viewFile   = $modulePath . 'view/sendmail.html.php';
            chdir($modulePath . 'view');

            if(file_exists($modulePath . 'ext/view/sendmail.html.php'))
            {
                $viewFile = $modulePath . 'ext/view/sendmail.html.php';
                chdir($modulePath . 'ext/view');
            }

            ob_start();
            include $viewFile;
            foreach(glob($modulePath . 'ext/view/sendmail.*.html.hook.php') as $hookFile) include $hookFile;
            $mailContent = ob_get_contents();
            ob_end_clean();
            /* 处理邮件标题。*/
            $subject = $mailTitle;

            /* Send emails. */
            if(empty($toList)) return false;
            $this->mail->send($toList, $subject, $mailContent,$ccList);

            if($this->mail->isError()) trigger_error(join("\n", $this->mail->getError()));
        }
    }

    /**
     * autosendmail
     *
     * @access public
     * @return void
     */
    /*
    public function autosendmail()
    {
        $this->loadModel('mail');
        $reviewList = $this->getAllReviewList();
        $dealUsers = '';
        $dealUserList = array();
        foreach ($reviewList as $review){
            $allowReviewStatusList = $this->lang->review->allowReviewStatusList;
            $allowAssignStatusList = $this->lang->review->allowAssignStatusList;
            $status = $review->status;
            if(in_array($status, $allowReviewStatusList) || in_array($status, $allowAssignStatusList)){
                if(!empty($review->reviewers)){
                    $review->dealUser =$review->reviewers ;
                }
            }
            $dealUsers .=$review->dealUser."," ;
        }
        $currentDate = date('Y-m-d H:i:s');
        $dealUserList =Array_filter( array_unique(explode(',',$dealUsers)));
        //获取配置的日期N和M
        $emilAlertLevel = $this->lang->review->emilAlert;
        $realReview1 = array();
        $realReview2 = array();
        $realReview3 = array();
        foreach ($dealUserList as $dealUser) {
            foreach ($reviewList as $review) {
                if (in_array($review->status, $this->lang->review->allowAutoDealStatusList)) {
                    if (strstr($review->dealUser, $dealUser) !== false) {
                        $diffDays = $this->getDiffDate($currentDate, $review->endDate);
                        if ($review->endDate != '0000-00-00 00:00:00') {
                            if ($diffDays != 0) {
                                if ($diffDays == -$emilAlertLevel['level1']) {
                                    $realReview1[] = $review;
                                } elseif ($diffDays == $emilAlertLevel['level2']) {
                                    $realReview2[] = $review;
                                } elseif ($diffDays > $emilAlertLevel['level2']) {
                                    $realReview3[] = $review;
                                }
                            }
                        }
                    }
                }
            }
            $this->sendmail('','',1,$dealUser,$realReview1,$realReview2,$realReview3);
        }

    }
    */

    /**
     * Print datatable cell.
     *
     * @param  object $col
     * @param  object $review
     * @param  array  $users
     * @param  array  $products
     * @access public
     * @return void
     */
    public function printCell($col, $review, $users, $products)
    {
        $canView = common::hasPriv('review', 'view');
        $canBatchAction = false;

        $reviewList = inlink('view', "reviewID=$review->id");
        $account    = $this->app->user->account;
        $id = $col->id;
        if($col->show)
        {
            $class = "c-$id";
            $title = '';
            if($id == 'id') $class .= ' cell-id';
            if($id == 'status')
            {
                $class .= ' status-' . $review->status;
            }
            if($id == 'result')
            {
                $class .= ' status-' . $review->result;
            }
            if($id == 'title')
            {
                $class .= ' text-left';
                $title  = "title='{$review->title}'";
            }

            echo "<td class='" . $class . "' $title>";
            switch($id)
            {
            case 'id':
                if($canBatchAction)
                {
                    echo html::checkbox('reviewIDList', array($review->id => '')) . html::a(helper::createLink('review', 'view', "reviewID=$review->id"), sprintf('%03d', $review->id));
                }
                else
                {
                    printf('%03d', $review->id);
                }
                break;
            case 'title':
                echo html::a(helper::createLink('review', 'view', "reviewID=$review->id"), $review->title);
                break;
            case 'product':
                echo zget($products, $review->product);
                break;
            case 'object':
                echo zget($this->lang->review->objectList, $review->object);
                break;
            case 'version':
                echo $review->version;
                break;
            case 'status':
                echo zget($this->lang->review->statusList, $review->status);
                break;
            case 'type':
                echo zget($this->lang->review->typeList, $review->type);
                break;
            case 'owner':
                $txt='';
                $owners = explode(',', $review->owner);
                foreach($owners as $account)
                {
                    $account = trim($account);
                    if(empty($account)) continue;
                    $txt.= zget($users, $account) . " &nbsp;";
                } 
                echo '<div class="ellipsis" title="' . $txt . '">' . $txt .'</div>';
                break;
            case 'expert':
                $txt='';
                $experts = explode(',', $review->expert);
                foreach($experts as $account)
                {
                    $account = trim($account);
                    if(empty($account)) continue;
                    $txt .= zget($users, $account) . " &nbsp;";
                }
                echo '<div class="ellipsis" title="' . $txt . '">' . $txt .'</div>';
                break;
            case 'reviewedBy':
                $txt='';
                $reviewedBy = explode(',', $review->reviewedBy);
                foreach($reviewedBy as $account)
                {
                    $account = trim($account);
                    if(empty($account)) continue;
                    $txt .= zget($users, $account) . " &nbsp;";
                }
                echo '<div class="ellipsis" title="' . $txt . '">' . $txt .'</div>';
                break;
            case 'createdBy':
                echo zget($users, $review->createdBy);
                break;
            case 'reviewer':
                echo zget($users, $review->reviewer);
                break;
            case 'createdDate':
                echo $review->createdDate;
                break;
            case 'deadline':
                echo $review->deadline;
                break;
            case 'deadline':
                echo $review->deadline;
                break;
            case 'lastReviewedDate':
                echo $review->lastReviewedDate;
                break;
            case 'lastAuditedDate':
                echo $review->lastAuditedDate;
                break;
            case 'result':
                echo zget($this->lang->review->resultList, $review->result);
                break;
            case 'auditResult':
                echo zget($this->lang->review->auditResultList, $review->auditResult);
                break;
            case 'actions':
              $params  = "reviewID=$review->id&status=$review->status";
              common::printIcon('review', 'submit', $params, $review, 'list', 'play', '', 'iframe', true, '', $this->lang->review->submit);
              common::printIcon('review', 'recall', $params, $review, 'list', 'back', 'hiddenwin', '', '', '', $this->lang->review->recall);
              common::printIcon('review', 'assess', $params, $review, 'list', 'glasses');
              common::printIcon('review', 'report',  $params, $review, 'list', 'bar-chart', '');
              if($review->status == 'pass')
              {
                  common::printIcon('cm', 'create', "project=$review->project&" . $params, $review, 'list', 'flag', '', '', '', '', $this->lang->review->createBaseline);
              }
              else
              {
                  common::printIcon('cm', 'create', $params, $review, 'list', 'flag', '', 'disabled');
              }
              common::printIcon('review', 'edit',    $params, $review, 'list');
            }
            echo '</td>';
        }
    }

    /**
     * 检查审核节点是否允许编辑
     *
     * @param $reviewInfo
     * @param $nodeId
     * @return array|void
     */
    public function checkReviewNodeIsAllowEdit($reviewInfo, $nodeId){
        $data = new stdClass();
        $res = array(
            'result'  => false,
            'message' => '',
            'data' => $data,
        );
        if(!($reviewInfo && $nodeId)){
            $res['message'] = $this->lang->common->errorParamId;
            return $res;
        }
        $statusArray = ['pending', 'wait'];
        //查询审批单是否允许审核
        $status = $reviewInfo->status;
        $checkRes = $this->getIsAllowEditNodeUsers($status);
        if(!$checkRes){
            $statusDesc = zget($this->lang->review->statusLabelList, $status);
            $res['message'] = sprintf($this->lang->review->checkEditNodeOpResultList['statusError'], $statusDesc);
            return $res;
        }
        //查询审核节点是否允许审核
        $reviewNode = $this->loadModel('review')->getReviewNodeById($nodeId);
        if(empty($reviewNode)){
            $res['message'] = $this->lang->review->checkEditNodeOpResultList['nodeIdError'];
            return $res;
        }
        //查询状态
        $nodeStatus = $reviewNode->status;
        if(!in_array($nodeStatus, $statusArray)){
            $nodeStatusDesc = zget($this->lang->review->confirmResultList, $nodeStatus);;
            $res['message'] =  sprintf($this->lang->review->checkEditNodeOpResultList['nodeStatusError'], $nodeStatusDesc);
            return $res;
        }
        //查询版本(todo暂时没有实现)
        //查询审核节点下面是否有未审核的人员
        $allowEditReviewers =  $this->loadModel('review')->getUnActionReviewersByNodeId($nodeId);
        if(empty($allowEditReviewers)){
            $res['message'] = $this->lang->review->checkEditNodeOpResultList['reviewersEmpty'];
            return $res;
        }
        $data->reviewNode         = $reviewNode;
        $data->allowEditReviewers = $allowEditReviewers;
        $res['result'] = true;
        $res['data'] = $data;
        return $res;
    }

    /**
     *获得评审主要信息
     *
     * @param $reviewId
     * @param string $select
     * @return mixed
     */
    public function getReviewMainInfo($reviewId, $select = '*'){
        $ret = $this->dao->select($select)
               ->from(TABLE_REVIEW)
               ->where('id')->eq($reviewId)
               ->fetch();
        return $ret;

    }

    /**
     * Desc:回去评审流转状态状态
     * Date: 2022/7/21
     * Time: 16:28
     *
     * @param $reviewID
     *
     */
    public function getStatusById($reviewID){
        return $this->dao->select('status,grade')->from(TABLE_REVIEW)->where('id')->eq($reviewID)->fetch();
    }

    /**
     *获得同一会议单号下的评审信息
     *
     * @param $meetingCode
     * @param string $select
     * @return array|void
     */
    public function getReviewListByMeetingCode($meetingCode, $select = '*', $status = '', $ids = ''){
        $data = [];
        if(!$meetingCode){
            return $data;
        }
        if(empty($ids)){
            $ret = $this->dao->select('review_id')
                ->from(TABLE_REVIEW_MEETING_DETAIL)
                ->where('meetingCode')->eq($meetingCode)
                ->beginIF(!empty($status))->andWhere('status')->eq($status)->fi()
                ->andWhere('deleted')->eq('0')
                ->fetchAll();
            if(!$ret){
                return $data;
            }
            //项目评审ids
            $reviewIds = array_column($ret, 'review_id');
        }else{
            //项目评审ids
            $reviewIds = $ids;
        }
        $ret = $this->dao->select($select)
            ->from(TABLE_REVIEW)
            ->where('meetingCode')->eq($meetingCode)
            ->andWhere('id')->in($reviewIds)
            ->fetchAll();
        if(!$ret){
            return $data;
        }
        $data = $ret;
        return $data;
    }

    /**
     *获得会议单号下待确定会议结论的评审信息
     *
     * @param $meetingCode
     * @param string $select
     * @return array|void
     */
    public function getReviewListByMeetingCodeOnlyWait($meetingCode, $select = '*'){
        $data = [];
        if(!$meetingCode){
            return $data;
        }

        $ret = $this->dao->select('t1.review_id')
            ->from(TABLE_REVIEW_MEETING_DETAIL)->alias('t1')
            ->leftJoin(TABLE_REVIEW)->alias('t2')
            ->on('t1.review_id=t2.id')
            ->where('t1.meetingCode')->eq($meetingCode)
            ->andWhere('t1.deleted')->eq('0')
            ->andWhere('t2.status')->eq('waitMeetingOwnerReview')
            ->fetchAll();
        if(!$ret){
            return $data;
        }

        //项目评审ids
        $reviewIds = array_column($ret, 'review_id');
        $ret = $this->dao->select($select)
            ->from(TABLE_REVIEW)
            ->where('meetingCode')->eq($meetingCode)
            ->andWhere('id')->in($reviewIds)
            ->fetchAll();
        if(!$ret){
            return $data;
        }
        $data = $ret;
        return $data;
    }

    /**
     *获得评审列表
     *
     * @param $reviewIds
     * @param string $select
     * @return array|null
     */
    public function getReviewListByIds($reviewIds, $select = '*'){
        $data = [];
        if(!$reviewIds) {
            return $data;
        }

        $ret = $this->dao->select($select)
            ->from(TABLE_REVIEW)
            ->where('id')->in($reviewIds)
            ->fetchAll('id');

        if($ret){
            $data = $ret;
        }
        return $data;
    }

    /**
     *获得评审验证用户列表
     *
     * @param $reviewIds
     * @return array|void
     */
    public function getVerifyUserList($reviewIds){
        $data = [];
        if(!$reviewIds){
            return $data;
        }
        $reviewList = $this->getReviewListByIds($reviewIds, 'id, version, isFirstReview, reviewedBy,outside');
        if(!$reviewList){
            return $data;
        }
        foreach ($reviewList as $val){
            $reviewId = $val->id;
            $version  = $val->version;
            if($val->isFirstReview == '2'){ //当跳过初审时
                $nodeCode = 'formalReview';
            }else{//获得评审验证人员默认为初审人员
                $nodeCode = 'firstReview';
            }
            $verifyReviewers = $this->getReviewersByNodeCode('review', $reviewId, $version, $nodeCode);
            if($verifyReviewers && $val->isFirstReview == '2'){ //正式评审去掉外部专家1和外部专家2
                $reviewedBy = $val->reviewedBy;
                $outside    = $val->outside;
                $verifyReviewersArray = explode(',', $verifyReviewers);
                //外部专家1
                if($reviewedBy){
                    $reviewedByArray = explode(',', $reviewedBy);
                    $verifyReviewersArray = array_diff($verifyReviewersArray, $reviewedByArray);
                }
                //外部专家2
                if($outside){
                    $outsideArray = explode(',', $outside);
                    $verifyReviewersArray = array_diff($verifyReviewersArray, $outsideArray);
                }
                $verifyReviewers = implode(',', $verifyReviewersArray);
            }
            $data[$reviewId] = $verifyReviewers;
        }
        return $data;
    }

    /**
     *获得审核提示信息
     *
     * @param $status
     * @return mixed
     */
    public function getReviewTipMsg($status){
        $reviewTipMsg = $this->lang->review->review;
        if(in_array($status, $this->lang->review->allowInMeetingReviewStatusList)){
            $reviewTipMsg =  $this->lang->review->meetingReviewTipMsg;
        }
        return $reviewTipMsg;
    }

    /**
     * Desc:根据评审id获取会议编号
     * Date: 2022/9/5
     * Time: 16:42
     *
     * @param int $id
     *
     */
    public function getMeetingById($id)
    {
        return $this->dao->select('id,meetingCode')->from(TABLE_REVIEW)->where('id')->eq($id)->fetchPairs('id', 'meetingCode');
    }

    /**
     * 检查项目评审是否允许挂起
     *
     * @author wangjiurong
     * @param $reviewInfo
     * @return array|void
     */
    public function checkReviewIsAllowSuspend($reviewInfo){
        $res = array(
            'result'  => false,
            'message' => '',
        );
        if(!$reviewInfo){
            $res['message'] = $this->lang->common->errorParamId;
            return $res;
        }

        //当前状态
        $status = $reviewInfo->status;
        if(in_array($status, $this->lang->review->notSuspendStatusList)){
            $statusDesc = zget($this->lang->review->statusLabelList, $status);
            $res['message'] = sprintf($this->lang->review->checkSuspendResultList['statusError'], $statusDesc);
            return $res;
        }
        $res['result'] = true;
        return $res;
    }

    /**
     * 检查项目评审是否允许恢复
     *
     * @author wangjiurong
     * @param $reviewInfo
     * @param $userAccount
     * @return array|void
     */
    public function checkReviewIsAllowRenew($reviewInfo, $userAccount){
        $res = array(
            'result'  => false,
            'message' => '',
        );
        if(!$reviewInfo){
            $res['message'] = $this->lang->common->errorParamId;
            return $res;
        }

        //当前状态
        $status = $reviewInfo->status;
        if($status != $this->lang->review->statusList['suspend']){
            $statusDesc = zget($this->lang->review->statusLabelList, $status);
            $res['message'] = sprintf($this->lang->review->checkRenewResultList['statusError'], $statusDesc);
            return $res;
        }
        $dealUsers = explode(',', $reviewInfo->dealUser);
        if(!in_array($userAccount, $dealUsers)){
            $res['message'] = $this->lang->review->checkRenewResultList['userError'];
            return $res;
        }
        $res['result'] = true;
        return $res;
    }

    /**
     * 检查项目评审是否允许给出验证结论
     *
     * @author wangjiurong
     * @param $reviewInfo
     * @param $userAccount
     * @return array|void
     */
    public function checkReviewIsAllowSetVerifyResult($reviewInfo, $userAccount){
        $res = array(
            'result'  => false,
            'message' => '',
        );
        if(!$reviewInfo){
            $res['message'] = $this->lang->common->errorParamId;
            return $res;
        }

        //当前状态
        $status = $reviewInfo->status;
        $type = $reviewInfo->type;
        if(!in_array($status, $this->lang->review->allowSetVerifyResultStatusList)){
            $res['message'] = $this->lang->review->checkResultList['statusError'];
            return $res;
        }
        if(!in_array($type, $this->lang->review->organizationTypeList)){
            $res['message'] = $this->lang->review->checkResultList['notOrganizationTypeError'];
            return $res;
        }
        //管理员评审专员
        $dealUsers = ['admin', $reviewInfo->reviewer];
        if(!in_array($userAccount, $dealUsers)){
            $res['message'] = $this->lang->review->checkResultList['userError'];
            return $res;
        }
        $res['result'] = true;
        return $res;
    }

    /**
     * 检查项目评审是否允许手动发送验证邮件
     *
     * @author wangjiurong
     * @param $reviewInfo
     * @param $userAccount
     * @return array|void
     */
    public function checkIsAllowSendUnDealIssueUsersMail($reviewInfo, $userAccount){
        $res = array(
            'result'  => false,
            'message' => '',
        );
        if(!$reviewInfo){
            $res['message'] = $this->lang->common->errorParamId;
            return $res;
        }

        //管理员评审专员
        $dealUsers = ['admin', $reviewInfo->reviewer];
        if(!in_array($userAccount, $dealUsers)){
            $res['message'] = $this->lang->review->checkResultList['userError'];
            return $res;
        }
        $reviewId = $reviewInfo->id;
        $type  = $reviewInfo->type;
        $owner = $reviewInfo->owner; //评审主席
        //查询是否有待处理的问题
        $unDealReviewIssueUsers =  $this->loadModel('reviewissue')->getUnDealReviewIssueUsers([$reviewId]);
        $raiseByUsers = isset($unDealReviewIssueUsers[$reviewId]) ? $unDealReviewIssueUsers[$reviewId] : [];
        if(empty($raiseByUsers)){
            $res['message'] = $this->lang->review->checkSendMailList['unDealIssueEmptyError'];
            return $res;
        }
        if(in_array($type, $this->lang->review->organizationTypeList)){ //组织级评审,去掉评审主席
            $raiseByUsers = array_diff($raiseByUsers, [$owner]);
            if(empty($raiseByUsers)){
                $res['message'] = $this->lang->review->checkSendMailList['unDealIssueOwnerUserError'];
                return $res;
            }
        }


        $res['result'] = true;
        $res['data'] = $raiseByUsers;
        return $res;
    }

    /**
     *获得格式化查询条件
     *
     * @param $reviewQuery
     * @return array|mixed|string|string[]
     */
    public function getFormatSearchQuery($reviewQuery){
        //匹配查找模式
        $pattern = "/(t1.status = ').*?(')/";
        preg_match($pattern, $reviewQuery, $patternRes);
        if(!empty($patternRes[0])){
            //获取查询状态
            $findInfo = $patternRes[0];
            $findStatus = substr($findInfo, 13, -1);
            $includeMultipleStatusList = $this->lang->review->includeMultipleStatusList;
            $multipleStatusArray = array_keys($includeMultipleStatusList);
            if(in_array($findStatus, $multipleStatusArray)){
                $includeStatusArray = $includeMultipleStatusList[$findStatus];
                $replaceInfo = "t1.status in ('" . implode("','", $includeStatusArray) . "') ";
                $reviewQuery = str_replace($findInfo, $replaceInfo, $reviewQuery);
            }
        }
        $pattern = "/t1.suspendBy/";
        preg_match($pattern, $reviewQuery, $patternRes);
        if(!empty($patternRes[0])){
            $reviewQuery .= " AND ( t1.suspendBy != '') ";
        }
        $pattern = "/t1.suspendTime/";
        preg_match($pattern, $reviewQuery, $patternRes);
        if(!empty($patternRes[0])){
            $reviewQuery .= " AND ( t1.suspendTime != '0000-00-00 00:00:00') ";
        }

        $pattern = "/t1.renewBy/";
        preg_match($pattern, $reviewQuery, $patternRes);
        if(!empty($patternRes[0])){
            $reviewQuery .= " AND ( t1.renewBy != '') ";
        }
        $pattern = "/t1.renewTime/";
        preg_match($pattern, $reviewQuery, $patternRes);
        if(!empty($patternRes[0])){
            $reviewQuery .= " AND ( t1.renewTime != '0000-00-00 00:00:00') ";
        }
        return $reviewQuery;
    }

    /**
     *从日志中获得会议单号
     *
     * @param array $logChanges
     * @return mixed|string
     */
    public function getMeetingCodeInLogChanges($logChanges = []){
        $meetingCode = '';
        if(!$logChanges){
            return $meetingCode;
        }
        foreach ($logChanges as $val){
            if($val['field'] == 'meetingCode'){
                $meetingCode = $val['new'];
                break;
            }
        }
        return  $meetingCode;
    }

    /**
     *获得项目评审打基线信息
     *
     * @param $reviewInfo
     * @return array
     */
    public function getBaseLineInfo($reviewInfo){
        $data = [];
        if(!$reviewInfo){
            return $data;
        }
        $baseLineType = $reviewInfo->baseLineType;
        $baseLinePath = $reviewInfo->baseLinePath;
        $baseLineTime = $reviewInfo->baseLineTime;
        if(!$baseLineType){
            return $data;
        }
        $baseLineTypeArray = explode(',', $baseLineType);
        $baseLinePathArray = explode(',', $baseLinePath);
        foreach ($baseLineTypeArray as $key => $baseLineType){
            $baseLinePath = $baseLinePathArray[$key];
            $temp = new stdClass();
            $temp->baseLineType = $baseLineType;
            $temp->baseLinePath = $baseLinePath;
            $temp->baseLineTime = $baseLineTime;
            $data[] = $temp;
        }
        return $data;
    }
    /**
     * 获取所属项目中第一个关闭的管理评审时间，作为报工时间区间限制条件
     * @param $projectID
     */
    public function getCloseDate($projectID){
        $res = $this->dao->select('closeDate')->from(TABLE_REVIEW)
            ->where('type')->eq('manage')
            ->andWhere('project')->eq($projectID)
            ->andWhere('deleted')->eq(0)
            ->andWhere('closeDate')->ne('0000-00-00')
            ->orderBy('id asc')
            ->limit(1)->fetch();

        return $res;
    }

    /**
     * @Notes:获取reviewnode数据
     * @Date: 2023/4/20
     * @Time: 13:49
     * @Interface getNodeInfoByParams
     * @param $field
     * @param $status
     * @param $version
     * @param $objectType
     * @param $objectID
     * @param $stage
     * @return mixed
     */
    function getNodeInfoByParams($field,$status,$version,$objectType,$objectID,$stage)
    {
        $node = $this->dao->select($field)->from(TABLE_REVIEWNODE)
            ->where('`status`')->eq($status)
            ->andWhere('version')->eq($version)
            ->andWhere('objectType')->eq($objectType)
            ->andWhere('objectID')->eq($objectID)
            ->andWhere('stage')->eq($stage)
            ->fetch();
        return $node;
    }

    /**
     * @Notes:获取节点对应的数据
     * @Date: 2023/4/20
     * @Time: 13:52
     * @Interface getReviewerInfoByParams
     * @param $field
     * @param $status
     * @param $node
     * @return mixed
     */
    function getReviewerInfoByParams($field,$status,$node)
    {
        $info = $this->dao->select($field)->from(TABLE_REVIEWER)
            ->where('`status`')->eq($status)
            ->andWhere('node')->eq($node)
            ->orderBy('id asc')
            ->fetch();
        return $info;
    }

    /**
     * 判断指定用户是否属于初审人员主审人员
     *
     * @param $reviewId
     * @param $version
     * @param $userAccount
     * @return bool
     */
    public function checkIsFirstMainReviewer($reviewId, $version, $userAccount){
        $isFirstMainReviewer =  false;
        if(!($reviewId && $userAccount)){
            return $isFirstMainReviewer;
        }
        $nodeCode = $this->lang->review->nodeCodeList['firstMainReview'];
        $reviewers = $this->loadModel('review')->getReviewersByNodeCode('review', $reviewId, $version, $nodeCode, 'array');
        if(in_array($userAccount, $reviewers)){
            $isFirstMainReviewer = true;
        }
        return $isFirstMainReviewer;
    }

    /**
     * 是否设置建议评审方式
     *
     * @param $review
     * @param $userAccount
     * @return bool
     */
    public function isSetAdviceGrade($review, $userAccount){
        $isSetAdviceGrade = false;
        $status = $review->status;
        if(in_array($status, $this->lang->review->allowFirstMainReviewStatusList)){
            $isSetAdviceGrade = true;
            return $isSetAdviceGrade;
        }
        if(in_array($review->status, $this->lang->review->allowFirstJoinReviewStatusList)){
            $isFirstMainReviewer = $this->checkIsFirstMainReviewer($review->id, $review->version, $userAccount);
            if($isFirstMainReviewer){
                $isSetAdviceGrade = true;
            }
        }
        return $isSetAdviceGrade;
    }

    public function getFirstPendingNode($objType,$objID,$version=0,$status='pending'){
        return $this->dao->select("*")->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq($objType)
            ->andWhere('objectID')->eq($objID)
            ->andWhere('version')->eq($version)
            ->andWhere('status')->eq($status)
            ->orderBy('stage,id')
            ->fetch();
    }

    /**
     * 查询是否允许操作附件
     *
     * @param $review
     * @return bool
     */
    public function isAllowOperateFile($review){
        $isOperate = false;
        $canOperateUsers = ['admin',$review->createdBy,$review->reviewer];
        if(!in_array($review->status, $this->lang->review->fileCanOperateList) && in_array($this->app->user->account, $canOperateUsers)){
            $isOperate = true;
        }
        return $isOperate;
    }

    /**
     * 检查项目评审是否允许根据字段修改用户信息
     *
     * @author wangjiurong
     * @param $reviewInfo
     * @param $userAccount
     * @return array|void
     */
    public function checkIsAllowEditUsersByField($reviewInfo, $userAccount){
        $res = array(
            'result'  => false,
            'message' => '',
        );
        if(!$reviewInfo){
            $res['message'] = $this->lang->common->errorParamId;
            return $res;
        }

       $allowUsers = [
           'admin',
           $reviewInfo->reviewer,
       ];
        if(!in_array($userAccount, $allowUsers)){
            $res['message'] = $this->lang->review->checkResultList['userError'];
            return $res;
        }
        $res['result'] = true;
        return $res;
    }

    /**
     * 获得是否包含不需要初审的对象
     *
     * @param $reviewObjects
     * @return bool
     */
    public function getIsIncludeNotNeedFirstReviewObject($reviewObjects){
        $isIncludeSpecifyObject = false;
        if(!$reviewObjects){
            return $isIncludeSpecifyObject;
        }
        if(!is_array($reviewObjects)){
            $reviewObjects = array_filter(explode(',', $reviewObjects));
        }
        $intersectObject = array_intersect($reviewObjects, $this->lang->review->notNeedFirstReviewObjects);
        if(!empty($intersectObject)){
            $isIncludeSpecifyObject = true;
        }
        return $isIncludeSpecifyObject;
    }
    /**
     * 是否需要显示安全测试选项
     *
     * @param $reviewObject
     * @param $reviewType
     * @return bool
     */
    public function isShowSafetyTest($reviewObject, $reviewType){
        $isShowSafetyTest = false;
        if(trim($reviewObject, ',') == 'PP' && $reviewType == 'manage'){
            $isShowSafetyTest = true;
        }
        return $isShowSafetyTest;
    }

}
