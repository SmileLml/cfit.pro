<?php

/**
 * Created by Yanqi Tong
 */

class weeklyreport extends control
{
    public function index($projectID = 0, $reportId = 0,$refer='')
    {
        $this->loadModel('project');
        $this->loadModel('risk');
        $this->project->setMenu($projectID);

        $project = $this->project->getByID($projectID);
        if(empty($project) || $project->type != 'project') die(js::error($this->lang->notFound) . js::locate('back'));

        $this->view->project    = $project;
        $this->view->title      = $this->lang->weeklyreport->view;
        if($reportId){ //取指定id
            $report = $this->weeklyreport->getByID($reportId);
        } else { //取当前日期内周报
            $report = $this->weeklyreport->getByLastProjectId($projectID);
        }

        if(empty($report)){
            $thisWeek = $this->weeklyreport->getWeekMyActionAndEnd();
            //设置标题 第几周的切换
            $this->lang->modulePageNav     = $this->weeklyreport->getPageNavByProject($project, $thisWeek['week_start'], $thisWeek['week_end']);
            $this->view->extraIcons =   html::a($this->createLink("weeklyreport", "create", "project=$projectID"), "<i class='icon-plus'></i> 创建周报", '', "class='btn btn-primary' style='margin-top:-7px;'");
            $this->display();
            die();
        }

        $this->view->report                 = $report;
        $this->loadModel('project')->setMenu($report->projectId);
//        $this->view->relations              = json_decode(html_entity_decode($report->productPlan));
//        $this->view->reportDesc             = html_entity_decode($report->reportDesc);
        $this->view->requirements = $this->weeklyreport->getProjectRequirementsNew($projectID);

        $this->view->users                  = $this->loadModel('user')->getPairs('noletter');
        $this->lang->modulePageNav          = $this->weeklyreport->getPageNavByProject($project, $report->reportStartDate, $report->reportEndDate,$report->weeknum);
        $this->view->extraIcons =  common::hasPriv('weeklyreport', 'create') ? html::a($this->createLink("weeklyreport", "templetecreate", "reportId=$report->id"), "<i title ='创建周报' class='icon-plus'></i> ", '', "class='btn btn-action'") : '';
        $this->view->extraIcons .= common::hasPriv('weeklyreport', 'edit') ? html::a($this->createLink("weeklyreport", "edit", "reportId=$report->id"), "<i title ='编辑周报' class='icon-edit'></i> ", '', "class='btn btn-action'"): '';
//        $this->view->extraIcons .= common::hasPriv('weeklyreport', 'create') ? html::a($this->createLink("weeklyreport", "copy", "reportId=$report->id"), "<i title ='复制周报' class='icon-copy'></i> ", '', "class='btn btn-action'"): '';
        //$this->createLink("weeklyreport", "delete", "reportId=$report->id")
        $this->view->extraIcons .= common::hasPriv('weeklyreport', 'delete')?  html::a("javascript:void(0)", "<i title ='删除周报' class='icon-trash'></i> ", '', "class='btn btn-action' onclick=deleteReport($report->id)", '', true): '';

        $this->view->extraIcons .= common::hasPriv('weeklyreport', 'export')?  html::a($this->createLink("weeklyreport", "export", "orderBy=id_desc&browseType=all&projectId=$project->id&reportId=$report->id&startDate=".strtotime($report->reportStartDate)."&endDate=".strtotime($report->reportEndDate)), "<i title ='导出周报' class='icon-export'></i> ", 'hiddenwin', "data-toggle='modal' data-type='iframe'  class='btn btn-action'"): '';
        $this->view->projectPlan = $this->loadModel('projectPlan')->getByProjectID($report->projectId);


        $outsideProjectIdArray = explode(',', $this->view->projectPlan->outsideProject);
        $this->view->outsidePlan = [];
        $outkey = 0;
        $this->view->mediumOutTask = [];
        foreach ($outsideProjectIdArray as $outsideProjectId){
            if(empty($outsideProjectId)) continue;
            $this->view->outsidePlan[$outkey] = $this->loadModel('outsideplan')->getSimpleByID($outsideProjectId);
            $this->view->outsidePlan[$outkey]->subprojectsTask = $this->loadModel('outsideplan')->getTaskByOutsideplanID($outsideProjectId);
            foreach ($this->view->outsidePlan[$outkey]->subprojectsTask as $task){

                $this->view->mediumOutTask[$task->id] = $task->subTaskName;
            }
            $outkey++;
        }

        if($refer){
            $this->view->referjump = 'top';
            $this->view->refershow = true;
            $this->view->refer = helper::createLink('weeklyreportin','browse','','html');
            $this->view->referhtml = html::a($this->view->refer, '<i class="icon-goback icon-back"></i> ' . $this->lang->goback, '', "id='back' data-app='platform' class='btn' title='{$this->lang->goback}{$this->lang->backShortcutKey}'");
        }else{
            $this->view->referjump = 'self';
            $this->view->refershow = false;
            if($reportId){
                $this->view->refer = helper::createLink('weeklyreport','index','projectID='.$report->projectId.'&reportId='.$report->id ,'html#app=project');
            }else{
                $this->view->refer = helper::createLink('weeklyreport','index','projectID='.$report->projectId ,'html#app=project');

            }
            $this->view->referhtml = "";
        }

        $this->view->mediumOutTask = [''=>'']+$this->view->mediumOutTask + ['其他'=>'其他'];

        $this->view->demands     = empty($this->view->projectPlan->id) ? null : $this->weeklyreport->getProjectDemands($this->view->projectPlan->id);
        $this->view->statusSelects = $this->weeklyreport->getSelects();


        $this->view->depts    = $this->loadModel('dept')->getOptionMenu();



        $this->view->outreportList = $this->loadModel('weeklyreportout')->getOutreportList($report->id);


        $requirementStr = '';
        $productRequirement = [];
        $requirementIdStr = '';
        foreach ($this->view->requirements as $requirement){
            $requirementStr .= $requirement->code.":".$requirement->name.PHP_EOL;
            $productRequirement[$requirement->id] = $requirement->code.":".$requirement->name;
            $requirementIdStr .= $requirement->id.',';
        }
        if($requirementIdStr){
            $requirementIdStr = ','.trim($requirementIdStr,',').',';
        }

        $this->view->requirementIdStr = $requirementIdStr;
        $this->view->requirementStr = $requirementStr;
        $this->view->productRequirement = $productRequirement;


        $this->view->actions  = $this->loadModel('action')->getList('weeklyreport', $report->id);
        $this->display();
    }


    /**
     * Create a report.
     * 创建周报
     * @access public
     * @return void
     */
    public function create($projectID)
    {
        if($_POST)
        {
            $this->postCheck();
            $check = $this->weeklyreport->checkDate($projectID, $_POST['reportStartDate'], $_POST['reportEndDate']);
            if($check == false){
                $response['result']  = 'fail';
                $response['message'] = dao::$errors['reportDateUnavailable'];
                $this->send($response);
            }
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
            $this->send($response);
        }
        $this->loadModel('project')->setMenu($projectID);
        $this->view->title = $this->lang->weeklyreport->create;
        $this->view->projectID = $projectID;
        $this->view->projectPlan = $this->loadModel('projectPlan')->getByProjectID($projectID);
        if(empty($this->view->projectPlan->code)){
            $this->toSelfError('无相关年度计划信息', $projectID);
        }
        $outsideProjectIdArray = explode(',', $this->view->projectPlan->outsideProject);

        foreach ($outsideProjectIdArray as $outsideProjectId){
            if(empty($outsideProjectId)) continue;
            $this->view->outsidePlan[] = $this->loadModel('outsideplan')->getByID($outsideProjectId);
        }
        $outsidePlans = [];
        foreach ( $this->view->outsidePlan as $outplan)
        {
            $outsidePlan['code'] = $outplan->code;
            $outsidePlan['name'] = $outplan->name;
            $outsidePlan['begin'] = $outplan->begin;
            $outsidePlan['end']  = $outplan->end;
            $outsidePlan['workload'] = $outplan->workload;
            $outsidePlans[] = $outsidePlan;
        }
        $this->view->outsidePlan =  base64_encode(json_encode($outsidePlans));

        $this->view->projectPlanRelation    = $this->getProjectPlanRelation($projectID);
        $this->view->week                   = $this->weeklyreport->getWeekMyActionAndEnd();
        $this->view->statusSelects          = $this->weeklyreport->getSelects();
        $this->view->risks                  = $this->weeklyreport->getProjectRisks($projectID);
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
        $this->view->project->progress      = $this->weeklyreport->getProgress($projectID);
        $this->view->depts                  = $this->loadModel('dept')->getOptionMenu();
        $this->view->creation               = $this->loadModel('projectPlan')->getCreationByID($this->view->projectPlan->id);

        //取项目需求和需求条目
        $this->view->demands = $this->weeklyreport->getDemandInfo($projectID, $this->view->projectPlan->id);
        $this->view->stages  = $this->weeklyreport->getStages($projectID);

        $this->display();
    }

    public function toSelfError($msg,$projectID,$url='')
    {
        $response['result']  = 'fail';
        $response['message'] = $msg;
        if($url){
            $response['locate']  =  $url;
        }else{
            $response['locate']  =  'weeklyreport-index-' . $projectID .'.html#app=project';
        }

        $this->sendBack($response, 'self');
    }
    /**
     * 获取项目产品版本关联
     * @param $projectID
     * @return string[]
     */
    public function getProjectPlanRelation($projectID)
    {
        $projectPlanRelationInfo = $this->loadModel('project')->getProjectRelations($projectID);
        $projectPlanRelation = [''];
        foreach( $projectPlanRelationInfo as $item)
        {
            $projectPlanRelation[$item] = $item;
        }
        return $projectPlanRelation;
    }
    /**
     * Edit a report
     * 编辑周报
     * @param  int $reportId
     * @access public
     * @return void
     */
    public function edit($reportId)
    {
        $this->view->title = $this->lang->weeklyreport->edit;
        $report = $this->weeklyreport->getByID($reportId);
        if($_POST)
        {
            $this->postCheck();
            $check = $this->weeklyreport->checkDate($report->projectId, $_POST['reportStartDate'], $_POST['reportEndDate'], $reportId);
            if($check == false){
                $response['result']  = 'fail';
                $response['message'] = '您已创建过该日期内的周报';
                $this->send($response);
            }
            $changes = $this->weeklyreport->update($reportId);
            if(dao::$errors)
            {
                $response['result']  = 'fail';
                $response['message'] = dao::$errors;
                $this->send($response);
            }
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'weeklyreport-index-' . $report->projectId .'.html#app=project';
            $this->send($response);
        }
        $this->loadModel('project')->setMenu($report->projectId);
        $this->view->report         = $report;
        $this->view->relations      = json_decode(html_entity_decode($report->productPlan));
        $this->view->reportId       = $reportId;
        $this->view->projectPlanRelation = $this->getProjectPlanRelation($report->projectId);
        $this->view->statusSelects  = $this->weeklyreport->getSelects();
        $this->view->stages         = $this->weeklyreport->getStages($report->projectId);
        $this->display();
    }

    /**
     * 复制周报内容并新建
     */
    public function copy($reportId)
    {
        $this->view->title = $this->lang->weeklyreport->copy;
        $report = $this->weeklyreport->getByID($reportId);
        $projectID = $report->projectId;
        if($_POST)
        {
            $this->weeklyreport->create();
            if(dao::$errors)
            {
                $response['result']  = 'fail';
                $response['message'] = dao::$errors;
                $this->send($response);
            }
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'weeklyreport-index-' . $projectID .'.html#app=project';
            $this->sendBack($response);
        }
        $this->loadModel('project')->setMenu($projectID);
        $this->view->title = $this->lang->weeklyreport->create;
        $this->view->projectID = $projectID;
        $this->view->projectPlan = $this->loadModel('projectPlan')->getByProjectID($projectID);
        $this->view->projectPlanRelation = $this->getProjectPlanRelation($projectID);
        $this->view->week = $this->weeklyreport->getWeekMyActionAndEnd();
        $this->view->statusSelects   = $this->weeklyreport->getSelects();
        $this->view->project   = $this->loadModel('project')->getByID($projectID);
        $this->view->relations = json_decode(html_entity_decode($report->productPlan));
        $this->view->reportId    = $reportId;
        $this->view->report      = $report;
        $this->view->stages = $this->weeklyreport->getStages($report->projectId);
        $this->display();
    }


    /**
     * Delete report.
     *
     * @param  int    $appID
     * @param  string $confirm    yes|no
     * @access public
     * @return void
     */
    public function delete()
    {

        $this->viewType = 'json';

        $appID = (int)$_POST['reportId'];
        if(!$appID){
            $this->send(['code'=>0,'message'=>"周报参数错误"]);
        }
        $report = $this->weeklyreport->getByID($appID);
        $isQA = $this->weeklyreport->authUserReportQA($this->app->user->account,$report->devDept);
        if(!$isQA){
            $this->send(['code'=>0,'message'=>"您不是QA！不能删除周报"]);
            /*echo js::alert("您不是QA！不能删除周报");
            die(js::locate('back'));*/

        }
        $this->weeklyreport->reportdelete($appID);
        $this->session->set('weeklyreport', '');
        $this->send(['code'=>200,'message'=>"成功"]);
//            header("Location:".getWebRoot()."weeklyreport-index-$report->projectId.html#app=project");
        die();
        /*if($confirm == 'no')
        {
            $isQA = $this->weeklyreport->getUserQADept($this->app->user->account);

            if($isQA['isogQA'] != 1 && !in_array($this->app->user->dept,$isQA['depts'])){

                echo js::alert("您不是QA！不能删除周报");
                die(js::locate('back'));

            }
            die(js::confirm($this->lang->weeklyreport->confirmDelete, $this->createLink('weeklyreport', 'delete', "appID=$appID&confirm=yes")));
        }
        else
        {
            $isQA = $this->weeklyreport->getUserQADept($this->app->user->account);

            if($isQA['isogQA'] != 1 && !in_array($this->app->user->dept,$isQA['depts'])){
                $this->send(['code'=>0,'message'=>"您不是QA！不能删除周报"]);
                echo js::alert("您不是QA！不能删除周报");
                die(js::locate('back'));

            }
            $report = $this->weeklyreport->getByID($appID);
            $this->weeklyreport->reportdelete($appID);
            $this->session->set('weeklyreport', '');
            $this->send(['code'=>200,'message'=>"成功"]);
//            header("Location:".getWebRoot()."weeklyreport-index-$report->projectId.html#app=project");
            die();
        }*/
    }



    /**
     * 导出excel
     * @param string $orderBy
     * @param string $browseType
     */
    public function export($orderBy = 'id_desc', $browseType = 'all', $projectId = 0, $reportId = 0, $startDate  = '', $endDate  = '')
    {

        $this->loadModel('project');
        if(!empty($projectId)) $project = $this->project->getByID($projectId);
        if($_POST && $_POST['fileType'] == 'xlsx')
        {
            $this->loadModel('file');
            $this->loadModel('projectplan');
            $this->loadModel('outsideplan');
            $this->loadModel('risk');
            $applicationLang   = $this->lang->weeklyreport;
            $applicationConfig = $this->config->weeklyreport;

            /* Create field lists. */
            $fields = $this->post->exportFields ? $this->post->exportFields : explode(',', $applicationConfig->list->exportFields);
            foreach($fields as $key => $fieldName)
            {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = isset($applicationLang->$fieldName) ? $applicationLang->$fieldName : $fieldName;
                unset($fields[$key]);
            }

            if($reportId) { //详细页面导出1条
                $applications = $this->dao->select('t1.*, t2.status')->from(TABLE_PROJECTWEEKLYREPORT)->alias('t1')
                    ->leftJoin(TABLE_PROJECT)->alias('t2')->on('t1.projectId=t2.id')
                    ->where(' t1.deleted = 0 ')
                    ->andwhere(' t2.status <> "closed" ')
                    ->andWhere('t1.id')->eq($reportId)
                    ->fetchAll('id');
            } else { //项目列表页 收索条件
                $idArr = [];
                if($this->session->projectQuery)
                {
                    $this->app->loadClass('pager', $static = true);
                    $pager = new pager(0, 200, 1);
                    $programTitle = $this->loadModel('setting')->getItem('owner=' . $this->app->user->account . '&module=project&key=programTitle');
                    $projectStats = $this->project->getProjectStats(0, 'bysearch', 0, 'id_desc', $pager, $programTitle);
                    if(!empty($projectStats)){
                        foreach ($projectStats as $item)
                        {
                            $idArr[] = $item->id;
                        }
                    }
                }
                $applications = $this->dao->select('t1.*, t2.status')->from(TABLE_PROJECTWEEKLYREPORT)->alias('t1')
                    ->leftJoin(TABLE_PROJECT)->alias('t2')->on('t1.projectId=t2.id')
                    ->where(' t1.deleted = 0 ')
                    ->andwhere(' t2.status <> "closed" ')
                    ->beginIF($this->post->exportType == 'selected')->andWhere('t1.projectId')->in($this->cookie->checkedItem)->fi()
                    ->beginIF(!empty($idArr))->andWhere('t1.projectId')->in($idArr)->fi()
                    ->orderBy('projectId desc, reportEndDate desc')
                    ->limit(200)
                    ->fetchAll('id');
            }

            /* Get users, products and executions. */
            $users = $this->loadModel('user')->getPairs('noletter');
            $statusSelects   = $this->weeklyreport->getSelects();
            $insideStatusList  = $statusSelects['insideReportStatusList'];
            $outsideStatusList = $statusSelects['outsideReportStatusList'];
            $depts                  = $this->loadModel('dept')->getOptionMenu();
            foreach($applications as  $application)
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

            $this->post->set('fields', $fields);
            $this->post->set('rows', $applications);
            $this->post->set('kind', '项目周报');
            $this->fetch('file', 'exportreport2' . $this->post->fileType, $_POST); //module/file/ext/control/exportreport2xlsx.php
        }
        if($_POST && $_POST['fileType'] != 'xlsx'){
            echo js::alert('只支持选择xlsx导出');
        }

        $this->view->fileName        = isset($project->name) ? $project->name . $this->lang->weeklyreport->exportName. '['.date('Y-m-d',$startDate).'~'.date('Y-m-d',$endDate).']': "周报列表";
        $this->view->allExportFields = $this->config->weeklyreport->list->exportFields;
        $this->view->customExport    = true;
        $this->view->reportId        = $reportId;
        $this->display();
    }

    /**
     * 校验post必填项
     * 错误提示
     */
    private function postCheck()
    {
        if(!$this->post->reportStartDate)
        {
            dao::$errors['reportStartDate'] = [$this->config->weeklyreport->reportStartDateEmpty];
            $response['result']  = 'fail';
            $response['message'] = dao::$errors;
            $this->send($response);
        }
        if(!$this->post->reportEndDate)
        {
            dao::$errors['reportEndDate'] = [$this->config->weeklyreport->reportEndDateEmpty];
            $response['result']  = 'fail';
            $response['message'] = dao::$errors;
            $this->send($response);
        }
        if($this->post->reportStartDate > $this->post->reportEndDate){
            dao::$errors['reportEndDate'] = [$this->config->weeklyreport->reportDateillegal];
            $response['result']  = 'fail';
            $response['message'] = dao::$errors;
            $this->send($response);
        }
        if(empty($_POST['progressStatus'][0])){
            dao::$errors['progressStatus'] = [$this->config->weeklyreport->progressStatusEmpty]; ;
            $response['result']  = 'fail';
            $response['message'] = dao::$errors;
            $this->send($response);
            die();
        }
        if(!$this->post->insideStatus)
        {
            dao::$errors['insideStatus'] = [$this->config->weeklyreport->insideStatusEmpty];
            $response['result']  = 'fail';
            $response['message'] = dao::$errors;
            $this->send($response);
        }
        if(!$this->post->outsideStatus)
        {
            dao::$errors['outsideStatus'] = [$this->config->weeklyreport->outsideStatusEmpty];
            $response['result']  = 'fail';
            $response['message'] = dao::$errors;
            $this->send($response);
        }

        $i = 0;
        $planCodeArr = [];
        foreach ($_POST['productPlanCode'] as $item){

            if(empty($_POST['productPlanCode'][$i])
                || empty($_POST['preRelease'][$i])
                || empty($_POST['preOnline'][$i])
                || baseValidater::checkDate($_POST['preRelease'][$i]) == false
                || baseValidater::checkDate($_POST['preOnline'][$i]) == false
                || baseValidater::checkDate($_POST['realRelease'][$i]) == false
                || baseValidater::checkDate($_POST['realOnline'][$i]) == false)
            {
                $i++;
                $response['result']  = 'fail';
                $response['message'] = sprintf($this->config->weeklyreport->productPlanillegal,$i);
                $this->send($response);
                die();
            }
            //制品名称重复填写提示
            if(in_array($item, $planCodeArr))
            {
                $response['result']  = 'fail';
                $response['message'] = sprintf($this->config->weeklyreport->planCodeDuplicated, $item);
                $this->send($response);
                die();
            }
            $planCodeArr[] = $item;
            $i++;
        }

    }

}
