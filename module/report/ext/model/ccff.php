<?php
/* 获取问题数据。*/
public function getUserProblemList()
{
    $problems = $this->dao->select('id,code,dealUser,lastDealDate')->from(TABLE_PROBLEM)
        ->where('status')->ne('deleted')
        ->andWhere('status')->ne('closed')
        ->fetchGroup('dealUser');
    if(empty($problems)) return array();
    return $problems;
}

/* 获取需求数据。*/
public function getUserDemandList()
{
    $demands = $this->dao->select('id,code,dealUser,lastDealDate')->from(TABLE_DEMAND)
        ->where('status')->ne('deleted')
        ->andWhere('status')->ne('closed')
        ->fetchGroup('dealUser');
    if(empty($demands)) return array();
    return $demands;
}

/* 获取二线生产变更数据。*/
public function getUserModifyList()
{
    $modifys = $this->dao->select('id,code,version,reviewStage,lastDealDate')->from(TABLE_MODIFY)
        ->where('status')->in('managersuccess,systemsuccess,posuccess,leadersuccess,gmsuccess,productsuccess')
        ->andWhere('reviewStage')->gt(0)
        ->fetchAll('id');
    if(empty($modifys)) return array();

    $objectIdList = array_keys($modifys);
    $reviewerList = $this->getObjectReviewList($objectIdList, 'modify');

    $userGroupModify = array();
    foreach($reviewerList as $reviewer)
    {
        if(empty($reviewer->reviewer)) continue;
        $userGroupModify[$reviewer->reviewer][] = $modifys[$reviewer->objectID];
    }
    return $userGroupModify;
}

/* 获取数据修正数据。*/
public function getUserFixList()
{
    $infos = $this->dao->select('id,code,version,reviewStage,lastDealDate')->from(TABLE_INFO)
        ->where('action')->eq('fix')
        ->andWhere('status')->in('reject,cmconfirmed,managersuccess,systemsuccess,posuccess,leadersuccess,gmsuccess,productsuccess')
        ->andWhere('reviewStage')->gt(0)
        ->fetchAll('id');

    if(empty($infos)) return array();

    $objectIdList = array_keys($infos);
    $reviewerList = $this->getObjectReviewList($objectIdList, 'info');

    $userGroupInfo = array();
    foreach($reviewerList as $reviewer)
    {
        if(empty($reviewer->reviewer)) continue;
        $userGroupInfo[$reviewer->reviewer][] = $infos[$reviewer->objectID];
    }

    return $userGroupInfo;
}

/* 获取数据获取数据。*/
public function getUserGainList()
{
    $infos = $this->dao->select('id,code,version,reviewStage,lastDealDate')->from(TABLE_INFO)
        ->where('action')->eq('gain')
        ->andWhere('status')->in('reject,cmconfirmed,managersuccess,systemsuccess,posuccess,leadersuccess,gmsuccess,productsuccess')
        ->andWhere('reviewStage')->gt(0)
        ->fetchAll('id');

    if(empty($infos)) return array();

    $objectIdList = array_keys($infos);
    $reviewerList = $this->getObjectReviewList($objectIdList, 'info');

    $userGroupInfo = array();
    foreach($reviewerList as $reviewer)
    {
        if(empty($reviewer->reviewer)) continue;
        $userGroupInfo[$reviewer->reviewer][] = $infos[$reviewer->objectID];
    }

    return $userGroupInfo;
}

/* 获取数据获取数据。*/
public function getUserGainqzList()
{
    $infos = $this->dao->select('id,code,version,reviewStage,lastDealDate')->from(TABLE_INFO_QZ)
        ->where('action')->eq('gain')
        ->andWhere('status')->in('reject,cmconfirmed,managersuccess,systemsuccess,posuccess,leadersuccess,gmsuccess,productsuccess')
        ->andWhere('reviewStage')->gt(0)
        ->fetchAll('id');

    if(empty($infos)) return array();

    $objectIdList = array_keys($infos);
    $reviewerList = $this->getObjectReviewList($objectIdList, 'info');

    $userGroupInfo = array();
    foreach($reviewerList as $reviewer)
    {
        if(empty($reviewer->reviewer)) continue;
        $userGroupInfo[$reviewer->reviewer][] = $infos[$reviewer->objectID];
    }

    return $userGroupInfo;
}

/* 获取项目计划数据。*/
public function getUserProjectplanList()
{
    $plans = $this->dao->select('id,status,name,lastDealDate')->from(TABLE_PROJECTPLAN)
        ->where('status')->in('yearwait,yearreviewing,wait,reviewing')
        ->fetchAll('id');

    if(empty($plans)) return array();

    $objectIdList = array_keys($plans);

    $markPairs    = $this->dao->select('plan,mark')->from(TABLE_PROJECTCREATION)->where('plan')->in($objectIdList)->fetchPairs();
    $reviewerList = $this->getObjectReviewList($objectIdList, 'projectplan,projectplanyear');

    $userGroupInfo = array();
    foreach($reviewerList as $reviewer)
    {
        if(empty($reviewer->reviewer)) continue;

        $plan = $plans[$reviewer->objectID];
        if($plan->status == 'yearwait' or $plan->status == 'yearreviewing')
        {
            $plan->mark = '';
        }
        else
        {
            $plan->mark = zget($markPairs, $plan->id, $plan->id);
        }
        $userGroupInfo[$reviewer->reviewer][] = $plan;
    }

    return $userGroupInfo;
}

/* 获取项目计划数据。*/
public function getUserReviewList()
{
    $reviews = $this->dao->select('id,project,title,reviewer,deadline as lastDealDate')->from(TABLE_REVIEW)
        ->where('deleted')->eq(0)
        ->andWhere('status')->in(array('wait', 'reviewing'))
        ->fetchAll();
    if(empty($reviews)) return array();

    $projectIdList = array();
    $userGroupInfo = array();
    foreach($reviews as $review)
    {
        if($review->project) $projectIdList[$review->project] = $review->project;
    }
    $markPairs = $this->dao->select('project,mark')->from(TABLE_PROJECTPLAN)->where('project')->in($projectIdList)->fetchPairs();

    foreach($reviews as $review)
    {
        if(empty($review->reviewer)) continue;

        $review->mark = empty($review->project) ? '' : $markPairs[$review->project];
        $userGroupInfo[$review->reviewer][] = $review;
    }

    return $userGroupInfo;
}

/* 获取项目变更数据。*/
public function getUserChangeList()
{
    $changes = $this->dao->select('id,code,project,createdDate as lastDealDate')->from(TABLE_CHANGE)
        ->where('status')->in('wait,cmconfirmed,managersuccess,leadersuccess,gmsuccess')
        ->fetchAll('id');

    if(empty($changes)) return array();

    $objectIdList = array_keys($changes);
    $reviewerList = $this->getObjectReviewList($objectIdList, 'change');

    $projectIdList = array();
    foreach($changes as $change)
    {
        if($change->project) $projectIdList[$change->project] = $change->project;
    }
    $markPairs = $this->dao->select('project,mark')->from(TABLE_PROJECTPLAN)->where('project')->in($projectIdList)->fetchPairs();

    $userGroupInfo = array();
    foreach($reviewerList as $review)
    {
        if(empty($review->reviewer)) continue;

        $change = $changes[$review->objectID];
        $change->mark         = empty($change->project) ? '' : $markPairs[$change->project];
        $change->lastDealDate = empty($change->lastDealDate) ? '' : substr($change->lastDealDate, 0, 10);
        $userGroupInfo[$review->reviewer][] = $change;
    }

    return $userGroupInfo;
}

/* 获取需求条目数据。*/
public function getUserRequirementList()
{
    $requirements = $this->dao->select('id,name,changedDate as lastDealDate')->from(TABLE_REQUIREMENT)
        ->where('status')->eq('reviewing')
        ->fetchAll('id');

    if(empty($requirements)) return array();

    $objectIdList = array_keys($requirements);
    $reviewerList = $this->getObjectReviewList($objectIdList, 'requirement');

    $userGroupInfo = array();
    foreach($reviewerList as $review)
    {
        if(empty($review->reviewer)) continue;
        $userGroupInfo[$review->reviewer][] = $requirements[$review->objectID];;
    }

    return $userGroupInfo;
}

public function getObjectReviewList($objectIdList, $objectType)
{
    $reviewerList = $this->dao->select('t1.objectID,t2.reviewer')->from(TABLE_REVIEWNODE)->alias('t1')
        ->leftjoin(TABLE_REVIEWER)->alias('t2')->on('t1.id=t2.node')
        ->where('t1.status')->eq('pending')
        ->andWhere('t1.objectType')->in($objectType)
        ->andWhere('t1.objectID')->in($objectIdList)
        ->andWhere('t2.status')->eq('pending')
        ->fetchAll();
    return $reviewerList;
}

public function getStagesByProjectID($projectID = 0)
{
    $stages = $this->loadModel('project')->getReportStageOrderByProject($projectID);

    $this->loadModel('execution');
    foreach($stages as $id => $stage)
    {
        if(!isset($stage->tasks)) $stage->tasks = 0;
        if($stage->grade == 2)
        {
            $stage->tasks = $this->execution->getExecutionTaskCount($stage->id);
        }

        if($stage->parent) $stages[$stage->parent]->tasks += $stage->tasks;
    }

    return $stages;
}

public function getExecutionStaff($executionIdList)
{
    return $this->dao->select('execution, count(distinct account) as count')
        ->from(TABLE_EFFORT)
        ->where('objectType')->eq('task')
        ->andWhere('deleted')->eq('0')
        ->andWhere('execution')->in($executionIdList)
        ->groupBy('execution')
        ->fetchPairs();
}

public function getChildrenStagePersonnelWorkload($stages, $begin, $end, $assignAccount,$projectID)
{
    //查询项目信息
    $project = $this->dao->select('*')->from(TABLE_PROJECT)->where('id')->eq($projectID)->fetch();
    $stageWorkloadList = array();

    // 获取所有用户账号、父阶段跨行、子阶段跨行。
    $accounts = array();
    $queryAccounts = array();
    $rowspanParent = array();
    $rowspanChild  = array();

    foreach($stages as $stageID => $stage)
    {
        if(!$stage->parent)
        {
            $rowspanParent[$stage->id]['user']  = 0;
            $rowspanParent[$stage->id]['dept']  = 0;
            $rowspanParent[$stage->id]['child'] = 0;
            continue;
        }

        $userWorkload = $this->dao->select('concat(account,"/",ifnull(deptID,0)) as info,sum(consumed) as total')->from(TABLE_EFFORT)
            ->where('objectType')->eq('task')
            ->andWhere('project')->eq($stage->project)
            ->andWhere('execution')->eq($stageID)
            ->andWhere('deleted')->eq('0')
            ->beginIF($begin and $end)->andWhere('`date`')->between($begin, $end)->fi()
            ->beginIF(!empty($assignAccount))->andWhere('account')->in($assignAccount)->fi()
            ->groupBy('account, deptID')
            ->fetchAll();

        $rowspanChild[$stage->id]['user'] = 0;
        $rowspanParent[$stage->parent]['child'] += 1;
        foreach($userWorkload as $index => $workload)
        {
            $info = explode('/',$workload->info);
            if(!in_array($info[0], $accounts)){
                $accounts[] = $info[0];
            }
            $workload->total    = sprintf('%.2f', $workload->total);
            $workload->perMonth = round(($workload->total / $project->workHours) / 8, 2);

            if(!empty($assignAccount) and !in_array($info[0], $assignAccount))
            {
                unset($userWorkload[$index]);
                continue;
            }

            $rowspanParent[$stage->parent]['user'] += 1;
            $rowspanChild[$stage->id]['user']      += 1;
        }

        $stageWorkloadList[$stageID] = $userWorkload;
    }

    // 查询出每个用户部门信息。
    $userDeptList = $this->dao->select('account,realname,dept,employeeNumber')->from(TABLE_USER)->where('account')->in($accounts)->fetchAll('account');

    foreach($stageWorkloadList as $index => $stageWorkload)
    {
        foreach($stageWorkload as $user)
        {
            $info = explode('/',$user->info);
            $user->realname = $userDeptList[$info[0]]->realname;
            $user->account = $userDeptList[$info[0]]->account;
            $user->employeeNumber = $userDeptList[$info[0]]->employeeNumber;
            $user->dept     = $info[1];

            $queryAccounts[$user->account] = $user->realname;
        }
        // 把用户按照部门进行分组。
        $stageWorkloadList[$index] = array();
        foreach($stageWorkload as $user)
        {
            $stageWorkloadList[$index][$user->dept][] = $user;
        }
    }

    foreach($stages as $stage)
    {
        if(!$stage->parent) continue;

        $rowspanChild[$stage->id]['dept']       = count($stageWorkloadList[$stage->id]);
        $rowspanParent[$stage->parent]['dept'] += count($stageWorkloadList[$stage->id]);
    }

    return array('stageWorkloadList' => $stageWorkloadList, 'accounts' => $queryAccounts, 'rowspanParent' => $rowspanParent, 'rowspanChild' => $rowspanChild);
}

public function getReportWorkloadUserPairs($projectID)
{
    $userGroups = $this->dao->select('account')->from(TABLE_EFFORT)->where('objectType')->eq('task')
        ->andWhere('project')->eq($projectID)
        ->andWhere('deleted')->eq('0')
        ->groupBy('account')
        ->fetchAll();

    $accounts = array();
    foreach($userGroups as $workload) $accounts[] = $workload->account;

    // 查询出每个用户部门信息。
    $userPairs = $this->dao->select('account,realname')->from(TABLE_USER)->where('account')->in($accounts)->fetchPairs();

    return $userPairs;
}

public function getPersonnelWorkloadDetail($projectID, $begin, $end, $account)
{
    // 查询出工时消耗信息。
    $workloads = $this->dao->select('id,objectID,execution,account,`work`,date,`left`,consumed,deptID,realDate')->from(TABLE_EFFORT)
        ->where('objectType')->eq('task')
        ->andWhere('project')->eq($projectID)
        ->beginIF($begin and $end)->andWhere('`date`')->between($begin, $end)->fi()
        ->beginIF(!empty($account))->andWhere('account')->in($account)->fi()
        ->andWhere('deleted')->eq('0')
        ->orderBy('id_desc')
        ->fetchAll();

    $tasks      = array();
    $accounts   = array();
    $executions = array();
    $userEmployeeNumbers    = $this->dao->select('account,employeeNumber')->from(TABLE_USER)->where('deleted')->eq('0')->fetchPairs('account');

    foreach($workloads as $workload)
    {
        $tasks[$workload->objectID]       = $workload->objectID;
        $accounts[$workload->account]     = $workload->account;
        $workload->employeeNumber     = $userEmployeeNumbers[$workload->account];
        $executions[$workload->execution] = $workload->execution;
    }

    $taskPairs      = $this->dao->select('id,name,progress,estStarted,deadline')->from(TABLE_TASK)->where('id')->in($tasks)->fetchAll('id');
    $userPairs      = $this->dao->select('account,realname')->from(TABLE_USER)->where('account')->in($accounts)->fetchPairs();

    $executionPairs = $this->loadModel('project')->getStagesByProject($projectID, true);

    foreach($workloads as $workload)
    {
        $workload->left     = sprintf('%.2f', $workload->left);
        $workload->consumed = sprintf('%.2f', $workload->consumed);

        $workload->executionName = zget($executionPairs, $workload->execution);
        $workload->realname      = zget($userPairs, $workload->account);

        $taskDetail = zget($taskPairs, $workload->objectID, '');
        if(empty($taskDetail))
        {
            $workload->taskName   = $this->lang->noData;
            $workload->estStarted = $this->lang->noData;
            $workload->deadline   = $this->lang->noData;
            $workload->progress   = $this->lang->noData;
        }
        else
        {
            $workload->taskName   = $taskDetail->name;
            $workload->estStarted = $taskDetail->estStarted;
            $workload->deadline   = $taskDetail->deadline;
            $workload->progress   = $taskDetail->progress;
        }
    }

    return $workloads;
}

// 根据项目获取评审数据
public function getReviewInfoByProjectId($reviewID){
    return $this->dao->select('t3.name,t3.mark,t3.code,t1.id,t1.title,t1.status,t1.createdBy,t1.createdDept,t1.type,t1.project,t1.version,t1.owner,t1.qa,t1.qualityCm,t1.createdDate,t1.closeTime,t1.suspendTime,t1.renewTime,t2.realExport')->from(TABLE_REVIEW)->alias('t1')
        ->leftjoin(TABLE_REVIEW_MEETING_DETAIL)->alias('t2')->on('t2.review_id =  t1.id')
        ->leftjoin(TABLE_PROJECTPLAN)->alias('t3')->on('t3.project =  t1.project')
        ->where('t1.deleted')->eq(0)
        ->beginIF($reviewID)->andWhere('t1.id')->in($reviewID)->fi()
        ->orderBy('t1.id_asc')
        ->fetchAll('id');
}

// 获取评审数据
public function getReviewLists($reviewIds){
    // 获取评审id范围
    return $this->dao->select('objectID,reviewStage,consumed')->from(TABLE_CONSUMED)
        ->where('objectType')->eq('review')
        ->beginIF($reviewIds)->andWhere('objectID')->in($reviewIds)->fi()
        ->andWhere('deleted')->eq('0')
        ->orderBy('objectID_desc,id_asc')
        ->fetchAll();
}

// 从报表中获取关联工作量数据
public function getReviewListsSearch($table, $projectID, $begin = '', $end = '', $assignAccount = '', $group, $fetch){
    // 获取评审id范围
    return $this->dao->select('t1.*')->from($table)->alias('t1')
        ->leftjoin(TABLE_CONSUMED)->alias('t2')->on('t2.objectID =  t1.reviewID')
        ->where('t1.projectID')->eq($projectID)
        ->andWhere('t2.objectType')->eq('review')
        ->andWhere('t1.deleted')->eq('0')
        ->andWhere('t2.deleted')->eq('0')
        ->beginIF($begin and $end)->andWhere('t2.`createdDate`')->between($begin, $end)->fi()
        ->beginIF(!empty($assignAccount))->andWhere('t1.blockMember')->in($assignAccount)->fi()
        ->groupBy($group)
        ->orderBy('reviewID_desc')
        ->fetchAll($fetch);
}

// 获取评审流转耗时工作量
public function getReviewStages($reviewIds, $begin, $end){
    return $this->dao->select('objectID,reviewStage,createdDate')->from(TABLE_CONSUMED)
        ->where('objectType')->eq('review')
        ->beginIF($reviewIds)->andWhere('objectID')->in($reviewIds)->fi()
        ->andWhere('deleted')->eq('0')
        ->beginIF($begin and $end)->andWhere('`createdDate`')->between($begin, $end)->fi()
        ->orderBy('objectID_desc,id_asc')
        ->fetchAll();
}

// 获取实际在线评审专家或验证人
public function getMember($reviewIDs, $nodeCode){
    // 查询所有用户真实姓名
    $accounts = $this->dao->select('account,realname')->from(TABLE_USER)->fetchPairs();
    $nodeIDs = [];$nodeReviewers = [];$res = [];
    $resNodeIDs = $this->dao->select('id,objectID')->from(TABLE_REVIEWNODE)
        ->where('objectType')->eq('review')
        ->beginIF($reviewIDs)->andWhere('objectID')->in($reviewIDs)->fi()
        ->andWhere('nodeCode')->eq($nodeCode)
        ->orderBy('version_asc')
        ->fetchAll('objectID');
    foreach($resNodeIDs as $nodeID){
        $nodeIDs[] = $nodeID->id;
    }
    $members = $this->dao->select('reviewer,node')->from(TABLE_REVIEWER)
        ->where('node')->in($nodeIDs)
        ->fetchAll();
    foreach($members as $member){
        $nodeReviewers[$member->node] .=   $accounts[$member->reviewer].',';
    }
    foreach($resNodeIDs as $reviewID => $reviewInfo){
        $res[$reviewID] = $nodeReviewers[$reviewInfo->id] ? substr($nodeReviewers[$reviewInfo->id],0,'-1'):'';
    }

    return $res;
}

// 获取所有申请时间
public function getNodeCreatedDate($list, $param, $before = '', $beforeParam = ''){
    return $this->dao->select('objectID,createdDate')->from(TABLE_CONSUMED)
        ->where('objectType')->eq('review')
        ->andWhere('after')->eq($param)
        ->beginIF($before)->andWhere('`before`')->eq($beforeParam)->fi()
        ->beginIF($list)->andWhere('objectID')->in($list)->fi()
        ->orderBy('id_desc')
        ->fetchPairs();
}

// 根据项目获取评审数据
public function getAllReviewConsumedList($reviewID){
    return $this->dao->select('t4.name,t4.mark,t4.code,t1.id,t1.title,t1.status,t1.createdBy,t1.createdDept,t1.type,t1.project,t1.version,t1.owner,t1.qa,t1.qualityCm,t1.createdDate,t1.closeTime,t1.suspendTime,t1.renewTime,t2.account,t2.deptId,format(sum(t2.consumed),2) as workload,t3.realExport')->from(TABLE_REVIEW)->alias('t1')
        ->leftjoin(TABLE_CONSUMED)->alias('t2')->on('t2.objectID=t1.id')
        ->leftjoin(TABLE_REVIEW_MEETING_DETAIL)->alias('t3')->on('t3.review_id =  t1.id')
        ->leftjoin(TABLE_PROJECTPLAN)->alias('t4')->on('t4.project =  t1.project')
        ->where('t2.objectType')->eq('review')
        ->beginIF($reviewID)->andWhere('t1.id')->in($reviewID)->fi()
        ->andWhere('t1.deleted')->eq(0)
        ->andWhere('t2.deleted')->eq('0')
        ->groupBy('t2.account, t2.deptId, t2.objectID')
        ->orderBy('t1.id_desc')
        ->fetchAll();
}

// 查询需要修改的评审数据
public function getNewReviews($maxInsertTime){
    $sql = "SELECT t1.id FROM ".TABLE_REVIEW." AS t1 LEFT JOIN ".TABLE_CONSUMED." AS t2 ON t1.id = t2.objectID WHERE ( t1.editDate > '".$maxInsertTime."' OR t1.createdDate > '".$maxInsertTime."' OR t2.createdDate > '".$maxInsertTime."' ) AND t2.objectType = 'review' AND t1.deleted = '0' AND t2.deleted = '0' GROUP BY t1.id;";
    return $this->dao->query($sql)->fetchAll();
}

/**
 * Calculate the project name.
 *
 * @param  array  $projects
 * @param  array  $projectIdList
 * @access public
 * @return string
 */
public function calculateProject($projects, $projectIdList)
{
    if(empty($projectIdList)) return '';
    if(is_string($projectIdList)) $projectIdList = explode(',', $projectIdList);

    $projectNameList = array();
    foreach($projectIdList as $projectID)
    {
        $projectName = isset($projects[$projectID]) ? $projects[$projectID] : 0;
        if(!$projectName) continue;
        $projectNameList[] = $projectName;
    }
    return implode(',', $projectNameList);
}

/**
 * Calculate the testcase name.
 *
 * @param  array  $projectTaskPairs
 * @param  array  $projectIdList
 * @access public
 * @return string
 */
public function calculateTestcase($projectTaskPairs, $projectIdList)
{
    if(empty($projectIdList)) return '';
    if(is_string($projectIdList)) $projectIdList = explode(',', $projectIdList);

    $testcaseNameList = array();
    foreach($projectIdList as $projectID)
    {
        $testcaseName = isset($projectTaskPairs[$projectID]) ? $projectTaskPairs[$projectID] : '';
        if(!$testcaseName) continue;
        $testcaseNameList[] = $testcaseName;
    }
    return implode(',', $testcaseNameList);
}

/**
 * Calculate the ratio.
 *
 * @param  array  $part
 * @param  array  $total
 * @access public
 * @return string
 */
public function calculatePercentage($part, $total)
{
    if(empty($total)) return '0.00%';
    $percentage = $part / $total * 100;

    $decimalPlaces = strlen(substr(strrchr($percentage, "."), 1));

    if($decimalPlaces <= 2)
    {
        $percentageFormatted = number_format($percentage, 2, '.', '') . '%';
    }
    else
    {
        $percentageFormatted = number_format($percentage, 2, '.', '') . '%';
    }

    return $percentageFormatted;
}

/**
 * Get the valid bug created.
 *
 * @param  array  $userList
 * @param  array  $productList
 * @param  array  $projectList
 * @access public
 * @return string
 */
public function getBugDiscoveryToCreate($userList, $productList, $projectList)
{
    $userAccountList = array_keys($userList);
    $createBugs = $this->dao->select('openedBy,count(*) as bugTotal,group_concat(distinct project) as projects')->from(TABLE_BUG)
        ->where('deleted')->eq(0)
        ->andWhere('resolution')->notin(array('duplicate', 'external', 'bydesign'))
        ->andWhere('openedBy')->in($userAccountList)
        ->beginIF(!empty($productList))->andWhere('product')->in($productList)->fi()
        ->beginIF(!empty($projectList))->andWhere('project')->in($projectList)->fi()
        ->groupBy('openedBy')
        ->fetchAll('openedBy');

    foreach($userList as $user)
    {
        $createBug = new stdClass();
        $createBug->bugTotal = 0;
        $createBug->projects = '';

        $createData = zget($createBugs, $user->account, $createBug);

        $user->createBugTotal = $createData->bugTotal;
        $user->projects       = $createData->projects;
    }
    return $userList;
}

/**
 * Get the valid bug confirm.
 *
 * @param  array  $userList
 * @param  array  $productList
 * @param  array  $projectList
 * @access public
 * @return string
 */
public function getBugDiscoveryToConfirm($userList, $productList, $projectList)
{
    $userAccountList = array_keys($userList);
    $confirmBugs = $this->dao->select('dealedBy,count(*) as defectTotal')->from(TABLE_DEFECT)
        ->where('deleted')->eq('0')
        ->andWhere('dealedBy')->in($userAccountList)
        ->beginIF(!empty($productList))->andWhere('product')->in($productList)->fi()
        ->beginIF(!empty($projectList))->andWhere('project')->in($projectList)->fi()
        ->groupBy('dealedBy')
        ->fetchPairs('dealedBy');

    foreach($userList as $user)
    {
        $defectTotal = zget($confirmBugs, $user->account, 0);
        $user->defectTotal = $defectTotal;
    }
    return $userList;
}

/**
 * Get the valid bug assign.
 *
 * @param  array  $userList
 * @param  array  $productList
 * @param  array  $projectList
 * @access public
 * @return string
 */
public function getBugDiscoveryToAssign($userList, $productList, $projectList)
{
    foreach($userList as $user)
    {
        $discoveryBugs = $this->dao->select('count(*) as bugTotal,group_concat(distinct project) as projects')->from(TABLE_BUG)
            ->where('deleted')->eq(0)
            ->andWhere('resolution')->notin(array('duplicate', 'external', 'bydesign'))
            ->andWhere('openedBy')->ne($user->account)
            ->andWhere('')->markLeft(1)->where('assignedTo')->eq($user->account)->orWhere('resolvedBy')->eq($user->account)->markRight(1)
            ->beginIF(!empty($productList))->andWhere('product')->in($productList)->fi()
            ->beginIF(!empty($projectList))->andWhere('project')->in($projectList)->fi()
            ->fetch();

        $user->discoveryBugTotal = $discoveryBugs->bugTotal;
        $projectIdList = $user->projects . ',' . $discoveryBugs->projects;
        $projectIdList = trim($projectIdList, ',');
        $projectIdList = explode(',', $projectIdList);
        $projectIdList = array_filter($projectIdList);
        $projectIdList = array_unique($projectIdList);

        $user->projects = $projectIdList;
    }
    return $userList;
}

/**
 * Get the valid bug assign.
 *
 * @param  array  $projectDataList
 * @access public
 * @return string
 */
public function getBugEscapeList($projectDataList)
{
    $accountList = array();
    foreach($projectDataList as $projectID => $project)
    {
        if(empty($project->PM)) continue;
        $accountList[$project->PM] = $project->PM;
    }
    $userDeptList = $this->dao->select('t1.account,t2.path')->from(TABLE_USER)->alias('t1')
        ->leftJoin(TABLE_DEPT)->alias('t2')->on('t1.dept=t2.id')
        ->where('t1.account')->in($accountList)
        ->fetchPairs();
    foreach($userDeptList as $account => $deptPath)
    {
        $deptPath = trim($deptPath, ',');
        $deptPath = explode(',', $deptPath);

        if(!empty($deptPath)) $accountList[$account] = $deptPath[0];
    }

    $projectIdList = array_keys($projectDataList);

    $projectBugs = $this->dao->select('project,count(*) as bugTotal')->from(TABLE_BUG)
        ->where('deleted')->eq('0')
        ->andWhere('resolution')->notin(array('duplicate', 'external', 'bydesign'))
        ->beginIF(!empty($projectIdList))->andWhere('project')->in($projectIdList)->fi()
        ->groupBy('project')
        ->fetchPairs();

    $defectBugs = $this->dao->select('project,count(*) as bugTotal')->from(TABLE_DEFECT)
        ->where('deleted')->eq('0')
        ->andWhere('createdBy')->eq('guestcn')
        ->beginIF(!empty($projectIdList))->andWhere('project')->in($projectIdList)->fi()
        ->groupBy('project')
        ->fetchPairs();

    foreach($projectDataList as $projectID => $project)
    {
        $project->dept        = isset($accountList[$project->PM]) ? $accountList[$project->PM] : '';
        $project->bugTotal    = isset($projectBugs[$projectID]) ? $projectBugs[$projectID] : 0;
        $project->defectTotal = isset($defectBugs[$projectID])  ? $defectBugs[$projectID]  : 0;
        $project->rate        = $this->calculatePercentage($project->defectTotal, $project->bugTotal + $project->defectTotal);
    }
    return $projectDataList;
}

/**
 * Get the total number of bugs grouped by personnel.
 *
 * @param  array  $userAccountList
 * @param  array  $projectList
 * @param  string $begin
 * @param  string $end
 * @param  string $skipUserAccountList
 * @access public
 * @return string
 */
public function getTesterBugList($userAccountList, $projectList, $begin, $end, $skipUserAccountList = false)
{
    if($skipUserAccountList) return [];

    $allBugs = $this->dao->select('openedBy,count(*) as createTotal,group_concat(distinct project) as projects')->from(TABLE_BUG)
        ->where('deleted')->eq('0')
        ->beginIF(!empty($userAccountList))->andWhere('openedBy')->in($userAccountList)->fi()
        ->beginIF(!empty($projectList))->andWhere('project')->in($projectList)->fi()
        ->beginIF(!empty($begin))->andWhere('openedDate')->between($begin, $end)->fi()
        ->groupBy('openedBy')
        ->fetchAll('openedBy');

    return $allBugs;
}

/**
 * Get the total number of bugs grouped by personnel.
 *
 * @param  array  $userInfoList
 * @param  array  $userAccountList
 * @param  array  $projectList
 * @param  string $begin
 * @param  string $end
 * @access public
 * @return string
 */
public function getTesterEffectiveBugList($userAccountList, $projectList, $begin, $end, $skipUserAccountList = false)
{
    if($skipUserAccountList) return [];

    $effectiveBugs = $this->dao->select('openedBy,count(*) as effectTotal,group_concat(distinct project) as projects')->from(TABLE_BUG)
        ->where('deleted')->eq('0')
        ->beginIF(!empty($userAccountList))->andWhere('openedBy')->in($userAccountList)->fi()
        ->beginIF(!empty($projectList))->andWhere('project')->in($projectList)->fi()
        ->beginIF(!empty($begin))->andWhere('openedDate')->between($begin, $end)->fi()
        ->andWhere('resolution')->notin(array('duplicate', 'external', 'bydesign'))
        ->groupBy('openedBy')
        ->fetchAll('openedBy');

    return $effectiveBugs;
}

public function mergeDataByOpenedBy($userList, $userAccountList, $createBugs, $effectBugs, $cases, $runs)
{
    if(empty($userAccountList))
    {
        if(!empty($createBugs)) $userAccountList = array_merge($userAccountList, array_keys($createBugs));
        if(!empty($effectBugs)) $userAccountList = array_merge($userAccountList, array_keys($effectBugs));
        if(!empty($cases))      $userAccountList = array_merge($userAccountList, array_keys($cases));
        if(!empty($runs))       $userAccountList = array_merge($userAccountList, array_keys($runs));
    }
    
    foreach($userAccountList as $account)
    {
        if(!isset($createBugs[$account]) && !isset($effectBugs[$account]) && !isset($cases[$account]) && !isset($runs[$account])) continue;

        $user = new stdClass();
        $user->account        = $account;
        $user->createBugTotal = 0;
        $user->effectBugTotal = 0;
        $user->caseTotal      = 0;
        $user->runTotal       = 0;
        $user->projects       = array();

        if(isset($createBugs[$account]))
        {
            $user->createBugTotal = $createBugs[$account]->createTotal;

            $projects       = explode(',', $createBugs[$account]->projects);
            $user->projects = array_unique(array_merge($user->projects, $projects));;
        }

        if(isset($effectBugs[$account]))
        {
            $user->effectBugTotal = $effectBugs[$account]->effectTotal;

            $projects       = explode(',', $effectBugs[$account]->projects);
            $user->projects = array_unique(array_merge($user->projects, $projects));;
        }

        if(isset($cases[$account]))
        {
            $user->caseTotal = $cases[$account]->caseTotal;
            
            $projects       = explode(',', $cases[$account]->projects);
            $user->projects = array_unique(array_merge($user->projects, $projects));;
        }

        if(isset($runs[$account]))
        {
            $user->runTotal = $runs[$account]->runTotal;

            $projects       = explode(',', $runs[$account]->projects);
            $user->projects = array_unique(array_merge($user->projects, $projects));;
        }

        $userList[$user->account] = $user;
    }
    return $userList;
}

/**
 * Get the test order data under the project.
 *
 * @param  array  $queryProjectList
 * @access public
 * @return string
 */
public function getProjectTaskList($queryProjectList)
{
    if(empty($queryProjectList)) return array();

    $testtaskPairs = $this->dao->select('project,group_concat(oddNumber) as oddNumber')->from(TABLE_TESTTASK)
        ->where('deleted')->eq('0')
        ->andWhere('project')->in($queryProjectList)
        ->groupBy('project')
        ->fetchPairs();
    return $testtaskPairs;
}

/**
 * Get the total number of cases grouped by personnel.
 *
 * @param  array  $userInfoList
 * @param  array  $userAccountList
 * @param  array  $projectList
 * @param  string $begin
 * @param  string $end
 * @access public
 * @return string
 */
public function getTesterCaseList( $userAccountList, $projectList, $begin, $end, $skipUserAccountList = false)
{
    if($skipUserAccountList) return [];

    $cases = $this->dao->select('openedBy,count(*) as caseTotal,group_concat(distinct project) as projects')->from(TABLE_CASE)
        ->where('deleted')->eq('0')
        ->beginIF(!empty($userAccountList))->andWhere('openedBy')->in($userAccountList)->fi()
        ->beginIF(!empty($projectList))->andWhere('project')->in($projectList)->fi()
        ->beginIF(!empty($begin))->andWhere('openedDate')->between($begin, $end)->fi()
        ->groupBy('openedBy')
        ->fetchAll('openedBy');
        
    return $cases;
}

/**
 * Get the total number of cases grouped by personnel.
 *
 * @param  array  $userInfoList
 * @param  array  $userAccountList
 * @param  array  $projectList
 * @param  string $begin
 * @param  string $end
 * @access public
 * @return string
 */
public function getTesterCaseRunList($userAccountList, $projectList, $begin, $end, $skipUserAccountList = false)
{
    if($skipUserAccountList) return [];

    $runs = $this->dao->select('t2.lastRunner as openedBy,count(t2.case) as runTotal,group_concat(distinct t1.project) as projects')->from(TABLE_CASE)->alias('t1')
        ->leftJoin(TABLE_TESTRESULT)->alias('t2')->on('t1.id=t2.case')
        ->where('t1.deleted')->eq('0')
        ->beginIF(!empty($projectList))->andWhere('t1.project')->in($projectList)->fi()
        ->beginIF(!empty($userAccountList))->andWhere('t2.lastRunner')->in($userAccountList)->fi()
        ->beginIF(!empty($begin))->andWhere('t2.date')->between($begin, $end)->fi()
        ->groupBy('t2.lastRunner')
        ->fetchAll('openedBy');

    return $runs;
}

/**
 * Get the total number of cases grouped by personnel.
 *
 * @param  string $begin
 * @param  string $end
 * @param  array  $projectList
 * @param  array  $testtaskList
 * @param  string $chartMode
 * @access public
 * @return string
 */
public function getTrendData($begin, $end, $projectList, $testtaskList, $chartMode = '')
{
    if(empty($end)) $end = date('Y-m-d');

    $projectCondition  = empty($projectList) ? '' : "and zt_bug.project in (" . implode(',', $projectList) . ")";

    $bugIds      = $this->getBugByTesttask($testtaskList);
    $idCondition = empty($testtaskList) ? '' : "and zt_bug.id " . helper::dbIN($bugIds);

    if(empty($begin))
    {
        $theFirstBug = $this->dao->select('openedDate')->from(TABLE_BUG)
            ->where('deleted')->eq('0')
            ->andWhere('project')->in($projectList)
            ->beginIF($testtaskList)->andWhere('id')->in($bugIds)->fi()
            ->orderBy('openedDate asc')
            ->limit(1)
            ->fetch();

        if($theFirstBug)
        {
            if(strtotime($end) < strtotime($theFirstBug->openedDate)) die(js::error($this->lang->report->endDateOutFirst) . js::locate('back'));
            $begin = $theFirstBug->openedDate;
        }
        else
        {
            $begin = date('Y-m-d', strtotime('-30 days'));
        }
    }

    if(strtotime($begin) > strtotime($end)) die(js::error($this->lang->report->greaterEndDate) . js::locate('back'));

    $createSql = "SELECT DATE_RANGE.Date AS BugDate, IFNULL(COUNT(zt_bug.id), 0) AS DailyBugCount
FROM (
    SELECT DATE_ADD('" . $begin . "', INTERVAL d DAY) AS Date
    FROM (
        SELECT t0 + t1 * 10 + t2 * 100 AS d
        FROM
        (SELECT 0 AS t0 UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) AS T0,
        (SELECT 0 AS t1 UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) AS T1,
        (SELECT 0 AS t2 UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) AS T2
    ) AS DateSequence
    WHERE DATE_ADD('" . $begin . "', INTERVAL d DAY) <= '" . $end . "'
) AS DATE_RANGE
LEFT JOIN zt_bug ON DATE(DATE_RANGE.Date) = DATE(zt_bug.openedDate)  and zt_bug.deleted = '0'
 " . $projectCondition . " " . $idCondition . "
GROUP BY DATE_RANGE.Date
ORDER BY DATE_RANGE.Date;";
    $createData  = $this->dao->query($createSql)->fetchAll();
    $createPairs = array();
    foreach($createData as $data) $createPairs[$data->BugDate] = $data->DailyBugCount;

    // 当没有指定图表模式时，根据数据量自动选择（以创建数据为准）
    if(empty($chartMode))
    {
        $chartMode = 'day';
        if(count($createPairs) > 7)   $chartMode = 'week';
        if(count($createPairs) > 30)  $chartMode = 'month';
        if(count($createPairs) > 365) $chartMode = 'year';
    }

    $createPairs = $this->combingData($createPairs, false, $chartMode);

    $resolvedSql = "SELECT DATE_RANGE.Date AS BugDate, IFNULL(COUNT(zt_bug.id), 0) AS DailyBugCount
FROM (
    SELECT DATE_ADD('" . $begin . "', INTERVAL d DAY) AS Date
    FROM (
        SELECT t0 + t1 * 10 + t2 * 100 AS d
        FROM
        (SELECT 0 AS t0 UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) AS T0,
        (SELECT 0 AS t1 UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) AS T1,
        (SELECT 0 AS t2 UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) AS T2
    ) AS DateSequence
    WHERE DATE_ADD('" . $begin . "', INTERVAL d DAY) <= '" . $end . "'
) AS DATE_RANGE
LEFT JOIN zt_bug ON DATE(DATE_RANGE.Date) = DATE(zt_bug.resolvedDate)  and zt_bug.deleted = '0'
 " . $projectCondition . " " . $idCondition . "
GROUP BY DATE_RANGE.Date
ORDER BY DATE_RANGE.Date;";
    $resolvedSqlData = $this->dao->query($resolvedSql)->fetchAll();
    $resolvedPairs   = array();
    foreach($resolvedSqlData as $data) $resolvedPairs[$data->BugDate] = $data->DailyBugCount;
    $resolvedPairs   = $this->combingData($resolvedPairs, false, $chartMode);

    $activatedPairs = $this->getBugCountFromHistory($begin, $end, $projectList, $testtaskList, 'activated');
    $activatedPairs = $this->combingData($activatedPairs, false, $chartMode);

    $closedPairs = $this->getBugCountFromHistory($begin, $end, $projectList, $testtaskList, 'closed');
    $closedPairs = $this->combingData($closedPairs, false, $chartMode);

    $totalCreateParis = [];
    $totalCreateCount = 0;
    // 获取开始时间之前的数据
    $testtaskStr = implode(',', $testtaskList);
    $totalCreateCount = $this->dao->select('count(*) as countValue')->from(TABLE_BUG)
        ->where('project')->in($projectList)
        ->beginIF($testtaskList)->andWhere("FIND_IN_SET('{$testtaskStr}',`linkTesttask`)")->fi()
        ->andWhere('openedDate')->lt($begin)
        ->andWhere('deleted')->eq(0)
        ->fetch();
    $totalCreateCount = $totalCreateCount->countValue;

    foreach($createPairs as $date => $value)
    {
        $totalCreateCount += $value;
        $totalCreateParis[$date] = $totalCreateCount;
    }

    $totalActivatedParis = $this->getBugCountFromHistory($begin, $end, $projectList, $testtaskList, 'totalActivated', true);
    $totalActivatedParis = $this->combingData($totalActivatedParis, true, $chartMode);

    $totalToCloseParis = $this->getBugCountFromHistory($begin, $end, $projectList, $testtaskList, 'totalToClose', true);
    $totalToCloseParis = $this->combingData($totalToCloseParis, true, $chartMode);

    $totalToResolveParis = $this->getBugCountFromHistory($begin, $end, $projectList, $testtaskList, 'totalToResolve', true);
    $totalToResolveParis = $this->combingData($totalToResolveParis, true, $chartMode);

    $trendData = array();
    $trendData['lables']         = array_keys($createPairs);
    $trendData['createPairs']    = array_values($createPairs);
    $trendData['resolvedPairs']  = array_values($resolvedPairs);
    $trendData['activatedPairs'] = array_values($activatedPairs);
    $trendData['closedPairs']    = array_values($closedPairs);

    $trendData['totalCreateParis']    = array_values($totalCreateParis);
    $trendData['totalActivatedParis'] = array_values($totalActivatedParis);
    $trendData['totalToCloseParis']   = array_values($totalToCloseParis);
    $trendData['totalToResolveParis'] = array_values($totalToResolveParis);

    $trendData['chartMode'] = $chartMode;

    return $trendData;
}

/**
 * 统计BUG数据
 * 如果不是当前日期，则会按一定规则统计历史数据，但这些历史数据可能会变动
 * 因此应当首先查询历史统计表
 *
 * @param string  $begin
 * @param array   $projectList
 * @param array   $testtaskList
 * @param string  $countType
 * @return void
 */
public function countBugByReportFilter($date, $projectList, $testtaskList, $countType)
{
    if($testtaskList[0] == 0) unset($testtaskList[0]);

    $testtaskStr     = implode(',', $testtaskList);
    $todayDate       = date('Y-m-d');
    $yesterdayDate   = date('Y-m-d', strtotime('-1 day'));
    
    $begin = $date . ' 00:00:00';
    $end   = $date . ' 23:59:59';

    $commonDao = $this->dao->select('count(*) as countValue')->from(TABLE_BUG)
        ->where('project')->in($projectList)
        ->beginIF($testtaskList)->andWhere("FIND_IN_SET('{$testtaskStr}',`linkTesttask`)")->fi()
        ->andWhere('deleted')->eq(0);

    if($countType == 'totalToClose')
    {
        // 累计待关闭
        if($date == $todayDate || $date == $yesterdayDate)
        {
            // 实时数据，当前状态已解决的但是未关闭的bug
            $value = $commonDao->andWhere('status')->eq('resolved')
                ->fetch();
        }
        else
        {
            // 历史数据统计方法
            // 这一天解决了，必定存在解决日期
            // 但是没有关闭日期或关闭日期大于这一天
            $value = $commonDao->andWhere('resolvedDate')->le($end)
                ->andWhere('closedDate')->gt($end)
                ->andWhere('resolvedDate')->ne('0000-00-00 00:00:00')
                ->fetch();
        }
    }
    else if($countType == 'activated')
    {
        // 激活的bug
        // 统计这一天激活的bug
        // 即便激活后又关闭了，也算激活
        // 历史数据可能会因为多次激活而变动，因此应当首先查询历史统计表
        $value = $commonDao->andWhere('activatedDate')->ge($begin)
            ->andWhere('activatedDate')->le($end)
            ->fetch();
    }
    else if($countType == 'closed')
    {
        // 关闭的bug
        // 统计这一天关闭的bug
        // 即便只要关闭了，就算关闭
        // 历史数据可能会因为多次关闭而变动，因此应当首先查询历史统计表
        $value = $commonDao->andWhere('closedDate')->ge($begin)
            ->andWhere('closedDate')->le($end)
            ->fetch();
    }
    else if($countType == 'totalActivated')
    {
        // 累计激活的bug
        // 统计这一天之前激活的bug
        // 历史数据可能会因为多次激活而变动，因此应当首先查询历史统计表
        // 累计激活的曲线不会下降，但最多不会超过新增的bug数，即便是激活后又关闭了，也算激活的bug
        $value = $commonDao->andWhere('activatedDate')->le($end)
            ->andWhere('activatedDate')->ne('0000-00-00 00:00:00')
            ->fetch();
    }
    else if($countType == 'totalToResolve')
    {
        // 待解决
        if($date == $todayDate || $date == $yesterdayDate)
        {
            // 实时数据，当前状态已激活的但是未解决的bug
            // 如果已解决或者已关闭，都不算这一天的待解决
            $value = $commonDao->andWhere('status')->eq('active')
                ->fetch();
        }
        else
        {
            // 历史数据的推算方法
            // 解决时间是这一天（包括之前）和创建时间是这一天（包括之前）的bug
            // 历史数据不准确的原因：解决后可能关闭后重新激活
            $value = $commonDao->andWhere('resolvedDate')->lt($begin)
                ->andWhere('openedDate')->lt($begin)
                ->fetch();
        }
    }
    else
    {
        return 0;
    }

    return $value->countValue;
}

public function getBugCountFromHistory($begin, $end, $projectList, $testtaskList, $countType, $countTotal = false)
{
    // 基础数据
    $currentDate = date('Y-m-d');
    $endDate     = date('Y-m-d 23:59:59', strtotime($end));
    $dateList    = [];

    $beginTime = strtotime($begin);
    $endTime   = strtotime($endDate);

    while($beginTime <= $endTime)
    {
        $dateList[] = date('Y-m-d', $beginTime);
        $beginTime += 86400;
    }

    if(empty($testtaskList)) $testtaskList = [0];

    // 查询记录下的统计数据
    $historyData = $this->dao->select('timeKey,countValue')->from(TABLE_REPORT_HISTORY_BUG)
        ->where('countType')->eq($countType)
        ->andWhere('countTime')->ge($begin)
        ->andWhere('countTime')->le($endDate)
        ->andWhere('project')->in($projectList)
        ->andWhere('testtask')->in($testtaskList)
        ->fetchAll();

    $dataParis = [];

    foreach($dateList as $date)
    {
        $dataItem = new stdClass();
        $dataItem->BugDate = $date;
        $dataItem->DailyBugCount = 0;

        if($date == $currentDate)
        {
            // 实时统计截止目前数据
            $dataItem->DailyBugCount = $this->countBugByReportFilter($date, $projectList, $testtaskList, $countType, $countTotal);
        }
        else
        {
            $historyCount = 0;
            foreach($historyData as $value)
            {
                
                if($value->timeKey == $date)
                {
                    $dataItem->DailyBugCount += $value->countValue;
                    $historyCount++;
                }
            }
        }

        $dataParis[$date] = $dataItem->DailyBugCount;
    }

    return $dataParis;
}

/**
 * Get the bug associated with the test order.
 *
 * @param  array  $testtaskList
 * @access public
 * @return string
 */
public function getBugByTesttask($testtaskList)
{
    if(empty($testtaskList)) return array();

    $bugIdList = array();
    foreach($testtaskList as $testtaskID)
    {
        $bugIds = $this->dao->select('id')->from(TABLE_BUG)
            ->where('deleted')->eq('0')
            ->andWhere("concat(',',linkTesttask,',')")->like('%,' . $testtaskID . ',%')
            ->fetchAll();
        foreach($bugIds as $bug) $bugIdList[$bug->id] = $bug->id;
    }

    return $bugIdList;
}

/**
 * Group data by year, month and day.
 *
 * @param  array  $data
 * @param  bool   $isTotal 数据是否是累计数据
 * @param  string $chartMode 图表模式
 * @param  string $end 结束时间
 * @access public
 * @return string
 */
public function combingData($data, $isTotal = false, $chartMode = '')
{
    $groupedData = [];

    if($chartMode == 'year')
    {
        foreach($data as $date => $value)
        {
            $year = substr($date, 0, 4);
            if(!isset($groupedData[$year]))
            {
                $groupedData[$year] = 0;
            }
            if($isTotal)  $groupedData[$year] = $value;
            if(!$isTotal) $groupedData[$year] += $value;
            
        }
    }
    elseif($chartMode == 'month')
    {
        foreach($data as $date => $value)
        {
            $yearMonth = substr($date, 0, 7);
            if(!isset($groupedData[$yearMonth]))
            {
                $groupedData[$yearMonth] = 0;
            }
            if($isTotal)  $groupedData[$yearMonth] = $value;
            if(!$isTotal) $groupedData[$yearMonth] += $value;
        }
    }
    elseif($chartMode == 'week')
    {
        $dateWeekMap = [];
        foreach($data as $date => $value)
        {
            $weekStart    = date('Y-m-d', strtotime($date . ' this week monday'));
            $weekEnd      = date('Y-m-d', strtotime($weekStart . ' +6 day'));
            $weekDateName = $weekStart . " ~ \n" . $weekEnd;
            if(!isset($groupedData[$weekDateName]))
            {
                $groupedData[$weekDateName] = 0;
            }
            if($isTotal)  $groupedData[$weekDateName] = $value;
            if(!$isTotal) $groupedData[$weekDateName] += $value;
        }
    }
    else
    {
        foreach($data as $date => $value)
        {
            $date = date('Y-m-d', strtotime($date));
            if(!isset($groupedData[$date])) $groupedData[$date] = 0; 
            if($isTotal)  $groupedData[$date] = $value;
            if(!$isTotal) $groupedData[$date] += $value;
        }
    }

    return $groupedData;
}

/**
 * 制版验证申请信息汇总
 * @param $projectID
 * @param $appName
 * @param $verifyActionDate
 * @param $verifyDealUser
 */
public function getBuildWorkLoad($projectID = 0, $appName, $verifyActionDate, $verifyDealUser){

    $appName = $appName ? " and appid = '$appName'" : '';
    $verifyActionDate = $verifyActionDate ? " and verifyActionDate like '$verifyActionDate%'" : '';
    $verifyDealUser = $verifyDealUser ? " and verifyDealUser = '$verifyDealUser'" : '';
    $whereQuery  = "where 1=1 ".$appName.$verifyActionDate.$verifyDealUser;

    $projectID = $projectID != '0' ? "and b.project ='$projectID'" : '';
    $projectQuery = " and 1= 1 " .$projectID;

   $result = $this->dao->query("select * from( 
       select
        b.id,
        p.code projectCode,
        p.name projectName,
        za.id appid,
        za.name appName,
        za.code appCode,
        (select  createdDate  from zt_consumed zc where zc.objectType ='build' and zc.objectID = b.id and zc.deleted ='0' and zc.`before` ='testsuccess' order by id desc limit 1)verifyActionDate,
        (select  account  from zt_consumed zc where zc.objectType ='build' and zc.objectID = b.id and zc.deleted ='0' and zc.`before` ='testsuccess' order by id desc limit 1)verifyDealUser,
        (select  `after`  from zt_consumed zc where zc.objectType ='build' and zc.objectID = b.id and zc.deleted ='0' and zc.`before` ='testsuccess' order by id desc limit 1)status,
        b.actualVerifyDate,
        b.actualVerifyUser
    from
        zt_build b
    left join zt_project p on
        b.project = p.id
    left join zt_application za on
        b.app = za.id
    where
        b.systemverify = '1'
        and b.actualVerifyUser != ''
        and b.deleted ='0'
       {$projectQuery})a   {$whereQuery}"
   )->fetchAll();
    return $result;
}
