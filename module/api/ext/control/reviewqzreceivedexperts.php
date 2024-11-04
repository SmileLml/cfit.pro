<?php
include '../../control.php';
class myApi extends api
{
    const PARAMS_MISSING = 1001; //缺少参数
    const PARAMS_ERROR   = 1002; //缺少参数

    /**
     * 清总评审接收专家意见
     */
    public function reviewQzReceivedExperts(){
        $logID  = $this->loadModel('requestlog')->insideSaveRequestLog('reviewqz' , '接收清总反馈专家意见'); //todo 测试不加日志
        $errMsg = $this->checkInput();
        if (!empty($errMsg)) {
            $this->requestlog->response('fail', implode(',', $errMsg), [], $logID, self::PARAMS_MISSING);
        }

        $res = $this->loadModel('reviewqz')->qzFeedbackApi($this->post->qzReviewId, $this->post->conclusion, $this->post->reason);
        if(!$res['result']) {
            $this->requestlog->response('fail', $res['message'], [], $logID, self::PARAMS_ERROR);
        }else{
            //返回
            $this->requestlog->response('success', $this->lang->api->successful, array('Review_ID' => $this->post->qzReviewId), $logID);
        }

    }

    /**
     * 校验
     *
     * @return array
     */
    private function checkInput(){
        $errMsg = [];
        $this->loadModel('reviewqz');
        $data = $_POST;
        $postKeys = array_keys($data);
        $apiAddItems = $this->lang->reviewqz->apiAddItemsFeedbackExpert;
        $addItemsKeys = array_keys($apiAddItems);
        //传入多余的字段
        $tempKeys = array_diff($postKeys, $addItemsKeys);
        if(!empty($tempKeys)){
            foreach ($tempKeys as $key){
                $errMsg[] = $key."不是协议字段";
            }
        }

        //验证每个字段
        foreach ($apiAddItems as $k => $v) {
            if($v['required'] && $this->post->$k == ''){
                $errMsg[] = $k.$v['name'].$this->post->$k.'不可以空';
            }
            if(($v['isChange']) && ($v['changeParams']['type'] == 'enum') && ($data[$k])){ //枚举类型需要检查是否枚举值中
                $enumDateList = $v['changeParams']['enumDateList'];
                if(!in_array($data[$k], $enumDateList)){
                    $errMsg[] = $k.$v['name'].$this->post->$k.'值错误';
                }else{
                    $this->post->$k = (array_flip($enumDateList))[$this->post->$k]; //重新赋值
                }
            }
            if($v['target'] != $k) {
                $_POST[$v['target']] = $this->post->$k;
                unset($_POST[$k]);
            }
        }
        if($data['Approval_conclusions'] == '审批不通过' && empty($data['Call_back_reason'])){
            $errMsg[] = 'Call_back_reason请填写审批结论。';
        }
        return $errMsg;
    }

}