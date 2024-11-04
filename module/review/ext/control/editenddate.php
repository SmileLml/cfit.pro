<?php
include '../../control.php';
class myReview extends review
{
    /**
     * review a review 审核.
     *
     * @param  int  $reviewID
     * @param sting $nodeId
     * @access public
     * @return void
     */
    public function editEndDate($reviewID){
        if($_POST) {
            $logChanges = $this->review->updateEndDate($reviewID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $actionID = $this->loadModel('action')->create('review', $reviewID, 'editEndDate', $this->post->comment);
            if($logChanges) {
                $this->action->logHistory($actionID, $logChanges);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }
        $this->view->title      = $this->lang->review->editNodeUsers;
        $this->view->position[] = $this->lang->review->editNodeUsers;
        $reviewInfo = $this->review->getByID($reviewID);

        $this->view->review     = $reviewInfo;
        $this->display();
    }
}