<?php
include '../../control.php';
class myMobileApi extends mobileapi
{

    /**
     * 对外移交详情页
     */
    public function sectransferViewApi()
    {
        $errMsg = $this->checkInput();
        $this->app->loadLang('sectransfer');
        $this->app->loadLang('opinion');
        if (!empty($errMsg)) {
            $this->loadModel('mobileapi')->response('fail', implode(',', $errMsg), array(),  0, 203,'sectransferViewApi');
        }
        $apps = $this->loadModel('application')->getapplicationNameCodePairs();

        $info = $this->loadModel('sectransfer')->getByID($_POST['id']);
        $inprojectList = $this->sectransfer->getInprojects();
        $outprojectList =array('' => '') + $this->loadModel('outsideplan')->getPairs();

        $info->status_text       = zget($this->lang->sectransfer->statusListName, $info->status, '');
        $info->app_text          = zget($apps, $info->app, '');
        $info->department_text   = zget($this->lang->application->teamList, $info->department,'');
        $info->jftype_text       = zget($this->lang->sectransfer->transferTypeList, $info->jftype,'');
        $info->transitionPhase_text       = zget($this->lang->sectransfer->transitionPhase, $info->transitionPhase,'');
        $info->inproject_text    = zget($inprojectList, $info->inproject,'');
        $info->outproject_text   = zget($outprojectList, $info->outproject,'');
        $info->iscode_text       = zget($this->lang->sectransfer->oldOrNotList, $info->iscode,'');
        $info->lastTransfer_text = zget($this->lang->sectransfer->orNotList, $info->lastTransfer,'');
        $info->externalRecipient_text = zget($this->lang->opinion->unionList, $info->externalRecipient,'');
        $info->externalStatus_text = zget($this->lang->sectransfer->externalStatusList, $info->externalStatus,'');
        $info->secondorder = '';
        $info->externalCode = '';
        if($info->secondorderId != 0){
            $secondorder = $this->loadModel('secondorder')->getById($info->secondorderId);
            $info->secondorder = $secondorder->code;
            $info->externalCode = $secondorder->externalCode;
        }
        $remoteFileListArray = [];
        if(!empty($info->remoteFileList)){
            foreach (explode(',' , $info->remoteFileList) as $value){
                $json = '{"str":"'.str_replace('#U', '\u',$value).'"}';
                $arr = json_decode($json,true);
                $remoteFileListArray[] = $arr['str'];
            }
        }
        $info->remoteFileList = implode(PHP_EOL,$remoteFileListArray);

        $data = ['info'=>$info,'transfersubTypeList'=>$this->lang->sectransfer->transfersubTypeList,'qszzx'=>$this->lang->sectransfer->qszzx];

        $this->loadModel('mobileapi')->response('success', '', $data ,  0, 200,'sectransferViewApi');
    }
    private function checkInput()
    {
        $errMsg = [];
        if(!isset($_POST['id'])){
            $errMsg[] = "缺少『id』参数";
            return $errMsg;
        }

        if( isset($_POST['id']) && !$_POST['id']){
            $errMsg[] = '『移交单ID』不能为空';
            return $errMsg;
        }
        if(isset($_POST['id']) && $_POST['id']){
            if(!preg_match("/^[1-9][0-9]*$/",$_POST['id'])){
                $errMsg = '移交单ID只能正整数';
                return $errMsg;
            }
        }
        return $errMsg;
    }
}
