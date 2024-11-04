<?php
include '../../control.php';
class myReview extends review
{
    /**
     * renew 恢复.
     *
     * @param  int  $reviewID
     * @access public
     * @return void
     */
    public function renew($reviewID, $source = 0)
    {
        if($_POST)
        {
            $logChanges = $this->review->renew($reviewID);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $extra = '';
            $updateFields = array_column($logChanges, 'field');
            if(in_array('meetingCode', $updateFields)){
                $meetingCode = $this->review->getMeetingCodeInLogChanges($logChanges);
                if($meetingCode){
                    $extra = '绑定会议，会议单号：'.$meetingCode;
                }
            }
            $actionID = $this->loadModel('action')->create('review', $reviewID, 'renew', $this->post->comment,$extra);

            if($logChanges) {
                $this->action->logHistory($actionID, $logChanges);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }
        $this->view->title      = $this->lang->review->renew;
        $this->view->position[] = $this->lang->review->renew;
        $review = $this->review->getByID($reviewID);
        $checkRes = $this->review->checkReviewIsAllowRenew($review, $this->app->user->account);
        if($checkRes['result']){ //允许恢复
            $lastStatus = $review->lastStatus;
            $version    = $review->version;
            $nodeCode = $this->review->getReviewNodeCodeByStatus($lastStatus, $lastStatus);
            if($nodeCode == $this->lang->review->nodeCodeList['formalAssignReviewerAppoint']){
                $nodeCode = $this->lang->review->nodeCodeList['formalAssignReviewer']; //评审主席指派专家
            }
            $exWhere =  "nodeCode != 'formalAssignReviewerAppoint'";
            $historyReviewStageList = $this->review->getHistoryReviewStageList('review', $reviewID, $version, $nodeCode, $exWhere);
            $historyReviewStageList = array_column($historyReviewStageList, 'nodeCodeName', 'nodeCode');
            if($lastStatus == 'pass'){
                $historyReviewStageList['close'] = '待关闭';
            }
            $this->view->historyReviewStageList = array('' => '') + $historyReviewStageList;

        }
        $this->view->review     = $review;
        $this->view->checkRes   = $checkRes;
        $this->view->source = $source;
        //项目承担部门
        $projectPlanInfo = $this->loadModel('projectplan')->getProjectPlanInfo($review->project, 'bearDept');
        $bearDept = $projectPlanInfo->bearDept;
        $this->view->bearDept = $bearDept;
        $this->display();
    }
}