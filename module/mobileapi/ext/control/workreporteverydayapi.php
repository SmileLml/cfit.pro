<?php
include '../../control.php';
class myMobileApi extends mobileapi
{

    /**
     * 查询每一天的报工数据
     */
    public function workReportEveryDayApi()
    {
        $recTotal = isset($_POST['recTotal']) ? $_POST['recTotal'] : 0;
        $recPerPage = isset($_POST['recPerPage']) ? $_POST['recPerPage'] : 2000;
        $pageID = isset($_POST['pageID']) ? $_POST['pageID'] : 1;

        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);
        $date = isset($_POST['date']) && $_POST['date'] ? $_POST['date'] : helper::today();
        $errMsg = $this->checkInput();
        if (!empty($errMsg)) {
            $this->loadModel('mobileapi')->response('fail', $errMsg, array(),  0, 200,'workReportEveryDayApi');
        }
        $data = $this->loadModel('workreport')->getEveryDayApi($this->app->user->account,$pager,$date);

       //报工时间
        $beginAndEnd =  $this->loadModel('workreport')->getCreateBeginAndEnd();
        $flag = false;
        if(strtotime($date) >= strtotime($beginAndEnd->begin) && strtotime($date) <= strtotime(helper::today())){
            $flag = true;
        }
        $work = new stdClass();
        $work->canAdd = $flag;
        $work->day  = $date;
        $work->everyDayWork   = $data;
        $work->totalWork   = $data ? round(array_sum(array_column($data,'consumed')),1) : 0;
        $work->recTotal   = $pager->recTotal;
        $work->recPerPage = $pager->recPerPage;
        $work->pageID     = $pager->pageID;

        $this->loadModel('mobileapi')->response('success', '', $data = $work,  0, 200,'workReportEveryDayApi');
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
