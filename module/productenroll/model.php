<?php
class productenrollModel extends model
{
    public function create($productEnrollData)
    {
        $productEnrollData['code'] = $this->getCode();
        $this->dao->insert(TABLE_PRODUCTENROLL)
            ->data($productEnrollData)
            ->exec();
        $lastInsertID = $this->dao->lastInsertID();
        if(!dao::isError())
        {
            $defects = $this->dao->select('id,productenrollId,app')->from(TABLE_DEFECT)->where('id')->in(explode(',', $productEnrollData['fixDefect'] . $productEnrollData['leaveDefect']))->fetchAll();

            foreach ($defects as $defect)
            {
                $data = new stdClass();
                if(empty($defect->ifTest)) $data->ifTest = '0';
                $data->realtedApp = $productEnrollData['app'];
//                $data->CBPproject = $productEnrollData['CBPprojectId'];
                $data->productenrollCode = $productEnrollData['code'];
                $data->productenrollId =  $lastInsertID;
                $data->productenrollCreatedBy = $this->app->user->account;
                $this->dao->update(TABLE_DEFECT)->data($data)->where('id')->eq($defect->id)->exec();
            }
        }
        return $lastInsertID;
    }

    public function edit($id, $productEnrollData)
    {
        $this->app->loadLang('outwarddelivery');
        $oldData = $this->dao->select('cardStatus,`status`')->from(TABLE_PRODUCTENROLL)->where('id')->eq($id)->fetch();
        if(!in_array($oldData->status,$this->lang->outwarddelivery->alloweditStatus)) {  //不是待提交 和被拒绝的 不能编辑 新增内部未通过状态
            return true;
        }
        if($oldData->cardStatus == 1) { //外部通过 不能编辑
            return true;
        }
        $res = $this->dao->update(TABLE_PRODUCTENROLL)
            ->data($productEnrollData)
//            ->autoCheck()
            ->where('id')->eq((int)$id)->exec();
        if(!dao::isError())
        {
            $defects = $this->dao->select('id,app')->from(TABLE_DEFECT)->where('id')->in(explode(',', $productEnrollData['fixDefect'] . $productEnrollData['leaveDefect']))->fetchAll();

            foreach ($defects as $defect)
            {
                $data = new stdClass();
                $data->realtedApp = $productEnrollData['app'];
                $data->productenrollCode = $productEnrollData['code'];
//                $data->CBPproject = $productEnrollData['CBPprojectId'];
                if(empty($defect->ifTest)) $data->ifTest = '0';
                $data->productenrollId =  $id;
                $this->dao->update(TABLE_DEFECT)->data($data)->where('id')->eq($defect->id)->exec();
            }
        }
        return $res;
    }


    /**
     * 更新产品登记状态
     * @param $outwarddelivery
     * @param $id
     * @param int $cardStatus
     * @param string $returnPerson
     * @param string $returnCase
     * @param string $emisRegisterNumber
     * @return mixed
     */
    public function updateStatus($outwardDelivery, $id, int $cardStatus, string $returnPerson ='', string $returnCase = '', string $emisRegisterNumber = '')
    {
        $productEnroll['cardStatus'] = $cardStatus;
        $productEnroll['returnPerson'] = $returnPerson;
        $productEnroll['returnCase'] = $returnCase;
        $productEnroll['emisRegisterNumber'] = $emisRegisterNumber;
        $productEnroll['returnDate'] = date('Y-m-d H:i:s');
        if($cardStatus == 1){ //子表单接口通过 将version改为父表单的version
          $productEnroll['status'] = 'emispass';
        }elseif($cardStatus == 0){
          $productEnroll['status'] = 'productenrollreject';
        }elseif($cardStatus == 2){          
            $versionNumber = $this->dao->select('version')->from(TABLE_OUTWARDDELIVERY)
            ->where('productEnrollId')->eq($id)
            ->andWhere('isNewProductEnroll')->eq(1)
            ->fetch('version');
          $productEnroll['version'] = $versionNumber;
          $productEnroll['status'] = 'giteepass';
        }
        $res = $this->dao->update(TABLE_PRODUCTENROLL)
            ->data($productEnroll)
            ->beginIF($cardStatus == 0)->set(", returnTimes = returnTimes+1")->fi()
            ->where('id')->eq((int)$id)->exec();
        //查询所有关联的父表单
        $outwardDeliveryList = $this->dao->select('id,version,reviewStage,isNewProductEnroll,isNewModifycncc,modifycnccId')->from(TABLE_OUTWARDDELIVERY)->where('productEnrollId')->eq((int)$id)->fetchALl();
        foreach($outwardDeliveryList as $item){
            if($cardStatus == 2){
                //设置父表单的状态
                if($item->isNewModifycncc == 0 and $item->status != 'cancel'){
                    $this->dao->update(TABLE_OUTWARDDELIVERY)->set('status = "productenrollpass"')->where('id')->eq((int)$item->id)->exec();  
                }

                // 有后续子表单审批变为生产变更，无后续子表单审批变为空
                if($item->status != 'cancel'){
                    if($item->modifycnccId != 0){
                        $this->dao->update(TABLE_OUTWARDDELIVERY)->set('currentReview="4"')->where('id')->eq((int)$item->id)->exec();
                    }else{
                        $this->dao->update(TABLE_OUTWARDDELIVERY)->set('currentReview="5",dealUser=""')->where('id')->eq((int)$item->id)->exec();
                    }
                }
            }elseif($cardStatus == 0){

                // 单子打回如果是非关联的单子当前审批进入对外交付，否者对外交付不变
                if($item->isNewProductEnroll == 1 and $item->status != 'cancel'){
                    $this->loadModel('outwarddelivery');
                    $this->app->loadLang('outwarddelivery');
                    $reviewers = $this->lang->outwarddelivery->apiDealUserList['userAccount'];
                    $this->dao->update(TABLE_OUTWARDDELIVERY)->set('currentReview="1",status="productenrollreject"')->where('id')->eq($item->id)->exec();
                    $this->dao->update(TABLE_OUTWARDDELIVERY)->set('dealUser')->eq($reviewers)->where('id')->eq($item->id)->exec();
                }
            }
        }
        
        //状态流转
        if($item->status != 'cancel'){
            $this->loadModel('consumed')->record('outwarddelivery', $outwardDelivery->id, 0, 'guestcn','withexternalapproval', $productEnroll['status'], array(), '产品登记单');
        }
        $logObj = $this->dao->select('*')->from(TABLE_REQUESTLOG)
        ->where('objectType')->eq('productenroll')
        ->andwhere('objectId')->eq($id)
        ->andwhere('purpose')->eq('productenrollfeedback')
        ->andwhere('status')->eq('success')
        ->fetch();
        if(empty($logObj) || count($logObj) <= 1){
            $action = 'productenrollsyncfeedback';
        }else{
            $action = 'productenrolleditfeedback';
        }
        $this->loadModel('action')->create('productenroll', $id, $action, $returnCase,'','guestjk');
        if($item->status != 'cancel'){
            $this->loadModel('action')->create('outwarddelivery', $outwardDelivery->id, $action, $returnCase,'','guestjk');
        }


        return $res;
    }
    public function view($id)
    {
        return $this->dao->select('*')->from(TABLE_PRODUCTENROLL)->where('id')->eq($id)->fetch($id);
    }
    /**
     * 生成code
     * @return string
     */
    public function getCode()
    {
        $prefix   = 'CFIT-RQ-'. date('Ymd-');
        $number = $this->dao->select('count(id) c')->from(TABLE_PRODUCTENROLL)->where('code')->like("$prefix%")->fetch('c') + 1;
        $code   = $prefix . sprintf('%02d', $number);
        return $code;
    }

    public function getList($browseType, $queryID, $orderBy, $pager = null)
    {
        $productEnrollQuery = '';
        if($browseType == 'bysearch')
        {
            $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : '';
            if($query)
            {
                $this->session->set('productEnrollQuery', $query->sql);
                $this->session->set('productEnrollForm', $query->form);
            }
            if($this->session->productEnrollQuery == false) $this->session->set('productEnrollQuery', ' 1 = 1');
            $productEnrollQuery = $this->session->productEnrollQuery;
            $productEnrollQuery = str_replace('AND `', ' AND `t1.', $productEnrollQuery);
            $productEnrollQuery = str_replace('AND (`', ' AND (`t1.', $productEnrollQuery);
            $productEnrollQuery = str_replace('`', '', $productEnrollQuery);

            // 处理[系统分类]搜索字段
            if(strpos($productEnrollQuery, 't1.isPayment') !== false)
            {
                $productEnrollQuery = str_replace('t1.isPayment', "t2.isPayment", $productEnrollQuery);
            }

            // 处理[承建单位]搜索字段
            if(strpos($productEnrollQuery, 't1.team') !== false)
            {
                $productEnrollQuery = str_replace('t1.team', "t2.team", $productEnrollQuery);
            }

            // 处理[关联对外交付]搜索字段
            if(isset($productEnrollQuery) && strpos($productEnrollQuery, 't1.relatedOutwardDelivery') !== false)
            {
                $productEnrollQuery = str_replace('t1.relatedOutwardDelivery', "t6.code", $productEnrollQuery);
            }

            // 处理[关联测试申请]搜索字段
            if(strpos($productEnrollQuery, 't1.relatedTestingRequest') !== false)
            {
                $productEnrollQuery = str_replace('t1.relatedTestingRequest', "t4.code", $productEnrollQuery);
            }

            // 处理[关联生产变更申请]搜索字段
            if(strpos($productEnrollQuery, 't1.relatedModifycncc') !== false)
            {
                $productEnrollQuery = str_replace('t1.relatedModifycncc', "t5.code", $productEnrollQuery);
            }

            if(strpos($productEnrollQuery, 't1.planDistributionTime') !== false)
            {
                $productEnrollQuery = str_replace('t1.planDistributionTime', "from_unixtime(LEFT(t1.planDistributionTime,length(t1.planDistributionTime) - 3))", $productEnrollQuery);
            }

            if(strpos($productEnrollQuery, 't1.planUpTime') !== false)
            {
                $productEnrollQuery = str_replace('t1.planUpTime', "from_unixtime(LEFT(t1.planUpTime,length(t1.planUpTime) - 3))", $productEnrollQuery);
            }

            if(strpos($productEnrollQuery, ',app') !== false){
                $productEnrollQuery = str_replace(',app', ",t1.app", $productEnrollQuery);
            }
            if(strpos($productEnrollQuery, 'requirementId') !== false){
                $productEnrollQuery = str_replace('requirementId', "t1.requirementId", $productEnrollQuery);
            }
            if(strpos($productEnrollQuery, 'demandId') !== false){
                $productEnrollQuery = str_replace('demandId', "t1.demandId", $productEnrollQuery);
            }
            if(strpos($productEnrollQuery, 'problemId') !== false){
                $productEnrollQuery = str_replace('problemId', "t1.problemId", $productEnrollQuery);
            }
            if(strpos($productEnrollQuery, 'secondorderId ') !== false){
                $productEnrollQuery = str_replace('t1.secondorderId', "CONCAT(',', t1.secondorderId, ',')", $productEnrollQuery);
            }

        }

        $data = $this->dao->select('distinct t1.*,t3.code as outwarddeliveryCode,t3.id as outwarddeliveryId')->from(TABLE_PRODUCTENROLL)->alias('t1')
            ->leftJoin(TABLE_APPLICATION)->alias('t2')
            ->on('t1.app=t2.id')
            ->leftJoin(TABLE_OUTWARDDELIVERY)->alias('t3')
            ->on('t1.id=t3.productEnrollId and t3.isNewProductEnroll=1')
            ->leftJoin(TABLE_TESTINGREQUEST)->alias('t4')
            ->on('t3.testingRequestId=t4.id')
            ->leftJoin(TABLE_MODIFYCNCC)->alias('t5')
            ->on('t3.modifycnccId=t5.id')
            ->leftJoin(TABLE_OUTWARDDELIVERY)->alias('t6')
            ->on('t1.id=t6.productEnrollId or (t3.testingRequestId = t6.testingRequestId and t6.isNewTestingRequest = 1)')
            ->where('t1.deleted')->ne(1)
            ->beginIF($browseType == 'closed')->andWhere('t1.closed')->eq(1)->fi()
            ->beginIF($browseType != 'all' and $browseType != 'bysearch' and $browseType != 'closed')->andWhere('t1.status')->eq($browseType)->andWhere('t1.closed')->ne(1)->fi()
            ->beginIF($browseType == 'bysearch')->andWhere($productEnrollQuery)->fi()
            ->orderBy($orderBy)
            ->page($pager,'t1.id')
            ->fetchALl('id');
        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'productEnroll', $browseType != 'bysearch');
        return $data;
    }

    public function getByID($id)
    {
        if(empty($id)) return [];
        $data = $this->dao->select('*')->from(TABLE_PRODUCTENROLL)
            ->where('id')->eq($id)
            ->andwhere('deleted')->eq(0)
            ->fetch();
        $data = $this->loadModel('file')->replaceImgURL($data, 'introductionToFunctionsAndUses,remark');
        if(!empty($data->mediaInfo)){
            $data->mediaInfo = json_decode($data->mediaInfo, true); //介质信息名 bytes
        }
        if(!empty($data->planDistributionTime)){
            $data->planDistributionTime = date("Y-m-d H:i:s", $data->planDistributionTime/1000);
        }
        if(!empty($data->planUpTime)){
            $data->planUpTime =  date("Y-m-d H:i:s", $data->planUpTime/1000);
        }
        $this->loadModel('outwarddelivery')->resetNodeAndReviewerName($data->createdDept);
        return $data;
    }

    /**
     * TongYanQi 2022/11/26
     * 获取全部
     */
    public function getAll()
    {
        $data = $this->dao->select('*')->from(TABLE_PRODUCTENROLL)
            ->fetchall();

        return $data;
    }
    public function getByCode($code)
    {
        if(empty($code)) return [];
        $data = $this->dao->select('*')->from(TABLE_PRODUCTENROLL)
            ->where('code')->eq($code)
            ->andwhere('deleted')->eq(0)
            ->fetch();
        $data = $this->loadModel('file')->replaceImgURL($data, 'introductionToFunctionsAndUses,remark');
        if(!empty($data->mediaInfo)){
            $data->mediaInfo = json_decode($data->mediaInfo, true); //介质信息名 bytes
        }
        if(!empty($data->planDistributionTime)){
            $data->planDistributionTime = date("Y-m-d H:i:s", $data->planDistributionTime/1000);
        }
        if(!empty($data->planUpTime)){
            $data->planUpTime =  date("Y-m-d H:i:s", $data->planUpTime/1000);
        }
        return $data;
    }


    public function fixProductEnrollData($update = 0)
    {
        $postData = fixer::input('post')
            ->join('app', ',')
            ->join('problemId', ',')
            ->join('productId', ',')
            ->join('demandId', ',')
            ->join('secondorderId', ',')
            ->join('requirementId', ',')
            ->join('projectPlanId', ',')
            ->join('CBPprojectId', ',')
            ->join('fixDefect', ',')
            ->join('leaveDefect', ',')
            ->get();
        if($update == 0){
            $fixData['createdBy'] = $this->app->user->account;
            $fixData['createdDate'] = helper::now();
            $fixData['createdDept'] = $this->app->user->dept;
            $fixData['code'] = $this->getCode();
            $fixData['version']   = 1;
        }
        $fixData['editedBy'] = $this->app->user->account;
        $fixData['editedDate'] = helper::now();
        $fixData['title'] = $postData->title ?? '';
        $fixData['isPlan'] = $postData->isPlan ?? '';
        $fixData['planProductName'] = $postData->planProductName ?? '';
        $fixData['issueId'] = $postData->issueId ?? 0;
        $fixData['testingRequestCode'] = $postData->testingRequestCode ?? '';
        $fixData['contactName'] = $this->app->user->realname ?? '';
        $fixData['contactTel'] = $postData->applyUsercontact ?? '';
        $fixData['contactEmail'] =  $this->app->user->email ?? '';
        $fixData['implementationForm'] = $postData->implementationForm ?? '';
        $fixData['implementationDepartment'] = $postData->implementationDepartment ?? '';
        $fixData['productCode'] = $postData->productCode  ?? '';
        $fixData['productName'] = $postData->productName  ?? '';
        $fixData['versionNum'] = $postData->versionNum ?? '';
        $fixData['lastVersionNum'] = $postData->lastVersionNum ?? '';
        $fixData['projectCode'] = $postData->projectCode ?? '';
        $fixData['projectName'] = $postData->projectName ?? '';
        $fixData['lastEOStime'] = $postData->lastEOStime ?? '';
        $fixData['cdCode'] = $postData->cdCode ?? '';
        $fixData['registeredCdNum'] = $postData->registeredCdNum ?? 0;
        $fixData['cdType'] = $postData->cdType ?? '';
        $fixData['fixDefect'] = $postData->fixDefect ?? '';
        $fixData['leaveDefect'] = $postData->leaveDefect ?? '';

//        $fixData['attchment'] = $postData->attchment;
        $fixData['remoteFilePath'] = $postData->remoteFilePath  ?? '';
        //$fixData['app'] = $postData->app  ?? '';
        if(!empty($postData->app)){
            $appArray = explode(',', str_replace(' ', '', $postData->app));
            $apps = ",";
            foreach ($appArray as $item) {
                if(!empty($item)){
                    $apps =  $apps.$item.",";
                }
            }
            $fixData['app'] = $apps;
        }else{
            $fixData['app'] = '';
        }
        //$fixData['problemId'] = $postData->problemId ?? '';
        if(!empty($postData->problemId)){
            $problemIdArray = explode(',', str_replace(' ', '', $postData->problemId));
            $problemIds = ",";
            foreach ($problemIdArray as $item) {
                if(!empty($item)){
                    $problemIds =  $problemIds.$item.",";
                }
            }
            $fixData['problemId'] = $problemIds;
        }else{
            $fixData['problemId'] = '';
        }

        if(!empty($postData->secondorderId)){
            $secondorderIdArray = explode(',', str_replace(' ', '', $postData->secondorderId));
            $secondorderIds = ",";
            foreach ($secondorderIdArray as $item) {
                if(!empty($item)){
                    $secondorderIds =  $secondorderIds.$item.",";
                }
            }
            $fixData['secondorderId'] = $secondorderIds;
        }else{
            $fixData['secondorderId'] = '';
        }

        /*$fixData['productId'] = $postData->productId ?? '';*/
        if(!empty($postData->productId)){
            $productIdArray = explode(',', str_replace(' ', '', $postData->productId));
            $productIds = ",";
            $productLineArray = array();
            $appArray = array();
            foreach ($productIdArray as $item) {
                if(!empty($item)){
                    $productIds =  $productIds.$item.",";
                    $product = $this->dao->select('line,app')->from(TABLE_PRODUCT)
                        ->where('id')->eq($item)
                        ->fetch();
                    if(!in_array($product->line, $productLineArray)){
                        array_push($productLineArray, $product->line);
                    }
                    if(!in_array($product->app, $appArray)){
                        array_push($appArray, $product->app);
                    }
                }
            }
            $fixData['productId'] = $productIds;
            $fixData['productLine'] = implode(',',$productLineArray);
            //$fixData['app'] = implode(',',$appArray);
        }else{
            $fixData['productId'] = '';
            $fixData['productLine'] = '';
            //$fixData['app'] = '';
        }
        //$fixData['demandId'] = $postData->demandId ?? '';
        if(!empty($postData->demandId)){
            $demandIdArray = explode(',', str_replace(' ', '', $postData->demandId));
            $demandIds = ",";
            foreach ($demandIdArray as $item) {
                if(!empty($item)){
                    $demandIds =  $demandIds.$item.",";
                }
            }
            $fixData['demandId'] = $demandIds;
        }else{
            $fixData['demandId'] = '';
        }
        //$fixData['requirementId'] = $postData->requirementId ?? '';
        if(!empty($postData->requirementId)){
            $requirementIdArray = explode(',', str_replace(' ', '', $postData->requirementId));
            $requirementIds = ",";
            foreach ($requirementIdArray as $item) {
                if(!empty($item)){
                    $requirementIds =  $requirementIds.$item.",";
                }
            }
            $fixData['requirementId'] = $requirementIds;
        }else{
            $fixData['requirementId'] = '';
        }
        $fixData['projectPlanId'] = $postData->projectPlanId ?? '';
        $fixData['CBPprojectId'] = $postData->CBPprojectId ?? '';
        $fixData['planSoftwareName'] = $postData->planSoftwareName ?? '';
        $fixData['platform'] = $postData->platform ?? '';
        $fixData['checkDepartment'] = $postData->checkDepartment ?? '';
        $fixData['result'] = $postData->result ?? '';
        $fixData['installationNode'] = $postData->installationNode ?? '';
        /*$fixData['productLine'] = $postData->productLine ?? '';*/
        $fixData['optionSystem'] = $postData->optionSystem ?? '';
        $fixData['planDistributionTime'] = isset($postData->planDistributionTime) ? strtotime($postData->planDistributionTime).'000' :'';
        $fixData['planUpTime'] = isset($postData->planUpTime) ? strtotime($postData->planUpTime).'000' : '';
        $fixData['softwareProductPatch'] = $postData->softwareProductPatch ?? '';
        $fixData['reasonFromJinke'] = $postData->reasonFromJinke ?? '';
        $fixData['introductionToFunctionsAndUses'] = $postData->introductionToFunctionsAndUses ?? '';
        $fixData['remark'] = $postData->remark ?? '';
        $fixData['softwareCopyrightRegistration'] = $postData->softwareCopyrightRegistration ?? '';
        //$fixData['applyTime'] = $postData->applyTime ?? '';
        $fixData['productenrollDesc'] = $postData->productenrollDesc ?? '';
        $fixData['dynacommEn'] = $postData->dynacommEn ?? '';
        $fixData['dynacommCn'] = $postData->dynacommCn ?? '';
        $fixData['status']    = $postData->status ?? "waitsubmitted";
        $fixData['pushStatus']    = 0;
        $fixData['returnCase'] = '';
        $fixData['cardStatus'] = '';
        $fixData['returnDate'] = '';



        //介质名和bytes
        if(!empty($postData->mediaName) && is_array($postData->mediaName)){
            $mediaInfo = [];
            $i = 0;
            foreach ($postData->mediaName as $mediaName){
                $media['name'] = $mediaName;
                $media['bytes'] = $postData->mediaBytes[$i] ?? 0;
                $mediaInfo[] = $media;
                $i++;
            }
            $fixData['mediaInfo'] = json_encode($mediaInfo);
        }
        if ($postData->issubmit != 'save'){
            $this->checkParams($fixData, $this->config->productenroll->create->requiredFields);
        }
        return $fixData;
    }

    /**
     * 检查必填项
     * @param $data
     */
    private function checkParams($data, $fields)
    {
        $fieldArray = explode(',', str_replace(' ', '', $fields));
        foreach ($fieldArray as $item)
        {
            if(is_null(strip_tags(str_replace("&nbsp;"," ",htmlspecialchars_decode($data[$item])))) || strip_tags(str_replace("&nbsp;"," ",htmlspecialchars_decode($data[$item]))) == ''){
                $itemName = $this->lang->productenroll->$item ?? $item;
                dao::$errors[$item] =  sprintf($this->lang->productenroll->emptyObject, $itemName);
            }
        }

        //介质名和bytes
        if(!empty($postData->mediaName) && is_array($postData->mediaName)){
            $mediaInfo = [];
            $i = 0;
            foreach ($postData->mediaName as $mediaName){
                if(!is_int($postData->mediaBytes[$i])){
                    dao::$errors[] =  '【产品介质及字节数】字节数必须是整数';
                    break;
                }
                $i++;
            }
        }
    }

    public function getPairs($outwarddeliveryId = '')
    {
        return $this->dao->select('zt.id,concat(zt.code,"（",zt.productenrollDesc ,"）")')->from(TABLE_PRODUCTENROLL)->alias('zt')
            ->innerjoin(TABLE_OUTWARDDELIVERY)->alias('zo')
            ->on('zt.id = zo.productEnrollId and zo.isNewProductEnroll = 1')
            ->where('zt.deleted')->ne(1)
            ->andwhere('zo.id')->ne($outwarddeliveryId)
            ->orderBy('zt.id_desc')
            ->fetchPairs();
    }

    public function getCodeGiteePairs()
    {
      return $this->dao->select('id,concat(code,"（",IFNULL(giteeId,"")  ,"）")')->from(TABLE_PRODUCTENROLL)
        ->where('deleted')->ne(1)
        ->orderBy('id_desc')
        ->fetchPairs('id');
    }

    public function getCodePairs()
    {
        return $this->dao->select('id,code')->from(TABLE_PRODUCTENROLL)
            ->where('deleted')->ne(1)
            ->orderBy('id_desc')
            ->fetchPairs();
    }
    public function getCodeById($id)
    {
        return $this->dao->select('code')->from(TABLE_PRODUCTENROLL)->where('id')->eq($id)->fetch('code') . "";
    }


    public function testTimingTask(){
      $data['title'] = 'fff';
      $this->dao->update(TABLE_PRODUCTENROLL)
        ->data($data)
        ->where('id')->eq(1)->exec();
    }

    //是否介质满住条件
    public function checkMediaFails($outwardDelivery)
    {
        /** @var outwarddeliveryModel $outwardDeliveryModel */
        $outwardDeliveryModel = $this->loadModel('outwarddelivery');
        $reviewers = $this->lang->outwarddelivery->apiDealUserList['userAccount'];
        $pushFailMax = $this->loadModel('release')->getFailsQz($outwardDelivery->release);
        if($pushFailMax){ //介质推送失败多次 直接跳过
            $action = 'qingzongsynfailed';
            $update['pushStatus'] = -1;
            $update['status'] = 'qingzongsynfailed'; //3次失败后 改为同步失败 不再重复发
            $this->dao->update(TABLE_PRODUCTENROLL)->data($update)->where('id')->eq((int)$outwardDelivery->productEnrollId)->exec();
            $this->loadModel('action')->create('productenroll', $outwardDelivery->productEnrollId, $action, '介质同步失败多次','guestjk');
            $outwardDeliveryModel->setOutwardDeliverySyncFail($outwardDelivery->id); //更改同步失败状态
            $this->loadModel('consumed')->record('outwarddelivery', $outwardDelivery->id, '0', 'guestjk', 'waitqingzong', 'qingzongsynfailed', array(),'介质多次同步失败');
            $this->loadModel('action')->create('outwarddelivery', $outwardDelivery->id, $action, '介质多次同步失败','', $reviewers);
            return false;
        }

        if($this->loadModel('release')->ifReleasesPushed($outwardDelivery->release) == false) { return false; } //介质未处理完 不推单子
        return true;
    }
    /**
     * 查看没推送的产品交付单所关联的测试申请是否通过
     * 如通过 将关联测试申请的产品登记推送出去
     */
    public function getUnPushedAndPush()
    {
        $this->loadModel('outwarddelivery');
        $unPushedProductEnrollIds = $this->dao->select('id, pushFailTimes, code, pushStatus')->from(TABLE_PRODUCTENROLL)->where('status')->eq('waitqingzong')->andwhere('pushStatus')->notin([1,-1])->andwhere('pushFailTimes')->le(10)->fetchALl('id');  //选取没推送成功的产品登记
        if(empty($unPushedProductEnrollIds)) return [];
        //取产品登记所关联的测试申请单
        $outwardDeliveryArray =  $this->dao->select('id, testingRequestId, productEnrollId, code, version, reviewStage,`release`')->from(TABLE_OUTWARDDELIVERY)->where('isNewProductEnroll')->eq(1)->andwhere('productEnrollId')->in(array_keys($unPushedProductEnrollIds))->fetchALl();
        $allPassedList = [];
        $outwardDeliveryList = [];
        $reviewers = $this->lang->outwarddelivery->apiDealUserList['userAccount'];
        foreach ($outwardDeliveryArray as $outwardDelivery)
        {
            if(empty($outwardDelivery->testingRequestId)) {
                if($this->checkMediaFails($outwardDelivery) == false) continue; //是否介质满住条件
                $allPassedList[$outwardDelivery->productEnrollId] = $outwardDelivery->code; //没有关联测试申请可以直接发
                $outwardDeliveryList[$outwardDelivery->productEnrollId] = $outwardDelivery; //以productid为key 的对外交付信息
                continue;
            }
            $passedTestingRequestId = $this->dao->select('id')->from(TABLE_TESTINGREQUEST)->where('cardStatus')->eq(1)->andWhere('id')->eq($outwardDelivery->testingRequestId)->fetchALl('id');
            if($passedTestingRequestId){
                if($this->checkMediaFails($outwardDelivery) == false) continue; //是否介质满住条件
                $allPassedList[$outwardDelivery->productEnrollId] = $outwardDelivery->code;
                $outwardDeliveryList[$outwardDelivery->productEnrollId] = $outwardDelivery; //以productid为key 的对外交付信息
            }
        }
        $res = [];
        foreach ($allPassedList as $unPushedProductEnrollId => $outwardDeliveryCode)
        {
            if(empty($outwardDeliveryCode)) continue;
            $response = $this->pushproductEnroll($outwardDeliveryCode);
            $mdmEmpty = '';
            if ($response == 'md5empty'){
                $mdmEmpty = 'MD5值不能为空';
                $response = '';
            }
            //记录日志
            $run['outwardDeliveryCode']  = $outwardDeliveryCode;
            $run['productEnrollId']  = $unPushedProductEnrollId;
            $run['productEnrollCode']  = $unPushedProductEnrollIds[$unPushedProductEnrollId]->code;
            $run['response'] = $response;
            $outwardDeliveryInfo = $outwardDeliveryList[$unPushedProductEnrollId];
            $this->app->loadLang('outwarddelivery');
            // || $response->isSave == 1
            if($response->code == 200){  //200 = 成功的 isSave == 1 代表成功保存 比如第一次没响应 再次请求
                $update['pushStatus'] = 1;
                $update['status'] = 'withexternalapproval';
                $outwardDeliveryUpdate['status'] = 'withexternalapproval';
                if($unPushedProductEnrollIds[$unPushedProductEnrollId]->pushStatus == '0'){
                    $action = 'syncproductenroll';
                }else{
                    $action = 'editproductenroll';
                }
                $this->loadModel('consumed')->record('outwarddelivery', $outwardDeliveryInfo->id, 0, 'guestjk','waitqingzong','withexternalapproval', array(), '产品登记单');

            }elseif(isset($response->code) && $response->code != 200){
                $update['pushStatus'] = -1;  //已经发出去 业务错误 不重复发
                $update['status'] = 'qingzongsynfailed';
                $outwardDeliveryUpdate['status'] = 'qingzongsynfailed';
                $outwardDeliveryUpdate['dealUser'] = $reviewers;
                $action = 'qingzongsynfailed';
                $this->loadModel('consumed')->record('outwarddelivery', $outwardDeliveryInfo->id, '0', 'guestjk', 'waitqingzong', 'qingzongsynfailed', array(),'产品登记单');

            }elseif($response === ''){
                $update['pushStatus'] = -1;  //没有发出去 业务错误 不重复发
                $update['status'] = 'qingzongsynfailed';
                $outwardDeliveryUpdate['status'] = 'qingzongsynfailed';
                $outwardDeliveryUpdate['dealUser'] = $reviewers;
                $action = 'qingzongsynfailed';
                $this->loadModel('consumed')->record('outwarddelivery', $outwardDeliveryInfo->id, '0', 'guestjk', 'waitqingzong', 'qingzongsynfailed', array(),'产品登记单');

            }else {
                $update['pushStatus'] = 2; //已发出去 其他错误
                $update['pushFailTimes'] = $unPushedProductEnrollIds[$unPushedProductEnrollId]->pushFailTimes + 1; //失败次数+1
                $action = 'qingzongsynfailed';
                if($update['pushFailTimes'] >= 3){
                    $update['pushStatus'] = -1;
                    $update['status'] = 'qingzongsynfailed'; //3次失败后 改为同步失败 不再重复发
                    $outwardDeliveryUpdate['status'] = 'qingzongsynfailed';
                    $outwardDeliveryUpdate['dealUser'] = $reviewers;
                    $this->loadModel('consumed')->record('outwarddelivery', $outwardDeliveryInfo->id, '0', 'guestjk', 'waitqingzong', 'qingzongsynfailed', array(),'产品登记单');
                    $this->dao->update(TABLE_OUTWARDDELIVERY)->data($outwardDeliveryUpdate)->where('id')->eq((int)$outwardDeliveryInfo->id)->exec();
                }
            }
            $this->dao->update(TABLE_PRODUCTENROLL)->data($update)->where('id')->eq((int)$unPushedProductEnrollId)->exec();

            // 缺陷
            if($update['pushStatus'] == 1)
            {
                $defect = $this->dao->select('leaveDefect, fixDefect')->from(TABLE_PRODUCTENROLL)->where('id')->eq($unPushedProductEnrollId)->fetch();
                $defects = array_unique(explode(',', $defect->leaveDefect . $defect->fixDefect));
                if(count($defects)) $this->loadModel('defect')->getUnPushedAndPush($defects);
            }
            //重发时 不更新对外交付单的状态
            if($update['pushStatus'] != 2){
                $this->dao->update(TABLE_OUTWARDDELIVERY)->data($outwardDeliveryUpdate)->where('id')->eq((int)$outwardDeliveryInfo->id)->exec();
            }
            if ($mdmEmpty!=''){
                $response->message = $mdmEmpty;
            }
            $this->loadModel('action')->create('productenroll', $unPushedProductEnrollIds[$unPushedProductEnrollId]->id, $action, $response->message,'','guestjk');
            $this->loadModel('action')->create('outwarddelivery', $outwardDeliveryInfo->id, $action, $response->message,'','guestjk');

            $res[] = $run;
        }
        return $res;
    }

    /**
     * 接口请求最后一个记录
     * @param $id
     */
    public function getRequestLog($id)
    {
        $log = $this->dao->select('id,`status`,response,requestDate')->from(TABLE_REQUESTLOG)->where('objectType')->eq('productenroll')->andWhere('objectId')->eq($id)->andWhere('purpose')->eq('pushproductenroll')->orderBy('id_desc')->fetch();
        if(isset($log->response)){
            $log->response = json_decode($log->response);
        }
        return $log;
    }

    /**
     * @param $id
     * @param $status
     * @return void
     * 保存删除状态
     */
    public function updateDeleteStatus($id, $status){
        $res = $this->dao->update(TABLE_PRODUCTENROLL)
            ->set('deleted')->eq($status)
            ->where('id')->eq((int)$id)->exec();
        return $res;
    }

    public function getOutercode($productenrollId)
    {
        return $this->dao->select('giteeId')->from(TABLE_PRODUCTENROLL)
            ->where('id')->eq((int)$productenrollId)
            ->fetch();
    }

    public function getEmisRegisterNumberById($id)
    {
        return $this->dao->select('emisRegisterNumber')->from(TABLE_PRODUCTENROLL)->where('id')->eq($id)->fetch();
    }

    /**
     * 编辑退回次数
     * @param $id
     * @return void
     */
    public function editreturntimes($outwardDeliveryId){
        //工作量验证
        $rejectTimes = $_POST['productenrollrejectTimes'];
        if($rejectTimes=='' || $rejectTimes==null)
        {
            dao::$errors['productenrollrejectTimes'] = sprintf($this->lang->productenroll->emptyObject, $this->lang->productenroll->productenrollrejectTimes);
        }else if(!is_numeric($rejectTimes) || (int)$rejectTimes<0 || strpos($rejectTimes,".")!==false) {
            dao::$errors['productenrollrejectTimes'] = sprintf($this->lang->productenroll->noNumeric, $this->lang->productenroll->productenrollrejectTimes);
        }

        $comment = $_POST['comment'];
        if(empty($comment))
        {
            dao::$errors['comment'] = sprintf($this->lang->productenroll->emptyObject, $this->lang->comment);
        }

        $this->tryError();

        $outwardDelivery = $this->loadModel("outwardDelivery")->getByID($outwardDeliveryId);

        /* 当请求方式为post时，更新需求条目的状态为关闭。判断所属需求意向下的需求条目都关闭时，关闭需求意向。*/
        $this->dao->update(TABLE_PRODUCTENROLL)->set('returnTimes')->eq($rejectTimes)->where('id')->eq($outwardDelivery->productEnrollId)->exec();
        $this->loadModel('action')->create('outwarddelivery', $outwardDeliveryId, 'editproductenrollreturntimes', $comment);
        $this->loadModel('action')->create('productenroll', $outwardDelivery->productEnrollId, 'editproductenrollreturntimes', $comment);
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
}