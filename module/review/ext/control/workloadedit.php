<?php

include '../../control.php';
class myReview extends review
{

    /**
     * @param int $reviewID
     * @param int $consumedID
     */
    public function workloadEdit($reviewID = 0, $consumedID = 0)
    {
        if($_POST)
        {
            $changes = $this->review->workloadEdit($reviewID, $consumedID);

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

        $this->view->title    = $this->lang->review->workloadEdit;
        $this->view->reviewID   = $this->review->getByID($reviewID);
        $this->view->users    = $this->loadModel('user')->getPairs('noclosed');
        $consumed = $this->review->getConsumedByID($consumedID);
        //相关配合人员详情信息
        $consumed->details = $this->loadModel('consumed')->getConsumedDetailsArray($consumed->details);
        $this->view->consumed = $consumed;

        //检查是否是最后一条工作量信息
        $isLastConsumed =  $this->loadModel('consumed')->checkIsLastConsumed($consumedID, $reviewID, 'review');
        $this->view->isLastConsumed = $isLastConsumed;
        $this->display();

    }


}