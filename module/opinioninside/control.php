<?php
class opinioninside extends control
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
        $this->app->loadLang('opinion');
        $this->loadModel('requirementinside');
        $browseType = strtolower($browseType);
        /* By search. 构建页面搜索表单。*/
        $queryID = ($browseType == 'bysearch') ? (int)$param : 0;
        $actionURL = $this->createLink('opinioninside', 'browse', "browseType=bySearch&param=myQueryID");
        $this->config->opinioninside->search['params']['synUnion']['values']  = $this->lang->opinion->synUnionList;
        $this->config->opinioninside->search['params']['union']['values']  = $this->lang->opinion->unionList;
        $this->config->opinioninside->search['params']['sourceMode']['values']  = $this->lang->opinion->sourceModeList;
        $this->config->opinioninside->search['params']['category']['values']  = $this->lang->opinion->categoryList;
        $this->opinioninside->buildSearchForm($queryID, $actionURL);

        /* Load pager. 加载分页逻辑，获取分页对象。*/
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);
        $this->session->set('opinioninsideList', $this->app->getURI(true),'backlog');

        $this->session->set('common_back_url', $this->app->getURI(true),'backlog');

        $this->session->set('opinioninsideHistory', $this->app->getURI(true)); // 浏览记录

        $opinions = $this->opinioninside->getList($browseType, $queryID, $orderBy, $pager);
        foreach ($opinions as $opinion){
            if(in_array($opinion->status,['delivery','online'])){
                $opinion->dealUser = '';
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

            $requirements = $this->loadModel('requirementinside')->getRequirementInfoByOpinionID($opinion->id);
            foreach ($requirements as $key => $requirement) {
                $dealUserArray = explode("," , $requirement->dealUser);
                $requirement->dealUser = implode(",", $dealUserArray);
                if(in_array($requirement->status,['delivered','onlined'])){
                    $requirement->dealUser = '';
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
        /* 将相关变量传递到页面。*/
        $this->view->title      = $this->lang->opinioninside->browse;
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
        $this->app->loadLang('opinion');
        /* 如果是post请求，就会调用model中的create方法，处理业务逻辑。根据model层返回信息，判断是否错误还是创建成功，如果创建成功会将创建操作记录到action表。*/
        if($_POST)
        {
            $this->config->opinioninside->create->requiredFields .=',background,overview,desc';

            $opinionID = $this->opinioninside->create();

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
        /* 获取创建需求意向时，页面所需的变量。*/
        $this->view->title = $this->lang->opinion->create;
        $this->view->sourceModeList = $sourceModeList;
        $this->view->title = $this->lang->opinioninside->create;
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
        $this->app->loadLang('opinion');
        $opinion = $this->loadModel('opinioninside')->getByID($opinionID);
        /* 如果是post请求，就会调用model中的update方法，处理业务逻辑。根据model层返回信息，判断是否错误还是编辑成功，如果成功了，会将修改操作记录到action表。*/
        if($_POST)
        {
            //创建人不是清总 并且 来源不是清总同步类型
            if($opinion->createdBy != 'guestcn'){ //$opinion->demandCode ?
                if(empty(strip_tags($_POST['background'])))
                {
                    dao::$errors['background'] =  "非外部需求意向【{$this->lang->opinioninside->background}】不能为空";
                }
                if(empty(strip_tags($_POST['overview'])))
                {
                    dao::$errors['overview'] = "非外部需求意向【{$this->lang->opinioninside->overview}】不能为空";
                }
                if(dao::isError())
                {
                    $response['result']  = 'fail';
                    $response['message'] = dao::getError();
                    $this->send($response);
                }
            }
            $changes = $this->opinioninside->update($opinionID);

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
        /* 将需求意向相关信息变量传统到页面用于编辑。*/
        $this->view->title   = $this->lang->opinioninside->edit;
        $this->view->sourceModeList = $sourceModeList;
        $this->view->users   = $this->loadModel('user')->getPairs('noclosed');
        $this->view->opinion = $this->loadModel('opinioninside')->getByID($opinionID);
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
        $this->app->loadLang('opinion');
        /* 设置需求详情页面返回的url连接。*/
        $this->session->set('demandinsideList', $this->app->getURI(true), 'backlog');
        /**
         * @var projectPlanModel $projectPlanModel
         * @var opinioninsideModel $opinionInsideModel
         * @var demandinsideModel $demandInsideModel
         * @var requirementinsideModel $requirementInsideModel
         */
        $projectPlanModel = $this->loadModel('projectplan');
        $opinionInsideModel = $this->loadModel('opinioninside');
        $demandInsideModel = $this->loadModel('demandinside');
        $requirementInsideModel = $this->loadModel('requirementinside');
        $opinionInfo = $opinionInsideModel->getByID($opinionID);
        if(in_array($opinionInfo->status,['delivery','online'])){
            $opinionInfo->dealUser = '';
        }

        if($opinionInfo->onlineTimeByDemand == '0000-00-00 00:00:00'){
            $opinionInfo->onlineTimeByDemand = '';
        }
        //接收时间
        if($opinionInfo->receiveDate == '0000-00-00 00:00:00'){
            $opinionInfo->receiveDate = '';
        }
        //期望完成时间
        if($opinionInfo->deadline == '0000-00-00'){
            $opinionInfo->deadline = '';
        }
        //提出时间
        if($opinionInfo->date == '0000-00-00'){
            $opinionInfo->date = '';
        }

        $changes = $this->loadModel('requirementchange')->getByDemandNumber($opinionInfo->demandCode);
        $demands = $demandInsideModel->getDemandByOpinionID($opinionID);
        $requirementInfo = $requirementInsideModel->getRequirementInfoByOpinionID($opinionID);
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
        /**@var projectPlanModel $projectPlanModel */
        $projectPlanModel = $this->loadModel('projectplan');
        $projectArray = array_filter(array_unique($mergeProjectArr));
        $projectList = [];
        if(!empty($projectArray)){
            $projectList = $projectPlanModel->getPlanInProjectIDs($projectArray);
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
        /* 查询需求意向详情及其相关变量信息，用于详情展示。*/
        $this->view->title   = $this->lang->opinioninside->view;
        $this->view->users   = $this->loadModel('user')->getPairs('noclosed|noletter');
        $this->view->actions = $this->loadModel('action')->getList('opinion', $opinionID);
        $this->view->opinion = $opinionInfo;
        $this->view->changes = $changes;
        $this->view->projectList =  $projectList;
        $this->display();
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
            $changes = $this->opinioninside->suspend($opinionID);

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

        $this->view->title   = $this->lang->opinioninside->suspend;
        $this->view->opinion = $this->opinioninside->getByID($opinionID);
        $this->display();
    }

    public function assignment($id)
    {
        if($_POST)
        {
            $changes = $this->opinioninside->assignment($id);

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
        $this->view->users   = $this->loadModel('user')->getPairs('noclosed|noletter');
        $this->display();
    }

    public function review($id)
    {
        if($_POST)
        {
            $this->opinioninside->review($id);

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

    public function close($id)
    {
        if($_POST)
        {
            $this->opinioninside->close($id);

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
        $status = $this->dao->select('status')->from(TABLE_OPINION)->where('id')->eq($id)->fetch('status');
        $this->view->status = $status;

        $this->display();
    }


    public function activate($id)
    {
        if($_POST)
        {
            $this->opinioninside->activate($id);

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
        // 关闭之后重启需求意向
        if($_POST)
        {
            $this->opinioninside->activate($id);

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

            if(isonlybody())
            {
                die(js::closeModal('parent.parent', 'this'));
            }
            else die(js::locate(inLink('browse'),'parent.parent'));
            die(js::reload('parent'));
        }

        /* 获取需求意向信息及其相关信息，用于删除时展示。*/
        $opinion = $this->opinioninside->getByID($opinionID);
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
            $this->loadModel('requirementinside')->subdivide($opinionID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $this->loadModel('action')->create('opinion', $opinionID, 'subdivide');

            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = $this->createLink('opinioninside', 'view', array('opinionID' => $opinionID));
            $this->send($response);
        }

        $this->view->title   = $this->lang->opinioninside->subdivide;
        $this->view->opinion = $this->opinioninside->getByID($opinionID);
        $this->view->users   = $this->loadModel('user')->getPairs('noclosed|nodeleted');
        $this->view->apps = array('0' => '') + $this->loadModel('application')->getapplicationNameCodePairs();

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
            $changes = $this->opinioninside->editassignedto($id);
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
        $this->view->title   = $this->lang->opinioninside->editassignedto;
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
            $this->opinioninside->setListValue();

            $this->loadModel('file');
            $this->app->loadLang('opinion');
            $opinionLang   = $this->lang->opinioninside;
            $opinionConfig = $this->config->opinioninside;

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
            if($this->session->opinioninsideOnlyCondition)
            {
                $opinions = $this->dao->select('*')->from(TABLE_OPINION)->where($this->session->opinioninsideQueryCondition)
                    ->beginIF($this->post->exportType == 'selected')->andWhere('id')->in($this->cookie->checkedItem)->fi()
                    ->orderBy($orderBy)
                    ->fetchAll('id');
            }
            else
            {
                $stmt  = $this->dbh->query($this->session->opinioninsideQueryCondition . (($this->post->exportType == 'selected' and $this->cookie->checkedItem) ? " AND $field IN({$this->cookie->checkedItem})" : '') . " ORDER BY " . strtr($orderBy, '_', ' '));
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
                 * @var demandinsideModel $demandInsideModel
                 * @var requirementinsideModel $requirementInsideModel
                 */
                $projectPlanModel = $this->loadModel('projectplan');
                $demandInsideModel = $this->loadModel('demandinside');
                $requirementInsideModel = $this->loadModel('requirementinside');
                $demands = $demandInsideModel->getDemandByOpinionID($opinion->id);
                $requirementInfo = $requirementInsideModel->getRequirementInfoByOpinionID($opinion->id);
                $ownProjectArr = !empty($opinion->project) ? explode(',',$opinion->project): [];
                $demandProjectArr = array_column($demands,'project');
                $mergeProjectArr = array_merge($ownProjectArr,$demandProjectArr,$requirementInfo);
                $projectArray = array_filter(array_unique($mergeProjectArr));
                $projectDesc=  $projectPlanModel->getPlanInProjectIDs($projectArray);
                if(!empty($projectArray)) $opinion->project = implode(',',array_values(array_column($projectDesc,'name')));

                /*待处理人*/
                if(in_array($opinion->status,['delivery','online'])){
                    $opinion->dealUser = '';
                }else{
                    if(isset($users[$opinion->dealUser]))  $opinion->dealUser  = $users[$opinion->dealUser];
                }
                if(isset($opinionLang->statusList[$opinion->status]))          $opinion->status     = $opinionLang->statusList[$opinion->status];
                if(isset($this->lang->opinion->categoryList[$opinion->category]))      $opinion->category   = $this->lang->opinion->categoryList[$opinion->category];
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

                if(!empty($opinion->synUnion))
                {
                    $opinion->synUnion = zmget($this->lang->opinion->synUnionList,$opinion->synUnion);
                }

                if(!empty($opinion->union))
                {
                    $opinion->union = zmget($this->lang->opinion->unionList,$opinion->union);
                }
                $opinion->createDate = substr($opinion->createdDate, 0, 10);
                $opinion->deadline   = substr($opinion->deadline, 0, 10);
                $opinion->date       = substr($opinion->date, 0, 10);
                $opinion->background = strip_tags($opinion->background);
                $opinion->overview   = strip_tags($opinion->overview);
                $opinion->desc       = strip_tags($opinion->desc);
            }

            /* 将字段和字段的值调用file模块的export2方法进行导出。*/
            $this->post->set('fields', $fields);
            $this->post->set('rows', $opinions);
            $this->post->set('kind', 'opinioninside');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        /* 将导出页面所需的相关变量传递到页面。*/
        $this->view->fileName        = $this->lang->opinioninside->common;
        $this->view->allExportFields = $this->config->opinioninside->list->exportFields;
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

            foreach($this->config->opinioninside->export->templateFields as $field) $fields[$field] = $this->lang->opinioninside->$field;
            $this->post->set('fields', $fields);
            $this->post->set('kind', 'opinioninside');
            $this->post->set('rows', array());
            $this->post->set('extraNum',   $this->post->num);
            $this->post->set('fileName',  $this->lang->opinioninside->opinionTemplate);
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
        $this->app->loadLang('opinion');
        /* 获取import方法导入的临时文件。*/
        $file    = $this->session->fileImport;
        $tmpPath = $this->loadModel('file')->getPathOfImportedFile();
        $tmpFile = $tmpPath . DS . md5(basename($file));

        /* 如果是post请求，则调用createFromImport方法保存导入的数据。如果是最后一页则跳转列表，否则跳转下一页数据。*/
        if($_POST)
        {
            $this->opinioninside->createFromImport();
            if($this->post->isEndPage)
            {
                unlink($tmpFile);
                die(js::locate($this->createLink('opinioninside','browse'), 'parent'));
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
            $opinionLang   = $this->lang->opinioninside;
            $opinionConfig = $this->config->opinioninside;
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
            die(js::locate($this->createLink('opinioninside','browse')));
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
        if(empty($opinionData)) die(js::locate($this->createLink('opinioninside','browse')));

        /* Judge whether the editedStories is too large and set session. */
        /* 判断要处理的需求意向是否太大，并设置session。*/
        $countInputVars  = count($opinionData) * 11;
        $showSuhosinInfo = common::judgeSuhosinSetting($countInputVars);
        if($showSuhosinInfo) $this->view->suhosinInfo = extension_loaded('suhosin') ? sprintf($this->lang->suhosinInfo, $countInputVars) : sprintf($this->lang->maxVarsInfo, $countInputVars);

        /* 将要导入的数据及其相关变量，传递到页面进行展示。*/
        $this->view->title      = $this->lang->opinioninside->common . $this->lang->colon . $this->lang->opinioninside->showImport;
        $this->view->position[] = $this->lang->opinioninside->showImport;

        $this->view->statusList = $this->lang->opinioninside->statusList;
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

    /**
     * Change a opion.
     *
     * @param  int    $opinionID
     * @access public
     * @return void
     */
    public function change($opinionID)
    {
        if($_POST)
        {
            /* 当请求方式为post时，调用change方法处理变更逻辑，如果处理成功则记录变更动作，然后返回成功信息。*/
            $changes = $this->opinioninside->change($opinionID);

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
            $changes = $this->opinioninside->recoveryed($opinionID);

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

        $this->view->title  = $this->lang->opinioninside->recoveryed;
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
            $changes = $this->opinioninside->ignore($opinionID);
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

        $this->view->title  = $this->lang->opinioninside->ignore;
        $this->view->notice  = $notice;
        $this->display();
    }
}
