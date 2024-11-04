<?php

include '../../control.php';
class myReview extends review
{
    /**
     * Project: chengfangjinke
     * Method: workloadDelete
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:12
     * Desc: This is the code comment. This method is called workloadDelete.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $reviewID
     * @param int $consumedID
     */
    public function workloadDelete($reviewID = 0, $consumedID = 0)
    {
        if($_POST)
        {
            $changes = $this->review->workloadDelete($reviewID, $consumedID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes || $this->post->comment != '')
            {
                $actionID = $this->loadModel('action')->create('review', $reviewID, 'edited', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }

        $this->view->title  = $this->lang->review->workloadDelete;
        $this->view->review = $this->review->getByID($reviewID);
        $this->display();
    }
}