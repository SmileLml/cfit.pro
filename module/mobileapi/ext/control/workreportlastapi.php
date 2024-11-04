<?php
include '../../control.php';
class myMobileApi extends mobileapi
{

    /**
     * 查询报工上一次
     */
    public function workReportLastApi()
    {
        $data = $this->loadModel('workreport')->getWorkLastApi($this->app->user->account);
        $work = new stdClass();
        //报工时间范围
        $beginAndEnd =  $this->loadModel('workreport')->getCreateBeginAndEnd();

        $work->allowDate  = array('begin'=> $beginAndEnd->begin,'end' => helper::today());
        $work->lastwork   = $data;
        $this->loadModel('mobileapi')->response('success','', $data = $work,  0, 200,'workReportLastApi');
    }
}
