<?php
include '../../control.php';
class myReview extends review
{
    /**
     * suspend 挂起.
     *
     * @param  int  $reviewID
     * @access public
     * @return void
     */
    public function suspend($reviewID,$source =0)
    {
        if($_POST)
        {
            $logChanges = $this->review->suspend($reviewID);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $actionID = $this->loadModel('action')->create('review', $reviewID, 'suspend', $this->post->comment);
            if($logChanges) {
                $this->action->logHistory($actionID, $logChanges);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }

        $review = $this->review->getByID($reviewID);
        $checkRes = $this->review->checkReviewIsAllowSuspend($review);
        $this->view->title      = $this->lang->review->suspend;
        $this->view->position[] = $this->lang->review->suspend;

        $this->view->review     = $review;
        $this->view->checkRes   = $checkRes;
        $this->view->source = $source;
        $this->display();
    }
}