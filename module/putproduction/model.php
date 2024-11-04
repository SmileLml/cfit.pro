<?php
class putproductionModel extends model
{
    public function buildSearchForm($queryID, $actionURL){
        $this->config->putproduction->search['actionURL'] = $actionURL;
        $this->config->putproduction->search['queryID']   = $queryID;
        $this->loadModel('search')->setSearchParams($this->config->putproduction->search);
    }

    /**
     * 获得查询列表
     *
     * @param $browseType
     * @param $queryID
     * @param $orderBy
     * @param null $pager
     * @return array
     */
    public function getList($browseType, $queryID, $orderBy, $pager = null){
        $data = [];
        $putproductionQuery = '';
        if($browseType == 'bysearch') {
            $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : '';
            if ($query) {
                $this->session->set('putproductionQuery', $query->sql);
                $this->session->set('putproductionForm', $query->form);
            }
            if ($this->session->putproductionQuery == false) $this->session->set('putproductionQuery', ' 1 = 1');

            $putproductionQuery = $this->session->putproductionQuery;
        }
        $account = $this->app->user->account;
        //查询列表
        $ret = $this->dao->select('*')
            ->from(TABLE_PUTPRODUCTION)
            ->where('deleted')->eq('0')
            ->beginIF($browseType != 'all' && $browseType != 'bysearch'   &&  $browseType != 'tomedeal' &&  $browseType != 'externalreject')
            ->andWhere('status')->eq($browseType)
            ->fi()
            ->beginIF($browseType == 'tomedeal')
            ->andWhere("FIND_IN_SET('{$account}', dealUser)")
            ->fi()
            ->beginIF($browseType == 'externalreject')
            ->andWhere('status')->in('externalreject,filereturn')
            ->fi()
            ->beginIF($browseType == 'bysearch')
            ->andWhere($putproductionQuery)
            ->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');
        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'putproduction', $browseType != 'bysearch');
        if($ret){
            $data = $ret;
        }
        return $data;
    }
    /**
     * 获得单条投产信息
     *
     * @param $putProductionId
     * @return mixed
     */
    public function getByID($putProductionId){
        $data = new  stdClass();
        $objectType = $this->config->putproduction->objectType;
        $info = $this->dao->select("*")
            ->from(TABLE_PUTPRODUCTION)
            ->where('id')->eq($putProductionId)
            ->andWhere('deleted')->eq('0')
            ->fetch();
        if(!$info){
            return $data;
        }
        $info->releases = [];
        if($info->releaseId){
            $releaseIds = array_filter(explode(',', $info->releaseId));
            $releases = $this->loadModel('release')->getReleaseListByIds($releaseIds, 'id,name,path,files');
            $info->releases = $releases;
        }
        //阶段列表
        $info->stageList = array_filter(explode(',', $info->stage));
        $info->isOnlyFistStage = $this->getIsOnlyFistStage($info->stage);
        if($info->firstStagePid){
            $select = 'id, CASE WHEN `desc` != "" THEN concat(concat(code,"（"), concat(`desc`, "）")) ELSE code END as code';
            $firstStageInfo = $this->getMainInfoByID($info->firstStagePid, $select);
            $info->firstStageInfo = $firstStageInfo;
        }
        $info->isIncludeFirstStage  = false;
        $info->isIncludeSecondStage = false;

        if(in_array('1', $info->stageList)){
            $info->isIncludeFirstStage = true;
        }
        if(in_array('2', $info->stageList)){
            $info->isIncludeSecondStage = true;
        }

        $consumedList = $this->dao->select('*')
            ->from(TABLE_CONSUMED)
            ->where('objectType')->eq($objectType)
            ->andWhere('objectID')->eq($putProductionId)
            ->fetchAll();
        $info->consumed = $consumedList;
        $this->resetNodeAndReviewerName($info->createdDept);
        return $info;
    }

    /**
     * 获得主要信息
     *
     * @param $putProductionId
     * @param string $select
     * @return mixed
     */
    public function getMainInfoByID($putProductionId, $select = '*'){
        $info = $this->dao->select($select)
            ->from(TABLE_PUTPRODUCTION)
            ->where('id')->eq($putProductionId)
            ->andWhere('deleted')->eq('0')
            ->fetch();
       return $info;
    }

    /**
     * 是否仅仅包含第一阶段
     *
     * @param $stage
     * @return bool
     */
    public function getIsOnlyFistStage($stage){
        $isOnlyFistStage = false;
        $stageList = array_filter(explode(',', $stage));
        if(count($stageList) == 1 && in_array('1', $stageList)){
            $isOnlyFistStage = true;
        }
        return $isOnlyFistStage;
    }

    /**
     * 创建
     *
     * @return mixed
     */
    public function create(){
        $objectType = $this->config->putproduction->objectType;
        $issubmit = $_POST['issubmit'];
        $postData = fixer::input('post')
            ->join('inProjectIds', ',')
            ->join('app', ',')
            ->join('productId', ',')
            ->join('demandId', ',')
            ->join('property',',')
            ->join('stage',',')
            ->join('dataCenter',',')
            ->remove('nodes,requiredNodes,uid')
            ->get();
        $postData = $this->getFormatPostData($postData);
        //审核节点
        $nodes = $this->post->nodes;
        $requiredNodesData = $this->post->requiredNodes;
        $requiredRequiredNodes = helper::getReviewRequiredNodes($requiredNodesData);
        if($issubmit == 'submit'){
            //检查基本信息
            $checkRes = $this->checkBasicInfo($postData);
            if(!$checkRes){
                return dao::$errors;
            }
            //检查审批节点信息
            $checkRes = $this->checkReviewerNodesInfo($nodes, $requiredRequiredNodes);
            if(!$checkRes){
                return dao::$errors;
            }
        }
        $status   = $this->lang->putproduction->statusArray['waitsubmit'];//待提交
        $dealUser = $this->app->user->account;
        $reviewerInfo = [$status => [$dealUser]];
        $reviewerInfo           = $this->getFormatReviewerInfo($nodes, $requiredRequiredNodes, $reviewerInfo); //审核人信息
        $postData->reviewerInfo = json_encode($reviewerInfo);
        $postData->createdBy    = $this->app->user->account;
        $postData->createdDept  = $this->app->user->dept;
        $postData->createdDate  = helper::now();
        $postData->status       = $status;
        $postData->dealUser     = $dealUser;
        $postData->code = $this->getCode();  //投产移交单号

        $this->createPutProduction($postData);
        if(dao::isError()) {
            return dao::$errors;
        }
        $putProductionId = $this->dao->lastInsertID();
        if($issubmit == 'submit'){ //提交
            //调取接口同步审核人信息
            $allReviewerInfo = $this->getAllReviewerInfo($postData, $reviewerInfo);
            $res = $this->loadModel('iwfp')->startWorkFlow($objectType, $putProductionId, $postData->code, $postData->createdBy, $allReviewerInfo,'1',$this->lang->putproduction->reviewNodeCodeNameList, '');
            if(dao::isError()) {
                dao::$errors = []; //重新赋值，否则数据库操作停止
                $ret = $this->dao->update(TABLE_PUTPRODUCTION)->set('deleted')->eq('1')
                    ->where('id')->eq($putProductionId)
                    ->exec();
                return dao::$errors[''] = $res;
            }

            $processInstanceId = $res->processInstanceId;
            $updateParams = new stdClass();
            $updateParams->workflowId = $processInstanceId;
            $this->dao->update(TABLE_PUTPRODUCTION)->data($updateParams)->autoCheck()
                ->where('id')->eq($putProductionId)
                ->exec();
            //从待提交到提交后进入待cm处理环节(提交默认处理通过)
            $dealResult = '1';
            $res = $this->submit($putProductionId, $dealUser, $dealResult, $postData->remark, true);
            if(dao::isError()) { //保存成功，但是提交失败
                $this->loadModel('consumed')->record($objectType, $putProductionId, '0', $this->app->user->account, '', $status);
                return $res;
            }
        }else{
            //添加状态流转
            $this->loadModel('consumed')->record($objectType, $putProductionId, '0', $this->app->user->account, '', $status);
        }


        //返回
        return $putProductionId;
    }

    /**
     * 获得所有审核人信息
     *
     * @param $putproduction
     * @param $reviewerInfo
     * @return mixed
     */
    public function getAllReviewerInfo($putproduction, $reviewerInfo){
        $dealUsers = [$putproduction->createdBy];
        if(!isset($reviewerInfo['reject'])){
            if(isset($reviewerInfo['waitsubmit'])){
                $reviewerInfo['reject'] = $reviewerInfo['waitsubmit'];
            }else{
                $reviewerInfo['reject'] = $dealUsers;
            }
        }

        if(!isset($reviewerInfo['reject'])){
            if(isset($reviewerInfo['waitsubmit'])){
                $reviewerInfo['reject'] = $reviewerInfo['waitsubmit'];
            }else{
                $reviewerInfo['reject'] = $dealUsers;
            }
        }
        if(!isset($reviewerInfo['externalreject'])){
            if(isset($reviewerInfo['waitsubmit'])){
                $reviewerInfo['externalreject'] = $reviewerInfo['waitsubmit'];
            }else{
                $reviewerInfo['externalreject'] = $dealUsers;
            }
        }
        $reviewerInfo['waitdelivery'] = [$this->config->putproduction->guestjkUser];        //待交付
        $reviewerInfo['waitexternalreview'] = [$this->config->putproduction->guestjxUser]; //待外部审批
        $reviewerInfo['syncfailed'] = explode(',' , $this->config->putproduction->syncFailList);        //同步失败-后台自定义
        $reviewerInfo['filereturn'] = $reviewerInfo['waitproduct'];         //同步材料退回
        return $reviewerInfo;
    }

    /**
     * 提交操作
     *
     * @param $putProductionId
     * @param $dealUser
     * @param $dealResult
     * @param string $dealMessage
     * @param $createSubmitIsMerge 创建和提交是否合并
     * @return array|bool
     */
    public function submit($putProductionId, $dealUser, $dealResult, $dealMessage = '', $createSubmitIsMerge = false){
        $objectType = $this->config->putproduction->objectType;
        $putproductionInfo = $this->getMainInfoByID($putProductionId);
        //检查是否允许更新
        $res = $this->checkIsAllowSubmit($putproductionInfo, $dealUser);
        if(!$res['result']){
            return dao::$errors[''] = $res['message'];
        }
        if(!$createSubmitIsMerge){ //单独提交
            $res = $this->checkInfoIsIntegrity($putproductionInfo);
            if(!$res['result']){
                return dao::$errors[''] = $this->lang->putproduction->submitMsgTip;
            }
        }
        $version = $putproductionInfo->version;
        if(in_array($putproductionInfo->status, $this->lang->putproduction->needUpdateVersionStatusArray)){
            $version = $putproductionInfo->version + 1;
        }
        $processInstanceId = $putproductionInfo->workflowId ? $putproductionInfo->workflowId:'';
        /*if(!$processInstanceId){ //保存工作流*/
            $reviewerInfo =  json_decode($putproductionInfo->reviewerInfo, true);
            $allReviewerInfo =  $this->getAllReviewerInfo($putproductionInfo, $reviewerInfo);
            $res = $this->saveWorkFlow($putproductionInfo, $allReviewerInfo,$version);
            if($res){
                $putproductionInfo = $this->getMainInfoByID($putProductionId);
                $processInstanceId = $putproductionInfo->workflowId ? $putproductionInfo->workflowId:'';
            }
        /*}*/
        $userVariableList = new stdClass();
        $userVariableList->level = $putproductionInfo->level;

        $res = $this->loadModel('iwfp')->completeTaskWithClaim($processInstanceId, $dealUser, $dealMessage, $dealResult, $userVariableList, $version);
        if(dao::isError()) {
            return $res;
        }
        //更新表已经提交
        $updateParams = new stdClass();
        $nextStatus = $res->toXmlTask;
        $nextUsers  = is_array($res->dealUser) ? implode(',', $res->dealUser):$res->dealUser;
        $updateParams->status   = $nextStatus;
        $updateParams->dealUser = $nextUsers;
        $updateParams->version = $version;
        $this->dao->update(TABLE_PUTPRODUCTION)->data($updateParams)->autoCheck()
            ->where('id')->eq($putProductionId)
            ->exec();
        if(dao::isError()) {
            return dao::getError();
        }
        if($createSubmitIsMerge){
            $oldStatus = '';
        }else{
            $oldStatus = $putproductionInfo->status;
        }
        //添加状态流转
        $this->loadModel('consumed')->record($objectType, $putProductionId, '0', $dealUser, $oldStatus, $nextStatus);
        return $res;
    }

//    /**
//     * 获得下一状态(暂时用不到)
//     *
//     * @param $putproductionInfo
//     * @param $dealResult
//     * @param bool $is_all_check
//     * @return string
//     */
//    public function getNextStatus($putproductionInfo, $dealResult, $is_all_check = false){
//        $nextStatus = '';
//        $status =  $putproductionInfo->status;
//        $reviewerInfo = json_decode($putproductionInfo->reviewerInfo);
//        if(!($status &&  $reviewerInfo && $dealResult)){
//            return $nextStatus;
//        }
//        $statusArray = array_keys($reviewerInfo);
//
//        if($dealResult == '1'){
//            if($is_all_check){ //需要全部审核
//                $dealUsers = array_filter(explode(',', $putproductionInfo->dealUser));
//                if(count($dealUsers) == 1){
//                    $subStatusArray = array_slice($statusArray, array_search($status)+1);
//                    foreach ($subStatusArray as $val){
//                        if(isset($reviewerInfo[$val]) && !empty($reviewerInfo[$val])){
//                            $nextStatus = $val;
//                            break;
//                        }
//                    }
//                }else{
//                    $nextStatus = $status;
//                }
//            }else{ //一人操作即可
//                $subStatusArray = array_slice($statusArray, array_search($status)+1);
//                foreach ($subStatusArray as $val){
//                    if(isset($reviewerInfo[$val]) && !empty($reviewerInfo[$val])){
//                        $nextStatus = $val;
//                        break;
//                    }
//                }
//            }
//
//        }else{
//            $nextStatus = $this->lang->putproduction->statusArray['reject'];
//        }
//
//        return $nextStatus;
//    }

//    /**
//     * 获得下一步操作人（暂时用不到）
//     *
//     * @param $reviewerInfo
//     * @param $nextStatus
//     * @return string
//     */
//    public function getNextUsers($reviewerInfo, $nextStatus){
//        $nextUsers = zget($reviewerInfo, $nextStatus, []);
//        return $nextUsers;
//    }

    /**
     *变更投产信息
     *
     * @param $putproductionId
     * @return mixed
     */
    public function update($putproductionId){
        $objectType = $this->config->putproduction->objectType;
        $putproductionInfo = $this->getByID($putproductionId);
        $issubmit = $_POST['issubmit'];
        $postData = fixer::input('post')
            ->join('inProjectIds', ',')
            ->join('app', ',')
            ->join('productId', ',')
            ->join('demandId', ',')
            ->join('property',',')
            ->join('stage',',')
            ->join('dataCenter',',')
            ->remove('nodes,requiredNodes,uid')
            ->get();
        $postData = $this->getFormatPostData($postData);
        //审核节点
        $nodes = $this->post->nodes;
        $requiredNodesData = $this->post->requiredNodes;
        $requiredRequiredNodes = helper::getReviewRequiredNodes($requiredNodesData);
        $account = $this->app->user->account;
        //检查是否允许更新
        $res = $this->checkIsAllowEdit($putproductionInfo, $account);
        if(!$res['result']){
            dao::$errors[] = $res['message'];
            return false;
        }

        if($issubmit == 'submit'){
            //检查基本信息
            $checkRes = $this->checkBasicInfo($postData, 'edit', $putproductionId);
            if(!$checkRes){
                return dao::$errors;
            }
            //检查审批节点信息
            $checkRes = $this->checkReviewerNodesInfo($nodes, $requiredRequiredNodes);
            if(!$checkRes){
                return dao::$errors;
            }
        }
        $oldStatus = $putproductionInfo->status;
        $status = $oldStatus;

        $waitSubmitStatus   = $this->lang->putproduction->statusArray['waitsubmit'];//待提交
        $oldReviewerInfo = json_decode($putproductionInfo->reviewerInfo, true);
        if(isset($oldReviewerInfo[$waitSubmitStatus]) && !empty($oldReviewerInfo[$waitSubmitStatus])){
            $reviewerInfo = [$waitSubmitStatus => $oldReviewerInfo[$waitSubmitStatus]];
        }else{
            $reviewerInfo = [$waitSubmitStatus => [$putproductionInfo->createdBy]];
        }

        $reviewerInfo = $this->getFormatReviewerInfo($nodes, $requiredRequiredNodes, $reviewerInfo); //审核人信息
        $postData->reviewerInfo = json_encode($reviewerInfo);
        $postData->editedBy    = $this->app->user->account;
        $postData->editedDate  = helper::now();

        $this->dao->begin(); //调试完逻辑最后开启事务
        $this->updatePutProduction($putproductionId, $postData);
        //调取接口同步审核人信息
        if($issubmit == 'submit'){ //提交
           /* if(!($putproductionInfo->workflowId && ($putproductionInfo->reviewerInfo == $postData->reviewerInfo))){
                //调取接口同步审核人信息
                $allReviewerInfo = $this->getAllReviewerInfo($putproductionInfo, $reviewerInfo);
                $res = $this->saveWorkFlow($putproductionInfo, $allReviewerInfo, $putproductionInfo->version);
            }*/
            //从待提交到提交后进入待cm处理环节(提交默认处理通过)
            $dealResult = '1';
            $dealUser = $this->app->user->account;
            $this->submit($putproductionId, $dealUser, $dealResult, $postData->remark);
        }else{
            //添加状态流转
            if($status == $oldStatus){
                $lastConsumedInfo = $this->loadModel('consumed')->getLastConsumed($putproductionId, 'putproduction');
                if($lastConsumedInfo && ($lastConsumedInfo->before == $lastConsumedInfo->after) && ($status == $lastConsumedInfo->after)){
                    $this->loadModel('consumed')->update($lastConsumedInfo->id, $lastConsumedInfo->objectType, $putproductionId, '0', $this->app->user->account,
                        $lastConsumedInfo->before, $lastConsumedInfo->after);
                }else{
                    $this->loadModel('consumed')->record('putproduction', $putproductionId, '0', $this->app->user->account, $oldStatus, $status);
                }
            }else{
                $this->loadModel('consumed')->record('putproduction', $putproductionId, '0', $this->app->user->account, $oldStatus, $status);
            }
        }

        $this->tryError(1); //检查报错 1= 需要rollback
        $this->dao->commit(); //调试完逻辑最后开启事务

        //返回
        return common::createChanges($putproductionInfo, (Object)$postData);
    }

    /**
     * 保存工作流信息
     *
     * @param $putproductionInfo
     * @return bool
     */
    public function saveWorkFlow($putproductionInfo, $allReviewerInfo, $version){
        $res = false;
        $objectType = $this->config->putproduction->objectType;
        $putproductionId = $putproductionInfo->id;
        $res = $this->loadModel('iwfp')->startWorkFlow($objectType, $putproductionId, $putproductionInfo->code, $putproductionInfo->createdBy, $allReviewerInfo, $version, $this->lang->putproduction->reviewNodeCodeNameList, $putproductionInfo->workflowId);
        if(!dao::isError()){
            $processInstanceId = $res->processInstanceId;
            $updateParams = new stdClass();
            $updateParams->workflowId = $processInstanceId;
            $this->dao->update(TABLE_PUTPRODUCTION)->data($updateParams)->autoCheck()
                ->where('id')->eq($putproductionId)
                ->exec();
        }
        if(!dao::isError()){
            $res = true;
        }
        return $res;
    }

    /**
     * @Notes:指派
     * @Date: 2024/1/9
     * @Time: 16:01
     * @Interface assignment
     * @param $id
     * @return array
     */
    public function assignment($id)
    {
        $oldData = $this->getByID($id);
        $data = fixer::input('post')
            ->remove('remark,uid')
            ->get();

        if(empty($data->dealUser)){
            dao::$errors[] =  $this->lang->putproduction->assignToEmpty;
            return;
        }


        $dealUserList = explode(',', $oldData->dealUser);
        if(!in_array($this->app->user->account, $dealUserList)){
            dao::$errors[] =  $this->lang->putproduction->dealUserEmpty;
            return;
        }
        $newDealUser = array();
        foreach ($dealUserList as $dealUser){
            if($dealUser == $this->app->user->account){
                array_push($newDealUser, $data->dealUser);
            }else{
                array_push($newDealUser, $dealUser);
            }
        }
        $this->loadModel('iwfp')->changeAssigneek($oldData->workflowId, $this->app->user->account, $data->dealUser,$oldData->version);

        $this->dao->update(TABLE_PUTPRODUCTION)->set('dealUser')->eq(implode(',', $newDealUser))->where('id')->eq($id)->exec();

        if (!dao::isError()) return common::createChanges($oldData, $data);
    }


    /**
     * @Notes:取消
     * @Date: 2024/1/19
     * @Time: 14:58
     * @Interface cancel
     * @param $info
     */
    public function cancel($info)
    {
        $postData = fixer::input('post')->get();
        //取消原因必填
        if(empty($postData->remark)){
            dao::$errors[] =  $this->lang->putproduction->remarkEmpty;
            return;
        }

        //不可重复取消
        if($info->status == 'cancel'){
            dao::$errors[] =  $this->lang->putproduction->repeatCancel;
            return;
        }
        //同步出去之后当前节点在金信取消时弹窗提示
        if(in_array($info->status,$this->lang->production->outsideStatusList))
        {
            dao::$errors[] =  $this->lang->putproduction->cantCancelTip;
            return;
        }

        $data = new stdClass();
        $data->dealUser     = '';
        $data->status     = 'cancel';
        $data->cancelBy   = $this->app->user->account;
        $data->cancelDate = helper::now();
        $data->cancelReason = $postData->remark;

        $res = $this->dao->update(TABLE_PUTPRODUCTION)->data($data)->where('id')->eq($info->id)->exec();
        return $res;
    }

    /**
     * 获得格式化post数据
     *
     * @param $postData
     * @return mixed
     */
    public function getFormatPostData($postData){
        $requiredFields = explode(',', $this->config->putproduction->create->requiredFields);
        foreach ($requiredFields as $requiredField){
            if(!isset($postData->$requiredField)){
                $postData->$requiredField = '';
            }
        }
        foreach ($this->config->putproduction->multipleValFields as $multipleValField){
            if(isset($postData->$multipleValField)){
                $postData->$multipleValField = trim($postData->$multipleValField, ','); //去掉左右","
            }else{
                $postData->$multipleValField = '';
            }

        }

        return $postData;
    }


    /**
     * 尝试报错 或需要rollback
     *
     * @param int $rollBack
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
     *设置投产单号
     *
     * @return string
     */
    private function getCode(){
        $number = $this->dao->select('count(id) c')->from(TABLE_PUTPRODUCTION)->where('code')->like('CFIT-TJ-' . date('Ymd-')."%")->fetch('c') ;
        $number = intval($number) + 1;
        $code   = 'CFIT-TJ-' . date('Ymd-') . sprintf('%02d', $number);
        return $code;
    }

    /**
     *创建投产信息
     *
     * @param $data
     * @return mixed
     */
    private function createPutProduction($data){
        $data = (object)$data;
        return $this->dao->insert(TABLE_PUTPRODUCTION)
            ->data($data)
//            ->checkIF($data->isBusinessCoopera == '2','businessCooperaContent','notempty')
//            ->checkIF($data->isBusinessAffect == '2','businessAffect','notempty')
            ->batchCheckIF($_POST['issubmit'] != 'save',$this->config->putproduction->create->requiredFields, 'notempty')
            ->exec();
    }

    /**
     * 变更信息
     *
     * @param $id
     * @param $data
     * @return mixed
     */
    private function updatePutProduction($id, $data){
        $data = (object)$data;
        $res = $this->dao->update(TABLE_PUTPRODUCTION)
            ->data($data)
//            ->checkIF($data->isBusinessCoopera == '2','businessCooperaContent','notempty')
//            ->checkIF($data->isBusinessAffect == '2','businessAffect','notempty')
            ->batchCheckIF($_POST['issubmit'] != 'save',$this->config->putproduction->edit->requiredFields, 'notempty')
            ->where('id')->eq((int)$id)->exec();
        return $res;
    }

    /**
     * 检查基本信息
     *
     * @param $params
     * @param op
     * @return bool
     */
    public function checkBasicInfo($params, $op = 'create', $putproductionId = 0){
        //检查结果
        $checkRes = false;
        $errorData = [];
        $requiredFields = explode(',', $this->config->putproduction->$op->requiredFields);
        foreach ($requiredFields as $requiredField){
            if(!isset($params->$requiredField) || empty($params->$requiredField)){
                $errorData[$requiredField] = sprintf($this->lang->error->notempty,$this->lang->putproduction->$requiredField);
            }
        }

        //关联内部项目
        $inProjectList = $this->loadModel('projectplan')->getPlanByOutID($params->outsidePlanId);
        if(!$params->inProjectIds){
            if(!empty($inProjectList)){
                $errorData['inProjectIds'] = sprintf($this->lang->putproduction->emptyObject, $this->lang->putproduction->inProjectIds);
            }
        }else{
            $allInProjectIds = array_column($inProjectList, 'id');
            $inProjectIds = explode(',', $params->inProjectIds);
            $diffInProjectIds = array_diff($inProjectIds, $allInProjectIds);
            if(!empty($diffInProjectIds)){
                $errorData['inProjectIds'] = $this->lang->putproduction->inProjectIdsError;
            }
        }
        //关联需求条目检查
        if($params->demandId){
            //关联需求条目 需判断条目所属需求任务是否外部已删除，如果外部已删除则不允许关联
            $deleteOutDataStr =  $this->loadModel('requirement')->getRequirementInfos($params->demandId);
            if(!empty($deleteOutDataStr)) {
                $errorData['demandId'] = sprintf($this->lang->putproduction->deleteOutTip , $deleteOutDataStr);
            }

            $demandIds = explode(',', $params->demandId);
            $demandList = $this->loadModel('demand')->getAllowPutProductionDemandList($putproductionId, $demandIds);
            $notAllowDemandIds = array_diff($demandIds, array_keys($demandList));
            if(!empty($notAllowDemandIds)){
                $usedDemandList = $this->loadModel('demand')->getPairsByIds($notAllowDemandIds);
                $usedDemandList = (array)$usedDemandList;
                $demandTitleList = array_column($usedDemandList, 'formatTitle');
                $errorData['demandId'] = sprintf($this->lang->putproduction->demandIdError, implode('、', $demandTitleList));

            }
        }



        $stageArray = explode(',', $params->stage);
        if((count($stageArray) == 1) && (in_array('1', $stageArray))){ //仅仅包含第一阶段
            if(!trim($params->fileUrlRevision)){
                $errorData['fileUrlRevision'] = $this->lang->putproduction->fileUrlRevisionEmpty;
            }
        }

        if(in_array('2', $stageArray)){ //包含第二阶段
            if(!$params->dataCenter){
                $errorData['dataCenter'] = $this->lang->putproduction->dataCenterEmpty;

            }
            if(!$params->isPutCentralCloud){
                $errorData['isPutCentralCloud'] = $this->lang->putproduction->isPutCentralCloudEmpty;
            }
            if(!$params->isBusinessCoopera){
                $errorData['isBusinessCoopera'] = $this->lang->putproduction->isBusinessCooperaEmpty;

            }
            if(!$params->isBusinessAffect){
                $errorData['isBusinessAffect'] = $this->lang->putproduction->isBusinessAffectEmpty;

            }
            if(!in_array('1', $stageArray)){ //仅仅包含第二阶段，需要设置第一阶段移交单
                if(!$params->firstStagePid){
                    $errorData['firstStagePid'] =  sprintf($this->lang->error->notempty, $this->lang->putproduction->firstStagePid);
                }
            }
        }
        
        if($params->firstStagePid){ //如果设置了第一阶段投产移交单，判断是否有效
            $exWhere = " status = 'filepass'";
            $ret = $this->getOnlyFirstStagePutProductionList($exWhere, $params->firstStagePid);
            if(!$ret){
                $errorData['firstStagePid'] =  $this->lang->putproduction->firstStagePidError;

            }
        }

        if($params->isBusinessCoopera == '2'){
            if(empty($params->businessCooperaContent)){
                $errorData['businessCooperaContent'] =  $this->lang->putproduction->businessCooperaContentEmpty;

            }
        }

        if($params->isBusinessAffect == '2'){
            if(empty($params->businessAffect)){
                $errorData['businessAffect'] =  $this->lang->putproduction->businessAffectEmpty;

            }
        }
        if($errorData){
            dao::$errors = $errorData;
        }else{
            $checkRes = true;
        }
        return $checkRes;
    }


    /**
     * 检查审核节点的审核人
     *
     * @param $nodes
     * @param $requiredReviewerNodes
     * @return bool
     */
    public function checkReviewerNodesInfo($nodes, $requiredReviewerNodes){
        //检查结果
        $checkRes = false;
        if(!$nodes){
            dao::$errors[] = $this->lang->putproduction->reviewerEmpty;
            return $checkRes;
        }

        $nodeKeys = array();
        foreach($nodes as $key => $currentNodes) {
            //去除空元素
            $currentNodes = array_filter($currentNodes);
            if(!empty($currentNodes))
            {
                $nodeKeys[] = $key;
                $nodes[$key] = $currentNodes;
            }
        }

        //必选审核人，却没有选
        $diffKeys = array_diff($requiredReviewerNodes, $nodeKeys);
        $errorData = [];
        if(!empty($diffKeys)){
            foreach ($diffKeys as  $nodeKey){
                $reviewerNode = $this->lang->putproduction->reviewNodeCodeNameList[$nodeKey];
                $nodeCodeName = zget($this->lang->putproduction->reviewNodeCodeList, $reviewerNode);
                $errorData[$nodeCodeName] = $reviewerNode. $this->lang->putproduction->reviewerEmpty;
            }
        }
        if($errorData){
            dao::$errors = $errorData;
        }

        if(dao::isError()){
            return $checkRes;
        }
        $checkRes = true;
        return $checkRes;
    }


    /**
     * 获得格式化的审核节点以及审核人信息
     *
     * @param $nodes
     * @param $requiredReviewerNodes
     * @param $data
     * @return array
     */
    private function getFormatReviewerInfo($nodes, $requiredReviewerNodes, $data = []){
        if(!($nodes)){
            return $data;
        }
        foreach($nodes as $key => $currentNodes) {
            //去除空元素
            $currentNodes = array_filter($currentNodes);
            if(!empty($currentNodes)) {
                $nodes[$key] = array_values($currentNodes); //重新排序
            }
        }

        foreach ($nodes as $key => $currentNodes){
            if(in_array($key, $requiredReviewerNodes)){
                $data[$key] = $currentNodes;
            }else{
                $data[$key] = [];
            }
        }
        return $data;
    }

    /**
     * 获得审核人信息
     *
     * @param int $deptId
     * @return array
     */
    public function getReviewers($deptId = 0){
        $reviewers = [];
        $users = $this->loadModel('user')->getPairs('noletter|noclosed');
        if (!$deptId) {
            $deptId = $this->app->user->dept;
        }
        $deptInfo = $this->loadModel('dept')->getByID($deptId);
        //质量部cm
        $currentUsers = explode(',', trim($deptInfo->cm, ','));
        $tempUsers  = array('' => '');
        if(!empty($currentUsers)){
            foreach($currentUsers as $account)
            {
                $tempUsers[$account] = zget($users, $account);
            }
        }
        $reviewers[$this->lang->putproduction->reviewNodeCodeList['waitcm']] = $tempUsers;

        //部门负责人
        $currentUsers = explode(',', trim($deptInfo->manager, ','));
        $tempUsers  = array('' => '');
        if(!empty($currentUsers)){
            foreach($currentUsers as $account)
            {
                $tempUsers[$account] = zget($users, $account);
            }
        }
        $reviewers[$this->lang->putproduction->reviewNodeCodeList['waitdept']] = $tempUsers;

        //部门分管领导
        $currentUsers = explode(',', trim($deptInfo->leader, ','));
        $tempUsers  = array('' => '');
        if(!empty($currentUsers)){
            foreach($currentUsers as $account)
            {
                $tempUsers[$account] = zget($users, $account);
            }
        }
        $reviewers[$this->lang->putproduction->reviewNodeCodeList['waitleader']] = $tempUsers;

        // 总经理

        $depts = $this->loadModel('dept')->getRecurveSubDeptIds(30);
        if (in_array($this->app->user->dept,$depts)){
            $this->app->loadConfig('modify');
            // 上海分公司特殊处理
            $reviewers[$this->lang->putproduction->reviewNodeCodeList['waitgm']] = [$this->config->modify->branchManagerList => $users[$this->config->modify->branchManagerList]];
        }else{
            $account = $this->dao->select('account')->from(TABLE_USER)->where('role')->eq('ceo')->fetch('account');
            $reviewers[$this->lang->putproduction->reviewNodeCodeList['waitgm']] = array($account => $users[$account]);
        }

        //产创二线专员
        $currentUsers = explode(',', trim($deptInfo->executive, ','));
        $tempUsers  = array('' => '');
        if(!empty($currentUsers)){
            foreach($currentUsers as $account)
            {
                $tempUsers[$account] = zget($users, $account);
            }
        }
        $reviewers[$this->lang->putproduction->reviewNodeCodeList['waitproduct']] = $tempUsers;
        return $reviewers;
    }

    /**
     * 获取投产使用的需求条目id
     *
     * @param int $ignorePutProductionId
     * @return array
     */
    public function getUsedByPutProductionDemandIds($ignorePutProductionId = 0){
        $demandIds = [];
        $ret = $this->dao->select('id,demandId')
            ->from(TABLE_PUTPRODUCTION)
            ->where('deleted')->eq('0')
            ->andWhere("status")->notin($this->lang->putproduction->finalStatusArray)
            ->andWhere(" FIND_IN_SET(2,stage)")
            ->beginIF($ignorePutProductionId > 0)->andWhere('id')->ne($ignorePutProductionId)->fi()
            ->fetchPairs();
        if(!empty($ret)){
            $ret = explode(',', implode(',', $ret));
            $demandIds = array_values(array_filter($ret));
            $demandIds = array_flip(array_flip($demandIds));
        }
        return $demandIds;
    }

    /**
     * 获得仅仅是第一阶段的投产移交单
     *
     * @param $exWhere
     * @param $putProductionId
     * @return array
     */
    public function getOnlyFirstStagePutProductionList($exWhere = '', $putProductionId = 0){
        $data = [];
        $ret = $this->dao->select('id, CASE WHEN `desc` != "" THEN concat(concat(code,"（"), concat(`desc`, "）")) ELSE code END as code')
            ->from(TABLE_PUTPRODUCTION)
            ->where('deleted')->eq('0')
            ->andWhere(" FIND_IN_SET(1,stage)")
            ->andWhere(" NOT FIND_IN_SET(2,stage)")
            ->beginIF($putProductionId > 0)->andWhere('id')->eq($putProductionId)->fi()
            ->beginIF(!empty($exWhere))->andWhere($exWhere)->fi()
            ->orderBy('id_desc')
            ->fetchPairs();
        if($ret){
            $data = $ret;
        }
        return $data;
    }

    /**
     * 检查操作权限
     *
     * @param $putproduction
     * @param $action
     * @return bool|mixed
     */
    public static function isClickable($putproduction, $action){
        global $app;
        $action = strtolower($action);
        if($putproduction->deleted || is_null($putproduction->deleted)){
            return false;
        }
        $account = $app->user->account;
        $putproductionModel = new putproductionModel();

        if($action == 'edit') {
            $res = $putproductionModel->checkIsAllowEdit($putproduction, $account);
            return $res['result'];
        }
        if($action == 'submit') {
            $res = $putproductionModel->checkIsAllowSubmit($putproduction, $account);
            return $res['result'];
        }
        //删除 创建人且待提交状态
        if ($action == 'delete')    return $app->user->account == 'admin' or (in_array($putproduction->status,array('waitsubmit')) and $app->user->account == $putproduction->createdBy);
        //指派 待处理人 且 待CM处理、待部门负责人审批、待分管领导审批、待总经理审批、待产创二线专员处理、内部未通过、外部退回
        if ($action == 'assignment')    return $app->user->account == 'admin' or (in_array($putproduction->status,array('waitcm','waitdept','waitleader','waitgm','waitproduct','reject','externalreject')) and in_array($app->user->account, explode(',', $putproduction->dealUser)));
        //审批
        if ($action == 'review')    return ($app->user->account == 'admin' or in_array($app->user->account, explode(',', $putproduction->dealUser))) and in_array($putproduction->status,array('waitcm','waitdept','waitleader','waitgm','waitproduct'));
        //取消
        if ($action == 'cancel')   return $putproductionModel->checkCancelAuth($putproduction, $account);
        //取消
        if ($action == 'repush')    return ($app->user->account == 'admin' or in_array($app->user->account, explode(',', $putproduction->dealUser))) and in_array($putproduction->status,array('syncfailed','filereturn'));
        return true;
    }


    /**
     * 检查是否允许编辑
     *
     * @param $putproduction
     * @param $account
     * @return array
     */
    public function checkIsAllowEdit($putproduction, $account){
        $res = array(
            'result'  => false,
            'message' => '',
        );
        if(empty((array)$putproduction)){
            $res['message'] = $this->lang->common->errorParamId;
            return $res;
        }

        //当前状态
        $status = $putproduction->status;

        if(!in_array($status, $this->lang->putproduction->allowEditStatusArray)){
            $statusDesc = zget($this->lang->putproduction->statusList, $status);
            $res['message'] = sprintf($this->lang->putproduction->checkOpResultList['statusError'], $statusDesc, $this->lang->putproduction->edit);
            return $res;
        }
        $createdBy = $putproduction->createdBy;
        $users = [$createdBy, 'admin'];
        if(!in_array($account, $users)){
            $res['message'] = sprintf($this->lang->putproduction->checkOpResultList['userError'], $this->lang->putproduction->edit);
            return $res;
        }
        $res['result'] = true;
        return $res;
    }

    /**
     * 检查是否允许编辑
     *
     * @param $putproduction
     * @param $account
     * @return array
     */
    public function checkIsAllowSubmit($putproduction, $account){
        $res = array(
            'result'  => false,
            'message' => '',
        );
        if(empty((array)$putproduction)){
            $res['message'] = $this->lang->common->errorParamId;
            return $res;
        }

        //当前状态
        $status = $putproduction->status;

        if(!in_array($status, $this->lang->putproduction->allowSubmitStatusArray)){
            $statusDesc = zget($this->lang->putproduction->statusList, $status);
            $res['message'] = sprintf($this->lang->putproduction->checkOpResultList['statusError'], $statusDesc, $this->lang->putproduction->submit);
            return $res;
        }
        $dealUser = $putproduction->dealUser;
        $users = array_filter(explode(',',$dealUser));

        $users[]  = 'admin';
        if(!in_array($account, $users)){
            $res['message'] = sprintf($this->lang->putproduction->checkOpResultList['userError'], $this->lang->putproduction->submit);
            return $res;
        }
        $res['result'] = true;
        return $res;
    }

    /**
     * @Notes:校验取消权限
     * @Date: 2024/1/19
     * @Time: 16:22
     * @Interface checkCancelAuth
     * @param $putproduction
     * @param $account
     */
    public function checkCancelAuth($putproduction,$account)
    {
        $res = false;
        //后台自定义 任何状态 通admin
        if($account == 'admin' or in_array($account,$this->lang->putproduction->outsideStatusList)) $res = true;
        //创建人 待CM处理、待部门负责人审批、待分管领导审批、待总经理审批、待二线专员审批、内部未通过
        if($account == $putproduction->createdBy and in_array($putproduction->status,array('waitcm','waitdept','waitleader','waitgm','waitproduct','reject'))) $res = true;
        return $res;
    }


    /**
     * sendmail
     *
     * @param  int    $putproductionID
     * @param  int    $actionID
     * @access public
     * @return void
     */
    public function sendmail($putproductionID, $actionID)
    {
        $this->loadModel('mail');
        $putproduction   = $this->getById($putproductionID);
        $users = $this->loadModel('user')->getPairs('noletter');

        /* 获取后台通知中配置的邮件发信。*/
        $this->app->loadLang('custommail');
        $mailConf   = isset($this->config->global->setPutproductionMail) ? $this->config->global->setPutproductionMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf   = json_decode($mailConf);

        if($putproduction->status == 'cancel')
        {
            $mailTitle  = $this->lang->putproduction->noticeTitle;
        }else{
            $mailTitle  = vsprintf($mailConf->mailTitle, $mailConf->variables);
        }

        /* Get action info. */
        $action          = $this->loadModel('action')->getById($actionID);
        $history         = $this->action->getHistory($actionID);
        $action->history = isset($history[$actionID]) ? $history[$actionID] : array();

        /* Get mail content. */
        $modulePath = $this->app->getModulePath($appName = '', 'putproduction');
        $oldcwd     = getcwd();
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

        $sendUsers = $this->getToAndCcList($putproduction);
        if(!$sendUsers) return;
        list($toList, $ccList) = $sendUsers;
        $subject = $mailTitle;
        /* Send mail. */
        $this->mail->send($toList, $subject, $mailContent, $ccList);
        if($this->mail->isError()) trigger_error(join("\n", $this->mail->getError()));
    }

    /**
     * @Notes:喧喧
     * @Date: 2024/1/10
     * @Time: 17:53
     * @Interface getXuanxuanTargetUser
     * @param $obj
     * @param $objectType
     * @param $objectID
     * @param $actionType
     * @param $actionID
     * @param string $actor
     * @return array|false
     */
    public function getXuanxuanTargetUser($obj,$objectType, $objectID, $actionType, $actionID, $actor = ''){
        $info   = $this->getById($objectID);
        $sendUsers = $this->getToAndCcList($info);

        if(!$sendUsers) return;
        $toList = $sendUsers[0];

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
        $subcontent['name']         = $obj->code;//消息体 编号后边位置 标题

        //标题
        $title = '';
        $actions = [];

        return ['toList'=>$toList,'subcontent'=>$subcontent,'url'=>$url,'title'=>$title,'actions'=>$actions];
    }

    /**
     * Get toList and ccList.
     *
     * @param object $putproduction
     * @access public
     * @return bool|array
     */
    public function getToAndCcList($putproduction)
    {
        /* Set toList and ccList. */
        /* 初始化发信人和抄送人变量，获取发信人和抄送人数据。*/
        $toList = '';
        $ccList = '';
        //待CM处理、待部门审批、待分管领导审批、待总经理审批、待产创处理、材料退回、外部退回、内部未通过
        if(in_array($putproduction->status,$this->lang->putproduction->sendmailStatusList))
        {
            $toList = $putproduction->dealUser;
        }
        //推送失败 主送二线人员+投产单创建人，+抄送后台可配
        $executiveList = $this->loadModel('dept')->getExecutiveUser();//二线专员
        $createdBy = [$putproduction->createdBy];

        if($putproduction->status == 'syncfailed')
        {
            $toList = implode(',',array_filter(array_unique(array_merge($executiveList,$createdBy))));
        }

        //取消发送通知邮件 给所有处理过的人发送
        if($putproduction->status == 'cancel')
        {
            $nodes = $this->loadModel('iwfp')->getAllVersionReviewNodes($putproduction->workflowId);
            $ccListArray = [];

            //数组解析
            foreach ($nodes as $node)
            {
                foreach ($node as $key => $detail)
                {
                    if(!empty($detail['result']))
                    {
                        $ccListArray[] = implode(',',$detail['toDealUser']);
                    }
                }
            }

            $ccListArray = implode(',',($ccListArray));
            $ccListArray = array_filter(array_unique(explode(',',$ccListArray)));
            $ccList = implode(',',$ccListArray);
        }

        return array($toList, $ccList);
    }

    /**
     * @Notes:获取自定义字段
     * @Date: 2024/1/9
     * @Time: 16:30
     * @Interface getDefineFieldByID
     * @param $id
     * @param $field
     */
    public function getDefineFieldByID($id,$field='*')
    {
        return $this->dao->select($field)->from(TABLE_PUTPRODUCTION)->where('id')->eq($id)->andWhere('deleted')->eq('0')->fetch();
    }

    public function review($putproductionInfo, $ismobile = false){
        $updateData = array();
        if($putproductionInfo->status == 'cancel'){
            dao::$errors[''] = sprintf($this->lang->putproduction->checkOpResultList['statusError'], $this->lang->putproduction->statusList['cancel'], $this->lang->putproduction->review);
            return false;
        }
        if(empty($_POST['dealResult']))
        {
            dao::$errors['dealResult'] = $this->lang->putproduction->resultError;
            return false;
        }
        if($this->post->dealResult == '2' && empty($_POST['dealMessage']))
        {
            dao::$errors['dealMessage'] = $this->lang->putproduction->suggestError;
            return false;
        }
        if($this->post->dealResult == '1' && $putproductionInfo->status == 'waitcm')
        {
            if($putproductionInfo->stage == '1'){
                $sftpList = array();
                $sftpDataList = $_POST['sftpPath'];
                foreach ($sftpDataList as $sftpData){
                    if(empty($sftpData)){
                        dao::$errors[] = $this->lang->putproduction->sftpError;
                        return false;
                    }
                    if (substr($sftpData, -4) !=='.zip'){
                        dao::$errors[] = $this->lang->putproduction->sftpFormat;
                        return false;
                    }
                    $this->checkRemoteFile($sftpData);
                    if(dao::isError()) {
                        return false;
                    }
                    array_push($sftpList, $sftpData);
                }
                $updateData['sftpPath'] = json_encode($sftpList);
                $updateData['openFile'] = 'true';
            }else{
                $releaseId = implode(',',$_POST['releaseId']);
                if(empty($releaseId)){
                    dao::$errors['releaseId'] = $this->lang->putproduction->releaseIdError ;
                    return false;
                }
                foreach ($_POST['releaseId'] as $release){
                    if(!empty($release)){
                        $releaseObj = $this->loadModel("release")->getByID($release);
                        $remoteFileStr = $releaseObj->path;
                        if(empty($remoteFileStr)){
                            dao::$errors[] = '【'.$releaseObj->name.'】'.$this->lang->putproduction->releaseFileError;
                            return false;
                        }
                        if (substr($remoteFileStr, -4) !=='.zip'){
                            dao::$errors[] = '【'.$releaseObj->name.'】'.$this->lang->putproduction->releaseFormat;
                            return false;
                        }
                        $this->checkRemoteFile($remoteFileStr);
                        if(dao::isError()) {
                            return false;
                        }
                    }
                }
                $updateData['releaseId'] = $releaseId;
            }
        }
        $userVariableList = new stdClass();
        $userVariableList->level = $putproductionInfo->level;
        $res = $this->loadModel('iwfp')->completeTaskWithClaim($putproductionInfo->workflowId, $this->app->user->account, $_POST['dealMessage'], $_POST['dealResult'], $userVariableList,$putproductionInfo->version);
        if(dao::isError()){
            return false;
        }
        $updateData['status'] = $res->toXmlTask;
        $updateData['dealUser'] = is_array($res->dealUser) ? implode(',', $res->dealUser):$res->dealUser;
        $updateData['pushStatus'] = 0;
        $this->dao->update(TABLE_PUTPRODUCTION)->data($updateData)->where('id')->eq($putproductionInfo->id)->exec();
        $commit = $this->lang->putproduction->dealResult.'：'.$this->lang->putproduction->dealResultList[$_POST['dealResult']].'<br>'.$this->lang->putproduction->dealMessage.'：'.$_POST['dealMessage'];
        $this->loadModel('consumed')->record('putproduction', $putproductionInfo->id, '0', $this->app->user->account, $putproductionInfo->status, $res->toXmlTask);
        if($ismobile){
            $actionID = $this->loadModel('action')->create('putproduction', $putproductionInfo->id, 'reviewed', $commit,'mobile');
        }else{
            $actionID = $this->loadModel('action')->create('putproduction', $putproductionInfo->id, 'reviewed', $commit);
        }
    }

    //校验sftp文件夹是否存在和遍历文件
    function checkRemoteFile($remoteFile){
        //线上环境sftp是从/ftpdatas开始，过滤掉/ftpdatas
        if(strpos($remoteFile,'/ftpdatas') === 0) {
            $remoteFile = substr($remoteFile,9);
        }
        $config         = $this->dao->select('`key`,`value`')->from(TABLE_LANG)->where('section')->eq('sftpList')->fetchPairs('key');
        $conn = ssh2_connect($config['host'], $config['port']);   //登陆远程服务器
        //用户名密码验证
        if(!ssh2_auth_password($conn, $config['username'], $config['password'])) {
            dao::$errors['path'] = 'sftp用户名密码配置错误';
            return false;
        }
        $sftp           = ssh2_sftp($conn);                     //打开sftp
        //将文件名中文转码
        //$fileInfoArr = explode('/', $remoteFile);
        //$zipName = end($fileInfoArr);
        //$remoteFile = iconv('UTF-8','GB2312',$remoteFile);
        //检查文件夹是否存在
        $resource = "ssh2.sftp://{$sftp}" . $remoteFile;    //远程文件地址md5
        if (!file_exists($resource)) {
            dao::$errors['path'] =  '文件在sftp上不存在';
            return false;
        }
        //检查md5文件存不存在
        $arr = explode('.', $remoteFile);
        $ext = end($arr);
        $extLen = strlen($ext);
        $localFileMd5 = substr($remoteFile, 0, -$extLen) . 'md5';
        $resource = "ssh2.sftp://{$sftp}" . $localFileMd5;    //远程文件地址md5
        if (!file_exists($resource)) {
            $arr = explode('.', $remoteFile);
            $ext = end($arr);
            $extLen = strlen($ext);
            $localFileMd5 = substr($remoteFile, 0, -$extLen) . 'org';
            $resource = "ssh2.sftp://{$sftp}" . $localFileMd5;    //远程文件地址md5
            if (!file_exists($resource)) {
                $arr = explode('/', $remoteFile);
                $arr[sizeof($arr)-1]='md5.org';
                $localFileMd5 =  rtrim(implode('/',$arr),'/');
                $resource = "ssh2.sftp://{$sftp}" . $localFileMd5;    //远程文件地址md5
                if (!file_exists($resource)) {
                    dao::$errors['path'] =  'md5文件在sftp上不存在';
                    return false;
                }
            }
        }
    }

    public function workloadedit($putproductionInfo,$consumedId){
        $consumed = fixer::input('post')->remove('comment')->get();
        $isLast = $this->loadModel('consumed')->checkIsLastConsumed($consumedId, $putproductionInfo->id, 'putproduction');
        if($this->post->before == ''){
            $errors['before'] = sprintf($this->lang->putproduction->emptyObject, $this->lang->consumed->before);
            return dao::$errors = $errors;
        }
        if($this->post->after == ''){
            $errors['after'] = sprintf($this->lang->putproduction->emptyObject, $this->lang->consumed->before);
            return dao::$errors = $errors;
        }
        if($isLast){
            $response = $this->loadModel('iwfp')->freeJump($putproductionInfo->workflowId, $this->post->after,$putproductionInfo->version);
            if(dao::isError()){
                return false;
            }
            $updateData = array();
            $updateData['dealUser'] = implode(',', $response->dealUser);
            $updateData['status'] = $response->status;
            $this->dao->update(TABLE_PUTPRODUCTION)->data($updateData)->where('id')->eq($putproductionInfo->id)->exec();
            $this->dao->update(TABLE_CONSUMED)->data($consumed)->where('id')->eq($consumedId)->exec();
        }else{
            $this->dao->update(TABLE_CONSUMED)->data($consumed)->where('id')->eq($consumedId)->exec();
        }
    }

    /**
     * 获取未发送的投产单
     * @return array
     */
    public function getUnPushedAndPush(){
        $unPushedIds = $this->dao->select('*')->from(TABLE_PUTPRODUCTION)->where('status')->eq('waitdelivery')->fetchALl('id');  //选取没推送成功的产品登记
        if(empty($unPushedIds)) return [];
        $res = [];
        foreach ($unPushedIds as $unPushedId)
        {
            //文件同步失败
            if($unPushedId->pushStatus == '5'){
                $res = $this->loadModel('iwfp')->completeTaskWithClaim($unPushedId->workflowId, 'guestjk', $unPushedId->putFileFailReason , '2', '',$unPushedId->version);
                if(dao::isError()){
                    return false;
                }
                $updateData['status'] = $res->toXmlTask;
                $updateData['dealUser'] = is_array($res->dealUser) ? implode(',', $res->dealUser):$res->dealUser;
                $updateData['pushFailTimes'] = 0;
                $this->dao->update(TABLE_PUTPRODUCTION)->data($updateData)->where('id')->eq($unPushedId->id)->exec();
                $this->loadModel('action')->create('putproduction',$unPushedId->id, 'jxsyncfail', "推送文件失败".$unPushedId->putFileFailReason);
                $this->loadModel('consumed')->record('putproduction', $unPushedId->id, 0, 'guestjk', 'waitdelivery', $res->toXmlTask, array(), '');
            }
            //文件同步成功
            if($unPushedId->pushStatus == '4'){
                if(!empty($unPushedId->firstStagePid)){
                    $firstStagePid = $this->getByID($unPushedId->firstStagePid);
                    if($firstStagePid->status != 'filepass'){
                        //第一阶段的单子没有到终态，不允许推送二阶段
                        $updateData['pushFailReason'] = '第一阶段的单子没有到终态，不允许推送二阶段';
                        $this->dao->update(TABLE_PUTPRODUCTION)->data($updateData)->where('id')->eq($unPushedId->id)->exec();
                        continue;
                    }
                }
                $response = $this->pushPutproduction($unPushedId->id);
                $response = json_decode($response);
                $run['putproductionId']    = $unPushedId->id;
                $run['response']            = $response;

                $res[] = $run;
            }
        }
        return $res;
    }

    /**
     * 变更流程发起接口
     */
    public function pushPutproduction($id)
    {
        $this->loadModel('requestlog');
        /* 获取生产变更单 */
        $putproduction = $this->getByID($id);
        $pushEnable = $this->config->global->pushPutproductionEnable;
        //判断请求配置是否可用
        if ($pushEnable == 'enable') {
            $users = $this->loadModel('user')->getPairs('noletter');
            $outsideProjectList = $this->loadModel('outsideplan')->getPairs();
            $appList =  $this->loadModel('application')->getapplicationNamePairs();
            $appCodeList =  $this->loadModel('application')->getapplicationCodePairs();

            $products = $this->dao->select('*')->from(TABLE_PRODUCT)->where('deleted')->eq(0)->fetchAll('id');
            $productList =  array_column($products, 'name' , 'id');
            $productCodeList =  array_column($products, 'code' , 'id');

            $url = $this->config->global->pushPutproductionUrl;

            $pushAppId = $this->config->global->pushPutproductionAppId;
            $pushAppSecret = $this->config->global->pushPutproductionAppSecret;

            $headers = array();
            $headers[] = 'appId: ' . $pushAppId;
            //$headers[] = 'appSecret: ' . $pushAppSecret;
            $ts = time();
            $headers[] = 'ts: ' . $ts;
            $uuid = $this->create_guid();
            $headers[] = 'nonce: ' . $uuid;
            $sign = md5('appId='.$pushAppId.'&nonce='.$uuid.'&ts='.$ts.'&appSecret='.$pushAppSecret);
            $headers[] = 'sign: ' . $sign;


            $pushData = array();
            //投产单id
            $pushData['numbers'] = $putproduction->code;
            //是否需要业务配合
            $pushData['busCoper'] = zget($this->lang->putproduction->isBusinessCooperaList, $putproduction->isBusinessCoopera);
            //投产期间是否有业务影响
            $pushData['busImpact'] = zget($this->lang->putproduction->isBusinessAffectList, $putproduction->isBusinessAffect);
            //是否投产到央行云环境
            $pushData['cloudEnvir'] = zget($this->lang->putproduction->isPutCentralCloudList, $putproduction->isPutCentralCloud);
            //创建时间
            $pushData['createTime'] = $putproduction->createdDate;
            //创建人
            $pushData['createUser'] = zget($users, $putproduction->createdBy);
            //文件id
            $fileArray = explode(',',trim($putproduction->jxfileId,','));
            $fileList = array();
            foreach ($fileArray as $fileId){
                if(!empty($fileId)){
                    array_push($fileList, $fileId);
                }
            }
            $pushData['fileIdList'] = $fileList;
            //总行系统性信息化项目名称
            $pushData['infoName'] = zget($outsideProjectList, $putproduction->outsidePlanId);
            //投产属性
            $pushData['productAttribute'] = zmget($this->lang->putproduction->propertyList, $putproduction->property);
            //投产数据中心
            $pushData['productData'] = zmget($this->lang->putproduction->dataCenterList, $putproduction->dataCenter);
            //投产级别
            $pushData['productLevel'] = zget($this->lang->putproduction->levelList, $putproduction->level);
            //投产材料所属阶段
            $pushData['productStage'] = zmget($this->lang->putproduction->stageList, $putproduction->stage);
            //投产摘要
            $pushData['productSummary'] = $putproduction->desc;
            //投产系统信息
            $productionSystemReqList = array();
            $appArray = explode(',',trim($putproduction->app, ','));
            foreach ($appArray as $app){
                $productionSystemReq = array();
                $productionSystemReq['cfitKey'] = $app;
                $productionSystemReq['systemCName'] = zmget($appList, $app);
                $productionSystemReq['systemEName'] = zmget($appCodeList, $app);
                /*$productInfoList = array();
                $productArray = explode(',',trim($putproduction->productId,','));
                foreach ($productArray as $productId){
                    $productInfo = array();
                    $productInfo['prod_c_name'] = zget($productList, $productId);
                    $productInfo['prod_e_name'] = zget($productCodeList, $productId);
                    array_push($productInfoList,$productInfo);
                }
                $productionSystemReq['prod'] = $productInfoList;*/
                array_push($productionSystemReqList, $productionSystemReq);
            }

            $pushData['productionSystemReqList'] = $productionSystemReqList;
            //投产材料评审意见
            $pushData['reviewComment'] = $this->clearHtml(strval(htmlspecialchars_decode($putproduction->reviewComment,ENT_QUOTES)));
            //投产材料是否通过评审（评审结论）
            $pushData['reviewConclusion'] = zget($this->lang->putproduction->isReviewList, $putproduction->isReview);
            //业务方如何配合
            $pushData['busCoperDesc'] = $this->clearHtml(strval(htmlspecialchars_decode($putproduction->businessCooperaContent,ENT_QUOTES)));
            //业务影响
            $pushData['busImpactDesc'] = $this->clearHtml(strval(htmlspecialchars_decode($putproduction->businessAffect,ENT_QUOTES)));
            if($putproduction->stage != '1'){
                $firstPutproduction = $this->getByID($putproduction->firstStagePid);
                $pushData['firstStageNumbers'] = $firstPutproduction->code;
            }

            $object = 'putproduction';
            $objectType = 'putproductionInfo';
            $method = 'POST';
            $response = '';
            $status = 'fail';
            $extra = '';
            $result = $this->loadModel('requestlog')->http($url, $pushData, $method, 'json', array(), $headers);
            $userVariableList = new stdClass();
            if (!empty($result)) {
                $resultData = json_decode($result);
                if ($resultData->status == 'success') {
                    $status = 'success';
                    $res = $this->loadModel('iwfp')->completeTaskWithClaim($putproduction->workflowId, 'guestjk', $response->message , '1', $userVariableList,$putproduction->version);
                    if(dao::isError()){
                        return false;
                    }
                    $updateData = array();
                    $updateData['status'] = $res->toXmlTask;
                    $updateData['dealUser'] = is_array($res->dealUser) ? implode(',', $res->dealUser):$res->dealUser;
                    $updateData['pushFailTimes'] = 0;
                    $updateData['externalId'] = 'true';
                    $this->dao->update(TABLE_PUTPRODUCTION)->data($updateData)->where('id')->eq($putproduction->id)->exec();
                    $this->loadModel('action')->create('putproduction',$putproduction->id, 'jxsyncsuccess', $response->message);
                    $this->loadModel('consumed')->record('putproduction', $putproduction->id, 0, 'guestjk', 'waitdelivery', $res->toXmlTask, array(), '');
                    //成功联动需求条目为已交付 第二阶段且待外部审批
                    if(strstr($putproduction->stage,'2'))
                    {
                        /* @var demandModel $demandModel*/
                        $demandModel = $this->loadModel('demand');
                        $dataParams = $this->dao->select("*")->from(TABLE_PUTPRODUCTION)->where('id')->eq($putproduction->id)->fetch();
                        $demandModel->putproductionAndDemandStatusChange($putproduction->demandId,$dataParams,$updateData['status']);
                    }
                }else{
                    $status = 'fail';
                    $updateData = array();
                    if($putproduction->pushFailTimes > 4){
                        $res = $this->loadModel('iwfp')->completeTaskWithClaim($putproduction->workflowId, 'guestjk', $response->description , '2', $userVariableList,$putproduction->version);
                        if(dao::isError()){
                            return false;
                        }
                        $updateData['status'] = $res->toXmlTask;
                        $updateData['dealUser'] = is_array($res->dealUser) ? implode(',', $res->dealUser):$res->dealUser;
                        $updateData['pushFailTimes'] = 0;
                        $this->dao->update(TABLE_PUTPRODUCTION)->data($updateData)->where('id')->eq($putproduction->id)->exec();
                        $this->loadModel('action')->create('putproduction',$putproduction->id, 'jxsyncfail', $response->description);
                        $this->loadModel('consumed')->record('putproduction', $putproduction->id, 0, 'guestjk', 'waitdelivery', $res->toXmlTask, array(), '');
                    }else{
                        $updateData['pushFailTimes'] = $putproduction->pushFailTimes+1;
                        $this->dao->update(TABLE_PUTPRODUCTION)->data($updateData)->where('id')->eq($putproduction->id)->exec();
                        $this->loadModel('action')->create('putproduction',$putproduction->id, 'jxsyncfail', $response->description);
                    }
                }
                $response = $result;
            }else{
                $status = 'fail';
                $updateData = array();
                if($putproduction->pushFailTimes > 4){
                    $res = $this->loadModel('iwfp')->completeTaskWithClaim($putproduction->workflowId, 'guestjk', '网络不通' , '2', $userVariableList,$putproduction->version);
                    if(dao::isError()){
                        return false;
                    }
                    $updateData['status'] = $res->toXmlTask;
                    $updateData['dealUser'] = is_array($res->dealUser) ? implode(',', $res->dealUser):$res->dealUser;
                    $updateData['pushFailTimes'] = 0;
                    $this->dao->update(TABLE_PUTPRODUCTION)->data($updateData)->where('id')->eq($putproduction->id)->exec();
                    $this->loadModel('action')->create('putproduction',$putproduction->id, 'jxsyncfail', '网络不通');
                    $this->loadModel('consumed')->record('putproduction', $putproduction->id, 0, 'guestjk', 'waitdelivery', $res->toXmlTask, array(), '');
                }else{
                    $updateData['pushFailTimes'] = $putproduction->pushFailTimes+1;
                    $this->dao->update(TABLE_PUTPRODUCTION)->data($updateData)->where('id')->eq($putproduction->id)->exec();
                    $this->loadModel('action')->create('putproduction',$putproduction->id, 'jxsyncfail', '网络不通');
                }
            }
            $this->requestlog->saveRequestLog($url, $object, $objectType, $method, $pushData, $response, $status, $extra, $putproduction->id);
        }
        return $response;
    }

    public function create_guid($namespace = '') {
        static $guid = '';
        $uid = uniqid("", true);
        $data = $namespace;
        $data .= $_SERVER['REQUEST_TIME'];
        $data .= $_SERVER['HTTP_USER_AGENT'];
        $data .= $_SERVER['LOCAL_ADDR'];
        $data .= $_SERVER['LOCAL_PORT'];
        $data .= $_SERVER['REMOTE_ADDR'];
        $data .= $_SERVER['REMOTE_PORT'];
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
    public function clearHtml($str){
        $str = trim($str); //清除字符串两边的空格
        $str = strip_tags($str,""); //利用php自带的函数清除html格式
        $str = preg_replace("/\t/","",$str); //使用正则表达式替换内容，如：空格，换行，并将替换为空。
        //$str = preg_replace("/\r\n/","",$str);
        $str = preg_replace("/\r/","",$str);
        //$str = preg_replace("/\n/","",$str);
        return trim($str); //返回字符串
    }


    /**
     *检查信息是否完整
     *
     * @param $putproductionInfo
     * @return array
     */
    public function checkInfoIsIntegrity($putproductionInfo){
        $res = array(
            'result'  => false,
            'message' => '',
        );
        if(!$putproductionInfo){
            return $res;
        }
        $putproductionId = $putproductionInfo->id;
        $checkRes = $this->checkBasicInfo($putproductionInfo, 'edit', $putproductionId);
        if(!$checkRes){
            $res['message'] = dao::$errors;
            return $res;
        }

        $reviewerInfo =  json_decode($putproductionInfo->reviewerInfo, true);
        $requiredReviewerNodes = zget($this->lang->putproduction->requiredReviewerNodes, $putproductionInfo->level);
        $checkRes = $this->checkReviewerNodesInfo($reviewerInfo, $requiredReviewerNodes);
        if(!$checkRes){
            $res['message'] = dao::$errors;
            return $res;
        }
        $res['result'] = true;
        return $res;
    }

    public function repush($putproductionInfo){
        $updateData = array();
        if(empty($_POST['repushResult']))
        {
            dao::$errors['repushResult'] = $this->lang->putproduction->resultError;
            return false;
        }
        $userVariableList = new stdClass();
        $res = $this->loadModel('iwfp')->completeTaskWithClaim($putproductionInfo->workflowId, $this->app->user->account, $_POST['dealMessage'], $_POST['repushResult'], $userVariableList,$putproductionInfo->version);
        if(dao::isError()){
            return false;
        }
        $updateData['status'] = $res->toXmlTask;
        $updateData['dealUser'] = is_array($res->dealUser) ? implode(',', $res->dealUser):$res->dealUser;
        $updateData['pushStatus'] = 0;
        $this->dao->update(TABLE_PUTPRODUCTION)->data($updateData)->where('id')->eq($putproductionInfo->id)->exec();
        $commit = $this->lang->putproduction->dealResult.'：'.$this->lang->putproduction->repushResultList[$_POST['repushResult']].'<br>'.$this->lang->putproduction->dealMessage.'：'.$_POST['dealMessage'];
        $actionID = $this->loadModel('action')->create('putproduction', $putproductionInfo->id, 'deal', $commit);
        $this->loadModel('consumed')->record('putproduction', $putproductionInfo->id, '0', $this->app->user->account, $putproductionInfo->status, $res->toXmlTask);
    }


    /**
     * 编辑退回次次数
     *
     * @param $putproductionID
     * @return array
     */
    public function editReturnCount($putproductionID){
        //工作量验证
        $returnCount = $_POST['returnCount'];
        if($returnCount == '' || $returnCount == null)
        {
            return dao::$errors['returnCount'] = sprintf($this->lang->putproduction->emptyObject, $this->lang->putproduction->returnCount);
        }else if(!is_numeric($returnCount) || (int)$returnCount < 0 || strpos($returnCount,".") !== false) {
            return dao::$errors['returnCount'] = sprintf($this->lang->putproduction->noNumeric, $this->lang->putproduction->returnCount);
        }

        $comment = $_POST['comment'];
        if(empty($comment)) {
            return dao::$errors['comment'] = sprintf($this->lang->putproduction->emptyObject, $this->lang->comment);
        }

        $this->tryError();
        $putproductionInfo = $this->getByID($putproductionID);
        $updateParams = new  stdClass();
        $updateParams->returnCount = $returnCount;

        $this->dao->update(TABLE_PUTPRODUCTION)->data($updateParams)->where('id')->eq($putproductionID)->exec();
        return common::createChanges($putproductionInfo, $updateParams);
    }

    /**
     * 手机端查找数据裂变
     * @param $search
     * @param $orderBy
     * @return mixed
     */
    public function getWaitListApi($search='',$orderBy='id_desc')
    {
        $account     = $this->app->user->account;
        $assigntomeQuery = '( 1    AND  FIND_IN_SET("'.$account.'",dealUser))';
        $putproductions = $this->dao->select('*')->from(TABLE_PUTPRODUCTION)
            ->where($assigntomeQuery)
            ->andWhere('deleted')->ne('1')
            ->andWhere('status')->in($this->lang->putproduction->mobileStatus)
            ->beginIF($search != '')->andwhere(" ( `code` like '%$search%' or `desc` like '%$search%' )")->fi()
            ->orderBy($orderBy)
            ->fetchAll('id');
        return $putproductions;
    }

    /***
     * @param string $search 关键字搜索
     * @param string $orderBy
     * 手机端获取已办列表接口
     */
    public function getCompletedListApi($pager,$search='',$orderBy='id_desc'){
        $this->app->loadLang('iwfp');
        $this->loadModel('iwfp');
        $dataList = $this->iwfp->getDoTaskList('putproduction', $this->app->user->account, 1);
        $doListData = json_decode($dataList);
        $doList = $doListData->data;
        $codes = array();
        foreach ($doList as $doData){
            array_push($codes, $doData->busiPkid);
        }
        $data = $this->dao->select("*")->from(TABLE_PUTPRODUCTION)
            ->where('code')->in($codes)
            ->andWhere('`deleted`')->ne('1')
            ->beginIF($search != '')->andwhere(" ( `code` like '%$search%' or `desc` like '%$search%' )")->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchall();

        return $data;
    }
    /**
     * @param int $id 部门id
     * 修改上海分公司节点名称
     */
    public function resetNodeAndReviewerName($id=0){
        $depts = $this->loadModel('dept')->getRecurveSubDeptIds(30);
        if ((in_array($this->app->user->dept,$depts) &&  in_array($this->app->getMethodName(),['create','copy'])) || (in_array($id,$depts) && !in_array($this->app->getMethodName(),['create','copy']))){
            $this->lang->putproduction->reviewNodeCodeNameList[$this->lang->putproduction->reviewNodeCodeList['waitleader']]  = '上海分公司领导';
            $this->lang->putproduction->reviewNodeCodeNameList[$this->lang->putproduction->reviewNodeCodeList['waitgm']]      = '上海分公司总经理';
        }

    }

}
