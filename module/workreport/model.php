<?php

class workreportModel extends model
{

    const WEEKLY_REPORT  = 1;    // 周工提醒
    const MONTH_REPORT   = 2;    // 月报工提醒

    /**
     * 新建报工
     * @return array|string
     */
    public function create($type = null){

        $data = $data = fixer::input('post')
            ->get();

        $i = 1; //数据下标
        $everyTotal = array();
        $this->app->loadConfig('task');
        $projects = array(''=>'') + $this->getProjectTeam('',$type ? 'suppend' : 'new',1);//有权限的项目
        //每一条报工都需要验证是否符合要求
        foreach ($data->id as $key => $value){
            foreach ($this->lang->workreport->createRequired as $parms => $desc){
                //如果所有必填项都为空，则本条数据跳过，不验证
                if((empty($data->project[$key]) && empty($data->activity[$key]) && empty($data->apps[$key]) && empty($data->objects[$key])   && empty($data->consumed[$key]) && empty($data->workType[$key])) && (!empty($data->beginDate[$key])|| empty($data->beginDate[$key]))){
                    unset($data->id[$key],$data->project[$key],$data->activity[$key],$data->apps[$key],$data->objects[$key],$data->beginDate[$key],$data->consumed[$key],$data->workType[$key],$data->workContent[$key]);//从数组中去掉
                    continue;
                }

                //非空判断
                if($data->$parms[$key] == '' or empty($data->$parms[$key]) )
                {
                    return dao::$errors[] = sprintf($this->lang->workreport->emptyObject, $i,$desc);
                }
                //项目是否关闭
                if(!in_array($data->project[$key],array_keys($projects))){
                    return dao::$errors[] = sprintf($this->lang->workreport->projectTip, $i,$desc);
                }
                //验证填报日期是否在允许时间内
                $endDate                = $this->loadModel('review')->getCloseDate($data->project[$key]);
                $beginAndEnd =  $this->getBeginAndEnd($data->project[$key],isset($endDate->closeDate) ? $endDate->closeDate : '');
                $beginDate = $beginAndEnd->begin;//补报窗口时间
                if(isset($beginDate) && strpos($beginDate,'0000-00-00') == false && $type){
                    if(strtotime($data->beginDate[$key]) < strtotime($beginDate) || strtotime($data->beginDate[$key]) > strtotime(helper::now())){
                        return dao::$errors[] = sprintf($this->lang->workreport->beginDateTip, $i,$data->beginDate[$key]);
                    }
                }else{
                    $start = $this->getCreateBeginAndEnd(); //正常窗口时间
                    if(strtotime($data->beginDate[$key]) < strtotime($start->begin) || strtotime($data->beginDate[$key]) > strtotime(helper::now())){
                        return dao::$errors[] = sprintf($this->lang->workreport->beginDateTip, $i,$data->beginDate[$key]);
                    }
                }
                //结束时间不能小于开始时间
               /* if(($data->beginDate[$key] && $data->endDate[$key]) && strtotime($data->endDate[$key]) < strtotime($data->beginDate[$key])){
                    return dao::$errors[$data->endDate[$key]] = sprintf($this->lang->workreport->endTips, $i);
                }*/
                //是否整周判断
                /*$date = strtotime($data->beginDate[$key]);//日期
                $beginDate = date('Y-m-d',strtotime('this week',$date));
                $endDate = date('Y-m-d',(strtotime('next week',$date) - 1));
                if($date > strtotime($endDate) || $date < strtotime($beginDate) ){
                    return dao::$errors[$data->beginDate[$key]] = sprintf($this->lang->workreport->errorTips,$i);
                }*/
                $consumed = $data->consumed[$key];

                $reg = '/^(([1-9][0-9]*)|(([0]\.\d{1}|[1-9][0-9]*\.\d{1})))$/';
                if (!preg_match($reg, $consumed)) {
                    return dao::$errors[] =  sprintf($this->lang->workreport->consumedTip,$i);
                }
                //日期一样的工时累加
                $everyTotal[$data->beginDate[$key]][$key] = $data->consumed[$key];
            }
            $i++;
        }

        //检测每天工时是否超出正常范围
        foreach ($everyTotal as $key => $item) {
            $total = array_sum($item);
            $res = $this->checkEffort($total, $key);
             if ($res) {
                 $year  = date('Y',strtotime($key));
                 $month = date('m',strtotime($key));
                 $day   = date('d',strtotime($key));
                 return dao::$errors['consumed'] = sprintf($this->lang->workreport->thresholdError,$year, $month,$day,$this->config->task->workThreshold);
             }
        }
        if(empty($data->id)) return dao::$errors[] = $this->lang->workreport->emptyTips;
        $workIDs = array();
        //查询当前用户历史报工最新的一次版本
        $version = $this->dao->select('max(versions) versions')->from(TABLE_WORKREPORT)
            ->where('account')->eq($this->app->user->account)
            ->andWhere('deleted')->eq(0)
            ->fetch();
        foreach ($data->id as $key => $value){
           // if(!$data->project[$key]); continue;

            //拼装入库
            $work = new stdClass();
            $work->project    = $data->project[$key];
            $work->activity   = $data->activity[$key];
            $work->apps       = $data->apps[$key];
            $work->objects    = $data->objects[$key];
            $work->beginDate  = $data->beginDate[$key];
           // $work->endDate    = $data->endDate[$key];
            $work->consumed   = $data->consumed[$key];
            $work->workType   = $data->workType[$key];
            $work->workContent = $data->workContent[$key];
            $work->account    = $this->app->user->account;
            //$work->createTime = helper::now();
            $work->versions = $version ? $version->versions + 1 : 1;
            $work->weeklyNum = date('W',strtotime($data->beginDate[$key]));
            $work->append    = $type ? '1' :'0'; //是否补报
            $this->dao->insert(TABLE_WORKREPORT)->data($work)->autoCheck()->exec();//存报工表

            $workID = $this->dao->lastInsertID();
            $workIDs[] = $workID;

            $effort = new stdClass();
            $effort->date     = $data->beginDate[$key];// helper::today();
            $effort->consumed =  $data->consumed[$key];
            $effort->work     = $data->workContent[$key];
           /* $effort->beginDate  =  $data->beginDate[$key];
            $effort->endDate    = $data->endDate[$key];*/
            $effort->workID     = $workID;
            $this->effort($data->objects[$key],$effort);//任务报工存工时表

        }
        return $workIDs;
    }

    /**
     * 更新报工
     * @param $workID
     * @return string
     */
    public function update($workID,$type = null)
    {

        $oldWork = $this->getByID($workID);
        $data = $data = fixer::input('post')
            ->get();
        if(isset($oldWork) && $oldWork->account != $this->app->user->account && $this->app->user->account != 'admin'){
            return dao::$errors[] = sprintf($this->lang->workreport->noOwnerTip);
        }
        $i = 1; //数据下标
        $everyTotal = array();
        $this->app->loadConfig('task');
        $projects = array(''=>'') + $this->getProjectTeam('',$oldWork->append ? 'suppend' :'new',1);//有权限的项目
        //每一条报工都需要验证是否符合要求
        foreach ($data->id as $key => $value) {
            foreach ($this->lang->workreport->createRequired as $parms => $desc) {
                //如果所有必填项都为空，则本条数据跳过，不验证
                if (empty($data->project[$key]) && empty($data->activity[$key]) && empty($data->apps[$key]) && empty($data->objects[$key]) && empty($data->beginDate[$key])  && empty($data->consumed[$key]) && empty($data->workType[$key])) {
                    unset($data->id[$key], $data->project[$key], $data->activity[$key], $data->apps[$key], $data->objects[$key], $data->beginDate[$key], $data->consumed[$key], $data->workType[$key], $data->workContent[$key]);//从数组中去掉
                    continue;
                }
                //非空判断
                if ($data->$parms[$key] == '' or empty($data->$parms[$key])) {
                    return dao::$errors[] = sprintf($this->lang->workreport->emptyObject, $i, $desc);
                }
                //项目是否关闭
                if(!in_array($data->project[$key],array_keys($projects))){
                    return dao::$errors[] = sprintf($this->lang->workreport->projectTip, $i,$desc);
                }
                //对填报时间二次验证
                if(!$type){
                    $start = $this->getCreateBeginAndEnd(); //正常窗口时间
                    if(strtotime($data->beginDate[$key]) < strtotime($start->begin) || strtotime($data->beginDate[$key]) > strtotime(helper::now())){
                        return dao::$errors[] = sprintf($this->lang->workreport->beginDateTip, $i,$data->beginDate[$key]);
                    }
                }
                //结束时间不能小于开始时间
               /* if (($data->beginDate[$key] && $data->endDate[$key]) && strtotime($data->endDate[$key]) < strtotime($data->beginDate[$key])) {
                    return dao::$errors[$data->endDate[$key]] = sprintf($this->lang->workreport->endTips, $i);
                }*/

                //是否整周判断
              /*  $date = strtotime($data->beginDate[$key]);//日期
                $beginDate = date('Y-m-d',strtotime('this week',strtotime($data->beginDate[$key])));
                $endDate = date('Y-m-d',(strtotime('next week',strtotime($data->beginDate[$key])) - 1));
                if(strtotime($date) > strtotime($endDate) || strtotime($date) < strtotime($beginDate) ){
                    return dao::$errors[$data->beginDate[$key]] = sprintf($this->lang->workreport->errorTips,$i);
                }*/
                $consumed = $data->consumed[$key];

                $reg = '/^(([1-9][0-9]*)|(([0]\.\d{1}|[1-9][0-9]*\.\d{1})))$/';
                if (!preg_match($reg, $consumed)) {
                    return dao::$errors[] =  sprintf($this->lang->workreport->consumedTip,$i);
                }
                //日期一样的工时累加
                $everyTotal[$data->beginDate[$key]][$key] = $data->consumed[$key];
            }
            $i++;
        }
        //检测每天工时是否超出正常范围
        foreach ($everyTotal as $key => $item) {
            $total = array_sum($item);
            $res = $this->checkEffort($total, $key, $workID);
            if ($res) {
                $year  = date('Y',strtotime($key));
                $month = date('m',strtotime($key));
                $day   = date('d',strtotime($key));
                return dao::$errors[] = sprintf($this->lang->workreport->thresholdError,$year, $month,$day,$this->config->task->workThreshold);
            }
        }
        if (empty($data->id)) return dao::$errors[] = $this->lang->workreport->emptyTips;
        $workIDs = array();
        //查询当前用户历史报工最新的一次版本
        /*$version = $this->dao->select('max(versions) versions')->from(TABLE_WORKREPORT)
            ->where('account')->eq($this->app->user->account)
            ->fetch();*/
        foreach ($data->id as $key => $value) {
            // if(!$data->project[$key]); continue;

            //拼装入库
            $work = new stdClass();
            $work->project = $data->project[$key];
            $work->activity = $data->activity[$key];
            $work->apps = $data->apps[$key];
            $work->objects = $data->objects[$key];
            $work->beginDate = $data->beginDate[$key];
           // $work->endDate = $data->endDate[$key];
            $work->consumed = $data->consumed[$key];
            $work->workType = $data->workType[$key];
            $work->workContent = $data->workContent[$key];
            $work->editedBy = $this->app->user->account;
            $work->editTime = helper::now();
            $work->weeklyNum = date('W',strtotime($data->beginDate[$key]));

            $this->dao->update(TABLE_WORKREPORT)->data($work)->autoCheck()
                ->where('id')->eq($workID)->exec();

            $workIDs[] = $workID;
            $effort = new stdClass();
            $effort->date = $data->beginDate[$key];//helper::today();
            $effort->project = $work->project;
            $effort->execution = $work->apps;
            $effort->consumed = $data->consumed[$key];
            $effort->work = $data->workContent[$key];
            $effort->objectID = $data->objects[$key];
           // $effort->beginDate = $data->beginDate[$key];
            //$effort->endDate = $data->endDate[$key];
            $effort->workID = $workID;

            $effrotID = $this->dao->select('id')->from(TABLE_EFFORT)->where('workID')->eq($workID)->fetch();
            $this->dao->update(TABLE_EFFORT)->data($effort)->autoCheck()
                ->where("id")->eq($effrotID->id)
                ->exec(); //任务报工更新工时表

            //编辑时可能所有项都会修改，比较新旧数据，查看关键项是否被修改
            if($oldWork->project != $work->project || $oldWork->activity !=  $work->activity || $oldWork->apps != $work->apps || $oldWork->objects !=  $work->objects){
                //有一项不一致，则更新原来的任务工作量相关
               /* $this->dao->update(TABLE_EFFORT)->set('deleted')->eq('1')
                    ->where("id =(select id from (select id from zt_effort where objectID = '$oldWork->objects' and objectType ='task')t1 )")->printSQL();
                $this->loadModel('action')->create('effort', $effrotID->id, 'DeleteEstimate', '');*/
                $this->loadModel('task')->computeTask($oldWork->objects);
                $this->loadModel('task')->computeConsumed($oldWork->objects);
		$this->loadModel('action')->create('effort', $oldWork->objects, 'DeleteEstimate', $this->lang->workreport->editConsumed );
            }
                //关键项都没变 ，只是时间、工时、工作内容变
                $this->loadModel('task')->computeTask($data->objects[$key]);
                $this->loadModel('task')->computeConsumed($data->objects[$key]);

            $this->loadModel('action')->create('effort', $effrotID->id, 'edited', '');


            $change = common::createChanges($oldWork, $work);
        }
        return $change;
    }

    /**
     * 查询列表
     * @return array
     */
    public function getList($browseType, $queryID,$pager = null,$begin = null ,$end = null){

        $workreportQuery = '';
        if($browseType == 'bysearch')
        {
            $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : '';
            if($query)
            {
                $this->session->set('workreportQuery', $query->sql);
                $this->session->set('workreportForm', $query->form);
            }

            if($this->session->workreportQuery == false) $this->session->set('workreportQuery', ' 1 = 1');
            $workreportQuery = $this->session->workreportQuery;
            if(strpos($workreportQuery,'project')){
                $workreportQuery = str_replace('AND `project', ' AND `t2.project', $workreportQuery);
                $workreportQuery = str_replace('`', '', $workreportQuery);
            }
            if(strpos($workreportQuery,'beginDate')){
                $workreportQuery = str_replace('AND `beginDate', ' AND `t1.date', $workreportQuery);
                $workreportQuery = str_replace('`', '', $workreportQuery);
            }
        }

        // 搜索年度
        if($browseType != 'all' && $browseType != 'bysearch' && $browseType != 'date'){
            //本年开始时间
            $yearStart = date('Y-01-01',strtotime($browseType.'-01-01'));
            //本年结束时间
            $yearEnd = date('Y-12-31',strtotime($browseType.'-01-01'));
            $this->session->set('workreportYear', $browseType);
        }else if($browseType == 'all' || $browseType == 'date'){
            $this->session->set('workreportYear', '');
        }
        $workreportYear = $this->session->workreportYear;
        if($workreportYear && $browseType == 'bysearch'){
            //本年开始时间
            $yearStart = date('Y-01-01',strtotime($workreportYear.'-01-01'));
            //本年结束时间
            $yearEnd = date('Y-12-31',strtotime($workreportYear.'-01-01'));
        }


        $yearStart = isset($yearStart) ? $yearStart : date('Y-m-01',strtotime(helper::today()));
        $yearEnd = isset($yearEnd) ? $yearEnd : date('Y-12-31',strtotime(helper::today()));
        $start = date('Y-m-d',strtotime(helper::today()));
        //本月开始时间
        $monthStart = date('Y-m-01',strtotime($start));
        //本月的第一周开始时间
       // $monthOneWeekly = $this->get_monthOneWeekly(date('Y-m',strtotime($monthStart)));
        //本月结束时间
        $monthend = date('Y-m-d',(strtotime("$monthStart+1 month -1 day") ));

        //默认查询当前月
        $res = $this->dao->select("t2.*,t1.consumed as effortConsumed")->from(TABLE_EFFORT)->alias('t1')
            ->leftJoin(TABLE_WORKREPORT)->alias('t2')
            ->on('t2.id = t1.workID')
            ->where('t1.deleted')->eq(0)
            ->andWhere('t1.objectType')->eq('task')
            ->beginIF((($browseType == 'all' )  && empty($workreportYear)))->andWhere('t1.date')->ge($monthStart)->andWhere('t1.date')->le($monthend)->fi()
            ->beginIF($browseType != 'all' && $browseType != 'bysearch' && $browseType != 'date')->andWhere('t1.date')->ge($yearStart)->andWhere('t1.date')->le($yearEnd)->fi()
            ->beginIF($browseType == 'bysearch' && !empty($workreportYear) )->andWhere('t1.date')->ge($yearStart)->andWhere('t1.date')->le($yearEnd)->andWhere($workreportQuery)->fi()
            ->beginIF($browseType == 'date' )->andWhere('t1.date')->ge(date('Y-m-d',strtotime($begin)))->andWhere('t1.date')->le(date('Y-m-d',strtotime($end)))->fi()
            ->beginIF($this->app->user->account != 'admin')->andWhere('t1.account')->eq($this->app->user->account)->fi()
            ->beginIF($browseType == 'bysearch' && empty($workreportYear))->andWhere($workreportQuery)->fi()
            ->andWhere('t2.deleted')->eq(0)
            ->fetchAll();
        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'workreport', $browseType != 'bysearch');
        $newRes = array();
        foreach ($res as $re) {
            $newRes[date('Y-m-d',strtotime($re->beginDate))][] = $re;
        }
        $allweeks =array();
        if($browseType != 'all' && $browseType != 'bysearch' && $browseType != 'date'){
            $weeklys = $this->get_monthDays($browseType,true);
        }else if($workreportYear && $browseType == 'bysearch'){
            $browseType = $workreportYear;
            $weeklys = $this->get_monthDays($browseType,true);
        }else{
           if($browseType == 'date'){
               $weeklys = $this->selectMonthDays($begin,$end);
           }else{
               $weeklys = $this->get_monthDays(date('Y-m',strtotime($monthStart)));
           }
        }

        //每个月
            foreach ($weeklys as $item) {
                $weeks =array();
                $week = new stdClass();
                $weeks['weekNum'] = $item['weeklyNum'];//sprintf($this->lang->workreport->weeklyNumTip, $item['weeklyNum']).'<br>'. $item['weeklyStart'].'-'. $item['weeklyEnd'] ;//周数
                //拼装每周报工
                if(!isset($newRes[$item['weeklyNum']])){
                    $week->id             = '';
                    $week->project        = '';
                    $week->activity       = '';
                    $week->apps           = '';
                    $week->objects        = '';
                    $week->beginDate      = '';;
                    //$week->endDate        = '';
                    $week->consumed       = '';
                    $week->workType       = '';
                    $week->workContent    = '';
                    $week->canEditAndDelete = false;
                    $weeks['effort'][] = $week;
                    $week->append     = '';
                }else{
                    foreach ($newRes[$item['weeklyNum']] as $newRe) {

                        $week = new stdClass();
                        $week->id             = $newRe->id;
                        $week->project        = $newRe->project;
                        $week->activity       = $newRe->activity;
                        $week->apps           = $newRe->apps;
                        $week->objects        = $newRe->objects;
                        $week->beginDate      =  date('Y-m-d',strtotime($newRe->beginDate));
                        //$week->endDate        =  date('Y-m-d',strtotime($newRe->endDate));
                        $week->consumed       = $newRe->consumed;
                        $week->workType       = $newRe->workType;
                        $week->workContent    = $newRe->workContent;
                        $week->canEditAndDelete   =  $newRe->source == 2 ? false : $this->checkEditAndDelete(date('Y-m-d',strtotime($newRe->beginDate)),$newRe->append,$newRe->project); //现场支持的数据不能编辑删除
                        $week->append         = $newRe->append;
                        $weeks['effort'][] = $week;
                        $weeks['total'][]  = $newRe->consumed;
                        $week->taskName    = $this->loadModel('task')->getByID($newRe->objects);;
                    }
                }
                $allweeks[] = $weeks;
            }

            $pager->recTotal = count($allweeks);
            $start = ($pager->pageID -1) * $pager->recPerPage ;
            $arr = array_slice($allweeks,$start,$pager->recPerPage);
            if(!empty($workreportYear) && !isset($begin) && !isset($end)){
                $arr['total'] = $this->getTotalConsuemd($workreportYear,'','',$workreportQuery);
            }else if(isset($begin) && isset($end) && $begin){
                $arr['total'] =  $this->getTotalConsuemd($workreportYear,$begin,$end) ;
            }else{
                $arr['total'] = $this->getTotalConsuemd($workreportYear,$monthStart,$monthend,$workreportQuery);
            }
        return $arr;
    }

    /**
     * @param $workID
     */
    public function getByID($workID){
        return $this->dao->select('*')->from(TABLE_WORKREPORT)->where('id')->eq($workID)->fetch();
    }

    /**查询已有报工年份
     * @return mixed
     */
    public function getAllYear(){
        return $this->dao->select('distinct(YEAR(beginDate)) year')->from(TABLE_WORKREPORT)->where('deleted')->eq(0)->fetchAll();
    }

    /**
     * 查询是否可以删除和编辑（T+3个工作日后不可操作）
     * @param $month
     * @return bool
     */
    public function checkEditAndDelete($month,$append,$project){
        $flag = false;
        $status = $this->dao->select('status')->from(TABLE_PROJECT)
            ->where('id')->eq($project)
            ->andWhere('deleted')->eq(0)
            ->fetch();
        if($status->status == 'closed' ||$status->status == 'suspended'){
            $flag = false;
        }else{
            if($append){
                $allow = $this->dao->select('allowBegin')->from(TABLE_PROJECT)
                    ->where('id')->eq($project)
                    ->andWhere('deleted')->eq(0)
                    ->fetch();
                if($allow->allowBegin != '0000-00-00' && !empty($allow->allowBegin)){
                    $flag = true;
                }
            }else{
                $this->app->loadConfig('task');
                $buffer = !empty($this->config->task->workBuffer) ? $this->config->task->workBuffer : 3;//预留工作日buffer(后台自定义无，默认3天)
                $now = date('Y-m-01',strtotime($month));//单子的月初
                //$nextMonth =  date('Y-m-01',strtotime('next month',strtotime($month)));//下个月
                $nextMonth =  date('Y-m-01',strtotime('next month',strtotime($now)));//下个月
                $endMax = helper::getTrueWorkDay($nextMonth,$buffer,true);//结束时间保留3个工作日的buffer
                //单子大于等于单子的当前月初 && 小于等于最大 buffer
                if(strtotime($month) >= $now && $month <= $endMax && helper::today() < $endMax){
                    $flag = true;
                }
            }
        }
        return $flag;
    }
    /**
     * 返回可操作报工时间区间
     * @param $projectID
     * @param $defalutEndDate 项目空间的评审关闭时间（二线和部门不会有评审）
     * @return stdClass
     */
    public function getBeginAndEnd($projectID,$defalutEndDate){
        //查询当前项目配置的开始时间。注意:结束时间没有评审关闭就永远取当前（当前结束时间页面已取消配置）
        $allow = $this->dao->select('allowBegin,allowEnd,name')->from(TABLE_PROJECT)
            ->where('id')->eq($projectID)
            ->andWhere('deleted')->eq(0)
            ->fetch();
        //二线管理和部门管理 不会有评审关闭时间
        if((strpos($allow->name,$this->lang->workreport->nameType['dept']) !== false || strpos($allow->name,$this->lang->workreport->nameType['second']) !== false) &&  empty($defalutEndDate)){
            $defalutEndDate =  helper::today();
        }
        $defalutEndDate = $defalutEndDate ?  $defalutEndDate : helper::today();
        $first = date('Y-m-01',strtotime($defalutEndDate));//月初
        $defalutMonth = date('Y-m',strtotime($defalutEndDate));//默认时间的月份
        $nowMonth = date('Y-m');//当前时间的月份
        $now = date('Y-m-01');//当前时间月初
        $diff = helper::diffDate3($nowMonth, $defalutMonth);
        $this->app->loadConfig('task');
        $buffer = !empty($this->config->task->workBuffer) ? $this->config->task->workBuffer : 3;//预留工作日buffer(后台自定义无，默认3天)
        $date = new stdClass();
        if(($allow->allowBegin != '0000-00-00' && !empty($allow->allowBegin)) && $diff > 1){
            //配置时间，当前日期 和默认日期比较，是否跨月，跨月
            //报工如果跨月，则结束时间为当前月 1号+ 三个工作日 ，开始时间为上个月1号；过三个工作日后，开始时间为本月1号，结束时间为当天
           /* if(helper::diffDate2($allow->allowBegin , $now) == '0'){
                $now =  date('Y-m-01',strtotime('last month',strtotime($now)));//跨月，当月1号，则开始获取上个月1号
            }
            $endMax = helper::getTrueWorkDay($now,$buffer,true);//结束时间，刚跨月。结束时间保留3个工作日的buffer（解决用户跨月后立即不能报上个月工时的问题）
            //判断当前日期 和最大的结束时间 ，如果当前时间大于 则开始时间从1号开始。结束时间取当天.反之开始时间从上月1号开始，结束时间取当天
            if( helper::diffDate3(date('Y-m-d',strtotime($endMax)),helper::today()) > 1){
                $end = helper::today();
            }else{
                $now = date('Y-m-01');//当前时间月初
                $end = helper::today();
            }*/
            $date->begin =  helper::diffDate2($allow->allowBegin , $now) == '0'   ?  $now : $allow->allowBegin;
            $date->end   =   helper::today();
            $date->flag   =  1;
        }else if(($allow->allowBegin != '0000-00-00' && !empty($allow->allowBegin)) && $diff == 1){
            //配置时间，当前日期 和默认日期比较，是否跨月，不跨月，则开始时间从默认日期 的当前月1号开始
            $date->begin =  helper::diffDate2($allow->allowBegin , $first) == '0'   ?  $first : $allow->allowBegin;
            $date->end   = helper::today(); //$allow->allowEnd;
            $date->flag   =  1;
        }else if(($allow->allowBegin == '0000-00-00' || empty($allow->allowBegin)) && $diff > 1){
            //未配置，跨月 则开始时间取当前月1号
            //报工如果跨月，则结束时间为当前月 1号+ 三个工作日 ，开始时间为上个月1号；过三个工作日后，开始时间为本月1号，结束时间为当天
            $now =  date('Y-m-01',strtotime('last month',strtotime($now)));//跨月，当月1号，则开始获取上个月1号
            $endMax = helper::getTrueWorkDay($now,$buffer,true);//结束时间，刚跨月。结束时间保留3个工作日的buffer（解决用户跨月后立即不能报上个月工时的问题）
            //判断当前日期 和最大的结束时间 ，如果当前时间大于 则开始时间从1号开始。结束时间取当天.反之开始时间从上月1号开始，结束时间取当天
            if( helper::diffDate3(date('Y-m-d',strtotime($endMax)),helper::today()) >= 1){
                $end = helper::today();
            }else{
                $now = date('Y-m-01');//当前时间月初
                $end = helper::today();
            }

            $date->begin = $now;
            $date->end   = $end;
            $date->flag   =  0;
        }else if(($allow->allowBegin == '0000-00-00' || empty($allow->allowBegin)) && $diff == 1 && (strpos($allow->name,$this->lang->workreport->nameType['dept']) == false && strpos($allow->name,$this->lang->workreport->nameType['second']) == false)){
            $endMax = helper::getTrueWorkDay($now,$buffer,true);//结束时间，刚跨月。结束时间保留3个工作日的buffer（解决用户跨月后立即不能报上个月工时的问题）
            //报工如果跨月，则结束时间为当前月 1号+ 三个工作日 ，开始时间为上个月1号；过三个工作日后，开始时间为本月1号，结束时间为当天
            $now =  date('Y-m-01',strtotime('last month',strtotime($now)));//跨月，当月1号，则开始获取上个月1号

            //判断当前日期 和最大的结束时间 ，如果当前时间大于 则开始时间从1号开始。结束时间取当天.反之开始时间从上月1号开始，结束时间取当天
            if( helper::diffDate3(date('Y-m-d',strtotime($endMax)),helper::today()) >= 1){
                $end = helper::today();
            }else{
                $now = date('Y-m-01');//当前时间月初
                $end = helper::today();
            }
            //未配置，不跨月 则开始时间不限制  项目
            $date->begin = $now;
            $date->end   = $end;
            $date->flag   =  0;
        }else if(($allow->allowBegin == '0000-00-00' || empty($allow->allowBegin)) && $diff == 1 && (strpos($allow->name,$this->lang->workreport->nameType['dept']) !== false || strpos($allow->name,$this->lang->workreport->nameType['second']) !== false)){
            $endMax = helper::getTrueWorkDay($now,$buffer,true);//结束时间，刚跨月。结束时间保留3个工作日的buffer（解决用户跨月后立即不能报上个月工时的问题）
            //报工如果跨月，则结束时间为当前月 1号+ 三个工作日 ，开始时间为上个月1号；过三个工作日后，开始时间为本月1号，结束时间为当天
            $now =  date('Y-m-01',strtotime('last month',strtotime($now)));//跨月，当月1号，则开始获取上个月1号

            //判断当前日期 和最大的结束时间 ，如果当前时间大于 则开始时间从1号开始。结束时间取当天.反之开始时间从上月1号开始，结束时间取当天
            if( helper::diffDate3(date('Y-m-d',strtotime($endMax)),helper::today()) >= 1){
                $end = helper::today();
            }else{
                $now = date('Y-m-01');//当前时间月初
                $end = helper::today();
            }
            //未配置，不跨月 则开始时间为当月1号  二线管理 部门管理
            $date->begin = $now;
            $date->end   = $end;
            $date->flag   =  0;
        }else if(($allow->allowBegin == '0000-00-00' || empty($allow->allowBegin)) && $diff < 0){
            //未配置，跨年跨月 则开始时间取当前月1号
            //报工如果跨月，则结束时间为当前月 1号+ 三个工作日 ，开始时间为上个月1号；过三个工作日后，开始时间为本月1号，结束时间为当天
            $now =  date('Y-m-01',strtotime('last month',strtotime($now)));//跨月，当月1号，则开始获取上个月1号

            $endMax = helper::getTrueWorkDay($now,$buffer,true);//结束时间，刚跨月。结束时间保留3个工作日的buffer（解决用户跨月后立即不能报上个月工时的问题）
            //判断当前日期 和最大的结束时间 ，如果当前时间大于 则开始时间从1号开始。结束时间取当天.反之开始时间从上月1号开始，结束时间取当天
            if( helper::diffDate3(date('Y-m-d',strtotime($endMax)),helper::today()) >= 1){
                $end = helper::today();
            }else{
                $now = date('Y-m-01');//当前时间月初
                $end = helper::today();
            }
            $date->begin = $now;
            $date->end   = $end;
            $date->flag   =  0;
        }
        return $date;
    }

    /**
     * 返回可操作报工时间区间
     * 1、所有项目都调整为只能报当月 2、下个有三个工作日内可报上月
     * @return stdClass
     */
    public function getCreateBeginAndEnd(){

        $defalutEndDate =  helper::today();
        $first = date('Y-m-01',strtotime($defalutEndDate));//月初
        $defalutMonth = date('Y-m',strtotime($defalutEndDate));//默认时间的月份
        $nowMonth = date('Y-m');//当前时间的月份
        $now = date('Y-m-01');//当前时间月初
        $diff = helper::diffDate3($nowMonth, $defalutMonth);
        $this->app->loadConfig('task');
        $buffer = !empty($this->config->task->workBuffer) ? $this->config->task->workBuffer : 3;//预留工作日buffer(后台自定义无，默认3天)
        $date = new stdClass();
         if($diff > 1){
            //未配置，跨月 则开始时间取当前月1号
            //报工如果跨月，则结束时间为当前月 1号+ 三个工作日 ，开始时间为上个月1号；过三个工作日后，开始时间为本月1号，结束时间为当天
            $now =  date('Y-m-01',strtotime('last month',strtotime($now)));//跨月，当月1号，则开始获取上个月1号
            $endMax = helper::getTrueWorkDay($now,$buffer-1,true);//结束时间，刚跨月。结束时间保留3个工作日的buffer（解决用户跨月后立即不能报上个月工时的问题）
            //判断当前日期 和最大的结束时间 ，如果当前时间大于 则开始时间从1号开始。结束时间取当天.反之开始时间从上月1号开始，结束时间取当天
            if( helper::diffDate3(date('Y-m-d',strtotime($endMax)),helper::today()) > 1){
                $end = helper::today();
            }else{
                $now = date('Y-m-01');//当前时间月初
                $end = helper::today();
            }
            $date->begin = $now;
            $date->end   = $end;
            $date->flag   =  0;
        }else if( $diff == 1 ){
            $endMax = helper::getTrueWorkDay($now,$buffer-1,true);//结束时间，刚跨月。结束时间保留3个工作日的buffer（解决用户跨月后立即不能报上个月工时的问题）
            //报工如果跨月，则结束时间为当前月 1号+ 三个工作日 ，开始时间为上个月1号；过三个工作日后，开始时间为本月1号，结束时间为当天
            $now =  date('Y-m-01',strtotime('last month',strtotime($now)));//跨月，当月1号，则开始获取上个月1号
            //判断当前日期 和最大的结束时间 ，如果当前时间大于 则开始时间从1号开始。结束时间取当天.反之开始时间从上月1号开始，结束时间取当天
            if( helper::diffDate3(date('Y-m-d',strtotime($endMax)),helper::today()) >= 1){
                //$now = date('Y-m-01');//当前时间月初
                $end = helper::today();
            }else{
                $now = date('Y-m-01');//当前时间月初
                $end = helper::today();
            }
            //未配置，不跨月 则开始时间不限制  项目
            $date->begin = $now;
            $date->end   = $end;
            $date->flag   =  0;
        }else if($diff < 0){
            //未配置，跨年跨月 则开始时间取当前月1号
            //报工如果跨月，则结束时间为当前月 1号+ 三个工作日 ，开始时间为上个月1号；过三个工作日后，开始时间为本月1号，结束时间为当天
            $now =  date('Y-m-01',strtotime('last month',strtotime($now)));//跨月，当月1号，则开始获取上个月1号
            $endMax = helper::getTrueWorkDay($now,$buffer-1,true);//结束时间，刚跨月。结束时间保留3个工作日的buffer（解决用户跨月后立即不能报上个月工时的问题）
            //判断当前日期 和最大的结束时间 ，如果当前时间大于 则开始时间从1号开始。结束时间取当天.反之开始时间从上月1号开始，结束时间取当天
            if( helper::diffDate3(date('Y-m-d',strtotime($endMax)),helper::today()) > 1){
                $end = helper::today();
            }else{
                $now = date('Y-m-01');//当前时间月初
                $end = helper::today();
            }
            $date->begin = $now;
            $date->end   = $end;
            $date->flag   =  0;
        }
        return $date;
    }

    /**
     * 查询当前用户历史报工最后一次，渲染页面
     * @return mixed
     */
    public function getLast(){
        $list = $this->dao->select('project')->from(TABLE_WORKREPORT)
            ->where("versions = (select max(versions) from zt_workreport where account = '{$this->app->user->account}' and deleted = 0)")
            ->andWhere('deleted')->eq(0)
            ->orderBy('id_asc')
            ->fetchAll();

        return $list;
    }
    /**
     * 存入任务工时表
     * @param $taskID
     * @param $effort
     */
    public function effort($taskID,$effort){

        $this->loadModel('effort');
        $this->loadModel('task');
        $task = $this->task->getByID($taskID);
        $left = $task->left;

        $efforts = array();
        $totalConsumed = array();

        $left -= $effort->consumed;

        $row = new stdclass();
        $row->date     = $effort->date;
        $row->consumed = $effort->consumed;
        $row->left     = $left;
        $row->work     = $effort->work;
       /* $row->beginDate   = $effort->beginDate;
        $row->endDate     = $effort->endDate;*/
        $row->workID      = $effort->workID;

        $efforts[] = $row;
        if(!isset($totalConsumed[$effort->date])) $totalConsumed[$effort->date] = 0;
        $totalConsumed[$effort->date] += $effort->consumed;

        foreach($totalConsumed as $consumedDate => $consumed)
        {
            $consumedToday = $this->loadModel('effort')->getWorkloadToday($this->app->user->account, $consumed, 'insert', $consumedDate);
        }

        $this->task->batchCreateEffort($taskID, $efforts);

    }
    /**
     * 查询用户在团队中的所属项目(只查询项目后台未配置自定义时间的)
     */
    public function getProjectTeam( $haveBeginDate =  null,$type = 'all',$status = null,$user = null){
        $team = $this->dao->select('concat(code,"_",name) as name,t1.root projectid')->from(TABLE_TEAM)->alias('t1')
            ->leftJoin(TABLE_PROJECT)->alias('t2')
            ->on('t1.root = t2.id')
            ->where('t2.type')->eq('project')
            ->beginIF($type =='new')->andWhere('t2.status')->ne('closed')->fi()
            ->beginIF($status)->andWhere('t2.switch')->eq('1')->fi() //报工开关
            ->beginIF($type != 'all' && empty($user))->andWhere('t1.account')->eq($this->app->user->account)->fi()
            ->beginIF($type != 'all' && !empty($user))->andWhere('t1.account')->eq($user)->fi()
            //->beginIF(empty($haveBeginDate) && $type != 'all' )->andWhere('t2.allowBegin')->isnull()->orWhere("!t2.allowBegin != '0000-00-00'")->fi()
            ->beginIF($haveBeginDate  && $type != 'all' )->andWhere('t2.allowBegin')->isNotnull()->andWhere("t2.allowBegin != '0000-00-00'")->fi()
            ->fetchPairs('projectid','name');
        return $team;
    }

    /**
     * 检测工时是否超出正常范围
     *
     * @param $effort
     * @param $begin
     * @param null $ignoreWorkID
     * @param string $ignoreType
     * @param $currentUser
     * @param $ignoreLocaleSupportId
     * @return bool
     */
    public function checkEffort($effort, $begin, $ignoreWorkID = null, $ignoreType = 'workreport', $currentUser = '', $ignoreLocaleSupportId = 0){
        $flag = false;
        $this->app->loadConfig('task');
        //目前默认每天最多可报14小时，每周最多可报98 小时（每天可报，后台自定义可修改）
      /*  $maxEffort = 7 * $this->config->task->workThreshold;
        $res = $this->dao->select("sum(t1.consumed) as total")->from(TABLE_EFFORT)->alias('t1')
            ->leftJoin(TABLE_WORKREPORT)->alias('t2')
            ->on('t2.objects = t1.objectID')
            ->where('t1.deleted')->eq(0)
            ->andWhere('t1.objectType')->eq('task')
            ->andWhere('t1.beginDate')->ge($weeklyStart)
            ->andWhere('t1.endDate')->le($weeklyend)
            ->andWhere('t1.account')->eq($this->app->user->account)
            ->fetch();
        $effort = $effort ? $effort : 0;
        $oldEffort = isset($res->total) ? $res->total : 0;
        $nowTotal = $oldEffort + $effort;
        //上报总工时不能超过一周总工时
        if($maxEffort < $nowTotal){
            $flag = 'totalError';
        }else{*/
       //查询每天平均工时是否超
       //实际工期M天
       //$diffDate = helper::diffDate2($begin, $end, false);
        //阈值
        $threshold = $this->config->task->workThreshold;
        if(!$effort){
            $effort = 0;
        }
        if($effort > $threshold){
            $flag = true;
            return $flag;
        }
        if(!$currentUser){
            $currentUser = $this->app->user->account;
        }
        $ret = $this->dao->select("sum(t1.consumed) as total")->from(TABLE_EFFORT)->alias('t1')
            ->leftJoin(TABLE_WORKREPORT)->alias('t2')
            ->on('t2.id = t1.workID')
            ->where('t1.deleted')->eq(0)
            ->andWhere('t1.objectType')->eq('task')
            ->andWhere('t1.date')->eq($begin)
            ->beginIF($ignoreType == 'workreport' && $ignoreWorkID)->andWhere('t2.id')->ne($ignoreWorkID)->fi()
            ->andWhere('t1.account')->eq($currentUser)
            ->fetch();
        if($ret){
            $effort += $ret->total;
            if($effort > $threshold){
                $flag = true;
                return $flag;
            }
        }
        $ret = $this->dao->select('sum(consumed) as  total')
            ->from(TABLE_LOCALESUPPORT_WORKREPORT)->alias('t1')
            ->leftJoin(TABLE_LOCALESUPPORT)->alias('t2')
            ->on('t2.id = t1.supportId')
            ->where('t1.deleted')->eq('0')
            ->andWhere('t2.deleted')->eq('0')
            ->andWhere('t1.supportUser')->eq($currentUser)
            ->andWhere('t1.supportDate')->eq($begin)
            ->andWhere('t1.syncStatus')->eq('1')
            ->beginIF(($ignoreType == 'localesupport') && ($ignoreWorkID > 0))->andWhere('t1.id')->ne($ignoreWorkID)->fi()
            ->beginIF($ignoreLocaleSupportId > 0)->andWhere('t1.supportId')->ne($ignoreLocaleSupportId)->fi()
            ->fetch();
        //echo $this->dao->get();
        if($ret){
            $effort += $ret->total;
        }
        if($effort > $threshold){
            $flag = true;
            return $flag;
        }
        return $flag;
    }

    /**
     * 统计用户年度工作量
     * @param $year
     * @return int
     */
    public function getTotalConsuemd($year,$begin = null,$end = null,$where = null ){
        $res = $this->dao->select("sum(t1.consumed) as total")->from(TABLE_EFFORT)->alias('t1')
            ->leftJoin(TABLE_WORKREPORT)->alias('t2')
            ->on('t2.id = t1.workID')
            ->where('t1.deleted')->eq(0)
            ->andWhere('t1.objectType')->eq('task')
            ->beginIF(empty($begin) && empty($end) && !$where)->andWhere('t1.date')->like("$year%")->fi()
            ->beginIF(empty($begin) && empty($end) && $where)->andWhere('t1.date')->like("$year%")->andWhere($where)->fi()
            ->beginIF($begin && $end)->andWhere('t1.date')->ge($begin)->andWhere('t1.date')->le($end)->fi()
            ->beginIF($begin && $end && $where)->andWhere('t1.date')->ge($begin)->andWhere('t1.date')->le($end)->andWhere($where)->fi()
            ->beginIF($this->app->user->account !='admin')->andWhere('t1.account')->eq($this->app->user->account)->fi()
            ->andWhere('t1.workID')->ne(0)
            ->fetch();
        return isset($res->total) ? round($res->total,2) : 0;
    }
    /**
     * Send mail.
     *
     * @access public
     * @return void
     */
    public function sendmail($flag)
    {
        $this->loadModel('mail');
        $user = $this->loadModel('user')->getList();
        $user = array_combine(array_column($user,'account'),array_column($user,'dept'));
        //以下领导不发邮件
        $noEmail = explode(',',$this->lang->workreport->leaderList['userList']);//array('luoyongzhong','zhujianqiang','hetielin','muxiaotian','zhulina');
        $depts = explode(',',$this->lang->workreport->deptList['depts']); //接收邮件部门
        foreach ($user as $key => $item) {
            if(in_array($key,$noEmail)) continue; // 不接收邮件领导
            if(!in_array($item,$depts)) continue; //不接收邮件部门
            $start = date('Y-m-d',strtotime(helper::today()));
            //本周
            if($flag == workreportModel::WEEKLY_REPORT){
                //本周开始时间（周一）
                $beginDate = date('Y-m-d',strtotime('this week',strtotime($start)));
                //本周结束时间（周日）
                $endDate = date('Y-m-d',(strtotime('next week',strtotime($start)) - 1));

            }else if($flag == workreportModel::MONTH_REPORT){

                //本月
                $one_day =strtotime(date('Y-m-01',strtotime($start)));//本月1号
                $beginDate =  date('Y-m-01',strtotime('last month',strtotime($start)));//获取上个月1号
                $endDate = date('Y-m-d',strtotime('-1 days',$one_day));//上个月最后一天

                $year = date('Y',strtotime($beginDate));
                $month = date('m',strtotime($beginDate));
            }
            //查询当前用户本周内报工
            $total = $this->dao->select('sum(consumed) allConsumed')->from(TABLE_EFFORT)
                ->where('account')->eq($key)
                ->andWhere('date')->ge($beginDate)
                ->andWhere('date')->le($endDate)
                ->andWhere('deleted')->eq(0)
                ->andWhere('objectType')->eq('task')
                ->fetch();

            /* 获取后台通知中配置的邮件发信。*/
            $this->app->loadLang('custommail');
            if($flag == workreportModel::WEEKLY_REPORT){
                $mailConf   = isset($this->config->global->setWorkReportWeeklyMail) ? $this->config->global->setWorkReportWeeklyMail : '{"mailTitle":"","variables":[],"mailContent":""}';
            }else{
                $mailConf   = isset($this->config->global->setWorkReportMonthMail) ? $this->config->global->setWorkReportMonthMail : '{"mailTitle":"","variables":[],"mailContent":""}';
            }

            $mailConf   = json_decode($mailConf);

            /* 处理邮件发信的标题和日期。*/
           /* $bestDeal = $this->dao->select('*')->from(TABLE_CONSUMED)->where('objectType')->eq('secondorder')
                ->andWhere('objectID')->eq($workReportID)
                ->orderBy('id_desc')
                ->fetch();
            $bestDate  = empty($bestDeal) ? '' : substr($bestDeal->createdDate, 0, 10);*/
            $mailTitle = vsprintf($mailConf->mailTitle, $mailConf->variables);


            /* Get mail content. */
            $oldcwd     = getcwd();
            $modulePath = $this->app->getModulePath($appName = '', 'workreport');
            $viewFile   = $modulePath . 'view/sendmail.html.php';
            chdir($modulePath . 'view');

            if(file_exists($modulePath . 'ext/view/sendmail.html.php'))
            {
                $viewFile = $modulePath . 'ext/view/sendmail.html.php';
                chdir($modulePath . 'ext/view');
            }

            ob_start();
            include $viewFile;
            foreach(glob($modulePath . 'ext/view/sendmail.*.html.hook.php') as $hookFile) include $hookFile;
            $mailContent = ob_get_contents();
            ob_end_clean();
            chdir($oldcwd);

            $toList = $key;
            $ccList = '';

            // $mailTitle = sprintf($this->lang->secondorder->ccMailTitle, $secondorder->code);
            list($toList, $ccList) = array($toList,$ccList);


            /* 处理邮件标题。*/
            $subject = $mailTitle;
            /* Send emails. */
            $this->mail->send($toList, $subject, $mailContent, $ccList);
            if($this->mail->isError()) error_log(join("\n", $this->mail->getError()));
        }
    }

    //计算某一年某个月第一周开始
    function get_monthOneWeekly($month)
    {
        $i = 1;
        $end_date = date('d',strtotime($month.' +1 month -1 day'));//计算当前月有多少天
        $w = date('N',strtotime($month.'-'.$i));  //计算第一天是周几
        $weeklyStart = date('Y-m-d',strtotime($month.'-'.$i.' -'.($w-1).' days')); //当周开始时间
        return $weeklyStart;
    }
    //计算某一年某个月有几周
    function get_weekinfo($month,$flag = false)
    {
        if($flag){
            for($i = 1;$i <= 12;$i ++){
                $newMonth = '';
                $i = $i < 10 ? "0".$i : $i;
                $newMonth = $month.'-'.$i;
                $end_date = date('d',strtotime($newMonth.' +1 month -1 day'));//计算当前月有多少天
                $weekinfo[$i] = $this->weekInfo($newMonth,$end_date);
            }
        }else{
            //获取月的周
            $end_date = date('d',strtotime($month.' +1 month -1 day'));//计算当前月有多少天
            $weekinfo[1] = $this->weekInfo($month,$end_date);
        }
        $every = array();
        $all = array();
        $weekly = array();
        //转换，处理周跨月造成的日期重叠问题
        foreach ($weekinfo as $item) {
            foreach ($item as $value) {
                $every['weeklyNum'] = $value['weeklyNum'];
                $every['weeklyStart'] = $value['weeklyStart'];
                $every['weeklyEnd'] = $value['weeklyEnd'];
                if(!in_array($value['weeklyNum'],$weekly) || $value['weeklyNum'] == '52'){
                    $all[] = $every;
                    $weekly[] = $value['weeklyNum'];
                }
            }
        }
        return $all;
    }
    function weekInfo($month,$end_date){
        $weekinfo = array();//创建一个空数组
        for ($i = 1; $i < $end_date ; $i = $i+7) {   //循环本月有多少周
            $w = date('N',strtotime($month.'-'.$i));  //计算第一天是周几
            $weeklyNum = date('W',strtotime($month.'-'.$i.' -'.($w-1).' days'));//第几周
            $weeklyStart = date('Ymd',strtotime($month.'-'.$i.' -'.($w-1).' days')); //当周开始时间
            $weeklyEnd = date('Ymd',strtotime($month.'-'.$i.' +'.(7-$w).' days'));//结束时间
            $weekinfo[] = array( 'weeklyNum'=> $weeklyNum,'weeklyStart' => $weeklyStart,'weeklyEnd'=> $weeklyEnd);
            //$weekinfo[] = array('week'=>sprintf($this->lang->workreport->weeklyNumTip, $weeklyNum).'<br>'. $weeklyStart.'-'. $weeklyEnd);
        }
        return $weekinfo;
    }
    /**
     * Project: chengfangjinke
     * Method: buildSearchForm
     * @param $queryID
     * @param $actionURL
     */
    public function buildSearchForm($queryID, $actionURL)
    {
        $this->config->workreport->search['actionURL'] = $actionURL;
        $this->config->workreport->search['queryID']   = $queryID;
        $this->loadModel('search')->setSearchParams($this->config->workreport->search);
    }

    //计算某一年某个月所有天
    function get_monthDays($month,$flag = false)
    {
        if($flag){
            for($i = 1;$i <= 12;$i ++){
                $newMonth = '';
                $i = $i < 10 ? "0".$i : $i;
                $newMonth = $month.'-'.$i;
                $weekinfo[$i] = $this->monthDays($newMonth);
            }
        }else{
            //获取月的周
            $weekinfo[1] = $this->monthDays($month);
        }
        $every = array();
        $all = array();
        $weekly = array();
        //转换，处理周跨月造成的日期重叠问题
        foreach ($weekinfo as $item) {
            foreach ($item as $value) {
                $every['weeklyNum'] = $value;
                 $all[] = $every;
            }
        }
        return $all;
    }

    /**
     * 获取当月时间
     */
    public function monthDays($start)
    {
        //本月开始时间
        $monthStart = date('Y-m-01',strtotime($start));
        //本月结束时间
        $monthend = date('Y-m-d',(strtotime("$monthStart+1 month -1 day") ));
        $array = array();
        $begin =  strtotime($monthStart);
        $end =   strtotime($monthend);
        $k = 1;
        for ($i = $begin; $i <= $end ;$i += 86400){
            $array[$k] = date('Y-m-d',$i);
            $k ++;
        }
        return $array;
    }

    /**
     * 获取当月时间
     */
    public function selectMonthDays($start,$end)
    {
        //本月开始时间
        $monthStart = trim($start,"'");//date('Y-m-d',strtotime(trim($start)));
        //本月结束时间
        $monthend = trim($end,"'"); // date('Y-m-d',(strtotime(trim($end)) ));
        $array = array();
        $begin =  strtotime($monthStart);
        $end =   strtotime($monthend);
        $k = 1;
        for ($i = $begin; $i <= $end ;$i += 86400){
            $array[$k] = date('Y-m-d',$i);
            $k ++;
        }
        $every = array();
        $all = array();
        foreach ($array as $item) {
            $every['weeklyNum'] = $item;
            $all[] = $every;
        }
        return $all;
    }

    /**
     * 获取当前周时间
     * @param $project
     * @param int $index
     */
    public function getWeeklyDays()
    {
        $start = helper::today();
        $beginDate = date('Y-m-d',strtotime('this week',strtotime($start)));
       // $endDate = date('Y-m-d',(strtotime('next week',strtotime($start)) - 1));
        $array = array();
        $begin =  strtotime($beginDate);
        $end =   strtotime($start);
        $k = 1;
        for ($i = $begin; $i <= $end ;$i += 86400){
            $array[$k] = date('Y-m-d',$i);
            $k ++;
        }
        return $array;
    }
    //展示
   /* function generation( $token,$date )
    {
        $uesr = $this->usercache( $token );

        $arr = [] ;
        foreach ($this->get_weekinfo($date) as $k => $v)
        {

            //连接上个月的数据去掉
            if( date("m",strtotime($v[0])) == date("m",strtotime($date))  )
            {
                $arr[] = $v;
            }
        }

        //月份的最后一天
        $lastday = date('Y-m-d', mktime(23, 59, 59, date('m', strtotime($date))+1, 00));

        $lastweek = end($arr);

        //不够下个月的数据补上
        if( strtotime($lastday) > strtotime($lastweek[1]) )
        {
            $newendarr = array( date( 'Y-m-d',strtotime($lastweek[1])+86400 ),date( 'Y-m-d',strtotime($lastweek[1])+(86400*7) ) );
            array_push($arr, $newendarr);
        }

        try{

            //数据插入
            foreach ($arr as $ke => $va)
            {

                $data['week'] = $ke+1;                        //第几周
                $data['uid']  = $uesr['uid'];                //用户ID
                $data['year'] = date('Y',strtotime($date));    //年
                $data['month'] = date('m',strtotime($date));//月

                //每个星期的开始-结束数据填充
                $i = strtotime($va[0]);
                while ( $i <= strtotime($va[1]) ) {
                    $data['date'] = date('Y-m-d',$i);         //年月日
                    $data['day']  = $this->getTimeWeek( $i );//星期几

                    //添加
                    $this->insertData( $data );
                    $i = $i+86400;
                }
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
    //获取星期参数
    function getTimeWeek($time)
    {
        $day = date("w",$time);
        return ($day == 0) ? '7' : $day ;
    }*/

   public function getHistory($pager = null,$begin = null,$end = null){

       $history = $this->dao->select('t1.*,t2.name project,t3.name execution,t4.name taskName,t5.realname,t4.type')->from(TABLE_EFFORT)->alias('t1')
           ->leftJoin(TABLE_PROJECT)->alias('t2')->on('t1.project = t2.id')
           ->leftJoin(TABLE_EXECUTION)->alias('t3')->on('t1.execution= t3.id')
           ->leftJoin(TABLE_TASK)->alias('t4')->on('t1.objectID = t4.id')
           ->leftJoin(TABLE_USER)->alias('t5')->on('t1.account = t5.account')
           ->where('t1.account')->eq($this->app->user->account)
           ->andWhere('t1.deleted')->eq(0)
           ->andWhere('t1.dataVersion')->eq(1)
           ->andWhere('t1.date')->ge('2023-01-01')
           ->andWhere('t1.date')->le('2023-09-08')
           ->andWhere('t1.objectType')->eq('task')
           ->beginIF($begin && $end )->andWhere('t1.date')->ge(date('Y-m-d',strtotime($begin)))->andWhere('t1.date')->le(date('Y-m-d',strtotime($end)))->fi()
           ->orderBy('t1.date desc')
           ->page($pager)
           ->fetchAll();
       return $history;
   }

    /**
     * 查询最后一次报工
     * @param $account
     * @return stdClass
     */
   public function getWorkLastApi($account){
       $this->app->loadLang('task');
       $work = $this->dao->select('project,activity,apps,objects,workType,account')->from(TABLE_WORKREPORT)
           ->where('deleted')->eq('0')
           ->andWhere('account')->eq($account)
           ->orderBy('versions desc')
           ->limit(1)
           ->fetch();

       $projects = array(''=>'') + $this->getProjectTeam('','new',1);//所有有权限的项目
       $data = new stdClass();
       $data->beginDate = helper::today();
       $data->project   = isset($work->project) ? array('key' => $work->project,'text' => zget($projects,$work->project,'')) :  array('key' =>'','text' => '') ;
       $data->activity  = isset($work->activity) && zget($projects,$work->project,'') ? array('key' => $work->activity,'text' => zget($this->loadModel('project')->getProjectOneStage($work->project ),$work->activity,''))  : array('key' =>'','text' => '') ;
       $data->apps       = isset($work->apps)&& zget($projects,$work->project,'') ? array('key' => $work->apps,'text' => zget($this->loadModel('project')->getProjectTwoStage($work->activity),$work->apps,'') ): array('key' =>'','text' => '') ;
       $data->objects      = isset($work->objects)&& zget($projects,$work->project,'') ? array('key' =>$work->objects,'text' => zget($this->loadModel('task')->getProjectTask($work->apps,$work->project,''),$work->objects,''))  : array('key' =>'','text' => '') ;
       $data->workType  = isset($work->workType)&& zget($projects,$work->project,'') ? array('key' =>$work->workType,'text' => zget($this->lang->task->typeList,$work->workType,'')) :  array('key' =>'','text' => '') ;
       $data->account   = isset($account) ? $account : '';
       $data->realname  = isset($account) ? zget($this->loadModel('user')->getPairs('noletter'),$account ,'') : '';
       return $data;
   }

    /**
     * 获取用户某一天所有报工
     * @param $account
     * @param null $pager
     * @param null $date
     * @return array
     */
   public function getEveryDayApi($account,$pager = null,$date = null){

       $res = $this->dao->select("t2.*,t1.consumed as effortConsumed")->from(TABLE_EFFORT)->alias('t1')
           ->leftJoin(TABLE_WORKREPORT)->alias('t2')
           ->on('t2.id = t1.workID')
           ->where('t1.deleted')->eq(0)
           ->andWhere('t1.objectType')->eq('task')
           ->andWhere('t1.account')->eq($account)
           ->beginIF($date)->andWhere('beginDate')->eq($date)->fi()
           ->andWhere('t2.deleted')->eq(0)
           ->orderBy('id_desc')
           ->page($pager)
           ->fetchAll();

       $datas = array();

       $projects = array(''=>'') + $this->getProjectTeam('','new');//所有有权限的项目
       $onestages = array(''=>'') + $this->loadModel('project')->getProjectOneStage('','browse');//所属活动
       $stages = array(''=>'') + $this->loadModel('project')->getProjectTwoStage();//所属应用系统
       $task = array(''=>'') + $this->loadModel('task')->getProjectAllTask('all');//所属任务
       foreach ($res as $item) {
           $projectStatus = $this->loadModel('project')->getByID($item->project);
           $data = new stdClass();
           $data->beginDate = $date;
           $data->id        = $item->id;
           $data->project   = isset($item->project) ? array('key' =>$item->project, 'text' => zget($projects,$item->project,'')) :'';
           $data->activity  = isset($item->activity) ? array('key' =>$item->activity, 'text' => zget($onestages,$item->activity,'')) :'' ;
           $data->apps       = isset($item->apps) ? array('key' =>$item->apps, 'text' => zget($stages,$item->apps,''))  :'' ;
           $data->objects      = isset($item->objects) ?  array('key' =>$item->objects,'text' => zget($task,$item->objects,'')) :'' ;
           $data->workType  = isset($item->workType) ?  array('key' => $item->workType,'text'=> zget($this->lang->task->typeList,$item->workType,'')) :'';
           $data->account   = isset($account) ? $account : '';
           $data->realname  = isset($account) ? zget($this->loadModel('user')->getPairs('noletter'),$account ,'') : '';
           $data->workContent   =  $item->workContent;
           $data->consumed   =  isset($item->consumed) ? $item->consumed : 0;
           //$data->canEditAndDelete   = isset($item->beginDate) ? $this->checkEditAndDelete(date('Y-m-d',strtotime($item->beginDate)),$item->append,$item->project) : '';
          //补报
          if($item->append){
         //当月可编辑
             if(date('Ym',strtotime($item->beginDate)) == date('Ym',strtotime(helper::today())) &&  $projectStatus->status != 'closed'){
                 $data->canEditAndDelete = true;
             }else{
                 $data->canEditAndDelete = false;
             }
          }else{
             $data->canEditAndDelete   = isset($item->beginDate) ? ($item->source == 2 ? false :  $this->checkEditAndDelete(date('Y-m-d',strtotime($item->beginDate)),$item->append,$item->project)) : false;
          }
           $datas[]         = $data;
       }

       return $datas;
   }
    /**
     * 报工详情
     * @param $workID
     */
    public function getWorkByIDApi($workID){
        $work =  $this->dao->select('project,activity,apps,objects,workType,account,beginDate,id,workContent,consumed')->from(TABLE_WORKREPORT)->where('id')->eq($workID)->fetch();

        $projects = array(''=>'') + $this->getProjectTeam('','new');//所有有权限的项目
        $onestages = array(''=>'') + $this->loadModel('project')->getProjectOneStage();//所属活动
        $stages = array(''=>'') + $this->loadModel('project')->getProjectTwoStage();//所属应用系统
        $task = array(''=>'') + $this->loadModel('task')->getProjectAllTask('all');//所属任务
        $data = new stdClass();
        $data->beginDate = isset($work->beginDate) ? date('Y-m-d',strtotime($work->beginDate)) :'';
        $data->id        = isset($work->id) ? $work->id :'';
        $data->project   = isset($work->project) ? array('key' =>$work->project, 'text' => zget($projects,$work->project,'')) :'';
        $data->activity  = isset($work->activity) ? array('key' =>$work->activity, 'text' => zget($onestages,$work->activity,'')) :'' ;
        $data->apps       = isset($work->apps) ? array('key' =>$work->apps, 'text' => zget($stages,$work->apps,''))  :'' ;
        $data->objects      = isset($work->objects) ?  array('key' =>$work->objects,'text' => zget($task,$work->objects,'')) :'' ;
        $data->workType  = isset($work->workType) ?  array('key' => $work->workType,'text'=> zget($this->lang->task->typeList,$work->workType,'')) :'';
        $data->account   = isset($work->account) ? $work->account :'';
        $data->realname  = isset($work->account) ? zget($this->loadModel('user')->getPairs('noletter'),$work->account ,'') :'';
        $data->workContent   = isset($work->workContent) ? $work->workContent :'';
        $data->consumed   =  isset($work->consumed) ? $work->consumed : 0;
        return $data;
    }
    /**
     * 报工详情(编辑)
     * @param $workID
     */
    public function getWorkByEditIDApi($workID){
        $work =  $this->dao->select('project,activity,apps,objects,workType,account,beginDate,id,workContent,append,consumed,source')->from(TABLE_WORKREPORT)
                ->where('id')->eq($workID)
                ->andWhere('account')->eq($this->app->user->account)
                ->fetch();

        $onestages = array(''=>'') + $this->loadModel('project')->getProjectOneStage('','browse');//所属活动
        $stages = array(''=>'') + $this->loadModel('project')->getProjectTwoStage();//所属应用系统
        $task = array(''=>'') + $this->loadModel('task')->getProjectAllTask('all');//所属任务
        $data = new stdClass();
        //补报
        if($work->append){
        //当月可编辑
            if(date('Ym',strtotime($work->beginDate)) == date('Ym',strtotime(helper::today()))){
                $data->canEditAndDelete = true;
            }else{
                $data->canEditAndDelete = false;
            }
        }else{
            $data->canEditAndDelete   = isset($work->beginDate) ? ( $work->source == 2 ? false :  $this->checkEditAndDelete(date('Y-m-d',strtotime($work->beginDate)),$work->append,$work->project)) : false;
        }
       // $data->canEditAndDelete   = isset($work->beginDate) ? $this->checkEditAndDelete(date('Y-m-d',strtotime($work->beginDate)),$work->append,$work->project) : '';
        $projects = array(''=>'') + $this->getProjectTeam('','new',$data->canEditAndDelete ? 1 :'');//所有有权限的项目
        $data->beginDate = isset($work->beginDate) ? date('Y-m-d',strtotime($work->beginDate)) :'';
        $data->id        = isset($work->id) ? $work->id :'';
        $data->project   = isset($work->project) ? array('key' =>$work->project, 'text' => zget($projects,$work->project,'')) :array('key' =>'','text' => '');
        $data->activity  = isset($work->activity) ? array('key' =>$work->activity, 'text' => zget($onestages,$work->activity,'')) :array('key' =>'','text' => '') ;
        $data->apps       = isset($work->apps) ? array('key' =>$work->apps, 'text' => zget($stages,$work->apps,''))  :array('key' =>'','text' => '') ;
        $data->objects      = isset($work->objects) ?  array('key' =>$work->objects,'text' => zget($task,$work->objects,'')) :array('key' =>'','text' => '') ;
        $data->workType  = isset($work->workType) ?  array('key' => $work->workType,'text'=> zget($this->lang->task->typeList,$work->workType,'')) :array('key' =>'','text' => '');
        $data->account   = isset($work->account) ? $work->account :'';
        $data->realname  = isset($work->account) ? zget($this->loadModel('user')->getPairs('noletter'),$work->account ,'') :'';
        $data->workContent   = isset($work->workContent) ? $work->workContent :'';
        $data->consumed   =  isset($work->consumed) ? $work->consumed : 0;

        return $data;
    }

    /**
     * 保存新建报工
     * @return string
     */
    public function workSaveApi(){
        $data = fixer::input('post')
            ->get();

        $this->app->loadConfig('task');
        $projects = array(''=>'') + $this->getProjectTeam('', 'new',1);//有权限项目
        //验证用户日期是否在允许时间内
        $start = $this->getCreateBeginAndEnd(); //正常窗口时间
        if(strtotime($data->beginDate) < strtotime($start->begin) || strtotime($data->beginDate) > strtotime(helper::now())){
             return dao::$errors = sprintf($this->lang->workreport->beginDateTipApi,$data->beginDate);
        }
       //项目是否关闭
        if(!in_array($data->project,array_keys($projects))){
            return dao::$errors = sprintf($this->lang->workreport->projectTipApi);
        }
        $consumed = $data->consumed;
        $reg = '/^(([1-9][0-9]*)|(([0]\.\d{1}|[1-9][0-9]*\.\d{1})))$/';
        if (!preg_match($reg, $consumed)) {
             return dao::$errors =  sprintf($this->lang->workreport->consumedTipApi);
        }
        //检测每天工时是否超出正常范围
        $res = $this->checkEffort($consumed, $data->beginDate);
        if ($res) {
            $year  = date('Y',strtotime($data->beginDate));
            $month = date('m',strtotime($data->beginDate));
            $day   = date('d',strtotime($data->beginDate));
            return dao::$errors = sprintf($this->lang->workreport->thresholdErrorApi,$this->config->task->workThreshold);
        }

        //查询当前用户历史报工最新的一次版本
        $version = $this->dao->select('max(versions) versions')->from(TABLE_WORKREPORT)
            ->where('account')->eq($this->app->user->account)
            ->andWhere('deleted')->eq(0)
            ->fetch();

        //拼装入库
         $work = new stdClass();
         $work->project    = $data->project;
         $work->activity   = $data->activity;
         $work->apps       = $data->apps;
         $work->objects    = $data->objects;
         $work->beginDate  = $data->beginDate;
         $work->consumed   = $data->consumed;
         $work->workType   = $data->workType;
         $work->workContent = $data->workContent;
         $work->account    = $this->app->user->account;

         $work->versions = $version ? $version->versions + 1 : 1;
         $work->weeklyNum = date('W',strtotime($data->beginDate));
         $work->append    = '0'; //是否补报
         $work->source    = '1'; //移动端
         $this->dao->insert(TABLE_WORKREPORT)->data($work)->autoCheck()->exec();//存报工表
         $workID = $this->dao->lastInsertID();

         $effort = new stdClass();
         $effort->date     = $data->beginDate;// helper::today();
         $effort->consumed =  $data->consumed;
         $effort->work     = $data->workContent;

         $effort->workID     = $workID;
         $this->effort($data->objects,$effort);//任务报工存工时表
         return $workID;
    }

    /**
     * 编辑报工
     * @param $workID
     * @return array|string
     */
    public function workEditApi($workID){
        $oldWork = $this->getByID($workID);
        $data = $data = fixer::input('post')
            ->get();

        $this->app->loadConfig('task');
        $projects = array(''=>'') + $this->getProjectTeam('', 'new',1);//有权限项目
        $start = $this->getCreateBeginAndEnd(); //正常窗口时间
        if(strtotime($data->beginDate) < strtotime($start->begin) || strtotime($data->beginDate) > strtotime(helper::now())){
              return dao::$errors = sprintf($this->lang->workreport->beginDateTipApi, $data->beginDate);
        }
        //项目是否关闭
        if(!in_array($data->project,array_keys($projects))){
            return dao::$errors = sprintf($this->lang->workreport->projectTipApi);
        }
        $consumed = $data->consumed;
        $reg = '/^(([1-9][0-9]*)|(([0]\.\d{1}|[1-9][0-9]*\.\d{1})))$/';
        if (!preg_match($reg, $consumed)) {
              return dao::$errors =  sprintf($this->lang->workreport->consumedTipApi);
        }

        //检测每天工时是否超出正常范围
        $res = $this->checkEffort($consumed, $data->beginDate, $workID);
        if ($res) {
             $year  = date('Y',strtotime($data->beginDate));
             $month = date('m',strtotime($data->beginDate));
             $day   = date('d',strtotime($data->beginDate));
             return dao::$errors = sprintf($this->lang->workreport->thresholdErrorApi,$this->config->task->workThreshold);
        }

        //拼装入库
        $work = new stdClass();
        $work->project = $data->project;
        $work->activity = $data->activity;
        $work->apps = $data->apps;
        $work->objects = $data->objects;
        $work->beginDate = $data->beginDate;
        // $work->endDate = $data->endDate[$key];
        $work->consumed = $data->consumed;
        $work->workType = $data->workType;
        $work->workContent = $data->workContent;
        $work->editedBy = $this->app->user->account;
        $work->editTime = helper::now();
        $work->source   ='1';
        $work->weeklyNum = date('W',strtotime($data->beginDate));

        $this->dao->update(TABLE_WORKREPORT)->data($work)->autoCheck()
            ->where('id')->eq($workID)->exec();

        $effort = new stdClass();
        $effort->date = $data->beginDate;//helper::today();
        $effort->project = $work->project;
        $effort->execution = $work->apps;
        $effort->consumed = $data->consumed;
        $effort->work = $data->workContent;
        $effort->objectID = $data->objects;
        $effort->workID = $workID;

        $effrotID = $this->dao->select('id')->from(TABLE_EFFORT)->where('workID')->eq($workID)->fetch();
        $this->dao->update(TABLE_EFFORT)->data($effort)->autoCheck()
                ->where("id")->eq($effrotID->id)
                ->exec(); //任务报工更新工时表

         //编辑时可能所有项都会修改，比较新旧数据，查看关键项是否被修改
         if($oldWork->project != $work->project || $oldWork->activity !=  $work->activity || $oldWork->apps != $work->apps || $oldWork->objects !=  $work->objects){
                //有一项不一致，则更新原来的任务工作量相关
            $this->loadModel('task')->computeTask($oldWork->objects);
            $this->loadModel('task')->computeConsumed($oldWork->objects);
            $this->loadModel('action')->create('effort', $oldWork->objects, 'DeleteEstimate', $this->lang->workreport->editConsumed );
         }
            //关键项都没变 ，只是时间、工时、工作内容变
         $this->loadModel('task')->computeTask($data->objects);
         $this->loadModel('task')->computeConsumed($data->objects);

         $this->loadModel('action')->create('effort', $effrotID->id, 'edited', '');

        return  common::createChanges($oldWork, $work);
    }

    /**
     * 获取项目空间
     */
    public function getProjectApi($pager = null ,$workID =  null ){
        $work = $workID ? $this->getByID($workID) : '';
        $project =   $this->dao->select('concat(code,"_",name) as name,t1.root projectid')->from(TABLE_TEAM)->alias('t1')
            ->leftJoin(TABLE_PROJECT)->alias('t2')
            ->on('t1.root = t2.id')
            ->where('t2.type')->eq('project')
            ->andWhere('t2.status')->ne('closed')
            ->andWhere('t2.switch')->eq('1') //报工开关
            ->andWhere('t1.account')->eq($this->app->user->account)
            ->beginIF(isset($work->append) && $work->append)->andWhere('t2.allowBegin')->isNotnull()->andWhere("t2.allowBegin != '0000-00-00'")->fi()
            ->page($pager)
            ->fetchPairs('projectid','name');
        $res = array();
        foreach ($project as $key => $item) {
            $pro = new stdClass();
            $pro->key  = $key;
            $pro->text = $item;
            $res[] = $pro;
        }
        return $res;
    }
    /**
     * 获取所属活动
     */
    public function getActivityApi($project = null,$pager = null,$type = null){

        $stages =   $this->dao->select("id,name")->from(TABLE_EXECUTION)
            ->where('deleted')->eq(0)
            ->andWhere('type')->eq('stage')
            ->andWhere('grade')->eq('1')
            ->beginIF($project)->andWhere('project')->eq($project)->fi()
            ->beginIF($project == '0')->andWhere('project')->eq($project)->fi()
            ->andWhere('dataVersion')->eq(2)
            ->beginIF(empty($type) )->andWhere('isLocaleSupport')->eq(1)->fi()//非现场支持
            ->orderBy('id_asc')
            ->page($pager)
            ->fetchPairs("id","name");
        $res = array();
        foreach ($stages as $key => $item) {
            $pro = new stdClass();
            $pro->key  = $key;
            $pro->text = $item;
            $res[] = $pro;
        }
        return $res;
    }
    /**
     * 获取所属阶段/系统
     */
    public function getAppApi($activity = null,$pager = null){

        $app =   $this->dao->select("id,name")->from(TABLE_EXECUTION)
            ->where('deleted')->eq(0)
            ->andWhere('type')->eq('stage')
            ->andWhere('grade')->eq('2')
            ->andWhere('parent')->eq($activity)
            ->andWhere('dataVersion')->eq(2)
            ->orderBy('id_asc')
            ->page($pager)
            ->fetchPairs("id","name");
        $res = array();
        foreach ($app as $key => $item) {
            $pro = new stdClass();
            $pro->key  = $key;
            $pro->text = $item;
            $res[] = $pro;
        }
        return isset($res) ? $res  :array();
    }
    /**
     * 获取所属对象
     */
    public function getTaskApi($app,$projectName,$pager = null){

        $this->app->loadLang('task');
        $project =  $team = $this->dao->select('concat(code,"_",name) as name')->from(TABLE_PROJECT)->where('id')->eq($projectName)->fetch();
        $projectName = isset($project->name) ? $project->name : '' ;

        $execution = $this->dao->select('name')->from(TABLE_EXECUTION)->where('id')->eq($app)->fetch();

        $taskType = array(
            $this->lang->task->stageSecondList['projectPlan']   ,//二级阶段 项目管理活动  计划阶段
            $this->lang->task->stageSecondList['projectProcure']  ,//二级阶段 项目管理活动  采购阶段
            $this->lang->task->stageSecondList['projectImplement'] ,//二级阶段 项目管理活动  工程实施阶段
            $this->lang->task->stageSecondList['projectTechnology'] ,//二级阶段 项目管理活动  技术支持阶段
            $this->lang->task->stageSecondList['projectDirect']   ,//二级阶段 项目管理活动  管理阶段
            $this->lang->task->stageSecondList['projectClose']   ,//二级阶段 项目管理活动  结项阶段
            $this->lang->task->stageSecondList['projectOther']    ,//二级阶段 项目管理活动  其他阶段
            $this->lang->task->stageSecondList['deptForeignThing'] ,//二级阶段 部门其他管理  外来事物
            $this->lang->task->stageSecondList['deptInternalAffairs']   //二级阶段 部门其他管理 内部事物

        );
        $appName = isset($execution->name) ? $execution->name : '';
        if(in_array($appName,$taskType)){
            $task =  $this->dao->select("name,id")->from(TABLE_TASK)->alias('t1')
                ->where('t1.deleted')->eq(0)
                ->andWhere('t1.grade')->eq('1')->fi()
                ->beginIF(empty($flag))->andWhere('t1.name')->notLike('%已%')->fi()
                ->andWhere('t1.execution')->eq($app)
                ->andWhere('t1.dataVersion')->eq(2)
                ->orderBy('t1.id_asc')
                ->page($pager)
                ->fetchPairs("id","name");
        }else{
            $task =   $this->dao->select("t1.id id,(case when t1.parent  !='0' then concat(t1.name, '/',(select name from zt_task where id = t1.parent))
	          else t1.name end)  name")->from(TABLE_TASK)->alias('t1')
                ->where('t1.deleted')->eq(0)
                ->beginIF(strpos($projectName,'DEP') !== false)->andWhere('t1.grade')->eq('1')->fi()
                ->beginIF(strpos($projectName,'DEP') == false )->andWhere('t1.grade')->eq('2')->fi()
                ->beginIF(empty($flag))->andWhere('t1.name')->notLike('%已%')->fi()
                ->andWhere('t1.execution')->eq($app)
                ->andWhere('t1.dataVersion')->eq(2)
                ->orderBy('t1.id_asc')
                ->page($pager)
                ->fetchPairs("id","name");
            foreach ($task as $key=>$item){
                if(!$item) continue;
                $demandOrProOrSecond =  $this->loadModel('task')->getTaskDemandProblemDesc($key); // 关联的问题单 需求单 二线工单
                $desc = isset($demandOrProOrSecond->desc) ? strip_tags($demandOrProOrSecond->desc) : (isset($demandOrProOrSecond->summary) ?strip_tags( $demandOrProOrSecond->summary ): '');
                $task[$key] = $item."($desc)";
            }
        }
        $res = array();
        foreach ($task as $key => $item) {
            $pro = new stdClass();
            $pro->key  = $key;
            $pro->text = $item;
            $res[] = $pro;
        }

        return $res;
    }

    /**
     * 删除
     * @param $workID
     * @return string
     */
    public function deleteApi($workID){
        $work = $this->getByID($workID);
        /*if(empty($_POST['comment'] ))
        {
            $error =  dao::$errors['comment'] = sprintf($this->lang->workreport->empty, $this->lang->workreport->comment);
            return $error;
        }else {*/

            $this->dao->update(TABLE_WORKREPORT)->set('deleted')->eq('1')->where('id')->eq($workID)->exec();
            $this->dao->update(TABLE_EFFORT)->set('deleted')->eq('1')->where("id =(select id from (select id from zt_effort where workID = '$workID')t1 )")->exec();
            $this->loadModel('action')->create('workreport', $workID, 'mobiledeleted', $this->post->comment);

            $this->loadModel('task')->computeTask($work->objects);
            $this->loadModel('task')->computeConsumed($work->objects);
            return $workID;
        //}
    }

    /**
     * 获取每月报工
     * @param null $month
     * @return stdClass
     */
    public function getMonthTotalApi($month = null ){

        $month = $month ? $month : helper::today();
        $monthStart = date('Y-m-01',strtotime($month));

        //报工时间范围
        $beginAndEnd =  $this->getCreateBeginAndEnd();
        $allowBegin  = $beginAndEnd->begin ;
        $allowEnd = helper::today();
        //本月结束时间
        $monthend = date('Y-m-d',(strtotime("$monthStart+1 month -1 day") ));
        $monthDays = $this->selectMonthDays($monthStart,(date('m',strtotime($monthStart)) == date('m',strtotime($allowEnd))) ? $allowEnd : $monthend);
        $newMonth = array();
        foreach ($monthDays as  $monthDay) {
            $newMonth[date('j',strtotime($monthDay['weeklyNum']))] = $monthDay['weeklyNum'];
        }
        $res = $this->dao->select("t2.account,t2.beginDate,sum(t2.consumed) as consumed")->from(TABLE_EFFORT)->alias('t1')
            ->leftJoin(TABLE_WORKREPORT)->alias('t2')
            ->on('t2.id = t1.workID')
            ->where('t1.deleted')->eq(0)
            ->andWhere('t1.objectType')->eq('task')
            ->andWhere('t1.date')->ge($monthStart)->andWhere('t1.date')->le($monthend)
            ->beginIF($this->app->user->account !='admin')->andWhere('t1.account')->eq($this->app->user->account)->fi()
            ->andWhere('t1.workID')->ne(0)
            ->groupBy('t2.beginDate')
            ->orderBy('t2.beginDate asc')
            ->fetchAll();
        $newRes = array();
        foreach ($res as  $item) {
            $newRes[date('j',strtotime($item->beginDate))] = $item;
        }
        $work = new stdClass();
        $color = '';
        foreach ($newMonth as $key => $new) {
            $everyWork = new stdClass();
            $canAdd =false;
           if(isset($newRes[$key])){
               if(strtotime($newRes[$key]->beginDate) < strtotime($allowBegin) ){
                   $color = 'green';
               }else if(strtotime($newRes[$key]->beginDate) >= strtotime($allowBegin) &&  strtotime($newRes[$key]->beginDate) <= strtotime($allowEnd) ) {
                   if($newRes[$key]->consumed >= 8 || helper::isWorkDay($newRes[$key]->beginDate) == false){
                       $color = 'blue';
                       $canAdd   = true;
                   }else{
                       $color = 'red';
                       $canAdd   = true;
                   }
               }
               $everyWork->account  = $this->app->user->account;
               $everyWork->beginDate  = $new;
               $everyWork->consumed = round($newRes[$key]->consumed,1);
               $everyWork->color   = $color;
               $everyWork->canAdd = $canAdd;
           }else{
               if(helper::isWorkDay($new)){
                   $everyWork->account  = $this->app->user->account;
                   $everyWork->beginDate  = $new;
                   $everyWork->consumed = 0;
                   $everyWork->color  = strtotime(date('Y-m-d',strtotime($new))) < strtotime($allowBegin) ? 'green' :(helper::isWorkDay($new) == false ?  'blue' :'red') ;

                   if( strtotime($new) <= strtotime($allowEnd) ){
                       $canAdd   = true;
                   }
                   $everyWork->canAdd = $canAdd;
               }

           }
            $newMonth[$key] =  $everyWork;
        }
        $work->workInfo   = array_values($newMonth);

        $work->monthTotal = $res ? round(array_sum(array_column($res,'consumed')),2) : 0;
        $work->begin      =  $monthStart;
        $work->end        =  $monthend;
        return $work;
    }
   /**
     * 获取历史报工
     * @param null $month
     * @return stdClass
     */
    public function getWorkHistory($pager = null,$date = null){
        $this->app->loadLang('todo');
        $res = $this->dao->select("t2.*,t1.consumed as effortConsumed")->from(TABLE_EFFORT)->alias('t1')
            ->leftJoin(TABLE_WORKREPORT)->alias('t2')
            ->on('t2.id = t1.workID')
            ->where('t1.deleted')->eq(0)
            ->andWhere('t1.objectType')->eq('task')
            ->andWhere('t1.account')->eq($this->app->user->account)
            ->beginIF($date)->andWhere('beginDate')->eq($date)->fi()
            ->andWhere('t2.deleted')->eq(0)
            ->orderBy('beginDate_desc')
            ->page($pager)
            ->fetchAll();

        $datas = array();

        $projects = array(''=>'') + $this->getProjectTeam('','new');//所有有权限的项目
        $onestages = array(''=>'') + $this->loadModel('project')->getProjectOneStage('','browse');//所属活动
        $stages = array(''=>'') + $this->loadModel('project')->getProjectTwoStage();//所属应用系统
        $task = array(''=>'') + $this->loadModel('task')->getProjectAllTask('all');//所属任务
        foreach ($res as $item) {
            $projectStatus = $this->loadModel('project')->getByID($item->project);
            $data = new stdClass();
            $data->beginDate = isset($item->beginDate) ? date('Y-m-d',strtotime($item->beginDate)) :'';
            $data->weekday = isset($item->beginDate) ?  $this->lang->todo->dayNames[date('w',strtotime($item->beginDate))] :'';
            $data->id        = $item->id;
            $data->project   = isset($item->project) ? array('key' =>$item->project, 'text' => zget($projects,$item->project,'')) :'';
            $data->activity  = isset($item->activity) ? array('key' =>$item->activity, 'text' => zget($onestages,$item->activity,'')) :'' ;
            $data->apps       = isset($item->apps) ? array('key' =>$item->apps, 'text' => zget($stages,$item->apps,''))  :'' ;
            $data->objects      = isset($item->objects) ?  array('key' =>$item->objects,'text' => zget($task,$item->objects,'')) :'' ;
            $data->workType  = isset($item->workType) ?  array('key' => $item->workType,'text'=> zget($this->lang->task->typeList,$item->workType,'')) :'';
            $data->account   = isset($account) ? $account : '';
            $data->realname  = isset($account) ? zget($this->loadModel('user')->getPairs('noletter'),$account ,'') : '';
            $data->workContent   =  $item->workContent;
            $data->consumed   =  $item->consumed;
            //$data->canEditAndDelete   = isset($item->beginDate) ? $this->checkEditAndDelete(date('Y-m-d',strtotime($item->beginDate)),$item->append,$item->project) : '';
            //补报
           if($item->append){
               //当月可编辑
                 if(date('Ym',strtotime($item->beginDate)) == date('Ym',strtotime(helper::today())) && $projectStatus->status !='closed'){
                     $data->canEditAndDelete = true;
                 }else{
                     $data->canEditAndDelete = false;
                 }
           }else{
             $data->canEditAndDelete   = isset($item->beginDate) ? ($item->source == 2 ? false :$this->checkEditAndDelete(date('Y-m-d',strtotime($item->beginDate)),$item->append,$item->project)) : false;
           }
            $datas[]         = $data;
        }

        return $datas;
    }
}

