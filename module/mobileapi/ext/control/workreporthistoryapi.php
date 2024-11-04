<?php
include '../../control.php';
class myMobileApi extends mobileapi
{

    /**
     * 查询每一天的报工数据
     */
    public function workReportHistoryApi()
    {
        $recTotal = isset($_POST['recTotal']) ? $_POST['recTotal'] : 0;
        $recPerPage = isset($_POST['recPerPage']) ? $_POST['recPerPage'] : 2000;
        $pageID = isset($_POST['pageID']) ? $_POST['pageID'] : 1;

        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);
        $date = isset($_POST['date']) && $_POST['date'] ? $_POST['date'] : '';
        $errMsg = $this->checkInput();
        if (!empty($errMsg)) {
            $this->loadModel('mobileapi')->response('fail', $errMsg, array(),  0, 203,'workReportHistoryApi');
        }

        $data = $this->loadModel('workreport')->getWorkHistory($pager,$date);
        $work = new stdClass();


        $work->day  = $date;
        $work->historyWork   = $data;
        $work->recTotal   = $pager->recTotal;
        $work->recPerPage = $pager->recPerPage;
        $work->pageID     = $pager->pageID;
        $work->pageTotal     = $pager->pageTotal;

        $this->loadModel('mobileapi')->response('success', '', $data = $work,  0, 200,'workReportHistoryApi');
    }
    /**
     * 校验
     * @return array
     */
    private function checkInput()
    {
        $errMsg = "";
        if($_POST['date']){
           $is_date = date('Y-m-d',strtotime($_POST['date'])) == $_POST['date'] ? $_POST['date'] :false;
           if( $is_date === false){
               $errMsg = '日期格式非法!';
           }
        }
        return $errMsg;
    }
}
