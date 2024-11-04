<?php

class weeklyreportout extends control
{
    public function browse($browseType = 'all', $param = 0, $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $this->view->pager         = $pager;
        $this->view->title         = $this->lang->weeklyreportout->common;
        $this->view->depts         = $this->loadModel('dept')->getOptionMenu();
        $this->view->users         = $this->loadModel('user')->getPairs('noletter|noclosed');
        $this->view->weeklyreports = $this->weeklyreportout->getList($browseType, $pager, $orderBy);
        $this->view->browseType    = $browseType;
        $this->view->orderBy       = $orderBy;
        $this->view->param         = $param;

        $this->display();
    }

    public function view($outreportID)
    {
        $this->view->title = $this->lang->weeklyreportout->view;
        $this->view->outreport = $this->weeklyreportout->getByID($outreportID);
        $outPlanTaskList       = $this->loadModel('outsideplan')->getTaskByOutsideplanID($this->view->outreport->outProjectID, 'id,subTaskName');

        $this->view->outPlanTaskList = array_column($outPlanTaskList, 'subTaskName', 'id');
        $this->loadModel('weeklyreport');
        $this->view->users = $this->loadModel('user')->getPairs('noletter');

        //关联内部项目，  根据关联的内部周报查询。
        /* if($this->view->outreport->innerReportId){
             $innerReportIdArr = explode(',',$this->view->outreport->innerReportId);
             $this->view->relationInsidePlan = $this->dao->select('*')->from(TABLE_PROJECTWEEKLYREPORT)->where('id')->in($innerReportIdArr)->andWhere('deleted')->eq(0)->fetchAll();
         }else{
             $this->view->relationInsidePlan = [];
         }*/

        //查询上周周报
        $oldweeknum = $this->view->outreport->outweeknum - 1;
        if ($oldweeknum > 0) {
            $this->view->preOutreprot = $this->dao->select('id,outFeedbackView,outsideProjectName,outreportEndDate,outreportStartDate')->from(TABLE_OUTSIDEPROJECTWEEKLYREPORT)->where('outProjectID')->eq($this->view->outreport->outProjectID)->andWhere('outweeknum')->eq($oldweeknum)->fetch();
        } else {
            $this->view->preOutreprot = [];
        }

        $this->view->depts           = $this->loadModel('dept')->getOptionMenu();
        $this->view->outsidetaskList = $this->loadModel('outsideplan')->getTaskByOutsideplanID($this->view->outreport->outProjectID);

        $this->view->actions = $this->loadModel('action')->getList('weeklyreportout', $this->view->outreport->id);
        $this->display();
    }

    public function edit($outreportID)
    {
        $this->view->title = $this->lang->weeklyreportout->edit;
        if ($_POST) {
            $changes = $this->weeklyreportout->update($outreportID);
            if (dao::$errors) {
                $response['result']  = 'fail';
                $response['message'] = dao::$errors;
                $this->send($response);
            }

            $actionID = $this->loadModel('action')->create('weeklyreportout', $outreportID, 'edited');
            if ($changes) {
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'weeklyreportout-browse.html#app=platform';
            $this->send($response);
        }

        $this->loadModel('projectplan');
        $this->view->outreport                  = $this->weeklyreportout->getByID($outreportID);
        $this->view->outreport->outriskListInfo = base64_encode(json_encode($this->view->outreport->outriskListInfo));
//    a($this->view->outreport);
        $this->view->outsideplan = $this->loadModel('outsideplan')->getSimpleByID($this->view->outreport->outProjectID);

        $outPlanTaskList = $this->loadModel('outsideplan')->getTaskByOutsideplanID($this->view->outreport->outProjectID, 'id,subTaskName');

        $this->view->outPlanTaskList = array_column($outPlanTaskList, 'subTaskName', 'id');
        $this->view->outPlanTaskList = [''=>''] + $this->view->outPlanTaskList;
        if ($this->view->outreport->relationInsideProject) {
            $relationInsideProject       = explode(',', $this->view->outreport->relationInsideProject);
            $this->view->projectplanList = $this->dao->select('*')->from(TABLE_PROJECTPLAN)->where('project')->in($relationInsideProject)->fetchAll();
            $this->view->projectList     = $this->dao->select('*')->from(TABLE_PROJECT)->where('id')->in($relationInsideProject)->fetchAll('id');
        } else {
            $this->view->projectplanList = [];
            $this->view->projectList     = [];
        }

        //项目子项
        $this->view->outSubProjectList = $this->dao->select('*')->from(TABLE_OUTSIDEPLANSUBPROJECTS)->where('outsideProjectPlanID')->eq($this->view->outreport->outProjectID)->fetchAll('id');

        $this->view->users = $this->loadModel('user')->getPairs('noletter');

        $this->view->depts = $this->loadModel('dept')->getOptionMenu();

        $this->view->allQAList = $this->weeklyreportout->getAllQA();

        $this->loadModel('weeklyreport');
        $this->display();
    }

    /**
     * 将外部周报加入待推送队列，实际推送由 定时任务推送
     */
    public function pushWeeklyreportQingZong()
    {
        if ($_POST) {
            $result = $this->weeklyreportout->pushWeeklyreportQingZong();
            if (dao::$errors) {
                $response['result']  = 'fail';
                $response['message'] = dao::$errors;
                $this->send($response);
            }
            if ($result) {
                //为外部周报批量添加操作记录
                foreach ($result as $outreportid) {
                    $actionID = $this->loadModel('action')->create('weeklyreportout', $outreportid, 'pushweeklyreportqingzong');
                }
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }

        $data = $this->dao
            ->select('distinct outweeknum')
            ->from(TABLE_OUTSIDEPROJECTWEEKLYREPORT)
            ->where('deleted')->eq(0)

            ->fetchAll();

        $data = array_column($data, 'outweeknum');

        rsort($data);

        $this->view->weekNumList = array_combine($data, $data);

        $this->display();
    }

    public function pushOneWeeklyreportQingZong()
    {
        if ($_POST) {
            $this->viewType = 'json';
            $result = $this->weeklyreportout->pushOneWeeklyreportQingZong();
            if (dao::isError()) {
               /* $response['result']  = 'fail';
                $response['message'] = dao::$errors;*/
                $this->send($result);
            }
            if ($result['code'] == 200) {
                //为外部周报批量添加操作记录

                  $actionID = $this->loadModel('action')->create('weeklyreportout', $result['data']->id, 'pushoneweeklyreportqingzong');

            }

         /*   $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';*/
            $this->send($result);
        }
    }

    /**
     * 重新生成周报
     */
    public function regeneration()
    {
        if ($_POST) {
            $result = $this->weeklyreportout->regeneration($_POST['outreportID']);
            if (dao::$errors) {
                $response['result']  = 'fail';
                $response['message'] = dao::$errors;
                $this->send($response);
            }
            if (200 == $result['code']) {
                $actionID = $this->loadModel('action')->create('weeklyreportout', $_POST['outreportID'], 'regeneration');
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }
    }

    /**
     * 单条生成。主要用于测试,
     * @param $outreportID
     * @param mixed $outreportqueeID
     * @param mixed $outplanID
     * @param mixed $weeknum
     */
    public function oneGeneration($outplanID, $weeknum = 0)
    {
        if (!$weeknum) {
            a('周数错误');

            return;
        }

        $outreportqueue = $this->dao->select('*')->from(TABLE_OUTSIDEPROJECTWEEKLYREPORT_QUEUE)->where('weeknum')->eq($weeknum)->fetch();

        if (!$outreportqueue) {
            a('生成队列不存在');

            return;
        }
        $outsieplan = $this->dao->select('*')->from(TABLE_OUTSIDEPLAN)->where('id')->eq($outplanID)->fetch();
        if (!$outsieplan) {
            a('(外部)项目/任务不存在');

            return;
        }

        $res = $this->weeklyreportout->generateOutReport($outsieplan, $outreportqueue->weeknum, $outreportqueue->outreportStartDate, $outreportqueue->outreportEndDate);
        if (200 == $res['code'] && isset($res['outreportid'])) {
            $actionID = $this->loadModel('action')->create('weeklyreportout', $res['outreportid'], 'oneGeneration');
        }

        a($res);
    }

    /**
     * 单条反馈
     * @param $outreportID
     * @param mixed $outreportId
     */
    public function feedbackMark($outreportId)
    {
        $this->weeklyreportout->feedbackMark($outreportId);
    }

    /**
     * 单条推送外部周报。主要用于测试
     * @param $outreportID
     */
    public function oneOutReportPush($outreportID)
    {
        $res = $this->weeklyreportout->weeklyReportRsync($outreportID);

        a($res);
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
        if ($_POST && 'xlsx' == $_POST['fileType']) {
            $this->loadModel('file');
            $this->loadModel('projectplan');
            $this->loadModel('outsideplan');
            $this->loadModel('risk');
            $this->loadModel('application');
            $applicationLang   = $this->lang->weeklyreportout;
            $applicationConfig = $this->config->weeklyreportout;

            // Create field lists.
            $fields = $this->post->exportFields ?: explode(',', $applicationConfig->list->exportFields);
            foreach ($fields as $key => $fieldName) {
                $fieldName          = trim($fieldName);
                $fields[$fieldName] = $applicationLang->{$fieldName} ?? $fieldName;
                unset($fields[$key]);
            }

            $applications = $this->dao->select('*')->from(TABLE_OUTSIDEPROJECTWEEKLYREPORT)
                ->where('deleted')->ne('1')
                ->beginIF('' != $_POST['weeknum'])->andWhere('outweeknum')->eq($_POST['weeknum'])->fi()
                ->orderBy($orderBy)
                ->fetchAll('id');

            // Get users, products and executions.
            $users = $this->loadModel('user')->getPairs('noletter');
            $depts = $this->loadModel('dept')->getOptionMenu();
            foreach ($applications as $application) {
                //周报时间
                $application->outreportDate = '第'.$application->outweeknum.'周+'.$application->outreportStartDate.'-'.$application->outreportEndDate;
                //新建人
                $application->createdBy = zget($users, $application->createdBy,'');
                //编辑人
                $application->editedBy = zget($users, $application->editedBy,'');
                //介质信息
                $outmediuListInfo                      = json_decode(base64_decode($application->outmediuListInfo));
                $application->outMediumName            = '';
                $application->outMediumOutsideplanSub  = '';
                $application->outPreMediumPublishDate  = '';
                $application->outPreMediumOnlineDate   = '';
                $application->outRealMediumPublishDate = '';
                $application->outRealMediumOnlineDate  = '';
                $application->outMediumRequirement     = '';
                $ii                                    = 0;
                $outPlanTaskList       = $this->loadModel('outsideplan')->getTaskByOutsideplanID($application->outProjectID, 'id,subTaskName');
                $outPlanTaskList = array_column($outPlanTaskList, 'subTaskName', 'id');
                foreach ($outmediuListInfo as $reportMedium) {
                    ++$ii;
                    $application->outMediumName            .= "[{$ii}]" . $reportMedium->outMediumName . PHP_EOL;
                    $application->outMediumOutsideplanSub  .= "[{$ii}]" . zget($outPlanTaskList, $reportMedium->outMediumOutsideplanSub, '') . PHP_EOL;
                    $application->outPreMediumPublishDate  .= "[{$ii}]" . $reportMedium->outPreMediumPublishDate . PHP_EOL;
                    $application->outPreMediumOnlineDate   .= "[{$ii}]" . $reportMedium->outPreMediumOnlineDate . PHP_EOL;
                    $application->outRealMediumPublishDate .= "[{$ii}]" . $reportMedium->outRealMediumPublishDate . PHP_EOL;
                    $application->outRealMediumOnlineDate .= "[{$ii}]" . $reportMedium->outRealMediumOnlineDate . PHP_EOL;
                    $application->outMediumRequirement .= "[{$ii}]" . $reportMedium->outMediumRequirement . PHP_EOL;
                }
                //外部里程碑
                $outmileListInfo                       = json_decode(base64_decode($application->outmileListInfo));
                $application->outMileStageName         = '';
                $application->outMileProductManual     = '';
                $application->outMileTechnicalProposal = '';
                $application->outMileDeploymentPlan    = '';
                $application->outMileUATTest           = '';
                $application->outMileProductReg        = '';
                $application->outMileAutoScript        = '';
                $ii                                    = 0;
                foreach ($outmileListInfo as $outmile) {
                    ++$ii;
                    $application->outMileStageName         .= "[{$ii}]" . $outmile->outMileStageName . PHP_EOL;
                    $application->outMileProductManual     .= "[{$ii}]" . $outmile->outMileProductManual . PHP_EOL;
                    $application->outMileTechnicalProposal .= "[{$ii}]" . $outmile->outMileTechnicalProposal . PHP_EOL;
                    $application->outMileDeploymentPlan    .= "[{$ii}]" . $outmile->outMileDeploymentPlan . PHP_EOL;
                    $application->outMileUATTest           .= "[{$ii}]" . $outmile->outMileUATTest . PHP_EOL;
                    $application->outMileProductReg .= "[{$ii}]" . $outmile->outMileProductReg . PHP_EOL;
                    $application->outMileAutoScript .= "[{$ii}]" . $outmile->outMileAutoScript . PHP_EOL;
                }
                //项目风险
                $outriskListInfo                  = json_decode(base64_decode($application->outriskListInfo));
                $application->riskDescribe        = '';
                $application->riskResponseMeasure = '';
                $application->stateOfRisk         = '';
                $ii                               = 0;
                foreach ($outriskListInfo as $outrisk) {
                    ++$ii;
                    if ('active' == $outrisk->reportRiskStatus) {
                        $application->stateOfRisk .= "[{$ii}]" . $this->lang->risk->statusList[$outrisk->reportRiskStatus]. PHP_EOL;
                    } else {
                        $application->stateOfRisk .= "[{$ii}]" . $this->lang->risk->statusList['closed']. PHP_EOL;
                    }

                    $application->riskDescribe .= "[{$ii}]" . $outrisk->riskDescribe. PHP_EOL;
                    $application->riskResponseMeasure .= "[{$ii}]" . $outrisk->riskResponseMeasure. PHP_EOL;
                }
                //(外部)项目/任务
                $outsideplan                   = $this->loadModel('outsideplan')->getSimpleByID($application->outProjectID);
                $application->cbpcode          = $outsideplan->code;
                $application->cbpname          = $outsideplan->name;
                $tasks    = $this->dao->select('*')->from(TABLE_OUTSIDEPLANTASKS)->where('outsideProjectPlanID')->eq($application->outProjectID)->andwhere('deleted')->eq(0)->fetchall();
                $application->govDept = '';
                $application->outDemander      = '';
                $application->outBearCompany   = '';
                $application->outPlanStartDate = '';
                $application->outPlanEndDate   = '';
                $ii = 0;
                $application->outProjectStatus = zget($this->lang->outsideplan->statusList, $outsideplan->status,''). PHP_EOL;;
                foreach ($tasks as $task){
                    ++$ii;
                    $vlist = explode(',', $task->subTaskUnit);
                    $arr = [];
                    foreach ($vlist as $itemv){
                        if(empty($itemv)) continue;
                        $arr[] = zget($this->lang->outsideplan->subProjectUnitList, $itemv,'');
                    }
                    $application->govDept          .= "[{$ii}]" .implode(',', $arr). PHP_EOL;

                    $vlist = explode(',', $task->subTaskBearDept);
                    $arr = [];
                    foreach ($vlist as $itemv){
                        if(empty($itemv)) continue;
                        $arr[] = zget($this->lang->application->teamList, $itemv,'') ;
                    }
                    echo
                    $application->outBearCompany          .= "[{$ii}]" .implode(',', $arr). PHP_EOL;

                    $vlist = explode(',', $task->subTaskDemandParty);
                    $arr = [];
                    foreach ($vlist as $itemv){
                        if(empty($itemv)) continue;
                        $arr[] = zget($this->lang->outsideplan->subProjectDemandPartyList, $itemv,'') ;
                    }
                    $application->outDemander          .= "[{$ii}]" .implode(',', $arr). PHP_EOL;

                    $application->outPlanStartDate          .= "[{$ii}]" .$task->subTaskBegin. PHP_EOL;
                    $application->outPlanEndDate          .= "[{$ii}]" .$task->subTaskEnd. PHP_EOL;

                }


                $outSubProjectList             = $this->dao->select('*')->from(TABLE_OUTSIDEPLANSUBPROJECTS)->where('outsideProjectPlanID')->eq($application->outProjectID)->fetchAll('id');
                $tempSubProject                = explode(',', $application->outsideProjectSubProject);
                $subProjectName                = [];
                foreach ($tempSubProject as $subID) {
                    $subProjectName[] = $outSubProjectList[$subID]->subProjectName;
                }
                $application->outSubProjectName = trim(implode(',', $subProjectName), ',');

                //内部项目
                $innerReportBaseInfo = json_decode(base64_decode($application->innerReportBaseInfo));
                $application->inProjectName      = '';
                $application->inProjectpm        = '';
                $application->inProjectCode      = '';
                $application->inProjectDept      = '';
                $application->inProjectMake      = '';
                $application->inProjectPlanStart = '';
                $application->inProjectPlanEnd   = '';
                $application->inProjectStage     = '';
                $ii                              = 0;
                foreach ($innerReportBaseInfo as $projectplan) {
                    ++$ii;
                    $application->inProjectName .= "[{$ii}]" . $projectplan->projectName . PHP_EOL;

                    $application->inProjectpm   .= "[{$ii}]" .zget($users,$projectplan->pm, $projectplan->pm). PHP_EOL;


                    $application->inProjectCode .= "[{$ii}]" . $projectplan->projectCode . PHP_EOL;
                    $tempdeptArr = explode(',',$projectplan->devDept);
                    $arr = array();
                    foreach ($tempdeptArr as $tempDept) {
                        array_push($arr, zget($depts, $tempDept), '');
                    }
                    $application->inProjectDept .= "[{$ii}]".implode(',',$arr) . PHP_EOL;
                    $application->inProjectMake .= "[{$ii}]" . $projectplan->projectAlias . PHP_EOL;
                    $application->inProjectPlanStart .= "[{$ii}]" . $projectplan->projectStartDate . PHP_EOL;
                    $application->inProjectPlanEnd .= "[{$ii}]" . $projectplan->projectEndDate . PHP_EOL;
                    $application->inProjectStage .= "[{$ii}]" . $projectplan->progressStatus . PHP_EOL;
                }
                $application->outSyncStatus = $this->lang->weeklyreportout->outSyncStatusList[$application->outSyncStatus];
            }
            if($_POST['weeknum'] != ''){
                $this->post->set('fileName', '(外部)项目/任务周报-第'.$_POST['weeknum'].'周');
            }else{
                $this->post->set('fileName', '(外部)项目/任务周报-全部');
            }
            $this->post->set('fields', $fields);
            $this->post->set('rows', $applications);
            $this->post->set('kind', '项目周报');
            $this->fetch('file', 'exportreport2' . $this->post->fileType, $_POST); //module/file/ext/control/exportreport2xlsx.php
        }
        if ($_POST && 'xlsx' != $_POST['fileType']) {
            echo js::alert('只支持选择xlsx导出');
        }
        $this->view->fileName        = '(外部)项目/任务周报-第' . $_POST['weeknum'] . '周';
        $this->view->allExportFields = $this->config->weeklyreportout->list->exportFields;
        $this->view->customExport    = true;
        $this->view->reportId        = $reportId;
        $weeknums                    = $this->dao->select('outweeknum')->from(TABLE_OUTSIDEPROJECTWEEKLYREPORT)
            ->where('deleted')->ne('1')
            ->groupBy('outweeknum')
            ->orderBy('outweeknum')
            ->fetchAll('outweeknum');
        $weekPair = ['' => '全部'];
        foreach ($weeknums as $key => $value) {
            $weekPair[$key] = '第' . $key . '周';
        }
        $this->view->weeknumList = $weekPair;
        $this->display();
    }
}
