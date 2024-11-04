<?php
include '../../control.php';
class myApi extends api
{
    const PARAMS_MISSING = 1001; //缺少参数
    const PARAMS_ERROR   = 1002; //缺少参数
    const FAIL_CODE   = 999;    //请求失败

    public function modifycomplete()
    {
        $logID = $this->loadModel('requestlog')->insideSaveRequestLog('modify' , 'modifycomplete'); //todo 测试不加日志
//        $this->checkApiToken();
        $errMsg = $this->checkInput();
        if(!empty($errMsg)) {
            $this->requestlog->response('fail', implode(',',$errMsg), [], $logID, self::FAIL_CODE);
        }
        //查找数据
        $modify = $this->loadModel('modify')->getByGiteeId($this->post->externalCode);

        //判断数据库是否存在记录
        if(!empty($modify->id)){
            $updateData = new stdClass();
            $updateData->changeRemark = $this->post->changeRemark;
            $updateData->realStartTime = $this->post->realStartTime;
            $updateData->realEndTime = $this->post->realEndTime;
            $updateData->returnReason = '';
            $updateData->changeDate = helper::now();
            if($this->post->changeStatus == '变更取消'){
                $updateData->status = 'modifycancel';
                $updateData->dealUser = '';
                $updateData->changeStatus = 4;
            }else if($this->post->changeStatus == '变更成功'){
                $updateData->status = 'modifysuccess';
                $updateData->dealUser = '';
                $updateData->changeStatus = 6;
            }else if($this->post->changeStatus == '部分成功'){
                $updateData->dealUser = '';
                $updateData->status = 'modifysuccesspart';
                $updateData->changeStatus = 3;
            }else if($this->post->changeStatus == '变更回退'){
                $updateData->dealUser = '';
                $updateData->status = 'modifyrollback';
                $updateData->changeStatus = 7;
            }else if($this->post->changeStatus == '变更异常'){
                $updateData->dealUser = '';
                $updateData->status = 'modifyfail';
                $updateData->changeStatus = 2;
            }else if($this->post->changeStatus == '变更失败'){
                $updateData->dealUser = '';
                $updateData->status = 'modifyerror';
                $updateData->changeStatus = 8;
            }else{
                $errMsg[] = "changeStatus不是在候选值中";
                $this->requestlog->response('fail', implode(',',$errMsg), [], $logID, self::FAIL_CODE);
            }
            $updateData->reviewFailReason = $this->loadModel('modify')->getHistoryReview((object)array_merge((array)$modify, (array)$updateData));
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
            if(!isset($this->lang->modify->apiCompleteItems[$key])){
                $errMsg[] = $key."不是协议字段";
            }
        }

        if(!empty($errMsg)){
            return $errMsg;
        }
        if($this->post->changeStatus == '变更取消'){
            $this->lang->modify->apiCompleteItems['realStartTime']['required'] = 0;
            $this->lang->modify->apiCompleteItems['realEndTime']['required'] = 0;
            $this->lang->modify->apiCompleteItems['changeRemark']['required'] = 0;
        }
        foreach ($this->lang->modify->apiCompleteItems as $k => $v)
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