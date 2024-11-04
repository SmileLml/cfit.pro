<?php
class outwarddeliveryModel extends model
{
    const OUTWARDDELIVERY = 'outwardDelivery';
    const TESTINGREQUEST = 'testingRequest';
    const PRODUCTENROLL = 'productEnroll';
    const MODIFYCNCC = 'modifycncc';
    const DEMAND = 'demand';
    const PROBLEM = 'problem';
    const REQUREMENT = 'requirement';
    const PROJECT = 'projectOD';
    const MAXNODE = 7;   //审批节点最大值是7
    const SYSTEMNODE = 3;   //系统部审批节点，可跳过

    static $_reviewers = '';

    /**
     * 创建对外交付 新建或者关联测试申请 产品登记 生产变更等子单
     * @return mixed
     */
    public function create()
    {
        $postData = fixer::input('post')
            ->join('problemId', ',')
            ->join('demandId', ',')
            ->join('secondorderId', ',')
            //->stripTags($this->config->outwarddelivery->editor->create['id'], $this->config->allowedTags)
            ->get();
        /* @var requirementModel $requirementModel*/
        $requirementModel = $this->loadModel('requirement');
        if ($postData->issubmit != 'save'){
            $this->app->loadLang('modifycncc');
            //变更锁提示
            $demandInfo = $this->loadModel('demand')->getDemandLockByIds(trim($postData->demandId,','));
            if(!empty($demandInfo)){
                $lockCode = implode(',',array_column($demandInfo,'code'));
                return dao::$errors[] = sprintf($this->lang->outwarddelivery->changeIngTip , $lockCode);
            }

            //判断交付类型必填
            if (empty(($_POST['isNewTestingRequest'][0])) && empty($_POST['isNewProductEnroll'][0]) && empty($_POST['isNewModifycncc'][0])) {
                dao::$errors['newTypeError'] = $this->lang->outwarddelivery->deliveryTypeEmpty; //这个errors 名可以随便起 不重复就行
            }
            //判断所属系统不能为空
            if ($postData->app[0] == '') {
                dao::$errors['app'] = sprintf($this->lang->outwarddelivery->emptyObject, $this->lang->outwarddelivery->app);
            }
            //判断是否关联需求单和问题单
            if (empty($postData->problemId) && empty($postData->demandId) && empty($postData->secondorderId)) {
                dao::$errors['relationTypeError'] = $this->lang->outwarddelivery->relationTypeError;
            }

            //关联需求条目 需判断条目所属需求任务是否外部已删除，如果外部已删除则不允许关联
            if(!empty($postData->demandId))
            {
                $deleteOutDataStr = $requirementModel->getRequirementInfos($postData->demandId);
            }
            if(!empty($deleteOutDataStr))
            {
                dao::$errors[] = sprintf($this->lang->outwarddelivery->deleteOutTip , $deleteOutDataStr);
                return false;
            }

            //判断联系人
            if (empty($postData->applyUsercontact)) {
                dao::$errors['applyUsercontact'] = sprintf($this->lang->outwarddelivery->emptyObject, $this->lang->outwarddelivery->applyUsercontact);
            }

            //判断工作量
           /* if (empty($postData->consumed)) {
                dao::$errors['consumed'] = sprintf($this->lang->outwarddelivery->emptyObject, $this->lang->outwarddelivery->consumed);
            }*/

            // 选择需求条目后，需求任务必填
            if (empty(implode(',', $postData->requirementId)) && !empty($postData->demandId)) {
                dao::$errors['requirementId'] = $this->lang->outwarddelivery->requirementIdError;
            }
            //判断电话号码
            if (!preg_match('/^1[0-9]{10}$/', $postData->applyUsercontact)) {
                dao::$errors['applyUsercontact'] = $this->lang->outwarddelivery->telError;
            }
            //判断审核人员是否填写
            if (!empty(($_POST['isNewTestingRequest'][0])) && empty($_POST['isNewProductEnroll'][0]) && empty($_POST['isNewModifycncc'][0])) {
                $this->checkReviewerNodesInfo($this->lang->outwarddelivery->requiredReviewerList[4], $this->post->nodes);
            } else if (!empty($_POST['isNewProductEnroll'][0]) && empty($_POST['isNewModifycncc'][0])) {
                $this->checkReviewerNodesInfo($this->lang->outwarddelivery->requiredReviewerList[5], $this->post->nodes);
            } else if (!empty($_POST['isNewModifycncc'][0])) {
                if (!$this->post->level) {
                    $this->checkReviewerNodesInfo($this->lang->outwarddelivery->requiredReviewerList[1], $this->post->nodes);
                } else {
                    $this->checkReviewerNodesInfo($this->lang->outwarddelivery->requiredReviewerList[$this->post->level], $this->post->nodes);
                }
            }

            //若该条目存在非终态的在途流程，则弹窗提示
            if(!dao::$errors){
                $isNewModifycncc = !empty($_POST['isNewModifycncc'][0]) ? 1 : 0;
                $demandCode = $this->loadModel('demand')->isSingleUsage($postData->demandId,'outwarddelivery', 0, $isNewModifycncc);
            }
            //当选择实施方式为：【人工实施、其他】时下方展示【不能自动化部署（AADS)原因说明】字段，单行文本框，必填，
            $implementModalityArr = [1,3,6];
            if (!empty($_POST['isNewModifycncc'][0]) && in_array($_POST['implementModality'],$implementModalityArr) && $_POST['aadsReason'] == ''){
                dao::$errors['aadsReason'] = $this->lang->outwarddelivery->aadsReasonError;
            }
            if (!empty($_POST['isNewModifycncc'][0])){
                if ($postData->isMakeAmends == ''){
                    dao::$errors['isMakeAmends'] = sprintf($this->lang->outwarddelivery->emptyObject , $this->lang->modify->isMakeAmends);
                }
                if ($postData->isMakeAmends == 'yes' && $postData->actualDeliveryTime == ''){
                    dao::$errors['actualDeliveryTime'] = sprintf($this->lang->outwarddelivery->emptyObject , $this->lang->modify->actualDeliveryTime);
                }
                if ((int)$_POST['changeForm'] <= 0){
                    dao::$errors['changeForm'] = sprintf($this->lang->outwarddelivery->emptyObject , $this->lang->modifycncc->changeForm);
                }
                if (in_array($_POST['implementModality'],[4,5]) && (int)$_POST['automationTools'] <= 0){
                    dao::$errors['automationTools'] = sprintf($this->lang->outwarddelivery->emptyObject , $this->lang->modifycncc->automationTools);
                }
            }
            // 紧急程度为“紧急”时
            if ($postData->type == 1) {
                if ($postData->urgentReason == ''){
                    dao::$errors['urgentReason'] = sprintf($this->lang->outwarddelivery->emptyObject, $this->lang->outwarddelivery->urgentReason);
                }
//                if (mb_strlen($postData->urgentReason) > 255){
//                    dao::$errors['urgentReason'] = $this->lang->outwarddelivery->urgentReasonLength;
//                    return false;
//                }
            }
        }
        if (dao::$errors) return dao::$errors;


        $this->tryError(); //检查报错


        $fixedOutwardDeliveryData = $this->fixOutwardDeliveryData(); //对外交付插入表的数组
        $fixedOutwardDeliveryData['isOutsideReject'] = 0;

        $this->tryError(); //检查报错

        $this->dao->begin(); //调试完逻辑最后开启事务
        $this->loadModel('testingrequest');
        if (!empty($_POST['isNewTestingRequest'][0])) {// 新建测试申请
            $testingRequestData = $this->testingrequest->fixTestingRequestData(); //测试申请插入表的数组
            $fixedOutwardDeliveryData['isNewTestingRequest'] = 1;
            $type = 'testingrequest';
        }
        $this->loadModel('productenroll');
        if (!empty($_POST['isNewProductEnroll'][0])) { //  新建产品登记
            $productEnrollData = $this->productenroll->fixProductEnrollData(); //产品登记插入表的数组
            $fixedOutwardDeliveryData['isNewProductEnroll'] = 1;
            $type = 'productenroll';
        }
        $this->loadModel('modifycncc');
        if (!empty($_POST['isNewModifycncc'][0])) { //  新建生产变更
            $fixModifycnccData = $this->modifycncc->fixModifycnccData(); //生产变更插入表的数组
            $fixedOutwardDeliveryData['isNewModifycncc'] = 1;
            $type = 'modifycncc';
        }
        $this->tryError(1); //检查报错 1= 需要rollback

        $this->dao->update(TABLE_DEFECT)
            ->set('testrequestId')->eq('')
            ->set('productenrollId')->eq('')
            ->set('testrequestCode')->eq('')
            ->set('productenrollCode')->eq('')
            ->where('id')->in(explode(',', $fixedOutwardDeliveryData['fixDefect'] . $fixedOutwardDeliveryData['leaveDefect']))->exec();

        //新建测试申请
        if (!empty($testingRequestData)) {
            $testingRequestId = $this->testingrequest->create($testingRequestData); //插入表
            $fixedOutwardDeliveryData['testingRequestId'] = $testingRequestId;
        }
        $this->tryError(1); //检查报错 1= 需要rollback

        //新建产品登记
        if (!empty($productEnrollData)) {
            $productEnrollId = $this->productenroll->create($productEnrollData); //插入表
            $fixedOutwardDeliveryData['productEnrollId'] = $productEnrollId;
        }
        $this->tryError(1); //检查报错 1= 需要rollback

        //新建生产变更
        if (!empty($fixModifycnccData)) {
            $modifycnccID = $this->modifycncc->createByData($fixModifycnccData); //插入表
            $fixedOutwardDeliveryData['modifycnccId'] = $modifycnccID;
        }
        $this->tryError(1); //检查报错 1= 需要rollback
        //新建对外交付
        $this->createOutwardDelivery($fixedOutwardDeliveryData); //插入表
        $lastId = $this->dao->lastInsertID();

        $this->dao->update(TABLE_DEFECT)->set('outwarddeliveryId')->eq($lastId)->set('CBPproject')->eq($fixedOutwardDeliveryData['CBPprojectId'])->where('id')->in(explode(',', $fixedOutwardDeliveryData['fixDefect'] . $fixedOutwardDeliveryData['leaveDefect']))->exec();

        $this->tryError(1); //检查报错 1= 需要rollback

        $this->modifycncc->submitReviewOutwardDelivery($lastId, 1, $fixedOutwardDeliveryData['level'], $type); //提交审批
        $this->loadModel('consumed')->record('outwarddelivery', $lastId, '0', $this->app->user->account, '', 'waitsubmitted', array());

        if (empty($testingRequestData) && $fixedOutwardDeliveryData['testingRequestId']) { //如果非新建 且关联 添加关联关系
            $this->addSecondLineRelation($lastId, $fixedOutwardDeliveryData['testingRequestId'], self::TESTINGREQUEST);
        }
        if (empty($productEnrollData) && $fixedOutwardDeliveryData['productEnrollId']) {
            $this->addSecondLineRelation($lastId, $fixedOutwardDeliveryData['productEnrollId'], self::PRODUCTENROLL);
        }
        if (empty($fixModifycnccData) && $fixedOutwardDeliveryData['modifycnccId']) {
            $this->addSecondLineRelation($lastId, $fixedOutwardDeliveryData['modifycnccId'], self::MODIFYCNCC);
        }
        $this->addSecondLineProblem($lastId, $fixedOutwardDeliveryData['problemId']); //问题关联
        $this->addSecondLineDemand($lastId, $fixedOutwardDeliveryData['demandId']);  //需求关联
        $this->addSecondLineRequirement($lastId, $fixedOutwardDeliveryData['requirementId']); //需求任务关联
        $this->addSecondLineProject($lastId, $fixedOutwardDeliveryData['projectPlanId']); //
        $this->addSecondLinesecondorder($lastId, $fixedOutwardDeliveryData['secondorderId']); //二线工单
        $this->tryError(1); //检查报错 1= 需要rollback
        $this->dao->commit(); //调试完逻辑最后开启事务

        //创建者修改二线默认打开地址-需求收集1375
        $item = new stdclass();
        $item->owner = $this->app->user->account;
        $item->module = 'common';
        $item->key = 'secondLink';
        $item->value = 'outwarddelivery-browse';
        $this->dao->replace(TABLE_CONFIG)->data($item)->exec();

        //新建关联二线，解决时间置空
        /** @var problemModel $problemModel*/
        $problemModel = $this->loadModel('problem');
        $outward = $this->getByID($lastId);
//        if($outward->modifycnccId > 0)
//        {
//            if(!empty($outward->demandId)){
//                $problemModel->dealSolveTime($outward->demandId,'demand',$outward->code);
//            }
//            /*if(!empty($outward->problemId)){
//                $problemModel->dealSolveTime($outward->problemId,'problem',$outward->code);
//            }*/
//        }
        if ((int)$_POST['abnormalCode'] > 0){
            //如果关联了异常变更单
            $this->editModifyAbnormal($fixedOutwardDeliveryData['modifycnccId'],$_POST['abnormalCode']);
        }
        return $lastId;
    }


    /**
     * 编辑对外交付
     * @param $id
     * @param $source
     * @return mixed
     */
    public function edit($id,$source='')
    {
        $postData = fixer::input('post')
            ->join('problemId', ',')
            ->join('demandId', ',')
            ->join('secondorderId', ',')
            ->join('leaveDefect', ',')
            ->join('fixDefect', ',')
            //->stripTags($this->config->outwarddelivery->editor->edit['id'], $this->config->allowedTags)
            ->get();
        $oldOutwardDeliveryData = $this->getByID($id);
        // 判断已关闭的问题单不可被关联
        if($postData->problemId){
            $problemIds = array_filter(explode(',', $postData->problemId));
            $res = $this->loadModel('problem')->checkIsClosed($problemIds);
            if (!$res['result']){
                dao::$errors['problemId'] = $res['msg'];
                return false;
            }
        }
        if(!in_array($oldOutwardDeliveryData->status,$this->lang->outwarddelivery->alloweditStatus))
        {
            dao::$errors['editerror'] = $this->lang->outwarddelivery->editStatusError;
            return false;
        }
        if ($postData->issubmit != 'save'){
            //判断交付类型必填
            if (empty(($_POST['isNewTestingRequest'][0])) && empty($_POST['isNewProductEnroll'][0]) && empty($_POST['isNewModifycncc'][0])) {
                dao::$errors['newTypeError'] = $this->lang->outwarddelivery->deliveryTypeEmpty; //这个errors 名可以随便起 不重复就行
            }
            //判断所属系统不能为空
            if ($postData->app[0] == '') {
                dao::$errors['app'] = sprintf($this->lang->outwarddelivery->emptyObject, $this->lang->outwarddelivery->app);
            }
            if(!empty($postData->demandId)){
                //迭代三十加变更锁
                $demandInfo = $this->loadModel('demand')->getDemandLockByIds(trim($postData->demandId,','));
                if(!empty($demandInfo)){
                    $lockCode = implode(',',array_column($demandInfo,'code'));
                    dao::$errors[] = sprintf('关联需求条目'.$lockCode.'所属需求任务或意向正在变更，当前流程锁死，待变更流程结束后再进行后续操作。');
                }
            }

            //判断审核人员是否填写
            if (!empty(($_POST['isNewTestingRequest'][0])) && empty($_POST['isNewProductEnroll'][0]) && empty($_POST['isNewModifycncc'][0])) {
                $this->checkReviewerNodesInfo($this->lang->outwarddelivery->requiredReviewerList[4], $this->post->nodes);
            } else if (!empty($_POST['isNewProductEnroll'][0]) && empty($_POST['isNewModifycncc'][0])) {
                $this->checkReviewerNodesInfo($this->lang->outwarddelivery->requiredReviewerList[5], $this->post->nodes);
            } else if (!empty($_POST['isNewModifycncc'][0])) {
                if (!$this->post->level) {
                    $this->checkReviewerNodesInfo($this->lang->outwarddelivery->requiredReviewerList[1], $this->post->nodes);
                } else {
                    $this->checkReviewerNodesInfo($this->lang->outwarddelivery->requiredReviewerList[$this->post->level], $this->post->nodes);
                }
            }
            //判断是否关联需求单和问题单
            if (empty($postData->problemId) && empty($postData->demandId) && empty($postData->secondorderId)) {
                dao::$errors['relationTypeError'] = $this->lang->outwarddelivery->relationTypeError;
            }
            //判断联系人
            if (empty($postData->applyUsercontact)) {
                dao::$errors['applyUsercontact'] = sprintf($this->lang->outwarddelivery->emptyObject, $this->lang->outwarddelivery->applyUsercontact);
            }
            //判断电话号码
            if (!preg_match('/^1[0-9]{10}$/', $postData->applyUsercontact)) {
                dao::$errors['applyUsercontact'] = $this->lang->outwarddelivery->telError;
            }

            //判断工作量
            /*if (empty($postData->consumed)) {
                dao::$errors['consumed'] = sprintf($this->lang->outwarddelivery->emptyObject, $this->lang->outwarddelivery->consumed);
            }*/

            // 选择需求条目后，需求任务必填
            if (empty(implode(',', $postData->requirementId)) && !empty($postData->demandId)) {
                dao::$errors['requirementId'] = $this->lang->outwarddelivery->requirementIdError;
            }
            //来源不为空 不需要判断修订记录必填
            if ($source == ''){
                //修订记录
                if (empty($postData->ROR)) {
                    dao::$errors['ROR'] = sprintf($this->lang->outwarddelivery->emptyObject, $this->lang->outwarddelivery->ROR);
                }
            }else{
                $this->config->outwarddelivery->edit->requiredFields = str_replace(',ROR','',$this->config->outwarddelivery->edit->requiredFields);
            }
            //当选择实施方式为：【人工实施、其他】时下方展示【不能自动化部署（AADS)原因说明】字段，单行文本框，必填，
            $implementModalityArr = [1,3,6];
            if (!empty($_POST['isNewModifycncc'][0]) && isset($_POST['implementModality']) && in_array($_POST['implementModality'],$implementModalityArr) && $_POST['aadsReason'] == ''){
                dao::$errors['aadsReason'] = $this->lang->outwarddelivery->aadsReasonError;
            }
            if (!empty($_POST['isNewModifycncc'][0])){
                if ($postData->isMakeAmends == ''){
                    dao::$errors['isMakeAmends'] = sprintf($this->lang->outwarddelivery->emptyObject , $this->lang->modify->isMakeAmends);
                }
                if ($postData->isMakeAmends == 'yes' && $postData->actualDeliveryTime == ''){
                    dao::$errors['actualDeliveryTime'] = sprintf($this->lang->outwarddelivery->emptyObject , $this->lang->modify->actualDeliveryTime);
                }
                if ((int)$_POST['changeForm'] <= 0){
                    dao::$errors['changeForm'] = sprintf($this->lang->outwarddelivery->emptyObject , $this->lang->modifycncc->changeForm);
                }
                if (in_array($_POST['implementModality'],[4,5]) && (int)$_POST['automationTools'] <= 0){
                    dao::$errors['automationTools'] = sprintf($this->lang->outwarddelivery->emptyObject , $this->lang->modifycncc->automationTools);
                }
            }
            // 紧急程度为“紧急”时
            if ($postData->type == 1) {
                if ($postData->urgentReason == ''){
                    dao::$errors['urgentReason'] = sprintf($this->lang->outwarddelivery->emptyObject, $this->lang->outwarddelivery->urgentReason);
                }
//                if (mb_strlen($postData->urgentReason) > 255){
//                    dao::$errors['urgentReason'] = $this->lang->outwarddelivery->urgentReasonLength;
//                    return false;
//                }
            }
            //若该条目存在非终态的在途流程，则弹窗提示
            if(!dao::$errors){
                $isNewModifycncc = !empty($_POST['isNewModifycncc'][0]) ? 1 : 0;
                $demandCode = $this->loadModel('demand')->isSingleUsage($postData->demandId, 'outwarddelivery', $id, $isNewModifycncc);
            }
        }
        if (dao::$errors) return dao::$errors;
        $fixedOutwardDeliveryData = $this->fixOutwardDeliveryData(1);

        $this->tryError();
        //修改评审记录
        $rorOld = $oldOutwardDeliveryData->ROR;
        if (empty($rorOld)) {
            $array = array();
        } else {
            $array = json_decode(json_encode($rorOld), true);
        }
        $arrayNew = [];
        if($fixedOutwardDeliveryData['ROR']){
            $arrayNew['RORDate'] = helper::now();
            $arrayNew['RORContent'] = $fixedOutwardDeliveryData['ROR'];
        }
        if (!empty($arrayNew)){
            array_push($array, $arrayNew);
        }
        if (!empty($array)){
            $fixedOutwardDeliveryData['ROR'] = json_encode($array);
        }else{
            $fixedOutwardDeliveryData['ROR'] = '';
        }

        $this->loadModel('testingrequest');
        if (!empty($_POST['isNewTestingRequest'][0]) && $oldOutwardDeliveryData->isNewTestingRequest == 0) {//原来的是关联 现在新建测试申请
            $testingRequestData = $this->testingrequest->fixTestingRequestData(); //整合新建内容
            $fixedOutwardDeliveryData['isNewTestingRequest'] = 1;
            $fixedOutwardDeliveryData['modifyLevel'] = '2';
        } elseif (!empty($_POST['isNewTestingRequest'][0]) && $oldOutwardDeliveryData->isNewTestingRequest == 1) { // 原来就是新建的 这样更新
            $testingRequestData = $this->testingrequest->fixTestingRequestData(1); //整合更新内容
            $fixedOutwardDeliveryData['isNewTestingRequest'] = 1;
        }
        if (empty($_POST['isNewTestingRequest'][0]) && $oldOutwardDeliveryData->isNewTestingRequest == 1) {
            $fixedOutwardDeliveryData['isNewTestingRequest'] = 0;
            $this->testingrequest->updateDeleteStatus($oldOutwardDeliveryData->testingRequestId, 1);
            $fixedOutwardDeliveryData['modifyLevel'] = '2';
        }
        //原来就是关联的 现在还是关联的 不用处理 取关联id就行了
        $this->loadModel('productenroll');
        if (!empty($_POST['isNewProductEnroll'][0]) && $oldOutwardDeliveryData->isNewProductEnroll == 0) { //  新建产品登记 原来没有或者是关联
            $productEnrollData = $this->productenroll->fixProductEnrollData();
            $fixedOutwardDeliveryData['isNewProductEnroll'] = 1;
            //修改了变更级别
            $fixedOutwardDeliveryData['modifyLevel'] = '2';
        } elseif (!empty($_POST['isNewProductEnroll'][0]) && $oldOutwardDeliveryData->isNewProductEnroll == 1) { //原来就是新建的 现在更新
            $productEnrollData = $this->productenroll->fixProductEnrollData(1);
            $fixedOutwardDeliveryData['isNewProductEnroll'] = 1;
        }
        if (empty($_POST['isNewProductEnroll'][0]) && $oldOutwardDeliveryData->isNewProductEnroll == 1) {
            $fixedOutwardDeliveryData['isNewProductEnroll'] = 0;
            $this->productenroll->updateDeleteStatus($oldOutwardDeliveryData->productEnrollId, 1);
            $fixedOutwardDeliveryData['modifyLevel'] = '2';
        }
        $this->loadModel('modifycncc');
        if (!empty($_POST['isNewModifycncc'][0]) && $oldOutwardDeliveryData->isNewModifycncc == 0) { //  现在要新建 原来不是新建的 新建
            $fixModifycnccData = $this->modifycncc->fixModifycnccData();
            $fixedOutwardDeliveryData['isNewModifycncc'] = 1;
            $fixedOutwardDeliveryData['isOutsideReject'] = 0;
            //修改了变更级别
            $fixedOutwardDeliveryData['modifyLevel'] = '2';
        } elseif (!empty($_POST['isNewModifycncc'][0]) && $oldOutwardDeliveryData->isNewModifycncc == 1) { //现在要新建 原来也是新建的 更新
            $fixModifycnccData = $this->modifycncc->fixModifycnccData(1);
            $fixedOutwardDeliveryData['isNewModifycncc'] = 1;
            $oldModifycnccData = $this->modifycncc->getByID($oldOutwardDeliveryData->modifycnccId);
            if ($oldModifycnccData->level != $fixedOutwardDeliveryData['level']) {
                $fixedOutwardDeliveryData['isOutsideReject'] = 0;
                //修改了变更级别
                $fixedOutwardDeliveryData['modifyLevel'] = '2';
            }
        }
        if (empty($_POST['isNewModifycncc'][0]) && $oldOutwardDeliveryData->isNewModifycncc == 1) {
            $fixedOutwardDeliveryData['isNewModifycncc'] = 0;
            $this->modifycncc->updateDeleteStatus($oldOutwardDeliveryData->modifycnccId, 1);
            $fixedOutwardDeliveryData['isOutsideReject'] = 0;
            $fixedOutwardDeliveryData['modifyLevel'] = '2';
        }
        if('save' != $oldOutwardDeliveryData->issubmit && ($oldOutwardDeliveryData->status == 'reject' || $oldOutwardDeliveryData->status == 'reviewfailed')){
            $fixedOutwardDeliveryData['version'] = $oldOutwardDeliveryData->version + 1;
        }else{
            $fixedOutwardDeliveryData['version'] = $oldOutwardDeliveryData->version;
        }

        //以上是校验字段 下面开始插入或更新表
        $this->tryError(); //检查报错
        $this->dao->begin(); //调试完逻辑最后开启事务
        //$this->deleteWaitConsume($id);
        $changes = array();

        // 更新之前先置空
        $this->dao->update(TABLE_DEFECT)
            ->set('testrequestId')->eq('')
            ->set('productenrollId')->eq('')
            ->set('testrequestCode')->eq('')
            ->set('productenrollCode')->eq('')
            ->set('outwarddeliveryId')->eq('')
            ->set('ifTest')->eq('')
            ->set('testType')->eq('')
            ->set('realtedApp')->eq('')
            ->where('id')->in(array_unique(explode(',', $fixedOutwardDeliveryData['fixDefect'] . $fixedOutwardDeliveryData['leaveDefect'] . $oldOutwardDeliveryData->fixDefect . $oldOutwardDeliveryData->leaveDefect)))->exec();

        //更新测试申请数据
        if (!empty($testingRequestData)) {
            $testingRequestData['version'] = $fixedOutwardDeliveryData['version'];
            if (!empty($_POST['isNewTestingRequest'][0]) && $oldOutwardDeliveryData->isNewTestingRequest == 0) {//原来的是关联 现在新建测试申请
                $changes['testingrequest'] = 'create';
                $testingRequestId = $this->testingrequest->create($testingRequestData);
                $fixedOutwardDeliveryData['testingRequestId'] = $testingRequestId;
            } elseif (!empty($_POST['isNewTestingRequest'][0]) && $oldOutwardDeliveryData->isNewTestingRequest == 1) { // 原来就是新建的 这样更新
                $oldTestingRequestData = $this->testingrequest->getByID($oldOutwardDeliveryData->testingRequestId);
                if (in_array($oldTestingRequestData->status,$this->lang->outwarddelivery->alloweditStatus)) {
                    $testingrequestChanges = $this->changesCommon($testingRequestData, $oldTestingRequestData);
                    if('save' == $postData->issubmit && in_array($oldTestingRequestData->status, ['waitsubmitted','reviewfailed','reject'])){
                        $testingRequestData['status'] = $oldTestingRequestData->status;
                    }
                    $changes['testingrequest'] = $testingrequestChanges;
                    $this->testingrequest->edit($oldOutwardDeliveryData->testingRequestId, $testingRequestData, $oldOutwardDeliveryData);
                }
                $fixedOutwardDeliveryData['testingRequestId'] = $oldOutwardDeliveryData->testingRequestId;
            }
            $type = 'testingrequest';
        }

        //更新产品登记
        if (!empty($productEnrollData)) {
            $productEnrollData['version'] = $fixedOutwardDeliveryData['version'];
            if (!empty($_POST['isNewProductEnroll'][0]) && $oldOutwardDeliveryData->isNewProductEnroll == 0) { //  新建产品登记 原来没有或者是关联
                $changes['productenroll'] = 'create';
                $productEnrollId = $this->productenroll->create($productEnrollData);
                $fixedOutwardDeliveryData['productEnrollId'] = $productEnrollId;
            } elseif (!empty($_POST['isNewProductEnroll'][0]) && $oldOutwardDeliveryData->isNewProductEnroll == 1) { //原来就是新建的 现在更新
                $oldProductenrollData = $this->productenroll->getByID($oldOutwardDeliveryData->productEnrollId);
                if (in_array($oldProductenrollData->status,$this->lang->outwarddelivery->alloweditStatus)) {
                    $productenrollChanges = $this->changesCommon($productEnrollData, $oldProductenrollData);
                    $changes['productenroll'] = $productenrollChanges;
                    if('save' == $postData->issubmit && in_array($oldProductenrollData->status, ['waitsubmitted','reviewfailed','reject'])){
                        $productEnrollData['status'] = $oldProductenrollData->status;
                    }
                    $this->productenroll->edit($oldOutwardDeliveryData->productEnrollId, $productEnrollData);
                }
                $fixedOutwardDeliveryData['productEnrollId'] = $oldOutwardDeliveryData->productEnrollId;
            }
            $type = 'productenroll';
        }
        //更新生产变更
        if (!empty($fixModifycnccData)) {
            $fixModifycnccData['version'] = $fixedOutwardDeliveryData['version'];
            if (!empty($_POST['isNewModifycncc'][0]) && $oldOutwardDeliveryData->isNewModifycncc == 0) { //  现在要新建 原来不是新建的 新建
                $changes['modifycncc'] = 'create';
                $modifycnccID = $this->modifycncc->createByData($fixModifycnccData);
                $fixedOutwardDeliveryData['modifycnccId'] = $modifycnccID;
            } elseif (!empty($_POST['isNewModifycncc'][0]) && $oldOutwardDeliveryData->isNewModifycncc == 1) { //现在要新建 原来也是新建的 更新
                $oldModifycnccData = $this->modifycncc->getByID($oldOutwardDeliveryData->modifycnccId);
                if (in_array($oldModifycnccData->status,$this->lang->outwarddelivery->alloweditStatus)) {
                    if('save' == $postData->issubmit && in_array($oldModifycnccData->status, ['waitsubmitted','reviewfailed','reject'])){
                        $fixModifycnccData['status'] = $oldModifycnccData->status;
                    }
                    $modifycnccChanges = $this->changesCommon($fixModifycnccData, $oldModifycnccData);
                    $changes['modifycncc'] = $modifycnccChanges;
                    $this->modifycncc->updateByData($oldOutwardDeliveryData->modifycnccId, $fixModifycnccData, $id);
                    //若被外部退回，重新走流程需要通知清总
                    if(!empty($oldModifycnccData->giteeId) and $oldModifycnccData->status == 'reject'){
                        $this->pushModifycnccState($oldModifycnccData->code, zget($this->lang->outwarddelivery->statusList, 'waitsubmitted'));
                    }
                }
                $fixedOutwardDeliveryData['modifycnccId'] = $oldOutwardDeliveryData->modifycnccId;
            }
            $type = 'modifycncc';
        }

        //更新对外交付
        if('save' == $postData->issubmit && in_array($oldOutwardDeliveryData->status, ['waitsubmitted','reviewfailed','reject'])){
            $fixedOutwardDeliveryData['status'] = $oldOutwardDeliveryData->status;
        }
        $outwarddeliveryChanges = $this->changesCommon($fixedOutwardDeliveryData, $oldOutwardDeliveryData);
        $changes['outwarddelivery'] = $outwarddeliveryChanges;
        $fixedOutwardDeliveryData['release'] = $oldOutwardDeliveryData->release;
        $this->update($id, $fixedOutwardDeliveryData);

        $this->dao->update(TABLE_DEFECT)->set('outwarddeliveryId')->eq($id)->set('CBPproject')->eq($fixedOutwardDeliveryData['CBPprojectId'])->where('id')->in(explode(',', $fixedOutwardDeliveryData['fixDefect'] . $fixedOutwardDeliveryData['leaveDefect']))->exec();

        //检查审核信息
        if ('save' != $oldOutwardDeliveryData->issubmit && ($oldOutwardDeliveryData->status == 'reject' || $oldOutwardDeliveryData->status == 'reviewfailed')) {
            //已退回区分为外部为退回，内部为审核未通过，保持原逻辑
            //$oldOutwardDeliveryData->status = 'waitsubmitted';
            $oldOutwardDeliveryData->version = $oldOutwardDeliveryData->version + 1;
            $oldOutwardDeliveryData->reviewStage = 0;
            if (empty($oldOutwardDeliveryData->level)) {
                $oldOutwardDeliveryData->level = 0;
            }
            $this->modifycncc->submitReviewOutwardDelivery($id, $oldOutwardDeliveryData->version, $fixModifycnccData['level'] - 0, $type);
            $this->loadModel('consumed')->record('outwarddelivery', $id, '0', $this->app->user->account, $oldOutwardDeliveryData->status, $fixedOutwardDeliveryData['status'], array());
        } else if ($oldOutwardDeliveryData->status == 'wait') {
            $oldOutwardDeliveryData->reviewStage = 0;
            if (empty($oldOutwardDeliveryData->level)) {
                $oldOutwardDeliveryData->level = 0;
            }
            $this->modifycncc->submitEditReviewOutwardDelivery($id, $oldOutwardDeliveryData->version, $fixModifycnccData['level'] - 0, $type);
            $this->loadModel('consumed')->record('outwarddelivery', $id,'0', $this->app->user->account, 'wait', 'waitsubmitted', array());
        } else {
            if (empty($oldOutwardDeliveryData->level)) {
                $oldOutwardDeliveryData->level = 0;
            }

            $this->modifycncc->submitEditReviewOutwardDelivery($id, $oldOutwardDeliveryData->version, $fixModifycnccData['level'] - 0, $type);
            //$this->loadModel('consumed')->remove('outwarddelivery', $id, $this->app->user->account, $oldOutwardDeliveryData->status); //逻辑删除原有状态 只保留最新的
            //$this->loadModel('consumed')->record('outwarddelivery', $id, $_POST['consumed'], $this->app->user->account, '', 'waitsubmitted', array());
            $lastConsumed = $this->loadModel('consumed')->getLastConsumed($id, 'outwarddelivery');
            $this->loadModel('consumed')->update($lastConsumed->id, $lastConsumed->objectType, $id, '0', $this->app->user->account,
                $lastConsumed->before, $lastConsumed->after);
        }

        $this->removeSecondLine($id); //将原来的关系删除 建立新所有的关系
        if (empty($testingRequestData) && $fixedOutwardDeliveryData['testingRequestId']) { //如果非新建 且关联 添加关联关系
            $this->addSecondLineRelation($id, $fixedOutwardDeliveryData['testingRequestId'], self::TESTINGREQUEST);
        }
        if (empty($productEnrollData) && $fixedOutwardDeliveryData['productEnrollId']) {
            $this->addSecondLineRelation($id, $fixedOutwardDeliveryData['productEnrollId'], self::PRODUCTENROLL);
        }
        if (empty($fixModifycnccData) && $fixedOutwardDeliveryData['modifycnccId']) {
            $this->addSecondLineRelation($id, $fixedOutwardDeliveryData['modifycnccId'], self::MODIFYCNCC);
        }
        $this->addSecondLineProblem($id, $fixedOutwardDeliveryData['problemId']); //问题关联
        $this->addSecondLineDemand($id, $fixedOutwardDeliveryData['demandId']);  //需求关联
        $this->addSecondLineRequirement($id, $fixedOutwardDeliveryData['requirementId']); //需求任务关联
        $this->addSecondLineProject($id, $fixedOutwardDeliveryData['projectPlanId']); //
        $this->addSecondLinesecondorder($id, $fixedOutwardDeliveryData['secondorderId']); //二线工单

        $this->tryError(1); //检查报错 1= 需要rollback
//        if ((int)$_POST['abnormalCode'] > 0){
//            //如果关联了异常变更单
//            $this->editModifyAbnormal($fixedOutwardDeliveryData['modifycnccId'],$_POST['abnormalCode']);
//        }
        $this->editModifyAbnormal($fixedOutwardDeliveryData['modifycnccId'],(int)$_POST['abnormalCode']);

        $this->dao->commit(); //调试完逻辑最后开启事务
        return $changes;
    }

    /**
     * 创建对外交付表
     * @param $fixedOutwardDeliveryData
     * @return mixed
     */
    private function createOutwardDelivery($fixedOutwardDeliveryData)
    {
        return $this->dao->insert(TABLE_OUTWARDDELIVERY)
            ->data($fixedOutwardDeliveryData)
            ->batchCheckIF($_POST['issubmit'] != 'save',$this->config->outwarddelivery->create->requiredFields, 'notempty')
            ->exec();
    }

    /**
     * 更新
     * @param $id
     * @return mixed
     */
    public function update($id, $data)
    {
        /* @var requirementModel $requirementModel*/
        $requirementModel = $this->loadModel('requirement');
        //关联需求条目 需判断条目所属需求任务是否外部已删除，如果外部已删除则不允许关联
        $demandIds = $_POST['demandId'];
        if(!empty($demandIds))
        {
            $deleteOutDataStr = $requirementModel->getRequirementInfos($demandIds);

        }
        if(!empty($deleteOutDataStr))
        {
            dao::$errors[] = sprintf($this->lang->outwarddelivery->deleteOutTip , $deleteOutDataStr);
            return false;
        }
        $res = $this->dao->update(TABLE_OUTWARDDELIVERY)
            ->data($data)
            ->batchCheckIF($_POST['issubmit'] != 'save',$this->config->outwarddelivery->edit->requiredFields, 'notempty')
            ->where('id')->eq((int)$id)->exec();
        return $res;
    }

    /**
     * 更新审核
     * @param $id
     * @param $status
     * @param $reviewStage
     * @param $level
     * @return mixed
     */
    public function updateReview($id, $status, $reviewStage, $level)
    {
        $data['status'] = $status;
        $data['reviewStage'] = $reviewStage;
        $data['level'] = $level;
        $res = $this->dao->update(TABLE_OUTWARDDELIVERY)
            ->data($data)
            ->where('id')->eq((int)$id)->exec();
        return $res;
    }

    public function view($id)
    {
        return $this->dao->select('*')->from(TABLE_OUTWARDDELIVERY)->where('id')->eq($id)->fetch($id);
    }

    /**
     * 生成code
     * @return string
     */
    public function getCode()
    {
        $prefix = 'CFIT-WQ-' . date('Ymd-');
        $number = $this->dao->select('count(id) c')->from(TABLE_OUTWARDDELIVERY)->where('code')->like("$prefix%")->fetch('c');
        $number = intval($number) + 1;
        $code = $prefix . sprintf('%02d', $number);
        return $code;
    }

    /**
     * 列表方法
     * @param $browseType
     * @param $queryID
     * @param $orderBy
     * @param null $pager
     * @return mixed
     */
    public function getList($browseType, $queryID, $orderBy, $pager = null)
    {
        // if($browseType == 'testingrequestpass' || $browseType == 'testingrequestreject'){
        //     return $this->getTestingRequestList($browseType, $queryID, $orderBy, $pager);
        // }
        // if($browseType == 'productenrollpass' || $browseType == 'productenrollreject'){
        //     return $this->getProductEnrollList($browseType, $queryID, $orderBy, $pager);
        // }
        $outwardDeliveryQuery = '';

        $testingQuery = 0;
        $productEnrollQuery = 0;
        $modifycnccQuery = 0;
        $appQuery = 0;


        if ($browseType == 'bysearch') {
            $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : '';
            if ($query) {
                $this->session->set('outwardDeliveryQuery', $query->sql);
                $this->session->set('outwardDeliveryForm', $query->form);
            }
            if ($this->session->outwardDeliveryQuery == false) $this->session->set('outwardDeliveryQuery', ' 1 = 1');
            $outwardDeliveryQuery = $this->session->outwardDeliveryQuery;
            $outwardDeliveryQuery = str_replace('AND `', ' AND `t1.', $outwardDeliveryQuery);
            $outwardDeliveryQuery = str_replace('AND (`', ' AND (`t1.', $outwardDeliveryQuery);
            $outwardDeliveryQuery = str_replace('`', '', $outwardDeliveryQuery);

            if (strpos($outwardDeliveryQuery, 'productCode') !== false) {
                $outwardDeliveryQuery = str_replace('productCode', "productInfoCode", $outwardDeliveryQuery);
            }

            if (strpos($outwardDeliveryQuery, 'testingRequestReturnTimes') !== false) {
                $outwardDeliveryQuery = str_replace('t1.testingRequestReturnTimes', "t2.returnTimes", $outwardDeliveryQuery);
                $testingQuery = 1;
            }

            if (strpos($outwardDeliveryQuery, 'productEnrollReturnTimes') !== false) {
                $outwardDeliveryQuery = str_replace('t1.productEnrollReturnTimes', "t3.returnTimes", $outwardDeliveryQuery);
                $productEnrollQuery = 1;
            }

            if (strpos($outwardDeliveryQuery, 'modifycnccReturnTimes') !== false) {
                $outwardDeliveryQuery = str_replace('t1.modifycnccReturnTimes', "t4.returnTimes", $outwardDeliveryQuery);
                $modifycnccQuery = 1;
            }
            if (strpos($outwardDeliveryQuery, 'urgentSource') !== false) {
                $outwardDeliveryQuery = str_replace('t1.urgentSource', "t4.urgentSource", $outwardDeliveryQuery);
                $modifycnccQuery = 1;
            }
            if (strpos($outwardDeliveryQuery, 'isMakeAmends') !== false) {
                $outwardDeliveryQuery = str_replace('t1.isMakeAmends', "t4.isMakeAmends", $outwardDeliveryQuery);
                $modifycnccQuery = 1;
            }
            if (strpos($outwardDeliveryQuery, 'implementModality') !== false) {
                $outwardDeliveryQuery = str_replace('t1.implementModality', "t4.implementModality", $outwardDeliveryQuery);
                $modifycnccQuery = 1;
            }
            if (strpos($outwardDeliveryQuery, 'automationTools') !== false) {
                $outwardDeliveryQuery = str_replace('t1.automationTools', "t4.automationTools", $outwardDeliveryQuery);
                $modifycnccQuery = 1;
            }
            if (strpos($outwardDeliveryQuery, 'changeForm') !== false) {
                $outwardDeliveryQuery = str_replace('t1.changeForm', "t4.changeForm", $outwardDeliveryQuery);
                $modifycnccQuery = 1;
            }

            if (strpos($outwardDeliveryQuery, 'isPayment') !== false) {
                $outwardDeliveryQuery = str_replace('t1.isPayment', "t5.isPayment", $outwardDeliveryQuery);
                $appQuery = 1;
            }

            if (strpos($outwardDeliveryQuery, 'team') !== false) {
                $outwardDeliveryQuery = str_replace('t1.team', "t5.team", $outwardDeliveryQuery);
                $appQuery = 1;
            }
            if(strpos($outwardDeliveryQuery, ',app') !== false){
                $outwardDeliveryQuery = str_replace(',app', ",t1.app", $outwardDeliveryQuery);
            }
            //退回原因（子项）搜索 json
            if (strpos($outwardDeliveryQuery, 'revertReason') ){
                $queryData = explode('AND',$outwardDeliveryQuery);
                foreach ($queryData as $qk=>$qv){
                    if (strpos($qv, 'revertReason')){
                        $revertArr = explode("'",$qv);
                        $str = '';
                        if (strpos('.'.$revertArr[1], '%')){
                            $str = '%';
                        }
                        $revertQueryStr = "'".$str.base64_decode(str_replace('%','',$revertArr[1])).$str."'";
                        $queryData[$qk] = $revertArr[0].$revertQueryStr.$revertArr[2];
                    }
                }
                $outwardDeliveryQuery = implode('AND',$queryData);
            }


        }
        $data = $this->dao->select('t1.*')->from(TABLE_OUTWARDDELIVERY)->alias('t1')
            ->beginIF($testingQuery == 1)->innerJoin(TABLE_TESTINGREQUEST)->alias('t2')->on('t1.testingRequestId=t2.id')->fi()
            ->beginIF($productEnrollQuery == 1)->innerJoin(TABLE_PRODUCTENROLL)->alias('t3')->on('t1.productEnrollId=t3.id')->fi()
            ->beginIF($modifycnccQuery == 1)->innerJoin(TABLE_MODIFYCNCC)->alias('t4')->on('t1.modifycnccId=t4.id')->fi()
            ->beginIF($appQuery == 1)->innerJoin(TABLE_APPLICATION)->alias('t5')->on('FIND_IN_SET(t5.id,t1.app)')->fi()
            ->where('t1.deleted')->ne(1)
            ->beginIF($browseType == 'closed')->andWhere('t1.closed')->eq(1)->fi()
            ->beginIF($browseType != 'all' and $browseType != 'bysearch' and $browseType != 'closed')->andWhere('t1.status')->eq($browseType)->andWhere('t1.closed')->ne(1)->fi()
            ->beginIF($browseType == 'bysearch')->andWhere($outwardDeliveryQuery)->fi()
            ->orderBy($orderBy)
            ->page($pager, 't1.id')
            ->fetchAll('id');
        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'outwardDelivery', $browseType != 'bysearch');

        return $data;
    }

    public function getTestingRequestList($browseType, $queryID, $orderBy, $pager = null)
    {
        $data = $this->dao->select('t1.*')->from(TABLE_OUTWARDDELIVERY)->alias('t1')
            ->leftJoin(TABLE_TESTINGREQUEST)->alias('t2')
            ->on('t1.testingRequestId = t2.id')
            ->where('t1.deleted')->ne(1)
            ->andwhere('t2.status')->eq($browseType)
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');
        return $data;
    }

    public function getProductEnrollList($browseType, $queryID, $orderBy, $pager = null)
    {
        $data = $this->dao->select('t1.*')->from(TABLE_OUTWARDDELIVERY)->alias('t1')
            ->leftJoin(TABLE_PRODUCTENROLL)->alias('t2')
            ->on('t1.productEnrollId = t2.id')
            ->where('t1.deleted')->ne(1)
            ->andwhere('t2.status')->eq($browseType)
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');
        return $data;
    }

    /**
     * 取本页id
     */
    private function getRangeIds($orderBy = 'id_desc', $pager)
    {
        $data = $this->dao->select('id, testingRequestId, productEnrollId, modifycnccId')->from(TABLE_OUTWARDDELIVERY)
            ->where('deleted')->ne(1)
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');
        $list = [];
        foreach ($data as $item) {
            $list['testingRequestId'][$item->testingRequestId] = $item->testingRequestId; //用key 去重
            $list['productEnrollId'][$item->productEnrollId] = $item->productEnrollId;
            $list['modifycnccId'][$item->modifycnccId] = $item->modifycnccId;
        }
        return $list;
    }

    /**
     * 获取单个详情
     * @param $id
     * @return object
     */
    public function getByID($id)
    {
        if (empty($id)) return null;
        $data = $this->dao->select('*')->from(TABLE_OUTWARDDELIVERY)
            ->where('id')->eq($id)
            ->andwhere('deleted')->eq(0)
            ->fetch();
        $data->ROR = json_decode($data->ROR, true);
        $this->loadModel('application');
        $app = $this->dao->select('id,isPayment,team')->from(TABLE_APPLICATION)->where('id')->eq(trim($data->app, ','))->fetch();
        $data->isPayment = $this->lang->application->isPaymentList[$app->isPayment];
        $data->team = $this->lang->application->teamList[$app->team];
        $cs = $this->dao->select('*')->from(TABLE_CONSUMED)->where('objectType')->eq('outwarddelivery')//状态流转 工作量
        ->andWhere('objectID')->eq($id)
            ->andWhere('deleted')->ne(1)
            ->orderBy('id_asc')
            ->fetchAll();
        $data->consumed = $cs;
        $this->resetNodeAndReviewerName($data->createdDept);
        return $data;
    }

    /**
     * 获取待处理人
     */
    public function getDealUserPairs($type)
    {

        if ($type == self::TESTINGREQUEST) { //关联测试申请
            $data = $this->dao->select('testingRequestId as id,dealUser')->from(TABLE_OUTWARDDELIVERY)
                ->where('deleted')->ne(1)
                ->andwhere('isNewTestingRequest')->eq(1)
                ->fetchPairs();
        } elseif ($type == self::PRODUCTENROLL) { //管理产品登记

            $data = $this->dao->select('productEnrollId as id,dealUser')->from(TABLE_OUTWARDDELIVERY)
                ->where('deleted')->ne(1)
                ->andwhere('isNewProductEnroll')->eq(1)
                ->fetchPairs();
        } elseif ($type == self::MODIFYCNCC) { //关联生产变更
            $data = $this->dao->select('modifycnccId as id,dealUser')->from(TABLE_OUTWARDDELIVERY)
                ->where('deleted')->ne(1)
                ->andwhere('isNewModifycncc')->eq(1)
                ->fetchPairs();
        } else { //其他
            return;
        }
        return $data;
    }

    /**
     * 获取单个详情-通过code
     * @param $id
     * @return null
     */
    public function getByCode($code)
    {
        if (empty($code)) return null;
        $data = $this->dao->select('*')->from(TABLE_OUTWARDDELIVERY)
            ->where('code')->eq($code)
            ->andwhere('deleted')->eq(0)
            ->fetch();
        return $data;
    }

    /**
     * 获取外部问题单id
     */
    public function getIssueIdByProblemId($problemId)
    {
        $issueId = $this->dao->select('IssueId')->from(TABLE_PROBLEM)
            ->where('id')->eq($problemId)
            ->fetch('IssueId');
        return $issueId;
    }

    /**
     * 关闭外部交付
     * @param $id
     * @return mixed
     */
    public function close($id)
    {
        //变更取消按钮开关(暂时取消执行原逻辑)
        //$changeFlag = isset($this->config->changeCloseSwitch) && $this->config->changeCloseSwitch == 1;
        $changeFlag = false;
        $syncFlag   = false;
        $comment    = trim($this->post->comment);
        if (!$comment) {
            dao::$errors['statusError'] = $this->lang->outwarddelivery->rejectCommentEmpty;
            return false;
        }

        //判断单子状态是否满足关闭状态
        //判断单子是否只有一个子单
        $outwarddelivery = $this->getByID($id);
        $childNum = 0;
        $lastChild = '';
        if($outwarddelivery->isNewTestingRequest == 1){
            $childNum += 1;
            $lastChild = 'testingRequest';
        }
        if($outwarddelivery->isNewProductEnroll == 1){
            $childNum += 1;
            $lastChild = 'productEnroll';
        }
        if($outwarddelivery->isNewModifycncc == 1){
            $childNum += 1;
            $lastChild = 'modifycncc';
        }
        //若只有一个子单
        if($childNum <= 1){
            //若子单已经外部审批，不能进行关闭
            if($lastChild == 'testingRequest'){
                $testingRequest = $this->loadModel('testingrequest')->getByID($outwarddelivery->testingRequestId);
                //变更取消开关为关执行原逻辑
                if(!$changeFlag && !empty($testingRequest->giteeId)){
                   return dao::$errors[''] = $this->lang->outwarddelivery->closeNotice;
                }
                //终态不能取消 [测试申请通过，已关闭，已取消]
                if($changeFlag && in_array($testingRequest->status, ['testingrequestpass', 'closed', 'cancel'])){
                    return dao::$errors[''] = $this->lang->outwarddelivery->statusEndNotice;
                }
                //外部状态不能取消 [待关联版本,待提交,内部未通过,已退回,待组长处理,待本部门审批,待系统部审批,待分管领导审批,待总经理审批,待产创部处理,待同步清总,测试申请不通过,同步清总失败]
                $insideStatus = ['wait','waitsubmitted','reviewfailed','reject','cmconfirmed','groupsuccess','managersuccess',
                    'posuccess','leadersuccess','gmsuccess','testingrequestreject','qingzongsynfailed','waitqingzong'];
                if($changeFlag && !in_array($testingRequest->status, $insideStatus)){
                    return dao::$errors[''] = $this->lang->outwarddelivery->closeNoticeNew;
                }
            }else if($lastChild == 'productEnroll'){
                $productenroll= $this->loadModel('productenroll')->getByID($outwarddelivery->productEnrollId);
                if(!$changeFlag && !empty($productenroll->giteeId)){
                    return dao::$errors[''] = $this->lang->outwarddelivery->closeNotice;
                }
                //终态不能取消
                if($changeFlag && in_array($productenroll->status, ['emispass', 'giteepass', 'closed', 'cancel'])){
                    return dao::$errors[''] = $this->lang->outwarddelivery->statusEndNotice;
                }
                //外部状态不能取消 [待关联版本,待提交,内部未通过,已退回,待组长处理,待本部门审批,待系统部审批,待分管领导审批,待总经理审批,待产创部处理,待同步清总,产品登记不通过,同步清总失败]
                $insideStatus = ['wait','waitsubmitted','reviewfailed','reject','cmconfirmed','groupsuccess','managersuccess',
                    'posuccess','leadersuccess','gmsuccess','productenrollreject','qingzongsynfailed','waitqingzong'];
                if($changeFlag && !in_array($productenroll->status, $insideStatus)){
                    return dao::$errors[''] = $this->lang->outwarddelivery->closeNoticeNew;
                }
            }else if($lastChild == 'modifycncc'){
                $modifycncc= $this->loadModel('modifycncc')->getByID($outwarddelivery->modifycnccId);
                if(!$changeFlag && !empty($modifycncc->giteeId)){
                    return dao::$errors[''] = $this->lang->outwarddelivery->closeNotice;
                }
                //终态不能取消 变更取消 变更成功 已关闭 部分成功 变更失败
                if($changeFlag && in_array($modifycncc->status, ['modifycancel', 'modifysuccess', 'closed','modifysuccesspart','modifyfail'])){
                    return dao::$errors[''] = $this->lang->outwarddelivery->statusEndNotice;
                }
                //外部状态不能取消 [待关联版本,待提交,内部未通过,已退回,待组长处理,待本部门审批,待系统部审批,待分管领导审批,待总经理审批,
                //待产创部处理,产品登记不通过,同步清总失败,待同步清总,待外部审批,gitee打回,变更退回]
                $insideStatus = ['wait','waitsubmitted','reviewfailed','reject','cmconfirmed','groupsuccess','managersuccess', 'posuccess','leadersuccess',
                    'gmsuccess','productenrollreject','qingzongsynfailed','waitqingzong','withexternalapproval','giteeback','modifyreject'];
                if($changeFlag && !in_array($modifycncc->status, $insideStatus)){
                    return dao::$errors[''] = $this->lang->outwarddelivery->closeNoticeNew;
                }
                //内部状态并且有giteeID同步清总
                $syncFlag = $changeFlag && !empty($modifycncc->giteeId);
            }
        }else{
            //若最后的子单进入外部审批，不能进行关闭
            if($lastChild == 'productEnroll'){
                $productenroll= $this->loadModel('productenroll')->getByID($outwarddelivery->productEnrollId);
                if(!$changeFlag && !empty($productenroll->giteeId)){
                    return dao::$errors[''] = $this->lang->outwarddelivery->closeNotice;
                }
                //终态不能取消
                if($changeFlag && in_array($productenroll->status, ['emispass', 'giteepass', 'closed', 'cancel'])){
                    return dao::$errors[''] = $this->lang->outwarddelivery->statusEndNotice;
                }
                //外部状态不能取消 [待关联版本,待提交,内部未通过,已退回,待组长处理,待本部门审批,待系统部审批,待分管领导审批,待总经理审批,待产创部处理,待同步清总,产品登记不通过,同步清总失败]
                $insideStatus = ['wait','waitsubmitted','reviewfailed','reject','cmconfirmed','groupsuccess','managersuccess',
                    'posuccess','leadersuccess','gmsuccess','productenrollreject','qingzongsynfailed','waitqingzong'];
                if($changeFlag && !in_array($productenroll->status, $insideStatus)){
                    return dao::$errors[''] = $this->lang->outwarddelivery->closeNoticeNew;
                }
            }else if($lastChild == 'modifycncc'){
                $modifycncc= $this->loadModel('modifycncc')->getByID($outwarddelivery->modifycnccId);
                if(!$changeFlag && !empty($modifycncc->giteeId)){
                    return dao::$errors[''] = $this->lang->outwarddelivery->closeNotice;
                }
                //终态不能取消
                if($changeFlag && in_array($modifycncc->status, ['modifycancel', 'modifysuccess', 'closed','modifysuccesspart','modifyfail'])){
                    return dao::$errors[''] = $this->lang->outwarddelivery->statusEndNotice;
                }
                //外部状态不能取消 [待关联版本,待提交,内部未通过,已退回,待组长处理,待本部门审批,待系统部审批,待分管领导审批,待总经理审批,
                //待产创部处理,产品登记不通过,同步清总失败,待同步清总,待外部审批,gitee打回,变更退回]
                $insideStatus = ['wait','waitsubmitted','reviewfailed','reject','cmconfirmed','groupsuccess','managersuccess', 'posuccess','leadersuccess',
                    'gmsuccess','productenrollreject','qingzongsynfailed','waitqingzong','withexternalapproval','giteeback','modifyreject'];
                if($changeFlag && !in_array($modifycncc->status, $insideStatus)){
                    return dao::$errors[''] = $this->lang->outwarddelivery->closeNoticeNew;
                }
                //内部状态并且有giteeID同步清总
                $syncFlag = $changeFlag && !empty($modifycncc->giteeId);
            }
        }

        //$data['closed'] = 1;
        $data['closedReason'] = $comment;
        $data['closedDate'] = helper::now();
        $data['closedBy'] = $this->app->user->account;
        $data['status'] = 'cancel';

        $outwarddelivery = $this->getByID($id);

        $res = $this->dao->update(TABLE_OUTWARDDELIVERY)
            ->data($data)
            ->where('id')->eq((int)$id)->exec();

        $this->dao->update(TABLE_OUTWARDDELIVERY)
            ->set('dealUser')->eq('')
            ->where('id')->eq((int)$id)->exec();
        $this->loadModel('consumed')->record('outwarddelivery', $id, 0, $this->app->user->account, $outwarddelivery->status, 'cancel', array());
        $this->loadModel('action')->create('outwarddelivery', $id, 'canceled', $this->post->comment);

        //修改审批结论
        $nodes = $this->dao->select('*')->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq('outwarddelivery')
            ->andWhere('objectID')->eq($id)
            ->andWhere('version')->eq($outwarddelivery->version)
            ->andWhere('status')->in(array('wait', 'pending'))
            ->orderBy('stage,id')
            ->fetchAll();
        $ns = array();
        foreach($nodes as $node) $ns[] = $node->id;
        if(!empty($ns)){
            $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('wait')
                ->where('id')->in($ns)
                ->andWhere('status')->in(array('wait', 'pending'))
                ->exec();
            $this->dao->update(TABLE_REVIEWER)->set('status')->eq('wait')
                ->where('node')->in($ns)
                ->andWhere('status')->in(array('wait', 'pending'))
                ->exec();
        }


        //判断单子状态是否满足关闭状态
        //判断单子是否只有一个子单
        $childNum = 0;
        $lastChild = '';
        if($outwarddelivery->isNewTestingRequest == 1){
            $childNum += 1;
            $lastChild = 'testingRequest';
        }
        if($outwarddelivery->isNewProductEnroll == 1){
            $childNum += 1;
            $lastChild = 'productEnroll';
        }
        if($outwarddelivery->isNewModifycncc == 1){
            $childNum += 1;
            $lastChild = 'modifycncc';
        }

        if ($outwarddelivery->isNewTestingRequest == 1) {
            $testingRequest = $this->loadModel('testingrequest')->getByID($outwarddelivery->testingRequestId);
            if(empty($testingRequest->giteeId) || in_array($testingRequest->status, array('waitsubmitted', 'wait', 'reviewfailed', 'reject', 'cmconfirmed', 'groupsuccess', 'managersuccess', 'posuccess', 'leadersuccess', 'gmsuccess'))){
                $this->dao->update(TABLE_TESTINGREQUEST)
                    ->data($data)
                    ->where('id')->eq((int)$outwarddelivery->testingRequestId)->exec();
                $this->loadModel('action')->create('testingrequest', $outwarddelivery->testingRequestId, 'canceled', $this->post->comment);
            }
        }
        if ($outwarddelivery->isNewProductEnroll == 1) {
            $productenroll= $this->loadModel('productenroll')->getByID($outwarddelivery->productEnrollId);
            if(empty($productenroll->giteeId) || in_array($testingRequest->status, array('waitsubmitted', 'wait', 'reviewfailed', 'reject', 'cmconfirmed', 'groupsuccess', 'managersuccess', 'posuccess', 'leadersuccess', 'gmsuccess'))){
                $this->dao->update(TABLE_PRODUCTENROLL)
                    ->data($data)
                    ->where('id')->eq((int)$outwarddelivery->productEnrollId)->exec();
                $this->loadModel('action')->create('productenroll', $outwarddelivery->productEnrollId, 'canceled', $this->post->comment);
            }
        }
        if ($outwarddelivery->isNewModifycncc == 1) {
            $data['status'] = 'modifycancel';
            $modifycncc= $this->loadModel('modifycncc')->getByID($outwarddelivery->modifycnccId);
            if(empty($modifycncc->giteeId)){
                $this->dao->update(TABLE_MODIFYCNCC)
                    ->data($data)
                    ->where('id')->eq((int)$outwarddelivery->modifycnccId)->exec();
                $this->loadModel('action')->create('modifycncc', $outwarddelivery->modifycnccId, 'canceled', $this->post->comment);
            }
        }
        $this->loadModel('demand')->changeBySecondLineV4($id,'outwarddelivery');
        //变更取消同步清总
        $r = $this->syncClosedStatus($outwarddelivery->modifycnccId, $syncFlag);
        return $res;
    }

    /**
     * 整理入库数组
     * @param int $update
     * @return array
     */
    private function fixOutwardDeliveryData($update = 0)
    {
        $postData = fixer::input('post')
//            ->stripTags($this->config->outsideplan->editor->create['id'], $this->config->allowedTags)
            ->join('problemId', ',')
            ->join('demandId', ',')
            ->join('requirementId', ',')
            ->join('secondorderId', ',')
            ->join('projectPlanId', ',')
            ->join('CBPprojectId', ',')
            ->join('productId', ',')
            ->join('release', ',')
            ->join('app', ',')
            ->join('leaveDefect', ',')
            ->join('fixDefect', ',')
            ->get();
        if ((empty($_POST['isNewTestingRequest']) || $_POST['isNewTestingRequest'][0] == 0) && (empty($_POST['isNewProductEnroll']) || $_POST['isNewProductEnroll'][0] == 0) && $_POST['isNewModifycncc'][0] == 1) {
            $value = 1;
            $postData->leaveDefect = '';
            $postData->fixDefect = '';
        } else {
            $value = 0;
        }

        if ($update == 0) {
            $outwarddelivery['createdBy'] = $this->app->user->account;
            $outwarddelivery['createdDept'] = $this->app->user->dept;
            $outwarddelivery['createdDate'] = helper::now();
            $outwarddelivery['code'] = $this->getCode();
            $outwarddelivery['reviewSubject'] = 'outwarddelivery'; //当前审核内容
            $outwarddelivery['ifMediumChanges'] = 1; //1= 没有介质变化
            $outwarddelivery['version'] = 1;
        }
        $outwarddelivery['outwardDeliveryDesc'] = $postData->outwardDeliveryDesc ?? "";
        $outwarddelivery['testingRequestId'] = isset($postData->testingRequestId) ? intval($postData->testingRequestId) : 0;
        $outwarddelivery['productEnrollId'] = isset($postData->productEnrollId) ? intval($postData->productEnrollId) : 0;
        $outwarddelivery['modifycnccId'] = isset($postData->modifycnccId) ? intval($postData->modifycnccId) : 0;
        $outwarddelivery['editedBy'] = $this->app->user->account;
        $outwarddelivery['editedDate'] = helper::now();
        //$outwarddelivery['problemId'] = $postData->problemId ?? '';
        if (!empty($postData->problemId)) {
            $problemIdArray = explode(',', str_replace(' ', '', $postData->problemId));
            $problemIds = ",";
            foreach ($problemIdArray as $item) {
                if (!empty($item)) {
                    $problemIds = $problemIds . $item . ",";
                }
            }
            $outwarddelivery['problemId'] = $problemIds;
        } else {
            $outwarddelivery['problemId'] = '';
        }

        if (!empty($postData->secondorderId)) {
            $secondorderIdArray = explode(',', str_replace(' ', '', $postData->secondorderId));
            $secondorderIds = ",";
            foreach ($secondorderIdArray as $item) {
                if (!empty($item)) {
                    $secondorderIds = $secondorderIds . $item . ",";
                }
            }
            $outwarddelivery['secondorderId'] = $secondorderIds;
        } else {
            $outwarddelivery['secondorderId'] = '';
        }

        //$outwarddelivery['demandId'] = $postData->demandId ?? '';
        if (!empty($postData->demandId)) {
            $demandIdArray = explode(',', str_replace(' ', '', $postData->demandId));
            $demandIds = ",";
            foreach ($demandIdArray as $item) {
                if (!empty($item)) {
                    $demandIds = $demandIds . $item . ",";
                }
            }
            $outwarddelivery['demandId'] = $demandIds;
        } else {
            $outwarddelivery['demandId'] = '';
        }
        //$outwarddelivery['requirementId'] = $postData->requirementId ?? '';
        if (!empty($postData->requirementId)) {
            $requirementIdArray = explode(',', str_replace(' ', '', $postData->requirementId));
            $requirementIds = ",";
            foreach ($requirementIdArray as $item) {
                if (!empty($item)) {
                    $requirementIds = $requirementIds . $item . ",";
                }
            }
            $outwarddelivery['requirementId'] = $requirementIds;
        } else {
            $outwarddelivery['requirementId'] = '';
        }
        //$outwarddelivery['app'] = $postData->app ?? '';
        if (!empty($postData->app)) {
            $appArray = explode(',', str_replace(' ', '', $postData->app));
            $apps = ",";
            foreach ($appArray as $item) {
                if (!empty($item)) {
                    $apps = $apps . $item . ",";
                }
            }
            $outwarddelivery['app'] = $apps;
        } else {
            $outwarddelivery['app'] = '';
        }
        /*$outwarddelivery['productLine'] = $postData->productLine ?? '';*/

        $outwarddelivery['contactName'] = $this->app->user->realname ?? '';
        $outwarddelivery['contactTel'] = $postData->applyUsercontact ?? '';
        $outwarddelivery['contactEmail'] = $postData->contactEmail ?? '';

        $outwarddelivery['implementationForm'] = $postData->implementationForm ?? '';
        $outwarddelivery['projectPlanId'] = $postData->projectPlanId ?? '';
        $outwarddelivery['CBPprojectId'] = $postData->CBPprojectId ?? '';
        $outwarddelivery['release'] = $postData->release ?? '';
        //if($value ==0){
        if (!empty($postData->productId)) {
            $productIdArray = explode(',', str_replace(' ', '', $postData->productId));
            $productIds = ",";
            $productLineArray = array();
            $appArray = array();
            foreach ($productIdArray as $item) {
                if (!empty($item)) {
                    $productIds = $productIds . $item . ",";
                    $product = $this->dao->select('line,app')->from(TABLE_PRODUCT)
                        ->where('id')->eq($item)
                        ->fetch();
                    if (!in_array($product->line, $productLineArray)) {
                        array_push($productLineArray, $product->line);
                    }
                    if (!in_array($product->app, $appArray)) {
                        array_push($appArray, $product->app);
                    }
                }
            }
            $outwarddelivery['productId'] = $productIds;
            $outwarddelivery['productLine'] = implode(',', $productLineArray);
            //$outwarddelivery['app'] = implode(',',$appArray);
        } else {
            $outwarddelivery['productId'] = '';
            $outwarddelivery['productLine'] = '';
            //$outwarddelivery['app'] = '';
        }
        //$outwarddelivery['productInfoCode'] = $postData->productInfoCode ?? '';
        //}
        $outwarddelivery['reviewStage'] = $postData->reviewStage ?? 0;
        $outwarddelivery['status'] = $postData->status ?? "waitsubmitted";
        $outwarddelivery['level'] = $postData->level ?? 0;
        $outwarddelivery['ROR'] = $postData->ROR ?? '';
        $outwarddelivery['dealUser'] = $this->app->user->account;
        $outwarddelivery['currentReview'] = '1';
        $outwarddelivery['fixDefect'] = $postData->fixDefect ?? '';
        $outwarddelivery['leaveDefect'] = $postData->leaveDefect ?? '';
        $outwarddelivery['manufacturer'] = $postData->manufacturer;
        $outwarddelivery['manufacturerConnect'] = $postData->manufacturerConnect;
        $outwarddelivery['issubmit'] = $postData->issubmit;

        if ($postData->issubmit != 'save'){
            if ($update == 0) {
                $this->checkParams($outwarddelivery, $this->config->outwarddelivery->create->requiredFields, $value);
            } else {
                $this->checkParams($outwarddelivery, $this->config->outwarddelivery->edit->requiredFields, $value);
            }
        }
        return $outwarddelivery;
    }

    /**
     * 检查必填项
     * @param $data
     */
    private function checkParams($data, $fields, $value)
    {
        if ($value == 0) {
            $fieldArray = explode(',', str_replace(' ', '', $fields));
            foreach ($fieldArray as $item) {
                if (is_null($data[$item]) || $data[$item] == '') {
                    $itemName = $this->lang->outwarddelivery->$item ?? $item;
                    dao::$errors[$item] = sprintf($this->lang->outwarddelivery->emptyObject, $itemName);
                }
            }
            if (is_null($data['productId']) || $data['productId'] == '' || $data['productId'] == ',') {
                dao::$errors[] = sprintf($this->lang->outwarddelivery->emptyObject, $this->lang->outwarddelivery->productName);
            }
        }
        /*if($data['productInfoCode'] != '无'){
            $productIdArray = explode(",",trim($data['productId'],","));
            $productCodeArray = explode(",",$data['productInfoCode']);
            if(count($productIdArray) != count($productCodeArray)){
                dao::$errors[] =  '产品名称和产品编号数量不匹配';
            }
            //版本号正则
            $versionReg = '/^V\d+(.\d+){3}$/';
            foreach ($productCodeArray as $productCode){
                $isVersion = false;
                $isFor = false;
                $isForEmpty = false;
                $codeArray = explode("-", $productCode);
                for($i=0;$i<count($codeArray); $i++){
                    $code = $codeArray[$i];
                    if(preg_match($versionReg, $code)){
                        $isVersion = true;
                    }
                    if($code == 'for' || $code == 'For' || $code == 'FOR'){
                        $isFor = true;
                        if($i+1 < count($codeArray) && !empty($codeArray[$i+1])){
                            $isForEmpty = true;
                        }
                    }
                }

                if(!$isVersion || !$isFor || !$isForEmpty){
                    dao::$errors['productInfoCode'] =  '填写“无”，或请补充完善“'.$productCode.'”产品编号，例如：RE-GCCRS-PBC-SERVER-V2.1.0.3-for-CentOS6';
                    break;
                }
            }
        }*/
    }

    /**
     * 介质下载信息数组
     * @param $fileName
     * @param $filePath
     * @return array
     */
    public function getRelationFileLinkArray($fileName, $filePath, $md5)
    {
        return ['url' => $filePath, 'md5' => $md5, 'fileName' => $fileName];
    }

    /**
     * 下载文件签名
     * @param $filename
     * @return int
     */
    public function getSign($filename)
    {
        return $this->loadModel('downloads')->getSign($filename);
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
     * 添加关联关系
     */
    private function addSecondLineRelation($outwardDeliveryId, $relationId, $type)
    {
        if (empty($relationId)) {
            return;
        }
        if ($type == self::TESTINGREQUEST) { //关联测试申请
            $info = $this->dao->select('id,code')->from(TABLE_OUTWARDDELIVERY)
                ->where('deleted')->ne(1)
                ->andwhere('testingRequestId')->eq($relationId)
                ->andwhere('isNewTestingRequest')->eq(1)
                ->fetch();

        } elseif ($type == self::PRODUCTENROLL) { //管理产品登记
            $info = $this->dao->select('id,code')->from(TABLE_OUTWARDDELIVERY)
                ->where('deleted')->ne(1)
                ->andwhere('productEnrollId')->eq($relationId)
                ->andwhere('isNewProductEnroll')->eq(1)
                ->fetch();
        } elseif ($type == self::MODIFYCNCC) { //关联生产变更
            $info = $this->dao->select('id,code')->from(TABLE_OUTWARDDELIVERY)
                ->where('deleted')->ne(1)
                ->andwhere('modifycncctId')->eq($relationId)
                ->andwhere('isNewModifycncc')->eq(1)
                ->fetch();
        } else { //其他
            return;
        }

        //关联子类型
        $data = new stdClass();
        $data->objectID = $outwardDeliveryId;
        $data->objectType = self::OUTWARDDELIVERY;
        $data->relationID = $relationId;
        $data->relationType = $type;
        $data->createdBy = $this->app->user->account;
        $data->createdDate = helper::now();
        $data->relationship = json_encode($info);
        $exts = $this->dao->select('id')->from(TABLE_SECONDLINE)
            ->where('deleted')->ne(1)
            ->andwhere('objectType')->eq($data->objectType)
            ->andwhere('relationType')->eq($data->relationType)
            ->andwhere('relationID')->eq($data->relationID)
            ->andwhere('objectID')->eq($data->objectID)
            ->fetch('id');
        if (empty($exts)) {
            $this->dao->insert(TABLE_SECONDLINE)->data($data)->exec();
        }

        //反相插一条
        $data->objectID = $relationId;
        $data->objectType = $type;
        $data->relationID = $outwardDeliveryId;
        $data->relationType = self::OUTWARDDELIVERY;
        $exts = $this->dao->select('id')->from(TABLE_SECONDLINE)
            ->where('deleted')->ne(1)
            ->andwhere('objectType')->eq($data->objectType)
            ->andwhere('relationType')->eq($data->relationType)
            ->andwhere('relationID')->eq($data->relationID)
            ->andwhere('objectID')->eq($data->objectID)
            ->fetch('id');
        if (empty($exts)) {
            $this->dao->insert(TABLE_SECONDLINE)->data($data)->exec();
        }

        //关联对外交付
        $data->objectID = $info->id;
        $data->objectType = self::OUTWARDDELIVERY;
        $data->relationID = $outwardDeliveryId;
        $data->relationType = self::OUTWARDDELIVERY;
        $exts = $this->dao->select('id')->from(TABLE_SECONDLINE)
            ->where('deleted')->ne(1)
            ->andwhere('objectType')->eq($data->objectType)
            ->andwhere('relationType')->eq($data->relationType)
            ->andwhere('relationID')->eq($data->relationID)
            ->andwhere('objectID')->eq($data->objectID)
            ->fetch('id');
        if (empty($exts)) {
            $this->dao->insert(TABLE_SECONDLINE)->data($data)->exec();
        }

        //反相插一条
        $data->objectID = $outwardDeliveryId;
        $data->objectType = self::OUTWARDDELIVERY;
        $data->relationID = $info->id;
        $data->relationType = self::OUTWARDDELIVERY;
        $exts = $this->dao->select('id')->from(TABLE_SECONDLINE)
            ->where('deleted')->ne(1)
            ->andwhere('objectType')->eq($data->objectType)
            ->andwhere('relationType')->eq($data->relationType)
            ->andwhere('relationID')->eq($data->relationID)
            ->andwhere('objectID')->eq($data->objectID)
            ->fetch('id');
        if (empty($exts)) {
            $this->dao->insert(TABLE_SECONDLINE)->data($data)->exec();
        }
    }

    /**
     * 添加二线工单关联
     * @param $outwardDeliveryId
     * @param $problemIds
     */
    public function addSecondLinesecondorder($outwardDeliveryId, $secondorderIds)
    {
        if (empty($secondorderIds)) {
            return;
        }
        $secondorders = explode(',', $secondorderIds);

        foreach ($secondorders as $secondorderId) {
            if (empty($secondorderId)) {
                continue;
            }
            $data = new stdClass();
            $data->objectID = $outwardDeliveryId;
            $data->objectType = self::OUTWARDDELIVERY;
            $data->relationID = $secondorderId;
            $data->relationType = 'secondorder';
            $data->createdBy = $this->app->user->account;
            $data->createdDate = helper::now();
            $this->dao->insert(TABLE_SECONDLINE)->data($data)->exec();

            //反相插一条
            $data->objectID = $secondorderId;
            $data->objectType = 'secondorder';
            $data->relationID = $outwardDeliveryId;
            $data->relationType = self::OUTWARDDELIVERY;
            $this->dao->insert(TABLE_SECONDLINE)->data($data)->exec();
        }
    }

    /**
     * 添加问题关联
     * @param $outwardDeliveryId
     * @param $problemIds
     */
    public function addSecondLineProblem($outwardDeliveryId, $problemIds)
    {
        if (empty($problemIds)) {
            return;
        }
        $problems = explode(',', $problemIds);

        foreach ($problems as $problemId) {
            if (empty($problemId)) {
                continue;
            }
            $data = new stdClass();
            $data->objectID = $outwardDeliveryId;
            $data->objectType = self::OUTWARDDELIVERY;
            $data->relationID = $problemId;
            $data->relationType = 'problem';
            $data->createdBy = $this->app->user->account;
            $data->createdDate = helper::now();
            $this->dao->insert(TABLE_SECONDLINE)->data($data)->exec();

            //反相插一条
            $data->objectID = $problemId;
            $data->objectType = 'problem';
            $data->relationID = $outwardDeliveryId;
            $data->relationType = self::OUTWARDDELIVERY;
            $this->dao->insert(TABLE_SECONDLINE)->data($data)->exec();
        }
    }

    /**
     * 添加问题关联
     * @param $outwardDeliveryId
     * @param $problemIds
     */
    public function addSecondLineDemand($outwardDeliveryId, $demandIds)
    {
        if (empty($demandIds)) {
            return;
        }
        $demands = explode(',', $demandIds);

        foreach ($demands as $demandId) {
            if (empty($demandId)) {
                continue;
            }
            $data = new stdClass();
            $data->objectID = $outwardDeliveryId;
            $data->objectType = self::OUTWARDDELIVERY;
            $data->relationID = $demandId;
            $data->relationType = 'demand';
            $data->createdBy = $this->app->user->account;
            $data->createdDate = helper::now();
            $this->dao->insert(TABLE_SECONDLINE)->data($data)->exec();

            //反相插一条
            $data->objectID = $demandId;
            $data->objectType = 'demand';
            $data->relationID = $outwardDeliveryId;
            $data->relationType = self::OUTWARDDELIVERY;
            $this->dao->insert(TABLE_SECONDLINE)->data($data)->exec();
        }
    }

    /**
     * 添加问题关联
     * @param $outwardDeliveryId
     * @param $problemIds
     */
    public function addSecondLineRequirement($outwardDeliveryId, $requirementIds)
    {
        if (empty($requirementIds)) {
            return;
        }
        $requirements = explode(',', $requirementIds);

        foreach ($requirements as $requirementId) {
            if (empty($requirementId)) {
                continue;
            }
            $data = new stdClass();
            $data->objectID = $outwardDeliveryId;
            $data->objectType = self::OUTWARDDELIVERY;
            $data->relationID = $requirementId;
            $data->relationType = 'requirement';
            $data->createdBy = $this->app->user->account;
            $data->createdDate = helper::now();
            $this->dao->insert(TABLE_SECONDLINE)->data($data)->exec();

            //反相插一条
            $data->objectID = $requirementId;
            $data->objectType = 'requirement';
            $data->relationID = $outwardDeliveryId;
            $data->relationType = self::OUTWARDDELIVERY;
            $this->dao->insert(TABLE_SECONDLINE)->data($data)->exec();
        }
    }

    /**
     * 添加问题关联
     * @param $outwardDeliveryId
     * @param $problemIds
     */
    public function addSecondLineProject($outwardDeliveryId, $projectIds)
    {
        if (empty($projectIds)) {
            return;
        }
        $projects = explode(',', $projectIds);

        foreach ($projects as $projectId) {
            if (empty($projectId)) {
                continue;
            }
            $data = new stdClass();
            $data->objectID = $outwardDeliveryId;
            $data->objectType = self::OUTWARDDELIVERY;
            $data->relationID = $projectId;
            $data->relationType = 'project';
            $data->createdBy = $this->app->user->account;
            $data->createdDate = helper::now();
            $this->dao->insert(TABLE_SECONDLINE)->data($data)->exec();

            //反相插一条
            $data->objectID = $projectId;
            $data->objectType = 'project';
            $data->relationID = $outwardDeliveryId;
            $data->relationType = self::OUTWARDDELIVERY;
            $this->dao->insert(TABLE_SECONDLINE)->data($data)->exec();
        }
    }

    /**
     * 获取关联关系
     * @param $id
     * @return array
     */
    public function getRelations($id, $type)
    {
        $info = $this->dao->select('*')->from(TABLE_SECONDLINE)
            ->where('objectType')->eq($type)
            ->andwhere('objectID')->eq($id)
            ->andwhere('deleted')->ne(1)
            ->fetchall();
        $relation = [];
        foreach ($info as &$item) {
            $item->relationship = json_decode($item->relationship, 1);

            $relation[] = (array)$item;
        }
        return $relation;
    }


    public function getAllRelations($id)
    {
        $info = $this->dao->select('*')->from(TABLE_SECONDLINE)
            ->where('objectType')->eq(self::OUTWARDDELIVERY)
            ->andwhere('objectID')->eq($id)
            ->andwhere('deleted')->ne(1)
            ->fetchall();
        $relation = [];
        $parent = [];
        foreach ($info as &$item) {
            $item->relationship = json_decode($item->relationship, 1);
            if ($item->relationship['id']) {
                $parent[$item->relationship['id']] = $item->relationship['id'];
            }
            if ($item->relationType == self::OUTWARDDELIVERY && $item->objectType == $item->relationType) {
                $parent[$item->relationID] = $item->relationID;
                continue;
            }
            $relation[$item->relationType][] = (array)$item;
        }

        unset($parent[$id]); //去掉自己
        foreach ($parent as $oid) {
            $item->children = (array)current($this->getDetailPairs($oid));
            $item->code = $item->children['code'];
            $item->relationID = $oid;
            $relation[self::OUTWARDDELIVERY][] = (array)$item;
        }
        return $relation;
    }

    /**
     * 获取详细信息
     * @param $outwardDeliveryId
     * @return array
     */
    public function getAllInfo($outwardDeliveryId, $type = self::OUTWARDDELIVERY)
    {
        $info['outwardDelivery'] = (array)($this->getByID($outwardDeliveryId));
        $info['testingRequest'] = $this->loadModel('testingrequest')->getByID($info['outwardDelivery']['testingRequestId']);
        $info['productEnroll'] = $this->loadModel('productenroll')->getByID($info['outwardDelivery']['productEnrollId']);
        $info['modifycncc'] = $this->loadModel('modifycncc')->getByID($info['outwardDelivery']['modifycnccId']);
        $info['secondorder'] = $this->loadModel('secondorder')->getPairsByIds(explode(',', $info['outwardDelivery']['secondorderId']));
        $info['demand'] = $this->loadModel('demand')->getPairsByIds(explode(',', $info['outwardDelivery']['demandId']));
        $info['problem'] = $this->loadModel('problem')->getPairsByIds(explode(',', $info['outwardDelivery']['problemId']));
        $info['requirement'] = $this->loadModel('requirement')->getPairsByIds(explode(',', $info['outwardDelivery']['requirementId']));
        $info['reviewReportList'] = $this->loadModel('review')->getPairs($info['outwardDelivery']['projectPlanId'], '');
        $info['relations'] = $this->getRelations($outwardDeliveryId, $type);
        return $info;
    }

    /**
     * Project: chengfangjinke
     * Method: review
     * User: tangfei
     * Year: 2022
     * Date: 2022/6/21
     * Time: 14:45
     * Desc: This is the code comment. This method is called review.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $outwarddeliveryID
     * @return false|void
     */
    public function review($outwarddeliveryID)
    {
        $info = []; //返回信息
        $outwarddelivery = $this->getByID($outwarddeliveryID);
        //判断当前状态和审核中传入的状态是否一致，以防有同组中其他人修改
        $res = $this->checkAllowReview($outwarddelivery, $this->post->version, $this->post->reviewStage, $this->app->user->account);
        if (!$res['result']) {
            dao::$errors['statusError'] = $res['message'];
            return false;
        }
       /* if (!$this->post->consumed) {
            dao::$errors['consumed'] = $this->lang->outwarddelivery->consumedEmpty;
            return false;
        }
        $reg = '/^(([1-9][0-9]*)|(([0]\.\d{1}|[1-9][0-9]*\.\d{1})))$/';
        if (!preg_match($reg, $this->post->consumed)) {
            dao::$errors['consumed'] = $this->lang->outwarddelivery->consumedError;
            return false;
        }*/
        $extra = new stdClass();
        if ($outwarddelivery->reviewStage == 2 and $outwarddelivery->isNewModifycncc == 1 and ((in_array(3, explode(',', $outwarddelivery->requiredReviewNode)) == 1 and $outwarddelivery->isOutsideReject == '1') or $outwarddelivery->isOutsideReject == '0')) {
            if (!$this->post->isNeedSystem) {
                dao::$errors['isNeedSystem'] = $this->lang->outwarddelivery->systemEmpty;
                return false;
            }

            $extra = $this->post->isNeedSystem == 'yes' ? true : false;
        }

        $is_all_check_pass = false;
        //reviewfailed为内部未通过，但是reviewnoe表里需要存reject
        $postResult = $this->post->result;
        if ($postResult == 'reviewfailed'){
            $postResult = 'reject';
        }
        //$result = $this->loadModel('review')->check('outwarddelivery', $outwarddeliveryID, $outwarddelivery->version, $postResult, $this->post->comment, $outwarddelivery->reviewStage, $extra, $is_all_check_pass);
        //$result = $this->loadModel('review')->check('outwarddelivery', $outwarddeliveryID, $outwarddelivery->version, $this->post->result, $this->post->comment, $outwarddelivery->reviewStage, $extra, $is_all_check_pass);
        //授权管咯
        $result = $this->loadModel('common')->check('outwarddelivery', $outwarddeliveryID, $outwarddelivery->version, $postResult, $this->post->comment, 0, null, $is_all_check_pass, $res['reviewsOriginal'], $res['reviews'], $res['reviewAuthorize']);
        //生产变更单状态需要存reviewfailed
        if ($result == 'reject'){
            $result = 'reviewfailed';
        }
        if ($result == 'pass') {
            //解决时间取二线专员审核通过节点的前一个节点的处理节点时间 选择生产变更单才更新
//            if(!empty($outwarddelivery->modifycnccId) && $outwarddelivery->reviewStage == 7){
//                /** @var infoModel $infoModel*/
//                $infoModel =  $this->loadModel('info');
//                $infoModel->dealDemandAndProblemSolvedTime($outwarddelivery,'outwardDelivery',$outwarddeliveryID,$outwarddelivery->version,$outwarddelivery->demandId,$outwarddelivery->problemId);
//            }

            //新代码
            //确定审批经过哪些节点
            if ($outwarddelivery->isOutsideReject) {   //如果是外部退回，且编辑过程中没有修改变更级别，则采用退回页面选择的审批节点
                $requiredStage = explode(',', $outwarddelivery->requiredReviewNode);
            } else {                                    //如果不是退回，或者退回后编辑了“变更级别”字段
                if ($outwarddelivery->isNewModifycncc == 1) {
                    $requiredStage = $this->lang->outwarddelivery->requiredReviewerList[$outwarddelivery->level];  //不同表单类型的审批节点不同
                } elseif ($outwarddelivery->isNewProductEnroll == 1) {
                    $requiredStage = $this->lang->outwarddelivery->requiredReviewerList[5];
                } elseif ($outwarddelivery->isNewTestingRequest == 1) {
                    $requiredStage = $this->lang->outwarddelivery->requiredReviewerList[4];
                }
            }

            //每次审批通过，至少前进一步
            $afterStage = $outwarddelivery->reviewStage + 1;
            $approveNode = explode(',', $outwarddelivery->approvedNode);
            array_push($approveNode, $afterStage - 1);
            while ($afterStage < self::MAXNODE) {
                if ($afterStage == self::SYSTEMNODE and $this->post->isNeedSystem == 'no') {
                    $afterStage += 1;
                    array_push($approveNode, $afterStage - 1);
                }  //如果跳过系统部审批，则再前进一步
                if (!in_array($afterStage, $requiredStage)) {  //如果跳过后的节点仍然跳过，继续前进
                    $afterStage += 1;
                    array_push($approveNode, $afterStage - 1);
                } else {  //如果节点不用继续跳过，则跳出循环
                    break;
                }
            }

            if ($afterStage - $outwarddelivery->reviewStage > 1) {
                // 审核人员置为忽略/跳过
                $reviewList = $this->dao->select('id,stage')->from(TABLE_REVIEWNODE)
                    ->where('objectType')->eq('outwarddelivery')   //将跳过的节点，更新为ignore
                    ->andWhere('objectID')->eq($outwarddelivery->id)
                    ->andWhere('version')->eq($outwarddelivery->version)
                    ->andWhere('status')->eq('wait')->orderBy('stage,id')
                    ->limit($afterStage - $outwarddelivery->reviewStage - 1)
                    ->fetchall();
                $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('ignore')->where('objectType')->eq('outwarddelivery')//将跳过的节点，更新为ignore
                ->andWhere('objectID')->eq($outwarddelivery->id)
                    ->andWhere('version')->eq($outwarddelivery->version)
                    ->andWhere('status')->eq('wait')->orderBy('stage,id')->limit($afterStage - $outwarddelivery->reviewStage - 1)->exec();
                $outwarddelivery->release = explode(',', trim($outwarddelivery->release,','));
                $checkSystemRes = $this->loadModel('build')->checkSystemPass($outwarddelivery->release);
                foreach ($reviewList as $k=>$v){
                    $updateData = new stdClass();
                    $updateData->status = 'ignore';
                    $updateData->comment = '';

//                    if ($outwarddelivery->reviewStage + 1 == self::SYSTEMNODE and $this->post->isNeedSystem == 'no' and $checkSystemRes){
//                        $updateData->comment = '已在制版菜单完成审批';
//                    }
                    $this->dao->update(TABLE_REVIEWER)->data($updateData)->where('node')->in($v->id)->exec();
                }
            }


            //更新状态
            if (isset($this->lang->outwarddelivery->reviewBeforeStatusList[$afterStage])) {
                $status = $this->lang->outwarddelivery->reviewBeforeStatusList[$afterStage];
            }

            $lastDealDate = date('Y-m-d');

            $this->dao->update(TABLE_OUTWARDDELIVERY)->set('approvedNode')->eq(trim(implode(',', $approveNode), ','))->set('reviewStage')->eq($afterStage)->set('status')->eq($status)->set('lastDealDate')->eq($lastDealDate)->where('id')->eq($outwarddeliveryID)->exec();
            //$this->loadModel('consumed')->remove('outwarddelivery', $outwarddeliveryID, $this->app->user->account, $status); //逻辑删除原有状态 只保留最新的
            $this->loadModel('consumed')->record('outwarddelivery', $outwarddeliveryID, '0', $this->app->user->account, $outwarddelivery->status, $status, array());

            //如果状态为”待外部审批“,更新当前审批字段
            if ($status == 'withexternalapproval') {
                //修改待处理人为清总
                $this->dao->update(TABLE_OUTWARDDELIVERY)->set('dealUser')->eq('guestcn')->where('id')->eq($outwarddeliveryID)->exec();

                //是否有测试申请单
                if ($outwarddelivery->testingRequestId != 0) {
                    $status2 = $this->dao->select('status')->from(TABLE_TESTINGREQUEST)->where('id')->eq($outwarddelivery->testingRequestId)->fetch()->status;
                    if ($status2 == 'testingrequestpass') {
                        //是否有产品登记单
                        if ($outwarddelivery->productEnrollId != 0) {
                            $status2 = $this->dao->select('status')->from(TABLE_PRODUCTENROLL)->where('id')->eq($outwarddelivery->productEnrollId)->fetch()->status;
                            if ($status2 == 'emispass' or $status2 == 'giteepass') {
                                //只剩下生产变更
                                $this->dao->update(TABLE_OUTWARDDELIVERY)->set('currentReview="4"')->where('id')->eq($outwarddeliveryID)->exec();
                            } else {
                                //产品登记单未处于外部审批通过
                                $this->dao->update(TABLE_OUTWARDDELIVERY)->set('currentReview="3"')->where('id')->eq($outwarddeliveryID)->exec();
                            }
                        } else {
                            //只剩下生产变更
                            $this->dao->update(TABLE_OUTWARDDELIVERY)->set('currentReview="4"')->where('id')->eq($outwarddeliveryID)->exec();
                        }
                    } else {
                        //测试申请未处于外部审批通过
                        $this->dao->update(TABLE_OUTWARDDELIVERY)->set('currentReview="2"')->where('id')->eq($outwarddeliveryID)->exec();
                    }

                } else {
                    //没有测试申请，是否有产品登记单
                    if ($outwarddelivery->productEnrollId != 0) {
                        $status2 = $this->dao->select('status')->from(TABLE_PRODUCTENROLL)->where('id')->eq($outwarddelivery->productEnrollId)->fetch()->status;
                        if ($status2 == 'emispass' or $status2 == 'giteepass') {
                            //只剩下生产变更
                            $this->dao->update(TABLE_OUTWARDDELIVERY)->set('currentReview="4"')->where('id')->eq($outwarddeliveryID)->exec();
                        } else {
                            //产品登记单未处于外部审批通过
                            $this->dao->update(TABLE_OUTWARDDELIVERY)->set('currentReview="3"')->where('id')->eq($outwarddeliveryID)->exec();
                        }
                    } else {
                        //没有产品登记，只剩下生产变更
                        $this->dao->update(TABLE_OUTWARDDELIVERY)->set('currentReview="4"')->where('id')->eq($outwarddeliveryID)->exec();
                    }
                }
            }

            //更新子表单的状态
            if ($status == 'withexternalapproval') {
                $status = 'waitqingzong';
                //审批通过后推送介质
                $info['mediaPush'] = 1;
            }
            if ($outwarddelivery->isNewModifycncc == 1) {
                $this->dao->update(TABLE_MODIFYCNCC)->set('status')->eq($status)->set('lastDealDate')->eq($lastDealDate)->where('id')->eq($outwarddelivery->modifycnccId)->exec();
                $this->loadModel('action')->create('modifycncc', $outwarddelivery->modifycnccId, 'review', $this->post->comment);
                //若被外部退回，重新走流程需要通知清总
                $modifycncc = $this->loadModel('modifycncc')->getByID($outwarddelivery->modifycnccId);
                if(!empty($modifycncc->giteeId) and in_array($status, array('wait','cmconfirmed','groupsuccess','gmsuccess'))){
                    $this->pushModifycnccState($modifycncc->code, zget($this->lang->outwarddelivery->statusList, $status));
                }
            }
            if ($outwarddelivery->isNewProductEnroll == 1 and in_array($outwarddelivery->reviewStage, explode(',', '0,1,2,7')) == 1) {
                if ($outwarddelivery->reviewStage == 2) $status = 'gmsuccess';
                $productenroll = $this->dao->select('status')->from(TABLE_PRODUCTENROLL)->where('id')->eq($outwarddelivery->productEnrollId)->fetch();
                if ($productenroll->status != 'giteepass' and $productenroll->status != 'emispass') {
                    $this->dao->update(TABLE_PRODUCTENROLL)->set('status')->eq($status)->set('lastDealDate')->eq($lastDealDate)->where('id')->eq($outwarddelivery->productEnrollId)->exec();
                    $action = 'cmconfirmed' == $testingrequest->status || 'gmsuccess' == $testingrequest->status ? 'deal' : 'review';
                    $this->loadModel('action')->create('productenroll', $outwarddelivery->productEnrollId, $action, $this->post->comment);
                }
            }
            if ($outwarddelivery->isNewTestingRequest == 1 and in_array($outwarddelivery->reviewStage, explode(',', '0,1,2,7')) == 1) {
                if ($outwarddelivery->reviewStage == 2) $status = 'gmsuccess';
                $testingrequest = $this->dao->select('status')->from(TABLE_TESTINGREQUEST)->where('id')->eq($outwarddelivery->testingRequestId)->fetch();
                if ($testingrequest->status != 'testingrequestpass') {
                    $this->dao->update(TABLE_TESTINGREQUEST)->set('status')->eq($status)->set('lastDealDate')->eq($lastDealDate)->where('id')->eq($outwarddelivery->testingRequestId)->exec();
                    $action = 'cmconfirmed' == $testingrequest->status || 'gmsuccess' == $testingrequest->status ? 'deal' : 'review';
                    $this->loadModel('action')->create('testingrequest', $outwarddelivery->testingRequestId, $action, $this->post->comment);
                }
            }

            //把跳过的节点改成忽略
            //这里写的有问题 但我不知道逻辑是怎么回事 -by tongyanqi
            if (isset($add2) && $add2 != 0) {
                $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('ignore')->where('objectType')->eq('outwarddelivery')
                    ->andWhere('objectID')->eq($outwarddeliveryID)
                    ->andWhere('version')->eq($outwarddelivery->version)
                    ->andWhere('status')->eq('wait')->orderBy('stage,id')->limit($add2)->exec();
                $this->dao->update(TABLE_OUTWARDDELIVERY)->set('reviewStage = reviewStage+' . $add2)->where('id')->eq($outwarddeliveryID)->exec();
            }


            $next = $this->dao->select('id')->from(TABLE_REVIEWNODE)->where('objectType')->eq('outwarddelivery')
                ->andWhere('objectID')->eq($outwarddeliveryID)
                ->andWhere('version')->eq($outwarddelivery->version)
                ->andWhere('status')->eq('wait')->orderBy('stage,id')->fetch('id');

            if ($next) {
                $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('pending')->where('id')->eq($next)->exec();
                $this->dao->update(TABLE_REVIEWER)->set('status')->eq('pending')->where('node')->eq($next)->exec();
                $this->loadModel('review');
                $reviewers = $this->review->getReviewer('outwardDelivery', $outwarddelivery->id, $outwarddelivery->version, $outwarddelivery->reviewStage);
                $this->dao->update(TABLE_OUTWARDDELIVERY)->set('dealUser')->eq($reviewers)->where('id')->eq($outwarddeliveryID)->exec();
            }
        } elseif ($result == 'reviewfailed') {
            //内部审核通过值由reject(已退回)改为reviewfailed（审核未通过）
            //如果单子被外部退回过，状态更新为已退回（迭代33）
            $rejectConsumed = $this->dao->select('id')
                ->from(TABLE_CONSUMED)
                ->where('objectType')->eq('outwarddelivery')
                ->andWhere('`objectID`')->eq($outwarddeliveryID)
                ->andWhere('`before`')->eq('reject')
                ->andWhere('deleted')->eq('0')->fetch();
            $newStatus = !empty($rejectConsumed) ? 'reject' : 'reviewfailed';
            $lastDealDate = date('Y-m-d');
            $this->dao->update(TABLE_OUTWARDDELIVERY)->set('isOutsideReject')->eq('0')->set('status')->eq($newStatus)->set('lastDealDate')->eq($lastDealDate)->set('dealUser')->eq($outwarddelivery->createdBy)->where('id')->eq($outwarddeliveryID)->exec();
            $this->loadModel('consumed')->record('outwarddelivery', $outwarddeliveryID, '0', $this->app->user->account, $outwarddelivery->status, $newStatus, array());

            //更新子表单的状态
            if ($outwarddelivery->isNewModifycncc == 1) {
                $this->dao->update(TABLE_MODIFYCNCC)->set('status')->eq($newStatus)->set('lastDealDate')->eq($lastDealDate)->where('id')->eq($outwarddelivery->modifycnccId)->exec();
                $this->loadModel('action')->create('modifycncc', $outwarddelivery->modifycnccId, 'review', $this->post->comment);
                //若被外部退回，重新走流程需要通知清总
                $modifycncc = $this->loadModel('modifycncc')->getByID($outwarddelivery->modifycnccId);
                if(!empty($modifycncc->giteeId)){
                    $this->pushModifycnccState($modifycncc->code, zget($this->lang->outwarddelivery->statusList, 'reject'));
                }
            }
            if ($outwarddelivery->isNewProductEnroll == 1) {
                $productenroll = $this->dao->select('status')->from(TABLE_PRODUCTENROLL)->where('id')->eq($outwarddelivery->productEnrollId)->fetch();
                if ($productenroll->status != 'giteepass' and $productenroll->status != 'emispass') {
                    $this->dao->update(TABLE_PRODUCTENROLL)->set('status')->eq($newStatus)->set('lastDealDate')->eq($lastDealDate)->where('id')->eq($outwarddelivery->productEnrollId)->exec();
                    $this->loadModel('action')->create('productenroll', $outwarddelivery->productEnrollId, 'review', $this->post->comment);
                }
            }
            if ($outwarddelivery->isNewTestingRequest == 1) {
                $testingrequest = $this->dao->select('status')->from(TABLE_TESTINGREQUEST)->where('id')->eq($outwarddelivery->testingRequestId)->fetch();
                if ($testingrequest->status != 'testingrequestpass') {
                    $this->dao->update(TABLE_TESTINGREQUEST)->set('status')->eq($newStatus)->set('lastDealDate')->eq($lastDealDate)->where('id')->eq($outwarddelivery->testingRequestId)->exec();
                    $this->loadModel('action')->create('testingrequest', $outwarddelivery->testingRequestId, 'review', $this->post->comment);
                }
            }
        }
        //更新需求和问题解决时间
        /** @var problemModel $problemModel */
//        $problemModel = $this->loadModel('problem');
//        if($outwarddelivery->modifycnccId > 0)
//        {
//           if(!empty($outwarddelivery->demandId)){
//              $demandIds =array_filter(explode(',',$outwarddelivery->demandId));
//              if($demandIds){
//                  foreach($demandIds as $demandId)
//                  {
//                    $problemModel->getAllSecondSolveTime($demandId,'demand');
//                  }
//              }
//           }
          /*if(!empty($outwarddelivery->problemId)){
              $problemIds =array_filter(explode(',',$outwarddelivery->problemId));
              if($problemIds){
                  foreach($problemIds as $problemId)
                  {
                     $problemModel->getAllSecondSolveTime($problemId,'problem');
                  }
              }
          }*/
//        }
        return $info;
    }

    /**
     * Project: chengfangjinke
     * Method: getToAndCcList
     * User: tangfei
     * Year: 2022
     * Date: 2022/06/20
     * Time: 14:44
     * Desc: 检查信息是否允许当前用户审核.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $outwarddelivery
     * @param $version
     * @param $reviewStage
     * @param $userAccount
     * @return array
     */
    public function checkAllowReview($outwarddelivery, $version = 1, $reviewStage = 0, $userAccount = '')
    {
        $res = array(
            'result' => false,
            'message' => '',
        );
        if (!$outwarddelivery) {
            $res['message'] = $this->lang->common->errorParamId;
            return $res;
        }
        if (!$userAccount) {
            $res['message'] = $this->lang->common->errorParamUser;
            return $res;
        }
        //审核节点已经经过
        if (($version != $outwarddelivery->version) || ($reviewStage != $outwarddelivery->reviewStage) || ($outwarddelivery->status == 'reject')) {
            $reviewerInfo = $this->loadModel('review')->getReviewedUserInfo('outwarddelivery', $outwarddelivery->id, $version, $reviewStage);
            $message = $this->lang->review->statusError;
            if ($reviewerInfo) {
                $message = str_replace('%', $reviewerInfo->realname, $this->lang->review->statusError);
            }
            $res['message'] = $message;
            return $res;
        }
        //获得当前的的审核人信息
        $reviews = $this->loadModel('review')->getReviewer('outwarddelivery', $outwarddelivery->id, $outwarddelivery->version, $outwarddelivery->reviewStage);
        if (!$reviews) {
            $res['message'] = $this->lang->review->reviewEnd;
            return $res;
        }
        //授权管理转化人员信息
        $reviewArray = $this->loadModel('common')->getAuthorizer('outwarddelivery', $reviews, $outwarddelivery->status, $this->lang->outwarddelivery->authorizeStatusList);
        $reviewArray = explode(',', $reviewArray);
        if (!in_array($userAccount, $reviewArray)) {
            $res['message'] = $this->lang->review->statusUserError;
            return $res;
        }
        $res['result'] = true;
        $res['reviews'] = $reviewArray;
        $reviewsOriginal = explode(',', $reviews);
        $res['reviewsOriginal'] = $reviewsOriginal;
        foreach ($reviewsOriginal as $original){
            $reviewAuthorize = $this->loadModel('common')->getAuthorizer('outwarddelivery', $original, $outwarddelivery->status, $this->lang->outwarddelivery->authorizeStatusList);
            $reviewAuthorize = explode(',' , $reviewAuthorize);
            if(in_array($userAccount, $reviewAuthorize)){
                $res['reviewAuthorize'] = $original;
                break;
            }
        }
        return $res;
    }

    /**
     * Project: chengfangjinke
     * Method: link
     * User: tangfei
     * Year: 2022
     * Date: 2022/6/22
     * Time: 14:46
     * Desc: This is the code comment. This method is called link.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $outwarddeliveryID
     * @return false|void
     */
    public function link($outwarddeliveryID)
    {
        $outwarddelivery = $this->getByID($outwarddeliveryID);
        $formId = $outwarddelivery->id . $outwarddelivery->reviewStage . $outwarddelivery->version . $outwarddelivery->dealUser;
        if ($_POST['formId'] != $formId) {
            dao::$errors['repeat'] = "您已经处理过该审批了"; //迭代15
            return false;
        }
        unset($_POST['formId']);

        //判断当前状态和审核中传入的状态是否一致，以防有同组中其他人修改
        $res = $this->checkAllowReview($outwarddelivery, $this->post->version, $this->post->reviewStage, $this->app->user->account);
        if (!$res['result']) {
            dao::$errors['statusError'] = $res['message'];
            return false;
        }

        if (empty($this->post->release[0]) && empty($this->post->release[1])) {
            dao::$errors['release'] = $this->lang->outwarddelivery->releaseEmpty;
            return false;
        }
        $config = $this->dao->select('`key`,`value`')->from(TABLE_LANG)->where('section')->eq('mediaCheckList')->fetchPairs('key');
        if ($config['link'] == 1) { //校验开关
            foreach ($this->post->release as $releaseId) {
                if (empty($releaseId)) continue; //多选有空
                $release = $this->loadModel('projectrelease')->getPath($releaseId);
                if (!$this->projectrelease->checkPath($release->path, $release->name)) {
                    dao::$errors['release'] = dao::$errors['path'];
                }
            }
            unset(dao::$errors['path']);
            if (dao::$errors['release']) {
                return false;
            }
        }


      /*  if (!$this->post->consumed) {
            dao::$errors['consumed'] = $this->lang->outwarddelivery->consumedEmpty;
            return false;
        }

        $reg = '/^(([1-9][0-9]*)|(([0]\.\d{1}|[1-9][0-9]*\.\d{1})))$/';
        if (!preg_match($reg, $this->post->consumed)) {
            dao::$errors['consumed'] = $this->lang->outwarddelivery->consumedError;
            return false;
        }*/

        //新代码
        //确定审批经过哪些节点
        if ($outwarddelivery->isOutsideReject) {   //如果是外部退回，且编辑过程中没有修改变更级别，则采用退回页面选择的审批节点
            $requiredStage = explode(',', $outwarddelivery->requiredReviewNode);
        } else {                                    //如果不是退回，或者退回后编辑了“变更级别”字段
            if ($outwarddelivery->isNewModifycncc == 1) {
                $requiredStage = $this->lang->outwarddelivery->requiredReviewerList[$outwarddelivery->level];  //不同表单类型的审批节点不同
            } elseif ($outwarddelivery->isNewProductEnroll == 1) {
                $requiredStage = $this->lang->outwarddelivery->requiredReviewerList[5];
            } elseif ($outwarddelivery->isNewTestingRequest == 1) {
                $requiredStage = $this->lang->outwarddelivery->requiredReviewerList[4];
            }
        }

        //每次审批通过，至少前进一步
        $afterStage = $outwarddelivery->reviewStage + 1;
        $approveNode = explode(',', $outwarddelivery->approvedNode);
        array_push($approveNode, $afterStage - 1);
        while ($afterStage < self::MAXNODE) {
            if (!in_array($afterStage, $requiredStage)) {  //如果跳过后的节点仍然跳过，继续前进
                $afterStage += 1;
                array_push($approveNode, $afterStage - 1);
            } else {  //如果节点不用继续跳过，则跳出循环
                break;
            }
        }

        //更新状态
        if (isset($this->lang->outwarddelivery->reviewBeforeStatusList[$afterStage])) {
            $status = $this->lang->outwarddelivery->reviewBeforeStatusList[$afterStage];
        }

        $data = new stdClass();
        $data->reviewStage = $afterStage;
        $data->status = $status;
        $data->release = trim(implode(',', $this->post->release), ',');
        $data->lastDealDate = date('Y-m-d');
        $data->approvedNode = trim(implode(',', $approveNode), ',');
        $data->ifMediumChanges = $this->post->isMediaChanged;

        $productInfoArray = array();
        foreach ($this->post->release as $item) {
            $release = $this->dao->select('`desc`')->from(TABLE_RELEASE)->where('id')->eq($item)->fetch('desc');
            $release = trim(strip_tags(str_replace("&nbsp;", ",", htmlspecialchars_decode($release))));
            $release = preg_replace('/[\r\n]/', ',',$release);
            $release = trim(implode(',',array_filter(explode(',',$release))),',');
            if (!in_array($release, $productInfoArray)) {
                array_push($productInfoArray, $release);
            }
        }
        $productInfoStr = trim(implode(',', $productInfoArray), ',');
        $data->productInfoCode = $productInfoStr;

        $this->dao->update(TABLE_OUTWARDDELIVERY)->data($data)->autoCheck()->batchCheck($this->config->outwarddelivery->link->requiredFields, 'notempty')
            ->where('id')->eq($outwarddeliveryID)->exec();

        //更新子表单的状态
        if ($outwarddelivery->isNewModifycncc == 1) {
            $this->dao->update(TABLE_MODIFYCNCC)->set('ifMediumChanges')->eq($data->ifMediumChanges)->set('status')->eq($data->status)->set('lastDealDate')->eq($data->lastDealDate)->set('release')->eq($data->release)->set('productCode')->eq($productInfoStr)->where('id')->eq($outwarddelivery->modifycnccId)->exec();
            $this->loadModel('action')->create('modifycncc', $outwarddelivery->modifycnccId, 'linkrelease', $this->post->comment);
            //若被外部退回，重新走流程需要通知清总
            $modifycncc = $this->loadModel('modifycncc')->getByID($outwarddelivery->modifycnccId);
            if(!empty($modifycncc->giteeId)){
                $this->pushModifycnccState($modifycncc->code, zget($this->lang->outwarddelivery->statusList, $data->status));
            }
        }
        if ($outwarddelivery->isNewProductEnroll == 1 and in_array($outwarddelivery->reviewStage, explode(',', '0,1,2,7')) == 1) {
            $productenroll = $this->dao->select('status')->from(TABLE_PRODUCTENROLL)->where('id')->eq($outwarddelivery->productEnrollId)->fetch();
            if ($productenroll->status != 'giteepass' and $productenroll->status != 'emispass') {
                $this->dao->update(TABLE_PRODUCTENROLL)->set('ifMediumChanges')->eq($data->ifMediumChanges)->set('status')->eq($data->status)->set('lastDealDate')->eq($data->lastDealDate)->set('release')->eq($data->release)->set('productCode')->eq($productInfoStr)->where('id')->eq($outwarddelivery->productEnrollId)->exec();
                $this->loadModel('action')->create('productenroll', $outwarddelivery->productEnrollId, 'linkrelease', $this->post->comment);
            }
        }
        if ($outwarddelivery->isNewTestingRequest == 1 and in_array($outwarddelivery->reviewStage, explode(',', '0,1,2,7')) == 1) {
            $testingrequest = $this->dao->select('status')->from(TABLE_TESTINGREQUEST)->where('id')->eq($outwarddelivery->testingRequestId)->fetch();
            if ($testingrequest->status != 'testingrequestpass') {
                $this->dao->update(TABLE_TESTINGREQUEST)->set('ifMediumChanges')->eq($data->ifMediumChanges)->set('status')->eq($data->status)->set('lastDealDate')->eq($data->lastDealDate)->set('release')->eq($data->release)->where('id')->eq($outwarddelivery->testingRequestId)->exec();
                $this->loadModel('action')->create('testingrequest', $outwarddelivery->testingRequestId, 'linkrelease', $this->post->comment);
            }
        }

        //一个人审核通过就可以
        $is_all_check_pass = false;
        //$this->loadModel('review')->check('outwarddelivery', $outwarddeliveryID, $outwarddelivery->version, 'pass', $this->post->comment, 0, null, $is_all_check_pass);
        //授权管咯
        $this->loadModel('common')->check('outwarddelivery', $outwarddeliveryID, $outwarddelivery->version, 'pass', $this->post->comment, 0, null, $is_all_check_pass, $res['reviewsOriginal'], $res['reviews'], $res['reviewAuthorize']);
        if ($afterStage - $outwarddelivery->reviewStage > 1) {
            $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('ignore')->where('objectType')->eq('outwarddelivery')//将跳过的节点，更新为ignore
            ->andWhere('objectID')->eq($outwarddelivery->id)
                ->andWhere('version')->eq($outwarddelivery->version)
                ->andWhere('status')->eq('wait')
                ->orderBy('stage,id')->limit($afterStage - $outwarddelivery->reviewStage - 1)->exec();
        }
        $next = $this->dao->select('id')->from(TABLE_REVIEWNODE)->where('objectType')->eq('outwarddelivery')
            ->andWhere('objectID')->eq($outwarddeliveryID)
            ->andWhere('version')->eq($outwarddelivery->version)
            ->andWhere('status')->eq('wait')->orderBy('stage,id')->fetch('id');

        if ($next) {
            $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('pending')->where('id')->eq($next)->exec();
            $this->dao->update(TABLE_REVIEWER)->set('status')->eq('pending')->where('node')->eq($next)->exec();
        }

        $newOutwarddelivery = $this->getByID($outwarddeliveryID);
        $this->loadModel('review');
        $reviewers = $this->review->getReviewer('outwardDelivery', $newOutwarddelivery->id, $newOutwarddelivery->version, $newOutwarddelivery->reviewStage);
        $this->dao->update(TABLE_OUTWARDDELIVERY)->set('dealUser')->eq($reviewers)->where('id')->eq($outwarddeliveryID)->exec();

        $this->loadModel('consumed')->record('outwarddelivery', $outwarddeliveryID, '0', $this->app->user->account, 'wait', 'cmconfirmed', array());
    }


    /**
     * Project: chengfangjinke
     * Method: isClickable
     * User: tangfei
     * Year: 2022
     * Date: 2022/6/21
     * Time: 14:46
     * Desc: This is the code comment. This method is called isClickable.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $outwarddelivery
     * @param $action
     * @return bool
     */
    public static function isClickable($outwarddelivery, $action)
    {
        global $app,$lang;
        $action = strtolower($action);
        //单子删除后，所有按钮不可见
        if($outwarddelivery->status == 'deleted' || !isset($outwarddelivery->status )){
            return false;
        }
        $outwarddeliveryModel = new self();

        if ($action == 'edit') return (in_array($outwarddelivery->status,$lang->outwarddelivery->alloweditStatus)) and ($app->user->account == $outwarddelivery->createdBy or $app->user->account == 'admin') and $outwarddelivery->closed != 1;
        if ($action == 'reject') return in_array($outwarddelivery->status,$lang->outwarddelivery->allowRejectArray) and $outwarddelivery->closed != 1;
        if ($action == 'review') return strpos(",$outwarddelivery->dealUser,", ",{$app->user->account},") !== false and in_array($outwarddelivery->status, array('reviewfailed','reject', 'withexternalapproval', 'waitsubmitted', 'qingzongsynfailed', 'modifyreject', 'testingrequestreject', 'productenrollreject', 'giteeback')) != 1 and $outwarddelivery->closed != 1;
        if ($action == 'submit') return $outwarddelivery->status == 'waitsubmitted' and ($app->user->account == $outwarddelivery->createdBy or $app->user->account == 'admin') and $outwarddelivery->closed != 1;// and $outwarddelivery->issubmit == 'submit'
        if ($action == 'delete') return $app->user->account == 'admin' or ($app->user->account == $outwarddelivery->createdBy and $outwarddelivery->status == 'waitsubmitted' and $outwarddelivery->version == 1);

        if ($action == 'close') return !in_array($outwarddelivery->status, array('testingrequestpass','productenrollpass','modifysuccess','cancel')) and $outwarddelivery->closed != 1;
        return true;
    }

    /**
     * @param $app //逗号分隔
     */
    public function getAppInfo($app)
    {
        return $this->dao->select('id,code,CASE WHEN code != "" THEN concat(concat(code,"_"),name) ELSE name END as name,isPayment,team')->from(TABLE_APPLICATION)->where('id')->in($app)->fetchAll();
    }

    /**
     * @param $CBPprojectIds //逗号分隔
     * @return mixed
     */
    public function getCBPInfo($CBPprojectIds)
    {
        return $this->dao->select('code,name')->from(TABLE_CBPPROJECT)->where('code')->in($CBPprojectIds)->andWhere('deleted')->ne(1)->fetchAll();

    }

    /**
     * 获取带有id code的 列表
     * @return mixed
     */
    public function getCodePairs()
    {
        return $this->dao->select('id,code')->from(TABLE_OUTWARDDELIVERY)
            ->where('deleted')->ne(1)
            ->orderBy('id_desc')
            ->fetchPairs();
    }

    /**
     * 获取带有新建内容的列表
     * @return mixed
     */
    public function getDetailPairs($id = 0)
    {
        $info = $this->dao->select('id,code, productEnrollId,testingRequestId,modifycnccId,isNewTestingRequest,isNewProductEnroll, isNewModifycncc')->from(TABLE_OUTWARDDELIVERY)
            ->where('deleted')->ne(1)
            ->beginIF($id)->andWhere('id')->eq($id)->fi()
            ->orderBy('id_desc')
            ->fetchAll('id');
        foreach ($info as &$item) {
            if ($item->isNewProductEnroll == 0) {
                $item->productEnrollId = 0;
            } else {
                $item->productEnrollCode = $this->dao->select('code')->from(TABLE_PRODUCTENROLL)->where('id')->eq($item->productEnrollId)->fetch('code');;

            }

            if ($item->isNewTestingRequest == 0) {
                $item->testingRequestId = 0;
            } else {
                $item->testingRequestCode = $this->dao->select('code')->from(TABLE_TESTINGREQUEST)->where('id')->eq($item->testingRequestId)->fetch('code');;

            }

            if ($item->isNewModifycncc == 0) {
                $item->modifycnccId = 0;
            } else {
                $item->modifycnccCode = $this->dao->select('code')->from(TABLE_MODIFYCNCC)->where('id')->eq($item->modifycnccId)->fetch('code');;

            }
            unset($item->isNewModifycncc);
            unset($item->isNewTestingRequest);
            unset($item->isNewProductEnroll);
        }

        return $info;


    }

    /**
     *检查是否允许驳回
     *
     * @param $info
     * @return bool
     */
    public function checkAllowReject($outwarddelivery)
    {
        return true;
        /*  $res = false;
          if(in_array($outwarddelivery->status, $this->lang->outwarddelivery->allowRejectStatusList)){
              $res = true;
          }
          $actions    = $this->loadModel('action')->getList('outwarddelivery', $outwarddelivery->id);

          $date = '';
          foreach ($actions as $action){
              if($action->action == 'sync' or $action->action == 'update'){
                  $date = $action->date;
              }
          }
          if($date != '' and $date < $outwarddelivery->feedbackDate){
              $res = true;
          }

          return  $res;*/
    }

    /**
     * Project: chengfangjinke
     * Method: getReviewers
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:45
     * Desc: This is the code comment. This method is called getReviewers.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $deptId
     * @return array
     */
    public function getReviewers($deptId = 0)
    {
        $users = $this->loadModel('user')->getPairs('noletter|noclosed');
        $reviewers = array();
        if (!$deptId) {
            $deptId = $this->app->user->dept;
        }
        $myDept = $this->loadModel('dept')->getByID($deptId);

        // 质量部CM
        $cms = explode(',', trim($myDept->cm, ','));
        $us = array('' => '');
        foreach ($cms as $c) {
            $us[$c] = $users[$c];
        }
        $reviewers[] = $us;

        //申请部门组长审批
        $groupUsers = explode(',', trim($myDept->groupleader, ','));
        $us = array('' => '');
        if (!empty($groupUsers)) {
            foreach ($groupUsers as $c) {
                $us[$c] = $users[$c];
            }
        }
        $reviewers[] = $us;

        // 部门负责人
        $cms = explode(',', trim($myDept->manager, ','));
        $us = array('' => '');
        foreach ($cms as $c) {
            $us[$c] = $users[$c];
        }
        $reviewers[] = $us;

        // 系统部
        $sysDept = $this->dao->select('id,manager')->from(TABLE_DEPT)->where('name')->eq('系统部')->fetch();
        $cms = explode(',', trim($sysDept->manager, ','));
        $us = array('' => '');
        foreach ($cms as $c) {
            $us[$c] = $users[$c];
        }
        $reviewers[] = $us;

        // 产品经理
        $cms = explode(',', trim($myDept->po, ','));
        $us = array('' => '');
        foreach ($cms as $c) {
            $us[$c] = $users[$c];
        }
        $reviewers[] = $us;

        // 部门分管领导
        $cms = explode(',', trim($myDept->leader, ','));
        $us = array('' => '');
        foreach ($cms as $c) {
            $us[$c] = $users[$c];
        }
        $reviewers[] = $us;

        // 总经理
        $reviewer = $this->dao->select('account')->from(TABLE_USER)->where('role')->eq('ceo')->fetch('account');
        $reviewers[] = array($reviewer => $users[$reviewer]);

        // 产创部二线专员
        $cms = explode(',', trim($myDept->executive, ','));
        $us = array('' => '');
        foreach ($cms as $c) {
            $us[$c] = $users[$c];
        }
        $reviewers[] = $us;

        return $reviewers;
    }

    /**
     * 获取发布信息
     * @param $releaseId
     * @return mixed
     */
    public function getReleaseInfo($releaseId)
    {
        $relase = $this->dao->select('id, name,path')->from(TABLE_RELEASE)->where('id')->eq($releaseId)->fetch();
        if ($relase) {
            $files = $this->dao->select('*')->from(TABLE_FILE)->where('objectType')->eq('release')
                ->andWhere('objectID')->in($releaseId)
                ->andWhere('deleted')->eq(0)
                ->fetchAll();
            $files && $relase->files = $files;
        }
        return $relase;
    }

    /**
     * User: TongYanQi
     * Date: 2022/8/29
     * 批量获取
     */
    public function getReleaseInfoInIds($releaseIds)
    {
        $releases = $this->dao->select('*')->from(TABLE_RELEASE)->where('id')->in($releaseIds)->fetchAll('id');

        if ($releases) {
            $files = $this->dao->select('*')->from(TABLE_FILE)->where('objectType')->eq('release')
                ->andWhere('objectID')->in($releaseIds)
                ->andWhere('deleted')->eq(0)
                ->fetchAll();
            foreach ($files as $file) {
                foreach ($releases as $releaseId => $v){
                    if ($file->objectID == $v->id){
                        $releases[$releaseId]->files[] = $file;
                    }
                }
            }
            $build = [];
            foreach ($releases as $releaseId => $v){
                if (empty($v->files)) $build[] = $v->build;
            }
            $builds = $this->loadModel('file')->getByObject('build', $build);
            foreach ($releases as $releaseId => $v){
                if (empty($v->files)){
                    foreach ($builds as $build) {
                        if ($build->objectID == $v->build){
                            $releases[$releaseId]->files[] = $build;
                        }
                    }
                }
            }
//            foreach ($releases as $releaseId => $v) {
//                $files = $this->dao->select('*')->from(TABLE_FILE)->where('objectType')->eq('release')
//                    ->andWhere('objectID')->in($releaseId)
//                    ->andWhere('deleted')->eq(0)
//                    ->fetchAll();
//                if (empty($files)) $files = $this->loadModel('file')->getByObject('build', $v->build);
//                $releases[$releaseId]->files = $files ?? null;
//            }

        }
        return $releases;
    }

    /**
     * 清空对外交付关联关系
     * @param $id
     */
    public function removeSecondLine($id)
    {
        $this->dao->update(TABLE_SECONDLINE)->set('deleted = "1" where (objectType = "outwardDelivery" and objectID = ' . $id . ') or (relationType = "outwardDelivery" and relationID = ' . $id . ') ')->exec();
    }

    /**
     *检查审核节点的审核人
     *
     * @param $level
     * @param $nodes
     * @param array $skipReviewNode
     * @return false
     */
    public function checkReviewerNodesInfo($requiredReviewerKeys, $nodes)
    {
        //检查结果
        $checkRes = true;
        $nodeKeys = array();
        foreach ($nodes as $key => $currentNodes) {
            //去除空元素
            $currentNodes = array_filter($currentNodes);
            if (!empty($currentNodes)) {
                $nodeKeys[] = $key;
            }
        }
        //必选审核人，却没有选
        $diffKeys = array_diff($requiredReviewerKeys, $nodeKeys);
        if (!empty($diffKeys)) {
            foreach ($diffKeys as $nodeKey) {
                dao::$errors[] = $this->lang->outwarddelivery->reviewerEmpty;
                break;
            }
        }

        if (dao::isError()) {
            $checkRes = false;
        }
        return $checkRes;
    }

    /**
     * 尝试报错 或需要rollback
     */
    public function tryError($rollBack = 0)
    {
        if (dao::isError()) {
            if ($rollBack == 1) {
                $this->dao->rollBack();
            }
            $response['result'] = 'fail';
            $response['message'] = dao::getError();
            $this->send($response);
        }
    }

    /**
     * Project: chengfangjinke
     * Method: update
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 11:31
     * Desc: This is the code comment. This method is called update.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $changeID
     * @return array
     */
    public function reject($outwardDeliveryID)
    {
        $revertReason = $this->post->revertReason;
        if (!$revertReason) {
            dao::$errors['statusError'] = $this->lang->outwarddelivery->revertReasonEmpty;
            return false;
        }
       /* if (!$this->post->consumed) {
            dao::$errors['consumed'] = $this->lang->outwarddelivery->consumedEmpty;
            return false;
        }
        $reg = '/^(([1-9][0-9]*)|(([0]\.\d{1}|[1-9][0-9]*\.\d{1})))$/';
        if (!preg_match($reg, $this->post->consumed)) {
            dao::$errors['consumed'] = $this->lang->outwarddelivery->consumedError;
            return false;
        }*/
        $comment = trim($this->post->comment);
        if (!$comment) {
            dao::$errors['statusError'] = $this->lang->outwarddelivery->rejectCommentEmpty;
            return false;
        }

        $outwardDelivery = $this->getByID($outwardDeliveryID);
        $skipReviewNodes = array_keys($this->post->skipReviewNode);
        $requiredReviewNode = '';
        if ($this->post->skipReviewNode) {
            $requiredReviewNode = implode(',', $skipReviewNodes);
        }
        $status = 'reject';
        //内部审核节点退回记为审核未通过，外部记为已退回
        if (in_array($outwardDelivery->status,$this->lang->outwarddelivery->reviewrejectStatus)){
            //如果单子被外部退回过，状态更新为已退回
            $rejectConsumed = $this->dao->select('id')
                ->from(TABLE_CONSUMED)
                ->where('objectType')->eq('outwarddelivery')
                ->andWhere('`objectID`')->eq($outwardDeliveryID)
                ->andWhere('`after`')->eq('reject')
                ->andWhere('deleted')->eq('0')->fetch();
            $status = !empty($rejectConsumed) ? 'reject' : 'reviewfailed';
        }
        //保存外部失败原因
        //$reviewFailReason = $this->getHistoryReview($outwardDelivery);
        $lastDealDate = date('Y-m-d');
        $data = new stdclass();
        $data->revertBy = $this->app->user->account;
        $data->revertReason = $revertReason;
        $data->lastDealDate = $lastDealDate;
        $data->revertDate = helper::now();
        $data->revertComment = $comment;
        $data->status = $status;
        $data->isOutsideReject = 1;
        $data->dealUser = $outwardDelivery->createdBy;
        $data->requiredReviewNode = $requiredReviewNode;
        $data->currentReview = '1';
        //$data->reviewFailReason = $reviewFailReason;

        $revertReasonOld = $outwardDelivery->revertReason;
        if (empty($revertReasonOld)) {
            $revertReasonArray = array();
        } else {
            $revertReasonArray = json_decode($revertReasonOld);
        }
        $revertReasonArray[] = array('RevertDate' => helper::now(), 'RevertReason' => $this->post->revertReason, 'RevertReasonChild' => $this->post->revertReasonChild);
        $data->revertReason = json_encode($revertReasonArray);

        $this->dao->update(TABLE_OUTWARDDELIVERY)->data($data)->where('id')->eq($outwardDeliveryID)->exec();

        $this->loadModel('consumed')->record('outwarddelivery', $outwardDeliveryID, '0', $this->app->user->account, $outwardDelivery->status, $status, array());

        //更新子表单的状态
        if ($outwardDelivery->isNewModifycncc == 1) {
            $this->dao->update(TABLE_MODIFYCNCC)->set('status')->eq($status)->set('lastDealDate')->eq($lastDealDate)->where('id')->eq($outwardDelivery->modifycnccId)->exec();
            //若被外部退回，重新走流程需要通知清总
            $modifycncc = $this->loadModel('modifycncc')->getByID($outwardDelivery->modifycnccId);
            if(!empty($modifycncc->giteeId)){
                $this->pushModifycnccState($modifycncc->code, zget($this->lang->outwarddelivery->statusList, 'reject'));
            }
        }
        if ($outwardDelivery->isNewProductEnroll == 1) {
            $productenroll = $this->dao->select('status')->from(TABLE_PRODUCTENROLL)->where('id')->eq($outwardDelivery->productEnrollId)->fetch();
            if ($productenroll->status != 'giteepass' and $productenroll->status != 'emispass') {
                $this->dao->update(TABLE_PRODUCTENROLL)->set('status')->eq($status)->set('lastDealDate')->eq($lastDealDate)->where('id')->eq($outwardDelivery->productEnrollId)->exec();
            }
        }
        if ($outwardDelivery->isNewTestingRequest == 1) {
            $testingrequest = $this->dao->select('status')->from(TABLE_TESTINGREQUEST)->where('id')->eq($outwardDelivery->testingRequestId)->fetch();
            if ($testingrequest->status != 'testingrequestpass') {
                $this->dao->update(TABLE_TESTINGREQUEST)->set('status')->eq($status)->set('lastDealDate')->eq($lastDealDate)->where('id')->eq($outwardDelivery->testingRequestId)->exec();
            }
        }

        //忽略节点
        $ret = $this->loadModel('review')->setReviewNodesIgnore('outwarddelivery', $outwardDeliveryID, $outwardDelivery->version);

        return true;
    }


    public function getOutwardDeliveryByTypeId($type, $id)
    {
        $type = strtolower($type);
        if ($type == strtolower(self::TESTINGREQUEST)) {
            return $this->dao->select('id')->from(TABLE_OUTWARDDELIVERY)->where('isNewTestingRequest')->eq(1)->andwhere('testingRequestId')->eq($id)->fetch('id');
        }
        if ($type == strtolower(self::PRODUCTENROLL)) {
            return $this->dao->select('id')->from(TABLE_OUTWARDDELIVERY)->where('isNewProductEnroll')->eq(1)->andwhere('productEnrollId')->eq($id)->fetch('id');
        }
        if ($type == strtolower(self::MODIFYCNCC)) {
            return $this->dao->select('id')->from(TABLE_OUTWARDDELIVERY)->where('isNewModifycncc')->eq(1)->andwhere('modifycnccId')->eq($id)->fetch('id');
        }
        return null;
    }

    public function getTypeRelations($type, $id)
    {
        $type = strtolower($type);
        $list = [];

        //获取其他对外交付关联自己的单 0xid = 0, 1xid = id 所有不是自己new的不会有id
        $select = "id, code,(isNewTestingRequest * testingRequestId) as testingRequestId, (isNewProductEnroll * productEnrollId) as productEnrollId, (isNewModifycncc * modifycnccId) as modifycnccId";
        if ($type == strtolower(self::TESTINGREQUEST)) {
            $list = $this->dao->select($select)->from(TABLE_OUTWARDDELIVERY)->where('testingRequestId')->eq($id)->andwhere('isNewTestingRequest')->eq(0)->andwhere('deleted')->eq(0)->fetchall('id');
        }
        if ($type == strtolower(self::PRODUCTENROLL)) {
            $list = $this->dao->select($select)->from(TABLE_OUTWARDDELIVERY)->where('productEnrollId')->eq($id)->andwhere('isNewProductEnroll')->eq(0)->andwhere('deleted')->eq(0)->fetchall('id');
        }
        if ($type == strtolower(self::MODIFYCNCC)) {
            $list = $this->dao->select($select)->from(TABLE_OUTWARDDELIVERY)->where('modifycnccId')->eq($id)->andwhere('isNewModifycncc')->eq(0)->andwhere('deleted')->eq(0)->fetchall('id');
        }

        $testList = [];
        $productList = [];
        $modifyList = [];
        $moreParent = [];
        foreach ($list as $item) {
            if ($item->testingRequestId) {
                $testList[] = $item->testingRequestId; //关联的所有测试申请
            }
            if ($item->productEnrollId) {
                $productList[] = $item->productEnrollId; //关联的所有产品登记
            }
            if ($item->modifycnccId) {
                $modifyList[] = $item->modifycnccId;  //关联的所有生产变更
            }
        }

        //获取本身关联的其他子表单
        $select = "id, code, isNewTestingRequest, testingRequestId, isNewProductEnroll, productEnrollId, isNewModifycncc, modifycnccId";
        if ($type == strtolower(self::TESTINGREQUEST)) {
            $myParent = $this->dao->select($select)->from(TABLE_OUTWARDDELIVERY)->where('testingRequestId')->eq($id)->andwhere('isNewTestingRequest')->eq(1)->andwhere('deleted')->eq(0)->fetch();
        }
        if ($type == strtolower(self::PRODUCTENROLL)) {
            $myParent = $this->dao->select($select)->from(TABLE_OUTWARDDELIVERY)->where('productEnrollId')->eq($id)->andwhere('isNewProductEnroll')->eq(1)->andwhere('deleted')->eq(0)->fetch();
        }
        if ($type == strtolower(self::MODIFYCNCC)) {
            $myParent = $this->dao->select($select)->from(TABLE_OUTWARDDELIVERY)->where('modifycnccId')->eq($id)->andwhere('isNewModifycncc')->eq(1)->andwhere('deleted')->eq(0)->fetch();
        }

        //其他子表单的父表单号
        if ($myParent->testingRequestId && $myParent->isNewTestingRequest != 1) {
            $moreParent[] = $this->dao->select($select)->from(TABLE_OUTWARDDELIVERY)->where('testingRequestId')->eq($myParent->testingRequestId)->andwhere('isNewTestingRequest')->eq(1)->andwhere('deleted')->eq(0)->fetch();
        } //关联的所有测试申请
        if ($myParent->productEnrollId && $myParent->isNewProductEnroll != 1) {
            $moreParent[] = $this->dao->select($select)->from(TABLE_OUTWARDDELIVERY)->where('productEnrollId')->eq($myParent->productEnrollId)->andwhere('isNewProductEnroll')->eq(1)->andwhere('deleted')->eq(0)->fetch();
        }
        if ($myParent->modifycnccId && $myParent->isNewModifycncc != 1) {
            $moreParent[] = $this->dao->select($select)->from(TABLE_OUTWARDDELIVERY)->where('modifycnccId')->eq($myParent->modifycnccId)->andwhere('isNewModifycncc')->eq(1)->andwhere('deleted')->eq(0)->fetch();

        }
        $moreParent = json_decode(json_encode($moreParent));

        //所有的父表单集合
        foreach ($moreParent as $item) {
            if ($item && empty($list[$item->id])) {
                $list[] = $item;
            }
        }
        $data = json_decode(json_encode(['parents' => $list, 'testList' => $testList, 'productList' => $productList, 'modifyList' => $modifyList]), 1);
        return ($data);
    }

    public function submit($outwarddeliveryID)
    {
        $outwardDelivery = $this->getByID($outwarddeliveryID);
        if ($outwardDelivery->status != 'waitsubmitted') {
            dao::$errors['statusError'] = $this->lang->outwarddelivery->statusError;
            return false;
        }
       /* if (!$this->post->consumed) {
            dao::$errors['consumed'] = $this->lang->outwarddelivery->consumedEmpty;
            return false;
        }
        $reg = '/^(([1-9][0-9]*)|(([0]\.\d{1}|[1-9][0-9]*\.\d{1})))$/';
        if (!preg_match($reg, $this->post->consumed)) {
            dao::$errors['consumed'] = $this->lang->outwarddelivery->consumedError;
            return false;
        }*/

        $skipReviewNodes = array_keys($this->post->skipReviewNode);
        $requiredReviewNode = '';
        if ($this->post->skipReviewNode) {
            $requiredReviewNode = implode(',', $skipReviewNodes);
        }

        //新代码
        //确定审批经过哪些节点
        $requiredStage = explode(',', $requiredReviewNode);

        //每次审批通过，至少前进一步
        $afterStage = $outwardDelivery->reviewStage;
        $approveNode = array();
        while ($afterStage < self::MAXNODE) {
            if (!in_array($afterStage, $requiredStage)) {  //如果跳过后的节点仍然跳过，继续前进
                array_push($approveNode, $afterStage);
                $afterStage += 1;
            } else {  //如果节点不用继续跳过，则跳出循环
                break;
            }
        }

        if ($afterStage - $outwardDelivery->reviewStage >= 1) {
            $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('ignore')->where('objectType')->eq('outwarddelivery')//将跳过的节点，更新为ignore
            ->andWhere('objectID')->eq($outwardDelivery->id)
                ->andWhere('version')->eq($outwardDelivery->version)
                ->orderBy('stage,id')->limit($afterStage - $outwardDelivery->reviewStage)->exec();
            $next = $this->dao->select('id')->from(TABLE_REVIEWNODE)->where('objectType')->eq('outwarddelivery')
                ->andWhere('objectID')->eq($outwarddeliveryID)
                ->andWhere('version')->eq($outwardDelivery->version)
                ->andWhere('status')->eq('wait')->orderBy('stage,id')->fetch('id');

            if ($next) {
                $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('pending')->where('id')->eq($next)->exec();
                $this->dao->update(TABLE_REVIEWER)->set('status')->eq('pending')->where('node')->eq($next)->exec();
            }
        }


        //更新状态
        if (isset($this->lang->outwarddelivery->reviewBeforeStatusList[$afterStage])) {
            $status = $this->lang->outwarddelivery->reviewBeforeStatusList[$afterStage];
        }

        $this->loadModel('review');
        $reviewers = $this->review->getReviewer('outwardDelivery', $outwardDelivery->id, $outwardDelivery->version, $outwardDelivery->reviewStage);
        $lastDealDate = date('Y-m-d');
        $this->dao->update(TABLE_OUTWARDDELIVERY)->set('modifyLevel')->eq('1')->set('isOutsideReject')->eq('1')->set('approvedNode')->eq(trim(implode(',', $approveNode), ','))->set('requiredReviewNode')->eq($requiredReviewNode)->set('reviewStage')->eq($afterStage)
            ->set('status')->eq($status)->set('lastDealDate')->eq($lastDealDate)->set('dealUser')->eq($reviewers)->where('id')->eq($outwarddeliveryID)->exec();

        $this->loadModel('consumed')->record('outwarddelivery', $outwarddeliveryID, '0', $this->app->user->account, $outwardDelivery->status, $status, array());
        //更新子表单的状态
        if ($outwardDelivery->isNewModifycncc == 1) {
            $this->dao->update(TABLE_MODIFYCNCC)->set('status')->eq($status)->set('lastDealDate')->eq($lastDealDate)->where('id')->eq($outwardDelivery->modifycnccId)->exec();
            $this->loadModel('action')->create('modifycncc', $outwardDelivery->modifycnccId, 'submitexamine', $this->post->comment);
            //若被外部退回，重新走流程需要通知清总
            $modifycncc = $this->loadModel('modifycncc')->getByID($outwardDelivery->modifycnccId);
            if(!empty($modifycncc->giteeId)){
                $this->pushModifycnccState($modifycncc->code, zget($this->lang->outwarddelivery->statusList, $status));
            }
        }
        if ($outwardDelivery->isNewProductEnroll == 1) {
            $productenroll = $this->dao->select('status')->from(TABLE_PRODUCTENROLL)->where('id')->eq($outwardDelivery->productEnrollId)->fetch();
            if ($productenroll->status != 'giteepass' and $productenroll->status != 'emispass') {
                $this->dao->update(TABLE_PRODUCTENROLL)->set('status')->eq($status)->set('lastDealDate')->eq($lastDealDate)->where('id')->eq($outwardDelivery->productEnrollId)->exec();
                $this->loadModel('action')->create('productenroll', $outwardDelivery->productEnrollId, 'submitexamine', $this->post->comment);
            }
        }
        if ($outwardDelivery->isNewTestingRequest == 1) {
            $testingrequest = $this->dao->select('status')->from(TABLE_TESTINGREQUEST)->where('id')->eq($outwardDelivery->testingRequestId)->fetch();
            if ($testingrequest->status != 'testingrequestpass') {
                $this->dao->update(TABLE_TESTINGREQUEST)->set('status')->eq($status)->set('lastDealDate')->eq($lastDealDate)->where('id')->eq($outwardDelivery->testingRequestId)->exec();
                $this->loadModel('action')->create('testingrequest', $outwardDelivery->testingRequestId, 'submitexamine', $this->post->comment);
            }
        }

        return true;
    }

    public function deleted($outwarddeliveryID)
    {
        $comment = trim($this->post->comment);
        if (!$comment) {

            dao::$errors['statusError'] = $this->lang->outwarddelivery->rejectCommentEmpty;
            return false;
        }

        $outwarddelivery = $this->getByID($outwarddeliveryID);

        if ($outwarddelivery->isNewTestingRequest == 1) {
            $logObj1 = $this->dao->select('*')->from(TABLE_REQUESTLOG)
                ->where('objectType')->eq('testingrequest')
                ->andwhere('objectId')->eq((int)$outwarddelivery->testingRequestId)
                ->andwhere('purpose')->eq('pushtestingrequest')
                ->andwhere('status')->eq('success')
                ->fetchAll();
            if (!empty($logObj1)) {
                dao::$errors['statusError'] = '测试申请' . $this->lang->outwarddelivery->rejectDelete;
                return false;
            }
        }
        if ($outwarddelivery->isNewProductEnroll == 1) {
            $logObj2 = $this->dao->select('*')->from(TABLE_REQUESTLOG)
                ->where('objectType')->eq('productenroll')
                ->andwhere('objectId')->eq((int)$outwarddelivery->productEnrollId)
                ->andwhere('purpose')->eq('pushproductenroll')
                ->andwhere('status')->eq('success')
                ->fetchAll();
            if (!empty($logObj2)) {
                dao::$errors['statusError'] = '产品登记' . $this->lang->outwarddelivery->rejectDelete;
                return false;
            }
        }
        if ($outwarddelivery->isNewModifycncc == 1) {
            $logObj3 = $this->dao->select('*')->from(TABLE_REQUESTLOG)
                ->where('objectType')->eq('modifycncc')
                ->andwhere('objectId')->eq((int)$outwarddelivery->modifycnccId)
                ->andwhere('purpose')->eq('pushmodifycncc')
                ->andwhere('status')->eq('success')
                ->fetchAll();
            if (!empty($logObj3)) {
                dao::$errors['statusError'] = '生产变更' . $this->lang->outwarddelivery->rejectDelete;
                return false;
            }
        }

        //删除对外交付单
        $this->dao->update(TABLE_OUTWARDDELIVERY)->set('status')->eq('deleted')->set('deleted')->eq('1')->where('id')->eq($outwarddeliveryID)->exec();
        $this->loadModel('action')->create('outwarddelivery', $outwarddeliveryID, 'deleted', $this->post->comment);

        //2022.4.21 tangfei、guchaonan 删除与问题需求的关联关系
        $this->dao->update(TABLE_SECONDLINE)
            ->set('deleted')->eq('1')
            ->where('relationID')->eq($outwarddeliveryID)
            ->andWhere('relationType')->eq('outwardDelivery')
            ->exec();
        $this->dao->update(TABLE_SECONDLINE)
            ->set('deleted')->eq('1')
            ->where('objectID')->eq($outwarddeliveryID)
            ->andWhere('objectType')->eq('outwardDelivery')
            ->exec();

        //删除子表单

        if ($outwarddelivery->isNewModifycncc == 1) {
            $this->dao->update(TABLE_MODIFYCNCC)->set('status')->eq('deleted')->set('deleted')->eq('1')->where('id')->eq($outwarddelivery->modifycnccId)->exec();
            //解绑关联的异常变更，以让旧单子继续被新单子关联
//            $this->dao->update(TABLE_OUTWARDDELIVERY)->set('abnormalCode')->eq('')->where('abnormalCode')->eq($outwarddelivery->modifycnccId)->exec();
            $findInSet = '(FIND_IN_SET("'.$outwarddelivery->modifycnccId.'",abnormalCode))';
            $oldInfo = $this->dao->select("id,abnormalCode")->from(TABLE_OUTWARDDELIVERY)->where('abnormalCode')->eq($outwarddelivery->modifycnccId)->fetch();
            if ($oldInfo->abnormalCode != ''){
                $arr = array_flip(explode(',',$oldInfo->abnormalCode));
                unset($arr[$outwarddelivery->modifycnccId]);
                $arr = array_flip(array_unique($arr));
                $str = implode(',',$arr);
                $this->dao->update(TABLE_OUTWARDDELIVERY)->set('abnormalCode="'.$str.'"')->where('id')->eq($oldInfo->id)->exec();
            }
            $this->loadModel('action')->create('modifycncc', $outwarddelivery->modifycnccId, 'deleted', $this->post->comment);
            $this->dao->update(TABLE_SECONDLINE)
                ->set('deleted')->eq('1')
                ->where('relationID')->eq($outwarddelivery->modifycnccId)
                ->andWhere('relationType')->eq('modifycncc')
                ->exec();
            $this->dao->update(TABLE_SECONDLINE)
                ->set('deleted')->eq('1')
                ->where('objectID')->eq($outwarddelivery->modifycnccId)
                ->andWhere('objectType')->eq('modifycncc')
                ->exec();
        }
        if ($outwarddelivery->isNewProductEnroll == 1) {
            $this->dao->update(TABLE_PRODUCTENROLL)->set('status')->eq('deleted')->set('deleted')->eq('1')->where('id')->eq($outwarddelivery->productEnrollId)->exec();
            $this->loadModel('action')->create('productenroll', $outwarddelivery->productEnrollId, 'deleted', $this->post->comment);
            $this->dao->update(TABLE_SECONDLINE)
                ->set('deleted')->eq('1')
                ->where('relationID')->eq($outwarddelivery->productEnrollId)
                ->andWhere('relationType')->eq('productenroll')
                ->exec();
            $this->dao->update(TABLE_SECONDLINE)
                ->set('deleted')->eq('1')
                ->where('objectID')->eq($outwarddelivery->productEnrollId)
                ->andWhere('objectType')->eq('productenroll')
                ->exec();
            $this->dao->update(TABLE_OUTWARDDELIVERY)->set('productEnrollId')->eq('')->where('productEnrollId')->eq($outwarddelivery->productEnrollId)->exec();
        }
        if ($outwarddelivery->isNewTestingRequest == 1) {
            $this->dao->update(TABLE_TESTINGREQUEST)->set('status')->eq('deleted')->set('deleted')->eq('1')->where('id')->eq($outwarddelivery->testingRequestId)->exec();
            $this->loadModel('action')->create('testingrequest', $outwarddelivery->testingRequestId, 'deleted', $this->post->comment);
            $this->dao->update(TABLE_SECONDLINE)
                ->set('deleted')->eq('1')
                ->where('relationID')->eq($outwarddelivery->testingRequestId)
                ->andWhere('relationType')->eq('testingrequest')
                ->exec();
            $this->dao->update(TABLE_SECONDLINE)
                ->set('deleted')->eq('1')
                ->where('objectID')->eq($outwarddelivery->testingRequestId)
                ->andWhere('objectType')->eq('testingrequest')
                ->exec();
            $this->dao->update(TABLE_OUTWARDDELIVERY)->set('testingRequestId')->eq('')->where('testingRequestId')->eq($outwarddelivery->testingRequestId)->exec();
        }
        //更新需求和问题解决时间
        /** @var problemModel $problemModel */
//        $problemModel = $this->loadModel('problem');
//        if($outwarddelivery->modifycnccId > 0)
//        {
//           if(!empty($outwarddelivery->demandId)){
//              $demandIds =array_filter(explode(',',$outwarddelivery->demandId));
//              if($demandIds){
//                  foreach($demandIds as $demandId)
//                  {
//                    $problemModel->getAllSecondSolveTime($demandId,'demand');
//                  }
//              }
//           }
          /*if(!empty($outwarddelivery->problemId)){
              $problemIds =array_filter(explode(',',$outwarddelivery->problemId));
              if($problemIds){
                  foreach($problemIds as $problemId)
                  {
                     $problemModel->getAllSecondSolveTime($problemId,'problem');
                  }
              }
          }*/
//        }
        return true;
    }

    /**
     * 删除原状态流转工作量
     * @param $id
     */
    private function deleteWaitConsume($id)
    {
        $this->dao->update(TABLE_CONSUMED)
            ->set('deleted')->eq('1')
            ->where('objectID')->eq($id)
            ->andWhere('objectType')->eq('outwarddelivery')
            ->andWhere('after')->eq('wait')
            ->exec();
    }

    /**
     * 新老类进行比较
     * @param $newObject
     * @param $oldObject
     * @return void
     */
    private function changesCommon($newObject, $oldObject)
    {
        $changes = array();
        foreach ($newObject as $key => $value) {
            $diff = '';
            if (isset($oldObject->$key) and $value != stripslashes(is_array($oldObject->$key) ? json_encode($oldObject->$key) : $oldObject->$key)) {
                $changes[] = array('field' => $key, 'old' => is_array($oldObject->$key) ? json_encode($oldObject->$key) : $oldObject->$key, 'new' => $value, 'diff' => $diff);
            }
        }
        return $changes;
    }

    /**
     * 获取二线专员
     */
    public function getSecondLineReviewers($id = 0, $version = 1, $stage = 0)
    {
        $this->loadModel('review');
        $stage = 0;
        foreach ($this->lang->outwarddelivery->reviewerList as $review) {
            $stage++;
            if ($review == '产创部二线专员') {
                break;
            }
        }
        return $this->review->getLastPendingPeople('outwardDelivery', $id, $version, $stage);
    }

    /**
     * Send mail
     * @param  int $outwarddelivery
     * @param  int $actionID
     * @access public
     * @return void
     */
    public function sendmail($outwarddeliveryId, $actionID)
    {
        $this->loadModel('mail');
        $outwarddelivery = $this->getById($outwarddeliveryId);
        if ($outwarddelivery->issubmit == 'save'){
            return false;
        }
        $users = $this->loadModel('user')->getPairs('noletter');

        $outwarddelivery->reviewers = !empty($outwarddelivery->dealUser) ? $outwarddelivery->dealUser : $outwarddelivery->createdBy;
        if (!empty($outwarddelivery->dealUser)) {
            $outwarddelivery->reviewers = $this->loadModel('common')->getAuthorizer('outwarddelivery', $outwarddelivery->reviewers, $outwarddelivery->status, $this->lang->outwarddelivery->authorizeStatusList);
            $dealUsersArray = explode(',', $outwarddelivery->reviewers);
            //所有审核人
            $dealUsers = getArrayValuesByKeys($users, $dealUsersArray);
            $dealUsersStr = implode(',', $dealUsers);
        }
        $outwarddelivery->dealUsersStr = $dealUsersStr;
        $this->app->loadLang('outwarddelivery');

        //流程状态
        $outwarddelivery->statusDesc = zget($this->lang->outwarddelivery->statusList, $outwarddelivery->status);

        /* 获取后台通知中配置的邮件发信。*/
        $this->app->loadLang('custommail');
        $confObject = 'setOutwardDeliveryMail';
        $mailConf = isset($this->config->global->$confObject) ? $this->config->global->$confObject : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);

        /* 处理邮件发信的标题和日期。*/
        $bestDeal = $this->dao->select('*')->from(TABLE_CONSUMED)->where('objectType')->eq('outwarddelivery')
            ->andWhere('objectID')->eq($outwarddeliveryId)
            ->orderBy('id_desc')
            ->fetch();
        $bestDate = empty($bestDeal) ? '' : substr($bestDeal->createdDate, 0, 10);
        $mailTitle = vsprintf($mailConf->mailTitle, $mailConf->variables);
        $browseType = 'outwarddelivery';
        //$serverIp = $_SERVER['SERVER_NAME'] ?? '127.0.0.1'; //cli模式取不到SERVER_NAME 报错

        //部分成功、变更失败、变更取消、变更成功、变更退回、对外交付取消 状态发送通知邮件
        if(in_array($outwarddelivery->status, $this->lang->outwarddelivery->noticeStatus)){
            $mailTitle= sprintf($this->lang->outwarddelivery->noticetitle, zget($this->lang->outwarddelivery->statusList, $outwarddelivery->status));
            $mailConf->mailContent = $this->lang->outwarddelivery->noticecontent;
        }

        /* Get action info. */
        $this->app->loadConfig('message');
        $action = $this->loadModel('action')->getById($actionID);
        if (!in_array($action->action, $this->config->message->objectTypes['outwarddelivery'])) {
            return;
        }
        $history = $this->action->getHistory($actionID);
        $action->history = isset($history[$actionID]) ? $history[$actionID] : array();

        //是否显示外部状态审核状态
        $isShowExternalStatus = false;
        //外部操作日志动作
        $externalActionArray = [
            'modifycnccsyncfeedback', 'productenrollsyncfeedback', 'testrequestfeedback', 'testrequesteditfeedback', 'productenrolleditfeedback',
            'modifycncceditstatus', 'modifycnccsyncstatus', 'modifycncceditfeedback'
        ];
        if (in_array($action->action, $externalActionArray)) {
            $isShowExternalStatus = true;
            if ($action->action == 'testrequestfeedback' || $action->action == 'testrequesteditfeedback') {
                $testingrequestObj = $this->loadModel('testingrequest')->getByID($outwarddelivery->testingRequestId);
                $outwarddelivery->externalStatusDesc = zget($this->lang->outwarddelivery->statusList, $testingrequestObj->status);
                $outwarddelivery->externalRejectReason = $testingrequestObj->returnCase;
                if ($outwarddelivery->status != 'testingrequestpass' && $outwarddelivery->status != 'testingrequestreject') {
                    return;
                }
            } else if ($action->action == 'productenrollsyncfeedback' || $action->action == 'productenrolleditfeedback') {
                $this->app->loadLang('productenroll');
                $productenrollObj = $this->loadModel('productenroll')->getByID($outwarddelivery->productEnrollId);
                $outwarddelivery->externalStatusDesc = zget($this->lang->productenroll->statusList, $productenrollObj->status);
                $outwarddelivery->externalRejectReason = $productenrollObj->returnCase;
                if ($outwarddelivery->status != 'productenrollreject' && $outwarddelivery->status != 'productenrollpass') {
                    return;
                }
            } else {
                $modifycnccObj = $this->loadModel('modifycncc')->getByID($outwarddelivery->modifycnccId);
                $outwarddelivery->externalStatusDesc = zget($this->lang->outwarddelivery->statusList, $modifycnccObj->status);
                $outwarddelivery->externalRejectReason = $modifycnccObj->reasonCNCC;
            }
        }

        /* Get mail content. */
        $oldcwd = getcwd();
        $modulePath = $this->app->getModulePath($appName = '', 'outwarddelivery');
        $viewFile = $modulePath . 'view/sendmail.html.php';
        chdir($modulePath . 'view');

        if (file_exists($modulePath . 'ext/view/sendmail.html.php')) {
            $viewFile = $modulePath . 'ext/view/sendmail.html.php';
            chdir($modulePath . 'ext/view');
        }

        ob_start();
        include $viewFile;
        foreach (glob($modulePath . 'ext/view/sendmail.*.html.hook.php') as $hookFile) include $hookFile;
        $mailContent = ob_get_contents();
        ob_end_clean();

        chdir($oldcwd);

        if (!$outwarddelivery->reviewers) return;
        $toList = $outwarddelivery->reviewers;
        $ccList = '';
        // 抄送产品经理
        if ($outwarddelivery->status == 'gmsuccess') {
            $productIds = explode(",", trim($outwarddelivery->productId, ','));
            $appIds = explode(",", trim($outwarddelivery->app, ","));
            if (!empty($productIds)) {
                $POList = $this->dao->select('PO')->from(TABLE_PRODUCT)->where('id')->in($productIds)->fetchall();
                $ccList = implode(',', array_column($POList, 'PO'));
            } else if (!empty($appIds)) {
                $POList = $this->dao->select('PO')->from(TABLE_PRODUCT)->where('app')->in($appIds)->fetchall();
                $ccList = implode(',', array_column($POList, 'PO'));
            } else {
                $ccList = '';
            }
        } else if ($outwarddelivery->status == 'wait') {
            $deptObj = $this->loadModel('dept')->getByID($outwarddelivery->createdDept);
            $ccList = $deptObj->qa;
        } else if (in_array($outwarddelivery->status,$this->lang->outwarddelivery->reissueArray)){
            //变更异常抄送节点所有审核人 审核过的
            $res = $this->dao->select("t1.id,t1.reviewer")->from(TABLE_REVIEWER)->alias('t1')
                ->leftJoin(TABLE_REVIEWNODE)->alias('t2')
                ->on('t1.node=t2.id')
                ->where('objectType')->eq('outwarddelivery')
                ->andWhere('objectID')->eq($outwarddeliveryId)
                ->andWhere('t1.status')->in(['pass','reject'])
                ->fetchall();
            $ccList = array_unique(array_column($res,'reviewer'));
            $ccList = implode(',',$ccList);
        }else if($outwarddelivery->status == 'cancel'){
            //取消抄送节点所有审核人 审核过的
            $res = $this->dao->select("t1.id,t1.reviewer")->from(TABLE_REVIEWER)->alias('t1')
                ->leftJoin(TABLE_REVIEWNODE)->alias('t2')
                ->on('t1.node=t2.id')
                ->where('objectType')->eq('outwarddelivery')
                ->andWhere('objectID')->eq($outwarddeliveryId)
                ->andWhere('t1.status')->in(['pass','reject'])
                ->fetchall();
            $ccList = array_unique(array_column($res,'reviewer'));
            $ccList = implode(',',$ccList);
        }

        /* 处理邮件标题。*/
        //$subject = $this->getSubject($info);
        $subject = $mailTitle;

        /* Send emails. */
        //授权管理
        $toList = $this->loadModel('common')->getAuthorizer('outwarddelivery', $toList, $outwarddelivery->status, $this->lang->outwarddelivery->authorizeStatusList);
        $ccList = $this->loadModel('common')->getAuthorizer('outwarddelivery', $ccList, $outwarddelivery->status, $this->lang->outwarddelivery->authorizeStatusList);
        $this->mail->send($toList, $subject, $mailContent, $ccList);
        if ($this->mail->isError()) {
            a($this->mail->getError());
        }
    }

    /**
     * 重置推送状态
     * @param $type
     * @param $code
     */
    public function rePush($type, $code, $id = '')
    {
        $data['status'] = 'waitqingzong';
        $data['pushStatus'] = 0;
        $data['pushFailTimes'] = 0;
        $data['cardStatus'] = "";
        if ($type == self::TESTINGREQUEST) {
            $data['returnCase'] = "";
            $data['returnDate'] = "";
            $data['returnPerson'] = "";
            $this->dao->update(TABLE_TESTINGREQUEST)->data($data)->where('code')->eq($code)->exec();
        } elseif ($type == self::PRODUCTENROLL) {
            $data['returnCase'] = "";
            $data['returnDate'] = "";
            $data['returnPerson'] = "";
            $this->dao->update(TABLE_PRODUCTENROLL)->data($data)->where('code')->eq($code)->exec();
        } elseif ($type == self::MODIFYCNCC) {
            $this->dao->update(TABLE_MODIFYCNCC)->data($data)->where('code')->eq($code)->exec();
        }
    }

    /**
     * TongYanQi 2022/12/1
     * 对外交付单同步失败
     */
    public function setOutwardDeliverySyncFail($id)
    {
        if (empty(self::$_reviewers)) {
            self::$_reviewers = $this->lang->outwarddelivery->apiDealUserList['userAccount'];
        }
        $outwardDeliveryUpdate['status'] = 'qingzongsynfailed';
        $outwardDeliveryUpdate['dealUser'] = self::$_reviewers;
        $this->dao->update(TABLE_OUTWARDDELIVERY)->data($outwardDeliveryUpdate)->where('id')->eq($id)->exec();
    }

    /**
     * @Notes:获取金信生产变更数据，用于状态联动
     * @Date: 2023/4/13
     * @Time: 16:33
     * @Interface getEffectiveOutwardDeliveryQzData
     * @param $id
     * @return mixed
     */
    public function getEffectiveOutwardDeliveryQzData($id){
        return $this->dao->select('id,code,status,closed,productEnrollId,testingRequestId,modifycnccId,dealUser')
            ->from(TABLE_OUTWARDDELIVERY)
            ->where('id')->eq($id)
            ->andWhere('closed')->eq(0)
            ->andWhere('abnormalCode')->eq('')
            ->andWhere('status')->notIN("waitsubmitted,closed,modifycancel") //待提交、已关闭、变更取消 不在联动范围内
            ->fetch();
    }

    public function getChildTypeList($assignType = '', $module = 'outwarddelivery')
    {
        if ($module != 'outwarddelivery') {
            $this->app->LoadLang($module);
        }
        $childTypeList = isset($this->lang->{$module}->childTypeList) ? $this->lang->{$module}->childTypeList['all'] : '[]';
        $childTypeList = json_decode($childTypeList, true);
        $customList = empty($childTypeList[$assignType]) ? array('0' => '') : $childTypeList[$assignType];
        if (!empty($customList)) $customList = array('0' => '') + $customList;
        return $customList;
    }

    //喧喧发信
    public function getXuanxuanTargetUser($obj, $objectType, $objectID, $actionType, $actionID, $actor = '')
    {
        $outwarddelivery = $obj;
        if ($outwarddelivery->issubmit == 'save'){
            return ['isSend'=>'no'];
        }
        $toList = '';
        $users = $this->loadModel('user')->getPairs('noletter');
        $outwarddelivery->reviewers = !empty($outwarddelivery->dealUser) ? $outwarddelivery->dealUser : $outwarddelivery->createdBy;
        if (!empty($outwarddelivery->dealUser)) {
            $dealUsersArray = explode(',', $outwarddelivery->reviewers);
            //所有审核人
            $dealUsers = getArrayValuesByKeys($users, $dealUsersArray);
            $dealUsersStr = implode(',', $dealUsers);
        }
        $outwarddelivery->dealUsersStr = $dealUsersStr;
        $this->app->loadLang('outwarddelivery');

        $action = $this->loadModel('action')->getById($actionID);
        if (!in_array($action->action, $this->config->message->objectTypes['outwarddelivery'])) {
            return;
        }
        $history = $this->action->getHistory($actionID);
        $action->history = isset($history[$actionID]) ? $history[$actionID] : array();

        $externalActionArray = [
            'modifycnccsyncfeedback', 'productenrollsyncfeedback', 'testrequestfeedback', 'testrequesteditfeedback', 'productenrolleditfeedback',
            'modifycncceditstatus', 'modifycnccsyncstatus', 'modifycncceditfeedback'
        ];
        if (in_array($action->action, $externalActionArray)) {
            $isShowExternalStatus = true;
            if ($action->action == 'testrequestfeedback' || $action->action == 'testrequesteditfeedback') {
                $testingrequestObj = $this->loadModel('testingrequest')->getByID($outwarddelivery->testingRequestId);
                $outwarddelivery->externalStatusDesc = zget($this->lang->outwarddelivery->statusList, $testingrequestObj->status);
                $outwarddelivery->externalRejectReason = $testingrequestObj->returnCase;
                if ($outwarddelivery->status != 'testingrequestpass' && $outwarddelivery->status != 'testingrequestreject') {
                    return;
                }
            } else if ($action->action == 'productenrollsyncfeedback' || $action->action == 'productenrolleditfeedback') {
                $this->app->loadLang('productenroll');
                $productenrollObj = $this->loadModel('productenroll')->getByID($outwarddelivery->productEnrollId);
                $outwarddelivery->externalStatusDesc = zget($this->lang->productenroll->statusList, $productenrollObj->status);
                $outwarddelivery->externalRejectReason = $productenrollObj->returnCase;
                if ($outwarddelivery->status != 'productenrollreject' && $outwarddelivery->status != 'productenrollpass') {
                    return;
                }
            } else {
                $modifycnccObj = $this->loadModel('modifycncc')->getByID($outwarddelivery->modifycnccId);
                $outwarddelivery->externalStatusDesc = zget($this->lang->outwarddelivery->statusList, $modifycnccObj->status);
                $outwarddelivery->externalRejectReason = $modifycnccObj->reasonCNCC;
            }
        }

        if (!$outwarddelivery->reviewers) return;
        $toList = $outwarddelivery->reviewers;
        $url = '';
        $subcontent = [];
        $subcontent['headTitle'] = '';
        $subcontent['headSubTitle'] = '';

        $subcontent['count'] = 0;
        $subcontent['id'] = 0;
        $subcontent['parent'] = '';
        $subcontent['parentURL'] = "";
        $subcontent['cardURL'] = $url;
        $subcontent['name'] = '';//消息体 编号后边位置 标题
        //标题
        $title = '';
        $actions = [];
        $confObject = 'setOutwardDeliveryMail';
        $mailConf = isset($this->config->global->$confObject) ? $this->config->global->$confObject : '{"mailTitle":"","variables":[],"mailContent":""}';
        return ['toList' => $toList, 'subcontent' => $subcontent, 'url' => $url, 'title' => $title, 'actions' => $actions,'mailconfig'=>$mailConf];

    }
    /**
     * @param $outwarddelivery 對外交付
     * description：退回时，保存外部失败原因
     */
    public function getHistoryReview($outwarddelivery, $type = 1){
        $historyReview = [];
        $this->app->loadLang('testingrequest');
        $this->app->loadLang('productenroll');
        $this->app->loadLang('modifycncc');
        $failReasonStatus = $this->lang->outwarddelivery->failReasonStatus;
        if ($type == 1 && $outwarddelivery->isNewTestingRequest){
            $testingrequest = $this->loadModel('testingrequest')->getByID($outwarddelivery->testingRequestId);
            $TRlog = $this->loadModel('testingrequest')->getRequestLog($outwarddelivery->testingRequestId);
            if(empty($TRlog)){
                $TRlog = new stdClass();
            }
            $testResult = "";
            if (in_array($testingrequest->status, $failReasonStatus[0])) {
                $testResult = zget($this->lang->testingrequest->statusList, $testingrequest->status, '');
            } elseif (in_array($testingrequest->status, $failReasonStatus[1])) {
                $testResult = $this->lang->outwarddelivery->synSuccess;
            }
            $testReason = "";
            if ($testingrequest->pushStatus and !empty($TRlog) and !empty($TRlog->response) and $TRlog->response->message and in_array($testingrequest->status, $failReasonStatus[2])) {
                $testReason = $TRlog->response->message;
            } elseif ($testingrequest->status == 'qingzongsynfailed') {
                $testReason = $this->lang->outwarddelivery->synFail;
            } else {
                $TRlog->requestDate = '';
            }
            if ($testResult != ''){
                $historyReview[0] = [
                    'reviewNode'        => 0,
                    'reviewUser'        => 'guestjk',
                    'reviewResult'      => $testResult,
                    'reviewFailReason'  => $testReason,
                    'reviewPushDate'    => $TRlog->requestDate,
                    'date'              => helper::now()
                ];
                $flag = $this->isCheckNode($outwarddelivery, $historyReview[0], 0);
                if(!$flag){
                    unset($historyReview[0]);
                }
            }
            $testResult = "";
            if (in_array($testingrequest->status, $failReasonStatus[1])) {
                $testResult = zget($this->lang->testingrequest->statusList, $testingrequest->status);
            }
            $testReason = '';
            if(in_array($testingrequest->status, $failReasonStatus[1])) {
                if ($testingrequest->status == 'testingrequestreject') {
                    $testReason = "打回人：" . $testingrequest->returnPerson . "<br>" . "审批意见：" . $testingrequest->returnCase;
                } else {
                    $testReason = $testingrequest->returnCase;
                }
            }
            $returnDate = "";
            if(strtotime($testingrequest->returnDate) > 0 and in_array($testingrequest->status, $failReasonStatus[1]))
            {
                $returnDate = $testingrequest->returnDate;
            }
            if ($testResult != ''){
                $historyReview[1] = [
                    'reviewNode'        => 1,
                    'reviewUser'        => 'guestcn',
                    'reviewResult'      => $testResult,
                    'reviewFailReason'  => $testReason,
                    'reviewPushDate'    => $returnDate,
                    'date'              => helper::now()
                ];
                $flag = $this->isCheckNode($outwarddelivery, $historyReview[1], 1);
                if(!$flag){
                    unset($historyReview[1]);
                }
            }

        }
        if ($type == 2 && $outwarddelivery->isNewProductEnroll){
            $productenroll = $this->loadModel('productenroll')->getByID($outwarddelivery->productEnrollId);
            $PElog = $this->loadModel('productenroll')->getRequestLog($outwarddelivery->productEnrollId);
            if(empty($PElog)){
                $PElog = new stdClass();
            }
            $productResult = '';
            if (in_array($productenroll->status, array('waitqingzong', 'qingzongsynfailed'))) {
                $productResult = zget($this->lang->productenroll->statusList, $productenroll->status, '');
            } elseif (in_array($productenroll->status, $failReasonStatus[4])) {
                $productResult = $this->lang->outwarddelivery->synSuccess;
            }
            $productReason = '';
            if ($productenroll->pushStatus and !empty($PElog) and !empty($PElog->response) and $PElog->response->message and in_array($productenroll->status, $failReasonStatus[3])) {
                $productReason = $PElog->response->message;
            } elseif ($productenroll->status == 'qingzongsynfailed') {
                $productReason = $this->lang->outwarddelivery->synFail;
            } else {
                $PElog->requestDate = '';
            }
            if ($productResult != ''){
                $historyReview[2] = [
                    'reviewNode'        => 2,
                    'reviewUser'        => 'guestjk',
                    'reviewResult'      => $productResult,
                    'reviewFailReason'  => $productReason,
                    'reviewPushDate'    => $PElog->requestDate,
                    'date'              => helper::now()
                ];
                $flag = $this->isCheckNode($outwarddelivery, $historyReview[2], 2);
                if(!$flag){
                    unset($historyReview[2]);
                }
            }
            $productResult = '';
            if (in_array($productenroll->status, $failReasonStatus[4])) {
                $productResult = zget($this->lang->productenroll->statusList, $productenroll->status);
            }
            $productReason = '';
            if(in_array($productenroll->status, $failReasonStatus[4])) {
                if ($productenroll->status == 'productenrollreject') {
                    $productReason = "打回人：" . $productenroll->returnPerson . "<br>" . "审批意见：" . $productenroll->returnCase;
                } else {
                    $productReason = $productenroll->returnCase;
                }
            }
            $returnDate = '';
            if(strtotime($productenroll->returnDate) > 0 and in_array($productenroll->status, $failReasonStatus[4])) {
                $returnDate = $productenroll->returnDate;
            }
            if ($productResult != ''){
                $historyReview[3] = [
                    'reviewNode'        => 3,
                    'reviewUser'        => 'guestcn',
                    'reviewResult'      => $productResult,
                    'reviewFailReason'  => $productReason,
                    'reviewPushDate'    => $returnDate,
                    'date'              => helper::now()
                ];
                $flag = $this->isCheckNode($outwarddelivery, $historyReview[3], 3);
                if(!$flag){
                    unset($historyReview[3]);
                }
            }
        }
        if ($type == 3 && $outwarddelivery->isNewModifycncc){
            $modifycncc = $this->loadModel('modifycncc')->getByID($outwarddelivery->modifycnccId);
            $MClog = $this->loadModel('modifycncc')->getRequestLog($outwarddelivery->modifycnccId);
            if(empty($MClog)){
                $MClog = new stdClass();
            }
            $modifycnccResult = '';
            if (in_array($modifycncc->status, $failReasonStatus[0])) {
                $modifycnccResult = zget($this->lang->modifycncc->statusList, $modifycncc->status, '');
            } elseif (in_array($modifycncc->status, $failReasonStatus[5])) {
                $modifycnccResult = $this->lang->outwarddelivery->synSuccess;
            }
            $modifycnccReason = '';
            if ($modifycncc->pushStatus and !empty($MClog) and !empty($MClog->response) and $MClog->response->message and in_array($modifycncc->status, $failReasonStatus[6])) {
                $modifycnccReason = $MClog->response->message;
            } elseif ($modifycncc->status == 'qingzongsynfailed') {
                $modifycnccReason = $this->lang->outwarddelivery->synFail;
            } else {
                $MClog->requestDate = '';
            }
            if ($modifycnccResult != ''){
                $historyReview[4] = [
                    'reviewNode'        => 4,
                    'reviewUser'        => 'guestjk',
                    'reviewResult'      => $modifycnccResult,
                    'reviewFailReason'  => $modifycnccReason,
                    'reviewPushDate'    => $MClog->requestDate,
                    'date'              => helper::now()
                ];
                $flag = $this->isCheckNode($outwarddelivery, $historyReview[4], 4);
                if(!$flag){
                    unset($historyReview[4]);
                }
            }
            $modifycnccResult = '';
            if (in_array($modifycncc->status, $failReasonStatus[7])) {
                $modifycnccResult = zget($this->lang->modifycncc->statusList, $modifycncc->status);
                if($modifycncc->status == 'modifyreject'){
                    $modifycnccResult .= "（金信退回总中心，仅供参考）";
                }
            }
            $modifycnccReason = '';
            if (in_array($modifycncc->status, $failReasonStatus[8])) {
                if ($modifycncc->status == 'giteeback') {
                    $modifycnccReason = "打回人：" . $modifycncc->approverName . "<br>审批意见：" . $modifycncc->reasonCNCC;
                } else {
                    $modifycnccReason = $modifycncc->reasonCNCC;
                }
            }
            $feedbackDate = '';
            if (strtotime($modifycncc->feedbackDate) > 0 and in_array($modifycncc->status, $failReasonStatus[8])) {
                $feedbackDate = $modifycncc->feedbackDate;
            }
            if ($modifycnccResult != ''){
                $historyReview[5] = [
                    'reviewNode'        => 5,
                    'reviewUser'        => 'guestcn',
                    'reviewResult'      => $modifycnccResult,
                    'reviewFailReason'  => $modifycnccReason,
                    'reviewPushDate'    => $feedbackDate,
                    'date'              => helper::now()
                ];
                $flag = $this->isCheckNode($outwarddelivery, $historyReview[5], 5);
                if(!$flag){
                    unset($historyReview[5]);
                }
            }

        }

        $reviewFailReason = $outwarddelivery->reviewFailReason;
        if (!empty($historyReview)){
            $reviewFailReason = json_decode($outwarddelivery->reviewFailReason,true);
            $reviewFailReason[$outwarddelivery->version][] = $historyReview;
            $reviewFailReason = json_encode($reviewFailReason);
        }
        return $reviewFailReason;
    }

    /**
     * 推送内部状态
     * @param $externalCode
     * @return mixed
     */
    public function pushModifycnccState($code, $statusValue){
        $this->loadModel('requestlog');

        $pushEnable = $this->config->global->pushModifycnccEnable;
        $requestClass = new stdClass();
        //判断是否开启发送反馈
        if ($pushEnable == 'enable') {
            $url = $this->config->global->modifycnccstateUrl;
            $pushAppId = $this->config->global->pushModifycnccAppId;
            $pushAppSecret = $this->config->global->pushModifycnccAppSecret;
            //请求头
            $headers = array();
            $headers[] = 'App-Id: ' . $pushAppId;
            $headers[] = 'App-Secret: ' . $pushAppSecret;
            //数据体
            $pushData = array();
            //外部单号
            $pushData['changeOrderId']               = $code;

            $pushData['problemJinKeStatus']               = $statusValue;


            //请求类型
            $object = 'modifycncc';
            $objectType = 'pushModifycnccState';
            $method = 'POST';

            $response = '';
            $status = 'fail';
            $extra = '';
            $result = $this->loadModel('requestlog')->http($url, $pushData, $method, 'json', array(), $headers);
            //若清总未返回结果或结果失败，就报错
            if (!empty($result)) {
                $resultData = json_decode($result);
                if ($resultData->code == '200') {
                    $status = 'success';
                }
                $response = $result;
            } else {
                $response = '对方无响应';
            }
            $this->requestlog->saveRequestLog($url, $object, $objectType, 'POST', $pushData, $response, $status, $extra, $code);
        }
    }
    /**
     * 获取变更异常的单子
     */
    public function getModifyAbnormal($isChoice=true){
        $data = $this->dao->select('id,`code`,`desc`')->from(TABLE_MODIFYCNCC)
            ->where('closed')->eq('0')
            ->andWhere('`status`')->in($this->lang->outwarddelivery->reissueArray)
//            ->andWhere('abnormalCode')->eq('')
            ->fetchall();
        $arr = [];
        $outList = $this->dao->select('modifycnccId')->from(TABLE_OUTWARDDELIVERY)->where('abnormalCode')->ne('')->fetchall();
        $outIds = array_column($outList,'modifycnccId');
//        $outIds = [];//异常变更单可以多次被关联
        foreach ($data as $v) {
            if (!in_array($v->id,$outIds)){
                $arr[$v->id] = $v->code;
                if ($v->desc != ''){
                    $arr[$v->id] .= '（'.$v->desc.'）';
                }
            }
        }
        return $arr;
    }
    //编辑关联的变更单
    public function editabnormalorder($modifyId){
        $abnormalList = $this->getModifyAbnormal();
        $id = $_POST['abnormalCode'];
        if ($id == ''){
            dao::$errors['abnormalCode'] =  $this->lang->outwarddelivery->abnormalCodeEmpty;
            return false;
        }
        if (!isset($abnormalList[$id]) || $abnormalList[$id] == ''){
            dao::$errors['abnormalCode'] =  $this->lang->outwarddelivery->checkassociaiton;
            return false;
        }
        $outId = $this->dao->select('id')->from(TABLE_OUTWARDDELIVERY)->where('modifycnccId')->eq($id)->fetch();
        $info = $this->getByID($outId->id);//要关联的异常变更单
        $modify = $this->getByID($modifyId);//当前变更单
        //将原变更单置空
        $this->editModifyAbnormal($modify->modifycnccId,$_POST['abnormalCode']);

        $data = new stdClass();
        $data->problemId = $info->problemId;
        $data->demandId  = $info->demandId;
        $demand = trim($info->demandId,',');
        $problem = trim($info->problemId,',');
        $this->addSecondLineProblem($modifyId, $problem); //问题关联
        $this->addSecondLineDemand($modifyId, $demand);  //需求关联
        $this->dao->update(TABLE_OUTWARDDELIVERY)->data($data)->where('id')->eq($modifyId)->exec();
        $this->dao->update(TABLE_SECONDLINE)
            ->set('deleted = "1" where (objectType = "outwarddelivery" and relationType in ("problem","demand") and objectID = '.$outId.') or (relationType = "outwarddelivery" AND objectType in ("problem","demand") and relationID = '.$outId.') ')
            ->exec();
        return common::createChanges($modify, $data);
    }

    /**
     * @param $id 要关联的变更单id
     * 修改关联变更单重置关系
     */
    public function editModifyAbnormal($modifyId,$id){
//        $this->dao->update(TABLE_OUTWARDDELIVERY)->set('abnormalCode=""')->where('abnormalCode')->eq($modifyId)->exec();
//        $this->dao->update(TABLE_OUTWARDDELIVERY)->set('abnormalCode="'.$modifyId.'"')->where('modifycnccId')->eq($id)->exec();
        $findInSet = '(FIND_IN_SET("'.$modifyId.'",abnormalCode))';
        $oldInfo = $this->dao->select("id,abnormalCode")->from(TABLE_OUTWARDDELIVERY)->where($findInSet)->fetch();
        if ($oldInfo->abnormalCode != ''){
            $arr = array_flip(explode(',',$oldInfo->abnormalCode));
            unset($arr[$modifyId]);
            $arr = array_flip(array_unique($arr));
            $str = implode(',',$arr);
            $this->dao->update(TABLE_OUTWARDDELIVERY)->set('abnormalCode="'.$str.'"')->where('id')->eq($oldInfo->id)->exec();
        }
        $newInfo = $this->dao->select("id,abnormalCode")->from(TABLE_OUTWARDDELIVERY)->where('modifycnccId')->eq($id)->fetch();
        if ($newInfo){
            if (!in_array($modifyId,explode(',',$newInfo->abnormalCode))){
                //一对一
                //$str = $newInfo->abnormalCode . ','.$modifyId;
//                $str = trim($str,',');
                $this->dao->update(TABLE_OUTWARDDELIVERY)->set('abnormalCode="'.$modifyId.'"')->where('id')->eq($newInfo->id)->exec();
            }
        }
    }

    /**
     * @Notes:根据id集合获取对外交付数据
     * @Date: 2023/12/6
     * @Time: 10:16
     * @Interface getByIds
     * @param array $ids
     * @param string $field
     * @return mixed
     */
    public function getByIds($ids = [],$field = '*')
    {
        return  $this->dao->select($field)->from(TABLE_OUTWARDDELIVERY)->where('id')->in($ids)->fetchAll();
    }
    /**
     * @param $search
     * 手机端获取待办列表
     */
    public function getWaitListApi($search='',$orderBy='t1.id_desc')
    {
        $dealUserList = $this->loadModel('common')->getOriginalAuthorizer('outwarddelivery', $this->app->user->account);
        $dealUserList = explode(',', $dealUserList);
        $condition = '';
        if(!empty($dealUserList)){
            $this->loadModel('outwarddelivery');
            foreach ($dealUserList as $dealUser){
                if(strpos($condition, 'FIND_IN_SET') !== false){
                    if($this->app->user->account == $dealUser){
                        $condition .= ' or (FIND_IN_SET("'.$dealUser.'",dealUser))';
                    }else{
                        $condition .= ' or (FIND_IN_SET("'.$dealUser.'",dealUser) and t1.status in(';
                        $i = 0;
                        foreach ($this->lang->outwarddelivery->authorizeStatusList as $key=>$value){
                            if($i == 0){
                                $condition .= "'".$key."'";
                            }else{
                                $condition .= ",'".$key."'";
                            }
                            $i++;
                        }
                        $condition .= '))';
                    }
                }else{
                    if($this->app->user->account == $dealUser){
                        $condition .= ' (FIND_IN_SET("'.$dealUser.'",dealUser))';
                    }else{
                        $condition .= ' (FIND_IN_SET("'.$dealUser.'",dealUser) and t1.status in(';
                        $i = 0;
                        foreach ($this->lang->outwarddelivery->authorizeStatusList as $key=>$value){
                            if($i == 0){
                                $condition .= "'".$key."'";
                            }else{
                                $condition .= ",'".$key."'";
                            }
                            $i++;
                        }
                        $condition .= '))';
                    }
                }
            }
        }
        $list = $this->dao->select('t1.*,t2.type')->from(TABLE_OUTWARDDELIVERY)->alias("t1")
            ->leftjoin(TABLE_MODIFYCNCC)->alias('t2')
            ->on("t1.modifycnccId=t2.id")
//            ->where('status')->ne('deleted')
            ->where('t1.status')->in($this->lang->outwarddelivery->mobileStatus)
            ->andWhere('issubmit')->eq('submit')
            ->beginIF(!empty($condition))->andWhere($condition)->fi()
            ->beginIF($search != '')->andwhere(" ( t1.code like '%$search%' or `outwardDeliveryDesc` like '%$search%' )")->fi()
            ->orderBy($orderBy)
            ->fetchAll('id');
//        $accountList = array();
//        $account     = $this->app->user->account;
//        $this->loadModel('review');
//        $this->loadModel('outwarddelivery');
//        $dataList = [];
//        foreach($list as $key => $value)
//        {
//            $value->dealUser = $this->loadModel('common')->getAuthorizer('outwarddelivery', $value->dealUser,$value->status, $this->lang->outwarddelivery->authorizeStatusList);
//            $reviewersArray = explode(',', $value->dealUser);
//            if( in_array($account,$reviewersArray) === false)
//            {
//                continue;
//            }
//            $dataList[] = $value;
//            $accountList[$value->createdBy] = $value->createdBy;
//        }

        return $list;
    }
    /***
     * @param string $search 关键字搜索
     * @param string $orderBy
     * 手机端获取已办列表接口
     */
    public function getCompletedListApi($pager,$search='',$orderBy='id_desc'){

        $consumeds =  $this->dao->select('id,objectID')->from(TABLE_CONSUMED)
            ->where('objectType')->eq('outwarddelivery')
            ->andWhere('deleted')->eq('0')
            ->andWhere('createdBy')->eq($this->app->user->account)
            ->andWhere('createdDate')->ge('2024-01-01 00:00:00')
            ->fetchAll();
        $consumedID = array_unique(array_column($consumeds,'objectID'));
        $str = '"proxy":"'.$this->app->user->account.'",';
        $reviews = $this->dao->select("objectID")->from(TABLE_REVIEWER)->alias("t1")
            ->leftjoin(TABLE_REVIEWNODE)->alias('t2')
            ->on("t1.node=t2.id")
            ->where( "(reviewer = '".$this->app->user->account."' or t1.extra like '%$str%')")
            ->andWhere('t1.status')->in(['pass','reject'])
            ->andWhere('reviewTime')->ge('2024-01-01 00:00:00')
            ->andWhere('objectType')->eq('outwarddelivery')
            ->fetchAll();
        $reviewID = array_unique(array_column($reviews,'objectID'));

        $ids = array_unique(array_merge($consumedID,$reviewID));

        $modifys = $this->dao->select("t1.id,t1.code,t1.outwardDeliveryDesc,t1.createdDate,t1.createdBy,t2.type")->from(TABLE_OUTWARDDELIVERY)->alias("t1")
            ->leftjoin(TABLE_MODIFYCNCC)->alias('t2')
            ->on("t1.modifycnccId=t2.id")
            ->where('t1.id')->in($ids)
            ->andWhere('t1.status')->ne('deleted')
            ->beginIF($search != '')->andwhere(" ( t1.code like '%$search%' or t1.outwardDeliveryDesc like '%$search%' )")->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchall();

        return $modifys;
    }
    // 表单提交时
    public function checkPostParams(){

    }
    /**
     * @param int $id 部门id
     * 修改上海分公司节点名称
     */
    public function resetNodeAndReviewerName($id=0){
        $depts = $this->loadModel('dept')->getRecurveSubDeptIds(30);
        if ((in_array($this->app->user->dept,$depts) &&  in_array($this->app->getMethodName(),['create','copy'])) || (in_array($id,$depts) && !in_array($this->app->getMethodName(),['create','copy']))){
            $this->lang->outwarddelivery->reviewerList['5'] = '上海分公司领导';
            $this->lang->outwarddelivery->reviewerList['6'] = '上海分公司总经理';

            $this->lang->outwarddelivery->reviewNodeList['5'] = '上海分公司领导';
            $this->lang->outwarddelivery->reviewNodeList['6'] = '上海分公司总经理';

            $this->lang->modifycncc->reviewerList['5'] = '上海分公司领导';
            $this->lang->modifycncc->reviewerList['6'] = '上海分公司总经理';

            $this->lang->modifycncc->reviewNodeList['5'] = '上海分公司领导';
            $this->lang->modifycncc->reviewNodeList['6'] = '上海分公司总经理';
        }

    }
}