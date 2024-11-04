<?php
include '../../control.php';
class myApimeasure extends apimeasure
{
    const PARAMS_MISSING = 1001; //缺少参数
    const PARAMS_ERROR   = 1002; //缺少参数
    const FAIL_CODE   = 999;    //请求失败

    public function getDpmpLang()
    {

        // token以及参数校验
        $this->checkApiToken();
        $errMsg = $this->checkInput();
        if(!empty($errMsg)) {
            $this->loadModel('requestlog')->response('fail', implode(',',$errMsg), [], 0, self::FAIL_CODE);
        }

        $module  = $_POST['module'];
        $section = $_POST['section'];
        $res = $this->dao->select('module,`section`,`key`,value')->from(TABLE_LANG)->where('module')->eq($module)->andWhere('section')->eq($section)->fetchAll();

        if(dao::isError()) {
            $this->loadModel('requestlog')->response('fail', dao::getError(), [], 0, self::FAIL_CODE);
        }
        $this->loadModel('requestlog')->response('success', $this->lang->api->successful, $res,0,200);
    }

    /**
     * 校验
     * @return void
     */
    private function checkInput(){
        $errMsg = [];
        if(!isset($_POST['module'])){
            $errMsg[] = "缺少『module』参数";
        }
        if(!isset($_POST['section'])){
            $errMsg[] = "缺少『section』参数";
        }
        foreach ($_POST as $key => $v)
        {
            if(!isset($_POST['module']) && !isset($_POST['section'])){
                $errMsg[] = $key."不是协议字段";
            }
        }
        if( isset($_POST['module']) && !$_POST['module']){
            $errMsg[] = '『模块』不能为空';
        }
        if( isset($_POST['section']) && !$_POST['section']){
            $errMsg[] = '『关键词』不能为空';
        }
        return $errMsg;
    }
}
