<?php
include '../../control.php';
class myMobileApi extends mobileapi
{

    /**
     * 获取所属活动
     */
    public function workReportActivityApi()
    {
        $errMsg = $this->checkInput();
        if (!empty($errMsg)) {
            // $this->requestlog->response('fail', implode(',', $errMsg), [], 0, self::PARAMS_MISSING);
            $this->loadModel('mobileapi')->response('fail', implode(',', $errMsg), array(),  0, 203,'workReportActivityApi');
        }
        $projectID = $_POST['projectID'];
        $recTotal = isset($_POST['recTotal']) ? $_POST['recTotal'] : 0;
        $recPerPage = isset($_POST['recPerPage']) ? $_POST['recPerPage'] : 2000;
        $pageID = isset($_POST['pageID']) ? $_POST['pageID'] : 1;

        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);
        $type = $_POST['type'];
        $data = $this->loadModel('workreport')->getActivityApi($projectID,$pager,$type);
        $work = new stdClass();

        $work->activitys  = $data;
        $work->recTotal   = $pager->recTotal;
        $work->recPerPage = $pager->recPerPage;
        $work->pageID     = $pager->pageID;
        $work->pageTotal     = $pager->pageTotal;
        $this->loadModel('mobileapi')->response('success', '', $work ,  0, 200,'workReportActivityApi');
    }
    /**
     * 校验
     * @return array
     */
    private function checkInput()
    {
        $errMsg = [];
        if(!isset($_POST['projectID'])){
            $errMsg[] = "缺少『projectID』参数";
        }
        foreach ($_POST as $key => $v)
        {
            if(!isset($_POST['projectID'])){
                $errMsg[] = $key."不是协议字段";
            }
        }

        if( isset($_POST['projectID']) && !$_POST['projectID']){
            $errMsg[] = '『项目ID』不能为空';
        }
        if(isset($_POST['projectID']) && $_POST['projectID']){
           if(!preg_match("/^[1-9][0-9]*$/",$_POST['projectID'])){
               $errMsg = '项目ID只能正整数';
               return $errMsg;
           }
       }
        return $errMsg;
    }
}
