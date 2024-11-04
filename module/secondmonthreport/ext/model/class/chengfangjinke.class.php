<?php
class chengfangjinkeSecondmonthreport extends secondmonthreportModel{

    /**
     * 为问题池提供数据源。 内外部反馈 ->beginIF($datasource == 'feedback')->andWhere('t1.createdBy')->in(['guestcn','guestjx'])->fi()
     * 问题整体统计表、未解决统计表 增加结转数据 1、历史表单 加，2、实时表单 本年度搜索 加
     * @param $start
     * @param $end
     * @param $deptID
     * @param $staticType
     * @param $isuseHisData 是否启用历史结转数据
     * @return mixed
     */

    public function getProblemDataList($start,$end,$deptID,$staticType,$isuseHisData){
        $realusedepts = [];
        $problemIds   = [];
        if($deptID){
            $realusedepts = $this->getRealUseDepts($deptID);
        }
        $delayResolutionDate = $this->dao
            ->select('objectId,delayResolutionDate')
            ->from(TABLE_DELAY)
            ->where('objectType')->eq('problem')
            //->andWhere('delayResolutionDate')->between($start, $end)
            ->andWhere('delayStatus')->eq('success')
            ->fetchpairs();
        $problemIds = array_keys($delayResolutionDate);

        if($deptID == -1){
            $problemData = $this->dao
                ->select('*')
                ->from(TABLE_PROBLEM)
                ->where('status')->ne('deleted')
                ->beginIF($staticType != 'problemCompletedPlan')
                ->andWhere('createdDate')->between($start, $end)
                ->fi()
                ->beginIF($staticType == 'problemCompletedPlan')
                ->andWhere()
                ->markLeft(1)
                ->where('PlannedTimeOfChange')->between($start, $end)
                ->orWhere('id')->in($problemIds)
                ->markRight(1)
                ->fi()
                ->andWhere(' (acceptDept=0 or acceptDept is null) ')
                ->beginIF(in_array($staticType,['problemExceedBackIn','problemExceedBackOut']) )
                ->andWhere('createdBy')->in(['guestcn','guestjx'])
                ->fi()
                ->orderBy("id_desc")
                ->fetchAll('id');
        }else{
            $problemData = $this->dao
                ->select('*')
                ->from(TABLE_PROBLEM)
                ->where('status')->ne('deleted')
                ->beginIF($staticType != 'problemCompletedPlan')
                ->andWhere('createdDate')->between($start, $end)
                ->fi()
                ->beginIF($staticType == 'problemCompletedPlan')
                ->andWhere()
                ->markLeft(1)
                ->where('PlannedTimeOfChange')->between($start, $end)
                ->orWhere('id')->in($problemIds)
                ->markRight(1)
                ->fi()
                ->beginIF($deptID && $realusedepts)
                ->andWhere('acceptDept')->in($realusedepts)
                ->fi()
                ->beginIF(in_array($staticType,['problemExceedBackIn','problemExceedBackOut']) )
                ->andWhere('createdBy')->in(['guestcn','guestjx'])
                ->fi()
                ->orderBy("id_desc")
                ->fetchAll('id');
        }

        if($isuseHisData == 1){
            $historyData = $this->getProblemHistoryDataList($deptID);
            if($problemData && $historyData){
                $problemData = $problemData+$historyData;
            }elseif (!$problemData && $historyData){
                $problemData = $historyData;
            }
        }

        foreach ($delayResolutionDate as $id => $val){
            if(!isset($problemData[$id])){
                continue;
            }
            if($problemData[$id]->PlannedTimeOfChange < $val){
                $problemData[$id]->PlannedTimeOfChange = $val;
            }
            if('problemCompletedPlan' == $staticType){
                if($start > $problemData[$id]->PlannedTimeOfChange || $end < $problemData[$id]->PlannedTimeOfChange){
                    unset($problemData[$id]);
                }
            }
        }

        return $problemData;
    }
    public function getProblemHistoryDataList($deptID){
        if($deptID == -1){
            $historyData = $this->dao->select("t2.*")->from(TABLE_SECONDMONTHHISTORYDATA)->alias("t1")
                ->innerJoin(TABLE_PROBLEM)->alias("t2")->on('t1.objectid = t2.id')
                ->where('t1.sourceyear')->eq((int)$this->lang->secondmonthreport->examinecycleList['examineyear'])
                ->andWhere('t1.sourcetype')->eq('problem')
                ->andWhere(' (t2.acceptDept=0 or t2.acceptDept is null) ')
                ->andWhere('t1.deleted')->eq(0)
                ->andWhere('t2.status')->ne('deleted')
                ->fetchAll("id");
        }else{
            $historyData = $this->dao->select("t2.*")->from(TABLE_SECONDMONTHHISTORYDATA)->alias("t1")
                ->innerJoin(TABLE_PROBLEM)->alias("t2")->on('t1.objectid = t2.id')
                ->where('t1.sourceyear')->eq((int)$this->lang->secondmonthreport->examinecycleList['examineyear'])
                ->andWhere('t1.sourcetype')->eq('problem')
                ->beginIF($deptID)->andWhere('t2.acceptDept')->eq($deptID)->fi()
                ->andWhere('t1.deleted')->eq(0)
                ->andWhere('t2.status')->ne('deleted')
                ->fetchAll("id");
        }

        return $historyData;
    }

    public function getProblemDataListByIDs($ids){
        if($ids){
            $problemData = $this->dao
                ->select('*')
                ->from(TABLE_PROBLEM)
                ->where('id')->in($ids)
                ->andWhere('status')->ne('deleted')
                ->orderBy("id_desc")
                ->fetchAll('id');
        }else{
            $problemData = [];
        }

        return $problemData;
    }
    public function getRealUseDepts($deptID){
        $depts = [];
        $topDept = 0;
        if($deptID){
            $deptInfo = $this->dao->select("parent,path")->from(TABLE_DEPT)->where('id')->eq($deptID)->fetch();
            if($deptInfo and $deptInfo->parent != 0){
                $tempDept = explode(',',trim($deptInfo->parent,','))[0];
                if($tempDept){
                    $topDept = $tempDept;
                    $depts = $this->dao->select("id")->from(TABLE_DEPT)->where('parent')->eq($tempDept)->fetchAll();
                }
            }else{
                $depts = $this->dao->select("id")->from(TABLE_DEPT)->where('parent')->eq($deptID)->fetchAll();
                $topDept = $deptID;
            }
        }
        if($depts){
            $depts = array_column($depts,'id');
            if($topDept){
                $depts[] = $topDept;
            }
        }else{
            $depts = [$deptID];
        }
        return $depts;

    }
    public function getDemandDataList($start,$end,$deptID,$staticType,$isuseHisData)
    {
//        $field = "t1.id,t1.requirementID,t1.status,t1.createdDate,t1.acceptDept,t2.newPublishedTime,t1.fixType,t1.solvedTime,t1.delayStatus,t1.isExtended,t2.createdBy,t2.actualMethod,t2.feekBackStartTime";
        $field = "t1.id,t1.state,t1.code,t1.title,t1.status,t1.app,t1.type,t1.union,t1.project,t1.end,t1.opinionID,t1.isPayment,t1.product,t1.productPlan,t1.delayStatus,t1.delayResolutionDate,t1.isExtended,t1.requirementID,t1.status,t1.createdBy,t1.acceptUser,t1.dealUser,t1.editedBy,t1.systemverify,t1.verifyperson,t1.laboratorytest,t1.closedBy,t1.desc,t1.createdDate,t1.acceptDept,t1.fixType,t1.solvedTime,t1.isExtended,t1.deliveryOver,t2.acceptTime,t2.opinion,t2.createdBy requirementcreatedBy,t2.createdDate requirementcreatedDate,t2.method,t2.actualMethod,t2.feekBackStartTime,t2.newPublishedTime";
        $realusedepts = [];
        if($deptID){
            $realusedepts = $this->getRealUseDepts($deptID);
        }
        if($deptID == -1){
            $demandData = $this->dao->select($field)->from(TABLE_DEMAND)->alias('t1')
                ->leftJoin(TABLE_REQUIREMENT)->alias('t2')->on('t1.requirementID = t2.id')
                ->where('t1.status')->ne('deleted')
//                ->andWhere('t2.`status`')->ne('deleteout')
                ->andWhere('t1.sourceDemand')->eq('1')
                ->andWhere('t1.fixType')->eq('second')
                ->andWhere(' (t1.acceptDept=0 or t1.acceptDept is null) ')
                ->andWhere('t1.createdDate')->between($start, $end)
                ->fetchAll('id');

        }else{
            $demandData = $this->dao->select($field)->from(TABLE_DEMAND)->alias('t1')
                ->leftJoin(TABLE_REQUIREMENT)->alias('t2')->on('t1.requirementID = t2.id')
                ->where('t1.status')->ne('deleted')
//                ->andWhere('t2.`status`')->ne('deleteout')
                ->andWhere('t1.sourceDemand')->eq('1')
                ->andWhere('t1.fixType')->eq('second')
                ->beginIF($deptID && $realusedepts)->andWhere('t1.acceptDept')->in($realusedepts)->fi()
                ->andWhere('t1.createdDate')->between($start, $end)
                ->fetchAll('id');

        }


        if($isuseHisData == 1){
            $historyData = $this->getDemandHistoryDataList($deptID);
            if($demandData && $historyData){
                $demandData = $demandData+$historyData;
            }elseif (!$demandData && $historyData){
                $demandData = $historyData;
            }

        }
        $opinionIDList = [];
        foreach ($demandData as $key => $item){
            $opinionIDList[$item->opinion] = $item->opinion;
        }
        $opinionList = [];
        if($opinionIDList){
            $opinionList = $this->dao->select('receiveDate,id')->from(TABLE_OPINION)->where('id')->in($opinionIDList)->fetchAll('id');
        }

        foreach ($demandData as $key => $item)
        {
            if($item->requirementcreatedBy == 'guestcn'){
                //需求任务接受时间
                $demandData[$key]->monthreportrcvDate = $item->requirementcreatedDate;
                $demandData[$key]->publishedTime = $item->feekBackStartTime != '0000-00-00 00:00:00' ? $item->feekBackStartTime : '';
            }else{

                $demandData[$key]->monthreportrcvDate = isset($opinionList[$item->opinion]) && $opinionList[$item->opinion]->receiveDate ?? '';

                $demandData[$key]->publishedTime = $item->newPublishedTime != '0000-00-00 00:00:00' ? $item->newPublishedTime : '';
            }
        }
        /*foreach ($demandData as $demand){
            if($demand->desc){
                $demand->desc = strip_tags($demand->desc);
            }
        }*/
        return $demandData;
    }
    public function getDemandHistoryDataList($deptID){
//        $field = "t1.id,t1.requirementID,t1.status,t1.createdDate,t1.acceptDept,t2.newPublishedTime,t1.fixType,t1.solvedTime,t1.delayStatus,t1.isExtended,t2.createdBy,t2.actualMethod,t2.feekBackStartTime";
        $field = "t1.id,t1.state,t1.code,t1.title,t1.status,t1.app,t1.type,t1.union,t1.project,t1.end,t1.opinionID,t1.isPayment,t1.product,t1.productPlan,t1.delayStatus,t1.delayResolutionDate,t1.isExtended,t1.requirementID,t1.status,t1.createdBy,t1.acceptUser,t1.dealUser,t1.editedBy,t1.systemverify,t1.verifyperson,t1.laboratorytest,t1.closedBy,t1.desc,t1.createdDate,t1.acceptDept,t1.fixType,t1.solvedTime,t1.isExtended,t1.deliveryOver,t2.acceptTime,t2.opinion,t2.createdBy requirementcreatedBy,t2.createdDate requirementcreatedDate,t2.method,t2.actualMethod,t2.feekBackStartTime,t2.newPublishedTime";
        $realusedepts = [];
        if($deptID){
            $realusedepts = $this->getRealUseDepts($deptID);
        }
        if($deptID == -1){
            $historyData = $this->dao->select($field)->from(TABLE_SECONDMONTHHISTORYDATA)->alias("t3")
                ->innerJoin(TABLE_DEMAND)->alias('t1')->on('t1.id = t3.objectid')
                ->leftJoin(TABLE_REQUIREMENT)->alias('t2')->on('t1.requirementID = t2.id')
                ->where('t1.status')->ne('deleted')
//                ->andWhere('t2.status')->ne('deleteout')
                ->andWhere('t3.sourcetype')->eq('demand')
                ->andWhere('t3.deleted')->eq(0)
                ->andWhere('t3.sourceyear')->eq((int)$this->lang->secondmonthreport->examinecycleList['examineyear'])
                ->andWhere('t1.sourceDemand')->eq('1')
                ->andWhere('t1.fixType')->eq('second')
                ->andWhere(' (t1.acceptDept=0 or t1.acceptDept is null) ')

                ->fetchAll('id');

        }else{
            $historyData = $this->dao->select($field)->from(TABLE_SECONDMONTHHISTORYDATA)->alias("t3")
                ->innerJoin(TABLE_DEMAND)->alias('t1')->on('t1.id = t3.objectid')
                ->leftJoin(TABLE_REQUIREMENT)->alias('t2')->on('t1.requirementID = t2.id')
                ->where('t1.status')->ne('deleted')
//                ->andWhere('t2.status')->ne('deleteout')
                ->andWhere('t3.sourcetype')->eq('demand')
                ->andWhere('t3.deleted')->eq(0)
                ->andWhere('t3.sourceyear')->eq((int)$this->lang->secondmonthreport->examinecycleList['examineyear'])
                ->andWhere('t1.sourceDemand')->eq('1')
                ->andWhere('t1.fixType')->eq('second')
                ->beginIF($deptID && $realusedepts)->andWhere('t1.acceptDept')->in($realusedepts)->fi()

                ->fetchAll('id');
        }

        return $historyData;
    }

    public function getDemandDataListByIDs($ids){
        if($ids){
            $field = "t1.id,t1.state,t1.code,t1.title,t1.status,t1.app,t1.type,t1.union,t1.project,t1.end,t1.opinionID,t1.isPayment,t1.product,t1.productPlan,t1.delayStatus,t1.delayResolutionDate,t1.isExtended,t1.requirementID,t1.status,t1.createdBy,t1.acceptUser,t1.dealUser,t1.editedBy,t1.systemverify,t1.verifyperson,t1.laboratorytest,t1.closedBy,t1.desc,t1.createdDate,t1.acceptDept,t1.fixType,t1.solvedTime,t1.isExtended,t2.acceptTime,t2.opinion,t2.createdBy requirementcreatedBy,t2.createdDate requirementcreatedDate,t2.method,t2.actualMethod,t2.feekBackStartTime,t2.newPublishedTime";

            $demandData = $this->dao->select($field)->from(TABLE_DEMAND)->alias('t1')
                ->leftJoin(TABLE_REQUIREMENT)->alias('t2')->on('t1.requirementID = t2.id')
                ->where('t1.status')->ne('deleted')
                ->andWhere('t1.sourceDemand')->eq(1) //查询外部的数据
                ->andWhere('t1.fixType')->eq('second')
                ->andWhere('t1.id')->in($ids)
//                ->andWhere('t2.`status`')->ne('deleteout')
                ->fetchAll('id');
            $opinionIDList = [];
            foreach ($demandData as $key => $item){
                $opinionIDList[$item->opinion] = $item->opinion;
            }
            $opinionList = [];
            if($opinionIDList){
                $opinionList = $this->dao->select('receiveDate,id')->from(TABLE_OPINION)->where('id')->in($opinionIDList)->fetchAll('id');
            }

            foreach ($demandData as $key => $item)
            {
                if($item->requirementcreatedBy == 'guestcn'){
                    //需求任务接受时间
                    $demandData[$key]->monthreportrcvDate = $item->requirementcreatedDate;
                    $demandData[$key]->publishedTime = $item->feekBackStartTime != '0000-00-00 00:00:00' ? $item->feekBackStartTime : '';
                }else{

                    $demandData[$key]->monthreportrcvDate = isset($opinionList[$item->opinion]) && $opinionList[$item->opinion]->receiveDate ?? '';

                    $demandData[$key]->publishedTime = $item->newPublishedTime != '0000-00-00 00:00:00' ? $item->newPublishedTime : '';
                }
            }
        }else{
            $demandData = [];
        }

        return $demandData;
    }

    public function getRequirementDataList($start,$end,$deptID,$staticType,$isuseHisData)
    {
        $field = "t1.id,t1.opinion,t1.project,t1.createdBy,t1.name,t1.newPublishedTime,t1.actualMethod,t1.productManager,t1.projectManager,t1.product,t1.editedBy,t1.closedBy,t1.activatedBy,t1.ignoredBy,t1.recoveryedBy,t1.feekBackEndTimeInside,t1.onlineTimeByDemand,t1.feekBackEndTimeOutSide,t1.ifOutUpdate,t1.lastChangeTime,t1.`desc`,t1.`line`,t1.`code`,t1.`status`,t1.createdDate,t1.app,t1.dealUser,t1.feedbackStatus,t1.feedbackBy,t1.feedbackDate,t1.ifOverDate,t1.ifOverTimeOutSide,t1.feekBackStartTime,t1.feekBackStartTimeOutside,t1.innovationPassTime,t1.deptPassTime,t1.feedbackOver,t1.feedbackDealUser,t2.dept";

        $realusedepts = [];
        if($deptID){
            $realusedepts = $this->getRealUseDepts($deptID);
        }

        //部门筛选 有待处理   部门不是固定的，不同的数据取了不同的部门 。部门筛选暂时无法实现
        $info = $this->dao->select($field)->from(TABLE_REQUIREMENT)->alias('t1')
            ->leftJoin(TABLE_USER)->alias('t2')->on('t1.feedbackBy = t2.account')
            ->where('t1.`status`')->ne('deleted')
            ->andWhere('t1.`status`')->ne('deleteout')
            ->andWhere('t1.createdBy')->eq('guestcn')
            ->andWhere('t1.sourceRequirement')->eq('1')
            ->andWhere('t1.createdDate')->between($start, $end)
//            ->beginIF($deptID && $realusedepts)->andWhere('t1.acceptDept')->in($realusedepts)->fi()
            ->fetchAll();
        $userDepts = $this->dao->select('account,dept')->from(TABLE_USER)->where('deleted')->eq(0)->fetchPairs();

        //待反馈状态取 需求任务待处理人第一个的所属部门
        foreach ($info as $key => $value){
            if($value->feedbackStatus == 'tofeedback')
            {
                if($value->feedbackDealUser){
                    $info[$key]->dept = $userDepts[$value->feedbackDealUser];
                }else{
                    $info[$key]->dept = '';
                }
            }
            //处理部门筛选逻辑 如果筛选部门为空  剔除有部门的数据，如果有明确筛选部门，则剔除筛选之外的部门
            if ($deptID){
                if($deptID == -1){
                    if($info[$key]->dept){
                        unset($info[$key]);
                    }
                }else{
                    if(!in_array($info[$key]->dept,$realusedepts)){
                        unset($info[$key]);
                    }
                }
            }
        }
        return $info;
    }
    public function getRequirementDataListByIDs($ids)
    {

//        $field = "t1.id,t1.`code`,t1.`status`,t1.createdDate,t1.app,t1.dealUser,t1.feedbackStatus,t1.feedbackBy,t1.feedbackDate,t1.ifOverDate,t1.ifOverTimeOutSide,t1.feekBackStartTime,t1.feekBackStartTimeOutside,t1.innovationPassTime,t1.deptPassTime,t1.feedbackOver,t1.feedbackDealUser,t2.dept";
        $field = "t1.id,t1.opinion,t1.project,t1.createdBy,t1.name,t1.newPublishedTime,t1.actualMethod,t1.productManager,t1.projectManager,t1.product,t1.editedBy,t1.closedBy,t1.activatedBy,t1.ignoredBy,t1.recoveryedBy,t1.feekBackEndTimeInside,t1.onlineTimeByDemand,t1.feekBackEndTimeOutSide,t1.ifOutUpdate,t1.lastChangeTime,t1.`desc`,t1.`line`,t1.`code`,t1.`status`,t1.createdDate,t1.app,t1.dealUser,t1.feedbackStatus,t1.feedbackBy,t1.feedbackDate,t1.ifOverDate,t1.ifOverTimeOutSide,t1.feekBackStartTime,t1.feekBackStartTimeOutside,t1.innovationPassTime,t1.deptPassTime,t1.feedbackOver,t1.feedbackDealUser,t2.dept";


        $info = $this->dao->select($field)->from(TABLE_REQUIREMENT)->alias('t1')
            ->leftJoin(TABLE_USER)->alias('t2')->on('t1.feedbackBy = t2.account')
            ->where('t1.`status`')->ne('deleted')
            ->andWhere('t1.`status`')->ne('deleteout')
            ->andWhere('t1.createdBy')->eq('guestcn')
            ->andWhere('t1.id')->in($ids)
            ->andWhere('t1.sourceRequirement')->eq('1')


            ->fetchAll('id');

        $userDepts = $this->dao->select('account,dept')->from(TABLE_USER)->where('deleted')->eq(0)->fetchPairs();

        //待反馈状态取 需求任务待处理人第一个的所属部门
        foreach ($info as $key => $value){
            if($value->feedbackStatus == 'tofeedback')
            {
                if($value->feedbackDealUser){
                    /*$userDept = $this->loadModel('user')->getByAccount();
                    $userDept->dept;*/
                    $info[$key]->dept = $userDepts[$value->feedbackDealUser];
                }else{

                    $info[$key]->dept = '';
                }
            }
        }
        return $info;
    }
    public function getSecondorderDataList($start,$end,$deptID,$staticType,$isuseHisData){
        $realusedepts = [];

        if($deptID){
            $realusedepts = $this->getRealUseDepts($deptID);
        }
        if($deptID == -1){
            $sencondorderList = $this->dao->select('`id`,`acceptDept`,`type`,`status`,`code`,`app`,`summary`,`acceptUser`,createdDate,subtype,ifAccept,createdBy')->from(TABLE_SECONDORDER)
                ->where('deleted')->eq('0')
                //->andWhere('status')->ne('backed')
                ->andWhere('createdDate')->between($start, $end)
                ->andWhere(' (acceptDept=0 or acceptDept is null) ')
                ->orderBy("id_desc")
                ->fetchAll('id');

        }else{
            $sencondorderList = $this->dao->select('`id`,`acceptDept`,`type`,`status`,`code`,`app`,`summary`,`acceptUser`,createdDate,subtype,ifAccept,createdBy')->from(TABLE_SECONDORDER)
                ->where('deleted')->eq('0')
                //->andWhere('status')->ne('backed')
                ->andWhere('createdDate')->between($start, $end)
                ->beginIF($deptID && $realusedepts)->andWhere('acceptDept')->in($realusedepts)->fi()
                ->orderBy("id_desc")
                ->fetchAll('id');
        }


        if($isuseHisData == 1){
            $historyData = $this->getSecondorerHistoryDataList($deptID);
            if($sencondorderList && $historyData){
                $sencondorderList = $sencondorderList+$historyData;
            }elseif (!$sencondorderList && $historyData){
                $sencondorderList = $historyData;
            }

        }

        return $sencondorderList;
    }

    /** 获取 工单历史结转数据
     * @param $deptID
     * @return mixed
     */
    public function getSecondorerHistoryDataList($deptID){

        $realusedepts = [];
        if($deptID){
            $realusedepts = $this->getRealUseDepts($deptID);
        }
        if($deptID == -1){
            $historyData = $this->dao->select('t2.`id`,t2.`acceptDept`,t2.`type`,t2.`status`,t2.`code`,t2.`app`,t2.`summary`,t2.`acceptUser`,t2.createdDate,t2.subtype,t2.ifAccept,t2.createdBy')->from(TABLE_SECONDMONTHHISTORYDATA)->alias("t1")
                ->innerJoin(TABLE_SECONDORDER)->alias("t2")->on('t1.objectid = t2.id')
                ->where('t2.deleted')->eq('0')
                ->andWhere('t1.deleted')->eq(0)
                ->andWhere('t1.sourcetype')->eq('secondorder')
                //->andWhere('t2.status')->ne('backed')
                ->andWhere(' (t2.acceptDept=0 or t2.acceptDept is null) ')
                ->orderBy("id_desc")
                ->fetchAll('id');
        }else{
            $historyData = $this->dao->select('t2.`id`,t2.`acceptDept`,t2.`type`,t2.`status`,t2.`code`,t2.`app`,t2.`summary`,t2.`acceptUser`,t2.createdDate,t2.subtype,t2.ifAccept,t2.createdBy')->from(TABLE_SECONDMONTHHISTORYDATA)->alias("t1")
                ->innerJoin(TABLE_SECONDORDER)->alias("t2")->on('t1.objectid = t2.id')
                ->where('t2.deleted')->eq('0')
                ->andWhere('t1.deleted')->eq(0)
                ->andWhere('t1.sourcetype')->eq('secondorder')
                //->andWhere('t2.status')->ne('backed')
                ->beginIF($deptID && $realusedepts)->andWhere('t2.acceptDept')->in($realusedepts)->fi()
                ->orderBy("id_desc")
                ->fetchAll('id');
        }

        return $historyData;
    }
    public function getSecondorderDataListByIDs($ids){

        $sencondorderList = $this->dao->select('`id`,`acceptDept`,`type`,`status`,`code`,`app`,`summary`,`acceptUser`,subtype,ifAccept,createdBy')->from(TABLE_SECONDORDER)
            ->where('deleted')->eq('0')

            ->andWhere('id')->in($ids)
            ->orderBy("id_desc")
            ->fetchAll('id');

        return $sencondorderList;
    }
    public function getSupportDataList($start,$end,$deptID,$staticType,$isuseHisData){
        $realusedepts = [];
        if($deptID){
            $realusedepts = $this->getRealUseDepts($deptID);
        }
        $fieldStr = 't2.id as id,t2.`code`, t2.startDate AS sdate, t2.endDate AS edate, t2.area, t2.appIds, t2.stype, t2.reason, t1.deptID as dept, t1.supportUser AS pnams, t1.consumed AS workh';
        //迭代34 去掉 待提交状态过滤
        if($deptID == -1){
//            $supportList = $this->dao->select('`id`,`dept`,`stype`,`workh`,`sdate`,`edate`,`area`,`app`,`reason`,`pnams`')->from(TABLE_FLOW_SUPPORT)
//                ->where('deleted')->eq('0')
////            ->andWhere('status')->eq('2')
//                ->andWhere('sdate')->between($start, $end)
//                ->andWhere(' (dept=0 or dept is null) ')
//                ->orderBy("id_desc")
//                ->fetchAll('id');

            $supportList = $this->dao
                ->select($fieldStr)
                ->from(TABLE_LOCALESUPPORT_WORKREPORT)->alias('t1')
                ->innerJoin(TABLE_LOCALESUPPORT)->alias('t2')
                ->on('t1.supportId = t2.id')
                ->where('t1.deleted')->eq('0')
                ->andWhere('t2.deleted')->eq('0')
                ->andWhere('t2.status')->in(['pass','waitdept'])
                ->andWhere('t2.startDate')->between($start, $end)
                ->orderBy("t1.id_desc")
                ->fetchAll();

        }else{
//            $supportList = $this->dao->select('`id`,`dept`,`stype`,`workh`,`sdate`,`edate`,`area`,`app`,`reason`,`pnams`')->from(TABLE_FLOW_SUPPORT)
//                ->where('deleted')->eq('0')
////            ->andWhere('status')->eq('2')
//                ->andWhere('sdate')->between($start, $end)
//                ->beginIF($deptID && $realusedepts)->andWhere('dept')->in($realusedepts)->fi()
//                ->orderBy("id_desc")
//                ->fetchAll('id');
            $supportList = $this->dao
                ->select($fieldStr)
                ->from(TABLE_LOCALESUPPORT_WORKREPORT)->alias('t1')
                ->innerJoin(TABLE_LOCALESUPPORT)->alias('t2')
                ->on('t1.supportId = t2.id')
                ->where('t1.deleted')->eq('0')
                ->andWhere('t2.deleted')->eq('0')
                ->andWhere('t2.status')->in(['pass','waitdept'])
                ->andWhere('t2.startDate')->between($start, $end)
                ->beginIF($deptID && $realusedepts)->andWhere('t1.deptID')->in($realusedepts)->fi()
                ->orderBy("t1.id_desc")
                ->fetchAll();
        }

        return $supportList;
    }
    public function getSupportDataListByIDs($ids){

//        $supportList = $this->dao->select('`id`,`dept`,`stype`,`workh`,`sdate`,`edate`,`area`,`app`,`reason`,`pnams`')->from(TABLE_FLOW_SUPPORT)
//            ->where('deleted')->eq('0')
//            ->andWhere('id')->in($ids)
//            ->orderBy("id_desc")
//            ->fetchAll('id');
        $fieldStr = 't2.id as id,t2.`code`, t2.startDate AS sdate, t2.endDate AS edate, t2.area, t2.appIds, t2.stype, t2.reason, t1.deptID as dept, t1.supportUser AS pnams, t1.consumed AS workh';
        $supportList = $this->dao
            ->select($fieldStr)
            ->from(TABLE_LOCALESUPPORT_WORKREPORT)->alias('t1')
            ->innerJoin(TABLE_LOCALESUPPORT)->alias('t2')
            ->on('t1.supportId = t2.id')
            ->where('t1.deleted')->eq('0')
            ->andWhere('t2.deleted')->eq('0')
            ->andWhere('t2.id')->in($ids)
            ->orderBy("t1.id_desc")
            ->fetchAll();

        return $supportList;
    }

    /**金信生产变更
     * @param $start
     * @param $end
     * @param $deptID
     * @param $staticType
     * @param $isuseHisData
     * @return mixed
     */
    public function getModifyDataList($start,$end,$deptID,$staticType,$isuseHisData){
        $realusedepts = [];
        if($deptID){
            $realusedepts = $this->getRealUseDepts($deptID);
        }

        //去除 删除的 ，迭代34去掉 待提交的
        if($deptID == -1){
            $modifyList = $this->dao->select('`id`,`status`,`mode`,`createdDept`,`code`,`app`,`level`,`desc`,`createdBy`,`type`,realEndTime')->from(TABLE_MODIFY)
                ->where('status')->notin(['deleted','waitsubmitted'])
                ->andWhere(' (createdDept=0 or createdDept is null) ')
                ->andWhere('createdDate')->between($start, $end)
                ->orderBy("id_desc")
                ->fetchAll('id');

        }else{
            $modifyList = $this->dao->select('`id`,`status`,`mode`,`createdDept`,`code`,`app`,`level`,`desc`,`createdBy`,`type`,realEndTime')->from(TABLE_MODIFY)
                ->where('status')->notin(['deleted','waitsubmitted'])
                ->beginIF($deptID && $realusedepts)->andWhere('createdDept')->in($realusedepts)->fi()
                ->andWhere('createdDate')->between($start, $end)
                ->orderBy("id_desc")
                ->fetchAll('id');
        }


        foreach ($modifyList as $modify){
            $modify->exybtjsource = 'modify';
        }
        $modifyList = array_values($modifyList);
        return $modifyList;
    }
    public function getModifyDataListByIDs($ids){

        $modifyList = $this->dao->select('`id`,`status`,`mode`,`createdDept`,`code`,`app`,`level`,`desc`,`createdBy`,`type`,realEndTime')->from(TABLE_MODIFY)
            ->where('status')->ne('deleted')
            ->andWhere('id')->in($ids)
            ->orderBy("id_desc")
            ->fetchAll();
        foreach ($modifyList as $modify){
            $modify->exybtjsource = 'modify';
        }

        return $modifyList;
    }
    public function getModifycnccDataList($start,$end,$deptID,$staticType,$isuseHisData){
        $realusedepts = [];
        if($deptID){
            $realusedepts = $this->getRealUseDepts($deptID);
        }

        //迭代34去掉剔除待提交
        if($deptID == -1){
            $modifycnccList = $this->dao->select('`id`,`status`,`mode`,`createdDept`,`code`,`belongedApp` as `app`,`level`,`desc`,`createdBy`,`type`,actualEnd as realEndTime')->from(TABLE_MODIFYCNCC)
                ->where('status')->notin(['deleted','waitsubmitted'])
                ->andWhere('deleted')->ne('1')
                ->andWhere(' (createdDept=0 or createdDept is null) ')
                ->andWhere('createdDate')->between($start, $end)
                ->orderBy("id_desc")
                ->fetchAll('id');

        }else{
            $modifycnccList = $this->dao->select('`id`,`status`,`mode`,`createdDept`,`code`,`belongedApp` as `app`,`level`,`desc`,`createdBy`,`type`,actualEnd as realEndTime')->from(TABLE_MODIFYCNCC)
                ->where('status')->notin(['deleted','waitsubmitted'])
                ->andWhere('deleted')->ne('1')
                ->beginIF($deptID && $realusedepts)->andWhere('createdDept')->in($realusedepts)->fi()
                ->andWhere('createdDate')->between($start, $end)
                ->orderBy("id_desc")
                ->fetchAll('id');
        }

        foreach ($modifycnccList as $modifycncc){
            $modifycncc->exybtjsource = 'modifycncc';
        }
        $modifycnccList = array_values($modifycnccList);
        return $modifycnccList;
    }
    public function getModifycnccDataListByIDs($ids){

        $modifycnccList = $this->dao->select('`id`,`status`,`mode`,`createdDept`,`code`,`belongedApp` as `app`,`level`,`desc`,`createdBy`,`type`,actualEnd as realEndTime')->from(TABLE_MODIFYCNCC)
            ->where('status')->ne('deleted')
            ->andWhere('deleted')->ne('1')
            ->andWhere('id')->in($ids)
            ->orderBy("id_desc")
            ->fetchAll();
        foreach ($modifycnccList as $modifycncc){
            $modifycncc->exybtjsource = 'modifycncc';
        }
        return $modifycnccList;
    }
    /**
     * 征信交付
     * @param $start
     * @param $end
     * @param $deptID
     * @param $staticType
     * @param $isuseHisData
     * @return mixed
     */
    public function getCreditDataList($start,$end,$deptID,$staticType,$isuseHisData){
        $realusedepts = [];
        if($deptID){
            $realusedepts = $this->getRealUseDepts($deptID);
        }

        //去除 删除的 ，迭代34去掉 待提交的
        if($deptID == -1){
            $creditList = $this->dao->select('`id`,`status`,`mode`,`createdDept`,`code`,`appIds`,`level`,`desc`,`createdBy`,`emergencyType` as type,onlineTime as realEndTime')->from(TABLE_CREDIT)
                ->where('status')->notin(['deleted','waitsubmit'])
                ->andWhere(' (createdDept=0 or createdDept is null) ')
                ->andWhere('createdDate')->between($start, $end)
                ->orderBy("id_desc")
                ->fetchAll('id');

        }else{
            $creditList = $this->dao->select('`id`,`status`,`mode`,`createdDept`,`code`,`appIds`,`level`,`desc`,`createdBy`,`emergencyType` as type,onlineTime as realEndTime')->from(TABLE_CREDIT)
                ->where('status')->notin(['deleted','waitsubmit'])
                ->beginIF($deptID && $realusedepts)->andWhere('createdDept')->in($realusedepts)->fi()
                ->andWhere('createdDate')->between($start, $end)
                ->orderBy("id_desc")
                ->fetchAll('id');
        }


        foreach ($creditList as $credit){
            $credit->exybtjsource = 'credit';
        }

        return array_values($creditList);
    }
    public function getCreditDataListByIDs($ids){

        $creditList = $this->dao->select('`id`,`status`,`mode`,`createdDept`,`code`,`appIds`,`level`,`desc`,`createdBy`,`type` as type,onlineTime as realEndTime')->from(TABLE_CREDIT)
            ->where('status')->ne('deleted')
            ->andWhere('id')->in($ids)
            ->orderBy("id_desc")
            ->fetchAll();
        foreach ($creditList as $credit){
            $credit->exybtjsource = 'credit';
        }

        return $creditList;
    }
    public function modifywholeStatic($modifywhoeData,$deptID=0){

        $useIDS = ['modify'=>[],'modifycncc'=>[],'credit'=>[]];
        $modifywholeDetailArr = ['modify'=>[],'modifycncc'=>[],'credit'=>[]];
        $modifywholeIdArr = ['modify'=>[],'modifycncc'=>[],'credit'=>[]];
        $deptIDArr = [];

//        $needStaticDeptList = $this->getNeedStaticDept();
        $needShowDeptList = $this->getNeedShowDept();

        /*$modifyList = $this->dao->select('`id`,`status`,`mode`,`createdDept`')->from(TABLE_MODIFY)
            ->where('status')->ne('deleted')
            ->andWhere('createdDate')->between($timeFrame['startdate'], $timeFrame['enddate'])
            ->fetchAll();*/


        $countData = [];
        $deptParent = $this->loadModel('dept')->getDeptAndChild();
        //按部门-》分类 统计
        //金信变更单总数  modifyCountNum
        //金信变更异常单总数  abnormalNum
        /*$abnormalNum = 'abnormalNum';
        $modifyCountNum = 'modifyCountNum';*/
        //金信数据处理
        foreach ($modifywhoeData['modify'] as $modify){

            if(!isset($this->lang->secondmonthreport->modifyUseStatus[$modify->status])){
                continue;
            }
            if(!$modify->mode){
                continue;
            }
            if(!$modify->createdDept){
                $modify->createdDept = -1;
            }
//            $modify->mode = 'a'.$modify->mode;
            /*if(!in_array($modify->createdDept,$needStaticDeptList)){
                continue;
            }*/

            if(!isset($countData[$deptParent[$modify->createdDept]][$this->lang->secondmonthreport->modifyMapmodeList[$modify->mode]])){
                $countData[$deptParent[$modify->createdDept]][$this->lang->secondmonthreport->modifyMapmodeList[$modify->mode]] = 1;
            }else{
                $countData[$deptParent[$modify->createdDept]][$this->lang->secondmonthreport->modifyMapmodeList[$modify->mode]]++;
            }
            $useIDS['modify'][] = $modify->id;
            $modifywholeIdArr['modify'][$deptParent[$modify->createdDept]][$this->lang->secondmonthreport->modifyMapmodeList[$modify->mode]][] = $modify->id;
            $modifywholeDetailArr['modify'][$deptParent[$modify->createdDept]][$this->lang->secondmonthreport->modifyMapmodeList[$modify->mode]][$modify->id] = $modify;
            $modifywholeIdArr['modify'][$deptParent[$modify->createdDept]]['total'][] = $modify->id;
            $modifywholeDetailArr['modify'][$deptParent[$modify->createdDept]]['total'][$modify->id] = $modify;
            /*if(!isset($countData[$deptParent[$modify->createdDept]][$modifyCountNum])){
                $countData[$deptParent[$modify->createdDept]][$modifyCountNum] = 1;
            }else{
                $countData[$deptParent[$modify->createdDept]][$modifyCountNum]++;
            }*/
            //异常单
            /*if(in_array($modify->status,$this->lang->secondmonthreport->modifyreissueArray)){
                if(!isset($countData[$deptParent[$modify->createdDept]][$abnormalNum])){
                    $countData[$deptParent[$modify->createdDept]][$abnormalNum] = 1;

                }else{
                    $countData[$deptParent[$modify->createdDept]][$abnormalNum]++;
                }
            }*/

        }

        //清总统计
        /*$modifyccList = $this->dao->select('`id`,`status`,`mode`,`createdDept`')->from(TABLE_MODIFYCNCC)
            ->where('status')->ne('deleted')
            ->andWhere('deleted')->ne('1')
            ->andWhere('createdDate')->between($timeFrame['startdate'], $timeFrame['enddate'])
            ->fetchAll();*/

        //基于金信的数据   拼接清总数据
        foreach ($modifywhoeData['modifycncc'] as $modifycncc){

            if(!isset($this->lang->secondmonthreport->modifyccUseStatus[$modifycncc->status])){
                continue;
            }
            if(!$modifycncc->mode){
                continue;
            }
            if(!$modifycncc->createdDept){
                $modifycncc->createdDept = -1;
            }
            /*if(!in_array($modifycc->createdDept,$needStaticDeptList)){
                continue;
            }*/
            if(!isset($countData[$deptParent[$modifycncc->createdDept]][$this->lang->secondmonthreport->modifyccMapmodeList[$modifycncc->mode]])){
                $countData[$deptParent[$modifycncc->createdDept]][$this->lang->secondmonthreport->modifyccMapmodeList[$modifycncc->mode]] = 1;
            }else{
                $countData[$deptParent[$modifycncc->createdDept]][$this->lang->secondmonthreport->modifyccMapmodeList[$modifycncc->mode]]++;
            }
            $useIDS['modifycncc'][] = $modifycncc->id;

            $modifywholeIdArr['modifycncc'][$deptParent[$modifycncc->createdDept]][$this->lang->secondmonthreport->modifyccMapmodeList[$modifycncc->mode]][] = $modifycncc->id;
            $modifywholeDetailArr['modifycncc'][$deptParent[$modifycncc->createdDept]][$this->lang->secondmonthreport->modifyccMapmodeList[$modifycncc->mode]][$modifycncc->id] = $modifycncc;
            $modifywholeIdArr['modifycncc'][$deptParent[$modifycncc->createdDept]]['total'][] = $modifycncc->id;
            $modifywholeDetailArr['modifycncc'][$deptParent[$modifycncc->createdDept]]['total'][$modifycncc->id] = $modifycncc;
            /*if(!isset($countData[$deptParent[$modifycc->createdDept]][$modifyCountNum])){
                $countData[$deptParent[$modifycc->createdDept]][$modifyCountNum] = 1;
            }else{
                $countData[$deptParent[$modifycc->createdDept]][$modifyCountNum]++;
            }*/
            //异常单
            /*if(in_array($modifycc->status,$this->lang->secondmonthreport->modifyccreissueArray)){
                if(!isset($countData[$deptParent[$modifycc->createdDept]][$abnormalNum])){
                    $countData[$deptParent[$modifycc->createdDept]][$abnormalNum] = 1;

                }else{
                    $countData[$deptParent[$modifycc->createdDept]][$abnormalNum]++;
                }
            }*/

        }

        //基于金信/清总的数据   拼接征信交付
        $this->app->loadLang('credit');
        foreach ($modifywhoeData['credit'] as $credit){

            if($credit->status == 'cancel'){
                continue;
            }
            if(!$credit->mode){
                continue;
            }
            if(!$credit->createdDept){
                $credit->createdDept = -1;
            }
            if(!isset($countData[$deptParent[$credit->createdDept]][$this->lang->secondmonthreport->modifyccMapmodeList[$credit->mode]])){
                $countData[$deptParent[$credit->createdDept]][$this->lang->secondmonthreport->modifyccMapmodeList[$credit->mode]] = 1;
            }else{
                $countData[$deptParent[$credit->createdDept]][$this->lang->secondmonthreport->modifyccMapmodeList[$credit->mode]]++;
            }
            $useIDS['credit'][] = $credit->id;

            $modifywholeIdArr['credit'][$deptParent[$credit->createdDept]][$this->lang->secondmonthreport->modifyccMapmodeList[$credit->mode]][] = $credit->id;
            $modifywholeDetailArr['credit'][$deptParent[$credit->createdDept]][$this->lang->secondmonthreport->modifyccMapmodeList[$credit->mode]][$credit->id] = $credit;
            $modifywholeIdArr['credit'][$deptParent[$credit->createdDept]]['total'][] = $credit->id;
            $modifywholeDetailArr['credit'][$deptParent[$credit->createdDept]]['total'][$credit->id] = $credit;
        }

        //每组数据补全分类 清总和金信分类一致
        foreach ($this->lang->secondmonthreport->modifyUsemodeList as $status=>$val){
            if(!$status){
                continue;
            }
            foreach ($countData as $dept=>$dataArr){
                if(!isset($dataArr[$status])){
                    $countData[$dept][$status] = 0;
                }
            }
        }

        //补齐部门
        if(!$deptID) {
            foreach ($needShowDeptList as $deptVal) {
                if (!isset($countData[$deptVal])) {
                    foreach ($this->lang->secondmonthreport->modifyUsemodeList as $type => $val) {
                        if (!$type) {
                            continue;
                        }
                        $countData[$deptVal][$type] = 0;
                    }
                }
            }
        }


        //行补充合计
        foreach ($countData as $dept=>$dataArr){
            $countData[$dept]['total']=0;
            foreach ($dataArr as $tkey=>$data){
                /*if(!in_array($tkey,['modifyCountNum','abnormalNum'])){

                }*/
                $countData[$dept]['total'] += $data;
            }
        }

        //计算 变更异常率  并且去除无用字段
        /*foreach ($countData as $ldept=>$ldataArr){
            if(isset($ldataArr[$abnormalNum])){
                $countData[$ldept]['banormalrate'] = sprintf("%.2f",($ldataArr[$abnormalNum]/$ldataArr[$modifyCountNum])*100);
                unset($countData[$ldept][$abnormalNum]);
            }else{
                $countData[$ldept]['banormalrate'] = "0.00";
            }
            unset($countData[$ldept][$modifyCountNum]);
        }*/

        //剔除 不是统计部门中部门数据为 0 的数据
        foreach ($countData as $dept=>$dataArr){
            if(!$dataArr['total'] && !in_array($dept,$needShowDeptList)){
                unset($countData[$dept]);
                continue;
            }
            $deptIDArr[] = $dept;
        }
        //反馈用到的数据，给接下来生成快照的方法使用。
        $useIDS['modifycncc'] = array_unique($useIDS['modifycncc']);
        $useIDS['modify'] = array_unique($useIDS['modify']);
        $useIDS['credit'] = array_unique($useIDS['credit']);
        return ['useids'=>$useIDS,'deptcolumids'=>$modifywholeIdArr,'staticdata'=>$countData,'detail'=>$modifywholeDetailArr,'deptids'=>$deptIDArr,'multkey'=>['modify','modifycncc','credit']];

    }
    public function modifyabnormalStatic($modifyabnormalData,$deptID=0){


        $useIDS = ['modify'=>[],'modifycncc'=>[],'credit'=>[]];
        $modifyabnormalDetailArr = ['modify'=>[],'modifycncc'=>[],'credit'=>[]];
        $modifyabnormalIdArr = ['modify'=>[],'modifycncc'=>[],'credit'=>[]];
        $deptIDArr = [];

        $needShowDeptList = $this->getNeedShowDept();
        //变更单总数  modifyCountNum
        //变更异常单总数  abnormalNum
        $abnormalNum = 'abnormalNum';
        $modifyCountNum = 'modifyCountNum';
        $deptParent = $this->loadModel('dept')->getDeptAndChild();

        /*$modifyList = $this->dao->select('`id`,`code`,`mode`,`desc`,`realEndTime` as `actualEnd`,`status`,`createdDept`')->from(TABLE_MODIFY)
            ->where('status')->in($this->lang->secondmonthreport->modifyUseStatus)
//            ->where('status')->ne('deleted')
            ->andWhere('createdDate')->between($timeFrame['startdate'], $timeFrame['enddate'])
            ->fetchAll();*/
        $countData = [];
        foreach ($modifyabnormalData['modify'] as $modify){
            if(!isset($this->lang->secondmonthreport->modifyUseStatus[$modify->status])){
                continue;
            }

            if(!$modify->createdDept){
                $modify->createdDept = -1;
            }
            if(!isset($countData[$deptParent[$modify->createdDept]][$modifyCountNum])){
                $countData[$deptParent[$modify->createdDept]][$modifyCountNum] = 1;
            }else{
                $countData[$deptParent[$modify->createdDept]][$modifyCountNum]++;
            }

            $modifyabnormalIdArr['modify'][$deptParent[$modify->createdDept]][$modifyCountNum][] = $modify->id;
            $modifyabnormalDetailArr['modify'][$deptParent[$modify->createdDept]][$modifyCountNum][$modify->id] = $modify;
            //异常单
            if(in_array($modify->status,$this->lang->secondmonthreport->modifyreissueArray)){
                if(!isset($countData[$deptParent[$modify->createdDept]][$abnormalNum])){
                    $countData[$deptParent[$modify->createdDept]][$abnormalNum] = 1;

                }else{
                    $countData[$deptParent[$modify->createdDept]][$abnormalNum]++;
                }

                $useIDS['modify'][] = $modify->id;
                $modifyabnormalIdArr['modify'][$deptParent[$modify->createdDept]][$abnormalNum][] = $modify->id;
                $modifyabnormalDetailArr['modify'][$deptParent[$modify->createdDept]][$abnormalNum][$modify->id] = $modify;
            }
        }


        //清总统计
        /*$modifyccList = $this->dao->select('`id`,`code`,`mode`,`desc`,`actualEnd`,`status`,`createdDept`')->from(TABLE_MODIFYCNCC)
//            ->where('status')->ne('deleted')
            ->where('deleted')->ne('1')
            ->andWhere('status')->in($this->lang->secondmonthreport->modifyccUseStatus)
//            ->andWhere('status')->in($this->lang->secondmonthreport->modifyccUseStatus)
            ->andWhere('createdDate')->between($timeFrame['startdate'], $timeFrame['enddate'])
            ->fetchAll();*/
        foreach ($modifyabnormalData['modifycncc'] as $modifycncc){
            if(!isset($this->lang->secondmonthreport->modifyccUseStatus[$modifycncc->status])){
                continue;
            }

            if(!$modifycncc->createdDept){
                $modifycncc->createdDept = -1;
            }
            if(!isset($countData[$deptParent[$modifycncc->createdDept]][$modifyCountNum])){
                $countData[$deptParent[$modifycncc->createdDept]][$modifyCountNum] = 1;
            }else{
                $countData[$deptParent[$modifycncc->createdDept]][$modifyCountNum]++;
            }
            $modifyabnormalIdArr['modifycncc'][$deptParent[$modifycncc->createdDept]][$modifyCountNum][] = $modifycncc->id;
            $modifyabnormalDetailArr['modifycncc'][$deptParent[$modifycncc->createdDept]][$modifyCountNum][$modifycncc->id] = $modifycncc;
            //异常单
            if(in_array($modifycncc->status,$this->lang->secondmonthreport->modifyccreissueArray)){
                if(!isset($countData[$deptParent[$modifycncc->createdDept]][$abnormalNum])){
                    $countData[$deptParent[$modifycncc->createdDept]][$abnormalNum] = 1;

                }else{
                    $countData[$deptParent[$modifycncc->createdDept]][$abnormalNum]++;
                }
                $useIDS['modifycncc'][] = $modifycncc->id;
                $modifyabnormalIdArr['modifycncc'][$deptParent[$modifycncc->createdDept]][$abnormalNum][] = $modifycncc->id;
                $modifyabnormalDetailArr['modifycncc'][$deptParent[$modifycncc->createdDept]][$abnormalNum][$modifycncc->id] = $modifycncc;
            }
        }

        foreach ($modifyabnormalData['credit'] as $credit){
            if($credit->status == 'cancel'){
                continue;
            }

            if(!$credit->createdDept){
                $credit->createdDept = -1;
            }
            if(!isset($countData[$deptParent[$credit->createdDept]][$modifyCountNum])){
                $countData[$deptParent[$credit->createdDept]][$modifyCountNum] = 1;
            }else{
                $countData[$deptParent[$credit->createdDept]][$modifyCountNum]++;
            }

            $modifyabnormalIdArr['credit'][$deptParent[$credit->createdDept]][$modifyCountNum][] = $credit->id;
            $modifyabnormalDetailArr['credit'][$deptParent[$credit->createdDept]][$modifyCountNum][$credit->id] = $credit;
            //异常单
            if(in_array($credit->status,$this->lang->secondmonthreport->creditreissueArray)){
                if(!isset($countData[$deptParent[$credit->createdDept]][$abnormalNum])){
                    $countData[$deptParent[$credit->createdDept]][$abnormalNum] = 1;
                }else{
                    $countData[$deptParent[$credit->createdDept]][$abnormalNum]++;
                }

                $useIDS['credit'][] = $credit->id;
                $modifyabnormalIdArr['credit'][$deptParent[$credit->createdDept]][$abnormalNum][] = $credit->id;
                $modifyabnormalDetailArr['credit'][$deptParent[$credit->createdDept]][$abnormalNum][$credit->id] = $credit;
            }
        }

        foreach ($countData as $ldept=>$ldataArr){
            if(isset($ldataArr[$abnormalNum])){
                $countData[$ldept]['banormalrate'] = sprintf("%.2f",($ldataArr[$abnormalNum]/$ldataArr[$modifyCountNum])*100);

            }else{
                $countData[$ldept]['banormalrate'] = "0.00";
            }
        }
        //补齐属性
        foreach ([$abnormalNum=>$abnormalNum,$modifyCountNum=>$modifyCountNum] as $status=>$val){
            if(!$status){
                continue;
            }
            foreach ($countData as $dept=>$dataArr){
                if(!isset($dataArr[$status])){
                    $countData[$dept][$status] = 0;
                }
            }
        }

        //补齐部门
        if(!$deptID){
            foreach ($needShowDeptList as $deptVal){
                if(!isset($countData[$deptVal])){
                    $countData[$deptVal][$abnormalNum] = '0';
                    $countData[$deptVal][$modifyCountNum] = '0';
                    $countData[$deptVal]['banormalrate'] = "0.00";
                }
            }
        }

        //剔除 不是统计部门中部门数据为 0 的数据
        foreach ($countData as $dept=>$dataArr){
            if(!$dataArr[$abnormalNum] && !$dataArr[$modifyCountNum] && !in_array($dept,$needShowDeptList)){
                unset($countData[$dept]);
                continue;
            }
            $deptIDArr[] = $dept;
        }
        //反馈用到的数据，给接下来生成快照的方法使用。
        $useIDS['modifycncc'] = array_unique($useIDS['modifycncc']);
        $useIDS['modify'] = array_unique($useIDS['modify']);
        $useIDS['credit'] = array_unique($useIDS['credit']);
        return ['useids'=>$useIDS,'deptcolumids'=>$modifyabnormalIdArr,'staticdata'=>$countData,'detail'=>$modifyabnormalDetailArr,'deptids'=>$deptIDArr,'multkey'=>['modify','modifycncc','credit']];


        //补齐部门 具体数据 不需要补齐部门
        /*foreach ($needShowDeptList as $deptVal){
            if(!isset($countData[$deptVal])){
                $dClass = new stdClass();
                $dClass->code = '';
                $dClass->mode = '';
                $dClass->desc = '';
                $dClass->actualEnd = '';
                $dClass->status = '';
                $dClass->createdDept = $deptVal;
                $countData[$deptVal][0] = $dClass;
            }
        }*/

    }
    public function modifyabnormalSave($modifyabnormalIdArr,$countData,$formType,$time,$timeFrame){

        $modifyabnormalId = $this->modifymonthreportadd($timeFrame, $time,$formType);
        //将 下钻查看用到的ID 更新到 whole_report表中
        $this->dao->update(TABLE_WHOLE_REPORT)->data(['useIDArr'=>json_encode($modifyabnormalIdArr)])->where('id')->eq($modifyabnormalId)->exec();

        foreach ($countData as $deptId => $cdata) {

            $arr   = [
                'deptID'      => $deptId,
                'tableType'   => $formType,
                'wholeID'     => $modifyabnormalId,
                'detail'      => json_encode($cdata),
                'createdDate' => date('Y-m-d H:i:s'),
            ];
            $this->dao->insert(TABLE_DETAIL_REPORT)->data($arr)->exec();
        }

        return $modifyabnormalId;
    }
    public function modifywholeSave($modifywholeIdArr,$countData,$formType,$time,$timeFrame){

        $modifywholeId = $this->modifymonthreportadd($timeFrame, $time,$formType);
        //将 下钻查看用到的ID 更新到 whole_report表中
        $this->dao->update(TABLE_WHOLE_REPORT)->data(['useIDArr'=>json_encode($modifywholeIdArr)])->where('id')->eq($modifywholeId)->exec();

        foreach ($countData as $deptId => $cdata) {

            $arr   = [
                'deptID'      => $deptId,
                'tableType'   => $formType,
                'wholeID'     => $modifywholeId,
                'detail'      => json_encode($cdata),
                'createdDate' => date('Y-m-d H:i:s'),
            ];
            $this->dao->insert(TABLE_DETAIL_REPORT)->data($arr)->exec();
        }
    }
    public function getWorkloadDataList($start,$end,$deptID,$staticType,$isuseHisData){
        $realusedepts = [];
        if($deptID){
            $realusedepts = $this->getRealUseDepts($deptID);
        }
        $getSecondLineProject = $this->getSecondLineProject();

        $effortList = $this->dao->select('t2.id,t1.id as taskid,t2.deptID,t2.consumed,t1.name,t1.source,t2.`date`,t2.`account`')->from(TABLE_TASK)->alias('t1')
            ->leftJoin(TABLE_EFFORT)->alias('t2')->on('t1.id = t2.objectID')
            ->where('t2.deleted')->eq('0')
            ->andWhere('t1.deleted ')->eq('0')
            ->andWhere('t2.objectType')->eq('task')
            ->andWhere('t2.source')->ne('2')
            ->andWhere('t2.`date`')->between($start, $end)
            ->andWhere('t1.project')->in($getSecondLineProject)
            ->beginIF($deptID && $realusedepts)->andWhere('t2.deptID')->in($realusedepts)->fi()
            ->orderBy("id_desc")
            ->fetchAll('id');

        return $effortList;
    }

    public function getWorkloadDataListByIDs($ids){

        $effortList = $this->dao->select('t2.id,t2.deptID,t2.consumed,t1.name,t1.source,t2.`date`,t2.`account`')->from(TABLE_TASK)->alias('t1')
            ->leftJoin(TABLE_EFFORT)->alias('t2')->on('t1.id = t2.objectID')
            ->where('t2.deleted')->eq('0')
            ->andWhere('t1.deleted ')->eq('0')
            ->andWhere('t2.objectType')->eq('task')
            ->andWhere('t2.source')->ne('2')
            ->andWhere('t2.id')->in($ids)
            ->orderBy("t2.id_desc")
            ->fetchAll('id');

        return $effortList;

    }
    public function workloadStatic($workloadData,$deptID=0){

        $needShowDeptList = $this->getNeedShowDept();

        $useIDS = [];
        $workloadDetailArr = [];
        $workloadIdArr = [];
        $deptIDArr = [];


        $countData = [];
        $deptParent = $this->loadModel('dept')->getDeptAndChild();


        foreach ($workloadData as $effort){
            if(!$effort->deptID){
                $effort->deptID = -1;
            }

            //问题单
            if(strpos($effort->name,'CFIT-Q-') !== false && $effort->source == 1){
                $stype = $this->lang->secondmonthreport->workloadMapTypeList['secondproblem'];
                //|| strpos($effort->name,'CFIT-WD-') !== false
            }else if((strpos($effort->name,'CFIT-D-') !== false ) && $effort->source == 1){ //需求池内部 去除 需求池内部数据
                //需求单
                $stype = $this->lang->secondmonthreport->workloadMapTypeList['seconddemand'];
            }else if(strpos($effort->name,'CFIT-T-') !== false && $effort->source == 1){
                $stype = $this->lang->secondmonthreport->workloadMapTypeList['secondorder'];
            }else if ($effort->source == 0 || ($effort->source == 1 && $effort->name == '其他类型')){
                //自建任务
                $stype = $this->lang->secondmonthreport->workloadMapTypeList['secondcustom'];

            }else{
                continue;
            }

            if(!isset($countData[$deptParent[$effort->deptID]][$stype])){
                $countData[$deptParent[$effort->deptID]][$stype] = $effort->consumed;

            }else{
                $countData[$deptParent[$effort->deptID]][$stype] += $effort->consumed;
            }
            $useIDS[] = $effort->id;
            $workloadIdArr[$deptParent[$effort->deptID]][$stype][] = $effort->id;
            $workloadDetailArr[$deptParent[$effort->deptID]][$stype][$effort->id] = $effort;
            $workloadIdArr[$deptParent[$effort->deptID]]['countPeopleMonth'][] = $effort->id;
            $workloadDetailArr[$deptParent[$effort->deptID]]['countPeopleMonth'][$effort->id] = $effort;

        }

        //人月转换
        $monthworkload = floatval($this->lang->secondmonthreport->monthReportWorkHours['workHours'])*8;
        foreach ($countData as $divdept=>$divDataArr){
            foreach ($divDataArr as $divTypeKey=>$divTypeValue){
                $countData[$divdept][$divTypeKey] = sprintf("%.2f",$divTypeValue/$monthworkload);
            }

        }


        //每组数据补全分类
        foreach ($this->lang->secondmonthreport->workloadMapTypeList as $type=>$val){
            if(!$val){
                continue;
            }
            foreach ($countData as $dept=>$dataArr){
                if(!isset($dataArr[$val])){
                    $countData[$dept][$val] = '0.00';
                }
            }
        }

        //补齐部门
        if(!$deptID){
            foreach ($needShowDeptList as $deptVal){
                if(!isset($countData[$deptVal])){
                    foreach ($this->lang->secondmonthreport->workloadMapTypeList as $type=>$val){
                        if(!$val){
                            continue;
                        }
                        $countData[$deptVal][$val] = '0.00';
                    }
                }
            }
        }


        //行补充合计

        foreach ($countData as $dept=>$dataArr){
            $countData[$dept]['countPeopleMonth'] = array_sum($countData[$dept]);

        }

        //剔除 不是统计部门中部门数据为 0 的数据
        foreach ($countData as $dept=>$dataArr){
            if(!$dataArr['countPeopleMonth']  && !in_array($dept,$needShowDeptList)){
                unset($countData[$dept]);
                continue;
            }
            $deptIDArr[] = $dept;
        }

        //反馈用到的数据，给接下来生成快照的方法使用。
        return ['useids'=>array_unique($useIDS),'deptcolumids'=>$workloadIdArr,'staticdata'=>$countData,'detail'=>$workloadDetailArr,'deptids'=>$deptIDArr];

    }
    public function workloadSave($workloadIdArr,$countData,$formType,$time,$timeFrame){
        $workloadId = $this->workloadmonthreportadd($timeFrame, $time,$formType);
        //将 下钻查看用到的ID 更新到 whole_report表中

        $this->dao->update(TABLE_WHOLE_REPORT)->data(['useIDArr'=>json_encode($workloadIdArr)])->where('id')->eq($workloadId)->exec();

        foreach ($countData as $deptId => $cdata) {

            $arr   = [
                'deptID'      => $deptId,
                'tableType'   => $formType,
                'wholeID'     => $workloadId,
                'detail'      => json_encode($cdata),
                'createdDate' => date('Y-m-d H:i:s'),
            ];
            $this->dao->insert(TABLE_DETAIL_REPORT)->data($arr)->exec();
        }

        return $workloadId;
    }
    public function supportStatic($supportData,$deptID=0){

        $useIDS = [];
        $supportDetailArr = [];
        $supportIdArr = [];
        $deptIDArr = [];

        $needShowDeptList = $this->getNeedShowDept();

        $countData = [];
        $deptParent = $this->loadModel('dept')->getDeptAndChild();
        //按部门-》分类 统计
        foreach ($supportData as $support){
            if(!$support->dept){
                $support->dept = -1;
            }
            if(!isset($this->lang->secondmonthreport->supportMapStypeList[$support->stype])){
                continue;
            }

            if(!isset($countData[$deptParent[$support->dept]][$this->lang->secondmonthreport->supportMapStypeList[$support->stype]])){
                $countData[$deptParent[$support->dept]][$this->lang->secondmonthreport->supportMapStypeList[$support->stype]] = $support->workh;

            }else{
                $countData[$deptParent[$support->dept]][$this->lang->secondmonthreport->supportMapStypeList[$support->stype]] += $support->workh;
            }
            $useIDS[] = $support->id;
            $supportIdArr[$deptParent[$support->dept]][$this->lang->secondmonthreport->supportMapStypeList[$support->stype]][] = $support->id;
            $supportDetailArr[$deptParent[$support->dept]][$this->lang->secondmonthreport->supportMapStypeList[$support->stype]][$support->id] = $support;
            $supportIdArr[$deptParent[$support->dept]]['total'][] = $support->id;
            $supportDetailArr[$deptParent[$support->dept]]['total'][$support->id] = $support;
        }

        //人月换算
        /*$monthworkload = floatval($this->lang->secondmonthreport->monthReportWorkHours['workHours'])*8;
        foreach ($countData as $divdept=>$divDataArr){
            foreach ($divDataArr as $divTypeKey=>$divTypeValue){
                $countData[$divdept][$divTypeKey] = sprintf("%.2f",$divTypeValue/$monthworkload);;
            }
        }*/


        //每组数据补全分类
        foreach ($this->lang->secondmonthreport->supportMapStypeList as $type=>$val){
            if(!$val){
                continue;
            }
            foreach ($countData as $dept=>$dataArr){
                if(!isset($dataArr[$val])){
                    $countData[$dept][$val] = 0;
                }
            }
        }

        //补齐部门
        if(!$deptID){
            foreach ($needShowDeptList as $deptVal){
                if(!isset($countData[$deptVal])){
                    foreach ($this->lang->secondmonthreport->supportMapStypeList as $type=>$val){
                        $countData[$deptVal][$val] = 0;
                    }
                }
            }
        }


        //行补充合计
        foreach ($countData as $dept=>$dataArr){
            $countData[$dept]['total'] = array_sum($countData[$dept]);
        }

        //剔除 不是统计部门中部门数据为 0 的数据
        foreach ($countData as $dept=>$dataArr){
            if(!$dataArr['total'] && !in_array($dept,$needShowDeptList)){
                unset($countData[$dept]);
                continue;
            }
            $deptIDArr[] = $dept;
        }
        //反馈用到的数据，给接下来生成快照的方法使用。
        return ['useids'=>array_unique($useIDS),'deptcolumids'=>$supportIdArr,'staticdata'=>$countData,'detail'=>$supportDetailArr,'deptids'=>$deptIDArr];

    }
    public function supportSave($supportIdArr,$countData,$formType,$time,$timeFrame){


        $supportId = $this->supportmonthreportadd($timeFrame, $time,$formType);
        //将 下钻查看用到的ID 更新到 whole_report表中

        $this->dao->update(TABLE_WHOLE_REPORT)->data(['useIDArr'=>json_encode($supportIdArr)])->where('id')->eq($supportId)->exec();

        foreach ($countData as $deptId => $cdata) {

            $arr   = [
                'deptID'      => $deptId,
                'tableType'   => $formType,
                'wholeID'     => $supportId,
                'detail'      => json_encode($cdata),
                'createdDate' => date('Y-m-d H:i:s'),
            ];
            $this->dao->insert(TABLE_DETAIL_REPORT)->data($arr)->exec();
        }

        return $supportId;
    }
    public function secondorderclassStatic($secondorderData,$deptID=0){

        //按照配置的部门 当统计数据中无此部门进行补全
        $needShowDeptList = $this->getNeedShowDept();


        $countData = [];
        $deptParent = $this->loadModel('dept')->getDeptAndChild();
        $useIDS = [];
        $secondorderclassDetailArr = [];
        $secondorderclassIdArr = [];
        $deptIDArr = [];
        //按部门-》分类 统计
        foreach ($secondorderData as $sencondorder){
            if(!$sencondorder->acceptDept){
                $sencondorder->acceptDept = -1;
            }
            //统计时不再过滤部门

            //如果是未受理的 忽略.
            /*if(isset($this->lang->secondmonthreport->secondorderFilterStatusList[$sencondorder->status])){
                continue;
            }*/
            //如果不再统计类型中，跳过
            if(!isset($this->lang->secondmonthreport->secondorderTypeList[$sencondorder->type])){
                continue;
            }
            if(!isset($countData[$deptParent[$sencondorder->acceptDept]][$sencondorder->type])){
                $countData[$deptParent[$sencondorder->acceptDept]][$sencondorder->type] = 1;
            }else{
                $countData[$deptParent[$sencondorder->acceptDept]][$sencondorder->type]++;
            }
            $useIDS[] = $sencondorder->id;
            $secondorderclassIdArr[$deptParent[$sencondorder->acceptDept]][$sencondorder->type][] = $sencondorder->id;
            $secondorderclassDetailArr[$deptParent[$sencondorder->acceptDept]][$sencondorder->type][$sencondorder->id] = $sencondorder;
            $secondorderclassIdArr[$deptParent[$sencondorder->acceptDept]]['total'][] = $sencondorder->id;
            $secondorderclassDetailArr[$deptParent[$sencondorder->acceptDept]]['total'][$sencondorder->id] = $sencondorder;
        }

        //每组数据补全分类
        foreach ($this->lang->secondmonthreport->secondorderTypeList as $type=>$val){
            if(!$type){
                continue;
            }
            foreach ($countData as $dept=>$dataArr){
                if(!isset($dataArr[$type])){
                    $countData[$dept][$type] = 0;
                }
            }
        }


        //补齐部门
        if(!$deptID){
            foreach ($needShowDeptList as $deptVal){
                if(!isset($countData[$deptVal])){
                    foreach ($this->lang->secondmonthreport->secondorderTypeList as $type=>$val){
                        if(!$type){
                            continue;
                        }
                        $countData[$deptVal][$type] = 0;
                    }
                }
            }
        }
        //行补充合计
        foreach ($countData as $dept=>$dataArr){
            $countData[$dept]['total'] = array_sum($countData[$dept]);
        }

        //剔除 不是统计部门中部门数据为 0 的数据
        foreach ($countData as $dept=>$dataArr){
            if(!$dataArr['total'] && !in_array($dept,$needShowDeptList)){
                unset($countData[$dept]);
                continue;
            }
            $deptIDArr[] = $dept;
        }
        //反馈用到的数据，给接下来生成快照的方法使用。
        return ['useids'=>array_unique($useIDS),'deptcolumids'=>$secondorderclassIdArr,'staticdata'=>$countData,'detail'=>$secondorderclassDetailArr,'deptids'=>$deptIDArr];

    }

    public function secondorderclassSave($secondorderclassIdArr,$countData,$formType,$time,$timeFrame){


        $secondorderclassId = $this->secondordermonthreportadd($timeFrame, $time,$formType);
        //将 下钻查看用到的ID 更新到 whole_report表中

        $this->dao->update(TABLE_WHOLE_REPORT)->data(['useIDArr'=>json_encode($secondorderclassIdArr)])->where('id')->eq($secondorderclassId)->exec();

        foreach ($countData as $deptId => $cdata) {

            $arr   = [
                'deptID'      => $deptId,
                'tableType'   => $formType,
                'wholeID'     => $secondorderclassId,
                'detail'      => json_encode($cdata),
                'createdDate' => date('Y-m-d H:i:s'),
            ];
            $this->dao->insert(TABLE_DETAIL_REPORT)->data($arr)->exec();
        }

        return $secondorderclassId;
    }
    public function secondorderacceptStatic($secondorderData,$deptID=0){
        $needShowDeptList = $this->getNeedShowDept();

        $useIDS = [];
        $secondorderacceptDetailArr = [];
        $secondorderacceptIdArr = [];
        $deptIDArr = [];

        $countData = [];
        $deptParent = $this->loadModel('dept')->getDeptAndChild();
        //按部门-》分类 统计
        foreach ($secondorderData as $sencondorder){
            if(!$sencondorder->acceptDept){
                $sencondorder->acceptDept = -1;
            }
            if(!isset($this->lang->secondmonthreport->secondorderMapStatusList[$sencondorder->status])){
                continue;
            }

            if(!isset($countData[$deptParent[$sencondorder->acceptDept]][$this->lang->secondmonthreport->secondorderMapStatusList[$sencondorder->status]])){
                $countData[$deptParent[$sencondorder->acceptDept]][$this->lang->secondmonthreport->secondorderMapStatusList[$sencondorder->status]] = 1;

            }else{
                $countData[$deptParent[$sencondorder->acceptDept]][$this->lang->secondmonthreport->secondorderMapStatusList[$sencondorder->status]]++;
            }
            $useIDS[] = $sencondorder->id;
            $secondorderacceptIdArr[$deptParent[$sencondorder->acceptDept]][$this->lang->secondmonthreport->secondorderMapStatusList[$sencondorder->status]][] = $sencondorder->id;
            $secondorderacceptDetailArr[$deptParent[$sencondorder->acceptDept]][$this->lang->secondmonthreport->secondorderMapStatusList[$sencondorder->status]][$sencondorder->id] = $sencondorder;
            $secondorderacceptIdArr[$deptParent[$sencondorder->acceptDept]]['total'][] = $sencondorder->id;
            $secondorderacceptDetailArr[$deptParent[$sencondorder->acceptDept]]['total'][$sencondorder->id] = $sencondorder;
        }


        //每组数据补全分类
        foreach ($this->lang->secondmonthreport->secondorderMapStatusUseList as $status=>$val){
            if(!$status){
                continue;
            }
            foreach ($countData as $dept=>$dataArr){
                if(!isset($dataArr[$status])){
                    $countData[$dept][$status] = 0;
                }

            }
        }

        //补齐部门
        if(!$deptID){
            foreach ($needShowDeptList as $deptVal){
                if(!isset($countData[$deptVal])){
                    foreach ($this->lang->secondmonthreport->secondorderMapStatusUseList as $status=>$val){
                        if(!$status){
                            continue;
                        }
                        $countData[$deptVal][$status] = 0;
                    }
                }
            }
        }
        //行补充合计
        foreach ($countData as $dept=>$dataArr){
            $countData[$dept]['total'] = array_sum($countData[$dept]);
        }

        //剔除 不是统计部门中部门数据为 0 的数据
        foreach ($countData as $dept=>$dataArr){
            if(!$dataArr['total'] && !in_array($dept,$needShowDeptList)){
                unset($countData[$dept]);
                continue;
            }
            $deptIDArr[] = $dept;
        }

        //反馈用到的数据，给接下来生成快照的方法使用。
        return ['useids'=>array_unique($useIDS),'deptcolumids'=>$secondorderacceptIdArr,'staticdata'=>$countData,'detail'=>$secondorderacceptDetailArr,'deptids'=>$deptIDArr];

    }
    public function secondorderacceptSave($secondorderacceptIdArr,$countData,$formType,$time,$timeFrame){


        $secondorderacceptId = $this->secondordermonthreportadd($timeFrame, $time,$formType);
        //将 下钻查看用到的ID 更新到 whole_report表中

        $this->dao->update(TABLE_WHOLE_REPORT)->data(['useIDArr'=>json_encode($secondorderacceptIdArr)])->where('id')->eq($secondorderacceptId)->exec();

        foreach ($countData as $deptId => $cdata) {

            $arr   = [
                'deptID'      => $deptId,
                'tableType'   => $formType,
                'wholeID'     => $secondorderacceptId,
                'detail'      => json_encode($cdata),
                'createdDate' => date('Y-m-d H:i:s'),
            ];
            $this->dao->insert(TABLE_DETAIL_REPORT)->data($arr)->exec();
        }
    }
    public function demandwholeDemandMonthStatic($demandData,$deptID=0)
    {
        /*
         * 构造入湖数据数组
         * ①按照部门做统计
         * ②已实现：取值范围为需求条目的“实现方式”为二线实现，且“流程状态”字段的上线成功、已关闭、已挂起、已交付的年度累计
         * ③未实现：取值范围为需求条目的“实现方式”为二线实现，且“流程状态”字段的已录入、开发中、测试中、已发布、变更单退回、变更单异常的年度累计。
         */
        $useIDS = [];
        $demandwholeDetailArr = [];
        $demandwholeIdArr = [];
        $deptIDArr = [];
        $deptArr = array();
        $jsonWholeData = array();

        $deptParent = $this->loadModel('dept')->getDeptAndChild();


        $needShowDeptList = $this->getNeedShowDept();

        foreach ($demandData as $wholeValue)
        {
            if(!$wholeValue->acceptDept){
                $wholeValue->acceptDept = -1;
            }
//                if(in_array($wholeValue->acceptDept,$needAllStaticDeptList)) {
            $deptArr[$deptParent[$wholeValue->acceptDept]][] = $wholeValue;
//                }
        }

        $this->loadModel('demand');

        foreach ($deptArr as $i => $item)
        {
            if($i == 0) continue;

            $implementedNum = 0;//已实现数量
            $unrealizedNum  = 0;//未实现数量
            foreach ($item as $v)
            {
                //已实现
                if($v->fixType == 'second' && in_array($v->status,$this->lang->demand->implementedArr))
                {
                    $implementedNum++;
                    $useIDS[]=$v->id;
                    $demandwholeIdArr[$i]['total'][] = $v->id;
                    $demandwholeIdArr[$i]['implementedNum'][] = $v->id;
                    $demandwholeDetailArr[$i]['total'][$v->id] = $v;
                    $demandwholeDetailArr[$i]['implementedNum'][$v->id] = $v;
                }

                //未实现
                if($v->fixType == 'second' && in_array($v->status,$this->lang->demand->unrealizedArr))
                {
                    $unrealizedNum++;
                    $useIDS[]=$v->id;
                    $demandwholeIdArr[$i]['total'][] = $v->id;
                    $demandwholeIdArr[$i]['unrealizedNum'][] = $v->id;
                    $demandwholeDetailArr[$i]['total'][$v->id] = $v;
                    $demandwholeDetailArr[$i]['unrealizedNum'][$v->id] = $v;
                }

            }
            $total = $implementedNum + $unrealizedNum;
            if(!empty($total))
            {
                $realizationRate = $implementedNum/$total* 100;
            }else{
                $realizationRate = '0';
            }


            $jsonWholeData[$i]['deptID'] = $i;
            $jsonWholeData[$i]['implementedNum'] = $implementedNum;
            $jsonWholeData[$i]['unrealizedNum'] = $unrealizedNum;
            $jsonWholeData[$i]['total'] = $total;
            $jsonWholeData[$i]['realizationRate'] = number_format($realizationRate,2);

        }
        //补齐部门数据
        if(!$deptID) {
        foreach ($needShowDeptList as $alldept){
            if(!isset($jsonWholeData[$alldept])){
                $jsonWholeData[$alldept] = [
                    'deptID'=>$alldept,
                    'implementedNum'=>0,
                    'unrealizedNum'=>0,
                    'total'=>0,
                    'realizationRate'=>"0.00",
                ];
            }
        }
        }
        //剔除 不是统计部门中部门数据为 0 的数据

            foreach ($jsonWholeData as $dept => $dataArr) {
                if (!$dataArr['total'] && !in_array($dept, $needShowDeptList)) {
                    unset($jsonWholeData[$dept]);
                    continue;
                }
                $jsonWholeData[$dept]['realizationRate'] = $dataArr['total'] > 0 ? number_format(($dataArr['implementedNum'] / $dataArr['total']) * 100, 2) : "0.00";
                $deptIDArr[] = $dept;
            }

        //反馈用到的数据，给接下来生成快照的方法使用。
        return ['useids'=>array_unique($useIDS),'deptcolumids'=>$demandwholeIdArr,'staticdata'=>$jsonWholeData,'detail'=>$demandwholeDetailArr,'deptids'=>$deptIDArr];

    }

    public function demanddemandwholeSave($demandwholdIdArr,$demandwholes,$formType,$time,$timeFrame){
        $demandwholeId = $this->demandmonthreportadd($timeFrame, $time,$formType);
        //将 下钻查看用到的ID 更新到 whole_report表中

        $this->dao->update(TABLE_WHOLE_REPORT)->data(['useIDArr'=>json_encode($demandwholdIdArr)])->where('id')->eq($demandwholeId)->exec();

        foreach ($demandwholes as $deptId => $demandwhole) {

            $arr   = [
                'deptID'      => $deptId,
                'tableType'   => $formType,
                'wholeID'     => $demandwholeId,
                'detail'      => json_encode($demandwhole),
                'createdDate' => date('Y-m-d H:i:s'),
            ];
            $this->dao->insert(TABLE_DETAIL_REPORT)->data($arr)->exec();
        }
    }

    public function demandunrealizedStatic($unrealizedInfo,$deptID=0){

        $unrealizedData     = array();
        $deptArr            = array();
        $jsonUnrealizedData = array();
        $useIDS = [];

        $demandunrealizedDetailArr = [];
        $demandunrealizedIdArr = [];
        $deptIDArr = [];

        $end = helper::now();


        $deptParent = $this->loadModel('dept')->getDeptAndChild();

//        $needStaticDeptList = $reportModel->getNeedStaticDept();
        $needShowDeptList = $this->getNeedShowDept();

        foreach ($unrealizedInfo as $unrealizedValue)
        {
            if(!$unrealizedValue->acceptDept){
                $unrealizedValue->acceptDept = -1;
            }

            $deptArr[$deptParent[$unrealizedValue->acceptDept]][] = $unrealizedValue;

        }
        $this->loadModel("demand");
        foreach ($deptArr as $i => $item)
        {
            $demandletwoMonth = 0;
            $demandlesixMonth = 0;
            $demandletwelveMonth  = 0;
            $demandgttwelveMonth  = 0;

            foreach ($item as $v)
            {
                //需求条目的“实现方式”为二线实现
                if($v->fixType == 'second' && in_array($v->status,$this->lang->demand->unrealizedArr))
                {
                    $publishedTime = $v->publishedTime;
                    if(!empty($publishedTime) && $publishedTime != '0000-00-00' && $publishedTime != '0000-00-00 00:00:00')
                    {
                        //2个月内未实现需求数
                        $demandletwoMonthEndTime = $this->getOverDate($publishedTime,2);

                        $demandletwoMonthEndTime .= substr($publishedTime, 10);
                        if($demandletwoMonthEndTime >= $end) {
                            $demandletwoMonth++;
                            $useIDS[] = $v->id;
                            $demandunrealizedIdArr[$i]['demandletwoMonth'][] = $v->id;
                            $demandunrealizedDetailArr[$i]['demandletwoMonth'][$v->id] = $v;

                        }
                        //2-6个月未实现需求数
                        $demandlesixMonthEndTime = $this->getOverDate($publishedTime,6);
                        $demandlesixMonthEndTime .= substr($publishedTime, 10);
                        if($demandletwoMonthEndTime < $end && $demandlesixMonthEndTime >= $end) {
                            $demandlesixMonth++;
                            $useIDS[] = $v->id;
                            $demandunrealizedIdArr[$i]['demandlesixMonth'][] = $v->id;
                            $demandunrealizedDetailArr[$i]['demandlesixMonth'][$v->id] = $v;

                        }
                        //6-12年未实现需求数
                        $demandletwelveMonthEndTime = $this->getOverDate($publishedTime,12);
                        $demandletwelveMonthEndTime .= substr($publishedTime, 10);
                        if($demandlesixMonthEndTime < $end && $demandletwelveMonthEndTime >= $end) {
                            $demandletwelveMonth++;

                            $useIDS[] = $v->id;
                            $demandunrealizedIdArr[$i]['demandletwelveMonth'][] = $v->id;
                            $demandunrealizedDetailArr[$i]['demandletwelveMonth'][$v->id] = $v;
                        }
                        if($demandletwelveMonthEndTime < $end) {
                            $demandgttwelveMonth++;
                            $useIDS[] = $v->id;
                            $demandunrealizedIdArr[$i]['demandgttwelveMonth'][] = $v->id;
                            $demandunrealizedDetailArr[$i]['demandgttwelveMonth'][$v->id] = $v;

                        }

                    }

                }

            }

            if($i == 0) continue;
            $jsonUnrealizedData[$i]['deptID'] = $i;
            $jsonUnrealizedData[$i]['demandletwoMonth'] = $demandletwoMonth;
            $jsonUnrealizedData[$i]['demandlesixMonth'] = $demandlesixMonth;
            $jsonUnrealizedData[$i]['demandletwelveMonth']  = $demandletwelveMonth;
            $jsonUnrealizedData[$i]['demandgttwelveMonth']  = $demandgttwelveMonth;

        }
        //补齐部门数据
        if(!$deptID) {
            foreach ($needShowDeptList as $showDept) {
                if (!isset($jsonUnrealizedData[$showDept])) {
                    $jsonUnrealizedData[$showDept] = [
                        'deptID'              => $showDept,
                        'demandletwoMonth'    => 0,
                        'demandlesixMonth'    => 0,
                        'demandletwelveMonth' => 0,
                        'demandgttwelveMonth' => 0,
                    ];
                }
            }
        }

        //剔除 不是统计部门中部门数据为 0 的数据
        foreach ($jsonUnrealizedData as $dept=>$dataArr){
            $total = $dataArr['demandletwoMonth'] + $dataArr['demandlesixMonth'] + $dataArr['demandletwelveMonth'] + $dataArr['demandgttwelveMonth'];
            if(!$total && !in_array($dept,$needShowDeptList)){
                unset($jsonUnrealizedData[$dept]);
                continue;
            }
            $deptIDArr[] = $dept;
        }
        //反馈用到的数据，给接下来生成快照的方法使用。
        return ['useids'=>array_unique($useIDS),'deptcolumids'=>$demandunrealizedIdArr,'staticdata'=>$jsonUnrealizedData,'detail'=>$demandunrealizedDetailArr,'deptids'=>$deptIDArr];
    }
    public function demandunrealizedSave($demandunrealizedIdArr,$jsonUnrealizedData,$formType,$time,$timeFrame){
        $unrealizedId = $this->demandmonthreportadd($timeFrame, $time,$formType);
        //将 下钻查看用到的ID 更新到 whole_report表中

        $this->dao->update(TABLE_WHOLE_REPORT)->data(['useIDArr'=>json_encode($demandunrealizedIdArr)])->where('id')->eq($unrealizedId)->exec();

        foreach ($jsonUnrealizedData as $deptId => $jsonUnrealized) {
            $arr   = [
                'deptID'      => $deptId,
                'tableType'   => $formType,
                'wholeID'     => $unrealizedId,
                'detail'      => json_encode($jsonUnrealized),
                'createdDate' => date('Y-m-d H:i:s'),
            ];
            $this->dao->insert(TABLE_DETAIL_REPORT)->data($arr)->exec();
        }
    }
    public function demandrealizedMonthStatic($realizedInfo,$deptID=0){


        $deptArr            = array();
        $jsonRealizedData   = array();
        $useIDS = [];

        $demand_realizedDetailArr = [];
        $demand_realizedIdArr = [];
        $deptIDArr = [];
        $this->loadModel('demand');
        $now = helper::now();

        $needShowDeptList = $this->getNeedShowDept();
        $deptParent = $this->loadModel('dept')->getDeptAndChild();
        foreach ($realizedInfo as $realizedValue)
        {
            if(!$realizedValue->acceptDept){
                $realizedValue->acceptDept = -1;
            }

            $deptArr[$deptParent[$realizedValue->acceptDept]][] = $realizedValue;

        }

        foreach ($deptArr as $i => $item)
        {
            $realizedNum = 0;
            $twoMonthNum = 0;
            $amount      = 0;
            $overdueRate = 0;
            $totalDemand       = 0;
//                $total       = count($item);

            foreach ($item as $v)
            {
                $solvedTime = $v->solvedTime;
                $publishedTime = $v->publishedTime;
                if($v->fixType == 'second')
                {
                    //条目总数 只统计二线
                    $totalDemand++;
                    $demand_realizedIdArr[$i]['totalDemand'][] = $v->id;
                    $demand_realizedDetailArr[$i]['totalDemand'][$v->id] = $v;

                }

                if(!empty($publishedTime) && $publishedTime != '0000-00-00' && $publishedTime != '0000-00-00 00:00:00')
                {
                    //$his = date("H:i:s",strtotime($publishedTime));
                    //2个月未实现需求数
                    //$twoMonthsEndTime = $this->getOverDate($publishedTime,2).' '.$his;

                    /*
                     * ①需求条目的“实现方式”为二线实现
                     * ②所属需求任务的“实现方式”仅为二线实现
                     * ③且“流程状态”上线成功、已关闭、已交付
                     * ④剔除“是否纳入交付超期”为“否”的
                     * ⑤除去延期审批通过的需求单
                     */
                    //2024-05-17 去掉  && $v->isExtended != 1
                    //2024-06-04 去掉  && $v->delayStatus != 'success'
                    if($v->fixType == 'second' && $v->actualMethod == 'second' && in_array($v->status,$this->lang->demand->realizedArr))
                    {
                        //已实现但超过2个月
                        //取需求条目是否交付超期字段
                        //if($twoMonthsEndTime <= $solvedTime){
                        if($v->deliveryOver == 2){
                            $realizedNum++;
                            $useIDS[] = $v->id;
                            $demand_realizedIdArr[$i]['realizedNum'][] = $v->id;
                            $demand_realizedDetailArr[$i]['realizedNum'][$v->id] = $v;
                            $demand_realizedIdArr[$i]['amount'][] = $v->id;
                            $demand_realizedDetailArr[$i]['amount'][$v->id] = $v;
                        }
                    }
                    //2024-05-17 去掉  && $v->isExtended != 1
                    //2024-06-04 去掉  && $v->delayStatus != 'success'
                    if($v->fixType == 'second' && $v->actualMethod == 'second' && in_array($v->status,$this->lang->demand->unrealizedArr))
                    {
                        //2个月未实现需求数
                        //取需求条目是否交付超期字段
                        //if($twoMonthsEndTime <= $now) {
                            if($v->deliveryOver == 2){
                            $twoMonthNum++;
                            $useIDS[] = $v->id;
                            $demand_realizedIdArr[$i]['twoMonthNum'][] = $v->id;
                            $demand_realizedDetailArr[$i]['twoMonthNum'][$v->id] = $v;
                            $demand_realizedIdArr[$i]['amount'][] = $v->id;
                            $demand_realizedDetailArr[$i]['amount'][$v->id] = $v;
                        }
                    }
                    $amount = $realizedNum + $twoMonthNum;//条目总数
                    if(!empty($totalDemand))
                    {
                        $overdueRate = $amount/$totalDemand* 100;//超期率 合计/条目总数
                    }else{
                        $overdueRate = "0.00";
                    }
                }

            }
            if($i == 0) continue;
            $jsonRealizedData[$i]['deptID'] = $i;
            $jsonRealizedData[$i]['realizedNum'] = $realizedNum;
            $jsonRealizedData[$i]['twoMonthNum'] = $twoMonthNum;
            $jsonRealizedData[$i]['amount']      = $amount;
            $jsonRealizedData[$i]['totalDemand']       = $totalDemand;
            $jsonRealizedData[$i]['overdueRate'] = number_format($overdueRate,2);

        }

        //补齐部门数据
        if(!$deptID){
            foreach ($needShowDeptList as $showDept){
                if(!isset($jsonRealizedData[$showDept])){

                    $jsonRealizedData[$showDept] = [
                        'deptID'=>$showDept,
                        'realizedNum'=>0,
                        'twoMonthNum'=>0,
                        'amount'=>0,
                        'totalDemand'=>0,
                        'overdueRate'=>"0.00"
                    ];
                }
            }
        }

        //剔除 不是统计部门中部门数据为 0 的数据
        foreach ($jsonRealizedData as $dept=>$dataArr){
            if(!$dataArr['totalDemand'] && !in_array($dept,$needShowDeptList)){
                unset($jsonRealizedData[$dept]);
                continue;
            }
            $deptIDArr[] = $dept;
        }
        //反馈用到的数据，给接下来生成快照的方法使用。
        return ['useids'=>array_unique($useIDS),'deptcolumids'=>$demand_realizedIdArr,'staticdata'=>$jsonRealizedData,'detail'=>$demand_realizedDetailArr,'deptids'=>$deptIDArr];


    }

    public function demandrealizedMonthSave($demand_realizedIdArr,$jsonRealizedData,$formType,$time,$timeFrame){
        $demandrealizedId = $this->demandmonthreportadd($timeFrame, $time,$formType);
        //将 下钻查看用到的ID 更新到 whole_report表中

        $this->dao->update(TABLE_WHOLE_REPORT)->data(['useIDArr'=>json_encode($demand_realizedIdArr)])->where('id')->eq($demandrealizedId)->exec();

        foreach ($jsonRealizedData as $deptId => $jsonUnrealized) {
            $arr   = [
                'deptID'      => $deptId,
                'tableType'   => $formType,
                'wholeID'     => $demandrealizedId,
                'detail'      => json_encode($jsonUnrealized),
                'createdDate' => date('Y-m-d H:i:s'),
            ];
            $this->dao->insert(TABLE_DETAIL_REPORT)->data($arr)->exec();
        }
    }

    public function requirementInsideStatic($requirementInsideDataList,$deptID=0)
    {

        $deptArr     = array();
        $jsonRealizedData   = array();
        $useIDS = [];

        $requirement_insideDetailArr = [];
        $requirement_insideIdArr = [];
        $deptIDArr = [];



        $deptParent = $this->loadModel('dept')->getDeptAndChild();

        $needShowDeptList = $this->getNeedShowDept();

        foreach ($requirementInsideDataList as $realizedValue)
        {
            if(!$realizedValue->dept){
                $realizedValue->dept = -1;
            }
            $deptArr[$deptParent[$realizedValue->dept]][] = $realizedValue;
        }
        //因不需要补齐部门，此判断可保留
        if(!empty($deptArr))
        {
            foreach ($deptArr as $i => $item)
            {
                $foverdueNum  = 0;//反馈超期数
                if(empty($i)) continue;
                $backTotal = count($item);//反馈单总数

                foreach ($item as $v)
                {


                    if($v->feekBackStartTime == '0000-00-00 00:00:00') $v->feekBackStartTime = '';
                    if($v->deptPassTime == '0000-00-00 00:00:00') $v->deptPassTime = '';
                    //反馈单总数
                    $requirement_insideIdArr[$i]['backTotal'][] = $v->id;
                    $requirement_insideDetailArr[$i]['backTotal'][$v->id] = $v;
                    //内部反馈超期数 && $v->feedbackOver != 1 2024-05-24去掉，注释中保留 预防业务方撤回修改
                    if($v->ifOverDate == 2 )
                    {
                        $foverdueNum++;
                        $useIDS[] = $v->id;
                        $requirement_insideIdArr[$i]['foverdueNum'][] = $v->id;
                        $requirement_insideDetailArr[$i]['foverdueNum'][$v->id] = $v;
                    }

                }

                if(!empty($backTotal))
                {
                    $backExceedRate = $foverdueNum/$backTotal* 100;//超期率 合计/条目总数
                }else{
                    $backExceedRate = 0;
                }
                $jsonRealizedData[$i]['deptID']          = $i;
                $jsonRealizedData[$i]['backTotal']           = $backTotal;
                $jsonRealizedData[$i]['foverdueNum']           = $foverdueNum;
                $jsonRealizedData[$i]['backExceedRate']     = number_format($backExceedRate,2);


            }
        }

        //补齐部门数据
        if(!$deptID) {
            foreach ($needShowDeptList as $showDept) {
                if (!isset($jsonRealizedData[$showDept])) {

                    $jsonRealizedData[$showDept] = [
                        'deptID'      => $showDept,
                        'backTotal'       => 0,
                        'foverdueNum' => 0,
                        'backExceedRate' => "0.00"
                    ];
                }
            }
        }

        //剔除 不是统计部门中部门数据为 0 的数据
        foreach ($jsonRealizedData as $dept=>$dataArr){
            if(!$dataArr['backTotal'] && !in_array($dept,$needShowDeptList)){
                unset($jsonRealizedData[$dept]);
                continue;
            }
            $deptIDArr[] = $dept;
        }
        //反馈用到的数据，给接下来生成快照的方法使用。
        return ['useids'=>array_unique($useIDS),'deptcolumids'=>$requirement_insideIdArr,'staticdata'=>$jsonRealizedData,'detail'=>$requirement_insideDetailArr,'deptids'=>$deptIDArr];

    }
    public function requirementInsideSave($requirement_insideIdArr,$jsonRealizedData,$formType,$time,$timeFrame){
        $requirement_insideId = $this->demandmonthreportadd($timeFrame, $time,$formType);
        //将 下钻查看用到的ID 更新到 whole_report表中

        $this->dao->update(TABLE_WHOLE_REPORT)->data(['useIDArr'=>json_encode($requirement_insideIdArr)])->where('id')->eq($requirement_insideId)->exec();

        foreach ($jsonRealizedData as $deptId => $jsonUnrealized) {
            $arr   = [
                'deptID'      => $deptId,
                'tableType'   => $formType,
                'wholeID'     => $requirement_insideId,
                'detail'      => json_encode($jsonUnrealized),
                'createdDate' => date('Y-m-d H:i:s'),
            ];
            $this->dao->insert(TABLE_DETAIL_REPORT)->data($arr)->exec();
        }

        return $requirement_insideId;
    }

    public function requirementOutsideStatic($requirementOutsideDataList,$deptID=0)
    {
        $realizedData= array();
        $deptArr     = array();
        $jsonRealizedData   = array();
        $useIDS = [];

        $requirement_outsideDetailArr = [];
        $requirement_outsideIdArr = [];
        $deptIDArr = [];

        $deptParent = $this->loadModel('dept')->getDeptAndChild();

        $needShowDeptList = $this->getNeedShowDept();

        foreach ($requirementOutsideDataList as $realizedValue)
        {
            if(!$realizedValue->dept){
                $realizedValue->dept = -1;
            }

            $deptArr[$deptParent[$realizedValue->dept]][] = $realizedValue;

        }

        //因不需要补齐部门，此判断可保留
        if(!empty($deptArr))
        {
            foreach ($deptArr as $i => $item)
            {
                $foverdueNum  = 0;
                if(empty($i)) continue;
                $backTotal = count($item);//反馈单总数
                foreach ($item as $v)
                {
                    if($v->feekBackStartTimeOutside == '0000-00-00 00:00:00') $v->feekBackStartTimeOutside = '';
                    if($v->innovationPassTime == '0000-00-00 00:00:00') $v->innovationPassTime = '';
                    //反馈单总数
                    $requirement_outsideIdArr[$i]['backTotal'][] = $v->id;
                    $requirement_outsideDetailArr[$i]['backTotal'][$v->id] = $v;
                    //内部反馈超期数  && $v->feedbackOver != 1(外部反馈超期不剔除【是否纳入反馈超期】为否的数据)
                    if($v->ifOverTimeOutSide == 2)
                    {
                        $foverdueNum++;

                        $useIDS[] = $v->id;
                        //反馈单外部反馈超期单数
                        $requirement_outsideIdArr[$i]['foverdueNum'][] = $v->id;
                        $requirement_outsideDetailArr[$i]['foverdueNum'][$v->id] = $v;
                    }
                }

                if(!empty($backTotal))
                {
                    $backExceedRate = $foverdueNum/$backTotal* 100;//超期率 合计/条目总数
                }else{
                    $backExceedRate = 0;
                }
                $jsonRealizedData[$i]['deptID']          = $i;
                $jsonRealizedData[$i]['backTotal']           = $backTotal;
                $jsonRealizedData[$i]['foverdueNum']           = $foverdueNum;
                $jsonRealizedData[$i]['backExceedRate']     = number_format($backExceedRate,2);
            }
        }
        //具体单子数据的统计表不需要补齐部门。
        //补齐部门数据
        if(!$deptID) {
            foreach ($needShowDeptList as $showDept) {
                if (!isset($jsonRealizedData[$showDept])) {

                    $jsonRealizedData[$showDept] = [
                        'deptID'         => $showDept,
                        'backTotal'      => 0,
                        'foverdueNum'    => 0,
                        'backExceedRate' => "0.00"
                    ];
                }
            }
        }

        //剔除 不是统计部门中部门数据为 0 的数据
        foreach ($jsonRealizedData as $dept=>$dataArr){
            if(!$dataArr['backTotal'] && !in_array($dept,$needShowDeptList)){
                unset($jsonRealizedData[$dept]);
                continue;
            }
            $deptIDArr[] = $dept;
        }
        //反馈用到的数据，给接下来生成快照的方法使用。
        return ['useids'=>array_unique($useIDS),'deptcolumids'=>$requirement_outsideIdArr,'staticdata'=>$jsonRealizedData,'detail'=>$requirement_outsideDetailArr,'deptids'=>$deptIDArr];

    }
    public function requirementOutsideSave($requirement_outsideIdArr,$jsonRealizedData,$formType,$time,$timeFrame){
        $requirement_outside = $this->demandmonthreportadd($timeFrame, $time,$formType);
        //将 下钻查看用到的ID 更新到 whole_report表中

        $this->dao->update(TABLE_WHOLE_REPORT)->data(['useIDArr'=>json_encode($requirement_outsideIdArr)])->where('id')->eq($requirement_outside)->exec();

        foreach ($jsonRealizedData as $deptId => $jsonUnrealized) {
            $arr   = [
                'deptID'      => $deptId,
                'tableType'   => $formType,
                'wholeID'     => $requirement_outside,
                'detail'      => json_encode($jsonUnrealized),
                'createdDate' => date('Y-m-d H:i:s'),
            ];
            $this->dao->insert(TABLE_DETAIL_REPORT)->data($arr)->exec();
        }
    }
    public function problemproblemOverallStatic($problemData,$deptID=0){
        $alreadySolve = ['delivery', 'onlinesuccess', 'closed', 'toclose'];
        $waitSolve    = ['assigned', 'feedbacked', 'released', 'build', 'exception'];
        $deptParent = $this->loadModel('dept')->getDeptAndChild();
        //通用部门
//        $needStaticDeptList = $this->getNeedStaticDept();
        $needShowDeptList = $this->getNeedShowDept();

        $overalls       = []; //整体统计
        $overallsIDS       = []; //整体统计



//        二线月报下钻查看  ID 变量 start
        $problemOverall = $this->lang->secondmonthreport->problemUsefield['problemOverall'];

        $problemOverallDetailArr = [];
        $problemOverallIdArr = [];
        $deptIDArr = [];

//        二线月报下钻查看  ID 变量 end
        $overallsDefault = [
            'unaccepted' => 0,
            'waitAllocation' => 0,
            'waitSolve'      => 0,
            'alreadySolve'   => 0,
            'total'          => 0,
            'solveRate'      => "0.00",
        ];

        foreach ($problemData as $item) {
            if (!$item->acceptDept) {
                $item->acceptDept = -1;
            }

            if (!isset($overalls[$deptParent[$item->acceptDept]])) {
                //问题单整体情况统计初始化

                $overalls[$deptParent[$item->acceptDept]] = $overallsDefault;

            }
            if ('suspend' != $item->status) {

                ++$overalls[$deptParent[$item->acceptDept]]['total'];
                $overallsIDS[] = $item->id;
                $problemOverallIdArr[$deptParent[$item->acceptDept]]['total'][] = $item->id;
                $problemOverallDetailArr[$deptParent[$item->acceptDept]]['total'][$item->id] = $item;
            }
            //状态为未受理
            if ('returned' == $item->status) {
                ++$overalls[$deptParent[$item->acceptDept]]['unaccepted'];
                $problemOverallIdArr[$deptParent[$item->acceptDept]]['unaccepted'][] = $item->id;
                $problemOverallDetailArr[$deptParent[$item->acceptDept]]['unaccepted'][$item->id] = $item;
            }
            //状态为待分配
            if ('confirmed' == $item->status) {
                ++$overalls[$deptParent[$item->acceptDept]]['waitAllocation'];
                $problemOverallIdArr[$deptParent[$item->acceptDept]]['waitAllocation'][] = $item->id;
                $problemOverallDetailArr[$deptParent[$item->acceptDept]]['waitAllocation'][$item->id] = $item;
            }
            //状态为待解决
            if (in_array($item->status, $waitSolve)) {

                ++$overalls[$deptParent[$item->acceptDept]]['waitSolve'];
                $problemOverallIdArr[$deptParent[$item->acceptDept]]['waitSolve'][] = $item->id;
                $problemOverallDetailArr[$deptParent[$item->acceptDept]]['waitSolve'][$item->id] = $item;

            }
            //状态为已解决
            if (in_array($item->status, $alreadySolve)) {

                ++$overalls[$deptParent[$item->acceptDept]]['alreadySolve'];
                $problemOverallIdArr[$deptParent[$item->acceptDept]]['alreadySolve'][] = $item->id;
                $problemOverallDetailArr[$deptParent[$item->acceptDept]]['alreadySolve'][$item->id] = $item;
            }
        }
        //补齐数据
        //整体统计
        if(!$deptID){
            foreach ($needShowDeptList as $alldept){
                if(!isset($overalls[$alldept])){
                    //部门为空 且 无数据时 不需要候补
                    $overalls[$alldept] = $overallsDefault;
                }
            }
        }


        //整体统计表剔除 不是统计部门中部门数据为 0 的数据
        foreach ($overalls as $dept=>$dataArr){
            if(!$dataArr['total'] && !in_array($dept,$needShowDeptList)){
                unset($overalls[$dept]);
                continue;
            }
            $overalls[$dept]['solveRate'] = $dataArr['total'] > 0 ? number_format(($dataArr['alreadySolve'] / $dataArr['total']) * 100, 2) : "0.00";
            $deptIDArr[] = $dept;
        }

        //反馈用到的数据，给接下来生成快照的方法使用。
        return ['useids'=>array_unique($overallsIDS),'deptcolumids'=>$problemOverallIdArr,'staticdata'=>$overalls,'detail'=>$problemOverallDetailArr,'deptids'=>$deptIDArr];
    }

    public function problemproblemOverallSave($problemOverallIdArr,$overalls,$formType,$time,$timeFrame){
        //内外部反馈超期 是具体数据 不需要补齐部门

        $overallId = $this->addWholeReport($timeFrame, $time,$formType);
        //将 下钻查看用到的ID 更新到 whole_report表中
        $this->dao->update(TABLE_WHOLE_REPORT)->data(['useIDArr'=>json_encode($problemOverallIdArr)])->where('id')->eq($overallId)->exec();

        foreach ($overalls as $deptId => $overall) {
            $overall['solveRate'] = $overall['total'] > 0 ? number_format(($overall['alreadySolve'] / $overall['total']) * 100, 2) : "0.00";
            $arr       = [
                'deptID'      => $deptId,

                'tableType'   => $formType,
                'wholeID'     => $overallId,
                'detail'      => json_encode($overall),
                'createdDate' => date('Y-m-d H:i:s'),
            ];
            $this->dao->insert(TABLE_DETAIL_REPORT)->data($arr)->exec();
        }

        return $overallId;
    }

    public function problemproblemWaitSolveStatic($problemData,$deptID=0){

        $waitSolve    = ['assigned', 'feedbacked', 'released', 'build', 'exception'];
        $now          = helper::now();
        $deptParent = $this->loadModel('dept')->getDeptAndChild();
        //通用部门
//        $needStaticDeptList = $this->getNeedStaticDept();
        $needShowDeptList = $this->getNeedShowDept();

        $waitSolves     = []; //两个月未解决问题统计

        $waitSolvesIDS     = []; //两个月未解决问题统计

//        二线月报下钻查看  ID 变量 start

        $problemWaitSolve = $this->lang->secondmonthreport->problemUsefield['problemWaitSolve'];


        $problemWaitSolveDetailArr = [];
        $problemWaitSolveIdArr = [];
        $deptIDArr = [];
//        二线月报下钻查看  ID 变量 end


        $waitSolvesDefault = [
            'twoMonth' => 0,
            'sixMonth' => 0,
            'twelveMonth' => 0,
        ];

        foreach ($problemData as $item) {
            if (!$item->acceptDept) {
                $item->acceptDept = -1;
            }
            //状态为待解决
            if (in_array($item->status, $waitSolve)) {

                $exceedTime = $this->getOverDate($item->dealAssigned, 2);
                $exceedTime .= substr($item->dealAssigned, 10);
                if (false === strpos($item->dealAssigned, '0000') && $exceedTime < $now) {

                    //&& in_array($item->acceptDept,$needStaticDeptList)
                    if (!isset($waitSolves[$deptParent[$item->acceptDept]]) ) {
                        $waitSolves[$deptParent[$item->acceptDept]] = $waitSolvesDefault;
                    }
//                    if(in_array($item->acceptDept,$needStaticDeptList)){
                    ++$waitSolves[$deptParent[$item->acceptDept]]['twoMonth'];
                    $waitSolvesIDS[] = $item->id;
                    $problemWaitSolveIdArr[$deptParent[$item->acceptDept]]['twoMonth'][] = $item->id;
                    $problemWaitSolveDetailArr[$deptParent[$item->acceptDept]]['twoMonth'][$item->id] = $item;

//                    }



                }
                $exceedTime = $this->getOverDate($item->dealAssigned, 6);
                $exceedTime .= substr($item->dealAssigned, 10);
                if (false === strpos($item->dealAssigned, '0000') && $exceedTime < $now) {
//                    if(in_array($item->acceptDept,$needStaticDeptList)){
                    ++$waitSolves[$deptParent[$item->acceptDept]]['sixMonth'];
                    $problemWaitSolveIdArr[$deptParent[$item->acceptDept]]['sixMonth'][] = $item->id;
                    $problemWaitSolveDetailArr[$deptParent[$item->acceptDept]]['sixMonth'][$item->id] = $item;

//                    }

                }

                $exceedTime = $this->getOverDate($item->dealAssigned, 12);
                $exceedTime .= substr($item->dealAssigned, 10);
                if (false === strpos($item->dealAssigned, '0000') && $exceedTime < $now) {

                    ++$waitSolves[$deptParent[$item->acceptDept]]['twelveMonth'];
                    $problemWaitSolveIdArr[$deptParent[$item->acceptDept]]['twelveMonth'][] = $item->id;
                    $problemWaitSolveDetailArr[$deptParent[$item->acceptDept]]['twelveMonth'][$item->id] = $item;

                }
            }

        }

        //补齐数据
        //整体统计

        //两个月未解决问题统计表
        if(!$deptID) {
            foreach ($needShowDeptList as $showDept){
                if(!isset($waitSolves[$showDept])){

                    $waitSolves[$showDept] = $waitSolvesDefault;
                }
            }
        }
        //两个月未解决问题统计表 剔除 不是统计部门中部门数据为 0 的数据

        foreach ($waitSolves as $dept => $dataArr) {
            if (!$dataArr['twoMonth'] && !in_array($dept, $needShowDeptList)) {
                unset($waitSolves[$dept]);
                continue;
            }
            $deptIDArr[] = $dept;
        }


        //反馈用到的数据，给接下来生成快照的方法使用。
        return ['useids'=>array_unique($waitSolvesIDS),'deptcolumids'=>$problemWaitSolveIdArr,'staticdata'=>$waitSolves,'detail'=>$problemWaitSolveDetailArr,'deptids'=>$deptIDArr];

    }

    public function problemproblemUnresolvedStatic($problemData,$deptID=0){

        $waitSolve    = ['assigned', 'feedbacked', 'released', 'build', 'exception'];
        $now          = helper::now();
        $deptParent = $this->loadModel('dept')->getDeptAndChild();
        //通用部门
//        $needStaticDeptList = $this->getNeedStaticDept();
        $needShowDeptList = $this->getNeedShowDept();

        $unresolveds     = []; //未解决问题统计表

        $unresolvedIDS     = []; //未解决问题统计表

//        二线月报下钻查看  ID 变量 start

        $problemUnresolvedDetailArr = [];
        $problemUnresolvedIdArr = [];
        $deptIDArr = [];
//        二线月报下钻查看  ID 变量 end


        $unresolvedDefault = [
            'letwoMonth' => 0,//两个月内
            'lesixMonth' => 0,//大于两个月小于等于6个月
            'letwelveMonth' => 0,//大于6个月小于等于12个月
            'gttwelveMonth' => 0,//大于12个月
        ];

        foreach ($problemData as $item) {
            if('noproblem' == $item->type){
                continue;
            }
            if (!$item->acceptDept) {
                $item->acceptDept = -1;
            }
            //状态为待解决
            if (in_array($item->status, $waitSolve)) {

                $letwoMonthTime = $this->getOverDate($item->dealAssigned, 2);
//                $letwoMonthTime .= substr($item->dealAssigned, 10);
                // 比较精确到  年月日  ，日期是当前日期
                // 时间 在两个月 内
                if (!isset($unresolveds[$deptParent[$item->acceptDept]]) ) {
                    $unresolveds[$deptParent[$item->acceptDept]] = $unresolvedDefault;
                }
                if (false === strpos($item->dealAssigned, '0000') && $letwoMonthTime >= $now) {
                    //&& in_array($item->acceptDept,$needStaticDeptList)


                    ++$unresolveds[$deptParent[$item->acceptDept]]['letwoMonth'];
                    $unresolvedIDS[] = $item->id;
                    $problemUnresolvedIdArr[$deptParent[$item->acceptDept]]['letwoMonth'][] = $item->id;
                    $problemUnresolvedDetailArr[$deptParent[$item->acceptDept]]['letwoMonth'][$item->id] = $item;

                }
                $lesixMonthTime = $this->getOverDate($item->dealAssigned, 6);
                $lesixMonthTime .= substr($item->dealAssigned, 10);
                if (false === strpos($item->dealAssigned, '0000') && $letwoMonthTime < $now && $lesixMonthTime >= $now ) {

                    ++$unresolveds[$deptParent[$item->acceptDept]]['lesixMonth'];
                    $unresolvedIDS[] = $item->id;
                    $problemUnresolvedIdArr[$deptParent[$item->acceptDept]]['lesixMonth'][] = $item->id;
                    $problemUnresolvedDetailArr[$deptParent[$item->acceptDept]]['lesixMonth'][$item->id] = $item;

                }

                $letwelveMonthTime = $this->getOverDate($item->dealAssigned, 12);
                $letwelveMonthTime .= substr($item->dealAssigned, 10);
                if (false === strpos($item->dealAssigned, '0000') && $lesixMonthTime < $now && $letwelveMonthTime >= $now) {
                    ++$unresolveds[$deptParent[$item->acceptDept]]['letwelveMonth'];
                    $unresolvedIDS[] = $item->id;
                    $problemUnresolvedIdArr[$deptParent[$item->acceptDept]]['letwelveMonth'][] = $item->id;
                    $problemUnresolvedDetailArr[$deptParent[$item->acceptDept]]['letwelveMonth'][$item->id] = $item;
                }


//                $exceedTime .= substr($item->dealAssigned, 10);
                if (false === strpos($item->dealAssigned, '0000') && $letwelveMonthTime < $now) {
                    ++$unresolveds[$deptParent[$item->acceptDept]]['gttwelveMonth'];
                    $unresolvedIDS[] = $item->id;
                    $problemUnresolvedIdArr[$deptParent[$item->acceptDept]]['gttwelveMonth'][] = $item->id;
                    $problemUnresolvedDetailArr[$deptParent[$item->acceptDept]]['gttwelveMonth'][$item->id] = $item;
                }
            }

        }

        //补齐数据

        //未解决问题统计表
        if(!$deptID) {
            foreach ($needShowDeptList as $showDept){
                if(!isset($unresolveds[$showDept])){

                    $unresolveds[$showDept] = $unresolvedDefault;
                }
            }
        }
        //两个月未解决问题统计表 剔除 不是统计部门中部门数据为 0 的数据

        foreach ($unresolveds as $dept => $dataArr) {
            $total = $dataArr['letwoMonth'] + $dataArr['lesixMonth'] + $dataArr['letwelveMonth'] + $dataArr['gttwelveMonth'];
            if (!$total && !in_array($dept, $needShowDeptList)) {
                unset($unresolveds[$dept]);
                continue;
            }
            $deptIDArr[] = $dept;
        }


        //反馈用到的数据，给接下来生成快照的方法使用。
        return ['useids'=>array_unique($unresolvedIDS),'deptcolumids'=>$problemUnresolvedIdArr,'staticdata'=>$unresolveds,'detail'=>$problemUnresolvedDetailArr,'deptids'=>$deptIDArr];

    }
    public function problemproblemUnresolvedSave($problemUnresolvedIdArr,$unresolveds,$formType,$time,$timeFrame){
        $exceedId = $this->addWholeReport($timeFrame, $time,$formType);
        //将 下钻查看用到的ID 更新到 whole_report表中

        $this->dao->update(TABLE_WHOLE_REPORT)->data(['useIDArr'=>json_encode($problemUnresolvedIdArr)])->where('id')->eq($exceedId)->exec();


        foreach ($unresolveds as $deptId => $unresolved) {


            $arr                  = [
                'deptID'      => $deptId,
                'tableType'   => $formType,
                'wholeID'     => $exceedId,
                'detail'      => json_encode($unresolved),
                'createdDate' => date('Y-m-d H:i:s'),
            ];
            $this->dao->insert(TABLE_DETAIL_REPORT)->data($arr)->exec();
        }
    }
    public function problemproblemExceedStatic($problemData,$deptID=0)
    {
        set_time_limit(0);


        $alreadySolve = ['delivery', 'onlinesuccess', 'closed', 'toclose'];
        $waitSolve    = ['assigned', 'feedbacked', 'released', 'build', 'exception'];

        $now          = helper::now();
        $deptParent = $this->loadModel('dept')->getDeptAndChild();

        //通用部门
//        $needStaticDeptList = $this->getNeedStaticDept();
        $needShowDeptList = $this->getNeedShowDept();

        $problemModel = $this->loadModel('problem');

        $exceeds        = []; //问题解决超期统计
        $exceedsIDS        = []; //问题解决超期统计


//        二线月报下钻查看  ID 变量 start

        $problemExceed = $this->lang->secondmonthreport->problemUsefield['problemExceed'];

        $problemExceedIdArr = [];
        $problemExceedDetailArr = [];
        $deptIDArr = [];

//        二线月报下钻查看  ID 变量 end

        $exceedsDefault = [
            'alreadySolve' => 0,
            'waitSolve'    => 0,
            'sum'          => 0,
            'total'        => 0,
            'exceedRate'   => "0.00",
        ];

        foreach ($problemData as $item) {
            if('noproblem' == $item->type){
                continue;
            }
            if (!$item->acceptDept) {
                $item->acceptDept = -1;
            }

//            if(in_array($item->acceptDept,$monthReportPandMStaticDept)){
            if (!isset($exceeds[$deptParent[$item->acceptDept]])) {
                //问题解决超期统计初始化
                $exceeds[$deptParent[$item->acceptDept]] = $exceedsDefault;
            }
//            if(32 != $item->source){
                ++$exceeds[$deptParent[$item->acceptDept]]['total'];
                $problemExceedIdArr[$deptParent[$item->acceptDept]]['total'][] = $item->id;
                $problemExceedDetailArr[$deptParent[$item->acceptDept]]['total'][$item->id] = $item;

//            }
//            }
//
            /*$exceedFlag = true;
            if ('noproblem' != $item->type && 32 != $item->source && 1 != $item->isExtended) {
                $delayInfo = $this->dao
                    ->select('id')
                    ->from(TABLE_DELAY)
                    ->where('objectType')->eq('problem')
                    ->andWhere('objectId')->eq($item->id)
                    ->andWhere('delayStatus')->eq('success')
                    ->fetch();
                if (empty($delayInfo)) {
                    $exceedFlag = false;
                }
            }

            //状态为待分配

            //状态为待解决
            if (in_array($item->status, $waitSolve)) {

                $exceedTime = $this->getOverDate($item->dealAssigned, 2);
                $exceedTime .= substr($item->dealAssigned, 10);
                if (false === strpos($item->dealAssigned, '0000') && $exceedTime < $now) {

                    if (!$exceedFlag) {
                        ++$exceeds[$deptParent[$item->acceptDept]]['waitSolve'];
                        $exceedsIDS[] = $item->id;
                        $problemExceedIdArr[$deptParent[$item->acceptDept]]['waitSolve'][] = $item->id;
                        $problemExceedIdArr[$deptParent[$item->acceptDept]]['sum'][] = $item->id;
                        $problemExceedDetailArr[$deptParent[$item->acceptDept]]['waitSolve'][$item->id] = $item;
                        $problemExceedDetailArr[$deptParent[$item->acceptDept]]['sum'][$item->id] = $item;
                    }
                }
            }
            //状态为已解决
            if (in_array($item->status, $alreadySolve)) {
                $exceedTime = $this->getOverDate($item->dealAssigned, 2);
                $exceedTime .= substr($item->dealAssigned, 10);
                if (false === strpos($item->dealAssigned, '0000') && $exceedTime < $item->solvedTime && !$exceedFlag) {

                    ++$exceeds[$deptParent[$item->acceptDept]]['alreadySolve'];
                    $exceedsIDS[] = $item->id;
                    $problemExceedIdArr[$deptParent[$item->acceptDept]]['alreadySolve'][] = $item->id;
                    $problemExceedIdArr[$deptParent[$item->acceptDept]]['sum'][] = $item->id;
                    $problemExceedDetailArr[$deptParent[$item->acceptDept]]['alreadySolve'][$item->id] = $item;
                    $problemExceedDetailArr[$deptParent[$item->acceptDept]]['sum'][$item->id] = $item;
                }
            }*/


            //状态为待解决
            if (in_array($item->status, $waitSolve)) {

                $exceedTime = $this->getOverDate($item->dealAssigned, 2);
                $exceedTime .= substr($item->dealAssigned, 10);
                if ($item->isExceedByTime == '是') {


                    ++$exceeds[$deptParent[$item->acceptDept]]['waitSolve'];
                    $exceedsIDS[] = $item->id;
                    $problemExceedIdArr[$deptParent[$item->acceptDept]]['waitSolve'][] = $item->id;
                    $problemExceedIdArr[$deptParent[$item->acceptDept]]['sum'][] = $item->id;
                    $problemExceedDetailArr[$deptParent[$item->acceptDept]]['waitSolve'][$item->id] = $item;
                    $problemExceedDetailArr[$deptParent[$item->acceptDept]]['sum'][$item->id] = $item;

                }
            }
            //状态为已解决
            if (in_array($item->status, $alreadySolve)) {
//                $exceedTime = $this->getOverDate($item->dealAssigned, 2);
//                $exceedTime .= substr($item->dealAssigned, 10);
                if ($item->isExceedByTime == '是') {

                    ++$exceeds[$deptParent[$item->acceptDept]]['alreadySolve'];
                    $exceedsIDS[] = $item->id;
                    $problemExceedIdArr[$deptParent[$item->acceptDept]]['alreadySolve'][] = $item->id;
                    $problemExceedIdArr[$deptParent[$item->acceptDept]]['sum'][] = $item->id;
                    $problemExceedDetailArr[$deptParent[$item->acceptDept]]['alreadySolve'][$item->id] = $item;
                    $problemExceedDetailArr[$deptParent[$item->acceptDept]]['sum'][$item->id] = $item;
                }
            }

        }

        //补齐数据

        //问题解决超期统计表
        if(!$deptID) {
            foreach ($needShowDeptList as $showDept) {
                if (!isset($exceeds[$showDept])) {

                    $exceeds[$showDept] = $exceedsDefault;
                }
            }
        }

        //问题解决超期统计表 剔除 不是统计部门中部门数据为 0 的数据
        foreach ($exceeds as $dept=>$dataArr){
            if(!$dataArr['total'] && !in_array($dept,$needShowDeptList)){
                unset($exceeds[$dept]);
                continue;
            }

            $exceeds[$dept]['sum']        = $dataArr['alreadySolve'] + $dataArr['waitSolve'];
            $exceeds[$dept]['exceedRate'] = $dataArr['total'] > 0 ? number_format(($exceeds[$dept]['sum'] / $dataArr['total']) * 100, 2) : "0.00";

            $deptIDArr[] = $dept;
        }


        //反馈用到的数据，给接下来生成快照的方法使用。
        return ['useids'=>array_unique($exceedsIDS),'deptcolumids'=>$problemExceedIdArr,'staticdata'=>$exceeds,'detail'=>$problemExceedDetailArr,'deptids'=>$deptIDArr];



    }
    public function problemproblemExceedSave($problemExceedIdArr,$exceeds,$formType,$time,$timeFrame){
        $exceedId = $this->addWholeReport($timeFrame, $time,$formType);
        //将 下钻查看用到的ID 更新到 whole_report表中

        $this->dao->update(TABLE_WHOLE_REPORT)->data(['useIDArr'=>json_encode($problemExceedIdArr)])->where('id')->eq($exceedId)->exec();


        foreach ($exceeds as $deptId => $exceed) {

            $exceed['sum']        = $exceed['alreadySolve'] + $exceed['waitSolve'];
            $exceed['exceedRate'] = $exceed['total'] > 0 ? number_format($exceed['sum'] / $exceed['total'] * 100, 2) : "0.00";
            $arr                  = [
                'deptID'      => $deptId,
                'tableType'   => $formType,
                'wholeID'     => $exceedId,
                'detail'      => json_encode($exceed),
                'createdDate' => date('Y-m-d H:i:s'),
            ];
            $this->dao->insert(TABLE_DETAIL_REPORT)->data($arr)->exec();
        }
    }
    public function problemproblemExceedBackInStatic($problemData,$deptID=0)
    {
        set_time_limit(0);

        $formType = 'problemExceedBackIn';

        $deptParent = $this->loadModel('dept')->getDeptAndChild();

        //通用部门
//        $needStaticDeptList = $this->getNeedStaticDept();
        $needShowDeptList = $this->getNeedShowDept();

        $problemModel = $this->loadModel('problem');

        $exceedBackIns  = []; //问题解决超期统计
        $exceedBackInsIDS  = []; //内部反馈超期

//        二线月报下钻查看  ID 变量 start

        $problemExceedBackIn = $this->lang->secondmonthreport->problemUsefield['problemExceedBackIn'];


        $problemExceedBackInIdArr = [];
        $problemExceedBackInDetailArr = [];
        $deptIDArr = [];
//        二线月报下钻查看  ID 变量 end

        $exceedBackInsDefault = [
            'backTotal'      => 0,
            'backExceedRate' => "0.00",
            'foverdueNum'    => 0,
        ];

        foreach ($problemData as $item) {
            if (!$item->acceptDept) {
                $item->acceptDept = -1;
            }


            if (!empty($item->IssueId)) {
//                if(in_array($item->acceptDept,$monthReportPandMStaticDept)){
                if (!isset($exceedBackIns[$deptParent[$item->acceptDept]])) {
                    //内部反馈超期统计初始化
                    $exceedBackIns[$deptParent[$item->acceptDept]] = $exceedBackInsDefault;
                }
//                }

//                if(in_array($item->acceptDept,$monthReportPandMStaticDept)){
                ++$exceedBackIns[$deptParent[$item->acceptDept]]['backTotal'];
                $problemExceedBackInIdArr[$deptParent[$item->acceptDept]]['backTotal'][] = $item->id;
                $problemExceedBackInDetailArr[$deptParent[$item->acceptDept]]['backTotal'][$item->id] = $item;

//                }

            }


            $item = $problemModel->getIfOverDate($item);
//           2023-05-24去掉， 注释里先保留 && '1' != $item->isBackExtended
            if (!empty($item->IssueId) && isset($item->ifOverDateInside) && '是' == $item->ifOverDateInside['flag'] ) {

                if(isset($exceedBackIns[$deptParent[$item->acceptDept]]['foverdueNum'])){
                    $exceedBackIns[$deptParent[$item->acceptDept]]['foverdueNum']++;
                }else{
                    $exceedBackIns[$deptParent[$item->acceptDept]]['foverdueNum'] = 1;
                }
                $exceedBackInsIDS[] = $item->id;
                $problemExceedBackInIdArr[$deptParent[$item->acceptDept]]['foverdueNum'][] = $item->id;
                $problemExceedBackInDetailArr[$deptParent[$item->acceptDept]]['foverdueNum'][$item->id] = $item;
//                }
            }

        }

        //内部反馈超期统计表 补齐部门
        //实时表单有部门搜索时 不再补齐部门
        if(!$deptID) {
            foreach ($needShowDeptList as $showDept) {
                if (!isset($exceedBackIns[$showDept])) {
                    $exceedBackIns[$showDept] = $exceedBackInsDefault;
                }
            }
        }

        //内部反馈超期统计表 剔除 不是统计部门中部门数据为 0 的数据
        foreach ($exceedBackIns as $dept=>$dataArr){
            if(!$dataArr['backTotal'] && !in_array($dept,$needShowDeptList)){
                unset($exceedBackIns[$dept]);
                continue;
            }
            $exceedBackIns[$dept]['backExceedRate'] = $dataArr['backTotal'] > 0 ? number_format($dataArr['foverdueNum'] / $dataArr['backTotal'] * 100, 2) : "0.00";
            $deptIDArr[] = $dept;
        }

        return ['useids'=>array_unique($exceedBackInsIDS),'deptcolumids'=>$problemExceedBackInIdArr,'staticdata'=>$exceedBackIns,'detail'=>$problemExceedBackInDetailArr,'deptids'=>$deptIDArr];



    }
    public function problemproblemExceedBackInSave($problemExceedBackInIdArr,$exceedBackIns,$formType,$time,$timeFrame){

        //内外部反馈超期 是具体数据 不需要补齐部门
        $exceedBackInId = $this->addWholeReport($timeFrame, $time,$formType);
        //将 下钻查看用到的ID 更新到 whole_report表中

        $this->dao->update(TABLE_WHOLE_REPORT)->data(['useIDArr'=>json_encode($problemExceedBackInIdArr)])->where('id')->eq($exceedBackInId)->exec();

        foreach ($exceedBackIns as $deptId => $exceedBackIn) {

            $exceedBackIn['backExceedRate'] = $exceedBackIn['backTotal'] > 0 ? number_format($exceedBackIn['foverdueNum'] / $exceedBackIn['backTotal'] * 100, 2) : "0.00";
            $arr = [
                'deptID'      => $deptId,
                'tableType'   => $formType,
                'wholeID'     => $exceedBackInId,
                'detail'      => json_encode($exceedBackIn),
                'createdDate' => date('Y-m-d H:i:s'),
            ];
            $this->dao->insert(TABLE_DETAIL_REPORT)->data($arr)->exec();
        }

        return $exceedBackInId;
    }
    public function problemproblemExceedBackOutStatic($problemData,$deptID=0)
    {
        set_time_limit(0);


        $deptParent = $this->loadModel('dept')->getDeptAndChild();

        //通用部门
//        $needStaticDeptList = $this->getNeedStaticDept();
        $needShowDeptList = $this->getNeedShowDept();


        $problemModel = $this->loadModel('problem');

        $exceedBackOuts = []; //问题解决超期统计
        $exceedBackOutsIDS = []; //外部反馈超期

//        二线月报下钻查看  ID 变量 start

        $problemExceedBackOut = $this->lang->secondmonthreport->problemUsefield['problemExceedBackOut'];

        $problemExceedBackOutIdArr = [];
        $problemExceedBackOutDetailArr = [];
        $deptIDArr = [];
//        二线月报下钻查看  ID 变量 end

        $exceedBackOutsDefault = [
            'backTotal'      => 0,
            'backExceedRate' => "0.00",
            'foverdueNum'    => 0,
        ];
        foreach ($problemData as $item) {
            if (!$item->acceptDept) {
                $item->acceptDept = -1;
            }

            if (!empty($item->IssueId)) {



                if (!isset($exceedBackOuts[$deptParent[$item->acceptDept]]) ) {
                    //外部反馈超期统计初始化
                    $exceedBackOuts[$deptParent[$item->acceptDept]] = $exceedBackOutsDefault;
                }

                ++$exceedBackOuts[$deptParent[$item->acceptDept]]['backTotal'];
                $problemExceedBackOutIdArr[$deptParent[$item->acceptDept]]['backTotal'][] = $item->id;
                $problemExceedBackOutDetailArr[$deptParent[$item->acceptDept]]['backTotal'][$item->id] = $item;

            }

            $item = $problemModel->getIfOverDate($item);


            if (!empty($item->IssueId) && '是' == $item->ifOverDate['flag']) {

                if(isset($exceedBackOuts[$deptParent[$item->acceptDept]]['foverdueNum'])){
                    $exceedBackOuts[$deptParent[$item->acceptDept]]['foverdueNum']++;
                }else{
                    $exceedBackOuts[$deptParent[$item->acceptDept]]['foverdueNum'] = 1;
                }

                $exceedBackOutsIDS[] = $item->id;
                $problemExceedBackOutIdArr[$deptParent[$item->acceptDept]]['foverdueNum'][] = $item->id;
                $problemExceedBackOutDetailArr[$deptParent[$item->acceptDept]]['foverdueNum'][$item->id] = $item;
//                }
            }
        }

        //补齐数据

        //外部反馈超期统计表 补齐部门
        //实时表单有部门搜索时 不再补齐部门
        if(!$deptID){
            foreach ($needShowDeptList as $showDept){
                if(!isset($exceedBackOuts[$showDept])){
                    $exceedBackOuts[$showDept] = $exceedBackOutsDefault;
                }
            }
        }



        //外部反馈超期统计表 问题解决超期统计表 剔除 不是统计部门中部门数据为 0 的数据
        foreach ($exceedBackOuts as $dept=>$dataArr){
            if(!$dataArr['backTotal'] && !in_array($dept,$needShowDeptList)){
                unset($exceedBackOuts[$dept]);
                continue;
            }
            $exceedBackOuts[$dept]['backExceedRate'] = $dataArr['backTotal'] > 0 ? number_format($dataArr['foverdueNum'] / $dataArr['backTotal'] * 100, 2) : "0.00";
            $deptIDArr[] = $dept;
        }
        //反馈用到的数据，给接下来生成快照的方法使用。
        return ['useids'=>array_unique($exceedBackOutsIDS),'deptcolumids'=>$problemExceedBackOutIdArr,'staticdata'=>$exceedBackOuts,'detail'=>$problemExceedBackOutDetailArr,'deptids'=>$deptIDArr];

    }
    public function problemproblemExceedBackOutSave($problemExceedBackOutIdArr,$exceedBackOuts,$formType,$time,$timeFrame){

        //内外部反馈超期 是具体数据 不需要补齐部门
        $exceedBackOutId = $this->addWholeReport($timeFrame, $time,$formType);
        //将 下钻查看用到的ID 更新到 whole_report表中

        $this->dao->update(TABLE_WHOLE_REPORT)->data(['useIDArr'=>json_encode($problemExceedBackOutIdArr)])->where('id')->eq($exceedBackOutId)->exec();

        foreach ($exceedBackOuts as $deptId => $exceedBackOut) {

            $exceedBackOut['backExceedRate'] = $exceedBackOut['backTotal'] > 0 ? number_format($exceedBackOut['foverdueNum'] / $exceedBackOut['backTotal'] * 100, 2) : "0.00";
            $arr                             = [
                'deptID'      => $deptId,
                'tableType'   => $formType,
                'wholeID'     => $exceedBackOutId,
                'detail'      => json_encode($exceedBackOut),
                'createdDate' => date('Y-m-d H:i:s'),
            ];
            $this->dao->insert(TABLE_DETAIL_REPORT)->data($arr)->exec();
        }

    }
    public function problemproblemWaitSolveSave($problemWaitSolveIdArr,$waitSolves,$formType,$time,$timeFrame){
        //内外部反馈超期 是具体数据 不需要补齐部门
        $waitSolveId = $this->addWholeReport($timeFrame, $time,$formType);
        //将 下钻查看用到的ID 更新到 whole_report表中
        $this->dao->update(TABLE_WHOLE_REPORT)->data(['useIDArr'=>json_encode($problemWaitSolveIdArr)])->where('id')->eq($waitSolveId)->exec();
//        krsort($waitSolves);
        foreach ($waitSolves as $deptId => $waitSolve) {
            $arr = [
                'deptID'      => $deptId,
                'tableType'   => $formType,
                'wholeID'     => $waitSolveId,
                'detail'      => json_encode($waitSolve),
                'createdDate' => date('Y-m-d H:i:s'),
            ];
            $this->dao->insert(TABLE_DETAIL_REPORT)->data($arr)->exec();
        }
    }

    public function problemCompletedPlanSave($problemOverallIdArr,$overalls,$formType,$time,$timeFrame){
        //内外部反馈超期 是具体数据 不需要补齐部门

        $overallId = $this->addWholeReport($timeFrame, $time,$formType);
        //将 下钻查看用到的ID 更新到 whole_report表中
        $this->dao->update(TABLE_WHOLE_REPORT)->data(['useIDArr'=>json_encode($problemOverallIdArr)])->where('id')->eq($overallId)->exec();

        foreach ($overalls as $deptId => $overall) {
            $arr       = [
                'deptID'      => $deptId,
                'tableType'   => $formType,
                'wholeID'     => $overallId,
                'detail'      => json_encode($overall),
                'createdDate' => date('Y-m-d H:i:s'),
            ];
            $this->dao->insert(TABLE_DETAIL_REPORT)->data($arr)->exec();
        }

        return $overallId;
    }

    public function getexportField($staticType,$phototype){
        $this->loadModel('problem');
        $this->loadModel('demand');
        $this->loadModel('requirement');
        $snapField = [];
        if($staticType == 'problemOverall'){
            $snapField = [
                'basic'=>$this->config->problem->list->exportMonthReportFields,
                'form'=>$this->config->problem->list->exportMonthReportPartFields1,
            ];
        }else if($staticType == 'problemWaitSolve'){
            $snapField = [
                'basic'=>$this->config->problem->list->exportMonthReportFields,
                'form'=>$this->config->problem->list->exportMonthReportPartFields1,
            ];
        }else if($staticType == 'problemUnresolved'){
            $snapField = [
                'basic'=>$this->config->problem->list->exportMonthReportFields,
                'form'=>$this->config->problem->list->exportMonthReportPartFields1,
            ];
        }else if($staticType == 'problemExceed'){
            $snapField = [
                'basic'=>$this->config->problem->list->exportMonthReportFields,
                'form'=>$this->config->problem->list->exportMonthReportPartFields1,
            ];
        }else if($staticType == 'problemExceedBackIn'){
            $snapField = [
                'basic'=>$this->config->problem->list->exportMonthReportFields,
                'form'=>$this->config->problem->list->exportMonthReportPartFields2,
            ];
        }else if($staticType == 'problemExceedBackOut'){
            $snapField = [
                'basic'=>$this->config->problem->list->exportMonthReportFields,
                'form'=>$this->config->problem->list->exportMonthReportPartFields3,
            ];
        }else if($staticType == 'problemCompletedPlan'){
            $snapField = [
                'basic'=>$this->config->problem->list->exportMonthReportFields,
                'form'=>$this->config->problem->list->exportMonthReportCompletedPlanFields,
            ];
        }else if($staticType == 'demand_whole'){
            $snapField = [
                'basic'=>$this->config->demand->list->exportMonthReportFields,
                'form'=>$this->config->demand->list->exportMonthReportPartFields1,
            ];
        }else if($staticType == 'demandunrealized'){
            $snapField = [
                'basic'=>$this->config->demand->list->exportMonthReportFields,
                'form'=>$this->config->demand->list->exportMonthReportPartFields1,
            ];
        }else if($staticType == 'demand_realized'){
            $snapField = [
                'basic'=>$this->config->demand->list->exportMonthReportFields,
                'form'=>$this->config->demand->list->exportMonthReportPartFields1,
            ];
        }else if($staticType == 'requirement_inside'){
            $snapField = [
                'basic'=>$this->config->requirement->exportlist->exportMonthReportFields,
                'form'=>$this->config->requirement->exportlist->exportMonthReportPartFields1,
            ];
        }else if($staticType == 'requirement_outside'){
            $snapField = [
                'basic'=>$this->config->requirement->exportlist->exportMonthReportFields,
                'form'=>$this->config->requirement->exportlist->exportMonthReportPartFields2,
            ];
        }else if($staticType == 'secondorderclass'){
            $snapField = [
                'basic'=>$this->config->secondmonthreport->export->secondorderclassFields,
                'form'=>$this->config->secondmonthreport->export->secondorderclassFields,
            ];
        }else if($staticType == 'secondorderaccept'){
            $snapField = [
                'basic'=>$this->config->secondmonthreport->export->secondorderclassFields,
                'form'=>$this->config->secondmonthreport->export->secondorderclassFields,
            ];
        }else if($staticType == 'support'){
            $snapField = [
                'basic'=>$this->config->secondmonthreport->export->supportFields,
                'form'=>$this->config->secondmonthreport->export->supportFields,
            ];
        }else if($staticType == 'workload'){
            $snapField = [
                'basic'=>$this->config->secondmonthreport->export->workloadFields,
                'form'=>$this->config->secondmonthreport->export->workloadFields,
            ];
        }else if($staticType == 'modifywhole'){
            $snapField = [
                'basic'=>$this->config->secondmonthreport->export->modifyFields,
                'form'=>$this->config->secondmonthreport->export->modifyFields,
            ];
        }else if($staticType == 'modifyabnormal'){
            $snapField = [
                'basic'=>$this->config->secondmonthreport->export->modifyFields,
                'form'=>$this->config->secondmonthreport->export->modifyFields,
            ];
        }
        return $snapField;
    }

    public function getSearchDefaultID($staticType, $timeType = 'hismonth'){
        $isYear = 'hisquarter' == $timeType ? [4] : [1,2];

        return $this->dao
            ->select('id')
            ->from(TABLE_WHOLE_REPORT)
            ->where('type')->eq($staticType)
            ->andWhere('isyear')->in($isYear)
            ->orderBy("year_desc,month_desc,id_desc")
            ->fetch('id');
    }

}