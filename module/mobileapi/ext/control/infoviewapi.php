<?php
include '../../control.php';
class myMobileApi extends mobileapi
{

    /**
     * 金信-数据获取详情页
     */
    public function infoViewApi()
    {
        $this->app->loadLang('info');
        $errMsg = $this->checkInput();
        if (!empty($errMsg)) {
            $this->loadModel('mobileapi')->response('fail', implode(',', $errMsg), array(),  0, 203,'infoViewApi');
        }
        $apps     = $this->loadModel('application')->getapplicationInfo();
        $objects  = $this->loadModel('secondline')->getByID($_POST['id'], 'gain');
        $users    = $this->loadModel('user')->getPairs('noletter');
        $info     = $this->loadModel('info')->getByID($_POST['id']);
        $secondorder = $this->loadModel('secondorder')->getPairsByIds(explode(',', $info->secondorderId));
        $projects = array('' => '') + $this->loadModel('projectplan')->getAliveProjects($info->fixType == 'second');//更新获取所属项目的方法
        $dataManagementID = $this->dao->select('id')->from(TABLE_DATAUSE)->where('code')->eq($info->dataManagementCode)->fetch('id');
        $info->dataManagementID = $dataManagementID;
        $info->fetchResult    = zget($this->lang->info->fetchResultList, $info->fetchResult, '');
//        $info->checkList      = html_entity_decode(strip_tags(str_replace("<br />","\n",strval(htmlspecialchars_decode($info->checkList,ENT_QUOTES)))));
        // 数据类型
        $info->type_text      = zget($this->lang->info->typeList, $info->type, '');
        // 实现方式
        $info->fixType_text   = zget($this->lang->info->fixTypeList, $info->fixType, '');

        $classifyList = explode(',', $info->classify);
        $as = [];
        foreach($classifyList as $classify)
        {
            if(!$classify) continue;
            $as[] = zget($this->lang->info->techList, $classify, '');
        }
        // 数据类别
        $info->classify      = implode('，',$as);
        // 流程状态
        $info->status_text   = zget($this->lang->info->statusList, $info->status, '');
        $as = [];
        foreach(explode(',', $info->app) as $app)
        {
            if(!$app) continue;
            $as[] = zget($apps, $app , "",$apps[$app]->name);
        }
        $app = implode('，', $as);
        // 受影响业务系统
        $info->apps          = implode(PHP_EOL, $as);
        $as = [];
        foreach(explode(',', $info->app) as $app)
        {
            if(!$app) continue;
            $as[] = zget($apps, $app, "",zget($this->lang->application->isPaymentList, $apps[$app]->isPayment, ''));
        }
        // 系统分类
        $info->applicationtype = implode('，', $as);
        // 关联问题单
        $problems = [];
        foreach($objects['problem'] as $objectID => $object){
            $problems[] = $object;
        }
        $info->problems      = implode(PHP_EOL,$problems);
        // 关联需求条目
        $demands = [];
        foreach($objects['demand'] as $objectID => $object){
            $demands[] = $object->code;
        }
        $info->demands = implode(PHP_EOL,$demands);

        // 关联任务工单
        $secondorders = [];
        foreach (explode(',', $info->secondorderId) as $secondorderId){
            if ($secondorderId and $secondorder->$secondorderId['code']) {
                $secondorders[] = $secondorder->$secondorderId['code'];
            }
        }
        $info->secondorders = implode(PHP_EOL,$secondorders);
        $arr = [];
        foreach(explode(',', $info->project) as $project){
            $arr[] = zget($projects, $project, '');
        };
        $info->createdBy   = zget($users, $info->createdBy, '');
        $info->editedBy    = zget($users, $info->editedBy, '');
        $info->project     = htmlspecialchars_decode(implode(PHP_EOL,$arr));
        // 支持人员
        $supplys = [];
        if(!empty($info->supply)){
            foreach(explode(',', $info->supply) as $supply){
                $supplys[] = zget($users, $supply);
            }
        }
        $info->supply = trim(implode('，',$supplys),'，');
        $revertReason = []; // 退回原因
        $RevertReasonChild = []; // 退回原因子项
        if($info->revertReason){
            foreach(json_decode($info->revertReason) as $item){
                $revertReason[] = $item->RevertDate.' '.zget($this->lang->info->revertReasonList, $item->RevertReason, '');
            }
            $childTypeList = isset($this->lang->info->childTypeList) ? $this->lang->info->childTypeList['all'] : '[]';
            $childTypeList = json_decode($childTypeList, true);
            foreach(json_decode($info->revertReason) as $item){
                if (isset($item->RevertReasonChild) && $item->RevertReasonChild != ''){
                    $RevertReasonChild[] = $item->RevertDate.' '.$childTypeList[$item->RevertReason][$item->RevertReasonChild];
                }
            }
        }
        $info->revertReason        = implode(PHP_EOL,$revertReason);
        $info->RevertReasonChild   = implode(PHP_EOL,$RevertReasonChild);

        $info->isJinke_text        = zget($this->lang->info->isJinkeList, $info->isJinke);
        if ($info->isJinke == 1){
            $info->desensitizationType  =zget($this->lang->info->desensitizationTypeList, $info->desensitizationType);

            if($info->isDeadline == '1'){
                $info->useTime = '长期';
            }else{
                $info->useTime = substr($info->deadline,0,10);
            }
        }
        $info->flag                = $this->loadModel('info')->checkAllowReview($info, $info->version, $info->reviewStage, $this->app->user->account);;
        $this->loadModel('mobileapi')->response('success', '', $info ,  0, 200,'infoViewApi');
    }
    private function checkInput()
    {
        $errMsg = [];
        if(!isset($_POST['id'])){
            $errMsg[] = "缺少『id』参数";
            return $errMsg;
        }

        if( isset($_POST['id']) && !$_POST['id']){
            $errMsg[] = '『数据获取单ID』不能为空';
            return $errMsg;
        }
        if(isset($_POST['id']) && $_POST['id']){
            if(!preg_match("/^[1-9][0-9]*$/",$_POST['id'])){
                $errMsg = '数据获取单ID只能正整数';
                return $errMsg;
            }
        }
        return $errMsg;
    }
}
