<?php
include '../../control.php';
class myApi extends api
{
    const PARAMS_MISSING = 1001; //缺少参数
    const PARAMS_ERROR   = 1002; //缺少参数
    const FAIL_CODE   = 999;    //请求失败

    public function handoverBack()
    {
        $logID = $this->loadModel('requestlog')->insideSaveRequestLog('sectransfer' , 'handoverBack');
        $this->checkApiToken();
        $errMsg = $this->checkInput();
        if(!empty($errMsg)) {
            $this->requestlog->response('fail', implode(',',$errMsg), [], $logID, self::FAIL_CODE);
        }
        $this->loadModel('sectransfer');
        //查找数据
        $sectransfer = $this->dao->findByID($this->post->id)->from(TABLE_SECTRANSFER)->fetch();

        //判断数据库是否存在记录
        $sectransferID = '';
        if(!empty($sectransfer->id)){
            //只有已交付状态才更新数据
            if($sectransfer->status != 'alreadyEdliver'){
                $this->requestlog->response('fail', '当前数据状态不允许更新', [], $logID, self::FAIL_CODE);
            }
            if($this->post->aduitStatus == 'reject'){
                if($this->post->rejectUser == '' || $this->post->rejectReason == ''){
                    $this->requestlog->response('fail', '退回状态时拒绝人和拒绝原因必填', [], $logID, self::FAIL_CODE);
                }
            }
            $sectransferID = $sectransfer->id;
            $updateData = new stdClass();
            //状态转换
            if($this->post->aduitStatus == 'pass'){
                $updateData->approver = '';
                $updateData->status = 'alreadyEdliver';
                $updateData->externalTime =  helper::now();
                $updateData->externalStatus = 'pass';
                $next = $this->dao->select('id')->from(TABLE_REVIEWNODE)->where('objectType')->eq('sectransfer')   //查找下一节点的状态
                    ->andWhere('objectID')->eq($sectransfer->id)
                    ->andWhere('version')->eq($sectransfer->version)
                    ->andWhere('stage')->eq('7')->fetch('id');
                if($next)
                {
                    $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('pass')->where('id')->eq($next)->exec();
                    $this->dao->update(TABLE_REVIEWER)->set('status')->eq('pass')->set('comment')->eq('清算总中心审批通过')->set('reviewTime')->eq(helper::now())->where('node')->eq($next)->exec();
                }
                $this->loadModel('consumed')->record('sectransfer', $sectransfer->id, 0, 'guestcn', $sectransfer->status, $updateData->status, array());
            }else if($this->post->aduitStatus == 'reject'){
                $updateData->status = 'centerReject';
                $updateData->approver = $sectransfer->sec; //二线专员
                $updateData->rejectUser = $this->post->rejectUser;
                $updateData->rejectReason = $this->post->rejectReason;
                $updateData->externalTime =  helper::now();
                $updateData->externalStatus = 'reject';
                $updateData->rejectNum = $sectransfer->rejectNum+1;
                $this->loadModel('consumed')->record('sectransfer', $sectransfer->id, 0, 'guestcn', $sectransfer->status, $updateData->status, array());
                $next = $this->dao->select('id')->from(TABLE_REVIEWNODE)->where('objectType')->eq('sectransfer')   //查找下一节点的状态
                ->andWhere('objectID')->eq($sectransfer->id)
                    ->andWhere('version')->eq($sectransfer->version)
                    ->andWhere('stage')->eq('7')->fetch('id');
                if($next)
                {
                    $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('reject')->where('id')->eq($next)->exec();
                    $this->dao->update(TABLE_REVIEWER)->set('status')->eq('reject')->set('comment')->eq($updateData->rejectReason)->set('reviewTime')->eq(helper::now())->where('node')->eq($next)->exec();
                }
            }else{
                $this->requestlog->response('fail', '状态值不符合范围', [], $logID, self::FAIL_CODE);
            }
            $this->dao->update(TABLE_SECTRANSFER)->data($updateData)->where('id')->eq($sectransfer->id)->exec();
            $actionID = $this->loadModel('action')->create('sectransfer', $sectransfer->id, 'syncstatus', $this->post->note,'','guestcn');
            $changes = common::createChanges($sectransfer, $updateData);
            if(!empty($changes)) $this->action->logHistory($actionID, $changes);
        }else{
            $errMsg[] = "该ID不存在";
            $this->requestlog->response('fail', implode(',',$errMsg), [], $logID, self::FAIL_CODE);
        }

        if(dao::isError()) {
            $this->requestlog->response('fail', dao::getError(), [], $logID, self::FAIL_CODE);
        }
        $this->dao->update(TABLE_REQUESTLOG)->set('objectId')->eq($sectransfer->id)->where('id')->eq($logID)->exec();
        $this->requestlog->response('success', $this->lang->api->successful, array('id' => $sectransfer->id), $logID);
    }

    /**
     * 校验
     * @return void
     */
    private function checkInput(){
        $errMsg = [];
        $this->loadModel('sectransfer');
        //校验是否存在异常字段
        foreach ($_POST as $key => $v)
        {
            if(!isset($this->lang->sectransfer->sendHandoverItems[$key])){
                $errMsg[] = $key."不是协议字段";
            }
        }

        if(!empty($errMsg)){
            return $errMsg;
        }

        foreach ($this->lang->sectransfer->sendHandoverItems as $k => $v)
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
