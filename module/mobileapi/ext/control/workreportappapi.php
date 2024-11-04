<?php
include '../../control.php';
class myMobileApi extends mobileapi
{

    /**
     * 获取所属活动
     */
    public function workReportAppApi()
    {
        $errMsg = $this->checkInput();
        if (!empty($errMsg)) {
            // $this->requestlog->response('fail', implode(',', $errMsg), [], 0, self::PARAMS_MISSING);
            $this->loadModel('mobileapi')->response('fail', implode(',', $errMsg), array(),  0, 203,'workReportAppApi');
        }
        $activityID = $_POST['activityID'];
        $recTotal = isset($_POST['recTotal']) ? $_POST['recTotal'] : 0;
        $recPerPage = isset($_POST['recPerPage']) ? $_POST['recPerPage'] : 2000;
        $pageID = isset($_POST['pageID']) ? $_POST['pageID'] : 1;

        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);
        $data = $this->loadModel('workreport')->getAppApi($activityID,$pager);
        $work = new stdClass();

        $work->apps  = $data;
        $work->recTotal   = $pager->recTotal;
        $work->recPerPage = $pager->recPerPage;
        $work->pageID     = $pager->pageID;

        $this->loadModel('mobileapi')->response('success', '', $work ,  0, 200,'workReportAppApi');
    }
    /**
     * 校验
     * @return array
     */
    private function checkInput()
    {
        $errMsg = [];
        if(!isset($_POST['activityID'])){
            $errMsg[] = "缺少『activityID』参数";
        }
        foreach ($_POST as $key => $v)
        {
            if(!isset($_POST['activityID'])){
                $errMsg[] = $key."不是协议字段";
            }
        }

        if( isset($_POST['activityID']) && !$_POST['activityID']){
            $errMsg[] = '『所属活动ID』不能为空';
        }
        if(isset($_POST['activityID']) && $_POST['activityID']){
           if(!preg_match("/^[1-9][0-9]*$/",$_POST['activityID'])){
               $errMsg = '所属活动ID只能正整数';
               return $errMsg;
           }
       }
        return $errMsg;
    }
}
