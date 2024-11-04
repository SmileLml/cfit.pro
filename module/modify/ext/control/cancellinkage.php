<?php
include '../../control.php';
class myModify extends modify
{
    public function cancelLinkage($modifyId, $type)
    {
        $modify  = $this->modify->getByID($modifyId);

        if($_POST)
        {
            $cancelLinkage = $_POST['cancelLinkage'] ?? 0;

            if($cancelLinkage != $modify->$type){
                $this->dao->update(TABLE_MODIFY)->set($type)->eq($cancelLinkage)->where('id')->eq($modifyId)->exec();

                $newModify = clone $modify;
                $newModify->$type = $cancelLinkage;

                $actionID = $this->loadModel('action')->create('modify', $modifyId, $type, '');
                $this->action->logHistory($actionID, common::createChanges($modify, $newModify));
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }

        $this->view->type            = $type;
        $this->view->modify = $modify;
        $this->display();
    }
}