<?php
include '../../control.php';
class myApi extends api
{
    const PARAMS_MISSING = 1001; //缺少参数
    const PARAMS_ERROR   = 1002; //缺少参数
    const FAIL_CODE   = 999;    //请求失败

    public function sendJXTaskOrderStatus()
    {
        $logID = $this->loadModel('requestlog')->insideSaveRequestLog('secondorder' , 'sendJXTaskOrderStatus');
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
            if(
                $secondorder->status != 'delivered'
                and $secondorder->status != 'backed'
                and $secondorder->status != 'solved'
                and $secondorder->status != 'indelivery'
                and $secondorder->status != 'passed'
            ){
                $this->requestlog->response('fail', '当前数据状态不允许更新', [], $logID, self::FAIL_CODE);
            }
            if($this->post->taskListStatus == 'reject'){
                if($this->post->rejectUser == '' || $this->post->rejectReason == ''){
                    $this->requestlog->response('fail', '退回状态时拒绝人和拒绝原因必填', [], $logID, self::FAIL_CODE);
                }
            }
            //只有未受理工单才允许外部取消，其他状态不允许取消
            if($this->post->taskListStatus == 'cancel' && $secondorder->status != 'backed'){
                $this->requestlog->response('fail', '只有未受理工单支持取消操作', [], $logID, self::FAIL_CODE);
            }
            $secondorderID = $secondorder->id;
            $updateData = new stdClass();
            //状态转换
            //若是咨询评估类
            if($this->post->taskListStatus == 'pass'){
                $updateData->status = 'passed';
                $updateData->dealUser = 'guestjx';
                $updateData->externalTime = helper::now();
                $updateData->externalStatus = 'passed';
                $updateData->rejectUser = '';
                $updateData->rejectReason = '';
                $this->dao->update(TABLE_SECONDORDER)->data($updateData)->where('id')->eq($secondorder->id)->exec();
                $actionID = $this->loadModel('action')->create('secondorder', $secondorder->id, 'syncstatus', $this->post->note,'','guestjx');
                $changes = common::createChanges($secondorder, $updateData);
                if(!empty($changes)) $this->action->logHistory($actionID, $changes);
                $this->loadModel('consumed')->record('secondorder', $secondorder->id, 0, 'guestjx', $secondorder->status, $updateData->status, array());
            }else if($this->post->taskListStatus == 'reject'){
                if(
                    (
                        ($secondorder->type == 'consult' && 'sectransfer' == $secondorder->handoverMethod)
                        || $secondorder->type != 'consult'
                    )
                    && $secondorder->type != 'support'
                ){
                    $this->dao->update(TABLE_SECONDORDER)
                        ->set('status')->eq('indelivery')
                        ->set('dealUser')->eq('')
                        ->set('pushDate')->eq('')
                        ->where('id')->eq($secondorderID)
                        ->exec();
                    $this->loadModel('action')->create('secondorder',$secondorderID, 'syncstatusbyprotransfer');
                    $this->loadModel('consumed')->record('secondorder', $secondorderID, 0, 'guestjx', $secondorder->status, 'indelivery', array(), '');
                    //查询对外移交关联
                    $sectransfer = $this->dao->select('*')->from(TABLE_SECTRANSFER)->where('secondorderId')->eq($secondorder->id)
                        ->andWhere('deleted')->eq(0)->fetch();
                    $next = $this->dao->select('id')->from(TABLE_REVIEWNODE)->where('objectType')->eq('sectransfer')   //查找下一节点的状态
                    ->andWhere('objectID')->eq($sectransfer->id)
                        ->andWhere('version')->eq($sectransfer->version)
                        ->andWhere('stage')->eq('7')->fetch('id');
                    if($next)
                    {
                        $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('reject')->where('id')->eq($next)->exec();
                        $this->dao->update(TABLE_REVIEWER)
                            ->set('status')->eq('reject')
                            ->set('comment')->eq($this->post->rejectReason)
                            ->set('reviewTime')->eq(helper::now())
                            ->where('node')->eq($next)
                            ->exec();
                    }
                    $sectransferData = new stdClass();
                    $sectransferData->status = 'centerReject';
                    $sectransferData->approver = $sectransfer->sec; //二线专员
                    $sectransferData->rejectUser = $this->post->rejectUser;
                    $sectransferData->rejectReason = $this->post->rejectReason;
                    $sectransferData->externalTime =  helper::now();
                    $sectransferData->externalStatus = 'reject';
                    $sectransferData->rejectNum = $sectransfer->rejectNum+1;
                    $this->dao->update(TABLE_SECTRANSFER)->data($sectransferData)->where('id')->eq($sectransfer->id)->exec();
                    $this->loadModel('consumed')->record('sectransfer', $sectransfer->id, 0, 'guestjx', $sectransfer->status, $sectransferData->status, array());
                    $actionID = $this->loadModel('action')->create('sectransfer', $sectransfer->id, 'syncstatus', $this->post->note,'','guestjx');
                    $changes = common::createChanges($sectransfer, $sectransferData);
                    if(!empty($changes)) $this->action->logHistory($actionID, $changes);
                }else{
                    $updateData->status = 'returned';
                    //待处理人-二线专员
                    $userIds = array();
                    foreach ($this->lang->secondorder->JXApiDealUserList as $key => $value) {
                        $userIds[] = $key;
                    }
                    $updateData->dealUser = implode(",", $userIds);
                    $updateData->rejectUser = $this->post->rejectUser;
                    $updateData->rejectReason = $this->post->rejectReason;
                    $updateData->externalTime = helper::now();
                    $updateData->externalStatus = 'reject';

                    $this->dao->update(TABLE_SECONDORDER)->data($updateData)->where('id')->eq($secondorder->id)->exec();
                    $actionID = $this->loadModel('action')->create('secondorder', $secondorder->id, 'syncstatus', $this->post->note,'','guestjx');
                    $changes = common::createChanges($secondorder, $updateData);
                    if(!empty($changes)) $this->action->logHistory($actionID, $changes);
                    $this->loadModel('consumed')->record('secondorder', $secondorder->id, 0, 'guestjx', $secondorder->status, $updateData->status, array());
                }
            }else if($this->post->taskListStatus == 'closed'){
                $updateData->status = 'closed';
                $updateData->dealUser = '';
                $updateData->completionFeedback = $this->post->completionFeedback;
                $updateData->externalTime = helper::now();
                $updateData->externalStatus = 'closed';
                $updateData->rejectUser = '';
                $updateData->rejectReason = '';
                $updateData->closedBy = 'guestjx';
                $updateData->closedDate = helper::now();
                if($secondorder->type != 'consult' and $secondorder->type != 'support'){
                    //查询对外移交关联
                    $sectransfer = $this->dao->select('*')->from(TABLE_SECTRANSFER)->where('secondorderId')->eq($secondorder->id)->andWhere('deleted')->eq(0)->fetch();
                    $next = $this->dao->select('id')->from(TABLE_REVIEWNODE)->where('objectType')->eq('sectransfer')   //查找下一节点的状态
                    ->andWhere('objectID')->eq($sectransfer->id)
                        ->andWhere('version')->eq($sectransfer->version)
                        ->andWhere('stage')->eq('7')->fetch('id');
                    if($next)
                    {
                        $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('pass')->where('id')->eq($next)->exec();
                        $this->dao->update(TABLE_REVIEWER)
                            ->set('status')->eq('pass')
                            ->set('comment')->eq($updateData->rejectReason)
                            ->set('reviewTime')->eq(helper::now())
                            ->where('node')->eq($next)
                            ->exec();
                    }
                    $sectransferData = new stdClass();
                    $sectransferData->approver = '';
                    $sectransferData->status = 'alreadyEdliver';
                    $sectransferData->externalTime =  helper::now();
                    $sectransferData->externalStatus = 'pass';
                    $this->dao->update(TABLE_SECTRANSFER)->data($sectransferData)->where('id')->eq($sectransfer->id)->exec();
                    $this->loadModel('consumed')->record('sectransfer', $sectransfer->id, 0, 'guestjx', $sectransfer->status, $sectransferData->status, array());
                    $actionID = $this->loadModel('action')->create('sectransfer', $sectransfer->id, 'syncstatus', $this->post->note,'','guestjx');
                    $changes = common::createChanges($sectransfer, $sectransferData);
                    if(!empty($changes)) $this->action->logHistory($actionID, $changes);

                    $this->dao->update(TABLE_SECONDORDER)->data($updateData)->where('id')->eq($secondorder->id)->exec();
                    $actionID = $this->loadModel('action')->create('secondorder', $secondorder->id, 'syncstatus', $this->post->note,'','guestjx');
                    $changes = common::createChanges($secondorder, $updateData);
                    if(!empty($changes)) $this->action->logHistory($actionID, $changes);
                    $this->loadModel('consumed')->record('secondorder', $secondorder->id, 0, 'guestjx', $secondorder->status, $updateData->status, array());
                }else{
                    $this->dao->update(TABLE_SECONDORDER)->data($updateData)->where('id')->eq($secondorder->id)->exec();
                    $actionID = $this->loadModel('action')->create('secondorder', $secondorder->id, 'syncstatus', $this->post->note,'','guestjx');
                    $changes = common::createChanges($secondorder, $updateData);
                    if(!empty($changes)) $this->action->logHistory($actionID, $changes);
                    $this->loadModel('consumed')->record('secondorder', $secondorder->id, 0, 'guestjx', $secondorder->status, $updateData->status, array());
                }
            }else if($this->post->taskListStatus == 'cancel'){//紧急需求：清总任务工单反馈增加取消状态，金科任务单置为已关闭
                $updateData->status = 'closed';
                $updateData->dealUser = '';
                $updateData->completionFeedback = $this->post->completionFeedback;
                $updateData->externalTime = helper::now();
                $updateData->externalStatus = 'closed';
                $updateData->rejectUser = '';
                $updateData->rejectReason = '';
                $updateData->closedBy = 'guestjx';
                $updateData->closedDate = helper::now();
                $updateData->closeReason = $this->post->rejectReason;

                $this->dao->update(TABLE_SECONDORDER)->data($updateData)->where('id')->eq($secondorder->id)->exec();
                $actionID = $this->loadModel('action')->create('secondorder', $secondorder->id, 'syncstatus', $this->post->note,'','guestjx');
                $changes = common::createChanges($secondorder, $updateData);
                if(!empty($changes)) $this->action->logHistory($actionID, $changes);
                $this->loadModel('consumed')->record('secondorder', $secondorder->id, 0, 'guestjx', $secondorder->status, $updateData->status, array());
            }else{
                $this->requestlog->response('fail', '状态值不符合范围', [], $logID, self::FAIL_CODE);
            }
        }else{
            $errMsg[] = "该ID不存在";
            $this->requestlog->response('fail', implode(',',$errMsg), [], $logID, self::FAIL_CODE);
        }

        if(dao::isError()) {
            $this->requestlog->response('fail', dao::getError(), [], $logID, self::FAIL_CODE);
        }
        if($this->post->taskListStatus == 'pass'){
            $this->orderByClose($secondorderID);
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
            if(!isset($this->lang->secondorder->sendTaskOrderStatusItems[$key])){
                $errMsg[] = $key."不是协议字段";
            }
        }

        if(!empty($errMsg)){
            return $errMsg;
        }

        foreach ($this->lang->secondorder->sendTaskOrderStatusItems as $k => $v)
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

    public function orderByClose($secondOrderId)
    {
        $secondorder = $this->dao->select('*')->from(TABLE_SECONDORDER)->where('id')->eq($secondOrderId)->fetch();
        $this->dao
            ->update(TABLE_SECONDORDER)
            ->set('status')->eq('closed')
            ->set('dealUser')->eq('')
            ->set('closedBy')->eq('guestjk')
            ->set('closedDate')->eq(helper::now())
            ->set('closeReason')->eq('外部已通过，工单自动关闭')
            ->where('id')->eq($secondorder->id)
            ->exec();

        $this->loadModel('consumed')->record('secondorder', $secondorder->id, 0, 'guestjk', $secondorder->status, 'closed', array());
    }
}
