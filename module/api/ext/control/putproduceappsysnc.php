<?php
include '../../control.php';
class myApi extends api
{
    const PARAMS_MISSING = 1001; //缺少参数
    const PARAMS_ERROR   = 1002; //缺少参数
    const FAIL_CODE   = 999;    //请求失败

    public function putproduceappsysnc()
    {
        $this->loadModel('cmdbsync');
        $this->loadModel('application');
        $postData = fixer::input('post')->get();
        $logID = $this->loadModel('requestlog')->insideSaveRequestLog('cmdbsync' , 'putproduceappsysnc');
        $this->checkApiToken();
        if(empty($postData->numbers)){
            $this->requestlog->response('fail', '投产单号id不能为空', [], $logID, self::FAIL_CODE);
        }
        //查找数据
        $putproduction = $this->dao->select("*")
            ->from(TABLE_PUTPRODUCTION)
            ->where('code')->eq($postData->numbers)
            ->andWhere('deleted')->eq('0')
            ->fetch();
        if(empty($putproduction)){
            $this->requestlog->response('fail', '没有找到相关投产信息', [], $logID, self::FAIL_CODE);
        }
        //检查输入是否满足
        $jxApplicationList = $postData->systemList;
        $errMsg = $this->checkData($jxApplicationList);
        if(!empty($errMsg)) {
            $this->requestlog->response('fail', implode(',',$errMsg), [], $logID, self::FAIL_CODE);
        }
        //查找投产系统信息
        $applicationList = array();
        $applicationIdList = explode(',',$putproduction->app);
        foreach ($applicationIdList as $applicationId){
            $applicationObj = $this->dao->findByID($applicationId)->from(TABLE_APPLICATION)->fetch();
            array_push($applicationList, $applicationObj);
        }
        //比对系统信息
        $isDiffer = false;
        $info = array();  //差异信息
        $addInfo = array(); //新增系统信息
        $updateInfo = array();  //修改系统信息
        $deleteInfo = array();  //删除系统信息
        $apps = array();
        foreach ($applicationList as $applicationKey=>$applicationObj){
            $isExist = false;
            foreach ($jxApplicationList as $jxApplicationKey=>$jxApplicationObj){
                //编辑对比系统信息
                $jxApplicationObj = (object)$jxApplicationObj;
                if($applicationObj->id == $jxApplicationObj->id){
                    $isExist = true;
                    $updateObj = $this->differApplication($jxApplicationObj, $applicationObj);
                    if($updateObj->isDiffer){
                        $isDiffer = true;
                    }
                    array_push($updateInfo, $updateObj);
                    array_push($apps,$jxApplicationObj->id);
                    array_push($apps,$applicationObj->id);
                    unset($jxApplicationList[$jxApplicationKey]);
                }
            }
            if(!$isExist){
                //删除系统信息
                $deleteObject = new stdClass();
                $deleteObject->id = $applicationObj->id;
                $deleteObject->name = $applicationObj->name;
                $deleteObject->code = $applicationObj->code;
                array_push($deleteInfo, $deleteObject);
                array_push($apps,$applicationObj->id);
                $isDiffer = true;
            }
        }
        //新增系统信息
        foreach ($jxApplicationList as $jxApplicationObj){
            $isDiffer = true;
            $jxApplicationObj = (object)$jxApplicationObj;
            if(!empty($jxApplicationObj->id)){
                $applicationExit = $this->dao->findByID($jxApplicationObj->id)->from(TABLE_APPLICATION)->fetch();
                if(empty($applicationExit)){
                    $this->requestlog->response('fail', '未找到对应的金科id的系统', [], $logID, self::FAIL_CODE);
                }
                $updateObj = $this->differApplication($jxApplicationObj, $applicationExit);
                if($updateObj->isDiffer){
                    $isDiffer = true;
                }
                array_push($updateInfo, $updateObj);
                array_push($apps,$jxApplicationObj->id);
                array_push($apps,$applicationObj->id);
            }else{
                $this->addApplication($jxApplicationObj);
                array_push($addInfo, $jxApplicationObj);
                array_push($apps,$jxApplicationObj->id);
            }

        }

        //构建系统同步信息
        $info['isDiffer'] = $isDiffer;
        $info['deleteInfo'] = $deleteInfo;
        $info['addInfo'] = $addInfo;
        $info['updateInfo'] = $updateInfo;


        //新建cmdb同步数据
        $insertData = array();
        $insertData['type'] = 'putproduction';
        $insertData['isDiffer'] = $isDiffer;
        $insertData['putproductionId'] = $putproduction->id;
        $insertData['app'] = implode(',', array_unique($apps));
        $insertData['info'] = json_encode($info);
        if($isDiffer == true){
            $insertData['status'] = 'toconfirm';
            foreach ($this->lang->cmdbsync->apiDealUserList as $key => $value) {
                if(!empty($key)){
                    $userIds[] = $key;
                }
            }
            $insertData['dealUser'] = implode(',',$userIds);
        }else{
            $insertData['status'] = 'pass';
            $insertData['sendStatus'] = 'tosend';
        }
        $insertData['createdBy'] = 'guestjx';

        $this->dao->insert(TABLE_CMDBSYNC)
            ->data($insertData)
            ->exec();
        $lastInsertID = $this->dao->lastInsertID();
        if($isDiffer == false){
            $this->updateApplication($updateInfo, $lastInsertID);
        }
        if(dao::isError()) {
            $this->requestlog->response('fail', dao::getError(), [], $logID, self::FAIL_CODE);
        }
        $this->dao->update(TABLE_REQUESTLOG)->set('objectId')->eq($lastInsertID)->where('id')->eq($logID)->exec();
        $this->loadModel('action')->create('cmdbsync', $lastInsertID, 'synccreated', '','','guestjx');
        $this->requestlog->response('success', $this->lang->api->successful, array('id' => $lastInsertID), $logID);
    }

    /**
     * 修改系统信息
     * @param $updateInfo
     * @return void
     */
    public function updateApplication($updateInfo,$cmdbId){
        $updateAppInfo = $updateInfo;
        foreach ($updateAppInfo as $id=>$updateApp){
            $appId = '';
            $updateAppArray = array();
            foreach ($updateApp as $key => $app){
                if($app->isColumnDiffer){
                    $updateAppArray[$key] = $app->new;
                }else if($key == 'id'){
                    $appId = $app->new;
                }
            }
            if(!empty($updateAppArray)){
                $applicationOld = $this->dao->findByID($appId)->from(TABLE_APPLICATION)->fetch();
                $this->dao->update(TABLE_APPLICATION)
                    ->data($updateAppArray)->where('id')->eq($appId)->exec();
                $actionID = $this->loadModel('action')->create('application', $appId, 'syncupdate', '由CMDB同步单'.$cmdbId.'自动修改','',$this->app->user->account);
                $changes = common::createChanges($applicationOld, $updateAppArray);
                $this->action->logHistory($actionID, $changes);
            }
        }
    }

    /**
     * 检查输入是否正确
     * @param $jxApplicationList
     * @return void
     */
    public function checkData(&$jxApplicationList){
        $errMsg = [];
        $this->loadModel('cmdbsync');

        foreach ($jxApplicationList as &$application){
            foreach ($this->lang->cmdbsync->apiItem as $k => $v)
            {
                if($v['required'] && $application[$k] == ''){
                    $errMsg[] = $k.$v['name'].$application[$k].'不可以为空';
                }
                if($v['target'] != $k)
                {
                    $application[$v['target']] = $application[$k];
                    unset($application[$k]);
                }
            }
        }
        return $errMsg;
    }

    /**
     * 构建新建消息
     * @param $jxApplicationObj
     * @return void
     */
    public function differApplication($jxApplicationObj, $application){
        $updateObj = new stdClass();
        $this->loadModel('cmdbsync');
        $this->loadModel('application');
        foreach ($this->lang->cmdbsync->apiItem as $k => $v)
        {
            $updateDiffer = new stdClass();
            $key = $v['target'];
            if(!empty($v['chosen']) && $v['chosen'] == '1'){
                $langKey = $v['lang'];
                if($v['single'] == '1'){
                    if(!empty($v['analysis']) && $v['analysis'] == '1'){
                        $arrayValue = explode('-',$jxApplicationObj->$key);
                        if(!empty($arrayValue) && count($arrayValue) > 1){
                            unset($arrayValue[0]);
                            $jxApplicationObj->$key = implode('-',$arrayValue);
                        }
                    }
                    if(zget($this->lang->application->$langKey, $application->$key) == $jxApplicationObj->$key){
                        $updateDiffer->isColumnDiffer = false;
                        $updateDiffer->old = $application->$key;
                        $updateDiffer->new = array_search($jxApplicationObj->$key, $this->lang->application->$langKey);
                        $updateObj->$key = $updateDiffer;
                    }else{
                        $updateDiffer->isColumnDiffer = true;
                        $updateDiffer->old = $application->$key;
                        $updateDiffer->new = array_search($jxApplicationObj->$key, $this->lang->application->$langKey);
                        $updateObj->$key = $updateDiffer;
                        if(in_array($key, explode(',',$this->lang->cmdbsync->differItem))){
                            $updateObj->isDiffer = true;
                        }
                    }
                }else{
                    $newKeyList = array();
                    foreach ($jxApplicationObj->$key as $keyValue){
                        if(!empty($v['analysis']) && $v['analysis'] == '1'){
                            $arrayValue = explode('-',$keyValue);
                            if(!empty($arrayValue) && count($arrayValue) > 1){
                                unset($arrayValue[0]);
                                $keyValue = implode('-',$arrayValue);
                            }
                        }
                        array_push($newKeyList, array_search($keyValue, $this->lang->application->$langKey));
                    }
                    $oldKeyList = explode(',',trim($application->$key,','));
                    if(array_diff($newKeyList, $oldKeyList) == array_diff($oldKeyList, $newKeyList)){
                        $updateDiffer->isColumnDiffer = false;
                        $updateDiffer->old = $application->$key;
                        $updateDiffer->new = implode(',', $newKeyList);
                        $updateObj->$key = $updateDiffer;
                    }else{
                        $updateDiffer->isColumnDiffer = true;
                        $updateDiffer->old = $application->$key;
                        $updateDiffer->new = implode(',', $newKeyList);
                        $updateObj->$key = $updateDiffer;
                        if(in_array($key, explode(',',$this->lang->cmdbsync->differItem))){
                            $updateObj->isDiffer = true;
                        }
                    }
                }
            }else{
                if(!empty($v['input']) && $v['input'] == 'array'){
                    $jxApplicationObj->$key = implode(',', $jxApplicationObj->$key);
                }
                if($application->$key == $jxApplicationObj->$key){
                    $updateDiffer->isColumnDiffer = false;
                    $updateDiffer->old = $application->$key;
                    $updateDiffer->new = $jxApplicationObj->$key;
                    $updateObj->$key = $updateDiffer;
                }else{
                    $updateDiffer->isColumnDiffer = true;
                    $updateDiffer->old = $application->$key;
                    $updateDiffer->new = $jxApplicationObj->$key;
                    $updateObj->$key = $updateDiffer;
                    if(in_array($key, explode(',',$this->lang->cmdbsync->differItem))){
                        $updateObj->isDiffer = true;
                    }
                }
            }
        }
        return $updateObj;
    }

    public function addApplication(&$jxApplicationObj){
        $this->loadModel('cmdbsync');
        $this->loadModel('application');
        foreach ($this->lang->cmdbsync->apiItem as $k => $v)
        {
            $key = $v['target'];
            if(!empty($v['chosen']) && $v['chosen'] == '1'){
                $langKey = $v['lang'];
                if($v['single'] == '1'){
                    if(!empty($v['analysis']) && $v['analysis'] == '1'){
                        $arrayValue = explode('-',$jxApplicationObj->$key);
                        if(!empty($arrayValue) && count($arrayValue) > 1){
                            unset($arrayValue[0]);
                            $jxApplicationObj->$key = implode('-',$arrayValue);
                        }
                    }
                    $jxApplicationObj->$key = array_search($jxApplicationObj->$key, $this->lang->application->$langKey);
                }else{
                    $newKeyList = array();
                    foreach ($jxApplicationObj->$key as $keyValue){
                        if(!empty($v['analysis']) && $v['analysis'] == '1'){
                            $arrayValue = explode('-',$keyValue);
                            if(!empty($arrayValue) && count($arrayValue) > 1){
                                unset($arrayValue[0]);
                                $keyValue = implode('-',$arrayValue);
                            }
                        }
                        array_push($newKeyList, array_search($keyValue, $this->lang->application->$langKey));
                    }
                    $jxApplicationObj->$key = implode(',',$newKeyList);
                }
            }else{
                if(!empty($v['input']) && $v['input'] == 'array'){
                    $jxApplicationObj->$key = implode(',', $jxApplicationObj->$key);
                }
            }
        }
    }
}
