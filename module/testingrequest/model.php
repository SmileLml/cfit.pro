<?php
class testingrequestModel extends model
{
    /*
     * 创建测试申请单
     */
    public function create($testingRequestData)
    {
        $testingRequestData['code'] = $this->getCode();
        $this->dao->insert(TABLE_TESTINGREQUEST)
            ->data($testingRequestData)
            ->batchCheckIF($_POST['issubmit'] !='save',$this->config->testingrequest->create->requiredFields, 'notempty')
            ->exec();
        $lastInsertID = $this->dao->lastInsertID();
        if(!dao::isError())
        {
            $data = new stdClass();
            $data->ifTest = $testingRequestData['isCentralizedTest'] == '1' ? '1' : '0';
            $data->project = $testingRequestData['projectPlanId'];
//            $data->CBPproject = $testingRequestData['CBPprojectId'];
            $data->realtedApp = $testingRequestData['app'];
            $data->testrequestCode = $testingRequestData['code'];
            $data->projectManager = $this->dao->select('PM')->from(TABLE_PROJECT)->where('id')->eq($testingRequestData['projectPlanId'])->fetch()->PM;
            $data->testType = $testingRequestData['acceptanceTestType'];
            $data->testrequestCreatedBy = $this->app->user->account;
            $data->testrequestId = $lastInsertID;
            $this->dao->update(TABLE_DEFECT)->data($data)->where('id')->in(explode(',', $testingRequestData['fixDefect'] . $testingRequestData['leaveDefect']))->exec();

//            $defects = $this->dao->select('id,testrequestId')->from(TABLE_DEFECT)->where('id')->in(explode(',', $testingRequestData['fixDefect'] . $testingRequestData['leaveDefect']))->fetchAll();
//
//            foreach ($defects as $defect)
//            {
//                $data->testrequestId getUnPushedAndPush= $lastInsertID;
//                $this->dao->update(TABLE_DEFECT)->data($data)->where('id')->eq($defect->id)->exec();
//            }
        }
        return $lastInsertID;
    }

    public function edit($id, $testingRequestData)
    {
        $this->app->loadLang('outwarddelivery');
        $oldData = $this->dao->select('cardStatus,`status`')->from(TABLE_TESTINGREQUEST)->where('id')->eq($id)->fetch();
        if(!in_array($oldData->status,$this->lang->outwarddelivery->alloweditStatus)) {  //不是待提交 和被拒绝的 不能编辑 新增内部未通过状态
            return true;
        }
        if($oldData->cardStatus == 1) { //外部通过 不能编辑
            return true;
        }
        $res = $this->dao->update(TABLE_TESTINGREQUEST)
            ->data($testingRequestData)
//            ->autoCheck()
            ->where('id')->eq((int)$id)->exec();
        if(!dao::isError() and ($testingRequestData['fixDefect'] or $testingRequestData['leaveDefect']))
        {
            $data = new stdClass();
            $data->ifTest = $testingRequestData['isCentralizedTest'] == '1' ? '1' : '0';
//            $data->project = $testingRequestData['projectPlanId'];
//            $data->CBPproject = $testingRequestData['CBPprojectId'];
            $data->realtedApp = $testingRequestData['app'];
//            $data->projectManager = $this->dao->select('PM')->from(TABLE_PROJECT)->where('id')->eq($testingRequestData['project'])->fetch();
            $data->testType = $testingRequestData['acceptanceTestType'];
            $data->testrequestCode = $testingRequestData['code'];
//            $defects = $this->dao->select('id,testrequestId')->from(TABLE_DEFECT)->where('id')->in(explode(',', $testingRequestData['fixDefect'] . $testingRequestData['leaveDefect']))->fetchAll();
            $data->testrequestId = $id;

            $this->dao->update(TABLE_DEFECT)->data($data)->where('id')->in(explode(',', $testingRequestData['fixDefect'] . $testingRequestData['leaveDefect']))->exec();

//            foreach ($defects as $defect)
//            {
//                $defect->testrequestId = str_replace(','.$id, '', $defect->testrequestId);
//                $data->testrequestId .= $defect->testrequestId . ',' . $id;
//                $this->dao->update(TABLE_DEFECT)->data($data)->where('id')->eq($defect->id)->exec();
//            }
        }
        return $res;
    }

    /**
     * 修改测试申请单状态
     * @param $id
     * @param int $cardStatus
     * @param string $returnPerson
     * @param string $returnCase
     * @param string $testReportFromTestCenter
     * @return mixed
     */
    public function updateStatus($id, $outwarddelivery, int $cardStatus, string $returnPerson = '', string $returnCase = '', Array $testReportFromTestCenter = null)
    {
        $testingRequest['cardStatus'] = $cardStatus;
        $testingRequest['returnPerson'] = $returnPerson;
        $testingRequest['returnCase'] = $returnCase;
        $testingRequest['testReportFromTestCenter'] = json_encode($testReportFromTestCenter);

        if($testReportFromTestCenter){
                $this->saveFile($testReportFromTestCenter['url'], $id, $testReportFromTestCenter['fileName']);// 下载并记录测试报告附件
        }
        if($cardStatus == 1){ //子表单接口通过 将version改为父表单的version
            $versionNumber = $this->dao->select('version')->from(TABLE_OUTWARDDELIVERY)
                ->where('testingRequestId')->eq($id)
                ->andWhere('isNewTestingRequest')->eq(1)
                ->fetch('version');
        }
        $res = $this->dao->update(TABLE_TESTINGREQUEST)
            ->data($testingRequest)
            ->beginIF($cardStatus == 0)->set(", returnTimes = returnTimes+1, returnDate = NOW(), status='testingrequestreject'")->fi()
            ->beginIF($cardStatus == 1)->set(", version = $versionNumber, returnDate = NOW(), status='testingrequestpass'")->fi()
            ->beginIF($cardStatus == 2)->set(", returnDate = NOW(), status='testing'")->fi()
            ->where('id')->eq((int)$id)->exec();
        //对外交付已取消不能修改主表单的数据
        if($outwarddelivery->status != 'cancel'){
            if($cardStatus == 1){
                //设置父表单的状态
                if($outwarddelivery->isNewProductEnroll == 0 && $outwarddelivery->isNewModifycncc == 0){
                    $this->dao->update(TABLE_OUTWARDDELIVERY)->set('status')->eq('testingrequestpass')
                        ->set('currentReview')->eq('5')
                        ->set('dealUser')->eq('')
                        ->where('id')->eq($outwarddelivery->id)->exec();
                }
                $this->dao->update(TABLE_OUTWARDDELIVERY)->set('currentReview')->eq('3')->where('testingRequestId')->eq($id)->andwhere('productEnrollId')->ne(0)->exec();
                $this->dao->update(TABLE_OUTWARDDELIVERY)->set('currentReview')->eq('4')->where('testingRequestId')->eq($id)->andwhere('productEnrollId')->eq(0)->andwhere('modifycnccId')->ne(0)->exec();
                $this->loadModel('consumed')->record('outwarddelivery', (int)$outwarddelivery->id, 0, 'guestcn', 'withexternalapproval', 'testingrequestpass', array(), '测试申请单');
                //$this->dao->update(TABLE_OUTWARDDELIVERY)->set('status')->eq('testingrequestpass')->where('testingRequestId')->eq($id)->exec();
            }else if($cardStatus == 0){
                $this->dao->update(TABLE_OUTWARDDELIVERY)->set('status')->eq('testingrequestreject')->where('id')->eq((int)$outwarddelivery->id)->exec();
                $this->app->loadLang('outwarddelivery');
                $reviewers = $this->lang->outwarddelivery->apiDealUserList['userAccount'];
                $this->dao->update(TABLE_OUTWARDDELIVERY)->set('dealUser')->eq($reviewers)->where('id')->eq((int)$outwarddelivery->id)->exec();
                $this->dao->update(TABLE_OUTWARDDELIVERY)->set('currentReview')->eq('1')->where('id')->eq((int)$outwarddelivery->id)->exec();
                $this->loadModel('consumed')->record('outwarddelivery', (int)$outwarddelivery->id, 0, 'guestcn', 'withexternalapproval', 'testingrequestreject', array(), '测试申请单');
            }
        }


        $logObj = $this->dao->select('*')->from(TABLE_REQUESTLOG)
            ->where('objectType')->eq('testingrequest')
            ->andwhere('objectId')->eq($outwarddelivery->testingRequestId)
            ->andwhere('purpose')->eq('testingrequestfeedback')
            ->andwhere('status')->eq('success')
            ->fetchAll();
        if(empty($logObj) || count($logObj) <= 1){
            //对外交付已取消不能修改主表单的数据
            if($outwarddelivery->status != 'cancel'){
                $this->loadModel('action')->create('outwarddelivery', $outwarddelivery->id, 'testrequestfeedback', $returnCase);
            }
            $this->loadModel('action')->create('testingrequest', $outwarddelivery->testingRequestId, 'testrequestfeedback', $returnCase);
        }else{
            //对外交付已取消不能修改主表单的数据
            if($outwarddelivery->status != 'cancel'){
                $this->loadModel('action')->create('outwarddelivery', $outwarddelivery->id, 'testrequesteditfeedback', $returnCase);
            }
            $this->loadModel('action')->create('testingrequest', $outwarddelivery->testingRequestId, 'testrequesteditfeedback',$returnCase);
        }
        return $res;
    }

    /**
     * 生成code
     * @return string
     */
    public function getCode()
    {
        $prefix   = 'CFIT-TQ-'. date('Ymd-');
        $number = $this->dao->select('count(id) c')->from(TABLE_TESTINGREQUEST)->where('code')->like("$prefix%")->fetch('c') + 1;
        $code   = $prefix . sprintf('%02d', $number);
        return $code;
    }

    public function getList($browseType, $queryID, $orderBy, $pager = null)
    {
        $testingRequestQuery = '';
        if($browseType == 'bysearch')
        {
            $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : '';
            if($query)
            {
                $this->session->set('testingRequestQuery', $query->sql);
                $this->session->set('testingRequestForm', $query->form);
            }
            if($this->session->testingRequestQuery == false) $this->session->set('testingRequestQuery', ' 1 = 1');
            $testingRequestQuery = $this->session->testingRequestQuery;
            $testingRequestQuery = str_replace('AND `', ' AND `t1.', $testingRequestQuery);
            $testingRequestQuery = str_replace('AND (`', ' AND (`t1.', $testingRequestQuery);
            $testingRequestQuery = str_replace('`', '', $testingRequestQuery);

            
            // 处理[关联对外交付]搜索字段
            if(strpos($testingRequestQuery, 't1.relatedOutwardDelivery') !== false)
            {
                $testingRequestQuery = str_replace('t1.relatedOutwardDelivery', "t6.code", $testingRequestQuery);
            }

            // 处理[关联产品登记]搜索字段
            if(strpos($testingRequestQuery, 't1.relatedProductEnroll') !== false)
            {
                $testingRequestQuery = str_replace('t1.relatedProductEnroll', "t3.code", $testingRequestQuery);
            }

            // 处理[关联生产变更申请]搜索字段
            if(strpos($testingRequestQuery, 't1.relatedModifycncc') !== false)
            {
                $testingRequestQuery = str_replace('t1.relatedModifycncc', "t4.code", $testingRequestQuery);
            }

            if(strpos($testingRequestQuery, 'isPayment') !== false)
            {
                $testingRequestQuery = str_replace('t1.isPayment', "t5.isPayment", $testingRequestQuery);
            }

            if(strpos($testingRequestQuery, 'team') !== false)
            {
                $testingRequestQuery = str_replace('t1.team', "t5.team", $testingRequestQuery);
            }
//            if(strpos($testingRequestQuery, 't1.app ') !== false){
//                $testingRequestQuery = str_replace('t1.app', "CONCAT(',', t1.app, ',')", $testingRequestQuery);
//            }
            if(strpos($testingRequestQuery, ',app') !== false){
                $testingRequestQuery = str_replace(',app', ",t1.app", $testingRequestQuery);
            }
            if(strpos($testingRequestQuery, 'requirementId') !== false){
                $testingRequestQuery = str_replace('requirementId', "t1.requirementId", $testingRequestQuery);
            }
            if(strpos($testingRequestQuery, 'demandId') !== false){
                $testingRequestQuery = str_replace('demandId', "t1.demandId", $testingRequestQuery);
            }
            if(strpos($testingRequestQuery, 'problemId') !== false){
                $testingRequestQuery = str_replace('problemId', "t1.problemId", $testingRequestQuery);
            }
        }

        $data = $this->dao->select('distinct t1.*,t2.code as outwarddeliveryCode,t2.id as outwarddeliveryId')->from(TABLE_TESTINGREQUEST)->alias('t1')
            ->leftJoin(TABLE_OUTWARDDELIVERY)->alias('t2') 
            ->on('t1.id=t2.testingRequestId and t2.isNewTestingRequest=1')
            ->leftJoin(TABLE_PRODUCTENROLL)->alias('t3')
            ->on('t2.productEnrollId=t3.id')
            ->leftJoin(TABLE_MODIFYCNCC)->alias('t4')
            ->on('t2.modifycnccId=t4.id')            
            ->leftJoin(TABLE_APPLICATION)->alias('t5')
            ->on('FIND_IN_SET(t5.id,t1.app)')
            ->leftJoin(TABLE_OUTWARDDELIVERY)->alias('t6') 
            ->on('t1.id=t6.testingRequestId')
            ->where('t1.deleted')->ne(1)
            ->beginIF($browseType == 'closed')->andWhere('t1.closed')->eq(1)->fi()
            ->beginIF($browseType != 'all' and $browseType != 'bysearch' and $browseType != 'closed')->andWhere('t1.status')->eq($browseType)->andWhere('t1.closed')->ne(1)->fi()
            ->beginIF($browseType == 'bysearch')->andWhere($testingRequestQuery)->fi()
            ->orderBy('t1.'.$orderBy)
            ->page($pager,'t1.id')
            ->fetchAll('id');

        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'testingRequest', $browseType != 'bysearch');

        return $data;
    }

    public function getByID($id)
    {
        if(empty($id)) return null;
        $data = $this->dao->select('*')->from(TABLE_TESTINGREQUEST)
            ->where('id')->eq($id)
            ->andwhere('deleted')->eq(0)
            ->fetch();
        $data = $this->loadModel('file')->replaceImgURL($data, 'content,env');
        $data->files = $this->loadModel('file')->getByObject('testingRequest', $id);
        $this->loadModel('outwarddelivery')->resetNodeAndReviewerName($data->createdDept);
        return $data;
    }

    /**
     * TongYanQi 2022/11/26
     * 获取全部
     */
    public function getAll()
    {
        $data = $this->dao->select('*')->from(TABLE_TESTINGREQUEST)
            ->fetchAll();
        return $data;
    }

    /**
     * 根据对外交付id推送测试申请单
     */
    public function pushTestingrequest($outwarddeliveryId)
    {
        $this->loadModel('requestlog');
        /* 获取对外交付单 */
        $outwarddelivery = $this->loadModel("outwarddelivery")->getByID($outwarddeliveryId);
        /* 获取测试申请单。*/
        $testingRequest = $this->getByID($outwarddelivery->testingRequestId);


        $pushEnable = $this->config->global->pushOutwarddeliveryEnable;
        //判断请求配置是否可用
        if ($pushEnable == 'enable') {
            $url = $this->config->global->testingRequestPushUrl;
            $pushAppId = $this->config->global->pushOutwarddeliveryAppId;
            $pushAppSecret = $this->config->global->pushOutwarddeliveryAppSecret;
            $pushUsername = $this->config->global->pushOutwarddeliveryUsername;
            $fileIP       = $this->config->global->pushOutwarddeliveryFileIP;
            $headers = array();
            $headers[] = 'App-Id: ' . $pushAppId;
            $headers[] = 'App-Secret: ' . $pushAppSecret;

            $deptList = $this->loadModel('dept')->getOptionMenu();
            $users = $this->loadmodel('user')->getPairs('noletter');

            $pushData = array();
            $depts = $this->loadModel('dept')->getTopPairs();
            $pushData['applicationDepartment']      = $depts[$testingRequest->createdDept];
            $pushData['idFromJinke']                = $testingRequest->code;
            $pushData['testSummary']                = $testingRequest->testSummary;
            $pushData['testTarget']                 = html_entity_decode(strip_tags(str_replace("<br />","\n",strval(htmlspecialchars_decode($testingRequest->testTarget,ENT_QUOTES)))));
            $pushData['acceptanceTestType']         = zget($this->lang->testingrequest->acceptanceTestTypeList, $testingRequest->acceptanceTestType);
            $pushData['currentStage']               = html_entity_decode(strip_tags(str_replace("<br />","\n",strval(htmlspecialchars_decode($testingRequest->currentStage,ENT_QUOTES)))));
            $pushData['testContent']                = html_entity_decode(strip_tags(str_replace("<br />","\n",strval(htmlspecialchars_decode($testingRequest->content,ENT_QUOTES)))));
            $pushData['environmentalOverview']      = html_entity_decode(strip_tags(str_replace("<br />","\n",strval(htmlspecialchars_decode($testingRequest->env,ENT_QUOTES)))));
            $pushData['operatingSystem']            = html_entity_decode(strip_tags(str_replace("<br />","\n",strval(htmlspecialchars_decode($testingRequest->os,ENT_QUOTES)))));
            /*$testProductList = array();
            $productIds = explode(',', $testingRequest->productId);
            foreach ($productIds as $productId){
                if(!empty($productId)){
                    $product = $this->loadModel("product")->getByID($productId);
                    array_push($testProductList, $product->code);
                }
            }*/
            $outwarddelivery->productInfoCode = preg_replace('/[\r\n]/', ',',$outwarddelivery->productInfoCode);
            $testProductList = array_values(array_filter(explode(',',$outwarddelivery->productInfoCode)));
//            $testProductList = explode(',', $outwarddelivery->productInfoCode);
            $pushData['testProduct']                = $testProductList;
            $pushData['databaseType']               = html_entity_decode(strip_tags(str_replace("<br />","\n",strval(htmlspecialchars_decode($testingRequest->db,ENT_QUOTES)))));

            //附件-从对外交付中获取
            $updateData = array();
            $updateData['remoteFilePath'] = $outwarddelivery->release;
            $updateData['ifMediumChanges'] = $outwarddelivery->ifMediumChanges;
            $this->dao->update(TABLE_TESTINGREQUEST)->data($updateData)->where('id')->eq($outwarddelivery->testingRequestId)->exec();
            $remoteFileList = array();
            if(!empty($testingRequest->release)){
                $releaseIdList = explode(',' , $testingRequest->release);
                foreach ($releaseIdList as $releaseId){
                    $releaseObj = $this->loadModel("release")->getByID($releaseId);
                    if(!empty($releaseObj)){
                        $remoteFileStr = $releaseObj->remotePathQz;
                        $arr=explode("/", $remoteFileStr);
                        $lastName=$arr[count($arr)-1];
                        $urlObject = $this->loadModel("outwarddelivery")->getRelationFileLinkArray($lastName, $remoteFileStr, $releaseObj->md5);
                        if(!empty($urlObject)){
                            array_push($remoteFileList, $urlObject);
                        }
                    }
                }
            }
            $pushData['relationFiles'] = $remoteFileList;
            //问题单列表
            $problemIds = explode(',', $testingRequest->problemId);
            $issueIds = array();
            foreach ($problemIds as $problemId){
                if(!empty($problemId)){
                    $problem = $this->loadModel("problem")->getByID($problemId);
                    if(!empty($problem->IssueId)){
                        array_push($issueIds, $problem->IssueId);
                    }
                }
            }
            $pushData['problemList']               =   $issueIds;
            $pushData['contactName']               =   $testingRequest->contactName;
            $pushData['Contact_telephone']         =   $testingRequest->contactTel;
            //项目编号
            $pushData['projectNumber']             =   $testingRequest->CBPprojectId;
            $pushData['ifMediumChanges']           =  strval($outwarddelivery->ifMediumChanges);
            //需求编号
            $requireIds = explode(',', $testingRequest->requirementId);
            $requireIdSTRs = array();
            foreach ($requireIds as $requireId){
                if(!empty($requireId)){
                    $requireObj = $this->loadModel("requirement")->getByIdSimple($requireId);
                    if(!empty($requireObj->entriesCode)){
                        array_push($requireIdSTRs, $requireObj->entriesCode);
                    }
                }
            }
            $pushData['demandKey']                 =  $requireIdSTRs;
            //系统编号
            $applicationIds = explode(',', $testingRequest->app);
            foreach ($applicationIds as $applicationId){
                $applicationObj = $this->loadModel("application")->getByID($applicationId);
                if(!empty($applicationObj->code)){
                    $pushData['Blongsystem']                 =  $applicationObj->code;
                }
            }
            $pushData['isCentralizedTest']                 =  $testingRequest->isCentralizedTest==1?'1':'0';


            $object = 'testingrequest';
            $objectType = 'pushtestingrequest';
            $response = '';
            $status = 'fail';
            $extra = '';
            if (!empty($pushData['relationFiles'])){
                foreach ($pushData['relationFiles'] as $relationFile) {
                    if ($relationFile['md5'] == ''){
                        $res = ['status'=>'fail','msg'=>'MD5值不能为空'];
                        $this->loadModel('requestlog')->saveRequestLog($url, $object, $objectType, 'POST', $pushData, json_encode($res), $status, $extra, $testingRequest->id);
                        return 'md5empty';
                    }
                }
            }

            $result = $this->loadModel('requestlog')->http($url, $pushData, 'POST', 'json', array(), $headers);
            if (!empty($result)) {
                $resultData = json_decode($result);
                //  || $resultData->isSave == 1 不需要这个判断
                if ($resultData->code == '200') { //200 = 成功的 isSave == 1 代表成功保存 比如第一次没响应 再次请求
                    $status = 'success';
                    if ($testingRequest->giteeId == ''){
                        $this->dao->update(TABLE_TESTINGREQUEST)->set('giteeId')->eq($resultData->key)->where('id')->eq($outwarddelivery->testingRequestId)->exec();
                    }
                    // 缺陷
                    $defects = array_unique(explode(',', $testingRequest->leaveDefect . $testingRequest->fixDefect));
                    if(count($defects)) $this->loadModel('defect')->getUnPushedAndPush($defects);
                }
                $response = $result;
            }
                //推送后成功执行
//                $this->dao->update(TABLE_TESTINGREQUEST)->set('status')->eq('withexternalapproval')->where('id')->eq($outwarddelivery->testingRequestId)->exec();
            $this->requestlog->saveRequestLog($url, $object, $objectType, 'POST', $pushData, $response, $status, $extra, $testingRequest->id);
        }
        return $response;
    }

    public function fixTestingRequestData($update = 0)
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
        $fixData['content'] = $postData->content  ?? '';
        $fixData['testSummary'] = $postData->testSummary  ?? '';
        $fixData['testTarget'] = $postData->testTarget  ?? '';
        $fixData['acceptanceTestType'] = $postData->acceptanceTestType  ?? '';
        $fixData['currentStage'] = $postData->currentStage ?? '';
        $fixData['env'] = $postData->env ?? '';
        $fixData['os'] = $postData->os ?? '';
        $fixData['testProduct'] = $postData->testProduct ?? '';
        $fixData['testProductName'] = $postData->testProductName ?? '';
        $fixData['db'] = $postData->db ?? '';
        $fixData['problemId'] = $postData->problemId ?? '';
        $fixData['contactName'] = $this->app->user->realname ?? '';
        $fixData['contactTel'] = $postData->applyUsercontact ?? '';
        $fixData['projectCode'] = $postData->projectCode ?? '';
        $fixData['projectName'] = $postData->projectName ?? '';
        $fixData['fixDefect'] = $postData->fixDefect ?? '';
        $fixData['leaveDefect'] = $postData->leaveDefect ?? '';
        $fixData['returnReason'] = '';
        $fixData['cardStatus'] = '';
        $fixData['returnDate'] = '';
        //$fixData['app'] = $postData->app ?? '';
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

        if(!empty($postData->productId)){
            $productIdArray = explode(',', str_replace(' ', '', $postData->productId));
            $productIds = ",";
            $appArray = array();
            foreach ($productIdArray as $item) {
                if(!empty($item)){
                    $productIds =  $productIds.$item.",";
                    $product = $this->dao->select('line,app')->from(TABLE_PRODUCT)
                        ->where('id')->eq($item)
                        ->fetch();
                    if(!in_array($product->app, $appArray)){
                        array_push($appArray, $product->app);
                    }
                }
            }
            $fixData['productId'] = $productIds;
            //$fixData['app'] = implode(',',$appArray);
        }else{
            $fixData['productId'] = '';
            //$fixData['app'] = '';
        }
        /*$fixData['productId'] = $postData->productId ?? '';*/
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
        $fixData['implementationForm'] = $postData->implementationForm ?? '';
        $fixData['status']    = $postData->status ?? "waitsubmitted";
        $fixData['pushStatus']    = 0;
        $fixData['isCentralizedTest']    = $postData->isCentralizedTest ?? '';
        //save为保存 不需要校验必填，submit：提交需要校验
        if ($postData->issubmit != 'save'){
            $this->checkParams($fixData, $this->config->testingrequest->create->requiredFields);
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
            if(empty(strip_tags(str_replace("&nbsp;"," ",htmlspecialchars_decode($data[$item]))))){
                $itemName = $this->lang->testingrequest->$item ?? $item;
                dao::$errors[$item] =  sprintf($this->lang->testingrequest->emptyObject, $itemName);
            }
        }
    }

    public function getPairs($outwarddeliveryId = '')
    {
        return $this->dao->select('zt.id,concat(zt.code,"（",zt.testSummary ,"）")')->from(TABLE_TESTINGREQUEST)->alias('zt')
            ->innerJoin(TABLE_OUTWARDDELIVERY)->alias('zo')
            ->on('zt.id = zo.testingRequestId and zo.isNewTestingRequest = 1')
            ->where('zt.deleted')->ne(1)
            ->andwhere('zo.id')->ne($outwarddeliveryId)
            ->orderBy('zt.id_desc')
            ->fetchPairs();
    }

    public function getCodeGiteePairs()
    {
      return $this->dao->select('id,concat(code,"（",IFNULL(giteeId,"")  ,"）")')->from(TABLE_TESTINGREQUEST)
        ->where('deleted')->ne(1)
        ->orderBy('id_desc')
        ->fetchPairs('id');
    }


    /**
     * 文件下载信息存库,返回file表id
     * @param $url
     * @param $testingRequestId
     * @return mixed
     */
    public function saveFile($url, $testingRequestId, $filename)
    {
        if(filter_var($url, FILTER_VALIDATE_URL) === false){ //url不正确
            return false;
        }
        $pathName = $this->getUrlFile($url); //测试申请 已经crc32后的文件
        $serverPath = $this->getDir().crc32(basename($url));
        $file['objectType'] = 'testingRequest';
        $file['objectID']   = $testingRequestId;
        $file['addedBy']    = $this->app->user->account;
        $file['addedDate']  = helper::now();
        $file['pathname']   = $pathName;
        if(strrchr($filename, '.')){ //如果有后缀 记录后缀
            $file['extension'] = substr(strrchr($filename, '.'),1);
            $file['pathname']   .= '.' . $file['extension']; //数据库保持时需要后缀 真实文件没有
        } else {
            $file['extension'] = '';
        }

        $file['title']      = $filename ?? basename($url);
        $file['size']        = filesize($serverPath);

        $exists = $this->dao->select("*")->from(TABLE_FILE)
            ->where('objectType')->eq($file['objectType'])
            ->andWhere('objectID')->eq($file['objectID'])
            ->andWhere('pathname')->eq($file['pathname'])
            ->fetch();
        if(!empty($exists)){
            return $exists->id;
        }

        if($file['size']){
            $this->dao->insert(TABLE_FILE)->data($file)->exec();
            return $this->dao->lastInsertId();
        } else {
            return  false;
        }
    }

    public function getCodePairs()
    {
        return $this->dao->select('id,code')->from(TABLE_TESTINGREQUEST)
            ->where('deleted')->ne(1)
            ->orderBy('id_desc')
            ->fetchPairs();
    }

    public function getoutwardDeliveryIdByTestingRequestId($ids)
    {
        $this->dao->select('id,code')->from(TABLE_TESTINGREQUEST)
            ->where('deleted')->ne(1)
            ->where('id')->in($ids)
            ->orderBy('id_desc')
            ->fetchPairs('id');
    }

    /**
     * 查看没推送的测试申请是否通过
     * 如通过 将测试申请推送出去
     * tongyanqi 2022-7-28
     */
    public function getUnPushedAndPush()
    {
        /** @var outwarddeliveryModel $outwardDeliveryModel */
        $outwardDeliveryModel = $this->loadModel('outwarddelivery');
        $reviewers = $this->lang->outwarddelivery->apiDealUserList['userAccount'];

        $unPushedTestingRequestIds = $this->dao->select('id, pushFailTimes')->from(TABLE_TESTINGREQUEST)->where('status')->eq('waitqingzong')->andwhere('pushStatus')->notin([1,-1])->andwhere('pushFailTimes')->le(10)->fetchALl('id');  //选取没推送成功的产品登记
        if(empty($unPushedTestingRequestIds)) return [];
        //取测试申请单
        $outwardDeliveryArray =  $this->dao->select('*')->from(TABLE_OUTWARDDELIVERY)->where('isNewTestingRequest')->ge(1)->andwhere('testingRequestId')->in(array_keys($unPushedTestingRequestIds))->fetchALl();
        $res = [];
        foreach ($outwardDeliveryArray as $outwardDelivery)
        {
            $pushFailMax = $this->loadModel('release')->getFailsQz($outwardDelivery->release);
            if($pushFailMax){ //介质推送失败多次 直接跳过
                $action = 'qingzongsynfailed';
                $update['pushStatus'] = -1;
                $update['status'] = 'qingzongsynfailed'; //3次失败后 改为同步失败 不再重复发
                $this->dao->update(TABLE_TESTINGREQUEST)->data($update)->where('id')->eq((int)$outwardDelivery->testingRequestId)->exec();
                $this->loadModel('action')->create('testingrequest', $outwardDelivery->testingRequestId, $action, '介质同步失败多次','guestjk');

                $outwardDeliveryModel->setOutwardDeliverySyncFail($outwardDelivery->id); //更改同步失败状态
                $this->loadModel('consumed')->record('outwarddelivery', $outwardDelivery->id, '0', 'guestjk', 'waitqingzong', 'qingzongsynfailed', array(),'介质多次同步失败');
                $this->loadModel('action')->create('outwarddelivery', $outwardDelivery->id, $action, '介质多次同步失败','', $reviewers);

                continue;
            }
            if($this->loadModel('release')->ifReleasesPushed($outwardDelivery->release) == false) { continue; } //介质未处理完 不推单子
            $response = $this->pushTestingrequest($outwardDelivery->id);
            $mdmEmpty = '';
            if ($response == 'md5empty'){
                $mdmEmpty = 'MD5值不能为空';
                $response = '';
            }
            $response = json_decode($response);
            $run['outwardDeliveryId']   = $outwardDelivery->id;
            $run['testingRequestId']    = $outwardDelivery->testingRequestId;
            $run['response']            = $response;
            $this->app->loadLang('outwarddelivery');

            //  || $response->isSave == 1
            if($response->code == 200){  //成功的  isSave == 1 代表成功保存 比如第一次没响应 再次请求
                $update['pushStatus'] = 1;
                $update['status'] = 'withexternalapproval';
                $update['pushDate'] = helper::now();
                $outwardDeliveryUpdate['status'] = 'withexternalapproval';
            }elseif(isset($response->code) && $response->code != 200){
                $update['pushStatus'] = -1;  //已经发出去 业务错误 不重复发
                $update['status'] = 'qingzongsynfailed';
                $update['pushDate'] = helper::now();
                $outwardDeliveryUpdate['status'] = 'qingzongsynfailed';
                $this->dao->update(TABLE_OUTWARDDELIVERY)->set('dealUser')->eq($reviewers)->where('id')->eq($outwardDelivery->id)->exec();
             }elseif($response === ''){
                $update['pushStatus'] = -1;  //没有发出去 业务错误 不重复发
                $update['status'] = 'qingzongsynfailed';
                $update['pushDate'] = helper::now();
                $outwardDeliveryUpdate['status'] = 'qingzongsynfailed';
                $this->dao->update(TABLE_OUTWARDDELIVERY)->set('dealUser')->eq($reviewers)->where('id')->eq($outwardDelivery->id)->exec();

                $this->loadModel('consumed')->record('outwarddelivery', $outwardDelivery->id, '0', 'guestjk', 'waitqingzong', 'qingzongsynfailed', array(),'测试申请单');
            }else {
                $update['pushStatus'] = 2; //已发出去 其他错误
                $update['pushFailTimes'] = $unPushedTestingRequestIds[$outwardDelivery->testingRequestId]->pushFailTimes + 1; //失败次数+1
                if($update['pushFailTimes'] >= 3){
                    $update['pushStatus'] = -1;
                    $update['status'] = 'qingzongsynfailed'; //3次失败后 改为同步失败 不再重复发
                    $update['pushDate'] = helper::now();
                    //$update['synFailedReason'] = '其他报错';
                    $outwardDeliveryUpdate['status'] = 'qingzongsynfailed';
                    //$outwardDeliveryUpdate['synFailedReason'] = '其他报错';
                    $this->dao->update(TABLE_OUTWARDDELIVERY)->set('dealUser')->eq($reviewers)->where('id')->eq($outwardDelivery->id)->exec();
                    $this->loadModel('action')->create('outwarddelivery', $outwardDelivery->id, 'qingzongsynfailed', '', '', 'guestjk'); //发邮件
                    $this->loadModel('consumed')->record('outwarddelivery', $outwardDelivery->id, '0', 'guestjk', 'waitqingzong', 'qingzongsynfailed', array(),'测试申请单');
                    $this->dao->update(TABLE_OUTWARDDELIVERY)->set('status')->eq($outwardDeliveryUpdate['status'])->where('id')->eq((int)$outwardDelivery->id)->exec();
                }
            }
            $this->dao->update(TABLE_TESTINGREQUEST)->data($update)->where('id')->eq((int)$outwardDelivery->testingRequestId)->exec();

            //重发时 不更新对外交付单的状态
            if($update['pushStatus'] != 2){
                $this->dao->update(TABLE_OUTWARDDELIVERY)->set('status')->eq($outwardDeliveryUpdate['status'])->where('id')->eq((int)$outwardDelivery->id)->exec();
            }


            // || $response->isSave == 1
            if($response->code == 200){ //200 = 成功的 isSave == 1 代表成功保存 比如第一次没响应
                $logObj = $this->dao->select('*')->from(TABLE_REQUESTLOG)
                    ->where('objectType')->eq('testingrequest')
                    ->andwhere('objectId')->eq((int)$outwardDelivery->testingRequestId)
                    ->andwhere('purpose')->eq('pushtestingrequest')
                    ->andwhere('status')->eq('success')
                    ->fetchAll();
                if(empty($logObj) || count($logObj)  <= 1){
                    $this->loadModel('action')->create('outwarddelivery',(int)$outwardDelivery->id, 'synctestrequest', $response->message);
                    $this->loadModel('action')->create('testingrequest', (int)$outwardDelivery->testingRequestId, 'synctestrequest', $response->message);
                }else{
                    $this->loadModel('action')->create('outwarddelivery', (int)$outwardDelivery->id, 'edittestrequest', $response->message);
                    $this->loadModel('action')->create('testingrequest', (int)$outwardDelivery->testingRequestId, 'edittestrequest', $response->message);
                }
                $this->loadModel('consumed')->record('outwarddelivery', (int)$outwardDelivery->id, 0, 'guestjk', 'waitqingzong', 'withexternalapproval', array(), '测试申请单');
            }else{
                if ($mdmEmpty!=''){
                    $response->message = $mdmEmpty;
                }
                $this->loadModel('action')->create('outwarddelivery', (int)$outwardDelivery->id, 'qingzongsynfailed', $response->message);
                $this->loadModel('action')->create('testingrequest', (int)$outwardDelivery->testingRequestId, 'qingzongsynfailed', $response->message);
                $this->loadModel('consumed')->record('outwarddelivery', (int)$outwardDelivery->id, 0, 'guestjk', 'waitqingzong', 'qingzongsynfailed', array(), '测试申请单');
            }
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
        $log = $this->dao->select('id,`status`,response,requestDate')->from(TABLE_REQUESTLOG)->where('objectType')->eq('testingrequest')->andWhere('objectId')->eq($id)->andWhere('purpose')->eq('pushtestingrequest')->orderBy('id_desc')->fetch();
        if(isset($log->response)){
            $log->response = json_decode($log->response);
        }
        return $log;
    }

    public function getCodeById($id)
    {
        return $this->dao->select('code')->from(TABLE_TESTINGREQUEST)->where('id')->eq($id)->fetch('code') . "";
    }
    /**
     * @param $id
     * @param $status
     * @return void
     * 保存删除状态
     */
    public function updateDeleteStatus($id, $status){
        $res = $this->dao->update(TABLE_TESTINGREQUEST)
            ->set('deleted')->eq($status)
            ->where('id')->eq((int)$id)->exec();
        return $res;
    }

    public function getOutercode($testingrequestId)
    {
        return $this->dao->select('giteeId')->from(TABLE_TESTINGREQUEST)
            ->where('id')->eq((int)$testingrequestId)
            ->fetch();
    }

    /**
     * 获取单个详情-通过code
     * @param $id
     * @return null
     */
    public function getByCode($code)
    {
        if(empty($code)) return null;
        $data = $this->dao->select('*')->from(TABLE_TESTINGREQUEST)
            ->where('code')->eq($code)
            ->andwhere('deleted')->eq(0)
            ->fetch();
        return $data;
    }

    /**
     * 通过测试单id反查对外交付单
     * @param $id
     * @return null
     */
    public function getOutwarddeliveryByTestId($id)
    {
        if(empty($id)) return null;
        $data = $this->dao->select('*')->from(TABLE_OUTWARDDELIVERY)
            ->where('isNewTestingRequest')->eq(1)
            ->andwhere('deleted')->eq(0)
            ->andwhere('testingRequestId')->eq($id)
            ->fetch();
        return $data;
    }

    /**
     * 编辑退回次数
     * @param $id
     * @return void
     */
    public function editreturntimes($outwardDeliveryId){
        //工作量验证
        $rejectTimes = $_POST['testingrejectTimes'];
        if($rejectTimes=='' || $rejectTimes==null)
        {
            dao::$errors['testingrejectTimes'] = sprintf($this->lang->testingrequest->emptyObject, $this->lang->testingrequest->testingrejectTimes);
        }else if(!is_numeric($rejectTimes) || (int)$rejectTimes<0 || strpos($rejectTimes,".")!==false) {
            dao::$errors['testingrejectTimes'] = sprintf($this->lang->testingrequest->noNumeric, $this->lang->testingrequest->testingrejectTimes);
        }

        $comment = $_POST['comment'];
        if(empty($comment))
        {
            dao::$errors['comment'] = sprintf($this->lang->testingrequest->emptyObject, $this->lang->comment);
        }

        $this->tryError();

        $outwardDelivery = $this->loadModel("outwardDelivery")->getByID($outwardDeliveryId);

        /* 当请求方式为post时，更新需求条目的状态为关闭。判断所属需求意向下的需求条目都关闭时，关闭需求意向。*/
        $this->dao->update(TABLE_TESTINGREQUEST)->set('returnTimes')->eq($rejectTimes)->where('id')->eq($outwardDelivery->testingRequestId)->exec();
        $this->loadModel('action')->create('outwarddelivery', $outwardDeliveryId, 'edittestingreturntimes', $comment);
        $this->loadModel('action')->create('testingrequest', $outwardDelivery->testingRequestId, 'edittestingreturntimes', $comment);
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
     * 获取投产移交正常、异常终态单子明细
     * @param $start 开始时间
     * @param $end 结束时间
     * @param $projects 项目
     * @param $depts 部门列表
     */
    public function getPutproduction($start,$end,$projects,$depts = []){
        $this->app->loadLang('putproduction');
        $successStatus = ['success','successpart'];
        $errorStatus   = ['cancel','putproductionfail'];
        $status = array_merge($successStatus,$errorStatus);
        $res = $this->dao->select("objectID,createdDate")->from(TABLE_CONSUMED)
            ->where('deleted')->eq('0')
            ->andWhere('objectType')->eq('putproduction')
            ->andWhere('after')->in($status)
            ->andWhere('createdDate')->ge($start)
            ->andWhere('createdDate')->le($end)
            ->fetchall('objectID');
        $ids    = array_values(array_unique(array_column($res, 'objectID')));
        $data = $this->dao->select('id,code,status,inProjectIds,returnCount,returnReason')->from(TABLE_PUTPRODUCTION)
            ->where('id')->in($ids)
            ->andWhere("status")->in($status)
            ->andWhere("externalId")->eq('true')
            ->andWhere("deleted")->eq('0')
            ->fetchall();

        foreach ($data as $key => $v) {
            foreach ($projects as $project) {
                if ($v->inProjectIds != ''){
                    $inProjectIds = array_unique(explode(',',$v->inProjectIds));
                    if (in_array($project->id,$inProjectIds)){
                        $data[$key]->deptId    .= $project->bearDept.',';
                        $data[$key]->deptName  .= $project->deptName.',';
                        $data[$key]->method    .= $project->mark.',';
                    }
                }
            }
        }
        foreach ($data as $key => $v) {
            $data[$key]->deptId    = trim($v->deptId,',');
            $data[$key]->deptName  = trim($v->deptName,',');
            $data[$key]->method    = trim($v->method,',');
            $count = $v->returnCount; //交付总次数
            if (in_array($v->status,$successStatus)){
                $count = $v->returnCount + 1; //交付总次数
            }
            $data[$key]->count  = $count;
            $data[$key]->type = '投产移交单';
            $data[$key]->productionIsFail = '';
            $data[$key]->isCBP = '否';
            $data[$key]->modifyIsFail = '';
            $data[$key]->returnTime = $v->returnCount;
            $data[$key]->objectType = 'putproduction';
            if (in_array($v->status,['cancel','putproductionfail','successpart'])){
                $data[$key]->returnReason = $v->returnReason!='' ? '打回原因：'.$v->returnReason : '';
            }

            $data[$key]->times = '1';
            //投产移交
            $data[$key]->modifyIsFail = '/';
            if (in_array($data->status,$errorStatus)){
                $data[$key]->productionIsFail = '是';
            }else{
                $data[$key]->productionIsFail = '否';
            }
            if ($v->status == 'cancel'){
                $data[$key]->productionIsFail = '/';
            }
            foreach ($res as $consumed) {
                if ($consumed->objectID == $v->id) {
                    $data[$key]->createdDate = $consumed->createdDate;
                }
            }
            $data[$key]->statusEn  = $v->status;
            $data[$key]->status    = $this->lang->putproduction->statusList[$v->status];
        }
        return [$data,$depts];
    }

    /**
     * 获取征信交付正常、异常终态单子明细
     * @param $start 开始时间
     * @param $end 结束时间
     * @param $projects 项目
     * @param $depts 部门列表
     */
    public function getCredit($start,$end,$projects,$depts = []){
        $this->app->loadLang('credit');
        $successStatus = ['success','successpart'];
        $errorStatus   = ['fail','cancel','modifyrollback','modifyerror'];
        $status = array_merge($successStatus,$errorStatus);
        $res = $this->dao->select("objectID,createdDate")->from(TABLE_CONSUMED)
            ->where('deleted')->eq('0')
            ->andWhere('objectType')->eq('credit')
            ->andWhere('after')->in($status)
            ->andWhere('createdDate')->ge($start)
            ->andWhere('createdDate')->le($end)
            ->fetchall('objectID');
        $ids    = array_values(array_unique(array_column($res, 'objectID')));
        $data = $this->dao->select('id,code,status,projectPlanId,implementationForm,mode')->from(TABLE_CREDIT)
            ->where('id')->in($ids)
            ->andWhere("status")->in($status)
            ->andWhere("deleted")->eq('0')
            ->fetchall();

        foreach ($data as $key => $v) {
            foreach ($projects as $project) {
                if ($v->projectPlanId == $project->project){
                    $depts[$project->bearDept] = $project->deptName;
                    $data[$key]->deptId = $project->bearDept;
                    $data[$key]->deptName = $project->deptName;
                    $data[$key]->method  = $project->mark;
                }
            }
        }

        foreach ($data as $key => $v) {
            $count = $v->returnCount + 1; //交付总次数
            //征信交付没有退回次数字段
            $data[$key]->count  = '0';
            $data[$key]->type = '征信交付';
            $data[$key]->productionIsFail = '';
            $data[$key]->isCBP = '否';
            $data[$key]->returnTime = '0';
            $data[$key]->objectType = 'credit';
            $data[$key]->fixType    = $v->implementationForm;

            $data[$key]->times = '1';
            $data[$key]->productionIsFail = '/';
            if (in_array($data->status,$errorStatus)){
                $data[$key]->modifyIsFail = '是';
            }else{
                $data[$key]->modifyIsFail = '否';
            }
            if ($v->mode == '1'){
                //投产移交
                $data[$key]->modifyIsFail = '/';
                if (in_array($v->status,$errorStatus)){
                    $data[$key]->productionIsFail = '是';
                }else{
                    $data[$key]->productionIsFail = '否';
                }
            }else{
                //变更
                $data[$key]->productionIsFail = '/';
                if (in_array($v->status,$errorStatus)){
                    $data[$key]->modifyIsFail = '是';
                }else{
                    $data[$key]->modifyIsFail = '否';
                }
            }
            if ($v->status == 'cancel'){
                $data[$key]->modifyIsFail = '/';
                $data[$key]->productionIsFail = '/';
            }
            foreach ($res as $consumed) {
                if ($consumed->objectID == $v->id) {
                    $data[$key]->createdDate = $consumed->createdDate;
                }
            }
            if (in_array($v->status,['fail','cancel','modifyrollback','modifyerror','successpart'])){
                $data[$key]->returnReason = $v->returnReason != '' ? '打回原因：'.$v->returnReason : '';
            }
            $data[$key]->statusEn = $v->status;
            $data[$key]->status   = $this->lang->credit->statusList[$v->status];

        }
        return [$data,$depts];
    }

    /**
     * 获取测试申请终态单子
     * @param $start
     * @param $end
     * @param $projects 项目
     */
    public function getTestingrequests($start,$end,$projects){
        $consumeds = $this->dao->select('*')->from(TABLE_CONSUMED)
            ->where('deleted')->eq('0')
            ->andWhere('objectType')->eq('outwarddelivery')
            ->andWhere('after')->in(['testingrequestpass','cancel'])
            ->andWhere('extra')->eq('测试申请单')
            ->andWhere('createdDate')->ge($start)
            ->andWhere('createdDate')->le($end)
            ->fetchall();

        $ids = array_values(array_unique(array_column($consumeds,'objectID')));
        $tests = $this->dao->select('t1.id,t2.id as tid,t1.code,t1.status,t2.code as tcode,t1.projectPlanId,t1.implementationForm,t1.returnTimes,t2.productInfoCode,t1.implementationForm,t1.giteeId')->from(TABLE_TESTINGREQUEST)->alias("t1")
            ->leftjoin(TABLE_OUTWARDDELIVERY)->alias("t2")
            ->on("t1.id=t2.testingRequestId")
            ->where('t2.id')->in($ids)
            ->andWhere("t2.isNewTestingRequest")->eq('1')
            ->andWhere("t1.deleted")->eq('0')
            ->andWhere("t1.status")->in(['testingrequestpass','cancel'])
            ->andWhere("t1.giteeId")->ne('')
            ->fetchall();
        foreach ($tests as $key => $test) {
            foreach ($projects as $project) {
                if ($project->project == $test->projectPlanId){
                    $tests[$key]->deptName = $project->deptName;
                    $tests[$key]->deptId   = $project->bearDept;
                    $tests[$key]->method  = $project->mark;
                }
            }
        }
        foreach ($tests as $key => $test) {
            if ($test->status == 'cancel' && $test->giteeId == ''){
                continue;
            }
            $count = $test->returnTimes + 1; //交付总次数
            $tests[$key]->count  = $count;
            $tests[$key]->type = '测试申请';
            $tests[$key]->productionIsFail = '/';
            $tests[$key]->isCBP = '是';
            $tests[$key]->returnTime = $test->returnTimes;
            $tests[$key]->fixType    = $test->implementationForm;

            $tests[$key]->statusEn = $test->status;
            $tests[$key]->status   = $this->lang->testingrequest->statusList[$test->status];
            $tests[$key]->objectType = 'testingrequest';

            $tests[$key]->modifyIsFail = '/';
            $tests[$key]->times = '1';
            foreach ($consumeds as $consumed) {
                if ($consumed->objectID == $test->tid) {
                    $tests[$key]->createdDate = $consumed->createdDate;
                }
            }
        }
        return $tests;
    }
    /**
     * 获取金信生产变更终态单子
     * @param $start
     * @param $end
     * @param $projects 项目
     */
    public function getModifysAndModifycnccs($start,$end,$projects){
        $this->app->loadLang('modify');
        $this->app->loadLang('modifycncc');
        // 金信生产变更 成功+异常
        $modifysuccess = ['modifysuccess','modifysuccesspart'];
        $modifyerror   = ['modifyerror','modifyrollback','modifycancel','modifyfail'];
        $modifyStatus = array_merge($modifysuccess,$modifyerror);
        // 清总生产变更 成功+异常
        $modifycnccsuccess = ['modifysuccess','modifysuccesspart'];
        $modifycnccerror   = ['modifyfail','modifycancel'];
        $modifycnccStatus = array_merge($modifycnccsuccess,$modifycnccerror);

        $finalModifys = $this->dao->select("objectID,createdDate")->from(TABLE_CONSUMED)
            ->where('deleted')->eq('0')
            ->andWhere('objectType')->eq('modify')
//            ->andWhere('after')->in($modifysuccess)
            ->andWhere('after')->in($modifyStatus)
            ->andWhere('createdDate')->ge($start)
            ->andWhere('createdDate')->le($end)
            ->fetchall('objectID');

        $finalModifycnccs = $this->dao->select("objectID,createdDate")->from(TABLE_CONSUMED)
            ->where('deleted')->eq('0')
            ->andWhere('objectType')->eq('outwarddelivery')
//            ->andWhere('after')->in($modifycnccsuccess)
            ->andWhere('after')->in($modifycnccStatus)
            ->andWhere('createdDate')->ge($start)
            ->andWhere('createdDate')->le($end)
            ->andWhere('createdDate')->le($end)
            ->fetchall('objectID');
        $finalModifycnccs = !empty($finalModifycnccs) ? $finalModifycnccs : [];
        $finalModifycnccs2 = $this->dao->select("objectID,createdDate")->from(TABLE_CONSUMED)
            ->where('deleted')->eq('0')
            ->andWhere('objectType')->eq('outwarddelivery')
            ->andWhere('extra')->eq('生产变更单')
//            ->andWhere('after')->in($modifycnccsuccess)
            ->andWhere('after')->in(['cancel'])
            ->andWhere('createdDate')->ge($start)
            ->andWhere('createdDate')->le($end)
            ->fetchall('objectID');
        $finalModifycnccs2 = !empty($finalModifycnccs2) ? $finalModifycnccs2 : [];
        $finalModifycnccs = array_merge($finalModifycnccs,$finalModifycnccs2);

        $finalModifyIds    = array_values(array_unique(array_column($finalModifys, 'objectID')));
        $modifys = $this->dao->select('id,code,status,projectPlanId,implementationForm,returnTime,changeSource,productInfoCode,mode,implementationForm,changeRemark,isDiskDelivery,externalCode')->from(TABLE_MODIFY)
            ->where('id')->in($finalModifyIds)
            ->andWhere("status")->in($modifyStatus)
//            ->andWhere("externalCode")->ne('')
            ->fetchall();

        $finalModifycnccIds    = array_values(array_unique(array_column($finalModifycnccs, 'objectID')));
        $modifycnccs = $this->dao->select('t1.changeRemark,t2.id,t1.code,t1.status,t2.projectPlanId,t2.implementationForm,t1.returnTimes,t1.changeSource,t2.id as tid,t2.productInfoCode,t1.mode,t2.implementationForm')->from(TABLE_MODIFYCNCC)->alias("t1")
            ->leftjoin(TABLE_OUTWARDDELIVERY)->alias("t2")
            ->on("t1.id=t2.modifycnccId")
            ->where('t2.id')->in($finalModifycnccIds)
            ->andWhere("t1.status")->in($modifycnccStatus+['cancel'])
            ->andWhere("t1.giteeId")->ne('')
            ->andWhere("t1.deleted")->eq('0')
            ->fetchall();

        // 有数据的部门
        $depts = [];
        foreach ($modifys as $key => $modify) {
            if ($modify->isDiskDelivery == 0 && $modify->externalCode == ''){
                unset($modifys[$key]);
            }
        }
        foreach ($modifys as $key => $modify) {
            foreach ($projects as $project) {
                if ($modify->projectPlanId == $project->project){
                    $depts[$project->bearDept] = $project->deptName;
                    $modifys[$key]->deptId = $project->bearDept;
                    $modifys[$key]->deptName = $project->deptName;
                    $modifys[$key]->method  = $project->mark;
                }
            }
        }

        foreach ($modifycnccs as $key => $modifycncc) {
            foreach ($projects as $project) {
                if ($modifycncc->projectPlanId == $project->project){
                    $depts[$project->bearDept] = $project->deptName;
                    $modifycnccs[$key]->deptId = $project->bearDept;
                    $modifycnccs[$key]->deptName = $project->deptName;
                    $modifycnccs[$key]->method  = $project->mark;
                }
            }
        }
        foreach ($modifys as $key => $modify) {
            $count = $modify->returnTime; //交付总次数
            if (in_array($modify->status,$modifysuccess)){
                $count = $modify->returnTime + 1; //交付总次数
            }
            if (in_array($modify->status,['modifysuccesspart','modifyerror','modifyrollback','modifycancel','modifyfail'])){
                $modifys[$key]->returnReason = $modify->changeRemark !='' ? '执行记录：'.$modify->changeRemark : '';
            }
            $modifys[$key]->count  = $count;
            $modifys[$key]->type = '生产变更单';
            $modifys[$key]->productionIsFail = '';
            $modifys[$key]->isCBP = '否';
            $modifys[$key]->modifyIsFail = '';
            $modifys[$key]->objectType = 'modify';
            $modifys[$key]->fixType    = $modify->implementationForm;

            $modifys[$key]->times = '1';
            if ($modify->mode == '1'){
                //投产移交
                $modifys[$key]->modifyIsFail = '/';
                if (in_array($modify->status,$modifyerror)){
                    $modifys[$key]->productionIsFail = '是';
                }else{
                    $modifys[$key]->productionIsFail = '否';
                }
            }else{
                //变更
                $modifys[$key]->productionIsFail = '/';
                if (in_array($modify->status,$modifyerror)){
                    $modifys[$key]->modifyIsFail = '是';
                }else{
                    $modifys[$key]->modifyIsFail = '否';
                }
            }
            if ($modify->status == 'modifycancel' || $modify->status == 'cancel'){
                $modifys[$key]->modifyIsFail = '/';
                $modifys[$key]->productionIsFail = '/';
            }
            foreach ($finalModifys as $finalModify) {
                if ($finalModify->objectID == $modify->id) {
                    $modifys[$key]->createdDate = $finalModify->createdDate;
                }
            }
            $modifys[$key]->statusEn  = $modify->status;
            $modifys[$key]->status    = $this->lang->modify->statusList[$modify->status];

        }

        foreach ($modifycnccs as $key2 => $modifycncc) {
            $count = $modifycncc->returnTimes; //交付总次数
            if (in_array($modifycncc->status,$modifycnccsuccess)){
                $count = $modifycncc->returnTimes + 1; //交付总次数
            }
            if (in_array($modifycncc->status,['cancel','modifysuccesspart','modifyfail','modifycancel'])){
                $modifycnccs[$key2]->returnReason = $modifycncc->changeRemark !='' ? '执行记录：'.$modifycncc->changeRemark : '';
            }
//            $count = $modifycncc->returnTimes + 1;
            $modifycnccs[$key2]->count  = $count;
            $modifycnccs[$key2]->type = '生产变更单';
            $modifycnccs[$key2]->returnTime = $modifycncc->returnTimes;
            $modifycnccs[$key2]->isCBP = '是';
            $modifycnccs[$key2]->objectType = 'modifycncc';
            $modifycnccs[$key2]->fixType    = $modifycncc->implementationForm;

            if ($modifycncc->mode == '1'){
                $modifycnccs[$key2]->modifyIsFail = '/';
                //投产移交
                if (in_array($modifycncc->status,$modifycnccerror)){
                    $modifycnccs[$key2]->productionIsFail = '是';
                }else{
                    $modifycnccs[$key2]->productionIsFail = '否';
                }
            }else{
                $modifycnccs[$key2]->productionIsFail = '/';
                //变更
                if (in_array($modifycncc->status,$modifycnccerror)){
                    $modifycnccs[$key2]->modifyIsFail = '是';
                }else{
                    $modifycnccs[$key2]->modifyIsFail = '否';
                }
            }
            if ($modifycncc->status == 'modifycancel'){
                $modifycnccs[$key2]->modifyIsFail = '/';
                $modifycnccs[$key2]->productionIsFail = '/';
            }
            foreach ($finalModifycnccs as $finalModifycncc) {
                if ($finalModifycncc->objectID == $modifycncc->tid) {
                    $modifycnccs[$key2]->createdDate = $finalModifycncc->createdDate;
                }
            }
            $modifycnccs[$key2]->times = '1';
            $modifycnccs[$key2]->statusEn  = $modifycncc->status;
            $modifycnccs[$key2]->status    = $this->lang->modifycncc->statusList[$modifycncc->status];
        }
        return [$modifys,$modifycnccs,$depts];
    }
}