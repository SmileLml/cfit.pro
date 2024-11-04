<?php
include '../../control.php';
class myOutwarddelivery extends outwarddelivery
{
    public function cancelLinkage($outwarddeliveryId, $type)
    {
        $allInfo          = $this->outwarddelivery->getAllInfo($outwarddeliveryId);
        $outwarddelivery  = (Object)$allInfo['outwardDelivery'];

        if($_POST)
        {
            $cancelLinkage = $_POST['cancelLinkage'] ?? 0;

            if($cancelLinkage != $outwarddelivery->$type){
                $this->dao->update(TABLE_OUTWARDDELIVERY)->set($type)->eq($cancelLinkage)->where('id')->eq($outwarddeliveryId)->exec();

                $newOutwarddelivery = clone $outwarddelivery;
                $newOutwarddelivery->$type = $cancelLinkage;

                $actionID = $this->loadModel('action')->create('outwarddelivery', $outwarddeliveryId, $type, '');
                $this->action->logHistory($actionID, common::createChanges($outwarddelivery, $newOutwarddelivery));
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }

        $this->view->type            = $type;
        $this->view->outwarddelivery = $outwarddelivery;
        $this->display();
    }
}