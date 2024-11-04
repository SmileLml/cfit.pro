<?php
include '../../control.php';
class myInfo extends info
{
    public function cancelLinkage($infoId, $type)
    {
        $info = $this->info->getByID($infoId);

        if($_POST)
        {
            $cancelLinkage = $_POST['cancelLinkage'] ?? 0;

            if($cancelLinkage != $info->$type){
                $this->dao->update(TABLE_INFO)->set($type)->eq($cancelLinkage)->where('id')->eq($infoId)->exec();

                $newInfo = clone $info;
                $newInfo->$type = $cancelLinkage;

                $actionID = $this->loadModel('action')->create('info', $infoId, $type, '');
                $this->action->logHistory($actionID, common::createChanges($info, $newInfo));
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }

        $this->view->type   = $type;
        $this->view->info = $info;
        $this->display();
    }
}