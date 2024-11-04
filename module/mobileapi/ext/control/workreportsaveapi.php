<?php
include '../../control.php';
class myMobileApi extends mobileapi
{

    /**
     * 保存报工
     */
    public function workReportSaveApi()
    {
        $errMsg = $this->checkInput();
        if (!empty($errMsg)) {
            // $this->requestlog->response('fail', implode(',', $errMsg), [], 0, self::PARAMS_MISSING);
            $this->loadModel('mobileapi')->response('fail', $errMsg, array(),  0, 203,'workReportSaveApi');
        }
        $data = $this->loadModel('workreport')->workSaveApi();
        if(dao::isError()){
            $error = dao::getError();
            if(is_array($error)){
                foreach ($error as $key => $item) {
                    $error = trim(implode(',',$item),',');
                }
            }
            $this->loadModel('mobileapi')->response('fail', $error, array(),  0, 203,'workReportSaveApi');
        }
        $this->loadModel('action')->create('workreport', $data, 'mobileCreated', $this->post->comment);
        $work = new stdClass();

        $work->workID   = $data;
        $this->loadModel('mobileapi')->response('success', $this->lang->api->successful, $data ,  0, 200,'workReportSaveApi');
    }

    /**
     * 校验
     * @return array
     */
    private function checkInput()
    {
        $errMsg = '';
        $this->app->loadLang('workreport');

        foreach ($_POST as $key => $v)
        {
            if(!isset($this->lang->workreport->apiItems[$key])){
                $errMsg = "『".$key."』"."不是协议字段";
                return $errMsg;
            }
        }
        foreach ($this->lang->workreport->apiItems as $k => $v)
        {
            if(!isset($_POST[$k]) ){
                $errMsg = "缺少『".$k."』".$v['name'].'参数';
                break;
            }
            if($v['required'] &&  isset($_POST[$k]) && $_POST[$k] == ''){
                $errMsg = $v['name'].$_POST[$k].'不可以空';
                break;
            }

        }
        return $errMsg;
    }
}
