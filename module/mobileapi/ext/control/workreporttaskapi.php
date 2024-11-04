<?php
include '../../control.php';
class myMobileApi extends mobileapi
{

    /**
     * 获取所属活动
     */
    public function workReportTaskApi()
    {
        $errMsg = $this->checkInput();
        if (!empty($errMsg)) {
            // $this->requestlog->response('fail', implode(',', $errMsg), [], 0, self::PARAMS_MISSING);
            $this->loadModel('mobileapi')->response('fail',  $errMsg, array(),  0, 203,'workReportTaskApi');
        }
        $appID = $_POST['appID'];
        $projectID = $_POST['projectID'];
        $recTotal = isset($_POST['recTotal']) ? $_POST['recTotal'] : 0;
        $recPerPage = isset($_POST['recPerPage']) ? $_POST['recPerPage'] : 100;
        $pageID = isset($_POST['pageID']) ? $_POST['pageID'] : 1;

        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);
        $data = $this->loadModel('workreport')->getTaskApi($appID,$projectID,$pager);
        $work = new stdClass();

        $work->tasks  = $data;
        $work->recTotal   = $pager->recTotal;
        $work->recPerPage = $pager->recPerPage;
        $work->pageID     = $pager->pageID;

        $this->loadModel('mobileapi')->response('success', '', $work ,  0, 200,'workReportTaskApi');
    }
    /**
     * 校验
     * @return array
     */
    private function checkInput()
    {
        $errMsg = '';
        if(!isset($_POST['projectID'])){
            $errMsg = "缺少『projectID』参数";
            return $errMsg;
        }
        if(!isset($_POST['appID'])){
            $errMsg = "缺少『appID』参数";
            return $errMsg;
        }
        foreach ($_POST as $key => $v)
        {
            if(!isset($_POST['appID']) && !isset($_POST['projectID'])){
                $errMsg = $key."不是协议字段";
                return $errMsg;
            }
        }
        if( isset($_POST['projectID']) && !$_POST['projectID']){
            $errMsg = '『所属项目ID』不能为空';
            return $errMsg;
        }
        if( isset($_POST['appID']) && !$_POST['appID']){
            $errMsg = '『所属阶段ID』不能为空';
            return $errMsg;
        }
        if(isset($_POST['projectID']) && $_POST['projectID']){
           if(!preg_match("/^[1-9][0-9]*$/",$_POST['projectID'])){
               $errMsg = '所属项目ID只能正整数';
               return $errMsg;
           }
       }
      if(isset($_POST['appID']) && $_POST['appID']){
           if(!preg_match("/^[1-9][0-9]*$/",$_POST['appID'])){
               $errMsg = '所属阶段ID只能正整数';
               return $errMsg;
           }
       }
        return $errMsg;
    }
}
