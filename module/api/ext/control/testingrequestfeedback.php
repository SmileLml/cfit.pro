<?php
include '../../control.php';
class myApi extends api
{
    const PARAMS_MISSING = 1001; //缺少参数
    const PARAMS_ERROR   = 1002; //缺少参数
    const FAIL_CODE   = 999;    //请求失败

    public function testingrequestfeedback()
    {
        $logID = $this->loadModel('requestlog')->insideSaveRequestLog('testingrequest' , 'testingrequestfeedback'); //todo 测试不加日志
        $errMsg = $this->checkInput();
        if(!empty($errMsg)) {
            $this->requestlog->response('fail', implode(',',$errMsg), [], $logID, self::FAIL_CODE);
        }
        $testingrequest = $this->loadModel('testingrequest')->getByCode($this->post->id);
        //根据对外交付表中查找测试申请id
        $outwarddelivery = $this->loadModel('testingrequest')->getOutwarddeliveryByTestId($testingrequest->id);
        //更新数据库

        //判断数据库是否存在记录
        if(!empty($testingrequest->id)){
            $testFile = $this->post->TestReportFromTestCenter; //框架已经decode 为对象了
            if(isset($testFile['url']) && isset($testFile['fileName'])){
                $this->loadModel('testingrequest')->updateStatus($testingrequest->id, $outwarddelivery, $this->post->cardStatus, $this->post->returnPerson
                    , $this->post->returnCase, $testFile);
            } else {
                $this->loadModel('testingrequest')->updateStatus($testingrequest->id, $outwarddelivery, $this->post->cardStatus, $this->post->returnPerson
                    , $this->post->returnCase);
            }
        }else{
            $errMsg[] = "该ID不存在";
            $this->requestlog->response('fail', implode(',',$errMsg), [], $logID, self::FAIL_CODE);
        }

        //清总对外交付-测试单外部退回，记录退回原因
        $newOutwarddelivery = $this->loadModel('testingrequest')->getOutwarddeliveryByTestId($testingrequest->id);
        $reviewFailReason = $this->loadModel('outwarddelivery')->getHistoryReview($newOutwarddelivery, 1);
        $this->dao->update(TABLE_OUTWARDDELIVERY)->set('reviewFailReason')->eq($reviewFailReason)->where('id')->eq($outwarddelivery->id)->exec();
        if(dao::isError()) {
            $this->requestlog->response('fail', dao::getError(), [], $logID, self::FAIL_CODE);
        }
        $this->dao->update(TABLE_REQUESTLOG)->set('objectId')->eq($outwarddelivery->testingRequestId)->where('id')->eq($logID)->exec();
        $this->requestlog->response('success', $this->lang->api->successful, array('id' => $this->post->id), $logID);
    }

    /**
     * 校验
     * @return void
     */
    private function checkInput(){
        $errMsg = [];
        $this->loadModel('testingrequest');
        //校验是否存在异常字段
        foreach ($_POST as $key => $v)
        {
            if(!isset($this->lang->testingrequest->apiItems[$key])){
                $errMsg[] = $key."不是协议字段";
            }
        }

        if(!empty($errMsg)){
            return $errMsg;
        }

        foreach ($this->lang->testingrequest->apiItems as $k => $v)
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