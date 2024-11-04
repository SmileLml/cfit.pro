<?php
include '../../control.php';
class myApi extends api
{
    const PARAMS_MISSING = 1001; //缺少参数
    const PARAMS_ERROR   = 1002; //缺少参数
    const FAIL_CODE   = 999;    //请求失败

    public function defectfeedback()
    {
        $logID = $this->loadModel('requestlog')->insideSaveRequestLog('defect' , 'defectfeedback'); //todo 测试不加日志
        $errMsg = $this->checkInput();
        if(!empty($errMsg)) {
            $this->requestlog->response('fail', implode(',',$errMsg), [], $logID, self::FAIL_CODE);
        }
        $defect = $this->dao->select('id,feedbackNum,dealSuggest,status,createdBy,dealedBy,testrequestCreatedBy,productenrollCreatedBy,source')->from(TABLE_DEFECT)->where('uatId')->eq($this->post->uatId)->fetch();

        //判断数据库是否存在记录
        if(!empty($defect->id)){
            $data = fixer::input('post')->get();
            if(($defect->dealSuggest == 'suggestClose' and $data->changeStatus == '已关闭') or ($defect->dealSuggest == 'fix' and $data->changeStatus == '确认通过'))
            {
                $data->status =  'solved';
                $data->dealUser =  '';
            }elseif($defect->dealSuggest == 'nextFix' and $data->changeStatus == '确认通过') {
                $data->status =  'nextfix';
                $data->dealUser =  $defect->dealedBy;
            }elseif($data->changeStatus == '审批打回') {
                $data->status =  'hitback';
                $data->dealUser =  $defect->dealedBy;
                $data->syncStatus =  0;

                if( $defect->source == '2') {
                    if($defect->dealSuggest == 'fix')  $data->status =  'toconfirm';
                    $data->dealUser = $defect->testrequestCreatedBy ?? $defect->productenrollCreatedBy;
                }
            }
            $data->feedbackNum = $defect->feedbackNum + 1;
            $data->approverDate = helper::now();
            $this->dao->update(TABLE_DEFECT)->data($data)->where('id')->eq($defect->id)->exec();
            if(!dao::isError()) {
                $this->loadModel('action')->create('defect', $defect->id, 'feedbacked','','', 'guestcn');
                $this->loadModel('consumed')->record('defect', $defect->id, 0, 'guestcn', $defect->status, $data->status, array());

            }
        }else{
            $errMsg[] = "该ID不存在";
            $this->requestlog->response('fail', implode(',',$errMsg), [], $logID, self::FAIL_CODE);
        }

        if(dao::isError()) {
            $this->requestlog->response('fail', dao::getError(), [], $logID, self::FAIL_CODE);
        }
        $this->dao->update(TABLE_REQUESTLOG)->set('objectId')->eq($defect->id)->where('id')->eq($logID)->exec();
        $this->requestlog->response('success', $this->lang->api->successful, array('id' => $this->post->uatId), $logID);
    }

    /**
     * 校验
     * @return void
     */
    private function checkInput(){
        $errMsg = [];
        $this->loadModel('defect');
        //校验是否存在异常字段
        foreach ($_POST as $key => $v)
        {
            if(!isset($this->lang->defect->apiFeedbackItems[$key])){
                $errMsg[] = $key."不是协议字段";
            }
        }

        if(!empty($errMsg)){
            return $errMsg;
        }

        foreach ($this->lang->defect->apiFeedbackItems as $k => $v)
        {
            if($v['required'] && $this->post->$k == ''){
                $errMsg[] = $k.$v['name'].$this->post->$k.'不可以为空';
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