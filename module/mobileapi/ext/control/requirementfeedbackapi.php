<?php
include '../../control.php';
class myMobileApi extends mobileapi
{

    /**
     * 金信-生产变更-评审
     */
    public function requirementFeedbackApi()
    {
        $this->app->loadLang('requirement');
        $errMsg = $this->checkInput();
        if (!empty($errMsg)) {
            $this->loadModel('mobileapi')->response('fail', implode(',', $errMsg), array(),  0, 203,'requirementFeedbackApi');
        }
        /**
         * @var requirementModel $requirementModel
         */
        $requirementModel = $this->loadModel('requirement');
        $id = $_POST['id'];
        $version = $_POST['version'];
        $reviewStage = $_POST['reviewStage'];
        $requirement = $requirementModel->getByID($id);

        $requirementModel->reviewfeedback($id,'mobile');

        if(dao::isError())
        {
            $this->loadModel('mobileapi')->response('fail', implode(',', dao::getError()), array(),  0, 203,'requirementFeedbackApi');
        }

        $this->loadModel('mobileapi')->response('success', '', $this->lang->saveSuccess ,  0, 200,'requirementFeedbackApi');
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
            $errMsg[] = '『反馈单ID』不能为空';
            return $errMsg;
        }
        if(isset($_POST['id']) && $_POST['id']){
            if(!preg_match("/^[1-9][0-9]*$/",$_POST['id'])){
                $errMsg[] = '反馈单ID只能正整数';
                return $errMsg;
            }
        }
        return $errMsg;
    }
}
