<?php
include '../../control.php';
class myMobileApi extends mobileapi
{

    /**
     * 金信-生产变更-评审
     */
    public function infoReviewApi()
    {
        $this->app->loadLang('info');
        $errMsg = $this->checkInput();
        if (!empty($errMsg)) {
            $this->loadModel('mobileapi')->response('fail', implode(',', $errMsg), array(),  0, 203,'infoReviewApi');
        }
        $id = $_POST['id'];
        $version = $_POST['version'];
        $reviewStage = $_POST['reviewStage'];
        $info = $this->loadModel('info')->getByID($id);
        //检查是否允许审核
        $res = $this->loadModel('info')->checkAllowReview($info, $version, $reviewStage, $this->app->user->account);

        if (!$res['result']){
            $this->loadModel('mobileapi')->response('fail', $res['message'], array(),  0, 203,'infoReviewApi');
        }
        $this->loadModel('info')->review($id);

        if(dao::isError())
        {
            $this->loadModel('mobileapi')->response('fail', implode(',', dao::getError()), array(),  0, 203,'infoReviewApi');
        }

        $action = 'cmconfirmed' == $info->status || 'gmsuccess' == $info->status ? 'deal' : 'review';
        $actionID = $this->loadModel('action')->create('info', $id, $action, $this->post->comment,'mobile');
        $this->loadModel('mobileapi')->response('success', '', $this->lang->saveSuccess ,  0, 200,'infoReviewApi');
    }
    private function checkInput()
    {
        $errMsg = [];
        if(!isset($_POST['id'])){
            $errMsg[] = "缺少『id』参数";
            return $errMsg;
        }
        if(!isset($_POST['version']) || (int)$_POST['version'] <= 0){
            $errMsg[] = "缺少『version』参数";
            return $errMsg;
        }
        if(!isset($_POST['reviewStage']) || (int)$_POST['reviewStage'] <= 0){
            $errMsg[] = "缺少『reviewStage』参数";
            return $errMsg;
        }

        if( isset($_POST['id']) && !$_POST['id']){
            $errMsg[] = '『数据获取单ID』不能为空';
            return $errMsg;
        }
        if(isset($_POST['id']) && $_POST['id']){
            if(!preg_match("/^[1-9][0-9]*$/",$_POST['id'])){
                $errMsg[] = '数据获取单ID只能正整数';
                return $errMsg;
            }
        }
        return $errMsg;
    }
}
