<?php
include '../../control.php';
class myApi extends api
{
    const PARAMS_MISSING = 1001; //缺少参数
    const PARAMS_ERROR   = 1002; //缺少参数
    const FAIL_CODE   = 999;    //请求失败

    public function sendPutProduceSecondStatus()
    {
        $postData = fixer::input('post')->get();
        $logID = $this->loadModel('requestlog')->insideSaveRequestLog('putproduction' , 'sendPutProduceSecondStatus');
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
            if($putproduction->stage == '1'){
                $this->requestlog->response('fail', '一阶段的投产单不接受二阶段的投产结果', [], $logID, self::FAIL_CODE);
            }
            //只有已交付状态才更新数据
            if($putproduction->status != 'waitexternalreview' && $putproduction->status != 'filepass'){
                $this->requestlog->response('fail', '当前数据状态不允许更新', [], $logID, self::FAIL_CODE);
            }
            if(empty($this->lang->putproduction->externalStatusArray[$postData->status])){
                $this->requestlog->response('fail', '投产状态不在候选值内', [], $logID, self::FAIL_CODE);
            }
            $updateData = array();
            //状态转换
            $res = $this->loadModel('iwfp')->completeTaskWithClaim($putproduction->workflowId, 'guestjx', $postData->failReason, '1', '',$putproduction->version);
            if(dao::isError()){
                return false;
            }
            $updateData['status'] = $this->lang->putproduction->externalStatusArray[$postData->status];
            $updateData['dealUser'] = '';
            $updateData['opFailReason'] = $postData->failReason;
            $updateData['realStartTime'] = $postData->startTime;
            $updateData['realEndTime'] = $postData->endTime;
            $updateData['planStartTime'] = $postData->expectedStartTime;
            $updateData['planEndTime'] = $postData->expectedEndTime;
            $updateData['implementedBy'] = $postData->implementedBy;
            $updateData['opResult'] = $postData->status;
            $updateData['planUsedWindow'] = $postData->planUsedWindow;
            $updateData['planUsedWindowReason'] = $postData->planUsedWindowReason;
            $updateData['realUsedWindow'] = $postData->realUsedWindow;
            $updateData['realUsedWindowReason'] = $postData->realUsedWindowReason;
            $updateData['appCauseFail'] = $postData->appCauseFail;
            $this->loadModel('consumed')->record('putproduction', $putproduction->id, 0, 'guestjx', $putproduction->status, $updateData['status'], array());
            $this->dao->update(TABLE_PUTPRODUCTION)->data($updateData)->where('id')->eq($putproduction->id)->exec();
            $actionID = $this->loadModel('action')->create('putproduction', $putproduction->id, 'syncstatus', $this->post->note,'','guestjx');
            $changes = common::createChanges($putproduction, $updateData);
            if(!empty($changes)) $this->action->logHistory($actionID, $changes);
            //与需求条目状态联动 第二阶段
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
