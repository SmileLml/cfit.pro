<?php
/**
 * The control file of case currentModule of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     case
 * @version     $Id: control.php 5112 2013-07-12 02:51:33Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
class testcase extends control
{
    /**
     * All apps.
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
     * Construct function, load product, tree, user auto.
     *
     * @access public
     * @return void
     */
    public function __construct($moduleName = '', $methodName = '')
    {
        parent::__construct($moduleName, $methodName);
        $this->loadModel('rebirth');
        $this->loadModel('product');
        $this->loadModel('tree');
        $this->loadModel('user');
        $this->loadModel('qa');

        $objectID = 0;
        $applicationList = $this->rebirth->getApplicationPairs();

        $this->view->applicationList = $this->applicationList = $applicationList;
        if(empty($applicationList) and !helper::isAjaxRequest()) die($this->locate($this->createLink('application', 'create')));
    }

    /**
     * Index page.
     *
     * @access public
     * @return void
     */
    public function index()
    {
        $this->locate($this->createLink('testcase', 'browse'));
    }

    /**
     * Browse cases.
     *
     * @param  int    $productID
     * @param  string $browseType
     * @param  int    $param
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @param  int    $projectID
     * @access public
     * @return void
     */
    public function browse($applicationID = 0, $productID = 'all', $branch = '', $browseType = 'all', $param = 0, $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1, $projectID = 0)
    {
        $this->loadModel('datatable');
        $this->app->loadLang('testtask');

        if(empty($productID)) $productID = 'na';
        $applicationID = $this->rebirth->saveState($this->applicationList, $applicationID, $productID);
        $application   = $this->rebirth->getApplicationByID($applicationID);
        $this->rebirth->setMenu($applicationID, $productID);
        $productID     = $this->rebirth->getProductIdByApplication($applicationID, $productID);
        $productIdList = $this->rebirth->getProductIdList($applicationID, $productID);

        /* 获取固定排序字段。 */
        if(isset($this->config->testcase->browse->fixedSort)) $orderBy = $this->config->testcase->browse->fixedSort;

        /* Set browse type. */
        $browseType = strtolower($browseType);

        /* Set browseType, productID, moduleID and queryID. */
        setcookie('preProductID', $productID, $this->config->cookieLife, $this->config->webRoot, '', $this->config->cookieSecure, true);

        if($this->cookie->preProductID != $productID)
        {
            $_COOKIE['caseModule'] = 0;
            setcookie('caseModule', 0, 0, $this->config->webRoot, '', $this->config->cookieSecure, false);
        }
        if($browseType == 'bymodule') setcookie('caseModule', (int)$param, 0, $this->config->webRoot, '', $this->config->cookieSecure, false);
        if($browseType == 'bysuite')  setcookie('caseSuite', (int)$param, 0, $this->config->webRoot, '', $this->config->cookieSecure, true);
        if($browseType != 'bymodule') $this->session->set('caseBrowseType', $browseType);

        $moduleID = ($browseType == 'bymodule') ? (int)$param : ($browseType == 'bysearch' ? 0 : ($this->cookie->caseModule ? $this->cookie->caseModule : 0));
        $suiteID  = ($browseType == 'bysuite')  ? (int)$param : ($browseType == 'bymodule' ? ($this->cookie->caseSuite ? $this->cookie->caseSuite : 0) : 0);
        $queryID  = ($browseType == 'bysearch') ? (int)$param : 0;

        $this->session->set('caseList', $this->app->getURI(true), $this->app->openApp);
        $this->session->set('productID', $productID);
        $this->session->set('moduleID', $moduleID);
        $this->session->set('browseType', $browseType);
        $this->session->set('orderBy', $orderBy);

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);
        $sort  = $this->loadModel('common')->appendOrder($orderBy);

        /* Get test cases. */
        $cases = $this->testcase->getTestCases($applicationID, $productIdList, $branch, $browseType, $browseType == 'bysearch' ? $queryID : $suiteID, $moduleID, $sort, $pager);
        if(empty($cases) and $pageID > 1)
        {
            $pager = pager::init(0, $recPerPage, 1);
            $cases = $this->testcase->getTestCases($applicationID, $productIdList, $branch, $browseType, $browseType == 'bysearch' ? $queryID : $suiteID, $moduleID, $sort, $pager);
        }

        /* save session .*/
        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'testcase', $browseType != 'bysearch' ? false : true);

        /* Process case for check story changed. */
        $cases = $this->loadModel('story')->checkNeedConfirm($cases);
        $cases = $this->testcase->appendData($cases);

        /* Build the search form. */
        $currentModule = 'testcase';
        $currentMethod = 'browse';
        $actionURL     = $this->createLink($currentModule, $currentMethod, "applicationID=$applicationID&productID=$productID&branch=$branch&browseType=bySearch&queryID=myQueryID");
        $this->config->testcase->search['onMenuBar'] = 'yes';

        $this->testcase->buildSearchForm($queryID, $actionURL, $applicationID, $productID);

        /* Get module tree.*/
        $moduleTree  = '';
        $modulePairs = array();
        $modules     = array();
        $showModule  = !empty($this->config->datatable->testcaseBrowse->showModule) ? $this->config->datatable->testcaseBrowse->showModule : '';
        if(is_numeric($productID))
        {
            $moduleTree  = $this->tree->getTreeMenu($productID, $viewType = 'case', $startModuleID = 0, array('treeModel', 'createCaseLink'), '', $branch);
            $modulePairs = $showModule ? $this->tree->getModulePairs($productID, 'case', $showModule) : array();
            $modules     = $this->tree->getOptionMenu($productID, $viewType = 'case', $startModuleID = 0, $branch);
        }

        $tree = $moduleID ? $this->tree->getByID($moduleID) : '';

        $projects = $this->rebirth->getProductLinkProjectPairs($applicationID, $productID);

        $this->view->title         = $this->applicationList[$applicationID] . $this->lang->colon . $this->lang->testcase->common;
        $this->view->modulePairs   = $modulePairs;
        $this->view->applicationID = $applicationID;
        $this->view->productID     = $productID;
        $this->view->modules       = $modules;
        $this->view->moduleTree    = $moduleTree;
        $this->view->moduleName    = $moduleID ? $tree->name : $this->lang->tree->all;
        $this->view->moduleID      = $moduleID;
        $this->view->summary       = $this->testcase->summary($cases);
        $this->view->pager         = $pager;
        $this->view->projects      = $projects;
        $this->view->users         = $this->user->getPairs('noletter');
        $this->view->orderBy       = $orderBy;
        $this->view->browseType    = $browseType;
        $this->view->param         = $param;
        $this->view->cases         = $cases;
        $this->view->branch        = $branch;
        $this->view->branches      = array();
        $this->view->suiteList     = $this->loadModel('testsuite')->getSuites($applicationID, $productIdList);
        $this->view->suiteID       = $suiteID;
        $this->view->products      = $this->rebirth->getProductPairs($applicationID, true);
        $this->view->setModule     = true;

        $this->display();
    }

    /**
     * Create a test case.
     * @param        $productID
     * @param string $branch
     * @param int    $moduleID
     * @param string $from
     * @param int    $param
     * @param int    $storyID
     * @param string $extras
     * @access public
     * @return void
     */
    public function create($applicationID, $productID, $branch = '', $moduleID = 0, $from = '', $param = 0, $storyID = 0, $extras = '')
    {
        $this->loadModel('story');

        $projectID = 0;
        if($this->app->openApp == 'project')
        {
            $projectID = $this->session->project;
            $this->loadModel('project')->setMenu($projectID);
        }
        else
        {
            $this->rebirth->setMenu($applicationID, $productID);
            $applicationID = $this->rebirth->saveState($this->applicationList, $applicationID, $productID);
            $productID     = $this->rebirth->getProductIdByApplication($applicationID, $productID);
        }

        $testcaseID = ($from and strpos('testcase|work|contribute', $from) !== false) ? $param : 0;
        $bugID      = $from == 'bug' ? $param : 0;

        if(!empty($_POST))
        {
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;

            setcookie('lastCaseModule', (int)$this->post->module, $this->config->cookieLife, $this->config->webRoot, '', $this->config->cookieSecure, false);
            $caseResult = $this->testcase->create($bugID);
            if(!$caseResult or dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $caseID = $caseResult['id'];
            if($caseResult['status'] == 'exists')
            {
                $response['message'] = sprintf($this->lang->duplicate, $this->lang->testcase->common);
                $response['locate']  = $this->createLink('testcase', 'view', "caseID=$caseID");
                $this->send($response);
            }

            $this->loadModel('action');
            $this->action->create('case', $caseID, 'Opened');

            $this->executeHooks($caseID);

            /* If link from no head then reload. */
            if(isonlybody()) $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'closeModal' => true));

            setcookie('caseModule', 0, 0, $this->config->webRoot, '', $this->config->cookieSecure, false);
            $response['locate'] = $this->session->caseList ? $this->session->caseList : $this->createLink('testcase', 'browse', "applicationID=$applicationID&productID=$productID&branch=$branch&browseType=all&param=0&orderBy=id_desc");
            $this->send($response);
        }
        
        /* Init vars. */
        $executionID  = 0;
        $type         = 'feature';
        $stage        = '';
        $pri          = 3;
        $caseTitle    = '';
        $precondition = '';
        $keywords     = '';
        $steps        = array();
        $color        = '';

        /* If testcaseID large than 0, use this testcase as template. */
        if($testcaseID > 0)
        {
            $testcase     = $this->testcase->getById($testcaseID);
            $productID    = $testcase->product;
            $projectID    = $testcase->project;
            $executionID  = $testcase->execution;
            $type         = $testcase->type ? $testcase->type : 'feature';
            $stage        = $testcase->stage;
            $pri          = $testcase->pri;
            $storyID      = $testcase->story;
            $caseTitle    = $testcase->title;
            $precondition = $testcase->precondition;
            $keywords     = $testcase->keywords;
            $steps        = $testcase->steps;
            $color        = $testcase->color;
        }

        /* If bugID large than 0, use this bug as template. */
        if($bugID > 0)
        {
            $bug         = $this->loadModel('bug')->getById($bugID);
            $projectID   = $bug->project;
            $executionID = $bug->execution;
            $type        = $bug->type;
            $pri         = $bug->pri ? $bug->pri : $bug->severity;
            $storyID     = $bug->story;
            $caseTitle   = $bug->title;
            $keywords    = $bug->keywords;
            $steps       = $this->testcase->createStepsFromBug($bug->steps);
        }

        /* Padding the steps to the default steps count. */
        if(count($steps) < $this->config->testcase->defaultSteps)
        {
            $paddingCount = $this->config->testcase->defaultSteps - count($steps);
            $step = new stdclass();
            $step->type   = 'item';
            $step->desc   = '';
            $step->expect = '';
            for($i = 1; $i <= $paddingCount; $i ++) $steps[] = $step;
        }

        /* Set story and currentModuleID. */
        if($storyID)
        {
            $story = $this->loadModel('story')->getByID($storyID);
            if(empty($moduleID)) $moduleID = $story->module;
        }
        $currentModuleID = (int)$moduleID;

        /* Get the status of stories are not closed. */
        $storyStatus = $this->lang->story->statusList;
        unset($storyStatus['closed']);
        $modules = array();
        if($currentModuleID)
        {
            $modules = $this->loadModel('tree')->getStoryModule($currentModuleID);
            $modules = $this->tree->getAllChildID($modules);
        }
        $stories = $this->story->getProductStoryPairs($productID, $branch, $modules, array_keys($storyStatus), 'id_desc', 50, 'null', 'story', false);
        if($storyID and !isset($stories[$storyID])) $stories = $this->story->formatStories(array($storyID => $story)) + $stories;//Fix bug #2406.

        /* Set custom. */
        foreach(explode(',', $this->config->testcase->customCreateFields) as $field) $customFields[$field] = $this->lang->testcase->$field;
        $this->view->customFields = $customFields;
        $this->view->showFields   = $this->config->testcase->custom->createFields;

        /* 获取产品关联的项目。*/
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

        $this->view->title            = $this->applicationList[$applicationID] . $this->lang->colon . $this->lang->testcase->create;
        $this->view->applicationID    = $applicationID;
        $this->view->productID        = $productID;
        $this->view->projectID        = $projectID;
        $this->view->executionID      = $executionID;
        $this->view->moduleOptionMenu = $this->tree->getOptionMenu($productID, $viewType = 'case', $startModuleID = 0, $branch);
        $this->view->currentModuleID  = $currentModuleID ? $currentModuleID : (int)$this->cookie->lastCaseModule;
        $this->view->stories          = $stories;
        $this->view->caseTitle        = $caseTitle;
        $this->view->color            = $color;
        $this->view->type             = $type;
        $this->view->stage            = $stage;
        $this->view->products         = $products;
        $this->view->projects         = $projects;
        $this->view->executions       = array(0 => '');
        $this->view->pri              = $pri;
        $this->view->storyID          = $storyID;
        $this->view->precondition     = $precondition;
        $this->view->keywords         = $keywords;
        $this->view->steps            = $steps;
        $this->view->users            = $this->user->getPairs('noletter|noclosed|nodeleted');
        $this->view->branch           = $branch;
        $this->view->branches         = array();

        $this->display();
    }


    /**
     * Create a batch test case.
     *
     * @param  int   $productID
     * @param  int   $moduleID
     * @param  int   $storyID
     * @access public
     * @return void
     */
    public function batchCreate($applicationID, $productID, $branch = '', $moduleID = 0, $storyID = 0)
    {
        $this->loadModel('story');
        if(!empty($_POST))
        {
            $caseID = $this->testcase->batchCreate($applicationID, $productID, $branch, $storyID);
            if(dao::isError()) die(js::error(dao::getError()));
            if(isonlybody()) die(js::closeModal('parent.parent', 'this'));

            setcookie('caseModule', 0, 0, $this->config->webRoot, '', $this->config->cookieSecure, false);
            $currentModule = 'testcase';
            $currentMethod = 'browse';

            $link = $this->createLink($currentModule, $currentMethod, "applicationID=$applicationID&productID=$productID&branch=$branch&browseType=all&param=0&orderBy=id_desc");
            if($this->app->openApp == 'project')
            {
                $currentModule = 'project';
                $currentMethod = 'testcase';
                $projectID     = $this->session->project;

                $link = $this->createLink($currentModule, $currentMethod, "project=$projectID&applicationID=$applicationID&productID=$productID&branch=$branch&browseType=all&param=0&orderBy=id_desc");
            }
            die(js::locate($link, 'parent'));
        }

        if($productID == 'all') $productID = 'na';

        $projectID     = '';
        $applicationID = $this->rebirth->saveState($this->applicationList, $applicationID, $productID);
        $productID     = $this->rebirth->getProductIdByApplication($applicationID, $productID);

        if($this->app->openApp == 'project')
        {
            $projectID = $this->session->project;
            $this->loadModel('project')->setMenu($projectID);
        }
        else
        {
            $this->rebirth->setMenu($applicationID, $productID);
        }

        if($storyID and empty($moduleID))
        {
            $story    = $this->loadModel('story')->getByID($storyID);
            $moduleID = $story->module;
        }
        $currentModuleID = (int)$moduleID;

        /* Set story list. */
        $story     = $storyID ? $this->story->getByID($storyID) : '';
        $storyList = $storyID ? array($storyID => $story->id . ':' . $story->title) : array('');

        /* Set module option menu. */
        $moduleOptionMenu          = $this->tree->getOptionMenu($productID, $viewType = 'case', $startModuleID = 0, $branch);
        $moduleOptionMenu['ditto'] = $this->lang->testcase->ditto;

        /* Set custom. */
        foreach(explode(',', $this->config->testcase->customBatchCreateFields) as $field)
        {
            $customFields[$field] = $this->lang->testcase->$field;
        }
        $showFields = $this->config->testcase->custom->batchCreateFields;
        $this->view->customFields = $customFields;
        $this->view->showFields   = $showFields;

        /* 获取产品关联的项目。*/
        $projects  = array(0 => '');
        $projects += $this->rebirth->getProductLinkProjectPairs($applicationID, $productID);

        $this->view->title            = $this->applicationList[$applicationID] . $this->lang->colon . $this->lang->testcase->batchCreate;
        $this->view->productID        = $productID;
        $this->view->projects         = $projects;
        $this->view->projectID        = $projectID;
        $this->view->story            = $story;
        $this->view->storyList        = $storyList;
        $this->view->moduleOptionMenu = $moduleOptionMenu;
        $this->view->currentModuleID  = $currentModuleID;
        $this->view->branch           = $branch;
        $this->view->branches         = array();
        $this->view->needReview       = $this->testcase->forceNotReview() == true ? 0 : 1;

        $this->display();
    }

    /**
     * View a test case.
     *
     * @param  int    $caseID
     * @param  int    $version
     * @param  string $from
     * @access public
     * @return void
     */
    public function view($caseID, $version = 0, $from = 'testcase', $taskID = 0)
    {
        $caseID = (int)$caseID;
        $case   = $this->testcase->getById($caseID, $version);
        if(!$case) die(js::error($this->lang->notFound) . js::locate('back'));

        if($case->auto == 'unit')
        {
            $this->lang->testcase->subMenu->testcase->feature['alias']  = '';
            $this->lang->testcase->subMenu->testcase->unit['alias']     = 'view';
            $this->lang->testcase->subMenu->testcase->unit['subModule'] = 'testcase';
        }

        if($from == 'testtask')
        {
            $run = $this->loadModel('testtask')->getRunByCase($taskID, $caseID);
            $case->assignedTo    = $run->assignedTo;
            $case->lastRunner    = $run->lastRunner;
            $case->lastRunDate   = $run->lastRunDate;
            $case->lastRunResult = $run->lastRunResult;
            $case->caseStatus    = $case->status;
            $case->status        = $run->status;

            $results = $this->testtask->getResults($run->id);
            $result  = array_shift($results);
            if($result)
            {
                $case->xml      = $result->xml;
                $case->duration = $result->duration;
            }

            if($version != $case->version)
            {
                $testrun = $this->dao->select('precondition')->from(TABLE_TESTRUN)
                ->where('`case`')->eq($caseID)
                ->andWhere('task')->eq($taskID)
                ->fetch();

                if(!is_null($testrun->precondition))
                {
                    $case->precondition = $testrun->precondition;
                }
            }
        }
        

        $isLibCase = ($case->lib and empty($case->product));
        if($isLibCase)
        {
            $libraries = $this->loadModel('caselib')->getLibraries();
            $this->caselib->setLibMenu($libraries, $case->lib);
            $this->view->title   = "CASE #$case->id $case->title - " . $libraries[$case->lib];
            $this->view->libName = $libraries[$case->lib];

            if($this->app->openApp == 'project')
            {
                $this->loadModel('project')->setMenu($case->project);
            }
        }
        else
        {
            if($case->product == 0) $case->product = 'na';
            $applicationID = $case->applicationID;

            $productID = $case->product;
            if($productID == 'na')
            {
                $product = new stdClass();
                $product->id   = 'na';
                $product->name = $this->lang->naProduct;
            }
            else
            {
                $product = $this->loadModel('product')->getByID($productID);
            }

            if($this->app->openApp == 'project')
            {
                $this->loadModel('project')->setMenu($this->session->project);
            }
            else
            {
                $applicationID = $this->rebirth->saveState($this->applicationList, $case->applicationID, $case->product);
                $this->rebirth->setMenu($applicationID, $case->product);
                $productID = $this->rebirth->getProductIdByApplication($applicationID, $case->product);
            }

            $this->view->title   = "CASE #$case->id $case->title - " . $this->applicationList[$applicationID];
            $this->view->product = $product;

            /* 用例所关联的项目，项目所关联的年度项目计划信息。*/
            $projectPlan = '';
            if($case->project) $projectPlan = $this->dao->select('*')->from(TABLE_PROJECTPLAN)->where('project')->eq($case->project)->fetch();
            $this->view->projectPlan = $projectPlan;

            $execution = '';
            if($case->execution) $execution = $this->dao->select('*')->from(TABLE_EXECUTION)->where('id')->eq($case->execution)->fetch();
            $this->view->execution = $execution;
        }

        $caseFails = $this->dao->select('COUNT(*) AS count')->from(TABLE_TESTRESULT)
            ->where('caseResult')->eq('fail')
            ->andwhere('`case`')->eq($caseID)
            ->beginIF($from == 'testtask')->andwhere('`run`')->eq($taskID)->fi()
            ->fetch('count');
        $case->caseFails = $caseFails;

        $this->executeHooks($caseID);

        $this->view->applicationID = $case->applicationID;
        $this->view->case          = $case;
        $this->view->from          = $from;
        $this->view->taskID        = $taskID;
        $this->view->version       = $version ? $version : $case->version;
        $this->view->modulePath    = $this->tree->getParents($case->module);
        $this->view->caseModule    = empty($case->module) ? '' : $this->tree->getById($case->module);
        $this->view->users         = $this->user->getPairs('noletter');
        $this->view->actions       = $this->loadModel('action')->getList('case', $caseID);
        $this->view->preAndNext    = $this->loadModel('common')->getPreAndNextObject('testcase', $caseID);
        $this->view->runID         = $from == 'testcase' ? 0 : $run->id;
        $this->view->isLibCase     = $isLibCase;
        $this->view->caseFails     = $caseFails;

        $this->display();
    }

    /**
     * Edit a case.
     *
     * @param  int   $caseID
     * @access public
     * @return void
     */
    public function edit($caseID, $comment = false)
    {
        $this->loadModel('story');

        if(!empty($_POST))
        {
            $changes = array();
            $files   = array();
            if($comment == false)
            {
                $changes = $this->testcase->update($caseID);
                if(dao::isError()) die(js::error(dao::getError()));
                $files = $this->loadModel('file')->saveUpload('testcase', $caseID);
            }
            if($this->post->comment != '' or !empty($changes) or !empty($files))
            {
                $this->loadModel('action');
                $action = (!empty($changes) or !empty($files)) ? 'Edited' : 'Commented';
                $fileAction = '';
                if(!empty($files)) $fileAction = $this->lang->addFiles . join(',', $files) . "\n";
                $actionID = $this->action->create('case', $caseID, $action, $fileAction . $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }

            $this->executeHooks($caseID);

            die(js::locate($this->createLink('testcase', 'view', "caseID=$caseID"), 'parent'));
        }

        $case = $this->testcase->getById($caseID);
        if(!$case) die(js::error($this->lang->notFound) . js::locate('back'));

        if($case->auto == 'unit')
        {
            $this->lang->testcase->subMenu->testcase->feature['alias']  = '';
            $this->lang->testcase->subMenu->testcase->unit['alias']     = 'view';
            $this->lang->testcase->subMenu->testcase->unit['subModule'] = 'testcase';
        }

        if(empty($case->steps))
        {
            $step = new stdclass();
            $step->type   = 'step';
            $step->desc   = '';
            $step->expect = '';
            $case->steps[] = $step;
        }

        $isLibCase = ($case->lib and empty($case->product));
        if($isLibCase)
        {
            $libraries = $this->loadModel('caselib')->getLibraries();
            $title     = "CASE #$case->id $case->title - " . $libraries[$case->lib];

            $this->caselib->setLibMenu($libraries, $case->lib);

            $this->view->libID     = $case->lib;
            $this->view->libName   = $libraries[$case->lib];
            $this->view->libraries = $libraries;
            $this->view->moduleOptionMenu = $this->tree->getOptionMenu($case->lib, $viewType = 'caselib', $startModuleID = 0);

            if($this->app->openApp == 'project')
            {
                $this->loadModel('project')->setMenu($case->project);
            }            
        }
        else
        {
            if($case->product == 0) $case->product = 'na';
            $productID = $case->product;

            /* Get product, then set menu. */
            $applicationID = $this->rebirth->saveState($this->applicationList, $case->applicationID, $case->product);

            if($this->app->openApp == 'project')
            {
                $this->loadModel('project')->setMenu($case->project);
                $products      = $this->rebirth->getProjectProductPairs($applicationID, $case->project);
                $assignProduct = $this->loadModel('product')->getByIdPairs(array($productID));
                $products      = $products + $assignProduct;
            }
            else
            {
                $this->rebirth->setMenu($applicationID, $case->product);
                $productID = $this->rebirth->getProductIdByApplication($applicationID, $case->product);
                $products = $this->rebirth->getProductPairs($applicationID, true);
            }

            $stories          = array();
            $moduleOptionMenu = array();
            if(is_numeric($productID))
            {
                $stories          = $this->story->getProductStoryPairs($productID, $case->branch);
                $moduleOptionMenu = $this->tree->getOptionMenu($productID, $viewType = 'case', $startModuleID = 0, $case->branch);
            }

            if($case->lib and $case->fromCaseID)
            {
                $libName    = $this->loadModel('caselib')->getById($case->lib)->name;
                $libModules = $this->tree->getOptionMenu($case->lib, 'caselib');
                foreach($libModules as $moduleID => $moduleName)
                {
                    if($moduleID == 0) continue;
                    $moduleOptionMenu[$moduleID] = $libName . $moduleName;
                }
            }

            $this->view->productID        = $productID;
            $this->view->moduleOptionMenu = $moduleOptionMenu;
            $this->view->stories          = $stories;

            $projects   = array(0 => '') + $this->rebirth->getProductLinkProjectPairs($case->applicationID, $case->product);
            $executions = array(0 => '');
            if($case->project) $executions += $this->loadModel('project')->getExecutionByAvailable($case->project);

            $this->view->products   = $products;
            $this->view->projects   = $projects;
            $this->view->executions = $executions;

            $title = $this->applicationList[$case->applicationID] . $this->lang->colon . $this->lang->testcase->edit;
        }

        $forceNotReview = $this->testcase->forceNotReview();
        if($forceNotReview) unset($this->lang->testcase->statusList['wait']);

        $this->view->title           = $title;
        $this->view->currentModuleID = $case->module;
        $this->view->users           = $this->user->getPairs('noletter');
        $this->view->case            = $case;
        $this->view->actions         = $this->loadModel('action')->getList('case', $caseID);
        $this->view->isLibCase       = $isLibCase;
        $this->view->forceNotReview  = $forceNotReview;

        $this->display();
    }

    /**
     * Batch edit case.
     *
     * @param  int    $productID
     * @access public
     * @return void
     */
    public function batchEdit($applicationID = 0, $productID = 0, $branch = 0, $type = 'case')
    {
        if($this->post->title)
        {
            $allChanges = $this->testcase->batchUpdate();
            if($allChanges)
            {
                foreach($allChanges as $caseID => $changes )
                {
                    if(empty($changes)) continue;

                    $actionID = $this->loadModel('action')->create('case', $caseID, 'Edited');
                    $this->action->logHistory($actionID, $changes);
                }
            }

            die(js::locate($this->session->caseList, 'parent'));
        }

        if($this->app->openApp == 'project')
        {
            $this->loadModel('project')->setMenu($this->session->project);
        }
        elseif(empty($applicationID))
        {
            $this->loadModel('my')->setMenu();
            $this->lang->task->menu = $this->lang->my->menu->work;
            $this->lang->my->menu->work['subModule'] = 'testcase';
        }
        else
        {
            $this->rebirth->setMenu($applicationID, $productID);
            $applicationID = $this->rebirth->saveState($this->applicationList, $applicationID, $productID);
            $productID     = $this->rebirth->getProductIdByApplication($applicationID, $productID);
        }

        $caseIDList = $this->post->caseIDList ? $this->post->caseIDList : die(js::locate($this->session->caseList));
        $caseIDList = array_unique($caseIDList);

        /* Judge whether the editedCases is too large and set session. */
        $cases = $this->testcase->getByList($caseIDList);
        $countInputVars  = count($cases) * (count(explode(',', $this->config->testcase->custom->batchEditFields)) + 3);
        $showSuhosinInfo = common::judgeSuhosinSetting($countInputVars);
        if($showSuhosinInfo) $this->view->suhosinInfo = extension_loaded('suhosin') ? sprintf($this->lang->suhosinInfo, $countInputVars) : sprintf($this->lang->maxVarsInfo, $countInputVars);

        /* Set custom. */
        foreach(explode(',', $this->config->testcase->customBatchEditFields) as $field) $customFields[$field] = $this->lang->testcase->$field;
        $this->view->customFields = $customFields;
        $this->view->showFields   = $this->config->testcase->custom->batchEditFields;

        /* Assign. */
        $this->view->title          = $this->lang->testcase->batchEdit;
        $this->view->caseIDList     = $caseIDList;
        $this->view->priList        = array('ditto' => $this->lang->testcase->ditto) + $this->lang->testcase->priList;
        $this->view->typeList       = array('' => '', 'ditto' => $this->lang->testcase->ditto) + $this->lang->testcase->typeList;
        $this->view->cases          = $cases;
        $this->view->forceNotReview = $this->testcase->forceNotReview();

        $this->display();
    }

    /**
     * Review case.
     *
     * @param  int    $caseID
     * @access public
     * @return void
     */
    public function review($caseID)
    {
        if($_POST)
        {
            $changes = $this->testcase->review($caseID);
            if(dao::isError()) die(js::error(dao::getError()));

            if($changes or $this->post->comment != '')
            {
                $result = $this->post->result;
                $actionID = $this->loadModel('action')->create('case', $caseID, 'Reviewed', $this->post->comment, ucfirst($result));
                $this->action->logHistory($actionID, $changes);
            }

            $this->executeHooks($caseID);

            die(js::reload('parent.parent'));
        }

        $this->view->users   = $this->user->getPairs('noletter|noclosed|nodeleted');
        $this->view->case    = $this->testcase->getById($caseID);
        $this->view->actions = $this->loadModel('action')->getList('case', $caseID);
        $this->display();
    }

    /**
     * Batch review case.
     *
     * @param  string $result
     * @access public
     * @return void
     */
    public function batchReview($result)
    {
        $caseIdList = $this->post->caseIDList ? $this->post->caseIDList : die(js::locate($this->session->caseList, 'parent'));
        $caseIdList = array_unique($caseIdList);
        $actions    = $this->testcase->batchReview($caseIdList, $result);

        if(dao::isError()) die(js::error(dao::getError()));
        die(js::locate($this->session->caseList, 'parent'));
    }

    /**
     * Delete a test case
     *
     * @param  int    $caseID
     * @param  string $confirm yes|noe
     * @access public
     * @return void
     */
    public function delete($caseID, $confirm = 'no')
    {
        if($confirm == 'no')
        {
            die(js::confirm($this->lang->testcase->confirmDelete, inlink('delete', "caseID=$caseID&confirm=yes")));
        }
        else
        {
            $this->testcase->delete(TABLE_CASE, $caseID);

            $this->executeHooks($caseID);

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
            die(js::locate($this->session->caseList, 'parent'));
        }
    }

    /**
     * Batch delete cases.
     *
     * @param  int    $productID
     * @access public
     * @return void
     */
    public function batchDelete($productID = 0)
    {
        $caseIDList = $this->post->caseIDList ? $this->post->caseIDList : die(js::locate($this->session->caseList));
        $caseIDList = array_unique($caseIDList);

        foreach($caseIDList as $caseID) $this->testcase->delete(TABLE_CASE, $caseID);
        die(js::locate($this->session->caseList));
    }

    /**
     * Batch change the module of case.
     *
     * @param  int    $moduleID
     * @access public
     * @return void
     */
    public function batchChangeModule($moduleID)
    {
        if($this->post->caseIDList)
        {
            $caseIDList = $this->post->caseIDList;
            $caseIDList = array_unique($caseIDList);
            unset($_POST['caseIDList']);
            $allChanges = $this->testcase->batchChangeModule($caseIDList, $moduleID);
            if(dao::isError()) die(js::error(dao::getError()));
            foreach($allChanges as $caseID => $changes)
            {
                $this->loadModel('action');
                $actionID = $this->action->create('case', $caseID, 'Edited');
                $this->action->logHistory($actionID, $changes);
            }
        }

        die(js::locate($this->session->caseList, 'parent'));
    }

    /**
     * Batch review case.
     *
     * @param  string $result
     * @access public
     * @return void
     */
    public function batchCaseTypeChange($result)
    {
        $caseIdList = $this->post->caseIDList ? $this->post->caseIDList : die(js::locate($this->session->caseList, 'parent'));
        $caseIDList = array_unique($caseIDList);
        $this->testcase->batchCaseTypeChange($caseIdList, $result);

        if(dao::isError()) die(js::error(dao::getError()));
        die(js::locate($this->session->caseList, 'parent'));
    }

    /**
     * Confirm story changes.
     *
     * @param  int    $caseID
     * @access public
     * @return void
     */
    public function confirmStoryChange($caseID,$reload=true)
    {
        $case = $this->testcase->getById($caseID);
        if(isset($case->latestStoryVersion))
        {
            $this->dao->update(TABLE_CASE)->set('storyVersion')->eq($case->latestStoryVersion)->where('id')->eq($caseID)->exec();
            $this->loadModel('action')->create('case', $caseID, 'confirmed', '', $case->latestStoryVersion);
        }
        if($reload) die(js::reload('parent'));
    }

    /**
     * Batch ctory change cases.
     *
     * @param  int    $productID
     * @access public
     * @return void
     */
    public function batchConfirmStoryChange($productID = 0)
    {
        $caseIDList = $this->post->caseIDList ? $this->post->caseIDList : die(js::locate($this->session->caseList));
        $caseIDList = array_unique($caseIDList);

        foreach($caseIDList as $caseID) $this->confirmStoryChange($caseID,false);
        die(js::locate($this->session->caseList));
    }

    /**
     * Batch confirm change cases.
     *
     * @access public
     * @return void
     */
    public function batchConfirmLibcaseChange()
    {
        $caseIDList = $this->post->caseIDList ? $this->post->caseIDList : die(js::locate($this->session->caseList));
        $caseIDList = array_unique($caseIDList);

        foreach($caseIDList as $caseID) $this->confirmLibcaseChange($caseID, null, 'control');
        die(js::locate($this->session->caseList));
    }

    /**
     * Export test case.
     *
     * @param  int    $applicationID
     * @param  int    $productID
     * @param  string $orderBy
     * @param  int    $taskID
     * @param  string $browseType
     * @param  int    $projectID
     * @access public
     * @return void
     */
    public function export($applicationID, $productID, $orderBy, $taskID = 0, $browseType = '', $projectID = 0)
    {
        if($_POST)
        {
            $this->loadModel('file');
            $this->app->loadLang('testtask');

            $caseLang        = $this->lang->testcase;
            $caseConfig      = $this->config->testcase;
            $applicationName = $this->applicationList[$applicationID];

            /* Create field lists. */
            $fields = $this->post->exportFields ? $this->post->exportFields : explode(',', $caseConfig->exportFields);
            foreach($fields as $key => $fieldName)
            {
                $fieldName          = trim($fieldName);
                $fields[$fieldName] = isset($caseLang->$fieldName) ? $caseLang->$fieldName : $fieldName;
                unset($fields[$key]);
            }

            if($taskID)
            {
                $fields['assignedTo'] = $this->lang->testcase->assignedTo;
                $fields['lastRunner'] = $this->lang->testcase->lastRunner;
            }

            /* Result of the latest 10 executions. */
            for($number = 1; $number <= 10; $number ++)
            {
                $fields['result' . $number] = sprintf($this->lang->testcase->nthTime, $number);
            }

            /* Get cases. */
            if($this->session->testcaseOnlyCondition)
            {
                if($taskID)
                {
                    $caseIDList = $this->dao->select('`case`')->from(TABLE_TESTRUN)->where('task')->eq($taskID)->fetchPairs();
                    $cases      = $this->dao->select('*')->from(TABLE_CASE)->where($this->session->testcaseQueryCondition)->andWhere('id')->in($caseIDList)
                        ->beginIF($this->post->exportType == 'selected')->andWhere('id')->in($this->cookie->checkedItem)->fi()
                        ->orderBy($orderBy)->fetchAll('id');
                }
                else
                {
                    $cases = $this->dao->select('*')->from(TABLE_CASE)->where($this->session->testcaseQueryCondition)
                        ->beginIF($this->post->exportType == 'selected')->andWhere('id')->in($this->cookie->checkedItem)->fi()
                        ->orderBy($orderBy)->fetchAll('id');
                }
            }
            else
            {
                $cases   = array();
                $orderBy = " ORDER BY " . str_replace(array('|', '^A', '_'), ' ', $orderBy);
                $stmt    = $this->dbh->query($this->session->testcaseQueryCondition . $orderBy);
                while($row = $stmt->fetch())
                {
                    $caseID = isset($row->case) ? $row->case : $row->id;
                    if($this->post->exportType == 'selected' and strpos(",{$this->cookie->checkedItem},", ",$caseID,") === false) continue;
                    $cases[$caseID] = $row;
                    $row->id        = $caseID;
                }
            }

            if($taskID) $caseLang->statusList = $this->lang->testtask->statusList;

            /* Get related objects id lists. */
            $relatedStoryIdList     = array();
            $relatedCaseIdList      = array();
            $relatedProductIdList   = array();
            $relatedProjectIdList   = array();
            $relatedExecutionIdList = array();

            foreach($cases as $case)
            {
                $relatedStoryIdList[$case->story]         = $case->story;
                $relatedProductIdList[$case->product]     = $case->product;
                $relatedProjectIdList[$case->project]     = $case->project;
                $relatedExecutionIdList[$case->execution] = $case->execution;

                /* Process link cases. */
                $case->linkCase = trim($case->linkCase, ',');
                $linkCases = explode(',', $case->linkCase);
                foreach($linkCases as $linkCaseID)
                {
                    if($linkCaseID) $relatedCaseIdList[$linkCaseID] = trim($linkCaseID);
                }

                $case->assignedTo = '';
                $case->lastRunner = '';
            }

            $products    = array(0 => $this->lang->naProduct);
            $products   += $this->dao->select('id,name')->from(TABLE_PRODUCT)->where('id')->in($relatedProductIdList)->fetchPairs();
            $projects    = $this->dao->select('id,name')->from(TABLE_PROJECT)->where('id')->in($relatedProjectIdList)->fetchPairs();
            $executions  = $this->dao->select('id,name')->from(TABLE_EXECUTION)->where('id')->in($relatedExecutionIdList)->fetchPairs();

            $stmt = $this->dao->select('t1.*,t2.assignedTo')->from(TABLE_TESTRESULT)->alias('t1')
                ->leftJoin(TABLE_TESTRUN)->alias('t2')->on('t1.run=t2.id')
                ->where('t1.`case`')->in(array_keys($cases))
                ->beginIF($taskID)->andWhere('t2.task')->eq($taskID)->fi()
                ->orderBy('id_desc')
                ->query();

            $results = array();
            while($result = $stmt->fetch())
            {
                if(!isset($results[$result->case])) $results[$result->case] = unserialize($result->stepResults);
                $case = $cases[$result->case];
                $case->assignedTo = $result->assignedTo;
                $case->lastRunner = $result->lastRunner;

                $cases[$result->case] = $case;
            }

            $caseIDList = array_keys($cases);

            /* Get related objects title or names. */
            $relatedModules = $this->loadModel('tree')->getAllModulePairs('case');
            $relatedStories = $this->dao->select('id,title')->from(TABLE_STORY) ->where('id')->in($relatedStoryIdList)->fetchPairs();
            $relatedCases   = $this->dao->select('id,title')->from(TABLE_CASE)->where('id')->in($relatedCaseIdList)->fetchPairs();
            $relatedSteps   = $this->dao->select('id,parent,`case`,version,type,`desc`,expect')->from(TABLE_CASESTEP)->where('`case`')->in($caseIDList)->orderBy('version desc,id')->fetchGroup('case', 'id');
            $relatedFiles   = $this->dao->select('id,objectID,pathname,title')->from(TABLE_FILE)->where('objectType')->eq('testcase')->andWhere('objectID')->in($caseIDList)->andWhere('extra')->ne('editor')->fetchGroup('objectID');

            $cases = $this->testcase->appendData($cases);
            $users = $this->user->getPairs('noletter');

            foreach($cases as $case)
            {
                if(empty($applicationName))
                {
                    $applicationID = $case->applicationID;
                    $applicationName = $this->applicationList[$applicationID];
                }

                $case->applicationID = $applicationName . "(#{$applicationID})";
                $case->stepDesc      = '';
                $case->stepExpect    = '';
                $case->real          = '';

                $result = isset($results[$case->id]) ? $results[$case->id] : array();
                if(!empty($result))
                {
                    $firstStep  = reset($result);
                    $case->real = $firstStep['real'];
                }

                if(isset($relatedSteps[$case->id]))
                {
                    $i = $childId = 0;
                    foreach($relatedSteps[$case->id] as $step)
                    {
                        $stepId = 0;
                        if($step->type == 'group' or $step->type == 'step')
                        {
                            $i++;
                            $childId = 0;
                            $stepId  = $i;
                        }
                        else
                        {
                            $stepId = $i . '.' . $childId;
                        }

                        if($step->version != $case->version) continue;
                        $sign = (in_array($this->post->fileType, array('html', 'xml'))) ? '<br />' : "\n";

                        $case->stepDesc   .= $stepId . ". " . $step->desc . $sign;
                        $case->stepExpect .= $stepId . ". " . $step->expect . $sign;
                        $case->real       .= $stepId . ". " . (isset($result[$step->id]) ? $result[$step->id]['real'] : '') . $sign;
                        $childId ++;
                    }
                }
                $case->stepDesc     = trim($case->stepDesc);
                $case->stepExpect   = trim($case->stepExpect);
                $case->real         = trim($case->real);
                $case->precondition = htmlspecialchars_decode($case->precondition, ENT_QUOTES);

                if($this->post->fileType == 'csv')
                {
                    $case->stepDesc   = str_replace('"', '""', $case->stepDesc);
                    $case->stepExpect = str_replace('"', '""', $case->stepExpect);
                }

                /* fill some field with useful value. */
                $case->project    = !isset($projects[$case->project])     ? '' : $projects[$case->project] . "(#$case->project)";
                $case->execution  = !isset($executions[$case->execution]) ? '' : $executions[$case->execution] . "(#$case->execution)";
                $case->product    = !isset($products[$case->product])     ? '' : $products[$case->product] . "(#$case->product)";
                $case->module     = !isset($relatedModules[$case->module])? '' : $relatedModules[$case->module] . "(#$case->module)";
                $case->story      = !isset($relatedStories[$case->story]) ? '' : $relatedStories[$case->story] . "(#$case->story)";
                $case->categories = $this->testcase->getCategoriesValueByKeys($case->categories);

                $case->pri           = zget($caseLang->priList, $case->pri, '');
                $case->type          = zget($caseLang->typeList, $case->type, '');
                $case->openedBy      = zget($users, $case->openedBy, '');
                $case->lastEditedBy  = zget($users, $case->lastEditedBy, '');
                $case->lastRunResult = zget($caseLang->resultList, $case->lastRunResult, '');
                $case->status        = $this->processStatus('testcase', $case);

                if($taskID)
                {
                    if(isset($users[$case->lastRunner])) $case->lastRunner = $users[$case->lastRunner];
                    if(isset($users[$case->assignedTo])) $case->assignedTo = $users[$case->assignedTo];
                }

                $case->bugsAB       = $case->bugs;
                $case->resultsAB    = $case->results;
                $case->stepNumberAB = $case->stepNumber;

                unset($case->bugs);
                unset($case->results);
                unset($case->stepNumber);
                unset($case->caseFails);

                $case->stage = explode(',', $case->stage);
                foreach($case->stage as $key => $stage) $case->stage[$key] = isset($caseLang->stageList[$stage]) ? $caseLang->stageList[$stage] : $stage;
                $case->stage = join("\n", $case->stage);

                $case->openedDate     = substr($case->openedDate, 0, 10);
                $case->lastEditedDate = substr($case->lastEditedDate, 0, 10);

                if($case->linkCase)
                {
                    $tmpLinkCases   = array();
                    $linkCaseIdList = explode(',', $case->linkCase);
                    foreach($linkCaseIdList as $linkCaseID)
                    {
                        $linkCaseID     = trim($linkCaseID);
                        $tmpLinkCases[] = isset($relatedCases[$linkCaseID]) ? $relatedCases[$linkCaseID] . "(#$linkCaseID)" : $linkCaseID;
                    }
                    $case->linkCase = join("; \n", $tmpLinkCases);
                }

                /* Set related files. */
                $case->files = '';
                if(isset($relatedFiles[$case->id]))
                {
                    foreach($relatedFiles[$case->id] as $file)
                    {
                        $fileURL = common::getSysURL() . $this->createLink('file', 'download', "fileID={$file->id}");
                        $case->files .= html::a($fileURL, $file->title, '_blank') . '<br />';
                    }
                }

                $results = $this->dao->select('id,caseResult')->from(TABLE_TESTRESULT)->where('`case`')->eq($case->id)->orderBy('id_desc')->limit(10)->fetchPairs();
                ksort($results);
                $index   = 1;
                foreach($results as $id => $result)
                {
                    $case->{'result' . $index} = zget($this->lang->testcase->resultList, $result, $result);
                    $index ++;
                }
            }
            if(isset($this->config->bizVersion)) list($fields, $cases) = $this->loadModel('workflowfield')->appendDataFromFlow($fields, $cases);

            $width['applicationID'] = 30;
            $width['project']       = 30;

            $this->post->set('width',  $width);
            $this->post->set('fields', $fields);
            $this->post->set('rows',   $cases);
            $this->post->set('kind',   'testcase');

            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        $title    = '';
        $fileName = $this->lang->testcase->common;

        if($projectID)
        {
            $projectName  = $this->dao->findById($projectID)->from(TABLE_PROJECT)->fetch('name');
            $title       .= $projectName;

            $applicationName = '';
            $productName     = '';
            if($applicationID)
            {
                $applicationName  = $this->dao->findById($applicationID)->from(TABLE_APPLICATION)->fetch('name');
                $title           .= $this->lang->dash . $applicationName;
            }

            if($productID != 'all')
            {
                if($productID)
                {
                    $productName  = $this->dao->findById($productID)->from(TABLE_PRODUCT)->fetch('name');
                    $title       .= $this->lang->dash . $productName;
                }
                else
                {
                    $title .= $this->lang->dash . $this->lang->naProduct;
                }
            }
        }
        else
        {
            $products        = $this->rebirth->getProductPairs($applicationID);
            $productID       = $this->rebirth->getProductIdByApplication($applicationID, $productID);
            $applicationName = $this->applicationList[$applicationID];
            $productName     = zget($products, $productID, '');

            $title .= $applicationName . $this->lang->dash . $productName;
        }

        $browseType = isset($this->lang->testcase->featureBar['browse'][$browseType]) ? $this->lang->testcase->featureBar['browse'][$browseType] : '';

        if($taskID) $taskName = $this->dao->findById($taskID)->from(TABLE_TESTTASK)->fetch('name');

        $this->view->fileName        = $title . $this->lang->dash . ($taskID ? $taskName . $this->lang->dash : '') . $browseType . $fileName;
        $this->view->allExportFields = $this->config->testcase->exportFields;
        $this->view->customExport    = true;
        $this->display();
    }

    /**
     * Import case from lib.
     *
     * @param  int    $productID
     * @param  int    $branch
     * @param  int    $libID
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function importFromLib($applicationID, $productID, $projectID, $branch = 0, $moduleID = 0, $libID = 0, $orderBy = 'id_desc', $browseType = '', $queryID = 0, $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        /* Get product, then set menu. */
        $applicationID = $this->rebirth->saveState($this->applicationList, $applicationID, $productID);
        $this->rebirth->setMenu($applicationID, $productID);
        $productID = $this->rebirth->getProductIdByApplication($applicationID, $productID);

        $browseType = strtolower($browseType);
        $queryID    = (int)$queryID;

        if($_POST)
        {
            $this->testcase->importFromLib($applicationID, $productID, $projectID);
            die(js::reload('parent'));
        }

        $libraries = $this->loadModel('caselib')->getLibraries();
        if(empty($libraries))
        {
            echo js::alert($this->lang->testcase->noLibrary);
            die(js::locate($this->session->caseList));
        }
        if(empty($libID) or !isset($libraries[$libID])) $libID = key($libraries);

        /* Build the search form. */
        $actionURL = $this->createLink('testcase', 'importFromLib', "applicationID=$applicationID&productID=$productID&projectID=$projectID&branch=$branch&moduleID=$moduleID&libID=$libID&orderBy=$orderBy&browseType=bySearch&queryID=myQueryID");

        $this->config->testcase->search['module']    = 'testsuite';
        $this->config->testcase->search['onMenuBar'] = 'no';
        $this->config->testcase->search['actionURL'] = $actionURL;
        $this->config->testcase->search['queryID']   = $queryID;
        $this->config->testcase->search['fields']['lib'] = $this->lang->testcase->lib;
        $this->config->testcase->search['params']['lib'] = array('operator' => '=', 'control' => 'select', 'values' => array('' => '', $libID => $libraries[$libID], 'all' => $this->lang->caselib->all));
        $this->config->testcase->search['params']['module']['values']  = $this->loadModel('tree')->getOptionMenu($libID, $viewType = 'caselib');
        if(!$this->config->testcase->needReview) unset($this->config->testcase->search['params']['status']['values']['wait']);
        unset($this->config->testcase->search['fields']['product']);
        unset($this->config->testcase->search['fields']['branch']);
        $this->loadModel('search')->setSearchParams($this->config->testcase->search);

        $modules = array();
        if(is_numeric($productID)) $modules = $this->loadModel('tree')->getOptionMenu($productID, 'case', 0, $branch);

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = pager::init(0, $recPerPage, $pageID);
        $this->view->title         = $this->lang->testcase->common . $this->lang->colon . $this->lang->testcase->importFromLib;
        $this->view->libraries     = $libraries;
        $this->view->libID         = $libID;
        $this->view->applicationID = $applicationID;
        $this->view->productID     = $productID;
        $this->view->projectID     = $projectID;
        $this->view->branch        = $branch;
        $this->view->cases         = $this->loadModel('testsuite')->getNotImportedCases($productID, $libID, $orderBy, $pager, $browseType, $queryID);
        $this->view->modules       = $modules;
        $this->view->moduleID      = $moduleID;
        $this->view->libModules    = $this->tree->getOptionMenu($libID, 'caselib');
        $this->view->pager         = $pager;
        $this->view->orderBy       = $orderBy;
        $this->view->branches      = array();
        $this->view->browseType    = $browseType;
        $this->view->queryID       = $queryID;
        $this->display();
    }

    /**
     * Confirm testcase changed.
     *
     * @param  int    $caseID
     * @param  int    $taskID
     * @param  string $from
     * @access public
     * @return void
     */
    public function confirmChange($caseID, $taskID = 0, $from = 'view')
    {
        $case = $this->testcase->getById($caseID);

        $this->dao->update(TABLE_TESTRUN)
            ->set('version')->eq($case->version)
            ->set('precondition = null')
            ->where('`case`')->eq($caseID)
            ->exec();
        $this->loadModel('action')->create('case', $caseID, 'confirmChange');

        if($from == 'view') die(js::locate(inlink('view', "caseID=$caseID&version=$case->version&from=testtask&taskID=$taskID"), 'parent'));
        die(js::reload('parent'));
    }

    /**
     * Confirm libcase changed.
     *
     * @param  int    $caseID
     * @param  int    $libcaseID
     * @param  string $from
     * @access public
     * @return void
     */
    public function confirmLibcaseChange($caseID, $libcaseID, $from = 'view')
    {
        $case = $this->testcase->getById($caseID);

        if(empty($libcaseID)) $libcaseID = $case->fromCaseID;
        if(empty($libcaseID) && $from == 'control') return;
        if($case->fromCaseVersion == $case->version && $from == 'control') return;

        $libCase = $this->testcase->getById($libcaseID);

        $version = $case->version + 1;
        $this->dao->update(TABLE_CASE)
        ->set('version')->eq($version)
        ->set('fromCaseVersion')->eq($version)
        ->set('precondition')->eq($libCase->precondition)
        ->where('id')->eq($caseID)->exec();

        $this->dao->update(TABLE_TESTRUN)
        ->set('precondition')->eq($case->precondition)
        ->where('`case`')->eq($caseID)
        ->andWhere('precondition')->isNull()
        ->exec();

        foreach($libCase->steps as $step)
        {
            unset($step->id);
            $step->case    = $caseID;
            $step->version = $version;
            $this->dao->insert(TABLE_CASESTEP)->data($step)->exec();
        }

        if($from == 'control') return;
        if($from == 'view')    die(js::locate($this->createLink('testcase', 'view', "caseID=$caseID&version=$version"), 'parent'));
        die(js::reload('parent'));
    }


    /**
     * Link related cases.
     *
     * @param  int    $caseID
     * @param  string $browseType
     * @param  int    $param
     * @access public
     * @return void
     */
    public function linkCases($caseID, $browseType = '', $param = 0)
    {
        /* Get case and queryID. */
        $case    = $this->testcase->getById($caseID);
        $queryID = ($browseType == 'bySearch') ? (int)$param : 0;

        if($case->product == 0) $case->product = 'na';
        $this->rebirth->setMenu($case->applicationID, $case->product);
        $productID = $this->rebirth->getProductIdByApplication($case->applicationID, $case->product);
        $products  = $this->rebirth->getProductPairs($case->applicationID, true);

        /* Build the search form. */
        $actionURL = $this->createLink('testcase', 'linkCases', "caseID=$caseID&browseType=bySearch&queryID=myQueryID", '', true);
        $this->testcase->buildSearchForm($queryID, $actionURL, $case->applicationID, $productID);

        /* Get cases to link. */
        $cases2Link = $this->testcase->getCases2Link($caseID, $browseType, $queryID);

        /* Assign. */
        $this->view->title      = $case->title . $this->lang->colon . $this->lang->testcase->linkCases;
        $this->view->case       = $case;
        $this->view->cases2Link = $cases2Link;
        $this->view->users      = $this->loadModel('user')->getPairs('noletter');
        $this->view->products   = array('0' => $this->lang->naProduct) + $products;

        $this->display();
    }

    /**
     * Ignore libcase changed.
     *
     * @param  int    $caseID
     * @access public
     * @return void
     */
    public function ignoreLibcaseChange($caseID)
    {
        $case = $this->testcase->getById($caseID);
        $this->dao->update(TABLE_CASE)->set('fromCaseVersion')->eq($case->version)->where('id')->eq($caseID)->exec();
        die(js::reload('parent'));
    }

    /**
     * Create bug.
     *
     * @param  int    $productID
     * @param  string $extras
     * @access public
     * @return void
     */
    public function createBug($applicationID, $productID, $branch = 0, $extras = '')
    {
        $extras = str_replace(array(',', ' '), array('&', ''), $extras);
        parse_str($extras, $params);
        extract($params);

        $this->loadModel('testtask');
        $case = '';
        if($runID)
        {
            $case = $this->testtask->getRunById($runID)->case;
        }
        elseif($caseID)
        {
            $case = $this->testcase->getById($caseID);
        }

        if(!$case) die(js::error($this->lang->notFound) . js::locate('back', 'parent'));

        $this->view->title         = $this->applicationList[$applicationID] . $this->lang->colon . $this->lang->testcase->createBug;
        $this->view->runID         = $runID;
        $this->view->case          = $case;
        $this->view->caseID        = $caseID;
        $this->view->version       = $version;
        $this->view->applicationID = $applicationID;
        $this->view->productID     = $productID;
        $this->display();
    }


    /**
     * Case bugs.
     *
     * @param  int    $runID
     * @param  int    $caseID
     * @param  int    $version
     * @access public
     * @return void
     */
    public function bugs($runID, $caseID = 0, $version = 0)
    {
        $this->view->title = $this->lang->testcase->bugs;
        $this->view->bugs  = $this->loadModel('bug')->getCaseBugs($runID, $caseID, $version);
        $this->view->users = $this->loadModel('user')->getPairs('noletter');
        $this->display();
    }

    /**
     * Group case.
     *
     * @param  int    $productID
     * @param  string $groupBy
     * @access public
     * @return void
     */
    public function groupCase($applicationID, $productID = 0, $branch = '', $groupBy = 'story')
    {
        $this->app->loadLang('testtask');
        $this->app->loadLang('execution');
        $this->app->loadLang('task');
        $groupBy = empty($groupBy) ? 'story' : $groupBy;

        $applicationID = $this->rebirth->saveState($this->applicationList, $applicationID, $productID);
        if($this->app->openApp == 'project')
        {
            $this->loadModel('project')->setMenu($this->session->project);
            $this->lang->modulePageNav = $this->rebirth->selectProduct($this->session->project, $applicationID, $productID, 'testcase');
        }
        else
        {
            $this->rebirth->setMenu($applicationID, $productID);
            $productID = $this->rebirth->getProductIdByApplication($applicationID, $productID);
        }

        $this->session->set('caseList', $this->app->getURI(true), $this->app->openApp);

        $productIdList = $this->rebirth->getProductIdList($applicationID, $productID);
        $cases = $this->testcase->getModuleCases($applicationID, $productIdList, $branch, 0, $groupBy);
        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'testcase', false);
        $cases = $this->loadModel('story')->checkNeedConfirm($cases);
        $cases = $this->testcase->appendData($cases);

        $groupCases  = array();
        $groupByList = array();
        foreach($cases as $case)
        {
            if($groupBy == 'story')
            {
                $groupCases[$case->story][] = $case;
                $groupByList[$case->story]  = $case->storyTitle;
            }
        }

        $this->view->title         = $this->applicationList[$applicationID] . $this->lang->colon . $this->lang->testcase->common;
        $this->view->applicationID = $applicationID;
        $this->view->productID     = $productID;
        $this->view->users         = $this->user->getPairs('noletter');
        $this->view->browseType    = 'group';
        $this->view->groupBy       = $groupBy;
        $this->view->orderBy       = $groupBy;
        $this->view->groupByList   = $groupByList;
        $this->view->cases         = $groupCases;
        $this->view->suiteList     = $this->loadModel('testsuite')->getSuites($applicationID, $productIdList);
        $this->view->suiteID       = 0;
        $this->view->moduleID      = 0;
        $this->view->branch        = $branch;
        $this->display();
    }

    /**
     * Show zero case story.
     *
     * @param  int    $productID
     * @param  int    $branchID
     * @param  string $orderBy
     * @access public
     * @return void
     */
    public function zeroCase($applicationID, $productID = 0, $branchID = 0, $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $this->session->set('storyList', $this->app->getURI(true) . '#app=' . $this->app->openApp, 'product');
        $this->session->set('caseList', $this->app->getURI(true), $this->app->openApp);

        $this->loadModel('story');
        $applicationID = $this->rebirth->saveState($this->applicationList, $applicationID, $productID);
        if($this->app->openApp == 'project')
        {
            $this->loadModel('project')->setMenu($this->session->project);
            $this->lang->modulePageNav = $this->rebirth->selectProduct($this->session->project, $applicationID, $productID, 'testcase');
        }
        else
        {
            $this->rebirth->setMenu($applicationID, $productID);
            $productID = $this->rebirth->getProductIdByApplication($applicationID, $productID);
        }

        /* Append id for secend sort. */
        $sort    = $this->loadModel('common')->appendOrder($orderBy);
        $stories = $this->story->getZeroCase($productID, $branchID, $sort);

        /* Pager. */
        $this->app->loadClass('pager', $static = true);
        $recTotal = count($stories);
        $pager    = new pager($recTotal, $recPerPage, $pageID);
        $stories  = array_chunk($stories, $pager->recPerPage);

        $this->view->title         = $this->lang->story->zeroCase;
        $this->view->stories       = empty($stories) ? $stories : $stories[$pageID - 1];
        $this->view->users         = $this->user->getPairs('noletter');
        $this->view->applicationID = $applicationID;
        $this->view->productID     = $productID;
        $this->view->projectID     = 0;
        $this->view->branchID      = $branchID;
        $this->view->orderBy       = $orderBy;
        $this->view->suiteList     = $this->loadModel('testsuite')->getSuites($applicationID, $productID);
        $this->view->browseType    = '';
        $this->view->pager         = $pager;
        $this->display();
    }

    /**
     * Export case getModuleByStory
     *
     * @params int $storyID
     * @return void
     */
    public function ajaxGetStoryModule($storyID)
    {
        $story = $this->dao->select('module')->from(TABLE_STORY)->where('id')->eq($storyID)->fetch();
        $moduleID = !empty($story) ? $story->module : 0;
        die(json_encode(array('moduleID'=> $moduleID)));
    }

    /**
     * Get status by ajax.
     *
     * @param  string $methodName
     * @param  int    $caseID
     * @access public
     * @return void
     */
    public function ajaxGetStatus($methodName, $caseID = 0)
    {
        $case   = $this->testcase->getByID($caseID);
        $status = $this->testcase->getStatus($methodName, $case);
        if($methodName == 'update') $status = zget($status, 1, '');
        die($status);
    }

    public function ajaxProductProjectCases($applicationID, $productID, $projectID = 0, $caseID = 0)
    {
        $cases  = array('0' => '');

        /* 将已经关联的用例加在数组中。*/
        if($caseID)
        {
            $case   = $this->dao->select('id,title')->from(TABLE_CASE)->where('id')->eq($caseID)->fetchPairs();
            $cases += $case;
        }

        $cases += $this->testcase->getProductProjectCases($applicationID, $productID, $projectID, 'id_desc', 'normal');
        die(html::select('case', $cases, $caseID, 'class="form-control"'));
    }

    /**
     * Export xmind.
     *
     * @param  int $applicationID
     * @param  int $productID
     * @param  int $moduleID
     * @param  int $branch
     * @access public
     * @return void
     */
    public function exportFreemind($applicationID, $productID, $moduleID = 0, $branch = 0)
    {
        if($_POST)
        {
            $this->classXmind = $this->app->loadClass('xmind');
            if (isset($_POST['imodule'])) $imoduleID = $_POST['imodule'];

            $configResult = $this->testcase->saveXmindConfig();
            if($configResult['result'] == 'fail') return print(js::alert($configResult['message']));

            $projectID = $this->app->openApp == 'project' ? $this->session->projectID : 0;
            $context   = $this->testcase->getXmindExport($productID, $imoduleID, $branch, $projectID);

            $xmlDoc = new DOMDocument('1.0', 'UTF-8');
            $xmlDoc->formatOutput = true;

            $versionAttr      = $xmlDoc->createAttribute('version');
            $versionAttrValue = $xmlDoc->createTextNode('1.0.1');
            $versionAttr->appendChild($versionAttrValue);

            $mapNode = $xmlDoc->createElement('map');
            $mapNode->appendChild($versionAttr);
            $xmlDoc->appendChild($mapNode);

            $productName = '';
            if(count($context['caseList']))
            {
                $firstCase   = array_shift($context['caseList']);
                $productName = $firstCase->productName;
            }
            else
            {
                $product     = $this->product->getById($productID);
                $productName = $product->name;
            }

            $productNode   = $xmlDoc->createElement('node');
            $textAttr      = $xmlDoc->createAttribute('TEXT');
            $textAttrValue = $xmlDoc->createTextNode($this->classXmind->toText("$productName", $productID));

            $textAttr->appendChild($textAttrValue);
            $productNode->appendChild($textAttr);
            $mapNode->appendChild($productNode);

            $sceneNodes  = array();
            $moduleNodes = array();

            $this->classXmind->createModuleNode($xmlDoc, $context, $productNode, $moduleNodes);
            $this->classXmind->createTestcaseNode($xmlDoc, $context, $productNode, $moduleNodes, $sceneNodes);

            $xmlStr = $xmlDoc->saveXML();
            $this->fetch('file', 'sendDownHeader', array('fileName' => $productName, 'mm', $xmlStr));
        }

        $tree    = $moduleID ? $this->tree->getByID($moduleID) : '';
        $product = $this->product->getById($productID);
        $config  = $this->testcase->getXmindConfig();

        $this->view->settings         = $config;
        $this->view->moduleName       = $tree != '' ? $tree->name : '/';
        $this->view->productName      = $product->name;
        $this->view->moduleID         = $moduleID;
        $this->view->moduleOptionMenu = $this->tree->getOptionMenu($productID, $viewType = 'case', $startModuleID = 0, ($branch === 'all' or !isset($branches[$branch])) ? 0 : $branch);

        $this->display();
    }

    /**
     * Export xmind.
     *
     * @param  int $applicationID
     * @param  int $productID
     * @param  int $moduleID
     * @param  int $branch
     * @access public
     * @return void
     */
    public function exportXMind($applicationID, $productID, $moduleID = 0, $branch = 0)
    {
        if($_POST)
        {
            $this->classXmind = $this->app->loadClass('mind');
            if (isset($_POST['imodule'])) $imoduleID = $_POST['imodule'];

            $configResult = $this->testcase->saveXmindConfig();
            if($configResult['result'] == 'fail') return print(js::alert($configResult['message']));

            $projectID = $this->app->openApp == 'project' ? $this->session->projectID : 0;
            $context   = $this->testcase->getXmindExport($productID, $imoduleID, $branch, $projectID);

            $productName = '';
            if(count($context['caseList']))
            {
                $firstCase   = current($context['caseList']);
                $productName = $firstCase->productName;
            }
            else
            {
                $product     = $this->product->getById($productID);
                $productName = $product->name;
            }

            $this->loadModel('file');
            $savePath = $this->file->savePath . 'xmind/';
            $filePath = $savePath . time() . '.xmind';
            $fileName = $productName . '.xmind';

            $sceneNodes  = array();
            $moduleNodes = array();

            $this->classXmind->createModuleNode($context, $moduleNodes);

            $mindData = $this->classXmind->createTestcaseNode($context, $moduleNodes, $sceneNodes);
            $mindData = $this->classXmind->mergeNodeData($mindData);
            $mindBody = $this->classXmind->createMindBody($productName . "[$productID]", 'product' . $productID);

            $this->classXmind->createMindContent($filePath, $mindData, $mindBody, 'testcase');
            $this->classXmind->export($filePath);

            $this->fetch('file', 'sendDownHeader', array('fileName' => $fileName, 'fileType' => '', 'content' => $filePath, 'type' => 'file'));
        }

        $tree    = $moduleID ? $this->tree->getByID($moduleID) : '';
        $product = $this->product->getById($productID);
        $config  = $this->testcase->getXmindConfig();

        $this->view->settings         = $config;
        $this->view->moduleName       = $tree != '' ? $tree->name : '/';
        $this->view->productName      = $product->name;
        $this->view->moduleID         = $moduleID;
        $this->view->moduleOptionMenu = $this->tree->getOptionMenu($productID, $viewType = 'case', $startModuleID = 0, $branch);

        $this->display();
    }

    /**
     * Get xmind config.
     *
     * @access public
     * @return void
     */
    public function getXmindConfig()
    {
        $result = $this->testcase->getXmindConfig();
        $this->send($result);
    }

    /**
     * Import xmind.
     *
     * @param  int $applicationID
     * @param  int $productID
     * @param  int $branch
     * @access public
     * @return void
     */
    public function importXmind($applicationID, $productID, $branch)
    {
        if($_FILES)
        {
            $this->classXmind = $this->app->loadClass('xmind');
            if($_FILES['file']['size'] == 0) return print(js::alert($this->lang->testcase->errorFileNotEmpty));

            $configResult = $this->testcase->saveXmindConfig();
            if($configResult['result'] == 'fail') return print(js::alert($configResult['message']));

            $tmpName  = $_FILES['file']['tmp_name'];
            $fileName = $_FILES['file']['name'];
            $extName  = trim(strtolower(pathinfo($fileName, PATHINFO_EXTENSION)));
            if($extName != 'xmind') return print(js::alert($this->lang->testcase->errorFileFormat));

            $newPureName  = $this->app->user->id."-xmind";
            $importFolder = $this->app->getTmpRoot() . "import";
            if(!is_dir($importFolder)) mkdir($importFolder, 0755, true);

            $dest = $this->app->getTmpRoot() . "import/".$newPureName.$extName;
            if(!move_uploaded_file($tmpName, $dest)) return print(js::alert($this->lang->testcase->errorXmindUpload));

            $extractFolder   = $this->app->getTmpRoot() . "import/".$newPureName;
            $this->classFile = $this->app->loadClass('zfile');
            if(is_dir($extractFolder)) $this->classFile->removeDir($extractFolder);

            $this->app->loadClass('pclzip', true);
            $zip = new pclzip($dest);

            if($zip->extract(PCLZIP_OPT_PATH, $extractFolder) == 0)
            {
                return print(js::alert($this->lang->testcase->errorXmindUpload));
            }

            $this->classFile->removeFile($dest);

            $jsonPath = $extractFolder."/content.json";
            if(file_exists($jsonPath) == true)
            {

                $fetchResult = $this->fetchByJSON($extractFolder, $productID, $branch);
            }
            else
            {
                $fetchResult = $this->fetchByXML($extractFolder, $productID, $branch);
            }

            if($fetchResult['result'] == 'fail')
            {
                return print(js::alert($fetchResult['message']));
            }

            $this->session->set('xmindImport', $extractFolder);
            $this->session->set('xmindImportType', $fetchResult['type']);

            $pId = $fetchResult['pId'];

            return print(js::locate($this->createLink('testcase', 'showXmindImport', "applicationID=$applicationID&productID=$pId&branch=$branch"), 'parent.parent'));
        }

        $config = $this->testcase->getXmindConfig();

        $this->view->settings = $config;

        $this->display();
    }

    /**
     * Show imported xmind.
     *
     * @param  int $applicationID
     * @param  int $productID
     * @param  int $branch
     * @access public
     * @return void
     */
    public function showXmindImport($applicationID, $productID,$branch)
    {
        if(!commonModel::hasPriv("testcase", "importXmind")) $this->loadModel('common')->deny('testcase', 'importXmind');
        $product  = $this->product->getById($productID);
        $branches = (isset($product->type) and $product->type != 'normal') ? $this->loadModel('branch')->getPairs($productID, 'active') : array();
        $config   = $this->testcase->getXmindConfig();

        if($this->app->openApp == 'project')
        {
            $projectID = $this->session->project;
            $this->loadModel('project')->setMenu($projectID);
        }
        else
        {
            $this->rebirth->setMenu($applicationID, $productID);
        }

        $jsLng = array();
        $jsLng['caseNotExist'] = $this->lang->testcase->caseNotExist;
        $jsLng['saveFail']     = $this->lang->testcase->saveFail;
        $jsLng['set2Scene']    = $this->lang->testcase->set2Scene;
        $jsLng['set2Testcase'] = $this->lang->testcase->set2Testcase;
        $jsLng['clearSetting'] = $this->lang->testcase->clearSetting;
        $jsLng['setModule']    = $this->lang->testcase->setModule;
        $jsLng['pickModule']   = $this->lang->testcase->pickModule;
        $jsLng['clearBefore']  = $this->lang->testcase->clearBefore;
        $jsLng['clearAfter']   = $this->lang->testcase->clearAfter;
        $jsLng['clearCurrent'] = $this->lang->testcase->clearCurrent;
        $jsLng['removeGroup']  = $this->lang->testcase->removeGroup;
        $jsLng['set2Group']    = $this->lang->testcase->set2Group;

        $this->view->title            = $this->lang->testcase->xmindImport;
        $this->view->settings         = $config;
        $this->view->applicationID    = $applicationID;
        $this->view->productID        = $productID;
        $this->view->projectID        = $this->session->project ? $this->session->project : 0;
        $this->view->branch           = $branch;
        $this->view->product          = $product;
        $this->view->moduleOptionMenu = $this->tree->getOptionMenu($productID, $viewType = 'case', $startModuleID = 0, ($branch === 'all' or !isset($branches[$branch])) ? 0 : $branch);
        $this->view->gobackLink       = $this->createLink('testcase', 'browse', "productID=$productID");
        $this->view->jsLng            = $jsLng;

        $this->display();
    }

    /**
     * Fetch by xml.
     *
     * @param  string $extractFolder
     * @param  int    $productID
     * @param  int    $branch
     * @access public
     * @return void
     */
    function fetchByXML($extractFolder, $productID, $branch)
    {
        $filePath = $extractFolder."/content.xml";
        $xmlNode = simplexml_load_file($filePath);
        $title = $xmlNode->sheet->topic->title;
        if(strlen($title) == 0)
        {
            return array('result'=>'fail','message'=>$this->lang->testcase->errorXmindUpload);
        }

        $pId = $productID;
        if($this->classXmind->endsWith($title,"]") == true)
        {
            $tmpId = $this->classXmind->getBetween($title,"[","]");
            if(empty($tmpId) == false)
            {
                $projectCount = $this->dao->select('count(*) as count')
                    ->from(TABLE_PRODUCT)
                    ->where('id')
                    ->eq((int)$tmpId)
                    ->andWhere('deleted')->eq('0')
                    ->fetch('count');

                if((int)$projectCount == 0) return array('result'=>'fail','message'=>$this->lang->testcase->errorImportBadProduct);

                $pId = $tmpId;
            }
        }

        return array('result'=>'success','pId'=>$pId, 'type'=>'xml');
    }

    /**
     * Fetch by json.
     *
     * @param  string $extractFolder
     * @param  int    $productID
     * @param  int    $branch
     * @access public
     * @return void
     */
    function fetchByJSON($extractFolder, $productID, $branch)
    {
        $filePath = $extractFolder."/content.json";
        $jsonStr = file_get_contents($filePath);
        $jsonDatas = json_decode($jsonStr, true);
        $title = $jsonDatas[0]['rootTopic']['title'];
        if(strlen($title) == 0)
        {
            return array('result'=>'fail','message'=>$this->lang->testcase->errorXmindUpload);
        }

        $pId = $productID;
        if($this->classXmind->endsWith($title,"]") == true)
        {
            $tmpId = $this->classXmind->getBetween($title,"[","]");
            if(empty($tmpId) == false)
            {
                $projectCount = $this->dao->select('count(*) as count')
                    ->from(TABLE_PRODUCT)
                    ->where('id')
                    ->eq((int)$tmpId)
                    ->andWhere('deleted')->eq('0')
                    ->fetch('count');

                if((int)$projectCount == 0) return array('result'=>'fail','message'=>$this->lang->testcase->errorImportBadProduct);

                $pId = $tmpId;
            }
        }

        return array('result'=>'success','pId'=>$pId,'type'=>'json');
    }

    /**
     * Get xmind content.
     *
     * @access public
     * @return void
     */
    public function ajaxGetXmindImport()
    {
        if(!commonModel::hasPriv("testcase", "importXmind")) $this->loadModel('common')->deny('testcase', 'importXmind');
        $folder = $this->session->xmindImport;
        $type   = $this->session->xmindImportType;

        if($type == 'xml')
        {
            $xmlPath = "$folder/content.xml";
            $results = $this->testcase->getXmindImport($xmlPath);

            echo $results;
        }
        else
        {
            $jsonPath = "$folder/content.json";
            $jsonStr = file_get_contents($jsonPath);

            echo $jsonStr;
        }
    }

    /**
     * Save imported xmind.
     *
     * @access public
     * @return void
     */
    public function saveXmindImport($applicationID = 0)
    {
        if(!commonModel::hasPriv("testcase", "importXmind")) $this->loadModel('common')->deny('testcase', 'importXmind');
        if(!empty($_POST))
        {
            $result = $this->testcase->saveXmindImport($applicationID);
            return $this->send($result);
        }

        $this->send(array('result' => 'fail', 'message' => $this->lang->testcase->errorSaveXmind));
    }
}
