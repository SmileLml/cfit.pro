<?php
include '../../control.php';
class myApi extends api
{
    const PARAMS_MISSING = 1001; //缺少参数
    const PARAMS_ERROR   = 1002; //缺少参数
    const FAIL_CODE   = 999;    //请求失败

    public function putcmdbsysnc()
    {
        $this->loadModel('cmdbsync');
        $this->loadModel('application');
        $postData = fixer::input('post')->get();
        $logID = $this->loadModel('requestlog')->insideSaveRequestLog('cmdbsync' , 'putcmdbsysnc');
        $this->checkApiToken();
        if(empty($postData->cfidKey)){
            $this->requestlog->response('fail', '金信系统id不能为空', [], $logID, self::FAIL_CODE);
        }
        if($postData->baselineRelated == '是' && empty($postData->baselineSystem)){
            $this->requestlog->response('fail', '属于基线，基线系统不能为空', [], $logID, self::FAIL_CODE);
        }
        //查找数据
        $application = $this->dao->select("*")
            ->from(TABLE_APPLICATION)
            ->where('cfidKey')->eq($postData->cfidKey)
            ->andWhere('deleted')->eq('0')
            ->fetch();
        if(empty($application)){
            $this->requestlog->response('fail', '没有找到相关系统信息', [], $logID, self::FAIL_CODE);
        }

        $applicationCikeyList = $this->dao->select("*")
            ->from(TABLE_APPLICATION)
            ->where('ciKey')->eq($postData->ciKey)
            ->andWhere('deleted')->eq('0')
            ->fetchAll('');
        if(count($applicationCikeyList) > 1){
            $this->requestlog->response('fail', '有多个系统对应此cikey', [], $logID, self::FAIL_CODE);
        }
        if(count($applicationCikeyList) == 1){
            $applicationCfidKeyList = $this->dao->select("*")
                ->from(TABLE_APPLICATION)
                ->where('ciKey')->eq($postData->ciKey)
                ->andWhere('cfidKey')->eq($postData->cfidKey)
                ->andWhere('deleted')->eq('0')
                ->fetchAll('');
            if(count($applicationCfidKeyList) > 1){
                $this->requestlog->response('fail', '有多个系统对应此cikey', [], $logID, self::FAIL_CODE);
            }else if(count($applicationCfidKeyList) == 1 && $applicationCfidKeyList[0]->id != $applicationCikeyList[0]->id){
                $this->requestlog->response('fail', '已有系统关联此cikey', [], $logID, self::FAIL_CODE);
            }else if(count($applicationCfidKeyList) == 0){
                $this->requestlog->response('fail', '已有系统关联此cikey', [], $logID, self::FAIL_CODE);
            }
        }
        /*if(count($applicationCikeyList) == 1 && !empty($applicationCikeyList[0]->cfidKey)){
            $this->requestlog->response('fail', '没有找到相关系统信息', [], $logID, self::FAIL_CODE);
        }*/

        $errMsg = $this->checkData($postData);
        if(!empty($errMsg)) {
            $this->requestlog->response('fail', implode(',',$errMsg), [], $logID, self::FAIL_CODE);
        }

        //构建系统同步信息
        $info = array();  //差异信息
        $updateInfo = array();  //修改系统信息
        $updateObj = $this->differApplication($postData, $application);
        array_push($updateInfo, $updateObj);
        $info['updateInfo'] = $updateInfo;

        //新建cmdb同步数据
        $insertData = array();
        $insertData['type'] = 'cmdb';
        $insertData['app'] = $application->id;
        $insertData['info'] = json_encode($info);
        $insertData['status'] = 'pass';
        $insertData['createdBy'] = 'guestjx';

        $this->dao->insert(TABLE_CMDBSYNC)
            ->data($insertData)
            ->exec();
        $lastInsertID = $this->dao->lastInsertID();
        if(dao::isError()) {
            $this->requestlog->response('fail', dao::getError(), [], $logID, self::FAIL_CODE);
        }
        $this->dao->update(TABLE_APPLICATION)->data($postData)->where('id')->eq($application->id)->exec();
        $this->dao->update(TABLE_REQUESTLOG)->set('objectId')->eq($lastInsertID)->where('id')->eq($logID)->exec();
        $this->loadModel('action')->create('cmdbsync', $lastInsertID, 'synccreated', '','','guestjx');
        $actionID = $this->loadModel('action')->create('application', $application->id, 'syncupdate', '由CMDB同步单'.$lastInsertID.'自动修改','','guestjx');
        $changes = common::createChanges($application, $postData);
        $this->action->logHistory($actionID, $changes);
        $this->requestlog->response('success', $this->lang->api->successful, array('id' => $lastInsertID), $logID);
    }

    /**
     * 检查输入是否正确
     * @param $jxApplicationList
     * @return void
     */
    public function checkData(&$application){
        $errMsg = [];
        $this->loadModel('cmdbsync');

        foreach ($this->lang->cmdbsync->putcmdbsysncApiItem as $k => $v)
        {
            if($v['required'] && $application->$k == ''){
                $errMsg[] = $k.$v['name'].$application->$k.'不可以为空';
            }
            if($v['target'] != $k)
            {
                $key = $v['target'];
                $application->$key = $application->$k;
                unset($application->$k);
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
        foreach ($this->lang->cmdbsync->putcmdbsysncApiItem as $k => $v)
        {
            $updateDiffer = new stdClass();
            $key = $v['target'];
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
            }
        }
        $updateDiffer = new stdClass();
        $updateDiffer->isColumnDiffer = false;
        $updateDiffer->old = $application->name;
        $updateDiffer->new = $application->name;
        $updateObj->name = $updateDiffer;
        return $updateObj;
    }

}
