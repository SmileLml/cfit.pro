<?php
include '../../control.php';
class myReview extends review
{
    /**
     * 评审主席变化时，抄送人跟着一起变化
     *
     * @param $reviewId
     */
    public function ajaxgetmailto($reviewId)
    {

        $review = $this->review->getByID($reviewId);
        $users  = $this->loadModel('user')->getPairs('noclosed');
        $deptInfo = $this->loadModel('dept')->getByID( $review->createdDept);
        $mailtos = $deptInfo->manager1.','.$review->createdBy;
        $this->view->mailto = $mailtos;
        echo html::select('mailto[]', $users, $mailtos , 'class="form-control chosen" multiple');
    }


}