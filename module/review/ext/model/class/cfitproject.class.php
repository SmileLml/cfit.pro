<?php
class cfitprojectReview  extends reviewModel
{

    /**
     * 检查项目审批是否允许申请审批
     *
     * @author wangjiurong
     * @param $reviewInfo
     * @param $userAccount
     * @return array
     */
    public function checkReviewIsAllowApply($reviewInfo, $userAccount){
        $res = array(
            'result'  => false,
            'message' => '',
        );
        if(!$reviewInfo){
            $res['message'] = $this->lang->common->errorParamId;
            return $res;
        }
        if(!$userAccount){
            $res['message'] = $this->lang->common->errorParamUser;
            return $res;
        }
        //当前状态
        $status = $reviewInfo->status;
        if(!in_array($status, $this->lang->review->allowSubmitStatusList)){
            $statusDesc = zget($this->lang->review->statusLabelList, $status);
            $res['message'] = sprintf($this->lang->review->checkApplyResultList['statusError'], $statusDesc);
            return $res;
        }
        $createdUser = $reviewInfo->createdBy;
        $dealUsers  = [];
        if($reviewInfo->dealUser){
            $dealUsers = explode(',', $reviewInfo->dealUser);
        }
        if($userAccount != $createdUser && !in_array($userAccount, $dealUsers)){
            $res['message'] = $this->lang->review->checkApplyResultList['userError'];
            return $res;
        }
        $res['result'] = true;
        return $res;
    }


    /**
     * 检查项目审批是否允许审批
     *
     * @author wangjiurong
     * @param $reviewInfo
     * @param $userAccount
     * @return array|void
     */
    public function checkReviewIsAllowReview($reviewInfo, $userAccount){
        $res = array(
            'result'  => false,
            'message' => '',
        );
        if(!$reviewInfo){
            $res['message'] = $this->lang->common->errorParamId;
            return $res;
        }
        if(!$userAccount){
            $res['message'] = $this->lang->common->errorParamUser;
            return $res;
        }
        //当前状态
        $status = $reviewInfo->status;
        $allowReviewStatusList = $this->lang->review->allowReviewStatusList;
        //是否在审核状态
        if(!in_array($status, $allowReviewStatusList)){
            $statusDesc = zget($this->lang->review->statusLabelList, $status);
            $res['message'] = sprintf($this->lang->review->checkReviewOpResultList['statusError'], $statusDesc);
            return $res;
        }
        /*

        //校验时间不同审核阶段审核时间
        $now = helper::today();
        //初审
        if(in_array($status, $this->lang->review->allowPreReviewStatusList)){
            $deadline = $reviewInfo->preReviewDeadline;
            if($deadline < $now){
                $res['message'] = $this->lang->review->checkReviewOpResultList['preReviewDeadlineError'];
                return $res;
            }
        }else if(in_array($status, $this->lang->review->allowFirstReviewStatusList)){ //预审
            $deadline = $reviewInfo->firstReviewDeadline;
            if($deadline < $now){
                $res['message'] = $this->lang->review->checkReviewOpResultList['firstReviewDeadlineError'];
                return $res;
            }
        }else{ //正式审核
            $deadline = $reviewInfo->deadline;
            if($deadline < $now){
                $res['message'] = $this->lang->review->checkReviewOpResultList['deadlineError'];
                return $res;
            }
        }
        */

        $reviewers  = [];
        if(isset($reviewInfo->reviewers)){
            $reviewers = explode(',', $reviewInfo->reviewers);
        }
        if(!in_array($userAccount, $reviewers)){
            $res['message'] = $this->lang->review->checkReviewOpResultList['userError'];
            return $res;
        }
        $res['result'] = true;
        return $res;
    }

    /**
     * 检查项目审批是否允许指派
     *
     * @author wangjiurong
     * @param $reviewInfo
     * @param $userAccount
     * @return array|void
     */
    public function checkReviewIsAllowAssign($reviewInfo, $userAccount){
        $res = array(
            'result'  => false,
            'message' => '',
        );
        if(!$reviewInfo){
            $res['message'] = $this->lang->common->errorParamId;
            return $res;
        }
        if(!$userAccount){
            $res['message'] = $this->lang->common->errorParamUser;
            return $res;
        }
        //当前状态
        $status = $reviewInfo->status;
        $allowAssignStatusList = $this->lang->review->allowAssignStatusList;

        //是否在指派状态
        if(!in_array($status, $allowAssignStatusList)){
            $statusDesc = zget($this->lang->review->statusLabelList, $status);
            $res['message'] = sprintf($this->lang->review->checkAssignOpResultList['statusError'], $statusDesc);
            return $res;
        }

        $reviewers  = [];
        if(isset($reviewInfo->reviewers)){
            $reviewers = explode(',', $reviewInfo->reviewers);
        }
        if(!in_array($userAccount, $reviewers)){
            $res['message'] = $this->lang->review->checkAssignOpResultList['userError'];
            return $res;
        }
        $res['result'] = true;
        return $res;
    }

    /**
     *获得是否初审 （1-初审 2-不需要初审）
     *
     * @param $type
     * @return int
     */
    public function getIsFirstReview($type){
        $isFirstReview = 1;
        if(in_array($type, $this->lang->review->defSkipFirstReviewTypeList)){
            $isFirstReview = 2;
        }
        return $isFirstReview;
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
        // 评审创建时间存时分秒
        $now = helper::now();
        if(is_array($this->post->files) && count($this->post->files)){
            return dao::$errors = array('files' => $this->lang->review->filesEmpty);
        }
        //关联项目信息
        $planSelect = 'project,isImportant,type';
        $projectPlan = $this->loadModel('projectplan')->getPlanMainInfoByProjectID($projectID, $planSelect);
        $isImportant = 2;
        $projectPlanType = 0;
        if(isset($projectPlan->project)){
           if($projectPlan->isImportant == 1){
               $isImportant = 1;
           }
            $projectPlanType = $projectPlan->type;
        }
        $data  = fixer::input('post')
            ->add('status', 'waitApply')
            ->add('createdBy', $this->app->user->account)
            ->add('createdDept', $this->app->user->dept)
            ->add('createdDate', $now)
            ->add('project', $projectID)
            ->add('dealUser', $this->app->user->account)
            ->add('projectType', $projectPlanType) //关联项目类型
            ->add('isImportant', $isImportant) //是否重点项目
            ->join('owner', ',')
            ->join('expert', ',')
            ->join('reviewedBy', ',')
            ->join('outside', ',')
            ->join('relatedUsers', ',')
            ->join('object', ',')
            ->remove('uid,files,consumed')
            ->get();

        if(!isset($_POST['owner'])){
            $data->owner = '';
        }
        if(!isset($_POST['relatedUsers']) && empty($_POST['relatedUsers']))
        {
            return dao::$errors['relatedUsers'] = $this->lang->review->relatedUsersEmpty ;
        }
        //判断评审方式是否为空
        if(!isset($_POST['grade'])){
            $data->grade = '';
        }
        //判断评审专家是否为空
        if(!isset($_POST['expert'])){
            $data->expert = '';
        }
        //判断评审参与人员是否为空
        if(!isset($_POST['reviewedBy'])){
            $data->reviewedBy = '';
        }
        //判断外部人员是否为空
        if(!isset($_POST['outside'])){
            $data->outside = '';
        }
        //判断备注是否为空
        if(!isset($_POST['comment'])){
            $data->comment = '';
        }
        //检查标题是否重复
        $checkRes = $this->getTitleIsExist($data->title);
        if($checkRes){
            return dao::$errors['title'] = $this->lang->review->titleError ;
        }

        $type = $data->type;

        //判断是否初审
        $data->isFirstReview = $this->getIsFirstReview($type);
       /* if(!ctype_digit($this->post->consumed))
        {
            $errors['consumed'] = sprintf($this->lang->review->noNumeric, $this->lang->review->consumed);
            return dao::$errors = $errors;
        }*/
       /* $checkRes = $this->loadModel('consumed')->checkConsumedInfo($this->post->consumed);
        if(!$checkRes){
            return false;
        }*/

        $data->owner        = trim($data->owner, ',');
        $data->expert       = trim($data->expert, ',');
        $data->reviewedBy   = trim($data->reviewedBy, ',');
        $data->outside      = trim($data->outside, ',');
        $data->relatedUsers = trim($data->relatedUsers, ',');
        //项目包含主从项目信息
        $data->mainRelationInfo  = $data->mainRelationInfo   == $this->lang->review->noRelationRecord ? '': $data->mainRelationInfo;
        $data->slaveRelationInfo = $data->slaveRelationInfo  == $this->lang->review->noRelationRecord ? '': $data->slaveRelationInfo;
        //开启事务
        $this->dao->begin();
        $this->dao->insert(TABLE_REVIEW)->data($data)
            ->autoCheck()
            ->batchCheck($this->config->review->create->requiredFields, 'notempty')
            ->exec();

        $reviewID = $this->dao->lastInsertID();
        $this->loadModel('consumed')->record('review', $reviewID, '0', $this->app->user->account, '', $data->status, array());

        $this->loadModel('file')->updateObjectID($this->post->uid, $reviewID, 'review');
        $this->file->saveUpload('review', $reviewID);
        $this->tryError(1); //检查报错 1= 需要rollback
        $this->dao->commit(); //调试完逻辑最后开启事务

        // 加入白名单
        $expert =  (isset($_POST['expert'])) ? $_POST['expert'] : array();
        $reviewedBy =  (isset($_POST['reviewedBy']) ) ? $_POST['reviewedBy'] : array();
        $outside =  (isset($_POST['outside']) ) ? $_POST['outside'] : array();
       // $allUser = array_unique( array_merge((array)$data->qa,(array)$data->reviewer,(array)$data->owner,$expert,$reviewedBy,(array)$data->outside));
        $allUser = array_unique(array_merge((array)$data->qa,(array)$data->reviewer,(array)$data->owner,$expert,$reviewedBy,$outside));
        $allUser = array_filter($allUser);
        foreach ($allUser as $user){
            if(empty($user)) continue;
            $this->addProjectReviewWhitelist($projectID, $reviewID, $user);
        }
        if(!dao::isError()) return $reviewID;

        return false;
    }
    /**
     * 尝试报错 或需要rollback
     */
    public function tryError($rollBack = 0)
    {
        if(dao::isError())
        {
            if($rollBack == 1){
                $this->dao->rollBack();
            }
            $response['result']  = 'fail';
            $response['message'] = dao::getError();
            $this->send($response);
        }
    }
    /**
     * 直接输出data数据
     * @access public
     */
    private function send($data)
    {
        die(json_encode($data, JSON_UNESCAPED_UNICODE));
    }


    /**
     *  Submit a review.
     *
     * @param int $reviewID
     * @return array|bool|void
     */
    public function submit($reviewID){
        //历史数据
        $oldReview = $this->getByID($reviewID);

        //项目id
        $projectId = $oldReview->project;
        //检查是否许允许申请审核
        $res = $this->checkReviewIsAllowApply($oldReview, $this->app->user->account);
        if(!$res['result']){
            dao::$errors['status'] = $res['message'];
            return false;
        }
        //获得数据
        $data = fixer::input('post')->get();
        if(($data->version != $oldReview->version) || ($data->rejectStage != $oldReview->rejectStage)){
            dao::$errors[] = $this->lang->review->checkApplyResultList['statusError'];
            return false;
        }

        //校验是否有已新建问题(只有正式评审需修改、验证退回时校验)
        $passButEditStatusArray = [
            $this->lang->review->statusList['formalPassButEdit'],
            $this->lang->review->statusList['meetingPassButEdit']
        ];
        if( in_array($oldReview->status, $passButEditStatusArray) || ($oldReview->status == $this->lang->review->statusList['waitApply']  && $oldReview->rejectStage ==  $this->lang->review->rejectStageList[5])){
            $issueRes = $this->checkIssueStatus($reviewID);
            if(!$issueRes){
                dao::$errors[] = $this->lang->review->checkIssueError;
                return false;
            }
        }

        //申请审核后的一下状态
        $nextStatus  = $this->getSubmitNextStatus($oldReview);
        $postUser = '';
        switch ($nextStatus){
            case $this->lang->review->statusList['waitPreReview']: //待预审
                $postUser = $this->post->qa;
                if(!$postUser){
                    dao::$errors['qa'] = $this->lang->review->checkApplyResultList['qaEmpty'];
                    return false;
                }
                break;

            case $this->lang->review->statusList['waitFirstReview']: //待初审
                $postUser = $this->post->firstReviewers;
                if(!$postUser){
                    dao::$errors['firstReviewers'] = $this->lang->review->checkApplyResultList['firstReviewersEmpty'] ;
                    return false;
                }
                $postUser = implode(',', $postUser);
                break;

            case $this->lang->review->statusList['waitVerify']: //待验证
//                if($oldReview->type == 'cbp' && $oldReview->status =='firstPassButEdit'){
//                    $maxVersion =  $this->getReviewNodeMaxVersion($reviewID);
//                    $verifyReviewers = $this->loadModel('review')->getReviewersByNodeCode('review', $reviewID, $maxVersion, 'firstReview');
//                    $postUser = $verifyReviewers;
//                }else{
//                    $postUser = $this->post->verifyReviewers;
//                }
                $postUser = $this->post->verifyReviewers;
                if(!$postUser){
                    if($oldReview->status != 'outPassButEdit'){
                        dao::$errors['verifyReviewers'] = $this->lang->review->checkApplyResultList['verifyReviewersEmpty'];
                        return false;
                    }

                }
                if(is_array($postUser)){
                    $postUser = implode(',', $postUser);
                }

                break;

            case $this->lang->review->statusList['waitFirstAssignDept']: //待指派初审部门
            case $this->lang->review->statusList['waitOutReview']: //待外部审核
                $postUser = $this->post->reviewer;
                if(!$postUser){
                    dao::$errors['reviewer'] = $this->lang->review->checkResultList['reviewerError'];
                    return false;
                }
                break;

            case $this->lang->review->statusList['waitFormalAssignReviewer']: //评审主席指派正式人员审核
                $postUser = $this->post->owner;
                if(!$postUser){
                    dao::$errors['owner'] = $this->lang->review->checkResultList['ownerError'];
                    return false;
                }
                break;

            case $this->lang->review->statusList['pass']: //评审通过
                if($oldReview->type == 'cbp'){
                    $postUser = $oldReview->qa ;

                }
                break;

            default:
                dao::$errors[] = $this->lang->review->checkApplyResultList['statusError'];
                return false;
                break;
        }
        //检查工作量(允许0)
       /* $consumed = $this->post->consumed;
        if($consumed !== '0'){
            $checkRes = $this->loadModel('consumed')->checkConsumedInfo($consumed);
            if(!$checkRes){
                return false;
            }
        }*/

        //查询是否存在提交审核节点
        $oldStatus = $oldReview->status;
        $oldMaxVersion =  $this->getReviewNodeMaxVersion($reviewID);
        if(($oldMaxVersion < $oldReview->version) && ($oldStatus == 'waitApply') && ($nextStatus == 'waitVerify')){ //主表已经升级了版本
            $oldNodeCode = 'rejectVerifyButEdit';
            $oldVersion = $oldMaxVersion;
        }else{
            $oldNodeCode = $this->getReviewNodeCodeByStatus($oldStatus, $oldStatus);
            $oldVersion = $oldReview->version;
        }

        if($oldNodeCode) {
            $pendingReviewNode = $this->loadModel('review')->getPendingReviewNode('review', $reviewID, $oldVersion, $oldNodeCode);
            if ($pendingReviewNode) {
                $reviewResult = 'pass';
                $result = $this->loadModel('review')->check('review', $reviewID, $oldVersion, $reviewResult, $this->post->comment);
                if (!$result) {
                    dao::$errors[] = $this->lang->review->checkResultList['opError'];
                    return false;
                }
            }
        }
        //下一节点处理人
//        if($nextStatus == $this->lang->review->statusList['waitVerify']){ //验证资料
//            $postUser = $this->getReviewLastVerifyUsers($reviewID, $postUser);
//        }
        $nextDealUser = $this->getNextDealUser($oldReview, $nextStatus, $postUser);
        $review = fixer::input('post')
            ->add('status', $nextStatus) //获得申请审批的下一状态
            ->add('dealUser', $nextDealUser) //获得下一状态的审核人
            ->remove('comment,consumed, uid, firstReviewers, verifyReviewers,mailto,firstReviewer')
            ->get();

        //提交待验证时
        if($nextStatus == 'waitVerify'){
            if(isset($review->unDealIssueRaiseByUsers)){
                $review->unDealIssueRaiseByUsers = implode(',', $review->unDealIssueRaiseByUsers);
            }else{
                $review->unDealIssueRaiseByUsers = '';
            }
        }


        if($nextStatus == 'waitPreReview'){
            $review->submitDate = $oldReview->createdDate;
        }elseif(in_array($nextStatus, $this->lang->review->sumitDateStatusList)){
            $review->submitDate = helper::now();
        }
        if(isset($review->submitDate)){
            $endDate = $this->getEndDate($nextStatus,$review->submitDate, $oldReview);
            if($nextStatus =='waitPreReview'){
                $review->preReviewDeadline = $endDate;
            }else if($nextStatus =='waitFirstAssignDept' ){
                $review->firstReviewDeadline = $endDate;
            } else if($nextStatus =='waitFirstReview'){
                $review->firstReviewDeadline = $endDate;
            }
            if(in_array($nextStatus, $this->lang->review->sumitDateStatusList)){
                $review->endDate = $endDate;
            }
        }

        //挂起恢复后申请评审
        if(($oldReview->status == $this->lang->review->statusList['waitApply']) && ($oldReview->rejectStage == $this->lang->review->rejectStageList[11])){
            $review->version = $oldReview->version + 1;
            //$review->rejectStage = 0; //初始化
        }

        $review->rejectStage = 0; //初始化
        //修改项目审批表记录
        $this->dao->update(TABLE_REVIEW)->data($review)
            ->autoCheck()
            ->batchCheck($this->config->review->submit->requiredFields, 'notempty')
            ->where('id')->eq($reviewID)->exec();

        if(dao::isError()) {
            return false;
        }
        //重新获得信息
        $newReviewInfo = $this->getByID($reviewID);
        //增加工作量记录
        $this->loadModel('consumed')->record('review', $reviewID, '0', $this->app->user->account, $oldReview->status, $nextStatus, $this->post->mailto, '', $newReviewInfo->version);

        //增加新版本
        if($nextStatus !== 'pass'){ //提交后审核通过时不需要增加版本
            $res = $this->addNewVersionReviewNodes($newReviewInfo, $oldReview->status);
        }

        if($nextStatus == $this->lang->review->statusList['waitVerify']){
            // 取初审意见为通过无修修改的验证人员
            $firstPassReviewers = $this->loadModel('review')->getReviewersAndCommentByNodeCode('review', $oldReview->id, $oldVersion, $this->lang->review->nodeCodeList['firstReview']);
            // 取正式评审意见为通过无修修改的验证人员
            $formalPassReviewers = $this->loadModel('review')->getReviewersAndCommentByNodeCode('review', $oldReview->id, $oldVersion, $this->lang->review->nodeCodeList['formalReview']);

            // 查询初审和正式评审验证人员
            $firstReviewers = $this->loadModel('review')->getReviewersByNodeCode('review', $oldReview->id, $oldVersion, $this->lang->review->nodeCodeList['firstReview']);
            $formalReviewers = $this->loadModel('review')->getReviewersByNodeCode('review', $oldReview->id, $oldVersion, $this->lang->review->nodeCodeList['formalReview']);
            $firstReviewers = explode(',',$firstReviewers);
            $formalReviewers = explode(',',$formalReviewers);

            // 遍历所选择验证人员
            $reviewers = explode(',', $review->dealUser);
            $count = count($reviewers); // 获取指派验证人员数
            foreach($reviewers as $passUser){
                if($count == 1) break; // 兜底人不流转为通过
                // 无初审时
                if($oldReview->isFirstReview == '2'){
                    if(in_array($passUser, $formalPassReviewers)){
                        // 判断是否没有待处理问题
                        $issueCount = $this->loadModel('reviewproblem')->getReviewIssueCountByUser($oldReview->id, $passUser, 0);
                        if(!empty($issueCount)) continue;
                        // 根据是否有验证未通过的问题判断是通过还是不通过
                        $failed = $this->loadModel('reviewproblem')->getReviewIssueCountByUser($oldReview->id, $passUser, 0,'failed');
                        $result = $failed == 0 ? 'pass' : 'reject';
                        $this->reviewVerify($oldReview->id, $result, $passUser);
                        $count--;
                    }
                }else{
                    // 有初审时(先判断是否都参与了初审和正式评审)
                    if(in_array($passUser, $firstReviewers) && in_array($passUser, $formalReviewers)) {
                        if(in_array($passUser, $firstPassReviewers) && in_array($passUser, $formalPassReviewers)) {
                            // 判断是否没有待处理问题
                            $issueCount = $this->loadModel('reviewproblem')->getReviewIssueCountByUser($oldReview->id, $passUser, 0);
                            if(!empty($issueCount)) continue;
                            // 根据是否有验证未通过的问题判断是通过还是不通过
                            $failed = $this->loadModel('reviewproblem')->getReviewIssueCountByUser($oldReview->id, $passUser, 0,'failed');
                            $result = $failed == 0 ? 'pass' : 'reject';
                            $this->reviewVerify($oldReview->id, $result, $passUser);
                            $count--;
                        }
                    }elseif(in_array($passUser, $firstReviewers)){
                        if(in_array($passUser, $firstPassReviewers)) {
                            // 判断是否没有待处理问题
                            $issueCount = $this->loadModel('reviewproblem')->getReviewIssueCountByUser($oldReview->id, $passUser, 0);
                            if(!empty($issueCount)) continue;
                            // 根据是否有验证未通过的问题判断是通过还是不通过
                            $failed = $this->loadModel('reviewproblem')->getReviewIssueCountByUser($oldReview->id, $passUser, 0,'failed');
                            $result = $failed == 0 ? 'pass' : 'reject';
                            $this->reviewVerify($oldReview->id, $result, $passUser);
                            $count--;
                        }
                    }elseif(in_array($passUser, $formalReviewers)){
                        if (in_array($passUser, $formalPassReviewers)) {
                            // 判断是否没有待处理问题
                            $issueCount = $this->loadModel('reviewproblem')->getReviewIssueCountByUser($oldReview->id, $passUser, 0);
                            if(!empty($issueCount)) continue;
                            // 根据是否有验证未通过的问题判断是通过还是不通过
                            $failed = $this->loadModel('reviewproblem')->getReviewIssueCountByUser($oldReview->id, $passUser, 0,'failed');
                            $result = $failed == 0 ? 'pass' : 'reject';
                            $this->reviewVerify($oldReview->id, $result, $passUser);
                            $count--;
                        }
                    }
                }
            }
        }

//        if($nextStatus == $this->lang->review->statusList['waitVerify']){ //验证资料
//            //用上版本验证通过的状态修改本版本验证通过的状态
//            $res = $this->setReviewVerifyUsersPass($reviewID);
//            //重新获得信息
//            $newReviewInfo = $this->getByID($reviewID);
//            if($newReviewInfo->reviewers){
//                $updateParams = new stdClass();
//                $updateParams->dealUser = $newReviewInfo->reviewers;
//                //修改项目审批表记录
//                $this->dao->update(TABLE_REVIEW)->data($updateParams)
//                    ->autoCheck()
//                    ->batchCheck($this->config->review->submit->requiredFields, 'notempty')
//                    ->where('id')->eq($reviewID)->exec();
//            }
//        }

        //增加项目白名单
        if(!is_array($postUser)){
            $postUser = explode(',', $postUser);
        }
        foreach ($postUser as $userAccount){
            $res = $this->addProjectReviewWhitelist($projectId, $reviewID, $userAccount);
        }
        //获得差异信息
        $ext = new stdClass();
        $ext->old = '';
        $ext->new  = isset($_POST['mailto'])  ?  implode(' ',$_POST['mailto']) :'';
        $logChange = common::createChanges($oldReview, $review,array('mailto'=>$ext));
        return $logChange;
    }

    /**
     * 获得审核上一版本验证人
     *
     * @param $reviewId
     * @param string $postUser
     * @return string
     */
    public function getReviewLastVerifyUsers($reviewId, $postUser = ''){
        $lastVerifyUsers = $postUser;
        $nodeInfo = $this->dao->select('id')
            ->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq('review')
            ->andWhere('objectID')->eq($reviewId)
            ->andWhere('nodeCode')->eq('verify')
            ->orderBy('id_desc')
            ->fetch();
        if(!$nodeInfo){
            return $lastVerifyUsers;
        }

        $nodeId = $nodeInfo->id;
        $sql = "SELECT reviewer FROM `zt_reviewer` wHeRe node = '{$nodeId}' AND status = 'pass' AND (extra LIKE '%\"subId\":0%') oRdEr bY id";
        $passVerifyUsersList =  $this->dao->query($sql)->fetchAll();
        if(!$passVerifyUsersList){
            return $lastVerifyUsers;
        }
        $reviewers = array_column($passVerifyUsersList, 'reviewer');
        $lastVerifyUsers .= ','. implode(',', $reviewers);
        return $lastVerifyUsers;
    }


    /**
     * 设置验证用户审核通过
     *
     *
     * @param $reviewId
     * @return bool
     */
    public function setReviewVerifyUsersPass($reviewId){
        $data = $this->dao->select('id')
            ->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq('review')
            ->andWhere('objectID')->eq($reviewId)
            ->andWhere('nodeCode')->eq('verify')
            ->orderBy('id_desc')
            ->limit(2)
            ->fetchAll();
        if(!$data){
            return true;
        }
        $count = count($data);
        if($count < 2){
            return true;
        }
        //当前版本node
        $currentNode = $data[0]->id;
        //上一版本node
        $lastNode = $data[1]->id;
        //查询上一版本已经审核通过的
        $sql = "SELECT * FROM `zt_reviewer` wHeRe node = '{$lastNode}' AND status = 'pass' AND (extra LIKE '%\"subId\":0%') oRdEr bY id";
        $passVerifyUsersList =  $this->dao->query($sql)->fetchAll();
        if(!$passVerifyUsersList){
            return true;
        }
        $sql = "SELECT id, reviewer FROM `zt_reviewer` wHeRe node = '{$currentNode}'";
        $currentNodeUserList =  $this->dao->query($sql)->fetchAll();
        if(!$currentNodeUserList){
            return true;
        }
        $currentNodeUserList = array_column($currentNodeUserList, null, 'reviewer');
        foreach ($passVerifyUsersList as $val){
            $reviewer = $val->reviewer;
            if(!isset($currentNodeUserList[$reviewer])){
                continue;
            }
            $updateParams = new stdClass();
            $updateParams->status     = 'pass';
            $updateParams->comment    = $val->comment;
            $updateParams->extra      =  $val->extra;
            $updateParams->reviewTime =  $val->reviewTime;
            $currentUser = zget($currentNodeUserList, $reviewer);
            $this->dao->update(TABLE_REVIEWER)->data($updateParams)
                ->where('id')->eq($currentUser->id)
                ->andWhere('node')->eq($currentNode)
                ->exec();
        }
        return true;
    }

    /**
     * 增加项目评审白名单
     *
     * @param $projectId
     * @params $reviewId
     * @param $userAccount
     * @return false|void
     */
    public function addProjectReviewWhitelist($projectId, $reviewId, $userAccount){
        if(!($projectId && $userAccount)){
            return false;
        }
        $reason = 1002;
        //检查是否有项目权限
        $res = $this->loadModel('project')->checkOwnProjectPermission($projectId, $userAccount, $reviewId, $reason);
        if($res){
            return true;
        }
        $res = $this->loadModel('project')->addProjectWhitelistInfo($projectId,  $userAccount, $reviewId, $reason);
        return $res;
    }
    /**
     * 新增审批新版本的审核节点
     *
     * @param $reviewInfo
     * @param $oldStatus
     * @return false|void
     */
    public function addNewVersionReviewNodes($reviewInfo, $oldStatus){
        $res = false;
        if(!$reviewInfo){
            return $res;
        }
        $reviewID = $reviewInfo->id;
        $status   = $reviewInfo->status;
        $version  = $reviewInfo->version;
        $dealUser = $reviewInfo->dealUser;
        $endDate  = $reviewInfo->endDate;

        //审核步骤id
        $rejectStage =  $reviewInfo->rejectStage;
        if(!$dealUser){
            dao::$errors[] = $this->lang->review->checkApplyResultList['dealUserEmpty'];
            return $res;
        }

        $reviewers = explode(',', $dealUser);
        //节点标识
        $nodeCode = $this->getReviewNodeCodeByStatus($status, $oldStatus);

        $subObjectType = $this->getReviewNodeSubObjectType($status);
        $type = $this->getReviewNodeType($status);
        //扩展信息
        $extParams = array(
            'subObjectType' => $subObjectType,
            'type'          => $type
        );
        $oldVersion =  $this->getReviewNodeMaxVersion($reviewID); //历史版本
        //if(in_array($oldStatus, $this->lang->review->passButEditStatusList)){ //审核通过以后需要编辑，编辑以后再申请审核（不需要升级版本）
        if($version == $oldVersion){ //审核通过以后需要编辑，编辑以后再申请审核（不需要升级版本）
            $maxStage = $this->loadModel('review')->getReviewMaxStage($reviewID, 'review', $version);
            if(in_array($oldStatus, $this->lang->review->allowAdvanceSetReviewersStatusList)){
                $stage = $maxStage;
            }else{
                $stage = $maxStage + 1;
            }
            //增加当前审核节点
            $nodes = array(
                array(
                    'reviewers' => $reviewers,
                    'stage'    => $stage,
                    'nodeCode' => $nodeCode,
                )
            );

            if(in_array($oldStatus, $this->lang->review->allowAdvanceSetReviewersStatusList)){ //删除原来的节点
                $nodeCode = $this->lang->review->nodeCodeList['verify'];
                $this->delReviewNodeByNodeCode($reviewID, 'review', $version, $nodeCode);
            }
            //新增的本次节点
            $this->submitReview($reviewID, 'review', $version, $nodes, $extParams);
        }else{ //升级了版本
            //获得历史版本
            //$oldVersion =  $this->getReviewNodeMaxVersion($reviewID);
            if($status == $this->lang->review->statusList['waitVerify']){ //待验证时显示编辑节点
                $maxStage = $this->loadModel('review')->getReviewMaxStage($reviewID, 'review', $oldVersion);
                $stage = $maxStage + 1;
            }else{
                $stage = $this->review->getNodeStage('review', $reviewID, $oldVersion, $nodeCode);
                if(!$stage){
                    $maxStage = $this->loadModel('review')->getReviewMaxStage($reviewID, 'review', $oldVersion);
                    $stage = $maxStage + 1;
                }
            }

            //增加当前审核节点
            $nodes = array(
                array(
                    'reviewers' => $reviewers,
                    'stage'    => $stage,
                    'nodeCode' => $nodeCode,
                )
            );
            //历史节点你信息补全
            if($status != $this->lang->review->statusList['waitPreReview']) { //提交后流转到非待预审（即初审或者待验证资料）
                $historyReviews = $this->review->getHistoryReviewers('review', $reviewID, $oldVersion, $stage);
                if(!empty($historyReviews)){
                    foreach ($historyReviews as $currentNodeInfo){
                        $currentNodeReviewers = $currentNodeInfo->reviewers;
                        unset($currentNodeInfo->reviewers);
                        unset($currentNodeInfo->id);
                        $currentNodeInfo->version = $version;
                        $currentNodeInfo->endDate = $endDate;
                        //新增审核节点
                        $this->dao->insert(TABLE_REVIEWNODE)->data($currentNodeInfo)->exec();
                        $newNodeID = $this->dao->lastInsertID();
                        foreach ($currentNodeReviewers as $currentNodeReviewer){
                            $currentNodeReviewer->node = $newNodeID;
                            unset($currentNodeReviewer->id);
                            $this->dao->insert(TABLE_REVIEWER)->data($currentNodeReviewer)->exec();
                        }
                    }
                }
            }

            //返回初审时需要补充下一节点的人
            //if (($status == $this->lang->review->statusList['waitFirstReview']) && (in_array($rejectStage, $this->lang->review->returnFirstRejectStageList))) {
            if ($status == $this->lang->review->statusList['waitFirstReview']) {
                //获得初审主审人员
                $nextStage = $stage + 1;
                $nextNodeCode = $this->lang->review->nodeCodeList['firstMainReview'];
                $mainReviewersInfo = $this->review->getReviewersByNodeCode('review', $reviewID, $oldVersion, $nextNodeCode);
                $mainReviewers = [];
                if ($mainReviewersInfo) {
                    $mainReviewers = explode(',', $mainReviewersInfo);
                }
                //参与人员
                $includeReviewers = array_diff($reviewers, $mainReviewers);
                //新增的本次节点（主审人员和审核人员）
                $joinReviewers = [];
                $joinReviewers[1] = $mainReviewers; //主审核人员
                $joinReviewers[2] = $includeReviewers;
                $nodes = array(
                    array(
                        'reviewers' => $joinReviewers,
                        'stage'    => $stage,
                        'nodeCode' => $nodeCode,
                    )
                );
                //新增的本次节点（主审人员和审核人员）
                $this->submitReview($reviewID, 'review', $version, $nodes, $extParams);
                //新增主要审核人员
                if ($mainReviewers){
                    $nextNodes = array(
                        array(
                            'reviewers' => $mainReviewers,
                            'stage'    => $nextStage,
                            'status'   => 'wait',
                            'nodeCode' => $nextNodeCode,
                        )
                    );
                    $this->submitReview($reviewID, 'review', $version, $nextNodes, $extParams);
                }
            }else{
                //新增的本次节点
                $this->submitReview($reviewID, 'review', $version, $nodes, $extParams);
            }
        }
        return true;
    }

    /**
     *获得驳回编辑以后的下一状态
     *
     * @param $rejectStage
     * @param $isFirstReview
     * @return mixed|string
     */
    public function getEditRejectNextStatus($rejectStage, $isFirstReview){
        $nextStatus = '';
        if($this->lang->review->editRejectNextStatusList[$rejectStage]){
            $nextStatus = $this->lang->review->editRejectNextStatusList[$rejectStage];
        }
        if($nextStatus == $this->lang->review->statusList['waitFirstReview']){ //按照审核驳回步骤应该返回初审
            if($isFirstReview == 2){ //上一操作跳过了初审，所以返回预审
                $nextStatus = $this->lang->review->statusList['waitPreReview'];
            }
        }
        return $nextStatus;
    }

    /**
     * 获得审批提交页面的后缀
     *
     * @param $nextStatus
     * @return string
     */
    public function getSubmitViewSuffix($nextStatus){
        $viewSuffix = '';
        switch ($nextStatus){
            case $this->lang->review->statusList['waitPreReview']:
                $viewSuffix = 'Pre';
                break;

            case $this->lang->review->statusList['waitFirstReview']:
                $viewSuffix = 'First';
                break;

            case $this->lang->review->statusList['waitFirstAssignDept']: //待指派初审部门人员
            case $this->lang->review->statusList['waitOutReview']: //待外部审核
                $viewSuffix = 'SetReviewer';
                break;

            case $this->lang->review->statusList['waitFormalAssignReviewer']: //待设置正式人员审核
                $viewSuffix = 'SetOwner'; //设置评审主席
                break;

            case $this->lang->review->statusList['waitVerify']:
                $viewSuffix = 'Verify';
                break;

            case $this->lang->review->statusList['pass']:
                $viewSuffix = 'SetPass';
                break;

            default:
                break;
        }
        return $viewSuffix;
    }

    /**
     *获得下一个状态的操作人
     *
     * @param $reviewInfo
     * @param $nextStatus
     * @param $postUser
     * @param $isReNew
     * @return string
     */
    public function getNextDealUser($reviewInfo, $nextStatus, $postUser = ''){
        $nextDealUser = '';
        switch ($nextStatus){
            case $this->lang->review->statusList['waitApply']: //待提交
                $nextDealUser = $reviewInfo->createdBy; //创建人
                break;

            case $this->lang->review->statusList['waitPreReview']: //待初审
                if($postUser){
                    $nextDealUser = $postUser;
                }else{
                    $nextDealUser = $reviewInfo->qa; //挂起恢复操作的时候指定待处理人
                }
                break;

            case $this->lang->review->statusList['waitFirstAssignDept']: //待指派初审部门
                if($postUser){
                    $nextDealUser = $postUser;
                }else{
                    $nextDealUser = $reviewInfo->reviewer; //评审专员
                }
                break;

            case $this->lang->review->statusList['waitFirstAssignReviewer']: //待指派初审人员
                $nextDealUser = $postUser; //部门接口人
                break;

            case $this->lang->review->statusList['firstAssigning']: //指派中
                $nextDealUser = $reviewInfo->reviewer; //评审专员
                break;

            case $this->lang->review->statusList['waitFirstReview']: //待初审
                if($postUser){
                    $nextDealUser = $postUser;
                }else{
                    $nextDealUser = $reviewInfo->reviewers; //评审专员
                }
                break;

            case $this->lang->review->statusList['firstReviewing']: //初审中
                $nextDealUser = $reviewInfo->reviewers; //评审专员
                break;

            case $this->lang->review->statusList['waitFirstMainReview']: //初审中->待主审人员审核
                $nextDealUser = $reviewInfo->reviewers;
                break;

            case $this->lang->review->statusList['firstMainReviewing']: //初审中->主审人员审核中
                $nextDealUser = $reviewInfo->reviewers;
                break;

            case $this->lang->review->statusList['waitFormalAssignReviewer']: //待指派正审人员审核
                if($postUser){
                    $nextDealUser = $postUser;
                }else{
                    $nextDealUser = $reviewInfo->owner; //评审主席
                }
                break;

            case $this->lang->review->statusList['waitFormalReview']: //待正式人员线上审核
                $expert     = [];
                $reviewedBy = [];
                if($reviewInfo->expert){ //评审内部专家
                    $expert = explode(',', $reviewInfo->expert);
                }
                if($reviewInfo->reviewedBy){ //评审外部专家1
                    $reviewedBy = explode(',', $reviewInfo->reviewedBy);
                }

                //内部专家和外部专家1
                $nextDealUser = array_merge($expert, $reviewedBy);
                if($nextDealUser){
                    $nextDealUser = array_flip(array_flip($nextDealUser));
                    $nextDealUser = implode(',', $nextDealUser);
                }else{
                    $nextDealUser = '';
                }
                break;

            case $this->lang->review->statusList['formalReviewing']: //正式人员线上评审中
                $nextDealUser = $reviewInfo->reviewers;
                break;

            case $this->lang->review->statusList['waitFormalOwnerReview']: //评主席确定评审结论
                $nextDealUser = $reviewInfo->owner;
                break;

            case $this->lang->review->statusList['waitMeetingReview']: //正式评审待会议评审(新增状态)
                $nextDealUser = $reviewInfo->reviewer;//评审专员
                break;

            case $this->lang->review->statusList['meetingReviewing']: //会议评审中(新增状态)
                $nextDealUser = $reviewInfo->reviewers;  //评审人员
                break;

            case $this->lang->review->statusList['waitMeetingOwnerReview']: //评审主席确定会议评审结论(新增状态)
               $nextDealUser = $reviewInfo->owner; //评审主席
                break;


            case $this->lang->review->statusList['waitVerify']: //待验证
            case $this->lang->review->statusList['verifying']: //待验证
                if($postUser){
                    $nextDealUser = $postUser;
                }else{
                    $nextDealUser = $reviewInfo->reviewers;  //评审人员
                }
                break;


            case $this->lang->review->statusList['waitOutReview']: //待外部人员审核
                if($postUser){
                    $nextDealUser = $postUser;
                }else{
                    $nextDealUser = $reviewInfo->qa; //评审专员
                }
                break;

            case $this->lang->review->statusList['prePassButEdit']: //预审通过-需修改资料
            case $this->lang->review->statusList['firstPassButEdit']: //初审通过-需修改资料
            case $this->lang->review->statusList['formalPassButEdit']: //正式审核通过-需修改资料
            case $this->lang->review->statusList['meetingPassButEdit']: //正式审核通过-需修改资料
            case $this->lang->review->statusList['outPassButEdit']: //外部评审通过-需修改资料
                 $nextDealUser = $reviewInfo->createdBy;
                break;

            case $this->lang->review->statusList['rejectPre']: //预审退回
            case $this->lang->review->statusList['rejectFirst']: //初审退回
            case $this->lang->review->statusList['rejectFormal']: //正式线上评审退回
            case $this->lang->review->statusList['rejectMeeting']: //正式会议评审退回
            case $this->lang->review->statusList['rejectOut']: //外部评审退回
            case $this->lang->review->statusList['rejectVerify']: //验证退回
                $nextDealUser = $reviewInfo->createdBy;
                break;

            case $this->lang->review->statusList['pass']:
                if($reviewInfo->type == 'cbp'){
                    $nextDealUser = $reviewInfo->qa; //qa
                }else{
                    $nextDealUser = $reviewInfo->reviewer; //评审专员
                }
                break;

            case $this->lang->review->statusList['archive']: //待归档
                $nextDealUser = [$reviewInfo->createdBy]; //创建人
                $nextDealUser = array_flip(array_flip($nextDealUser));
                $nextDealUser = implode(',', $nextDealUser);
                break;

            case $this->lang->review->statusList['baseline']: //待打基线
                $nextDealUser = $reviewInfo->qualityCm; //cm
                break;

            case $this->lang->review->statusList['reviewpass']: //打基线关闭
                $nextDealUser = '';
                break;

            case $this->lang->review->statusList['fail']: //放弃评审
            case $this->lang->review->statusList['drop']: //评审失败
                $nextDealUser = ''; //处理人是空
                break;

            case $this->lang->review->statusList['suspend']: //挂起
                $nextDealUser = $reviewInfo->reviewer; //评审专员
                break;

            default:
                break;
        }
        return $nextDealUser;
    }

    /**
     *检查当前审核节点是否需要修改资料
     *
     * @param $reviewId
     * @param $version
     * @param $nodeCode
     * @return false
     */
    public function checkCurrentNodeIsEditInfo($reviewId, $version, $nodeCode = ''){
        $res = false;
        if(!$reviewId){
            return $res;
        }
        $ret = $this->dao->select('t2.id')->from(TABLE_REVIEWNODE)->alias('t1')
            ->leftJoin(TABLE_REVIEWER)->alias('t2')
            ->on('t1.id = t2.node')
            ->where('t1.objectType')->eq('review')
            ->andWhere('t1.objectID')->eq($reviewId)
            ->andWhere('t1.version')->eq($version)
            ->andWhere('t1.status')->eq('pass')
            ->andWhere('t2.status')->eq('pass')
            ->beginIF($nodeCode)->andWhere('t1.nodeCode')->eq($nodeCode)->fi()
            ->andWhere('t2.extra')->like('%"isEditInfo":1%')
            ->fetch();
        if($ret){
            $res = true;
        }
        return $res;
    }



    /**
     * 检查当前审核节点是否需要修改资料按照父节点分组
     *
     * @param $nodeId
     * @param $parentIds
     * @return array
     */
    public function checkNodeIsEditInfoGroupByParentId($nodeId, $parentIds){
        $data = [];
        foreach ($parentIds as $parentId){
            $data[$parentId]['isEditInfo'] = 2;
        }
        if(!($nodeId)){
            return $data;
        }
        $ret = $this->dao->select('parentId')
            ->from(TABLE_REVIEWER)
            ->where('node')->eq($nodeId)
            ->andWhere('status')->eq('pass')
            ->andWhere('parentId')->in($parentIds)
            ->andWhere('extra')->like('%"isEditInfo":1%')
            ->groupBy('parentId')
            ->fetchAll();
        if(!$ret){
            return $data;
        }
        foreach ($ret as $val){
            $parentId = $val->parentId;
            $data[$parentId]['isEditInfo'] = 1;
        }
        return $data;
    }

    public function checkIssueStatus($reviewId){
        $res = false;
        if(!$reviewId){
            return $res;
        }
        $ret = $this->dao->select('count(1) as total')->from(TABLE_REVIEWISSUE)
            ->where('review')->eq($reviewId)
            ->andWhere('status')->eq('create')
            ->andWhere('deleted')->eq(0)
            ->fetch();
        if($ret->total == 0){
            $res = true;
        }
        return $res;
    }

    /**
     *获得审核后下一个状态
     *
     * @param $reviewInfo
     * @param $reviewAction
     * @param $extParams
     * @return string
     */
    public function getReviewNextStatus($reviewInfo, $reviewAction, $extParams = []){
        $nextStatus = '';
        if(!($reviewInfo && $reviewAction)){
            return $nextStatus;
        }
        //当前记录状态
        $status = $reviewInfo->status;
        if($reviewAction == 'reject'){
            if(in_array($status, $this->lang->review->allowPreReviewStatusList)){
                $nextStatus = $this->lang->review->statusList['rejectPre'];
            }elseif (in_array($status, $this->lang->review->allowFirstReviewStatusList)){
                $nextStatus = $this->lang->review->statusList['rejectFirst'];
            }elseif (in_array($status, $this->lang->review->allowFormalReviewStatusList)){
                $nextStatus = $this->lang->review->statusList['rejectFormal'];
            }elseif (in_array($status, $this->lang->review->allowFormalMeetingReviewStatusList)){
                $nextStatus = $this->lang->review->statusList['rejectMeeting'];
            }elseif (in_array($status, $this->lang->review->allowOutReviewStatusList)){
                $nextStatus = $this->lang->review->statusList['rejectOut'];
            }elseif (in_array($status, $this->lang->review->allowVerifyReviewStatusList)){
                $nextStatus = $this->lang->review->statusList['rejectVerify'];
            }elseif ($status == $this->lang->review->statusList['baseline']){ //待打基线驳回退回以后变成待归档
                $nextStatus = $this->lang->review->statusList['archive']; //待归档
            }
        }elseif($reviewAction == 'suspend'){ //挂起
            $nextStatus = $this->lang->review->statusList['suspend'];
        }elseif ($reviewAction == 'pass'){
            $nodeCode = $this->getReviewNodeCodeByStatus($status, $status);
            switch ($status){
                case $this->lang->review->statusList['waitPreReview']: //待QA预审
                    if((isset($extParams['isEditInfo'])) && ($extParams['isEditInfo'] == 1)){ //需要修改审核资料
                        $nextStatus = $this->lang->review->statusList['prePassButEdit']; //预审通过-待修改
                    }else{ //不需要修改审核资料
                        $nextStatus = $this->lang->review->statusList['waitFirstAssignDept']; //QA预审通过，待指派初审部门
                    }
                    break;

                case $this->lang->review->statusList['waitFirstAssignDept']:  //QA预审通过，待指派初审部门
                    if((isset($extParams['isFirstReview'])) && ($extParams['isFirstReview'] == 1)){
                        $nextStatus = $this->lang->review->statusList['waitFirstAssignReviewer']; //待指派审核人员
                    }else{
                        if($reviewInfo->type == $this->lang->review->typeValList['cbp']){ //金科初审
                            $nextStatus = $this->lang->review->statusList['waitOutReview']; //待外部审核
                        }else{
                            $nextStatus = $this->lang->review->statusList['waitFormalAssignReviewer']; //待评审主席指派正式审核人员
                        }
                    }
                    break;

                case $this->lang->review->statusList['waitFirstAssignReviewer']: //待指派初审核人员
                case $this->lang->review->statusList['firstAssigning']: //指派初审核人员中
                    $nextStatus = $this->lang->review->statusList['waitFirstReview']; //待初审
                    break;

                case $this->lang->review->statusList['waitFirstReview']: //待初审
                case $this->lang->review->statusList['firstReviewing']: //初审中
                    //$nextStatus = $this->lang->review->statusList['waitFirstMainReview']; //待初审-主审人员审核
                    $ret = $this->setSyncFirstMainReview($reviewInfo->id, $reviewInfo->version);
                    $nextStatus = $this->getSyncFirstMainReviewNextStatus($reviewInfo->id, $reviewInfo->type, $reviewInfo->version); //待初审-主审人员审核
                    break;

                case $this->lang->review->statusList['waitFirstMainReview']: //初审-待主审人员审核
                case $this->lang->review->statusList['firstMainReviewing']: //初审-主审人员审核中
                    //检查当前节点是否需要修改资料
                    $ret = $this->review->checkCurrentNodeIsEditInfo($reviewInfo->id, $reviewInfo->version, $nodeCode);
                    if($ret || ((isset($extParams['isEditInfo'])) && ($extParams['isEditInfo'] == 1))){ //需要修改审核资料(判断条件需要修改)
                        $nextStatus = $this->lang->review->statusList['firstPassButEdit']; //初审通过-待修改
                    }else{ //不需要修改审核资料
                        if($reviewInfo->type == $this->lang->review->typeValList['cbp']){ //金科初审
                            $nextStatus = $this->lang->review->statusList['waitOutReview']; //待外部审核
                        }else{
                            $nextStatus = $this->lang->review->statusList['waitFormalAssignReviewer']; //待评审主席指派正式审核人员
                        }
                    }
                    break;

                case $this->lang->review->statusList['waitFormalAssignReviewer']: //评审主席指派正式审核人员
                    if(isset($extParams['owner']) && ($reviewInfo->owner != $extParams['owner'])){ //评审主席做了修改（变为指派流程）
                        $nextStatus = $this->lang->review->statusList['waitFormalAssignReviewer']; //还是待评审主席指派人员
                    }else{
                        if((isset($extParams['type'])) && ($extParams['type'] ==  $this->lang->review->typeValList['cbp'])){ //金科初审
                            $nextStatus = $this->lang->review->statusList['waitOutReview']; //待外部审核
                        }else{
                            $nextStatus = $this->lang->review->statusList['waitFormalReview']; //正式审核专家在线评审
                        }
                    }
                    break;

                case $this->lang->review->statusList['waitFormalReview']: //正式人员审核
                case $this->lang->review->statusList['formalReviewing']: //正式人员审核中
                    if($reviewInfo->isSkipMeetingResult == 1){ //跳过评审主席确定线上评审结论
                        $nextStatus = $this->lang->review->statusList['waitMeetingReview']; //正式评审-待会议评审
                    }else{
                        $nextStatus = $this->lang->review->statusList['waitFormalOwnerReview']; //待评审主席确定线上评审结论
                    }
                    break;

                case $this->lang->review->statusList['waitFormalOwnerReview']: //待评审主席确定评审结论
                    $ret = $this->review->checkCurrentNodeIsEditInfo($reviewInfo->id, $reviewInfo->version, $nodeCode);
                    if($ret || ((isset($extParams['isEditInfo'])) && ($extParams['isEditInfo'] == 1))){ //需要修改审核资料(判断条件需要修改)
                        $nextStatus = $this->lang->review->statusList['formalPassButEdit']; //正式审核通过-待修改
                    } else{ //不需要修改审核资料
                        if((isset($extParams['grade'])) && ($extParams['grade'] == 'meeting')){ //又选择了会议评审
                            $nextStatus = $this->lang->review->statusList['waitMeetingReview'];  //正式评审-待会议评审
                        }else{
                            $nextStatus = $this->lang->review->statusList['pass']; //审核通过
                        }
                    }
                    break;

                case $this->lang->review->statusList['waitMeetingReview']: //待会议评审
                case $this->lang->review->statusList['meetingReviewing']: //待会议评审中
                    $nextStatus = $this->lang->review->statusList['waitMeetingOwnerReview']; //确定会议评审结论
                     break;

                case $this->lang->review->statusList['waitMeetingOwnerReview']: //待评审主席确定会议评审结论
                    $ret = $this->review->checkCurrentNodeIsEditInfo($reviewInfo->id, $reviewInfo->version, $nodeCode);
                    if($ret || ((isset($extParams['isEditInfo'])) && ($extParams['isEditInfo'] == 1))){ //需要修改审核资料(判断条件需要修改)
                        $nextStatus = $this->lang->review->statusList['meetingPassButEdit']; //正式会议审核通过-待修改
                    } else{ //不需要修改审核资料
                        $nextStatus = $this->lang->review->statusList['pass']; //审核通过
                    }
                    break;

                case $this->lang->review->statusList['waitVerify']: //待验证
                case $this->lang->review->statusList['verifying']: //验证中
                    if($reviewInfo->type == 'cbp'){
                        $nextStatus = $this->lang->review->statusList['waitOutReview'];
                    }elseif($reviewInfo->type == 'pro' && empty($this->checkFormalOrNot($reviewInfo->id, $reviewInfo->version))){
                        $nextStatus = $this->lang->review->statusList['waitFormalAssignReviewer']; //待评审主席指派正式审核人员
                    }else{
                        $nextStatus = $this->lang->review->statusList['pass']; //审核通过
                    }
                    break;

                case $this->lang->review->statusList['waitOutReview']: //待外审核
                case $this->lang->review->statusList['outReviewing']: //外部审核中
                    $ret = $this->review->checkCurrentNodeIsEditInfo($reviewInfo->id, $reviewInfo->version, $nodeCode);
                    if($ret || ((isset($extParams['isEditInfo'])) && ($extParams['isEditInfo'] == 1))){ //需要修改审核资料(判断条件需要修改)
                        $nextStatus = $this->lang->review->statusList['outPassButEdit']; //外部评审通过-待修改
                    } else{ //不需要修改审核资料
                        $nextStatus = $this->lang->review->statusList['pass']; //审核通过
                    }
                    break;

                case $this->lang->review->statusList['archive']: //待归档
                    $nextStatus = $this->lang->review->statusList['baseline']; //待打基线
                    break;

                case $this->lang->review->statusList['baseline']: //待打基线
                    $nextStatus = $this->lang->review->statusList['reviewpass']; //评审通过
                    break;

                default:
                    break;
            }
        }elseif ($reviewAction == 'part') {//部分操作成功
            $status = $reviewInfo->status;
            switch ($status){
                case $this->lang->review->statusList['waitFirstAssignReviewer']: //待指派初审核人员
                case $this->lang->review->statusList['firstAssigning']: //指派初审核人员中
                    $nextStatus = $this->lang->review->statusList['firstAssigning']; //指派初审核人员中
                    break;

                case $this->lang->review->statusList['waitFirstReview']: //待初审
                case $this->lang->review->statusList['firstReviewing']: //初审中

                    $nextStatus = $this->lang->review->statusList['firstReviewing']; //初审-参与人员审核中
                    break;

                case $this->lang->review->statusList['waitFirstMainReview']: //初审-待主审人员审核
                case $this->lang->review->statusList['firstMainReviewing']: //初审-主审人员审核中
                    $nextStatus = $this->lang->review->statusList['firstMainReviewing']; //初审-主审人员审核中
                    break;

                case $this->lang->review->statusList['waitFormalAssignReviewer']: //评审主席指派正式审核人员
                    $nextStatus = $this->lang->review->statusList['waitFormalAssignReviewer']; //评审主席指派正式审核人员
                    break;

                case $this->lang->review->statusList['waitFormalReview']: //正式人员审核
                case $this->lang->review->statusList['formalReviewing']: //正式人员审核中
                    $nextStatus = $this->lang->review->statusList['formalReviewing']; //正式人员审核中
                    break;

                case $this->lang->review->statusList['waitFormalOwnerReview']: //待评审主席确定审核结论
                    $nextStatus = $this->lang->review->statusList['waitFormalOwnerReview']; //待评审主席确定审核结论
                    break;

                case $this->lang->review->statusList['waitMeetingReview']: //待会议评审
                case $this->lang->review->statusList['meetingReviewing']: //待会议评审中
                    $nextStatus = $this->lang->review->statusList['meetingReviewing']; //待会议评审中
                    break;

                case $this->lang->review->statusList['waitMeetingOwnerReview']: //待评审主席确定会议审核结论
                    $nextStatus = $this->lang->review->statusList['waitMeetingOwnerReview']; //待评审主席确定会议审核结论
                    break;

                case $this->lang->review->statusList['waitVerify']: //待验证
                case $this->lang->review->statusList['verifying']: //验证中
                    $nextStatus = $this->lang->review->statusList['verifying']; //验证中
                    break;

                case $this->lang->review->statusList['waitOutReview']: //待外审核
                case $this->lang->review->statusList['outReviewing']: //外部审核中
                    $nextStatus = $this->lang->review->statusList['outReviewing']; //外部审核中
                    break;

                default:
                    break;
            }

        }
        return $nextStatus;
    }

    // 查找当前专业评审是否正式评审过
    public function checkFormalOrNot($reviewId, $version){
        $res = false;
        if(!$reviewId){
            return $res;
        }
        $ret = $this->dao->select('id')->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq('review')
            ->andWhere('objectID')->eq($reviewId)
            ->andWhere('version')->eq($version)
            ->andWhere('nodeCode')->eq('formalAssignReviewer')
            ->fetch();
        if($ret){
            $res = true;
        }
        return $res;
    }

    /**
     *返回驳回步骤id
     *
     * @param $status
     * @return int|void
     */
    public function getRejectStage($status){
        $rejectStage = $this->lang->review->rejectStageList[0];
        if(in_array($status, $this->lang->review->allowPreReviewStatusList)){
            $rejectStage = $this->lang->review->rejectStageList[1];
        }elseif (in_array($status, $this->lang->review->allowFirstReviewStatusList)){
            $rejectStage = $this->lang->review->rejectStageList[2];
        }elseif (in_array($status, $this->lang->review->allowFormalReviewStatusList)){
            $rejectStage = $this->lang->review->rejectStageList[3];  //正式评审线上退回
        }elseif (in_array($status, $this->lang->review->allowFormalMeetingReviewStatusList)){ //正式评审会议退回
            $rejectStage = $this->lang->review->rejectStageList[3];
        }elseif (in_array($status, $this->lang->review->allowOutReviewStatusList)){
            $rejectStage = $this->lang->review->rejectStageList[4];
        }elseif (in_array($status, $this->lang->review->allowVerifyReviewStatusList)){
            $rejectStage = $this->lang->review->rejectStageList[5];
        } elseif ($status == $this->lang->review->statusList['suspend']){ //挂起
            $rejectStage = $this->lang->review->rejectStageList[11];
        }
        return $rejectStage;
    }

    /**
     *获得撤销后的驳回节点
     *
     * @param $status
     * @return int|void
     */
    public function getRecallRejectStage($status){
        $rejectStage = $this->lang->review->rejectStageList[0];
        //指派或者审核
        if(in_array($status, $this->lang->review->allowReviewStatusList) || in_array($status, $this->lang->review->allowAssignStatusList)){
            if(in_array($status, $this->lang->review->allowReviewStatusList)){
                $rejectStage = $this->getRejectStage($status);
            }else{
                if(in_array($status, $this->lang->review->allowFirstAssignStatusList)){
                    $rejectStage = $this->lang->review->rejectStageList[1];
                }else{
                    $rejectStage = $this->lang->review->rejectStageList[2];
                }
            }

        }
        return $rejectStage;
    }

    /**
     * 删除审核节点以及审核人
     *
     * @param $objectID
     * @param $objectType
     * @param $version
     * @param $stage
     * @return bool|void
     */
    public function delReviewNode($objectID, $objectType, $version, $stage){
        $ret = $this->dao->select('id')->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq($objectType)
            ->andWhere('objectID')->eq($objectID)
            ->andWhere('version')->eq($version)
            ->andWhere('stage')->eq($stage)
            ->fetch();
        if(!$ret){
            return true;
        }
        $node = $ret->id;
        $this->dao->delete()->from(TABLE_REVIEWNODE)->where('id')->eq($node)->exec();
        $this->dao->delete()->from(TABLE_REVIEWER)->where('node')->eq($node)->exec();
        return true;
    }

    /**
     *删除审核节点以及审核人
     *
     * @param $objectID
     * @param $objectType
     * @param $version
     * @param $nodeCode
     * @return bool
     */
    public function delReviewNodeByNodeCode($objectID, $objectType, $version, $nodeCode){
        $ret = $this->dao->select('id')->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq($objectType)
            ->andWhere('objectID')->eq($objectID)
            ->andWhere('version')->eq($version)
            ->andWhere('nodeCode')->eq($nodeCode)
            ->fetch();
        if(!$ret){
            return true;
        }
        $node = $ret->id;
        $this->dao->delete()->from(TABLE_REVIEWNODE)->where('id')->eq($node)->exec();
        $this->dao->delete()->from(TABLE_REVIEWER)->where('node')->eq($node)->exec();
        return true;

    }


    /**
     *新增设置审核节点信息
     *
     * @param $reviewID
     * @param $objectType
     * @param $version
     * @param $reviewNodes
     * @param array $extParams
     * @return bool
     */
    public function submitReview($reviewID, $objectType, $version, $reviewNodes, $extParams = [])
    {
        $status = 'pending';
        //获得最小节点
        $stage  = 1;
        foreach($reviewNodes as $key => $currentNode) {
            $reviewers = $currentNode['reviewers'];
            if(!is_array($reviewers)){
                $reviewers = array($reviewers);
            }
            $reviewers = array_filter($reviewers);
            if(isset($currentNode['status'])){
                $status = $currentNode['status'];
            }else{
                if($key > 0){
                    $status = 'wait';
                    if(empty($reviewers)){
                        $status = 'ignore';
                    }
                }
            }
            if(isset($currentNode['stage'])){
                $stage = $currentNode['stage'];
            }
            //节点标识
            if(isset($currentNode['nodeCode'])){
                $extParams['nodeCode'] = $currentNode['nodeCode'];
            }
            if(isset($currentNode['isShow'])){
                $extParams['isShow'] = $currentNode['isShow'];
            }
            if(isset($currentNode['nodeId']) && $currentNode['nodeId']){ //只新增审核人信息
                $reviewerExtParams = zget($extParams, 'reviewerExtParams', []);
                $res = $this->loadModel('review')->addNodeReviewers($currentNode['nodeId'], $reviewers, true, $status, $reviewerExtParams);
            }else{ //新增审核节点和审核人信息
                $this->loadModel('review')->addNode($objectType, $reviewID, $version, $reviewers, true, $status, $stage, $extParams);
            }
            $stage++;
        }
        return true;
    }

    /**
     * Project: chengfangjinke
     * Method: review
     * User: wangjiurong
     * Year: 2022
     * Date: 2022/04/12
     * Time: 14:45
     * Desc: This is the code comment. This method is called review.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     *
     * @param $reviewID
     * @return array
     */
    public function review($reviewID){
        $reviewInfo = $this->getByID($reviewID);
        $version = $reviewInfo->version;
        //判断是否允许审核
        $res = $this->checkReviewIsAllowReview($reviewInfo, $this->app->user->account);
        if(!$res['result']){
            dao::$errors[] = $res['message'];
            return false;
        }

        //检查并且获得参数
        $res = $this->checkReviewParams($reviewInfo);
        if(!$res['result']){
            return false;
        }
        //工作量允许为0
       /* $consumed = $this->post->consumed;
        if($consumed != 0 || $consumed != '0.0'){
            $checkRes = $this->loadModel('consumed')->checkConsumedInfo($consumed);
            if(!$checkRes){
                return false;
            }
        }*/
        //数据
        $data = $res['data'];
        //评审结果
        $reviewResult = $data->result;
        $postUser = [];
        $isSyncProject = false;

        //审核人
        if(isset($data->verifyReviewers) && !empty($data->verifyReviewers)){
            $postUser = $data->verifyReviewers;
        }

        //扩展信息
        $extra = new stdClass();
        //评审时间
        $extra->reviewedDate = isset($data->reviewedDate) ? $data->reviewedDate : '';
        //是否需要修改资料
        $extra->isEditInfo = '';
        $extParams = [];
        if(isset($data->isEditInfo) && $data->isEditInfo){
            $extra->isEditInfo = $data->isEditInfo;
            $extParams['isEditInfo'] = $data->isEditInfo;
        }
        //评审方式
        if(isset($data->grade) && $data->grade){
            $extra->grade = $data->grade;
            $extParams['grade'] = $data->grade;
        }
        //验证人员指派
        if(in_array($reviewInfo->status, $this->lang->review->allowVerifyReviewStatusList)){
            if(isset($data->appointOther) && ($data->appointOther == 1)) {
                //新增验证委托人
                $res = $this->addAppointVerifyUser($reviewID, $version, $data->appointUser);
                if(!$res['result']){
                    dao::$errors[] = $res['message'];
                    return false;
                }
                $extra->appointUser = $data->appointUser;
                //验证人记录id
                $recordId = $res['data']['id'];
                $extra->subId = $recordId;
            }else{ //非指派
                $extra->subId = 0;
            }
        }

        //处理审核操作
        $is_all_check_pass = $this->getReviewIsAllUsersCheck($reviewInfo->status);
        $result = $this->loadModel('review')->check('review', $reviewID, $version, $reviewResult, $this->post->comment, $reviewInfo->reviewStage, $extra, $is_all_check_pass);
        if(!$result){
            dao::$errors[] = $this->lang->review->checkResultList['opError'];
            return false;
        }

        $oldStatus = $reviewInfo->status;
        //下一个状态
        $nextStatus = $this->getReviewNextStatus($reviewInfo, $result, $extParams);
        //修改信息
        $updateParams = new stdClass();
        $updateParams->status = $nextStatus;
        $updateParams->lastReviewedBy   = $this->app->user->account;
        $updateParams->lastReviewedDate = isset($data->reviewedDate)?$data->reviewedDate:helper::today();

        //验证截止时间
        if(isset($data->verifyDeadline) && $data->verifyDeadline){
            $updateParams->verifyDeadline  = $data->verifyDeadline;
        }
        //提交时间和结束时间
        if(in_array($nextStatus, $this->lang->review->sumitDateStatusList)){
            $updateParams->submitDate = helper::now();
        }
        if($nextStatus == 'waitFirstMainReview' or $nextStatus == 'firstMainReviewing' OR (in_array($oldStatus, $this->lang->review->allowFirstJoinReviewStatusList) && $result == 'pass')){
            $endDate = $reviewInfo->endDate;
        }else{
            if(isset($updateParams->submitDate)){
                $endDate = $this->getEndDate($nextStatus,$updateParams->submitDate, $reviewInfo);
            }
        }
        if(isset($endDate) && in_array($nextStatus, $this->lang->review->sumitDateStatusList)){
            $updateParams->endDate = $endDate;
        }

        //评审方式
        if(isset($data->grade) && $data->grade){
            if(!in_array($oldStatus, $this->lang->review->allowFirstReviewStatusList)){ //初审时设置了评审方式也不修改
                $updateParams->grade  = $data->grade;
            }
        }
        //会议评审预计时间
        if(isset($data->meetingPlanTime) && $data->meetingPlanTime){
            $updateParams->meetingPlanTime  = $data->meetingPlanTime;
        }

        if($result == 'reject'){
            //审核步骤reviewStage获取
            $rejectStage = $this->getRejectStage($oldStatus);
            $updateParams->rejectStage = $rejectStage;
        }
        //评审主席确定线上评审结论时选择了会议评审
        if($oldStatus ==  $this->lang->review->statusList['waitFormalOwnerReview']){
            if($nextStatus == $this->lang->review->statusList['waitMeetingReview']){
                //会议评审
                $meetingPlanExport = $this->getReviewMeetingPlanExportUsers($reviewInfo->expert, $reviewInfo->reviewedBy, $reviewInfo->outside);
                $updateParams->meetingPlanExport = $meetingPlanExport;
                $updateParams->grade = 'meeting';

            }else{ //非会议评审
                if($reviewInfo->grade == 'meeting'){
                    $updateParams->grade = 'online';
                }
            }
        } elseif ($oldStatus ==  $this->lang->review->statusList['archive']) { //待归档
            $isShowSafetyTest = $this->review->isShowSafetyTest($reviewInfo->object, $reviewInfo->type);
            if($isShowSafetyTest){
                $updateParams->isSafetyTest      = $data->isSafetyTest;
                $updateParams->isPerformanceTest = $data->isPerformanceTest;
                //同步项目信息
                $isSyncProject = true;
                $projectParams = new stdClass();
                $projectParams->isSafetyTest = $data->isSafetyTest;
                $projectParams->isPerformanceTest = $data->isPerformanceTest;
            }
        } elseif ($oldStatus ==  $this->lang->review->statusList['baseline']) { //待打基线
            $updateParams->baseLineCondition = $data->baseLineCondition;
            $updateParams->baseLineType = $data->baseLineType;
            $updateParams->baseLinePath = $data->baseLinePath;
            $updateParams->baseLineTime = $data->baseLineTime;
            if ($nextStatus == $this->lang->review->statusList['archive']) { //返回到打基线
                $nextDealUser = $this->getNextDealUser($reviewInfo, $nextStatus);
                $version = $version + 1;
                $updateParams->version = $version;
                $updateParams->dealUser = $nextDealUser;
            }
        }
        //修改评审表
        $this->dao->update(TABLE_REVIEW)->data($updateParams)->where('id')->eq($reviewID)->exec();
        if(dao::isError()) {
            return false;
        }

        //工时
        if(isset($extra->appointUser)){
            $consumedExtra = $extra;
        }elseif($oldStatus == $this->lang->review->statusList['baseline']){ //待打基线
            $consumedExtra = new stdClass();
            $consumedExtra->isReject = $data->isReject; //是否退回
            $consumedExtra->baseLineCondition = $data->baseLineCondition; //是否打基线
        }else{
            $consumedExtra = $extra->isEditInfo;
        }
        if($nextStatus == 'verifying'){
            $result = $data->result == 'pass' ? $data->result : 'fail';
            //记录工时信息
            $this->loadModel('consumed')->record('review', $reviewID, '0', $this->app->user->account, $oldStatus, $data->result, $this->post->mailto,  $consumedExtra);
        } else{
            //记录工时信息
            $this->loadModel('consumed')->record('review', $reviewID, '0', $this->app->user->account, $oldStatus, $nextStatus, $this->post->mailto,  $consumedExtra);
        }
        //审核时考虑是否忽略了确定线上评审结论(如果忽略增加忽略节点)
        if(($reviewInfo->isSkipMeetingResult == 1) && ($nextStatus == $this->lang->review->statusList['waitMeetingReview'])){
            $maxStage = $this->loadModel('review')->getReviewMaxStage($reviewInfo->id, 'review', $version);
            $stage    = $maxStage + 1;
            $nodeCode = $this->lang->review->nodeCodeList['formalOwnerReview'];
            //新增审核节点
            $reviewNodes = array(
                array(
                    'reviewers' => array($reviewInfo->owner),
                    'stage'     => $stage,
                    'status'    => 'ignore',
                    'nodeCode'  => $nodeCode,
                )
            );
            $this->submitReview($reviewInfo->id, 'review', $version, $reviewNodes);
        }

        //是否需要增加审核节点
        $isAddNode = $this->getIsAddReviewNode($nextStatus);
        if($isAddNode){
            $newReviewInfo = $this->getByID($reviewID);
            if($newReviewInfo->version != $reviewInfo->version){ //版本不一样
                $res = $this->addNewVersionReviewNodes($newReviewInfo, $oldStatus);

            }else{
                $res = $this->addReviewNode($newReviewInfo, $oldStatus, $postUser);
            }
        }

        //提前增加验证审核节点
        if(in_array($nextStatus, $this->lang->review->allowAdvanceSetReviewersStatusList)){
            $maxStage = $this->loadModel('review')->getReviewMaxStage($reviewInfo->id, 'review', $version);
            $stage    = $maxStage + 1;
            $nodeCode = $this->lang->review->nodeCodeList['verify'];
            //新增审核节点
            $reviewNodes = array(
                array(
                    'reviewers' => $postUser,
                    'stage'     => $stage,
                    'status'    => 'wait',
                    'nodeCode'  => $nodeCode,
                )
            );
            $this->submitReview($reviewInfo->id, 'review', $version, $reviewNodes);
        }

        if($result == 'pass' || $nextStatus == $this->lang->review->statusList['rejectVerify']) { //审核通过或者验证驳回
            $ret = $this->loadModel('review')->setNextReviewNodePending('review', $reviewID, $version);
        }

        //处理人
        $newReviewInfo = $this->getByID($reviewID);
        $nextDealUser = $this->getNextDealUser($newReviewInfo, $newReviewInfo->status);

        if($nextDealUser != $newReviewInfo->dealUser) {
            $tempUpdateParams = new stdClass();
            $tempUpdateParams->dealUser = $nextDealUser;
            $this->dao->update(TABLE_REVIEW)->data($tempUpdateParams)->where('id')->eq($reviewID)->exec();
            $updateParams->dealUser = $nextDealUser;
        }


        //增加到会议评审
        if($nextStatus == $this->lang->review->statusList['waitMeetingReview']){ //下一个状态是会议评审
            // 查询同会议单号中是否已有填写过会议纪要的评审
            $meetResult = $this->dao->select('id')->from(TABLE_REVIEW_MEETING_DETAIL)
                ->where('meetingCode')->eq($reviewInfo->meetingCode)
                ->andWhere('status')->eq($this->lang->review->statusList['waitMeetingOwnerReview'])
                ->fetch('id');
            // 如果有说明是后入会评审
            if(!empty($meetResult)){
                $meetInfo = $this->dao->select('id,reviewer,status')->from(TABLE_REVIEW_MEETING)
                    ->where('meetingCode')->eq($reviewInfo->meetingCode)
                    ->fetch();
                $detailInfoStatus = $this->lang->review->statusList['waitMeetingReview'];
                $detailInfoDealUser = $meetInfo->reviewer;
                $this->dao->update(TABLE_REVIEW_MEETING_DETAIL)->set('status')->eq($detailInfoStatus)
                    ->where('review_meeting_id')->eq($meetInfo->id)
                    ->andWhere('review_id')->eq($reviewInfo->id)
                    ->exec();
                $this->dao->update(TABLE_REVIEW_MEETING)->set('dealUser')->eq($detailInfoDealUser)->set('status')->eq($detailInfoStatus)
                    ->where('id')->eq($meetInfo->id)
                    ->exec();
                // 新加会议节点
                $maxStage = $this->loadModel('review')->getReviewMaxStage($meetInfo->id, 'reviewmeeting', $version);
                $stage    = $maxStage + 1;
                $nodeCode = $this->lang->review->nodeCodeList['meetingReview'];
                //新增审核节点
                $reviewNodes = array(
                    array(
                        'reviewers' => array($detailInfoDealUser),
                        'stage'     => $stage,
                        'status'    => 'pending',
                        'nodeCode'  => $nodeCode,
                    )
                );
                $this->submitReview($meetInfo->id, 'reviewmeeting', 0, $reviewNodes);

                // 状态流转
                $this->loadModel('consumed')->record('reviewmeeting', $meetInfo->id, '0', $this->app->user->account, $meetInfo->status, $detailInfoStatus);

                // 新增会议历史记录
                $comment = $this->lang->review->commenthistory1.$reviewInfo->title.$this->lang->review->commenthistory2;
                $this->loadModel('action')->create('reviewmeeting', $meetInfo->id, 'reviewed', $comment, '', '', true);
            }else{
                $res = $this->opMeetingReview($reviewInfo, $nextStatus, $data);
                if(!$res['result']){
                    dao::$errors[] = $res['message'];
                    return false;
                }
                //有返回数据
                if(isset($res['data']) && !empty($res['data'])){
                    $meetingData = $res['data'];
                    if(isset($meetingData->meetingCode)){
                        $meetingCode = $meetingData->meetingCode;
                    }
                }
            }
        }else{ //下一个状态是非会议评审中
            if($oldStatus == $this->lang->review->statusList['waitFormalOwnerReview']){ //会议评审，上一个状态是评审主席确定线上评审结论，且下一个状态非会议评审（即会议评审后续不需要会议评审环节）
                //解除绑定
                if($reviewInfo->meetingCode) {
                    $res = $this->loadModel('reviewmeeting')->cancelBindReviewMeeting($reviewInfo->meetingCode, $reviewID);
                    if(!$res['result']){
                        dao::$errors[] = $res['message'];
                        return false;
                    }
                }

            }
        }
        //待归档,添加规档信息
        if($oldStatus ==  $this->lang->review->statusList['archive']){
            $svnUrlArray     = $data->svnUrl;
            $svnVersionArray = $data->svnVersion;
            foreach($svnUrlArray as $key => $svnUrl){
                $archiveParams = new stdClass();
                $archiveParams->svnUrl = $svnUrl;
                $archiveParams->svnVersion = $svnVersionArray[$key];
                $this->loadModel('archive')->addArchiveInfo($reviewInfo->project, 'review', $reviewID, $reviewInfo->version, $archiveParams);
            }
            if($isSyncProject){
                $projectComment = sprintf($this->lang->review->syncProjectCommnet, $reviewID);
                $this->loadModel('project')->updateProject($reviewInfo->project, $projectParams, $projectComment);
            }

        } elseif ($oldStatus ==  $this->lang->review->statusList['baseline']){ //待打基线
            if($data->baseLineCondition == 'yes'){
                $this->addBaseLine($reviewID);
            }
        }
        //增加项目白名单
        if(!empty($postUser)){
            foreach ($postUser as $userAccount){
                $projectId = $reviewInfo->project;
                $res = $this->addProjectReviewWhitelist($projectId, $reviewID, $userAccount);
            }
        }


        //获得差异信息
        $extChangeInfo = [];
        //抄送人
        $ext = new stdClass();
        $ext->old = '';
        $ext->new = isset($_POST['mailto'])  ?  implode(' ',$_POST['mailto']) :'';
        $extChangeInfo['mailto'] = $ext;
        //委托用户
        if(isset($data->appointOther) && ($data->appointOther == 1)) {
            $appointUserObj = new stdClass();
            $appointUserObj->old = '';
            $appointUserObj->new =$data->appointUser;
            $extChangeInfo['appointUser'] = $appointUserObj;
        }

        //验证人
        if(isset($data->verifyReviewers) && !empty($data->verifyReviewers)){
            $verifyReviewersObj = new stdClass();
            $verifyReviewersObj->old = '';
            $verifyReviewersObj->new =implode(',', $data->verifyReviewers);
            $extChangeInfo['verifyReviewers'] = $verifyReviewersObj;
        }
        if(isset($meetingCode)){
            $updateParams->meetingCode = $meetingCode;
        }
        $logChange = common::createChanges($reviewInfo, $updateParams, $extChangeInfo);

        $extra = '';
        $updateFields = array_column($logChange, 'field');
        if(in_array('meetingCode', $updateFields)){
            $meetingCode = $this->loadModel('review')->getMeetingCodeInLogChanges($logChange);
            if($meetingCode){
                $extra = '绑定会议，会议单号：'.$meetingCode;
            }
        }
        //日志扩展信息
        $isSetHistory = true;
        $actionID = $this->loadModel('action')->create('review', $reviewID, 'reviewed', $this->post->comment, $extra, '', true, $isSetHistory, $logChange);

        //自动关闭
        if($nextStatus == 'pass'){
            $this->autoclose($reviewID);
        }
        return $logChange;
    }


    // 自动流转验证流程
    public function reviewVerify($reviewID, $reviewResult, $user = ''){
        $reviewInfo = $this->getByID($reviewID);
        if($reviewInfo->status != 'waitVerify' && $reviewInfo->status != 'verifying') return false;
        $version = $reviewInfo->version;

        //工作量为0.5
        $consumed = 0;//0.5;


        //处理审核操作
        $result = $this->loadModel('review')->checkVerify($reviewID, $version, $reviewResult, $user);
        $user = empty($user) ? $this->app->user->account : $user;

        if(!$result){
            dao::$errors[] = $this->lang->review->checkResultList['opError'];
            return false;
        }

        $oldStatus = $reviewInfo->status;
        //下一个状态
        $nextStatus = $this->getReviewNextStatus($reviewInfo, $result, []);
        //修改信息
        $updateParams = new stdClass();
        $updateParams->status = $nextStatus;
        $updateParams->lastReviewedBy   = $user;
        $updateParams->lastReviewedDate = helper::now();

        if($result == 'reject'){
            //审核步骤reviewStage获取
            $rejectStage = $this->getRejectStage($oldStatus);
            $updateParams->rejectStage = $rejectStage;
        }

        //修改评审表
        $this->dao->update(TABLE_REVIEW)->data($updateParams)->where('id')->eq($reviewID)->exec();
        if(dao::isError()) {
            return false;
        }

        if($nextStatus == 'verifying'){
            //记录工时信息
            $this->loadModel('consumed')->record('review', $reviewID, $consumed, $user, $oldStatus, $reviewResult);
        } else{
            //记录工时信息
            $this->loadModel('consumed')->record('review', $reviewID, $consumed, $user, $oldStatus, $nextStatus);
        }

        if($result == 'pass' || $nextStatus == $this->lang->review->statusList['rejectVerify']) { //审核通过或者验证驳回
            $ret = $this->loadModel('review')->setNextReviewNodePending('review', $reviewID, $version);
        }

        //处理人
        $newReviewInfo = $this->getByID($reviewID);
        $nextDealUser = $this->getNextDealUser($newReviewInfo, $newReviewInfo->status);

        if($nextDealUser != $newReviewInfo->dealUser) {
            $tempUpdateParams = new stdClass();
            $tempUpdateParams->dealUser = $nextDealUser;
            $this->dao->update(TABLE_REVIEW)->data($tempUpdateParams)->where('id')->eq($reviewID)->exec();
            $updateParams->dealUser = $nextDealUser;
        }

        //增加项目白名单
        if(!empty($postUser)){
            foreach ($postUser as $userAccount){
                $projectId = $reviewInfo->project;
                $res = $this->addProjectReviewWhitelist($projectId, $reviewID, $userAccount);
            }
        }

        // 记录历史记录
        $changes = common::createChanges($reviewInfo, $updateParams);
        $closeID = $this->loadModel('action')->create('review', $reviewID, 'autoverify','','','','','',$changes);
        $this->action->logHistory($closeID, $changes);
        // 自动关闭
        if($nextStatus == 'pass'){
            $this->autoclose($reviewID);
        }
    }

    /**
     * 初审和主审人员一致时，审核两次
     * @param $reviewID
     * @param $postData
     * @return false
     */
    public function setFirstReviewResult($reviewID, $postData){
        $reviewInfo = $this->getByID($reviewID);
        $reviewResult = $postData->result;;
        $version = $reviewInfo->version;
        //扩展信息
        $extra = new stdClass();
        //评审时间
        $extra->reviewedDate = $postData->reviewedDate;
        //是否需要修改资料
        $extra->isEditInfo = '';
        $extParams = [];
        $postUser = [];

        $extParams = [];
        if(isset($postData->isEditInfo) && $postData->isEditInfo){
            $extra->isEditInfo = $postData->isEditInfo;
            $extParams['isEditInfo'] = $postData->isEditInfo;
        }
        //评审方式
        if(isset($postData->grade) && $postData->grade){
            $extra->grade = $postData->grade;
            $extParams['grade'] = $postData->grade;
        }
        //处理审核操作
        $result = $this->loadModel('review')->check('review', $reviewID, $version, $reviewResult, $postData->comment, $reviewInfo->reviewStage, $extra);
        if(!$result){
            dao::$errors[] = $this->lang->review->checkResultList['opError'];
            return false;
        }
        $oldStatus = $reviewInfo->status;
        //下一个状态
        $nextStatus = $this->getReviewNextStatus($reviewInfo, $result, $extParams);
        //修改信息
        $allowReviewStatusList = $this->lang->review->allowReviewStatusList;
        $allowAssignStatusList = $this->lang->review->allowAssignStatusList;
        $status = $reviewInfo->status;
        $dealUserTemp = $reviewInfo->reviewers;
        if(in_array($status, $allowReviewStatusList) || in_array($status, $allowAssignStatusList)){
            $dealUserTemp = $reviewInfo->reviewers;
        }
        $dealUsersArray = explode(',', $dealUserTemp);
        if(count($dealUsersArray) == 1 and ($reviewInfo->status == 'waitFirstMainReview' or $reviewInfo->status == 'firstMainReviewing')  and $reviewInfo->type == 'cbp' and  $postData->isEditInfo == 1 ){
            $nextStatus = 'firstPassButEdit';
        }
        $updateParams = new stdClass();
        $updateParams->status = $nextStatus;
        $updateParams->lastReviewedBy   = $this->app->user->account;
        $updateParams->lastReviewedDate = $postData->reviewedDate;

        if(in_array($nextStatus, $this->lang->review->sumitDateStatusList)){
            $updateParams->submitDate = helper::now();
        }
        if($nextStatus =='waitFirstMainReview' or $nextStatus == 'firstMainReviewing' OR (in_array($oldStatus, $this->lang->review->allowFirstJoinReviewStatusList) && $result == 'pass')){
            $endDate = $reviewInfo->endDate;
        }else{
            if(isset($updateParams->submitDate)){
                $endDate = $this->getEndDate($nextStatus,$updateParams->submitDate, $reviewInfo);
            }
        }
        if(isset($endDate) && in_array($nextStatus, $this->lang->review->sumitDateStatusList)){
            $updateParams->endDate = $endDate;
        }

        $this->dao->update(TABLE_REVIEW)->data($updateParams)->where('id')->eq($reviewID)->exec();
        if(dao::isError()) {
            return false;
        }

        //是否需要增加审核节点
        $isAddNode = $this->getIsAddReviewNode($nextStatus);
        //判断外部评审是否需要增加节点(在初审确定结论时新增节点)
        if($isAddNode){
            $newReviewInfo = $this->getByID($reviewID);
            $res = $this->addReviewNode($newReviewInfo, $oldStatus, $postUser);
        }
        //需要增加验证节点
        if(in_array($nextStatus, $this->lang->review->allowAdvanceSetReviewersStatusList)){
            $maxStage = $this->loadModel('review')->getReviewMaxStage($reviewInfo->id, 'review', $version);
            $stage    = $maxStage + 1;
            $nodeCode = $this->lang->review->nodeCodeList['verify'];
            //新增审核节点
            $reviewNodes = array(
                array(
                    'reviewers' => $postUser,
                    'stage'     => $stage,
                    'status'    => 'wait',
                    'nodeCode'  => $nodeCode,
                )
            );
            $this->submitReview($reviewInfo->id, 'review', $version, $reviewNodes);
        }


        if($result == 'pass') {
            $next = $this->dao->select('id')->from(TABLE_REVIEWNODE)->where('objectType')->eq('review')
                ->andWhere('objectID')->eq($reviewID)
                ->andWhere('version')->eq($version)
                ->andWhere('status')->eq('wait')->orderBy('stage,id')->fetch('id');
            //有其他审核节点
            if($next) {
               $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('pending')->where('id')->eq($next)->exec();
                $this->dao->update(TABLE_REVIEWER)->set('status')->eq('pending')->where('node')->eq($next)->exec();

            }
        }


        //处理人
        $newReviewInfo = $this->getByID($reviewID);
        if(count($dealUsersArray) == 1 and ($nextStatus == 'waitOutReview' or $nextStatus=='pass') and $newReviewInfo->type == 'cbp'){
            $nextDealUser = $reviewInfo->qa;
        }else{
            $nextDealUser = $this->getNextDealUser($newReviewInfo, $newReviewInfo->status);
        }
        if($nextDealUser != $newReviewInfo->dealUser){
            $tempUpdateParams = new stdClass();
            $tempUpdateParams->dealUser = $nextDealUser;
            $this->dao->update(TABLE_REVIEW)->data($tempUpdateParams)->where('id')->eq($reviewID)->exec();
            $updateParams->dealUser = $nextDealUser;
        }
        //增加项目白名单
        if(!empty($postUser)){
            foreach ($postUser as $userAccount){
                $projectId = $reviewInfo->project;
                $res = $this->addProjectReviewWhitelist($projectId, $reviewID, $userAccount);
            }
        }

        //增加到会议评审
        if($nextStatus == $this->lang->review->statusList['waitMeetingReview']){ //下一个状态是会议评审
            $res = $this->opMeetingReview($reviewInfo, $nextStatus, $postData);
            if(!$res['result']){
                dao::$errors[] = $res['message'];
                return false;
            }
            //有返回数据
            if(isset($res['data']) && !empty($res['data'])){
                $meetingData = $res['data'];
                if(isset($meetingData->meetingCode)){
                        $meetingCode = $meetingData->meetingCode;
                }
            }
        }else{ //下一个状态是非会议评审中
            if(($reviewInfo->meetingCode) && ($oldStatus == $this->lang->review->statusList['waitFormalOwnerReview'])){ //会议评审，上一个状态是评审主席确定线上评审结论，且下一个状态非会议评审（即会议评审后续不需要会议评审环节）
                //解除绑定
                $res = $this->loadModel('reviewmeeting')->cancelBindReviewMeeting($reviewInfo->meetingCode, $reviewID);
                if(!$res['result']){
                    dao::$errors[] = $res['message'];
                    return false;
                }
            }
        }
        return true;

    }

    /**
     * 操作会议评审信息
     *
     * @param $reviewInfo
     * @param $nextStatus
     * @param $params
     * @return array
     */
    public function opMeetingReview($reviewInfo, $nextStatus, $params){
        $res = array(
            'result'  => false,
            'message' => '',
        );
        if(!($reviewInfo && $nextStatus && $params)){
            $res['message'] = '参数错误';
            return $res;
        }
        $reviewID = $reviewInfo->id;
        //绑定或者修改下一个状态
        if(isset($params->meetingPlanType)){
            if($params->meetingPlanType == 1){ //已选择会议
                if($params->meetingCode == $reviewInfo->meetingCode){
                    $res = $this->loadModel('reviewmeeting')->updateReviewMeetingDetailStatus($reviewID, $nextStatus); //修改会议状态
                    return $res;
                }else{
                    if($reviewInfo->meetingCode){ //解绑旧的
                        $res = $this->loadModel('reviewmeeting')->cancelBindReviewMeeting($reviewInfo->meetingCode, $reviewID);
                        if(!$res['result']){
                            return  $res;
                        }
                    }
                    //绑定新的
                    $res = $this->loadModel('reviewmeeting')->bindToReviewMeeting($params->meetingCode, $reviewInfo, $nextStatus);
                    return $res;
                }
            }else if($params->meetingPlanType == 2){ //当选择新建会议
                if($reviewInfo->meetingCode){ //解绑旧的
                    $res = $this->loadModel('reviewmeeting')->cancelBindReviewMeeting($reviewInfo->meetingCode, $reviewID);
                    if(!$res['result']){
                        return  $res;
                    }
                }
                //新增会议评审
                $res = $this->loadModel('reviewmeeting')->createReviewMeeting($reviewInfo, $nextStatus, $params->meetingPlanTime);
                return $res;
            }else if($params->meetingPlanType == 3) { //当选择暂不排期
                if($reviewInfo->meetingCode){
                    $res = $this->loadModel('reviewmeeting')->cancelBindReviewMeeting($reviewInfo->meetingCode, $reviewID);
                    return $res;
                }
            }
        }else{
            //原来就是会议评审
            if($reviewInfo->meetingCode){
                $res = $this->loadModel('reviewmeeting')->updateReviewMeetingDetailStatus($reviewID, $nextStatus); //修改会议状态
                return $res;
            }
        }
        //返回
        $res['result'] = true;
        return $res;
    }

    /**
     * 新增委托验证人员
     *
     * @param $reviewID
     * @param $version
     * @param $reviewer
     * @return array
     */
    public function addAppointVerifyUser($reviewID, $version, $reviewer){
        $res = [
            'result'  => false,
            'message' => '',
            'data'    => [],
        ];
        if(!($reviewID && $reviewer)){
            $res['message'] = '参数错误';
            return  $res;
        }
        //处理中
        $status = 'pending';
        $extParams = [
            'status'   => $status,
            'nodeCode' => 'verify',
        ];
        $nodeIds = $this->loadModel('review')->getReviewerNodeIds('review', $reviewID, $version, $extParams);
        if(empty($nodeIds)){
            $res['message'] = $this->lang->review->checkAssignOpResultList['reviewNodeEmpty'];
            return  $res;
        }
        //取第一个审核节点
        $nodeId = $nodeIds[0];
        //查询是否存在当前操作人记录
        $reviewerInfo = $this->dao->select('id')->from(TABLE_REVIEWER)
            ->where('node')->eq($nodeId)
            ->andWhere('reviewer')->eq($this->app->user->account)
            ->andWhere('status')->eq($status)
            ->fetch();

        if(!$reviewerInfo){
            $res['message'] = $this->lang->review->checkAssignOpResultList['addAppointUsersError'];
            return  $res;
        }
        $parentId  = $reviewerInfo->id;
        //查询指派人员是否存在
        $appointReviewerInfo = $this->dao->select('id, parentId')->from(TABLE_REVIEWER)
            ->where('node')->eq($nodeId)
            ->andWhere('reviewer')->eq($reviewer)
            ->andWhere('status')->eq($status)
            ->fetch();
        if($appointReviewerInfo){
            $id = $appointReviewerInfo->id;
            if($appointReviewerInfo->parentId == 0){
                $updateParams = new stdClass();
                $updateParams->parentId = $parentId;
                $this->dao->update(TABLE_REVIEWER)->data($updateParams)->where('id')->eq($id)->exec();
                if(dao::isError()){
                    $res['message'] = $this->lang->review->checkAssignOpResultList['addAppointUsersError'];
                    return  $res;
                }
            }
        }else{
            $user = new stdClass();
            $user->parentId    = $parentId;
            $user->node        = $nodeId;
            $user->reviewer    = $reviewer;
            $user->status      = $status;
            $user->createdBy   = $this->app->user->account;
            $user->createdDate = helper::today();
            $this->dao->insert(TABLE_REVIEWER)->data($user)->exec();
            if(dao::isError()){
                $res['message'] = $this->lang->review->checkAssignOpResultList['addAppointUsersError'];
                return  $res;
            }
            //返回
            $id = $this->dao->lastInsertID();
        }
        $data = [
            'id' => $id,
        ];
        $res['result'] = true;
        $res['data']   = $data;
        return $res;
    }

    /**
     * Desc: 状态流转为打基线的数据入库操作
     * Date: 2022/6/26
     * Time: 13:44
     *
     * @param $reviewID
     * @param $data
     * @param $consumed
     *
     */
    public function updateData($reviewID,$data,$consumed)
    {
        $data->dealUser = '';
        $data->status = 'reviewpass';
        $this->dao->update(TABLE_REVIEW)->data($data)->autoCheck()
            ->where('id')->eq($reviewID)
            ->exec();
        $updateData = new stdClass();
        $updateData->status = 'pass';
        $this->dao->update(TABLE_REVIEWNODE)->data($updateData)
            ->autoCheck()
            ->where('objectID')->eq($reviewID)
            ->andWhere('nodeCode')->eq('baseline')
            ->exec();
        $reviewerId = $this->dao->select('id')->from(TABLE_REVIEWNODE)->where('objectID')->eq($reviewID)
            ->andWhere('nodeCode')->eq('baseline')
            ->orderBy('id desc')
            ->fetch('id');

        //增加备注字段
        $updateData->comment = $this->post->comment;
        $this->dao->update(TABLE_REVIEWER)->data($updateData)
            ->autoCheck()
            ->where('node')->eq($reviewerId)
            ->exec();
        //状态流转
        $consumedData = new stdClass();
        $consumedData->objectType = 'review';
        $consumedData->objectID = $reviewID;
        $consumedData->consumed = $consumed;
        $consumedData->account = $this->app->user->account;
        $consumedData->before = 'baseline';
        $consumedData->after = $data->baseLineCondition;
        $consumedData->createdBy = $this->app->user->account;
        $consumedData->createdDate = $data->baseLineTime;
        $this->dao->insert(TABLE_CONSUMED)->data($consumedData)
            ->autoCheck()
            ->exec();

    }

    /**
     * 基线情况：打基线 相关数据入库baseline
     * @param $reviewID
     */
    public function addBaseLine($reviewID)
    {
        $review = $this->getByID($reviewID);
        $baseLineType = explode(',',$review->baseLineType);
        $baseLinePath =  explode(',',$review->baseLinePath);
        $member =  $this->loadModel('project')->getTeamMembers($review->project);//团队
        $member = array_column($member,'role','account');

        foreach($review->objects as $object)
        {
            $objects[] = zget($this->lang->review->objectList,$object,'');
        }
        $itemTitle = trim(implode(',',$objects),',');

        $proj = $this->loadModel('project')->getByID($review->project);
        foreach ($baseLineType as $key => $item) {
            $title = $this->cut_str($baseLinePath[$key],"/",-1); //取最后

            $baseline = new stdClass();
            $baseline->title = $title;
            $baseline->type  = $item;
            $baseline->cm = $this->app->user->account;
            $baseline->cmDate = $review->baseLineTime;
            $baseline->reviewer = $this->lang->review->reviewerName;
            $baseline->reviewedDate = helper::now();
            $baseline->project = $review->project;
            $baseline->objectType = 'review';
            $baseline->objectID   = $reviewID;
            $baseline->version    = $review->version;
            $baseline->createdDate = helper::today();
            $baseline->createdBy = $this->app->user->account;

            $project = new stdclass();
            foreach ($member as $k=>$value) {
                $val = explode(',',$value);
                if(in_array('2',$val)){
                    $PM = $k;//项目经理2
                }
                if (in_array('11',$val)){
                    $QA = $k;//质量保证工程师11
                }
                if (in_array('1',$val)){
                    $PO = $k;//项目主管1
                }
            }
            $project->PM =  !empty($PM) ? $PM : $proj->PM;//项目经理2
            $project->QA =  !empty($QA) ? $QA : $proj->QA;//质量保证工程师11
            $project->PO =  !empty($PO) ? $PO : $proj->PO;//项目主管1

            $pathFinal = explode('/',$baseLinePath[$key]);
            krsort($pathFinal);
            $checkPath = array_values($pathFinal)[0];

            $item = new stdClass();
            $item->title = $itemTitle;
            $item->code = '';//空
            $item->version = $this->cut_str($checkPath,'_',3);
            $item->changed = '0';
            $item->changedDate = '';
            $item->path = $baseLinePath[$key];
            $item->comment = $this->lang->review->commentDesc;

            //存基线表
            $this->dao->insert(TABLE_BASELINE)->data($baseline)->autoCheck()->exec();
            $baselineID = $this->dao->lastInsertID();

            //存配置表
            $item->baseline = $baselineID;
            $this->dao->insert(TABLE_CMITEM)->data($item)->exec();

            //更新项目表
            $this->dao->update(TABLE_PROJECT)->data($project)->where('id')->eq($review->project)->exec();
        }
    }

    /**
     * 按符号截取字符串
     * @param string $str 需要截取字符串
     * @param string $sign 符号
     * @param int $number 正数从左向右，负数从右向左
     * @return string ·返回
     */
    function cut_str($str,$sign,$number){
        $array  = explode($sign, $str);
        $length = count($array);
        if($number < 0){
            $new_array = array_reverse($array);
            $abs_number = abs($number);
            if($abs_number <= $length){
                return $new_array[$abs_number-1];
            }
        }else{
            if($number < $length){
                return $array[$number];
            }
        }
    }


    /**
     * 新增审核节点
     *
     * @param $reviewInfo
     * @param $oldStatus
     * @param array $reviewers
     * @return bool
     */
    public function addReviewNode($reviewInfo, $oldStatus, $reviewers = []){
        $res = false;
        if(!$reviewInfo){
            return $res;
        }
        //审核或者指派节点
        $maxStage = $this->loadModel('review')->getReviewMaxStage($reviewInfo->id, 'review', $reviewInfo->version);
        $stage = $maxStage + 1;
        $nodeCode = $this->getReviewNodeCodeByStatus($reviewInfo->status, $oldStatus);

        if(($reviewInfo->status == 'outReviewing' or $reviewInfo->status == 'pass' or $reviewInfo->status == 'pending' or $reviewInfo->status == 'waitOutReview' ) and $reviewInfo->type =='cbp'){
           $dealUser = $reviewInfo->qa;
        }else{
            $dealUser = $this->getNextDealUser($reviewInfo, $reviewInfo->status, $reviewers);
        }
        if($reviewInfo->status == 'waitFirstMainReview' and $reviewInfo->type == 'cbp' and $this->getIsEdit($reviewInfo->id)==1){
            $dealUser = $reviewInfo->createdBy;
        }
        //增加审核节点
        if(!is_array($dealUser)){
            $dealUserArray = explode(',', $dealUser);
        }else{
            $dealUserArray = $dealUser;
        }
        //新增审核节点
        $reviewNodes = array(
            array(
                'reviewers' => $dealUserArray,
                'stage'     => $stage,
                'status'    => 'wait',
                'nodeCode'  => $nodeCode,
            )
        );

        $subObjectType = $this->getReviewNodeSubObjectType($reviewInfo->status);
        $type = $this->getReviewNodeType($reviewInfo->status);
        $extParams = array(
            'subObjectType' => $subObjectType,
            'type'          => $type
        );
        $this->submitReview($reviewInfo->id, 'review', $reviewInfo->version, $reviewNodes, $extParams);
        return true;
    }

    /**
     * Project: chengfangjinke
     * Method: assign
     * User: wangjiurong
     * Year: 2022
     * Date: 2022/04/13
     * Time: 14:45
     * Desc: This is the code comment. This method is called review.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $reviewID
     * @return false|void
     */
    public function assign($reviewID)
    {
        $reviewInfo = $this->getByID($reviewID);
        if(!$reviewInfo){
            dao::$errors['status'] = $this->lang->common->errorParamId;
            return false;
        }
        //是否允许指派
        $res = $this->checkReviewIsAllowAssign($reviewInfo, $this->app->user->account);
        if(!$res['result']){
            dao::$errors['status'] = $res['message'];
            return false;
        }
        $status = $reviewInfo->status;
        $methodSuffix = $this->lang->review->assignViewSuffixList[$status];
        $method = 'assign'. $methodSuffix;

        $res = $this->$method($reviewID);
        return $res;
    }

    /**
     *指派初审部门
     *
     * @param $reviewID
     * @return false|void
     */
    private function assignFirstAssignDept($reviewID){
        //获得表单信息
        $data = fixer::input('post')
            ->remove('comment, consumed, depts, uid, status, grade, mailto,reviewedDate, reviewType')
            ->get();
        //工作量
       // $consumed = $this->post->consumed;
        $depts    = $this->post->depts;
        $comment  = $this->post->comment;
        $status   = $this->post->status;
        $data->isFirstReview = $this->post->isFirstReview;

        //是否选择初审
        if(!isset($data->isFirstReview) || !in_array($data->isFirstReview, $this->lang->review->isFirstReviewList)){
            dao::$errors['isFirstReview'] = $this->lang->review->checkAssignOpResultList['isFirstReviewError'];
            return false;
        }
        //评审方式
        if(!$this->post->grade){
            dao::$errors['grade'] = $this->lang->review->checkResultList['gradeError'];
            return false;
        }

        if($data->isFirstReview == 1){ //需要初审
            if(!$depts){
                dao::$errors['depts'] = $this->lang->review->checkAssignOpResultList['deptsEmpty'];
                return false;
            }
            //审核部门信息
            $deptSelect = 'id,name, firstReviewer';
            $deptList = $this->loadModel('dept')->getDeptListByIds($depts, $deptSelect);
            if(empty($deptList)){
                dao::$errors['depts'] = $this->lang->review->checkAssignOpResultList['deptsError'];
                return false;
            }
            $firstReviewers = []; //初审接口人
            //部门信息
            foreach ($deptList as $val){
                if(!$val->firstReviewer){
                    dao::$errors['deptFirstReviewer'] = $val->name . $this->lang->review->checkAssignOpResultList['deptFirstReviewerError'];
                    return false;
                }else{
                    $firstReviewers[] = $val->firstReviewer;
                }
            }

        }else{ //不需要初审
            if(!isset($data->deadline) || !$data->deadline){
                dao::$errors['deadline'] = $this->lang->review->checkResultList['deadlineEmpty'];
                return false;
            }

            /*
            if($data->deadline < $today){
                dao::$errors['deadline'] = $this->lang->review->checkResultList['deadlineError'];
                return false;
            }
            */

            if(!isset($data->owner) || !$data->owner){
                dao::$errors['owner'] = $this->lang->review->checkResultList['ownerEmpty'];
                return false;
            }
        }
        //工作量校验
       /* if($consumed !== '0'){
            $checkRes = $this->loadModel('consumed')->checkConsumedInfo($consumed);
            if (!$checkRes) {
                return false;
            }
        }*/

        //评审信息
        $reviewInfo = $this->getByID($reviewID);
        $oldStatus = $reviewInfo->status;
        //检查前后状态
        if($status != $oldStatus){
            dao::$errors['status'] = $this->lang->review->checkResultList['statusError'];
            return false;
        }

        $version = $reviewInfo->version;
        //扩展信息
        $extra = new stdClass();
        //建议评审方式
        $extra->grade = $this->post->grade;
        //评审时间
        $extra->reviewedDate = $this->post->reviewedDate;
        //是否需要修改资料
        $extra->isEditInfo = '';
        if($data->isFirstReview == 2){
          $extra->skipfirstreview = '跳过初审';
        }
        //$extra->dept = isset($depts) ? implode(',',$depts) : '';
        //处理本步骤指派信息
        $result = $this->loadModel('review')->check('review', $reviewID, $version, 'pass', $comment, $reviewInfo->reviewStage, $extra);
        if(!$result){
            dao::$errors['assign'] = $this->lang->review->checkAssignOpResultList['assignError'];
            return false;
        }

        //获得一下状态
        $extParams = [
            'isFirstReview' =>  $data->isFirstReview,
        ];

        $nextStatus = $this->getReviewNextStatus($reviewInfo, $result, $extParams);
        //获得下一阶段处理人员
        $postUser = '';
        if($data->isFirstReview == 1) { //需要初审
            if(!empty($firstReviewers)){
                $postUser = implode(',', $firstReviewers);
            }
        }else{
            $postUser = $data->owner; //评审主席
        }

        //修改信息
        $data->status = $nextStatus;
        if($reviewInfo->status = 'waitApply'){
            $data->submitDate = $reviewInfo->createdDate;
        }
        if(in_array($nextStatus, $this->lang->review->sumitDateStatusList)){
            $data->submitDate = helper::now();
        }

        if($nextStatus == 'waitFirstMainReview' or $nextStatus == 'firstMainReviewing' OR (in_array($oldStatus, $this->lang->review->allowFirstJoinReviewStatusList) && $result == 'pass')){
            $endDate = $reviewInfo->endDate;
        }else{
            if($data->submitDate){
                $endDate = $this->getEndDate($nextStatus,$data->submitDate, $reviewInfo);
            }
        }
        if(isset($endDate)){
            if($nextStatus =='waitPreReview'){
                $data->preReviewDeadline = $endDate;
            }else if($nextStatus =='waitFirstAssignDept' ){
                $data->firstReviewDeadline = $endDate;
            } else if($nextStatus =='waitFirstReview'){
                $data->firstReviewDeadline = $endDate;
            }
            if(in_array($nextStatus, $this->lang->review->sumitDateStatusList)){
                $data->endDate = $endDate;
            }
        }

        //审核通过
        if($data->isFirstReview == 1) { //需要初审,只保留初审信息
            unset($data->deadline);
            unset($data->owner);
        }
        $this->dao->update(TABLE_REVIEW)->data($data)->where('id')->eq($reviewID)->exec();
        if(dao::isError()) {
            return false;
        }
        //工时
        $this->loadModel('consumed')->record('review', $reviewID, '0', $this->app->user->account, $oldStatus, $nextStatus, $this->post->mailto, '', $reviewInfo->version);

        //是否需要增加审核节点
        $isAddNode = $this->getIsAddReviewNode($nextStatus);
        if($isAddNode){
            $newReviewInfo = $this->getByID($reviewID);
            $res = $this->addReviewNode($newReviewInfo, $oldStatus, $postUser);
        }

        if($result == 'pass') {
            $next = $this->dao->select('id')->from(TABLE_REVIEWNODE)->where('objectType')->eq('review')
                ->andWhere('objectID')->eq($reviewID)
                ->andWhere('version')->eq($version)
                ->andWhere('status')->eq('wait')->orderBy('stage,id')->fetch('id');
            //有其他审核节点
            if($next) {
                $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('pending')->where('id')->eq($next)->exec();
                $this->dao->update(TABLE_REVIEWER)->set('status')->eq('pending')->where('node')->eq($next)->exec();
            }
        }

        //处理人
        $newReviewInfo = $this->getByID($reviewID);
        if($nextStatus == 'waitOutReview'){
            $nextDealUser = $newReviewInfo->qa;
        }else{
            $nextDealUser = $this->getNextDealUser($newReviewInfo, $newReviewInfo->status, $postUser);
        }
        if($nextDealUser != $newReviewInfo->dealUser){
            $tempUpdateParams = new stdClass();
            $tempUpdateParams->dealUser = $nextDealUser;
            $this->dao->update(TABLE_REVIEW)->data($tempUpdateParams)->where('id')->eq($reviewID)->exec();
            $data->dealUser = $nextDealUser;
        }
        //加入白名单
        if($postUser){
            if(!is_array($postUser)){
                $postUser = explode(',', $postUser);
            }
            foreach ($postUser as $userAccount){
                $projectId = $newReviewInfo->project;
                $res = $this->addProjectReviewWhitelist($projectId, $reviewID, $userAccount);
            }
        }

        //获得差异信息
        $ext = new stdClass();
        $ext->old = '';

        $ext->new  = isset($_POST['mailto'])  ?  implode(' ',$_POST['mailto']) :'';
        $logChange = common::createChanges($reviewInfo, $data,array('mailto' => $ext));
        return $logChange;
    }

    /**
     *指派初审人员
     *
     * @param $reviewID
     * @return false|void
     */
    private function assignFirstAssignReviewer($reviewID){
        //主审核人
        $mainReviewer     = $this->post->mainReviewer;
        //参与审核人
        $includeReviewers = $this->post->includeReviewers;
        //工作量
       // $consumed         = $this->post->consumed;
        //备注信息
        $comment          = $this->post->comment;

        //评审方式
        if(!$mainReviewer){
            dao::$errors['mainReviewer'] = $this->lang->review->checkAssignOpResultList['mainReviewerError'];
            return false;
        }
        //工作量校验
       /* if($consumed !== '0'){
            $checkRes = $this->loadModel('consumed')->checkConsumedInfo($consumed);
            if (!$checkRes) {
                return false;
            }
        }*/

        //评审信息
        $reviewInfo = $this->getByID($reviewID);
        $oldStatus = $reviewInfo->status;

        //检查是否允许指派初审人员
        if(!in_array($oldStatus, $this->lang->review->allowFirstAssignReviewerStatusList)){
            dao::$errors[] = $this->lang->review->checkAssignOpResultList['statusError'];
            return false;
        }

        //子类型
        $subObjectType = $this->getReviewNodeSubObjectType($oldStatus);
        //分类
        $type = $this->getReviewNodeType($oldStatus, 'review');
        //初审-参与人员审核
        $firstIncludeNodeCode = $this->lang->review->nodeCodeList['firstReview'];
        //初审-主审人员审核
        $firstMainNodeCode = $this->lang->review->nodeCodeList['firstMainReview'];

        //初审人员审核节点信息
        $tempNodeCodeList = [
            $firstIncludeNodeCode,
            $firstMainNodeCode,
        ];
        $assignReviewerNodeIds = [];
        //待指派修改成指派中
        if($oldStatus != $this->lang->review->statusList['firstAssigning']){
            $nextStatus = $this->lang->review->statusList['firstAssigning'];
            $this->dao->update(TABLE_REVIEW)
                ->set('status')->eq($nextStatus)
                ->where('id')->eq($reviewID)->exec();
        }else{
            //扩展信息
            $extParams = array(
                'subObjectType' => $subObjectType,
                'type'          => $type,
                'nodeCode'      => $tempNodeCodeList,
            );
            $assignReviewerNodeIds = $this->loadModel('review')->getReviewerNodeIds('review', $reviewID, $reviewInfo->version, $extParams);
        }

        //处理本步骤指派信息

        //扩展信息
        $extra = new stdClass();
        //评审时间
        $extra->reviewedDate = $this->post->reviewedDate;
        //是否需要修改资料
        $extra->isEditInfo = '';
        $result = $this->loadModel('review')->check('review', $reviewID, $reviewInfo->version, 'pass', $comment, $reviewInfo->reviewStage, $extra);
        if(!$result){
            dao::$errors['assign'] = $this->lang->review->checkAssignOpResultList['assignError'];
            return false;
        }

        //增加新的指派节点
        $mainReviewers = [$mainReviewer];

        //所有人员
        $allReviewers = $mainReviewers;

        //参与人员
        $joinReviewers = [];
        $joinReviewers[1] = $mainReviewers;
        if($includeReviewers){
            $includeReviewers = array_filter($includeReviewers);
            $joinReviewers[2] = $includeReviewers;
            $allReviewers = array_merge($allReviewers, $includeReviewers);
        }

        //去重复
        $allReviewers = array_flip(array_flip($allReviewers));

        $nodeStatus = 'wait';

        //审核节点
        $maxStage = $this->loadModel('review')->getReviewMaxStage($reviewInfo->id, 'review', $reviewInfo->version);
        $firstIncludeStage = $maxStage + 1;
        $firstMainStage    = $firstIncludeStage + 1;

        $reviewNodes = array(
            array( //参与人员
                'reviewers' => $joinReviewers,
                'status'    => $nodeStatus,
                'stage'     => $firstIncludeStage,
                'nodeCode' => $firstIncludeNodeCode,
            ),
            array(//主审人员
                'reviewers' => $mainReviewers,
                'status'    => $nodeStatus,
                'stage'     => $firstMainStage,
                'nodeCode'  => $firstMainNodeCode,
            )
        );
        //是否已经存在指派节点
        if (!empty($assignReviewerNodeIds)) {
            foreach ($reviewNodes as $key => $val) {
                if ($assignReviewerNodeIds[$key]) {
                    $val['nodeId'] = $assignReviewerNodeIds[$key];
                }
                $reviewNodes[$key] = $val;
            }
        }
        //扩展信息
        $reviewerExtParams['parentId'] = $this->app->user->id;
        $extParams = array(
            'subObjectType' => $subObjectType,
            'type'           => $type,
            'reviewerExtParams' => $reviewerExtParams,
        );
        //增加指派审核人员节点
        $this->submitReview($reviewID, 'review', $reviewInfo->version, $reviewNodes, $extParams);
        //下一状态
        $nextStatus = $this->getReviewNextStatus($reviewInfo, $result);
        $updateParams = new stdClass();
        $updateParams->status = $nextStatus;

        //全部指派通过
        if ($result == 'pass') {
            $next = $this->dao->select('id')->from(TABLE_REVIEWNODE)->where('objectType')->eq('review')
                ->andWhere('objectID')->eq($reviewID)
                ->andWhere('version')->eq($reviewInfo->version)
                ->andWhere('status')->eq('wait')->orderBy('stage,id')->fetch('id');
            //有其他审核节点
            if ($next) {
                $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('pending')->where('id')->eq($next)->exec();
                $this->dao->update(TABLE_REVIEWER)->set('status')->eq('pending')->where('node')->eq($next)->exec();
            }
            $reviewInfo = $this->getByID($reviewID);
            $dealUser = $this->getNextDealUser($reviewInfo, $nextStatus);
        }else{
            $reviewInfo = $this->getByID($reviewID);
            $dealUser = $reviewInfo->reviewers;
        }
        //修改信息
        $updateParams->dealUser = $dealUser;
        if($nextStatus == 'waitPreReview'){
            $updateParams->submitDate = $reviewInfo->createdDate . '00:00:00';
        }else if(in_array($nextStatus, $this->lang->review->sumitDateStatusList)){
            $updateParams->submitDate = helper::now();
        }

        if($nextStatus =='waitFirstMainReview' or $nextStatus=='firstMainReviewing' OR (in_array($oldStatus, $this->lang->review->allowFirstJoinReviewStatusList) && $result == 'pass')){
            $endDate = $reviewInfo->endDate;
        }else{
            if(isset($updateParams->submitDate)){
                $endDate = $this->getEndDate($nextStatus,$updateParams->submitDate, $reviewInfo);
            }
        }

        if(isset($endDate)){
            if($nextStatus =='waitPreReview'){
                $updateParams->preReviewDeadline = $endDate;
            }else if($nextStatus =='waitFirstAssignDept' ){
                $updateParams->firstReviewDeadline = $endDate;
            } else if($nextStatus =='waitFirstReview'){
                $updateParams->firstReviewDeadline = $endDate;
            }
            if(in_array($nextStatus, $this->lang->review->sumitDateStatusList)){
                $updateParams->endDate = $endDate;
            }
        }

        //修改主记录状态
        $this->dao->update(TABLE_REVIEW)->data($updateParams)->where('id')->eq($reviewID)->exec();
        if(dao::isError()) {
            return false;
        }
        //工时信息
        $this->loadModel('consumed')->record('review', $reviewID, '0', $this->app->user->account, $oldStatus, $nextStatus, $this->post->mailto, '', $reviewInfo->version);

        //加入白名单
        $postUser = $allReviewers;
        foreach ($postUser as $userAccount){
            $projectId = $reviewInfo->project;
            $res = $this->addProjectReviewWhitelist($projectId, $reviewID, $userAccount);
        }
        $ext = new stdClass();
        $ext->old = '';
        $ext->new = isset($_POST['mailto'])  ?  implode(' ',$_POST['mailto']) :'';
        $logChange = common::createChanges($reviewInfo, $updateParams,array('mailto' => $ext));
        return $logChange;
    }

    /**
     *指派正式审核人员
     *
     * @param $reviewID
     * @return array|false
     */
    private function assignFormalAssignReviewer($reviewID){
        $data = fixer::input('post')
            ->add('isConfirmGrade', 1) //确定评审方式
            ->join('reviewer', ',')
            ->join('expert', ',')
            ->join('reviewedBy', ',')
            ->join('outside', ',')
            ->join('relatedUsers', ',')
            ->remove('uid,comment,consumed, status,mailto,reviewedDate, meetingPlanType')
            ->get();

        if(isset($data->expert)){
            $data->expert = trim($data->expert, ',');
        }else{
            $data->expert = '';
        }
        if(isset($data->reviewedBy)){
            $data->reviewedBy = trim($data->reviewedBy, ',');
        }else{
            $data->reviewedBy = '';
        }
        if(isset($data->outside)){
            $data->outside = trim($data->outside, ',');
        }else{
            $data->outside = '';
        }

        if(isset($data->relatedUsers)){
            $data->relatedUsers = trim($data->relatedUsers, ',');
        }else{
            $data->relatedUsers = '';
        }

        //工作量
       // $consumed = $this->post->consumed;
        //备注信息
        $comment = $this->post->comment;

        //评审类型
        if(!isset($data->type) || !$data->type){
            dao::$errors['type'] = $this->lang->review->checkResultList['typeError'];
            return false;
        }

        //评审方式
        if(!isset($data->grade) || !$data->grade){
            dao::$errors['grade'] = $this->lang->review->checkResultList['gradeError'];
            return false;
        }

        //评审专员
        if(!isset($data->reviewer) || !$data->reviewer){
            dao::$errors['reviewer'] = $this->lang->review->checkResultList['reviewerError'];
            return false;
        }
        //评审主席
        if(!isset($data->owner) || !$data->owner){
            dao::$errors['owner'] = $this->lang->review->checkResultList['ownerEmpty'];
            return false;
        }else{ //设置了评审主席，如果绑定会议评审检查和会议评审的主席是否一致，如果不一致，提示报错，需要和绑定会议的评审主席一致
            if(isset($data->meetingCode) && $data->meetingCode) {
                $meetingInfo = $this->loadModel('reviewmeeting')->getMeetingByMeetingCode($data->meetingCode, 'owner');
                if($data->owner != $meetingInfo->owner){
                    dao::$errors['owner'] = $this->lang->review->checkResultList['ownerUserError'];
                    return false;
                }
            }
        }

        //评审专家
        if(!isset($data->expert) || !$data->expert){
            dao::$errors['expert'] = $this->lang->review->checkResultList['expertEmpty'];
            return false;
        }
        //计划完成时间
        if(!isset($data->deadline) || !$data->deadline){
            dao::$errors['deadline'] = $this->lang->review->checkResultList['deadlineEmpty'];
            return false;
        }

        //工作量校验
       /* if($consumed !== '0'){
            $checkRes = $this->loadModel('consumed')->checkConsumedInfo($consumed);
            if (!$checkRes) {
                return false;
            }
        }*/

        //评审信息
        $reviewInfo = $this->getByID($reviewID);
        $oldStatus = $reviewInfo->status;

        //当前状态是否允许指派
        if($oldStatus != $this->post->status){
            dao::$errors[] = $this->lang->review->checkAssignOpResultList['statusError'];
            return false;
        }
        if(!isset($data->expert) || !$data->expert){
            $data->expert = '';
        }

        if(!isset($data->reviewedBy) || !$data->reviewedBy){
            $data->reviewedBy = '';
        }

        if(!isset($data->outside) || !$data->outside){
            $data->outside = '';
        }
        if(!isset($data->relatedUsers) || !$data->relatedUsers){
            $data->relatedUsers = '';
        }

        //修改主表记录
        $extParams = [
            'owner' => $data->owner,
            'type'  => $data->type,
            'grade' => $data->grade,
        ];

        $nextStatus = $this->getReviewNextStatus($reviewInfo, 'pass', $extParams);
        $data->status = $nextStatus;
        //评审方式
        if(isset($data->grade) && ($data->grade == 'meeting')){
            if(($nextStatus == 'waitFormalReview') && isset($data->type) && ($data->type != 'cbp')){ //制定会议评审，且要流转到在线评审
                //会议评审
                $meetingPlanExport = $this->getReviewMeetingPlanExportUsers($data->expert, $data->reviewedBy, $data->outside);
                $data->meetingPlanExport       = $meetingPlanExport;
                $reviewInfo->meetingPlanExport = $meetingPlanExport;
                //检查会议评审信息
                $meetingPlanType = $this->post->meetingPlanType ? $this->post->meetingPlanType: '';
                $meetingCode     = isset($data->meetingCode)? $data->meetingCode: '';
                $meetingPlanTime = isset($data->meetingPlanTime)? $data->meetingPlanTime: '';
                $checkRes = $this->checkReviewMeetingInfo($reviewInfo, $data->type, $meetingPlanType, $meetingCode, $meetingPlanTime, $data->owner);
                if(!$checkRes['result']){
                   return false;
                }
            }
        }

        //不修改评审表中的字段
        if(isset($data->meetingCode)){
            unset($data->meetingCode);
        }
        if(isset($data->meetingPlanTime)){
            unset($data->meetingPlanTime);
        }
        if($nextStatus == 'waitPreReview'){
            $data->submitDate =$reviewInfo->createdDate;
        }else if(in_array($nextStatus, $this->lang->review->sumitDateStatusList)){
            $data->submitDate = helper::now();
        }
        $result = 'pass';
        if($nextStatus =='waitFirstMainReview' or $nextStatus == 'firstMainReviewing' OR (in_array($oldStatus, $this->lang->review->allowFirstJoinReviewStatusList) && $result == 'pass')){
            $endDate = $reviewInfo->endDate;
        }else{
            if(isset($data->submitDate)){
                $endDate = $this->getEndDate($nextStatus,$data->submitDate, $reviewInfo);
            }
        }

        if(isset($endDate)){
            if($nextStatus =='waitPreReview'){
                $data->preReviewDeadline = $endDate;
            }else if($nextStatus =='waitFirstAssignDept' ){
                $data->firstReviewDeadline = $endDate;
            } else if($nextStatus =='waitFirstReview'){
                $data->firstReviewDeadline = $endDate;
            }
            if(in_array($nextStatus, $this->lang->review->sumitDateStatusList)){
                $data->endDate = $endDate;
            }
        }

        $this->dao->update(TABLE_REVIEW)->data($data)
            ->autoCheck()
            ->batchCheck($this->config->review->assignFormalAssignReviewer->requiredFields, 'notempty')
            ->where('id')->eq($reviewID)
            ->exec();
        if(dao::isError()) {
            return false;
        }

        //处理本步骤指派信息
        //扩展信息
        $extra = new stdClass();
        //评审方式
        if(isset($data->grade) && $data->grade){
            $extra->grade  = $data->grade;
        }

        $extra->expert     = $data->expert;
        $extra->reviewedBy = $data->reviewedBy; //外部专家1
        $extra->outside    = $data->outside; //外部专家2
        //评审时间
        $extra->reviewedDate = $this->post->reviewedDate;
        //是否需要修改资料
        $extra->isEditInfo = '';
        if($nextStatus == $oldStatus){
            $extra->appoint = 1; //委派
        }
        $result = $this->loadModel('review')->check('review', $reviewID, $reviewInfo->version, 'pass', $comment, $reviewInfo->reviewStage, $extra);
        if(!$result){
            dao::$errors['assign'] = $this->lang->review->checkAssignOpResultList['assignError'];
            return false;
        }
        //重新获得审批信息
        $newReviewInfo = $this->getByID($reviewID);
        //工时信息
        $this->loadModel('consumed')->record('review', $reviewID, '0', $this->app->user->account, $oldStatus, $nextStatus, $this->post->mailto, '', $newReviewInfo->version);

        $dealUser = $this->getNextDealUser($newReviewInfo, $nextStatus);

        //是否需要增加审核节点
        $isAddNode = $this->getIsAddReviewNode($oldStatus);
        if($isAddNode){
            $res = $this->addReviewNode($newReviewInfo, $oldStatus, $dealUser);
        }

        //全部指派通过
        if ($result == 'pass') {
            $next = $this->dao->select('id')->from(TABLE_REVIEWNODE)->where('objectType')->eq('review')
                ->andWhere('objectID')->eq($reviewID)
                ->andWhere('version')->eq($reviewInfo->version)
                ->andWhere('status')->eq('wait')->orderBy('stage,id')->fetch('id');
            //有其他审核节点
            if ($next) {
                $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('pending')->where('id')->eq($next)->exec();
                $this->dao->update(TABLE_REVIEWER)->set('status')->eq('pending')->where('node')->eq($next)->exec();
            }
        }

        if($dealUser != $newReviewInfo->dealUser){
            $tempUpdateParams = new stdClass();
            if(is_array($dealUser)){
                $dealUser = implode(',', $dealUser);
            }
            $tempUpdateParams->dealUser = $dealUser;
            $this->dao->update(TABLE_REVIEW)->data($tempUpdateParams)->where('id')->eq($reviewID)->exec();
            $data->dealUser = $dealUser;
        }

        //加入白名单
        if(is_array($dealUser)){
            $postUser = $dealUser;
        }else{
            $postUser = explode(',', $dealUser);
        }
        foreach ($postUser as $userAccount){
            $projectId = $reviewInfo->project;
            $res = $this->addProjectReviewWhitelist($projectId, $reviewID,  $userAccount);
        }

        //评审方式
        if(isset($data->grade) && ($data->grade == 'meeting')){
            if(($nextStatus == 'waitFormalReview') && isset($data->type) && ($data->type != 'cbp')){ //制定会议评审，且要流转到在线评审
                //如果需要会议评审
                if($meetingPlanType == 1){ //选择已有会议
                    $res = $this->loadModel('reviewmeeting')->bindToReviewMeeting($meetingCode, $reviewInfo, $nextStatus);
                    if(!$res['result']){
                        dao::$errors[] = $res['message'];
                        return false;
                    }
                    $meetingCode = $res['data']->meetingCode;
                }elseif ($meetingPlanType == 2){ //新建会议
                    $res = $this->loadModel('reviewmeeting')->createReviewMeeting($reviewInfo, $nextStatus, $meetingPlanTime, '0', $data->reviewer);
                    if(!$res['result']){
                        dao::$errors[] = $res['message'];
                        return false;
                    }
                    $meetingCode = $res['data']->meetingCode;
                }
            }
        }
        if(isset($meetingCode) && !empty($meetingCode)){
            $data->meetingCode = $meetingCode;
        }
        //会议评审转为在线评审的时候
        $deleteMeeting =  new stdClass();
        $deleteMeeting->meetingCode = '';
        $deleteMeeting->meetingPlanTime = '0000-00-00 00:00:00';
        if($data->grade == 'online'){
            $this->dao->update(TABLE_REVIEW)->data($deleteMeeting)->where('id')->eq($reviewID)->exec();
        }

        //返回
        $ext = new stdClass();
        $ext->old = '';
        $ext->new = isset($_POST['mailto'])  ?  implode(' ', $_POST['mailto']) :'';
        $logChange = common::createChanges($reviewInfo, $data, array('mailto' => $ext));

        return $logChange;
    }

    /**
     *获会议预审专家
     *
     * @param $expert
     * @param $reviewedBy
     * @param $outside
     * @return string
     */
    public function getReviewMeetingPlanExportUsers($expert, $reviewedBy, $outside){
        $expertUsers     = explode(',', $expert);
        $reviewedByUsers = explode(',', $reviewedBy);
        $outsideUsers    = explode(',', $outside);
        $planExportUsers = array_merge($expertUsers, $reviewedByUsers, $outsideUsers);
        $planExportUsers = array_flip(array_flip($planExportUsers));
        //去掉空元素
        $planExportUsers = array_filter($planExportUsers);
        $planExportUsers = implode(',', $planExportUsers);
        return $planExportUsers;
    }

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
        // 搜索关闭时间时排除0000-00-00数据
        if(strpos($reviewQuery,'closeTime')){
            $reviewQuery = str_replace("AND `closeTime`", "AND `closeTime` != '0000-00-00 00:00:00' AND `closeTime`", $reviewQuery);
        }
       //此判断为了解决连表查询时，重复字段未指明表的问题
        if(strpos($reviewQuery,'createdDate')){
            $reviewQuery = str_replace('AND (`', ' AND (`t1.', $reviewQuery);
            $reviewQuery = str_replace('AND `', ' AND `t1.', $reviewQuery);
            $reviewQuery = str_replace('`', '', $reviewQuery);
        }else{
            $reviewQuery = str_replace('AND `', ' AND `t1.', $reviewQuery);
            $reviewQuery = str_replace('`', '', $reviewQuery);
        }
        //留着，主要为了查看状态处理历史数据
       /* $reviews = $this->dao->select('t1.*, t2.category, t2.product')->from(TABLE_REVIEW)->alias('t1')
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
            ->fetchAll();*/
        $reviewQuery = $this->loadModel('review')->getFormatSearchQuery($reviewQuery);
        $reviews = $this->dao->select('t1.*, t2.category, t2.product')->from(TABLE_REVIEW)->alias('t1')
            ->leftJoin(TABLE_OBJECT)->alias('t2')
            ->on('t1.object=t2.id')
            ->where('t1.deleted')->eq(0)
            ->beginIF($projectID)->andWhere('t1.project')->eq($projectID)->fi()
            ->beginIF($browseType == 'bysearch')->andWhere($reviewQuery)->fi()
            ->beginIF($browseType == 'waitapply')->andWhere('t1.status')->eq('waitApply')->fi()
            ->beginIF($browseType == 'waitprereview')->andWhere('t1.status')->eq('waitPreReview')->fi()
            ->beginIF($browseType == 'waitfirstassigndept')->andWhere('t1.status')->eq('waitFirstAssignDept')->fi()
            ->beginIF($browseType == 'waitfirstassignreviewer')->andWhere('t1.status')->in(array('waitFirstAssignReviewer','firstassigning'))->fi()
            ->beginIF($browseType == 'waitfirstreview')->andWhere('t1.status')->in(array('waitFirstReview','firstReviewing'))->fi()
            ->beginIF($browseType == 'waitfirstmainreview')->andWhere('t1.status')->in(array('waitFirstMainReview','firstMainReviewing'))->fi()
            ->beginIF($browseType == 'waitformalassignreviewer')->andWhere('t1.status')->eq('waitFormalAssignReviewer')->fi()
            ->beginIF($browseType == 'waitformalreview')->andWhere('t1.status')->in(array('waitFormalReview','formalReviewing'))->fi()
            ->beginIF($browseType == 'waitformalownerreview')->andWhere('t1.status')->eq('waitFormalOwnerReview')->fi()
            ->beginIF($browseType == 'waitmeetingreview')->andWhere('t1.status')->in(array('waitMeetingReview','meetingReviewing'))->fi()
            ->beginIF($browseType == 'waitmeetingownerreview')->andWhere('t1.status')->eq('waitMeetingOwnerReview')->fi()
            ->beginIF($browseType == 'waitverify')->andWhere('t1.status')->in(array('waitVerify','verifying'))->fi()
            ->beginIF($browseType == 'waitoutreview')->andWhere('t1.status')->in(array('waitOutReview','outReviewing'))->fi()
            ->beginIF($browseType == 'pass')->andWhere('t1.status')->eq('pass')->fi()
            ->beginIF($browseType == 'reviewpass')->andWhere('t1.status')->eq('reviewpass')->fi()
            ->beginIF($browseType == 'prepassbutedit')->andWhere('t1.status')->eq('prePassButEdit')->fi()
            ->beginIF($browseType == 'firstpassbutedit')->andWhere('t1.status')->eq('firstPassButEdit')->fi()
            ->beginIF($browseType == 'formalpassbutedit')->andWhere('t1.status')->eq('formalPassButEdit')->fi()
            ->beginIF($browseType == 'meetingpassbutedit')->andWhere('t1.status')->eq('meetingPassButEdit')->fi()
            ->beginIF($browseType == 'outpassbutedit')->andWhere('t1.status')->eq('outPassButEdit')->fi()
            ->beginIF($browseType == 'rejectpre')->andWhere('t1.status')->eq('rejectPre')->fi()
            ->beginIF($browseType == 'rejectfirst')->andWhere('t1.status')->eq('rejectFirst')->fi()
            ->beginIF($browseType == 'rejectformal')->andWhere('t1.status')->eq('rejectFormal')->fi()
            ->beginIF($browseType == 'rejectmeeting')->andWhere('t1.status')->eq('rejectMeeting')->fi()
            ->beginIF($browseType == 'rejectout')->andWhere('t1.status')->eq('rejectOut')->fi()
            ->beginIF($browseType == 'rejectverify')->andWhere('t1.status')->eq('rejectVerify')->fi()
            ->beginIF($browseType == 'reject')->andWhere('t1.status')->eq('reject')->fi()
            ->beginIF($browseType == 'recall')->andWhere('t1.status')->eq('recall')->fi()
            ->beginIF($browseType == 'fail')->andWhere('t1.status')->eq('fail')->fi()
            ->beginIF($browseType == 'drop')->andWhere('t1.status')->eq('drop')->fi()
            ->beginIF($browseType == 'archive')->andWhere('t1.status')->eq('archive')->fi()
            ->beginIF($browseType == 'baseline')->andWhere('t1.status')->eq('baseline')->fi()
            ->beginIF(in_array($browseType, $this->lang->review->uniqueStatusList))->andWhere('t1.status')->eq($browseType)->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll();
        if(empty($reviews)){
            return $reviews;
        }

        $this->loadModel('review');
        $allowReviewStatusList = $this->lang->review->allowReviewStatusList;
        $allowAssignStatusList = $this->lang->review->allowAssignStatusList;

        foreach($reviews as $key => $reviewInfo) {
            $status = $reviewInfo->status;
            $reviews[$key]->statusDesc = $this->getReviewStatusDesc($status, $reviewInfo->rejectStage);

            if(in_array($status, $allowReviewStatusList) || in_array($status, $allowAssignStatusList)){
                if($status == 'baseline'){
                    $reviews[$key]->reviewers = $reviewInfo->dealUser;
                    $reviews[$key]->dealUser = $reviewInfo->dealUser;
                }else{
                    $reviewVersion = $this->getReviewVersion($reviewInfo);
                    $reviewers = $this->review->getReviewer('review', $reviewInfo->id, $reviewVersion, $reviewInfo->reviewStage);
                    $reviews[$key]->reviewers = $reviewers;
                    $reviews[$key]->dealUser = $reviewers;
                }
            }
        }
        return $reviews;
    }

    /**
     * 获得状态描述
     *
     * @param $status
     * @param $rejectStage
     * @return mixed
     */
    public function getReviewStatusDesc($status, $rejectStage){
        $statusDesc = zget($this->lang->review->statusLabelList, $status, '');
        return $statusDesc;
    }

    /**
     * Get review by id.
     *
     * @param  int    $reviewID
     * @access public
     * @return void
     */
    public function  getByID($reviewID){
        if(!$reviewID) return new stdclass();
        $review = $this->dao->select('*')->from(TABLE_REVIEW)->where('id')->eq($reviewID)->orderBy('id')->fetch();
        if(empty($review)) return null;
        $review->objects = explode(',',$review->object);
        $allowReviewStatusList = $this->lang->review->allowReviewStatusList;
        $allowAssignStatusList = $this->lang->review->allowAssignStatusList;
        $status = $review->status;

        if(in_array($status, $allowReviewStatusList) || in_array($status, $allowAssignStatusList)){
            $this->loadModel('review');
            $version = $review->version;
            $review->reviewers = $this->review->getReviewer('review', $review->id, $version, $review->reviewStage);
        }
        $review = $this->loadModel('file')->replaceImgURL($review, 'comment');
        $review = $this->getConsumed($review);

        $review->files = $this->loadModel('file')->getByObject('review', $review->id);
        return $review;
    }

    /**
     * 检查标题是否存在
     *
     * @param $title
     * @param int $excludeId
     * @return false|void
     */
    public function getTitleIsExist($title, $excludeId = 0){
        $ret = false;
        if($title == ''){
            return $ret;
        }
        $review = $this->dao->select('ID')
            ->from(TABLE_REVIEW)
            ->where('title')->eq($title)
            ->beginIF($excludeId > 0)->andWhere('id')->ne($excludeId)->fi()
            ->fetch();
        if($review){
            $ret = true;
        }
        return $ret;
    }

    /**
     *获得审核表主要信息
     *
     * @param $reviewID
     * @param string $select
     * @return stdclass|void
     */
    public function getReviewMainInfoByID($reviewID, $select = '*'){
        $data = new stdclass();
        if(!$reviewID) {
            return $data;
        }
        $reviewInfo = $this->dao->select($select)->from(TABLE_REVIEW)->where('id')->eq($reviewID)->fetch();
        if($reviewInfo){
            $data = $reviewInfo;
        }
        return $data;
    }


    /* 获取工时投入信息*/
    public function getConsumed($review)
    {
        if(empty($review)) return array();

        $cs = $this->dao->select('*')->from(TABLE_CONSUMED)->where('objectType')->eq('review')
            ->andWhere('objectID')->eq($review->id)
            ->orderBy('createdDate')
            ->fetchAll();
        $review->consumed = $cs;
        return $review;
    }


    /**
     * 返回评审信息的版本号
     *
     * @param $reviewInfo
     * @return string
     */
    public function getReviewVersion($reviewInfo){
        $reviewVersion = $reviewInfo->version;
        return $reviewVersion;
    }


    /**
     *获得是否需要新增审核节点
     *
     * @param $status
     * @return bool
     */
    public function getIsAddReviewNode($status){
        $isAddReviewNode = false;
        if(!$status){
            return $isAddReviewNode;
        }
        if(in_array($status, $this->lang->review->needAddReviewNodeStatusList)){
            $isAddReviewNode = true;
        }
        return $isAddReviewNode;
    }


    /**
     *获得审核节点的子分类
     *
     * @param $status
     * @return string|void
     */
    public function getReviewNodeSubObjectType($status){
        $subObjectType = '';
        if(!$status){
            return $subObjectType;
        }
        if($status == $this->lang->review->statusList['waitApply'] || in_array($status, $this->lang->review->allowPreReviewStatusList)){
            $subObjectType = 'reviewPre';
        }elseif (in_array($status, $this->lang->review->allowFirstReviewStatusList) || in_array($status, $this->lang->review->allowFirstAssignStatusList)){
            $subObjectType = 'reviewFirst';
        }elseif ($status == 'baseline'  ){
            $subObjectType = 'baseline';
        }else{
            $subObjectType = 'reviewFormal';
        }
        return $subObjectType;
    }

    /**
     *获得审核节点类型 1-审核 2-指派
     *
     * @param $status
     * @param $nodeOp
     * @return int
     */
    public function getReviewNodeType($status, $nodeOp = ''){
        $type = $this->lang->review->reviewNodeTypeList[1];
        if($nodeOp == 'review'){
            return $type;
        }
        if(in_array($status, $this->lang->review->allowAssignStatusList)){
            $type = $this->lang->review->reviewNodeTypeList[2];
        }
        return $type;
    }


    /**
     * 获得评审自定义列表
     *
     * @param $section
     * @params $isSpecial
     * @return array
     */
    public function getReviewLangList($section, $isSpecial = false){
        $data = [];
        $data[''] = '';
        if(!$section){
            return $data;
        }

        $langData = $this->dao->select('`section`, `key`, `value`, `system`')->from(TABLE_LANG)->where('module')->eq('review')->andWhere('section')->eq($section)->orderBy('id')->fetchAll();
        if(!$langData){
            return $data;
        }
        if($isSpecial){
            foreach ($langData as $item)
            {
                if($item->key == 'meeting' || $item->key == 'online'){
                    $data[$item->key] = $item->value;
                }
            }

        }else{
            foreach ($langData as $item)
            {
                $data[$item->key] = $item->value;
            }
        }
        return $data;
    }

    /**
     * 获得项目评审的建议评审方式
     *
     * @param $type
     * @param $grade
     * @return string
     */
    public function getReviewAdviceGrade($type, $grade){
        $adviceGrade = $grade;
        if($type == 'cbp'){ //金科初审
            $adviceGrade = 'out'; //外部评审
        }
        return $adviceGrade;
    }

    /**
     *获得评审方式列表
     *
     * @param $adviceGrade
     * @return mixed
     */
    public function getReviewAdviceGradeList($adviceGrade){
        if($adviceGrade == 'out'){
            $data = $this->lang->review->adviceGradeList;
        }else{
            $data = $this->getReviewLangList('gradeList', true);
        }
        return $data;
    }

    /**
     *获得所有评审方式列表
     *
     * @param $isSpecial
     * @return mixed
     */
    public function getReviewAllGradeList($isSpecial = false){
        $temp = $this->getReviewLangList('gradeList', $isSpecial);
        $data = array_merge($temp, $this->lang->review->adviceGradeList);
        return $data;
    }

    /**
     *检查归档信息
     * @param $review
     * @param $data
     * @return mixed
     */
    public function checkArchiveInfo($review, $data){
        $res = array(
            'result'  => false,
            'message' => '',
            'data'    => $data,
        );
        $svnUrl     = $data->svnUrl;
        $svnVersion = $data->svnVersion;
        $svnUrlArray     = [];
        $svnVersionArray = [];
        //是否需要显示安全测试和性能测试
        $isShowSafetyTest = $this->isShowSafetyTest($review->object, $review->type);
        if($isShowSafetyTest){ //校验是否需要安全测试和是否需要性能测试
            $isSafetyTestList = $this->lang->review->isSafetyTestList;
            $isPerformanceTestList = $this->lang->review->isPerformanceTestList;
            unset($isSafetyTestList[1]);
            unset($isPerformanceTestList[1]);
            if(!in_array($data->isSafetyTest, array_keys($isSafetyTestList))){
                $message = sprintf($this->lang->review->checkResultList['fieldEmpty'], $this->lang->review->isSafetyTest);
                $res['message'] = $message;
                dao::$errors['isSafetyTest'] = $message;
                return $res;
            }
            if(!in_array($data->isPerformanceTest, array_keys($isPerformanceTestList))){
                $message = sprintf($this->lang->review->checkResultList['fieldEmpty'], $this->lang->review->isPerformanceTest);
                $res['message'] = $message;
                dao::$errors['isPerformanceTest'] = $message;
                return $res;
            }
        }
        foreach ($svnUrl as $key => $item) {
            $item = trim($item);
            $currentSvnVersion = trim($svnVersion[$key]);
            $sortKey = $key + 1;
            if (!$currentSvnVersion && $item ) {
                $message = sprintf($this->lang->review->svnUrlVersionErrorTip, $sortKey);
                $res['message'] = $message;
                dao::$errors['svnVersion' . $sortKey] = $message;
                return $res;

            }
            if (!$item && $currentSvnVersion) {
                $message = sprintf($this->lang->review->svnUrlVersionErrorTip, $sortKey);
                $res['message'] = $message;
                dao::$errors['svnUrl' . $sortKey] = $message;
                return $res;
            }

            //同时为空
            if(!$item && !$currentSvnVersion){
                if ($sortKey == 1) {
                    $message = $this->lang->review->svnUrlVersionEmptyTip;
                }else{
                    $message = sprintf($this->lang->review->svnUrlVersionBothEmptyTip, $sortKey);
                }
                dao::$errors['svnUrl' . $sortKey] = $message;
                $res['message'] = $message;
                return $res;
            }

            $maxStrLen = 255;
            if(mb_strlen($currentSvnVersion) > $maxStrLen){
                $message = sprintf($this->lang->review->svnVersionLenErrorTip, $sortKey, $maxStrLen);
                $res['message'] = $message;
                dao::$errors['svnVersion' . $sortKey] = $message;
                return $res;
            }

            $svnUrlArray[]     = $item;
            $svnVersionArray[] = $currentSvnVersion;
        }

        //查询是否有一条记录
        if (empty($svnUrlArray)){
            $message = $this->lang->review->svnUrlVersionEmptyTip;
            dao::$errors['svnUrl1'] = $message;
            $res['message'] = $message;
            return $res;
        }
        //返回
        $res['result'] = true;
        $data->svnUrl     = $svnUrlArray;
        $data->svnVersion = $svnVersionArray;
        $data->result = 'pass'; //默认审核
        $res['data'] = $data;
        return $res;
    }

    /**
     *检查打基线信息
     *
     * @param $data
     * @return array
     */
    public function checkBaseLineInfo($data){
        $res = array(
            'result'  => false,
            'message' => '',
            'data'    => $data,
        );
        if(!$data){
            return $res;
        }
        $isReject = $data->isReject; //是否退回
        $baselineType = $data->baseLineType;   //基线类型
        $baselinePath = $data->baseLinePath;  //基线路径
        if ($isReject == 2) { //需要打基线
            foreach ($baselineType as $key => $item) {
                if (!$baselinePath[$key] && $item) {
                    $message = sprintf($this->lang->review->baseLineTypeTip, $key + 1);
                    $res['message'] = $message;
                    if ($key + 1 != 1) {
                        dao::$errors['baseLinePath' . ($key + 1)] = $message;
                    }else{
                        dao::$errors['baseLinePath'] = $message;
                    }
                    return $res;
                }
                if (!$item && $baselinePath[$key]) {
                    $message = sprintf($this->lang->review->baseLineTypeTip, $key + 1);
                    $res['message'] = $message;
                    if ($key + 1 != 1) {
                        dao::$errors['baseLineType' . ($key + 1)] = $message;
                    }else{
                        dao::$errors['baseLineType'] = $message;
                    }
                    return $res;
                }

                //验证路径规则
                if (!empty($baselinePath[$key])) {
                    // $flag =  preg_match("/^(?!_)([0-9a-zA-Z_:\x80-\xff.\/]{0,})(?<!_)$/", $baselinePath[$key]);
                    $pathFinal = explode('/', $baselinePath[$key]);
                    krsort($pathFinal);
                    $checkPath = array_values($pathFinal)[0];
                    $count = substr_count($checkPath, '_');
                    // if((($flag != '1' ) || $count != 4) || (($flag == '1' ) && $count != 4)){
                    if ($count != 4) {
                        $message = sprintf($this->lang->review->baseLinePathError, $key + 1);
                        $res['message'] = $message;
                        if ($key + 1 != 1) {
                            dao::$errors['baseLinePath' . ($key + 1)] = $message;
                        }else{
                            dao::$errors['baseLinePath'] = $message;
                        }
                        return $res;
                    }
                }
            }
            //基线时间
            if (isset($_POST['baseLineTime']) && empty($_POST['baseLineTime'])) {
                $message =  $this->lang->review->timeEmpty;
                dao::$errors['baseLineTime'] = $message;
                return $res;
            }


            if (array_filter($baselineType) && array_filter($baselinePath)) {
                $data->baseLineCondition = 'yes';//已打基线
                $data->baseLineType = implode(',', array_filter($baselineType));
                $data->baseLinePath = implode(',', array_filter($baselinePath));

            }else{
                $data->baseLineCondition = 'no' ;//未打基线
                $data->baseLineType = '';
                $data->baseLinePath = '';
            }
            $data->result = 'pass';
        } else {
            $data->baseLineType = '';
            $data->baseLinePath = '';
            $data->baseLineCondition = 'no' ;//未打基线
            $data->result = 'reject';
            $data->baseLineTime = '';
        }

        //返回
        $res['result'] = true;
        $res['data']  = $data;
        return $res;
    }

    /**
     *检查评审主席确定审核结论的参数
     *
     * @param $reviewInfo
     * @return array
     */
    public function checkReviewParams($reviewInfo){
        $data = fixer::input('post')
            ->stripTags($this->config->review->editor->review['id'], $this->config->allowedTags)
            ->get();
        $data = $this->loadModel('file')->processImgURL($data, $this->config->review->editor->review['id'], $this->post->uid);

        $res = array(
            'result' => false,
            'data' => $data,
        );

        $reviewId = $reviewInfo->id;
        //评审状态
        $reviewStatus = $reviewInfo->status;
        $version = $reviewInfo->version;

        if (isset($data->appointOther) && ($data->appointOther == 1)) { //验证指派验证人员
            $data->result = 'passNoNeedEdit'; //验证通过无需修改
            $data->reviewedDate = ''; //验证日期
            if (!$data->appointUser) { //验证人员不能为空
                dao::$errors['appointUser'] = $this->lang->review->checkAssignOpResultList['appointUserEmpty'];
                return $res;
            }
            if ($data->appointUser == $this->app->user->account) { //验证人员不能是自己
                dao::$errors['appointUser'] = $this->lang->review->checkAssignOpResultList['appointUserError'];
                return $res;
            }
        }elseif($reviewStatus == $this->lang->review->statusList['archive']){ //待归档
            $checkRes = $this->checkArchiveInfo($reviewInfo, $data);
            if(!$checkRes['result']){
                return $res;
            }
            $data = $checkRes['data'];
        }elseif($reviewStatus == $this->lang->review->statusList['baseline']){ //待打基线
            $checkRes = $this->checkBaseLineInfo($data);
            if(!$checkRes['result']){
                return $res;
            }
            $data = $checkRes['data'];
        }elseif(($reviewStatus == $this->lang->review->statusList['waitVerify'] || $reviewStatus == $this->lang->review->statusList['verifying']) && $data->result == $this->lang->review->passNoNeedEdit){
            // 当状态为待验证或验证中并且验证结果为通过时判断是否还有问题未验证
            $dealUser = explode(',', str_replace(' ', '', $reviewInfo->dealUser));
            //count($dealUser)参数为1时,说明是最后一名处理人 此时要判断整个评审是否还有不是已验证的问题
            $issueCount = $this->loadModel('reviewproblem')->getReviewIssueCountByUser($reviewId, $this->app->user->account, count($dealUser));
            if($issueCount != 0){
                dao::$errors['result'] = $this->lang->review->checkVerify['passNoEdit'];
                return $res;
            }
        } else { //评审
            //评审结果
            if (!$data->result) {
                dao::$errors['result'] = $this->lang->review->checkResultList['resultError'];
                return $res;
            }
            //评审时间
            if (!$data->reviewedDate) {
                dao::$errors['reviewedDate'] = $this->lang->review->checkReviewOpResultList['reviewedDateError'];
                return $res;
            }

            //评审方式

            $isSetAdviceGrade =  $this->loadModel('review')->isSetAdviceGrade($reviewInfo, $this->app->user->account);
            if($isSetAdviceGrade){
                if (!$data->grade) {
                    dao::$errors['grade'] = $this->lang->review->checkReviewOpResultList['adviceGradeError'];
                    return $res;
                }
            }

            //线上评审校验
            if (in_array($reviewStatus, $this->lang->review->allowMeetingReviewStatusList)) {
                if (!(isset($data->meetingRealTime) && !empty($data->meetingRealTime))) {
                    dao::$errors['meetingRealTime'] = $this->lang->review->checkReviewOpResultList['meetingRealTimeEmpty'];
                    return $res;
                }
                if (!(isset($data->realExport) && !empty($data->realExport))) {
                    dao::$errors['realExport'] = $this->lang->review->checkReviewOpResultList['realExportEmpty'];
                    return $res;
                }
                if (!(isset($data->meetingConsumed) && !empty($data->meetingConsumed))) {
                    dao::$errors['meetingConsumed'] = $this->lang->review->checkReviewOpResultList['meetingConsumedEmpty'];
                    return $res;
                }
                $checkRes = $this->loadModel('consumed')->checkConsumedInfo($data->meetingConsumed, false, 'meetingConsumed');
                if (!$checkRes) {
                    dao::$errors['meetingConsumed'] = dao::$errors['meetingConsumed'];
                    return $res;
                }
                if (!isset($data->meetingContent) || empty($data->meetingContent)) {
                    dao::$errors['meetingContent'] = $this->lang->review->checkReviewOpResultList['meetingContentEmpty'];
                    return $res;
                }
                if (!isset($data->meetingSummary) || empty($data->meetingSummary)) {
                    dao::$errors['meetingSummary'] = $this->lang->review->checkReviewOpResultList['meetingSummaryEmpty'];
                    return $res;
                }

                //会议评审信息
                $meetingParams = new stdClass();
                $meetingParams->meetingRealTime = $data->meetingRealTime;
                $meetingParams->realExport = $data->realExport;
                $meetingParams->consumed = $data->meetingConsumed;
                $meetingParams->meetingContent = $data->meetingContent;
                $meetingParams->meetingSummary = $data->meetingSummary;
                unset($data->meetingRealTime);
                unset($data->realExport);
                unset($data->meetingConsumed);
                unset($data->meetingContent);
                unset($data->meetingSummary);
                $data->meetingParams = $meetingParams;
            }
        }

        //材料审核人
        if(in_array($reviewStatus, $this->lang->review->allowAssignVerifyersStatusList)){
            if($data->result == 'passNeedEdit'){
                if ((!isset($data->verifyReviewers) || !$data->verifyReviewers)  and $reviewInfo->status != 'waitOutReview' and $reviewInfo->status != 'outReviewing'){
                    dao::$errors['verifyReviewers'] = $this->lang->review->checkReviewOpResultList['verifyReviewersError'];
                    return $res;
                }
                if ((!isset($data->verifyDeadline) || !$data->verifyDeadline)  and $reviewInfo->status != 'waitOutReview' and $reviewInfo->status != 'outReviewing'){
                    dao::$errors['verifyDeadline'] = $this->lang->review->checkReviewOpResultList['verifyDeadlineError'];
                    return $res;
                }
            }
        }

        //线上评审结论
        if($reviewStatus == $this->lang->review->statusList['waitFormalOwnerReview']){ //线上评审结论
            //查询评审专家是否选择通过需要修改
            $nodeCode = 'formalReview';
            $resultStatusArray = ['pass'];
            $extra = '"isEditInfo":1';
            $reviewResultList = $this->getReviewResultList($reviewId, $version, $nodeCode, $resultStatusArray, 'id', $extra);
            if (!empty($reviewResultList)) { //有评审专家选择了审核通过需要修改
                if($reviewInfo->grade == 'online'){ //在线评审
                    if ($data->result == 'passNoNeedEdit') {//通过无需修改
                        dao::$errors[] = $this->lang->review->checkReviewOpResultList['reviewResultError'];
                        return $res;
                    }
                }elseif($reviewInfo->grade == 'meeting'){ //会议评审
                    if ($data->result == 'passNoNeedEdit') {//通过无需修改
                        dao::$errors[] = $this->lang->review->checkReviewOpResultList['meetingReviewResultError'];
                        return $res;
                    }
                }
            }
        }


        //评审主席
        if($reviewStatus == $this->lang->review->statusList['waitMeetingOwnerReview']){ //会议评审结论
            if($data->result == 'passNoNeedEdit') {//通过无需修改
                $reviewIssueCount = $this->loadModel('reviewissue')->getReviewIssueCount($reviewId);
                if ($reviewIssueCount > 0) {
                    dao::$errors[] = $this->lang->review->checkReviewOpResultList['reviewResultError'];
                    return $res;
                }
            }
        }

        if($reviewStatus == $this->lang->review->statusList['waitFormalOwnerReview']){ //会议评审结论{ //评审主席确定线上评审结论
            if ($data->result == 'meeting'){ //评审结果是会议评审
                //会议评审
                $meetingPlanType = $this->post->meetingPlanType ? $this->post->meetingPlanType: '';
                $meetingCode     = isset($data->meetingCode)? $data->meetingCode: '';
                $meetingPlanTime = isset($data->meetingPlanTime)? $data->meetingPlanTime: '';
                $checkRes = $this->checkReviewMeetingInfo($reviewInfo, $reviewInfo->type, $meetingPlanType, $meetingCode, $meetingPlanTime, $reviewInfo->owner);
                if(!$checkRes['result']){
                    return $res;
                }
            }
        }


        $res['result'] = true;
        if($data->result != 'reject'){
            if($data->result == 'meeting'){ //评审主席确定在线评审结论(继续会议评审)
                $data->grade = 'meeting';
            }elseif($data->result == 'passNeedEdit'){
                $data->isEditInfo = 1;
            }else{ //评审人员不需要显示审核通过是否修改资料
               if(!in_array($reviewStatus, $this->lang->review->allowVerifyReviewStatusList)){
                   $data->isEditInfo = 2;
                }
            }
            $data->result = 'pass';
        }else{
            $data->result = 'reject';
        }
        $res['data'] = $data;
        return $res;
    }

    /**
     *获得审核结果
     *
     * @param $reviewResult
     * @return stdClass
     */
    public function getReviewResultInfo($reviewResult){
        $data = new stdClass();
        if($reviewResult == 'reject'){
            $data->result = 'reject';
            $data->isEditInfo = '';
        }elseif($reviewResult == 'suspend'){
            $data->result = 'suspend';
            $data->isEditInfo = '';
        }else{
            $data->result = 'pass';
            if($reviewResult == 'passNeedEdit'){
                $data->isEditInfo = 1;
            }else{
                $data->isEditInfo = 2;
            }
        }
        return $data;
    }

    /**
     *获得评审结果列表
     *
     * @param $reviewId
     * @param $version
     * @param $nodeCode
     * @param $statusArray
     * @param $select
     * @param null $extra
     * @return array
     */
    public function getReviewResultList($reviewId, $version, $nodeCode, $statusArray, $select = '*', $extra = null){
        $data = [];
        if(!($reviewId && $nodeCode)){
            return $data;
        }
        $node = $this->dao->select('id')->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq('review')
            ->andWhere('objectID')->eq($reviewId)
            ->andWhere('version')->eq($version)
            ->andWhere('nodeCode')->eq($nodeCode)
            ->andWhere('status')->in($statusArray)
            ->orderBy('stage,id')
            ->fetch();
        if(!$node) {
            return  $data;
        }
        $reviewsList = $this->dao->select($select)->from(TABLE_REVIEWER)
            ->where('node')->eq($node->id)
            ->beginIF($extra)->andWhere('extra')->like('%'.$extra.'%')->fi()
            ->fetchAll();

        if($reviewsList) {
            $data = $reviewsList;
        }
        return $data;

    }

    /**
     *获得审核节点标识
     *
     * @param $status
     * @param $oldStatus
     * @return string
     */
    public function getReviewNodeCodeByStatus($status, $oldStatus){
        $nodeCode = '';
        switch ($status){
            case $this->lang->review->statusList['waitPreReview']:
                $nodeCode = $this->lang->review->nodeCodeList['preReview'];
                break;

            case $this->lang->review->statusList['waitFirstAssignDept']:
                $nodeCode = $this->lang->review->nodeCodeList['firstAssignDept'];
                break;

            case $this->lang->review->statusList['waitFirstAssignReviewer']:
            case $this->lang->review->statusList['firstAssigning']:
                $nodeCode = $this->lang->review->nodeCodeList['firstAssignReviewer'];
                break;

            case $this->lang->review->statusList['waitFirstReview']: //待初审
                $nodeCode = $this->lang->review->nodeCodeList['firstReview'];
                break;
            case $this->lang->review->statusList['firstReviewing']: //初审中
                $nodeCode = $this->lang->review->nodeCodeList['firstReview'];
                break;

            case $this->lang->review->statusList['waitFirstMainReview']: //待初审-主审
                $nodeCode = $this->lang->review->nodeCodeList['firstMainReview'];
                break;

            case $this->lang->review->statusList['firstMainReviewing']: //待初审-主审中
                $nodeCode = $this->lang->review->nodeCodeList['firstMainReview'];
                break;

            case $this->lang->review->statusList['waitFormalAssignReviewer']: //待正审指派
                if($status == $oldStatus){
                    $nodeCode = $this->lang->review->nodeCodeList['formalAssignReviewerAppoint']; //评审主席确定评审结论(委派)
                }else{
                    $nodeCode = $this->lang->review->nodeCodeList['formalAssignReviewer'];//评审主席确定评审结论
                }
                break;

            case $this->lang->review->statusList['waitFormalReview']:  //待正审-专家在线评审
                $nodeCode = $this->lang->review->nodeCodeList['formalReview'];
                break;

            case $this->lang->review->statusList['formalReviewing']: //正审中
                $nodeCode = $this->lang->review->nodeCodeList['formalReview'];
                break;

            case $this->lang->review->statusList['waitFormalOwnerReview']: //评审主席确定评审结论
                $nodeCode = $this->lang->review->nodeCodeList['formalOwnerReview'];//评审主席确定评审结论
                break;

            case $this->lang->review->statusList['waitMeetingReview']: //评审主席确定评审结论
                $nodeCode = $this->lang->review->nodeCodeList['meetingReview'];//会议评审
                break;

            case $this->lang->review->statusList['waitMeetingOwnerReview']: //评审主席确定会议评审结论
                $nodeCode = $this->lang->review->nodeCodeList['meetingOwnerReview'];//评审主席确定会议评审结论
                break;

            case $this->lang->review->statusList['waitVerify']: //待验证资料
            case $this->lang->review->statusList['verifying']: //验证资料中
//            case $this->lang->review->statusList['formalPassButEdit']: //正式审核通过待修改
//            case $this->lang->review->statusList['meetingPassButEdit']: //会议通过需修改资料
//            case $this->lang->review->statusList['outPassButEdit']: //外部审核通过待修改
                 $nodeCode = $this->lang->review->nodeCodeList['verify'];
                 break;

            case $this->lang->review->statusList['waitOutReview']: //待外部审核
            case $this->lang->review->statusList['outReviewing']: //外部审核中
                $nodeCode = $this->lang->review->nodeCodeList['outReview'];
                break;

            case $this->lang->review->statusList['archive']: //待归档
                $nodeCode = $this->lang->review->nodeCodeList['archive'];
                break;

            case $this->lang->review->statusList['baseline']: //待打基线
                $nodeCode = $this->lang->review->nodeCodeList['baseline'];
                break;

            case $this->lang->review->statusList['prePassButEdit']: //预审通过待修改
                $nodeCode = $this->lang->review->nodeCodeList['prePassButEdit'];
                break;

            case $this->lang->review->statusList['firstPassButEdit']: //初审通过待修改
                $nodeCode = $this->lang->review->nodeCodeList['firstPassButEdit'];
                break;
            case $this->lang->review->statusList['formalPassButEdit']: //在线待修改
                $nodeCode = $this->lang->review->nodeCodeList['formalPassButEdit'];
                break;

            case $this->lang->review->statusList['meetingPassButEdit']: //会议审核通过待修改
                $nodeCode = $this->lang->review->nodeCodeList['meetingPassButEdit'];
                break;

            case $this->lang->review->statusList['outPassButEdit']: //外部审核待修改
                $nodeCode = $this->lang->review->nodeCodeList['outPassButEdit'];
                break;

            case $this->lang->review->statusList['rejectVerify']: //验证退回待修改
                $nodeCode = $this->lang->review->nodeCodeList['rejectVerifyButEdit'];
                break;


            default:
                break;
        }
        return $nodeCode;
    }

    /**
     * 通过nodeCode获得对应状态
     *
     * @param $nodeCode
     * @return string
     */
    public function getReviewStatusByNodeCode($nodeCode){
        $status = '';
        switch ($nodeCode){
            case $this->lang->review->nodeCodeList['preReview']:
                $status = $this->lang->review->statusList['waitPreReview'];
                break;

            case $this->lang->review->nodeCodeList['firstAssignDept']:
                $status = $this->lang->review->statusList['waitFirstAssignDept'];
                break;

            case $this->lang->review->nodeCodeList['firstAssignReviewer']:
                $status = $this->lang->review->statusList['waitFirstAssignReviewer'];
                break;

            case $this->lang->review->nodeCodeList['firstReview']: //待初审
                $status = $this->lang->review->statusList['waitFirstReview'];
                break;

            case $this->lang->review->nodeCodeList['firstMainReview']: //待初审-主审
                $status = $this->lang->review->statusList['waitFirstMainReview'];
                break;

            case $this->lang->review->nodeCodeList['formalAssignReviewer']: //待正审指派
                $status = $this->lang->review->statusList['waitFormalAssignReviewer'];
                break;

            case $this->lang->review->nodeCodeList['formalAssignReviewerAppoint']: //待正审指派
                $status = $this->lang->review->statusList['waitFormalAssignReviewer'];
                break;

            case $this->lang->review->nodeCodeList['formalReview']:  //待正审-专家在线评审
                $status = $this->lang->review->statusList['waitFormalReview'];
                break;

            case $this->lang->review->nodeCodeList['formalOwnerReview']: //评审主席确定评审结论
                $status = $this->lang->review->statusList['waitFormalOwnerReview'];//评审主席确定评审结论
                break;

            case $this->lang->review->nodeCodeList['meetingReview']: //会议评审
                $status = $this->lang->review->statusList['waitMeetingReview'];//会议评审
                break;

            case $this->lang->review->nodeCodeList['meetingOwnerReview']: //评审主席确定会议评审结论
                $status = $this->lang->review->statusList['waitMeetingOwnerReview'];//评审主席确定会议评审结论
                break;

            case $this->lang->review->nodeCodeList['verify']: //待验证资料
                $status = $this->lang->review->statusList['waitVerify'];
                break;

            case $this->lang->review->nodeCodeList['outReview']: //待外部审核
                $status = $this->lang->review->statusList['waitOutReview'];
                break;

            case $this->lang->review->nodeCodeList['archive']: //待归档
                $status = $this->lang->review->statusList['archive'];
                break;

            case $this->lang->review->nodeCodeList['baseline']: //待打基线
                $status = $this->lang->review->statusList['baseline'];
                break;

            case $this->lang->review->nodeCodeList['prePassButEdit']: //预审通过待修改
                $status = $this->lang->review->statusList['prePassButEdit'];
                break;

            case $this->lang->review->nodeCodeList['firstPassButEdit']: //初审通过待修改
                $status = $this->lang->review->statusList['firstPassButEdit'];
                break;
            case $this->lang->review->nodeCodeList['formalPassButEdit']: //在线待修改
                $status = $this->lang->review->statusList['formalPassButEdit'];
                break;

            case $this->lang->review->nodeCodeList['meetingPassButEdit']: //会议审核通过待修改
                $status = $this->lang->review->statusList['meetingPassButEdit'];
                break;

            case  $this->lang->review->nodeCodeList['outPassButEdit']: //外部审核待修改
                $status = $this->lang->review->statusList['outPassButEdit'];
                break;

            case $this->lang->review->nodeCodeList['rejectVerifyButEdit']: //验证退回待修改
                $status = $this->lang->review->statusList['rejectVerify'];
                break;

            default:
                break;
        }
        return $status;
    }


    /**
     * 查询评审人 reviewer 表和 reviewnnode
     *
     * @param $users
     * @param $version
     * @param $subObjectType
     * @param $type
     * @param $stage
     * @param $flag
     * @return string
     */

    public function getReviewerName($users,$version,$subObjectType,$type,$stage,$flag){
        $txt='';
        $reviewnode = $this->dao->select('id')->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq("review")
            ->andWhere('version')->eq($version)
            ->andWhere('subObjectType')->in($subObjectType)
            ->andWhere('type')->eq($type)
            ->andWhere('stage')->eq($stage)
            ->fetch('id');
        $reviewer = $this->dao->select('reviewer')->from(TABLE_REVIEWER)
            ->where('node')->eq($reviewnode)->fetchAll();
        if(is_array($reviewer)){

            $reviewer = array_column( $reviewer,'reviewer');
            foreach($reviewer as $revi)
            {
                $revi = trim($revi);
                if(empty($revi)) continue;
                if($flag == ','){
                    $txt .= $revi . $flag;
                }else{
                    $txt .= zget($users,$revi) . $flag;
                }

            }
        }
        return $txt;
    }



    /**
     * Send mail.
     *
     * @param int $reviewID
     * @param int $actionID
     * @param int $isAutoSendMail
     * @param string $dealUser
     * @param string $realReview1
     * @param string $realReview2
     * @param string $realReview3
     * @return bool|void
     */
    public function sendmail($reviewID, $actionID, $isAutoSendMail = 0, $dealUser = '', $realReview1 = '', $realReview2 = '', $realReview3 = '')
    {
        $this->loadModel('mail');

        if($isAutoSendMail == 1){
            $isAutoSendMail = 1;
            $dealUser = $dealUser;
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
            $isAutoDealTime = true; //是否显示自动处理时间
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

                $this->mail->send($toList, $subject, $mailContent,$ccList,true);

                if($this->mail->isError()) trigger_error(join("\n", $this->mail->getError()));
            }
            if (!empty($realReview2)) {
                $reviewListSend = $realReview2;
                $mailConf->mailTitle = "【待办】您有【%s】个【项目评审】已超时，请尽快登录研发过程管理平台进行处理";
                $mailTitle = vsprintf($mailConf->mailTitle, count($reviewListSend));
                /* 处理邮件标题。*/
                $subject = $mailTitle;
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
                //$this->mail->send($toList, $subject, $mailContent,$ccList,true);

                //if($this->mail->isError()) trigger_error(join("\n", $this->mail->getError()));

            }
            if (!empty($realReview3)) {
                $emilAlertLevel = $this->lang->review->emilAlert;
                $reviewListSend = $realReview3;
                $mailConf->mailTitle = "【通知】您有【%s】个【项目评审】已逾期【%s】天，系统已自动处理，请登录研发过程管理平台查看";
                $mailTitle = vsprintf($mailConf->mailTitle, array(count($reviewListSend), $emilAlertLevel['level2']));
                /* 处理邮件标题。*/
                $subject = $mailTitle;
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
                $this->mail->send($toList, $subject, $mailContent,$ccList,true);

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
            $isDeadDate = false;
            if(in_array($review->status, $this->lang->review->closeStatusList)){
                if(in_array($review->status, array('drop', 'fail'))){
                    $review->dealUser = '';
                }elseif($review->comment == $this->lang->review->autoCloseComment && $review->status == 'archive'){
                    // 当前状态为待归档且关闭节点为自动关闭时 直接给待归档待处理人发邮件(跳过给关闭节点待处理人发邮件)
                    $toList = $review->dealUser;
                }
                $closeReasonDesc = zget($this->lang->review->closeReasonList, $review->status);
                $mailConf->mailTitle = "【通知】您有一个【%s】已关闭（{$closeReasonDesc}），请及时登录研发过程管理平台查看";
            }else if($review->status == $this->lang->review->statusList['suspend']){  //挂起
                $mailConf->mailTitle = "【通知】您有一个【%s】已挂起，请及时登录研发过程管理平台查看";
            }else{
                $isDeadDate = true;
            }
            $mailTitle = vsprintf($mailConf->mailTitle, $mailConf->variables);

            //显示截止日期
            if($isDeadDate){
                if($review->endDate && $review->endDate != '0000-00-00 00:00:00'){
                    $endDate = date('Y-m-d', strtotime($review->endDate));
                    $mailTitle .= '【截止日期：'. $endDate.'】';
                }
            }

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

//    public function autosendmail()
//    {
//        $reviewList = $this->getAllReviewList();
//        $dealUsers = '';
//        $dealUserList = array();
//        foreach ($reviewList as $review){
//            $dealUsers .=$review->dealUser."," ;
//        }
//        $currentDate = date('Y-m-d');
//        $dealUserList =Array_filter( array_unique(explode(',',$dealUsers)));
//        //获取配置的日期N和M
//        $emilAlertLevel = $this->lang->review->emilAlert;
//        $realReview1 = array();
//        $realReview2 = array();
//        $realReview3 = array();
//        foreach ($dealUserList as $dealUser) {
//            foreach ($reviewList as $review) {
//                if (in_array($review->status, $this->lang->review->allowAutoDealStatusList)) {
//                    if (strstr($review->dealUser, $dealUser) !== false) {
//                        $diffDays =  $this->getDiffDate($review->endDate,$currentDate);
//
//                        if ($review->endDate != '0000-00-00 00:00:00') {
//                            if ($diffDays != 0) {
//                                if ($diffDays == -$emilAlertLevel['level1']) {
//                                    $realReview1[] = $review;
//                                } elseif ($diffDays == $emilAlertLevel['level2']) {
//                                    $realReview2[] = $review;
//                                } elseif ($diffDays > $emilAlertLevel['level2']) {
//                                    $realReview3[] = $review;
//                                }
//                            }
//                        }
//                    }
//                }
//            }
//            $this->sendmail('', '', 1, $dealUser, $realReview1, $realReview2, $realReview3);
//
//            $realReview1 = [];
//            $realReview2 = [];
//            $realReview3 = [];
//        }
//        echo '发送成功';
//
//    }

    public function autosendmail()
    {
        $reviewList = $this->getAllReviewList();
        $dealUsers = '';
        $dealUserList = array();
        foreach ($reviewList as $review){
            $dealUsers .= $review->dealUser."," ;
        }
        $currentDate = date('Y-m-d');
        $dealUserList = Array_filter(array_unique(explode(',',$dealUsers)));
        //获取配置的日期N和M
        $emilAlertLevel = $this->lang->review->emilAlert;
        $realReview1 = array();
        $realReview2 = array();
        $realReview3 = array();
        foreach ($dealUserList as $dealUser) {
            foreach ($reviewList as $review) {
                if (in_array($review->status, $this->lang->review->allowAutoDealStatusList)) {
                    $dealUsers = array_filter(explode(',', $review->dealUser));
                    if (!empty($dealUsers) && in_array($dealUser, $dealUsers)) {
                        if ($review->endDate != '0000-00-00 00:00:00') {
                            $diffDays =  $this->getDiffDate($review->endDate,$currentDate);
                            //if ($diffDays != 0) {
                            if ($diffDays == -$emilAlertLevel['level1']) {
                                if(in_array($review->status, $this->lang->review->timeOutAutoDealStatusList)){
                                    //最小超时时间
                                    $minTimeOutDay = $this->loadModel('holiday')->getActualWorkingDate($review->endDate, $emilAlertLevel['level2']);
                                    //$review->autoDealTime = date('Y-m-d 4:00:00', strtotime("$minTimeOutDay + 1 days")); //如果逾期，逾期的处理时间
                                    $autoDealDay  = $this->loadModel('holiday')->getActualWorkingDate($minTimeOutDay, 1);
                                    $review->autoDealTime = date('Y-m-d 4:00:00', strtotime($autoDealDay)); //如果逾期，逾期的处理时间
                                }else{
                                    $review->autoDealTime = '';
                                }
                                $realReview1[] = $review;
                            } elseif ($diffDays == $emilAlertLevel['level2']) {
                                $realReview2[] = $review;
                            } elseif ($diffDays > $emilAlertLevel['level2']) {
                                $realReview3[] = $review;
                            }
                            //}
                        }
                    }
                }
            }

            if(!empty($realReview1) || !empty($realReview2) || !empty($realReview3)){
                $this->sendmail('', '', 1, $dealUser, $realReview1, $realReview2, $realReview3);

                $realReview1 = [];
                $realReview2 = [];
                $realReview3 = [];
            }
        }
        echo '发送成功';

    }
    /**
     * 自动处理评审
     */
    public function autodealreview()
    {
        $reviewList = $this->getAutoDealReviewList();
        $dealUsers = '';
        $dealUserList = array();
        foreach ($reviewList as $review){
            $dealUsers .=$review->dealUser."," ;
        }
        $currentDate = date('Y-m-d');
        $dealUserList = Array_filter( array_unique(explode(',',$dealUsers)));

        $isStart = $this->config->review->startTimeOut;
        $isStartList = Array_filter( array_unique(explode(',',$isStart)));

        //超时处理时间
        $reviewConsumed = $this->config->review->reviewConsumed->reviewConsumed;
        foreach ($dealUserList as $dealUser) {
            foreach ($reviewList as $review) {
                //判断评审类型是否需要开启异步超时处理
                $this->commonReviewDeal($dealUser,$currentDate,$isStartList,$reviewConsumed,$review);
            }
        }
        echo '处理成功';

    }

    /**
     * 处理确定初审结论（暂时不用）
     * @param $reviewID
     * @param $isEdit
     * @param $dealUser
     * @return false
     */
    public function dealFirstMainReview($review,$dealUser){
        $currentDate = date('Y-m-d');

        $isStart = $this->config->review->startTimeOut;
        $isStartList =Array_filter( array_unique(explode(',',$isStart)));

        //超时处理时间
        $reviewConsumed=$this->config->review->reviewConsumed->reviewConsumed;
        if (strstr($review->dealUser, $dealUser) !== false) {
            $statusFirstAndFormalList = ['waitFormalReview', 'formalReviewing', 'waitFirstReview', 'firstReviewing','waitFirstMainReview','firstMainReviewing'];
            if (in_array($review->type, $isStartList) and (in_array($review->status, $statusFirstAndFormalList))) {
                //判断是否是内部专家
                $outsideList1 = $this->loadModel('user')->getUsersNameByType('outsideExpertType');
                $outsideList2 = $this->loadModel('user')->getUsersNameByType('outside');
                $outsideUsers = array_merge($outsideList1, $outsideList2);

                $timeOut = $this->lang->review->timeOut;
                $timeValue = '';
                if (in_array($dealUser, array_keys($outsideUsers))) {
                    $timeValue = $timeOut['outside'];
                } else {
                    $timeValue = $timeOut['inside'];
                }
                $diffDays = $this->getDiffDate($review->endDate,$currentDate);
                //当当前时间-截至日期 > 超时阈值
                if ($diffDays > $timeValue && $diffDays != 0) {
                    //判断当前评审是否有该专家提出的问题
                    $issues = $this->loadModel('reviewissue')->getIssueByReview($review->id);
                    $count = 0;
                    foreach ($issues as $issue) {
                        if (($issue->createdBy == $dealUser or $issue->raiseBy == $dealUser) and $issue->deleted == 0) {
                            $count++;
                        }
                    }
                    $extra = new stdClass();
                    if($review->status == 'waitFirstMainReview' or $review->status=='firstMainReviewing'){
                        if($this->getIsEdit($review->id)==1){
                            $comment = '逾期自动处理';
                            $historyComment = '逾期自动处理：通过（需修改）';
                            //是否需要修改资料
                            $extra->isEditInfo = 1;
                            $extra->grade = $review->grade;
                            $extParams = [];
                            $extParams['isEditInfo'] = 1;
                            $extParams['grade'] = $review->grade;
                            $consumed = $reviewConsumed;
                        }else{
                            $comment = '逾期未提交意见';
                            $historyComment = '逾期未提交意见：通过（无需修改）';
                            //是否需要修改资料
                            $extra->isEditInfo = 2;
                            $extra->grade = $review->grade;
                            $extParams = [];
                            $extParams['isEditInfo'] = 2;
                            $extParams['grade'] = $review->grade;
                            $consumed = 0;
                        }
                    }
                    $version = $this->getReviewNodeMaxVersion($review->id);
                    $reviewResult = 'pass';
                    //处理审核操作
                    $result = $this->loadModel('review')->autoDealcheck('review', $review->id, $version, $reviewResult, $comment, $review->reviewStage, $extra,$dealUser);
                    $oldStatus = $review->status;
                    //下一个状态
                    $nextStatus = $this->getReviewNextStatus($review, $result, $extParams);

                    //修改信息
                    $updateParams = new stdClass();
                    $updateParams->status = $nextStatus;
                    $updateParams->lastReviewedBy = $dealUser;
                    $updateParams->lastReviewedDate = $currentDate;
                    if (in_array($nextStatus, $this->lang->review->sumitDateStatusList)) {
                        $updateParams->submitDate = date('Y-m-d');
                    }
                    if($nextStatus =='waitFirstMainReview' or $nextStatus=='firstMainReviewing'){
                        $endDate = $review->endDate;
                    }else{
                        if(isset($updateParams->submitDate)){
                            $endDate = $this->getEndDate($nextStatus, $updateParams->submitDate, $review);
                        }

                    }

                    if (isset($endDate) && in_array($nextStatus, $this->lang->review->sumitDateStatusList)) {
                        $updateParams->endDate = $endDate;
                    }

                    $this->dao->update(TABLE_REVIEW)->data($updateParams)->where('id')->eq($review->id)->exec();
                    if (dao::isError()) {
                        return false;
                    }
                    //工时
                    if (isset($extra->appointUser)) {
                        $consumedExtra = $extra;
                    } else {
                        $consumedExtra = $extra->isEditInfo;
                    }

                    $this->loadModel('consumed')->record('review', $review->id, $consumed, $dealUser, $oldStatus, $nextStatus, '', $consumedExtra);

                    //是否需要增加审核节点
                    $isAddNode = $this->getIsAddReviewNode($nextStatus);
                    if ($isAddNode) {
                        $newReviewInfo = $this->getByID($review->id);
                        $res = $this->addReviewNode($newReviewInfo, $oldStatus, '');
                    }
                    //if (!in_array($nextStatus, $this->lang->review->allowAdvanceSetReviewersStatusList)) { //状态是通过待修改（中间需要经过编辑申请评审才可以评审操作）
                        if ($result == 'pass') {
                            $next = $this->dao->select('id')->from(TABLE_REVIEWNODE)->where('objectType')->eq('review')
                                ->andWhere('objectID')->eq($review->id)
                                ->andWhere('version')->eq($version)
                                ->andWhere('status')->eq('wait')->orderBy('stage,id')->fetch('id');
                            //有其他审核节点
                            if ($next) {
                                $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('pending')->where('id')->eq($next)->exec();
                                $this->dao->update(TABLE_REVIEWER)->set('status')->eq('pending')->where('node')->eq($next)->exec();
                            }
                        }
                    //}
                    //处理人
                    $newReviewInfo = $this->getByID($review->id);
                    $nextDealUser = $this->getNextDealUser($newReviewInfo, $newReviewInfo->status);
                    if($nextStatus =='firstPassButEdit'){
                        $nextDealUser = $review->createdBy;
                    }
                    if ($nextDealUser != $newReviewInfo->dealUser) {
                        $tempUpdateParams = new stdClass();
                        $tempUpdateParams->dealUser = $nextDealUser;
                        $this->dao->update(TABLE_REVIEW)->data($tempUpdateParams)->where('id')->eq($review->id)->exec();
                        $updateParams->dealUser = $nextDealUser;
                    }

                    $logChanges = common::createChanges($review, $updateParams, '');
                    $isSetHistory = true;
                    $actionID = $this->loadModel('action')->create('review', $review->id, 'reviewed', $historyComment, $extra, $dealUser, true, $isSetHistory, $logChanges);

                }
            }
        }

    }
    /**
     * 判断初审是否含有初审待修改
     * @param $reviewID
     * @return int
     */
    public function getIsEdit($reviewID){
        $version = $this->getReviewNodeMaxVersion($reviewID);
        $reviews= $this->dao->select('t3.extra')->from(TABLE_REVIEW)->alias('t1')
            ->leftJoin(TABLE_REVIEWNODE)->alias('t2')
            ->on('t1.id=t2.objectID')
            ->leftJoin(TABLE_REVIEWER)->alias('t3')
            ->on('t2.id=t3.node')
            ->where('t1.deleted')->eq(0)
            ->andWhere('t1.id')->eq($reviewID)
            ->andWhere('t2.objectType')->eq('review')
            ->andWhere('t2.version')->eq($version)
            ->andWhere('t2.nodeCode')->eq('firstReview')
            ->fetchAll();
        $result = 2;
       if(!empty($reviews)){
           foreach ($reviews as $review){
              if(json_decode($review->extra)->isEditInfo ==1){
                  $result =1;
              }
           }
       }
        return $result;
    }

    /**
     *获得全部评审信息
     */
    public function getAllReviewList(){
        $data = [];
        $startTimeOutTypes = $this->config->review->startTimeOut;
        $startTimeOutTypes = array_filter(explode(',', $startTimeOutTypes));
        if(empty($startTimeOutTypes)){
            return $data;
        }
        $data = $this->dao->select('*')->from(TABLE_REVIEW)
            ->where('deleted')->eq(0)
            ->andWhere('endDate')->ne('0000-00-00 00:00:00')
            ->andWhere('status')->notin('pass,fail,drop,reviewpass')
            ->andWhere('type')->in($startTimeOutTypes)
            ->fetchAll();
        return $data;
    }
    /**
     *获得全部评审信息
     */
    public function getAutoDealReviewList(){
        $data = [];
        $startTimeOutTypes = $this->config->review->startTimeOut;
        $startTimeOutTypes = array_filter(explode(',', $startTimeOutTypes));
        if(empty($startTimeOutTypes)){
            return $data;
        }
        $data = $this->dao->select('*')->from(TABLE_REVIEW)
            ->where('deleted')->eq(0)
            ->andWhere('endDate')->ne('0000-00-00 00:00:00')
            ->andWhere('status')->in('waitFirstReview,firstReviewing,waitFormalReview,formalReviewing,waitFirstMainReview,firstMainReviewing')
            ->andWhere('type')->in($startTimeOutTypes)
            ->fetchAll();
        return $data;
    }

    /**
     * 获得两个时间之间的相差工作日时间
     *
     * @param $begin
     * @param $end
     * @return int
     */
    public function getDiffDate($begin,$end){
        if(empty($end)){
            return 0;
        }
        $begin = date('Y-m-d', strtotime($begin));
        $end = date('Y-m-d', strtotime($end));
        if(strtotime($begin) > strtotime($end)){
            $fullDays   = $this->loadModel('holiday')->getActualWorkingDays($end,$begin);
           if(count($fullDays) == 0){
               return 0;
           }else{
               return 1-count($fullDays);
           }
        }
        $fullDays   = $this->loadModel('holiday')->getActualWorkingDays($begin,$end);
        if(count($fullDays) == 0 ){
            return count($fullDays);
        }
        $diffDays=count($fullDays)-1;

        return $diffDays;
    }

    /**
     *获得会议信息
     *
     * @param $meetingCode
     * @param $reviewId
     * @return stdClass|void
     */
    public function getMeetingDetailInfo($meetingCode, $reviewId){
        $data = new stdClass();
        if(!($meetingCode && $reviewId)){
            return $data;
        }
        $ret = $this->dao->select('*')->from(TABLE_REVIEW_MEETING_DETAIL)
            ->where('meetingCode')->eq($meetingCode)
            ->andWhere('review_id')->eq($reviewId)
            ->fetch();
        if($ret){
            $data = $ret;
        }
        return $data;
    }

    /**
     *获得收件人和抄送人信息
     *
     * @param $reviewInfo
     * @return string[]|void
     */
    public function getPendingToAndCcList($reviewInfo, $action = null){
        $toList = '';
        $ccList = '';
        $data = [
            'toList' => $toList,
            'ccList' => $ccList,
        ];
        if(!$reviewInfo){
            return $data;
        }
        $status  = $reviewInfo->status;
        if(in_array($status, $this->lang->review->notSendMailstatusList)){ //无需发邮件状态
            //可能需要发邮件
            if($status == $this->lang->review->statusList['verifying']){ //验证材料审核，可能出现指派
                if(!empty($action) && isset($action->history) && !empty($action->history)){
                    $history = $action->history;
                    $appointUser = '';
                    $mailto = '';
                    $isAppointUser = false;
                    foreach ($history as $val){
                        if($val->field == 'appointUser'){
                            $isAppointUser = true;
                            $appointUser = $val->new;
                        }
                        if($val->field == 'mailto'){
                            $mailto = $val->new;
                            if($mailto){
                                $mailtoArray = explode(' ', $mailto);
                                $mailto = implode(',', $mailtoArray);
                            }
                        }
                    }
                    //委托发邮件
                    if($isAppointUser){
                        $toList = $appointUser;
                        $ccList = $mailto;
                    }
                }
            }
            $data['toList'] = $toList;
            $data['ccList'] = $ccList;
            return $data;
        }

        //需要发邮件
        $reviewID = $reviewInfo->id;
        $status  = $reviewInfo->status;
        $version = $reviewInfo->version;
        //待处理人
        $dealUser = $reviewInfo->dealUser;
        //允许审核
        $allowReviewStatusList = $this->lang->review->allowReviewStatusList;
        //允许指派
        $allowAssignStatusList = $this->lang->review->allowAssignStatusList;
        $allowReviewStatusList = array_values($allowReviewStatusList);

        if(isset($this->lang->review->sendMailCombineStatusList[$status])){
            $statusArray = $this->lang->review->sendMailCombineStatusList[$status];
        }else{
            $statusArray = [$status];
        }
        if (in_array($status, $this->lang->review->closeStatusList)){ //关闭以后的流转状态（关闭评审以后的状态）
            $toList  = $reviewInfo->closeMailAccount;
            $ccList = $this->getCcList($reviewID, $statusArray,0, $version);
        }elseif($status == $this->lang->review->statusList['suspend']){ //已挂起
            if(!$reviewInfo->meetingCode){ //项目评审过程中挂起
                $mailToUsers = [$reviewInfo->owner, $reviewInfo->createdBy , $reviewInfo->reviewer];
                //部门负责人
                $createdDept = $reviewInfo->createdDept;
                $deptInfo = $this->loadModel('dept')->getByID($createdDept);
                $manager = $deptInfo->manager;
                $managerArray = explode(',', $manager);
                $mailToUsers = array_merge($mailToUsers, $managerArray);
                $toList  = implode(',', $mailToUsers);
                $ccList  = '';
            }else{ //确定会议评审结论挂起
                //发送人
                $toList = $dealUser;
                $ccList = $this->getOtherReviewCcUsers($reviewID, $statusArray, 0, $version, $action);
            }
        }else if(in_array($status, $allowReviewStatusList) || in_array($status, $allowAssignStatusList)){  //审核节点（审核或者指派）
            //评审人
            $reviewers = $reviewInfo->reviewers;
            $toList = $reviewers;
            $ccList = $this->getCcList($reviewID, $statusArray,0, $version);
            if($status == $this->lang->review->statusList['waitFormalReview']){ //评审主席指派完毕，等待正式线上审核
                $toList .=  ',' . $reviewInfo->outside; //外部专家2
            }else if($status == $this->lang->review->statusList['waitVerify']){ //待验证
                if(isset($action) && ($action->action == 'applyreview') && ($reviewInfo->unDealIssueRaiseByUsers)){
                    $toList .=  ',' . $reviewInfo->unDealIssueRaiseByUsers; //未验证问题的提出人
                }
            }
        }else {
            //发送人
            $toList = $dealUser;
           if ($status == $this->lang->review->statusList['waitApply'] && $reviewInfo->rejectStage == $this->lang->review->rejectStageList[11]) { //挂起后恢复
                //抄送人评审主席和部门领导多人
                $mailToUsers = [$reviewInfo->owner];
                //部门负责人
                $createdDept = $reviewInfo->createdDept;
                $deptInfo = $this->loadModel('dept')->getByID($createdDept);
                $manager = $deptInfo->manager;
                $managerArray = explode(',', $manager);
                $mailToUsers = array_merge($mailToUsers, $managerArray);
                $ccList  = implode(',', $mailToUsers);
            } else {
                $ccList = $this->getOtherReviewCcUsers($reviewID, $statusArray, 0, $version, $action);
            }
        }
        $data['toList'] = $toList;
        $data['ccList'] = $ccList;
        return $data;
    }

    /**
     *获得其他评审方法审核发送邮件的抄送人
     *
     * @param $reviewID
     * @param $statusArray
     * @param string $flag
     * @param int $version
     * @param null $action
     * @return string
     */
    public function getOtherReviewCcUsers($reviewID, $statusArray, $flag = '', $version = 0, $action = null){
         $ccList = $this->getCcList($reviewID, $statusArray, $flag, $version);
        if (!empty($action) && isset($action->history) && !empty($action->history)) {
            $history = $action->history;
            $isMeetingOwnerReview = false; //是否是评审主席确定线上评审结论
            $mailto = '';
            foreach ($history as $val) {
                if ($val->field == 'status' && $val->old == 'waitMeetingOwnerReview') {
                    $isMeetingOwnerReview = true;
                }
                if ($val->field == 'mailto') {
                    $mailto = $val->new;
                }
            }
            //评审主席确定线上评审结论添加抄送
            if ($isMeetingOwnerReview) {
                if ($ccList) {
                    $ccList .= ',' . $mailto;
                } else {
                    $ccList = $mailto;
                }
            }
        }
         return $ccList;
    }


    /**
     * 关闭评审
     *
     * @param $reviewID
     * @return array
     */
    public function close($reviewID){
        $today = helper::now();
        $oldReview = $this->getByID($reviewID);
        $data = fixer::input('post')
            ->stripTags($this->config->review->editor->close['id'], $this->config->allowedTags)
            ->join('closeMailAccount', ',')
            ->remove('uid,mailto,consumed')
            ->add('closeDate', helper::today())
            ->add('closeTime', $today)
            ->add('closePerson', $this->app->user->account)
            ->get();

        //质量部CM不随此post数据改变，此处只是邮件的收件人
        if(isset($data->closeMailAccount)){
            $data->closeMailAccount = trim($data->closeMailAccount, ',');
        }
        /*$consumed = $this->post->consumed;
        if($consumed != 0 || $consumed != '0.0'){
            $checkRes = $this->loadModel('consumed')->checkConsumedInfo($consumed);
            if(!$checkRes){
                return false;
            }
        }*/
        //如果中途关闭则将pending和wait的状态置为ignore
        $maxVersion = $this->getReviewNodeMaxVersion($reviewID);
        $needDealIgnore = $this->loadModel('review')->getUnDealReviewNodes('review', $reviewID, $maxVersion);
        if(!empty($needDealIgnore)){
            $ret = $this->loadModel('review')->ignoreReviewNodeAndReviewers($needDealIgnore);
        }
        //有会议号
        if($oldReview->meetingCode){
            if(in_array($oldReview->status, $this->lang->review->inMeetingReviewStatusList)){
                $meetingDetailInfo = $this->loadModel('reviewmeeting')->getMeetingDetailInfoByReviewId($reviewID);
                if($meetingDetailInfo){
                    $this->loadModel('reviewmeeting')->cancelBindReviewMeeting($oldReview->meetingCode, $reviewID);
                }
            }
        }

        if($data->status == 'reviewpass') {
            $data->status = $this->lang->review->statusList['archive']; //待归档
        }
        //下一步审核状态
        $nextStatus = $data->status;
        //下一步处理人
        $nextDealUser = $this->getNextDealUser($oldReview, $nextStatus);
        $data->dealUser = $nextDealUser;
        //质量部CM不根据收件人变化，只作为邮件的接收人用。
        $this->dao->update(TABLE_REVIEW)->data($data)->autoCheck()
            ->batchCheck($this->config->review->close->requiredFields, 'notempty')
            ->where('id')->eq($reviewID)
            ->exec();

        //是否需要增加审核节点
        $isAddNode = $this->getIsAddReviewNode($nextStatus);
        if($isAddNode){
            $newReviewInfo = $this->getByID($reviewID);
            $oldStatus = $oldReview->status;
            $res = $this->addReviewNode($newReviewInfo, $oldStatus);
            
            //设置成待处理
            $version = $newReviewInfo->version;
            $next = $this->dao->select('id')->from(TABLE_REVIEWNODE)->where('objectType')->eq('review')
                ->andWhere('objectID')->eq($reviewID)
                ->andWhere('version')->eq($version)
                ->andWhere('status')->eq('wait')->orderBy('stage,id')->fetch('id');
            //有其他审核节点
            if($next) {
                $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('pending')->where('id')->eq($next)->exec();
                $this->dao->update(TABLE_REVIEWER)->set('status')->eq('pending')->where('node')->eq($next)->exec();
            }
        }

        $this->loadModel('consumed')->record('review', $reviewID, '0', $this->app->user->account, $oldReview->status, $nextStatus, $this->post->mailto, '', $oldReview->version);
        $reason = '1002';//代表评审加入
        $this->deleteWhiteList($reviewID,$reason);//删除白名单

        //获得差异信息
        $ext = new stdClass();
        $ext->old = '';
        $ext->new  = isset($_POST['mailto'])  ?  implode(' ',$_POST['mailto']) :'';
        return common::createChanges($oldReview, $data,array('mailto' => $ext));
    }

    /**
     * 自动关闭评审
     *
     * @param $reviewID
     */
    public function autoclose($reviewID){
        $oldReview = $this->getByID($reviewID);
        $mailUsers = $this->getCloseMailUsersInfo($oldReview);
        $mailMainUsers = $mailUsers['mailMainUsers'];
        $mailCopyUsers = $mailUsers['mailCopyUsers'];
        $data = new stdClass();
        $data->status = $this->lang->review->statusList['archive']; //待归档
        $data->comment = $this->lang->review->autoCloseComment;
        $data->closeDate = helper::today();
        $data->closeTime = helper::now();
        $data->closePerson = $oldReview->reviewer;
        $consumed = 0;//0.1;
        $data->closeMailAccount = implode(',', $mailMainUsers);

        //有会议号
        if($oldReview->meetingCode){
            if(in_array($oldReview->status, $this->lang->review->inMeetingReviewStatusList)){
                $meetingDetailInfo = $this->loadModel('reviewmeeting')->getMeetingDetailInfoByReviewId($reviewID);
                if($meetingDetailInfo){
                    $this->loadModel('reviewmeeting')->cancelBindReviewMeeting($oldReview->meetingCode, $reviewID);
                }
            }
        }

        //下一步审核状态
        $nextStatus = $data->status;
        //下一步处理人
        $nextDealUser = $this->getNextDealUser($oldReview, $nextStatus);
        $data->dealUser = $nextDealUser;
        //质量部CM不根据收件人变化，只作为邮件的接收人用。
        $this->dao->update(TABLE_REVIEW)->data($data)->autoCheck()
            ->batchCheck($this->config->review->close->requiredFields, 'notempty')
            ->where('id')->eq($reviewID)
            ->exec();

        //增加审核节点
        $newReviewInfo = $this->getByID($reviewID);
        $oldStatus = $oldReview->status;
        $res = $this->addReviewNode($newReviewInfo, $oldStatus);

        //设置成待处理
        $version = $newReviewInfo->version;
        $ret = $this->loadModel('review')->setNextReviewNodePending('review', $reviewID, $version);

        $this->loadModel('consumed')->record('review', $reviewID, $consumed, 'guestjk', $oldReview->status, $nextStatus, $mailCopyUsers, '', $oldReview->version);

        $reason = '1002';//代表评审加入
        $this->deleteWhiteList($reviewID,$reason);//删除白名单

        // 记录历史记录
        $closeID = $this->loadModel('action')->create('review', $reviewID, 'autoclosed','','','guestjk');
        $changes = common::createChanges($oldReview, $data);
        $this->action->logHistory($closeID, $changes);
    }

    /**
     * 关闭评审（历史方法)
     *
     * @param $reviewID
     * @return array
     */
    public function closeOld($reviewID){

        $today = helper::now();
        $oldReview = $this->getByID($reviewID);
        $data = fixer::input('post')
            ->stripTags($this->config->review->editor->close['id'], $this->config->allowedTags)
            ->join('closeMailAccount', ',')
            ->remove('uid,mailto,consumed')
            ->add('closeDate', helper::today())
            ->add('closeTime', $today)
            ->add('closePerson', $this->app->user->account)
            ->get();

        //质量部CM不随此post数据改变，此处只是邮件的收件人
        if(isset($data->closeMailAccount)){
            $data->closeMailAccount = trim($data->closeMailAccount, ',');
        }
        $consumed = $this->post->consumed;
        if($consumed != 0 || $consumed != '0.0'){
            $checkRes = $this->loadModel('consumed')->checkConsumedInfo($consumed);
            if(!$checkRes){
                return false;
            }
        }
        //如果中途关闭则将pending和wait的状态置为ignore
        $masVersion = $this->getReviewNodeMaxVersion($reviewID);
        $needDealIgnore = $this->loadModel('review')->getUnDealReviewNodes('review', $reviewID, $masVersion);
        if(!empty($needDealIgnore)){
            $ret = $this->loadModel('review')->ignoreReviewNodeAndReviewers($needDealIgnore);
        }
        //有会议号
        if($oldReview->meetingCode){
            if(in_array($oldReview->status, $this->lang->review->inMeetingReviewStatusList)){
                $meetingDetailInfo = $this->loadModel('reviewmeeting')->getMeetingDetailInfoByReviewId($reviewID);
                if($meetingDetailInfo){
                    $this->loadModel('reviewmeeting')->cancelBindReviewMeeting($oldReview->meetingCode, $reviewID);
                }
            }
        }

        if($data->status == 'reviewpass'){
            $data->status = 'baseline';
            $data->submitDate = helper::now();
            $endDate = $this->getEndDate('baseline',$data->submitDate, $oldReview);
            $data->endDate = $endDate;
            $maxStage = $this->loadModel('review')->getReviewMaxStage($reviewID, 'review', $oldReview->version);
            //构造reviewNode数据
            $stage = $maxStage + 1;
            $data->dealUser = $oldReview->qualityCm;
            $reviewNodeList = new stdClass();
            $reviewNodeList->status = 'pending';
            $reviewNodeList->subObjectType = 'baseline';
            $reviewNodeList->objectType = 'review';
            $reviewNodeList->objectID = $reviewID;
            $reviewNodeList->stage = $stage;
            $reviewNodeList->createdBy = $this->app->user->account;
            $reviewNodeList->createdDate = helper::today();
            $reviewNodeList->nodeCode = 'baseline';
            $reviewNodeList->version = $oldReview->version;
            $this->dao->insert(TABLE_REVIEWNODE)->data($reviewNodeList)
                ->autoCheck()
                ->exec();

            $nodeId = $this->dao->lastInsertID();
            $extra = [];
            $extra['reviewedDate'] = $data->closeDate;
            $extra['isEditInfo'] = 2;
            //构造reviewer数据
            $reviewerList = new stdClass();
            $reviewerList->node = $nodeId;
            $reviewerList->reviewer = $oldReview->qualityCm;
            $reviewerList->status = 'pending';
            $reviewerList->grade = 0;
            $reviewerList->comment = '';
            $reviewerList->extra = json_encode($extra);
            $reviewerList->createdBy = $this->app->user->account;
            $reviewerList->createdDate = helper::today();
            $this->dao->insert(TABLE_REVIEWER)->data($reviewerList)
                ->autoCheck()
                ->exec();
        }else{
            $data->dealUser = '';
        }

        //质量部CM不根据收件人变化，只作为邮件的接收人用。
        $this->dao->update(TABLE_REVIEW)->data($data)->autoCheck()
            ->batchCheck($this->config->review->close->requiredFields, 'notempty')
            ->where('id')->eq($reviewID)
            ->exec();
        $this->loadModel('consumed')->record('review', $reviewID, $this->post->consumed, $this->app->user->account, $oldReview->status, $data->status, $this->post->mailto, '', $oldReview->version);
        $reason = '1002';//代表评审加入
        $this->deleteWhiteList($reviewID,$reason);//删除白名单
        //获得差异信息
        $ext = new stdClass();
        $ext->old = '';
        $ext->new  = isset($_POST['mailto'])  ?  implode(' ',$_POST['mailto']) :'';
        return common::createChanges($oldReview, $data,array('mailto'=>$ext));
    }


    /**
     * Edit a review.
     *
     * @param $reviewID
     * @return array|bool|void
     */
    public function update($reviewID)
    {
        $oldReview = $this->getByID($reviewID);
        $today  = helper::now();
        if(count($oldReview->files) == 0){
            if(is_array($this->post->files) && count($this->post->files)){
                return dao::$errors = array('files' => $this->lang->review->filesEmpty);
            }
        }

        $review = fixer::input('post')
            ->add('editBy', $this->app->user->account)
            ->add('editDate', $today)
            ->add('dealUser', $this->app->user->account)
            ->join('owner', ',')
            ->join('expert', ',')
            ->join('reviewedBy', ',')
            ->join('outside', ',')
            ->join('relatedUsers', ',')
            ->join('object', ',')
            ->remove('uid,files,consumed')
            ->add('lastEditedBy', $this->app->user->account)
            ->add('lastEditedDate', helper::today())
            ->get();
      /*  if(!ctype_digit($this->post->consumed))
        {
            $errors['consumed'] = sprintf($this->lang->review->noNumeric, $this->lang->review->consumed);
            return dao::$errors = $errors;
        }*/
       /* $checkRes = $this->loadModel('consumed')->checkConsumedInfo($this->post->consumed);
        if(!$checkRes){
            return false;
        }*/
        if(!isset($_POST['relatedUsers'])&&empty($_POST['relatedUsers']))
        {
            return dao::$errors['relatedUsers'] = $this->lang->review->relatedUsersEmpty ;
        }

        /*
        //判断评审方式是否为空
        if(!isset($_POST['grade'])){
            $review->grade = '';
        }
        */
        //判断评审专家是否为空
        if(!isset($_POST['expert'])){
            $review->expert = '';
        }
        //判断评审参与人员是否为空
        if(!isset($_POST['reviewedBy'])){
            $review->reviewedBy = '';
        }
        //判断外部人员是否为空
        if(!isset($_POST['outside'])){
            $review->outside = '';
        }
        //判断备注是否为空
        if(!isset($_POST['comment'])){
            $review->comment = '';
        }
        //查询标题是否存在
        $checkRes = $this->getTitleIsExist($review->title, $reviewID);
        if($checkRes){
            return dao::$errors['title'] = $this->lang->review->titleError ;
        }
        //是否初审
        if(isset($review->type) && ($review->type != $oldReview->type)){
            $review->isFirstReview = $this->getIsFirstReview($review->type);
        }
        
        if(in_array($oldReview->status, $this->lang->review->rejectStatusList) || $oldReview->status == $this->lang->review->statusList['recall']){ //驳回或者撤回编辑
            $review->status = $this->lang->review->statusList['waitApply']; //待审核
            $review->version = $oldReview->version + 1;
        }else{
            $review->status  = $oldReview->status;
            $review->version = $oldReview->version;
        }
        //项目包含主从项目信息
        $review->mainRelationInfo  = $review->mainRelationInfo   == $this->lang->review->noRelationRecord ? '': $review->mainRelationInfo;
        $review->slaveRelationInfo = $review->slaveRelationInfo  == $this->lang->review->noRelationRecord ? '': $review->slaveRelationInfo;

        $this->dao->update(TABLE_REVIEW)->data($review)
            ->autoCheck()
            ->batchCheck($this->config->review->edit->requiredFields, 'notempty')
            ->where('id')->eq($reviewID)
            ->exec();
        $this->loadModel('consumed')->record('review', $reviewID, '0', $this->app->user->account, $oldReview->status, $review->status, array(), '', $review->version);

        $this->loadModel('file')->updateObjectID($this->post->uid, $reviewID, 'review');
        $this->file->saveUpload('review', $reviewID);

        // 加入白名单
        $expert =  isset($_POST['expert']) ? $_POST['expert'] : array();
        $reviewedBy =  isset($_POST['reviewedBy']) ? $_POST['reviewedBy'] : array();
        $outside =  isset($_POST['outside']) ? $_POST['outside'] : array();

       // $allUser = array_unique( array_merge((array)$review->qa,(array)$review->reviewer,(array)$review->owner,$expert,$reviewedBy,(array)$review->outside));
        $allUser = array_unique( array_merge((array)$review->qa,(array)$review->reviewer,(array)$review->owner,$expert,$reviewedBy,$outside));
        $allUser = array_filter($allUser);
        foreach ($allUser as $user){
            if(empty($user)) continue;
            $this->addProjectReviewWhitelist($oldReview->project, $reviewID, $user);
        }
        if(!dao::isError()) return common::createChanges($oldReview, $review);

        return false;
    }

    /**
     * Get all issue for review.
     *
     * @param  int    $projectID
     * @access public
     * @return object
     */
    public function getReviewIssue($projectID,$reviewID)
    {
       return  $this->dao->select('t1.*,t2.title as reviewtitle')->from(TABLE_REVIEWISSUE)->alias('t1')
            ->leftJoin(TABLE_REVIEW)->alias('t2')
            ->on('t1.review = t2.id')
            ->Where('t1.deleted')->eq('0')
            ->andWhere('t2.project')->eq($projectID)
            ->andWhere('t1.review')->eq($reviewID)
            ->fetchAll();
    }

    /**
     *获得审核节点的最大版本
     *
     * @param $reviewID
     * @return int|void
     */
    public function getReviewNodeMaxVersion($reviewID){
        $maxVersion = 0;
        if(!$reviewID){
            return $maxVersion;
        }
        $ret = $this->dao->select('version')
            ->from(TABLE_REVIEWNODE)
            ->Where('objectID')->eq($reviewID)
            ->andWhere('objectType')->eq('review')
            ->orderBy('version_desc')
            ->fetch();
        if(!empty($ret)){
            $maxVersion = $ret->version;
        }
        return $maxVersion;
    }



    /**
     * 获得审核节点审核人信息
     *
     * @param $reviewID
     * @return array
     */
    public function getReviewNodeFormatReviewerList($reviewID){
        $data = array();
        $maxVersion = $this->getReviewNodeMaxVersion($reviewID);
        $users  = $this->loadModel('user')->getPairs('noletter');
        $trial = $this->getTrial($reviewID, $maxVersion, $users,2);
        //部门id
        $deptid = array_filter(explode(' ',$trial['deptid']));
        //正式人员
        $deptzs = array_filter(explode(' ',$trial['deptzs']));
        //参与人员
        $deptjoin = array_filter(explode(' ',$trial['deptjoin']));

        $reviewNodeList =  $this->dao->select('id, stage, status, nodeCode')
            ->from(TABLE_REVIEWNODE)
            ->Where('objectID')->eq($reviewID)
            ->andWhere('objectType')->eq('review')
            ->andWhere('version')->eq($maxVersion)
            ->orderBy('stage')
            ->fetchAll();
        if(empty($reviewNodeList)){
            return $data;
        }
        $nodeIds = array_column($reviewNodeList,'id');

        $reviewerList = $this->dao->select('*')->from(TABLE_REVIEWER)->Where('node')->in($nodeIds)->groupBy('node, reviewer, status')->orderBy('id')->fetchAll();
        if(empty($reviewerList)){
            return $data;
        }
        //每个节点下的审核人
        $reviewers = [];
        foreach ($reviewerList as $val){
            $node = $val->node;
            $val->extraInfo = [];
            if($val->extra){
                $val->extraInfo = json_decode($val->extra, true);
            }
            $reviewers[$node][] = $val;
        }

        foreach ($reviewers as $key => $item) {
            $reviewers[$key]['total'] = count($item);
        }
        $nowbefore = $this->getCcList($reviewID,'waitFormalAssignReviewer',1, $maxVersion);
        foreach ($reviewNodeList as $val){
            $nodeId   = $val->id;
            $nodeCode = $val->nodeCode;
            $nodeCodeStage = zget($this->lang->review->nodeCodeStageList, $nodeCode);
            $val->reviewers = [];
            if(isset($reviewers[$nodeId])){
                $val->reviewers = $reviewers[$nodeId];
            }
            if($val->nodeCode == 'firstAssignDept'){
                $val->dept = $deptid;
                $val->nowbefore = $nowbefore;
            }
            if($val->nodeCode == 'firstAssignReviewer'){
                $dept = array_merge($deptzs,$deptjoin);
                if($dept){
                    $dept = array_flip(array_flip($dept));
                }
                $val->deptzs = isset($dept) ? $dept : array();
            }
            $data[$nodeCodeStage]['data'][] = $val;
            if(!isset($data[$nodeCodeStage]['total'])){
                $data[$nodeCodeStage]['total'] = 0;
            }
            //节点下总的用户数量
            $data[$nodeCodeStage]['total'] += ($val->reviewers)['total'];
        }
        //查询评审详情信息
        $reviewInfo = $this->getReviewMainInfoByID($reviewID);
        $status = $reviewInfo->status;
        $nodeCodeStage = zget($this->lang->review->nodeCodeStageList, $status);

        //判断是否关闭，关闭加入
        $closeReview = new stdClass();
        $closeReview->status = $status;
        $closeReview->nodeCode = 'close';
        $rev = new stdClass();
        //审核节点状态
        $rev->status = 'pending';
        if($status != 'pass'){
            $rev->status = 'pass';
        }

        if($reviewInfo->comment == $this->lang->review->autoCloseComment){
            $rev->reviewer = empty($reviewInfo->closePerson) ? $reviewInfo->dealUser : $reviewInfo->closePerson;
        }else{
            $rev->reviewer = empty($reviewInfo->closePerson) ? $reviewInfo->dealUser : $reviewInfo->closePerson;
        }
        $rev->comment = '';
        if(($reviewInfo->closeDate) && ($reviewInfo->closeDate != '0000-00-00')){
            $rev->comment = $reviewInfo->comment;
        }
        $rev->extraInfo = ['reviewedDate' => $reviewInfo->closeTime];
        $closeReview->reviewers[] = $rev;

        if($nodeCodeStage == 'baseline'){
            $data['close']['total'] = 1;
            $data['close']['data'][] = $closeReview;
            $data['baselineTmp'] =  $data['baseline'];
            unset($data['baseline']);
            $data['baseline'] = $data['baselineTmp'];
            unset($data['baselineTmp']);
            $reviewBaselineType = $this->dao->select('baselineCondition')->from(TABLE_REVIEW)->Where('id')->eq($reviewID)->fetch('baselineCondition');
            $data['baseline']['baselineCondition'] = $reviewBaselineType;
        }
        if($nodeCodeStage == 'close'){
            $data['close']['total'] = 1;
            $data['close']['data'][] = $closeReview;
        }
        return $data;
    }

    /**
     * 获得所有审核节点审核人信息
     *
     * @param $reviewID
     * @return array
     */
    public function getAllReviewNodeFormatReviewerList($reviewID,$maxVersion){
        $data = array();
        $users  = $this->loadModel('user')->getPairs('noletter');
        $trial = $this->getTrial($reviewID, $maxVersion, $users,2);
        //部门id
        $deptid = array_filter(explode(' ',$trial['deptid']));
        //正式人员
        $deptzs = array_filter(explode(' ',$trial['deptzs']));
        //参与人员
        $deptjoin = array_filter(explode(' ',$trial['deptjoin']));

        $reviewNodeList =  $this->dao->select('id, stage, status, nodeCode')
            ->from(TABLE_REVIEWNODE)
            ->Where('objectID')->eq($reviewID)
            ->andWhere('objectType')->eq('review')
            ->andWhere('version')->eq($maxVersion)
            ->orderBy('stage')
            ->fetchAll();
        if(empty($reviewNodeList)){
            return $data;
        }
        $nodeIds = array_column($reviewNodeList,'id');

        $reviewerList = $this->dao->select('*')->from(TABLE_REVIEWER)->Where('node')->in($nodeIds)->groupBy('node, reviewer, status')->orderBy('id')->fetchAll();
        if(empty($reviewerList)){
            return $data;
        }
        //每个节点下的审核人
        $reviewers = [];
        foreach ($reviewerList as $val){
            $node = $val->node;
            $val->extraInfo = [];
            if($val->extra){
                $val->extraInfo = json_decode($val->extra, true);
            }
            $reviewers[$node][] = $val;
        }

        foreach ($reviewers as $key => $item) {
            $reviewers[$key]['total'] = count($item);
        }
        $nowbefore = $this->getCcList($reviewID,'waitFormalAssignReviewer',1, $maxVersion);
        foreach ($reviewNodeList as $val){
            $nodeId   = $val->id;
            $nodeCode = $val->nodeCode;
            $nodeCodeStage = zget($this->lang->review->nodeCodeStageList, $nodeCode);
            $val->reviewers = [];
            if(isset($reviewers[$nodeId])){
                $val->reviewers = $reviewers[$nodeId];
            }
            if($val->nodeCode == 'firstAssignDept'){
                $val->dept = $deptid;
                $val->nowbefore = $nowbefore;
            }
            if($val->nodeCode == 'firstAssignReviewer'){
                $dept = array_merge($deptzs,$deptjoin);
                if($dept){
                    $dept = array_flip(array_flip($dept));
                }
                $val->deptzs = isset($dept) ? $dept : array();
            }
            $data[$nodeCodeStage]['data'][] = $val;
            if(!isset($data[$nodeCodeStage]['total'])){
                $data[$nodeCodeStage]['total'] = 0;
            }
            //节点下总的用户数量
            $data[$nodeCodeStage]['total'] += ($val->reviewers)['total'];
        }
        //查询评审详情信息
        $reviewInfo = $this->getReviewMainInfoByID($reviewID);
        $status = $reviewInfo->status;
        $nodeCodeStage = zget($this->lang->review->nodeCodeStageList, $status);

        //判断是否关闭，关闭加入
        $closeReview = new stdClass();
        $closeReview->status = $status;
        $closeReview->nodeCode = 'close';
        $rev = new stdClass();
        //审核节点状态
        $rev->status = 'pending';
        if($status != 'pass'){
            $rev->status = 'pass';
        }
        $rev->reviewer = empty($reviewInfo->closePerson) ? $reviewInfo->dealUser : $reviewInfo->closePerson;
        $rev->comment = '';
        if($reviewInfo->closeDate){
            $rev->comment = $reviewInfo->comment;
        }
        $rev->extraInfo = ['reviewedDate' => $reviewInfo->closeDate];
        $closeReview->reviewers[] = $rev;

        if($nodeCodeStage == 'baseline'){
            $data['close']['total'] = 1;
            $data['close']['data'][] = $closeReview;
            $data['baselineTmp'] =  $data['baseline'];
            unset($data['baseline']);
            $data['baseline'] = $data['baselineTmp'];
            unset($data['baselineTmp']);
            $reviewBaselineType = $this->dao->select('baselineCondition')->from(TABLE_REVIEW)->Where('id')->eq($reviewID)->fetch('baselineCondition');
            $data['baseline']['baselineCondition'] = $reviewBaselineType;
        }
        if($nodeCodeStage == 'close'){
            $data['close']['total'] = 1;
            $data['close']['data'][] = $closeReview;
        }
        return $data;
    }

    /**
     * 获取版本截止日期
     * @param $objectType
     * @param $objectID
     * @param int $version
     * @return array
     */
    public function getNodeEndDate($objectType, $objectID, $version = 1)
    {
        $date = $this->dao->select('createdDate')->from(TABLE_CONSUMED)
            ->where('objectType')->eq($objectType)
            ->andWhere('objectID')->eq($objectID)
            ->andWhere('version')->eq($version)
            ->orderBy('createdDate_desc')->fetch();
        if(empty($date)){
            $date = $this->dao->select('createdDate')->from(TABLE_REVIEWNODE)
                ->where('objectType')->eq($objectType)
                ->andWhere('objectID')->eq($objectID)
                ->andWhere('version')->eq($version)
                ->orderBy('createdDate_desc')->fetch();
        }
        return $date;
    }
    /**
     * 获取版本截止日期
     * @param $objectType
     * @param $objectID
     * @param int $version
     * @return array
     */
    public function getNodeStartDate($objectType, $objectID, $version = 1)
    {
        $date = $this->dao->select('createdDate')->from(TABLE_CONSUMED)
            ->where('objectType')->eq($objectType)
            ->andWhere('objectID')->eq($objectID)
            ->andWhere('version')->eq($version)
            ->orderBy('createdDate_asc')->fetch();
        if(empty($date)){
            $date = $this->dao->select('createdDate')->from(TABLE_REVIEWNODE)
                ->where('objectType')->eq($objectType)
                ->andWhere('objectID')->eq($objectID)
                ->andWhere('version')->eq($version)
                ->orderBy('createdDate_asc')->fetch();
        }
        return $date;
    }

    /**
     * 查询工作量
     * @param $reviewID
     * @return mixed
     */
    public function getConsumedList($reviewID)
    {
        $cs = $this->dao->select('*')->from(TABLE_CONSUMED)->where('objectType')->eq('review')
            ->andWhere('objectID')->eq($reviewID)
            ->orderBy('id_asc')
            ->fetchAll();
        return $cs;
    }
    /**
     * 工作量编辑
     * @param $reviewID
     * @return mixed
     */
    public function workloadEdit($reviewID , $consumedID){

        //返回信息
        $res = array();
        //检查时间信息
        $consumedTime = $this->post->consumed;
        $checkRes = $this->loadModel('consumed')->checkConsumedInfo($consumedTime);
        if(!$checkRes){
            return dao::$errors;
        }
        //检查关配合人员工作量信息
        $checkRes = $this->loadModel('consumed')->checkPostDetails($consumedTime);
        if(!$checkRes){
            return dao::$errors;
        }

        //工作量节点信息
        $consumed = fixer::input('post')->remove('comment, relevantUser, workload, dealUser')->get();
        /* Judge whether the current work record is the last one. */
        $isLast = $this->loadModel('consumed')->checkIsLastConsumed($consumedID, $reviewID, 'review');
        if($isLast){
            //最后一个节点时没有设置处理人
            $dealUser = $this->post->dealUser;
            if(!$dealUser){
                $errors['dealUser'] = vsprintf($this->lang->demand->emptyObject, $this->lang->demand->dealUser);
                return dao::$errors = $errors;
            }
        }


        $consumed->details = $this->loadModel('consumed')->getPostDetails();
        //检查信息
        $this->dao->update(TABLE_CONSUMED)->data($consumed)->autoCheck()
            ->batchCheck($this->config->demand->workloadedit->requiredFields, 'notempty')
            ->where('id')->eq($consumedID)
            ->exec();

        $actions = $this->dao->select('*')->from(TABLE_ACTION)
            ->where('objectType')->eq('review')
            ->andWhere('objectID')->eq($reviewID)
            ->andWhere('action')->eq('deal')
            ->orderBy('id_asc')
            ->fetchAll();
        $consumeds = $this->getConsumedList($reviewID);

        //最后一个工作量节点修改需求单的待处理状态和待处理人
        if($isLast) {
            $oldReview = $this->getByID($reviewID);
            if(($oldReview->status != $consumed->after) || ($oldReview->dealUser != $dealUser)){
                $this->dao->update(TABLE_REVIEW)->set('status')->eq($consumed->after)->set('dealUser')->eq($dealUser)->where('id')->eq($reviewID)->exec();

                $data = new stdClass();
                $data->status   = $consumed->after;
                $data->dealUser = $dealUser;
                $res = common::createChanges($oldReview, $data);
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

        return $res;
    }

    /**
     * 工作量详情
     * @param $reviewID
     * @return mixed
     */
    public function workloadDetails($reviewID , $consumedID){
        $this->view->title    = $this->lang->review->workloadDetails;
        $this->view->users    = $this->loadModel('user')->getPairs('noletter');
        $this->view->details  = $this->loadModel('consumed')->getWorkloadDetails($consumedID);
        $this->display();
    }

    /**
     * 工作量删除
     * @param $reviewID
     * @return mixed
     */
    public function workloadDelete($reviewID , $consumedID ){
        $actions = $this->dao->select('*')->from(TABLE_ACTION)
            ->where('objectType')->eq('review')
            ->andWhere('objectID')->eq($reviewID)
            ->andWhere('action')->eq('deal')
            ->orderBy('id_asc')
            ->fetchAll();

        $consumeds = $this->getConsumedList($reviewID);

        /* Judge whether the current work record is the last one. */
        $total  = count($consumeds) - 1;
        $isLast = false;
        $previousID = 0;
        foreach($consumeds as $index => $cs)
        {
            if($cs->id == $consumedID)
            {
                $isLast = $index == $total ? true : false;
                $previousID = $consumeds[$total - 1]->id; //上一条
            }
        }

        if($isLast and $previousID)
        {
            $consumed = $this->getConsumedByID($previousID); //获得上一条的工作量信息
            $this->dao->update(TABLE_REVIEW)->set('status')->eq($consumed->after)->where('id')->eq($reviewID)->exec(); //只是修改了下一个处理状态
        }

        /* Get the corresponding relationship between work record and operation record. */
        $actionID = 0;
        array_splice($consumeds, 0, 1); // Remove the first work record.

        foreach($consumeds as $index => $cs)
        {
            if($cs->id == $consumedID) $actionID = $actions[$index]->id;
        }

        if($actionID) $this->dao->delete()->from(TABLE_ACTION)->where('id')->eq($actionID)->exec();

        $this->dao->delete()->from(TABLE_CONSUMED)->where('id')->eq($consumedID)->exec();
        return array();
    }

    /**
     * 获取指定工作量
     * @param $consumedID
     * @return mixed
     */
    public function getConsumedByID($consumedID)
    {
        return $this->dao->select('*')->from(TABLE_CONSUMED)->where('id')->eq($consumedID)->fetch();
    }

    /**
     * 关闭评审删除白名单
     * @param $subOjectID
     * @param $reason
     */
    public function deleteWhiteList($subOjectID,$reason){
        $data = $this->dao->select('*')->from(TABLE_ACL)->where('subObjectID')->eq($subOjectID)->andWhere('reason')->eq($reason)->fetchAll();
        if(!empty($data)){
            $temp = $data[0];
            $objectID   = $temp->objectID;
            $objectType = $temp->objectType;
            $ids = array_column($data, 'id');
            $accountArray = array_column($data, 'account');
            $this->dao->delete()->from(TABLE_ACL)->where('id')->in($ids)->exec();

            //查询删除以后还存在的用户信息
            $validUsers = [];
            $userList = $this->dao->select('account')->from(TABLE_ACL)
                ->where('objectType')->eq($objectType)
                ->andWhere('objectID')->eq($objectID)
                ->andWhere('account')->in($accountArray)
                ->fetchAll();
            if(!empty($userList)){
                $validUsers = array_column($userList, 'account');
            }
            //更新权限
            $diffUserAccounts = array_diff($accountArray, $validUsers);
            if(!empty($diffUserAccounts)){
                $this->loadModel('user')->updateUserView($objectID, $objectType, $diffUserAccounts);
            }
        }
    }

    /**
     *获得提交评审后的下一状态
     *
     * @param $reviewInfo
     * @return mixed|string
     */
    public function getSubmitNextStatus($reviewInfo){
        $status = $reviewInfo->status;
        if(in_array($status, $this->lang->review->passButEditStatusList)){
            $type = $reviewInfo->type;
            if(($status == $this->lang->review->statusList['firstPassButEdit']) && ($type == 'cbp')){
                $nextStatus = $this->lang->review->statusList['waitVerify']; //待验证
            }else{
                $nextStatus =  $this->lang->review->passButEditNextStatusList[$status]; //对应的状态
            }
        }else{
            $rejectStage = $reviewInfo->rejectStage;
            $isFirstReview = $reviewInfo->isFirstReview;
            //下一状态
            $nextStatus  = $this->getEditRejectNextStatus($rejectStage, $isFirstReview);
        }
        return $nextStatus;
    }

    /**
     * 获取整个评审参与人员
     * @param $objectType
     * @param $version
     * @param $objectID
     * @return mixed
     */
    public function getAllReview($objectType,$version, $objectID){

       return  $this->dao->select('distinct(t1.reviewer) as reviewer')->from(TABLE_REVIEWER)->alias('t1')
            ->leftJoin(TABLE_REVIEWNODE)->alias('t2')
            ->on('t1.node = t2.id')
            ->where('t2.objectType')->eq($objectType)
            ->andWhere('t2.version')->eq($version)
            ->andWhere('t2.objectID')->eq($objectID)
           ->fetchPairs();
    }


    /**
     * 获取初审部门 、初审参与人员、初审主审人员
     *
     * @param $reviewId
     * @param $version
     * @param $users
     * @param $flag 1-获取账户不获得历史账户 2-查历史版本并且获得用户姓名
     * @return string[]
     */
    public function getTrial($reviewId,  $version, $users, $flag, $deptParam = ''){
        $data = array(
            'deptjkr'  => '',
            'deptid'   => '',
            'deptzs'   => '',
            'deptjoin' => '',
        );

        $nodeCodes = ['firstAssignReviewer', 'firstReview', 'firstMainReview'];

        $nodeReviewers = $this->loadModel('review')->getReviewersListByNodeCodes('review', $reviewId, $version, $nodeCodes);
        if($flag == 2 && empty($nodeReviewers)){
            $maxVersion = $this->loadModel('review')->getReviewNodeMaxVersion($reviewId);
            $nodeReviewers = $this->loadModel('review')->getReviewersListByNodeCodes('review', $reviewId, $maxVersion, $nodeCodes);
        }

        //初审部门接口人
        $trialDeptLiasisonOfficer = isset($nodeReviewers['firstAssignReviewer']) ? $nodeReviewers['firstAssignReviewer']:[];
        $trialDeptLiasisonOfficer = array_flip(array_flip($trialDeptLiasisonOfficer));
        //初审参与人员
        $trialJoinOfficer = isset($nodeReviewers['firstReview']) ? $nodeReviewers['firstReview']:[];
        $trialJoinOfficer         = array_flip(array_flip($trialJoinOfficer));
        //初审主审人员
        $trialAdjudicatingOfficer = isset($nodeReviewers['firstMainReview']) ? $nodeReviewers['firstMainReview']:[];
        $trialAdjudicatingOfficer = array_flip(array_flip($trialAdjudicatingOfficer));

        //初审部门接口人

        foreach($trialDeptLiasisonOfficer as $officer) {
            $officer = trim($officer);
            if(empty($officer)) continue;
            if($flag == 1){
                $data['deptjkr'] .= $officer . "  ";
            }else{
                $data['deptjkr'] .= zget($users, $officer) . "  ";
            }
        }

        //获得初审人部门ids
        $trialDeptIds = [];
        if($trialDeptLiasisonOfficer){
            $trialDeptIds  = $this->loadModel('user')->getUserDeptIds($trialDeptLiasisonOfficer);
        }
        $deptMap = $this->loadModel('dept')->getOptionMenu();
        foreach($trialDeptIds as $dept) {
            $dept = trim($dept);
            if(empty($dept)) {
                continue;
            }
            if(!empty($deptParam)){
                $data['deptid'] .= $dept . "  ";
            }else{
                $data['deptid'] .= zget($deptMap, $dept) . "  ";
            }
        }

        //初审主审人员
        if(!empty($trialAdjudicatingOfficer)){
            foreach($trialAdjudicatingOfficer as $officer)
            {
                $officer = trim($officer);
                if(empty($officer)) continue;
                if($flag == 1){
                    $data['deptzs'] .= $officer . "  ";
                }else{
                    $data['deptzs'] .= zget($users,$officer) . "  ";
                }

            }
        }

        //初审参与人员
        if(!empty($trialJoinOfficer)){
            foreach($trialJoinOfficer as $officer) {
                if($officer) {
                    $officer = trim($officer);
                    if (empty($officer)) continue;
                    if($flag == 1){
                        $data['deptjoin'] .= $officer . "  ";
                    }else{
                        $data['deptjoin'] .= zget($users, $officer) . "  ";
                    }
                }
            }
        }

        return $data;
    }
    /**
     *  获取各部门负责人（除财务、人力、综合外）
     * @return mixed
     */
    public function getAllManager1(){
//        $result =  $this->dao->select('manager1')->from(TABLE_DEPT)->where('name')->notin("财务部,人力资源部,综合部,公司领导(立项会签不用勾选)")->andWhere('manager1')->ne('')->fetchAll();
//        $arr = '';
//        if(count($result) > 0){
//            $arr = array_column($result,'manager1');
//            $arr = implode(',',$arr);
//        }
        $arr = $this->config->review->manageReviewDefExperts;
        $deptIds = $this->loadModel('dept')->getRecurveSubDeptIds(30);
        if(in_array($this->app->user->dept, $deptIds)){ //当前登录账号是上海分公司或者上海份公司子部门用户
            $userArray = explode(',', $arr);
            if(!in_array('jiangcaikang' ,$userArray)){
                $userArray[]  = 'jiangcaikang';
                $arr = implode(',', $userArray);
            }
        }
        return $arr;
    }

    /**
     *  获取各部门负责人(多人)（除财务、人力、综合外）
     * @return mixed
     */
    public function getAllManager($deptId){
        $result =  $this->dao->select('manager')->from(TABLE_DEPT)->where('name')->notin("财务部,人力资源部,综合部,公司领导(立项会签不用勾选)")->andWhere('manager')->ne('')->andWhere('id')->in($deptId)->fetchAll();
        $arr = '';
        if(count($result) > 0){
            $arr = array_column($result,'manager');
            $arr = implode(',',$arr);
        }
        return $arr;
    }
    /**
     *  获取评审未处理问题
     * @param $reviewId
     * @return mixed
     */
    public function getNoDealIssue($reviewId){
        return  $this->dao->select('count(*) total,review')->from(TABLE_REVIEWISSUE)->where('review')->eq($reviewId)->andWhere('status')->in('active,failed,part,create')->fetchPairs('review','total');

    }

    /**
     * Desc:打基线节点单独获取抄送人
     * Date: 2022/6/27
     * Time: 17:28
     *
     * @param $objectID
     * @param string $before
     * @param string $flag
     * @param int $version
     * @return string
     *
     */
    public function getBaselineCcList($objectID, $before = '',$flag = '', $version = 0){
        $mailto = '';
        $ccList = $this->dao->select('max(id) id,mailto')->from(TABLE_CONSUMED)
            ->where('objectID')->eq($objectID)
            ->andWhere('objectType')->eq('review')
            ->andWhere('`before`')->eq('baseline')
            ->fetch();
        if($ccList){
            $mailto = $ccList->mailto;
        }
        return $mailto;
    }

    /**
     *获取邮件抄送人
     *
     * @param $objectID
     * @param string $before
     * @param string $flag
     * @param int $version
     * @return string
     */
    public function getCcList($objectID, $before = '',$flag = '', $version = 0){
        $ids = $this->dao->select('max(id) id')->from(TABLE_CONSUMED)
            ->where('objectID')->eq($objectID)
            ->andWhere('objectType')->eq('review')
            ->andWhere('`after`')->eq('waitApply')
            ->fetch();
        if(isset($ids->id)){
            $details = $this->dao->select('*')->from(TABLE_CONSUMED)
                ->where('objectID')->eq($objectID)
                ->andWhere('objectType')->eq('review')
                ->beginIF(is_array($before))->andWhere('`after`')->in($before)->fi()
                ->beginIF(!is_array($before))->andWhere('`after`')->eq($before)->fi()
                ->beginIF($version > 0)->andWhere('`version`')->eq($version)->fi()
                ->andWhere('id')->ge($ids->id)
                ->fetchAll();
        }
        $list =  '';
        if($flag){
            $ccList  = isset($details[0]->before) ? $details[0]->before : '';
        }else{
            if (isset($details)){
                foreach ($details as $detail) {
                    $list .= $detail->mailto.',';
                }
            }

            $ccList  = isset($list) ? trim($list,',') : '';
        }
        return $ccList;
    }


    public function getDetailStageReviews($objectType, $objectID, $version = 1, $stage = 1, $subObjectType = '')
    {
        $data = [];
        $node = $this->dao->select('*')->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq($objectType)
            ->andWhere('objectID')->eq($objectID)
            ->andWhere('version')->eq($version)
            ->andWhere('stage')->eq($stage)
            ->beginIF($subObjectType != '')->andWhere('subObjectType')->eq($subObjectType)->fi()
            ->orderBy('stage,id')
            ->fetch();
        if(!$node) return '';

        $reviewsList = $this->dao->select('reviewer, grade,createdBy')->from(TABLE_REVIEWER)
            ->where('node')->eq($node->id)
            ->fetchAll();
        if(!$reviewsList) {
            return $data;
        }
       /* foreach ($reviewsList as $val){
            $grade = $val->grade;
            $createBy = $val->createdBy;
            $reviewer = $val->reviewer;
            $data[$grade][$createBy][] = $reviewer;
        }*/
        foreach ($reviewsList as $val){
            $grade = $val->grade;
            $reviewer = $val->reviewer;
            $data[$grade][] = $reviewer;
        }
        return $data;
    }


    /**
     * 检查当前节点审核结果是否需要修改资料(1需要修改资料 2-不需要修改资料)
     *
     * @param $nodeId
     * @return int
     */
    public function getNodeIsEditInfo($nodeId){
        $isEditInfo = 2;
        if(!$nodeId){
            return $isEditInfo;
        }
        $ret = $this->dao->select('id')->from(TABLE_REVIEWER)
            ->where('node')->eq($nodeId)
            ->andWhere('status')->in('pass')
            ->andWhere('extra')->like('%"isEditInfo":1%')
            ->fetch();

        if(!empty($ret)){
            $isEditInfo = 1;
        }
        return $isEditInfo;
    }

    /**
     * 修改评审截止日期
     *
     * @param $reviewID
     * @return false|void
     */
    public function updateEndDate($reviewID){
        //历史数据
        $reviewInfo = $this->getByID($reviewID);
        //获得数据
        $postData = fixer::input('post')->get();
        //变更截止时间
        $maxVersion = $this->getReviewNodeMaxVersion($reviewID);
        //主表修改信息
        $updateParams = new stdClass();
        $nodeId = $this->dao->select('id')->from(TABLE_REVIEWNODE)->where('objectType')->eq('review')
            ->andWhere('objectID')->eq($reviewID)
            ->andWhere('version')->eq($maxVersion)
            ->orderBy('id_desc')->fetch('id');
         $this->dao->update(TABLE_REVIEW)->set('endDate')->eq($postData->endDate)->where('id')->eq($reviewID)->exec();
         $this->dao->update(TABLE_REVIEWNODE)->set('endDate')->eq($postData->endDate)->where('id')->eq($nodeId)->exec();

        $updateParams->endDate = $postData->endDate;
        $logChange = common::createChanges($reviewInfo, $updateParams);
        return $logChange;
    }

    /**
     * 评审自动处理公共方法
     * @param $dealUser
     * @param $currentDate
     * @param $isStartList
     * @param $reviewConsumed
     * @param $review
     * @return false
     */
    public function commonReviewDeal($dealUser, $currentDate, $isStartList, $reviewConsumed, $review){
        //判断评审类型是否需要开启异步超时处理
        $dealUsers = array_filter(explode(',', $review->dealUser));
        if (!empty($dealUsers) && in_array($dealUser, $dealUsers)) {
            $statusFirstAndFormalList = $this->lang->review->timeOutAutoDealStatusList;
            if (in_array($review->type, $isStartList) and (in_array($review->status, $statusFirstAndFormalList))) {
                $isOutsideUser = $this->loadModel('user')->getUserIsOutsideUser($dealUser);
                $timeOut = $this->lang->review->timeOut;
                $timeValue = '';
                if ($isOutsideUser) {
                    $timeValue = $timeOut['outside'];
                } else {
                    $timeValue = $timeOut['inside'];
                }
                $diffDays = $this->getDiffDate($review->endDate, $currentDate);
                //当当前时间-截至日期 > 超时阈值
                if ($diffDays > $timeValue && $diffDays != 0) {
                    $extra = new stdClass();
                    if ($review->status == 'waitFirstMainReview' || $review->status == 'firstMainReviewing') {
                        if ($this->getIsEdit($review->id) == 1) {
                            $comment = '逾期自动处理';
                            $historyComment = '逾期自动处理：通过（需修改）';
                            //是否需要修改资料
                            $extra->isEditInfo = 1;
                            $extra->grade = $review->grade;
                            $extParams = [];
                            $extParams['isEditInfo'] = 1;
                            $extParams['grade'] = $review->grade;
                            $consumed = $reviewConsumed;
                        } else {
                            $comment = '逾期未提交意见';
                            $historyComment = '逾期未提交意见：通过（无需修改）';
                            //是否需要修改资料
                            $extra->isEditInfo = 2;
                            $extra->grade = $review->grade;
                            $extParams = [];
                            $extParams['isEditInfo'] = 2;
                            $extParams['grade'] = $review->grade;
                            $consumed = 0;
                        }
                    } else {
                        //判断当前评审是否有该专家提出的问题
                        $count = $this->loadModel('reviewissue')->getIssueCountByUser($review->id, $dealUser);
                        if ($count > 0) {
                            $comment = '逾期自动处理';
                            $historyComment = '逾期自动处理：通过（需修改）';
                            //是否需要修改资料
                            $extra->isEditInfo = 1;
                            $extra->grade = $review->grade;
                            $extParams = [];
                            $extParams['isEditInfo'] = 1;
                            $extParams['grade'] = $review->grade;
                            $consumed = $reviewConsumed;
                        } else {
                            $comment = '逾期未提交意见';
                            $historyComment = '逾期未提交意见：通过（无需修改）';
                            //是否需要修改资料
                            $extra->isEditInfo = 2;
                            $extra->grade = $review->grade;
                            $extParams = [];
                            $extParams['isEditInfo'] = 2;
                            $extParams['grade'] = $review->grade;
                            $consumed = 0;
                        }
                    }
                    $res = array();
                    $res['result'] =1;
                    $newData = new stdClass();
                    $newData->result ='pass';
                    $newData->reviewedDate =$currentDate;
                    $newData->consumed =$consumed;
                    $newData->mailto ='';
                    $newData->comment =$comment;
                    $newData->isEditInfo =$extra->isEditInfo;
                    $res['data'] = $newData;
                    //检查并且获得参数
                    if(!$res['result']){
                        return false;
                    }
                    $data = $res['data'];
                    $version = $this->getReviewNodeMaxVersion($review->id);
                    $reviewResult = 'pass';
                    //处理审核操作
                    $result = $this->loadModel('review')->autoDealcheck('review', $review->id, $version, $reviewResult, $comment, $review->reviewStage, $extra, $dealUser);
                    $oldStatus = $review->status;
                    //下一个状态
                    $nextStatus = $this->getReviewNextStatus($review, $result, $extParams);

                    //修改信息
                    $currentTime = helper::now();
                    $updateParams = new stdClass();
                    $updateParams->autoDealTime = $currentTime;
                    $updateParams->status = $nextStatus;
                    $updateParams->lastReviewedBy = $dealUser;
                    $updateParams->lastReviewedDate = $currentDate;
                    if (in_array($nextStatus, $this->lang->review->sumitDateStatusList)) {
                        $updateParams->submitDate = $currentTime;
                    }
                    if ($nextStatus == 'waitFirstMainReview' or $nextStatus == 'firstMainReviewing') {
                        $endDate = $review->endDate;
                    } else {
                        if (isset($updateParams->submitDate)) {
                            $endDate = $this->getEndDate($nextStatus, $updateParams->submitDate, $review);
                        }
                    }

                    if (isset($endDate) && in_array($nextStatus, $this->lang->review->sumitDateStatusList)) {
                        $updateParams->endDate = $endDate;
                    }

                    $this->dao->update(TABLE_REVIEW)->data($updateParams)->where('id')->eq($review->id)->exec();
                    if (dao::isError()) {
                        return false;
                    }
                    //工时
                    if (isset($extra->appointUser)) {
                        $consumedExtra = $extra;
                    } else {
                        $consumedExtra = $extra->isEditInfo;
                    }

                    $this->loadModel('consumed')->record('review', $review->id, $consumed, $dealUser, $oldStatus, $nextStatus, '', $consumedExtra);

                    //审核时考虑是否忽略了确定线上评审结论(如果忽略增加忽略节点)
                    if(($review->isSkipMeetingResult == 1) && ($nextStatus == $this->lang->review->statusList['waitMeetingReview'])){
                        $maxStage = $this->loadModel('review')->getReviewMaxStage($review->id, 'review', $review->version);
                        $stage    = $maxStage + 1;
                        $nodeCode = $this->lang->review->nodeCodeList['formalOwnerReview'];
                        //新增审核节点
                        $reviewNodes = array(
                            array(
                                'reviewers' => array($review->owner),
                                'stage'     => $stage,
                                'status'    => 'ignore',
                                'nodeCode'  => $nodeCode,
                            )
                        );
                        $this->submitReview($review->id, 'review', $review->version, $reviewNodes);
                    }
                    //是否需要增加审核节点
                    $isAddNode = $this->getIsAddReviewNode($nextStatus);
                    if($isAddNode){
                        $newReviewInfo = $this->getByID($review->id);
                        if($newReviewInfo->version != $review->version){ //版本不一样
                            $res = $this->addNewVersionReviewNodes($newReviewInfo, $oldStatus);
                        }else{
                            $res = $this->addReviewNode($newReviewInfo, $oldStatus, '');
                        }
                    }
                    //提前增加验证审核节点
                    if(in_array($nextStatus, $this->lang->review->allowAdvanceSetReviewersStatusList)){
                        $maxStage = $this->loadModel('review')->getReviewMaxStage($review->id, 'review', $version);
                        $stage    = $maxStage + 1;
                        $nodeCode = $this->lang->review->nodeCodeList['verify'];
                        //新增审核节点
                        $reviewNodes = array(
                            array(
                                'reviewers' => $dealUser,
                                'stage'     => $stage,
                                'status'    => 'wait',
                                'nodeCode'  => $nodeCode,
                            )
                        );
                        $this->submitReview($review->id, 'review', $version, $reviewNodes);
                    }

                    if($result == 'pass' || $nextStatus == $this->lang->review->statusList['rejectVerify']) { //审核通过或者验证驳回
                        $ret = $this->loadModel('review')->setNextReviewNodePending('review',$review->id, $version);
                    }
                    //}
                    //处理人
                    $newReviewInfo = $this->getByID($review->id);
                    $nextDealUser = $this->getNextDealUser($newReviewInfo, $newReviewInfo->status);
                    if ($nextStatus == 'firstPassButEdit') {
                        $nextDealUser = $review->createdBy;
                    }
                    if ($nextDealUser != $newReviewInfo->dealUser) {
                        $tempUpdateParams = new stdClass();
                        $tempUpdateParams->dealUser = $nextDealUser;
                        $this->dao->update(TABLE_REVIEW)->data($tempUpdateParams)->where('id')->eq($review->id)->exec();
                        $updateParams->dealUser = $nextDealUser;
                    }
                    //增加项目白名单
                    if(!empty($nextDealUser)){
                        if(!is_array($nextDealUser)){
                            $nextDealUser = explode(',', $nextDealUser);
                        }
                        foreach ($nextDealUser as $userAccount){
                            $projectId = $review->project;
                            $res = $this->addProjectReviewWhitelist($projectId, $review->id, $userAccount);
                        }
                    }
                    //增加到会议评审
                    if($nextStatus == $this->lang->review->statusList['waitMeetingReview']){ //下一个状态是会议评审
                        $res = $this->opMeetingReview($review, $nextStatus, $data);
                        if(!$res['result']){
                            dao::$errors[] = $res['message'];
                            return false;
                        }
                        //有返回数据
                        if(isset($res['data']) && !empty($res['data'])){
                            $meetingData = $res['data'];
                            if(isset($meetingData->meetingCode)){
                                $meetingCode = $meetingData->meetingCode;
                            }
                        }
                    }else{ //下一个状态是非会议评审中
                        if(($review->meetingCode) && ($oldStatus == $this->lang->review->statusList['waitFormalOwnerReview'])){ //会议评审，上一个状态是评审主席确定线上评审结论，且下一个状态非会议评审（即会议评审后续不需要会议评审环节）
                            //解除绑定
                            $res = $this->loadModel('reviewmeeting')->cancelBindReviewMeeting($review->meetingCode, $review->id);
                            if(!$res['result']){
                                dao::$errors[] = $res['message'];
                                return false;
                            }
                        }
                    }

                    if(isset($meetingCode)){
                        $updateParams->meetingCode = $meetingCode;
                    }
                    $logChanges = common::createChanges($review, $updateParams, '');
                    $isSetHistory = true;
                    $actionID = $this->loadModel('action')->create('review', $review->id, 'reviewed', $historyComment, $extra, $dealUser, true, $isSetHistory, $logChanges);
                }
            }

        }
    }
    /**
     * 单条评审自动处理
     *
     * @param $reviewID
     * @return false|void
     */
    public function reviewDealSingle($reviewID){

        $review = $this->getByID($reviewID);
        $dealUserList =[];
        if(!empty($review->dealUser)){
            $dealUserList = explode(',', $review->dealUser);
        }

        $currentDate = date('Y-m-d');

        $isStart = $this->config->review->startTimeOut;
        $isStartList =Array_filter( array_unique(explode(',',$isStart)));
        //超时处理时间
        $reviewConsumed=$this->config->review->reviewConsumed->reviewConsumed;
        foreach($dealUserList as $dealUser) {
            //判断评审类型是否需要开启异步超时处理
            $this ->commonReviewDeal($dealUser,$currentDate,$isStartList,$reviewConsumed,$review);
        }
        return true;
    }


    /**
     *变更节点用信息
     *
     * @param $reviewID
     * @return array
     */
    public function updateReviewNodeUsers($reviewID){
        //历史数据
        $reviewInfo = $this->getByID($reviewID);
        //获得数据
        $postData = fixer::input('post')->get();
        $nodeId    = $postData->nodeId;
        //更变用户信息
        $reviewers = [];
        if(isset($postData->reviewers) && !empty($postData->reviewers)){
            if(!is_array($postData->reviewers)){
                $reviewers = explode(',', $postData->reviewers);
            }else{
                $reviewers = $postData->reviewers;
            }
        }
        if(!empty($reviewers)){
            $reviewers = array_filter($reviewers);
        }
        //检查是否允许变更
        $checkRes = $this->checkReviewNodeIsAllowEdit($reviewInfo, $nodeId);
        if(!$checkRes['result']){
            dao::$errors[] = $checkRes['message'];
            return false;
        }
        $data = $checkRes['data'];
        //当前节点
        $reviewNode = $data->reviewNode;
        //允许修改的用户
        $allowEditReviewers = $data->allowEditReviewers;

        //当前单据的状态
        $oldStatus = $reviewInfo->status;
        $version = $reviewInfo->version;
        //当前节点的审核状态
        $nodeStatus = $reviewNode->status;
        $nodeCode   = $reviewNode->nodeCode;
        //比较修改用户信息
        $delUsers = array_diff($allowEditReviewers, $reviewers);
        $addUsers = array_diff($reviewers, $allowEditReviewers);
        
        //如果没有新增和删除无需提交
        if(empty($delUsers) && empty($addUsers)){
            dao::$errors[] = $this->lang->review->checkEditNodeOpResultList['updateReviewersEmpty'];
            return false;
        }
        //获得已经审核的审核信息
        $reviewedReviewers = $this->loadModel('review')->getReviewedReviewersByNodeId($nodeId);
        if(empty($reviewedReviewers)){
            if(!(isset($reviewers) && !empty($reviewers))){
                dao::$errors[] = $this->lang->review->checkEditNodeOpResultList['chooseReviewersEmpty'];
                return false;
            }
        }

        //修改审核人表(删除审核人信息)
        if(!empty($delUsers)){
            $this->dao->delete()->from(TABLE_REVIEWER)->where('node')->eq($nodeId) ->andWhere('reviewer')->in($delUsers)->exec();
            if(dao::isError()) {
                return false;
            }
        }
        //修改审核人表(增加审核人信息)
        if(!empty($addUsers)){
            $reviewerExtParams = [];
            $nodeInfo = $this->loadModel('review')->getNodeInfoByNodeId($nodeId);
            $nodeCode = $nodeInfo->nodeCode;
            if(in_array($nodeCode, ['firstReview','firstMainReview'])){ //初审参与人员,初审主审人员
                foreach ($addUsers as $currentUser){
                    if($nodeCode == 'firstMainReview'){ //修改初审参与人员审核节点
                        $mainNodeId = $nodeId;
                    }else{ //修改初审主审人员审核节点
                        $mainNodeCode = 'firstMainReview';
                        $mainNodeInfo = $this->loadModel('review')->getNodeByNodeCode('review', $reviewID, $version, $mainNodeCode);
                        $mainNodeId  = $mainNodeInfo->id;
                    }
                    $parentId = $this->getReviewerParentId($mainNodeId, $currentUser);
                    $reviewerExtParams['parentId'] = $parentId;
                    $res = $this->loadModel('review')->addNodeReviewers($nodeId, array($currentUser), true, $nodeStatus, $reviewerExtParams);
                }
            }else{
                $res = $this->loadModel('review')->addNodeReviewers($nodeId, $addUsers, true, $nodeStatus, $reviewerExtParams);
            }
            if(dao::isError()) {
                return false;
            }
        }

        //数组转化为字符串
        $reviewersStr = implode(',', $reviewers);
        $allReviewersStr = '';
        //求并集
        $allReviewers = array_merge($reviewedReviewers, $reviewers);
        if(!empty($allReviewers)){
            $allReviewersStr = implode(',', $allReviewers);
        }

        //主表修改信息
        $updateParams = new stdClass();
        //如果在评审主表中存在该字段
        $field = '';
        if(isset($this->lang->review->nodeCodeFieldMapList[$nodeCode])){
            $field = $this->lang->review->nodeCodeFieldMapList[$nodeCode];
            if($field == 'expert'){ //当字段修改的是专家的时候
                //评审主席
                //$owner = [$reviewInfo->owner];
                //排除评审主席的其他人员（包括评审内部专家和评审外部专家）
                //$expertUsers = array_diff($allReviewers, $owner);
                //专家用户列表
                $expertUsersList = $this->loadModel('user')->getExpertUsersList($allReviewers);
                $insideExperts  = $expertUsersList['insideExperts'];
                $outsideExperts = $expertUsersList['outsideExperts'];
                $expertUsers = '';
                $reviewedByUsers = '';
                if(!empty($insideExperts)){
                    $expertUsers = implode(',', $insideExperts);
                }
                if(!empty($outsideExperts)){
                    $reviewedByUsers = implode(',', $outsideExperts);
                }
                $updateParams->expert     = $expertUsers;
                $updateParams->reviewedBy = $reviewedByUsers;
                $meetingPlanExport = $this->getReviewMeetingPlanExportUsers($expertUsers, $reviewedByUsers, $reviewInfo->outside);
                $updateParams->meetingPlanExport = $meetingPlanExport;
            }else{
                $updateParams->$field = $allReviewersStr;
            }
        }

        //修改后该节点人员不为空
        if(!empty($reviewers)){
            if($nodeStatus == 'pending'){
                $updateParams->dealUser = $reviewersStr;
            }
            //修改主表
            if(!empty((array)$updateParams)){
                $this->dao->update(TABLE_REVIEW)->data($updateParams)->where('id')->eq($reviewID)->exec();
                if(dao::isError()) {
                    return false;
                }
            }
        }else{ //修改后节点人员为空

            $reviewAction = 'pass';
            if($nodeCode == $this->lang->review->nodeCodeList['verify']){ //评审时需要根据所有的验证人结果
                $reviewAction = $this->loadModel('review')->getReviewNodeReviewAction($nodeId, $reviewAction);
            }
            //修改当前节点为pass或者reject
            $ret = $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq($reviewAction)->where('id')->eq($nodeId)->exec();

            //根据当前节点的其他审核信息修改主表的待处理人和处理状态以及触发邮件
            $nextStatus = $this->getReviewNextStatus($reviewInfo, $reviewAction);

            //修改信息
            $updateParams->status = $nextStatus;
            $updateParams->lastReviewedBy   = $this->app->user->account;
            $updateParams->lastReviewedDate = helper::today();
            if($reviewAction == 'reject'){
                //审核步骤reviewStage获取
                $rejectStage = $this->getRejectStage($oldStatus);
                $updateParams->rejectStage = $rejectStage;
            }
            if(in_array($nextStatus, $this->lang->review->sumitDateStatusList)){
                $updateParams->submitDate = helper::now();
            }

            if($nextStatus =='waitFirstMainReview' or $nextStatus=='firstMainReviewing' OR (in_array($oldStatus, $this->lang->review->allowFirstJoinReviewStatusList) && $reviewAction == 'pass')){
                $endDate = $reviewInfo->endDate;
            }else{
                if(isset($updateParams->submitDate)){
                    $endDate = $this->getEndDate($nextStatus,$updateParams->submitDate, $reviewInfo);
                }
            }
            if(isset($endDate) && in_array($nextStatus, $this->lang->review->sumitDateStatusList)){
                $updateParams->endDate = $endDate;
            }

            $this->dao->update(TABLE_REVIEW)->data($updateParams)->where('id')->eq($reviewID)->exec();
            if(dao::isError()) {
                return false;
            }

            //跳过评审主席审核结论
            if(($reviewInfo->isSkipMeetingResult == 1) && ($nextStatus == $this->lang->review->statusList['waitMeetingReview'])){
                $maxStage = $this->loadModel('review')->getReviewMaxStage($reviewInfo->id, 'review', $reviewInfo->version);
                $stage    = $maxStage + 1;
                $nodeCode = $this->lang->review->nodeCodeList['formalOwnerReview'];
                //新增审核节点
                $reviewNodes = array(
                    array(
                        'reviewers' => array($reviewInfo->owner),
                        'stage'     => $stage,
                        'status'    => 'ignore',
                        'nodeCode'  => $nodeCode,
                    )
                );
                $this->submitReview($reviewInfo->id, 'review', $reviewInfo->version, $reviewNodes);
            }


            //提前增加验证审核节点
            if(in_array($nextStatus, $this->lang->review->allowAdvanceSetReviewersStatusList)){
                if($reviewInfo->isFirstReview == '2'){ //当跳过初审时
                    $nodeCode = 'formalReview';
                }else{//获得评审验证人员默认为初审人员
                    $nodeCode = 'firstReview';
                }
                $verifyReviewers = $this->loadModel('review')->getReviewersByNodeCode('review', $reviewID, $reviewInfo->version, $nodeCode);
                if($verifyReviewers){
                    $maxStage = $this->loadModel('review')->getReviewMaxStage($reviewInfo->id, 'review', $version);
                    $stage    = $maxStage + 1;
                    $nodeCode = $this->lang->review->nodeCodeList['verify'];
                    $postUser = explode(',', $verifyReviewers);

                    //新增审核节点
                    $reviewNodes = array(
                        array(
                            'reviewers' => $postUser,
                            'stage'     => $stage,
                            'status'    => 'wait',
                            'nodeCode'  => $nodeCode,
                        )
                    );
                    $this->submitReview($reviewInfo->id, 'review', $version, $reviewNodes);
                }
            }
            //是否需要增加审核节点
            $isAddNode = $this->getIsAddReviewNode($nextStatus);
            if($isAddNode){
                $newReviewInfo = $this->getByID($reviewID);
                $res = $this->addReviewNode($newReviewInfo, $oldStatus);
            }

            //修改成待处理
            if($reviewAction == 'pass' || $nextStatus == $this->lang->review->statusList['rejectVerify']) { //审核通过或者验证驳回
                $ret = $this->loadModel('review')->setNextReviewNodePending('review', $reviewID, $version);
            }

            //处理人
            $newReviewInfo = $this->getByID($reviewID);
            $nextDealUser = $this->getNextDealUser($newReviewInfo, $newReviewInfo->status);
            if($nextDealUser != $newReviewInfo->dealUser){
                $tempUpdateParams = new stdClass();
                $tempUpdateParams->dealUser = $nextDealUser;
                $this->dao->update(TABLE_REVIEW)->data($tempUpdateParams)->where('id')->eq($reviewID)->exec();
                $updateParams->dealUser = $nextDealUser;
            }
            if(($reviewInfo->isSkipMeetingResult == 1) && ($nextStatus == $this->lang->review->statusList['waitMeetingReview'])){
                //修改会议评审中到会议评审
                if($reviewInfo->meetingCode){
                    $res = $this->loadModel('reviewmeeting')->updateReviewMeetingDetailStatus($reviewID, $nextStatus); //修改会议状态
                    if(!$res['result']){
                        return  false;
                    }
                }
            }
        }

        //判断是否白名单，添加白名单
        if(!empty($addUsers)){
            $projectId = $reviewInfo->project;
            foreach ($addUsers as $userAccount){
                $res = $this->addProjectReviewWhitelist($projectId, $reviewID, $userAccount);
            }
        }

        //是否需要需求会议评审的预计参会专家
        if(($field == 'expert') && ($reviewInfo->meetingCode)){
            $ret = $this->loadModel('reviewmeeting')->updateMeetingPlanExports($reviewInfo->meetingCode);
        }
        $logChange = common::createChanges($reviewInfo, $updateParams);
        return $logChange;
    }

    // 修改评审类型和评审会主席
    public function updateReviewInfos($reviewID){
        $oldReview = $this->getByID($reviewID);
        $today  = helper::now();

        $review = fixer::input('post')
            ->add('editBy', $this->app->user->account)
            ->add('editDate', $today)
            ->remove('uid,files,consumed')
            ->add('lastEditedBy', $this->app->user->account)
            ->add('lastEditedDate', helper::today())
            ->get();
        if(empty($review->owner)){
            return dao::$errors['editReviewOwner'] = $this->lang->review->editReviewOwnerTip ;
        }
        $this->dao->update(TABLE_REVIEW)->data($review)->where('id')->eq($reviewID)->exec();

        // 判断是否由专业评审转为CBP评审
        if($oldReview->type == 'pro' && $review->type != 'pro'){
            $this->updateProNodeInfos($reviewID, $oldReview->qa);
        }

        if(!dao::isError()) return common::createChanges($oldReview, $review);

        return false;
    }

    // 修改专业评审节点信息
    public function updateProNodeInfos($reviewID, $dealUser){
        // 查看当前节点是不是专业评审待评审主席处理
        $node = $this->dao->select('id,nodeCode')->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq('review')
            ->andWhere('objectID')->eq($reviewID)
            ->andWhere('status')->eq('pending')->fetch();
        // 改正式评审为外部评审并调整待处理人(评审主席改为qa)
        if($node->nodeCode == 'formalAssignReviewer'){
            $this->dao->update(TABLE_REVIEWNODE)->set('nodeCode')->eq('outReview')->where('id')->eq($node->id)->exec();
            $this->dao->update(TABLE_REVIEWER)->set('reviewer')->eq($dealUser)->where('node')->eq($node->id)->exec();
            $this->dao->update(TABLE_REVIEW)->set('status')->eq('waitOutReview')->set('dealUser')->eq($dealUser)->where('id')->eq($reviewID)->exec();
        }
    }

    // 修改评审节点处理信息
    public function updateReviewNodeInfos($reviewID, $nodeID){
        $this->loadModel('review');
        $this->loadModel('reviewmanage');
        // 查询当前修改节点
        $nodeCode = $this->dao->select('nodeCode')->from(TABLE_REVIEWNODE)->where('id')->eq($nodeID)->fetch();
        // 查询该评审原数据
        $oldReview = $this->dao->select('baseLineCondition,closePerson,type,grade')->from(TABLE_REVIEW)->where('id')->eq($reviewID)->fetch();
        $users = $this->loadModel('user')->getPairs('noletter');
        $oldReview->adviceGrade = $this->review->getReviewAdviceGrade($oldReview->type, $oldReview->grade);
        $adviceGradeList = $this->review->getReviewAdviceGradeList($oldReview->adviceGrade, true);
        $review = fixer::input('post')->get();
        $count = count($review->reviewers);
        $editInfo = '';
        // 拼接历史记录备注
        $comment = "“".$this->lang->review->nodeCodeNameList[$nodeCode->nodeCode]."”节点由“".$users[$this->app->user->account].'”';
        // 不同节点所需修改数据不同
        if(in_array($nodeCode->nodeCode, $this->lang->review->updateNodeInfoStatusList)){
            for($i = 0; $i < $count; $i++) {
                $oldReviewer = $this->dao->select('reviewer,comment,status,extra')->from(TABLE_REVIEWER)->where('id')->eq($review->reviewerID[$i])->fetch();
                $oldExtraInfo = json_decode($oldReviewer->extra, true);
                $extraInfo =  json_decode($oldReviewer->extra, true);
                $updateParams = new stdClass();
                $updateParams->reviewer = $review->reviewers[$i];
                $updateParams->comment = $review->comment[$i];
                $gradeEdit = false;
                if(isset($review->grade[$i]) && (empty($review->grade[$i]))){
                    return dao::$errors[''] = $this->lang->review->checkResultList['adviceGradeEmpty'];
                }
                if(isset($review->grade[$i]) && ($review->grade[$i] != $extraInfo['grade'])){
                    $extraInfo['grade'] = $review->grade[$i];
                    $updateParams->extra = json_encode($extraInfo);
                    $gradeEdit = true;
                }
                //修改
                $this->dao->update(TABLE_REVIEWER)->data($updateParams)->where('id')->eq($review->reviewerID[$i])->exec();
                // 判断是否记录备注
                if($oldReviewer->reviewer != $review->reviewers[$i]){
                    $comment .= "”".$this->lang->review->updateDealuser."“".$users[$oldReviewer->reviewer]."”".$this->lang->review->updateInfo."“".$users[$review->reviewers[$i]]."”，";
                }
                if($oldReviewer->comment != $review->comment[$i]){
                    $comment .= "”".$this->lang->review->updateComment."“".$oldReviewer->comment."”".$this->lang->review->updateInfo."“".$review->comment[$i]."”，";
                }
                if($gradeEdit){
                    $comment .= "”".$this->lang->review->adviceGrade."“".zget($adviceGradeList, $oldExtraInfo['grade'])."”".$this->lang->review->updateInfo."“".zget($adviceGradeList, $review->grade[$i])."”，";
                }
                // 节点为打基线时还须修改评审表数据
                if ($nodeCode->nodeCode == 'baseline') {
                    $this->dao->update(TABLE_REVIEW)->set('baseLineCondition')->eq($review->results[$i])->where('id')->eq($reviewID)->exec();
                    if($oldReviewer->status != $review->results[$i]){
                        $comment .= "”".$this->lang->review->updateResult."“".$this->lang->reviewmanage->condition[$oldReview->baseLineCondition]."”".$this->lang->review->updateInfo."“".$this->lang->reviewmanage->condition[$review->results[$i]]."”，";
                    }
                }
            }
        }elseif($nodeCode->nodeCode == 'archive' || (in_array($nodeCode->nodeCode, $this->lang->review->passButEditnodeCodeList) || $nodeCode->nodeCode == 'rejectVerifyButEdit')){
            for($i = 0; $i < $count; $i++) {
                $oldReviewer = $this->dao->select('reviewer,comment,status')->from(TABLE_REVIEWER)->where('id')->eq($review->reviewerID[$i])->fetch();
                // 当节点为归档时,通过要显示为已归档
                if($nodeCode->nodeCode == 'archive' && $review->results[$i] == 'pass'){
                    $this->lang->reviewmanage->confirmResultList[$review->results[$i]] = $this->lang->review->archived;
                }elseif($nodeCode->nodeCode == 'archive' && $oldReviewer->status == 'pass'){
                    $this->lang->reviewmanage->confirmResultList[$oldReviewer->status] = $this->lang->review->archived;
                }
                $this->dao->update(TABLE_REVIEWER)->set('reviewer')->eq($review->reviewers[$i])->set('status')->eq($review->results[$i])->set('comment')->eq($review->comment[$i])->where('id')->eq($review->reviewerID[$i])->exec();
                // 判断是否记录历史记录备注
                if($oldReviewer->reviewer != $review->reviewers[$i]){
                    $comment .= "”".$this->lang->review->updateDealuser."“".$users[$oldReviewer->reviewer]."”".$this->lang->review->updateInfo."“".$users[$review->reviewers[$i]]."”，";
                }
                if($oldReviewer->status != $review->results[$i]){
                    $comment .= "”".$this->lang->review->updateResult."“".$this->lang->reviewmanage->confirmResultList[$oldReviewer->status]."”".$this->lang->review->updateInfo."“".$this->lang->reviewmanage->confirmResultList[$review->results[$i]]."”，";
                }
                if($oldReviewer->comment != $review->comment[$i]){
                    $comment .= "”".$this->lang->review->updateComment."“".$oldReviewer->comment."”".$this->lang->review->updateInfo."“".$review->comment[$i]."”，";
                }
            }
        }elseif(empty($nodeCode)){
            // 当无节点数据是说明是关闭节点
            if($oldReview->closePerson !=  $review->reviewers[0]){
                $nodeCode->nodeCode = 'close';
                $this->dao->update(TABLE_REVIEW)->set('closePerson')->eq($review->reviewers[0])->where('id')->eq($reviewID)->exec();
                $comment = "“".$this->lang->review->nodeCodeNameList[$nodeCode->nodeCode]."”节点由“".$this->app->user->account;
                $comment .= "”".$this->lang->review->updateDealuser."“".$users[$oldReview->closePerson]."”".$this->lang->review->updateInfo."“".$users[$review->reviewers[0]]."”,";
            }
        }elseif(in_array($nodeCode->nodeCode, $this->lang->review->assignExpertNodeCodeList)){
            // 指派正式评审人员节点
            for($i = 0; $i < $count; $i++) {
                $oldReviewer = $this->dao->select('reviewer,comment,status')->from(TABLE_REVIEWER)->where('id')->eq($review->reviewerID[$i])->fetch();
                // 获取扩展信息字段
                $extra = $this->dao->select('extra')->from(TABLE_REVIEWER)->where('id')->eq($review->reviewerID[$i])->fetch();
                $extraInfo = json_decode($extra->extra,true);
                if (!isset($extraInfo['expert']) && !isset($extraInfo['outside'])) {
                    // 无扩展信息 直接改数据
                    $this->dao->update(TABLE_REVIEWER)->set('reviewer')->eq($review->reviewers[$i])->set('comment')->eq($review->comment[$i])->set('status')->eq($review->results[$i])->where('id')->eq($review->reviewerID[$i])->exec();
                    if($oldReviewer->reviewer != $review->reviewers[$i]){
                        $comment .= "”".$this->lang->review->updateDealuser."“".$users[$oldReviewer->reviewer]."”".$this->lang->review->updateInfo."“".$users[$review->reviewers[$i]]."”，";
                    }
                    if($oldReviewer->status != $review->results[$i]){
                        $comment .= "”".$this->lang->review->updateResult."“".$this->lang->reviewmanage->confirmResultList[$oldReviewer->status]."”".$this->lang->review->updateInfo."“".$this->lang->reviewmanage->confirmResultList[$review->results[$i]]."”，";
                    }
                    if($oldReviewer->comment != $review->comment[$i]){
                        $comment .= "”".$this->lang->review->updateComment."“".$oldReviewer->comment."”".$this->lang->review->updateInfo."“".$review->comment[$i]."”，";
                    }
                } else {
                    // 由扩展信息, 同事修改扩展信息数据
                    $expert = explode(',',$extraInfo['expert']);
                    $extraInfo['expert'] = implode(',', $review->results);
                    $extraRes = json_encode($extraInfo);
                    $this->dao->update(TABLE_REVIEWER)->set('reviewer')->eq($review->reviewers[$i])->set('comment')->eq($review->comment[$i])->set('extra')->eq($extraRes)->where('id')->eq($review->reviewerID[$i])->exec();
                    if($oldReviewer->reviewer != $review->reviewers[$i]){
                        $comment .= "”".$this->lang->review->updateDealuser."“".$users[$oldReviewer->reviewer]."”".$this->lang->review->updateInfo."“".$users[$review->reviewers[$i]]."”，";
                    }
                    sort($review->results);
                    sort($expert);
                    // 判断多选专家是否改变
                    if($review->results != $expert){
                        $comment .= "”".$this->lang->review->updateResult."“".implode(',',$expert)."”".$this->lang->review->updateInfo."“".$extraInfo['expert']."”，";
                    }
                    if($oldReviewer->comment != $review->comment[$i]){
                        $comment .= "”".$this->lang->review->updateComment."“".$oldReviewer->comment."”".$this->lang->review->updateInfo."“".$review->comment[$i]."”，";
                    }
                }
            }
        }else{
            $this->lang->reviewmanage->confirmResultList['passEdit'] = $this->lang->reviewmanage->confirmResultList['pass'];
            for($i = 0; $i < $count; $i++) {
                $updateUser = '';$updateStatus = '';

                // 查询原评审处理人表数据
                $oldReviewer = $this->dao->select('reviewer,comment,status,extra')->from(TABLE_REVIEWER)->where('id')->eq($review->reviewerID[$i])->fetch();
                $extraInfo = json_decode($oldReviewer->extra,true);
                $oldExtraInfo = json_decode($oldReviewer->extra, true);
                $oldEditInfo = '';
                $updateParams = new stdClass();
                $updateParams->reviewer = $review->reviewers[$i];
                $updateParams->comment = $review->comment[$i];
                $gradeEdit = false;
                if(isset($review->grade[$i]) && ($review->grade[$i] != $extraInfo['grade'])){
                    $extraInfo['grade'] = $review->grade[$i];
                    $gradeEdit = true;
                }
                if($oldReviewer->status == 'pass'){
                    // 原状态为通过时,判断原扩展为需修改还是无需修改,需要在历史备注显示
                    $oldEditInfo = $extraInfo['isEditInfo'] == '1' ? $this->lang->review->isEditInfoList[1] : $this->lang->review->isEditInfoList[2];
                }
                if($review->results[$i] == 'pass' || $review->results[$i] == 'passEdit'){
                    // 现状态为通过时,根据现状态判断扩展为需修改还是无需修改(根据pass或passEdit判断)
                    $extraInfo['isEditInfo'] = $review->results[$i] == 'pass' ? 2 : 1;
                    if (!isset($extraInfo['isEditInfo'])) {
                        // 如果之前无扩展说明是非通过状态改为通过状态,则需要写入扩展,并记录时间
                        $extraInfo['reviewedDate'] = helper::now();
                    }else{
                        $editInfo = $extraInfo['isEditInfo'] == '1'? $this->lang->review->isEditInfoList[1] : $this->lang->review->isEditInfoList[2];
                    }
                    $extraRes = json_encode($extraInfo);
                    $updateParams->status = 'pass';
                    $updateParams->extra = $extraRes;
                    $this->dao->update(TABLE_REVIEWER)->data($updateParams)->where('id')->eq($review->reviewerID[$i])->exec();
                }else {
                    if (isset($extraInfo['isEditInfo'])){
                        // 现状态不为通过并旧数据有需修改或无需修改时时,删掉旧数据的需修改或无需修改
                        unset($extraInfo['isEditInfo']);
                        $extraRes = json_encode($extraInfo);
                        $updateParams->status = $review->results[$i];
                        $updateParams->extra = $extraRes;
                        $this->dao->update(TABLE_REVIEWER)->data($updateParams)->where('id')->eq($review->reviewerID[$i])->exec();
                    }else{
                        // 现状态不为通过并且原状态也不是通过时直接改
                        $extraRes = json_encode($extraInfo);
                        $updateParams->status = $review->results[$i];
                        $updateParams->extra = $extraRes;
                        $this->dao->update(TABLE_REVIEWER)->data($updateParams)->where('id')->eq($review->reviewerID[$i])->exec();
                    }
                }

                // 记录历史记录备注
                if($oldReviewer->reviewer != $review->reviewers[$i]){
                    $comment .= "”".$this->lang->review->updateDealuser."“".$users[$oldReviewer->reviewer]."”".$this->lang->review->updateInfo."“".$users[$review->reviewers[$i]]."”，";
                }else{
                    $updateUser = 'unEdit';// 处理人未修改
                }
                // 两个状态不相同并且有一个状态不为通过时说明状态改变了（过滤pass！=passedit情况）或者两个状态都为通过但是是否需修改有改变时说明状态改变了
                if(($oldReviewer->status != $review->results[$i] && (!in_array($oldReviewer->status,$this->lang->review->passStatusList)|| !in_array($review->results[$i],$this->lang->review->passStatusList))) || (in_array($oldReviewer->status,$this->lang->review->passStatusList) && in_array($review->results[$i],$this->lang->review->passStatusList) && $oldEditInfo != $editInfo)){
                    if($updateUser == 'unEdit'){
                        $comment .= $this->lang->review->updateDealuser.'“'.$users[$review->reviewers[$i]].'”的';
                    }
                    $updateStatus = 'unEdit';
                    $comment .= "”".$this->lang->review->updateResult."“".$this->lang->reviewmanage->confirmResultList[$oldReviewer->status].$oldEditInfo."”".$this->lang->review->updateInfo."“".$this->lang->reviewmanage->confirmResultList[$review->results[$i]].$editInfo."”，";
                }
                if($oldReviewer->comment != $review->comment[$i]){
                    if($updateUser == 'unEdit' && empty($updateStatus)){
                        $comment .= $this->lang->review->updateDealuser.'“'.$users[$review->reviewers[$i]].'“的';
                    }
                    $comment .= "”".$this->lang->review->updateComment."“".$oldReviewer->comment."”".$this->lang->review->updateInfo."“".$review->comment[$i]."”，";
                }
                if($gradeEdit){
                    $comment .= "”".$this->lang->review->adviceGrade."“".zget($adviceGradeList, $oldExtraInfo['grade'])."”".$this->lang->review->updateInfo."“".zget($adviceGradeList, $review->grade[$i])."”，";
                }
            }
        }
        // 处理结束返回历史记录备注
        $comment = strpos($comment,$this->lang->review->commentEdit) ? trim($comment,'，').'。' : '';
        if(!dao::isError()) return $comment;
    }

    /**
     * update files
     *
     * @param  int    $reviewID
     * @access public
     * @return void
     */
    public function editFilesByID($reviewID)
    {
        $oldReview = $this->getByID($reviewID);
        $today  = helper::now();
        if(is_array($this->post->files) && count($this->post->files)){
            return dao::$errors = array('files' => $this->lang->review->filesEmpty);
        }


        $review = fixer::input('post')
            ->add('editBy', $this->app->user->account)
            ->add('editDate', $today)
            ->add('dealUser', $this->app->user->account)
            ->join('owner', ',')
            ->join('expert', ',')
            ->join('reviewedBy', ',')
            ->join('reviewer', ',')
            ->join('object', ',')
            ->remove('uid,files,consumed')
            ->add('lastEditedBy', $this->app->user->account)
            ->add('lastEditedDate', helper::today())
            ->get();
       /* $checkRes = $this->loadModel('consumed')->checkConsumedInfo($this->post->consumed);
        if(!$checkRes){
            return false;
        }*/

        //判断本次备注是否为空
        if(empty($_POST['currentComment']))
        {
            return dao::$errors['currentComment'] = $this->lang->review->currentCommentEmpty ;
        }

        $this->loadModel('consumed')->record('review', $reviewID, '0', $this->app->user->account, $oldReview->status, 'updateFiles', array(), '', $oldReview->version);

        $this->loadModel('file')->updateObjectID($this->post->uid, $reviewID, 'review');
        $this->file->saveUpload('review', $reviewID);

        // 加入白名单

        if(!dao::isError()) return common::createChanges($oldReview, $review);

        return false;
    }
    /**
     * 项目移动空间
     *
     * @param  int    $reviewID
     * @access public
     * @return void
     */
    public function projectSwap($reviewID)
    {
        $oldReview = $this->getByID($reviewID);
        $today     = helper::now();
        $data      = fixer::input('post')->get();

        $review = fixer::input('post')
            ->add('editBy', $this->app->user->account)
            ->add('editDate', $today)
            ->add('lastEditedBy', $this->app->user->account)
            ->add('lastEditedDate', helper::today())
            ->get();

        //判断本次项目是否为空
        if(empty($_POST['project']))
        {
            return dao::$errors['project'] = $this->lang->review->projectSwapTip ;
        }
        //判断本次备注是否为空
        if(empty($_POST['currentComment']))
        {
            return dao::$errors['currentComment'] = $this->lang->review->currentCommentEmpty ;
        }
        //项目权限表
        $allUsers = $this->dao->select('account')->from(TABLE_ACL)->where('subObjectID')->eq($reviewID)->fetchPairs();
        if(!empty($oldReview->allOwner)){
            $newUsers = $oldReview->allOwner;
        }else{
            $newUsers =$oldReview->qa.",".$oldReview->owner.",".$oldReview->reviewer.","
                .$oldReview->relatedUsers.",".$oldReview->reviewedBy.",".$oldReview->createdBy.",".$oldReview->meetingPlanExport.",".$oldReview->qualityCm;
        }
        $newUsersArry = explode(',',$newUsers);
        foreach ($newUsersArry as $item){
           if(!empty($item)){
               $allUsers[$item] = $item;
           }
        }

        //更改主表信息
        $this->dao->update(TABLE_REVIEW)->set('project')->eq($data->project)->where('id')->eq($reviewID)->exec();
        // 白名单迁移
        $this->dao->update(TABLE_ACL)->set('objectID')->eq($data->project)->where('subObjectID')->eq($reviewID)->exec();
        if($oldReview->status != 'close'){
        foreach ($allUsers as $allUser){
            $projects = $this->dao->select('projects')->from(TABLE_USERVIEW)->where('account')->eq($allUser)->fetch();
            if(!empty($projects) and strpos($projects->projects,$data->project)==false){
                $newProjects = $projects->projects.",".$data->project;
                if(!empty($newProjects)){
                    $this->dao->update(TABLE_USERVIEW)->set('projects')->eq($newProjects)->where('account')->eq($allUser)->exec();
                }
            }
        }
        }
        if(!dao::isError()) return common::createChanges($oldReview, $review);

        return false;
    }

    /**
     *获得默认的评审方式
     *
     * @param $reviewObjects
     * @param $planType
     * @param $planIsImportant
     * @return string
     */
    public function getDefGrade($reviewObjects, $planType, $planIsImportant){
        $grade = 'online';
        if(in_array('zzbg', $reviewObjects)){ //项目总结报告
            $grade = 'meeting';
            return $grade;
        }
        $planTypeArray = array(1, 2);
        if(in_array($planType, $planTypeArray)){
            $grade = 'meeting';
            return $grade;
        }
        if($planIsImportant == 1){
            $grade = 'meeting';
            return $grade;
        }
        return $grade;
    }

    /**
     *检查新家或者绑定会议评审信息
     *
     * @param $reviewInfo
     * @param $reviewInType
     * @param $meetingPlanType
     * @param string $meetingCode
     * @param string $meetingPlanTime
     * @param $owner
     * @return array|void
     */
    public function checkReviewMeetingInfo($reviewInfo, $reviewInType, $meetingPlanType, $meetingCode = '', $meetingPlanTime = '', $owner = '' ){
        $res = array(
            'result'  => false,
            'message' => '',
        );

        if(!$meetingPlanType){
            dao::$errors['meetingPlanType'] = $this->lang->review->checkResultList['meetingPlanTypeEmpty'];
            $res['message'] =  $this->lang->review->checkResultList['meetingPlanTypeEmpty'];
            return $res;
        }

        if($meetingPlanType == 1){
            //选择已有会议
            if(!isset($meetingCode) || !$meetingCode){
                dao::$errors['meetingCode'] = $this->lang->review->checkResultList['meetingCodeEmpty'];
                $res['message'] =  $this->lang->review->checkResultList['meetingCodeEmpty'];
                return $res;
            }
            $meetingInfo = $this->loadModel('reviewmeeting')->getMeetingByMeetingCode($meetingCode);
            $res = $this->loadModel('reviewmeeting')->checkMeetingIsAllowBind($meetingInfo);
            if(!$res['result']){
                dao::$errors['meetingCode'] = $res['message'];
                return $res;
            }
        }elseif ($meetingPlanType == 2){
            //新建会议
            if(!isset($meetingPlanTime) || !$meetingPlanTime){
                dao::$errors['meetingPlanTime'] = $this->lang->review->checkResultList['meetingPlanTimeEmpty'];
                $res['message'] =  $this->lang->review->checkResultList['meetingPlanTimeEmpty'];
                return $res;
            }
            if(!isset($owner) || !$owner){ //评审主席检查
                dao::$errors['owner'] = $this->lang->review->checkResultList['ownerError'];
                $res['message'] =  $this->lang->review->checkResultList['ownerError'];
                return $res;
            }
            if($reviewInfo->meetingCode && $reviewInfo->meetingPlanTime  && $reviewInfo->meetingPlanTime == $meetingPlanTime){
                dao::$errors['meetingPlanTime'] = $this->lang->review->checkReviewOpResultList['meetingSameError'];
                $res['message'] =  $this->lang->review->checkReviewOpResultList['meetingSameError'];
                return $res;
            }
            $today = helper::today();
            if($meetingPlanTime < $today){
                dao::$errors['meetingPlanTime'] = $this->lang->review->checkResultList['meetingPlanTimeError'];
                $res['message'] =  $this->lang->review->checkResultList['meetingPlanTimeError'];
                return $res;
            }
            $params = [
                'type'              => $reviewInType,
                'meetingPlanTime' => $meetingPlanTime,
                'owner'             => $owner,
            ];
            $meetingInfo = $this->loadModel('reviewmeeting')->getInfo($params, 'id');
            if($meetingInfo && isset($meetingInfo->id)){
                dao::$errors['meetingPlanTime'] = $this->lang->review->checkResultList['meetingExist'];
                $res['message'] = $this->lang->review->checkResultList['meetingExist'];
                return $res;
            }
        }
        $res['result'] = true;
        return $res;
    }


    /**
     *新增会议评审结论
     *
     * @param $params
     * @return bool
     */
    public function addReviewMeetingResultInfo($params){
        $detailParams = $params->detailParams;
        $reviewIds    = array_column($detailParams, 'review_id');
        $reviewList   = $this->loadModel('review')->getReviewListByIds($reviewIds);
        $reviewCount = count($reviewList);
        $consumed    = 0;//$params->consumed;
        //每个项目分摊工时
        $averageConsumed = $this->loadModel('reviewmeeting')->getReviewAverageConsumed($consumed, $reviewCount);
        //单个项目详情
        $objectType = 'review';
        foreach ($detailParams as $detailInfo){
            $reviewID        = $detailInfo->review_id;
            unset($detailInfo->review_id);

            $reviewInfo      = $reviewList[$reviewID];
            $oldStatus       = $reviewInfo->status;
            $version         = $reviewInfo->version;
            $reviewResult    = $detailInfo->reviewResult;
            $postUser = [];
            if(!empty($detailInfo->verifyUsers)){
                $postUser = $detailInfo->verifyUsers;
            }
            $reviewResultInfo = $this->getReviewResultInfo($reviewResult);
            //审核结果
            $result = $reviewResultInfo->result;
            $extra = new stdClass();
            $extra->isEditInfo = $reviewResultInfo->isEditInfo;
            $result = $this->loadModel('review')->check($objectType, $reviewID, $version, $result, $this->post->comment, $reviewInfo->reviewStage, $extra);
            if(!$result){
                dao::$errors[] = $this->lang->reviewmeeting->checkReviewOpResultList['opError'];
                return false;
            }
            $extParams = [
                'isEditInfo' => $reviewResultInfo->isEditInfo,
            ];

            //项目评审的下一个状态
            $nextStatus = $this->getReviewNextStatus($reviewInfo, $result, $extParams);

            //项目评审表
            $updateParams = new stdClass();
            $updateParams->status           = $nextStatus;
            $updateParams->editDeadline     = $params->editDeadline;
            $updateParams->verifyDeadline   = $params->verifyDeadline;
            $updateParams->lastReviewedBy   = $this->app->user->account;
            $updateParams->lastReviewedDate = helper::today();
            if($result == 'reject'){
                //审核步骤reviewStage获取
                $rejectStage = $this->getRejectStage($reviewInfo->status);
                $updateParams->rejectStage = $rejectStage;
            }
            if($nextStatus == $this->lang->review->statusList['suspend']){ //挂起
                $updateParams->suspendBy = $this->app->user->account;
                $updateParams->suspendTime   = helper::now();
                $updateParams->suspendReason = $this->lang->review->suspendFixedReason;
            }
            $this->dao->update(TABLE_REVIEW)->data($updateParams)->where('id')->eq($reviewID)->exec();
            if(dao::isError()) {
                return false;
            }
            //添加单个项目工时记录
            $mailto = isset($params->mailto)? $params->mailto : '';
            $this->loadModel('consumed')->record('review', $reviewID, $averageConsumed, $this->app->user->account, $reviewInfo->status, $nextStatus, $mailto, $reviewResultInfo->isEditInfo);
            //是否需要增加审核节点
            $isAddNode = $this->getIsAddReviewNode($nextStatus);
            //新增审核节点
            if($isAddNode){
                $newReviewInfo = $this->getByID($reviewID);
                $res = $this->addReviewNode($newReviewInfo, $oldStatus, $postUser);
            }

            if(in_array($nextStatus, $this->lang->review->allowAdvanceSetReviewersStatusList)){
                $maxStage = $this->loadModel('review')->getReviewMaxStage($reviewInfo->id, 'review', $version);
                $stage    = $maxStage + 1;
                $nodeCode = $this->lang->review->nodeCodeList['verify'];
                //新增审核节点
                $reviewNodes = array(
                    array(
                        'reviewers' => $postUser,
                        'stage'     => $stage,
                        'status'    => 'wait',
                        'nodeCode'  => $nodeCode,
                    )
                );
                $this->submitReview($reviewInfo->id, 'review', $version, $reviewNodes);
            }


            if($result == 'pass') {
                $next = $this->dao->select('id')->from(TABLE_REVIEWNODE)->where('objectType')->eq('review')
                    ->andWhere('objectID')->eq($reviewID)
                    ->andWhere('version')->eq($version)
                    ->andWhere('status')->eq('wait')->orderBy('stage,id')->fetch('id');
                //有其他审核节点
                if($next) {
                    $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('pending')->where('id')->eq($next)->exec();
                    $this->dao->update(TABLE_REVIEWER)->set('status')->eq('pending')->where('node')->eq($next)->exec();
                }
            }


            //处理人
            $newReviewInfo = $this->getByID($reviewID);
            $nextDealUser = $this->getNextDealUser($newReviewInfo, $newReviewInfo->status);
            if($nextDealUser != $newReviewInfo->dealUser){
                $tempUpdateParams = new stdClass();
                $tempUpdateParams->dealUser = $nextDealUser;
                $this->dao->update(TABLE_REVIEW)->data($tempUpdateParams)->where('id')->eq($reviewID)->exec();
                $updateParams->dealUser = $nextDealUser;
            }
            //增加项目白名单
            if(!empty($postUser)){
                foreach ($postUser as $userAccount){
                    $projectId = $reviewInfo->project;
                    $res = $this->addProjectReviewWhitelist($projectId, $reviewID, $userAccount);
                }
            }
            //扩展信息
            $extChangeInfo = [];
            //抄送人
            $ext = new stdClass();
            $ext->old = '';
            $ext->new = '';

            //抄送人设置要增加部门领导,评审新建人,内部专家、外部专家1、外部专家2、相关人员
            $mailCcList = [];
            $createdDept  = $reviewInfo->createdDept;
            $deptInfo     = $this->loadModel('dept')->getByID($createdDept);
            $manager      = $deptInfo->manager;  //部门领导
            $createdBy    = $reviewInfo->createdBy; //创建人
            $expert       = $reviewInfo->expert; //内部专家
            $reviewedBy   = $reviewInfo->reviewedBy; //外部专家1
            $outside      = $reviewInfo->outside; //外部专家2
            $relatedUsers =  $reviewInfo->relatedUsers; //相关人员
            //发件人
            $managerArray    = [];
            $createdByArray  = [$createdBy];
            $expertArray     = [];
            $reviewedByArray = [];
            $outsideArray    = [];
            $relatedUsersArray = [];
            if($manager){
                $managerArray = explode(',', $manager);
            }
            if($expert){
                $expertArray = explode(',', $expert);
            }
            if($reviewedBy){
                $reviewedByArray = explode(',', $reviewedBy);
            }
            if($outside){
                $outsideArray = explode(',', $outside);
            }
            if($relatedUsers){
                $relatedUsersArray = explode(',', $relatedUsers);
            }
            $mailCcList = array_merge($managerArray, $createdByArray, $expertArray, $reviewedByArray, $outsideArray, $relatedUsersArray);
            $mailCcList = array_flip(array_flip($mailCcList));
            $mailCcListUsers = implode(',', $mailCcList);
            $ext->new = $mailCcListUsers;
            if(isset($mailto) && !empty($mailto)){
                $ext->new .= implode(' ', $mailto);
            }

            $extChangeInfo['mailto'] = $ext;
            //历史记录显示验证人员
            $reviewInfo->verifyReviewers   = '';
            $updateParams->verifyReviewers = implode(',',$detailInfo->verifyUsers);
            $logChange = common::createChanges($reviewInfo, $updateParams, $extChangeInfo);
            //日志扩展信息
            $isSetHistory = true;
            //记录日志
            $actionID = $this->loadModel('action')->create('review', $reviewID, 'reviewed', $this->post->comment, '', '', true, $isSetHistory, $logChange);
        }
        return true;
    }

    /**
     *获得允许绑定的会议列表
     *
     * @param $type
     * @return array|void
     */
    public function getAllowBindReviewListByType($type){
        $data = [];
        if(!$type){
            return $data;
        }
        //不允许绑定会议评审的状态
        $statusArray = [
            $this->lang->review->statusList['waitMeetingOwnerReview'],
            $this->lang->review->statusList['waitVerify'],
            $this->lang->review->statusList['verifying'],
            $this->lang->review->statusList['waitOutReview'],
            $this->lang->review->statusList['outReviewing'],
            $this->lang->review->statusList['meetingPassButEdit'], //会议通过待修改
            $this->lang->review->statusList['outPassButEdit'], //外部评审通过-待修改  2022-0518 新增
            $this->lang->review->statusList['rejectMeeting'], //会议退回
            $this->lang->review->statusList['rejectOut'],
            $this->lang->review->statusList['rejectVerify'],
            $this->lang->review->statusList['pass'],
            $this->lang->review->statusList['baseline'],
            'reviewpass',
            'fail',
            'drop',
        ];
        $ret = $this->dao->select('id,title')
            ->from(TABLE_REVIEW)
            ->where('type')->eq($type)
            ->andWhere('grade')->eq('meeting')
            ->andWhere('deleted')->eq('0')
            ->andWhere('status')->notin($statusArray)
            ->fetchAll();
        if($ret){
            $data = array_column($ret, 'title', 'id');
        }
        return $data;
    }

    /**
     *获得评审验证人(待审核、审核驳回、审核忽略)
     *
     * @param $reviewId
     * @param $version
     * @return string
     */
    public function getReviewVerifyUsers($reviewId, $version){
        $reviewers = '';
        if(!($reviewId)){
            return $reviewers;
        }

        $node = $this->dao->select('id')->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq('review')
            ->andWhere('objectID')->eq($reviewId)
           // ->andWhere('version')->eq($version)
            ->andWhere('nodeCode')->eq('verify')
            ->orderBy('version desc, id desc')
            ->limit(1)
            ->fetch();
        if(!$node) {
            return  $reviewers;
        }

        $sql = "SELECT reviewer FROM `zt_reviewer` wHeRe node = '{$node->id}' AND status != 'pass' AND (extra LIKE '%\"subId\":0%'  or extra is null) oRdEr bY id";
        $data =  $this->dao->query($sql)->fetchAll();
        if(!empty($data)){
            $reviewersArray = array_column($data, 'reviewer');
            $reviewers = implode(',', $reviewersArray);
        }
        return $reviewers;
    }

    // 获得评审待验证人员
    public function getReviewVerifyPendingUsers($reviewId, $version){
        $reviewers = '';
        if(!($reviewId)){
            return $reviewers;
        }

        $node = $this->dao->select('id')->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq('review')
            ->andWhere('objectID')->eq($reviewId)
            ->andWhere('version')->eq($version)
            ->andWhere('nodeCode')->eq('verify')
            ->orderBy('id_desc')
            ->fetch();
        if(!$node) {
            return  $reviewers;
        }
        $sql = "SELECT reviewer FROM `zt_reviewer` wHeRe node = '{$node->id}' AND status = 'pending' AND (extra LIKE '%\"subId\":0%'  or extra is null) oRdEr bY id";
        $data =  $this->dao->query($sql)->fetchAll();
        if(!empty($data)){
            $reviewersArray = array_column($data, 'reviewer');
            $reviewers = implode(',', $reviewersArray);
        }
        return $reviewers;
    }

    /**
     * 挂起项目评审
     *
     * @param $reviewID
     * @return false|void
     */
    public function suspend($reviewID){
        if(!$reviewID){
            dao::$errors[] = $this->lang->idEmpty;
            return false;
        }
        $reviewInfo = $this->getByID($reviewID);
        //判断是否允许挂起
        $res = $this->checkReviewIsAllowSuspend($reviewInfo);
        if(!$res['result']){
            dao::$errors[] = $res['message'];
            return false;
        }
        //提交参数信息
        $data = fixer::input('post')
            ->stripTags($this->config->review->editor->suspend['id'], $this->config->allowedTags)
            ->get();

        if(!isset($data->suspendReason) || (trim($data->suspendReason) == '')){
            dao::$errors['suspendReason'] = $this->lang->review->checkSuspendResultList['suspendReasonEmpty'];
            return false;
        }
        //时间校验
       /* $checkRes = $this->loadModel('consumed')->checkConsumedInfo($this->post->consumed);
        if(!$checkRes){
            return false;
        }*/
        $consumed = 0;//$this->post->consumed;
        if(!isset($data->comment) || (trim($data->comment) == '')){
            dao::$errors['comment'] = $this->lang->commentEmpty;
            return false;
        }
        $currentTime = helper::now();
        //下一状态
        $nextStatus = $this->lang->review->statusList['suspend'];
        $nextDealUser = $this->getNextDealUser($reviewInfo, $nextStatus);
        //更新信息
        $updateParams = new stdClass();
        $updateParams->lastStatus    = $reviewInfo->status; //记录挂起操作前状态
        $updateParams->status        = $nextStatus;
        $updateParams->dealUser      = $nextDealUser;
        $updateParams->suspendBy     = $this->app->user->account;
        $updateParams->suspendTime   = $data->suspendTime;
        $updateParams->suspendReason = $data->suspendReason;
        $this->dao->update(TABLE_REVIEW)->data($updateParams)->where('id')->eq($reviewID)->exec();
        if(dao::isError()) {
            dao::$errors[] = $this->lang->review->reviewcheckSuspendResultList['fail'];
            return false;
        }
        //查询下面是否有待审核节点，如果中途挂起则将pending和wait的状态置为ignore
        $maxVersion = $this->getReviewNodeMaxVersion($reviewID);
        $needDealIgnore = $this->loadModel('review')->getUnDealReviewNodes('review', $reviewID, $maxVersion);
        if(!empty($needDealIgnore)){
            $ret = $this->loadModel('review')->ignoreReviewNodeAndReviewers($needDealIgnore);
        }
        //有会议号
        if($reviewInfo->meetingCode){
            if(in_array($reviewInfo->status, $this->lang->review->allowFormalMeetingReviewStatusList)){
                $res = $this->loadModel('reviewmeeting')->deleteMeetingDetail($reviewInfo->meetingCode, $reviewID); //只是修改项目评审，不修改会议状态
            }else{
                $meetingInfo = $this->loadModel('reviewmeeting')->getMeetingByMeetingCode($reviewInfo->meetingCode, 'meetingCode,status');
                if($meetingInfo && isset($meetingInfo->status) && ($meetingInfo->status != 'pass')){ //关联了会议评审但是没有确定会议评审结论时可以取消绑定
                    $this->loadModel('reviewmeeting')->cancelBindReviewMeeting($reviewInfo->meetingCode, $reviewID);
                }
            }
        }

        //增加工作量记录
        $this->loadModel('consumed')->record('review', $reviewID, $consumed, $this->app->user->account, $reviewInfo->status, $nextStatus, '', '', $reviewInfo->version);
        //返回
        $logChange = common::createChanges($reviewInfo, $updateParams);
        return $logChange;
    }

    /**
     * 恢复项目评审
     *
     * @param $reviewID
     * @return false|void
     */
    public function renew($reviewID){
        if(!$reviewID){
            dao::$errors[] = $this->lang->idEmpty;
            return false;
        }
        $reviewInfo = $this->getByID($reviewID);
        //判断是否允许恢复
        $res = $this->checkReviewIsAllowRenew($reviewInfo, $this->app->user->account);
        if(!$res['result']){
            dao::$errors[] = $res['message'];
            return false;
        }

        //提交参数信息
        $data = fixer::input('post')
            ->stripTags($this->config->review->editor->renew['id'], $this->config->allowedTags)
            ->get();

        if(!isset($data->renewReason) || (trim($data->renewReason) == '')){
            dao::$errors['renewReason'] = $this->lang->review->checkRenewResultList['renewReasonEmpty'];
            return false;
        }
        //时间校验
       /* $checkRes = $this->loadModel('consumed')->checkConsumedInfo($this->post->consumed);
        if(!$checkRes){
            return false;
        }*/
        $consumed = 0;//$this->post->consumed;
        if(!isset($data->comment) || (trim($data->comment) == '')) {
            dao::$errors['comment'] = $this->lang->commentEmpty;
            return false;
        }
        //判断是否能恢复
        $allReviewStatus =  $this->getAllReviewStatus($reviewID);
        if($reviewInfo->lastStatus == 'pass'){
            $allReviewStatus[] = 'close';
        }
        if(!isset($data->nextStage) || !in_array($data->nextStage,$allReviewStatus)){
            dao::$errors['nextStage'] = $this->lang->review->checkRenewResultList['beyondNextStage'];
            return false;
        }

        //下一状态和待处理人
        $nextStatus = $this->getStatusByNodeCode($data->nextStage);
        if ($nextStatus == 'waitMeetingOwnerReview'){ //待确认会议结论
            if(empty($reviewInfo->meetingCode)){
                dao::$errors[''] = $this->lang->review->checkRenewResultList['meetingCodeEmpty'];
                return false;
            }else{
                $meetingInfo = $this->loadModel('reviewmeeting')->getMeetingByMeetingCode($reviewInfo->meetingCode, 'status');
                if($meetingInfo->status == 'pass'){
                    dao::$errors[''] = sprintf($this->lang->review->checkRenewResultList['meetingCodeEnd'], $reviewInfo->meetingCode);
                    return false;
                }
            }
        }

        $nextDealUser = $this->getReNewNextDealUser($reviewInfo, $nextStatus, $data->nextStage);
        //备注挂起步骤
        //$rejectStage = $this->getRejectStage($reviewInfo->status);
        //更新信息
        $updateParams = new stdClass();
        $updateParams->status        = $nextStatus;
        $updateParams->dealUser      = $nextDealUser;
        $updateParams->renewBy       = $this->app->user->account;
        $updateParams->renewTime     = $data->renewTime;
        $updateParams->renewReason   = $data->renewReason;
        //$updateParams->rejectStage   = $rejectStage;
        $updateParams->version       = $reviewInfo->version + 1;
        if(in_array($nextStatus, $this->lang->review->sumitDateStatusList)){
            $updateParams->submitDate = helper::now();
        }
        if($nextStatus == 'waitPreReview'){
            $updateParams->submitDate =$reviewInfo->createdDate;
        }
        if($nextStatus =='waitFirstMainReview' or $nextStatus=='firstMainReviewing' ){
            $endDate = $reviewInfo->endDate;
        }else{
            if(isset($updateParams->submitDate)){
                $endDate = $this->getEndDate($nextStatus,$updateParams->submitDate, $reviewInfo);
            }
        }

        if(isset($endDate)) {
            if ($nextStatus == 'waitPreReview') {
                $updateParams->preReviewDeadline = $endDate;
            } else if ($nextStatus == 'waitFirstAssignDept') {
                $updateParams->firstReviewDeadline = $endDate;
            } else if ($nextStatus == 'waitFirstReview') {
                $updateParams->firstReviewDeadline = $endDate;
            }
            if (in_array($nextStatus, $this->lang->review->sumitDateStatusList)) {
                $updateParams->endDate = $endDate;
            }
        }
        //重新在线评审结论
        if($nextStatus == $this->lang->review->statusList['waitFormalReview'] && $reviewInfo->grade == 'meeting'){
            if(empty($reviewInfo->meetingCode)){
                if($reviewInfo->isSkipMeetingResult == 1){
                    $updateParams->isSkipMeetingResult = 2;
                }
            }else{
                $tempMeetingCode = $reviewInfo->meetingCode;
                $tempMeetingInfo = $this->loadModel('reviewmeeting')->getMeetingByMeetingCode($tempMeetingCode);
                if(empty($tempMeetingInfo) || (!empty($tempMeetingInfo) && $tempMeetingInfo->status == 'pass')){
                    $updateParams->isSkipMeetingResult = 2;
                }else{
                    $res = $this->loadModel('reviewmeeting')->bindToReviewMeeting($tempMeetingCode, $reviewInfo, $nextStatus);
                }
            }

        }
        //修改主表
        $this->dao->update(TABLE_REVIEW)->data($updateParams)->where('id')->eq($reviewID)->exec();
        if(dao::isError()) {
            dao::$errors[] = $this->lang->review->checkRenewResultList['fail'];
            return false;
        }
        if(!empty($meetingCode)){
            $updateParams->meetingCode = $meetingCode;
        }

        $newReviewInfo = $this->getByID($reviewID);
        //恢复时增加历史节点
        $this->renewNewVersionReviewNodes($newReviewInfo, $reviewInfo->status, 1);

        if($nextStatus == 'waitMeetingReview'){ //待会议评审
            //不修改评审表中的字段
            $meetingPlanType = $this->post->meetingPlanType ? $this->post->meetingPlanType: '';
            $meetingCode     = isset($data->meetingCode)? $data->meetingCode: '';
            $meetingPlanTime = isset($data->meetingPlanTime)? $data->meetingPlanTime: '';
            $checkRes = $this->checkReviewMeetingInfo($reviewInfo, $reviewInfo->type, $meetingPlanType, $meetingCode, $meetingPlanTime, $reviewInfo->owner);
            if(!$checkRes['result']){
                return false;
            }
            if(isset($data->meetingCode)){
                unset($data->meetingCode);
            }
            if(isset($data->meetingPlanTime)){
                unset($data->meetingPlanTime);
            }
            if($meetingPlanType == 1){ //选择已有会议
                $res = $this->loadModel('reviewmeeting')->bindToReviewMeeting($meetingCode, $reviewInfo, $nextStatus);
                if(!$res['result']){
                    dao::$errors[] = $res['message'];
                    return false;
                }
                $meetingCode = $res['data']->meetingCode;
            }elseif ($meetingPlanType == 2){ //新建会议
                $res = $this->loadModel('reviewmeeting')->createReviewMeeting($reviewInfo, $nextStatus, $meetingPlanTime,1);
                if(!$res['result']){
                    dao::$errors[] = $res['message'];
                    return false;
                }
                $meetingCode = $res['data']->meetingCode;
            }

            //查询项目评审是否有会议评审节点，如果没有新增
            $nodeCode = $this->getReviewNodeCodeByStatus($newReviewInfo->status, $reviewInfo->status);
            $objectType = 'review';
            $version  = $newReviewInfo->version;
            $nodeId   = $this->loadModel('review')->getReviewNodeId($objectType, $reviewID, $version, $nodeCode);
            if(!$nodeId) { //不存在，新增审核节点
                $maxStage = $this->loadModel('review')->getReviewMaxStage($reviewID, $objectType, $version);
                $stage = $maxStage + 1;
                //新增会议审核节点信息
                $reviewNodes = array(
                    array(
                        'reviewers' => $nextDealUser,
                        'stage' => $stage,
                        'nodeCode' => $nodeCode,
                    )
                );
                $this->loadModel('review')->submitReview($reviewID, $objectType, $version, $reviewNodes);
            }
        }elseif ($nextStatus == 'waitMeetingOwnerReview'){ //待确认会议结论,重新激活
            $params = new  stdClass();
            $params->deleted = '0';
            $params->status  = $nextStatus;
            $res = $this->loadModel('reviewmeeting')->activeMeetingDetailInfo($reviewInfo->meetingCode, $reviewID, $params);
        }
        //增加工作量记录
        $this->loadModel('consumed')->record('review', $reviewID, $consumed, $this->app->user->account, $reviewInfo->status, $nextStatus, '', '', $reviewInfo->version);
        //返回
        $logChange = common::createChanges($reviewInfo, $updateParams);
        return $logChange;
    }

    /**
     * 修改用户信息
     *
     * @param $reviewID
     * @param $field
     * @return array|bool
     */
    public function editUsersByField($reviewID, $field){
        if(!($reviewID && $field)){
            dao::$errors[] = $this->lang->idEmpty;
            return false;
        }
        $reviewInfo = $this->getByID($reviewID);
        $type = $reviewInfo->type;
        //判断是否允许修改
        $res = $this->checkIsAllowEditUsersByField($reviewInfo, $this->app->user->account);

        if(!$res['result']){
            dao::$errors[] = $res['message'];
            return false;
        }

        //提交参数信息
        $data = fixer::input('post')
            ->join($field, ',')
            ->get();
        $data->$field = isset($data->$field) ? trim($data->$field, ',') : '';
        //必填字段
        $requiredFields = explode(',', $this->config->review->create->requiredFields);
        if(in_array($field, $requiredFields) || (in_array($type, array('manage', 'pro')) && $field == 'expert')){
            if(!isset($data->$field) || empty($data->$field)){
                $message = vsprintf($this->lang->review->checkResultList['fieldEmpty'], $this->lang->review->$field);
                dao::$errors[$field] = $message;
                return false;
            }
        }
        //返回
        $updateParams = new stdClass();
        $updateParams->$field = $data->$field;
        $this->dao->update(TABLE_REVIEW)->data($updateParams)->where('id')->eq($reviewID)->exec();
        if(dao::isError()) {
            dao::$errors[] = $this->lang->review->checkResultList['opError'];
            return false;
        }
        $logChange = common::createChanges($reviewInfo, $updateParams);
        return $logChange;
    }


    /**
     * 获得挂起恢复的下一步操作人
     *
     * @param $reviewInfo
     * @param $nextStatus
     * @param $nextNodeCode
     * @return string
     */
    public function getReNewNextDealUser($reviewInfo, $nextStatus, $nextNodeCode){
        $dealUsers = '';
        if($nextNodeCode == $this->lang->review->nodeCodeList['formalAssignReviewer']){ //评审主席指派评审专家
            $nextNodeCode = [
                $nextNodeCode,
                $this->lang->review->nodeCodeList['formalAssignReviewerAppoint'], //可能存在指派
            ];
        }
        $ret = $this->loadModel('review')->getReviewerListByNodeCode('review', $reviewInfo->id, $reviewInfo->version, $nextNodeCode);
        if(!$ret){
            $dealUsers = $this->getNextDealUser($reviewInfo, $nextStatus);
            return $dealUsers;
        }
        $allReviewers = [];
        $unOpReviewers = [];
        $unOpStatusArray = ['pending', 'wait', 'ignore'];
        foreach ($ret as $val){
            $reviewer = $val->reviewer;
            $status = $val->status;
            $allReviewers[] = $reviewer;
            if(in_array($status, $unOpStatusArray)){
                $unOpReviewers[] = $reviewer;
            }
        }
        $renewIncludeMultipleStatusList = array_keys($this->lang->review->renewIncludeMultipleStatusList);
        if(in_array(strtolower($nextStatus), $renewIncludeMultipleStatusList)){
            if(!empty($unOpReviewers)){ //有待处理的取待处理的
                $dealUsers = implode(',', $unOpReviewers);
            }else{
                $dealUsers = implode(',', $allReviewers); //取全部
            }
        }else{
            $dealUsers = implode(',', $allReviewers); //取全部
        }
        return $dealUsers;
    }



    /**
     * 通过nodeCode获得对应状态
     *
     * @param $nodeCode
     * @return string
     */
    public function getStatusByNodeCode($nodeCode){
        $status = '';
        switch ($nodeCode){
            case $this->lang->review->nodeCodeList['preReview']:
                $status = $this->lang->review->statusList['waitPreReview'];
                break;

            case $this->lang->review->nodeCodeList['firstAssignDept']:
                $status = $this->lang->review->statusList['waitFirstAssignDept'];
                break;

            case $this->lang->review->nodeCodeList['firstAssignReviewer']:
                $status = $this->lang->review->statusList['waitFirstAssignReviewer'];
                break;

            case $this->lang->review->nodeCodeList['firstReview']: //待初审
                $status = $this->lang->review->statusList['waitFirstReview'];
                break;

            case $this->lang->review->nodeCodeList['firstMainReview']: //待初审-主审
                $status = $this->lang->review->statusList['waitFirstMainReview'];
                break;

            case $this->lang->review->nodeCodeList['formalAssignReviewer']: //待正审指派
                $status = $this->lang->review->statusList['waitFormalAssignReviewer'];
                break;

            case $this->lang->review->nodeCodeList['formalAssignReviewerAppoint']: //待正审指派
                $status = $this->lang->review->statusList['waitFormalAssignReviewer'];
                break;

            case $this->lang->review->nodeCodeList['formalReview']:  //待正审-专家在线评审
                $status = $this->lang->review->statusList['waitFormalReview'];
                break;

            case $this->lang->review->nodeCodeList['formalOwnerReview']: //评审主席确定评审结论
                $status = $this->lang->review->statusList['waitFormalOwnerReview'];//评审主席确定评审结论
                break;

            case $this->lang->review->nodeCodeList['meetingReview']: //会议评审
                $status = $this->lang->review->statusList['waitMeetingReview'];//会议评审
                break;

            case $this->lang->review->nodeCodeList['meetingOwnerReview']: //评审主席确定会议评审结论
                $status = $this->lang->review->statusList['waitMeetingOwnerReview'];//评审主席确定会议评审结论
                break;

            case $this->lang->review->nodeCodeList['verify']: //待验证资料
                $status = $this->lang->review->statusList['waitVerify'];
                break;

            case $this->lang->review->nodeCodeList['outReview']: //待外部审核
                $status = $this->lang->review->statusList['waitOutReview'];
                break;

            case $this->lang->review->nodeCodeList['archive']: //待归档
                $status = $this->lang->review->statusList['archive'];
                break;

            case $this->lang->review->nodeCodeList['baseline']: //待打基线
                $status = $this->lang->review->statusList['baseline'];
                break;

            case $this->lang->review->nodeCodeList['prePassButEdit']: //预审通过待修改
                $status = $this->lang->review->statusList['prePassButEdit'];
                break;

            case $this->lang->review->nodeCodeList['firstPassButEdit']: //初审通过待修改
                $status = $this->lang->review->statusList['firstPassButEdit'];
                break;
            case $this->lang->review->nodeCodeList['formalPassButEdit']: //在线待修改
                $status = $this->lang->review->statusList['formalPassButEdit'];
                break;

            case $this->lang->review->nodeCodeList['meetingPassButEdit']: //会议审核通过待修改
                $status = $this->lang->review->statusList['meetingPassButEdit'];
                break;

            case  $this->lang->review->nodeCodeList['outPassButEdit']: //外部审核待修改
                $status = $this->lang->review->statusList['outPassButEdit'];
                break;

            case $this->lang->review->nodeCodeList['rejectVerifyButEdit']: //验证退回待修改
                $status = $this->lang->review->statusList['rejectVerify'];
                break;
            case 'close': //待关闭
                $status = $this->lang->review->statusList['pass'];
                break;

            default:
                break;
        }
        return $status;
    }

    /**
     * 判断初审人员和主审人员是否一致且有且只有一个
     */
    public function  judgeSamePerson($reviewID){
        $reviewNodeReviewerList = $this->getReviewNodeFormatReviewerList($reviewID);
        $firstReviewer = '';
        $firstMainReviewer = '';
        foreach ($reviewNodeReviewerList as $nodeCode => $nodeData){
            $count = $nodeData['total'];
            $currentNode = $nodeData['data'];
            if(isset($nodeData['data'][0]->dept) and count($nodeData['data'][0]->dept)>1 ){
                return false;
            }
            foreach ($currentNode as $key => $current){
               if($current->nodeCode == 'firstReview'){
                   if($current->reviewers['total'] != 1){
                       return false;
                   }else{
                       $firstReviewer =   $current->reviewers[0]->reviewer;
                   }
               }elseif($current->nodeCode == 'firstMainReview'){
                   if($current->reviewers['total'] != 1){
                       return false;
                   }else{
                       $firstMainReviewer =   $current->reviewers[0]->reviewer;
                   }
               }
            }
    }

    if($firstReviewer ==$firstMainReviewer ){
        return true;
    }
    return false;
}

/**
 * 获取评审所有经过的状态
 */
    public function getAllReviewStatus($reviewID){
        $maxVersion =  $this->getReviewNodeMaxVersion($reviewID);
        $allReviewStatus = $this->dao->select('nodeCode')->from(TABLE_REVIEWNODE)
                ->where('objectType')->eq('review')
                ->andWhere('objectID')->eq($reviewID)
                ->andWhere('version')->eq($maxVersion)
                ->fetchPairs();
        //去掉空元素
        return Array_filter($allReviewStatus);
    }

    /**
     * 恢复到指定节点新增历史版本信息
     *
     * @param $reviewInfo
     * @param $oldStatus
     * @return false|void
     */
    public function renewNewVersionReviewNodes($reviewInfo, $oldStatus, $renewSet = '0'){
        $res = false;
        if(!$reviewInfo){
            return $res;
        }
        $isAddCurrentNode = true;
        $reviewID = $reviewInfo->id;
        $status   = $reviewInfo->status;
        $version  = $reviewInfo->version;
        $dealUser = $reviewInfo->dealUser;

        //审核步骤id
        $rejectStage =  $reviewInfo->rejectStage;
        if(!$dealUser){
            dao::$errors[] = $this->lang->review->checkApplyResultList['dealUserEmpty'];
            return $res;
        }

        $reviewers = explode(',', $dealUser);
        //节点标识
        $nodeCode = $this->getReviewNodeCodeByStatus($status, $oldStatus);

        $subObjectType = $this->getReviewNodeSubObjectType($status);
        $type = $this->getReviewNodeType($status);
        //扩展信息
        $extParams = array(
            'subObjectType' => $subObjectType,
            'type'          => $type
        );
        if(in_array($oldStatus, $this->lang->review->passButEditStatusList)){ //审核通过以后需要编辑，编辑以后再申请审核（不需要升级版本）
            $maxStage = $this->loadModel('review')->getReviewMaxStage($reviewID, 'review', $version);
            if(in_array($oldStatus, $this->lang->review->allowAdvanceSetReviewersStatusList)){
                $stage = $maxStage;
            }else{
                $stage = $maxStage + 1;
            }
            //增加当前审核节点
            $nodes = array(
                array(
                    'reviewers' => $reviewers,
                    'stage'    => $stage,
                    'nodeCode' => $nodeCode,
                )
            );

            if(in_array($oldStatus, $this->lang->review->allowAdvanceSetReviewersStatusList)){ //删除原来的节点
                $nodeCode = $this->lang->review->nodeCodeList['verify'];
                $this->delReviewNodeByNodeCode($reviewID, 'review', $version, $nodeCode);
            }
            //新增的本次节点
            $this->submitReview($reviewID, 'review', $version, $nodes, $extParams);
        }else{
            //获得历史版本
            $oldVersion =  $this->getReviewNodeMaxVersion($reviewID);
            $stage =  $this->loadModel('review')->getNodeStage('review', $reviewID, $oldVersion, $nodeCode);
            if(!$stage){
                $maxStage = $this->loadModel('review')->getReviewMaxStage($reviewID, 'review', $oldVersion);
                $stage = $maxStage + 1;
            }

            //增加当前审核节点
            $nodes = array(
                array(
                    'reviewers' => $reviewers,
                    'stage'    => $stage,
                    'nodeCode' => $nodeCode,
                )
            );
            //历史节点你信息补全
            if($status != $this->lang->review->statusList['waitPreReview']) { //提交后流转到非待预审（即初审或者待验证资料）
                $historyReviews =  $this->loadModel('review')->getHistoryReviewers('review', $reviewID, $oldVersion, $stage);
                if(!empty($historyReviews)){
                    foreach ($historyReviews as $currentNodeInfo){
                        $currentNodeReviewers = $currentNodeInfo->reviewers;
                        unset($currentNodeInfo->reviewers);
                        unset($currentNodeInfo->id);
                        $currentNodeInfo->version = $version;
                        //新增审核节点
                        //$meetingReviewStage =  $this->loadModel('review')->getNodeStage('review', $reviewID, $oldVersion, 'formalOwnerReview');
                        $this->dao->insert(TABLE_REVIEWNODE)->data($currentNodeInfo)->exec();
                        $newNodeID = $this->dao->lastInsertID();
                        foreach ($currentNodeReviewers as $currentNodeReviewer){
                            $currentNodeReviewer->node = $newNodeID;
                            unset($currentNodeReviewer->id);
                            $this->dao->insert(TABLE_REVIEWER)->data($currentNodeReviewer)->exec();
                        }
                    }
                }
            }
            $currentNodeId = 0;
            if($renewSet) {
                $isAddCurrentNode = $status != 'pass' ? true: false; //关闭时不需要关闭节点
                if($isAddCurrentNode){
                    //恢复并挂起，查询当前节点是否有已经审核处理过的
                    $renewIncludeMultipleStatusList = array_keys($this->lang->review->renewIncludeMultipleStatusList);
                    if (in_array(strtolower($status), $renewIncludeMultipleStatusList)) {
                        $nodeInfo = $this->loadModel('review')->getNodeInfoByNodeCode('review', $reviewID, $oldVersion, $nodeCode);
                        $unOpReviewers = $this->loadModel('review')->getReviewerListByNodeCode('review', $reviewID, $oldVersion, $nodeCode, $statusArray = ['pending', 'wait', 'ignore']);
                        $reviewedReviewers = $this->loadModel('review')->getReviewerListByNodeCode('review', $reviewID, $oldVersion, $nodeCode, $statusArray = ['pass', 'reject']);
                        if (!empty($unOpReviewers) && !empty($reviewedReviewers)) { //上一个版本既有处理的又有未处理的，把处理的审核节点复制到当前版本
                            unset($nodeInfo->id);
                            $nodeInfo->version = $version;
                            $nodeInfo->status = 'pending';
                            $this->dao->insert(TABLE_REVIEWNODE)->data($nodeInfo)->exec();
                            $currentNodeId = $this->dao->lastInsertID();
                            foreach ($reviewedReviewers as $currentNodeReviewer) {
                                $currentNodeReviewer->node = $currentNodeId;
                                unset($currentNodeReviewer->id);
                                $this->dao->insert(TABLE_REVIEWER)->data($currentNodeReviewer)->exec();
                            }
                        }
                    }
                }

            }
            //返回初审时需要补充下一节点的人
            if (($status == $this->lang->review->statusList['waitFirstReview']) && (in_array($rejectStage, $this->lang->review->returnFirstRejectStageList) || $renewSet)) {
                //获得初审主审人员
                $nextStage = $stage + 1;
                $nextNodeCode = $this->lang->review->nodeCodeList['firstMainReview'];
                $mainReviewersInfo =  $this->loadModel('review')->getReviewersByNodeCode('review', $reviewID, $oldVersion, $nextNodeCode);
                $mainReviewers = [];
                if ($mainReviewersInfo) {
                    $mainReviewers = explode(',', $mainReviewersInfo);
                }
                if($renewSet){ //恢复时取未处理人员
                    $nodes = array(
                        array(
                            'reviewers' => $reviewers,
                            'stage'    => $stage,
                            'nodeId' => $currentNodeId,
                            'nodeCode' => $nodeCode,
                        )
                    );
                }else{
                    //参与人员
                    $includeReviewers = array_diff($reviewers, $mainReviewers);
                    //新增的本次节点（主审人员和审核人员）
                    $joinReviewers = [];
                    $joinReviewers[1] = $mainReviewers; //主审核人员
                    $joinReviewers[2] = $includeReviewers;
                    $nodes = array(
                        array(
                            'reviewers' => $joinReviewers,
                            'stage'    => $stage,
                            'nodeId' => $currentNodeId,
                            'nodeCode' => $nodeCode,
                        )
                    );
                }
                //新增的本次节点（主审人员和审核人员）
                $this->submitReview($reviewID, 'review', $version, $nodes, $extParams);
                //新增主要审核人员
                if ($mainReviewers){
                    $nextNodes = array(
                        array(
                            'reviewers' => $mainReviewers,
                            'stage'    => $nextStage,
                            'status'   => 'wait',
                            'nodeCode' => $nextNodeCode,
                        )
                    );
                    $this->submitReview($reviewID, 'review', $version, $nextNodes, $extParams);
                }
            }else{
                if($isAddCurrentNode){
                    $nodes = array(
                        array(
                            'reviewers' => $reviewers,
                            'stage'    => $stage,
                            'nodeId' => $currentNodeId,
                            'nodeCode' => $nodeCode,
                        )
                    );
                    //新增的本次节点
                    if($nodeCode !='meetingReview'){
                        $this->submitReview($reviewID, 'review', $version, $nodes, $extParams);
                    }
                }
            }
        }
        return true;
    }


    /**
     * 获取截止时间
     *
     * @param $status
     * @param $begin
     * @param $reviewInfo
     * @return false|string
     */
    public function getEndDate($status, $begin, $reviewInfo){
        $actualEnd = '';
        if(!($status && $begin)){
            return $actualEnd;
        }
        //获取后台配置的时间
        $dateLevel = $this->lang->review->endDates;
        //获取节假日
        $days = '';
       if($status =='waitPreReview'){
           $days =  $dateLevel['waitPreReview'];
       }else if($status =='waitFirstAssignDept' ){
           $days =  $dateLevel['waitFirstAssignDept'];
       } else if($status =='waitFirstReview' ){
           $days =  $dateLevel['waitFirstReview'];
       }else if($status=='waitFirstAssignReviewer'){
           $days =  $dateLevel['waitFirstAssignReviewer'];
       }else if($status =='waitFirstMainReview' ){
           $days =  $dateLevel['waitFirstReview'];
       }else if($status=='waitFormalAssignReviewer'){
           $days =  $dateLevel['waitFormalAssignReviewer'];
       }else if($status=='waitFormalOwnerReview'){
           $days =  $dateLevel['waitFormalOwnerReview'];
       }else if($status=='waitFormalReview' ){
           $days =  $dateLevel['waitFormalReview'];
       }else if($status =='waitMeetingReview' ){
           $days =  $dateLevel['waitMeetingReview'];
       }else if($status =='waitMeetingOwnerReview'){
           $days =  $dateLevel['waitMeetingOwnerReview'];
       }else if($status =='waitOutReview'){
           $days =  $dateLevel['waitOutReview'];
       }else if($status =='waitVerify'){
           $days =  $dateLevel['waitVerify'];
       }else if($status=='baseline'){
           $days =  $dateLevel['baseline'];
       } else if($status =='pass'){
           $days =  $dateLevel['pass'];

       }else{
           $days = 10;
       }
       if(empty($days)){
           $days = 10;
       }
       
       /*
        $end = date('Y-m-d', strtotime("$begin + $days day"));
       //循环判断截止日期
        $i = 10;
        while($i != 0){
            $fullDays = $this->loadModel('holiday')->getActualWorkingDays($begin, $end);
            echo '<pre>';
            print_r($fullDays);
            echo '</pre>';
            echo '<br/>';
            $diffDays = $days+1 -count($fullDays);
            if($diffDays == 0){
                $end = $end;
                break;
            }else{
                // $begin = $end;
                $end =date('Y-m-d', strtotime("$end + $diffDays day"));
                $i = $i-1;
            }
        }
        $actualEnd = $end;
       */

        $actualEnd = helper::getTrueWorkDay($begin, $days, true);
        return $actualEnd;
   }

    /**
     *获得评审阶段
     *
     * @param string $beforeStatus
     * @param string $afterStatus
     * @return string
     */
   public function getReviewStage($beforeStatus = '', $afterStatus = ''){
       $reviewStage = '';
       if($afterStatus == $this->lang->review->statusList['suspend']){ //挂起
           $reviewStage = $this->lang->review->reviewStageList['suspend'];
           return $reviewStage;
       }
       if($afterStatus == 'updateFiles'){ //修改上传附件
           $reviewStage = $this->lang->review->reviewStageList['updateFiles'];
           return $reviewStage;
       }
       if(!$beforeStatus){ //前置状态为空
           $reviewStage = $this->lang->review->reviewStageList['preReviewBefore'];
           return $reviewStage;
       }

       $closeStatusList = $this->lang->review->notCloseStatusList;
       switch ($beforeStatus){
           case $this->lang->review->statusList['waitApply']: //操作前待申请
               if(in_array($afterStatus, $closeStatusList)){ //操作关闭
                   $reviewStage = $this->lang->review->reviewStageList['close'];
               }elseif($afterStatus == $this->lang->review->statusList['waitFirstReview']){ //流转到初审
                   $reviewStage = $this->lang->review->reviewStageList['firstEdit'];
               }elseif($afterStatus == $this->lang->review->statusList['waitVerify']){ //流转到验证
                   $reviewStage = $this->lang->review->reviewStageList['verifyEdit'];
               }else{
                   $reviewStage = $this->lang->review->reviewStageList['preReviewBefore'];
               }
               break;

           case $this->lang->review->statusList['waitPreReview']: //操作前待预审
               if(in_array($afterStatus, $closeStatusList)){ //操作关闭
                   $reviewStage = $this->lang->review->reviewStageList['close'];
               }else {
                   $reviewStage = $this->lang->review->reviewStageList['preReview']; //预审操作
               }
               break;

           case $this->lang->review->statusList['waitFirstAssignDept']: //待指派初审部门
               if(in_array($afterStatus, $closeStatusList)){ //操作关闭
                   $reviewStage = $this->lang->review->reviewStageList['close'];
               }else {
                   $reviewStage = $this->lang->review->reviewStageList['firstAssignDept']; //指派初审部门操作
               }
               break;

           case $this->lang->review->statusList['waitFirstAssignReviewer']: //待指派初审核人员
           case $this->lang->review->statusList['firstAssigning']: //指派初审核人员中
               if(in_array($afterStatus, $closeStatusList)){ //操作关闭
                   $reviewStage = $this->lang->review->reviewStageList['close'];
               }else {
                   $reviewStage = $this->lang->review->reviewStageList['firstAssignReviewer']; //指派初审人员操作
               }
               break;

           case $this->lang->review->statusList['waitFirstReview']: //待初审
           case $this->lang->review->statusList['firstReviewing']: //初审中
               if(in_array($afterStatus, $closeStatusList)){ //操作关闭
                   $reviewStage = $this->lang->review->reviewStageList['close'];
               }elseif($afterStatus == $this->lang->review->statusList['waitFormalAssignReviewer']){ //操作恢复
                   $reviewStage = $this->lang->review->reviewStageList['renew'];
               }else {
                   $reviewStage = $this->lang->review->reviewStageList['firstReview']; //初审人员审核
               }
               break;

           case $this->lang->review->statusList['waitFirstMainReview']: //初审-待主审人员审核
           case $this->lang->review->statusList['firstMainReviewing']: //初审-主审人员审核中
               if(in_array($afterStatus, $closeStatusList)){ //操作关闭
                   $reviewStage = $this->lang->review->reviewStageList['close'];
               }else {
                   $reviewStage = $this->lang->review->reviewStageList['firstMainReview']; //初审主审人员确定初审结果
               }
               break;

           case $this->lang->review->statusList['waitFormalAssignReviewer']:
               if(in_array($afterStatus, $closeStatusList)){ //操作关闭
                   $reviewStage = $this->lang->review->reviewStageList['close'];
               }else {
                   $reviewStage = $this->lang->review->reviewStageList['formalAssignReviewer']; //指派评审专家
               }
               break;

           case $this->lang->review->statusList['waitFormalReview']: //正式人员审核
           case $this->lang->review->statusList['formalReviewing']: //正式人员审核中
               if(in_array($afterStatus, $closeStatusList)){ //操作关闭
                   $reviewStage = $this->lang->review->reviewStageList['close'];
               }else {
                   $reviewStage = $this->lang->review->reviewStageList['formalReview']; //专家在线评审
               }
               break;

           case $this->lang->review->statusList['waitFormalOwnerReview']:
               if(in_array($afterStatus, $closeStatusList)){ //操作关闭
                   $reviewStage = $this->lang->review->reviewStageList['close'];
               }elseif($afterStatus == $this->lang->review->statusList['waitFormalAssignReviewer']){ //操作恢复
                   $reviewStage = $this->lang->review->reviewStageList['renew'];
               }else {
                   $reviewStage = $this->lang->review->reviewStageList['formalOwnerReview']; //正式评审-评审主席确定在线评审结论
               }
               break;

           case $this->lang->review->statusList['waitMeetingReview']: //待会议评审
           case $this->lang->review->statusList['meetingReviewing']: //待会议评审中
               if(in_array($afterStatus, $closeStatusList)){ //操作关闭
                   $reviewStage = $this->lang->review->reviewStageList['close'];
               }else {
                   $reviewStage = $this->lang->review->reviewStageList['meetingReview']; //正式评审-专家会议评审
               }
               break;

           case $this->lang->review->statusList['waitMeetingOwnerReview']: //待评审主席确定会议评审结论
               if(in_array($afterStatus, $closeStatusList)){ //操作关闭
                   $reviewStage = $this->lang->review->reviewStageList['close'];
               }elseif($afterStatus == $this->lang->review->statusList['waitFormalAssignReviewer']){ //操作恢复
                   $reviewStage = $this->lang->review->reviewStageList['renew'];
               }else {
                   $reviewStage = $this->lang->review->reviewStageList['meetingOwnerReview']; //会议评审结论
               }
               break;

           case $this->lang->review->statusList['waitVerify']: //待验证
           case $this->lang->review->statusList['verifying']: //验证中
               if(in_array($afterStatus, $closeStatusList)){ //操作关闭
                   $reviewStage = $this->lang->review->reviewStageList['close'];
               }else {
                   $reviewStage = $this->lang->review->reviewStageList['verify']; //验证
               }
               break;

           case $this->lang->review->statusList['waitOutReview']: //待外审核
           case $this->lang->review->statusList['outReviewing']: //外部审核中
               if(in_array($afterStatus, $closeStatusList)){ //操作关闭
                   $reviewStage = $this->lang->review->reviewStageList['close'];
               }else {
                   $reviewStage = $this->lang->review->reviewStageList['outReview']; //外部审核
               }
               break;

           case $this->lang->review->statusList['archive']: //待归档
               if((in_array($afterStatus, $closeStatusList)) && ($afterStatus != $this->lang->review->statusList['baseline'])){ //操作关闭
                   $reviewStage = $this->lang->review->reviewStageList['close'];
               }else {
                   $reviewStage = $this->lang->review->reviewStageList['archive']; //归档
               }
               break;

           case $this->lang->review->statusList['baseline']: //待打基线
               if((in_array($afterStatus, $closeStatusList)) && ($afterStatus != $this->lang->review->statusList['archive'])){ //操作关闭
                   $reviewStage = $this->lang->review->reviewStageList['close'];
               }else {
                   $reviewStage = $this->lang->review->reviewStageList['baseline']; //打基线
               }
               break;

           case $this->lang->review->statusList['prePassButEdit']: //预审修改
               if(in_array($afterStatus, $closeStatusList)){ //操作关闭
                   $reviewStage = $this->lang->review->reviewStageList['close'];
               }else {
                   $reviewStage = $this->lang->review->reviewStageList['preEdit']; //预审修改
               }
               break;

           case $this->lang->review->statusList['firstPassButEdit']: //初审修改
               if(in_array($afterStatus, $closeStatusList)){ //操作关闭
                   $reviewStage = $this->lang->review->reviewStageList['close'];
               }else {
                   $reviewStage = $this->lang->review->reviewStageList['firstEdit']; //初审修改
               }
               break;

           case $this->lang->review->statusList['formalPassButEdit']: //正式审核专家线上评审待修改
               if(in_array($afterStatus, $closeStatusList)){ //操作关闭
                   $reviewStage = $this->lang->review->reviewStageList['close'];
               }else {
                   $reviewStage = $this->lang->review->reviewStageList['formalEdit']; //专家线上评审修改
               }
               break;

           case $this->lang->review->statusList['meetingPassButEdit']: //正式审核专家会议评审待修改
               if(in_array($afterStatus, $closeStatusList)){ //操作关闭
                   $reviewStage = $this->lang->review->reviewStageList['close'];
               }else {
                   $reviewStage = $this->lang->review->reviewStageList['meetingEdit']; //专家会议评审修改
               }
               break;

           case $this->lang->review->statusList['outPassButEdit']: //外部评审待修改
               if(in_array($afterStatus, $closeStatusList)){ //操作关闭
                   $reviewStage = $this->lang->review->reviewStageList['close'];
               }else {
                   $reviewStage = $this->lang->review->reviewStageList['outEdit']; //外部评审修改
               }
               break;

           case $this->lang->review->statusList['suspend']: //外部评审待修改
               if(in_array($afterStatus, $closeStatusList)){ //操作关闭
                   $reviewStage = $this->lang->review->reviewStageList['close'];
               }else {
                   $reviewStage = $this->lang->review->reviewStageList['renew']; //恢复操作
               }
               break;

           case $this->lang->review->statusList['pass']:
               $reviewStage = $this->lang->review->reviewStageList['close']; //关闭操作
               break;

           case $this->lang->review->statusList['recall']: //撤回
               if(in_array($afterStatus, $closeStatusList)){ //操作关闭
                   $reviewStage = $this->lang->review->reviewStageList['close'];
               }else {
                   $reviewStage = $this->lang->review->reviewStageList['recall']; //撤回
               }
               break;

           case $this->lang->review->statusList['rejectPre']: //预审退回
               if(in_array($afterStatus, $closeStatusList)){ //操作关闭
                   $reviewStage = $this->lang->review->reviewStageList['close'];
               }else {
                   $reviewStage = $this->lang->review->reviewStageList['rejectPreEdit']; //预审退回修改
               }
               break;

           case $this->lang->review->statusList['rejectFirst']: //初审退回
               if(in_array($afterStatus, $closeStatusList)){ //操作关闭
                   $reviewStage = $this->lang->review->reviewStageList['close'];
               }else {
                   $reviewStage = $this->lang->review->reviewStageList['rejectFirstEdit']; //初审退回修改
               }
               break;

           case $this->lang->review->statusList['rejectFormal']: //正式评审-线上评审退回
               if(in_array($afterStatus, $closeStatusList)){ //操作关闭
                   $reviewStage = $this->lang->review->reviewStageList['close'];
               }else {
                   $reviewStage = $this->lang->review->reviewStageList['rejectFormalEdit']; //正式评审-线上评审退回修改
               }
               break;

           case $this->lang->review->statusList['rejectMeeting']: //正式评审-会议评审退回
               if(in_array($afterStatus, $closeStatusList)){ //操作关闭
                   $reviewStage = $this->lang->review->reviewStageList['close'];
               }else {
                   $reviewStage = $this->lang->review->reviewStageList['rejectMeetingEdit']; //正式评审-会议评审退回修改
               }
               break;

           case $this->lang->review->statusList['rejectOut']: //外部评审退回
               if(in_array($afterStatus, $closeStatusList)){ //操作关闭
                   $reviewStage = $this->lang->review->reviewStageList['close'];
               }else {
                   $reviewStage = $this->lang->review->reviewStageList['rejectOutEdit']; //外部评审退回修改
               }
               break;

           case $this->lang->review->statusList['rejectVerify']: //验证退回
               if(in_array($afterStatus, $closeStatusList)){ //操作关闭
                   $reviewStage = $this->lang->review->reviewStageList['close'];
               }else {
                   $reviewStage = $this->lang->review->reviewStageList['rejectVerifyEdit']; //验证退回修改
               }
               break;

           default:
               break;
       }
       return $reviewStage;
   }

   // 喧喧消息
    public function getXuanxuanTargetUser($obj,$objectType, $objectID, $actionType, $actionID, $actor = ''){
        $review = $this->getById($objectID);
        $users  = $this->loadModel('user')->getPairs('noletter');

        /*获取初审相关数据*/
        $dataTrial = $this->getTrial($objectID, $review->version, $users,0);
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
        $toList = $sendUsers['toList'];

        if(in_array($review->status, $this->lang->review->closeStatusList)){
            if($review->comment == $this->lang->review->autoCloseComment && $review->status == 'archive'){
                // 当前状态为待归档且关闭节点为自动关闭时 直接给待归档待处理人发邮件(跳过给关闭节点待处理人发邮件)
                $toList = $review->dealUser;
            }
        }

        $server   = $this->loadModel('im')->getServer('zentao');
        $url = $server.helper::createLink($objectType, 'view', "id=$objectID", 'html');
        $subcontent = [];
        $subcontent['headTitle']    = '';
        $subcontent['headSubTitle'] = '';
        $subcontent['count']        = 0;
        $subcontent['id']           = 0;
        $subcontent['parent']       = '';
        $subcontent['parentURL']    = "";
        $subcontent['cardURL']      = $url;
        $subcontent['name']         = '';//消息体 编号后边位置 标题

        //标题
        $title = '';
        $actions = [];

        return ['toList'=>$toList,'subcontent'=>$subcontent,'url'=>$url,'title'=>$title,'actions'=>$actions];
    }


    /**
     * 设置初审主审信息
     *
     * @param $reviewId
     * @param $version
     * @return bool
     */
    public function setSyncFirstMainReview($reviewId, $version){
        if(!$reviewId){
            return false;
        }
        $isEditNodeStatus = false; //是否修改node状态
        $statusArray = ['pending', 'wait'];
        $mainNodeCode = $this->lang->review->nodeCodeList['firstMainReview'];
        //查询是否有待审核的节点
        $mainNodeInfo = $this->loadModel('review')->getNodeByNodeCode('review', $reviewId, $version, $mainNodeCode, $statusArray);
        if(!$mainNodeInfo){
            return false;
        }
        $mainNodeId = $mainNodeInfo->id;
        $unCheckUserList = $this->dao->select('*')
            ->from(TABLE_REVIEWER)
            ->where('node')->eq($mainNodeId)
            ->andWhere('status')->in($statusArray)
            ->groupBy('reviewer')
            ->fetchAll();
        if(empty($unCheckUserList)){ //不存在未审核的用户
            $isEditNodeStatus = true;
        }else{ //有未审核的用户
            $users = array_column($unCheckUserList, 'reviewer'); //未审核的用户
            $parentIds = array_column($unCheckUserList, 'parentId'); //父节点
            $unCheckUserList = array_column($unCheckUserList, null, 'reviewer'); //用户名作为键值
            //查询这些用户在初审的操作信息
            $joinNodeCode = $this->lang->review->nodeCodeList['firstReview'];
            $joinNodeInfo = $this->loadModel('review')->getNodeByNodeCode('review', $reviewId, $version, $joinNodeCode);
            if(!$joinNodeInfo){
                return true;
            }
            $joinNodeId = $joinNodeInfo->id;
            $joinCheckedUserList = $this->dao->select('*')->from(TABLE_REVIEWER)
                ->where('node')->eq($joinNodeId)
                ->andWhere('reviewer')->in($users)
                ->andWhere('status')->notin($statusArray)
                ->groupBy('reviewer')
                ->fetchAll();
            if(empty($joinCheckedUserList)){
                return true;
            }
            //检查这些部门在初审参与阶段是否需要审核
            $isEditList = $this->loadModel('review')->checkNodeIsEditInfoGroupByParentId($joinNodeId, $parentIds);
            foreach ($joinCheckedUserList as $val){ //初审参与审核信息同步到初审主审信息
                $reviewer = $val->reviewer;
                $mainReviewerInfo = zget($unCheckUserList, $reviewer, new stdClass());
                $parentId = zget($mainReviewerInfo, 'parentId');
                $isEditDeptInfo = zget($isEditList, $parentId, []);
                $extraInfo      = json_decode($val->extra, true);
                $extraInfo['isEditInfo'] = zget($isEditDeptInfo, 'isEditInfo', 2); //是否需要修改
                $updateParams = new stdClass();
                $updateParams->status     = $val->status;
                $updateParams->comment    = $val->comment;
                $updateParams->extra      = json_encode($extraInfo);
                $updateParams->reviewTime = $val->reviewTime;
                $ret = $this->dao->update(TABLE_REVIEWER)->data($updateParams)
                    ->where('node')->eq($mainNodeId)
                    ->andWhere('reviewer')->eq($reviewer)
                    ->andWhere('status')->in($statusArray)
                    ->exec();
                unset($unCheckUserList[$reviewer]);
            }
            if(empty($unCheckUserList)){ //不存在未审核的用户
                $isEditNodeStatus = true;
            }
        }
        if($isEditNodeStatus){ //需要修改node表状态
            $updateParams = new stdClass();
            $updateParams->status = 'pass';
            $ret = $this->dao->update(TABLE_REVIEWNODE)->data($updateParams)
                ->where('id')->eq($mainNodeId)->exec();
        }
        return true;
    }

    /**
     * 获得初审主审信息同步后的状态
     *
     * @param $reviewId
     * @param $reviewType
     * @param $version
     * @return bool
     */
    public function getSyncFirstMainReviewNextStatus($reviewId, $reviewType, $version){
        $nextStatus = '';
        if(!($reviewId && $reviewType)){
            return $nextStatus;
        }
        $mainNodeCode = $this->lang->review->nodeCodeList['firstMainReview'];
        //查询是否有待审核的节点
        $mainNodeInfo = $this->dao->select('*')->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq('review')
            ->andWhere('objectID')->eq($reviewId)
            ->andWhere('version')->eq($version)
            ->andWhere('nodeCode')->eq($mainNodeCode)
            ->orderBy('stage,id')
            ->fetch();
        if(!$mainNodeInfo){
            return $nextStatus;
        }
        $status = $mainNodeInfo->status;
        if($status == 'reject'){
            $nextStatus = $this->lang->review->statusList['rejectFirst'];
            return $nextStatus;
        }
        if($status == 'suspend'){
            $nextStatus = $this->lang->review->statusList['suspend'];
            return $nextStatus;
        }

        $userList = $this->dao->select('*')->from(TABLE_REVIEWER)
            ->where('node')->eq($mainNodeInfo->id)
            ->fetchAll();
        if(empty($userList)){
            return $nextStatus;
        }
        $reviewedUsers = [];
        $unReviewUsers = [];
        $isEditInfo = false;
        $statusArray = ['pending', 'wait'];
        foreach ($userList as $val){
            $reviewerUser = $val->reviewer;
            $reviewStatus = $val->status;
            if(in_array($reviewStatus, $statusArray)){
                $unReviewUsers[] = $reviewerUser;
            }else{
                $reviewedUsers[] = $reviewerUser;
                $extraInfo = json_decode($val->extra, true);
                if(isset($extraInfo['isEditInfo']) && ($extraInfo['isEditInfo'] == '1')){
                    $isEditInfo = true;
                }
            }
        }
        if(empty($reviewedUsers)){ //没有已经审核的，全部未审核
            $nextStatus = $this->lang->review->statusList['waitFirstMainReview'];
            return $nextStatus;
        }
        if(!empty($unReviewUsers)){ //部分审核，部分未审核
            $nextStatus = $this->lang->review->statusList['firstMainReviewing'];
            return $nextStatus;
        }
        //全部审核过
        if($isEditInfo){
            $nextStatus = $this->lang->review->statusList['firstPassButEdit'];
            return $nextStatus;
        }

        if($reviewType == $this->lang->review->typeValList['cbp']){ //金科初审
            $nextStatus = $this->lang->review->statusList['waitOutReview']; //待外部审核
        }else{
            $nextStatus = $this->lang->review->statusList['waitFormalAssignReviewer']; //待评审主席指派正式审核人员
        }
        return $nextStatus;
    }

    /**
     * 获得关闭时的邮件账户信息
     *
     * @param $review
     * @return array
     */
    public function getCloseMailUsersInfo($review){
        $mailMainUsers = []; //主送人
        $mailCopyUsers = [];//抄送人
        $data = [
            'mailMainUsers' => $mailMainUsers,
            'mailCopyUsers' => $mailCopyUsers,
        ];
        if(!$review){
            return $data;
        }
        //主送人
        $mailMainUsers = [$review->createdBy];
        $mailMainUsers = array_filter(array_flip(array_flip($mailMainUsers)));
        $reviewID = $review->id;
        $mailCopyUsersStr = $review->createdBy.','.$review->qa.','.$review->reviewer.','.$review->owner.','.$review->expert.','.$review->reviewedBy.','. $review->qualityCm;
        $mailCopyUsers = explode(',', $mailCopyUsersStr);
        //相关人员
        $relatedUsers = $review->relatedUsers;
        if($relatedUsers){
            $relatedUsers = explode(',', $relatedUsers);
            $mailCopyUsers = array_merge($mailCopyUsers, $relatedUsers);
        }
        $objectType = 'review';
        $maxVersion = $this->loadModel('review')->getReviewNodeMaxVersion($reviewID);
        $allReviewUsers  = $this->loadModel('review')->getAllReview($objectType, $maxVersion, $reviewID); //获取评审所有人员
        if(count($allReviewUsers) > 0){
            $allReviewUsers = array_values($allReviewUsers);
            $createdDept  = $review->createdDept;
            $deptInfo =  $this->loadModel('dept')->getByID($createdDept);
            $deptManager = empty($deptInfo->manager1) ? array() : array($deptInfo->manager1);//部门领导
            $mailCopyUsers = array_merge($mailCopyUsers, $allReviewUsers,$deptManager); // 取并集
        }
        $mailCopyUsers = array_filter(array_flip(array_flip($mailCopyUsers)));
        $mailCopyUsers = array_diff($mailCopyUsers, $mailMainUsers); //排除主送人员
        $data['mailMainUsers'] = $mailMainUsers;
        $data['mailCopyUsers'] = $mailCopyUsers;
        return $data;
    }

    /**
     * 获得是否需要全部审核
     *
     * @param $status
     * @return bool
     */
    public function  getReviewIsAllUsersCheck($status){
        $is_all_check_pass = true;
        if(in_array($status, $this->lang->review->allowSingleUserReviewStatusList)){
            $is_all_check_pass = false;
        }
        return $is_all_check_pass;
    }

    /**
     * 获得用户的父级节点
     *
     * @param $nodeId
     * @param $userAccount
     * @return int
     */
    public function getReviewerParentId($nodeId, $userAccount){
        $parentId = 0;
        if(!($nodeId && $userAccount)){
            return $parentId;
        }
        $ret = $this->dao->select('t3.parentId')
            ->from(TABLE_USER)->alias('t1')
            ->leftJoin(TABLE_USER)->alias('t2')->on('t1.dept = t2.dept')
            ->leftJoin(TABLE_REVIEWER)->alias('t3')->on('t3.reviewer = t2.account')
            ->Where('t1.account')->eq($userAccount)
            ->andWhere('t3.node')->eq($nodeId)
            ->andWhere('t3.parentId')->gt(0)
            ->fetch();
        if($ret){
            $parentId = $ret->parentId;
            return $parentId;
        }
        $ret = $this->dao->select('parentId')
            ->from(TABLE_REVIEWER)
            ->where('node')->eq($nodeId)
            ->andWhere('parentId')->gt(0)
            ->fetch();
        if($ret){
            $parentId = $ret->parentId;
        }
        return $parentId;
    }

    /**
     * 设置验证结果
     *
     * @param $reviewID
     * @return bool
     */
    public function setVerifyResult($reviewID){
        $reviewInfo = $this->getByID($reviewID);
        $user = $this->app->user->account;

        //验证结果
        $data = fixer::input('post')
            ->get();
        if(!$data->result){
            dao::$errors['result'] = $this->lang->review->checkSetVerifyResultList['resultEmptyError'];
            return false;
        }
        //是否允许设置验证结果
        $res = $this->checkReviewIsAllowSetVerifyResult($reviewInfo, $this->app->user->account);
        if(!$res['result']){

            dao::$errors[] = $res['message'];
            return false;
        }
        $reviewIssueCount = $this->loadModel('reviewissue')->getNeedDealReviewIssueCount($reviewID);
        if($reviewIssueCount > 0){
            dao::$errors[] = $this->lang->review->checkSetVerifyResultList['issueStatusError'];
            return false;
        }
        //验证
        $result = $data->result;
        $version    = $reviewInfo->version;
        $nodeCode  = 'verify';
        $objectType = 'review';
        $reviewNode = $this->loadModel('review')->getPendingReviewNode($objectType, $reviewID, $version, $nodeCode);
        if(!$reviewNode){
            dao::$errors[] = $this->lang->review->checkResultList['statusError'];
            return false;
        }

        //审批节点id
        $nodeId = $reviewNode->id;
        //处理审核操作
        $comment = $this->lang->review->autoReviewPassComment;
        $ret = $this->loadModel('review')->setReviewNodeAutoPass($nodeId, $comment);
        if(!$ret){
            dao::$errors[] = $this->lang->review->checkResultList['opError'];
            return false;
        }

        $oldStatus = $reviewInfo->status;
        //下一个状态
        $nextStatus = $this->getReviewNextStatus($reviewInfo, $result);
        //修改信息
        $updateParams = new stdClass();
        $updateParams->status = $nextStatus;
        $updateParams->lastReviewedBy   = $user;
        $updateParams->lastReviewedDate = helper::now();

        //修改评审表
        $this->dao->update(TABLE_REVIEW)->data($updateParams)->where('id')->eq($reviewID)->exec();
        if(dao::isError()) {
            return false;
        }

        //记录工时信息
        $consumed = 0;
        $this->loadModel('consumed')->record($objectType, $reviewID, $consumed, $user, $oldStatus, $nextStatus);

        if($result == 'pass' || $nextStatus == $this->lang->review->statusList['rejectVerify']) { //审核通过或者验证驳回
            $ret = $this->loadModel('review')->setNextReviewNodePending('review', $reviewID, $version);
        }

        //处理人
        $newReviewInfo = $this->getByID($reviewID);
        $nextDealUser = $this->getNextDealUser($newReviewInfo, $newReviewInfo->status);

        if($nextDealUser != $newReviewInfo->dealUser) {
            $tempUpdateParams = new stdClass();
            $tempUpdateParams->dealUser = $nextDealUser;
            $this->dao->update(TABLE_REVIEW)->data($tempUpdateParams)->where('id')->eq($reviewID)->exec();
            $updateParams->dealUser = $nextDealUser;
        }

        // 记录历史记录
        $changes = common::createChanges($reviewInfo, $updateParams);
        $actionID = $this->loadModel('action')->create($objectType, $reviewID, 'setverifyresult');
        $this->action->logHistory($actionID, $changes);
        // 自动关闭
        if($nextStatus == 'pass'){
            $this->autoclose($reviewID);
        }
        return true;
    }

    /**
     * 手动发送未处理问题提出人邮件
     *
     * @param $reviewID
     * @return bool
     */
    public function sendUnDealIssueUsersMail($reviewID){
        $review = $this->getByID($reviewID);
        $user = $this->app->user->account;
        $data = fixer::input('post')->get();
        if(!isset($data->unDealIssueRaiseByUsers) || empty($data->unDealIssueRaiseByUsers)){
            dao::$errors[] = $this->lang->review->checkSendMailList['unDealIssueUserEmptyError'];
            return false;
        }

        //未验证问题提出人
        $toList = implode(',', $data->unDealIssueRaiseByUsers);
        $ccList = '';
        $this->loadModel('mail');
        $users  = $this->loadModel('user')->getPairs('noletter');
        /* 获取后台通知中配置的邮件发信。*/
        $this->app->loadLang('custommail');
        $mailConf   = isset($this->config->global->setReviewproblemMail) ? $this->config->global->setReviewproblemMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf   = json_decode($mailConf);
        $browseType = 'review';

        /* 处理邮件发信的标题。*/
        $mailTitle = vsprintf($mailConf->mailTitle, $mailConf->variables);

        /* Get mail content. */
        $oldcwd     = getcwd();
        $modulePath = $this->app->getModulePath($appName = '', 'review');
        $viewFile   = $modulePath . 'view/sendundealissueusersmail.html.php';
        chdir($modulePath . 'view');

        if(file_exists($modulePath . 'ext/view/sendundealissueusersmail.html.php'))
        {
            $viewFile = $modulePath . 'ext/view/sendundealissueusersmail.html.php';
            chdir($modulePath . 'ext/view');
        }

        ob_start();
        include $viewFile;
        foreach(glob($modulePath . 'ext/view/sendundealissueusersmail.*.html.hook.php') as $hookFile) include $hookFile;
        $mailContent = ob_get_contents();
        ob_end_clean();
        /* 处理邮件标题。*/
        $subject = $mailTitle;
        /* Send emails. */
        if(empty($toList)) return false;
        $this->mail->send($toList, $subject, $mailContent,$ccList);

        if($this->mail->isError()) trigger_error(join("\n", $this->mail->getError()));
        return true;
    }
}
