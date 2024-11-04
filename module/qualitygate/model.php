<?php

class qualitygateModel extends model
{
    public function buildSearchForm($queryID, $actionURL){
        $this->config->qualitygate->search['actionURL'] = $actionURL;
        $this->config->qualitygate->search['queryID']   = $queryID;
        $this->loadModel('search')->setSearchParams($this->config->qualitygate->search);
    }

    /**
     * 获得查询列表
     *
     * @param $projectId
     * @param $browseType
     * @param $queryID
     * @param $orderBy
     * @param null $pager
     * @return array
     */
    public function getList($projectId, $browseType, $queryID, $orderBy, $pager = null){
        $qualitygateQuery = '';
        if($browseType == 'bysearch') {
            $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : '';
            if ($query) {
                $this->session->set('qualitygateQuery', $query->sql);
                $this->session->set('qualitygateForm', $query->form);
            }
            if ($this->session->qualitygateQuery == false) $this->session->set('qualitygateQuery', ' 1 = 1');

            $qualitygateQuery = $this->session->qualitygateQuery;
        }
        $qualitygateQuery = $this->addTableAlias($qualitygateQuery, 'code', 'zq', 'code');
        $qualitygateQuery = $this->addTableAlias($qualitygateQuery, 'productVersion', 'zq', 'productVersion');
        $qualitygateQuery = $this->addTableAlias($qualitygateQuery, 'name', 'zp', 'productName');
        $qualitygateQuery = $this->addTableAlias($qualitygateQuery, 'productCode', 'zq', 'productCode');
        $qualitygateQuery = $this->addTableAlias($qualitygateQuery, 'name', 'zb', 'buildName');
        $qualitygateQuery = $this->addTableAlias($qualitygateQuery, 'status', 'zb', 'buildStatus');
        $qualitygateQuery = $this->addTableAlias($qualitygateQuery, 'status', 'zq', 'status');
//        $account = $this->app->user->account;
        //查询列表
        $ret = $this->dao->select('zq.id, zq.code, zq.projectId, zq.productId, zq.productCode, zq.productVersion, zq.buildId, zq.status, zq.dealUser, zq.createdBy, zq.createdDept, zq.createdTime, zq.editedBy, zq.updateTime,
                                    zb.name as buildName, zb.status as buildStatus,
                                    zp.name as productName')
            ->from(TABLE_QUALITYGATE)->alias('zq')
            ->leftJoin(TABLE_BUILD)->alias('zb')->on('zq.buildId = zb.id')
            ->leftJoin(TABLE_PRODUCT)->alias('zp')->on('zq.productId = zp.id')
            ->where('zq.deleted')->eq('0')
            ->andWhere('zq.projectId')->eq($projectId)
            ->beginIF($browseType != 'all' && $browseType != 'bysearch')
            ->andWhere('zq.status')->eq($browseType)
            ->fi()
            ->beginIF($browseType == 'bysearch')
            ->andWhere($qualitygateQuery)
            ->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');

        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'qualitygate', $browseType != 'bysearch');

        foreach ($ret as $item) {
            // 质量门禁未关联制版前，制版名称、值班状态显示空
            if ($item->buildId == '0') {
                $item->buildName = null;
                $item->buildStatus = null;
            }
            $item->severityGate = $this->getSeverityGateResult($item->projectId, $item->productId, $item->productVersion, $item->buildId);
        }
        return $ret;
    }

    /**
     * 判断是否有创建权限
     *
     * @param $projectId
     * @param $account
     * @return bool
     */
    public function checkIsAllowCreate($projectId, $account){
        $isAllowCreate = false;
        if(!($projectId && $account)){
            return $isAllowCreate;
        }
        //创建人
        if($account == 'admin'){
            $isAllowCreate = true;
            return $isAllowCreate;
        }
        $severityRole = 9;
        $exWhere = " FIND_IN_SET('{$severityRole}', `role`) ";
        $projectTeamUsers = $this->loadModel('project')->getProjectTeamUsers($projectId, $exWhere);
        if(in_array($account, $projectTeamUsers)){
            $isAllowCreate = true;
        }
        return $isAllowCreate;
    }

    /**
     * 获的项目团队安全成员
     * $projectId
     *
     * @param $projectId
     * @param $isIncludeQualityGate
     * @param $qualityGateId
     * @return array
     */
     public function getProjectTeamSeverityUsers($projectId, $isIncludeQualityGate = false, $qualityGateId = 0){
         $data = [];
         if(!$projectId){
             return $data;
         }
         $severityRole = 9;
         $exWhere = " FIND_IN_SET('{$severityRole}', `role`) ";
         $projectTeamUsers = $this->loadModel('project')->getProjectTeamUsers($projectId, $exWhere);
         if($isIncludeQualityGate){
             if($qualityGateId){
                 $qualitygateInfo = $this->getBasicInfoById($qualityGateId, 'createdBy');
                 if($qualitygateInfo){
                     $projectTeamUsers[] = $qualitygateInfo->createdBy;
                 }
             }else{
                 $projectTeamUsers[] = $this->app->user->account;
             }
         }

         if(!$projectTeamUsers){
             return $data;
         }
         $data = $this->loadModel('user')->getUserListByAccounts($projectTeamUsers);
         return $data;
     }


    /**
     *获得质量门禁结果
     *
     * @param $projectId
     * @param int $productId
     * @param $productVersion
     * @param $buildId
     * @return int
     */
     public function getSeverityGateResult($projectId, $productId = 0, $productVersion = 0, $buildId = 0){
         $isQualityGateResult = 0;
         $qualityGateBugCount = $this->getQualityGateBugCount($projectId, $productId, $productVersion, $buildId);
         if($qualityGateBugCount == 0){
             return $isQualityGateResult;
         }
         $this->app->loadLang('bug');
         $childTypeComputer = $this->lang->bug->childTypeComputer;
         $exWhere = "status != 'closed' and ((severity in (1,2) and childType != '{$childTypeComputer}') or isBlackList = 2)";
         $unClosedQualityGateBugCount = $this->getQualityGateBugCount($projectId, $productId, $productVersion, $buildId, $exWhere);
         if($unClosedQualityGateBugCount > 0){
             $isQualityGateResult = 2; //不通过
         }else{
             $isQualityGateResult = 1; //通过
         }
         return $isQualityGateResult;
     }

    /**
     * 获得质量门禁bug数量
     *
     * @param $projectId
     * @param int $productId
     * @param $productVersion
     * @param $buildId
     * @param $exWhere
     * @return int
     */
    public function getQualityGateBugCount($projectId, $productId = 0, $productVersion = 0, $buildId = 0, $exWhere = ''){
        $qualityGateBugCount = 0;
        if(!$projectId){
            return $qualityGateBugCount;
        }
        if($buildId){
            $ret = $this->dao->select('count(id) as count')
                ->from(TABLE_BUILD_BUG_PHOTO)
                ->where('buildId')->eq($buildId)
                ->beginIF($exWhere)->andWhere($exWhere)->fi()
                ->fetch();
            if($ret){
                $qualityGateBugCount = $ret->count;
            }
        }
        if(!$qualityGateBugCount){
            $ret = $this->dao->select('count(id) as count')
                ->from(TABLE_BUG)
                ->where('deleted')->eq('0')
                ->andWhere('type')->eq('security')
                ->andWhere('project')->eq($projectId)
                ->beginIF($productId)->andWhere('product')->eq($productId)->fi()
                ->beginIF($productVersion && $productVersion == 1)->andWhere(" `linkPlan` in ('1', '') ")->fi()
                ->beginIF($productVersion && $productVersion != 1)->andWhere(" ( FIND_IN_SET('{$productVersion}',`linkPlan`) OR (`linkPlan` in ('1', ''))) ")->fi()
                ->beginIF($exWhere)->andWhere($exWhere)->fi()
                ->fetch();
            if($ret){
                $qualityGateBugCount = $ret->count;
            }
        }
        return $qualityGateBugCount;
     }

    /**
     *获得安全门禁未关闭的bug列表
     *
     * @param $projectId
     * @param int $productId
     * @param $productVersion
     * @return array
     */
     public function getSeverityGateUnClosedBugList($projectId, $productId = 0, $productVersion = ''){
        $data = [];
        if(!$projectId){
            return $data;
        }
         $this->app->loadLang('bug');
         $childTypeComputer = $this->lang->bug->childTypeComputer;
         $exWhere = "status != 'closed' and ((severity in (1,2) and childType != '{$childTypeComputer}') or isBlackList = 2)";
         $ret = $this->dao->select('id as bugId,title,severity,pri,status,openedBy,openedDate,type,childType,assignedTo,resolution,isBlackList')
             ->from(TABLE_BUG)
             ->where('deleted')->eq('0')
             ->andWhere('type')->eq('security')
             ->andWhere('project')->eq($projectId)
             ->beginIF($productId)->andWhere('product')->eq($productId)->fi()
             ->beginIF($productVersion && $productVersion == 1)->andWhere(" `linkPlan` in ('1', '') ")->fi()
             ->beginIF($productVersion && $productVersion != 1)->andWhere(" ( FIND_IN_SET('{$productVersion}',`linkPlan`) OR (`linkPlan` in ('1', ''))) ")->fi()
             ->andWhere($exWhere)
             ->fetchAll();
         if($ret){
             $data = $ret;
         }
         return $data;
     }


    /**
     * 创建质量门禁
     *
     * @param $postData
     * @return array
     */
     public function create($postData){
         if(isset($postData->createdBy) && $postData->createdBy){
             $currentUser = $postData->createdBy;
             $userInfo    = $this->loadModel('user')->getUserInfo($currentUser, 'dept');
             $currentDept = $userInfo->dept;
         }else{
             $currentUser = $this->app->user->account;
             $currentDept = $this->app->user->dept;
         }

         $currentTime = helper::now();
         //检查基本信息
         $res = $this->checkParams($postData, 'create');
         if (!$res['checkRes']) {
             dao::$errors = $res['errorData'];
             return dao::$errors;
         }
         //获得单号
         $code = $this->getCode();
         $postData->code = $code;
         $status = $postData->status;
         if($status == 'waitconfirm'){
             $postData->dealUser = $postData->severityTestUser;
         }
         //产品信息
         $productInfo = $this->loadModel('product')->getProductBasicInfo($postData->productId);
         $postData->productCode = isset($productInfo->code) ? $productInfo->code: '';
         $postData->createdBy   = $currentUser;
         $postData->createdDept = $currentDept;
         $postData->createdTime = $currentTime;
         //新增
         $this->dao->insert(TABLE_QUALITYGATE)->data($postData)->batchCheck($this->config->qualitygate->create->requiredFields, 'notempty')->exec();
         $recordId = $this->dao->lastInsertId();
         if (!dao::isError()) {
             $objectType = $this->config->qualitygate->objectType;
             if($status == 'waitconfirm'){ //添加工作流
                 $allReviewerInfo = $this->getAllReviewerInfo($postData);
                 $res = $this->loadModel('iwfp')->startWorkFlow($objectType, $recordId, $postData->code,  $postData->createdBy, $allReviewerInfo,'1', $this->lang->qualitygate->reviewNodeCodeNameList);
                 if(dao::isError()) {
                     dao::$errors = []; //重新赋值，否则数据库操作停止
                     $ret = $this->dao->update(TABLE_QUALITYGATE)->set('deleted')->eq('1')
                         ->where('id')->eq($recordId)
                         ->exec();
                     return dao::$errors[''] = $res;
                 }
                 $processInstanceId = $res->processInstanceId;
                 $updateParams = new stdClass();
                 $updateParams->workflowId = $processInstanceId;
                 $this->dao->update(TABLE_QUALITYGATE)->data($updateParams)->autoCheck()
                     ->where('id')->eq($recordId)
                     ->exec();

                 //从待提交到提交后进入待处理环节
                 $dealResult = '1';
                 $dealUser = $postData->createdBy;
                 $userVariableList = new stdClass();
                 $res = $this->loadModel('iwfp')->completeTaskWithClaim($processInstanceId, $dealUser, '', $dealResult, $userVariableList, 1);
                 if(dao::isError()) {
                     return $res;
                 }
             }

             //日志
             $this->loadModel('action')->create($objectType, $recordId, 'created');
             $this->loadModel('consumed')->record($objectType, $recordId, 0, $currentUser, '', $status, array());
         }
         return $recordId;
     }

    /**
     * 获得所有审核节点的审核人
     *
     * @param $qualityGateInfo
     * @return array
     */
     public function getAllReviewerInfo($qualityGateInfo){
         $allReviewerInfo = [
             'waitsubmit'  => [$qualityGateInfo->createdBy],
             'waitconfirm' => [$qualityGateInfo->dealUser],
         ];
         return $allReviewerInfo;
     }


    /**
     * 修改信息
     *
     * @param $qualityGateId
     * @param $postData
     * @param $isCheck
     * @return array|bool
     */
     public function update($qualityGateId, $postData, $isCheck = true){
         $op = 'edit';
         $objectType = $this->config->qualitygate->objectType;
         $currentUser = $this->app->user->account;
         $currentTime = helper::now();
         $account = $this->app->user->account;
         $info = $this->getBasicInfoById($qualityGateId);
         $oldStatus = $info->status;
         //检查信息
         $postData->id        = $qualityGateId;
         $postData->projectId =  $info->projectId;
         if(!isset($postData->buildId)){
             $postData->buildId = $info->buildId;
         }
         $postData->editedBy   = $currentUser;
         $postData->editedtime = $currentTime;

         if($isCheck){
             //检查是否允许更新
             $res = $this->checkIsAllowEdit($info, $account);
             if (!$res['result']) {
                 dao::$errors[] = $res['message'];
                 return false;
             }
             $res = $this->checkParams($postData, $op);
             if (!$res['checkRes']) {
                 dao::$errors = $res['errorData'];
                 return false;
             }

             //当前状态
             $status = $postData->status;
             $isCreateWorkFlow = false;
             $isCloseWorkFlow  = false;
             $isUpdateDealUser = false;
             if($status != $oldStatus){
                 if($status == 'waitconfirm'){
                     $postData->dealUser = $postData->severityTestUser;
                     //todo调取工作流
                     $isCreateWorkFlow = true;

                 }else{
                     $postData->dealUser = '';
                     if($oldStatus == 'waitconfirm'){
                         //todo关闭工作流
                         $isCloseWorkFlow  = true;
                     }
                 }

             }else{ //状态没有修改,修改了待处理人
                 if($status == 'waitconfirm' && ($postData->severityTestUser != $info->severityTestUser)){
                     $postData->dealUser = $postData->severityTestUser;
                     $isUpdateDealUser = true;
                 }

             }
             //产品信息
             if($postData->productId != $info->productId){
                 $productInfo = $this->loadModel('product')->getProductBasicInfo($postData->productId);
                 $postData->productCode = isset($productInfo->code) ? $productInfo->code: '';
             }

             $this->dao->update(TABLE_QUALITYGATE)
                 ->data($postData)->batchCheck($this->config->qualitygate->edit->requiredFields, 'notempty')->where('id')->eq($qualityGateId)
                 ->exec();
             if (!dao::isError()){
                 $dealResult = '1';
                 $userVariableList = new stdClass();
                 $version = 1;
                 if($isCreateWorkFlow){ //新建工作流
                     $newInfo = $this->getBasicInfoById($qualityGateId);
                     $allReviewerInfo = $this->getAllReviewerInfo($newInfo);
                     $res = $this->loadModel('iwfp')->startWorkFlow($objectType, $qualityGateId, $newInfo->code,  $newInfo->createdBy, $allReviewerInfo,'1', $this->lang->qualitygate->reviewNodeCodeNameList);
                     if(dao::isError()) {
                         return $res;
                     }

                     $processInstanceId = $res->processInstanceId;
                     $updateParams = new stdClass();
                     $updateParams->workflowId = $processInstanceId;
                     $this->dao->update(TABLE_QUALITYGATE)->data($updateParams)->autoCheck()
                         ->where('id')->eq($qualityGateId)
                         ->exec();
                     //从待提交到提交后进入待处理环节
                     $res = $this->loadModel('iwfp')->completeTaskWithClaim($processInstanceId, $newInfo->createdBy, '', $dealResult, $userVariableList, $version);
                     if(dao::isError()) {
                         return $res;
                     }
                 }
                 //关闭工作流
                 if($isCloseWorkFlow){
                     $res = $this->loadModel('iwfp')->completeTaskWithClaim($info->workflowId, $info->dealUser, '',$dealResult, $userVariableList, $version);
                     if(dao::isError()){
                         return $res;
                     }
                 }

                 //修改工作流处理人
                 if($isUpdateDealUser){
                     $res =  $this->loadModel('iwfp')->changeAssigneek($info->workflowId, $info->dealUser, $postData->dealUser, $version);
                     if(dao::isError()){
                         return $res;
                     }
                 }
             }
         }else{
             $this->dao->update(TABLE_QUALITYGATE)
                 ->data($postData)->batchCheck($this->config->qualitygate->edit->requiredFields, 'notempty')->where('id')->eq($qualityGateId)
                 ->exec();
         }
         if (!dao::isError()){
             $changes = common::createChanges($info, $postData);
             $actionID = $this->loadModel('action')->create($objectType, $qualityGateId, 'edited', '', '', '', true, true, $changes);
         }
         return true;
     }

    /**
     *设置质量门禁
     * @param $createTime
     * @return string
     */
    public function getCode($createTime = ''){
        if(!$createTime){
            $createTime = strtotime(helper::today());
        }
        $codePrefix = 'CFIT-QG-';
        $createDay = date('Ymd-', $createTime);
        $codeTemp = $codePrefix.$createDay;
        $number = $this->dao->select('count(id) c')->from(TABLE_QUALITYGATE)->where('code')->like($codeTemp."%")->fetch('c') ;
        $number = intval($number) + 1;
        $code   = $codeTemp . sprintf('%02d', $number);
        return $code;
    }

    /**
     * 校验信息
     *
     * @param $postData
     * @param string $op
     * @return array
     */
     public function checkParams($postData, $op = 'create'){
         $checkRes   = false;
         $errorData  = [];
         $data = [
             'checkRes'  => $checkRes,
             'errorData' => $errorData,
         ];
         if(!$postData){
             $errorData[] =  $this->lang->common->errorParamId;
             $data['errorData'] = $errorData;
             return $data;
         }
         //必填字段
         $requiredFields = explode(',', $this->config->qualitygate->$op->requiredFields);
         foreach ($requiredFields as $requiredField){
             if(!isset($postData->$requiredField) || !$postData->$requiredField){
                 $errorData[$requiredField] = sprintf($this->lang->error->notempty, $this->lang->qualitygate->$requiredField);
             }
         }

         if($postData->status == 'waitconfirm'){
             if(!$postData->severityTestUser){
                 $errorData['severityTestUser'] = $this->lang->qualitygate->checkOpResultList['severityTestUserEmptyError'];
             }
         }
         //基本信息
         if(!empty($errorData)){
             $data['errorData'] = $errorData;
             return $data;
         }

         //检查信息唯一性
         $projectId      = $postData->projectId ? $postData->projectId : 0;
         $productId      = $postData->productId ? $postData->productId : 0;
         $productVersion = $postData->productVersion ? $postData->productVersion : 1;
         $buildId        = isset($postData->buildId) ? $postData->buildId : 0;
         $notIncludeId   = isset($postData->id) ? $postData->id : 0;
         $info = $this->getOneQualityGateInfo($projectId, $productId, $productVersion, $buildId, $notIncludeId);
         if($info){
             if($buildId){
                 $errorData['buildId'] = $this->lang->qualitygate->checkOpResultList['buildIdExistError'];
             }else{
                 $errorData['productVersion'] = $this->lang->qualitygate->checkOpResultList['productVersionExistError'];
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
     *获得单条信息
     *
     * @param $id
     * @param $select
     * @return bool
     */
     public function getBasicInfoById($id, $select = '*'){
          if(!$id){
              return false;
          }
         $ret = $this->dao->select($select)
             ->from(TABLE_QUALITYGATE)
             ->where('deleted')->eq('0')
             ->andWhere('id')->eq($id)
             ->fetch();
          return $ret;
     }


    /**
     * 获得单条质量门禁信息
     *
     * @param $projectId
     * @param $productId
     * @param $productVersion
     * @param $buildId
     * @param int $notIncludeId
     * @param string $select
     * @return mixed
     */
     function getOneQualityGateInfo($projectId, $productId, $productVersion, $buildId, $notIncludeId = 0, $select = 'id'){
         $ret = $this->dao->select($select)
             ->from(TABLE_QUALITYGATE)
             ->where('deleted')->eq('0')
             ->andWhere('projectId')->eq($projectId)
             ->andWhere('productId')->eq($productId)
             ->andWhere('productVersion')->eq($productVersion)
             ->andWhere('buildId')->eq($buildId)
             ->beginIF($notIncludeId)->andWhere('id')->ne($notIncludeId)->fi()
             ->fetch();
         return $ret;
     }

    /**
     * 通过制版ID获得质量门禁信息
     *
     * @param $buildId
     * @param string $select
     * @return bool
     */
     public function getQualityGateInfoByBuildId($buildId, $select = 'id'){
         if(!$buildId){
             return false;
         }
         $ret = $this->dao->select($select)
             ->from(TABLE_QUALITYGATE)
             ->where('deleted')->eq('0')
             ->andWhere('buildId')->eq($buildId)
             ->fetch();
         return $ret;
     }

    /**
     * Project: chengfangjinke
     * Method: isClickable  'Button Highlight'
     * @param $qualitygate
     * @param $action
     * @return bool
     */
    public static function isClickable($qualitygate, $action)
    {
        global $app;
        global $lang;
        $account = $app->user->account;
        $qualitygateModel = new qualitygateModel();
//        $action = strtolower($action);
        switch ($action){
            case 'deal': //处理
                $dealUser = str_replace(' ', '', $qualitygate->dealUser);
                return ($app->user->account == $dealUser || $app->user->account == "admin") && in_array($qualitygate->status, $lang->qualitygate->allowDealStatusArr);
            case 'assignedTo': // 指派
                return common::hasPriv('qualitygate', 'assignedTo', $qualitygate)
                    && ($app->user->account == $qualitygate->dealUser || $app->user->account == 'admin')
                    && in_array($qualitygate->status, $lang->qualitygate->allowDealStatusArr);
            case 'edit': //编辑
                $res = $qualitygateModel->checkIsAllowEdit($qualitygate, $account);
                return $res['result'];
            case 'delete': //删除
                return $app->user->account == "admin";
            default:
                return true;
        }
    }

    /**
     * Project: chengfangjinke
     * Method: getByID
     * @param $qualityGateId
     * @return mixed
     */
    public function getById($qualityGateId)
    {
        $ret = $this->dao->select('zq.*,
        zb.name as buildName, zb.status as buildStatus,
        zpp.title as productVersionTitle, zpp.begin as productVersionBeginDate, zpp.end as productVersionEndDate, zpp.desc as productPlanDesc'
        )
        ->from(TABLE_QUALITYGATE)->alias('zq')
        ->leftJoin(TABLE_BUILD)->alias('zb')->on('zq.buildId = zb.id')
        ->leftJoin(TABLE_PRODUCTPLAN)->alias('zpp')->on('zq.productVersion = zpp.id')
        ->where('zq.id')->eq($qualityGateId)
        ->fetch();
        if($ret){
            //项目信息
            $projectInfo = $this->loadModel('project')->getProjectInfoById($ret->projectId, 'name');
            $ret->projectName = $projectInfo->name;
            //产品信息
            $productInfo = $this->loadModel('product')->getProductBasicInfo($ret->productId, 'name');
            $ret->productName = $productInfo->name;
            //安全问题
            $ret->severityGate = $this->getSeverityGateResult($ret->projectId, $ret->productId, $ret->productVersion, $ret->buildId);
        }

        return $ret;
    }

    /**
     * 是否允许报工
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
        $status = $info->status;
        if(!in_array($status, $this->lang->qualitygate->allowEditStatusArray)){
            $statusDesc = zget($this->lang->qualitygate->statusList, $status);
            $res['message'] = sprintf($this->lang->qualitygate->checkOpResultList['statusError'], $statusDesc, $this->lang->qualitygate->edit);

            return $res;
        }
        
        $allowUsers = ['admin', $info->createdBy];
        if(!in_array($account, $allowUsers)){
            $res['message'] = sprintf($this->lang->qualitygate->checkOpResultList['userError'], $this->lang->qualitygate->edit);
            return $res;
        }
        $res['result'] = true;
        return $res;
    }


    /**
     * sendmail
     *
     * @param $qualitygateId
     * @param $actionID
     * @return bool
     */
    public function sendmail($qualitygateId, $actionID)
    {
        $this->loadModel('mail');
        $info   = $this->getByID($qualitygateId);

        /* 获取后台通知中配置的邮件发信。*/
        $this->app->loadLang('custommail');
        $this->app->loadLang('build');

        $mailConf   = isset($this->config->global->setQualitygateMail) ? $this->config->global->setQualitygateMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf   = json_decode($mailConf);
        $mailTitle  = vsprintf($mailConf->mailTitle, $mailConf->variables);

        /* Get action info. */
        $action          = $this->loadModel('action')->getById($actionID);
        $history         = $this->action->getHistory($actionID);
        $action->history = isset($history[$actionID]) ? $history[$actionID] : array();
        $sendUsers = $this->getToAndCcList($info, $action);
        list($toList, $ccList) = $sendUsers;
        if(!$toList){
            return  true;
        }
        $userAccount = [$info->createdBy];
//        $users = $this->loadModel('user')->getUserListByAccounts($userAccount);
        $users = $this->loadModel('user')->getPairs('noletter|nodeleted');
        /* Get mail content. */
        $modulePath = $this->app->getModulePath($appName = '', 'qualitygate');
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
        $subject = $mailTitle;
        /* Send mail. */
        $this->mail->send($toList, $subject, $mailContent, $ccList);
        if($this->mail->isError()) trigger_error(join("\n", $this->mail->getError()));
    }

    /*
    *Get toList and ccList.
    *
    * @param object $info
    * @param object $action
    * @access public
    * @return bool|array
    */
    public function getToAndCcList($info, $actionInfo){
        $toList = '';
        $ccList = '';
        $status   = $info->status;
        $dealUser = $info->dealUser;
        $action  = $actionInfo->action;
        $history = $actionInfo->history;
        if(in_array($status, $this->lang->qualitygate->sendMailStatusArray)){
            if($dealUser){
                if($action == 'created'){ //创建
                    $toList = $dealUser;
                }else{
                    if($history){
                        $fields = array_column($history, 'field');
                        if(in_array('dealUser', $fields)){
                            $toList = $dealUser;  //待处理人发生了变更
                        }
                    }
                }
            }
        }
        return array($toList, $ccList);
    }

    /**
     * 是否需要设置安全测试接口人
     *
     * @param $projectID
     * @return bool
     */
    public function getIsSetQualityGate($projectID){
        $isSetSeverityTestUser = false;
        if(!$projectID){
            return $isSetSeverityTestUser;
        }
        //项目承担部门
        $projectPlanInfo = $this->loadModel('projectplan')->getPlanMainInfoByProjectID($projectID, 'bearDept');

        $bearDept = $projectPlanInfo->bearDept;
        if(in_array($bearDept, explode(',', $this->config->qualitygate->allowQualityGateDeptIds))){
            $projectInfo = $this->loadModel('project')->getProjectInfoById($projectID, 'isSafetyTest');
            if($projectInfo->isSafetyTest == 2){ //需要安全测试
                $isSetSeverityTestUser = true;
            }
        }
        return $isSetSeverityTestUser;

    }

    /**
     * Project: chengfangjinke
     * Method: printAssignedHtml: Change in style assigned by the processor
     * @param $qualitygate
     * @param $account
     * @return string
     */
    public function printAssignedHtml($qualitygate, $users)
    {
        $dealUser = trim( $qualitygate->dealUser);
        $curUser = $this->app->user->account;
        $assignedToText = !empty($dealUser) ? zget($users, $dealUser) : '';
        if ($qualitygate->status != $this->lang->qualitygate->statusArray['waitconfirm']) {
            return "<span style='padding-left: 21px'></span>";
        }
        if($curUser != $dealUser && $curUser != 'admin'){
            return "<span style='padding-left: 21px'>{$assignedToText}</span>";
        }

        $btnClass     = "iframe btn btn-icon-left btn-sm";
        $assignToLink = helper::createLink('qualitygate', 'assignedTo', "qualitygateId=$qualitygate->id", '', true);

        return html::a(
            $assignToLink,
            "<i class='icon icon-hand-right'></i> <span title='" . $assignedToText . "' class='text-primary'>{$assignedToText}</span>",
            '', "class='$btnClass'");
    }

    /**
     * Project: chengfangjinke
     * Method: isAssigned
     * @param $qualitygate
     * @return string
     */
    public function isAssigned($qualitygate)
    {
        if (!common::hasPriv('qualitygate', 'assignedTo', $qualitygate)) {
            return $this->lang->qualitygate->assignedAuthError;
        } else if (!($this->app->user->account == $qualitygate->dealUser || $this->app->user->account == 'admin')) {
            return $this->lang->qualitygate->assignedUserError;
        } else if (!in_array($qualitygate->status, $this->lang->qualitygate->allowDealStatusArr)) {
            return $this->lang->qualitygate->assignedStatusError;
        }
        return '';
    }

    /**
     * Project: chengfangjinke
     * Method: diffColorStatus
     * @param $status
     * @return string
     */
    public function diffColorStatus($status) {
        $label = '';
        switch ($status) {
            case 'waitconfirm':
                $label = '<span style="color: #d06666"><i class="icon icon-exclamation-sign"></i>  '.zget($this->lang->qualitygate->labelList, $status).'</span>';
                break;
            case 'finish':
                $label = '<span style="color: #3eaf2d"><i class="icon icon-check-circle"></i>  '.zget($this->lang->qualitygate->labelList, $status).'</span>';
                break;
            case 'noneedtest':
                $label = '<span style="color: #d5983d"><i class="icon icon-minuse-solid-circle"></i>  '.zget($this->lang->qualitygate->labelList, $status).'</span>';
                break;
        }
        return $label;
    }


    /**
     * Project: chengfangjinke
     * Method: diffSeverityGateResult
     * @param $status
     * @return string
     */
    public function diffSeverityGateResult($status) {
        $label = '';
        switch ($status) {
            case '0':
                $label = '<span style="color: #d5983d"><i class="icon icon-minuse-solid-circle"></i>  '.zget($this->lang->qualitygate->severityGateResultList, $status).'</span>';
                break;
            case '1':
                $label = '<span style="color: #3eaf2d"><i class="icon icon-check-circle"></i>  '.zget($this->lang->qualitygate->severityGateResultList, $status).'</span>';
                break;
            case '2':
                $label = '<span style="color: #d06666"><i class="iconfont icon-butongguo1"></i>  '.zget($this->lang->qualitygate->severityGateResultList, $status).'</span>';
                break;
        }
        return $label;
    }

    /**
     * Project: chengfangjinke
     * Method: deal
     * @param $qualitygateId
     * @return array
     */
    public function deal($qualitygateId)
    {
        $oldQualitygate = $this->getByID($qualitygateId);
        $data = fixer::input('post')
            ->get();

        $changes = common::createChanges($oldQualitygate, $data);
        if(!$changes) {
            return $changes;
        }
        $userVariableList = new stdClass();
        $dealResult = '1';
        $res = $this->loadModel('iwfp')->completeTaskWithClaim($oldQualitygate->workflowId, $oldQualitygate->dealUser, '', $dealResult, $userVariableList, $this->lang->qualitygate->approvalVersion);
        if(dao::isError()){
            return $res;
        }
        $this->dao->update(TABLE_QUALITYGATE)->data($data)->autoCheck()
            ->where('id')->eq($qualitygateId)
            ->exec();

        return $changes;
    }

    /**
     * Project: chengfangjinke
     * Method: addTableAlias
     * @param $qualitygateQuery
     * @param $field
     * @param $alias
     * @return array|mixed|string|string[]
     */
    public function addTableAlias($qualitygateQuery, $field, $tableAlias, $fieldAlias) {
        if(strpos($qualitygateQuery, $fieldAlias)){
            $qualitygateQuery = str_replace('AND `'.$fieldAlias, ' AND '.$tableAlias.'.`'.$field, $qualitygateQuery);
        }
        return $qualitygateQuery;
    }

    /**
     * @Notes:喧喧
     * @Date: 2024/9/25
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
        $info   = $this->getBasicInfoById($objectID);
        $action          = $this->loadModel('action')->getById($actionID);
        $history         = $this->action->getHistory($actionID);
        $action->history = isset($history[$actionID]) ? $history[$actionID] : array();
        $sendUsers = $this->getToAndCcList($info, $action);

        if(!$sendUsers) return;
        $toList = $sendUsers[0];

        $server   = $this->loadModel('im')->getServer('zentao');
        //$url = $server.helper::createLink($objectType, 'view', "id=$objectID", 'html');

        $url = $server.'/qualitygate-view-'.$objectID.'.html';
        $subcontent = [];
        $subcontent['headTitle']    = '';
        $subcontent['headSubTitle'] = '';
        $subcontent['count']        = 0;
        $subcontent['id']           = 0;
        $subcontent['parent']       = '';
        $subcontent['parentURL']    = "";
        $subcontent['cardURL']      = $url;
        $subcontent['name']         =  $info->code;//消息体 编号后边位置 标题

        //标题
        $title = '';
        $actions = [];
        return ['toList' => $toList,'subcontent' => $subcontent,'url' => $url,'title' => $title,'actions' => $actions];
    }
}
