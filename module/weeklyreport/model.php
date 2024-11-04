<?php

/**
 * Created by Yanqi Tong
 */

class weeklyreportModel extends model
{
    /**
     * Get report list.
     * 根据项目id 获取周报列表
     * @param  string  $browseType
     * @param  string  $orderBy
     * @param  object  $pager
     * @access public
     * @return void
     */
    public function getList($projectID, $browseType, $queryID, $orderBy, $pager = null)
    {
        $reports = $this->dao->select('*')->from(TABLE_PROJECTWEEKLYREPORT)
            ->where('deleted')->eq(0)
            ->andwhere('projectId')->eq((int)$projectID)
            ->orderBy('id desc')
            ->page($pager)
            ->fetchAll('id');

        return $reports;
    }

    /**
     * Project: chengfangjinke
     * Method: getPairs
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 11:22
     * Desc: This is the code comment. This method is called getPairs.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $programID
     * @return mixed
     */
    public function getPairs($programID = 0)
    {
        return $this->dao->select('id,name')->from(TABLE_APPLICATION)
            ->where('deleted')->eq(0)
            ->beginIF($programID)->andWhere('program')->eq($programID)->fi()
            ->orderBy('id_desc')
            ->fetchPairs();
    }



    /**
     * Create a application.
     * 新建周报
     * @access public
     * @return void
     */
    public function create()
    {

        if(!isset($_POST['weeknum']) || !$_POST['weeknum'] || !is_numeric($_POST['weeknum'])){
            dao::$errors['weeknum'] = sprintf($this->lang->weeklyreport->errorNotempty,$this->lang->weeklyreport->weeknum);
            return false;
        }
        if(isset($_POST['mileDelayNum']) && $_POST['mileDelayNum'] && !is_numeric($_POST['mileDelayNum'])){
            dao::$errors['mileDelayNum'] = $this->lang->weeklyreport->milesDelyVarTypeError;
            return false;
        }
        if(!(int)$_POST['projectId']){
            dao::$errors[] = $this->lang->weeklyreport->projectIDError;
            return false;
        }
        if(!$_POST['reportStartDate']){
            dao::$errors['reportStartDate'] = vsprintf($this->lang->weeklyreport->errorNotempty,$this->lang->weeklyreport->reportStartDate);
            return false;
        }
        if(!$_POST['reportEndDate']){
            dao::$errors['reportEndDate'] = vsprintf($this->lang->weeklyreport->errorNotempty,$this->lang->weeklyreport->reportEndDate);
            return false;
        }
        $report = $this->getWeekReportByProjectAndWeeknum($_POST['projectId'],$_POST['weeknum']);
        if($report){
            dao::$errors[] = $this->lang->weeklyreport->reportExistError;
            return false;
        }
        $projectPlan = $this->loadModel('projectPlan')->getByProjectID($_POST['projectId']);
        $isQA = $this->authUserReportQA($this->app->user->account,$projectPlan->bearDept);
        if(!$isQA){
            dao::$errors[] = $this->lang->weeklyreport->nopermissioncreate;
            return false;
        }
        //内部里程碑
        $insideMileInfoList = [];
        $itemnum = 1;
        foreach ($_POST['insideMileStage'] as $key=>$insideMileStage){
            if(!isset($_POST['insideMileName'][$key]) || !$_POST['insideMileName'][$key]){
                dao::$errors[] = vsprintf($this->lang->weeklyreport->errorNotempty,['第 '.$itemnum.' 条内部'.$this->lang->weeklyreport->insideMileName]);
                return false;
            }
            $insideMileInfoList[$key]['insideMileStage']    = $insideMileStage;
            $insideMileInfoList[$key]['insideMileName']         = $_POST['insideMileName'][$key];
            $insideMileInfoList[$key]['projectID']         = $_POST['projectId'];
            $insideMileInfoList[$key]['insideMilePreDate']         = $_POST['insideMilePreDate'][$key];
            $insideMileInfoList[$key]['insideMileRealDate']         = $_POST['insideMileRealDate'][$key];
            $insideMileInfoList[$key]['insideMileMark']         = $_POST['insideMileMark'][$key];
            $itemnum ++;
        }

        $mediumInfoList = [];
        //介质信息
        foreach ($_POST['mediumName'] as $key=>$mediumName){
            $mediumInfoList[$key]['mediumName']    = $mediumName;
            $mediumInfoList[$key]['mediumOutsideplanTask']         = $_POST['mediumOutsideplanTask'][$key];
            $mediumInfoList[$key]['projectID']         = $_POST['projectId'];
            $mediumInfoList[$key]['preMediumPublishDate']         = $_POST['preMediumPublishDate'][$key];
            $mediumInfoList[$key]['preMediumOnlineDate']         = $_POST['preMediumOnlineDate'][$key];
            $mediumInfoList[$key]['realMediumPublishDate']         = $_POST['realMediumPublishDate'][$key];
            $mediumInfoList[$key]['realMediumOnlineDate']         = $_POST['realMediumOnlineDate'][$key];
            if(isset($_POST['mediumRequirement'][$key])){
                $mediumInfoList[$key]['mediumRequirement']         = implode(",",$_POST['mediumRequirement'][$key]);
            }else{
                $mediumInfoList[$key]['mediumRequirement']         = "";
            }

            $mediumInfoList[$key]['mediumMark']         = $_POST['mediumMark'][$key];


        }
        //外部里程碑
        $outMileInfoList = [];
        foreach ($_POST['outMileStageName'] as $key=>$outMileStageName){
            $outMileInfoList[$key]['outMileStageName']    = $outMileStageName;
            $outMileInfoList[$key]['outMileName']         = $_POST['outMileName'][$key];
            $outMileInfoList[$key]['projectID']         = $_POST['projectId'];
            $outMileInfoList[$key]['outMilePreDate']         = $_POST['outMilePreDate'][$key];
            $outMileInfoList[$key]['outMileRealDate']         = $_POST['outMileRealDate'][$key];
            $outMileInfoList[$key]['outMileMark']         = $_POST['outMileMark'][$key];

        }

        //风险信息
        $riskInfoArr = json_decode($_POST['risks'],true);

        /**
         * strategy 应对策略
         * prevention 预防措施
         * remedy 应急措施
         *
         */
        $riskInfoList = [];
        foreach ($riskInfoArr as $key=>$riskInfo){
            $riskInfoList[$key]['projectID'] = $_POST['projectId'];
            $riskInfoList[$key]['reportRiskMark'] = $riskInfo['name'];
            $riskInfoList[$key]['reportRiskStatus'] = $riskInfo['status'];
            $riskInfoList[$key]['reportRiskStrategy'] = $riskInfo['strategy'];
            $riskInfoList[$key]['reportRiskPrevention'] = $riskInfo['prevention'];
            $riskInfoList[$key]['reportRiskRemedy'] = $riskInfo['remedy'];
            $riskInfoList[$key]['reportRiskResolution'] = $riskInfo['resolution'];

        }
        //周报基础信息
        $weeklyReportBasicInfo = [
            'projectId'=>$_POST['projectId'],
            'projectName'=>$_POST['projectName'],
            'projectCode'=>$_POST['projectCode'],
            'projectAlias'=>$_POST['projectAlias'],
            'projectType'=>$_POST['projectType'],
            'devDept'=>$_POST['devDept'],
            'pm'=>$_POST['pm'],
            'isImportant'=>$_POST['isImportant'],
            'projectplanYear'=>$_POST['projectplanYear'],
            'projectStartDate'=>$_POST['projectStartDate'],
            'projectEndDate'=>$_POST['projectEndDate'],
            'relationRequirement'=>$_POST['relationRequirement'],
            'reportStartDate'=>$_POST['reportStartDate'],
            'reportEndDate'=>$_POST['reportEndDate'],
            'weeknum'=>$_POST['weeknum'],
            'projectStage'=>$_POST['projectStage'],
            'projectProgress'=>$_POST['projectProgress'],
            'progressStatus'=>$_POST['progressStatus'],
            'mileDelayNum'=>$_POST['mileDelayNum'],
            'mileDelayMark'=>$_POST['mileDelayMark'],
            'projectProgressMark'=>$_POST['projectProgressMark'],
            'projectTransDesc'=>$_POST['projectTransDesc'],
            'productBuilds'=>$_POST['productBuilds'],
            'projectAbnormalDesc'=>$_POST['projectAbnormalDesc'],
            'nextWeekplan'=>$_POST['nextWeekplan'],
            'outPlanId'=>$_POST['outPlanId'],
            'remark'=>$_POST['remark'],
            'qa'=>$_POST['qa'],
            'planID'=>$_POST['planID'],
            'deleted'=>0,
            'createTime'=>helper::now(),
//            'updateTime'=>helper::now(),
            'createdBy'=>$this->app->user->account,

            'productPlan'=>'',

        ];

        /*a($_POST);
        a("介质");
        a($mediumInfoList);
        a("外部里程碑");
        a($outMileInfoList);
        a("内部里程碑");
        a($insideMileInfoList);
        a("周报基本信息");
        a($weeklyReportBasicInfo);*/
        $this->dao->begin();


        try {
            //写入主表
            $this->dao->insert(TABLE_PROJECTWEEKLYREPORT)->data($weeklyReportBasicInfo)->exec();
            $lastInsertID = $this->dao->lastInsertID();

            //介质
            foreach ($mediumInfoList as $medium){
                $medium['weekreportID'] = $lastInsertID;
                $this->dao->insert(TABLE_PROJECTWEEKLYREPORT_MEDIUM)->data($medium)->exec();

            }
            //外部里程碑
            foreach ($outMileInfoList as $outMile){
                $outMile['weekreportID'] = $lastInsertID;
                $this->dao->insert(TABLE_PROJECTWEEKLYREPORT_OUTMILE)->data($outMile)->exec();

            }
            //内部里程碑
            foreach ($insideMileInfoList as $insideMile){
                $insideMile['weekreportID'] = $lastInsertID;
                $this->dao->insert(TABLE_PROJECTWEEKLYREPORT_INSIDEMILE)->data($insideMile)->exec();

            }

            //风险信息
            foreach ($riskInfoList as $risk){
                $risk['weekreportID'] = $lastInsertID;
                $this->dao->insert(TABLE_PROJECTWEEKLYREPORT_RISK)->data($risk)->exec();

            }


            if(dao::isError()){
                $this->dao->rollBack();
                return false;
            }
            $this->dao->commit();
            return $lastInsertID;
        }catch (Error $e){
            dao::$errors[] = $e->getMessage();
            $this->dao->rollBack();
            return false;
        }





    }


    public function reportdelete($appID){
        $this->dao->update(TABLE_PROJECTWEEKLYREPORT)->set('deleted')->eq(1)->where('id')->eq($appID)->exec();

        $this->loadModel('action')->create('weeklyreport', $appID, 'deleted', '', $extra = ACTIONMODEL::CAN_UNDELETED);
    }

    public function getProjectQA($deptids){

        $QAArr = [];
        $deptList = $this->dao->select("qa")->from(TABLE_DEPT)->where('id')->in($deptids)->fetchAll();
        foreach ($deptList as $dept){
            if($dept->qa){
                $tempQA = explode(',',$dept->qa);
                foreach ($tempQA as $val){
                    if($val){
                        $QAArr[] = $val;
                    }
                }
            }
        }
        if($QAArr){
            $QAArr = array_unique($QAArr);
        }
        return $QAArr;
    }


    /**
     * Update application.
     * 编辑
     * @access int $appID
     * @access public
     * @return void
     */
    public function update($reportId)
    {
        if(!isset($_POST['weeknum']) || !$_POST['weeknum'] || !is_numeric($_POST['weeknum'])){
            dao::$errors['weeknum'] = sprintf($this->lang->weeklyreport->errorNotempty,$this->lang->weeklyreport->weeknum);
            return false;
        }
        if(isset($_POST['mileDelayNum']) && $_POST['mileDelayNum'] && !is_numeric($_POST['mileDelayNum'])){
            dao::$errors['mileDelayNum'] = $this->lang->weeklyreport->milesDelyVarTypeError;
            return false;
        }
        $report = $this->getByID($_POST['reportId']);
        if(!$report){
            dao::$errors[] = $this->lang->weeklyreport->reportNoExistError;
            return false;
        }
        if(!$_POST['reportStartDate']){
            dao::$errors['reportStartDate'] = vsprintf($this->lang->weeklyreport->errorNotempty,$this->lang->weeklyreport->reportStartDate);
            return false;
        }
        if(!$_POST['reportEndDate']){
            dao::$errors['reportEndDate'] = vsprintf($this->lang->weeklyreport->errorNotempty,$this->lang->weeklyreport->reportEndDate);
            return false;
        }
        if(!$_POST['reportId']){
            dao::$errors[] = $this->lang->weeklyreport->reportOwnerProjectError;
            return false;
        }
        $otherreport = $this->getWeekReportByProjectAndWeeknum($report->projectId,$_POST['weeknum']);

        if($otherreport && $otherreport->id != $_POST['reportId'] ){
            dao::$errors[] = $this->lang->weeklyreport->reportExistError;
            return false;
        }
        $isQA = $this->authUserReportQA($this->app->user->account,$report->devDept);
        if(!$isQA){
            dao::$errors[] = $this->lang->weeklyreport->nopermissionedit;
            return false;
        }

        //内部里程碑 需要校验值
        $insideMileInfoList = [];
        $upinsideMileInfoList = [];
        $insideMileIDArr = [];
        $itemnum = 1;
        foreach ($_POST['insideMileStage'] as $key=>$insideMileStage){

            if(!isset($_POST['insideMileName'][$key]) || !$_POST['insideMileName'][$key]){
                dao::$errors[] = vsprintf($this->lang->weeklyreport->errorNotempty,['第 '.$itemnum.' 条内部'.$this->lang->weeklyreport->insideMileName]);
                return false;
            }

            if(isset($_POST['InsidemileID'][$key]) && $_POST['InsidemileID'][$key]) {
                $upinsideMileInfoList[$_POST['InsidemileID'][$key]]['insideMileStage']    = $insideMileStage;
                $upinsideMileInfoList[$_POST['InsidemileID'][$key]]['insideMileName']         = $_POST['insideMileName'][$key];
                $upinsideMileInfoList[$_POST['InsidemileID'][$key]]['projectID']         = $report->projectId;
                $upinsideMileInfoList[$_POST['InsidemileID'][$key]]['insideMilePreDate']         = $_POST['insideMilePreDate'][$key];
                $upinsideMileInfoList[$_POST['InsidemileID'][$key]]['insideMileRealDate']         = $_POST['insideMileRealDate'][$key];
                $upinsideMileInfoList[$_POST['InsidemileID'][$key]]['insideMileMark']         = $_POST['insideMileMark'][$key];
                $insideMileIDArr[] = $_POST['InsidemileID'][$key];
            }else{
                $insideMileInfoList[$key]['insideMileStage']    = $insideMileStage;
                $insideMileInfoList[$key]['insideMileName']         = $_POST['insideMileName'][$key];
                $insideMileInfoList[$key]['projectID']         = $report->projectId;
                $insideMileInfoList[$key]['insideMilePreDate']         = $_POST['insideMilePreDate'][$key];
                $insideMileInfoList[$key]['insideMileRealDate']         = $_POST['insideMileRealDate'][$key];
                $insideMileInfoList[$key]['insideMileMark']         = $_POST['insideMileMark'][$key];
            }


            $itemnum++;

        }

        $mediumInfoList = [];
        $upmediumInfoList = [];
        $mediumIDArr = [];
        //介质信息
        foreach ($_POST['mediumName'] as $key=>$mediumName){
            //如果存在id说明要更新
            if(isset($_POST['MediumID'][$key]) && $_POST['MediumID'][$key]) {
                $upmediumInfoList[$_POST['MediumID'][$key]]['mediumName']    = $mediumName;
                $upmediumInfoList[$_POST['MediumID'][$key]]['mediumOutsideplanTask']         = $_POST['mediumOutsideplanTask'][$key];
                $upmediumInfoList[$_POST['MediumID'][$key]]['projectID']         = $report->projectId;
                $upmediumInfoList[$_POST['MediumID'][$key]]['preMediumPublishDate']         = $_POST['preMediumPublishDate'][$key];
                $upmediumInfoList[$_POST['MediumID'][$key]]['preMediumOnlineDate']         = $_POST['preMediumOnlineDate'][$key];
                $upmediumInfoList[$_POST['MediumID'][$key]]['realMediumPublishDate']         = $_POST['realMediumPublishDate'][$key];
                $upmediumInfoList[$_POST['MediumID'][$key]]['realMediumOnlineDate']         = $_POST['realMediumOnlineDate'][$key];
                if(isset($_POST['mediumRequirement'][$key])){
                    $upmediumInfoList[$_POST['MediumID'][$key]]['mediumRequirement']         = implode(",",$_POST['mediumRequirement'][$key]);
                }else{
                    $upmediumInfoList[$_POST['MediumID'][$key]]['mediumRequirement']         = "";
                }

                $upmediumInfoList[$_POST['MediumID'][$key]]['mediumMark']         = $_POST['mediumMark'][$key];
                $mediumIDArr[] = $_POST['MediumID'][$key];
            }else{
                $mediumInfoList[$key]['mediumName']    = $mediumName;
                $mediumInfoList[$key]['mediumOutsideplanTask']         = $_POST['mediumOutsideplanTask'][$key];
                $mediumInfoList[$key]['projectID']         = $report->projectId;
                $mediumInfoList[$key]['preMediumPublishDate']         = $_POST['preMediumPublishDate'][$key];
                $mediumInfoList[$key]['preMediumOnlineDate']         = $_POST['preMediumOnlineDate'][$key];
                $mediumInfoList[$key]['realMediumPublishDate']         = $_POST['realMediumPublishDate'][$key];
                $mediumInfoList[$key]['realMediumOnlineDate']         = $_POST['realMediumOnlineDate'][$key];
                if(isset($_POST['mediumRequirement'][$key])){
                    $mediumInfoList[$key]['mediumRequirement']         = implode(",",$_POST['mediumRequirement'][$key]);
                }else{
                    $mediumInfoList[$key]['mediumRequirement']         = "";
                }

                $mediumInfoList[$key]['mediumMark']         = $_POST['mediumMark'][$key];
            }



        }


        //外部里程碑
        $outMileInfoList = [];
        $upoutMileInfoList = [];
        $outMileIDArr = [];

        foreach ($_POST['outMileStageName'] as $key=>$outMileStageName){

            if(isset($_POST['OutmileID'][$key]) && $_POST['OutmileID'][$key]) {
                $upoutMileInfoList[$_POST['OutmileID'][$key]]['outMileStageName']    = $outMileStageName;
                $upoutMileInfoList[$_POST['OutmileID'][$key]]['outMileName']         = $_POST['outMileName'][$key];
                $upoutMileInfoList[$_POST['OutmileID'][$key]]['projectID']         = $report->projectId;
                $upoutMileInfoList[$_POST['OutmileID'][$key]]['outMilePreDate']         = $_POST['outMilePreDate'][$key];
                $upoutMileInfoList[$_POST['OutmileID'][$key]]['outMileRealDate']         = $_POST['outMileRealDate'][$key];
                $upoutMileInfoList[$_POST['OutmileID'][$key]]['outMileMark']         = $_POST['outMileMark'][$key];
                $outMileIDArr[] = $_POST['OutmileID'][$key];
            }else{
                $outMileInfoList[$key]['outMileStageName']    = $outMileStageName;
                $outMileInfoList[$key]['outMileName']         = $_POST['outMileName'][$key];
                $outMileInfoList[$key]['projectID']         = $report->projectId;
                $outMileInfoList[$key]['outMilePreDate']         = $_POST['outMilePreDate'][$key];
                $outMileInfoList[$key]['outMileRealDate']         = $_POST['outMileRealDate'][$key];
                $outMileInfoList[$key]['outMileMark']         = $_POST['outMileMark'][$key];
            }


        }
 /*       a("介质信息start");
        a($_POST['MediumID']);
        a($upmediumInfoList);
        a($mediumInfoList);
        a($mediumIDArr);
        a("介质信息end");

        a("外部里程碑start");
        a($_POST['OutmileID']);
        a($upoutMileInfoList);
        a($outMileInfoList);
        a($outMileIDArr);
        a("外部里程碑end");

        a("内部里程碑start");
        a($_POST['InsidemileID']);
        a($upinsideMileInfoList);
        a($insideMileInfoList);
        a($insideMileIDArr);
        a("内部里程碑end");*/
//        exit();
        //风险信息
        $riskInfoArr = json_decode($_POST['risks'],true);

        /**
         * strategy 应对策略
         * prevention 预防措施
         * remedy 应急措施
         *
         */
        $riskInfoList = [];
        foreach ($riskInfoArr as $key=>$riskInfo){
            $riskInfoList[$key] = new stdClass();
            $riskInfoList[$key]->weekreportID = $_POST['reportId'];
            $riskInfoList[$key]->projectID = $report->projectId;
            $riskInfoList[$key]->reportRiskMark = $riskInfo['name'];
            $riskInfoList[$key]->reportRiskStatus = $riskInfo['status'];
            $riskInfoList[$key]->reportRiskStrategy = $riskInfo['strategy'];
            $riskInfoList[$key]->reportRiskPrevention = $riskInfo['prevention'];
            $riskInfoList[$key]->reportRiskRemedy = $riskInfo['remedy'];
            $riskInfoList[$key]->reportRiskResolution = $riskInfo['resolution'];
            /*$riskInfoList[$key]['projectID'] = $report->projectId;
            $riskInfoList[$key]['reportRiskMark'] = $riskInfo['name'];
            $riskInfoList[$key]['reportRiskStatus'] = $riskInfo['status'];
            $riskInfoList[$key]['reportRiskStrategy'] = $riskInfo['strategy'];
            $riskInfoList[$key]['reportRiskPrevention'] = $riskInfo['prevention'];
            $riskInfoList[$key]['reportRiskRemedy'] = $riskInfo['remedy'];
            $riskInfoList[$key]['reportRiskResolution'] = $riskInfo['resolution'];*/

        }
        //周报基础信息
        $weeklyReportBasicInfo = [

            'projectName'=>$_POST['projectName'],
            'projectCode'=>$_POST['projectCode'],
            'projectAlias'=>$_POST['projectAlias'],
            'projectType'=>$_POST['projectType'],
            'devDept'=>$_POST['devDept'],
            'pm'=>$_POST['pm'],
            'isImportant'=>$_POST['isImportant'],
            'projectplanYear'=>$_POST['projectplanYear'],
            'projectStartDate'=>$_POST['projectStartDate'],
            'projectEndDate'=>$_POST['projectEndDate'],
            'relationRequirement'=>$_POST['relationRequirement'],
            'reportStartDate'=>$_POST['reportStartDate'],
            'reportEndDate'=>$_POST['reportEndDate'],
            'weeknum'=>$_POST['weeknum'],
            'projectStage'=>$_POST['projectStage'],
            'projectProgress'=>$_POST['projectProgress'],
            'progressStatus'=>$_POST['progressStatus'],
            'mileDelayNum'=>$_POST['mileDelayNum'],
            'mileDelayMark'=>$_POST['mileDelayMark'],
            'projectProgressMark'=>$_POST['projectProgressMark'],
            'projectTransDesc'=>$_POST['projectTransDesc'],
            'productBuilds'=>$_POST['productBuilds'],
            'projectAbnormalDesc'=>$_POST['projectAbnormalDesc'],
            'nextWeekplan'=>$_POST['nextWeekplan'],
            'outPlanId'=>$_POST['outPlanId'],
            'remark'=>$_POST['remark'],
            'qa'=>$_POST['qa'],
            'planID'=>$_POST['planID'],
            'deleted'=>0,
            'updateTime'=>helper::now(),
            'editedBy'=>$this->app->user->account,
            'productPlan'=>'',
//            'produceStatus'=>0,

        ];

        /*a($_POST);
        a("介质");
        a($mediumInfoList);
        a("外部里程碑");
        a($outMileInfoList);
        a("内部里程碑");
        a($insideMileInfoList);
        a("周报基本信息");
        a($weeklyReportBasicInfo);*/
        $this->dao->begin();
        try {
            //写入主表
            $this->dao->update(TABLE_PROJECTWEEKLYREPORT)->data($weeklyReportBasicInfo)->where("id")->eq($_POST['reportId'])->exec();


            //介质
            $this->dao->delete()->from(TABLE_PROJECTWEEKLYREPORT_MEDIUM)->where("weekreportID")->eq($_POST['reportId'])->andWhere('id')->notin($mediumIDArr)->exec();
            foreach ($mediumInfoList as $medium){
                $medium['weekreportID'] = $_POST['reportId'];
                $this->dao->insert(TABLE_PROJECTWEEKLYREPORT_MEDIUM)->data($medium)->exec();

            }
            //介质更新
            foreach ($upmediumInfoList as $meidumID=>$medium){
                $medium['weekreportID'] = $_POST['reportId'];
                $this->dao->update(TABLE_PROJECTWEEKLYREPORT_MEDIUM)->data($medium)->where('id')->eq($meidumID)->exec();

            }

            //外部里程碑
            $this->dao->delete()->from(TABLE_PROJECTWEEKLYREPORT_OUTMILE)->where("weekreportID")->eq($_POST['reportId'])->andWhere('id')->notin($outMileIDArr)->exec();
            foreach ($outMileInfoList as $outMile){
                $outMile['weekreportID'] = $_POST['reportId'];
                $this->dao->insert(TABLE_PROJECTWEEKLYREPORT_OUTMILE)->data($outMile)->exec();

            }
            //更新
            foreach ($upoutMileInfoList as $outMileID=>$outMile){
                $outMile['weekreportID'] = $_POST['reportId'];
                $this->dao->update(TABLE_PROJECTWEEKLYREPORT_OUTMILE)->data($outMile)->where('id')->eq($outMileID)->exec();

            }


            //内部里程碑
            $this->dao->delete()->from(TABLE_PROJECTWEEKLYREPORT_INSIDEMILE)->where("weekreportID")->eq($_POST['reportId'])->andWhere('id')->notin($insideMileIDArr)->exec();
            foreach ($insideMileInfoList as $insideMile){
                $insideMile['weekreportID'] = $_POST['reportId'];
                $this->dao->insert(TABLE_PROJECTWEEKLYREPORT_INSIDEMILE)->data($insideMile)->exec();

            }
            foreach ($upinsideMileInfoList as $insideMildID=>$insideMile){
                $insideMile['weekreportID'] = $_POST['reportId'];
                $this->dao->update(TABLE_PROJECTWEEKLYREPORT_INSIDEMILE)->data($insideMile)->where('id')->eq($insideMildID)->exec();

            }
            if($report->reportRisk){
                foreach ($report->reportRisk as $reportRisk){
                    unset($reportRisk->id);
                }
            }
            $riskupflag = false;
            if(base64_encode(json_encode($riskInfoList)) != base64_encode(json_encode($report->reportRisk))){
                $riskupflag = true;
                //风险信息
                $this->dao->delete()->from(TABLE_PROJECTWEEKLYREPORT_RISK)->where("weekreportID")->eq($_POST['reportId'])->exec();
                foreach ($riskInfoList as $risk){
//                $risk->weekreportID = $_POST['reportId'];
                    $this->dao->insert(TABLE_PROJECTWEEKLYREPORT_RISK)->data($risk)->exec();

                }
            }



            if(dao::isError()){
                $this->dao->rollBack();

                return ['code'=>0,'changes'=>[]];
            }

            $this->dao->commit();
            $newreport = $this->getByID($_POST['reportId']);
            //获取扩展信息
            $extChangeInfo = [];

            if(base64_encode(json_encode($newreport->reportMedium)) != base64_encode(json_encode($report->reportMedium))){
                $extChangeInfo[$this->lang->weeklyreport->mediumDetails] = new stdClass();
                $extChangeInfo[$this->lang->weeklyreport->mediumDetails]->new = base64_encode(json_encode($newreport->reportMedium));
                $extChangeInfo[$this->lang->weeklyreport->mediumDetails]->old = base64_encode(json_encode($report->reportMedium));
            }




            if(base64_encode(json_encode($newreport->reportOutmile)) != base64_encode(json_encode($report->reportOutmile))){
                $extChangeInfo[$this->lang->weeklyreport->externalMilestones] = new stdClass();
                $extChangeInfo[$this->lang->weeklyreport->externalMilestones]->new = base64_encode(json_encode($newreport->reportOutmile));
                $extChangeInfo[$this->lang->weeklyreport->externalMilestones]->old = base64_encode(json_encode($report->reportOutmile));
            }


            if(base64_encode(json_encode($newreport->reportInsidemile)) != base64_encode(json_encode($report->reportInsidemile))){
                $extChangeInfo[$this->lang->weeklyreport->internalMilestones] = new stdClass();
                $extChangeInfo[$this->lang->weeklyreport->internalMilestones]->new = base64_encode(json_encode($newreport->reportInsidemile));
                $extChangeInfo[$this->lang->weeklyreport->internalMilestones]->old = base64_encode(json_encode($report->reportInsidemile));
            }



            if($riskupflag){
                $extChangeInfo[$this->lang->weeklyreport->projectRiskSituation] = new stdClass();
                $extChangeInfo[$this->lang->weeklyreport->projectRiskSituation]->new = base64_encode(json_encode($riskInfoList));
                $extChangeInfo[$this->lang->weeklyreport->projectRiskSituation]->old = base64_encode(json_encode($report->reportRisk));
            }


            return ['code'=>200,'changes'=>common::createNewChanges($report, $newreport,$extChangeInfo)];

        }catch (Error $e){
            dao::$errors[] = $e->getMessage();
            $this->dao->rollBack();
            return ['code'=>0,'changes'=>[]];
        }


    }
    public function getWeekReportByProjectAndWeeknum($project,$weeknum,$field="*"){
        return $this->dao->select($field)->from(TABLE_PROJECTWEEKLYREPORT)->where("projectId")->eq($project)->andWhere("weeknum")->eq($weeknum)->andWhere('deleted')->eq(0)->fetch();
    }
    /**
     * Create a application.
     * 新建周报
     * @access public
     * @return void
     */
    public function createbak()
    {
        $i = 0;
        $productPlan = [];
        foreach ($_POST['productPlanCode'] as $productPlanCode){
            $temp['productPlanCode']    = $productPlanCode;
            $temp['preRelease']         = $_POST['preRelease'][$i];
            $temp['preOnline']          = $_POST['preOnline'][$i];
            $temp['realRelease']        = $_POST['realRelease'][$i];
            $temp['realOnline']         = $_POST['realOnline'][$i];
            $productPlan[] = $temp;
            $i++;
        }
        $_POST['progressStatus'] = implode(',', $_POST['progressStatus']);

        //没有关联(外部)项目/任务时 避免错误
        $_POST['outProjectCode'] = strlen($_POST['outProjectCode']) > 50 ? "" : $_POST['outProjectCode'];
        $_POST['outProjectName'] = strlen($_POST['outProjectName']) > 50 ? "" : $_POST['outProjectName'];
        $_POST['outPlanStartDate'] = strlen($_POST['outPlanStartDate']) > 50 ? "": $_POST['outPlanStartDate'];
        $_POST['outPlanWorkload'] = strlen($_POST['outPlanWorkload']) > 10 ? "": $_POST['outPlanWorkload'];
        $_POST['outPlanEndDate'] = strlen($_POST['outPlanEndDate']) > 10 ? "": $_POST['outPlanEndDate'];

        $app = fixer::input('post')
            ->add('createdBy', $this->app->user->account)
            ->add('editedBy', $this->app->user->account)
            ->add('createTime', helper::now())
            ->add('updateTime', helper::now())
            ->add('productPlan', "")
            ->add('deleted', 0)
            ->remove('uid')
            ->remove('productPlanCode')
            ->remove('preRelease')
            ->remove('realOnline')
            ->remove('preOnline')
            ->remove('realRelease')
            ->get();
        $app->productPlan = json_encode($productPlan); //避免被fixer::input转译
        $this->dao->insert(TABLE_PROJECTWEEKLYREPORT)->data($app)->autoCheck()->batchCheck($this->config->weeklyreport->create->requiredFields, 'notempty')->exec();
        $lastInsertID = $this->dao->lastInsertID();
        $this->loadModel('action')->create('projectweeklyreport', $lastInsertID, 'created', '', '');
        return $lastInsertID;
    }
    /**
     * 检查用户是否已有当前日期的周报
     */
    public function checkDate($projectId, $startDate, $endDate, $reportId = 0)
    {
        $sql    = 'select id, reportStartDate, reportEndDate from '. TABLE_PROJECTWEEKLYREPORT.
            ' where deleted = 0 and projectId = '.$projectId .
            ' and ((reportStartDate between "'. $startDate .'" and "'.$endDate.'") 
                or (reportEndDate between "'.$startDate.'" and "'.$endDate.'")) ';
        if($reportId) {
            $sql .= ' and id <>' . $reportId;
        }

        $record = $this->dbh->query($sql)->fetch();
        if(empty($record)){
            return true; //如果没有记录 返回可以
        }
        dao::$errors['reportDateUnavailable'] = sprintf($this->config->weeklyreport->reportDateUnavailable, $record->reportStartDate, $record->reportEndDate);
        return false;
    }
    /**
     * 根据周报id 获取周报内容
     * @param  int    $appID
     * @access public
     * @return void
     */
    public function getByID($reportId)
    {
        $report = $this->dao->select('*')->from(TABLE_PROJECTWEEKLYREPORT)
            ->where('id')->eq($reportId)
            ->andwhere('deleted')->eq(0)
            ->fetch();
        if($report){
            $report->reportMedium = $this->dao->select("*")->from(TABLE_PROJECTWEEKLYREPORT_MEDIUM)->where("weekreportID")->eq($reportId)->fetchAll();
            $report->reportOutmile = $this->dao->select("*")->from(TABLE_PROJECTWEEKLYREPORT_OUTMILE)->where("weekreportID")->eq($reportId)->fetchAll();
            $report->reportInsidemile = $this->dao->select("*")->from(TABLE_PROJECTWEEKLYREPORT_INSIDEMILE)->where("weekreportID")->eq($reportId)->fetchAll();
            $report->reportRisk = $this->dao->select("*")->from(TABLE_PROJECTWEEKLYREPORT_RISK)->where("weekreportID")->eq($reportId)->fetchAll();
        }
        return $report;
    }

    /**
     * 根据项目id 获取周报内容
     * @param $projectId
     * @return false|mixed
     */
    public function getByProjectId($projectId)
    {
        $report = $this->dao->select('*')->from(TABLE_PROJECTWEEKLYREPORT)
            ->where('projectId')->eq($projectId)
            ->andwhere('reportStartDate')->le(date('Y-m-d'))
            ->andwhere('reportEndDate')->ge(date('Y-m-d'))
            ->andwhere('deleted')->eq(0)
            ->fetchAll('projectId');
        return current($report);
    }

    /**
     * 根据项目id 获取最新周报
     * @param $projectId
     * @return false|mixed
     */
    public function getByLastProjectId($projectId)
    {
        $report = $this->dao->select('*')->from(TABLE_PROJECTWEEKLYREPORT)
            ->where('projectId')->eq($projectId)
            ->andwhere('deleted')->eq(0)
            ->orderBy("id desc")
            ->fetch();
        if($report){
            $report->reportMedium = $this->dao->select("*")->from(TABLE_PROJECTWEEKLYREPORT_MEDIUM)->where("weekreportID")->eq($report->id)->fetchAll();
            $report->reportOutmile = $this->dao->select("*")->from(TABLE_PROJECTWEEKLYREPORT_OUTMILE)->where("weekreportID")->eq($report->id)->fetchAll();
            $report->reportInsidemile = $this->dao->select("*")->from(TABLE_PROJECTWEEKLYREPORT_INSIDEMILE)->where("weekreportID")->eq($report->id)->fetchAll();
            $report->reportRisk = $this->dao->select("*")->from(TABLE_PROJECTWEEKLYREPORT_RISK)->where("weekreportID")->eq($report->id)->fetchAll();
        }
        return $report;
    }


    /**
     * Update application.
     * 编辑
     * @access int $appID
     * @access public
     * @return void
     */
    public function updatebak()
    {
        $i = 0;
        foreach ($_POST['productPlanCode'] as $productPlanCode){
            $temp['productPlanCode']    = $productPlanCode;
            $temp['preRelease']         = $_POST['preRelease'][$i];
            $temp['preOnline']          = $_POST['preOnline'][$i];
            $temp['realRelease']        = $_POST['realRelease'][$i];
            $temp['realOnline']         = $_POST['realOnline'][$i];
            $productPlan[] = $temp;
            $i++;
        }
        $progressStatus = implode(',', $_POST['progressStatus']);
        $this->dao->update(TABLE_PROJECTWEEKLYREPORT)
            ->set('updateTime')->eq( helper::now())
            ->set('editedBy')->eq($this->app->user->account)
            ->set('productPlan')->eq(json_encode($productPlan))
            ->set('reportStartDate')->eq($_POST['reportStartDate'])
            ->set('reportEndDate')->eq($_POST['reportEndDate'])
            ->set('progressStatus')->eq( $progressStatus)
            ->set('insideStatus')->eq($_POST['insideStatus'])
            ->set('outsideStatus')->eq($_POST['outsideStatus'])
            ->set('reportDesc')->eq($_POST['reportDesc'])
            ->set('insideMilestone')->eq($_POST['insideMilestone'])
            ->set('outsideMilestone')->eq($_POST['outsideMilestone'])
            ->set('outsideStatus')->eq($_POST['outsideStatus'])
            ->set('transDesc')->eq($_POST['transDesc'])
            ->set('remark')->eq($_POST['remark'])
            ->where('id')->eq((int)$_POST['reportId'])
            ->exec();
        $this->loadModel('action')->create('projectweeklyreport', $_POST['reportId'], 'edited', '', '');
    }


    public function getWeekMyActionAndEnd($first = 1)
    {
        //当前日期
        $time = time();
        $sdefaultDate = date("Y-m-d", $time);
        //$first =1 表示每周星期一为开始日期 0表示每周日为开始日期
        //获取当前周的第几天 周日是 0 周一到周六是 1 - 6
        $w = date('w', strtotime($sdefaultDate));
        //获取本周开始日期，如果$w是0，则表示周日，减去 6 天
        $week_start = date('Y-m-d', strtotime("$sdefaultDate -" . ($w ? $w - $first : 6) . ' days'));
        //本周结束日期
        $week_end = date('Y-m-d', strtotime("$week_start +4 days"));
        return array("week_start" => $week_start, "week_end" => $week_end);
    }
    /**
     * GetPageNav
     *
     * @param  int    $project
     * @access public
     * @return string
     */
    public function getPageNavByProject($project, $startDate, $endDate,$weeknum=0)
    {

        $selectHtml  = "<div class='btn-group angle-btn' title='".$project->name."'>";

        $selectHtml .= html::a('###', ''.$this->lang->weeklyreport->common.'-'  . $project->name, '', "class='btn customNav'");
        $selectHtml .= '</div>';

        $selectHtml .= "<div class='btn-group angle-btn'>";
        $selectHtml .= "<div class='btn-group'>";
        if($weeknum>0){
            $line = $startDate.'~'.$endDate. ' 第'.$weeknum.'周';
        }else{
            $line = $startDate.'~'.$endDate;
        }

        $selectHtml .= "<a data-toggle='dropdown' class='btn' title=$line>" . $line . " <span class='caret'></span></a>";
        $selectHtml .= "<ul class='dropdown-menu'>";

        $list = $this->dao->select('id,projectId,reportStartDate,reportEndDate,weeknum')->from(TABLE_PROJECTWEEKLYREPORT)
            ->where('projectId')->eq($project->id)
            ->andwhere('deleted')->eq(0)
            ->orderBy('reportStartDate_desc')
            ->fetchAll();
        foreach($list as $item)
        {
            $line = $item->reportStartDate . '~' . $item->reportEndDate;
            $selectHtml .= '<li><a href="weeklyreport-index-'.$item->projectId.'-'.$item->id.'.html#app=project">' .  $line  . ' 第'.$item->weeknum.'周</a></li>';
        }
        $selectHtml .='</ul></div></div>';
        return $selectHtml;

    }
    /**
     * GetWeekPairs
     *
     * @param  int    $begin
     * @access public
     * @return array
     */
    public function getWeekPairs($begin, $end = '')
    {
        $sn = $end != '' ? $this->getWeekSN($begin, $end) : $this->getWeekSN($begin, date('Y-m-d'));
        $weeks = array();
        for($i = 0; $i <= $sn; $i++)
        {
            $monday = $this->getThisMonday($begin);
            $sunday = $this->getThisSunday($begin);
            $begin = date('Y-m-d', strtotime("$begin +7 days"));
            $key = date('Ymd', strtotime($monday));
            $weeks[$key] = sprintf($this->lang->weeklyreport->weekDesc, $i + 1, $monday, $sunday);
        }
        krsort($weeks);
        return $weeks;
    }
    /**
     * GetWeekSN
     *
     * @param  int    $begin
     * @param  int    $date
     * @access public
     * @return int
     */
    public function getWeekSN($begin, $date)
    {
        return ceil((strtotime($date) - strtotime($begin)) / 7 / 86400);
    }

    /**
     * Get monday for a date.
     *
     * @param  int $date
     * @access public
     * @return date
     */
    public function getThisMonday($date)
    {
        $day = date('w', strtotime($date));
        if($day == 0) $day = 7;
        $days = $day - 1;
        return date('Y-m-d', strtotime("$date - $days days"));
    }

    /**
     * GetThisSunday
     *
     * @param  int    $date
     * @access public
     * @return date
     */
    public function getThisSunday($date)
    {
        $monday = $this->getThisMonday($date);
        return date('Y-m-d', strtotime("$monday +6 days"));
    }

    /**
     * GetLastDay
     * 获取周1 和周日
     * @param  int    $date
     * @access public
     * @return string
     */
    public function getLastDay($date)
    {
        $this->loadModel('project');
        $weekend  = zget($this->config->project, 'weekend', 2);
        $monday   = $this->getThisMonday($date);
        $sunday   = $this->getThisSunday($date);
        $workdays = $this->loadModel('holiday')->getActualWorkingDays($monday, $sunday);
        return end($workdays);
    }

    /**
     * 获取项目风险
     * @param $projectID
     * @return string
     */
    public function getProjectRisks($projectID)
    {
        $risks = $this->dao->select('*')->from(TABLE_RISK)
            ->where('project')->eq($projectID)
            ->andwhere('pri')->eq('high')
            ->andwhere('deleted')->eq(0)
            ->fetchAll();
        $chose['hangup'] = '挂起';
        $chose['closed'] = '关闭';
        $chose['active'] = '开发';
        $chose['canceled'] = '取消';
        $list = [];
        foreach ($risks as $item){
            $risk['name'] = $item->name;
            $risk['resolution'] = $item->resolution;
            $risk['status'] = $chose[$item->status] . "";
            $list[] = $risk;
        }
        return base64_encode(json_encode($list));
    }

    /**
     * 获取项目风险
     * @param $projectID
     * @return string
     */
    public function getProjectRisksNew($projectID)
    {
        $risks = $this->dao->select('*')->from(TABLE_RISK)
            ->where('project')->eq($projectID)
            ->andwhere('pri')->eq('high')
            ->andwhere('deleted')->eq(0)
            ->fetchAll();
        $chose['hangup'] = '挂起';
        $chose['closed'] = '关闭';
        $chose['active'] = '开发';
        $chose['canceled'] = '取消';
        $list = [];
        foreach ($risks as $item){
            $risk['name'] = $item->name;
            $risk['resolution'] = strip_tags($item->resolution);
            $risk['prevention'] = strip_tags($item->prevention);
            $risk['strategy'] = $item->strategy;
            $risk['remedy'] = strip_tags($item->remedy);
//            $risk['status'] = $chose[$item->status] . "";
            $risk['status'] = $item->status;
            if($item->status != 'active'){
                $risk['status'] = 'closed';
            }

            $list[] = $risk;
        }
        return $list;
    }

    /**
     * 获取项目问题
     * @param $projectID
     * @return string
     */
    public function getProjectIssues($projectID)
    {
        $risks = $this->dao->select('*')->from(TABLE_ISSUE)
            ->where('project')->eq($projectID)
            ->andwhere('severity')->le(2)
            ->andwhere('deleted')->eq(0)
            ->fetchAll();
        $statusList['unconfirmed'] = '待确认';
        $statusList['confirmed']   = '已确认';
        $statusList['resolved']    = '已解决';
        $statusList['canceled']    = '取消';
        $statusList['closed']      = '已关闭';
        $statusList['active']      = '激活';
        $list = [];
        foreach ($risks as $item){
            $risk['name'] = $item->title;
            $risk['resolution'] = $item->resolutionComment;
            $risk['status'] = $statusList[$item->status] . "";
            $list[] = $risk;
        }
        return base64_encode(json_encode($list));
    }

    /**
     * 获取需求条目
     * @param $projectID
     * @return mixed
     */
    public function getProjectRequirements($projectID)
    {
        $list = $this->dao->select('id,name')->from(TABLE_REQUIREMENT)
            ->where('project')->eq($projectID)
            ->fetchAll();
        return $list;
    }

    /**
     * 获取需求条目
     * @param $projectID
     * @return mixed
     */
    public function getProjectRequirementsNew($projectID)
    {
        $list = $this->dao->select('id,name,code')->from(TABLE_REQUIREMENT)
            ->where("FIND_IN_SET('{$projectID}',`project`)")
            ->fetchAll('id');

        return $list;
    }
    /**
     * 获取发布内容
     * @param $projectID
     * @return mixed
     */
    public function getProjectReleases($projectID)
    {
        $list = $this->dao->select('id,name,date,createdBy,`desc`')->from(TABLE_RELEASE)
            ->where('project')->eq($projectID)
            ->andwhere('deleted')->eq(0)
            ->fetchAll();
        return $list;
    }

    /**
     * 获取项目需求
     * @param $projectPlanID
     * @return mixed
     */
    public function getProjectDemands($projectPlanID)
    {
        $list = $this->dao->select('id,title')->from(TABLE_DEMAND)
            ->where('projectPlan')->eq($projectPlanID)
            ->fetchAll();
        return $list;
    }

    /**
     * 获取自定义的项目状态列表
     * @return array
     */
    public function getSelects(): array
    {
        $langData = $this->dao->select('`section`, `key`, `value`, `system`')->from(TABLE_LANG)->where('module')->in('project')->orderBy('id')->fetchAll();
        $selects = [];
        foreach ($langData as $item)
        {
            $selects[$item->section][$item->key] = $item->value;
        }

        //空选项排第一个
        unset($selects['insideReportStatusList']['']);
        $selects['insideReportStatusList'] = [''] + $selects['insideReportStatusList'];
        unset($selects['outsideReportStatusList']['']);
        $selects['outsideReportStatusList'] = [''] + $selects['insideReportStatusList'];
        return $selects;
    }

    /**
     * 获取项目需求和需求条目内容
     * @param $projectID
     * @param $projectPlanId
     * @return string
     */
    public function getDemandInfo($projectID, $projectPlanId)
    {
        $demands = empty($projectPlanId) ? null : $this->getProjectDemands($projectPlanId);
        $i = 0;
        $demand_info = [];
        foreach ($demands as $demand)
        {
            $i++;
            if($i >30) break;
            $item['id'] = $demand->id;
            $item['name'] = $demand->name ?? $demand->title;
            $demand_info['data'][] = $item;
        }
        $requirements = $this->getProjectRequirements($projectID);
        $i = 0;
        foreach ($requirements as $demand)
        {
            $i++;
            if($i >30) break;
            $item['id'] = $demand->id;
            $item['name'] = $demand->name ?? $demand->title;
            $demand_info['requirement'][] = $item;
        }
        return base64_encode(json_encode($demand_info));
    }

    /**
     * 获取项目计划阶段内容
     * @param $projectID
     * @return array
     */
    public function getStages($projectID)
    {
        //先获取项目计划阶段的id
        $execution = $this->dao->select('id,name,grade,parent')->from(TABLE_EXECUTION)
            ->where('project')->eq($projectID)
            ->andwhere('type')->eq('stage')
            ->andWhere('deleted')->eq('0')
            ->orderby('id_asc')
            ->fetchAll();
        if(empty($execution)){
            return [];
        }
        $list = $stageList = [];
        foreach ($execution as $item)
        {
            if($item->parent == 0 ){
                $num = $item->id * 100000000;
            } else {
                $num = ($item->parent * 100000000) + $item->id;
            }
            $list[$num]['name'] = $item->name;
            $list[$num]['parent'] = $item->parent;
        }
        ksort($list);
        foreach ($list as $item)
        {
            if($item['parent'] == 0){
                $stageList[$item['name']] = $item['name'];
            } else {
                $stageList[$item['name']] = '&nbsp;&nbsp;&nbsp;&nbsp;' . $item['name'];
            }
        }
        return $stageList;
    }

    /**
     * 计算整体进度
     */
    public function getProgress($projectID)
    {
        $tasks = $this->dao->select('t1.*')->from(TABLE_TASK)->alias('t1')
            ->leftJoin(TABLE_EXECUTION)->alias('t2')
            ->on('t1.execution = t2.id')
            ->where('t1.deleted')->eq(0)
            ->andWhere('t2.deleted')->eq(0)
            ->andWhere('t1.project')->eq($projectID)
            ->andWhere('t1.parent')->eq(0)
            ->fetchAll('id');
        $progress = $estimate = 0;
        foreach($tasks as $task)
        {
            $estimate += $task->estimate;
            $progress += $task->progress * $task->estimate;
        }
        $progress = $estimate > 0 ? round($progress/$estimate) : 0;
        return $progress;
        return $estimate > 0 ? round($progress/$estimate) : 0;
    }


    public function getUserQADept($account){
        $this->loadModel('project');
        $organizationQA = array_keys($this->lang->project->setOrganization);

        if(in_array($account,$organizationQA)){
            //是组织QA
           return ['isogQA'=>1,'depts'=>''];
        }else{
            //非组织级QA,如果部门为空，说明这个人不是 QA，部门不为空，是普通QA
            $deptes = $this->dao->select("id,name")->from(TABLE_DEPT)->where("FIND_IN_SET('{$account}',`qa`)")->fetchAll();
            return ['isogQA'=>0,'depts'=>$deptes];
        }


    }

    /**
     * @param $account 当前登录账号信息
     * @param $reportDept 周报部门
     * @return bool
     */
    public function authUserReportQA($account,$reportDept=''){
        $isQA = $this->getUserQADept($account);

        if($isQA['isogQA'] == 1){
            return true;
        }
        if(!$isQA['depts']){
            return false;
        }
        $depts = array_column($isQA['depts'],'id');

        $reportDeptArr = explode(',',$reportDept);
        $res = array_intersect($reportDeptArr,$depts);
        if($res){
            return true;
        }else{
            return false;
        }





    }



}

