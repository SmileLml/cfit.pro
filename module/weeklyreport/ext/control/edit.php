<?php
include '../../control.php';
class myWeeklyreport extends weeklyreport
{

    public function edit($reportId,$refer='')
    {
        $this->view->title = $this->lang->weeklyreport->edit;
        $report = $this->weeklyreport->getByID($reportId);
        if($_POST)
        {


            $result = $this->weeklyreport->update($reportId);
            if(dao::$errors)
            {
                $response['result']  = 'fail';
                $response['message'] = dao::$errors;
                $this->send($response);
            }
            if($result['code'] == 200){
                $actionID = $this->loadModel('action')->create('weeklyreport', $report->id, 'edited');

                $this->action->logHistory($actionID, $result['changes']);
            }
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'weeklyreport-index-' . $report->projectId .'-'.$reportId.'.html#app=project';
            $this->send($response);
        }
        $this->loadModel('project')->setMenu($report->projectId);
        $isQA = $this->weeklyreport->authUserReportQA($this->app->user->account,$report->devDept);
        if(!$isQA){
            echo js::alert($this->lang->weeklyreport->nopermission);
            die(js::locate('back'));
        }
        $this->view->projectPlan = $this->loadModel('projectPlan')->getByProjectID($report->projectId);
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
            $this->view->outsidePlan[$outkey]->preWeekOutreport = $this->loadModel("weeklyreportout")->getOutreportByOutProject($outsideProjectId,"id,outFeedbackView,outweeknum,outProjectID");

            $outkey++;
        }
        $this->view->depts                  = $this->loadModel('dept')->getOptionMenu();
        $this->view->mediumOutTask = [''=>'']+$this->view->mediumOutTask + ['其他'=>'其他'];
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




        $this->view->users                  = $this->loadModel('user')->getPairs('noletter');
        $this->view->project                = $this->loadModel('project')->getByID($report->projectId);

        $this->view->requirements = $this->weeklyreport->getProjectRequirementsNew($report->projectId);

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

        //获取上周外部周报。
//        $this->view->outreport = $this->loadModel("weeklyreportout")->getOutreportByOutProject($outsideProjectIdArray,"id,outFeedbackView,outweeknum");

        $this->view->risks                  = json_encode($this->weeklyreport->getProjectRisksNew($report->projectId));
        $this->view->creation               = $this->loadModel('projectPlan')->getCreationByID($this->view->projectPlan->id);

        $this->view->report         = $report;
        $this->view->relations      = json_decode(html_entity_decode($report->productPlan));
        $this->view->reportId       = $reportId;
        $this->view->projectPlanRelation = $this->getProjectPlanRelation($report->projectId);
        $this->view->statusSelects  = $this->weeklyreport->getSelects();
        $this->view->stages         = $this->weeklyreport->getStages($report->projectId);

        if($refer){
            $this->view->referjump = 'top';
            $this->view->refer = helper::createLink('weeklyreportin','browse','','html#app=platform');
        }else{
            $this->view->referjump = 'self';
            $this->view->refer = helper::createLink('weeklyreport','index','projectID='.$report->projectId.'&reportId='.$report->id ,'html#app=project');
        }


        $this->display();
    }

}