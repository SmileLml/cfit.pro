<?php
include '../../control.php';
class myApi extends api
{
    const PARAMS_MISSING = 1001; //缺少参数
    const PARAMS_ERROR   = 1002; //缺少参数
    const FAIL_CODE   = 999;    //请求失败

    public function sendUniversalStatus()
    {
        $logID = $this->loadModel('requestlog')->insideSaveRequestLog('secondorder' , 'sendUniversalStatus');
        $this->checkApiToken();
        $errMsg = $this->checkInput();
        if(!empty($errMsg)) {
            $this->requestlog->response('fail', implode(',',$errMsg), [], $logID, self::FAIL_CODE);
        }
        $this->loadModel('secondorder');
        $this->loadModel('application');
        $this->loadModel('opinion');
        //查找数据
        $secondorder = $this->loadModel('secondorder')->getByExternalCode($this->post->externalCode);

        //判断数据库是否存在记录
        $secondorderID = '';
        if(!empty($secondorder->id)){
            //只有未受理状态才更新数据
            if($secondorder->status != 'delivered' and $secondorder->status != 'backed' and $secondorder->status != 'solved' and $secondorder->status != 'indelivery' and $secondorder->status != 'passed'){
                $this->requestlog->response('fail', '当前数据状态不允许更新', [], $logID, self::FAIL_CODE);
            }
            if($this->post->taskListStatus == 'reject'){
                if($this->post->rejectReason == ''){
                    $this->requestlog->response('fail', '退回状态时拒绝原因必填', [], $logID, self::FAIL_CODE);
                }
            }
            $secondorderID = $secondorder->id;
            $updateData = new stdClass();
            //状态转换
            if($this->post->taskListStatus == 'cancel'){
                $updateData->status = 'closed';
                $updateData->dealUser = '';
                $updateData->externalTime = helper::now();
                $updateData->externalStatus = 'closed';
                $updateData->rejectUser = '';
                $updateData->rejectReason = '';
                $this->dao->update(TABLE_SECONDORDER)->data($updateData)->where('id')->eq($secondorder->id)->exec();
                $actionID = $this->loadModel('action')->create('secondorder', $secondorder->id, 'syncstatus', $this->post->note,'','guestcn');
                $changes = common::createChanges($secondorder, $updateData);
                if(!empty($changes)) $this->action->logHistory($actionID, $changes);
                $this->loadModel('consumed')->record('secondorder', $secondorder->id, 0, 'guestcn', $secondorder->status, $updateData->status, array());
            }else if($this->post->taskListStatus == 'reject'){
                if($secondorder->status == 'backed'){
                    $updateData->status = 'toconfirmed';
                }else{
                    $updateData->status = 'returned';
                }

                //待处理人-二线专员
                $userIds = array();
                foreach ($this->lang->secondorder->apiDealUserList as $key => $value) {
                    $userIds[] = $key;
                }
                $updateData->dealUser = implode(",", $userIds);
                $updateData->rejectUser = $this->post->rejectUser;
                $updateData->rejectReason = $this->post->rejectReason;
                $updateData->externalTime = helper::now();
                $updateData->externalStatus = 'reject';

                $this->dao->update(TABLE_SECONDORDER)->data($updateData)->where('id')->eq($secondorder->id)->exec();
                $actionID = $this->loadModel('action')->create('secondorder', $secondorder->id, 'syncstatus', $this->post->note,'','guestcn');
                $changes = common::createChanges($secondorder, $updateData);
                if(!empty($changes)) $this->action->logHistory($actionID, $changes);
                $this->loadModel('consumed')->record('secondorder', $secondorder->id, 0, 'guestcn', $secondorder->status, $updateData->status, array());
            }else if($this->post->taskListStatus == 'closed'){
                $updateData->status = 'closed';
                $updateData->dealUser = '';
                $updateData->completionFeedback = $this->post->completionFeedback;
                $updateData->externalTime = helper::now();
                $updateData->externalStatus = 'closed';
                $updateData->rejectUser = '';
                $updateData->rejectReason = '';
                $updateData->closedBy = 'guestcn';
                $updateData->closedDate = helper::now();
                $this->dao->update(TABLE_SECONDORDER)->data($updateData)->where('id')->eq($secondorder->id)->exec();
                $actionID = $this->loadModel('action')->create('secondorder', $secondorder->id, 'syncstatus', $this->post->note,'','guestcn');
                $changes = common::createChanges($secondorder, $updateData);
                if(!empty($changes)) $this->action->logHistory($actionID, $changes);
                $this->loadModel('consumed')->record('secondorder', $secondorder->id, 0, 'guestcn', $secondorder->status, $updateData->status, array());
            } else{
                $this->requestlog->response('fail', '状态值不符合范围', [], $logID, self::FAIL_CODE);
            }
        }else{
            $errMsg[] = "该ID不存在";
            $this->requestlog->response('fail', implode(',',$errMsg), [], $logID, self::FAIL_CODE);
        }

        if(dao::isError()) {
            $this->requestlog->response('fail', dao::getError(), [], $logID, self::FAIL_CODE);
        }
        $this->dao->update(TABLE_REQUESTLOG)->set('objectId')->eq($secondorderID)->where('id')->eq($logID)->exec();
        $this->requestlog->response('success', $this->lang->api->successful, array('id' => $secondorderID), $logID);
    }

    /**
     * 校验
     * @return void
     */
    private function checkInput(){
        $errMsg = [];
        $this->loadModel('secondorder');
        //校验是否存在异常字段
        foreach ($_POST as $key => $v)
        {
            if(!isset($this->lang->secondorder->sendUniversalStatusItems[$key])){
                $errMsg[] = "通用型服务请求单".$key."不是协议字段";
            }
        }

        if(!empty($errMsg)){
            return $errMsg;
        }

        foreach ($this->lang->secondorder->sendUniversalStatusItems as $k => $v)
        {
            if($v['required'] && $this->post->$k == ''){
                $errMsg[] = "通用型服务请求单".$k.$v['name'].$this->post->$k.'不可以为空';
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
