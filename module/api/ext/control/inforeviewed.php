<?php
include '../../control.php';
class myApi extends api
{
    const PARAMS_MISSING = 1001; //缺少参数
    const PARAMS_ERROR   = 1002; //参数错误

    public function infoReviewed()
    {
        $this->loadModel('infoqz');
        $logID = $this->loadModel('requestlog')->insideSaveRequestLog('infoQz' , 'review');
        $this->requestlog->judgeRequestMode($logID);

        if($_POST['changeStatus'] == '数据获取退回')
        {
            $this->lang->infoqz->apiReviewItems['reason']['required'] = 1;
        }else{
            $this->lang->infoqz->apiReviewItems['changeRemark']['required'] = 1;
            $this->lang->infoqz->apiReviewItems['realStartTime']['required'] = 1;
            $this->lang->infoqz->apiReviewItems['realEndTime']['required'] = 1;
        }

        $errMsg = $this->checkInput();
        if(!empty($errMsg)) {
            $this->requestlog->response('fail', implode(',',$errMsg).'不可以空', [], $logID, self::PARAMS_MISSING);
        }

        $info = $this->infoqz->getInfoByCode($this->post->code, 'id, externalStatus'); //查询是否存在
        if(empty($info->id)){ //不存在,报错
            $infoEmpty = sprintf($this->lang->api->infoEmpty, $_POST['code']);
            $this->requestlog->response('fail',  $infoEmpty, [], $logID, self::PARAMS_ERROR);
        }
        $infoID = $info->id;
        $changes = $this->infoqz->updateByApi($infoID); // 存在 更新

        if(dao::isError()) {
            $this->requestlog->response('fail', dao::getError(), [], $logID, self::PARAMS_ERROR);
        }
        //记录日志
        $action = 'syncstatus';
        if($info->externalStatus){
            $action = 'editstatus';
        }
        $actionID = $this->loadModel('action')->create('infoqz', $infoID, $action, '', '', 'guestcn');
        $this->action->logHistory($actionID, $changes);

        $this->requestlog->response('success', $this->lang->api->successful, array('getDataNum' => $this->post->code), $logID);
    }

    /**
     * 校验
     * @return array
     */
    private function checkInput()
    {
        $this->loadModel('info');
        $errMsg = [];
        foreach ($this->lang->infoqz->apiReviewItems as $k => $v)
        {
            if($v['required'] && $this->post->$k == ''){
                $errMsg[] = $k.$v['name'].$this->post->$k;
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