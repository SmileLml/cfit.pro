<?php
/**
 * The control file of dashboard module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     dashboard
 * @version     $Id: control.php 5020 2013-07-05 02:03:26Z wyd621@gmail.com $
 * @link        http://www.zentao.net
 */
class my extends control
{
    /**
     * Construct function.
     *
     * @access public
     * @return void
     */
    public function __construct($module = '', $method = '')
    {
        parent::__construct($module, $method);
        $this->loadModel('user');
        $this->loadModel('dept');
    }

    /**
     * Index page, goto todo.
     *
     * @access public
     * @return void
     */
    public function index()
    {
        $this->view->title = $this->lang->my->common;

        $result=$this->my->queryPublish($this->app->user->id);
        //根据当前用户是否存在未通知的公告来判断是否需要弹框
        $notice="yes";
        if(empty($result)){
            $notice="no";
        }
        $this->view->notice=$notice;

        $this->display();
    }

    /**
     * Get score list
     *
     * @param int $recTotal
     * @param int $recPerPage
     * @param int $pageID
     *
     * @access public
     * @return mixed
     */
    public function score($recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $this->app->loadClass('pager', $static = true);
        $pager  = new pager($recTotal, $recPerPage, $pageID);
        $scores = $this->loadModel('score')->getListByAccount($this->app->user->account, $pager);

        $this->view->title      = $this->lang->score->common;
        $this->view->user       = $this->loadModel('user')->getById($this->app->user->account);
        $this->view->pager      = $pager;
        $this->view->scores     = $scores;
        $this->view->position[] = $this->lang->score->record;

        $this->display();
    }

    /**
     * My calendar.
     *
     * @access public
     * @return void
     */
    public function calendar()
    {
        $this->locate($this->createLink('my', 'todo', 'type=before&userID=&status=undone'));
    }

    /**
     * My work view.
     *
     * @param  string $mode
     * @param  string $type
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function work($mode = 'task', $type = 'assignedTo', $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        echo $this->fetch('my', $mode, "type=$type&orderBy=$orderBy&recTotal=$recTotal&recPerPage=$recPerPage&pageID=$pageID");
    }

    /**
     * My contribute view.
     *
     * @param  string $mode
     * @param  string $type
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function contribute($mode = 'task', $type = 'openedBy', $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        if(($mode == 'issue' or $mode == 'risk') and $type == 'openedBy') $type = 'createdBy';

        echo $this->fetch('my', $mode, "type=$type&orderBy=$orderBy&recTotal=$recTotal&recPerPage=$recPerPage&pageID=$pageID");
    }

    /**
     * My todos.
     *
     * @param  string $type
     * @param  int    $userID
     * @param  string $status
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function todo($type = 'before', $userID = '', $status = 'all', $orderBy = "date_desc,status,begin", $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        /* Save session. */
        $uri = $this->app->getURI(true);
        $this->session->set('todoList',     $uri, 'my');
        $this->session->set('bugList',      $uri, 'qa');
        $this->session->set('taskList',     $uri, 'execution');
        $this->session->set('storyList',    $uri, 'product');
        $this->session->set('testtaskList', $uri, 'qa');

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        if($this->app->getViewType() == 'mhtml') $recPerPage = 10;
        $pager = pager::init($recTotal, $recPerPage, $pageID);

        if(empty($userID)) $userID = $this->app->user->id;
        $user    = $this->loadModel('user')->getById($userID, 'id');
        $account = $user->account;

        /* The title and position. */
        $this->view->title      = $this->lang->my->common . $this->lang->colon . $this->lang->my->todo;
        $this->view->position[] = $this->lang->my->todo;

        /* Append id for secend sort. */
        $sort = $this->loadModel('common')->appendOrder($orderBy);

        /* Assign. */
        $this->view->todos        = $this->loadModel('todo')->getList($type, $account, $status, 0, $pager, $sort);
        $this->view->date         = (int)$type == 0 ? date(DT_DATE1) : date(DT_DATE1, strtotime($type));
        $this->view->type         = $type;
        $this->view->recTotal     = $recTotal;
        $this->view->recPerPage   = $recPerPage;
        $this->view->pageID       = $pageID;
        $this->view->status       = $status;
        $this->view->user         = $user;
        $this->view->users        = $this->loadModel('user')->getPairs('noletter');
        $this->view->account      = $this->app->user->account;
        $this->view->orderBy      = $orderBy == 'date_desc,status,begin,id_desc' ? '' : $orderBy;
        $this->view->pager        = $pager;
        $this->view->times        = date::buildTimeList($this->config->todo->times->begin, $this->config->todo->times->end, $this->config->todo->times->delta);
        $this->view->time         = date::now();
        $this->view->importFuture = ($type != 'today');

        $this->display();
    }

    /**
     * My stories.
     *
     * @param  string $type
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function story($type = 'assignedTo', $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        /* Save session. */
        if($this->app->viewType != 'json') $this->session->set('storyList', $this->app->getURI(true), 'product');

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        if($this->app->getViewType() == 'mhtml') $recPerPage = 10;
        $pager = pager::init($recTotal, $recPerPage, $pageID);

        /* Append id for secend sort. */
        $sort = $this->loadModel('common')->appendOrder($orderBy);

        /* Assign. */
        $this->view->title      = $this->lang->my->common . $this->lang->colon . $this->lang->my->story;
        $this->view->position[] = $this->lang->my->story;
        $this->view->stories    = $this->loadModel('story')->getUserStories($this->app->user->account, $type, $sort, $pager, 'story');
        $this->view->users      = $this->user->getPairs('noletter');
        $this->view->projects   = $this->loadModel('project')->getPairsByProgram();
        $this->view->type       = $type;
        $this->view->recTotal   = $recTotal;
        $this->view->recPerPage = $recPerPage;
        $this->view->pageID     = $pageID;
        $this->view->orderBy    = $orderBy;
        $this->view->pager      = $pager;
        $this->view->mode       = 'story';

        $this->display();
    }

    /**
     * My requirements.
     *
     * @param  string $type
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function requirement($type = 'assignedTo', $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        /* Save session. */
        if($this->app->viewType != 'json') $this->session->set('storyList', $this->app->getURI(true), 'product');

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        if($this->app->getViewType() == 'mhtml') $recPerPage = 10;
        $pager = pager::init($recTotal, $recPerPage, $pageID);

        /* Append id for secend sort. */
        $sort = $this->loadModel('common')->appendOrder($orderBy);
        /* Assign. */
        $this->view->title      = $this->lang->my->common . $this->lang->colon . $this->lang->my->story;
        $this->view->position[] = $this->lang->my->story;
        $this->view->stories    = $this->loadModel('story')->getUserStories($this->app->user->account, $type, $sort, $pager, 'requirement');
        $this->view->users      = $this->user->getPairs('noletter');
        $this->view->projects   = $this->loadModel('project')->getPairsByProgram();
        $this->view->type       = $type;
        $this->view->recTotal   = $recTotal;
        $this->view->recPerPage = $recPerPage;
        $this->view->pageID     = $pageID;
        $this->view->orderBy    = $orderBy;
        $this->view->pager      = $pager;
        $this->view->mode       = 'requirement';

        $this->display();
    }

    /**
     * My tasks
     *
     * @param  string $type
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function task($type = 'assignedTo', $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        /* Save session. */
        if($this->app->viewType != 'json') $this->session->set('taskList', $this->app->getURI(true), 'execution');

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        if($this->app->getViewType() == 'mhtml') $recPerPage = 10;
        $pager = pager::init($recTotal, $recPerPage, $pageID);

        /* append id for secend sort. */
        $sort = $this->loadModel('common')->appendOrder($orderBy);

        /* Get tasks. */
        $tasks = $this->loadModel('task')->getUserTasks($this->app->user->account, $type, 0, $pager, $sort);

        $parents = array();
        foreach($tasks as $task)
        {
            if($task->parent > 0) $parents[$task->parent] = $task->parent;
        }
        $parents = $this->dao->select('*')->from(TABLE_TASK)->where('id')->in($parents)->fetchAll('id');

        foreach($tasks as $task)
        {
            if($task->parent > 0)
            {
                if(isset($tasks[$task->parent]))
                {
                    $tasks[$task->parent]->children[$task->id] = $task;
                    unset($tasks[$task->id]);
                }
                else
                {
                    $parent = $parents[$task->parent];
                    $task->parentName = $parent->name;
                }
            }
        }

        /* Get the story language configuration. */
        $this->app->loadLang('story');

        /* Assign. */
        $this->view->title      = $this->lang->my->common . $this->lang->colon . $this->lang->my->task;
        $this->view->position[] = $this->lang->my->task;
        $this->view->tabID      = 'task';
        $this->view->tasks      = $tasks;
        $this->view->summary    = $this->loadModel('execution')->summary($tasks);
        $this->view->type       = $type;
        $this->view->recTotal   = $recTotal;
        $this->view->recPerPage = $recPerPage;
        $this->view->pageID     = $pageID;
        $this->view->orderBy    = $orderBy;
        $this->view->projects   = $this->loadModel('project')->getPairsByProgram();
        $this->view->users      = $this->loadModel('user')->getPairs('noletter');
        $this->view->pager      = $pager;
        $this->view->mode       = 'task';

        if($this->app->viewType == 'json') $this->view->tasks = array_values($this->view->tasks);
        $this->display();
    }

    /**
     * My bugs.
     *
     * @param  string $type
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function bug($type = 'assignedTo', $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        /* Save session. load Lang. */
        if($this->app->viewType != 'json') $this->session->set('bugList', $this->app->getURI(true), 'qa');
        $this->app->loadLang('bug');

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        if($this->app->getViewType() == 'mhtml') $recPerPage = 10;
        $pager = pager::init($recTotal, $recPerPage, $pageID);

        /* Append id for secend sort. */
        $sort = $this->loadModel('common')->appendOrder($orderBy);
        $bugs = $this->loadModel('bug')->getUserBugs($this->app->user->account, $type, $sort, 0, $pager);
        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'bug');

        /* assign. */
        $this->view->title       = $this->lang->my->common . $this->lang->colon . $this->lang->my->bug;
        $this->view->position[]  = $this->lang->my->bug;
        $this->view->bugs        = $bugs;
        $this->view->users       = $this->user->getPairs('noletter');
        $this->view->memberPairs = $this->user->getPairs('noletter|nodeleted');
        $this->view->tabID       = 'bug';
        $this->view->type        = $type;
        $this->view->recTotal    = $recTotal;
        $this->view->recPerPage  = $recPerPage;
        $this->view->pageID      = $pageID;
        $this->view->orderBy     = $orderBy;
        $this->view->pager       = $pager;
        $this->view->mode        = 'bug';
        $this->view->typeTileList = $this->bug->getChildTypeTileList();

        $this->display();
    }

    /**
     * My test task.
     *
     * @param  string $type wait|done
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function testtask($type = 'wait', $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = pager::init($recTotal, $recPerPage, $pageID);

        /* Save session. */
        if($this->app->viewType != 'json') $this->session->set('testtaskList', $this->app->getURI(true), 'qa');

        $this->app->loadLang('testcase');

        /* Append id for secend sort. */
        $sort  = $this->loadModel('common')->appendOrder($orderBy);
        $tasks = $this->loadModel('testtask')->getByUser($this->app->user->account, $pager, $sort, $type);

        $this->view->title      = $this->lang->my->common . $this->lang->colon . $this->lang->my->testTask;
        $this->view->position[] = $this->lang->my->testTask;
        $this->view->tasks      = $tasks;

        $this->view->recTotal   = $recTotal;
        $this->view->recPerPage = $recPerPage;
        $this->view->pageID     = $pageID;
        $this->view->orderBy    = $orderBy;
        $this->view->type       = $type;
        $this->view->pager      = $pager;
        $this->view->mode       = 'testtask';
        $this->display();

    }

    /**
     * My test case.
     *
     * @param  string $type      assigntome|openedbyme
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function testcase($type = 'assigntome', $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $this->loadModel('testcase');
        $this->loadModel('testtask');

        /* Save session. */
        $this->session->set('caseList', $this->app->getURI(true), 'qa');

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = pager::init($recTotal, $recPerPage, $pageID);

        /* Append id for secend sort. */
        $sort = $this->loadModel('common')->appendOrder($orderBy);

        $cases = array();
        if($type == 'assigntome')
        {
            $cases = $this->testcase->getByAssignedTo($this->app->user->account, $sort, $pager, '', 'my');
        }
        elseif($type == 'openedbyme')
        {
            $cases = $this->testcase->getByOpenedBy($this->app->user->account, $sort, $pager, '');
        }
        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'testcase', $type == 'assigntome' ? false : true);

        $cases = $this->testcase->appendData($cases, $type == 'assigntome' ? 'run' : 'case');

        $applicationIdList = array();
        foreach($cases as $case) $applicationIdList[] = $case->applicationID;
        $applicationPairs = $this->dao->select('id,name')->from(TABLE_APPLICATION)->where('id')->in($applicationIdList)->fetchPairs();

        /* Assign. */
        $this->view->title            = $this->lang->my->common . $this->lang->colon . $this->lang->my->testCase;
        $this->view->position[]       = $this->lang->my->testCase;
        $this->view->cases            = $cases;
        $this->view->applicationPairs = $applicationPairs;
        $this->view->users            = $this->user->getPairs('noletter');
        $this->view->tabID            = 'test';
        $this->view->type             = $type;
        $this->view->summary          = $this->testcase->summary($cases);
        $this->view->recTotal         = $recTotal;
        $this->view->recPerPage       = $recPerPage;
        $this->view->pageID           = $pageID;
        $this->view->orderBy          = $orderBy;
        $this->view->pager            = $pager;
        $this->view->mode             = 'testcase';
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: doc
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:48
     * Desc: This is the code comment. This method is called doc.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param string $type
     * @param string $orderBy
     * @param int $recTotal
     * @param int $recPerPage
     * @param int $pageID
     */
    public function doc($type = 'openedbyme', $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        /* Save session, load lang. */
        if($this->app->viewType != 'json') $this->session->set('docList', $this->app->getURI(true), 'doc');
        $this->loadModel('doc');

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = pager::init($recTotal, $recPerPage, $pageID);

        /* Append id for secend sort. */
        $sort = $this->loadModel('common')->appendOrder($orderBy);

        $docs = $this->doc->getDocsByBrowseType($type, 0, 0, $sort, $pager);

        /* Assign. */
        $this->view->title      = $this->lang->my->common . $this->lang->colon . $this->lang->my->doc;
        $this->view->position[] = $this->lang->my->doc;
        $this->view->docs       = $docs;
        $this->view->users      = $this->user->getPairs('noletter');
        $this->view->type       = $type;
        $this->view->recTotal   = $recTotal;
        $this->view->recPerPage = $recPerPage;
        $this->view->pageID     = $pageID;
        $this->view->orderBy    = $orderBy;
        $this->view->pager      = $pager;

        $this->display();

    }

    /**
     * My projects.
     *
     * @param  string  $status doing|wait|suspended|closed|openedbyme
     * @param  int     $recTotal
     * @param  int     $recPerPage
     * @param  int     $pageID
     * @access public
     * @return void
     */
    public function project($status = 'doing', $recTotal = 0, $recPerPage = 15, $pageID = 1)
    {
        $this->loadModel('program');
        $this->app->loadLang('project');

        $this->app->session->set('programList', $this->app->getURI(true), 'program');
        $this->app->session->set('projectList', $this->app->getURI(true), 'project');

        /* Set the pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        /* Get PM id list. */
        $accounts = array();
        $projects = $this->user->getExecutions($this->app->user->account, 'project', $status, 'id_desc', $pager);
        foreach($projects as $project)
        {
            if(!empty($project->PM) and !in_array($project->PM, $accounts)) $accounts[] = $project->PM;
        }
        $PMList = $this->user->getListByAccounts($accounts, 'account');

        $this->view->title      = $this->lang->my->common . $this->lang->colon . $this->lang->my->project;
        $this->view->position[] = $this->lang->my->project;
        $this->view->users      = $this->loadModel('user')->getPairs('noletter');
        $this->view->projects   = $projects;
        $this->view->PMList     = $PMList;
        $this->view->pager      = $pager;
        $this->view->status     = $status;
        $this->display();
    }

    /**
     * My executions.
     * @param  string  $type undone|done
     * @param  string  $orderBy
     * @param  int     $recTotal
     * @param  int     $recPerPage
     * @param  int     $pageID
     *
     * @access public
     * @return void
     */
    public function execution($type = 'undone', $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 15, $pageID = 1)
    {
        $this->app->loadLang('project');
        $this->app->loadLang('execution');

        /* Set the pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $this->view->title      = $this->lang->my->common . $this->lang->colon . $this->lang->my->execution;
        $this->view->position[] = $this->lang->my->execution;
        $this->view->tabID      = 'project';
        $this->view->executions = $this->user->getExecutions($this->app->user->account, 'execution', $type, $orderBy, $pager);
        $this->view->type       = $type;
        $this->view->pager      = $pager;
        $this->view->mode       = 'execution';

        $this->display();
    }

    /**
     * My issues.
     *
     * @access public
     * @param  string $type
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @return void
     */
    public function issue($type = 'assignedTo', $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        /* Set the pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $this->app->session->set('issueList', $this->app->getURI(true), 'project');

        $this->view->title      = $this->lang->my->issue;
        $this->view->position[] = $this->lang->my->issue;
        $this->view->mode       = 'issue';
        $this->view->users      = $this->loadModel('user')->getPairs('noclosed|noletter');
        $this->view->orderBy    = $orderBy;
        $this->view->pager      = $pager;
        $this->view->type       = $type;
        $this->view->issues     = $this->loadModel('issue')->getUserIssues($type, $this->app->user->account, $orderBy, $pager);
        $this->display();
    }

    /**
     * My risks.
     *
     * @access public
     * @param  string $type
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @return void
     */
    public function risk($type = 'assignedTo', $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        /* Set the pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $this->app->session->set('riskList', $this->app->getURI(true), 'project');

        $this->view->title      = $this->lang->my->risk;
        $this->view->position[] = $this->lang->my->risk;
        $this->view->risks      = $this->loadModel('risk')->getUserRisks($type, $this->app->user->account, $orderBy, $pager);
        $this->view->users      = $this->loadModel('user')->getPairs('noclosed|noletter');
        $this->view->orderBy    = $orderBy;
        $this->view->pager      = $pager;
        $this->view->type       = $type;
        $this->view->mode       = 'risk';
        $this->display();
    }

    /**
     * My audits.
     *
     * @param  string $browseType
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function audit($browseType = 'modify', $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 15, $pageID = 1)
    {
        /* 工作流配置额外的菜单 */
        $flowPairs = $this->loadModel('customflow')->getFlowPairs();
        $customFlowList = array_keys($flowPairs);
        foreach($flowPairs as $flowCode => $flowName)
        {
            $this->lang->my->myReviewList[$flowCode] = $flowName;
        }

        if(in_array($browseType, $customFlowList))
        {
            /* 工作流返回链接处理。*/
            $sessionName = $browseType . 'List';
            $this->session->set($sessionName, $this->createLink($browseType, 'browse'));

            /* 获取工作流字段 */
            $fields      = $this->dao->select('*')->from(TABLE_WORKFLOWFIELD)->where('module')->eq($browseType)->fetchAll();
            $titleList   = array();
            $controlList = array();
            $optionList  = array();

            $this->loadModel('workflowfield');
            $this->loadModel('flow');
            foreach($fields as $field)
            {
                $titleList[$field->field]   = $field->name;
                $controlList[$field->field] = $field->control;
                $optionList[$field->field]  = $field->options;

                if(in_array($field->control, $this->config->workflowfield->optionControls))
                {
                    $field = $this->workflowfield->processFieldOptions($field);
                    $data  = $this->workflowfield->getFieldOptions($field, true, '', '', $this->config->flowLimit);
                    $optionList[$field->field] = empty($data) ? $field->options : $data;
                }
            }

            /* 获取工作流列表显示字段 */
            $showFields = $this->dao->select('*')->from(TABLE_WORKFLOWLAYOUT)->where('module')->eq($browseType)->andWhere('action')->eq('browse')->orderBy('order_asc')->fetchAll();

            $flowList = $this->customflow->getFlowList();

            /* 获取工作流列表待处理数据 */
            $pendingField = empty($flowList[$browseType]['flowAssign']) ? 'assignedBy' : $flowList[$browseType]['flowAssign'];
            $showDatas = $this->dao->select('*')->from('zt_flow_' . $browseType)->where($pendingField)->eq($this->app->user->account)->andWhere('deleted')->eq('0')->fetchAll();
            
            $this->view->titleList    = $titleList;
            $this->view->controlList  = $controlList;
            $this->view->optionList   = $optionList;
            $this->view->queryControl = array('select', 'multi-select', 'radio', 'checkbox');

            $this->view->title      = $this->lang->my->myReview;
            $this->view->fields     = $fields;
            $this->view->showFields = $showFields;
            $this->view->showDatas  = $showDatas;
            $this->view->browseType = $browseType;
            $this->view->mode       = 'audit';
            $this->view->users      = $this->loadModel('user')->getPairs('noclosed|noletter');
            $this->view->flowList   = $flowList;
            $this->display('my', 'auditflow');
        }
        else
        {
            // 判断是那个对象，根据对象加载语言项。
            if($browseType == 'wait') $browseType = 'opinion';

            // 不要移动这个位置
            $lang = $browseType;
            if($browseType == 'fix' or $browseType == 'gain') {
                $lang = 'info';
            }else if($browseType == 'fixqz' or $browseType == 'gainqz') {
                $lang = 'infoqz';
            }

            $this->app->loadLang($lang);

            $this->app->loadClass('pager', true);
            $pager = pager::init($recTotal, $recPerPage, $pageID);

            // 按照不同评审对象查询数据。
            $reviewList = array();
            if($browseType == 'copyrightqz'){
                $this->app->loadLang('copyrightqz');
                $reviewList = $this->my->getUserCopyrightqzList($orderBy);
                $this->view->users = $this->loadModel('user')->getPairs('noclosed|noletter');
            }
            if($browseType == 'copyright'){
                $this->app->loadLang('copyright');
                $reviewList = $this->my->getUserCopyrightList($orderBy);
                $this->view->users = $this->loadModel('user')->getPairs('noclosed|noletter');
            }
            if($browseType == 'putproduction') {
                $reviewList = $this->my->getUserPutproductionList($orderBy);
                if($reviewList){
                    $this->view->users = $this->loadModel('user')->getPairs('noclosed|noletter');
                    $outsideProjectList =  array('' => '') + $this->loadModel('outsideplan')->getPairs();//(外部)项目/任务
                    $this->view->outsideProjectList = $outsideProjectList;
                }
            }
            if($browseType == 'cmdbsync') {
                $data = $this->my->getUserCmdbsyncList($orderBy);
                if($data){
                    $this->view->users = $this->loadModel('user')->getPairs('noclosed|noletter');
                    $appList =  $this->loadModel('application')->getapplicationNameCodePairs();
                    $this->view->appList = $appList;
                    $this->view->data = $data;
                }
            }
            if($browseType == 'credit') {
                $reviewList = $this->my->getUserCreditList($orderBy);
                if($reviewList){
                    $users = $this->loadModel('user')->getPairs('noletter');
                    $projectList     =  $this->loadModel('projectplan')->getAllProjects();
                    $this->view->users = $users;
                    $this->view->projectList = $projectList;

                }
            }
            if($browseType == 'cmdbsync') {
                $data = $this->my->getUserCmdbsyncList($orderBy);
                if($data){
                    $this->view->users = $this->loadModel('user')->getPairs('noclosed|noletter');
                    $appList =  $this->loadModel('application')->getPairsAll();
                    $this->view->appList = $appList;
                    $this->view->data = $data;
                }
            }
            if($browseType == 'modify') {
                $reviewList = $this->my->getUserModifyList($orderBy);
                $this->view->users = $this->loadModel('user')->getPairs('noclosed|noletter');
                $this->view->projectList = $this->loadModel('projectplan')->getAllProjects();
            }
            if($browseType == 'modifycncc'){
                $reviewList = $this->my->getUserModifycnccList($orderBy);
                $this->view->users = $this->loadModel('user')->getPairs('noclosed|noletter');
            }
            if($browseType == 'outwarddelivery'){
                $this->loadModel('outwarddelivery');
                $reviewList = $this->my->getUserOutwardDeliveryList($orderBy);
                $this->view->users = $this->loadModel('user')->getPairs('noclosed|noletter');
                $apps  = $this->loadModel('application')->getapplicationNameCodePairsWithisPayment();
                $appList = array();
                foreach($apps as $app){
                    $appList[$app->id] = $app->name;
                }
                foreach ($reviewList as $item){
                    $apps = array();
                    foreach(explode(',',$item->app)  as $app){
                        if(!empty($app)){
                            $apps[] = zget($appList,$app);
                        }
                    }
                    $item->app = implode('，',$apps);
                    $totalReturn    = 0;
                    $childrenCode   = array();
                    $item->totalReturn  = $totalReturn;
                    $item->childrenCode = implode(',', $childrenCode);
                    $item->currentReview = zget($this->lang->outwarddelivery->currentReviewList,$item->currentReview);
                }

            }
            if($browseType == 'fix')    $reviewList = $this->my->getUserFixList($orderBy);
            if($browseType == 'gain')   $reviewList = $this->my->getUserGainList($orderBy);
            if($browseType == 'gainqz'){
                $reviewList = $this->my->getUserGainqzList($orderBy);
                $this->view->users = $this->loadModel('user')->getPairs('noclosed|noletter');
            }
            if($browseType == 'change') {
                $reviewList = $this->my->getUserChangeList($orderBy);
                $this->view->users      = $this->loadModel('user')->getPairs('noletter|noclosed');
            }elseif($browseType == 'projectplan') {
                $reviewList = $this->my->getUserProjectplanList($orderBy);
            }elseif($browseType == 'projectplansh') {
                $this->app->loadLang('projectplan');
                $this->app->loadLang('projectplansh');
                $reviewList = $this->my->getUserProjectplanShList($orderBy);
            }
            elseif($browseType == 'defect') {
                $this->app->loadLang('bug');
                $reviewList = $this->my->getUserDefectList($orderBy,'all');
                $products  = $this->loadModel('product')->getSimplePairs();
                $this->view->products      = $products;
                $this->view->projects      = $this->loadModel('project')->getProjects();
                $this->view->users = $this->loadModel('user')->getPairs('noclosed|noletter');
            }
            elseif($browseType == 'projectplanChange') {
                $this->app->loadLang('projectplan');
                $reviewList = $this->my->getUserProjectplanChangeList($orderBy);
            }elseif($browseType == 'projectplanStart') { //我的待立项
                $this->app->loadLang('projectplan');
                $reviewList = $this->my->getUserProjectplanStartList($orderBy);
                $this->view->users = $this->loadModel('user')->getPairs('noclosed|noletter');
            }elseif($browseType == 'projectplanshChange') {
                $this->app->loadLang('projectplan');
                $shanghaiDeptList = $this->loadModel('dept')->getAllChildId(30); //获取所有上海部门
                $reviewList = $this->my->getUserProjectplanChangeList($orderBy,$shanghaiDeptList);
            }elseif($browseType == 'projectplanshStart') { //我的待立项
                $this->app->loadLang('projectplan');
                $shanghaiDeptList = $this->loadModel('dept')->getAllChildId(30); //获取所有上海部门
                $reviewList = $this->my->getUserProjectplanStartList($orderBy,$shanghaiDeptList);
                $this->view->users = $this->loadModel('user')->getPairs('noclosed|noletter');
            }elseif($browseType == 'requirement') {
                $this->app->loadLang('demand');
                $requirements = $this->my->getUserRequirementList($orderBy);
                $this->view->users = $this->loadModel('user')->getPairs('noclosed|noletter');
                $this->view->projects = $this->loadModel('projectplan')->getPairs();
                foreach ($requirements as $id => $requirement){
                    if(strstr($requirement->ignoredBy, $this->app->user->account) !== false && $requirement->ignoreStatus == 1){
                        $reviewListIgnore[] = $requirement;
                    } else {
                        $reviewList[] = $requirement;
                    }
                    $demandsOther = $this->loadModel('requirement')->getDemandByRequirement($requirement->id);
                    $ownProjectArr = !empty($requirement->project) ? explode(',',$requirement->project): [];
                    $demandProjectArr = array_column($demandsOther,'project');
                    $mergeProjectArr = array_merge($ownProjectArr,$demandProjectArr);
                    /**@var projectplanModel $projectPlanModel */
                    $projectPlanModel = $this->loadModel('projectplan');
                    $projectArray = array_filter(array_unique($mergeProjectArr));
                    if(!empty($projectArray)){
                        $projectList = $projectPlanModel->getPlanInProjectIDs($projectArray);
                        if($projectList){
                            $arr = [];
                            $projectStr = '';
                            foreach ($projectList as $v){
                                $arr[] = $v->id;
                            }
                            $projectStr = implode(',',$arr);
                            $requirement->project = $projectStr;
                        }
                    }
                    $acceptUserArr = array_filter(array_unique(array_column($demandsOther,'acceptUser')));
                    $acceptDeptArr = array_filter(array_unique(array_column($demandsOther,'acceptDept')));
                    $requirement->owner = implode(',',$acceptUserArr);
                    $requirement->dept = implode(',',$acceptDeptArr);

                    //变更单下一节点处理人
                    $changeInfo = $this->loadModel('requirement')->getPendingOrderByRequirementId($requirement->id);
                    $requirement->changeNextDealuser = $changeInfo->nextDealUser ?? '';

                }
                $suspender = array_filter($this->lang->demand->requirementSuspendList);
                $suspendList = [];
                if(!empty($suspender))
                {
                    foreach ($suspender as $key=>$value){
                        $suspendList[$key] = $key;
                    }
                }
                $this->view->executives   = $suspendList ? array_keys($suspendList): [];

            }elseif($browseType == 'requirementinside') {
                $requirements = $this->my->getUserRequirementInsideList($orderBy);
                $this->view->users = $this->loadModel('user')->getPairs('noclosed|noletter');
                $this->view->projects = $this->loadModel('projectplan')->getPairs();
                foreach ($requirements as $id => $requirement){
                    if(strstr($requirement->ignoredBy, $this->app->user->account) !== false){
                        $reviewListIgnore[] = $requirement;
                    } else {
                        $reviewList[] = $requirement;
                    }
                    $demandsOther = $this->loadModel('requirement')->getDemandByRequirement($requirement->id);
                    $ownProjectArr = !empty($requirement->project) ? explode(',',$requirement->project): [];
                    $demandProjectArr = array_column($demandsOther,'project');
                    $mergeProjectArr = array_merge($ownProjectArr,$demandProjectArr);
                    /**@var projectplanModel $projectPlanModel */
                    $projectPlanModel = $this->loadModel('projectplan');
                    $projectArray = array_filter(array_unique($mergeProjectArr));
                    if(!empty($projectArray)){
                        $projectList = $projectPlanModel->getPlanInProjectIDs($projectArray);
                        if($projectList){
                            $arr = [];
                            $projectStr = '';
                            foreach ($projectList as $v){
                                $arr[] = $v->id;
                            }
                            $projectStr = implode(',',$arr);
                            $requirement->project = $projectStr;
                        }
                    }
                    $acceptUserArr = array_filter(array_unique(array_column($demandsOther,'acceptUser')));
                    $acceptDeptArr = array_filter(array_unique(array_column($demandsOther,'acceptDept')));
                    $requirement->owner = implode(',',$acceptUserArr);
                    $requirement->dept = implode(',',$acceptDeptArr);
                }

                $suspender = array_filter($this->lang->demand->requirementSuspendList);
                $suspendList = [];
                if(!empty($suspender))
                {
                    foreach ($suspender as $key=>$value){
                        $suspendList[$key] = $key;
                    }
                }
                $this->view->executives   = $suspendList ? array_keys($suspendList): [];

            }else if($browseType == 'problem') {
                $reviewList = $this->my->getUserProblemList($orderBy);
                $this->view->users = $this->loadModel('user')->getPairs('noclosed|noletter');
            }else if($browseType == 'productionchange') {
                $this->app->loadLang('productionchange');
                $reviewList = $this->my->getUserPreproductionList($orderBy);
                $this->view->title    = $this->lang->productionchange->common;
                $this->view->apps     = $this->loadModel('application')->getPairs();
                $this->view->users    = $this->loadModel('user')->getPairs('noletter|noclosed');
                $this->view->depts    = $this->loadModel('dept')->getOptionMenu();
            } else if($browseType == 'demand') {
                $this->app->loadLang('opinion'); // 引用需求意向的需求来源方式自定义数据。
                $demands = $this->my->getUserDemandList($orderBy);

                $userDepts = $this->dao->select('account,dept')->from(TABLE_USER)->fetchAll('account');
                foreach($demands as $key => $demand)
                {
                    $demands[$key]->createdDept = $userDepts[$demand->createdBy]->dept;
                    $dealUserList = explode(',', $demand->dealUser);
                    if(!empty($demand->delayDealUser)){
                        $delayDealUserList = explode(',', $demand->delayDealUser);
                        foreach ($delayDealUserList as $delayDealUser){
                            if(!in_array($delayDealUser, $dealUserList)){
                                array_push($dealUserList, $delayDealUser);
                            }
                        }
                    }
                    $demand->dealUser = implode(',', $dealUserList);
                    if($demand->ignoreStatus == 1){
                        $reviewListIgnore[] = $demand;
                    } else {
                        $reviewList[] = $demand;
                    }
                }
                $this->view->users = $this->loadModel('user')->getPairs('noclosed|noletter');

                $poList = $this->dept->getPoUser();//产品经理
                $executiveList = $this->dept->getExecutiveUser();//二线专员
                $suspender = array_filter($this->lang->demand->suspendList);
                $suspendList = [];
                if(!empty($suspender))
                {
                    foreach ($suspender as $key=>$value){
                        $suspendList[$key] = $key;
                    }
                }
                $closeAccountList = array_merge($poList,$executiveList,$suspendList);
                $this->view->executives = $closeAccountList;

            }else if($browseType == 'demandinside') {
                $this->app->loadLang('opinioninside'); // 引用需求意向的需求来源方式自定义数据。
                $this->app->loadLang('opinion'); // 引用需求意向的需求来源方式自定义数据。
                $demands = $this->my->getUserDemandInsideList($orderBy);

                $userDepts = $this->dao->select('account,dept')->from(TABLE_USER)->fetchAll('account');
                foreach($demands as $key => $demand)
                {
                    $demands[$key]->createdDept = $userDepts[$demand->createdBy]->dept;
                    if($demand->ignoreStatus == 1){
                        $reviewListIgnore[] = $demand;
                    } else {
                        $reviewList[] = $demand;
                    }
                }
                $this->view->users = $this->loadModel('user')->getPairs('noclosed|noletter');

                $poList = $this->dept->getPoUser();//产品经理
                $executiveList = $this->dept->getExecutiveUser();//二线专员
                $suspender = array_filter($this->lang->demand->suspendList);
                $suspendList = [];
                if(!empty($suspender))
                {
                    foreach ($suspender as $key=>$value){
                        $suspendList[$key] = $key;
                    }
                }
                $closeAccountList = array_merge($poList,$executiveList,$suspendList);
                $this->view->executives = $closeAccountList;

            }else if($browseType == 'review') {
                $this->loadModel('datatable');
                $this->app->loadLang('review');
                $reviewList = $this->my->getUserReviewList($orderBy);
                $this->view->users = $this->loadModel('user')->getPairs('noclosed|noletter');
            }else if($browseType == 'opinion') {
                $this->loadModel('datatable');
                $this->app->loadLang('opinion');
                $this->app->loadLang('demand');
                $opinions = $this->my->getUserOpinionList($orderBy);
                $account = $this->app->user->account;
                foreach ($opinions as $opinion)
                {
                    if(in_array($account, explode(',',$opinion->ignore)) == true)
                    {
                        $reviewListIgnore[] = $opinion;
                    } else{
                        $reviewList[] = $opinion;
                    }

                    $demands = $this->loadModel('demand')->getDemandByOpinionID($opinion->id);
                    //构造研发责任人，为变更按钮提供权限
                    $opinion->acceptUser = '';
                    if(!empty($demands))
                    {
                        $acceptUser = implode(',',array_unique(array_column($demands,'acceptUser')));
                        $opinion->acceptUser = $acceptUser;
                    }

                    //变更单下一节点处理人
                    $changeInfo = $this->loadModel('opinion')->getPendingOrderByOpinionId($opinion->id);
                    $opinion->changeNextDealuser = $changeInfo->nextDealUser ?? '';
                    //判断审核需求意向权限
                    $opinion->reviewOpinionDealUser = $opinion->dealUser;
                }

                //后台配置挂起人 需求意向
                $suspenderOpinion = array_filter($this->lang->demand->opinionSuspendList);
                $executivesList = [];
                if(!empty($suspenderOpinion))
                {
                    foreach ($suspenderOpinion as $key=>$value){
                        $executivesList[$key] = $key;
                    }
                }
                $this->view->executivesOpinion = $executivesList;

                $this->view->users = $this->loadModel('user')->getPairs('noclosed|noletter');
            }else if($browseType == 'opinioninside') {
                $this->loadModel('datatable');
                $this->app->loadLang('opinioninside');
                $this->app->loadLang('demand');
                $opinions = $this->my->getUserOpinionInsideList($orderBy);
                $account = $this->app->user->account;
                foreach ($opinions as $opinion)
                {
                    if(in_array($account, explode(',',$opinion->ignore)) == true)
                    {
                        $reviewListIgnore[] = $opinion;
                    } else{
                        $reviewList[] = $opinion;
                    }
                }
                //后台配置挂起人 需求意向
                $suspenderOpinion = array_filter($this->lang->demand->opinionSuspendList);
                $executivesList = [];
                if(!empty($suspenderOpinion))
                {
                    foreach ($suspenderOpinion as $key=>$value){
                        $executivesList[$key] = $key;
                    }
                }
                $this->view->executivesOpinion = $executivesList;
                $this->view->users = $this->loadModel('user')->getPairs('noclosed|noletter');
            }else if($browseType == 'component') {
                $this->app->loadLang('component');
                $reviewList = $this->my->getUserComponentList($orderBy);
                $this->view->users = $this->loadModel('user')->getPairs('noclosed|noletter');
                $this->view->projectPlanList = $this->loadModel('project')->getPairs();
            }else if($browseType == 'build') {
                $this->app->loadLang('build');
                $reviewList = $this->my->getUserBuildList($orderBy,$pager);
                $this->view->users = $this->loadModel('user')->getPairs('noclosed|noletter');
            }else if($browseType == 'secondorder') {
                $reviewList = $this->my->getUserSecondorderList($orderBy);
                $this->view->users = $this->loadModel('user')->getPairs('noclosed|noletter');
            }else if($browseType == 'deptorder') {
                $reviewList = $this->my->getUserDeptorderList($orderBy);
                $this->view->users = $this->loadModel('user')->getPairs('noclosed|noletter');
            }else if($browseType == 'residentsupport') {
                $reviewList = $this->my->getUserResidentSupportList($orderBy);
                $this->view->users = $this->loadModel('user')->getPairs('noclosed|noletter');
                $this->session->set('residentsupportList', $this->app->getURI(true));
            }else if($browseType == 'localesupport') {
                $reviewList = $this->my->getUserLocaleSupportList($this->app->user->account, $orderBy);
                if($reviewList){
                    $users = $this->loadModel('user')->getPairs('noletter');
                    $this->view->users = $users;
                }
            }else if($browseType == 'environmentorder') {
                $reviewList = $this->my->getUserEnvironmentOrder($this->app->user->account);
                if($reviewList){
                    $users = $this->loadModel('user')->getPairs('noletter');
                    $this->view->users = $users;
                }
            }else if($browseType == 'authorityapply') {
                $reviewList = $this->my->getUserAuthorityapply($this->app->user->account);
                if($reviewList){
                    $userList = $this->loadModel('user')->getAllUserList();
                    $userList = array_column($userList, 'realname', 'account');
                    $deptList = $this->loadModel('dept')->getDeptPairs();
                    $this->view->deptList = $deptList;
                    $this->view->userList = $userList;
                }
            }else if($browseType == 'qualitygate') {
                $reviewList = $this->my->getUserQualityGateList($this->app->user->account, $orderBy);
                if($reviewList){
                    $users = $this->loadModel('user')->getPairs('noletter');
                    $this->view->users = $users;
                    $this->view->buildstatusList = $this->loadModel('build')->lang->build->statusList;
                }
            }else if($browseType == 'datamanagement') {
                $this->app->loadLang('datamanagement');
                $reviewList = $this->my->getUserDatamanagementList($orderBy);
                $this->view->users = $this->loadModel('user')->getPairs('noclosed|noletter');
            } else if($browseType == 'projectrelease') {
                $this->app->loadLang('release');
                $reviewList = $this->my->getUserProjectReleaseList($orderBy);
                $this->view->users = $this->loadModel('user')->getPairs('noclosed|noletter');
                $this->view->from  = 'project';
            }else if($browseType == 'reviewqz') {
                $this->loadModel('datatable');
                $reviewList = $this->loadModel('reviewqz')->reviewList('wait',0,$orderBy,$pager);
                $this->view->users = $this->loadModel('user')->getPairs('noclosed|noletter');
            }else if($browseType == 'sectransfer') {
                $this->app->loadLang('sectransfer');
                $this->loadModel('datatable');
                $this->loadModel('sectransfer');
                $reviewList = $this->my->getUserSectransferList($orderBy);
                $this->view->users = $this->loadModel('user')->getPairs('noclosed|noletter');
            }else if($browseType == 'closingitem') {
                $this->loadModel('projectplan');
                $this->view->projects     = $this->projectplan->getAllProjects();
                $this->view->typeList     = $this->lang->projectplan->typeList;
                $reviewList = $this->my->getUserClosingItemList($orderBy);
                $this->view->users = $this->loadModel('user')->getPairs('noclosed|noletter');
                $this->session->set('closingitemHistory', $this->app->getURI(true));
            }else if($browseType == 'closingadvise') {
                $this->loadModel('projectplan');
                $this->view->projects     = $this->projectplan->getAllProjects();
                $this->loadModel('closingitem');
                $this->view->feedbackResults = $this->lang->closingitem->feedbackResult;
                $reviewList = $this->my->getUserClosingAdviseList($orderBy);
                $this->view->users = $this->loadModel('user')->getPairs('noclosed|noletter');
                $this->session->set('closingadviseHistory', $this->app->getURI(true));
            }else if($browseType == 'osspchange') {
                $this->loadModel('datatable');
                $this->loadModel('osspchange');
                $reviewList = $this->my->getUserOsspchangeList($orderBy);
                $this->view->users = $this->loadModel('user')->getPairs('noclosed|noletter');
            }
            $this->view->title      = $this->lang->my->myReview;
            $this->view->depts      = $this->loadModel('dept')->getOptionMenu();
            $this->view->apps       = $this->loadModel('application')->getapplicationNameCodePairs();
            $this->view->pageID     = $pageID;
            $this->view->recTotal   = $recTotal;
            $this->view->recPerPage = $recPerPage;
            $this->view->pager      = $pager;
            $this->view->orderBy    = $orderBy;
            $this->view->browseType = $browseType;
            $this->view->reviewList = $reviewList;
            $this->view->reviewListIgnore = $reviewListIgnore ?? [];
            $this->view->mode       = 'audit';
            $this->display('my', 'audit' . $browseType);
        }
    }

    /**
     * My ncs.
     *
     * @param  string $browseType
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function ncbak($browseType = 'assignedToMe', $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $this->loadModel('nc');
        $this->session->set('ncList', $this->app->getURI(true));

        /* Set the pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage = 50, $pageID = 1);

        $this->view->title      = $this->lang->my->common . $this->lang->colon . $this->lang->my->nc;
        $this->view->position[] = $this->lang->my->nc;
        $this->view->browseType = $browseType;
        $this->view->pager      = $pager;
        $this->view->ncs        = $this->my->getNcList($browseType, $orderBy, $pager);
        $this->view->users      = $this->loadModel('user')->getPairs('noclosed|noletter');
        $this->view->projects   = $this->loadModel('project')->getPairsByProgram(0);
        $this->view->mode       = 'nc';
        $this->display();
    }

    //QA周报改造。2023-05-19
    public function nc($browseType = 'assignedToMe', $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $this->loadModel('nc');
        $this->loadModel('projectplan');
        $this->loadModel('weeklyreport');
        $this->loadModel('project');
        $this->session->set('ncList', $this->app->getURI(true));

        /* Set the pager. */
        $this->app->loadClass('pager', $static = true);


        $this->view->title      = $this->lang->my->ncQA;
        $this->view->position[] = $this->lang->my->nc;
        $this->view->browseType = $browseType;

        $outsideproject = $this->loadModel('outsideplan')->getPairs();
        $weekly = $this->weeklyreport->getWeekMyActionAndEnd();

        $weeklyReportPorject = $this->dao->select("projectId,id")->from(TABLE_PROJECTWEEKLYREPORT)->where('reportStartDate')->between($weekly['week_start'],$weekly['week_end'])->orderBy("weeknum asc")->fetchAll('projectId');
       /* $weeklyReportProjectIDArr = [];
        if($weeklyReportPorject){
            $weeklyReportProjectIDArr = array_unique(array_column($weeklyReportPorject,'projectId'));
        }*/


       /* a($weeklyReportPorject);
        a($weekly);*/
        //查组织级QA
        //查询当前登录用户所属部门
        $qaDepts = $this->weeklyreport->getUserQADept($this->app->user->account);

        if($qaDepts['isogQA'] == 1){

            $this->dao->select("*")->from(TABLE_PROJECTPLAN)->where(" status in ('projected','pass','yearpass' ,'start','reviewing') ")->andWhere('deleted')->eq(0);
            $projectplanProjectedList =  $this->dao->orderBy("id desc")->fetchAll();

        }else{
            $deptsIDArr = array_column($qaDepts['depts'],'id');
            if($deptsIDArr){
                $this->dao->select("*")->from(TABLE_PROJECTPLAN)->where(" status in ('projected','pass','yearpass' ,'start','reviewing') AND ");

                $this->dao->markLeft(1);
                $tempcount = count($deptsIDArr) - 1;
                foreach ($deptsIDArr as $key => $dept){
                    if($tempcount == $key){
                        $this->dao->where(" FIND_IN_SET('{$dept}',`bearDept`) ");
                    }else{
                        $this->dao->where(" FIND_IN_SET('{$dept}',`bearDept`) or ");
                    }
                }
                $this->dao->markRight(1);
                $projectplanProjectedList =  $this->dao->andWhere('deleted')->eq(0)->orderBy("id desc")->fetchAll();
            }else{
                $projectplanProjectedList = [];
            }

        }


//        $this->dao->printSQL();
        foreach ($projectplanProjectedList as $key=>$projectplan){
            if($projectplan->status == 'projected'){
                $project = $this->project->getByID($projectplan->project);
                if(!$project){
                    unset($projectplanProjectedList[$key]);
                    continue;
                }
                if($project->status == 'closed'){
                    unset($projectplanProjectedList[$key]);
                }
            }

        }

        $this->view->planProjectedList = [];
        $this->view->planPssList = [];
        foreach($projectplanProjectedList as $planID => $plan)
        {
            $outsideProjectList = explode(',', str_replace(' ', '', $plan->outsideProject));
            $plan->outsides = '';
            $outsideTitle = array();
            foreach($outsideProjectList as $outsideID)
            {
                if(empty($outsideID)) continue;
                $outsideTitle[] = zget($outsideproject, $outsideID, $outsideID);
            }
            if(!empty($outsideTitle)) $plan->outsides = implode(',', $outsideTitle);


            if($plan->status == 'projected'){
                if(isset($weeklyReportPorject[$plan->project])){
                    $plan->weeklyreportID = $weeklyReportPorject[$plan->project]->id;
                }else{
                    $plan->weeklyreportID = 0;
                }

                $this->view->planProjectedList[] = $plan;
            }else{

                $this->view->planPssList[] = $plan;
            }


        }
        $recTotal = count($this->view->planProjectedList);
        if($recTotal){
            $curPage = $pageID - 1;
            if($curPage < 0){
                $curPage = 0;
            }
            $start = $curPage * $recPerPage;
            $this->view->planProjectedList = array_slice($this->view->planProjectedList,$start,$recPerPage);
        }

        $pager = new pager($recTotal, $recPerPage , $pageID);
        $this->view->recTotal   = $recTotal;
        $this->view->recPerPage = $recPerPage;
        $this->view->pageID     = $pageID;
        $this->view->orderBy    = $orderBy;

        $this->view->pager      = $pager;
//        $this->view->ncs        = $this->my->getNcList($browseType, $orderBy, $pager);
        $this->view->users      = $this->loadModel('user')->getPairs('noclosed|noletter');
        $this->view->projects   = $this->loadModel('project')->getPairsByProgram(0);
        $this->view->mode       = 'nc';
        $this->display();
    }

    /**
     * My ncs.
     *
     * @param  string $browseType
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function defect($browseType = 'all', $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $this->loadModel('defect');
        $this->session->set('defectList', $this->app->getURI(true));
        $defectInfo = $this->my->getUserDefectList($orderBy,$browseType);
        /* Set the pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage = 50, $pageID = 1);
        $this->app->loadLang('bug');
        $products  = $this->loadModel('product')->getSimplePairs();
        $this->view->title         = $this->lang->my->defect;
        $this->view->products      = $products;
        $this->view->projects      = $this->loadModel('project')->getProjects();
        $this->view->browseType = $browseType;
        $this->view->orderBy    = $orderBy;
        $this->view->pager      = $pager;
        $this->view->defectInfo = $defectInfo;
        $this->view->users      = $this->loadModel('user')->getPairs('noclosed|noletter');
        $this->view->mode       = 'defect';
        $this->display();
    }
    /**
     * My team.
     *
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function team($orderBy = 'id', $recTotal = 0, $recPerPage = 15, $pageID = 1)
    {
        $this->lang->navGroup->my = 'system';

        /* Set the pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        /* Append id for secend sort. */
        $sort = $this->loadModel('common')->appendOrder($orderBy);

        /* Get users by dept. */
        $user   = $this->loadModel('user')->getById($this->app->user->account, 'account');
        $deptID = $user->dept;
        $users  = $this->loadModel('company')->getUsers('inside', 'bydept', 0, $deptID, $sort, $pager);
        foreach($users as $user) unset($user->password); // Remove passwd.

        $this->view->title      = $this->lang->my->team;
        $this->view->position[] = $this->lang->my->team;
        $this->view->users      = $users;
        $this->view->orderBy    = $orderBy;
        $this->view->pager      = $pager;

        $this->display();
    }

    /**
     * Edit profile
     *
     * @access public
     * @return void
     */
    public function editProfile()
    {
        if($this->app->user->account == 'guest') die(js::alert('guest') . js::locate('back'));
        if(!empty($_POST))
        {
            $_POST['account'] = $this->app->user->account;
            $this->user->update($this->app->user->id);
            if(dao::isError()) die(js::error(dao::getError()));
            die(js::locate($this->createLink('my', 'profile'), 'parent'));
        }

        $this->app->loadConfig('user');
        $this->app->loadLang('user');

        $userGroups = $this->loadModel('group')->getByAccount($this->app->user->account);

        $this->view->title      = $this->lang->my->common . $this->lang->colon . $this->lang->my->editProfile;
        $this->view->position[] = $this->lang->my->editProfile;
        $this->view->user       = $this->user->getById($this->app->user->account);
        $this->view->rand       = $this->user->updateSessionRandom();
        $this->view->userGroups = implode(',', array_keys($userGroups));
        $this->view->groups     = $this->dao->select('id, name')->from(TABLE_GROUP)->fetchPairs('id', 'name');

        $this->display();
    }

    /**
     * Change password
     *
     * @access public
     * @return void
     */
    public function changePassword()
    {
        if($this->app->user->account == 'guest') die(js::alert('guest') . js::locate('back'));
        if(!empty($_POST))
        {
            $this->user->updatePassword($this->app->user->id);
            if(dao::isError()) die(js::error(dao::getError()));
            if(isonlybody()) die(js::closeModal('parent.parent', 'this'));
            die(js::locate($this->createLink('my', 'index'), 'parent.parent'));
        }

        $this->view->title      = $this->lang->my->common . $this->lang->colon . $this->lang->my->changePassword;
        $this->view->position[] = $this->lang->my->changePassword;
        $this->view->user       = $this->user->getById($this->app->user->account);
        $this->view->rand       = $this->user->updateSessionRandom();

        $this->display();
    }

    /**
     * Manage contacts.
     *
     * @param  int    $listID
     * @param  string $mode
     * @access public
     * @return void
     */
    public function manageContacts($listID = 0, $mode = '')
    {
        if($_POST)
        {
            $data = fixer::input('post')->get();
            if($data->mode == 'new')
            {
                $listID = $this->user->createContactList($data->newList, $data->users);
                $this->user->setGlobalContacts($listID, isset($data->share));
                if(isonlybody()) die(js::closeModal('parent.parent', '', ' function(){parent.parent.ajaxGetContacts(\'#mailto\')}'));
                die(js::locate(inlink('manageContacts', "listID=$listID"), 'parent'));
            }
            elseif($data->mode == 'edit')
            {
                $this->user->updateContactList($data->listID, $data->listName, $data->users);
                $this->user->setGlobalContacts($data->listID, isset($data->share));
                die(js::locate(inlink('manageContacts', "listID={$data->listID}"), 'parent'));
            }
        }

        $mode  = empty($mode) ? 'edit' : $mode;
        $lists = $this->user->getContactLists($this->app->user->account);

        $globalContacts = isset($this->config->my->global->globalContacts) ? $this->config->my->global->globalContacts : '';
        $globalContacts = !empty($globalContacts) ? explode(',', $globalContacts) : array();

        $myContacts = $this->user->getListByAccount($this->app->user->account);
        $disabled   = $globalContacts;

        if(!empty($myContacts) && !empty($globalContacts))
        {
            foreach($globalContacts as $id)
            {
                if(in_array($id, array_keys($myContacts))) unset($disabled[array_search($id, $disabled)]);
            }
        }

        $listID = $listID ? $listID : key($lists);
        if(!$listID) $mode = 'new';

        /* Create or manage list according to mode. */
        if($mode == 'new')
        {
            $this->view->title      = $this->lang->my->common . $this->lang->colon . $this->lang->user->contacts->createList;
            $this->view->position[] = $this->lang->user->contacts->createList;
        }
        else
        {
            $this->view->title      = $this->lang->my->common . $this->lang->colon . $this->lang->user->contacts->manage;
            $this->view->position[] = $this->lang->user->contacts->manage;
            $this->view->list       = $this->user->getContactListByID($listID);
        }

        $users = $this->user->getPairs('noletter|noempty|noclosed|noclosed', $mode == 'new' ? '' : $this->view->list->userList, $this->config->maxCount);
        if(isset($this->config->user->moreLink)) $this->config->moreLinks['users[]'] = $this->config->user->moreLink;

        $this->view->mode           = $mode;
        $this->view->lists          = $lists;
        $this->view->listID         = $listID;
        $this->view->users          = $users;
        $this->view->disabled       = $disabled;
        $this->view->globalContacts = $globalContacts;
        $this->display();
    }

    /**
     * Delete a contact list.
     *
     * @param  int    $listID
     * @param  string $confirm
     * @access public
     * @return void
     */
    public function deleteContacts($listID, $confirm = 'no')
    {
        if($confirm == 'no')
        {
            die(js::confirm($this->lang->user->contacts->confirmDelete, inlink('deleteContacts', "listID=$listID&confirm=yes")));
        }
        else
        {
            $this->user->deleteContactList($listID);
            die(js::locate(inlink('manageContacts'), 'parent'));
        }
    }

    /**
     * Build contact lists.
     *
     * @access public
     * @return void
     */
    public function buildContactLists()
    {
        $this->view->contactLists = $this->user->getContactLists($this->app->user->account, 'withnote');
        $this->display();
    }

    /**
     * View my profile.
     *
     * @access public
     * @return void
     */
    public function profile()
    {
        if($this->app->user->account == 'guest') die(js::alert('guest') . js::locate('back'));

        $this->app->loadConfig('user');
        $this->app->loadLang('user');
        $user = $this->user->getById($this->app->user->account);

        $this->view->title        = $this->lang->my->common . $this->lang->colon . $this->lang->my->profile;
        $this->view->position[]   = $this->lang->my->profile;
        $this->view->user         = $user;
        $this->view->groups       = $this->loadModel('group')->getByAccount($this->app->user->account);
        $this->view->deptPath     = $this->dept->getParents($user->dept);
        $this->view->personalData = $this->user->getPersonalData();
        $this->display();
    }

    /**
     * User preference setting.
     *
     * @access public
     * @return void
     */
    public function preference()
    {
        $this->loadModel('setting');

        if($_POST)
        {
            foreach($_POST as $key => $value) $this->setting->setItem("{$this->app->user->account}.common.$key", $value);

            $this->setting->setItem("{$this->app->user->account}.common.preferenceSetted", 1);
            if(isOnlybody()) die(js::closeModal('parent.parent'));

            die(js::locate($this->createLink('my', 'index'), 'parent'));
        }

        $this->view->title      = $this->lang->my->common . $this->lang->colon . $this->lang->my->preference;
        $this->view->position[] = $this->lang->my->preference;

        $this->view->URSRList         = $this->loadModel('custom')->getURSRPairs();
        $this->view->URSR             = $this->setting->getURSR();
        $this->view->programLink      = isset($this->config->programLink) ? $this->config->programLink : 'program-browse';
        $this->view->productLink      = isset($this->config->productLink) ? $this->config->productLink : 'product-all';
        $this->view->projectLink      = isset($this->config->projectLink) ? $this->config->projectLink : 'project-browse';
        $this->view->secondLink      = isset($this->config->secondLink) ? $this->config->secondLink : 'modify-browse';
        //$this->view->executionLink  = isset($this->config->executionLink) ? $this->config->executionLink : 'execution-task';
        $this->view->preferenceSetted = isset($this->config->preferenceSetted) ? true : false;

        $this->display();
    }

    /**
     * My dynamic.
     *
     * @param  string $type
     * @param  int    $recTotal
     * @param  string $date
     * @param  string $direction    next|pre
     * @access public
     * @return void
     */
    public function dynamic($type = 'today', $recTotal = 0, $date = '', $direction = 'next', $originTotal = 0)
    {
        /* Save session. */
        $uri = $this->app->getURI(true);
        $this->session->set('productList',     $uri, 'product');
        $this->session->set('storyList',       $uri, 'product');
        $this->session->set('designList',      $uri, 'project');
        $this->session->set('productPlanList', $uri, 'product');
        $this->session->set('releaseList',     $uri, 'product');
        $this->session->set('programList',     $uri, 'program');
        $this->session->set('projectList',     $uri, 'project');
        $this->session->set('executionList',   $uri, 'execution');
        $this->session->set('taskList',        $uri, 'execution');
        $this->session->set('buildList',       $uri, 'execution');
        $this->session->set('bugList',         $uri, 'qa');
        $this->session->set('caseList',        $uri, 'qa');
        $this->session->set('caselibList',     $uri, 'qa');
        $this->session->set('testsuiteList',   $uri, 'qa');
        $this->session->set('testtaskList',    $uri, 'qa');
        $this->session->set('reportList',      $uri, 'qa');
        $this->session->set('docList',         $uri, 'doc');
        $this->session->set('todoList',        $uri, 'my');
        $this->session->set('riskList',        $uri, 'project');
        $this->session->set('issueList',       $uri, 'project');
        $this->session->set('stakeholderList', $uri, 'project');

        /* Set the pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage = 50, $pageID = 1);

        /* Append id for secend sort. */
        $orderBy = $direction == 'next' ? 'date_desc' : 'date_asc';
        $sort = $this->loadModel('common')->appendOrder($orderBy);

        /* The header and position. */
        $this->view->title      = $this->lang->my->common . $this->lang->colon . $this->lang->my->dynamic;
        $this->view->position[] = $this->lang->my->dynamic;

        $date    = empty($date) ? '' : date('Y-m-d', $date);
        $actions = $this->loadModel('action')->getDynamic($this->app->user->account, $type, $sort, $pager, 'all', 'all', 'all', $date, $direction);
        if(empty($recTotal)) $originTotal = $pager->recTotal;

        /* Assign. */
        $this->view->type        = $type;
        $this->view->orderBy     = $orderBy;
        $this->view->pager       = $pager;
        $this->view->dateGroups  = $this->action->buildDateGroup($actions, $direction, $type);
        $this->view->direction   = $direction;
        $this->view->originTotal = $originTotal;
        $this->display();
    }

    /**
     * Upload avatar.
     *
     * @access public
     * @return void
     */
    public function uploadAvatar()
    {
        if($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $result = $this->loadModel('user')->uploadAvatar();
            $this->send($result);
        }
    }

    /**
     * Unbind ranzhi
     *
     * @param  string $confirm
     * @access public
     * @return void
     */
    public function unbind($confirm = 'no')
    {
        $this->loadModel('user');
        if($confirm == 'no')
        {
            die(js::confirm($this->lang->user->confirmUnbind, $this->createLink('my', 'unbind', "confirm=yes")));
        }
        else
        {
            $this->user->unbind($this->app->user->account);
            die(js::locate($this->createLink('my', 'profile'), 'parent'));
        }
    }

    /**
     * My byme .
     *
     * @param  string $browseType
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function bymeaudit($browseType = 'bymeaudit',$orderBy = 'id_desc', $recTotal = 0, $recPerPage = 15, $pageID = 1)
    {
            // 判断是那个对象，根据对象加载语言项。
            if($browseType == 'wait') $browseType = 'bymereview';

            // 不要移动这个位置
            $lang = $browseType;

            $this->app->loadLang($lang);

            $this->app->loadClass('pager', true);
            $pager = pager::init($recTotal, $recPerPage, $pageID);

            // 按照不同评审对象查询数据。
            $reviewList = array();
            if($browseType == 'bymereview')
            {
                $this->loadModel('datatable');
                $this->app->loadLang('review');
                $reviewList = $this->my->getByMeUserReviewList($orderBy,$pager,1); //20220711 优化增加分页
                $this->view->users = $this->loadModel('user')->getPairs('noclosed|noletter');
            }

            $this->view->title      = $this->lang->my->myReview;
            $this->view->depts      = $this->loadModel('dept')->getOptionMenu();
            $this->view->apps       = $this->loadModel('application')->getapplicationNameCodePairs();
            $this->view->pageID     = $pageID;
            $this->view->recTotal   = $recTotal;
            $this->view->recPerPage = $recPerPage;
            $this->view->pager      = $pager;
            $this->view->orderBy    = $orderBy;
            $this->view->browseType = $browseType;
            $this->view->reviewList = $reviewList;
            $this->view->mode       = 'bymeaudit';

            $this->display('my', 'audit' . $browseType);
    }

    /**
     * 获取用户手册内容
     * @param $moduleName
     * @param int $id
     */
    public function showHelpDoc($moduleName,$id=0){
        $res = $this->my->getDocTypes();
        $options = $res['options'];
        $data    = $res['data'];
        $docs = [];
        foreach ($options as $k=>$option) {
            $docs[$k]['type']      = $k;
            $docs[$k]['typeName']  = $option;
            foreach ($data as $dk=>$dv) {
                $type = explode(',',$dv->type);
                if (in_array($k,$type)){
                    $docs[$k]['data'][] = $dv;
                }
            }
        }
        $this->view->moduleName   = $moduleName;
        $this->view->data         = $docs;
        $this->display();
    }

    /**
     * @param $id
     */
    public function ajaxgetDocContent($id){
        $info = $this->dao->select("*")->from(TABLE_FLOW_HANDBOOK)->where('id')->eq($id)->fetch();
        $info = $this->loadModel('file')->replaceImgURL($info, 'content');
        if ($info->content == ''){
            $info->content = '<div class="nodata">'.$this->lang->stayTuned.'</div>';
        }
        $info->files = $this->loadModel('file')->getByObject('handbook', $id);
        $info->filesHtmlTag = '';
        if ($info->files){
            foreach ($info->files as $key => $file) {
                $info->filesHtmlTag .= $this->fetch('file', 'printFiles', array('files' => array($key => $file), 'fieldset' => 'false', 'object' => $info, 'canOperate' => ($file->addedBy == $this->app->user->account && ($copyrightqz->status=='tosubmit' || $copyrightqz->status=='feedbackFailed'))));
            }
        }
        echo json_encode($info);
    }
    /**
     * 授权管理
     *
     * @param  string $type
     * @param  int    $recTotal
     * @param  string $date
     * @param  string $direction    next|pre
     * @access public
     * @return void
     */
    public function authorization($authorizer='')
    {
        if($_POST)
        {
            $comment = $this->my->authorization();

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $user = $this->loadModel('user')->getUserInfo($_POST["authorizerAccount"]);
            $actionID = $this->loadModel('action')->create('authorization', $user->id, 'edited',$comment);
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'reload';


            $this->send($response);
        }
        $this->view->title         = $this->lang->my->authorization;
        $this->view->users      = $this->loadModel('user')->getPairs('noletter|noclosed');
        $this->view->authorizer = '';
        if($this->app->user->account != 'admin'){
            $authorizationList         = $this->dao->select("*")->from(TABLE_AUTHORIZATION)->where('authorizer')->eq($this->app->user->account)->andWhere('deleted')->ne('2')->orderBy('num')->fetchAll('id');
            $authorizationArray = array();
            $maxNum = 1;
            foreach ($authorizationList as $authorization){
                $authorizationObj = new stdClass();
                $authorizationObj->authorizedPerson = $authorization->authorizedPerson;
                $authorizationObj->startTime = date('Y-m-d', strtotime($authorization->startTime));
                $authorizationObj->endTime = date('Y-m-d', strtotime($authorization->endTime));
                $authorizationObj->permanently = $authorization->permanently;
                $authorizationObj->enabled = $authorization->enabled;
                $authorizationObj->objectType = $authorization->objectType;
                $authorizationObj->authorizer = $authorization->authorizer;
                $authorizationObj->id = $authorization->id;
                $authorizationObj->num = $authorization->num;
                if($authorization->id>$maxNum){
                    $maxNum = $authorization->id;
                }
                array_push($authorizationArray, $authorizationObj);
            }
            $this->view->authorizationArray = $authorizationArray;
            $this->view->actions = $this->loadModel('action')->getListDesc('authorization', $this->app->user->id);
            $this->view->account = $this->app->user->account;
            $this->view->userId = $this->app->user->id;
            $this->view->maxNum = $maxNum;
        }else{
            if(!empty($authorizer)){
                $maxNum = 1;
                $authorizationList = $this->dao->select("*")->from(TABLE_AUTHORIZATION)->where('authorizer')->eq($authorizer)->andWhere('deleted')->ne('2')->orderBy('num')->fetchAll('id');
                $authorizationArray = array();
                foreach ($authorizationList as $authorization){
                    $authorizationObj = new stdClass();
                    $authorizationObj->authorizedPerson = $authorization->authorizedPerson;
                    $authorizationObj->startTime = date('Y-m-d', strtotime($authorization->startTime));
                    $authorizationObj->endTime = date('Y-m-d', strtotime($authorization->endTime));
                    $authorizationObj->permanently = $authorization->permanently;
                    $authorizationObj->enabled = $authorization->enabled;
                    $authorizationObj->objectType = $authorization->objectType;
                    $authorizationObj->authorizer = $authorization->authorizer;
                    $authorizationObj->id = $authorization->id;
                    $authorizationObj->num = $authorization->num;
                    if($authorization->id>$maxNum){
                        $maxNum = $authorization->id;
                    }
                    array_push($authorizationArray, $authorizationObj);
                }
                $this->view->authorizationArray = $authorizationArray;
                $this->view->authorizer = $authorizer;
                $user = $this->loadModel('user')->getUserInfo($authorizer);
                $this->view->actions = $this->loadModel('action')->getListDesc('authorization', $user->id);
                $this->view->account = $authorizer;
                $this->view->userId = $user->id;
                $this->view->maxNum = $maxNum;
            }
        }


        $this->display();
    }

    /**
     * 查询授权管理
     *
     * @param  string $type
     * @param  int    $recTotal
     * @param  string $date
     * @param  string $direction    next|pre
     * @access public
     * @return void
     */
    public function ajaxGetAuthorizer($authorizer){
        $authorizationList = $this->dao->select("*")->from(TABLE_AUTHORIZATION)->where('authorizer')->eq($authorizer)->andWhere('deleted')->ne('2')->orderBy('id')->fetchAll('id');
        foreach ($authorizationList as $authorization){
            $authorization->startTime = date('Y-m-d', strtotime($authorization->startTime));
            $authorization->endTime = date('Y-m-d', strtotime($authorization->endTime));
        }
        die(json_encode($authorizationList));
    }

    /**
     * 查询历史记录
     *
     * @param  string $type
     * @param  int    $recTotal
     * @param  string $date
     * @param  string $direction    next|pre
     * @access public
     * @return void
     */
    public function ajaxGetAction($authorizer){
        $user = $this->loadModel('user')->getUserInfo($authorizer);
        $actions = $this->loadModel('action')->getListDesc('authorization', $user->id);
        $users      = $this->loadModel('user')->getPairs('noletter|noclosed');
        foreach ($actions as $action){
            $action->actor = zget($users, $action->actor);
        }
        die(json_encode($actions));
    }
}
