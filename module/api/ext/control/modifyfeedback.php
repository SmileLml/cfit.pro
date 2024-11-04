<?php
include '../../control.php';
class myApi extends api
{
    const PARAMS_MISSING = 1001; //缺少参数
    const PARAMS_ERROR   = 1002; //缺少参数
    const FAIL_CODE   = 999;    //请求失败

    public function modifyfeedback()
    {
        $logID = $this->loadModel('requestlog')->insideSaveRequestLog('modify' , 'modifyfeedback'); //todo 测试不加日志
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
            $updateData->operateName = $this->post->operateName;
            $updateData->operateType = $this->post->operateType;
            $updateData->implementResult = $this->post->implementResult;
            $updateData->realStartTime = $this->post->startTime;
            $updateData->realEndTime = $this->post->endTime;
            $updateData->supportUserName = $this->post->supportUserName;
            $updateData->operateUserName = $this->post->operateUserName;
            $updateData->issueDesc = $this->post->issueDesc;
            $updateData->resolveMethod = $this->post->resolveMethod;
            $updateData->feedbackId = $this->post->feedbackId;
            $updateData->feedbackDate = helper::now();
            $this->dao->update(TABLE_MODIFY)->data($updateData)->where('id')->eq($modify->id)->exec();
            if(empty($modify->feedbackId)){
                $actionID = $this->loadModel('action')->create('modify', $modify->id, 'feedbacksyn', '','','guestjx');
            }else{
                $actionID = $this->loadModel('action')->create('modify', $modify->id, 'feedbacksynedit', '','','guestjx');
            }

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
            if(!isset($this->lang->modify->apiFeedbackItems[$key])){
                $errMsg[] = $key."不是协议字段";
            }
        }

        if(!empty($errMsg)){
            return $errMsg;
        }

        foreach ($this->lang->modify->apiFeedbackItems as $k => $v)
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
        if(!empty($errMsg)){
            return $errMsg;
        }
        if(strlen($this->post->issueDesc) > 500){
            $errMsg[] = '问题描述长度不超过500';
        }
        if(strlen($this->post->resolveMethod) > 500){
            $errMsg[] = '原因分析/解决方法长度不超过500';
        }
        return $errMsg;
    }
}