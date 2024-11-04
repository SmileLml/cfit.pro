<?php
include '../../control.php';
class myMobileApi extends mobileapi
{

    /**
     * 清总-对外交付待办列表
     */
    public function outwardViewApi()
    {
        if (!empty($errMsg)) {
            $this->loadModel('mobileapi')->response('fail', implode(',', $errMsg), array(),  0, 203,'outwardViewApi');
        }
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        $this->app->loadLang('release');
        $this->app->loadLang('projectrelease');
        $this->app->loadLang('testingrequest');
        $this->app->loadLang('productenroll');
        $this->app->loadLang('modifycncc');
        $this->app->loadLang('application');
        $this->app->loadLang('file');
        $this->app->loadLang('api');
        $this->app->loadLang('defect');
        $this->app->loadLang('modify');

        unset($this->lang->modifycncc->implementModalityNewList[0]);
        $this->lang->modifycncc->implementModalityList = $this->lang->modifycncc->implementModalityList + $this->lang->modifycncc->implementModalityNewList;

        $allInfo          = $this->loadModel('outwarddelivery')->getAllInfo($id);
        $outwarddelivery  = (Object)$allInfo['outwardDelivery'];
        $testingrequest          = $allInfo['testingRequest'];
        $productenroll           = $allInfo['productEnroll'];
        $modifycncc              = $allInfo['modifycncc'];
        $reviewReportList        = $allInfo['reviewReportList'];
        $allInfo['statusList'] = $this->lang->outwarddelivery->statusList;
        $depts = $this->loadModel('dept')->getTopPairs();
        $users = $this->loadModel('user')->getPairs('noletter');



        $projects                = array('' => '') + $this->loadModel('projectplan')->getProject($outwarddelivery->implementationForm == 'second');//更新获取所属项目的方法
        $allLines                = $this->loadModel('productline')->getPairs();
        $allProductNames         = $this->loadModel('product')->getNamePairs();
        $allProductCodes         = $this->loadModel('product')->getCodePairs();
        $outwarddelivery->CBPInfo            = $this->outwarddelivery->getCBPInfo($outwarddelivery->CBPprojectId);
        $problems                = $this->loadModel('problem')->getPairsByIds(explode(',', $outwarddelivery->problemId));
        $demand                  = $this->loadModel('demand')->getPairsByIds(explode(',', $outwarddelivery->demandId));
        $requirement             = $this->loadModel('requirement')->getPairsByIds(explode(',', $outwarddelivery->requirementId));
        $relations               = $this->outwarddelivery->getAllRelations($id);



        $outwarddelivery->currentReview_txt        = zget($this->lang->outwarddelivery->currentReviewList, $outwarddelivery->currentReview, '');
        $deliveryType = array();
        if ($outwarddelivery->isNewTestingRequest) {
            $deliveryType[] = $this->lang->outwarddelivery->testingrequest;
        }
        if ($outwarddelivery->isNewProductEnroll) {
            $deliveryType[] = $this->lang->outwarddelivery->productenroll;
        }
        if ($outwarddelivery->isNewModifycncc) {
            $deliveryType[] = $this->lang->outwarddelivery->modifycncc;
        }
        // 交付类型
        $outwarddelivery->deliveryType   = implode('，', $deliveryType);
        // 所属系统
        $outwarddelivery->appsInfo           = (Object)$this->outwarddelivery->getAppInfo(explode(',',$outwarddelivery->app));

        if ($outwarddelivery->appsInfo) {
            foreach ($outwarddelivery->appsInfo as $appID => $appInfo) {
                $apps[] = $appInfo->name;
                $isPayments[] = zget($this->lang->application->isPaymentList, $appInfo->isPayment, '');
                $teams[] = zget($this->lang->application->teamList, $appInfo->team, '');
            }
        }
        //产品名称 产品编号
        if ($outwarddelivery->productId) {
            foreach (explode(',', $outwarddelivery->productId) as $productID) {
                if ($productID) {
                    $productCode[] = $allProductCodes[$productID];
                    $productName[] = $allProductNames[$productID];
                }
            }
        }
        //产品线
        if ($outwarddelivery->productLine) {
            foreach (explode(',', $outwarddelivery->productLine) as $line) {
                if ($line) {
                    $productLine[] = $allLines[$line];
                }
            }
        }
        //所属项目
        $projectNames = [];
        foreach (explode(',', $outwarddelivery->projectPlanId) as $project) {
            if ($project) {
                $projectNames[] = zget($projects, $project, '');
            }
        }
        $outwarddelivery->cbp_txt = '';
        if ($outwarddelivery->CBPprojectId) {
            $outwarddelivery->cbp_txt = $outwarddelivery->CBPInfo[0]->code . '（' . $outwarddelivery->CBPInfo[0]->name . '）';
        }
        $secondorder = $this->loadModel('secondorder')->getPairsByIds(explode(',', $outwarddelivery->secondorderId));
        $secondorders = [];
        foreach (explode(',', $outwarddelivery->secondorderId) as $secondorderId){
            if ($secondorderId and $secondorder->$secondorderId['code']) {
                $secondorders[] = $secondorder->$secondorderId['code'];
            }
        }
        $problemsArray = [];
        foreach (explode(',', $outwarddelivery->problemId) as $objectID){
            if ($objectID and $problems->$objectID['code']) {
                $problemsArray[] = $problems->$objectID['code'];
            }
        }
        $demandArray = [];
        foreach (explode(',', $outwarddelivery->demandId) as $objectID){
            if ($objectID and $demand->$objectID['code']) {
                $demandArray[] = $demand->$objectID['code'];
            }
        }
        $requirementArray = [];
        foreach (explode(',', $outwarddelivery->requirementId) as $objectID){
            if ($objectID and $requirement->$objectID['code']) {
                $requirementArray[] = $requirement->$objectID['code'];
            }
        }
        $outwarddelivery->msg = '';
        if(!empty($relations) && !empty($relations['outwardDelivery'])){
            $outwarddeliveryMsg = '';
            foreach ($relations['outwardDelivery'] as $object) {
                $outwarddeliveryMsg .= $object['code'].',';

                if ($object['children']['testingRequestId']) {
                    $outwarddeliveryMsg .= $object['children']['testingRequestCode'].',';
                }
                if ($object['children']['productEnrollId']) {
                    $outwarddeliveryMsg .= $object['children']['productEnrollCode'];
                }
                if ($object['children']['modifycnccId']) {
                    $outwarddeliveryMsg .= $object['children']['modifycnccCode'];
                }
            }
            $outwarddelivery->msg = trim($outwarddeliveryMsg, ',');
        }
        //方案评审报告
        $reviewReportArray = [];
        foreach (explode(',',$modifycncc->reviewReport) as $value){
            $reviewReportArray[] = zget($reviewReportList, $value, '');
        }
        $outwarddelivery->relationTest = '';
        if ($outwarddelivery->isNewTestingRequest == 0 and $outwarddelivery->testingRequestId and $testingrequest->code){
            $outwarddelivery->relationTest = $testingrequest->code;
        }
        $outwarddelivery->relationProduct = '';
        if ($outwarddelivery->isNewProductEnroll == 0 and $outwarddelivery->productEnrollId and $productenroll->code){
            $outwarddelivery->relationProduct = $productenroll->code;
        }
        $outwarddelivery->relationModifycncc = '';
        if ($outwarddelivery->isNewModifycncc == 0 and $outwarddelivery->modifycnccId and $modifycncc->code){
            $outwarddelivery->relationModifycncc = $modifycncc->code;
        }
        $revertReasonArray = [];
        $revertReasonChildArray = [];
        if($outwarddelivery->revertReason){
            $childTypeList = isset($this->lang->outwarddelivery->childTypeList) ? $this->lang->outwarddelivery->childTypeList['all'] : '[]';
            $childTypeList = json_decode($childTypeList, true);
            foreach(json_decode($outwarddelivery->revertReason) as $item){
                $revertReasonArray[] = zget($this->lang->outwarddelivery->revertReasonList, $item->RevertReason, '');
                if (isset($item->RevertReasonChild) && $item->RevertReasonChild != ''){
                    $revertReasonChildArray[] = $childTypeList[$item->RevertReason][$item->RevertReasonChild];
                }
            }
        }
        $outwarddelivery->productLine_text         = implode(PHP_EOL,array_unique($productLine));
        $outwarddelivery->productName_text         = implode(PHP_EOL,array_unique($productName));
        $outwarddelivery->productCode_text         = implode(PHP_EOL,array_unique($productCode));
        $outwarddelivery->isPayments_text          = implode(PHP_EOL,array_unique($isPayments));
        $outwarddelivery->teams_text               = implode(PHP_EOL,array_unique($teams));
        $outwarddelivery->apps_text                = implode(PHP_EOL,array_unique($apps));
        $outwarddelivery->project_text             = implode(PHP_EOL,array_unique($projectNames));
        $outwarddelivery->implementationForm_text  = zget($this->lang->outwarddelivery->implementationFormList, $outwarddelivery->implementationForm, '');
        $outwarddelivery->secondorder_text         = implode(PHP_EOL,array_unique($secondorders));
        $outwarddelivery->demand_text              = implode(PHP_EOL,array_unique($demandArray));
        $outwarddelivery->problem_text             = implode(PHP_EOL,array_unique($problemsArray));
        $outwarddelivery->requirement_text         = implode(PHP_EOL,array_unique($requirementArray));
        $outwarddelivery->deptName                 = zget($depts, $outwarddelivery->createdDept, '');
        $outwarddelivery->createUser               = zget($users, $outwarddelivery->createdBy, '');
        $outwarddelivery->closeUser                = zget($users, $outwarddelivery->closedBy, '');
        $outwarddelivery->closeReason_text         = zget($this->lang->outwarddelivery->closedReasonList, $outwarddelivery->closedReason);
        $outwarddelivery->revertUser               = zget($users, $outwarddelivery->revertBy, '');
        $outwarddelivery->revertReason_text        = implode(PHP_EOL,array_unique($revertReasonArray));
        $outwarddelivery->revertReasonChild_text   = implode(PHP_EOL,array_unique($revertReasonChildArray));


        //测试申请
        //验收类型
        $testingrequest->acceptanceTestType_text      = zget($this->lang->testingrequest->acceptanceTestTypeList, $testingrequest->acceptanceTestType);
        //是否为集中测试
        $testingrequest->isCentralizedTest_text       = zget($this->lang->testingrequest->isCentralizedTestList, $testingrequest->isCentralizedTest);
        $testingrequest->env                          = html_entity_decode($testingrequest->env);
        $testingrequest->content                      = html_entity_decode($testingrequest->content);
        $testingrequest->reviewResult                 = zget($this->lang->testingrequest->cardStatusList, $testingrequest->cardStatus, '');
        if (strtotime($testingrequest->returnDate) > 0){
            $testingrequest->returnDate;
        }else{
            $testingrequest->returnDate = '';
        }
        $testFiles = [];
        if ($testingrequest->files){
            foreach ($testingrequest->files as $file) {
                $testFiles[] = $file->title;
            }
        }
        $testingrequest->filesNames              = implode(PHP_EOL,$testFiles);

        $productenroll->isPlan_text              = zget($this->lang->productenroll->isPlanList, $productenroll->isPlan, '');
        $productenroll->checkDepartment_text     = zget($this->lang->productenroll->checkDepartmentList, $productenroll->checkDepartment, '');
        $productenroll->result_text              = zget($this->lang->productenroll->resultList, $productenroll->result, '');
        $productenroll->installationNode_text    = zget($this->lang->productenroll->installNodeList, $productenroll->installationNode, '');
        $productenroll->softwareProductPatch_text   = zget($this->lang->productenroll->softwareProductPatchList, $productenroll->softwareProductPatch, '');
        $productenroll->softwareCopyrightRegistration_text   = zget($this->lang->productenroll->softwareCopyrightRegistrationList, $productenroll->softwareCopyrightRegistration, '');
        $productenroll->platform_text            = zget($this->lang->productenroll->appList, $productenroll->platform, '');
        $productenroll->introductionToFunctionsAndUses       = html_entity_decode($productenroll->introductionToFunctionsAndUses);
        $productenroll->remark                   = html_entity_decode($productenroll->remark);
        $productenroll->cardStatus_text          = zget($this->lang->productenroll->cardStatusList, $productenroll->cardStatus, '');
        if (strtotime($productenroll->returnDate) > 0){
            $productenroll->returnDate;
        }else{
            $productenroll->returnDate = '';
        }
        $changeNodes = [];
        foreach (explode(',', $modifycncc->node) as $node) {
            if (empty($node)) continue;
            $changeNodes[] = zget($this->lang->modifycncc->nodeList, $node, '');
        }
        $modifycncc->reviewReport_text        = implode(PHP_EOL, array_unique($reviewReportArray));
        $modifycncc->type_text                = zget($this->lang->modifycncc->typeList, $modifycncc->type, '');
        $modifycncc->level                    = zget($this->lang->modifycncc->levelList, $modifycncc->level, '');
        $modifycncc->operationType_text       = zget($this->lang->modifycncc->operationTypeList, $modifycncc->operationType, '');
        $modifycncc->node_text                = implode(PHP_EOL,$changeNodes);
        $modifycncc->isReview_text            = $this->lang->modifycncc->isReviewList[$modifycncc->isReview];
        $modifycncc->mode_text                = $this->lang->modifycncc->modeList[$modifycncc->mode];
        $modifycncc->classify_text            = $this->lang->modifycncc->classifyList[$modifycncc->classify];
        $modifycncc->changeSource_text        = $this->lang->modifycncc->changeSourceList[$modifycncc->changeSource];
        $modifycncc->changeStage_text         = $this->lang->modifycncc->changeStageList[$modifycncc->changeStage];
        $modifycncc->implementModality_text   = $this->lang->modifycncc->implementModalityList[$modifycncc->implementModality];
        $modifycncc->changeForm_text          = $this->lang->modifycncc->changeFormList[$modifycncc->changeForm];
        $modifycncc->automationTools_text     = $this->lang->modifycncc->automationToolsList[$modifycncc->automationTools];
        $modifycncc->mode_text                = $this->lang->modifycncc->modeList[$modifycncc->mode];

        $modifycncc->isBusinessCooperate_text = $this->lang->modifycncc->isBusinessCooperateList[$modifycncc->isBusinessCooperate];
        $modifycncc->isBusinessJudge_text     = $this->lang->modifycncc->isBusinessJudgeList[$modifycncc->isBusinessJudge];
        $modifycncc->isBusinessAffect_text    = $this->lang->modifycncc->isBusinessAffectList[$modifycncc->isBusinessAffect];
        $modifycncc->property_text            = zget($this->lang->modifycncc->propertyList, $modifycncc->property, '');
        $modifycncc->cooperateDepName_text    = zget($this->lang->modifycncc->cooperateDepNameListList, $modifycncc->cooperateDepNameList, '');
        $modifycncc->target_text              = !empty($modifycncc->target) ? html_entity_decode($modifycncc->target) : '';
        $modifycncc->reason_text              = !empty($modifycncc->reason) ? html_entity_decode($modifycncc->reason) : '';
        $modifycncc->step_text                = !empty($modifycncc->step) ? html_entity_decode($modifycncc->step) : '';
        $modifycncc->techniqueCheck_text      = !empty($modifycncc->techniqueCheck) ? html_entity_decode($modifycncc->techniqueCheck) : '';
        $modifycncc->changeContentAndMethod_text = !empty($modifycncc->changeContentAndMethod) ? html_entity_decode($modifycncc->changeContentAndMethod) : '';
        $modifycncc->checkList_text              = !empty($modifycncc->checkList) ? html_entity_decode($modifycncc->checkList) : '';
        $modifycncc->checkList_text             = !empty($modifycncc->checkList) ? html_entity_decode($modifycncc->checkList) : '';
        $modifycncc->preChange_text           = !empty($modifycncc->preChange) ? html_entity_decode($modifycncc->preChange) : '';
        $modifycncc->postChange_text          = !empty($modifycncc->postChange) ? html_entity_decode($modifycncc->postChange) : '';
        $modifycncc->synImplement_text        = !empty($modifycncc->synImplement) ? html_entity_decode($modifycncc->synImplement) : '';
        $modifycncc->pilotChange_text         = !empty($modifycncc->pilotChange) ? html_entity_decode($modifycncc->pilotChange) : '';
        $modifycncc->promotionChange_text     = !empty($modifycncc->promotionChange) ? html_entity_decode($modifycncc->promotionChange) : '';
        $modifycncc->businessCooperateContent_text     = !empty($modifycncc->businessCooperateContent) ? html_entity_decode($modifycncc->businessCooperateContent) : '';
        $modifycncc->judgePlan_text           = !empty($modifycncc->judgePlan) ? html_entity_decode($modifycncc->judgePlan) : '';
        $modifycncc->risk_text                = !empty($modifycncc->risk) ? html_entity_decode($modifycncc->risk) : '';
        $modifycncc->judgeDep_text            = zget($this->lang->modifycncc->judgeDepList, $modifycncc->judgeDep, '');
        $modifycncc->test                     = html_entity_decode($modifycncc->test);
        $modifycncc->applyReasonOutWindow                     = html_entity_decode($modifycncc->applyReasonOutWindow);
        $modifycncc->keyGuaranteePeriodApplyReason                     = html_entity_decode($modifycncc->keyGuaranteePeriodApplyReason);
        if ($modifycncc->backspaceExpectedEndTime == '0000-00-00 00:00:00') $modifycncc->backspaceExpectedEndTime = '';
        if ($modifycncc->backspaceExpectedStartTime == '0000-00-00 00:00:00') $modifycncc->backspaceExpectedStartTime = '';

        $modifycncc->app_text = '';
        $mapps = [];
        if ($modifycncc->app) {
            foreach ($modifycncc->appsInfo as $appID => $appInfo) {
                $partitionMsg = $appInfo->name;
                if (!empty($appInfo->partition[0])) {
                    $partitionMsg .= ' (';
                    foreach ($appInfo->partition as $partition) {
                        $partitionMsg .= $partition . ' 分区,';
                    }
                    $partitionMsg = trim($partitionMsg, ', ') . ' )';
                }
                $mapps[] = $partitionMsg ;
            }
        }
        $modifycncc->feasibilityAnalysis_text = '';
        if (!empty($modifycncc->feasibilityAnalysis)) {
            $feasibilityAnalysisInfo = array();
            $feasibilityAnalysises = explode(',', $modifycncc->feasibilityAnalysis);
            foreach ($feasibilityAnalysises as $feasibilityAnalysis) {
                $feasibilityAnalysisInfo[] = zget($this->lang->modifycncc->feasibilityAnalysisList, $feasibilityAnalysis, '');
            }
            $modifycncc->feasibilityAnalysis_text = trim(implode(',', $feasibilityAnalysisInfo), ',');
        }
        $modifycnccList = $this->dao->select('id,concat(concat(concat(code,"（"),substring(`desc`,1,255)),"）")')->from(TABLE_MODIFYCNCC)->fetchPairs();
        $modifyRelation = [];
        if(!empty($modifycncc) || count($modifycncc->relation)>0){
            foreach($modifycncc->relation as $key => $line){
                $modifyRelation[] = $this->lang->modifycncc->relateTypeList[$line[0]].'-'.$modifycnccList[$line[1]];
            }
        }
        //判断是否经过系统部审核
        $outwarddelivery->release = explode(',', trim($outwarddelivery->release,','));
        $outwarddelivery->checkSystemPass = $this->loadModel('build')->checkSystemPass($outwarddelivery->release);

        $outwarddelivery->isNeedSystemShow = 0;
        if($outwarddelivery->reviewStage == 2 and $outwarddelivery->isNewModifycncc == 1 and ((in_array(3,explode(',',$outwarddelivery->requiredReviewNode)) == 1 and $outwarddelivery->isOutsideReject == '1') or $outwarddelivery->isOutsideReject == '0')){
            $outwarddelivery->isNeedSystemShow = 1;
        }
        $modifycncc->controlTableFile = html_entity_decode(str_replace("\n",PHP_EOL,$modifycncc->controlTableFile));
        $modifycncc->controlTableFile = html_entity_decode(str_replace("\n",PHP_EOL,$modifycncc->controlTableSteps));
        $modifycncc->modifyRelation_text = implode(PHP_EOL,$modifyRelation);
        $modifycncc->app_text = implode(PHP_EOL,$mapps);
        $modifycncc->benchmarkVerificationType_text = zget($this->lang->modifycncc->benchmarkVerificationTypeList, $modifycncc->benchmarkVerificationType, '');
        $modifycncc->verificationResults_text       = html_entity_decode($modifycncc->verificationResults);
        $modifycncc->status_text                    = zget($this->lang->modifycncc->changeStatusList, $modifycncc->changeStatus, '');
        $modifycncc->urgentSource                   = zget($this->lang->modifycncc->urgentSourceList,$modifycncc->urgentSource,'');
        // 手否后补流程、实际交付时间
        $modifycncc->actualDeliveryTime = $modifycncc->isMakeAmends == 'yes' ? $modifycncc->actualDeliveryTime : '';
        $modifycncc->isMakeAmends_txt  = zget($this->lang->modify->isMakeAmendsList,$modifycncc->isMakeAmends,'');

        $allInfo['outwardDelivery']       = $outwarddelivery;
        $allInfo['testingRequest']        = $testingrequest;
        $allInfo['productEnroll']         = $productenroll;
        $allInfo['modifycncc']            = $modifycncc;
        $this->loadModel('mobileapi')->response('success', '', $allInfo ,  0, 200,'outwardViewApi');

    }



    private function checkInput()
    {
        $errMsg = [];
        if(!isset($_POST['id'])){
            $errMsg[] = "缺少『id』参数";
            return $errMsg;
        }

        if( isset($_POST['id']) && !$_POST['id']){
            $errMsg[] = '『对外交付ID』不能为空';
            return $errMsg;
        }
        if(isset($_POST['id']) && $_POST['id']){
            if(!preg_match("/^[1-9][0-9]*$/",$_POST['id'])){
                $errMsg = '对外交付ID只能正整数';
                return $errMsg;
            }
        }
        return $errMsg;
    }
}
