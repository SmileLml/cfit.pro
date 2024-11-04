<?php
class defect extends control
{

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
        $this->loadModel('project');
        $this->loadModel('datatable');
        $this->app->loadLang('report');

        /* Get product data. */
        $objectID = 0;
        $applicationList = $this->rebirth->getApplicationPairs();

        $this->view->applicationList = $this->applicationList = $applicationList;

        if(empty($applicationList) and !helper::isAjaxRequest()) die($this->locate($this->createLink('application', 'create')));
    }

    /**
     * Method: browse
     * @param string $browseType
     * @param int $param
     * @param string $orderBy
     * @param int $recTotal
     * @param int $recPerPage
     * @param int $pageID
     */
    public function browse($browseType = 'all', $param = 0, $extra = '', $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $browseType = strtolower($browseType);
        $applicationList = $this->rebirth->getApplicationPairs();
        $applicationID   = $this->session->applicationID ? $this->session->applicationID : 0;
        $productID       = 'all';
        $applicationID = $this->rebirth->saveState($applicationList, $applicationID, $productID);
        $application   = $this->rebirth->getApplicationByID($applicationID);
        $this->rebirth->setMenu($applicationID, $productID);
        $productID     = $this->rebirth->getProductIdByApplication($applicationID, $productID);
        $this->lang->switcherMenu = "";

        /* 设置详情页面返回的url连接。*/
        $this->session->set('defectList', $this->app->getURI(true), $this->app->openApp);

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = pager::init($recTotal, $recPerPage, $pageID);
        $queryID = ($browseType == 'bysearch')  ? (int)$param : 0;
        $actionURL = $this->createLink('defect', 'browse', "browseType=bySearch&param=myQueryID");

        $this->defect->buildSearchForm($queryID, $actionURL);


        $defects = $this->defect->getListAll($browseType,$extra, $orderBy, $pager);
        $this->view->title      = $this->lang->defect->common;
        $products  = $this->loadModel('product')->getSimplePairs();
        $this->view->products       = $products;
        $this->view->extra    = $extra;
        $this->view->orderBy    = $orderBy;
        $this->view->browseType    = $browseType;
        $this->view->param    = $param;
        $this->view->pager      = $pager;
        $this->view->projects      = $this->loadModel('project')->getProjects();
        $this->view->users      = $this->loadModel('user')->getPairs('noletter|noclosed');
        $this->view->defects = $defects;
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: create
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:52
     * Desc: This is the code comment. This method is called create.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     */
    public function create()
    {
        if($_POST)
        {
            $defectID = $this->defect->create();

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $this->loadModel('action')->create('defect', $defectID, 'created', $this->post->comment);
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = inlink('browse');

            $this->send($response);
        }

        $this->view->title = $this->lang->defect->create;
        $this->view->users = $this->loadModel('user')->getPairs('noclosed');
        $this->view->apps  = array('' => '') + $this->loadModel('application')->getapplicationNameCodePairs();
        $this->view->childTypeList = array();
        $this->app->loadLang('opinion');
        $this->app->loadLang('application');
        $this->display();
    }

    /**
     * Edit a defect.
     * 
     * @param  int $defectID 
     * @access public
     * @return void
     */
    public function edit($defectID = 0)
    {
        if($_POST)
        {
            $changes = $this->defect->update($defectID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

//            if($changes || $this->post->comment)
//            {
                $actionID = $this->loadModel('action')->create('defect', $defectID, 'edited', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
//            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = isonlybody() ? 'parent' : inlink('view', "defectID=$defectID");

            $this->send($response);
        }

        $applicationList = $this->rebirth->getApplicationPairs();
        $applicationID   = $this->session->applicationID ? $this->session->applicationID : 0;
        $productID       = 0;
        $applicationID = $this->rebirth->saveState($applicationList, $applicationID, $productID);
        $application   = $this->rebirth->getApplicationByID($applicationID);
        $this->rebirth->setMenu($applicationID, $productID);
        $productID     = $this->rebirth->getProductIdByApplication($applicationID, $productID);
        /* 设置详情页面返回的url连接。*/
        $this->session->set('defectView', $this->app->getURI(true), $this->app->openApp);

        $this->view->title   = $this->lang->defect->edit;
        $this->view->users   = $this->loadModel('user')->getPairs('noclosed,noletter');
        $defect = $this->defect->getByID($defectID);
        $defect = $this->loadModel('file')->replaceImgURL($defect,'issues');

        $bug       = $this->loadModel('bug')->getById($defect->bugId);
        $productID = $bug->product;
        $allBuilds    = $this->loadModel('build')->getProductBuildPairs($productID, $branch = 0, 'noempty');
        $openedBuilds = $this->build->getProductBuildPairs($productID, $bug->branch, 'noempty,noterminate,nodone');

        if($this->app->openApp == 'project')
        {
            $this->loadModel('project')->setMenu($defect->project);
        }


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

        $this->view->resolvedBuilds   = array('' => '') + $openedBuilds + $oldResolvedBuild;

        $this->view->apps  = array('' => '') + $this->loadModel('application')->getapplicationNameCodePairs();
        $this->view->productList= array('0' => '无') + $this->loadModel('product')->getPairs();
        $this->view->projects   = array('' => '') + $this->loadModel('project')->getPairs('noclosed');
        $this->view->defect = $defect;
        $this->view->childTypeList = $this->loadModel('bug')->getChildTypeList($defect->type);
        $this->app->loadLang('bug');
        $this->display();
    }




    /**
     * Project: chengfangjinke
     * Method: editAssignedTo
     * Product: PhpStorm
     * @param int $defectID
     */
    public function editAssignedTo($defectID = 0)
    {
        if($_POST)
        {
            $changes = $this->defect->editAssignedTo($defectID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $actionID = $this->loadModel('action')->create('defect', $defectID, 'editAssignTo', $this->post->comment);
            $this->action->logHistory($actionID, $changes);

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $this->view->title      = $this->lang->defect->editAssignedTo;
        $this->view->defect    = $this->defect->getByID($defectID);
        $this->view->users      = $this->loadModel('user')->getPairs('noclosed');

        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: view
     * Product: PhpStorm
     * @param int $defectID
     */
    public function view($defectID = 0)
    {
        $defect = $this->defect->getByID($defectID);
        $consumeds = $this->defect->getConsumedsByID($defect->id);
        if($this->app->openApp == 'project')
        {
            $this->loadModel('project')->setMenu($defect->project);
        }
        $bug       = $this->loadModel('bug')->getById($defect->bugId);
        $productID = $bug->product;
        $allBuilds    = $this->loadModel('build')->getProductBuildPairs($productID, $branch = 0, 'noempty');
        $openedBuilds = $this->build->getProductBuildPairs($productID, $bug->branch, 'noempty,noterminate,nodone');

        if($this->app->openApp == 'project')
        {
            $this->loadModel('project')->setMenu($defect->project);
        }

        $applicationList = $this->rebirth->getApplicationPairs();
        $applicationID   = $this->session->applicationID ? $this->session->applicationID : 0;
        $productID       = 0;
        $applicationID = $this->rebirth->saveState($applicationList, $applicationID, $productID);
        $application   = $this->rebirth->getApplicationByID($applicationID);
        $this->rebirth->setMenu($applicationID, $productID);
        $productID     = $this->rebirth->getProductIdByApplication($applicationID, $productID);
        /* 设置详情页面返回的url连接。*/
        $this->session->set('defectView', $this->app->getURI(true), $this->app->openApp);


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

        $this->view->resolvedBuilds   = array('' => '') + $openedBuilds + $oldResolvedBuild;
        $defect = $this->loadModel('file')->replaceImgURL($defect,'issues');

        $this->view->title   = $this->lang->defect->view;
        $this->view->users   = $this->loadModel('user')->getPairs('noclosed|noletter');
        $this->view->actions = $this->loadModel('action')->getList('defect', $defectID);
        $this->view->defect = $defect;
        $this->view->consumeds  = $consumeds;
//        $this->view->apps    = $this->loadModel('application')->getapplicationInfo();
        $this->view->apps    = $this->loadmodel('application')->getapplicationNameCodePairs();
        $this->view->products= array('0' => '无') + $this->loadModel('product')->getPairs();
        $this->view->projects   = array('' => '') + $this->loadModel('project')->getPairs('noclosed');
        $this->view->depts   = $this->loadModel('dept')->getOptionMenu();
        $this->view->productenrollCode   = $this->loadModel('productenroll')->getCodeById($defect->productenrollId);
        $this->view->testrequestCode   = $this->loadModel('testingrequest')->getCodeById($defect->testrequestId);
        $this->view->modifycnccCode   = $this->loadModel('modifycncc')->getCodeById($defect->modifycnccId);
        $this->view->defectTypeList = $this->loadModel('bug')->getChildTypeTileList();
        //所属CBP项目
        $this->view->cbpprojectList     = array('' => '') + $this->dao->select('code,concat(concat(concat(code,"（"),name),"）")')->from(TABLE_CBPPROJECT)->where('deleted')->ne(1)->fetchPairs();
        $this->app->loadLang('bug');
        $this->app->loadLang('testingrequest');
        $this->app->loadLang('build');
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: deal
     * Product: PhpStorm
     * @param $defectID
     */
    public function deal($defectID)
    {
        if($_POST)
        {
            $changes = $this->defect->deal($defectID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $projectID = $this->post->project;
            $response['locate']  = isonlybody() ? 'parent' : $this->createLink('project', 'defect',"projectID=$projectID");

            $this->send($response);
        }

        $defect = $this->loadModel('defect')->getByID($defectID);
        if($defect->status == 'nextfix') $defect->dealSuggest = 'fix';
        if($this->app->openApp == 'project')
        {
            $this->loadModel('project')->setMenu($defect->project);
        }
        $this->view->title      = $this->lang->defect->deal;
        $this->view->users      = $this->loadModel('user')->getPairs('noclosed');
        $defect->submitChangeDate = $defect->submitChangeDate != '0000-00-00 00:00:00' ? substr($defect->submitChangeDate, 0, 10) : '';
        $defect->changeDate = $defect->changeDate != '0000-00-00 00:00:00' ? substr($defect->changeDate, 0, 10) : '';
        if(empty($defect->cc)){
            $usersDept = $this->loadModel('user')->getUserDeptIds($this->app->user->account);
            if(!empty($usersDept)){
                $deptId = $usersDept[0];
            }
            $deptList = explode(",", $this->config->bug->allowDeptList);
            if(in_array($deptId, $deptList)){
                //部门负责人和测试负责人
                $dept  = $this->loadModel("dept")->getById($deptId);
                $defect->cc = $defect->cc.','.$dept->manager.','.$dept->testLeader;
            }
        }
        $this->view->defect    = $defect;
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: change
     * Product: PhpStorm
     * @param $defectID
     */
    public function change($defectID)
    {
        if($_POST)
        {
            $changes = $this->defect->change($defectID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

//            $actionID = $this->loadModel('action')->create('defect', $defectID, 'applychange', $this->post->comment);

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $projectID = $this->post->project;
            $response['locate']  = isonlybody() ? 'parent' : $this->createLink('project', 'defect',"projectID=$projectID");

            $this->send($response);
        }

        $defect = $this->loadModel('defect')->getByID($defectID);

        $bug       = $this->loadModel('bug')->getById($defect->bugId);
        $productID = $bug->product;
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

        $this->view->resolvedBuilds   = array('' => '') + $openedBuilds + $oldResolvedBuild;

        if($this->app->openApp == 'project')
        {
            $this->loadModel('project')->setMenu($defect->project);
        }
        $this->view->title      = $this->lang->defect->deal;
        $this->view->users      = $this->loadModel('user')->getPairs('noclosed');
        $defect->submitChangeDate = $defect->submitChangeDate != '0000-00-00 00:00:00' ? substr($defect->submitChangeDate, 0, 10) : '';
        $defect->changeDate = $defect->changeDate != '0000-00-00 00:00:00' ? substr($defect->changeDate, 0, 10) : '';
        $this->view->defect    = $defect;
        $this->display();
    }


    /**
     * Project: chengfangjinke
     * Method: confirm
     * @param int $defectID
     */
    public function confirm($defectID = 0)
    {
        if($_POST)
        {
            $this->defect->confirm($defectID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $this->loadModel('action')->create('defect', $defectID, 'reviewedconfirm', $this->post->comment);

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $this->view->title   = $this->lang->defect->confirm;
        $this->view->users      = $this->loadModel('user')->getPairs('noclosed');
        $this->view->defect = $this->loadModel('defect')->getByID($defectID);
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: export
     * @param string $orderBy
     * @param string $browseType
     */
    public function export($orderBy = 'id_desc', $browseType = 'all')
    {
        /* format the fields of every defect in order to export data. */
        if($_POST)
        {
            $this->loadModel('file');
            $defectLang   = $this->lang->defect;
            $bugLang   = $this->lang->bug;
            $testingRequestLang   = $this->lang->testingrequest;
            $defectConfig = $this->config->defect;
            $this->app->loadLang('opinion');
            $this->app->loadLang('application');
            $this->app->loadLang('testingrequest');

            /* Create field lists. */
            $fields = $this->post->exportFields ? $this->post->exportFields : explode(',', $defectConfig->list->exportFields);
            foreach($fields as $key => $fieldName)
            {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = isset($defectLang->$fieldName) ? $defectLang->$fieldName : $fieldName;
                unset($fields[$key]);
            }
            /* Get defects. */
            $defects = array();
            if($this->session->defectOnlyCondition)
            {
                $defects = $this->dao->select('*')->from(TABLE_DEFECT)->where($this->session->defectQueryCondition)
                    ->andWhere('deleted')->ne('1')
                    ->beginIF($this->post->exportType == 'selected')->andWhere('id')->in($this->cookie->checkedItem)->fi()
                    ->orderBy($orderBy)->fetchAll('id');
            }
            else
            {
                $stmt = $this->dbh->query($this->session->defectQueryCondition . ($this->post->exportType == 'selected' ? " AND id IN({$this->cookie->checkedItem})" : '') . " ORDER BY " . strtr($orderBy, '_', ' '));
                while($row = $stmt->fetch()) $defects[$row->id] = $row;
            }
            $defectIdList = array_keys($defects);

            /* Get users, products and executions. */
            $users = $this->loadModel('user')->getPairs('noletter');
            $apps  = $this->loadModel('application')->getapplicationNameCodePairs();
            $depts = $this->loadModel('dept')->getTopPairs();
            $childTypeList =  $this->loadModel('bug')->getChildTypeTileList();
            $products  = $this->loadModel('product')->getSimplePairs();
            $project   = $this->loadModel('project')->getProjects();

            foreach($defects as $defect)
            {
                //解决版本
                $bug       = $this->loadModel('bug')->getById($defect->bugId);
                $productID = $bug->product;
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

                $resolvedBuilds   = array('' => '') + $openedBuilds + $oldResolvedBuild;

                $defect->source     = $defectLang->sourceList[$defect->source];
                $defect->app        = zget($apps, $defect->app, '');
                $defect->product    = zget($products, $defect->product, '');
                $defect->project    = zget($project, $defect->project, '');
                $defect->reportUser = zget($users, $defect->reportUser, '');
                $defect->pri        = $bugLang->defectPriList[$defect->pri];
                $defect->type       = $bugLang->typeList[$defect->type];
                $defect->childType  = !empty($defect->childType) ? $childTypeList[$defect->childType] : '';
                $defect->severity   = $bugLang->defectSeverityList[$defect->severity];
                $defect->frequency  = $bugLang->defectFrequencyList[$defect->frequency];
                $defect->developer  = zget($users, $defect->developer, '');
                $defect->dept       = zget($depts, $defect->dept, '');
                $defect->testEngineer  = zget($users, $defect->tester, '');
                $defect->testType   = $testingRequestLang->acceptanceTestTypeList[$defect->testType];
                $defect->projectManager  = zget($users, $defect->projectManager, '');
                $defect->testEnvironment = !empty($defect->testEnvironment) ? $defectLang->testEnvironmentList[$defect->testEnvironment] : '';
                $defect->verification    = $defectLang->verificationList[$defect->verification];
                $defect->testrequest     = $this->loadModel('testingrequest')->getCodeById($defect->testrequestId);
                $defect->productenroll   = $this->loadModel('productenroll')->getCodeById($defect->productenrollId);
                $defect->nextUser        = zget($users, $defect->dealUser, '');
                $defect->createdBy       = zget($users, $defect->createdBy, '');
                $defect->confirmedBy     = zget($users, $defect->confirmedBy, '');
                $defect->dealedBy        = zget($users, $defect->dealedBy, '');
                $defect->syncStatus      = $defectLang->syncStatusList[$defect->syncStatus];
                $defect->defectTitle     = $defect->title;
                $defect->uatId           = $defect->uatId;
                $defect->steps           = strip_tags($defect->issues);
                $defect->resolution      = !empty($defect->resolution) ? $bugLang->resolutionList[$defect->resolution] : '';
                $defect->resolvedBuild   = zget($resolvedBuilds, $defect->resolvedBuild, '');
                $defect->ifTest          = $defectLang->ifList[$defect->ifTest];
                $defect->dealSuggest     = $defectLang->dealSuggestList[$defect->dealSuggest];
                $defect->dealComment     = strip_tags($defect->dealComment);
                $defect->ifHisIssue      = $defectLang->ifList[$defect->ifHisIssue];
            }
            $this->post->set('fields', $fields);
            $this->post->set('rows', $defects);
            $this->post->set('kind', '清总缺陷');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        $this->view->fileName        = $this->lang->defect->exportName;
        $this->view->allExportFields = $this->config->defect->list->exportFields;
        $this->view->customExport    = true;
        $this->display();
    }


    public function rePush($defectID = 0)
    {

        if($_POST)
        {
            $this->defect->rePush($defectID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }
        $this->view->defect = $this->defect->getByID($defectID);
        $this->display();
    }

    /**
     * @param string $type
     * 获取子类型
     */
    public function ajaxGetChildTypeList($type = '', $number = '')
    {

        if($number === '')
        {
            $list = $this->loadModel('bug')->getChildTypeList($type);
            die(html::select('childType', $list, '', 'class=form-control'));
        }
        else
        {
            $childTypeName = "childTypes[$number]";
            $childTypeList = $this->loadModel('bug')->getChildTypeList($type);
            die(html::select($childTypeName, $childTypeList, '', "class='form-control'"));
        }
    }
    //清总缺陷通知PM
    public function remindProjectManagerMail(){
        $this->defect->remindProjectManagerMail();
    }

}
