<?php
include '../../control.php';
class myMobileApi extends mobileapi
{

    /**
     * 金信-生产变更待办列表
     */
    public function modifyBrowseApi()
    {
        $search = isset($_POST['search']) ? $_POST['search'] : '';
        $reviewList = $this->loadModel('modify')->getModifyWaitListApi($search);
        $users = $this->loadModel('user')->getPairs('noclosed|noletter');
        $this->app->loadLang('modifycncc');
        $list = [];
        foreach ($reviewList as $k => $v){
            $obj = new stdClass();
            $obj->id      = $v->id;
            $obj->code    = $v->code;
            $obj->desc    = html_entity_decode(strip_tags(str_replace("<br />","\n",strval(htmlspecialchars_decode($v->desc,ENT_QUOTES)))));//富文本7

            $obj->createdDate    = $v->createdDate;
            $obj->user    = $users[$v->createdBy];
            $obj->type    = $this->lang->modifycncc->typeList[$v->type];
            $list[] = $obj;
        }
        $this->loadModel('mobileapi')->response('success', '', $list ,  0, 200,'modifyBrowseApi');
    }
}
