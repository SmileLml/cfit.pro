<?php
include '../../control.php';
class myApi extends api
{   
    const PARAMS_MISSING = 1001; //缺少参数
    const PARAMS_ERROR   = 1002; //参数错误

    public function modifycnccfeedback()
    {   
        //record log
        $logID = $this->loadModel('requestlog')->insideSaveRequestLog('modifycncc' , 'modifycnccfeedback');
        $this->requestlog->judgeRequestMode($logID);

        //判断所需字段是否存在
        $data = fixer::input('post')->get();

        if($data->changeStatus=='变更退回')
        {
            $this->lang->modifycncc->apiFeedbackstateItems['reason']['required'] = 1;
        }

        $errMsg = $this->checkInput();
        if(!empty($errMsg)) {
            $this->requestlog->response('fail', implode(',',$errMsg).'不可以为空', [], $logID, self::PARAMS_MISSING);
        }

        // 判断生产变更单是否存在   
        $modifycncc = $this->dao->select('*')->from(TABLE_MODIFYCNCC)->where('code')->eq($data->changeOrderId)->fetch();
        if(empty($modifycncc))
        {
            $this->requestlog->response('fail', 'changeOrderId'.'生产变更单不存在', array(), $logID);
        }



        //更新生产变更单，记录日志
        $updateData = new stdClass();
        //todo operationType 枚举取值
        $updateData->operationName      = $data->operationName;
        $updateData->feedBackOperationType = $data->feedBackOperationType;
        $updateData->executionResults   = $data->executionResults;
        $updateData->planBegin          = date('Y-m-d H:i:s',$data->startTime/1000);
        $updateData->planEnd            = date('Y-m-d H:i:s',$data->endTime/1000);
        $updateData->depOddName         = $data->depOddName;
        $updateData->supply             = $data->supportStaff;
        $updateData->changeNum          = $data->changeNum;
        $updateData->operationStaff     = $data->operationStaff;
        $updateData->problemDescription = $data->problemDescription;
        $updateData->resolveMethod      = $data->resolveMethod;
        $updateData->feedBackId         = $data->feedBackId;

        $this->dao->begin();
        $this->dao->update(TABLE_MODIFYCNCC)->data($updateData)->where('id')->eq($modifycncc->id)->exec();
        $changes = common::createChanges($modifycncc, $updateData);
        //记录日志
        $action = 'modifycnccsyncfeedback';
        if($modifycncc->isSyncFeedback == '1'){
            $action = 'modifycncceditfeedback';
        }else{
            $this->dao->update(TABLE_MODIFYCNCC)->set('isSyncFeedback')->eq('1')->where('id')->eq($modifycncc->id)->exec();
        }

        $outwarddelivery = $this->dao->select("id")->from(TABLE_OUTWARDDELIVERY)
            ->where('modifycnccId')->eq($modifycncc->id)
            ->andWhere('isNewModifycncc')->eq('1')
            ->fetch();

        $this->loadModel('demand')->changeBySecondLineV4($outwarddelivery->id,'outwarddelivery');
        $this->dao->commit();
        $actionID = $this->loadModel('action')->create('modifycncc', $modifycncc->id, $action, '', '', 'guestcn');
        $actionID2 = $this->loadModel('action')->create('outwarddelivery', $outwarddelivery->id, $action, '', '', 'guestcn');
        $this->action->logHistory($actionID, $changes);
        $this->action->logHistory($actionID2, $changes);

        //接口 日志
        $this->requestlog->response('success', $this->lang->api->successful, array('changeOrderId' => $modifycncc->code), $logID);

    }


     
     private function checkInput()
     {
         $this->loadModel('modifycncc');
         $errMsg = [];
         foreach ($this->lang->modifycncc->apiFeedbackItems as $k => $v)
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