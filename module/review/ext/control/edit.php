<?php

include '../../control.php';
class myReview extends review
{
    /**
     * Edit a review.
     *
     * @param  int  $reviewID
     * @access public
     * @return void
     */
    public function edit($reviewID,$flag = 0,$source =0)
    {
        $review = $this->review->getByID($reviewID);
        $this->commonAction($review->project);

        if($_POST)
        {
            $changes = $this->review->update($reviewID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes or $this->post->comment)
            {
                if($source ==1){
                    $actionID = $this->loadModel('action')->create('reviewmeeting', $reviewID, 'editreview', $extra=$review->title);
                    $this->action->logHistory($actionID, $changes);
                }else{
                    $actionID = $this->loadModel('action')->create('review', $reviewID, 'Edited', $this->post->comment);
                    $this->action->logHistory($actionID, $changes);
                }


            }
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            if($source ==1){
                $response['locate']  =  $this->createLink('reviewmanage', 'board', "");
            }else{
                if($flag){
                    $response['locate']  = inlink('view', "project=$review->id");
                }else{
                    $response['locate']  = inlink('browse', "project=$review->project");
                }
            }
            $this->send($response);

        }

        $stakeholder = $this->loadModel('stakeholder')->getStakeholders($review->project, 'outside');
        $stakeList   = array();
        foreach($stakeholder as $s)
        {
            $stakeList[$s->user] = $s->companyName . '/' . $s->name;
        }

        //QA预审
        $depts = $this->loadModel('dept')->getByID($this->app->user->dept);
        //质量部CM
        $cmList = $this->loadModel('dept')->getRenameListByAccountStr($depts->cm);

        $objectList = $this->lang->review->objectList;
        if(!empty($objectList)){
            $objectList = $this->dao->select('`key`, `value`')->from(TABLE_LANG)->where('module')->eq('review')->andWhere('section')->eq('objectList')->orderBy('order')->fetchPairs();
        }
        $this->view->title      = $this->lang->review->edit;
        $this->view->position[] = $this->lang->review->edit;
        $this->view->review     = $review;
        $this->view->project    = $this->loadModel('project')->getByID($review->project);
        $this->view->products   = $this->loadModel('product')->getPairs($review->project);
        $this->view->users      = array(''=>'') + $this->loadModel('user')->getPairs('noclosed');
        $this->view->outsideList1 =array('' => '') +$this->loadModel('user')->getUsersNameByType('outsideExpertType');
        $this->view->outsideList2 =array('' => '') +$this->loadModel('user')->getUsersNameByType('outside');
        $this->view->relatedUsers  = $this->loadModel('user')->getPairs('noclosed');
        $this->view->qapre     = $depts;
        $this->view->cmList     = array('' => '') + $cmList;
        $project = $this->dao->select('project,mark,id')->from(TABLE_PROJECTPLAN)->where('project')->in($review->project)->fetch();
        $this->view->mark = isset($project->mark) ? $project->mark : '';
        $this->view->source = $source;
        $this->view->objectList = $objectList;
        //项目主从关系
        //查询多条
        $this->view->mainRelationInfo  = $mainRelationInfo  = $this->loadModel("projectplanmsrelation")->getBySlavePlanID($project->id);
        //查询单条
        $this->view->slaveRelationInfo = $slaveRelationInfo = $this->loadModel("projectplanmsrelation")->getByMainPlanID($project->id);
        $this->view->relationProjectplanList = [];
        if($mainRelationInfo || $slaveRelationInfo){
            $planArr = [$project->id];
            if($mainRelationInfo){ //该项目是从项目，获取主项目
                foreach ($mainRelationInfo as $slave){
                    $planArr[] = $slave->mainPlanID;
                }
            }
            if($slaveRelationInfo){ //该项目是主项目，获取从项目
                $planArr = array_merge($planArr,explode(',',$slaveRelationInfo->slavePlanID));
            }

            //$relationProjectplanList =  array_column($this->loadModel('projectplan')->getByIDMultipleList(array_unique($planArr),"id,mark"),'mark','id');
            $planIds = array_flip(array_flip($planArr));
            $relationProjectplanList = $this->loadModel('projectplan')->getCodeListByPlanIds($planIds);
            $this->view->relationProjectplanList = $relationProjectplanList;
        }
        //项目承担部门
        $projectPlanInfo = $this->loadModel('projectplan')->getProjectPlanInfo($review->project, 'bearDept');
        $bearDept = $projectPlanInfo->bearDept;
        $this->view->bearDept = $bearDept;
        $this->display();
    }
}