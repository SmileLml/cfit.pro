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
    public function recall($reviewID,$source =0)
    {
        $review = $this->review->getByID($reviewID);
        $status = $review->status;
        $rejectStage = $this->review->getRecallRejectStage($status);

        //撤回的记录一下撤回状态
        $params = new stdClass();
        $params->status = 'recall';
        $params->rejectStage = $rejectStage;
        $params->dealUser    = $review->createdBy;

        $this->dao->update(TABLE_REVIEW)->data($params)->where('id')->eq($reviewID)->exec();
        //有会议号
        if($review->meetingCode){
            if(in_array($review->status, $this->lang->review->inMeetingReviewStatusList)){
                $meetingDetailInfo = $this->loadModel('reviewmeeting')->getMeetingDetailInfoByReviewId($reviewID);
                if($meetingDetailInfo){
                    $this->loadModel('reviewmeeting')->cancelBindReviewMeeting($review->meetingCode, $reviewID);
                }
            }
        }
        $this->loadModel('action')->create('review', $reviewID, 'Recall');

        die(js::reload('parent.parent'));
    }
}