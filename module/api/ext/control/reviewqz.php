<?php
include '../../control.php';
class myApi extends api
{
    const PARAMS_MISSING = 1001; //缺少参数
    const PARAMS_ERROR   = 1002; //缺少参数

    public function reviewqz(){
        $logID  = $this->loadModel('requestlog')->insideSaveRequestLog('reviewqz' , '清总评审同步'); //todo 测试不加日志
        $errMsg = $this->checkInput();
        if (!empty($errMsg)) {
            $this->requestlog->response('fail', implode(',', $errMsg), [], $logID, self::PARAMS_MISSING);
        }
        $qzReviewId = $this->post->qzReviewId;
        $reviewInfo = $this->loadModel('reviewqz')->getReviewByQzReviewId($qzReviewId, 'id'); //查询是否存在
        $objectType  = $this->lang->reviewqz->objectType;
        $account     = $this->lang->reviewqz->defCreateBy;
        if($reviewInfo->id){
            $reviewId = $reviewInfo->id;
            $res = $this->reviewqz->updateByApi($reviewId); // 清总单;
            $action = 'update';
            $mailConfTemp = isset($res['data']['mailConfTemp'])? $res['data']['mailConfTemp']: 'setReviewQzMail';
        }else{
            $res = $this->reviewqz->createByApi(); // 清总单;
            $reviewId = $res['data']['reviewId'];
            $action = 'created';
            $mailConfTemp = 'setReviewQzMail';
        }
        if(!$res['result']) {
            $this->requestlog->response('fail', $res['message'], [], $logID, self::PARAMS_ERROR);
        }else{
            $actionID = $this->loadModel('action')->create($objectType, $reviewId, $action, '清总评审单号:' . $qzReviewId,'',$account);
            if((isset($res['data']['logChanges'])) && !empty($res['data']['logChanges'])){
                $this->action->logHistory($actionID, $res['data']['logChanges']);
            }
            if($action == 'created' || ((isset($res['data']['isSendMail'])) && $res['data']['isSendMail'])){ //需要发邮件
                $this->loadModel('reviewqz')->sendmail($reviewId, $actionID, '', '', '', $mailConfTemp); //新同步的清总评审发送邮件
            }
            //返回
            $this->requestlog->response('success', $this->lang->api->successful, array('Review_ID' => $qzReviewId), $logID);
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
        $apiAddItems = $this->lang->reviewqz->apiAddItems;
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

//            if(($v['isChange']) && ($v['changeParams']['type'] == 'time') && ($data[$k])){ //时间毫秒的转化
//                $timeStamp = substr($this->post->$k, 0, 10);
//                $processingTime = date('Y-m-d H:i:s', $timeStamp);
//                $this->post->$k =  $processingTime;
//            }

            if($v['target'] != $k) {
                $_POST[$v['target']] = $this->post->$k;
                unset($_POST[$k]);
            }
        }

        return $errMsg;
    }

}