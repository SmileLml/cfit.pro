<?php
class demand extends control
{
    public function __construct($module = '', $method = '')
    {
        parent::__construct($module, $method);
        $this->loadModel('opinion');
        $this->lang->demand->typeList = $this->lang->opinion->sourceModeListOld;
        $this->lang->demand->unionList = $this->lang->opinion->unionList;
    }

    /**
     * Project: chengfangjinke
     * Method: browse
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:12
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
    public function browse($browseType = 'all', $param = 0, $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $browseType = strtolower($browseType);
        $queryID   = ($browseType == 'bysearch')  ? (int)$param : 0;
        $actionURL = $this->createLink('demand', 'browse', "browseType=bySearch&param=myQueryID");
        /* 为搜索字段赋值. */
        $depts           = $this->loadModel('dept')->getOptionMenu();
        $projectPlanList = $this->loadModel('project')->getPairs();
        $productList     = array('0' => '') + $this->loadModel('product')->getCodeNamePairs();
        $opinionList     = array('0' => '') + $this->loadModel('opinion')->getPairs();
        $requirementList     = array('0' => '') + $this->loadModel('requirement')->getPairs();
        $apps = $this->loadModel('application')->getapplicationNameCodePairs();
        $this->config->demand->search['params']['app']['values']         = array('0' => '') + $apps;
        $productPlanList = array('0' => '') + $this->loadModel('productplan')->getSimplePairs();
        $isPaymentList = array();
        foreach($this->lang->application->isPaymentList as $paymentID => $paymentValue)
        {
            if(!$paymentID) continue;
            $isPaymentList[',' . $paymentID . ','] = $paymentValue;
        }
        
        $isOverDateInfoVisible = $this->demand->getIsOverDateInfoVisible($this->app->user->account);
        if($isOverDateInfoVisible){
            $this->config->demand->search['fields']['isExtended'] = $this->lang->demand->isExtended;
            $this->config->demand->search['params']['isExtended']     = ['operator' => '=', 'control' => 'select', 'values' => $this->lang->demand->isExtendedList];
            $this->config->demand->search['fields']['deliveryOver'] = $this->lang->demand->deliveryOver;
            $this->config->demand->search['params']['deliveryOver']     = ['operator' => '=', 'control' => 'select', 'values' => $this->lang->demand->deliveryOverList];
        }


        $depts[0] = '';
        $this->config->demand->search['params']['acceptDept']['values']  = $depts;
        $this->config->demand->search['params']['createdDept']['values'] = $depts;
        $this->config->demand->search['params']['type']['values']        = $this->lang->demand->typeList;
        $this->config->demand->search['params']['union']['values']        = $this->lang->demand->unionList;
        $this->config->demand->search['params']['opinionID']['values']   = $opinionList;
        $this->config->demand->search['params']['requirementID']['values']   = $requirementList;
        $this->config->demand->search['params']['isPayment']['values']   += $isPaymentList;
        if(!empty($projectPlanList)) $this->config->demand->search['params']['project']['values'] = array(0 => '') + $projectPlanList;
        if(!empty($productList))     $this->config->demand->search['params']['product']['values']     = $productList;
        if(!empty($productPlanList)) $this->config->demand->search['params']['productPlan']['values']     = $productPlanList;


        $this->demand->buildSearchForm($queryID, $actionURL);

        /* 设置需求详情页面返回的url连接。*/
        $this->session->set('demandList', $this->app->getURI(true), 'backlog');
        $this->session->set('common_back_url', $this->app->getURI(true),'backlog');

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);
        $closeAccountList = $this->getAuthCloseUser();
        $demands = $this->demand->getList($browseType, $queryID, $orderBy, $pager);
        foreach ($demands as $demand)
        {
            if($demand->endDate == '0000-00-00'){
                $demand->endDate = '';
            }
            if($demand->end == '0000-00-00'){
                $demand->end = '';
            }
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
            if(in_array($demand->status,['suspend','closed','deleteout']))
            {
                $demand->dealUser = '';
            }
        }
        $this->view->title      = $this->lang->demand->common;
        $this->view->demands    = $demands;
        $this->view->orderBy    = $orderBy;
        $this->view->pager      = $pager;
        $this->view->browseType = $browseType;
        $this->view->param      = $param;
        $this->view->apps       = $apps;
        $this->view->depts      = $depts;
        $this->view->users      = $this->loadModel('user')->getPairs('noletter|noclosed');
        $this->view->executives = $closeAccountList;
        $this->view->projectPlanList = $projectPlanList;
        $this->display();
    }

    /**
     * @Notes:关闭按钮人员权限
     * @Date: 2024/1/25
     * @Time: 15:37
     * @Interface getAuthCloseUser
     * @return array
     */
    public function getAuthCloseUser()
    {
        $poList = $this->lang->demand->productManagerList;//产品经理后台自义配置
        if(!empty($poList))
        {
            foreach ($poList as $key=>$value){
                $poList[$key] = $key;
            }
        }
        $executiveList = $this->dept->getExecutiveUser();//二线专员
        $suspender = array_filter($this->lang->demand->demandCloseList); //后台配置关闭人
        $suspendList = [];
        if(!empty($suspender))
        {
            foreach ($suspender as $key=>$value){
                $suspendList[$key] = $key;
            }
        }
        $closeAccountList = array_filter(array_unique(array_merge($poList,$executiveList,$suspendList)));
        return $closeAccountList;

    }

    public function changestatus(){
        $this->demand->changeBySecondLine();
    }

    /**
     * Project: chengfangjinke
     * Method: create
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:12
     * Desc: This is the code comment. This method is called create.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     */
    public function create()
    {
        if($_POST)
        {
            $demandID = $this->demand->create();

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $this->loadModel('action')->create('demand', $demandID, 'created');
            $this->loadModel('action')->create('requirement', $_POST['requirementID'], 'createdemand');
            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = inlink('browse');

            
            $this->send($response);
        }

        /**
         * @var requirementModel $requirementModel
         * 倒挂需求任务时，登录人为需求任务的创建人和待处理人可选
         */
        $requirementModel = $this->loadModel('requirement');
        $requirements = $requirementModel->getRequirementByUser();

        //迭代三十三 创建时下一节点处理人为后台自定义产品经理
        $productManagerList = $this->lang->demand->productManagerList;
        $this->view->productManagerList = $productManagerList;
        $this->view->title = $this->lang->demand->create;
        $this->view->users = $this->loadModel('user')->getPairs('noclosed');
        $this->view->dept  = $this->loadModel('dept')->getByID($this->app->user->dept);
        $this->view->productList  = array('' => '');//array('0' => '','99999'=>'无') + $this->loadModel('product')->getProductWithCodeName('noclosed');
        $this->view->opinions     = array('0' => '') + $this->loadModel('opinion')->getOpinionList();
        $this->view->requirements = array('0' => '') + $requirements;
        $this->view->apps         = array('0' => '') + $this->loadModel('application')->getapplicationNameCodePairs();
        //根据项目实现和二线实现，默认获取对应所属项目list
        $this->view->plans      = array('0' => '') + $this->loadModel('projectplan')->getAliveProjects(false);
        $executions = array('' => '');
        $this->view->executions       = $executions;
        $this->view->fixType          = '';
        $this->display();
    }

    /**
     * Edit a demand.
     * 
     * @param  int $demandID 
     * @access public
     * @return void
     */
    public function edit($demandID = 0,$opinionID = 0)
    {
        $demand = $this->loadModel('demand')->getByID($demandID, true);
        if($_POST)
        {
            $changes = $this->demand->update($demandID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes || $this->post->comment)
            {
                $actionID = $this->loadModel('action')->create('demand', $demandID, 'edited');
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = inlink('view', "demandID=$demandID");

            $this->send($response);
        }

        //所属项目是否关闭
        $projectByIdInfo = $this->loadModel('project')->getByID($demand->project);
        if($projectByIdInfo)
        {
            if($projectByIdInfo->status == 'closed')
          {
              $demand->project = '';
          }
        }
        $requirementSelect = $this->getRequirementByOpinionID($opinionID,'id_desc');

        $requirement = $this->loadModel('requirement')->getByID($demand->requirementID);
        $opinion = $this->loadModel('opinion')->getByID($demand->opinionID);
        $opinionLock = isset($opinion->changeLock) ? $opinion->changeLock : '';
        $requirementLock = isset($requirement->changeLock) ? $requirement->changeLock : '';
        //变更中，不允许操作 2标识锁定
        if($demand->changeLock == 2)
        {
            $response['result']  = 'fail';
            $response['message'] = $this->lang->demand->changeIng;
            $this->send($response);
        }
        $consumed = $this->loadModel('consumed')->getObjectByID($demandID,'demand','wait');
        $plans = array('0' => '','1' => '无') + $this->loadModel('productplan')->getPairs($demand->product);
        $this->view->title            = $this->lang->demand->edit;
        $this->view->demand           = $demand;
        $this->view->plans            = $plans;
        $this->view->consumed         = $consumed->consumed ?? '';
        $this->view->users            = $this->loadModel('user')->getPairs('noclosed');
        $this->view->opinions         = array('0' => '') + $this->loadModel('opinion')->getOpinionList();
        $this->view->productList      = $demand->app ? array('0' => '','99999'=>'无') + $this->loadModel('product')->getCodeNamePairsByApp($demand->app) :array('0' => '','99999'=>'无');//array('0' => '','99999' => '无') + $this->loadModel('product')->getProductWithCodeName('noclosed');
        $this->view->productPlanList  = $demand->product ? array('0' => '','1' =>'无')+ $this->loadModel('productplan')->getPairs($demand->product) : array('0' => '','1' =>'无');//array('0' => '') + $this->loadModel('productplan')->getSimplePairs();
        $this->view->requirements     = array('' => '') + $requirementSelect;
        $this->view->apps             = array('0' => '') + $this->loadModel('application')->getapplicationNameCodePairs();
        $this->view->demand->requirement = explode(',', $this->view->demand->requirement)?:[];
        //迭代三十三 创建时下一节点处理人为后台自定义产品经理
        $productManagerList = $this->lang->demand->productManagerList;
        $this->view->productManagerList = $productManagerList;
        //根据项目实现和二线实现，默认获取对应所属项目list
        $this->view->projects      = array('0' => '') + $this->loadModel('projectplan')->getAliveProjects($demand->fixType == 'second');
        $executions = array('' => '');
        $this->view->executions       = $executions;
        $this->view->fixType          = '';
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: workloadEdit
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:12
     * Desc: This is the code comment. This method is called workloadEdit.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $demandID
     * @param int $consumedID
     */
    public function workloadEdit($demandID = 0, $consumedID = 0)
    {
        if($_POST)
        {
            $changes = $this->demand->workloadEdit($demandID, $consumedID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes || $this->post->comment != '')
            {
                $actionID = $this->loadModel('action')->create('demand', $demandID, 'edited', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $this->view->title    = $this->lang->demand->workloadEdit;
        $this->view->demand   = $this->demand->getByID($demandID);
        $this->view->users    = $this->loadModel('user')->getPairs('noclosed');
        $consumed = $this->demand->getConsumedByID($consumedID);
        //相关配合人员详情信息
        $consumed->details = $this->loadModel('consumed')->getConsumedDetailsArray($consumed->details);
        $this->view->consumed = $consumed;

        //检查是否是最后一条工作量信息
        $isLastConsumed =  $this->loadModel('consumed')->checkIsLastConsumed($consumedID, $demandID, 'demand');
        $this->view->isLastConsumed = $isLastConsumed;
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: workloadDelete
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:12
     * Desc: This is the code comment. This method is called workloadDelete.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $demandID
     * @param int $consumedID
     */
    public function workloadDelete($demandID = 0, $consumedID = 0)
    {
        if($_POST)
        {
            $changes = $this->demand->workloadDelete($demandID, $consumedID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes || $this->post->comment != '')
            {
                $actionID = $this->loadModel('action')->create('demand', $demandID, 'edited', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $this->view->title  = $this->lang->demand->workloadDelete;
        $this->view->demand = $this->demand->getByID($demandID);
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: workloadDetails
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:12
     * Desc: This is the code comment. This method is called workloadDetails.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $demandID
     * @param int $consumedID
     */
    public function workloadDetails($demandID = 0, $consumedID = 0)
    {
        $this->view->title    = $this->lang->demand->workloadDetails;
        $this->view->users    = $this->loadModel('user')->getPairs('noletter');
        $this->view->details  = $this->loadModel('consumed')->getWorkloadDetails($consumedID);
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: editSpecial
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:12
     * Desc: This is the code comment. This method is called editSpecial.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $demandID
     */
    public function editSpecial($demandID = 0)
    {
        if($_POST)
        {
            $changes = $this->demand->editSpecial($demandID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes)
            {
                $actionID = $this->loadModel('action')->create('demand', $demandID, 'editspecialed');
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $this->view->demand     = $this->demand->getByID($demandID);
        $this->display();
    }

    /**
     * @Notes:进展跟踪信息 用于权限配置显示详情页进展跟踪数据
     * @Date: 2024/4/16
     * @Time: 16:54
     * @Interface fieldsAboutonConlusion
     */
    public function fieldsAboutonConlusion()
    {
        return true;
    }

    /**
     * Project: chengfangjinke
     * Method: editAssignedTo
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:12
     * Desc: This is the code comment. This method is called editAssignedTo.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $demandID
     */
    public function editAssignedTo($demandID = 0)
    {
        if($_POST)
        {
            $changes = $this->demand->editAssignedTo($demandID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes || $this->post->comment != '')
            {
                $actionID = $this->loadModel('action')->create('demand', $demandID, 'edited', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        // Obtain the receiver.
        $acceptUser = $this->dao->select('account')->from(TABLE_CONSUMED)
             ->where('objectType')->eq('demand')
             ->andWhere('objectID')->eq($demandID)
             ->andWhere('`before`')->eq('assigned')
             ->fetch('account');

        $this->view->title      = $this->lang->demand->editAssignedTo;
        $this->view->demand     = $this->demand->getByID($demandID);
        $this->view->users      = $this->loadModel('user')->getPairs('noclosed');
        $this->view->acceptUser = $acceptUser;

        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: view
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:12
     * Desc: This is the code comment. This method is called view.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $demandID
     * @throws Exception
     */
    public function view($demandID = 0)
    {
        $demand = $this->demand->getByID($demandID, true);
        $requirements = array('' => '') + $this->loadModel('requirement')->getPairs();
        $requirementName = '';
        if(!empty($demand->requirementID))
        {
            $requirementName  = $requirements[$demand->requirementID] ?? '';
        }

        $mailto = '';
        if(isset($demand->consumed) && !empty($demand->consumed)){
            $consumedArr = $demand->consumed;
            $mailtoArr = array_column($consumedArr,'mailto');
            $countArr = count($mailtoArr);
            if($countArr > 0){
                $mailto = $consumedArr[$countArr-1]->mailto;
            }
        }
        $demand->mailto = $mailto;
        if($demand->status != 'onlinesuccess') $demand->actualOnlineDate = '';

        //时间置空
        if($demand->actualOnlineDate == '0000-00-00 00:00:00') $demand->actualOnlineDate = '';
        if($demand->end == '0000-00-00') $demand->end = '';
        if($demand->endDate == '0000-00-00') $demand->endDate = '';



        //迭代三十三要求已挂起待处理人置空
//        if(in_array($demand->status,['wait','suspend'])){
        if(in_array($demand->status,['wait'])){
            $dealUser = implode(',',array_filter(explode(',',$demand->dealUser)));
            $demand->dealUser = $dealUser;
        }else{
            $demand->dealUser = '';
        }

        if($demand->status == 'closed')
        {
            $demand->delayDealUser = '';
        }

        //自定义变更解锁人
        $unLock = array_filter(array_keys($this->lang->demand->unLockList));
        $this->view->unLock  = $unLock;
        $this->view->title   = $this->lang->demand->view;
        $this->view->users   = $this->loadModel('user')->getPairs('noclosed|noletter');
        $this->view->actions = $this->loadModel('action')->getList('demand', $demandID);
        $this->view->demand  = $demand;
        $this->view->apps    = $this->loadModel('application')->getPairs();
        $this->view->objects = $this->loadModel('secondline')->getInfoByDemand($demandID);
        $this->view->depts   = $this->loadModel('dept')->getOptionMenu();
        $this->view->requirementName = $requirementName;

        $opinion = $this->loadModel('opinion')->getByIdSimple($demand->opinionID);
        //构造需求全生命周期跟踪矩阵数据
        $lifeOpinionInfo = $this->buildLifeInfo($opinion);
        $this->view->lifeOpinionInfo  = $lifeOpinionInfo;//需求全生命周期跟踪矩阵数据

        if($demand->opinionID)   $this->view->opinion = $opinion;
//        if($demand->projectPlan) $this->view->plan    = $this->loadModel('projectplan')->getInIDs($demand->projectPlan);
        if($demand->project) $this->view->plan    = $this->loadModel('project')->getPairs();
        if($demand->product)     $this->view->product     = $this->loadModel('product')->getByID($demand->product);
        if($demand->productPlan) $this->view->productPlan = $this->loadModel('productplan')->getByID($demand->productPlan);
        if($demand->requirement) $this->view->requirements = array('' => '') + $requirements;

        $closeAccountList = $this->getAuthCloseUser();
        $this->view->executives =$closeAccountList;
        $this->view->projectList = $this->dao->select("id, `name`")->from(TABLE_PROJECT)->where('id')->in($this->view->demand->project)->fetchall();
        //所属阶段
        $this->view->executions =  $this->getExecution($demand->project);
        $this->view->task = $this->demand->getTaskName($demand->project,trim($demand->app,','),$demand->product,$demand->productPlan,$demandID,1,'demand');
        $this->view->creatorCanEdit =  $this->demand->checkCreatorPri($demand)  ? 1 : 0 ;

        //project是一个 xxx,xx的字符串 并且查看前逻辑，所属项目名称和id均为年度计划相关
        $plans = [];
        if($demand->project){
            $projectArr = explode(',',$demand->project);
            if(count($projectArr) > 1){
                $plan = $this->dao->select('id')->from(TABLE_PROJECTPLAN)->where('project')->in(array_filter(array_unique($projectArr)))->fetchALl();
                foreach ($plan as $planId){
                    $plans[] = $this->loadModel('projectplan')->getByID($planId->id);
                }
            }else if(count($projectArr) == 1){
                $plan = $this->dao->select('id')->from(TABLE_PROJECTPLAN)->where('project')->eq($demand->project)->fetch();
                $plans[] = $this->loadModel('projectplan')->getByID($plan->id);;
            }
        }
        $overDate = $this->overDate($demand->requirementID);
        $this->view->overDate = $overDate;
        $this->view->plans = $plans;

        $this->view->buildAndRelease = $this->demand->getBuildRelease($this->view->task ? $this->view->task->id : 0);
        $this->view->nodes       = $this->loadModel('review')->getNodesByStage('demandDelay', $demand->id, $demand->delayVersion);
        //超时考核信息是否可见
        $isOverDateInfoVisible =  $this->demand->getIsOverDateInfoVisible($this->app->user->account);
        $this->view->isOverDateInfoVisible = $isOverDateInfoVisible;
        $this->display();
    }

    /**
     * @Notes:        //交付超期剩余天数计算
     * @Date: 2024/4/9
     * @Time: 15:36
     * @Interface overDate
     * @param $requirementID
     * @throws Exception
     */
    public function overDate($requirementID)
    {
        /**
         * @var requirementModel $requirementModel
         * @var problemModel $problemModel
         */
        $requirementModel       = $this->loadModel('requirement');
        $problemModel           = $this->loadModel('problem');
        $requirement = $requirementModel->getById($requirementID);
        if('guestcn' == $requirement->createdBy){
            $newPublishedTime = $requirement->feekBackStartTime != '0000-00-00 00:00:00' ? $requirement->feekBackStartTime : '';
        }else{
            $newPublishedTime = $requirement->newPublishedTime != '0000-00-00 00:00:00' ? $requirement->newPublishedTime : '';
        }
        $outTime = $this->lang->demand->demandOutTime['demandOutTime']     ?? 2; //解决时间超时月份
        $finalDate  = $problemModel->getOverDate($newPublishedTime, $outTime); //超期时间
        $date1 = new DateTime($finalDate);
        $date2 = new DateTime(helper::today());
        $interval = $date1->diff($date2);
        $days = $interval->days;
        if(helper::today() > $finalDate)
        {
            $days = 0;
        }

        return $days;
    }

    /**
     * @Notes: 构造需求全生命周期跟踪矩阵数据
     * @Date: 2023/7/11
     * @Time: 9:30
     * @Interface buildLifeInfo
     * @param $opinion
     */
    public function buildLifeInfo($opinion)
    {
        /**
         * @var requirementModel $requirementModel
         * @var demandModel $demandModel
         */
        $requirementModel       = $this->loadModel('requirement');
        $demandModel            = $this->loadModel('demand');

        $lifeRequirementInfo = array();
        $lifeDemand          = array();
        //需求意向子集需求任务
        $requirementInfo = $requirementModel->getRequirementInfoByOpinionID($opinion->id);
        $countRequirement = count($requirementInfo);
        $countAll = 0;
        foreach ($requirementInfo as $key => $requirement)
        {
            $lifeDemand = $demandModel->getByRequirementID('id,code,title',$requirement->id);
            if(empty($lifeDemand))
            {
                $demandEmpty = new stdClass();
                $demandEmpty->code = '';
                $demandEmpty->title =  '';
                $lifeDemand[0] = $demandEmpty;
            }
            $lifeRequirementInfo[$key]['id'] = $requirement->id;
            $lifeRequirementInfo[$key]['code'] = $requirement->code;
            $lifeRequirementInfo[$key]['name'] = $requirement->name;
            $lifeRequirementInfo[$key]['count'] = count($lifeDemand);
            $lifeRequirementInfo[$key]['demands'] = $lifeDemand;
            $countAll += count($lifeDemand);
        }

        $lifeOpinionInfo['id']      = $opinion->id;
        $lifeOpinionInfo['code']    = $opinion->code;
        $lifeOpinionInfo['name']    = $opinion->name;
        $lifeOpinionInfo['countAll']  = ($countAll > $countRequirement) ? $countAll : $countRequirement;
        $lifeOpinionInfo['requirements']  = $lifeRequirementInfo;
        return $lifeOpinionInfo;
    }

    /**
     * Project: chengfangjinke
     * Method: feedback
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:12
     * Desc: This is the code comment. This method is called feedback.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $demandID
     */
    public function feedback($demandID)
    {
        if($_POST)
        {
            $changes = $this->demand->feedback($demandID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes || $this->post->comment)
            {
                $actionID = $this->loadModel('action')->create('demand', $demandID, 'feedbacked', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $this->view->depts   = $this->loadModel('dept')->getOptionMenu();
        $this->view->title   = $this->lang->demand->edit;
        $this->view->users   = $this->loadModel('user')->getPairs('noclosed');
        $this->view->demand = $this->loadModel('demand')->getByID($demandID);
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: suspend
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:13
     * Desc: This is the code comment. This method is called suspend.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $demandID
     */
    public function suspend($demandID = 0)
    {
        $demand = $this->demand->getByID($demandID);
        //校验挂起状态
        if($demand->status == 'suspend')
        {
            $this->view->canChange = false;
        }

        if($_POST)
        {
            $changes = $this->demand->suspend($demandID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes || $this->post->comment != '')
            {
                $actionID = $this->loadModel('action')->create('demand', $demandID, 'suspended', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }
        $this->view->title  = $this->lang->demand->suspend;
        $this->view->demand = $demand;
        $this->view->users   = $this->loadModel('user')->getPairs('noclosed');
        $this->view->actions = $this->loadModel('action')->getList('demand', $demandID);
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: start
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:13
     * Desc: This is the code comment. This method is called start.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $demandID
     */
    public function start($demandID = 0)
    {
        $demand = $this->demand->getByID($demandID);
        //校验挂起状态
        if($demand->status != 'suspend')
        {
            $this->view->canChange = false;
        }
        if($_POST)
        {
            $changes = $this->demand->start($demandID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes || $this->post->comment != '')
            {
                $actionID = $this->loadModel('action')->create('demand', $demandID, 'activated', $this->post->comment);
                $changes = array_reverse($changes);
                $this->loadModel('consumed')->record('demand', $demandID, 0, $this->app->user->account, $changes[0]['old'],$changes[0]['new'],'');
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $this->view->title  = $this->lang->demand->start;
        $this->view->demand = $demand;
        $this->view->users   = $this->loadModel('user')->getPairs('noclosed');
        $this->view->actions = $this->loadModel('action')->getList('demand', $demandID);
        $this->display();
    }

    /**
     * Desc: 忽略
     * Date: 2022/8/11
     * Time: 15:21
     *
     * @param int $demandID
     *
     */
    public function ignore($demandID = 0, $notice = 0)
    {
        if($_POST)
        {
            $changes = $this->demand->ignore($demandID);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes)
            {
                $actionID = $this->loadModel('action')->create('demand', $demandID, 'ignore', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $this->view->title  = $this->lang->demand->ignore;
        $this->view->notice  = $notice;
        $this->view->demand = $this->demand->getByID($demandID);
        $this->view->users   = $this->loadModel('user')->getPairs('noclosed');
        $this->view->actions = $this->loadModel('action')->getList('demand', $demandID);

        $this->display();
    }

    /**
     * Desc: 恢复
     * Date: 2022/8/11
     * Time: 15:21
     *
     * @param int $demandID
     *
     */
    public function recoveryed($demandID = 0)
    {
        if($_POST)
        {
            $changes = $this->demand->recoveryed($demandID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes)
            {
                $actionID = $this->loadModel('action')->create('demand', $demandID, 'recoveryed', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $this->view->title  = $this->lang->demand->recoveryed;
        $this->view->demand = $this->demand->getByID($demandID);
        $this->view->users   = $this->loadModel('user')->getPairs('noclosed');
        $this->view->actions = $this->loadModel('action')->getList('demand', $demandID);
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: close
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:13
     * Desc: This is the code comment. This method is called close.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $demandID
     */
    public function close($demandID = 0)
    {
        if($_POST)
        {
            $changes = $this->demand->close($demandID);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            if($changes || $this->post->comment != '')
            {
                $actionID = $this->loadModel('action')->create('demand', $demandID, 'closed', $this->post->comment);
                $this->loadModel('consumed')->record('demand', $demandID, 0, $this->app->user->account,$changes[0]['old'], 'closed', '');
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $this->view->depts   = $this->loadModel('dept')->getOptionMenu();
        $this->view->title   = $this->lang->demand->edit;
        $this->view->users   = $this->loadModel('user')->getPairs('noclosed');
        $this->view->actions = $this->loadModel('action')->getList('demand', $demandID);
        $this->view->demand = $this->loadModel('demand')->getByID($demandID);
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: export
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:13
     * Desc: This is the code comment. This method is called export.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param string $orderBy
     * @param string $browseType
     */
    public function export($orderBy = 'id_desc', $browseType = 'all')
    {
        /* format the fields of every demand in order to export data. */
        if($_POST)
        {
            $this->loadModel('file');
            $demandLang   = $this->lang->demand;
            $demandConfig = $this->config->demand;

            /* Create field lists. */
            $fields = $this->post->exportFields ? $this->post->exportFields : explode(',', $demandConfig->list->exportFields);
            foreach($fields as $key => $fieldName)
            {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = isset($demandLang->$fieldName) ? $demandLang->$fieldName : $fieldName;
                unset($fields[$key]);
            }

            /* Get demands. */
            $demands = array();
            if($this->session->demandOnlyCondition)
            {
                $demands = $this->dao->select('*')->from(TABLE_DEMAND)->where($this->session->demandQueryCondition)
                    ->beginIF($this->post->exportType == 'selected')->andWhere('id')->in($this->cookie->checkedItem)->fi()
                    ->orderBy($orderBy)->fetchAll('id');
            }
            else
            {
                $stmt = $this->dbh->query($this->session->demandQueryCondition . ($this->post->exportType == 'selected' ? " AND id IN({$this->cookie->checkedItem})" : '') . " ORDER BY " . strtr($orderBy, '_', ' '));
                while($row = $stmt->fetch()) $demands[$row->id] = $row;
            }
            $demandIdList = array_keys($demands);

            /* Get users, products and executions. */
            $users = $this->loadModel('user')->getPairs('noletter');
            $apps  = $this->loadModel('application')->getPairs();
            $depts = $this->loadModel('dept')->getTopPairs();
            $dmap  = $this->dao->select('account,dept')->from(TABLE_USER)->fetchAll('account');
            $this->loadModel('secondline');

            $productPairs     = $this->loadModel('product')->getSimplePairs();
            $productPlanPairs = $this->loadModel('productplan')->getSimplePairs();
            $plans = $this->loadModel('projectplan')->getPairs();

            // Obtain the receiver.
            $cmap = $this->dao->select('objectID, account')->from(TABLE_CONSUMED)
                 ->where('objectType')->eq('demand')
                 ->andWhere('`before`')->eq('assigned')
                 ->orderBy('id')
                 ->fetchPairs();

            // 获取所有需求条目数据。
            $allRequirement = $this->loadModel('requirement')->getPairs();


            foreach($demands as $demand)
            {
                // 获取需求意向。
                if($demand->opinionID) $demand->opinionID = $this->dao->select('name')->from(TABLE_OPINION)->where('id')->eq($demand->opinionID)->fetch('name');

                // 处理需求来源方式。
                $demand->type = zget($demandLang->typeList, $demand->type, '');

                // 处理业务需求单位。
                $demand->union = zget($demandLang->unionList, $demand->union, '');

                //处理所属需求任务
                $demand->requirementID = zget($allRequirement, $demand->requirementID, '');

                // 获取关联的需求条目。
//                $demand->requirement = trim($demand->requirement, ',');
//                if($demand->requirement)
//                {
//                    $requirements = explode(',', $demand->requirement);
//                    $requirementNameList = array();
//                    foreach($requirements as $requirementID) $requirementNameList[] = zget($allRequirement, $requirementID, $requirementID);
//                    $demand->requirement = implode(',', $requirementNameList);
//                }

                $demand->status = $demandLang->statusList[$demand->status];
                $demand->state  = $demandLang->stateList[$demand->state];

                $demand->createdDept = $depts[$dmap[$demand->createdBy]->dept];
                $demand->acceptUser  = isset($cmap[$demand->id]) ? $users[$cmap[$demand->id]] : '';
                $demand->acceptDept  = isset($cmap[$demand->id]) ? $depts[$dmap[$cmap[$demand->id]]->dept] : '';
                $demand->createdBy   = $users[$demand->createdBy];
                $demand->dealUser    = zget($users, $demand->dealUser, '');
                $demand->editedBy    = zget($users, $demand->editedBy, '');
                $demand->closedBy    = zget($users, $demand->closedBy, '');
                if($demand->fixType) $demand->fixType = $demandLang->fixTypeList[$demand->fixType];

                // 处理所属应用系统。
                if($demand->projectPlan)
                {
                    $as = array();
                    foreach(explode(',', $demand->projectPlan) as $projectPlan)
                    {
                        if(!$projectPlan) continue;
                        $as[] = zget($plans, $projectPlan);
                    }
                    $demand->projectPlan = implode(',', $as);
                }
                // 处理所属应用系统。
                if($demand->app)
                {
                    $as = array();
                    foreach(explode(',', $demand->app) as $app)
                    {
                        if(!$app) continue;
                        $as[] = zget($apps, $app);
                    }
                    $demand->app = implode(',', $as);
                }

                // 处理系统分类。
                if($demand->isPayment)
                {
                    $as = array();
                    foreach(explode(',', $demand->isPayment) as $paymentID)
                    {
                        if(!$paymentID) continue;
                        $as[] =  zget($this->lang->application->isPaymentList, $paymentID, $paymentID);
                    }
                    $isPayment = implode(',', $as);
                    $demand->isPayment = $isPayment;
                }

                /* 获取制版次数。*/
                $demand->buildTimes = $this->demand->getBuild($demand->id);

                /* 获取所属产品。*/
                if($demand->product) $demand->product = zget($productPairs, $demand->product, $demand->product);

                /* 获取所属产品计划。*/
                if($demand->productPlan) $demand->productPlan = zget($productPlanPairs, $demand->productPlan, $demand->productPlan);

                /* 获取关联的生产变更，数据修正，数据获取。*/
                $demand->relationModify = '';
                $demand->relationFix    = '';
                $demand->relationGain   = '';
                $relationObject = $this->secondline->getByID($demand->id, 'demand');
                foreach($relationObject['modify'] as $objectID => $object)
                {
                    $demand->relationModify .= $object . "\r\n";
                }

                foreach($relationObject['fix'] as $objectID => $object)
                {
                    $demand->relationFix .= $object . "\r\n";
                }

                foreach($relationObject['gain'] as $objectID => $object)
                {
                    $demand->relationGain .= $object . "\r\n";
                }
            }

            $this->post->set('fields', $fields);
            $this->post->set('rows', $demands);
            $this->post->set('kind', 'demand');
            $this->fetch('file', 'export' . $this->post->fileType, $_POST);
        }

        $this->view->fileName        = $this->lang->demand->exportName;
        $this->view->allExportFields = $this->config->demand->list->exportFields;
        $this->view->customExport    = true;
        $this->display();
    }


    public function exportTemplate()
    {
        if($_POST)
        {
            // $this->demand->setListValue();
            $fields = [];
            $templateFields = $this->config->demand->export->templateFields;
            foreach($templateFields as $field){
                $fields[$field] = $this->lang->demand->$field;
            }
            $num = $this->post->num;
            $rows =array();
            $dealArray = [];
            for ($i=0;$i < $num;$i++){
                foreach ($templateFields as $v){
                    $dealArray[$v] = '';
                    switch ($v){
                        case 'actualOnlineDate':
                        case 'createdDate':
                        case 'endDate':
                            $dealArray[$v] = '0000-00-00';
                            break;
                        default:
                            break;
                    }
                }
                $rows[$i] = (object)$dealArray;
            }
            $this->post->set('fields', $fields);
            $this->post->set('kind', 'demand');
            $this->post->set('rows', $rows);
            $this->post->set('fileName', '需求条目模板');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: deal
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:13
     * Desc: This is the code comment. This method is called deal.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $demandID
     */
    public function deal($demandID)
    {
        if($_POST)
        {
            $changes = $this->demand->deal($demandID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($this->post->status == 'assigned') //已确认时通知处理人
            {
                $actionID = $this->loadModel('action')->create('demand', $demandID, 'deal', $this->post->comment,'', $this->post->dealUser);
            } else {
                $actionID = $this->loadModel('action')->create('demand', $demandID, 'deal', $this->post->comment);
            }

            if($changes) $this->action->logHistory($actionID, $changes);
            //$this->demand->sendmail($demandID, $actionID);

            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $demand = $this->loadModel('demand')->getByID($demandID);
        if(!in_array($demand->status, ['confirmed','assigned'])){
            $response['result']  = 'fail';
            $response['message'] = "状态不开处理";  //到开发中就不能人工处理了
            $this->send($response);
        }
        $statusList = array('' => '');
        switch($demand->status)
        {
        case 'wait':
            $statusList['confirmed'] = $this->lang->demand->statusList['confirmed'];
            break;
        case 'confirmed':
            $statusList['assigned'] = $this->lang->demand->statusList['assigned'];
            break;
        case 'assigned':
            $statusList['feedbacked'] = $this->lang->demand->statusList['feedbacked'];
            break;
        case 'feedbacked':
            $statusList['build'] = $this->lang->demand->statusList['build'];
            break;
//        case 'solved':
//            $statusList['build'] = $this->lang->demand->statusList['build'];
//
//            $es = $this->dao->select('executive')->from(TABLE_DEPT)->fetchAll();
//            foreach($es as $e)
//            {
//                if(in_array($this->app->user->account, explode(',', $e->executive)))
//                {
//                    $statusList['closed'] = $this->lang->demand->statusList['closed'];
//                    break;
//                }
//            }
//
//            break;
        case 'build':
            $statusList['testsuccess'] = $this->lang->demand->testsuccess;
//            $statusList['testfailed'] = $this->lang->demand->statusList['testfailed'];
            $statusList['build'] = $this->lang->demand->statusList['build'];
            break;
        case 'testsuccess':
            $statusList['testsuccess'] = $this->lang->demand->testsuccess;
//            $statusList['verifysuccess'] = $this->lang->demand->verifysuccess;
//            $statusList['verifyfailed'] = $this->lang->demand->statusList['verifyfailed'];
            $statusList['build'] = $this->lang->demand->statusList['build'];
            break;
        case 'verifysuccess':
            $statusList['released'] = $this->lang->demand->statusList['released'];
            break;
        case 'released':
            $statusList['delivery'] = $this->lang->demand->statusList['delivery'];
            break;
        case 'delivery':
            $statusList['onlinesuccess'] = $this->lang->demand->statusList['onlinesuccess'];
//            $statusList['onlinefailed'] = $this->lang->demand->statusList['onlinefailed'];
            $statusList['build'] = $this->lang->demand->statusList['build'];
            break;
        case 'onlinesuccess':
//        case 'onlinefailed':
            $statusList['closed'] = $this->lang->demand->statusList['closed'];
            break;
//        case 'testfailed':
//        case 'verifyfailed':
//            $statusList['solved'] = $this->lang->demand->statusList['solved'];
//            break;
        }

        $this->view->depts      = $this->loadModel('dept')->getOptionMenu();
        $this->view->title      = $this->lang->demand->edit;
        $this->view->users      = $this->loadModel('user')->getPairs('noclosed');
        $this->view->demand     = $demand;
        $this->view->statusList = $statusList;
        $this->view->plans      = array('0' => '') + $this->loadModel('projectplan')->getAliveProjects(false);
        $this->view->apps       = array('0' => '') + $this->loadModel('application')->getPairs();
        $this->view->products   = array('0' => '') + $this->loadModel('product')->getPairs();
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: delete
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:13
     * Desc: This is the code comment. This method is called delete.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $demandID
     */
    public function delete($demandID,$requirementID)
    {
        if(!empty($_POST))
        {
            $this->dao->update(TABLE_DEMAND)->set('status')->eq('deleted')->where('id')->eq($demandID)->exec();
            $actionID = $this->loadModel('action')->create('demand', $demandID, 'deleted', $this->post->comment);

            //如果需求任务下的需求条目都删除完了，在需求任务状态要联动回退到“已发布”
            $demandList = $this->loadModel('demand')->getByRequirementID('*',$requirementID);
            if(!$demandList){
                $this->dao->update(TABLE_REQUIREMENT)->set('status')->eq('published')->where('id')->eq($requirementID)->exec();
            }
            //2022.4.27 tangfei 删除与二线管理单子的关联关系
            $sql = "delete from zt_secondline where (objectType='modify'or objectType='fix' or objectType='gain') and relationID=$demandID  and relationType='demand';";
            $this->dao->query($sql);
            //删除需求更新任务名
            $demand = $this->demand->getByID($demandID);
           /* $task = $this->demand->getTaskName($demand->project,$demand->app,$demand->product,$demand->productPlan,$demandID,1,'demand');
            if($task){
                $this->dao->update(TABLE_DEMAND)->set('execution')->eq('')->where('id')->eq($demandID)->exec();
                $this->loadModel('task')->deleteCodeUpdateTask($task,$demand->code);
            }*/
            $this->loadModel('task')->checkCodeExist($demand->project, $demand->app, 'demand', $demand->code, $demand, 1);

            $backUrl =  $this->session->demandList ? $this->session->demandList : inLink('browse');
            if(isonlybody())
            {
                die(js::closeModal('parent.parent', $backUrl));
            }
            else{
                die(js::locate(inLink('browse'),'parent.parent'));
            }
            die(js::reload('parent'));
        }

        $demand = $this->demand->getByID($demandID);
        $this->view->actions = $this->loadModel('action')->getList('demand', $demandID);
        $this->view->demand = $demand;
        $this->view->users   = $this->loadModel('user')->getPairs();
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: exportWord
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:13
     * Desc: This is the code comment. This method is called exportWord.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $demandID
     */
    public function exportWord($demandID)
    {
        $demand = $this->demand->getById($demandID);
        $users  = $this->loadModel('user')->getPairs('noletter');

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

        $section->addTitle($this->lang->demand->exportTitle, 1);
        $section->addText($this->lang->demand->code . ' ' . $demand->code, 'font_default', 'align_right');

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
        $table->addCell(1000, $cellStyle)->addText($this->lang->demand->title);
        $table->addCell(3000, array('gridSpan' => 3))->addText($demand->title);

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->demand->code);
        $table->addCell(1000, $cellStyle)->addText($demand->code);
        $table->addCell(1000, $cellStyle)->addText($this->lang->demand->source);
        $table->addCell(1000, $cellStyle)->addText($demand->source);

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->demand->type);
        $table->addCell(1000, $cellStyle)->addText(zget($this->lang->demand->typeList, $demand->type, ''));
        $table->addCell(1000, $cellStyle)->addText($this->lang->demand->status);
        $table->addCell(1000, $cellStyle)->addText(zget($this->lang->demand->statusList, $demand->status, ''));

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->demand->createdBy);
        $table->addCell(1000, $cellStyle)->addText(zget($users, $demand->createdBy, ''));
        $table->addCell(1000, $cellStyle)->addText($this->lang->demand->createdDate);
        $table->addCell(1000, $cellStyle)->addText($demand->createdDate);

        $apps = $this->loadModel('application')->getPairs();
        $as = array();
        foreach(explode(',', $demand->app) as $app)
        {
            if(!$app) continue;
            $as[] = zget($apps, $app);
        }
        $demand->app = implode(',', $as);

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->demand->app);
        $table->addCell(3000, array('gridSpan' => 3))->addText($demand->app);

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->demand->reason);
        $table->addCell(3000, array('gridSpan' => 3))->addText(strip_tags($demand->reason));

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->demand->solution);
        $table->addCell(3000, array('gridSpan' => 3))->addText(strip_tags($demand->solution));

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->demand->progress);
        $table->addCell(3000, array('gridSpan' => 3))->addText(strip_tags($demand->progress));

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->demand->conclusion);
        $table->addCell(3000, array('gridSpan' => 3))->addText(strip_tags($demand->conclusion));

        // Obtain the receiver.
        $acceptUser = $this->dao->select('account')->from(TABLE_CONSUMED)
             ->where('objectType')->eq('demand')
             ->andWhere('objectID')->eq($demandID)
             ->andWhere('`before`')->eq('assigned')
             ->fetch('account');

        $acceptUserName = $acceptUser ? zget($users, $acceptUser, '') : '';
        $acceptDeptID   = 0;
        $acceptDeptName = '';
        if($acceptUser)   $acceptDeptID   = $this->dao->select('dept')->from(TABLE_USER)->where('account')->eq($acceptUser)->fetch('dept');
        if($acceptDeptID) $acceptDeptName = $this->dao->select('name')->from(TABLE_DEPT)->where('id')->eq($acceptDeptID)->fetch('name');

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->demand->acceptUser);
        $table->addCell(3000, array('gridSpan' => 3))->addText($acceptUserName);

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->demand->acceptDept);
        $table->addCell(3000, array('gridSpan' => 3))->addText($acceptDeptName);

        /* Review. */
        $table->addRow();
        $table->addCell(4000, array('gridSpan' => 4))->addText($this->lang->consumedTitle, 'font_bold', array('align' => 'center'));

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->demand->nodeUser);
       // $table->addCell(1000, $cellStyle)->addText($this->lang->demand->consumed);
        $table->addCell(1000, $cellStyle)->addText($this->lang->demand->before);
        $table->addCell(2000, array('gridSpan' => 2))->addText($this->lang->demand->after);

        foreach($demand->consumed as $c)
        {
            $table->addRow();
            $table->addCell(1000, $cellStyle)->addText(zget($users, $c->createdBy, ''));
           // $table->addCell(1000, $cellStyle)->addText($c->consumed . '' . $this->lang->hour);
            $table->addCell(1000, $cellStyle)->addText(zget($this->lang->demand->statusList, $c->before, '-'));
            $table->addCell(2000, array('gridSpan' => 2))->addText(zget($this->lang->demand->statusList, $c->after, '-'));
        }

        $this->loadModel('file')->export2Word($this->lang->demand->exportTitle . $demand->code, $phpWord);
    }

    /**
     * Project: chengfangjinke
     * Method: import
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:13
     * Desc: This is the code comment. This method is called import.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     */
    public function import($projectId = 0)
    {
        if($_FILES)
        {
            $file = $this->loadModel('file')->getUpload('file');
            $file = $file[0];
            if($file['extension'] != 'xlsx') die(js::alert($this->lang->file->onlySupportXLSX));

            $fileName = $this->file->savePath . $this->file->getSaveName($file['pathname']);

            move_uploaded_file($file['tmpname'], $fileName);

            $phpExcel  = $this->app->loadClass('phpexcel');
            $phpReader = new PHPExcel_Reader_Excel2007();
            if(!$phpReader->canRead($fileName))
            {
                $phpReader = new PHPExcel_Reader_Excel5();
                if(!$phpReader->canRead($fileName))die(js::alert($this->lang->reviewissue->emptyReviewMsg,true));
            }
            $this->session->set('fileImport', $fileName);
            die(js::locate(inlink('showImport'), 'parent.parent'));
        }

        $this->display();
    }

    public function showImport($pagerID = 1, $maxImport = 0, $insert = '')
    {
        /* 获取import方法导入的临时文件。*/
        $file    = $this->session->fileImport;
        $tmpPath = $this->loadModel('file')->getPathOfImportedFile();
        $tmpFile = $tmpPath . DS . md5(basename($file));

        /* 如果是post请求，则调用createFromImport方法保存导入的数据。如果是最后一页则跳转列表，否则跳转下一页数据。*/
        if($_POST)
        {
            $this->demand->createFromImport();
            if($this->post->isEndPage)
            {
                unlink($tmpFile);
                die(js::locate($this->createLink('demand','browse'), 'parent'));
            }
            else
            {
                die(js::locate(inlink('showImport', "pagerID=" . ($this->post->pagerID + 1) . "&maxImport=$maxImport&insert=" . zget($_POST, 'insert', '')), 'parent'));
            }
        }

        /* 如果最大导入数量不为空，且导入文件存在，则获取文件内容进行序列化。*/
        if(!empty($maxImport) and file_exists($tmpFile))
        {
            $demandData = unserialize(file_get_contents($tmpFile));
        }
        else
        {
            /* 初始化变量，获取要导入的字段。*/
            $pagerID       = 1;
            $demandLang   = $this->lang->demand;
            $demandConfig = $this->config->demand;
            $fields        = explode(',', $demandConfig->list->exportFields);
            $fields[]      = 'workload';
            foreach($fields as $key => $fieldName)
            {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = isset($demandLang->$fieldName) ? $demandLang->$fieldName : $fieldName;
                unset($fields[$key]);
            }

            /* 获取导入文件所有行的数据。*/
            $rows = $this->file->getRowsFromExcel($file);
            $users = $this->loadModel('user')->getPairs('noletter|noclosed');
            $apps = $this->loadModel('application')->getapplicationNameCodePairs();
            $appNames = $this->loadModel('application')->getapplicationNamePairs();
            $opinions = $this->loadModel('opinion')->getPairsByRequmentBrowse();
            $requirements = array('' => '') + $this->loadModel('requirement')->getPairs();
            $depts = array('' => '') + $this->loadModel('dept')->getPairs();
            $products = array('0' => '无') + $this->loadModel('product')->getPairs();
            $productCodes = array('0' => '无') + $this->loadModel('product')->getCodePairs();
            $demandData = array();
            foreach($rows as $currentRow => $row)
            {
                $demand = new stdclass();
                foreach($row as $currentColumn => $cellValue)
                {
                    /* 获取导入文件第一行标题对应的导入字段key值。*/
                    if($currentRow == 1)
                    {
                        $field = array_search($cellValue, $fields);
                        $columnKey[$currentColumn] = $field ? $field : '';
                        continue;
                    }

                    /* 判断该列是否存在于导入的列中。*/
                    if(empty($columnKey[$currentColumn]))
                    {
                        $currentColumn++;
                        continue;
                    }
                    $field = $columnKey[$currentColumn];
                    $currentColumn++;
                    // check empty data.
                    /* 判断导入字段的值是否为空，如果为空，则设置该字段值为空。*/
                    if(empty($cellValue))
                    {
                        $demand->$field = '';
                        continue;
                    }

                    //if(in_array($field, $demandConfig->import->ignoreFields)) continue;
                    /* 针对下拉选项字段进行处理，然后赋值转换。*/
                    if(in_array($field, $demandConfig->export->listFields))
                    {
                        $demand->$field = $cellValue;
                        
                        if($field == 'opinionID'){
                            $fieldKey = array_search($cellValue, $opinions)?array_search($cellValue, $opinions):'';
                        }elseif($field == 'requirementID'){
                            $fieldKey = array_search($cellValue, $requirements)?array_search($cellValue, $requirements):'';
                        }elseif($field == 'acceptDept'){
                            $fieldKey = array_search($cellValue, $depts)?array_search($cellValue, $depts):'';
                        }elseif($field == 'product'){
                            if(array_search($cellValue, $products)){
                                $fieldKey = array_search($cellValue, $products);
                            }elseif(array_search($cellValue, $productCodes)){
                                $fieldKey = array_search($cellValue, $productCodes);
                            }else{
                                $fieldKey = '';
                            }
                        }elseif(in_array($field, array('createdBy','dealUser','acceptUser'))){
                            $fieldKey = array_search($cellValue, $users)?array_search($cellValue, $users):$cellValue;
                        }elseif($field == 'app'){
                            if(array_search($cellValue, $apps)){
                                $fieldKey = array_search($cellValue, $apps);
                            }elseif(array_search($cellValue, $appNames)){
                                $fieldKey = array_search($cellValue, $appNames);
                            }else{
                                $fieldKey = '';
                            }
                        }else{
                            if(!isset($demandLang->{$field . 'List'}) or !is_array($demandLang->{$field . 'List'})) continue;
                            /* when the cell value is key of list then eq the key. */
                            $listKey = array_keys($demandLang->{$field . 'List'});
                            unset($listKey[0]);
                            unset($listKey['']);
                            $fieldKey =  array_search($cellValue, $demandLang->{$field . 'List'});
                        }
                        if($fieldKey) $demand->$field = $fieldKey;
                    }
                    else
                    {
                        $demand->$field = $cellValue;
                    }
                    // if(isset($demand->createdBy)) $demand->createdBy = $this->pinyin($demand->createdBy);
                    // if(isset($demand->acceptUser)) $demand->acceptUser = $this->pinyin($demand->acceptUser);
                    // if(isset($demand->dealUser)) $demand->dealUser = $this->pinyin($demand->dealUser);
                }

//                if(empty($demand->name)) continue;
                $demandData[$currentRow] = $demand;
                unset($demand);
            }
            /* 获取处理好的数据后，写入临时文件中。*/
            file_put_contents($tmpFile, serialize($demandData));
        }
        /* 当导入文件的内容处理完成后，删除临时文件，并刷新列表页面。*/
        if(empty($demandData))
        {
            unlink($this->session->fileImport);
            unset($_SESSION['fileImport']);
            echo js::alert($this->lang->excel->noData);
            die(js::locate($this->createLink('demand','browse')));
        }

        unset($demandData[1]);

        /* 判断导入的数据是否大于系统预设最大导入数，如果大于则对数据进行拆分处理。*/
        $allCount = count($demandData);
        $allPager = 1;
        if($allCount > $this->config->file->maxImport)
        {
            if(empty($maxImport))
            {
                $this->view->allCount  = $allCount;
                $this->view->maxImport = $maxImport;
                $this->view->productID = $productID;
                $this->view->branch    = $branch;
                $this->view->type      = $type;
                die($this->display());
            }

            $allPager  = ceil($allCount / $maxImport);
            $demandData = array_slice($demandData, ($pagerID - 1) * $maxImport, $maxImport, true);
        }
        if(empty($demandData)) die(js::locate($this->createLink('demand','browse')));

        /* Judge whether the editedStories is too large and set session. */
        /* 判断要处理的需求意向是否太大，并设置session。*/
        $countInputVars  = count($demandData) * 11;
        $showSuhosinInfo = common::judgeSuhosinSetting($countInputVars);
        if($showSuhosinInfo) $this->view->suhosinInfo = extension_loaded('suhosin') ? sprintf($this->lang->suhosinInfo, $countInputVars) : sprintf($this->lang->maxVarsInfo, $countInputVars);


        /* 将要导入的数据及其相关变量，传递到页面进行展示。*/
        $this->view->title      = $this->lang->demand->common . $this->lang->colon . $this->lang->demand->showImport;
        $this->view->position[] = $this->lang->demand->showImport;
        $this->view->demandData  = $demandData;
        $this->view->allCount    = $allCount;
        $this->view->allPager    = $allPager;
        $this->view->pagerID     = $pagerID;
        $this->view->isEndPage   = $pagerID >= $allPager;
        $this->view->maxImport   = $maxImport;
        $this->view->dataInsert  = $insert;
        $this->view->dept        = array('0' => '')  + $this->loadModel('dept')->getPairs();
        $this->view->opinionList = array('0' => '') + $this->loadModel('opinion')->getOpinionList();
        $this->view->requirementList = array('0' => '') + $this->loadModel('requirement')->getPairs();
        $this->view->productList = array('0' => '无') + $this->loadModel('product')->getPairs();
        $this->view->apps        = array('0' => '') + $this->loadModel('application')->getapplicationNameCodePairs();
        $this->view->users       = $this->loadModel('user')->getPairs('noclosed');
        $this->display();
    }

    /**
     * Desc: 将中文转换为拼音
     * Date: 2022/4/26
     * Time: 11:22
     *
     * @param string $string
     * @return string
     *
     */
    public function pinyin($string = ''): string
    {
        //将中文转换为拼音
        $this->app->loadClass('pinyin');
        $pinyin = new pinyin();
        $pinyinString = $pinyin->convert($string);
        if(isset($pinyinString[0]) && $pinyinString[0] == 't'){
            $pinyinString[0] = 't_';
        }
        return implode('',$pinyinString);
    }


    /**
     * Project: chengfangjinke
     * Method: createConsumed
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:13
     * Desc: This is the code comment. This method is called createConsumed.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $demandID
     * @param $before
     * @param $after
     * @param $account
     * @param $consumed
     * @param $date
     */
    private function createConsumed($demandID, $before, $after, $account, $consumed, $date)
    {
        if(!$account) return;

        $data = new stdclass();
        $data->objectID    = $demandID;
        $data->objectType  = 'demand';
        $data->before      = $before;
        $data->after       = $after;
        $data->account     = $account;
        $data->consumed    = $consumed;
        $data->createdBy   = $account;
        $data->createdDate = $date;
        $this->dao->insert(TABLE_CONSUMED)->data($data)->exec();

        //2022-4-20 更新解决时间 //20220805 废弃
       /* if($after == 'closed' || $after == 'delivery') {
            $this->dao->update(TABLE_DEMAND)->set('solvedTime')->eq(date('Y-m-d H:i:s'))->where('id')->eq($demandID)->exec();
        }*/
    }

    /**
     * Project: chengfangjinke
     * Method: getValue
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:13
     * Desc: This is the code comment. This method is called getValue.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $sheet
     * @param $cell
     * @return mixed|string
     */
    private function getValue($sheet, $cell)
    {
        $value = $sheet->getCell($cell)->getValue();
        if(strpos($value, '_x000D') !== FALSE)
        {
            $vs = explode('_x000D', $value);
            $value = $vs[0];
        }

        return $value;
    }
    /**
     * Notes:实现方式与所属项目联动
     * Date: 2022/3/29
     * Time: 10:22
     *
     * @param $fixType
     *
     */
    public function ajaxGetSecondLine($fixType,$app = null,$sub = '0')
    {
        $secondLineType = $fixType == 'second';
        $plans= $this->loadModel('projectplan')->getAliveProjects($secondLineType);
        $plans[0] = '';
        $where = '';
        if($fixType == 'project'){
            $where = "onchange='loadProductExecutions(this.value)'";
        }else{
            $where = "onchange='loadProductExecutions(this.value,\"$fixType\",\"$app\")'";
        }
        if($sub == '1') $where = "onchange='selectproject(this.id,this.value)'";
        echo html::select('project', $plans, 0, "class='form-control chosen projectClass' $where");
    }

    public function ajaxGetOpinion($opinionID = 0)
    {
        $opinion = $this->loadModel('opinion')->getByID($opinionID);
        $opinionName = empty($opinion->name) ? '' : $opinion->name;
        $sourceName = empty($opinion->sourceName) ? '' : $opinion->sourceName;
        $sourceMode = empty($opinion->sourceMode) ? '' : $opinion->sourceMode;
        $deadline   = empty($opinion->deadline)   ? '' : $opinion->deadline;
        $union = empty($opinion->union) ? '' : $opinion->union;
        $overview = empty($opinion->overview) ? '' : $opinion->overview;
        $changeLock = empty($opinion->changeLock) ? '' : $opinion->changeLock;
        $object = array('sourceName' => $sourceName, 'sourceMode' => $sourceMode, 'deadline' => $deadline,
            'unionKey' => $union,'opinionName'=>$opinionName,'overview'=>$overview,'changeLock'=>$changeLock);

        echo json_encode($object);
    }

    /**
     * Desc: ajax获取所属需求意向
     * Date: 2022/8/4
     * Time: 17:53
     *
     * @param int $opinionID
     * @param int $requirementID
     * @param string $orderBy
     *
     */
    public function ajaxGetRequirement($opinionID = 0, $requirementID = 0,$orderBy = 'id_desc')
    {
        $pairs = $this->getRequirementByOpinionID($opinionID,$orderBy);
        $pairs = array('0' => '') + $pairs;
        echo html::select('requirementID', $pairs, '', "class='form-control chosen' disabled onchange='requirementChange(this.value)'");
    }

    /**
     * @Notes:复制时联动
     * @Date: 2024/1/22
     * @Time: 11:18
     * @Interface ajaxGetRequirement
     * @param int $opinionID
     * @param int $requirementID
     * @param string $orderBy
     */
    public function ajaxGetRequirementCopy($opinionID = 0, $requirementID = 0,$orderBy = 'id_desc')
    {
        $pairs = $this->getRequirementByOpinionIDCopy($opinionID,'published,splited,underchange,delivered,onlined',$orderBy);
        $pairs = array('0' => '') + $pairs;
        echo html::select('requirementID', $pairs, '', "class='form-control chosen' onchange='requirementChange(this.value)'");
    }

    public function ajaxGetOpinionByRequirement($id)
    {
        $opinion = $this->dao->select('opinion,changeLock')->from(TABLE_REQUIREMENT)->where('id')->eq($id)->fetch();
        $opinionName = $this->dao->select('name')->from(TABLE_OPINION)->where('id')->eq($opinion->opinion)->fetch('name');
        $data = new stdclass();
        $data->opinionID = $opinion->opinion;
        $data->opinionName = $opinionName;
        $data->changeLock = $opinion->changeLock;
        echo json_encode($data);
    }

    /**
     * Desc: 根据需求意向opinionID获取未删除的需求任务
     * Date: 2022/8/8
     * Time: 18:00
     *
     * @param int $opinionID
     * @param string $orderBy
     * @return mixed
     *
     */
    public function getRequirementByOpinionID($opinionID = 0, $orderBy = 'id_desc')
    {
        $pairs = $this->dao->select('id,name')->from(TABLE_REQUIREMENT)
            ->where('opinion')->eq($opinionID)
            ->andWhere('status')->ne('deleted')
            ->orderBy($orderBy)
            ->fetchPairs();
        return $pairs;
    }

    /**
     * @Notes:需求任务倒挂
     * @Date: 2024/2/2
     * @Time: 18:11
     * @Interface getRequirementByOpinionIDCopy
     * @param int $opinionID
     * @param string $orderBy
     * @param string $field
     * @return mixed
     */
    public function getRequirementByOpinionIDCopy($opinionID = 0,$field ='published,splited,underchange,delivered,onlined', $orderBy = 'id_desc')
    {
        $pairs = $this->dao->select('id,status,changeLock,name')->from(TABLE_REQUIREMENT)
            ->where('opinion')->eq($opinionID)
            ->andWhere('status')->in($field)
            ->orderBy($orderBy)
            ->fetchAll();

        $info = [];
        foreach ($pairs as $key => $value)
        {
            $info[$value->id] = $value->name;
        }

        return $info;
    }

    /**
     * 获取需求意向
     * @param int $id
     * @param string $orderBy
     */
    public function ajaxGetSelectOpinion($id = 0, $orderBy = 'id_desc')
    {
        if(empty($id) || $id == 'null'){
            $pairs = [];
        } else {
            //先获取 id
            $opinid = $this->dao->select('opinion')->from(TABLE_REQUIREMENT)
                ->where('id')->in($id)
                ->andWhere('status')->ne('deleted')
//                ->andWhere('status')->ne('closed')
                ->orderBy($orderBy)
                ->fetchPairs();
           if($opinid){
               $pairs = $this->dao->select('id,name')->from(TABLE_OPINION)
                   ->where('id')->in($opinid)
                   ->andWhere('status')->ne('deleted')
//                   ->andWhere('status')->ne('closed')
                   ->orderBy($orderBy)
                   ->fetchPairs();


           }else{
               $pairs = [];
           }

        }
        echo json_encode($pairs);
    }
    /**
     * 获取需求任务
     * @param int $id
     * @param string $orderBy
     */
    public function ajaxGetSelectRequirement($id = 0, $orderBy = 'id_desc')
    {
        if(empty($id) || $id == 'null'){
            $pairs = [];
        } else {
            $pairs = $this->dao->select('id,name')->from(TABLE_REQUIREMENT)
                ->where('opinion')->in($id)
                ->andWhere('status')->ne('deleted')
                ->andWhere('status')->ne('closed')
                ->orderBy($orderBy)
                ->fetchPairs();
            $pairs = array('0' => '') + $pairs;
        }
        echo html::select('requirement[]', $pairs, '', "class='form-control chosen' multiple onchange='setDemand(this)'");
    }

    /**
     * 获取需求条目
     * @param int $opinionID
     * @param string $orderBy
     */
    public function ajaxGetDemand($id = 0, $orderBy = 'id_desc')
    {
        if(empty($id) || $id == 'null'){
            $pairs = [];
        } else {
            $pairs = $this->dao->select('id, title')->from(TABLE_DEMAND)
                ->where('requirementID')->in($id)
                ->andWhere('status')->ne('deleted')
                ->andWhere('status')->ne('closed')
                ->orderBy($orderBy)
                ->fetchPairs();
            $pairs = array('0' => '') + $pairs;
        }

        echo html::select('demand[]', $pairs, '', "class='form-control chosen ' multiple ");
    }

    /**
     * @Notes:根据需求任务id集合获取需求条目默认选中 主要用于变更需求意向
     * @Date: 2023/11/13
     * @Time: 10:06
     * @Interface ajaxGetDemandChoosed
     * @param int $id
     * @param string $orderBy
     */
    public function ajaxGetDemandSelected($ids = 0, $orderBy = 'id_desc')
    {
        $selectedIds = [];
        $affectDemands = [];
        if($ids != 'null') {
            /**
             * @var demandModel $demandModel
             */
            $demandModel = $this->loadModel('demand');
            $demands = $demandModel->getDemandsByRequirementIds($ids,'id,code,title,`status`');


            $affectDemands = [];
            if(!empty($demands))
            {
                foreach ($demands as $value)
                {
                    //受影响条目范围 已录入、开发中、变更单退回
                    if(in_array($value->status,['wait','feedbacked','chanereturn']))
                    {
                        $demandStatus = zget($this->lang->demand->statusList,$value->status);
                        $affectDemands[$value->id] = $value->code. "(" .$value->title. "_". $demandStatus .")";
                    }
                }
            }

            $selectedIds = array_column($demands, 'id');
        }
        echo html::select('affectDemand[]', $affectDemands, $selectedIds, "class='form-control chosen ' multiple ");
    }


    public function ajaxGetProductPlan($productID = 0, $orderBy = 'id_desc')
    {
        $plans = $this->loadModel('productplan')->getPairs($productID, 0);

        $planName = 'productPlan';
       // $plans    = empty($plans) ? array('' => '') : $plans;
        $plans    = empty($plans) ? array('0' => '','1' => '无') : array('0' => '','1' => '无') + $plans;
        echo html::select($planName, $plans, '', "class='form-control chosen'");
    }

    public function ajaxGetProductPlan2($productID = 0, $data_id = 1)
    {
        $plans = $this->loadModel('productplan')->getPairs($productID, 0);

        $plans    = empty($plans) ? array('0' => '','1' => '无') : array('0' => '','1' => '无') + $plans;
        echo html::select('productPlan[]', $plans, '', "class='form-control chosen productPlanSelect w-100px' id='p-{$data_id}'");
    }

    public function copy($demandID = 0,$opinionID = 0)
    {
        $demand = $this->loadModel('demand')->getByID($demandID, true);
        if($_POST)
        {
            //校验提交权限
            $requirementInfo = $this->loadModel('requirement')->getById($this->post->requirementID);
            $checkAuthArr = $this->checkSubmitAuth($requirementInfo);

            if(!in_array($this->app->user->account,$checkAuthArr))
            {
                $response['result']  = 'fail';
                $response['message'] = $this->lang->demand->submitAuth;
                $this->send($response);
            }

            $demandID = $this->demand->create();

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $this->loadModel('action')->create('demand', $demandID, 'created', $this->post->comment);
            $this->loadModel('action')->create('requirement', $_POST['requirementID'], 'createdemand');
            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = inlink('browse');

            $this->send($response);
        }
        //所属项目是否关闭
        $projectByIdInfo = $this->loadModel('project')->getByID($demand->project);
        if($projectByIdInfo)
        {
            if($projectByIdInfo->status == 'closed')
            {
                $demand->project = '';
            }
        }
        $requirement = $this->getRequirementByOpinionIDCopy($opinionID,'published,splited,underchange,delivered,onlined','id_desc');
        //默认选中的需求任务时已锁定状态，默认将选中置空
        $changeLockAboutRequirement = $this->loadModel('requirement')->getByID($demand->requirementID);
        if($changeLockAboutRequirement->changeLock == 2){
            $demand->requirementID = '0';
        }
        $consumed = $this->loadModel('consumed')->getObjectByID($demandID,'demand','wait');
        $plans = array('1' => '无') + $this->loadModel('productplan')->getPairs($demand->product, 0);
        //迭代三十三 创建时下一节点处理人为后台自定义产品经理
        $productManagerList = $this->lang->demand->productManagerList;
        $this->view->productManagerList = $productManagerList;
        $this->view->title            = $this->lang->demand->copytable;
        $this->view->demand           = $demand;
        $this->view->plans            = $plans;
        $this->view->consumed         = $consumed->consumed ?? '';
        $this->view->users            = $this->loadModel('user')->getPairs('noclosed');
        $this->view->opinions         = array('0' => '') + $this->loadModel('opinion')->getPairsDefineStatus('delete,deleteout'); //过滤外部已删除数据
        $this->view->productList      = $demand->app ? array('0' => '','99999'=>'无') + $this->loadModel('product')->getCodeNamePairsByApp($demand->app) :array('0' => '','99999'=>'无');//array('0' => '','99999' => '?') + $this->loadModel('product')->getProductWithCodeName('noclosed');
        $this->view->productPlanList  = $demand->product ? array('0' => '','1' =>'无')+ $this->loadModel('productplan')->getPairs($demand->product) : array('0' => '','1' =>'无');//array('0' => '') + $this->loadModel('productplan')->getSimplePairs();
        $this->view->requirements     = array('' => '') + $requirement;
        $this->view->apps             = array('0' => '') + $this->loadModel('application')->getapplicationNameCodePairs();
        $this->view->demand->requirement = explode(',', $this->view->demand->requirement)?:[];

        //根据项目实现和二线实现，默认获取对应所属项目list
        $this->view->projects      = array('0' => '') + $this->loadModel('projectplan')->getAliveProjects($demand->fixType == 'second');
        $executions = array('' => '');
        $this->view->executions       = $executions;
        $this->view->fixType          = '';
        $this->display();
    }


    /**
     * @Notes:校验提交权限  任务的研发责任人、项目经理、数据库待处理人
     * @Date: 2024/3/7
     * @Time: 16:58
     * @Interface checkSubmitAuth
     * @param $requirement
     * @return array
     */
    public function checkSubmitAuth($requirement): array
    {
        $ownerInfo = $this->demand->getByRequirementID('acceptUser',$requirement->id);
        $ownerArr = [];
        $projectManagerArr = [];
        $dealUserArr = [];
        //任务的研发责任人（所有条目acceptUser合集）
        if($ownerInfo)
        {
            $ownerArr = array_column($ownerInfo,'acceptUser');
        }

        //任务项目经理
        $projectManager = $requirement->projectManager ?? '';
        if(!empty($projectManager))
        {
            $projectManagerArr = explode(',',$projectManager);
        }

        //数据库待处理人
        $dealUsersDb = $requirement->dealUser;
        if(!empty($dealUsersDb))
        {
            $dealUserArr = explode(',',$dealUsersDb);
        }

        return array_filter(array_unique(array_merge($ownerArr,$projectManagerArr,$dealUserArr,['admin'])));
    }

    public function assignment($id)
    {
        if($_POST)
        {
            $changes = $this->demand->assignment($id);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $actionID = $this->loadModel('action')->create('demand', $id, 'Assigned', $this->post->comment, $this->post->dealUser);
            $this->action->logHistory($actionID, $changes);

            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = 'parent';

            $this->send($response);

        }
        $this->view->demand  = $this->loadModel('demand')->getByID($id);
        $this->view->users   = $this->loadModel('user')->getPairs('noclosed|noletter');
        $this->display();
    }



    /**
     * Desc: 期望实现日期根据需求任务联动
     * Date: 2022/8/12
     * Time: 9:52
     *
     * @param $requirementID
     * @return mixed
     *
     */
    public function ajaxGetEndDateByRequirementID($requirementID)
    {
        $requirement = $this->loadModel('requirement')->getByRequirementID($requirementID);
        $deadline   = empty($requirement->deadLine)   ? '' : $requirement->deadLine;
        $end        = empty($requirement->planEnd)   ? '' : $requirement->planEnd;
        $reason     = empty($requirement->analysis)   ? '' : $requirement->analysis;
        $desc       = empty($requirement->desc)   ? '' : $requirement->desc;
        $app        = empty($requirement->app)          ? '' : $requirement->app;
        $changeLock = empty($requirement->changeLock)          ? '' : $requirement->changeLock;
        $return     = array('endDate' => $deadline,'end' => $end,'reason' => $reason,'desc'=>$desc,'app'=>$app,'changeLock'=>$changeLock);
        echo json_encode($return);
    }

    /**
     * 获取计划阶段
     * @param $projectID
     */
    public function getExecution($projectID){
        $this->loadModel('project');
        $defaults = array('' => '');
        if(!empty($projectID))
        {
            $executions = $this->project->getExecutionByAvailable($projectID);

            if(!empty($executions)) $defaults += $executions;
        }
        return $defaults;
    }

    /**
     * 延期
     * @param $projectID
     */
    public function delay($id)
    {
        $demand = $this->loadModel('demand')->getByID($id, true);
        if($_POST)
        {
            $this->demand->delay($id);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $actionID = $this->loadModel('action')->create('demand', $id, 'demanddelay', $this->post->comment, $this->post->dealUser);

            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = 'parent';

            $this->send($response);

        }
        $requirement = $this->dao
            ->select('*')
            ->from(TABLE_REQUIREMENT)
            ->where('id')->eq($demand->requirementID)
            ->fetch();
        if($requirement->createdBy == 'guestcn'){
            $requirement->newPublishedTime = $requirement->feekBackStartTime != '0000-00-00 00:00:00' ? $requirement->feekBackStartTime : '';
        }else{
            $requirement->newPublishedTime = $requirement->newPublishedTime != '0000-00-00 00:00:00' ? $requirement->newPublishedTime : '';
        }
        $problemObj  = $this->loadModel('problem');
        $month       = $this->lang->demand->demandOutTime['demandOutTime'] ?? 2;
        $demand->end = $problemObj->getOverDate($requirement->newPublishedTime, $month);

        //是否允许延期 迭代三十四更改为弹框提醒
        $canDelay = true;
        if(!empty($demand->requirementID))
        {
            //计算需求任务已发布是否超过2个月，若超过不允许延期
            $consumed = $this->dao->select('*')->from(TABLE_CONSUMED)->where('objectType')->eq('requirement')
                ->andWhere('objectID')->eq($demand->requirementID)
                ->andWhere('after')->eq('published')
                ->orderBy('id_desc')
                ->fetch();
            if(!empty($consumed)){
                $delayDays = date('Y-m-d',strtotime("+2 month",strtotime($consumed->createdDate)));
                if(helper::today() > $delayDays)
                {
                    $canDelay = false;
                }
            }
        }

        $this->view->canDelay = $canDelay;
        $this->view->demand = $demand;
        $this->display();
    }

    // 审批
    public function reviewdelay($id){
        $demand       = $this->demand->getByID($id);
        if($_POST)
        {
            $logChanges = $this->demand->delayReview($id);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $actionID = $this->loadModel('action')->create('demand', $id, 'reviewdelay', $this->post->suggest);
            $this->action->logHistory($actionID, $logChanges);

            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }

        $requirement = $this->loadModel('requirement')->getByID($demand->requirementID);
        $opinion = $this->loadModel('opinion')->getByID($demand->opinionID);

        $this->view->requirement    = $requirement;
        $this->view->opinion        = $opinion;
        $this->view->title          = $this->lang->review->submit;
        $this->view->position[]     = $this->lang->review->submit;
        $this->view->demand       = $demand;
        $this->display();

    }

    /**
     * @Notes:需求条目是否纳入交付超期标记
     * @Date: 2023/9/14
     * @Time: 18:23
     * @Interface isExtended
     * @param $demandID
     */
    public function isExtended($demandID)
    {
        $demand       = $this->demand->getByID($demandID);

        if($_POST)
        {
            $isExtended = $_POST['isExtended'] ?? '';
            if(empty($_POST['isExtended'])){
                dao::$errors['isExtended'] = sprintf($this->lang->demand->emptyObject, $this->lang->demand->isExtended);

                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $this->dao->update(TABLE_DEMAND)->set('isExtended')->eq($isExtended)->where('id')->eq($demandID)->exec();

            $historyDemand = new stdClass();
            $historyDemand->isExtended = $demand->isExtended;

            $newDemand = new stdClass();
            $newDemand->isExtended = $isExtended;

            $actionID = $this->loadModel('action')->create('demand', $demandID, 'updateisextended');
            $this->action->logHistory($actionID, common::createChanges($historyDemand, $newDemand));

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }

        $this->view->demand       = $demand;
        $this->display();
    }

    /**
     * 查询历史数据
     * @param $id
     * @return void
     */
    public function showdelayHistoryNodes($id){
        $res = $this->dao->select('version')->from(TABLE_REVIEWNODE)->where('objectType')->eq('demandDelay')->andWhere('objectID')->eq($id)->groupby('version')->fetchall();
        $versions = array_column($res,'version');
        foreach ($versions as $version) {
            $data = $this->loadModel('review')->getNodesByStage('demandDelay', $id, $version);
            foreach ($data as $k=>$v){
                if ($v->status == 'wait' || !(is_array($v->reviewers) && !empty($v->reviewers))){
                    unset($data[$k]);
                }
            }
            $nodes[$version]['nodes'] = $data;
        }
        foreach ($nodes as $key=>$node) {
            $nodes[$key]['countNodes'] = count($node['nodes']);
        }
        $this->view->nodes      = $nodes;
        $this->view->users      = $this->loadModel('user')->getPairs('noletter');
        $this->display();
    }

    public function sendmailByOutTime()
    {
        $res = $this->demand->sendmailByOutTime();

        a($res);
    }

    /**
     * @Notes:ajax获取需求条目
     * @Date: 2023/12/20
     * @Time: 9:30
     * @Interface ajaxGetById
     * @param int $demandId
     */
    public function ajaxGetById($demandId = 0)
    {
        $demandInfo = $this->loadModel('demand')->getById($demandId);
        $desc       = empty($demandInfo->desc)   ? '' : $demandInfo->desc;
        $reason     = empty($demandInfo->reason)  ? '' : $demandInfo->reason;
        $return     = array('reason' => $reason,'desc'=>$desc);
        echo json_encode($return);
    }

    /**
     * 第一次按照计划完成时间提醒
     */
    public function remindToEndMailFirst(){
        $this->demand->remindToEndMailFirst();
    }

    /**
     * 第二次按照计划完成时间提醒
     */
    public function remindToEndMailSecond(){
        $this->demand->remindToEndMailSecond();
    }
}

