<?php

include '../../control.php';
class myReview extends review
{
    /**
     * Recall a review.
     *
     * @param  int 	   $reviewID
     * @access public
     * @return void
     */
    public function close($reviewID,$source =0)
    {
        if($_POST)
        {
            $logChanges = $this->review->close($reviewID); //关闭后，需要根据标志位删除临时白名单人员
            if(!dao::isError())
            {
                $actionID = $this->loadModel('action')->create('review', $reviewID, 'closed', $this->post->comment);
                if($logChanges) {
                    $this->action->logHistory($actionID, $logChanges);
                }
                $response['result']  = 'success';
                $response['message'] = $this->lang->saveSuccess;
                $response['locate']  = 'parent';

                $this->send($response);
            }
            $response['result']  = 'fail';
            $response['message'] = dao::getError();
            $this->send($response);
        }

        $users   = $this->loadModel('user')->getPairs();
        $review  = $this->review->getByID($reviewID);
        //关闭时收件人信息
        $mailUsersInfo = $this->review->getCloseMailUsersInfo($review);
        $this->view->mailUsersInfo = $mailUsersInfo;

        $this->view->actions = $this->loadModel('action')->getList('review', $reviewID);
        $this->view->review  = $review;
        $this->view->users   = $users;
        $this->view->closestatus = $this->lang->review->closeList;
        $this->view->source = $source;
        $this->display();
    }
}