<?php
include '../../control.php';
class myApi extends api
{
    const PARAMS_MISSING = 1001; //缺少参数
    const PARAMS_ERROR   = 1002; //缺少参数
    const FAIL_CODE   = 999;    //请求失败

    public function defect()
    {
        $logID = $this->loadModel('requestlog')->insideSaveRequestLog('defect' , 'defectReciveFeedback'); //todo 测试不加日志
        $errMsg = $this->checkInput();
        if(!empty($errMsg)) {
            $this->requestlog->response('fail', implode(',',$errMsg), [], $logID, self::FAIL_CODE);
        }
        $this->loadModel('defect')->updateByApi();

        if(dao::isError()) {
            $this->requestlog->response('fail', dao::getError(), [], $logID, self::FAIL_CODE);
        }
        $this->requestlog->response('success', $this->lang->api->successful, array('id' => $this->post->uatId), $logID);
    }

    /**
     * 校验
     * @return void
     */
    private function checkInput(){
        $errMsg = [];
        $this->loadModel('defect');
        //校验是否存在异常字段
        foreach ($_POST as $key => $v)
        {
            if(!isset($this->lang->defect->apiItems[$key])){
                $errMsg[] = $key."不是协议字段";
            }
        }

        //测试申请和产品登记必须关联一个
        $relatedTestApplication      = $_POST['relatedTestApplication'];//测试申请giteeId
        $relatedProductRegistrations = $_POST['relatedProductRegistrations'];//产品登记giteeId
        if(!isset($relatedTestApplication) && !isset($relatedTestApplication))
        {
            $errMsg[] =  $this->lang->defect->requiredTip;
        }
        if(isset($relatedTestApplication) && empty($relatedTestApplication) && isset($relatedProductRegistrations) && empty($relatedProductRegistrations))
        {
            $errMsg[] =  $this->lang->defect->requiredTip;
        }

        //校验是否存在 有测试申请单以测试申请单为主
        $testrequestGiteeId = isset($relatedTestApplication) && !empty($relatedTestApplication) ? $relatedTestApplication : '';
        if(!empty($testrequestGiteeId)){
            $tdata = $this->dao->select('id,giteeId')->from(TABLE_TESTINGREQUEST)->where('giteeId')->eq($testrequestGiteeId)->andWhere('deleted')->eq(0)->fetch();
            if($tdata === false) $errMsg[] =  $this->lang->defect->testrequestGiteeIdTip;
        }else{
            $productenrollGiteeId = isset($relatedProductRegistrations) && !empty($relatedProductRegistrations) ? $relatedProductRegistrations : '';
            if(!empty($productenrollGiteeId)){
                $pdata = $this->dao->select('id,giteeId')->from(TABLE_PRODUCTENROLL)->where('giteeId')->eq($productenrollGiteeId)->andwhere('deleted')->eq(0)->fetch();
                if($pdata === false) $errMsg[] =  $this->lang->defect->productenrollGiteeIdTip;
            }
        }

        if(!empty($errMsg)){
            return $errMsg;
        }

        foreach ($this->lang->defect->apiItems as $k => $v)
        {
            if($v['required'] && $this->post->$k == ''){
                $errMsg[] = $k.$v['name'].$this->post->$k.'不可以为空';
            }
            if($v['target'] != $k)
            {
                $_POST[$v['target']] = $this->post->$k;
                unset($_POST[$k]);
            }
        }
        return $errMsg;
    }
}