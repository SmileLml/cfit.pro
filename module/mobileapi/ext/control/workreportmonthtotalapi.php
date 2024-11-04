<?php
include '../../control.php';
class myMobileApi extends mobileapi
{

    /**
     * 查询月报工数据
     */
    public function workReportMonthTotalApi()
    {
        $date = isset($_POST['date']) && $_POST['date'] ? $_POST['date'] : date('Y-m-d',strtotime(helper::today()));
        $errMsg = $this->checkInput();
        if (!empty($errMsg)) {
            $this->loadModel('mobileapi')->response('fail', $errMsg, array(),  0, 200,'workReportMonthTotalApi');
        }
        $data = $this->loadModel('workreport')->getMonthTotalApi($date);

        $this->loadModel('mobileapi')->response('success','', $data,  0, 200,'workReportMonthTotalApi');
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
