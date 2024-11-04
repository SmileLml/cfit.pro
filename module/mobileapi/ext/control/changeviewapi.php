<?php
include '../../control.php';
class myMobileApi extends mobileapi
{
    public function changeviewapi()
    {
        $errMsg = $this->checkInput();
        $this->app->loadLang('change');
        if (!empty($errMsg)) {
            $this->loadModel('mobileapi')->response('fail', implode(',', $errMsg), array(),  0, 203,'changeViewApi');
        }
        $info = $this->loadModel('change')->getByID($_POST['id']);
        $users =  $this->loadModel('user')->getPairs('noletter');
        $depts = $this->loadModel('dept')->getOptionMenu();

        if(strlen($info->code) > 15){
            $info->code_text = mb_substr($info->code, 0, 15).'...';
        }else{
            $info->code_text = $info->code;
        }

        $info->reason = !empty($info->reason) ? html_entity_decode(str_replace("\n","<br/>",$info->reason)) : '';
        $info->content = !empty($info->content) ? html_entity_decode(str_replace("\n","<br/>",$info->content)) : '';
        $info->effect = !empty($info->effect) ? html_entity_decode(str_replace("\n","<br/>",$info->effect)) : '';

        $info->level_text = zget($this->lang->change->levelList, $info->level, '');
        $info->type_text = zget($this->lang->change->typeList, $info->type, '');
        $info->category_text = zget($this->lang->change->categoryList, $info->category, '');
        $info->subCategory_text = zmget($this->lang->change->subCategoryList, $info->subCategory, '');
        $info->isInteriorPro_text = zget($this->lang->change->isInteriorProList, $info->isInteriorPro, '');
        $info->isMasterPro_text = zget($this->lang->change->isMasterProList, $info->isMasterPro, '');
        $info->isSlavePro_text = zget($this->lang->change->isSlaveProList, $info->isSlavePro, '');
        $info->status_text = zget($this->lang->change->statusList, $info->status, '');
        $projects = array('' => '') + $this->loadModel('project')->getPairs('noclosed');
        $info->project_text = zmget($projects, $info->project, '');
        $info->baseLineCondition_text = zget($this->lang->change->condition, $info->baseLineCondition, '');
        $info->createdBy_text = zget($users, $info->createdBy, '');
        $info->mailUsers_text = zmget($users, $info->mailUsers);

        //评审归档信息
        $info->archiveList = $this->loadModel('archive')->getArchiveList('change', $_POST['id']);
        //评审打基线信息
        $baseLineList = $this->loadModel('change')->getBaseLineInfo($info);
        if(!empty($baseLineList)){
            $this->app->loadLang('cm');
            $baseLineTypelist = $this->lang->cm->typeList;
            foreach ($baseLineList as &$val){
                $val->baseLineType_text = zget($baseLineTypelist, $val->baseLineType);
            }
        }
        $info->baseLineList = $baseLineList;

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
            $errMsg[] = '『项目变更单ID』不能为空';
            return $errMsg;
        }
        if(isset($_POST['id']) && $_POST['id']){
            if(!preg_match("/^[1-9][0-9]*$/",$_POST['id'])){
                $errMsg = '项目变更单ID只能正整数';
                return $errMsg;
            }
        }
        return $errMsg;
    }
}
