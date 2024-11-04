<?php
include '../../control.php';
class myMobileApi extends mobileapi
{
    public function infoqzViewApi()
    {
        $errMsg = $this->checkInput();
        $this->app->loadLang('infoqz');
        if (!empty($errMsg)) {
            $this->loadModel('mobileapi')->response('fail', implode(',', $errMsg), array(),  0, 203,'infoqzViewApi');
        }
        $info = $this->loadModel('infoqz')->getByID($_POST['id']);
        $users =  $this->loadModel('user')->getPairs('noletter');
        $depts = $this->loadModel('dept')->getOptionMenu();
        $projects = $this->loadModel('projectplan')->getAliveProjects($info->fixType == 'second');
        $objects = $this->loadModel('secondline')->getByID($_POST['id'], 'gainQz');
        $apps = $this->loadModel('application')->getapplicationNameCodePairsWithPartition();

        $info->externalStatus_text       = zget(array_flip($this->lang->infoqz->externalStatusMapArray), $info->externalStatus, '');
        $info->status_text = zget($this->lang->infoqz->statusList, $info->status, '');
        $dealUsersStr = '';
        $dealUsers = $info->dealUsers;
        if($dealUsers){
            $dealUsersArray = explode(',', $dealUsers);
            //所有审核人
            $dealUsers    = getArrayValuesByKeys($users, $dealUsersArray);
            $dealUsersStr = implode(',', $dealUsers);
        }
        $info->dealUser_text = $dealUsersStr;
        $info->externalStatus_text = zget(array_flip($this->lang->infoqz->externalStatusMapArray), $info->externalStatus, '');
        $info->type_text = zget($this->lang->infoqz->typeList, $info->type, '');
        $info->classify_text = zmget($this->lang->infoqz->businessList+$this->lang->infoqz->techList, $info->classify, '');
        $info->isNPC_text = zget( $this->lang->infoqz->isNPCList, $info->isNPC, '');
        $info->node_text = zmget($this->lang->infoqz->gainNodeNPCList + $this->lang->infoqz->gainNodeCNCCList, $info->node);
        $info->gainType_text = zget($this->lang->infoqz->gainTypeList, $info->gainType, '');
        $info->createdDept_text = zget($depts, $info->createdDept, '');
        $info->systemType_text = zget($this->lang->infoqz->systemTypeList, $info->systemType,'');
        $info->fixType_text = zget($this->lang->infoqz->fixTypeList, $info->fixType, '');
        $info->project_text = zmget($projects, $info->project, '');
        $info->desc = htmlspecialchars_decode($info->desc);
        $info->reason = htmlspecialchars_decode($info->reason);
        $info->purpose = htmlspecialchars_decode($info->purpose);
        $info->test = htmlspecialchars_decode($info->test);
        $info->content = htmlspecialchars_decode($info->content);
        $info->operation = htmlspecialchars_decode($info->operation);
        $info->step = htmlspecialchars_decode($info->step);
        $info->desensitization = htmlspecialchars_decode($info->desensitization);
        $info->desensitizeProcess = htmlspecialchars_decode($info->desensitizeProcess);
        if(!empty($objects['problem'])){
            $problemCodeArray = array();
            foreach($objects['problem'] as $objectID => $object){
                array_push($problemCodeArray, $object);
            }
            $info->problem_text = implode('<br>', $problemCodeArray);
        }
        if(!empty($objects['demand'])){
            $demandCodeArray = array();
            foreach($objects['demand'] as $objectID => $object){
                array_push($demandCodeArray, $object->code);
            }
            $info->demand_text = implode('<br>',$demandCodeArray);
        }
        if(!empty($objects['secondorder'])){
            $secondorderArray = array();
            foreach($objects['secondorder'] as $objectID => $object){
                array_push($secondorderArray, $object);
            }
            $info->secondorder_text = implode('<br>',$secondorderArray);
        }
        $info->isTest_text = zget($this->lang->infoqz->isTestList, $info->isTest, '');
        $info->createdBy_text = zget($users, $info->createdBy, '');
        $info->editedBy_text = zget($users, $info->editedBy, '');
        $info->supply_text = zmget($users, $info->supply, '');
        if($info->revertReason){
            $revertReasonArray = array();
            foreach(json_decode($info->revertReason) as $item){
                array_push($revertReasonArray, $item->RevertDate.' '.zget($this->lang->infoqz->revertReasonList, $item->RevertReason, ''));
            }
            $info->revertReason_text = implode('<br/>', $revertReasonArray);
        }
        if($info->revertReason){
            $childTypeList = isset($this->lang->infoqz->childTypeList) ? $this->lang->infoqz->childTypeList['all'] : '[]';
            $childTypeList = json_decode($childTypeList, true);
            $revertReasonChildArray = array();
            foreach(json_decode($info->revertReason) as $item){
                if (isset($item->RevertReasonChild) && $item->RevertReasonChild != ''){
                    array_push($revertReasonChildArray, $item->RevertDate.' '.$childTypeList[$item->RevertReason][$item->RevertReasonChild]);
                }
            }
            $info->revertReasonChild_text = implode('<br/>', $revertReasonChildArray);
        }
        $info->desc = !empty($info->desc) ? html_entity_decode(str_replace("\n","<br/>",$info->desc)) : '';
        $info->reason = !empty($info->reason) ? html_entity_decode(str_replace("\n","<br/>",$info->reason)) : '';
        $info->purpose = !empty($info->purpose) ? html_entity_decode(str_replace("\n","<br/>",$info->purpose)) : '';
        $info->test = !empty($info->test) ? html_entity_decode(str_replace("\n","<br/>",$info->test)) : '';
        $info->content = !empty($info->content) ? html_entity_decode(str_replace("\n","<br/>",$info->content)) : '';
        $info->operation = !empty($info->operation) ? html_entity_decode(str_replace("\n","<br/>",$info->operation)) : '';
        $info->step = !empty($info->step) ? html_entity_decode(str_replace("\n","<br/>",$info->step)) : '';
        $info->desensitization = !empty($info->desensitization) ? html_entity_decode(str_replace("\n","<br/>",$info->desensitization)) : '';

        $info->isJinke_text = zget($this->lang->infoqz->isJinkeList, $info->isJinke);
        $info->desensitizationType_text = zget($this->lang->infoqz->desensitizationTypeList, $info->desensitizationType);
        $info->dataCollectApplyCompany_text = zget($this->lang->infoqz->demandUnitTypeList, $info->dataCollectApplyCompany, '');
        $demandUnitDeptList = $this->infoqz->getDemandUnitDeptList();
        if (in_array($info->dataCollectApplyCompany,[1,2,3])){
            $arr = [];
            foreach (explode(',',$info->demandUnitOrDep) as $item) {
                $arr[] = zget($demandUnitDeptList,$item,'');
            }
            $info->demandUnitOrDep = implode(',',$arr);
        }
        $as = [];
        foreach(explode(',', $info->app) as $app){
            if(!$app) continue;
            $as[] = zget($apps, $app , "");
        }
        $info->app_text = implode(',', $as);
        $as = [];
        foreach(explode(',', $info->dataSystem) as $app)
        {
            if(!$app) continue;
            $as[] = zget($apps, $app , "");
        }
        $info->dataSystem_text = implode(',', $as);
        if($info->isDeadline == '1'){
            $info->deadline_text = '长期';
        }else{
            $info->deadline_text = substr($info->deadline,0,10);
        }
        $this->loadModel('mobileapi')->response('success', '', $info ,  0, 200,'infoqzViewApi');
    }
    private function checkInput()
    {
        $errMsg = [];
        if(!isset($_POST['id'])){
            $errMsg[] = "缺少『id』参数";
            return $errMsg;
        }

        if( isset($_POST['id']) && !$_POST['id']){
            $errMsg[] = '『清总数据获取单ID』不能为空';
            return $errMsg;
        }
        if(isset($_POST['id']) && $_POST['id']){
            if(!preg_match("/^[1-9][0-9]*$/",$_POST['id'])){
                $errMsg = '清总数据获取单ID只能正整数';
                return $errMsg;
            }
        }
        return $errMsg;
    }
}
