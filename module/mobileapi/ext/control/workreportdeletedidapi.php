<?php
include '../../control.php';
class myMobileApi extends mobileapi
{
    const PARAMS_MISSING = 1001; //缺少参数
    /**
     * 查询报工详情
     */
    public function workReportDeletedIDApi()
    {
        $errMsg = $this->checkInput();
        if (!empty($errMsg)) {
           // $this->requestlog->response('fail', implode(',', $errMsg), [], 0, self::PARAMS_MISSING);
            $this->loadModel('mobileapi')->response('fail',  $errMsg, array(),  0, 203,'workReportDeletedIDApi');
        }
        $workID = $_POST['workID'];
        $oldWork = $this->loadModel('workreport')->getByID($workID);
        if(isset($oldWork) && $oldWork->account != $this->app->user->account){
            $errMsg = '不能操作其他用户数据';
            $this->loadModel('mobileapi')->response('fail', $errMsg, array(),  0, 203,'workReportDeletedIDApi');
        }
        $data = $this->loadModel('workreport')->deleteApi($workID);
        $work = new stdClass();
        $work->workInfo   = $data;
        $this->loadModel('mobileapi')->response('success', '',  $work,  0, 200,'workReportDeletedIDApi');
    }

    /**
     * 校验
     * @return array
     */
    private function checkInput()
    {
        $errMsg = '';
        if(!isset($_POST['workID'])){
            $errMsg = "缺少『workID』参数";
            return $errMsg;
        }
       /* if(!isset($_POST['comment'])){
            $errMsg = "缺少『comment』参数";
            return $errMsg;
        }*/
        foreach ($_POST as $key => $v)
        {
            if(!isset($_POST['workID']) && !isset($_POST['comment'])){
                $errMsg = $key."不是协议字段";
                return $errMsg;
            }
        }

        if( isset($_POST['workID']) && !$_POST['workID']){
            $errMsg = '报工ID不能为空';
            return $errMsg;
        }
       /* if( isset($_POST['comment']) && !$_POST['comment']){
            $errMsg = '备注不能为空';
            return $errMsg;
        }*/
       if(isset($_POST['workID']) && $_POST['workID']){
           if(!preg_match("/^[1-9][0-9]*$/",$_POST['workID'])){
               $errMsg = '报工ID只能正整数';
               return $errMsg;
           }
       }
        return $errMsg;
    }
}
