<?php
class info extends control
{
    public function __construct()
    {
        parent::__construct();
        // 上海分公司审核节点名称修改
        if (in_array($this->app->getMethodName(),['create','copy'])){
            $this->info->resetNodeAndReviewerName();
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
        $this->config->info->search['params']['createdDept']['values'] += $depts;

        $projects = $this->loadModel('project')->getPairs('noclosed');
        $this->config->info->search['params']['project']['values'] = $projects;

        $apps = $this->loadModel('application')->getapplicationNameCodePairs();
        if(!empty($apps))
        {
            $appList = array();
            foreach($apps as $key => $app)
            {
                $appList[',' . $key . ','] = $app;
            }
            $this->config->info->search['params']['app']['values'] += $appList;
        }

        $isPaymentList = array();
        foreach($this->lang->application->isPaymentList as $paymentID => $paymentValue)
        {
            if(!$paymentID) continue;
            $isPaymentList[',' . $paymentID . ','] = $paymentValue;
        }
        $this->config->info->search['params']['isPayment']['values'] += $isPaymentList;

        unset($this->config->info->search['params']['status']['values']['systemsuccess']);

        // 例外搜索字段。
        $nodeList = array();
        foreach($this->lang->info->nodeList as $id => $objectValue)
        {
            if(!$id) continue;
            $nodeList[',' . $id . ','] = $objectValue;
        }

        $this->config->info->search['fields']['node']   = $this->lang->info->fixNode;
        $this->config->info->search['fields']['reason'] = $this->lang->info->fixReason;
        $this->config->info->search['fields']['step']   = $this->lang->info->fixStep;
        $this->config->info->search['fields']['desc']   = $this->lang->info->fixDesc;
        $this->config->info->search['fields']['result'] = $this->lang->info->fixResult;
        $this->config->info->search['fields']['operation'] = $this->lang->info->operation;
        $this->config->info->search['fields']['test']      = $this->lang->info->test;
        $this->config->info->search['fields']['checkList'] = $this->lang->info->checkList;

        $this->config->info->search['params']['node']   = array('operator' => 'include', 'control' => 'select', 'values' => array(0 => '') + $nodeList);
        $this->config->info->search['params']['reason'] = array('operator' => 'include', 'control' => 'input', 'values' => '');
        $this->config->info->search['params']['step']   = array('operator' => 'include', 'control' => 'input', 'values' => '');
        $this->config->info->search['params']['desc']   = array('operator' => 'include', 'control' => 'input', 'values' => '');
        $this->config->info->search['params']['result'] = array('operator' => 'include', 'control' => 'input', 'values' => '');
        $this->config->info->search['params']['operation'] = array('operator' => 'include', 'control' => 'input', 'values' => '');
        $this->config->info->search['params']['test']      = array('operator' => 'include', 'control' => 'input', 'values' => '');
        $this->config->info->search['params']['checkList'] = array('operator' => 'include', 'control' => 'input', 'values' => '');

        $queryID = ($browseType == 'bysearch')  ? (int)$param : 0;
        $actionURL = $this->createLink('info', 'fix', "browseType=bySearch&param=myQueryID");
        $this->info->buildSearchForm($queryID, $actionURL);

        /* 设置详情页面返回的url连接。*/
        $this->session->set('infoList', $this->app->getURI(true));

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $this->view->title      = $this->lang->info->commonFix;
        $this->view->infos      = $this->info->getList('fix', $browseType, $queryID, $orderBy, $pager);
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
        $this->config->info->search['params']['createdDept']['values'] = $depts;

        $projects = $this->loadModel('project')->getPairs('noclosed');
        $this->config->info->search['params']['project']['values'] += $projects;

        $apps = $this->loadModel('application')->getapplicationNameCodePairs();
        if(!empty($apps))
        {
            $appList = array();
            foreach($apps as $key => $app)
            {
                $appList[',' . $key . ','] = $app;
            }
            $this->config->info->search['params']['app']['values'] += $appList;
        }

        $isPaymentList = array();
        foreach($this->lang->application->isPaymentList as $paymentID => $paymentValue)
        {
            if(!$paymentID) continue;
            $isPaymentList[',' . $paymentID . ','] = $paymentValue;
        }
        $this->config->info->search['params']['isPayment']['values'] += $isPaymentList;

        unset($this->config->info->search['params']['status']['values']['systemsuccess']);

        // 例外搜索字段。
        $nodeList = array();
        foreach($this->lang->info->nodeList as $id => $objectValue)
        {
            if(!$id) continue;
            $nodeList[',' . $id . ','] = $objectValue;
        }

        // $this->config->info->search['fields']['node']   = $this->lang->info->gainNode;
        $this->config->info->search['fields']['reason'] = $this->lang->info->gainReason;
        $this->config->info->search['fields']['purpose']= $this->lang->info->gainPurpose;
        $this->config->info->search['fields']['step']   = $this->lang->info->gainStep;
        $this->config->info->search['fields']['desc']   = $this->lang->info->gainDesc;
        $this->config->info->search['fields']['result'] = $this->lang->info->gainResult;
        $this->config->info->search['fields']['test']      = $this->lang->info->test;
        $this->config->info->search['fields']['checkList'] = $this->lang->info->checkList;
        $this->config->info->search['fields']['gainType']  = $this->lang->info->gainType;

        $this->config->info->search['params']['node']   = array('operator' => 'include', 'control' => 'select', 'values' => array(0 => '') + $nodeList);
        $this->config->info->search['params']['purpose']= array('operator' => 'include', 'control' => 'input', 'values' => '');
        $this->config->info->search['params']['reason'] = array('operator' => 'include', 'control' => 'input', 'values' => '');
        $this->config->info->search['params']['step']   = array('operator' => 'include', 'control' => 'input', 'values' => '');
        $this->config->info->search['params']['desc']   = array('operator' => 'include', 'control' => 'input', 'values' => '');
        $this->config->info->search['params']['result'] = array('operator' => 'include', 'control' => 'input', 'values' => '');
        $this->config->info->search['params']['test']      = array('operator' => 'include', 'control' => 'input', 'values' => '');
        $this->config->info->search['params']['checkList'] = array('operator' => 'include', 'control' => 'input', 'values' => '');
        $this->config->info->search['params']['gainType']  = array('operator' => 'include', 'control' => 'select', 'values' => $this->lang->info->gainTypeList);

        $queryID = ($browseType == 'bysearch')  ? (int)$param : 0;
        $actionURL = $this->createLink('info', 'gain', "browseType=bySearch&param=myQueryID");
        $this->info->buildSearchForm($queryID, $actionURL);

        /* 设置详情页面返回的url连接。*/
        $this->session->set('infoList', $this->app->getURI(true));
        $this->session->set('infoGainList', $this->app->getURI(true));


        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $this->view->title      = $this->lang->info->commonGain;
        $this->view->infos      = $this->info->getList('gain', $browseType, $queryID, $orderBy, $pager);
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
            $infoID = $this->info->create($action);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $actionID = $this->loadModel('action')->create('info', $infoID, 'created', $this->post->comment);
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
        $this->view->title     = $action == 'gain' ? $this->lang->info->gainApply : $this->lang->info->fixApply;
        $this->view->apps      = $this->loadModel('application')->getapplicationNameCodePairs();
        $this->view->users     = $this->loadModel('user')->getPairs('noclosed');
        $this->view->problems  = array('' => '') + $this->loadModel('problem')->getPairsAbstract('noclosed');
        $this->view->demands   = array('' => '') + $this->loadModel('demand')->getPairsTitle('noclosed');
//        $this->view->projects  = array('' => '') + $this->loadModel('project')->getPairs('noclosed');
        $this->view->projects  =  array('' => '') + $this->loadModel('projectplan')->getAliveProjects(false);//关联二线工单
        $this->view->secondorderList   = array('' => '') + $this->dao->select('id,concat(concat(concat(code,"（"),summary),"）")')->from(TABLE_SECONDORDER)->where('deleted')->ne('1')->fetchPairs();
        

        //审核节点下的审核人列表
        $reviewers            = $this->info->getReviewers();
        //审核节点下的审核人列表
        $reviewerAccounts     = $this->loadModel('review')->getReviewerAccounts($reviewers);
        //审核节点下默认设置审核节点人
        $defChosenReviewNodes = $this->config->info->create->setDefChosenReviewNodes;
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
            $changes = $this->info->update($infoID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes || $this->post->comment)
            {
                $actionID = $this->loadModel('action')->create('info', $infoID, 'edited', $this->post->comment);
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

        $info = $this->info->getByID($infoID);
        $this->app->rawMethod = $info->action;

        $this->view->title     = $this->lang->info->edit;
        $this->view->users     = $this->loadModel('user')->getPairs('noclosed');
        $this->view->apps      = $this->loadModel('application')->getapplicationNameCodePairs();
        $this->view->info      = $info;
        $this->view->problems  = array('' => '') + $this->loadModel('problem')->getPairsAbstract('noclosed','',explode(',',$info->problem));
        $this->view->demands   = array('' => '') + $this->loadModel('demand')->getPairsTitle('noclosed');
//        $this->view->projects  = array('' => '') + $this->loadModel('project')->getPairs('noclosed');
        $this->view->projects  =  array('' => '') + $this->loadModel('projectplan')->getAliveProjects($info->fixType == 'second');//关联二线工单
        $this->view->secondorderList   = array('' => '') + $this->dao->select('id,concat(concat(concat(code,"（"),summary),"）")')->from(TABLE_SECONDORDER)->where('deleted')->ne('1')->fetchPairs();
        $this->view->action    = $this->view->info->action;
        $deptId = $info->createdDept;
        $this->view->reviewers = $this->info->getReviewers($deptId);
        //审核节点以及审核节点的审核人
        $nodesReviewers = $this->loadModel('review')->getAllNodeReviewers('info', $infoID, $info->version);
        $this->view->reviewerAccounts = $this->loadModel('review')->getReviewerAccounts($this->view->reviewers);
        $this->view->nodesReviewers = $nodesReviewers;
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
        $info = $this->info->getByID($infoID);
        $dataManagementID = $this->dao->select('id')->from(TABLE_DATAUSE)->where('code')->eq($info->dataManagementCode)->fetch('id');
        $info->dataManagementID = $dataManagementID;
        $this->app->rawMethod = $info->action;

        $this->view->title    = $this->lang->info->view;
        $this->view->users    = $this->loadModel('user')->getPairs('noclosed');
        $this->view->actions  = $this->loadModel('action')->getList('info', $infoID);
        $this->view->problems = array('' => '') + $this->loadModel('problem')->getPairs('noclosed');
        $this->view->demands  = array('' => '') + $this->loadModel('demand')->getPairs('noclosed');
        //$this->view->projects = array('' => '') + $this->loadModel('project')->getPairs('noclosed');
        $this->view->projects  =  array('' => '') + $this->loadModel('projectplan')->getAliveProjects($info->fixType == 'second');//更新获取所属项目的方法
        $this->view->info     = $info;
        $this->view->action   = $this->view->info->action;
        $this->view->apps     = $this->loadModel('application')->getapplicationInfo();
        $this->view->nodes    = $this->loadModel('review')->getNodes('info', $infoID, $info->version);
        $this->view->releases = array('' => '') + $this->loadModel('project')->getReleases($info->project);
        $this->view->secondorder     = $this->loadModel('secondorder')->getPairsByIds(explode(',', $info->secondorderId));
        if($info->action === 'gain') $this->view->objects = $this->loadModel('secondline')->getByID($infoID, 'gain');
        else if($info->action === 'fix') $this->view->objects = $this->loadModel('secondline')->getByID($infoID, 'fix');
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
            $info = $this->info->getByID($infoID);
            $this->info->review($infoID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $action = 'cmconfirmed' == $info->status || 'gmsuccess' == $info->status ? 'deal' : 'review';
            $actionID = $this->loadModel('action')->create('info', $infoID, $action, $this->post->comment);
            //$this->info->sendmail($infoID, $actionID);

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }
        //数据信息
        $info = $this->loadModel('info')->getByID($infoID);
        //检查是否允许审核
        $res = $this->loadModel('info')->checkAllowReview($info, $version, $reviewStage, $this->app->user->account);
        $this->view->depts = $this->loadModel('dept')->getOptionMenu();
        $this->view->title = $this->lang->info->edit;
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
            $changes = $this->info->feedback($infoID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes || $this->post->comment)
            {
                $actionID = $this->loadModel('action')->create('info', $infoID, 'feedbacked', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $this->view->depts   = $this->loadModel('dept')->getOptionMenu();
        $this->view->title   = $this->lang->info->edit;
        $this->view->users   = $this->loadModel('user')->getPairs('noclosed');
        $this->view->info = $this->loadModel('info')->getByID($infoID);
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
            $this->info->close($infoID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            //更新需求和问题解决时间
            $info = $this->info->getByID($infoID);
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

            $this->loadModel('action')->create('info', $infoID, 'closed', $this->post->comment, $this->post->result);

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $this->view->depts = $this->loadModel('dept')->getOptionMenu();
        $this->view->users = $this->loadModel('user')->getPairs('noclosed');
        $this->view->info  = $this->loadModel('info')->getByID($infoID);
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
            $this->info->run($infoID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $this->loadModel('action')->create('info', $infoID, 'run', $this->post->comment);

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }
        //数据信息
        $info = $this->loadModel('info')->getByID($infoID);
        $this->view->title   = $this->lang->info->run;
        $this->view->users   = $this->loadModel('user')->getPairs('noclosed');
        $this->view->info = $this->loadModel('info')->getByID($infoID);
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
            $this->info->link($infoID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $this->loadModel('action')->create('info', $infoID, 'linkrelease', $this->post->comment);

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $info = $this->loadModel('info')->getByID($infoID);
        //检查是否允许关联
        $res = $this->loadModel('info')->checkAllowReview($info, $version, $reviewStage, $this->app->user->account);
        $this->view->depts    = $this->loadModel('dept')->getOptionMenu();
        $this->view->title    = $this->lang->info->edit;
        $this->view->users    = $this->loadModel('user')->getPairs('noclosed');
        $this->view->info     = $info;
        $this->view->res     = $res; //是否允许关联
       /* //新增查询，projectplan 表和TABLE_RELEASE 表关联关系
        $projectid = $this->dao->select('project')->from(TABLE_PROJECTPLAN)
            ->where('deleted')->eq(0)
            ->andWhere('id')->in($this->view->info->project)
            ->orderBy('id_desc')
            ->fetchPairs();
        $projectid =  implode(',',$projectid);*/
        $this->view->releases = array('' => '') + $this->loadModel('project')->getReleases($this->view->info->project);
       // $this->view->releases = array('' => '') + $this->loadModel('project')->getReleases($this->view->info->project);
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
        /* format the fields of every info in order to export data. */
        if($_POST)
        {
            $this->loadModel('file');
            $infoLang   = $this->lang->info;
            $infoConfig = $this->config->info;

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
            if($this->session->infoOnlyCondition)
            {
                $infos = $this->dao->select('*')->from(TABLE_INFO)->where($this->session->infoQueryCondition)
                    ->beginIF($this->post->exportType == 'selected')->andWhere('id')->in($this->cookie->checkedItem)->fi()
                    ->orderBy($orderBy)->fetchAll('id');
            }
            else
            {
                $stmt = $this->dbh->query($this->session->infoQueryCondition . ($this->post->exportType == 'selected' ? " AND id IN({$this->cookie->checkedItem})" : '') . " ORDER BY " . strtr($orderBy, '_', ' '));
                while($row = $stmt->fetch()) $infos[$row->id] = $row;
            }
            $infoIdList = array_keys($infos);

            /* Get users, products and executions. */
            $users = $this->loadModel('user')->getPairs('noletter');
            $apps  = $this->loadModel('application')->getapplicationNameCodePairs();
            $depts = $this->loadModel('dept')->getTopPairs();
            $dmap  = $this->dao->select('account,dept')->from(TABLE_USER)->fetchAll('account');
           // $projects = $this->loadModel('project')->getPairs('noclosed');
            $projects  =  array('0' => '') + $this->loadModel('projectplan')->getAllProjects(false);//更新表
            $this->loadModel('secondline');

            // 获取待处理人数据集。
            $reviewerList = $this->loadModel('review')->getObjectIdListReviewer($infoIdList, 'info');

            foreach($infos as $info)
            {
                $initStatus = $info->status;
                // 获取关联发布。
                if($info->release) $info->release = $this->dao->select('name')->from(TABLE_RELEASE)->where('id')->eq($info->release)->fetch('name');

                $info->status   = $infoLang->statusList[$info->status];
                $info->type     = $infoLang->typeList[$info->type];

                //迭代二十六-删除部门第一个'/'
                $info->createdDept = ltrim(zget($depts, $dmap[$info->createdBy]->dept, ''), '/');
                $info->createdBy   = $users[$info->createdBy];

                $info->createdDate  = substr($info->createdDate,0, 10);//创建时间
                $info->editedDate  = substr($info->editedDate,0, 10);//编辑时间

                // 处理实现方式。
                $info->fixType = $infoLang->fixTypeList[$info->fixType];
                $info->isJinke = $infoLang->isJinkeList[$info->isJinke];
                $info->desensitizationType = $infoLang->desensitizationTypeList[$info->desensitizationType];
                $info->deadline = $info->isDeadline == '1'? '长期':substr($info->deadline,0,10);
                $info->fetchResult = $infoLang->fetchResultList[$info->fetchResult];
                $info->secondorderId = trim($info->secondorderId, ',');
                if($info->secondorderId)
                {
                    $objects = $this->dao->select('id,code')->from(TABLE_SECONDORDER)->where('id')->in($info->secondorderId)->fetchPairs();
                    $as = array();
                    foreach($objects as $object)
                    {
                        if(!$object) continue;
                        $as[] = $object;
                    }
                    $info->secondorderId = implode(',', $as);
                }

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

                $dealUsers = $this->loadModel('info')->getInfoDealUsers($tempInfo);
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
                    $cs = array();
                    foreach(explode(',', $info->project) as $c)
                    {
                        if($c) $cs[] = $projects[$c];
                    }
                    $info->project = implode(',', $cs);
                }

                // 处理数据类别。
                if($info->classify)
                {
                    $cs = array();
                    foreach(explode(',', $info->classify) as $c)
                    {
                        if($c and isset($infoLang->techList[$c])) $cs[] = $infoLang->techList[$c];
                    }
                    $info->classify = implode(',', $cs);
                }

                if($action == 'gain')
                {
                    // if($info->node)
                    // {
                    //     $cs = array();
                    //     foreach(explode(',', $info->node) as $c)
                    //     {
                    //         if($c and isset($infoLang->nodeList[$c])) $cs[] = $infoLang->nodeList[$c];
                    //     }
                    //     $info->gainNode = implode(',', $cs);
                    // }

                    $info->gainReason  = $info->reason;
                    $info->gainPurpose = $info->purpose;
                    $info->gainStep    = $info->step;
                    $info->gainDesc    = $info->desc;

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

                /* 获取关联的问题单、需求单。*/
                $info->problem = '';
                $info->demand  = '';
                $relationObject = $this->secondline->getByID($info->id, $action);
                foreach($relationObject['problem'] as $objectID => $object)
                {
                    $info->problem .= $object . "\r\n";
                }

                foreach($relationObject['demand'] as $objectID => $object)
                {
                    $info->demand .= $object . "\r\n";
                }
                //退回原因
                $revertReason       = '';
                $revertReasonChild  = '';
                if(!empty($info->revertReason)) {
                    $childTypeList = json_decode($this->lang->info->childTypeList['all'],true);
                    $childType = [];
                    foreach ($childTypeList as $k=>$v){
                        $childType += $v;
                    }

                    foreach (json_decode($info->revertReason,true) as $index => $item) {
                        if (!empty($item)) {
                            $revertReason       .= $item['RevertDate'] .':'. zget($this->lang->info->revertReasonList, $item['RevertReason']).PHP_EOL;
                            if (isset($childType[$item['RevertReasonChild']])){
                                $revertReasonChild  .= $item['RevertDate'] .':'. $childType[$item['RevertReasonChild']].PHP_EOL;
                            }
                        }
                    }
                }
                $info->revertReason                      = $revertReason;
                $info->revertReasonChild                 = $revertReasonChild;
            }
            $this->post->set('fields', $fields);
            $this->post->set('rows', $infos);
            $this->post->set('kind', $action);
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

       // $this->view->fileName        = $this->lang->info->browse;
        $this->view->fileName        = $action == 'gain' ? $this->lang->info->exportgain : $this->lang->info->exportfix; //20220224 修改获取正确文件名称
        $this->view->allExportFields = $action == 'gain' ? $this->config->info->list->exportGainFields : $this->config->info->list->exportFixFields;
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
            $this->dao->update(TABLE_INFO)->set('status')->eq('deleted')->where('id')->eq($infoID)->exec();
            $this->loadModel('datamanagement')->syncDataStatus($infoID,'info');
            $actionID = $this->loadModel('action')->create('info', $infoID, 'deleted', $this->post->comment);
            //2022.4.21 tangfei 删除与问题需求的关联关系
            $sql = "delete from zt_secondline where (objectType='demand'or objectType='problem') and relationID=$infoID  and (relationType='gain' or relationType='fix');";
            $this->dao->query($sql);

           //更新需求和问题解决时间
           $info = $this->info->getByID($infoID);
           /** @var problemModel $problemModel */
           $problemModel = $this->loadModel('problem');
//           if(!empty($info->demand)){
//               $demandIds =array_filter(explode(',',$info->demand));
//               if($demandIds){
//                   foreach($demandIds as $demandId)
//                   {
//                     $problemModel->getAllSecondSolveTime($demandId,'demand');
//                   }
//               }
//           }
           /*if(!empty($info->problem)){
               $problemIds =array_filter(explode(',',$info->problem));
               if($problemIds){
                   foreach($problemIds as $problemId)
                   {
                      $problemModel->getAllSecondSolveTime($problemId,'problem');
                   }
               }
           }*/
            $list  =  $info->action == 'fix' ? ($this->session->infoList ? $this->session->infoList : inLink('fix')) : ($this->session->infoGainList ? $this->session->infoGainList : inLink('gain'));
            $backUrl = $list;
            if(isonlybody()) die(js::closeModal('parent.parent', $backUrl));
            die(js::reload('parent'));
        }

        $info = $this->info->getByID($infoID);
        $this->view->actions = $this->loadModel('action')->getList('info', $infoID);
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
        $info  = $this->info->getById($infoID);
        $users = $this->loadModel('user')->getPairs();

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

        $section->addTitle(($info->action == 'fix' ? $this->lang->info->exportFixTitle : $this->lang->info->exportGainTitle), 1);
        $section->addText($this->lang->info->code . ' ' . $info->code, 'font_default', 'align_right');

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
        $table->addCell(1000, $cellStyle)->addText($this->lang->info->type);
        $table->addCell(1000, $cellStyle)->addText(zget($this->lang->info->typeList, $info->type, ''));
        $table->addCell(1000, $cellStyle)->addText($this->lang->info->status);
        $table->addCell(1000, $cellStyle)->addText(zget($this->lang->info->statusList, $info->status, ''));

        $projects  = array('' => '') + $this->loadModel('project')->getPairs('noclosed');
        $ps = array();
        foreach(explode(',', $info->project) as $project)
        {
            $ps[] = zget($projects, $project, '');
        }
        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->info->node);
        $table->addCell(1000, $cellStyle)->addText($info->node);
        $table->addCell(1000, $cellStyle)->addText($this->lang->info->project);
        $table->addCell(1000, $cellStyle)->addText(implode(',', $ps));

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->info->isJinke);
        $table->addCell(1000, $cellStyle)->addText($this->lang->info->isJinkeList[$info->isJinke]);
        $table->addCell(1000, $cellStyle)->addText('');
        $table->addCell(1000, $cellStyle)->addText('');
        
        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->info->deadline);
        $table->addCell(1000, $cellStyle)->addText($info->isDeadline=='1'?'长期':substr($info->deadline,0,10));
        $table->addCell(1000, $cellStyle)->addText($this->lang->info->desensitizationType);
        $table->addCell(1000, $cellStyle)->addText($this->lang->info->desensitizationTypeList[$info->desensitizationType]);

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->info->planBegin);
        $table->addCell(1000, $cellStyle)->addText($info->planBegin);
        $table->addCell(1000, $cellStyle)->addText($this->lang->info->planEnd);
        $table->addCell(1000, $cellStyle)->addText($info->planEnd);

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->info->actualBegin);
        $table->addCell(1000, $cellStyle)->addText($info->actualBegin);
        $table->addCell(1000, $cellStyle)->addText($this->lang->info->actualEnd);
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
        $table->addCell(1000, $cellStyle)->addText($this->lang->info->problem);
        $table->addCell(1000, $cellStyle)->addText(implode(',', $ps));

        $demands = $this->loadModel('demand')->getPairs('noclosed');
        $info->demand = trim($info->demand, ',');
        $ds = array();
        if($info->demand)
        {
            $demandList = explode(',', $info->demand);
            foreach($demandList as $d) $ds[] = $demands[$d];
        }

        $table->addCell(1000, $cellStyle)->addText($this->lang->info->demand);
        $table->addCell(1000, $cellStyle)->addText(implode(',', $ds));

        $secondorder = $this->loadModel('secondorder')->getPairsByIds(explode(',', $info->secondorderId));
        $secondorderCode = '';
        foreach (explode(',', $info->secondorderId) as $secondorderId){
            if ($secondorderId and $secondorder->$secondorderId['code']) {
                if($secondorderCode==''){
                    $secondorderCode = $secondorder->$secondorderId['code'];
                }else{
                    $secondorderCode = $secondorderCode.'，'.$secondorder->$secondorderId['code'];
                }
            }
        }

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->info->secondorderId);
        $table->addCell(3000, array('gridSpan' => 3))->addText($secondorderCode);

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->info->createdBy);
        $table->addCell(1000, $cellStyle)->addText(zget($users, $info->createdBy, ''));
        $table->addCell(1000, $cellStyle)->addText($this->lang->info->createdDate);
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
        $table->addCell(1000, $cellStyle)->addText($this->lang->info->app);
        $table->addCell(3000, array('gridSpan' => 3))->addText($info->app);

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($info->action == 'fix' ? $this->lang->info->fixDesc : $this->lang->info->gainDesc);
        $table->addCell(3000, array('gridSpan' => 3))->addText(strip_tags($info->desc));

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($info->action == 'fix' ? $this->lang->info->fixReason : $this->lang->info->gainReason);
        $table->addCell(3000, array('gridSpan' => 3))->addText(strip_tags($info->reason));

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->info->operation);
        $table->addCell(3000, array('gridSpan' => 3))->addText(strip_tags($info->operation));

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->info->test);
        $table->addCell(3000, array('gridSpan' => 3))->addText(strip_tags($info->test));

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($info->action == 'fix' ? $this->lang->info->fixStep : $this->lang->info->gainStep);
        $table->addCell(3000, array('gridSpan' => 3))->addText(strip_tags($info->step));

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->info->checkList);
        $table->addCell(3000, array('gridSpan' => 3))->addText(strip_tags($info->checkList));

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($info->action == 'fix' ? $this->lang->info->fixResult : $this->lang->info->gainResult);
        $table->addCell(3000, array('gridSpan' => 3))->addText(strip_tags($info->result));

        /* Review. */
        $table->addRow();
        $table->addCell(4000, array('gridSpan' => 4))->addText($this->lang->info->reviewComment, 'font_bold', array('align' => 'center'));

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->info->reviewNode);
        $table->addCell(1000, $cellStyle)->addText($this->lang->info->reviewer);
        $table->addCell(1000, $cellStyle)->addText($this->lang->info->reviewResult);
        $table->addCell(1000, $cellStyle)->addText($this->lang->info->reviewComment);

        $nodes = $this->loadModel('review')->getNodes('info', $infoID, $info->version);
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
            $table->addCell(1000, $cellStyle)->addText(zget($this->lang->info->reviewNodeList, $key));
            $table->addCell(1000, $cellStyle)->addText($reviewerUsersStr);
            $table->addCell(1000, $cellStyle)->addText(zget($this->lang->info->confirmResultList, $realReviewerInfo->status, '') . $realReviewerInfo->reviewerUserName);
            $table->addCell(1000, $cellStyle)->addText($realReviewerInfo->comment);
        }

        /* Consumed. */
        $table->addRow();
        $table->addCell(4000, array('gridSpan' => 4))->addText($this->lang->consumedTitle, 'font_bold', array('align' => 'center'));

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->info->nodeUser);
       // $table->addCell(1000, $cellStyle)->addText($this->lang->info->consumed);
        $table->addCell(1000, $cellStyle)->addText($this->lang->info->before);
        $table->addCell(2000,  array('gridSpan' => 2))->addText($this->lang->info->after);

        foreach($info->consumed as $c)
        {
            $table->addRow();
            $table->addCell(1000, $cellStyle)->addText(zget($users, $c->createdBy, ''));
           // $table->addCell(1000, $cellStyle)->addText($c->consumed . '' . $this->lang->hour);
            $table->addCell(1000, $cellStyle)->addText(zget($this->lang->info->statusList, $c->before, '-'));
            $table->addCell(2000,  array('gridSpan' => 2))->addText(zget($this->lang->info->statusList, $c->after, '-'));
        }

        $this->loadModel('file')->export2Word(($info->action == 'fix' ? $this->lang->info->exportFixTitle : $this->lang->info->exportGainTitle) . $info->code, $phpWord);
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
            $infoID = $this->info->create($action);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $actionID = $this->loadModel('action')->create('info', $infoID, 'created', $this->post->comment);
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
 
         $info = $this->info->getByID($infoID);
         $this->app->rawMethod = $info->action;
 
         $this->view->title     = $this->lang->info->copy;
         $this->view->users     = $this->loadModel('user')->getPairs('noclosed');
         $this->view->apps      = $this->loadModel('application')->getapplicationNameCodePairs();
         $this->view->info      = $info;
         $this->view->problems  = array('' => '') + $this->loadModel('problem')->getPairsAbstract('noclosed');
         $this->view->demands   = array('' => '') + $this->loadModel('demand')->getPairsTitle('noclosed');//关联二线工单
         $this->view->secondorderList   = array('' => '') + $this->dao->select('id,concat(concat(concat(code,"（"),summary),"）")')->from(TABLE_SECONDORDER)->where('deleted')->ne('1')->fetchPairs(); 
//         $this->view->projects  = array('' => '') + $this->loadModel('project')->getPairs('noclosed');
         $this->view->projects  =  array('' => '') + $this->loadModel('projectplan')->getAliveProjects($info->fixType == 'second');
         $this->view->action    = $this->view->info->action;
         $deptId = $info->createdDept;
         $this->view->reviewers = $this->info->getReviewers($deptId);
         $this->view->reviewers = $this->info->getReviewers();
         //审核节点以及审核节点的审核人
         $nodesReviewers = $this->loadModel('review')->getAllNodeReviewers('info', $infoID, $info->version);
         $this->view->reviewerAccounts = $this->loadModel('review')->getReviewerAccounts($this->view->reviewers);
         $this->view->nodesReviewers = $nodesReviewers;
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
            $this->info->reject($infoID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $actionID = $this->loadModel('action')->create('info', $infoID, 'reject', $this->post->comment);
            $this->loadModel('datamanagement')->syncDataStatus($infoID,'info');
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }
        //数据信息
        $info = $this->info->getByID($infoID);
        //检查是否允许驳回
        $res = $this->info->checkAllowReject($info);

        $this->view->depts = $this->loadModel('dept')->getOptionMenu();
        $this->view->title = $this->lang->info->reject;
        $this->view->users = $this->loadModel('user')->getPairs('noclosed');
        $this->view->info  = $info;
        $nodesReviewers = $this->loadModel('review')->getAllNodeReviewers('info', $infoID, $info->version);
        $this->view->nodesReviewers = $nodesReviewers;
        //是否允许审核
        $this->view->res   = $res;
        $this->display();
    }
    //查看历史审核记录
    public function showHistoryNodes($id){
        $info = $this->info->getByID($id);
        $this->app->loadLang('outwarddelivery');
        $res = $this->dao->select('version')->from(TABLE_REVIEWNODE)->where('objectType')->eq('info')->andWhere('objectID')->eq($id)->groupby('version')->fetchall();
        $versions = array_column($res,'version');
        $nodes = [];
        foreach ($versions as $version) {
            $data = $this->loadModel('review')->getNodes('info', $id, $version);
            foreach ($data as $k=>$v){
                if ($v->status == 'wait' || !(is_array($v->reviewers) && !empty($v->reviewers))){
                    unset($data[$k]);
                }
                if ($v->stage == 4 && isset($info->createdDate) && $info->createdDate > "2024-04-02 23:59:59"){
                    unset($data[$k]);
                }
            }
            $nodes[]['nodes'] = $data;
        }
        foreach ($nodes as $key=>$node) {
            $nodes[$key]['countNodes'] = count($node['nodes']);
        }
        $this->view->nodes = $nodes;
        $this->view->users                   = $this->loadModel('user')->getPairs('noletter');
        $this->view->info = $this->info->getByID($id);
        $this->display();
    }


}

