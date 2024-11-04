<?php
include '../../control.php';
class myProblem extends problem
{

    /**
     * 解除状态联动
     */
    public function updateStatusLinkage($problemID)
    {
        if($_POST)
        {
            $changes = $this->problem->updateLinkage($problemID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes || $this->post->comment)
            {
                $actionID = $this->loadModel('action')->create('problem', $problemID, 'secureed', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $this->view->title   = $this->lang->problem->edit;
        $this->view->problem = $this->problem->getByID($problemID);
        $this->display();
    }
}
