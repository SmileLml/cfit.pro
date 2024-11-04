<?php
include '../../control.php';
class myApi extends api
{
    const PARAMS_MISSING = 1001; //缺少参数
    const FAIL_CODE   = 999;    //请求失败
    const SUCCESS     = 200;    //成功

    public function change()
    {

        //需求池接收生产变更单
        $logID = $this->loadModel('requestlog')->insideSaveRequestLog('requirement' , 'requirementChange');
//        $this->checkApiToken();//校验token
        $errMsg = $this->checkInput();
        if(!empty($errMsg)) {
            $this->requestlog->response('fail', implode(',',$errMsg), [], $logID, self::FAIL_CODE);
        }
        $this->loadModel('requestlog');
        $postData = fixer::input('post')->get();

        //校验是否存在异常字段
        foreach($this->config->api->changeParams as $param)
        {
            if(!isset($postData->{$param}))
            {
                $errorMessage = sprintf($this->lang->api->fieldMissing, $param);
                $this->requestlog->response('fail', $errorMessage, [] ,$logID, self::PARAMS_MISSING);
            }
        }
        if ($postData->Change_number == ''){
            $errMsg[] = 'Change_number'."不能为空";
        }
        if ($postData->Demand_number == ''){
            $errMsg[] = 'Demand_number'."不能为空";
        }
        if (!in_array($postData->Missed_demolition,[0,1])){
            $this->requestlog->response('fail','Missed_demolition参数值有误请检查' , [], $logID,self::HAD_CHANGE);
        }
        if(!empty($errMsg)) {
            $this->requestlog->response('fail', implode(',',$errMsg), [], $logID, self::FAIL_CODE);
        }
        if(dao::isError()) {
            $this->requestlog->response('fail', dao::getError(), [], $logID, self::FAIL_CODE);
        }
        //处理入库数据,转换字段名称
        foreach($this->config->api->changeFields as $paramName => $field)
        {
            $this->post->set($field, $postData->{$paramName});
            unset($_POST[$paramName]);
        }
        $data = $_POST;
//        $data['changeEntry'] = implode(',',$data['changeEntry']);
        $data['changeEntry'] = '';//尚未同步
        $data['createdDate'] = date('Y-m-d H:i:s',time());
        $data['createdBy'] = 'qz';
        $data['deleted'] = 0;
        $opinionInfo = $this->loadModel('opinion')->getByCode($data['demandNumber']);
        $requireList = $this->dao->select("id")->from(TABLE_REQUIREMENT)->where('entriesCode')->in($data['changeEntry'])->fetchAll();
        $requireIds = array_column($requireList,'id');
        /** @var requirementChangeModel $requirementChange */
        $requirementChange = $this->loadModel('requirementchange');
        //查询该需求变更单是否已存在，存在则更新
        $changInfo = $requirementChange->getByChangeNumber($data['changeNumber']);
        if($changInfo){
            $type = 'edited';
            $data['editDate'] = date('Y-m-d H:i:s',time());
            $requirementChangeId = $changInfo->id;
            $this->dao->update(TABLE_REQUIREMENTCHANGE)->data($data)->where('changeNumber')->eq($data['changeNumber'])->exec();
//            $this->requirementChange->changeorder($data,'add');
        }else{
            $type = 'created';
            $this->dao->insert(TABLE_REQUIREMENTCHANGE)->data($data)->exec();
            $requirementChangeId = $this->dao->lastInsertID();
        }
        $this->dao->update(TABLE_REQUESTLOG)->set('objectId')->eq($requirementChangeId)->where('id')->eq($logID)->exec();
        //需求意向历史记录
        $this->loadModel('action')->create('opinion', $opinionInfo->id, 'initchanges', '总中心同步变更单','','guestcn');
        foreach ($requireIds as $v) {
            $this->loadModel('action')->create('requirement', $v, 'initchanges', '总中心同步需求任务变更','','guestcn');
        }
//        $this->loadModel("requirementchange")->sendmail($requirementChangeId,$data);
        $actionId = $this->loadModel('action')->create('requirementchange', $requirementChangeId, 'changeorder','','','guestcn');
        $this->requestlog->response('success', $this->lang->api->successful,array('Change_Number' => $data['changeNumber']) , $logID,self::SUCCESS);
    }
    private function checkInput(){
        $errMsg = [];
        //校验是否存在异常字段
        foreach ($_POST as $key => $v)
        {
            if(!isset($this->config->api->changeFields[$key])){
                $errMsg[] = $key."不是协议字段";
            }
            if (!in_array($key,['Change_entry','Circumstance']) && $v === ''){
                $errMsg[] = $key."不能为空";
            }
        }
        return $errMsg;
    }
}