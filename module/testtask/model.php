<?php
/**
 * The model file of test task module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     testtask
 * @version     $Id: model.php 5114 2013-07-12 06:02:59Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php
class testtaskModel extends model
{
    /**
     * Create a test task.
     *
     * @param  int   $projectID
     * @access public
     * @return void
     */
    function create($projectID = 0)
    {
        $task = fixer::input('post')
            ->setDefault('build', '')
            ->setDefault('problem', '')
            ->setDefault('requirement', '')
            ->setDefault('createdBy', $this->app->user->account)
            ->setDefault('createdDate', helper::now())
            ->stripTags($this->config->testtask->editor->create['id'], $this->config->allowedTags)
            ->join('problem', ',')
            ->join('build', ',')
            ->join('requirement', ',')
            ->join('secondorder', ',')
            ->join('mailto', ',')
            ->cleanINT('product')
            ->remove('uid,contactListMenu')
            ->get();

        $task->build = trim($task->build, ',');
        $task = $this->loadModel('file')->processImgURL($task, $this->config->testtask->editor->create['id'], $this->post->uid);
        $this->dao->insert(TABLE_TESTTASK)->data($task)
            ->autoCheck($skipFields = 'begin,end')
            ->batchcheck($this->config->testtask->create->requiredFields, 'notempty')
            ->checkIF($task->begin != '', 'begin', 'date')
            ->checkIF($task->end != '', 'end', 'date')
            ->checkIF($task->end != '', 'end', 'ge', $task->begin)
            ->exec();

        if(!dao::isError())
        {
            $taskID = $this->dao->lastInsertID();

            // 更新测试单单号
            $oddNumber = 'CFIT-TR-' . date('Ymd') . '-' . sprintf('%02d', $taskID);
            $this->dao->update(TABLE_TESTTASK)->set('oddNumber')->eq($oddNumber)->where('id')->eq($taskID)->exec();

            $this->file->updateObjectID($this->post->uid, $taskID, 'testtask');
            return $taskID;
        }
    }

    /**
     * Get test tasks of a product.
     * 
     * @param  array  $products
     * @param  string $orderBy
     * @param  object $pager
     * @param  array  $scopeAndStatus
     * @param  int    $beginTime
     * @param  int    $endTime
     * @access public
     * @return array
     */
    public function getProductTasks($applicationID, $products, $orderBy = 'id_desc', $pager = null, $scopeAndStatus = array(), $queryID = null)
    {
        if($queryID)
        {
            $query = $this->loadModel('search')->getQuery($queryID);
            if($query)
            {
                $this->session->set('testtaskQuery', $query->sql);
                $this->session->set('testtaskForm', $query->form);
            }
            else
            {
                $this->session->set('testtaskQuery', ' 1 = 1');
            }
        }
        else
        {
            if($this->session->testtaskQuery == false) $this->session->set('testtaskQuery', ' 1 = 1');
        }

        $testtaskQuery = $this->session->testtaskQuery;

        if($scopeAndStatus[1] != 'bySearch') $testtaskQuery = ' 1 = 1';

        if(strpos($testtaskQuery, '`testrunAssignedTo` =') !== false) $testtaskQuery = str_replace('`testrunAssignedTo` =', '`t2`.`assignedTo` =', $testtaskQuery);
        if(strpos($testtaskQuery, '`testrunLastRunner` =') !== false) $testtaskQuery = str_replace('`testrunLastRunner` =', '`t2`.`lastRunner` =', $testtaskQuery);

        $orderBy = 't1.' . $orderBy;

        $testtaskList = $this->dao->select('distinct t1.*')->from(TABLE_TESTTASK)->alias('t1')
            ->leftJoin(TABLE_TESTRUN)->alias('t2')->on('t1.id = t2.task')
            ->where($testtaskQuery)
            ->andWhere('deleted')->eq(0)
            ->andWhere('applicationID')->eq($applicationID)
            ->andWhere('auto')->eq('no')
            ->beginIF($scopeAndStatus[0] == 'local')->andWhere('product')->in($products)->fi()
            ->beginIF($scopeAndStatus[0] == 'all')->andWhere('product')->in($products)->fi()
            ->beginIF($scopeAndStatus[1] == 'totalStatus')->andWhere('t1.status')->in('blocked,doing,wait,done')->fi()
            ->beginIF($scopeAndStatus[1] != 'totalStatus' && $scopeAndStatus[1] != 'bySearch')->andWhere('t1.status')->eq($scopeAndStatus[1])->fi()
            ->orderBy($orderBy)
            ->page($pager, 't1.id')
            ->fetchAll('id');

        /* 获取产品、项目和版本数据。*/
        $productIdList = array();
        $projectIdList = array();
        $buildIdList   = array();
        foreach($testtaskList as $testtask)
        {
            if($testtask->product) $productIdList[$testtask->product] = $testtask->product;
            if($testtask->project) $projectIdList[$testtask->project] = $testtask->project;
            if($testtask->build)
            {
                $builds = explode(',', $testtask->build);
                foreach($builds as $build) $buildIdList[$build] = $build;
            }
        }

        $productDataList = $this->dao->select('id,name')->from(TABLE_PRODUCT)->where('id')->in($productIdList)->fetchAll('id');
        $projectDataList = $this->dao->select('id,name')->from(TABLE_PROJECT)->where('id')->in($projectIdList)->fetchAll('id');
        $buildDataList   = $this->dao->select('id,name')->from(TABLE_BUILD)->where('id')->in($buildIdList)->fetchAll('id');

        foreach($testtaskList as $testtask)
        {
            $testtask->productData = '';
            $testtask->projectData = '';
            $testtask->buildData   = array();
            if($testtask->product) $testtask->productData = $productDataList[$testtask->product];
            if($testtask->project) $testtask->projectData = $projectDataList[$testtask->project];
            if($testtask->build)
            {
                $builds = explode(',', $testtask->build);
                foreach($builds as $build) $testtask->buildData[] = $buildDataList[$build];
            }
        }

        return $testtaskList;
    }

    /**
     * Get product unit tasks.
     *
     * @param  int    $productID
     * @param  string $browseType
     * @param  string $orderBy
     * @param  int    $pager
     * @access public
     * @return void
     */
    public function getProductUnitTasks($applicationID, $productIdList, $browseType = '', $orderBy = 'id_desc', $pager = null)
    {
        $beginAndEnd = $this->loadModel('action')->computeBeginAndEnd($browseType);
        if(empty($beginAndEnd)) $beginAndEnd = array('begin' => '', 'end' => '');

        if($browseType == 'newest') $orderBy = 'end_desc,' . $orderBy;
        $tasks = $this->dao->select("t1.*")
            ->from(TABLE_TESTTASK)->alias('t1')
            ->where('t1.deleted')->eq(0)
            ->andWhere('t1.applicationID')->in($applicationID)
            ->andWhere('t1.product')->in($productIdList)
            ->beginIF($this->config->systemMode == 'new' and $this->lang->navGroup->testtask != 'qa')->andWhere('t1.project')->eq($this->session->project)->fi()
            ->andWhere('t1.auto')->eq('unit')
            ->beginIF($browseType != 'all' and $browseType != 'newest' and $beginAndEnd)
            ->andWhere('t1.end')->ge($beginAndEnd['begin'])
            ->andWhere('t1.end')->le($beginAndEnd['end'])
            ->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');

        $resultGroups = $this->dao->select('t1.task, t2.*')->from(TABLE_TESTRUN)->alias('t1')
            ->leftJoin(TABLE_TESTRESULT)->alias('t2')->on('t1.id=t2.run')
            ->where('t1.task')->in(array_keys($tasks))
            ->fetchGroup('task', 'run');

        foreach($tasks as $taskID => $task)
        {
            $results = zget($resultGroups, $taskID, array());

            $task->caseCount = count($results);
            $task->passCount = 0;
            $task->failCount = 0;
            foreach($results as $result)
            {
                if($result->caseResult == 'pass') $task->passCount ++;
                if($result->caseResult == 'fail') $task->failCount ++;
            }
        }

        return $tasks;
    }

    /**
     * Get test tasks of a project.
     * 
     * @param  int    $projectID
     * @param  string $orderBy
     * @param  array  $scopeAndStatus
     * @param  object $pager
     * @access public
     * @return array
     */
    public function getProjectTasks($projectID, $orderBy = 'id_desc', $pager = null, $applicationID = 0, $productID = 'all', $scopeAndStatus = array() , $queryID = null)
    {

        if($queryID)
        {
            $query = $this->loadModel('search')->getQuery($queryID);
            if($query)
            {
                $this->session->set('projecttesttaskQuery', $query->sql);
                $this->session->set('projecttesttaskForm', $query->form);
            }
            else
            {
                $this->session->set('projecttesttaskQuery', ' 1 = 1');
            }
        }
        else
        {
            if($this->session->projecttesttaskQuery == false) $this->session->set('projecttesttaskQuery', ' 1 = 1');
        }

        $testtaskQuery = $this->session->projecttesttaskQuery;
        if($scopeAndStatus[1] != 'bySearch') $testtaskQuery = ' 1 = 1';
        
        if(strpos($testtaskQuery, '`testrunAssignedTo` =') !== false) $testtaskQuery = str_replace('`testrunAssignedTo` =', '`t2`.`assignedTo` =', $testtaskQuery);
        if(strpos($testtaskQuery, '`testrunLastRunner` =') !== false) $testtaskQuery = str_replace('`testrunLastRunner` =', '`t2`.`lastRunner` =', $testtaskQuery);

        $orderBy = 't1.' . $orderBy;

        $testtaskList = $this->dao->select('distinct t1.*')->from(TABLE_TESTTASK)->alias('t1')
            ->leftJoin(TABLE_TESTRUN)->alias('t2')->on('t1.id = t2.task')
            ->where($testtaskQuery)
            ->andWhere('deleted')->eq(0)
            ->andWhere('auto')->eq('no')
            ->andWhere('project')->eq((int)$projectID)
            ->beginIF($applicationID)->andWhere('applicationID')->eq((int)$applicationID)->fi()
            ->beginIF($productID != 'all')->andWhere('product')->eq((int)$productID)->fi()
            ->beginIF($scopeAndStatus[1] == 'totalStatus')->andWhere('t1.status')->in('blocked,doing,wait,done')->fi()
            ->beginIF($scopeAndStatus[1] != 'totalStatus' && $scopeAndStatus[1] != 'bySearch')->andWhere('t1.status')->eq($scopeAndStatus[1])->fi()
            ->orderBy($orderBy)
            ->page($pager, 't1.id')
            ->fetchAll('id');

        $buildIdList = array();
        foreach($testtaskList as $testtask)
        {
            if($testtask->build)
            {
                $builds = explode(',', $testtask->build);
                foreach($builds as $build) $buildIdList[$build] = $build;
            }
        }

        $buildDataList = $this->dao->select('id,name')->from(TABLE_BUILD)->where('id')->in($buildIdList)->fetchAll('id');

        foreach($testtaskList as $testtask)
        {
            $testtask->buildData = array();
            if($testtask->build)
            {
                $builds = explode(',', $testtask->build);
                foreach($builds as $build) $testtask->buildData[] = $buildDataList[$build];
            }
        }

        return $testtaskList;
    }

    /**
     * Get test tasks of a execution.
     *
     * @param  int    $executionID
     * @param  string $orderBy
     * @param  object $pager
     * @access public
     * @return array
     */
    public function getExecutionTasks($executionID, $orderBy = 'id_desc', $pager = null)
    {
        return $this->dao->select('t1.*, t2.name AS buildName')
            ->from(TABLE_TESTTASK)->alias('t1')
            ->leftJoin(TABLE_BUILD)->alias('t2')->on('t1.build = t2.id')
            ->where('t1.execution')->eq((int)$executionID)
            ->andWhere('t1.auto')->ne('unit')
            ->andWhere('t1.deleted')->eq(0)
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');
    }

    /**
     * Get testtask pairs.
     *
     * @param  int    $productID
     * @param  int    $executionID
     * @param  string $appendIdList
     * @param  string $params noempty
     * @param  string $valueField
     * @param  int    $applicationID
     * @param  int    $projectID
     * @access public
     * @return array
     */
    public function getPairs($productID, $executionID = 0, $appendIdList = '', $params = '', $valueField = 'name', $applicationID = 0, $projectID = 0)
    {
        if($productID == 'na' || $productID == 'all')
        {
            $productID = 0;
        }

        $selectFields = $valueField == 'name' ?  "id,name" : "id,concat($valueField, '(', name, ')') as $valueField";
        $pairs = $this->dao->select($selectFields)->from(TABLE_TESTTASK)
            ->where('deleted')->eq(0)
            ->beginIF($productID)->andWhere('product')->eq((int)$productID)->fi()
            ->beginIF($executionID)->andWhere('execution')->eq((int)$executionID)->fi()
            ->beginIF($applicationID)->andWhere('`applicationID`')->eq((int)$applicationID)->fi()
            ->beginIF($projectID)->andWhere('`project`')->eq((int)$projectID)->fi()
            ->andWhere('auto')->ne('unit')
            ->orderBy('id_desc')
            ->fetchPairs('id', $valueField);

        if($appendIdList) $pairs += $this->dao->select($selectFields)->from(TABLE_TESTTASK)->where('id')->in($appendIdList)->fetchPairs('id', $valueField);
        if(strpos($params, 'noempty') === false) $pairs = array(0 => '') + $pairs;

        return $pairs;
    }

    /**
     * Get task by idList.
     *
     * @param  array    $idList
     * @access public
     * @return array
     */
    public function getByList($idList)
    {
        return $this->dao->select("*")->from(TABLE_TESTTASK)->where('id')->in($idList)->fetchAll('id');
    }

    /**
     * Get test task info by id.
     *
     * @param  int   $taskID
     * @param  bool  $setImgSize
     * @access public
     * @return void|object
     */
    public function getById($taskID, $setImgSize = false)
    {
        $task = $this->dao->select("*")->from(TABLE_TESTTASK)->where('id')->eq((int)$taskID)->fetch();
        if($task)
        {
            $task->branch = 0;
            $projectPlan  = '';
            if($task->project) $projectPlan = $this->dao->select('*')->from(TABLE_PROJECTPLAN)->where('project')->eq($task->project)->fetch();
            $task->projectPlan = $projectPlan;

            $task->buildList = array();
            if($task->build) $task->buildList = $this->dao->select('id,name')->from(TABLE_BUILD)->where('id')->in($task->build)->fetchPairs();

            $task->progress = $this->processProgress($task->id);
        }

        if(!$task) return false;

        $task = $this->loadModel('file')->replaceImgURL($task, 'desc');
        if($setImgSize) $task->desc = $this->loadModel('file')->setImgSize($task->desc);
        return $task;
    }

    /**
     * Get test tasks by user.
     *
     * @param   string $account
     * @access  public
     * @return  array
     */
    public function getByUser($account, $pager = null, $orderBy = 'id_desc', $type = '')
    {
        $testtaskList = $this->dao->select('*')->from(TABLE_TESTTASK)
            ->where('deleted')->eq(0)
            ->andWhere('auto')->eq('no')
            ->andWhere('owner')->eq($account)
            ->beginIF($type == 'wait')->andWhere('status')->ne('done')->fi()
            ->beginIF($type == 'done')->andWhere('status')->eq('done')->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll();

        /* 获取产品、项目和版本数据。*/
        $applicationIdList = array();
        $projectIdList = array();
        $buildIdList   = array();
        foreach($testtaskList as $testtask)
        {
            if($testtask->applicationID) $applicationIdList[$testtask->applicationID] = $testtask->applicationID;
            if($testtask->project) $projectIdList[$testtask->project] = $testtask->project;
            if($testtask->build)
            {
                $builds = explode(',', $testtask->build);
                foreach($builds as $build) $buildIdList[$build] = $build;
            }
        }

        $applicationDataList = $this->dao->select('id,name')->from(TABLE_APPLICATION)->where('id')->in($applicationIdList)->fetchAll('id');
        $projectDataList = $this->dao->select('id,name')->from(TABLE_PROJECT)->where('id')->in($projectIdList)->fetchAll('id');
        $buildDataList   = $this->dao->select('id,name')->from(TABLE_BUILD)->where('id')->in($buildIdList)->fetchAll('id');

        foreach($testtaskList as $testtask)
        {
            $testtask->applicationData = '';
            $testtask->projectData = '';
            $testtask->buildData   = array();
            if($testtask->applicationID) $testtask->applicationData = $applicationDataList[$testtask->applicationID];
            if($testtask->project) $testtask->projectData = $projectDataList[$testtask->project];
            if($testtask->build)
            {
                $builds = explode(',', $testtask->build);
                foreach($builds as $build) $testtask->buildData[] = $buildDataList[$build];
            }
        }

        return $testtaskList;
    }



    /**
     * Get taskrun by case id.
     *
     * @param  int    $taskID
     * @param  int    $caseID
     * @access public
     * @return void
     */
    public function getRunByCase($taskID, $caseID)
    {
        return $this->dao->select('*')->from(TABLE_TESTRUN)->where('task')->eq($taskID)->andWhere('`case`')->eq($caseID)->fetch();
    }

    /**
     * Get linkable casses.
     *
     * @param  int    $productID
     * @param  object $task
     * @param  int    $taskID
     * @param  string $type
     * @param  string $param
     * @param  object $pager
     * @access public
     * @return array
     */
    public function getLinkableCases($applicationID, $productID, $task, $taskID, $type, $param, $pager)
    {
        if($this->session->testtask_link_testcaseQuery == false) $this->session->set('testtask_link_testcaseQuery', ' 1 = 1');
        $query = $this->session->testtask_link_testcaseQuery;
        $allProduct = "`product` = 'all'";
        if(strpos($query, '`product` =') === false && $type != 'bysuite') $query .= " AND `product` = $productID";
        if(strpos($query, $allProduct) !== false) $query = str_replace($allProduct, '1', $query);
        if(strpos($query, 'linkTesttask') !== false) $query = preg_replace('/`linkTesttask` = \'(\d+)\'/', '1', $query);
        if($this->app->openApp == 'project') $query .= " AND `project` = {$this->session->project}";

        $cases = array();
        $linkedCases = $this->dao->select('`case`')->from(TABLE_TESTRUN)->where('task')->eq($taskID)->fetchPairs('case');
        if($type == 'all')     $cases = $this->getAllLinkableCases($applicationID, $task, $query, $linkedCases, $pager);
        if($type == 'bystory') $cases = $this->getLinkableCasesByStory($applicationID, $productID, $task, $query, $linkedCases, $pager);
        if($type == 'bybug')   $cases = $this->getLinkableCasesByBug($applicationID, $productID, $task, $query, $linkedCases, $pager);
        if($type == 'bysuite') $cases = $this->getLinkableCasesBySuite($applicationID, $productID, $task, $query, $param, $linkedCases, $pager);
        if($type == 'bybuild') $cases = $this->getLinkableCasesByTestTask($applicationID, $param, $linkedCases, $query, $pager);

        return $cases;
    }

    /**
     * Get all linkable  cases.
     *
     * @param  object $task
     * @param  string $query
     * @param  array  $linkedCases
     * @param  object $pager
     * @access public
     * @return array
     */
    public function getAllLinkableCases($applicationID, $task, $query, $linkedCases, $pager)
    {
        return $this->dao->select('*')->from(TABLE_CASE)
                ->where($query)
                ->andWhere('applicationID')->eq($applicationID)
                ->andWhere('id')->notIN($linkedCases)
                ->andWhere('status')->ne('wait')
                ->andWhere('type')->ne('unit')
                ->beginIF($task->branch)->andWhere('branch')->in("0,$task->branch")->fi()
                ->andWhere('deleted')->eq(0)
                ->orderBy('id desc')
                ->page($pager)
                ->fetchAll();
    }

    public function getLinkableBugs($applicationID, $product, $task, $pager)
    {
        $bugQuery = $this->session->testtask_link_bugQuery;

        if(empty($bugQuery)) $bugQuery = "1 = 1";

        if (strpos($bugQuery, "`product` = 'all'"))
        {
            $bugQuery = str_replace("`product` = 'all'", '1', $bugQuery);
        }
        else if (strpos($bugQuery, '`product` =') === false)
        {
            $bugQuery.= " AND `product` = $product";
        }

        if($this->app->openApp == 'project') $bugQuery .= " AND `project` = {$this->session->project}";

        if (strpos($bugQuery, 'linkDefect'))
        {
            $bugQuery = str_replace('AND (`', ' AND (`t2.', $bugQuery);
            $bugQuery = str_replace('AND `', ' AND `t2.', $bugQuery);
            $bugQuery = str_replace('OR (`', ' OR (`t2.', $bugQuery);
            $bugQuery = str_replace('OR `', ' OR `t2.', $bugQuery);
            $bugQuery = str_replace('t2.linkDefect', 't2.code', $bugQuery);
            $bugQuery = str_replace('`', '', $bugQuery);
        }
        else
        {
            $bugQuery = str_replace('AND (`', ' AND (`t1.', $bugQuery);
            $bugQuery = str_replace('AND `', ' AND `t1.', $bugQuery);
            $bugQuery = str_replace('OR (`', ' OR (`t1.', $bugQuery);
            $bugQuery = str_replace('OR `', ' OR `t1.', $bugQuery);
            $bugQuery = str_replace('`', '', $bugQuery);
        }

        return $this->dao->select('t1.*')->from(TABLE_BUG)->alias("t1")
                ->leftJoin(TABLE_DEFECT)->alias('t2')->on('t1.id = t2.bugId')
                ->where($bugQuery)
                ->andWhere('t1.applicationID')->eq($applicationID)
                ->andWhere("NOT FIND_IN_SET({$task->id},linkTesttask)")
                ->andWhere('t1.deleted')->eq(0)
                ->orderBy('t1.id desc')
                ->page($pager)
                ->fetchAll();
    }

    /**
     * Get linkable cases by story.
     *
     * @param  int    $productID
     * @param  object $task
     * @param  string $query
     * @param  array  $linkedCases
     * @param  object $pager
     * @access public
     * @return array
     */
    public function getLinkableCasesByStory($applicationID, $productID, $task, $query, $linkedCases, $pager)
    {
        $task->build = trim($task->build, ',');
        $builds = $this->dao->select('id,stories')->from(TABLE_BUILD)->where('id')->in($task->build)->fetchPairs();

        $storyIdList = array();
        foreach($builds as $buildID => $stories)
        {
            $stories = trim($stories, ',');
            if(empty($stories)) continue;
            $stories = explode(',', $stories);
            foreach($stories as $story) $storyIdList[] = $story;
        }

        $cases = array();
        if(!empty($storyIdList))
        {
            $cases = $this->dao->select('*')->from(TABLE_CASE)
                ->where($query)
                ->beginIF($this->config->systemMode == 'new' and $this->lang->navGroup->testtask != 'qa')->andWhere('project')->eq($this->session->project)->fi()
                ->andWhere('applicationID')->eq($applicationID)
                ->andWhere('status')->ne('wait')
                ->beginIF($linkedCases)->andWhere('id')->notIN($linkedCases)->fi()
                ->beginIF($task->branch)->andWhere('branch')->in("0,$task->branch")->fi()
                ->andWhere('story')->in($storyIdList)
                ->andWhere('deleted')->eq(0)
                ->orderBy('id desc')
                ->page($pager)
                ->fetchAll();
        }

        return $cases;
    }

    /**
     * Get linkable cases by bug.
     *
     * @param  int    $productID
     * @param  object $task
     * @param  string $query
     * @param  array  $linkedCases
     * @param  object $pager
     * @access public
     * @return array
     */
    public function getLinkableCasesByBug($applicationID, $productID, $task, $query, $linkedCases, $pager)
    {
        $task->build = trim($task->build, ',');
        $builds = $this->dao->select('id,bugs')->from(TABLE_BUILD)->where('id')->in($task->build)->fetchPairs();

        $bugIdList = array();
        foreach($builds as $buildID => $bugs)
        {
            $bugs = trim($bugs, ',');
            if(empty($bugs)) continue;
            $bugs = explode(',', $bugs);
            foreach($bugs as $bug) $bugIdList[] = $bug;
        }

        $cases = array();
        if(!empty($bugIdList))
        {
            $cases = $this->dao->select('*')->from(TABLE_CASE)->where($query)
                ->beginIF($this->config->systemMode == 'new' and $this->lang->navGroup->testtask != 'qa')->andWhere('project')->eq($this->session->project)->fi()
                ->andWhere('applicationID')->eq($applicationID)
                ->andWhere('status')->ne('wait')
                ->beginIF($linkedCases)->andWhere('id')->notIN($linkedCases)->fi()
                ->beginIF($task->branch)->andWhere('branch')->in("0,$task->branch")->fi()
                ->andWhere('fromBug')->in($bugIdList)
                ->andWhere('deleted')->eq(0)
                ->orderBy('id desc')
                ->page($pager)
                ->fetchAll();
        }

        return $cases;
    }

    /**
     * Get linkable cases by suite.
     *
     * @param  int    $productID
     * @param  object $task
     * @param  string $query
     * @param  string $suite
     * @param  array  $linkedCases
     * @param  object $pager
     * @access public
     * @return array
     */
    public function getLinkableCasesBySuite($applicationID, $productID, $task, $query, $suite, $linkedCases, $pager)
    {
        if(strpos($query, '`product`') !== false) $query = str_replace('`product`', 't1.`product`', $query);
        return $this->dao->select('t1.*,t2.version as version')->from(TABLE_CASE)->alias('t1')
                ->leftJoin(TABLE_SUITECASE)->alias('t2')->on('t1.id=t2.case')
                ->where($query)
                ->beginIF($this->config->systemMode == 'new' and $this->lang->navGroup->testtask != 'qa')->andWhere('t1.project')->eq($this->session->project)->fi()
                ->andWhere('t2.suite')->eq((int)$suite)
                ->andWhere('t1.applicationID')->eq($applicationID)
                ->andWhere('status')->ne('wait')
                ->beginIF($linkedCases)->andWhere('t1.id')->notIN($linkedCases)->fi()
                ->beginIF($task->branch)->andWhere('t1.branch')->in("0,$task->branch")->fi()
                ->andWhere('deleted')->eq(0)
                ->orderBy('id desc')
                ->page($pager)
                ->fetchAll();
    }

    /**
     * Get linkeable cases by test task.
     *
     * @param  string $testTask
     * @param  array  $linkedCases
     * @param  string $query
     * @param  object $pager
     * @access public
     * @return array
     */
    public function getLinkableCasesByTestTask($applicationID, $testTask, $linkedCases, $query, $pager)
    {
        /* Format the query condition. */
        $query = preg_replace('/`(\w+)`/', 't1.`$1`', $query);
        $query = str_replace('t1.`lastRunner`', 't2.`lastRunner`', $query);
        $query = str_replace('t1.`lastRunDate`', 't2.`lastRunDate`', $query);
        $query = str_replace('t1.`lastRunResult`', 't2.`lastRunResult`', $query);

        return $this->dao->select("t1.*,t2.lastRunner,t2.lastRunDate,t2.lastRunResult")->from(TABLE_CASE)->alias('t1')
            ->leftJoin(TABLE_TESTRUN)->alias('t2')->on('t1.id = t2.case')
            ->where($query)
            ->andWhere('t1.id')->notin($linkedCases)
            ->andWhere('t1.applicationID')->eq($applicationID)
            ->andWhere('t2.task')->eq($testTask)
            ->beginIF($this->config->systemMode == 'new' and $this->lang->navGroup->testtask != 'qa')->andWhere('t1.project')->eq($this->session->project)->fi()
            ->andWhere('t1.status')->ne('wait')
            ->page($pager)
            ->fetchAll();
    }

    /**
     * Get related test tasks.
     *
     * @param  int    $productID
     * @param  int    $testtaskID
     * @access public
     * @return array
     */
    public function getRelatedTestTasks($applicationID, $productID, $testTaskID)
    {
        $beginDate = $this->dao->select('begin')->from(TABLE_TESTTASK)->where('id')->eq($testTaskID)->fetch('begin');

        return $this->dao->select('id, name')->from(TABLE_TESTTASK)
            ->where('applicationID')->eq($applicationID)
            ->andWhere('product')->eq($productID)
            ->andWhere('auto')->ne('unit')
            ->beginIF($beginDate)->andWhere('begin')->le($beginDate)->fi()
            ->andWhere('deleted')->eq('0')
            ->andWhere('id')->notin($testTaskID)
            ->orderBy('begin desc')
            ->fetchPairs('id', 'name');
    }

    /**
     * Get report data of test task per run result.
     *
     * @param  int     $taskID
     * @access public
     * @return array
     */
    public function getDataOfTestTaskPerRunResult($taskID)
    {
        $datas = $this->dao->select("t1.lastRunResult AS name, COUNT('t1.*') AS value")->from(TABLE_TESTRUN)->alias('t1')
            ->leftJoin(TABLE_CASE)->alias('t2')
            ->on('t1.case = t2.id')
            ->where('t1.task')->eq($taskID)
            ->andWhere('t2.deleted')->eq(0)
            ->groupBy('name')
            ->orderBy('value DESC')
            ->fetchAll('name');

        if(!$datas) return array();

        $this->app->loadLang('testcase');
        foreach($datas as $result => $data) $data->name = isset($this->lang->testcase->resultList[$result])? $this->lang->testcase->resultList[$result] : $this->lang->testtask->unexecuted;

        return $datas;
    }

    /**
     * Get report data of test task per Type.
     *
     * @param  int     $taskID
     * @access public
     * @return array
     */
    public function getDataOfTestTaskPerType($taskID)
    {
        $datas = $this->dao->select('t2.type as name,count(*) as value')->from(TABLE_TESTRUN)->alias('t1')
            ->leftJoin(TABLE_CASE)->alias('t2')->on('t1.case = t2.id')
            ->where('t1.task')->eq($taskID)
            ->andWhere('t2.deleted')->eq(0)
            ->groupBy('name')
            ->orderBy('value desc')
            ->fetchAll('name');
        if(!$datas) return array();

        foreach($datas as $result => $data) if(isset($this->lang->testcase->typeList[$result])) $data->name = $this->lang->testcase->typeList[$result];

        return $datas;
    }

    /**
     * Get report data of test task per module
     *
     * @param  int     $taskID
     * @access public
     * @return array
     */
    public function getDataOfTestTaskPerModule($taskID)
    {
        $datas = $this->dao->select('t2.module as name,count(*) as value')->from(TABLE_TESTRUN)->alias('t1')
            ->leftJoin(TABLE_CASE)->alias('t2')->on('t1.case = t2.id')
            ->where('t1.task')->eq($taskID)
            ->andWhere('t2.deleted')->eq(0)
            ->groupBy('name')
            ->orderBy('value desc')
            ->fetchAll('name');
        if(!$datas) return array();

        $modules = $this->loadModel('tree')->getModulesName(array_keys($datas));
        foreach($datas as $moduleID => $data) $data->name = isset($modules[$moduleID]) ? $modules[$moduleID] : '/';

        return $datas;
    }

    /**
     * Get report data of test task per runner
     *
     * @param  int     $taskID
     * @access public
     * @return array
     */
    public function getDataOfTestTaskPerRunner($taskID)
    {
        $datas = $this->dao->select("t1.lastRunner AS name, COUNT('t1.*') AS value")->from(TABLE_TESTRUN)->alias('t1')
            ->leftJoin(TABLE_CASE)->alias('t2')->on('t1.case = t2.id')
            ->where('t1.task')->eq($taskID)
            ->andWhere('t2.deleted')->eq(0)
            ->groupBy('name')
            ->orderBy('value DESC')
            ->fetchAll('name');
        if(!$datas) return array();
        $users = $this->loadModel('user')->getPairs('noclosed|noletter');
        foreach($datas as $result => $data) $data->name = $result ? zget($users, $result, $result) : $this->lang->testtask->unexecuted;

        return $datas;
    }

    /**
     * Get bug info.
     *
     * @param  int    $taskID
     * @param  int    $productID
     * @access public
     * @return array
     */
    public function getBugInfo($taskID, $applicationID, $productID)
    {
        $foundBugs = $this->dao->select('*')->from(TABLE_BUG)
            ->where('applicationID')->eq($applicationID)
            ->andWhere('product')->eq($productID)
            ->andWhere("FIND_IN_SET({$taskID},linkTesttask)")
            ->andWhere('deleted')->eq(0)->fetchAll();

        $severityGroups = $statusGroups = $openedByGroups = $resolvedByGroups = $resolutionGroups = $moduleGroups = array();
        $resolvedBugs   = 0;
        foreach($foundBugs as $bug)
        {
            $severityGroups[$bug->severity] = isset($severityGroups[$bug->severity]) ? $severityGroups[$bug->severity] + 1 : 1;
            $statusGroups[$bug->status]     = isset($statusGroups[$bug->status])     ? $statusGroups[$bug->status]     + 1 : 1;
            $openedByGroups[$bug->openedBy] = isset($openedByGroups[$bug->openedBy]) ? $openedByGroups[$bug->openedBy] + 1 : 1;
            $moduleGroups[$bug->module]     = isset($moduleGroups[$bug->module])     ? $moduleGroups[$bug->module]     + 1 : 1;

            if($bug->resolvedBy) $resolvedByGroups[$bug->resolvedBy] = isset($resolvedByGroups[$bug->resolvedBy]) ? $resolvedByGroups[$bug->resolvedBy] + 1 : 1;
            if($bug->resolution) $resolutionGroups[$bug->resolution] = isset($resolutionGroups[$bug->resolution]) ? $resolutionGroups[$bug->resolution] + 1 : 1;
            if($bug->status == 'resolved' or $bug->status == 'closed') $resolvedBugs ++;
        }

        $bugInfo['bugConfirmedRate']    = empty($resolvedBugs) ? 0 : round((zget($resolutionGroups, 'fixed', 0) + zget($resolutionGroups, 'postponed', 0)) / $resolvedBugs * 100, 2);
        $bugInfo['bugCreateByCaseRate'] = empty($byCaseNum) ? 0 : round($byCaseNum / count($foundBugs) * 100, 2);

        $this->app->loadLang('bug');
        $users = $this->loadModel('user')->getPairs('noclosed|noletter|nodeleted');
        $data  = array();
        foreach($severityGroups as $severity => $count)
        {
            $data[$severity] = new stdclass();
            $data[$severity]->name  = zget($this->lang->bug->severityList, $severity);
            $data[$severity]->value = $count;
        }
        $bugInfo['bugSeverityGroups'] = $data;

        $data = array();
        foreach($statusGroups as $status => $count)
        {
            $data[$status] = new stdclass();
            $data[$status]->name  = zget($this->lang->bug->statusList, $status);
            $data[$status]->value = $count;
        }
        $bugInfo['bugStatusGroups'] = $data;

        $data = array();
        foreach($resolutionGroups as $resolution => $count)
        {
            $data[$resolution] = new stdclass();
            $data[$resolution]->name  = zget($this->lang->bug->resolutionList, $resolution);
            $data[$resolution]->value = $count;
        }
        $bugInfo['bugResolutionGroups'] = $data;

        $data = array();
        foreach($openedByGroups as $openedBy => $count)
        {
            $data[$openedBy] = new stdclass();
            $data[$openedBy]->name  = zget($users, $openedBy);
            $data[$openedBy]->value = $count;
        }
        $bugInfo['bugOpenedByGroups'] = $data;

        $modules = array();
        if(is_numeric($productID)) $modules = $this->loadModel('tree')->getOptionMenu($productID, $viewType = 'bug');

        $data = array();
        foreach($moduleGroups as $moduleID => $count)
        {
            $data[$moduleID] = new stdclass();
            $data[$moduleID]->name  = zget($modules, $moduleID);
            $data[$moduleID]->value = $count;
        }
        $bugInfo['bugModuleGroups'] = $data;

        $data = array();
        foreach($resolvedByGroups as $resolvedBy => $count)
        {
            $data[$resolvedBy] = new stdclass();
            $data[$resolvedBy]->name  = zget($users, $resolvedBy);
            $data[$resolvedBy]->value = $count;
        }
        $bugInfo['bugResolvedByGroups'] = $data;

        return $bugInfo;
    }

     /**
     * Merge the default chart settings and the settings of current chart.
     *
     * @param  string    $chartType
     * @access public
     * @return void
     */
    public function mergeChartOption($chartType)
    {
        $chartOption  = isset($this->lang->testtask->report->$chartType) ? $this->lang->testtask->report->$chartType : new stdclass();
        $commonOption = $this->lang->testtask->report->options;

        if(!isset($chartOption->graph)) $chartOption->graph = new stdclass();
        $chartOption->graph->caption = $this->lang->testtask->report->charts[$chartType];
        if(!isset($chartOption->type))    $chartOption->type  = $commonOption->type;
        if(!isset($chartOption->width))  $chartOption->width  = $commonOption->width;
        if(!isset($chartOption->height)) $chartOption->height = $commonOption->height;

        /* 合并配置。*/
        foreach($commonOption->graph as $key => $value) if(!isset($chartOption->graph->$key)) $chartOption->graph->$key = $value;
        return $chartOption;
    }

    /**
     * Update a test task.
     *
     * @param  int   $taskID
     * @access public
     * @return void
     */
    public function update($taskID)
    {
        $oldTask = $this->dao->select("*")->from(TABLE_TESTTASK)->where('id')->eq((int)$taskID)->fetch();

        $task = fixer::input('post')
            ->setDefault('build', '')
            ->setDefault('problem', '')
            ->setDefault('requirement', '')
            ->stripTags($this->config->testtask->editor->edit['id'], $this->config->allowedTags)
            ->join('mailto', ',')
            ->join('build', ',')
            ->join('problem', ',')
            ->join('requirement', ',')
            ->join('secondorder', ',')
            ->cleanINT('product')
            ->remove('uid,comment,contactListMenu')
            ->get();

        $task->build = trim($task->build, ',');
        $task = $this->loadModel('file')->processImgURL($task, $this->config->testtask->editor->edit['id'], $this->post->uid);
        $this->dao->update(TABLE_TESTTASK)->data($task)
            ->autoCheck()
            ->batchcheck($this->config->testtask->edit->requiredFields, 'notempty')
            ->checkIF($task->end != '', 'end', 'ge', $task->begin)
            ->where('id')->eq($taskID)
            ->exec();

        if(!dao::isError())
        {
            $this->file->updateObjectID($this->post->uid, $taskID, 'testtask');
            return common::createChanges($oldTask, $task);
        }
    }

    /**
     * Start testtask.
     *
     * @param  int    $taskID
     * @access public
     * @return void
     */
    public function start($taskID)
    {
        $oldTesttask = $this->getById($taskID);
        $testtask = fixer::input('post')
            ->setDefault('status', 'doing')
            ->remove('comment')->get();

        $this->dao->update(TABLE_TESTTASK)->data($testtask)
            ->autoCheck()
            ->where('id')->eq((int)$taskID)
            ->exec();

        if(!dao::isError()) return common::createChanges($oldTesttask, $testtask);
    }

    /**
     * Close testtask.
     *
     * @access public
     * @return void
     */
    public function close($taskID)
    {
        $oldTesttask = $this->getById($taskID);
        $testtask = fixer::input('post')
            ->setDefault('status', 'done')
            ->stripTags($this->config->testtask->editor->close['id'], $this->config->allowedTags)
            ->join('mailto', ',')
            ->remove('comment,uid')
            ->get();

        $testtask = $this->loadModel('file')->processImgURL($testtask, $this->config->testtask->editor->close['id'], $this->post->uid);
        $this->dao->update(TABLE_TESTTASK)->data($testtask)
            ->autoCheck()
            ->where('id')->eq((int)$taskID)
            ->exec();

        if(!dao::isError())
        {
            $this->file->updateObjectID($this->post->uid, $taskID, 'testtask');
            return common::createChanges($oldTesttask, $testtask);
        }
    }

    /**
     * update block testtask.
     *
     * @param  int    $taskID
     * @access public
     * @return void
     */
    public function block($taskID)
    {
        $oldTesttask = $this->getById($taskID);
        $testtask = fixer::input('post')
            ->setDefault('status', 'blocked')
            ->remove('comment')->get();

        $this->dao->update(TABLE_TESTTASK)->data($testtask)
            ->autoCheck()
            ->where('id')->eq((int)$taskID)
            ->exec();

        if(!dao::isError()) return common::createChanges($oldTesttask, $testtask);
    }

    /**
     * update activate testtask.
     *
     * @param  int    $taskID
     * @access public
     * @return void
     */
    public function activate($taskID)
    {
        $oldTesttask = $this->getById($taskID);
        $testtask = fixer::input('post')
            ->setDefault('status', 'doing')
            ->remove('comment')->get();

        $this->dao->update(TABLE_TESTTASK)->data($testtask)
            ->autoCheck()
            ->where('id')->eq((int)$taskID)
            ->exec();

        if(!dao::isError()) return common::createChanges($oldTesttask, $testtask);
    }

    /**
     * Link cases.
     *
     * @param  int    $taskID
     * @param  string $type
     * @access public
     * @return void
     */
    public function linkCase($taskID, $type)
    {
        if($this->post->cases == false) return;
        $postData = fixer::input('post')->get();

        $this->loadModel('action');

        if($type == 'bybuild') $assignedToPairs = $this->dao->select('`case`, assignedTo')->from(TABLE_TESTRUN)->where('`case`')->in($postData)->fetchPairs('case', 'assignedTo');

        $task = $this->dao->select('oddNumber')->from(TABLE_TESTTASK)->where('id')->eq($taskID)->fetch();

        foreach($postData->cases as $caseID)
        {
            $row = new stdclass();
            $row->task       = $taskID;
            $row->case       = $caseID;
            $row->version    = $postData->versions[$caseID];
            $row->assignedTo = '';
            $row->status     = 'normal';
            if($type == 'bybuild') $row->assignedTo = zget($assignedToPairs, $caseID, '');
            $this->dao->replace(TABLE_TESTRUN)->data($row)->exec();

            $this->action->create('case', $caseID, 'linked2testtask', '', $task->oddNumber);

            /* When the cases linked the testtask, the cases link to the project. */
            if($this->app->openApp != 'qa')
            {
                $lastOrder = (int)$this->dao->select('*')->from(TABLE_PROJECTCASE)->where('project')->eq($projectID)->orderBy('order_desc')->limit(1)->fetch('order');
                $project   = $this->app->openApp == 'project' ? $this->session->project : $this->session->execution;

                $data = new stdclass();
                $data->project = $project;
                $data->product = $this->session->product;
                $data->case    = $caseID;
                $data->version = 1;
                $data->order   = ++ $lastOrder;
                $this->dao->replace(TABLE_PROJECTCASE)->data($data)->exec();
            }
        }
    }

    /**
     * Link bugs.
     *
     * @param  int    $taskID
     * @param  string $type
     * @access public
     * @return void
     */
    public function linkBug($taskID)
    {
        if($this->post->bugs == false) return;
        $postData = fixer::input('post')->get();

        foreach($postData->bugs as $bugID)
        {
            $bug = $this->dao->select('*')->from(TABLE_BUG)->where('id')->eq((int)$bugID)->fetch();
            
            $originalLinkTesttaskList = [];
            $originalLinkTesttask = $bug->linkTesttask;
            if(!empty($originalLinkTesttask))
            {
                $originalLinkTesttaskList = explode(',', $originalLinkTesttask);
            }

            if(in_array($taskID, $originalLinkTesttaskList)) continue;

            $originalLinkTesttaskList[] = $taskID;

            $this->dao->update(TABLE_BUG)->set('linkTesttask')->eq(implode(',', $originalLinkTesttaskList))->where('id')->eq((int)$bugID)->exec();
        }
    }

    /**
     * Get test runs of a test task.
     *
     * @param  int    $taskID
     * @param  int    $moduleID
     * @param  object $pager
     * @access public
     * @return array
     */
    public function getRuns($taskID, $moduleID, $orderBy, $pager = null)
    {
        /* Select the table for these special fields. */
        $specialFields = ',assignedTo,status,lastRunResult,lastRunner,lastRunDate,';
        $fieldToSort   = substr($orderBy, 0, strpos($orderBy, '_'));
        $orderBy       = strpos($specialFields, ',' . $fieldToSort . ',') !== false ? ('t1.' . $orderBy) : ('t2.' . $orderBy);
        $orderBy       = $this->replaceSortField($orderBy);

        return $this->dao->select('t2.*,t1.*,t2.version as caseVersion,t3.title as storyTitle,t2.status as caseStatus')->from(TABLE_TESTRUN)->alias('t1')
            ->leftJoin(TABLE_CASE)->alias('t2')->on('t1.case = t2.id')
            ->leftJoin(TABLE_STORY)->alias('t3')->on('t2.story = t3.id')
            ->where('t1.task')->eq((int)$taskID)
            ->andWhere('t2.deleted')->eq(0)
            ->beginIF($moduleID)->andWhere('t2.module')->in($moduleID)->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');
    }

    /**
     * Get test runs of a user.
     *
     * @param  int    $taskID
     * @param  int    $user
     * @param  obejct $pager
     * @access public
     * @return array
     */
    public function getUserRuns($taskID, $user, $modules = '', $orderBy, $pager = null)
    {
        /* Select the table for these special fields. */
        $specialFields = ',assignedTo,status,lastRunResult,lastRunner,lastRunDate,';
        $fieldToSort   = substr($orderBy, 0, strpos($orderBy, '_'));
        $orderBy       = strpos($specialFields, ',' . $fieldToSort . ',') !== false ? ('t1.' . $orderBy) : ('t2.' . $orderBy);
        $orderBy       = $this->replaceSortField($orderBy);

        return $this->dao->select('t2.*,t1.*,t2.version as caseVersion,t3.title as storyTitle,t2.status as caseStatus')->from(TABLE_TESTRUN)->alias('t1')
            ->leftJoin(TABLE_CASE)->alias('t2')->on('t1.case = t2.id')
            ->leftJoin(TABLE_STORY)->alias('t3')->on('t2.story = t3.id')
            ->where('t1.task')->eq((int)$taskID)
            ->andWhere('t1.assignedTo')->eq($user)
            ->andWhere('t2.deleted')->eq(0)
            ->beginIF($modules)->andWhere('t2.module')->in($modules)->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');
    }

    /**
     * Get testtask linked cases.
     *
     * @param  int    $productID
     * @param  string $browseType
     * @param  int    $queryID
     * @param  int    $moduleID
     * @param  string $sort
     * @param  object $pager
     * @param  object $task
     * @access public
     * @return array
     */
    public function getTaskCases($applicationID, $productID, $browseType, $queryID, $moduleID, $sort, $pager, $task)
    {
        /* Set modules and browse type. */
        $modules    = $moduleID ? $this->loadModel('tree')->getAllChildId($moduleID) : '0';
        $browseType = ($browseType == 'bymodule' and $this->session->taskCaseBrowseType and $this->session->taskCaseBrowseType != 'bysearch') ? $this->session->taskCaseBrowseType : $browseType;
        $browseType = strtolower($browseType);

        if($browseType == 'bymodule' or $browseType == 'all')
        {
            $runs = $this->getRuns($task->id, $modules, $sort, $pager);
        }
        elseif($browseType == 'assignedtome')
        {
            $runs = $this->getUserRuns($task->id, $this->session->user->account, $modules, $sort, $pager);
        }
        /* By search. */
        elseif($browseType == 'bysearch')
        {
            if($this->session->testtaskQuery == false) $this->session->set('testtaskQuery', ' 1 = 1');
            if($queryID)
            {
                $query = $this->loadModel('search')->getQuery($queryID);
                if($query)
                {
                    $this->session->set('testtaskQuery', $query->sql);
                    $this->session->set('testtaskForm', $query->form);
                }
            }

            $queryProductID = $productID;
            $allProduct     = "`product` = 'all'";
            $caseQuery      = $this->session->testtaskQuery;
            if(strpos($this->session->testtaskQuery, $allProduct) !== false)
            {
                $products  = $this->loadModel('rebirth')->getAllProductIdList($applicationID);
                $products  = implode(',', $products);
                $caseQuery = str_replace($allProduct, '1', $this->session->testtaskQuery);
                $caseQuery = $caseQuery . ' AND `product` ' . helper::dbIN($products);
                $queryProductID = 'all';
            }

            $caseQuery = preg_replace('/`(\w+)`/', 't2.`$1`', $caseQuery);
            $caseQuery = str_replace(array('t2.`assignedTo`', 't2.`lastRunner`', 't2.`lastRunDate`', 't2.`lastRunResult`', 't2.`status`'), array('t1.`assignedTo`', 't1.`lastRunner`', 't1.`lastRunDate`', 't1.`lastRunResult`', 't1.`status`'), $caseQuery);

            /* Select the table for these special fields. */
            $specialFields = ',assignedTo,status,lastRunResult,lastRunner,lastRunDate,';
            $fieldToSort   = substr($sort, 0, strpos($sort, '_'));
            $orderBy       = strpos($specialFields, ',' . $fieldToSort . ',') !== false ? ('t1.' . $sort) : ('t2.' . $sort);
            $orderBy       = $this->replaceSortField($orderBy);

            $runs = $this->dao->select('t2.*,t1.*, t2.version as caseVersion,t3.title as storyTitle,t2.status as caseStatus')->from(TABLE_TESTRUN)->alias('t1')
                ->leftJoin(TABLE_CASE)->alias('t2')->on('t1.case = t2.id')
                ->leftJoin(TABLE_STORY)->alias('t3')->on('t2.story = t3.id')
                ->where($caseQuery)
                ->andWhere('t1.task')->eq($task->id)
                ->andWhere('t2.applicationID')->eq($applicationID)
                ->andWhere('t2.deleted')->eq(0)
                ->beginIF($queryProductID != 'all')->andWhere('t2.product')->eq($queryProductID)->fi()
                ->beginIF($task->branch)->andWhere('t2.branch')->in("0,{$task->branch}")->fi()
                ->orderBy($orderBy)
                ->page($pager)
                ->fetchAll('id');
        }

        return $runs;
    }

    /**
     * Get testtask pairs of a user.
     *
     * @param  string $account
     * @param  int    $limit
     * @param  string $status all|wait|doing|done|blocked
     * @param  array $skipProductIDList
     * @param  array $skipExecutionIDList
     * @access public
     * @return array
     */
    public function getUserTestTaskPairs($account, $limit = 0, $status = 'all', $skipProductIDList = array(), $skipExecutionIDList = array())
    {
        $stmt = $this->dao->select('t1.id, t1.name, t2.name as execution')
            ->from(TABLE_TESTTASK)->alias('t1')
            ->leftjoin(TABLE_EXTENSION)->alias('t2')->on('t1.execution = t2.id')
            ->where('t1.owner')->eq($account)
            ->andWhere('t1.deleted')->eq(0)
            ->beginIF($status != 'all')->andWhere('t1.status')->in($status)->fi()
            ->beginIF(!empty($skipProductIDList))->andWhere('t1.product')->notin($skipProductIDList)->fi()
            ->beginIF(!empty($skipExecutionIDList))->andWhere('t1.execution')->notin($skipExecutionIDList)->fi()
            ->beginIF($limit)->limit($limit)->fi()
            ->query();

        $testtaskPairs = array();
        while($testtask = $stmt->fetch())
        {
            $testtaskPairs[$testtask->id] = $testtask->execution . ' / ' . $testtask->name;
        }
        return $testtaskPairs;
    }

    /**
     * Get info of a test run.
     *
     * @param  int   $runID
     * @access public
     * @return void
     */
    public function getRunById($runID)
    {
        $testRun = $this->dao->findById($runID)->from(TABLE_TESTRUN)->fetch();
        $testRun->case = $this->loadModel('testcase')->getById($testRun->case, $testRun->version);
        return $testRun;
    }

    /**
     * update Task status
     *
     * @param  int   $caseID
     * @access public
     */
    public function updateTaskStatus($caseID){
        $caseList = $this->dao->select('task')->from(TABLE_TESTRUN)
            ->where('`case`')->eq($caseID)
            ->fetchAll('task');
        $Ids = array_keys($caseList);
        $this->dao->update(TABLE_TESTTASK)->set('status')->eq('doing')
            ->where('status')->eq('wait')
            ->andWhere('id')->in($Ids)
            ->exec();
    }

    /**
     * Create test result
     *
     * @param  int   $runID
     * @access public
     * @return void
     */
    public function createResult($runID = 0)
    {
        /* Compute the test result.
         *
         * 1. if there result in the post, use it.
         * 2. if no result, set default is pass.
         * 3. then check the steps to compute result.
         *
         * */
        $postData   = fixer::input('post')->get();
        $caseResult = isset($postData->result) ? $postData->result : 'pass';
        if(isset($postData->steps) and $postData->steps)
        {
            foreach($postData->steps as $stepID => $stepResult)
            {
                if($stepResult != 'pass' and $stepResult != 'n/a')
                {
                    $caseResult = $stepResult;
                    break;
                }
            }
        }

        /* Create result of every step. */
        foreach($postData->steps as $stepID =>$stepResult)
        {
            $step['result'] = $stepResult;

            $_POST['tmpReals'] = $_POST['reals'][$stepID];
            $tmpReals = fixer::input('post')->stripTags('tmpReals', $this->config->allowedTags)->get();
            $tmpReals = $this->loadModel('file')->processImgURL($tmpReals, 'tmpReals', $this->post->uid);

            $step['real'] = $tmpReals->tmpReals;
            $stepResults[$stepID] = $step;
        }

        /* Insert into testResult table. */
        $now = helper::now();
        $result = fixer::input('post')
            ->add('run', $runID)
            ->add('caseResult', $caseResult)
            ->setForce('stepResults', serialize($stepResults))
            ->setDefault('lastRunner', $this->app->user->account)
            ->setDefault('date', $now)
            ->skipSpecial('stepResults')
            ->remove('tmpReals,steps,reals,result')
            ->get();

        /* Remove files and labels field when uploading files for case result or step result. */
        foreach($result as $fieldName => $field)
        {
            if((strpos($fieldName, 'files') !== false) or (strpos($fieldName, 'labels') !== false)) unset($result->$fieldName);
        }

        $this->dao->insert(TABLE_TESTRESULT)->data($result)->autoCheck()->exec();

        /* Save upload files for case result or step result. */
        if(!dao::isError())
        {
            $resultID = $this->dao->lastInsertID();
            $this->file->updateObjectID($this->post->uid, $resultID, 'testresult');

            foreach($stepResults as $stepID => $stepResult) $this->loadModel('file')->saveUpload('stepResult', $resultID, $stepID, "files{$stepID}", "labels{$stepID}");
        }
        $this->dao->update(TABLE_CASE)->set('lastRunner')->eq($this->app->user->account)->set('lastRunDate')->eq($now)->set('lastRunResult')->eq($caseResult)->where('id')->eq($postData->case)->exec();

        if($runID)
        {
            /* Update testRun's status. */
            if(!dao::isError())
            {
                $runStatus = $caseResult == 'blocked' ? 'blocked' : 'normal';
                $this->dao->update(TABLE_TESTRUN)
                    ->set('lastRunResult')->eq($caseResult)
                    ->set('status')->eq($runStatus)
                    ->set('lastRunner')->eq($this->app->user->account)
                    ->set('lastRunDate')->eq($now)
                    ->where('id')->eq($runID)
                    ->exec();
            }
        }

        if(!dao::isError()) $this->loadModel('score')->create('testtask', 'runCase', $runID);

        return $caseResult;
    }

    /**
     * Batch run case
     *
     * @param  string $runCaseType
     * @access public
     * @return void
     */
    public function batchRun($runCaseType = 'testcase', $taskID = 0)
    {
        $runs       = array();
        $postData   = fixer::input('post')->get();
        $caseIdList = isset($postData->caseIDList) ? array_keys($postData->caseIDList) : array_keys($postData->results);
        foreach($caseIdList as $caseId)  $this->updateTaskStatus($caseId); 
        if($runCaseType == 'testtask')
        {
            $runs = $this->dao->select('id, `case`')->from(TABLE_TESTRUN)
                ->where('`case`')->in($caseIdList)
                ->beginIF($taskID)->andWhere('task')->eq($taskID)->fi()
                ->fetchPairs('case', 'id');
        }

        $stepGroups = $this->dao->select('t1.*')->from(TABLE_CASESTEP)->alias('t1')
            ->leftJoin(TABLE_CASE)->alias('t2')->on('t1.case = t2.id')
            ->where('t1.case')->in($caseIdList)
            ->andWhere('t1.version=t2.version')
            ->andWhere('t2.status')->ne('wait')
            ->fetchGroup('case', 'id');

        $now = helper::now();
        $this->loadModel('action');
        foreach($postData->results as $caseID => $result)
        {
            $runID       = isset($runs[$caseID]) ? $runs[$caseID] : 0;
            $version     = $postData->version[$caseID];
            $dbSteps     = isset($stepGroups[$caseID]) ? $stepGroups[$caseID] : array();
            $postSteps   = isset($postData->steps[$caseID]) ? $postData->steps[$caseID] : array();
            $postReals   = $postData->reals[$caseID];

            $caseResult  = $result ? $result : 'pass';
            $stepResults = array();
            if($dbSteps)
            {
                foreach($dbSteps as $stepID => $step)
                {
                    $step           = array();
                    $step['result'] = $caseResult == 'pass' ? $caseResult : $postSteps[$stepID];
                    $step['real']   = $caseResult == 'pass' ? '' : $postReals[$stepID];
                    $stepResults[$stepID] = $step;
                }
            }
            else
            {
                $step           = array();
                $step['result'] = $caseResult;
                $step['real']   = $caseResult == 'pass' ? '' : $postReals[0];
                $stepResults[]  = $step;
            }

            /* Replace caseID if caseID is runID. */
            if(isset($postData->caseIDList[$caseID])) $caseID = $postData->caseIDList[$caseID];

            $result              = new stdClass();
            $result->run         = $runID;
            $result->case        = $caseID;
            $result->version     = $version;
            $result->caseResult  = $caseResult;
            $result->stepResults = serialize($stepResults);
            $result->lastRunner  = $this->app->user->account;
            $result->date        = $now;
            $this->dao->insert(TABLE_TESTRESULT)->data($result)->autoCheck()->exec();
            $this->dao->update(TABLE_CASE)->set('lastRunner')->eq($this->app->user->account)->set('lastRunDate')->eq($now)->set('lastRunResult')->eq($caseResult)->where('id')->eq($caseID)->exec();

            $this->action->create('case', $caseID, 'run');

            if($runID)
            {
                /* Update testRun's status. */
                if(!dao::isError())
                {
                    $runStatus = $caseResult == 'blocked' ? 'blocked' : 'normal';
                    $this->dao->update(TABLE_TESTRUN)
                        ->set('lastRunResult')->eq($caseResult)
                        ->set('status')->eq($runStatus)
                        ->set('lastRunner')->eq($this->app->user->account)
                        ->set('lastRunDate')->eq($now)
                        ->where('id')->eq($runID)
                        ->exec();
                }
            }
        }
    }

    /**
     * Get results by runID or caseID
     *
     * @param  int   $runID
     * @param  int   $caseID
     * @access public
     * @return array
     */
    public function getResults($runID, $caseID = 0)
    {
        if($runID > 0)
        {
            $results = $this->dao->select('*')->from(TABLE_TESTRESULT)->where('run')->eq($runID)->orderBy('id desc')->fetchAll('id');
        }
        else
        {
            $results = $this->dao->select('*')->from(TABLE_TESTRESULT)->where('`case`')->eq($caseID)->orderBy('id desc')->fetchAll('id');
        }

        if(!$results) return array();

        $relatedVersions = array();
        $runIdList       = array();
        foreach($results as $result)
        {
            $runIdList[$result->run] = $result->run;
            $relatedVersions[]       = $result->version;
            $runCaseID               = $result->case;
        }
        $relatedVersions = array_unique($relatedVersions);

        $relatedSteps = $this->dao->select('*')->from(TABLE_CASESTEP)
            ->where('`case`')->eq($runCaseID)
            ->andWhere('version')->in($relatedVersions)
            ->orderBy('id')
            ->fetchGroup('version', 'id');
        $runs = $this->dao->select('t1.id,t2.build')->from(TABLE_TESTRUN)->alias('t1')
            ->leftJoin(TABLE_TESTTASK)->alias('t2')->on('t1.task=t2.id')
            ->where('t1.id')->in($runIdList)
            ->fetchPairs();

        $this->loadModel('file');
        $files = $this->dao->select('*')->from(TABLE_FILE)
            ->where("(objectType = 'caseResult' or objectType = 'stepResult')")
            ->andWhere('objectID')->in(array_keys($results))
            ->andWhere('extra')->ne('editor')
            ->orderBy('id')
            ->fetchAll();
        $resultFiles = array();
        $stepFiles   = array();
        foreach($files as $file)
        {
            $pathName = $this->file->getRealPathName($file->pathname);
            $file->webPath  = $this->file->webPath . $pathName;
            $file->realPath = $this->file->savePath . $pathName;
            if($file->objectType == 'caseResult')
            {
                $resultFiles[$file->objectID][$file->id] = $file;
            }
            elseif($file->objectType == 'stepResult' and $file->extra !== '')
            {
                $stepFiles[$file->objectID][(int)$file->extra][$file->id] = $file;
            }
        }
        foreach($results as $resultID => $result)
        {
            $result->stepResults = unserialize($result->stepResults);
            $result->build       = $result->run ? zget($runs, $result->run, 0) : 0;
            $result->files       = zget($resultFiles, $resultID, array()); //Get files of case result.
            if(isset($relatedSteps[$result->version]))
            {
                $relatedStep = $relatedSteps[$result->version];
                foreach($relatedStep as $stepID => $step)
                {
                    $relatedStep[$stepID] = (array)$step;
                    if(isset($result->stepResults[$stepID]))
                    {
                        $relatedStep[$stepID]['result'] = $result->stepResults[$stepID]['result'];

                        $realData = new stdClass();
                        $realData->result = $result->stepResults[$stepID]['real'];
                        $realData = $this->loadModel('file')->replaceImgURL($realData, 'result');
                        $relatedStep[$stepID]['real'] = $realData->result;
                    }
                }
                $result->stepResults = $relatedStep;
            }

            /* Get files of step result. */
            foreach($result->stepResults as $stepID => $stepResult)
            {
                $result->stepResults[$stepID]['files'] = isset($stepFiles[$resultID][$stepID]) ? $stepFiles[$resultID][$stepID] : array();
            }
        }
        return $results;
    }

    /**
     * Judge an action is clickable or not.
     *
     * @param  object $product
     * @param  string $action
     * @access public
     * @return void
     */
    public static function isClickable($testtask, $action)
    {
        $action = strtolower($action);

        if($action == 'start')    return $testtask->status  == 'wait';
        if($action == 'block')    return ($testtask->status == 'doing'   || $testtask->status == 'wait');
        if($action == 'activate') return ($testtask->status == 'blocked' || $testtask->status == 'done');
        if($action == 'close')    return $testtask->status != 'done';
        if($action == 'runcase' and isset($testtask->auto) and $testtask->auto == 'unit')  return false;
        if($action == 'runcase')  return isset($testtask->caseStatus) ? $testtask->caseStatus != 'wait' : $testtask->status != 'wait';
        return true;
    }

    /**
     * Print cell data.
     * 
     * @param  object  $col
     * @param  object  $run
     * @param  array   $users
     * @param  object  $task
     * @param  array   $branches
     * @param  string  $mode
     * @param  array   $products
     * @param  array   $projects
     * @access public
     * @return void
     */
    public function printCell($col, $run, $users, $task, $branches, $mode = 'datatable', $products = array(), $projects = array())
    {
        $canBatchEdit   = common::hasPriv('testcase', 'batchEdit');
        $canBatchUnlink = common::hasPriv('testtask', 'batchUnlinkCases');
        $canBatchAssign = common::hasPriv('testtask', 'batchAssign');
        $canBatchRun    = common::hasPriv('testtask', 'batchRun');

        $canBatchAction = ($canBatchEdit or $canBatchUnlink or $canBatchAssign or $canBatchRun);

        $canView      = common::hasPriv('testcase', 'view');
        $caseLink     = helper::createLink('testcase', 'view', "caseID=$run->case&version=$run->version&from=testtask&taskID=$run->task");
        $account      = $this->app->user->account;
        $id           = $col->id;
        $caseChanged  = $run->version < $run->caseVersion;
        $fromCaseID   = $run->fromCaseID;
        $projectParam = $this->app->openApp == 'project' ? "projectID={$this->session->project}," : '';

        if($col->show)
        {
            $class = "c-$id ";
            if($id == 'status') $class .= $run->status;
            if($id == 'title')  $class .= ' text-left';
            if($id == 'id')     $class .= ' cell-id';
            if($id == 'lastRunResult') $class .= " $run->lastRunResult";
            if($id == 'assignedTo' && $run->assignedTo == $account) $class .= ' red';
            if($id == 'actions') $class .= ' c-actions text-right';

            echo "<td class='" . $class . "'" . ($id=='title' ? "title='{$run->title}'":'') . ">";
            if(isset($this->config->bizVersion)) $this->loadModel('flow')->printFlowCell('testcase', $run, $id);
            switch ($id)
            {
            case 'id':
                if($canBatchAction)
                {
                    echo html::checkbox('caseIDList', array($run->case => sprintf('%03d', $run->case)));
                }
                else
                {
                    printf('%03d', $run->case);
                }
                break;
            case 'pri':
                echo "<span class='label-pri label-pri-" . $run->pri . "' title='" . zget($this->lang->testcase->priList, $run->pri, $run->pri) . "'>";
                echo zget($this->lang->testcase->priList, $run->pri, $run->pri);
                echo "</span>";
                break;
            case 'title':
                if($run->branch) echo "<span class='label label-info label-outline'>{$branches[$run->branch]}</span>";
                if($canView)
                {
                    if($fromCaseID)
                    {
                        echo html::a($caseLink, $run->title, null, "style='color: $run->color'  data-app='{$this->app->openApp}'") . html::a(helper::createLink('testcase', 'view', "caseID=$fromCaseID"), "[<i class='icon icon-share' title='{$this->lang->testcase->fromCase}'></i>#$fromCaseID]");
                    }
                    else
                    {
                        echo html::a($caseLink, $run->title, null, "style='color: $run->color' data-app='{$this->app->openApp}'");
                    }
                }
                else
                {
                    echo "<span style='color: $run->color'>$run->title</span>";
                }
                break;
            case 'product':
                $product = $run->product;
                if(!$product) $product = 'na';
                echo zget($products, $product, '');
                break;
            case 'project':
                echo zget($projects, $run->project, '');
                break;
            case 'branch':
                echo $branches[$run->branch];
                break;
            case 'type':
                echo $this->lang->testcase->typeList[$run->type];
                break;
            case 'stage':
                foreach(explode(',', trim($run->stage, ',')) as $stage) echo $this->lang->testcase->stageList[$stage] . '<br />';
                break;
            case 'status':
                if($caseChanged)
                {
                    echo "<span class='warning'>{$this->lang->testcase->changed}</span>";
                }
                else
                {
                    $status = $this->processStatus('testcase', $run);
                    if($run->status == $status) $status = $this->processStatus('testtask', $run);
                    echo $status;
                }
                break;
            case 'precondition':
                echo $run->precondition;
                break;
            case 'keywords':
                echo $run->keywords;
                break;
            case 'version':
                echo $run->version;
                break;
            case 'openedBy':
                echo zget($users, $run->openedBy);
                break;
            case 'openedDate':
                echo substr($run->openedDate, 5, 11);
                break;
            case 'reviewedBy':
                echo zget($users, $run->reviewedBy);
                break;
            case 'reviewedDate':
                echo substr($run->reviewedDate, 5, 11);
                break;
            case 'lastEditedBy':
                echo zget($users, $run->lastEditedBy);
                break;
            case 'lastEditedDate':
                echo substr($run->lastEditedDate, 5, 11);
                break;
            case 'lastRunner':
                echo zget($users, $run->lastRunner);
                break;
            case 'lastRunDate':
                if(!helper::isZeroDate($run->lastRunDate)) echo date(DT_MONTHTIME1, strtotime($run->lastRunDate));
                break;
            case 'lastRunResult':
                $lastRunResultText = $run->lastRunResult ? zget($this->lang->testcase->resultList, $run->lastRunResult, $run->lastRunResult) : $this->lang->testcase->unexecuted;
                $class = 'result-' . $run->lastRunResult;
                echo "<span class='$class'>" . $lastRunResultText . "</span>";
                break;
            case 'story':
                if($run->story and $run->storyTitle) echo html::a(helper::createLink('story', 'view', "storyID=$run->story"), $run->storyTitle);
                break;
            case 'assignedTo':
                echo zget($users, $run->assignedTo);
                break;
            case 'bugs':
                echo (common::hasPriv('testcase', 'bugs') and $run->bugs) ? html::a(helper::createLink('testcase', 'bugs', "runID={$run->id}&caseID={$run->case}"), $run->bugs, '', "class='iframe'") : $run->bugs;
                break;
            case 'results':
                echo (common::hasPriv('testtask', 'results') and $run->results) ? html::a(helper::createLink('testtask', 'results', "runID={$run->id}&caseID={$run->case}"), $run->results, '', "class='iframe'") : $run->results;
                break;
            case 'stepNumber':
                echo $run->stepNumber;
                break;
            case 'actions':
                if($caseChanged)
                {
                    common::printIcon('testtask', 'confirmChange', "id=$run->case&taskID=$run->task", $run, 'list', 'search', 'hiddenwin');
                    break;
                }

                common::printIcon('testcase', 'createBug', "applicationID=$run->applicationID&product=$run->product&branch=$run->branch&extras={$projectParam}buildID=$task->build,caseID=$run->case,version=$run->version,runID=$run->id,testtask=$task->id", $run, 'list', 'bug', '', 'iframe', '', "data-width='90%'");

                common::printIcon('testtask', 'results', "id=$run->id", $run, 'list', '', '', 'iframe', '', "data-width='90%'");
                common::printIcon('testtask', 'runCase', "id=$run->id", $run, 'list', 'play', '', 'runCase iframe', false, "data-width='95%'");

                if(common::hasPriv('testtask', 'unlinkCase', $run))
                {
                    $unlinkURL = helper::createLink('testtask', 'unlinkCase', "caseID=$run->id&confirm=yes");
                    echo html::a("javascript:void(0)", '<i class="icon-unlink"></i>', '', "title='{$this->lang->testtask->unlinkCase}' class='btn' onclick='ajaxDelete(\"$unlinkURL\", \"casesForm\", confirmUnlink)'");
                }

                break;
            }
            echo '</td>';
        }
    }

    /**
     * Send mail.
     *
     * @param  int    $testtaskID
     * @param  int    $actionID
     * @access public
     * @return void
     */
    public function sendmail($testtaskID, $actionID)
    {
        $this->loadModel('mail');
        $testtask = $this->getByID($testtaskID);
        $build    = $this->loadModel('build')->getByID($testtask->build);
        $planList = !isset($build->product) ? array() : $this->loadModel('productplan')->getPairs($build->product);
        $version  =  array('0' => '','1'=>'无') + $planList;
        $users    = $this->loadModel('user')->getPairs('noletter');
        $applicationList = $this->loadModel('rebirth')->getApplicationPairs();
        $products = $this->loadModel('rebirth')->getProductPairs($testtask->applicationID, true);
        $mailConf = $this->lang->testtask->mailConf;

        /* Get action info. */
        $action          = $this->loadModel('action')->getById($actionID);
        $history         = $this->action->getHistory($actionID);
        $action->history = isset($history[$actionID]) ? $history[$actionID] : array();

        /* Get mail content. */
        $modulePath = $this->app->getModulePath($appName = '', 'testtask');
        $oldcwd     = getcwd();
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

        $sendUsers = $this->getToAndCcList($testtask);
        if(!$sendUsers) return;
        list($toList, $ccList) = $sendUsers;
        $subject = $this->getSubject($testtask, $action->action);

        /* Send mail. */
        $this->mail->send($toList, $subject, $mailContent, $ccList);
        if($this->mail->isError()) error_log(join("\n", $this->mail->getError()));
    }

    /**
     * Get mail subject.
     *
     * @param  object    $testtask
     * @param  string    $actionType
     * @access public
     * @return string
     */
    public function getSubject($testtask, $actionType)
    {
        /* Set email title. */
        if($actionType == 'opened')
        {
            return sprintf($this->lang->testtask->mail->create->title, $this->app->user->realname, $testtask->id, $testtask->name);
        }
        elseif($actionType == 'closed')
        {
            return sprintf($this->lang->testtask->mail->close->title, $this->app->user->realname, $testtask->id, $testtask->name);
        }
        else
        {
            return sprintf($this->lang->testtask->mail->edit->title, $this->app->user->realname, $testtask->id, $testtask->name);
        }
    }

    /**
     * Get toList and ccList.
     *
     * @param  object    $testtask
     * @access public
     * @return bool|array
     */
    public function getToAndCcList($testtask)
    {
        /* Set toList and ccList. */
        $toList   = $testtask->owner;
        $ccList   = str_replace(' ', '', trim($testtask->mailto, ','));

        if(empty($toList))
        {
            if(empty($ccList)) return false;
            if(strpos($ccList, ',') === false)
            {
                $toList = $ccList;
                $ccList = '';
            }
            else
            {
                $commaPos = strpos($ccList, ',');
                $toList   = substr($ccList, 0, $commaPos);
                $ccList   = substr($ccList, $commaPos + 1);
            }
        }
        return array($toList, $ccList);
    }

    /**
     * Import unit results.
     *
     * @param  int    $productID
     * @access public
     * @return string
     */
    public function importUnitResult($applicationID, $productID, $projectID)
    {
        $file = $this->loadModel('file')->getUpload('resultFile');
        if(empty($file))
        {
            dao::$errors[] = $this->lang->testtask->unitXMLFormat;
            die(js::error(dao::getError()));
        }

        $file     = $file[0];
        $fileName = $this->file->savePath . $this->file->getSaveName($file['pathname']);
        move_uploaded_file($file['tmpname'], $fileName);
        if(simplexml_load_file($fileName) === false)
        {
            dao::$errors[] = $this->lang->testtask->cannotBeParsed;
            die(js::error(dao::getError()));
        }

        $frame = $this->post->frame;
        unset($_POST['frame']);

        $data = $this->parseXMLResult($fileName, $applicationID, $productID, $projectID, $frame);
        if($frame == 'cppunit' and empty($data['cases'])) $data = $this->parseCppXMLResult($fileName, $productID, $frame);

        /* Create task. */
        $this->post->set('auto', 'unit');
        $testtaskID = $this->create();

        unlink($fileName);
        unset($_SESSION['resultFile']);
        if(dao::isError()) return false;

        return $this->processAutoResult($testtaskID, $applicationID, $productID, $data['suites'], $data['cases'], $data['results'], $data['suiteNames'], $data['caseTitles'], 'unit');
    }

    /**
     * Process auto test result.
     *
     * @param  int    $testtaskID
     * @param  int    $productID
     * @param  array  $suites
     * @param  array  $cases
     * @param  array  $results
     * @param  array  $suiteNames
     * @param  array  $caseTitles
     * @param  string $auto     unit|func
     * @access public
     * @return int
     */
    public function processAutoResult($testtaskID, $applicationID, $productID, $suites, $cases, $results, $suiteNames = array(), $caseTitles = array(), $auto = 'unit')
    {
        if(empty($cases)) die(js::alert($this->lang->testtask->noImportData));

        /* Import cases and link task and insert result. */
        $this->loadModel('action');
        $existSuites = $this->dao->select('*')->from(TABLE_TESTSUITE)
            ->where('name')->in($suiteNames)
            ->andWhere('applicationID')->eq($applicationID)
            ->andWhere('product')->eq($productID)
            ->andWhere('type')->eq($auto)
            ->andWhere('deleted')->eq(0)
            ->fetchPairs('name', 'id');

        foreach($suites as $suiteIndex => $suite)
        {
            $suiteID = 0;
            if($suite)
            {
                if(!isset($existSuites[$suite->name]))
                {
                    $this->dao->insert(TABLE_TESTSUITE)->data($suite)->exec();
                    $suiteID = $this->dao->lastInsertID();
                    $this->action->create('testsuite', $suiteID, 'opened');
                }
                else
                {
                    $suiteID = $existSuites[$suite->name];
                }
            }

            $existCases = array();

            foreach($cases[$suiteIndex] as $i => $case)
            {
                if(isset($case->id))
                {
                    $caseID = $case->id;
                    $this->dao->update(TABLE_CASE)->data($case)->where('id')->eq($caseID)->exec();
                }
                elseif(!isset($existCases[$case->title]))
                {
                    $this->dao->insert(TABLE_CASE)->data($case)->exec();
                    $caseID = $this->dao->lastInsertID();
                    $this->action->create('case', $caseID, 'Opened');
                }
                else
                {
                    $caseID = $existCases[$case->title];
                }

                $testrun = new stdclass();
                $testrun->task          = $testtaskID;
                $testrun->case          = $caseID;
                $testrun->version       = $case->version;
                $testrun->lastRunner    = $case->lastRunner;
                $testrun->lastRunDate   = $case->lastRunDate;
                $testrun->lastRunResult = $case->lastRunResult;
                $testrun->status        = 'done';

                $this->dao->replace(TABLE_TESTRUN)->data($testrun)->exec();
                $runID = $this->dao->lastInsertID();

                if($suiteID)
                {
                    $suitecase = new stdclass();
                    $suitecase->suite   = $suiteID;
                    $suitecase->case    = $caseID;
                    $suitecase->version = $case->version;
                    $suitecase->product = $case->product;
                    $this->dao->replace(TABLE_SUITECASE)->data($suitecase)->exec();
                }

                $testresult = $results[$suiteIndex][$i];
                $testresult->run  = $runID;
                $testresult->case = $caseID;
                $this->dao->insert(TABLE_TESTRESULT)->data($testresult)->exec();
            }
        }

        return $testtaskID;
    }

    /**
     * Parse cppunit XML result.
     *
     * @param  string $fileName
     * @param  int    $productID
     * @param  string $frame
     * @access public
     * @return array
     */
    public function parseCppXMLResult($fileName, $productID, $frame)
    {
        /* Parse result xml. */
        $parsedXML = simplexml_load_file($fileName);

        /* Get testcase node. */
        $failNodes  = $parsedXML->xpath('FailedTests/FailedTest');
        $passNodes  = $parsedXML->xpath('SuccessfulTests/Test');
        $matchNodes = array_merge($failNodes, $passNodes);
        if(count($matchNodes) == 0) return array('suites' => array(), 'cases' => array(), 'results' => array(), 'suiteNames' => array(), 'caseTitles' => array());

        /* Get cases and results by parsed node. */
        $now        = helper::now();
        $cases      = array();
        $results    = array();
        $caseTitles = array();
        $suiteNames = array();
        $suiteIndex = 0;
        $suites     = array($suiteIndex => '');
        foreach($matchNodes as $caseIndex => $matchNode)
        {
            $case = new stdclass();
            $case->product    = $productID;
            $case->title      = (string)$matchNode->Name;
            $case->pri        = 3;
            $case->type       = 'unit';
            $case->stage      = 'unittest';
            $case->status     = 'normal';
            $case->openedBy   = $this->app->user->account;
            $case->openedDate = $now;
            $case->version    = 1;
            $case->auto       = 'unit';
            $case->frame      = $frame ? $frame : 'junit';

            $result = new stdclass();
            $result->case       = 0;
            $result->version    = 1;
            $result->caseResult = 'pass';
            $result->lastRunner = $this->app->user->account;
            $result->date       = $now;
            $result->duration   = 0;
            $result->xml        = $matchNode->asXML();
            $result->stepResults[0]['result'] = 'pass';
            $result->stepResults[0]['real']   = '';
            if(isset($matchNode->Message))
            {
                $result->caseResult = 'fail';
                $result->stepResults[0]['result'] = 'fail';
                $result->stepResults[0]['real']   = (string)$matchNode->Message;
            }
            $result->stepResults = serialize($result->stepResults);
            $case->lastRunner    = $this->app->user->account;
            $case->lastRunDate   = $now;
            $case->lastRunResult = $result->caseResult;

            $caseTitles[$suiteIndex][]        = $case->title;
            $cases[$suiteIndex][$caseIndex]   = $case;
            $results[$suiteIndex][$caseIndex] = $result;
        }

        return array('suites' => $suites, 'cases' => $cases, 'results' => $results, 'suiteNames' => $suiteNames, 'caseTitles' => $caseTitles);
    }

    /**
     * Parse unit result from xml.
     *
     * @param  string $fileName
     * @param  int    $productID
     * @param  string $frame
     * @access public
     * @return array
     */
    public function parseXMLResult($fileName, $applicationID, $productID, $projectID, $frame)
    {
        /* Parse result xml. */
        $rules     = zget($this->config->testtask->unitResultRules, $frame, $this->config->testtask->unitResultRules->common);
        $parsedXML = simplexml_load_file($fileName);

        /* Get testcase node. */
        $matchPaths = $rules['path'];
        $nameFields = $rules['name'];
        $failure    = $rules['failure'];
        $skipped    = $rules['skipped'];
        $suiteField = $rules['suite'];
        $aliasSuite = zget($rules, 'aliasSuite', array());
        $aliasName  = zget($rules, 'aliasName', array());
        $matchNodes = array();
        foreach($matchPaths as $matchPath)
        {
            $matchNodes = $parsedXML->xpath($matchPath);
            if(count($matchNodes) != 0) break;
        }
        if(count($matchNodes) == 0) return array('suites' => array(), 'cases' => array(), 'results' => array(), 'suiteNames' => array(), 'caseTitles' => array());

        $parentPath  = '';
        $caseNode    = $matchPath;
        $parentNodes = array($parsedXML);
        if(strpos($matchPath, '/') !== false)
        {
            $explodedPath = explode('/', $matchPath);
            $caseNode     = array_pop($explodedPath);
            $parentPath   = implode('/', $explodedPath);
            $parentNodes  = $parsedXML->xpath($parentPath);
        }

        /* Get cases and results by parsed node. */
        $now        = helper::now();
        $cases      = array();
        $results    = array();
        $suites     = array();
        $caseTitles = array();
        $suiteNames = array();
        foreach($parentNodes as $suiteIndex => $parentNode)
        {
            $caseNodes  = $parentNode->xpath($caseNode);
            $attributes = $parentNode->attributes();
            $suite      = '';
            if(isset($attributes[$suiteField]))
            {
                $suite                = new stdclass();
                $suite->applicationID = $applicationID;
                $suite->product       = $productID;
                $suite->project       = $projectID;
                $suite->name          = (string)$attributes[$suiteField];
                $suite->type          = 'unit';
                $suite->addedBy       = $this->app->user->account;
                $suite->addedDate     = $now;
                $suiteNames[]         = $suite->name;
            }
            else
            {
                $attributes = $caseNodes[0]->attributes();
                foreach($aliasSuite as $alias)
                {
                    if(isset($attributes[$alias]))
                    {
                        $suite                = new stdclass();
                        $suite->applicationID = $applicationID;
                        $suite->product       = $productID;
                        $suite->project       = $projectID;
                        $suite->name          = (string)$attributes[$alias];
                        $suite->type          = 'unit';
                        $suite->addedBy       = $this->app->user->account;
                        $suite->addedDate     = $now;
                        $suiteNames[]         = $suite->name;
                        break;
                    }
                }
            }
            $suites[$suiteIndex] = $suite;

            foreach($caseNodes as $caseIndex => $matchNode)
            {
                $case                = new stdclass();
                $case->product       = $productID;
                $case->project       = $projectID;
                $case->applicationID = $applicationID;
                $case->title         = '';
                $case->pri           = 3;
                $case->type          = 'unit';
                $case->stage         = 'unittest';
                $case->status        = 'normal';
                $case->openedBy      = $this->app->user->account;
                $case->openedDate    = $now;
                $case->version       = 1;
                $case->auto          = 'unit';
                $case->frame         = $frame ? $frame : 'junit';

                $attributes = $matchNode->attributes();
                foreach($nameFields as $field)
                {
                    if(!isset($attributes[$field])) continue;
                    $case->title .= (string)$attributes[$field] . ' ';
                }
                $case->title = trim($case->title);
                if(empty($case->title))
                {
                    foreach($aliasName as $field)
                    {
                        if(!isset($attributes[$field])) continue;
                        $case->title .= (string)$attributes[$field] . ' ';
                    }
                    $case->title = trim($case->title);
                }
                if(empty($case->title)) continue;

                $result = new stdclass();
                $result->case       = 0;
                $result->version    = 1;
                $result->caseResult = 'pass';
                $result->lastRunner = $this->app->user->account;
                $result->date       = $now;
                $result->duration   = isset($attributes['time']) ? (float)$attributes['time'] : 0;
                $result->xml        = $matchNode->asXML();
                $result->stepResults[0]['result'] = 'pass';
                $result->stepResults[0]['real']   = '';
                if(isset($matchNode->$failure))
                {
                    $result->caseResult = 'fail';
                    $result->stepResults[0]['result'] = 'fail';
                    if(is_string($matchNode->$failure))
                    {
                        $result->stepResults[0]['real'] = (string)$matchNode->$failure;
                    }
                    elseif(isset($matchNode->$failure[0]))
                    {
                        $result->stepResults[0]['real'] = (string)$matchNode->$failure[0];
                    }
                    else
                    {
                        $failureAttrs = $matchNode->$failure->attributes();
                        $result->stepResults[0]['real'] = (string)$failureAttrs['message'];
                    }
                }
                elseif(isset($matchNode->$skipped))
                {
                    $result->caseResult = 'n/a';
                    $result->stepResults[0]['result'] = 'n/a';
                    $result->stepResults[0]['real']   = '';
                }
                $result->stepResults = serialize($result->stepResults);
                $case->lastRunner    = $this->app->user->account;
                $case->lastRunDate   = $now;
                $case->lastRunResult = $result->caseResult;

                $caseTitles[$suiteIndex][]        = $case->title;
                $cases[$suiteIndex][$caseIndex]   = $case;
                $results[$suiteIndex][$caseIndex] = $result;
            }
        }

        return array('suites' => $suites, 'cases' => $cases, 'results' => $results, 'suiteNames' => $suiteNames, 'caseTitles' => $caseTitles);
    }

    /**
     * Parse unit result from ztf.
     *
     * @param  array  $caseResults
     * @param  string $frame
     * @param  int    $productID
     * @param  int    $jobID
     * @param  int    $compileID
     * @access public
     * @return array
     */
    public function parseZTFUnitResult($caseResults, $frame, $productID, $jobID, $compileID)
    {
        $now        = helper::now();
        $cases      = array();
        $results    = array();
        $suites     = array();
        $caseTitles = array();
        $suiteNames = array();
        $suiteIndex = 0;
        foreach($caseResults as $caseIndex => $caseResult)
        {
            $suite = '';
            if(isset($caseResult->testSuite) and !isset($suiteNames[$caseResult->testSuite]))
            {
                $suite = new stdclass();
                $suite->product   = $productID;
                $suite->name      = $caseResult->testSuite;
                $suite->type      = 'unit';
                $suite->addedBy   = $this->app->user->account;
                $suite->addedDate = $now;

                $suiteNames[$suite->name] = $suite->name;
                $suiteIndex ++;
            }
            if(!isset($suites[$suiteIndex])) $suites[$suiteIndex] = $suite;

            $case = new stdclass();
            $case->product    = $productID;
            $case->title      = $caseResult->title;
            $case->pri        = 3;
            $case->type       = 'unit';
            $case->stage      = 'unittest';
            $case->status     = 'normal';
            $case->openedBy   = $this->app->user->account;
            $case->openedDate = $now;
            $case->version    = 1;
            $case->auto       = 'unit';
            $case->frame      = $frame;

            $result = new stdclass();
            $result->case       = 0;
            $result->version    = 1;
            $result->caseResult = 'pass';
            $result->lastRunner = $this->app->user->account;
            $result->job        = $jobID;
            $result->compile    = $compileID;
            $result->date       = $now;
            $result->duration   = zget($caseResult, 'duration', 0);
            $result->stepResults[0]['result'] = 'pass';
            $result->stepResults[0]['real']   = '';
            if(!empty($caseResult->failure))
            {
                $result->caseResult = 'fail';
                $result->stepResults[0]['result'] = 'fail';
                $result->stepResults[0]['real']   = zget($caseResult->failure, 'desc', '');
            }
            $result->stepResults = serialize($result->stepResults);
            $case->lastRunner    = $this->app->user->account;
            $case->lastRunDate   = $now;
            $case->lastRunResult = $result->caseResult;

            $caseTitles[$suiteIndex][]        = $case->title;
            $cases[$suiteIndex][$caseIndex]   = $case;
            $results[$suiteIndex][$caseIndex] = $result;
        }

        return array('suites' => $suites, 'cases' => $cases, 'results' => $results, 'suiteNames' => $suiteNames, 'caseTitles' => $caseTitles);
    }

    /**
     * Parse function result from ztf.
     *
     * @param  array  $caseResults
     * @param  string $frame
     * @param  int    $productID
     * @param  int    $jobID
     * @param  int    $compileID
     * @access public
     * @return array
     */
    public function parseZTFFuncResult($caseResults, $frame, $productID, $jobID, $compileID)
    {
        $now        = helper::now();
        $cases      = array();
        $results    = array();
        $suites     = array();
        $caseTitles = array();
        $suiteNames = array();
        $suiteIndex = 0;
        foreach($caseResults as $caseIndex => $caseResult)
        {
            $suite = '';
            if(!isset($suites[$suiteIndex])) $suites[$suiteIndex] = $suite;

            $case = new stdclass();
            $case->product    = $productID;
            $case->title      = $caseResult->title;
            $case->pri        = 3;
            $case->type       = 'feature';
            $case->stage      = 'feature';
            $case->status     = 'normal';
            $case->openedBy   = $this->app->user->account;
            $case->openedDate = $now;
            $case->version    = 1;
            $case->auto       = 'func';
            $case->frame      = $frame;

            $result = new stdclass();
            $result->case       = 0;
            $result->version    = 1;
            $result->caseResult = 'pass';
            $result->lastRunner = $this->app->user->account;
            $result->job        = $jobID;
            $result->compile    = $compileID;
            $result->date       = $now;
            $result->stepResults[0]['result'] = 'pass';
            $result->stepResults[0]['real']   = '';
            if(!empty($caseResult->steps))
            {
                $result->stepResults = array();
                $stepStatus = 'pass';
                foreach($caseResult->steps as $i => $step)
                {
                    $result->stepResults[$i]['result'] = $step->status ? 'pass' : 'fail';
                    $result->stepResults[$i]['real']   = $step->status ? '' : $step->checkPoints[0]->actual;
                    if(!$step->status) $stepStatus = 'fail';
                }
                $result->caseResult = $stepStatus;
            }
            $result->stepResults = serialize($result->stepResults);
            $case->lastRunner    = $this->app->user->account;
            $case->lastRunDate   = $now;
            $case->lastRunResult = $result->caseResult;

            $caseTitles[$suiteIndex][]        = $case->title;
            $cases[$suiteIndex][$caseIndex]   = $case;
            $results[$suiteIndex][$caseIndex] = $result;
        }

        return array('suites' => $suites, 'cases' => $cases, 'results' => $results, 'suiteNames' => $suiteNames, 'caseTitles' => $caseTitles);
    }

    /**
     * Print cell data.
     * 
     * @param object $col
     * @param object $task
     * @param int    $applicationID
     * @param int    $productID
     * @param array  $users
     * @param string $mode
     * @param array  $projects
     * @param array  $products
     * @access public
     * @return void
     */
    public function printMainBrowseCell($col, $task, $applicationID, $productID, $users, $mode = 'datatable', $projects = [], $products = [])
    {
        $account = $this->app->user->account;
        $id      = $col->id;
        
        if($col->show)
        {
            $class = "c-$id ";
            if($id == 'status')
            {
                $class .= $task->status;
            }
            if($id == 'title')
            {
                $class .= ' text-left';
            }
            if($id == 'id')
            {
                $class .= ' cell-id';
            }
            if($id == 'assignedTo' && $task->assignedTo == $account)
            {
                $class .= ' red';
            }
            if($id == 'actions')
            {
                $class .= 'c-actions text-right';
            }

            $product = $task->product;
            if(!$product)
            {
                $product = 'na';
            }
            $productName = zget($products, $product, '');
            $projectName = zget($projects, $task->project, '');

            $buildData = '';
            foreach($task->buildData as $build)
            {
                $buildData .= html::a(helper::createLink('build', 'view', "buildID=$build->id"), $build->name, '', "data-group=project") . '<br>';
            }

            echo "<td class='" . $class . "'" . ($id == 'title' ? "title='{$task->title}'" : '') . ">";
            if(isset($this->config->bizVersion)) $this->loadModel('flow')->printFlowCell('testtask', $task, $id);

            switch ($id)
            {
                case 'id':
                    echo html::a(inlink('cases', "taskID=$task->id"), sprintf('%03d', $task->id));
                    break;
                case 'oddNumber':
                    echo $task->oddNumber;
                    break;
                case 'name':
                    echo html::a(inlink('cases', "taskID=$task->id"), $task->name, '', "title='{$task->name}'");
                    break;
                case 'product':
                    echo $productName;
                    break;
                case 'project':
                    echo $projectName;
                    break;
                case 'build':
                    echo $buildData;
                    break;
                case 'owner':
                    echo zget($users, $task->owner);
                    break;
                case 'begin':
                    echo $task->begin;
                    break;
                case 'end':
                    echo $task->end;
                    break;
                case 'progress':
                    $progress = $this->processProgress($task->id);
                    echo $progress;
                    break;
                case 'status':
                    $status = $this->processStatus('testtask', $task);
                    echo "<span class='status-task status-".$task->status."'>";
                    echo $status;
                    echo "</span>";
                    break;
                case 'actions':
                    echo '<div id="action-divider">';
                    common::printIcon('bug', 'browse', "applicationID=testtask$task->id", $task, 'list', 'bug');
                    echo '</div>';
                    echo '<div id="action-divider">';
                    common::printIcon('testtask', 'cases', "taskID=$task->id", $task, 'list', 'sitemap');
                    common::printIcon('testtask', 'linkCase', "taskID=$task->id&type=all&param=myQueryID", $task, 'list', 'link');
                    common::printIcon('testreport', 'browse', "applicationID=$applicationID&objectID=$task->product&objectType=product&extra=$task->id", $task, 'list', 'flag');
                    echo '</div>';
                    common::printIcon('testtask', 'view', "taskID=$task->id", '', 'list', 'list-alt');
                    common::printIcon('testtask', 'edit', "taskID=$task->id", $task, 'list');
                    if(common::hasPriv('testtask', 'delete', $task))
                    {
                        $deleteURL = helper::createLink('testtask', 'delete', "taskID=$task->id&confirm=yes");
                        echo html::a("javascript:ajaxDelete(\"$deleteURL\",\"taskList\",confirmDelete)", '<i class="icon-common-delete icon-trash"></i>', '', "title='{$this->lang->testtask->delete}' class='btn'");
                    }
                    break;
            }
            echo '</td>';
        }
    }

    /**
     * Replacement sort field.
     *
     * @param  string $orderBy
     * @return string
     */
    public function replaceSortField($orderBy)
    {
        if(strpos($orderBy, 't2.bugs') !== false)       $orderBy = str_replace('t2.bugs', 't1.taskBugs', $orderBy);
        if(strpos($orderBy, 't2.results') !== false)    $orderBy = str_replace('t2.results', 't1.taskResults', $orderBy);
        if(strpos($orderBy, 't2.stepNumber') !== false) $orderBy = str_replace('t2.stepNumber', 't1.taskStepNumber', $orderBy);
        return $orderBy;
    }


    /**
     * Get project testtask.
     *
     * @param  string $projects
     * @return array
     */
    public function getProjectTestTasks($projects)
    {
        if(empty($projects)) return array();

        $paris = $this->dao->select('id,concat(oddNumber, "(", name, ")") as oddNumber')->from(TABLE_TESTTASK)
            ->where('deleted')->eq('0')
            ->andwhere('project')->in($projects)
            ->fetchPairs();
        return $paris;
    }

    /**
     * Get project testtask.
     *
     * @param  string $projects
     * @return array
     */
    public function getProjectTestTasksGroup($projects)
    {
        if(empty($projects)) return array();

        $paris = $this->dao->select('id,oddNumber,project')->from(TABLE_TESTTASK)
            ->where('deleted')->eq('0')
            ->andwhere('project')->in($projects)
            ->fetchGroup('project');
        return $paris;
    }
    
    /**
     * Get all testtask.
     *
     * @return array
     */
    public function getAllPairs()
    {
        $paris = $this->dao->select('id,concat(oddNumber, "(", name, ")") as oddNumber')->from(TABLE_TESTTASK)
            ->where('deleted')->eq('0')
            ->fetchPairs();
        return $paris;
    }

    /**
     * Build search form.
     *
     * @param  int    $queryID
     * @param  string $actionURL
     * @param  int    $applicationID
     * @param  int    $productID
     * @access public
     * @return void
     */
    public function buildSearchForm($queryID, $actionURL, $applicationID = 0, $productID = 0, $projectID = 0)
    {
        $builds   = $this->loadModel('build')->getPairsByJoins($applicationID, $projectID, $productID, 0, 'notrunk');
        $projects = $this->loadModel('rebirth')->getProductLinkProjectPairs($applicationID, $productID);

        $this->config->testtask->search['actionURL']                   = $actionURL;
        $this->config->testtask->search['queryID']                     = $queryID;
        $this->config->testtask->search['params']['project']['values'] = [0 => ''] + $projects;
        $this->config->testtask->search['params']['build']['values']   = [0 => ''] + $builds;

        $this->loadModel('search')->setSearchParams($this->config->testtask->search);
    }
    
    /**
     * process Progress
     *
     * @param  int    $id
     * @access public
     * @return string
     */
    public function processProgress($id)
    {
        $count = $this->dao->select('t2.status as caseStatus,t1.lastRunResult')->from(TABLE_TESTRUN)->alias('t1')
            ->leftJoin(TABLE_CASE)->alias('t2')->on('t1.case = t2.id')
            ->leftJoin(TABLE_STORY)->alias('t3')->on('t2.story = t3.id')
            ->where('t1.task')->eq((int)$id)
            ->andWhere('t2.deleted')->eq(0)
            ->fetchAll();
        
        $noneExec = 0;
        foreach($count as $key => $value) if($value->lastRunResult == 'pass' || $value->lastRunResult == 'fail') $noneExec++;

        return $noneExec > 0 ? round($noneExec / count($count)*100, 2) . '%' : '0.00%';
    }
}
