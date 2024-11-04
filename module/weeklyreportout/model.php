<?php
class weeklyreportoutModel extends model
{

    public function getList($browseType, $pager, $orderBy = 'id_desc'){
        $projectweeklyreports = $this->dao->select('*')->from(TABLE_OUTSIDEPROJECTWEEKLYREPORT)
            ->where('deleted')->eq('0')
            ->beginIF($browseType != 'all')->andWhere('outprojectStatus')->eq($browseType)->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');
        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'weeklyreportout', $browseType != 'bysearch');

        return $projectweeklyreports;
    }

    public function getByID($outreportID,$isdecode = 1)
    {
        $report = $this->dao->select('*')->from(TABLE_OUTSIDEPROJECTWEEKLYREPORT)
            ->where('id')->eq($outreportID)
            ->andwhere('deleted')->eq(0)
            ->fetch();
        if($report && $isdecode == 1){
            $report->outmediuListInfo = json_decode(base64_decode($report->outmediuListInfo));
            $report->outmileListInfo = json_decode(base64_decode($report->outmileListInfo));
            $report->outriskListInfo = json_decode(base64_decode($report->outriskListInfo));
            $report->innerReportBaseInfo = json_decode(base64_decode($report->innerReportBaseInfo));

        }
        return $report;
    }

    public function update($outreportID){

        $outreport = $this->getByID($outreportID,0);


        $data["outProjectID"] = $_POST['outProjectID'];
        $data["outsideProjectName"] = $_POST['outsideProjectName'];
        $data["outsideProjectCode"] = $_POST['outsideProjectCode'];
        $data["outsideProjectSubProject"] = $_POST['outsideProjectSubProject'];
        $data["relationInsideProject"] = $_POST['relationInsideProject'];
        $data["outweeknum"] = $_POST['outweeknum'];
//        $data["outreportStartDate"] = $_POST['outreportStartDate'];
//        $data["outreportEndDate"] = $_POST['outreportEndDate'];
        $data["outprojectStatus"] = $_POST['outprojectStatus'];

        $data["outOverallProgress"] = str_replace("\r\n",PHP_EOL,$_POST['outOverallProgress']);
        $data["outProjectTransferMark"] = str_replace("\r\n",PHP_EOL,$_POST['outProjectTransferMark']);
        $data["outProjectAbnormal"] = str_replace("\r\n",PHP_EOL,$_POST['outProjectAbnormal']);
        $data["outNextWeekplan"] = str_replace("\r\n",PHP_EOL,$_POST['outNextWeekplan']);


        $data["outFeedbackTime"] = $_POST['outFeedbackTime'];
        $data["outFeedbackUser"] = $_POST['outFeedbackUser'];
        $data["outFeedbackView"] = $_POST['outFeedbackView'];
        $data["outOperatingRemarks"] = $_POST['outOperatingRemarks'];


        $data["editedBy"] = $this->app->user->account;
        $data["updateTime"] = helper::now();

        //介质信息
        $mediumInfoList = [];
        foreach ($_POST['outMediumName'] as $key=>$outMediumName){
            if(!$outMediumName){
                continue;
            }
            $mediumInfoList[$key]['outMediumName']    = $outMediumName;
            $mediumInfoList[$key]['outPreMediumPublishDate']         = $_POST['outPreMediumPublishDate'][$key];
            $mediumInfoList[$key]['outPreMediumOnlineDate']         = $_POST['outPreMediumOnlineDate'][$key];
            $mediumInfoList[$key]['outRealMediumPublishDate']         = $_POST['outRealMediumPublishDate'][$key];
            $mediumInfoList[$key]['outRealMediumOnlineDate']         = $_POST['outRealMediumOnlineDate'][$key];
            $mediumInfoList[$key]['outMediumOutsideplanSub']         = $_POST['outMediumOutsideplanSub'][$key];

            if($_POST['outMediumRequirement'][$key]){

                $mediumInfoList[$key]['outMediumRequirement']         = str_replace("\r\n",PHP_EOL,$_POST['outMediumRequirement'][$key]);
            }else{
                $mediumInfoList[$key]['outMediumRequirement']         = $_POST['outMediumRequirement'][$key];
            }


        }

        //里程碑
        $outMileInfoList = [];
        foreach ($_POST['outMileStageName'] as $key=>$outMileStageName){
            if(!$outMileStageName){
                continue;
            }
            $outMileInfoList[$key]['outMileProductManual']         = $_POST['outMileProductManual'][$key];
            $outMileInfoList[$key]['outMileTechnicalProposal']         = $_POST['outMileTechnicalProposal'][$key];
            $outMileInfoList[$key]['outMileDeploymentPlan']         = $_POST['outMileDeploymentPlan'][$key];

            $outMileInfoList[$key]['outMileUATTest']         = $_POST['outMileUATTest'][$key];
            $outMileInfoList[$key]['outMileProductReg']         = $_POST['outMileProductReg'][$key];
            $outMileInfoList[$key]['outMileAutoScript']         = $_POST['outMileAutoScript'][$key];
            $outMileInfoList[$key]['outMileStageName']    = $outMileStageName;

            ksort($outMileInfoList[$key]);


        }

        $data['outmileListInfo'] = base64_encode(json_encode($outMileInfoList));
        $data['outmediuListInfo'] = base64_encode(json_encode($mediumInfoList));

        //写入
        $this->dao->update(TABLE_OUTSIDEPROJECTWEEKLYREPORT)->data($data)->where("id")->eq($outreportID)->exec();
        $newOutreport = $this->getByID($outreportID,0);

        return common::createNewChanges($outreport, $newOutreport);



    }

    public function getAllQA(){
        $deptlist = $this->dao->select("id,qa")->from(TABLE_DEPT)->fetchAll("id");
        foreach ($deptlist as $key=>$dept){

            if($dept->qa){
                $deptlist[$key] = explode(",",$dept->qa);
            }else{
                $deptlist[$key] = [];
            }
        }
        return $deptlist;
    }

    public function getOutreportList($reportID,$field="*"){
        return $this->dao->select($field)->from(TABLE_OUTSIDEPROJECTWEEKLYREPORT)->where("FIND_IN_SET($reportID,`innerReportId`)")->fetchAll();
    }

    public function getOutreportByOutProject($outprojectID,$field="*"){

        return $this->dao->select($field)->from(TABLE_OUTSIDEPROJECTWEEKLYREPORT)->where("outProjectID")->eq($outprojectID)->orderBy("outweeknum desc")->fetch();
    }
    public function pushWeeklyreportQingZong(){
        $outweeknum = (int)$_POST['outweeknum'];
        if(!$outweeknum){
            dao::$errors['outweeknum'] = $this->lang->weeklyreportout->outweeknumError;
            return false;
        }

        //外部周报id
        $outreportID = isset($_POST["outreportId"]) ? (int)$_POST["outreportId"] : '';
        $this->dao->select("id")->from(TABLE_OUTSIDEPROJECTWEEKLYREPORT)->where('outweeknum')->eq($outweeknum)->andWhere("iscbp")->eq(1)->andWhere("outSyncStatus")->in([0,1,2]);

        if($outreportID){
            $this->dao->andWhere("id")->eq($outreportID);
        }
        $outWeeklyreport = $this->dao->fetchAll();

        $outWeeklyreportIDArr = array_column($outWeeklyreport,'id');
        if($outWeeklyreportIDArr){
            $updata = [
                'outSyncStatus'=>3
            ];

            $this->dao->update(TABLE_OUTSIDEPROJECTWEEKLYREPORT)->data($updata)->where('id')->in($outWeeklyreportIDArr)->exec();
        }

        if(dao::$errors)
        {
            return false;
        }

        return $outWeeklyreportIDArr;

    }

    public function pushOneWeeklyreportQingZong(){

        //外部周报id
        $outreportID = isset($_POST["outreportId"]) ? (int)$_POST["outreportId"] : '';
        if(!$outreportID){
            return ['code'=>0,'message'=>$this->lang->weeklyreportout->outreportIDError];
        }
        $outreport = $this->dao->select("id")->from(TABLE_OUTSIDEPROJECTWEEKLYREPORT)->where('id')->eq($outreportID)->fetch();
        if(!$outreport){
            return ['code'=>0,'message'=>$this->lang->weeklyreportout->outreportNoexistError];
        }


        $updata = [
            'outSyncStatus'=>3
        ];

        $this->dao->update(TABLE_OUTSIDEPROJECTWEEKLYREPORT)->data($updata)->where('id')->eq($outreport->id)->exec();


        if(dao::isError())
        {
            return ['code'=>0,'message'=>dao::getError()];
        }

        return ['code'=>200,'message'=>$this->lang->success,'data'=>$outreport];

    }


    /**
     * @param $outreportID 外部周报id
     * @return void
     */
    public function regeneration($outreportID){
        if(!$outreportID){
            dao::$errors[] = $this->lang->weeklyreportout->outreportIDError;
            return false;
        }
        $outReport = $this->dao->select("id,outProjectID,outweeknum,outreportStartDate,outreportEndDate")->from(TABLE_OUTSIDEPROJECTWEEKLYREPORT)->where("id")->eq($outreportID)->fetch();
        if(!$outReport){
            dao::$errors[] = $this->lang->weeklyreportout->outreportNoexistError;
            return false;
        }
        $outplanInfo = $this->dao->select("*")->from(TABLE_OUTSIDEPLAN)->where("id")->eq($outReport->outProjectID)->fetch();
        if(!$outplanInfo){
            dao::$errors[] = $this->lang->weeklyreportout->outProjectNoexistError;
            return false;
        }
        return $this->actionGenerateOutReport($outplanInfo,$outReport->outweeknum,$outReport->outreportStartDate,$outReport->outreportEndDate,$outreportID);

    }

    //生成 定时任务使用
    public function generateOutReport($outplanInfo,$weeknum,$starttime,$endtime,$user=''){

        if(!$outplanInfo){
            return ['code'=>0,'message'=>$this->lang->weeklyreportout->outProjectNoexistError];
        }
        $outprojectReport = $this->dao->select("id")->from(TABLE_OUTSIDEPROJECTWEEKLYREPORT)->where("outweeknum")->eq($weeknum)->andWhere("outProjectID")->eq($outplanInfo->id)->fetch();

        if($outprojectReport){

            return ['code'=>0,'message'=>$this->lang->weeklyreportout->outreportExistError];
        }


        return $this->actionGenerateOutReport($outplanInfo,$weeknum,$starttime,$endtime,0,$user);
    }

    /**
     *生成外部周报
     * @param $outplanInfo
     * @param $weeknum
     * @param $outreportID 0 新增   1更新
     * @return false|void
     */
    public function actionGenerateOutReport($outplanInfo,$weeknum,$starttime,$endtime,$outreportID=0,$user=''){


//        var_dump($outplanInfo,$weeknum,$starttime,$endtime,$outreportID);
        //(外部)项目/任务不存在
        if(!$outplanInfo){

            return ['code'=>0,'message'=>$this->lang->weeklyreportout->outreportParamError];
        }
        $outprojectId = $outplanInfo->id;
        $this->loadModel('risk');
        $outSubProjectList = $this->dao->select("*")->from(TABLE_OUTSIDEPLANSUBPROJECTS)->where('outsideProjectPlanID')->eq($outplanInfo->id)->fetchAll();

        $outreportInfo = [];

        if($outSubProjectList){
            $outsubIdList = array_column($outSubProjectList,'id');
            $outsubIdList = implode(",",$outsubIdList);
            $outreportInfo['outsideProjectSubProject'] = $outsubIdList;
        }else{
            $outreportInfo['outsideProjectSubProject'] = '';
        }


        $outPlanTaskList = $this->loadModel("outsideplan")->getTaskByOutsideplanID($outprojectId,"id,subProjectID,subTaskName");
        $outPlanTaskIDList = array_column($outPlanTaskList,'id');

        $outreportInfo['outsideProjectName'] = $outplanInfo->name;
        $outreportInfo['outsideProjectCode'] = $outplanInfo->code;
        $outreportInfo['iscbp'] = 0;

        //判断是否cbp项目
        if(stripos($outplanInfo->name,'cbp') !== false){
            $outreportInfo['iscbp'] = 1;
        }


        if($user){
            $outreportInfo['createdBy'] = $user;
//            $outreportInfo['editedBy'] = $user;
        }else{
            $outreportInfo['createdBy'] = $this->app->user->account;
//            $outreportInfo['editedBy'] = $this->app->user->account;
        }



//        $outreportInfo['updateTime'] = helper::now();
        $outreportInfo['outProjectID'] = $outprojectId;

        $outreportInfo['relationInsideProject'] = '';


        $projectPlanList = $this->dao->select("*")->from(TABLE_PROJECTPLAN)->where("FIND_IN_SET('{$outprojectId}',`outsideProject`)")->andWhere('deleted')->eq(0)->fetchAll();

        $projectPlanIdList = [];


        //内部项目状态
        $projectstatus = [];
        //介质
        $outmediuList = [];
        //项目整体描述
        $projectOverallDesc = '';
        //项目移交情况说明
        $projectTransDesc = '';
        //项目异常情况
        $projectAbnormalDesc = '';
        //下周工作计划
        $nextWeekplan = '';

        //内部项目的项目阶段
        $insideProjectStageList = [];
        //风险
        $outriskList = [];
        $outriskStr = '';
        //里程碑信息
        $outmileList = [];
        $realoutmileList = [];
        $realoutmileKey = 0;
        //内部周报id
        $insideReportIDList = [];
        //介质下标计数器
        $reportMediumKey = 0;
        $reportOutmileKey = 0;
        $reportRiskKey = 0;
        $otherKey = 0;
        $bianhao = 1;

        $innerReportBaseInfoList = [];
        $this->loadModel("weeklyreport");
        if($projectPlanList){

            //查找周报
            foreach ($projectPlanList as $projectplan){




                $weekreport = $this->dao->select("*")->from(TABLE_PROJECTWEEKLYREPORT)->where("projectId")->eq($projectplan->project)->andWhere("weeknum")->eq($weeknum)
                    ->andwhere('deleted')->eq(0)
                    ->andwhere('produceStatus')->eq(1)
                    ->fetch();

                if($weekreport){

                    $projectPlanIdList[] = $weekreport->projectId;
                    //项目阶段
                    $insideProjectStageList[$weekreport->projectId] = $weekreport->progressStatus;
                    $insideReportIDList[] = $weekreport->id;

                    //内部周报项目信息，存入外部周报，增加快照，推送清总使用，并可以减少查询量
                    $innerReportBaseInfoList[$weekreport->projectId]['id'] = $weekreport->id;
                    $innerReportBaseInfoList[$weekreport->projectId]['projectId'] = $weekreport->projectId;
                    $innerReportBaseInfoList[$weekreport->projectId]['planid'] = $projectplan->id;
                    $innerReportBaseInfoList[$weekreport->projectId]['projectName'] = $weekreport->projectName;
                    $innerReportBaseInfoList[$weekreport->projectId]['projectCode'] = $weekreport->projectCode;
                    $innerReportBaseInfoList[$weekreport->projectId]['projectAlias'] = $weekreport->projectAlias;
                    $innerReportBaseInfoList[$weekreport->projectId]['projectStartDate'] = $weekreport->projectStartDate;
                    $innerReportBaseInfoList[$weekreport->projectId]['projectEndDate'] = $weekreport->projectEndDate;
                    $innerReportBaseInfoList[$weekreport->projectId]['pm'] = $weekreport->pm;
                    $innerReportBaseInfoList[$weekreport->projectId]['qa'] = $weekreport->qa;
                    $innerReportBaseInfoList[$weekreport->projectId]['projectStage'] = $this->lang->weeklyreport->projectState[$weekreport->projectStage];
                    $innerReportBaseInfoList[$weekreport->projectId]['devDept'] = $weekreport->devDept;
                    $innerReportBaseInfoList[$weekreport->projectId]['progressStatus'] = $weekreport->progressStatus;




                    $projectstatus[] = isset($this->lang->weeklyreport->innerProjectRelationOutStatusList[$weekreport->projectStage]) ? $this->lang->weeklyreport->innerProjectRelationOutStatusList[$weekreport->projectStage] : 0;
                    $weekreport->reportMedium = $this->dao->select("*")->from(TABLE_PROJECTWEEKLYREPORT_MEDIUM)->where("weekreportID")->eq($weekreport->id)->fetchAll();
                    $weekreport->reportOutmile = $this->dao->select("*")->from(TABLE_PROJECTWEEKLYREPORT_OUTMILE)->where("weekreportID")->eq($weekreport->id)->fetchAll();
                    $weekreport->reportInsidemile = $this->dao->select("*")->from(TABLE_PROJECTWEEKLYREPORT_INSIDEMILE)->where("weekreportID")->eq($weekreport->id)->fetchAll();
                    $weekreport->reportRisk = $this->dao->select("*")->from(TABLE_PROJECTWEEKLYREPORT_RISK)->where("weekreportID")->eq($weekreport->id)->fetchAll();

                    //外部周报介质信息
                    if($weekreport->reportMedium){
                        //查询关联的需求任务。

                        $RequirementsList = $this->weeklyreport->getProjectRequirementsNew($projectplan->project);

                        foreach ($weekreport->reportMedium as $reportMedium){
                            if($reportMedium->mediumOutsideplanTask == '其他'){
                                continue;
                            }

                            if(in_array($reportMedium->mediumOutsideplanTask,$outPlanTaskIDList)){

                                $outmediuList[$reportMediumKey]['outMediumName'] = $reportMedium->mediumName;
                                $outmediuList[$reportMediumKey]['outPreMediumPublishDate'] = $reportMedium->preMediumPublishDate;
                                $outmediuList[$reportMediumKey]['outPreMediumOnlineDate'] = $reportMedium->preMediumOnlineDate;
                                $outmediuList[$reportMediumKey]['outRealMediumPublishDate'] = $reportMedium->realMediumPublishDate;
                                $outmediuList[$reportMediumKey]['outRealMediumOnlineDate'] = $reportMedium->realMediumOnlineDate;

                                //所属外部子任务
                                $outmediuList[$reportMediumKey]['outMediumOutsideplanSub'] = $reportMedium->mediumOutsideplanTask;
                                $mediumRequirementTitle = '';
                                if($reportMedium->mediumRequirement){
                                    $mediumRequirement = explode(',',$reportMedium->mediumRequirement);

                                    foreach ($mediumRequirement as $medRequire){
                                        if(isset($RequirementsList[$medRequire])){
                                            $mediumRequirementTitle .= $RequirementsList[$medRequire]->code.':'.$RequirementsList[$medRequire]->name.',';
                                        }

                                    }
                                }
                                $mediumRequirementTitle = trim($mediumRequirementTitle,',');
                                if($mediumRequirementTitle && trim($reportMedium->mediumMark)){
                                    $outmediuList[$reportMediumKey]['outMediumRequirement'] = $mediumRequirementTitle.PHP_EOL.$reportMedium->mediumMark;
                                }elseif ($mediumRequirementTitle && !trim($reportMedium->mediumMark)){
                                    $outmediuList[$reportMediumKey]['outMediumRequirement'] = $mediumRequirementTitle;
                                }elseif (!$mediumRequirementTitle && trim($reportMedium->mediumMark)){
                                    $outmediuList[$reportMediumKey]['outMediumRequirement'] = $reportMedium->mediumMark;
                                }elseif (!$mediumRequirementTitle && !trim($reportMedium->mediumMark)){
                                    $outmediuList[$reportMediumKey]['outMediumRequirement'] = $this->lang->weeklyreportout->noneValueString;
                                }



                                $reportMediumKey++;
                            }

                        }
                    }

                    //风险
                    if($weekreport->reportRisk){

                        foreach ($weekreport->reportRisk as $reportRisk){

                            if($reportRisk->reportRiskStatus == 'active'){
                                $outriskList[$reportRiskKey]['stateOfRisk'] = $this->lang->risk->statusList[$reportRisk->reportRiskStatus];
                            }else{
                                $outriskList[$reportRiskKey]['stateOfRisk'] = $this->lang->risk->statusList['closed'];
                            }

                            $outriskList[$reportRiskKey]['riskDescribe'] = $reportRisk->reportRiskMark;
                            $tempriskstr = '';
                            $tempriskstr .= $this->lang->weeklyreport->riskCopingStrategies.":".$this->lang->risk->strategyList[$reportRisk->reportRiskStrategy].PHP_EOL;
                            $tempriskstr .= $this->lang->weeklyreport->riskPreventiveMeasure.":".$reportRisk->reportRiskPrevention.PHP_EOL;
                            $tempriskstr .= $this->lang->weeklyreport->riskEmergencyMeasure.":".$reportRisk->reportRiskRemedy.PHP_EOL;
                            if(trim($reportRisk->reportRiskResolution)){
                                $tempriskstr .= $this->lang->weeklyreport->riskSolutionMeasures.":".$reportRisk->reportRiskResolution.PHP_EOL;
                            }else{
                                $tempriskstr .= $this->lang->weeklyreport->riskSolutionMeasures.":".$this->lang->weeklyreportout->noneValueString.PHP_EOL;
                            }

                            $outriskList[$reportRiskKey]['riskResponseMeasure'] = $tempriskstr;
                            $outriskList[$reportRiskKey]['projectName'] = $weekreport->projectName;
                            $reportRiskKey++;
                        }
                    }


                    if($weekreport->reportOutmile){
                        foreach ($weekreport->reportOutmile as $reportOutmile){
                            if(!$reportOutmile->outMileName){
                                continue;
                            }

                            if($reportOutmile->outMileName == $this->lang->weeklyreport->outmileNameListEenglishMap['outMileProductManual']){
                                $outmileList[$otherKey][$reportOutmile->outMileStageName]['outMileProductManual'] = $this->lang->weeklyreportout->plannedCompletionTime.':'.$reportOutmile->outMilePreDate.' '.$this->lang->weeklyreportout->ActualFinishTime.':'.$reportOutmile->outMileRealDate;
                            }
                            if($reportOutmile->outMileName == $this->lang->weeklyreport->outmileNameListEenglishMap['outMileTechnicalProposal']){
                                $outmileList[$otherKey][$reportOutmile->outMileStageName]['outMileTechnicalProposal'] = $this->lang->weeklyreportout->plannedCompletionTime.':'.$reportOutmile->outMilePreDate.' '.$this->lang->weeklyreportout->ActualFinishTime.':'.$reportOutmile->outMileRealDate;
                            }
                            if($reportOutmile->outMileName == $this->lang->weeklyreport->outmileNameListEenglishMap['outMileDeploymentPlan']){
                                $outmileList[$otherKey][$reportOutmile->outMileStageName]['outMileDeploymentPlan'] = $this->lang->weeklyreportout->plannedCompletionTime.':'.$reportOutmile->outMilePreDate.' '.$this->lang->weeklyreportout->ActualFinishTime.':'.$reportOutmile->outMileRealDate;
                            }
                            if($reportOutmile->outMileName == $this->lang->weeklyreport->outmileNameListEenglishMap['outMileUATTest']){
                                $outmileList[$otherKey][$reportOutmile->outMileStageName]['outMileUATTest'] = $this->lang->weeklyreportout->plannedCompletionTime.':'.$reportOutmile->outMilePreDate.' '.$this->lang->weeklyreportout->ActualFinishTime.':'.$reportOutmile->outMileRealDate;
                            }
                            if($reportOutmile->outMileName == $this->lang->weeklyreport->outmileNameListEenglishMap['outMileProductReg']){
                                $outmileList[$otherKey][$reportOutmile->outMileStageName]['outMileProductReg'] = $this->lang->weeklyreportout->plannedCompletionTime.':'.$reportOutmile->outMilePreDate.' '.$this->lang->weeklyreportout->ActualFinishTime.':'.$reportOutmile->outMileRealDate;
                            }
                            if($reportOutmile->outMileName == $this->lang->weeklyreport->outmileNameListEenglishMap['outMileAutoScript']){
                                $outmileList[$otherKey][$reportOutmile->outMileStageName]['outMileAutoScript'] = $this->lang->weeklyreportout->plannedCompletionTime.':'.$reportOutmile->outMilePreDate.' '.$this->lang->weeklyreportout->ActualFinishTime.':'.$reportOutmile->outMileRealDate;
                            }
                        }



                    }



                    //整体进展
                    /*$projectOverallDesc[$otherKey]['code'] = $weekreport->projectCode;
                    $projectOverallDesc[$otherKey]['name'] = $weekreport->projectName;
                    $projectOverallDesc[$otherKey]['projectProgressMark'] = $weekreport->projectProgressMark;*/

                    if(trim($weekreport->projectProgressMark)){
                        $projectProgressMark = $weekreport->projectProgressMark;
                    }else{
                        $projectProgressMark = $this->lang->weeklyreportout->noneValueString;
                    }
                    $projectOverallDesc .= $bianhao.'.'.$weekreport->projectName.':'.PHP_EOL.$projectProgressMark.PHP_EOL;
                    //项目移交情况说明
                    /*$projectTransDesc[$otherKey]['code'] = $weekreport->projectCode;
                    $projectTransDesc[$otherKey]['name'] = $weekreport->projectName;
                    $projectTransDesc[$otherKey]['projectTransDesc'] = $weekreport->projectTransDesc;*/
                    if(trim($weekreport->projectTransDesc)){
                        $tempProjectTransDesc = $weekreport->projectTransDesc;
                    }else{
                        $tempProjectTransDesc = $this->lang->weeklyreportout->noneValueString;
                    }
                    $projectTransDesc .= $bianhao.'.'.$weekreport->projectName.':'.PHP_EOL.$tempProjectTransDesc.PHP_EOL;
                    //项目异常情况
                    /*$projectAbnormalDesc[$otherKey]['code'] = $weekreport->projectCode;
                    $projectAbnormalDesc[$otherKey]['name'] = $weekreport->projectName;
                    $projectAbnormalDesc[$otherKey]['projectAbnormalDesc'] = $weekreport->projectAbnormalDesc;*/
                    if(trim($weekreport->projectAbnormalDesc)){
                        $tempProjectAbnormalDesc = $weekreport->projectAbnormalDesc;
                    }else{
                        $tempProjectAbnormalDesc = $this->lang->weeklyreportout->noneValueString;
                    }
                    $projectAbnormalDesc .= $bianhao.'.'.$weekreport->projectName.':'.PHP_EOL.$tempProjectAbnormalDesc.PHP_EOL;

                    //下周工作计划
                    /*$nextWeekplan[$otherKey]['code'] = $weekreport->projectCode;
                    $nextWeekplan[$otherKey]['name'] = $weekreport->projectName;
                    $nextWeekplan[$otherKey]['nextWeekplan'] = $weekreport->nextWeekplan;*/
                    if(trim($weekreport->nextWeekplan)){
                        $tempNextWeekplan = $weekreport->nextWeekplan;
                    }else{
                        $tempNextWeekplan = $this->lang->weeklyreportout->noneValueString;
                    }
                    $nextWeekplan .= $bianhao.'.'.$weekreport->projectName.':'.PHP_EOL.$tempNextWeekplan.PHP_EOL;
                    $otherKey++;
                    $bianhao++;


                }

            }

            if($projectstatus){
                $outreportInfo['outprojectStatus'] = min($projectstatus);
            }else{
                $outreportInfo['outprojectStatus'] = 0;
            }

        }else{

            $outreportInfo['outprojectStatus'] = 0;

        }

        foreach ($outmileList as $outmile){
            foreach ($outmile as $key=>$outm){
                if(!isset($outm['outMileProductManual'])){
                    $outm['outMileProductManual'] = $this->lang->weeklyreportout->notInvolved;
                }
                if(!isset($outm['outMileTechnicalProposal'])){
                    $outm['outMileTechnicalProposal'] = $this->lang->weeklyreportout->notInvolved;
                }
                if(!isset($outm['outMileDeploymentPlan'])){
                    $outm['outMileDeploymentPlan'] = $this->lang->weeklyreportout->notInvolved;
                }
                if(!isset($outm['outMileUATTest'])){
                    $outm['outMileUATTest'] = $this->lang->weeklyreportout->notInvolved;
                }
                if(!isset($outm['outMileProductReg'])){
                    $outm['outMileProductReg'] = $this->lang->weeklyreportout->notInvolved;
                }
                if(!isset($outm['outMileAutoScript'])){
                    $outm['outMileAutoScript'] = $this->lang->weeklyreportout->notInvolved;
                }

                $realoutmileList[$realoutmileKey] = $outm;
                $realoutmileList[$realoutmileKey]['outMileStageName']=$key;
                ksort($realoutmileList[$realoutmileKey]);
                $realoutmileKey++;
            }


        }


        $outreportInfo['outreportStartDate'] = $starttime;
        $outreportInfo['outreportEndDate'] = $endtime;

        if($projectPlanIdList){

            $projectPlanIdList = implode(",",$projectPlanIdList);
            $outreportInfo['relationInsideProject'] = $projectPlanIdList;
        }

        //当外部周报重新生成时，部分字段不更新
        if(!$outreportID){
            $outreportInfo['createTime'] = helper::now();
            //同步状态
            $outreportInfo['outSyncStatus'] = 0;
            //外部同步提示
            $outreportInfo['outSyncDesc'] = '';
            //外部反馈人
            $outreportInfo['outFeedbackUser'] = '';
            //外部反馈意见
            $outreportInfo['outFeedbackView'] = '';

            //反馈说明
            $outreportInfo['outFeedbackMark'] = '';
            //操作备注
            $outreportInfo['outOperatingRemarks'] = '';
            $outreportInfo['outweeknum'] = $weeknum;
        }

        //项目异常情况
//        $outreportInfo['outProjectAbnormal'] = '';





        if($insideReportIDList){

            $outreportInfo['innerReportId'] = trim(implode(',',$insideReportIDList),',');
        }else{
            $outreportInfo['innerReportId'] = '';
        }

        /*$outreportInfo['outmediuListInfo'] = base64_encode(json_encode($outmediuList));
        $outreportInfo['outmileListInfo'] = base64_encode(json_encode($outmileList));
        $outreportInfo['outOverallProgress'] = base64_encode(json_encode($projectOverallDesc));
        $outreportInfo['outProjectTransferMark'] = base64_encode(json_encode($projectTransDesc));
        $outreportInfo['projectAbnormalDesc'] = base64_encode(json_encode($projectAbnormalDesc));
        $outreportInfo['outNextWeekplan'] = base64_encode(json_encode($nextWeekplan));*/

        $outreportInfo['outmediuListInfo'] = base64_encode(json_encode($outmediuList));
        $outreportInfo['outmileListInfo'] = base64_encode(json_encode($realoutmileList));
        $outreportInfo['outriskListInfo'] = base64_encode(json_encode($outriskList));
        $outreportInfo['insideProjectStage'] = base64_encode(json_encode($insideProjectStageList));
        $outreportInfo['innerReportBaseInfo'] = base64_encode(json_encode($innerReportBaseInfoList));
        $outreportInfo['outOverallProgress'] = $projectOverallDesc;
        $outreportInfo['outProjectTransferMark'] = $projectTransDesc;
        $outreportInfo['outProjectAbnormal'] = $projectAbnormalDesc;
        $outreportInfo['outNextWeekplan'] = $nextWeekplan;
        /*a("---------介质start-------");
        a($outmediuList);
        a("---------介质end-------");
        a("---------外部里程碑start-------");
        a($realoutmileList);
        a("---------外部里程碑end-------");
        a("---------风险start-------");
        a($outriskList);
        a("---------风险end-------");
        a("---------项目阶段start-------");
        a($insideProjectStageList);
        a("---------项目阶段end-------");
        a("---------内部周报基本信息start-------");
        a($innerReportBaseInfoList);
        a("---------内部周报基本信息end-------");
        a($outreportInfo);
        exit();*/
        try {
            if($outreportID){
                $res = $this->dao->update(TABLE_OUTSIDEPROJECTWEEKLYREPORT)->data($outreportInfo)->where('id')->eq($outreportID)->exec();

            }else{
                $this->dao->insert(TABLE_OUTSIDEPROJECTWEEKLYREPORT)->data($outreportInfo)->exec();
                $outreportID = $res = $this->dao->lastInsertID();

            }

            if($res){

                return ['code'=>200,'message'=>$this->lang->success,'outreportid'=>$outreportID];
            }else{
                return ['code'=>0,'message'=>$this->lang->fail,'outreportid'=>$outreportID];
            }
        }catch (Error $e){
            return ['code'=>500,'message'=>$e->getMessage(),'outreportid'=>$outreportID];
        }



//        a($outreportInfo);


    }
    //将外部周报同步给青总。

    public function batchPushWeeklyrportQz(){

        $outreportList = $this->dao->select("*")->from(TABLE_OUTSIDEPROJECTWEEKLYREPORT)->where('outSyncStatus')->eq(3)->fetchAll();

        $result = [];
        foreach ($outreportList as $outreport){
            $result[$outreport->id] = $this->weeklyReportRsync($outreport->id);
        }
        return $result;

    }

    /**
     * 同步青总
     * @param $outReportID
     * @return array
     */
    public function weeklyReportRsync($outReportID){
        if($this->config->global->weeklyreportPushEnable != 'enable'){
            return ['code'=>404,'message'=>$this->lang->weeklyreportout->interfaceConfigurationNotEnabled,'data'=>$outReportID];
        }

        if(!$outReportID){
            return ['code'=>404,'message'=>$this->lang->weeklyreportout->outreportIDError,'data'=>$outReportID];
        }
        $outreport = $this->dao->select("*")->from(TABLE_OUTSIDEPROJECTWEEKLYREPORT)->where('id')->eq($outReportID)->fetch();
        if(!$outreport){
            return ['code'=>404,'message'=>$this->lang->weeklyreportout->outreportNoexistError,'data'=>$outReportID];
        }
        if($outreport->outSyncStatus != 3){
            return ['code'=>404,'message'=>$this->lang->weeklyreportout->outreportNotPushState,'data'=>$outReportID];
        }
        //项目子项
        $outSubProjectList = $this->dao->select("*")->from(TABLE_OUTSIDEPLANSUBPROJECTS)->where('outsideProjectPlanID')->eq($outreport->outProjectID)->fetchAll('id');

        $outsideProjectSubProjectList =explode(',',$outreport->outsideProjectSubProject);
        $outsideProjectSubProjectStr = '';
        foreach ($outsideProjectSubProjectList as $outsideProjectSubProject){
            if(isset($outSubProjectList[$outsideProjectSubProject])){
                $outsideProjectSubProjectStr .= $outSubProjectList[$outsideProjectSubProject]->subProjectName;
            }

        }
        if($outsideProjectSubProjectStr){
            $outsideProjectSubProjectStr = trim($outsideProjectSubProjectStr,',');
        }
        $this->loadModel('weeklyreport');
        $pushdata = [];

        $pushdata['weeklyReportId'] = $outreport->id;
        $pushdata['cbp'] = $outreport->outsideProjectCode;
//        $pushdata['cbp'] = 'CBP202106_1';
        $pushdata['cbpName'] = $outreport->outsideProjectName;

        //包含研发子项
        $pushdata['subDevProject'] = $outsideProjectSubProjectStr;
        //项目状态
        $pushdata['projectProgressStatus'] = $this->lang->weeklyreport->outProjectStatusList[$outreport->outprojectStatus];


        //金科包含项目 json
        $includeJinkeProject = [];
        if($outreport->relationInsideProject){
            $depts                  = $this->loadModel('dept')->getOptionMenu();
            $users                  = $this->loadModel('user')->getPairs('noletter');
            $insideProjectStage = json_decode(base64_decode($outreport->insideProjectStage),true);
//            $projectList = $this->dao->select("*")->from(TABLE_PROJECT)->where('id')->in(explode(',',$outreport->relationInsideProject))->fetchALL('id');
//            $projectPlanList = $this->dao->select("*")->from(TABLE_PROJECTPLAN)->where('project')->in(explode(',',$outreport->relationInsideProject))->fetchALL('project');

            $innerReportBaseInfoList = json_decode(base64_decode($outreport->innerReportBaseInfo));

            $i = 0;
            foreach ($innerReportBaseInfoList as $innerBaseInfo){
                $includeJinkeProject[$i]['department'] = '';

                $tmpdept = explode(',',$innerBaseInfo->devDept);

                foreach ($tmpdept as $tdept){

                    if($tdept){
                        $includeJinkeProject[$i]['department'] .= $depts[$tdept].',';
                    }

                }


                $includeJinkeProject[$i]['department'] = trim($includeJinkeProject[$i]['department'],',');
                $includeJinkeProject[$i]['projectManager'] = $users[$innerBaseInfo->pm];
                $includeJinkeProject[$i]['projectName'] = $innerBaseInfo->projectName;
                $includeJinkeProject[$i]['projectCode'] = $innerBaseInfo->projectCode;
                $includeJinkeProject[$i]['projectStage'] = $insideProjectStage[$innerBaseInfo->projectId];
                $includeJinkeProject[$i]['planStartTime'] = strtotime($innerBaseInfo->projectStartDate)*1000;
                $includeJinkeProject[$i]['planEndTime'] = strtotime($innerBaseInfo->projectEndDate)*1000;
                $includeJinkeProject[$i]['projectNo'] = $innerBaseInfo->projectAlias;
                $i++;

            }
            /*$i = 0;
            foreach ($projectList as $key=>$project){
                $includeJinkeProject[$i]['department'] = '';
                if(isset($projectPlanList[$project->id]->bearDept)){
                    $tmpdept = explode(',',$projectPlanList[$project->id]->bearDept);

                    foreach ($tmpdept as $tdept){

                        if($tdept){
                            $includeJinkeProject[$i]['department'] .= $depts[$tdept].',';
                        }

                    }

                }
                $includeJinkeProject[$i]['department'] = trim($includeJinkeProject[$i]['department'],',');
                $includeJinkeProject[$i]['projectManager'] = $users[$project->PM];
                $includeJinkeProject[$i]['projectName'] = $project->name;
                $includeJinkeProject[$i]['projectCode'] = $project->code;
                $includeJinkeProject[$i]['projectStage'] = $insideProjectStage[$project->id];
                $includeJinkeProject[$i]['planStartTime'] = strtotime($project->begin)*1000;
                $includeJinkeProject[$i]['planEndTime'] = strtotime($project->end)*1000;
                $includeJinkeProject[$i]['projectNo'] = $projectPlanList[$project->id]->mark;
                $i++;
            }*/

        }

//        $pushdata['includeJinkeProject'] = json_encode($includeJinkeProject);
        $pushdata['includeJinkeProject'] = $includeJinkeProject;

        //介质发布情况 json
        $outmediuListInfo = json_decode(base64_decode($outreport->outmediuListInfo));

        $mediaReleaseStatus = [];
        $outPlanTaskList = $this->loadModel("outsideplan")->getTaskByOutsideplanID($outreport->outProjectID,"id,subTaskName");

        $outPlanTaskList = array_column($outPlanTaskList,'subTaskName','id');

        $outmediuI = 0;
        foreach ($outmediuListInfo as $key=>$outmediu){
            if(!$outmediu->outMediumName){
                continue;
            }
            $mediaReleaseStatus[$outmediuI]['name'] = $outmediu->outMediumName;
            //计划发布时间
            $mediaReleaseStatus[$outmediuI]['planReleaseTime'] = strtotime($outmediu->outPreMediumPublishDate)*1000;
            //计划上线时间
            $mediaReleaseStatus[$outmediuI]['planUptime'] = strtotime($outmediu->outPreMediumOnlineDate)*1000;
            //实际发布时间
            $mediaReleaseStatus[$outmediuI]['actualReleaseTime'] = strtotime($outmediu->outRealMediumPublishDate)*1000;
            //实际上线时间
            $mediaReleaseStatus[$outmediuI]['actualplanUptime'] = strtotime($outmediu->outRealMediumOnlineDate)*1000;
            //产品实现需求功能
            if($outmediu->outMediumRequirement){
                $mediaReleaseStatus[$outmediuI]['functionDetail'] = $outmediu->outMediumRequirement;
            }else{
                $mediaReleaseStatus[$outmediuI]['functionDetail'] = $this->lang->weeklyreportout->noneValueString;
            }

            //所属研发子项名称
            $mediaReleaseStatus[$outmediuI]['subProject'] = $outPlanTaskList[$outmediu->outMediumOutsideplanSub];
            $outmediuI++;
        }
//        $pushdata['mediaReleaseStatus'] = json_encode($mediaReleaseStatus);
        $pushdata['mediaReleaseStatus'] = $mediaReleaseStatus;




        //风险情况 json
        $riskProfile = [];
        $outriskListInfo = json_decode(base64_decode($outreport->outriskListInfo));

        foreach ($outriskListInfo as $key=>$outrisk){
            $riskProfile[$key]['riskDescribe'] = $outrisk->riskDescribe;
            $riskProfile[$key]['stateOfRisk'] = $outrisk->stateOfRisk;
            $riskProfile[$key]['riskResponseMeasures'] = $outrisk->riskResponseMeasure;
        }

//        $pushdata['riskProfile'] = json_encode($riskProfile);
        $pushdata['riskProfile'] = $riskProfile;




        //关键里程碑节点提交情况 json
        $outmileListInfo = json_decode(base64_decode($outreport->outmileListInfo));

        $keyMilestoneCommits = [];

        foreach ($outmileListInfo as $key=>$outmile){
            //阶段名称
            $keyMilestoneCommits[$key]['stageName'] = isset($outmile->outMileStageName) ? $outmile->outMileStageName : $this->lang->weeklyreportout->notInvolved;
            //产品规格说明书
            $keyMilestoneCommits[$key]['document'] = isset($outmile->outMileProductManual) ? $outmile->outMileProductManual : $this->lang->weeklyreportout->notInvolved;
            //技术总体方案
            $keyMilestoneCommits[$key]['technicalSolution'] = isset($outmile->outMileTechnicalProposal) ? $outmile->outMileTechnicalProposal : $this->lang->weeklyreportout->notInvolved;
            //应用部署方案
            $keyMilestoneCommits[$key]['deploymentPlan'] = isset($outmile->outMileDeploymentPlan) ? $outmile->outMileDeploymentPlan : $this->lang->weeklyreportout->notInvolved;
            //UAT测试
            $keyMilestoneCommits[$key]['uatTest'] = isset($outmile->outMileUATTest) ? $outmile->outMileUATTest : $this->lang->weeklyreportout->notInvolved;
            //自动化切换脚本
            $keyMilestoneCommits[$key]['autoSwitchScript'] = isset($outmile->outMileAutoScript) ? $outmile->outMileAutoScript : $this->lang->weeklyreportout->notInvolved;
            //提交产品等级时间   暂无
            $keyMilestoneCommits[$key]['submitReportTime'] = time()*1000;


        }

//        $pushdata['keyMilestoneCommits'] = json_encode($keyMilestoneCommits);
        $pushdata['keyMilestoneCommits'] = $keyMilestoneCommits;
        //整体进展
        if(trim($outreport->outOverallProgress)){
            $pushdata['overallProgress'] = $outreport->outOverallProgress;
        }else{
            $pushdata['overallProgress'] = $this->lang->weeklyreportout->noneValueString;
        }

        //项目移交情况说明
        if(trim($outreport->outProjectTransferMark)){
            $pushdata['projectHandOverDescription'] = $outreport->outProjectTransferMark;
        }else{
            $pushdata['projectHandOverDescription'] = $this->lang->weeklyreportout->noneValueString;
        }

        //项目异常情况
        if(trim($outreport->outProjectAbnormal)){
            $pushdata['projectAnomalies'] = $outreport->outProjectAbnormal;
        }else{
            $pushdata['projectAnomalies'] = $this->lang->weeklyreportout->noneValueString;
        }

        //下周工作计划
        if(trim($outreport->outNextWeekplan)){
            $pushdata['nextWeekPlan'] = $outreport->outNextWeekplan;
        }else{
            $pushdata['nextWeekPlan'] = $this->lang->weeklyreportout->noneValueString;
        }

        //反馈意见
        $pushdata['feedback'] = $outreport->outFeedbackView;

        //反馈说明
        $pushdata['feedbackComment'] = $outreport->outFeedbackMark;


        /*       $pushdata['includeJinkeProject'] = [];
               $pushdata['mediaReleaseStatus'] = [];
               $pushdata['riskProfile'] = [];
               $pushdata['keyMilestoneCommits'] = [];*/

        $url = $this->config->global->weeklyreportPushUrl;
//        $url = $this->config->global->weeklyReportRsync;
        $header = [
//            "Content-type: application/json;charset=UTF-8",
//            'Accept: application/json',
            'App-Id: '.$this->config->global->weeklyreportPushAppId,
            'App-Secret: '.$this->config->global->weeklyreportPushAppSecret
        ];

//        $res = common::http($url,json_encode($pushdata,JSON_UNESCAPED_UNICODE),[],$header);

        $result = $this->loadModel('requestlog')->http($url, $pushdata, 'POST', 'json', array(), $header);
//        $result = $this->http($url,json_encode($pushdata,JSON_UNESCAPED_UNICODE),[],$header);

        $res = json_decode($result);


        if($res->code == 200){
            $updata = [
                'outSyncStatus'=>1,
                'outSyncDesc'=>$res->data,
                'outSyncTime'=>helper::now(),

            ];

            $this->dao->update(TABLE_OUTSIDEPROJECTWEEKLYREPORT)->data($updata)->where('id')->eq($outreport->id)->exec();
            $requestStatus = 'success';
        }else{
            $updata = [
                'outSyncStatus'=>2,
                'outSyncDesc'=>$res->code.':'.$res->data,
                'outSyncTime'=>helper::now(),

            ];
            $requestStatus = 'fail';

            $this->dao->update(TABLE_OUTSIDEPROJECTWEEKLYREPORT)->data($updata)->where('id')->eq($outreport->id)->exec();
            $this->sendmail();
        }

        $this->loadModel('requestlog')->saveRequestLog($url, "weeklreportout", "周报推送", 'POST', $pushdata, $result, $requestStatus, '');

        return ['code'=>$res->code,'message'=>$updata['outSyncDesc'],'data'=>$outReportID];

    }

    public  function http($url, $data = null, $options = array(), $headers = array())
    {

        global $lang, $app;
        if(!extension_loaded('curl')) return json_encode(array('result' => 'fail', 'message' => $lang->error->noCurlExt));

        commonModel::$requestErrors = array();

        /*if(!is_array($headers)) $headers = (array)$headers;
        $headers[] = "API-RemoteIP: " . zget($_SERVER, 'REMOTE_ADDR', '');*/

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Sae T OAuth2 v0.1');
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_ENCODING, "");
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($curl, CURLOPT_HEADER, FALSE);
        curl_setopt($curl, CURLINFO_HEADER_OUT, TRUE);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_URL, $url);

        if(!empty($data))
        {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }

        if($options) curl_setopt_array($curl, $options);

        $response = curl_exec($curl);
        $errors   = curl_error($curl);

        curl_close($curl);


        if($errors) commonModel::$requestErrors[] = $errors;

        return $response;
    }

    public function feedbackMark($outreportId,$issend=0){

        if($this->config->global->weeklyreportPushMarkEnable != 'enable'){
            a(['code'=>404,'message'=>$this->lang->weeklyreportout->interfaceConfigurationNotEnabled]);
            return ['code'=>404,'message'=>$this->lang->weeklyreportout->interfaceConfigurationNotEnabled];
        }
        $outreport = $this->getByID($outreportId);
        $users                  = $this->loadModel('user')->getPairs('noletter');
        $pushData = [
            'weeklyReportId'=>$outreport->id,
            'feedbackComment'=>$outreport->outFeedbackMark,
//            'feedbackPeople'=>$users[$outreport->outFeedbackUser],
            'feedbackPeople'=>$outreport->outFeedbackUser,

        ];


        $url = $this->config->global->weeklyreportPushMarkUrl;
//        $url = $this->config->global->feedbackMark;
        $header = [
//            "Content-type: application/json;charset=UTF-8",
//            'Accept: application/json',
            'App-Id: '.$this->config->global->weeklyreportPushMarkAppId,
            'App-Secret: '.$this->config->global->weeklyreportPushMarkAppSecret
        ];


//        $res = common::http($url,$pushData,[],$header);
        $result = $this->loadModel('requestlog')->http($url, $pushData, 'POST', 'json', array(), $header);
//        $res = $this->http($url,json_encode($pushData,JSON_UNESCAPED_UNICODE),[],$header);
        $res = json_decode($result);


        if($res->code == 200){
            $requestStatus = 'success';
        }else{
            $requestStatus = 'fail';
        }
        $this->loadModel('requestlog')->saveRequestLog($url, "weeklreportout", "周报反馈推送", 'POST', $pushData, $result, $requestStatus, '');
        echo "<pre>";
        var_dump($res);
        var_dump(commonModel::$requestErrors);


    }

    /**
     * Project: chengfangjinke
     * Desc: 发送邮件
     * shixuyang
     */
    public function sendmail()
    {
        $this->loadModel('mail');
        $this->loadModel('project');
        //邮件显示详细信息
        $users  = $this->loadModel('user')->getPairs('noletter|noclosed');


        /* 获取后台通知中配置的邮件发信。*/
        $this->app->loadLang('custommail');
        $mailConf   = isset($this->config->global->setWeeklyreportoutMail) ? $this->config->global->setWeeklyreportoutMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf   = json_decode($mailConf);
        $browseType = 'weeklyreportout';


        $mailTitle = vsprintf($mailConf->mailTitle, $mailConf->variables);


        /* Get mail content. */
        $oldcwd     = getcwd();
        $modulePath = $this->app->getModulePath($appName = '', 'weeklyreportout');
        $viewFile   = $modulePath . 'view/sendmail.html.php';
        chdir($modulePath . 'view');

        if(file_exists($modulePath . 'ext/view/sendmail.html.php'))
        {
            $viewFile = $modulePath . 'ext/view/sendmail.html.php';
            chdir($modulePath . 'ext/view');
        }
        //$serverIp = $_SERVER['SERVER_NAME'];

        ob_start();
        include $viewFile;
        foreach(glob($modulePath . 'ext/view/sendmail.*.html.hook.php') as $hookFile) include $hookFile;
        $mailContent = ob_get_contents();
        ob_end_clean();
        chdir($oldcwd);

        $toList = array();
        foreach ($this->lang->project->pushWeeklyreportQingZong as $key=>$value) {
            array_push($toList, $key);
        }
        $toList = trim(implode(',', $toList),',');
        $ccList = '';

        /* 处理邮件标题。*/
        $subject = $mailTitle;

        /* Send emails. */
        $this->mail->send($toList, $subject, $mailContent, $ccList);
        if($this->mail->isError()) error_log(join("\n", $this->mail->getError()));
    }

    public static function isClickable($weeklyreport, $action)
    {
        switch (strtolower($action)){
            case 'pushoneweeklyreportqingzong': //推送
                return $weeklyreport->iscbp == 1;
            default:
                return true;
        }
    }
}