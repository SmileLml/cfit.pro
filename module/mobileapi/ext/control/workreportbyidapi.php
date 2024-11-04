<?php
include '../../control.php';
class myMobileApi extends mobileapi
{
    const PARAMS_MISSING = 1001; //缺少参数
    /**
     * 查询报工详情
     */
    public function workReportByIDApi()
    {
        $errMsg = $this->checkInput();
        if (!empty($errMsg)) {
           // $this->requestlog->response('fail', implode(',', $errMsg), [], 0, self::PARAMS_MISSING);
            $this->loadModel('mobileapi')->response('fail', implode(',', $errMsg), array(),  0, 203,'workReportByIDApi');
        }
        $workID = $_POST['workID'];
        $oldWork = $this->loadModel('workreport')->getByID($workID);
        if(isset($oldWork) && $oldWork->account != $this->app->user->account){
            $errMsg = '不能操作其他用户数据';
            $this->loadModel('mobileapi')->response('fail', $errMsg, array(),  0, 203,'workReportByIDApi');
        }
        $data = $this->loadModel('workreport')->getWorkByIDApi($workID);
        $work = new stdClass();
        $work->workInfo   = $data;
        $this->loadModel('mobileapi')->response('success', '',  $work,  0, 200,'workReportByIDApi');
    }

    /**
     * 校验
     * @return array
     */
    private function checkInput()
    {
        $errMsg = [];
        if(!isset($_POST['workID'])){
            $errMsg[] = "缺少『workID』参数";
        }
        foreach ($_POST as $key => $v)
        {
            if(!isset($_POST['workID'])){
                $errMsg[] = $key."不是协议字段";
            }
        }

        if( isset($_POST['workID']) && !$_POST['workID']){
            $errMsg[] = '报工ID不能为空';
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
