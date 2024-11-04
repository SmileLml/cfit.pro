<?php
include '../../control.php';
class myMobileApi extends mobileapi
{

    /**
     * 金信-生产变更待办列表
     */
    public function sectransferBrowseApi()
    {
        $search = isset($_POST['search']) ? $_POST['search'] : '';
        $reviewList = $this->loadModel('sectransfer')->getWaitListApi($search);
        $users = $this->loadModel('user')->getPairs('noclosed|noletter');
        $list = [];
        foreach ($reviewList as $k => $v){
            $obj = new stdClass();
            $obj->id      = $v->id;
//            $obj->code    = $v->code;
            $obj->desc    = $v->protransferDesc;//富文本7

            $obj->createdDate    = $v->createdDate;
            $obj->user    = $users[$v->createdBy];
            $list[] = $obj;
        }
        $this->loadModel('mobileapi')->response('success', '', $list ,  0, 200,'sectransferBrowseApi');
    }
}
