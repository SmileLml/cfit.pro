<?php

include '../../control.php';
class myReview extends review
{
    /**
     * View a review.
     *
     * @param int $reviewID
     * @access public
     * @return void
     */
    public function checkhistoryadvice($reviewID)
    {
        $review = $this->review->getByID($reviewID);
        $this->commonAction($review->project);
        $users = $this->loadModel('user')->getPairs('noletter');
        $outsideList1 = $this->loadModel('user')->getUsersNameByType('outsideExpertType');
        $outsideList2 = $this->loadModel('user')->getUsersNameByType('outside');
        $users = array_merge($users, $outsideList1, $outsideList2);
        $this->view->outsideList1 = $outsideList1;
        $this->view->outsideList2 =  $outsideList2;
        $this->view->users = $users;
        $this->view->maxVersion =  $this->review->getReviewNodeMaxVersion($reviewID);
        $dataTrial = $this->review->getTrial($reviewID, $review->version, $users, 2);
        $review->trialDept = $dataTrial['deptid'];
        $review->trialDeptLiasisonOfficer = $dataTrial['deptjkr'];
        $review->trialAdjudicatingOfficer = $dataTrial['deptzs'];
        $review->trialJoinOfficer = $dataTrial['deptjoin'];

        //查询允许编辑审核人员的节点
        $review->allowEditNodes = $this->review->getAllowEditNodes('review', $reviewID, $review->version);

        $this->app->loadLang('reviewissue');
        $stakeholder = $this->loadModel('stakeholder')->getStakeholders($review->project, 'outside');
        $stakeList = array();
        foreach ($stakeholder as $s) {
            $stakeList[$s->user] = $s->companyName . '/' . $s->name;
        }

        //由打基线节点，关闭的结果展示
        $reviewNodeReviewerList = $this->review->getReviewNodeFormatReviewerList($reviewID);
        $closeType = 'nopass';
        if (in_array('baseline', array_keys($reviewNodeReviewerList))) {
            $closeType = 'pass';
        }
        //会议评审
        if ($review->meetingCode) {
            $meetingInfo = $this->loadModel('reviewmeeting')->getMeetingByMeetingCode($review->meetingCode);
            $meetingDetailInfo = $this->review->getMeetingDetailInfo($review->meetingCode, $reviewID);
            $meetingDetailInfo = $this->loadModel('file')->replaceImgURL($meetingDetailInfo, 'meetingContent,meetingSummary');
            if($meetingInfo->meetingSummaryCode){
                $meetingSummary = $meetingDetailInfo->meetingSummary;
                if(!$meetingSummary){
                    $meetingSummaryArray = $this->loadModel('reviewmeeting')->getMeetingSummaryListByReviewId($reviewID, $meetingInfo->owner);
                    $meetingDetailInfo->meetingSummaryArray = $meetingSummaryArray;
                }
            }
            $review->meetingInfo = $meetingInfo;
            $review->meetingDetailInfo = $meetingDetailInfo;
        }
        $this->view->title = $this->lang->review->view;
        $this->view->position[] = $this->lang->review->view;
        $this->view->review = $review;
        $this->view->actions = $this->loadModel('action')->getList('review', $reviewID);

        $this->view->relatedUsers = $this->loadModel('user')->getPairs('noletter');
        $this->view->deptMap = $this->loadModel('dept')->getOptionMenu();
        $this->view->issueList = $this->review->getReviewIssue($review->project, $reviewID);
        $this->view->gradeList = $this->review->getReviewAllGradeList();

        $this->view->companies   = $this->loadModel('company')->getOutsideCompanies();
        $this->view->reviewNodeReviewerList = $reviewNodeReviewerList;
        $this->view->closeType = $closeType;
        $this->view->allstatus = $this->lang->review->statusLabelList + $this->lang->review->statusFile;
        $this->app->loadLang('projectplan');
        $this->display();
    }
}
