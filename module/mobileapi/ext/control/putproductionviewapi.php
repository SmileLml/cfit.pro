<?php
include '../../control.php';
class myMobileApi extends mobileapi
{

    /**
     * 对外移交详情页
     */
    public function putproductionViewApi()
    {
        $errMsg = $this->checkInput();
        $this->app->loadLang('putproduction');
        if (!empty($errMsg)) {
            $this->loadModel('mobileapi')->response('fail', implode(',', $errMsg), array(),  0, 203,'putproductionViewApi');
        }
        $info = $this->loadModel('putproduction')->getByID($_POST['id']);
        $outsideProjectList = $this->loadModel('outsideplan')->getPairs();
        $inProjectList = $this->loadModel('projectplan')->getPlanNameListByOutID($info->outsidePlanId);
        $apps =  $this->loadModel('application')->getApplicationCodePairsSyncJinx();
        $appList =  array_column($apps, 'name', 'id');
        $products = $this->loadModel('product')->getNamePairs();
        $demandList = [];
        if($info->demandId){
            //关联需求条目
            $demandIds = array_filter(explode(',', $info->demandId));
            $exWhere = " id In ( ".implode(',', $demandIds).")";
            $demandList = $this->loadModel('demand')->getPairsTitle('noclosed', $exWhere);
        }
        $users =  $this->loadModel('user')->getPairs('noletter');

        $info->stage_text       = zmget($this->lang->putproduction->stageList, $info->stage, '');
        $info->property_text    = zmget($this->lang->putproduction->propertyList, $info->property, '');
        $info->isReview_text   = zget($this->lang->putproduction->isReviewList, $info->isReview,'');
        $info->level_text       = zget($this->lang->putproduction->levelList, $info->level,'');
        $info->isPutCentralCloud_text       = zget($this->lang->putproduction->isPutCentralCloudList, $info->isPutCentralCloud,'');
        $info->dataCenter_text    = zmget($this->lang->putproduction->dataCenterList, $info->dataCenter,'');
        $info->isBusinessCoopera_text   = zget($this->lang->putproduction->isBusinessCooperaList, $info->isBusinessCoopera,'');
        $info->isBusinessAffect_text       = zget($this->lang->putproduction->isBusinessAffectList, $info->isBusinessAffect,'');
        $info->status_text = zget($this->lang->putproduction->statusList, $info->status,'');
        $info->outsidePlanId_text = zget($outsideProjectList, $info->outsidePlanId,'无');
        $info->inProjectIds_text = zget($inProjectList, $info->inProjectIds,'无');
        $remoteFileListArray = explode(',',$info->remoteFileList);
        $info->fileList = implode('<br>',$remoteFileListArray);
        $info->app_text = zmget($appList, $info->app,'');
        $info->productId_text = zmget($products, $info->productId,'');
        $info->demandId_text = zget($demandList, $info->demandId,'');
        $info->createdBy_text = zget($users, $info->createdBy,'');
        $firstStageInfo = $info->firstStageInfo;
        $info->firstStagePid_text = zget($firstStageInfo, 'code');
        $info->dealUser_text = zmget($users, $info->dealUser,'');
        if($info->isIncludeSecondStage){
            $sftPathArray = array();
            foreach($info->releases as $release){
                array_push($sftPathArray, $release->path);
            }
            $info->sftPathList = implode('<br>',$sftPathArray);
        }else{
            $sftPathArray = json_decode($info->sftpPath);
            $info->sftPathList = implode('<br>',$sftPathArray);
        }


        $info->desc = !empty($info->desc) ? html_entity_decode(str_replace("\n","<br/>",$info->desc)) : '';
        $info->reviewComment = !empty($info->reviewComment) ? html_entity_decode(str_replace("\n","<br/>",$info->reviewComment)) : '';
        $info->businessCooperaContent = !empty($info->businessCooperaContent) ? html_entity_decode(str_replace("\n","<br/>",$info->businessCooperaContent)) : '';
        $info->businessAffect = !empty($info->businessAffect) ? html_entity_decode(str_replace("\n","<br/>",$info->businessAffect)) : '';
        $info->remark = !empty($info->remark) ? html_entity_decode(str_replace("\n","<br/>",$info->remark)) : '';

        $this->loadModel('mobileapi')->response('success', '', $info ,  0, 200,'putproductionViewApi');
    }
    private function checkInput()
    {
        $errMsg = [];
        if(!isset($_POST['id'])){
            $errMsg[] = "缺少『id』参数";
            return $errMsg;
        }

        if( isset($_POST['id']) && !$_POST['id']){
            $errMsg[] = '『投产移交单ID』不能为空';
            return $errMsg;
        }
        if(isset($_POST['id']) && $_POST['id']){
            if(!preg_match("/^[1-9][0-9]*$/",$_POST['id'])){
                $errMsg = '投产移交单ID只能正整数';
                return $errMsg;
            }
        }
        return $errMsg;
    }
}
