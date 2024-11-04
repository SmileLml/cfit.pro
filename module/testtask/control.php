<?php
/**
 * The control file of testtask module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     testtask
 * @version     $Id: control.php 5114 2013-07-12 06:02:59Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
class testtask extends control
{
    /**
     * All application.
     *
     * @var    array
     * @access public
     */
    public $applicationList = array();

    /**
     * Project id.
     *
     * @var    int
     * @access public
     */
    public $projectID = 0;

    /**
     * Construct function, load product module, assign products to view auto.
     *
     * @access public
     * @return void
     */
    public function __construct($moduleName = '', $methodName = '')
    {
        parent::__construct($moduleName, $methodName);

        $this->loadModel('rebirth');
        $this->loadModel('qa');
        $this->loadModel('product');

        $objectID        = 0;
        $applicationList = $this->rebirth->getApplicationPairs();

        $this->view->applicationList = $this->applicationList = $applicationList;

        if(empty($applicationList) and !helper::isAjaxRequest()) die($this->locate($this->createLink('application', 'create')));
    }

    /**
     * Index page, header to browse.
     *
     * @access public
     * @return void
     */
    public function index()
    {
        $this->locate($this->createLink('testtask', 'browse'));
    }

    /**
     * Browse test tasks.
     *
     * @param  int    $productID
     * @param  string $type
     * @param  int    $param
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function browse($applicationID, $productID = 'all', $branch = '', $type = 'local,totalStatus',$param = 0 , $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $this->loadModel('datatable');
        /* Save session. */
        $this->session->set('testtaskList', $this->app->getURI(true), $this->app->openApp);
        $this->session->set('caseList', $this->app->getURI(true), $this->app->openApp);
        
        $scopeAndStatus = explode(',', $type);
        $this->session->set('testTaskVersionScope', $scopeAndStatus[0]);
        $this->session->set('testTaskVersionStatus', $scopeAndStatus[1]);

        /* Build the search form. */
        $queryID = 0;
        if($scopeAndStatus[1] == 'bySearch')
        {
            $queryID = (int)$param;
        }

        /* Set menu. */
        $applicationID = $this->rebirth->saveState($this->applicationList, $applicationID, $productID);
        $productID     = $this->rebirth->getProductIdByApplication($applicationID, $productID);

        if($productID != 'all' && $productID == 0) $productID = 'na';
        $this->rebirth->setMenu($applicationID, $productID);
    
        $productIdList = $this->rebirth->getProductIdList($applicationID, $productID);
        $products      = $this->rebirth->getProductPairs($applicationID, true);
        $projects      = $this->rebirth->getProductLinkProjectPairs($applicationID, $productID);

        $actionURL = $this->createLink('testtask', 'browse', "applicationID=$applicationID&productID=$productID&branch=$branch&type=local,bySearch&queryID=myQueryID");
        $this->loadModel('testtask')->buildSearchForm($queryID, $actionURL, $applicationID, $productID);

        /* 获取固定排序字段。 */
        if(isset($this->config->testtask->browse->fixedSort)) $orderBy = $this->config->testtask->browse->fixedSort;

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = pager::init($recTotal, $recPerPage, $pageID);

        /* Append id for secend sort. */
        $sort = $this->loadModel('common')->appendOrder($orderBy);

        /* Get tasks. */
        $tasks = $this->testtask->getProductTasks($applicationID, $productIdList, $sort, $pager, $scopeAndStatus, $queryID);
        
        $this->view->title         = $this->applicationList[$applicationID] . $this->lang->colon . $this->lang->testtask->common;
        $this->view->applicationID = $applicationID;
        $this->view->productID     = $productID;
        $this->view->products      = $products;
        $this->view->projects      = $projects;
        $this->view->orderBy       = $orderBy;
        $this->view->tasks         = $tasks;
        $this->view->users         = $this->loadModel('user')->getPairs('noclosed|noletter');
        $this->view->pager         = $pager;
        $this->view->branch        = $branch;
        $this->view->browseType    = $type;
        $this->view->param         = $param;

        $this->display();
    }

    /**
     * Browse unit tasks.
     *
     * @param  int    $productID
     * @param  string $browseType
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function browseUnits($applicationID, $productID = 0, $browseType = 'newest', $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        /* Save session. */
        $this->session->set('testtaskList', $this->app->getURI(true), $this->app->openApp);
        $this->session->set('caseList', $this->app->getURI(true), $this->app->openApp);
        $this->loadModel('testcase');
        $this->app->loadLang('tree');

        /* Set menu. */
        $applicationID = $this->rebirth->saveState($this->applicationList, $applicationID, $productID);
        $this->rebirth->setMenu($applicationID, $productID);
        $productID     = $this->rebirth->getProductIdByApplication($applicationID, $productID);
        $productIdList = $this->rebirth->getProductIdList($applicationID, $productID);
        $products      = $this->rebirth->getProductPairs($applicationID, true);
        $projects      = $this->rebirth->getProductLinkProjectPairs($applicationID, $productID);

        $this->app->rawModule = 'testcase';

        /* Load pager. */
        if($browseType == 'newest') $recPerPage = '10';
        $this->app->loadClass('pager', $static = true);
        $pager = pager::init($recTotal, $recPerPage, $pageID);

        /* Append id for secend sort. */
        $sort = $this->loadModel('common')->appendOrder($orderBy);

        $tasks = $this->testtask->getProductUnitTasks($applicationID, $productIdList, $browseType, $sort, $pager);
        $buildIdList = array();
        foreach($tasks as $taskID => $task) $buildIdList[] = $task->build;
        $builds = $this->dao->select('id,name')->from(TABLE_BUILD)->where('id')->in($buildIdList)->fetchPairs();

        $this->view->title         = $this->applicationList[$applicationID] . $this->lang->colon . $this->lang->testtask->common;
        $this->view->productID     = $productID;
        $this->view->applicationID = $applicationID;
        $this->view->products      = $products;
        $this->view->projects      = $projects;
        $this->view->builds        = $builds;
        $this->view->orderBy       = $orderBy;
        $this->view->browseType    = $browseType;
        $this->view->tasks         = $tasks;
        $this->view->users         = $this->loadModel('user')->getPairs('noclosed|noletter');
        $this->view->pager         = $pager;
        $this->view->suiteList     = $this->loadModel('testsuite')->getSuites($applicationID, $productIdList);

        $this->display();
    }

    /**
     * Create a test task.
     *
     * @param  int    $productID
     * @param  int    $build
     * @param  int    $projectID
     * @access public
     * @return void
     */
    public function create($applicationID, $productID = 0, $build = 0, $projectID = 0)
    {
        if(!empty($_POST))
        {
            $taskID = $this->testtask->create($projectID);
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));
            $this->loadModel('action')->create('testtask', $taskID, 'opened');

            $this->executeHooks($taskID);
            $task = $this->dao->findById($taskID)->from(TABLE_TESTTASK)->fetch();
            if($this->app->openApp == 'qa') $link = $this->createLink('testtask', 'browse', "applicationID=$applicationID&productID=$productID");
            if($this->app->openApp == 'project') $link = $this->createLink('project', 'testtask', "projectID=$task->project&applicationID=$applicationID&productID=$productID");
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $link));
        }

        /* 当$applicationID为空时，从产品查询一下。*/
        if(empty($applicationID))
        {
            $applicationID = $this->dao->select('app')->from(TABLE_PRODUCT)->where('id')->eq($productID)->fetch('app');
        }

        /* Set menu. */
        $applicationID = $this->rebirth->saveState($this->applicationList, $applicationID, $productID);
        $productID     = $this->rebirth->getProductIdByApplication($applicationID, $productID);
        if($this->app->openApp == 'project')
        {
            $this->loadModel('project')->setMenu($projectID);
        }
        else
        {
            $this->rebirth->setMenu($applicationID, $productID);
        }

        $builds = $this->loadModel('build')->getPairsByJoins($applicationID, $projectID, $productID == 'all' ? 'na' : $productID, 0, 'notrunk', true);

        if($this->app->openApp == 'project')
        {
            $products    = $this->rebirth->getProjectProductPairs($applicationID, $projectID);
            $projectName = $this->dao->select('name')->from(TABLE_PROJECT)->where('id')->eq($projectID)->fetch('name');
            $projects    = array($projectID => $projectName);
        }
        else
        {
            $products = $this->rebirth->getProductPairs($applicationID, true);
            $projects = array(0 => '') + $this->rebirth->getProductLinkProjectPairs($applicationID, $productID);
        }

        $this->view->problems     = array('0' => '') + $this->loadModel('problem')->getPairsAbstract('noclosed');
        $this->view->requirements = array('0' => '') + $this->loadModel('demand')->getPairsTitle();

        $this->view->title         = $this->applicationList[$applicationID] . $this->lang->colon . $this->lang->testtask->create;
        $this->view->applicationID = $applicationID;
        $this->view->products      = $products;
        $this->view->projects      = $projects;
        $this->view->productID     = $productID;
        $this->view->projectID     = $projectID;
        $this->view->builds        = $builds;
        $this->view->build         = $build;
        $this->view->users         = $this->loadModel('user')->getPairs('noclosed|qdfirst|nodeleted');

        $this->display();
    }

    /**
     * View a test task.
     *
     * @param  int    $taskID
     * @access public
     * @return void
     */
    public function view($taskID)
    {
        /* Get test task, and set menu. */
        $taskID = (int)$taskID;
        $task   = $this->testtask->getById($taskID, true);
        if(!$task) die(js::error($this->lang->notFound) . js::locate('back'));

        if($task->product == 0) $task->product = 'na';
        $applicationID = $task->applicationID;
        $productID     = $task->product;

        $applicationID = $this->rebirth->saveState($this->applicationList, $applicationID, $productID);
        $productID     = $this->rebirth->getProductIdByApplication($applicationID, $productID);
        $products      = $this->rebirth->getProductPairs($applicationID, true);

        if($this->app->openApp == 'project')
        {
            $this->loadModel('project')->setMenu($this->session->project);
        }
        else
        {
            $this->rebirth->setMenu($applicationID, $productID);
        }

        $buildID = $task->build;
        $build   = $this->loadModel('build')->getByID($buildID);
        $stories = array();
        $bugs    = array();

        if($build)
        {
            $stories = $this->dao->select('*')->from(TABLE_STORY)->where('id')->in($build->stories)->fetchAll();
            $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'story');

            $bugs    = $this->dao->select('*')->from(TABLE_BUG)->where('id')->in($build->bugs)->fetchAll();
            $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'bug');
        }

        // 获取关联的问题单和需求条目信息。
        if($task->problem)     $this->view->problems = $this->loadModel('problem')->getByIdList($task->problem, true);
//        if($task->requirement) $this->view->demands  = $this->loadModel('demand')->getByIdList($task->requirement, true);
        if($task->requirement) $this->view->demands  = $this->loadModel('demand')->getByIdListNew($task->requirement);

        $this->executeHooks($taskID);

        $this->view->title     = "TASK #$task->id $task->name/" . $this->applicationList[$applicationID];
        $this->view->products  = $products;
        $this->view->task      = $task;
        $this->view->users     = $this->loadModel('user')->getPairs('noclosed|noletter');
        $this->view->actions   = $this->loadModel('action')->getList('testtask', $taskID);
        $this->view->build     = $build;
        $this->view->stories   = $stories;
        $this->view->bugs      = $bugs;
        $this->display();
    }

    /**
     * Browse cases of a test task.
     *
     * @param  int    $taskID
     * @param  string $browseType  bymodule|all|assignedtome
     * @param  string $orderBy
     * @param  int    $param
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function cases($taskID, $browseType = 'all', $param = 0, $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        /* Load modules. */
        $this->loadModel('datatable');
        $this->loadModel('testcase');
        $this->loadModel('execution');

        /* Save the session. */
        $this->session->set('caseList', $this->app->getURI(true) . '#app=' . $this->app->openApp, $this->app->openApp);
        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = pager::init($recTotal, $recPerPage, $pageID);

        /* 获取固定排序字段。 */
        if(isset($this->config->testtask->cases->fixedSort)) $orderBy = $this->config->testtask->cases->fixedSort;

        /* Set the browseType and moduleID. */
        $browseType = strtolower($browseType);

        /* Get task and product info, set menu. */
        $task = $this->testtask->getById($taskID);
        if(!$task) die(js::error($this->lang->testtask->checkLinked) . js::locate('back'));
        $task->branch = 0;

        if($task->product == 0) $task->product = 'na';
        $applicationID = $task->applicationID;
        $productID     = $task->product;

        $applicationID = $this->rebirth->saveState($this->applicationList, $applicationID, $productID);
        $productID     = $this->rebirth->getProductIdByApplication($applicationID, $productID);
        $products      = $this->rebirth->getProductPairs($applicationID, true);
        $projects      = $this->rebirth->getProductLinkProjectPairs($applicationID, $productID);

        if($this->app->openApp == 'project')
        {
            $this->loadModel('project')->setMenu($task->project);
        }
        else
        {
            $this->rebirth->setMenu($applicationID, $productID);
        }

        setcookie('preTaskID', $taskID, $this->config->cookieLife, $this->config->webRoot, '', $this->config->cookieSecure, true);

        /* Determines whether an object is editable. */
        $canBeChanged = common::canBeChanged('testtask', $task);

        if($this->cookie->preTaskID != $taskID)
        {
            $_COOKIE['taskCaseModule'] = 0;
            setcookie('taskCaseModule', 0, 0, $this->config->webRoot, '', $this->config->cookieSecure, true);
        }

        if($browseType == 'bymodule') setcookie('taskCaseModule', (int)$param, 0, $this->config->webRoot, '', $this->config->cookieSecure, true);
        if($browseType != 'bymodule') $this->session->set('taskCaseBrowseType', $browseType);

        /* Set the browseType, moduleID and queryID. */
        $moduleID = ($browseType == 'bymodule') ? (int)$param : 0;
        $queryID  = ($browseType == 'bysearch') ? (int)$param : 0;

        $assignedToList = $this->loadModel('user')->getPairs('noclosed|noletter|nodeleted|qafirst');

        /* Append id for secend sort. */
        $sort = $this->loadModel('common')->appendOrder($orderBy, 't2.id');

        /* Get test cases. */
        $runs = $this->testtask->getTaskCases($applicationID, $productID, $browseType, $queryID, $moduleID, $sort, $pager, $task);
        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'testcase', false);

        $modules    = array();
        $moduleTree = '';
        $this->loadModel('tree');
        if(is_numeric($productID))
        {
            $modules    = $this->tree->getOptionMenu($productID, $viewType = 'case');
            $moduleTree = $this->tree->getTreeMenu($productID, $viewType = 'case', $startModuleID = 0, array('treeModel', 'createTestTaskLink'), $extra = $taskID);
        }

        /* Build the search form. */
        $this->loadModel('testcase');
        $this->config->testcase->search['module']                      = 'testtask';
        $this->config->testcase->search['params']['product']['values'] = array($productID => $products[$productID], 'all' => $this->lang->testcase->allProduct);
        $this->config->testcase->search['params']['module']['values']  = $modules;
        $this->config->testcase->search['params']['status']['values']  = array('' => '') + $this->lang->testtask->statusList;
        $this->config->testcase->search['params']['lib']['values']     = $this->loadModel('caselib')->getLibraries();

        $this->config->testcase->search['queryID']              = $queryID;
        $this->config->testcase->search['fields']['assignedTo'] = $this->lang->testtask->assignedTo;
        $this->config->testcase->search['params']['assignedTo'] = array('operator' => '=', 'control' => 'select', 'values' => 'users');
        $this->config->testcase->search['actionURL'] = inlink('cases', "taskID=$taskID&browseType=bySearch&queryID=myQueryID");
        if(!$this->config->testcase->needReview) unset($this->config->testcase->search['params']['status']['values']['wait']);
        unset($this->config->testcase->search['fields']['branch']);
        unset($this->config->testcase->search['params']['branch']);

        if($this->app->openApp == 'project')
        {
            unset($this->config->testcase->search['fields']['project']);
            unset($this->config->testcase->search['params']['project']);
        }

        $this->loadModel('search')->setSearchParams($this->config->testcase->search);

        /* Append bugs and results. */
        $runs = $this->testcase->appendData($runs, 'run');

        $this->view->title          = $this->applicationList[$applicationID] . $this->lang->colon . $this->lang->testtask->cases;
        $this->view->applicationID  = $applicationID;
        $this->view->productID      = $productID;
        $this->view->products       = $products;
        $this->view->projects       = $projects;
        $this->view->task           = $task;
        $this->view->runs           = $runs;
        $this->view->users          = $this->loadModel('user')->getPairs('noclosed|qafirst|noletter');
        $this->view->assignedToList = $assignedToList;
        $this->view->moduleTree     = $moduleTree;
        $this->view->browseType     = $browseType;
        $this->view->param          = $param;
        $this->view->orderBy        = $orderBy;
        $this->view->taskID         = $taskID;
        $this->view->moduleID       = $moduleID;
        $this->view->moduleName     = $moduleID ? $this->tree->getById($moduleID)->name : $this->lang->tree->all;
        $this->view->treeClass      = $browseType == 'bymodule' ? '' : 'hidden';
        $this->view->pager          = $pager;
        $this->view->branches       = array();
        $this->view->setModule      = false;
        $this->view->canBeChanged   = $canBeChanged;

        $this->display();
    }

    /**
     * The report page.
     *
     * @param  int    $productID
     * @param  string $browseType
     * @param  int    $branchID
     * @param  int    $moduleID
     * @access public
     * @return void
     */
    public function report($applicationID, $productID, $taskID, $browseType, $branchID = 0, $moduleID = 0, $chartType = 'pie')
    {
        $this->loadModel('report');
        $this->view->charts = array();

        $task = $this->testtask->getById($taskID);

        if(!empty($_POST))
        {
            $this->app->loadLang('testcase');
            $bugInfo = $this->testtask->getBugInfo($taskID, $applicationID, $productID);
            foreach($this->post->charts as $chart)
            {
                $chartFunc   = 'getDataOf' . $chart;
                $chartData   = isset($bugInfo[$chart]) ? $bugInfo[$chart] : $this->testtask->$chartFunc($taskID);
                $chartOption = $this->testtask->mergeChartOption($chart);
                if(!empty($chartType)) $chartOption->type = $chartType;

                $this->view->charts[$chart] = $chartOption;
                $this->view->datas[$chart]  = $this->report->computePercent($chartData);
            }
        }

        if($task->product == 0) $task->product = 'na';
        $applicationID = $task->applicationID;
        $productID     = $task->product;

        $applicationID = $this->rebirth->saveState($this->applicationList, $applicationID, $productID);
        $productID     = $this->rebirth->getProductIdByApplication($applicationID, $productID);

        if($this->app->openApp == 'project')
        {
            $this->loadModel('project')->setMenu($this->session->project);
        }
        else
        {
            $this->rebirth->setMenu($applicationID, $productID);
        }

        $this->view->title         = $this->applicationList[$applicationID] . $this->lang->colon . $this->lang->testtask->common . $this->lang->colon . $this->lang->testtask->reportChart;
        $this->view->applicationID = $applicationID;
        $this->view->productID     = $productID;
        $this->view->taskID        = $taskID;
        $this->view->browseType    = $browseType;
        $this->view->moduleID      = $moduleID;
        $this->view->branchID      = $branchID;
        $this->view->chartType     = $chartType;
        $this->view->checkedCharts = $this->post->charts ? join(',', $this->post->charts) : '';

        $this->display();
    }

    /**
     * Group case.
     *
     * @param  int    $taskID
     * @param  string $groupBy
     * @access public
     * @return void
     */
    public function groupCase($taskID, $groupBy = 'story')
    {
        /* Save the session. */
        $this->loadModel('testcase');
        $this->app->loadLang('execution');
        $this->app->loadLang('task');
        $this->session->set('caseList', $this->app->getURI(true), 'qa');

        /* Get task and product info, set menu. */
        $groupBy = empty($groupBy) ? 'story' : $groupBy;
        $task    = $this->testtask->getById($taskID);
        if(!$task) die(js::error($this->lang->notFound) . js::locate('back'));

        if($task->product == 0) $task->product = 'na';
        $applicationID = $task->applicationID;
        $productID     = $task->product;

        $applicationID = $this->rebirth->saveState($this->applicationList, $applicationID, $productID);
        $productID     = $this->rebirth->getProductIdByApplication($applicationID, $productID);

        if($this->app->openApp == 'project')
        {
            $this->loadModel('project')->setMenu($this->session->project);
        }
        else
        {
            $this->rebirth->setMenu($applicationID, $productID);
        }

        /* Determines whether an object is editable. */
        $canBeChanged = common::canBeChanged('testtask', $task);

        $runs = $this->testtask->getRuns($taskID, 0, $groupBy);
        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'testcase', false);
        $runs = $this->testcase->appendData($runs, 'run');
        $groupCases  = array();
        $groupByList = array();
        foreach($runs as $run)
        {
            if($groupBy == 'story')
            {
                $groupCases[$run->story][] = $run;
                $groupByList[$run->story]  = $run->storyTitle;
            }
            elseif($groupBy == 'assignedTo')
            {
                $groupCases[$run->assignedTo][] = $run;
            }
        }

        if($groupBy == 'story' && $task->build)
        {
            $buildStoryIdList = $this->dao->select('stories')->from(TABLE_BUILD)->where('id')->eq($task->build)->fetch('stories');
            $buildStories     = $this->dao->select('id,title')->from(TABLE_STORY)->where('id')->in($buildStoryIdList)->andWhere('id')->notin(array_keys($groupCases))->fetchAll('id');
            foreach($buildStories as $buildStory)
            {
                $groupCases[$buildStory->id][] = $buildStory;
                $groupByList[$buildStory->id]  = $buildStory->title;
            }
        }

        $this->view->title         = $this->applicationList[$applicationID] . $this->lang->colon . $this->lang->testtask->cases;
        $this->view->users         = $this->loadModel('user')->getPairs('noletter');
        $this->view->applicationID = $applicationID;
        $this->view->productID     = $productID;
        $this->view->task          = $task;
        $this->view->taskID        = $taskID;
        $this->view->browseType    = 'group';
        $this->view->groupBy       = $groupBy;
        $this->view->groupByList   = $groupByList;
        $this->view->cases         = $groupCases;
        $this->view->account       = 'all';
        $this->view->canBeChanged  = $canBeChanged;
        $this->display();
    }

    /**
     * Edit a test task.
     *
     * @param  int    $taskID
     * @access public
     * @return void
     */
    public function edit($taskID)
    {
        if(!empty($_POST))
        {
            $changes = $this->testtask->update($taskID);
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));
            if($changes or $this->post->comment)
            {
                $actionID = $this->loadModel('action')->create('testtask', $taskID, 'edited', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }

            $this->executeHooks($taskID);

            $link = isonlybody() ? 'parent' : $this->session->testtaskList;
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $link));
        }

        $task = $this->testtask->getById($taskID);
        if($task->product == 0) $task->product = 'na';
        $applicationID = $task->applicationID;
        $productID     = $task->product;

        $applicationID = $this->rebirth->saveState($this->applicationList, $applicationID, $productID);
        $productID     = $this->rebirth->getProductIdByApplication($applicationID, $productID);
        $projects      = $this->rebirth->getProductLinkProjectPairs($applicationID, $productID);

        if($this->app->openApp == 'project')
        {
            $this->loadModel('project')->setMenu($task->project);
            $products      = $this->rebirth->getProjectProductPairs($applicationID, $task->project);
            $assignProduct = $this->loadModel('product')->getByIdPairs(array($productID));
            $products      = $products + $assignProduct;
        }
        else
        {
            $this->rebirth->setMenu($applicationID, $productID);
            $products = $this->rebirth->getProductPairs($applicationID, true);
        }

        $builds = $this->loadModel('build')->getPairsByJoins($applicationID, '', $productID, 0, 'notrunk', true);

        $this->view->title        = $this->applicationList[$applicationID] . $this->lang->colon . $this->lang->testtask->edit;
        $this->view->products     = $products;
        $this->view->projects     = $projects;
        $this->view->task         = $task;
        $this->view->builds       = $builds;
        $this->view->users        = $this->loadModel('user')->getPairs('nodeleted', $task->owner);
        $this->view->contactLists = $this->user->getContactLists($this->app->user->account, 'withnote');
        $this->view->problems     = array('0' => '') + $this->loadModel('problem')->getPairsAbstract('noclosed','',array_filter(explode(',',$task->problem)));
        $this->view->requirements = array('0' => '') + $this->loadModel('demand')->getPairsTitle();

        $this->display();
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
        if(!empty($_POST))
        {
            $changes = $this->testtask->start($taskID);
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

            if($this->post->comment != '' or !empty($changes))
            {
                $actionID = $this->loadModel('action')->create('testtask', $taskID, 'Started', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }

            $this->executeHooks($taskID);

            if(isonlybody()) $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'parent'));
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $this->createLink('testtask', 'view', "taskID=$taskID")));
        }

        /* Get task info. */
        $task = $this->testtask->getById($taskID);
        if($task->product == 0) $task->product = 'na';
        $applicationID = $task->applicationID;
        $productID     = $task->product;

        $applicationID = $this->rebirth->saveState($this->applicationList, $applicationID, $productID);
        $this->rebirth->setMenu($applicationID, $productID);
        $productID     = $this->rebirth->getProductIdByApplication($applicationID, $productID);

        $this->view->testtask = $task;
        $this->view->title    = $task->name . $this->lang->colon . $this->lang->testtask->start;
        $this->view->users    = $this->loadModel('user')->getPairs('nodeleted', $task->owner);
        $this->view->actions  = $this->loadModel('action')->getList('testtask', $taskID);
        $this->display();
    }

    /**
     * activate testtask.
     *
     * @param  int    $taskID
     * @access public
     * @return void
     */
    public function activate($taskID)
    {
        if(!empty($_POST))
        {
            $changes = $this->testtask->activate($taskID);
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

            if($this->post->comment != '' or !empty($changes))
            {
                $actionID = $this->loadModel('action')->create('testtask', $taskID, 'Activated', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }

            $this->executeHooks($taskID);

            if(isonlybody()) $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'parent'));
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $this->createLink('testtask', 'view', "taskID=$taskID")));
        }

        /* Get task info. */
        $task  = $this->testtask->getById($taskID);
        if($task->product == 0) $task->product = 'na';
        $applicationID = $task->applicationID;
        $productID     = $task->product;

        $applicationID = $this->rebirth->saveState($this->applicationList, $applicationID, $productID);
        $this->rebirth->setMenu($applicationID, $productID);
        $productID     = $this->rebirth->getProductIdByApplication($applicationID, $productID);

        $this->view->testtask   = $task;
        $this->view->title      = $task->name . $this->lang->colon . $this->lang->testtask->start;
        $this->view->users      = $this->loadModel('user')->getPairs('nodeleted', $task->owner);
        $this->view->actions    = $this->loadModel('action')->getList('testtask', $taskID);
        $this->display();
    }

    /**
     * Close testtask.
     *
     * @param  int    $taskID
     * @access public
     * @return void
     */
    public function close($taskID)
    {
        if(!empty($_POST))
        {
            $changes = $this->testtask->close($taskID);
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

            if($this->post->comment != '' or !empty($changes))
            {
                $actionID = $this->loadModel('action')->create('testtask', $taskID, 'Closed', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }

            $this->executeHooks($taskID);

            if(isonlybody()) $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'parent'));
            $this->send(array('result' => 'success', 'message' => $this->lang->success, 'locate' => $this->createLink('testtask', 'view', "taskID=$taskID")));
        }

        /* Get task info. */
        $task  = $this->testtask->getById($taskID);
        if($task->product == 0) $task->product = 'na';
        $applicationID = $task->applicationID;
        $productID     = $task->product;

        $applicationID = $this->rebirth->saveState($this->applicationList, $applicationID, $productID);
        $this->rebirth->setMenu($applicationID, $productID);
        $productID     = $this->rebirth->getProductIdByApplication($applicationID, $productID);

        $this->view->testtask     = $task;
        $this->view->title        = $task->name . $this->lang->colon . $this->lang->close;
        $this->view->actions      = $this->loadModel('action')->getList('testtask', $taskID);
        $this->view->users        = $this->loadModel('user')->getPairs('noclosed|nodeleted|qdfirst');
        $this->view->contactLists = $this->user->getContactLists($this->app->user->account, 'withnote');
        $this->display();
    }

    /**
     * Delete a test task.
     *
     * @param  int    $taskID
     * @param  string $confirm yes|no
     * @access public
     * @return void
     */
    public function delete($taskID, $confirm = 'no')
    {
        if($confirm == 'no')
        {
            die(js::confirm($this->lang->testtask->confirmDelete, inlink('delete', "taskID=$taskID&confirm=yes")));
        }
        else
        {
            $task = $this->testtask->getByID($taskID);
            $this->testtask->delete(TABLE_TESTTASK, $taskID);

            $this->executeHooks($taskID);

            /* if ajax request, send result. */
            if($this->server->ajax)
            {
                if(dao::isError())
                {
                    $response['result']  = 'fail';
                    $response['message'] = dao::getError();
                }
                else
                {
                    $response['result']  = 'success';
                    $response['message'] = '';
                }
                $this->send($response);
            }

            $browseList = $this->createLink('testtask', 'browse', "applicationID=$task->applicationID&productID=$task->product");
            if($this->app->openApp == 'project') $browseList = $this->createLink('project', 'testtask', "projectID=$task->project");
            die(js::locate($browseList, 'parent'));
        }
    }

    /**
     * block testtask.
     *
     * @param  int    $taskID
     * @access public
     * @return void
     */
    public function block($taskID)
    {
        if(!empty($_POST))
        {
            $changes = $this->testtask->block($taskID);
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

            if($this->post->comment != '' or !empty($changes))
            {
                $actionID = $this->loadModel('action')->create('testtask', $taskID, 'Blocked', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }

            $this->executeHooks($taskID);

            if(isonlybody()) $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'parent'));
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $this->createLink('testtask', 'view', "taskID=$taskID")));
        }

        /* Get task info. */
        $task = $this->testtask->getById($taskID);
        if($task->product == 0) $task->product = 'na';
        $applicationID = $task->applicationID;
        $productID     = $task->product;

        $applicationID = $this->rebirth->saveState($this->applicationList, $applicationID, $productID);
        $this->rebirth->setMenu($applicationID, $productID);
        $productID     = $this->rebirth->getProductIdByApplication($applicationID, $productID);

        $this->view->testtask = $task;
        $this->view->title    = $task->name . $this->lang->colon . $this->lang->testtask->start;
        $this->view->users    = $this->loadModel('user')->getPairs('nodeleted', $task->owner);
        $this->view->actions  = $this->loadModel('action')->getList('testtask', $taskID);
        $this->display();
    }

    /**
     * Link cases to a test task.
     *
     * @param  int    $taskID
     * @access public
     * @return void
     */
    public function linkCase($taskID, $type = 'all', $param = 0, $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        if(!empty($_POST))
        {
            $this->testtask->linkCase($taskID, $type);
            $this->locate(inlink('cases', "taskID=$taskID"));
        }
        /* Save session. */
        $this->session->set('caseList', $this->app->getURI(true), 'qa');

        /* Get task and product id. */
        $task          = $this->testtask->getById($taskID);
        $applicationID = $task->applicationID;
        $productID     = $task->product == 0 ? 'na' : $task->product;

        $applicationID = $this->rebirth->saveState($this->applicationList, $applicationID, $productID);
        $productID     = $this->rebirth->getProductIdByApplication($applicationID, $productID);

        if($this->app->openApp == 'project')
        {
            $this->loadModel('project')->setMenu($task->project);
        }
        else
        {
            $this->rebirth->setMenu($applicationID, $productID);
        }

        $modules = array();
        if(is_numeric($productID)) $modules = $this->loadModel('tree')->getOptionMenu($productID, $viewType = 'case');

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = pager::init($recTotal, $recPerPage, $pageID);

        /* Build the search form. */
        $this->loadModel('testcase');
        $this->config->testcase->search['module']                      = 'testtask_link_testcase';
        $this->config->testcase->search['params']['module']['values']  = $modules;
        $this->config->testcase->search['actionURL'] = inlink('linkcase', "taskID=$taskID&type=$type&param=$param");

        if($this->app->openApp == 'project')
        {
            unset($this->config->testcase->search['fields']['project']);
            unset($this->config->testcase->search['params']['project']);
            $products = $this->loadModel('rebirth')->getProjectProductPairsByProjectID($this->session->project);
        }
        else
        {
            $projects = $this->rebirth->getProductLinkProjectPairs($applicationID, $productID);
            $products = $this->rebirth->getProductPairs($applicationID, false);

            $this->config->testcase->search['params']['project']['values'] = ['' => ''] + $projects;
        }

        $this->config->testcase->search['params']['product']['values'] = array('' => '') + $products;

        if(!$this->config->testcase->needReview) unset($this->config->testcase->search['params']['status']['values']['wait']);
        $this->loadModel('search')->setSearchParams($this->config->testcase->search);

        $testTask = $this->testtask->getRelatedTestTasks($applicationID, $task->product, $taskID);
        /* Get cases. */
        $cases = $this->testtask->getLinkableCases($applicationID, $task->product, $task, $taskID, $type, $param, $pager);

        $this->view->title     = $task->name . $this->lang->colon . $this->lang->testtask->linkCase;
        $this->view->users     = $this->loadModel('user')->getPairs('noletter');
        $this->view->products  = $products;
        $this->view->cases     = $cases;
        $this->view->taskID    = $taskID;
        $this->view->testTask  = $testTask;
        $this->view->pager     = $pager;
        $this->view->task      = $task;
        $this->view->type      = $type;
        $this->view->param     = $param;
        $this->view->suiteList = $this->loadModel('testsuite')->getSuites($applicationID, $task->product);

        $this->display();
    }

    /**
     * Link cases to a test task.
     *
     * @param int $taskID
     * @access public
     * @return void
     */
    public function linkBug($taskID, $type = 'all', $param = 0, $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        if(!empty($_POST))
        {
            $this->testtask->linkBug($taskID);
            $this->locate(helper::createLink('bug', 'browse',"applicationID=testtask{$taskID}"));
        }

        /* Save session. */
        $this->session->set('bugList', $this->app->getURI(true), 'qa');

        /* Get task and product id. */
        $task          = $this->testtask->getById($taskID);
        $applicationID = $task->applicationID;
        $productID     = $task->product == 0 ? 'na' : $task->product;

        $applicationID = $this->rebirth->saveState($this->applicationList, $applicationID, $productID);
        $productID     = $this->rebirth->getProductIdByApplication($applicationID, $productID);

        if($this->app->openApp == 'project')
        {
            $this->loadModel('project')->setMenu($task->project);
        }
        else
        {
            $this->rebirth->setMenu($applicationID, $productID);
        }

        $modules = [];
        if(is_numeric($productID))
        {
            $modules = $this->loadModel('tree')->getOptionMenu($productID, $viewType = 'case');
        }

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = pager::init($recTotal, $recPerPage, $pageID);

        /* Build the search form. */
        $this->loadModel('bug');
        $this->config->bug->search['module']                      = 'testtask_link_bug';      
        $this->config->bug->search['params']['module']['values']  = $modules;
        $this->config->bug->search['actionURL']                   = inlink('linkbug', "taskID=$taskID");

        if($this->app->openApp == 'project')
        {
            unset($this->config->bug->search['fields']['project']);
            unset($this->config->bug->search['params']['project']);
            $products = $this->loadModel('rebirth')->getProjectProductPairsByProjectID($this->session->project);
        }
        else
        {
            $projects = $this->rebirth->getProductLinkProjectPairs($applicationID, $productID);
            $products = $this->rebirth->getProductPairs($applicationID, false);

            $this->config->bug->search['params']['project']['values'] = ['' => ''] + $projects;
        }

        $this->config->bug->search['params']['product']['values'] = ['' => ''] + $products;

        $this->loadModel('search')->setSearchParams($this->config->bug->search);

        $testTask = $this->testtask->getRelatedTestTasks($applicationID, $task->product, $taskID);

        /* Get bugs. */
        $bugs = $this->testtask->getLinkableBugs($applicationID, $task->product, $task, $pager);

        $this->view->title     = $task->name . $this->lang->colon . $this->lang->testtask->linkBug;
        $this->view->users     = $this->loadModel('user')->getPairs('noletter');
        $this->view->products  = $products;
        $this->view->bugs      = $bugs;
        $this->view->taskID    = $taskID;
        $this->view->testTask  = $testTask;
        $this->view->pager     = $pager;
        $this->view->task      = $task;
        $this->view->type      = $type;
        $this->view->param     = $param;
        $this->view->suiteList = $this->loadModel('testsuite')->getSuites($applicationID, $task->product);

        $this->display();
    }

    /**
     * Remove a case from test task.
     *
     * @param  int    $rowID
     * @access public
     * @return void
     */
    public function unlinkCase($rowID, $confirm = 'no')
    {
        if($confirm == 'no')
        {
            die(js::confirm($this->lang->testtask->confirmUnlinkCase, $this->createLink('testtask', 'unlinkCase', "rowID=$rowID&confirm=yes")));
        }
        else
        {
            $response['result']  = 'success';
            $response['message'] = '';

            $testrun = $this->dao->select('task,`case`')->from(TABLE_TESTRUN)->where('id')->eq((int)$rowID)->fetch();
            $task    = $this->dao->select('oddNumber')->from(TABLE_TESTTASK)->where('id')->eq($testrun->task)->fetch();

            $this->loadModel('action');
            $this->action->create('case', $testrun->case, 'unlinkedfromtesttask', '', $task->oddNumber);

            $this->dao->delete()->from(TABLE_TESTRUN)->where('id')->eq((int)$rowID)->exec();
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
            }
            $this->send($response);
        }
    }

    /**
     * Remove a case from test task.
     *
     * @param int $taskID
     * @access public
     * @return void
     */
    public function unlinkBug($taskID, $confirm = 'no',$bugIDList = '')
    {
        if($confirm == 'no')
        {
            die(js::confirm(
                $this->lang->testtask->confirmUnlinkBug, 
                $this->createLink('testtask', 'unlinkBug', "taskID=$taskID&confirm=yes&bugIDList=".implode(',',$this->post->bugIDList)),
                $this->createLink('bug','browse',"applicationID=testtask$taskID")
            ));
        }
        else
        {
            $response['result']  = 'success';
            $response['message'] = '';

            $bugs = $this->dao->select('*')->from(TABLE_BUG)->where('id')->in($bugIDList)->fetchAll('id');

            foreach ($bugs as $bug) {
                $originalLinkTesttask = $bug->linkTesttask;

                $originalLinkTesttaskList = explode(',', $originalLinkTesttask);

                if(in_array($taskID, $originalLinkTesttaskList))
                {
                    unset($originalLinkTesttaskList[array_search($taskID, $originalLinkTesttaskList)]);
                }

                $newLinkTesttask = implode(',', $originalLinkTesttaskList);
                $this->dao->update(TABLE_BUG)->set('linkTesttask')->eq($newLinkTesttask)->where('id')->eq($bug->id)->exec();
            }

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
            }
            $response['locate'] =  $this->createLink('bug', 'browse',"applicationID=testtask{$taskID}");
            $this->send($response);
        }
    }

    /**
     * Batch unlink cases.
     *
     * @param  int    $taskID
     * @access public
     * @return void
     */
    public function batchUnlinkCases($taskID)
    {
        if(isset($_POST['caseIDList']))
        {
            $testruns = $this->dao->select('task,`case`')->from(TABLE_TESTRUN)
            ->where('task')->eq((int)$taskID)
            ->andWhere('`case`')->in($this->post->caseIDList)
            ->fetchAll();

            $testtasks = $this->dao->select('id,oddNumber')->from(TABLE_TESTTASK)
            ->where('id')->eq((int)$taskID)
            ->fetchAll('id');

            foreach ($testruns as $testrun)
            {
                $this->loadModel('action');
                $this->action->create('case', $testrun->case, 'unlinkedfromtesttask', '', $testtasks[$testrun->task]->oddNumber);
            }

            $this->dao->delete()->from(TABLE_TESTRUN)
                ->where('task')->eq((int)$taskID)
                ->andWhere('`case`')->in($this->post->caseIDList)
                ->exec();
        }

        die(js::locate($this->createLink('testtask', 'cases', "taskID=$taskID")));
    }

    /**
     * Run case.
     *
     * @param  int    $runID
     * @param  String $extras   others params, forexample, caseID=10, version=3
     * @access public
     * @return void
     */
    public function runCase($runID, $caseID = 0, $version = 0)
    {
        if($runID)
        {
            $run = $this->testtask->getRunById($runID);
        }
        else
        {
            $run = new stdclass();
            $run->case = $this->loadModel('testcase')->getById($caseID, $version);
        }

        $caseID     = $caseID ? $caseID : $run->case->id;
        $preAndNext = $this->loadModel('common')->getPreAndNextObject('testcase', $caseID);
        if(!empty($_POST))
        {
            $caseResult = $this->testtask->createResult($runID);
            if(dao::isError()) die(js::error(dao::getError()));

            $taskID = empty($run->task) ? 0 : $run->task;
            $this->loadModel('action')->create('case', $caseID, 'run', '', $taskID);
            $this->testtask->updateTaskStatus($caseID);
            if($caseResult == 'fail')
            {

                $response['result'] = 'success';
                $response['locate'] = $this->createLink('testtask', 'results',"runID=$runID&caseID=$caseID&version=$version");
                die($this->send($response));
            }
            else
            {
                /* set cookie for ajax load caselist when close colorbox. */
                setcookie('selfClose', 1, 0, $this->config->webRoot, '', $this->config->cookieSecure, false);

                if($preAndNext->next)
                {
                    $nextRunID   = $runID ? $preAndNext->next->id : 0;
                    $nextCaseID  = $runID ? $preAndNext->next->case : $preAndNext->next->id;
                    $nextVersion = $preAndNext->next->version;

                    $response['result'] = 'success';
                    $response['next']   = 'success';
                    $response['locate'] = inlink('runCase', "runID=$nextRunID&caseID=$nextCaseID&version=$nextVersion");
                    die($this->send($response));
                }
                else
                {
                    $response['result'] = 'success';
                    $response['locate'] = 'reload';
                    $response['target'] = 'parent';
                    die($this->send($response));
                }
            }
        }

        $preCase  = array();
        $nextCase = array();
        if($preAndNext->pre)
        {
            $preCase['runID']   = $runID ? $preAndNext->pre->id : 0;
            $preCase['caseID']  = $runID ? $preAndNext->pre->case : $preAndNext->pre->id;
            $preCase['version'] = $preAndNext->pre->version;
        }
        if($preAndNext->next)
        {
            $nextCase['runID']   = $runID ? $preAndNext->next->id : 0;
            $nextCase['caseID']  = $runID ? $preAndNext->next->case : $preAndNext->next->id;
            $nextCase['version'] = $preAndNext->next->version;
        }

        $this->view->run      = $run;
        $this->view->preCase  = $preCase;
        $this->view->nextCase = $nextCase;
        $this->view->users    = $this->loadModel('user')->getPairs('noclosed, noletter');
        $this->view->caseID   = $caseID;
        $this->view->version  = $version;
        $this->view->runID    = $runID;

        $this->display();
    }

    /**
     * Batch run case.
     *
     * @param  int    $productID
     * @param  string $orderBy
     * @param  string $from
     * @access public
     * @return void
     */
    public function batchRun($applicationID, $productID = 0, $orderBy = 'id_desc', $from = 'testcase', $taskID = 0)
    {
        $url = $this->session->caseList ? $this->session->caseList : $this->createLink('testcase', 'browse', "applicationID=$applicationID&productID=$productID");
        if($this->post->results)
        {
            $this->testtask->batchRun($from, $taskID);
            die(js::locate($url, 'parent'));
        }

        $applicationID = $this->rebirth->saveState($this->applicationList, $applicationID, $productID);
        if($this->app->openApp == 'project')
        {
            $this->loadModel('project')->setMenu($this->session->project);
        }
        else
        {
            $this->rebirth->setMenu($applicationID, $productID);
        }

        $productID     = $this->rebirth->getProductIdByApplication($applicationID, $productID);
        $products      = $this->rebirth->getProductPairs($applicationID, true);

        $caseIDList = $this->post->caseIDList ? $this->post->caseIDList : die(js::locate($url, 'parent'));
        $caseIDList = array_unique($caseIDList);

        $cases = $this->dao->select('*')->from(TABLE_CASE)->where('id')->in($caseIDList)->fetchAll('id');

        /* If case has changed and not confirmed, remove it. */
        if($from == 'testtask')
        {
            $runs = $this->dao->select('`case`, version')->from(TABLE_TESTRUN)
                ->where('`case`')->in($caseIDList)
                ->andWhere('task')->eq($taskID)
                ->fetchPairs();
            foreach($cases as $caseID => $case)
            {
                if(isset($runs[$caseID]) && $runs[$caseID] < $case->version) unset($cases[$caseID]);
            }
        }

        $steps = $this->dao->select('t1.*')->from(TABLE_CASESTEP)->alias('t1')
            ->leftJoin(TABLE_CASE)->alias('t2')->on('t1.case=t2.id')
            ->where('t2.id')->in($caseIDList)
            ->andWhere('t1.version=t2.version')
            ->andWhere('t2.status')->ne('wait')
            ->fetchGroup('case', 'id');

        $this->view->products      = $products;
        $this->view->applicationID = $applicationID;
        $this->view->productID     = $productID;
        $this->view->cases         = $cases;
        $this->view->steps         = $steps;
        $this->view->caseIDList    = array_keys($cases);
        $this->view->title         = $this->lang->testtask->batchRun;

        $this->display();
    }

    /**
     * Browse unit cases.
     *
     * @param  int    $taskID
     * @param  string $orderBy
     * @access public
     * @return void
     */
    public function unitCases($taskID, $orderBy = 'id')
    {
        /* Load lang. */
        $this->app->loadLang('testtask');
        $this->app->loadLang('execution');

        $task = $this->testtask->getById($taskID);
        if($task->product == 0) $task->product = 'na';
        $applicationID = $task->applicationID;
        $productID     = $task->product;

        $applicationID = $this->rebirth->saveState($this->applicationList, $applicationID, $productID);
        $this->rebirth->setMenu($applicationID, $productID);
        $productID     = $this->rebirth->getProductIdByApplication($applicationID, $productID);

        /* Save session. */
        $this->session->set('caseList', $this->app->getURI(true), 'qa');

        /* Get test cases. */
        $runs = $this->testtask->getRuns($taskID, 0, $orderBy);

        /* save session .*/
        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'testcase', false);

        $cases = array();
        $runs = $this->loadModel('testcase')->appendData($runs, 'testrun');
        foreach($runs as $run) $cases[$run->case] = $run;

        $results = $this->dao->select('*')->from(TABLE_TESTRESULT)->where('`case`')->in(array_keys($cases))->andWhere('run')->in(array_keys($runs))->fetchAll('run');
        foreach($results as $result)
        {
            $runs[$result->run]->caseResult = $result->caseResult;
            $runs[$result->run]->xml        = $result->xml;
            $runs[$result->run]->duration   = $result->duration;
        }

        $groupCases = $this->dao->select('*')->from(TABLE_SUITECASE)->where('`case`')->in(array_keys($cases))->orderBy('case')->fetchGroup('suite', 'case');
        $summary    = array();
        if(empty($groupCases)) $groupCases[] = $cases;
        foreach($groupCases as $suiteID => $groupCase)
        {
            $caseCount = 0;
            $failCount = 0;
            $duration  = 0;
            foreach($groupCase as $caseID => $suitecase)
            {
                $case = $cases[$caseID];
                $groupCases[$suiteID][$caseID] = $case;
                $duration += $case->duration;
                $caseCount ++;
                if($case->caseResult == 'fail') $failCount ++;
            }
            $summary[$suiteID] = sprintf($this->lang->testtask->summary, $caseCount, $failCount, $duration);
        }

        $suites = $this->loadModel('testsuite')->getUnitSuites($applicationID, $productID);

        /* Assign. */
        $this->view->title         = $this->applicationList[$applicationID] . $this->lang->colon . $this->lang->testcase->common;
        $this->view->applicationID = $applicationID;
        $this->view->productID     = $productID;
        $this->view->task          = $task;
        $this->view->users         = $this->loadModel('user')->getPairs('noletter');
        $this->view->groupCases    = $groupCases;
        $this->view->suites        = $suites;
        $this->view->summary       = $summary;
        $this->view->taskID        = $taskID;

        $this->display();
    }

    /**
     * View test results of a test run.
     *
     * @param  int    $runID
     * @param  int    $caseID
     * @access public
     * @return void
     */
    public function results($runID, $caseID = 0, $version = 0)
    {
        if($runID)
        {
            $case    = $this->testtask->getRunById($runID)->case;
            $results = $this->testtask->getResults($runID);

            $testtaskID = $this->dao->select('task')->from(TABLE_TESTRUN)->where('id')->eq($runID)->fetch('task');
            $testtask   = $this->dao->select('id, build, execution, product')->from(TABLE_TESTTASK)->where('id')->eq($testtaskID)->fetch();

            $this->view->testtask = $testtask;
        }
        else
        {
            $case    = $this->loadModel('testcase')->getByID($caseID, $version);
            $results = $this->testtask->getResults(0, $caseID);
        }

        $this->view->case    = $case;
        $this->view->runID   = $runID;
        $this->view->results = $results;
        $this->view->builds  = $this->loadModel('build')->getProductBuildPairs($case->product, $branch = 0, $params = '');
        $this->view->users   = $this->loadModel('user')->getPairs('noclosed, noletter');

        die($this->display());
    }

    /**
     * Batch assign cases.
     *
     * @param  int    $taskID
     * @access public
     * @return void
     */
    public function batchAssign($taskID)
    {
        $this->dao->update(TABLE_TESTRUN)
            ->set('assignedTo')->eq($this->post->assignedTo)
            ->where('task')->eq((int)$taskID)
            ->andWhere('`case`')->in($this->post->caseIDList)
            ->exec();
        die(js::reload('parent'));
    }

    /**
     * Import unit results.
     *
     * @param  int    $productID
     * @access public
     * @return void
     */
    public function importUnitResult($applicationID, $productID)
    {
        if($_POST)
        {
            if($_POST['product'] == 'na') $_POST['product'] = 0;
            $productID = $_POST['product'];
            $projectID = $_POST['project'];
            $taskID    = $this->testtask->importUnitResult($applicationID, $productID, $projectID);
            if(dao::isError()) die(js::error(dao::getError()));

            $this->loadModel('action')->create('testtask', $taskID, 'opened');
            die(js::locate($this->createLink('testtask', 'unitCases', "taskID=$taskID"), 'parent'));
        }

        $this->app->loadLang('job');
        $this->app->rawModule = 'testcase';

        /* Set menu. */
        $applicationID = $this->rebirth->saveState($this->applicationList, $applicationID, $productID);
        $this->rebirth->setMenu($applicationID, $productID);
        $productID     = $this->rebirth->getProductIdByApplication($applicationID, $productID);

        $products = $this->rebirth->getProductPairs($applicationID, true);
        $projects = array(0 => '') + $this->rebirth->getProductLinkProjectPairs($applicationID, $productID);

        $projectID = 0;
        $builds    = is_numeric($productID) ? $this->loadModel('build')->getProductBuildPairs($productID, 0, 'notrunk') : array();

        $this->view->title         = $this->applicationList[$applicationID] . $this->lang->colon . $this->lang->testtask->importUnitResult;
        $this->view->productID     = $productID;
        $this->view->applicationID = $applicationID;
        $this->view->projectID     = $projectID;
        $this->view->products      = $products;
        $this->view->projects      = $projects;
        $this->view->builds        = $builds;
        $this->view->users         = $this->loadModel('user')->getPairs('noletter|nodeleted|noclosed');

        $this->display();
    }

    /**
     * AJAX: return test tasks of a user in html select.
     *
     * @param  int    $userID
     * @param  string $id
     * @param  string $status
     * @access public
     * @return void
     */
    public function ajaxGetUserTestTasks($userID = '', $id = '', $status = 'all')
    {
        if($userID == '') $userID = $this->app->user->id;
        $user    = $this->loadModel('user')->getById($userID, 'id');
        $account = $user->account;

        $testTasks = $this->testtask->getUserTestTaskPairs($account, 0, $status);

        if($id) die(html::select("testtasks[$id]", $testTasks, '', 'class="form-control"'));
        die(html::select('testtask', $testTasks, '', 'class=form-control'));
    }

    /**
     * Ajax get test tasks.
     *
     * @param  int    $productID
     * @param  int    $executionID
     * @access public
     * @return void
     */
    public function ajaxGetTestTasks($productID, $executionID = 0)
    {
        $pairs = $this->testtask->getPairs($productID, $executionID);
        die(html::select('testtask', $pairs, '', "class='form-control chosen'"));
    }

    /**
     * Ajax get test tasks.
     *
     * @param  string $projects
     * @param  string $defaultValue
     * @param  bool   $isMultiple
     * @param  string $placeholder
     * @param  string $noEmptyResultHint
     * @access public
     * @return void
     */
    public function ajaxGetProjectTestTasks($projects = 0, $defaultValue = '', $isMultiple = true)
    {
        $projects = trim($projects, ',');
        $pairs    = $this->testtask->getProjectTestTasks($projects);
        $name     = $isMultiple ? 'testtask[]' : 'testtask';
        $multiple = $isMultiple ? 'multiple' : '';

        $this->loadModel('report');
        $placeholder       = $this->lang->report->selectProjectTips;
        $noEmptyResultHint = $this->lang->report->noTesttasks;
        die(html::select($name, $pairs, $defaultValue, "class='form-control picker-select' $multiple placeholder='{$placeholder}' data-empty-result-hint='{$noEmptyResultHint}'"));
    }
}
