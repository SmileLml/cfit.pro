<?php
include '../../control.php';
class myMobileApi extends mobileapi
{

    /**
     * 金信-生产变更已办列表
     */
    public function modifyCompletedApi()
    {
        $recTotal = isset($_POST['recTotal']) ? $_POST['recTotal'] : 0;
        $recPerPage = isset($_POST['recPerPage']) ? $_POST['recPerPage'] : 15;
        $pageID = isset($_POST['pageID']) ? $_POST['pageID'] : 1;

        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $search = isset($_POST['search']) ? $_POST['search'] : '';
        $modifys = $this->loadModel('modify')->getCompletedListApi($pager,$search);

        $users = $this->loadModel('user')->getPairs('noclosed|noletter');
        $this->app->loadLang('modifycncc');
        $list = [];
        foreach ($modifys as $k => $v){
            $obj = new stdClass();
            $obj->id      = $v->id;
            $obj->code    = $v->code;
            $obj->desc    = html_entity_decode(strip_tags(str_replace("<br />","\n",strval(htmlspecialchars_decode($v->desc,ENT_QUOTES)))));//富文本
            $obj->createdDate    = $v->createdDate;
            $obj->user    = $users[$v->createdBy];
            $obj->type    = $this->lang->modifycncc->typeList[$v->type];
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
