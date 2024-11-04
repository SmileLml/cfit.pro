<?php
include '../../control.php';
class myMobileApi extends mobileapi
{

    /**
     * 清总-对外交付待办列表
     */
    public function outwardBrowseApi()
    {
        $search = isset($_POST['search']) ? $_POST['search'] : '';
        $reviewList = $this->loadModel('outwarddelivery')->getWaitListApi($search);
        $users = $this->loadModel('user')->getPairs('noclosed|noletter');
        $this->app->loadLang('modifycncc');
        $list = [];
        foreach ($reviewList as $k => $v){
            $obj = new stdClass();
            $obj->id      = $v->id;
            $obj->code    = $v->code;
            $obj->desc    = $v->outwardDeliveryDesc;

            $obj->createdDate    = $v->createdDate;
            $obj->user    = $users[$v->createdBy];
            $obj->type    = isset($v->type) ? $this->lang->modifycncc->typeList[$v->type] : '';
            $list[] = $obj;
        }
        $this->loadModel('mobileapi')->response('success', '', $list ,  0, 200,'outwardBrowseApi');
    }
}
