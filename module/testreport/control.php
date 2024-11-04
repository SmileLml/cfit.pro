<?php
/**
 * The control file of testreport of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Yidong Wang <yidong@cnezsoft.com>
 * @package     testreport
 * @version     $Id$
 * @link        http://www.zentao.net
 */
class testreport extends control
{
    public $projectID = 0;

    /**
     * All application.
     *
     * @var    array
     * @access public
     */
    public $applicationList = array();

    /**
     * Construct
     *
     * @param  string $moduleName
     * @param  string $methodName
     * @access public
     * @return void
     */
    public function __construct($moduleName = '', $methodName = '')
    {
        parent::__construct($moduleName, $methodName);
        $this->loadModel('rebirth');
        $this->loadModel('qa');
        $this->loadModel('product');
        $this->loadModel('story');
        $this->loadModel('build');
        $this->loadModel('bug');
        $this->loadModel('tree');
        $this->loadModel('testcase');
        $this->loadModel('testtask');
        $this->loadModel('user');
        $this->app->loadLang('report');

        /* Get product data. */
        $objectID = 0;
        $applicationList = $this->rebirth->getApplicationPairs();

        $this->view->applicationList = $this->applicationList = $applicationList;

        if(empty($applicationList) and !helper::isAjaxRequest()) die($this->locate($this->createLink('application', 'create')));
    }

    /**
     * Browse report.
     *
     * @param  int    $applicationID
     * @param  int    $productID
     * @param  string $objectType
     * @param  string $extra
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function browse($applicationID, $productID = 'all', $objectType = 'product', $extra = '', $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $this->loadModel('datatable');
        if($extra)
        {
            $task          = $this->testtask->getById($extra);
            $applicationID = $task->applicationID;
            $productID     = empty($task->product) ? 'na' : $task->product;
        }
        $title = $extra ? $task->name : $this->applicationList[$applicationID];

        $applicationID = $this->rebirth->saveState($this->applicationList, $applicationID, $productID);
        $application   = $this->rebirth->getApplicationByID($applicationID);


        /* Set menu.  */
        if($this->app->openApp == 'project')
        {
            $this->loadModel('project')->setMenu($this->session->project);
            $this->lang->modulePageNav = $this->rebirth->selectProduct($this->session->project, $applicationID, $productID, 'testreport');
        }
        else
        {
            $this->rebirth->setMenu($applicationID, $productID);
        }

        $productID     = $this->rebirth->getProductIdByApplication($applicationID, $productID);
        $productIdList = $this->rebirth->getProductIdList($applicationID, $productID);
        $products      = $this->rebirth->getProductPairs($applicationID, true);

        $this->session->set('reportList', $this->app->getURI(true), $this->app->openApp);

        /* 获取固定排序字段。 */
        if(isset($this->config->testreport->browse->fixedSort)) $orderBy = $this->config->testreport->browse->fixedSort;

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = pager::init($recTotal, $recPerPage, $pageID);

        $reports = $this->testreport->getList($applicationID, $productIdList, $objectType, $extra, $orderBy, $pager);

        $projects = array();
        $tasks    = array();
        foreach($reports as $report)
        {
            $projects[$report->project] = $report->project;
            foreach(explode(',', $report->tasks) as $taskID) $tasks[$taskID] = $taskID;
        }
        if($projects) $projects = $this->dao->select('id,name')->from(TABLE_PROJECT)->where('id')->in($projects)->andWhere('`type`')->eq('project')->fetchPairs('id', 'name');
        if($tasks)    $tasks    = $this->dao->select('id,name')->from(TABLE_TESTTASK)->where('id')->in($tasks)->fetchPairs('id', 'name');

        $this->view->title         = $title . $this->lang->colon . $this->lang->testreport->common;
        $this->view->reports       = $reports;
        $this->view->orderBy       = $orderBy;
        $this->view->applicationID = $applicationID;
        $this->view->productID     = $productID;
        $this->view->products      = $products;
        $this->view->projects      = $projects;
        $this->view->objectType    = $objectType;
        $this->view->extra         = $extra;
        $this->view->pager         = $pager;
        $this->view->users         = $this->user->getPairs('noletter|noclosed|nodeleted');
        $this->view->tasks         = $tasks;
        $this->display();
    }

    /**
     * Create report.
     *
     * @param  int    $objectID
     * @param  string $objectType
     * @param  string $extra
     * @param  string $begin
     * @param  string $end
     * @access public
     * @return void
     */
    public function create($applicationID, $productID = 'all', $objectType = 'testtask', $extra = '', $begin = '', $end = '')
    {
        if($_POST)
        {
            $reportID = $this->testreport->create();
            if(dao::isError()) die(js::error(dao::getError()));
            $this->loadModel('action')->create('testreport', $reportID, 'Opened');
            die(js::locate(inlink('view', "reportID=$reportID"), 'parent'));
        }

        if($productID == 'all') $productID = 'na';
        if($objectType == 'testtask')
        {
            if($extra)
            {
                $testtask = $this->testtask->getById($extra);
                $defaultTesttaskID = $testtask->id;
                $applicationID     = $testtask->applicationID;
                $productID         = empty($testtask->product) ? 'na' : $testtask->product;
            }

            $applicationID = $this->rebirth->saveState($this->applicationList, $applicationID, $productID);
            $application   = $this->rebirth->getApplicationByID($applicationID);

            $productID     = $this->rebirth->getProductIdByApplication($applicationID, $productID);
            $productIdList = $this->rebirth->getProductIdList($applicationID, $productID);
            $products      = $this->rebirth->getProductPairs($applicationID, true);

            $taskPairs         = array();
            $scopeAndStatus[0] = 'local';
            $scopeAndStatus[1] = 'totalStatus';

            /* set menu.  */
            if($this->app->openApp == 'project')
            {
                $projectID = $this->session->projectID;
                $this->loadModel('project')->setMenu($this->session->project);
                $tasks = $this->testtask->getProjectTasks($projectID, 'id_desc', null, $applicationID, $productID, $scopeAndStatus, null);
            }
            else
            {
                $this->rebirth->setMenu($applicationID, $productID);
                $tasks = $this->testtask->getProductTasks($applicationID, $productIdList, 'id_desc', null, $scopeAndStatus);
            }


            foreach($tasks as $task)
            {
                if($task->build == 'trunk') continue;
                $taskPairs[$task->id] = $task->name;
            }
            if(empty($taskPairs)) die(js::alert($this->lang->testreport->noTestTask) . js::locate('back'));

            if(empty($extra))
            {
               $defaultTesttaskID = key($taskPairs);
               $testtask = $this->testtask->getById($defaultTesttaskID);
            }
            $this->view->testtask  = $testtask;
            $this->view->taskPairs = $taskPairs;
        }

        if(empty($defaultTesttaskID)) die(js::alert($this->lang->testreport->noObjectID) . js::locate('back'));
        if($objectType == 'testtask')
        {
            $begin   = !empty($begin) ? date("Y-m-d", strtotime($begin)) : $testtask->begin;
            $end     = !empty($end) ? date("Y-m-d", strtotime($end)) : $testtask->end;
            $builds  = array();
            $stories = array();
            $bugs    = array();
            if($testtask->build == 'trunk')
            {
                echo js::alert($this->lang->testreport->errorTrunk);
                die(js::locate('back'));
            }
            else
            {
                $buildIdList = $testtask->build ? explode(',', $testtask->build) : array();
                foreach($buildIdList as $buildID)
                {
                    $build    = $this->build->getById($buildID);
                    $stories += empty($build->stories) ? array() : $this->story->getByList($build->stories);

                    if(!empty($build->id)) $builds[$build->id] = $build;
                }
                $bugs = $this->testreport->getBugs4Test($builds, $applicationID, $productIdList, $begin, $end);
            }

            $tasks = array($testtask->id => $testtask);
            $owner = $testtask->owner;

            $this->setChartDatas($testtask->id);

            $this->view->title       = $testtask->name . $this->lang->testreport->create;
            $this->view->reportTitle = date('Y-m-d') . " TESTTASK#{$testtask->id} {$testtask->name} {$this->lang->testreport->common}";
        }

        $cases   = $this->testreport->getTaskCases($tasks, $begin, $end);
        $bugInfo = $this->testreport->getBugInfo($tasks, $applicationID, $productIdList, $begin, $end, $builds);

        $this->view->applicationID = $testtask->applicationID;
        $this->view->productID     = empty($testtask->product) ? 'na' : $testtask->product;
        $this->view->begin         = $begin;
        $this->view->end           = $end;
        $this->view->members       = $this->dao->select('DISTINCT lastRunner')->from(TABLE_TESTRUN)->where('task')->in(array_keys($tasks))->fetchPairs('lastRunner', 'lastRunner');
        $this->view->owner         = $owner;

        $this->view->stories       = $stories;
        $this->view->bugs          = $bugs;
        $this->view->productIdList = join(',', array_keys($productIdList));
        $this->view->tasks         = join(',', array_keys($tasks));
        $this->view->storySummary  = $this->product->summary($stories);

        $this->view->builds      = $builds;
        $this->view->users       = $this->user->getPairs('noletter|noclosed|nodeleted');
        $this->view->cases       = $cases;
        $this->view->caseSummary = $this->testreport->getResultSummary($tasks, $cases, $begin, $end);

        $perCaseResult = $this->testreport->getPerCaseResult4Report($tasks, $cases, $begin, $end);
        $perCaseRunner = $this->testreport->getPerCaseRunner4Report($tasks, $cases, $begin, $end);
        $this->view->datas['testTaskPerRunResult'] = $this->loadModel('report')->computePercent($perCaseResult);
        $this->view->datas['testTaskPerRunner']    = $this->report->computePercent($perCaseRunner);

        $this->view->legacyBugs = $bugInfo['legacyBugs'];
        unset($bugInfo['legacyBugs']);
        $this->view->bugInfo = $bugInfo;

        $this->view->defaultTesttaskID = $defaultTesttaskID;
        $this->view->objectType        = $objectType;
        $this->view->extra             = $extra;
        $this->display();
    }

    /**
     * Edit report
     *
     * @param  int       $reportID
     * @param  string    $begin
     * @param  string    $end
     * @access public
     * @return void
     */
    public function edit($reportID, $begin = '', $end ='')
    {
        if($_POST)
        {
            $changes = $this->testreport->update($reportID);
            if(dao::isError()) die(js::error(dao::getError()));

            $files      = $this->loadModel('file')->saveUpload('testreport', $reportID);
            $fileAction = !empty($files) ? $this->lang->addFiles . join(',', $files) . "\n" : '';
            $actionID   = $this->loadModel('action')->create('testreport', $reportID, 'Edited', $fileAction);
            if(!empty($changes)) $this->action->logHistory($actionID, $changes);

            die(js::locate(inlink('view', "reportID=$reportID"), 'parent'));
        }

        $report = $this->testreport->getById($reportID);

        $applicationID = $report->applicationID;
        $productID     = empty($report->product) ? 'na' : $report->product;

        $applicationID = $this->rebirth->saveState($this->applicationList, $applicationID, $productID);
        $application   = $this->rebirth->getApplicationByID($applicationID);

        /* Set menu. */
        if($this->app->openApp == 'project')
        {
            $this->loadModel('project')->setMenu($this->session->project);
        }
        else
        {
            $this->rebirth->setMenu($applicationID, $productID);
        }

        $productID     = $this->rebirth->getProductIdByApplication($applicationID, $productID);

        $browseLink = inlink('browse', "applicationID=$applicationID&productID=$productID&objectType=product");

        $begin   = !empty($begin) ? date("Y-m-d", strtotime($begin)) : $report->begin;
        $end     = !empty($end) ? date("Y-m-d", strtotime($end)) : $report->end;

        $stories = array();
        $bugs    = array();
        if($report->objectType == 'testtask')
        {
            $linkedProductIdList[$report->product] = $report->product;
            $task   = $this->testtask->getById($report->objectID);
            $builds = array();
            if($task->build == 'trunk')
            {
                echo js::alert($this->lang->testreport->errorTrunk);
                die(js::locate('back'));
            }
            else
            {
                $build   = $this->build->getById($task->build);
                $buildIdList = $task->build ? explode(',', $task->build) : array();
                foreach($buildIdList as $buildID)
                {
                    $build    = $this->build->getById($buildID);
                    $stories += empty($build->stories) ? array() : $this->story->getByList($build->stories);

                    if(!empty($build->id)) $builds[$build->id] = $build;
                }
                $bugs = $this->testreport->getBugs4Test($builds, $applicationID, $linkedProductIdList, $begin, $end);
            }
            $tasks = array($task->id => $task);

            $this->setChartDatas($report->objectID);
        }

        $cases   = $this->testreport->getTaskCases($tasks, $begin, $end);
        $bugInfo = $this->testreport->getBugInfo($tasks, $applicationID, $linkedProductIdList, $begin, $end, $builds);

        $this->view->title = $report->title . $this->lang->testreport->edit;

        $this->view->report        = $report;
        $this->view->begin         = $begin;
        $this->view->end           = $end;
        $this->view->stories       = $stories;
        $this->view->bugs          = $bugs;
        $this->view->linkedProductIdList = join(',', array_keys($linkedProductIdList));
        $this->view->tasks         = join(',', array_keys($tasks));
        $this->view->storySummary  = $this->product->summary($stories);

        $this->view->builds = $builds;
        $this->view->users  = $this->user->getPairs('noletter|noclosed|nodeleted');

        $this->view->cases       = $cases;
        $this->view->caseSummary = $this->testreport->getResultSummary($tasks, $cases, $begin, $end);

        $perCaseResult = $this->testreport->getPerCaseResult4Report($tasks, $cases, $begin, $end);
        $perCaseRunner = $this->testreport->getPerCaseRunner4Report($tasks, $cases, $begin, $end);
        $this->view->datas['testTaskPerRunResult'] = $this->loadModel('report')->computePercent($perCaseResult);
        $this->view->datas['testTaskPerRunner']    = $this->report->computePercent($perCaseRunner);

        $this->view->legacyBugs = $bugInfo['legacyBugs'];
        unset($bugInfo['legacyBugs']);
        $this->view->bugInfo = $bugInfo;

        $this->display();
    }

    /**
     * View report.
     *
     * @param  int    $reportID
     * @param  string $tab
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function view($reportID, $tab = 'basic', $recTotal = 0, $recPerPage = 100, $pageID = 1)
    {
        $reportID = (int)$reportID;
        $report   = $this->testreport->getById($reportID);

        $applicationID = $report->applicationID;
        $productID     = empty($report->product) ? 'na' : $report->product;

        $applicationID = $this->rebirth->saveState($this->applicationList, $applicationID, $productID);
        $application   = $this->rebirth->getApplicationByID($applicationID);
        $this->rebirth->setMenu($applicationID, $productID);
        $productID     = $this->rebirth->getProductIdByApplication($applicationID, $productID);
        $productIdList = $this->rebirth->getProductIdList($applicationID, $productID);
        $products      = $this->rebirth->getProductPairs($applicationID, true);

        /* set menu.  */
        if($this->app->openApp == 'project')
        {
            $this->loadModel('project')->setMenu($this->session->project);
        }
        else
        {
            $this->rebirth->setMenu($applicationID, $productID);
        }

        $stories = $report->stories ? $this->story->getByList($report->stories) : array();
        $results = $this->dao->select('*')->from(TABLE_TESTRESULT)->where('run')->in($report->tasks)->andWhere('`case`')->in($report->cases)->fetchAll();
        $failResults = array();
        $runCasesNum = array();
        foreach($results as $result)
        {
            $runCasesNum[$result->case] = $result->case;
            if($result->caseResult == 'fail') $failResults[$result->case] = $result->case;
        }

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = pager::init($recTotal, $recPerPage, $pageID);

        $tasks      = $report->tasks  ? $this->testtask->getByList($report->tasks) : array();
        $builds     = $report->builds ? $this->build->getByList($report->builds)   : array();
        $cases      = $this->testreport->getTaskCases($tasks, $report->begin, $report->end, $report->cases, $pager);
        $caseIdList = $this->testreport->getCaseIdList($reportID);
        $bugInfo    = $this->testreport->getBugInfo($tasks, $applicationID, $productIdList, $report->begin, $report->end, $builds);
        $bugs       = $report->bugs ? $this->bug->getByList($report->bugs) : array();

        if($report->objectType == 'testtask')
        {
            $this->setChartDatas($report->objectID);
        }
        elseif($tasks)
        {
            foreach($tasks as $task) $this->setChartDatas($task->id);
        }

        $projectPlan = '';
        if($report->project) $projectPlan = $this->dao->select('*')->from(TABLE_PROJECTPLAN)->where('project')->eq($report->project)->fetch();

        $this->view->title       = $report->title;
        $this->view->projectPlan = $projectPlan;

        $this->view->tab      = $tab;
        $this->view->pager    = $pager;
        $this->view->report   = $report;
        $this->view->products = $products;
        $this->view->stories  = $stories;
        $this->view->bugs     = $bugs;
        $this->view->builds   = $builds;
        $this->view->cases    = $cases;
        $this->view->users    = $this->user->getPairs('noletter|noclosed|nodeleted');
        $this->view->actions  = $this->loadModel('action')->getList('testreport', $reportID);

        $this->view->storySummary = $this->product->summary($stories);
        $this->view->caseSummary  = $this->testreport->getResultSummary($tasks, $caseIdList, $report->begin, $report->end);

        $perCaseResult = $this->testreport->getPerCaseResult4Report($tasks, $caseIdList, $report->begin, $report->end);
        $perCaseRunner = $this->testreport->getPerCaseRunner4Report($tasks, $caseIdList, $report->begin, $report->end);
        $this->view->datas['testTaskPerRunResult'] = $this->loadModel('report')->computePercent($perCaseResult);
        $this->view->datas['testTaskPerRunner']    = $this->report->computePercent($perCaseRunner);

        $this->view->legacyBugs = $bugInfo['legacyBugs'];
        unset($bugInfo['legacyBugs']);
        $this->view->bugInfo    = $bugInfo;
        $this->display();
    }

    /**
     * Delete report.
     *
     * @param  int    $reportID
     * @param  string $confirm
     * @access public
     * @return void
     */
    public function delete($reportID, $confirm = 'no')
    {
        if($confirm == 'no')
        {
            die(js::confirm($this->lang->testreport->confirmDelete, inlink('delete', "reportID=$reportID&confirm=yes")));
        }
        else
        {
            $this->testreport->delete(TABLE_TESTREPORT, $reportID);
            die(js::locate($this->session->reportList, 'parent'));
        }
    }

    /**
     * Set chart datas of cases.
     *
     * @param  int    $taskID
     * @access public
     * @return void
     */
    public function setChartDatas($taskID)
    {
        $this->loadModel('report');
        $task   = $this->loadModel('testtask')->getById($taskID);
        foreach($this->lang->testtask->report->charts as $chart => $title)
        {
            if(strpos($chart, 'testTask') === false) continue;

            $chartFunc   = 'getDataOf' . $chart;
            $chartData   = $this->testtask->$chartFunc($taskID);
            $chartOption = $this->testtask->mergeChartOption($chart);
            if(!empty($chartType)) $chartOption->type = $chartType;

            $this->view->charts[$chart] = $chartOption;
            if(isset($this->view->datas[$chart]))
            {
                $existDatas = $this->view->datas[$chart];
                $sum        = 0;
                foreach($chartData as $key => $data)
                {
                    if(isset($existDatas[$key]))
                    {
                        $data->value += $existDatas[$key]->value;
                        unset($existDatas[$key]);
                    }
                    $sum += $data->value;
                }
                foreach($existDatas as $key => $data)
                {
                    $sum += $data->value;
                    $chartData[$key] = $data;
                }
                if($sum)
                {
                    foreach($chartData as $data) $data->percent = round($data->value / $sum, 2);
                }
                ksort($chartData);
                $this->view->datas[$chart] = $chartData;
            }
            else
            {
                $this->view->datas[$chart] = $this->report->computePercent($chartData);
            }
        }
    }
}
