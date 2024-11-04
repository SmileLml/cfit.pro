<?php
include '../../control.php';
class myApi extends api
{
    const PARAMS_MISSING = 1001; //缺少参数
    const PARAMS_ERROR   = 1002; //缺少参数

    /**
     * 清总评审接收最终评审结果
     */
    public function reviewQzReceivedResult(){
        $logID  = $this->loadModel('requestlog')->insideSaveRequestLog('reviewqz' , '清总同步最终结果'); //todo 测试不加日志
        $errMsg = $this->checkInput();
        if (!empty($errMsg)) {
            $this->requestlog->response('fail', implode(',', $errMsg), [], $logID, self::PARAMS_MISSING);
        }

        $qzReviewId  = $this->post->qzReviewId;
        $finalResult = $this->post->finalResult == 1 ? '通过' : '不通过';
        $reviewInfo = $this->loadModel('reviewqz')->getReviewByQzReviewId($qzReviewId, 'id'); //查询是否存在
        if(empty($reviewInfo->id)){
            $this->requestlog->response('fail', '未关联到对应评审', [], $logID, self::PARAMS_ERROR);
        }
        $objectType  = $this->lang->reviewqz->objectType;
        $account     = $this->lang->reviewqz->defCreateBy;
        $res = $this->reviewqz->qzFinalResult($_POST); // 清总最终结果同步;
        if(!$res['result']) {
            $this->requestlog->response('fail', $res['message'], [], $logID, self::PARAMS_ERROR);
        }else{
            $actionID = $this->loadModel('action')->create($objectType, $reviewInfo->id, '同步', '清总同步最终结果：' . $finalResult,'',$account);
            if((isset($res['data']['logChanges'])) && !empty($res['data']['logChanges'])){
                $this->action->logHistory($actionID, $res['data']['logChanges']);
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
        $apiAddItems = $this->lang->reviewqz->apiResultItems;
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
                $errMsg[] = $k.$v['name'].$this->post->$k.'不可以为空';
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

        if(!empty($data['Tickets_list'])){
            $issueList = json_decode($data['Tickets_list'],true);
            $apiAddIssueItems = $this->lang->reviewqz->apiAddIssueItems;
            $addIssueItemsKeys = array_keys($apiAddIssueItems);
            foreach($issueList as $num => $issue){
                $postIssueKeys = array_keys($issue);
                //传入多余的字段
                $tempIssueKeys = array_diff($postIssueKeys, $addIssueItemsKeys);
                if(!empty($tempIssueKeys)){
                    foreach ($tempIssueKeys as $kk){
                        $errMsg[] = $kk."不是协议字段";
                    }
                }
                //验证每个字段
                foreach ($apiAddIssueItems as $issueK => $issueV) {
                    if($issueK == 'Review_ID') continue;
                    if($issueV['required'] && $issue[$issueK] == ''){
                        $errMsg[] = $issueK.$issueV['name'].$issue[$issueK].'不可以空';
                    }
                    if(($issueV['isChange']) && ($issueV['changeParams']['type'] == 'enum') && ($issue[$issueK])){ //枚举类型需要检查是否枚举值中
                        $enumDateList = $issueV['changeParams']['enumDateList'];
                        if(!in_array($issue[$issueK], $enumDateList)){
                            $errMsg[] = $issueK.$issueV['name'].$issue[$issueK].'值错误';
                        }else{
                            $issue[$issueK] = (array_flip($enumDateList))[$issue[$issueK]]; //重新赋值
                        }
                    }
//                    if($issueV['target'] != $issueK) {
//                        $_POST[$issueV['target']] = $issue[$issueK];
//                        unset($_POST[$issueK]);
//                    }
                }
            }
        }
        return $errMsg;
    }
}