<?php
include '../../control.php';
class myReview extends review
{
    /**
     * Submit a review.
     *
     * @param  int  $reviewID
     * @access public
     * @return void
     */
    public function submit($reviewID,$source =0)
    {
        if($_POST)
        {
            $logChanges = $this->review->submit($reviewID);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $actionID = $this->loadModel('action')->create('review', $reviewID, 'applyreview', $this->post->comment);
            if($logChanges) {
                $this->action->logHistory($actionID, $logChanges);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }

        $review = $this->review->getByID($reviewID);
        $checkRes = $this->review->checkReviewIsAllowApply($review, $this->app->user->account);
        $this->view->title      = $this->lang->review->submit;
        $this->view->position[] = $this->lang->review->submit;
        if($checkRes['result']){
            $this->view->actions    = $this->loadModel('action')->getList('review', $reviewID);
            $this->view->users      = $this->loadModel('user')->getPairs('noletter');
            //下一状态
            $nextStatus  = $this->review->getSubmitNextStatus($review);
            $viewSuffix = $this->review->getSubmitViewSuffix($nextStatus);
            $this->view->view = 'submit'. $viewSuffix.'.html.php';
            if($review->status =='firstPassButEdit' and $review->type =='cbp'){
                $this->view->view = 'submit'. 'Verify'.'.html.php';
                $nextStatus = 'waitVerify';
            }
            
            $maxVersion =  $this->review->getReviewNodeMaxVersion($reviewID);
            if($nextStatus == $this->lang->review->statusList['waitFirstReview']){ //驳回到初审
                //获得评审验证人员默认为初审人员
                $nodeCode = 'firstReview';
                $review->firstReviewers = $this->review->getReviewersByNodeCode('review', $reviewID, $maxVersion, $nodeCode);
            }else if($nextStatus == $this->lang->review->statusList['waitVerify']){ //流转到验证材料
                //获得评审验证人员默认为验证人员
                //$nodeCode = $this->lang->review->nodeCodeList['verify'];
                $verifyReviewers = $this->review->getReviewVerifyUsers($reviewID, $maxVersion);
                if(empty($verifyReviewers)){
                    $nodeCode = 'firstReview';
                    $verifyReviewers  = $this->review->getReviewersByNodeCode('review', $reviewID, $maxVersion, $nodeCode);
                }
                if(!is_array($verifyReviewers)){
                    $verifyReviewers = explode(',', $verifyReviewers);
                }

                //提出问题还未验证的人员
                $unDealReviewIssueUsers =  $this->loadModel('reviewissue')->getUnDealIssueUsersByReviewId($reviewID);
                $type  = $review->type;
                $owner = $review->owner ;
                if(in_array($type, $this->lang->review->organizationTypeList)){ //组织级评审,去掉评审主席
                    $unDealReviewIssueUsers = array_diff($unDealReviewIssueUsers, [$owner]);
                }
                if($type == 'dept' && $unDealReviewIssueUsers){ //部门级评审，待审核人中添加提出问题还未验证的提出人
                    $verifyReviewers = array_merge($verifyReviewers, $unDealReviewIssueUsers); //待审核人中添加提出问题还未验证的提出人
                    $unDealReviewIssueUsers = [];
                }
                $review->verifyReviewers  = $verifyReviewers; //验证人
                $this->view->unDealReviewIssueUsers = $unDealReviewIssueUsers; //提出问题还未验证的提出人

                $users = $this->loadModel('user')->getPairs('noclosed');
                $otherUsersAccount = array_merge($verifyReviewers, $unDealReviewIssueUsers);
                $otherUsers = $this->loadModel('user')->getUserListByAccounts($otherUsersAccount);
                $users = $otherUsers + $users;
                $this->view->users = $users;
            }
        }

        $this->view->review     = $review;
        $this->view->checkRes   = $checkRes;
        $this->view->source = $source;
        $this->display();
    }
}