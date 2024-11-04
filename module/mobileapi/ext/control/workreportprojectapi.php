<?php
include '../../control.php';
class myMobileApi extends mobileapi
{

    /**
     * 获取空间名称
     */
    public function workReportProjectApi()
    {
        $recTotal = isset($_POST['recTotal']) ? $_POST['recTotal'] : 0;
        $recPerPage = isset($_POST['recPerPage']) ? $_POST['recPerPage'] : 2000;
        $pageID = isset($_POST['pageID']) ? $_POST['pageID'] : 1;

        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);
        $workID = isset($_POST['workID']) ? $_POST['workID'] : '';
        if($workID){
            $oldWork = $this->loadModel('workreport')->getByID($workID);
            if(isset($oldWork) && $oldWork->account != $this->app->user->account){
                $errMsg = '不能操作其他用户数据';
                $this->loadModel('mobileapi')->response('fail', $errMsg, array(),  0, 203,'workReportProjectApi');
            }
        }
        $data = $this->loadModel('workreport')->getProjectApi($pager,$workID);
        $work = new stdClass();

        $work->projects   = $data;
        $work->recTotal   = $pager->recTotal;
        $work->recPerPage = $pager->recPerPage;
        $work->pageID     = $pager->pageID;


        $this->loadModel('mobileapi')->response('success', '', $work ,  0, 200,'workReportProjectApi');
    }
    /**
     * 校验
     * @return array
     */
    private function checkInput()
    {
        $errMsg = [];

        if(isset($_POST['workID']) && $_POST['workID']){
           if(!preg_match("/^[1-9][0-9]*$/",$_POST['workID'])){
               $errMsg = '报工ID只能正整数';
               return $errMsg;
           }
       }
        return $errMsg;
    }
}
