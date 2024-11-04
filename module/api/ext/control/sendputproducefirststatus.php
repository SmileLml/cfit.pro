<?php
include '../../control.php';
class myApi extends api
{
    const PARAMS_MISSING = 1001; //缺少参数
    const PARAMS_ERROR   = 1002; //缺少参数
    const FAIL_CODE   = 999;    //请求失败

    public function sendPutProduceFirstStatus()
    {
        $postData = fixer::input('post')->get();
        $logID = $this->loadModel('requestlog')->insideSaveRequestLog('putproduction' , 'sendPutProduceFirstStatus');
        $this->checkApiToken();
        $this->loadModel('putproduction');
        if(empty($postData->id)){
            $this->requestlog->response('fail', '单号id不能为空', [], $logID, self::FAIL_CODE);
        }
        //查找数据
        $putproduction = $this->dao->select("*")
            ->from(TABLE_PUTPRODUCTION)
            ->where('code')->eq($postData->id)
            ->andWhere('deleted')->eq('0')
            ->fetch();

        //判断数据库是否存在记录
        if(!empty($putproduction->id)){
            //只有已交付状态才更新数据
            if($putproduction->status != 'waitexternalreview'){
                $this->requestlog->response('fail', '当前数据状态不允许更新', [], $logID, self::FAIL_CODE);
            }
            if($postData->status == 'reject'){
                if($postData->rejectBy == '' || $postData->rejectReason == ''){
                    $this->requestlog->response('fail', '退回状态时拒绝人和拒绝原因必填', [], $logID, self::FAIL_CODE);
                }
                preg_match_all("/./u", $postData->rejectReason, $ar);
                if(count($ar[0]) < 10){
                    $this->requestlog->response('fail', '退回原因字符长度不能小于10个字符', [], $logID, self::FAIL_CODE);
                }
            }
            $updateData = array();
            //状态转换
            if($postData->status == 'pass'){
                if($putproduction->stage == '1'){
                    $res = $this->loadModel('iwfp')->completeTaskWithClaim($putproduction->workflowId, 'guestjx', '', '1', '',$putproduction->version);
                    if(dao::isError()){
                        return false;
                    }
                    $updateData['status'] = 'filepass';
                    $updateData['isOnLine'] = $postData->bonlineCondition == '是'?'1':'2';
                    $updateData['dealUser'] = '';
                    $updateData['returnBy'] = '';
                    $updateData['returnTel'] = '';
                    $updateData['returnReason'] = '';
                    $updateData['returnDate'] = '';
                    $updateData['opResult'] = 'filepass';
                    $this->loadModel('consumed')->record('putproduction', $putproduction->id, 0, 'guestjx', $putproduction->status, $updateData['status'], array());
                }else{
                    $updateData['status'] = 'filepass';
                    $updateData['isOnLine'] = $postData->bonlineCondition == '是'?'1':'2';
                    $updateData['dealUser'] = '';
                    $updateData['returnBy'] = '';
                    $updateData['returnTel'] = '';
                    $updateData['returnReason'] = '';
                    $updateData['returnDate'] = '';
                    $updateData['opResult'] = 'filepass';
                    $this->loadModel('consumed')->record('putproduction', $putproduction->id, 0, 'guestjx', $putproduction->status, $updateData['status'], array());
                }
            }else if($postData->status == 'reject'){
                $res = $this->loadModel('iwfp')->completeTaskWithClaim($putproduction->workflowId, 'guestjx', $postData->rejectReason, '2', '',$putproduction->version);
                if(dao::isError()){
                    return false;
                }
                $updateData['status'] = $res->toXmlTask;
                $updateData['returnBy'] = $postData->rejectBy;
                $updateData['returnTel'] = $postData->rejectContact;
                $updateData['returnReason'] = $postData->rejectReason;
                $updateData['returnCount'] = $putproduction->returnCount+1;
                $updateData['returnDate'] = helper::now();
                $updateData['isOnLine'] = $postData->bonlineCondition == '是'?'1':'2';
                $updateData['dealUser'] = is_array($res->dealUser) ? implode(',', $res->dealUser):$res->dealUser;
                $this->loadModel('consumed')->record('putproduction', $putproduction->id, 0, 'guestjx', $putproduction->status, $updateData['status'], array());
            }else{
                $this->requestlog->response('fail', '状态值不符合范围', [], $logID, self::FAIL_CODE);
            }
            $this->dao->update(TABLE_PUTPRODUCTION)->data($updateData)->where('id')->eq($putproduction->id)->exec();
            $actionID = $this->loadModel('action')->create('putproduction', $putproduction->id, 'syncstatus', $this->post->note,'','guestjx');
            $changes = common::createChanges($putproduction, $updateData);
            if(!empty($changes)) $this->action->logHistory($actionID, $changes);
            //与需求条目状态联动 第二阶段且待外部审批
            if(in_array($updateData['status'],$this->lang->putproduction->changeDemandStatus) && strstr($putproduction->stage,'2'))
            {
                /* @var demandModel $demandModel*/
                $demandModel = $this->loadModel('demand');
                $dataParams = $this->dao->select("*")->from(TABLE_PUTPRODUCTION)->where('id')->eq($putproduction->id)->fetch();
                $demandModel->putproductionAndDemandStatusChange($putproduction->demandId,$dataParams,$updateData['status']);
            }
        }else{
            $errMsg[] = "该ID不存在";
            $this->requestlog->response('fail', implode(',',$errMsg), [], $logID, self::FAIL_CODE);
        }

        if(dao::isError()) {
            $this->requestlog->response('fail', dao::getError(), [], $logID, self::FAIL_CODE);
        }
        $this->dao->update(TABLE_REQUESTLOG)->set('objectId')->eq($putproduction->id)->where('id')->eq($logID)->exec();
        $this->requestlog->response('success', $this->lang->api->successful, array('id' => $putproduction->id), $logID);
    }
}
