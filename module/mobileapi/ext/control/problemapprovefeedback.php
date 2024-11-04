<?php
include '../../control.php';
class myMobileApi extends mobileapi
{

    /**
     * 问题单 反馈单审核
     */
    public function problemApprovefeedback()
    {
        $errMsg = $this->checkInput();
        if (!empty($errMsg)) {
            $this->loadModel('mobileapi')->response('fail', implode(',', $errMsg), array(),  0, 203,'problemApprovefeedback');
        }
        $problemID = $_POST['id'];
        $problem = $this->loadModel('problem')->getByID($problemID);
        $res = $this->problem->checkAllowReview($problem, $problem->version, $problem->reviewStage, $this->app->user->account);
        if(!$res['result']){
            $this->loadModel('mobileapi')->response('fail', $res['message'], array(),  0, 203,'problemApprovefeedback');

        }
        $this->problem->review($problemID,'mobile');
        if(dao::isError()){
            $this->loadModel('mobileapi')->response('fail', implode(',', dao::getError()), array(),  0, 203,'problemApprovefeedback');
        }
        $this->app->loadLang('problem');
        $this->loadModel('mobileapi')->response('fail', '保存成功', array(),  0, 200,'problemApprovefeedback');
    }
    private function checkInput()
    {
        $errMsg = [];
        if(!isset($_POST['id'])){
            $errMsg[] = "缺少『id』参数";
            return $errMsg;
        }

        if( isset($_POST['id']) && !$_POST['id']){
            $errMsg[] = '『问题单ID』不能为空';
            return $errMsg;
        }
        if(isset($_POST['id']) && $_POST['id']){
            if(!preg_match("/^[1-9][0-9]*$/",$_POST['id'])){
                $errMsg = '『问题单ID』只能正整数';
                return $errMsg;
            }
        }
        return $errMsg;
    }
}