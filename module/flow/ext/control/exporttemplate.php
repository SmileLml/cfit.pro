<?php
include "../../control.php";
class myflow extends flow
{
    public function exportTemplate($module)
    {
        if(!commonModel::hasPriv($module, 'exporttemplate')) $this->loadModel('common')->deny($module, 'exporttemplate');

        $flow     = $this->loadModel('workflow')->getByModule($module);
        $fileName = $flow->name . $this->lang->flow->template;

        if($_POST) $this->post->set('fileName', $fileName);

        return parent::exportTemplate($module);
    }
}
