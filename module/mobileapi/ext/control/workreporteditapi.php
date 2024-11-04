<?php
include '../../control.php';
class myMobileApi extends mobileapi
{

    /**
     * 编辑报工
     */
    public function workReportEditApi()
    {
        $errMsg = $this->checkInput();
        if (!empty($errMsg)) {
            // $this->requestlog->response('fail', implode(',', $errMsg), [], 0, self::PARAMS_MISSING);
            $this->loadModel('mobileapi')->response('fail', $errMsg, array(),  0, 203,'workReportEditApi');
        }
        $workID = $_POST['workID'];
        $oldWork = $this->loadModel('workreport')->getByID($workID);
        if(isset($oldWork) && $oldWork->account != $this->app->user->account){
            $errMsg = '不能操作其他用户数据';
            $this->loadModel('mobileapi')->response('fail', $errMsg, array(),  0, 203,'workReportEditApi');
        }
        $data = $this->loadModel('workreport')->workEditApi($workID);
        if(dao::isError()){
            $error = dao::getError();
            if(is_array($error)){
                foreach ($error as $key => $item) {
                    $error = trim(implode(',',$item),',');
                }
            }
            $this->loadModel('mobileapi')->response('fail',$error, array(),  0, 203,'workReportEditApi');
        }
        $actionID = $this->loadModel('action')->create('workreport', $workID, 'mobileedited', $this->post->comment);
        $this->action->logHistory($actionID, $data);
        $work = new stdClass();
        $work->workID   = $workID;
        $this->loadModel('mobileapi')->response('success', $this->lang->api->successful, $work ,  0, 200,'workReportEditApi');
    }

    /**
     * 校验
     * @return array
     */
    private function checkInput()
    {
        $errMsg = '';
        $this->app->loadLang('workreport');
        if(!isset($_POST['workID'])){
            $errMsg = "缺少『workID』参数";
            return $errMsg;
        }
        if( isset($_POST['workID']) && !$_POST['workID']){
            $errMsg = '报工ID不能为空';
            return $errMsg;
        }
        foreach ($_POST as $key => $v)
        {
            if($key == 'workID') continue;
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
                $errMsg = "『".$k."』".$v['name'].$_POST[$k].'不可以空';
                break;
            }

        }
        if(isset($_POST['workID']) && $_POST['workID']){
           if(!preg_match("/^[1-9][0-9]*$/",$_POST['workID'])){
               $errMsg = '报工ID只能正整数';
               return $errMsg;
           }
       }
        return $errMsg;
    }
}
