<?php
class creditModel extends model{
    public function buildSearchForm($queryID, $actionURL){
        $this->config->credit->search['actionURL'] = $actionURL;
        $this->config->credit->search['queryID']   = $queryID;
        $this->loadModel('search')->setSearchParams($this->config->credit->search);
    }

    /**
     * 通过id获得基本信息
     *
     * @param $id
     * @param string $select
     * @return mixed
     */
    public function getBasicInfoById($id, $select = '*'){
        if(!$id){
            return  false;
        }
        $ret = $this->dao->select($select)
            ->from(TABLE_CREDIT)
            ->where('deleted')->eq('0')
            ->andWhere('id')->eq($id)
            ->fetch();
        return $ret;
    }

    /**
     * 获得列表页面
     *
     * @param $browseType
     * @param $queryID
     * @param $orderBy
     * @param null $pager
     * @return array
     */
    public function getList($browseType, $queryID, $orderBy, $pager = null){
        $data = [];
        $creditQuery = '';
        if($browseType == 'bysearch') {
            $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : '';
            if ($query) {
                $this->session->set('creditQuery', $query->sql);
                $this->session->set('creditForm', $query->form);
            }
            if ($this->session->creditQuery == false) $this->session->set('creditQuery', ' 1 = 1');

            $creditQuery = $this->session->creditQuery;
            $pattern = '/`riskAnalysisEmergencyHandle`(.*)%\' \)/';
            preg_match($pattern, $creditQuery, $patternRes);
            if(!empty($patternRes[0])){
                $findInfoArray = explode(')',  $patternRes[0]);
                $findInfo = $findInfoArray[0];
                $patternVal = '/\'%(.*)%\'/';
                preg_match($patternVal, $findInfo, $patternResVal);
                $findFieldVal = $patternResVal[0];
                $replaceInfo = " (riskAnalysisEmergencyHandle->'$[*].riskAnalysis' LIKE ".$findFieldVal."  or riskAnalysisEmergencyHandle->'$[*].emergencyBackWay' LIKE ".$findFieldVal.") ";
                $creditQuery = str_replace($findInfo, $replaceInfo, $creditQuery);
            }
        }
        $account = $this->app->user->account;
        //查询列表
        $ret = $this->dao->select('*')
            ->from(TABLE_CREDIT)
            ->where('deleted')->eq('0')
            ->beginIF($browseType != 'all' && $browseType != 'bysearch'   &&  $browseType != 'tomedeal')
            ->andWhere('status')->eq($browseType)
            ->fi()
            ->beginIF($browseType == 'tomedeal')
            ->andWhere("FIND_IN_SET('{$account}', dealUsers)")
            ->fi()
            ->beginIF($browseType == 'bysearch')
            ->andWhere($creditQuery)
            ->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');
        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'credit', $browseType != 'bysearch');
        if($ret){
            $data = $ret;
        }
        return $data;
    }

    /**
     * 获得异常变更单id
     *
     * @param $abnormalId
     * @param string $select
     * @return mixed
     */
    public function getAbnormalInfoByAbnormalId($abnormalId, $select = '*'){
        $ret = $this->dao->select($select)
            ->from(TABLE_CREDIT)
            ->where('deleted')->eq('0')
            ->andWhere('abnormalId')->eq($abnormalId)
            ->fetch();
        return $ret;
    }

    /**
     * 获得征信交付信息
     *
     * @param $id
     * @return bool
     */
    public function getById($id){
        $ret = $this->dao->select('*')
            ->from(TABLE_CREDIT)
            ->where('deleted')->eq('0')
            ->andWhere('id')->eq($id)
            ->fetch();
        if(!$ret){
            return false;
        }
        //查询异常关联单
        $abnormalInfo = $this->getAbnormalInfoByAbnormalId($ret->id, 'id,code');
        if($abnormalInfo){
            $ret->abnormalId = $abnormalInfo->id;
            $ret->abnormalCode = $abnormalInfo->code;
        }
        $consumedList = $this->loadModel('consumed')->getConsumed($this->config->credit->objectType, $id);
        $ret->consumed = $consumedList;
        if ($ret->actualDeliveryTime == '0000-00-00 00:00:00') $ret->actualDeliveryTime = '';

        return $ret;
    }

    /**
     * 获得列表信息
     *
     * @param string $exWhere
     * @param string $select
     * @return mixed
     */
    public function getListByExWhere($exWhere = '', $select = '*'){
        $ret = $this->dao->select($select)
            ->from(TABLE_CREDIT)
            ->where('deleted')->eq('0')
            ->beginIF($exWhere)->andWhere($exWhere)->fi()
            ->fetchAll();
        return $ret;
    }

    /**
     * 获得审核人信息
     *
     * @param int $deptId
     * @return array
     */
    public function getReviewNodeUserList($deptId = 0){
        $reviewers = [];
        $users = $this->loadModel('user')->getPairs('noletter|noclosed');
        if (!$deptId) {
            $deptId = $this->app->user->dept;
        }
        $deptInfo = $this->loadModel('dept')->getByID($deptId);
        $this->app->loadLang('dept');
        $tianjinDeptId = $this->lang->dept->tianjinDeptId;
        $tjDeptInfo = $this->loadModel('dept')->getByID($tianjinDeptId);

        //质量部cm
        $currentUsers = array_filter(explode(',', $tjDeptInfo->cm));
        $tempUsers  = array('' => '');
        if(!empty($currentUsers)){
            foreach($currentUsers as $account) {
                $tempUsers[$account] = zget($users, $account);
            }
        }
        $reviewers[$this->lang->credit->nodeCodeList['waitcm']] = $tempUsers;

        //部门负责人
        $currentUsers = explode(',', trim($deptInfo->manager, ','));
        $tempUsers  = array('' => '');
        if(!empty($currentUsers)){
            foreach($currentUsers as $account) {
                $tempUsers[$account] = zget($users, $account);
            }
        }
        $reviewers[$this->lang->credit->nodeCodeList['waitdept']] = $tempUsers;

        //部门分管领导
        $currentUsers = explode(',', trim($deptInfo->leader, ','));
        $tempUsers  = array('' => '');
        if(!empty($currentUsers)){
            foreach($currentUsers as $account)
            {
                $tempUsers[$account] = zget($users, $account);
            }
        }
        $reviewers[$this->lang->credit->nodeCodeList['waitleader']] = $tempUsers;

        // 总经理

        $depts = $this->loadModel('dept')->getRecurveSubDeptIds(30);
        if (in_array($this->app->user->dept,$depts)){
            $this->app->loadConfig('modify');
            // 上海分公司特殊处理
            $reviewers[$this->lang->credit->nodeCodeList['waitgm']] = [$this->config->modify->branchManagerList => $users[$this->config->modify->branchManagerList]];
        }else{
            $account = $this->dao->select('account')->from(TABLE_USER)->where('role')->eq('ceo')->fetch('account');
            $reviewers[$this->lang->credit->nodeCodeList['waitgm']] = array($account => $users[$account]);
        }

        //产创二线专员

        $currentUsers = array_filter(explode(',', $tjDeptInfo->executive));
        $tempUsers  = array('' => '');
        if(!empty($currentUsers)){
            foreach($currentUsers as $account) {
                $tempUsers[$account] = zget($users, $account);
            }
        }
        $reviewers[$this->lang->credit->nodeCodeList['waitproductsecond']] = $tempUsers;
        return $reviewers;
    }

    /**
     * 获得允许绑定的异常变更单列表
     *
     * @param $includeAbnormalId
     * @return array
     */
    function  getAllowBindAbnormalList($includeAbnormalId = 0){
        $data = [];
        $ret = $this->dao->select('id, CASE WHEN summary != "" THEN concat(concat(concat(code,"("),summary),")") ELSE code END as name')
            ->from(TABLE_CREDIT)
            ->where('deleted')->eq('0')
            ->andWhere('`status`')->in($this->lang->credit->reissueStatusArray)
            ->beginIF($includeAbnormalId != 0)->andWhere(" (abnormalId = '{$includeAbnormalId}' OR abnormalId = '0')")->fi()
            ->beginIF($includeAbnormalId == 0)->andWhere('abnormalId')->eq('0')->fi()
            ->fetchPairs();
        if($ret){
            $data = $ret;
        }
        return $data;
    }

    /**
     * 获得异常关联列表
     *
     * @param array $abnormalIds
     * @return array
     */
    public function getAbnormalList($abnormalIds = []){
        $data = [];
        $ret = $this->dao->select('id, abnormalId, code, CASE WHEN summary != "" THEN concat(concat(concat(code,"("),summary),")") ELSE code END as name')
            ->from(TABLE_CREDIT)
            ->where('deleted')->eq('0')
            ->andWhere('abnormalId')->ne('0')
            ->beginIF($abnormalIds)->andWhere('abnormalId')->in($abnormalIds)->fi()
            ->fetchAll();
        if($ret){
            $data = $ret;
        }
        return $data;
    }

    /**
     * 获得所有异常变更单列表
     *
     * @return array
     */
    public function getAllAbnormalList(){
        $data = [];
        $ret = $this->dao->select('id, CASE WHEN summary != "" THEN concat(concat(concat(code,"("),summary),")") ELSE code END as name')
            ->from(TABLE_CREDIT)
            ->where('deleted')->eq('0')
            ->andWhere('`status`')->in($this->lang->credit->reissueStatusArray)
            ->fetchPairs();
        if($ret){
            $data = $ret;
        }
        return $data;
    }

    /**
     * 创建征信交付
     * @param $op
     * @return mixed
     */
    public function create(){
        $objectType = $this->config->credit->objectType;
        $issubmit = $_POST['issubmit'];
        $postData = fixer::input('post')
            ->remove('uid')
            ->get();
        $postData = $this->getFormatPostData($postData);

        if($issubmit == 'submit'){
            //检查基本信息
            $res = $this->checkPostParamsInfo($postData, 'create', 0);
            if(!$res['checkRes']){
                dao::$errors = $res['errorData'];
                return dao::$errors;
            }
        }
        $status   = $this->lang->credit->statusArray['waitsubmit'];//待提交
        $dealUsers = $this->app->user->account;
        $postData->createdBy    = $this->app->user->account;
        $postData->createdDept  = $this->app->user->dept;
        $postData->createdDate  = helper::now();
        $postData->status       = $status;
        $postData->dealUsers     = $dealUsers;
        $postData->code = $this->getCode();  //征信交付单号
        $postData->riskAnalysisEmergencyHandle = json_encode($postData->riskAnalysisEmergencyHandle);
        $reviewerInfo = $postData->reviewerInfo;
        $postData->reviewerInfo = json_encode($postData->reviewerInfo);
        $abnormalId = $postData->abnormalId;
        if($abnormalId){
            unset($postData->abnormalId);
        }
        $this->createCredit($postData);
        if(dao::isError()) {
            return dao::$errors;
        }
        $creditId = $this->dao->lastInsertID();
        if($issubmit == 'submit'){ //提交
            //调取接口同步审核人信息
            $reviewNodeNameList = $this->getReviewNodeNameListByLevel($postData->level);
            $res = $this->loadModel('iwfp')->startWorkFlow($objectType, $creditId, $postData->code, $postData->createdBy, $reviewerInfo, '1', $reviewNodeNameList);
            if(dao::isError()) {
                dao::$errors = []; //重新复制
                $ret = $this->dao->update(TABLE_CREDIT)->set('deleted')->eq('1')
                    ->where('id')->eq($creditId)
                    ->exec();
                return dao::$errors[''] = $res;
            }

            $processInstanceId = $res->processInstanceId;
            $updateParams = new stdClass();
            $updateParams->workflowId = $processInstanceId;
            $this->dao->update(TABLE_CREDIT)->data($updateParams)->autoCheck()
                ->where('id')->eq($creditId)
                ->exec();

            //从待提交到提交后进入待cm处理环节(提交默认处理通过)
            $dealResult = '1';
            $res = $this->submit($creditId, $dealUsers, $dealResult, '', true, false);
            if(dao::isError()) { //保存成功，但是提交失败
                $this->loadModel('consumed')->record($objectType, $creditId, '0', $this->app->user->account, '', $status);
                return $res;
            }
        }else{
            //添加状态流转
            $this->loadModel('consumed')->record($objectType, $creditId, '0', $this->app->user->account, '', $status);
        }

        if($abnormalId){ //关联异常变更单
            $this->bindCreditAbnormalInfo($abnormalId, $creditId);
        }
        //返回
        return $creditId;
    }

    /**
     * 更新交付单信息
     *
     * @param $creditId
     * @return array
     */
    function update($creditId){
        $objectType = $this->config->credit->objectType;
        $creditInfo = $this->getByID($creditId);
        $account = $this->app->user->account;
        //检查是否允许更新
        $res = $this->checkIsAllowEdit($creditInfo, $account);
        if(!$res['result']){
            dao::$errors[] = $res['message'];
            return false;
        }
        $issubmit = $_POST['issubmit'];
        $postData = fixer::input('post')
            ->remove('uid')
            ->get();
        $postData = $this->getFormatPostData($postData);
        if($issubmit == 'submit'){
            //检查基本信息
            $res = $this->checkPostParamsInfo($postData);
            if(!$res['checkRes']){
                dao::$errors = $res['errorData'];
                return false;
            }
        }

        $postData->editedBy   = $account;
        $postData->editedDate = helper::now();
        $postData->riskAnalysisEmergencyHandle = json_encode($postData->riskAnalysisEmergencyHandle);
        $postData->reviewerInfo = json_encode($postData->reviewerInfo);
        $abnormalId = $postData->abnormalId;
        $oldStatus = $creditInfo->status;
        $status = $oldStatus;
        unset($postData->abnormalId);
        //修改之前关联的工单ids
        $oldSecondorderIds = $this->loadModel('secondline')->getRelationIds($objectType, $creditId, 'secondorder');
        //更新操作
        $this->updateCredit($creditId, $postData);
        if($issubmit == 'submit'){ //提交
            //从待提交到提交后进入待cm处理环节(提交默认处理通过)
            $dealResult = '1';
            $dealUser = $this->app->user->account;
            $this->submit($creditId, $dealUser, $dealResult, '', false, false);

            //取消之前关联的
            if($oldSecondorderIds){
                $newSecondorderIds = array_filter(explode(',', $postData->secondorderIds));
                $diffSecondorderIds = array_diff($oldSecondorderIds, $newSecondorderIds);
                if($diffSecondorderIds){ //这些工单不在关联征信交付
                    foreach ($diffSecondorderIds as $secondorderId){
                        $secondorderStatus = 'todelivered';
                        $result = $this->loadModel('secondorder')->syncSecondorderStatus($secondorderId, $objectType, $creditId, '', $creditInfo->code, $secondorderStatus);
                    }
                }
            }

        }else{
            //添加状态流转
            if($status == $oldStatus){
                $lastConsumedInfo = $this->loadModel('consumed')->getLastConsumed($creditId, $objectType);
                if($lastConsumedInfo && ($lastConsumedInfo->before == $lastConsumedInfo->after) && ($status == $lastConsumedInfo->after)){
                    $this->loadModel('consumed')->update($lastConsumedInfo->id, $objectType, $creditId, '0', $account, $lastConsumedInfo->before, $lastConsumedInfo->after);
                }else{
                    $this->loadModel('consumed')->record($objectType, $creditId, '0', $account, $oldStatus, $status);
                }
            }else{
                $this->loadModel('consumed')->record($objectType, $creditId, '0', $account, $oldStatus, $status);
            }
        }

//        //关联关系
//        if($postData->projectPlanId != $creditInfo->projectPlanId){
//            $this->loadModel('secondline')->saveRelationship($creditId, $objectType, $postData->projectPlanId, 'project');
//        }
//        if($postData->demandIds != $creditInfo->demandIds){
//            $this->loadModel('secondline')->saveRelationship($creditId, $objectType, $postData->demandIds, 'demand');
//        }
//        if($postData->problemIds != $creditInfo->problemIds){
//            $this->loadModel('secondline')->saveRelationship($creditId, $objectType, $postData->problemIds, 'problem');
//        }
//        if($postData->secondorderIds != $creditInfo->secondorderIds){
//            $this->loadModel('secondline')->saveRelationship($creditId, $objectType, $postData->secondorderIds, 'secondorder');
//        }

        $oldAbnormalId = $creditInfo->abnormalId;
        if($abnormalId != $oldAbnormalId){ //异常变更单是修改
            $this->bindCreditAbnormalInfo($abnormalId, $creditId);
        }
        //返回
        $diffInfo = $this->getJsonFieldDiffInfo($postData->riskAnalysisEmergencyHandle, $creditInfo->riskAnalysisEmergencyHandle);
        $extChangeInfo = [];
        if($diffInfo['isDiff']){
            $extChangeInfo['riskAnalysisEmergencyHandle'] = $diffInfo['diffInfo'];
        }
        unset($postData->riskAnalysisEmergencyHandle);
        unset($creditInfo->riskAnalysisEmergencyHandle);
//        echo '<pre>';
//        print_r($diffInfo);
//        echo '</pre>';

        return common::createChanges($creditInfo, (Object)$postData, $extChangeInfo);
    }

    /**
     * 获得json字段差异信息
     *
     * @param $jsonNewInfo
     * @param $jsonOldInfo
     * @return bool
     */
    public function getJsonFieldDiffInfo($jsonNewInfo, $jsonOldInfo){
        $isDiff = false;
        $diffInfo = new stdClass();
        $res = [
            'isDiff'    => $isDiff,
            'diffInfo'  => $diffInfo
        ];
        $newInfo = json_decode($jsonNewInfo, true);
        $oldInfo = json_decode($jsonOldInfo, true);
        $newRiskAnalysis     = array_column($newInfo, 'riskAnalysis');
        $newEmergencyBackWay = array_column($newInfo, 'emergencyBackWay');

        $oldRiskAnalysis     = array_column($oldInfo, 'riskAnalysis');
        $oldEmergencyBackWay = array_column($oldInfo, 'emergencyBackWay');

        if(array_diff($newRiskAnalysis, $oldRiskAnalysis) || array_diff($oldRiskAnalysis, $newRiskAnalysis) || array_diff($newEmergencyBackWay, $oldEmergencyBackWay) || array_diff($oldEmergencyBackWay, $newEmergencyBackWay)){
            $isDiff = true;
            $oldData = '';
            $newData = '';
            if(!empty($oldInfo)){
                foreach ($oldInfo as $key => $val){
                    if(is_numeric($key)){
                        $keyVal = '<br/>编号'.($key+1);
                    }else{
                        $keyVal = $key;
                    }
                    if(is_array($val)){
                        $oldData .= $keyVal.':' .' ';
                        foreach ($val as $subKey => $subVal){
                            $fieldKey = $this->lang->credit->$subKey;
                            $oldData .= $fieldKey .':' . $subVal .' ';
                        }
                    }else{
                        $oldData .= $keyVal .':' . $val .'';
                    }
                }
            }

            if(!empty($newInfo)){
                foreach ($newInfo as $key => $val){
                    if(is_numeric($key)){
                        $keyVal = $key+1;
                    }else{
                        $keyVal = $key;
                    }
                    if(is_array($val)){
                        $newData .= $keyVal.':' .' ';
                        foreach ($val as $subKey => $subVal){
                            $newData .= $subKey .':' . $subVal .' ';
                        }
                    }else{
                        $newData .= $keyVal .':' . $val .' ';
                    }
                }
            }
            $diffInfo->new = $newData;
            $diffInfo->old = $oldData;
        }
        $res['isDiff'] = $isDiff;
        $res['diffInfo'] = $diffInfo;
        return $res;
    }


    /**
     * 变更信息
     *
     * @param $id
     * @param $data
     * @return mixed
     */
    private function updateCredit($id, $data){
        $data = (object)$data;
        $res = $this->dao->update(TABLE_CREDIT)
            ->data($data)
            ->batchCheckIF($_POST['issubmit'] != 'save', $this->config->credit->edit->requiredFields, 'notempty')
            ->where('id')->eq((int)$id)->exec();
        return $res;
    }

    /**
     * 取消绑定异常变更单
     *
     * @param $abnormalId
     * @param $relatedCreditId
     * @return bool
     */
    public function bindCreditAbnormalInfo($abnormalId, $relatedCreditId){
        if(!($relatedCreditId && $abnormalId)){
            return false;
        }
        //更新本关联
        $updateParams = new stdClass();
        $updateParams->abnormalId = $relatedCreditId;
        $this->dao->update(TABLE_CREDIT)->data($updateParams)->where('id')->eq($abnormalId)->exec();
        //是否被其他关联,如果被其他关联则取消关联
        $exWhere = " abnormalId = '{$relatedCreditId}' AND id != '{$abnormalId}'";
        $otherList = $this->getListByExWhere($exWhere, 'id');
        if($otherList){
            $updateParams = new stdClass();
            $updateParams->abnormalId = 0;
            $ids = array_column($otherList, 'id');
            $this->dao->update(TABLE_CREDIT)->data($updateParams)->where('id')->in($ids)->exec();
        }
        return true;
    }

    /**
     * 提交操作
     *
     * @param $creditId
     * @param $dealUsers
     * @param $dealResult
     * @param string $dealMessage
     * @param bool $createSubmitIsMerge 创建和提交是否合并操作
     * @param $isCheckParams
     * @return array
     */
    public function submit($creditId, $dealUsers, $dealResult, $dealMessage = '', $createSubmitIsMerge = false, $isCheckParams = true){
        $objectType = $this->config->credit->objectType;
        $creditInfo = $this->getBasicInfoById($creditId);
        //检查是否允许更新
        $res = $this->checkIsAllowSubmit($creditInfo, $dealUsers);
        if(!$res['result']){
            return dao::$errors[''] = $res['message'];
        }
        if($isCheckParams){ //单独提交时，检查信息是否完善
            $res = $this->checkPostParamsInfo($creditInfo);
            if(!$res['checkRes']){
                return dao::$errors = $res['errorData'];
            }
        }
        $version = $creditInfo->version;
        if(in_array($creditInfo->status, $this->lang->credit->needUpdateVersionStatusArray)){
            $version = $creditInfo->version + 1;
        }
        $processInstanceId = $creditInfo->workflowId ? $creditInfo->workflowId:'';
        $reviewerInfo =  json_decode($creditInfo->reviewerInfo, true);
        $res = $this->saveWorkFlow($creditInfo, $reviewerInfo, $version);
        if($res){
            $creditInfo = $this->getBasicInfoById($creditId);
            $processInstanceId = $creditInfo->workflowId ? $creditInfo->workflowId:'';
        }

        $userVariableList = new stdClass();
        $userVariableList->level = $creditInfo->level;

        $res = $this->loadModel('iwfp')->completeTaskWithClaim($processInstanceId, $dealUsers, $dealMessage, $dealResult, $userVariableList, $version);
        if(dao::isError()) {
            return $res;
        }
        //更新表已经提交
        $updateParams = new stdClass();
        $nextStatus = $res->toXmlTask;
        $nextUsers  = is_array($res->dealUser) ? implode(',', $res->dealUser):$res->dealUser;
        $updateParams->status   = $nextStatus;
        $updateParams->dealUsers = $nextUsers;
        $updateParams->version = $version;
        $updateParams->deliveryTime = '0000-00-00 00:00:00'; //交付时间为空
        $this->dao->update(TABLE_CREDIT)->data($updateParams)->autoCheck()
            ->where('id')->eq($creditId)
            ->exec();
        if(dao::isError()) {
            return dao::getError();
        }
        if($createSubmitIsMerge){
            $oldStatus = '';
        }else{
            $oldStatus = $creditInfo->status;
        }
        //添加状态流转
        $this->loadModel('consumed')->record($objectType, $creditId, '0', $dealUsers, $oldStatus, $nextStatus);

        $this->loadModel('secondline')->saveRelationship($creditId, $objectType, $creditInfo->projectPlanId, 'project');
        $this->loadModel('secondline')->saveRelationship($creditId, $objectType, $creditInfo->demandIds, 'demand');
        $this->loadModel('secondline')->saveRelationship($creditId, $objectType, $creditInfo->problemIds, 'problem');
        $this->loadModel('secondline')->saveRelationship($creditId, $objectType, $creditInfo->secondorderIds, 'secondorder');

        //本次关联的工单
        if($creditInfo->secondorderIds){
            $secondorderIds = array_filter(explode(',', $creditInfo->secondorderIds));
            foreach ($secondorderIds as $secondorderId){
                $result = $this->loadModel('secondorder')->syncSecondorderStatus($secondorderId, $objectType, $creditId, $nextStatus, $creditInfo->code);
            }
        }
        return $res;
    }

    /**
     * 检查是否允许编辑
     *
     * @param $info
     * @param $account
     * @return array
     */
    public function checkIsAllowSubmit($info, $account){
        $res = array(
            'result'  => false,
            'message' => '',
        );
        if(!$info){
            $res['message'] = $this->lang->common->errorParamId;
            return $res;
        }
        //当前状态
        $status = $info->status;
        if(!in_array($status, $this->lang->credit->allowSubmitStatusArray)){
            $statusDesc = zget($this->lang->credit->statusList, $status);
            $res['message'] = sprintf($this->lang->credit->checkOpResultList['statusError'], $statusDesc, $this->lang->credit->submit);
            return $res;
        }
        $dealUsers = $info->dealUsers;
        $users = array_filter(explode(',', $dealUsers));
        $users[]  = 'admin';
        if(!in_array($account, $users)){
            $res['message'] = sprintf($this->lang->credit->checkOpResultList['userError'], $this->lang->credit->submit);
            return $res;
        }
        $res['result'] = true;
        return $res;
    }

    /**
     * 检查参数信息
     *
     * @param $params
     * @param string $op
     * @param int $creditId
     * @return array
     */
    public function checkPostParamsInfo($params, $op = 'create', $creditId = 0){
        //检查结果
        $checkRes = false;
        $errorData = [];
        $data = [
            'checkRes'  => $checkRes,
            'errorData' => $errorData,
        ];
        $objectType = $this->config->credit->objectType;
        $requiredFields = explode(',', $this->config->credit->$op->requiredFields);
        foreach ($requiredFields as $requiredField){
            if(!isset($params->$requiredField) || empty($params->$requiredField)){
                $errorData[$requiredField] = sprintf($this->lang->error->notempty, $this->lang->credit->$requiredField);
            }
        }
        //问题单，关联需求单，二线工单同时为空
        if(empty($params->problemIds) &&  empty($params->demandIds) && empty($params->secondorderIds)){
            $errorData['secondorderIds'] = $this->lang->credit->relationTypeError;
        }

        //关联了需求工单
        if(!empty($params->demandIds)){
            $demandIds = array_filter(explode(',', $params->demandIds));
            $exWhere = " id In ( ".implode(',', $demandIds).")";
            $demandList = $this->loadModel('demand')->getPairsTitle('', $exWhere);
            if(empty($demandList)){
                $errorData['demandIds'] = $this->lang->credit->demandError;
            }
            $deleteOutDataStr =  $this->loadModel('requirement')->getRequirementInfos($demandIds);
            if($deleteOutDataStr){
                $errorData['demandIds'] = sprintf($this->lang->credit->deleteOutTip , $deleteOutDataStr);
            }

            //变更锁提示
            $demandInfo = $this->loadModel('demand')->getDemandLockByIds($demandIds, 'code');
            if(!empty($demandInfo)){
                $lockCode = implode(',',array_column($demandInfo,'code'));
                $errorData['demandIds'] = sprintf($this->lang->credit->demandLockError , $lockCode);
            }
            //是否被其他模块占用
            $tempCreditId = (isset($params->id) && $params->id) ? $params->id : 0;
            if($tempCreditId == 0 && (isset($params->abnormalId) && !empty($params->abnormalId))){
                $tempCreditId = $params->abnormalId;
            }
            $allowDemandList =  $this->loadModel('demand')->modifySelect($objectType, $tempCreditId, 1, '', $demandIds);
            $allowDemandIds = array_keys($allowDemandList);
            $diffDemandIds = array_diff($demandIds, $allowDemandIds);
            if(!empty($diffDemandIds)){
                $demandErrorData = [];
                foreach ($diffDemandIds as $demandId){
                    $demandCode = zget($demandList, $demandId);
                    $demandErrorData[$demandId] = sprintf($this->lang->credit->demandUsedError , $demandCode);
                }
                if(isset($errorData['demandIds'])){
                    $errorData['demandIds'] .= implode('<br/>', $demandErrorData);
                }else{
                    $errorData['demandIds'] = implode('<br/>', $demandErrorData);
                }
            }
        }

        //检查二线工单
        if(!empty($params->secondorderIds)){
            $secondorderIds = array_filter(explode(',', $params->secondorderIds));
            $ignoreId = isset($params->id) ? $params->id : 0; //忽略id
            if((!$ignoreId) && ($params->abnormalId)){
                $ignoreId = $params->abnormalId;
            }
            $secondorderCheckRes = $this->loadModel('secondorder')->checkSecondorderIdsIsAllowUse($secondorderIds, $this->config->credit->objectType, $ignoreId);
            if(!$secondorderCheckRes['checkRes']){
                $errorData['secondorderIds'] = implode(' ', $secondorderCheckRes['errorData']);
            }
        }
        //时间验证
        if(!$this->loadModel('common')->checkJkDateTime($params->planBeginTime)){
            $errorData['planBeginTime'] = sprintf($this->lang->credit->formatErrorObject, $this->lang->credit->planBeginTime);
        }
        if(!$this->loadModel('common')->checkJkDateTime($params->planEndTime)){
            $errorData['planEndTime'] = sprintf($this->lang->credit->formatErrorObject, $this->lang->credit->planEndTime);
        }
        if($params->planEndTime < $params->planBeginTime){
            $errorData['planEndTime'] = $this->lang->credit->planEndTimeLessError;
        }
        // 是否后补流程判断
        if ($params->isMakeAmends == ''){
            $errorData['isMakeAmends'] = sprintf($this->lang->credit->emptyObject , $this->lang->modify->isMakeAmends);
        }
        if ($params->isMakeAmends == 'yes' && $params->actualDeliveryTime == ''){
            $errorData['actualDeliveryTime'] = sprintf($this->lang->credit->emptyObject , $this->lang->modify->actualDeliveryTime);
        }
        if($params->level == 1){ //一级变更，预计开始时间距离当前时间是否由多少个工作日限制

        }
        //风险分析与应急处置
        $riskAnalysisEmergencyHandle = $params->riskAnalysisEmergencyHandle;
        if(!is_array($riskAnalysisEmergencyHandle)){
            $riskAnalysisEmergencyHandle = json_decode($riskAnalysisEmergencyHandle);
        }
        if(empty($riskAnalysisEmergencyHandle)){
            $errorData["riskAnalysis_1"]      = sprintf($this->lang->credit->emptyObject ,  $this->lang->credit->riskAnalysis);
            $errorData["emergencyBackWay_1"] = sprintf($this->lang->credit->emptyObject ,   $this->lang->credit->emergencyBackWay);
        }else{
            foreach ($riskAnalysisEmergencyHandle as $key => $val){
                $indexKey = $key + 1;
                $riskAnalysis     = $val->riskAnalysis;
                $emergencyBackWay = $val->emergencyBackWay;
                if(empty($riskAnalysis)){
                    $errorData["riskAnalysis_{$indexKey}"]      = sprintf($this->lang->credit->indexKeyEmptyObject , $indexKey, $this->lang->credit->riskAnalysis);
                }
                if(empty($emergencyBackWay)){
                    $errorData["emergencyBackWay_{$indexKey}"] = sprintf($this->lang->credit->indexKeyEmptyObject , $indexKey, $this->lang->credit->emergencyBackWay);
                }
            }
        }

        //产品svn路径是否由格式校验和真是存在校验
        $res = $this->checkReviewerNodesInfo($params->reviewerInfo, $params->level);
        if(!$res['checkRes']){
            $errorData = array_merge($errorData, $res['errorData']);
        }
        if($errorData){
            $data['errorData'] = $errorData;
        }else{
            $checkRes = true;
            $data['checkRes'] = $checkRes;
        }
        return $data;
    }


    /**
     * 校验审核人节点信息
     *
     * @param $reviewerInfo
     * @param $level
     * @return array
     */
    public function checkReviewerNodesInfo($reviewerInfo, $level){
        //检查结果
        $checkRes = false;
        $errorData = [];
        $data = [
            'checkRes' => $checkRes,
            'errorData' => $errorData,
        ];
        if(!is_array($reviewerInfo)){
            $reviewerInfo = json_decode($reviewerInfo, true);
        }
        if(!$reviewerInfo){
            $errorData['reviewerInfo'] = $this->lang->credit->reviewerInfoEmpty;
        }else{
            if($level){
                $reviewNodeCodeList = zget($this->lang->credit->reviewNodeCodeListGroupLevel, $level);
            }
            if(empty($reviewNodeCodeList)){
                $reviewNodeCodeList = $this->lang->credit->reviewNodeCodeList;
            }

            $nodeKeys = array();
            foreach($reviewerInfo as $key => $currentNodes) {
                //去除空元素
                $currentNodes = array_filter($currentNodes);
                if(!empty($currentNodes))
                {
                    $nodeKeys[] = $key;
                    $reviewerInfo[$key] = $currentNodes;
                }
            }
            //必选审核人，却没有选
            $diffKeys = array_diff($reviewNodeCodeList, $nodeKeys);
            if(!empty($diffKeys)) {
                foreach ($diffKeys as $nodeKey) {
                    $reviewerNodeName = $this->lang->credit->reviewNodeNameList[$nodeKey];
                    $errorData["reviewerInfo{$nodeKey}"] = sprintf($this->lang->credit->nodeReviewerInfoEmpty, $reviewerNodeName);
                }
            }
        }
        if(!empty($errorData)){
            $data['errorData'] = $errorData;
        }else{
            $checkRes = true;
            $data['checkRes'] = $checkRes;
        }
        return $data;
    }

    /**
     *创建投产信息
     *
     * @param $data
     * @return mixed
     */
    private function createCredit($data){
        $data = (object)$data;
        return $this->dao->insert(TABLE_CREDIT)
            ->data($data)
            ->batchCheckIF($_POST['issubmit'] != 'save',$this->config->credit->create->requiredFields, 'notempty')
            ->exec();
    }

    /**
     *设置交付单号
     *
     * @return string
     */
    private function getCode(){
        $codePrefix = 'CFIT-CZ-';
        $number = $this->dao->select('count(id) c')->from(TABLE_CREDIT)->where('code')->like($codePrefix . date('Ymd-')."%")->fetch('c') ;
        $number = intval($number) + 1;
        $code   = $codePrefix . date('Ymd-') . sprintf('%02d', $number);
        return $code;
    }

    /**
     *通过变更级别获得审核人员信息
     *
     * @param $nodes
     * @param string $level
     * @param $oldReviewersInfo
     * @return array
     */
    public function getFormatReviewerInfo($nodes = [], $level = '', $oldReviewersInfo = []){
        //重新排序
        $data = [];
        if(!$nodes){
            return $data;
        }
        if($level){
           $reviewNodeCodeList = zget($this->lang->credit->reviewNodeCodeListGroupLevel, $level);
        }
        if(empty($reviewNodeCodeList)){
            $reviewNodeCodeList = $this->lang->credit->reviewNodeCodeList;
        }

        $reviewerInfo = [];
        foreach ($reviewNodeCodeList as $nodeCode){
            if(isset($nodes[$nodeCode])){
                $reviewerInfo[$nodeCode] = array_values(array_filter($nodes[$nodeCode]));
            }else{
                $reviewerInfo[$nodeCode] = [];
            }
        }
        $tempUserArray = [$this->app->user->account];
        //待提交
        if(!isset($reviewerInfo['waitsubmit'])){
            if(isset($oldReviewersInfo['waitsubmit']) && !empty($oldReviewersInfo['waitsubmit'])){
                $reviewerInfo['waitsubmit'] = $oldReviewersInfo['waitsubmit'];
            }else{
                $reviewerInfo['waitsubmit'] = $tempUserArray;
            }
        }
        //待确认结论
        if(!isset($reviewerInfo['waitconfirmresult'])){
            if(isset($oldReviewersInfo['waitconfirmresult']) && !empty($oldReviewersInfo['waitconfirmresult'])){
                $reviewerInfo['waitconfirmresult'] = $oldReviewersInfo['waitconfirmresult'];
            }else{
                $reviewerInfo['waitconfirmresult'] = explode(',', $this->config->credit->confirmResultUsers);
            }
        }
        //退回
        if(!isset($reviewerInfo['reject'])){
            if(isset($oldReviewersInfo['reject']) && !empty($oldReviewersInfo['reject'])){
                $reviewerInfo['reject'] = $oldReviewersInfo['reject'];
            }else{
                $reviewerInfo['reject'] = $tempUserArray;
            }
        }

        foreach ($this->lang->credit->nodeCodeList as $nodeCode){
            if(isset($reviewerInfo[$nodeCode])){
                $data[$nodeCode] = $reviewerInfo[$nodeCode];
            }else{
                $data[$nodeCode] = [];
            }
        }
        return $data;
    }


    /**
     * 获得格式话post数据
     *
     * @param $postData
     * @param array $oldReviewerInfo
     * @return mixed
     */
    public function getFormatPostData($postData, $oldReviewerInfo = []){
        $requiredFields = explode(',', $this->config->credit->create->requiredFields);
        foreach ($requiredFields as $requiredField){
            if(!isset($postData->$requiredField)){
                $postData->$requiredField = '';
            }
        }
        foreach ($this->config->credit->multipleSelectFields as $multipleSelectField){
            if(isset($postData->$multipleSelectField) && !empty($postData->$multipleSelectField)){
               if(is_array($postData->$multipleSelectField)){
                   $postData->$multipleSelectField = implode($postData->$multipleSelectField, ',');
               }
                $postData->$multipleSelectField = trim($postData->$multipleSelectField, ',');
            }else{
                $postData->$multipleSelectField = '';
            }
        }
        $riskAnalysis     = isset($postData->riskAnalysis) ? $postData->riskAnalysis:[];
        $emergencyBackWay = isset($postData->emergencyBackWay) ? $postData->emergencyBackWay:[];
        $riskAnalysisEmergencyHandle = [];
        if(!empty($riskAnalysis)){
            foreach ($riskAnalysis as $key => $risk){
                $temp = new  stdClass();
                $temp->riskAnalysis = $risk;
                $temp->emergencyBackWay = zget($emergencyBackWay, $key, '');
                $riskAnalysisEmergencyHandle[] = $temp;
            }
        }
        $postData->riskAnalysisEmergencyHandle = $riskAnalysisEmergencyHandle;
        unset($postData->riskAnalysis);
        unset($postData->emergencyBackWay);

        //审核节点的审核人信息
        $reviewerInfo = [];
        if(isset($postData->reviewerInfo) && !empty($postData->reviewerInfo)){
            $reviewerInfo = $postData->reviewerInfo;
        }
        $postData->reviewerInfo = $this->getFormatReviewerInfo($reviewerInfo, $postData->level, $oldReviewerInfo); //审核人信息
        return $postData;
    }

    /**
     * 获得审核节点名称
     *
     * @param $level
     * @return array
     */
    public function getReviewNodeNameListByLevel($level){
        $reviewNodeCodeList = zget($this->lang->credit->reviewNodeCodeListGroupLevel, $level, []);
        $data = [];
        foreach ($reviewNodeCodeList as $nodeCode){
            $nodeName = zget($this->lang->credit->reviewNodeNameList, $nodeCode);
            $data[$nodeCode] = $nodeName;
        }
        return $data;
    }


    /**
     * 保存工作流
     *
     * @param $info
     * @param $reviewerInfo
     * @param $version
     * @return bool
     */
    public function saveWorkFlow($info, $reviewerInfo, $version){
        $res = false;
        $objectType = $this->config->credit->objectType;
        $objectId = $info->id;
        $reviewNodeNameList = $this->getReviewNodeNameListByLevel($info->level);
        $res = $this->loadModel('iwfp')->startWorkFlow($objectType, $objectId, $info->code, $info->createdBy, $reviewerInfo, $version, $reviewNodeNameList, $info->workflowId);
        if(!dao::isError()){
            $processInstanceId = $res->processInstanceId;
            $updateParams = new stdClass();
            $updateParams->workflowId = $processInstanceId;
            $this->dao->update(TABLE_CREDIT)->data($updateParams)->autoCheck()
                ->where('id')->eq($objectId)
                ->exec();
        }
        if(!dao::isError()){
            $res = true;
        }
        return $res;
    }


    /**
     * 检查是否允许编辑
     *
     * @param $info
     * @param $account
     * @return array
     */
    public function checkIsAllowEdit($info, $account){
        $res = array(
            'result'  => false,
            'message' => '',
        );
        if(!$info){
            $res['message'] = $this->lang->common->errorParamId;
            return $res;
        }

        //当前状态
        $status = $info->status;
        if(!in_array($status, $this->lang->credit->allowEditStatusArray)){
            $statusDesc = zget($this->lang->credit->statusList, $status);
            $res['message'] = sprintf($this->lang->credit->checkOpResultList['statusError'], $statusDesc, $this->lang->credit->edit);
            return $res;
        }
        $createdBy = $info->createdBy;
        $users = [$createdBy, 'admin'];
        if(!in_array($account, $users)){
            $res['message'] = sprintf($this->lang->credit->checkOpResultList['userError'], $this->lang->credit->edit);
            return $res;
        }
        $res['result'] = true;
        return $res;
    }

    /**
     * 检查操作权限
     *
     * @param $info
     * @param $action
     * @return bool|mixed
     */
    public static function isClickable($info, $action){
        global $app;
        $action = strtolower($action);
        $account = $app->user->account;
        $creditModel = new creditModel();

        if($action == 'edit') {
            $res = $creditModel->checkIsAllowEdit($info, $account);
            return $res['result'];
        }
        if($action == 'submit') {
            $res = $creditModel->checkIsAllowSubmit($info, $account);
            return $res['result'];
        }

        //审批
        if ($action == 'review') {
            $res = $creditModel->checkIsAllowReview($info, $account);
            return $res['result'];
        }
        //取消
        if ($action == 'cancel') {
            $res = $creditModel->checkIsAllowCancel($info, $account);
            return $res['result'];
        }

        //删除 创建人且待提交状态
        if ($action == 'delete')    {
            $res = $creditModel->checkIsAllowDelete($info, $account);
            return $res['result'];
        }
        return true;
    }

    /**
     * 是否允许删除
     *
     * @param $info
     * @param $account
     * @return array
     */
    public function checkIsAllowDelete($info, $account){
        $res = array(
            'result'  => false,
            'message' => '',
        );
        if(!$info){
            $res['message'] = $this->lang->common->errorParamId;
            return $res;
        }
        if($account == 'admin'){
            $res['result'] = true;
            return $res;
        }
        //当前状态
        $status = $info->status;
        if(!(in_array($status, $this->lang->credit->allowDeleteStatusArray) && ($info->version == 1))){
            $statusDesc = zget($this->lang->credit->statusList, $status);
            $res['message'] = sprintf($this->lang->credit->checkOpResultList['statusError'], $statusDesc, $this->lang->credit->delete);
            return $res;
        }
        $users = [$info->createdBy];
        if(!in_array($account, $users)){
            $res['message'] = sprintf($this->lang->credit->checkOpResultList['userError'], $this->lang->credit->delete);
            return $res;
        }
        $res['result'] = true;
        return $res;
    }

    /**
     * 是否允许取消
     *
     * @param $info
     * @param $account
     * @return array
     */
    public function checkIsAllowCancel($info, $account){
        $res = array(
            'result'  => false,
            'message' => '',
        );
        if(!$info){
            $res['message'] = $this->lang->common->errorParamId;
            return $res;
        }
        //当前状态
        $status = $info->status;
        if(in_array($status, $this->lang->credit->notAllowCancelStatusArray)){
            $statusDesc = zget($this->lang->credit->statusList, $status);
            $res['message'] = sprintf($this->lang->credit->checkOpResultList['statusError'], $statusDesc, $this->lang->credit->cancel);
            return $res;
        }
        $users = [$info->createdBy, 'admin'];
        if(!in_array($account, $users)){
            $res['message'] = sprintf($this->lang->credit->checkOpResultList['userError'], $this->lang->credit->cancel);
            return $res;
        }
        $res['result'] = true;
        return $res;
    }

    /**
     * 判断是否允许审批/处理
     *
     * @param $info
     * @param $account
     * @return array
     */
    public function checkIsAllowReview($info, $account){
        $res = array(
            'result'  => false,
            'message' => '',
        );
        if(!$info){
            $res['message'] = $this->lang->common->errorParamId;
            return $res;
        }
        //当前状态
        $status = $info->status;
        if(!in_array($status, $this->lang->credit->allowReviewStatusArray)){
            $statusDesc = zget($this->lang->credit->statusList, $status);
            $res['message'] = sprintf($this->lang->credit->checkOpResultList['statusError'], $statusDesc, $this->lang->credit->review);
            return $res;
        }
        $dealUsers = $info->dealUsers;
        $users = array_filter(explode(',', $dealUsers));
        $users[]  = 'admin';
        if(!in_array($account, $users)){
            $res['message'] = sprintf($this->lang->credit->checkOpResultList['userError'], $this->lang->credit->review);
            return $res;
        }
        $res['result'] = true;
        return $res;
    }

    /**
     * 编辑状态流转
     *
     * @param $creditInfo
     * @param $consumedId
     * @return bool
     */
    public function workLoadEdit($creditInfo, $consumedId){
        $consumed = fixer::input('post')->remove('comment')->get();
        if($this->post->before == ''){
            $isFirst = $this->loadModel('consumed')->checkIsFirstConsumed($consumedId, $creditInfo->id, 'credit');
            if(!$isFirst){
                $errors['before'] = sprintf($this->lang->credit->emptyObject, $this->lang->consumed->before);
                return dao::$errors = $errors;
            }
        }
        if($this->post->after == ''){
            $errors['after'] = sprintf($this->lang->credit->emptyObject, $this->lang->consumed->after);
            return dao::$errors = $errors;
        }
        $consumedInfo = $this->loadModel('consumed')->getById($consumedId);
        if(($consumed->before == $consumedInfo->before) && ($consumed->after == $consumedInfo->after)){
            $errors[''] = $this->lang->credit->checkOpResultList['noParamsChange'];
            return dao::$errors = $errors;
        }
        $isLast = $this->loadModel('consumed')->checkIsLastConsumed($consumedId, $creditInfo->id, 'credit');
        if($isLast && ($consumed->after != $consumedInfo->after)){ //最后一条信息且变更后状态发生了修改
            $response = $this->loadModel('iwfp')->freeJump($creditInfo->workflowId, $this->post->after, $creditInfo->version);
            if(dao::isError()){
                return false;
            }
            $updateData = array();
            $updateData['dealUsers'] = implode(',', $response->dealUser);
            $updateData['status'] = $response->status;
            $this->dao->update(TABLE_CREDIT)->data($updateData)->where('id')->eq($creditInfo->id)->exec();
        }
        $this->dao->update(TABLE_CONSUMED)->data($consumed)->where('id')->eq($consumedId)->exec();
    }

    /**
     *检查评审信息
     *
     * @param $params
     * @param $status
     * @return array|bool
     */
    public function checkReviewParams($params, $status){
        //检查结果
        $checkRes = false;
        $errorData = [];
        $requiredFields = explode(',', $this->config->credit->review->requiredFields);
        //检查参数是否必填
        foreach ($params as $key => $val){
            if(in_array($key, $requiredFields) && !$val){ //必填但是未填写
                $errorData[$key] = sprintf($this->lang->credit->emptyObject, $this->lang->credit->$key);
            }
        }
        if($status == $this->lang->credit->statusArray['waitcm']){ //cm处理，校验SVN路径是否存在
            if(mb_strlen($params->svnUrl) > 255){
                $errorData['svnUrl'] = sprintf($this->lang->credit->svnUrlLenhError, 255);
            }
        }elseif($status == $this->lang->credit->statusArray['waitconfirmresult']){ //校验交付时间和上线时间关系
            if(isset($params->status) && in_array($params->status, $this->lang->credit->needReasonEndStatusArray)){
                if(!isset($params->dealMessage) || !$params->dealMessage){
                    $errorData['dealMessage'] = sprintf($this->lang->credit->emptyObject, $this->lang->credit->dealMessage);
                }
            }
            if($params->status != 'cancel'){ //非变更取消需要填写项目上线时间
                 if(!$params->onlineTime){
                     $errorData['onlineTime'] = sprintf($this->lang->credit->emptyObject, $this->lang->credit->onlineTime);
                 }
            }

        }else{
            if(isset($params->dealResult) && $params->dealResult == 2){
                if(!isset($params->dealMessage) || !$params->dealMessage){
                    $errorData['dealMessage'] = sprintf($this->lang->credit->emptyObject, $this->lang->credit->dealMessage);
                }
            }
        }

        if(empty($errorData)){
            $checkRes = true;
        }
        //返回数据
        $data = [
            'checkRes'  => $checkRes,
            'errorData' => $errorData,
        ];
        return $data;
    }

    /**
     * 评审操作
     *
     * @param $creditId
     * @return mixed
     */
    public function review($creditId, $ismobile = false){
        $dealUser   = $this->app->user->account;
        $objectType = $this->config->credit->objectType;
        //获得征信交付信息
        $creditInfo = $this->getBasicInfoById($creditId);
        $res = $this->checkIsAllowReview($creditInfo, $dealUser);
        if(!$res['result']){
             dao::$errors[''] = $res['message'];
             return false;
        }
        //提交参数
        $postData = fixer::input('post')
            ->stripTags($this->config->credit->editor->review['id'], $this->config->allowedTags)
            ->get();
        $postData = $this->loadModel('file')->processImgURL($postData, $this->config->credit->editor->review['id'], $this->post->uid);

        $oldStatus = $creditInfo->status;
        //校验评审信息
        $res = $this->checkReviewParams($postData, $oldStatus);
        if(!$res['checkRes']){
            dao::$errors = $res['errorData'];
            return false;
        }

        $userVariableList = new stdClass();
        $userVariableList->level = $creditInfo->level;
        if($creditInfo->status == 'waitdept'){ //待部门审批，此时区分三级和非三级
            $userVariableList->level = $creditInfo->level == 3 ? $creditInfo->level: 1; //不是三级时当成1
        }

        $dealMessage = isset($_POST['dealMessage'])? $_POST['dealMessage'] :'';
        $dealResult = isset($postData->dealResult) ? $postData->dealResult : 1;
        $res = $this->loadModel('iwfp')->completeTaskWithClaim($creditInfo->workflowId, $dealUser, $dealMessage, $dealResult, $userVariableList, $creditInfo->version);
        if(dao::isError()){
            return false;
        }
        if($oldStatus == $this->lang->credit->statusArray['waitconfirmresult']){ //结束状态
            $nextStatus    = $postData->status;
            $nextDealUsers = '';
        }else{
            //下一状态
            $nextStatus = $res->toXmlTask;
            //下一状态处理人
            $nextDealUsers =  is_array($res->dealUser) ? implode(',', $res->dealUser): $res->dealUser;
        }

        //更新信息
        $currentTime = helper::now();
        $updateData = new stdClass();
        $updateData->status   = $nextStatus;
        $updateData->dealUsers = $nextDealUsers;
        $isCancel = false; //是否是变更取消操作
        if($oldStatus == $this->lang->credit->statusArray['waitcm']){ //待CM处理
            $updateData->svnUrl = $postData->svnUrl;
            $updateData->onLineFile = $postData->onLineFile;
        }elseif($oldStatus == $this->lang->credit->statusArray['waitconfirmresult']){ //待填写变更结果
            $updateData->onlineTime   = $postData->onlineTime;
            if($postData->status == 'cancel'){ //变更取消
                $isCancel = true; //填写变更结果时选择了变更取消
                $updateData->cancelBy     = $dealUser;
                $updateData->cancelDate   = $currentTime;
                $updateData->cancelReason = $dealMessage;
                $updateData->lastStatus   = $creditInfo->status;
                $updateData->onlineTime   = ''; //变更取消时没有上线时间
            }
        }

        //二线专员上一步审核
        if($nextStatus == $this->lang->credit->statusArray['waitproductsecond']){
            $updateData->deliveryTime = $currentTime;
        }
        //修改主表
        $this->dao->update(TABLE_CREDIT)->data($updateData)->where('id')->eq($creditId)->exec();
        //状态流转
        $this->loadModel('consumed')->record($objectType, $creditId, '0', $dealUser, $oldStatus, $nextStatus);
        //备注信息
        if($oldStatus == $this->lang->credit->statusArray['waitcm']){ //待CM处理
            $comment = '';
        }else if($oldStatus == $this->lang->credit->statusArray['waitconfirmresult']){ //待确认变更结论
            $comment = $this->lang->credit->modifyStatus.'：'.$this->lang->credit->statusList[$nextStatus].'<br>'.$this->lang->credit->dealMessage .'：' . $dealMessage;
        }else{
            $comment = $this->lang->credit->dealResult.'：'.$this->lang->credit->dealResultList[$dealResult].'<br>'.$this->lang->credit->dealMessage .'：' . $dealMessage;
        }

        //历史记录
        if($ismobile){
            $actionID = $this->loadModel('action')->create($objectType, $creditId, 'reviewed', $comment,'mobile');
        }else{
            $actionID = $this->loadModel('action')->create($objectType, $creditId, 'reviewed', $comment);
        }
        $changes =  common::createChanges($creditInfo, $updateData);
        if($changes){
            $this->action->logHistory($actionID, $changes);
        }

        if($nextStatus == $this->lang->credit->statusArray['waitproductsecond'] || $oldStatus == $this->lang->credit->statusArray['waitconfirmresult'] || $dealResult == 2){ //状态待确认变更结果、确认变更结果、退回

            if($creditInfo->secondorderIds){
                $secondorderIds = array_filter(explode(',', $creditInfo->secondorderIds));
                if($isCancel){
                    $secondorderStatus = 'todelivered'; //取消操作修改成待交付
                    foreach ($secondorderIds as $secondorderId){
                        $res = $this->loadModel('secondorder')->syncSecondorderStatus($secondorderId, $objectType, $creditId, '', $creditInfo->code, $secondorderStatus);
                    }
                }else{
                    foreach ($secondorderIds as $secondorderId){
                        $res = $this->loadModel('secondorder')->syncSecondorderStatus($secondorderId, $objectType, $creditId, $nextStatus, $creditInfo->code);
                    }
                }
            }

            //需求条目状态联动
            if($creditInfo->demandIds){
                $this->loadModel('demand')->changeBySecondLineV4($creditInfo->id, 'credit');
            }
        }
        return true;
    }

    /**
     * 取消操作
     *
     * @param $creditId
     * @return bool
     */
    public function cancel($creditId){
        $dealUser   = $this->app->user->account;
        $objectType = $this->config->credit->objectType;
        //获得征信交付信息
        $creditInfo = $this->getBasicInfoById($creditId);
        $dealUsers = array_filter(explode(',', $creditInfo->dealUsers));
        $res = $this->checkIsAllowCancel($creditInfo, $dealUser);
        if(!$res['result']){
            dao::$errors[''] = $res['message'];
            return false;
        }
        $oldStatus = $creditInfo->status;
        $postData = fixer::input('post')->get();
        //取消原因必填
        if(empty($postData->cancelReason)){
            dao::$errors['cancelReason'] =   sprintf($this->lang->credit->emptyObject, $this->lang->credit->cancelReason);
            return false;
        }
        if($oldStatus != $this->lang->credit->statusArray['waitsubmit'] && ($dealUser == 'admin' || in_array($dealUser, $dealUsers))){
            $userVariableList = new stdClass();
            $userVariableList->level = $creditInfo->level;
            $dealMessage = $postData->cancelReason;
            $dealResult = 4;
            $res = $this->loadModel('iwfp')->completeTaskWithClaim($creditInfo->workflowId, $dealUser, $dealMessage, $dealResult, $userVariableList, $creditInfo->version);
            if(dao::isError()){
                return false;
            }
        }
        $nextStatus = $this->lang->credit->statusArray['cancel'];  //下一状态
        $nextDealUsers =  '';  //下一状态处理人
        //更新信息
        $data = new stdClass();
        $data->status       = $nextStatus;
        $data->dealUsers    = $nextDealUsers;
        $data->cancelBy     = $dealUser;
        $data->cancelDate   = helper::now();
        $data->cancelReason = $postData->cancelReason;
        $data->lastStatus   = $creditInfo->status;
        $data->onlineTime   = ''; //变更取消时没有上线时间
        $res = $this->dao->update(TABLE_CREDIT)->data($data)->where('id')->eq($creditInfo->id)->exec();

        if($creditInfo->secondorderIds){
            $secondorderIds = $this->loadModel('secondline')->getRelationIds($objectType, $creditId, 'secondorder');
            if($secondorderIds){
                $secondorderStatus = 'todelivered'; //取消操作修改成待交付
                foreach ($secondorderIds as $secondorderId){
                    $res = $this->loadModel('secondorder')->syncSecondorderStatus($secondorderId, $objectType, $creditId, '', $creditInfo->code, $secondorderStatus);
                }
            }
        }


        //状态流转
        $this->loadModel('consumed')->record($objectType, $creditId, '0', $dealUser, $oldStatus, $nextStatus);
        //备注信息
        $comment = $this->lang->credit->cancelReason.'：'.$this->post->cancelReason;
        //历史记录
        $actionID = $this->loadModel('action')->create($objectType, $creditId, 'canceled', $comment);
        $changes =  common::createChanges($creditInfo, $data);
        if($changes){
            $this->action->logHistory($actionID, $changes);
        }
        return true;
    }

    /**
     * 删除操作
     *
     * @param string $creditId
     * @return bool|void
     */
    function deleteInfo($creditId){
        $dealUser   = $this->app->user->account;
        $objectType = $this->config->credit->objectType;
        //获得征信交付信息
        $creditInfo = $this->getBasicInfoById($creditId);
        $res = $this->checkIsAllowDelete($creditInfo, $dealUser);
        if(!$res['result']){
            dao::$errors[''] = $res['message'];
            return false;
        }
        $postData = fixer::input('post')->get();

        //删除操作
        $data = new stdClass();
        $data->deleted  = 1;
        $res = $this->dao->update(TABLE_CREDIT)->data($data)->where('id')->eq($creditInfo->id)->exec();
        //关联信息解绑
        $res = $this->loadModel('secondline')->cancelRelationship( $objectType, $creditId);

        $changes = common::createChanges($creditInfo, $data);
        return $changes;
    }


    /**
     * Get toList and ccList.
     *
     * @param $creditInfo
     * @param bool $isGetCcList
     * @return array
     */
    public function getToAndCcList($creditInfo, $isGetCcList = true)
    {
        /* Set toList and ccList. */
        /* 初始化发信人和抄送人变量，获取发信人和抄送人数据。*/
        $toList = '';
        $ccList = '';
        $status = $creditInfo->status;
        //待CM处理、待部门审批、待分管领导审批、待总经理审批、待产创处理
        if(in_array($status,$this->lang->credit->sendmailStatusList))
        {
            $toList = $creditInfo->dealUsers;
        }elseif(in_array($status, array_keys($this->lang->credit->endStatusList))){ //通知邮件
            $toList = $creditInfo->createdBy; //收件人是创建人
            if($isGetCcList){
                $nodes = $this->loadModel('iwfp')->getAllVersionReviewNodes($creditInfo->workflowId);
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
        }

        return array($toList, $ccList);
    }

    /**
     * sendmail
     *
     * @param  int    $creditID
     * @param  int    $actionID
     * @access public
     * @return void
     */
    public function sendmail($creditID, $actionID)
    {
        $this->loadModel('mail');
        $info   = $this->getById($creditID);
        $status = $info->status;
        $users = $this->loadModel('user')->getPairs('noletter');
        //部门信息
        $deptInfo = $this->loadModel('dept')->getByID($info->createdDept);

        /* 获取后台通知中配置的邮件发信。*/
        $this->app->loadLang('custommail');
        $mailConf   = isset($this->config->global->setCreditMail) ? $this->config->global->setCreditMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf   = json_decode($mailConf);

        if(in_array($status, array_keys($this->lang->credit->endStatusList)))
        {
            $statusDesc = zget($this->lang->credit->statusList, $status);
            $mailTitle  = vsprintf($this->lang->credit->noticeTitle, $statusDesc);
        }else{
            $mailTitle  = vsprintf($mailConf->mailTitle, $mailConf->variables);
        }

        /* Get action info. */
        $action          = $this->loadModel('action')->getById($actionID);
        $history         = $this->action->getHistory($actionID);
        $action->history = isset($history[$actionID]) ? $history[$actionID] : array();

        /* Get mail content. */
        $modulePath = $this->app->getModulePath($appName = '', 'credit');
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

        $sendUsers = $this->getToAndCcList($info);
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
    public function getXuanxuanTargetUser($obj, $objectType, $objectID, $actionType, $actionID, $actor = ''){
        $info   = $this->getById($objectID);
        $sendUsers = $this->getToAndCcList($info, false);

        if(!$sendUsers) return;
        $toList = $sendUsers[0];

        $server   = $this->loadModel('im')->getServer('zentao');
        //$url = $server.helper::createLink($objectType, 'view', "id=$objectID", 'html');

        $url = $server.'/credit-view-'.$objectID.'.html';
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

        return ['toList' => $toList,'subcontent' => $subcontent,'url' => $url,'title' => $title,'actions' => $actions];
    }


    /**
     * 获得工单关联的征信列表
     *
     * @param $secondorderID
     * @return array
     */
    public function getSecondorderRelatedCreditList($secondorderID){
        $data = [];
        if(!($secondorderID)){
            return $data;
        }
        $ret = $this->dao->select('id,code')
            ->from(TABLE_CREDIT)
            ->where("FIND_IN_SET($secondorderID, `secondorderIds`)")
            ->andWhere('deleted')->eq('0')
            ->fetchpairs();
        if($ret){
            $data = $ret;
        }
        return $data;
    }

    /**
     * 获得参与状态联动的数据列表
     *
     * @param $creditIds
     * @param $linkType
     * @return array
     */
    public function getTakeLinkStatusChangeCreditList($creditIds, $linkType){
        $data = [];
        if(!($creditIds && $linkType)){
            return $data;
        }
        $ret = $this->dao->select('id, code, status, actualEndTime,onlineTime, deliveryTime, dealUsers, abnormalId')
            ->from(TABLE_CREDIT)
            ->where('id')->in($creditIds)
            ->andWhere('status')->notIN('waitsubmit,cancel') //待提交、变更取消 已删除不做联动
            ->andWhere('deleted')->eq(0)//生产变更单解除状态联动后不做联动
            ->andWhere('abnormalId')->eq(0)
            ->beginIF($linkType == 'secondorder')->andWhere('secondorderCancelLinkage')->eq('0')->fi()
            ->fetchAll();
        if($ret){
            $data = $ret;
        }
        return $data;
    }


    /**
     * 获得被关联的列表
     *
     * @param $relateType
     * @param $relateId
     * @param string $select
     * @return array
     */
    public function getCreditListByRelatedId($relateType, $relateId, $select = '*'){
        $data = [];
        if(!($relateType && $relateId)){
            return $data;
        }
        $relateField = $relateType . 'Ids';
        $ret = $this->dao->select($select)
            ->from(TABLE_CREDIT)
            ->where("FIND_IN_SET($relateId, $relateField)")
            ->andWhere('deleted')->eq('0')
            ->andWhere('status')->notIN('waitsubmit,cancel') //待提交、变更取消 已删除不做联动
            ->andWhere('abnormalId')->eq(0)
            ->beginIF($relateType == 'secondorder')->andWhere('secondorderCancelLinkage')->eq('0')->fi()
            ->fetchAll();
        if($ret){
            $data = $ret;
        }
        return $data;
    }

    /**
     * 参与状态联动
     *
     * @param $creditId
     * @return array|bool
     */
    public function editSecondorderCancelLinkage($creditId){
        $objectType = $this->config->credit->objectType;
        //获得征信交付信息
        $select = 'id,code,status,secondorderIds, secondorderCancelLinkage';
        $creditInfo = $this->getBasicInfoById($creditId, $select);

        $postData = fixer::input('post')->get();
        if($postData->secondorderCancelLinkage == $creditInfo->secondorderCancelLinkage){ //无需修改
            dao::$errors[''] = $this->lang->credit->checkOpResultList['noParamsChange'];
            return false;
        }

        //修改是否参与状态联动
        $data = new stdClass();
        $data->secondorderCancelLinkage = $postData->secondorderCancelLinkage;
        $res = $this->dao->update(TABLE_CREDIT)->data($data)->where('id')->eq($creditInfo->id)->exec();

        if($creditInfo->secondorderIds){
            $status = $creditInfo->status;
            $secondorderIds = array_filter(explode(',', $creditInfo->secondorderIds));
            if($data->secondorderCancelLinkage == 0){ //修改为参与状态联动
                foreach ($secondorderIds as $secondorderId){
                    $res = $this->loadModel('secondorder')->syncSecondorderStatus($secondorderId, $objectType, $creditId, $status, $creditInfo->code);
                }
            }
        }

        $changes = common::createChanges($creditInfo, $data);
        return $changes;
    }
}
