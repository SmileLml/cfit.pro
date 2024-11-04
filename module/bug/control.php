<?php
/**
 * The control file of bug currentModule of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     bug
 * @version     $Id: control.php 5107 2013-07-12 01:46:12Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
class bug extends control
{
    /**
     * All application.
     *
     * @var    array
     * @access public
     */
    public $applicationList = array();

    /**
     * All products.
     *
     * @var    array
     * @access public
     */
    public $products = array();
    /**
     * Project id.
     *
     * @var    int
     * @access public
     */
    public $projectID = 0;

    /**
     * Construct function, load some modules auto.
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
        $this->loadModel('product');
        $this->loadModel('tree');
        $this->loadModel('user');
        $this->loadModel('action');
        $this->loadModel('story');
        $this->loadModel('task');
        $this->loadModel('qa');

        $objectID        = 0;
        $applicationList = $this->rebirth->getApplicationPairs();

        $this->view->applicationList = $this->applicationList = $applicationList;

        if(empty($applicationList) and !helper::isAjaxRequest()) die($this->locate($this->createLink('application', 'create')));
    }

    /**
     * The index page, locate to browse.
     *
     * @access public
     * @return void
     */
    public function index()
    {
        $this->locate($this->createLink('bug', 'browse'));
    }

    /**
     * Browse bugs.
     *
     * @param  int    $applicationID
     * @param  int    $productID
     * @param  string $branch
     * @param  string $browseType
     * @param  int    $param
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function browse($applicationID = 0, $productID = 'all', $branch = '', $browseType = 'unclosed', $param = 0, $orderBy = '', $recTotal = 0, $recPerPage = 20, $pageID = 1,$taskID = 0)
    {
        $taskID = null;
        $tempApplicationID = $applicationID;
        $bugBrowseMode = '';
        if(strpos($applicationID,'testtask') === 0)
        {
            $this->session->tempApplicationID = $applicationID;

            $taskID = substr($applicationID, 8);

            $task = $this->loadModel('testtask')->getById($taskID);

            $bugBrowseMode = 'testtaskbug';
            $applicationID = $task->applicationID;
            $productID     = $task->product;

            unset($this->config->bug->search['fields']['product']);
            unset($this->config->bug->search['fields']['linkTesttask']);
        }

        $this->loadModel('datatable');

        if(empty($productID)) $productID = 'na';
        $applicationID = $this->rebirth->saveState($this->applicationList, $applicationID, $productID);
        $application   = $this->rebirth->getApplicationByID($applicationID);
        $productID     = $this->rebirth->getProductIdByApplication($applicationID, $productID);
        $productIdList = $this->rebirth->getProductIdList($applicationID, $productID);
        $products      = $this->rebirth->getProductPairs($applicationID, true);

        if($this->app->openApp == 'project')
        {
            $this->loadModel('project')->setMenu($task->project);
        }
        else
        {
            $this->rebirth->setMenu($applicationID, $productID, 0, $bugBrowseMode);
        }

        $testtasks = $this->loadModel('testtask')->getPairs(0, 0, '', '', "oddNumber", $applicationID);
        $this->config->bug->search['params']['linkTesttask']['values'] = $testtasks;

        /* Set browse type. */
        $browseType = strtolower($browseType);

        setcookie('preProductID', $productID, $this->config->cookieLife, $this->config->webRoot, '', $this->config->cookieSecure, true);

        if($this->cookie->preProductID != $productID or $browseType == 'bybranch')
        {
            $_COOKIE['bugModule'] = 0;
            setcookie('bugModule', 0, 0, $this->config->webRoot, '', $this->config->cookieSecure, false);
        }
        if($browseType == 'bymodule' or $browseType == '')
        {
            setcookie('bugModule', (int)$param, 0, $this->config->webRoot, '', $this->config->cookieSecure, false);
        }
        if($browseType != 'bymodule') $this->session->set('bugBrowseType', $browseType);

        $moduleID = ($browseType == 'bymodule') ? (int)$param : (($browseType == 'bysearch') ? 0 : ($this->cookie->bugModule ? $this->cookie->bugModule : 0));
        $queryID  = ($browseType == 'bysearch') ? (int)$param : 0;

        /* 获取固定排序字段。 */
        if(isset($this->config->bug->browse->fixedSort)) $orderBy = $this->config->bug->browse->fixedSort;

        /* Set session. */
        $this->session->set('bugList', $this->app->getURI(true) . '#app=' . $this->app->openApp, $this->app->openApp);

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        /* Get bugs. */
        $sort = $this->loadModel('common')->appendOrder($orderBy);
        
        if(is_null($taskID)) $bugs = $this->bug->getBugs($productIdList, $branch, $browseType, $moduleID, $queryID, $sort, $pager, $this->projectID, $applicationID);
        if(!is_null($taskID)) $bugs = $this->bug->getTesttaskBugs($branch, $browseType, $moduleID, $queryID, $sort, $pager, $taskID, $applicationID);

        /* Process the sql, get the conditon partion, save it to session. */
        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'bug', $browseType == 'needconfirm' ? false : true);

        /* Process bug for check story changed. */
        $bugs = $this->loadModel('story')->checkNeedConfirm($bugs);

        /* Process the openedBuild and resolvedBuild fields. */
        $bugs = $this->bug->processBuildForBugs($bugs);
        $bugs = $this->bug->processPlanForBugs($bugs);
        /* Get story and task id list. */
        $storyIdList = $taskIdList = array();
        foreach($bugs as $bug)
        {
            if($bug->story)  $storyIdList[$bug->story] = $bug->story;
            if($bug->task)   $taskIdList[$bug->task]   = $bug->task;
            if($bug->toTask) $taskIdList[$bug->toTask] = $bug->toTask;
        }
        $storyList = $storyIdList ? $this->loadModel('story')->getByList($storyIdList) : array();
        $taskList  = $taskIdList  ? $this->loadModel('task')->getByList($taskIdList)   : array();

        /* Build the search form. */
        $actionURL = $this->createLink('bug', 'browse', "applicationID=$tempApplicationID&productID=$productID&branch=$branch&browseType=bySearch&queryID=myQueryID");
        $this->config->bug->search['onMenuBar'] = 'yes';
        $this->bug->buildSearchForm($queryID, $actionURL, $applicationID, $productID);

        $moduleTree  = '';
        $modulePairs = array();
        $showModule  = !empty($this->config->datatable->bugBrowse->showModule) ? $this->config->datatable->bugBrowse->showModule : '';
        if(is_numeric($productID))
        {
            $treeFunc = array('treeModel', 'createBugLink');
            if(!is_null($taskID)) $treeFunc = array('treeModel', 'createTesttaskBugLink'); 
            $moduleTree  = $this->tree->getTreeMenu($productID, 'bug', 0, $treeFunc, $taskID, $branch);
            $modulePairs = $showModule ? $this->tree->getModulePairs($productID, 'bug', $showModule) : array();
        }

        $projects = $this->rebirth->getProductLinkProjectPairs($applicationID, $productID);

        $productlist = $this->loadModel('rebirth')->getProductIdList($applicationID, 'all');
        $productstr  = implode(',', $productlist);

        /* Set view. */
        $this->view->title         = $application->name . $this->lang->colon . $this->lang->bug->common;
        $this->view->taskID        = $taskID;
        $this->view->applicationID = $applicationID;
        $this->view->productID     = $productID;
        $this->view->builds        = $this->loadModel('build')->getProductBuildPairs($productstr);
        $this->view->planInfo      = $this->loadModel('productplan')->getPairs($productstr);
        $this->view->modules       = $this->tree->getOptionMenu($productID, $viewType = 'bug', $startModuleID = 0, $branch);
        $this->view->products      = $products;
        $this->view->testtasks     = $testtasks;
        $this->view->moduleTree    = $moduleTree;
        $this->view->moduleName    = $moduleID ? $this->tree->getById($moduleID)->name : $this->lang->tree->all;
        $this->view->summary       = $this->bug->summary($bugs);
        $this->view->browseType    = $browseType;
        $this->view->bugs          = $bugs;
        $this->view->users         = $this->user->getPairs('noletter');
        $this->view->memberPairs   = $this->user->getPairs('noletter|nodeleted');
        $this->view->pager         = $pager;
        $this->view->param         = $param;
        $this->view->orderBy       = $orderBy;
        $this->view->moduleID      = $moduleID;
        $this->view->branch        = $branch;
        $this->view->branches      = array();
        $this->view->plans         = $this->loadModel('productplan')->getPairs($productIdList);
        $this->view->projects      = $projects;
        $this->view->stories       = $storyList;
        $this->view->tasks         = $taskList;
        $this->view->setModule     = true;
        $this->view->isProjectBug  = ($productID and !$this->projectID) ? false : true;
        $this->view->modulePairs   = $modulePairs;
        $this->view->typeTileList  = $this->bug->getChildTypeTileList();

        $this->display();
    }

    /**
     * Create a bug.
     *
     * @param  int    $productID
     * @param  string $branch
     * @param  string $extras       others params, forexample, executionID=10,moduleID=10
     * @access public
     * @return void
     */
    public function create($applicationID, $productID, $branch = '', $extras = '')
    {
        if(empty($this->applicationList)) $this->locate($this->createLink('product', 'create'));

        /* Unset discarded types. */
        foreach($this->config->bug->discardedTypes as $type) unset($this->lang->bug->typeList[$type]);

        /* Whether there is a object to transfer bug, for example feedback. */
        $extras = str_replace(array(',', ' '), array('&', ''), $extras);
        parse_str($extras, $output);

        /* Get product, then set menu. */
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

        $branches = array();
        foreach($output as $paramKey => $paramValue)
        {
            if(isset($this->config->bug->fromObjects[$paramKey]))
            {
                $fromObjectIDKey  = $paramKey;
                $fromObjectID     = $paramValue;
                $fromObjectName   = $this->config->bug->fromObjects[$fromObjectIDKey]['name'];
                $fromObjectAction = $this->config->bug->fromObjects[$fromObjectIDKey]['action'];
                break;
            }
        }

        /* If there is a object to transfer bug, get it by getById function and set objectID,object in views. */
        if(isset($fromObjectID))
        {
            $fromObject = $this->loadModel($fromObjectName)->getById($fromObjectID);
            if(!$fromObject) die(js::error($this->lang->notFound) . js::locate('back', 'parent'));

            $this->view->$fromObjectIDKey = $fromObjectID;
            $this->view->$fromObjectName  = $fromObject;
        }

        $this->view->users = $this->user->getPairs('devfirst|noclosed|nodeleted');
        $this->app->loadLang('release');

        if(!empty($_POST))
        {
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            if(!empty($_POST['openedBuild']))
            {
                $builds = $this->dao->select('version')->from(TABLE_BUILD)
                    ->where('id')->in($_POST['openedBuild'])
                    ->fetchAll('version');
                $number = array_keys($builds);
                $number = implode(',',$number);
                $_POST['linkPlan'] = $number;
            } else
            {
                if(empty($_POST['linkPlan'])) $_POST['linkPlan'] = [];
                $_POST['linkPlan'] = implode(',', $_POST['linkPlan']);
            }


            /* Set from param if there is a object to transfer bug. */
            setcookie('lastBugModule', (int)$this->post->module, $this->config->cookieLife, $this->config->webRoot, '', $this->config->cookieSecure, false);
            $bugResult = $this->bug->create($from = isset($fromObjectIDKey) ? $fromObjectIDKey : '');
            if(!$bugResult or dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $bugID = $bugResult['id'];
            if($bugResult['status'] == 'exists')
            {
                $response['message'] = sprintf($this->lang->duplicate, $this->lang->bug->common);
                $response['locate']  = $this->createLink('bug', 'view', "bugID=$bugID");
                $this->send($response);
            }

            /* Record related action, for example FromFeedback. */
            if(isset($fromObjectID))
            {
                $actionID = $this->action->create('bug', $bugID, $fromObjectAction, '', $fromObjectID);
            }
            else
            {
                $actionID = $this->action->create('bug', $bugID, 'Opened');
            }

            $extras = str_replace(array(',', ' '), array('&', ''), $extras);
            parse_str($extras, $output);
            if(isset($output['todoID']))
            {
                $this->dao->update(TABLE_TODO)->set('status')->eq('done')->where('id')->eq($output['todoID'])->exec();
                $this->action->create('todo', $output['todoID'], 'finished', '', "BUG:$bugID");
            }

            $this->executeHooks($bugID);

            /* If link from no head then reload. */
            if(isonlybody()) $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'parent'));

            if(defined('RUN_MODE') && RUN_MODE == 'api') $this->send(array('status' => 'success', 'data' => $bugID));

            if($this->app->openApp == 'project')
            {
                if(isset($output['taskID'])) $location = $this->createLink('bug', 'browse', "applicationID=testtask{$output['taskID']}");
                if(!isset($output['taskID'])) $location = $this->createLink('project', 'bug', "projectID={$this->session->project}&applicationID=$applicationID&productID={$this->post->product}");
            }
            else
            {
                setcookie('bugModule', 0, 0, $this->config->webRoot, '', $this->config->cookieSecure, false);
                $location = $this->createLink('bug', 'browse', "applicationID=$applicationID&productID={$this->post->product}&branch=$branch&browseType=byModule&param={$this->post->module}&orderBy=id_desc");
            }
            $response['locate'] = $location;
            $this->send($response);
        }

        /* Init vars. */
        $projectID   = 0;
        $moduleID    = 0;
        $executionID = 0;
        $taskID      = 0;
        $storyID     = 0;
        $buildID     = 0;
        $caseID      = 0;
        $runID       = 0;
        $planID      = 0;
        $version     = 0;
        $title       = '';
        //$steps       = $this->lang->bug->tplStep . $this->lang->bug->tplResult . $this->lang->bug->tplExpect;
        $steps       = $this->lang->bug->tplStep . $this->lang->bug->tplExpect . $this->lang->bug->tplResult . $this->lang->bug->tplFile . $this->lang->bug->tplFrequency . $this->lang->bug->tplEnvironment . $this->lang->bug->tplData ;
        $os          = '';
        $browser     = '';
        $assignedTo  = '';
        $deadline    = '';
        $mailto      = '';
        $keywords    = '';
        $severity    = 3;
        $type        = 'funcdetect';
        $pri         = 2;
        $color       = '';

        $linkTesttaskID = '';

        if(isset($output['taskID'])) $linkTesttaskID = $output['taskID'];

        if($this->app->openApp == 'project') $moduleID = $this->session->moduleID;
        
        /* Parse the extras. extract fix php7.2. */
        $extras = str_replace(array(',', ' '), array('&', ''), $extras);
        parse_str($extras, $output);
        extract($output);

        if($runID and $resultID) extract($this->bug->getBugInfoFromResult($resultID, 0, 0, isset($stepIdList) ? $stepIdList : ''));// If set runID and resultID, get the result info by resultID as template.
        if(!$runID and $caseID)  extract($this->bug->getBugInfoFromResult($resultID, $caseID, $version, isset($stepIdList) ? $stepIdList : ''));// If not set runID but set caseID, get the result info by resultID and case info.

        /* If bugID setted, use this bug as template. */
        if(isset($bugID))
        {
            $bug = $this->bug->getById($bugID);
            extract((array)$bug);
            $projectID   = $bug->project;
            $executionID = $bug->execution;
            $moduleID    = $bug->module;
            $taskID      = $bug->task;
            $planID      = $bug->linkPlan;
            $storyID     = $bug->story;
            $buildID     = $bug->openedBuild;
            $severity    = $bug->severity;
            $type        = $bug->type;
            $assignedTo  = $bug->assignedTo;
            $deadline    = $bug->deadline;
            $color       = $bug->color;
            
            $linkTesttaskID = $bug->linkTesttask;
        }

        if(isset($todoID))
        {
            $todo  = $this->loadModel('todo')->getById($todoID);
            $title = $todo->name;
            $steps = $todo->desc;
            $pri   = $todo->pri;
        }
        /* Replace the value of bug that needs to be replaced with the value of the object that is transferred to bug. */
        if(isset($fromObject))
        {
            foreach($this->config->bug->fromObjects[$fromObjectIDKey]['fields'] as $bugField => $fromObjectField)
            {
                $$bugField = $fromObject->{$fromObjectField};
            }
        }

        // 如果是指定了制版参数打开的新增页面
        if(isset($buildID) && !empty($buildID))
        {
            $builds = $this->dao->select('version')->from(TABLE_BUILD)
                    ->where('id')->in($buildID)
                    ->fetchAll('version');
            $number = array_keys($builds);
            $planID = implode(',',$number);
        }

        $builds   = $this->loadModel('build')->getProductBuildPairs($productID, $branch, 'noempty,noterminate,nodone');
        $stories  = $this->story->getProductStoryPairs($productID, $branch);
        $linkPlan = $this->loadModel('productplan')->getPairs($productID, $branch, 'noempty,noterminate,nodone');

        $productMembers = array('' => '');

        $moduleOptionMenu = $this->tree->getOptionMenu($productID, $viewType = 'bug', $startModuleID = 0, $branch);
        if(empty($moduleOptionMenu)) die(js::locate(helper::createLink('tree', 'browse', "productID=$productID&view=story")));

        /* Get products and projects. */
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

        /* Get block id of assinge to me. */
        $blockID = 0;
        if(isonlybody())
        {
            $blockID = $this->dao->select('id')->from(TABLE_BLOCK)
                ->where('block')->eq('assingtome')
                ->andWhere('module')->eq('my')
                ->andWhere('account')->eq($this->app->user->account)
                ->orderBy('order_desc')
                ->fetch('id');
        }

        // 获取Bug子类的数据。
        $this->view->childType        = 'b1';
        $this->view->childTypeList    = $this->bug->getChildTypeList($type);
        $this->view->allChildTypeList = $this->bug->getAllChildTypeList();

        // 缺陷定级指南。
        $this->loadModel('file');
        $this->view->file = $this->loadModel('custom')->getGuideFile();

        // 测试单下用例失败转bug时
        if(isset($testtask)) $linkTesttaskID = $testtask;

        // 读取相关测试单
        $testtasks = $this->loadModel('testtask')->getPairs(0, 0, $linkTesttaskID, '', "oddNumber", $applicationID);
        $this->view->testtasks = $testtasks;

        /* Set custom. */
        foreach(explode(',', $this->config->bug->list->customCreateFields) as $field) $customFields[$field] = $this->lang->bug->$field;
        $this->view->customFields = $customFields;
        $this->view->showFields   = $this->config->bug->custom->createFields;

        $this->view->title = $this->applicationList[$applicationID] . $this->lang->colon . $this->lang->bug->create;

        $this->view->linkTesttaskID   = $linkTesttaskID;
        $this->view->applicationID    = $applicationID;
        $this->view->products         = $products;
        $this->view->productID        = $productID;
        $this->view->moduleOptionMenu = $moduleOptionMenu;
        $this->view->stories          = $stories;
        $this->view->projects         = $projects;
        $this->view->linkPlan         = $linkPlan;
        $this->view->executions       = array(0 => '');
        $this->view->builds           = $builds;
        $this->view->moduleID         = (int)$moduleID;
        $this->view->projectID        = $projectID;
        $this->view->executionID      = $executionID;
        $this->view->taskID           = $taskID;
        $this->view->storyID          = $storyID;
        $this->view->buildID          = $buildID;
        $this->view->caseID           = $caseID;
        $this->view->runID            = $runID;
        $this->view->planID           = $planID;
        $this->view->version          = $version;
        $this->view->bugTitle         = $title;
        $this->view->pri              = $pri;
        $this->view->steps            = htmlspecialchars($steps);
        $this->view->os               = $os;
        $this->view->browser          = $browser;
        $this->view->productMembers   = $productMembers;
        $this->view->assignedTo       = $assignedTo;
        $this->view->deadline         = $deadline;
        $this->view->mailto           = $mailto;
        $this->view->keywords         = $keywords;
        $this->view->severity         = $severity;
        $this->view->type             = $type;
        $this->view->branch           = $branch;
        $this->view->branches         = $branches;
        $this->view->blockID          = $blockID;
        $this->view->color            = $color;
        $this->view->stepsRequired    = strpos($this->config->bug->create->requiredFields, 'steps');
        $this->view->isStepsTemplate  = $steps == $this->lang->bug->tplStep . $this->lang->bug->tplExpect . $this->lang->bug->tplResult . $this->lang->bug->tplFile . $this->lang->bug->tplFrequency . $this->lang->bug->tplEnvironment . $this->lang->bug->tplData ? true : false;

        $this->display();
    }

    /**
     * Batch create.
     *
     * @param  int    $productID
     * @param  int    $executionID
     * @param  int    $moduleID
     * @access public
     * @return void
     */
    public function batchCreate($applicationID, $productID, $branch = '', $executionID = 0, $moduleID = 0)
    {
        /* Get product, then set menu. */
        if($productID == 'all') $productID = 'na';

        $projectID     = '';
        $fromProjectID = 0;
        $applicationID = $this->rebirth->saveState($this->applicationList, $applicationID, $productID);
        $productID     = $this->rebirth->getProductIdByApplication($applicationID, $productID);

        if($this->app->openApp == 'project')
        {
            $fromProjectID = $this->session->project;
            $this->loadModel('project')->setMenu($fromProjectID);
        }
        else
        {
            $this->rebirth->setMenu($applicationID, $productID);
        }

        if(!empty($_POST))
        {
            $actions = $this->bug->batchCreate($applicationID, $productID, $branch);
            if($this->app->openApp == 'project')
            {
                die(js::locate($this->createLink('project', 'bug', "projectID=$fromProjectID&applicationID=$applicationID&productID={$productID}&browseType=unclosed&branch=$branch"), 'parent'));
            }
            else
            {
                die(js::locate($this->createLink('bug', 'browse', "applicationID=$applicationID&productID={$productID}&branch=$branch&browseType=unclosed&param=0&orderBy=id_desc"), 'parent'));
            }
        }

        $builds = $this->loadModel('build')->getProductBuildPairs($productID, $branch, 'noempty,noterminate,nodone');

        if($this->session->bugImagesFile)
        {
            $files = $this->session->bugImagesFile;
            foreach($files as $fileName => $file)
            {
                $title = $file['title'];
                $titles[$title] = $fileName;
            }
            $this->view->titles = $titles;
        }

        /* Set custom. */
        foreach(explode(',', $this->config->bug->list->customBatchCreateFields) as $field)
        {
            $customFields[$field] = $this->lang->bug->$field;
        }

        $showFields = $this->config->bug->custom->batchCreateFields;
        $showFields = str_replace(array(0 => ",branch,", 1 => ",platform,"), '', ",$showFields,");
        $showFields = trim($showFields, ',');

        $this->lang->bug->severityList = array('' => '') + $this->lang->bug->severityList;

         /* Get products and projects. */
        if($this->app->openApp == 'project')
        {
            $projects = array($fromProjectID => '');
        }
        else
        {
            $projects = $this->rebirth->getProductLinkProjectPairs($applicationID, $productID);
        }

        // 获取Bug子类的数据。
        $this->view->pri           = 2;
        $this->view->severity      = 3;
        $this->view->type          = 'funcdetect';
        $this->view->childType     = 'b1';
        $this->view->childTypeList = $this->bug->getChildTypeList($this->view->type);

        $this->view->customFields = $customFields;
        $this->view->showFields   = $showFields;

        $this->view->title            = $this->applicationList[$applicationID] . $this->lang->colon . $this->lang->bug->batchCreate;
        $this->view->applicationID    = $applicationID;
        $this->view->productID        = $productID;
        $this->view->fromProjectID    = $fromProjectID;
        $this->view->builds           = $builds;
        $this->view->projects         = array('' => '') + $projects;
        $this->view->executions       = array('' => '');
        $this->view->executionID      = $executionID;
        $this->view->moduleID         = $moduleID;
        $this->view->branch           = $branch;
        $this->view->moduleOptionMenu = $this->tree->getOptionMenu($productID, $viewType = 'bug', $startModuleID = 0, $branch);
        $this->view->branches         = array();
        $this->display();
    }

    /**
     * View a bug.
     *
     * @param  int    $bugID
     * @param  string $form
     * @access public
     * @return void
     */
    public function view($bugID, $from = 'bug')
    {
        /* Judge bug exits or not. */
        $bugID = (int)$bugID;
        $bug   = $this->bug->getById($bugID, true);
        $bug = $this->loadModel('file')->replaceImgURL($bug,'issues');
        $defectInfo = $this->bug->getOneBybugId($bugID);
        $this->view->defectCode = $defectInfo->code ?? '';
        $this->view->defectID = $defectInfo->id ?? '';
        if(!$bug) die(js::error($this->lang->notFound) . js::locate('back'));

        $this->session->set('storyList', '', 'product');

        /* Update action. */
        if($bug->assignedTo == $this->app->user->account) $this->loadModel('action')->read('bug', $bugID);

        /* Set menu. */
        if($bug->product == 0) $bug->product = 'na';

        /* Get product info. */
        $applicationID = $bug->applicationID;
        $productID     = $bug->product;
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

        if(!isonlybody())
        {
            if($this->app->openApp == 'project')
            {
                $this->loadModel('project')->setMenu($bug->project);
            }
            else
            {
                /* Get product, then set menu. */
                $applicationID = $this->rebirth->saveState($this->applicationList, $bug->applicationID, $bug->product);
                $this->rebirth->setMenu($applicationID, $bug->product);
                $productID = $this->rebirth->getProductIdByApplication($applicationID, $bug->product);
            }
        }

        $this->executeHooks($bugID);

        // 缺陷定级指南。
        $this->loadModel('file');
        $this->view->file = $this->loadModel('custom')->getGuideFile();

        // Bug所关联的项目，项目所关联的年度项目计划信息。
        $projectPlan = '';
        if($bug->project) $projectPlan = $this->dao->select('*')->from(TABLE_PROJECTPLAN)->where('project')->eq($bug->project)->fetch();
        $this->view->projectPlan = $projectPlan;

        /* Assign. */
        $this->view->title      = "BUG #$bug->id $bug->title - " . $product->name;
        $this->view->productID  = $productID;
        $this->view->modulePath = $this->tree->getParents($bug->module);
        $this->view->bugModule  = empty($bug->module) ? '' : $this->tree->getById($bug->module);
        $this->view->bug        = $bug;
        $this->view->from       = $from;
        $this->view->users      = $this->user->getPairs('noletter');
        $this->view->actions    = $this->action->getList('bug', $bugID);
        $this->view->builds     = $this->loadModel('build')->getProductBuildPairs($productID, $branch = 0, $params = '');
        $this->view->preAndNext = $this->loadModel('common')->getPreAndNextObject('bug', $bugID);
        $this->view->product    = $product;
        $this->view->typeTileList = $this->bug->getChildTypeTileList();

        $this->display();
    }

    /**
     * Edit a bug.
     *
     * @param  int    $bugID
     * @access public
     * @return void
     */
    public function edit($bugID, $comment = false)
    {
        if(!empty($_POST))
        {
            $changes = array();
            $files   = array();
            
            if(!empty($_POST['openedBuild']))
            {
                $builds = $this->dao->select('version')->from(TABLE_BUILD)
                    ->where('id')->in($_POST['openedBuild'])
                    ->fetchAll('version');
                $number = array_keys($builds);
                $number = implode(',',$number);
                $_POST['linkPlan'] = $number;
            } else
            {
                $_POST['linkPlan'] = implode(',', $_POST['linkPlan']);
            }
            if($comment == false)
            {
                $changes  = $this->bug->update($bugID);
                if(dao::isError())
                {
                    if(defined('RUN_MODE') && RUN_MODE == 'api')
                    {
                        $this->send(array('status' => 'error', 'message' => dao::getError()));
                    }
                    else
                    {
                        die(js::error(dao::getError()));
                    }
                }
                $files = $this->loadModel('file')->saveUpload('bug', $bugID);
            }
            if($this->post->comment != '' or !empty($changes) or !empty($files))
            {
                $action = (!empty($changes) or !empty($files)) ? 'Edited' : 'Commented';
                $fileAction = '';
                if(!empty($files)) $fileAction = $this->lang->addFiles . join(',', $files) . "\n" ;
                $actionID = $this->action->create('bug', $bugID, $action, $fileAction . $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }
            if(defined('RUN_MODE') && RUN_MODE == 'api') $this->send(array('status' => 'success', 'data' => $bugID));
            $bug = $this->bug->getById($bugID);

            $this->executeHooks($bugID);

            if($bug->toTask != 0)
            {
                foreach($changes as $change)
                {
                    if($change['field'] == 'status')
                    {
                        $confirmURL = $this->createLink('task', 'view', "taskID=$bug->toTask");
                        $cancelURL  = $this->server->HTTP_REFERER;
                        die(js::confirm(sprintf($this->lang->bug->remindTask, $bug->Task), $confirmURL, $cancelURL, 'parent', 'parent'));
                    }
                }
            }
            die(js::locate($this->createLink('bug', 'view', "bugID=$bugID"), 'parent'));
        }

        /* Get the info of bug, current product and modue. */
        $bug           = $this->bug->getById($bugID);
        $applicationID = $this->rebirth->saveState($this->applicationList, $bug->applicationID, $bug->product);
        
        if($this->app->openApp == 'project')
        {
            $this->loadModel('project')->setMenu($bug->project);
        }
        else
        {
            $this->rebirth->setMenu($bug->applicationID, $bug->product);
        }

        if($bug->product == 0) $bug->product = 'na';

        $productID = $this->rebirth->getProductIdByApplication($applicationID, $bug->product);

        /* 获取产品关联的项目。*/
        if($this->app->openApp == 'project')
        {
            $products      = $this->rebirth->getProjectProductPairs($applicationID, $bug->project);
            $assignProduct = $this->loadModel('product')->getByIdPairs(array($productID));
            $products      = $products + $assignProduct;
        }
        else
        {
            $products = $this->rebirth->getProductPairs($applicationID, true);
        }

        $currentModuleID = $bug->module;

        /* Unset discarded types. */
        foreach($this->config->bug->discardedTypes as $type)
        {
            if($bug->type != $type) unset($this->lang->bug->typeList[$type]);
        }
        
        /* Set header and position. */
        $this->view->title    = $this->lang->bug->edit . "BUG #$bug->id $bug->title - " . $products[$productID];
        $this->view->products = $products;

        /* Assign. */
        $product      = $this->loadModel('product')->getByID($productID);
        $allBuilds    = $this->loadModel('build')->getProductBuildPairs($productID, $branch = 0, 'noempty');
        $openedBuilds = $this->build->getProductBuildPairs($productID, $bug->branch, 'noempty,noterminate,nodone');
        $linkPlan     = $this->loadModel('productplan')->getPairs($productID, $branch, 'noempty,noterminate,nodone');
        /* Set the openedBuilds list. */
        $oldOpenedBuilds = array();
        $bugOpenedBuilds = explode(',', $bug->openedBuild);
        foreach($bugOpenedBuilds as $buildID)
        {
            if(isset($allBuilds[$buildID])) $oldOpenedBuilds[$buildID] = $allBuilds[$buildID];
        }
        $openedBuilds = $openedBuilds + $oldOpenedBuilds;

        /* Set the resolvedBuilds list. */
        $oldResolvedBuild = array();
        if(($bug->resolvedBuild) and isset($allBuilds[$bug->resolvedBuild])) $oldResolvedBuild[$bug->resolvedBuild] = $allBuilds[$bug->resolvedBuild];

        // Bug子类的数据。
        $this->view->parentChildTypeList = $this->bug->getChildTypeParentList();

        // 缺陷定级指南。
        $this->loadModel('file');
        $this->view->file = $this->loadModel('custom')->getGuideFile();

        $testtasks = $this->loadModel('testtask')->getPairs(0, 0, '', '', "oddNumber", $applicationID);

        $this->view->bug              = $bug;
        $this->view->testtasks        = $testtasks;
        $this->view->productID        = $productID;
        $this->view->product          = $product;
        $this->view->plans            = $this->loadModel('productplan')->getPairs($productID, $bug->branch);
        $this->view->linkPlan         = $linkPlan;
        $this->view->moduleOptionMenu = $this->tree->getOptionMenu($productID, $viewType = 'bug', $startModuleID = 0, $bug->branch);
        $this->view->currentModuleID  = $currentModuleID;
        $this->view->stories          = $this->story->getProductStoryPairs($bug->product, $bug->branch);
        $this->view->tasks            = $this->task->getExecutionTaskPairs($bug->execution);
        $this->view->users            = $this->user->getPairs('nodeleted', "$bug->assignedTo,$bug->resolvedBy,$bug->closedBy,$bug->openedBy");
        $this->view->openedBuilds     = $openedBuilds;
        $this->view->resolvedBuilds   = array('' => '') + $openedBuilds + $oldResolvedBuild;
        $this->view->actions          = $this->action->getList('bug', $bugID);
        $this->view->projects         = array(0 => '') + $this->rebirth->getProductLinkProjectPairs($bug->applicationID, $bug->product);
        $this->view->executions       = array(0 => '') + $this->loadModel('project')->getExecutionByAvailable($bug->project);
        $this->view->linkPlanDisabled = $bug->openedBuild ? 'disabled' : '';

        $this->display();
    }

    /**
     * Batch edit bug.
     *
     * @param  int    $productID
     * @access public
     * @return void
     */
    public function batchEdit($applicationID = 0, $productID = 0, $branch = 0)
    {
        if($this->post->titles)
        {
            $allChanges = $this->bug->batchUpdate();

            foreach($allChanges as $bugID => $changes)
            {
                if(empty($changes)) continue;

                $actionID = $this->action->create('bug', $bugID, 'Edited');
                $this->action->logHistory($actionID, $changes);

                $bug = $this->bug->getById($bugID);
                if($bug->toTask != 0)
                {
                    foreach($changes as $change)
                    {
                        if($change['field'] == 'status')
                        {
                            $confirmURL = $this->createLink('task', 'view', "taskID=$bug->toTask");
                            $cancelURL  = $this->server->HTTP_REFERER;
                            die(js::confirm(sprintf($this->lang->bug->remindTask, $bug->task), $confirmURL, $cancelURL, 'parent', 'parent'));
                        }
                    }
                }
            }

            die(js::locate($this->session->bugList, 'parent'));
        }

        /* set navigation menu */
        if($this->app->openApp == 'my')
        {
            $this->loadModel('my')->setMenu();
            $this->lang->task->menu = $this->lang->my->menu->work;
            $this->lang->my->menu->work['subModule'] = 'bug';
        }
        elseif($this->app->openApp == 'project')
        {
            $this->loadModel('project')->setMenu($this->session->project);
        }
        else
        {
            $this->rebirth->setMenu($applicationID, $productID);
            $applicationID = $this->rebirth->saveState($this->applicationList, $applicationID, $productID);
            $productID     = $this->rebirth->getProductIdByApplication($applicationID, $productID);
        }

        $bugIDList = $this->post->bugIDList ? $this->post->bugIDList : die(js::locate($this->session->bugList, 'parent'));
        $bugIDList = array_unique($bugIDList);

        /* Initialize vars.*/
        $bugs = $this->dao->select('*')->from(TABLE_BUG)->where('id')->in($bugIDList)->fetchAll('id');

        /* Judge whether the editedBugs is too large and set session. */
        $countInputVars  = count($bugs) * (count(explode(',', $this->config->bug->custom->batchEditFields)) + 2);
        $showSuhosinInfo = common::judgeSuhosinSetting($countInputVars);
        if($showSuhosinInfo) $this->view->suhosinInfo = extension_loaded('suhosin') ? sprintf($this->lang->suhosinInfo, $countInputVars) : sprintf($this->lang->maxVarsInfo, $countInputVars);

        /* Set Custom*/
        foreach(explode(',', $this->config->bug->list->customBatchEditFields) as $field) $customFields[$field] = $this->lang->bug->$field;
        $this->view->customFields = $customFields;
        $this->view->showFields   = $this->config->bug->custom->batchEditFields;

        /* Set users. */
        $appendUsers = array();
        foreach($bugs as $bug)
        {
            $appendUsers[$bug->assignedTo] = $bug->assignedTo;
            $appendUsers[$bug->resolvedBy] = $bug->resolvedBy;
        }
        $users = $this->user->getPairs('devfirst|nodeleted', $appendUsers, $this->config->maxCount);
        $users = array('' => '', 'ditto' => $this->lang->bug->ditto) + $users;

        // Bug子类的数据。
        $this->view->parentChildTypeList = $this->bug->getChildTypeParentList();

        /* Assign. */
        $this->view->title          = "BUG" . $this->lang->bug->batchEdit;
        $this->view->applicationID  = $applicationID;
        $this->view->productID      = $productID;
        $this->view->branchProduct  = false;
        $this->view->severityList   = array('ditto' => $this->lang->bug->ditto) + $this->lang->bug->severityList;
        $this->view->typeList       = array('' => '') + $this->lang->bug->typeList;
        $this->view->priList        = array('0' => '', 'ditto' => $this->lang->bug->ditto) + $this->lang->bug->priList;
        $this->view->resolutionList = array('' => '',  'ditto' => $this->lang->bug->ditto) + $this->lang->bug->resolutionList;
        $this->view->statusList     = array('' => '',  'ditto' => $this->lang->bug->ditto) + $this->lang->bug->statusList;
        $this->view->osList         = array('' => '',  'ditto' => $this->lang->bug->ditto) + $this->lang->bug->osList;
        $this->view->browserList    = array('' => '',  'ditto' => $this->lang->bug->ditto) + $this->lang->bug->browserList;
        $this->view->bugs           = $bugs;
        $this->view->branch         = $branch;
        $this->view->users          = $users;

        $this->display();
    }

    /**
     * Update assign of bug.
     *
     * @param  int    $bugID
     * @access public
     * @return void
     */
    public function assignTo($bugID)
    {
        if(!empty($_POST))
        {
            $this->loadModel('action');
            $changes = $this->bug->assign($bugID);
            if(dao::isError()) die(js::error(dao::getError()));
            $actionID = $this->action->create('bug', $bugID, 'Assigned', $this->post->comment, $this->post->assignedTo);
            $this->action->logHistory($actionID, $changes);

            $this->executeHooks($bugID);

            if(isonlybody()) die(js::closeModal('parent.parent'));
            die(js::locate($this->createLink('bug', 'view', "bugID=$bugID"), 'parent'));
        }

        $bug = $this->bug->getById($bugID);
        if($bug->product == 0) $bug->product = 'na';

        /* Get product, then set menu. */
        $applicationID = $this->rebirth->saveState($this->applicationList, $bug->applicationID, $bug->product);
        $this->rebirth->setMenu($applicationID, $bug->product);
        $productID = $this->rebirth->getProductIdByApplication($applicationID, $bug->product);
        $products  = $this->rebirth->getProductPairs($applicationID, true);

        $users = $this->user->getPairs('nodeleted|nofeedback', $bug->assignedTo);

        $this->view->title   = $products[$bug->product] . $this->lang->colon . $this->lang->bug->assignedTo;
        $this->view->users   = $users;
        $this->view->bug     = $bug;
        $this->view->bugID   = $bugID;
        $this->view->actions = $this->action->getList('bug', $bugID);
        $this->display();
    }

    /**
     * Batch change the module of bug.
     *
     * @param  int    $moduleID
     * @access public
     * @return void
     */
    public function batchChangeModule($moduleID)
    {
        if($this->post->bugIDList)
        {
            $bugIDList = $this->post->bugIDList;
            $bugIDList = array_unique($bugIDList);
            unset($_POST['bugIDList']);
            $allChanges = $this->bug->batchChangeModule($bugIDList, $moduleID);
            if(dao::isError()) die(js::error(dao::getError()));
            foreach($allChanges as $bugID => $changes)
            {
                $this->loadModel('action');
                $actionID = $this->action->create('bug', $bugID, 'Edited');
                $this->action->logHistory($actionID, $changes);
            }
        }
        $this->loadModel('score')->create('ajax', 'batchOther');
        die(js::locate($this->session->bugList, 'parent'));
    }

    /**
     * Batch update assign of bug.
     *
     * @param  int     $objectID  projectID|executionID
     * @param  string  $type      execution|project|product|my
     * @access public
     * @return void
     */
    public function batchAssignTo($objectID, $type = 'project', $applicationID = 0, $productID = 0)
    {
        if(!empty($_POST) && isset($_POST['bugIDList']))
        {
            $bugIDList = $this->post->bugIDList;
            $bugIDList = array_unique($bugIDList);
            unset($_POST['bugIDList']);
            foreach($bugIDList as $bugID)
            {
                $this->loadModel('action');
                $changes = $this->bug->assign($bugID);
                if(dao::isError()) die(js::error(dao::getError()));
                $actionID = $this->action->create('bug', $bugID, 'Assigned', $this->post->comment, $this->post->assignedTo);
                $this->action->logHistory($actionID, $changes);
            }
            $this->loadModel('score')->create('ajax', 'batchOther');
        }

        if($type == 'product' || $type == 'my') die(js::locate($this->session->bugList, 'parent'));
        if($type == 'project') die(js::locate($this->createLink('project', 'bug', "projectID=$objectID&applicationID=$applicationID&productID=$productID")));
    }

    /**
     * confirm a bug.
     *
     * @param  int    $bugID
     * @access public
     * @return void
     */
    public function confirmBug($bugID)
    {
        if(!empty($_POST))
        {
            $changes = $this->bug->confirm($bugID);
            if(dao::isError()) die(js::error(dao::getError()));
            $actionID = $this->action->create('bug', $bugID, 'bugConfirmed', $this->post->comment);
            $this->action->logHistory($actionID, $changes);

            $this->executeHooks($bugID);

            if(isonlybody()) die(js::closeModal('parent.parent'));
            die(js::locate($this->createLink('bug', 'view', "bugID=$bugID"), 'parent'));
        }

        $bug = $this->bug->getById($bugID);
        if($bug->product == 0) $bug->product = 'na';

        /* Get product, then set menu. */
        $applicationID = $this->rebirth->saveState($this->applicationList, $bug->applicationID, $bug->product);
        $this->rebirth->setMenu($applicationID, $bug->product);
        $productID = $this->rebirth->getProductIdByApplication($applicationID, $bug->product);
        $products  = $this->rebirth->getProductPairs($applicationID, true);

        $this->view->title   = $products[$productID] . $this->lang->colon . $this->lang->bug->confirmBug;
        $this->view->bug     = $bug;
        $this->view->users   = $this->user->getPairs('nodeleted', $bug->assignedTo);
        $this->view->actions = $this->action->getList('bug', $bugID);
        $this->display();
    }

    /**
     * bug转缺陷
     *
     * @param  int    $bugID
     * @access public
     * @return void
     */
    public function defectbug($bugID)
    {
        if(!empty($_POST))
        {
            $defect = $this->bug->defect($bugID);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->action->create('bug', $bugID, 'defectbug');
            $this->send($response);
        }

        $bug       = $this->bug->getById($bugID);
        $productID = $bug->product;
        $projectID = $bug->project;
        $allBuilds    = $this->loadModel('build')->getProductBuildPairs($productID, $branch = 0, 'noempty');
        $openedBuilds = $this->build->getProductBuildPairs($productID, $bug->branch, 'noempty,noterminate,nodone');

        /* Set the openedBuilds list. */
        $oldOpenedBuilds = array();
        $bugOpenedBuilds = explode(',', $bug->openedBuild);
        foreach($bugOpenedBuilds as $buildID)
        {
            if(isset($allBuilds[$buildID])) $oldOpenedBuilds[$buildID] = $allBuilds[$buildID];
        }
        $openedBuilds = $openedBuilds + $oldOpenedBuilds;
        /* Set the resolvedBuilds list. */
        $oldResolvedBuild = array();
        if(($bug->resolvedBuild) and isset($allBuilds[$bug->resolvedBuild])) $oldResolvedBuild[$bug->resolvedBuild] = $allBuilds[$bug->resolvedBuild];
        $this->view->executions = $this->loadModel('product')->getExecutionPairsByProduct($productID, $bug->branch ? "0,{$bug->branch}" : 0, 'id_desc', $projectID);
        $this->view->parentChildTypeList = $this->bug->getChildTypeParentList();
        $this->view->childType        = 'b1';
        $this->view->childTypeList    = $this->bug->getChildTypeList($bug->type);
        // Bug子类的数据。
        $this->view->parentChildTypeList = $this->bug->getChildTypeParentList();
        $this->view->resolvedBuilds   = array('' => '') + $openedBuilds + $oldResolvedBuild;
        $this->view->title      = $this->products[$productID] ?? '' . $this->lang->colon . $this->lang->bug->defect;
        $this->view->productList= array('0' => '无') + $this->loadModel('product')->getPairs();
        $this->view->projects   = array('' => '') + $this->loadModel('project')->getPairs('noclosed');
        $this->view->apps       = array(0 => '') + $this->loadmodel('application')->getapplicationNameCodePairs();
        $this->view->bug        = $bug;
        $this->view->users      = $this->user->getPairs('nodeleted', $bug->assignedTo);
        $this->view->actions    = $this->action->getList('bug', $bugID);
        $this->view->childTypeList    = $this->bug->getChildTypeList('');

        $this->display();
    }

    /**
     * 获取所属部门
     */
    public function ajaxGetDeptById($account)
    {
        $deptId = 0;
        $usersDept = $this->loadModel('user')->getUserDeptIds($account);
        if(!empty($usersDept)){
            $deptId = $usersDept[0];
        }
        $deptList = $this->loadModel('dept')->getDeptPairs();
        $deptName = $deptList[$deptId];
        die(html::input('dept',$deptName, "class='form-control' disabled"));
    }

    /**
     * 部门负责人和测试负责人
     */
    public function ajaxGetDeptUserById($account)
    {
        $deptId = 0;
        $usersDept = $this->loadModel('user')->getUserDeptIds($account);
        if(!empty($usersDept)){
            $deptId = $usersDept[0];
        }
        $deptList = explode(",", $this->config->bug->allowDeptList);
        if(in_array($deptId, $deptList)){
            //部门负责人和测试负责人
            $dept  = $this->loadModel("dept")->getById($deptId);
            die($dept->manager.','.$dept->testLeader);
        }
        die("");
    }



    /**
     * Batch confirm bugs.
     *
     * @access public
     * @return void
     */
    public function batchConfirm()
    {
        $bugIDList = $this->post->bugIDList ? $this->post->bugIDList : die(js::reload('parent'));
        $bugIDList = array_unique($bugIDList);
        $this->bug->batchConfirm($bugIDList);
        if(dao::isError()) die(js::error(dao::getError()));
        foreach($bugIDList as $bugID) $this->action->create('bug', $bugID, 'bugConfirmed');
        $this->loadModel('score')->create('ajax', 'batchOther');
        die(js::reload('parent'));
    }

    /**
     * Resolve a bug.
     *
     * @param  int    $bugID
     * @access public
     * @return void
     */
    public function resolve($bugID)
    {
        if(!empty($_POST))
        {
            $changes = $this->bug->resolve($bugID);
            if(dao::isError()) die(js::error(dao::getError()));
            $files = $this->loadModel('file')->saveUpload('bug', $bugID);

            $fileAction = !empty($files) ? $this->lang->addFiles . join(',', $files) . "\n" : '';
            $actionID = $this->action->create('bug', $bugID, 'Resolved', $fileAction . $this->post->comment, $this->post->resolution . ($this->post->duplicateBug ? ':' . (int)$this->post->duplicateBug : ''));
            $this->action->logHistory($actionID, $changes);

            $bug = $this->bug->getById($bugID);

            $this->executeHooks($bugID);

            if($bug->toTask != 0)
            {
                /* If task is not finished, update it's status. */
                $task = $this->task->getById($bug->toTask);
                if($task->status != 'done')
                {
                    $confirmURL = $this->createLink('task', 'view', "taskID=$bug->toTask");
                    unset($_GET['onlybody']);
                    $cancelURL  = $this->createLink('bug', 'view', "bugID=$bugID");
                    die(js::confirm(sprintf($this->lang->bug->remindTask, $bug->toTask), $confirmURL, $cancelURL, 'parent', 'parent.parent'));
                }
            }
            if(isonlybody()) die(js::closeModal('parent.parent'));
            if(defined('RUN_MODE') && RUN_MODE == 'api')
            {
                die(array('status' => 'success', 'data' => $bugID));
            }
            else
            {
                die(js::locate($this->createLink('bug', 'view', "bugID=$bugID"), 'parent'));
            }
        }
        $resolutionList = $this->lang->bug->resolutionList;

        $bug        = $this->bug->getById($bugID);
        $assignedTo = $bug->openedBy;
        unset($this->lang->bug->resolutionList['tostory']);

        if($bug->product == 0) $bug->product = 'na';

        /* Get product, then set menu. */
        $applicationID = $this->rebirth->saveState($this->applicationList, $bug->applicationID, $bug->product);
        $this->rebirth->setMenu($applicationID, $bug->product);
        $productID = $this->rebirth->getProductIdByApplication($applicationID, $bug->product);
        $products  = $this->rebirth->getProductPairs($applicationID, true);
        $projects  = array();

        $users = $this->user->getPairs('noclosed');
        $this->view->title      = $this->applicationList[$applicationID] . $this->lang->colon . $this->lang->bug->resolve;
        $this->view->bug        = $bug;
        $this->view->resolutionList        = $resolutionList;
        $this->view->users      = $users;
        $this->view->products   = $products;
        $this->view->projects   = $projects;
        $this->view->assignedTo = $assignedTo;
        $this->view->builds     = $this->loadModel('build')->getProductBuildPairs($productID, $branch = $bug->branch, 'all');
        $this->view->actions    = $this->action->getList('bug', $bugID);
        $this->display();
    }

    /**
     * Batch resolve bugs.
     *
     * @param  string    $resolution
     * @param  string    $resolvedBuild
     * @access public
     * @return void
     */
    public function batchResolve($resolution, $resolvedBuild = '')
    {
        $bugIDList = $this->post->bugIDList ? $this->post->bugIDList : die(js::locate($this->session->bugList, 'parent'));
        $bugIDList = array_unique($bugIDList);

        $changes   = $this->bug->batchResolve($bugIDList, $resolution, $resolvedBuild);
        if(dao::isError()) die(js::error(dao::getError()));

        foreach($changes as $bugID => $bugChanges)
        {
            $actionID = $this->action->create('bug', $bugID, 'Resolved', '', $resolution);
            $this->action->logHistory($actionID, $bugChanges);
        }

        $this->loadModel('score')->create('ajax', 'batchOther');
        die(js::locate($this->session->bugList, 'parent'));
    }

    /**
     * Activate a bug.
     *
     * @param  int    $bugID
     * @access public
     * @return void
     */
    public function activate($bugID)
    {
        if(!empty($_POST))
        {
            if(!empty($_POST['openedBuild']))
            {
                $builds = $this->dao->select('version')->from(TABLE_BUILD)
                    ->where('id')->in($_POST['openedBuild'])
                    ->fetchAll('version');
                $number = array_keys($builds);
                $number = implode(',',$number);
                $_POST['linkPlan'] = $number;
            } else
            {
                $_POST['linkPlan'] = implode(',', $_POST['linkPlan']);
                $_POST['openedBuild'] = '';
            }

            $changes = $this->bug->activate($bugID);
            if(dao::isError()) die(js::error(dao::getError()));

            $files = $this->loadModel('file')->saveUpload('bug', $bugID);

            $actionID = $this->action->create('bug', $bugID, 'Activated', $this->post->comment);
            $this->action->logHistory($actionID, $changes);

            $this->executeHooks($bugID);

            if(isonlybody()) die(js::closeModal('parent.parent'));
            die(js::locate($this->createLink('bug', 'view', "bugID=$bugID"), 'parent'));
        }

        $bug = $this->bug->getById($bugID);
        if($bug->product == 0) $bug->product = 'na';

        /* Get product, then set menu. */
        $applicationID = $this->rebirth->saveState($this->applicationList, $bug->applicationID, $bug->product);
        $this->rebirth->setMenu($applicationID, $bug->product);
        $productID = $this->rebirth->getProductIdByApplication($applicationID, $bug->product);
        $products  = $this->rebirth->getProductPairs($applicationID, true);

        $this->view->title            = $products[$productID] . $this->lang->colon . $this->lang->bug->activate;
        $this->view->bug              = $bug;
        $this->view->users            = $this->user->getPairs('nodeleted', $bug->resolvedBy);
        $this->view->builds           = $this->loadModel('build')->getProductBuildPairs($productID, $bug->branch, 'noempty');
        $this->view->linkPlan         = $this->loadModel('productplan')->getPairs($productID, $bug->branch, 'noempty,noterminate,nodone');
        $this->view->actions          = $this->action->getList('bug', $bugID);
        $this->view->linkPlanDisabled = $bug->openedBuild ? 'disabled' : '';

        $this->display();
    }

    /**
     * Close a bug.
     *
     * @param  int    $bugID
     * @access public
     * @return void
     */
    public function close($bugID)
    {
        if(!empty($_POST))
        {
            $changes = $this->bug->close($bugID);
            if(dao::isError()) die(js::error(dao::getError()));

            $actionID = $this->action->create('bug', $bugID, 'Closed', $this->post->comment);
            $this->action->logHistory($actionID, $changes);

            $this->executeHooks($bugID);

            if(isonlybody()) die(js::closeModal('parent.parent'));
            if(defined('RUN_MODE') && RUN_MODE == 'api')
            {
                die(array('status' => 'success', 'data' => $bugID));
            }
            else
            {
                die(js::locate($this->createLink('bug', 'view', "bugID=$bugID"), 'parent'));
            }
        }

        $bug = $this->bug->getById($bugID);
        if($bug->product == 0) $bug->product = 'na';

        /* Get product, then set menu. */
        $applicationID = $this->rebirth->saveState($this->applicationList, $bug->applicationID, $bug->product);
        $this->rebirth->setMenu($applicationID, $bug->product);
        $productID = $this->rebirth->getProductIdByApplication($applicationID, $bug->product);
        $products  = $this->rebirth->getProductPairs($applicationID, true);

        $this->view->title   = $products[$productID] . $this->lang->colon . $this->lang->bug->close;
        $this->view->bug     = $bug;
        $this->view->users   = $this->user->getPairs('noletter');
        $this->view->actions = $this->action->getList('bug', $bugID);
        $this->display();
    }

    /**
     * Link related bugs.
     *
     * @param  int    $bugID
     * @param  string $browseType
     * @param  int    $param
     * @access public
     * @return void
     */
    public function linkBugs($bugID, $browseType = '', $param = 0)
    {
        /* Get bug and queryID. */
        $bug     = $this->bug->getById($bugID);
        $queryID = ($browseType == 'bySearch') ? (int)$param : 0;

        if($bug->product == 0) $bug->product = 'na';
        $this->rebirth->setMenu($bug->applicationID, $bug->product);
        $productID = $this->rebirth->getProductIdByApplication($bug->applicationID, $bug->product);
        $products  = $this->rebirth->getProductPairs($bug->applicationID, true);

        /* Build the search form. */
        $actionURL = $this->createLink('bug', 'linkBugs', "bugID=$bugID&browseType=bySearch&queryID=myQueryID", '', true);
        $this->bug->buildSearchForm($queryID, $actionURL, $bug->applicationID, $productID);

        /* Get bugs to link. */
        $bugs2Link = $this->bug->getBugs2Link($bugID, $browseType, $queryID);

        /* Assign. */
        $this->view->title     = $this->lang->bug->linkBugs . "BUG #$bug->id $bug->title";
        $this->view->bug       = $bug;
        $this->view->products  = array('0' => $this->lang->naProduct) + $products;
        $this->view->bugs2Link = $bugs2Link;
        $this->view->users     = $this->loadModel('user')->getPairs('noletter');

        $this->display();
    }

    /**
     * Batch close bugs.
     *
     * @access public
     * @return void
     */
    public function batchClose()
    {
        if($this->post->bugIDList)
        {
            $bugIDList = $this->post->bugIDList;
            $bugIDList = array_unique($bugIDList);

            /* Reset $_POST. Do not unset that because the function of close need that in model. */
            $_POST = array();

            $bugs = $this->bug->getByList($bugIDList);
            foreach($bugs as $bugID => $bug)
            {
                if($bug->status != 'resolved')
                {
                    if($bug->status != 'closed') $skipBugs[$bugID] = $bugID;
                    continue;
                }

                $changes = $this->bug->close($bugID);

                $actionID = $this->action->create('bug', $bugID, 'Closed');
                $this->action->logHistory($actionID, $changes);
            }
            $this->loadModel('score')->create('ajax', 'batchOther');
            if(isset($skipBugs)) echo js::alert(sprintf($this->lang->bug->skipClose, join(',', $skipBugs)));
        }
        die(js::reload('parent'));
    }

    /**
     * Batch activate bugs.
     *
     * @access public
     * @return void
     */
    public function batchActivate($productID, $branch = 0)
    {
        if($this->post->statusList)
        {
            $activateBugs = $this->bug->batchActivate();
            foreach($activateBugs as $bugID => $bug) $this->action->create('bug', $bugID, 'Activated', $bug['comment']);
            $this->loadModel('score')->create('ajax', 'batchOther');
            die(js::locate($this->session->bugList, 'parent'));
        }

        $bugIDList = $this->post->bugIDList ? $this->post->bugIDList : die(js::locate($this->session->bugList, 'parent'));
        $bugIDList = array_unique($bugIDList);
        $bugs = $this->dao->select('id, title, status, resolvedBy, openedBuild')->from(TABLE_BUG)->where('id')->in($bugIDList)->fetchAll('id');

        $this->view->title  = $this->products[$productID] . $this->lang->colon . $this->lang->bug->batchActivate;
        $this->view->bugs   = $bugs;
        $this->view->users  = $this->user->getPairs();
        $this->view->builds = $this->loadModel('build')->getProductBuildPairs($productID, $branch, 'noempty');

        $this->display();
    }

    /**
     * Confirm story change.
     *
     * @param  int    $bugID
     * @access public
     * @return void
     */
    public function confirmStoryChange($bugID)
    {
        $bug = $this->bug->getById($bugID);
        $this->dao->update(TABLE_BUG)->set('storyVersion')->eq($bug->latestStoryVersion)->where('id')->eq($bugID)->exec();
        $this->loadModel('action')->create('bug', $bugID, 'confirmed', '', $bug->latestStoryVersion);
        die(js::reload('parent'));
    }

    /**
     * Delete a bug.
     *
     * @param  int    $bugID
     * @param  string $confirm  yes|no
     * @access public
     * @return void
     */
    public function delete($bugID, $confirm = 'no')
    {
        $bug = $this->bug->getById($bugID);
        if($confirm == 'no')
        {
            die(js::confirm($this->lang->bug->confirmDelete, inlink('delete', "bugID=$bugID&confirm=yes")));
        }
        else
        {
            $this->bug->delete(TABLE_BUG, $bugID);
            if($bug->toTask != 0)
            {
                $task = $this->task->getById($bug->toTask);
                if(!$task->deleted)
                {
                    $confirmURL = $this->createLink('task', 'view', "taskID=$bug->toTask");
                    unset($_GET['onlybody']);
                    $cancelURL  = $this->createLink('bug', 'view', "bugID=$bugID");
                    die(js::confirm(sprintf($this->lang->bug->remindTask, $bug->toTask), $confirmURL, $cancelURL, 'parent', 'parent.parent'));
                }
            }

            $this->executeHooks($bugID);

            die(js::locate($this->session->bugList, 'parent'));
        }
    }

    /**
     * AJAX: get bugs of a user in html select.
     *
     * @param  int    $userID
     * @param  string $id       the id of the select control.
     * @access public
     * @return string
     */
    public function ajaxGetUserBugs($userID = '', $id = '')
    {
        if($userID == '') $userID = $this->app->user->id;
        $user    = $this->loadModel('user')->getById($userID, 'id');
        $account = $user->account;
        $bugs    = $this->bug->getUserBugPairs($account);

        if($id) die(html::select("bugs[$id]", $bugs, '', 'class="form-control"'));
        die(html::select('bug', $bugs, '', 'class=form-control'));
    }

    /**
     * AJAX: Get bug owner of a module.
     *
     * @param  int    $moduleID
     * @param  int    $productID
     * @access public
     * @return string
     */
    public function ajaxGetModuleOwner($moduleID, $productID = 0)
    {
        $account  = $this->bug->getModuleOwner($moduleID, $productID);
        $realName = '';
        if(!empty($account))
        {
            $user        = $this->dao->select('realname')->from(TABLE_USER)->where('account')->eq($account)->fetch();
            $firstLetter = ucfirst(substr($account, 0, 1)) . ':';
            if(!empty($this->config->isINT)) $firstLetter = '';
            $realName = $firstLetter . ($user->realname ? $user->realname : $account);
        }
        die(json_encode(array($account, $realName)));
    }

    /**
     * AJAX: get team members of the executions as assignedTo list.
     *
     * @param  int    $projectID
     * @param  string $selectedUser
     * @access public
     * @return string
     */
    public function ajaxLoadAssignedTo($projectID, $selectedUser = '')
    {
        $executionMembers = $this->user->getTeamMemberPairs($projectID, 'project', '', $selectedUser);

        die(html::select('assignedTo', $executionMembers, $selectedUser, 'class="form-control"'));
    }

    /**
     * AJAX: get team members of the latest executions of a product as assignedTo list.
     *
     * @param  int    $productID
     * @param  string $selectedUser
     * @access public
     * @return string
     */
    public function ajaxLoadExecutionTeamMembers($productID, $selectedUser = '')
    {
        $productMembers = $this->bug->getProductMemberPairs($productID);

        die(html::select('assignedTo', $productMembers, $selectedUser, 'class="form-control"'));
    }

    /**
     * AJAX: get all users as assignedTo list.
     *
     * @param  string $selectedUser
     * @access public
     * @return string
     */
    public function ajaxLoadAllUsers($selectedUser = '')
    {
        $allUsers = $this->loadModel('user')->getPairs('devfirst|noclosed');

        die(html::select('assignedTo', $allUsers, $selectedUser, 'class="form-control"'));
    }

    /**
     * AJAX: get actions of a bug. for web app.
     *
     * @param  int    $bugID
     * @access public
     * @return void
     */
    public function ajaxGetDetail($bugID)
    {
        $this->view->actions = $this->loadModel('action')->getList('bug', $bugID);
        $this->display();
    }

    /**
     * Ajax get bug by ID.
     *
     * @param  int    $bugID
     * @access public
     * @return void
     */
    public function ajaxGetByID($bugID)
    {
        $bug = $this->dao->select('*')->from(TABLE_BUG)->where('id')->eq($bugID)->fetch();
        $realname = $this->dao->select('*')->from(TABLE_USER)->where('account')->eq($bug->assignedTo)->fetch('realname');
        $bug->assignedTo = $realname ? $realname : ($bug->assignedTo == 'closed' ? 'Closed' : $bug->assignedTo);
        die(json_encode($bug));
    }

    /**
     * Ajax get bug filed options for auto test.
     *
     * @param  int    $productID
     * @param  int    $executionID
     * @access public
     * @return void
     */
    public function ajaxGetBugFieldOptions($productID, $executionID = 0)
    {
        $modules  = $this->loadModel('tree')->getOptionMenu($productID, 'bug');
        $builds   = $this->loadModel('build')->getExecutionBuildPairs($executionID, $productID);
        $type     = $this->lang->bug->typeList;
        $pri      = $this->lang->bug->priList;
        $severity = $this->lang->bug->severityList;

        die(json_encode(array('modules' => $modules, 'categories' => $type, 'versions' => $builds, 'severities' => $severity, 'priorities' => $pri)));
    }

    /**
     * Drop menu page.
     *
     * @param  int    $productID
     * @param  string $module
     * @param  string $method
     * @param  string $extra
     * @access public
     * @return void
     */
    public function ajaxGetDropMenu($productID, $module, $method, $extra = '')
    {
        $products = array();
        if(!empty($extra)) $products = $this->product->getProducts($extra, $this->config->CRProduct ? 'all' : 'noclosed', 'program desc, line desc, ');

        $this->view->link      = $this->product->getProductLink($module, $method, $extra);
        $this->view->productID = $productID;
        $this->view->module    = $module;
        $this->view->method    = $method;
        $this->view->extra     = $extra;
        $this->view->products  = $products;
        $this->view->projectID = $this->session->project;
        $this->view->programs  = $this->loadModel('program')->getPairs(true);
        $this->view->lines     = $this->product->getLinePairs();
        $this->display();
    }

    public function ajaxGetChildTypeList($type = '', $number = '')
    {

        if($number === '')
        {
            $list = $this->bug->getChildTypeList($type);
            die(html::select('childType', $list, '', 'class=form-control'));
        }
        else
        {
            $childTypeName = "childTypes[$number]";
            $childTypeList = $this->bug->getChildTypeList($type);
            die(html::select($childTypeName, $childTypeList, '', "class='form-control'"));
        }
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
    public function report($applicationID, $productID, $browseType, $branchID, $moduleID, $chartType = 'default')
    {
        /* Get product, then set menu. */
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

        $this->loadModel('report');
        $this->view->charts = array();

        if(!empty($_POST))
        {
            foreach($this->post->charts as $chart)
            {
                $chartFunc   = 'getDataOf' . $chart;
                $chartData   = $this->bug->$chartFunc();
                $chartOption = $this->lang->bug->report->$chart;
                if(!empty($chartType) and $chartType != 'default') $chartOption->type = $chartType;
                $this->bug->mergeChartOption($chart);

                $this->view->charts[$chart] = $chartOption;
                $this->view->datas[$chart]  = $this->report->computePercent($chartData);
            }
        }

        $this->view->title         = $this->applicationList[$applicationID] . $this->lang->colon . $this->lang->bug->common . $this->lang->colon . $this->lang->bug->reportChart;
        $this->view->applicationID = $applicationID;
        $this->view->productID     = $productID;
        $this->view->browseType    = $browseType;
        $this->view->branchID      = $branchID;
        $this->view->moduleID      = $moduleID;
        $this->view->chartType     = $chartType;
        $this->view->checkedCharts = $this->post->charts ? join(',', $this->post->charts) : '';
        $this->display();
    }

    /**
     * Get data to export
     *
     * @param  string $productID
     * @param  string $orderBy
     * @param  string $browseType
     * @param  int    $executionID | $projectID
     * @access public
     * @return void
     */
    public function export($applicationID, $productID, $orderBy, $browseType = '', $executionID = 0)
    {
        if($_POST)
        {
            $this->loadModel('file');
            $this->loadModel('branch');
            $this->loadModel('defect');
            $bugLang   = $this->lang->bug;
            $bugConfig = $this->config->bug;
            /* Create field lists. */
            $fields = $this->post->exportFields ? $this->post->exportFields : explode(',', $bugConfig->list->exportFields);
            foreach($fields as $key => $fieldName)
            {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = isset($bugLang->$fieldName) ? $bugLang->$fieldName : $fieldName;
                unset($fields[$key]);
            }

            $bugQuery   = $this->session->bugQueryCondition;
            $bugQuery = str_replace('delete', 't1.delete', $bugQuery);
            if (strpos($bugQuery, 'linkDefect')) {
                $bugQuery = str_replace('linkDefect', 't2.code', $bugQuery);
                $bugQuery = str_replace('`', '', $bugQuery);
            }else{
                $searchFieldConfig =  $bugConfig->search;
                $fieldsArr = $searchFieldConfig['fields'];
                foreach ($fieldsArr as $field=>$name){
                    if($field == 'product'){
                        $bugQuery = str_replace('product', 't1.product',$bugQuery);
                    }elseif($field == 'os'){
                        $bugQuery = $bugQuery;
                    }else{
                        $bugQuery = str_replace($field, 't1.'.$field,$bugQuery);
                    }
                }
            }
            /* Get bugs. */
//            $bugs = $this->dao->select('*')->from(TABLE_BUG)->where($this->session->bugQueryCondition)
//                ->beginIF($this->post->exportType == 'selected')->andWhere('id')->in($this->cookie->checkedItem)->fi()
//                ->orderBy($orderBy)
//                ->fetchAll('id');

            $bugs = $this->dao->select('t1.*,t2.id as defectId,t2.title as defectTitle,t2.code as defectCode')->from(TABLE_BUG)->alias('t1')
                ->leftJoin(TABLE_DEFECT)->alias('t2')->on('t1.id = t2.bugId')
                ->where($bugQuery)
                ->beginIF($this->post->exportType == 'selected')->andWhere('t1.id')->in($this->cookie->checkedItem)->fi()
                ->andWhere('t1.deleted')->eq(0)
                ->orderBy('t1.status,t1.id_desc')
                ->fetchAll('id');
                
            /* Get users, products and executions. */
            $users = $this->loadModel('user')->getPairs('noletter');

            /* Get related objects id lists. */
            $relatedProductIdList   = array();
            $relatedStoryIdList     = array();
            $relatedTaskIdList      = array();
            $relatedBugIdList       = array();
            $relatedCaseIdList      = array();
            $relatedBuildIdList     = array();
            $relatedProjectIdList   = array();
            $relatedExecutionIdList = array();

            foreach($bugs as $bug)
            {
                $relatedProjectIdList[$bug->project]     = $bug->project;
                $relatedExecutionIdList[$bug->execution] = $bug->execution;
                $relatedProductIdList[$bug->product]     = $bug->product;
                $relatedStoryIdList[$bug->story]         = $bug->story;
                $relatedTaskIdList[$bug->task]           = $bug->task;
                $relatedCaseIdList[$bug->case]           = $bug->case;
                $relatedBugIdList[$bug->duplicateBug]    = $bug->duplicateBug;

                /* Process link bugs. */
                $linkBugs = explode(',', $bug->linkBug);
                foreach($linkBugs as $linkBugID)
                {
                    if($linkBugID) $relatedBugIdList[$linkBugID] = trim($linkBugID);
                }

                /* Process builds. */
                $builds = $bug->openedBuild . ',' . $bug->resolvedBuild;
                $builds = explode(',', $builds);
                foreach($builds as $buildID)
                {
                    if($buildID) $relatedBuildIdList[$buildID] = trim($buildID);
                }
            }

            /* Get related objects title or names. */
            $products       = array(0 => $this->lang->naProduct) + $this->dao->select('id, name')->from(TABLE_PRODUCT)->where('id')->in($relatedProductIdList)->fetchPairs();
            $projects       = $this->dao->select('id,name')->from(TABLE_PROJECT) ->where('id')->in($relatedProjectIdList)->fetchPairs();
            $executions     = $this->dao->select('id,name')->from(TABLE_EXECUTION) ->where('id')->in($relatedExecutionIdList)->fetchPairs();
            $relatedStories = $this->dao->select('id,title')->from(TABLE_STORY) ->where('id')->in($relatedStoryIdList)->fetchPairs();
            $relatedTasks   = $this->dao->select('id, name')->from(TABLE_TASK)->where('id')->in($relatedTaskIdList)->fetchPairs();
            $relatedBugs    = $this->dao->select('id, title')->from(TABLE_BUG)->where('id')->in($relatedBugIdList)->fetchPairs();
            $relatedCases   = $this->dao->select('id, title')->from(TABLE_CASE)->where('id')->in($relatedCaseIdList)->fetchPairs();
            $relatedBuilds  = array('trunk' => $this->lang->trunk) + $this->dao->select('id, name')->from(TABLE_BUILD)->where('id')->in($relatedBuildIdList)->fetchPairs();
            $relatedPlan    = array('trunk' => $this->lang->trunk) + $this->dao->select('id, title')->from(TABLE_PRODUCTPLAN)->where('product')->in($relatedProductIdList)->fetchPairs();
            $relatedModules = $this->loadModel('tree')->getAllModulePairs('bug');
            $childTypeList  = $this->bug->getChildTypeTileList();

            $testtasks = $this->loadModel('testtask')->getPairs(0, 0, '', '', "oddNumber", $applicationID);

            foreach($bugs as $bug)
            {
                if($this->post->fileType == 'csv')
                {
                    $bug->steps = str_replace("<br />", "\n", $bug->steps);
                    $bug->steps = str_replace('"', '""', $bug->steps);
                    $bug->steps = str_replace('&nbsp;', ' ', $bug->steps);
                }

                /* fill some field with useful value. */
                $bug->applicationID = zget($this->applicationList, $bug->applicationID, '') . "(#{$applicationID})";
                $bug->product       = !isset($products[$bug->product])     ? '' : $products[$bug->product] . "(#$bug->product)";
                $bug->project       = !isset($projects[$bug->project])     ? '' : $projects[$bug->project] . "(#$bug->project)";
                $bug->execution     = !isset($executions[$bug->execution]) ? '' : $executions[$bug->execution] . "(#$bug->execution)";
                $bug->story         = !isset($relatedStories[$bug->story]) ? '' : $relatedStories[$bug->story] . "(#$bug->story)";
                $bug->task          = !isset($relatedTasks[$bug->task])    ? '' : $relatedTasks[$bug->task] . "($bug->task)";
                $bug->case          = !isset($relatedCases[$bug->case])    ? '' : $relatedCases[$bug->case] . "($bug->case)";

                if(isset($relatedModules[$bug->module]))       $bug->module        = $relatedModules[$bug->module] . "(#$bug->module)";
                if(isset($relatedBugs[$bug->duplicateBug]))    $bug->duplicateBug  = $relatedBugs[$bug->duplicateBug] . "($bug->duplicateBug)";
                if(isset($relatedBuilds[$bug->resolvedBuild])) $bug->resolvedBuild = $relatedBuilds[$bug->resolvedBuild] . "(#$bug->resolvedBuild)";

                if(isset($bugLang->priList[$bug->pri]))               $bug->pri        = $bugLang->priList[$bug->pri];
                if(isset($bugLang->typeList[$bug->type]))             $bug->type       = $bugLang->typeList[$bug->type];
                if(isset($bugLang->severityList[$bug->severity]))     $bug->severity   = $bugLang->severityList[$bug->severity];
                if(isset($bugLang->osList[$bug->os]))                 $bug->os         = $bugLang->osList[$bug->os];
                if(isset($bugLang->browserList[$bug->browser]))       $bug->browser    = $bugLang->browserList[$bug->browser];
                if(isset($bugLang->statusList[$bug->status]))         $bug->status     = $this->processStatus('bug', $bug);
                if(isset($bugLang->confirmedList[$bug->confirmed]))   $bug->confirmed  = $bugLang->confirmedList[$bug->confirmed];
                if(isset($bugLang->resolutionList[$bug->resolution])) $bug->resolution = $bugLang->resolutionList[$bug->resolution];

                if(isset($users[$bug->openedBy]))     $bug->openedBy     = $users[$bug->openedBy];
                if(isset($users[$bug->assignedTo]))   $bug->assignedTo   = $users[$bug->assignedTo];
                if(isset($users[$bug->resolvedBy]))   $bug->resolvedBy   = $users[$bug->resolvedBy];
                if(isset($users[$bug->lastEditedBy])) $bug->lastEditedBy = $users[$bug->lastEditedBy];
                if(isset($users[$bug->closedBy]))     $bug->closedBy     = $users[$bug->closedBy];

                $bug->title = htmlspecialchars_decode($bug->title,ENT_QUOTES);

                if($bug->linkBug)
                {
                    $tmpLinkBugs = array();
                    $linkBugIdList = explode(',', $bug->linkBug);
                    foreach($linkBugIdList as $linkBugID)
                    {
                        $linkBugID = trim($linkBugID);
                        $tmpLinkBugs[] = isset($relatedBugs[$linkBugID]) ? $relatedBugs[$linkBugID] : $linkBugID;
                    }
                    $bug->linkBug = join("; \n", $tmpLinkBugs);
                }

                if($bug->openedBuild)
                {
                    $tmpOpenedBuilds   = array();
                    $tmpResolvedBuilds = array();
                    $buildIdList = explode(',', $bug->openedBuild);
                    foreach($buildIdList as $buildID)
                    {
                        $buildID = trim($buildID);
                        $tmpOpenedBuilds[] = isset($relatedBuilds[$buildID]) ? $relatedBuilds[$buildID] . "(#$buildID)" : $buildID;
                    }
                    $bug->openedBuild = join("\n", $tmpOpenedBuilds);
                    if($this->post->fileType == 'html') $bug->openedBuild = nl2br($bug->openedBuild);
                }

                if($bug->linkPlan)
                {
                    $tmpOpenedPlans   = array();
                    $tmpResolvedPlans = array();

                    $planList = explode(',', $bug->linkPlan);
                    foreach($planList as $planId)
                    {
                        $planId = trim($planId);
                        $tmpOpenedPlans[] = isset($relatedPlan[$planId]) ? $bug->product.'_'.$relatedPlan[$planId] . "(#$planId)" : $planId;
                    }
                    $bug->linkPlan = join("\n", $tmpOpenedPlans);
                    if($this->post->fileType == 'html') $bug->linkPlan = nl2br($bug->linkPlan);
                }

                $bug->mailto = trim(trim($bug->mailto), ',');
                $mailtos     = explode(',', $bug->mailto);
                $bug->mailto = '';
                foreach($mailtos as $mailto)
                {
                    $mailto = trim($mailto);
                    if(isset($users[$mailto])) $bug->mailto .= $users[$mailto] . ',';
                }
                $bug->mailto = rtrim($bug->mailto, ',');
                $bug->childType = zget($childTypeList, $bug->childType, '');
                $defect = $this->dao->select('id,code')->from(TABLE_DEFECT)->where('bugId')->eq($bug->id)->fetch();
                $bug->defectId = $defect->code ?? '';

                $bug->linkTesttask = zmget($testtasks, $bug->linkTesttask);

                unset($bug->caseVersion);
                unset($bug->result);
                unset($bug->deleted);
            }

            if(isset($this->config->bizVersion)) list($fields, $bugs) = $this->loadModel('workflowfield')->appendDataFromFlow($fields, $bugs);

            $width['applicationID'] = 30;
            $width['project']       = 30;
            $this->post->set('width', $width);
            $this->post->set('fields', $fields);
            $this->post->set('rows', $bugs);
            $this->post->set('kind', 'bug');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        $fileName = $this->lang->bug->common;
        $title    = '';
        if($executionID)
        {
            $executionName = $this->dao->findById($executionID)->from(TABLE_EXECUTION)->fetch('name');
            $title .= $executionName;

            $applicationName = '';
            $productName     = '';
            if($applicationID)
            {
                $applicationName = $this->dao->findById($applicationID)->from(TABLE_APPLICATION)->fetch('name');
                $title .= $this->lang->dash . $applicationName;
            }

            if($productID != 'all')
            {
                if($productID)
                {
                    $productName = $this->dao->findById($productID)->from(TABLE_PRODUCT)->fetch('name');
                    $title .= $this->lang->dash . $productName;
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

        $browseType = isset($this->lang->bug->featureBar['browse'][$browseType]) ? $this->lang->bug->featureBar['browse'][$browseType] : zget($this->lang->bug->moreSelects, $browseType, '');

        $fileName = $title . $this->lang->dash . $browseType . $fileName;

        $this->view->fileName        = $fileName;
        $this->view->allExportFields = $this->config->bug->list->exportFields;
        $this->view->customExport    = true;
        $this->display();
    }
}
