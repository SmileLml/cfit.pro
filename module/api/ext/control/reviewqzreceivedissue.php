<?php
include '../../control.php';
class myApi extends api
{
    const PARAMS_MISSING = 1001; //缺少参数
    const PARAMS_ERROR   = 1002; //缺少参数

    /**
     * 清总评审接收反馈问题结果
     */
    public function reviewQzReceivedIssue(){
        $logID  = $this->loadModel('requestlog')->insideSaveRequestLog('reviewqz' , '清总评审问题同步'); //todo 测试不加日志
        $errMsg = $this->checkInput();
        if (!empty($errMsg)) {
            $this->requestlog->response('fail', implode(',', $errMsg), [], $logID, self::PARAMS_MISSING);
        }

        $qzReviewId = $this->post->qzReviewId;
        $reviewInfo = $this->loadModel('reviewqz')->getReviewByQzReviewId($qzReviewId, 'id'); //查询是否存在
        if(empty($reviewInfo->id)){
            $this->requestlog->response('fail', '未关联到对应评审', [], $logID, self::PARAMS_ERROR);
        }
        $qzIssueId = $this->post->qzIssueId;
        $param = $this->post->sourceFrom == 'jk'?'id':'qzIssueId';
        $issueInfo = $this->loadModel('reviewqz')->getIssueQz($param, $qzIssueId); //查询是否存在
        $objectType  = $this->lang->reviewqz->objectIssueType;
        $account     = $this->lang->reviewqz->defCreateBy;
        if(!empty($issueInfo->id)){
            $res = $this->reviewqz->updateIssueByApi($issueInfo->id); // 修改清总问题;
            $issueId = $res['data']['reviewId'];
            $action = 'created';
        }else{
            $res = $this->reviewqz->createIssueByApi($reviewInfo->id); // 新增清总问题;
            $issueId = $res['data']['reviewId'];
            $action = 'created';
        }
        if(!$res['result']) {
            $this->requestlog->response('fail', $res['message'], [], $logID, self::PARAMS_ERROR);
        }else{
            $actionID = $this->loadModel('action')->create($objectType, $issueId, $action, '清总评审问题单号:' . $qzIssueId,'',$account);
            if((isset($res['data']['logChanges'])) && !empty($res['data']['logChanges'])){
                $this->action->logHistory($actionID, $res['data']['logChanges']);
            }
            //返回
            $this->requestlog->response('success', $this->lang->api->successful, array('Issue_ID' => $qzIssueId), $logID);
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
        $apiAddItems = $this->lang->reviewqz->apiAddIssueItems;
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

        return $errMsg;
    }
}