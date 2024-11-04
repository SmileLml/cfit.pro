<?php
include '../../control.php';
class myMobileApi extends mobileapi
{

    /**
     * 金信-生产变更-评审
     */
    public function modifyReviewApi()
    {
        $users = $this->loadModel('user')->getPairs('noclosed');
        $this->app->loadLang('modify');
        $errMsg = $this->checkInput();
        if (!empty($errMsg)) {
            $this->loadModel('mobileapi')->response('fail', implode(',', $errMsg), array(),  0, 203,'modifyReviewApi');
        }
        $id = $_POST['id'];
        $version = $_POST['version'];
        $reviewStage = $_POST['reviewStage'];
        $modify = $this->loadModel('modify')->getByID($id);
        //检查是否允许审核
        $res = $this->loadModel('modify')->checkAllowReview($modify, $version, $reviewStage, $this->app->user->account);

        if (!$res['result']){
            $this->loadModel('mobileapi')->response('fail', $res['message'], array(),  0, 203,'modifyReviewApi');
        }

        if($res['result']){
            $this->loadModel('demand')->isSingleUsage($modify->demandId, 'modify', $id);
            if(dao::isError()){
                $this->loadModel('mobileapi')->response('fail', implode(',', dao::getError()), array(),  0, 203,'modifyReviewApi');
            }
        }
        $modify = $this->modify->getByID($id);
        $this->loadModel('modify')->review($id);

        if(dao::isError())
        {
            $this->loadModel('mobileapi')->response('fail', implode(',', dao::getError()), array(),  0, 203,'modifyReviewApi');
        }

        if($res['reviewAuthorize'] == $this->app->user->account){
            if(!isset($_POST['cancelStatus'])) {
                $action = 'gmsuccess' == $modify->status || 'cmconfirmed' == $modify->status ? 'deal' : 'review';
                $this->loadModel('action')->create('modify', $id, $action, $this->post->comment,'mobile');
            }else{
                $this->loadModel('action')->create('modify', $id, 'cancelreview', $this->post->comment,'mobile');
            }
        }else{
            $authorizeComment = sprintf($this->lang->modify->authorizeComment,zget($users, $this->app->user->account), zget($users, $res['reviewAuthorize']));
            if(!isset($_POST['cancelStatus'])) {
                $action = 'gmsuccess' == $modify->status || 'cmconfirmed' == $modify->status ? 'deal' : 'review';
                $this->loadModel('action')->create('modify', $id, $action, $this->post->comment.'<br>'.$authorizeComment,'mobile');
            }else{
                $this->loadModel('action')->create('modify', $id, 'cancelreview', $this->post->comment.'<br>'.$authorizeComment,'mobile');
            }
        }
        $this->loadModel('mobileapi')->response('success', '', $this->lang->saveSuccess ,  0, 200,'modifyReviewApi');
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
            $errMsg[] = '『生产变更单ID』不能为空';
            return $errMsg;
        }
        if(isset($_POST['id']) && $_POST['id']){
            if(!preg_match("/^[1-9][0-9]*$/",$_POST['id'])){
                $errMsg[] = '生产变更单ID只能正整数';
                return $errMsg;
            }
        }
        return $errMsg;
    }
}
