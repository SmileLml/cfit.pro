<?php
include '../../control.php';
class myApi extends api
{   
    const PARAMS_MISSING = 1001; //缺少参数
    const PARAMS_ERROR   = 1002; //参数错误

    public function modifycnccfeedbackstate()
    {   
        $this->loadModel('modifycncc');
        //record log
        $logID = $this->loadModel('requestlog')->insideSaveRequestLog('modifycncc' , 'modifycnccfeedbackstate');
        $this->requestlog->judgeRequestMode($logID);

        //判断所需字段是否存在
        $data = fixer::input('post')->get();
        if(in_array($_POST['changeStatus'], $this->lang->modifycncc->returnStatusList))
        {
            $this->lang->modifycncc->apiFeedbackstateItems['reason']['required'] = 1;
        }else if(!in_array($_POST['changeStatus'], $this->lang->modifycncc->noRequiredList)){
            $this->lang->modifycncc->apiFeedbackstateItems['changeRemark']['required'] = 1;
            $this->lang->modifycncc->apiFeedbackstateItems['realStartTime']['required'] = 1;
            $this->lang->modifycncc->apiFeedbackstateItems['realEndTime']['required'] = 1;
        }


        $errMsg = $this->checkInput();
        if(!empty($errMsg)) {
            $this->requestlog->response('fail', implode(',',$errMsg).'不可以为空', [],$logID, self::PARAMS_MISSING);
        }

        // 判断生产变更单是否存在   
        $modifycncc = $this->dao->select('*')->from(TABLE_MODIFYCNCC)->where('code')->eq($data->changeOrderId)->fetch();
        if(empty($modifycncc))
        {
            $this->requestlog->response('fail', 'changeOrderId'.'生产变更单不存在', array(), $logID);
        }

        //更新生产变更单，记录日志
        $updateData = new stdClass();
        //todo changeStatus 枚举取值
        $updateData->changeStatus  = array_search($data->changeStatus,$this->lang->modifycncc->changeStatusList);
        $updateData->status  = array_search($data->changeStatus,$this->lang->modifycncc->statusList);
        if($_POST['changeStatus'] =='gitee打回'){
            $updateData->changeStatus  = 'giteeback';
            $updateData->status  = 'giteeback';
            if(empty($modifycncc->returnLog)){
                $returnLogArray = array();
            }else{
                $returnLogArray = json_decode($modifycncc->returnLog);
            }
            $returnLog = new stdClass();
            $returnLog->date = helper::now();
            $returnLog->node = $_POST['node'];
            $returnLog->dealUser = $_POST['approverName'];
            $returnLog->reason = $data->reason;
            array_push($returnLogArray, $returnLog);
            $updateData->returnLog = json_encode($returnLogArray);
        }
        if(!empty($data->changeRemark)){$updateData->changeRemark  = $data->changeRemark;}
        if(!empty($data->realStartTime)){$updateData->actualBegin   = date('Y-m-d H:i:s',$data->realStartTime/1000);}
        if(!empty($data->realEndTime)){$updateData->actualEnd     = date('Y-m-d H:i:s',$data->realEndTime/1000);}
        if(!empty($data->reason)){$updateData->reasoncncc    = $data->reason;}
        if(!empty($data->approverName)){$updateData->approverName    = $data->approverName;}

        $updateData->feedbackDate  = date('Y-m-d H:i:s',time());
        if($updateData->changeStatus == ''){
            $this->requestlog->response('fail', '变更状态不在金科枚举范围内,请通知金科增加枚举值', [], $logID, self::PARAMS_ERROR);
        }

        $this->dao->update(TABLE_MODIFYCNCC)->data($updateData)->where('id')->eq($modifycncc->id)->exec();

        //$this->dao->select("testingRequestId,productEnrollId,ifMediumChanges,productInfoCode,'release'")->from(TABLE_OUTWARDDELIVERY)->where('modifycnccId')->eq($modifycnccId)->fetch();
        $this->loadModel('outwarddelivery');
        //改变父表单状态
        $outwarddelivery = $this->dao->select("*")->from(TABLE_OUTWARDDELIVERY)
            ->where('modifycnccId')->eq($modifycncc->id)
            ->andWhere('isNewModifycncc')->eq('1')
            ->fetch();

        $changeStatus  = array_search($data->changeStatus,$this->lang->outwarddelivery->statusList);
        if($data->changeStatus =='gitee打回'){
            $changeStatus  = 'giteeback';
        }
        $dealUser = '';
        $currentReview = '5';
//        or $changeStatus == 'modifyreject' 变更退回待处理人置空
        if($changeStatus == 'psdlreview' or $changeStatus == 'giteepass'){
            $dealUser = 'guestcn';
            $currentReview = '4';
        }
        $this->dao->begin();
        $this->dao->update(TABLE_OUTWARDDELIVERY)
            ->set('status')->eq($changeStatus)
            ->set('dealUser')->eq($dealUser)
            ->set('currentReview')->eq($currentReview)
            //->set('reviewFailReason')->eq($reviewFailReason)
            ->where('modifycnccId')->eq($modifycncc->id)->exec();
        if($data->changeStatus == 'gitee打回'){
            //变更退回需要记录退回次数
            $this->dao->update(TABLE_MODIFYCNCC)->set('returnTimes = returnTimes+1')->where('id')->eq($modifycncc->id)->exec();

            $this->loadModel('outwarddelivery');
            $this->app->loadLang('outwarddelivery');
            $reviewers = $this->lang->outwarddelivery->apiDealUserList['userAccount'];
            $this->dao->update(TABLE_OUTWARDDELIVERY)->set('rejectTimes = rejectTimes+1')->set('dealUser')->eq($reviewers)->set('currentReview="1"')->where('modifycnccId')->eq($modifycncc->id)->exec();
        }
        //tangfei 增加状态流转
        $this->loadModel('consumed')->record('outwarddelivery', $outwarddelivery->id, '0', 'guestcn', $outwarddelivery->status, $changeStatus, array(),'生产变更单');

        $changes = common::createChanges($modifycncc, $updateData);
        //记录日志
        $action = 'modifycnccsyncstatus';
        if($modifycncc->isSyncState == '1'){
            $action = 'modifycncceditstatus';
        }else{
            $this->dao->update(TABLE_MODIFYCNCC)->set('isSyncState')->eq('1')->where('id')->eq($modifycncc->id)->exec();
        }
        if ($_POST['changeStatus'] != '变更退回'){
            $this->loadModel('demand')->changeBySecondLineV4($outwarddelivery->id,'outwarddelivery');
        }
        $this->dao->commit();
        $actionID = $this->loadModel('action')->create('modifycncc', $modifycncc->id, $action, $data->reason, '', 'guestcn');
        $actionID2 = $this->loadModel('action')->create('outwarddelivery', $outwarddelivery->id, $action, $data->reason, '', 'guestcn');
        $this->action->logHistory($actionID, $changes);
        $this->action->logHistory($actionID2, $changes);
        //追加审批节点
        $outwarddelivery = $this->dao->select("*")->from(TABLE_OUTWARDDELIVERY)
            ->where('modifycnccId')->eq($modifycncc->id)
            ->andWhere('isNewModifycncc')->eq('1')
            ->fetch();
        $reviewFailReason = $this->loadModel('outwarddelivery')->getHistoryReview($outwarddelivery, 3);
        $this->dao->update(TABLE_OUTWARDDELIVERY)
            ->set('reviewFailReason')->eq($reviewFailReason)
            ->where('modifycnccId')->eq($modifycncc->id)->exec();
        //接口 日志
        $this->requestlog->response('success', $this->lang->api->successful, array('changeOrderId' => $modifycncc->code), $logID);

    }


     
     private function checkInput()
     {
         $this->loadModel('modifycncc');
         $errMsg = [];
         foreach ($this->lang->modifycncc->apiFeedbackstateItems as $k => $v)
         {
             if($v['required'] && $this->post->$k == ''){
                 $errMsg[] = $k.$v['name'].$this->post->$k;
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