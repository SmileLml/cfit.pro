<?php
class productionchange extends control
{

    /**
     * Method: browse
     * @param string $browseType
     * @param int $param
     * @param string $orderBy
     * @param int $recTotal
     * @param int $recPerPage
     * @param int $pageID
     */
    public function browse($browseType = 'all', $param = 0, $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $browseType = strtolower($browseType);
        $queryID = ($browseType == 'bysearch')  ? (int)$param : 0;
        $actionURL = $this->createLink('productionchange', 'browse', "browseType=bySearch&param=myQueryID");

        $this->app->loadClass('pager', $static = true);
        $pager = pager::init($recTotal, $recPerPage, $pageID);

        $this->productionchange->buildSearchForm($queryID, $actionURL);

        /* 设置需求详情页面返回的url连接。*/
        $this->session->set('productionchangeList', $this->app->getURI(true), 'backlog');
        $this->session->set('common_back_url', $this->app->getURI(true),'backlog');
        //获取列表数据
        $info = $this->productionchange->getList($browseType, $queryID, $orderBy, $pager);

        $this->view->info       = $info;
        $this->view->param      = $param;
        $this->view->pager      = $pager;
        $this->view->orderBy    = $orderBy;
        $this->view->browseType = $browseType;
        $this->view->title      = $this->lang->productionchange->common;
        $this->view->apps       = $this->loadModel('application')->getPairs();
        $this->view->users      = $this->loadModel('user')->getPairs('noletter|noclosed');
        $this->view->depts      = $this->loadModel('dept')->getOptionMenu();

        $this->display();
    }

    /**
     * @Notes:create
     * @Date: 2024/4/24
     * @Time: 11:29
     * @Interface create
     */
    public function create()
    {
        /**
         * @var demandModel $demandModel
         * @var demandinsideModel $demandInsideModel
         * @var problemModel $problemModel
         * @var secondorderModel $secondOrderModel
         * @var applicationModel $applicationModel
         * @var userModel $userModel
         * @var deptModel $deptModel
         */
        $demandInsideModel  = $this->loadModel('demandinside');
        $problemModel       = $this->loadModel('problem');
        $secondOrderModel   = $this->loadModel('secondorder');
        $applicationModel   = $this->loadModel('application');
        $userModel          = $this->loadModel('user');
        $deptModel          = $this->loadModel('dept');

        if($_POST)
        {
            $preproductionID = $this->productionchange->create();

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $this->loadModel('action')->create('productionchange', $preproductionID, 'created', $this->post->comment);
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = inlink('browse');

            $this->send($response);
        }

        $this->view->getProjects    = array('' => '') + $this->getProjects();//获取项目空间（当前用忽有权限的）
        $this->view->problems       = array('' => '') + $problemModel->getPairs('noclosed');
        $this->view->secondorders   = array('' => '') + $secondOrderModel->getNamePairsAll();
        $this->view->demands        = array('' => '') + $demandInsideModel->getPairs('noclosed');
        $this->view->depts          = array('' => '') + $deptModel->getOptionMenu();
        $this->view->title          = $this->lang->productionchange->create;
        $this->view->users          = array('' => '') + $userModel->getPairs('noclosed|noletter');
        $this->view->apps           = array('' => '') + $applicationModel->getapplicationNameCodePairs();
        $this->display();
    }

    /**
     * edit
     * 
     * @param  int $preproductionID
     * @access public
     * @return void
     */
    public function edit($preproductionID = 0)
    {
        /**
         * @var demandModel $demandModel
         * @var demandinsideModel $demandInsideModel
         * @var problemModel $problemModel
         * @var secondorderModel $secondOrderModel
         * @var applicationModel $applicationModel
         * @var userModel $userModel
         * @var deptModel $deptModel
         * @var productionchangeModel $productionChangeModel
         */
        $demandInsideModel  = $this->loadModel('demandinside');
        $problemModel       = $this->loadModel('problem');
        $secondOrderModel   = $this->loadModel('secondorder');
        $applicationModel   = $this->loadModel('application');
        $userModel          = $this->loadModel('user');
        $deptModel          = $this->loadModel('dept');
        $productionChangeModel = $this->loadModel('productionchange');
        $productionChangeInfo  = $productionChangeModel->getByID($preproductionID,true);
        if($_POST)
        {
            $changes = $this->productionchange->update($preproductionID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $actionID = $this->loadModel('action')->create('productionchange', $preproductionID, 'edited', $this->post->comment);
            $this->action->logHistory($actionID, $changes);
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = isonlybody() ? 'parent' : inlink('view', "preproductionID=$preproductionID");

            $this->send($response);
        }

        $this->view->getProjects    = array('' => '') + $this->getProjects();//获取项目空间（当前用忽有权限的）
        $this->view->releases       = array('' => '') + $this->getProjectRelease($productionChangeInfo->space); //发布
        $this->view->problems       = array('' => '') + $problemModel->getPairs('noclosed');
        $this->view->secondorders   = array('' => '') + $secondOrderModel->getNamePairsAll();;
        $this->view->demands        = array('' => '') + $demandInsideModel->getPairs('noclosed');
        $this->view->depts          = array('' => '') + $deptModel->getOptionMenu();
        $this->view->title          = $this->lang->productionchange->edit;
        $this->view->users          = array('' => '') + $userModel->getPairs('noclosed|noletter');
        $this->view->apps           = array('' => '') + $applicationModel->getapplicationNameCodePairs();
        $this->view->productionChangeInfo  = $productionChangeInfo;
        $this->display();
    }

    /**
     * @Notes:详情页
     * @Date: 2024/4/24
     * @Time: 17:32
     * @Interface view
     * @param int $preproductionID
     */
    public function view($preproductionID = 0)
    {
        /**
         * @var demandModel $demandModel
         * @var demandinsideModel $demandInsideModel
         * @var problemModel $problemModel
         * @var secondorderModel $secondOrderModel
         * @var applicationModel $applicationModel
         * @var userModel $userModel
         * @var deptModel $deptModel
         * @var iwfpModel $iwfpModel
         * @var projectModel $projectModel
         * @var productionchangeModel $productionChangeModel
         */
        $demandInsideModel  = $this->loadModel('demandinside');
        $problemModel       = $this->loadModel('problem');
        $secondOrderModel   = $this->loadModel('secondorder');
        $applicationModel   = $this->loadModel('application');
        $userModel          = $this->loadModel('user');
        $deptModel          = $this->loadModel('dept');
        $iwfpModel          = $this->loadModel('iwfp');
        $productionChangeModel = $this->loadModel('productionchange');
        $projectModel      = $this->loadModel('project');

        $productionChangeInfo  = $productionChangeModel->getByID($preproductionID);
        //实际上线时间格式处理
        $actualOnlineTime =  $productionChangeInfo->actualOnlineTime;
        $productionChangeInfo->actualOnlineTime = $actualOnlineTime != '0000-00-00 00:00:00' && !empty($actualOnlineTime) ? $actualOnlineTime : '';

        $productionChangeInfo->files = $this->loadModel('file')->getByObject('productionchange', $preproductionID);
        //需求条目数据构造，用于增加超链接直接查看
        $correlationDemandArray = explode(',',$productionChangeInfo->correlationDemand);
        if(!empty($correlationDemandArray))
        {
            $correlationDemandInfo = $demandInsideModel->getPairsByIds($correlationDemandArray);
            $this->view->correlationDemandInfo  = $correlationDemandInfo;
        }

        //问题单数据构造，用于增加超链接直接查看
        $correlationProblemArray = explode(',',$productionChangeInfo->correlationProblem);
        if(!empty($correlationProblemArray))
        {
            $correlationProblemInfo = $problemModel->getPairsByIds($correlationProblemArray);
            $this->view->correlationProblemInfo = $correlationProblemInfo;
        }

        //任务工单数据构造，用于增加超链接直接查看
        $correlationSecondaryArray = explode(',',$productionChangeInfo->correlationSecondorder);
        if(!empty($correlationSecondaryArray))
        {
            $correlationSecondaryInfo = $secondOrderModel->getPairsByIds($correlationSecondaryArray);
            $this->view->correlationSecondaryInfo = $correlationSecondaryInfo;
        }

        $consumed = $this->productionchange->getConsumedByID($preproductionID);
        $nodes = $iwfpModel->getCurrentVersionReviewNodes($productionChangeInfo->processInstanceId, $productionChangeInfo->version);

        $this->view->projects       = $projectModel->getPairs();//获取全部项目空间
        $this->view->releases       = array('' => '') + $this->getProjectRelease($productionChangeInfo->space); //发布
        $this->view->nodes          = $nodes;
        $this->view->actions        = $this->loadModel('action')->getList('productionchange', $preproductionID);
        $this->view->problems       = array('' => '') + $problemModel->getPairs('noclosed');
        $this->view->secondorders   = array('' => '') + $secondOrderModel->getNamePairsAll();
        $this->view->demands        = array('' => '') + $demandInsideModel->getPairs('noclosed');
        $this->view->depts          = array('' => '') + $deptModel->getOptionMenu();
        $this->view->title          = $this->lang->productionchange->create;
        $this->view->users          = array('' => '') + $userModel->getPairs('noclosed|noletter');
        $this->view->apps           = array('' => '') + $applicationModel->getapplicationNameCodePairs();
        $this->view->productionChangeInfo       = $productionChangeInfo;
        $this->view->consumeds      = $consumed;

        $this->display();
    }

    /**
     * @Notes:申请审批投产/变更
     * @Date: 2024/4/29
     * @Time: 10:15
     * @Interface deal
     * @param $preproductionID
     */
    public function deal($preproductionID)
    {
        /**
         * @var userModel $userModel
         * @var deptModel $deptModel
         * @var productionchangeModel $productionChangeModel
         */
        $userModel          = $this->loadModel('user');
        $deptModel          = $this->loadModel('dept');
        $productionChangeModel = $this->loadModel('productionchange');
        $productionChangeInfo  = $productionChangeModel->getByID($preproductionID);

        if ($_POST)
        {
            $changes = $this->productionchange->deal($preproductionID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $this->loadModel('action')->create('productionchange', $preproductionID, 'submitreview', $this->post->comment);

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = isonlybody() ? 'parent' : inlink('view', "preproductionID=$preproductionID");

            $this->send($response);
        }

        $this->view->defaultMailto          = $this->productionchange->getDefaultMailto($productionChangeInfo);
        $this->view->depts                  = array('' => '') + $deptModel->getOptionMenu();
        $this->view->productionChangeInfo   = $productionChangeInfo;
        $this->view->users                  = array('' => '') + $userModel->getPairs('noclosed|noletter');
        $this->display();
    }

    /**
     * @Notes:审批
     * @Date: 2024/5/6
     * @Time: 15:45
     * @Interface review
     * @param $preproductionID
     */
    public function review($preproductionID)
    {
        /**
         * @var userModel $userModel
         * @var deptModel $deptModel
         * @var productionchangeModel $productionChangeModel
         */
        $userModel          = $this->loadModel('user');
        $deptModel          = $this->loadModel('dept');
        $productionChangeModel = $this->loadModel('productionchange');
        $productionChangeInfo  = $productionChangeModel->getByID($preproductionID);
        $account = $this->app->user->account;

        //增加登录人是实施人员或复核人员标识 personType 1:实施人员 2：复核人员
        $productionChangeInfo->personType = 1;
        if(!empty($productionChangeInfo->reviewPerson))
        {
            if(in_array($account,explode(',',$productionChangeInfo->reviewPerson)))
            {
                $productionChangeInfo->personType = 2;
            }
        }

        if ($_POST)
        {
            $this->productionchange->review($productionChangeInfo);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $this->loadModel('action')->create('productionchange', $preproductionID, 'reviewed', $this->post->comment);

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = isonlybody() ? 'parent' : inlink('view', "preproductionID=$preproductionID");

            $this->send($response);
        }

        $dept = $this->loadModel('user')->getByAccount($account);
        $deptId = empty($dept->dept) ? '' : $dept->dept;
        $deptInfo = $this->loadModel('dept')->getFieldByDeptId('manager',$deptId);

        $this->view->defaultMailto          = $this->productionchange->getDefaultMailto($productionChangeInfo);
        $this->view->deptManagers           = $deptInfo->manager ?? '';
        $this->view->depts                  = array('' => '') + $deptModel->getOptionMenu();
        $this->view->productionChangeInfo   = $productionChangeInfo;
        $this->view->users                  = array('' => '') + $userModel->getPairs('noclosed|noletter');
        $this->display();
    }

    /**
     * @Notes:导出数据
     * @Date: 2024/4/25
     * @Time: 16:05
     * @Interface export
     * @param string $orderBy
     * @param string $browseType
     */
    public function export($orderBy = 'id_desc', $browseType = 'all')
    {

        if($_POST)
        {
            /**
             * @var demandModel $demandModel
             * @var demandinsideModel $demandInsideModel
             * @var problemModel $problemModel
             * @var secondorderModel $secondOrderModel
             * @var applicationModel $applicationModel
             * @var userModel $userModel
             * @var deptModel $deptModel
             * @var projectModel $projectModel
             * @var productionchangeModel $productionChangeModel
             */
            $demandInsideModel  = $this->loadModel('demandinside');
            $problemModel       = $this->loadModel('problem');
            $secondOrderModel   = $this->loadModel('secondorder');
            $applicationModel   = $this->loadModel('application');
            $userModel          = $this->loadModel('user');
            $deptModel          = $this->loadModel('dept');
            $projectModel       = $this->loadModel('project');

            $demands  = $demandInsideModel->getPairs('noclosed');
            $projects = $projectModel->getPairs();
            $problems = $problemModel->getPairs('noclosed');
            $secondOrders = $secondOrderModel->getNamePairsAll();
            $depts = $deptModel->getOptionMenu();
            $users = $userModel->getPairs('noclosed|noletter');
            $apps  = $applicationModel->getapplicationNameCodePairs();
            $releases = $this->getProjectRelease();

            $this->loadModel('file');
            $preproductionLang   = $this->lang->productionchange;
            $preproductionConfig = $this->config->productionchange;
            $this->app->loadLang('opinion');
            $this->app->loadLang('application');

            /* Create field lists. */
            $fields = $this->post->exportFields ? $this->post->exportFields : explode(',', $preproductionConfig->list->exportFields);

            foreach($fields as $key => $fieldName)
            {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = isset($preproductionLang->$fieldName) ? $preproductionLang->$fieldName : $fieldName;
                unset($fields[$key]);
            }

            /* Get defects. */
            $preproductionInfo = array();
            if($this->session->productionchangeOnlyCondition)
            {
                $preproductionInfo = $this->dao->select($preproductionConfig->list->exportFields)->from(TABLE_PRODUCTIONCHANGE)
                    ->where($this->session->productionchangeQueryCondition)
                    ->andWhere('deleted')->eq('0')
                    ->beginIF($this->post->exportType == 'selected')->andWhere('id')->in($this->cookie->checkedItem)->fi()
                    ->orderBy($orderBy)
                    ->fetchAll('id');
            }
            else
            {
                $stmt = $this->dbh->query($this->session->productionchangeQueryCondition . ($this->post->exportType == 'selected' ? " AND id IN({$this->cookie->checkedItem})" : '') . " ORDER BY " . strtr($orderBy, '_', ' '));
                while($row = $stmt->fetch()) $preproductionInfo[$row->id] = $row;
            }

            foreach($preproductionInfo as $preproduction)
            {
                $preproduction->id            = $preproduction->id;
                $preproduction->code          = $preproduction->code;
                $preproduction->applicant     = zget($users,$preproduction->applicant);
                $preproduction->applicantDept = zget($depts,$preproduction->applicantDept);
                $preproduction->onlineType    = zget($preproductionLang->onlineTypeList,$preproduction->onlineType);
                $preproduction->status        = zget($preproductionLang->statusList,$preproduction->status);
                $preproduction->dealUser      = zget($users,$preproduction->dealUser);
                $preproduction->createdBy     = zget($users,$preproduction->createdBy);
                $preproduction->createdDate   = $preproduction->createdDate;
                $preproduction->application   = zmget($apps,$preproduction->application);
                $preproduction->abstract      = strip_tags($preproduction->abstract);
                $preproduction->onlineStart   = $preproduction->onlineStart;
                $preproduction->onlineEnd     = $preproduction->onlineEnd;
                $preproduction->implementContent  = strip_tags($preproduction->implementContent);
                $preproduction->effect        = strip_tags($preproduction->effect);
                $preproduction->ifEffectSystem= zget($preproductionLang->ifEffectSystemList,$preproduction->ifEffectSystem);
                $preproduction->effectSystemExplain  = strip_tags($preproduction->effectSystemExplain);
                $preproduction->materialExplain  = strip_tags($preproduction->materialExplain);
                $preproduction->record  = strip_tags($preproduction->record);
                $preproduction->materialExplain  = strip_tags($preproduction->materialExplain);
                $preproduction->remark         =   strip_tags($preproduction->remark);
                $preproduction->space          =   zmget($projects,$preproduction->space);
                $preproduction->correlationPublish  = zget($releases,$preproduction->correlationPublish);
                $preproduction->correlationDemand  = zmget($demands,$preproduction->correlationDemand);
                $preproduction->correlationProblem  = zmget($problems,$preproduction->correlationProblem);
                $preproduction->correlationSecondorder  = zmget($secondOrders,$preproduction->correlationSecondorder);

                $preproduction->ifReport= zget($preproductionLang->ifReportList,$preproduction->ifReport);
                $preproduction->deptConfirmPerson  = zmget($users,$preproduction->deptConfirmPerson);
                $preproduction->interfacePerson    = zmget($users,$preproduction->interfacePerson);
                $preproduction->mediaPackage       = $preproduction->mediaPackage;
                $preproduction->operationPerson    = zmget($users,$preproduction->operationPerson);
            }

            $this->post->set('fields', $fields);
            $this->post->set('rows', $preproductionInfo);
            $this->post->set('kind',  $this->lang->productionchange->exportName);
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        $this->view->fileName        = $this->lang->productionchange->exportName;
        $this->view->allExportFields = $this->config->productionchange->list->exportFields;
        $this->view->customExport    = true;
        $this->display();
    }

    /**
     * @Notes:历史审批记录
     * @Date: 2024/5/10
     * @Time: 11:09
     * @Interface showHistoryNodes
     * @param $preproductionID
     */
    function showHistoryNodes($preproductionID){
        $this->view->title = $this->lang->productionchange->showHistoryNodes;
        $preproductionInfo = $this->productionchange->getByID($preproductionID);
        $nodes = $this->loadModel('iwfp')->getAllVersionReviewNodes($preproductionInfo->processInstanceId);
        if($nodes){
            $users =  $this->loadModel('user')->getPairs('noletter');
            $this->view->users = $users;
        }
        $this->view->nodes = $nodes;
        $this->view->preproductionInfo = $preproductionInfo;
        $this->display();
    }


    /**
     * @Notes:上传附件
     * @Date: 2024/5/10
     * @Time: 18:46
     * @Interface uploadFile
     * @param $preproductionID
     */
    public function uploadFile($preproductionID)
    {
        if($_POST)
        {
            $this->productionchange->uploadFile($preproductionID);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $this->loadModel('action')->create('productionchange', $preproductionID, 'uploadFile');

            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $this->display();
    }

    /**
     * @Notes:申请人对应部门获取
     * @Date: 2024/4/26
     * @Time: 16:31
     * @Interface ajaxGetDeptByAccount
     * @param $account
     */
    public function ajaxGetDeptByAccount($account)
    {
        $dept = $this->loadModel('user')->getByAccount($account);
        $deptId = empty($dept->dept) ? '' : $dept->dept;
        $pairs = array('' => '') + $this->loadModel('dept')->getOptionMenu();
        echo html::select('applicantDept', $pairs, $deptId, "class='form-control chosen'");
    }

    /**
     * @Notes:
     * @Date: 2024/4/30
     * @Time: 10:51
     * @Interface ajaxGetDeptByID
     * @param $deptID
     */
    public function ajaxGetDeptByID($deptID)
    {
        $deptInfo = $this->loadModel('dept')->getFieldByDeptId('manager',$deptID);
        $users =  array('' => '') + $this->loadModel('user')->getPairs('noletter|noclosed');
        echo html::select('deptConfirmPerson[]', $users, $deptInfo->manager, "class='form-control chosen'multiple");
    }

    /**
     * @Notes:
     * @Date: 2024/4/30
     * @Time: 10:51
     * @Interface ajaxGetDeptByID
     * @param $projectIDs
     */
    public function ajaxGetRelease($projectIDs)
    {
        $releases = $this->getProjectRelease($projectIDs);
        echo html::select('correlationPublish[]', $releases, '', "class='form-control chosen' multiple");
    }

    /**
     * @Notes:获取项目空间
     * @Date: 2024/5/7
     * @Time: 18:52
     * @Interface getProjects
     * @return mixed
     */
    public function getProjects()
    {
        $programTitle = $this->loadModel('setting')->getItem('owner=' . $this->app->user->account . '&module=project&key=programTitle');
        $res = $this->loadModel('program')->getProjectStats(0, 'doing', 0, 'id_desc', null, $programTitle);
        $projectsArray = [];
        foreach ($res as $key => $project){
            $projectsArray[$project->id] = $project->name;
        }
        return $projectsArray;
    }

    /**
     * @Notes:获取发布
     * @Date: 2024/5/7
     * @Time: 19:04
     * @Interface getProjectRelease
     * @param string $projectIDs
     * @return mixed
     */
    public function getProjectRelease($projectIDs = '')
    {
        $res = $this->dao->select('id,name')
            ->from(TABLE_RELEASE)
            ->where('deleted')->eq(0)
            ->beginIF(!empty($projectIDs))->andWhere('project')->in($projectIDs)
            ->orderBy('date DESC')
            ->fetchAll('id');
        $releasesArray = [];
        foreach ($res as $release){
            $releasesArray[$release->id] = $release->name;
        }
        return $releasesArray;
    }


    /**
     * 检查权限
     * @param $id
     * @param $action
     * @return void
     */
    public function ajaxIsClickable($id, $action)
    {
        $info = $this->loadModel('productionchange')->getByID($id, true);

        $res  = $this->loadModel('productionchange')->isClickable($info, $action);
        if(!$res){
            die(json_encode(['code' => 1001, 'message' => 'fail', 'data' => $this->lang->productionchange->authError]));
        }
        if('deal' == $action){//检查数据是否完整
            $this->loadModel('productionchange')->checkRequired($info);
            if(dao::isError()){
                die(json_encode(['code' => 1002, 'fail' => 'success', 'data' => implode(dao::getError())]));
            }
        }

        die(json_encode(['code' => 0, 'message' => 'success', 'data' => 1]));
    }
}
