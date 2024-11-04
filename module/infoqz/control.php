<?php
class infoqz extends control
{
    public function __construct()
    {
        parent::__construct();
        // 上海分公司审核节点名称修改
        if (in_array($this->app->getMethodName(),['create','copy'])){
            $this->infoqz->resetNodeAndReviewerName();
        }
    }
    /**
     * Project: chengfangjinke
     * Method: fix
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:42
     * Desc: This is the code comment. This method is called fix.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param string $browseType
     * @param int $param
     * @param string $orderBy
     * @param int $recTotal
     * @param int $recPerPage
     * @param int $pageID
     */
    public function fix($browseType = 'all', $param = 0, $orderBy = 'createdDate_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $browseType = strtolower($browseType);

        /* By search. */
        $depts = $this->loadModel('dept')->getOptionMenu();
        $depts[0] = '';
        $this->config->infoqz->search['params']['createdDept']['values'] += $depts;

        $projects = $this->loadModel('project')->getPairs('noclosed');
        $this->config->infoqz->search['params']['project']['values'] = $projects;

//        $apps = $this->loadModel('application')->getapplicationNameCodePairs();
//        if(!empty($apps))
//        {
//            $appList = array();
//            foreach($apps as $key => $app)
//            {
//                $appList[',' . $key . ','] = $app;
//            }
//            $this->config->infoqz->search['params']['app']['values'] += $appList;
//        }

        $isPaymentList = array();
        foreach($this->lang->application->isPaymentList as $paymentID => $paymentValue)
        {
            if(!$paymentID) continue;
            $isPaymentList[',' . $paymentID . ','] = $paymentValue;
        }
        $this->config->infoqz->search['params']['isPayment']['values'] += $isPaymentList;

        // 例外搜索字段。
        $nodeList = array();
        foreach($this->lang->infoqz->nodeList as $id => $objectValue)
        {
            if(!$id) continue;
            $nodeList[',' . $id . ','] = $objectValue;
        }

        $this->config->infoqz->search['fields']['node']   = $this->lang->infoqz->fixNode;
        $this->config->infoqz->search['fields']['reason'] = $this->lang->infoqz->fixReason;
        $this->config->infoqz->search['fields']['step']   = $this->lang->infoqz->fixStep;
        $this->config->infoqz->search['fields']['desc']   = $this->lang->infoqz->fixDesc;
        $this->config->infoqz->search['fields']['result'] = $this->lang->infoqz->fixResult;
        $this->config->infoqz->search['fields']['operation'] = $this->lang->infoqz->operation;
        $this->config->infoqz->search['fields']['test']      = $this->lang->infoqz->test;
        $this->config->infoqz->search['fields']['checkList'] = $this->lang->infoqz->checkList;

        $this->config->infoqz->search['params']['node']      = array('operator' => 'include', 'control' => 'select', 'values' => array(0 => '') + $nodeList);
        $this->config->infoqz->search['params']['reason']    = array('operator' => 'include', 'control' => 'input', 'values' => '');
        $this->config->infoqz->search['params']['step']      = array('operator' => 'include', 'control' => 'input', 'values' => '');
        $this->config->infoqz->search['params']['desc']      = array('operator' => 'include', 'control' => 'input', 'values' => '');
        $this->config->infoqz->search['params']['result']    = array('operator' => 'include', 'control' => 'input', 'values' => '');
        $this->config->infoqz->search['params']['operation'] = array('operator' => 'include', 'control' => 'input', 'values' => '');
        $this->config->infoqz->search['params']['test']      = array('operator' => 'include', 'control' => 'input', 'values' => '');
        $this->config->infoqz->search['params']['checkList'] = array('operator' => 'include', 'control' => 'input', 'values' => '');

        $queryID = ($browseType == 'bysearch')  ? (int)$param : 0;
        $actionURL = $this->createLink('infoqz', 'fix', "browseType=bySearch&param=myQueryID");
        $this->infoqz->buildSearchForm($queryID, $actionURL);

        /* 设置详情页面返回的url连接。*/
        $this->session->set('infoQzList', $this->app->getURI(true));

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $this->view->title      = $this->lang->infoqz->commonFix;
        $this->view->infos      = $this->infoqz->getList('fix', $browseType, $queryID, $orderBy, $pager);
        $this->view->orderBy    = $orderBy;
        $this->view->pager      = $pager;
        $this->view->param      = $param;
        $this->view->apps       = $this->loadModel('application')->getapplicationNameCodePairs();
        $this->view->browseType = $browseType;
        $this->view->depts      = $this->loadModel('dept')->getOptionMenu();
        $this->view->users      = $this->loadModel('user')->getPairs('noletter|noclosed');
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: gain
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:42
     * Desc: This is the code comment. This method is called gain.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param string $browseType
     * @param int $param
     * @param string $orderBy
     * @param int $recTotal
     * @param int $recPerPage
     * @param int $pageID
     */
    public function gain($browseType = 'all', $param = 0, $orderBy = 'createdDate_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $browseType = strtolower($browseType);
        /* By search. */
        $depts = $this->loadModel('dept')->getOptionMenu();
        $depts[0] = '';
        $this->config->infoqz->search['params']['createdDept']['values'] = $depts;

        $projects = $this->loadModel('project')->getPairs('noclosed');
        $this->config->infoqz->search['params']['project']['values'] += $projects;

//        $apps = $this->loadModel('application')->getapplicationNameCodePairs();
//        if(!empty($apps))
//        {
//            $appList = array();
//            foreach($apps as $key => $app)
//            {
//                $apps = explode('_',$app);
//                $appList[$apps[0]] = $app;
//            }
//            $this->config->infoqz->search['params']['app']['values'] += $appList;
//        }

        $apps = $this->loadModel('application')->getapplicationNameCodePairsWithPartition();
        $this->config->infoqz->search['params']['app']['values'] += $apps;
        $this->config->infoqz->search['params']['dataSystem']['values'] += $apps;

        $isPaymentList = array();
        foreach($this->lang->application->isPaymentList as $paymentID => $paymentValue)
        {
            if(!$paymentID) continue;
            $isPaymentList[',' . $paymentID . ','] = $paymentValue;
        }
        $this->config->infoqz->search['params']['isPayment']['values'] += $isPaymentList;

        unset($this->lang->infoqz->statusList['systemsuccess']);   //去掉产品经理审批节点
        $this->config->infoqz->search['params']['status']['values'] = $this->lang->infoqz->statusList;

        // 例外搜索字段。
        $nodeList = array();
        foreach($this->lang->infoqz->nodeList as $id => $objectValue)
        {
            if(!$id) continue;
            $nodeList[',' . $id . ','] = $objectValue;
        }

        $queryID = ($browseType == 'bysearch')  ? (int)$param : 0;
        $actionURL = $this->createLink('infoqz', 'gain', "browseType=bySearch&param=myQueryID");
        $this->infoqz->buildSearchForm($queryID, $actionURL);

        /* 设置详情页面返回的url连接。*/
        $this->session->set('infoQzList', $this->app->getURI(true));

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $this->view->title      = $this->lang->infoqz->commonGain;
        $this->view->infos      = $this->infoqz->getList('gain', $browseType, $queryID, $orderBy, $pager);
        $this->view->orderBy    = $orderBy;
        $this->view->pager      = $pager;
        $this->view->param      = $param;
        $this->view->apps       = $this->loadModel('application')->getapplicationNameCodePairs();
        $this->view->browseType = $browseType;
        $this->view->depts      = $this->loadModel('dept')->getOptionMenu();
        $this->view->users      = $this->loadModel('user')->getPairs('noletter|noclosed');
        $this->session->set('common_back_url', $this->app->getURI(true),'backlog');

        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: create
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:42
     * Desc: This is the code comment. This method is called create.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $action
     */
    public function create($action)
    {
        if($_POST)
        {
            $infoID = $this->infoqz->create($action);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $actionID = $this->loadModel('action')->create('infoqz', $infoID, 'created', $this->post->comment);
            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            if ($_POST['issubmit'] == 'save'){
                $response['message'] = $this->lang->saveSuccess;
            }else{
                $this->dao->update(TABLE_ACTION)->set('action')->eq('createdandsubmitexamine')->where('id')->eq($actionID)->exec();
            }
            $response['locate']  = inlink($action);

            $this->send($response);
        }

        $this->app->rawMethod  = $action;
        $this->view->action    = $action;
        $this->view->title     = $action == 'gain' ? $this->lang->infoqz->gainApply : $this->lang->infoqz->fixApply;
        $this->view->apps      = $this->loadModel('application')->getapplicationNameCodePairsWithPartition();
        $this->view->users     = $this->loadModel('user')->getPairs('noclosed');
        $this->view->problems  = array('' => '') + $this->loadModel('problem')->getPairsAbstract('noclosed');
        $this->view->demands   = array('' => '') + $this->loadModel('demand')->getPairsTitle('noclosed');
        $this->view->projects  =  array('' => '') + $this->loadModel('projectplan')->getAliveProjects(false);
        $this->view->secondorders     =  $this->loadModel('secondorder')->getNamePairs();
        //审核节点下的审核人列表
        $reviewers            = $this->infoqz->getReviewers();
        //审核节点下的审核人列表
        $reviewerAccounts     = $this->loadModel('review')->getReviewerAccounts($reviewers);
        //审核节点下默认设置审核节点人
        $defChosenReviewNodes = $this->config->infoqz->create->setDefChosenReviewNodes;
        $this->view->reviewers            = $reviewers;
        $this->view->reviewerAccounts     = $reviewerAccounts;
        $this->view->defChosenReviewNodes = $defChosenReviewNodes;
        $this->display();
    }

    /**
     * Edit a info.
     * 
     * @param  int $infoID 
     * @access public
     * @return void
     */
    public function edit($infoID = 0)
    {
        if($_POST)
        {
            $changes = $this->infoqz->update($infoID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes || $this->post->comment)
            {
                $actionID = $this->loadModel('action')->create('infoqz', $infoID, 'edited', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            if ($_POST['issubmit'] == 'save'){
                $response['message'] = $this->lang->saveSuccess;
            }else{
                if ((int)$actionID > 0){
                    $this->dao->update(TABLE_ACTION)->set('action')->eq('submitexamine')->where('id')->eq($actionID)->exec();
                }
            }
            $response['locate']  = inlink('view', "infoID=$infoID");

            $this->send($response);
        }

        $info = $this->infoqz->getByID($infoID);
        if($info->isNPC == '2')
        {
            $nodeList = $this->lang->infoqz->gainNodeCNCCList;
        }else if ($info->isNPC == '1') {
            $nodeList = $this->lang->infoqz->gainNodeNPCList;
        }else {
            $nodeList = $this->lang->infoqz->gainNodeCNCCList + $this->lang->infoqz->gainNodeNPCList;
        }
        if($info->type == 'tech')
        {
            $classifyList = $this->lang->infoqz->techList;
        }else if ($info->type == 'business') {
            $classifyList = $this->lang->infoqz->businessList;
        }else {
            $classifyList = $this->lang->infoqz->techList + $this->lang->infoqz->businessList;
        }
        $this->app->rawMethod = $info->action;

        $this->view->title     = $this->lang->infoqz->edit;
        $this->view->users     = $this->loadModel('user')->getPairs('noclosed');
        $this->view->apps      = $this->loadModel('application')->getapplicationNameCodePairsWithPartition();
        $this->view->info      = $info;
        $this->view->nodeList  = $nodeList;
        $this->view->classifyList  = $classifyList;
        $this->view->problems  = array('' => '') + $this->loadModel('problem')->getPairsAbstract('noclosed','',array_filter(explode(',',$info->problem)));
        $this->view->demands   = array('' => '') + $this->loadModel('demand')->getPairsTitle('noclosed');
        $this->view->projects  =  array('' => '') + $this->loadModel('projectplan')->getAliveProjects($info->fixType == 'second');
        $this->view->secondorders     =  $this->loadModel('secondorder')->getNamePairs();
        $this->view->action    = $this->view->info->action;
        $deptId = $info->createdDept;
        $this->view->reviewers = $this->infoqz->getReviewers($deptId);
        //审核节点以及审核节点的审核人
        $nodesReviewers = $this->loadModel('review')->getAllNodeReviewers('infoQz', $infoID, $info->version);
        $this->view->nodesReviewers = $nodesReviewers;
        $this->view->reviewerAccounts = $this->loadModel('review')->getReviewerAccounts($this->view->reviewers);
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: view
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:43
     * Desc: This is the code comment. This method is called view.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $infoID
     */
    public function view($infoID = 0)
    {
        $this->app->loadLang('release');
        $this->app->loadLang('api');
        $info = $this->infoqz->getByID($infoID);
        $dataManagementID = $this->dao->select('id')->from(TABLE_DATAUSE)->where('code')->eq($info->dataManagementCode)->fetch('id');
        $info->dataManagementID = $dataManagementID;
        $this->app->rawMethod = $info->action;

        if($info->isNPC == '2')
        {
            $nodeList = $this->lang->infoqz->gainNodeCNCCList;
        }else if ($info->isNPC == '1') {
            $nodeList = $this->lang->infoqz->gainNodeNPCList;
        }else {
            $nodeList = $this->lang->infoqz->gainNodeCNCCList + $this->lang->infoqz->gainNodeNPCList;
        }
        if($info->type == 'tech')
        {
            $classifyList = $this->lang->infoqz->techList;
        }else if ($info->type == 'business') {
            $classifyList = $this->lang->infoqz->businessList;
        }else {
            $classifyList = $this->lang->infoqz->techList + $this->lang->infoqz->businessList;
        }

        $this->view->title    = $this->lang->infoqz->view;
        $this->view->users    = $this->loadModel('user')->getPairs('noclosed');
        $this->view->actions  = $this->loadModel('action')->getList('infoqz', $infoID);
        $this->view->problems = array('' => '') + $this->loadModel('problem')->getPairs();
        $this->view->demands  = array('' => '') + $this->loadModel('demand')->getPairs();
        $this->view->projects  =  array('' => '') + $this->loadModel('projectplan')->getAliveProjects($info->fixType == 'second');//更新获取所属项目的方法
        $this->view->info     = $info;
        $this->view->action   = $this->view->info->action;
        $this->view->apps     = $this->loadModel('application')->getapplicationNameCodePairsWithPartition();
        $this->view->nodes    = $this->loadModel('review')->getNodes('infoQz', $infoID, $info->version);
        $this->view->releases             = array('' => '') + $this->loadModel('project')->getReleases($info->project);
//        $this->view->releaseInfoList        = $this->loadModel('outwarddelivery')->getReleaseInfoInIds(array_keys($this->view->releases));
        $this->view->releaseInfoList        = $this->loadModel('outwarddelivery')->getReleaseInfoInIds($info->release);
        $this->view->releasePushLogs             = $this->loadModel('release')->getPushLog($info->release);
        $this->view->demandUnitDeptList          = $this->infoqz->getDemandUnitDeptList();

        if($info->action === 'gain') {
            $this->view->objects = $this->loadModel('secondline')->getByID($infoID, 'gainQz');
        } else if($info->action === 'fix') {
            $this->view->objects = $this->loadModel('secondline')->getByID($infoID, 'fixQz');
        }
        $as = [];
        foreach(explode(',', $info->app) as $app)
        {
            if(!$app) continue;
            $as[] = zget($apps, $app , "");
        }
        $app = implode(',', $as);
        $this->view->depts      = $this->loadModel('dept')->getOptionMenu();
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: review
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:43
     * Desc: This is the code comment. This method is called review.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $infoID
     * @param int version
     * @param int $reviewStage
     */
    public function review($infoID = 0, $version = 1, $reviewStage  = 0)
    {
        if($_POST)
        {
            $info = $this->infoqz->getByID($infoID);
            $this->infoqz->review($infoID);
            if(dao::isError()) {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $action = 'cmconfirmed' == $info->status || 'leadersuccess' == $info->status ? 'deal' : 'review';
            $actionID = $this->loadModel('action')->create('infoqz', $infoID, 'review', $this->post->comment);

            //检查是否需要退送到清算中心接口
            $newInfo = $this->infoqz->getSearchInfoByID($infoID,  'status, deliveryType, pushExternalStatus');
            $res = $this->infoqz->checkAllowPush($newInfo);
            if($res){
                $res = $this->infoqz->pushInfo($infoID);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }
        //数据信息
        $info = $this->infoqz->getByID($infoID);
        //检查是否允许审核
        $res = $this->infoqz->checkAllowReview($info, $version, $reviewStage, $this->app->user->account);

        $this->view->depts = $this->loadModel('dept')->getOptionMenu();
        $this->view->title = $this->lang->infoqz->edit;
        $this->view->users = $this->loadModel('user')->getPairs('noclosed');
        $this->view->info  = $info;
        //是否允许审核
        $this->view->res   = $res;
        $this->view->isNeedSystem = in_array('3',explode(',',$info->requiredReviewNode))?'yes':'no';
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: feedback
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:43
     * Desc: This is the code comment. This method is called feedback.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $infoID
     */
    public function feedback($infoID)
    {
        if($_POST)
        {
            $changes = $this->infoqz->feedback($infoID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes || $this->post->comment)
            {
                $actionID = $this->loadModel('action')->create('infoqz', $infoID, 'feedbacked', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $this->view->depts   = $this->loadModel('dept')->getOptionMenu();
        $this->view->title   = $this->lang->infoqz->edit;
        $this->view->users   = $this->loadModel('user')->getPairs('noclosed');
        $this->view->info   = $this->infoqz->getByID($infoID);
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: close
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:43
     * Desc: This is the code comment. This method is called close.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $infoID
     */
    public function close($infoID = 0)
    {
        if($_POST)
        {
            $this->infoqz->close($infoID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            //更新需求和问题解决时间
            $info = $this->infoqz->getByID($infoID);
            /** @var problemModel $problemModel */
            $problemModel = $this->loadModel('problem');
//            if(!empty($info->demand)){
//                $demandIds =array_filter(explode(',',$info->demand));
//                if($demandIds){
//                    foreach($demandIds as $demandId)
//                    {
//                        $problemModel->getAllSecondSolveTime($demandId,'demand');
//                    }
//                }
//            }
            /*if(!empty($info->problem)){
                $problemIds =array_filter(explode(',',$info->problem));
                if($problemIds){
                    foreach($problemIds as $problemId)
                    {
                        $problemModel->getAllSecondSolveTime($problemId,'problem');
                    }
                }
            }*/

            $this->loadModel('action')->create('infoqz', $infoID, 'closed', $this->post->comment, $this->post->result);

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $this->view->depts = $this->loadModel('dept')->getOptionMenu();
        $this->view->users = $this->loadModel('user')->getPairs('noclosed');
        $this->view->info  = $this->infoqz->getByID($infoID);
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: run
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:43
     * Desc: This is the code comment. This method is called run.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $infoID
     */
    public function run($infoID = 0)
    {
        if($_POST)
        {
            $this->infoqz->run($infoID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $this->loadModel('action')->create('infoqz', $infoID, 'run');

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $this->view->title   = $this->lang->infoqz->run;
        $this->view->users   = $this->loadModel('user')->getPairs('noclosed');
        $this->view->info    = $this->infoqz->getByID($infoID);
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: link
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:43
     * Desc: This is the code comment. This method is called link.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $infoID
     * @param int $version
     * @param int $reviewStage
     */
    public function link($infoID = 0, $version = 1, $reviewStage = 0)
    {
        if($_POST)
        {
            $this->infoqz->link($infoID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $this->loadModel('action')->create('infoqz', $infoID, 'linkrelease', $this->post->comment);

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $info = $this->infoqz->getByID($infoID);
        //检查是否允许关联
        $res = $this->infoqz->checkAllowReview($info, $version, $reviewStage, $this->app->user->account);
        $this->view->depts    = $this->loadModel('dept')->getOptionMenu();
        $this->view->title    = $this->lang->infoqz->edit;
        $this->view->users    = $this->loadModel('user')->getPairs('noclosed');
        $this->view->info     = $info;
        $this->view->res     = $res; //是否允许关联

        $this->view->releases = array('' => '') + $this->loadModel('project')->getReleases($this->view->info->project);

        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: export
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:43
     * Desc: This is the code comment. This method is called export.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $action
     * @param string $orderBy
     * @param string $browseType
     */
    public function export($action, $orderBy = 'id_desc', $browseType = 'all')
    {
        
        // $this->infoqz->pushDestroyedData();
        // die('目前导出不可用，正在调试销毁');
        /* format the fields of every info in order to export data. */
        if($_POST)
        {
            $this->loadModel('file');
            $infoLang   = $this->lang->infoqz;
            $infoConfig = $this->config->infoqz;

            /* Create field lists. */
            $fields = $this->post->exportFields ? $this->post->exportFields : explode(',', $infoConfig->list->exportFields);
            foreach($fields as $key => $fieldName)
            {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = isset($infoLang->$fieldName) ? $infoLang->$fieldName : $fieldName;
                unset($fields[$key]);
            }

            /* Get infos. */
            $infos = array();
            if($this->session->infoqzOnlyCondition)
            {
                $infos = $this->dao->select('*')->from(TABLE_INFO_QZ)->where($this->session->infoqzQueryCondition)
                    ->beginIF($this->post->exportType == 'selected')->andWhere('id')->in($this->cookie->checkedItem)->fi()
                    ->orderBy($orderBy)->fetchAll('id');
            }
            else
            {
                $stmt = $this->dbh->query($this->session->infoqzQueryCondition . ($this->post->exportType == 'selected' ? " AND id IN({$this->cookie->checkedItem})" : '') . " ORDER BY " . strtr($orderBy, '_', ' '));
                while($row = $stmt->fetch()) $infos[$row->id] = $row;
            }
            $infoIdList = array_keys($infos);

            /* Get users, products and executions. */
            $users = $this->loadModel('user')->getPairs('noletter');
            $apps  = $this->loadModel('application')->getapplicationNameCodePairsWithPartition();
            $depts = $this->loadModel('dept')->getTopPairs();
            $dmap  = $this->dao->select('account,dept')->from(TABLE_USER)->fetchAll('account');
           // $projects = $this->loadModel('project')->getPairs('noclosed');
            $projects  =  array('0' => '') + $this->loadModel('projectplan')->getAllProjects(false);//更新表
            $this->loadModel('secondline');
            $secondorderList     =  $this->loadModel('secondorder')->getNamePairs();

            // 获取待处理人数据集。
            $reviewerList = $this->loadModel('review')->getObjectIdListReviewer($infoIdList, 'infoQz');

            $demandUnitDeptList = $this->infoqz->getDemandUnitDeptList();
            foreach($infos as $info)
            {
                $initStatus = $info->status;
                $initExternalStatus = $info->externalStatus;
                // 获取关联发布。
                if($info->release) $info->release = $this->dao->select('name')->from(TABLE_RELEASE)->where('id')->eq($info->release)->fetch('name');
                $info->status   = $infoLang->statusList[$info->status];
                $info->isNPC    = $infoLang->isNPCList[$info->isNPC];
                $info->type     = $infoLang->typeList[$info->type];
                $info->systemType     = $infoLang->systemTypeList[$info->systemType];
                $info->isTest     = $infoLang->isTestList[$info->isTest];
                $info->externalStatus  = zget($infoLang->externalStatusList, $info->externalStatus,'');

                //迭代二十六-删除部门第一个'/'
                $info->createdDept = trim(zget($depts, $dmap[$info->createdBy]->dept, ''),'/');
                $info->createdBy   = $users[$info->createdBy];

                $info->createdDate  = substr($info->createdDate,0, 10);//创建时间
                $info->editedDate   = substr($info->editedDate,0, 10);//编辑时间
                $info->isJinke = $infoLang->isJinkeList[$info->isJinke];
                $info->desensitizationType = $infoLang->desensitizationTypeList[$info->desensitizationType];
                $info->deadline = $info->isDeadline == '1'? '长期':substr($info->deadline,0,10);

                $info->appName = '';
                $info->appTeam = '';
                if($info->app)
                {
                    $as = [];
                    foreach(explode(',', $info->app) as $app)
                    {
                        if(!$app) continue;
                        $as[] = $app;
                    }

                    $apps1 = $this->dao->select('name,team')->from(TABLE_APPLICATION)->where('id')->in($as)->fetchAll();
                    $this->app->loadLang('application');
                    foreach($apps1 as $app)
                    {
                        $info->appName .= $app->name . ' ';
                        $info->appTeam .= $this->lang->application->teamList[$app->team] . ' ';
                    }
                }
                $info->team   = $info->appTeam;

                // 处理实现方式。
                $info->fixType = $infoLang->fixTypeList[$info->fixType];

                $info->reviewers = '';
                if(isset($reviewerList[$info->id])) {
                    $reviewers = [];
                    foreach($reviewerList[$info->id] as $reviewer) {
                        if(!$reviewer) continue;
                        $reviewers[] = $reviewer;
                    }
                    $info->reviewers = implode(',', $reviewers);
                }

                //待处理人
                $tempInfo = new stdClass();
                $tempInfo->status   = $initStatus;
                $tempInfo->reviewers = $info->reviewers;
                $tempInfo->createdBy = $info->createdBy;

                $dealUsers = $this->loadModel('infoqz')->getInfoDealUsers($tempInfo);
                $info->dealUser = '';

                if(!empty($dealUsers)){
                    $as = array();
                    $dealUsersArray = explode(',', $dealUsers);
                    foreach($dealUsersArray as $currentUser)
                    {
                        $as[] = zget($users, $currentUser);
                    }
                    $info->dealUser = implode(',', $as);
                }

                // 处理执行节点。
                if($info->node)
                {
                    $cs = array();
                    foreach(explode(',', $info->node) as $c)
                    {
                        if($c and isset($infoLang->nodeList[$c])) $cs[] = $infoLang->nodeList[$c];
                    }
                    $info->fixNode = implode(',', $cs);
                }

                // 处理所属项目。
                if($info->project)
                {
                    $as = array();
                    foreach(explode(',', $info->project) as $project)
                    {
                        if(!$project) continue;
                        $as[] = zget($projects, $project);
                    }
                    $info->project = implode(',', $as);
                }


                // 处理数据类别。
                if($info->classify)
                {
                    $classifyList = $this->lang->infoqz->techList + $this->lang->infoqz->businessList;
                    $cs = array();
                    foreach(explode(',', $info->classify) as $c)
                    {
                        if($c and isset($classifyList[$c])) $cs[] = $classifyList[$c];
                    }
                    $info->classify = implode(',', $cs);
                }
                if($action == 'gain')
                {
                    if($info->node)
                    {
                        $as = array();
                        foreach(explode(',', $info->node) as $nodeID)
                        {
                            if(!$nodeID) continue;
                            $as[] = zget($this->lang->infoqz->gainNodeNPCList + $this->lang->infoqz->gainNodeCNCCList, $nodeID, $nodeID);
                        }
                        $info->gainNode = implode(',', $as);
                    }
                    $info->gainReason  = $info->reason;
                    $info->gainPurpose = $info->purpose;
                    $info->gainDesc    = $info->desc;
                    $info->gainResult  = zget(array_flip($this->lang->infoqz->externalStatusMapArray),$initExternalStatus,'');
                    $info->content =strip_tags(str_replace("&nbsp;"," ",htmlspecialchars_decode( $info->content)));
                    $info->desensitization =strip_tags(str_replace("&nbsp;"," ",htmlspecialchars_decode( $info->desensitization)));
                    $info->operation =strip_tags(str_replace("&nbsp;"," ",htmlspecialchars_decode( $info->operation)));
                    $info->step =strip_tags(str_replace("&nbsp;"," ",htmlspecialchars_decode( $info->step)));
                    $info->gainStep    = $info->step;

                    // 获取方式处理。
                    $info->gainType = $infoLang->gainTypeList[$info->gainType];
                }
                else
                {
                    $info->fixNode   = $infoLang->nodeList[$info->node];
                    $info->fixReason = $info->reason;
                    $info->fixStep   = $info->step;
                    $info->fixDesc   = $info->desc;
                    $info->fixResult = $info->result;
                }

                // 处理所属应用系统。
                if($info->app)
                {
                    $as = array();
                    foreach(explode(',', $info->app) as $app)
                    {
                        if(!$app) continue;
                        $as[] = zget($apps, $app);
                    }
                    $info->app = implode(',', $as);
                }
                if($info->dataSystem)
                {
                    $as = array();
                    foreach(explode(',', $info->dataSystem) as $app)
                    {
                        if(!$app) continue;
                        $as[] = zget($apps, $app);
                    }
                    $info->dataSystem = implode(',', $as);
                }

                // 处理系统分类。
                if($info->isPayment)
                {
                    $as = array();
                    foreach(explode(',', $info->isPayment) as $paymentID)
                    {
                        if(!$paymentID) continue;
                        $as[] = zget($this->lang->application->isPaymentList, $paymentID, $paymentID);
                    }
                    $isPayment = implode(',', $as);
                    $info->isPayment = $isPayment;
                }

                if($info->supply)
                {
                    $as = array();
                    foreach(explode(',', $info->supply) as $supply)
                    {
                        if(!$supply) continue;
                        $as[] = zget($users, $supply);
                    }
                    $info->supply = implode(',', $as);
                }

                $secondorderIds = explode(',', $info->secondorderId);
                $secondorderNameList = array();
                foreach ($secondorderIds as $secondorder){
                    if(!empty($secondorder)){
                        $secondorderNameList[] = zget($secondorderList, $secondorder,'');
                    }
                }
                $info->secondorderId             = implode(',',$secondorderNameList);

                /* 获取关联的问题单、需求单。*/
                $problems = $this->loadModel('problem')->getPairs('noclosed');
                $info->problem = trim($info->problem, ',');
                $ps = array();
                if($info->problem)
                {
                    $problemList = explode(',', $info->problem);
                    foreach($problemList as $p) $ps[] = $problems[$p];
                    $info->problem =implode(',', $ps);
                }
                $demands = $this->loadModel('demand')->getPairs('noclosed');
                $info->demand = trim($info->demand, ',');
                $ds = array();
                if($info->demand)
                {
                    $demandList = explode(',', $info->demand);
                    foreach($demandList as $d) $ds[] = $demands[$d];
                    $info->demand =implode(',', $ds);
                }
                //退回原因
                $revertReason       = '';
                $revertReasonChild  = '';
                if(!empty($info->revertReason)) {
                    $childTypeList = json_decode($this->lang->infoqz->childTypeList['all'],true);
                    $childType = [];
                    foreach ($childTypeList as $k=>$v){
                        $childType += $v;
                    }

                    foreach (json_decode($info->revertReason,true) as $index => $item) {
                        if (!empty($item)) {
                            $revertReason       .= $item['RevertDate'] .':'. zget($this->lang->infoqz->revertReasonList, $item['RevertReason']).PHP_EOL;
                            if (isset($childType[$item['RevertReasonChild']])){
                                $revertReasonChild  .= $item['RevertDate'] .':'. $childType[$item['RevertReasonChild']].PHP_EOL;
                            }
                        }
                    }
                }
                $info->revertReason                      = $revertReason;
                $info->revertReasonChild                 = $revertReasonChild;
                if (in_array($info->dataCollectApplyCompany,[1,2,3])){
                    $arr = [];
                    foreach (explode(',',$info->demandUnitOrDep) as $item) {
                        $arr[] = zget($demandUnitDeptList,$item,'');
                    }
                    $info->demandUnitOrDep = implode(',',$arr);
                }
                $info->dataCollectApplyCompany           = zget($this->lang->infoqz->demandUnitTypeList,$info->dataCollectApplyCompany,'');
            }
            $this->post->set('fields', $fields);
            $this->post->set('rows', $infos);
            $this->post->set('kind', $action);
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        $this->view->fileName        = $action == 'gain' ? $this->lang->infoqz->exportgain : $this->lang->infoqz->exportfix; //20220224 修改获取正确文件名称
        $this->view->allExportFields = $action == 'gain' ? $this->config->infoqz->list->exportGainFields : $this->config->infoqz->list->exportFixFields;
        $this->view->customExport    = true;
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: delete
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:43
     * Desc: This is the code comment. This method is called delete.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $infoID
     */
    public function delete($infoID)
    {
        if(!empty($_POST))
        {
            $this->dao->update(TABLE_INFO_QZ)->set('status')->eq('deleted')->where('id')->eq($infoID)->exec();
            $this->loadModel('datamanagement')->syncDataStatus($infoID,'infoqz');
            $actionID = $this->loadModel('action')->create('infoqz', $infoID, 'deleted', $this->post->comment);
            //2022.4.21 tangfei 删除与问题需求的关联关系
            $sql = "delete from zt_secondline where (objectType='demand'or objectType='problem') and relationID=$infoID  and (relationType='gain' or relationType='fix' or relationType='gainQz');";
            $this->dao->query($sql);

            //更新需求和问题解决时间
            $info = $this->infoqz->getByID($infoID);
            /** @var problemModel $problemModel */
            $problemModel = $this->loadModel('problem');
//            if(!empty($info->demand)){
//                $demandIds =array_filter(explode(',',$info->demand));
//                if($demandIds){
//                    foreach($demandIds as $demandId)
//                    {
//                      $problemModel->getAllSecondSolveTime($demandId,'demand');
//                    }
//                }
//            }
            /*if(!empty($info->problem)){
                $problemIds =array_filter(explode(',',$info->problem));
                if($problemIds){
                    foreach($problemIds as $problemId)
                    {
                       $problemModel->getAllSecondSolveTime($problemId,'problem');
                    }
                }
            }*/

            $backUrl =  $this->session->infoQzList ? $this->session->infoQzList : inLink('gain');
            if(isonlybody()) die(js::closeModal('parent.parent', $backUrl));
            die(js::reload('parent'));
        }

        $info = $this->infoqz->getByID($infoID);
        $this->view->actions = $this->loadModel('action')->getList('infoqz', $infoID);
        $this->view->info = $info;
        $this->view->users   = $this->loadModel('user')->getPairs();
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: exportWord
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:43
     * Desc: This is the code comment. This method is called exportWord.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $infoID
     */
    public function exportWord($infoID)
    {
        $info  = $this->infoqz->getById($infoID);
        $users = $this->loadModel('user')->getPairs();
        $demandUnitDeptList = $this->infoqz->getDemandUnitDeptList();

        $this->app->loadClass('phpword', true);
        $phpWord = new PhpOffice\PhpWord\PHPWord();
        $section = $phpWord->addSection();

        $phpWord->addParagraphStyle('pStyle', array('spacing'=>100));
        $phpWord->addTitleStyle(1, array('size' => 15, 'name' => '黑体'), array('space' => array('before' => 100, 'after' => 200), 'align' => 'center'));
        $phpWord->addTitleStyle(2, array('size' => 10, 'name' => '黑体'), array('space' => array('before' => 100, 'after' => 100)));
        $phpWord->addTitleStyle(3, array('size' => 10, 'name' => '黑体'), array('space' => array('before' => 100, 'after' => 100)));

        $phpWord->addParagraphStyle('align_right', array('lineHeight' => "1.2", 'spaceBefore' => 0, 'spaceAfter' => 0, 'align' => 'right'));
        $phpWord->addFontStyle('font_default', array('name'=>'Arial', 'size'=>11, 'color'=>'37363a'));
        $phpWord->addFontStyle('font_bold', array('name'=>'Arial', 'size'=>11, 'color'=>'000000', 'bold'=> true));

        $section->addTitle(($info->action == 'fix' ? $this->lang->infoqz->exportFixTitle : $this->lang->infoqz->exportGainTitle), 1);
        $section->addText($this->lang->infoqz->code . ' ' . $info->code, 'font_default', 'align_right');

        $tableStyle = array(
            'unit' => \PhpOffice\PhpWord\Style\Table::WIDTH_PERCENT,
            'width' => 100 * 50,
            'cellMargin' => 50,
            'borderSize' => 10,
            'borderColor' => '000000',
        );
        $cellStyle = array();
        $table = $section->addTable($tableStyle);
        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->infoqz->type);
        $table->addCell(1000, $cellStyle)->addText(zget($this->lang->infoqz->typeList, $info->type, ''));
        $table->addCell(1000, $cellStyle)->addText($this->lang->infoqz->status);
        $table->addCell(1000, $cellStyle)->addText(zget($this->lang->infoqz->statusList, $info->status, ''));

        //需求收集4597
        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->infoqz->dataCollectApplyCompany);
        $table->addCell(1000, $cellStyle)->addText(zget($this->lang->infoqz->demandUnitTypeList,$info->dataCollectApplyCompany,''));

        $table->addCell(1000, $cellStyle)->addText($this->lang->infoqz->demandUnitOrDep);
        if (in_array($info->dataCollectApplyCompany,[1,2,3])){
            $arr = [];
            foreach (explode(',',$info->demandUnitOrDep) as $item) {
                $arr[] = zget($demandUnitDeptList,$item,'');
            }
            $info->demandUnitOrDep = implode(',',$arr);
        }
        $table->addCell(1000, $cellStyle)->addText($info->demandUnitOrDep);

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->infoqz->demandUser);
        $table->addCell(1000, $cellStyle)->addText($info->demandUser);

        $table->addCell(1000, $cellStyle)->addText($this->lang->infoqz->demandUserPhone);
        $table->addCell(1000, $cellStyle)->addText($info->demandUserPhone);

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->infoqz->demandUserEmail);
        $table->addCell(3000, array('gridSpan' => 3))->addText($info->demandUserEmail);

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->infoqz->portUser);
        $table->addCell(1000, $cellStyle)->addText($info->portUser);

        $table->addCell(1000, $cellStyle)->addText($this->lang->infoqz->portUserPhone);
        $table->addCell(1000, $cellStyle)->addText($info->portUserPhone);

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->infoqz->portUserEmail);
        $table->addCell(3000, array('gridSpan' => 3))->addText($info->portUserEmail);

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->infoqz->supportUser);
        $table->addCell(1000, $cellStyle)->addText($info->supportUser);

        $table->addCell(1000, $cellStyle)->addText($this->lang->infoqz->supportUserPhone);
        $table->addCell(1000, $cellStyle)->addText($info->supportUserPhone);

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->infoqz->supportUserEmail);
        $table->addCell(3000, array('gridSpan' => 3))->addText($info->supportUserEmail);

        $projects  = array('' => '') + $this->loadModel('project')->getPairs('noclosed');
        $ps = array();
        foreach(explode(',', $info->project) as $project)
        {
            $ps[] = zget($projects, $project, '');
        }
        $as = array();
        foreach(explode(',', $info->node) as $nodeID)
        {
            if(!$nodeID) continue;
            $as[] = zget($this->lang->infoqz->gainNodeNPCList + $this->lang->infoqz->gainNodeCNCCList, $nodeID, $nodeID);
        }
        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->infoqz->gainNode);
        $table->addCell(1000, $cellStyle)->addText(implode(',', $as));
        $table->addCell(1000, $cellStyle)->addText($this->lang->infoqz->project);
        $table->addCell(1000, $cellStyle)->addText(implode(',', $ps));

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->infoqz->isJinke);
        $table->addCell(1000, $cellStyle)->addText($this->lang->infoqz->isJinkeList[$info->isJinke]);
        $table->addCell(1000, $cellStyle)->addText($this->lang->infoqz->isDesensitize);
        $table->addCell(1000, $cellStyle)->addText($this->lang->infoqz->aclList[$info->isDesensitize]);
        
        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->infoqz->deadline);
        $table->addCell(1000, $cellStyle)->addText($info->isDeadline=='1'?'长期':substr($info->deadline,0,10));
        $table->addCell(1000, $cellStyle)->addText($this->lang->infoqz->desensitizationType);
        $table->addCell(1000, $cellStyle)->addText($this->lang->infoqz->desensitizationTypeList[$info->desensitizationType]);

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->infoqz->desensitizeProcess);
        $table->addCell(3000, array('gridSpan' => 3))->addText($info->desensitizeProcess);

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->infoqz->planBegin);
        $table->addCell(1000, $cellStyle)->addText($info->planBegin);
        $table->addCell(1000, $cellStyle)->addText($this->lang->infoqz->planEnd);
        $table->addCell(1000, $cellStyle)->addText($info->planEnd);

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->infoqz->actualBegin);
        $table->addCell(1000, $cellStyle)->addText($info->actualBegin);
        $table->addCell(1000, $cellStyle)->addText($this->lang->infoqz->actualEnd);
        $table->addCell(1000, $cellStyle)->addText($info->actualEnd);

        $problems = $this->loadModel('problem')->getPairs('noclosed');
        $info->problem = trim($info->problem, ',');
        $ps = array();
        if($info->problem)
        {
            $problemList = explode(',', $info->problem);
            foreach($problemList as $p) $ps[] = $problems[$p];
        }
        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->infoqz->problem);
        $table->addCell(1000, $cellStyle)->addText(implode(',', $ps));

        $demands = $this->loadModel('demand')->getPairs('noclosed');
        $info->demand = trim($info->demand, ',');
        $ds = array();
        if($info->demand)
        {
            $demandList = explode(',', $info->demand);
            foreach($demandList as $d) $ds[] = $demands[$d];
        }

        $table->addCell(1000, $cellStyle)->addText($this->lang->infoqz->demand);
        $table->addCell(1000, $cellStyle)->addText(implode(',', $ds));

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->infoqz->createdBy);
        $table->addCell(1000, $cellStyle)->addText(zget($users, $info->createdBy, ''));
        $table->addCell(1000, $cellStyle)->addText($this->lang->infoqz->createdDate);
        $table->addCell(1000, $cellStyle)->addText($info->createdDate);

        $apps = $this->loadModel('application')->getapplicationNameCodePairs();
        $as = array();
        foreach(explode(',', $info->app) as $app)
        {
            if(!$app) continue;
            $as[] = zget($apps, $app);
        }
        $info->app = implode(',', $as);

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->infoqz->app);
        $table->addCell(1000, $cellStyle)->addText($info->app);

        $table->addCell(1000, $cellStyle)->addText($this->lang->infoqz->dataSystem);
        $table->addCell(1000, $cellStyle)->addText($info->dataSystem);

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($info->action == 'fix' ? $this->lang->infoqz->fixDesc : $this->lang->infoqz->gainDesc);
        $table->addCell(3000, array('gridSpan' => 3))->addText(strip_tags($info->desc));

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($info->action == 'fix' ? $this->lang->infoqz->fixReason : $this->lang->infoqz->gainReason);
        $table->addCell(3000, array('gridSpan' => 3))->addText(strip_tags($info->reason));

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->infoqz->gainPurpose);
        $table->addCell(3000, array('gridSpan' => 3))->addText(strip_tags($info->purpose));

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->infoqz->test);
        $table->addCell(3000, array('gridSpan' => 3))->addText(strip_tags($info->test));

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->infoqz->content);
        $table->addCell(3000, array('gridSpan' => 3))->addText(strip_tags($info->content));

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->infoqz->operation);
        $table->addCell(3000, array('gridSpan' => 3))->addText(strip_tags($info->operation));

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($info->action == 'fix' ? $this->lang->infoqz->fixStep : $this->lang->infoqz->gainStep);
        $table->addCell(3000, array('gridSpan' => 3))->addText(strip_tags($info->step));

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->infoqz->desensitization);
        $table->addCell(3000, array('gridSpan' => 3))->addText(strip_tags($info->desensitization));

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->infoqz->externalRejectReason);
        $table->addCell(3000, array('gridSpan' => 3))->addText(strip_tags($info->externalRejectReason));

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($info->action == 'fix' ? $this->lang->infoqz->fixResult : $this->lang->infoqz->gainResult);
        $table->addCell(3000, array('gridSpan' => 3))->addText(isset(array_flip($this->lang->infoqz->externalStatusMapArray)[$info->externalStatus]) ? array_flip($this->lang->infoqz->externalStatusMapArray)[$info->externalStatus] : '');

        /* Review. */
        $table->addRow();
        $table->addCell(4000, array('gridSpan' => 4))->addText($this->lang->infoqz->reviewComment, 'font_bold', array('align' => 'center'));

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->infoqz->reviewNode);
        $table->addCell(1000, $cellStyle)->addText($this->lang->infoqz->reviewer);
        $table->addCell(1000, $cellStyle)->addText($this->lang->infoqz->reviewResult);
        $table->addCell(1000, $cellStyle)->addText($this->lang->infoqz->reviewComment);

        $nodes = $this->loadModel('review')->getNodes('infoQz', $infoID, $info->version);

//        $nodes = $this->loadModel('review')->getNodes('infoQz', $infoID, $info->version);

        foreach($nodes as $key => $node)
        {
            //if($key == 0) continue;
            if($node->status == 'wait' or $node->status == 'ignore') continue;
            $reviewers = [];
            if(is_array($node->reviewers) && !empty($node->reviewers)){
                $reviewers = array_column($node->reviewers, 'reviewer');
            }
            //所有审核人
            $reviewerUsers    = getArrayValuesByKeys($users, $reviewers);

            $reviewerUsersStr = implode(',', $reviewerUsers);

            $realReviewerInfo = $this->loadModel('review')->getRealReviewerInfo($node->status, $node->reviewers);

            $realReviewerInfo->reviewerUserName = '';
            if($realReviewerInfo->reviewer){
                $realReviewerInfo->reviewerUserName = '（'.zget($users, $realReviewerInfo->reviewer).'）';
            }

            $table->addRow();
            $table->addCell(1000, $cellStyle)->addText(zget($this->lang->infoqz->reviewNodeList, $key));
            $table->addCell(1000, $cellStyle)->addText($reviewerUsersStr);
            $table->addCell(1000, $cellStyle)->addText(zget($this->lang->infoqz->confirmResultList, $realReviewerInfo->status, '') . $realReviewerInfo->reviewerUserName);
            $table->addCell(1000, $cellStyle)->addText($realReviewerInfo->comment);
        }

        /* Consumed. */
        $table->addRow();
        $table->addCell(4000, array('gridSpan' => 4))->addText($this->lang->consumedTitle, 'font_bold', array('align' => 'center'));

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->infoqz->nodeUser);
       // $table->addCell(1000, $cellStyle)->addText($this->lang->infoqz->consumed);
        $table->addCell(1000, $cellStyle)->addText($this->lang->infoqz->before);
        $table->addCell(2000,  array('gridSpan' => 2))->addText($this->lang->infoqz->after);

        foreach($info->consumed as $c)
        {
            $table->addRow();
            $table->addCell(1000, $cellStyle)->addText(zget($users, $c->createdBy, ''));
           // $table->addCell(1000, $cellStyle)->addText($c->consumed . '' . $this->lang->hour);
            $table->addCell(1000, $cellStyle)->addText(zget($this->lang->infoqz->statusList, $c->before, '-'));
            $table->addCell(2000,  array('gridSpan' => 2))->addText(zget($this->lang->infoqz->statusList, $c->after, '-'));
        }

        $this->loadModel('file')->export2Word(($info->action == 'fix' ? $this->lang->infoqz->exportFixTitle : $this->lang->infoqz->exportGainTitle) . $info->code, $phpWord);
    }

    /**
     * copy a info.
     * 
     * @param  int $infoID 
     * @access public
     * @return void
     */
     public function copy($infoID = 0,$action='fix')
     {
        if($_POST)
        {
            $infoID = $this->infoqz->create($action);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $actionID = $this->loadModel('action')->create('infoqz', $infoID, 'created', $this->post->comment);
            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            if ($_POST['issubmit'] == 'save'){
                $response['message'] = $this->lang->saveSuccess;
            }else{
                $this->dao->update(TABLE_ACTION)->set('action')->eq('createdandsubmitexamine')->where('id')->eq($actionID)->exec();
            }
            $response['locate']  = inlink($action);

            $this->send($response);
        }
 
         $info = $this->infoqz->getByID($infoID);
         $this->app->rawMethod = $info->action;

         if($info->isNPC == '2')
         {
             $nodeList = $this->lang->infoqz->gainNodeCNCCList;
         }else if ($info->isNPC == '1') {
             $nodeList = $this->lang->infoqz->gainNodeNPCList;
         }else {
             $nodeList = $this->lang->infoqz->gainNodeCNCCList + $this->lang->infoqz->gainNodeNPCList;
         }
         if($info->type == 'tech')
         {
             $classifyList = $this->lang->infoqz->techList;
         }else if ($info->type == 'business') {
             $classifyList = $this->lang->infoqz->businessList;
         }else {
             $classifyList = $this->lang->infoqz->techList + $this->lang->infoqz->businessList;
         }

 
         $this->view->title     = $this->lang->infoqz->copy;
         $this->view->users     = $this->loadModel('user')->getPairs('noclosed');
         $this->view->apps      = $this->loadModel('application')->getapplicationNameCodePairsWithPartition();
         $this->view->info      = $info;
         $this->view->nodeList  = $nodeList;
         $this->view->classifyList  = $classifyList;
         $this->view->problems  = array('' => '') + $this->loadModel('problem')->getPairsAbstract('noclosed');
         $this->view->demands   = array('' => '') + $this->loadModel('demand')->getPairsTitle('noclosed');
         $this->view->projects  =  array('' => '') + $this->loadModel('projectplan')->getAliveProjects($info->fixType == 'second');
         $this->view->secondorders     =  $this->loadModel('secondorder')->getNamePairs();
         $this->view->action    = $this->view->info->action;
         $deptId = $info->createdDept;
         $this->view->reviewers = $this->infoqz->getReviewers($deptId);
         $nodesReviewers = $this->loadModel('review')->getAllNodeReviewers('infoQz', $infoID, $info->version);
         $this->view->nodesReviewers = $nodesReviewers;
         $this->view->reviewerAccounts = $this->loadModel('review')->getReviewerAccounts($this->view->reviewers);
         $this->display();
     }

    /**
     * Desc:实现方式与所属项目联动
     * Date: 2022/3/21
     * Time: 17:00
     *
     * @param string $fixType
     *
     */
    public function ajaxGetSecondLine($fixType)
    {
        $secondLineType = $fixType == 'second';
        $project = $this->loadModel('projectplan')->getAliveProjects($secondLineType);
        $secondProject = [];
        if ($secondLineType){
            $res = $this->dao->select('t1.project,t2.name,t1.bearDept')->from(TABLE_PROJECTPLAN)->alias('t1')
                ->leftjoin(TABLE_PROJECT)->alias('t2')
                ->on('t1.project=t2.id')
                ->where('t1.deleted')->eq(0)
                ->andwhere('t2.status')->ne('closed')
                ->beginIF($secondLineType)->andwhere('t1.year')->eq('2022')->fi()
                ->beginIF($secondLineType)->andwhere('t1.code')->like('%EX')->fi()
                ->beginIF($secondLineType)->andWhere('t1.secondLine')->eq('1')->fi()
                ->beginIF(!$secondLineType)->andWhere('t1.secondLine')->eq('0')->fi()
                ->andWhere('t1.bearDept')->eq($this->session->user->dept)
                ->fetchPairs();
            $secondProject = array_keys($res);
        }
        echo html::select('project[]', $project, $secondProject,"class='form-control chosen' multiple");
    }

    /**
     * Desc:实现是否NPC与节点名称联动
     * User: liugaoyang
     * Date: 2022/5/19
     * Time: 16:00
     *
     * @param string $isNPC
     *
     */
    public function ajaxGetisNPC($isNPC)
    {
        $result = $this->infoqz->getNodeListByIsNPC($isNPC);
        //echo html::select('project[]', $project, 0,"class='form-control chosen' multiple");
        echo html::select('node[]', $result, '', "class='form-control chosen' multiple");
    }
    /**
     * Desc:实现数据类型和数据类别联动
     * User: liugaoyang
     * Date: 2022/5/19
     * Time: 16:00
     *
     * @param string $isNPC
     *
     */
    public function ajaxGetclassify($type)
    {
        $result = $this->infoqz->getClassifyByType($type);
        echo html::select('classify[]', $result, '', "class='form-control chosen' multiple");
    }

    /**
     * Project: chengfangjinke
     * Method: review
     * User: Tony Stark
     * Year: 2021
     * Date: 2022/05/22
     * Time: 14:43
     * Desc: 驳回操作
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $infoID
     */
    public function reject($infoID = 0)
    {

        if($_POST)
        {
            $this->infoqz->reject($infoID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $actionID = $this->loadModel('action')->create('infoqz', $infoID, 'reject', $this->post->comment);
            $this->loadModel('datamanagement')->syncDataStatus($infoID,'infoqz');
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }
        //数据信息
        $info = $this->infoqz->getByID($infoID);
        //检查是否允许驳回
        $res = $this->infoqz->checkAllowReject($info);

        $this->view->depts = $this->loadModel('dept')->getOptionMenu();
        $this->view->title = $this->lang->infoqz->reject;
        $this->view->users = $this->loadModel('user')->getPairs('noclosed');
        $this->view->info  = $info;
        $nodesReviewers = $this->loadModel('review')->getAllNodeReviewers('infoqz', $infoID, $info->version);
        $this->view->nodesReviewers = $nodesReviewers;
        //是否允许审核
        $this->view->res   = $res;
        $this->display();
    }


    /**
     * 推送数据到Api
     *
     * @param int $infoID
     */
    public function pushInfoToApi($infoID = 0){
        $ids = $this->infoqz->getAllowPushInfoIds($infoID);
        if(!$ids){
            return true;
        }
        foreach ($ids as $infoID){
            $ret = $this->infoqz->pushInfo($infoID);
        }
        return true;
    }
    //查看历史审核记录
    public function showHistoryNodes($id){
        $this->app->loadLang('outwarddelivery');
        $info = $this->infoqz->getByID($id);
        $reviewFailReason = json_decode($info->reviewFailReason,true);
        $res = $this->dao->select('version')->from(TABLE_REVIEWNODE)->where('objectType')->eq('infoqz')->andWhere('objectID')->eq($id)->groupby('version')->fetchall();
        $versions = array_column($res,'version');
        $nodes = [];
        foreach ($versions as $version) {
            $data = $this->loadModel('review')->getNodes('infoqz', $id, $version);
            foreach ($data as $k=>$v){
                if ($v->status == 'wait' || !(is_array($v->reviewers) && !empty($v->reviewers))){
                    unset($data[$k]);
                }
                if ($v->stage == 4 && isset($info->createdDate) && $info->createdDate > "2024-04-02 23:59:59"){
                    unset($data[$k]);
                }
            }
            $nodes[$version]['nodes'] = $data;
        }
        foreach ($nodes as $key=>$node) {
            $nodes[$key]['countNodes'] = count($node['nodes']);
            if (isset($reviewFailReason[$key]['guestjk']) && !empty($reviewFailReason[$key]['guestjk'])){
                $nodes[$key]['countNodes']++;
            }
            if (isset($reviewFailReason[$key]['guestcn']) && !empty($reviewFailReason[$key]['guestcn'])){
                $nodes[$key]['countNodes']++;
            }
        }
        $this->view->nodes = $nodes;
        $this->view->users                   = $this->loadModel('user')->getPairs('noletter');
        $this->view->reviewFailReason        = $reviewFailReason;
        $this->view->info                    = $info;
        $this->display();
    }

    /**
     * 重新推送
     * @param $infoID
     * @return void
     */
    public function push($infoID)
    {
//        $info = $this->dao->select('id, status')->from(TABLE_INFO_QZ)->where('id')->eq($infoID)->fetch();
//        if('qingzongsynfailed' == $info->status){
//            $this->dao->update(TABLE_INFO_QZ)
//                ->set('status')->eq('pass')
//                ->where('id')->eq($infoID)
//                ->exec();
//            $this->loadModel('action')->create('infoqz', $infoID, 'repush', "重新推送");
//            $this->loadModel('consumed')->record('infoQz', $infoID, '0', $this->app->user->account, 'qingzongsynfailed', 'pass', array());
//        }
//
//        die(js::locate($this->createLink('infoqz', 'view', "infoID=$infoID"), 'parent.parent'));
    }

    /**
     * 获取需求部门或单位
     * @param $type
     */
    public function ajaxGetDemandUnitList($type){
        $str = 'demandUnitList'.$type;
        $data = $this->lang->infoqz->{$str};
        echo html::select('demandUnitOrDepSelect[]', $data, '', "class='form-control chosen' multiple");
    }
}

