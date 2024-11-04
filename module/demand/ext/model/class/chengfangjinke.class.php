<?php

class chengfangjinkeDemand extends demandModel
{
    /**
     * 需求条目超时邮件提醒
     * @return array[]
     */
    public function outTimeNew()
    {
        $this->app->loadLang('problem');
        $problemObj     = $this->loadModel('problem');
        $outTime        = $this->lang->demand->demandOutTime['demandOutTime']     ?? 2; //解决时间超时月份
        $toOut          = $this->lang->demand->demandToOutTime['demandToOutTime'] ?? 5; //即将超时天数
        $start          = date('Y-m-d', strtotime('-' . ($outTime + 1) . ' months'));
        $requirementIds = [];//即将超期和超期的需求任务ID
        $aboutToIds     = [];//即将超期的需求任务ID
        $outTimeIds     = [];//超期的需求任务ID
        $DAboutToIds    = [];//即将超期的需求ID
        $DOutTimeIds    = [];//超期的需求ID
        $date           = date('Y-m-d');

        $requirements = $this->dao
            ->select('id, createdBy, newPublishedTime, feedbackStatus, feekBackStartTime')
            ->from(TABLE_REQUIREMENT)
            ->where('status')->notIN('deleted,deleteout')
            ->andWhere('sourceRequirement')->eq(1)
            ->andWhere()
            ->markleft(1)
            ->where('newPublishedTime')->gt($start)
            ->orWhere('feekBackStartTime')->gt($start)
            ->markright(1)
            ->fetchAll('id');
        if(empty($requirements)){
            return ['aboutTo' => $DAboutToIds, 'outTime' => $DOutTimeIds];
        }

        foreach ($requirements as $requirement){
            if('guestcn' == $requirement->createdBy && !in_array($requirement->feedbackStatus, ['tofeedback','todepartapproved','toinnovateapproved'])){
                continue;
            }
            if('guestcn' == $requirement->createdBy){
                $newPublishedTime = $requirement->feekBackStartTime != '0000-00-00 00:00:00' ? $requirement->feekBackStartTime : '';
            }else{
                $newPublishedTime = $requirement->newPublishedTime != '0000-00-00 00:00:00' ? $requirement->newPublishedTime : '';
            }
            if(empty($newPublishedTime)){
                continue;
            }

            $end   = $problemObj->getOverDate($newPublishedTime, $outTime); //超期时间
            $start = date('Y-m-d', strtotime($end) - 86400 * $toOut); //即将超期提醒
            $end   = date('Y-m-d', strtotime($end) + 86400); //超期的下一天
            if ($date > $start && $date < $end) {
                $requirementIds[] = $aboutToIds[$requirement->id] = $requirement->id;
            }
            if ($date == $end) {
                $requirementIds[] = $outTimeIds[$requirement->id] = $requirement->id;
            }
        }

        if(empty($requirementIds)){
            return ['aboutTo' => $DAboutToIds, 'outTime' => $DOutTimeIds];
        }

        $demands = $this->dao
            ->select('`id`,`code`,`title`,`end`,`acceptUser`,`status`,`desc`,`dealUser`,`delayStatus`, `requirementID`')
            ->from(TABLE_DEMAND)
            ->where('requirementID')->in($requirementIds)
            ->andWhere('status')->in('wait,feedbacked,build,released')
            ->andWhere('fixType')->eq('second')
            ->andWhere('sourceDemand')->eq(1)
            ->fetchAll('id');
        if(empty($demands)){
            return ['aboutTo' => $DAboutToIds, 'outTime' => $DOutTimeIds];
        }

        foreach ($demands as $key => $demand){
            //延期申请单已通过，不发超时提醒
            if($demand->delayStatus == 'success'){
                unset($demands[$key]);
                continue;
            }

            $toList = trim(trim($demand->dealUser, ',') . ',' . trim($demand->acceptUser, ','), ',');
            if(empty($toList)){
                unset($demands[$key]);
                continue;
            }

            if(in_array($demand->requirementID, $aboutToIds)){
                $DAboutToIds[] = $demand->id;
                $setMail       = 'setDemandToOutTimeMail';
                $ccList        = '';
            }else{
                $DOutTimeIds[] = $demand->id;
                $setMail       = 'setDemandOutTimeMail';
                $ccList        = implode(',', array_filter(array_unique(array_keys($this->lang->demand->outTimeList))));
            }

            $this->sendmailBase($demand, $setMail, 'demand', $toList, $ccList, 'demandouttime');
        }

        return ['aboutTo' => $DAboutToIds, 'outTime' => $DOutTimeIds];
    }

    public function outTime($start, $end, $setMail, $isOut = true)
    {
        $data = [];
        /*迭代二十九修改*/
        //内部自建
        $requirementInsideInfo =  $this->dao->select('id')->from(TABLE_REQUIREMENT)
            ->where('createdBy')->ne('guestcn')
            ->where('status')->notIN('deleted,deleteout')
            ->andWhere('sourceRequirement')->eq(1)
            ->fetchAll('id');
        $requirementInsideIds = $this->dao
            ->select('objectID,max(createdDate) as createdDate')
            ->from(TABLE_CONSUMED)
            ->where('objectType')->eq('requirement')
            ->andWhere('after')->eq('published')
            ->andWhere('deleted')->eq('0')
            ->andWhere('objectID')->in(array_keys($requirementInsideInfo))
            ->groupBy('objectID')
            ->having('createdDate between "' .$start . '" and "' . $end . '"' )
            ->fetchAll('objectID');
        $idsInside = array_keys($requirementInsideIds);

        //清总同步
        $requirementIdsByQz = $this->dao
            ->select('`id`,`code`')
            ->from(TABLE_REQUIREMENT)
            ->where('createdBy')->eq('guestcn')
            ->andWhere('feedbackStatus')->in(['tofeedback','todepartapproved','toinnovateapproved'])
            ->andWhere('feekBackStartTime')->between($start, $end)
            ->andWhere('sourceRequirement')->eq(1)
            ->fetchAll('id');
        $idsQz = array_keys($requirementIdsByQz);
        $requirementIds = array_unique(array_merge($idsInside,$idsQz));

        if(!empty($requirementIds)){
            $data = $this->dao
                ->select('`id`,`code`,`title`,`end`,`acceptUser`,`status`,`desc`,`dealUser`,`delayStatus`')
                ->from(TABLE_DEMAND)
                ->where('requirementID')->in($requirementIds)
                ->andWhere('status')->in('wait,feedbacked,build,released')
                ->andWhere('fixType')->eq('second')
                ->andWhere('sourceDemand')->eq(1)
                ->fetchAll('id');
        }

        if(!empty($data)){
            foreach ($data as $key => $item){
                //延期申请单已通过，不发超时提醒
                if($item->delayStatus == 'success'){
                    unset($data[$key]);
                    continue;
                }
                $toList = trim(trim($item->dealUser, ',') . ',' . trim($item->acceptUser, ','), ',');
                if(empty($toList)){
                    unset($data[$key]);
                    continue;
                }
                $ccList = implode(',', array_filter(array_unique(array_keys($this->lang->demand->outTimeList))));
                $ccList = $isOut ? $ccList : '';

                $this->sendmailBase($item, $setMail, 'demand', $toList, $ccList, 'demandouttime');
            }
        }

        return array_column($data, 'id');
    }

    /**
     * @param $demand
     * @param $setMail
     * @param $browseType
     * @param $toList
     * @param $ccList
     * @param $viewName
     * @param $variables
     * @param $mailTitle
     * @return void
     */
    function sendmailBase($demand, $setMail, $browseType, $toList, $ccList, $viewName, $mailTitle = false)
    {
        $this->loadModel('mail');
        $users   = $this->loadModel('user')->getPairs('noletter');

        /* 获取后台通知中配置的邮件发信。*/
        $this->app->loadLang('custommail');
        $mailConf   = $this->config->global->$setMail ?? '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf   = json_decode($mailConf);

        //邮件标题
        $mailTitle = !$mailTitle ? vsprintf($mailConf->mailTitle, $mailConf->variables) : $mailTitle;

        //邮件内容
        $oldcwd     = getcwd();
        $modulePath = $this->app->getModulePath($appName = '', $browseType);
        $viewFile   = $modulePath . 'view/' . $viewName . '.html.php';
        chdir($modulePath . 'view');
        if(file_exists($modulePath . 'ext/view/' . $viewName . '.html.php')) {
            $viewFile = $modulePath . 'ext/view/' . $viewName . '.html.php';
            chdir($modulePath . 'ext/view');
        }

        ob_start();
        include $viewFile;
        foreach(glob($modulePath . 'ext/view/sendmail.*.html.hook.php') as $hookFile) include $hookFile;
        $mailContent = ob_get_contents();
        ob_end_clean();
        chdir($oldcwd);

        //获取后台自定义配置人 研发部门
        $deptLeadersList = $this->config->demand->deptLeadersList;
        $user = $this->loadModel('user')->getById($demand->acceptUser);
        if($user)
        {
            $dept = $user->dept;
            $ccListArray = explode(',',$ccList);
            $deptLeaderListArray = explode(',',$deptLeadersList->$dept);
            $mergeCcList = array_filter(array_unique(array_merge($ccListArray,$deptLeaderListArray)));
            if(!empty($mergeCcList))
            {
                $ccList = implode(',',$mergeCcList);
            }
        }

        $this->mail->send($toList, $mailTitle, $mailContent, $ccList);

        if($this->mail->isError()) error_log(join("\n", $this->mail->getError()));
    }

    /**
     * 获取生成变更关联需求条目下拉框数据
     * @param $type
     * @param $id
     * @param $isNewModifycncc
     * @param $exWhere
     * @return mixed
     */

    public function modifySelect($type = '', $id = 0, $isNewModifycncc = 1, $exWhere = '', $demandIds = [])
    {
        //获取已关联生产变更的需求条目ID
        $idArr = $this->getUseDemandId($type, $id, $isNewModifycncc);

        //查询内部需求条目
        $demandInsideList = $this->dao->select("id,concat(code,'（',IFNULL(trim(title),''),'）') as code")->from(TABLE_DEMAND)
            ->where('status')->ne('deleted')
            ->andWhere('sourceDemand')->eq(2)
            ->beginIF($exWhere)->andWhere($exWhere)->fi()
            ->beginIF($demandIds)->andWhere('id')->in($demandIds)->fi()
            ->orderBy('id_desc')
            ->fetchPairs();
        //查询没有关联生产变更的外部需求条目
        $demandOutsideList = $this->dao->select("id,concat(code,'（',IFNULL(title,''),'）') as code")->from(TABLE_DEMAND)
            ->where('status')->in(['feedbacked', 'delivery', 'chanereturn', 'changeabnormal', 'onlinesuccess'])//开发中、已交付、变更退回、变更单异常、上线成功
            ->andWhere('id')->notin($idArr)
            ->andWhere('sourceDemand')->eq(1)
            ->beginIF($exWhere)->andWhere($exWhere)->fi()
            ->beginIF($demandIds)->andWhere('id')->in($demandIds)->fi()
            ->orderBy('id_desc')
            ->fetchPairs();
        //合并内部、外部需求条目
        $demandList = $demandOutsideList + $demandInsideList;
        if($demandIds){
            $tempList = $this->dao->select("id,concat(code,'（',IFNULL(trim(title),''),'）') as code")->from(TABLE_DEMAND)
                ->where('status')->ne('deleted')
                ->beginIF($exWhere)->andWhere($exWhere)->fi()
                ->andWhere('id')->in($demandIds)
                ->orderBy('id_desc')
                ->fetchPairs();
            if($tempList){
                $demandList = $tempList + $demandList;
            }
        }
        krsort($demandList);
        return $demandList;
    }


    /**
     * 获得投产允许使用的需求条目
     *
     * @param int $ignorePutProductionId
     * @param $demandIds
     * @return mixed
     */
    public function getAllowPutProductionDemandList($ignorePutProductionId = 0, $demandIds = []){
        $demandList = [];
        //获取已关联生产变更的需求条目ID
        $modifyDemandId = $this->getDemandIdByModify();
        $outwardDemandId = $this->getDemandIdByOutwarddelivery(true);
        $usedDemandIds = $this->loadModel('putproduction')->getUsedByPutProductionDemandIds($ignorePutProductionId); //被投产使用的
        $usedDemandIds = array_unique(array_merge($usedDemandIds, $modifyDemandId, $outwardDemandId));

        //查询没有关联生产变更的外部需求条目
        $demandOutsideList = $this->dao->select("id,concat(code,'（',IFNULL(title,''),'）') as code")->from(TABLE_DEMAND)
            ->where('status')->in(['feedbacked', 'changeabnormal', 'chanereturn'])
            ->andWhere('id')->notin($usedDemandIds)
            ->beginIF(!empty($demandIds))->andWhere('id')->in($demandIds)->fi()
            ->andWhere('sourceDemand')->eq(1)
            ->orderBy('id_desc')
            ->fetchPairs();

        //合并内部、外部需求条目
        if($demandOutsideList){
            $demandList = $demandOutsideList;
            krsort($demandList);
        }
        return $demandList;
    }

    /**
     * 生产变更修改页面需求条目下拉框
     * @param $demandId
     * @return array
     */
    public function modifySelectByEdit($demandId, $type, $objectId, $isNewModifycncc = 1,$source='')
    {
        $demandDiffList = [];
        $demandList     = array('' => '') + $this->modifySelect($type, $objectId, $isNewModifycncc);

        if(isset($this->config->singleUsage) && 'on' == $this->config->singleUsage){
            $demandId   = array_filter(explode(',', trim($demandId)));
            $diffId     = array_diff($demandId, array_keys($demandList));

            if(!empty($diffId)){
                $demandDiffList = $this->dao->select("id,concat(code,'（',IFNULL(title,''),'）') as code")->from(TABLE_DEMAND)
                    ->where('id')->in($diffId)
                    ->orderBy('id_desc')
                    ->fetchPairs();
            }
            $demandList = $demandList + $demandDiffList;
        }
        $demandId = explode(',', trim($demandId));

        if (!empty($demandId) && $source == 'edit'){
            $demandDiffList = $this->dao->select("id,concat(code,'（',IFNULL(title,''),'）') as code")->from(TABLE_DEMAND)
                ->where('id')->in($demandId)
                ->orderBy('id_desc')
                ->fetchPairs();
            $demandList = $demandList + $demandDiffList;
        }

        return $demandList;
    }

    /**
     * 需求条目是否关联一次
     * @param $demandId
     * @param $type
     * @param $objectId
     * @return array
     */
    public function isSingleUsage($demandId, $type = '', $objectId = '', $isNewModifycncc = 1)
    {
        $demandId   = explode(',', trim($demandId));
        $demandId   = array_filter($demandId);

        $demandData = [];
        $demandList = $this->dao->select("id,concat(code,'（',IFNULL(title,''),'）') as code, status")->from(TABLE_DEMAND)
            ->where('id')->in($demandId)
            ->andWhere('sourceDemand')->eq(1)
            //->andWhere('status')->in(['closed', 'suspend', 'wait'])
            ->orderBy('id_desc')
            ->fetchAll('id');
        if(empty($demandList)){
            return [];
        }

        //如果关联的外部需求条目有已关闭、已挂起、已录入，提示用户
        foreach ($demandList as $item){
            $demandData[$item->status][$item->id] = $item->code;
        }
        if(isset($demandData['closed'])){
            dao::$errors[] = sprintf($this->lang->demand->statusClosedError , implode('、', $demandData['closed']));
        }
        if(isset($demandData['suspend'])){
            dao::$errors[] = sprintf($this->lang->demand->statusSuspendError , implode('、', $demandData['suspend']));
        }
        if(isset($demandData['wait'])){
            dao::$errors[] = sprintf($this->lang->demand->statusWaitError , implode('、', $demandData['wait']));
        }
        if(dao::isError()){
            return $demandData;
        }

        //判断是否有重复关联需求条目
        $demandIdList = $this->getUseDemandId($type, $objectId, $isNewModifycncc);
        $diff         = array_intersect(array_column($demandList, 'id'), $demandIdList);
        if(!empty($diff)){
            $singleUsage = $this->dao->select("id,concat(code,'（',IFNULL(title,''),'）') as code")->from(TABLE_DEMAND)
                ->where('id')->in($diff)
                ->orderBy('id_desc')
                ->fetchPairs();
            dao::$errors[] = sprintf($this->lang->demand->singleUsageError , implode('、', $singleUsage));

            return $singleUsage;
        }

        return [];
    }

    /**
     * 已关联生产变更的需求条目
     * @param $type
     * @param $id
     * @return array
     */
    public function usedDemandIds($type = '', $id = 0, $demandIds = []){
        $modifyUsedDemandIds = $this->loadModel('secondorder')->getUsedDemandIdsByModify($type, $id, $demandIds);
        $outwardDeliveryUsedDemandIds = $this->loadModel('secondorder')->getUsedDemandIdsByOutwardDelivery($type, $id, $demandIds);
        //忽略id
        $ignorePutProductionId = $type == 'putproduction'? $id: 0;
        $putproductionUsedDemandIds = $this->loadModel('putproduction')->getUsedByPutProductionDemandIds($ignorePutProductionId);
        $creditUsedDemandIds = $this->loadModel('secondorder')->getUsedDemandIdsByCredit($type, $id, $demandIds);
        $data = array_unique(array_merge($modifyUsedDemandIds, $outwardDeliveryUsedDemandIds, $putproductionUsedDemandIds, $creditUsedDemandIds));
        return $data;
    }

    /**
     * 清总对外交付、金信生产变更、金信投产已关联的需求条目
     * @param $type
     * @param $id
     * @param $isNewModifycncc
     * @return void
     */
    public function getUseDemandId($type = '', $id = 0, $isNewModifycncc = 1)
    {
        //是否完全互斥
        $isAbsoluteMutex = false;
        $absoluteMutexModules = $this->lang->demand->absoluteMutexModules;
        if(in_array($type, $absoluteMutexModules)){
            $isAbsoluteMutex = true;
        }
        if($isAbsoluteMutex){ //完全互斥 (目前就金投产和征信交付)
            $usedDemandIds = $this->usedDemandIds($type, $id);
            return $usedDemandIds;
        }
        $modifyId          = 0;
        $outwardDeliveryId = 0;
        $usedDemandIds     = [];
        if($isNewModifycncc){
            $usedDemandIds = $this->loadModel('putproduction')->getUsedByPutProductionDemandIds(0); //被投产使用的
            $creditUsedDemandIds = $this->loadModel('secondorder')->getUsedDemandIdsByCredit($type, $id);
            $usedDemandIds = array_merge($usedDemandIds, $creditUsedDemandIds);
        }

        $singleUsageFlag = isset($this->config->singleUsage) && 'on' == $this->config->singleUsage;
        if($singleUsageFlag){
            if('modify' == $type){
                $modifyId = $id;
            }elseif($type == 'outwarddelivery'){
                $outwardDeliveryId = $id;
            }
            $modifyType = false;
            if(in_array($type, $this->lang->demand->mutexIsNewModifycnccModules)){
                $modifyType = true;
            }
            $modifyDemandId = $this->getDemandIdByModify($modifyId);
            $outwardDemandId = $this->getDemandIdByOutwarddelivery($modifyType, $outwardDeliveryId);
            $usedDemandIds = array_unique(array_merge($usedDemandIds, $modifyDemandId, $outwardDemandId));
        }
        return $usedDemandIds;
    }

    /**
     * 获取金信生产变更关联的需求条目
     * @param $id
     * @return array
     */
    public function getDemandIdByModify($id = 0)
    {
        $modify = $this->dao->select('id, demandId')->from(TABLE_MODIFY)
            ->where('status')->notin(['modifysuccesspart', 'modifyerror', 'modifyrollback', 'modifyfail', 'modifycancel','deleted','cancel'])
            ->beginIF($id > 0)->andWhere('id')->ne($id)->fi()
            ->fetchPairs();
        $modify = explode(',', implode(',', $modify));

        return array_values(array_filter($modify));
    }

    /**
     * 获取清总对外交付关联需求条目
     * @param $modify 为true时，只获取有生产变更的单子
     * @param $id
     * @return array
     */
    public function getDemandIdByOutwarddelivery($modify = false, $id = 0)
    {
        $outwarddelivery = $this->dao->select('id, demandId')->from(TABLE_OUTWARDDELIVERY)
            ->where('status')->notin(['modifysuccesspart','modifyfail','modifycancel','deleted','cancel'])
            ->andWhere('deleted')->eq(0)
            ->beginIF($id > 0)->andWhere('id')->ne($id)->fi()
            ->beginIF($modify)->andWhere('isNewModifycncc')->eq('1')->fi()
            ->fetchPairs();
        $outwarddelivery = explode(',', implode(',', $outwarddelivery));

        return array_values(array_filter($outwarddelivery));
    }

    /**
     * @Notes:需求条目二线月报统计
     * @Date: 2023/10/9
     * @Time: 15:40
     * @Interface monthreport
     * @param $date
     * @param $time
     */
    public function monthReport($endtime,$time,$starttime,$dtype)
    {

        if(!$time){
            $time = time();
        }

        if($starttime){
            if(strpos($starttime,'_') !== false){
                $starttime = str_replace('_','-',$starttime);
            }
        }
        if($endtime){
            if(strpos($endtime,'_') !== false){
                $endtime = str_replace('_','-',$endtime);
            }
        }
        /* @var requirementModel $requirementModel*/
        $requirementModel = $this->loadModel('requirement');
        $demandModel = $this->loadModel('demand');
//        $secondmonthreportModel = $this->loadModel('secondmonthreport');
        //基础数据
        $wholeInfo = $this->needReportDataBase($endtime,$time,$starttime,$dtype); //需求条目
        $wholeRequirementInfo = $requirementModel->monthReportBaseDataAboutRequirement($endtime,$starttime,$dtype); //需求任务

        //需求整体情况统计表
        $wholeId = $this->monthReportInsertData('demand_whole',$endtime,$time,$starttime,$dtype);
        $wholeDemandIDS = [];
        if(!empty($wholeId)){
            $wholeDemandIDS = $this->wholeDemandMonth($wholeId,$wholeInfo);
        }

        //未实现需求统计表
        $demand_unrealizedIDS = [];
        $unrealizedId = $this->monthReportInsertData('demand_unrealized',$endtime,$time,$starttime,$dtype);

        if(!empty($unrealizedId)) {
            $demand_unrealizedIDS = $this->unrealizedDemandMonth($unrealizedId,$wholeInfo,$endtime,$starttime,$dtype);
        }

        //需求条目实现超期统计表
        $realizedId = $this->monthReportInsertData('demand_realized',$endtime,$time,$starttime,$dtype);

        $demand_realizedIDS = [];
        if(!empty($realizedId)) {
            $demand_realizedIDS = $this->realizedDemandMonth($realizedId,$wholeInfo,$endtime,$starttime,$dtype);
        }

        //需求任务内部反馈超期统计表
        $realizedRequirementInsideId = $this->monthReportInsertData('requirement_inside',$endtime,$time,$starttime,$dtype);
        $requirement_insideIDS = [];

        if(!empty($realizedRequirementInsideId)) {
            $requirement_insideIDS = $this->realizedRequirementMonthInside($realizedRequirementInsideId,$wholeRequirementInfo);
        }

        //需求任务外部反馈超期统计表
        $realizedRequirementOutsideId = $this->monthReportInsertData('requirement_outside',$endtime,$time,$starttime,$dtype);
        $requirement_outsideIDS = [];

        if(!empty($realizedRequirementOutsideId)) {
            $requirement_outsideIDS = $this->realizedRequirementMonthOutside($realizedRequirementOutsideId,$wholeRequirementInfo);
        }

        return ['wholeDemandIDS'=>$wholeDemandIDS,'demand_unrealizedIDS'=>$demand_unrealizedIDS,'demand_realizedIDS'=>$demand_realizedIDS,'requirement_insideIDS'=>$requirement_insideIDS,'requirement_outsideIDS'=>$requirement_outsideIDS];
    }

    /**
     * @Notes: 月报整体统计数据入库
     * @Date: 2023/10/12
     * @Time: 14:46
     * @Interface monthReportInsertData
     * @param $type
     * @param $date
     * @param $time
     */
    public function monthReportInsertData($type,$endtime,$time,$starttime,$dtype)
    {

        /*$year = helper::currentYear();
        $month = date('m');
        if(!empty($endtime))
        {
            $year  = date('Y', strtotime($endtime));
            $month = date('n', strtotime($endtime));
        }

        $fileUrlType = 'demand';
        if(strpos($type,'requirement') !== false)
        {
            $fileUrlType = 'requirement';
        }

        if($month == '01' || $month == '1')
        {
            $month = '13';
            $year = $year-1;
        }*/
        $fileUrlType = 'demand';
        if(strpos($type,'requirement') !== false)
        {
            $fileUrlType = 'requirement';
        }
        $startAndEndDate = $this->loadModel('secondmonthreport')->getTimeFrame($endtime,$starttime,$dtype);
        $year = helper::currentYear();
        $topData = array();//构造顶部整体表数据
        $topData['year'] = $year;
        $topData['month'] = 0;
        $topData['type'] = $type;
        $topData['dtype'] = $dtype;
        $topData['startday'] = $startAndEndDate['startday'];
        $topData['endday'] = $startAndEndDate['endday'];

        $filestartday = str_replace(['-','_'],'',$startAndEndDate['startday']);
        $fileendday = str_replace(['-','_'],'',$startAndEndDate['endday']);
        $topData['fileUrl'] = '/data/upload/'.$fileUrlType.'_monthreport' . $filestartday . '_' . $fileendday. '_' . $time . '.xlsx';
        $fileUrlType2 = strtolower($type);
        $topData['fileUrl2'] = '/data/upload/'.$fileUrlType.'_monthreport_'.$fileUrlType2 . $filestartday . '_' . $fileendday. '_' . $time . '.xlsx';

        $this->dao->insert(TABLE_WHOLE_REPORT)->data($topData)->exec();
        return $this->dao->lastInsertId();
    }

    /**
     * @Notes:月报统计基础数据
     * @Date: 2023/10/12
     * @Time: 15:02
     * @Interface needReportDataBase
     * @return mixed
     * @param $date
     */
    public function needReportDataBase($endtime,$time,$starttime,$dtype)
    {
        /* @var secondmonthreportModel $reportModel*/
        $reportModel = $this->loadModel('secondmonthreport');
        $startAndEndDate = $reportModel->getTimeFrame($endtime,$starttime,$dtype);
        $start = $startAndEndDate['startdate'] ?? '';
        $end = $startAndEndDate['enddate'] ?? '';
        $field = "t1.id,t1.requirementID,t1.status,t1.createdDate,t1.acceptDept,t2.newPublishedTime,t1.fixType,t1.solvedTime,t1.delayStatus,t1.isExtended,t2.createdBy,t2.actualMethod,t2.feekBackStartTime";
        $info = $this->dao->select($field)->from(TABLE_DEMAND)->alias('t1')
            ->leftJoin(TABLE_REQUIREMENT)->alias('t2')->on('t1.requirementID = t2.id')
            ->where('t1.status')->ne('deleted')
            ->andWhere('t2.`status`')->ne('deleteout')
            ->andWhere('t1.sourceDemand')->eq('1')
            ->andWhere('t1.createdDate')->between($start, $end)
            ->fetchAll();
        foreach ($info as $key => $item)
        {
            if($item->createdBy == 'guestcn'){
                $info[$key]->publishedTime = $item->feekBackStartTime != '0000-00-00 00:00:00' ? $item->feekBackStartTime : '';
            }else{
                $info[$key]->publishedTime = $item->newPublishedTime != '0000-00-00 00:00:00' ? $item->newPublishedTime : '';
            }
        }

        return $info;
    }

    /**
     * @Notes:需求整体情况统计表详情数据
     * @Date: 2023/10/12
     * @Time: 15:06
     * @Interface wholeDemandMonth
     * @param $wholeId
     * @param $wholeInfo
     */
    public function wholeDemandMonth($wholeId,$wholeInfo)
    {
        $wholeData = array();  //需求整体情况统计表详情
        $useIDS = [];

            /*
             * 构造入湖数据数组
             * ①按照部门做统计
             * ②已实现：取值范围为需求条目的“实现方式”为二线实现，且“流程状态”字段的上线成功、已关闭、已挂起、已交付的年度累计
             * ③未实现：取值范围为需求条目的“实现方式”为二线实现，且“流程状态”字段的已录入、开发中、测试中、已发布、变更单退回、变更单异常的年度累计。
             */
            $deptArr = array();
            $jsonWholeData = array();
            $secondmonthreportModel = $this->loadModel('secondmonthreport');
            $deptParent = $this->loadModel('dept')->getDeptAndChild();


            $needShowDeptList = $secondmonthreportModel->getNeedShowDept();

            foreach ($wholeInfo as $wholeValue)
            {
                if(!$wholeValue->acceptDept){
                    $wholeValue->acceptDept = -1;
                }
//                if(in_array($wholeValue->acceptDept,$needAllStaticDeptList)) {
                    $deptArr[$deptParent[$wholeValue->acceptDept]][] = $wholeValue;
//                }
            }


                foreach ($deptArr as $i => $item)
                {
                    $implementedNum = 0;//已实现数量
                    $unrealizedNum  = 0;//未实现数量
                    foreach ($item as $v)
                    {
                        //已实现
                        if($v->fixType == 'second' && in_array($v->status,$this->lang->demand->implementedArr))
                        {
                            $implementedNum++;
                            $useIDS[]=$v->id;
                        }

                        //未实现
                        if($v->fixType == 'second' && in_array($v->status,$this->lang->demand->unrealizedArr))
                        {
                            $unrealizedNum++;
                            $useIDS[]=$v->id;
                        }

                    }
                    $total = $implementedNum + $unrealizedNum;
                    if(!empty($total))
                    {
                        $realizationRate = $implementedNum/$total* 100;
                    }else{
                        $realizationRate = '0';
                    }

                    if($i == 0) continue;
                    $jsonWholeData[$i]['deptID'] = $i;
                    $jsonWholeData[$i]['implementedNum'] = $implementedNum;
                    $jsonWholeData[$i]['unrealizedNum'] = $unrealizedNum;
                    $jsonWholeData[$i]['total'] = $total;
                    $jsonWholeData[$i]['realizationRate'] = number_format($realizationRate,2);

                }
                //补齐部门数据
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
                //剔除 不是统计部门中部门数据为 0 的数据
                foreach ($jsonWholeData as $dept=>$dataArr){
                    if(!$dataArr['total'] && !in_array($dept,$needShowDeptList)){
                        unset($jsonWholeData[$dept]);
                    }
                }

                //构造入库数据
//                arsort($jsonWholeData);
                foreach ($jsonWholeData as $key => $data)
                {
                    $wholeData['deptID']    = $data['deptID'];
                    $wholeData['tableType'] = 'demand_whole';
                    $wholeData['wholeID']   = $wholeId;
                    $wholeData['detail']    = json_encode($data);
                    $wholeData['createdDate']= helper::now();
                    $this->dao->insert(TABLE_DETAIL_REPORT)->data($wholeData)->exec();
                }



        return array_unique($useIDS);
    }

    /**
     * @Notes:未实现需求统计数据入库
     * @Desc 需求数：取值范围为需求条目的“实现方式”为二线实现，
     * 且“需求任务的已发布（取最新已发布）时间”据统计截止时点两个月的，
     * “流程状态”字段为已录入、开发中、测试中、已发布、变更单退回、变更单异常的年度累计
     * @Date: 2023/10/12
     * @Time: 15:07
     * @Interface unrealizedDemandMonth
     * @param $unrealizedId
     * @param $unrealizedInfo
     * @param $date
     */
    public function unrealizedDemandMonth($unrealizedId,$unrealizedInfo,$endtime,$starttime,$dtype)
    {
        $unrealizedData     = array();
        $deptArr            = array();
        $jsonUnrealizedData = array();
        $useIDS = [];
        /* @var secondmonthreportModel $reportModel*/
        $reportModel = $this->loadModel('secondmonthreport');
        $startAndEndDate = $reportModel->getTimeFrame($endtime,$starttime,$dtype);
        $end = $startAndEndDate['enddate'] ? date('Y-m-d',strtotime($startAndEndDate['enddate'])) : '';


        $deptParent = $this->loadModel('dept')->getDeptAndChild();

//        $needStaticDeptList = $reportModel->getNeedStaticDept();
        $needShowDeptList = $reportModel->getNeedShowDept();

        foreach ($unrealizedInfo as $unrealizedValue)
        {
            if(!$unrealizedValue->acceptDept){
                $unrealizedValue->acceptDept = -1;
            }
//            if(in_array($unrealizedValue->acceptDept,$needStaticDeptList)) {
                $deptArr[$deptParent[$unrealizedValue->acceptDept]][] = $unrealizedValue;
//            }
        }

            foreach ($deptArr as $i => $item)
            {
                $twoMonthNum = 0;
                $sixMonthNum = 0;
                $oneYearNum  = 0;

                foreach ($item as $v)
                {
                    //需求条目的“实现方式”为二线实现
                    if($v->fixType == 'second' && in_array($v->status,$this->lang->demand->unrealizedArr))
                    {
                        $publishedTime = $v->publishedTime;
                        if(!empty($publishedTime) && $publishedTime != '0000-00-00' && $publishedTime != '0000-00-00 00:00:00')
                        {
                            //2个月未实现需求数
                            $twoMonthsEndTime = $reportModel->getOverDate($publishedTime,2);
                            if($twoMonthsEndTime <= $end) {
                                $twoMonthNum++;
                                $useIDS[] = $v->id;
                            }
                            //6个月未实现需求数
                            $sixMonthsEndTime = $reportModel->getOverDate($publishedTime,6);
                            if($sixMonthsEndTime <= $end) {
                                $sixMonthNum++;

                            }
                            //1年未实现需求数
                            $oneYearEndTime = $reportModel->getOverDate($publishedTime,12);
                            if($oneYearEndTime <= $end) {
                                $oneYearNum++;

                            }
//                            if($i == 10)
//                            {
//                                echo "部门id".$i." 条目ID:".$v->id." requirementID:".$v->requirementID.' 开始时间：'.$publishedTime." 两个月：".$twoMonthsEndTime." 六个月：".$sixMonthsEndTime.' 1年：'.$oneYearEndTime.'结束：'.$end."<br />";
//                            }
                        }

                    }

                }

                if($i == 0) continue;
                $jsonUnrealizedData[$i]['deptID'] = $i;
                $jsonUnrealizedData[$i]['twoMonthNum'] = $twoMonthNum;
                $jsonUnrealizedData[$i]['sixMonthNum'] = $sixMonthNum;
                $jsonUnrealizedData[$i]['oneYearNum']  = $oneYearNum;

            }
            //补齐部门数据
            foreach ($needShowDeptList as $showDept){
                if(!isset($jsonUnrealizedData[$showDept])){
                    $jsonUnrealizedData[$showDept] = [
                        'deptID'=>$showDept,
                        'twoMonthNum'=>0,
                        'sixMonthNum'=>0,
                        'oneYearNum'=>0,
                    ];
                }
            }

        //剔除 不是统计部门中部门数据为 0 的数据
        foreach ($jsonUnrealizedData as $dept=>$dataArr){
            if(!$dataArr['twoMonthNum'] && !in_array($dept,$needShowDeptList)){
                unset($jsonUnrealizedData[$dept]);
            }
        }

            //构造入库数据
//            arsort($jsonUnrealizedData);
            foreach ($jsonUnrealizedData as $key => $data)
            {
                $unrealizedData['deptID']    = $data['deptID'];
                $unrealizedData['tableType'] = 'demand_unrealized';
                $unrealizedData['wholeID']   = $unrealizedId;
                $unrealizedData['detail']    = json_encode($data);
                $unrealizedData['createdDate']= helper::now();
                $this->dao->insert(TABLE_DETAIL_REPORT)->data($unrealizedData)->exec();
            }


        return array_unique($useIDS);


    }

    /**
     * @Notes:需求条目实现超期统计数入库
     *
     * @Desc （1）已实现但超过两个月的，取值范围为需求条目的“实现方式”为二线实现，且所属需求任务的“实现方式”仅为二线实现，且“流程状态”上线成功、已关闭、已交付的，
     * 且“需求任务的发布时间（取最新已发布）”和“交付日期”字段差值超过两个月的；
     *（2）截至到统计时点两个月仍未实现的，取值范围为需求条目的“实现方式”为二线实现，且所属需求任务的“实现方式”仅为二线实现，“需求任务的发布时间（取最新已发布）”据统计截止时点两个月的，
     * “流程状态”字段为已录入、开发中、已发布、测试中、变更单退回、变更单异常的累计值；
     * 上述两者加和作为最终超期需求条目数X。
     * 1、除去延期审批通过的需求单
     * 2、除去已挂起、已关闭状态的需求单
     * 3、剔除“是否纳入交付超期”为“否”的。
     * 4、超期率：合计/条目总数。
     * 5、条目总数：该部门内所有的需求条目（剔除已删除）
     *
     * @Date: 2023/10/12
     * @Time: 15:07
     * @Interface realizedDemandMonth
     * @param $realizedId
     * @param $realizedInfo
     * @param $date
     */
    public function realizedDemandMonth($realizedId,$realizedInfo,$endtime,$starttime,$dtype)
    {
        $realizedData       = array();
        $deptArr            = array();
        $jsonRealizedData   = array();
        $useIDS = [];
        /* @var secondmonthreportModel $reportModel*/
        $reportModel = $this->loadModel('secondmonthreport');
        $startAndEndDate = $reportModel->getTimeFrame($endtime,$starttime,$dtype);
        $end = $startAndEndDate['enddate'] ? date('Y-m-d 23:59:59',strtotime($startAndEndDate['enddate'])) : '';
        $now = helper::now();

        $porjectLang = $this->app->loadLang('project');


        $needShowDeptList = $reportModel->getNeedShowDept();

        $deptParent = $this->loadModel('dept')->getDeptAndChild();
        foreach ($realizedInfo as $realizedValue)
        {
            if(!$realizedValue->acceptDept){
                $realizedValue->acceptDept = -1;
            }

//            if(in_array($realizedValue->acceptDept,$monthReportPandMStaticDept)){
                $deptArr[$deptParent[$realizedValue->acceptDept]][] = $realizedValue;
//            }

        }

            foreach ($deptArr as $i => $item)
            {
                $realizedNum = 0;
                $twoMonthNum = 0;
                $amount      = 0;
                $overdueRate = 0;
                $total       = 0;
//                $total       = count($item);

                foreach ($item as $v)
                {
                    $solvedTime = $v->solvedTime;
                    $publishedTime = $v->publishedTime;
                    if($v->fixType == 'second')
                    {
                        //条目总数 只统计二线
                        $total++;

                    }

                    if(!empty($publishedTime) && $publishedTime != '0000-00-00' && $publishedTime != '0000-00-00 00:00:00')
                    {
                        $his = date("H:i:s",strtotime($publishedTime));
                        //2个月未实现需求数
                        $twoMonthsEndTime = $reportModel->getOverDate($publishedTime,2).' '.$his;

                        /*
                         * ①需求条目的“实现方式”为二线实现
                         * ②所属需求任务的“实现方式”仅为二线实现
                         * ③且“流程状态”上线成功、已关闭、已交付
                         * ④剔除“是否纳入交付超期”为“否”的
                         * ⑤除去延期审批通过的需求单
                         */
                        if($v->fixType == 'second' && $v->actualMethod == 'second' && $v->isExtended != 1 && in_array($v->status,$this->lang->demand->realizedArr))
                        {
                            //已实现但超过2个月
                            if($twoMonthsEndTime <= $solvedTime){
                                $realizedNum++;
                                $useIDS[] = $v->id;
                            }
                        }
                        if($v->fixType == 'second' && $v->actualMethod == 'second' && $v->isExtended != 1 && in_array($v->status,$this->lang->demand->unrealizedArr))
                        {
                            //2个月未实现需求数
                            if($twoMonthsEndTime <= $now) {
//                                if($i == 8)
//                                {
//                                    echo "部门id".$i." 条目ID:".$v->id." requirementID:".$v->requirementID.' 开始时间：'.$publishedTime." 两个月：".$twoMonthsEndTime." 交付时间：".$solvedTime.'结束：'.$end." 是否交付超期！=1符合：".$v->isExtended.' 流程状态'.$v->status." 任务实现方式".$v->actualMethod."<br />";
//                                }
                                $twoMonthNum++;
                                $useIDS[] = $v->id;
                            }
                        }
                        $amount = $realizedNum + $twoMonthNum;//条目总数
                        if(!empty($total))
                        {
                            $overdueRate = $amount/$total* 100;//超期率 合计/条目总数
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
                $jsonRealizedData[$i]['total']       = $total;
                $jsonRealizedData[$i]['overdueRate'] = number_format($overdueRate,2);

            }



            //补齐部门数据
            foreach ($needShowDeptList as $showDept){
                if(!isset($jsonRealizedData[$showDept])){

                    $jsonRealizedData[$showDept] = [
                        'deptID'=>$showDept,
                        'realizedNum'=>0,
                        'twoMonthNum'=>0,
                        'amount'=>0,
                        'total'=>0,
                        'overdueRate'=>"0.00"
                    ];
                }
            }

            //剔除 不是统计部门中部门数据为 0 的数据
            foreach ($jsonRealizedData as $dept=>$dataArr){
                if(!$dataArr['total'] && !in_array($dept,$needShowDeptList)){
                    unset($jsonRealizedData[$dept]);
                }
            }

            //构造入库数据
//            arsort($jsonRealizedData);
            foreach ($jsonRealizedData as $key => $data)
            {
                $realizedData['deptID']    = $data['deptID'];
                $realizedData['tableType'] = 'demand_realized';
                $realizedData['wholeID']   = $realizedId;
                $realizedData['detail']    = json_encode($data);
                $realizedData['createdDate']= helper::now();
                $this->dao->insert(TABLE_DETAIL_REPORT)->data($realizedData)->exec();
            }


        return array_unique($useIDS);

    }

    /**
     * @Notes:需求任务内部反馈超期统计数据入库
     * @Date: 2023/10/17
     * @Time: 10:51
     * @Interface realizedRequirementMonthInside
     * @param $realizedId
     * @param $realizedInfo
     */
    public function realizedRequirementMonthInside($realizedId,$realizedInfo)
    {
        $realizedData= array();
        $deptArr     = array();
        $jsonRealizedData   = array();
        $useIDS = [];
        $porjectLang = $this->app->loadLang('project');

        $requirementInfo = [];
        $secondmonthreportModel = $this->loadModel('secondmonthreport');
        $deptParent = $this->loadModel('dept')->getDeptAndChild();

        $needShowDeptList = $secondmonthreportModel->getNeedShowDept();

        foreach ($realizedInfo as $realizedValue)
        {
            if(!$realizedValue->dept){
                $realizedValue->dept = -1;
            }
//            if(in_array($realizedValue->dept,$monthReportPandMStaticDept)){
                $deptArr[$deptParent[$realizedValue->dept]][] = $realizedValue;
//            }

        }
        //因不需要补齐部门，此判断可保留
        if(!empty($deptArr))
        {
            foreach ($deptArr as $i => $item)
            {
                $overdueNum  = 0;//反馈超期数
                if(empty($i)) continue;
                $total = count($item);//反馈单总数

                foreach ($item as $v)
                {
                    if($v->feekBackStartTime == '0000-00-00 00:00:00') $v->feekBackStartTime = '';
                    if($v->deptPassTime == '0000-00-00 00:00:00') $v->deptPassTime = '';
                    //内部反馈超期数
                    if($v->ifOverDate == 2 && $v->feedbackOver != 1)
                    {
                        $overdueNum++;
//                        $requirementInfo[$i][] = ['code'=>$v->code, 'system'=>$v->app, 'acceptTime'=>$v->feekBackStartTime,'feedbackDate'=>$v->deptPassTime];
                        $useIDS[] = $v->id;
                    }

                }

                if(!empty($total))
                {
                    $overdueRate = $overdueNum/$total* 100;//超期率 合计/条目总数
                }else{
                    $overdueRate = 0;
                }
                $jsonRealizedData[$i]['deptID']          = $i;
                $jsonRealizedData[$i]['total']           = $total;
                $jsonRealizedData[$i]['foverdueNum']           = $overdueNum;
                $jsonRealizedData[$i]['overdueRate']     = number_format($overdueRate,2);
                /*if(isset($requirementInfo[$i])){
                    $jsonRealizedData[$i]['requirementCode'] = $requirementInfo[$i];
                }else{
                    $jsonRealizedData[$i]['requirementCode'] = [];
                }*/

            }
        }

        //补齐部门数据
        foreach ($needShowDeptList as $showDept){
            if(!isset($jsonRealizedData[$showDept])){

                $jsonRealizedData[$showDept] = [
                    'deptID'=>$showDept,
                    'total'=>0,
                    'foverdueNum'=>0,
                    'overdueRate'=>"0.00"
                ];
            }
        }

        //剔除 不是统计部门中部门数据为 0 的数据
        foreach ($jsonRealizedData as $dept=>$dataArr){
            if(!$dataArr['total'] && !in_array($dept,$needShowDeptList)){
                unset($jsonRealizedData[$dept]);
            }
        }


        //构造入库数据
//        arsort($jsonRealizedData);
        foreach ($jsonRealizedData as $key => $data)
        {
            $realizedData['deptID']    = $data['deptID'];
            $realizedData['tableType'] = 'requirement_inside';
            $realizedData['wholeID']   = $realizedId;
            $realizedData['detail']    = json_encode($data);
            $realizedData['createdDate']= helper::now();
            $this->dao->insert(TABLE_DETAIL_REPORT)->data($realizedData)->exec();
        }

        return array_unique($useIDS);

    }

    /**
     * @Notes:需求任务外部反馈超期统计数据入库
     * @Date: 2023/10/17
     * @Time: 10:52
     * @Interface realizedRequirementMonthOutside
     * @param $realizedId
     * @param $realizedInfo
     */
    public function realizedRequirementMonthOutside($realizedId,$realizedInfo)
    {
        $realizedData= array();
        $deptArr     = array();
        $jsonRealizedData   = array();
        $useIDS = [];
        $requirementInfo = [];
        $secondmonthreportModel = $this->loadModel('secondmonthreport');
        $deptParent = $this->loadModel('dept')->getDeptAndChild();

        $needShowDeptList = $secondmonthreportModel->getNeedShowDept();

        foreach ($realizedInfo as $realizedValue)
        {
            if(!$realizedValue->dept){
                $realizedValue->dept = -1;
            }
//            if(in_array($realizedValue->dept,$needStaticDeptList)) {
                $deptArr[$deptParent[$realizedValue->dept]][] = $realizedValue;
//            }
        }

        //因不需要补齐部门，此判断可保留
        if(!empty($deptArr))
        {
            foreach ($deptArr as $i => $item)
            {
                $overdueNum  = 0;
                if(empty($i)) continue;
                $total = count($item);//反馈单总数
                foreach ($item as $v)
                {
                    if($v->feekBackStartTimeOutside == '0000-00-00 00:00:00') $v->feekBackStartTimeOutside = '';
                    if($v->innovationPassTime == '0000-00-00 00:00:00') $v->innovationPassTime = '';
                    //内部反馈超期数  && $v->feedbackOver != 1(外部反馈超期不剔除【是否纳入反馈超期】为否的数据)
                    if($v->ifOverTimeOutSide == 2)
                    {
                        $overdueNum++;
//                        $requirementInfo[$i][] = ['code'=>$v->code, 'system'=>$v->app, 'acceptTime'=>$v->feekBackStartTimeOutside,'feedbackDate'=>$v->innovationPassTime];
                        $useIDS[] = $v->id;
                    }


                }

                if(!empty($total))
                {
                    $overdueRate = $overdueNum/$total* 100;//超期率 合计/条目总数
                }else{
                    $overdueRate = 0;
                }
                $jsonRealizedData[$i]['deptID']          = $i;
                $jsonRealizedData[$i]['total']           = $total;
                $jsonRealizedData[$i]['foverdueNum']           = $overdueNum;
                $jsonRealizedData[$i]['overdueRate']     = number_format($overdueRate,2);


                /*if(isset($requirementInfo[$i])){
                    $jsonRealizedData[$i]['requirementCode'] = $requirementInfo[$i];
                }else{
                    $jsonRealizedData[$i]['requirementCode'] = [];
                }*/

            }
        }
        //具体单子数据的统计表不需要补齐部门。
    //补齐部门数据
        foreach ($needShowDeptList as $showDept){
            if(!isset($jsonRealizedData[$showDept])){

                $jsonRealizedData[$showDept] = [
                    'deptID'=>$showDept,
                    'total'=>0,
                    'foverdueNum'=>0,
                    'overdueRate'=>"0.00"
                ];
            }
        }

        //剔除 不是统计部门中部门数据为 0 的数据
        foreach ($jsonRealizedData as $dept=>$dataArr){
            if(!$dataArr['total'] && !in_array($dept,$needShowDeptList)){
                unset($jsonRealizedData[$dept]);
            }
        }
        //构造入库数据
//        arsort($jsonRealizedData);
        foreach ($jsonRealizedData as $key => $data)
        {
            $realizedData['deptID']    = $data['deptID'];
            $realizedData['tableType'] = 'requirement_outside';
            $realizedData['wholeID']   = $realizedId;
            $realizedData['detail']    = json_encode($data);
            $realizedData['createdDate']= helper::now();
            $this->dao->insert(TABLE_DETAIL_REPORT)->data($realizedData)->exec();
        }

        return array_unique($useIDS);

    }

    public function getIsExceed($demand, $publishedTime)
    {
        $demand->isExceed = '否';
        if(!empty($publishedTime) && $publishedTime != '0000-00-00' && $publishedTime != '0000-00-00 00:00:00')
        {
            /*
             * ①需求条目的“实现方式”为二线实现
             * ②所属需求任务的“实现方式”仅为二线实现
             * ③且“流程状态”上线成功、已关闭、已交付
             * ④剔除“是否纳入交付超期”为“否”的
             * ⑤除去延期审批通过的需求单
             */
            $flag             = $demand->fixType == 'second' && $demand->actualMethod == 'second' && $demand->isExtended != 1 && $demand->delayStatus != 'success';
            $twoMonthsEndTime = $this->loadModel('secondmonthreport')->getOverDate($publishedTime,2) . ' ' . date("H:i:s",strtotime($publishedTime));
            if($flag && $twoMonthsEndTime <= $demand->solvedTime && in_array($demand->status,$this->lang->demand->realizedArr)) {
                $demand->isExceed = '是';
            }
            if($flag && $twoMonthsEndTime <= helper::now() && in_array($demand->status,$this->lang->demand->unrealizedArr)) {
                $demand->isExceed = '是';
            }
        }

        return $demand;
    }

    /**
     * @Notes:二线需求条目交付是否超期状态修改
     * @Date: 2024/4/11
     * @Time: 15:13
     * @Interface updateDemandDeliveryOver
     */
    public function updateDemandDeliveryOver()
    {
        /* @var problemModel $problemModel*/
        $problemModel = $this->loadModel('problem');
        $demandInfo = $this->dao->select('`id`,`solvedTime`,`status`,`delayStatus`, `requirementID`')
            ->from(TABLE_DEMAND)
            ->where('sourceDemand')->eq('1')
            ->andWhere('status')->notIN('deleted,deleteout')
            ->andWhere('fixType')->eq('second')
            ->fetchAll();
        $outTime   = $this->lang->demand->demandOutTime['demandOutTime']  ?? 2; //交付超期月份
        $ids_yes = [];//超期数组
        $ids_no  = [];//未超期数组

        /*需求条目根据所属需求任务时间作计算*/
        foreach ($demandInfo as $demand)
        {
            if($demand->requirementID == 0) continue;
            $solvedTime = $demand->solvedTime != '0000-00-00 00:00:00' ? $demand->solvedTime : '';
            $requirement = $this->dao->select('id, createdBy, newPublishedTime, feedbackStatus, feekBackStartTime')
                ->from(TABLE_REQUIREMENT)
                ->where('id')->eq($demand->requirementID)
                ->andWhere('status')->notIN('deleted,deleteout')
                ->fetch();

            if($requirement)
            {
                /*
                 * ①清总同步按照内部反馈开始时间计算
                 * ②内部自建按照交付周期计算起始时间计算
                 */
                if('guestcn' == $requirement->createdBy){
                    $newPublishedTime = $requirement->feekBackStartTime != '0000-00-00 00:00:00' ? $requirement->feekBackStartTime : '';
                }else{
                    $newPublishedTime = $requirement->newPublishedTime != '0000-00-00 00:00:00' ? $requirement->newPublishedTime : '';
                }

                $his = substr($newPublishedTime, 10);
                $end   = $problemModel->getOverDate($newPublishedTime, $outTime); //超期时间
                $end   = date('Y-m-d', strtotime($end)).$his;

                //无交付时间用当前时间作对比
                $demandId = $demand->id;
                if(empty($solvedTime))
                {
                    if(helper::now() > $end){
                        $ids_yes[] = $demandId;
                    }else{
                        $ids_no[] = $demandId;
                    }
                }else{
                    if($solvedTime > $end){
                        $ids_yes[] = $demandId;
                    }else{
                        $ids_no[] = $demandId;
                    }
                }
            }


        }
        if(!empty($ids_yes))  $this->dao->update(TABLE_DEMAND)->set('deliveryOver')->eq(2)->where('id')->in($ids_yes)->exec();
        if(!empty($ids_no))   $this->dao->update(TABLE_DEMAND)->set('deliveryOver')->eq(1)->where('id')->in($ids_no)->exec();

    }


}
