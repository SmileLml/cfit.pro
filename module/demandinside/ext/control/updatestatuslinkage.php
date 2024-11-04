<?php
include '../../control.php';
class myDemandinside extends demandinside
{

    /**
     * 解除状态联动
     */
    public function updateStatusLinkage($demandID)
    {
        if($_POST)
        {
            $changes = $this->demandinside->updateLinkage($demandID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes || $this->post->comment)
            {
                $actionID = $this->loadModel('action')->create('demand', $demandID, 'secureed', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $this->view->title   = $this->lang->demandinside->edit;
        $this->view->demand = $this->demandinside->getByID($demandID);
        $this->display();
    }
}
