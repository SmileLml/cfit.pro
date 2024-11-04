<?php
include '../../control.php';
class myWeeklyreport extends weeklyreport
{

    public function create($projectID)
    {
        if($_POST)
        {
            /*$this->postCheck();
            $check = $this->weeklyreport->checkDate($projectID, $_POST['reportStartDate'], $_POST['reportEndDate']);
            if($check == false){
                $response['result']  = 'fail';
                $response['message'] = dao::$errors['reportDateUnavailable'];
                $this->send($response);
            }*/
            $lastInsertID = $this->weeklyreport->create();
            if(dao::$errors)
            {
                $response['result']  = 'fail';
                $response['message'] = dao::$errors;
                $this->send($response);
            }
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'weeklyreport-index-' . $projectID .'-'. $lastInsertID .'.html#app=project';
            $this->loadModel('action')->create('weeklyreport', $lastInsertID, 'created');
            $this->send($response);
        }


        $this->loadModel('project')->setMenu($projectID);

        $this->view->title = $this->lang->weeklyreport->create;
        $this->view->projectID = $projectID;
        $this->view->projectPlan = $this->loadModel('projectPlan')->getByProjectID($projectID);

        if(!$this->view->projectPlan){
            $this->toSelfError($this->lang->weeklyreport->noProjectplanError, $projectID);
        }

        if($this->view->projectPlan->status != 'projected'){
            $this->toSelfError($this->lang->weeklyreport->ProjectplanNoProjectedError , $projectID);
        }

        $isQA = $this->weeklyreport->authUserReportQA($this->app->user->account,$this->view->projectPlan->bearDept);
        if(!$isQA){
            echo js::alert($this->lang->weeklyreport->nopermission);
            die(js::locate('back'));
        }


        $this->view->depts                  = $this->loadModel('dept')->getOptionMenu();
        /*$deptArr = explode(',',$this->view->projectPlan->bearDept);
        $devDept = "";
        foreach ($deptArr as $dept){
            if(isset($this->view->depts[$dept])){
                $devDept .= $this->view->depts[$dept];
            }
        }*/


        $tempbearDept = explode(',',$this->view->projectPlan->bearDept);
        $this->view->devDept = '';
        foreach ($tempbearDept as $dept){
            $this->view->devDept .= $this->view->depts[$dept]."<br />";

        }

        $this->view->qa = implode(',',$this->weeklyreport->getProjectQA($tempbearDept));

        $this->view->outprojectlanID = trim($this->view->projectPlan->outsideProject,',');
        $outsideProjectIdArray = explode(',', $this->view->outprojectlanID);
        $this->view->outsidePlan = [];
        $outkey = 0;
        $this->view->mediumOutTask = [];
        foreach ($outsideProjectIdArray as $outsideProjectId){
            if(empty($outsideProjectId)) continue;
            $this->view->outsidePlan[$outkey] = $this->loadModel('outsideplan')->getSimpleByID($outsideProjectId);
            $this->view->outsidePlan[$outkey]->subprojectsTask = $this->loadModel('outsideplan')->getTaskByOutsideplanID($outsideProjectId);
            $this->view->outsidePlan[$outkey]->subprojectsTaskStr = '';
            foreach ($this->view->outsidePlan[$outkey]->subprojectsTask as $task){
                $this->view->outsidePlan[$outkey]->subprojectsTaskStr .= $task->subTaskName.PHP_EOL;
                $this->view->mediumOutTask[$task->id] = $task->subTaskName;
            }
            //获取上周周报
            $this->view->outsidePlan[$outkey]->preWeekOutreport = $this->loadModel("weeklyreportout")->getOutreportByOutProject($outsideProjectId,"id,outFeedbackView,outweeknum,outProjectID");
            $outkey++;
        }

        $this->view->mediumOutTask = [''=>'']+$this->view->mediumOutTask + ['其他'=>'其他'];

        $outsidePlans = [];
        foreach ( $this->view->outsidePlan as $outplan)
        {
            $outsidePlan['code'] = $outplan->code;
            $outsidePlan['name'] = $outplan->name;
            $outsidePlan['begin'] = $outplan->begin;
            $outsidePlan['end']  = $outplan->end;
            $outsidePlan['workload'] = $outplan->workload;
            $outsidePlan['subprojectsTaskStr'] = '';
            foreach ($outplan->subprojectsTask as $subproject){
                $outsidePlan['subprojectsTaskStr'] .= $subproject->subTaskName.PHP_EOL;
            }

            $outsidePlans[] = $outsidePlan;
        }

//        $this->view->outsidePlan =  base64_encode(json_encode($outsidePlans));

//        $this->view->projectPlanRelation    = $this->getProjectPlanRelation($projectID);
        $this->view->week                   = $this->weeklyreport->getWeekMyActionAndEnd();
        $this->view->statusSelects          = $this->weeklyreport->getSelects();
        $this->view->risks                  = json_encode($this->weeklyreport->getProjectRisksNew($projectID));
        $this->view->issues                 = $this->weeklyreport->getProjectIssues($projectID);
        $builds                             = $this->weeklyreport->getProjectReleases($projectID);
        $this->view->users                  = $this->loadModel('user')->getPairs('noletter');
        $i = 0;
        $buildLines = '';
        foreach ($builds as $build)
        {
            $i++;
            $buildLines .= "[$i]". $build->date .' 由'.zget($this->view->users, $build->createdBy, '').'发布了 '.
                rtrim(str_replace(PHP_EOL,',', strip_tags($build->desc)),',')
                .PHP_EOL;
        }
        $this->view->builds                 = $buildLines;
        $this->view->project                = $this->loadModel('project')->getByID($projectID);

        //获取上周外部周报。
//        $this->view->outreport = $this->loadModel("weeklyreportout")->getOutreportByOutProject($outsideProjectIdArray,"id,outFeedbackView,outweeknum,outProjectID");
        $this->view->creation               = $this->loadModel('projectPlan')->getCreationByID($this->view->projectPlan->id);

        $this->view->requirements = $this->weeklyreport->getProjectRequirementsNew($projectID);

        $requirementStr = '';
        $productRequirement = [];
        $requirementIdStr = '';
        foreach ($this->view->requirements as $requirement){
            $requirementStr .= $requirement->code.":".$requirement->name.PHP_EOL;
            $productRequirement[$requirement->id] = $requirement->code.":".$requirement->name;
            $requirementIdStr .= $requirement->id.',';
        }
        if($requirementIdStr){
            $requirementIdStr = trim($requirementIdStr,',');
        }

        $this->view->requirementIdStr = $requirementIdStr;
        $this->view->requirementStr = $requirementStr;
        $this->view->productRequirement = $productRequirement;
        //取项目需求和需求条目
        $this->view->demands = $this->weeklyreport->getDemandInfo($projectID, $this->view->projectPlan->id);
        $this->view->stages  = $this->weeklyreport->getStages($projectID);

        $this->display();
    }

}