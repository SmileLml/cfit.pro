<?php
class requirementinside extends control
{
    public function __construct($moduleName = '', $methodName = '')
    {
        parent::__construct($moduleName, $methodName);
        $this->app->loadLang("demand");
        $this->app->loadLang("opinion");
        $this->app->loadLang("requirement");
    }
    /**
     * Browse requirements.
     *
     * @param  string $browseType
     * @param  int    $param
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function browse($browseType = 'all', $param = 0, $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        /* By search. */
        /* 构建需求条目搜索表单所需参数。*/
        $this->app->loadLang("opinioninside");
        $this->app->loadLang("demandinside");

        $browseType = strtolower($browseType);
        $queryID    = ($browseType == 'bysearch') ? (int)$param : 0;
        $actionURL  = $this->createLink('requirementinside', 'browse', "browseType=bySearch&param=myQueryID");
        /* 处理搜索字段的赋值。*/
        $apps = $this->loadModel('application')->getPairs();
        $this->config->requirementinside->search['params']['app']['values']         = array('0' => '') + $this->loadModel('application')->getapplicationNameCodePairs();

        $depts = $this->loadModel('dept')->getOptionMenu();
        $this->config->requirementinside->search['params']['dept']['values'] = $depts;

        $this->config->requirementinside->search['params']['sourceMode']['values'] = $this->lang->opinioninside->sourceModeList;
        $this->config->requirementinside->search['params']['union']['values'] = $this->lang->opinion->unionList;
        $this->loadModel('requirementinside')->buildSearchForm($queryID, $actionURL);

        /* Load pager. */
        /* 初始化分页对象。*/
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);
        $this->session->set('requirementinsideList', $this->app->getURI(true),'backlog');

        $requirements = $this->loadModel('requirementinside')->getList($browseType, $queryID, $orderBy, $pager);
        foreach ($requirements as $requirement){
            //计划完成时间
            if($requirement->end == '0000-00-00'){
                $requirement->end = '';
            }
            if($requirement->planEnd == '0000-00-00'){
                $requirement->planEnd = '';
            }
            $dealUserArray = explode("," , $requirement->dealUser);
            $requirement->reviewer = implode(",", array_unique($dealUserArray));
            if(in_array($requirement->status,['delivered','onlined'])){
                $requirement->reviewer = '';
            }
            if($requirement->deadLine == '0000-00-00') $requirement->deadLine='';
            $demands = $this->loadModel('demandinside')->getBrowesByRequirementID($requirement->id);
            foreach ($demands as $key => $demand) {
                //已交付 上线成功 处理人置空显示 已关闭也不显示待处理人
                if(in_array($demand->status, ['delivery','onlinesuccess','closed'])){
                    $demands[$key]->dealUser  = '';
                }
                if(isset($dmap[$demand->createdBy])){
                    $demands[$key]->createdDept = $dmap[$demand->createdBy]->dept;
                }
                $demands[$key]->creatorCanEdit = 0;
                if($this->loadModel('demandinside')->checkCreatorPri($demand)){
                    $demands[$key]->creatorCanEdit = 1;
                }
            }
            $requirement->children = $demands;
            //需求任务的所属项目取需求条目的并集 project字段存在  xxx,xxx 需单独处理 迭代二十五要求年度计划与需求条目并集
            $demandsOther = $this->loadModel('requirementinside')->getDemandByRequirement($requirement->id);
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
        //后台配置挂起人
        $this->loadModel('demand');
        $suspender = array_filter($this->lang->demand->requirementSuspendList);
        $suspendList = [];
        if(!empty($suspender))
        {
            foreach ($suspender as $key=>$value){
                $suspendList[$key] = $key;
            }
        }
        $this->session->set('requirementHistory', $this->app->getURI(true));
        /* 获取需求条目数据及其相关配合的部门、项目和人员信息。*/
        $this->view->title        = $this->lang->requirementinside->common;
        $this->view->requirements = $requirements;
        $this->view->executives   = $suspendList ? array_keys($suspendList): [];
        $this->view->orderBy      = $orderBy;
        $this->view->param        = $param;
        $this->view->pager        = $pager;
        $this->view->browseType   = $browseType;
        $this->view->depts        = $depts;
        $this->view->projects     = $this->loadModel('project')->getPairs();
//        $this->view->projects     = $this->loadModel('project')->getPairsByProgram(); 搜索与展示不匹配
        $this->view->users        = $this->loadModel('user')->getPairs('noletter|noclosed');
        $this->view->projectPlanList = $this->loadModel('projectplan')->getPairs();
        $this->view->user        = $this->app->user->account;
        $this->display();
    }

    /**
     * Create a requirement.
     *
     * @access public
     * @return void
     */
    public function create()
    {
        /* 调用需求条目传教页面。当请求方式时post时，调用create方法获取请求参数，处理创建逻辑。如果失败则返回错误信息，成功则记录创建操作。*/
        if($_POST)
        {
            $requirementID = $this->loadModel('requirementinside')->create();

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $this->loadModel('action')->create('requirement', $requirementID, 'created');
            $this->loadModel('action')->create('opinion', $_POST['opinionID'], 'createdrequirement');
            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = inlink('browse');
            $this->send($response);
        }
        $opinions = array('0' => '') + $this->loadModel('opinioninside')->getOpinionsPairsByUser();
        $this->view->opinions     = $opinions;
        $this->view->users = $this->loadModel('user')->getPairs('noclosed');
        $this->view->title = $this->lang->requirementinside->create;
        $this->view->apps = array('0' => '') + $this->loadModel('application')->getapplicationNameCodePairs();
        $this->display();
    }

    /**
     * Edit a requirement.
     *
     * @param  int    $requirementID
     * @access public
     * @return void
     */
    public function edit($requirementID)
    {
        /* 查询需求条目信息，传递到页面编辑。当请求方式为post时，调用update方法接收请求参数，处理编辑逻辑，当编辑成功时，会记录编辑动作，响应成功信息。*/
        if($_POST)
        {
            $changes = $this->loadModel('requirementinside')->update($requirementID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $actionID = $this->loadModel('action')->create('requirement', $requirementID, 'edited');
            $this->action->logHistory($actionID, $changes);

            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = inlink('view', "requirementID=$requirementID");
            $this->send($response);
        }

        $opinions = array('0' => '') + $this->loadModel('opinioninside')->getPairs();
        $requirement = $this->loadModel('requirementinside')->getByID($requirementID);
        $consumed = $this->loadModel('consumed')->getObjectByID($requirementID,'requirement','published');

        if($requirement->deadLine == '0000-00-00'){
            $requirement->deadLine = ''; 
        }
        $this->view->opinions     = $opinions;
        $this->view->users = $this->loadModel('user')->getPairs('noclosed');
        $this->view->title       = $this->lang->requirementinside->edit;
        $this->view->requirement = $requirement;
        $this->view->consumed = $consumed->consumed ?? '';
        $this->view->apps = array('0' => '') + $this->loadModel('application')->getapplicationNameCodePairs();
        $this->display();
    }

    /**
     * Confirm a requirement.
     *
     * @param  int    $requirementID
     * @access public
     * @return void
     */
    public function confirm($requirementID)
    {
        /* 当请求方式为post时，调用confirm方法处理确认逻辑，处理成功则记录确认动作，响应成功信息。*/
        if($_POST)
        {
            $changes = $this->loadModel('requirementinside')->confirm($requirementID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes)
            {
                $actionID = $this->loadModel('action')->create('requirement', $requirementID, 'confirmed');
                $this->loadModel('requirementinside')->sendmail($requirementID,$actionID);
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        /* 获取需求条目信息和相关操作记录信息。*/
        $this->view->title       = $this->lang->requirementinside->confirm;
        $this->view->requirement = $this->loadModel('requirementinside')->getByID($requirementID);
        $this->view->users = $this->loadModel('user')->getPairs('noclosed');
        $this->view->actions     = $this->loadmodel('action')->getList('requirement', $requirementID);
        $this->display();
    }

    /**
     * Feedback a requirement.
     *
     * @param  int    $requirementID
     * @access public
     * @return void
     */
    public function feedback($requirementID)
    {
        /* 当请求方式为post时，调用feedback方法处理需求条目的反馈逻辑，成功则记录操作动作并返回成功信息。*/
        if($_POST)
        {
            $changes = $this->loadModel('requirementinside')->feedback($requirementID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes)
            {
                /* 判断是否为推送的需求条目。*/
                /*$newRequirement = $this->loadModel('requirementinside')->getByID($requirementID);
                $pushEnable     = $this->config->global->pushEnable;
                $pushPrompt     = '';
                if($newRequirement->feedbackCode and $pushEnable == 'enable')
                {
                    $pushPrompt = $this->lang->requirementinside->pushPrompt;
                }*/
                $actionID = $this->loadModel('action')->create('requirement', $requirementID, 'createfeedback');
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = inlink('view', "requirementID=$requirementID");

            $this->send($response);
        }

        $requirement = $this->loadModel('requirementinside')->getByID($requirementID);

        /* 此方法需要填写实施部门、归属项目、所属产品线、应用系统、设计产品等，此处代码是获取对应数据到view模板中。*/
        $this->view->title       = $this->lang->requirementinside->feedback;
        $this->view->requirement = $requirement;
        $this->view->opinion     = $this->loadModel('opinioninside')->getByID($requirement->opinion);
        $this->view->lines       = array('' => '') + $this->loadModel('product')->getLinePairs();
        $this->view->products    = $this->product->getPairs();
        $this->view->depts       = $this->loadModel('dept')->getOptionMenu();
        $this->view->projects    = array('' => '') + $this->loadModel('projectplan')->getPairs();
        $this->view->users       = $this->loadmodel('user')->getPairs('noclosed');
        $this->view->apps        = array('' => '') + $this->loadmodel('application')->getPairs();

        $this->display('requirement', 'newfeedback');
    }

    /**
     * Change a requirement.
     *
     * @param  int    $requirementID
     * @access public
     * @return void
     */
    public function change($requirementID)
    {
        if($_POST)
        {
            $oldRequirement = $this->loadModel('requirementinside')->getByID($requirementID);
            /* 判断需求条目变更时，评审人是否选填了(系统手动拆分的需求条目才判断)。*/
            if(!implode('', $this->post->reviewer) and empty($oldRequirement->entriesCode))
            {
                $response = array();
                $response['result']  = 'fail';
                $response['message'] = $this->lang->requirementinside->reviewerEmpty;
                $this->send($response);
            }

            /* 当请求方式为post时，调用change方法处理变更逻辑，如果处理成功则记录变更动作，然后返回成功信息。*/
            $changes = $this->loadModel('requirementinside')->change($requirementID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes)
            {
                /* 判断是否为推送的需求条目。*/
                $newRequirement = $this->loadModel('requirementinside')->getByID($requirementID);
                $pushEnable     = $this->config->global->pushEnable;
                $pushPromptChange = '';
                if($newRequirement->feedbackCode and $pushEnable == 'enable')
                {
                    $pushPromptChange = $this->lang->requirementinside->pushPromptChange;
                }
                $actionID = $this->loadModel('action')->create('requirement', $requirementID, 'changed', $pushPromptChange, $newRequirement->version);
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = inlink('browse');

            $this->send($response);
        }

        $requirement = $this->loadModel('requirementinside')->getByID($requirementID);

        /* 此方法类似feedback方法，需要填写实施部门、归属项目、所属产品线、应用系统、设计产品等，此处代码是获取对应数据到view模板中。*/
        $this->view->title       = $this->lang->requirementinside->change;
        $this->view->requirement = $requirement;
        $this->view->opinion     = $this->loadModel('opinioninside')->getByID($requirement->opinion);
        $this->view->lines       = array('' => '') + $this->loadModel('product')->getLinePairs();
        $this->view->products    = $this->product->getPairs();
        $this->view->depts       = $this->loadModel('dept')->getOptionMenu();
        $this->view->projects    = array(0 => '') + $this->loadModel('projectplan')->getPairs();
        $this->view->users       = $this->loadmodel('user')->getPairs('noclosed');
        $this->view->apps        = array(0 => '') + $this->loadmodel('application')->getPairs();

        $this->display('requirement', 'newchange');
    }

    /**
     * Review a requirement.
     *
     * @param  int    $requirementID
     * @access public
     * @return void
     */
    public function review($requirementID)
    {
        $requirement = $this->loadModel('requirementinside')->getByID($requirementID, 'latest');
        /* 当请求方式为post时，调用review方法处理需求条目评审逻辑，评审成功则记录操作动作和变动字段，返回成功信息。*/
        if($_POST)
        {
            $changes = $this->loadModel('requirementinside')->review($requirementID, $requirement->changeVersion, $this->post->result, $this->post->comment);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            if($changes)
            {
                $obj = $this->loadModel('requirementinside')->getByID($requirementID);
                $actionID = $this->loadModel('action')->create('requirement', $requirementID, 'reviewed', $this->post->comment, $obj->version);
//                $this->loadModel('requirementinside')->sendmail($requirementID,$actionID);
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        /* 获取需求条目信息、产品线、产品、部门、项目计划、用户和应用系统信息。*/
        $this->view->title       = $this->lang->requirementinside->review;
        $this->view->requirement = $requirement;
        $this->view->lines       = array('' => '') + $this->loadModel('product')->getLinePairs();
        $this->view->products    = $this->product->getPairs();
        $this->view->depts       = $this->loadModel('dept')->getOptionMenu();
        $this->view->projects    = array(0 => '') + $this->loadModel('projectplan')->getPairs();
        $this->view->users       = $this->loadmodel('user')->getPairs('noclosed|noletter');
        $this->view->apps        = array(0 => '') + $this->loadmodel('application')->getPairs();

        $this->display('requirement', 'newreview');
    }

    /**
     * View a requirement.
     *
     * @param  int    $requirementID
     * @access public
     * @return void
     */
    public function view($requirementID = 0)
    {
        $requirement = $this->loadModel('requirementinside')->getByID($requirementID);
        //外部同步需求任务接收时间取创建时间，内部需求任务取需求意向接收时间
        if($requirement->createdBy == 'guestcn'){
            $requirement->acceptTime = $requirement->createdDate;
        }else{
            $opinionInfo = $this->loadModel('opinioninside')->getByID($requirement->opinion);
            $requirement->acceptTime = $opinionInfo->receiveDate ?? '';
        }

        //上线时间
        if($requirement->onlineTimeByDemand == '0000-00-00 00:00:00'){
            $requirement->onlineTimeByDemand = '';
        }

        //期望完成时间
        if($requirement->deadLine == '0000-00-00' || empty($requirement->deadLine)){
            $requirement->deadLine = '';
        }else{
            $requirement->deadLine = date('Y-m-d',strtotime($requirement->deadLine));
        }

        //计划完成时间
        if($requirement->end == '0000-00-00' || empty($requirement->end))
        {
            $requirement->end = '';
        }else{
            $requirement->end = date('Y-m-d',strtotime($requirement->end));
        }

        //任务接收时间
        if($requirement->acceptTime == '0000-00-00 00:00:00'){
            $requirement->acceptTime = '';
        }
        if(in_array($requirement->status,['delivered','onlined'])){
            $requirement->dealUser = '';
        }
        $requirement = $this->loadModel('file')->replaceImgURL($requirement, 'analysis,handling,implement,comment');

        if($this->app->openApp == 'product')
        {
            $this->app->openApp = 'backlog';
            $productID = $requirement->product;
            $branch    = 0;
            $this->commonAction($productID, $branch);
        }

        //需求任务的所属项目取需求条目的并集 project字段存在  xxx,xxx 需单独处理 迭代二十五要求年度计划与需求条目并集
        $demands = $this->loadModel('requirementinside')->getDemandByRequirement($requirementID);
        $ownProjectArr = !empty($requirement->project) ? explode(',',$requirement->project): [];
        $demandProjectArr = array_column($demands,'project');
        $mergeProjectArr = array_merge($ownProjectArr,$demandProjectArr);
        /**@var projectPlanModel $projectPlanModel */
        $projectPlanModel = $this->loadModel('projectplan');
        $projectArray = array_filter(array_unique($mergeProjectArr));
        $projectList = [];
        if(!empty($projectArray)){
            $projectList = $projectPlanModel->getPlanInProjectIDs($projectArray);
        }
        $lastTaskTime = '';
        $productIds = '';
        $lastEndTime = '';
        $apps = '';

        foreach ($demands as $demand){
            //研发部门，研发责任人根据需求条目获取

            if(empty($lastTaskTime)){
                $lastTaskTime = $demand->onlineDate;
            }else{
                if(strtotime($lastTaskTime) < strtotime($demand->onlineDate)){
                    $lastTaskTime = $demand->onlineDate;
                }
            }
        }
        $acceptUserArr = array_filter(array_unique(array_column($demands,'acceptUser')));
        $acceptDeptArr = array_filter(array_unique(array_column($demands,'acceptDept')));
        $requirement->owner = implode(',',$acceptUserArr);
        $requirement->dept = implode(',',$acceptDeptArr);

        //查询需求变更单信息
        /**@var requirementChangeModel $requirementChangeModel*/
        $requirementChangeModel = $this->loadModel('requirementchange');
//        $requirementChangeInfo = $requirementChangeModel->getByDemandNumber($requirement->changeOrderNumber);
        $requirementChangeInfo = $requirementChangeModel->getChangesByEntries($requirement->entriesCode);

        //后台配置挂起人
        $this->loadModel('demand');
        $suspender = array_filter($this->lang->demand->requirementSuspendList);
        $suspendList = [];
        if(!empty($suspender))
        {
            foreach ($suspender as $key=>$value){
                $suspendList[$key] = $key;
            }
        }

        /* 查询需求条目及其相关的信息。*/
        $this->view->executives   = $suspendList ? array_keys($suspendList): [];
        $this->view->title       = $this->lang->requirementinside->view;
        $this->view->users       = $this->loadmodel('user')->getPairs('noletter|noclosed');
        $this->view->actions     = $this->loadmodel('action')->getList('requirement', $requirementID);
        $this->view->requirement = $requirement;
        $this->view->lines       = $this->loadModel('product')->getLinePairs();
        $this->view->products    = $this->product->getPairs();
        $this->view->depts       = $this->loadModel('dept')->getOptionMenu();
        $this->view->opinion     = $this->loadModel('opinioninside')->getByID($requirement->opinion);
        $this->view->apps        = array('' => '') + $this->loadmodel('application')->getapplicationNameCodePairs();
        $this->view->nodes       = $this->loadModel('review')->getNodes('requirement', $requirementID, $requirement->version);
        $this->view->projectList     = $projectList;

        $this->view->lastTaskTime       = $lastTaskTime;
        $this->view->requirementChangeInfo  = $requirementChangeInfo;
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: close
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:23
     * Desc: This is the code comment. This method is called close.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $requirementID
     */
    public function close($requirementID)
    {
        $requirement = $this->loadModel('requirementinside')->getByID($requirementID);
        if(!empty($_POST))
        {
            $dealcomment = $this->post->dealcomment;
            if(empty($dealcomment)){
                dao::$errors['dealcomment'] = sprintf($this->lang->requirementinside->emptyObject, $this->lang->requirementinside->dealcomment);
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            /* 当请求方式为post时，更新需求条目的状态为关闭。判断所属需求意向下的需求条目都关闭时，关闭需求意向。*/
            $this->dao->update(TABLE_REQUIREMENT)->set('status')->eq('closed')->set('lastStatus')->eq($requirement->status)->set('closedBy')->eq($this->app->user->account)->set('closedDate')->eq(helper::now())->where('id')->eq($requirementID)->exec();

            $demands = $this->loadModel("demandinside")->getByRequirementID($requirementID);
            $data1 = new stdclass();
            foreach($demands as $demand){
                if($demand->status == 'suspend'){
                    continue;
                }
                $data1->lastStatus = $demand->status; // 记录关闭前状态
                $data1->status = 'suspend';
                $data1->closedBy = $this->app->user->account;
                $data1->closedDate = helper::today();
                $this->dao->update(TABLE_DEMAND)
                    ->data($data1)
                    ->where('id')->eq($demand->id)
                    ->exec();
                if(!dao::isError())
                {
                    $this->loadModel('action')->create('demand', $demand->id, 'suspended', $dealcomment);
                    $this->loadModel('consumed')->record('demand', $demand->id, 0, $this->app->user->account, $demand->status, 'suspended');
                }
            }
            $this->loadModel('consumed')->record('requirement', $requirementID, 0, $this->app->user->account, $requirement->status, 'closed', array());
            $this->loadModel('action')->create('requirement', $requirementID, 'suspenditem', $dealcomment);

            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        /* 获取需求条目、操作记录和用户信息。*/
        $this->view->title       = $this->lang->requirementinside->delete;
        $this->view->actions     = $this->loadModel('action')->getList('requirement', $requirementID);
        $this->view->requirement = $requirement;
        $this->view->users       = $this->loadModel('user')->getPairs();
        $this->display();
    }

    public function activate($requirementID)
    {
        $requirement = $this->loadModel('requirementinside')->getByID($requirementID);
        if(!empty($_POST))
        {
            $dealcomment = $this->post->dealcomment;
            if(empty($dealcomment)){
                dao::$errors['dealcomment'] = sprintf($this->lang->requirementinside->emptyObject, $this->lang->requirementinside->dealcomment);
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            /* 当请求方式为post时，更新需求条目的状态为关闭。判断所属需求意向下的需求条目都关闭时，关闭需求意向。*/
            $this->dao->update(TABLE_REQUIREMENT)->set('status')->eq($requirement->lastStatus)->set('activatedBy')->eq($this->app->user->account)->set('activatedDate')->eq(helper::now())->where('id')->eq($requirementID)->exec();

            $demands = $this->loadModel("demand")->getBrowesByRequirementID($requirementID);
            $data = new stdclass();
            foreach($demands as $demand){
                if($demand->status != 'suspend'){
                    continue;
                }
                $data->status = $demand->lastStatus; // 记录关闭前状态
                $data->activatedBy = $this->app->user->account;
                $data->activatedDate = helper::today();
                $this->dao->update(TABLE_DEMAND)
                    ->data($data)
                    ->where('id')->eq($demand->id)
                    ->exec();;
                if(!dao::isError())
                {
                    $this->loadModel('action')->create('demand', $demand->id, 'activated', $dealcomment);
                    $this->loadModel('consumed')->record('demand', $demand->id, 0, $this->app->user->account, 'suspend', $demand->lastStatus);
                }
            }

            $this->loadModel('consumed')->record('requirement', $requirementID, 0, $this->app->user->account, $requirement->status, $requirement->lastStatus, array());
            $this->loadModel('action')->create('requirement', $requirementID, 'activate', $dealcomment);

            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        /* 获取需求条目、操作记录和用户信息。*/
        $this->view->title       = $this->lang->requirementinside->activate;
        $this->view->actions     = $this->loadModel('action')->getList('requirement', $requirementID);
        $this->view->requirement = $requirement;
        $this->view->users       = $this->loadModel('user')->getPairs();
        $this->display();
    }

    public function recover($requirementID)
    {
        $requirement = $this->loadModel('requirementinside')->getByID($requirementID);
        if(!empty($_POST))
        {
            $dealcomment = $this->post->dealcomment;
            if(strstr($requirement->ignoredBy, $this->app->user->account) !== false){
                $ignoredByArray = explode("," , $requirement->ignoredBy);
                $key=array_search($this->app->user->account,$ignoredByArray,true);
                unset($ignoredByArray[$key]);
                $ignoredBy = trim(implode(",", $ignoredByArray),",");
            }else{
                $ignoredBy = $requirement->ignoredBy;
            }

            if(strstr($requirement->recoveryedBy, $this->app->user->account) !== false){
                $recoveryedBy = $requirement->recoveryedBy;
            }else{
                $recoveryedByArray = explode("," , $requirement->recoveryedBy);
                array_push($recoveryedByArray, $this->app->user->account);
                $recoveryedBy = trim(implode(",", $recoveryedByArray),",");
            }
            $this->dao->update(TABLE_REQUIREMENT)->set('ignoreStatus')->eq(0)->set('ignoredBy')->eq($ignoredBy)->set('recoveryedBy')->eq($recoveryedBy)->set('recoveryedDate')->eq(helper::now())->where('id')->eq($requirementID)->exec();
            $this->loadModel('action')->create('requirement', $requirementID, 'recoveryed', $dealcomment);
            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        /* 获取需求条目、操作记录和用户信息。*/
        $this->view->title       = $this->lang->requirementinside->recover;
        $this->view->actions     = $this->loadModel('action')->getList('requirement', $requirementID);
        $this->view->requirement = $requirement;
        $this->view->users       = $this->loadModel('user')->getPairs();
        $this->display();
    }

    public function ignore($requirementID, $notice = 0)
    {
        $requirement = $this->loadModel('requirementinside')->getByID($requirementID);
        if(!empty($_POST))
        {
            $dealcomment = $this->post->dealcomment;

            /* 当请求方式为post时，更新需求条目的状态为关闭。判断所属需求意向下的需求条目都关闭时，关闭需求意向。*/

            if(strstr($requirement->ignoredBy, $this->app->user->account) !== false){
                $ignoredBy = $requirement->ignoredBy;
            }else{
                $ignoredByArray = explode("," , $requirement->ignoredBy);
                array_push($ignoredByArray, $this->app->user->account);
                $ignoredBy = trim(implode(",", $ignoredByArray),",");
            }

            if(strstr($requirement->recoveryedBy, $this->app->user->account) !== false){
                $recoveryedByArray = explode("," , $requirement->recoveryedBy);
                $key=array_search($this->app->user->account,$recoveryedByArray,true);
                unset($recoveryedByArray[$key]);
                $recoveryedBy = trim(implode(",", $recoveryedByArray),",");
            }else{
                $recoveryedBy = $requirement->recoveryedBy;
            }

            $this->dao->update(TABLE_REQUIREMENT)->set('ignoreStatus')->eq(1)->set('recoveryedBy')->eq($recoveryedBy)->set('ignoredBy')->eq($ignoredBy)->set('ignoredDate')->eq(helper::now())->where('id')->eq($requirementID)->exec();
            $this->loadModel('action')->create('requirement', $requirementID, 'ignore', $dealcomment);
            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        /* 获取需求条目、操作记录和用户信息。*/
        $this->view->title       = $this->lang->requirementinside->ignore;
        $this->view->actions     = $this->loadModel('action')->getList('requirement', $requirementID);
        $this->view->requirement = $requirement;
        $this->view->notice = $notice;
        $this->view->users       = $this->loadModel('user')->getPairs();
        $this->display();
    }

    /**
     * Delete a requirement.
     *
     * @param  int    $requirementID
     * @access public
     * @return void
     */
    public function delete($requirementID)
    {
        /* 当请求方式为post时，更新需求条目的状态为删除，并记录操作动作。*/
        if(!empty($_POST))
        {
            $dealcomment = $this->post->dealcomment;
            if(empty($dealcomment)){
                dao::$errors['dealcomment'] = sprintf($this->lang->requirementinside->emptyObject, $this->lang->requirementinside->dealcomment);
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $requirement = $this->loadModel('requirementinside')->getByID($requirementID);
            $this->dao->update(TABLE_REQUIREMENT)
            ->set('status')->eq('deleted')
            ->where('id')->eq($requirementID)->exec();
            $this->loadModel('action')->create('requirement', $requirementID, 'deleted', $dealcomment);
            $this->loadModel('consumed')->record('requirement', $requirementID, 0, $this->app->user->account, $requirement->status, 'deleted', array());
            $demands = $this->loadModel("demandinside")->getBrowesByRequirementID($requirementID);
            $data1 = new stdclass();
            foreach($demands as $demand){
                $data1->status = 'deleted'; // 记录关闭前状态
                $this->dao->update(TABLE_DEMAND)
                    ->data($data1)
                    ->where('id')->eq($demand->id)
                    ->exec();;
                if(!dao::isError())
                {
                    $this->loadModel('action')->create('demand', $demand->id, 'deleted', $dealcomment);
                    $this->loadModel('consumed')->record('demand', $demand->id, 0, $this->app->user->account, $demand->status, 'deleted');
                }
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        /* 获取需求条目信息、操作记录和用户信息。*/
        $requirement = $this->loadModel('requirementinside')->getByID($requirementID);
        $this->view->title       = $this->lang->requirementinside->delete;
        $this->view->actions     = $this->loadModel('action')->getList('requirement', $requirementID);
        $this->view->requirement = $requirement;
        $this->view->users       = $this->loadModel('user')->getPairs();
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: matrix
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:23
     * Desc: This is the code comment. This method is called matrix.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param string $status
     * @param string $begin
     * @param string $end
     */
    public function matrix($status = 'noclosed', $begin = '', $end = '')
    {
        $begin_export=$begin;
        $end_export=$end;
        $begin = $begin ? date('Y-m-d', strtotime($begin)) : date('Y-01-01');
        $end   = $end   ? date('Y-m-d', strtotime($end)) : date('Y-12-31');

        /* 该方法主要是展示数据用，查询符合筛选条件内的所有需求意向、以及它拆分的需求条目、需求条目关联的项目、产品、应用系统等。*/
        $this->loadModel('demandinside');
        $this->view->title    = $this->lang->requirementinside->matrixTitle;
        $this->view->opinions = $this->loadModel('opinioninside')->getList($status, 0, 'id_desc', 'null', 'nodeleted', $begin, $end);
        $this->view->progress = $this->opinioninside->getProgress($this->view->opinions);
        $this->view->projects = $this->loadModel('projectplan')->getPairs();
        $this->view->products = $this->loadModel('product')->getPairs();
        //$this->view->codes    = $this->product->getModifyCodePairs();
        $this->view->codes    = $this->product->getModifyProjectLinkProduct($this->view->opinions);
        $this->view->lines    = $this->product->getLinePairs();
        $this->view->status   = $status;
        $this->view->begin    = $begin;
        $this->view->end      = $end;
        $this->view->begin_export=$begin_export;
        $this->view->end_export=$end_export;
        $this->view->apps     = $this->loadModel('application')->getPairs();
        $this->session->set('common_back_url', $this->app->getURI(true),'backlog');

        $this->display();
    }

    public function commonAction($productID, $branch)
    {
        $this->loadModel('product')->setMenu($productID, $branch);

        $product = $this->product->getById($productID);
        //if(empty($product)) $this->locate($this->createLink('product', 'create'));
        $this->view->product    = $product;
        $this->view->branch     = $branch;
        $this->view->branches   = $product->type == 'normal' ? array() : $this->loadModel('branch')->getPairs($product->id);
        $this->view->productID  = $productID;
    }

     /**
     * Project: chengfangjinke
     * Method: export
     * author: 齐京望
     * Year: 2021
     * Date: 2021/12/2
     * Time: 17:23
     * Desc: 该功能目前主要用于导出总行的需求模块数据
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param string $status
     * @param string $begin
     * @param string $end
     */
     public function export($orderBy = 'id_desc', $browseType = 'all')
     {
         if($_POST)
         {
            $this->loadModel('file');
            $requirementLang   = $this->lang->requirementinside;
            $requirementConfig = $this->config->requirementinside;
            $this->app->loadLang("opinioninside");
            $fields = $this->post->exportFields ? $this->post->exportFields : explode(',', $requirementConfig->exportlist->exportFields);
            foreach($fields as $key => $fieldName)
            {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = isset($requirementLang->$fieldName) ? $requirementLang->$fieldName : $fieldName;
                unset($fields[$key]);
            }
            $requirements = array();
            if($this->session->requirementinsideOnlyCondition)
            {
                $requirements = $this->dao->select('*')->from(TABLE_REQUIREMENT)->where($this->session->requirementinsideQueryCondition)
                    ->beginIF($this->post->exportType == 'selected')->andWhere('id')->in($this->cookie->checkedItem)->fi()
                    ->orderBy($orderBy)->fetchAll('id');
            }
            else
            {
                $stmt = $this->dbh->query($this->session->requirementinsideQueryCondition . (($this->post->exportType == 'selected' and $this->cookie->checkedItem) ? " AND t1.id IN({$this->cookie->checkedItem})" : '') . " ORDER BY " . strtr('t1.'.$orderBy, '_', ' '));
                while($row = $stmt->fetch()) $requirements[$row->id] = $row;
            }
            $users = $this->loadModel('user')->getPairs('noletter');
            $this->app->loadLang("opinion");
            $apps = $this->loadModel('application')->getPairs();
            $lines       = $this->loadModel('product')->getLinePairs();
            $products    = $this->product->getPairs();
            $depts       = $this->loadModel('dept')->getOptionMenu();
            $projects    = $this->loadModel('projectplan')->getPairs();
             foreach ($requirements as $requirement){
                 $opinion = $this->loadModel("opinioninside")->getByID($requirement->opinion);
                 $demands = $this->loadModel('demandinside')->getBrowesByRequirementID($requirement->id);
                 $demandsOther = $this->loadModel('requirementinside')->getDemandByRequirement($requirement->id);
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
                 $requirement->owner = zmget($users,implode(',',$acceptUserArr));
                 $requirement->dept = zmget($depts,implode(',',$acceptDeptArr));

                 //外部同步需求任务接收时间取创建时间，内部需求任务取需求意向接收时间
                 if($requirement->createdBy == 'guestcn'){
                     $requirement->acceptTime = $requirement->createdDate;
                 }else{
                     $opinionInfo = $this->loadModel('opinioninside')->getByID($requirement->opinion);
                     $requirement->acceptTime = $opinionInfo->receiveDate ?? '';
                 }

                 foreach($demands as $demand){
                    $requirement->demands .= $demand->code . PHP_EOL;
                 }
                 $requirement->ID = $requirement->id;
                 $dealUserList = explode("," , $requirement->dealUser);
                 $dealUserChnList = array();
                 foreach ($dealUserList as $dealUser){
                     $dealUserChn = zget($users, $dealUser, '');
                     array_push($dealUserChnList, $dealUserChn);
                 }
                 $requirement->dealUser = implode(",", $dealUserChnList);
                 if(in_array($requirement->status,['delivered','onlined'])){
                     $requirement->dealUser = '';
                 }
                 $requirement->status = zget($requirementLang->statusList, $requirement->status, '');
                 $requirement->opinion = $opinion->code;
                 $requirement->sourceMode = zget($this->lang->opinion->sourceModeList, $opinion->sourceMode, '');
                 $requirement->method = zget($requirementLang->methodList, $requirement->method, '');
                 $unionList = explode("," , $opinion->union);
                 $unionChnList = array();
                 foreach ($unionList as $union){
                     $unionChn = zget($this->lang->opinion->unionList, $union, '');
                     array_push($unionChnList, $unionChn);
                 }
                 $requirement->union = implode(",", $unionChnList);
                 $appList = explode("," , $requirement->app);
                 $appChnList = array();
                 foreach ($appList as $app){
                     $appChn = zget($apps, $app, '');
                     array_push($appChnList, $appChn);
                 }
                 $requirement->app = implode(",", $appChnList);
                 $productManagerList = explode("," , $requirement->productManager);
                 $productManagerChnList = array();
                 foreach ($productManagerList as $productManager){
                     $productManagerChn = zget($users, $productManager, '');
                     array_push($productManagerChnList, $productManagerChn);
                 }
                 $requirement->productManager = implode(",", $productManagerChnList);

                 $projectManagerList = explode("," , $requirement->projectManager);
                 $projectManagerChnList = array();
                 foreach ($projectManagerList as $projectManager){
                     $projectManagerChn = zget($users, $projectManager, '');
                     array_push($projectManagerChnList, $projectManagerChn);
                 }
                 $requirement->projectManager = implode(",", $projectManagerChnList);

                 $feedbackDealUserList = explode("," , $requirement->feedbackDealUser);
                 $feedbackDealUserChnList = array();
                 foreach ($feedbackDealUserList as $feedbackDealUser){
                     $feedbackDealUserChn = zget($users, $feedbackDealUser, '');
                     array_push($feedbackDealUserChnList, $feedbackDealUserChn);
                 }
                 $requirement->feedbackDealUser = implode(",", $feedbackDealUserChnList);

                 $requirement->feedbackStatus = zget($requirementLang->feedbackStatusList, $requirement->feedbackStatus, '');

                 $lineList = explode("," , $requirement->line);
                 $lineChnList = array();
                 foreach ($lineList as $line){
                     $lineChn = zget($lines, $line, '');
                     array_push($lineChnList, $lineChn);
                 }
                 $requirement->line = implode(",", $lineChnList);

                 $productList = explode("," , $requirement->product);
                 $productChnList = array();
                 foreach ($productList as $product){
                     $productChn = zget($products, $product, '');
                     array_push($productChnList, $productChn);
                 }
                 $requirement->product = implode(",", $productChnList);
                 $requirement->desc = strip_tags($requirement->desc);
                 $requirement->createdBy = zget($users, $requirement->createdBy, '');
                 $requirement->editedBy = zget($users, $requirement->editedBy, '');
                 $requirement->closedBy = zget($users, $requirement->closedBy, '');
                 $requirement->activatedBy = zget($users, $requirement->activatedBy, '');
                 $requirement->ignoredBy = zget($users, $requirement->ignoredBy, '');
                 $requirement->recoveryedBy = zget($users, $requirement->recoveryedBy, '');
                 $requirement->feedbackBy = zget($users, $requirement->feedbackBy, '');

                 $projectList = explode("," , $requirement->project);
                 $projectChnList = array();
                 foreach ($projectList as $project){
                     $projectChn = zget($projects, $project, '');
                     array_push($projectChnList, $projectChn);
                 }
                 $requirement->project = implode(",", $projectChnList);
             }
             $this->post->set('fields', $fields);
             $this->post->set('rows', $requirements);
             $this->post->set('kind', 'requirementinside');
             $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
         }


         $this->view->fileName        = $this->lang->requirementinside->exportName;
         $this->view->allExportFields = $this->config->requirementinside->exportlist->exportFields;
         $this->view->customExport    = true;
         $this->display();
     }

    /**
     * Project: chengfangjinke
     * Method: ajaxGetProjects
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:23
     * Desc: This is the code comment. This method is called ajaxGetProjects.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $projectID
     */
    public function ajaxGetProjects($projectID)
    {
        /* 调用projectplan模块获取项目计划键值对，返回select数据。*/
        $projects = $this->loadModel('projectplan')->getPairs();
        die(html::select('project', $projects, $projectID, "class='form-control chosen'"));
    }

    /**
     * Project: chengfangjinke
     * Method: ajaxGetProducts
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:23
     * Desc: This is the code comment. This method is called ajaxGetProducts.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $productID
     */
    public function ajaxGetProducts($productID)
    {
        $products = $this->loadModel('product')->getPairs();
        die(html::select('product', $products, $productID, "class='form-control chosen' multiple"));
    }

    /**
     * Project: chengfangjinke
     * Method: ajaxGetLines
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:23
     * Desc: This is the code comment. This method is called ajaxGetLines.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     */
    public function ajaxGetLines()
    {
        /* 查询所有的产品计划数据，返回select数据。*/
        $lines = $this->dao->select('id,name')->from(TABLE_MODULE)
            ->where('type')->eq('line')
            ->andWhere('deleted')->eq(0)
            ->orderBy('id_desc')
            ->fetchPairs();

        die(html::select('line', $lines, key($lines), "class='form-control chosen'"));
    }

    /**
     * Project: chengfangjinke
     * Method: ajaxGetApps
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:23
     * Desc: This is the code comment. This method is called ajaxGetApps.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $appID
     */
    public function ajaxGetApps($appID)
    {
        /* 调用application模块的getPairs方法获取应用系统键值对数据。*/
        $apps = $this->loadModel('application')->getPairs();
        die(html::select('app', $apps, $appID, "class='form-control chosen'"));
    }

    /**
     * 指派需求任务
     *
     * @param  int    $requirementID
     * @access public
     * @return void
     */
    public function assignTo($requirementID)
    {
        /* 当请求方式为post时，调用confirm方法处理确认逻辑，处理成功则记录确认动作，响应成功信息。*/
        if($_POST)
        {
            $changes = $this->loadModel('requirementinside')->assignTo($requirementID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $actionID = $this->loadModel('action')->create('requirement', $requirementID, 'assigned', $this->post->comment, $this->post->assignedTo);
            $this->action->logHistory($actionID, $changes);

            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        /* 获取需求条目信息和相关操作记录信息。*/
        $this->view->title       = $this->lang->requirementinside->assigned;
        $this->view->requirement = $this->loadModel('requirementinside')->getByID($requirementID);
        $this->view->users = $this->loadModel('user')->getPairs('noclosed');
        $this->view->actions     = $this->loadmodel('action')->getList('requirement', $requirementID);
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: subdivide
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:49
     * Desc: This is the code comment. This method is called subdivide.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $requirementID
     */
    public function subdivide($requirementID)
    {
        $this->app->loadLang('demandinside');
        //清总同步的数据 校验需求意向的【下一节点处理人】字段以及【需求分类】是否为空，为空则弹框提示
        $requirement = $this->loadModel('requirementinside')->getByID($requirementID);
        $opinion = $this->loadModel('opinioninside')->getByID($requirement->opinion);
        if($requirement->createdBy == 'guestcn'){
            if(empty($opinion->category)){
                $response['result']  = 'fail';
                $response['message'] = '所属意向未补充完整请联系产品经理进行补充';
                $this->send($response);
            }
        }

        if($_POST){
            $this->loadModel('requirementinside')->subdivideDemand($requirementID);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = $this->createLink('requirementinside', 'view', array('requirementID' => $requirementID));
            $this->send($response);
        }

        $this->view->title   = $this->lang->requirementinside->subdivide;

        if($requirement->end === '0000-00-00') $requirement->end = '';
        if($requirement->analysis === NULL) $requirement->analysis = '';
        $this->view->requirement = $requirement;
        $this->view->users   = $this->loadModel('user')->getPairs('noclosed|nodeleted');
//        $this->view->apps = array('' => '')  + $this->loadModel('application')->getapplicationNameCodePairs();
        $this->view->appAll = $this->loadModel('application')->getapplicationNameCodePairsWithisPayment();
        $this->view->apps = array('' => '') + array_column($this->view->appAll, 'name', 'id');
        $this->view->productList   = array('0' => '', '99999' => '无');//array('0' => '', '99999' => '无') + $this->loadModel('product')->getProductWithCodeName('noclosed');
        //根据项目实现和二线实现，默认获取对应所属项目list
        $this->view->plans      = array('' => '') + $this->loadModel('projectplan')->getAliveProjects(false);
        $executions = array('' => '');
        $this->view->executions       = $executions;
        $this->view->fixType          = '';
        $this->display();
    }

    public function ajaxGetSecondLine($fixType)
    {
        $secondLineType = $fixType == 'patch';
        $projects = array('' => '') +  $this->loadModel('projectplan')->getAliveProjects($secondLineType);
        echo html::select('project', $projects, '',"class='form-control chosen'");
    }

    public function ajaxGetProductPlan($productID = 0, $orderBy = 'id_desc')
    {
        $plans = $this->loadModel('productplan')->getPairs($productID, 0);

        $planName = 'productPlan[]';
        $plans    = empty($plans) ? array('0' => '','1' => '无') : array('0' => '','1' => '无') + $plans;
        echo html::select($planName, $plans, '', "class='form-control versionClass chosen'");
    }

     /**
      * 获取产品
     */
     public function ajaxGetProduct()
      {
        $products = array('0' => '无') + $this->loadModel('product')->getPairs();
        $productName = 'demandProduct[]';
        $products    = empty($products) ? array('' => '') : $products;
        echo html::select($productName, $products, ' ', "class='form-control chosen demandProductClass' onchange='selectProduct(this.id, this.value)'");
     }

     /**
      * 获取产品
     */
     public function ajaxGetProductCode($app,$data_id = 0)
      {
          $products = $app ? array('0' => '','99999'=>'无') + $this->loadModel('product')->getCodeNamePairsByApp($app) :array('0' => '','99999'=>'无');
          $productName = 'product';
          echo html::select($productName.$data_id, $products, '', "class='form-control chosen productClass' onchange='selectProduct(this.id, this.value)'");
     }

    /**
     * 导出模板
     * @return void
     */
    public function exportTemplate()
    {
        /* 调用导出模板页面，如果是post请求，将调用setListValue方法处理多选字段的值，然后设置导出的相关信息，调用file模块的export2方法进行导出模板处理。*/
        if($_POST)
        {
            // $this->loadModel('requirementinside')->setListValue();

            $fields = [];
            $templateFields = $this->config->requirementinside->exportlist->templateFields;
            foreach($templateFields as $field){
                $fields[$field] = $this->lang->requirementinside->$field;
            }
            $num = $this->post->num;
            $rows =array();
            $dealArray = [];
            for ($i=0;$i < $num;$i++){
                foreach ($templateFields as $v){
                    $dealArray[$v] = '';
                    switch ($v){
                        case 'onlineTimeByDemand':
                        // case 'deadLineDate':
                            $dealArray[$v] = '0000-00-00';
                            break;
                        default:
                            break;
                    }
                }
                $rows[$i] = (object)$dealArray;
            }
            $this->post->set('fields', $fields);
            $this->post->set('kind', '需求任务');
            $this->post->set('rows', $rows);
            $this->post->set('fileName', '需求任务模板'); 
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }
        $this->display();
    }

    /**
     * 导入
     * @return void
     * @throws PHPExcel_Reader_Exception
     */
    public function import()
    {
        if($_FILES)
        {
            /* 如果文件存在，则判断文件类型是否符合要求。*/
            $file = $this->loadModel('file')->getUpload('file');
            $file = $file[0];
            if($file['extension'] != 'xlsx') die(js::alert($this->lang->file->onlySupportXLSX));

            /* 将导入的文件存放于临时目录。*/
            $fileName = $this->file->savePath . $this->file->getSaveName($file['pathname']);
            move_uploaded_file($file['tmpname'], $fileName);

            /* 加载phpexcel库，解析excel文件内容，解析完调用showImport方法进行数据确认。*/
            $phpExcel  = $this->app->loadClass('phpexcel');
            $phpReader = new PHPExcel_Reader_Excel2007();
            if(!$phpReader->canRead($fileName))
            {
                $phpReader = new PHPExcel_Reader_Excel5();
                if(!$phpReader->canRead($fileName))die(js::alert($this->lang->excel->canNotRead));
            }
            $this->session->set('fileImport', $fileName);
            die(js::locate(inlink('showImport'), 'parent.parent'));
        }

        $this->display();
    }

    /**
     * 导入数据
     * @param $pagerID
     * @param $maxImport
     * @param $insert
     * @return void
     */
    public function showImport($pagerID = 1, $maxImport = 0, $insert = '')
    {
        /* 获取import方法导入的临时文件。*/
        $file    = $this->session->fileImport;
        $tmpPath = $this->loadModel('file')->getPathOfImportedFile();
        $tmpFile = $tmpPath . DS . md5(basename($file));

        /* 如果是post请求，则调用createFromImport方法保存导入的数据。如果是最后一页则跳转列表，否则跳转下一页数据。*/
        if($_POST)
        {
            $this->loadModel('requirementinside')->createFromImport();
            if($this->post->isEndPage)
            {
                unlink($tmpFile);
                die(js::locate($this->createLink('requirementinside','browse'), 'parent'));
            }
            else
            {
                die(js::locate(inlink('showImport', "pagerID=" . ($this->post->pagerID + 1) . "&maxImport=$maxImport&insert=" . zget($_POST, 'insert', '')), 'parent'));
            }
        }

        /* 如果最大导入数量不为空，且导入文件存在，则获取文件内容进行序列化。*/
        if(!empty($maxImport) and file_exists($tmpFile))
        {
            $requirementData = unserialize(file_get_contents($tmpFile));
        }
        else
        {
            /* 初始化变量，获取要导入的字段。*/
            $pagerID       = 1;
            $requirementLang   = $this->lang->requirementinside;
            $requirementConfig = $this->config->requirementinside;
            $fields        = $requirementConfig->exportlist->templateFields;
            $fields[]      = 'workload';
            foreach($fields as $key => $fieldName)
            {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = isset($requirementLang->$fieldName) ? $requirementLang->$fieldName : $fieldName;
                unset($fields[$key]);
            }

            /* 获取导入文件所有行的数据。*/
            $rows = $this->file->getRowsFromExcel($file);
            $requirementData = array();
            $users = $this->loadModel('user')->getPairs('noletter|noclosed');
            $apps = $this->loadModel('application')->getapplicationNameCodePairs();
            $appNames = $this->loadModel('application')->getapplicationNamePairs();
            $opinions = $this->loadModel('opinioninside')->getPairsByRequmentBrowse();
            foreach($rows as $currentRow => $row)
            {
                $requirement = new stdclass();
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
                        $requirement->$field = '';
                        continue;
                    }

                    //if(in_array($field, $opinionConfig->import->ignoreFields)) continue;
                    /* 针对下拉选项字段进行处理，然后赋值转换。*/
                    if(in_array($field, $requirementConfig->exportlist->listFields))
                    {
                        $requirement->$field = $cellValue;
                        if($field == 'opinionID'){
                            $fieldKey = array_search($cellValue, $opinions)?array_search($cellValue, $opinions):'';
                        }elseif(in_array($field, array('createdBy','projectManager','dealUser'))){
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
                            if(!isset($requirementLang->{$field . 'List'}) or !is_array($requirementLang->{$field . 'List'})) continue;
                            /* when the cell value is key of list then eq the key. */
                            $listKey = array_keys($requirementLang->{$field . 'List'});
                            unset($listKey[0]);
                            unset($listKey['']);
                            $fieldKey =  array_search($cellValue, $requirementLang->{$field . 'List'});
                        }
                        if($fieldKey) $requirement->$field = $fieldKey;
                    }
                    elseif($field == 'background' or $field == 'overview' or $field == 'desc')
                    {
                        /* 针对富文本类型字段内容进行处理。*/
                        $requirement->$field = str_replace("\n", "\n", $cellValue);
                    }
                    else
                    {
                        $requirement->$field = $cellValue;
                    }
                }

                if(empty($requirement->name)) continue;
                $requirementData[$currentRow] = $requirement;
                unset($requirement);
            }
            /* 获取处理好的数据后，写入临时文件中。*/
            file_put_contents($tmpFile, serialize($requirementData));
        }

        /* 当导入文件的内容处理完成后，删除临时文件，并刷新列表页面。*/
        if(empty($requirementData))
        {
            unlink($this->session->fileImport);
            unset($_SESSION['fileImport']);
            echo js::alert($this->lang->excel->noData);
            die(js::locate($this->createLink('requirementinside','browse')));
        }

        /* 判断导入的数据是否大于系统预设最大导入数，如果大于则对数据进行拆分处理。*/
        $allCount = count($requirementData);
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
            $requirementData = array_slice($requirementData, ($pagerID - 1) * $maxImport, $maxImport, true);
        }
        if(empty($requirementData)) die(js::locate($this->createLink('requirementinside','browse')));

        /* Judge whether the editedStories is too large and set session. */
        /* 判断要处理的需求意向是否太大，并设置session。*/
        $countInputVars  = count($requirementData) * 11;
        $showSuhosinInfo = common::judgeSuhosinSetting($countInputVars);
        if($showSuhosinInfo) $this->view->suhosinInfo = extension_loaded('suhosin') ? sprintf($this->lang->suhosinInfo, $countInputVars) : sprintf($this->lang->maxVarsInfo, $countInputVars);

        /* 将要导入的数据及其相关变量，传递到页面进行展示。*/
        $this->view->title      = $this->lang->requirementinside->common . $this->lang->colon . $this->lang->requirementinside->showImport;
        $this->view->position[] = $this->lang->requirementinside->showImport;


        $this->view->statusList = $this->lang->requirementinside->searchstatusList;
        $this->view->requirementData = $requirementData;
        $this->view->allCount    = $allCount;
        $this->view->allPager    = $allPager;
        $this->view->pagerID     = $pagerID;
        $this->view->isEndPage   = $pagerID >= $allPager;
        $this->view->maxImport   = $maxImport;
        $this->view->dataInsert  = $insert;
        $this->view->users       = $this->loadModel('user')->getPairs('noclosed');
        $opinions = array('0' => '') + $this->loadModel('opinioninside')->getPairsByRequmentBrowse();
        $this->view->opinions     = $opinions;
        $this->view->apps = array('0' => '') + $this->loadModel('application')->getapplicationNameCodePairs();
        $this->display();
    }

    /**
     * @Notes:内部自建任务编辑计划完成时间
     * @Date: 2024/4/7
     * @Time: 15:58
     * @Interface editEnd
     * @param $requirementID
     */
    public function editEnd($requirementID)
    {
        $requirement = $this->requirementinside->getByID($requirementID);
        if($requirement->end == '0000-00-00' || empty($requirement->end))
        {
            if($requirement->deadLine == '0000-00-00 00:00:00' || $requirement->deadLine == '0000-00-00' || empty($requirement->deadLine))
            {
                $requirement->end = '';
                if($requirement->createdBy == 'guestcn'){
                    $opinionInfo = $this->loadModel('opinion')->getByID($requirement->opinion);
                    $requirement->end = date('Y-m-d',strtotime($opinionInfo->deadline));
                }
            }else{
                $requirement->end = date('Y-m-d',strtotime($requirement->deadLine));
            }
        }

        if($_POST)
        {
            $info = $this->requirementinside->editEnd($requirement);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $actionID = $this->loadModel('action')->create('requirement', $requirementID, 'editend');
            $this->action->logHistory($actionID, $info);

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }

        $this->view->requirement = $requirement;
        $this->display();
    }

    /**
     * 同步失败重新推送
     * @param $id
     */
    public function push($id)
    {
        $this->dao->update(TABLE_REQUIREMENT)->set('feedbackStatus')->eq('toexternalapproved')->where('id')->eq($id)->exec();
        $this->loadModel('requirementinside')->pushfeedback($id);
        $response['result']  = 'success';
        $response['message'] = '重新推送';
        $response['locate']  = $this->createLink('requirement', 'view', array('requirementID' => $id));
        $this->loadModel('action')->create('requirement', $id, 'repush', "重新推送");
        die(js::locate($this->createLink('requirement', 'view', "requirementID=$id"), 'parent.parent'));
    }
}