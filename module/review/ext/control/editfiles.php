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
    public function editfiles($reviewID){

        $this->view->title      = $this->lang->review->editNodeUsers;
        $this->view->position[] = $this->lang->review->editNodeUsers;
        $review = $this->review->getByID($reviewID);
        if($_POST){
            $changes =  $this->review->editFilesByID($reviewID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            if($changes or $this->post->currentComment)
            {

                $actionID = $this->loadModel('action')->create('review', $reviewID, 'renewfile', $this->post->currentComment);
                $this->action->logHistory($actionID, $changes);

            }
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }
        $this->view->review     = $review;
        $this->display();
    }
}