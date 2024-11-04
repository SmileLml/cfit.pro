<?php
include '../../control.php';
class myMobileApi extends mobileapi
{
    public function creditviewapi()
    {
        $errMsg = $this->checkInput();
        $this->app->loadLang('credit');
        if (!empty($errMsg)) {
            $this->loadModel('mobileapi')->response('fail', implode(',', $errMsg), array(),  0, 203,'creditViewApi');
        }
        $info = $this->loadModel('credit')->getByID($_POST['id']);
        $users =  $this->loadModel('user')->getPairs('noletter');
        $depts = $this->loadModel('dept')->getOptionMenu();

        $info->summary = !empty($info->summary) ? html_entity_decode(str_replace("\n","<br/>",$info->summary)) : '';
        $info->desc = !empty($info->desc) ? html_entity_decode(str_replace("\n","<br/>",$info->desc)) : '';
        $info->techniqueCheck = !empty($info->techniqueCheck) ? html_entity_decode(str_replace("\n","<br/>",$info->techniqueCheck)) : '';
        $info->feasibilityAnalysis = !empty($info->feasibilityAnalysis) ? html_entity_decode(str_replace("\n","<br/>",$info->feasibilityAnalysis)) : '';
        $info->productAffect = !empty($info->productAffect) ? html_entity_decode(str_replace("\n","<br/>",$info->productAffect)) : '';
        $info->businessAffect = !empty($info->businessAffect) ? html_entity_decode(str_replace("\n","<br/>",$info->businessAffect)) : '';
        $info->svnUrl = !empty($info->svnUrl) ? html_entity_decode(str_replace("\n","<br/>",$info->svnUrl)) : '';
        $info->onLineFile = !empty($info->onLineFile) ? html_entity_decode(str_replace("\n","<br/>",$info->onLineFile)) : '';
        $info->status_text = zget($this->lang->credit->statusList, $info->status, '');
        $info->dealUser_text = zmget($users, $info->dealUsers, '');
        $info->riskAnalysisEmergencyHandle = json_decode($info->riskAnalysisEmergencyHandle);
        //所属系统列表
        $appList = [];
        if($info->appIds){
            $appIds = array_filter(explode(',', $info->appIds));
            $appExWhere = " id In ( ".implode(',', $appIds).")";
            $appList =  $this->loadModel('application')->getPairs(0, $appExWhere);
        }
        $info->appIds_text = zmget($appList, $info->appIds, '', '<br/>');
        //所属产品
        $productList = [];
        if($info->productIds){
            $productIds = array_filter(explode(',', $info->productIds));
            $productList = $this->loadModel('product')->getProductNamesByIds($productIds);
        }
        $info->productIds_text = zmget($productList, $info->productIds, '', '<br/>');
        $info->implementationForm_text = zget($this->lang->credit->implementationFormList, $info->implementationForm);
        //项目
        $projectList = [];
        if($info->projectPlanId){
            $projectPlanId = array_filter(explode(',', $info->projectPlanId));
            $exWhere = " project In ( ".implode(',', $projectPlanId).")";
            $projectList = $this->loadModel('projectplan')->getAllProjects(false, $exWhere);
        }
        $info->projectPlanId_text = zmget($projectList, $info->projectPlanId, '', '<br/>');
        //问题单
        $problemList = [];
        if($info->problemIds){
            $problemIds = array_filter(explode(',', $info->problemIds));
            $exWhere = " id In ( ".implode(',', $problemIds).")";
            $problemList =  $this->loadModel('problem')->getPairsAbstract('noclosed', $exWhere);
        }
        $info->problemIds_text = zmget($problemList, $info->problemIds, '', '<br/>');
        //任务单
        $secondorderList = [];
        if($info->secondorderIds){
            $secondorderIds = array_filter(explode(',', $info->secondorderIds));
            $exWhere = " id In ( ".implode(',', $secondorderIds).")";
            $secondorderList =  array('' => '') + $this->loadModel('secondorder')->getNameList($exWhere);
        }
        $info->secondorderIds_text = zmget($secondorderList, $info->secondorderIds, '', '<br/>');
        $info->secondorderCancelLinkage_text = zget($this->lang->credit->secondorderCancelLinkageList, $info->secondorderCancelLinkage);
        //需求单
        $demandList = [];
        if($info->demandIds){
            $demandIds = array_filter(explode(',', $info->demandIds));
            $exWhere = " id In ( ".implode(',', $demandIds).")";
            $demandList = $this->loadModel('demand')->getPairsTitle('noclosed', $exWhere);
        }
        $info->demandIds_text = zmget($demandList, $info->demandIds, '', '<br/>');
        $deptInfo = $this->loadModel('dept')->getByID($info->createdDept);
        $info->deptInfo_text = zget($deptInfo, 'name', '');
        $info->createdBy_text = zget($users, $info->createdBy, '');
        $info->editedBy_text = zget($users, $info->editedBy, '');

        $info->level_text = zget($this->lang->credit->levelList, $info->level, '');
        $info->changeNode_text = zmget($this->lang->credit->changeNodeList, $info->changeNode, '');
        $info->mode_text = zmget($this->lang->credit->modeList, $info->mode, '');
        $info->type_text = zmget($this->lang->credit->typeList, $info->type, '');
        $info->changeSource_text = zmget($this->lang->credit->changeSourceList, $info->changeSource, '');
        $info->executeMode_text = zmget($this->lang->credit->executeModeList, $info->executeMode, '');
        $info->emergencyType_text = zget($this->lang->credit->emergencyTypeList, $info->emergencyType, '');
        $info->isBusinessAffect_text = zget($this->lang->credit->isBusinessAffectList, $info->isBusinessAffect, '');
        $info->planBeginTime = $info->planBeginTime == '0000-00-00 00:00:00'? '':$info->planBeginTime;
        $info->planEndTime = $info->planEndTime == '0000-00-00 00:00:00'? '':$info->planEndTime;
        $info->editedDate = $info->editedDate == '0000-00-00 00:00:00'? '':$info->editedDate;


        $this->loadModel('mobileapi')->response('success', '', $info ,  0, 200,'creditViewApi');
    }
    private function checkInput()
    {
        $errMsg = [];
        if(!isset($_POST['id'])){
            $errMsg[] = "缺少『id』参数";
            return $errMsg;
        }

        if( isset($_POST['id']) && !$_POST['id']){
            $errMsg[] = '『征信交付单ID』不能为空';
            return $errMsg;
        }
        if(isset($_POST['id']) && $_POST['id']){
            if(!preg_match("/^[1-9][0-9]*$/",$_POST['id'])){
                $errMsg = '征信交付单ID只能正整数';
                return $errMsg;
            }
        }
        return $errMsg;
    }
}
