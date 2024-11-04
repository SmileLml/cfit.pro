<?php
include '../../control.php';
class myApi extends api
{
    const PARAMS_MISSING = 1001; //缺少参数
    const PARAMS_ERROR   = 1002; //缺少参数
    const PARAMS_OPTIONSYSTEM_ERROR  = 1003; //受影响业务系统单选
    const PARAMS_UPDATE_ERROR        = 1004; //不允许更新

    public function problem()
    {

        $logID = $this->loadModel('requestlog')->insideSaveRequestLog('problem' , '问题同步'); //todo 测试不加日志

        // 金信同步处理
        $jx = 0;
        if(isset($_POST['idUnique']))
        {
            $this->checkApiToken();
            $_POST['IssueId'] = $_POST['idUnique'];
            unset($_POST['idUnique']);
            $jx = 1;
        }

        if($jx == 1){
            //受影响业务系统单选
            if(strripos($_POST['OptionSystem'],',')){
                $this->requestlog->response('fail', 'OptionSystem为单选项', array(), $logID, self::PARAMS_OPTIONSYSTEM_ERROR);
            }
        }
        $errMsg = $this->checkInput($jx);
        if (!empty($errMsg)) {
            $this->requestlog->response('fail', implode(',', $errMsg), [], $logID, self::PARAMS_MISSING);
        }
        $problem = $this->loadModel('problem')->getProblemIdByIssueId($this->post->IssueId); //查询是否存在
        if (!empty($problem->id)) { //已存在 更新
            // 迭代26 更新待分析之后都取消
            if($problem->status == 'confirmed'){
                if($jx == 1) {
                  $this->problem->updateByApi($problem->id,1); // 金信单更新
                  $account = 'guestjx';
                }else {
                  $this->problem->updateByApi($problem->id); // 清总单更新
                  $account = 'guestcn';
                }
               $this->loadModel('action')->create('problem', $problem->id, 'update', '问题单号:' . $problem->id,'',$account);
            }else{
             //不可更新
              $this->requestlog->response('fail', $this->lang->api->problemNoUpdate, [], $logID, self::PARAMS_UPDATE_ERROR);
            }
        } else {
            if($jx == 1) {
                $problemId = $this->problem->createByApi(1); // 金信单
                $account = 'guestjx';
            }else {
                $problemId = $this->problem->createByApi(); // 清总单
                $account = 'guestcn';
            }
            $this->loadModel('action')->create('problem', $problemId, 'created', '问题单号:' . $problemId,'',$account);
        }



        if(dao::isError()) {
            $this->requestlog->response('fail', dao::getError(), [], $logID, self::PARAMS_ERROR);
        }
        if($jx == 1) {
            $this->requestlog->response('success', $this->lang->api->successful, array('idUnique' => $this->post->IssueId), $logID);
        }else {
            $this->requestlog->response('success', $this->lang->api->successful, array('IssueId' => $this->post->IssueId), $logID);
        }
    }

    /**
     * 校验
     * @return array
     */
    private function checkInput($jx)
    {
        $errMsg = [];
        if($_POST['ProblemLevel'] == '一级' || $_POST['ProblemLevel'] == 1) {
            $_POST['ProblemLevel']  = 1;
        } elseif($_POST['ProblemLevel'] == '二级' || $_POST['ProblemLevel'] == 2) {
            $_POST['ProblemLevel']  = 2;
        }  elseif($_POST['ProblemLevel'] == '三级' || $_POST['ProblemLevel'] == 3) {
            $_POST['ProblemLevel']  = 3;
        }  elseif($_POST['ProblemLevel'] == '四级' || $_POST['ProblemLevel'] == 4) {
            $_POST['ProblemLevel']  = 4;
        } else{
            $errMsg[] =  "ProblemLevel不正确";
        }

        $this->loadModel('problem');

        if($_POST['CataOfIssue'] == 'undefined'){ $_POST['CataOfIssue'] = '';}
        foreach ($this->lang->problem->typeList as $k =>$v){
            if($_POST['CataOfIssue'] == $v || $_POST['CataOfIssue'] == $k) {
                $_POST['CataOfIssue'] = $k;
                unset($errMsg['CataOfIssue']);
                break;
            } else {
                $errMsg['CataOfIssue'] = "CataOfIssue：'{$_POST['CataOfIssue']}' 不是协议枚举项";
            }
        }
        foreach ($_POST as $key => $v)
        {
            if(!isset($this->lang->problem->apiItems[$key])){
                $errMsg[] = $key."不是协议字段";
            }
        }

        foreach ($this->lang->problem->apiItems as $k => $v)
        {
            if($v['required'] && $this->post->$k == ''){
                $errMsg[] = $k.$v['name'].$this->post->$k.'不可以空';
            }
            if($v['target'] != $k)
            {
                $_POST[$v['target']] = $this->post->$k;
                unset($_POST[$k]);
            }
        }
        if($jx && in_array('IssueId外部单号不可以空',$errMsg)) {
            $errMsg[array_search('IssueId外部单号不可以空',$errMsg)] = 'idUnique外部单号不可以空';
        }

        return $errMsg;
    }

    /**
     * 金信同步处理
     *
     */
    private function jxInput($logID) {

        /* 判断所需字段是否存在。*/
        $data = fixer::input('post')
            ->stripTags('User_demand_background', $this->config->allowedTags)
            ->get();
        foreach($this->config->api->problemParams as $param)
        {
            if(!isset($data->{$param}))
            {
                $errorMessage = sprintf($this->lang->api->fieldMissing, $param);
                $this->requestlog->response('fail', $errorMessage, array(), $logID);
            }
        }

        /* 对必填字段做处理。*/
        unset($_POST);
        $this->config->opinion->create->requiredFields = 'name,sourceMode,sourceName,union,date,contact,contactInfo,deadline,background';

        /* 设置参数到post中。*/
        foreach($this->config->api->problemFields as $paramName => $field)
        {
            /* 对时间戳做处理。*/
            if($paramName == 'occurTime' or $paramName == 'recoverTime' or $paramName == 'reportTime')
            {
                $timeStamp = substr($data->{$paramName}, 0, 10);
                $processingTime = date('Y-m-d', $timeStamp);
                $this->post->set($field, $processingTime);
                continue;
            }
            $this->post->set($field, $data->{$paramName});
        }

    }
}