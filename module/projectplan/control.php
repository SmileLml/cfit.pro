<?php
class projectPlan extends control
{
    /**
     * Project: chengfangjinke
     * Method: browse
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:20
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
    public function browse($browseType = 'all', $param = 0, $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1, $isSecondline = 0,$shanghaipart = 0)
    {
        $browseType = strtolower($browseType);
        $this->setSearchList();
        /* By search. */
        $queryID = ($browseType == 'bysearch')  ? (int)$param : 0;
        $actionURL = $this->createLink('projectplan', 'browse', "browseType=bySearch&param=myQueryID&order=$orderBy&recTotal=$recTotal&recPerPage=$recPerPage&pageID=$pageID&secondline=$isSecondline");
        $this->projectplan->buildSearchForm($queryID, $actionURL);
        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);
        $this->session->set('projectplanList', $this->app->getURI(true));
        $userDept = $this->loadModel('user')->getUserDeptName($this->app->user->account);
        $plans = $this->projectplan->getList($browseType, $queryID, $orderBy, $pager, $isSecondline,'projectplan',$shanghaipart);
        foreach ($plans as $key=>$plan){

//            if(in_array($plan->status,array("yearreject","yearstart","yearpass","start"))){
            //|| ($plan->status=='pass' && $plan->insideStatus=='pass')
            if(!$plans[$key]->reviewers && ((in_array($plan->status,array("yearreject","yearstart","yearpass","start","reject")) && $plan->changeStatus !== 'pending') || ($plan->status=='pass' && $plan->insideStatus=='wait')) ){
                $plans[$key]->reviewers = $plan->owner;
            }

            //$yearNodes = $this->loadModel('review')->getNodes('projectplanyear', $planID, $yearVersion);
        }

        //分管领导获取以及总经理获取
        $users = $this->loadModel('user')->getPairs('noletter');
        $deptInfo = $this->loadModel('dept')->getDeptPairs();
        $deptIds = implode(',',array_keys($deptInfo));
        $leaderOfDeptsMerge = $this->loadModel('dept')->getByIDs($deptIds);
        $leadersMergeInfo = array_flip(array_filter(array_unique(array_column($leaderOfDeptsMerge,'leader1'))));
        $leaderCN = array_flip($leadersMergeInfo);
        foreach ($leaderCN as $name){
            $leader[$name] = $users[$name];
        }
        if(!isset($leader['hetielin'])){
            $arrCTO = array('hetielin'=>'贺铁林');
            $leader = array_merge($arrCTO,$leader);
        }
        if(!isset($leader['luoyongzhong'])){
            $arrGM = array('luoyongzhong'=>'罗永忠');
            $leader = array_merge($arrGM,$leader);
        }
        $this->view->title      = $this->lang->projectplan->common;
        $this->view->plans      = $plans;
        $this->view->orderBy    = $orderBy;
        $this->view->pager      = $pager;
        $this->view->param      = $param;
        $this->view->leader     = array_keys($leader);
        $this->view->isSecondline      = $isSecondline;
        $this->view->shanghaipart      = $shanghaipart;
        $this->view->browseType = $browseType;
        $this->view->depts      = $this->loadModel('dept')->getOptionMenu();
        $this->view->users      = $this->user->getPairs('noletter|noclosed');
        $this->view->userDept   = $userDept->deptName == '平台架构部' ? true : false;
        $this->view->outsidePlans = $this->loadModel('outsideplan')->getPairs();
        $this->display();
    }

    
    private function setSearchList()
    {
        $outside = $this->loadModel('outsideplan')->getPairs();
        $this->config->projectplan->search['params']['outsideProject']['values'] += $outside;
        $this->loadModel('opinion');
        $this->config->projectplan->search['params']['category']['values'] = $this->lang->opinion->categoryList;

        $this->config->projectplan->search['params']['app']['values'] = array(0 => '') + $this->loadmodel('application')->getapplicationNameCodePairs();
        $this->config->projectplan->search['params']['bearDept']['values'] = $this->loadModel('dept')->getOptionMenu();
        $values = $this->loadModel('outsideplan')->getPairs();
        $vlist = [];
        foreach ($values as $key => $value)
        {
            $vlist[','.$key.','] = $value;
        }
        $this->config->projectplan->search['params']['outsideProject']['values'] = $vlist;
        $values = $this->loadModel('outsideplan')->getTaskPairs();
        $vlist = [];
        foreach ($values as $key => $value)
        {
            $vlist[','.$key.','] = $value;
        }
        $this->config->projectplan->search['params']['outsideSubProject']['values'] = $vlist;
        $values = $this->loadModel('outsideplan')->getSubProjectPairs();
        $vlist = [];
        foreach ($values as $key => $value)
        {
            $vlist[','.$key.','] = $value;
        }
        $this->config->projectplan->search['params']['outsideTask']['values'] = $vlist;
    }
    /**
     * Project: chengfangjinke
     * Method: create
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:20
     * Desc: This is the code comment. This method is called create.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     */
    public function create($secondline  = 0)
    {
        $this->app->loadLang('opinion');
        $this->app->loadConfig('execution');

        if($_POST)
        {
            $this->checkPost();//校验字段
            $planID = $this->projectplan->create();

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $this->loadModel('action')->create('projectplan', $planID, 'created', $this->post->comment);

            if(isonlybody()) $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'closeModal' => true, 'callback' => "parent.loadProjects($planID)"));

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $vars = [
                'browseType' => 'all',
            ];
            $response['locate']  = inlink('browse', $vars);

            $this->send($response);
        }
        $products = $this->loadModel('product')->getProductInfo();
        $this->view->title    = $this->lang->projectplan->create;
        $this->view->users    = $this->loadModel('user')->getPairs('noclosed');
        $this->view->lines    = array('' => '') + $this->loadModel('product')->getLinePairs();
        $this->view->products = [''] + $products;
        $this->view->secondline = $secondline;
        $this->view->depts    =  array('' => '') + $this->loadModel('dept')->getOptionMenu();
        if(isset($this->view->depts[0])){
            unset($this->view->depts[0]);
        }

        $this->view->apps     = array(0 => '') + $this->loadmodel('application')->getapplicationNameCodePairs();
        $this->view->outsideIDs = '';
        $this->view->outsideNames = '';
        $this->view->subOutsideIDs = '';
        $this->view->subOutsideNames = '';
//        $this->view->outsideProject = $this->loadModel('outsideplan')->getPairsHavingTasks();
        $this->view->outsideTask        = $this->loadModel('outsideplan')->getTaskPairsNameUnion();
        $this->display();
    }

    public function editPlanOpinion($planID,$opinionID){

        if($_POST)
        {

            $changes = $this->projectplan->editPlanOpinion();
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            if($changes)
            {
                $actionID = $this->loadModel('action')->create('projectplan', $planID, 'editplanopinion');
                $this->action->logHistory($actionID, $changes);
            }
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;

            $response['locate']  = 'parent';
            $this->send($response);

        }
        $plan = $this->projectplan->getSimpleByID($planID);
        $planRequirementList = [];
        if($plan->requirement){
            $planRequirementIDList = explode(',',$plan->requirement);
            $requirementList = $this->loadModel("requirement")->getByOpinion($opinionID);
            $requirementIDList = array_column($requirementList,'id');
            $intersect = array_intersect($planRequirementIDList,$requirementIDList);
            if($intersect){
                foreach ($intersect as $value){
                    $planRequirementList[$value] = $requirementList[$value];
                }
            }
        }

        $this->view->planRequirementList =$planRequirementList;
        $this->view->opinionID = $opinionID;
        $this->view->planID = $planID;

        $this->display();
    }
    public function editPlanRequirement($planID,$requirementID){

        if($_POST)
        {

            $changes = $this->projectplan->editPlanRequirement();
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            if($changes)
            {
                $actionID = $this->loadModel('action')->create('projectplan', $planID, 'editplanrequirement');
                $this->action->logHistory($actionID, $changes);
            }
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;

            $response['locate']  = 'parent';
            $this->send($response);

        }
        $plan = $this->projectplan->getSimpleByID($planID);
        $planDemandList = [];
        if($plan->requirement){
            $planDemandIDList = explode(',',$plan->demand);
            $demandList = $this->loadModel("demand")->getBrowesByRequirementID($requirementID);
            $demandList = array_column($demandList,null,'id');

            $demandIDList = array_column($demandList,'id');
            $intersect = array_intersect($planDemandIDList,$demandIDList);
            if($intersect){
                foreach ($intersect as $value){
                    $planDemandList[$value] = $demandList[$value];
                }
            }
        }

        $this->view->planDemandList =$planDemandList;
        $this->view->requirementID = $requirementID;
        $this->view->planID = $planID;

        $this->display();

    }

    public function editDelayYear($planID)
    {
        $this->view->plan     = $this->projectplan->getByID($planID);
        if($_POST)
        {
            if(array_key_exists($_POST['isDelayPreYear'],$this->lang->projectplan->isDelayPreYearList)){
                $this->dao->update(TABLE_PROJECTPLAN)->data(['isDelayPreYear'=>$_POST['isDelayPreYear']])->where('id')->eq($planID)->exec();
            }else{
                dao::$errors['realRelease'] = $this->lang->projectplan->isDelayPreYearError;

            }

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->loadModel('action')->create('projectplan', $planID, 'editdelayyear',$_POST['isDelayPreYear']);
            $this->send($response);
        }
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: edit
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:20
     * Desc: This is the code comment. This method is called edit.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $planID
     */
    public function edit($planID = 0, $confirm = 'no')
    {
        $this->app->loadLang('opinion');
        $this->app->loadConfig('execution');
        $this->view->plan     = $this->projectplan->getByID($planID);
        if($_POST)
        {
            $this->checkPost();//校验字段
            $changes = $this->projectplan->update($planID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes)
            {
                $actionID = $this->loadModel('action')->create('projectplan', $planID, 'edited');
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = inlink('view', "planID=$planID");

            $this->send($response);
        }
        $products = $this->loadModel('product')->getProductInfo();
        $this->view->title    = $this->lang->projectplan->edit;
        $this->view->users    = $this->loadModel('user')->getPairs('noclosed');
        $this->view->plan     = $this->projectplan->getByID($planID);
        $this->view->lines    = array('' => '') + $this->loadModel('product')->getLinePairs();
        $this->view->products = [''] + $products;
        $this->view->depts    =  array('' => '') + $this->loadModel('dept')->getOptionMenu();
        if(isset($this->view->depts[0])){
            unset($this->view->depts[0]);
        }

        $this->view->leader = $this->dept->getByID($this->app->user->dept);
//        $this->view->outsideProject = $this->loadModel('outsideplan')->getPairs();
        if($this->view->plan->outsideProject){
            $outsideProjectList = $this->loadModel('outsideplan')->getPairsByID($this->view->plan->outsideProject);
            $this->view->outsideIDs = implode(',',array_keys($outsideProjectList));
            $this->view->outsideNames = implode(',',$outsideProjectList);
        }else{
            $this->view->outsideIDs = '';
            $this->view->outsideNames = '';
        }
        if($this->view->plan->outsideSubProject){
            $outsideSubProjectList  = $this->loadModel('outsideplan')->getSubProjectPairs($this->view->plan->outsideSubProject);
            $this->view->subOutsideIDs = implode(',',array_keys($outsideSubProjectList));
            $this->view->subOutsideNames = implode(',',$outsideSubProjectList);
        }else{
            $this->view->subOutsideIDs = '';
            $this->view->subOutsideNames = '';
        }
        $this->view->apps     = array(0 => '') + $this->loadmodel('application')->getapplicationNameCodePairs();
//        $this->view->outsideProject     = $this->loadModel('outsideplan')->getPairsHavingTasks();
//        $this->view->outsideTask        = $this->loadModel('outsideplan')->getTaskPairs($this->view->plan->outsideTask);
        $this->view->outsideTask        = $this->loadModel('outsideplan')->getTaskPairsNameUnion();
//        $this->view->outsideSubProject  = $this->loadModel('outsideplan')->getSubProjectPairs($this->view->plan->outsideSubProject);
        $this->view->productsRelated = isset($this->view->plan->productsRelated) ? json_decode(base64_decode($this->view->plan->productsRelated),1) : [['productId' =>'','realRelease' =>'','realOnline' =>'']];
        $this->view->planStages = isset($this->view->plan->planStages) ? json_decode(base64_decode($this->view->plan->planStages), 1) : [['stageBegin' =>'','stageEnd' =>'']];
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: view
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:20
     * Desc: This is the code comment. This method is called view.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $planID
     */
    public function view($planID = 0)
    {
        $this->app->loadLang('opinion');

        $plan = $this->projectplan->getByID($planID);
        $creationID = $plan->creation->id ?? 0;
        $this->view->creationID  = $creationID;
        $this->view->plan  = $plan;
        $this->view->title = $this->lang->projectplan->view;

        $this->view->actions  = $this->decodeActions($this->loadModel('action')->getList('projectplan', $planID));

        $this->view->users    = $this->loadModel('user')->getPairs('noclosed|noletter');
        $this->view->lines    = array('' => '') + $this->loadModel('product')->getLinePairs();
        $this->view->depts    = $this->loadModel('dept')->getOptionMenu();

        $this->view->apps     = array('' => '') + $this->loadmodel('application')->getapplicationNameCodePairs();
        $this->view->plans    = $this->projectplan->getPairs();
        /** @var outsideplanModel $outsideModel */
        $outsideModel = $this->loadModel('outsideplan');
        $this->view->outsideproject  = $outsideModel->getPairs();
        $this->view->outsideTask        = $this->view->plan->outsideTask ? $outsideModel->getTasks($this->view->plan->outsideTask) : [];
        $this->view->outsideSubProject  = $this->view->plan->outsideSubProject ? $outsideModel->getSubProjects($this->view->plan->outsideSubProject) : [];
        $this->view->requirementList = $this->loadModel('project')->getUserRequirementList($plan->id);
//        $this->view->relatedObject   = $this->loadModel('secondline')->getByID($plan->project, 'project');
        //2023-09-14 隐藏此块代码，计划展示实际属于超纲，实际应取项目空间展示。金信无直接关联问题单和条目，取值逻辑存在问题需修正。修数据源，修取数逻辑未定。
//        $relatedObject   = $this->loadModel('secondline')->getNewByID($plan->project, 'project', 1);
//
//        //强制显示顺序
//        $relatedObjectList = [];
//        $relatedObjectList['projectDemand']     = isset($relatedObject['projectDemand']) ? $relatedObject['projectDemand'] : [];
//        $relatedObjectList['projectProblem']       = isset($relatedObject['projectProblem']) ? $relatedObject['projectProblem'] : [];
//
//        $relatedObjectList['projectModify']     = isset($relatedObject['projectModify']) ? $relatedObject['projectModify'] : [];
//        if(isset($relatedObject['modify']) && $relatedObject['modify']){
//            $relatedObjectList['projectModify'] = array_merge($relatedObject['modify'],$relatedObjectList['projectModify']);
//        }
//        $relatedObjectList['projectGain']       = isset($relatedObject['projectGain']) ? $relatedObject['projectGain'] : [];
////        $relatedObjectList['projectFix']        = isset($relatedObject['projectFix']) ? $relatedObject['projectFix'] : [];
//        $relatedObjectList['projectOutwardDelivery']       = isset($relatedObject['outwardDelivery']) ? $relatedObject['outwardDelivery'] : [];
////        $relatedObjectList['projectModifycncc'] = isset($relatedObject['projectModifycncc']) ? $relatedObject['projectModifycncc'] : [];
//        $relatedObjectList['projectGainQz'] = isset($relatedObject['projectGainQz']) ? $relatedObject['projectGainQz'] : [];



        /*foreach ($relatedObject as $k => $v) //以防新增或遗漏
        {
            $relatedObjectList[$k] = $v;
        }*/
        //年度计划审批节点
        $yearVersion = $this->view->plan->yearVersion;

        $yearNodes = $this->loadModel('review')->getNodes('projectplanyear', $planID, $yearVersion);
        $isShow = false;
        if($yearNodes){
            if(isset($yearNodes[1])){
                if($yearNodes[1]->nodeCode == 'builtLeader'){
                    $isShow = true;
                }
            }
            //退回后跳过部门负责人，详情页仍需要将之前节点部门负责人通过最新记录显示
            if($yearNodes[0]->nodeCode == 'builtPerson'){
                for ($i=$yearVersion-1; $i>=0; $i--){
                    $beforeNode = $this->loadModel('projectplan')->getNodesByWhere('projectplanyear', $planID, $i,'pass','deptLeader');
                    if($beforeNode){
                        $yearNodes =array_merge($beforeNode,$yearNodes);
                        break;
                    }
                }
            }

        }

        //变更年度计划审批节点
        $changeVersion = $this->view->plan->changeVersion;
//        a($this->view->plan->changeVersion);
        $changeYearNodes = [];
        for ($i = $changeVersion; $i >= 0; $i--){

            $changeYearNodes[$i] = $this->loadModel('review')->getNodes('planchange', $planID, $i);
            if(!$changeYearNodes[$i]){
                unset($changeYearNodes[$i]);
            }
        }

        //项目主从关系
        $this->view->mainRelationInfo = $mainRelationInfo = $this->loadModel("projectplanmsrelation")->getByMainPlanID($planID);
        $this->view->slaveRelationInfo = $slaveRelationInfo = $this->loadModel("projectplanmsrelation")->getBySlavePlanID($planID);
        $this->view->relationProjectplanList = [];
        if($mainRelationInfo || $slaveRelationInfo){
            $planArr = [$planID];
            if($mainRelationInfo){
                $planArr = array_merge($planArr,explode(',',$mainRelationInfo->slavePlanID));
            }
            if($slaveRelationInfo){
                foreach ($slaveRelationInfo as $slave){
                    $planArr[] = $slave->mainPlanID;
                }
            }


            $this->view->relationProjectplanList = array_column($this->projectplan->getByIDMultipleList(array_unique($planArr),"id,name"),'name','id');

        }

        $allow = $this->loadModel('projectplan')->isBuildDeptPerson();

        //发起人看到详情所有节点
        $submittedBy = $plan->submitedBy;
        if($this->app->user->account == $submittedBy){
            $allow .= $submittedBy;
        }

        if(!empty($allow)){
            $isShow = true;
        }

        //->andWhere("status")->in(['pending','pass'])
        $ChangeList = $this->dao->select('id,status,planID,planRemark,createdDate,isreview')->from(TABLE_PROJECTPLANCHANGE)->where("planID")->eq($planID)->orderBy("id desc")->fetchAll();
        if($ChangeList){
            foreach ($ChangeList as $key=>$change){
                $ChangeList[$key] = $this->loadModel('file')->replaceImgURL($change, 'planRemark');
            }
        }
        $this->view->ChangeList = $ChangeList;
        $this->view->allow  = $allow;
        $this->view->isShow  = $isShow;
//        $this->view->relatedObject = $relatedObjectList;
        $this->view->bookNodes = $this->loadModel('review')->getNodes('projectplan', $planID, $this->view->plan->version);
        $this->view->yearNodes = !empty($yearNodes[0]) ? $yearNodes :[];
        $this->view->changeYearNodesList = !empty($changeYearNodes[0]) ? $changeYearNodes :[];
        $this->view->products  = $this->projectplan->getCreationProducts($plan->project);
        $this->view->productsRelated = isset($this->view->plan->productsRelated) ? json_decode(base64_decode($this->view->plan->productsRelated), 1) : [['productId' =>'', 'productName' =>'','realRelease' =>'','realOnline' =>'']];
        $this->view->planStages = isset($this->view->plan->planStages) ? json_decode(base64_decode($this->view->plan->planStages), 1) : [['stageBegin' =>'','stageEnd' =>'']];
//        a($changeYearNodes[1]);exit();

        //查询涉及产品名
        $productsRelatedIds = [];
        foreach ($this->view->productsRelated as $productsRelated)
        {
            if(empty($productsRelated['productId'])) continue;
            $productsRelatedIds[] = $productsRelated['productId'];
        }
        $relationProducts = $this->loadModel('product')->getProductNamesByIds($productsRelatedIds);
        foreach ($this->view->productsRelated as &$productsRelated)
        {
            if(empty($productsRelated['productId'])) continue;
            $productsRelated['productName'] = $relationProducts[$productsRelated['productId']];
        }
        if($plan->oldview == -1){ //旧版的demand都更新为-1了 旧版显示旧的页面
            $this->display('projectplan', 'oldview');
        } else {
            $this->display();
        }

    }


    public function ajaxshowdiffchange($changeID=0){
        $this->app->loadLang('opinion');
        $this->app->loadConfig('execution');
        $res = $this->projectplan->ajaxshowdiffchange($changeID);


        $this->view->productsRelated = isset($res->content->productsRelated) ? json_decode(base64_decode($res->content->productsRelated),1) : [['productId' =>'','realRelease' =>'','realOnline' =>'']];
        $this->view->planStages = isset($res->content->planStages) ? json_decode(base64_decode($res->content->planStages), 1) : [['stageBegin' =>'','stageEnd' =>'']];

        if(!isset($res->content->outsideProject)){
            $res->content->outsideProject = '';
        }
        if(!isset($res->content->outsideSubProject)){
            $res->content->outsideSubProject = '';
        }
        if(!isset($res->content->outsideTask)){
            $res->content->outsideTask = '';
        }
        $this->view->changefield = array_keys($res->new);

//        $res->new->productsRelated = isset($res->new->productsRelated) ? json_decode(base64_decode($res->new->productsRelated),1) : [['productId' =>'','realRelease' =>'','realOnline' =>'']];
//        $res->new->planStages = isset($res->new->planStages) ? json_decode(base64_decode($res->new->planStages), 1) : [['stageBegin' =>'','stageEnd' =>'']];
//
//        $res->old->productsRelated = isset($res->old->productsRelated) ? json_decode(base64_decode($res->old->productsRelated),1) : [['productId' =>'','realRelease' =>'','realOnline' =>'']];
//        $res->old->planStages = isset($res->old->planStages) ? json_decode(base64_decode($res->old->planStages), 1) : [['stageBegin' =>'','stageEnd' =>'']];
//a($res->content);
//exit();
//        $this->view->projectplanChange = $res;
        $products = $this->loadModel('product')->getProductInfo();
        $this->view->title    = $this->lang->projectplan->edit;
        $this->view->users    = $this->loadModel('user')->getPairs('noclosed');
//        $this->view->plan     = $this->projectplan->getByID($res->planID);
        $this->view->plan     = $res->content;
        $this->view->lines    = array('' => '') + $this->loadModel('product')->getLinePairs();
        $this->view->products = [''] + $products;
        $this->view->depts    = $this->loadModel('dept')->getOptionMenu();


        $this->view->leader = $this->dept->getByID($this->app->user->dept);

        $this->view->apps     = array(0 => '') + $this->loadmodel('application')->getapplicationNameCodePairs();
        $this->view->outsideProject     = $this->loadModel('outsideplan')->getPairsHavingTasks();
        $this->view->outsideTask        = $this->loadModel('outsideplan')->getTaskPairs($res->content->outsideTask);
        $this->view->outsideSubProject  = $this->loadModel('outsideplan')->getSubProjectPairs($res->content->outsideSubProject);

//        a($this->view->planStages);
        $this->display();
    }

    public function ajaxGetAllChangeInfo($planID){
        $ChangeList = $this->dao->select('id,status,planID,planRemark,createdDate,isreview')->from(TABLE_PROJECTPLANCHANGE)->where("planID")->eq($planID)->andWhere("status")->in(['pass','reject'])->orderBy("id desc")->fetchAll();
        if($ChangeList){
            foreach ($ChangeList as $key=>$change){
                $ChangeList[$key] = $this->loadModel('file')->replaceImgURL($change, 'planRemark');
            }
        }
        $this->view->ChangeList = $ChangeList;

        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: initProject
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:20
     * Desc: This is the code comment. This method is called initProject.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $planID
     */
    public function initProject($planID = 0)
    {
        $this->app->loadLang('opinion');
        $this->app->loadConfig('execution');

        if($_POST)
        {
            $data = $this->projectplan->initProject($planID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if(is_numeric($data))
            {
                $this->loadModel('action')->create('projectplan', $planID, 'initproject');
            }
            else
            {
                $actionID = $this->loadModel('action')->create('projectplan', $planID, 'initproject');
                $this->action->logHistory($actionID, $data);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = inlink('view', "planID=$planID");

            $this->send($response);
        }

        $this->view->title    = $this->lang->projectplan->initProject;
        $this->view->actions  = $this->loadModel('action')->getList('projectplan', $planID);
        $this->view->plan     = $this->projectplan->getByID($planID);
        $this->view->plans    = $this->projectplan->getPairs();
        $this->view->creation = $this->projectplan->getCreationByID($planID);
        $this->view->users    = $this->loadModel('user')->getPairs('noclosed|noletter');
        $this->view->lines    = array('' => '') + $this->loadModel('product')->getLinePairs();
        $this->view->products = $this->product->getPairs();
        $this->view->depts    = $this->loadModel('dept')->getOptionMenu();
        if(isset($this->view->depts[0])){
            unset($this->view->depts[0]);
        }
        $this->view->apps     = array('' => '') + $this->loadmodel('application')->getapplicationNameCodePairs();
        $this->display();
    }

    /**
     * tongyanqi 2022-4-19 修改立项书
     */
    public function editProjectDoc($planID = 0)
    {
        $oldCreation =  $this->projectplan->getCreationByID($planID);
        if($oldCreation == false){
            echo js::alert("立项书不存在");
            die(js::locate('back'));
        }

        $this->app->loadLang('opinion');
        $this->app->loadConfig('execution');

        if($_POST)
        {
            $data = $this->projectplan->initProject($planID, 1);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $actionID = $this->loadModel('action')->create('projectplan', $planID, 'renewinitproject');

            $this->action->logHistory($actionID, $data);
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = inlink('view', "planID=$planID");

            $this->send($response);
        }

        $this->view->title    = $this->lang->projectplan->initProject;
        $this->view->actions  = $this->loadModel('action')->getList('projectplan', $planID);
        $this->view->plan     = $this->projectplan->getByID($planID);
        $this->view->plans    = $this->projectplan->getPairs();
        $this->view->creation = $this->projectplan->getCreationByID($planID);
        $this->view->users    = $this->loadModel('user')->getPairs('noclosed|noletter');
        $this->view->lines    = array('' => '') + $this->loadModel('product')->getLinePairs();
        $this->view->products = $this->product->getPairs();
        $this->view->depts    = $this->loadModel('dept')->getOptionMenu();
        if(isset($this->view->depts[0])){
            unset($this->view->depts[0]);
        }
        $this->view->apps     = array('' => '') + $this->loadmodel('application')->getapplicationNameCodePairs();
        $this->display();
    }
    /**
     * Project: chengfangjinke
     * Method: submit
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:20
     * Desc: This is the code comment. This method is called submit.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $planID
     */
    public function submit($planID = 0)
    {
        if($_POST)
        {
            $this->projectplan->submit($planID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $actionID = $this->loadModel('action')->create('projectplan', $planID, 'submitapproval');
            //$this->projectplan->sendmail($planID, $actionID);

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }
        $this->view->ownerDepts = $this->loadModel('dept')->getOptionMenu();
        $this->view->deptParent = $this->loadModel('dept')->getDeptAndChild();
        $this->view->plan  = $this->projectplan->getByID($planID);
        //是否是内部项目
        $isInerProject = false;
        $ownerbasis = explode(',',$this->view->plan->basis);
        if(in_array(6,$ownerbasis)){
            $isInerProject = true;
        }

        //前台需要过滤的部门
        //如果是内部项目  只过滤 项目负责人  所在部门。
        if($isInerProject){
            $filterDepts[] = zget($this->view->deptParent,$this->app->user->dept);
        }else{
            //外部项目过滤  产创和架构
            $filterDepts = $this->lang->projectplan->submitFilterDept;
            // 如果 补齐 项目负责人 的部门
            if(!in_array(zget($this->view->deptParent,$this->app->user->dept),$this->lang->projectplan->submitFilterDept)){
                $filterDepts[] = zget($this->view->deptParent,$this->app->user->dept);
            }
        }




        $this->view->title = $this->lang->projectplan->initProject;

        $this->view->users = $this->loadModel('user')->getPairs('noclosed|noletter');
        //上海分公司会签需要显示二级部门
        $isShanghai = $this->projectplan->isShangHai($planID);
        $this->view->depts = $this->projectplan->getTopDepts($isShanghai );
        //过滤 产创/架构/本部门
        foreach ($this->view->depts as $key=>$deptval){
            if(in_array($deptval->id,$filterDepts)){
                unset($this->view->depts[$key]);
            }
        }

        $deptPairs = array();
        foreach($this->view->depts as $dept) $deptPairs[$dept->id] = $dept->name;
        $this->view->deptPairs = $deptPairs;
        $this->view->filterDepts = $filterDepts;
        $this->view->requirement    = [''] + $this->loadModel('requirement')->getAllPairs();
        $this->view->opinionvalue = explode(",",$this->view->plan->opinion);
        $this->view->requirementvalue = explode(",",$this->view->plan->requirement);
        $this->view->issyncjob = $this->view->plan->requirement ? 1 : 0;
        $this->view->isShangHai = $isShanghai; //是否上海项目

        $this->display();
    }

    /* 申请年度计划审批。*/
    public function yearReview($planID = 0)
    {
        if($_POST)
        {
            $this->projectplan->yearReview($planID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $this->loadModel('action')->create('projectplan', $planID, 'yearreview');

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }
        $planPerson = $this->loadModel('projectplan')->isBuildDeptPerson();
        if(!empty($planPerson)){
            $this->view->planPerson  = $planPerson;
        }
        $manageInfo = $this->loadModel('dept')->getByID($this->app->user->dept);
        $plan = $this->projectplan->getByID($planID);
        $reviewStage = $plan->reviewStage;
        $this->view->title = $this->lang->projectplan->yearReview;
        $this->view->plan  = $plan;
        $this->view->users = $this->loadModel('user')->getPairs('noclosed|noletter');
        $this->view->leaders = $this->loadModel('user')->getLeaders();
        $this->view->depts = $this->projectplan->getAllDepts();
        $this->view->manager = $manageInfo->manager1;
        $this->view->reviewStage = $reviewStage;


        $deptPairs = array();
        foreach($this->view->depts as $dept) $deptPairs[$dept->id] = $dept->name;
        $this->view->deptPairs = $deptPairs;

        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: review
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:21
     * Desc: This is the code comment. This method is called review.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $planID
     */
    public function review($planID = 0)
    {
        $projectplan = $this->projectplan->getByID($planID);
        if($_POST)
        {
            if($projectplan->status == "pass")
            {
                dao::$errors[] = "该流程节点已审批完，请刷新页面查看审批结果";
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $result = $this->projectplan->review($planID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $actionID = $this->loadModel('action')->create('projectplan', $planID, 'planApproval', $this->post->comment.$result['appendComment'], $this->post->result);
            //2023-08-07 因拒绝邮件重复发送，注释掉，待测试通过后可删除此段逻辑
            /*if($result['result'] == 'reject'){
                $this->projectplan->sendmail($planID, $actionID);
            }*/

            //如果审批通过。则单独 发给 组织姐QA和系统管理员。两者均在后台自定义中配置
            if($result['mailsend']){
                $this->projectplan->sendPlanProjectedMail($planID, $actionID);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $nodes = $this->loadModel('review')->getNodes('projectplan', $planID, $projectplan->version);

//        $this->view->curPendingNode = $this->review->getFirstPendingNode('projectplan', $planID, $projectplan->version);
        //获取当前审核节点
        $this->view->curPendingNode = $this->loadModel("review")->getReviewByAccount('projectplan', $planID,$this->app->user->account, $projectplan->version);

        $this->view->title   = $this->lang->projectplan->initProject;
        $this->view->plan    = $this->projectplan->getByID($planID);
        $this->view->deptUsers = array(0 => 'NA') + $this->loadModel('dept')->getDeptUserPairs($this->app->user->dept);
        $this->view->users     = $this->loadModel('user')->getPairs('noletter');
        $this->view->depts     = $this->loadModel('dept')->getOptionMenu();
        $this->view->bookNodes = $nodes;

        $this->display();
    }
    //年度计划 部分操作记录
    public function addPlanAction($actionResult){

        $this->projectplan->addPlanAction($actionResult);
        //触发邮件
        $this->projectplan->sendActionmail($actionResult['planID']);


    }



    public function yearReviewing($planID = 0)
    {
        $projectplan = $this->projectplan->getByID($planID);
        if($_POST)
        {
            $result = $this->projectplan->yearReviewing($planID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $this->loadModel('action')->create('projectplan', $planID, 'yearReviewing', $this->post->comment, $this->post->result);
            //记录年度计划审批通过不通过
            if(isset($result['actionflag']) and $result['actionflag'] == true){
                $this->addPlanAction($result['actionActResult']);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }
        //防止在地盘待处理和年度计划页面重复审批
        $repeatVerify = $this->projectplan->selectActionInfo($planID,$projectplan->yearVersion,'projectplanyear');
        if(!$repeatVerify){
            die(js::alert($this->lang->projectplan->approvalEmpty) .js::closeModal('parent'));
        }

        //退回后到架构部接口人要自动获取退回前指派的架构师
        $architect = [];
        $yearVersion = $projectplan->yearVersion;
        if($projectplan->reviewStage == 2 && $projectplan->rejectStatus == 3 ){
            $nodeCode = $this->lang->projectplan->nodeCode[3];
            $architect = $this->loadModel('projectplan')->dealRejectStageData($planID,$yearVersion-1,$nodeCode);
        }

        $users = $this->loadModel('user')->getPairs('noletter');

        //分管领导获取
        $deptInfo = $this->loadModel('dept')->getDeptPairs();

        $shanghaiDeptList = $this->loadModel('dept')->getAllChildId(30); //获取所有上海部门
        $deptIds =  array_diff(array_keys($deptInfo),$shanghaiDeptList);//保留非上海领导
        $deptIds = implode(',',array_values($deptIds));

        $leaderOfDeptsMerge = $this->loadModel('dept')->getByIDs($deptIds);
        $leadersMergeInfo = array_flip(array_filter(array_unique(array_column($leaderOfDeptsMerge,'leader1'))));

        //由于最后节点总经理必为luoyongzhong，此时分领导处需要将其删除，避免多次审批
        unset($leadersMergeInfo['luoyongzhong']);
        unset($leadersMergeInfo['hetielin']);
        $leaderCN = array_flip($leadersMergeInfo);
        foreach ($leaderCN as $name){
            $leader[$name] = $users[$name];
        }

        $cto = array('hetielin'=>'贺铁林');
        $title = $this->lang->projectplan->nodeCodeDesc[$this->lang->projectplan->nodeCode[$projectplan->reviewStage]];
        $nodes = $this->loadModel('review')->getNodes('projectplanyear', $planID, $yearVersion);
        $this->view->plan      = $projectplan;
        $this->view->title     = $title;
        $this->view->architect = $architect;
        $this->view->leader    = $leader;
        $this->view->cto       = $cto;
        $this->view->deptUsers = array(0 => 'NA') + $this->loadModel('dept')->getDeptUserPairs($this->app->user->dept);
        $this->view->users     = $users;
        $this->view->depts     = $this->loadModel('dept')->getOptionMenu();
        $this->view->yearNodes = $nodes;
        $this->view->userdept  = $this->app->user->dept;
        $this->view->leaders = $this->loadModel('user')->getLeaders();

        $this->display();
    }

    public function yearBatchReviewing($planID = 0)
    {

        $projectplan = $this->projectplan->getByID($planID);
//        a($projectplan);
        if($_POST)
        {
            $result = $this->projectplan->yearBatchReviewing($planID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            foreach ($result as $key=>$res){
                $this->loadModel('action')->create('projectplan', $key, 'yearBatchReviewing', $this->post->comment, $this->post->result);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }
        //退回后到架构部接口人要自动获取退回前指派的架构师
        $architect = [];
        $yearVersion = $projectplan->yearVersion;
        if($projectplan->reviewStage == 2 && $projectplan->rejectStatus == 3 ){
            $nodeCode = $this->lang->projectplan->nodeCode[3];
            $architect = $this->loadModel('projectplan')->dealRejectStageData($planID,$yearVersion-1,$nodeCode);
        }

        $users = $this->loadModel('user')->getPairs('noletter');

        //分管领导获取
        $deptInfo = $this->loadModel('dept')->getDeptPairs();
        $deptIds = implode(',',array_keys($deptInfo));
        $leaderOfDeptsMerge = $this->loadModel('dept')->getByIDs($deptIds);

        $leadersMergeInfo = array_flip(array_filter(array_unique(array_column($leaderOfDeptsMerge,'leader1'))));
        //由于最后节点总经理必为luoyongzhong，此时分领导处需要将其删除，避免多次审批
        unset($leadersMergeInfo['luoyongzhong']);
        unset($leadersMergeInfo['hetielin']);
        $leaderCN = array_flip($leadersMergeInfo);
        foreach ($leaderCN as $name){
            $leader[$name] = $users[$name];
        }

        $cto = array('hetielin'=>'贺铁林');
        $title = $this->lang->projectplan->nodeCodeDesc[$this->lang->projectplan->nodeCode[$projectplan->reviewStage]];
        $nodes = $this->loadModel('review')->getNodes('projectplanyear', $planID, $yearVersion);
        $this->view->plan      = $projectplan;
        $this->view->title     = $title;
        $this->view->architect = $architect;
        $this->view->leader    = $leader;
        $this->view->cto       = $cto;
        $this->view->deptUsers = array(0 => 'NA') + $this->loadModel('dept')->getDeptUserPairs($this->app->user->dept);
        $this->view->users     = $users;
        $this->view->depts     = $this->loadModel('dept')->getOptionMenu();
        $this->view->yearNodes = $nodes;
        $this->view->userdept  = $this->app->user->dept;
        $this->view->leaders = $this->loadModel('user')->getLeaders();

        $this->display();
    }

    /**
     * 变更年度计划审批
     * @param int $planID
     */
    public function changeReview($planID = 0)
    {
        $projectplan = $this->projectplan->getByID($planID);
        $changeReviewInfo = $this->projectplan->getChangePlanInfo($planID,$projectplan->changeVersion);
        $changeReviewInfo = $this->loadModel('file')->replaceImgURL($changeReviewInfo, 'planRemark');
        if($_POST)
        {
            $result = $this->projectplan->changeReview($planID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $this->loadModel('action')->create('projectplan', $planID, 'changereview', $this->post->comment, $this->post->result);
            //记录年度计划变更审批通过不通过
            if(isset($result['actionflag']) and $result['actionflag'] == true){
                $this->addPlanAction($result['actionActResult']);
            }
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }
        $title = $this->lang->projectplan->nodeCodeDesc[$this->lang->projectplan->changeNodeCode[$projectplan->changeStage]];
        $this->view->plan      = $projectplan;
        $this->view->title     = $title;
        $this->view->leaderApproval = strpos($projectplan->leaderApproval,"isBoss") !== false ? 'isBoss' : '';
//        $this->view->changePlaInfo = $changeReviewInfo;
        $this->view->planRemark = $changeReviewInfo->planRemark;
        $this->view->deptUsers = array(0 => 'NA') + $this->loadModel('dept')->getDeptUserPairs($this->app->user->dept);
        $this->view->users     = $this->loadModel('user')->getPairs('noletter');
        $this->view->depts     = $this->loadModel('dept')->getOptionMenu();
        $this->view->userdept  = $this->app->user->dept;
        $this->view->leaders = $this->loadModel('user')->getLeaders();

        //获取架构师
        if($projectplan->changeStage == 2){
            $architectUser = [];
            if($projectplan->changeVersion >= 1){
                $tempChangeVersion = $projectplan->changeVersion - 1;
                for($cversion=$tempChangeVersion;$cversion >= 0 ; $cversion--){
                    $architectUser = $this->loadModel('review')->getReviewersByNodeCode('planchange',$planID,$cversion,'architect','array');
                    if($architectUser){
                        break;
                    }
                }
            }

            if(!$architectUser){
                //变更获取不到架构师时，查询年度计划曾经的历史版本中的架构师
                if($projectplan->yearVersion >= 1){
                    for($version = $projectplan->yearVersion; $version >= 0 ; $version-- ){
                        $architectUser = $this->loadModel('review')->getReviewersByNodeCode('projectplanyear',$planID,$version,'architect','array');
                        if($architectUser){
                            break;
                        }
                    }
                }else{
                    $architectUser = $this->loadModel('review')->getReviewersByNodeCode('projectplanyear',$planID,$projectplan->yearVersion,'architect','array');
                }
            }
            $this->view->architectUser = $architectUser;

        }


        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: ajaxGetPMDept
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:21
     * Desc: This is the code comment. This method is called ajaxGetPMDept.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $account
     */
    public function ajaxGetPMDept($account)
    {
        $user = $this->loadModel('user')->getByID($account);
        die($user->dept);
    }

    public function ajaxGetProductplans($productID, $dataID)
    {
        $plans = $this->loadModel('productplan')->getPairs($productID, 0);
        $plans = empty($plans) ? array('0' => '') : $plans;
        die(html::select('plans[]', $plans, '', "id='planIndex{$dataID}' data-id='{$dataID}' class='form-control'"));
    }

    //已关联版本选择框
    public function ajaxGetProductplansRelation($productID, $dataID)
    {
        $plans = $this->loadModel('productplan')->getPairsForRelation($productID, 0);
        $plans = empty($plans) ? array('0' => '') : $plans;
        die(html::select('plans[]', $plans, '', "id='planIndex{$dataID}' data-id='{$dataID}' class='form-control'"));
    }
    /**
     * Project: chengfangjinke
     * Method: delete
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:21
     * Desc: This is the code comment. This method is called delete.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $planID
     * @param string $confirm
     */
    public function delete($planID, $confirm = 'no')
    {
        if($confirm == 'no')
        {
            echo js::confirm($this->lang->projectplan->confirmDelete, $this->createLink('projectplan', 'delete', "planID=$planID&confirm=yes"), '');
            exit;
        }
        else
        {
            $this->projectplan->delete(TABLE_PROJECTPLAN, $planID);
//            $this->dao->update(TABLE_PROJECTPLAN)->set('status')->eq('deleted')->where('id')->eq($planID)->exec();

            die(js::locate(inlink('browse'), 'parent'));
        }
    }

    /**
     * Project: chengfangjinke
     * Method: exec
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:21
     * Desc: This is the code comment. This method is called exec.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $planID
     */
    public function exec($planID = 0)
    {

        if($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            /*if(empty($this->post->opinion[0])){
                dao::$errors['opinion'] = $this->lang->projectplan->opinionEmpty.'!!';
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            if(empty($this->post->requirement[0])){
                dao::$errors['requirement'] = $this->lang->projectplan->requirementEmpty2;
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            if(empty($this->post->demand[0])){
                dao::$errors['demand'] = $this->lang->projectplan->demandEmpty.'!!';
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }*/


            $this->projectplan->exec($planID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $this->loadModel('action')->create('projectplan', $planID, 'exec');
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $this->view->title    = $this->lang->projectplan->initProject;
        $this->view->actions  = $this->loadModel('action')->getList('projectplan', $planID);
        $this->view->plan     = $this->projectplan->getByID($planID);
        $this->view->users    = $this->loadModel('user')->getPairs('noclosed|noletter');
        $this->view->products = array('0' => '') + $this->loadModel('product')->getPairs();
        $this->view->opinions    = [''] + $this->loadModel('opinion')->getPairs();



        $this->display();
    }

    /**
     * 已关联版本弹窗
     * 项目关联产品编号+计划版本
     * @param int $planID //是项目id
     */
    public function relationExec($projectID = 0)
    {
        if($_SERVER['REQUEST_METHOD'] == 'POST')
        {

         if(empty($_POST) or !implode('', $this->post->products))
            {
                $response = array();
                $response['result']  = 'fail';
                $response['message'] = $this->lang->projectplan->productEmpty;
                $this->send($response);
                return;
            }

            $plans = $_POST['plans'];
            $list = $this->projectplan->getProductInfo($_POST['products']);

            $selects = $this->loadModel('product')->getSelects();
            foreach ($list as &$product){
                $product->os = $selects['osTypeList'][$product->os] ?? "";
                $product->arch = $selects['archTypeList'][$product->arch] ?? "";
            }
            $i = 0;
            $relations = [];
            $tmp = [];
            foreach ($_POST['products'] as $productId){
                if(empty($productId)){
                    continue;
                }
                $planPairs = $this->loadModel('productplan')->getPairsForRelation($productId, 0, '', false, 1);
                $item = $list[$productId];
                $newItem = new stdClass();
                if($plans[$i]){
                    if(isset($tmp[$productId]) && $tmp[$productId] == $plans[$i]){ //如果已有则忽略
                        $i++;
                        continue;
                    }
                    $tmp[$productId] = $plans[$i];
                    $newItem->plan = $plans[$i];
                    $newItem->planTitle = $planPairs[$plans[$i]];
                    $newItem->id = $item->id;
                    $newItem->code = $item->code;
                    $newItem->os = $item->os;
                    $newItem->arch = $item->arch;
                    $relations[] = $newItem;
                    $i++;
                } else {
                $response['result']  = 'fail';
                $response['message'] = '选择内容缺失';
                $this->send($response);
                }
            }
            $this->projectplan->addRelation($projectID, json_encode($relations)); //记录详细产品版本关系内容
            $this->projectplan->recordRelationPlan($projectID, $_POST['products'], $plans); //更新产品版本关系型数据表

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }

        $this->view->title    = $this->lang->projectplan->initProject;
        $this->view->actions  = $this->loadModel('action')->getList('projectplan', $projectID);
        $this->view->plan     = $this->projectplan->getByProjectID($projectID);
        $this->view->users    = $this->loadModel('user')->getPairs('noclosed|noletter');

        $allProducts    = $this->loadModel('project')->getProjectRelationList($projectID) ?? [];
        $this->view->linkedProducts = $this->loadModel('product')->getProducts($projectID) ?? [];
        $this->view->products = array('0' => '') ;
        $this->view->allProducts = []; //可关联产品及计划版本
        foreach ($allProducts as &$item) {

            $plans = $this->loadModel('productplan')->getPairsForRelation($item['id'], 0);
            $item['allPlans'] = empty($plans) ? [""] : $plans;
            $this->view->allProducts[] = $item;
        }
        foreach ($this->view->linkedProducts as $item) {
            $product['id'] = $item->id;
            $product['os'] = $item->os;
            $product['name'] = $item->name;
            $product['code'] = $item->code;
            $choose[$item->id] = $product['name'] ??'';
            $this->view->products += $choose;
        }
        $this->loadModel('product');
        $this->display();
    }
    /**
     * Project: chengfangjinke
     * Method: execEdit
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:21
     * Desc: This is the code comment. This method is called execEdit.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $planID
     */
    public function execEdit($planID = 0)
    {
        $this->app->loadLang('opinion');
        $this->app->loadConfig('execution');
        $plan = $this->projectplan->getByID($planID);
        $projectChanges = array('' => '') + $this->loadModel('change')->getPairs($plan->project);
        if($_POST)
        {
            $this->checkPost();//校验字段
            $changes = $this->projectplan->execEdit($planID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $changeCode = zget($projectChanges, $this->post->projectChange, '');
            if($changeCode && $_POST['comment']) {
                $changeCode = $changeCode.'<br>'. $_POST['comment'];
            }
            if(!$changeCode && $_POST['comment']) {
                $changeCode =  $_POST['comment'];
            }
            $actionID = $this->loadModel('action')->create('projectplan', $planID, 'renewchange', $changeCode);

            if($changes)
            {
                $this->action->logHistory($actionID, $changes);
            }
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = inlink('view', "planID=$planID");

            $this->send($response);
        }
        $products = $this->loadModel('product')->getProductInfo();

        $this->view->title    = $this->lang->projectplan->execEdit;
        $this->view->plan     = $plan;
        $this->view->changes  = $projectChanges;
        $this->view->users    = $this->loadModel('user')->getPairs('noclosed');
        $this->view->lines    = array('' => '') + $this->loadModel('product')->getLinePairs();
        $this->view->products = [''] + $products;
        $this->view->depts    =  array('' => '') + $this->loadModel('dept')->getOptionMenu();
        if(isset($this->view->depts[0])){
            unset($this->view->depts[0]);
        }
        $this->view->apps     = array(0 => '') + $this->loadmodel('application')->getapplicationNameCodePairs();
        $this->view->productsRelated = isset($this->view->plan->productsRelated) ? json_decode(base64_decode($this->view->plan->productsRelated), 1) : [['productId' =>'','realRelease' =>'','realOnline' =>'']];
        $this->view->planStages = isset($this->view->plan->planStages) ? json_decode(base64_decode($this->view->plan->planStages), 1) : [['stageBegin' =>'','stageEnd' =>'']];
//        $this->view->outsideProject = $this->loadModel('outsideplan')->getPairs();
//        $this->view->outsideSubProject  = $this->loadModel('outsideplan')->getSubProjectPairs($this->view->plan->outsideSubProject);
        if($this->view->plan->outsideProject){
            $outsideProjectList = $this->loadModel('outsideplan')->getPairsByID($this->view->plan->outsideProject);
            $this->view->outsideIDs = implode(',',array_keys($outsideProjectList));
            $this->view->outsideNames = implode(',',$outsideProjectList);
        }else{
            $this->view->outsideIDs = '';
            $this->view->outsideNames = '';
        }
        if($this->view->plan->outsideSubProject){
            $outsideSubProjectList  = $this->loadModel('outsideplan')->getSubProjectPairs($this->view->plan->outsideSubProject);
            $this->view->subOutsideIDs = implode(',',array_keys($outsideSubProjectList));
            $this->view->subOutsideNames = implode(',',$outsideSubProjectList);
        }else{
            $this->view->subOutsideIDs = '';
            $this->view->subOutsideNames = '';
        }


//        $this->view->outsideTask        = $this->loadModel('outsideplan')->getTaskPairs($this->view->plan->outsideTask);
        $this->view->outsideTask        = $this->loadModel('outsideplan')->getTaskPairsNameUnion();

        $this->display();
    }
    /**
     * TongYanQi 2022/10/9
     * 编辑外部年度计划状态
     */
    public function editStatus($planID)
    {
        $this->view->plan     = $this->projectplan->getByID($planID);
        if($_POST)
        {
            $this->dao->update(TABLE_PROJECTPLAN)->data(['insideStatus'=>$_POST['insideStatus']])->where('id')->eq($planID)->exec();
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->loadModel('action')->create('projectplan', $planID, 'editStatus');
            $this->send($response);
        }
        $this->display();
    }
    /**
     * 变更年度计划
     * @param int $planID
     */
    public function planChange($planID = 0)
    {
        $this->app->loadLang('opinion');
        $this->app->loadConfig('execution');
        $plan = $this->projectplan->getByID($planID);
        $projectChanges = array('' => '') + $this->loadModel('change')->getPairs($plan->project);
        if($_POST)
        {
            $this->checkPost();//校验字段
            $changes = $this->projectplan->planChange($planID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            if($changes)
            {
                $actionID = $this->loadModel('action')->create('projectplan', $planID, 'planchange');
                $this->action->logHistory($actionID, $changes);
            }
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = inlink('browse');

            $this->send($response);
        }
        $products = $this->loadModel('product')->getProductInfo();

        $this->view->title    = $this->lang->projectplan->execEdit;
        $this->view->plan     = $plan;
        $this->view->changes  = $projectChanges;
        $this->view->users    = $this->loadModel('user')->getPairs('noclosed');
        $this->view->lines    = array('' => '') + $this->loadModel('product')->getLinePairs();
        $this->view->products = [''] + $products;
        $this->view->depts    = $this->loadModel('dept')->getOptionMenu();
        if(isset($this->view->depts[0])){
            unset($this->view->depts[0]);
        }
        $this->view->apps     = array(0 => '') + $this->loadmodel('application')->getapplicationNameCodePairs();
        $this->view->productsRelated = isset($this->view->plan->productsRelated) ? json_decode(base64_decode($this->view->plan->productsRelated), 1) : [['productId' =>'','realRelease' =>'','realOnline' =>'']];
        $this->view->planStages = isset($this->view->plan->planStages) ? json_decode(base64_decode($this->view->plan->planStages), 1) : [['stageBegin' =>'','stageEnd' =>'']];
//        $this->view->outsideProject = $this->loadModel('outsideplan')->getPairs();
        if($this->view->plan->outsideProject){
            $outsideProjectList = $this->loadModel('outsideplan')->getPairsByID($this->view->plan->outsideProject);
            $this->view->outsideIDs = implode(',',array_keys($outsideProjectList));
            $this->view->outsideNames = implode(',',$outsideProjectList);
        }else{
            $this->view->outsideIDs = '';
            $this->view->outsideNames = '';
        }
        if($this->view->plan->outsideSubProject){
            $outsideSubProjectList  = $this->loadModel('outsideplan')->getSubProjectPairs($this->view->plan->outsideSubProject);
            $this->view->subOutsideIDs = implode(',',array_keys($outsideSubProjectList));
            $this->view->subOutsideNames = implode(',',$outsideSubProjectList);
        }else{
            $this->view->subOutsideIDs = '';
            $this->view->subOutsideNames = '';
        }
        $this->view->outsideTask        = $this->loadModel('outsideplan')->getTaskPairsNameUnion();
//        $this->view->outsideProject = $this->loadModel('outsideplan')->getPairsHavingTasks();
//        $this->view->outsideTask        = $this->loadModel('outsideplan')->getTaskPairs($this->view->plan->outsideTask);
//        $this->view->outsideSubProject  = $this->loadModel('outsideplan')->getSubProjectPairs($this->view->plan->outsideSubProject);
        $this->display();
    }



    /**
     * Project: chengfangjinke
     * Method: export
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:21
     * Desc: This is the code comment. This method is called export.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param string $orderBy
     * @param string $browseType
     */
    public function export($orderBy = 'id_desc', $browseType = 'all')
    {
        /* format the fields of every projectplan in order to export data. */
        $this->app->loadLang('opinion');
        if($_POST)
        {
            $allProducts = $this->loadModel('product')->getSimplePairs(); //2022-04-14 tongyanqi 所有产品名
            $this->projectplan->setListValue();

            $this->loadModel('file');
            $projectplanLang   = $this->lang->projectplan;
            $projectplanConfig = $this->config->projectplan;

            /* Create field lists. */
            $fields = $this->post->exportFields ? $this->post->exportFields : explode(',', $projectplanConfig->list->exportFields);
            foreach($fields as $key => $fieldName)
            {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = isset($projectplanLang->$fieldName) ? $projectplanLang->$fieldName : $fieldName;
                unset($fields[$key]);
            }

            /* Get projectplans. */
            $projectplans = array();
            if($this->session->projectplanOnlyCondition)
            {
                $projectplans = $this->dao->select('*')->from(TABLE_PROJECTPLAN)->where($this->session->projectplanQueryCondition)
                    ->beginIF($this->post->exportType == 'selected')->andWhere('id')->in($this->cookie->checkedItem)->fi()
                    ->orderBy($orderBy)->fetchAll('id');
            }
            else
            {
                $stmt = $this->dbh->query($this->session->projectplanQueryCondition . ($this->post->exportType == 'selected' ? " AND $field IN({$this->cookie->checkedItem})" : '') . " ORDER BY " . strtr($orderBy, '_', ' '));
                while($row = $stmt->fetch()) $projectplans[$row->id] = $row;
            }
            $projectplanIdList = array_keys($projectplans);

            /* Get users, products and executions. */
            $users = $this->loadModel('user')->getPairs('noletter');
            $depts = $this->loadModel('dept')->getOptionMenu();
            $lines = $this->loadModel('product')->getLinePairs(0);
            $appList  = $this->loadModel('application')->getPairs(0);
            $outsideproject     = $this->loadModel('outsideplan')->getPairs();
            $outsideTask        = $this->loadModel('outsideplan')->getTaskPairs();
            $outsideSubProject  = $this->loadModel('outsideplan')->getSubProjectPairs();
            $ops = $this->dao->select('id,name,linkedPlan')->from(TABLE_OUTSIDEPLAN)->where('deleted')->eq(0)->fetchAll();
            $opMap = array();
            foreach($ops as $op)
            {
                $linked = explode(',', $op->linkedPlan);
                foreach($linked as $l)
                {
                    if(!$l) continue;
                    if(!isset($opMap[$l])) $opMap[$l] = array();
                    $opMap[$l][] = $op->name;
                }
            }

            //获取所有年度计划
            $projectplanList = $this->projectplan->getAllList("id,mark");
            $projectplanList = array_column($projectplanList,"mark",'id');

            foreach($projectplans as $projectplan)
            {
                if(isset($projectplanLang->typeList[$projectplan->type]))               $projectplan->type        = $projectplanLang->typeList[$projectplan->type];
                if(isset($users[$projectplan->createdBy])) $projectplan->createdBy                                = $users[$projectplan->createdBy];
                if(isset($this->lang->opinion->categoryList[$projectplan->category]))   $projectplan->category    = $this->lang->opinion->categoryList[$projectplan->category];
                $projectplan->insideStatus    = zget($projectplanLang->insideStatusList, $projectplan->insideStatus);
                //2022-04-14 tongyanqi
                $basisArr = explode(',', str_replace(' ', '', $projectplan->basis));
                $allBasis = '';
                foreach($basisArr as $a)
                {
                    $allBasis .=  zget($projectplanLang->basisList, $a, '') . PHP_EOL;
                }
                $projectplan->basis       = $allBasis;

                //2023-08-16
                $mainRelationInfo = $this->loadModel("projectplanmsrelation")->getByMainPlanID($projectplan->id);
                $slaveRelationInfo = $this->loadModel("projectplanmsrelation")->getBySlavePlanID($projectplan->id);

                if($mainRelationInfo){
                    $tempmainplanIDArr = explode(",",$mainRelationInfo->slavePlanID);
                    foreach ($tempmainplanIDArr as $tempmainplanID){
                        $projectplan->planIsMainProject .= $projectplanList[$tempmainplanID].',';
                    }
                    $projectplan->planIsMainProject = trim($projectplan->planIsMainProject,',');

                }else{
                    $projectplan->planIsMainProject = "无";
                }

                if($slaveRelationInfo){

                    foreach ($slaveRelationInfo as $slaveInfo){
                        $projectplan->planIsSlaveProject .= $projectplanList[$slaveInfo->mainPlanID].',';
                    }
                    $projectplan->planIsSlaveProject = trim($projectplan->planIsSlaveProject,',');

                }else{
                    $projectplan->planIsSlaveProject = "无";
                }


                $projectplan->isImportant = $projectplanLang->isImportantList[$projectplan->isImportant] ?? $projectplan->isImportant;
                $projectplan->secondLine = $projectplanLang->secondLineList[$projectplan->secondLine] ?? $projectplan->secondLine;
                $projectplan->architrcturalTransform = $projectplanLang->architrcturalTransformList[$projectplan->architrcturalTransform] ?? $projectplan->architrcturalTransform;
                $projectplan->systemAssemble = $projectplanLang->systemAssembleList[$projectplan->systemAssemble] ?? $projectplan->systemAssemble;
                $projectplan->cloudComputing = $projectplanLang->cloudComputingList[$projectplan->cloudComputing] ?? $projectplan->cloudComputing;
                $projectplan->passwordChange = $projectplanLang->passwordChangeList[$projectplan->passwordChange] ?? $projectplan->passwordChange;
                if(isset($projectplanLang->storyStatusList[$projectplan->storyStatus])) $projectplan->storyStatus = $projectplanLang->storyStatusList[$projectplan->storyStatus];
                if(isset($projectplanLang->localizeList[$projectplan->localize]))       $projectplan->localize    = $projectplanLang->localizeList[$projectplan->localize];
                if(isset($projectplanLang->statusList[$projectplan->status])){
                    $projectplanstatusstr = '';
                    if($projectplan->status==$this->lang->projectplan->statusEnglishList['yearpass'] && $projectplan->changeStatus == $this->lang->projectplan->ChangestatusEnglishList['pending']){
                        $projectplanstatusstr = $this->lang->projectplan->changeing;
                    }else{
                        $projectplanstatusstr = zget($this->lang->projectplan->statusList, $projectplan->status, '');
                        if($projectplan->changeStatus == $this->lang->projectplan->ChangestatusEnglishList['pass']){
                            $projectplanstatusstr .= $this->lang->projectplan->changePass;
                        }else if($projectplan->changeStatus == $this->lang->projectplan->ChangestatusEnglishList['reject']){
                            $projectplanstatusstr .= $this->lang->projectplan->changeReject;
                        }
                    }
//                    $projectplan->status      = $projectplanLang->statusList[$projectplan->status];
                    $projectplan->status      = $projectplanstatusstr;
                }
                if(isset($projectplanLang->dataEnterLakeList[$projectplan->dataEnterLake]))         $projectplan->dataEnterLake      = $projectplanLang->dataEnterLakeList[$projectplan->dataEnterLake];
                if(isset($projectplanLang->basicUpgradeList[$projectplan->basicUpgrade]))           $projectplan->basicUpgrade       = $projectplanLang->basicUpgradeList[$projectplan->basicUpgrade];

                $owners = isset($projectplan->owner) ? explode(',', $projectplan->owner) : [];
                $allOwners = '';
                foreach ($owners as $owner) { $allOwners .= zget($users, $owner, ''). PHP_EOL;};
                $projectplan->owner     = $allOwners;

                $planDepts = explode(',', str_replace(' ', '', $projectplan->bearDept));
                $allDepts = '';
                foreach($planDepts as $deptID)
                {
                    if($deptID) $allDepts .= zget($depts, $deptID, '') .PHP_EOL;
                }
                $projectplan->bearDept  = $allDepts;

                /*$productsRelated = '';
                if($projectplan->productsRelated){
                    foreach (json_decode(base64_decode($projectplan->productsRelated), 1) as $item)
                    {
                        if(empty($item['productId'])) continue;
                        $productName = $allProducts[$item['productId']];
                        $productsRelated .=  $productName . '('. $item['realRelease'] .'~'. $item['realOnline'] .')'. PHP_EOL;
                    }
                }
                $projectplan->productsRelated = $productsRelated;*/

                $planStages = '';
                if($projectplan->planStages){
                    $stageNum = 1;
                    foreach (json_decode(base64_decode($projectplan->planStages), 1) as $item)
                    {
                        $planStages .=  '第'. $stageNum .'阶段:'. $item['stageBegin'] .'~'. $item['stageEnd'] . PHP_EOL;
                        $stageNum++;
                    }
                }
                $projectplan->planStages = $planStages;
                $projectplan->planRemark = strip_tags(br2nl($projectplan->planRemark)); //处理富文本换行
                $projectplan->content = strip_tags(str_replace(['&nbsp;'],' ',br2nl($projectplan->content))); //处理富文本换行

                $projectplan->platformowner = zmget($this->lang->projectplan->platformownerList,$projectplan->platformowner);
                $apps = explode(',', str_replace(' ', '', $projectplan->app));
                $allApps = '';
                foreach($apps as $appId)
                {
                    if($appId) $allApps .= $appList[$appId] . PHP_EOL;
                }
                $projectplan->app       = $allApps;
                //end  2022-04-27 tongyanqi
//                $creation = $this->dao->select('id,dept')->from(TABLE_PROJECTCREATION)->where('plan')->eq($projectplan->id)->fetch();

                    $allDepts = explode(',', $projectplan->depts);
                    $projectplan->depts = '';
                    foreach($allDepts as $dept)
                    {
                        if(isset($depts[$dept])) $projectplan->depts .= $depts[$dept].',';
                    }

                $outsideProjects = explode(',', $projectplan->outsideProject);
                $projectplan->outsideProject = '';
                foreach ($outsideProjects as $outsideProjectId) {
                    $outsideId = trim($outsideProjectId);
                    if(isset($outsideproject[$outsideId])) $projectplan->outsideProject .=$outsideproject[$outsideId].',';
                }
                $projectplan->outsides =$projectplan->outsideProject;

                $outsideSubProjects = explode(',', $projectplan->outsideSubProject);
                $projectplan->outsideSubProject = '';
                foreach ($outsideSubProjects as $outsideSubProjectId) {
                    if(isset($outsideSubProject[$outsideSubProjectId])) $projectplan->outsideSubProject .=$outsideSubProject[$outsideSubProjectId].',';
                }


                $outsideTasks = explode(',', $projectplan->outsideTask);
                $projectplan->outsideTask = '';
                foreach ($outsideTasks as $outsideTaskId) {
                    if(isset($outsideTask[$outsideTaskId])) $projectplan->outsideTask .=$outsideTask[$outsideTaskId].',';
                }

                $projectplan->line = trim(trim($projectplan->line), ',');
                $linePairs         = explode(',', $projectplan->line);
                $projectplan->line = '';
                foreach($linePairs as $line)
                {
                    $line = trim($line);
                    if(isset($lines[$line])) $projectplan->line .= $lines[$line] . ','; 
                }
                $projectplan->line = rtrim($projectplan->line, ',');

                $projectplan->reviewDate = substr($projectplan->reviewDate, 0, 10);
                $projectplan->createDate = substr($projectplan->createDate, 0, 10);
                $projectplan->begin      = substr($projectplan->begin, 0, 10);
                $projectplan->end        = substr($projectplan->end, 0, 10);
            }

            $this->post->set('fields', $fields);
            $this->post->set('rows', $projectplans);
            $this->post->set('kind', 'projectplan');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        $this->view->fileName        = $this->lang->projectplan->common;
        $this->view->allExportFields = $this->config->projectplan->list->exportFields;
        $this->view->customExport    = false;
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: exportTemplate
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:21
     * Desc: This is the code comment. This method is called exportTemplate.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     */
    public function exportTemplate()
    {
        if($_POST)
        {
            $this->projectplan->setListValue();

            foreach($this->config->projectplan->export->templateFields as $field) $fields[$field] = $this->lang->projectplan->$field;

            $this->post->set('fields', $fields);
            $this->post->set('kind', 'projectplan');
            $this->post->set('rows', array());
            $this->post->set('extraNum',   $this->post->num);
            $this->post->set('fileName', 'projectplanTemplate');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }
        $this->display();
    }

    /**
     * TongYanQi 2022/11/25
     * 导出历史记录
     */
    public function exportHistorybak($orderBy = 'id_desc', $browseType = 'all')
    {
        /* format the fields of every projectplan in order to export data. */
        $this->app->loadLang('opinion');
        if($_POST)
        {
            $allProducts = $this->loadModel('product')->getSimplePairs(); //2022-04-14 tongyanqi 所有产品名
            $this->projectplan->setListValue();
            $planList = [];
            $this->loadModel('file');
            $projectplanLang   = $this->lang->projectplan;
            $projectplanConfig = $this->config->projectplan;

            /* Create field lists. */
            $fields = explode(',', $projectplanConfig->list->exportHistoryFields);
            foreach($fields as $key => $fieldName)
            {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = isset($projectplanLang->$fieldName) ? $projectplanLang->$fieldName : $fieldName;
                unset($fields[$key]);
            }
            $fields['historyTime']= $projectplanLang->changeHistoryTime;
            $fields['history']= $projectplanLang->changeHistory;

            /* Get projectplans. */
            $projectplans = array();
            if($this->session->projectplanOnlyCondition)
            {
                $projectplans = $this->dao->select('*')->from(TABLE_PROJECTPLAN)->where($this->session->projectplanQueryCondition)
                    ->beginIF($this->post->exportType == 'selected')->andWhere('id')->in($this->cookie->checkedItem)->fi()
                    ->orderBy($orderBy)->fetchAll('id');
            }
            else
            {
                $stmt = $this->dbh->query($this->session->projectplanQueryCondition . ($this->post->exportType == 'selected' ? " AND $field IN({$this->cookie->checkedItem})" : '') . " ORDER BY " . strtr($orderBy, '_', ' '));
                while($row = $stmt->fetch()) $projectplans[$row->id] = $row;
            }
            $projectplanIdList = array_keys($projectplans);

            /* Get users, products and executions. */
            $users = $this->loadModel('user')->getPairs('noletter');
            $depts = $this->loadModel('dept')->getOptionMenu();
            $lines = $this->loadModel('product')->getLinePairs(0);
            $appList  = $this->loadModel('application')->getPairs(0);
            $outsideproject     = $this->loadModel('outsideplan')->getPairs();
            $outsideTask        = $this->loadModel('outsideplan')->getTaskPairs();
            $outsideSubProject  = $this->loadModel('outsideplan')->getSubProjectPairs();
            $ops = $this->dao->select('id,name,linkedPlan')->from(TABLE_OUTSIDEPLAN)->where('deleted')->eq(0)->fetchAll();
            $opMap = array();
            foreach($ops as $op)
            {
                $linked = explode(',', $op->linkedPlan);
                foreach($linked as $l)
                {
                    if(!$l) continue;
                    if(!isset($opMap[$l])) $opMap[$l] = array();
                    $opMap[$l][] = $op->name;
                }
            }

            foreach($projectplans as $projectplan)
            {
                if(isset($projectplanLang->typeList[$projectplan->type]))               $projectplan->type        = $projectplanLang->typeList[$projectplan->type];
                if(isset($users[$projectplan->createdBy])) $projectplan->createdBy                                = $users[$projectplan->createdBy];
                if(isset($this->lang->opinion->categoryList[$projectplan->category]))   $projectplan->category    = $this->lang->opinion->categoryList[$projectplan->category];
                $projectplan->insideStatus    = zget($projectplanLang->insideStatusList, $projectplan->insideStatus);
                //2022-04-14 tongyanqi
                $basisArr = explode(',', str_replace(' ', '', $projectplan->basis));
                $allBasis = '';
                foreach($basisArr as $a)
                {
                    $allBasis .=  zget($projectplanLang->basisList, $a, '') . PHP_EOL;
                }
                $projectplan->basis       = $allBasis;

                $projectplan->isImportant = $projectplanLang->isImportantList[$projectplan->isImportant] ?? $projectplan->isImportant;
                $projectplan->secondLine = $projectplanLang->secondLineList[$projectplan->secondLine] ?? $projectplan->secondLine;
                $projectplan->architrcturalTransform = $projectplanLang->architrcturalTransformList[$projectplan->architrcturalTransform] ?? $projectplan->architrcturalTransform;
                $projectplan->systemAssemble = $projectplanLang->systemAssembleList[$projectplan->systemAssemble] ?? $projectplan->systemAssemble;
                $projectplan->cloudComputing = $projectplanLang->cloudComputingList[$projectplan->cloudComputing] ?? $projectplan->cloudComputing;
                $projectplan->passwordChange = $projectplanLang->passwordChangeList[$projectplan->passwordChange] ?? $projectplan->passwordChange;
                if(isset($projectplanLang->storyStatusList[$projectplan->storyStatus])) $projectplan->storyStatus = $projectplanLang->storyStatusList[$projectplan->storyStatus];
                if(isset($projectplanLang->localizeList[$projectplan->localize]))       $projectplan->localize    = $projectplanLang->localizeList[$projectplan->localize];
                if(isset($projectplanLang->statusList[$projectplan->status]))           $projectplan->status      = $projectplanLang->statusList[$projectplan->status];
                if(isset($projectplanLang->dataEnterLakeList[$projectplan->dataEnterLake]))         $projectplan->dataEnterLake      = $projectplanLang->dataEnterLakeList[$projectplan->dataEnterLake];
                if(isset($projectplanLang->basicUpgradeList[$projectplan->basicUpgrade]))           $projectplan->basicUpgrade       = $projectplanLang->basicUpgradeList[$projectplan->basicUpgrade];

                $owners = isset($projectplan->owner) ? explode(',', $projectplan->owner) : [];
                $allOwners = '';
                foreach ($owners as $owner) { $allOwners .= zget($users, $owner, ''). PHP_EOL;};
                $projectplan->owner     = $allOwners;

                $planDepts = explode(',', str_replace(' ', '', $projectplan->bearDept));
                $allDepts = '';
                foreach($planDepts as $deptID)
                {
                    if($deptID) $allDepts .= zget($depts, $deptID, '') .PHP_EOL;
                }
                $projectplan->bearDept  = $allDepts;

                $projectplan->planRemark = strip_tags(br2nl($projectplan->planRemark)); //处理富文本换行
                $projectplan->content = strip_tags(br2nl($projectplan->content)); //处理富文本换行

                $allDepts = explode(',', $projectplan->depts);
                $projectplan->depts = '';
                foreach($allDepts as $dept)
                {
                    if(isset($depts[$dept])) $projectplan->depts .= $depts[$dept].',';
                }

                $linePairs         = explode(',', $projectplan->line);
                $projectplan->line = '';
                foreach($linePairs as $line)
                {
                    $line = trim($line);
                    if(isset($lines[$line])) $projectplan->line .= $lines[$line] . ',';
                }
                $projectplan->line = rtrim($projectplan->line, ',');

                $projectplan->reviewDate = substr($projectplan->reviewDate, 0, 10);
                $projectplan->createDate = substr($projectplan->createDate, 0, 10);
                $projectplan->begin      = substr($projectplan->begin, 0, 10);
                $projectplan->end        = substr($projectplan->end, 0, 10);
                $thisActions  = $this->decodeActions($this->loadModel('action')->getList('projectplan', $projectplan->id));
                $historyList   =   $this->loadModel('common')->getActionLines($thisActions, $users);
                foreach ($historyList as $history){
                    $planItem = new stdClass();
                    foreach ($projectplan as $ki => $vi)
                    {
                        $planItem->$ki = $vi;
                    }

                    $planItem->historyTime = $history['time'];
                    $planItem->history = $history['line'];
                    $planList[] = $planItem;
                }
            }

            $this->post->set('fields', $fields);
            $this->post->set('rows', $planList);
            $this->post->set('kind', 'projectplan');
            $this->loadModel('file')->setExcelWidth(30);
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        $this->view->fileName        = $this->lang->projectplan->changeHistory;
        $this->view->allExportFields = $this->config->projectplan->list->exportFields;
        $this->view->customExport    = false;
        $this->display();
    }

    public function exportHistory($orderBy = 'id_desc', $browseType = 'all')
    {
        /* format the fields of every projectplan in order to export data. */
        $this->app->loadLang('opinion');
        if($_POST)
        {

            $projectplanLang   = $this->lang->projectplan;
            $projectplanConfig = $this->config->projectplan;
            $fields = $this->post->exportFields ? $this->post->exportFields : explode(',', $projectplanConfig->list->exportHistoryFields);
            foreach($fields as $key => $fieldName)
            {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = isset($projectplanLang->$fieldName) ? $projectplanLang->$fieldName : $fieldName;
                unset($fields[$key]);
            }
            $fields['commitinfo'] =  $projectplanLang->auditResults;
            $fields['historyTime'] =  $projectplanLang->changeHistoryTime;
            $fields['actionType'] =  $projectplanLang->actionType;
            $fields['actor'] =  $projectplanLang->actor;
            $fields['actionTimeType'] =  $projectplanLang->actionTimeType;
            $fields['historyinfo'] =  $projectplanLang->projectChange;




            if($this->post->startTime >= $this->post->endTime){

                $response['result']  = 'fail';
                $response['message'] = $projectplanLang->timeError;
                $this->send($response);
                return;
            }

            $whereStr = " AND date BETWEEN '{$this->post->startTime}' AND '{$this->post->endTime}'";

            $actionList = $this->loadModel('action')->getBySQL(" objectType='projectplan' {$whereStr}","id desc");

            $users = $this->loadModel('user')->getPairs('noletter');
            $depts = $this->loadModel('dept')->getOptionMenu();
            $lines = $this->loadModel('product')->getLinePairs(0);
            $appList  = $this->loadModel('application')->getPairs(0);
            $outsideproject     = $this->loadModel('outsideplan')->getPairs();
            $outsideTask        = $this->loadModel('outsideplan')->getTaskPairs();
            $outsideSubProject  = $this->loadModel('outsideplan')->getSubProjectPairs();
            $exportList = [];
            $planIDList = [];
            foreach ($actionList as $action){
               /* if(isset($exportList[$action->objectID])){
                    $exportList[$action->objectID]['actionList'][] = $action;
                }else{
                    $planInfo = $this->dao->select('*')->from(TABLE_PROJECTPLAN)
                        ->where('id')->eq($action->objectID)
                        ->fetch();
//                    $exportList[$action->objectID] = $planInfo;

                }*/

                $action->historyTime = $action->date;
                $action->commitinfo = '';
                $action->actionType = isset($projectplanLang->actionActionType[$action->action]) ? $projectplanLang->actionActionType[$action->action] :'未知';
                $action->actionTimeType = isset($projectplanLang->actionActionTimeType[$action->action]) ? $projectplanLang->actionActionTimeType[$action->action] :'未知';
                $action->actor = zget($users, $action->actor);
                if($action->action == 'assigned') $action->extra = zget($users, $action->extra);
                if(strpos($action->actor, ':') !== false) $action->actor = substr($action->actor, strpos($action->actor, ':') + 1);

                $action->commitinfo .= $this->action->returnAction($action);

                if(strlen(trim(($action->comment))) != 0) {

                    if (isset($defaultComment)) {
                        $action->commitinfo .= strip_tags($action->comment) == $action->comment ? nl2br($action->comment) : $action->comment;
                    } else {
                        $action->commitinfo .= strip_tags($action->comment) == $action->comment ? nl2br($action->comment) : $action->comment;
                    }

                }


                $action->commitinfo = strip_tags(br2nl($action->commitinfo));
                $exportList[$action->id] = $action;
                $planIDList[] = $action->objectID;
                $history = $this->dao->select("*")->from(TABLE_HISTORY)->where('action')->eq($action->id)->andWhere("field")->eq("planRemark")->fetch();
                $action->historyinfo = '';

                if($history){
//                    $action->historyinfo = strip_tags(br2nl($this->action->getPrintChangesCh($action->objectType,$history)));
                    $action->historyinfo = strip_tags(br2nl($history->new));
                }

            }
            $planIDList = array_unique($planIDList);

            $projectplans = $this->dao->select('*')->from(TABLE_PROJECTPLAN)
                ->where('id')->in($planIDList)
                ->fetchAll('id');
            foreach ($actionList as $key=>$action){

                if(isset($projectplans[$action->objectID])){

                    if(isset($projectplanLang->statusList[$projectplans[$action->objectID]->status])){
                        $projectplans[$action->objectID]->status      = $projectplanLang->statusList[$projectplans[$action->objectID]->status];
                    }
                    $projectplans[$action->objectID]->insideStatus    = zget($projectplanLang->insideStatusList, $projectplans[$action->objectID]->insideStatus);
                    $actionList[$key] = (object)array_merge((array)$action,(array)$projectplans[$action->objectID]);
                }

            }

//            a($actionList);exit();
            $this->post->set('fields', $fields);
            $this->post->set('rows', $actionList);
            $this->post->set('kind', 'projectplanhistory');
            $this->loadModel('file')->setExcelWidth(30);
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
            exit();

        }

        $this->view->startTime = date("Y-m-d H:i",strtotime("-1 day"));
        $this->view->endTime = date("Y-m-d H:i",time());
        $this->view->fileName        = $this->lang->projectplan->changeHistory;
        $this->view->allExportFields = $this->config->projectplan->list->exportFields;
        $this->view->customExport    = false;
        $this->display();
    }
    /**
     * Project: chengfangjinke
     * Method: import
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:21
     * Desc: This is the code comment. This method is called import.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     */
    public function import()
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
     * Time: 17:21
     * Desc: This is the code comment. This method is called showImport.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $pagerID
     * @param int $maxImport
     * @param string $insert
     */
    public function showImport($pagerID = 1, $maxImport = 0, $insert = '')
    {
        $this->app->loadLang('opinion');
        $this->lang->projectplan->categoryList = $this->lang->opinion->categoryList;
        $file    = $this->session->fileImport;
        $tmpPath = $this->loadModel('file')->getPathOfImportedFile();
        $tmpFile = $tmpPath . DS . md5(basename($file));

        if($_POST)
        {
            $this->projectplan->createFromImport();
            if($this->post->isEndPage)
            {
                unlink($tmpFile);
                $vars = [
                    'browseType' => 'all',
                ];
                die(js::locate($this->createLink('projectplan','browse', $vars), 'parent'));
            }
            else
            {
                die(js::locate(inlink('showImport', "pagerID=" . ($this->post->pagerID + 1) . "&maxImport=$maxImport&insert=" . zget($_POST, 'insert', '')), 'parent'));
            }
        }

        if(!empty($maxImport) and file_exists($tmpFile))
        {
            $projectplanData = unserialize(file_get_contents($tmpFile));
        }
        else
        {
            $pagerID           = 1;
            $projectplanLang   = $this->lang->projectplan;
            $projectplanConfig = $this->config->projectplan;
            $fields            = explode(',', $projectplanConfig->list->exportFields);

            foreach($fields as $key => $fieldName)
            {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = isset($projectplanLang->$fieldName) ? $projectplanLang->$fieldName : $fieldName;
                unset($fields[$key]);
            }

            $rows = $this->file->getRowsFromExcel($file);
            $projectplanData = array();
            foreach($rows as $currentRow => $row)
            {
                $projectplan = new stdclass();
                foreach($row as $currentColumn => $cellValue)
                {
                    if($currentRow == 1)
                    {
                        $field = array_search($cellValue, $fields);
                        $columnKey[$currentColumn] = $field ? $field : '';
                        continue;
                    }

                    if(empty($columnKey[$currentColumn]))
                    {
                        $currentColumn++;
                        continue;
                    }
                    $field = $columnKey[$currentColumn];
                    $currentColumn++;

                    // check empty data.
                    if(empty($cellValue))
                    {
                        $projectplan->$field = '';
                        continue;
                    }

                    //if(in_array($field, $projectplanConfig->import->ignoreFields)) continue;
                    if(in_array($field, $projectplanConfig->export->listFields))
                    {
                        if(strrpos($cellValue, '(#') === false)
                        {
                            $projectplan->$field = $cellValue;
                            if(!isset($projectplanLang->{$field . 'List'}) or !is_array($projectplanLang->{$field . 'List'})) continue;

                            /* when the cell value is key of list then eq the key. */
                            $listKey = array_keys($projectplanLang->{$field . 'List'});
                            unset($listKey[0]);
                            unset($listKey['']);
                            $fieldKey = array_search($cellValue, $projectplanLang->{$field . 'List'});
                            if($fieldKey) $projectplan->$field = $fieldKey;
                        }
                        else
                        {
                            $id = trim(substr($cellValue, strrpos($cellValue,'(#') + 2), ')');
                            $projectplan->$field = $id;
                        }
                    }
                    else
                    {
                        $projectplan->$field = $cellValue;
                    }
                }

                if(empty($projectplan->name)) continue;
                $projectplanData[$currentRow] = $projectplan;
                unset($projectplan);
            }
            file_put_contents($tmpFile, serialize($projectplanData));
        }

        if(empty($projectplanData))
        {
            unlink($this->session->fileImport);
            unset($_SESSION['fileImport']);
            echo js::alert($this->lang->excel->noData);
            die(js::locate($this->createLink('projectplan','browse')));
        }

        $allCount = count($projectplanData);
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
            $projectplanData = array_slice($projectplanData, ($pagerID - 1) * $maxImport, $maxImport, true);
        }
        if(empty($projectplanData)) die(js::locate($this->createLink('projectplan','browse')));

        /* Judge whether the editedStories is too large and set session. */
        $countInputVars  = count($projectplanData) * 11;
        $showSuhosinInfo = common::judgeSuhosinSetting($countInputVars);
        if($showSuhosinInfo) $this->view->suhosinInfo = extension_loaded('suhosin') ? sprintf($this->lang->suhosinInfo, $countInputVars) : sprintf($this->lang->maxVarsInfo, $countInputVars);

        $this->view->title      = $this->lang->projectplan->common . $this->lang->colon . $this->lang->projectplan->showImport;
        $this->view->position[] = $this->lang->projectplan->showImport;

        $this->view->projectplanData = $projectplanData;
        $this->view->allCount        = $allCount;
        $this->view->allPager        = $allPager;
        $this->view->pagerID         = $pagerID;
        $this->view->isEndPage       = $pagerID >= $allPager;
        $this->view->maxImport       = $maxImport;
        $this->view->dataInsert      = $insert;
        $this->view->lines           = array('' => '') + $this->loadModel('product')->getLinePairs();
        $this->view->depts           = $this->loadModel('dept')->getOptionMenu();
        $this->view->apps            = array(0 => '') + $this->loadmodel('application')->getapplicationNameCodePairs();
        $this->view->outsideProjects = array(0 => '') + $this->loadModel('outsideplan')->getPairs();
        $this->view->outsideSubProject = array(0 => '') + $this->loadModel('outsideplan')->getSubProjectPairs();
        $this->view->outsideTask     = array(0 => '') + $this->loadModel('outsideplan')->getTaskPairs();
        $this->view->users           = $this->loadModel('user')->getPairs('noclosed');
        $this->display();
    }

    /**
     * 个别字段批量校验无效 需要单独校验
     */
    private function checkPost()
    {
        if(isset($_POST['outsideProject'][0]) && empty($_POST['outsideSubProject'][0]))
        {
            dao::$errors['outsideSubProject'] = ['选择了“(外部)项目/任务名称”则本字段必填'];
            $response['result']  = 'fail';
            $response['message'] = dao::$errors;
            $this->send($response);
        }
        if(isset($_POST['outsideSubProject'][0]) && empty($_POST['outsideTask'][0]))
        {
            dao::$errors['outsideTask'] = ['选择了“(外部)项目/任务名称”则本字段必填'];
            $response['result']  = 'fail';
            $response['message'] = dao::$errors;
            $this->send($response);
        }
        if($_POST['begin'] > $_POST['end'])
        {
            dao::$errors['end'] = [$this->config->projectplan->endError];
            $response['result']  = 'fail';
            $response['message'] = dao::$errors;
            $this->send($response);
        }
        if(!$this->post->owner)
        {
            dao::$errors['owner'] = [$this->config->projectplan->ownerEmpty];
            $response['result']  = 'fail';
            $response['message'] = dao::$errors;
            $this->send($response);
        }
        if($_POST['begin'] > $_POST['end'])
        {
            dao::$errors['end'] = [$this->config->projectplan->endError];
            $response['result']  = 'fail';
            $response['message'] = dao::$errors;
            $this->send($response);
        }

        for ($i = 0; $i<count($_POST['stageBegin']); $i++)
        {
            if (empty($_POST['stageBegin'][$i]) || empty($_POST['stageEnd'][$i])) { continue; } //空在model校验了  这里是后写的
            if ($_POST['stageBegin'][$i] > $_POST['stageEnd'][$i])   {
                dao::$errors['stageEndError2'] = [sprintf($this->config->projectplan->stageEndError2, $i + 1)];
                $response['result']  = 'fail';
                $response['message'] = dao::$errors;
                $this->send($response);
            }
        }
    }

    public function checkTaskDate()
    {
        parse_str($this->server->query_String, $queryString); //获取get参数
        $end = $queryString['end'];
        $ids = $queryString['ids'];
        $ids = trim(',',$ids);

        $notice = '';
        if($ids != ',' && $ids != '')
        {
            $tasks = $this->dao->select('*')->from(TABLE_OUTSIDEPLANTASKS)->where('id')->in($ids)->fetchall();
            foreach ($tasks as $task){
                if($end > $task->subTaskEnd) $notice .= "{$task->subTaskName}:本(外部)子项/子任务计划完成时间为{$task->subTaskEnd}，本内部项目计划完成时间为{$end}".PHP_EOL;
            }

        }
        if($notice){
            $notice.='若确定直接点击“确定”即可，并同步联系对应的外部年度计划维护人员调整(外部)子项/子任务计划完成时间（若(外部)子项/子任务计划完成时间无法调整，则需再次编辑/修改本内部项目计划的计划完成时间）';
        }
        die($notice);
    }
    /**
     * 2022-4-21 tongyanqi
     * 处理操作记录
     */
    private function decodeActions($actions)
    {
        foreach ($actions as $action)
        {
            if(isset($action->history[0]->field) && $action->history[0]->field== 'productsRelated') {
                $productRelateds = json_decode(base64_decode($action->history[0]->old), 1);
                $action->history[0]->old = '';
                foreach ($productRelateds as $productRelated){
                    $action->history[0]->old .= "产品id：{$productRelated['productId']}(计划开始：{$productRelated['realRelease']}~计划结束：{$productRelated['realOnline']}) ";
                }
                $productRelateds = json_decode(base64_decode($action->history[0]->new), 1);
                $action->history[0]->new ='';
                foreach ($productRelateds as $productRelated){
                    $action->history[0]->new .= "产品id：{$productRelated['productId']}(计划开始：{$productRelated['realRelease']}/计划结束：{$productRelated['realOnline']}) ";
                }
            }
        }
        return $actions;
    }
}

