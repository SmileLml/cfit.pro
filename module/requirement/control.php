<?php
class requirement extends control
{
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
        $browseType = strtolower($browseType);
        $queryID    = ($browseType == 'bysearch') ? (int)$param : 0;
        $actionURL  = $this->createLink('requirement', 'browse', "browseType=bySearch&param=myQueryID");

        /* 处理搜索字段的赋值。*/
        $apps = $this->loadModel('application')->getapplicationNameCodePairs();
        $this->config->requirement->search['params']['app']['values']         = array('0' => '') + $this->loadModel('application')->getapplicationNameCodePairs();

        $depts = $this->loadModel('dept')->getOptionMenu();
        $this->config->requirement->search['params']['dept']['values'] = $depts;
        $this->app->loadLang("opinion");
        $this->config->requirement->search['params']['sourceMode']['values'] = $this->lang->opinion->sourceModeListOld;
        $this->config->requirement->search['params']['union']['values'] = $this->lang->opinion->unionList;

        if(isset($this->lang->requirement->feedbackOverErList[$this->app->user->account]) || $this->app->user->account == 'admin'){
            $this->config->requirement->search['fields']['feedbackOver'] = $this->lang->requirement->feedbackOver;
            $this->config->requirement->search['params']['feedbackOver']     = ['operator' => '=', 'control' => 'select', 'values' => $this->lang->requirement->feedbackOverList];
        }

        $this->requirement->buildSearchForm($queryID, $actionURL);

        /* Load pager. */
        /* 初始化分页对象。*/
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);
        $this->session->set('requirementList', $this->app->getURI(true),'backlog');
        $requirements = $this->requirement->getList($browseType, $queryID, $orderBy, $pager);
        foreach ($requirements as $requirement){
            $dealUserArray = explode("," , $requirement->dealUser);
            $feedbackDealUserArray = explode("," , $requirement->feedbackDealUser);
            $dealUserArray = array_merge($dealUserArray, $feedbackDealUserArray);
            //迭代二十八 待处理人拼接变更单待处理人共同显示
            if(!empty($requirement->changeDealUser)){
                $opinionChangeDealUser = explode(',',$requirement->changeDealUser);
                $dealUserArray = array_merge($dealUserArray,$opinionChangeDealUser);
            }
            $dealUserArray = array_unique($dealUserArray);
            $requirement->reviewer = implode(",", $dealUserArray);

            if(in_array($requirement->status,['closed','deleteout']))
            {
                $requirement->reviewer = '';
            }
            if(in_array($requirement->status,['delivered','onlined']))
            {
                if($requirement->createdBy == 'guestcn'){
                    $requirement->reviewer = implode(",", $feedbackDealUserArray);
                }else{
                    $requirement->reviewer = '';
                    if($browseType == 'assigntome' && empty($requirement->changeDealUser))
                    {
                        unset($requirements[$requirement->id]);
                    }
                }
            }
            //期望完成时间
            if($requirement->deadLine == '0000-00-00' || empty($requirement->deadLine)){
                $requirement->deadLine = '';
                if($requirement->createdBy == 'guestcn'){
                    $opinionInfo = $this->loadModel('opinion')->getByID($requirement->opinion);
                    $requirement->deadLine = $opinionInfo->deadline;
                }
            }

            //计划完成时间
            if($requirement->end == '0000-00-00'){
                $requirement->end = '';
            }

            $demands = $this->loadModel('demand')->getBrowesByRequirementID($requirement->id);
            foreach ($demands as $key => $demand) {
                //开发中 测试中 已发布 已交付 上线成功 处理人置空显示 已关闭也不显示待处理人
                if(in_array($demand->status, ['feedbacked','changeabnormal','chanereturn','delivery','onlinesuccess','closed','deleteout'])){
                    $demands[$key]->dealUser  = '';
                }
                if(isset($dmap[$demand->createdBy])){
                    $demands[$key]->createdDept = $dmap[$demand->createdBy]->dept;
                }
                $demands[$key]->creatorCanEdit = 0;
                if($this->loadModel('demand')->checkCreatorPri($demand)){
                    $demands[$key]->creatorCanEdit = 1;
                }
                $demands[$key]->endDate = '';
                //延期待处理人
                $dealUserList = explode(',', $demand->dealUser);
                if(!empty($demand->delayDealUser)){
                    $delayDealUserList = explode(',', $demand->delayDealUser);
                    foreach ($delayDealUserList as $delayDealUser){
                        if(!in_array($delayDealUser, $dealUserList)){
                            array_push($dealUserList, $delayDealUser);
                        }
                    }

                }
                $demands[$key]->dealUser = implode(',', $dealUserList);
                if($demand->status == 'suspend')
                {
                    $demand->dealUser = '';
                }
            }
            $requirement->children = $demands;
            //需求任务的所属项目取需求条目的并集 project字段存在  xxx,xxx 需单独处理 迭代二十五要求年度计划与需求条目并集
            $demandsOther = $this->requirement->getDemandByRequirement($requirement->id, 'id,project,acceptUser,acceptDept');
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
            //$requirement->owner = implode(',',$acceptUserArr);
            $requirement->dept = implode(',',$acceptDeptArr);

            //变更单下一节点处理人
            $changeInfo = $this->loadModel('requirement')->getPendingOrderByRequirementId($requirement->id);
            $requirement->changeNextDealuser = $changeInfo->nextDealUser ?? '';
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
        $createButton = $this->requirement->checkAuthCreate();
        /* 获取需求条目数据及其相关配合的部门、项目和人员信息。*/
        $this->view->createButton = $createButton;
        $this->view->title        = $this->lang->requirement->common;
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
            $requirementID = $this->requirement->create();

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
        $opinions = array('0' => '') + $this->loadModel('opinion')->getOpinionsPairsByUser();
        $this->view->opinions     = $opinions;
        $this->view->users = $this->loadModel('user')->getPairs('noclosed');
        $this->view->title = $this->lang->requirement->create;
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
            $changes = $this->requirement->update($requirementID);

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

        $opinions = array('0' => '') + $this->loadModel('opinion')->getPairs();
        $requirement = $this->loadModel('requirement')->getByID($requirementID);
        $consumed = $this->loadModel('consumed')->getObjectByID($requirementID,'requirement','published');

        if($requirement->deadLine == '0000-00-00' || empty($requirement->deadLine)){
            $requirement->deadLine = '';
            if($requirement->createdBy == 'guestcn'){
                $opinionInfo = $this->loadModel('opinion')->getByID($requirement->opinion);
                $requirement->deadLine = $opinionInfo->deadline;
            }
        }
        //处理页面只读数据
        $readonly = false;
        if($requirement->createdBy != 'guestcn' && in_array($requirement->status,['underchange','splited']))
        {
            $readonly =  true;
        }elseif ($requirement->createdBy == 'guestcn')
        {
            $readonly =  true;
        }

        $this->view->readonly     = $readonly;
        $this->view->opinions     = $opinions;
        $this->view->users = $this->loadModel('user')->getPairs('noclosed');
        $this->view->title       = $this->lang->requirement->edit;
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
            $changes = $this->requirement->confirm($requirementID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes)
            {
                $actionID = $this->loadModel('action')->create('requirement', $requirementID, 'confirmed');
                $this->loadModel('requirement')->sendmail($requirementID,$actionID);
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        /* 获取需求条目信息和相关操作记录信息。*/
        $this->view->title       = $this->lang->requirement->confirm;
        $this->view->requirement = $this->loadModel('requirement')->getByID($requirementID);
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
            $changes = $this->requirement->feedback($requirementID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes)
            {
                /* 判断是否为推送的需求条目。*/
                /*$newRequirement = $this->requirement->getByID($requirementID);
                $pushEnable     = $this->config->global->pushEnable;
                $pushPrompt     = '';
                if($newRequirement->feedbackCode and $pushEnable == 'enable')
                {
                    $pushPrompt = $this->lang->requirement->pushPrompt;
                }*/
                $actionID = $this->loadModel('action')->create('requirement', $requirementID, 'createfeedback');
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = inlink('view', "requirementID=$requirementID");

            $this->send($response);
        }

        $requirement = $this->loadModel('requirement')->getByID($requirementID);

        /* 此方法需要填写实施部门、归属项目、所属产品线、应用系统、设计产品等，此处代码是获取对应数据到view模板中。*/
        $this->view->title       = $this->lang->requirement->feedback;
        $this->view->requirement = $requirement;
        $this->view->opinion     = $this->loadModel('opinion')->getByID($requirement->opinion);
        $this->view->lines       = array('' => '') + $this->loadModel('product')->getLinePairs();
        $this->view->products    = $this->product->getPairs();
        $this->view->depts       = $this->loadModel('dept')->getOptionMenu();
        $this->view->projects    = array('' => '') + $this->loadModel('projectplan')->getAliveProjects(false);
        $this->view->users       = $this->loadmodel('user')->getPairs('noclosed');
        $this->view->apps        = array('' => '') + $this->loadmodel('application')->getapplicationNameCodePairs();

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
            $oldRequirement = $this->requirement->getByID($requirementID);
            /* 判断需求条目变更时，评审人是否选填了(系统手动拆分的需求条目才判断)。*/
            if(!implode('', $this->post->reviewer) and empty($oldRequirement->entriesCode))
            {
                $response = array();
                $response['result']  = 'fail';
                $response['message'] = $this->lang->requirement->reviewerEmpty;
                $this->send($response);
            }

            /* 当请求方式为post时，调用change方法处理变更逻辑，如果处理成功则记录变更动作，然后返回成功信息。*/
            $changes = $this->requirement->change($requirementID);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes)
            {
                $this->loadModel('action')->create('requirement', $requirementID, 'changed', $this->post->comment);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = inlink('browse');

            $this->send($response);
        }

        $requirement = $this->loadModel('requirement')->getByID($requirementID);

        /* 此方法类似feedback方法，需要填写实施部门、归属项目、所属产品线、应用系统、设计产品等，此处代码是获取对应数据到view模板中。*/
        $this->view->title       = $this->lang->requirement->change;
        $this->view->requirement = $requirement;
        $this->view->opinion     = $this->loadModel('opinion')->getByID($requirement->opinion);
        $this->view->lines       = array('' => '') + $this->loadModel('product')->getLinePairs();
        $this->view->products    = $this->product->getPairs();
        $this->view->depts       = $this->loadModel('dept')->getOptionMenu();
        $this->view->projects    = array(0 => '') + $this->loadModel('projectplan')->getPairs();
        $this->view->users       = $this->loadmodel('user')->getPairs('noclosed');
        $this->view->apps        = array(0 => '') + $this->loadmodel('application')->getapplicationNameCodePairs();

        $this->display('requirement', 'newchange');
    }

    /**
     * @Notes: 编辑变更单
     * @Date: 2023/7/13
     * @Time: 9:39
     * @Interface editchange
     * @param $changeID
     * @param $requirementID
     */
    public function editchange($changeID,$requirementID)
    {
        $this->app->loadLang('opinion');
        if($_POST)
        {
            /* 当请求方式为post时，调用change方法处理变更逻辑，如果处理成功则记录变更动作，然后返回成功信息。*/
            $changes = $this->requirement->editchange($changeID,$requirementID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes)
            {
                $actionID = $this->loadModel('action')->create('requirement', $requirementID, 'editchanged', $this->post->revokeRemark);
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        /**
         * @var deptModel $deptModel
         * @var demandModel $demandModel
         * @var requirementModel $requirementModel
         */
        $requirementModel = $this->loadModel('requirement');
        $deptModel = $this->loadModel('dept');
        $demandModel = $this->loadModel('demand');
        $users = $this->loadModel('user')->getPairs('noletter|noclosed');
        //产品经理
        $poUsers = $deptModel->getPoUser();
        foreach ($poUsers as $name)
        {
            $poUsers[$name] = zget($users,$name,'');
        }

        //部门负责人
        $deptLeaderCN = $deptModel->getFieldByDeptId('id,manager',$this->app->user->dept);

        //需要拼接后台配置人员
        $define = $this->lang->demand->deptReviewList['reviewer'];
        if(!empty($define))
        {
            $manager =  implode(',',array_unique(array_merge(explode(',',$define),explode(',',$deptLeaderCN->manager))));
        }else{
            $manager = $deptLeaderCN->manager;
        }
        $deptLeader = $deptModel->getRenameListByAccountStr($manager);
        //获取后台配置人员的数组下标
        $defineIndexInfo = array_flip(array_keys($deptLeader));
        $defineIndex = $defineIndexInfo[$define] + 1;
        $this->view->defineIndex   = $defineIndex;

        $requirement = $requirementModel->getByID($requirementID);
        //处理时间0000
        $this->dealEmptyTime($requirement);
        $changeInfo = $requirementModel->getChangeInfoByChangeId($changeID);

        if(strpos($changeInfo->alteration,'changeTitle') === false)         $changeInfo->changeTitle = '';
        if(strpos($changeInfo->alteration,'requirementOverview') === false)   $changeInfo->changeOverview = '';
        if(strpos($changeInfo->alteration,'requirementDeadline') === false)   $changeInfo->changeDeadline = '';

        //选中不可编辑人拼接后台配置人员
        $defaultChoose = false;
        if(!empty($define))
        {
            $leaderChoose =  implode(',',array_unique(array_merge(explode(',',$define),explode(',',$changeInfo->deptLeader))));
            $defaultChoose = true;
        }else{
            $leaderChoose = $changeInfo->deptLeader;
        }

        $this->view->leaderChoose   = $leaderChoose;
        $this->view->defaultChoose  = $defaultChoose;

        $demand = $demandModel->getByRequirementID('id,`code`,title,`status`',$requirementID);
        $affectDemands = array();
        $selectDemandIds = $changeInfo->affectDemand;
        $affectRadio = 'no';//是否涉及受影响条目
        if(!empty($selectDemandIds))
        {
            $affectRadio = 'yes';
        }
        if(!empty($demand))
        {
            foreach ($demand as $value)
            {
                //受影响条目范围 已录入、开发中、变更单退回
                if(in_array($value->status,['wait','feedbacked','chanereturn']))
                {
                    $demandStatus = zget($this->lang->demand->statusList,$value->status);
                    $affectDemands[$value->id] = $value->code. "(" .$value->title. "_". $demandStatus .")";
                }

            }
        }

        $this->view->changeInfo  = $changeInfo;
        $this->view->requirement = $requirement;
        $this->view->deptLeader  = array('0' => '') + $deptLeader;
        $this->view->poUsers     = array('0' => '') + $poUsers;
        $this->view->users       = $users;
        $this->view->affectDemands = $affectDemands;
        $this->view->selectDemandIds = $selectDemandIds;
        $this->view->affectRadio = $affectRadio;
        //是否是清总单据
        $isGuestcn = $requirementModel->getIsGuestcn($requirement->createdBy);
        $this->view->isGuestcn  = $isGuestcn;

        $this->display();
    }

    /**
     * @Notes: 撤销变更单
     * @Date: 2023/6/26
     * @Time: 17:53
     * @Interface revoke
     * @param $changeID
     * @param $requirementID
     */
    public function revoke($changeID,$requirementID)
    {
        if($_POST)
        {
            /* 当请求方式为post时，调用change方法处理变更逻辑，如果处理成功则记录变更动作，然后返回成功信息。*/
            $changes = $this->requirement->revoke($changeID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes)
            {
                $actionID = $this->loadModel('action')->create('requirement', $requirementID, 'revoke', $this->post->revokeRemark);
            }


            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }
        $this->display();
    }

    /**
     * @Notes:变更单详情
     * @Date: 2023/6/27
     * @Time: 10:30
     * @Interface changeview
     * @param $changeID
     * @param $requirementID
     */
    public function changeview($changeID,$requirementID)
    {
        /**
         * @var deptModel $deptModel
         * @var requirementModel $requirementModel
         * @var fileModel $fileModel
         * @var demandodel $demandModel
         */
        $this->app->loadlang('opinion');
        $demandModel = $this->loadModel('demand');
        $requirementModel = $this->loadModel('requirement');
        $fileModel = $this->loadModel('file');
        $deptModel = $this->loadModel('dept');
        $users = $this->loadModel('user')->getPairs('noletter|noclosed');
        //产品经理
        $poUsers = $deptModel->getPoUser();
        foreach ($poUsers as $name)
        {
            $poUsers[$name] = zget($users,$name,'');
        }
        $changeInfo = $requirementModel->getChangeInfoByChangeId($changeID);
        //部门负责人
        $deptLeaderCN = $deptModel->getFieldByDeptId('id,manager',$this->app->user->dept);
        $deptLeader = $deptModel->getRenameListByAccountStr($deptLeaderCN->manager);
        $requirement = $requirementModel->getByID($requirementID);
        //处理附件
        $requirement->requirementFiles = [];
        $changeInfo->changeFiles = [];

        if(!empty($changeInfo->requirementFile))
        {
            $filesIDs = explode(',',$changeInfo->requirementFile);
            $requirement->requirementFiles = $fileModel->getByObjectHaveDelete($filesIDs);
        }
        if(!empty($changeInfo->changeFile))
        {
            $changeFilesIDs = explode(',',$changeInfo->changeFile);
            $changeInfo->changeFiles = $fileModel->getByObjectHaveDelete($changeFilesIDs);
        }


        $affectDemandIds = $changeInfo->affectDemand;
        $affectDemands = [];
        if(!empty($affectDemandIds))
        {
            foreach (explode(',',$affectDemandIds) as $value)
            {
                $demandInfo = $demandModel->getByID($value);
                $demandStatus = zget($this->lang->demand->statusList,$demandInfo->status);
                $affectDemands[$value]['id'] = $demandInfo->id;
                $affectDemands[$value]['name'] = $demandInfo->code.'('.$demandInfo->title. "_". $demandStatus .")"."<br />";
            }
        }

        //处理时间0000
        $this->dealEmptyTime($requirement);
        $bookNodes = $this->loadModel('review')->getNodes('requirementchange', $changeID, $changeInfo->version);
        $this->view->changeInfo  = $changeInfo;
        $this->view->bookNodes   = $bookNodes;
        $this->view->requirement = $requirement;
        $this->view->deptLeader  = array('0' => '') + $deptLeader;
        $this->view->poUsers     = array('0' => '') + $poUsers;
        $this->view->users       = $users;
        $this->view->affectDemands = $affectDemands;
        $this->display();
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
        $requirement = $this->loadModel('requirement')->getByID($requirementID, 'latest');
        /* 当请求方式为post时，调用review方法处理需求条目评审逻辑，评审成功则记录操作动作和变动字段，返回成功信息。*/
        if($_POST)
        {
            $changes = $this->requirement->review($requirementID, $requirement->changeVersion, $this->post->result, $this->post->comment);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            if($changes)
            {
                $obj = $this->requirement->getByID($requirementID);
                $actionID = $this->loadModel('action')->create('requirement', $requirementID, 'reviewed', $this->post->comment, $obj->version);
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        /* 获取需求条目信息、产品线、产品、部门、项目计划、用户和应用系统信息。*/
        $this->view->title       = $this->lang->requirement->review;
        $this->view->requirement = $requirement;
        $this->view->lines       = array('' => '') + $this->loadModel('product')->getLinePairs();
        $this->view->products    = $this->product->getPairs();
        $this->view->depts       = $this->loadModel('dept')->getOptionMenu();
        $this->view->projects    = array(0 => '') + $this->loadModel('projectplan')->getPairs();
        $this->view->users       = $this->loadmodel('user')->getPairs('noclosed|noletter');
        $this->view->apps        = array(0 => '') + $this->loadmodel('application')->getapplicationNameCodePairs();

        $this->display('requirement', 'newreview');
    }

    /**
     * @Notes: 审批变更流程
     * @Date: 2023/6/29
     * @Time: 17:11
     * @Interface reviewchange
     * @param $requirementID
     */
    public function reviewchange($requirementID)
    {
        //获取审批中的变更单
        /**
         * @var requirementModel $requirementModel
         * @var fileModel $fileModel
         * @var demandModel $demandModel
         */
        $fileModel = $this->loadModel('file');
        $demandModel = $this->loadModel('demand');
        $requirementModel = $this->loadModel('requirement');
        $pendingChangeOrderInfo = $requirementModel->getPendingOrderByRequirementId($requirementID);
        $requirement = $requirementModel->getByID($requirementID);
        //所属意向如果发起变更且该任务受影响则不允许再次发起
        $followOpinion = $requirementModel->followChange($requirementID,$requirement->opinion);
        //获取变更单ID
        $changeID = 0;
        $nextDealNode = '';
        if(!empty($pendingChangeOrderInfo))
        {
            $changeID = $pendingChangeOrderInfo->id;
            $nextDealNode = $pendingChangeOrderInfo->nextDealNode;
        }
        if($_POST)
        {
            $this->requirement->reviewchange($changeID,$requirementID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $changeInfo = $requirementModel->getChangeInfoByChangeId($changeID);
        //处理附件
        $requirement->requirementFiles = [];
        $changeInfo->changeFiles = [];

        if(!empty($changeInfo->requirementFile))
        {
            $filesIDs = explode(',',$changeInfo->requirementFile);
            $requirement->requirementFiles = $fileModel->getByObjectHaveDelete($filesIDs);
        }
        if(!empty($changeInfo->changeFile))
        {
            $changeFilesIDs = explode(',',$changeInfo->changeFile);
            $changeInfo->changeFiles = $fileModel->getByObjectHaveDelete($changeFilesIDs);
        }

        $affectDemandIds = $changeInfo->affectDemand;
        $affectDemands = [];
        if(!empty($affectDemandIds))
        {
            foreach (explode(',',$affectDemandIds) as $value)
            {
                $demandInfo = $demandModel->getByID($value);
                $demandStatus = zget($this->lang->demand->statusList,$demandInfo->status);
                $affectDemands[$value]['id'] = $demandInfo->id;
                $affectDemands[$value]['name'] = $demandInfo->code.'('.$demandInfo->title. "_". $demandStatus .")"."<br />";
            }
        }
        $this->view->requirement   = $requirement;
        $this->view->affectDemands = $affectDemands;
        $this->view->changeInfo    = $changeInfo;
        $this->view->followOpinion = $followOpinion;
        $this->view->nextDealNode  = $nextDealNode;

        //是否是清总单据
        $isGuestcn = $requirementModel->getIsGuestcn($requirement->createdBy);
        $this->view->isGuestcn  = $isGuestcn;
        $this->display();
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
        //更新是否超时状态
        $this->loadModel('requirement')->updateRequirementIfOverDate('ifOverDate',$requirementID); //内部反馈超时 未做过处理的
        $this->loadModel('requirement')->updateRequirementIfOverDate('ifOverTimeOutSide',$requirementID); //外部反馈超时 未做过处理的

        $this->app->loadLang('demand');
        $requirement = $this->requirement->getByID($requirementID);
        //外部同步需求任务接收时间取创建时间，内部需求任务取需求意向接收时间
        if($requirement->createdBy == 'guestcn'){
            $requirement->acceptTime = $requirement->createdDate;
            $requirement->newPublishedTime = $requirement->feekBackStartTime != '0000-00-00 00:00:00' ? $requirement->feekBackStartTime : '';
        }else{
            $opinionInfo = $this->loadModel('opinion')->getByID($requirement->opinion);
            $requirement->acceptTime = $opinionInfo->receiveDate ?? '';
            $requirement->newPublishedTime = $requirement->newPublishedTime != '0000-00-00 00:00:00' ? $requirement->newPublishedTime : '';

        }

        /**
         * 迭代三十四 增加距内外部反馈超期剩余天数
         * ①如果反馈期限大于当前时间则计算剩余天数
         * ②如果反馈期限小区当前时间且状态为否则无需计算天数
         * ifOverDate 1:否 2:是
         * ifOverTimeOutSide 1:否 2:是
         */
        $now = date('Y-m-d',strtotime(helper::now()));
        $insideDays = 0;
        $outsideDays = 0;
        //内部
        if($requirement->feekBackEndTimeInside != '0000-00-00 00:00:00' && $requirement->feekBackEndTimeInside != '0000-00-00' && !empty($requirement->feekBackEndTimeInside))
        {
            $insideStartDate = date('Y-m-d',strtotime($requirement->feekBackEndTimeInside));
            $insideDays = $this->loadModel('review')->getDiffDate($now,$insideStartDate);
        }
        //外部
        if($requirement->feekBackEndTimeOutSide != '0000-00-00 00:00:00' && $requirement->feekBackEndTimeOutSide != '0000-00-00' && !empty($requirement->feekBackEndTimeOutSide))
        {
            $outsideStartDate = date('Y-m-d',strtotime($requirement->feekBackEndTimeOutSide));
            $outsideDays = $this->loadModel('review')->getDiffDate($now,$outsideStartDate);
        }

        //内部
        if(empty($insideDays))
        {
            $insideDays = 0;
        }else{
            if($insideDays >= 0)  $insideDays = $insideDays.' (工作日)';
            if($requirement->ifOverDate == 2)   $insideDays = '已超期';
            if($insideDays < 0 && $requirement->ifOverDate == 1)   $insideDays = 0;
        }

        //外部
        if(empty($outsideDays))
        {
            $outsideDays = 0;
        }else{
            if($outsideDays >= 0)  $outsideDays = $outsideDays.' (工作日)';
            if($requirement->ifOverTimeOutSide == 2)   $outsideDays = '已超期';
            if($outsideDays < 0 && $requirement->ifOverTimeOutSide == 1)   $outsideDays = 0;
        }

        //处理时间0000的情况
        $this->dealEmptyTime($requirement);

        if(in_array($requirement->status,['delivered','onlined','deleteout'])){
            $requirement->reviewer = '';
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
        $demands = $this->requirement->getDemandByRequirement($requirementID, 'id,project,acceptUser,acceptDept');
        $ownProjectArr = !empty($requirement->project) ? explode(',',$requirement->project): [];
        $demandProjectArr = array_column($demands,'project');
        $mergeProjectArr = array_merge($ownProjectArr,$demandProjectArr);
        /**
         * @var projectPlanModel $projectPlanModel 
         * @var opinionModel $opinionModel
         */
        $projectPlanModel = $this->loadModel('projectplan');
        $opinionModel = $this->loadModel('opinion');
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
        $requirement->demandOwner = implode(',',$acceptUserArr);
        $requirement->dept = implode(',',$acceptDeptArr);

        //转换多行文本换行显示问题
        $requirement->desc = str_replace(chr(13),'<br>',$requirement->desc);

        //查询需求变更单信息
        /**
         * @var requirementChangeModel $requirementChangeModel
         * @var requirementModel $requirementModel
         * @var demandModel $demandModel
         */
        $requirementChangeModel = $this->loadModel('requirementchange');
        $requirementModel       = $this->loadModel('requirement');
        $demandModel            = $this->loadModel('demand');
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
        $secondLinePersonList = $this->loadModel('dept')->getExecutiveUser();//二线专员

        $changeInfo = $requirementModel->getChangeInfoByRequirementId($requirementID);

        $reviewInfo = $requirementModel->getPendingOrderByRequirementId($requirementID);
        //变更单下一节点处理人
        $requirement->changeNextDealuser = $reviewInfo->nextDealUser ?? '';

        $opinion = $opinionModel->getByID($requirement->opinion);
        //构造需求全生命周期跟踪矩阵数据
        $lifeOpinionInfo = $this->buildLifeInfo($opinion);
        //自定义变更解锁人
        $unLock = array_filter(array_keys($this->lang->demand->unLockList));
        $this->view->unLock  = $unLock;
        /* 查询需求条目及其相关的信息。*/
        $this->view->executives   = $suspendList ? array_keys($suspendList): [];

        $this->view->insideDays  = $insideDays;
        $this->view->outsideDays = $outsideDays;
        $this->view->title       = $this->lang->requirement->view;
        $this->view->users       = $this->loadmodel('user')->getPairs('noletter|noclosed');
        $this->view->actions     = $this->loadmodel('action')->getList('requirement', $requirementID);
        $this->view->requirement = $requirement;
        $this->view->lines       = $this->loadModel('product')->getLinePairs();
        $this->view->products    = $this->product->getPairs();
        $this->view->depts       = $this->loadModel('dept')->getOptionMenu();
        $this->view->opinion     = $opinion;
        $this->view->apps        = array('' => '') + $this->loadmodel('application')->getapplicationNameCodePairs();
        $this->view->nodes       = $this->loadModel('review')->getNodes('requirement', $requirementID, $requirement->version);
        if($this->view->nodes){
            $this->view->nodes = array_column($this->view->nodes,null,'stage');
        }
        if(isset($this->view->nodes[2])){
            $this->view->alloWRePush   = $secondLinePersonList ? array_keys($secondLinePersonList) : [];
        }else{
            $this->view->alloWRePush = [$requirement->feedbackBy,'admin'];
        }
        $this->view->projectList = $projectList;
        $this->view->changeInfo  = $changeInfo;
        $this->view->lastTaskTime       = $lastTaskTime;
        $this->view->requirementChangeInfo  = $requirementChangeInfo;
        $this->view->lifeOpinionInfo  = $lifeOpinionInfo;//需求全生命周期跟踪矩阵数据

        //超时考核信息是否可见
        $isOverDateInfoVisible =  $requirementModel->getIsOverDateInfoVisible($this->app->user->account);
        $this->view->isOverDateInfoVisible = $isOverDateInfoVisible;
        $this->display();
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
     * 获取反馈单所有历史审批记录
     * @param $requirementID
     */
    public function historyRecord($requirementID)
    {
        $allNodes = $this->loadModel('review')->getAllNodes('requirement', $requirementID); //所有历史审批信息

        foreach ($allNodes as $key=>$node){
            $allNodes[$key] = array_column($node,null,'stage');

        }

        $requirement = $this->loadModel('problem')->getByID($requirementID);
        $this->view->allNodes = $allNodes;
        $this->view->requirement = $requirement;
        $this->view->users   = $this->loadModel('user')->getPairs('noclosed|noletter');
        $this->display();
    }

    /**
     * @Notes: 处理时间0000的情况
     * @Date: 2023/6/8
     * @Time: 16:45
     * @Interface dealEmptyTime
     * @param $requirement
     * @return mixed
     */
    public function dealEmptyTime($requirement)
    {
        //上线时间
        if($requirement->onlineTimeByDemand == '0000-00-00 00:00:00' || $requirement->onlineTimeByDemand == '0000-00-00' || empty($requirement->onlineTimeByDemand))
        {
            $requirement->onlineTimeByDemand = '';
        }
        //计划完成时间
        if($requirement->end == '0000-00-00 00:00:00' || $requirement->end == '0000-00-00' || empty($requirement->end))
        {
            $requirement->end = '';
        }else{
            $requirement->end = date('Y-m-d',strtotime($requirement->end));
        }

        //期望完成时间
//        if($requirement->deadLine == '0000-00-00 00:00:00' || $requirement->deadLine == '0000-00-00' || empty($requirement->deadLine))
//        {
//            $requirement->deadLine = '';
//            if($requirement->createdBy == 'guestcn'){
//                $opinionInfo = $this->loadModel('opinion')->getByID($requirement->opinion);
//                $requirement->deadLine = date('Y-m-d',strtotime($opinionInfo->deadline));
//            }
//        }else{
//            $requirement->deadLine = date('Y-m-d',strtotime($requirement->deadLine));
//        }
        //任务首次接收时间
        if($requirement->acceptTime == '0000-00-00 00:00:00' || $requirement->acceptTime == '0000-00-00' || empty($requirement->acceptTime))
        {
            $requirement->acceptTime = '';
        }
        //任务最新变更时间
        if($requirement->lastChangeTime == '0000-00-00 00:00:00' || $requirement->lastChangeTime == '0000-00-00' || empty($requirement->lastChangeTime))
        {
            $requirement->lastChangeTime = '';
        }
        //反馈同步成功日期
        if($requirement->feedbackDate == '0000-00-00 00:00:00' || $requirement->feedbackDate == '0000-00-00' || empty($requirement->feedbackDate))
        {
            $requirement->feedbackDate = '';
        }
        //反馈开始时间
        if($requirement->feekBackStartTime == '0000-00-00 00:00:00' || $requirement->feekBackStartTime == '0000-00-00' || empty($requirement->feekBackStartTime))
        {
            $requirement->feekBackStartTime = '';
        }
        //部门通过时间
        if($requirement->deptPassTime == '0000-00-00 00:00:00' || $requirement->deptPassTime == '0000-00-00' || empty($requirement->deptPassTime))
        {
            $requirement->deptPassTime = '';
        }
        //产创通过时间
        if($requirement->innovationPassTime == '0000-00-00 00:00:00' || $requirement->innovationPassTime == '0000-00-00' || empty($requirement->innovationPassTime))
        {
            $requirement->innovationPassTime = '';
        }

        if($requirement->feekBackStartTimeOutside == '0000-00-00 00:00:00' || $requirement->feekBackStartTimeOutside == '0000-00-00' || empty($requirement->feekBackStartTimeOutside))
        {
            $requirement->feekBackStartTimeOutside = '';
        }

        if(empty($requirement->feekBackStartTime) && empty($requirement->deptPassTime))
        {
            $requirement->feekBackBetweenTimeInside = '';
        }else{
            $requirement->feekBackBetweenTimeInside = '('.$requirement->feekBackStartTime.'~'.$requirement->deptPassTime.')';
        }
        //外部反馈区间
        $requirement->feekBackBetweenOutSide = '('.$requirement->feekBackStartTimeOutside.'~'.$requirement->innovationPassTime.')';
        if(empty($requirement->feekBackStartTimeOutside) && empty($requirement->innovationPassTime))
        {
            $requirement->feekBackBetweenOutSide = '';
        }

        if($requirement->ifOverTimeOutSide == 100)
        {
            $requirement->feekBackBetweenOutSide = '否'.'('.$requirement->feekBackStartTimeOutside.'~'.$requirement->innovationPassTime.')';
            if(empty($requirement->feekBackStartTimeOutside) && empty($requirement->innovationPassTime))
            {
                $requirement->feekBackBetweenOutSide = '否';
            }
        }

        if($requirement->ifOverDate == 100)
        {
            $requirement->feekBackBetweenTimeInside = '否'.'('.$requirement->feekBackStartTime.'~'.$requirement->deptPassTime.')';
            if(($requirement->feekBackStartTime == '0000-00-00 00:00:00' || empty($requirement->feekBackStartTime)) && ($requirement->deptPassTime == '0000-00-00 00:00:00' || empty($requirement->deptPassTime))){
                $requirement->feekBackBetweenTimeInside = '否';
            }
        }
        //内部反馈截止时间
        if($requirement->feekBackEndTimeInside == '0000-00-00 00:00:00' || $requirement->feekBackEndTimeInside == '0000-00-00' || empty($requirement->feekBackEndTimeInside)){
            $requirement->feekBackEndTimeInside = '';
        }
        //外部反馈截止时间
        if($requirement->feekBackEndTimeOutSide == '0000-00-00 00:00:00' || $requirement->feekBackEndTimeOutSide == '0000-00-00' || empty($requirement->feekBackEndTimeOutSide)){
            $requirement->feekBackEndTimeOutSide = '';
        }
        //交付时间
        if($requirement->solvedTime == '0000-00-00 00:00:00' || $requirement->solvedTime == '0000-00-00' || empty($requirement->solvedTime)){
            $requirement->solvedTime = '';
        }


        return $requirement;
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
        $this->app->loadLang('demand');
        $requirement = $this->requirement->getByID($requirementID);
        if($requirement->status == 'closed')
        {
            $this->view->canChange = false;
        }
        $opinion = $this->loadModel('opinion')->getByID($requirement->opinion);
        if(!empty($_POST))
        {
            //变更中，不允许操作
            if($requirement->changeLock == 2){
                $response['result']  = 'fail';
                $response['message'] = $this->lang->requirement->changeIng;
                $this->send($response);
            }

            $dealcomment = $this->post->dealcomment;
            if(empty($dealcomment)){
                dao::$errors['dealcomment'] = sprintf($this->lang->requirement->emptyObject, $this->lang->requirement->dealcomment);
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            /*
             * 迭代三十三
             * 挂起需求任务时，该任务下的需求条目为：已交付、上线成功、变更单退回、变更单异常、已挂起、已关闭状态下不改变需求条目状态。
             * 如果为存在【已录入、开发中】状态的需求条目则
             * 提示：该任务下存在【已录入、开发中】状态的需求条目，请先挂起/关闭该需求条目后再挂起需求任务。
             */
            $demands = $this->loadModel("demand")->getByRequirementID('*',$requirementID);
            if($demands)
            {
                $demandStatus = array_filter(array_unique(array_column($demands,'status')));
                if(in_array('wait',$demandStatus) || in_array('feedbacked',$demandStatus))
                {
                    $response['result']  = 'fail';
                    $response['message'] = $this->lang->requirement->suspendTip;
                    $response['locate']  = 'parent';
                    $this->send($response);
                }
            }


            $this->dao->update(TABLE_REQUIREMENT)->set('status')->eq('closed')->set('lastStatus')->eq($requirement->status)->set('closedBy')->eq($this->app->user->account)->set('closedDate')->eq(helper::now())->where('id')->eq($requirementID)->exec();
            $this->loadModel('consumed')->record('requirement', $requirementID, 0, $this->app->user->account, $requirement->status, 'closed', array());
            $this->loadModel('action')->create('requirement', $requirementID, 'suspenditem', $dealcomment); 

            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        /* 获取需求条目、操作记录和用户信息。*/
        $this->view->title       = $this->lang->requirement->delete;
        $this->view->actions     = $this->loadModel('action')->getList('requirement', $requirementID);
        $this->view->requirement = $requirement;
        $this->view->opinion = $opinion;
        $this->view->users       = $this->loadModel('user')->getPairs();
        $this->display();
    }

    public function activate($requirementID)
    {
        $requirement = $this->requirement->getByID($requirementID);
        if($requirement->status != 'closed')
        {
            $this->view->canChange = false;
        }
        if(!empty($_POST))
        {
            $dealcomment = $this->post->dealcomment;
            if(empty($dealcomment)){
                dao::$errors['dealcomment'] = sprintf($this->lang->requirement->emptyObject, $this->lang->requirement->dealcomment);
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            /**
             * 迭代三十三 激活需求任务需判断所属需求意向是否已挂起，已挂起则提示，不允许激活   且只激活本身
             */
            $opinionStatus = $this->dao->select('status')->from(TABLE_OPINION)->where('id')->eq($requirement->opinion)->fetch('status');
            if($opinionStatus == 'closed')
            {
                $response['result']  = 'fail';
                $response['message'] = $this->lang->requirement->activationTip;
                $this->send($response);
            }

            /* 当请求方式为post时，更新需求条目的状态为关闭。判断所属需求意向下的需求条目都关闭时，关闭需求意向。*/
            $this->dao->update(TABLE_REQUIREMENT)->set('status')->eq($requirement->lastStatus)->set('activatedBy')->eq($this->app->user->account)->set('activatedDate')->eq(helper::now())->where('id')->eq($requirementID)->exec();

            $this->loadModel('consumed')->record('requirement', $requirementID, 0, $this->app->user->account, $requirement->status, $requirement->lastStatus, array());
            $this->loadModel('action')->create('requirement', $requirementID, 'activate', $dealcomment);

            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        /* 获取需求条目、操作记录和用户信息。*/
        $this->view->title       = $this->lang->requirement->activate;
        $this->view->actions     = $this->loadModel('action')->getList('requirement', $requirementID);
        $this->view->requirement = $requirement;
        $this->view->users       = $this->loadModel('user')->getPairs();
        $this->display();
    }

    public function recover($requirementID)
    {
        $requirement = $this->requirement->getByID($requirementID);
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
        $this->view->title       = $this->lang->requirement->recover;
        $this->view->actions     = $this->loadModel('action')->getList('requirement', $requirementID);
        $this->view->requirement = $requirement;
        $this->view->users       = $this->loadModel('user')->getPairs();
        $this->display();
    }

    public function ignore($requirementID, $notice = 0)
    {
        $requirement = $this->requirement->getByID($requirementID);
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
        $this->view->title       = $this->lang->requirement->ignore;
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
        $requirement = $this->requirement->getByID($requirementID);
        /* 当请求方式为post时，更新需求条目的状态为删除，并记录操作动作。*/
        if(!empty($_POST))
        {
            $isonlybody = isonlybody();
            //变更中，不允许操作
            if($requirement->changeLock == 2){
                $response['result']  = 'fail';
                $response['message'] = $this->lang->requirement->changeIng;
                $this->send($response);
            }

            $dealcomment = $this->post->dealcomment;
            if(empty($dealcomment)){
                dao::$errors['dealcomment'] = sprintf($this->lang->requirement->emptyObject, $this->lang->requirement->dealcomment);
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $this->dao->update(TABLE_REQUIREMENT)
            ->set('status')->eq('deleted')
            ->where('id')->eq($requirementID)->exec();
            $this->loadModel('action')->create('requirement', $requirementID, 'deleted', $dealcomment);
            $this->loadModel('consumed')->record('requirement', $requirementID, 0, $this->app->user->account, $requirement->status, 'deleted', array());
            $demands = $this->loadModel("demand")->getBrowesByRequirementID($requirementID);
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

            $backUrl =  $this->session->requirementList ? $this->session->requirementList : inLink('browse');

            if($isonlybody) die(js::closeModal('parent', $backUrl));
            die(js::reload('parent'));
           /* $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = 'parent';

            $this->send($response);*/
        }

        /* 获取需求条目信息、操作记录和用户信息。*/
        $requirement = $this->requirement->getByID($requirementID);
        $this->view->title       = $this->lang->requirement->delete;
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
        $this->loadModel('demand');
        $this->view->title    = $this->lang->requirement->matrixTitle;
        $this->view->opinions = $this->loadModel('opinion')->getList($status, 0, 'id_desc', 'null', 'nodeleted', $begin, $end);
        $this->view->progress = $this->opinion->getProgress($this->view->opinions);
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
        $this->view->apps     = $this->loadModel('application')->getapplicationNameCodePairs();
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
         if($_POST) {
             $this->loadModel('file');
             $requirementLang = $this->lang->requirement;
             $requirementConfig = $this->config->requirement;
             $this->app->loadLang("opinioninside");
             $fields = $this->post->exportFields ? $this->post->exportFields : explode(',', $requirementConfig->exportlist->exportFields);
             foreach ($fields as $key => $fieldName) {
                 $fieldName = trim($fieldName);
                 $fields[$fieldName] = isset($requirementLang->$fieldName) ? $requirementLang->$fieldName : $fieldName;
                 unset($fields[$key]);
             }
             //考核信息是否可见
             $isOverDateInfoVisible = $this->requirement->getIsOverDateInfoVisible($this->app->user->account);
             if(!$isOverDateInfoVisible){
                 foreach ($this->lang->requirement->overDateInfoVisibleFields as $tempField){
                     unset($fields[$tempField]);
                 }
             }
             $fieldsKeys = array_keys($fields);

             $requirements = array();
             if ($this->session->requirementOnlyCondition) {
                 $requirements = $this->dao->select('*')->from(TABLE_REQUIREMENT)->where($this->session->requirementQueryCondition)
                     ->beginIF($this->post->exportType == 'selected')->andWhere('id')->in($this->cookie->checkedItem)->fi()
                     ->orderBy($orderBy)->fetchAll('id');
             } else {
                 $stmt = $this->dbh->query($this->session->requirementQueryCondition . (($this->post->exportType == 'selected' and $this->cookie->checkedItem) ? " AND t1.id IN({$this->cookie->checkedItem})" : '') . " ORDER BY " . strtr('t1.' . $orderBy, '_', ' '));
                 while ($row = $stmt->fetch()) $requirements[$row->id] = $row;
             }
             /* Get users, products and executions. */
             $users = $this->loadModel('user')->getPairs('noletter');
             $this->app->loadLang("opinion");
             $apps = $this->loadModel('application')->getapplicationNameCodePairs();
             $lines = $this->loadModel('product')->getLinePairs();
             $products = $this->product->getPairs();
             $depts = $this->loadModel('dept')->getOptionMenu();
             foreach ($depts as $key => $dept)
             {
                 $depts[$key] = substr_replace($dept,'',0,1);
             }

             $projects = $this->loadModel('projectplan')->getPairs();
             foreach ($requirements as $requirement) {
                 $opinion = $this->loadModel("opinion")->getByID($requirement->opinion);
                 $demands = $this->loadModel('demand')->getBrowesByRequirementID($requirement->id);
                 $demandsOther = $this->requirement->getDemandByRequirement($requirement->id, 'id,project,acceptUser,acceptDept');
                 $ownProjectArr = !empty($requirement->project) ? explode(',', $requirement->project) : [];
                 $demandProjectArr = array_column($demandsOther, 'project');
                 $mergeProjectArr = array_merge($ownProjectArr, $demandProjectArr);
                 /**@var projectplanModel $projectPlanModel */
                 $projectPlanModel = $this->loadModel('projectplan');
                 $projectArray = array_filter(array_unique($mergeProjectArr));
                 if (!empty($projectArray)) {
                     $projectList = $projectPlanModel->getPlanInProjectIDs($projectArray);
                     if ($projectList) {
                         $arr = [];
                         $projectStr = '';
                         foreach ($projectList as $v) {
                             $arr[] = $v->id;
                         }
                         $projectStr = implode(',', $arr);
                         $requirement->project = $projectStr;
                     }
                 }
                 $acceptUserArr = array_filter(array_unique(array_column($demandsOther, 'acceptUser')));
                 $acceptDeptArr = array_filter(array_unique(array_column($demandsOther, 'acceptDept')));
                 $requirement->requirementOwner = zget($users, $requirement->owner);
                 //$requirement->owner = zmget($users, implode(',', $acceptUserArr));
                 $requirement->dept = zmget($depts, implode(',', $acceptDeptArr));
                 //最新发布时间
                 $requirement->publishedTime = $requirement->newPublishedTime != '0000-00-00 00:00:00' ? $requirement->newPublishedTime : '';

                 //外部同步需求任务接收时间取创建时间，内部需求任务取需求意向接收时间
                 if ($requirement->createdBy == 'guestcn') {
                     $requirement->acceptTime = $requirement->createdDate;
                     //交付周期计算起始时间
                     $requirement->newPublishedTime = $requirement->feekBackStartTime != '0000-00-00 00:00:00' ? $requirement->feekBackStartTime : '';
                 } else {
                     $opinionInfo = $this->loadModel('opinion')->getByID($requirement->opinion);
                     $requirement->acceptTime = $opinionInfo->receiveDate ?? '';
                     //交付周期计算起始时间
                     $requirement->newPublishedTime = $requirement->newPublishedTime != '0000-00-00 00:00:00' ? $requirement->newPublishedTime : '';
                 }
                 foreach ($demands as $demand) {
                     $requirement->demands .= $demand->code . PHP_EOL;
                 }
                 $requirement->solvedTime = $requirement->solvedTime != '0000-00-00 00:00:00' ? $requirement->solvedTime : '';
                 $requirement->ID = $requirement->id;
                 //迭代二十九 【反馈人员所属部门】（若反馈人为空，则取反馈单待处理人所属部门，若反馈单待处理人为空则反馈人员所属部门为空）
                 if (!empty($requirement->feedbackBy)) {
                     $feedbackDepts = $this->loadModel('user')->getUserDeptIds($requirement->feedbackBy);
                     $requirement->feedbackBy = zget($users, $requirement->feedbackBy, '');
                     $requirement->feedbackDept = zmget($depts, implode(',', $feedbackDepts));
                 }

                 //待反馈状态取反馈单待处理人所属部门
                 if($requirement->feedbackStatus == 'tofeedback')
                 {
                     $userDept = $this->loadModel('user')->getByAccount($requirement->feedbackDealUser);
                     $requirement->feedbackDept = isset($userDept->dept) ? zget($depts, $userDept->dept, '') : '';
                 }

                 $feedbackDealUserList = explode(",", $requirement->feedbackDealUser);
                 $feedbackDealUserChnList = array();
                 foreach ($feedbackDealUserList as $feedbackDealUser) {
                     $feedbackDealUserChn = zget($users, $feedbackDealUser, '');
                     array_push($feedbackDealUserChnList, $feedbackDealUserChn);
                 }
                 $requirement->feedbackDealUser = implode(",", $feedbackDealUserChnList);
                 $requirement->feedbackStatus = zget($requirementLang->feedbackStatusList, $requirement->feedbackStatus, '');


                 $dealUserList = explode(",", $requirement->dealUser);
                 $dealUserChnList = array();
                 foreach ($dealUserList as $dealUser) {
                     $dealUserChn = zget($users, $dealUser, '');
                     array_push($dealUserChnList, $dealUserChn);
                 }
                 $requirement->dealUser = implode(",", $dealUserChnList);
                 if (in_array($requirement->status, ['delivered', 'onlined','deleteout'])) {
                     $requirement->dealUser = '';
                 }
                 $requirement->status = zget($requirementLang->statusList, $requirement->status, '');
                 $requirement->opinion = $opinion->code;
                 $requirement->sourceMode = zget($this->lang->opinion->sourceModeListOld, $opinion->sourceMode, '');
                 $requirement->method = zget($requirementLang->methodList, $requirement->method, '');
                 $requirement->actualMethod = zmget($requirementLang->actualMethodList, $requirement->actualMethod, '');
                 $unionList = explode(",", $opinion->union);
                 $unionChnList = array();
                 foreach ($unionList as $union) {
                     $unionChn = zget($this->lang->opinion->unionList, $union, '');
                     array_push($unionChnList, $unionChn);
                 }
                 $requirement->union = implode(",", $unionChnList);
                 $appList = explode(",", $requirement->app);
                 $appChnList = array();
                 foreach ($appList as $app) {
                     $appChn = zget($apps, $app, '');
                     array_push($appChnList, $appChn);
                 }
                 $requirement->app = implode(",", $appChnList);
                 $productManagerList = explode(",", $requirement->productManager);
                 $productManagerChnList = array();
                 foreach ($productManagerList as $productManager) {
                     $productManagerChn = zget($users, $productManager, '');
                     array_push($productManagerChnList, $productManagerChn);
                 }
                 $requirement->productManager = implode(",", $productManagerChnList);

                 $projectManagerList = explode(",", $requirement->projectManager);
                 $projectManagerChnList = array();
                 foreach ($projectManagerList as $projectManager) {
                     $projectManagerChn = zget($users, $projectManager, '');
                     array_push($projectManagerChnList, $projectManagerChn);
                 }
                 $requirement->projectManager = implode(",", $projectManagerChnList);
                 $requirement->desc = strip_tags($requirement->desc);

                 $lineList = explode(",", $requirement->line);
                 $lineChnList = array();
                 foreach ($lineList as $line) {
                     $lineChn = zget($lines, $line, '');
                     array_push($lineChnList, $lineChn);
                 }
                 $requirement->line = implode(",", $lineChnList);

                 $productList = explode(",", $requirement->product);
                 $productChnList = array();
                 foreach ($productList as $product) {
                     $productChn = zget($products, $product, '');
                     array_push($productChnList, $productChn);
                 }
                 $requirement->product = implode(",", $productChnList);
                 $requirement->createdBy = zget($users, $requirement->createdBy, '');
                 $requirement->editedBy = zget($users, $requirement->editedBy, '');
                 $requirement->closedBy = zget($users, $requirement->closedBy, '');
                 $requirement->activatedBy = zget($users, $requirement->activatedBy, '');
                 $requirement->ignoredBy = zget($users, $requirement->ignoredBy, '');
                 $requirement->recoveryedBy = zget($users, $requirement->recoveryedBy, '');

                 //100 是一个标识，标记默认值 为否
                 if ($requirement->ifOverDate == 100) {
                     $requirement->ifOverTime = '否';
                 } else {
                     $requirement->ifOverTime = zget($this->lang->requirement->ifOverDateList, $requirement->ifOverDate, '');
                 }
                 //内部反馈开始时间
                 $requirement->insideStart = $requirement->feekBackStartTime != '0000-00-00 00:00:00' ? $requirement->feekBackStartTime : '';
                 //部门审核通过时间（内部反馈结束时间）
                 $requirement->insideEnd = $requirement->deptPassTime != '0000-00-00 00:00:00' ? $requirement->deptPassTime : '';
                 //内部反馈期限
                 $requirement->insideFeedback = $requirement->feekBackEndTimeInside != '0000-00-00 00:00:00' ? $requirement->feekBackEndTimeInside : '';

                 //100 是一个标识，标记默认值 为否
                 if ($requirement->ifOverTimeOutSide == 100) {
                     $requirement->ifOverTimeOutSide = '否';
                 } else {
                     $requirement->ifOverTimeOutSide = zget($requirementLang->ifOverDateList, $requirement->ifOverTimeOutSide, '');
                 }
                 //外部反馈开始时间
                 $requirement->outsideStart = $requirement->feekBackStartTimeOutside != '0000-00-00 00:00:00' ? $requirement->feekBackStartTimeOutside : '';
                 //同步清总成功时间（外部反馈结束时间）
                 $requirement->outsideEnd = $requirement->innovationPassTime != '0000-00-00 00:00:00' ? $requirement->innovationPassTime : '';
                 //外部反馈期限
                 $requirement->outsideFeedback = $requirement->feekBackEndTimeOutSide;

                 $requirement->ifOutUpdate = zget($requirementLang->ifOutUpdateList, $requirement->ifOutUpdate);
                 $requirement->feedbackOver = zget($this->lang->requirement->feedbackOverList, $requirement->feedbackOver);
                 $requirement->lastChangeTime = $requirement->lastChangeTime != '0000-00-00 00:00:00' ? $requirement->lastChangeTime : '';
                 $requirement->onlineTimeByDemand = $requirement->onlineTimeByDemand != '0000-00-00 00:00:00' ? $requirement->onlineTimeByDemand : '';
                 $requirement->planEnd = $requirement->planEnd != '0000-00-00' ? $requirement->planEnd : '';
                 $requirement->end = $requirement->end != '0000-00-00' ? $requirement->end : '';
                 $projectList = explode(",", $requirement->project);
                 $projectChnList = array();
                 foreach ($projectList as $project) {
                     $projectChn = zget($projects, $project, '');
                     array_push($projectChnList, $projectChn);
                 }
                 $requirement->project = implode(",", $projectChnList);
                 $requirement->requireStartTime =  $requirement->requireStartTime != '0000-00-00'? $requirement->requireStartTime: '';

                 //外部
                 if(in_array('outsideDays', $fieldsKeys)){
                     $outsideDays = 0;
                     if($requirement->feekBackEndTimeOutSide != '0000-00-00 00:00:00' && $requirement->feekBackEndTimeOutSide != '0000-00-00' && !empty($requirement->feekBackEndTimeOutSide))
                     {
                         $outsideStartDate = date('Y-m-d',strtotime($requirement->feekBackEndTimeOutSide));
                         $outsideDays = $this->loadModel('review')->getDiffDate(helper::today(),$outsideStartDate);
                     }

                     //外部
                     if(empty($outsideDays))
                     {
                         $outsideDays = 0;
                     }else{
                         if($outsideDays >= 0)  $outsideDays = $outsideDays.' (工作日)';
                         if($requirement->ifOverTimeOutSide == 2)   $outsideDays = '已超期';
                         if($outsideDays < 0 && $requirement->ifOverTimeOutSide == 1)   $outsideDays = 0;
                     }
                     $requirement->outsideDays = $outsideDays;
                 }
             }

             $this->post->set('fields', $fields);
             $this->post->set('rows', $requirements);
             $this->post->set('kind', 'requirement');
             $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
         }


         $this->view->fileName        = $this->lang->requirement->exportName;
         $this->view->allExportFields = $this->config->requirement->exportlist->exportFields;
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
        $apps = $this->loadModel('application')->getapplicationNameCodePairs();
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
            $changes = $this->requirement->assignTo($requirementID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
//            if($changes)
//            {
                $actionID = $this->loadModel('action')->create('requirement', $requirementID, 'assigned', $this->post->comment, $this->post->assignedTo);
                //$this->loadModel('requirement')->sendmail($requirementID,$actionID);
                $this->action->logHistory($actionID, $changes);
//            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        /* 获取需求条目信息和相关操作记录信息。*/
        $this->view->title       = $this->lang->requirement->assigned;
        $this->view->requirement = $this->loadModel('requirement')->getByID($requirementID);
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
        $this->app->loadLang('demand');
        //清总同步的数据 校验需求意向的【下一节点处理人】字段以及【需求分类】是否为空，为空则弹框提示
        $requirement = $this->requirement->getByID($requirementID);
        $opinion = $this->loadModel('opinion')->getByID($requirement->opinion);

        //变更中，不允许操作 2标识锁定 requirementChangeStatus  默认0  1：完成 2：变更进行中 3:已退回
        if($requirement->changeLock == 2){
            $response['result']  = 'fail';
            $response['message'] = $this->lang->requirement->changeIng;
            $this->send($response);
        }

        if($requirement->createdBy == 'guestcn'){
            if(empty($opinion->category)){
                $response['result']  = 'fail';
                $response['message'] = '所属意向未补充完整请联系产品经理进行补充';
                $this->send($response);
            }
        }

        if($_POST){
            $this->loadModel('requirement')->subdivideDemand($requirementID);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = $this->createLink('requirement', 'view', array('requirementID' => $requirementID));
            $this->send($response);
        }

        $this->view->title   = $this->lang->requirement->subdivide;

        if($requirement->end === '0000-00-00') $requirement->end = '';
        if($requirement->analysis === NULL) $requirement->analysis = '';

        //迭代三十三 拆分时下一节点处理人为后台自定义产品经理
        $productManagerList = $this->lang->demand->productManagerList;
        $this->view->productManagerList = $productManagerList;
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

    //CBP二线默认暂无
    public function ajaxCBPSecond($fixType)
    {
        $selectItem = '';
        if($fixType == 'patch'){
            $selectItem = '暂无';
        }
        $cbpprojectList =  array('' => '') + array('暂无' => '暂无') + $this->dao->select('code,concat(concat(concat(code,"（"),name),"）")')->from(TABLE_CBPPROJECT)->where('deleted')->ne(1)->fetchPairs();
        echo html::select('CBPProject', $cbpprojectList, $selectItem, "class='form-control chosen'");
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
            // $this->requirement->setListValue();

            $fields = [];
            $templateFields = $this->config->requirement->exportlist->templateFields;
            foreach($templateFields as $field){
                $fields[$field] = $this->lang->requirement->$field;
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
            $this->requirement->createFromImport();
            if($this->post->isEndPage)
            {
                unlink($tmpFile);
                die(js::locate($this->createLink('requirement','browse'), 'parent'));
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
            $requirementLang   = $this->lang->requirement;
            $requirementConfig = $this->config->requirement;
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
            $opinions = $this->loadModel('opinion')->getPairsByRequmentBrowse();
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
            die(js::locate($this->createLink('requirement','browse')));
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
        if(empty($requirementData)) die(js::locate($this->createLink('requirement','browse')));

        /* Judge whether the editedStories is too large and set session. */
        /* 判断要处理的需求意向是否太大，并设置session。*/
        $countInputVars  = count($requirementData) * 11;
        $showSuhosinInfo = common::judgeSuhosinSetting($countInputVars);
        if($showSuhosinInfo) $this->view->suhosinInfo = extension_loaded('suhosin') ? sprintf($this->lang->suhosinInfo, $countInputVars) : sprintf($this->lang->maxVarsInfo, $countInputVars);

        /* 将要导入的数据及其相关变量，传递到页面进行展示。*/
        $this->view->title      = $this->lang->requirement->common . $this->lang->colon . $this->lang->requirement->showImport;
        $this->view->position[] = $this->lang->requirement->showImport;


        $this->view->statusList = $this->lang->requirement->searchstatusList;
        $this->view->requirementData = $requirementData;
        $this->view->allCount    = $allCount;
        $this->view->allPager    = $allPager;
        $this->view->pagerID     = $pagerID;
        $this->view->isEndPage   = $pagerID >= $allPager;
        $this->view->maxImport   = $maxImport;
        $this->view->dataInsert  = $insert;
        $this->view->users       = $this->loadModel('user')->getPairs('noclosed');
        $opinions = array('0' => '') + $this->loadModel('opinion')->getPairsByRequmentBrowse();
        $this->view->opinions     = $opinions;
        $this->view->apps = array('0' => '') + $this->loadModel('application')->getapplicationNameCodePairs();
        $this->display();
    }

    /**
     * 解除状态联动
     */
    public function unlockseparate($requirementID)
    {
        if($_POST)
        {
            $changes = $this->requirement->updateLock($requirementID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes || $this->post->comment)
            {
                $actionID = $this->loadModel('action')->create('requirement', $requirementID, 'securedLock', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $this->view->title   = $this->lang->requirement->edit;
        $this->view->requirement = $this->requirement->getByID($requirementID);
        $this->display();
    }

    /**
     * 同步失败重新推送
     * @param $id
     */
    public function push($id)
    {
        $this->dao->update(TABLE_REQUIREMENT)->set('feedbackStatus')->eq('toexternalapproved')->where('id')->eq($id)->exec();
        $this->loadModel('action')->create('requirement', $id, 'repush', "重新推送");
        $this->loadModel('requirement')->pushfeedback($id);
        $response['result']  = 'success';
        $response['message'] = '重新推送';
        $response['locate']  = $this->createLink('requirement', 'view', array('requirementID' => $id));
        die(js::locate($this->createLink('requirement', 'view', "requirementID=$id"), 'parent.parent'));
    }

    //执行超时邮件需求任务-内部
    public function sendmailByOutTime()
    {
        $res = $this->requirement->sendmailByOutTime();

        a($res);
    }

    //执行超时邮件需求任务-外部
    public function sendmailByOutTimeOutside()
    {
        $res = $this->requirement->sendmailByOutTimeOutSide();
        a($res);
    }

    /**
     * @Notes: 返回超期维护
     * @Date: 2023/8/10
     * @Time: 15:03
     * @Interface defend
     * @param $requirementID
     */
    public function defend($requirementID)
    {
        $requirement = $this->requirement->getByID($requirementID);
        if($requirement->deptPassTime == '0000-00-00 00:00:00') $requirement->deptPassTime = '';
        if($requirement->innovationPassTime == '0000-00-00 00:00:00') $requirement->innovationPassTime = '';
        if($requirement->feekBackStartTime == '0000-00-00 00:00:00') $requirement->feekBackStartTime = '';
        if($requirement->feekBackStartTimeOutside == '0000-00-00 00:00:00') $requirement->feekBackStartTimeOutside = '';
        if($_POST)
        {
            $info = $this->requirement->defend($requirement);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $actionID = $this->loadModel('action')->create('requirement', $requirementID, 'defend');
            $this->action->logHistory($actionID, $info);

            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }
        $this->view->requirement = $requirement;
        $this->display();
    }


    /**
     * @Notes: 是否纳入反馈超期
     * @Date: 2023/10/10
     * @Time: 10:06
     * @Interface feedbackOver
     * @param $requirementID
     */
    public function feedbackOver($requirementID)
    {
        $requirement = $this->requirement->getByID($requirementID);
        if($_POST)
        {
            $feedbackOver = $_POST['feedbackOver'] ?? '';
            if(empty($_POST['feedbackOver'])){
                dao::$errors['feedbackOver'] = sprintf($this->lang->requirement->emptyObject, $this->lang->requirement->feedbackOver);

                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $this->dao->update(TABLE_REQUIREMENT)->set('feedbackOver')->eq($feedbackOver)->where('id')->eq($requirementID)->exec();

            $historyRequirement = new stdClass();
            $historyRequirement->feedbackOver = $requirement->feedbackOver;

            $newRequirement = new stdClass();
            $newRequirement->feedbackOver = $feedbackOver;

            $actionID = $this->loadModel('action')->create('requirement', $requirementID, 'updateFeedbackOver');
            $this->action->logHistory($actionID, common::createChanges($historyRequirement, $newRequirement));

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }

        $this->view->requirement = $requirement;
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
        $requirement = $this->requirement->getByID($requirementID);
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
            $info = $this->requirement->editEnd($requirement);
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

    public function runCli()
    {
        $res['inside'] = $this->loadModel('requirement')->updateRequirementIfOverDate('ifOverDate'); //内部反馈超时 未做过处理的
        $res['out'] = $this->loadModel('requirement')->updateRequirementIfOverDate('ifOverTimeOutSide'); //外部反馈超时 未做过处理的
        echo json_encode($res);die;
    }

    public function pushByHand($requirementID)
    {
        $res = $this->loadModel('requirement')->pushfeedback($requirementID);
        return $res;
    }

    //需求任务和需求条目的状态联动
    public function runStatus()
    {
        $res = $this->loadModel('requirement')->changeStatus();
        echo json_encode($res);die;
    }

    //需求任务和需求意向的状态联动
    public function runOpinionStatus()
    {
        $res = $this->loadModel('opinion')->changeStatus();
        echo json_encode($res);die;
    }

}