<?php
include '../../control.php';
class myApi extends api
{
    const PARAMS_MISSING = 1001; //缺少参数
    const FAIL_CODE   = 999;    //请求失败
    const SUCCESS     = 200;    //成功
    //变更单修改时间（gitee、精卫）
    public function changeordertime(){
        $postData = fixer::input('post')->get();
        $codePrefix = substr($postData->changeOrderId,0,7);
        $type = '';
        if ($codePrefix == 'CFIT-CQ'){
            $user = 'guestcn';
            $type = 'modifycncc';
            //清总生产变更单
            $table = TABLE_MODIFYCNCC;
        }elseif ($codePrefix == 'CFIT-CJ'){
            $user = 'guestjx';
            $type = 'modify';
            $table = TABLE_MODIFY;
        }
        $logID = $this->loadModel('requestlog')->insideSaveRequestLog($type , 'changeordertime');
        if ($type == ''){
            $this->requestlog->response('fail', '变更单不存在', [] ,$logID, self::FAIL_CODE);

        }

        //截取前7位判断变更单类型
//        foreach($this->config->api->changeordertimeParams as $param)
//        {
//            if(!isset($postData->{$param}))
//            {
//                $errorMessage = sprintf($this->lang->api->fieldMissing, $param);
//                $this->requestlog->response('fail', $errorMessage, [] ,0, self::PARAMS_MISSING);
//            }
//        }
        if ($postData->changeOrderId == ''){
            $this->requestlog->response('fail', 'changeOrderId参数不能为空', [] ,$logID, self::FAIL_CODE);
        }
        $fileds = $this->config->api->changeordertimeFields;
        $postData = array_filter((array)$postData);
        $changeOrderId = $postData['changeOrderId'];
        unset($postData['changeOrderId']);
        $data = new stdClass();
        foreach ($fileds as $k=>$v){
            if (isset($postData[$v])){
                $data->{$k} = date("Y-m-d H:i:s",substr($postData[$v],0,10));
            }
        }
        if (isset($data->backspaceExpectedStartTime) && isset($data->backspaceExpectedEndTime) && $data->backspaceExpectedStartTime != '' && $data->backspaceExpectedEndTime != ''){
            if ($data->backspaceExpectedStartTime >= $data->backspaceExpectedEndTime){
                $this->requestlog->response('fail', '开始时间不能大于结束时间', [] ,$logID, self::FAIL_CODE);
            }
        }
        if (isset($data->planBegin) && isset($data->planEnd) && $data->planBegin != '' && $data->planEnd != ''){
            if ($data->planBegin >= $data->planEnd){
                $this->requestlog->response('fail', '开始时间不能大于结束时间', [] ,$logID, self::FAIL_CODE);
            }
        }
        $info = $this->dao->select("id,`code`,backspaceExpectedStartTime,backspaceExpectedEndTime,planBegin,planEnd")->from($table)->where('code')->eq($changeOrderId)->fetch();
        if (!$info){
            $this->requestlog->response('fail', '变更单不存在', [] ,$logID, self::FAIL_CODE);
        }
//        echo $user;exit;
        $this->dao->update($table)->data($data)->where('id')->eq($info->id)->exec();
        if(!dao::isError()) {
            $changes = common::createChanges($info, $data);
            $actionID = $this->loadModel('action')->create($type, $info->id, 'edited', '','',$user);
            $this->action->logHistory($actionID, $changes);
        }
        $this->requestlog->response('success', $this->lang->api->successful, array('id' => $info->id), $logID);
        exit;
    }
    private function checkInput(){
        $errMsg = [];
        //校验是否存在异常字段
        foreach ($_POST as $key => $v)
        {
            if(!isset($this->config->api->changeordertimeParams[$key])){
                $errMsg[] = $key."不是协议字段";
            }
        }
        return $errMsg;
    }
}