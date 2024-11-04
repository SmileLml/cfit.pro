<?php
include '../../control.php';
class myMobileApi extends mobileapi
{

    /**
     * 金信-生产变更已办列表
     */
    public function sectransferCompletedApi()
    {
        $recTotal = isset($_POST['recTotal']) ? $_POST['recTotal'] : 0;
        $recPerPage = isset($_POST['recPerPage']) ? $_POST['recPerPage'] : 15;
        $pageID = isset($_POST['pageID']) ? $_POST['pageID'] : 1;

        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $search = isset($_POST['search']) ? $_POST['search'] : '';
        $modifys = $this->loadModel('sectransfer')->getCompletedListApi($pager,$search);

        $users = $this->loadModel('user')->getPairs('noclosed|noletter');
        $list = [];
        foreach ($modifys as $k => $v){
            $obj = new stdClass();
            $obj->id      = $v->id;
//            $obj->code    = $v->code;
            $obj->desc    = $v->protransferDesc;
            $obj->createdDate    = $v->createdDate;
            $obj->user    = $users[$v->createdBy];
            $list[]       = $obj;
        }
        $data = new stdClass();
        $data->list       = $list;
        $data->recTotal   = $pager->recTotal;
        $data->recPerPage = $pager->recPerPage;
        $data->pageID     = $pager->pageID;
        $data->pageTotal  = $pager->pageTotal;
        $this->loadModel('mobileapi')->response('success', '', $data ,  0, 200,'modifyCompletedApi');
    }
}
