<?php
include '../../control.php';
class myApi extends api
{
    const PARAMS_MISSING = 1001; //缺少参数
    const PARAMS_ERROR   = 1002; //缺少参数
    const FAIL_CODE   = 999;    //请求失败

    public function modifyapprove()
    {
        $logID = $this->loadModel('requestlog')->insideSaveRequestLog('modify' , 'modifyapprove'); //todo 测试不加日志
        $this->checkApiToken();
        $errMsg = $this->checkInput();
        if(!empty($errMsg)) {
            $this->requestlog->response('fail', implode(',',$errMsg), [], $logID, self::FAIL_CODE);
        }
        //查找数据
        $modify = $this->loadModel('modify')->getByGiteeId($this->post->externalCode);

        //判断数据库是否存在记录
        if(!empty($modify->id)){
            $updateData = new stdClass();
            if($this->post->approvalResults == '生产调度部变更经理排期并提交实施'){
                $updateData->status = 'jxSubmitImplement';
                $updateData->changeStatus = 'jxSubmitImplement';
            }else if($this->post->approvalResults == '受理人受理变更并审核'){
                $updateData->status = 'jxacceptorReview';
                $updateData->changeStatus = 'jxacceptorReview';
            } else{
                $errMsg[] = "approvalResults不是在候选值中";
                $this->requestlog->response('fail', implode(',',$errMsg), [], $logID, self::FAIL_CODE);
            }
            $updateData->implementers = $this->post->implementers;
            $updateData->implementDepartment = $this->post->implementDepartment;
            $updateData->implementStartTime = $this->post->implementStartTime;
            $updateData->implementEndTime = $this->post->implementEndTime;
            $updateData->changeDate = helper::now();
            $updateData->dealUser = 'guestjx';
            $this->dao->begin();
            $this->dao->update(TABLE_MODIFY)->data($updateData)->where('id')->eq($modify->id)->exec();
            $this->loadModel('consumed')->record('modify', $modify->id, 0, 'guestjx', $modify->status, $updateData->status, array());
            $this->loadModel('demand')->changeBySecondLineV4($modify->id,'modify');
            $this->dao->commit();
            $actionID = $this->loadModel('action')->create('modify', $modify->id, 'modifysyncstatus', '','','guestjx');
        }else{
            $errMsg[] = "该ID不存在";
            $this->requestlog->response('fail', implode(',',$errMsg), [], $logID, self::FAIL_CODE);
        }

        if(dao::isError()) {
            $this->requestlog->response('fail', dao::getError(), [], $logID, self::FAIL_CODE);
        }
        $this->dao->update(TABLE_REQUESTLOG)->set('objectId')->eq($modify->id)->where('id')->eq($logID)->exec();
        $this->requestlog->response('success', $this->lang->api->successful, array('idUnique' => $modify->externalCode), $logID);
    }

    /**
     * 校验
     * @return void
     */
    private function checkInput(){
        $errMsg = [];
        $this->loadModel('modify');
        //校验是否存在异常字段
        foreach ($_POST as $key => $v)
        {
            if(!isset($this->lang->modify->apiApproveItems[$key])){
                $errMsg[] = $key."不是协议字段";
            }
        }

        if(!empty($errMsg)){
            return $errMsg;
        }

        foreach ($this->lang->modify->apiApproveItems as $k => $v)
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