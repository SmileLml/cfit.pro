<?php
include '../../control.php';
class myApi extends api
{
    const PARAMS_MISSING = 1001; //缺少参数
    const PARAMS_ERROR   = 1002; //缺少参数
    const FAIL_CODE   = 999;    //请求失败

    public function modifyreturn()
    {
        $logID = $this->loadModel('requestlog')->insideSaveRequestLog('modify' , 'modifyreturn'); //todo 测试不加日志
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
            $secondLineReviewList = implode(",",array_keys($this->lang->modify->secondLineReviewList));
            $updateData->dealUser = $secondLineReviewList;
            $updateData->status = 'modifyreject';
            $updateData->changeStatus = '5';
            $updateData->returnReason = $this->post->returnReason;
            $updateData->approverName = $this->post->approverName;
            $updateData->returnTime = $modify->returnTime+1;
            $updateData->changeDate = helper::now();
            $updateData->jsreturn = 1;
            $updateData->reviewFailReason = $this->loadModel('modify')->getHistoryReview((object)array_merge((array)$modify, (array)$updateData));
            $this->dao->begin();
            $this->dao->update(TABLE_MODIFY)->data($updateData)->where('id')->eq($modify->id)->exec();
            $this->loadModel('consumed')->record('modify', $modify->id, 0, 'guestjx', $modify->status, 'modifyreject', array());
            $this->loadModel('demand')->changeBySecondLineV4($modify->id,'modify');
            $this->dao->commit();
            $actionID = $this->loadModel('action')->create('modify', $modify->id, 'reject', '','','guestjx');
            $changes = common::createChanges($modify, $updateData);
            if(!empty($changes)) $this->action->logHistory($actionID, $changes);

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
        $this->app->loadLang('modify');
        $errMsg = [];
        $this->loadModel('modify');
        //校验是否存在异常字段
        foreach ($_POST as $key => $v)
        {
            if(!isset($this->lang->modify->apiReturnItems[$key])){
                $errMsg[] = $key."不是协议字段";
            }
        }

        if(!empty($errMsg)){
            return $errMsg;
        }

        foreach ($this->lang->modify->apiReturnItems as $k => $v)
        {
            if($v['required'] && $this->post->$k == ''){
                $errMsg[] = $k.$v['name'].$this->post->$k.'不可以为空';
            }
            //退回原因不能少于规定字符
            if($k == 'reason' && mb_strlen($this->post->$k) < $this->lang->modify->rejectingMinLength['rejectingMinLength']){
                $errMsg[] = $k.$v['name'].'不能少于' . $this->lang->modify->rejectingMinLength['rejectingMinLength'] . '字符';
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