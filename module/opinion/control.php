<?php
class opinion extends control
{
    /**
     * Browse opinion list.
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
        /* 加载requirement模块，可以使用该模块的配置，语言项，model方法。*/
        /**
         * @var demandModel $demandModel
         * @var requirementModel $requirementModel
         */
        $requirementModel = $this->loadModel('requirement');
        $demandModel = $this->loadModel('demand');
        $browseType = strtolower($browseType);

        /* By search. 构建页面搜索表单。*/
        $queryID = ($browseType == 'bysearch') ? (int)$param : 0;
        $actionURL = $this->createLink('opinion', 'browse', "browseType=bySearch&param=myQueryID");
        $this->opinion->buildSearchForm($queryID, $actionURL);

        /* Load pager. 加载分页逻辑，获取分页对象。*/
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);
        $this->session->set('opinionList', $this->app->getURI(true),'backlog');

        $this->session->set('common_back_url', $this->app->getURI(true),'backlog');

        $this->session->set('opinionHistory', $this->app->getURI(true)); // 浏览记录

        $opinions = $this->opinion->getList($browseType, $queryID, $orderBy, $pager);
        //根据opinionID分类聚合的条目数据
        $demandInfo = $demandModel->allDemandsGroupOpinionID('id,opinionID,acceptUser');
        //根据opinion分类聚合的任务数据
        $requirementInfo = $requirementModel->allRequirementsGroupOpinionID('*');

        foreach ($opinions as $opinion){
            if(in_array($opinion->status,['delivery','online','closed','deleteout'])){
                $opinion->dealUser = '';
            }
            //构造研发责任人，为变更按钮提供权限
            $opinion->acceptUser = '';
            if(isset($demandInfo[$opinion->id]))
            {
                $acceptUser = implode(',',array_unique(array_column($demandInfo[$opinion->id],'acceptUser')));
                $opinion->acceptUser = $acceptUser;
            }
            //变更单下一节点处理人
            $changeInfo = $this->loadModel('opinion')->getPendingOrderByOpinionId($opinion->id);
            $opinion->changeNextDealuser = $changeInfo->nextDealUser ?? '';

            if($opinion->createdBy == 'guestcn')
            {
                $opinion->date = $opinion->createdDate;
            }
            //提出时间
            if($opinion->date == '0000-00-00'){
                $opinion->date = '';
            }
            //期望完成时间
            if($opinion->deadline == '0000-00-00'){
                $opinion->deadline = '';
            }
            //上线时间
            if($opinion->onlineTimeByDemand == '0000-00-00 00:00:00'){
                $opinion->onlineTimeByDemand = '';
            }

            $requirements = array();
            if(isset($requirementInfo[$opinion->id])){
                $requirements = $requirementInfo[$opinion->id];
            }
            if(!empty($requirements)){
                foreach ($requirements as $key => $requirement) {

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

                    $demands = $this->loadModel('demand')->getBrowesByRequirementID($requirement->id);
                    foreach ($demands as $key => $demand) {
                        //开发中 测试中 已发布 已交付 上线成功 处理人置空显示 已关闭也不显示待处理人
                        if(in_array($demand->status, ['feedbacked','build','released','delivery','onlinesuccess','closed'])){
                            $demands[$key]->dealUser  = '';
                        }
                        if(isset($dmap[$demand->createdBy])){
                            $demands[$key]->createdDept = $dmap[$demand->createdBy]->dept;
                        }
                        $demands[$key]->creatorCanEdit = 0;
                        if($this->loadModel('demand')->checkCreatorPri($demand)){
                            $demands[$key]->creatorCanEdit = 1;
                        }
                    }
                    //需求任务的所属项目取需求条目的并集 project字段存在  xxx,xxx 需单独处理 迭代二十五要求年度计划与需求条目并集
                    $demandsOther = $this->requirement->getDemandByRequirement($requirement->id);
                    $acceptUserArr = array_filter(array_unique(array_column($demandsOther,'acceptUser')));
                    $acceptDeptArr = array_filter(array_unique(array_column($demandsOther,'acceptDept')));
                    $requirement->owner = implode(',',$acceptUserArr);
                    $requirement->dept = implode(',',$acceptDeptArr);

                    //变更单下一节点处理人
                    $changeInfo = $this->loadModel('requirement')->getPendingOrderByRequirementId($requirement->id);
                    $requirement->changeNextDealuser = $changeInfo->nextDealUser ?? '';
                }
            }
            $opinion->children = $requirements;
        }
        //后台配置挂起人 需求任务
        $this->loadModel('demand');
        $suspender = array_filter($this->lang->demand->requirementSuspendList);
        $suspendList = [];
        if(!empty($suspender))
        {
            foreach ($suspender as $key=>$value){
                $suspendList[$key] = $key;
            }
        }
        $this->view->executives   = $suspendList;

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
        $createButton = $requirementModel->checkAuthCreate();
        $this->view->createButton = $createButton;
        /* 将相关变量传递到页面。*/
        $this->view->title      = $this->lang->opinion->browse;
        $this->view->opinions   = $opinions;
        $this->view->orderBy    = $orderBy;
        $this->view->pager      = $pager;
        $this->view->browseType = $browseType;
        $this->view->users      = $this->loadModel('user')->getPairs('noletter|noclosed');
        $this->display();
    }

    /**
     * Create a opinion.
     *
     * @access public
     * @return void
     */
    public function create()
    {
        $this->app->loadLang('demand');
        /* 如果是post请求，就会调用model中的create方法，处理业务逻辑。根据model层返回信息，判断是否错误还是创建成功，如果创建成功会将创建操作记录到action表。*/
        if($_POST)
        {
            $this->config->opinion->create->requiredFields .=',background,overview,desc';

            $opinionID = $this->opinion->create();

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $this->loadModel('action')->create('opinion', $opinionID, 'created', $this->post->comment);
            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = inlink('browse');

            $this->send($response);
        }
        $this->app->loadLang('opinion');
        $sourceModeList = $this->lang->opinion->sourceModeList;
        foreach ($sourceModeList as $key=>$item){
            if(in_array($item,['清总接口同步','二线','技术驱动']))
            {
                unset($sourceModeList[$key]);
            }
        }

        $this->opinion->filterJinKeStartValue($this->app->user->account);

        //迭代三十三 拆分时下一节点处理人为后台自定义产品经理
        $productManagerList = $this->lang->demand->productManagerList;
        $this->view->productManagerList = $productManagerList;
        /* 获取创建需求意向时，页面所需的变量。*/
        $this->view->title = $this->lang->opinion->create;
        $this->view->sourceModeList = $sourceModeList;
        $this->view->users = $this->loadModel('user')->getPairs('noclosed');
        $this->display();
    }

    /**
     * Edit a opinion.
     *
     * @param  int $opinionID
     * @access public
     * @return void
     */
    public function edit($opinionID = 0)
    {
        /* 如果是post请求，就会调用model中的update方法，处理业务逻辑。根据model层返回信息，判断是否错误还是编辑成功，如果成功了，会将修改操作记录到action表。*/
        $opinion = $this->loadModel('opinion')->getByID($opinionID);
        if($_POST)
        {
            //创建人不是清总 并且 来源不是清总同步类型
            if($opinion->createdBy != 'guestcn'){ //$opinion->demandCode ?
                if(empty(strip_tags($_POST['background'])))
                {
                    dao::$errors['background'] =  "非外部需求意向【{$this->lang->opinion->background}】不能为空";
                }
                if(empty(strip_tags($_POST['overview'])))
                {
                    dao::$errors['overview'] = "非外部需求意向【{$this->lang->opinion->overview}】不能为空";
                }
                if(dao::isError())
                {
                    $response['result']  = 'fail';
                    $response['message'] = dao::getError();
                    $this->send($response);
                }
            }
            $changes = $this->opinion->update($opinionID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $actionID = $this->loadModel('action')->create('opinion', $opinionID, 'edited', $this->post->comment);
            $this->action->logHistory($actionID, $changes);

            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = inlink('view', "opinionID=$opinionID");

            $this->send($response);
        }

        $this->app->loadLang('opinion');
        $sourceModeList = $this->lang->opinion->sourceModeList;
        if($opinion->createdBy != 'guestcn'){
            foreach ($sourceModeList as $key=>$item){
                if(in_array($item,['清总接口同步','二线','技术驱动']))
                {
                    unset($sourceModeList[$key]);
                }
            }
        }

        //处理页面只读数据 变更中、已拆分、审核已通过
        $readonly = false;
        if($opinion->createdBy != 'guestcn' && in_array($opinion->status,['underchange','subdivided','pass']))
        {
            $readonly =  true;
        }
        if($opinion->createdBy == 'guestcn')
        {
            $readonly =  true;
        }

        $this->view->readonly     = $readonly;
        $this->view->title   = $this->lang->opinion->edit;
        $this->view->sourceModeList = $sourceModeList;
        $this->view->users   = $this->loadModel('user')->getPairs('noclosed');
        $this->view->opinion = $this->loadModel('opinion')->getByID($opinionID);
        $this->opinion->filterJinKeStartValue($this->view->opinion->createdBy);
        $this->display();
    }

    /**
     * View a opinion.
     *
     * @param  int    $opinionID
     * @access public
     * @return void
     */
    public function view($opinionID = 0)
    {
        $this->app->loadLang('demand');
        /* 设置需求详情页面返回的url连接。*/
        $this->session->set('demandList', $this->app->getURI(true), 'backlog');
        /**
         * @var projectPlanModel $projectPlanModel
         * @var opinionModel $opinionModel
         * @var demandModel $demandModel
         * @var requirementModel $requirementModel
         */
        $projectPlanModel = $this->loadModel('projectplan');
        $opinionModel = $this->loadModel('opinion');
        $demandModel = $this->loadModel('demand');
        $requirementModel = $this->loadModel('requirement');
        $opinionInfo = $opinionModel->getByID($opinionID);
        if(in_array($opinionInfo->status,['delivery','online','deleteout'])){
            $opinionInfo->dealUser = '';
        }
        //判断审核需求意向权限
        $opinionInfo->reviewOpinionDealUser = $opinionInfo->dealUser;

        //变更单下一节点处理人
        $changeInfo = $this->loadModel('opinion')->getPendingOrderByOpinionId($opinionID);
        $opinionInfo->changeNextDealuser = $changeInfo->nextDealUser ?? '';
        //转换多行文本换行显示问题
        $opinionInfo->background = str_replace(chr(13),'<br>',$opinionInfo->background);
        $opinionInfo->overview = str_replace(chr(13),'<br>',$opinionInfo->overview);

        //处理时间0000的情况
        $this->dealEmptyTime($opinionInfo);

        $changes = $this->loadModel('requirementchange')->getByDemandNumber($opinionInfo->demandCode);
        $demands = $demandModel->getDemandByOpinionID($opinionID);
        //构造研发责任人，为变更按钮提供权限
        $opinionInfo->acceptUser = '';
        if(!empty($demands))
        {
            $acceptUser = implode(',',array_unique(array_column($demands,'acceptUser')));
            $opinionInfo->acceptUser = $acceptUser;
        }
        $requirementInfo = $requirementModel->getRequirementInfoByOpinionID($opinionID);
        //需求意向的所属项目取需求条目与年度计划的并集
        $ownProjectArr = !empty($opinionInfo->project) ? explode(',',$opinionInfo->project): [];
        $demandProjectArr = array_column($demands,'project');
        $requirementProjectArr = array_column($requirementInfo,'project');
        if(!empty($requirementProjectArr))
        {
            $str = '';
            foreach ($requirementProjectArr as $requirementProject){
                $str .= $requirementProject;
            }
            $requirementProjectArr = explode(',',$str);
        }
        $mergeProjectArr = array_merge($ownProjectArr,$demandProjectArr,$requirementProjectArr);
        $projectArray = array_filter(array_unique($mergeProjectArr));
        $projectList = [];
        if(!empty($projectArray)){
            $projectList = $projectPlanModel->getPlanInProjectIDs($projectArray);
        }
        /* 查询需求意向详情及其相关变量信息，用于详情展示。*/
        //后台配置挂起人 需求任务
        $suspender = array_filter($this->lang->demand->requirementSuspendList);
        $suspendList = [];
        if(!empty($suspender))
        {
            foreach ($suspender as $key=>$value){
                $suspendList[$key] = $key;
            }
        }
        $this->view->executives   = $suspendList;

        //后台配置挂起人 需求意向
        $suspenderOpinion = array_filter($this->lang->demand->opinionSuspendList);
        $executivesList = [];
        if(!empty($suspenderOpinion))
        {
            foreach ($suspenderOpinion as $key=>$value){
                $executivesList[$key] = $key;
            }
        }

        //构造需求全生命周期跟踪矩阵数据
        $lifeOpinionInfo = $this->buildInfo($opinionInfo);
        $changeInfo = $opinionModel->getChangeInfoByOpinionId($opinionID);
        //自定义变更解锁人
        $unLock = array_filter(array_keys($this->lang->demand->unLockList));
        $this->view->executivesOpinion = $executivesList;
        $this->view->title   = $this->lang->opinion->view;
        $this->view->unLock  = $unLock;
        $this->view->title   = $this->lang->opinion->view;
        $this->view->users   = $this->loadModel('user')->getPairs('noclosed|noletter');
        $this->view->actions = $this->loadModel('action')->getList('opinion', $opinionID);
        $this->view->lifeOpinionInfo = $lifeOpinionInfo;
        $this->view->opinion = $opinionInfo;
        $this->view->changes = $changes;
        $this->view->demands = $demands;
        $this->view->requirements = $requirementInfo;
        $this->view->changeInfo  = $changeInfo;
        $this->view->projectList =  $projectList;
        $this->display();
    }


    /**
     * @Notes:构造生命周期统计表数据
     * @Date: 2023/7/20
     * @Time: 14:46
     * @Interface buildInfo
     * @param $opinionInfo
     * @return array
     */
    public function buildInfo($opinionInfo)
    {
        $lifeOpinionInfo = array();

        $lifeOpinionInfo['id'] = $opinionInfo->id;
        $lifeOpinionInfo['name'] = $opinionInfo->code.'('.$opinionInfo->name.')';

        $lifeRequirements = array();
        $allCount = 0;
        if(!empty($opinionInfo->requirements))
        {
            //需求任务构造
            foreach ($opinionInfo->requirements as $requirementKey => $requirement)
            {
                if(!empty($requirement))
                {
                    $lifeRequirements[$requirementKey]['id']   = $requirement->id;
                    $lifeRequirements[$requirementKey]['name'] = $requirement->code.'('.$requirement->name.')';
                    //需求条目构造
                    $demandData = $this->loadModel('demand')->getByRequirementID('id,code,title',$requirement->id);
                    if(!empty($demandData)){
                        foreach ($demandData as $demandKey => $demand)
                        {
                            $lifeRequirements[$requirementKey]['demands'][$demandKey]['id'] = $demand->id;
                            $lifeRequirements[$requirementKey]['demands'][$demandKey]['name'] = $demand->code.'('.$demand->title.')';
                        }
                    }else{
                        $lifeRequirements[$requirementKey]['demands'][0]['id'] = '';
                        $lifeRequirements[$requirementKey]['demands'][0]['name'] = '-';
                    }
                }else{
                    $lifeRequirements[$requirementKey]['demands'][0]['id'] = '';
                    $lifeRequirements[$requirementKey]['demands'][0]['name'] = '-';
                }
                $demandCount = count($lifeRequirements[$requirementKey]['demands']);
                $allCount += $demandCount;
                $lifeRequirements[$requirementKey]['demandCount'] = $demandCount;
            }
        }else{
            $lifeRequirements[0]['id'] = '';
            $lifeRequirements[0]['name'] = '-';
            $lifeRequirements[0]['demandCount']  = 0;
            $lifeRequirements[0]['demands'][0]['id']   =  '';
            $lifeRequirements[0]['demands'][0]['name'] =  '-';
        }
        $lifeOpinionInfo['allCount'] = $allCount;
        $lifeOpinionInfo['requirements'] = $lifeRequirements;
        return $lifeOpinionInfo;
    }

    /**
     * @Notes: 处理时间0000的情况
     * @Date: 2023/6/19
     * @Time: 16:45
     * @Interface dealEmptyTime
     * @param $opinion
     * @return mixed
     */
    public function dealEmptyTime($opinion)
    {
        if($opinion->onlineTimeByDemand == '0000-00-00 00:00:00' || $opinion->onlineTimeByDemand == '0000-00-00' || empty($opinion->onlineTimeByDemand)){
            $opinion->onlineTimeByDemand = '';
        }
        //接收时间
        if($opinion->receiveDate == '0000-00-00 00:00:00' || $opinion->receiveDate == '0000-00-00' || empty($opinion->receiveDate)){
            $opinion->receiveDate = '';
        }
        //需求最新变更时间
        if($opinion->lastChangeTime == '0000-00-00 00:00:00' || $opinion->lastChangeTime == '0000-00-00' || empty($opinion->lastChangeTime)){
            $opinion->lastChangeTime = '';
        }
        //期望完成时间
        if($opinion->deadline == '0000-00-00 00:00:00' || $opinion->deadline == '0000-00-00' || empty($opinion->deadline)){
            $opinion->deadline = '';
        }
        //提出时间
        if($opinion->date == '0000-00-00 00:00:00' || $opinion->date == '0000-00-00' || empty($opinion->date)){
            $opinion->date = '';
        }
        //交付时间
        if($opinion->solvedTime == '0000-00-00 00:00:00' || $opinion->solvedTime == '0000-00-00' || empty($opinion->solvedTime)){
            $opinion->solvedTime = '';
        }
        return $opinion;
    }

    /**
     * Project: chengfangjinke
     * Method: suspend
     * User: Tony Stark
     * Year: 2022
     * Date: 2022/1/6
     * Time: 13:13
     * Desc: This is the code comment. This method is called suspend.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $opinionID
     */
    public function suspend($opinionID = 0)
    {
        if($_POST)
        {
            $changes = $this->opinion->suspend($opinionID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes || $this->post->comment != '')
            {
                $actionID = $this->loadModel('action')->create('opinion', $opinionID, 'suspended', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $this->view->title   = $this->lang->opinion->suspend;
        $this->view->opinion = $this->opinion->getByID($opinionID);
        $this->display();
    }

    public function assignment($id)
    {
        $this->app->loadLang('demand');
        if($_POST)
        {
            $changes = $this->opinion->assignment($id);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $actionID = $this->loadModel('action')->create('opinion', $id, 'Assigned', $this->post->comment, $this->post->dealUser);
            $this->action->logHistory($actionID, $changes);

            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = 'parent';

            $this->send($response);

        }
        //迭代三十三 指派人为后台自定义产品经理
        $productManagerList = $this->lang->demand->productManagerList;
        $this->view->productManagerList = $productManagerList;

        $this->view->opinion = $this->opinion->getByID($id);
        $this->view->users   = $this->loadModel('user')->getPairs('noclosed|noletter');
        $this->display();
    }

    public function review($id)
    {
        if($_POST)
        {
            $this->opinion->review($id);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $actionId = $this->loadModel('action')->create('opinion', $id, 'review', $this->post->comment);

            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = 'parent';

            $this->send($response);

        }
        $this->view->users   = $this->loadModel('user')->getPairs('noclosed|noletter');
        $this->view->opinion = $this->loadModel('opinion')->getByID($id);
        $this->display();
    }

    /**
     * @Notes: 审批变更流程
     * @Date: 2023/6/29
     * @Time: 17:12
     * @Interface reviewchange
     * @param $opinionID
     */
    public function reviewchange($opinionID)
    {
        /**
         * @var opinionModel $opinionModel
         * @var demandModel $demandModel
         * @var fileModel $fileModel
         * @var requirementModel $requirementModel
         */
        $opinionModel = $this->loadModel('opinion');
        $demandModel = $this->loadModel('demand');
        $fileModel = $this->loadModel('file');
        $requirementModel = $this->loadModel('requirement');

        //获取审批中的变更单
        $pendingChangeOrderInfo = $opinionModel->getPendingOrderByOpinionId($opinionID);
        $opinion = $opinionModel->getByID($opinionID);

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
            $this->opinion->reviewchange($changeID,$opinionID);

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

        $changeInfo = $opinionModel->getChangeInfoByChangeId($changeID);

        $affectRequirementIds = $changeInfo->affectRequirement;
        $affectRequirementArr = [];
        if(!empty($affectRequirementIds))
        {
            foreach (explode(',',$affectRequirementIds) as $value)
            {
                $requirementInfo = $requirementModel->getByID($value);
                $requirementStatus = zget($this->lang->requirement->statusList,$requirementInfo->status);
                $affectRequirementArr[$value]['id'] = $requirementInfo->id;
                $affectRequirementArr[$value]['name'] = $requirementInfo->code.'('.$requirementInfo->name. "_". $requirementStatus .")"."<br />";
            }
        }

        //受影响需求条目
        $affectDemandIds = $changeInfo->affectDemand;
        $affectDemandArr = [];
        if(!empty($affectDemandIds))
        {
            foreach (explode(',',$affectDemandIds) as $value)
            {
                $demandInfo = $demandModel->getByID($value);
                $demandStatus = zget($this->lang->demand->statusList,$demandInfo->status);
                $affectDemandArr[$value]['id'] = $demandInfo->id;
                $affectDemandArr[$value]['name'] = $demandInfo->code.'('.$demandInfo->title. "_". $demandStatus .")"."<br />";
            }
        }

        //处理附件
        $opinion->opinionFiles = [];
        $changeInfo->changeFiles = [];
        if(!empty($changeInfo->opinionFile))
        {
            $filesIDs = explode(',',$changeInfo->opinionFile);
            $opinion->opinionFiles = $fileModel->getByObjectHaveDelete($filesIDs);
        }
        if(!empty($changeInfo->changeFile))
        {
            $changeFilesIDs = explode(',',$changeInfo->changeFile);
            $changeInfo->changeFiles = $fileModel->getByObjectHaveDelete($changeFilesIDs);
        }


        $this->view->opinion    = $opinion;
        $this->view->changeInfo  = $changeInfo;
        $this->view->affectRequirementArr = $affectRequirementArr;
        $this->view->affectDemandArr      = $affectDemandArr;
        $this->view->nextDealNode = $nextDealNode;
        $this->display();
    }

    public function close($id)
    {
        $opinion = $this->loadModel('opinion')->getByID($id);
        if($opinion->status == 'closed')
        {
            $this->view->canChange = false;
        }
        /**@var requirementModel $requirementModel*/
        $requirementModel = $this->loadModel('requirement');
        $requirementInfo = $requirementModel->getRequirementInfoByOpinionID($id,'id,requirementChangeStatus');
        //任务有变更中不允许意向挂起
        $rChange = true;
        if($requirementInfo)
        {
            $rChangeStatusInfo = array_column($requirementInfo,'requirementChangeStatus');
            foreach ($rChangeStatusInfo as $rChangeStatus)
            {
                if(in_array($rChangeStatus,[2,3]))
                {
                    $rChange = false;
                }
            }
        }

        if($_POST)
        {
            $this->opinion->close($id);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $this->loadModel('action')->create('opinion', $id, 'suspenditem', $this->post->comment);
            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $this->view->rChange     = $rChange;
        $status = $this->dao->select('status')->from(TABLE_OPINION)->where('id')->eq($id)->fetch('status');
        $this->view->status = $status;
        $this->view->opinion = $opinion;

        $this->display();
    }


    public function activate($id)
    {
        if($_POST)
        {
            $this->opinion->activate($id);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $this->loadModel('action')->create('opinion', $id, 'activated', $this->post->comment);

            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }
        $status = $this->dao->select('status')->from(TABLE_OPINION)->where('id')->eq($id)->fetch('status');
        $this->view->status = $status;

        $this->display();
    }

    public function reset($id)
    {
        $opinion = $this->loadModel('opinion')->getByID($id);
        if($opinion->status != 'closed')
        {
            $this->view->canChange = false;
        }
        // 关闭之后重启需求意向
        if($_POST)
        {
            $this->opinion->activate($id);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $this->loadModel('action')->create('opinion', $id, 'reseted', $this->post->comment);

            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $this->display();
    }
    /**
     * Project: chengfangjinke
     * Method: delete
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:49
     * Desc: This is the code comment. This method is called delete.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $opinionID
     */
    public function delete($opinionID)
    {
        /* 如果时post请求，接收参数后删除需求意向，如果意向拆分了条目，将条目也进行删除。删除时记录操作动作，都删除完后，调用发信逻辑，然后刷新页面。*/
        if(!empty($_POST))
        {
            $mailto = $this->post->mailto;
            if(!empty($mailto)){
                $mailto = implode(',',array_filter($this->post->mailto));
            }
            $this->dao->update(TABLE_OPINION)->set('status')->eq('deleted')->set('mailto')->eq($mailto)->where('id')->eq($opinionID)->exec();
            $actionID = $this->loadModel('action')->create('opinion', $opinionID, 'deleted', $this->post->comment);
            $requirements = $this->dao->select('id, name')->from(TABLE_REQUIREMENT)->where('opinion')->eq($opinionID)->fetchPairs();
            if($requirements)
            {
                $this->dao->update(TABLE_REQUIREMENT)->set('status')->eq('deleted')->where('opinion')->eq($opinionID)->exec();
                foreach($requirements as $id => $requirement) $this->loadModel('action')->create('requirement', $id, 'deleted', $this->post->comment);
            }

            $backUrl =  $this->session->opinionList ? $this->session->opinionList : inLink('browse');
            if(isonlybody())
            {
                die(js::closeModal('parent.parent', $backUrl));
            }
            else die(js::locate(inLink('browse'),'parent.parent'));
            die(js::reload('parent'));
        }

        /* 获取需求意向信息及其相关信息，用于删除时展示。*/
        $opinion = $this->opinion->getByID($opinionID);
        $this->view->actions = $this->loadModel('action')->getList('opinion', $opinionID);
        $this->view->opinion = $opinion;
        $this->view->users   = $this->loadModel('user')->getPairs();
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
     * @param $opinionID
     */
    public function subdivide($opinionID)
    {
        if($_POST){
            $this->loadModel('requirement')->subdivide($opinionID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $this->loadModel('action')->create('opinion', $opinionID, 'subdivide');

            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = $this->createLink('opinion', 'view', array('opinionID' => $opinionID));
            $this->send($response);
        }

        $opinion = $this->opinion->getByID($opinionID);
        $this->view->title   = $this->lang->opinion->subdivide;
        $this->view->opinion = $opinion;
        $this->view->users   = $this->loadModel('user')->getPairs('noclosed|nodeleted');
        $this->view->apps = array('0' => '') + $this->loadModel('application')->getapplicationNameCodePairs();

        //变更中，不允许操作
        if($this->view->opinion->changeLock == 2){
            $response['result']  = 'fail';
            $response['message'] = $this->lang->opinion->changeIng;
            $this->send($response);
        }

        if(empty($this->view->opinion->category)){
            $response['result']  = 'fail';
            $response['message'] = "所属意向未补充完整请联系产品经理进行补充";
            $this->send($response);
        }

        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: ajaxGetOwner
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:49
     * Desc: This is the code comment. This method is called ajaxGetOwner.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $groupID
     */
    public function ajaxGetOwner($groupID)
    {
        die(zget($this->lang->opinion->ownerList, $groupID, ''));
    }

    public function editassignedto($id){
        if($_POST)
        {
            $changes = $this->opinion->editassignedto($id);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $actionID = $this->loadModel('action')->create('opinion', $id, 'editassignedto', $this->post->comment, $this->post->dealUser);
            $this->action->logHistory($actionID, $changes);

            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $this->view->users   = $this->loadModel('user')->getPairs('noclosed');
        $this->view->title   = $this->lang->opinion->editassignedto;
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: export
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:49
     * Desc: This is the code comment. This method is called export.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param string $orderBy
     * @param string $browseType
     */
    public function export($orderBy = 'id_desc', $browseType = 'all')
    {
        /* format the fields of every opinion in order to export data. */
        /* 如果是post请求将格式化需求意见的相关字段用以导出数据。*/
        if($_POST)
        {
            $this->opinion->setListValue();

            $this->loadModel('file');
            $opinionLang   = $this->lang->opinion;
            $opinionConfig = $this->config->opinion;

            /* Create field lists. */
            /* 处理将要导出的字段列表。*/
            $fields = $this->post->exportFields ? $this->post->exportFields : explode(',', $opinionConfig->list->exportFields);
            foreach($fields as $key => $fieldName)
            {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = isset($opinionLang->$fieldName) ? $opinionLang->$fieldName : $fieldName;
                unset($fields[$key]);
            }

            /* Get opinions. */
            /* 查询要导出的需求意见数据。*/
            $opinions = array();
            if($this->session->opinionOnlyCondition)
            {
                $opinions = $this->dao->select('*')->from(TABLE_OPINION)->where($this->session->opinionQueryCondition)
                    ->beginIF($this->post->exportType == 'selected')->andWhere('id')->in($this->cookie->checkedItem)->fi()
                    ->orderBy($orderBy)->fetchAll('id');
            }
            else
            {
                $stmt  = $this->dbh->query($this->session->opinionQueryCondition . (($this->post->exportType == 'selected' and $this->cookie->checkedItem) ? " AND $field IN({$this->cookie->checkedItem})" : '') . " ORDER BY " . strtr($orderBy, '_', ' '));

                while($row = $stmt->fetch()) $opinions[$row->id] = $row;
            }
            $opinionIdList = array_keys($opinions);

            /* Get users, products and executions. */
            /* 处理需求意见导出字段的对应值。*/
            $users = $this->loadModel('user')->getPairs('noletter');
            foreach($opinions as $opinion)
            {
                /**
                 * 需求意向的所属项目取需求任务的并集
                 * @var projectPlanModel $projectPlanModel
                 * @var demandModel $demandModel
                 * @var requirementModel $requirementModel
                 */
                $projectPlanModel = $this->loadModel('projectplan');
                $demandModel = $this->loadModel('demand');
                $requirementModel = $this->loadModel('requirement');
                $demands = $demandModel->getDemandByOpinionID($opinion->id);
                $requirementInfo = $requirementModel->getRequirementInfoByOpinionID($opinion->id);
                $ownProjectArr = !empty($opinion->project) ? explode(',',$opinion->project): [];
                $demandProjectArr = array_column($demands,'project');
                $mergeProjectArr = array_merge($ownProjectArr,$demandProjectArr,$requirementInfo);
                $projectArray = array_filter(array_unique($mergeProjectArr));
                $projectDesc=  $projectPlanModel->getPlanInProjectIDs($projectArray);
                if(!empty($projectArray)) $opinion->project = implode(',',array_values(array_column($projectDesc,'name')));

                /*待处理人*/
                if(in_array($opinion->status,['delivery','online'])){
                    if(empty($opinion->changeDealUser)){
                        $dealUser = '';
                    }else{
                        $dealUser = $opinion->changeDealUser;
                    }
                }else{
                    $opinionDealUser = explode(',',$opinion->dealUser);
                    $opinionChangeDealUser = explode(',',$opinion->changeDealUser);
                    $finalDealUser = array_merge($opinionDealUser,$opinionChangeDealUser);
                    $dealUser = implode(',',array_unique(array_filter($finalDealUser)));
                }
                if($opinion->status == 'deleteout')
                {
                    $dealUser = '';
                }

                if(isset($users[$opinion->dealUser]) && !empty($opinion->dealUser))  $opinion->dealUser  = zmget($users,$dealUser);
                if(isset($opinionLang->statusList[$opinion->status]))          $opinion->status     = $opinionLang->statusList[$opinion->status];
                if(isset($opinionLang->categoryList[$opinion->category]))      $opinion->category   = $opinionLang->categoryList[$opinion->category];
                if(isset($opinionLang->sourceModeListOld[$opinion->sourceMode]))  $opinion->sourceMode = $opinionLang->sourceModeListOld[$opinion->sourceMode];
                if(isset($users[$opinion->editedBy]))  $opinion->editedBy  = $users[$opinion->editedBy];
                if(isset($users[$opinion->closedBy]))  $opinion->closedBy  = $users[$opinion->closedBy];
                if(isset($users[$opinion->activedBy]))  $opinion->activedBy  = $users[$opinion->activedBy];
                if(isset($users[$opinion->suspendBy]))  $opinion->suspendBy  = $users[$opinion->suspendBy];
                if(isset($users[$opinion->recoveredBy]))  $opinion->recoveredBy  = $users[$opinion->recoveredBy];
                if(isset($users[$opinion->createdBy]))  $opinion->createdBy  = $users[$opinion->createdBy];
                $assignedTo = '';
                foreach(explode(',', trim($opinion->assignedTo,',')) as $assignedToItem){
                    if($assignedTo){
                        $assignedTo .= '，';
                    }
                    $assignedTo = $assignedTo.zget($users,$assignedToItem);
                }
                $opinion->assignedTo = $assignedTo;
                // if(isset($users[$opinion->owner]))      $opinion->owner      = $users[$opinion->owner];

                $synUnions         = explode(',', $opinion->synUnion);
                $opinion->synUnion = '';
                foreach($synUnions as $synUnion)
                {
                    $synUnion = trim($synUnion);
                    if($synUnion!='' && isset($opinionLang->synUnionList[$synUnion])) $opinion->synUnion .= $opinionLang->synUnionList[$synUnion] . ',';
                }

                $unions = explode(',', $opinion->union);
                $opinion->union = '';
                foreach ($unions as $union)
                {
                    $union = trim($union);
                    if($union!='' && isset($opinionLang->unionList[$union])) $opinion->union .= $opinionLang->unionList[$union] . ',';
                }
                $opinion->createDate = substr($opinion->createdDate, 0, 10);
                $opinion->deadline   = substr($opinion->deadline, 0, 10);
                $opinion->date       = substr($opinion->date, 0, 10);
                $opinion->background = strip_tags($opinion->background);
                $opinion->overview   = strip_tags($opinion->overview);
                $opinion->desc       = strip_tags($opinion->desc);
                $opinion->opinionChangeTimes = $opinion->opinionChangeTimes ?? 0;
                if($opinion->lastChangeTime == '0000-00-00 00:00:00' || empty($opinion->lastChangeTime))
                {
                    $opinion->lastChangeTime = '';
                }
                if($opinion->solvedTime == '0000-00-00 00:00:00' || empty($opinion->solvedTime))
                {
                    $opinion->solvedTime = '';
                }
            }

            /* 将字段和字段的值调用file模块的export2方法进行导出。*/
            $this->post->set('fields', $fields);
            $this->post->set('rows', $opinions);
            $this->post->set('kind', 'opinion');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }
        /* 将导出页面所需的相关变量传递到页面。*/
        $this->view->fileName        = $this->lang->opinion->common;
        $this->view->allExportFields = $this->config->opinion->list->exportFields;
        $this->view->customExport    = true;
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: exportTemplate
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:49
     * Desc: This is the code comment. This method is called exportTemplate.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     */
    public function exportTemplate()
    {
        /* 调用导出模板页面，如果是post请求，将调用setListValue方法处理多选字段的值，然后设置导出的相关信息，调用file模块的export2方法进行导出模板处理。*/
        if($_POST)
        {
            // $this->opinion->setListValue();

            foreach($this->config->opinion->export->templateFields as $field) $fields[$field] = $this->lang->opinion->$field;

            $this->post->set('fields', $fields);
            $this->post->set('kind', 'opinion');
            $this->post->set('rows', array());
            $this->post->set('extraNum',   $this->post->num);
            $this->post->set('fileName',  $this->lang->opinion->opinionTemplate);
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: import
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:49
     * Desc: This is the code comment. This method is called import.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
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
     * Project: chengfangjinke
     * Method: showImport
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:49
     * Desc: This is the code comment. This method is called showImport.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $pagerID
     * @param int $maxImport
     * @param string $insert
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
            $this->opinion->createFromImport();
            if($this->post->isEndPage)
            {
                unlink($tmpFile);
                die(js::locate($this->createLink('opinion','browse'), 'parent'));
            }
            else
            {
                die(js::locate(inlink('showImport', "pagerID=" . ($this->post->pagerID + 1) . "&maxImport=$maxImport&insert=" . zget($_POST, 'insert', '')), 'parent'));
            }
        }

        /* 如果最大导入数量不为空，且导入文件存在，则获取文件内容进行序列化。*/
        if(!empty($maxImport) and file_exists($tmpFile))
        {
            $opinionData = unserialize(file_get_contents($tmpFile));
        }
        else
        {
            /* 初始化变量，获取要导入的字段。*/
            $pagerID       = 1;
            $opinionLang   = $this->lang->opinion;
            $opinionConfig = $this->config->opinion;
            $fields        = explode(',', $opinionConfig->list->exportFields);
            $fields[]      = 'workload';
            foreach($fields as $key => $fieldName)
            {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = isset($opinionLang->$fieldName) ? $opinionLang->$fieldName : $fieldName;
                unset($fields[$key]);
            }

            /* 获取导入文件所有行的数据。*/
            $rows = $this->file->getRowsFromExcel($file);
            $opinionData = array();
            $users = $this->loadModel('user')->getPairs('noletter|noclosed');
            foreach($rows as $currentRow => $row)
            {
                $opinion = new stdclass();
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
                        if($field == 'status'){
                            $opinion->$field = 'created';
                        }
                        $opinion->$field = '';
                        continue;
                    }

                    //if(in_array($field, $opinionConfig->import->ignoreFields)) continue;
                    /* 针对下拉选项字段进行处理，然后赋值转换。*/
                    if(in_array($field, $opinionConfig->export->listFields))
                    {
                        $opinion->$field = $cellValue;
                        if($field == 'union'){
                            $fieldKeys = array();
                            foreach(explode(',', str_replace('，',',',$cellValue)) as $union){
                                $fieldKeys[] =  array_search($union, $opinionLang->{$field . 'List'})?array_search($union, $opinionLang->{$field . 'List'}):'';
                            }
                            $fieldKey = implode(',', $fieldKeys);
                        }elseif(in_array($field, array('createdBy','assignedTo'))){
                            $fieldKey = array_search($cellValue, $users)?array_search($cellValue, $users):$cellValue;
                        }elseif($field=='dealUser'){
                            $fieldKeys = array();
                            foreach(explode(',', str_replace('，',',',$cellValue)) as $user){
                                $fieldKeys[] =  array_search($user, $users)?array_search($user, $users):$user;
                            }
                            $fieldKey = implode(',', $fieldKeys);
                        }else{
                            if(!isset($opinionLang->{$field . 'List'}) or !is_array($opinionLang->{$field . 'List'})) continue;
                            /* when the cell value is key of list then eq the key. */
                            $listKey = array_keys($opinionLang->{$field . 'List'});
                            unset($listKey[0]);
                            unset($listKey['']);
                            $fieldKey =  array_search($cellValue, $opinionLang->{$field . 'List'});
                        }
                        if($fieldKey) $opinion->$field = $fieldKey;
                    }
                    elseif($field == 'background' or $field == 'overview' or $field == 'desc')
                    {
                        /* 针对富文本类型字段内容进行处理。*/
                        $opinion->$field = str_replace("\n", "\n", $cellValue);
                    }
                    else
                    {
                        $opinion->$field = $cellValue;
                    }
                }

                if(empty($opinion->name)) continue;
                $opinionData[$currentRow] = $opinion;
                unset($opinion);
            }
            /* 获取处理好的数据后，写入临时文件中。*/
            file_put_contents($tmpFile, serialize($opinionData));
        }

        /* 当导入文件的内容处理完成后，删除临时文件，并刷新列表页面。*/
        if(empty($opinionData))
        {
            unlink($this->session->fileImport);
            unset($_SESSION['fileImport']);
            echo js::alert($this->lang->excel->noData);
            die(js::locate($this->createLink('opinion','browse')));
        }

        /* 判断导入的数据是否大于系统预设最大导入数，如果大于则对数据进行拆分处理。*/
        $allCount = count($opinionData);
        $allPager = 1;
        if($allCount > $this->config->file->maxImport)
        {
            if(empty($maxImport))
            {
                $this->view->allCount  = $allCount;
                $this->view->maxImport = $maxImport;
                // $this->view->productID = $productID;
                // $this->view->branch    = $branch;
                // $this->view->type      = $type;
                die($this->display());
            }

            $allPager  = ceil($allCount / $maxImport);
            $opinionData = array_slice($opinionData, ($pagerID - 1) * $maxImport, $maxImport, true);
        }
        if(empty($opinionData)) die(js::locate($this->createLink('opinion','browse')));

        /* Judge whether the editedStories is too large and set session. */
        /* 判断要处理的需求意向是否太大，并设置session。*/
        $countInputVars  = count($opinionData) * 11;
        $showSuhosinInfo = common::judgeSuhosinSetting($countInputVars);
        if($showSuhosinInfo) $this->view->suhosinInfo = extension_loaded('suhosin') ? sprintf($this->lang->suhosinInfo, $countInputVars) : sprintf($this->lang->maxVarsInfo, $countInputVars);

        /* 将要导入的数据及其相关变量，传递到页面进行展示。*/
        $this->view->title      = $this->lang->opinion->common . $this->lang->colon . $this->lang->opinion->showImport;
        $this->view->position[] = $this->lang->opinion->showImport;

        $this->view->statusList = $this->lang->opinion->statusList;
        $this->view->opinionData = $opinionData;
        $this->view->allCount    = $allCount;
        $this->view->allPager    = $allPager;
        $this->view->pagerID     = $pagerID;
        $this->view->isEndPage   = $pagerID >= $allPager;
        $this->view->maxImport   = $maxImport;
        $this->view->dataInsert  = $insert;
        $this->view->users       = $this->loadModel('user')->getPairs('noclosed');
        $this->display();
    }

    public function changeOld($opinionID)
    {
        if($_POST)
        {
            /* 当请求方式为post时，调用change方法处理变更逻辑，如果处理成功则记录变更动作，然后返回成功信息。*/
            $changes = $this->opinion->change($opinionID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes)
            {
                $actionID = $this->loadModel('action')->create('opinion', $opinionID, 'changed', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }


            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }
        $this->display();
    }

    /**
     * Change a opion.
     *
     * @param  int    $opinionID
     * @access public
     * @return void
     */
    public function change($opinionID)
    {
        $this->app->loadLang('demand');
        //如果存在审批中的单子不允许发起变更
        /**
         * @var opinionModel $opinionModel
         * @var requirementModel $requirementModel
         */
        $opinionModel = $this->loadModel('opinion');
        $requirementModel = $this->loadModel('requirement');
        $pendingChangeOrderInfo = $opinionModel->getPendingOrderByOpinionId($opinionID);
        $allowChange = true;
        if($pendingChangeOrderInfo){
            $allowChange = false;//提示已存在变更中的单子，不允许再次发起
        }
        $requirementInfo = $requirementModel->getRequirementInfoByOpinionID($opinionID,'id,code,name,`status`,requirementChangeStatus');
        //任务有变更中不允许意向发起变更
        $rChange = true;
        if($requirementInfo)
        {
            $rChangeStatusInfo = array_column($requirementInfo,'requirementChangeStatus');
            foreach ($rChangeStatusInfo as $rChangeStatus)
            {
                if(in_array($rChangeStatus,[2,3]))
                {
                    $rChange = false;
                }
            }
        }

        if($_POST)
        {
            /* 当请求方式为post时，调用change方法处理变更逻辑，如果处理成功则记录变更动作，然后返回成功信息。*/
            $changes = $this->opinion->change($opinionID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes)
            {
                $this->loadModel('action')->create('opinion', $opinionID, 'changed', $this->post->comment);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }
        /**
         * @var deptModel $deptModel
         * @var opinionModel $opinionModel
         */
        $deptModel = $this->loadModel('dept');
        $opinionModel = $this->loadModel('opinion');
        $users = $this->loadModel('user')->getPairs('noletter|noclosed');
        //产创部产品经理
        $poUsers = $deptModel->getPoUserByDeptId(1);
        foreach ($poUsers as $name)
        {
            $poUsers[$name] = zget($users,$name,'');
        }

        //部门负责人
        $deptLeaderCN = $deptModel->getFieldByDeptId('id,manager',$this->app->user->dept);
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
        $this->view->define        = $define;

        $opinion      = $opinionModel->getByID($opinionID);


        $requirement = [];
        $affectDemand = [];
        $selectDemandIds = '';
        $selectRequirementIds = '';
        $affectRequirementIds = array_column($requirementInfo,'id');
        if(!empty($requirementInfo))
        {
            foreach ($requirementInfo as $value)
            {
                $requirementStatus = zget($this->lang->requirement->statusList,$value->status);
                $requirement[$value->id] = $value->code.'('.$value->name. "_". $requirementStatus .")";
            }
            $selectRequirementIds = implode(',',$affectRequirementIds);
            $affectDemand = $this->buildAffectDemands($affectRequirementIds);
            $selectDemandIds = implode(',',array_keys($affectDemand));
        }

        //处理时间0000
        $this->dealEmptyTime($opinion);
        $this->view->opinion     = $opinion;
        $this->view->requirement = $requirement;
        $this->view->deptLeader  = array('0' => '') + $deptLeader;
        $this->view->poUsers     = array('0' => '') + $poUsers;
        $this->view->users       = $users;
        $this->view->allowChange = $allowChange;
        $this->view->rChange     = $rChange;
        $this->view->selectRequirementIds = $selectRequirementIds;
        $this->view->affectDemand         = $affectDemand;
        $this->view->selectDemandIds      = $selectDemandIds;
        $this->display();
    }


    /**
     * @Notes:构造需求条目数据
     * @Date: 2023/11/10
     * @Time: 17:29
     * @Interface buildAffectDemands
     * @param $requirementIds
     */
    public function buildAffectDemands($requirementIds)
    {
        /**
         * @var demandModel $demandModel
         */
        $demandModel = $this->loadModel('demand');
        $demands = $demandModel->getDemandsByRequirementIds($requirementIds,'id,code,title,`status`');
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
        return $affectDemands;

    }

    /**
     * @Notes: 撤销变更单
     * @Date: 2023/6/26
     * @Time: 17:53
     * @Interface revoke
     * @param $changeID
     * @param $opinionID
     */
    public function revoke($changeID,$opinionID)
    {
        if($_POST)
        {
            /* 当请求方式为post时，调用change方法处理变更逻辑，如果处理成功则记录变更动作，然后返回成功信息。*/
            $changes = $this->opinion->revoke($changeID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes)
            {
                $this->loadModel('action')->create('opinion', $opinionID, 'revoke', $this->post->revokeRemark);
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
     */
    public function changeview($changeID,$opinionID)
    {
        /**
         * @var deptModel $deptModel
         * @var opinionModel $opinionModel
         * @var demandModel $demandModel
         * @var fileModel $fileModel
         * @var requirementModel $requirementModel
         */
        $opinionModel = $this->loadModel('opinion');
        $demandModel = $this->loadModel('demand');
        $deptModel = $this->loadModel('dept');
        $fileModel = $this->loadModel('file');
        $requirementModel = $this->loadModel('requirement');
        $users = $this->loadModel('user')->getPairs('noletter|noclosed');
        //产品经理
        $poUsers = $deptModel->getPoUser();
        foreach ($poUsers as $name)
        {
            $poUsers[$name] = zget($users,$name,'');
        }
        $opinion = $opinionModel->getByID($opinionID);
        $changeInfo = $opinionModel->getChangeInfoByChangeId($changeID);
        $affectRequirementIds = $changeInfo->affectRequirement;
        $affectRequirementArr = [];
        if(!empty($affectRequirementIds))
        {
            foreach (explode(',',$affectRequirementIds) as $value)
            {
                $requirementInfo = $requirementModel->getByID($value);
                $requirementStatus = zget($this->lang->requirement->statusList,$requirementInfo->status);
                $affectRequirementArr[$value]['id'] = $requirementInfo->id;
                $affectRequirementArr[$value]['name'] = $requirementInfo->code.'('.$requirementInfo->name. "_". $requirementStatus .")"."<br />";
            }
        }

        //受影响需求条目
        $affectDemandIds = $changeInfo->affectDemand;
        $affectDemandArr = [];
        if(!empty($affectDemandIds))
        {
            foreach (explode(',',$affectDemandIds) as $value)
            {
                $demandInfo = $demandModel->getByID($value);
                $demandStatus = zget($this->lang->demand->statusList,$demandInfo->status);
                $affectDemandArr[$value]['id'] = $demandInfo->id;
                $affectDemandArr[$value]['name'] = $demandInfo->code.'('.$demandInfo->title. "_". $demandStatus .")"."<br />";
            }
        }

        //处理附件
        $opinion->opinionFiles = [];
        $changeInfo->changeFiles = [];
        if(!empty($changeInfo->opinionFile))
        {
            $filesIDs = explode(',',$changeInfo->opinionFile);
            $opinion->opinionFiles = $fileModel->getByObjectHaveDelete($filesIDs);
        }
        if(!empty($changeInfo->changeFile))
        {
            $changeFilesIDs = explode(',',$changeInfo->changeFile);
            $changeInfo->changeFiles = $fileModel->getByObjectHaveDelete($changeFilesIDs);
        }

        //部门负责人
        $deptLeaderCN = $deptModel->getFieldByDeptId('id,manager',$this->app->user->dept);
        $deptLeader = $deptModel->getRenameListByAccountStr($deptLeaderCN->manager);
        //处理时间0000
        $this->dealEmptyTime($opinion);
        $bookNodes = $this->loadModel('review')->getNodes('opinionchange', $changeID, $changeInfo->version);
        $this->view->changeInfo  = $changeInfo;
        $this->view->affectRequirementArr = $affectRequirementArr;
        $this->view->affectDemandArr      = $affectDemandArr;
        $this->view->bookNodes   = $bookNodes;
        $this->view->opinion     = $opinion;
        $this->view->deptLeader  = array('0' => '') + $deptLeader;
        $this->view->poUsers     = array('0' => '') + $poUsers;
        $this->view->users       = $users;
        $this->display();
    }

    /**
     * @Notes: 编辑变更单
     * @Date: 2023/7/13
     * @Time: 9:39
     * @Interface editchange
     * @param $changeID
     * @param $opinionID
     */
    public function editchange($changeID,$opinionID)
    {
        $this->app->loadLang('demand');
        if($_POST)
        {
            /* 当请求方式为post时，调用change方法处理变更逻辑，如果处理成功则记录变更动作，然后返回成功信息。*/
            $changes = $this->opinion->editchange($changeID,$opinionID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes)
            {
                $actionID = $this->loadModel('action')->create('opinion', $opinionID, 'editchanged', $this->post->revokeRemark);
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        /**
         * @var deptModel $deptModel
         * @var opinionModel $opinionModel
         * @var requirementModel $requirementModel
         */
        $requirementModel = $this->loadModel('requirement');
        $opinionModel = $this->loadModel('opinion');
        $deptModel = $this->loadModel('dept');
        $users = $this->loadModel('user')->getPairs('noletter|noclosed');
        //产品经理
        $poUsers = $deptModel->getPoUser();
        foreach ($poUsers as $name)
        {
            $poUsers[$name] = zget($users,$name,'');
        }

        //部门负责人
        $deptLeaderCN = $deptModel->getFieldByDeptId('id,manager',$this->app->user->dept);
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
        $this->view->define        = $define;

        $opinion = $opinionModel->getByID($opinionID);
        //处理时间0000
        $this->dealEmptyTime($opinion);
        $changeInfo = $opinionModel->getChangeInfoByChangeId($changeID);
        if(strpos($changeInfo->alteration,'changeTitle') === false)        $changeInfo->changeTitle = '';
        if(strpos($changeInfo->alteration,'opinionBackground') === false)   $changeInfo->changeBackground = '';
        if(strpos($changeInfo->alteration,'opinionOverview') === false)     $changeInfo->changeOverview = '';
        if(strpos($changeInfo->alteration,'opinionDeadline') === false)      $changeInfo->changeDeadline = '';
        //处理回填数据

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

        $requirementInfo = $requirementModel->getRequirementInfoByOpinionID($opinionID,'id,code,name,`status`');
        $requirement = [];
        $affectDemand = [];
        if(!empty($requirementInfo))
        {
            foreach ($requirementInfo as $value)
            {
                $requirementStatus = zget($this->lang->requirement->statusList,$value->status);
                $requirement[$value->id] = $value->code.'('.$value->name. "_". $requirementStatus .")";
            }
        }

        $affectRequirementRadio = 'no';//是否涉及受影响条目
        if(!empty($changeInfo->affectRequirement))
        {
            $affectRequirementRadio = 'yes';
            $affectDemand = $this->buildAffectDemands($changeInfo->affectRequirement);
        }

        $affectDemandRadio = 'no';//是否涉及受影响条目
        if(!empty($changeInfo->affectDemand))
        {
            $affectDemandRadio = 'yes';
        }

        $this->view->affectDemandRadio  = $affectDemandRadio;
        $this->view->affectRequirementRadio  = $affectRequirementRadio;
        $this->view->affectDemand  = $affectDemand;
        $this->view->requirement  = $requirement;
        $this->view->changeInfo  = $changeInfo;
        $this->view->opinion     = $opinion;
        $this->view->deptLeader  = array('0' => '') + $deptLeader;
        $this->view->poUsers     = array('0' => '') + $poUsers;
        $this->view->users       = $users;
        $this->display();
    }

    /**
     * 解除状态联动
     */
    public function unlockseparate($opinionID)
    {
        if($_POST)
        {
            $changes = $this->opinion->updateLock($opinionID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes || $this->post->comment)
            {
                $actionID = $this->loadModel('action')->create('opinion', $opinionID, 'securedLock', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }
        $this->view->unlock  = !empty($unLock) ? $unLock : [];
        $this->view->title   = $this->lang->opinion->edit;
        $this->view->opinion = $this->opinion->getByID($opinionID);
        $this->display();
    }

    /**
     * Desc: 恢复
     * Date: 2022/8/11
     * Time: 15:21
     *
     * @param int $opinionID
     *
     */
    public function recoveryed($opinionID = 0)
    {
        if($_POST)
        {
            $changes = $this->opinion->recoveryed($opinionID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes)
            {
                $actionID = $this->loadModel('action')->create('opinion', $opinionID, 'recoveryed', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $this->view->title  = $this->lang->opinion->recoveryed;
        $this->display();
    }

    /**
     * Desc: 忽略
     *
     * @param int $opinionID
     *
     */
    public function ignore($opinionID = 0, $notice = 0)
    {
        if($_POST)
        {
            $changes = $this->opinion->ignore($opinionID);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $actionID = $this->loadModel('action')->create('opinion', $opinionID, 'ignore', $this->post->comment);
            $this->action->logHistory($actionID, $changes);

            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $this->view->title  = $this->lang->opinion->ignore;
        $this->view->notice  = $notice;
        $this->display();
    }
}
