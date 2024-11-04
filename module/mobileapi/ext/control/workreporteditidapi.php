<?php
include '../../control.php';
class myMobileApi extends mobileapi
{
    const PARAMS_MISSING = 1001; //缺少参数
    /**
     * 查询编辑报工详情
     */
    public function workReportEditIDApi()
    {

        $errMsg = $this->checkInput();
        if (!empty($errMsg)) {
           // $this->requestlog->response('fail', implode(',', $errMsg), [], 0, self::PARAMS_MISSING);
            $this->loadModel('mobileapi')->response('fail', implode(',', $errMsg), array(),  0, 203,'workReportEditIDApi');
        }
        $workID = $_POST['workID'];
        $data = $this->loadModel('workreport')->getWorkByEditIDApi($workID);
        $work = new stdClass();
       //报工时间范围
        $beginAndEnd =  $this->loadModel('workreport')->getCreateBeginAndEnd();

        $work->allowDate  = array('begin'=> $beginAndEnd->begin,'end' => helper::today());
        $work->workInfo   = $data;
        $this->loadModel('mobileapi')->response('success', '',  $work,  0, 200,'workReportEditIDApi');
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
