<?php
/**
 * The control file of testsuite module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     testsuite
 * @version     $Id: control.php 5114 2013-07-12 06:02:59Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
class testsuite extends control
{
    /**
     * All applications.
     *
     * @var    array
     * @access public
     */
    public $applicationList = array();

    /**
     * Construct function.
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
        $this->locate($this->createLink('testsuite', 'browse'));
    }

    /**
     * Browse test suites.
     *
     * @param  int    $productID
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function browse($applicationID = 0, $productID = 'all', $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $this->loadModel('datatable');
        /* Save session. */
        $this->session->set('testsuiteList', $this->app->getURI(true), 'qa');

        /* Set menu. */
        if(empty($productID)) $productID = 'na';
        $applicationID = $this->rebirth->saveState($this->applicationList, $applicationID, $productID);
        $application   = $this->rebirth->getApplicationByID($applicationID);
        $productID     = $this->rebirth->getProductIdByApplication($applicationID, $productID);
        $this->rebirth->setMenu($applicationID, $productID);
        $productIdList = $this->rebirth->getProductIdList($applicationID, $productID);
        $products      = $this->rebirth->getProductPairs($applicationID, true);

        /* 获取固定排序字段。 */
        if(isset($this->config->testsuite->browse->fixedSort)) $orderBy = $this->config->testsuite->browse->fixedSort;

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = pager::init($recTotal, $recPerPage, $pageID);

        /* Append id for secend sort. */
        $sort = $this->loadModel('common')->appendOrder($orderBy);

        $suites = $this->testsuite->getSuites($applicationID, $productIdList, $sort, $pager);
        if(empty($suites) and $pageID > 1)
        {
            $pager  = pager::init(0, $recPerPage, 1);
            $suites = $this->testsuite->getSuites($applicationID, $productIdList, $sort, $pager);
        }

        $this->view->title         = $this->applicationList[$applicationID] . $this->lang->testsuite->common;
        $this->view->applicationID = $applicationID;
        $this->view->productID     = $productID;
        $this->view->orderBy       = $orderBy;
        $this->view->suites        = $suites;
        $this->view->products      = $products;
        $this->view->users         = $this->loadModel('user')->getPairs('noclosed|noletter');
        $this->view->pager         = $pager;

        $this->display();
    }

    /**
     * Create a test suite.
     *
     * @param  int    $productID
     * @access public
     * @return void
     */
    public function create($applicationID, $productID)
    {
        //if($productID == 'all') $productID = 'na';
        if(!empty($_POST))
        {
            $response['result']  = 'success';
            $response['message'] = $this->lang->testsuite->successSaved;
            $suiteID = $this->testsuite->create($applicationID, $productID);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $actionID = $this->loadModel('action')->create('testsuite', $suiteID, 'opened');

            $this->executeHooks($suiteID);

            if($this->app->openApp == 'project')
            {
                $location = $this->session->testsuiteList;
            }
            else
            {
                $location = $this->createLink('testsuite', 'browse', "applicationID=$applicationID&productID=$productID", null);
            }
            $response['locate'] = $location;
            $this->send($response);
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

        /* Set menu. */
        $application   = $this->rebirth->getApplicationByID($applicationID);
        $productID     = $this->rebirth->getProductIdByApplication($applicationID, $productID);
        $products      = $this->rebirth->getProductPairs($applicationID, true);

        $this->view->title         = $this->applicationList[$applicationID] . $this->lang->colon . $this->lang->testsuite->create;
        $this->view->applicationID = $applicationID;
        $this->view->productID     = $productID;
        $this->view->products      = $products;
        $this->display();
    }

    /**
     * View a test suite.
     *
     * @param  int    $suiteID
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function view($suiteID, $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $this->app->loadLang('testtask');

        /* Get test suite, and set menu. */
        $suite = $this->testsuite->getById($suiteID, true);
        if(!$suite) die(js::error($this->lang->notFound) . js::locate('back'));
        if($suite->type == 'private' and $suite->addedBy != $this->app->user->account and !$this->app->user->admin) die(js::error($this->lang->error->accessDenied) . js::locate('back'));

        $linkDataApp     = '';
        $linkedCasesFrom = '';

        if(!isonlybody())
        {
            if($this->app->openApp == 'project')
            {
                $this->loadModel('project')->setMenu($this->session->project);
                $applicationID   = $suite->applicationID;
                $linkDataApp     = 'data-app="project"';
                $linkedCasesFrom = 'projectTestsuite';
            }
            else
            {
                /* Set product session. */
                $applicationID = $this->rebirth->saveState($this->applicationList, $suite->applicationID, $suite->product);
                $this->rebirth->setMenu($applicationID, $suite->product);
            }
        }

        $productID = $this->rebirth->getProductIdByApplication($applicationID, $suite->product);

        /* Save session. */
        $this->session->set('caseList', $this->app->getURI(true), $this->app->openApp);

        /* Append id for secend sort. */
        $sort = $this->loadModel('common')->appendOrder($orderBy);

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = pager::init($recTotal, $recPerPage, $pageID);

        $this->executeHooks($suiteID);
        $modules = array();
        if(is_numeric($productID)) $modules = $this->loadModel('tree')->getOptionMenu($suite->product, 'case');

        $this->view->title        = "SUITE #$suite->id $suite->name";
        $this->view->suite        = $suite;
        $this->view->users        = $this->loadModel('user')->getPairs('noclosed|noletter');
        $this->view->actions      = $this->loadModel('action')->getList('testsuite', $suiteID);
        $this->view->cases        = $this->testsuite->getLinkedCases($suiteID, $linkedCasesFrom, $sort, $pager);
        $this->view->orderBy      = $orderBy;
        $this->view->pager        = $pager;
        $this->view->modules      = $modules;
        $this->view->linkDataApp  = $linkDataApp;
        $this->view->branches     = array();
        $this->view->canBeChanged = common::canBeChanged('testsuite', $suite);

        $this->display();
    }

    /**
     * Edit a test suite.
     *
     * @param  int    $suiteID
     * @access public
     * @return void
     */
    public function edit($suiteID)
    {
        $suite = $this->testsuite->getById($suiteID);
        if(!empty($_POST))
        {
            $response['result']  = 'success';
            $response['message'] = $this->lang->testsuite->successSaved;
            $changes = $this->testsuite->update($suiteID);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            if($changes)
            {
                $actionID = $this->loadModel('action')->create('testsuite', $suiteID, 'edited');
                $this->action->logHistory($actionID, $changes);
            }

            $this->executeHooks($suiteID);

            $response['locate']  = inlink('view', "suiteID=$suiteID");
            $this->send($response);
        }

        if($suite->type == 'private' and $suite->addedBy != $this->app->user->account and !$this->app->user->admin) die(js::error($this->lang->error->accessDenied) . js::locate('back'));

        $applicationID = $this->rebirth->saveState($this->applicationList, $suite->applicationID, $suite->product);
        
        if($this->app->openApp == 'project')
        {
            $this->loadModel('project')->setMenu($this->session->project);
        }
        else
        {
            $this->rebirth->setMenu($applicationID, $suite->product);
        }

        /* Set product session. */
        $productID = $this->rebirth->getProductIdByApplication($applicationID, $suite->product);
        $products  = $this->rebirth->getProductPairs($applicationID, true);

        $this->view->title    = $this->applicationList[$applicationID] . $this->lang->colon . $this->lang->testsuite->edit;
        $this->view->suite    = $suite;
        $this->view->products = $products;
        $this->display();
    }

    /**
     * Delete a test suite.
     *
     * @param  int    $suiteID
     * @param  string $confirm yes|no
     * @access public
     * @return void
     */
    public function delete($suiteID, $confirm = 'no')
    {
        if($confirm == 'no')
        {
            die(js::confirm($this->lang->testsuite->confirmDelete, inlink('delete', "suiteID=$suiteID&confirm=yes")));
        }
        else
        {
            $suite = $this->testsuite->getById($suiteID);
            if($suite->type == 'private' and $suite->addedBy != $this->app->user->account and !$this->app->user->admin) die(js::error($this->lang->error->accessDenied) . js::locate('back'));

            $this->testsuite->delete($suiteID);

            $this->executeHooks($suiteID);

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
            die(js::locate($this->session->testsuiteList, 'parent'));
        }
    }

    /**
     * Link cases to a test suite.
     *
     * @param  int    $suiteID
     * @param  int    $param
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function linkCase($suiteID, $param = 0, $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        /* Save session. */
        $this->session->set('caseList', $this->app->getURI(true), 'qa');

        if(!empty($_POST))
        {
            $this->testsuite->linkCase($suiteID);
            $this->locate(inlink('view', "suiteID=$suiteID"));
        }

        $suite = $this->testsuite->getById($suiteID);


        if($suite->product == 0) $suite->product = 'na';

        $applicationID = $this->rebirth->saveState($this->applicationList, $suite->applicationID, $suite->product);

        $linkedCasesFrom = '';
        if($this->app->openApp == 'project')
        {
            $this->loadModel('project')->setMenu($this->session->project);
            $linkedCasesFrom = 'projectTestsuite';
        }
        else
        {
            $this->rebirth->setMenu($applicationID, $suite->product);
        }
        
        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = pager::init($recTotal, $recPerPage, $pageID);
        $cases = $this->testsuite->getUnlinkedCases($suite, $linkedCasesFrom, $param, $pager);

        /* Set product session. */
        $productID = $this->rebirth->getProductIdByApplication($applicationID, $suite->product);

        $modules = array();
        if(is_numeric($productID)) $modules = $this->loadModel('tree')->getOptionMenu($productID, $viewType = 'case');

        /* Build the search form. */
        $this->loadModel('testcase');
        $this->config->testcase->search['params']['module']['values'] = $modules;
        $this->config->testcase->search['module']    = 'testsuite';
        $this->config->testcase->search['actionURL'] = inlink('linkCase', "suiteID=$suiteID&param=myQueryID");
        unset($this->config->testcase->search['fields']['product']);
        unset($this->config->testcase->search['params']['product']);
        unset($this->config->testcase->search['fields']['branch']);
        unset($this->config->testcase->search['params']['branch']);
        unset($this->config->testcase->search['fields']['project']);
        unset($this->config->testcase->search['params']['project']);

        $this->config->testcase->search['params']['lib']['values'] = $this->loadModel('caselib')->getLibraries();

        if(!$this->config->testcase->needReview) unset($this->config->testcase->search['params']['status']['values']['wait']);
        $this->loadModel('search')->setSearchParams($this->config->testcase->search);

        $this->view->title   = $suite->name . $this->lang->colon . $this->lang->testsuite->linkCase;
        $this->view->users   = $this->loadModel('user')->getPairs('noletter');
        $this->view->cases   = $cases;
        $this->view->suiteID = $suiteID;
        $this->view->pager   = $pager;
        $this->view->suite   = $suite;

        $this->display();
    }

    /**
     * Remove a case from test suite.
     *
     * @param  int    $suiteID
     * @param  int    $rowID
     * @param  string $confirm
     * @access public
     * @return void
     */
    public function unlinkCase($suiteID, $rowID, $confirm = 'no')
    {
        if($confirm == 'no')
        {
            die(js::confirm($this->lang->testsuite->confirmUnlinkCase, $this->createLink('testsuite', 'unlinkCase', "rowID=$rowID&confirm=yes")));
        }
        else
        {
            $response['result']  = 'success';
            $response['message'] = '';

            $this->dao->delete()->from(TABLE_SUITECASE)->where('`case`')->eq((int)$rowID)->andWhere('suite')->eq($suiteID)->exec();
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
            }
            $this->send($response);
        }
    }

    /**
     * Batch unlink cases.
     *
     * @param  int    $suiteID
     * @access public
     * @return void
     */
    public function batchUnlinkCases($suiteID)
    {
        $caseIDList = implode(',', $this->post->caseIDList);
        if(empty($caseIDList)) die(js::error($this->lang->testsuite->noCaseSelected) . js::locate('back'));

        $this->dao->delete()->from(TABLE_SUITECASE)
            ->where('suite')->eq((int)$suiteID)
            ->andWhere('`case`')->in($caseIDList)
            ->exec();

        die(js::locate($this->createLink('testsuite', 'view', "suiteID=$suiteID")));
    }
}
