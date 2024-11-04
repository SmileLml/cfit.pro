<?php
include '../../control.php';
class myReview extends review
{
    /**
     * review a review 审核.
     *
     * @param  int  $reviewID
     * @param sting $status
     * @access public
     * @return void
     */
    public function assign($reviewID, $status = '',$source =0)
    {
        if($_POST) {
            $logChanges = $this->review->assign($reviewID);
            if(dao::isError()) {
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
            $actionID = $this->loadModel('action')->create('review', $reviewID, 'assigning', $this->post->comment, $extra);
            if($logChanges) {
                $this->action->logHistory($actionID, $logChanges);
            }
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }
        $review = $this->review->getByID($reviewID);
        $isRenew = 0;
        if(!empty($review->renewTime)){
            $isRenew = 1;
        }
        $this->view->isRenew = $isRenew;
        //是否包含不需要初审的对象
        $isIncludeNotNeedFirstReviewObject = $this->review->getIsIncludeNotNeedFirstReviewObject($review->object);
        $this->view->isIncludeNotNeedFirstReviewObject = $isIncludeNotNeedFirstReviewObject;
        //是否允许指派
        $checkRes = $this->review->checkReviewIsAllowAssign($review, $this->app->user->account);

        $this->view->title      = $this->lang->review->assign;
        $this->view->position[] = $this->lang->review->assign;
        $this->view->review     = $review;
        //会议评审主席列表
        $meetingOwnerList = [];
        if($checkRes['result']){
            $this->view->actions    = $this->loadModel('action')->getList('review', $reviewID);
            $this->view->users      = $this->loadModel('user')->getPairs('noletter');
            $this->view->outsideList1 = array('' => '') + $this->loadModel('user')->getUsersNameByType('outsideExpertType');
            $this->view->outsideList2 = array('' => '') + $this->loadModel('user')->getUsersNameByType('outside');
            $status = $review->status;
            $viewSuffix = $this->lang->review->assignViewSuffixList[$status];
            $this->view->view = 'assign'. $viewSuffix.'.html.php';
            //评审方式
            $this->view->gradeList = $this->review->getReviewLangList('gradeList', true);
            $mailto = $review->createdBy;
            $deptInfo = $this->loadModel('dept')->getByID( $review->createdDept);
            if($deptInfo){
                $mailto .= ','.$deptInfo->manager1;
            }
            if($review->status == $this->lang->review->statusList['waitFirstAssignDept']){ //待指派初审部门
                $this->view->depts = $this->loadModel('projectplan')->getTopDepts();
                //关联项目信息
                $this->app->loadLang('projectplan');
                $projectId = $review->project;
                $planSelect = 'project,isImportant, type';
                $projectPlanType = 0;
                $isImportant = 2;
                $projectPlan = $this->loadModel('projectplan')->getPlanMainInfoByProjectID($projectId, $planSelect);
                if(isset($projectPlan->project)){
                    $projectPlanType = $projectPlan->type;
                    $isImportant     =  $projectPlan->isImportant;
                }
                $defGrade = $this->review->getDefGrade($review->objects, $projectPlanType, $isImportant);
                $this->view->projectPlan = $projectPlan;
                $this->view->defGrade = $defGrade;
            }elseif ($review->status ==  $this->lang->review->statusList['waitFormalAssignReviewer']){ //指派正式人员
                $minTime = helper::today();
                $meetingOwnerList = $this->loadModel('reviewmeeting')->getAllowBindMeetingOwnerList($minTime);
                $mailto .= ','.$review->reviewer;
                $mailto .= ','.$review->relatedUsers;
            }
            $this->view->mailto = $mailto;
        }

        $this->view->checkRes   = $checkRes;
        $this->view->source = $source;
        $this->view->meetingOwnerList = $meetingOwnerList;
        //项目承担部门
        $projectPlanInfo = $this->loadModel('projectplan')->getProjectPlanInfo($review->project, 'bearDept');
        $bearDept = $projectPlanInfo->bearDept;
        $this->view->bearDept = $bearDept;
        $this->display();
    }

}