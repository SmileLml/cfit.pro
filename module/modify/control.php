<?php
class modify extends control
{
    public function __construct()
    {
        parent::__construct();
        // 上海分公司审核节点名称修改
        if (in_array($this->app->getMethodName(),['create','copy'])){
            $this->modify->resetNodeAndReviewerName();
        }
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
    public function reject($modifyID = 0)
    {
        if($_POST)
        {
            $this->modify->reject($modifyID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $actionID = $this->loadModel('action')->create('modify', $modifyID, 'reject', $this->post->comment);
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }
        //数据信息
        $modify = $this->modify->getByID($modifyID);
        //检查是否允许驳回
        $res = $this->modify->checkAllowReject($modify);

        $this->view->depts      = $this->loadModel('dept')->getOptionMenu();
        $this->view->title      = $this->lang->modify->reject;
        $this->view->users      = $this->loadModel('user')->getPairs('noclosed');
        $this->view->modify     = $modify;
        $nodesReviewers = $this->loadModel('review')->getAllNodeReviewers('modify', $modifyID, $modify->version);
        $this->view->nodesReviewers = $nodesReviewers;
        $this->view->releases = array('' => '') + $this->loadModel('project')->getReleases($this->view->modify->projectPlanId);
        //是否允许审核
        $this->view->res        = $res;
        $this->display();
    }

    public function submit($id = 0,$linkType=1)
    {
        if($_POST)
        {
            $this->modify->submit($id);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $this->loadModel('action')->create('modify', $id, 'submit', $this->post->comment);
            if ($linkType != '1'){
                $url = explode('?',$this->createLink('modify', 'view','id='.$id));
                die(js::locate($url[0],'parent'));
            }
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }
        $modify = $this->modify->getByID($id);
        $nodesReviewers = $this->loadModel('review')->getAllNodeReviewers('modify', $id, $modify->version);
        $lastAction = $this->dao->select('action')->from(TABLE_ACTION)
        ->where('objectType')->eq('modify')
        ->andwhere('objectID')->eq($id)
        ->andwhere('action')->in(array('reject','review'))
        ->orderBy('id_desc')
        ->fetch('action');
        //迭代三十加变更锁
        $demandInfo = $this->loadModel('demand')->getDemandLockByIds(trim($modify->demandId,','));
        $lockCode = '';
        if(!empty($demandInfo))  $lockCode = implode(',',array_column($demandInfo,'code'));

        $lastReview = '';
        if($lastAction =='review'){// 内部退回就判断已经进行到了哪一步
            $lastReview = $this->dao->select('`before`')->from(TABLE_CONSUMED)
            ->where('objectType')->eq('modify')
            ->andwhere('objectID')->eq($id)
//            ->andwhere('after')->eq('reviewfailed')
            ->andwhere('after')->eq('reject')
            ->orderBy('id_desc')
            ->fetch('before');
            if ($lastReview == ''){
                $lastReview = $this->dao->select('`before`')->from(TABLE_CONSUMED)
                    ->where('objectType')->eq('modify')
                    ->andwhere('objectID')->eq($id)
                    ->andwhere('after')->eq('reviewfailed')
                    ->orderBy('id_desc')
                    ->fetch('before');
            }
        }
        $this->view->unpassedKey =$lastReview?array_search($lastReview,$this->lang->modify->reviewBeforeStatusList):'';
        $this->view->lastAction = $lastAction;
        $this->view->lockCode = $lockCode;
        $this->view->modify = $modify;
        $this->view->linkType = $linkType;
        $this->view->nodesReviewers = $nodesReviewers;
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: browse
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:44
     * Desc: This is the code comment. This method is called browse.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param string $browseType
     * @param int $param
     * @param string $orderBy
     * @param int $recTotal
     * @param int $recPerPage
     * @param int $pageID
     */
    public function browse($browseType = 'all', $param = 0, $orderBy = 'code_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
      $this->view->title        = $this->lang->modify->browse;
      $this->view->users        = $this->loadModel('user')->getPairs('noletter');
      $this->view->depts        = $this->loadModel('dept')->getTopPairs();
      $this->view->dmap         = $this->dao->select('account,realname,dept')->from(TABLE_USER)->fetchAll('account');
      $this->view->projectList  = $this->loadModel('projectplan')->getAllProjects();

      $apps  = $this->loadModel('application')->getapplicationNameCodePairsWithisPayment();
      $appList = array();
      foreach($apps as $app){
        $appList[$app->id] = $app->name;
      }

      $browseType = strtolower($browseType);

      $queryID = ($browseType == 'bysearch')  ? (int)$param : 0;
      $actionURL = $this->createLink('modify', 'browse', "browseType=bySearch&param=myQueryID");
      $this->modify->buildSearchForm($queryID, $actionURL);

      /* Load pager. */
      $this->app->loadClass('pager', $static = true);
      $pager = new pager($recTotal, $recPerPage, $pageID);

        /* 设置详情页面返回的url连接。*/
      $this->session->set('modifyList', $this->app->getURI(true));
      $modify = $this->modify->getList($browseType,$queryID,$orderBy,$pager);
      foreach ($modify as $item){
        if(!$item->dealUser){
            $item->dealUser = $this->loadModel('review')->getReviewer('modify', $item->id, $item->version, $item->reviewStage);
        }
        //授权管理
        $item->dealUser = $this->loadModel('common')->getAuthorizer('modify', $item->dealUser,$item->status, $this->lang->modify->authorizeStatusList);
        if (in_array($item->status,$this->lang->modify->reissueArray) || $item->status == 'modifysuccess' || $item->status == 'cancel'){
          $item->dealUser = '';
        }
        $apps = array();
        foreach(explode(',',$item->app)  as $app){
          if(!empty($app)){
            $apps[] = zget($appList,$app);
          }
        }
        $item->app = implode('，',$apps);
      }

      $this->view->modify = $modify;

      $this->view->orderBy    = $orderBy;
      $this->view->pager      = $pager;
      $this->view->param      = $param;
      $this->view->browseType = $browseType;
      $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: create
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:44
     * Desc: This is the code comment. This method is called create.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     */
    public function create()
    {
        $this->view->title = $this->lang->modify->create;

        if($_POST)
        {
            $modifyID = $this->modify->create();
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            if ($_POST['abnormalCode']){
                $modify = $this->modify->getByID($_POST['abnormalCode']);
                $this->post->comment = $this->lang->modify->associaitonOrder.'：'.$modify->code.'<br/>'.$this->post->comment;
            }
            $this->loadModel('action')->create('modify', $modifyID, 'created', $this->post->comment);

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = inlink('browse');
            $response['id']      = $modifyID;
            if ($_POST['issubmit'] == 'submit'){
                $response['message'] = $this->lang->submitSuccess;
                $response['iframeUrl']  = $this->createLink('modify', 'submit', "id=".$modifyID."&linkType=2",'',true);
            }
            $this->send($response);
        }

        //获取未被关联的异常变更单
        $abnormalList = array('' => '') + $this->modify->getModifyAbnormal();
        $this->view->abnormalList = $abnormalList;
        $demandLang = $this->app->loadLang('demand')->demand;
        $this->loadModel("modifycncc");
        //申请测试单键值对
        $this->view->testingrequestList = array('' => '') + $this->loadModel("testingrequest")->getPairs();
        //产品等级键值对
        $this->view->productenrollList = array('' => '') + $this->loadModel("productenroll")->getPairs();
        //产品线键值对
        $this->view->productlineList = array('' => '') + $this->loadModel('productline')->getPairs();
        //所属系统
        $this->view->appAll  =  $this->loadModel('application')->getApplicationCodePairsSyncJinx();
        $this->view->apps = array('' => '') + array_column($this->view->appAll, 'name', 'id');        
        //产品名称
//        $this->view->productList   = array('' => '') + $this->loadModel('product')->getProductWithCodeName('noclosed');
        //所属项目
        $this->view->projectList       = array('' => '') + $this->loadModel('projectplan')->getAliveProjects(false);
        //关联问题
        $this->view->problemList       = array('' => '') + $this->loadModel('problem')->getPairsAbstract('noclosed');
        //关联需求
        $this->view->demandList        = array('' => '') + $this->loadModel('demand')->modifySelect('modify');
        //关联二线工单
        $this->view->secondorderList   = array('' => '') + $this->dao->select('id,concat(concat(concat(code,"（"),summary),"）")')->from(TABLE_SECONDORDER)->where('deleted')->ne('1')->fetchPairs();
        //关联需求任务
        $this->view->requirementList    = array('' => '') + $this->dao->select('id,concat(concat(concat(code,"（"),name),"）")')->from(TABLE_REQUIREMENT)->where('status')->ne('deleted')->fetchPairs();
        //所属CBP项目
        $this->view->cbpprojectList     = array('' => '') + $this->dao->select('code,concat(concat(concat(code,"（"),name),"）")')->from(TABLE_CBPPROJECT)->where('deleted')->ne(1)->fetchPairs();
        // 关联变更单
        $this->view->modifycnccList = array('' => '') + $this->dao->select('id,concat(concat(concat(code,"（"),substring(`desc`,1,25)),"）")')->from(TABLE_MODIFYCNCC)->fetchPairs();        //审核节点下的审核人列表
        //(外部)项目/任务
        $this->view->outsideProjectList =  array('' => '') + $this->loadModel('outsideplan')->getPairs();
        $reviewers            = $this->modify->getReviewers();
        //审核节点下的审核人列表
        $reviewerAccounts     = $this->loadModel('review')->getReviewerAccounts($reviewers);
        $this->view->users           = $this->loadModel('user')->getPairs('noletter');

        //审核节点下默认设置审核节点人
        $defChosenReviewNodes = $this->config->modify->create->setDefChosenReviewNodes;
        $this->view->reviewers            = $reviewers;
        $this->view->reviewerAccounts     = $reviewerAccounts;
        $this->view->defChosenReviewNodes = $defChosenReviewNodes;
        $this->display();
    }

    /**
     * Edit a modify.
     * 
     * @param  int $modifyID 
     * @access public
     * @return void
     */
    public function edit($modifyID = 0)
    {
        $this->loadModel("modifycncc");
        //获取对外交付信息
        $modify = $this->modify->getByID($modifyID);
        if($_POST)
        {
            $changes = $this->modify->edit($modifyID);
            if(dao::isError() or isset($changes->editError))
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            if ($_POST['abnormalCode'] && $_POST['abnormalCode'] != $modify->abnormalCode){
                $modify = $this->modify->getByID($_POST['abnormalCode']);
                $this->post->comment = $this->lang->modify->associaitonOrder.'：'.$modify->code.'<br/>'.$this->post->comment;
            }
            $actionID = $this->loadModel('action')->create('modify', $modifyID, 'edited', $this->post->comment);
            $this->action->logHistory($actionID, $changes);
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = inlink('browse');
            $response['status']  = $modify->status;
            if ($_POST['issubmit'] == 'submit'){
                $response['message'] = $this->lang->submitSuccess;
                $response['iframeUrl']  = $this->createLink('modify', 'submit', "id=".$modifyID."&linkType=2",'',true);
            }
            $this->send($response);
        }
        //获取关联。被关联的变更单
        $abnormalOrder = $this->getAbnormalById($modify);
        $modify->abnormalCode = isset($abnormalOrder['nowOrder']->id) ? $abnormalOrder['nowOrder']->id : '';
        // 手动赋值
        $modify->isNewTestingRequest = 0;
        $modify->isNewProductEnroll = 0;
        $modify->isNewModifycncc = 1;
        if(empty($modify->ROR)){
            $modify->RORList = array();
        }else{
            $modify->RORList = json_decode(json_encode($modify->ROR),true);
        }
        if(!empty($modify->consumed)){
            $modify->consumed = array_pop($modify->consumed)->consumed;
        }
        $this->view->modify   = $modify;
        $this->view->productenroll = new stdclass();
        $this->view->productenroll->isOnly = false;
        $this->view->productenroll->disable = false;
        $this->view->testingrequest = new stdclass();
        $this->view->testingrequest->isOnly = false;
        $this->view->testingrequest->disable = false;
        $this->view->modifycncc = new stdclass();
        $this->view->modifycncc->disable = false;

        //获取异常变更单
        $abnormalList = array('' => '') + $this->modify->getModifyAbnormal();
        if ($abnormalOrder['nowOrder']){
            $abnormalList[$abnormalOrder['nowOrder']->id] = $abnormalOrder['nowOrder']->code;
        }
//        if ($modify->abnormalCode!=''){
//            $abnormalList = $abnormalList + array($abnormalOrder['nowOrder']->id=>$abnormalOrder['nowOrder']->code);
//        }
        $this->view->abnormalList = $abnormalList;
        //标题
        $this->view->title = $this->lang->modify->edit;
        //申请测试单键值对
        $this->view->testingrequestList = array('' => '') + $this->loadModel("testingrequest")->getPairs($modifyID);
        //产品等级键值对
        $this->view->productenrollList = array('' => '') + $this->loadModel("productenroll")->getPairs($modifyID);
        //产品线键值对
        $this->view->productlineList = array('' => '') + $this->loadModel('productline')->getPairs();
        //所属系统
        $this->view->appAll  =  $this->loadModel('application')->getApplicationCodePairsSyncJinx();
        $this->view->apps = array('' => '') + array_column($this->view->appAll, 'name', 'id');
        //产品名称
        $app = 0;
        if ($modify->createdDate > "2023-05-31 23:59:59"){
            $app = trim($modify->app,',');
        }
        $this->view->productList   = array('' => '') + $this->loadModel('product')->getProductWithCodeName('noclosed',0,'',$app);
        //产品名称被选中的列
        $this->view->productSelectList   = $this->dao->select('id,name')
                                            ->from(TABLE_PRODUCT)
                                            ->where('deleted')->eq(0)->andwhere('id')->in($modify->productId)
                                            ->fetchPairs('id', 'name');
        //所属项目
        $this->view->projectList       = array('' => '') + $this->loadModel('projectplan')->getAliveProjects(false);
        //关联问题
        $this->view->problemList       = array('' => '') + $this->loadModel('problem')->getPairsAbstract('noclosed');
        //获取问题选中列
        $this->view->problemSelectList     = $this->dao->select("id,concat(code,'（',IFNULL(abstract,''),'）') as code")->from(TABLE_PROBLEM)
                                        ->where('status')->ne('deleted')->andwhere('id')->in($modify->problemId)
                                        ->orderBy('id_desc')
                                        ->fetchPairs();;
        //关联需求
//        $this->view->demandList = $this->loadModel('demand')->modifySelectByEdit($modify->demandId, 'modify', $modifyID,1,'edit');
        //关联二线工单
        $this->view->secondorderList   = array('' => '') + $this->dao->select('id,concat(concat(concat(code,"（"),summary),"）")')->from(TABLE_SECONDORDER)->where('deleted')->ne('1')->fetchPairs();
        //关联需求任务
        if(empty($modify->demandId)){
            $requirementList = array('' => '') + $this->dao->select('id,concat(concat(concat(code,"（"),name),"）")')->from(TABLE_REQUIREMENT)->where('status')->ne('deleted')->fetchPairs();
        }else{
            $opinionId = $this->dao->select('distinct opinionID')->from(TABLE_DEMAND)->where('id')->in(explode(',',$modify->demandId))->fetchAll();
            $requirementList = array('' => '') + $this->dao->select('id,concat(concat(concat(code,"（"),name),"）")')->from(TABLE_REQUIREMENT)->where('opinion')->in(array_column($opinionId,'opinionID'))->andWhere('status')->ne('deleted')->fetchpairs();
        }
        $this->view->requirementList   = $requirementList;
        //获取关联需求任务选中列
        $this->view->requirementSelectList     = $this->dao->select('id,concat(concat(concat(code,"（"),name),"）")')->from(TABLE_REQUIREMENT)
            ->where('status')->ne('deleted')->andwhere('id')->in($modify->requirementId)
            ->orderBy('id_desc')
            ->fetchPairs();;
        //所属CBP项目
        $this->view->cbpprojectList     = array('' => '') + $this->dao->select('code,concat(concat(concat(code,"（"),name),"）")')->from(TABLE_CBPPROJECT)->where('deleted')->ne(1)->fetchPairs();
        //产品变更
        $this->view->modifycnccList = array('' => '') + $this->dao->select('id,concat(concat(concat(code,"（"),substring(`desc`,1,25)),"）")')->from(TABLE_MODIFYCNCC)->fetchPairs();
        //审核节点下的审核人列表
        $reviewers            = $this->modify->getReviewers($modify->createdDept);
        //审核节点下的审核人列表
        $reviewerAccounts     = $this->loadModel('review')->getReviewerAccounts($reviewers);
        //审核节点下默认设置审核节点人
        $defChosenReviewNodes = $this->config->modifycncc->create->setDefChosenReviewNodes;
        $this->view->reviewers            = $reviewers;
        $this->view->users           = $this->loadModel('user')->getPairs('noletter');
        $this->view->reviewerAccounts     = $reviewerAccounts;
        $this->view->defChosenReviewNodes = $defChosenReviewNodes;
        //审核节点以及审核节点的审核人
        $nodesReviewers = $this->loadModel('review')->getAllNodeReviewers('modify', $modifyID, $modify->version);
        $this->view->nodesReviewers = $nodesReviewers;
        //(外部)项目/任务
        $this->view->outsideProjectList =  array('' => '') + $this->loadModel('outsideplan')->getPairs();
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: view
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:44
     * Desc: This is the code comment. This method is called view.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $modifyID
     */
    public function view($id = 0)
    {
        $this->app->loadLang('outwarddelivery');
        $this->app->loadLang('release');
        $this->app->loadLang('projectrelease');
        $this->app->loadLang('testingrequest');
        $this->app->loadLang('productenroll');
        $this->app->loadLang('modifycncc');
        $this->app->loadLang('application');
        $this->app->loadLang('file');
        $this->app->loadLang('api');
        $this->loadModel('outwarddelivery');

        $this->view->title                   = $this->lang->modify->view;
        $modify  = $this->modify->getByID($id);
        //a(json_decode($modify->reviewFailReason));die;
        //获取关联。被关联的变更单
        $abnormalOrder = $this->getAbnormalById($modify);
        $this->view->abnormalOrder              = $abnormalOrder;
        //获取未被关联的异常变更单
        $abnormalList = array('' => '') + $this->modify->getModifyAbnormal();
        $this->view->abnormalList = $abnormalList;
        // $requirementId = $this->dao->select('requirementID')->from(TABLE_DEMAND)->where('id')->in(explode(',',trim($modify->demandId,',')))->fetchall();
        // $modify->requirementId =  implode(',', array_column($requirementId,'requirementID'));
        $this->view->modify              = $modify;
        if($modify->productenrollId){
            $this->view->productenroll          = $this->loadModel('productenroll')->getByID($modify->productenrollId);
            $this->view->modify->productRegistrationCode = $this->productenroll->getEmisRegisterNumberById($modify->productenrollId)->emisRegisterNumber;
        }else{
            $this->view->modify->productRegistrationCode = '';
        }
        if($modify->testingRequestId){
            $this->view->testingrequest          = $this->loadModel('testingrequest')->getByID($modify->testingRequestId);
        }else{
            $this->view->testingrequest = '';
        }
        $this->view->MClog = $this->modify->getRequestLog($modify->id);
        $this->view->reviewReportTitle = $this->dao->select('title')->from(TABLE_REVIEW)->where('id')->eq($modify->reviewReport)->fetch('title');
        $this->view->demand                  = $this->loadModel('demand')->getPairsByIds(explode(',', $modify->demandId));
        $this->view->problem                 = $this->loadModel('problem')->getPairsByIds(explode(',', $modify->problemId));
        $this->view->requirement             = $this->loadModel('requirement')->getPairsByIds(explode(',', $modify->requirementId));
        $this->view->secondorder             = $this->loadModel('secondorder')->getPairsByIds(explode(',', $modify->secondorderId));

        // $this->view->relations               = $this->outwarddelivery->getAllRelations($id);

        $this->view->allLines                = $this->loadModel('productline')->getPairs();
        $this->view->allProductNames         = $this->loadModel('product')->getNamePairs();
        $this->view->allProductCodes         = $this->loadModel('product')->getCodePairs();
        $this->view->depts                   = $this->loadModel('dept')->getDeptPairs();
        $this->view->users                   = $this->loadModel('user')->getPairs('noletter');
        $this->view->projects                = array('' => '') + $this->loadModel('projectplan')->getProject($modify->implementationForm == 'second');//更新获取所属项目的方法
        
        $modify->appsInfo                    = (Object)$this->outwarddelivery->getAppInfo(explode(',',$modify->app));
        $modify->CBPInfo                     = $this->outwarddelivery->getCBPInfo($modify->CBPprojectId);
        $this->view->releaseInfoList         = $this->outwarddelivery->getReleaseInfoInIds($modify->release);
        $this->view->actions                 = $this->loadModel('action')->getList('modify', $id );
        $this->view->currentUser                 = $this->app->user->account;
        $secondLineReviewList = array_keys($this->lang->modify->secondLineReviewList);
        $reviewReportList = array('' => '') + $this->loadModel('review')->getPairs('','');
        $this->view->reviewReportList = $reviewReportList;
        if(!in_array($modify->status, array('waitsubmitted', 'wait', 'reject', 'cmconfirmed', 'groupsuccess', 'managersuccess', 'posuccess', 'leadersuccess','gmsuccess','waitqingzong')) and strtotime($modify->pushDate) <= strtotime('2023-08-16')){
            $this->view->isSecond = true;
        }else{
            $this->view->isSecond = false;
        }

        if(in_array($this->app->user->account,$secondLineReviewList)){
            $this->view->isReview = true;
        }else{
            $this->view->isReview = false;
        }

        $this->view->nodes                    = $this->loadModel('review')->getNodes('modify', $id, $modify->version);
        $this->view->currentReviewers         = $this->loadModel('review')->getReviewer('modify', $id, $modify->version, $modify->reviewStage);
        //tangfei 详情页面增加处理人变量，审批按钮显示条件
        $modify->reviewers = $this->view->currentReviewers;
        $secondlineStage                      = array_search('产创部二线专员',$this->lang->outwarddelivery->reviewerList);
        $this->view->secondlineReviewer       = $this->loadModel('review')->getLastPendingPeople('modify',$id, $modify->version, $secondlineStage+1);
        //授权管理转换待处理人
        $modify->dealUser = $this->loadModel('common')->getAuthorizer('modify', $modify->dealUser, $modify->status, $this->lang->modify->authorizeStatusList);

        $this->view->modify                   = $modify;
        $this->view->isDiskDeliveryable       = 'jxsynfailed' == $modify->status && in_array($this->app->user->account,$secondLineReviewList);
        //(外部)项目/任务
        $this->view->outsideProjectList =  array('' => '') + $this->loadModel('outsideplan')->getPairs();

        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: review
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:45
     * Desc: This is the code comment. This method is called review.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $modifyID
     * @param int $version
     * @param int $reviewStage
     */
    public function review($modifyID = 0, $version = 1, $reviewStage = 0)
    {
        $modify = $this->loadModel('modify')->getByID($modifyID);
        $release = explode(',', trim($modify->release,','));
        $modify->checkSystemPass = $this->loadModel('build')->checkSystemPass($release);
        //检查是否允许审核
        $res = $this->loadModel('modify')->checkAllowReview($modify, $version, $reviewStage, $this->app->user->account);
        if($res['result']){
            $this->loadModel('demand')->isSingleUsage($modify->demandId, 'modify', $modifyID);
            if(dao::isError()){
                $res = ['result' => false, 'message' => implode('<br />', dao::getError())];
            }
        }
        $this->view->users   = $this->loadModel('user')->getPairs('noclosed|noletter');
        if($_POST)
        {
            if($reviewStage == 0){
                $this->modify->link($modifyID);

                if(dao::isError())
                {
                    $response['result']  = 'fail';
                    $response['message'] = dao::getError();
                    $this->send($response);
                }


                if($res['reviewAuthorize'] == $this->app->user->account){
                    $this->loadModel('action')->create('modify', $modifyID, 'linkrelease', $this->post->comment);
                }else{
                    $authorizeComment = sprintf($this->lang->modify->authorizeComment,zget($this->view->users, $this->app->user->account), zget($this->view->users, $res['reviewAuthorize']));
                    $this->loadModel('action')->create('modify', $modifyID, 'linkrelease', $this->post->comment.'<br>'.$authorizeComment);
                }

                $response['result']  = 'success';
                $response['message'] = $this->lang->saveSuccess;
                $response['locate']  = 'parent';

                $this->send($response);

            }else{
                $modify = $this->modify->getByID($modifyID);
                $this->modify->review($modifyID);

                if(dao::isError())
                {
                    $response['result']  = 'fail';
                    $response['message'] = dao::getError();
                    $this->send($response);
                }

                if($res['reviewAuthorize'] == $this->app->user->account){
                    if(!isset($_POST['cancelStatus'])) {
                        $action = 'gmsuccess' == $modify->status || 'cmconfirmed' == $modify->status ? 'deal' : 'review';
                        $this->loadModel('action')->create('modify', $modifyID, $action, $this->post->comment);
                    }else{
                        $this->loadModel('action')->create('modify', $modifyID, 'cancelreview', $this->post->comment);
                    }
                }else{
                    $authorizeComment = sprintf($this->lang->modify->authorizeComment,zget($this->view->users, $this->app->user->account), zget($this->view->users, $res['reviewAuthorize']));
                    if(!isset($_POST['cancelStatus'])) {
                        $action = 'gmsuccess' == $modify->status || 'cmconfirmed' == $modify->status ? 'deal' : 'review';
                        $this->loadModel('action')->create('modify', $modifyID, $action, $this->post->comment.'<br>'.$authorizeComment);
                    }else{
                        $this->loadModel('action')->create('modify', $modifyID, 'cancelreview', $this->post->comment.'<br>'.$authorizeComment);
                    }
                }

                $response['result']  = 'success';
                $response['message'] = $this->lang->saveSuccess;
                $response['locate']  = 'parent';

                $this->send($response);
            }
        }
        $this->view->depts   = $this->loadModel('dept')->getOptionMenu();
        $this->view->title   = $this->lang->modify->edit;
        $this->view->modify  = $modify;
        $this->view->res     = $res;
        $this->view->releases = array('' => '') + $this->loadModel('project')->getReleases($this->view->modify->projectPlanId);
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: link
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:45
     * Desc: This is the code comment. This method is called link.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $modifyID
     * @param int $version
     * @param int $reviewStage
     */
    public function link($modifyID = 0,  $version = 1, $reviewStage = 0)
    {
        if($_POST)
        {
            $this->modify->link($modifyID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $this->loadModel('action')->create('modify', $modifyID, 'linkrelease', $this->post->comment);

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }
        $modify = $this->loadModel('modify')->getByID($modifyID);
        //检查是否允许审核
        $res = $this->loadModel('modify')->checkAllowReview($modify, $version,  $reviewStage, $this->app->user->account);

        $this->view->depts    = $this->loadModel('dept')->getOptionMenu();
        $this->view->title    = $this->lang->modify->edit;
        $this->view->users    = $this->loadModel('user')->getPairs('noclosed');
        $this->view->modify   = $modify;
        $this->view->res      = $res;
        //新增查询，projectplan 表和TABLE_RELEASE 表关联关系
       /* $projectid = $this->dao->select('project')->from(TABLE_PROJECTPLAN)
            ->where('deleted')->eq(0)
            ->andWhere('id')->in($this->view->modify->project)
            ->orderBy('id_desc')
            ->fetchPairs();
        $projectid =  implode(',',$projectid);*/
        $this->view->releases = array('' => '') + $this->loadModel('project')->getReleases($this->view->modify->project);
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: feedback
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:45
     * Desc: This is the code comment. This method is called feedback.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $modifyID
     */
    public function feedback($modifyID)
    {
        if($_POST)
        {
            $changes = $this->modify->feedback($modifyID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes || $this->post->comment)
            {
                $actionID = $this->loadModel('action')->create('modify', $modifyID, 'feedbacked', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $this->view->depts   = $this->loadModel('dept')->getOptionMenu();
        $this->view->title   = $this->lang->modify->edit;
        $this->view->users   = $this->loadModel('user')->getPairs('noclosed');
        $this->view->modify = $this->loadModel('modify')->getByID($modifyID);
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: close
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:45
     * Desc: This is the code comment. This method is called close.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $modifyID
     */
    public function close($modifyID = 0)
    {
        $modify = $this->loadModel('modify')->getByID($modifyID);

        $changeFlag = isset($this->config->changeCloseSwitch) && $this->config->changeCloseSwitch == 1;
        //取消变更开关关闭，执行原来逻辑
        if(!$changeFlag && !empty($modify->externalCode)){
            $this->view->errorMsg = $this->lang->modify->closeNotice;
            $this->display();die;
        }
        //变更单为终态不能取消
        $statusEnd = array_merge(['closed','modifysuccess'], $this->lang->modify->reissueArray);
        if($changeFlag && in_array($modify->status, $statusEnd)){
            $this->view->errorMsg = $this->lang->modify->statusEndNotice;
            $this->display();die;
        }
        //变更单在外部不能取消
        if($changeFlag && !in_array($modify->status, $this->lang->modify->allowRejectArray)){
            $this->view->errorMsg = $this->lang->modify->closeNoticeNew;
            $this->display();die;
        }

        if($_POST)
        {
            $this->modify->close($modifyID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $this->loadModel('action')->create('modify', $modifyID, 'canceled', $this->post->comment);

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }
        $this->view->modify = $modify;
        $this->view->title  = $this->lang->modify->close;
        $this->loadModel('outwarddelivery');
        $this->display();
    }


    /**
     * Project: chengfangjinke
     * Method: close
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:45
     * Desc: This is the code comment. This method is called close.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $modifyID
     */
    public function closeold($modifyID = 0)
    {
        if($_POST)
        {
            $this->modify->closeOld($modifyID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $this->loadModel('action')->create('modify', $modifyID, 'closed', $this->post->comment, $this->post->result);

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }
        $this->loadModel('outwarddelivery');
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: run
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:45
     * Desc: This is the code comment. This method is called run.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $modifyID
     */
    public function run($modifyID = 0)
    {
        if($_POST)
        {
            $this->modify->run($modifyID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $this->loadModel('action')->create('modify', $modifyID, 'runresult',$this->post->comment);

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $this->view->title   = $this->lang->modify->run;
        $this->view->users   = $this->loadModel('user')->getPairs('noclosed');
        $this->view->modify = $this->loadModel('modify')->getByID($modifyID);
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: export
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:45
     * Desc: This is the code comment. This method is called export.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param string $orderBy
     * @param string $browseType
     */
    public function export($orderBy = 'id_desc', $browseType = 'all')
    {
        /* format the fields of every modify in order to export data. */
        if($_POST)
        {
            $this->loadModel('file');
            $this->loadModel('productenroll');
            $modifyLang   = $this->lang->modify;
            $modifyConfig = $this->config->modify;

            /* Create field lists. */
            $fields = $this->post->exportFields ? $this->post->exportFields : explode(',', $modifyConfig->list->exportFields);
            foreach($fields as $key => $fieldName)
            {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = isset($modifyLang->$fieldName) ? $modifyLang->$fieldName : $fieldName;
                unset($fields[$key]);
            }

            /* Get modifys. */
            $modifys = array();
            if($this->session->modifyOnlyCondition)
            {
                $modifys = $this->dao->select('*')->from(TABLE_MODIFY)->where($this->session->modifyQueryCondition)
                    ->beginIF($this->post->exportType == 'selected')->andWhere('id')->in($this->cookie->checkedItem)->fi()
                    ->orderBy($orderBy)->fetchAll('id');
            }
            else
            {
                $stmt = $this->dbh->query($this->session->modifyQueryCondition . ($this->post->exportType == 'selected' ? " AND id IN({$this->cookie->checkedItem})" : '') . " ORDER BY " . strtr($orderBy, '_', ' '));
                while($row = $stmt->fetch()) $modifys[$row->id] = $row;
            }
            
            $modifyIdList = array_keys($modifys);
            $modifysId = array_column($modifys,'id');
            $actions = $this->dao->select("objectType,objectID,action,date")->from(TABLE_ACTION)
                ->where('objectType')->eq('modify')
                ->andWhere('action')->eq('linkrelease')
                ->andWhere('objectID')->in($modifysId)
                ->orderBy("date_asc")
                ->fetchall();
            $modifyConsumed = $this->dao->select('createdDate,objectID')->from(TABLE_CONSUMED)
                ->where('objectType')->eq('modify')
                ->andWhere('after')->eq('modifysuccess')
                ->andWhere('objectID')->in($modifysId)
                ->orderBy('createdDate_asc')
                ->fetchall();
            // 获取待处理人数据集。
            $reviewerList = $this->loadModel('review')->getObjectIdListReviewer($modifyIdList, 'modify');

            /* Get users, products and executions. */
            $depts = $this->loadModel('dept')->getTopPairs();
            $dmap  = $this->dao->select('account,realname,dept')->from(TABLE_USER)->fetchAll('account');
            $users = $this->loadModel('user')->getPairs('noletter');
//            $projects  =  array('0' => '') + $this->loadModel('projectplan')->getAliveProjects(false);
            $projects  =  array('0' => '') + $this->loadModel('projectplan')->getAllProjects();
            $projectsSecondLine  =  array('0' => '') + $this->loadModel('projectplan')->getAliveProjects(true);
//            $projectsSecondLine  =  array('0' => '') + $this->loadModel('projectplan')->getAliveProjects(true);
            $apps  = $this->loadModel('application')->getapplicationNameCodePairsWithisPayment();
            $appList        = array_column($apps, 'name','id');
            $isPaymentList  = array_column($apps, 'isPayment','id');
            $teamList       = array_column($apps, 'team','id');
            $isPaymentPairs = $this->dao->select('`key`,value')->from(TABLE_LANG)->where('module')->eq('application')->andWhere('section')->eq('isPaymentList')->fetchPairs();
            $teamPairs = $this->dao->select('`key`,value')->from(TABLE_LANG)->where('module')->eq('application')->andWhere('section')->eq('teamList')->fetchPairs();
            $requirementList = $this->dao->select('id,concat(concat(concat(code,"（"),name),"）")')->from(TABLE_REQUIREMENT)->where('status')->ne('deleted')->fetchPairs();
            $allProductNames = $this->loadModel('product')->getNamePairs();
            $reviewReportList = array('' => '') + $this->loadModel('review')->getPairs('','');
            foreach($modifys as $modify)
            {
                // $modify->isReviewPass = $modify->isReview==2?zget($modifyLang->isReviewPassList,$modify->isReviewPass):'';
                if($modify->isReview==2){
                    $reviewReport = array();
                    foreach (explode(',',$modify->reviewReport) as $value){
                        $reviewReport[] =  zget($reviewReportList, $value, '');
                    }
                    $modify->reviewReport = implode(',', $reviewReport);
                }
                $modify->isReview = zget($modifyLang->isReviewList,$modify->isReview,'');
                // 产品登记号
                if($modify->productenrollId){
                    // $modify->productenrollId          = $this->loadModel('productenroll')->getByID($modify->productenrollId)->code;
                    $modify->productRegistrationCode = $this->productenroll->getEmisRegisterNumberById($modify->productenrollId)->emisRegisterNumber;
                }else{
                    // $modify->productenrollId          = '';
                    $modify->productRegistrationCode = '';
                }

                
                if($modify->testingRequestId){
                    $modify->testingRequestId          = $this->loadModel('testingrequest')->getByID($modify->testingRequestId)->code;
                }else{
                    $modify->testingRequestId          = '';
                    $this->view->testingrequest = '';
                }

                $apps = array();
                $isPayments = array();
                $teams = array();
                foreach(explode(',',$modify->app) as $app){
                  if(!empty($app)){
                    $apps[] = zget($appList, $app,'');
                    $isPayments[] = $isPaymentPairs[zget($isPaymentList, $app,'')];
                    $teams[] = $teamPairs[zget($teamList, $app,'')];
                  }
                }
                $modify->app  = implode(',', $apps);
                $modify->isPayment = implode(',', $isPayments);
                $modify->team = implode(',', $teams);


                // 获取关联发布。
                if($modify->release) $modify->release = $this->dao->select('name')->from(TABLE_RELEASE)->where('id')->eq($modify->release)->fetch('name');

                // 获取需求单。
                $modify->demandId = trim($modify->demandId, ',');
                if($modify->demandId)
                {
                    $objects = $this->dao->select('id,code')->from(TABLE_DEMAND)->where('id')->in($modify->demandId)->fetchPairs();
                    $as = array();
                    foreach($objects as $object)
                    {
                        if(!$object) continue;
                        $as[] = $object;
                    }
                    $modify->demandId = implode(',', $as);
                }

                // 获取问题单。
                $modify->problemId = trim($modify->problemId, ',');
                if($modify->problemId)
                {
                    $objects = $this->dao->select('id,code')->from(TABLE_PROBLEM)->where('id')->in($modify->problemId)->fetchPairs();
                    $as = array();
                    foreach($objects as $object)
                    {
                        if(!$object) continue;
                        $as[] = $object;
                    }
                    $modify->problemId = implode(',', $as);
                }
                $modify->secondorderId = trim($modify->secondorderId, ',');
                if($modify->secondorderId)
                {
                    $objects = $this->dao->select('id,code')->from(TABLE_SECONDORDER)->where('id')->in($modify->secondorderId)->fetchPairs();
                    $as = array();
                    foreach($objects as $object)
                    {
                        if(!$object) continue;
                        $as[] = $object;
                    }
                    $modify->secondorderId = implode(',', $as);
                }

                $modify->requirementId = trim($modify->requirementId, ',');
                if($modify->requirementId)
                {
                    $as = array();
                    foreach(explode(',', $modify->requirementId)  as $item)
                    {
                        if(!$item) continue;
                        $as[] =$requirementList[$item];
                    }
                    $modify->requirementId = implode(',', $as);
                }

                $modify->feasibilityAnalysis = trim($modify->feasibilityAnalysis, ',');
                if($modify->feasibilityAnalysis)
                {
                    $as = array();
                    foreach(explode(',', $modify->feasibilityAnalysis)  as $item)
                    {
                        if(!$item) continue;
                        $as[] =$modifyLang->feasibilityAnalysisList[$item];
                    }
                    $modify->feasibilityAnalysis = implode(',', $as);
                }

                $modify->node = trim($modify->node, ',');
                if($modify->node)
                {
                    $as = array();
                    foreach(explode(',', $modify->node)  as $item)
                    {
                        if(!$item) continue;
                        $as[] =$modifyLang->nodeList[$item];
                    }
                    $modify->node = implode(',', $as);
                }
                                
                if ($modify->riskAnalysisEmergencyHandle){
                    $modify->riskAnalysisEmergencyHandle = json_decode($modify->riskAnalysisEmergencyHandle);
                    $num=1;
                    $ERmsg='';
                    foreach ($modify->riskAnalysisEmergencyHandle as $ER){
                        $ERmsg=$ERmsg.$num.'、【'.$modifyLang->riskAnalysis.'】'.$ER->riskAnalysis.';';
                        $ERmsg=$ERmsg.' 【'.$modifyLang->emergencyBackWay.'】'.$ER->emergencyBackWay."\r\n";
                        $num=$num+1;
                    }
                    $modify->riskAnalysisEmergencyHandle=$ERmsg;
                }else{ // 历史数据
                    $modify->riskAnalysisEmergencyHandle = $modify->plan;
                }
                $modify->launchDate = "";
                if ($modify->status == 'modifysuccess'){
                    foreach ($modifyConsumed as $ck=>$cv) {
                        if ($modify->id == $cv->objectID){
                            $modify->launchDate = date("Y-m-d",strtotime($cv->createdDate));
                        }
                    }
                }
                
                $modify->operationType = $modifyLang->operationTypeList[$modify->operationType];
                $modify->level    = $modifyLang->levelList[$modify->level];
                $modify->jxLevel    = $modifyLang->levelJxList[$modify->jxLevel];
                $modify->status   = $modifyLang->statusList[$modify->status];
                $modify->type     = $modifyLang->typeList[$modify->type];
                $modify->mode     = $modifyLang->modeList[$modify->mode];
                $modify->classify = $modifyLang->classifyList[$modify->classify];
                $modify->changeSource = $modifyLang->changeSourceList[$modify->changeSource];
                $modify->changeStage = $modifyLang->changeStageList[$modify->changeStage];
                $modify->implementModality  = $modifyLang->implementModalityList[$modify->implementModality];
                $modify->isBusinessCooperate  = $modifyLang->isBusinessCooperateList[$modify->isBusinessCooperate];
                $modify->isBusinessJudge  = $modifyLang->isBusinessJudgeList[$modify->isBusinessJudge];
                $modify->isBusinessAffect  = $modifyLang->isBusinessAffectList[$modify->isBusinessAffect];
                $modify->ifMediumChanges  = $modifyLang->ifMediumChangesList[$modify->ifMediumChanges];
                $modify->changeStatus  = $modifyLang->changeStatusList[$modify->changeStatus];
                $modify->cooperateDepNameList  = $modifyLang->cooperateDepNameListList[$modify->cooperateDepNameList];
                $modify->judgeDep  = $modifyLang->judgeDepList[$modify->judgeDep];
                $modify->property = $modifyLang->propertyList[$modify->property];
                $modify->createdDate  = substr($modify->createdDate,0, 10);//创建时间
                $modify->editedDate  = substr($modify->editedDate,0, 10);//编辑时间
                $modify->isDiskDelivery = $modifyLang->isDiskDeliveryList[$modify->isDiskDelivery];

                // 是否中断业务和是否后补流程。
                $modify->isInterrupt = $modifyLang->interruptList[$modify->isInterrupt];
                $modify->isAppend    = $modifyLang->appendList[$modify->isAppend];

                //迭代二十六-删除部门第一个'/'
                $modify->createdDept = ltrim(zget($depts, $modify->createdDept, ''), '/');
                $modify->createdBy   = zget($users, $modify->createdBy, $modify->createdBy);
                $modify->editedBy    = zget($users, $modify->editedBy, $modify->editedBy);
                $modify->preChange    = empty($modify->preChange)?$this->lang->modify->noChange:strval($modify->preChange);
                $modify->postChange    = empty($modify->postChange)?$this->lang->modify->noChange:strval($modify->postChange);
                $modify->synImplement    = empty($modify->synImplement)?$this->lang->modify->noChange:strval($modify->synImplement);
                $modify->pilotChange    = empty($modify->pilotChange)?$this->lang->modify->noChange:strval($modify->pilotChange);
                $modify->promotionChange    = empty($modify->promotionChange)?$this->lang->modify->noChange:strval($modify->promotionChange);


                // 待处理人处理。
                if(!$modify->dealUser){
                    $modify->dealUser = $this->loadModel('review')->getReviewer('modify', $modify->id, $modify->version, $modify->reviewStage);
                }
                $modify->dealUser = $users[$modify->dealUser];
                if($modify->implementationForm == 'second'){
                    // 处理所属项目。
                    if($modify->projectPlanId)
                    {
                        $as = array();
                        foreach(explode(',', $modify->projectPlanId) as $project)
                        {
                            if(!$project) continue;
                            $as[] = zget($projects, $project);
                        }
                        $modify->projectPlanId = implode(',', $as);
                    }
                }else{
                    // 处理所属项目。
                    if($modify->projectPlanId)
                    {
                        $as = array();
                        foreach(explode(',', $modify->projectPlanId) as $project)
                        {
                            if(!$project) continue;
                            $as[] = zget($projects, $project);
                        }
                        $modify->projectPlanId = implode(',', $as);
                    }
                }
                $productName = "";
                if ($modify->productId) {
                    foreach (explode(',', $modify->productId) as $productID) {
                        if ($productID) {
                            $productName .= $allProductNames[$productID].',';
                        }
                    }
                }
                //退回原因
                $revertReason       = '';
                $revertReasonChild  = '';
                if(!empty($modify->revertReason)) {
                    $childTypeList = json_decode($this->lang->modify->childTypeList['all'],true);
                    $childType = [];
                    foreach ($childTypeList as $k=>$v){
                        $childType += $v;
                    }

                    foreach (json_decode($modify->revertReason,true) as $index => $item) {
                        if (!empty($item)) {
                            $revertReason       .= $item['RevertDate'] .':'. zget($this->lang->modify->revertReasonList, $item['RevertReason']).PHP_EOL;
                            if (isset($childType[$item['RevertReasonChild']])){
                                $revertReasonChild  .= $item['RevertDate'] .':'. $childType[$item['RevertReasonChild']].PHP_EOL;
                            }
                        }
                    }
                }
                $modify->revertReason                      = $revertReason;
                $modify->revertReasonChild                 = $revertReasonChild;
                $modify->implementationForm = $modifyLang->implementationFormList[$modify->implementationForm];
                $systemEnAbbreviation = explode('_',$modify->app);
                $productInfoCodeArray = explode('-',$modify->productInfoCode);
                $modify->systemEnAbbreviation = $systemEnAbbreviation[0];
                $modify->versionNum = $productInfoCodeArray[4];
                $modify->productName = rtrim($productName,',');
                $modify->researchDept = $modify->createdDept;
                $modify->projectManager   = $modify->createdBy;
                $modify->releaseDate      = "";
                $modify->involveDatabase  = zget($this->lang->modify->materialIsReviewList,$modify->involveDatabase,'');

                // 手否后补流程、实际交付时间
                $modify->actualDeliveryTime = $modify->isMakeAmends == 'yes' ? $modify->actualDeliveryTime : '';
                $modify->isMakeAmends = zget($this->lang->modify->isMakeAmendsList,$modify->isMakeAmends,'');
                foreach ($actions as $action) {
                    if ($action->objectID == $modify->id){
                        $modify->releaseDate = date("Y-m-d",strtotime($action->date));
                    }
                }
                if ($modify->realStartTime != '0000-00-00 00:00:00') $modify->actualBegin = $modify->realStartTime;
                if ($modify->realEndTime != '0000-00-00 00:00:00') $modify->actualEnd = $modify->realEndTime;
            }
            $this->post->set('fields', $fields);
            $this->post->set('rows', $modifys);
            $this->post->set('kind', 'modify');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        $this->view->fileName        = $this->lang->modify->exportName;
        $this->view->allExportFields = $this->config->modify->list->exportFields;
        $this->view->customExport    = true;
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: delete
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:45
     * Desc: This is the code comment. This method is called delete.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $modifyID
     */
    public function delete($modifyID)
    {
        if(!empty($_POST))
        {
            $this->dao->update(TABLE_MODIFY)->set('status')->eq('deleted')->where('id')->eq($modifyID)->exec();
            //解绑关联的异常变更，以让旧单子继续被新单子关联
            $findInSet = '(FIND_IN_SET("'.$modifyID.'",abnormalCode))';
            $oldInfo = $this->dao->select("id,abnormalCode")->from(TABLE_MODIFY)->where($findInSet)->fetch();
            if ($oldInfo->abnormalCode != ''){
                $arr = array_flip(explode(',',$oldInfo->abnormalCode));
                unset($arr[$modifyID]);
                $arr = array_flip(array_unique($arr));
                $str = implode(',',$arr);
                $this->dao->update(TABLE_MODIFY)->set('abnormalCode="'.$str.'"')->where('id')->eq($oldInfo->id)->exec();
            }
            $actionID = $this->loadModel('action')->create('modify', $modifyID, 'deleted', $this->post->comment);

            //2022.4.21 tangfei 删除与问题需求的关联关系
            $sql = "delete from zt_secondline where (objectType='demand'or objectType='problem') and relationID=$modifyID  and relationType='modify';";
            $this->dao->query($sql);

            //更新需求和问题解决时间
             $modify = $this->modify->getByID($modifyID);
//            /** @var problemModel $problemModel */
//            $problemModel = $this->loadModel('problem');
//            if(!empty($modify->demandId)){
//                $demandIds =array_filter(explode(',',$modify->demandId));
//                if($demandIds){
//                    foreach($demandIds as $demandId)
//                    {
//                      $problemModel->getAllSecondSolveTime($demandId,'demand');
//                    }
//                }
//            }
            /*if(!empty($modify->problemId)){
                $problemIds =array_filter(explode(',',$modify->problemId));
                if($problemIds){
                    foreach($problemIds as $problemId)
                    {
                       $problemModel->getAllSecondSolveTime($problemId,'problem');
                    }
                }
            }*/
            $backUrl =  $this->session->modifyList ? $this->session->modifyList : inLink('browse');
            if(isonlybody()) die(js::closeModal('parent.parent', $backUrl));
            die(js::reload('parent'));
        }

        $modify = $this->modify->getByID($modifyID);
        $this->view->actions = $this->loadModel('action')->getList('modify', $modifyID);
        $this->view->modify = $modify;
        $this->view->users   = $this->loadModel('user')->getPairs();
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: fix
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:45
     * Desc: This is the code comment. This method is called fix.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     */
    public function fix()
    {
        return;

        //创建部门
        $umap = array();

        $users = $this->dao->select('*')->from(TABLE_USER)->fetchAll();
        foreach($users as $user)
        {
            $umap[$user->account] = $user->dept;
        }
        $modifies = $this->dao->select('*')->from(TABLE_MODIFY)->fetchAll();
        foreach($modifies as $m)
        {
            $this->dao->update(TABLE_MODIFY)->set('createdDept')->eq($umap[$m->createdBy])->where('id')->eq($m->id)->exec();
        }
        $infos = $this->dao->select('*')->from(TABLE_INFO)->fetchAll();
        foreach($infos as $m)
        {
            $this->dao->update(TABLE_INFO)->set('createdDept')->eq($umap[$m->createdBy])->where('id')->eq($m->id)->exec();
        }
        $problems = $this->dao->select('*')->from(TABLE_PROBLEM)->fetchAll();
        foreach($problems as $m)
        {
            $this->dao->update(TABLE_PROBLEM)->set('createdDept')->eq($umap[$m->createdBy])->where('id')->eq($m->id)->exec();
        }
        $demands = $this->dao->select('*')->from(TABLE_DEMAND)->fetchAll();
        foreach($demands as $m)
        {
            $this->dao->update(TABLE_DEMAND)->set('createdDept')->eq($umap[$m->createdBy])->where('id')->eq($m->id)->exec();
        }

        echo "<p>创建部门修改完成</p>";

        //添加产品经理节点
        $nodes = $this->dao->select('*')->from(TABLE_REVIEWNODE)->where('objectType')->in('modify,info')->orderBy('id')->fetchAll();
        $map = array();
        foreach($nodes as $node)
        {
            if(!isset($map[$node->objectType . '-' . $node->objectID . '-' . $node->version])) $map[$node->objectType . '-' . $node->objectID . '-' . $node->version] = array();
            $map[$node->objectType . '-' . $node->objectID . '-' . $node->version][] = $node;
        }

        foreach($map as $ns)
        {
            $stage = 0;
            $reviewStage = 0;
            $type = $ns[0]->objectType;
            $oid  = $ns[0]->objectID;
            foreach($ns as $key => $n)
            {
                $stage++;
                $this->dao->update(TABLE_REVIEWNODE)->set('stage')->eq($stage)->where('id')->eq($n->id)->exec();
                if($n->status == 'pending') $reviewStage = $stage;

                if($key == 2)
                {
                    $stage++;

                    $data = new stdclass();
                    $data->status = ($n->status == 'wait' or $n->status == 'pending') ? 'wait' : 'ignore';
                    $data->objectType  = $n->objectType;
                    $data->objectID    = $n->objectID;
                    $data->stage       = $stage;
                    $data->createdBy   = $n->createdBy;
                    $data->createdDate = $n->createdDate;
                    $data->version     = $n->version;
                    $this->dao->insert(TABLE_REVIEWNODE)->data($data)->exec();

                    $insertID = $this->dao->lastInsertID();
                    $r = new stdclass();
                    $r->node        = $insertID;
                    $r->reviewer    = '';
                    $r->status      = $data->status;
                    $r->createdBy   = $n->createdBy;
                    $r->createdDate = $n->createdDate;
                    $this->dao->insert(TABLE_REVIEWER)->data($r)->exec();
                }
            }
            if($reviewStage != 0)
            {
                if($type == 'modify') $this->dao->update(TABLE_MODIFY)->set('reviewStage')->eq($reviewStage-1)->where('id')->eq($oid)->exec();
                if($type == 'info') $this->dao->update(TABLE_INFO)->set('reviewStage')->eq($reviewStage-1)->where('id')->eq($oid)->exec();
            }
        }
        echo '产品经理节点添加完成';
    }

    /**
     * Project: chengfangjinke
     * Method: exportWord
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:45
     * Desc: This is the code comment. This method is called exportWord.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $modifyID
     */
    public function exportWord($modifyID)
    {
        $modify = $this->modify->getByID($modifyID);
        $users  = $this->loadModel('user')->getPairs('noletter');
        $reviewReportList = array('' => '') + $this->loadModel('review')->getPairs('','');
        $this->app->loadLang('modifycncc');
        $this->app->loadLang('outwarddelivery');

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

        $section->addTitle($this->lang->modify->exportTitle, 1);
        $section->addText($this->lang->modify->code . ' ' . $modify->code, 'font_default', 'align_right');

        $tableStyle = array(
            'unit' => \PhpOffice\PhpWord\Style\Table::WIDTH_PERCENT,
            'width' => 100 * 50,
            'cellMargin' => 50,
            'borderSize' => 10,
            'borderColor' => '000000',
        );
        $cellStyle1  =  array('gridSpan' => 1);
        $cellStyle2 =  array('gridSpan' => 2);
        $cellStyle3 =  array('gridSpan' => 3);
        $cellStyle4 =  array('gridSpan' => 4);
        $cellStyle6 =  array('gridSpan' => 6);
        $table = $section->addTable($tableStyle);
        $table->addRow();
        $table->addCell(1000, $cellStyle1)->addText($this->lang->modifycncc->level);
        $table->addCell(2000, $cellStyle2)->addText(zget($this->lang->modifycncc->levelList, $modify->level, ''));
        $table->addCell(1000, $cellStyle1)->addText($this->lang->modifycncc->type);
        $table->addCell(2000, $cellStyle2)->addText(zget($this->lang->modifycncc->typeList, $modify->type, ''));

        $table->addRow();
        $table->addCell(1000, $cellStyle1)->addText($this->lang->modifycncc->mode);
        $table->addCell(2000, $cellStyle2)->addText(zget($this->lang->modifycncc->modeList, $modify->mode, ''));
        $table->addCell(1000, $cellStyle1)->addText($this->lang->modifycncc->status);
        $table->addCell(2000, $cellStyle2)->addText(zget($this->lang->modify->statusList, $modify->status, ''));

        $projects  =  array('0' => '') + $this->loadModel('projectplan')->getProject($modify->implementationForm == 'second');//更新获取所属项目的方法
        $ps = array();
        foreach(explode(',', $modify->projectPlanId) as $project)
        {
            $ps[] = zget($projects, $project, '');
        }
        $modify->problemId = trim($modify->problemId, ',');
        $modify->demandId = trim($modify->demandId, ',');
        if(!empty($modify->problemId)) $modify->problemId = $this->dao->select("group_concat(`code`) as code")->from(TABLE_PROBLEM)->where('id')->in($modify->problemId)->fetch('code');
        if(!empty($modify->demandId))  $modify->demandId  = $this->dao->select("group_concat(`code`) as code")->from(TABLE_DEMAND)->where('id')->in($modify->demandId)->fetch('code');

        $changeNodes = [];
        foreach (explode(',',$modify->node) as $node)
        {
            if(empty($node)) continue;
            $changeNodes [] = zget($this->lang->modify->nodeList, $node, '') ;
        }

        $table->addRow();
        $table->addCell(1000, $cellStyle1)->addText($this->lang->modifycncc->node);
        $table->addCell(2000, $cellStyle2)->addText(implode(',', $changeNodes));
        $table->addCell(1000, $cellStyle1)->addText($this->lang->modifycncc->project);
        $table->addCell(2000, $cellStyle2)->addText(implode(',', $ps));

        $table->addRow();
        $table->addCell(1000, $cellStyle1)->addText($this->lang->modifycncc->planBegin);
        $table->addCell(2000, $cellStyle2)->addText($modify->planBegin);
        $table->addCell(1000, $cellStyle1)->addText($this->lang->modifycncc->planEnd);
        $table->addCell(2000, $cellStyle2)->addText($modify->planEnd);

        $table->addRow();
        $table->addCell(1000, $cellStyle1)->addText($this->lang->modifycncc->actualBegin);
        $table->addCell(2000, $cellStyle2)->addText($modify->realStartTime);
        $table->addCell(1000, $cellStyle1)->addText($this->lang->modifycncc->actualEnd);
        $table->addCell(2000, $cellStyle2)->addText($modify->realEndTime);

        if($modify->testingRequestId){
            $trMsg     = $this->loadModel('testingrequest')->getCodePairs()[$modify->testingRequestId];
            $giteeId   = $this->testingrequest->getOutercode($modify->testingRequestId)->giteeId;
            $trMsg     = $giteeId ? $trMsg . '（' . $giteeId . '）' : $trMsg;
        }
        // if($modify->productenrollId){
        //     $peMsg    = $this->loadModel('productenroll')->getCodePairs()[$modify->productenrollId];
        //     $giteeId  = $this->productenroll->getOutercode($modify->productenrollId)->giteeId;
        //     $peMsg = $giteeId ? $peMsg . '（' . $giteeId . '）' : $peMsg;
        // }

        $table->addRow();
        $table->addCell(1000, $cellStyle1)->addText($this->lang->modify->jxLevel);
        $table->addCell(2000, $cellStyle2)->addText(zget($this->lang->modify->jxLevelList, $modify->jxLevel, ''));
        $table->addCell(1000, $cellStyle1)->addText($this->lang->modifycncc->relatedTestingRequest);
        $table->addCell(2000, $cellStyle2)->addText(isset($trMsg) ? $trMsg : '');
        // $table->addCell(1000, $cellStyle1)->addText($this->lang->modifycncc->relatedProductEnroll);
        // $table->addCell(2000, $cellStyle2)->addText($peMsg?$peMsg:'');

        /*
        $ps = array();
        if($modifycncc->problem)
        {
            $problemList = explode(',', $modifycncc->problem);
            foreach($problemList as $p) $ps[] = $problems[$p];
        }
         */

        $table->addRow();
        $table->addCell(1000, $cellStyle1)->addText($this->lang->modifycncc->problem);
        //$table->addCell(1000, $cellStyle1)->addText(implode(',', $ps));
        $table->addCell(2000, $cellStyle2)->addText($modify->problemId);

        $demands = $this->loadModel('demand')->getPairs('noclosed');
        $modify->demandId = trim($modify->demandId, ',');
        /*
        $ds = array();
        if($modifycncc->demand)
        {
            $demandList = explode(',', $modifycncc->demand);
            foreach($demandList as $d) $ds[] = $demands[$d];
        }
         */

        $table->addCell(1000, $cellStyle1)->addText($this->lang->modifycncc->demand);
        //$table->addCell(1000, $cellStyle1)->addText(implode(',', $ds));
        $table->addCell(2000, $cellStyle2)->addText($modify->demandId);


        $table->addRow();
        $table->addCell(1000, $cellStyle1)->addText($this->lang->modifycncc->createdBy);
        $table->addCell(2000, $cellStyle2)->addText(zget($users, $modify->createdBy, ''));
        $table->addCell(1000, $cellStyle1)->addText($this->lang->modifycncc->createdDate);
        $table->addCell(2000, $cellStyle2)->addText($modify->createdDate);

        $table->addRow();
        $table->addCell(1000, $cellStyle1)->addText($this->lang->modify->isReview);
        $table->addCell(2000, $cellStyle2)->addText(zget($this->lang->modify->isReviewList,$modify->isReview,''));

        if($modify->isReview==2){
            // $table->addCell(1000, $cellStyle1)->addText($this->lang->modify->isReviewPass);
            // $table->addCell(1000, $cellStyle2)->addText(zget($this->lang->modify->isReviewPassList,$modify->isReviewPass));
            $table->addCell(1000, $cellStyle1)->addText($this->lang->modify->reviewReport);
            $reviewReport = array();
            foreach (explode(',',$modify->reviewReport) as $value){
                $reviewReport[] =  zget($reviewReportList, $value, '');
            }
            $table->addCell(2000, $cellStyle2)->addText(implode(',', $reviewReport));
        }else{
            $table->addCell(1000, $cellStyle1)->addText('');
            $table->addCell(2000, $cellStyle2)->addText('');
        }

        $partitionMsg='';
        foreach($modify->appsInfo as $appID=>$appInfo)
        {
            $partitionMsg.=$appInfo->name;
            $partitionMsg.='<w:br />';
        }

        $table->addRow();
        $table->addCell(2000, $cellStyle2)->addText($this->lang->modifycncc->app);
        $table->addCell(4000, $cellStyle4)->addText($partitionMsg);

        $secondorder = $this->loadModel('secondorder')->getPairsByIds(explode(',', $modify->secondorderId));
        $secondorderCode = '';
        foreach (explode(',', $modify->secondorderId) as $secondorderId){
            if ($secondorderId and $secondorder->$secondorderId['code']) {
                if($secondorderCode==''){
                    $secondorderCode = $secondorder->$secondorderId['code'];
                }else{
                    $secondorderCode = $secondorderCode.'，'.$secondorder->$secondorderId['code'];
                }
            }
        }

        $table->addRow();
        $table->addCell(2000, $cellStyle2)->addText($this->lang->modify->secondorderId);
        $table->addCell(2000, $cellStyle4)->addText($secondorderCode);

        $table->addRow();
        $table->addCell(2000, $cellStyle2)->addText($this->lang->modifycncc->productCode);
        $productInfoCode = str_replace("\n","<w:br/>", implode('<w:br/>', explode(',', $modify->productInfoCode)));
        $table->addCell(4000, $cellStyle4)->addText($productInfoCode);

        $table->addRow();
        $table->addCell(2000, $cellStyle2)->addText($this->lang->modifycncc->desc);
        $table->addCell(4000, $cellStyle4)->addText(strip_tags($modify->desc));

        $table->addRow();
        $table->addCell(2000, $cellStyle2)->addText($this->lang->modifycncc->reason);
        $table->addCell(4000, $cellStyle4)->addText(strip_tags($modify->reason));

        $table->addRow();
        $table->addCell(2000, $cellStyle2)->addText($this->lang->modifycncc->target);
        $table->addCell(4000, $cellStyle4)->addText(strip_tags($modify->target));

        $table->addRow();
        $table->addCell(2000, $cellStyle2)->addText($this->lang->modifycncc->feasibilityAnalysis);
        if (!empty($modify->feasibilityAnalysis)) {
            $feasibilityAnalysisInfo = array();
            $feasibilityAnalysises = explode(',', $modify->feasibilityAnalysis);
            foreach ($feasibilityAnalysises as $feasibilityAnalysis) {
                $feasibilityAnalysisInfo[] = zget($this->lang->modifycncc->feasibilityAnalysisList, $feasibilityAnalysis, '');
            }
            $table->addCell(4000, $cellStyle4)->addText(trim(implode(',', $feasibilityAnalysisInfo),','));
        }
        else{
            $table->addCell(4000, $cellStyle4)->addText('');
        }

        $table->addRow();
        $table->addCell(2000, $cellStyle2)->addText($this->lang->modifycncc->risk);
        $table->addCell(4000, $cellStyle4)->addText(strip_tags($modify->risk));

        $table->addRow();
        $table->addCell(2000, $cellStyle2)->addText($this->lang->modifycncc->step);
        $table->addCell(4000, $cellStyle4)->addText(strip_tags($modify->step));

        $table->addRow();
        $table->addCell(2000, $cellStyle2)->addText($this->lang->modifycncc->effect);
        $table->addCell(4000, $cellStyle4)->addText(strip_tags($modify->effect));

        $table->addRow();
        $table->addCell(2000, $cellStyle2)->addText($this->lang->modifycncc->checkList);
        $table->addCell(4000, $cellStyle4)->addText(strip_tags($modify->checkList));

        $table->addRow();
        $table->addCell(2000, $cellStyle2)->addText($this->lang->modifycncc->result);
        $table->addCell(4000, $cellStyle4)->addText(zget($this->lang->modifycncc->resultList, $modify->result, ''));

        $table->addRow();
        $table->addCell(2000, $cellStyle2)->addText($this->lang->modify->implementationForm);
        $table->addCell(4000, $cellStyle4)->addText(zget($this->lang->modify->implementationFormList, $modify->implementationForm, ''));

        /* guchaonan 添加风险分析与应急处置字段 */
        $table->addRow();
        $table->addCell(6000, $cellStyle6)->addText($this->lang->modifycncc->riskAnalysisEmergencyHandle, 'font_bold', array('align' => 'center'));

        $table->addRow();
        $table->addCell(3000,  $cellStyle3)->addText($this->lang->modifycncc->riskAnalysis);
        $table->addCell(3000,  $cellStyle3)->addText($this->lang->modifycncc->emergencyBackWay);

        if ($modify->riskAnalysisEmergencyHandle){
            foreach ($modify->riskAnalysisEmergencyHandle as $ER){
                $table->addRow();
                $table->addCell(3000,  $cellStyle3)->addText($ER->riskAnalysis);
                $table->addCell(3000,  $cellStyle3)->addText($ER->emergencyBackWay);
            }
        }

        /* Review. */
        $table->addRow();
        $table->addCell(6000, $cellStyle6)->addText($this->lang->modifycncc->reviewComment, 'font_bold', array('align' => 'center'));

        $table->addRow();
        $table->addCell(1000, $cellStyle1)->addText($this->lang->modifycncc->reviewNode);
        $table->addCell(1000, $cellStyle1)->addText($this->lang->modifycncc->reviewer);
        $table->addCell(1000, $cellStyle1)->addText($this->lang->modifycncc->reviewResult);
        $table->addCell(1000, $cellStyle1)->addText($this->lang->modifycncc->reviewComment);
        $table->addCell(1000, $cellStyle1)->addText($this->lang->modifycncc->reviewTime);

        $nodes = $this->loadModel('review')->getNodes('modify', $modifyID, $modify->version);
        foreach($nodes as $key => $node)
        {
            # if($key == 0) continue;
            if($node->status == 'ignore') continue;
            //跳过系统部节点
            if ($modify->createdDate > "2024-04-02 23:59:59" && $key == 3){
                continue;
            }
            $reviewers = [];
            if(is_array($node->reviewers) && !empty($node->reviewers)){
                $reviewers = array_column($node->reviewers, 'reviewer');
            }
            $reviewers = $this->loadModel('common')->getAuthorizer('modify', implode(',', $reviewers), $this->lang->modify->reviewBeforeStatusList[$key], $this->lang->modify->authorizeStatusList);
            $reviewers = explode(',', $reviewers);

            //所有审核人
            $reviewerUsers    = getArrayValuesByKeys($users, $reviewers);

            $reviewerUsersStr = implode(',', $reviewerUsers);

            $realReviewerInfo = $this->loadModel('review')->getRealReviewerInfo($node->status, $node->reviewers);

            $realReviewerInfo->reviewerUserName = '';
            if(isset($realReviewerInfo->reviewer) and !empty($realReviewerInfo->reviewer)){
                $extra = json_decode($realReviewerInfo->extra);
                if(!empty($extra->proxy)){
                    $realReviewerInfo->reviewerUserName = '（'.zget($users, $extra->proxy)."处理【".zget($users, $realReviewerInfo->reviewer)."授权】".'）';
                }else{
                    $realReviewerInfo->reviewerUserName = '（'.zget($users, $realReviewerInfo->reviewer).'）';
                }

            }

            if ($key==4 and (! in_array($realReviewerInfo->status,['pass','reject']))) { continue; }

            $table->addRow();
            $table->addCell(1000, $cellStyle1)->addText(zget($this->lang->modifycncc->reviewNodeList, $key));
            $table->addCell(1000, $cellStyle1)->addText($reviewerUsersStr);
            $table->addCell(1000, $cellStyle1)->addText(zget($this->lang->modifycncc->confirmResultList, $realReviewerInfo->status, '') . $realReviewerInfo->reviewerUserName);
            $table->addCell(1000, $cellStyle1)->addText(strip_tags($realReviewerInfo->comment));
            $table->addCell(1000, $cellStyle1)->addText($realReviewerInfo->reviewTime);
        }

        //外部审批信息
        $table->addRow();
        $table->addCell(1000, $cellStyle1)->addText($this->lang->outwarddelivery->outerReviewNodeList['4']);
        $table->addCell(1000, $cellStyle1)->addText(zget($users,'guestjk',','));

        if(in_array($modify->status,array('waitqingzong','jxsynfailed'))){
            $table->addCell(1000, $cellStyle1)->addText(zget($this->lang->modify->statusList, $modify->status, ''));
        }
        elseif(in_array($modify->status,array('withexternalapproval', 'modifyfail', 'modifysuccesspart', 'modifysuccess', 'modifyreject', 'modifyrollback','modifycancel','giteepass','jxSubmitImplement'))){
            $table->addCell(1000, $cellStyle1)->addText($this->lang->modify->synSuccess);
        }
        else{
            $table->addCell(1000, $cellStyle1)->addText('');
        }

        $MClog      = $this->modify->getRequestLog($modifyID);
        if(in_array($modify->status, array('withexternalapproval', 'modifyfail', 'modifysuccesspart', 'modifysuccess', 'modifyreject', 'modifyrollback','modifycancel','giteepass','jxSubmitImplement'))){
            $table->addCell(2000, $cellStyle1)->addText('生产变更单同步成功');
        }
        elseif(in_array($modify->status, array('waitqingzong', 'jxsynfailed'))){
            $table->addCell(2000, $cellStyle1)->addText($modify->pushFailReason);
        }
        else{
            $MClog = new stdclass();
            $MClog->requestDate='';
            $table->addCell(2000, $cellStyle1)->addText('');
        }
        $table->addCell(1000, $cellStyle1)->addText($MClog->requestDate);

        $table->addRow();
        $table->addCell(1000, $cellStyle1)->addText($this->lang->outwarddelivery->outerReviewNodeList['5']);
        $table->addCell(1000, $cellStyle1)->addText(zget($users,'guestjx',','));
        if(in_array($modify->status,array('withexternalapproval', 'modifyfail', 'modifysuccesspart', 'modifysuccess', 'modifyreject', 'modifyrollback','modifycancel','giteepass','jxSubmitImplement'))){
            $table->addCell(1000, $cellStyle1)->addText(zget($this->lang->modify->statusList,$modify->status));
        }
        else{
            $table->addCell(1000, $cellStyle1)->addText('');
        }

        if($modify->returnReason){
            $table->addCell(2000, $cellStyle1)->addText($modify->returnReason);
        }
        else{
            $table->addCell(2000, $cellStyle1)->addText('');
        }

        if(strtotime($modify->changeDate)>0){
            $table->addCell(1000, $cellStyle1)->addText($modify->changeDate);
        }
        else {
            $table->addCell(1000, $cellStyle1)->addText('');
        }

        /* Consumed. */
        $table->addRow();
        $table->addCell(6000, $cellStyle6)->addText($this->lang->consumedTitle, 'font_bold', array('align' => 'center'));

        $table->addRow();
        $table->addCell(2000, $cellStyle2)->addText($this->lang->modifycncc->nodeUser);
        //$table->addCell(1000, $cellStyle1)->addText($this->lang->modifycncc->consumed);
        $table->addCell(1000, $cellStyle1)->addText($this->lang->modifycncc->before);
        $table->addCell(2000, array('gridSpan' => 2))->addText($this->lang->modifycncc->after);

        $modify->consumed = $this->dao->select('*')->from(TABLE_CONSUMED)->where('objectType')->eq('modify') //状态流转 工作量
        ->andWhere('objectID')->eq($modifyID)
            ->andWhere('deleted')->ne(1)
            ->orderBy('id_asc')
            ->fetchAll();

        foreach($modify->consumed as $c)
        {
            $table->addRow();
            $table->addCell(2000, $cellStyle2)->addText(zget($users, $c->account, ''));
           // $table->addCell(1000, $cellStyle1)->addText($c->consumed . '' . $this->lang->hour);
            $table->addCell(1000, $cellStyle1)->addText(zget($this->lang->modify->statusList, $c->before, '-'));
            $table->addCell(2000, array('gridSpan' => 2))->addText(zget($this->lang->modify->statusList, $c->after, '-'));
        }

        $this->loadModel('file')->export2Word($this->lang->modify->exportTitle . $modify->code, $phpWord);
    }

    /**
     * copy a modify.
     * 
     * @param  int $modifyID 
     * @access public
     * @return void
     */
     public function copy($modifyID = 0)
     {
        if($_POST)
        {
            $modifyID = $this->modify->create();

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            if ($_POST['abnormalCode']){
                $modify = $this->modify->getByID($_POST['abnormalCode']);
                $this->post->comment = $this->lang->modify->associaitonOrder.'：'.$modify->code.'<br/>'.$this->post->comment;
            }
            $this->loadModel('action')->create('modify', $modifyID, 'created', $this->post->comment);
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = inlink('browse');
            $response['id']    = $modifyID;
            if ($_POST['issubmit'] == 'submit'){
                $response['message'] = $this->lang->submitSuccess;
                $response['iframeUrl']  = $this->createLink('modify', 'submit', "id=".$modifyID."&linkType=2",'',true);
            }
            $this->send($response);
        }
         $demandLang = $this->app->loadLang('demand')->demand;
        $this->loadModel("modifycncc");
        //获取对外交付信息
        $modify = $this->modify->getByID($modifyID);
        // 手动赋值
        $modify->isNewTestingRequest = 0;
        $modify->isNewProductEnroll = 0;
        $modify->isNewModifycncc = 1;
        if(empty($modify->ROR)){
            $modify->RORList = array();
        }else{
            $modify->RORList = json_decode(json_encode($modify->ROR),true);
        }
        if(!empty($modify->consumed)){
            $modify->consumed = array_pop($modify->consumed)->consumed;
        }

        $this->view->modify   = $modify;
        $this->view->productenroll = new stdclass();
        $this->view->productenroll->isOnly = false;
        $this->view->productenroll->disable = false;
        $this->view->testingrequest = new stdclass();
        $this->view->testingrequest->isOnly = false;
        $this->view->testingrequest->disable = false;
        $this->view->modifycncc = new stdclass();
        $this->view->modifycncc->disable = false;

         //获取未被关联的异常变更单
         $abnormalList = array('' => '') + $this->modify->getModifyAbnormal();
         $this->view->abnormalList = $abnormalList;
        //标题
        $this->view->title = $this->lang->modify->copy;
        //申请测试单键值对
        $this->view->testingrequestList = array('' => '') + $this->loadModel("testingrequest")->getPairs($modifyID);
        //产品等级键值对
        $this->view->productenrollList = array('' => '') + $this->loadModel("productenroll")->getPairs($modifyID);
        //产品线键值对
        $this->view->productlineList = array('' => '') + $this->loadModel('productline')->getPairs();
        //所属系统
        $this->view->appAll  =  $this->loadModel('application')->getApplicationCodePairsSyncJinx();
        $this->view->apps = array('' => '') + array_column($this->view->appAll, 'name', 'id');
        //产品名称
         $app = 0;
         if ($modify->createdDate > "2023-05-31 23:59:59"){
             $app = trim($modify->app,',');
         }
         $this->view->productList   = array('' => '') + $this->loadModel('product')->getProductWithCodeName('noclosed',0,'',$app);
        //产品名称被选中的列
        $this->view->productSelectList   = $this->dao->select('id,name')
                                            ->from(TABLE_PRODUCT)
                                            ->where('deleted')->eq(0)->andwhere('id')->in($modify->productId)
                                            ->fetchPairs('id', 'name');
        //所属项目
        $this->view->projectList       = array('' => '') + $this->loadModel('projectplan')->getAliveProjects(false);
        //关联问题
        $this->view->problemList       = array('' => '') + $this->loadModel('problem')->getPairsAbstract('noclosed');
        //关联二线工单
        $this->view->secondorderList   = array('' => '') + $this->dao->select('id,concat(concat(concat(code,"（"),summary),"）")')->from(TABLE_SECONDORDER)->where('deleted')->ne('1')->fetchPairs();
        //获取问题选中列
        $this->view->problemSelectList     = $this->dao->select("id,concat(code,'（',IFNULL(abstract,''),'）') as code")->from(TABLE_PROBLEM)
                                        ->where('status')->ne('deleted')->andwhere('id')->in($modify->problemId)
                                        ->orderBy('id_desc')
                                        ->fetchPairs();;
        //关联需求
         $this->view->demandList = array('' => '') + $this->loadModel('demand')->modifySelect('modify');
        //关联需求任务
        if(empty($modify->demandId)){
            $requirementList = array('' => '') + $this->dao->select('id,concat(concat(concat(code,"（"),name),"）")')->from(TABLE_REQUIREMENT)->where('status')->ne('deleted')->fetchPairs();
        }else{
            $opinionId = $this->dao->select('distinct opinionID')->from(TABLE_DEMAND)->where('id')->in(explode(',',$modify->demandId))->fetchAll();
            $requirementList = array('' => '') + $this->dao->select('id,concat(concat(concat(code,"（"),name),"）")')->from(TABLE_REQUIREMENT)->where('opinion')->in(array_column($opinionId,'opinionID'))->andWhere('status')->ne('deleted')->fetchpairs();
        }
        $this->view->requirementList   = $requirementList;
        //获取关联需求任务选中列
        $this->view->requirementSelectList     = $this->dao->select('id,concat(concat(concat(code,"（"),name),"）")')->from(TABLE_REQUIREMENT)
            ->where('status')->ne('deleted')->andwhere('id')->in($modify->requirementId)
            ->orderBy('id_desc')
            ->fetchPairs();;
        //所属CBP项目
        $this->view->cbpprojectList     = array('' => '') + $this->dao->select('code,concat(concat(concat(code,"（"),name),"）")')->from(TABLE_CBPPROJECT)->where('deleted')->ne(1)->fetchPairs();
        //产品变更
        $this->view->modifycnccList = array('' => '') + $this->dao->select('id,concat(concat(concat(code,"（"),substring(`desc`,1,25)),"）")')->from(TABLE_MODIFYCNCC)->fetchPairs();
         //(外部)项目/任务
         $this->view->outsideProjectList =  array('' => '') + $this->loadModel('outsideplan')->getPairs();
        //审核节点下的审核人列表
         if($modify->createdDept == $this->app->user->dept){
             $reviewers            = $this->modify->getReviewers($modify->createdDept);
             //审核节点下的审核人列表
             $reviewerAccounts     = $this->loadModel('review')->getReviewerAccounts($reviewers);
             //审核节点下默认设置审核节点人
             $defChosenReviewNodes = $this->config->modifycncc->create->setDefChosenReviewNodes;
             $this->view->reviewers            = $reviewers;
             $this->view->reviewerAccounts     = $reviewerAccounts;
             $this->view->defChosenReviewNodes = $defChosenReviewNodes;
             //审核节点以及审核节点的审核人
             $nodesReviewers = $this->loadModel('review')->getAllNodeReviewers('modify', $modifyID, $modify->version);
             $this->view->nodesReviewers = $nodesReviewers;
         }else{
             $reviewers            = $this->modify->getReviewers();
             //审核节点下的审核人列表
             $reviewerAccounts     = $this->loadModel('review')->getReviewerAccounts($reviewers);
             //审核节点下默认设置审核节点人
             $defChosenReviewNodes = $this->config->modify->create->setDefChosenReviewNodes;
             $this->view->reviewers            = $reviewers;
             $this->view->nodesReviewers       = $reviewerAccounts;
             $this->view->reviewerAccounts     = $reviewerAccounts;
             $this->view->defChosenReviewNodes = $defChosenReviewNodes;
         }
         $this->view->users           = $this->loadModel('user')->getPairs('noletter');

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
        echo html::select('project[]', $project, 0,"class='form-control chosen' multiple");
    }

    /**
     * @param int $modifyID
     * @param int $version
     * @param int $reviewStage
     */
    public function cancel($modifyID = 0,  $version = 1)
    {
        if($_POST)
        {
            $this->modify->cancel($modifyID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $this->loadModel('action')->create('modify', $modifyID, 'cancelchange', $this->post->comment);

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }
        $modify = $this->loadModel('modify')->getByID($modifyID);
        $this->view->modify   = $modify;
        $this->display();
    }

        /**
     * 编辑退回次数
     * @param $testingrequestID
     * @return void
     */
    public function editreturntimes($modifyId = 0){
        if($_POST)
        {
            $this->modify->editreturntimes($modifyId);
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
        $this->view->title             = $this->lang->modify->editreturntimes;
        $this->display();
    }

    /**
     * 同步失败重新推送
     * @param $id
     */
    public function push($id)
    {
        $data['status']         = 'waitqingzong';
        $data['pushStatus']     = 0;
        $data['pushFailTimes']  = 0;
        $data['pushDate']     = "";
        $data['pushFailReason']     = "";
        $data['changeStatus']     = "";
        $data['returnReason']   = "";
        $data['changeDate']   = "";
        $data['dealUser']   = "guestjk";
        $this->dao->update(TABLE_MODIFY)->data($data)->where('id')->eq($id)->exec();
        $response['result']  = 'success';
        $response['message'] = $this->lang->saveSuccess;
        $response['locate']  = inlink('view', 'modifyId='.$id);
        $this->loadModel('action')->create('modify', $id, 'repush', "重新推送");
        /*$this->send($response);*/
        die(js::locate($this->createLink('modify', 'view', "modifyId=$id"), 'parent.parent'));
    }

    /**
     * 同步失败重新推送
     * @param $id
     */
    public function pushCancel($id)
    {
        $data['status']         = 'canceltojx';
        $data['cancelPushStatus']     = 0;
        $data['cancelPushFailTimes']  = 0;
        $data['cancelPushDate']     = "";
        $data['cancelPushFailReason']     = "";
        $data['cancelStatus']     = "";
        $this->dao->update(TABLE_MODIFY)->data($data)->where('id')->eq($id)->exec();
        $response['result']  = 'success';
        $response['message'] = $this->lang->saveSuccess;
        $response['locate']  = 'parent';
        $this->loadModel('action')->create('modify', $id, 'repush', "重新推送");
        $this->send($response);
    }

    /**
     * 编辑外部变更级别
     * shixuyang
     * @param $testingrequestID
     * @return void
     */
    public function editlevel($modifyId = 0){
        if($_POST)
        {
            $this->modify->editlevel($modifyId);
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
        $this->view->modify             = $this->modify->getByID($modifyId);
        $this->view->title             = $this->lang->modify->editlevel;
        $this->display();
    }

    public function isDiskDelivery($modifyId)
    {
        if($_POST)
        {
            $this->modify->isDiskDelivery($modifyId);
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
        $this->view->modify            = $this->modify->getByID($modifyId);
        $this->view->title             = $this->lang->modify->isDiskDelivery;
        $this->display();
    }

    /**
     * @param $products
     * @param $project
     * 获取评审数据
     */
    public function ajaxGetReview($project) {
        $reviewList = array('' => '') + $this->dao->select('id,title')->from(TABLE_REVIEW)
            ->where('project')->eq($project)
            ->andWhere('status')->eq('reviewpass')
            ->andWhere('deleted')->eq(0)
            ->orderBy('id_desc')
            ->fetchPairs();
        die(html::select('reviewReport[]', $reviewList,'', "class='form-control chosen' multiple"));
    }

    /**
     * 同步发布信息到发布表
     * type 不为1时 同步为cm
     */
    public function syncReleaseInfo($type=1){
//        $sql = "select id, `release`, createdBy, createdDate,createdDept from zt_modify zm where 1 and status in ('modifysuccess', 'modifysuccesspart') and releaseSyncStatus = 1 and `release` != '' and createdDate >= '2021-01-01 00:00:00.00'";
//        $data = $this->dao->query($sql)->fetchAll();
        $data = $this->dao->select("id, `code`, `release`, createdBy, createdDate, createdDept, `status`")
            ->from(TABLE_MODIFY)
            ->where("status")->in($this->lang->modify->syncReleaseStatus)
            ->andwhere('releaseSyncStatus')->eq(1)
            ->andwhere('`release`')->ne('')
            ->andwhere('createdDate')->ge('2021-01-01 00:00:00')
            ->fetchAll();
        if(!$data){
            echo '没有数据需要同步';
            exit();
        }
        $releaseIds = [];
        foreach ($data as $key => $val){
            $currentReleaseIds = explode(',', $val->release);
            $val->releaseIds = $currentReleaseIds;
            $data[$key] = $val;
            $releaseIds = array_merge($releaseIds, $currentReleaseIds);
        }

        $select = 'id, status, dealUser, version, syncObjectCreateTime,syncStateTimes,createdBy';
        $releaseList = $this->loadModel('projectrelease')->getValidListByIds($releaseIds, $select,true);
        if(!$releaseList){
            echo '没有数据需要同步';
            exit();
        }
        $releaseList = array_column($releaseList, null, 'id');
        $data        = array_column($data, null, 'id');

        //要操作的发布信息
        $tempReleaseList = [];
        foreach ($data as $val){
            $currentReleaseIds = $val->releaseIds;
            foreach ($currentReleaseIds as $releaseId){
                $releaseInfo = zget($releaseList, $releaseId);
                if(!$releaseInfo){
                    continue;
                }
                $releaseInfo->syncModifyId = $val->id;
                $tempReleaseList[] = $releaseInfo;
            }
        }
        if(!$tempReleaseList){
            echo '没有数据需要同步';
            exit();
        }

        $i = 0;
        $releaseSyncStatus = 2;
        $updateParams = new stdClass();
        $updateParams->releaseSyncStatus = $releaseSyncStatus;
        foreach ($tempReleaseList as $val){
            $syncModifyId = $val->syncModifyId;
            $modifyInfo = zget($data, $syncModifyId);
            if(($modifyInfo->createdDate > $val->syncObjectCreateTime) && ($modifyInfo->createdBy)){
                //2023-01-01~2023-04-30之后的表单刷成CM
                $dealUser = $modifyInfo->createdBy.',';
                //需求收集3670 将变更单创建人所属部门 cm 拼接
                //if($type != 1){
                    $deptObj = $this->loadModel('dept')->getByID($modifyInfo->createdDept);
                    $dealUser .= $deptObj->cm;
               // }
                $modifyInfo->status = zget($this->lang->modify->statusList, $modifyInfo->status);
                $res = $this->loadModel('projectrelease')->syncObjectInfo($val, 'modify', $syncModifyId, trim($dealUser,','), $modifyInfo->createdDate, $modifyInfo);
                $i++;
            }
            $this->dao->update(TABLE_MODIFY)->data($updateParams)->where('id')->eq($syncModifyId)->exec();
        }
        echo '处理了'.$i.'条数据';
        exit();
    }
    public function showHistoryNodes($id){
        $modify = $this->modify->getByID($id);
        $reviewFailReason = json_decode($modify->reviewFailReason,true);
        $this->app->loadLang('outwarddelivery');
        $res = $this->dao->select('version')->from(TABLE_REVIEWNODE)->where('objectType')->eq('modify')->andWhere('objectID')->eq($id)->groupby('version')->fetchall();
        $versions = array_column($res,'version');
        foreach ($versions as $version) {
            $data = $this->loadModel('review')->getNodes('modify', $id, $version);
            foreach ($data as $k=>$v){
                if ($v->status == 'wait' || !(is_array($v->reviewers) && !empty($v->reviewers))){
                    unset($data[$k]);
                }
                // 后续不再显示系统部审核节点，去掉
                if ($v->stage == 4 && isset($modify->createdDate) && $modify->createdDate > "2024-04-02 23:59:59"){
                    unset($data[$k]);
                }
            }
            $nodes[$version]['nodes'] = $data;
        }
        foreach ($nodes as $key=>$node) {
            $nodes[$key]['countNodes'] = count($node['nodes']);
            if(isset($reviewFailReason[$key]) && !empty($reviewFailReason[$key])){
                foreach ($reviewFailReason[$key] as $value){
                    $nodes[$key]['countNodes'] += count($value);
                }
            }
//            if (isset($reviewFailReason[$key][4]) && !empty($reviewFailReason[$key][4])){
//                $nodes[$key]['countNodes']++;
//            }
//            if (isset($reviewFailReason[$key][5]) && !empty($reviewFailReason[$key][5])){
//                $nodes[$key]['countNodes']++;
//            }
        }
        $this->view->nodes      = $nodes;
        $this->view->modify     = $modify;
        $this->view->users      = $this->loadModel('user')->getPairs('noletter');
        $this->view->reviewFailReason      = $reviewFailReason;
        $this->display();
    }

    /**
     * @param $modifyID
     * 【变更回退、变更异常、变更失败】状态下 且 未被新的变更单关联时高亮。
     * 重新发起变更
     */
    public function reissue($modifyID){
        if($_POST)
        {
            $modifyID = $this->modify->create();

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            if ($_POST['abnormalCode']){
                $modify = $this->modify->getByID($_POST['abnormalCode']);
                $this->post->comment = $this->lang->modify->associaitonOrder.'：'.$modify->code.'<br/>'.$this->post->comment;
            }
            $this->loadModel('action')->create('modify', $modifyID, 'reissue', $this->post->comment);
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = inlink('browse');
            $response['id']    = $modifyID;
            if ($_POST['issubmit'] == 'submit'){
                $response['message'] = $this->lang->submitSuccess;
                $response['iframeUrl']  = $this->createLink('modify', 'submit', "id=".$modifyID."&linkType=2",'',true);
            }
            $this->send($response);
        }
        $demandLang = $this->app->loadLang('demand')->demand;
        $this->loadModel("modifycncc");
        //获取对外交付信息
        $modify = $this->modify->getByID($modifyID);
        $abnormalList = $this->modify->getModifyAbnormal();
        $abnormalList[$modify->id] = $modify->code;
        // 手动赋值
        $modify->isNewTestingRequest = 0;
        $modify->isNewProductEnroll = 0;
        $modify->isNewModifycncc = 1;
        if(empty($modify->ROR)){
            $modify->RORList = array();
        }else{
            $modify->RORList = json_decode(json_encode($modify->ROR),true);
        }
        if(!empty($modify->consumed)){
            $modify->consumed = array_pop($modify->consumed)->consumed;
        }

        $this->view->modify   = $modify;
        $this->view->abnormalList   = $abnormalList;
        $this->view->productenroll = new stdclass();
        $this->view->productenroll->isOnly = false;
        $this->view->productenroll->disable = false;
        $this->view->testingrequest = new stdclass();
        $this->view->testingrequest->isOnly = false;
        $this->view->testingrequest->disable = false;
        $this->view->modifycncc = new stdclass();
        $this->view->modifycncc->disable = false;

        //标题
        $this->view->title = $this->lang->modify->reissue;
        //申请测试单键值对
        $this->view->testingrequestList = array('' => '') + $this->loadModel("testingrequest")->getPairs($modifyID);
        //产品等级键值对
        $this->view->productenrollList = array('' => '') + $this->loadModel("productenroll")->getPairs($modifyID);
        //产品线键值对
        $this->view->productlineList = array('' => '') + $this->loadModel('productline')->getPairs();
        //所属系统
        $this->view->appAll  =  $this->loadModel('application')->getApplicationCodePairsSyncJinx();
        $this->view->apps = array('' => '') + array_column($this->view->appAll, 'name', 'id');
        $this->view->outsideProjectList =  array('' => '') + $this->loadModel('outsideplan')->getPairs();
        //产品名称
        $app = 0;
        if ($modify->createdDate > "2023-05-31 23:59:59"){
            $app = trim($modify->app,',');
        }
        $this->view->productList   = array('' => '') + $this->loadModel('product')->getProductWithCodeName('noclosed',0,'',$app);
        //产品名称被选中的列
        $this->view->productSelectList   = $this->dao->select('id,name')
            ->from(TABLE_PRODUCT)
            ->where('deleted')->eq(0)->andwhere('id')->in($modify->productId)
            ->fetchPairs('id', 'name');
        //所属项目
        $this->view->projectList       = array('' => '') + $this->loadModel('projectplan')->getAliveProjects(false);
        //关联问题
        $this->view->problemList       = array('' => '') + $this->loadModel('problem')->getPairsAbstract('noclosed');
        //关联二线工单
        $this->view->secondorderList   = array('' => '') + $this->dao->select('id,concat(concat(concat(code,"（"),summary),"）")')->from(TABLE_SECONDORDER)->where('deleted')->ne('1')->fetchPairs();
        //获取问题选中列
        $this->view->problemSelectList     = $this->dao->select("id,concat(code,'（',IFNULL(abstract,''),'）') as code")->from(TABLE_PROBLEM)
            ->where('status')->ne('deleted')->andwhere('id')->in($modify->problemId)
            ->orderBy('id_desc')
            ->fetchPairs();;
        //关联需求
        $singleUsageFlag = isset($this->config->singleUsage) && 'on' == $this->config->singleUsage;
        $this->view->demandList = array('' => '') + $this->loadModel('demand')->modifySelect('modify');
        //关联需求任务
        if(empty($modify->demandId)){
            $requirementList = array('' => '') + $this->dao->select('id,concat(concat(concat(code,"（"),name),"）")')->from(TABLE_REQUIREMENT)->where('status')->ne('deleted')->fetchPairs();
        }else{
            $opinionId = $this->dao->select('distinct opinionID')->from(TABLE_DEMAND)->where('id')->in(explode(',',$modify->demandId))->fetchAll();
            $requirementList = array('' => '') + $this->dao->select('id,concat(concat(concat(code,"（"),name),"）")')->from(TABLE_REQUIREMENT)->where('opinion')->in(array_column($opinionId,'opinionID'))->andWhere('status')->ne('deleted')->fetchpairs();
        }
        $this->view->requirementList   = $requirementList;
        //获取关联需求任务选中列
        $this->view->requirementSelectList     = $this->dao->select('id,concat(concat(concat(code,"（"),name),"）")')->from(TABLE_REQUIREMENT)
            ->where('status')->ne('deleted')->andwhere('id')->in($modify->requirementId)
            ->orderBy('id_desc')
            ->fetchPairs();;
        //所属CBP项目
        $this->view->cbpprojectList     = array('' => '') + $this->dao->select('code,concat(concat(concat(code,"（"),name),"）")')->from(TABLE_CBPPROJECT)->where('deleted')->ne(1)->fetchPairs();
        //产品变更
        $this->view->modifycnccList = array('' => '') + $this->dao->select('id,concat(concat(concat(code,"（"),substring(`desc`,1,25)),"）")')->from(TABLE_MODIFYCNCC)->fetchPairs();
        //审核节点下的审核人列表
        $reviewers            = $this->modify->getReviewers($modify->createdDept);
        //审核节点下的审核人列表
        $reviewerAccounts     = $this->loadModel('review')->getReviewerAccounts($reviewers);
        //审核节点下默认设置审核节点人
        $defChosenReviewNodes = $this->config->modifycncc->create->setDefChosenReviewNodes;
        $this->view->reviewers            = $reviewers;
        $this->view->reviewerAccounts     = $reviewerAccounts;
        $this->view->defChosenReviewNodes = $defChosenReviewNodes;
        //审核节点以及审核节点的审核人
        $nodesReviewers = $this->loadModel('review')->getAllNodeReviewers('modify', $modifyID, $modify->version);
        $this->view->nodesReviewers = $nodesReviewers;
        $this->view->users           = $this->loadModel('user')->getPairs('noletter');

        $this->display();
    }

    /**
     * @param $id
     * 修改关联的异常变更单
     */
    public function editabnormalorder($id){
        $modify = $this->modify->getByID($id);
        if ($_POST){
            $changes = $this->modify->editabnormalorder($id);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            if ($_POST['abnormalCode'] && $_POST['abnormalCode'] != $modify->abnormalCode){
                $info = $this->modify->getByID($_POST['abnormalCode']);
                $this->post->comment = $this->lang->modify->associaitonOrder.'：'.$info->code.'<br/>'.$this->post->comment;
            }
            $actionID = $this->loadModel('action')->create('modify', $id, 'editabnormalorder', $this->post->comment);
            $this->action->logHistory($actionID, $changes);
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
            exit;
        }
        $abnormalOrder = $this->getAbnormalById($modify);
        $abnormalList = array('' => '') + array($abnormalOrder['nowOrder']->id => $abnormalOrder['nowOrder']->code) + $this->modify->getModifyAbnormal();
        unset($abnormalList[$id]);
        //获取关联。被关联的变更单
        $this->view->abnormalOrder              = $abnormalOrder;
        $this->view->abnormalList               = $abnormalList;
        $this->display();
    }

    /**
     * @param $id
     * 根据异常变更单获取该变更单关联的问题单、需求条目
     */
    public function ajaxGetorderByabnormalId($id,$isAbnormal = 1,$source=''){
        $info = $this->modify->getByID($id);
        $problemIds = [];
        if ($info->problemId){
            $problemIds = array_filter(explode(',',$info->problemId));
        }
        //关联问题
        $problemList       = array('' => '') + $this->loadModel('problem')->getPairsAbstract('noclosed','',$problemIds);
//        $demandList        = array('' => '') + $this->loadModel('demand')->getPairsTitle('noclosed');
        $demandList        = $this->loadModel('demand')->modifySelectByEdit($info->demandId, 'modify', $id,'1',$source);
        $secondorderList   = array('' => '') + $this->dao->select('id,concat(concat(concat(code,"（"),summary),"）")')->from(TABLE_SECONDORDER)->where('deleted')->ne('1')->fetchPairs();
        $str = '';
        if ($isAbnormal == 1){
            $str = 'disabled';
        }
//        if ($str == 'disabled'){
//            $newDemandList       = [];
//            $newProblemList      = [];
//            $newsecondorderList  = [];
//            foreach (explode(',',) as $item) {
//
//            }
//        }
        if ($info){
            $data[0] = html::select('demandId[]', $demandList, $info->demandId, "class='form-control chosen demandIdClass' onchange='selectDemand()' multiple $str");;
            $data[1] = html::select('problemId[]', $problemList, $info->problemId,"class='form-control chosen problemIdClass' multiple $str");
            $data[2] = html::select('secondorderId[]', $secondorderList, $info->secondorderId,"class='form-control chosen' multiple $str");
        }else{
            $data[0] = html::select('demandId[]', $demandList, [], "class='form-control chosen demandIdClass' onchange='selectDemand()' multiple $str");;
            $data[1] = html::select('problemId[]', $problemList, [],"class='form-control chosen problemIdClass' multiple ");
            $data[2] = html::select('secondorderId[]', $secondorderList, [],"class='form-control chosen' multiple $str");
        }
        echo json_encode($data);
    }

    /**
     * @param $id
     * 获取变更单被关联的信息,以及关联的变更单
     */
    public function getAbnormalById($modify){
        $res = $this->dao->select('id,code')->from(TABLE_MODIFY)->where('id')->in($modify->abnormalCode)->fetchAll();
        $findInSet = '(FIND_IN_SET("'.$modify->id.'",abnormalCode))';
        $ret = $this->dao->select('id,code')->from(TABLE_MODIFY)->where($findInSet)->andWhere('`status`')->ne('deleted')->fetch();
        return ['newOrder'=>$res,'nowOrder'=>$ret];
    }
    public function ajaxdemo(){
        try {
            $res['pushModify'] = $this->loadModel('modify')->getUnPushedAndPush();
        } catch (Exception $e) {
            $res['pushModify'] = $e;
        }
        try {
            $res['pushCancelModify'] = $this->loadModel('modify')->getCancelUnPushedAndPush();
        } catch (Exception $e) {
            $res['pushCancelModify'] = $e;
        }
        a($res);
    }
}
