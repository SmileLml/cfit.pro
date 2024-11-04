<?php
include 'Sm4Helper.php';
class iwfpModel extends model
{
    /**
     * 保存配置
     * @return void
     */
    public function setPush()
    {
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.startWorkFlowUrl', $this->post->startWorkFlowUrl);
        $this->setting->setItem('system.common.global.getButtonListUrl', $this->post->getButtonListUrl);
        $this->setting->setItem('system.common.global.completeTaskWithClaimUrl', $this->post->completeTaskWithClaimUrl);
        $this->setting->setItem('system.common.global.getToDoTaskListUrl', $this->post->getToDoTaskListUrl);
        $this->setting->setItem('system.common.global.listApproveLogUrl', $this->post->listApproveLogUrl);
        $this->setting->setItem('system.common.global.turnBackUrl', $this->post->turnBackUrl);
        $this->setting->setItem('system.common.global.getFreeJumpNodeListUrl', $this->post->getFreeJumpNodeListUrl);
        $this->setting->setItem('system.common.global.freeJumpUrl', $this->post->freeJumpUrl);
        $this->setting->setItem('system.common.global.withDrawUrl', $this->post->withDrawUrl);
        $this->setting->setItem('system.common.global.addSignTaskUrl', $this->post->addSignTaskUrl);
        $this->setting->setItem('system.common.global.changeAssigneekUrl', $this->post->changeAssigneekUrl);
        $this->setting->setItem('system.common.global.queryProcessTrackImageUrl', $this->post->queryProcessTrackImageUrl);
        $this->setting->setItem('system.common.global.completeTaskUrl', $this->post->completeTaskUrl);
        $this->setting->setItem('system.common.global.getTaskDefListUrl', $this->post->getTaskDefListUrl);

        $this->setting->setItem('system.common.global.jxPutproductionKey', $this->post->jxPutproductionKey);
        $this->setting->setItem('system.common.global.jxPutproductionId', $this->post->jxPutproductionId);
        $this->setting->setItem('system.common.global.tjCreditKey', $this->post->tjCreditKey);
        $this->setting->setItem('system.common.global.tjCreditId', $this->post->tjCreditId);
        $this->setting->setItem('system.common.global.productionchangeKey', $this->post->productionchangeKey);
        $this->setting->setItem('system.common.global.productionchangeId', $this->post->productionchangeId);
        $this->setting->setItem('system.common.global.preproductionTempId', $this->post->preproductionTempId);

        $this->setting->setItem('system.common.global.tenantId', $this->post->tenantId);
        $this->setting->setItem('system.common.global.AuthorizationKey', $this->post->AuthorizationKey);

        $this->setting->setItem('system.common.global.localesupportKey', $this->post->localesupportKey);
        $this->setting->setItem('system.common.global.localesupportId', $this->post->localesupportId);
        $this->setting->setItem('system.common.global.localesupportTempId', $this->post->localesupportTempId);

        $this->setting->setItem('system.common.global.authorityapplyKey', $this->post->authorityapplyKey);
        $this->setting->setItem('system.common.global.authorityapplyId', $this->post->authorityapplyId);
        $this->setting->setItem('system.common.global.authorityapplyTempId', $this->post->authorityapplyTempId);

        $this->setting->setItem('system.common.global.environmentorderKey', $this->post->environmentorderKey);
        $this->setting->setItem('system.common.global.environmentorderId', $this->post->environmentorderId);
        $this->setting->setItem('system.common.global.environmentorderTempId', $this->post->environmentorderTempId);

        $this->setting->setItem('system.common.global.qualitygateKey', $this->post->qualitygateKey);
        $this->setting->setItem('system.common.global.qualitygateId', $this->post->qualitygateId);
    }
    /******                         V1.0版本   适配全是竞争节点并且发起流程时已确认待处理人和节点                          *******/

    /**
     * 发起流程
     *
     * @param $objectType 模块类型
     * @param $objectId  数据id
     * @param $objectCode 数据编号
     * @param $createdBy 创建人
     * @param $nodeDealUser 审核节点以及审核节点人
     * @param $version 版本
     * @param $logNodeArray
     * @param string $processInstanceId 流程实例Id
     * @return stdClas
     */
    public function startWorkFlow($objectType, $objectId, $objectCode, $createdBy, $nodeDealUser, $version, $logNodeArray, $processInstanceId = ''){
        $response = new stdClass();
        //1、校验参数
        if(empty($objectType) || empty($objectId) || empty($objectCode) || empty($createdBy) || empty($nodeDealUser)){
            return dao::$errors[''] = $this->lang->iwfp->errorMessageList['emptyError'];
        }
        //2、根据模块类型查找流程模版定义Key和流程模版定义Id
        $definitionKey = $this->lang->iwfp->templateKeyList[$objectType];
        $definitionId = $this->lang->iwfp->templateIdList[$objectType];
        $objectDefinitionKey = $this->config->global->$definitionKey;
        $objectDefinitionId = $this->config->global->$definitionId;
        if(empty($objectDefinitionKey) || empty($objectDefinitionId)){
            return dao::$errors[''] = $this->lang->iwfp->errorMessageList['templateConfigEmpty'];
        }
        $newProcessInstanceId = $processInstanceId;
        //3、若流程实例为空，发起流程
        if(empty($processInstanceId)){
            $result = $this->sendStartWorkFlow($objectDefinitionKey, $objectCode, $objectId, $objectType, $createdBy);
            if(!empty($result)){
                $resultData = json_decode($result);
                if ($resultData->success == '1') {
                    //4.1插入数据
                    $createData = new stdClass();
                    $createData->processInstanceId = $resultData->data->processInstanceId;
                    $createData->processDefinitionKey = $resultData->data->processDefinitionKey;
                    $createData->processDefinitionId = $resultData->data->processDefinitionId;
                    $createData->objectType = $objectType;
                    $createData->objectID = $objectId;
                    $createData->objectCode = $objectCode;
                    $createData->status = 'running';
                    $createData->createdDate = helper::now();
                    $createData->createdBy = $createdBy;
                    $createData->delete = 1;
                    $createData->nodeDealUser = json_encode($nodeDealUser);
                    $this->dao->insert(TABLE_IWFP)->data($createData)->exec();
                    $response->processInstanceId = $resultData->data->processInstanceId;
                    $response->processDefinitionKey = $resultData->data->processDefinitionKey;
                    $response->processDefinitionId = $resultData->data->processDefinitionId;
                    $newProcessInstanceId = $resultData->data->processInstanceId;
                }else{
                    return dao::$errors[''] = $resultData->errorMessage;
                }
            }else{
                return dao::$errors[''] = $this->lang->iwfp->errorMessageList['networkError'];
            }
        }else {
            //5、若流程实例不为空
            $iwfp = $this->getIwfpByProcessInstanceId($processInstanceId);
            if(empty($iwfp)){
                return dao::$errors[''] = $this->lang->iwfp->errorMessageList['iwfpEmpty'];
            }
            //5.1、判断当前模块的流程模版定义Key和流程模版定义Id和原数据保存的流程模版定义Key和流程模版定义Id是否一致，如果一致不重新发起流程，直接返回流程实例id;
            if($iwfp->processDefinitionKey == $objectDefinitionKey && $iwfp->processDefinitionId == $objectDefinitionId){
                $response->processInstanceId = $processInstanceId;
                $response->processDefinitionKey = $iwfp->processDefinitionKey;
                $response->processDefinitionId = $iwfp->processDefinitionId;
                $nodeDealUserStr = json_encode($nodeDealUser);
                $this->dao->update(TABLE_IWFP)->set('nodeDealUser')->eq($nodeDealUserStr)->where('processInstanceId')->eq($processInstanceId)->exec();
                $newProcessInstanceId = $processInstanceId;
            }else{
                //5.2、如果不一致，重新发起流程
                $result = $this->sendStartWorkFlow($objectDefinitionKey, $objectCode, $objectId, $objectType, $createdBy);
                if(!empty($result)){
                    $resultData = json_decode($result);
                    if ($resultData->success == '1') {
                        //5.3、将之前的流程数据设置为挂起
                        $this->dao->update(TABLE_IWFP)->set('status')->eq('pending')->where('processInstanceId')->eq($processInstanceId)->exec();
                        //5.4插入数据
                        $createData = new stdClass();
                        $createData->processInstanceId = $resultData->data->processInstanceId;
                        $createData->processDefinitionKey = $resultData->data->processDefinitionKey;
                        $createData->processDefinitionId = $resultData->data->processDefinitionId;
                        $createData->objectType = $objectType;
                        $createData->objectID = $objectId;
                        $createData->objectCode = $objectCode;
                        $createData->status = 'running';
                        $createData->createdDate = helper::now();
                        $createData->createdBy = $createdBy;
                        $createData->delete = 1;
                        $createData->nodeDealUser = json_encode($nodeDealUser);
                        $this->dao->insert(TABLE_IWFP)->data($createData)->exec();
                        $response->processInstanceId = $resultData->data->processInstanceId;
                        $response->processDefinitionKey = $resultData->data->processDefinitionKey;
                        $response->processDefinitionId = $resultData->data->processDefinitionId;
                        $newProcessInstanceId = $resultData->data->processInstanceId;
                    }else{
                        return dao::$errors[''] = $resultData->errorMessage;
                    }
                }else{
                    return dao::$errors[''] = $this->lang->iwfp->errorMessageList['networkError'];
                }
            }
        }
        $updateData = new stdClass();
        //获取下一节点taskid
        if(empty($newProcessInstanceId)){
            return dao::$errors[''] = $this->lang->iwfp->errorMessageList['instanceIdEmpty'];
        }
        $toDoListResult = $this->getToDoTaskList($newProcessInstanceId, '', '0', $objectCode);
        if(empty($toDoListResult)){
            return dao::$errors[''] = $this->lang->iwfp->errorMessageList['networkError'];
        }
        $toDoListData = json_decode($toDoListResult);
        if($toDoListData->success != '1'){
            return dao::$errors[''] = $toDoListData->errorMessage;
        }
        $toDoList = $toDoListData->data;
        $taskId = '';
        $xmlTaskId = '';
        foreach ($toDoList as $toDo){
            if(!empty($toDo)){
                $taskId = $toDo->taskId;
                $xmlTaskId = $toDo->xmlTaskId;
            }
        }
        $updateData->processTaskId = $taskId;
        $updateData->processXmlTaskId = $xmlTaskId;
        //下一节点待处理人
        if($createdBy != 'admin'){
            $dealUser = $createdBy.',admin';
        }else{
            $dealUser = $createdBy;
        }
        $updateData->dealUser = $dealUser;
        //构建审批信息
        $allLogList = array();
        $logList = array();
        if(empty($processInstanceId)){
            foreach ($logNodeArray as $logNode => $logValue){
                $log = new stdClass();
                $log->nodeName = $logNode;
                $log->toDealUser = $nodeDealUser[$logNode];
                $log->dealUser = '';
                if($xmlTaskId == $logNode){
                    $log->result = 'pending';
                }else{
                    $log->result = '';
                }
                $log->comment = '';
                $log->dealDate = '';
                $log->version = $version;
                if(!empty($log->toDealUser)){
                    array_push($logList, $log);
                }
            }
            $allLogList[$version] = $logList;
        }else{
            $allLogList = json_decode($iwfp->logList,true);
            foreach ($logNodeArray as $logNode=>$logValue){
                $log = new stdClass();
                $log->nodeName = $logNode;
                $log->toDealUser = $nodeDealUser[$logNode];
                $log->dealUser = '';
                if($xmlTaskId == $logNode){
                    $log->result = 'pending';
                }else{
                    $log->result = '';
                }
                $log->comment = '';
                $log->dealDate = '';
                $log->version = $version;
                if(!empty($log->toDealUser)){
                    array_push($logList, $log);
                }
            }
            $allLogList[$version] = $logList;
        }
        $updateData->logList = json_encode($allLogList);
        $this->dao->update(TABLE_IWFP)->data($updateData)->where('processInstanceId')->eq($newProcessInstanceId)->exec();
        return $response;
    }


    /**
     * 接受任务并处理任务
     * @param $processInstanceId    任务id
     * @param $dealUser 处理人
     * @param $dealMessage  处理意见
     * @param $dealResult   处理结果1-通过；2-不通过；3-撤回
     * @param $nextUserStr 下一节点处理人,根据逗号分割
     * @param $userVariableKeyList  用户变量
     * @return void
     */
    public function completeTaskWithClaim($processInstanceId, $dealUser, $dealMessage, $dealResult, $userVariableList, $version){
        $response = new stdClass();
        //1、校验参数
        if(empty($processInstanceId) || empty($dealUser) || empty($version)){
            return dao::$errors[''] = $this->lang->iwfp->errorMessageList['emptyError'];
        }
        $dealResultKeyList = array_keys($this->lang->iwfp->dealResultList);
        if(!in_array($dealResult, $dealResultKeyList)){
            return dao::$errors[''] = $this->lang->iwfp->errorMessageList['resultError'];
        }
        $iwfp = $this->getIwfpByProcessInstanceId($processInstanceId);
        if(empty($iwfp)){
            return dao::$errors[''] = $this->lang->iwfp->errorMessageList['iwfpEmpty'];
        }
        $objectCode = $iwfp->objectCode;
        //校验当前处理人是否是目前处理人
        $dealUserIwfp = explode(',',$iwfp->dealUser);
        if(!in_array($dealUser, $dealUserIwfp)){
            return dao::$errors[''] = $this->lang->iwfp->errorMessageList['todoEmpty'];
        }
        //查找委派字段，判断用户是否是委派权限
        $realDeal = $dealUser;
        if(!empty($iwfp->assign)){
            $assignList = json_decode($iwfp->assign,true);
            foreach ($assignList as $assign){
                if($assign['assignUserNo'] == $dealUser){
                    $realDeal = $assign['userNo'];
                }
            }
        }
        $taskId = $iwfp->processTaskId;
        $processXmlTaskId = $iwfp->processXmlTaskId;
        //3、查找该任务下的授权按钮
        $btnListResult = $this->getButtonList($taskId, $objectCode);

        if(empty($btnListResult)){
            return dao::$errors[''] = $this->lang->iwfp->errorMessageList['networkError'];
        }
        $btnListData = json_decode($btnListResult);

        if($btnListData->success != '1'){
            return dao::$errors[''] = $btnListData->errorMessage;
        }
        $btnList = $btnListData->data;

        //3、根据处理结果和用户参数来查找到下一节点的按钮
        $choseBtn = '';
        foreach ($btnList as $btn){
            $chose = true;
            if(!empty($btn->userVariable)){
                $btnUserVariableList = json_decode($btn->userVariable);
                foreach ($btnUserVariableList as $key=>$value){
                    if($key == 'skip'){
                        continue;
                    }else if($key == 'result'){
                        if($value != $dealResult){
                            $chose = false;
                        }
                    }else{
                        if(empty($userVariableList->$key) || $value != $userVariableList->$key){
                            $chose = false;
                        }
                    }
                }
            }else{
                $chose = false;
            }
            if($chose){
                $choseBtn = $btn;
                break;
            }
        }
        if(empty($choseBtn)){
            return dao::$errors[''] = $this->lang->iwfp->errorMessageList['btnEmpty'];
        }
//        echo '<pre>';
//        print_r($choseBtn);
//        echo '</pre>';

        //4、调用处理接口
        //4.1组装数据
        $url = $this->config->global->completeTaskWithClaimUrl;
        $pushData = array();
        $pushData['tenantId'] = $this->config->global->tenantId;
        $pushData['btnId'] = $choseBtn->btnId;
        $pushData['taskId'] = $taskId;
        $pushData['dealUserNo'] = $realDeal;
        $pushData['dealUserName'] = $realDeal;
        $dealMessageObject = new stdClass();
        //$dealMessageObject->dealMessage = $dealMessage;
        $dealMessageObject->dealResult = $dealResult;
        $dealMessageObject->version = $version;
        $dealMessageObject->dealUser = $dealUser;
        $dealMessageObject->todealUser = $dealUserIwfp;
        $pushData['dealMessage'] = json_encode($dealMessageObject);
        $nextUserList = array();
        $nodeDealUserList = json_decode($iwfp->nodeDealUser);
        $nextStatus = $choseBtn->xmlTaskId;
        if(!empty($nodeDealUserList->$nextStatus)){
            if(is_array($nodeDealUserList->$nextStatus)){
                $nextUserArray = $nodeDealUserList->$nextStatus;
            }else{
                $nextUserArray = explode(',' , $nodeDealUserList->$nextStatus);
            }
            $isAdmin = false;
            foreach ($nextUserArray as $nextUserObject) {
                $nextUser = new stdClass();
                $nextUser->userNo = $nextUserObject;
                $nextUser->userName = $nextUserObject;
                array_push($nextUserList, $nextUser);
                if($nextUserObject == 'admin'){
                    $isAdmin = true;
                }
            }
            if(!$isAdmin){
                $adminUser = new stdClass();
                $adminUser->userNo = 'admin';
                $adminUser->userName = 'admin';
                array_push($nextUserList, $adminUser);
            }
            $pushData['nextUserList'] = $nextUserList;
        }else{
            $nextUserArray = [];
        }
        if(empty($userVariableList)){
            $userVariableList = new stdClass();
        }
        $userVariableList->result = $dealResult;
        $pushData['userVariable'] = json_encode($userVariableList);
        $method = 'POST';
        $sendType = 'completeTaskWithClaim';
        $result = $this->sendIwfp($url, $pushData, $method, $sendType, $objectCode);
        if(empty($result)){
            return dao::$errors[''] = $this->lang->iwfp->errorMessageList['networkError'];
        }
        $resultData = json_decode($result);
        if($resultData->success != '1'){
            $completeTaskUrl = $this->config->global->completeTaskUrl;
            $sendType = 'completeTask';
            $result = $this->sendIwfp($completeTaskUrl, $pushData, $method, $sendType, $objectCode);
            if(empty($result)){
                return dao::$errors[''] = $this->lang->iwfp->errorMessageList['networkError'];
            }
            $resultData = json_decode($result);
            if($resultData->success != '1'){
                return dao::$errors[''] = $resultData->errorMessage;
            }
        }
        $isEnd = $resultData->data->isEnd;
        $toXmlTaskId = $resultData->data->clickedButtonDTO->toXmlTaskId;
        $toXmlTask =$toXmlTaskId;
        //流程结束修改表数据
        if($isEnd == 1){
            $this->dao->update(TABLE_IWFP)->set('status')->eq('closed')->where('processInstanceId')->eq($processInstanceId)->exec();
        }
        $response->isEnd = $isEnd;
        $response->toXmlTask = $toXmlTask;
        $response->dealUser = zget($nodeDealUserList, $nextStatus, []);
        $isSkip = false;
        //判断是否跳过相同处理人节点
        $chosebtnUserVariableList = json_decode($choseBtn->userVariable);
        foreach ($chosebtnUserVariableList as $key=>$value){
            if($key == 'skip'){
                if(!empty($value) and $value == 1){
                    $isSkip = true;
                }
            }
        }
        $updateData = new stdClass();
        //获取下一节点taskid
        $toDoListResult = $this->getToDoTaskList($processInstanceId, '', '0', $objectCode);
        if(empty($toDoListResult)){
            return dao::$errors[''] = $this->lang->iwfp->errorMessageList['networkError'];
        }
        $toDoListData = json_decode($toDoListResult);
        if($toDoListData->success != '1'){
            return dao::$errors[''] = $toDoListData->errorMessage;
        }

        $toDoList = $toDoListData->data;
        $taskId = '';
        $xmlTaskId = '';
        foreach ($toDoList as $toDo){
            if(!empty($toDo)){
                $taskId = $toDo->taskId;
                $xmlTaskId = $toDo->xmlTaskId;
            }
        }
        $updateData->processTaskId = $taskId;
        $updateData->processXmlTaskId = $xmlTaskId;
        //下一节点待处理人
        if(isset($nextUserArray) && !empty($nextUserArray)){
            $updateData->dealUser = implode(',',$nextUserArray).',admin';
        }
        //构建审批信息
        $allLogList = json_decode($iwfp->logList,true);
        $logList = $allLogList[$version];
        foreach ($logList as &$log){
            if($log['nodeName'] == $processXmlTaskId){
                if(in_array($dealUser, $nextUserArray) and $isSkip){
                    $log['result'] = 'ignore';
                    $log['comment'] = '';
                }else{
                    $log['result'] = $this->lang->iwfp->dealResultKeyList[$dealResult];
                    $log['comment'] = $dealMessage;
                }
                $log['dealDate'] = helper::now();
                $log['dealUser'] = $dealUser;
            }else if($log['nodeName'] == $toXmlTask){
                $log['result'] = 'pending';
                $log['comment'] = '';
                $log['dealDate'] = '';
                $log['dealUser'] = '';
            }
        }
        $allLogList[$version] = $logList;
        $updateData->logList = json_encode($allLogList);
        $updateData->assign = '';
        $this->dao->update(TABLE_IWFP)->data($updateData)->where('processInstanceId')->eq($processInstanceId)->exec();

        if(isset($nextUserArray) && !empty($nextUserArray) && isset($dealUser) && !empty($dealUser) && (in_array($dealUser, $nextUserArray)) && $isSkip){
            return $this->completeTaskWithClaim($processInstanceId, $dealUser, $dealMessage, $dealResult, $userVariableList, $version);
        }
        return $response;
    }

    /**
     * 接受任务并处理任务，临时设置节点并设置下一节点待处理人
     * @param $processInstanceId    任务id
     * @param $dealUser 处理人
     * @param $dealMessage  处理意见
     * @param $dealResult   处理结果1-通过；2-不通过；3-撤回
     * @param $nextUserStr 下一节点处理人,根据逗号分割
     * @param $userVariableKeyList  用户变量
     * @return void
     */
    public function completeTaskWithClaimByDealUser($processInstanceId, $dealUser, $dealMessage, $dealResult, $userVariableList, $version, $toDealUserList){
        $response = new stdClass();
        //1、校验参数
        if(empty($processInstanceId) || empty($dealUser) || empty($version) || empty($toDealUserList)){
            return dao::$errors[''] = $this->lang->iwfp->errorMessageList['emptyError'];
        }
        $dealResultKeyList = array_keys($this->lang->iwfp->dealResultList);
        if(!in_array($dealResult, $dealResultKeyList)){
            return dao::$errors[''] = $this->lang->iwfp->errorMessageList['resultError'];
        }
        $iwfp = $this->getIwfpByProcessInstanceId($processInstanceId);
        if(empty($iwfp)){
            return dao::$errors[''] = $this->lang->iwfp->errorMessageList['iwfpEmpty'];
        }
        $objectCode = $iwfp->objectCode;
        //校验当前处理人是否是目前处理人
        $dealUserIwfp = explode(',',$iwfp->dealUser);
        if(!in_array($dealUser, $dealUserIwfp)){
            return dao::$errors[''] = $this->lang->iwfp->errorMessageList['todoEmpty'];
        }
        //查找委派字段，判断用户是否是委派权限
        $realDeal = $dealUser;
        if(!empty($iwfp->assign)){
            $assignList = json_decode($iwfp->assign,true);
            foreach ($assignList as $assign){
                if($assign['assignUserNo'] == $dealUser){
                    $realDeal = $assign['userNo'];
                }
            }
        }
        $taskId = $iwfp->processTaskId;
        $processXmlTaskId = $iwfp->processXmlTaskId;
        //3、查找该任务下的授权按钮
        $btnListResult = $this->getButtonList($taskId, $objectCode);
        if(empty($btnListResult)){
            return dao::$errors[''] = $this->lang->iwfp->errorMessageList['networkError'];
        }
        $btnListData = json_decode($btnListResult);
        if($btnListData->success != '1'){
            return dao::$errors[''] = $btnListData->errorMessage;
        }
        $btnList = $btnListData->data;

        //3、根据处理结果和用户参数来查找到下一节点的按钮
        $choseBtn = '';
        foreach ($btnList as $btn){
            $chose = true;
            if(!empty($btn->userVariable)){
                $btnUserVariableList = json_decode($btn->userVariable);
                foreach ($btnUserVariableList as $key=>$value){
                    if($key == 'skip'){
                        continue;
                    }else if($key == 'result'){
                        if($value != $dealResult){
                            $chose = false;
                        }
                    }else{
                        if(empty($userVariableList->$key) || $value != $userVariableList->$key){
                            $chose = false;
                        }
                    }
                }
            }else{
                $chose = false;
            }
            if($chose){
                $choseBtn = $btn;
                break;
            }
        }
        if(empty($choseBtn)){
            return dao::$errors[''] = $this->lang->iwfp->errorMessageList['btnEmpty'];
        }

        //4、调用处理接口
        //4.1组装数据
        $url = $this->config->global->completeTaskWithClaimUrl;
        $pushData = array();
        $pushData['tenantId'] = $this->config->global->tenantId;
        $pushData['btnId'] = $choseBtn->btnId;
        $pushData['taskId'] = $taskId;
        $pushData['dealUserNo'] = $realDeal;
        $pushData['dealUserName'] = $realDeal;
        $dealMessageObject = new stdClass();
        //$dealMessageObject->dealMessage = $dealMessage;
        $dealMessageObject->dealResult = $dealResult;
        $dealMessageObject->version = $version;
        $dealMessageObject->dealUser = $dealUser;
        $dealMessageObject->todealUser = $dealUserIwfp;
        $pushData['dealMessage'] = json_encode($dealMessageObject);

        $nextUserList = array();
        $nextStatus = $choseBtn->xmlTaskId;
        $isAdmin = false;
        $nextUserArray = $toDealUserList;
        foreach ($toDealUserList as $nextUserObject) {
            $nextUser = new stdClass();
            $nextUser->userNo = $nextUserObject;
            $nextUser->userName = $nextUserObject;
            array_push($nextUserList, $nextUser);
            if($nextUserObject == 'admin'){
                $isAdmin = true;
            }
        }
        if(!$isAdmin){
            $adminUser = new stdClass();
            $adminUser->userNo = 'admin';
            $adminUser->userName = 'admin';
            array_push($nextUserList, $adminUser);
        }
        $pushData['nextUserList'] = $nextUserList;
        if(empty($userVariableList)){
            $userVariableList = new stdClass();
        }
        $userVariableList->result = $dealResult;
        $pushData['userVariable'] = json_encode($userVariableList);
        $method = 'POST';
        $sendType = 'completeTaskWithClaim';
        $result = $this->sendIwfp($url, $pushData, $method, $sendType, $objectCode);
        if(empty($result)){
            return dao::$errors[''] = $this->lang->iwfp->errorMessageList['networkError'];
        }
        $resultData = json_decode($result);
        if($resultData->success != '1'){
            $completeTaskUrl = $this->config->global->completeTaskUrl;
            $sendType = 'completeTask';
            $result = $this->sendIwfp($completeTaskUrl, $pushData, $method, $sendType, $objectCode);
            if(empty($result)){
                return dao::$errors[''] = $this->lang->iwfp->errorMessageList['networkError'];
            }
            $resultData = json_decode($result);
            if($resultData->success != '1'){
                return dao::$errors[''] = $resultData->errorMessage;
            }
        }
        $isEnd = $resultData->data->isEnd;
        $toXmlTaskId = $resultData->data->clickedButtonDTO->toXmlTaskId;
        $toXmlTask =$toXmlTaskId;
        //流程结束修改表数据
        if($isEnd == 1){
            $this->dao->update(TABLE_IWFP)->set('status')->eq('closed')->where('processInstanceId')->eq($processInstanceId)->exec();
        }
        $response->isEnd = $isEnd;
        $response->toXmlTask = $toXmlTask;
        $response->dealUser = $toDealUserList;
        $updateData = new stdClass();
        //获取下一节点taskid
        $toDoListResult = $this->getToDoTaskList($processInstanceId, '', '0', $objectCode);
        if(empty($toDoListResult)){
            return dao::$errors[''] = $this->lang->iwfp->errorMessageList['networkError'];
        }
        $toDoListData = json_decode($toDoListResult);
        if($toDoListData->success != '1'){
            return dao::$errors[''] = $toDoListData->errorMessage;
        }

        $toDoList = $toDoListData->data;
        $taskId = '';
        $xmlTaskId = '';
        foreach ($toDoList as $toDo){
            if(!empty($toDo)){
                $taskId = $toDo->taskId;
                $xmlTaskId = $toDo->xmlTaskId;
            }
        }
        $updateData->processTaskId = $taskId;
        $updateData->processXmlTaskId = $xmlTaskId;
        //下一节点待处理人
        if(isset($nextUserArray) && !empty($nextUserArray)){
            $updateData->dealUser = implode(',',$nextUserArray).',admin';
        }
        //构建审批信息
        $allLogList = json_decode($iwfp->logList,true);
        $logList = $allLogList[$version];
        $i = 0;
        $insertIndex = 0;
        foreach ($logList as &$log){
            if($log['nodeName'] == $processXmlTaskId){
                $log['result'] = $this->lang->iwfp->dealResultKeyList[$dealResult];
                $log['comment'] = $dealMessage;
                $log['dealDate'] = helper::now();
                $log['dealUser'] = $dealUser;
                $insertIndex = $i;
            }
            $i++;
        }
        $newlog = array();
        $newlog['nodeName'] = $toXmlTask;
        $newlog['toDealUser'] = $toDealUserList;
        $newlog['dealUser'] = '';
        $newlog['result'] = 'pending';
        $newlog['comment'] = '';
        $newlog['dealDate'] = '';
        $newlog['version'] = $version;
        array_splice($logList, $insertIndex+1, 0, [(object)$newlog]);
        $allLogList[$version] = $logList;
        $updateData->logList = json_encode($allLogList);
        $updateData->assign = '';
        //将节点待处理人保存在表格中，跳转时直接获取
        $nodeDealUserList = json_decode($iwfp->nodeDealUser);
        $nodeDealUserList->$toXmlTask = $toDealUserList;
        $updateData->nodeDealUser = json_encode($nodeDealUserList);
        $this->dao->update(TABLE_IWFP)->data($updateData)->where('processInstanceId')->eq($processInstanceId)->exec();
        return $response;
    }
    /******                         V1.0版本   适配全是竞争节点并且发起流程时已确认待处理人和节点     结束                      *******/



    /******                         V2.0版本   适配竞争节点、会签节点并且可以临时增加节点     开始                             *******/
    /**
     * 发起流程V2.0
     *
     * @param $objectType 模块类型
     * @param $objectId  数据id
     * @param $objectCode 数据编号
     * @param $createdBy 创建人
     * @param $nodeDealUser 审核节点以及审核节点人
     * @param $version 版本
     * @param $logNodeArray
     * @param string $processInstanceId 流程实例Id
     * @return stdClas
     */
    public function startWorkFlow_V2($objectType, $objectId, $objectCode, $createdBy, $nodeDealUser, $version, $logNodeArray, $processInstanceId = ''){
        $response = new stdClass();
        //1、校验参数
        if(empty($objectType) || empty($objectId) || empty($objectCode) || empty($createdBy) || empty($nodeDealUser)){
            return dao::$errors[''] = $this->lang->iwfp->errorMessageList['emptyError'];
        }
        //2、根据模块类型查找流程模版定义Key和流程模版定义Id
        $definitionKey = $this->lang->iwfp->templateKeyList[$objectType];
        $definitionId = $this->lang->iwfp->templateIdList[$objectType];
        $tempId = $this->lang->iwfp->iwfpTempIdList[$objectType];
        $objectDefinitionKey = $this->config->global->$definitionKey;
        $objectDefinitionId = $this->config->global->$definitionId;
        $objectTempId = $this->config->global->$tempId;

        if(empty($objectDefinitionKey) || empty($objectDefinitionId)){
            return dao::$errors[''] = $this->lang->iwfp->errorMessageList['templateConfigEmpty'];
        }
        $newProcessInstanceId = $processInstanceId;
        $iwfp = array();
        //3、若流程实例为空，发起流程
        if(empty($processInstanceId)){
            $result = $this->sendStartWorkFlow($objectDefinitionKey, $objectCode, $objectId, $objectType, $createdBy);
            if(!empty($result)){
                $resultData = json_decode($result);
                if ($resultData->success == '1') {
                    //4.1插入数据
                    $this->insertIwfpData($resultData->data, $objectType, $objectId, $objectCode, $createdBy,$nodeDealUser);
                    $response->processInstanceId = $resultData->data->processInstanceId;
                    $response->processDefinitionKey = $resultData->data->processDefinitionKey;
                    $response->processDefinitionId = $resultData->data->processDefinitionId;
                    $newProcessInstanceId = $resultData->data->processInstanceId;
                }else{
                    return dao::$errors[''] = $resultData->errorMessage;
                }
            }else{
                return dao::$errors[''] = $this->lang->iwfp->errorMessageList['networkError'];
            }
        }else {
            //5、若流程实例不为空
            $iwfp = $this->getIwfpByProcessInstanceId($processInstanceId);
            if(empty($iwfp)){
                return dao::$errors[''] = $this->lang->iwfp->errorMessageList['iwfpEmpty'];
            }
            //5.1、判断当前模块的流程模版定义Key和流程模版定义Id和原数据保存的流程模版定义Key和流程模版定义Id是否一致，如果一致不重新发起流程，直接返回流程实例id;
            if($iwfp->processDefinitionKey == $objectDefinitionKey && $iwfp->processDefinitionId == $objectDefinitionId){
                $response->processInstanceId = $processInstanceId;
                $response->processDefinitionKey = $iwfp->processDefinitionKey;
                $response->processDefinitionId = $iwfp->processDefinitionId;
                $nodeDealUserStr = json_encode($nodeDealUser);
                $this->dao->update(TABLE_IWFP)->set('nodeDealUser')->eq($nodeDealUserStr)->where('processInstanceId')->eq($processInstanceId)->exec();
                $newProcessInstanceId = $processInstanceId;
            }else{
                //5.2、如果不一致，重新发起流程
                $result = $this->sendStartWorkFlow($objectDefinitionKey, $objectCode, $objectId, $objectType, $createdBy);
                if(!empty($result)){
                    $resultData = json_decode($result);
                    if ($resultData->success == '1') {
                        //5.3、将之前的流程数据设置为挂起
                        $this->dao->update(TABLE_IWFP)->set('status')->eq('pending')->where('processInstanceId')->eq($processInstanceId)->exec();
                        //5.4插入数据
                        $this->insertIwfpData($resultData->data, $objectType, $objectId, $objectCode, $createdBy,$nodeDealUser);
                        $response->processInstanceId = $resultData->data->processInstanceId;
                        $response->processDefinitionKey = $resultData->data->processDefinitionKey;
                        $response->processDefinitionId = $resultData->data->processDefinitionId;
                        $newProcessInstanceId = $resultData->data->processInstanceId;
                    }else{
                        return dao::$errors[''] = $resultData->errorMessage;
                    }
                }else{
                    return dao::$errors[''] = $this->lang->iwfp->errorMessageList['networkError'];
                }
            }
        }
        $updateData = new stdClass();
        //获取下一节点taskid
        if(empty($newProcessInstanceId)){
            return dao::$errors[''] = $this->lang->iwfp->errorMessageList['instanceIdEmpty'];
        }
        $toDoListResult = $this->getToDoTaskList($newProcessInstanceId, '', '0', $objectCode);
        if(empty($toDoListResult)){
            return dao::$errors[''] = $this->lang->iwfp->errorMessageList['networkError'];
        }
        $toDoListData = json_decode($toDoListResult);
        if($toDoListData->success != '1'){
            return dao::$errors[''] = $toDoListData->errorMessage;
        }
        $toDoList = $toDoListData->data;
        $taskId = array();
        $xmlTaskId = '';
        foreach ($toDoList as $toDo){
            if(!empty($toDo)){
                $xmlTaskId = $toDo->xmlTaskId;
                $taskId[$toDo->receiveUserNo] = $toDo->taskId;
            }
        }
        $updateData->processTaskId = json_encode($taskId);
        $updateData->processXmlTaskId = $xmlTaskId;
        if($iwfp->objectType == 'authorityapply'){
            $xmlTaskId = 'waitsubmit';
            $updateData->processXmlTaskId = $xmlTaskId;
        }

        //下一节点待处理人
        if($createdBy != 'admin'){
            $dealUser = $createdBy.',admin';
        }else{
            $dealUser = $createdBy;
        }
        $updateData->dealUser = $dealUser;
        //获取节点信息
        $nodeInfoList = $this->getNodeInfoList($objectTempId);
        if(dao::isError()){
            return dao::$errors[''] = dao::getError();
        }

        //构建审批信息
        $allLogList = $this->buildLogList($processInstanceId,$logNodeArray,$nodeDealUser,$xmlTaskId,$version,$iwfp, $nodeInfoList);
        $updateData->logList = json_encode($allLogList);
        $updateData->nodeInfoList = json_encode($nodeInfoList);
        $this->dao->update(TABLE_IWFP)->data($updateData)->where('processInstanceId')->eq($newProcessInstanceId)->exec();
        return $response;
    }


    /**
     * 接受任务并处理任务
     * @param $processInstanceId    任务id
     * @param $dealUser 处理人
     * @param $dealMessage  处理意见
     * @param $dealResult   处理结果1-通过；2-不通过；3-撤回
     * @param $nextUserStr 下一节点处理人,根据逗号分割
     * @param $userVariableKeyList  用户变量
     * @return void
     */
    public function completeTaskWithClaim_V2($processInstanceId, $dealUser, $dealMessage, $dealResult, $userVariableList, $version, $toDealUserList=''){
        $response = new stdClass();
        //1、校验参数
        if(empty($processInstanceId) || empty($dealUser) || empty($version)){
            return dao::$errors[''] = $this->lang->iwfp->errorMessageList['emptyError'];
        }
        $dealResultKeyList = array_keys($this->lang->iwfp->dealResultList);
        if(!in_array($dealResult, $dealResultKeyList)){
            return dao::$errors[''] = $this->lang->iwfp->errorMessageList['resultError'];
        }
        $iwfp = $this->getIwfpByProcessInstanceId($processInstanceId);
        if(empty($iwfp)){
            return dao::$errors[''] = $this->lang->iwfp->errorMessageList['iwfpEmpty'];
        }
        $objectCode = $iwfp->objectCode;
        //校验当前处理人是否是目前处理人
        $dealUserIwfp = explode(',',$iwfp->dealUser);
        if(!in_array($dealUser, $dealUserIwfp)){
            return dao::$errors[''] = $this->lang->iwfp->errorMessageList['todoEmpty'];
        }
        //查找委派字段，判断用户是否是委派权限
        $realDeal = $dealUser;
        if(!empty($iwfp->assign)){
            $assignList = json_decode($iwfp->assign,true);
            foreach ($assignList as $assign){
                if($assign['assignUserNo'] == $dealUser){
                    $realDeal = $assign['userNo'];
                }
            }
        }
        $taskIdArray = json_decode($iwfp->processTaskId,true);
        $taskId = $taskIdArray[$realDeal];
        $processXmlTaskId = $iwfp->processXmlTaskId;
        $nodeInfoList = json_decode($iwfp->nodeInfoList,true);
        //3、查找该任务下的授权按钮
        $btnListResult = $this->getButtonList($taskId, $objectCode);
        if(empty($btnListResult)){
            return dao::$errors[''] = $this->lang->iwfp->errorMessageList['networkError'];
        }
        $btnListData = json_decode($btnListResult);
        if($btnListData->success != '1'){
            return dao::$errors[''] = $btnListData->errorMessage;
        }
        $btnList = $btnListData->data;

        //3、根据处理结果和用户参数来查找到下一节点的按钮
        if($iwfp->objectType == 'authorityapply'){
            $choseBtn = $this->choseBtn_V2($nodeInfoList, $processXmlTaskId, $iwfp, $version, $dealResult, $btnList,$userVariableList);
        }else{
            $choseBtn = $this->choseBtn($nodeInfoList, $processXmlTaskId, $iwfp, $version, $dealResult, $btnList,$userVariableList);
        }

        if(empty($choseBtn)){
            return dao::$errors[''] = $this->lang->iwfp->errorMessageList['btnEmpty'];
        }

        //4、调用处理接口
        //4.1组装数据
        $url = $this->config->global->completeTaskWithClaimUrl;
        $taskPushObj = $this->buildTaskPushData($choseBtn, $taskId, $realDeal,$dealResult,$version,$dealUser,$dealUserIwfp,$iwfp,$toDealUserList);
        $nextUserArray = $taskPushObj['nextUserArray'];
        $pushData = $taskPushObj['pushData'];
        $method = 'POST';
        $sendType = 'completeTaskWithClaim';
        $result = $this->sendIwfp($url, $pushData, $method, $sendType, $objectCode);
        if(empty($result)){
            return dao::$errors[''] = $this->lang->iwfp->errorMessageList['networkError'];
        }
        $resultData = json_decode($result);
        if($resultData->success != '1'){
            $completeTaskUrl = $this->config->global->completeTaskUrl;
            $sendType = 'completeTask';
            $result = $this->sendIwfp($completeTaskUrl, $pushData, $method, $sendType, $objectCode);
            if(empty($result)){
                return dao::$errors[''] = $this->lang->iwfp->errorMessageList['networkError'];
            }
            $resultData = json_decode($result);
            if($resultData->success != '1'){
                return dao::$errors[''] = $resultData->errorMessage;
            }
        }
        $isSkip = false;
        //判断是否跳过相同处理人节点
        $chosebtnUserVariableList = json_decode($choseBtn->userVariable);
        foreach ($chosebtnUserVariableList as $key=>$value){
            if($key == 'skip'){
                if(!empty($value) and $value == 1){
                    $isSkip = true;
                }
            }
        }
        $updateData = new stdClass();
        //获取下一节点taskid
        $toDoListResult = $this->getToDoTaskList($processInstanceId, '', '0', $objectCode);
        if(empty($toDoListResult)){
            return dao::$errors[''] = $this->lang->iwfp->errorMessageList['networkError'];
        }
        $toDoListData = json_decode($toDoListResult);
        if($toDoListData->success != '1'){
            return dao::$errors[''] = $toDoListData->errorMessage;
        }

        $toDoList = $toDoListData->data;
        $taskId = array();
        $xmlTaskId = '';
        $toDealUserArray = array();
        foreach ($toDoList as $toDo){
            if(!empty($toDo)){
                $xmlTaskId = $toDo->xmlTaskId;
                $taskId[$toDo->receiveUserNo] = $toDo->taskId;
                $receiveUserNo = $toDo->receiveUserNo;
                //查找委派字段，判断用户是否是委派权限
                if(!empty($iwfp->assign)){
                    $assignList = json_decode($iwfp->assign,true);
                    foreach ($assignList as $assign){
                        if($assign['userNo'] ==  $toDo->receiveUserNo){
                            $receiveUserNo = $assign['assignUserNo'];
                        }
                    }
                }
                array_push($toDealUserArray, $receiveUserNo);
            }
        }
        $isEnd = $resultData->data->isEnd;
        $toXmlTaskId = $xmlTaskId;
        $toXmlTask =$toXmlTaskId;
        //流程结束修改表数据
        if($isEnd == 1){
            $this->dao->update(TABLE_IWFP)->set('status')->eq('closed')->where('processInstanceId')->eq($processInstanceId)->exec();
        }
        $response->isEnd = $isEnd;
        $response->toXmlTask = $toXmlTask;
        $response->dealUser = $toDealUserArray;
        $updateData->processTaskId = json_encode($taskId);;
        $updateData->processXmlTaskId = $xmlTaskId;
        //下一节点待处理人
        if(isset($toDealUserArray) && !empty($toDealUserArray)){
            $updateData->dealUser = implode(',',$toDealUserArray);
        }
        //构建审批信息
        $allLogList = $this->buildTaskLog($iwfp,$version,$processXmlTaskId,$dealUser,$nextUserArray,$isSkip,$dealResult,$dealMessage,$toXmlTask,$nodeInfoList,$toDealUserArray,$updateData,$toDealUserList);
        $updateData->logList = json_encode($allLogList);
        if($toXmlTask != $iwfp->processXmlTaskId){
            $updateData->assign = '';
        }
        $this->dao->update(TABLE_IWFP)->data($updateData)->where('processInstanceId')->eq($processInstanceId)->exec();

        if(isset($nextUserArray) && !empty($nextUserArray) && isset($dealUser) && !empty($dealUser) && (in_array($dealUser, $nextUserArray)) && $isSkip){
            return $this->completeTaskWithClaim_V2($processInstanceId, $dealUser, $dealMessage, $dealResult, $userVariableList, $version);
        }
        return $response;
    }

        /**
         * 测回任务
         * @param $processInstanceId    任务id
         * @param $dealUser 处理人
         * @param $dealMessage  处理意见
         * @param $nextUserStr 下一节点处理人,根据逗号分割
         * @param $userVariableKeyList  用户变量
         * @return void
         */
        public function withdraw_V2($processInstanceId, $dealUser, $dealMessage, $version, $toDealUserList='')
        {
            $response = new stdClass();
            //1、校验参数
            if (empty($processInstanceId) || empty($dealUser) || empty($version)) {
                return dao::$errors[''] = $this->lang->iwfp->errorMessageList['emptyError'];
            }
            $iwfp = $this->getIwfpByProcessInstanceId($processInstanceId);
            if (empty($iwfp)) {
                return dao::$errors[''] = $this->lang->iwfp->errorMessageList['iwfpEmpty'];
            }
            $objectCode = $iwfp->objectCode;
            $dealUserIwfp = explode(',', $iwfp->dealUser);
            $realDeal = $dealUserIwfp[0];
            $taskIdArray = json_decode($iwfp->processTaskId, true);
            $taskId = $taskIdArray[$realDeal];
            $processXmlTaskId = $iwfp->processXmlTaskId;
            $nodeInfoList = json_decode($iwfp->nodeInfoList, true);
            //3、查找该任务下的授权按钮
            $btnListResult = $this->getButtonList($taskId, $objectCode);
            if (empty($btnListResult)) {
                return dao::$errors[''] = $this->lang->iwfp->errorMessageList['networkError'];
            }
            $btnListData = json_decode($btnListResult);
            if ($btnListData->success != '1') {
                return dao::$errors[''] = $btnListData->errorMessage;
            }
            $btnList = $btnListData->data;

            //3、根据处理结果和用户参数来查找到下一节点的按钮
            $choseBtn = array();
            foreach ($btnList as $btn) {
                if (!empty($btn->userVariable)) {
                    $btnUserVariableList = json_decode($btn->userVariable);
                    foreach ($btnUserVariableList as $key => $value) {
                        if ($key == 'result') {
                            if ($value == '3') {
                                $choseBtn = $btn;
                            }
                        }
                    }
                }
            }
            if (empty($choseBtn)) {
                return dao::$errors[''] = $this->lang->iwfp->errorMessageList['btnEmpty'];
            }

            //4、调用处理接口
            //4.1组装数据
            $url = $this->config->global->completeTaskWithClaimUrl;
            $taskPushObj = $this->buildTaskPushData($choseBtn, $taskId, $realDeal, '3', $version, $dealUser, $dealUserIwfp, $iwfp, $toDealUserList);
            $nextUserArray = $taskPushObj['nextUserArray'];
            $pushData = $taskPushObj['pushData'];
            $method = 'POST';
            $sendType = 'completeTaskWithClaim';
            $result = $this->sendIwfp($url, $pushData, $method, $sendType, $objectCode);
            if (empty($result)) {
                return dao::$errors[''] = $this->lang->iwfp->errorMessageList['networkError'];
            }
            $resultData = json_decode($result);
            if ($resultData->success != '1') {
                $completeTaskUrl = $this->config->global->completeTaskUrl;
                $sendType = 'completeTask';
                $result = $this->sendIwfp($completeTaskUrl, $pushData, $method, $sendType, $objectCode);
                if (empty($result)) {
                    return dao::$errors[''] = $this->lang->iwfp->errorMessageList['networkError'];
                }
                $resultData = json_decode($result);
                if ($resultData->success != '1') {
                    return dao::$errors[''] = $resultData->errorMessage;
                }
            }
            $isSkip = false;

            $updateData = new stdClass();
            //获取下一节点taskid
            $toDoListResult = $this->getToDoTaskList($processInstanceId, '', '0', $objectCode);
            if (empty($toDoListResult)) {
                return dao::$errors[''] = $this->lang->iwfp->errorMessageList['networkError'];
            }
            $toDoListData = json_decode($toDoListResult);
            if ($toDoListData->success != '1') {
                return dao::$errors[''] = $toDoListData->errorMessage;
            }

            $toDoList = $toDoListData->data;
            $taskId = array();
            $xmlTaskId = '';
            $toDealUserArray = array();
            foreach ($toDoList as $toDo) {
                if (!empty($toDo)) {
                    $xmlTaskId = $toDo->xmlTaskId;
                    $taskId[$toDo->receiveUserNo] = $toDo->taskId;
                    $receiveUserNo = $toDo->receiveUserNo;
                    //查找委派字段，判断用户是否是委派权限
                    if (!empty($iwfp->assign)) {
                        $assignList = json_decode($iwfp->assign, true);
                        foreach ($assignList as $assign) {
                            if ($assign['userNo'] == $toDo->receiveUserNo) {
                                $receiveUserNo = $assign['assignUserNo'];
                            }
                        }
                    }
                    array_push($toDealUserArray, $receiveUserNo);
                }
            }
            $isEnd = $resultData->data->isEnd;
            $toXmlTaskId = $xmlTaskId;
            $toXmlTask = $toXmlTaskId;
            //流程结束修改表数据
            if ($isEnd == 1) {
                $this->dao->update(TABLE_IWFP)->set('status')->eq('closed')->where('processInstanceId')->eq($processInstanceId)->exec();
            }
            $response->isEnd = $isEnd;
            $response->toXmlTask = $toXmlTask;
            $response->dealUser = $toDealUserArray;
            $updateData->processTaskId = json_encode($taskId);;
            $updateData->processXmlTaskId = $xmlTaskId;
            //判断是否是会签
            if ($nodeInfoList[$toXmlTaskId]['taskType'] == 'RuleJoinTask') {
                $isSkip = true;
            }
            //下一节点待处理人
            if (isset($toDealUserArray) && !empty($toDealUserArray)) {
                $updateData->dealUser = implode(',', $toDealUserArray);
            }
            //构建审批信息
            $allLogList = $this->buildTaskLog($iwfp, $version, $processXmlTaskId, $dealUser, $nextUserArray, $isSkip, '3', $dealMessage, $toXmlTask, $nodeInfoList, $toDealUserArray, $updateData, $toDealUserList);
            $updateData->logList = json_encode($allLogList);
            if ($toXmlTask != $iwfp->processXmlTaskId) {
                $updateData->assign = '';
            }
            $this->dao->update(TABLE_IWFP)->data($updateData)->where('processInstanceId')->eq($processInstanceId)->exec();

            if ($isSkip) {
                return $this->withdraw_V2($processInstanceId, $dealUser, $dealMessage, $version, $toDealUserList);
            }
            return $response;
        }

            /**
             * 测回任务
             * @param $processInstanceId    任务id
             * @param $dealUser 处理人
             * @param $dealMessage  处理意见
             * @param $nextUserStr 下一节点处理人,根据逗号分割
             * @param $userVariableKeyList  用户变量
             * @return void
             */
            public function reject_V2($processInstanceId, $dealUser, $dealMessage, $version, $toDealUserList=''){
                $response = new stdClass();
                //1、校验参数
                if(empty($processInstanceId) || empty($dealUser) || empty($version)){
                    return dao::$errors[''] = $this->lang->iwfp->errorMessageList['emptyError'];
                }
                $iwfp = $this->getIwfpByProcessInstanceId($processInstanceId);
                if(empty($iwfp)){
                    return dao::$errors[''] = $this->lang->iwfp->errorMessageList['iwfpEmpty'];
                }
                $objectCode = $iwfp->objectCode;
                $dealUserIwfp = explode(',', $iwfp->dealUser);
                $realDeal = $dealUserIwfp[0];
                $taskIdArray = json_decode($iwfp->processTaskId,true);
                $taskId = $taskIdArray[$realDeal];
                $processXmlTaskId = $iwfp->processXmlTaskId;
                $nodeInfoList = json_decode($iwfp->nodeInfoList,true);
                //3、查找该任务下的授权按钮
                $btnListResult = $this->getButtonList($taskId, $objectCode);
                if(empty($btnListResult)){
                    return dao::$errors[''] = $this->lang->iwfp->errorMessageList['networkError'];
                }
                $btnListData = json_decode($btnListResult);
                if($btnListData->success != '1'){
                    return dao::$errors[''] = $btnListData->errorMessage;
                }
                $btnList = $btnListData->data;

                //3、根据处理结果和用户参数来查找到下一节点的按钮
                $choseBtn = array();
                foreach ($btnList as $btn){
                    if(!empty($btn->userVariable)){
                        $btnUserVariableList = json_decode($btn->userVariable);
                        foreach ($btnUserVariableList as $key=>$value){
                            if($key == 'result'){
                                if($value == '2'){
                                    $choseBtn = $btn;
                                }
                            }
                        }
                    }
                }
                if(empty($choseBtn)){
                    return dao::$errors[''] = $this->lang->iwfp->errorMessageList['btnEmpty'];
                }

                //4、调用处理接口
                //4.1组装数据
                $url = $this->config->global->completeTaskWithClaimUrl;
                $taskPushObj = $this->buildTaskPushData($choseBtn, $taskId, $realDeal,'2',$version,$dealUser,$dealUserIwfp,$iwfp,$toDealUserList);
                $nextUserArray = $taskPushObj['nextUserArray'];
                $pushData = $taskPushObj['pushData'];
                $method = 'POST';
                $sendType = 'completeTaskWithClaim';
                $result = $this->sendIwfp($url, $pushData, $method, $sendType, $objectCode);
                if(empty($result)){
                    return dao::$errors[''] = $this->lang->iwfp->errorMessageList['networkError'];
                }
                $resultData = json_decode($result);
                if($resultData->success != '1'){
                    $completeTaskUrl = $this->config->global->completeTaskUrl;
                    $sendType = 'completeTask';
                    $result = $this->sendIwfp($completeTaskUrl, $pushData, $method, $sendType, $objectCode);
                    if(empty($result)){
                        return dao::$errors[''] = $this->lang->iwfp->errorMessageList['networkError'];
                    }
                    $resultData = json_decode($result);
                    if($resultData->success != '1'){
                        return dao::$errors[''] = $resultData->errorMessage;
                    }
                }
                $isSkip = false;

                $updateData = new stdClass();
                //获取下一节点taskid
                $toDoListResult = $this->getToDoTaskList($processInstanceId, '', '0', $objectCode);
                if(empty($toDoListResult)){
                    return dao::$errors[''] = $this->lang->iwfp->errorMessageList['networkError'];
                }
                $toDoListData = json_decode($toDoListResult);
                if($toDoListData->success != '1'){
                    return dao::$errors[''] = $toDoListData->errorMessage;
                }

                $toDoList = $toDoListData->data;
                $taskId = array();
                $xmlTaskId = '';
                $toDealUserArray = array();
                foreach ($toDoList as $toDo){
                    if(!empty($toDo)){
                        $xmlTaskId = $toDo->xmlTaskId;
                        $taskId[$toDo->receiveUserNo] = $toDo->taskId;
                        $receiveUserNo = $toDo->receiveUserNo;
                        //查找委派字段，判断用户是否是委派权限
                        if(!empty($iwfp->assign)){
                            $assignList = json_decode($iwfp->assign,true);
                            foreach ($assignList as $assign){
                                if($assign['userNo'] ==  $toDo->receiveUserNo){
                                    $receiveUserNo = $assign['assignUserNo'];
                                }
                            }
                        }
                        array_push($toDealUserArray, $receiveUserNo);
                    }
                }
                $isEnd = $resultData->data->isEnd;
                $toXmlTaskId = $xmlTaskId;
                $toXmlTask =$toXmlTaskId;
                //流程结束修改表数据
                if($isEnd == 1){
                    $this->dao->update(TABLE_IWFP)->set('status')->eq('closed')->where('processInstanceId')->eq($processInstanceId)->exec();
                }
                $response->isEnd = $isEnd;
                $response->toXmlTask = $toXmlTask;
                $response->dealUser = $toDealUserArray;
                $updateData->processTaskId = json_encode($taskId);;
                $updateData->processXmlTaskId = $xmlTaskId;
                //判断是否是会签
                if($nodeInfoList[$toXmlTaskId]['taskType'] == 'RuleJoinTask'){
                    $isSkip = true;
                }
                //下一节点待处理人
                if(isset($toDealUserArray) && !empty($toDealUserArray)){
                    $updateData->dealUser = implode(',',$toDealUserArray);
                }
                //构建审批信息
                $allLogList = $this->buildTaskLog($iwfp,$version,$processXmlTaskId,$dealUser,$nextUserArray,false,'2',$dealMessage,$toXmlTask,$nodeInfoList,$toDealUserArray,$updateData,$toDealUserList);
                $updateData->logList = json_encode($allLogList);
                if($toXmlTask != $iwfp->processXmlTaskId){
                    $updateData->assign = '';
                }
                $this->dao->update(TABLE_IWFP)->data($updateData)->where('processInstanceId')->eq($processInstanceId)->exec();

                if($isSkip){
                    return $this->reject_V2($processInstanceId, $dealUser, $dealMessage, $version, $toDealUserList);
                }
                return $response;
    }
    /******                         V2.0版本   适配竞争节点、会签节点并且可以临时增加节点     结束                             *******/


    /**
     * 回退到上一步
     * @param $processInstanceId
     * @param $objectCode
     * @return mixed
     */
    public function turnBack($processInstanceId){
        //1、校验参数
        if(empty($processInstanceId)){
            return dao::$errors[''] = $this->lang->iwfp->errorMessageList['emptyError'];
        }
        $iwfp = $this->getIwfpByProcessInstanceId($processInstanceId);
        if(empty($iwfp)){
            return dao::$errors[''] = $this->lang->iwfp->errorMessageList['iwfpEmpty'];
        }
        $objectCode = $iwfp->objectCode;
        //2、查找代办任务
        $toDoListResult = $this->getToDoTaskList($processInstanceId, '', '0', $objectCode);
        if(empty($toDoListResult)){
            return dao::$errors[''] = $this->lang->iwfp->errorMessageList['networkError'];
        }
        $toDoListData = json_decode($toDoListResult);
        if($toDoListData->success != '1'){
            return dao::$errors[''] = $toDoListData->errorMessage;
        }

        $toDoList = $toDoListData->data;
        $taskId = '';
        foreach ($toDoList as $toDo){
            if(!empty($toDo)){
                $taskId = $toDo->taskId;
            }
        }
        if(empty($taskId)){
            return dao::$errors[''] = $this->lang->iwfp->errorMessageList['todoEmpty'];
        }
        //3、组装参数
        $url = $this->config->global->turnBackUrl;
        $pushData = array();
        $pushData['tenantId'] = $this->config->global->tenantId;
        $pushData['currentTaskId'] = $taskId;
        $method = 'POST';
        $sendType = 'turnBack';
        $result = $this->sendIwfp($url, $pushData, $method, $sendType, $objectCode);
        if(empty($result)){
            return dao::$errors[''] = $this->lang->iwfp->errorMessageList['networkError'];
        }
        $resultData = json_decode($result);
        if($resultData->success != '1'){
            return dao::$errors[''] = $resultData->errorMessage;
        }
        $response = $resultData->data;
        return $response;
    }

    /**
     * 获取可以退回的节点
     * @param $processInstanceId
     * @param $objectCode
     * @return void
     */
    public function getFreeJumpNodeList($processInstanceId){
        //1、校验参数
        if(empty($processInstanceId)){
            return dao::$errors[''] = $this->lang->iwfp->errorMessageList['emptyError'];
        }
        $iwfp = $this->getIwfpByProcessInstanceId($processInstanceId);
        if(empty($iwfp)){
            return dao::$errors[''] = $this->lang->iwfp->errorMessageList['iwfpEmpty'];
        }
        $objectCode = $iwfp->objectCode;
        //2、查找代办任务
        $toDoListResult = $this->getToDoTaskList($processInstanceId, '', '0', $objectCode);
        if(empty($toDoListResult)){
            return dao::$errors[''] = $this->lang->iwfp->errorMessageList['networkError'];
        }
        $toDoListData = json_decode($toDoListResult);
        if($toDoListData->success != '1'){
            return dao::$errors[''] = $toDoListData->errorMessage;
        }

        $toDoList = $toDoListData->data;
        $taskId = '';
        foreach ($toDoList as $toDo){
            if(!empty($toDo)){
                $taskId = $toDo->taskId;
            }
        }
        if(empty($taskId)){
            return dao::$errors[''] = $this->lang->iwfp->errorMessageList['todoEmpty'];
        }
        //3、组装参数
        $url = $this->config->global->getFreeJumpNodeListUrl;
        $pushData = array();
        $pushData['tenantId'] = $this->config->global->tenantId;
        $pushData['processInstanceId'] = $processInstanceId;
        $pushData['taskId'] = $taskId;
        $method = 'GET';
        $sendType = 'getFreeJumpNodeList';
        $result = $this->sendIwfp($url, $pushData, $method, $sendType, $objectCode);
        if(empty($result)){
            return dao::$errors[''] = $this->lang->iwfp->errorMessageList['networkError'];
        }
        $resultData = json_decode($result);
        if($resultData->success != '1'){
            return dao::$errors[''] = $resultData->errorMessage;
        }
        $response = $resultData->data;
        return $response;
    }

    /**
     * 自由跳转（跳转到审批过的节点）
     * @param $processInstanceId
     * @param $objectCode
     * @param $targetXmlTaskId
     * @param $nextUser,多个逗号隔开
     * @return void
     */
    public function freeJump($processInstanceId, $targetXmlTaskId, $version){
        //1、校验参数
        if(empty($processInstanceId) || empty($targetXmlTaskId)){
            return dao::$errors[''] = $this->lang->iwfp->errorMessageList['emptyError'];
        }
        $iwfp = $this->getIwfpByProcessInstanceId($processInstanceId);
        if(empty($iwfp)){
            return dao::$errors[''] = $this->lang->iwfp->errorMessageList['iwfpEmpty'];
        }
        $objectCode = $iwfp->objectCode;

        //3、组装数据
        $url = $this->config->global->freeJumpUrl;
        $pushData = array();
        $pushData['tenantId'] = $this->config->global->tenantId;
        $pushData['currentTaskId'] = $iwfp->processTaskId;
        $pushData['targetXmlTaskId'] = $targetXmlTaskId;
        $nextUserList = array();
        $nodeDealUserList = json_decode($iwfp->nodeDealUser);
        if(is_array($nodeDealUserList->$targetXmlTaskId)){
            $nextUserArray = $nodeDealUserList->$targetXmlTaskId;
        }else{
            $nextUserArray = explode(',' , $nodeDealUserList->$targetXmlTaskId);
        }
        foreach ($nextUserArray as $nextUserObject) {
            $nextUser = new stdClass();
            $nextUser->userNo = $nextUserObject;
            $nextUser->userName = $nextUserObject;
            array_push($nextUserList, $nextUser);
        }
        $adminUser = new stdClass();
        $adminUser->userNo = 'admin';
        $adminUser->userName = 'admin';
        array_push($nextUserList, $adminUser);
        $pushData['userList'] = $nextUserList;


        $method = 'POST';
        $sendType = 'freeJump';
        $result = $this->sendIwfp($url, $pushData, $method, $sendType, $objectCode);
        if(empty($result)){
            return dao::$errors[''] = $this->lang->iwfp->errorMessageList['networkError'];
        }
        $resultData = json_decode($result);
        if($resultData->success != '1'){
            return dao::$errors[''] = $resultData->errorMessage;
        }
        $response = new stdClass();
        $response->dealUser = $nextUserArray;
        $response->status = $targetXmlTaskId;

        $updateData = new stdClass();
        //获取下一节点taskid
        $toDoListResult = $this->getToDoTaskList($processInstanceId, '', '0', $objectCode);
        if(empty($toDoListResult)){
            return dao::$errors[''] = $this->lang->iwfp->errorMessageList['networkError'];
        }
        $toDoListData = json_decode($toDoListResult);
        if($toDoListData->success != '1'){
            return dao::$errors[''] = $toDoListData->errorMessage;
        }

        $toDoList = $toDoListData->data;
        $taskId = '';
        $xmlTaskId = '';
        foreach ($toDoList as $toDo){
            if(!empty($toDo)){
                $taskId = $toDo->taskId;
                $xmlTaskId = $toDo->xmlTaskId;
            }
        }
        $updateData->processTaskId = $taskId;
        $updateData->processXmlTaskId = $xmlTaskId;
        //下一节点待处理人
        $updateData->dealUser = implode(',',$nextUserArray).',admin';

        //构建审批信息
        $allLogList = json_decode($iwfp->logList,true);
        $logList = $allLogList[$version];
        $isReturn = false;
        foreach ($logList as &$log){
            if($log['nodeName'] == $xmlTaskId){
                $log['result'] = 'pending';
                $isReturn = true;
                continue;
            }
            if($isReturn){
                $log['result'] = '';
                $log['comment'] = '';
                $log['dealDate'] = '';
                $log['dealUser'] = '';
            }
        }
        $allLogList[$version] = $logList;
        $updateData->logList = json_encode($allLogList);
        $this->dao->update(TABLE_IWFP)->data($updateData)->where('processInstanceId')->eq($processInstanceId)->exec();

        return $response;
    }

    /**撤回
     * @param $processInstanceId
     * @return void
     */
    public function withDraw($processInstanceId){
        //1、校验参数
        if(empty($processInstanceId)){
            return dao::$errors[''] = $this->lang->iwfp->errorMessageList['emptyError'];
        }
        $iwfp = $this->getIwfpByProcessInstanceId($processInstanceId);
        if(empty($iwfp)){
            return dao::$errors[''] = $this->lang->iwfp->errorMessageList['iwfpEmpty'];
        }
        $objectCode = $iwfp->objectCode;
        //2、获取最后一个已办任务id
        $url = $this->config->global->listApproveLogUrl;
        $pushData = array();
        $pushData['tenantId'] = $this->config->global->tenantId;
        $pushData['processInstanceId'] = $iwfp->processInstanceId;
        $method = 'GET';
        $sendType = 'listApproveLog';
        $logResult = $this->sendIwfp($url, $pushData, $method, $sendType, $iwfp->objectCode);
        if(empty($logResult)){
            return dao::$errors[''] = $this->lang->iwfp->errorMessageList['networkError'];
        }
        $logResultData = json_decode($logResult);
        if($logResultData->success != '1'){
            return dao::$errors[''] = $logResultData->errorMessage;
        }
        $logArray = $logResultData->data;
        $log = end($logArray);
        if(empty($log)){
            return dao::$errors[''] = $this->lang->iwfp->errorMessageList['logLastEmpty'];
        }
        //3、组装数据
        $url = $this->config->global->withDrawUrl;
        $pushData = array();
        $pushData['tenantId'] = $this->config->global->tenantId;
        $pushData['taskId'] = $log->taskId;
        $method = 'POST';
        $sendType = 'withDraw';
        $result = $this->sendIwfp($url, $pushData, $method, $sendType, $objectCode);
        if(empty($result)){
            return dao::$errors[''] = $this->lang->iwfp->errorMessageList['networkError'];
        }
        $resultData = json_decode($result);
        if($resultData->success != '1'){
            return dao::$errors[''] = $resultData->errorMessage;
        }
        $response = $resultData->data;
        return $response;
    }

    /**
     * 加签
     * @param $processInstanceId
     * @param $addSignType 加签类型 1：前加签，2：后加签
     * @param $addUserStr   加签待处理人
     * @param $dealUserNo 处理人
     * @return void
     */
    public function addSignTask($processInstanceId, $addSignType, $addUserStr, $dealUserNo){
        //1、校验参数
        if(empty($processInstanceId) || empty($addSignType) || empty($addUserStr) || empty($dealUserNo)){
            return dao::$errors[''] = $this->lang->iwfp->errorMessageList['emptyError'];
        }
        $iwfp = $this->getIwfpByProcessInstanceId($processInstanceId);
        if(empty($iwfp)){
            return dao::$errors[''] = $this->lang->iwfp->errorMessageList['iwfpEmpty'];
        }
        $objectCode = $iwfp->objectCode;
        //2、查找代办任务
        $toDoListResult = $this->getToDoTaskList($processInstanceId, $dealUserNo, '0', $objectCode);
        if(empty($toDoListResult)){
            return dao::$errors[''] = $this->lang->iwfp->errorMessageList['networkError'];
        }
        $toDoListData = json_decode($toDoListResult);
        if($toDoListData->success != '1'){
            return dao::$errors[''] = $toDoListData->errorMessage;
        }

        $toDoList = $toDoListData->data;
        $id = '';
        foreach ($toDoList as $toDo){
            if(!empty($toDo)){
                $id = $toDo->id;
            }
        }
        if(empty($id)){
            return dao::$errors[''] = $this->lang->iwfp->errorMessageList['todoEmpty'];
        }
        //3、组装数据
        $url = $this->config->global->addSignTaskUrl;
        $pushData = array();
        $pushData['tenantId'] = $this->config->global->tenantId;
        $pushData['id'] = $id;
        $pushData['addSignType'] = $addSignType;
        $addUserList = array();
        $addUserArray = explode(',' , $addUserStr);
        foreach ($addUserArray as $addUserObject) {
            $addUser = new stdClass();
            $addUser->userNo = $addUserObject;
            $addUser->userName = $addUserObject;
            array_push($addUserList, $addUser);
        }
        $adminUser = new stdClass();
        $adminUser->userNo = 'admin';
        $adminUser->userName = 'admin';
        array_push($addUserList, $adminUser);
        $pushData['addUserList'] = $addUserList;
        $pushData['dealUserNo'] = $dealUserNo;
        $pushData['dealUserName'] = $dealUserNo;

        $method = 'POST';
        $sendType = 'addSignTask';
        $result = $this->sendIwfp($url, $pushData, $method, $sendType, $objectCode);
        if(empty($result)){
            return dao::$errors[''] = $this->lang->iwfp->errorMessageList['networkError'];
        }
        $resultData = json_decode($result);
        if($resultData->success != '1'){
            return dao::$errors[''] = $resultData->errorMessage;
        }
        $response = $resultData->data;
        return $response;
    }

    /**
     * 委派
     * @param $processInstanceId
     * @param $oldUserNo
     * @param $newUserNo
     * @return void
     */
    public function changeAssigneek($processInstanceId, $oldUserNo, $newUserNo,$version){
        //1、校验参数
        if(empty($processInstanceId) || empty($oldUserNo) || empty($newUserNo)){
            return dao::$errors[''] = $this->lang->iwfp->errorMessageList['emptyError'];
        }
        $iwfp = $this->getIwfpByProcessInstanceId($processInstanceId);
        if(empty($iwfp)){
            return dao::$errors[''] = $this->lang->iwfp->errorMessageList['iwfpEmpty'];
        }
        $dealUserIwfp = explode(',',$iwfp->dealUser);
        if(!in_array($oldUserNo, $dealUserIwfp)){
            return dao::$errors[''] = $this->lang->iwfp->errorMessageList['todoEmpty'];
        }
        $objectCode = $iwfp->objectCode;

        //修改数据库的值
        $xmlTaskId = $iwfp->processXmlTaskId;
        if(empty($iwfp->assign)){
            $assignList = array();
        }else{
            $assignList = json_decode($iwfp->assign);
        }
        $isChange = false;
        foreach ($assignList as &$assign){
            if($assign->assignUserNo == $oldUserNo){
                $assign->assignUserNo = $newUserNo;
                $isChange = true;
            }
        }
        if(!$isChange){
            $assignObj = new stdClass();
            $assignObj->userNo = $oldUserNo;
            $assignObj->assignUserNo = $newUserNo;
            array_push($assignList, $assignObj);
        }


        $updateData = new stdClass();
        $updateData->assign = json_encode($assignList);
        //构建审批信息
        $allLogList = json_decode($iwfp->logList,true);
        $logList = $allLogList[$version];
        foreach ($logList as &$log){
            if($log['nodeName'] == $xmlTaskId){
                foreach ($log['toDealUser'] as &$dealUser){
                    if($dealUser == $oldUserNo){
                        $dealUser = $newUserNo;
                    }
                }
            }
        }
        $allLogList[$version] = $logList;
        $updateData->logList = json_encode($allLogList);
        $updateData->dealUser = $iwfp->dealUser.','.$newUserNo;

        $this->dao->update(TABLE_IWFP)->data($updateData)->where('processInstanceId')->eq($processInstanceId)->exec();
    }

    /**
     * 查询svg图
     * @param $processInstanceId
     * @return void
     */
    public function queryProcessTrackImage($processInstanceId){
        //1、校验参数
        if(empty($processInstanceId)){
            return dao::$errors[''] = $this->lang->iwfp->errorMessageList['emptyError'];
        }
        $iwfp = $this->getIwfpByProcessInstanceId($processInstanceId);
        if(empty($iwfp)){
            return dao::$errors[''] = $this->lang->iwfp->errorMessageList['iwfpEmpty'];
        }
        $objectCode = $iwfp->objectCode;
        //2、组装数据
        $url = $this->config->global->queryProcessTrackImageUrl;
        $pushData = array();
        $pushData['tenantId'] = $this->config->global->tenantId;
        $pushData['processInstanceId'] = $processInstanceId;

        $method = 'GET';
        $sendType = 'queryProcessTrackImage';
        $result = $this->sendIwfp($url, $pushData, $method, $sendType, $objectCode);
        if(empty($result)){
            return dao::$errors[''] = $this->lang->iwfp->errorMessageList['networkError'];
        }
        $resultData = json_decode($result);
        if($resultData->success != '1'){
            return dao::$errors[''] = $resultData->errorMessage;
        }
        $response = $resultData->data;
        return $response;
    }

    /**
     * 获取当前版本的审核信息
     *
     * @param $processInstanceId
     * @param $version
     * @return array
     */
    public function getCurrentVersionReviewNodes($processInstanceId, $version){
        $data = [];
        if(!$processInstanceId){
            return $data;
        }
        $iwfp = $this->getIwfpByProcessInstanceId($processInstanceId);
        if(!$iwfp){
            return $data;
        }
        $allLogList = json_decode($iwfp->logList, true);
        $logList = $allLogList[$version];
        return $logList;
    }

    /**
     * 获得所有的审核节点
     *
     * @param $objectType
     * @param $objectID
     * @return array
     */
    public function getAllVersionReviewNodes($processInstanceId){
        $data = [];
        if(!$processInstanceId){
            return $data;
        }
        $iwfp = $this->getIwfpByProcessInstanceId($processInstanceId);
        if(!$iwfp){
            return $data;
        }
        $allLogList = json_decode($iwfp->logList, true);
        return $allLogList;
    }

    /**
     * 获取代办或已办任务
     * @param $processInstanceId
     * @param $receiveUserNo
     * @param $isFinished
     * @return void
     */
    public function getDoTaskList($objectType, $receiveUserNo, $isFinished){
        //1.组装参数
        $url = $this->config->global->getToDoTaskListUrl;
        $pushData = array();
        $pushData['tenantId'] = $this->config->global->tenantId;
        $definitionKey = $this->lang->iwfp->templateKeyList[$objectType];
        $objectDefinitionKey = $this->config->global->$definitionKey;
        $pushData['processDefinitionKey'] = $objectDefinitionKey;
        if(!empty($receiveUserNo)){
            $pushData['receiveUserNo'] = $receiveUserNo;
        }
        $pushData['isFinished'] = $isFinished;
        $method = 'GET';
        $sendType = 'getToDoTaskList';
        $result = $this->sendIwfp($url, $pushData, $method, $sendType, '');
        return $result;
    }


    /***
     *
     * 私有方法不提供外部调用，不做参数检验
     *
     ***/

    /**
     * 根据流程id获取流程信息
     * @param $processInstanceId
     * @return void
     */
    private function getIwfpByProcessInstanceId($processInstanceId){
        $iwfp = $this->dao->select('*')->from(TABLE_IWFP)->where('processInstanceId')->eq($processInstanceId)
            ->fetch();
        return $iwfp;
    }

    /**
     * 根据流程ids获取流程信息列表
     *
     * @param $processInstanceIds
     * @param  $select
     * @return mixed
     */
    private function getIwfpListByProcessInstanceIds($processInstanceIds, $select = '*'){
        $data = [];
        if(empty($processInstanceIds)){
            return $data;
        }
        $ret = $this->dao->select($select)->from(TABLE_IWFP)->where('processInstanceId')->in($processInstanceIds)
            ->fetchAll();
        if($ret){
            $data = $ret;
        }
        return $data;
    }
    /**
     * 获取代办或已办任务
     * @param $processInstanceId
     * @param $receiveUserNo
     * @param $isFinished
     * @return void
     */
    private function getToDoTaskList($processInstanceId, $receiveUserNo, $isFinished, $objectCode){
        //1.组装参数
        $url = $this->config->global->getToDoTaskListUrl;
        $pushData = array();
        $pushData['tenantId'] = $this->config->global->tenantId;
        $pushData['processInstanceId'] = $processInstanceId;
        if(!empty($receiveUserNo)){
            $pushData['receiveUserNo'] = $receiveUserNo;
        }
        $pushData['isFinished'] = $isFinished;
        $method = 'GET';
        $sendType = 'getToDoTaskList';
        $result = $this->sendIwfp($url, $pushData, $method, $sendType, $objectCode);
        return $result;
    }


    /**
     * 获取授权按钮
     * @param $taskId   //流程id
     * @param $objectCode   //数据编号
     * @return void
     */
    private function getButtonList($taskId, $objectCode){
        //1.组装参数
        $url = $this->config->global->getButtonListUrl;
        $pushData = array();
        $pushData['tenantId'] = $this->config->global->tenantId;
        $pushData['taskId'] = $taskId;
        $method = 'GET';
        $sendType = 'getButtonList';
        $result = $this->sendIwfp($url, $pushData, $method, $sendType, $objectCode);
        return $result;
    }



    /**
     * 根据业务数据查询工作流id
     * @param $objectType
     * @param $objectId
     * @param $processDefinitionKey
     * @param $status
     * @return void
     */
    private function getWorkFlowByObejctId($objectType, $objectId, $processDefinitionKey, $status){
        $iwfpList = $this->dao->select('*')->from(TABLE_IWFP)->where('objectType')->eq($objectType)
            ->andWhere('objectID')->eq($objectId)
            ->andWhere('processDefinitionKey')->eq($processDefinitionKey)
            ->andWhere('status')->eq($status)
            ->orderBy('id_desc')
            ->fetch();
        return $iwfpList;
    }

    /**
     * 调用http接口
     * @param $url
     * @param $pushData
     * @param $method
     * @param $sendType
     * @param $objectCode
     * @return mixed
     */
    private function sendIwfp($url, $pushData, $method, $sendType, $objectCode){
        $startDate = microtime(true)*1000;
        $this->loadModel('requestlog');
        $headers = array();
        $headers[] = 'tenantId: ' . $this->config->global->tenantId;
        $authorizationKey = $this->createToken();
        $headers[] = 'AuthorizationKey: ' . $authorizationKey;
        $extra = '';
        if($method == 'POST'){
            $result = $this->loadModel('requestlog')->http($url, $pushData, $method, 'json', array(), $headers);
        }else if($method == 'GET'){
            if(!empty($pushData)){
                $queryStr = '';
                foreach ($pushData as $key => $value){
                    $queryStr = $queryStr.$key.'='.$value.'&';
                }
                $url = $url.'?'.rtrim($queryStr, '&');
            }
            $result = $this->loadModel('requestlog')->http($url, '', $method, 'json', array(), $headers);
        }

        if(empty($result)){
            $status = 'fail';
        }else{
            $status = 'success';
        }
        $this->requestlog->saveRequestLog($url, 'iwfp', $sendType, $method, $pushData, $result, $status, microtime(true)*1000-$startDate, $objectCode);
        return $result;
    }

    /**
     * 发送发起流程接口
     * @return void
     */
    private function sendStartWorkFlow($objectDefinitionKey, $objectCode, $objectId, $objectType, $createdBy){
        $url = $this->config->global->startWorkFlowUrl;
        $pushData = array();
        $pushData['processDefinitionKey'] = $objectDefinitionKey;
        $pushData['businessId'] = $objectCode;
        $pushData['busiName'] = $objectType.'-'.$objectId;
        $pushData['tenantId'] = $this->config->global->tenantId;
        $nextUserList = array();
        $nextUser = new stdClass();
        $nextUser->userNo = $createdBy;
        $nextUser->userName = $createdBy;
        array_push($nextUserList, $nextUser);
        $pushData['nextUserList'] = $nextUserList;
        $method = 'POST';
        $sendType = 'startWorkFlow';
        $result = $this->sendIwfp($url, $pushData, $method, $sendType, $objectCode);
        return $result;
    }

    /**
     * 插入iwfp数据
     * @param $data
     * @return void
     */
    private function insertIwfpData($data,$objectType,$objectId,$objectCode,$createdBy,$nodeDealUser){
        $createData = new stdClass();
        $createData->processInstanceId = $data->processInstanceId;
        $createData->processDefinitionKey = $data->processDefinitionKey;
        $createData->processDefinitionId = $data->processDefinitionId;
        $createData->objectType = $objectType;
        $createData->objectID = $objectId;
        $createData->objectCode = $objectCode;
        $createData->status = 'running';
        $createData->createdDate = helper::now();
        $createData->createdBy = $createdBy;
        $createData->delete = 1;
        $createData->nodeDealUser = json_encode($nodeDealUser);
        $this->dao->insert(TABLE_IWFP)->data($createData)->exec();
    }

    /**
     * 获取模版节点信息
     * @param $processInstanceId
     * @param $receiveUserNo
     * @param $isFinished
     * @return void
     */
    private function getNodeInfoList($objectDefinitionKey){
        //1.组装参数
        $url = $this->config->global->getTaskDefListUrl;
        $pushData = array();
        $pushData['templateId'] = $objectDefinitionKey;
        $method = 'GET';
        $sendType = 'getTaskDefList';
        $result = $this->sendIwfp($url, $pushData, $method, $sendType, '');
        $nodeInfoList = array();
        if(!empty($result)){
            $resultData = json_decode($result);
            if ($resultData->success == 'true') {
                foreach ($resultData->data as $data){
                    $nodeInfo = array();
                    $nodeInfo['id'] = $data->xmlTaskId;
                    $nodeInfo['name'] = $data->xmlTaskName;
                    $nodeInfo['taskType'] = $data->taskType;
                    $nodeInfoList[$nodeInfo['id']] = $nodeInfo;
                }
            }else{
                return dao::$errors[''] = $resultData->errorMessage;
            }
        }else{
            return dao::$errors[''] = $this->lang->iwfp->errorMessageList['networkError'];
        }
        return $nodeInfoList;
    }

    /**
     * 构建历史记录
     * @return void
     */
    private function buildLogList($processInstanceId,$logNodeArray,$nodeDealUser,$xmlTaskId,$version,$iwfp, $nodeInfoList){
        //构建审批信息
        $allLogList = array();
        $logList = array();
        $xmlTaskId = zget($this->lang->iwfp->changStatusList, $xmlTaskId);
        if(empty($processInstanceId)){
            $isjumpNode = true;
            foreach ($logNodeArray as $logNode => $logValue){
                if($nodeInfoList[$logNode]['taskType'] == 'RuleJoinTask'){
                    $toDealUserList = $nodeDealUser[$logNode];
                    $joinVersion = time();
                    foreach ($toDealUserList as $toDealUser){
                        $log = new stdClass();
                        $log->nodeName = $logNode;
                        $toDealUserArray = array($toDealUser);
                        $log->toDealUser = $toDealUserArray;
                        $log->dealUser = '';
                        if($xmlTaskId == $logNode){
                            $log->result = 'pending';
                            $isjumpNode = false;
                        }else{
                            if($isjumpNode){
                                if(in_array($logNode,$this->lang->iwfp->ingoreStatusList)){
                                    $log->result = 'pass';
                                }else{
                                    $log->result = 'ignore';
                                }
                            }else{
                                $log->result = '';
                            }
                        }
                        $log->comment = '';
                        $log->dealDate = '';
                        $log->version = $version;
                        $log->joinVersion = $joinVersion;
                        if(!empty($log->toDealUser)){
                            array_push($logList, $log);
                        }
                    }
                }else{
                    $log = new stdClass();
                    $log->nodeName = $logNode;
                    $log->toDealUser = $nodeDealUser[$logNode];
                    $log->dealUser = '';
                    if($xmlTaskId == $logNode){
                        $log->result = 'pending';
                        $isjumpNode = false;
                    }else{
                        if($isjumpNode){
                            if(in_array($logNode,$this->lang->iwfp->ingoreStatusList)){
                                $log->result = 'pass';
                            }else{
                                $log->result = 'ignore';
                            }
                        }else{
                            $log->result = '';
                        }
                    }
                    $log->comment = '';
                    $log->dealDate = '';
                    $log->version = $version;
                    if(!empty($log->toDealUser)){
                        array_push($logList, $log);
                    }
                }
            }
            $allLogList[$version] = $logList;
        }else{
            $isjumpNode = true;
            $allLogList = json_decode($iwfp->logList,true);
            foreach ($logNodeArray as $logNode=>$logValue){
                if($nodeInfoList[$logNode]['taskType'] == 'RuleJoinTask'){
                    $toDealUserList = $nodeDealUser[$logNode];
                    foreach ($toDealUserList as $toDealUser){
                        $log = new stdClass();
                        $log->nodeName = $logNode;
                        $toDealUserArray = array($toDealUser);
                        $log->toDealUser = $toDealUserArray;
                        $log->dealUser = '';
                        if($xmlTaskId == $logNode){
                            $log->result = 'pending';
                            $isjumpNode = false;
                        }else{
                            if($isjumpNode){
                                if(in_array($logNode,$this->lang->iwfp->ingoreStatusList)){
                                    $log->result = 'pass';
                                }else{
                                    $log->result = 'ignore';
                                }
                            }else{
                                $log->result = '';
                            }
                        }
                        $log->comment = '';
                        $log->dealDate = '';
                        $log->version = $version;
                        if(!empty($log->toDealUser)){
                            array_push($logList, $log);
                        }
                    }
                }else{
                    $log = new stdClass();
                    $log->nodeName = $logNode;
                    $log->toDealUser = $nodeDealUser[$logNode];
                    $log->dealUser = '';
                    if($xmlTaskId == $logNode){
                        $log->result = 'pending';
                        $isjumpNode = false;
                    }else{
                        if($isjumpNode){
                            if(in_array($logNode,$this->lang->iwfp->ingoreStatusList)){
                                $log->result = 'pass';
                            }else{
                                $log->result = 'ignore';
                            }
                        }else{
                            $log->result = '';
                        }
                    }
                    $log->comment = '';
                    $log->dealDate = '';
                    $log->version = $version;
                    if(!empty($log->toDealUser)){
                        array_push($logList, $log);
                    }
                }
            }
            $allLogList[$version] = $logList;
        }
        return $allLogList;
    }

    /**
     * 获取授权按钮
     * @return void
     */
    private function choseBtn_V2($nodeInfoList, $processXmlTaskId, $iwfp, $version, $dealResult, $btnList,$userVariableList){
        //如果是会签节点
        $choseBtn = array();
        if($nodeInfoList[$processXmlTaskId]['taskType'] == 'RuleJoinTask'){
            $allLogList = json_decode($iwfp->logList,true);
            $logList = $allLogList[$version];
            $revewNum = 1;
            $passNum = $dealResult == 1?1:0;
            $joinVersion = 0;
            foreach ($logList as $log){
                if($log['nodeName'] == $processXmlTaskId){
                    if($log['joinVersion'] != $joinVersion){
                        $joinVersion = $log['joinVersion'];
                        $revewNum = 1;
                        $passNum = $dealResult == 1?1:0;
                    }
                    if($log['result'] != 'pending'){
                        $revewNum++;
                        if($log['result'] == 'pass'){
                            $passNum++;
                        }
                    }
                }
            }
            $passrate = $passNum/$revewNum;
            $chosebtn = false;
            foreach ($btnList as $btn){
                if(!empty($btn->userVariable)){
                    $btnUserVariableList = json_decode($btn->userVariable);
                    foreach ($btnUserVariableList as $key=>$value){
                        if($key == 'passrate'){
                            if($value == 0){
                                if($passrate <= $value){
                                    $chosebtn = false;
                                }else{
                                    $chosebtn = true;
                                }
                            }else{
                                if($passrate < $value){
                                    $chosebtn = false;
                                }else{
                                    $chosebtn = true;
                                }
                            }
                        }
                    }
                }
            }
            foreach ($btnList as $btn){
                $chose = true;
                if(!empty($btn->userVariable)){
                    $btnUserVariableList = json_decode($btn->userVariable);
                    foreach ($btnUserVariableList as $key=>$value){
                        if($key == 'result'){
                            if($chosebtn && $value != '1'){
                                $chose = false;
                            }else if(!$chosebtn && $value != '2'){
                                $chose = false;
                            }
                        }else if($key != 'result' && $key != 'passrate'){
                            if(empty($userVariableList->$key) || $value != $userVariableList->$key){
                                $chose = false;
                            }
                        }
                    }
                }else{
                    $chose = false;
                }
                if($chose){
                    $choseBtn = $btn;
                    break;
                }
            }
        }else{
            foreach ($btnList as $btn){
                $chose = true;
                if(!empty($btn->userVariable)){
                    $btnUserVariableList = json_decode($btn->userVariable);
                    foreach ($btnUserVariableList as $key=>$value){
                        if($key == 'skip'){
                            continue;
                        }else if($key == 'result'){
                            if($value != $dealResult){
                                $chose = false;
                            }
                        }else{
                            if(empty($userVariableList->$key) || $value != $userVariableList->$key){
                                $chose = false;
                            }
                        }
                    }
                }else{
                    $chose = false;
                }
                if($chose){
                    $choseBtn = $btn;
                    break;
                }
            }
        }
        return $choseBtn;
    }

    /**
     * 获取授权按钮
     * @return void
     */
    private function choseBtn($nodeInfoList, $processXmlTaskId, $iwfp, $version, $dealResult, $btnList,$userVariableList){
        //如果是会签节点
        $choseBtn = array();
        if($nodeInfoList[$processXmlTaskId]['taskType'] == 'RuleJoinTask'){
            $allLogList = json_decode($iwfp->logList,true);
            $logList = $allLogList[$version];
            $revewNum = 1;
            $passNum = $dealResult == 1?1:0;
            $joinVersion = 0;
            foreach ($logList as $log){
                if($log['nodeName'] == $processXmlTaskId){
                    if($log['joinVersion'] != $joinVersion){
                        $joinVersion = $log['joinVersion'];
                        $revewNum = 1;
                        $passNum = $dealResult == 1?1:0;
                    }
                    if($log['result'] != 'pending'){
                        $revewNum++;
                        if($log['result'] == 'pass'){
                            $passNum++;
                        }
                    }
                }
            }
            $passrate = $passNum/$revewNum;
            $chosebtn = false;
            foreach ($btnList as $btn){
                if(!empty($btn->userVariable)){
                    $btnUserVariableList = json_decode($btn->userVariable);
                    foreach ($btnUserVariableList as $key=>$value){
                        if($key == 'passrate'){
                            if($value == 0){
                                if($passrate <= $value){
                                    $chosebtn = false;
                                }else{
                                    $chosebtn = true;
                                }
                            }else{
                                if($passrate < $value){
                                    $chosebtn = false;
                                }else{
                                    $chosebtn = true;
                                }
                            }
                        }
                    }
                }
            }
            foreach ($btnList as $btn){
                if(!empty($btn->userVariable)){
                    $btnUserVariableList = json_decode($btn->userVariable);
                    foreach ($btnUserVariableList as $key=>$value){
                        if($key == 'result'){
                            if($chosebtn && $value == '1'){
                                $choseBtn = $btn;
                                break;
                            }else if(!$chosebtn && $value == '2'){
                                $choseBtn = $btn;
                                break;
                            }
                        }
                    }
                }
            }
        }else{
            foreach ($btnList as $btn){
                $chose = true;
                if(!empty($btn->userVariable)){
                    $btnUserVariableList = json_decode($btn->userVariable);
                    foreach ($btnUserVariableList as $key=>$value){
                        if($key == 'skip'){
                            continue;
                        }else if($key == 'result'){
                            if($value != $dealResult){
                                $chose = false;
                            }
                        }else{
                            if(empty($userVariableList->$key) || $value != $userVariableList->$key){
                                $chose = false;
                            }
                        }
                    }
                }else{
                    $chose = false;
                }
                if($chose){
                    $choseBtn = $btn;
                    break;
                }
            }
        }
        return $choseBtn;
    }

    /**
     * 构建完成任务的发送数据
     * @return void
     */
    private function buildTaskPushData($choseBtn, $taskId, $realDeal,$dealResult,$version,$dealUser,$dealUserIwfp,$iwfp,$toDealUserList){
        $pushData = array();
        $pushData['tenantId'] = $this->config->global->tenantId;
        $pushData['btnId'] = $choseBtn->btnId;
        $pushData['taskId'] = $taskId;
        $pushData['dealUserNo'] = $realDeal;
        $pushData['dealUserName'] = $realDeal;
        $dealMessageObject = new stdClass();
        //$dealMessageObject->dealMessage = $dealMessage;
        $dealMessageObject->dealResult = $dealResult;
        $dealMessageObject->version = $version;
        $dealMessageObject->dealUser = $dealUser;
        $dealMessageObject->todealUser = $dealUserIwfp;
        $pushData['dealMessage'] = json_encode($dealMessageObject);
        $nextUserList = array();
        $nodeDealUserList = json_decode($iwfp->nodeDealUser);
        $nextStatus = $choseBtn->xmlTaskId;
        if(empty($toDealUserList)){
            if(!empty($nodeDealUserList->$nextStatus)){
                if(is_array($nodeDealUserList->$nextStatus)){
                    $nextUserArray = $nodeDealUserList->$nextStatus;
                }else{
                    $nextUserArray = explode(',' , $nodeDealUserList->$nextStatus);
                }
                $isAdmin = false;
                foreach ($nextUserArray as $nextUserObject) {
                    if(!empty($nextUserObject)){
                        $nextUser = new stdClass();
                        $nextUser->userNo = $nextUserObject;
                        $nextUser->userName = $nextUserObject;
                        array_push($nextUserList, $nextUser);
                        if($nextUserObject == 'admin'){
                            $isAdmin = true;
                        }
                    }
                }
                $pushData['nextUserList'] = $nextUserList;
            }else{
                $nextUserArray = [];
            }
        }else{
            $isAdmin = false;
            $nextUserArray = $toDealUserList;
            foreach ($toDealUserList as $nextUserObject) {
                if(!empty($nextUserObject)){
                    $nextUser = new stdClass();
                    $nextUser->userNo = $nextUserObject;
                    $nextUser->userName = $nextUserObject;
                    array_push($nextUserList, $nextUser);
                    if($nextUserObject == 'admin'){
                        $isAdmin = true;
                    }
                }
            }
            $pushData['nextUserList'] = $nextUserList;
        }

        if(empty($userVariableList)){
            $userVariableList = new stdClass();
        }
        $userVariableList->result = $dealResult;
        $pushData['userVariable'] = json_encode($userVariableList);
        $resultObj = array();
        $resultObj['pushData'] = $pushData;
        $resultObj['nextUserArray'] = $nextUserArray;
        return $resultObj;
    }

    private function buildTaskLog($iwfp,$version,$processXmlTaskId,$dealUser,$nextUserArray,$isSkip,$dealResult,$dealMessage,$toXmlTask,$nodeInfoList,$toDealUserArray,&$updateData,$toDealUserList){
        //构建审批信息
        $allLogList = json_decode($iwfp->logList,true);
        $logList = $allLogList[$version];
        $addNode = true;
        $nowNode = -1;
        $nodeNum = 0;
        if(empty($toDealUserList)){
            foreach ($logList as &$log){
                if($log['nodeName'] == $processXmlTaskId && $log['result'] == 'pending'){
                    if(in_array($dealUser, $nextUserArray) and $isSkip){
                        $log['result'] = 'ignore';
                        $log['comment'] = '';
                        $log['dealDate'] = helper::now();
                        $log['dealUser'] = $dealUser;
                    }else{
                        if(in_array($dealUser, $log['toDealUser'])){
                            $log['result'] = $this->lang->iwfp->dealResultKeyList[$dealResult];
                            $log['comment'] = $dealMessage;
                            $log['dealDate'] = helper::now();
                            $log['dealUser'] = $dealUser;
                        }
                    }
                    $nowNode = $nodeNum;
                }else if($log['nodeName'] == $toXmlTask && ($log['result'] == '' || $log['result'] == 'pending') && ($nowNode != -1)){
                    $log['result'] = 'pending';
                    $log['comment'] = '';
                    $log['dealDate'] = '';
                    $log['dealUser'] = '';
                }else if($log['nodeName'] == $toXmlTask && $log['result'] != '' && $log['result'] != 'pending' && $addNode && !in_array($toXmlTask, $this->lang->iwfp->ingoreStatusList) && $processXmlTaskId!=$toXmlTask){
                    if($nodeInfoList[$toXmlTask]['taskType'] == 'RuleJoinTask'){
                        $joinVersion = time();
                        foreach ($toDealUserArray as $toDealUser){
                            $newlog = array();
                            $newlog['nodeName'] = $toXmlTask;
                            $toDealUserArrayObj = array($toDealUser);
                            $newlog['toDealUser'] = $toDealUserArrayObj;
                            $newlog['dealUser'] = '';
                            $newlog['result'] = 'pending';
                            $newlog['comment'] = '';
                            $newlog['dealDate'] = '';
                            $newlog['version'] = $version;
                            $newlog['joinVersion'] = $joinVersion;
                            array_push($logList, $newlog);
                        }
                    }else{
                        $newlog = array();
                        $newlog['nodeName'] = $toXmlTask;
                        $newlog['toDealUser'] = $toDealUserArray;
                        $newlog['dealUser'] = '';
                        $newlog['result'] = 'pending';
                        $newlog['comment'] = '';
                        $newlog['dealDate'] = '';
                        $newlog['version'] = $version;
                        array_push($logList, $newlog);
                    }
                    $addNode = false;
                }
                $nodeNum++;
            }
        }else{
            $i = 0;
            $insertIndex = -1;
            $isAdd = true;
            foreach ($logList as &$log){
                if($log['nodeName'] == $processXmlTaskId){
                    if($log['result'] == 'pending'){
                        if(in_array($dealUser, $nextUserArray) and $isSkip){
                            $log['result'] = 'ignore';
                            $log['comment'] = '';
                        }else{
                            if(in_array($dealUser, $log['toDealUser'])){
                                $log['result'] = $this->lang->iwfp->dealResultKeyList[$dealResult];
                                $log['comment'] = $dealMessage;
                                $log['dealDate'] = helper::now();
                                $log['dealUser'] = $dealUser;
                            }
                        }
                    }
                    $insertIndex = $i;
                }
                if($log['nodeName'] == $toXmlTask && $insertIndex != -1){
                    if($log['result'] == '' || $log['result'] == 'pending'){
                        $log['result'] = 'pending';
                        $log['comment'] = '';
                        $log['dealDate'] = '';
                        $log['dealUser'] = '';
                        $isAdd = false;
                    }
                }
                $i++;
            }
            if($isAdd && $toXmlTask != $processXmlTaskId){
                if($nodeInfoList[$toXmlTask]['taskType'] == 'RuleJoinTask'){
                    $joinVersion = time();
                    foreach ($toDealUserList as $toDealUser){
                        $newlog = array();
                        $newlog['nodeName'] = $toXmlTask;
                        $toDealUserArrayObj = array($toDealUser);
                        $newlog['toDealUser'] = $toDealUserArrayObj;
                        $newlog['dealUser'] = '';
                        $newlog['result'] = 'pending';
                        $newlog['comment'] = '';
                        $newlog['dealDate'] = '';
                        $newlog['version'] = $version;
                        $newlog['joinVersion'] = $joinVersion;
                        $insertIndex = $insertIndex+1;
                        array_splice($logList, $insertIndex, 0, [(object)$newlog]);
                    }
                }else{
                    $newlog = array();
                    $newlog['nodeName'] = $toXmlTask;
                    $newlog['toDealUser'] = $toDealUserList;
                    $newlog['dealUser'] = '';
                    $newlog['result'] = 'pending';
                    $newlog['comment'] = '';
                    $newlog['dealDate'] = '';
                    $newlog['version'] = $version;
                    array_splice($logList, $insertIndex+1, 0, [(object)$newlog]);
                }
                //将节点待处理人保存在表格中，跳转时直接获取
                $nodeDealUserList = json_decode($iwfp->nodeDealUser);
                $nodeDealUserList->$toXmlTask = $toDealUserList;
                $updateData->nodeDealUser = json_encode($nodeDealUserList);
            }
        }
        $allLogList[$version] = $logList;
        return $allLogList;
    }

    /**
     * 创建token
     * @param $data
     * @param $refreshtoken
     * @return void
     */
    public function createToken()
    {

        $sm4 = new Sm4Helper();
        $time = time(); // 当前时间戳。
        $header = array(
            'alg'=>'Sm4',
            'typ'=>'JWT'
        );
        $payload = [
            'iss' => 'CFIT_IWFP', // 签发者。
            'iat' => $time-120, // 签发时间。
            'exp' => $time + 10 * 60, // 过期时间，不设置则永久有效。
            'tenantId' => $this->config->global->tenantId,
            'random' => uniqid('iwfp')
        ];
        $keyByte = base64_decode($this->config->global->AuthorizationKey);
        //第一部分
        $base64 = json_encode($header);
        $baseencode = base64_encode($base64);
        //第二部分
        $basepayLoad64=json_encode($payload);
        $base64payload = base64_encode($basepayLoad64);
        //第三部分，生成签名
        $input = $baseencode.".".$base64payload;
        $token = $sm4->encrypt($keyByte, $input);
        $token = $input.".".$token;
        return $token;
    }
}
