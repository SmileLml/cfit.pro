<?php
include '../../control.php';
class myMobileApi extends mobileapi
{

    /**
     * 对外交付-评审
     */
    public function outwardReviewApi()
    {
        $users = $this->loadModel('user')->getPairs('noclosed');
        $this->app->loadLang('outwarddelivery');
        $errMsg = $this->checkInput();
        if (!empty($errMsg)) {
            $this->loadModel('mobileapi')->response('fail', implode(',', $errMsg), array(),  0, 203,'outwardReviewApi');
        }
        $outwarddeliveryID = $_POST['id'];
        $version = $_POST['version'];
        $reviewStage = $_POST['reviewStage'];
        $outwarddelivery = $this->loadModel('outwarddelivery')->getByID($outwarddeliveryID);
        //检查是否允许审核
        $res = $this->loadModel('outwarddelivery')->checkAllowReview($outwarddelivery, $version,  $reviewStage, $this->app->user->account);
        if (!$res['result']){
            $this->loadModel('mobileapi')->response('fail', $res['message'], array(),  0, 203,'outwardReviewApi');
        }
        if($res['result']){
            $this->loadModel('demand')->isSingleUsage($outwarddelivery->demandId, 'outwarddelivery', $outwarddeliveryID,$outwarddelivery->isNewModifycncc);
            if(dao::isError()){
                $this->loadModel('mobileapi')->response('fail', implode(',', dao::getError()), array(),  0, 203,'outwardReviewApi');
            }
        }
        $outInfo = $this->outwarddelivery->getByID($outwarddeliveryID);
        $info    = $this->outwarddelivery->review($outwarddeliveryID);

        if(dao::isError())
        {
            $response['result']  = 'fail';
            $response['message'] = dao::getError();
            $this->send($response);
        }
        $action = 'cmconfirmed' == $outInfo->status || 'gmsuccess' == $outInfo->status ? 'deal' : 'review';
        if($res['reviewAuthorize'] == $this->app->user->account){
            $actionID = $this->loadModel('action')->create('outwarddelivery', $outwarddeliveryID, $action, $this->post->comment,'mobile');
        }else{
            $authorizeComment = sprintf($this->lang->outwarddelivery->authorizeComment,zget($users, $this->app->user->account), zget($users, $res['reviewAuthorize']));
            $actionID = $this->loadModel('action')->create('outwarddelivery', $outwarddeliveryID, $action, $this->post->comment.'<br>'.$authorizeComment,'mobile');
        }


        if(isset($info['mediaPush']) && $info['mediaPush'] == 1){
            $this->dao->update(TABLE_RELEASE)
                ->set('pushStatusQz')->eq(1)
                ->set('pushFailsQz')->eq(0) //重发 失败归零 不重置remotePathQz 因为发送要校验是否最新并成功
                ->set('md5')->eq("")
                ->where('id')->in(explode(',', trim($outwarddelivery->release,',')))
                ->exec();

            $this->loadModel('action')->create('outwarddelivery', $outwarddelivery->id, 'pushmedia', "推送介质到清总", "", "guestjk");
        }
        $this->loadModel('mobileapi')->response('success', '', $this->lang->saveSuccess ,  0, 200,'outwardReviewApi');
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
            $errMsg[] = '『对外交付ID』不能为空';
            return $errMsg;
        }
        if(isset($_POST['id']) && $_POST['id']){
            if(!preg_match("/^[1-9][0-9]*$/",$_POST['id'])){
                $errMsg[] = '对外交付ID只能正整数';
                return $errMsg;
            }
        }
        return $errMsg;
    }
}
