<?php
include '../../control.php';
class myflow extends flow
{
    public function view($module, $dataID, $linkType = '', $mode = 'browse')
    {
        $this->view->actionFormLink = $this->createLink('action', 'comment', "objectType={$module}&objectID=$dataID");
        parent::view($module, $dataID, $linkType, $mode);
    }
}
