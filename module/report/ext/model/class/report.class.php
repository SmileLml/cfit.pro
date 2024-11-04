<?php
/**
 * The model file of report module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2020 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     report
 * @link        https://www.zentao.net
 */
class reportReport extends reportModel
{
    /**
     * Get work summary.
     *
     * @param  date    $begin
     * @param  date    $end
     * @param  int     $dept
     * @param  string  $type worksummary|workassignsummary
     * @access public
     * @return array
     */
    public function getWorkSummary($begin, $end, $dept, $type)
    {
        $today = helper::today();
        $end   = date('Y-m-d', strtotime("$end +1 day"));
        $depts = array();
        if($dept) $depts = $this->loadModel('dept')->getAllChildId($dept);
        $condition = $type == 'worksummary';

        $userField = $condition ? 'finishedBy' : 'assignedTo';
        $dateField = $condition ? 'finishedDate' : 'assignedDate';

        $tasks = $this->dao->select('t1.*')->from(TABLE_TASK)->alias('t1')
            ->leftJoin(TABLE_USER)->alias('t2')->on("t1.$userField=t2.account")
            ->where('t1.deleted')->eq(0)
            ->andWhere('t1.parent')->ge(0)
            ->beginIF($condition)->andWhere('t1.status')->in('closed, done')->fi()
            ->andWhere("t1.$dateField")->lt($end)
            ->andWhere("t1.$dateField")->ge($begin)
            ->beginIF($dept)->andWhere('t2.dept')->in($depts)->fi()
            ->orderBy('t1.execution')
            ->fetchAll('id');
        $teams = $this->dao->select('t1.*')->from(TABLE_TEAM)->alias('t1')
            ->leftJoin(TABLE_USER)->alias('t2')->on("t1.account=t2.account && t1.type='task'")
            ->where('t1.root')->in(array_keys($tasks))
            ->andWhere('t1.type')->eq('task')
            ->beginIF($dept)->andWhere('t2.dept')->in($depts)->fi()
            ->fetchGroup('root', 'account');

        $userTasks = array();
        foreach($tasks as $task)
        {
            if(!helper::isZeroDate($task->deadline) and strpos('|wait|doing|', "|{$task->status}|") !== false)
            {
                $delay = helper::diffDate($today, $task->deadline);
                if($delay > 0) $task->delay = $delay;
            }

            if(isset($teams[$task->id]))
            {
                foreach($teams[$task->id] as $account => $team)
                {
                    if($condition)  $task->finishedBy = $account;
                    if(!$condition) $task->assignedTo = $account;
                    $task->estimate = round($team->estimate, 1);
                    $task->consumed = round($team->consumed, 1);
                    $task->left     = round($team->left, 1);
                    $task->multiple = true;

                    if($condition)  $userTasks[$task->finishedBy][$task->execution][] = clone $task;
                    if(!$condition) $userTasks[$task->assignedTo][$task->execution][] = clone $task;
                }
            }
            else
            {
                $task->multiple = false;
                if($condition)  $userTasks[$task->finishedBy][$task->execution][] = $task;
                if(!$condition) $userTasks[$task->assignedTo][$task->execution][] = $task;
            }
        }
        return $userTasks;
    }

    /**
     * Get bug summary.
     *
     * @param  int     $dept
     * @param  date    $begin
     * @param  date    $end
     * @param  string  $type worksummary|workassignsummary
     * @access public
     * @return array
     */
    public function getBugSummary($dept, $begin, $end, $type)
    {
        $depts = array();
        if($dept) $depts = $this->loadModel('dept')->getAllChildId($dept);

        $userField = $type == 'bugsummary' ? 'resolvedBy' : 'assignedTo';
        $dateField = $type == 'bugsummary' ? 'resolvedDate' : 'assignedDate';

        $end = date('Y-m-d', strtotime("$end +1 day"));

        $userBugs = $this->dao->select('t1.*')->from(TABLE_BUG)->alias('t1')
            ->leftJoin(TABLE_USER)->alias('t2')->on("t1.$userField=t2.account")
            ->where('t1.deleted')->eq(0)
            ->andWhere("t1.$dateField")->lt($end)
            ->andWhere("t1.$dateField")->ge($begin)
            ->beginIF($type == 'bugsummary')->andWhere('t1.status')->in('resolved, closed')->fi()
            ->beginIF($dept)->andWhere('t2.dept')->in($depts)->fi()
            ->fetchGroup($userField);
        return $userBugs;
    }

    /**
     * Get test cases.
     *
     * @param  int   $applicationID
     * @param  int   $productID
     * @param  int   $projectID
     * @access public
     * @return array
     */
    public function getTestcases($applicationID = 0, $productID = 0, $projectID = 0)
    {
        // 找出所有的产品
        $casesProduct = $this->dao->select('product')->from(TABLE_CASE)
            ->where('deleted')->eq(0)
            ->beginIF($applicationID)->andWhere('applicationID')->eq($applicationID)->fi()
            ->beginIF($productID)->andWhere('product')->eq($productID)->fi()
            ->beginIF($projectID)->andWhere('project')->eq($projectID)->fi()
            ->andWhere('product')->ne(0)
            ->groupBy('product')
            ->fetchPairs('product');

        $products = $this->dao->select('id,name,app')->from(TABLE_PRODUCT)
            ->where('id')->in($casesProduct)
            ->fetchAll('id');

        // 找出产品对应的系统
        $applicationIDList = $this->dao->select('app')->from(TABLE_PRODUCT)
            ->where('id')->in($casesProduct)
            ->fetchPairs();
        $applicationIDList = array_unique($applicationIDList);

        $applications = $this->dao->select('id,name')->from(TABLE_APPLICATION)
            ->where('id')->in($applicationIDList)
            ->fetchPairs();

        // 如果以项目条件搜索，则在表格中展示项目名称
        $projectName = '/';
        if($projectID)
        {
            $projectName = $this->dao->select('IF(code = "",name,CONCAT(code,"_",name)) as name')->from(TABLE_PROJECT)
                ->where('id')->eq($projectID)
                ->fetch()->name;
        }

        $modules = [];

        foreach($casesProduct as $casesProductID) {
            /* Get createdVersion. */
            $createdVersion = $this->dao->select('createdVersion')->from(TABLE_PRODUCT)
                ->where('id')->eq($casesProductID)
                ->andWhere('deleted')->eq('0')
                ->orderBy('createdVersion_desc')
                ->limit(1)
                ->fetch('createdVersion');

            /* Check if it is new version. */
            $new = (!empty($createdVersion) and (!is_numeric($createdVersion[0]) or version_compare($createdVersion, '4.1', '>'))) ? true : false;

            $productModules = $this->dao->select('id, name, path')->from(TABLE_MODULE)
                ->where('root')->eq($casesProductID)
                ->beginIF($new)->andWhere('type')->in('story,case')->fi()
                ->beginIF(!$new)->andWhere('type')->eq('case')->fi()
                ->andWhere('grade')->eq('1')
                ->andWhere('deleted')->eq(0)
                ->fetchAll('id');

            // 有可能找到多个产品下的模块，所以给所有模块拼接产品名前缀
            $productBaseModule = new stdclass();
            $productBaseModule->name = $products[$casesProductID]->name . '/';
            $productBaseModule->path = '';
            $productBaseModule->productID = $casesProductID;
            $productBaseModule->applicationID = $products[$casesProductID]->app;

            foreach($productModules as $module) {
                $module->name = $products[$casesProductID]->name . '/' . $module->name;
                $module->productID = $casesProductID;
                $module->applicationID = $products[$casesProductID]->app;
            }

            $productModules = array(0 => $productBaseModule) + $productModules;
            $modules = array_merge($modules, $productModules);
        }

        foreach($modules as $module)
        {
            $children = empty($module->path) ? 0 : $this->dao->select('id')->from(TABLE_MODULE)->where('path')->like($module->path . '%')->andWhere('deleted')->eq(0)->fetchPairs();
            // 相对原有逻辑，增加了按系统查询和按项目查询
            // 但是按系统查询意义不大，因为产品和系统是对应的
            $cases    = $this->dao->select('id, status, lastRunResult')->from(TABLE_CASE)
                ->where('module')->in($children)
                ->andWhere('product')->eq($module->productID)
                ->beginIF($applicationID)->andWhere('applicationID')->eq($applicationID)->fi()
                ->beginIF($projectID)->andWhere('project')->eq($projectID)->fi()
                ->andWhere('deleted')->eq('0')
                ->fetchAll();

            $module->applicationName = $applications[$module->applicationID] ?? '/';
            $module->productName     = $products[$module->productID]->name ?? '/';
            $module->projectName     = $projectName;

            $module->pass    = 0;
            $module->blocked = 0;
            $module->fail    = 0;
            $module->run     = 0;
            $module->total   = count($cases);

            foreach($cases as $case)
            {
                if($case->status == 'normal' and $case->lastRunResult == 'pass')
                {
                    $module->pass ++;
                    $module->run  ++;
                }
                else if($case->status == 'normal' and $case->lastRunResult == 'fail')
                {
                    $module->fail ++;
                    $module->run  ++;
                }
                else if($case->status == 'normal' and $case->lastRunResult == 'blocked')
                {
                    $module->blocked ++;
                    $module->run     ++;
                }
            }
        }
        return $modules;
    }

    /**
     * Get build bugs.
     *
     * @param  int    $productID
     * @access public
     * @return array
     */
    public function getBuildBugs($productID)
    {
        $builds = $this->dao->select('id, name, execution, bugs')->from(TABLE_BUILD)->where('product')->eq($productID)->andWhere('execution')->ne('0')->andWhere('deleted')->eq('0')->fetchAll();
        $buildBugs = array();
        foreach($builds as $build)
        {
            $bugs = $this->dao->select('severity, type, status')->from(TABLE_BUG)->where('id')->in($build->bugs)->andWhere('deleted')->eq(0)->fetchAll();
            foreach($bugs as $bug)
            {
                $buildBugs[$build->execution][$build->id]['severity'][$bug->severity] = isset($buildBugs[$build->execution][$build->id]['severity'][$bug->severity]) ? ($buildBugs[$build->execution][$build->id]['severity'][$bug->severity] + 1) : 1;
                $buildBugs[$build->execution][$build->id]['type'][$bug->type]         = isset($buildBugs[$build->execution][$build->id]['type'][$bug->type]) ? ($buildBugs[$build->execution][$build->id]['type'][$bug->type] + 1) : 1;
                $buildBugs[$build->execution][$build->id]['status'][$bug->status]     = isset($buildBugs[$build->execution][$build->id]['status'][$bug->status]) ? ($buildBugs[$build->execution][$build->id]['status'][$bug->status] + 1) : 1;
            }
        }
        return $buildBugs;
    }

    /**
     * Get roadmaps.
     *
     * @param  string $conditions
     * @access public
     * @return array
     */
    public function getRoadmaps($conditions = '')
    {
        $products = $this->dao->select('t1.id as id,t1.name as name')->from(TABLE_PRODUCT)->alias('t1')
            ->leftJoin(TABLE_PROGRAM)->alias('t2')->on('t1.program = t2.id')
            ->where('t1.deleted')->eq(0)
            ->beginIF(empty($conditions))->andWhere('t1.status')->ne('closed')->fi()
            ->orderBy('t2.order_asc, t1.line_desc, t1.order_asc')
            ->fetchPairs('id', 'name');

        $plans = $this->dao->select('*')->from(TABLE_PRODUCTPLAN)->where('deleted')->eq(0)
            ->andWhere('product')->in(array_keys($products))
            ->andWhere('end')->gt(date('Y-m-d'))
            ->orderBy('begin')
            ->fetchGroup('product', 'id');
        return array('products' => $products, 'plans' => $plans);
    }

    /**
     * Get cases run data.
     *
     * @param  int   $applicationID
     * @param  int   $productID
     * @param  int   $projectID
     * @access public
     * @return array
     */
    public function getCasesRun($applicationID = 0, $productID = 0, $projectID = 0)
    {
        $testtasks = $this->dao->select('t1.id,t1.name,t2.id as taskID,t1.applicationID,t1.product,t1.project')->from(TABLE_TESTTASK)->alias('t1')
            ->leftJoin(TABLE_TESTRUN)->alias('t2')->on('t2.task=t1.id')
            ->leftJoin(TABLE_CASE)->alias('t3')->on('t2.case=t3.id')
            ->where('t1.deleted')->eq(0)
            ->andWhere('t3.deleted')->eq(0)
            ->beginIF($applicationID)->andWhere('t1.applicationID')->eq($applicationID)->fi()
            ->beginIF($productID)->andWhere('t1.product')->eq($productID)->fi()
            ->beginIF($projectID)->andWhere('t1.project')->eq($projectID)->fi()
            ->fetchGroup('id', 'taskID');

        $applicationIDList = [];
        $productIDList     = [];
        $projectIDList     = [];

        foreach($testtasks as $testtaskGroup)
        {
            foreach($testtaskGroup as $testtask)
            {
                $applicationIDList[] = $testtask->applicationID;
                $productIDList[]     = $testtask->product;
                $projectIDList[]     = $testtask->project;
            }
        }

        $applicationIDList = array_unique($applicationIDList);
        $productIDList     = array_unique($productIDList);
        $projectIDList     = array_unique($projectIDList);

        $applications = $this->dao->select('id,name')->from(TABLE_APPLICATION)
            ->where('id')->in($applicationIDList)
            ->fetchPairs();
        $products = $this->dao->select('id,name')->from(TABLE_PRODUCT)
            ->where('id')->in($productIDList)
            ->fetchPairs();
        $projects = $this->dao->select('id,IF(code = "",name,CONCAT(code,"_",name))')->from(TABLE_PROJECT)
            ->where('id')->in($projectIDList)
            ->fetchPairs();

        $data = array();
        if(!empty($testtasks))
        {
            foreach($testtasks as $id => $tasks)
            {
                $data[$id]['applicationName'] = $applications[$tasks[key($tasks)]->applicationID] ?? '/';
                $data[$id]['productName']     = $products[$tasks[key($tasks)]->product] ?? '/';
                $data[$id]['projectName']     = $projects[$tasks[key($tasks)]->project] ?? '/';

                $data[$id]['name']    = $tasks[key($tasks)]->name;
                $data[$id]['fail']    = 0;
                $data[$id]['pass']    = 0;
                $data[$id]['blocked'] = 0;

                if(!key($tasks) && (count($tasks) == 1))
                {
                    $data[$id]['total'] = 0;
                }
                else
                {
                    $data[$id]['total'] = count(array_keys($tasks));

                    $results = $this->dao->select('caseResult')->from(TABLE_TESTRESULT)
                        ->where('run')->in(array_keys($tasks))
                        ->fetchAll();
                    if(!empty($results))
                    {
                        foreach($results as $result)
                        {
                            if(!isset($data[$id][$result->caseResult])) $data[$id][$result->caseResult] = 0;
                            $data[$id][$result->caseResult] += 1;
                        }
                    }
                }
            }
        }
        return $data;
    }

    /**
     * Get story bugs.
     *
     * @param  int    $moduleID
     * @access public
     * @return array
     */
    public function getStoryBugs($moduleID)
    {
        if(empty($moduleID)) return array();

        $bugs = $this->dao->select('id,title,status,story')->from(TABLE_BUG)
            ->where('module')->eq($moduleID)
            ->andWhere('story')->ne('0')
            ->andWhere('deleted')->eq('0')
            ->fetchAll();

        $dataList = array();
        if(!empty($bugs))
        {
            foreach($bugs as $bug) $dataList[$bug->story]['bugList'][] = $bug;

            $stories = $this->dao->select('id,title')->from(TABLE_STORY)
                ->where('id')->in(array_keys($dataList))
                ->andWhere('deleted')->eq('0')
                ->fetchPairs('id', 'title');

            foreach($stories as $id => $title)
            {
                $dataList[$id]['title'] = $title;
                $dataList[$id]['total'] = count($dataList[$id]['bugList']);
            }
        }
        return $dataList;
    }

    /**
     * Get module.
     *
     * @param  int    $productID
     * @access public
     * @return array
     */
    public function getModule($productID)
    {
        $modules = $this->dao->select('id,name')->from(TABLE_MODULE)
            ->where('root')->eq($productID)
            ->andWhere('parent')->eq('0')
            ->andWhere('deleted')->eq('0')
            ->fetchAll();

        $pairs = array();
        foreach($modules as $module)
        {
            $pairs[$module->id] = $module->name;
        }
        return $pairs;
    }


    /**
     * 获得质量门禁bug汇总
     *
     * @param int $projectID
     * @param int $productId
     * @param int $productVersion
     * @param int $buildId
     * @return array
     */
    public function getQualityGateBugSummaryOld($projectID = 0, $productId = 0, $productVersion = 0, $buildId  = 0){
        $data = [];
        $ret = [];
        if($buildId){ //制版ID,查询制版安全门禁快照
            $ret = $this->dao->select('zb.id, zb.project, zb.product, zb.linkPlan, zbbp.childType, zbbp.severity, zbbp.isBlackList')
                ->from(TABLE_BUILD_BUG_PHOTO)->alias('zbbp')
                ->leftJoin(TABLE_BUG)->alias('zb')->on('zbbp.bugId = zb.id')
                ->where('zbbp.buildId')->eq($buildId)
                ->fetchAll();
        }
        if(!$ret){
            $ret = $this->dao->select('id,project,product,linkPlan,childType,severity,isBlackList')
                ->from(TABLE_BUG)
                ->where('deleted')->eq('0')
                ->andWhere('type')->eq('security')
                ->andWhere('status ')->ne('closed')
                ->beginIF($projectID)->andWhere('project')->eq($projectID)->fi()
                ->beginIF($productId)->andWhere('product')->eq($productId)->fi()
                ->beginIF($productVersion)->andWhere("FIND_IN_SET('{$productVersion}',`linkPlan`)")->fi()
                ->fetchAll();
        }
        if($ret){
            $this->app->loadLang('bug');
            $childTypeList = zget(json_decode($this->lang->bug->childTypeList['all'], true), 'security');
            $severityList = $this->lang->bug->severityList;
            $tempData = [];
            foreach ($ret as $val){
                $product     = $val->product;
                $childType   = $val->childType;
                $severity    = $val->severity;
                $isBlackList = $val->isBlackList;
                if(isset($tempData[$product][$childType][$severity])) {
                    $tempData[$product][$childType][$severity] += 1;
                }else{
                    $tempData[$product][$childType][$severity] = 1;
                }
                if($isBlackList == 2){ //是否黑名单
                    if(isset($tempData[$product][$childType]['isBlackList'])) {
                        $tempData[$product][$childType]['isBlackList'] += 1;
                    }else{
                        $tempData[$product][$childType]['isBlackList'] = 1;
                    }
                }
            }
            //二级子类维度数据
            $childTypeData = [];
            //问题级别维度数据
            $severityData = [];
            $productBugCount = 0;
            $serousSeverityArray = [1, 2];
            foreach ($tempData as $product => $temp){
                $isNotAllowPass = false; //是否不允许通过
                $childTypeBugCount = 0;
                foreach ($childTypeList as $childType => $childTypeVal){
                    foreach ($severityList as $severity => $severityVal){
                        $productBugCount ++;
                        $childTypeBugCount ++;
                        if(isset($tempData[$product][$childType][$severity])) {
                            $severityData[$severity] = $tempData[$product][$childType][$severity];
                            if(in_array($severity, $serousSeverityArray)){
                                $isNotAllowPass = true;
                            }
                        }else{
                            $severityData[$severity] = 0;
                        }
                    }
                    $childTypeData[$childType]['data'] = $severityData;
                    if(isset($tempData[$product][$childType]['isBlackList'])) {
                        $childTypeData[$childType]['blackBugCount'] = $tempData[$product][$childType]['isBlackList'];
                        $isNotAllowPass = true;
                    }else{
                        $childTypeData[$childType]['blackBugCount'] = 0;
                    }
                }
                $data[$product]['data']  = $childTypeData;
                $data[$product]['count'] = $childTypeBugCount;
                $data[$product]['isNotAllowPass'] = $isNotAllowPass;
            }
        }
        return $data;
    }


    /**
     * 获得质量门禁汇总
     *
     * @param int $projectID
     * @param int $productId
     * @param int $productVersion
     * @param int $buildId
     * @return array
     */
    public function getQualityGateBugSummary($projectID = 0, $productId = 0, $productVersion = 0,  $buildId  = 0){
        $dataSource = 'bug';
        $dataList   = [];
        $data = [
            'dataSource' => $dataSource,
            'data' => $dataList,
        ];

        $this->app->loadLang('bug');
        $childTypeComputer = $this->lang->bug->childTypeComputer;
        $childTypeList = zget(json_decode($this->lang->bug->childTypeList['all'], true), 'security');
        $severityList = $this->lang->bug->severityList;
        $tempData = [];
        $ret = [];
        if($buildId){ //制版ID,查询制版安全门禁快照
            $ret = $this->dao->select('zb.id, zb.project, zb.product, zb.linkPlan, zbbp.childType, zbbp.severity, zbbp.isBlackList')
                ->from(TABLE_BUILD_BUG_PHOTO)->alias('zbbp')
                ->leftJoin(TABLE_BUG)->alias('zb')->on('zbbp.bugId = zb.id')
                ->where('zbbp.buildId')->eq($buildId)
                ->fetchAll();
            if($ret){
                $dataSource = 'bugPhoto'; //来源快照
            }
        }
        if(!$ret){ //不存在快照查询bug表
            $ret = $this->dao->select('id,project,product,linkPlan,childType,severity,isBlackList')
                ->from(TABLE_BUG)
                ->where('deleted')->eq('0')
                ->andWhere('type')->eq('security')
                ->andWhere('status ')->ne('closed')
                ->beginIF($projectID)->andWhere('project')->eq($projectID)->fi()
                ->beginIF($productId)->andWhere('product')->eq($productId)->fi()
                ->beginIF($productVersion && $productVersion == 1)->andWhere(" `linkPlan` in ('1', '') ")->fi()
                ->beginIF($productVersion && $productVersion != 1)->andWhere(" ( FIND_IN_SET('{$productVersion}',`linkPlan`) OR (`linkPlan` in ('1', ''))) ")->fi()
                ->fetchAll();
        }
        if($ret){ //查询有数据
            foreach ($ret as $val){
                $product     = $val->product;
                $linkPlan    = $val->linkPlan;
                $childType   = $val->childType;
                $severity    = $val->severity;
                $isBlackList = $val->isBlackList;
                $linkPlanArray = explode(',', $linkPlan);
                if($linkPlanArray){
                    $linkPlanArray = array_filter($linkPlanArray);
                }
                if(empty($linkPlanArray)){
                    if(isset($tempData[$product][1][$childType][$severity])) {
                        $tempData[$product][1][$childType][$severity] += 1;
                    }else{
                        $tempData[$product][1][$childType][$severity] = 1;
                    }
                }else{
                    foreach ($linkPlanArray as $linkPlanId){
                        if(isset($tempData[$product][$linkPlanId][$childType][$severity])) {
                            $tempData[$product][$linkPlanId][$childType][$severity] += 1;
                        }else{
                            $tempData[$product][$linkPlanId][$childType][$severity] = 1;
                        }
                    }
                }

                if($isBlackList == 2){ //是否黑名单
                    if(empty($linkPlanArray)){
                        if(isset($tempData[$product][1][$childType]['isBlackList'])) {
                            $tempData[$product][1][$childType]['isBlackList'] += 1;
                        }else{
                            $tempData[$product][1][$childType]['isBlackList'] = 1;
                        }
                    }else{
                        foreach ($linkPlanArray as $linkPlanId){
                            if(isset($tempData[$product][$linkPlanId][$childType]['isBlackList'])) {
                                $tempData[$product][$linkPlanId][$childType]['isBlackList'] += 1;
                            }else{
                                $tempData[$product][$linkPlanId][$childType]['isBlackList'] = 1;
                            }
                        }
                    }
                }
            }
        }else{
            //查询不到bug，查询项目下包含的版本信息
            $productList  =  $this->loadModel('project')->getProductList($projectID);
            $productIds = array_keys($productList);
            if(empty($productIds)){
                return $data;
            }
            $ret = $this->dao->select('id,product')
                ->from(TABLE_PRODUCTPLAN)
                ->where('deleted')->eq('0')
                ->andWhere('product')->in($productIds)
                ->beginIF($productId)->andWhere('product')->eq($productId)->fi()
                ->beginIF($productVersion)->andWhere('id')->eq($productVersion)->fi()
                ->fetchAll();
            if($ret){
                foreach ($ret as $val){
                    $tempProductId = $val->product;
                    $tempProductVersion = $val->id;
                    $tempData[$tempProductId][$tempProductVersion] = [];
                }
            }
        }

        if(empty($tempData) && $productId){
            $tempData[$productId][1] = []; //默认版本
        }
        //精确到版本搜索
        if($productVersion){
            $currentTempData = [];
            $currentTempData[$productId][$productVersion] = isset($tempData[$productId][$productVersion]) ? $tempData[$productId][$productVersion] : [];
            if($productVersion != 1 && isset($tempData[$productId][1])){
                $currentTempData[$productId][1] = $tempData[$productId][1];
            }
            $tempData = $currentTempData;
        }
        //格式化信息
        if(!empty($tempData)){
            $productBugCount = 0;
            $serousSeverityArray = [1, 2];
            foreach ($tempData as $product => $temp){
                $isNotAllowPass = false; //是否不允许通过
                $productVersionBugCount = 0; //产品版本下的数量
                $productVersionData = [];//产品版本下的列表
                foreach ($temp as $productVersion => $productVersionTemp){
                    $childTypeBugCount = 0; //bug安全缺陷分类数量
                    $childTypeData     = [];
                    foreach ($childTypeList as $childType => $childTypeVal){
                        $severityData = []; //bug严重等级列表
                        foreach ($severityList as $severity => $severityVal){
                            $productBugCount ++;
                            $productVersionBugCount ++;
                            $childTypeBugCount ++;
                            if(isset($tempData[$product][$productVersion][$childType][$severity])) {
                                $severityData[$severity] = $tempData[$product][$productVersion][$childType][$severity];
                                if(!$isNotAllowPass && in_array($severity, $serousSeverityArray) && $childType != $childTypeComputer){ //非主机类P0，P1问题
                                    $isNotAllowPass = true;
                                }
                            }else{
                                $severityData[$severity] = 0;
                            }
                        }
                        $childTypeData[$childType]['data'] = $severityData;
                        if(isset($tempData[$product][$productVersion][$childType]['isBlackList'])) {
                            $childTypeData[$childType]['blackBugCount'] = $tempData[$product][$productVersion][$childType]['isBlackList'];
                            if(!$isNotAllowPass){
                                $isNotAllowPass = true;
                            }
                        }else{
                            $childTypeData[$childType]['blackBugCount'] = 0;
                        }
                    }
                    $productVersionData[$productVersion]['data'] = $childTypeData;
                    $productVersionData[$productVersion]['count'] = $childTypeBugCount;
                    $productVersionData[$productVersion]['isNotAllowPass'] =  $isNotAllowPass;
                }
                $dataList[$product]['data']  = $productVersionData;
                $dataList[$product]['count'] = $productVersionBugCount;
            }
        }
        $data['dataSource'] = $dataSource;
        $data['data']        = $dataList;
        return $data;
    }

    /**
     * 获得安全门禁bug列表
     *
     * @param string $dataSource
     * @param int $projectID
     * @param int $productId
     * @param int $productVersion
     * @param int $build
     * @param string $childType
     * @param string $sourceType
     * @param int $severity
     * @param $orderBy
     * @param $pager
     * @return array
     */
    public function getQualityGateBugList($dataSource = 'bug', $projectID = 0, $productId = 0, $productVersion = 0,  $build = 0, $childType = '', $sourceType = '', $severity = 0, $orderBy = 'id_desc', $pager = null){
        $data = [];
        if($dataSource == 'bugPhoto'){ //快照
            $orderBy = 'zb.'.$orderBy;
            $ret = $this->dao->select('zb.*')
                ->from(TABLE_BUILD_BUG_PHOTO)->alias('zbbp')
                ->leftJoin(TABLE_BUG)->alias('zb')->on('zbbp.bugId = zb.id')
                ->where('zbbp.buildId')->eq($build)
                ->beginIF($projectID)->andWhere('zb.project')->eq($projectID)->fi()
                ->beginIF($productId)->andWhere('zb.product')->eq($productId)->fi()
                ->beginIF($productVersion && $productVersion == 1)->andWhere("( FIND_IN_SET('{$productVersion}', zb.`linkPlan`) OR zb.`linkPlan` = '') ")->fi()
                ->beginIF($productVersion && $productVersion != 1)->andWhere(" FIND_IN_SET('{$productVersion}', zb.`linkPlan`)")->fi()
                ->beginIF($childType)->andWhere('zbbp.childType')->eq($childType)->fi()
                ->beginIF($sourceType == 'severity' && $severity)->andWhere('zbbp.severity')->eq($severity)->fi()
                ->beginIF($sourceType == 'blackList')->andWhere('zbbp.isBlackList')->eq('2')->fi()
                ->orderBy($orderBy)
                ->page($pager)
                ->fetchAll();
        }else{
            $ret = $this->dao->select('*')
                ->from(TABLE_BUG)
                ->where('deleted')->eq('0')
                ->andWhere('type')->eq('security')
                ->andWhere('status ')->ne('closed')
                ->beginIF($projectID)->andWhere('project')->eq($projectID)->fi()
                ->beginIF($productId)->andWhere('product')->eq($productId)->fi()
                ->beginIF($productVersion && $productVersion == 1)->andWhere("( FIND_IN_SET('{$productVersion}',`linkPlan`) OR `linkPlan` = '') ")->fi()
                ->beginIF($productVersion && $productVersion != 1)->andWhere(" FIND_IN_SET('{$productVersion}',`linkPlan`)")->fi()
                //->beginIF($build)->andWhere('openedBuild')->eq($build)->fi()
                ->beginIF($childType)->andWhere('childType')->eq($childType)->fi()
                ->beginIF($sourceType == 'severity' && $severity)->andWhere('severity')->eq($severity)->fi()
                ->beginIF($sourceType == 'blackList')->andWhere('isBlackList')->eq('2')->fi()
                ->orderBy($orderBy)
                ->page($pager)
                ->fetchAll();
        }
        if($ret){
            $data = $ret;
        }
        return $data;

    }
}
