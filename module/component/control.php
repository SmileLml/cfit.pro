<?php
class component extends control
{

    /**
     * 列表展示
     * shixuyang
     * @param $browseType
     * @param $param
     * @param $orderBy
     * @param $recTotal
     * @param $recPerPage
     * @param $pageID
     * @return void
     */
    public function browse($browseType = 'all', $param = 0, $orderBy = 'createdDate_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $browseType = strtolower($browseType);
        //搜索框的值
        $depts = $this->loadModel('dept')->getOptionMenu();
        $depts[0] = '';
        $this->config->component->search['params']['createdDept']['values'] = $depts;

        $projectPlanList = $this->loadModel('project')->getCodeNamePairs();
        if(!empty($projectPlanList)) {
            $this->config->component->search['params']['projectId']['values'] += $projectPlanList;
        }

        $queryID = ($browseType == 'bysearch')  ? (int)$param : 0;
        $actionURL = $this->createLink('component', 'browse', "browseType=bySearch&param=myQueryID");
        $this->component->buildSearchForm($queryID, $actionURL);

        /* 设置详情页面返回的url连接。*/
        $this->session->set('componentList', $this->app->getURI(true));
        $this->session->set('common_back_url', $this->app->getURI(true),'backlog');
        $this->session->set('componentHistory', $this->app->getURI(true));

        /* 构建pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);
        $components = $this->component->getList($browseType, $queryID, $orderBy, $pager);

        $this->view->title      = $this->lang->component->common;
        $this->view->orderBy    = $orderBy;
        $this->view->pager      = $pager;
        $this->view->param      = $param;
        $this->view->browseType = $browseType;
        $this->view->components   = $components;
        $this->view->projectPlanList   = $projectPlanList;
        $this->view->users      = $this->loadModel('user')->getPairs('noletter|noclosed');
        $this->view->depts      = $this->loadModel('dept')->getOptionMenu();
        $this->display();
    }

    /**
     * 详情页面.
     * shixuyang
     * @param  int    $componentID
     * @access public
     * @return void
     */
    public function view($componentID = 0)
    {

        $component = $this->component->getByID($componentID);
        $component = $this->loadModel('file')->replaceImgURL($component, '');
        //判断状态流转中是否前后状态一致，若操作后状态一样，则仅保留【操作时间】最近的那一次记录
        $consumedFix = $component->consumed;
        $temp = '';
        for ($i=count($consumedFix)-1; $i>=0; $i--){
            if (($consumedFix[$i]->before==$consumedFix[$i]->after) && ($consumedFix[$i]->after == $temp)){
                unset($consumedFix[$i]);
            }else{
                $temp = $consumedFix[$i]->after;
            }
        }

        /*if($component->level == 'dept'){
            unset($this->lang->component->reviewNodeStatusList[5]);
        }*/

        /* 查询需求条目及其相关的信息。*/
        $this->view->title       = $this->lang->component->view;
        $this->view->users       = $this->loadmodel('user')->getPairs('noletter|noclosed');
        $this->view->actions     = $this->loadmodel('action')->getList('component', $componentID);
        $this->view->component = $component;
        $this->view->consumed = $consumedFix;
        $this->view->depts       = $this->loadModel('dept')->getOptionMenu();
        $this->view->nodes       = $this->loadModel('review')->getNodes('component', $componentID, $component->changeVersion);
        $this->view->projectPlanList     = $this->loadModel('project')->getCodeNamePairs();
        if($component->isattach==1){
            $this->view->componentParent = $this->loadModel("componentpublic")->getSimpleByID($component->cid);
        }

        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Desc: 新建页面
     * User: liuyuhan
     */
    public function create(){
        if($_POST){
            $id = $this->component->create();
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            //操作记录
            $actionID = $this->loadModel('action')->create('component', $id, 'created');

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = inlink('browse');
            $this->send($response);

        }
        $this->view->title       = $this->lang->component->create;
        //公共项目
        //$openProjectList = $this->loadModel('projectplan')->getOpenProject();
        //关联项目
        $projectListInfo = $this->loadModel('project')->getProjectStats(0, 'all', 0, 'id_desc', null, 0);
//        $projectListInfo = $this->loadModel('user')->getExecutions($this->app->user->account, 'project');
//        $this->view->projectList= array('' => '') +array_column($projectListInfo,'name','id')+array_column($openProjectList,'name','project');
        $projectCodeList = array('' => '');
        foreach ($projectListInfo as $projectInfo){
            $projectCodeList = $projectCodeList+array($projectInfo->id => $projectInfo->code."（".$projectInfo->name."）");
        }

        $this->view->projectList= $projectCodeList;
        //维护人，为研效平台的与该操作人同部门的人员名单
        $userDeptId = $this->loadModel('user')->getUserDeptIds($this->app->user->account);
        $userInfoList = $this->loadModel('dept')->getUsers('inside',$userDeptId);
        $this->view->maintainers= array('' => '') + array_column($userInfoList,'realname','account');
        $this->display();
    }


    /**
     * 编辑功能
     * shixuyang
     * @return void
     */
    public function edit($componentID = 0){
        if($_POST){
            $changes = $this->component->update($componentID);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            if($changes)
            {
                $actionID = $this->loadModel('action')->create('component', $componentID, 'edited');
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = inlink('browse');
            $this->send($response);

        }
        //公共项目
        //$openProjectList = $this->loadModel('projectplan')->getOpenProject();

//        $projectListInfo = $this->loadModel('user')->getExecutions($this->app->user->account, 'project');
//        $this->view->projectList= array('' => '') +array_column($projectListInfo,'name','id')+array_column($openProjectList,'name','project');
        //维护人，为研效平台的与该操作人同部门的人员名单
        $userDeptId = $this->loadModel('user')->getUserDeptIds($this->app->user->account);
        $userInfoList = $this->loadModel('dept')->getUsers('inside',$userDeptId);
        $this->view->maintainers= array('' => '') + array_column($userInfoList,'realname','account');
        $component = $this->component->getByID($componentID);
        if($component->type == 'thirdParty'){
            $component->newThirdPartyName = $component->name;
            $component->newThirdPartyVersion = $component->version;
            $component->newThirdPartyDevelopLanguage = $component->developLanguage;
            $component->newThirdPartyProjectId = $component->projectId;
            $component->newPublicName = '';
            $component->newPublicVersion = '';
            $component->newPublicDevelopLanguage = '';
            $component->newPublicProjectId = '';
        }else if($component->type == 'public'){
            $component->newPublicName = $component->name;
            $component->newPublicVersion = $component->version;
            $component->newPublicDevelopLanguage = $component->developLanguage;
            $component->newPublicProjectId = $component->projectId;
            $component->newThirdPartyName = '';
            $component->newThirdPartyVersion = '';
            $component->newThirdPartyDevelopLanguage = '';
            $component->newThirdPartyProjectId = '';
        }
        if(in_array($this->app->user->account, $component->pmrm)){
            //所有项目
            $projectListInfo = $this->loadModel('project')->getCodeNamePairs();
            $this->view->projectList= array('' => '') + $projectListInfo;
            //维护人
            $this->view->maintainers       = $this->loadmodel('user')->getPairs('noletter|noclosed');
        }else{
            //关联项目
            $projectListInfo = $this->loadModel('project')->getProjectStats(0, 'all', 0, 'id_desc', null, 0);
            $projectCodeList = array('' => '');
            foreach ($projectListInfo as $projectInfo){
                $projectCodeList = $projectCodeList+array($projectInfo->id => $projectInfo->code."（".$projectInfo->name."）");
            }
            $this->view->projectList= $projectCodeList;
            //维护人
            $userInfoList = $this->loadModel('dept')->getUsers('inside',$userDeptId);
            $this->view->maintainers= array('' => '') + array_column($userInfoList,'realname','account');
        }


        $this->view->title       = $this->lang->component->edit;
        $this->view->component = $component;

        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Desc:提交审核页面
     * @param int $componentID
     * liuyuhan
     */
    public function submit($componentID = 0){

        if($_POST) {
            $changes = $this->component->submit($componentID);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            if($changes)
            {
                $actionID = $this->loadModel('action')->create('component', $componentID, 'submit', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->component->submitSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }
        $this->display();
    }

    /**
     * 评审功能
     * shixuyang
     * @param $componentID
     * @return void
     */
    public function review($componentID = 0,$changeVersion = 1, $reviewStage = 0){
        if($_POST){
            if($reviewStage == 1){
                $this->component->leaderReview($componentID);
            }else if($reviewStage == 2){
                $this->component->toappointReview($componentID);
            }else if($reviewStage == 4 || $reviewStage == 5){
                $this->component->architReview($componentID);
            }else if($reviewStage == 3){
                $this->component->teamreviewReview($componentID);
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

            $this->send($response);

        }
        $component = $this->component->getByID($componentID);
        if($component->status == 'toarchitreview'){
            $resultStatus =  $this->dao->select('status')->from(TABLE_REVIEWNODE)->where('objectType')->eq('component')
                ->andWhere('objectID')->eq($componentID)
                ->andWhere('version')->eq($component->changeVersion)
                ->andWhere('stage')->eq('3')->fetch('status');
            $this->view->resultStatus = $resultStatus;
        }else if($component->status == 'toarchitleaderreview'){
            $resultStatus =  $this->dao->select('status')->from(TABLE_REVIEWNODE)->where('objectType')->eq('component')
                ->andWhere('objectID')->eq($componentID)
                ->andWhere('version')->eq($component->changeVersion)
                ->andWhere('stage')->eq('4')->fetch('status');
            //如果第四节点是ignore,就去找第二节点的审批结果
            if($resultStatus == 'ignore'){
                $resultStatus =  $this->dao->select('status')->from(TABLE_REVIEWNODE)->where('objectType')->eq('component')
                    ->andWhere('objectID')->eq($componentID)
                    ->andWhere('version')->eq($component->changeVersion)
                    ->andWhere('stage')->eq('2')->fetch('status');
                $this->view->resultStatus = $resultStatus;
            }else{
                $this->view->resultStatus = $resultStatus;
            }
        }

        if($component->type=='public'){
            $this->view->componentList = array('' => '') +$this->loadModel("componentpublic")->getPublicComponetList();
        }else{
            $this->view->componentList = [];
        }
//        a($this->view->componentList );
//        exit();

        $this->view->title       = $this->lang->component->review;
        $this->view->component = $component;
        $this->view->users       = $this->loadmodel('user')->getPairs('noclosed|noletter|noempty');
        $this->display();

    }

    /**
     * 发布组件管理
     * shixuyang
     * @param $componentID
     * @return void
     */
    public function publish($componentID = 0){
        $component = $this->component->getByID($componentID);
        if($_POST) {

            $changes = $this->component->publish($componentID);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            if($changes)
            {
                //
                if($this->post->publishType == "incorporate"){
                    $attachcomponent = $this->loadModel("componentpublic")->getSimpleByID($this->post->cid);
                    $actionID = $this->loadModel('action')->create('component', $componentID, 'incorporate', "发布形式：纳入现有组件<br>组件名称：".$attachcomponent->name);
                }else{
                    $actionID = $this->loadModel('action')->create('component', $componentID, 'publish', $this->post->comment);
                }

                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }
        $this->view->component       = $component;
        $this->view->users       = $this->loadmodel('user')->getPairs('noletter|noclosed');
        $this->view->depts       = $this->loadModel('dept')->getOptionMenu();
        if($component->type == 'thirdParty'){
            $this->view->categoryList       = array('' => '请选择')+$this->lang->component->thirdcategoryList;
        }else{
            $this->view->categoryList       = array('' => '请选择')+$this->lang->component->categoryList;
        }
        $this->view->componentList = array('' => '请选择') +$this->loadModel("componentpublic")->getPublicComponetList();
//a($this->view->componentList);
//exit();
        $this->view->publishStatusList       = array('' => '请选择')+$this->lang->component->publishStatusList;
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Desc:重新指派评估小组成员
     * liuyuhan
     */
    public function changeteamreviewer($componentID = 0){
        if($_POST){
            $this->component->changeteamreviewer($componentID);
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
        $component = $this->component->getByID($componentID);
        $nodes = $this->loadModel('review')->getNodes('component', $componentID, $component->changeVersion);
        $selectedViewers = array_column($nodes[2]->reviewers,'reviewer');
        $this->view->component = $component;
        $this->view->selectedViewers = $selectedViewers;
        $this->view->users = $this->loadmodel('user')->getPairs('noclosed|noletter');
        $this->display();
    }

    public function editcomment($reviewersID,$commentID){
        if($_POST){
            $this->component->editcomment($reviewersID,$commentID);
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
        $this->display();
    }


    /**
     * Project: chengfangjinke
     * Desc: 导出列表页数据 Excel
     * t_jinzhuliang
     */
    public function export($orderBy = 'id_desc', $browseType = 'all')
    {
        /* format the fields of every data in order to export data. */
        $this->app->loadLang('component');
        unset($this->lang->exportTypeList['selected']);
        $this->lang->exportTypeList['all'] = '全部查询结果';
        if($_POST)
        {
            $this->loadModel('file');
            $componentLang   = $this->lang->component;
            $componentConfig = $this->config->component;

            /* Create field lists. */
            $fields = $this->post->exportFields ? $this->post->exportFields : explode(',', $componentConfig->list->exportFields);
            foreach($fields as $key => $fieldName)
            {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = isset($componentLang->$fieldName) ? $componentLang->$fieldName : $fieldName;
                unset($fields[$key]);
            }

            /* Get datas. */
            $datas = array();

            if($this->session->componentOnlyCondition)
            {

                $datas = $this->dao->select('*')->from(TABLE_COMPONENT)->where($this->session->componentOnlyCondition)
                    ->andWhere('deleted')->eq('0')
                    ->beginIF($this->post->exportType == 'selected')->andWhere('id')->in($this->cookie->checkedItem)->fi()
                    ->orderBy('id_desc')->fetchAll('id');
            }
            else
            {

                $stmt = $this->dbh->query($this->session->componentQueryCondition . ($this->post->exportType == 'selected' ? " AND id IN({$this->cookie->checkedItem})" : '') . " ORDER BY " . strtr($orderBy, '_', ' '));
                while($row = $stmt->fetch()) $datas[$row->id] = $row;
            }

            $depts      = $this->loadModel('dept')->getOptionMenu();
            $users = $this->loadModel('user')->getPairs('noletter|noclosed');
            $projectPlanList = $this->loadModel('project')->getCodeNamePairs();
            foreach ($datas as $k=>$data)
            {
                //组件

                $data->componentType = zget($this->lang->component->type,$data->type);
                $data->level = $data->type == 'public' ? zget($this->lang->component->levelList,$data->level):'/';
                $data->application = zget($this->lang->component->applicationMethod,$data->applicationMethod);
                $data->project = zget($projectPlanList,$data->projectId);
                $data->status = zget($this->lang->component->statusList,$data->status);
                $dealUserTitle = '';
                $dealUsersTitles = '';
                if (!empty($data->dealUser)) {
                    foreach (explode(',', $data->dealUser) as $dealUser) {
                        if (!empty($dealUser)) $dealUserTitle .= zget($users, $dealUser, $dealUser) . ',';
                    }
                }
                $data->dealUser = trim($dealUserTitle, ',');

                $data->createdBy = zget($users,$data->createdBy);
                $data->createdDept = zget($depts,$data->createdDept);


            }


            $this->post->set('fields', $fields);
            $this->post->set('rows', $datas);
            $this->post->set('kind', 'component');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        $this->view->fileName        = $this->lang->component->exportExcel.'-'.time();
        $this->view->allExportFields = $this->config->component->list->exportFields;
        $this->view->customExport    = false;

        $this->display();
    }

    public function delete($ID, $confirm = 'no')
    {
        if($confirm == 'no')
        {
            echo js::confirm($this->lang->component->confirmDelete, $this->createLink('component', 'delete', "ID=$ID&confirm=yes"), '');
            exit;
        }
        else
        {

            $componentInfo = $this->component->getSimpleByID($ID);
            if(!$componentInfo){
                die(js::alert($this->lang->component->nowComponetNotExist));
            }

            //架构部指定人员（架构部处理、架构部确认）节点人员
            $productManagerReviewer = array_keys($this->lang->component->productManagerReviewer);
            //平台结构部领导
            $productManagerReviewerManager = $this->dao->select('id,manager1')->from(TABLE_DEPT)->where('name')->eq('平台架构部')->fetch();
            $pmrm = explode(',', trim($productManagerReviewerManager->manager1, ','));
            $pmrm = array_merge($productManagerReviewer, $pmrm);

            //验证权限: 状态待提交,当前操作人是管理员(后台-》自定义-》组件管理-》架构部处理人和部门负责人单人)或提交人
            if(!(($componentInfo->status == 'tosubmit') and ($this->app->user->account == $componentInfo->createdBy or in_array($this->app->user->account, $pmrm)))){
                die(js::alert($this->lang->component->notDeleteAuth));
            }
            if($componentInfo->status == $this->lang->component->tosubmit ){
                //默认允许删除
                $flag = true;
                $reviewnodeList = $this->dao->select("*")->from(TABLE_REVIEWNODE)->where("objectType")->eq('component')->andWhere('objectID')->eq($ID)->fetchAll();
                if($reviewnodeList){
                    foreach ($reviewnodeList as $val){
                        if(in_array($val->nodeCode,$this->lang->component->notAllowDeleteStatus) || in_array($val->nodeCode,$this->lang->component->notAllowDeleteStage)){
                            $flag = false;
                            break;
                        }
                    }
                }



            }else{
                //不允许删除
                $flag = false;
            }

            if($flag){
                $this->component->delete(TABLE_COMPONENT, $ID);
                echo js::alert($this->lang->component->deleteSuccess);
                die(js::locate(inlink('browse'), 'parent'));
            }else{

                die(js::alert($this->lang->component->deleteNotAllow));
            }

        }
    }


    public function editstatus($ID){

        if($_POST){

            $changes = $this->component->updatestatus($ID);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            if($changes)
            {
                $actionID = $this->loadModel('action')->create('component', $ID, 'editstatus');
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }

        $this->view->component = $this->component->getSimpleByID($ID);
        $statusArr = [];
        if($this->view->component->status == 'reject'){
            $statusArr = [$this->lang->component->statusKeyList['tosubmit']=>$this->lang->component->statusList['tosubmit']];

        }elseif ($this->view->component->status == 'tosubmit'){
            $statusArr = [$this->lang->component->statusKeyList['reject']=>$this->lang->component->statusList['reject']];

        }
        $this->view->statusArr = $statusArr;
        $this->display();
    }

}