<?php

class weeklyreportin extends control
{
    public function browse($browseType = 'all', $param = 0, $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $this->view->title         = $this->lang->weeklyreportin->common;
        $this->view->pager         = $pager;
        $this->view->depts         = $this->loadModel('dept')->getOptionMenu();
        $this->view->users         = $this->loadModel('user')->getPairs('noletter|noclosed');
        $this->view->weeklyreports = $this->weeklyreportin->getList($browseType, $pager, $orderBy);
        $this->view->browseType    = $browseType;
        $this->view->orderBy       = $orderBy;
        $this->view->param         = $param;

        $this->display();
    }

    /**
     * 导出excel
     * @param string $orderBy
     * @param string $browseType
     * @param mixed  $projectId
     * @param mixed  $reportId
     * @param mixed  $startDate
     * @param mixed  $endDate
     */
    public function export($orderBy = 'id_desc', $browseType = 'all', $projectId = 0, $reportId = 0, $startDate = '', $endDate = '')
    {
        $this->loadModel('weeklyreport');
        if ($_POST && 'xlsx' == $_POST['fileType']) {
            $this->loadModel('file');
            $this->loadModel('projectplan');
            $this->loadModel('outsideplan');
            $this->loadModel('risk');
            $applicationLang   = $this->lang->weeklyreport;
            $applicationConfig = $this->config->weeklyreport;

            // Create field lists.
            $fields = $this->post->exportFields ?: explode(',', $applicationConfig->list->exportFields);
            foreach ($fields as $key => $fieldName) {
                $fieldName          = trim($fieldName);
                $fields[$fieldName] = $applicationLang->{$fieldName} ?? $fieldName;
                unset($fields[$key]);
            }
            /*$qa = $this->weeklyreport->getUserQADept($this->app->user->account);
            $applications = array();
            if($qa['isogQA'] != 0 or !empty($qa['depts'])){
                $objDao = $this->dao->select('*')->from(TABLE_PROJECTWEEKLYREPORT)
                    ->where('deleted')->eq('0')
                    ->beginIF(!empty($qa['depts']))->andwhere()->markleft(1);
                if(!empty($qa['depts'])){
                    foreach ($qa['depts'] as $key => $dept){
                        if($key > 0){
                            $objDao->orwhere(" FIND_IN_SET('{$dept->id}',`devDept`) ");
                        }else{
                            $objDao->where(" FIND_IN_SET('{$dept->id}',`devDept`) ");
                        }
                    }
                }
                $applications = $objDao->markright(1)->fi()
                    ->beginIF('' != $_POST['weeknum'])->andWhere('weeknum')->eq($_POST['weeknum'])->fi()
                    ->orderBy($orderBy)
                    ->fetchAll('id');
            }*/

            $applications = $this->dao->select('*')->from(TABLE_PROJECTWEEKLYREPORT)
                ->where('deleted')->eq('0')
                ->beginIF('' != $_POST['weeknum'])->andWhere('weeknum')->eq($_POST['weeknum'])->fi()
                ->orderBy($orderBy)
                ->fetchAll('id');

            // Get users, products and executions.
            $users             = $this->loadModel('user')->getPairs('noletter');
            $statusSelects     = $this->weeklyreport->getSelects();
            $insideStatusList  = $statusSelects['insideReportStatusList'];
            $outsideStatusList = $statusSelects['outsideReportStatusList'];
            $depts             = $this->loadModel('dept')->getOptionMenu();
            foreach ($applications as $application)
            {
                //周报时间
                $application->reportDate = '第'.$application->weeknum.'周+'.$application->reportStartDate.'-'.$application->reportEndDate;
                $application->reportMedium = $this->dao->select("*")->from(TABLE_PROJECTWEEKLYREPORT_MEDIUM)->where("weekreportID")->eq($application->id)->fetchAll();
                $application->reportOutmile = $this->dao->select("*")->from(TABLE_PROJECTWEEKLYREPORT_OUTMILE)->where("weekreportID")->eq($application->id)->fetchAll();
                $application->reportInsidemile = $this->dao->select("*")->from(TABLE_PROJECTWEEKLYREPORT_INSIDEMILE)->where("weekreportID")->eq($application->id)->fetchAll();
                $application->reportRisk = $this->dao->select("*")->from(TABLE_PROJECTWEEKLYREPORT_RISK)->where("weekreportID")->eq($application->id)->fetchAll();

                if(isset($users[$application->createdBy])) $application->createdBy = $users[$application->createdBy];
                $application->createDate = substr($application->createTime, 0, 10);
                //介质信息整理
                $application->productPlan = '';
                $application->productPlanPublishTime = '';
                $application->productPlanOnlineTime = '';
                $application->realMediumPublishDate = '';
                $application->realMediumOnlineDate = '';
                $application->mediumRequirement = '';
                $application->mediumMark = '';
                $application->mediumOutsideplanTask = '';
                $ii = 0;
                $requirements = $this->weeklyreport->getProjectRequirementsNew($application->projectId);
                $productRequirement = [];
                foreach ($requirements as $requirement){
                    $productRequirement[$requirement->id] = $requirement->code.":".$requirement->name;
                }
                $mediumOutTask = [];
                $projectPlans = $this->dao->select('*')->from(TABLE_PROJECTPLAN)->where('project')->eq($application->projectId)->fetch();;
                $outsideProjectIdArray = explode(',', $projectPlans->outsideProject);
                foreach ($outsideProjectIdArray as $outsideProjectId){
                    if(empty($outsideProjectId)) continue;
                    $subprojectsTask = $this->loadModel('outsideplan')->getTaskByOutsideplanID($outsideProjectId);
                    foreach ($subprojectsTask as $task){
                        $mediumOutTask[$task->id] = $task->subTaskName;
                    }
                }
                foreach ($application->reportMedium as $reportMedium){
                    $ii++;
                    $application->productPlan .= "[$ii]".$reportMedium->mediumName.PHP_EOL;
                    $application->productPlanPublishTime .= "[$ii]".$reportMedium->preMediumPublishDate.PHP_EOL;
                    $application->productPlanOnlineTime .= "[$ii]".$reportMedium->preMediumOnlineDate.PHP_EOL;
                    $application->realMediumPublishDate .= "[$ii]".$reportMedium->realMediumPublishDate.PHP_EOL;
                    $application->realMediumOnlineDate .= "[$ii]".$reportMedium->realMediumOnlineDate.PHP_EOL;
                    $showMediumRequirementArr = explode(',',$reportMedium->mediumRequirement);
                    $application->mediumRequirement .= "[$ii]";
                    foreach ($showMediumRequirementArr as $mediumReuirement){
                        if(!$mediumReuirement){
                            continue;
                        }
                        $application->mediumRequirement .= zget($productRequirement,$mediumReuirement).',';
                    }
                    $application->mediumRequirement = trim($application->mediumRequirement, ',').PHP_EOL;
                    $application->mediumMark .= "[$ii]".$reportMedium->mediumMark.PHP_EOL;
                    $application->mediumOutsideplanTask .= "[$ii]".$mediumOutTask[$reportMedium->mediumOutsideplanTask].PHP_EOL;
                }
                $application->reportDesc = strip_tags(br2nl(html_entity_decode($application->reportDesc)));
                $application->transDesc = strip_tags(br2nl(html_entity_decode($application->transDesc)));
                $application->insideMilestone = strip_tags(br2nl(html_entity_decode($application->insideMilestone)));
                $application->outsideMilestone = strip_tags(br2nl(html_entity_decode($application->outsideMilestone)));
                //项目阶段
                $progressStatusStr = '';
                $ii = 0;
                foreach(explode(',', $application->progressStatus) as $progressStatus){
                    $ii++;
                    $progressStatusStr .= "[$ii]" . $progressStatus . PHP_EOL;
                }
                $application->progressStatus =   $progressStatusStr;
                $application->projectProgress =  ($application->projectProgress - 0) . '%' ;
                //风险
                $application->riskName          = '';
                $application->riskResolution    = '';
                $application->riskStatus        = '';
                $ii = 0;
                foreach ($application->reportRisk as $risk) {
                    $ii++;
                    $application->riskName .= "[$ii]".$risk->reportRiskMark.PHP_EOL;
                    $application->riskResolution .= "[$ii]".'应对策略：'.$this->lang->risk->strategyList[$risk->reportRiskStrategy].'，预防措施：'.$risk->reportRiskPrevention
                        .'，应急措施：'.$risk->reportRiskRemedy.'，解决措施：'.$risk->reportRiskResolution.PHP_EOL;
                    $application->riskStatus .= "[$ii]".$this->lang->risk->statusList[$risk->reportRiskStatus].PHP_EOL;
                }
                //问题
                $application->issueName          = '';
                $application->issueResolution    = '';
                $application->issueStatus        = '';
                $issues = json_decode(base64_decode($application->issues),1);
                $ii = 0;
                foreach ($issues as $issue) {
                    $ii++;
                    $application->issueName .= "[$ii]".$issue['name'].PHP_EOL;
                    $application->issueResolution .= "[$ii]".$issue['resolution'].PHP_EOL;
                    $application->issueStatus .= "[$ii]".$issue['status'].PHP_EOL;
                }
                $demands = json_decode(base64_decode($application->productDemand),1);
                //产品需求
                $application->productDemand = '';
                $ii = 0;
                foreach ($demands['data'] as $demand) {
                    $ii++;
                    $application->productDemand .= "[$ii]".$demand['name'].PHP_EOL;
                }
                //需求条目
                $application->productRequirement = '';
                $ii = 0;
                foreach ($demands['requirement'] as $requirement) {
                    $ii++;
                    $application->productRequirement .= "[$ii]".$requirement['name'].PHP_EOL;
                }
                //内部状态
                $application->insideStatus = $this->lang->weeklyreport->projectState[$application->projectStage];
                //外部状态
                $application->outsideStatus = $outsideStatusList[$application->outsideStatus];
                //产品发版信息
                $application->productPublishDesc = $application->productBuilds;
                //项目类型
                $application->projectType = zget($this->lang->projectplan->typeList, $application->projectType, '');
                //承担部门
                $tempdeptDeptArray = array();
                $tempdeptDept = explode(',',$application->devDept);
                foreach ($tempdeptDept as $deptid){
                    if($deptid){
                        array_push($tempdeptDeptArray, zget($depts, $deptid, ''));
                    }
                }
                $application->devDept = trim(implode(",", $tempdeptDeptArray),',');
                //项目经理
                $application->pm =  zget($users, $application->pm, '');
                //项目进展描述
                $application->reportDesc = $application->projectProgressMark;
                //项目移交情况
                $application->transDesc = $application->projectTransDesc;
                //外部里程碑
                $application->outsideMilestonePhaseName = '';
                $application->outsideMilestoneName = '';
                $application->outsideMilestonePlanDate = '';
                $application->outsideMilestoneRealDate = '';
                $application->outsideMilestoneDesc = '';
                $ii = 0;
                foreach ($application->reportOutmile as $reportOutmile){
                    $ii++;
                    $application->outsideMilestonePhaseName .= "[$ii]".$reportOutmile->outMileStageName.PHP_EOL;
                    $application->outsideMilestoneName .= "[$ii]".$reportOutmile->outMileName.PHP_EOL;
                    $application->outsideMilestonePlanDate .= "[$ii]".$reportOutmile->outMilePreDate.PHP_EOL;
                    $application->outsideMilestoneRealDate .= "[$ii]".$reportOutmile->outMileRealDate.PHP_EOL;
                    $application->outsideMilestoneDesc .= "[$ii]".$reportOutmile->outMileMark.PHP_EOL;
                }
                //内部里程碑
                $application->insideMilestonePhaseName = '';
                $application->insideMilestoneName = '';
                $application->insideMilestonePlanDate = '';
                $application->insideMilestoneRealDate = '';
                $application->insideMilestoneDesc = '';
                $ii = 0;
                foreach ($application->reportInsidemile as $reportInsidemile){
                    $ii++;
                    $application->insideMilestonePhaseName .= "[$ii]".$reportInsidemile->insideMileStage.PHP_EOL;
                    $application->insideMilestoneName .= "[$ii]".$reportInsidemile->insideMileName.PHP_EOL;
                    $application->insideMilestonePlanDate .= "[$ii]".$reportInsidemile->insideMilePreDate.PHP_EOL;
                    $application->insideMilestoneRealDate .= "[$ii]".$reportInsidemile->insideMileRealDate.PHP_EOL;
                    $application->insideMilestoneDesc .= "[$ii]".$reportInsidemile->insideMileMark.PHP_EOL;
                }
                //是否重点项目
                $application->isImportant = zget($this->lang->projectplan->isImportantList, $application->isImportant, '');
                //(外部)项目/任务
                $projectPlan = $this->dao->select('*')->from(TABLE_PROJECTPLAN)->where('project')->eq($application->projectId)->fetch();
                if(!empty($projectPlan->code)){
                    $outsideProjectIdArray = explode(',', $projectPlan->outsideProject);
                    $outsidePlan = array();
                    foreach ($outsideProjectIdArray as $outsideProjectId){
                        if(empty($outsideProjectId)) continue;
                        $outsidePlanValue = $this->loadModel('outsideplan')->getByID($outsideProjectId);
                        if(!empty($outsidePlanValue)){
                            array_push($outsidePlan, $outsidePlanValue);
                        }
                    }
                    $application->outProjectCode = '';
                    $application->outProjectName = '';
                    $application->outProjectStatus = '';
                    $application->outProjectTask = '';
                    $ii = 0;
                    foreach($outsidePlan as $outplan)
                    {
                        $ii++;
                        $application->outProjectCode .= "[$ii]".$outplan->code.PHP_EOL;
                        $application->outProjectName .= "[$ii]".$outplan->name.PHP_EOL;
                        $application->outProjectStatus .= "[$ii]".zget($this->lang->outsideplan->statusList,$outplan->status,'').PHP_EOL;
                        $subprojectsTask = array();
                        $subprojectsTaskList = $this->loadModel('outsideplan')->getTaskByOutsideplanID($outplan->id);
                        foreach ($subprojectsTaskList as $task){
                            array_push($subprojectsTask, $task->subTaskName);
                        }

                        $application->outProjectTask .= "[$ii]".trim(implode(',',$subprojectsTask), ',').PHP_EOL;
                    }
                }
            }
            //拼接年度计划-立项审批通过但没有创建项目空间

            $this->dao->select("*")->from(TABLE_PROJECTPLAN)->where(" status in ('projected','pass','yearpass' ,'start','reviewing') ")->andWhere('deleted')->eq(0);
            $projectPlanList =  $this->dao->orderBy("id desc")->fetchAll();

            foreach ($projectPlanList as $key=>$projectplan){
                if($projectplan->status == 'projected'){
                    $project = $this->loadModel('project')->getByID($projectplan->project);
                    if(!$project){
                        unset($projectPlanList[$key]);
                        continue;
                    }
                    if($project->status == 'closed'){
                        unset($projectPlanList[$key]);
                    }
                }

            }
            $planProjectedList = [];
            $planPssList = [];
            foreach($projectPlanList as $planID => $plan)
            {
                if($plan->status == 'projected'){
                    $planProjectedList[] = $plan;
                }else{
                    $planPssList[] = $plan;
                }
            }

           foreach ($planPssList as $projectPlan){
               $application = new stdClass();
               //(外部)项目/任务
               $outsideProjectIdArray = explode(',', $projectPlan->outsideProject);

               $outsidePlan= array();
               foreach ($outsideProjectIdArray as $outsideProjectId){
                   if(empty($outsideProjectId)) continue;
                   $outsidePlanValue = $this->loadModel('outsideplan')->getByID($outsideProjectId);
                   if(!empty($outsidePlanValue)){
                       array_push($outsidePlan, $outsidePlanValue);
                   }
               }
               $application->outProjectCode = '';
               $application->outProjectName = '';
               $application->outProjectStatus = '';
               $application->outProjectTask = '';
               $ii = 0;
               foreach($outsidePlan as $outplan)
               {
                   $ii++;
                   $application->outProjectCode .= "[$ii]".$outplan->code.PHP_EOL;
                   $application->outProjectName .= "[$ii]".$outplan->name.PHP_EOL;
                   $application->outProjectStatus .= "[$ii]".zget($this->lang->outsideplan->statusList,$outplan->status,'').PHP_EOL;
                   $subprojectsTask = array();
                   $subprojectsTaskList = $this->loadModel('outsideplan')->getTaskByOutsideplanID($outplan->id);
                   foreach ($subprojectsTaskList as $task){
                       array_push($subprojectsTask, $task->subTaskName);
                   }

                   $application->outProjectTask .= "[$ii]".trim(implode(',',$subprojectsTask), ',').PHP_EOL;
               }

               //项目类型
               $application->projectType = zget($this->lang->projectplan->typeList, $projectPlan->type, '');
               //项目编号
               $application->projectCode = $projectPlan->code;
               //项目名称
               $application->projectName = $projectPlan->name;
               //项目代号
               $application->projectAlias = $projectPlan->mark;
               //开始时间
               $application->projectStartDate = $projectPlan->begin;
               //结束时间
               $application->projectEndDate = $projectPlan->end;
               //是否重要
               $application->isImportant = zget($this->lang->projectplan->isImportantList, $projectPlan->isImportant, '');
               //年份
               $application->projectplanYear = $projectPlan->year;
               //承建部门
               $tempdeptDeptArray = array();
               $tempdeptDept = explode(',',$projectPlan->bearDept);
               foreach ($tempdeptDept as $deptid){
                   if($deptid){
                       array_push($tempdeptDeptArray, zget($depts, $deptid, ''));
                   }
               }
               $application->devDept = trim(implode(",", $tempdeptDeptArray),',');
               //pm
               $userArray = array();
               $userList = explode(',',$projectPlan->owner);
               foreach ($userList as $user){
                   if($user){
                       array_push($userArray, zget($users, $user, ''));
                   }
               }
               $application->pm = trim(implode(",", $userArray),',');

               array_push($applications, $application);
           }




            $this->post->set('fields', $fields);
            $this->post->set('rows', $applications);
            $this->post->set('kind', '项目周报');
            if($_POST['weeknum'] != ''){
                $this->post->set('fileName', '内部项目周报-第'.$_POST['weeknum'].'周');
            }else{
                $this->post->set('fileName', '内部项目周报-全部');
            }

            $this->fetch('file', 'exportreport2' . $this->post->fileType, $_POST); //module/file/ext/control/exportreport2xlsx.php
        }
        if($_POST && $_POST['fileType'] != 'xlsx'){
            echo js::alert('只支持选择xlsx导出');
        }

        $this->view->fileName        = "内部项目周报";
        $this->view->allExportFields = $this->config->weeklyreport->list->exportFields;
        $this->view->customExport    = true;
        $this->view->reportId        = $reportId;
        $weeknums = $this->dao->select('weeknum')->from(TABLE_PROJECTWEEKLYREPORT)
            ->where('deleted')->ne('1')
            ->groupBy('weeknum')
            ->orderBy('weeknum')
            ->fetchAll('weeknum');
        $weekPair = array(''=>'全部');
        foreach ($weeknums as $key=>$value){
            $weekPair[$key] = '第'.$key.'周';
        }
        $this->view->weeknumList        = $weekPair;
        $this->display();
    }

    /**
     * 确认周报
     * @return void
     */
    public function confirm()
    {
        if ($_POST) {
            $res = $this->weeklyreportin->confirm($this->post->weekNum);

            if (dao::isError()) {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $this->weeklyreportin->sendmail();
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }
        $data = $this->dao
            ->select('distinct weeknum')
            ->from(TABLE_PROJECTWEEKLYREPORT)
            ->where('deleted')->eq(0)
//            ->groupBy('weeknum')
            ->fetchAll();

        $data = array_column($data, 'weeknum');

        rsort($data);

        $this->view->weekNumList = array_combine($data, $data);
        $this->view->users       = $this->loadModel('user')->getPairs('noletter|noclosed');



        $this->display();
    }
}
