<?php
include '../../control.php';
class myReview extends review
{
    /**
     * review a review 审核.
     *
     * @param  int  $reviewID
     * @access public
     * @return void
     */
    public function review($reviewID,$source =0)
    {
        if($_POST) {
            $logChanges = $this->review->review($reviewID);
            if(dao::isError()) {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
//            $extra = '';
//            $updateFields = array_column($logChanges, 'field');
//            if(in_array('meetingCode', $updateFields)){
//                $meetingCode = $this->review->getMeetingCodeInLogChanges($logChanges);
//                if($meetingCode){
//                    $extra = '绑定会议，会议单号：'.$meetingCode;
//                }
//            }
//
//            //日志扩展信息
//            $isSetHistory = true;
//            $actionID = $this->loadModel('action')->create('review', $reviewID, 'reviewed', $this->post->comment, $extra, '', true, $isSetHistory, $logChanges);
//
            /*
             * 日志记录中已经添加详情，此处无需重复添加
            if($logChanges) {
                $this->action->logHistory($actionID, $logChanges);
            }
            */
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }
        $userAccount = $this->app->user->account;
        $review = $this->review->getByID($reviewID);
        $allowReviewStatusList = $this->lang->review->allowReviewStatusList;
        $allowAssignStatusList = $this->lang->review->allowAssignStatusList;
        $status = $review->status;
        if(in_array($status, $allowReviewStatusList) || in_array($status, $allowAssignStatusList)){
            $review->dealUser = $review->reviewers;
        }
        $dealUsers = $review->dealUser;

        $dealUser = explode(',', str_replace(' ', '', $dealUsers));
        //取出最后一个评审人
        //判断当前用户是否是最后一个验证人
        $this->view->lastVerifyer = '';
        if(count($dealUser) == 1){
            $this->view->lastVerifyer = 1;
        }
        //是否允许审批
        $checkRes = $this->review->checkReviewIsAllowReview($review, $userAccount);
        //评审验证判断
        if($review->status == 'waitVerify' or $review->status == 'verifying' ){
            $issueCount = $this->loadModel('reviewproblem')->getReviewIssueCount2($review->id,'createAndAccept');
            if($issueCount != 0 and $this->view->lastVerifyer == 1){
                die(js::alert('该评审存在'.$issueCount.'个问题未验证，请先验证评审问题') . js::reload('parent'));
            }
        }
        $this->view->title      = $this->lang->review->review;
        $this->view->position[] = $this->lang->review->review;

        if($checkRes['result']){
            $this->view->actions    = $this->loadModel('action')->getList('review', $reviewID);
            $this->view->users      = $this->loadModel('user')->getPairs('noletter');
            $review->verifyReviewers = '';
            //获取评审方式
            $gradeList = $this->review->getReviewLangList('gradeList', true);
            //初审参与人员审核时同时判断是否时主审人员
            $isSetAdviceGrade =  $this->review->isSetAdviceGrade($review, $userAccount);
            if($isSetAdviceGrade){
                //获得建议评审方式
                $review->adviceGrade = $this->review->getReviewAdviceGrade($review->type, $review->grade);
                $adviceGradeList = $this->review->getReviewAdviceGradeList($review->adviceGrade, true);
                $this->view->adviceGradeList  = $adviceGradeList;
            }else if(in_array($review->status, $this->lang->review->allowAssignVerifyersStatusList)){ //确定评审结论
                if($review->isFirstReview == '2'){ //当跳过初审时
                    $nodeCode = 'formalReview';
                }else{//获得评审验证人员默认为初审人员
                    $nodeCode = 'firstReview';
                }
                $verifyReviewers = $this->review->getReviewersByNodeCode('review', $reviewID, $review->version, $nodeCode);
                $review->verifyReviewers = $verifyReviewers;
            }
            if($review->status ==  $this->lang->review->statusList['archive']){
                $archiveList = $this->loadModel('archive')->getMaxVersionArchiveList('review', $reviewID);
                //是否需要显示安全测试和性能测试
                $isShowSafetyTest = $this->review->isShowSafetyTest($review->object, $review->type);
                $this->view->archiveList = $archiveList;
                $this->view->isShowSafetyTest = $isShowSafetyTest;
            }
            $this->view->gradeList = $gradeList;
            $this->view->isSetAdviceGrade = $isSetAdviceGrade;
        }
        $this->app->loadLang('cm');
        $this->view->typelist = array(''=>'') + $this->lang->cm->typeList;
        $this->view->review  = $review;
        $this->view->checkRes = $checkRes;
        //抄送人
        $mailto = '';
        if(in_array($review->status, $this->lang->review->allowMeetingReviewStatusList)){
            $mailto = $review->createdBy; //创建人
        }else if($review->status == 'waitFormalOwnerReview' ||  $review->status == 'waitMeetingOwnerReview'){ //会议评审结论，线上评审结论
            $mailto = $review->createdBy .','.$review->relatedUsers; //创建人,相关人员
            $rev = $this->loadModel('dept')->getByID( $review->createdDept);
            if($rev){
                $mailto .= ',' . $rev->manager1;
            }
        }
        //评审会主席给结论和会议评审确认评审会议纪要，查出初审人员和提出问题人员
        if($review->status == 'waitFormalOwnerReview'){
            $users = $this->loadModel('user')->getPairs('noletter');
            $verifyReviewers = $this->review->getReviewersByNodeCode('review', $reviewID, $review->version, $nodeCode);
            $this->view->preliminaryReviewer = zmget($users, $verifyReviewers);

            $issueList = $this->review->getReviewIssue($review->project, $reviewID);
            $issueUsers = array();
            foreach ($issueList as $issue){
                array_push($issueUsers, zget($users,$issue->raiseBy,''));
            }
            $this->view->questionReviewer = implode(',', array_unique($issueUsers));
        }

        $this->view->mailto = $mailto;
        $this->view->source = $source;
        $this->display();
    }
}