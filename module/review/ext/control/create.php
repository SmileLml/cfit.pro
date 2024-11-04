<?php

include '../../control.php';
class myReview extends review
{
    /**
     * 新增字段
     * @param int $projectID
     * @param string $object
     * @param int $productID
     * @param string $reviewRange
     * @param string $checkedItem
     */
    public function create($projectID = 0, $object = '', $productID = 0, $reviewRange = 'all', $checkedItem = '')
    {
        global $app;
        $this->commonAction($projectID);

        if($_POST)
        {
            $reviewID = $this->review->create($projectID, $reviewRange, $checkedItem);

            if(!dao::isError())
            {
                $this->loadModel('action')->create('review', $reviewID, 'created', $this->post->comment);
                $response['result']  = 'success';
                $response['message'] = $this->lang->saveSuccess;
                $response['locate']  = inlink('browse', "project=$projectID");
                $this->send($response);
            }

            $response['result']  = 'fail';
            $response['message'] = dao::getError();
            $this->send($response);
        }

        $this->view->title      = $this->lang->review->create;
        $this->view->position[] = $this->lang->review->create;

        $stakeholder = $this->loadModel('stakeholder')->getStakeholders($projectID, 'outside');
        $stakeList   = array();
        foreach($stakeholder as $s)
        {
            $stakeList[$s->user] = $s->companyName . '/' . $s->name;
        }

        //QA预审
        $depts = $this->loadModel('dept')->getByID($app->user->dept);
        //质量部CM
        $cmList = $this->loadModel('dept')->getRenameListByAccountStr($depts->cm);

        $objectList = $this->lang->review->objectList;
        if(!empty($objectList)){
            $objectList = $this->dao->select('`key`, `value`')->from(TABLE_LANG)->where('module')->eq('review')->andWhere('section')->eq('objectList')->orderBy('order')->fetchPairs();
        }

        $this->loadModel('dept')->getByID($app->user->dept);
        $this->view->object    = $object;
        $this->view->projectID = $projectID;
        $this->view->users     = array('' => '') + $this->loadModel('user')->getPairs('noclosed');
        $this->view->inside    = array('' => '') +$this->loadModel('user')->getUsersNameByType('inside');
        $this->view->outsideList1 =array('' => '') +$this->loadModel('user')->getUsersNameByType('outsideExpertType');
        $this->view->outsideList2 =array('' => '') +$this->loadModel('user')->getUsersNameByType('outside');
        $this->view->relatedUsers  = $this->loadModel('user')->getPairs('noclosed');
        $this->view->qapre      = $depts;
        $this->view->cmList     = $cmList;
        $this->view->objectList = $objectList;
        $project = $this->dao->select('project,mark,id')->from(TABLE_PROJECTPLAN)->where('project')->in($projectID)->fetch();
        $this->view->mark = isset($project->mark) ? $project->mark : '';
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
        $projectPlanInfo = $this->loadModel('projectplan')->getProjectPlanInfo($projectID, 'bearDept');
        $bearDept = $projectPlanInfo->bearDept;
        $this->view->bearDept = $bearDept;
        $this->display();
    }

}