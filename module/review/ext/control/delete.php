<?php

include '../../control.php';
class myReview extends review
{
    /**
     * 删除评审
     * @param $reviewID
     */
    public function delete($reviewID,$source =0)
    {
        if(!empty($_POST))
        {
            $review = $this->review->getByID($reviewID);

            $this->dao->update(TABLE_REVIEW)->set('deleted')->eq('1')->where('id')->eq($reviewID)->exec();
            //有会议号
            if($review->meetingCode){
                if(in_array($review->status, $this->lang->review->inMeetingReviewStatusList)){
                    $meetingDetailInfo = $this->loadModel('reviewmeeting')->getMeetingDetailInfoByReviewId($reviewID);
                    if($meetingDetailInfo){
                        $this->loadModel('reviewmeeting')->cancelBindReviewMeeting($review->meetingCode, $reviewID);
                    }
                }
            }

            $this->loadModel('action')->create('review', $reviewID, 'delete', $this->post->comment);
            $reason = '1002';//代表评审加入
            $this->review->deleteWhiteList($reviewID,$reason);//删除白名单

            if(isonlybody()) return print(js::closeModal('parent.parent', 'this', "function(){parent.parent.location.reload();}"));//die(js::closeModal('parent.parent', 'parent'));
            die(js::reload('parent'));
        }

        $review = $this->review->getByID($reviewID);
        $this->view->actions = $this->loadModel('action')->getList('review', $reviewID);
        $this->view->review = $review;
        $this->view->users   = $this->loadModel('user')->getPairs();
        $this->view->source = $source;
        $this->display();

    }
}