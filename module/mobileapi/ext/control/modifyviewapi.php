<?php
include '../../control.php';
class myMobileApi extends mobileapi
{

    /**
     * 金信-生产变更详情页
     */
    public function modifyViewApi()
    {
        $this->app->loadLang('outwarddelivery');
        $this->app->loadLang('release');
        $this->app->loadLang('projectrelease');
        $this->app->loadLang('testingrequest');
        $this->app->loadLang('productenroll');
        $this->app->loadLang('modifycncc');
        $this->app->loadLang('application');
        $this->app->loadLang('file');
        $this->app->loadLang('api');
        $this->loadModel('outwarddelivery');
        $errMsg = $this->checkInput();
        $this->app->loadLang('modifycncc');
        if (!empty($errMsg)) {
            $this->loadModel('mobileapi')->response('fail', implode(',', $errMsg), array(),  0, 203,'modifyViewApi');
        }
        $info = $this->loadModel('modify')->getByID($_POST['id']);
        $info->level = zget($this->lang->modifycncc->levelList, $info->level, '');
        $info->status_text    = $this->lang->modify->statusList[$info->status];
        $info->appsInfo                    = (Object)$this->outwarddelivery->getAppInfo(explode(',',$info->app));
        $info->CBPInfo                     = $this->outwarddelivery->getCBPInfo($info->CBPprojectId);
        $info->releaseInfoList         = $this->outwarddelivery->getReleaseInfoInIds($info->release);
        $allLines                = $this->loadModel('productline')->getPairs();
        $allProductNames         = $this->loadModel('product')->getNamePairs();
        $allProductCodes         = $this->loadModel('product')->getCodePairs();
        $depts                   = $this->loadModel('dept')->getDeptPairs();
        $users                   = $this->loadModel('user')->getPairs('noletter');
        $problems                = $this->loadModel('problem')->getPairsByIds(explode(',', $info->problemId));
        $demand                  = $this->loadModel('demand')->getPairsByIds(explode(',', $info->demandId));
        $requirement             = $this->loadModel('requirement')->getPairsByIds(explode(',', $info->requirementId));
        $reviewReportList = array('' => '') + $this->loadModel('review')->getPairs('','');

        $projects                = array('' => '') + $this->loadModel('projectplan')->getProject($info->implementationForm == 'second');//更新获取所属项目的方法
        if ($info->appsInfo) {
            foreach ($info->appsInfo as $appID => $appInfo) {
                $apps[] = $appInfo->name;
                $isPayments[] = zget($this->lang->application->isPaymentList, $appInfo->isPayment, '');
                $teams[] = zget($this->lang->application->teamList, $appInfo->team, '');
            }
        }
        $productCode = array();
        $productName = array();
        if ($info->productId) {
            foreach (explode(',', $info->productId) as $productID) {
                if ($productID) {
                    $productCode[] = $allProductCodes[$productID];
                    $productName[] = $allProductNames[$productID];
                }
            }
        }
        $projectNames = [];
        foreach (explode(',', $info->projectPlanId) as $project) {
            if ($project) {
                $projectNames[] = zget($projects, $project, '');
            }
        }
        $secondorder = $this->loadModel('secondorder')->getPairsByIds(explode(',', $info->secondorderId));
        $secondorders = [];
        foreach (explode(',', $info->secondorderId) as $secondorderId){
            if ($secondorderId and $secondorder->$secondorderId['code']) {
                $secondorders[] = $secondorder->$secondorderId['code'];
            }
        }
        $problemsArray = [];
        foreach (explode(',', $info->problemId) as $objectID){
            if ($objectID and $problems->$objectID['code']) {
                $problemsArray[] = $problems->$objectID['code'];
            }
        }
        $demandArray = [];
        foreach (explode(',', $info->demandId) as $objectID){
            if ($objectID and $demand->$objectID['code']) {
                $demandArray[] = $demand->$objectID['code'];
            }
        }
        $requirementArray = [];
        foreach (explode(',', $info->requirementId) as $objectID){
            if ($objectID and $requirement->$objectID['code']) {
                $requirementArray[] = $requirement->$objectID['code'];
            }
        }
        $info->testingrequest = '';
        if($info->testingRequestId){
            $testingrequest          = $this->loadModel('testingrequest')->getByID($info->testingRequestId);
            $info->testingrequest = $testingrequest->code;
        }
        $changeNodes = [];
        foreach (explode(',', $info->node) as $node) {
            if (empty($node)) continue;
            $changeNodes[] = zget($this->lang->modify->nodeList, $node, '');
        }
        $info->feasibilityAnalysis_text = '';
        if (!empty($info->feasibilityAnalysis)) {
            $feasibilityAnalysisInfo = array();
            $feasibilityAnalysises = explode(',', $info->feasibilityAnalysis);
            foreach ($feasibilityAnalysises as $feasibilityAnalysis) {
                $feasibilityAnalysisInfo[] = zget($this->lang->modifycncc->feasibilityAnalysisList, $feasibilityAnalysis, '');
            }
            $info->feasibilityAnalysis_text = trim(implode(',', $feasibilityAnalysisInfo), ',');
        }
        $reviewReportArray = [];
        foreach (explode(',',$info->reviewReport) as $value){
            $reviewReportArray[] = zget($reviewReportList, $value, '');
        }

        $info->productName_text         = implode(PHP_EOL, array_unique($productName));
        $info->reviewReport_text        = implode(PHP_EOL, array_unique($reviewReportArray));
        $info->productCode_text         = implode(PHP_EOL, array_unique($productCode));
        $info->apps_text                = implode(PHP_EOL,array_unique($apps));
        $info->isPayments_text          = implode(PHP_EOL,array_unique($isPayments));
        $info->teams_text               = implode(PHP_EOL,array_unique($teams));
        $info->projects_text            = implode(PHP_EOL,array_unique($projectNames));
        $info->implementationForm_text               = zget($this->lang->outwarddelivery->implementationFormList, $info->implementationForm, '');
        $info->secondorder_text         = implode(PHP_EOL,$secondorders);
        $info->problems_text            = implode(PHP_EOL,$problemsArray);
        $info->demand_text              = implode(PHP_EOL,$demandArray);
        $info->requirement_text         = implode(PHP_EOL,$requirementArray);
        $info->isDiskDelivery_text      = zget($this->lang->modify->isDiskDeliveryList,$info->isDiskDelivery);
        $info->node_text                = implode(PHP_EOL,$changeNodes);
        $info->isReview_text            = $this->lang->modify->isReviewList[$info->isReview];
        $info->mode_text                = $this->lang->modifycncc->modeList[$info->mode];
        $info->classify_text            = $this->lang->modifycncc->classifyList[$info->classify];
        $info->changeSource_text        = $this->lang->modifycncc->changeSourceList[$info->changeSource];
        $info->changeStage_text         = $this->lang->modifycncc->changeStageList[$info->changeStage];
        $info->implementModality_text   = $this->lang->modifycncc->implementModalityList[$info->implementModality];
        $info->type_text                = zget($this->lang->modifycncc->typeList, $info->type, '');
        $info->isBusinessCooperate_text = $this->lang->modifycncc->isBusinessCooperateList[$info->isBusinessCooperate];
        $info->isBusinessJudge_text     = $this->lang->modifycncc->isBusinessJudgeList[$info->isBusinessJudge];
        $info->isBusinessAffect_text    = $this->lang->modifycncc->isBusinessAffectList[$info->isBusinessAffect];
        $info->property_text            = zget($this->lang->modifycncc->propertyList, $info->property, '');
        $info->cooperateDepName_text    = zget($this->lang->modify->cooperateDepNameListList, $info->cooperateDepNameList, '');
        $info->target_text              = !empty($info->target) ? html_entity_decode(str_replace("\n","<br/>",$info->target)) : '';
        $info->reason_text              = !empty($info->reason) ? html_entity_decode(str_replace("\n","<br/>",$info->reason)) : '';
        $info->step_text                = !empty($info->step) ? html_entity_decode(str_replace("\n","<br/>",$info->step)) : '';
        $info->techniqueCheck_text      = !empty($info->techniqueCheck) ? html_entity_decode(str_replace("\n","<br/>",$info->techniqueCheck)) : '';
        $info->changeContentAndMethod_text = !empty($info->changeContentAndMethod) ? html_entity_decode(str_replace("\n","<br/>",$info->changeContentAndMethod)) : '';
        $info->checkList_text              = !empty($info->checkList) ? html_entity_decode(str_replace("\n","<br/>",$info->checkList)) : '';
        $info->checkList_text             = !empty($info->checkList) ? html_entity_decode($info->checkList) : '';
        $info->preChange_text           = !empty($info->preChange) ? html_entity_decode(str_replace("\n","<br/>",$info->preChange)) : '';
        $info->postChange_text          = !empty($info->postChange) ? html_entity_decode(str_replace("\n","<br/>",$info->postChange)) : '';
        $info->synImplement_text        = !empty($info->synImplement) ? html_entity_decode(str_replace("\n","<br/>",$info->synImplement)) : '';
        $info->pilotChange_text         = !empty($info->pilotChange) ? html_entity_decode(str_replace("\n","<br/>",$info->pilotChange)) : '';
        $info->promotionChange_text     = !empty($info->promotionChange) ? html_entity_decode(str_replace("\n","<br/>",$info->promotionChange)) : '';
        $info->businessCooperateContent_text     = !empty($info->businessCooperateContent) ? html_entity_decode(str_replace("\n","<br/>",$info->businessCooperateContent)) : '';
        $info->judgePlan_text           = !empty($info->judgePlan) ? html_entity_decode(str_replace("\n","<br/>",$info->judgePlan)) : '';
        $info->risk_text                = !empty($info->risk) ? html_entity_decode(str_replace("\n","<br/>",$info->risk)) : '';
        $info->judgeDep_text            = zget($this->lang->modify->judgeDepList, $info->judgeDep, '');
        if ($info->backspaceExpectedEndTime == '0000-00-00 00:00:00') $info->backspaceExpectedEndTime = '';
        if ($info->backspaceExpectedStartTime == '0000-00-00 00:00:00') $info->backspaceExpectedStartTime = '';
        //展示是否需要系统部审批选项
        $info->isNeedSystemShow = 0;
        $info->release = explode(',', trim($info->release,','));
        $info->checkSystemPass = $this->loadModel('build')->checkSystemPass($info->release);
        if($info->reviewStage == 2 && strpos($info->requiredReviewNode,'3')){
            $info->isNeedSystemShow = 1;
        }
        $info->isShowMaterial = 0;
        if(in_array(1,explode(',',$info->app))){
            $info->isShowMaterial = 1;
        }
        $info->materialIsReview   = zget($this->lang->modify->materialIsReviewList,$info->materialIsReview,'');
        $info->materialReviewUser = zget($users,$info->materialReviewUser,'');

        // 手否后补流程、实际交付时间
        $info->actualDeliveryTime = $info->isMakeAmends == 'yes' ? $info->actualDeliveryTime : '';
        $info->isMakeAmends_txt  = zget($this->lang->modify->isMakeAmendsList,$info->isMakeAmends,'');

        $this->loadModel('mobileapi')->response('success', '', $info ,  0, 200,'modifyViewApi');
    }
    private function checkInput()
    {
        $errMsg = [];
        if(!isset($_POST['id'])){
            $errMsg[] = "缺少『id』参数";
            return $errMsg;
        }

        if( isset($_POST['id']) && !$_POST['id']){
            $errMsg[] = '『生产变更单ID』不能为空';
            return $errMsg;
        }
        if(isset($_POST['id']) && $_POST['id']){
            if(!preg_match("/^[1-9][0-9]*$/",$_POST['id'])){
                $errMsg = '生产变更单ID只能正整数';
                return $errMsg;
            }
        }
        return $errMsg;
    }
}
