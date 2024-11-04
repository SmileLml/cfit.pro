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
    public function view($reviewID)
    {
        $review = $this->review->getByID($reviewID);
        $this->view->maxVersion =  $this->review->getReviewNodeMaxVersion($reviewID);
        $this->view->issues = $this->loadModel('reviewissue')->getIssueByReview($reviewID);
        $this->commonAction($review->project);
        $users = $this->loadModel('user')->getPairs('noletter');
        $outsideList1 = $this->loadModel('user')->getUsersNameByType('outsideExpertType');
        $outsideList2 = $this->loadModel('user')->getUsersNameByType('outside');
        $users = array_merge($users, $outsideList1, $outsideList2);
        $this->view->outsideList1 = $outsideList1;
        $this->view->outsideList2 = $outsideList2;
        $this->view->users = $users;
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
        if (in_array('baseline', array_keys($reviewNodeReviewerList)) || in_array('archive', array_keys($reviewNodeReviewerList))) {
            $closeType = 'pass';
        }
        //会议评审
        if ($review->meetingCode) {
            $meetingInfo = $this->loadModel('reviewmeeting')->getMeetingByMeetingCode($review->meetingCode);
            $meetingDetailInfo = $this->review->getMeetingDetailInfo($review->meetingCode, $reviewID);
            $meetingDetailInfo = $this->loadModel('file')->replaceImgURL($meetingDetailInfo, 'meetingContent,meetingSummary');
            if ($meetingInfo->meetingSummaryCode) {
                $meetingSummary = $meetingDetailInfo->meetingSummary;
                if (!$meetingSummary) {
                    $meetingSummaryArray = $this->loadModel('reviewmeeting')->getMeetingSummaryListByReviewId($reviewID, $meetingInfo->owner);
                    $meetingDetailInfo->meetingSummaryArray = $meetingSummaryArray;
                }
            }
            $review->meetingInfo = $meetingInfo;
            $review->meetingDetailInfo = $meetingDetailInfo;
        }
        //评审归档信息
        $archiveList = $this->loadModel('archive')->getMaxVersionArchiveList('review', $reviewID);
        //评审打基线信息
        $baseLineList = $this->review->getBaseLineInfo($review);
        if(!empty($baseLineList)){
            $this->app->loadLang('cm');
            $this->view->baseLineTypelist = $this->lang->cm->typeList;
        }

        //项目主从关系
        $planID = $this->dao->select('id')->from(TABLE_PROJECTPLAN)->where('project')->eq($review->project)->fetch('id');
        $this->view->mainRelationInfo = $mainRelationInfo = $this->loadModel("projectplanmsrelation")->getByMainPlanID($planID);
        $this->view->slaveRelationInfo = $slaveRelationInfo = $this->loadModel("projectplanmsrelation")->getBySlavePlanID($planID);
        $this->view->relationProjectplanList = [];
        if($mainRelationInfo || $slaveRelationInfo){
            $planArr = [$planID];
            if($mainRelationInfo){
                $planArr = array_merge($planArr,explode(',',$mainRelationInfo->slavePlanID));
            }
            if($slaveRelationInfo){
                foreach ($slaveRelationInfo as $slave){
                    $planArr[] = $slave->mainPlanID;
                }
            }
            $this->view->relationProjectplanList = array_column($this->loadModel("projectplan")->getByIDMultipleList(array_unique($planArr),"id,mark"),'mark','id');
        }

        $this->view->archiveList = $archiveList;
        $this->view->baseLineList = $baseLineList;
        $this->view->typeList   = $this->lang->reviewissue->typeList;
        $this->view->statusList = $this->lang->reviewissue->statusList;
        $this->view->title = $this->lang->review->view;
        $this->view->position[] = $this->lang->review->view;
        $this->view->review = $review;
        $this->view->actions = $this->loadModel('action')->getList('review', $reviewID);

        $this->view->relatedUsers = $this->loadModel('user')->getPairs('noletter');
        $this->view->deptMap = $this->loadModel('dept')->getOptionMenu();
        $this->view->issueList = $this->review->getReviewIssue($review->project, $reviewID);
        $this->view->gradeList = $this->review->getReviewAllGradeList();
        $this->view->companies = $this->loadModel('company')->getOutsideCompanies();
        $this->view->reviewNodeReviewerList = $reviewNodeReviewerList;
        $this->view->closeType = $closeType;
        $this->view->allstatus = $this->lang->review->statusLabelList + $this->lang->review->statusFile + $this->lang->review->statusReject;
        $this->app->loadLang('projectplan');
        //是否可以操作附件
        $isAllowOperateFile = $this->review->isAllowOperateFile($review);
        $this->view->isAllowOperateFile = $isAllowOperateFile;
        $this->display();
    }
}
