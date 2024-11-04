<?php
/**
 * Project: chengfangjinke
 * Method: getStats
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 17:15
 * Desc: This is the code comment. This method is called getStats.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param int $projectID
 * @param string $status
 * @param int $productID
 * @param int $branch
 * @param int $itemCounts
 * @param string $orderBy
 * @param null $pager
 * @return mixed
 */
public function getStats($projectID = 0, $status = 'undone', $productID = 0, $branch = 0, $itemCounts = 30, $orderBy = 'id_asc', $pager = null, $flag = 0)
{
    return $this->loadExtension('chengfangjinke')->getStats($projectID, $status, $productID, $branch, $itemCounts, $orderBy, $pager, $flag );
}

/**
 * Project: chengfangjinke
 * Method: getTaskStats
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 17:15
 * Desc: This is the code comment. This method is called getTaskStats.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param int $projectID
 * @param string $status
 * @return mixed
 */
public function getTaskStats($projectID = 0, $status = 'all')
{
    return $this->loadExtension('chengfangjinke')->getTaskStats($projectID, $status);
}

/**
 * Project: chengfangjinke
 * Method: printStage
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 17:15
 * Desc: This is the code comment. This method is called printStage.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param $executions
 * @param $tasks
 * @return mixed
 */
public function printStage($executions, $tasks, $grade = 1, $projectStatus = '')
{
    return $this->loadExtension('chengfangjinke')->printStage($executions, $tasks, $grade, $projectStatus);
}

/**
 * 新项目计划使用
 * @param $executions
 * @param $tasks
 * @param int $grade
 * @param string $projectStatus
 * @return mixed
 */
public function printStageNew($executions, $tasks, $grade = 1, $projectStatus = '', $projectType)
{
    return $this->loadExtension('chengfangjinke')->printStageNew($executions, $tasks, $grade, $projectStatus, $projectType);
}

/**
 * Project: chengfangjinke
 * Method: getReleases
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 17:15
 * Desc: This is the code comment. This method is called getReleases.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param $projectID
 * @return mixed
 */
public function getReleases($projectID)
{
    return $this->loadExtension('chengfangjinke')->getReleases($projectID);
}

/**
 * Project: chengfangjinke
 * Method: getReleasesList
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 17:15
 * Desc: This is the code comment. This method is called getReleasesList.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param $projectID
 * @return mixed
 */
public function getReleasesList($projectID)
{
    return $this->loadExtension('chengfangjinke')->getReleasesList($projectID);
}

/**
 * Project: chengfangjinke
 * Method: printCell
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 17:15
 * Desc: This is the code comment. This method is called printCell.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param $col
 * @param $project
 * @param $users
 * @param int $programID
 * @return mixed
 */
public function printCell($col, $project, $users, $programID = 0)
{
    return $this->loadExtension('chengfangjinke')->printCell($col, $project, $users, $programID);
}

/**
 * Project: chengfangjinke
 * Method: printCellNew
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 17:15
 * Desc: This is the code comment. This method is called printCellNew.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param $col
 * @param $project
 * @param $users
 * @param int $programID
 * @return mixed
 */
public function printCellNew($col, $project, $users, $programID = 0)
{
    return $this->loadExtension('chengfangjinke')->printCellNew($col, $project, $users, $programID);
}

/**
 * Project: chengfangjinke
 * Method: getPairs
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 17:15
 * Desc: This is the code comment. This method is called getPairs.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @return mixed
 */
public function getPairs()
{
    return $this->loadExtension('chengfangjinke')->getPairs();
}

/**
 * Project: chengfangjinke
 * Method: getProjectStats
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 17:15
 * Desc: This is the code comment. This method is called getProjectStats.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param int $programID
 * @param string $browseType
 * @param int $queryID
 * @param string $orderBy
 * @param null $pager
 * @param int $programTitle
 * @param int $involved
 * @param false $queryAll
 * @return mixed
 */
public function getProjectStats($programID = 0, $browseType = 'undone', $queryID = 0, $orderBy = 'id_desc', $pager = null, $programTitle = 0, $involved = 0, $queryAll = false)
{
    return $this->loadExtension('chengfangjinke')->getProjectStats($programID, $browseType, $queryID, $orderBy, $pager, $programTitle, $involved, $queryAll);
}

/**
 * Project: chengfangjinke
 * Method: start
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 17:15
 * Desc: This is the code comment. This method is called start.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param $projectID
 * @param string $type
 * @return mixed
 */
public function start($projectID, $type = 'project')
{
    return $this->loadExtension('chengfangjinke')->start($projectID, $type);
}

/**
 * Project: chengfangjinke
 * Method: getUserRequirementList
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 17:15
 * Desc: This is the code comment. This method is called getUserRequirementList.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param int $projectID
 * @return mixed
 */
public function getUserRequirementList($projectID = 0)
{
    return $this->loadExtension('chengfangjinke')->getUserRequirementList($projectID);
}

/**
 * Project: chengfangjinke
 * Method: buildSearchForm
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 17:15
 * Desc: This is the code comment. This method is called buildSearchForm.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param $actionURL
 * @param $queryID
 * @return mixed
 */
public function buildSearchForm($actionURL, $queryID)
{
    return $this->loadExtension('chengfangjinke')->buildSearchForm($actionURL, $queryID);
}

/**
 * Get products of a project.
 *
 * @param  int    $projectID
 * @param  bool   $withBranch
 * @access public
 * @return array
 */
public function getProducts($projectID, $withBranch = true)
{
    $query = $this->dao->select('t2.id, t2.name, t2.type, t1.branch, t1.plan,t2.code')->from(TABLE_PROJECTPRODUCT)->alias('t1')
        ->leftJoin(TABLE_PRODUCT)->alias('t2')->on('t1.product = t2.id')
        ->where('t1.project')->eq((int)$projectID)
        ->beginIF(!$this->app->user->admin)->andWhere('t1.product')->in($this->app->user->view->products)->fi()
        ->andWhere('t2.deleted')->eq(0);
    if(!$withBranch) return $query->fetchPairs('id', 'name');
    return $query->fetchAll('id');
}

/**
 * Get project members.
 *
 * @param int $projectID
 * @access public
 * @return array
 */
public function getMembersByProject($projectID)
{
    $users = $this->dao->select('t2.id, t2.account, t2.realname, t2.dept, t2.staffType,t2.employeeNumber')->from(TABLE_TEAM)->alias('t1')
        ->leftJoin(TABLE_USER)->alias('t2')->on('t1.account = t2.account')
        ->where('t1.root')->eq((int)$projectID)
        ->andWhere('t1.type')->eq('project')
        ->andWhere('t2.deleted')->eq(0)
        ->orderBy('t2.dept_asc')
        ->fetchAll();
    return $users;
}

/**
 * Get project effort.
 *
 * @param int $projectID
 * @access public
 * @return array
 */
public function getEffortByProject($projectID, $begin = 0, $end = 0, $account = '')
{
    /* 根据项目和时间范围分组查询用户工作量。 */
    $workloadPairs = $this->dao->select('concat(account,"/",deptID), cast(sum(consumed) as decimal(11,2)) as workload')->from(TABLE_EFFORT)
        ->where('project')->eq((int)$projectID)
        ->andWhere('objectType')->eq('task')
        ->beginIF($account)->andWhere('account')->eq($account)->fi()
        ->beginIF($begin && $end)->andWhere('`date`')->between($begin, $end)->fi()
        ->andWhere('deleted')->eq('0')
        ->groupBy('account, deptID')
        ->fetchPairs();
    return $workloadPairs;
}

/**
 * Get execution task.
 *
 * @param int $projectID
 * @access public
 * @return array
 */
public function getExecutionTaskByProject($projectID, $browseType = 'all')
{
    $tasks = $this->dao->select('id, parent')->from(TABLE_TASK)->where('project')->eq($projectID)->andWhere('deleted')->eq('0')->fetchPairs();
    foreach($tasks as $taskID => $parentID) unset($tasks[$parentID]);
    $taskIDList = array_keys($tasks);

    $today = date('Y-m-d');
    $list = $this->dao->select('*')->from(TABLE_TASK)->where('id')->in($taskIDList)
        ->beginIF($browseType == 'delay')->andWhere('deadline')->lt($today)->andWhere('status')->in(array('doing', 'wait'))->fi()
        ->fetchAll();

    return $list;
}

/**
 * Get stage pairs.
 *
 * @param int $projectID
 * @access public
 * @return array
 */
public function getStagesByProject($projectID, $onlyNamePath = false, $orderBy = 'order_asc',$flag = null)
{
    $stages = $this->dao->select('*')->from(TABLE_PROJECT)
        ->where('project')->eq($projectID)
        ->andWhere('deleted')->eq('0')
        ->andWhere('type')->eq('stage')
        ->beginIF($flag )->andWhere('dataVersion')->eq('2')->fi()
        ->orderBy($orderBy)
        ->fetchAll('id');

    if(!$onlyNamePath) return $stages;
    $data   = array();
    foreach($stages as $stage)
    {
        if(isset($stages[$stage->parent]))
        {
            $stage->name = $stages[$stage->parent]->name . '/' . $stage->name; //只会有两层，所以只循环一次就行
        }
        $data[$stage->id] = $stage->name;
    }
    return $data;
}

/**
 * Get stage pairs.
 *
 * @param int $projectID
 * @access public
 * @return array
 */
public function getReportStageLevelByProject($projectID)
{
    $stages = $this->dao->select('*')->from(TABLE_PROJECT)
        ->where('project')->eq($projectID)
        ->andWhere('type')->eq('stage')
        ->andWhere('deleted')->eq('0')
        ->orderBy('id_asc')
        ->fetchAll('id');

    foreach($stages as $index => $stage)
    {
        if($stage->parent)
        {
            $stages[$stage->parent]->childres[] = $stage;
            unset($stages[$index]);
        }
    }

    return $stages;
}

public function getConditionStagePairsByProject($projectID)
{
    $stages = $this->dao->select('*')->from(TABLE_PROJECT)
        ->where('project')->eq($projectID)
        ->andWhere('type')->eq('stage')
        ->andWhere('deleted')->eq('0')
        ->orderBy('id_asc')
        ->fetchAll('id');

    foreach($stages as $index => $stage)
    {
        if($stage->parent)
        {
            $stages[$stage->parent]->childres[] = $stage;
            unset($stages[$index]);
        }
    }

    $data = array();
    foreach($stages as $stage)
    {
        $data[$stage->id] = $stage->name;
        if(!isset($stage->childres)) continue;
        foreach($stage->childres as $child)
        {
            $data[$child->id] = '&nbsp;&nbsp;&nbsp;&nbsp;' . $child->name;
        }
    }

    return $data;
}

public function getReportStageOrderByProject($projectID, $assignStageID = array())
{
    if(!empty($assignStageID))
    {
        // 判断是否知道阶段查询，如果选中二级阶段需要查出一级阶段，选中一级阶段，查询出二级阶段。
        $assignStage = $this->dao->select('*')->from(TABLE_PROJECT)->where('id')->in($assignStageID)->fetchAll();

        $conditionStages = array();
        foreach($assignStage as $stage)
        {
            if(!$stage->parent)
            {
                $conditionStages[$stage->id] = $stage;
            }
            else
            {
                $conditionStages[$stage->parent] = $this->dao->select('*')->from(TABLE_PROJECT)->where('id')->eq($stage->parent)->fetch();
            }

            $stages = $this->dao->select('*')->from(TABLE_PROJECT)
                ->where('project')->eq($projectID)
                ->andWhere('type')->eq('stage')
                ->beginIF($stage->parent)->andWhere('id')->eq($stage->id)->fi()
                ->beginIF(!$stage->parent)->andWhere('parent')->eq($stage->id)->fi()
                ->andWhere('deleted')->eq('0')
                ->orderBy('id_asc')
                ->fetchAll('id');

            foreach($stages as $conditionStage)
            {
                $conditionStages[$conditionStage->id] = $conditionStage;
            }
        }
        $stages = $conditionStages;
    }
    else
    {
        $stages = $this->dao->select('*')->from(TABLE_PROJECT)
            ->where('project')->eq($projectID)
            ->andWhere('type')->eq('stage')
            ->andWhere('deleted')->eq('0')
            ->orderBy('id_asc')
            ->fetchAll('id');
    }

    foreach($stages as $index => $stage)
    {
        if($stage->parent)
        {
            $stages[$stage->parent]->childres[] = $stage;
            unset($stages[$index]);
        }
    }

    $data = array();
    foreach($stages as $stage)
    {
        $data[$stage->id] = $stage;
        if(!isset($stage->childres)) continue;
        foreach($stage->childres as $child)
        {
            $data[$child->id] = $child;
        }
    }
    $stages = $data;

    return $stages;
}

public function isCanRecordEstimate($projectID)
{
    // 判断用户必须是项目团队成员或超级管理员才可以提交工时。
    $teamUser = $this->dao->select('*')->from(TABLE_TEAM)->where('root')->eq($projectID)->andWhere('type')->eq('project')->andWhere('account')->eq($this->app->user->account)->fetch();
    $canRecordEstimate = false;
    if(!empty($teamUser) or $this->app->user->admin) $canRecordEstimate = true;
    return $canRecordEstimate;
}

/**
 * 检查用户是否拥有项目权限
 *
 * @param $projectId
 * @param $account
 * @param $subObjectID
 * @param $reason
 * @return mixed
 */
public function checkOwnProjectPermission($projectId, $account, $subObjectID = 0, $reason = 0){

    return $this->loadExtension('chengfangjinke')->checkOwnProjectPermission($projectId, $account, $subObjectID, $reason);
}

/**
 * 增加项目白名单信息
 *
 * @param $projectId
 * @param $account
 * @param $subObjectID
 * @param int $reason
 * @return mixed
 */
public function addProjectWhitelistInfo($projectId, $account, $subObjectID = 0, $reason = 0){

    return $this->loadExtension('chengfangjinke')->addProjectWhitelistInfo($projectId, $account, $subObjectID, $reason);
}

/**
 * Import case from Lib.
 *
 * @param  int    $projectID
 * @param  int    $applicationID
 * @param  int    $productID
 * @access public
 * @return void
 */
public function importFromLib($projectID,$applicationID, $productID)
{
    return $this->loadExtension('chengfangjinke')->importFromLib($projectID,$applicationID, $productID);
}
/**
 * Get testreport list for project
 *
 * @param  int    $applicationID
 * @param  int    $productID
 * @param  int    $projectID
 * @param  string $objectType
 * @param  string $extra
 * @param  string $orderBy
 * @param  object $pager
 * @access public
 * @return void
 */
public function getTestreportListForProject($applicationID, $productID, $projectID, $orderBy = 'id_desc', $pager)
{
    $testreportList = $this->dao->select('*')->from(TABLE_TESTREPORT)
        ->beginIF($projectID)->where('project')->eq($projectID)->fi()
        ->beginIF($applicationID)->andWhere('applicationID')->eq($applicationID)->fi()
        ->beginIF(!($productID === 'all'))->andWhere('product')->eq($productID)->fi()
        ->andWhere('deleted')->eq(0)
        ->orderBy($orderBy)
        ->page($pager)
        ->fetchAll('id');

    return $testreportList;
}
/**
 * create cell data.
 *
 * @param  string $col
 * @param  object $report
 * @param  array  $users
 * @param  string $mode
 * @param  array  $projects
 * @param  array  $products
 * @param  array  $tasks
 * @access public
 * @return void
 */
public function printCellTestreport($col, $report, $users, $mode = 'datatable', $projects, $products, $tasks)
{
    $this->loadModel('testreport');
    $viewLink = helper::createLink('testreport', 'view', "reportID=$report->id");

    $id = $col->id;
    if($col->show)
    {
        $class = 'c-' . $id;
        $title = '';
        if($id == 'title')
        {
            $class .= ' text-left';
            $title = "title='{$report->title}'";
        }
        if($id == 'status')
        {
            $class .= $report->status;
            $title = "title='" . $this->testreport->processStatus('testreport', $report) . "'";
        }
        if($id == 'actions') $class .= ' c-actions text-right';

        echo "<td class='{$class}' {$title}>";

        $this->loadModel('flow')->printFlowCell('testreport', $report, $id);

        $product = $report->product;

        $productName = zget($products, $report->applicationID . '-' . $product, '');

        $taskName = '';
        foreach(explode(',', $report->tasks) as $taskID)
        {
            $taskName .= $tasks[$taskID] . ' ';
        }

        switch($id)
        {
            case 'id':
                echo html::a($viewLink, sprintf('%03d', $report->id), '', "data-app='{$this->app->openApp}'");
                break;
            case 'title':
                echo html::a($viewLink, $report->title, '', "data-app='{$this->app->openApp}'");
                break;
            case 'product':
                echo $productName;
                break;
            case 'project':
                echo zget($projects, $report->project, '');
                break;
            case 'createdBy':
                echo zget($users, $report->createdBy);
                break;
            case 'createdDate':
                echo $report->createdDate;
                break;
            case 'tasks':
                echo zget($tasks, $report->tasks, '');
                break;
            case 'actions':
                if(common::canBeChanged('report', $report))
                {
                    common::printIcon('testreport', 'edit', "id=$report->id", 'project', 'list');
                    common::printIcon('testreport', 'delete', "id=$report->id", '', 'list', 'trash', 'hiddenwin');
                }
                break;
        }
        echo '</td>';
    }
}

/**
 * 查询项目的基准工时表 ，获取所有项目的所有工时，列表-实际工作量
 * @param $project
 * @return int
 */
public function getEffortConsumed(){
    return $this->loadExtension('chengfangjinke')->getEffortConsumed();
}
