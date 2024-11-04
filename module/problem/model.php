<?php
class problemModel extends model
{
    public static $_dealStatus = [ 'wait','confirmed','assigned','toclose'];
    public static $_notStatus = [ 'suspend','closed','returned'];
    /**
     * Project: chengfangjinke
     * Method: getList
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:53
     * Desc: This is the code comment. This method is called getList.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $browseType
     * @param $queryID
     * @param $orderBy
     * @param null $pager
     * @return mixed
     */
    public function getList($browseType, $queryID, $orderBy, $pager = null , $flag)
    {
        $problemQuery = '';
        if($browseType == 'bysearch')
        {
            $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : '';
            if($query)
            {
                $this->session->set('problemQuery', $query->sql);
                $this->session->set('problemForm', $query->form);
            }
            if($this->session->problemQuery == false) $this->session->set('problemQuery', ' 1 = 1');

            $problemQuery = $this->session->problemQuery;
            // 处理受影响的业务系统搜索字段
            if(strpos($problemQuery, '`app`') !== false)
            {
                $problemQuery = str_replace('`app`', "CONCAT(',', `app`, ',')", $problemQuery);
            }

            // 处理[系统分类]搜索字段
            if(strpos($problemQuery, '`isPayment`') !== false)
            {
                $problemQuery = str_replace('`isPayment`', "CONCAT(',', `isPayment`, ',')", $problemQuery);
            }

            $problemQuery = str_replace("`IfultimateSolution` = ''", "`IfultimateSolution` = 0", $problemQuery);
            $problemQuery = str_replace("`ifReturn` = ''", "`ifReturn` = 0", $problemQuery);
            $problemQuery = str_replace("`problemFeedbackId`", "`IssueId`", $problemQuery);
            $problemQuery = str_replace("`ultimateSolution`", "`solution`", $problemQuery);

            //反馈次数= feedbackNum -1
            if(strpos($problemQuery, '`feedbackNum`') !== false)
            {
                $problemQuery = str_replace("`feedbackNum` = '1'", "`feedbackNum` <= '1'", $problemQuery);

            }
            //受理状态
            if(strpos($problemQuery, '`ifReturn`') !== false)
            {
                $problemQuery = $problemQuery."AND status != 'confirmed'";

            }
        }
        if(strpos($orderBy, 'ifRecive') !== false){
            $orderBy = str_replace('ifRecive','ifReturn',$orderBy);
        }
        $field = 't2.originalResolutionDate,t2.delayResolutionDate,t2.delayReason,t2.delayStatus,t2.delayVersion,t2.delayStage,t2.delayDealUser,t2.delayUser,t2.delayDate,t2.id as delayId,t4.id changeId ,t4.changeCommunicate,t4.successVersion,t4.changeStatus,t4.changeDealUser';
        $consumedSql = "select objectID from " . TABLE_CONSUMED . " where objectType = 'problem' and account = '".$this->app->user->account."' and deleted = 0";
        $reviewSql   = "SELECT t3.objectID from zt_reviewnode as t3 INNER JOIN zt_reviewer as t4 on t3.id = t4.node and t3.objectType in ('problem', 'problemDelay','problemChange') where t3.objectType in ('problem', 'problemDelay','problemChange') and t4.reviewer = '".$this->app->user->account."'";
        $problems = $this->dao
            ->select("t1.*,{$field}")
            ->from(TABLE_PROBLEM)->alias('t1')
            ->leftJoin(TABLE_DELAY)->alias('t2')
            //->on("t1.id = t2.objectId and t2.objectType = 'problem' and t2.isEnd = '1'") // 暂时去掉isEnd = 1 条件
            ->on("t1.id = t2.objectId and t2.objectType = 'problem' ")
            ->leftJoin(TABLE_PROBLEM_CHANGE)->alias('t4')
            ->on("t1.id = t4.objectId and t4.objectType = 'problem' and  t4.id = (select id from zt_problem_change where objectId= t1.id order by changeVersion desc limit 1 )")
            ->where('t1.status')->ne('deleted')
            ->beginIF($browseType != 'all' and $browseType != 'bysearch' and $browseType != 'me'  and $flag != 'true')->andWhere('t1.status')->eq($browseType)->fi()
            ->beginIF($browseType == 'me'  and $flag != 'true')
            ->andWhere()->markLeft(1)
            ->where('t1.id in ('.$consumedSql.')')
            ->orWhere('t1.id in ('.$reviewSql.')')
            ->orWhere('t1.createdBy')->eq($this->app->user->account)
            ->orWhere('t1.dealUser')->eq($this->app->user->account)
            ->markRight(1)
            ->fi()
            ->beginIF($flag)->andWhere('t1.ReviewStatus')->eq($browseType)->fi()
            ->beginIF($browseType == 'bysearch')->andWhere($problemQuery)->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');
//        a($this->dao->get());die;

        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'problem', $browseType != 'bysearch');
        $dmap = $this->dao->select('account,dept')->from(TABLE_USER)->fetchAll('account');
        foreach($problems as $key => $problem)
        {
            //开发中 测试中 已发布 已交付 上线成功 处理人置空显示 已关闭也不显示待处理人
            if(in_array($problem->status, ['feedbacked','build','released','delivery','onlinesuccess','closed'])){
                $problems[$key]->dealUser  = '';
                $problems[$key]->feedbackToHandle  = $problem->status == 'closed' ? '' : $problem->feedbackToHandle;
            }
            if(isset($dmap[$problem->createdBy]->dept)){
                $problems[$key]->createdDept = $dmap[$problem->createdBy]->dept;
            }
            $res = $this->checkAllowReview($problem, $problem->version, $problem->reviewStage, $this->app->user->account);
            $problems[$key]->feedBackFlag = $res['result'];

            $closeUser = $this->dao->select('objectID, account')
                ->from(TABLE_CONSUMED)
                ->where('objectType')->eq('problem')
                ->andWhere('objectID')->eq($problem->id)
                ->andWhere('`before`')->eq('confirmed')
                ->andWhere('deleted')->eq('0')
                ->orderBy('id_desc')
                ->fetch();
            $problem->closeUser = $closeUser->account ?? '';
        }
        return $problems;
    }

    /**
     * Project: chengfangjinke
     * Method: buildSearchForm
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:54
     * Desc: This is the code comment. This method is called buildSearchForm.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $queryID
     * @param $actionURL
     */
    public function buildSearchForm($queryID, $actionURL)
    {
        $this->config->problem->search['actionURL'] = $actionURL;
        $this->config->problem->search['queryID']   = $queryID;
        $this->config->problem->search['params']['createdBy']['values'] = array('' => '') + $this->loadModel('user')->getPairs('noletter|noclosed');

        $this->loadModel('search')->setSearchParams($this->config->problem->search);
    }

    /**
     * Project: chengfangjinke
     * Method: create
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:54
     * Desc: This is the code comment. This method is called create.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @return mixed
     */
    public function create()
    {
        $data = fixer::input('post')
            ->remove('consumed,uid,files')
            ->join('app', ',')
            ->stripTags($this->config->problem->editor->create['id'], $this->config->allowedTags)->get();

        $data->createdBy    = $this->app->user->account;
        $data->app          = $data->app ?? '';
        $data->createdDate  = helper::today();
        $data->createdDept  = $this->app->user->dept;
        $data->lastDealDate = date('Y-m-d');
        $data->status       = 'confirmed';
        $data = $this->loadModel('file')->processImgURL($data, $this->config->problem->editor->create['id'], $this->post->uid);
        $applicationInfo = $this->loadModel('application')->getapplicationInfo();
        if(!empty($data->app)){
            $as = [];
            foreach(explode(',', $data->app) as $apptype)
            {
                if(!$apptype) continue;
                $as[] = zget($applicationInfo, $apptype,"",$applicationInfo[$apptype]->isPayment);
            }
            $applicationtype = implode(',', $as);
            if(!empty($applicationtype))$applicationtype=",".$applicationtype;
            $data->isPayment=$applicationtype;
        }
       /* $consumed = $_POST['consumed'];
        if(empty($consumed))
        {
            $errors['consumed'] = sprintf($this->lang->problem->emptyObject, $this->lang->problem->consumed);
            return dao::$errors = $errors;
        }
        $reg = '/^(([1-9][0-9]*)|(([0]\.\d{1}|[1-9][0-9]*\.\d{1})))$/';
        if (!preg_match($reg, $this->post->consumed))
        {
            dao::$errors['consumed'] = $this->lang->problem->consumedError;
            return false;
        }*/
        $this->dao->insert(TABLE_PROBLEM)->data($data)->autoCheck()->batchCheck($this->config->problem->create->requiredFields, 'notempty')->exec();
        $problemID = $this->dao->lastInsertId();
        if(!dao::isError())
        {
            $date   = date('Y-m-d');
            $number = $this->dao->select('count(id) c')->from(TABLE_PROBLEM)->where('createdDate')->eq($date)->fetch('c');
            $code   = 'CFIT-Q-' . date('Ymd-') . sprintf('%02d', $number);

            $this->dao->update(TABLE_PROBLEM)->set('code')->eq($code)->where('id')->eq($problemID)->exec();
            $this->post->consumed = 0;
            $this->loadModel('consumed')->record('problem', $problemID, $this->post->consumed, $this->app->user->account, '', 'confirmed', array());

            $this->loadModel('file')->updateObjectID($this->post->uid, $problemID, 'problem');
            $this->file->saveUpload('problem', $problemID);
        }

        return $problemID;
    }

    /**
     * Project: chengfangjinke
     * Method: update
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:54
     * Desc: This is the code comment. This method is called update.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $problemID
     * @return array
     */
    public function update($problemID)
    {
        $oldProblem = $this->getByID($problemID);
        $problem = fixer::input('post')
            ->remove('uid,files,labels,consumed,comment')
            ->join('app', ',')
            ->striptags($this->config->problem->editor->edit['id'], $this->config->allowedTags)
            ->get();
        //api 问题 受影响业务系统保留原值
        if($oldProblem->IssueId){
            $problem->app = $oldProblem->app;
        }
        //判断工作量是否为空
       /* $consumed = $_POST['consumed'];
        if(empty($consumed))
        {
            $errors['consumed'] = sprintf($this->lang->problem->emptyObject, $this->lang->problem->consumed);
            return dao::$errors = $errors;
        }
        else
        {
            if(!is_numeric($consumed))
            {
                $errors['consumed'] = sprintf($this->lang->problem->noNumeric, $this->lang->problem->consumed);
                return dao::$errors = $errors;
            }
            $reg = '/^(([1-9][0-9]*)|(([0]\.\d{1}|[1-9][0-9]*\.\d{1})))$/';
            if (!preg_match($reg, $this->post->consumed))
            {
                dao::$errors['consumed'] = $this->lang->problem->consumedError;
                return false;
            }
        }*/

        $problem->editedBy   = $this->app->user->account;
        $problem->app   = $problem->app ?? '';
        $problem->editedDate = date('Y-m-d');
        $problem->status       = 'confirmed';
        $problem->ifReturn = $problem->ifReturn == '1' ? '0' : $problem->ifReturn; //是否受理问题 如果未受理，更新为受理
        $problem = $this->loadModel('file')->processImgURL($problem, $this->config->problem->editor->edit['id'], $this->post->uid);
        $applicationInfo = $this->loadModel('application')->getapplicationInfo();
        if(!empty($problem->app)){
            $as = [];
            foreach(explode(',', $problem->app) as $apptype)
            {
                if(!$apptype) continue;
                $as[] = zget($applicationInfo, $apptype,"",$applicationInfo[$apptype]->isPayment);
            }
            $applicationtype = implode(',', $as);
            if(!empty($applicationtype))$applicationtype=",".$applicationtype;
            $problem->isPayment=$applicationtype;
        }
        $this->dao->update(TABLE_PROBLEM)->data($problem)->autoCheck()
            ->batchCheck($this->config->problem->edit->requiredFields, 'notempty')
            ->where('id')->eq($problemID)
            ->exec();

        //修改工作量
        $consumed = 0;
        $this->dao->update(TABLE_CONSUMED)->set('consumed')->eq($consumed) //修改工作量
            ->where('id')->eq(end($oldProblem->consumed)->id)
            ->exec();

        $this->loadModel('file')->updateObjectID($this->post->uid, $problemID, 'problem');
        $this->file->saveUpload('problem', $problemID);
       /*
        $editConsumed = Array();
        if(end($oldProblem->consumed)->consumed != $consumed) {
            // 外部单只编辑工作量记录
            $editConsumed['consumed']->old   = end($oldProblem->consumed)->consumed;
            $editConsumed['consumed']->new   = $consumed;
        }*/

        //return common::createChanges($oldProblem, $problem, $editConsumed);
        return common::createChanges($oldProblem, $problem);
    }

    /**
     * Project: chengfangjinke
     * Method: editSpecial
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:54
     * Desc: This is the code comment. This method is called editSpecial.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $problemID
     * @return array
     */
    public function editSpecial($problemID)
    {
        $oldProblem = $this->getByID($problemID);
        $problem = fixer::input('post')
            ->remove('uid')
            ->stripTags($this->config->problem->editor->editspecial['id'], $this->config->allowedTags)
            ->get();

        // 当前进展追加
        $problem->progress = trim(str_replace('&nbsp;','',$problem->progress));
        if ($problem->progress) {
             $users = $this->loadModel('user')->getPairs('noclosed');
             $progress = '<span style="background-color: #ffe9c6">' . helper::now() . " 由<strong>" . zget($users, $this->app->user->account, '') . "</strong>新增" . '<br></span>' . $problem->progress;
             $problem->progress = $oldProblem->progress . '<br>' . $progress;
         }else{
            $problem->progress = $oldProblem->progress;
        }

        $problem = $this->loadModel('file')->processImgURL($problem, $this->config->problem->editor->editspecial['id'], $this->post->uid);
        $this->dao->update(TABLE_PROBLEM)->data($problem)
            ->where('id')->eq($problemID)
            ->exec();
        $flag = $this->lang->problem->OverDateList;
        //同步当前进展开关  有外部单号且 当前进展有变化 且问题单创建人为清算总中心
        if($flag['openType'] && $oldProblem->IssueId && $oldProblem->createdBy == 'guestcn' && $problem->progress != $oldProblem->progress  ){
            $pushEnable = $this->config->global->pushProblemCommentEnable;
            if($pushEnable == 'enable'){
                $url = $this->config->global->pushProblemCommentUrl;
                $pushAppId = $this->config->global->pushProblemCommentAppId;
                $pushAppSecret = $this->config->global->pushProblemCommentAppSecret;
                $headers = array();
                $headers[] = 'App-Id: ' . $pushAppId;
                $headers[] = 'App-Secret: ' . $pushAppSecret;

                $pushData = array();
                $pushData['IssueId']     = $oldProblem->IssueId;

                $pushData['problemJinKeStatus']                       = html_entity_decode(strip_tags(str_replace("<br />","\n",strval(htmlspecialchars_decode($problem->progress,ENT_QUOTES)))));//富文本7


                $result = $this->loadModel('requestlog')->http($url, $pushData, 'POST', 'json', array(), $headers);
                $status = 'fail';
                if (!empty($result)) {
                    $resultData = json_decode($result);
                    if ($resultData->code == '200' || $resultData->isSave == 1 ) { //200 = 成功的 isSave == 1 代表成功保存 比如第一次没响应 再次请求
                        $status = 'success';
                    }
                    $response = $result;
                } else {
                    $response = "对方无响应";
                }
                $this->requestlog->saveRequestLog($url, 'problem', 'pushProblemComment', 'POST', $pushData, $response, $status, '', $problemID);
            }

        }
        $this->loadModel('file')->updateObjectID($this->post->uid, $problemID, 'problem');
        $this->file->saveUpload('problem', $problemID);

        return common::createChanges($oldProblem, $problem);
    }

    /**
     * qA追加工作进展
     * @param $problemID
     * @return array
     */
    public function editSpecialQA($problemID)
    {
        $oldProblem = $this->getByID($problemID);
        $problem = fixer::input('post')
            ->remove('uid')
            //->stripTags($this->config->problem->editor->editspecialqa['id'], $this->config->allowedTags)
            ->get();

        $this->dao->update(TABLE_PROBLEM)
            ->data($problem)
            ->where('id')->eq($problemID)
            ->exec();

        return common::createChanges($oldProblem, $problem);
    }
  /**
   * 同步反馈单状态
   */
    public function syncFeedBackStatus($problemID,$statusName)
    {
        $oldProblem = $this->getByID($problemID);
        $flag = $this->lang->problem->OverDateList;
        //同步当前进展开关  有外部单号
        /*if($flag['openType'] && $oldProblem->IssueId && $oldProblem->createdBy == 'guestcn'){
            $pushEnable = $this->config->global->pushProblemCommentEnable;
            if($pushEnable == 'enable'){
                $url = $this->config->global->pushProblemCommentUrl;
                $pushAppId = $this->config->global->pushProblemCommentAppId;
                $pushAppSecret = $this->config->global->pushProblemCommentAppSecret;
                $headers = array();
                $headers[] = 'App-Id: ' . $pushAppId;
                $headers[] = 'App-Secret: ' . $pushAppSecret;

                $pushData = array();
                $pushData['IssueId']     = $oldProblem->IssueId;
                $pushData['jinKeApprovalStatus']  = $statusName;

                $result = $this->loadModel('requestlog')->http($url, $pushData, 'POST', 'json', array(), $headers);
                $status = 'fail';
                if (!empty($result)) {
                    $resultData = json_decode($result);
                    if ($resultData->code == '200' || $resultData->isSave == 1 ) { //200 = 成功的 isSave == 1 代表成功保存 比如第一次没响应 再次请求
                        $status = 'success';
                    }
                    $response = $result;
                } else {
                    $response = "对方无响应";
                }
                $this->requestlog->saveRequestLog($url, 'problem', 'pushProblemStatus', 'POST', $pushData, $response, $status, '', $problemID);
            }

        }*/

    }

    /**
     * Project: chengfangjinke
     * Method: editAssignedTo
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:54
     * Desc: This is the code comment. This method is called editAssignedTo.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $problemID
     * @return array
     */
    public function editAssignedTo($problemID)
    {
        // Obtain the receiver.
        //张二欢5月31号关闭此判断
       /* $acceptUser = $this->dao->select('*')->from(TABLE_CONSUMED)
             ->where('objectType')->eq('problem')
             ->andWhere('objectID')->eq($problemID)
             ->andWhere('`before`')->eq('assigned')
             ->fetch();
        if(empty($acceptUser)) return dao::$errors['acceptUser'] = $this->lang->problem->acceptStatusEmpty;*/

        $oldProblem = $this->getByID($problemID);
        $problem    = array();

        if(empty($_POST['acceptUser']))
        {
            return dao::$errors['acceptUser'] = $this->lang->problem->acceptUserEmpty;
        }
        else
        {
            $acceptDept =  $this->dao->select('dept')->from(TABLE_USER)->where('account')->eq($_POST['acceptUser'])->fetch();
            $this->dao->update(TABLE_CONSUMED) //编辑分配人
                 ->set('account')->eq($_POST['acceptUser'])
                 ->where('objectType')->eq('problem')
                 ->andWhere('objectID')->eq($problemID)
                 ->andWhere('`before`')->eq('assigned')
                 ->exec();

            $this->dao->update(TABLE_PROBLEM)
                 ->set('acceptUser')->eq($_POST['acceptUser'])
                 ->set('acceptDept')->eq($acceptDept->dept)
                 ->where('id')->eq($problemID)
                 ->exec();
        }

        return common::createChanges($oldProblem, $problem);
    }

    /**
     * Project: chengfangjinke
     * Method: workloadDelete
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:54
     * Desc: This is the code comment. This method is called workloadDelete.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $problemID
     * @param $consumedID
     * @return array
     */
    public function workloadDelete($problemID, $consumedID)
    {
        $actions = $this->dao->select('*')->from(TABLE_ACTION)
            ->where('objectType')->eq('problem')
            ->andWhere('objectID')->eq($problemID)
            ->andWhere('action')->eq('deal')
            ->orderBy('id_asc')
            ->fetchAll();

        $consumeds = $this->getConsumedList($problemID);

        /* Judge whether the current work record is the last one. */
        $total  = count($consumeds) - 1;
        $isLast = false;
        $previousID = 0;
        foreach($consumeds as $index => $cs)
        {
            if($cs->id == $consumedID)
            {
                $isLast = $index == $total ? true : false;
                $previousID = $consumeds[$total - 1]->id;
            }
        }

        if($isLast and $previousID)
        {
            $consumed = $this->getConsumedByID($previousID);
            $this->dao->update(TABLE_PROBLEM)->set('status')->eq($consumed->after)->where('id')->eq($problemID)->exec();
        }

        /* Get the corresponding relationship between work record and operation record. */
        $actionID = 0;
        array_splice($consumeds, 0, 1); // Remove the first work record.

        foreach($consumeds as $index => $cs)
        {
            if($cs->id == $consumedID) $actionID = $actions[$index]->id;
        }

        if($actionID) $this->dao->delete()->from(TABLE_ACTION)->where('id')->eq($actionID)->exec();

        /* 逻辑删除 */
        $this->dao->update(TABLE_CONSUMED)->set('deleted')->eq(1)->where('id')->eq($consumedID)->exec(); //逻辑删除
       /* 删除相关配合人员记录 */
        $this->dao->update(TABLE_CONSUMED)->set('deleted')->eq(1)->where('parentID')->eq($consumedID)->exec(); //删除相关配合人员记录

        return array();
    }

    /**
     * Project: chengfangjinke
     * Method: workloadEdit
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:54
     * Desc: This is the code comment. This method is called workloadEdit.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $problemID
     * @param $consumedID
     * @return array
     */
    public function workloadEdit($problemID, $consumedID)
    {

        //返回信息
        $res = array();
        //检查时间信息
        $consumedTime = $this->post->consumed;
       /* if($consumedTime != '0'){
         $checkRes = $this->loadModel('consumed')->checkConsumedInfo($consumedTime);
                if(!$checkRes){
                    return dao::$errors;
                }
        }*/
        //检查关配合人员工作量信息
        $checkRes = $this->loadModel('consumed')->checkPostDetails(true);
        if(!$checkRes){
            return dao::$errors;
        }

        $consumed = fixer::input('post')->remove('comment, relevantUser, workload, dealUser')->get();
        //2022-4-21 更新解决时间
        /* if($consumed->after == 'closed' || $consumed->after == 'delivery') {
            $this->dao->update(TABLE_PROBLEM)->set('solvedTime')->eq(date('Y-m-d H:i:s'))->where('id')->eq($problemID)->exec();
        } */
        /* Judge whether the current work record is the last one. */
        $isLast = $this->loadModel('consumed')->checkIsLastConsumed($consumedID, $problemID, 'problem');
        if($isLast){
            //最后一个节点时没有设置处理人
            $dealUser = $this->post->dealUser;
            if(!$dealUser){
                $errors['dealUser'] = sprintf($this->lang->problem->emptyObject, $this->lang->problem->dealUser);
                return dao::$errors = $errors;
            }
        }
        $problemClose = isset($consumed->closedDate) ? $consumed->closedDate : '';
        //获得相关配合人员工作量信息
        $consumed->details = $this->loadModel('consumed')->getPostDetails();
        unset($consumed->closedDate);
        $this->dao->update(TABLE_CONSUMED)->data($consumed)->autoCheck() //编辑工作量
            ->batchCheck($this->config->problem->workloadedit->requiredFields, 'notempty')
            ->where('id')->eq($consumedID)
            ->exec();

        $actions = $this->dao->select('*')->from(TABLE_ACTION)
            ->where('objectType')->eq('problem')
            ->andWhere('objectID')->eq($problemID)
            ->andWhere('action')->eq('deal')
            ->orderBy('id_asc')
            ->fetchAll();

        $consumeds = $this->getConsumedList($problemID);

        //最后一个工作量节点修改需求单的待处理状态和待处理人
        if($isLast) {
            $oldProblem = $this->getByID($problemID);
            //如果状态为待关闭和已关闭更新解决时间
            if(!$this->isSecondSolve($problemID, 'problem')){
                if($consumed->after == 'toclosed'){
                    $this->dao->update(TABLE_PROBLEM)
                        ->set('solvedTime')->eq(helper::now())
                        ->where('id')->eq($problemID)->exec();
                }else{
                    $this->getAllSecondSolveTime($problemID, 'problem');
                }
            }
            if(($oldProblem->status != $consumed->after) || ($oldProblem->dealUser != $dealUser)){
            $this->dao->update(TABLE_PROBLEM)->set('status')->eq($consumed->after)
                    ->set('dealUser')->eq($dealUser)
                    ->beginIF($consumed->after == 'closed' && empty($oldProblem->solvedTime))->set('closedDate')->eq($problemClose)->set('solvedTime')->eq($problemClose)->fi()
                    ->beginIF($consumed->after == 'closed' && !empty($oldProblem->solvedTime))->set('closedDate')->eq($problemClose)->fi()
                    ->where('id')->eq($problemID)->exec();
                $data = new stdClass();
                $data->status   = $consumed->after;
                $data->dealUser = $dealUser;
                if($consumed->after == 'closed') {
                    $data->closedDate = $problemClose;
                }
                $res = common::createChanges($oldProblem, $data);
            }
        }

        /* Get the corresponding relationship between work record and operation record. */
        $actionID = 0;
        array_splice($consumeds, 0, 1); // Remove the first work record.

        foreach($consumeds as $index => $cs)
        {
            if($cs->id == $consumedID) $actionID = $actions[$index]->id;
        }

        if($actionID)
        {
            $this->dao->update(TABLE_ACTION)->set('actor')->eq($consumed->account)->where('id')->eq($actionID)->exec();
        }

        /* 处理相关配合人员的记录（增删改） */
        $this->loadModel('consumed')->dealRelevantUser($consumedID);

        return $res;
    }

    /**
     * Project: chengfangjinke
     * Method: getConsumedByID
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:54
     * Desc: This is the code comment. This method is called getConsumedByID.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $consumedID
     * @return mixed
     */
    public function getConsumedByID($consumedID)
    {
        return $this->dao->select('*')->from(TABLE_CONSUMED)->where('id')->eq($consumedID)->fetch();
    }

    /**
     * Project: chengfangjinke
     * Method: getConsumedList
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:54
     * Desc: This is the code comment. This method is called getConsumedList.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $problemID
     * @return mixed
     */
    public function getConsumedList($problemID)
    {
        $cs = $this->dao->select('*')->from(TABLE_CONSUMED)->where('objectType')->eq('problem')
            ->andWhere('objectID')->eq($problemID)
            ->andWhere('parentID')->eq('0')
            ->andWhere('deleted')->eq(0)
            ->orderBy('id_asc')
            ->fetchAll();
        return $cs;
    }

    /**
     * Project: chengfangjinke
     * Method: getByID
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:54
     * Desc: This is the code comment. This method is called getByID.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $problemID
     * @return mixed
     */
    public function getByID($problemID)
    {
        $field = 't2.id delayId,t2.originalResolutionDate,t2.delayResolutionDate,t2.delayReason,t2.delayStatus,t2.delayVersion,t2.delayStage,t2.delayDealUser,t2.delayUser,t2.delayDate,t2.id as delayId,t3.id changeId,t3.changeCommunicate,t3.successVersion,t1.reviewStatus,t3.changeVersion,t3.changeResolutionDate,t3.changeStatus,t3.changeDealUser,t3.changeOriginalResolutionDate,t3.changeReason,t3.changeStage,t3.changeUser,t3.changeContent';
        $problem = $this->dao
            ->select("t1.*,{$field}")
            ->from(TABLE_PROBLEM)->alias('t1')
            ->leftJoin(TABLE_DELAY)->alias('t2')
            ->on("t1.id = t2.objectId and t2.objectType = 'problem' ")
            ->leftJoin(TABLE_PROBLEM_CHANGE)->alias('t3')
            ->on("t1.id = t3.objectId and t3.objectType = 'problem' and  t3.id = (select id from zt_problem_change where objectId= t1.id order by changeVersion desc limit 1 )")
            ->where('t1.id')->eq($problemID)
            ->fetch();
        $problem = $this->getIfOverDate($problem);
        $problem = $this->getSolvedTime($problem);
        $problem = $this->loadModel('file')->replaceImgURL($problem, 'desc,reason,solution,progress,plateMakAp
            ,plateMakInfo,ReviewOpinion,Tier1Feedback,ChangeSolvingTheIssue,EditorImpactscope,ReasonOfIssueRejecting,revisionRecord,delayReason,communicate,changeReason,changeCommunicate');
        $problem = $this->getConsumed($problem);
        $problem = $this->getFeedBackConsumed($problem);
        $problem = $this->getDelayConsumed($problem);
        $problem = $this->getChangeConsumed($problem);

        $problem->files = $this->loadModel('file')->getByObject('problem', $problem->id);
        //2022.5.12 tangfei 获取反馈单附件
        $problem->RelationFiles = $this->loadModel('file')->getByObject('problemFeedback', $problem->id);

        $problem->Tier1Feedback = strip_tags(str_replace("&nbsp;"," ",htmlspecialchars_decode($problem->Tier1Feedback)));
        $problem->solution      = strip_tags(str_replace("&nbsp;"," ",htmlspecialchars_decode($problem->solution)));
        $problem->reason        = strip_tags(str_replace("&nbsp;"," ",htmlspecialchars_decode($problem->reason)));
        $problem->EditorImpactscope = strip_tags(str_replace("&nbsp;"," ",htmlspecialchars_decode($problem->EditorImpactscope)));

        //开发中 测试中 已发布 已交付 上线成功 处理人置空显示 已关闭也不显示待处理人
        if(in_array($problem->status, ['feedbacked','build','released','delivery','onlinesuccess','closed'])) {
            $problem->dealUser = '';
        }

        $closeUser = $this->dao->select('objectID, account')
            ->from(TABLE_CONSUMED)
            ->where('objectType')->eq('problem')
            ->andWhere('objectID')->eq($problem->id)
            ->andWhere('`before`')->eq('confirmed')
            ->andWhere('deleted')->eq('0')
            ->orderBy('id_desc')
            ->fetch();
        $problem->closeUser = $closeUser->account ?? '';

         return $problem;
    }

    public function getByIdList($problemIdList, $isPairs = false)
    {
        if(empty($problemIdList)) return array();

        $problems = $this->dao->select("*")->from(TABLE_PROBLEM)->where('id')->in($problemIdList)->fetchAll();
        if($isPairs)
        {
            $pairs = array();
            foreach($problems as $problem)
            {
                $pairs[$problem->id] = $problem->code;
            }
            $problems = $pairs;
        }
        return $problems;
    }

    /**
     * Desc:获取流程关闭时的时间
     * Date: 2022/3/28
     * Time: 17:44
     *
     * @param $problemID
     *
     */
    public function getDate($problemID)
    {
        return $this->dao->select('lastDealDate')->from(TABLE_PROBLEM)->where('id')->eq($problemID)->fetch();;
    }


    /* 获取工时投入信息和制版次数。*/
    public function getConsumed($problem)
    {
        if(empty($problem)) return array();

        $cs = $this->dao->select('*')->from(TABLE_CONSUMED)->where('objectType')->eq('problem')
            ->andWhere('objectID')->eq($problem->id)
            ->andWhere('parentID')->eq('0')
            ->andWhere('deleted')->eq(0)
//            ->andWhere('extra')->ne('problemFeedBack')
            ->andWhere('extra')->notIn(['problemFeedBack', 'problemDelay', 'problemChange'])
            ->fetchAll();
        $problem->buildTimes = 0;
        foreach($cs as $c)
        {
            if($c->after === 'build') $problem->buildTimes++;
        }
        $problem->consumed = $cs;
        return $problem;
    }
    /* 获取反馈单流转情况。*/
    public function getFeedBackConsumed($problem)
    {
        if(empty($problem)) return array();

        $cs = $this->dao->select('*')->from(TABLE_CONSUMED)->where('objectType')->eq('problem')
            ->andWhere('objectID')->eq($problem->id)
            ->andWhere('parentID')->eq('0')
            ->andWhere('deleted')->eq(0)
            ->andWhere('extra')->eq('problemFeedBack')
            ->fetchAll();
        $problem->feeckBackConsumed = $cs;
        return $problem;
    }

    /**
     * 获取延时申请单流转情况
     * @param $problem
     * @return array
     */
    public function getDelayConsumed($problem)
    {
        if(empty($problem)) return array();

        $cs = $this->dao->select('*')->from(TABLE_CONSUMED)->where('objectType')->eq('problem')
            ->andWhere('objectID')->eq($problem->id)
            ->andWhere('parentID')->eq('0')
            ->andWhere('deleted')->eq(0)
            ->andWhere('extra')->eq('problemDelay')
            ->fetchAll();
        $problem->delayConsumed = $cs;
        return $problem;
    }
    /**
     * 获取变更申请单流转情况
     * @param $problem
     * @return array
     */
    public function getChangeConsumed($problem)
    {
        if(empty($problem)) return array();

        $cs = $this->dao->select('*')->from(TABLE_CONSUMED)->where('objectType')->eq('problem')
            ->andWhere('objectID')->eq($problem->id)
            ->andWhere('parentID')->eq('0')
            ->andWhere('deleted')->eq(0)
            ->andWhere('extra')->eq('problemChange')
            ->fetchAll();
        $problem->changeConsumed = $cs;
        return $problem;
    }

    /* 获取制版次数。*/
    public function getBuild($problemID)
    {
        $buildTotal = $this->dao->select('count(*) as total')->from(TABLE_CONSUMED)
            ->where('objectType')->eq('problem')
            ->andWhere('objectID')->eq($problemID)
            ->andWhere('after')->eq('build')
            ->fetch('total');
        return empty($buildTotal) ? 0 : $buildTotal;
    }

    /**
     * Project: chengfangjinke
     * Method: feedback
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:54
     * Desc: This is the code comment. This method is called feedback.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $problemID
     * @return array
     */
    public function feedback($problemID)
    {
        $oldProblem = $this->getByID($problemID);

        $data = fixer::input('post')->stripTags($this->config->problem->editor->feedback['id'], $this->config->allowedTags)->get();
        $data->status = 'feedbacked';
        $this->dao->update(TABLE_PROBLEM)->data($data)->where('id')->eq($problemID)->exec();

        return common::createChanges($oldProblem, $data);
    }

    /**
     * Project: chengfangjinke
     * Method: deal
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:54
     * Desc: This is the code comment. This method is called deal.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $problemID
     * @return array
     */
    public function deal($problemID)
    {
        if(!$this->post->consumed)
        {
            return dao::$errors['consumed'] = $this->lang->problem->consumedEmpty;
        }
        $reg = '/^(([1-9][0-9]*)|(([0]\.\d{1}|[1-9][0-9]*\.\d{1})))$/';
        if (!preg_match($reg, $this->post->consumed))
        {
            dao::$errors['consumed'] = $this->lang->problem->consumedError;
            return false;
        }
        if(!$this->post->dealUser)
        {
            return dao::$errors['dealUser'] = $this->lang->problem->nextUserEmpty;
        }

        $oldProblem = $this->getByID($problemID);

        $data = fixer::input('post')->stripTags($this->config->problem->editor->deal['id'], $this->config->allowedTags)
            ->remove('relevantUser,workload,user,consumed,mailto,uid')
            ->get();

        if($this->post->status == 'feedbacked')
        {
            $acceptUser = $this->loadModel('user')->getByAccount($this->post->user);
            $data->acceptDept = $acceptUser->dept;
            $data->acceptUser = $this->post->user;
        }

        /* 当状态为已分析，已解决，必填项所属项目和问题类型。*/
        if($this->post->status == 'feedbacked' or $this->post->status == 'solved')
        {
            /* 必填判断所属项目。*/
            if(empty($data->projectPlan)) return dao::$errors = array('projectPlan' => $this->lang->problem->projectPlanEmpty);

            if($data->fixType == 'second')
            {
                // 判断二线实现的解决方案必须为二线项目。
                $plan = $this->dao->select('*')->from(TABLE_PROJECTPLAN)->where('deleted')->eq(0)->andWhere('id')->eq($data->projectPlan)->fetch();
                if(empty($plan->secondLine)) return dao::$errors = array('' => $this->lang->problem->noSecondLinse);
            }

            /* 必填增加问题类型。*/
            $this->config->problem->deal->requiredFields .= ',fixType,type';
        }

        // 判断处理后的状态是否为【已关闭】，如果是则记录关闭人和关闭时间。
        $today = helper::today();
        if($this->post->status == 'closed')
        {
            $data->closedBy   = $this->post->user;
            $data->closedDate = $today;
        }

        // 判断[buildTimes制版次数]是否自增。
        if($this->post->status == 'build')
        {
            $data->buildTimes = $oldProblem->buildTimes + 1;
        }
        $data->lastDealDate = $today;
        $data = $this->loadModel('file')->processImgURL($data, $this->config->problem->editor->deal['id'], $this->post->uid);

        $this->dao->update(TABLE_PROBLEM)->data($data)->autoCheck()
             ->batchCheck($this->config->problem->deal->requiredFields, 'notempty')
             ->where('id')->eq($problemID)
             ->exec();
        $this->loadModel('consumed')->record('problem', $problemID, $this->post->consumed, $this->post->user, $oldProblem->status, $this->post->status, $this->post->mailto);

        $this->loadModel('file')->updateObjectID($this->post->uid, $problemID, 'problem');
        $this->file->saveUpload('problem', $problemID);

        /* 当状态为已分析，已解决，处理所属项目关联关系。*/
        if($this->post->status == 'feedbacked' or $this->post->status == 'solved')
        {
            //$this->loadModel('secondline');

            /* 删除旧的关联记录。*/
            //$this->secondline->saveRelationship($problemID, 'problemPool', '', 'projectPlan');

            /* 记录与所属项目的关联关系。*/
            //$this->secondline->saveRelationship($problemID, 'problemPool', $data->projectPlan, 'projectPlan');
        }

        return common::createChanges($oldProblem, $data);
    }

    /**
     * Project: chengfangjinke
     * Method: close
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:54
     * Desc: This is the code comment. This method is called close.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $problemID
     */
    public function close($problemID)
    {
        if(!$this->post->comment){
            return dao::$errors['comment'] = $this->lang->problem->commentEmpty;
        }
        $oldProblem = $this->getByID($problemID);
        $data = new stdClass();
        $data->status   = 'closed';
        $data->closedBy = $this->app->user->account;
        $data->closedDate = helper::today();
        if(empty($oldProblem->solvedTime)){
            $data->solvedTime = helper::now();
        }
        //如果问题单由待分配直接关闭问题单则解决计算周期和内部反馈开始时间取创建时间
        if($oldProblem->status == 'confirmed'){
            $actionInfo = $this->dao
                ->select('date')
                ->from(TABLE_ACTION)
                ->where('objectType')->eq('problem')
                ->andWhere('objectId')->eq($problemID)
                ->andWhere('action')->eq('created')
                ->fetch();
            $data->dealAssigned = $data->feedbackStartTimeInside = $actionInfo->date ?? '';
        }
        $this->dao->update(TABLE_PROBLEM)->data($data)->autoCheck()
            ->batchCheck($this->config->problem->close->requiredFields, 'notempty')
            ->where('id')->eq($problemID)->exec();
        $this->loadModel('consumed')->record('problem', $problemID, 0, $this->app->user->account, $oldProblem->status, $data->status, '');
        //更新需求和问题解决时间
        //$this->getAllSecondSolveTime($problemID,'problem');
    }

    /**
     * Project: chengfangjinke
     * Method: suspend
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:54
     * Desc: This is the code comment. This method is called suspend.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $problemID
     * @return array
     */
    public function suspend($problemID)
    {
        $oldProblem = $this->getByID($problemID);
        $problem = fixer::input('post')
            ->remove('comment')
            ->get();

        $this->dao->update(TABLE_PROBLEM)->data($problem)
            ->where('id')->eq($problemID)
            ->exec();
        $this->loadModel('consumed')->record('problem', $problemID, 0, $this->app->user->account, $oldProblem->status, $problem->status, '');
        return common::createChanges($oldProblem, $problem);
    }

    /**
     * Project: chengfangjinke
     * Method: start
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:54
     * Desc: This is the code comment. This method is called start.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $problemID
     * @return array
     */
    public function start($problemID)
    {
        $oldProblem = $this->getByID($problemID);
        $problem = fixer::input('post')
            ->remove('comment')
            ->get();

        $this->dao->update(TABLE_PROBLEM)->data($problem)
            ->where('id')->eq($problemID)
            ->exec();
        $this->loadModel('consumed')->record('problem', $problemID, 0, $this->app->user->account, $oldProblem->status, $problem->status, '');
        return common::createChanges($oldProblem, $problem);
    }

    /**
     * Project: chengfangjinke
     * Method: isClickable
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:54
     * Desc: This is the code comment. This method is called isClickable.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $problem
     * @param $action
     * @return bool
     */
    public static function isClickable($problem, $action)
    {
        global $app;
        $action = strtolower($action);
        if($problem->status == 'deleted'){
           return false;
        }
        if($action == 'edit')            return (($problem->status == 'confirmed' || $problem->status == 'returned') && !in_array($problem->createdBy,array('guestjx','guestcn'))) or $_SESSION['user']->admin;
        if($action == 'deal')            return (in_array($problem->status, self::$_dealStatus) == 1 and $app->user->account == $problem->dealUser) or $_SESSION['user']->admin;  //到开发中就不能人工处理了
        if($action == 'delete')          return ($problem->status == 'confirmed' and $app->user->account == $problem->createdBy) or $_SESSION['user']->admin;
        if($action == 'editspecial')     return (!in_array($problem->status, self::$_notStatus)) or $_SESSION['user']->admin;
        if($action == 'workloadedit')    return (!in_array($problem->status, self::$_notStatus)) or $_SESSION['user']->admin;
        if($action == 'workloaddelete')  return (!in_array($problem->status, self::$_notStatus)) or $_SESSION['user']->admin;
        if($action == 'editassignedto')  return (!in_array($problem->status, self::$_notStatus)) or $_SESSION['user']->admin;
        if($action == 'workloaddetails') return (!in_array($problem->status, self::$_notStatus)) or $_SESSION['user']->admin ;
        if($action == 'createfeedback')  return !in_array($problem->status, self::$_notStatus) and $problem->status != 'assigned'
            and ( $_SESSION['user']->account == $problem->acceptUser)
            and ($problem->ReviewStatus == 'tofeedback'
                or $problem->ReviewStatus == 'todeptapprove'
                or ($problem->ReviewStatus == 'sendback')
                or $problem->ReviewStatus == 'syncfail'
                or $problem->ReviewStatus == 'jxsyncfail'
                or $problem->ReviewStatus == 'firstpassed'
                or $problem->ReviewStatus == 'externalsendback'
                or ($problem->ReviewStatus == 'approvesuccess' and $problem->IfultimateSolution == '0' ))
            and ($problem->IssueId != null) or $_SESSION['user']->admin ;
        if($action == 'approvefeedback') return !in_array($problem->status, self::$_notStatus)
            and ( in_array($_SESSION['user']->account, $problem->approver))
            and ($problem->IssueId != null)
            and ($problem->ReviewStatus == 'todeptapprove' or $problem->ReviewStatus == 'deptapproved') or $_SESSION['user']->admin ;
        if($action == 'close'){
            $flag = 'closed' != $problem->status && '' == $problem->IssueId;
            if($flag){
                //需求 3850 内部问题单仅创建人有权限关闭
               // $closeUser = 'confirmed' == $problem->status ? $problem->dealUser : $problem->closeUser;
                $flag      = $app->user->account == $problem->createdBy;
            }
            return $flag or $_SESSION['user']->admin;
        }
        if($action == 'redeal'){
            $userList = array_merge([$problem->acceptUser], $app->lang->problem->redealUserList);
            return $problem->status == 'feedbacked' and in_array($_SESSION['user']->account, $userList);
        }
        return true;
    }

    /**
     * Project: chengfangjinke
     * Method: getPairs
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:54
     * Desc: This is the code comment. This method is called getPairs.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @return mixed
     */
    public function getPairs()
    {
        return $this->dao->select('id,code')->from(TABLE_PROBLEM)
            ->where('status')->ne('deleted')
            ->orderBy('id_desc')
            ->fetchPairs();
    }


    public function getPairsBycode($codeList = array())
    {
        return $this->dao->select('id,code')->from(TABLE_PROBLEM)
            ->where('status')->ne('deleted')
            ->beginIF(is_array($codeList) && count($codeList))->andWhere('code')->in($codeList)->fi()
            ->orderBy('id_desc')
            ->fetchPairs();
    }

    /**
     * 根据多个id获取信息
     * @param array $ids
     * @return stdClass
     */
    public function getPairsByIds($ids)
    {
        if(empty($ids)) return null;
        $info = $this->dao->select('id,code,`desc`')->from(TABLE_PROBLEM)
            ->where('status')->ne('deleted')
            ->andwhere('id')->in($ids)
            ->orderBy('id_desc')
            ->fetchall();
        $problems = new stdClass();
        foreach ($info as $item)
        {
            $id = $item->id;
            $problems->$id = ['code'=>$item->code, 'desc' =>$item->desc];
        }
        return  $problems;
    }

    /**
     * This is the code comment. This method is called getPairs.
     *
     * @param bool $status
     * @param string $exWhere
     * @return mixed
     */
    public function getPairsAbstract($status = false, $exWhere = '',$problemIds = [])
    {
        $data = [];
        $ret = $this->dao->select("id,concat(code,'（',IFNULL(abstract,''),'）') as code")->from(TABLE_PROBLEM)
            ->where('status')->ne('deleted')
//            ->beginIF(!$status)->where('status')->ne('deleted')->fi()
//            ->beginIF($status)->where('status')->in(['onlinesuccess','suspend','released','delivery','toclose'])->fi()
            ->beginIF($status)->andWhere('status')->ne('closed')->fi()
            ->beginIF($exWhere)->andWhere($exWhere)->fi()
            ->orderBy('id_desc')
            ->fetchPairs();
        $res = [];
        // 编辑页要正常回显被关联的已关闭的问题单
        if (!empty($problemIds)){
            $res = $this->dao->select("id,concat(code,'（',IFNULL(abstract,''),'）') as code")->from(TABLE_PROBLEM)
                ->where('status')->ne('deleted')
                ->andWhere('id')->in($problemIds)
                ->orderBy('id_desc')
                ->fetchPairs();
        }
        if($ret){
            $data = $ret;
        }
        if ($res){
            $data = $data + $res;
        }
       return $data;
    }

    /**
     * Send mail.
     *
     * @param  int    $problemID
     * @param  int    $actionID
     * @access public
     * @return void
     */
    public function sendmail($problemID, $actionID)
    {
        $this->loadModel('mail');
        $this->app->loadLang('problem');
        $problem = $this->getById($problemID);
        $users   = $this->loadModel('user')->getPairs('noletter');

        /* 获取后台通知中配置的邮件发信。*/
        $this->app->loadLang('custommail');
        $mailConf   = isset($this->config->global->setProblemMail) ? $this->config->global->setProblemMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf   = json_decode($mailConf);
        $browseType = 'problem';

        /* 处理邮件发信的标题和日期。*/
        $bestDeal = $this->dao->select('*')->from(TABLE_CONSUMED)->where('objectType')->eq('problem')
            ->andWhere('objectID')->eq($problemID)
            ->orderBy('id_desc')
            ->fetch();
        $bestDate  = empty($bestDeal) ? '' : substr($bestDeal->createdDate, 0, 10);
        $mailTitle = vsprintf($mailConf->mailTitle, $mailConf->variables);

        /* Get action info. */
        $action          = $this->loadModel('action')->getById($actionID);
        $history         = $this->action->getHistory($actionID);
        $action->history = isset($history[$actionID]) ? $history[$actionID] : array();

        //重发机制-不发邮件
        if($action->action == 'syncfail' && $problem->syncFailTimes > 1){
            return ;
        }
        //延期
        if($action->action == 'problemdelay' || $action->action == 'reviewdelay'){
            $problem->delay = "true";
            $delayInfo = $this->dao
                ->select('originalResolutionDate,delayResolutionDate,delayReason,delayStatus,delayVersion,delayStage,delayDealUser,delayUser,delayDate')
                ->from(TABLE_DELAY)
                ->where('objectType')->eq('problem')
                ->andWhere('objectId')->eq($problemID)
                ->fetch();
            foreach ($delayInfo as $key => $value){
                $problem->$key = $value;
            }
        }
       //变更
        if($action->action == 'problemchange' || $action->action == 'problemreviewchange'){
            $problem->change = "true";
            $changeInfo = $this->dao
                ->select('changeOriginalResolutionDate,changeResolutionDate,changeReason,changeStatus,changeVersion,changeStage,changeDealUser,changeUser,changeDate,changeContent,changeCommunicate')
                ->from(TABLE_PROBLEM_CHANGE)
                ->where('objectType')->eq('problem')
                ->andWhere('objectId')->eq($problemID)
                ->orderBy('id_desc')
                ->limit(1)
                ->fetch();
            foreach ($changeInfo as $key => $value){
                $problem->$key = $value;
            }
        }

        /* Get mail content. */
        $oldcwd     = getcwd();
        $modulePath = $this->app->getModulePath($appName = '', 'problem');
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

        chdir($oldcwd);
        if($action->action == 'review' || $action->action == 'createfeedback')
        {
            $sendUsers = $this->getToAndCcListFeedBack($problem);
        }else if($action->action == 'syncstatus' || $action->action == 'syncfail') {
            $sendUsers =  array($problem->acceptUser, array());
        }else{
            $sendUsers = $this->getToAndCcList($problem);
        }
        if(!$sendUsers) return;
        list($toList, $ccList) = $sendUsers;

        /* 处理邮件标题。*/
        //$subject = $this->getSubject($problem);
        $subject = $mailTitle;

        if($problem->status == 'closed') {
            $subject = sprintf($this->lang->problem->ccMailTitle, $problem->code);
            $toList = $problem->createdBy;
            //抄送人
            $consumed = $this->loadModel('consumed')->getConsumed('problem',$problemID);
            $cclist = array();
            foreach ($consumed as $key => $item) {
                if($item->after == 'confirmed' ||$item->after == 'assigned' || $item->after == 'feedbacked'){
                    $cclist[$key] = $item->account;
                }
            }
            $problemDeal = $this->getByIdList($problemID);
            $ccList = implode(',',array_filter(array_unique($cclist))).','.$problemDeal[0]->dealUser;
           
        }
        //延期申请和审批发送邮件
        if($action->action == 'problemdelay' || $action->action == 'reviewdelay'){
            if($problem->delayStatus == 'success' || $problem->delayStatus == 'fail'){
                $toList = $problem->delayUser;
                if($problem->delayStatus == 'success'){
                    $user = $this->loadModel('user')->getUserInfo($problem->delayUser);
                    $myDept = $this->loadModel('dept')->getByID($user->dept);
                    $toList = $toList.','.trim($myDept->executive, ',');

                    $ccList = implode(',', array_filter(array_keys($this->lang->problem->delayCCUserList)));
                }
                $subject = $this->lang->problem->delayMaile;
            }else{
                $toList = $problem->delayDealUser;
            }
        }
        //变更
        if($action->action == 'problemchange' || $action->action == 'problemreviewchange'){
            if($problem->changeStatus == 'success' || $problem->changeStatus == 'fail'){
                $toList = $problem->changeUser;
                if($problem->changeStatus == 'success'){
                    $user = $this->loadModel('user')->getUserInfo($problem->changeUser);
                    $myDept = $this->loadModel('dept')->getByID($user->dept);
                    $toList = $toList.','.trim($myDept->executive, ',');

                    //$ccList = implode(',', array_filter(array_keys($this->lang->problem->delayCCUserList)));
                }

            }else{
                $toList = $problem->changeDealUser;
            }
            $subject = $problem->changeStatus == 'success' ? $this->lang->problem->delayMaile :$mailTitle;
        }
        /* Send emails. */
        $status = array('confirmed','assigned', 'toclose','closed','returned'); //20220930 待分配和待分析 或待开发且不是问题发邮件,其他都不发 //20240430增加退回状态发邮件
        if(
            in_array($problem->status,$status) ||
            ($problem->status == 'feedbacked' && $problem->type == 'noproblem') ||
            ($action->action == 'problemdelay' ||$action->action == 'reviewdelay' )||($action->action == 'problemchange' ||$action->action == 'problemreviewchange' ) || $problem->ReviewStatus == 'deptapproved'
        ){
            $this->mail->send($toList, $subject, $mailContent, $ccList);
        }
        if($this->mail->isError()) error_log(join("\n", $this->mail->getError()));
    }

    /**
     * Project: chengfangjinke
     * Method: getToAndCcList
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:54
     * Desc: This is the code comment. This method is called getToAndCcList.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $object
     * @return array
     */
    public function getToAndCcList($object)
    {
        /* Set toList and ccList. */
        $toList = $object->dealUser;

        $details = $this->loadModel('consumed')->getObjectByID($object->id, 'problem', $object->status);
        $ccList  = isset($details->mailto) ? trim($details->mailto, ',') : '';

        return array($toList, $ccList);
    }

    /**
     * Get mail subject.
     *
     * @param  object
     * @access public
     * @return string
     */
    public function getSubject($object)
    {
        return $this->lang->problem->common  . '#' . $object->id . '-' . $object->code;
    }

    /**
     * Project: chengfangjinke
     * Method: setListValue
     * Desc: This is the code comment. This method is called setListValue.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * 2022.4.20    tangfei
     */
    public function setListValue()
    {
        $sourceList      = $this->lang->problem->sourceList;
        $severityList    = $this->lang->problem->severityList;
        $priList         = $this->lang->problem->priList;

        $this->post->set('source2List',        join(',', $sourceList));
        $this->post->set('severityList',      join(',', $severityList));
        $this->post->set('priList',               join(',', $priList));
        $this->post->set('listStyle',      $this->config->problem->export->listFields);

        $this->post->set('extraNum', 0);
    }

    /**
     * Project: chengfangjinke
     * Method: createFromImport
     * User: tangfei
     * Date: 2022/4/19
     * Product: PhpStorm
     */
    public function createFromImport()
    {
        $date   = date('Y-m-d');
        $number = $this->dao->select('count(id) c')->from(TABLE_PROBLEM)->where('createdDate')->eq($date)->fetch('c');

        $problems = array();
        $line = 1;
        foreach($_POST['severity'] as $key => $name)
        {
            $problem = new stdClass();
            $problem->code          = 'CFIT-Q-' . date('Ymd-') . sprintf('%02d', ++$number);
            $problem->abstract      = $_POST['abstract'][$key];
            $problem->source        = $_POST['source'][$key];
            $problem->severity      = $_POST['severity'][$key];
            $problem->app           = implode(',',$_POST['app'][$key]);
            $problem->pri           = $_POST['pri'][$key];
            $problem->occurDate     = $_POST['occurDate'][$key];
            $problem->dealUser      = $_POST['dealUser'][$key];
            $problem->consumed      = $_POST['consumed'][$key];
            $problem->desc          = $_POST['desc'][$key];
            $problem->createdBy   = $this->app->user->account;
            $problem->createdDate = date('Y-m-d');
            $problem->editedBy   = $this->app->user->account;
            $problem->editedDate = date('Y-m-d');

            if(isset($this->config->problem->create->requiredFields))
            {
                $requiredFields = explode(',', $this->config->problem->create->requiredFields);
                foreach($requiredFields as $requiredField)
                {
                    $requiredField = trim($requiredField);
                    if(empty($problem->$requiredField)) dao::$errors[] = sprintf($this->lang->problem->noRequire, $line, $this->lang->problem->$requiredField);
                }
            }

            $problems[] = $problem;
            $line++;
        }
        if(dao::isError()) die(js::error(dao::getError()));

        foreach($problems as $key => $problem)
        {
            $this->dao->insert(TABLE_PROBLEM)->data($problem)->batchCheck('occurDate', 'date')->batchCheck('consumed', 'float')->exec();
            if(dao::isError()) die(js::error(dao::getError()));

            $problemID = $this->dao->lastInsertID();
            $this->loadModel('action')->create('problem', $problemID, 'created', '');

            if(!dao::isError())
            {
                $this->loadModel('consumed')->record('problem', $problemID, $_POST['consumed'][$key], $this->app->user->account, '', 'confirmed', array());
            }
        }
    }

    /**
     * 接口创建问题
     * @param $jx
     * @return mixed
     */
    public function createByApi($jx = 0)
    {
        $userAccount = $jx ? $this->lang->problem->apiDealUserList['jxDealAccount'] : $this->lang->problem->apiDealUserList['userAccount']; // 金信默认'zhangyun',清总默认 'litianzi';
        $data = fixer::input('post')
//            ->add('source', '4')  //4 = 清算总中心
            ->add('dealUser', $userAccount)
            ->add('systemverify', 0)
            ->add('fixType', '')
            ->get();
        $data->source       = $jx ? '31' : '4';
        $data->createdBy    = $jx ? 'guestjx' : 'guestcn';
        $data->createdDate  = $data->feedbackStartTimeOutside = date('Y-m-d H:i:s');//创建时间、外部反馈开始时间
        $data->createdDept  = 1; //默认产品创新部
        if($jx) {
            $data->pri = $data->pri == 1 ? $data->pri : 0; //默认非紧急
            $this->lang->problem->TimeOfReport = $this->lang->problem->jxTimeOfReport;// 清算报告时间报错对应
        }else {
            $data->pri = $data->pri ?? 0; //默认非紧急
        }
        $data->lastDealDate = helper::today();
        $data->app = $jx ? $data->app : $this->getAppIdByAppCode($data->app);
        $data->feedbackExpireTime = $jx ? $this->getDateAfter($this->lang->problem->expireDaysList['jxExpireDays']) : $this->getDateAfter($this->lang->problem->expireDaysList['days'],true);
        $data->status       = 'confirmed';
        $data->ReviewStatus = 'tofeedback';

        $applicationInfo = $this->loadModel('application')->getapplicationInfo();
        if(!empty($data->app)){
            $as = [];
            foreach(explode(',', $data->app) as $apptype)
            {
                if(!$apptype) continue;
                $as[] = zget($applicationInfo, $apptype,"",$applicationInfo[$apptype]->isPayment);
            }
            $applicationtype = implode(',', $as);
            if(!empty($applicationtype))$applicationtype=",".$applicationtype;
            $data->isPayment=$applicationtype;
        }
        if($jx){
            $data->relationFiles = json_encode($data->relationFiles);
        }else {
            $data->relationFiles = html_entity_decode($data->relationFiles);
        }

        $this->dao->insert(TABLE_PROBLEM)->data($data)->autoCheck()->batchCheck($this->config->problem->create->requiredFields, 'notempty')->exec();
        $problemID = $this->dao->lastInsertId();
        //保存附件
        if($data->relationFiles){
            $relationFiles = json_decode($data->relationFiles, 1);
            foreach ($relationFiles as $file){
                $this->saveFile($file['url'], $problemID, $file['fileName']);// 下载并记录测试报告附件
            }
        }
        if(!dao::isError())
        {
            $date   = date('Y-m-d');
            //$number = $this->dao->select('count(id) c')->from(TABLE_PROBLEM)->where('createdDate')->eq($date)->fetch('c');
            $firstId= $this->dao->select('id')->from(TABLE_PROBLEM)->where('createdDate')->eq($date)->orderBy('id_asc')->fetch();
            $number = $problemID - $firstId->id + 1;
            $code   = 'CFIT-Q-' . date('Ymd-') . sprintf('%02d', $number);
            $this->dao->update(TABLE_PROBLEM)->set('code')->eq($code)->where('id')->eq($problemID)->exec();
            $this->loadModel('consumed')->record('problem', $problemID, 0, 	$data->createdBy, '', 'confirmed', array());
            $this->loadModel('consumed')->record('problem', $problemID, 0,  $data->createdBy, '', 'tofeedback',  array(),"problemFeedBack");
            //外部反馈单在内部中的状态也需要接口同步
            if(!$jx){
                $statusName = zget($this->lang->problem->consumedstatusList,'tofeedback','');
                $this->syncFeedBackStatus($problemID,$statusName);
            }
        }

        return $problemID;
    }

    /**
     * 接口更新问题
     * @param $problemID
     * * @param $jx
     * @return array
     */
    public function updateByApi($problemID,$jx = 0)
    {
        $userAccount = $jx ? $this->lang->problem->apiDealUserList['jxDealAccount'] : $this->lang->problem->apiDealUserList['userAccount']; // 金信默认'zhangyun',清总默认 'litianzi';
        $oldProblem = $this->getByID($problemID);
        $problem = fixer::input('post')
            ->add('dealUser', $userAccount)
            ->add('systemverify', 0)
            ->add('fixType', '')
            ->get();
        $problem->source          = $jx ? '31' : '4';
        $problem->createdBy    = $jx ? 'guestjx' : 'guestcn';
        //$problem->createdDate  = helper::today();
        $problem->createdDept  = 1; //默认产品创新部
        $problem->lastDealDate = helper::today();
        if($oldProblem->isChangeFeedbackTime == 0){
            $problem->feedbackStartTimeOutside = date('Y-m-d H:i:s');//外部反馈开始时间
            $problem->feedbackEndTimeOutside   = '';//外部反馈开始时间
        }
        if($jx){
            $problem->pri      = $problem->pri == 1 ? $problem->pri : 0; //默认非紧急

            // 清算报告时间报错对应
            $this->lang->problem->TimeOfReport = $this->lang->problem->jxTimeOfReport;

        }else {
            $problem->pri      = $problem->pri ?? 0; //默认非紧急
        }
        $problem->app = $jx ? $problem->app : $this->getAppIdByAppCode($problem->app);

        $problem->editedBy          = $userAccount;
        $problem->status            = 'confirmed';
        $problem->pri               = $problem->pri ?? 0;
        $problem->editedDate        = date('Y-m-d');
        $problem->reviewStage       = 1;
        $problem->version           = intval($oldProblem->version) + 1;
        $problem->acceptDept        = 0;
        $problem->acceptUser        = '';
        $problem->feedbackToHandle  = '';
        $problem->ReviewStatus      = 'tofeedback';
        if(empty($oldProblem->feedbackExpireTime) || strpos($oldProblem->feedbackExpireTime,'0000-00-00') !== false){
              $problem->feedbackExpireTime = $jx ? $this->getDateAfter($this->lang->problem->expireDaysList['jxExpireDays']) : $this->getDateAfter($this->lang->problem->expireDaysList['days'],true);
        }
        if($problem->relationFiles){
            if($jx){
                $problem->relationFiles = json_encode($problem->relationFiles);
            }else {
                $problem->relationFiles = html_entity_decode($problem->relationFiles);
            }
            $relationFiles = json_decode($problem->relationFiles, 1);

            foreach ($relationFiles as $file){
                $this->saveFile($file['url'], $problemID, $file['fileName']);// 下载并记录测试报告附件
            }
        }
        $applicationInfo = $this->loadModel('application')->getapplicationInfo();
        if(!empty($problem->app)){
            $as = [];
            foreach(explode(',', $problem->app) as $apptype)
            {
                if(!$apptype) continue;
                $as[] = zget($applicationInfo, $apptype,"",$applicationInfo[$apptype]->isPayment);
            }
            $applicationtype = implode(',', $as);
            if(!empty($applicationtype))$applicationtype=",".$applicationtype;
            $problem->isPayment=$applicationtype;
        }
        $this->dao->update(TABLE_PROBLEM)->data($problem)->autoCheck()
            ->batchCheck($this->config->problem->edit->requiredFields, 'notempty')
            ->where('id')->eq($problemID)
            ->exec();

        $this->loadModel('consumed')->record('problem', $problemID, 0, 	$problem->createdBy, $oldProblem->status, 'confirmed', array());


        return common::createChanges($oldProblem, $problem);
    }

    /**
     * 接口查询外部问题单号
     */
    public function getProblemIdByIssueId($issueId)
    {
        return $this->dao->select('id,status')->from(TABLE_PROBLEM)->where('IssueId')->eq($issueId)->fetch();
    }

    /**
     * 根据拼音首字母取系统id
     * @param $code
     * @return mixed
     */
    public function getAppIdByAppCode($codes)
    {
        $apps = $this->dao->select('id')->from(TABLE_APPLICATION)->where('code')->in($codes)->fetchAll('id');
        if(empty($apps)) return '';
        return implode(',', array_keys($apps));
    }


    /**
     * 获取20个工作日后的日期（排除周6，周日）
     * @param int $num
     * @return false|mixed
     */
    public function getDateAfter($after = 3,$flag = false)
    {
        if(!is_numeric($after)) { $after = 3; }
        /*$now =  time() ;
        $days = array();
        $i = 1;
        while(count($days) < $after)
        {
            $timer = $now+3600*24*$i;
            $num= date("N",$timer)-2; //周一开始
            if($num>=-1 and $num<=3)
            {
                $days[]=date("Y-m-d H:i:s",$now+3600*24*$i);
            }
            $i++;
        }
        return end($days);*/
        $now = helper::today();
        $hms = date('H:i:s',strtotime(helper::now()));
        $enday = helper::getTrueWorkDay($now,$after,$flag)." ".$hms;
        return $enday;

    }


    /**
     * 推送问题反馈单
     * 20220829 添加清总问题反馈失败重试
     */
    public function pushFeedback($problemID, $problem = null)
    {
        $consumedUser = empty($this->app->user->account) ? 'guestjk' : $this->app->user->account;
        /*
        此时反馈单待审批人为空，反馈单审批状态为待外部审批
        */
        //解决方式为【非应用问题】时，清总同步时传【其他】
        $this->lang->problem->solutionFeedbackList[5] = '其他';

        /* 获取问题单。*/
        if($problem == null){
            $problem = $this->getByID($problemID);
        }

        $pushEnable = $this->config->global->pushProblemFeedbackEnable;
        //判断请求配置是否可用
        if ($pushEnable == 'enable') {
            $url = $this->config->global->pushProblemFeedbackUrl;
            $pushAppId = $this->config->global->pushProblemFeedbackAppId;
            $pushAppSecret = $this->config->global->pushProblemFeedbackAppSecret;
            $pushUsername = $this->config->global->pushProblemFeedbackUsername;
            $fileIP       = $this->config->global->pushProblemFileIP;
            $headers = array();
            $headers[] = 'App-Id: ' . $pushAppId;
            $headers[] = 'App-Secret: ' . $pushAppSecret;

            $deptList = $this->loadModel('dept')->getOptionMenu();
            $users = $this->loadmodel('user')->getPairs('noletter');

            $pushData = array();
            $pushData['IssueId']                = $problem->IssueId;
            $pushData['HandlerOfIssue']         = zget($users, $problem->acceptUser, '');
            $pushData['Tier1Feedback']          = strip_tags(str_replace("&nbsp;"," ",htmlspecialchars_decode($problem->Tier1Feedback)));
            //解决方式

            $pushData['Solution']               = zget($this->lang->problem->solutionFeedbackList, $problem->SolutionFeedback, $problem->SolutionFeedback);;
            $pushData['UltimateSolution']       = strip_tags(str_replace("&nbsp;"," ",htmlspecialchars_decode($problem->solution)));
            $pushData['Cause']                  = strip_tags(str_replace("&nbsp;"," ",htmlspecialchars_decode($problem->reason)));
            $pushData['TeleOfIssueHandler']     = $problem->TeleOfIssueHandler;
            $pushData['IfUltimateSolution']     = strval($problem->IfultimateSolution) ;
            //只有变更时才传 ChangeSolvingTheIssue
            $pushData['ChangeSolvingTheIssue']  = $problem->SolutionFeedback == 1 ? strip_tags(str_replace("&nbsp;"," ",htmlspecialchars_decode($problem->ChangeSolvingTheIssue))) : "";
            $pushData['PlannedTimeOfChange']    = $problem->PlannedTimeOfChange;
            //问题分级
            $pushData['Problemgrade']     = zget($this->lang->problem->problemGradeList,$problem->problemGrade,'');
            //是否基准验证
            $pushData['Ifbenchmarkverify'] = zget($this->lang->problem->standardVerifyList,$problem->standardVerify,'') ;
            //附件
            $RelationFiles  =   array();
            $RelationFiles = $this->loadModel('common')->sendFileBySftp($problem->RelationFiles,'problem',$problem->code);
            //附件传输失败
            if (dao::isError()) {
                $updateComment = dao::getError();
                $node = $this->dao->select('*')->from(TABLE_REVIEWNODE)
                    ->where('objectType')->eq('problem')
                    ->andWhere('objectID')->eq($problem->id)
                    ->andWhere('version')->eq($problem->version)
                    ->andWhere('stage')->eq('3')
                    ->orderBy('stage,id')
                    ->fetch();
                $this->dao->update(TABLE_PROBLEM)
                    ->set('ReviewStatus')->eq('syncfail')
                    ->set('syncFailTimes')->eq(intval($problem->syncFailTimes) + 1)->fi()
                    ->set('feedbackToHandle')->eq($problem->acceptUser)
                    ->where('id')->eq($problem->id)->exec();
                $updateStatus = 'syncfail';
                $updateComment = implode(',',$updateComment);
                if('syncfail' != $problem->ReviewStatus){
                    $this->loadModel('consumed')->record('problem', $problem->id, 0, $consumedUser, $problem->ReviewStatus, 'syncfail',  array(),"problemFeedBack");
                }
                $this->dao->update(TABLE_REVIEWER)
                    ->set('status')->eq($updateStatus)
                    ->set('comment')->eq($updateComment)
                    ->set('reviewTime')->eq(helper::now())
                    ->where('node')->eq($node->id)
                    ->andWhere('reviewer')->eq('guestjk') //当前审核人
                    ->exec();

                $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq($updateStatus)
                    ->where('id')->eq($node->id)
                    ->exec();
                $this->loadModel('action')->create('problem', $problem->id, $updateStatus, $updateComment,'','guestjk');
                return false;
            }
            $pushData['RelationFiles']             = $RelationFiles;
            $pushData['DepIdOfIssueHandler']       = zget($deptList, $problem->acceptDept, '');
            $pushData['PlannedDateOfChangeReport'] = $problem->PlannedDateOfChangeReport;
            $pushData['PlannedDateOfChange']       = $problem->PlannedDateOfChange;
            $pushData['CorresProduct']             = $problem->CorresProduct;//所属产品
            $pushData['EditorImpactscope']         = strip_tags(str_replace("&nbsp;"," ",htmlspecialchars_decode($problem->EditorImpactscope)));


            $object = 'problem';
            $objectType = 'feedback';
            $response = '';
            $status = 'fail';
            $extra = '';

            $result = $this->loadModel('requestlog')->http($url, $pushData, 'POST', 'json', array(), $headers);
            $node = $this->dao->select('*')->from(TABLE_REVIEWNODE)
                ->where('objectType')->eq('problem')
                ->andWhere('objectID')->eq($problem->id)
                ->andWhere('version')->eq($problem->version)
                ->andWhere('stage')->eq('3')
                ->orderBy('stage,id')
                ->fetch();
            $updateStatus = '';
            $updateComment = '';
            if(!empty($result))
            {
                $resultData = json_decode($result);
                if(isset($resultData->success) && $resultData->success == '1')
                {
                    $now = date('Y-m-d H:i:s');
                    $isOverTime = 0;
                    if(strtotime($problem->feedbackExpireTime) < strtotime($now)){
                        $isOverTime = 1;
                    }
                    $isInnovationPassTime = $this->getInnovationPassTime($problem);
                    $status = 'success';
                    $this->dao->update(TABLE_PROBLEM)
                        ->set('ReviewStatus')->eq('secondlineapproved')
                        ->set('feedbackToHandle')->eq('guestcn')
                        ->beginIF($problem->firstPushDateFlag == 1)->set('firstPushDate')->eq(date('Y-m-d H:i:s'))
                        ->set('firstPushDateFlag')->eq(0)->fi()
                        ->set('firstPush')->eq(0)
                        ->set('ifOverDate')->eq($isOverTime)
                        ->beginIF($isInnovationPassTime == false && $problem->isChangeFeedbackTime == 0)
                        ->set('innovationPassTime')->eq(helper::now())->fi()
                        ->where('id')->eq($problem->id)->exec();
                    $updateStatus = 'syncsuccess';
                    $updateComment = $resultData->message;
                    $this->loadModel('consumed')->record('problem', $problem->id, 0,  $consumedUser, $problem->ReviewStatus, 'syncsuccess',  array(),"problemFeedBack");
                    $this->loadModel('consumed')->record('problem', $problem->id, 0,  'guestjk', 'syncsuccess', 'secondlineapproved',  array(),"problemFeedBack");
                } else {
                    $this->dao->update(TABLE_PROBLEM)
                        ->set('ReviewStatus')->eq('syncfail')
                        ->set('syncFailTimes')->eq(intval($problem->syncFailTimes) + 1)->fi()
                        ->set('feedbackToHandle')->eq($problem->acceptUser)
                        ->where('id')->eq($problem->id)->exec();
                    $updateStatus = 'syncfail';
                    $updateComment = $resultData->message;
                    if('syncfail' != $problem->ReviewStatus){
                        $this->loadModel('consumed')->record('problem', $problem->id, 0, $consumedUser, $problem->ReviewStatus, 'syncfail',  array(),"problemFeedBack");
                    }
                }

                $response = $result;
            } else {
                $this->dao->update(TABLE_PROBLEM)
                    ->set('ReviewStatus')->eq('syncfail')
                    ->set('syncFailTimes')->eq(intval($problem->syncFailTimes) + 1)->fi()
                    ->set('feedbackToHandle')->eq($problem->acceptUser)
                    ->where('id')->eq($problem->id)->exec();
                $updateStatus = 'syncfail';
                $updateComment = '网络不通';
                if('syncfail' != $problem->ReviewStatus){
                    $this->loadModel('consumed')->record('problem', $problem->id, 0, $consumedUser, $problem->ReviewStatus, 'syncfail',  array(),"problemFeedBack");
                }
            }
            $this->dao->update(TABLE_REVIEWER)
                ->set('status')->eq($updateStatus)
                ->set('comment')->eq($updateComment)
                ->set('reviewTime')->eq(helper::now())
                ->where('node')->eq($node->id)
                ->andWhere('reviewer')->eq('guestjk') //当前审核人
                ->exec();

            $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq($updateStatus)
                ->where('id')->eq($node->id)
                ->exec();

            $this->loadModel('action')->create('problem', $problem->id, $updateStatus, $updateComment,'','guestjk');
            $this->requestlog->saveRequestLog($url, $object, $objectType, 'POST', $pushData, $response, $status, $extra);
        }

        return $updateStatus;
    }

    /**
     * Project: chengfangjinke
     * Method: createfeedback
     * User: shixuyang
     * Date: 2022/5/10
     * Desc: 创建反馈单
     * @param $problemID
     * @return array
     */
    public function createfeedback($problemID)
    {
        $oldProblem = $this->getByID($problemID);
        $problem = fixer::input('post')
            ->join('feedbackToHandle', ',')
            ->remove('files,PlannedTimeOfChangeDisabled,comment,consumed')
            ->get();

        $comment = $_POST['comment'] ?? 0;
        if(isset($problem->ifreturn) && $problem->ifreturn == '0' && !$this->checkTimeFormat($problem->PlannedTimeOfChange)){
            dao::$errors['PlannedTimeOfChange'] = $this->lang->problem->PlannedTimeOfChangeSizeError;
            return false;
        }
        if(!$this->checkDateFormat($problem->PlannedDateOfChangeReport) && $oldProblem->createdBy != 'guestjx'){
            dao::$errors['PlannedDateOfChangeReport'] = $this->lang->problem->dateError;
            return false;
        }
        if(!$this->checkDateFormat($problem->PlannedDateOfChange) && $oldProblem->createdBy != 'guestjx'){
            dao::$errors['PlannedDateOfChange'] = $this->lang->problem->dateError;
            return false;
        }
        if(($oldProblem->ReviewStatus == 'firstpassed' || $oldProblem->lastReviewStatus == 'firstpassed') && $problem->IfultimateSolution == '0' && $oldProblem->createdBy != 'guestjx'){
            dao::$errors['IfultimateSolution'] = $this->lang->problem->ifultimateSolutionError;
            return false;
        }
        //按照外部（清总）要求：如果是最终反馈审批未通过，再次修改反馈时，只能是最终解决方案，不能选择否。增加此限制
        if(($oldProblem->ReviewStatus == 'externalsendback' || $oldProblem->lastReviewStatus == 'externalsendback') && $oldProblem->IfultimateSolution == '1' && $problem->IfultimateSolution == '0' && $oldProblem->createdBy != 'guestjx'){
            dao::$errors['IfultimateSolution'] = $this->lang->problem->ifultimateSolutionBackError;
            return false;
        }
        if(
            $oldProblem->ReviewStatus == 'firstpassed'
            && $problem->IfultimateSolution == 1
            && !empty($oldProblem->PlannedTimeOfChange)
            && strpos($oldProblem->PlannedTimeOfChange, '0000') === false
            && $problem->PlannedTimeOfChange > $oldProblem->PlannedTimeOfChange
        ){
            dao::$errors[] = $this->lang->problem->delayUnderPlannedTimeOfChangeError;
            return false;
        }
        //问题分级
        if(!$problem->problemGrade  && $oldProblem->createdBy != 'guestjx'){
            dao::$errors['problemGrade'] = $this->lang->problem->problemGradeEmpty;
            return false;
        }
        //最终方案 是时 是否基准验证必填
        if($problem->IfultimateSolution  && $problem->IfultimateSolution == "1" && $oldProblem->createdBy != 'guestjx'){
            if(!$problem->standardVerify){
                dao::$errors['standardVerify'] = $this->lang->problem->standardVerifyEmpty;
                return false;
            }
        }

        if(empty($problem->feedbackToHandle)){
            $errors['feedbackToHandle'] = sprintf($this->lang->problem->emptyObject, $this->lang->problem->feedbackToHandle);
            return dao::$errors = $errors;
        }

        if($problem->ifReturn == '0'){
            $problem->ReasonOfIssueRejecting = null;
            if($problem->SolutionFeedback != '1'){
                $problem->ChangeSolvingTheIssue = null;
            }

        }else if($problem->ifReturn == '1'){
            $problem->Tier1Feedback = null;
            $problem->SolutionFeedback = null;
            $problem->PlannedTimeOfChange = null;
            $problem->CorresProduct = null;
            $problem->PlannedDateOfChangeReport = null;
            $problem->PlannedDateOfChange = null;
            $problem->ChangeSolvingTheIssue = null;
            $problem->EditorImpactscope = null;
            $problem->solution = null;
            $problem->reason = null;
            $problem->IfultimateSolution = '1';
        }

        $problem->acceptUser   = $this->app->user->account;
        $problem->acceptDept   = $this->app->user->dept;
        //反馈单状态待审批
        $problem->ReviewStatus = "todeptapprove";
        //保存重要状态-初次通过和外部退回
        if($oldProblem->ReviewStatus == 'firstpassed' || $oldProblem->ReviewStatus == 'externalsendback'){
            $problem->lastReviewStatus = $oldProblem->ReviewStatus;
        }

        //2022.5.17 反馈单审核步骤id,反馈单版本版本
        $problem->reviewStage = '1';
        $problem->version     =  $oldProblem->version+1;
        $problem->ReviewStatus     = "todeptapprove"; //用户点击反馈按键 重新走流程 不再重试发送清总失败反馈
        $problem->syncFailTimes    =  0;

        $problemObject = $this->getByID($problemID);
        if($problemObject->ReviewStatus != 'tofeedback' and $problemObject->ReviewStatus != 'todeptapprove' and $problemObject->ReviewStatus != 'sendback'  and $problemObject->ReviewStatus != 'syncfail' and $problemObject->ReviewStatus != 'jxsyncfail' and $problemObject->ReviewStatus != 'firstpassed' and $problemObject->ReviewStatus != 'externalsendback' and $problemObject->ReviewStatus != 'approvesuccess'){
            dao::$errors[''] = $this->lang->problem->editError;
            return false;
        }
        // 解决内容中有特殊字符导致截取问题（<= ）
        if(isset($problem->reason) && $problem->reason){
            $problem->reason = htmlentities($problem->reason);
        }
        if(isset($problem->solution) && $problem->solution){
            $problem->solution = htmlentities($problem->solution);
        }
        $this->dao->update(TABLE_PROBLEM)->data($problem)
            /*->checkIF($problem->ifReturn == '0', $this->config->problem->createfeedback->requiredFields, 'notempty')*/
            ->checkIF($problem->ifReturn == '0', 'Tier1Feedback', 'notempty')
            ->checkIF($problem->ifReturn == '0', 'SolutionFeedback', 'notempty')
            ->checkIF($problem->ifReturn == '0', 'PlannedTimeOfChange', 'notempty')
            ->checkIF($problem->ifReturn == '0', 'CorresProduct', 'notempty')
            ->checkIF($problem->ifReturn == '0' && $oldProblem->createdBy != 'guestjx', 'PlannedDateOfChangeReport', 'notempty')
            ->checkIF($problem->ifReturn == '0' && $oldProblem->createdBy != 'guestjx', 'PlannedDateOfChange', 'notempty')
            ->check('TeleOfIssueHandler', 'notempty')
            ->check('feedbackToHandle', 'notempty')
            /*->checkIF($problem->ifReturn == '0'  and $problem->SolutionFeedback == '1', 'ChangeSolvingTheIssue', 'notempty')*/
            ->checkIF($problem->ifReturn == '0', 'EditorImpactscope', 'notempty')
            ->checkIF($problem->ifReturn == '0', 'solution', 'notempty')
            ->checkIF($problem->ifReturn == '0', 'reason', 'notempty')
            ->checkIF($problem->ifReturn == '1', 'ReasonOfIssueRejecting', 'notempty')
            ->where('id')->eq($problemID)
            ->exec();

        if(!dao::isError())
        {
            $this->loadModel('file')->updateObjectID($this->post->uid, $problemID, 'problemFeedback');
            $this->file->saveUpload('problemFeedback', $problemID);
            $this->loadModel('consumed')->record('problem', $problemID, '0', $this->app->user->account,
                $oldProblem->ReviewStatus, $problem->ReviewStatus, array(), "problemFeedBack");

            //外部反馈单在内部中的状态也需要接口同步 待反馈 待部门审核 待产创审核
            $statusName = zget($this->lang->problem->consumedstatusList,'todeptapprove','');
            $this->syncFeedBackStatus($problemID,$statusName);

            //2022.5.16 tangfei 插入审批人员
            //获取二线专员
            $apiUser = $oldProblem->createdBy == 'guestjx' ? $this->lang->problem->apiDealUserList['jxDealAccount'] : $this->lang->problem->apiDealUserList['userAccount'];

            //            $apiUser        =  $this->dao->select('value')->from(TABLE_LANG)->where('module')->eq('problem')->andWhere('section')->eq('apiDealUserList')->fetch()->value;
            $this->loadModel('review');
            $this->review->addNode('problem', $problemID, $problem->version, explode(',', $problem->feedbackToHandle), true, 'pending', 1);
            $this->review->addNode('problem', $problemID, $problem->version, explode(',', $apiUser), true, 'wait', 2);
            $this->review->addNode('problem', $problemID, $problem->version, explode(',', 'guestjk'), true, 'wait', 3);
            $this->review->addNode('problem', $problemID, $problem->version, explode(',', $oldProblem->createdBy), true, 'wait', 4);
        }
        return common::createChanges($oldProblem, $problem);
    }

    public function getToAndCcListFeedBack($object)
    {
        /* Set toList and ccList. */
        $toList = $object->feedbackToHandle;

        $details = $this->loadModel('consumed')->getObjectByID($object->id, 'problem', $object->status);
        $ccList  = trim($details->mailto, ',');

        return array($toList, $ccList);
    }

    /**
     * 2022.5.17 tangfei 反馈单审批.
     */
    public function review($problemID,$extra='')
    {
        $problem = $this->getByID($problemID);
        //判断当前状态和审核中传入的状态是否一致，以防有同组中其他人修改
        $res = $this->checkAllowReview($problem, $this->post->version, $this->post->reviewStage, $this->app->user->account);
        if(!$res['result']){
            dao::$errors['statusError'] = $res['message'];
            return false;
        }

        $is_all_check_pass = false;
        $result = $this->loadModel('review')->check('problem', $problemID, $problem->version, $this->post->result, $this->post->reviewOpinion, $problem->reviewStage, '', $is_all_check_pass);
        if($result == 'pass')
        {
            $add = 1;
            //下一审核节点
            $nextReviewStage = $problem->reviewStage + $add;
            //下一审核状态
            if(isset($this->lang->problem->reviewNodeList[$nextReviewStage])){
                $status = $this->lang->problem->reviewNodeList[$nextReviewStage];
            }
            $lastDealDate = date('Y-m-d');
            $this->dao->update(TABLE_PROBLEM)->set('reviewStage = reviewStage+' . $add)->set('ReviewStatus')->eq($status)->set('lastDealDate')->eq($lastDealDate)->set('ReviewOpinion')->eq($this->post->reviewOpinion)->where('id')->eq($problemID)->exec();
            //反馈单部门审核通过的时间
            if($nextReviewStage == '2'){
                $dealPassDate = helper::now();
                if(empty($problem->dealFeedbackPass) ||strpos($problem->dealFeedbackPass,'0000-00-00') !== false){
                     $this->dao->update(TABLE_PROBLEM)->set('dealFeedbackPass')->eq($dealPassDate)->where('id')->eq($problemID)->exec();
                }
                //部门反馈审核首次通过时间
                if($problem->isChangeFeedbackTime == 0){
                    $node = $this->dao->select('id')->from(TABLE_REVIEWNODE)
                        ->where('`status`')->eq('pass')
                        ->andWhere('objectType')->eq('problem')
                        ->andWhere('objectID')->eq($problem->id)
                        ->andWhere('stage')->eq(1)->fetchAll();
                    if(count($node) == 1){
                        $this->dao->update(TABLE_PROBLEM)
                            ->set('deptPassTime')->eq($dealPassDate)
                            ->where('id')->eq($problemID)
                            ->exec();
                    }
                }
            }
            if('secondlineapproved' != $status){
                $this->loadModel('consumed')->record('problem', $problemID, '0', $this->app->user->account, $problem->ReviewStatus, $status, array(),"problemFeedBack");
            }
            $action = 'deptapproved' == $problem->ReviewStatus ? 'secondeal' : 'review';
            //外部反馈单在内部中的状态也需要接口同步 待反馈 待部门审核 待产创审核
            $syncStatus = array('tofeedback','todeptapprove','deptapproved');
            if(in_array($status,$syncStatus)){
               $statusName = zget($this->lang->problem->consumedstatusList,$status,'');
               $this->syncFeedBackStatus($problemID,$statusName);
            }
            $next = $this->dao->select('id')->from(TABLE_REVIEWNODE)->where('objectType')->eq('problem')
                ->andWhere('objectID')->eq($problemID)
                ->andWhere('version')->eq($problem->version)
                ->andWhere('status')->eq('wait')->orderBy('stage,id')->fetch('id');
            if($next)
            {
                $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('pending')->where('id')->eq($next)->exec();
                $this->dao->update(TABLE_REVIEWER)->set('status')->eq('pending')->where('node')->eq($next)->exec();

                $feedbackToHandle = $this->dao->select('reviewer')->from(TABLE_REVIEWER)->where('node')->eq($next)->fetch();
                $this->dao->update(TABLE_PROBLEM)->set('feedbackToHandle')->eq($feedbackToHandle->reviewer)->where('id')->eq($problemID)->exec();

                // 如果下一个状态为带外部审批，把返回单状态置为待同步外部
                if($status == 'secondlineapproved')
                {
                    $this->dao->update(TABLE_PROBLEM)->set('ReviewStatus')->eq('waitsync')->where('id')->eq($problemID)->exec();
                    $this->loadModel('consumed')->record('problem', $problemID, '0', $this->app->user->account, $problem->ReviewStatus, 'waitsync', array(),"problemFeedBack");
                    $problem->ReviewStatus = 'waitsync';
                }
                $this->loadModel('action')->create('problem', $problemID, $action, $this->post->comment,$extra);

                //如果下一个状态为带外部审批就推送反馈单
                if($status == 'secondlineapproved')
                {
                    if($problem->createdBy == 'guestjx' && $problem->ifReturn == '1') {
                        $this->rejectJxFeedback($problemID, $problem);
                    }elseif($problem->createdBy == 'guestjx'){
                        $this->pushJxFeedback($problemID, $problem);
                    }else {
                        $this->pushFeedback($problemID, $problem);
                    }
                }
            }
        }else if($result == 'reject')
        {
            $action = 'deptapproved' == $problem->ReviewStatus ? 'secondeal' : 'review';
            $lastDealDate = date('Y-m-d');
            $this->dao->update(TABLE_PROBLEM)->set('ReviewStatus')->eq('sendback')->set('lastDealDate')->eq($lastDealDate)->set('feedbackToHandle')->eq($problem->acceptUser)->set('ReviewOpinion')->eq($this->post->reviewOpinion)->where('id')->eq($problemID)->exec();
            $this->loadModel('consumed')->record('problem', $problemID, '0', $this->app->user->account, $problem->ReviewStatus, 'sendback', array(),"problemFeedBack");
            $this->loadModel('action')->create('problem', $problemID, $action, $this->post->comment,$extra);
             //外部反馈单在内部中的状态也需要接口同步 内部退回
            $statusName = zget($this->lang->problem->consumedstatusList,'sendback','');
            $this->syncFeedBackStatus($problemID,$statusName);
        }
    }


    /**
     * 2022.5.17 tangfei 检查信息是否允许当前用户审核.
     */
    public function checkAllowReview($problem, $version = 1,  $reviewStage = 0, $userAccount = '')
    {
        $res = array(
            'result'  => false,
            'message' => '',
        );
        if(!$problem){
            $res['message'] = $this->lang->common->errorParamId;
            return $res;
        }
        if(!$userAccount){
            $res['message'] = $this->lang->common->errorParamUser;
            return $res;
        }
        if('closed' == $problem->status){
            $res['message'] = $this->lang->problem->statusError;
            return $res;
        }
        //审核节点已经经过
        if(($version != $problem->version) || ($reviewStage != $problem->reviewStage) || ($problem->status == 'sendback')){
            $reviewerInfo = $this->loadModel('review')->getReviewedUserInfo('problem', $problem->id, $version, $reviewStage-1);
            if($reviewerInfo){
                $message = str_replace('%', $reviewerInfo->realname, $this->lang->review->statusError);
            }else{
                $message = $this->lang->problem->statusError;
            }
            $res['message'] = $message;
            return $res;
        }
        //获得当前的的审核人信息
        $reviews =  $this->loadModel('review')->getReviewer('problem', $problem->id, $problem->version, $problem->reviewStage);
        if(!$reviews){
            $res['message'] = $this->lang->review->reviewEnd;
            return $res;
        }
        $reviews = explode(',', $reviews);
        if(!in_array($userAccount, $reviews)){
            $res['message'] = $this->lang->review->statusUserError;
            return $res;
        }
        $res['result'] = true;
        return  $res;
    }

    function checkTimeFormat($date)
    {
        if(date('Y-m-d H:i:s', strtotime($date)) == $date ){
            return true;
        }
        if(date('Y-m-d H:i', strtotime($date)) == $date ){
            return true;
        }
        return false;
    }

    function checkDateFormat($date)
    {
        if(date('Y-m-d', strtotime($date)) == $date ){
            return true;
        }
        return false;
    }
    /**
     * 文件下载信息存库,返回file表id
     * @param $url
     * @param $problemId
     * @return mixed
     */
    public function saveFile($url, $problemId, $filename)
    {

//        if(filter_var($url, FILTER_VALIDATE_URL) === false){ //url不正确
//            return false;
//        }
        $pathName = $this->getUrlFile($url); //问题单下载文件 //已经crc32后的文件
        $serverPath = $this->getDir().crc32(basename($url)); //服务器绝对路径 没有后缀
        $file['objectType'] = 'problem';
        $file['objectID']   = $problemId;
        $file['addedBy']    = $this->app->user->account;
        $file['addedDate']  = helper::now();

        $file['title']      = $filename ?? basename($url);
        $file['pathname']   = $pathName;
        if(strrchr($filename, '.')){ //如果有后缀 记录后缀
            $file['extension'] = substr(strrchr($filename, '.'),1);
            $file['pathname']   .= '.' . $file['extension']; //数据库保持时需要后缀 真实文件没有
        } else {
            $file['extension'] = '';
        }

        $file['size']        = filesize($serverPath);
        $file['apiFile']     = 1; //标记接口文件

        $this->dao->update(TABLE_FILE)->set('deleted')->eq(1)
            ->where('objectType')->eq($file['objectType'])
            ->andWhere('objectID')->eq($file['objectID'])
            ->andWhere('pathname')->eq($file['pathname'])
            ->exec();

        if($file['size']){
            $this->dao->insert(TABLE_FILE)->data($file)->exec();
            return $this->dao->lastInsertId();
        } else {
            return  false;
        }
    }

    /**
     * 如果生产变更单、数据修正、数据获取单状态为“待上线”，则自动回填关联的需求条目或问题单的状态为“待上线”，待处理人置空
     * 如果生产变更单、数据修正、数据获取单状态为上线成功类的状态，则自动回填关联的需求条目或问题单的状态为“上线成功”，待处理人置空
     * 如果生产变更单、数据修正、数据获取单状态为上线失败类的状态，则自动回填关联的需求条目或问题单的状态为“上线失败”，待处理人置空
     */
    public function changeBySecondLine()
    {
        $problems =  $this->dao->select('id, status, actualOnlineDate')
            ->from(TABLE_PROBLEM)
            ->where('status')
            ->notIN("closed, deleted") //onlinesuccess, delivery,
            ->andWhere('createdDate')
            ->gt(date('Y-m-d', strtotime("-1 year")))
            ->fetchAll('id');
        $problemIds = array_keys($problems);
        $deliveryIdList = [];
        $onlineIdList = [];
        $onlineFailIdList = [];
        foreach ($problemIds as $problemId)
        {
            //取本单最后一个二线关联
            $relation =  $this->dao->select('relationID as last_relation_id, relationType')
                ->from(TABLE_SECONDLINE)
                ->where('objectType')
                ->eq('problem')
                ->andwhere('objectID')
                ->eq($problemId)
                ->andwhere('deleted') //选非删除的二线关联
                ->eq(0)
                ->andwhere('relationType')
                ->in('fix,gain,gainQz,infoqz,modify,modifycncc')
                ->orderBY("id_desc")
                ->fetch();
            if(empty($relation)) continue;
            if($relation->relationType == 'fix') {
                $info =  $this->dao->select('status, actualEnd')
                    ->from(TABLE_INFO)
                    ->where('id')
                    ->eq($relation->last_relation_id)
                    ->andwhere('action')
                    ->eq('fix')
                    ->fetch();
                if($info->status == 'productsuccess'){
                    if($problems[$problemId]->status != 'delivery') {
                        $deliveryIdList[] = $problemId;
                        $this->loadModel('consumed')->recordAuto('problem', $problemId, 0, $problems[$problemId]->status, 'delivery');
                    }
                } elseif ($info->status == 'closing') {
                    if($problems[$problemId]->status != 'onlinesuccess'){
                        $onlineIdList[] = $problemId;
                        $this->loadModel('consumed')->recordAuto('problem', $problemId, 0, $problems[$problemId]->status, 'onlinesuccess');
                    }
                    if($problems[$problemId]->actualOnlineDate < substr($info->actualEnd, 0, 10)) $this->setActEndTime($info->actualEnd, $problemId);
                }
            }
            elseif($relation->relationType == 'gain') {
                $info =  $this->dao->select('status,actualEnd')
                    ->from(TABLE_INFO)
                    ->where('id')
                    ->eq($relation->last_relation_id)
                    ->andwhere('action')
                    ->eq('gain')
                    ->fetch();
                if($info->status == 'productsuccess'){
                    if($problems[$problemId]->status != 'delivery'){
                        $deliveryIdList[] = $problemId;
                        $this->loadModel('consumed')->recordAuto('problem', $problemId, 0, $problems[$problemId]->status, 'delivery');
                    }
                }
                if ($info->status == 'fetchsuccess') {
                    if($problems[$problemId]->status != 'onlinesuccess'){
                        $onlineIdList[] = $problemId;
                        $this->loadModel('consumed')->recordAuto('problem', $problemId, 0, $problems[$problemId]->status, 'onlinesuccess');
                    }
                    if($problems[$problemId]->actualOnlineDate < substr($info->actualEnd, 0, 10)) $this->setActEndTime($info->actualEnd, $problemId);
                }
                if ($info->status == 'fetchfail') {
                    if($problems[$problemId]->status != 'onlinefailed'){
                        $onlineFailIdList[] = $problemId;
                        $this->loadModel('consumed')->recordAuto('problem', $problemId, 0, $problems[$problemId]->status, 'onlinefailed');
                    }
//                    if($problems[$problemId]->actualOnlineDate < substr($info->actualEnd, 0, 10)) $this->setActEndTime($info->actualEnd, $problemId);
                }
            }
            elseif($relation->relationType == 'gainQz') {
                $info =  $this->dao->select('status, externalStatus, actualEnd')
                    ->from(TABLE_INFO_QZ)
                    ->where('id')
                    ->eq($relation->last_relation_id)
                    ->andwhere('action')
                    ->eq('gain')
                    ->fetch();
                if($info->status == 'pass'){
                    if($problems[$problemId]->status != 'delivery'){
                        $deliveryIdList[] = $problemId;
                        $this->loadModel('consumed')->recordAuto('problem', $problemId, 0, $problems[$problemId]->status, 'delivery');
                    }
                }
                $this->loadModel('infoqz');
                if ($info->externalStatus == $this->lang->infoqz->externalStatusSuccess) { //if ($info->status == 'closing') {
                    if($problems[$problemId]->status != 'onlinesuccess'){
                        $onlineIdList[] = $problemId;
                        $this->loadModel('consumed')->recordAuto('problem', $problemId, 0, $problems[$problemId]->status, 'onlinesuccess'); $this->setActEndTime($info->actualEnd, $problemId);
                    }
                    if($problems[$problemId]->actualOnlineDate < substr($info->actualEnd, 0, 10)) $this->setActEndTime($info->actualEnd, $problemId);
                }
                elseif ($info->externalStatus == $this->lang->infoqz->externalStatusfailed) {
                    if($problems[$problemId]->status != 'onlinefailed'){
                        $onlineFailIdList[] = $problemId;
                        $this->loadModel('consumed')->recordAuto('problem', $problemId, 0, $problems[$problemId]->status, 'onlinefailed');
                    }
                    if($problems[$problemId]->actualOnlineDate < substr($info->actualEnd, 0, 10)) $this->setActEndTime($info->actualEnd, $problemId);
                }
            }
            elseif($relation->relationType == 'modify') {
                $info =  $this->dao->select('status, actualEnd,realEndTime')
                    ->from(TABLE_MODIFY)
                    ->where('id')
                    ->eq($relation->last_relation_id)
                    ->fetch();
                if($info->status == 'withexternalapproval'){ //bug 19537
                    if($problems[$problemId]->status != 'delivery'){
                        $this->loadModel('consumed')->recordAuto('problem', $problemId, 0, $problems[$problemId]->status, 'delivery');
                        $deliveryIdList[] = $problemId;
                    }
                } elseif ($info->status == 'modifysuccess') { //bug 19537
                    if($problems[$problemId]->status != 'onlinesuccess'){
                        $onlineIdList[] = $problemId;
                        $this->loadModel('consumed')->recordAuto('problem', $problemId, 0, $problems[$problemId]->status, 'onlinesuccess');
                    }
                    if($problems[$problemId]->actualOnlineDate < substr($info->realEndTime, 0, 10)) $this->setActEndTime($info->realEndTime, $problemId);
                }
            }
            elseif($relation->relationType == 'modifycncc') {
                $info =  $this->dao->select('status, actualEnd')
                    ->from(TABLE_MODIFYCNCC)
                    ->where('id')
                    ->eq($relation->last_relation_id)
                    ->fetch();
                if($info->status == 'withexternalapproval'){
                    if($problems[$problemId]->status != 'delivery'){
                        $deliveryIdList[] = $problemId;
                        $this->loadModel('consumed')->recordAuto('problem', $problemId, 0, $problems[$problemId]->status, 'delivery');
                    }
                } elseif ($info->status == 'modifysuccess') {
                    if($problems[$problemId]->status != 'onlinesuccess'){
                        $onlineIdList[] = $problemId;
                        $this->loadModel('consumed')->recordAuto('problem', $problemId, 0, $problems[$problemId]->status, 'onlinesuccess');
                    }
                    if($problems[$problemId]->actualOnlineDate < substr($info->actualEnd, 0, 10)) $this->setActEndTime($info->actualEnd, $problemId);
                }elseif ($info->status == 'modifyfail') {
                    if($problems[$problemId]->status != 'onlinefailed'){
                        $onlineFailIdList[] = $problemId;
                        $this->loadModel('consumed')->recordAuto('problem', $problemId, 0, $problems[$problemId]->status, 'onlinefailed');
                    }
                    if($problems[$problemId]->actualOnlineDate < substr($info->actualEnd, 0, 10)) $this->setActEndTime($info->actualEnd, $problemId);
                }
            }
        }
        if($deliveryIdList){
            $this->dao->update(TABLE_PROBLEM)->set('status')->eq('delivery')->where('id')->in($deliveryIdList)->exec();
            $this->setNoReview($deliveryIdList);
            $this->loadModel('action')->createActions('problem', $deliveryIdList, 'delivery');
        }
        if($onlineIdList){
            $this->dao->update(TABLE_PROBLEM)->set('status')->eq('onlinesuccess')->where('id')->in($onlineIdList)->exec();
            $this->setNoReview($onlineIdList);
            $this->loadModel('action')->createActions('problem', $onlineIdList, 'onlinesuccess');
        }
        if($onlineFailIdList){
            $this->dao->update(TABLE_PROBLEM)->set('status')->eq('onlinefailed')->where('id')->in($onlineFailIdList)->exec();
            $this->setReviewers($onlineFailIdList);
            $this->loadModel('action')->createActions('problem', $onlineFailIdList, 'onlinefailed');
        }

        return ['problemSecondLine onlinesuccess' =>  implode(',', $onlineIdList), 'problemSecondLine delivery' =>  implode(',', $deliveryIdList),'problemSecondLine onlinefailed' =>  implode(',', $onlineFailIdList),];

    }

    /**
     * TongYanQi 2022/12/24
     * 产品联系 云吉 产创联系 丽娇
     */
    public function changeBySecondLineV2()
    {
        $this->loadModel('review');
        //获取所有有效的问题单
        $problems =  $this->dao->select('id, status, code, actualOnlineDate, dealUser')
            ->from(TABLE_PROBLEM)
            ->where('status')
            //->notIN("confirmed,assigned,toclose,suspend,closed,deleted") //根据迭代25要求 待分配、待分析、待关闭、已挂起、已关闭不做联动
            ->notIN($this->lang->problem->statusArr['problemNotIn'] ) //根据迭代25要求 待分配、待分析、待关闭、已挂起、已关闭不做联动
            ->andWhere('createdDate')
            ->gt(date('Y-m-d', strtotime("-1 year")))
            ->andWhere('secureStatusLinkage')->eq('0')
            ->fetchAll('id');

        $problemIds = array_keys($problems); //取所有id
        $feedbackedIdList = []; //开发中
        $testingIdList  = [];  //测试中
        $releaseIdList  = [];  //已发布
        $deliveryIdList = []; //已交付
        $onlineIdList   = [];   //已上线
        $onlineTimeList = []; //上线时间
        //$exceptionIdList  = []; //变更异常
        foreach ($problemIds as $problemId) {
            $statusList[$problemId]['feedbacked'] = 0;  //开发中 关联数量
            $statusList[$problemId]['releaseSecond'] = 0;  //已发布 关联数量
            $statusList[$problemId]['releaseBuild'] = 0;  //已发布 关联数量
            $statusList[$problemId]['delivery'] = 0; //已交付 关联数量
            $statusList[$problemId]['online'] = 0;   //已上线 关联数量
            $statusList[$problemId]['testing'] = 0;  //测试中 关联数量
            #region 二线关联状态联动
            //取本单所有的二线关联
            $relations = $this->dao->select('relationID as last_relation_id, relationType')
                ->from(TABLE_SECONDLINE)
                ->where('objectType')->eq('problem')
                ->andwhere('objectID')->eq($problemId)
                ->andwhere('deleted')->eq(0)
               //->andwhere('relationType')->in('modify,gain,modifycncc,gainQz,outwardDelivery')
                ->andwhere('relationType')->in($this->lang->problem->statusArr['relationType'])
                ->orderBY("id_asc")
                ->fetchAll();
            foreach ($relations as $relation) {
                if (empty($relation)) continue;
                #region 二线金信
                if (in_array($relation->relationType, ['gain'])) { //如果是金信数据获取
                    $info = $this->dao->select('id, `status`, actualEnd, reviewStage, version,code')
                        ->from(TABLE_INFO)
                        ->where('id')->eq($relation->last_relation_id)
                        ->andwhere('action')->in('gain')
                        ->andwhere('status')->notIN('closed,deleted') //已关闭 已删除不做联动
                        ->fetch();
                    if (empty($info)) continue;
                    //待关联版本 已退回 待组长审批 待本部门审批 待系统部审批 待分管领导审批 待总经理审批 待产创部审核 联动为已发布
                    //if (in_array($info->status, ['wait','reject','cmconfirmed','groupsuccess','managersuccess','posuccess','gmsuccess'])) {  //
                    if (in_array($info->status, $this->lang->problem->statusArr['releaseGainType'])) {
                        $statusList[$problemId]['releaseSecond']++;
                        $releaseIdList[$problemId]['codeSecond'] = $info->code."($info->id)";
                    }
                    //待上线 获取失败 联动为已交付
                    //if (in_array($info->status, ['productsuccess', 'fetchfail'])) {  //
                    if (in_array($info->status, $this->lang->problem->statusArr['deliveryGainType'])) {  //
                        $statusList[$problemId]['delivery']++;
                        $deliveryIdList[$problemId]['codeSecond'] = $info->code."($info->id)";
                    }
                    //获取成功 联动为上线成功
                    if ($info->status == 'fetchsuccess') {
                        $statusList[$problemId]['online']++;
                        $onlineIdList[$problemId]['codeSecond'] = $info->code."($info->id)";
                        //取最新上线时间
//                       if(empty($onlineTimeList[$problemId]) || $info->actualEnd  > $onlineTimeList[$problemId]) { $onlineTimeList[$problemId] = $info->actualEnd; }
                    }
                    //金信交付-数据获取联动至问题池变更异常状态为：获取失败
                    /*if (in_array($info->status, $this->lang->problem->statusArr['exceptionGainType'])) {
                        $exceptionIdList[$problemId] = $problems[$problemId]->status;
                    }*/
                }
                elseif ($relation->relationType == 'modify') { //如果是金信生产变更
                    $info = $this->dao->select('id,code,status, actualEnd, realEndTime, dealUser')
                        ->from(TABLE_MODIFY)
                        ->where('id')->eq($relation->last_relation_id)
                        ->andWhere('abnormalCode')->eq('')
                        ->andWhere('status')->notIN("waitsubmitted,modifycancel,deleted") //待提交、已关闭、变更取消 已删除不做联动
                        ->fetch();
                    if (empty($info)) continue;
                    //待同步金信 同步金信失败 待关联版本 已退回 待组长审批 待本部门审批 待系统部审批 待分管领导审批 待总经理审批 待产创部审核 联动为已发布
                   /* if (in_array($info->status, ['waitqingzong', 'jxsynfailed','wait','reject','cmconfirmed','groupsuccess',
                        'managersuccess','posuccess','leadersuccess','gmsuccess'])) {*/
                      if (in_array($info->status, $this->lang->problem->statusArr['releaseModifyType'] )) {
                        $statusList[$problemId]['releaseSecond']++;
                        $releaseIdList[$problemId]['codeSecond'] = $info->code."($info->id)";
                    }
                    //待外部审批 部分成功 变更失败 变更退回 变更回退 变更异常 待变更实施  待上线 待关闭 受理人受理变更并审核  生产排期并提交实施 取消变更同步金信失败 已取消 取消退回 取消待同步金信 取消成功 取消待审批 联动为已交付
                    /*if (in_array($info->status, ['withexternalapproval',  'modifysuccesspart','modifyerror', 'modifyreject',
                        'modifyrollback','modifyfail','waitImplement','productsuccess','closing','jxacceptorReview','jxSubmitImplement','jxsyncancelfailed','canceled','cancelback','canceltojx','cancelsuccess','cancel'])) {*/
                      if (in_array($info->status, $this->lang->problem->statusArr['deliveryModifyType'])) {
                        $statusList[$problemId]['delivery']++;
                        $deliveryIdList[$problemId]['codeSecond'] = $info->code."($info->id)";
                    }
                    //变更成功 联动为上线成功
                    if (in_array($info->status,['modifysuccess','closed'])) {
                        $statusList[$problemId]['online']++;
                        $onlineIdList[$problemId]['codeSecond'] = $info->code."($info->id)";
                        $realEndTime = substr($info->realEndTime, 0, 10);
                        if(empty($onlineTimeList[$problemId]) || $realEndTime  > $onlineTimeList[$problemId]) { $onlineTimeList[$problemId] = $realEndTime; }
                    }
                    //金信交付-生产变更联动至问题池变更异常状态为：变更失败、变更退回、部分成功、变更异常、变更取消、变更回退
                    /*if (in_array($info->status, $this->lang->problem->statusArr['exceptionModifyType'])) {
                        $exceptionIdList[$problemId] = $problems[$problemId]->status;
                    }*/
                }
                #endregion
                #region 二线清总
                elseif ($relation->relationType == 'gainQz') {  //清总数据获取
                    $info = $this->dao->select('id, code,`status`, externalStatus, actualEnd, version, reviewStage')
                        ->from(TABLE_INFO_QZ)
                        ->where('id')->eq($relation->last_relation_id)
                        ->andwhere('action')->eq('gain')
                        ->andwhere('status')->notIN("closed,fetchclose,fentchcancel,deleted") //已关闭 数据获取关闭 数据获取取消 已删除 不做联动
                        ->fetch();
                    if (empty($info)) continue;

                    //待关联版本 已退回 待组长审批 待本部门审批 待系统部审批 待分管领导审批  待产创部审核 同步清总失败 待同步清总 联动为已发布
                    /*if (in_array($info->status, ['wait','reject','cmconfirmed','groupsuccess','managersuccess','posuccess',
                        'leadersuccess','qingzongsynfailed','pass'])) {*/
                      if (in_array($info->status, $this->lang->problem->statusArr['releaseQzType'])) {
                        $statusList[$problemId]['releaseSecond']++;
                        $releaseIdList[$problemId]['codeSecond'] = $info->code."($info->id)";
                    }
                    //待外部审批 获取部分成功 数据获取失败 数据获取退回 联动为已交付
                   /* if (in_array($info->status, ['withexternalapproval', 'fetchsuccesspart', 'fetchfail', 'outreject'])) {*/
                    if (in_array($info->status, $this->lang->problem->statusArr['deliveryQzType'] )) {
                        $statusList[$problemId]['delivery']++;
                        $deliveryIdList[$problemId]['codeSecond'] = $info->code."($info->id)";
                    }
                    // 数据获取成功 联动为 上线成功
                    if ($info->status == 'fetchsuccess') {
                        $statusList[$problemId]['online']++;
                        $onlineIdList[$problemId]['codeSecond'] = $info->code."($info->id)";
//                        if(empty($onlineTimeList[$problemId]) || $info->actualEnd  > $onlineTimeList[$problemId]) { $onlineTimeList[$problemId] = $info->actualEnd; }
                    }
                    //清总交付-数据获取联动之问题池变更异常状态为：获取部分成功、数据获取失败、数据获取退回、数据获取取消
                    /*if (in_array($info->status, $this->lang->problem->statusArr['exceptionQzType'] )) {
                        $exceptionIdList[$problemId] = $problems[$problemId]->status;
                    }*/
                }
                elseif (strtolower($relation->relationType) == 'outwarddelivery') { //清总对外交付
                    $info = $this->dao->select('id,code,status,closed,productEnrollId,testingRequestId,modifycnccId,dealUser')
                        ->from(TABLE_OUTWARDDELIVERY)
                        ->where('id')->eq($relation->last_relation_id)
                        ->andWhere('abnormalCode')->eq('')
                        ->andWhere('status')->notIN("waitsubmitted,modifycancel,closed") //待提交 变更取消 已关闭不做联动
                        ->andWhere('deleted')->eq(0)
                        ->fetch();
                    if (empty($info)) continue;
                    if ($info->closed){  //如果已关闭 忽略该条
                        continue;
                    }
                    if ($info->modifycnccId > 0) { //对外交付只处理生产变更
                        //待关联版本 已退回 待组长审批 待本部门审批 待系统部审批 待分管领导审批 待总经理审批 待产创部审核 同步清总失败 待同步清总 联动为已发布
                       /* if (in_array($info->status, ['wait','reject','cmconfirmed','groupsuccess','managersuccess','posuccess','leadersuccess',
                            'gmsuccess','qingzongsynfailed','waitqingzong'])) {*/
                          if (in_array($info->status,$this->lang->problem->statusArr['releaseOutwarddeliveryType'])) {
                            $statusList[$problemId]['releaseSecond']++;
                            $releaseIdList[$problemId]['codeSecond'] = $info->code."($info->id)";
                        }
                        //待外部审批 总中心产品经理审批 基准实验室审核 gitee审核通过 部分成功 变更失败 变更退回 联动为已交付
                        /*if (in_array($info->status, ['withexternalapproval',  'centrepmreview', 'psdlreview',
                            'giteepass', 'modifysuccesspart', 'modifyfail', 'modifyreject'])) {*/
                          if (in_array($info->status, $this->lang->problem->statusArr['deliveryOutwarddeliveryType'])) {
                            $statusList[$problemId]['delivery']++;
                            $deliveryIdList[$problemId]['codeSecond'] = $info->code."($info->id)";
                        }
                        if ($info->status == 'modifysuccess') {
                            $statusList[$problemId]['online']++;
                            $dealUserList[$problemId]['online'] = '';
                            $onlineIdList[$problemId]['codeSecond'] = $info->code."($info->id)";
                            $lastDealDate = $this->dao->select('actualEnd')->from(TABLE_MODIFYCNCC)->where('id')->eq($info->modifycnccId)->fetch('actualEnd');
                            if(empty($onlineTimeList[$problemId]) || $lastDealDate  > $onlineTimeList[$problemId]) { $onlineTimeList[$problemId] = $lastDealDate; }
                        }
                        //清总交付-生产变更联动之问题池变更异常状态为：变更失败、变更取消、变更退回
                        /*if (in_array($info->status, $this->lang->problem->statusArr['exceptionOutwardDeliveryType'])) {
                            $exceptionIdList[$problemId] = $problems[$problemId]->status;
                        }*/
                    }
                }
                #endregion
            }
            #region 关联制版
            //取所有关联的任务制版
            $builds = $this->dao->select('t.id,t.`name`,t.`status`,t.dealuser')
                ->from(TABLE_BUILD)->alias('t')
                ->where('t.problemid')->like("%{$problems[$problemId]->code}%")
                ->andwhere('t.`status`')->ne('wait')
                ->andWhere("id in(select max(id) from zt_build where project = t.project and app = t.app and product = t.product and  problemid like '%{$problems[$problemId]->code}%' and  deleted = '0' group by taskid)")
                ->andwhere('t.deleted')->eq(0)
                ->fetchAll('id');
            if($builds){
                foreach ($builds as $build) {
                    if ($build->status == 'released') {
                        $statusList[$problemId]['releaseBuild']++;
                        $releaseIdList[$problemId]['code'][] = $build->name."($build->id)";
                    } else {
                        $statusList[$problemId]['testing']++;
                        $testingIdList[$problemId]['code'][] = $build->name."($build->id)";
                    }
                }
            }
            else{
                $statusList[$problemId]['feedbacked'] ++;
                $feedbackedIdList[$problemId]['code'][] = $this->lang->problem->noBuildAndSecond;
            }
            #endregion
            #region 核心联动逻辑
            //重点：如果制版和二线同时存在 ,制版不是已发布 ，则取制版状态。反之，取二线状态  状态联动最小原则 ： 开发中 ->测试中 ->已发布 ->已交付 ->上线成功
            if(($statusList[$problemId]['releaseBuild'] || $statusList[$problemId]['testing']) && ($statusList[$problemId]['releaseSecond'] ||$statusList[$problemId]['delivery']||$statusList[$problemId]['online'])){
                //制版和二线同时存在
                //如果制版 有测试中的 状态就是测试(build)
                if ($statusList[$problemId]['testing']) {
                    if($problems[$problemId]->status != 'build' ){ //如果状态不是测试中
                        //为备注创建
                        $testingIdList = $this->createToArr($problemId,$testingIdList,$problems[$problemId]->status,'build');
                        $this->dao->update(TABLE_PROBLEM)->set('status')->eq('build')->set('actualOnlineDate')->eq(null)->where('id')->eq($problemId)->exec();
                        $this->loadModel('consumed')->recordAuto('problem', $problemId, 0, $problems[$problemId]->status, 'build');
                    }
                    //清除不满足数组条件的
                    $clearArr =  $this->clearToArr($onlineIdList,$deliveryIdList,$releaseIdList,$testingIdList,$feedbackedIdList,$problemId);
                    $onlineIdList = $clearArr['online'];
                    $deliveryIdList  = $clearArr['delivery'];
                    $releaseIdList = $clearArr['release'];
                    $testingIdList = $clearArr['testing'];
                    $feedbackedIdList = $clearArr['feedbacked'];
                    continue;
                }
                //如果有制版发布 && 有二线已发布.则问题单状态为 二线已发布
                if($statusList[$problemId]['releaseBuild'] && $statusList[$problemId]['releaseSecond']){
                    if($problems[$problemId]->status != 'released'){
                        unset($releaseIdList[$problemId]['code']);
                        //为备注创建
                        $releaseIdList = $this->createToArr($problemId,$releaseIdList,$problems[$problemId]->status,'released');
                        $releaseIdList[$problemId]['code'][]  = isset($releaseIdList[$problemId]['codeSecond'] ) ? $releaseIdList[$problemId]['codeSecond'] :'';
                        //$this->dao->update(TABLE_PROBLEM)->set('status')->eq('onlinesuccess')->set('dealUser')->eq('')->set('actualOnlineDate')->eq($onlineTimeList[$problemId])->where('id')->eq($problemId)->exec();
                        $this->loadModel('consumed')->recordAuto('problem', $problemId, 0, $problems[$problemId]->status, 'released');
                    }
                    //清除不满足数组条件的
                    $clearArr =  $this->clearToArr($onlineIdList,$deliveryIdList,$releaseIdList,$testingIdList,$feedbackedIdList,$problemId);
                    $onlineIdList = $clearArr['online'];
                    $deliveryIdList  = $clearArr['delivery'];
                    $releaseIdList = $clearArr['release'];
                    $testingIdList = $clearArr['testing'];
                    $feedbackedIdList = $clearArr['feedbacked'];
                    continue;
                }
                //如果有发布,且所有制版状态都是已发布 &&没有二线已发布.有二线已交付 则问题单状态为 已交付
                if($statusList[$problemId]['releaseBuild'] && !$statusList[$problemId]['releaseSecond'] && $statusList[$problemId]['delivery'] ){
                    if(count($builds) == $statusList[$problemId]['releaseBuild'] && count($builds) != 0 && $problems[$problemId]->status != 'delivery') { //如果状态不是已发布
                        //为备注创建
                        $deliveryIdList = $this->createToArr($problemId,$deliveryIdList,$problems[$problemId]->status,'delivery');
                        $deliveryIdList[$problemId]['code'][]  = isset($deliveryIdList[$problemId]['codeSecond'] ) ? $deliveryIdList[$problemId]['codeSecond'] :'';
                        $this->loadModel('consumed')->recordAuto('problem', $problemId, 0, $problems[$problemId]->status, 'delivery');
                    }

                    //清除不满足数组条件的
                    $clearArr =  $this->clearToArr($onlineIdList,$deliveryIdList,$releaseIdList,$testingIdList,$feedbackedIdList,$problemId);
                    $onlineIdList = $clearArr['online'];
                    $deliveryIdList  = $clearArr['delivery'];
                    $releaseIdList = $clearArr['release'];
                    $testingIdList = $clearArr['testing'];
                    $feedbackedIdList = $clearArr['feedbacked'];
                    continue;
                }
                //如果有发布,且所有制版状态都是已发布 &&没有二线已发布.有二线上线成功 则问题单状态为 上线成功
                if($statusList[$problemId]['releaseBuild'] && !$statusList[$problemId]['releaseSecond'] && $statusList[$problemId]['online'] ){
                    if(count($builds) == $statusList[$problemId]['releaseBuild'] && count($builds) != 0 && $problems[$problemId]->status != 'onlinesuccess') { //如果状态不是已发布
                        //为备注创建
                        $onlineIdList = $this->createToArr($problemId,$onlineIdList,$problems[$problemId]->status,'onlinesuccess');
                        $onlineIdList[$problemId]['code'][]  = isset($onlineIdList[$problemId]['codeSecond'] ) ? $onlineIdList[$problemId]['codeSecond'] :'';
                        $this->dao->update(TABLE_PROBLEM)->set('status')->eq('onlinesuccess')->set('actualOnlineDate')->eq($onlineTimeList[$problemId])->where('id')->eq($problemId)->exec();
                        $this->loadModel('consumed')->recordAuto('problem', $problemId, 0, $problems[$problemId]->status, 'onlinesuccess');
                    }
                    //更新上线时间
                    if(!empty($onlineTimeList[$problemId]) && $onlineTimeList[$problemId]  > $problems[$problemId]->actualOnlineDate) {
                        $this->dao->update(TABLE_PROBLEM)->set('actualOnlineDate')->eq($onlineTimeList[$problemId])->where('id')->eq($problemId)->exec();
                    }

                    //清除不满足数组条件的
                    $clearArr =  $this->clearToArr($onlineIdList,$deliveryIdList,$releaseIdList,$testingIdList,$feedbackedIdList,$problemId);
                    $onlineIdList = $clearArr['online'];
                    $deliveryIdList  = $clearArr['delivery'];
                    $releaseIdList = $clearArr['release'];
                    $testingIdList = $clearArr['testing'];
                    $feedbackedIdList = $clearArr['feedbacked'];
                    continue;
                }
            }else if(($statusList[$problemId]['releaseBuild'] || $statusList[$problemId]['testing']) && ($statusList[$problemId]['releaseSecond'] == 0 && $statusList[$problemId]['delivery'] == 0 && $statusList[$problemId]['online'] == 0)){
                //只有制版
                //如果制版 有测试中的 状态就是测试(build)
                if ($statusList[$problemId]['testing']) {
                    if($problems[$problemId]->status != 'build' ){ //如果状态不是测试中
                        //为备注创建
                        $testingIdList = $this->createToArr($problemId,$testingIdList,$problems[$problemId]->status,'build');
                        $this->dao->update(TABLE_PROBLEM)->set('status')->eq('build')->set('actualOnlineDate')->eq(null)->where('id')->eq($problemId)->exec();
                        $this->loadModel('consumed')->recordAuto('problem', $problemId, 0, $problems[$problemId]->status, 'build');
                    }
                    //清除不满足数组条件的
                    $clearArr =  $this->clearToArr($onlineIdList,$deliveryIdList,$releaseIdList,$testingIdList,$feedbackedIdList,$problemId);
                    $onlineIdList = $clearArr['online'];
                    $deliveryIdList  = $clearArr['delivery'];
                    $releaseIdList = $clearArr['release'];
                    $testingIdList = $clearArr['testing'];
                    $feedbackedIdList = $clearArr['feedbacked'];
                    continue;
                }
                //如果有发布,且所有制版状态都是已发布 &&没有二线已发布.则问题单状态为 已发布
                if($statusList[$problemId]['releaseBuild']  ){
                    if(count($builds) == $statusList[$problemId]['releaseBuild'] && count($builds) != 0 && $problems[$problemId]->status != 'released') { //如果状态不是已发布
                        //为备注创建
                        $releaseIdList = $this->createToArr($problemId,$releaseIdList,$problems[$problemId]->status,'released');
                        $this->loadModel('consumed')->recordAuto('problem', $problemId, 0, $problems[$problemId]->status, 'released');
                    }
                    //清除不满足数组条件的
                    $clearArr =  $this->clearToArr($onlineIdList,$deliveryIdList,$releaseIdList,$testingIdList,$feedbackedIdList,$problemId);
                    $onlineIdList = $clearArr['online'];
                    $deliveryIdList  = $clearArr['delivery'];
                    $releaseIdList = $clearArr['release'];
                    $testingIdList = $clearArr['testing'];
                    $feedbackedIdList = $clearArr['feedbacked'];
                    continue;
                }
            }else if(($statusList[$problemId]['releaseSecond'] ||$statusList[$problemId]['delivery']||$statusList[$problemId]['online']) && ($statusList[$problemId]['releaseBuild'] == 0 && $statusList[$problemId]['testing'] == 0)){
                //只有二线
                if(isset($feedbackedIdList[$problemId]) && !isset($feedbackedIdList[$problemId]['status'])){
                    unset($feedbackedIdList[$problemId]);
                }
                //如果有二线已发布.则问题单状态为 二线已发布
                if($statusList[$problemId]['releaseSecond']){
                    if($problems[$problemId]->status != 'released'){
                        if(isset($releaseIdList[$problemId]['code'])) unset($releaseIdList[$problemId]['code']);
                        //为备注创建
                        $releaseIdList = $this->createToArr($problemId,$releaseIdList,$problems[$problemId]->status,'released');
                        $releaseIdList[$problemId]['code'][]  = isset($releaseIdList[$problemId]['codeSecond'] ) ? $releaseIdList[$problemId]['codeSecond'] :'';
                        //$this->dao->update(TABLE_PROBLEM)->set('status')->eq('onlinesuccess')->set('dealUser')->eq('')->set('actualOnlineDate')->eq($onlineTimeList[$problemId])->where('id')->eq($problemId)->exec();
                        $this->loadModel('consumed')->recordAuto('problem', $problemId, 0, $problems[$problemId]->status, 'released');
                    }
                    //清除不满足数组条件的
                    $clearArr =  $this->clearToArr($onlineIdList,$deliveryIdList,$releaseIdList,$testingIdList,$feedbackedIdList,$problemId);
                    $onlineIdList = $clearArr['online'];
                    $deliveryIdList  = $clearArr['delivery'];
                    $releaseIdList = $clearArr['release'];
                    $testingIdList = $clearArr['testing'];
                    $feedbackedIdList = $clearArr['feedbacked'];
                    continue;
                }
                //如果当前状态不是已交付 && 无其他关联状态 改为已交付状态
                if ($statusList[$problemId]['delivery']) {
                    if($problems[$problemId]->status != 'delivery'){ //如果状态 没变化
                        //为备注创建
                        $deliveryIdList = $this->createToArr($problemId,$deliveryIdList,$problems[$problemId]->status,'delivery');
                        $deliveryIdList[$problemId]['code'][]  = isset($deliveryIdList[$problemId]['codeSecond'] ) ? $deliveryIdList[$problemId]['codeSecond'] :'';
                        $this->loadModel('consumed')->recordAuto('problem', $problemId, 0, $problems[$problemId]->status, 'delivery');
                    }
                    //清除不满足数组条件的
                    $clearArr =  $this->clearToArr($onlineIdList,$deliveryIdList,$releaseIdList,$testingIdList,$feedbackedIdList,$problemId);
                    $onlineIdList = $clearArr['online'];
                    $deliveryIdList  = $clearArr['delivery'];
                    $releaseIdList = $clearArr['release'];
                    $testingIdList = $clearArr['testing'];
                    $feedbackedIdList = $clearArr['feedbacked'];
                    continue;
                }
                //如果其他都没有只有上线成功 状态改为上线成功
                if ($statusList[$problemId]['online']){
                    if($problems[$problemId]->status != 'onlinesuccess'){
                    //为备注创建
                    $onlineIdList = $this->createToArr($problemId,$onlineIdList,$problems[$problemId]->status,'onlinesuccess');
                    $onlineIdList[$problemId]['code'][]  = isset($onlineIdList[$problemId]['codeSecond'] ) ? $onlineIdList[$problemId]['codeSecond'] :'';
                    $this->dao->update(TABLE_PROBLEM)->set('status')->eq('onlinesuccess')->set('actualOnlineDate')->eq($onlineTimeList[$problemId])->where('id')->eq($problemId)->exec();
                    $this->loadModel('consumed')->recordAuto('problem', $problemId, 0, $problems[$problemId]->status, 'onlinesuccess');
                    }
                    //更新上线时间
                    if(!empty($onlineTimeList[$problemId]) && $onlineTimeList[$problemId]  > $problems[$problemId]->actualOnlineDate) {
                        $this->dao->update(TABLE_PROBLEM)->set('actualOnlineDate')->eq($onlineTimeList[$problemId])->where('id')->eq($problemId)->exec();
                    }
                    //清除不满足数组条件的
                    $clearArr =  $this->clearToArr($onlineIdList,$deliveryIdList,$releaseIdList,$testingIdList,$feedbackedIdList,$problemId);
                    $onlineIdList = $clearArr['online'];
                    $deliveryIdList  = $clearArr['delivery'];
                    $releaseIdList = $clearArr['release'];
                    $testingIdList = $clearArr['testing'];
                    $feedbackedIdList = $clearArr['feedbacked'];
                    continue;
                }
            }else{
                //制版和二线都不存在
                //如果没有制版，问题单状态更新为开发中（迭代25）
                if($statusList[$problemId]['feedbacked']){
                    if($problems[$problemId]->status != 'feedbacked'){
                        //为备注创建
                        $feedbackedIdList = $this->createToArr($problemId,$feedbackedIdList,$problems[$problemId]->status,'feedbacked');
                        $this->dao->update(TABLE_PROBLEM)->set('status')->eq('feedbacked')->set('actualOnlineDate')->eq(null)->where('id')->eq($problemId)->exec();
                        $this->loadModel('consumed')->recordAuto('problem', $problemId, 0, $problems[$problemId]->status, 'feedbacked');
                    }
                    //清除不满足数组条件的
                    $clearArr =  $this->clearToArr($onlineIdList,$deliveryIdList,$releaseIdList,$testingIdList,$feedbackedIdList,$problemId);
                    $onlineIdList = $clearArr['online'];
                    $deliveryIdList  = $clearArr['delivery'];
                    $releaseIdList = $clearArr['release'];
                    $testingIdList = $clearArr['testing'];
                    $feedbackedIdList = $clearArr['feedbacked'];
                    continue;
                }
            }
        }
        //开发中
        if($feedbackedIdList){
            $this->loadModel('action')->createActions('problem', $feedbackedIdList, 'feedback',$this->lang->problem->nowConsumedstatusList);
        }
        //测试中
        if($testingIdList){
            $this->loadModel('action')->createActions('problem', $testingIdList, 'build',$this->lang->problem->nowConsumedstatusList);
        }
        //已交付
        if($deliveryIdList){
            $this->dao->update(TABLE_PROBLEM)->set('status')->eq('delivery')->set('actualOnlineDate')->eq(null)->where('id')->in(array_filter(array_unique(array_keys($deliveryIdList))))->exec();
            $this->loadModel('action')->createActions('problem', $deliveryIdList, 'delivery',$this->lang->problem->nowConsumedstatusList);
        }
        //上线成功
        if($onlineIdList){ //需要处理每个上线时间 不能统一执行
            $this->loadModel('action')->createActions('problem', $onlineIdList, 'onlinesuccess',$this->lang->problem->nowConsumedstatusList);
        }
        //已发布统一处理
        if($releaseIdList){
            $this->dao->update(TABLE_PROBLEM)->set('status')->eq('released')->set('actualOnlineDate')->eq(null)->where('id')->in(array_filter(array_unique(array_keys($releaseIdList))))->exec();
            $this->loadModel('action')->createActions('problem', $releaseIdList, 'released',$this->lang->problem->nowConsumedstatusList);
        }
        //变更异常
        /*if(!empty($exceptionIdList)){
            $this->dao
                ->update(TABLE_PROBLEM)
                ->set('status')->eq('exception')
                ->where('id')->in(array_keys($exceptionIdList))
                ->exec();
            foreach ($exceptionIdList as $id => $status){
                $this->loadModel('consumed')->recordAuto('problem', $id, 0, $status, 'exception');
            }
        }*/

        return ['problem onlinesuccess' => $onlineIdList, 'problem delivery' => $deliveryIdList,'problem released' => $releaseIdList, 'problem testing' => $testingIdList, 'problem feedback' => $feedbackedIdList];

    }

    /**
     * 为备注创建数组
     * @param $problemID
     * @param $typeIdList
     * @param $oldStatus
     * @param $status
     * @return mixed
     */
    public function createToArr($problemID,$typeIdList,$oldStatus,$status){
        $typeIdList[$problemID]['id'] = $problemID;
        $typeIdList[$problemID]['oldStatus'] = $oldStatus;
        $typeIdList[$problemID]['status'] = $status;
        return $typeIdList;
    }
    /**
     * 清除不满足要求数组
     * @param $onlineIdList   上线成功
     * @param $deliveryIdList 已交付
     * @param $releaseIdList  已发布
     * @param $testingIdList  测试中
     * @param $feedbackedIdList 开发中
     * @param $exceptionIdList 变更异常
     * @param $problemID
     */
    public function clearToArr($onlineIdList,$deliveryIdList,$releaseIdList,$testingIdList,$feedbackedIdList, $problemID){
        if(isset($onlineIdList[$problemID]) && !isset($onlineIdList[$problemID]['status'])){
            unset($onlineIdList[$problemID]);
        }
        if(isset($deliveryIdList[$problemID]) && !isset($deliveryIdList[$problemID]['status'])){
            unset($deliveryIdList[$problemID]);
        }
        if(isset($releaseIdList[$problemID]) && !isset($releaseIdList[$problemID]['status'])){
            unset($releaseIdList[$problemID]);
        }
        if(isset($testingIdList[$problemID]) && !isset($testingIdList[$problemID]['status'])){
            unset($testingIdList[$problemID]);
        }
        if(isset($feedbackedIdList[$problemID]) && !isset($feedbackedIdList[$problemID]['status'])){
            unset($feedbackedIdList[$problemID]);
        }
        return array(
            'online' => $onlineIdList,
            'delivery' =>$deliveryIdList,
            'release' =>$releaseIdList,
            'testing' =>$testingIdList,
            'feedbacked' =>$feedbackedIdList,
        );
    }

    /**
     * TongYanQi 2022/12/29
     * 整里待处理人
     */
    private function getNowDealUserString ($nowDealUsers): string
    {
        $nowDealUsers = trim($nowDealUsers ,',');
        $nowDealUsers = explode(',', $nowDealUsers );
        $nowDealUsers = array_unique($nowDealUsers);
        return ','.implode(',', $nowDealUsers).',';
    }
    /**
     * 待处理人置空
     * @param $problemID
     */
    public function setNoReview($problemIDs)
    {
        if(empty($problemIDs)) return false;
        return $this->dao->update(TABLE_PROBLEM)
            ->set('dealUser')->eq("")
            ->where('id')->in($problemIDs)
            ->exec();
    }

    /**
     * User: TongYanQi
     * Date: 2022/8/30
     * 更新实际上线时间
     */
    public function setActEndTime($datetime, $problemID)
    {
        if(empty($datetime)) return;
        return $this->dao->update(TABLE_PROBLEM)
            ->set('actualOnlineDate')->eq($datetime)
            ->where('id')->eq($problemID)
            ->exec();
    }

    /**
     * 待处理人重置
     * @param $problemID
     */
    public function setReviewers($problemIDs)
    {
        if(empty($problemIDs)) return false;
        return $this->dao->update(TABLE_PROBLEM)
            ->set('dealUser = acceptUser')
            ->where('id')->in($problemIDs)
            ->exec();
    }

    /**
     * User: TongYanQi
     * Date: 2022/8/29
     * 清总问题反馈失败重试
     */
    public function rePushFeedBacks()
    {
        //这里select* 是了提高子函数效率 无需再次查询id
        $problems = $this->dao->select("*")
            ->from(TABLE_PROBLEM)
            ->where('ReviewStatus')->in(['syncfail','jxsyncfail'])
            ->andWhere('syncFailTimes')->lt(3)
            ->fetchall('id');
        if(empty($problems)) return null;
        foreach ($problems as $id => $problem){
            if($problem->createdBy == 'guestjx' && $problem->ifReturn == '1') {
                $this->rejectJxFeedback($id, $problem);
            }elseif($problem->createdBy == 'guestjx'){
                $this->pushJxFeedback($id, $problem);
            }else {
                $this->pushFeedback($id, $problem);
            }
        }
        return  "problem_feedback_repush:".implode(',',  array_keys($problems));
    }

    /**
     * @param $time
     * @param $length
     * @return false|string
     */
    function setMonthAfterTime($time, $length)
    {
        // $time => 时间戳  $length => 加减几月(数字)
        if (!is_numeric($time)) $time = strtotime($time);
        if ($length > 0) $length = "+$length";
        $hour = date(' H:i:s', $time);
        $day = date('d', $time);
        if ($day == '29' || $day == '30' || $day == '31') {
            // 目标年月
            $targetTime = strtotime(date('Y-m', $time) . " $length month");
            $targetYearMonth = date('Y-m-', $targetTime);
            // 目标月最后一天
            $targetLastDay = date('t', $targetTime);
            // 如果目标月最后一天大于等于 $day 则正常返回，否则返回目标月的最后一天
            if ($targetLastDay >= $day) $targetLastDay = $day;
            // 返回目标时间 格式:xxxx-xx-xx xx:xx:xx
            return $targetYearMonth . $targetLastDay . $hour;
        }
        return date('Y-m-d H:i:s', strtotime("$length month", $time));
    }

    /**
     * @param $problemID
     * @param null $problem
     *
     */
    public function pushJxFeedback($problemID, $problem = null)
    {
        $consumedUser = empty($this->app->user->account) ? 'guestjk' : $this->app->user->account;
        /* 获取问题单。*/
        if($problem == null){
            $problem = $this->getByID($problemID);
        }

        $pushEnable = $this->config->global->jxProblemFeedbackEnable;
        //判断请求配置是否可用
        if ($pushEnable == 'enable') {
            $url = $problem->firstPush == 1 ? $this->config->global->jxProblemFeedbackUrl : $this->config->global->jxProblemReFeedbackUrl;
            $pushAppId = $this->config->global->jxProblemFeedbackAppId;
            $pushAppSecret = $this->config->global->jxProblemFeedbackAppSecret;
//            $pushUsername = $this->config->global->pushProblemFeedbackUsername;
            $fileIP       = $this->config->global->jxProblemFileIP;
            $headers = array();
            $headers[] = 'appId: ' . $pushAppId;
            $headers[] = 'appSecret: ' . $pushAppSecret;
            $ts = time();
            $headers[] = 'ts: ' . $ts;
            $uuid = $this->create_guid();
            $headers[] = 'nonce: ' . $uuid;
            $sign = md5('appId='.$pushAppId.'&nonce='.$uuid.'&ts='.$ts.'&appSecret='.$pushAppSecret);
            $headers[] = 'sign: ' . $sign;

            $deptList = $this->loadModel('dept')->getOptionMenu();
            $users = $this->loadmodel('user')->getPairs('noletter');

            $data = array();
            $pushData = array();
            $data['idUnique']               = $problem->IssueId;
            $data['approvalOpinion']        = $problem->ReviewOpinion;
            $data['id']                     = $problem->extId;
            $data['approvalType']           = 1;
            $data['processUserName']        = zget($users, $problem->acceptUser, '');
            //解决方式
            $data['resolveMethod']          = zget($this->lang->problem->solutionFeedbackList, $problem->SolutionFeedback, $problem->SolutionFeedback);;
            $data['problemSolutionProgram'] = strip_tags(str_replace("&nbsp;"," ",htmlspecialchars_decode($problem->solution)));
            $data['problemReason']          = strip_tags(str_replace("&nbsp;"," ",htmlspecialchars_decode($problem->reason)));
            $data['processUserContact']     = $problem->TeleOfIssueHandler;
            $data['isFinalSolution']        = strval($problem->IfultimateSolution) ;
            //只有变更时才传 ChangeSolvingTheIssue
            $data['changeIdUniqueResolveTo']  = $problem->SolutionFeedback == 1 ? strip_tags(str_replace("&nbsp;"," ",htmlspecialchars_decode($problem->ChangeSolvingTheIssue))) : "";
            $data['resolveTime']    = $problem->PlannedTimeOfChange;

            //附件
            $RelationFiles  =   array();
            $problem->RelationFiles = $problem->RelationFiles ?? [];
            foreach ($problem->RelationFiles as $file)
            {
                $tail  = 0;
                if($file->extension){
                    $tail = strlen($file->extension) + 1;
                }
                $file->realPath = substr($fileIP.'/api.php?m=api&f=getfile&code=jinke1problem&time=1&token=1&filename='.$file->pathname, 0, -$tail); //实际存的附件没有后缀 需要去掉
                $file->md5      = md5_file($file->realPath);
                array_push($RelationFiles, array('address'=> $file->realPath, 'md5'=> $file->md5, 'fileName' => $file->title));
            }
            $data['processFileInfoList']       = $RelationFiles;

            $pushData['data'] = $data;
            $object = 'problem';
            $objectType = 'feedback';
            $response = '';
            $status = 'fail';
            $extra = '';

            $result = $this->loadModel('requestlog')->http($url, $pushData, 'PATCH', 'json', array(), $headers);
            $node = $this->dao->select('*')->from(TABLE_REVIEWNODE)
                ->where('objectType')->eq('problem')
                ->andWhere('objectID')->eq($problem->id)
                ->andWhere('version')->eq($problem->version)
                ->andWhere('stage')->eq('3')
                ->orderBy('stage,id')
                ->fetch();
            $updateStatus = '';
            $updateComment = '';
            if(!empty($result))
            {
                $resultData = json_decode($result);
                if($resultData->code == '0')
                {
                    $now = date('Y-m-d H:i:s');
                    $isOverTime = 0;
                    if(strtotime($problem->feedbackExpireTime) < strtotime($now)){
                        $isOverTime = 1;
                    }
                    $isInnovationPassTime = $this->getInnovationPassTime($problem);
                    $status = 'success';
                    $this->dao->update(TABLE_PROBLEM)
                        //->set('ReviewStatus')->eq('jxsyncsuccess')
                        ->set('ReviewStatus')->eq('secondlineapproved')
                        ->set('feedbackToHandle')->eq('guestjx')
                        ->set('isAfterSubmit')->eq(1)
                        ->beginIF($problem->firstPushDateFlag == 1)->set('firstPushDate')->eq($now)
                        ->set('ifOverDate')->eq($isOverTime)
                        ->set('firstPushDateFlag')->eq(0)->fi()
                        ->set('firstPush')->eq(0)
                        ->beginIF($isInnovationPassTime == false && $problem->isChangeFeedbackTime == 0)
                        ->set('innovationPassTime')->eq(helper::now())->fi()
                        ->where('id')->eq($problem->id)->exec();
                    $updateStatus = 'jxsyncsuccess';
                    $updateComment = $resultData->description;
                    $this->loadModel('consumed')->record('problem', $problem->id, 0, $consumedUser, $problem->ReviewStatus, 'jxsyncsuccess',  array(),"problemFeedBack");
                    $this->loadModel('consumed')->record('problem', $problem->id, 0, 'guestjk', 'jxsyncsuccess', 'secondlineapproved',  array(),"problemFeedBack");
                } else {
                    $this->dao->update(TABLE_PROBLEM)
                        ->set('ReviewStatus')->eq('jxsyncfail')
                        ->set('syncFailTimes')->eq(intval($problem->syncFailTimes) + 1)->fi()
                        ->set('feedbackToHandle')->eq($problem->acceptUser)
                        ->where('id')->eq($problem->id)->exec();
                    $updateStatus = 'jxsyncfail';
                    $updateComment = $resultData->description;
                    if('jxsyncfail' != $problem->ReviewStatus){
                        $this->loadModel('consumed')->record('problem', $problem->id, 0, $consumedUser, $problem->ReviewStatus, 'jxsyncfail',  array(),"problemFeedBack");
                    }
                }

                $response = $result;
            } else {
                $this->dao->update(TABLE_PROBLEM)
                    ->set('ReviewStatus')->eq('jxsyncfail')
                    ->set('syncFailTimes')->eq(intval($problem->syncFailTimes) + 1)->fi()
                    ->set('feedbackToHandle')->eq($problem->acceptUser)
                    ->where('id')->eq($problem->id)->exec();
                $updateStatus = 'jxsyncfail';
                $updateComment = '网络不通';
                if('jxsyncfail' != $problem->ReviewStatus){
                    $this->loadModel('consumed')->record('problem', $problem->id, 0, $consumedUser, $problem->ReviewStatus, 'jxsyncfail',  array(),"problemFeedBack");
                }
            }
            $this->dao->update(TABLE_REVIEWER)
                ->set('status')->eq($updateStatus)
                ->set('comment')->eq($updateComment)
                ->set('reviewTime')->eq(helper::now())
                ->where('node')->eq($node->id)
                ->andWhere('reviewer')->eq('guestjk') //当前审核人
                ->exec();

            $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq($updateStatus)
                ->where('id')->eq($node->id)
                ->exec();

            $this->loadModel('action')->create('problem', $problem->id, $updateStatus, $updateComment,'','guestjk');
            $this->requestlog->saveRequestLog($url, $object, $objectType, 'POST', $pushData, $response, $status, $extra);
        }
    }

    /**
     * @param $problemID
     * @param null $problem
     *
     */
    public function rejectJxFeedback($problemID, $problem = null)
    {
        $consumedUser = empty($this->app->user->account) ? 'guestjk' : $this->app->user->account;
        /* 获取问题单。*/
        if($problem == null){
            $problem = $this->getByID($problemID);
        }

        $pushEnable = $this->config->global->jxProblemFeedbackEnable;
        //判断请求配置是否可用
        if ($pushEnable == 'enable') {
            $url = $this->config->global->jxProblemRejectFeedbackUrl;
            $pushAppId = $this->config->global->jxProblemFeedbackAppId;
            $pushAppSecret = $this->config->global->jxProblemFeedbackAppSecret;
            $headers = array();
            $headers[] = 'appId: ' . $pushAppId;
            $headers[] = 'appSecret: ' . $pushAppSecret;
            $ts = time();
            $headers[] = 'ts: ' . $ts;
            $uuid = $this->create_guid();
            $headers[] = 'nonce: ' . $uuid;
            $sign = md5('appId='.$pushAppId.'&nonce='.$uuid.'&ts='.$ts.'&appSecret='.$pushAppSecret);
            $headers[] = 'sign: ' . $sign;

            $pushData = array();
            //数据体
            $data = array();
            $data['idUnique']               = $problem->IssueId;
            $data['approvalOpinion']        = $problem->ReasonOfIssueRejecting;
            $data['id']                     = $problem->extId;
            $data['isAfterSubmit']           = $problem->isAfterSubmit;
            $pushData['data'] = $data;
            $object = 'problem';
            $objectType = 'feedback';
            $response = '';
            $status = 'fail';
            $extra = '';

            $result = $this->loadModel('requestlog')->http($url, $pushData, 'PATCH', 'json', array(), $headers);

            $node = $this->dao->select('*')->from(TABLE_REVIEWNODE)
                ->where('objectType')->eq('problem')
                ->andWhere('objectID')->eq($problemID)
                ->andWhere('version')->eq($problem->version)
                ->andWhere('stage')->eq('3')
                ->orderBy('stage,id')
                ->fetch();
//            $result = [
//                "status" => 'success',
//                "code" => 0,
//                "cost" => 2025,
//                "data" => '退回成功',
//                "success" => 1,
//            ];
//            $result = json_encode($result);
            if(!empty($result))
            {
                $resultData = json_decode($result);
                if($resultData->code == '0')
                {
                    $now = date('Y-m-d H:i:s');
                    $isOverTime = 0;
                    if(strtotime($problem->feedbackExpireTime) < strtotime($now)){
                        $isOverTime = 1;
                    }
                    $status = 'success';
                    $isInnovationPassTime = $this->getInnovationPassTime($problem);
                    $this->dao->update(TABLE_PROBLEM)
                        ->set('ReviewStatus')->eq('approvesuccess')
                        ->set('feedbackToHandle')->eq(' ')
                        ->beginIF($problem->firstPushDateFlag == 1)->set('firstPushDate')->eq($now)
                        ->set('ifOverDate')->eq($isOverTime)
                        ->set('firstPushDateFlag')->eq(0)->fi()
                        ->set('firstPush')->eq(1)
                        ->set('status')->eq('closed')
                        ->set('dealUser')->eq(' ')
                        ->beginIF($isInnovationPassTime == false && $problem->isChangeFeedbackTime == 0)
                        ->set('innovationPassTime')->eq(helper::now())->fi()
                        ->set('closedBy')->eq('guestjk')
                        ->set('closedDate')->eq(helper::now())
                        ->beginIF(empty($problem->solvedTime))->set('solvedTime')->eq(helper::now())->fi()
                        ->where('id')->eq($problem->id)->exec();
                    $problemStatus = 'closed';
                    $reviewStatus  = 'approvesuccess';
                    $updateStatus  = 'jxsyncsuccess';
                    $updateComment = $resultData->data;

                    $endnode = $this->dao->select('*')->from(TABLE_REVIEWNODE)
                        ->where('objectType')->eq('problem')
                        ->andWhere('objectID')->eq($problemID)
                        ->andWhere('version')->eq($problem->version)
                        ->andWhere('stage')->eq('4')
                        ->orderBy('stage,id')
                        ->fetch();
                    $this->dao->update(TABLE_REVIEWER)
                        ->set('status')->eq('sendback')
                        ->set('comment')->eq($updateComment)
                        ->set('reviewTime')->eq(helper::now())
                        ->where('node')->eq($endnode->id)
                        ->andWhere('reviewer')->eq('guestjx') //当前审核人
                        ->exec();

                    $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('pass')
                        ->where('id')->eq($endnode->id)
                        ->exec();

                } else {
                    $this->dao->update(TABLE_PROBLEM)
                        ->set('ReviewStatus')->eq('jxsyncfail')
                        ->set('syncFailTimes')->eq(intval($problem->syncFailTimes) + 1)->fi()
                        ->set('feedbackToHandle')->eq($problem->acceptUser)
                        //->set('status')->eq('assigned') //同步失败，不能影响单子的内部状态（主流程状态）
                        ->set('dealUser')->eq($problem->acceptUser)
                        ->where('id')->eq($problem->id)->exec();
                    $problemStatus = $problem->status;// 'assigned';
                    $reviewStatus  = 'jxsyncfail';
                    $updateStatus  = 'jxsyncfail';
                    $updateComment = $resultData->data;
                }

                $response = $result;
            } else {
                $this->dao->update(TABLE_PROBLEM)
                    ->set('ReviewStatus')->eq('jxsyncfail')
                    ->set('syncFailTimes')->eq(intval($problem->syncFailTimes) + 1)->fi()
                    ->set('feedbackToHandle')->eq($problem->acceptUser)
                    ->where('id')->eq($problem->id)->exec();
                $reviewStatus  = 'jxsyncfail';
                $updateStatus = 'jxsyncfail';
                $updateComment = '网络不通';
            }

            $this->dao->update(TABLE_REVIEWER)
                ->set('status')->eq($updateStatus)
                ->set('comment')->eq($updateComment)
                ->set('reviewTime')->eq(helper::now())
                ->where('node')->eq($node->id)
                ->andWhere('reviewer')->eq('guestjk') //当前审核人
                ->exec();

            $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq($updateStatus)
                ->where('id')->eq($node->id)
                ->exec();
            $updateComment = $updateComment == '退回成功' ?$updateComment.": ". $this->lang->problem->backCloseDesc : $updateComment;
            $changes  = common::createChanges($problem, ['status' => $problemStatus, 'ReviewStatus' => $reviewStatus]);
            $actionID = $this->loadModel('action')->create('problem', $problem->id, $updateStatus, $updateComment,'','guestjk');
            if(isset($problemStatus) && !empty($problemStatus) && $problemStatus != $problem->status){
                $this->loadModel('consumed')->record('problem', $problem->id, 0,'guestjk', $problem->status, $problemStatus);
            }
            if($updateStatus != $problem->ReviewStatus){
                $this->loadModel('consumed')->record('problem', $problem->id, '0', $consumedUser, $problem->ReviewStatus, $updateStatus, [],"problemFeedBack");
            }
            if('jxsyncsuccess' == $updateStatus && $updateStatus != $reviewStatus){
                $this->loadModel('consumed')->record('problem', $problem->id, '0', 'guestjk', $updateStatus, $reviewStatus, [],"problemFeedBack");
            }
            $this->action->logHistory($actionID, $changes);

            $this->requestlog->saveRequestLog($url, $object, $objectType, 'POST', $pushData, $response, $status, $extra);
        }
    }


    public function create_guid($namespace = '') {
        static $guid = '';
        $uid = uniqid("", true);
        $data = $namespace;
        $data .= $_SERVER['REQUEST_TIME'] ?? "";
        $data .= $_SERVER['HTTP_USER_AGENT'] ?? "";
        $data .= $_SERVER['LOCAL_ADDR'] ?? "";
        $data .= $_SERVER['LOCAL_PORT'] ?? "";
        $data .= $_SERVER['REMOTE_ADDR'] ?? "";
        $data .= $_SERVER['REMOTE_PORT'] ?? "";
        $hash = strtoupper(hash('ripemd128', $uid . $guid . md5($data)));
        $guid = substr($hash, 0, 8) .
            '-' .
            substr($hash, 8, 4) .
            '-' .
            substr($hash, 12, 4) .
            '-' .
            substr($hash, 16, 4) .
            '-' .
            substr($hash, 20, 12);
        return $guid;
    }

/**
* 公共方法（问题池、需求池）
* 相关信息保存任务问题关联表
* @param $data
* @param $id
* @param $type
* @return mixed
*/
    public function toTaskProblemDemand($data,$id,$type){
        //查询是否存在
        if($type != 'problem') { //同一个问题可能关联多个，不唯一
            $res = $this->dao->select('id')->from(TABLE_TASK_DEMAND_PROBLEM)
                ->where('typeid')->eq($id)->andWhere('deleted')->eq(0)
                ->andWhere('type')->eq($type)
                ->fetchAll();
            //存在删除
            if ($res) {
                $this->dao->update(TABLE_TASK_DEMAND_PROBLEM)
                    ->set('deleted')->eq(1)
                    ->where('typeid')->eq($id)->andWhere('type')->eq($type)->exec();
            }
        }
        //新增
        $task = new stdClass();
        $task->product = $data->product; //产品
        $task->project = $type == 'problem' ? $data->projectPlan : $data->project; //项目
        $task->application = $data->app; //应用系统
        $task->version = $data->productPlan; //产品版本
        $task->execution = isset($data->execution) ? $data->execution : '0' ; //所属阶段
        $task->code = $data->code;//单号
        $task->typeid = $id;//id
        $task->assignTo = trim($data->dealUser,'');//指派给
        $task->type = $type;//类型（问题、需求、二线工单）
        $task->createdDate = date('Y-m-d H:i:s');//创建时间
        $task->taskid = isset($data->taskid) ? $data->taskid : '';
        $this->dao->insert(TABLE_TASK_DEMAND_PROBLEM)->data($task)->autoCheck()->exec();
        $taskID = $this->dao->lastInsertId();
        return $taskID;

    }

    public function toTaskProblemDemandV2($code, $product,$productPlan,$execution, $projectPlan, $project, $dealUser, $app, $id,$type){
        //查询是否存在
        $res = $this->dao->select('id')->from(TABLE_TASK_DEMAND_PROBLEM)
            ->where('typeid')->eq($id)->andWhere('deleted')->eq(0)
            ->andWhere('type')->eq($type)
            ->fetchAll();
        //存在删除
        if($res){
            $this->dao->update(TABLE_TASK_DEMAND_PROBLEM)
                ->set('deleted')->eq(1)
                ->where('typeid')->eq($id)->andWhere('type')->eq($type)->exec();
        }
        //新增
        $task = new stdClass();
        $task->product = $product; //产品
        $task->project = $type == 'problem' ? $projectPlan : $project; //项目
        $task->application = $app; //应用系统
        $task->version = $productPlan; //产品版本
        $task->execution = $execution ?? '0' ; //所属阶段
        $task->code = $code;//单号
        $task->typeid = $id;//id
        $task->assignTo = trim($dealUser,'');//指派给
        $task->type = $type;//类型（问题、需求、二线工单）
        $task->createdDate = date('Y-m-d H:i:s');//创建时间
        $this->dao->insert(TABLE_TASK_DEMAND_PROBLEM)->data($task)->autoCheck()->exec();
        $taskID = $this->dao->lastInsertId();
        return $taskID;

    }

    /**
     * 获取计划阶段
     * @param $projectID
     */
    public function getExecution($projectID){
        $this->loadModel('project');
        $defaults = array('' => '');
        if(!empty($projectID))
        {
            $executions = $this->project->getExecutionByAvailable($projectID);

            if(!empty($executions)) $defaults += $executions;
        }
        return $defaults;
    }

    /**验证多个产品和产品版本是否唯一
     * @param $product
     * @param $plan
     */
    public function checkProductAndPlanOnly($products,$plans){
        $flag = 'success';
        $temp = array();
        $plan = array();
        //拼接处理
        foreach ($products as $key=>$product) {
            $plan[$key] = $plans[$key];
            if(empty($product) || empty($plans[$key])) {
               //不一致，有数据为空
               $flag = 'no';
               return $flag;
            } else {
                //拼接处理
                if($product == '99999' && $plans[$key] != '1'){
                    $flag = 'wu';
                    return $flag;
                }
                $temp[$key] = $product . '-' . $plans[$key];
            }

        }
        $tempCount = count($temp);
        //拼接去重后，比较个数
        if(count(array_unique($temp)) < $tempCount) {
            $flag = 'fail';
        }
        //产品版本只能存在一个无 生成脚本任务
      /*  $res = array_count_values($plan);
        if(isset($res[1]) && $res[1] > 1){
            $flag = 'error';
        }*/
        return $flag;
    }

    /**
     * 返回产品和版本数组
     * @param $products
     * @param $plans
     */
    public function getProductAndPlan($products ,$plans){
        $tmp = array();
        $productArr = array_filter(explode(',',$products));
        $planstArr  = array_filter(explode(',',$plans));

        foreach ($productArr as $key=>$item) {

            $tmpStdclass = new stdClass();
            $tmpStdclass->product = $item;
            $tmpStdclass->plan    = $planstArr[$key];
            $tmpStdclass->productPlan = array('0' => '','1' =>'无') + $this->loadModel('productplan')->getPairs($item);
            $tmp[] = $tmpStdclass;
        }
        return $tmp;
    }

    /**
     * 查询中间表已有数据，返回数组
     * @param $id
     * @param $type
     * @return array
     */
    public function getTaskProblem($id,$type){
        $exists = array();
        $res = $this->dao->select('concat_ws("_",typeid,project,application,product,version) as name,taskid,id,assignTo,product,version')
            ->from(TABLE_TASK_DEMAND_PROBLEM)
            ->where('typeid')->eq($id)
            ->andWhere('deleted')->eq(0)
            ->andWhere('type')->eq($type)
            ->fetchAll();
        if($res){
            foreach ($res as $re) {
                $exists[$re->id]['str'] =  $re->name;
                $exists[$re->id]['assignTo'] =  $re->assignTo;
                $exists[$re->id]['taskid'] =  $re->taskid;
                $exists[$re->id]['id']  =  $re->id;
                $exists[$re->id]['productAndPlan']  =  $re->product.'_'.$re->version.'_'.$re->taskid;
            }
        }
        return $exists;
    }

    /**
     * 获取本次提交的
     * @param $products
     * @param $productPlan
     * @param $data
     * @param $problemID
     * @return array
     */
    public function getNewTaskProblem($products,$productPlan,$data,$problemID){
        $news = array();
        $keysWu = array(); // 产品版本 无
        $keys = array();   // 产品版本不是 无
        foreach ($products as $key => $product) {
            if($productPlan[$key] == '1'){
                $keysWu[] = $product.'_'.$productPlan[$key];
            }else{
                $keys[] = $product.'_'.$productPlan[$key];
            }
            $news[$product.'_'.$productPlan[$key]]['str'] = $problemID.'_'.$data->projectPlan.'_'.$data->app.'_'.$product.'_'.$productPlan[$key];//.'_'.$data->execution;
            $news[$product.'_'.$productPlan[$key]]['assignTo'] = trim($data->dealUser,'');
            $news[$product.'_'.$productPlan[$key]]['id'] = $product.'_'.$productPlan[$key];
        }
        end($keysWu); // 只保留最后一个产品版本 无
        $all = array_merge($keysWu,$keys);
        $nowNews = array();
        foreach ($all as $item) {
            $nowNews[$item] = $news[$item];
        }
        return $nowNews;
    }

    /**
     * 返回新旧中间表的差异
     * @param $old
     * @param $new
     */
    public function getNewOldTaskDiff($old,$new){

        $arrayAdd    = array(); // 新增
        $arrayUpdate = array(); //更新
        $arrayDelete = array(); //删除的
        $oldstr = array_column($old,'str');
        $oldassign = array_column($old,'assignTo');
        $oldObject = array_combine($oldstr,$oldassign);

        $newstr = array_column($new,'str');
        $newassign = array_column($new,'assignTo');
        $newObject = array_combine($newstr,$newassign);

        $oldArray = array_keys($oldObject);//旧数据
        $newArray = array_keys($newObject);//新

        //判断差异
        $diffdelete = array_diff($oldArray,$newArray); //删除的

        //删除的存删除数组
        $oldid = array_column($old,'id');
        $oldidAttr = array_combine($oldstr,$oldid);
        foreach ($diffdelete as $delete) {
            $arrayDelete[$delete] = $oldidAttr[$delete];
        }
        $diffAdd    = array_diff($newArray,$oldArray); //新增的
        //新增的存新增数组
        $newid = array_column($new,'id');
        $newidAttr = array_combine($newstr,$newid);
        foreach ($diffAdd as $add) {
            $arrayAdd[$add] = $newidAttr[$add];
        }
        $intersect  = array_intersect($oldArray,$newArray); //交集
        //交集查看值是否修改，存入更新数组
        $oldid = array_column($old,'id');
        $oldidAttr = array_combine($oldstr,$oldid);
        $oldplan = array_column($old,'productAndPlan');
        $oldplanAttr = array_combine($oldstr,$oldplan);
        foreach ($intersect as $inter) {
            //比较指派给
            if($oldObject[$inter] != $newObject[$inter]){
                $arrayUpdate[$inter]['id'] = $oldidAttr[$inter];
                $arrayUpdate[$inter]['productAndPlan'] = $oldplanAttr[$inter];
            }
        }
        return array('add' => $arrayAdd, 'del' => $arrayDelete,'update' => $arrayUpdate);
    }

    /**
     * 更新问题单任务
     * @param $taskIDs
     * @param $data
     * @param int $flag
     */
    public function updateProblemTask($taskIDs,$data,$flag = 0){
        /** @var taskModel $taskModel */
        $taskModel = $this->loadModel('task');
        $this->app->loadLang('task');
        //删除任务中的问题单号
        $taskfour = new stdClass();
        $apps =  array_flip($this->loadModel('application')->getapplicationNameCodePairs());

        foreach ($taskIDs as $tid) {
            if (!$tid) continue;
            $item = $this->dao->select("*")->from(TABLE_TASK)->where('id')->eq($tid)
                ->andWhere('grade')->eq(2)
                ->andWhere('deleted')->eq(0)
                ->andWhere('dropType')->eq(0)
                ->andWhere('name')->notLike('%已%')
                ->fetch();
            $taskfour->status = $item->status;
            $taskfour->version = $item->version;
            $taskfour->lastEditedDate = $item->lastEditedDate;
            $taskfour->estStarted = $item->estStarted;
            $taskfour->deadline = $item->deadline;
            $taskfour->left =  $item->left;
            $taskfour->estimate = $item->estimate;
            $execution = $this->dao->select("*")->from(TABLE_EXECUTION)->where('id')->eq($item->execution)
                ->andWhere('deleted')->eq(0)
                ->fetch();
            $oldapp = zget($apps,$execution->name,'');//旧应用系统

            $taskProduct = $this->dao->select("name")->from(TABLE_TASK)->where('id')->eq($item->parent)
                ->andWhere('deleted')->eq(0)
                ->fetch();

            $code = strpos($data->code,$this->lang->task->deptname['problem'] ) !== false ? $data->code : $this->lang->task->deptname['problem'] ."_".$data->code ;
            $appName = zget($apps,$data->app);

            //项目 或二线
            $productList = $this->loadModel('product')->getCodeNamePairs();//产品
            $product = zget($productList,$data->product,'');
            $productversion = $data->productPlan;//产品版本
            $productplanlist      = array('0' => '') + $this->loadModel('productplan')->getPairs($data->product);
            $productVersionName = zget($productplanlist,$productversion,'');//产品版本
            if($data->product != '99999' && $productversion != '1'){
                $taskName  = $product.'_'.$productVersionName;
            }elseif($data->product != '99999' && $productversion == '1'){
                $taskName  = $product;
            }elseif($data->product == '99999' && $productversion == '1'){
                $taskName  = $appName;
            }

                //单号一致 项目不一致 更新任务名，新增 不纳入本项目后缀
            if($flag){
                $name = $item->name;
                $taskModel->assignededitTaskObject($item->assignedTo, $name, $item->execution, $item, $item->id, 0, $this->lang->task->sourceType['problem'] , $data);
                $id = $this->dao->select("id")->from(TABLE_TASK_DEMAND_PROBLEM)->where('taskid')->eq($item->id)->fetch();
                $this->dao->update(TABLE_TASK_DEMAND_PROBLEM)->set('deleted')->eq('1')->where('id')->eq($id->id)->exec();
            }else{
                if($item->project != $data->projectPlan){
                    $name = $item->name."(".$this->lang->task->taskNoProject.")";
                    $taskModel->assignededitTaskObject($item->assignedTo, $name, $item->execution, $item, $item->id, 0, $this->lang->task->sourceType['problem'] , $data);
                }elseif($oldapp != $data->app){
                    //单号一致 系统不一致 更新任务名，新增 已不属于本系统
                    $name = $item->name."(".$this->lang->task->taskNoApp .")";
                    $taskModel->assignededitTaskObject($item->assignedTo, $name, $item->execution, $item, $item->id, 0, $this->lang->task->sourceType['problem'] , $data);
                }else {
                    if($this->lang->task->sourceType['problem'] == 'deptorder'){
                        $taskModel->assignededitTaskObject($item->assignedTo, $code, $item->execution, $item, $item->id, 0, $this->lang->task->sourceType['problem'] , $data);
                    }else if($taskName != $taskProduct->name){
                        $name = $code."(".$this->lang->task->taskNoProduct .")";
                        $taskModel->assignededitTaskObject($item->assignedTo, $name, $item->execution, $item, $item->id, 0, $this->lang->task->sourceType['problem'] , $data);
                    }else{
                        $taskModel->assignededitTaskObject($item->assignedTo, $code, $item->execution, $item, $item->id, 0, $this->lang->task->sourceType['problem'] , $data);
                    }
                }
            }



           /* if($flag){
                $nowname = $item->name;
                $taskfour->dropType = '0';
            }else{
                if(strpos($item->name,'V') !== false || strpos($item->name,'.') !== false){
                     $nowname = trim(str_replace(',,', ',', str_replace('[,', '[', str_replace(',]', ']', str_replace($data->code, '', $item->name)))), ',');
                     $assigned = $this->dao->select('assignTo')->from(TABLE_TASK_DEMAND_PROBLEM)
                         ->where('taskid')->eq($item->id)
                         ->andWhere('deleted')->eq('0')
                         ->andWhere('code')->ne($data->code)
                         ->fetchAll();
                     $item->assignedTo = array_column($assigned, 'assignTo');
                     if (strpos($nowname, '[]') !== false) {
                         $nowname = str_replace('[]', '[废弃]', $nowname);
                         $taskfour->dropType = '1';
                     } else {
                         $taskfour->dropType = '0';
                     }
                 }else{
                     $nowname =  $item->name.'[废弃]';
                     $taskfour->dropType = '1';
                 }
            }
            //查询是否团队
            $team = $this->dao->select("*")->from(TABLE_TEAM)->where('root')->eq($tid)->andWhere('type')->eq('task')->fetchAll();
            $assignedTo = is_array($item->assignedTo) ? $item->assignedTo : array($item->assignedTo);
            if (count($team) > 0) {
                $taskModel->editTaskObject($assignedTo, $nowname, $item->execution, $taskfour, $item->id, 1, 'yf', $data);
            } else {
                $taskModel->editTaskObject($assignedTo, $nowname, $item->execution, $taskfour, $item->id, 0, 'yf', $data, true);
            }*/
        }
    }

    /**
     * 问题单生成任务前存中间表
     * @param $product_plan
     * @param $data
     * @param $oldProblem
     * @param $problemID
     * @param int $flag
     */
    public function insertTaskToProblem($product_plan,$data,$oldProblem,$problemID,$flag = 0){

        /** @var problemModel $problemModel */
        $problemModel = $this->loadModel('problem');
        foreach ($product_plan as $item) {
            $all = explode('_',$item);
            $product = $all[0];
            $productPlanArr = $all[1];
            $taskData = new stdClass();
            foreach ($data as $k => $v)
            {
                $taskData->$k = $v;
            }
            $_POST['product'] = $taskData->product = $product;   //改为单个，用于兼容
            $_POST['productPlan'] = $taskData->productPlan = $productPlanArr; //同上
            $_POST['app'] = $oldProblem->app;
            if($flag){
                $taskData->taskid = $all[2];
            }
            $problemModel->toTaskProblemDemand($taskData,$problemID,'problem'); //新增关联表
        }
    }

    /**
     * 查询所有问题单号（除当前单子）
     * @param $problemID
     */
    public function getAllCode($problemID){

        $all = $this->dao->select('id,concat_ws("(",code,abstract) code')->from(TABLE_PROBLEM)
            ->where('status')->ne('deleted')
            ->andWhere('id')->ne($problemID)
            ->orderBy('id desc')
            ->fetchPairs();
        foreach ($all as $key => $item) {
            if(strpos($item,'(') !== false){
                $all[$key] = $item.')';
            }
        }
        return $all;
    }

    /**
     * 查询问题单号
     * @param $problemID
     */
    public function getCodes($problemIDs){

        $all = $this->dao->select('code')->from(TABLE_PROBLEM)
            ->where('status')->ne('deleted')
            ->andWhere('id')->in($problemIDs)
            ->orderBy('id desc')
            ->fetchAll();
        $all = $all ? implode(',',array_column($all,'code')) : '';
        return $all;
    }
    /**
     *  生成多任务
     * @param $data
     * @param $oldProblem
     * @param $problemID
     */
    public function createProblemManyTask($data,$oldProblem,$problemID){
        //待开发且是其他问题,项目管理中创建任务
        if($data->status == 'feedbacked' and $data->type != 'noproblem' && $this->post->type != 'repeat'){
            $data->code = $oldProblem->code;
            /** @var problemModel $problemModel */
            $problemModel = $this->loadModel('problem');
            /** @var taskModel $taskModel */
            $taskModel = $this->loadModel('task');
            #region 最新生成任务逻辑
            //查询中间表,返回包含此单号数据
            $oldproblem = $problemModel->getTaskProblem($problemID,'problem');
            //获取本次提交的
            $productArr = explode(',', $data->product);
            $productPlanArr = explode(',', $data->productPlan);
            $newproblem = $problemModel->getNewTaskProblem($productArr,$productPlanArr,$data,$problemID);
            $dealArr = $problemModel->getNewOldTaskDiff($oldproblem,$newproblem);
            if($dealArr['del'] ||$dealArr['update']){
                $data->id = $problemID;
                $data->mailto = isset($data->mailto) ? $data->mailto : [];
                if($dealArr['del']){
                    $deleteids = array_values($dealArr['del']);
                    //删除中间关联表
                    $this->dao->update(TABLE_TASK_DEMAND_PROBLEM)->set('deleted')->eq(1)->where('id')->in($deleteids)->exec();
                    $taskIDs = $this->dao->select('taskid')->from(TABLE_TASK_DEMAND_PROBLEM)->where('id')->in($deleteids)->fetchAll();
                    $problemModel->updateProblemTask(array_column($taskIDs,'taskid'),$data,0);
                }
                if($dealArr['update']){
                    $updateids = array_column($dealArr['update'],'id' );
                    //删除中间关联表
                    $this->dao->update(TABLE_TASK_DEMAND_PROBLEM)->set('deleted')->eq(1)->where('id')->in($updateids)->exec();
                    $product_plan = array_column($dealArr['update'],'productAndPlan' );
                    $problemModel->insertTaskToProblem($product_plan,$data,$oldProblem,$problemID,1);
                    $taskIDs = $this->dao->select('taskid')->from(TABLE_TASK_DEMAND_PROBLEM)->where('id')->in($updateids)->fetchAll();
                    $problemModel->updateProblemTask(array_column($taskIDs,'taskid'),$data,1);
                }
                // $this->dao->update(TABLE_TASK_DEMAND_PROBLEM)->set('deleted')->eq(1)->where('id')->in($haveId)->exec();
            }

            if($dealArr['add'] ){
                $product_plan = array_values($dealArr['add'] );
                foreach ($product_plan as $item) {
                    $all = explode('_',$item);
                    $product = $all[0];
                    $productPlanArr = $all[1];
                    $taskData = new stdClass();
                    foreach ($data as $k => $v)
                    {
                        $taskData->$k = $v;
                    }
                    $_POST['product'] = $taskData->product = $product;   //改为单个，用于兼容
                    $_POST['productPlan'] = $taskData->productPlan = $productPlanArr; //同上
                    $_POST['app'] = $oldProblem->app;
                    $_POST['PlannedDateOfChange'] = $oldProblem->PlannedDateOfChange;
                    $_POST['createdBy'] = $oldProblem->createdBy;
                    $_POST['PlannedTimeOfChange'] = $oldProblem->PlannedTimeOfChange;
                    $taskID = $problemModel->toTaskProblemDemand($taskData,$problemID,'problem'); //新增关联表
                    if($taskID){
                        $taskData->id = $problemID;
                        $taskData->createdBy =  $oldProblem->createdBy;
                        $taskData->mailto = isset($data->mailto) ? $data->mailto : [];
                        $this->dao->update(TABLE_TASK_DEMAND_PROBLEM)->set('application')->eq($taskData->app)->where('id')->eq($taskID)->exec();
                       // $taskModel->checkStageAndTask($taskData->projectPlan, $taskData->app,'project',$taskData,1);//创建任务

                        $taskModel->assignedAutoCreateStageTask($data->projectPlan,'problem',$taskData->app,$data->code,$taskData);
                    }
                }
            }
            //只修改了计划结束时间
            if(empty($dealArr['add']) && empty($dealArr['del'] ) && empty($dealArr['update'])){
                if(isset($data->PlannedDateOfChange) || isset($data->PlannedTimeOfChange)){
                    $updateids = array_column($oldproblem,'id' );
                    //删除关联中间表
                    $this->dao->update(TABLE_TASK_DEMAND_PROBLEM)->set('deleted')->eq(1)->where('id')->in($updateids)->exec();
                    $product_plan = array_column($oldproblem,'productAndPlan' );
                    $problemModel->insertTaskToProblem($product_plan,$data,$oldProblem,$problemID,1);
                    $taskIDs = $this->dao->select('taskid')->from(TABLE_TASK_DEMAND_PROBLEM)->where('id')->in($updateids)->fetchAll();
                    $problemModel->updateProblemTask(array_column($taskIDs,'taskid'),$data,1);
                }
            }
            #endregion
        }
    }

    /**
     * 二线单子解决时间同步problem
     * @param $problemId
     * @param $solvedTimeAboutSecondLine
     */
    public function updateProblemSolvedTime($problemId,$solvedTimeAboutSecondLine){
        $problemIdArr = array_filter(explode(',',$problemId));
        if($problemIdArr){
            foreach ($problemIdArr as $problemId){
                $problemInfo = $this->loadModel('problem')->getByID($problemId);
                $solvedTime = $problemInfo ? $problemInfo->solvedTime : '';
                if($solvedTimeAboutSecondLine > $solvedTime){
                    $this->dao->update(TABLE_PROBLEM)->set('solvedTime')->eq($solvedTimeAboutSecondLine)->where('id')->eq($problemId)->exec();
                }
            }
        }
    }

    /**
     * 更新内部反馈是否超时字段
     * @throws Exception
     */
    public function updateifOverDateInside(){
        $problems =  $this->dao->select('id, status, code, actualOnlineDate, dealUser,createdBy,dealAssigned,dealFeedbackPass,ifOverDateInside')
            ->from(TABLE_PROBLEM)
            ->where('status')->notIN("confirmed,deleted") // 待分配、已删除不做联动
            ->fetchAll('id');
        $arr = array();
        foreach ($problems as $problem) {
            if($problem->createdBy == 'guestjx'){
                $jx = 1;
            }else if($problem->createdBy == 'guestcn'){
                $jx = 0;
            }else{
                continue;
            }
            $dealAssignDate = ( $problem->dealAssigned == '0000-00-00 00:00:00' || empty( $problem->dealAssigned)) ? '0000-00-00 00:00:00' : $problem->dealAssigned; // 待分析处理时间
            $dealFeedbackPass = ($problem->dealFeedbackPass == '0000-00-00 00:00:00' || empty($problem->dealFeedbackPass)) ? helper::today() : $problem->dealFeedbackPass; // 反馈单部门 通过时间
            //金信15 自然日 
            $diff = $dealAssignDate == '0000-00-00 00:00:00'  ? 0 : helper::diffDate3($dealFeedbackPass,$dealAssignDate);
            
            if($jx){ 
                $ifOverDateInside = $diff <= $this->lang->problem->expireDaysList['jxExpireDays'] ? 0 : 1;
            }else{
                //清总 3 工作日              
                $end = $dealAssignDate == '0000-00-00 00:00:00'  ? '0000-00-00 00:00:00' : helper::getTrueWorkDay($dealAssignDate,$this->lang->problem->expireDaysList['days'],true);                
                $ifOverDateInside  = helper::diffDate2(date('Y-m-d',strtotime($end)),$dealFeedbackPass) > 1 ? 1 : 0;
            }
            // 更新
            if($problem->ifOverDateInside != $ifOverDateInside){
                $this->dao->update(TABLE_PROBLEM)->set('ifOverDateInside')->eq($ifOverDateInside)->where('id')->eq($problem->id)->exec();
                $arr[$problem->id] = 'old: '.$problem->ifOverDateInside.';'.'new: '.$ifOverDateInside;
            }
        }
        return $arr;
    }

    /**
     * 查询问题单所有二线
     * @param $problemID
     * @param $type
     * @return array
     */
    public function getAllSecond($problemID,$type)
    {
        $flag   = false;
        $gainArr   = array(); //金信数据获取
        $modifyArr = array(); //金信生产变更
        $gainQzArr = array(); //清总数据获取
        $outwarddeliveryArr = array(); //清总对外交付
        $creditArr = array(); //征信交付
        //查询问题单二线
        $relations = $this->dao->select('relationID as last_relation_id, relationType')
            ->from(TABLE_SECONDLINE)
            ->where('objectType')->eq($type)
            ->andwhere('objectID')->eq($problemID)
            ->andwhere('deleted')->eq(0)
//            ->andwhere('relationType')->in('modify,gain,gainQz,outwardDelivery')
            ->beginIF($type == 'demand')->andWhere('relationType')->in('modify,outwardDelivery,credit')->fi()
            ->beginIF($type == 'problem')->andwhere('relationType')->in('modify,gain,gainQz,outwardDelivery,credit')->fi()
            ->orderBY("id_asc")
            ->fetchAll();
        $count = 0;
        foreach ($relations as $relation) {
            if (empty($relation)) continue;
            #region 二线金信
            if (in_array($relation->relationType, ['gain'])) { //如果是金信数据获取
                $info = $this->dao->select('id')
                    ->from(TABLE_INFO)
                    ->where('id')->eq($relation->last_relation_id)
                    ->andwhere('action')->in('gain')
                    ->andwhere('status')->notin(['deleted', 'waitsubmitted']) //已关闭 已删除 待提交不做联动
                    ->fetch();
                if (empty($info)) continue;
                $gainArr[] = $info->id;
                $count ++;

            } elseif ($relation->relationType == 'modify') { //如果是金信生产变更
                $modify = $this->dao->select('id, status, externalCode')
                    ->from(TABLE_MODIFY)
                    ->where('id')->eq($relation->last_relation_id)
                    ->andWhere('status')->notin(["deleted", 'waitsubmitted']) //已删除 待提交不做联动
                    ->fetch();
                //内部变更取消不参与联动
                /*内部状态不参与交付时间修改，只有交付出去才参与
                待关联版本、待组长审批、待本部门审批、待系统部审批、待产品经理审批、待分管领导审批、待总经理审批、待产创部审核
                */
                $noChangeTimeStatus = ['modifycancel','wait','cmconfirmed','groupsuccess','managersuccess','systemsuccess','posuccess','leadersuccess','gmsuccess'];

                if (empty($modify) || (in_array($modify->status,$noChangeTimeStatus) && empty($modify->externalCode))) continue;
                $modifyArr[] = $modify->id;
                $count ++;
            }
            #endregion
            #region 二线清总
            elseif ($relation->relationType == 'gainQz') {  //清总数据获取
                $gainQz = $this->dao->select('id')
                    ->from(TABLE_INFO_QZ)
                    ->where('id')->eq($relation->last_relation_id)
                    ->andwhere('action')->eq('gain')
                    ->andwhere('status')->notin(["deleted", 'waitsubmitted']) //已关闭 数据获取关闭 数据获取取消 已删除 待提交 不做联动
                    ->fetch();
                if (empty($gainQz)) continue;
                $gainQzArr[] = $gainQz->id;
                $count ++;
            } elseif (strtolower($relation->relationType) == 'outwarddelivery') { //清总对外交付
                $outwarddelivery = $this->dao->select('id,modifycnccId,isNewModifycncc,status')
                    ->from(TABLE_OUTWARDDELIVERY)
                    ->where('id')->eq($relation->last_relation_id)
                    ->andWhere('deleted')->eq(0)
                    ->andWhere('status')->notin(['waitsubmitted'])
                    ->fetch();
                if (empty($outwarddelivery)) continue;
                if ($outwarddelivery->modifycnccId > 0) { //对外交付只处理生产变更
                    //内部状态不参与交付时间修改，只有交付出去才参与
                    $noChangeTimeStatus = ['cancle','wait','cmconfirmed','reviewfailed','groupsuccess','managersuccess','systemsuccess','posuccess','leadersuccess','gmsuccess'];
                    if(1 == $outwarddelivery->isNewModifycncc && in_array($outwarddelivery->status,$noChangeTimeStatus)) continue;
                    $outwarddeliveryArr[] = $outwarddelivery->id;
                    $count ++;
                }
            }elseif ($relation->relationType == 'credit') { //如果是征信交付
                $creditInfo = $this->dao->select('id, status, deliveryTime')
                    ->from(TABLE_CREDIT)
                    ->where('id')->eq($relation->last_relation_id)
                    ->andWhere('deleted')->eq('0') //已删除
                    ->fetch();
                if($creditInfo){
                    $creditArr[] = $creditInfo->id;
                    $count ++;
                }

            }
            #endregion
        }
        return ['info' => $gainArr,'modify' => $modifyArr,'infoQz' => $gainQzArr,'outwarddelivery' => $outwarddeliveryArr, 'credit' => $creditArr, 'count' => $count ];
    }

    /**
     * 获取二线审批节点是否审核通过，并返回前一个节点最大时间
     */
    public function getOneSecondWhertherPass($type,$sids)
    {
        $solveTime  = array();
        $hasTimeArr = array();
        if($type == 'credit'){
            foreach ($sids as $sid){
                if(!$sid){
                    continue;
                }
                $creditInfo = $this->dao->select('id, status, deliveryTime,isMakeAmends,actualDeliveryTime')
                    ->from(TABLE_CREDIT)
                    ->where('id')->eq($sid)
                    ->andWhere('deleted')->eq('0') //已删除
                    ->fetch();
                if($creditInfo){
                    $solveTime[$type.'_'.$sid] = $creditInfo->isMakeAmends == 'yes' ? strtotime($creditInfo->actualDeliveryTime) : strtotime($creditInfo->deliveryTime);
                    $hasTimeArr[] = $sid;
                }
            }
        }else{
            foreach ($sids as $sid)
            {
                if(!$sid) continue;

                $info = [];
                if ($type == 'modify'){
                    $info = $this->dao->select('id,isMakeAmends,actualDeliveryTime')->from(TABLE_MODIFY)
                        ->where('id')->eq($sid)
                        ->andWhere('status')->ne('deleted')
                        ->fetch();
                }
                if ($type == 'outwarddelivery'){
                    $info = $this->dao->select('t1.id,t2.isMakeAmends,t2.actualDeliveryTime')->from(TABLE_OUTWARDDELIVERY)->alias("t1")
                        ->leftjoin(TABLE_MODIFYCNCC)->alias("t2")
                        ->on('t1.modifycnccId = t2.id')
                        ->where('t1.id')->eq($sid)
                        ->andWhere('t1.deleted')->eq('0')
                        ->fetch();
                }

                if (!empty($info) && $info->isMakeAmends == 'yes'){
                    $solveTime[$type.'_'.$sid] = strtotime($info->actualDeliveryTime);
                    $hasTimeArr[] = $sid;
                }else{
                    //查询审批节点和记录
                    $second = $this->dao
                        //                ->select('zr.id,zr.node,zr.reviewer,zr.reviewTime,node.status,node.stage,node.version,concat(zr.id,"_",node.stage) as idstage')
                        ->select('zr.id,zr.node,zr.reviewer,zr.reviewTime,node.status,node.stage,node.version,concat(node.stage,"_",zr.id) as idstage')
                        ->from(TABLE_REVIEWER)->alias('zr')
                        ->leftJoin(TABLE_REVIEWNODE)->alias('node')
                        ->on("zr.node = node.id")
                        ->where('node.objectType')->eq($type)
                        ->andWhere('node.objectID')->eq($sid)
                        ->andWhere("node.version = (select max(version) from zt_reviewnode  where  objectType = '$type' and objectID = '$sid')" )
                        ->fetchAll('idstage');
                    $secondIds = array_keys($second);

                    foreach ($second as $key => $item)
                    {
                        //二线审批 通过
                        if(($type == 'info' || $type == 'modify' ||$type == 'outwarddelivery') && $item->stage == '8' && $item->status == 'pass')
                        {
                            //获取前一个节点 最大时间
                            $solveTime[$type.'_'.$sid] = strtotime($this->getMaxReviewTime($secondIds,$item,$second,$type, $sid));
                            $hasTimeArr[] = $sid;
                        }
                        else if(($type == 'infoQz' && $item->stage == '7') && $item->status == 'pass')
                        {
                            //获取前一个节点 最大时间
                            $solveTime[$type.'_'.$sid] = strtotime($this->getMaxReviewTime($secondIds,$item,$second, $type, $sid));
                            $hasTimeArr[] = $sid;
                        }

                    }
                }
            }
        }
        return array('solve' => $solveTime,'hasTime' => $hasTimeArr);
    }

    /**
     * 获取前一个节点最大时间
     * @param $secondIds
     * @param $item
     * @param $second
     * @return string
     */
    public function getMaxReviewTime($secondIds,$item,$second, $type = false, $objectID = 0)
    {
        $solvedTime = '';
        arsort($secondIds);
        foreach ($secondIds as $secondId) {
            $i = $item->stage - 1;//去掉二线节点
            for ($i; $i > 0; $i--){
                $secondIdArr = explode('_',$secondId);
                $stage = current($secondIdArr);
                //前面的节点一致且pass (可能会出现前一个节点跳过，故一直获取到未跳过且pass节点最大时间)
                if($i == $stage && $second[$secondId]->status == 'pass'){
                    $nodeId = $second[$secondId]->node;
                    $reviewers = $this->dao->select('id,status,reviewTime,reviewer')->from(TABLE_REVIEWER)->where('node')->eq($nodeId)->fetchAll();
                    $reviewTime = array_column($reviewers,'reviewTime');
                    $solvedTime = max($reviewTime);
                    //审批节点时间为空，查询历史记录时间
                    if(empty($solvedTime) && $type){
                        $actions = $this->dao->select('*')->from(TABLE_ACTION)
                            ->where('objectType')->eq($type)
                            ->andWhere('action')->eq('review')
                            ->andWhere('objectID')->eq($objectID)
                            ->andWhere('actor')->in(array_column($reviewers,'reviewer'))
                            ->fetchAll();
                        if($actions){
                            $solvedTime = max(array_column($actions,'date'));
                        }
                    }
                    break;
                }
            }
            if($solvedTime) break;
        }
        return $solvedTime;
    }

    /**
     * 获取所有二线前一个节点的时间（取最大）
     */
    public function getAllSecondSolveTime($problemID,$type)
    {
        //所有二线
        $allSecond =  $this->getAllSecond($problemID,$type);

        $allSolveTime = array();
        $hasTime      = array();
        foreach ($allSecond as $key => $item)
        {
            if($key == 'count') continue;
            if(!$item) continue;
            $secondType = $key;
            //获取每一个二线解决时间
           $allSolve       = $this->getOneSecondWhertherPass($secondType, $item);
           $allSolveTime[] = $allSolve['solve'];//解决时间
           $hasTime[$key]  = $allSolve['hasTime'];//所有有解决时间的二线

        }
        $newArr = array();
        foreach ($allSolveTime as $alls)
        {
            foreach ($alls as $key =>$all) {
                $newArr[$key] = $all;
            }
        }

        //所有二线全部审批通过
        $flag = false; //是否关闭
        if(($allSecond['count'] == count($newArr)) && count($newArr) != 0)
        {
            $solveTime = max($newArr);//获取多个二线前一个节点时间中最大的
            $maxTypeID = explode('_',array_search(max($newArr),$newArr));
            $maxType   = current($maxTypeID);
            $maxID     = end($maxTypeID);
        }
        else
        {
            //未全部通过,查询单子是否关闭,关闭取关闭时间.反之,置空,问题单查看是否有待关闭时间，没有取关闭时间，置空
            if($type == 'problem'){
                $consumed = $this->dao
                    ->select('objectID,max(createdDate) as createdDate')
                    ->from(TABLE_CONSUMED)
                    ->where('objectType')->eq('problem')
                    ->andWhere('objectID')->eq($problemID)
                    ->andWhere('after')->eq('toclose')
                    ->groupBy('objectID')
                    ->fetch();
            }
            $closeTime = $this->dao->select('closedDate')->from($this->lang->problem->dealTable[$type])
                ->where('id')->eq($problemID)
                ->fetch();
            if(isset($consumed) && !empty($consumed)){
                $flag = $toClose = true;
                $solveTime = strtotime($consumed->createdDate);
            }elseif($closeTime->closedDate && strpos($closeTime->closedDate, '0000-00-00') === false)
            {
                $flag = true;
                $solveTime = strtotime($closeTime->closedDate); //关闭时间
            }else{
                $solveTime = '';//置空
            }
        }
        //处理以下逻辑前检查表中的解决时间和本次是否一致，不一致再处理
        $oldSolveTime = $this->dao->select('solvedTime,secureStatusLinkage')->from($this->lang->problem->dealTable[$type])
            ->where('id')->in($problemID)
            ->fetch();

        if($oldSolveTime->solvedTime == '0000-00-00 00:00:00' || strpos($oldSolveTime->solvedTime,'1970-01-01') !== false ){
            $oldSolveTime->solvedTime = '';
        }else{
            $oldSolveTime->solvedTime = strtotime($oldSolveTime->solvedTime);
        }
        if( $oldSolveTime->solvedTime != $solveTime){
            //获取所有二线和有时间二线的差异,用于回填历史记录备注
            $diff = array();
            foreach($allSecond as $key => $all)
            {
                if($key == 'count') continue;
                if(!$all) continue;
                $diff[$key] = array_diff($all,$hasTime[$key]);
            }
            $diff = array_filter($diff);

            //根据差异查询具体单号,并生成备注
            $codes = array();
            foreach ($diff as $key => $item) {
                if(!$item) continue;
                $code = $this->dao->select('code')->from($this->lang->problem->secondTable[$key])
                    ->where('id')->in($item)
                    ->fetch();
                $codes[] = isset($code->code) ? $code->code : '';
            }
            //$comment = implode(',',array_filter($codes));//备注code
            if(isset($toClose)){
                $comment = sprintf($this->lang->problem->solveTimeToColseTip,$this->lang->problem->typeName[$type],$this->lang->problem->timeDesc[$type]);
            }elseif($flag) {
                //未全部通过,单子关闭
                $comment = sprintf($this->lang->problem->solveTimeTip,$this->lang->problem->typeName[$type],$this->lang->problem->timeDesc[$type]);
            } elseif((empty($solveTime) || strpos($solveTime, '0000-00-00') !== false) && $oldSolveTime->solvedTime > 0) {
                //二线单内部取消
                $comment = sprintf($this->lang->problem->solveTimeCancelTip,$this->lang->problem->timeDesc[$type]);
            }else {
                if($codes) {
                    //未全部通过,单子未关闭
                    $comment = sprintf($this->lang->problem->solveTimeNoColseTip,$this->lang->problem->typeName[$type],$this->lang->problem->timeDesc[$type]);
                } else {
                    $code = $this->dao->select('code')->from($this->lang->problem->secondTable[$maxType])
                        ->where('id')->in($maxID)
                        ->fetch();
                    $comment = sprintf($this->lang->problem->solveTimeColseTip,$code->code,$this->lang->problem->timeDesc[$type]);
                }
            }

            //更新解决时间
            $solveTime = !empty($solveTime) && strpos($solveTime, '0000-00-00') === false ? date('Y-m-d H:i:s',$solveTime) : '';
            if($oldSolveTime->secureStatusLinkage == '0'){
               $this->dao->update($this->lang->problem->dealTable[$type])->set('solvedTime')->eq($solveTime)
                    ->where('id')->eq($problemID)
                    ->exec();
               $this->loadModel('action')->create($type, $problemID, 'updatesolvetime', $comment,'','guestjk');
            }

        }
    }

    /**
     * 新建二线，则关联单解决时间置空
     * @param $ID
     * @param $type
     * @param $code
     * @param bool $flag
     */
    public function dealSolveTime($ID,$type,$code,$flag = false){
        $ids = array_filter(explode(',',$ID));
        if($ids)
        {
           foreach($ids as $item)
           {
               $oldSolveTime = $this->dao->select('solvedTime,closedDate,secureStatusLinkage')->from($this->lang->problem->dealTable[$type])
                    ->where('id')->eq($item)
                    ->fetch();
                // 新建或复制二线 查看问题或需求单是否关闭,问题单如果有待关闭取待关闭时间
               if($type == 'problem'){
                    $consumed = $this->dao
                        ->select('objectID,max(createdDate) as createdDate')
                        ->from(TABLE_CONSUMED)
                        ->where('objectType')->eq('problem')
                        ->andWhere('objectID')->eq($item)
                        ->andWhere('after')->eq('toclose')
                        ->groupBy('objectID')
                        ->fetch();
               }
               if(isset($consumed) && !empty($consumed) && $oldSolveTime->secureStatusLinkage == '0'){
                   $this->dao->update($this->lang->problem->dealTable[$type])
                       ->set('solvedTime')->eq($consumed->createdDate)
                       ->where('id')->eq($item)
                       ->exec();
                   $comment = sprintf($this->lang->problem->solveTimeToColseTip,$code,$this->lang->problem->timeDesc[$type]);
                   $this->loadModel('action')->create($type, $item, 'updatesolvetime', $comment,'','guestjk');
               }elseif(strpos($oldSolveTime->closedDate,  '0000-00-00') === false && !empty($oldSolveTime->closedDate) && $oldSolveTime->secureStatusLinkage == '0')
                {
                    $solvedTime = $oldSolveTime->closedDate; //解决时间取关闭时间
                    $this->dao->update($this->lang->problem->dealTable[$type])->set('solvedTime')->eq($solvedTime)
                            ->where('id')->eq($item)
                            ->exec();
                    $comment = sprintf($this->lang->problem->solveTimeTip,$code,$this->lang->problem->timeDesc[$type]);
                    $this->loadModel('action')->create($type, $item, 'updatesolvetime', $comment,'','guestjk');
                }
                else
                {
                    //有解决时间，置空
                    if(strpos($oldSolveTime->solvedTime,'0000-00-00') === false  && !empty($oldSolveTime->solvedTime) && $oldSolveTime->secureStatusLinkage == '0')
                    {
                        $this->dao->update($this->lang->problem->dealTable[$type])->set('solvedTime')->eq('0000-00-00')
                            ->where('id')->eq($item)
                            ->exec();
                        $desc = $flag ? $this->lang->problem->solveTimeRejectTip : $this->lang->problem->solveTimeNewTip;
                        $comment = sprintf($desc,$code,$this->lang->problem->timeDesc[$type]);
                        $this->loadModel('action')->create($type, $item, 'updatesolvetime', $comment,'','guestjk');
                    }
                }

           }
        }
    }
    //喧喧发信
    public function getXuanxuanTargetUser($obj,$objectType, $objectID, $actionType, $actionID, $actor = '')
    {
        $problem  = $obj;
        /* Get action info. */
        $action          = $this->loadModel('action')->getById($actionID);
        $history         = $this->action->getHistory($actionID);
        $action->history = isset($history[$actionID]) ? $history[$actionID] : array();

        //重发机制-不发邮件
        if($action->action == 'syncfail' && $problem->syncFailTimes > 1){
            return ;
        }

        if($action->action == 'review' || $action->action == 'createfeedback')
        {
            $sendUsers = $this->getToAndCcListFeedBack($problem);
        }else if($action->action == 'syncstatus' || $action->action == 'syncfail') {
            $sendUsers =  array($problem->acceptUser, array());
        }else{
            $sendUsers = $this->getToAndCcList($problem);
        }
        if(!$sendUsers) return;
        list($toList, $ccList) = $sendUsers;

        if($problem->status == 'closed') {
            $toList = $problem->createdBy;
        }
        /* Send emails. */
        $status = array('confirmed','assigned', 'toclose','closed'); //20220930 待分配和待分析 或待开发且不是问题发邮件,其他都不发
        if(in_array($problem->status,$status) || ($problem->status == 'feedbacked' && $problem->type == 'noproblem')){
            $toList = $toList;
        }else{
            $toList = '';
        }
        $mailConf   = isset($this->config->global->setProblemMail) ? $this->config->global->setProblemMail : '{"mailTitle":"","variables":[],"mailContent":""}';

        if($action->action == 'problemdelay' || $action->action == 'reviewdelay'){
            $delayInfo = $this->dao
                ->select('originalResolutionDate,delayResolutionDate,delayReason,delayStatus,delayVersion,delayStage,delayDealUser,delayUser,delayDate')
                ->from(TABLE_DELAY)
                ->where('objectType')->eq('problem')
                ->andWhere('objectId')->eq($problem->id)
                ->fetch();
            foreach ($delayInfo as $key => $value){
                $problem->$key = $value;
            }
            if($problem->delayStatus == 'success' || $problem->delayStatus == 'fail'){
                //萱萱发送通知邮件，需要设置session
                $this->session->set('isSendXuanxuan', true);
                $toList = $problem->delayUser;
                if($problem->delayStatus == 'success'){
                    $user = $this->loadModel('user')->getUserInfo($problem->delayUser);
                    $myDept = $this->loadModel('dept')->getByID($user->dept);
                    $toList = $toList.','.trim($myDept->executive, ',');
                }
                $mailConf = json_decode($mailConf);
                $mailConf->mailTitle = $this->lang->problem->delayMaile;
                $mailConf->variables = [];
                $mailConf->mailContent = $this->lang->problem->delayContentMaile;
                $mailConf = json_encode($mailConf);
            }else{
                $toList = $problem->delayDealUser;
            }
        }

        //变更
        if($action->action == 'problemchange' || $action->action == 'problemreviewchange'){
            $changeInfo = $this->dao
                ->select('changeOriginalResolutionDate,changeResolutionDate,changeReason,changeStatus,changeVersion,changeStage,changeDealUser,changeUser,changeDate,changeContent')
                ->from(TABLE_PROBLEM_CHANGE)
                ->where('objectType')->eq('problem')
                ->andWhere('objectId')->eq($problem->id)
                ->orderBy('id_desc')
                ->limit(1)
                ->fetch();
            foreach ($changeInfo as $key => $value){
                $problem->$key = $value;
            }
            //萱萱发送通知邮件，需要设置session
            $this->session->set('isSendXuanxuan', true);
            if($problem->changeStatus == 'success' || $problem->changeStatus == 'fail'){
                $toList = $problem->changeUser;
                if($problem->changeStatus == 'success'){
                    $user = $this->loadModel('user')->getUserInfo($problem->changeUser);
                    $myDept = $this->loadModel('dept')->getByID($user->dept);
                    $toList = $toList;//.','.trim($myDept->executive, ',');
                }
                $mailConf = json_decode($mailConf);
                $mailConf->mailTitle = $problem->changeStatus == 'success' ? $this->lang->problem->delayMaile : vsprintf($mailConf->mailTitle, $mailConf->variables);;
                $mailConf->variables = [];
                $mailConf->mailContent = $this->lang->problem->delayContentMaile;
                $mailConf = json_encode($mailConf);
            }else{
                $toList = $problem->changeStatus == 'toManager' ?  ','.$problem->changeDealUser : $problem->changeDealUser;
            }
        }

        $server   = $this->loadModel('im')->getServer('zentao');
        $url = $server.helper::createLink($objectType, 'view', "id=$objectID", 'html');
        $subcontent = [];
        $subcontent['headTitle']    = '';
        $subcontent['headSubTitle'] = '';
        $subcontent['count']       = 0;
        $subcontent['id']       = 0;
        $subcontent['parent']       = '';
        $subcontent['parentURL']    = "";
        $subcontent['cardURL']      = $url;
        $subcontent['name']      = '';
        //标题
        $title = '';
        $actions = [];
        return ['toList'=>$toList,'subcontent'=>$subcontent,'url'=>$url,'title'=>$title,'actions'=>$actions,'mailconfig'=>$mailConf];
    }

    /**
     * 解除状态联动
     * @param $problemID
     * @return array
     */
    public function updateLinkage($problemID)
    {
        $oldProblem = $this->getByID($problemID);
        $problem = fixer::input('post')
            ->remove('comment')
            ->get();

        $this->dao->update(TABLE_PROBLEM)->data($problem)->autoCheck()
            ->where('id')->eq($problemID)
            ->exec();

        return common::createChanges($oldProblem, $problem);
    }

    /**
     * 判断问题单是否是已关闭状态，已关闭状态不可被关联
     * @param $problemIds array
     */
    public function checkIsClosed($problemIds){
        $problems = $this->dao->select("id,code,status")->from(TABLE_PROBLEM)
            ->where('status')->ne('deleted')
            ->andWhere('id')->in($problemIds)
            ->orderBy('id_desc')
            ->fetchAll();
        $arr = [];
        $res = ['result' => true,'msg'=>''];
        foreach ($problems as $problem) {
            if ($problem->status == 'closed'){
                $res['result'] = false;
                $arr[] = $problem->code;
            }
        }
        $res['msg'] = implode($arr,'、').'问题单已处于“已关闭”状态，已关闭状态不支持在交付，请确认。';
        return $res;
    }

    /**
     * 查询变更信息
     * @param $problemID
     * @return mixed
     */
    public function getChangeInfo($problemID){

        $change =  $this->dao->select('problemchange.*,problem.PlannedTimeOfChange')->from(TABLE_PROBLEM_CHANGE)->alias('problemchange')
            ->leftJoin(TABLE_PROBLEM)->alias('problem')
            ->on('problemchange.objectId = problem.id')
            ->where('objectId')->eq($problemID)
            ->andWhere('objectType')->eq('problem')
            ->fetchAll();
        foreach ($change as $key => $item) {
            $change[$key] = $this->loadModel('file')->replaceImgURL($item, 'changeReason,changeCommunicate');
            $change[$key]->files = $this->loadModel('file')->getByObject('problem', $problemID);
        }
        return $change;
    }

    /**
     * 第一次按照计划完成时间提前提醒
     *
     * @return bool
     * @throws Exception
     */
    public function remindToEndMailFirst(){
        //是否有发送邮件权限
        if(!$this->loadModel('common')->isSetMessage('mail', 'problem', 'toEndRemindMailFirst')){
            return false;
        }

        if(!helper::isWorkDay(date('Y-m-d H:i:s'))){
            return false;
        }
        $toEndDay = $this->lang->problem->problemOutTime['ToOutByPlannedTime_1'] ? $this->lang->problem->problemOutTime['ToOutByPlannedTime_1']: 0 ; //即将超时天数
        if($toEndDay <= 0){
            return false;
        }
        $this->remindToEndMail($toEndDay);
        return true;
    }

    /**
     * 第二次按照计划完成时间提前提醒
     *
     * @return bool
     * @throws Exception
     */
    public function remindToEndMailSecond(){
        if(!$this->loadModel('common')->isSetMessage('mail', 'problem', 'toEndRemindMailSecond')){
            return false;
        }

        if(!helper::isWorkDay(date('Y-m-d H:i:s'))){
            return false;
        }
        $toEndDay = $this->lang->problem->problemOutTime['ToOutByPlannedTime_2'] ? $this->lang->problem->problemOutTime['ToOutByPlannedTime_2']: 0 ; //即将超时天数
        if($toEndDay <= 0){
            return false;
        }
        $this->remindToEndMail($toEndDay);
        return true;
    }

    /**
     * 提示即将超期邮件
     *
     * @param int $toEndDay
     * @return bool
     */
    public function remindToEndMail($toEndDay = 0){
        $today = helper::today();
        $minEndDate = helper::getTrueWorkDay($today, $toEndDay, true);
        $maxEndDate = helper::getTrueWorkDay($today, $toEndDay +1, true);
        $problemList = $this->dao->select('id,code,abstract,PlannedTimeOfChange,status,acceptUser,acceptDept,createdBy')
            ->from(TABLE_PROBLEM)
            ->where('status')->notIN('deleted,closed,onlinesuccess,returned,delivery')
            ->andWhere('PlannedTimeOfChange')->ge($minEndDate)
            ->andWhere('PlannedTimeOfChange')->lt($maxEndDate)
            ->fetchAll('id');
        if(empty($problemList)){
            return true;
        }

        $userAccounts = array_column($problemList, 'acceptUser');
        $acceptUserList = $this->loadModel('user')->getUserInfoListByAccounts($userAccounts, 'account,realname');
        $mailAcceptUserData  = [];
        $mailCreatedUserData = [];
        $mailAcceptDeptData  = [];
        foreach ($problemList as $val){
            $problemId = $val->id;
            $val->status = zget($this->lang->problem->statusList, $val->status);
            $userInfo = zget($acceptUserList, $val->acceptUser, new stdClass());
            $val->acceptUserName = zget($userInfo, 'realname');
            $createdBy  = $val->createdBy;
            $acceptUser = $val->acceptUser;
            $acceptDept = $val->acceptDept;
            if($acceptUser){
                $mailAcceptUserData[$acceptUser][$problemId] = $val;
            }
            if($acceptDept){
                $mailAcceptDeptData[$acceptDept][$problemId] = $val;
            }
            //创建人
            if($createdBy){
                if(!isset($mailAcceptUserData[$createdBy][$problemId])){ //研发责任人和创建人是同一人时，按照研发责任人发送即可
                    $mailCreatedUserData[$createdBy][$problemId] = $val;
                }
            }
        }

        $setMail = 'remindtoendmail';
        $mailTitle = sprintf($this->lang->problem->remindToEndMail, $toEndDay);
        if($mailAcceptUserData){
            $ccList = '';
            foreach ($mailAcceptUserData as $account => $problemData){
                $toList = $account;
                $this->loadModel('demand')->sendmailSummary($problemData, $setMail, 'problem', $toList, $ccList, 'remindtoendmail', $mailTitle);
            }
        }

        //给创建人发邮件
        $isMailCreateUser = $this->lang->problem->problemOutTime['ToOutByPlannedTimeIsCreateUser'];
        if($isMailCreateUser == '1' && $mailCreatedUserData){
            $ccList = '';
            foreach ($mailCreatedUserData as $account => $demandData){
                $toList = $account;
                $this->loadModel('demand')->sendmailSummary($demandData, $setMail, 'problem', $toList, $ccList, 'remindtoendmail', $mailTitle);
            }
        }

        //给部门领导发邮件
        $isManagerCreateUser = $this->lang->problem->problemOutTime['ToOutByPlannedTimeIsManagerUser'];
        if($isManagerCreateUser == '1' && $mailAcceptDeptData){
            $ccList = '';
            $deptIds  = array_keys($mailAcceptDeptData);
            $deptList = $this->loadModel('dept')->getDeptListByIds($deptIds, 'id,name');
            if($deptList){
                $deptList = array_column($deptList, null, 'id');
            }
            foreach ($mailAcceptDeptData as $deptId => $demandData){
                $deptInfo = zget($deptList, $deptId, new stdClass());
                $deptName = zget($deptInfo, 'name');
                $mailTitle = sprintf($this->lang->problem->remindManagerToEndMail, $deptName, $toEndDay);
                $toList = trim(zget($this->config->problem->deptLeadersList, $deptId, ''), ',');
                $this->loadModel('demand')->sendmailSummary($demandData, $setMail, 'problem', $toList, $ccList, 'remindmanagertoendmail', $mailTitle);
            }
        }
        return true;
    }

   
    /**
     * 编辑考核结果
     * @param $problemID
     * @return array
     */
    public function editExaminationResult($problemID)
    {
        $oldProblem = $this->getByID($problemID);
        $problem = fixer::input('post')
            ->remove('comment')
            ->get();

        $this->dao->update(TABLE_PROBLEM)->data($problem)->autoCheck()
            ->batchCheck($this->config->problem->editexaminationresult->requiredFields, 'notempty')
            ->where('id')->eq($problemID)
            ->exec();

        return common::createChanges($oldProblem, $problem);
    }


}
