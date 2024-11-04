<?php
include '../../control.php';
class myDemand extends demand
{

    /**
     * 解除状态联动
     */
    public function unlockseparate($demandID)
    {

        if($_POST)
        {
            $changes = $this->demand->updateLock($demandID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes || $this->post->comment)
            {
                $actionID = $this->loadModel('action')->create('demand', $demandID, 'securedLock', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $this->view->title   = $this->lang->demand->edit;
        $this->view->demand = $this->demand->getByID($demandID);
        $this->display();
    }
}
