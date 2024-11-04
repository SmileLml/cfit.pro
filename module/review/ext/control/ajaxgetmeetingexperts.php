<?php
include '../../control.php';
class myReview extends review
{
    /**
     *  根据评审id获取项目评审预计参会专家
     * @param $type
     *
     */
    public function ajaxgetmeetingexperts($reviewId)
    {
       $exports =  $this->dao->select('meetingPlanExport')->from(TABLE_REVIEW)
            ->where('id')->eq($reviewId)
            ->fetch();
       echo $exports->meetingPlanExport;
    }

}