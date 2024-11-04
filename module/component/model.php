<?php
class componentModel extends model
{
    /**
     * 获取列表
     * shixuyang
     * @param $browseType
     * @param $queryID
     * @param $orderBy
     * @param $pager
     * @return mixed
     */
    public function getList($browseType, $queryID, $orderBy, $pager = null)
    {
        $componentQuery = '';
        if($browseType == 'bysearch')
        {
            $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : '';
            if($query)
            {
                $this->session->set('componentQuery', $query->sql);
                $this->session->set('componentForm', $query->form);
            }

            if($this->session->componentQuery == false) $this->session->set('componentQuery', ' 1 = 1');

            $componentQuery = $this->session->componentQuery;

        }

        $components = $this->dao->select('*')->from(TABLE_COMPONENT)
            ->where('deleted')->eq('0')
            ->beginIF($browseType != 'all' and $browseType != 'bysearch')->andWhere('status')->eq($browseType)->fi()
            ->beginIF($browseType == 'bysearch')->andWhere($componentQuery)->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');
        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'component', $browseType != 'bysearch');
        //每个数据添加架构部处理人，方便按钮高亮
        //架构部指定人员（架构部处理、架构部确认）节点人员
        $productManagerReviewer = array_keys($this->lang->component->productManagerReviewer);
        //平台结构部领导
        $productManagerReviewerManager = $this->dao->select('id,manager1')->from(TABLE_DEPT)->where('name')->eq('平台架构部')->fetch();
        $pmrm = explode(',', trim($productManagerReviewerManager->manager1, ','));
        $pmrm = array_merge($productManagerReviewer, $pmrm);
        foreach ($components as $component){
            $component->pmrm = $pmrm;
        }

        return $components;
    }


    /**
     * 构建搜索框
     * shixuyang
     * @param $queryID
     * @param $actionURL
     * @return void
     */
    public function buildSearchForm($queryID, $actionURL)
    {
        $this->config->component->search['actionURL'] = $actionURL;
        $this->config->component->search['queryID']   = $queryID;


        $this->loadModel('search')->setSearchParams($this->config->component->search);
    }

    /**
     * 获取单个数据
     * shixuyang
     * @param $requirementID
     * @param $version
     * @return mixed
     */
    public function getByID($componentID)
    {
        $component = $this->dao->findByID($componentID)->from(TABLE_COMPONENT)->fetch();
        $component = $this->loadModel('file')->replaceImgURL($component, 'applicationReason,evidence,functionDesc');

        $component->files = $this->loadModel('file')->getByObject('component', $componentID);

        $reviewer = $this->loadModel('review')->getReviewer('component', $component->id, $component->changeVersion);
        $component->reviewer = $reviewer ? ',' . $reviewer . ','  : '';

        $component = $this->loadModel('file')->replaceImgURL($component, 'desc');

        $cs = $this->dao->select('*')->from(TABLE_CONSUMED)->where('objectType')->eq('component') //状态流转 工作量
        ->andWhere('objectID')->eq($component->id)
            ->andWhere('deleted')->ne(1)
            ->orderBy('id_asc')
            ->fetchAll();
        $component->consumed = $cs;

        //获取架构部领导和机构部处理人
        //架构部指定人员（架构部处理、架构部确认）节点人员
        $productManagerReviewer = array_keys($this->lang->component->productManagerReviewer);
        //平台结构部领导
        $productManagerReviewerManager = $this->dao->select('id,manager1')->from(TABLE_DEPT)->where('name')->eq('平台架构部')->fetch();
        $pmrm = explode(',', trim($productManagerReviewerManager->manager1, ','));
        $pmrm = array_merge($productManagerReviewer, $pmrm);
        $component->pmrm = $pmrm;

        return $component;
    }

    public function getSimpleByID($componentID)
    {
        $component = $this->dao->findByID($componentID)->from(TABLE_COMPONENT)->fetch();
        if($component){
            $component = $this->loadModel('file')->replaceImgURL($component, 'applicationReason,evidence,functionDesc');

            $component->files = $this->loadModel('file')->getByObject('component', $componentID);
        }



        return $component;
    }

    public function getRelationComponent($cid,$field="*"){
        $relationComponent = $this->dao->select($field)->from(TABLE_COMPONENT)->where('cid')->eq($cid)->andWhere('type')->eq('public')->andWhere('status')->eq('incorporate')->fetchAll();
        return $relationComponent;
    }

    /**
     * Project: chengfangjinke
     * Desc: 新建组件申请
     * liuyuhan
     */
    public function create(){

        $mydepts = $this->loadModel('user')->getUserDeptIds($this->app->user->account)[0];
        if(!$mydepts){
            dao::$errors['depts'] = $this->lang->component->mydeptisempty;
            return false;
        }

        $postData = fixer::input('post')
            ->add('createdBy',$this->app->user->account)
            ->add('createdDept',$mydepts)
            ->add('createdDate',helper::now())
            ->add('dealUser', $this->app->user->account)
            ->add('status', 'tosubmit')
            ->addIF($this->post->type=='thirdParty' && $this->post->applicationMethod=='new', 'name',$this->post->newThirdPartyName)
            ->addIF($this->post->type=='thirdParty' && $this->post->applicationMethod=='new', 'version',$this->post->newThirdPartyVersion)
            ->addIF($this->post->type=='thirdParty' && $this->post->applicationMethod=='new', 'developLanguage',$this->post->newThirdPartyDevelopLanguage)
            ->addIF($this->post->type=='thirdParty' && $this->post->applicationMethod=='new', 'projectId',$this->post->newThirdPartyProjectId)
            ->addIF($this->post->type=='public' && $this->post->applicationMethod=='new', 'name',$this->post->newPublicName)
            ->addIF($this->post->type=='public' && $this->post->applicationMethod=='new', 'version',$this->post->newPublicVersion)
            ->addIF($this->post->type=='public' && $this->post->applicationMethod=='new', 'developLanguage',$this->post->newPublicDevelopLanguage)
            ->addIF($this->post->type=='public' && $this->post->applicationMethod=='new', 'projectId',$this->post->newPublicProjectId)
            ->remove('files')
            ->stripTags($this->config->component->editor->edit['id'], $this->config->allowedTags)
            ->get();

        if(isset($postData->gitlab)){
            $postData->gitlab = json_encode($postData->gitlab,JSON_UNESCAPED_UNICODE);
        }else{
            $postData->gitlab = json_encode([]);
        }

        //富文本框处理
        $postData = $this->loadModel('file')->processImgURL($postData, $this->config->component->editor->create['id'], $this->post->uid);
        //进行申请数据的校验
        $this->filterApplicationForm($postData);

        //若申请方式为【新引入】，需要根据“组件名称”做判重验证
        if($postData->applicationMethod=='new'){
            $componentName = $this->dao->select('name')->from(TABLE_COMPONENT)->where('name')->eq($postData->name)->fetch();
            $componentRealeaseName = $this->dao->select('name')->from(TABLE_COMPONENT_RELEASE)->where('name')->eq($postData->name)->fetch();
            if (!empty($componentName) or !empty($componentRealeaseName)){
                $itemName='';
                if ($postData->type=='thirdParty' && $postData->applicationMethod=='new') $itemName = 'newThirdPartyName';
                if ($postData->type=='public' && $postData->applicationMethod=='new') $itemName = 'newPublicName';
                dao::$errors[$itemName] =  sprintf($this->lang->component->nameRepeatError, $this->lang->component->name);
            }
        }
        //存入数据库
        if(!dao::isError()){
            $this->createApplicationForm($postData);
            $lastId =  $this->dao->lastInsertID();
            //附件上传
            $this->loadModel('file')->updateObjectID($postData->uid, $lastId, 'component');
            $this->loadModel('file')->saveUpload('component', $lastId);
            //状态流转
            $this->loadModel('consumed')->record('component', $lastId, '0', $this->app->user->account, '', 'tosubmit', array());

            if(!dao::isError()) return $lastId;
        }
        return false;
    }


    /**
     * Project: chengfangjinke
     * Desc: 组件申请入库
     * User: liuyuhan
     */
    private function createApplicationForm($data)
    {
        $data = (array)$data;
        $deletItemsArray = explode(',', str_replace(' ', '', $this->config->component->create->deletItems));
        foreach ($deletItemsArray as $item)
        {
            unset($data[$item]);
        }
        return $this->dao->insert(TABLE_COMPONENT)
            ->data($data)->autoCheck()
            ->exec();

    }


    /**
     * Project: chengfangjinke
     * Desc: 检查必填项是否为空
     * liuyuhan
     */
    private function checkParamsNotEmpty($data, $fields)
    {
        if(!is_array($data)) {
            if (!is_object($data)) $data = (object)$data;
            $data = (array)$data;
        }
        $fieldArray = explode(',', str_replace(' ', '', $fields));
        foreach ($fieldArray as $item)
        {
            if(is_null($data[$item]) || $data[$item] == ''){
                $itemName = $this->lang->component->$item ?? $item;
                dao::$errors[$item] =  sprintf($this->lang->component->emptyObject, $itemName);
            }
        }
    }

    /**
     * Project: chengfangjinke
     * Desc: 进行申请数据的校验
     * liuyuhan
     */
    private function filterApplicationForm($data){
        //判断是否数字和英文.的组合
//        $reg_version = '/^[A-Za-z0-9.]+$/';
        //判断是否为中文
        $reg_licenseType = '/[\x80-\xff]/';
        $reg_version = $reg_licenseType;
        //判断是否英文大小写组合
//        $reg_licenseType = '/^[A-Za-z]+$/';
        //第三方组件-新引入输入检查
        if ($data->type=='thirdParty' && $data->applicationMethod=='new'){
            $this->checkParamsNotEmpty($data, $this->config->component->create->newThirdParty->requiredFields);
            if (preg_match($reg_version,$data->version)){
                dao::$errors['newThirdPartyVersion'] =sprintf($this->lang->component->versionError,$this->lang->component->version);
            }
            if (preg_match($reg_licenseType,$data->licenseType)){
                dao::$errors['licenseType'] =sprintf($this->lang->component->licenseTypeError,$this->lang->component->version);
            }
            //公共组件-新引入输入检查
        }elseif ($data->type=='public' && $data->applicationMethod=='new'){
            $this->checkParamsNotEmpty($data, $this->config->component->create->newPublic->requiredFields);
            if (preg_match($reg_version,$data->version)){
                dao::$errors['newPublicVersion'] =sprintf($this->lang->component->versionError,$this->lang->component->version);
            }
        }
    }

    /**
     * 编辑
     * shixuyang
     * @return false
     */
    public function update($componentID = 0){
        $componentOld = $this->getByID($componentID);

        $postData = fixer::input('post')
            ->add('editedBy',$this->app->user->account)
            ->add('editedDate',helper::now())
            /*->add('dealUser', $this->app->user->account)*/
            /*->add('status', 'tosubmit')*/
            ->add('deleted', '0')
            ->addIF($this->post->type=='thirdParty' && $this->post->applicationMethod=='new', 'name',$this->post->newThirdPartyName)
            ->addIF($this->post->type=='thirdParty' && $this->post->applicationMethod=='new', 'version',$this->post->newThirdPartyVersion)
            ->addIF($this->post->type=='thirdParty' && $this->post->applicationMethod=='new', 'developLanguage',$this->post->newThirdPartyDevelopLanguage)
            ->addIF($this->post->type=='thirdParty' && $this->post->applicationMethod=='new', 'projectId',$this->post->newThirdPartyProjectId)
            ->addIF($this->post->type=='public' && $this->post->applicationMethod=='new', 'name',$this->post->newPublicName)
            ->addIF($this->post->type=='public' && $this->post->applicationMethod=='new', 'version',$this->post->newPublicVersion)
            ->addIF($this->post->type=='public' && $this->post->applicationMethod=='new', 'developLanguage',$this->post->newPublicDevelopLanguage)
            ->addIF($this->post->type=='public' && $this->post->applicationMethod=='new', 'projectId',$this->post->newPublicProjectId)
            ->remove('files')
            ->stripTags($this->config->component->editor->create['id'], $this->config->allowedTags)
            ->get();
        if(isset($postData->gitlab)){
            $postData->gitlab = json_encode($postData->gitlab,JSON_UNESCAPED_UNICODE);
        }else{
            $postData->gitlab = json_encode([]);
        }
        //富文本框处理
        $postData = $this->loadModel('file')->processImgURL($postData, $this->config->component->editor->edit['id'], $this->post->uid);

        //进行申请数据的校验
        $this->filterApplicationForm($postData);

        //若申请方式为【新引入】，需要根据“组件名称”做判重验证
        if($postData->applicationMethod=='new'){
            $componentName = $this->dao->select('name')->from(TABLE_COMPONENT)->where('name')->eq($postData->name)->andWhere('id')->ne($componentID)->fetch();
            $componentRealeaseName = $this->dao->select('name')->from(TABLE_COMPONENT_RELEASE)->where('name')->eq($postData->name)->andWhere('componentId')->ne($componentID)->fetch();
            $componentRealeaseNull = $this->dao->select('name')->from(TABLE_COMPONENT_RELEASE)->where('name')->eq($postData->name)->andWhere('componentId')->isNull()->fetch();
            if (!empty($componentName) or !empty($componentRealeaseName) or !empty($componentRealeaseNull)){
                $itemName='';
                if ($postData->type=='thirdParty' && $postData->applicationMethod=='new') $itemName = 'newThirdPartyName';
                if ($postData->type=='public' && $postData->applicationMethod=='new') $itemName = 'newPublicName';
                dao::$errors[$itemName] =  sprintf($this->lang->component->nameRepeatError, $this->lang->component->name);
            }
        }
        $this->tryError();
        if($postData->type != $componentOld->type && $postData->type=='thirdParty'){
            $postData->cid = 0;
            $postData->isattach = 0;

        }
        //存入数据库
        $this->updateApplicationForm($componentID,$postData);
        if(!dao::isError()){
            //附件上传
            $this->loadModel('file')->updateObjectID($postData->uid, $componentID, 'component');
            $this->loadModel('file')->saveUpload('component', $componentID);
            //状态流转
            $this->loadModel('consumed')->record('component', $componentID, '0', $this->app->user->account, $componentOld->status, $componentOld->status, array());
        }
        //获取新的数据
        $componentNew = $this->getByID($componentID);
        $this->tryError();
        return common::createChanges($componentOld, $componentNew);
    }

    /**
     * 更新数据
     * @param $data
     * @return mixed
     */
    private function updateApplicationForm($componentID,$data)
    {
        $data = (array)$data;
        $deletItemsArray = explode(',', str_replace(' ', '', $this->config->component->edit->deletItems));
        foreach ($deletItemsArray as $item)
        {
            unset($data[$item]);
        }
        return $this->dao->update(TABLE_COMPONENT)->data($data)->autoCheck()
            ->where('id')->eq($componentID)
            ->exec();

    }

    /**
     * 尝试报错 或需要rollback
     */
    public function tryError($rollBack = 0)
    {
        if(dao::isError())
        {
            if($rollBack == 1){
                $this->dao->rollBack();
            }
            $response['result']  = 'fail';
            $response['message'] = dao::getError();
            die(json_encode($response, JSON_UNESCAPED_UNICODE));
        }
    }

    /**
     * 按钮权限控制
     * @param
     * @param $action
     * @return bool
     */
    public static function isClickable($component, $action)
    {
        global $app;
        $action = strtolower($action);

        if($action == 'edit'){
            return (($component->status == 'tosubmit') and ($app->user->account == $component->createdBy)) or (!in_array($component->status,['published','incorporate']) and in_array($app->user->account, $component->pmrm));
        }
        if($action == 'review') return (in_array($component->status, array('todepartreview','toappoint','toteamreview','toarchitreview','toarchitleaderreview')))   and (in_array($app->user->account,explode(',',$component->dealUser)));
        if($action == 'submit') return ($component->status == 'tosubmit') and (($app->user->account == $component->createdBy) or (in_array($app->user->account, $component->pmrm)));
        if($action == 'publish') return ($component->status == 'topublish') and (in_array($app->user->account,explode(',',$component->dealUser)) or (in_array($app->user->account, $component->pmrm)));
        if($action == 'changeteamreviewer') return ($component->status == 'toteamreview');

        if($action == 'delete'){
            return (($component->status == 'tosubmit') and ($app->user->account == $component->createdBy or in_array($app->user->account, $component->pmrm)));
        }
        if($action == 'editstatus'){
            return ((in_array($component->status,[$app->lang->component->statusKeyList['tosubmit'],$app->lang->component->statusKeyList['reject']]) ) and (in_array($app->user->account, $component->pmrm)));;
        }
        return true;
    }

    /**
     * 领导审批审批
     * @param $componentId
     * @return void
     */
    public function leaderReview($componentId){
        $component = $this->getByID($componentId);
        //判断当前状态和审核中传入的状态是否一致，以防有同组中其他人修改
        $res = $this->checkAllowReview($component, $this->post->changeVersion, $this->post->reviewStage, $this->app->user->account);
        if(!$res['result']){
            dao::$errors['statusError'] = $res['message'];
        }
        $this->tryError();
        if(!$this->post->result){
            dao::$errors['result'] = sprintf($this->lang->component->emptyObject, $this->lang->component->result);
        }
        if($this->post->result == 'reject' && !$this->post->rejectReason){
            dao::$errors['rejectReason'] = sprintf($this->lang->component->emptyObject, $this->lang->component->rejectReason);
        }
        $this->tryError();
        $is_all_check_pass = false;
        $extraObj = new stdclass();
        $extraObj->involved = $this->post->mailto;
        if($this->post->result == 'reject'){
            $extraObj->rejectReason = $this->post->rejectReason;
        }
        $result = $this->check('component', $componentId, $component->changeVersion, $this->post->result, $this->post->dealcomment, '', $extraObj, $is_all_check_pass,1);
        if($result == 'pass')
        {
            $add = 1;
            //下一审核节点
            $nextReviewStage = $component->reviewStage + $add;
            //下一审核状态
            if(isset($this->lang->component->reviewNodeList[$nextReviewStage])){
                $status = $this->lang->component->reviewNodeStatusList[$nextReviewStage];
            }
            $this->dao->update(TABLE_COMPONENT)->set('reviewStage = reviewStage+' . $add)->set('status')->eq($status)->where('id')->eq($componentId)->exec();
            $this->loadModel('consumed')->record('component', $componentId, 0, $this->app->user->account, $component->status, $status, $this->post->mailto,"");
            $next = $this->dao->select('id')->from(TABLE_REVIEWNODE)->where('objectType')->eq('component')
                ->andWhere('objectID')->eq($componentId)
                ->andWhere('version')->eq($component->changeVersion)
                ->andWhere('status')->eq('wait')->orderBy('stage,id')->fetch('id');
            if($next)
            {
                $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('pending')->where('id')->eq($next)->exec();
                $this->dao->update(TABLE_REVIEWER)->set('status')->eq('pending')->where('node')->eq($next)->exec();

                $reviewers = $this->loadModel('review')->getReviewer('component', $componentId, $component->changeVersion, $nextReviewStage);
                $this->dao->update(TABLE_COMPONENT)->set('dealUser')->eq($reviewers)->where('id')->eq($componentId)->exec();
                $actionID = $this->loadModel('action')->create('component', $componentId, 'reviewed', "审批结论：通过<br>操作备注：".$this->post->dealcomment);
            }
        }else if($result == 'reject') {
            $this->dao->update(TABLE_COMPONENT)->set('reviewStage')->eq('')->set('status')->eq('tosubmit')->set('dealUser')->eq($component->createdBy)->where('id')->eq($componentId)->exec();
            $this->loadModel('consumed')->record('component', $componentId, 0, $this->app->user->account, $component->status, 'tosubmit', $this->post->mailto,"");
            $actionID = $this->loadModel('action')->create('component', $componentId, 'reviewed', "审批结论：不通过<br>不通过原因：".$this->post->rejectReason."<br>操作备注：".$this->post->dealcomment);
        }
    }

    /**
     * 架构部处理审批
     * @param $componentId
     * @return void
     */
    public function toappointReview($componentId){
        $component = $this->getByID($componentId);
        //判断当前状态和审核中传入的状态是否一致，以防有同组中其他人修改
        $res = $this->checkAllowReview($component, $this->post->changeVersion, $this->post->reviewStage, $this->app->user->account);
        if(!$res['result']){
            dao::$errors['statusError'] = $res['message'];
        }
        $this->tryError();
        if(!$this->post->result){
            dao::$errors['result'] = sprintf($this->lang->component->emptyObject, $this->lang->component->result);
        }
        if($this->post->result == 'reject' && !$this->post->rejectReason){
            dao::$errors['rejectReason'] = sprintf($this->lang->component->emptyObject, $this->lang->component->rejectReason);
        }

        if($this->post->result == 'appoint' && !$this->post->teamMember){
            dao::$errors['teamMember'] = sprintf($this->lang->component->emptyObject, $this->lang->component->teamMember);
        }
        if($this->post->result == 'appoint' && $this->post->teamMember){
            $teamMemberStr = trim(implode(",", $this->post->teamMember),',');
            $teamMemberCount = count(explode(",",$teamMemberStr));
            if($teamMemberCount%2==0){
                dao::$errors['teamMember'] = $this->lang->component->teamMemberCountError;
            }
        }
        if($this->post->result == 'incorporate' && !$this->post->cid){

            dao::$errors['cid'] = $this->lang->component->componetNOTEmpty;

        }

        $this->tryError();

        $is_all_check_pass = false;
        $extraObj = new stdclass();
        $extraObj->involved = $this->post->mailto;
        if($this->post->result == 'reject'){
            $extraObj->rejectReason = $this->post->rejectReason;
        }

        $result = $this->check('component', $componentId, $component->changeVersion, $this->post->result, $this->post->dealcomment, '', $extraObj, $is_all_check_pass,1);
        if($result == 'pass')
        {
            if($component->level == 'dept'){
                $productManagerReviewer = array_keys($this->lang->component->productManagerReviewer);
                $dealUser = implode(",", $productManagerReviewer);
                $this->dao->update(TABLE_COMPONENT)->set('reviewTime')->eq(helper::now())->set('status')->eq('topublish')->set('dealUser')->eq($dealUser)->where('id')->eq($componentId)->exec();
                $this->loadModel('consumed')->record('component', $componentId, 0, $this->app->user->account, $component->status, 'topublish', $this->post->mailto,"");
                $actionID = $this->loadModel('action')->create('component', $componentId, 'reviewed', "审批结论：通过<br>操作备注：".$this->post->dealcomment);
            }else{
                $add = 3;
                //下一审核节点
                $nextReviewStage = $component->reviewStage + $add;
                /* //跳过的节点设置成ignore
                 if($nextReviewStage - $component->reviewStage > 1){
                     $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('ignore')->where('objectType')->eq('component')   //将跳过的节点，更新为ignore
                     ->andWhere('objectID')->eq($componentId)
                         ->andWhere('version')->eq($component->version)
                         ->andWhere('status')->eq('wait')->orderBy('stage,id')->limit($nextReviewStage - $component->reviewStage - 1)->exec();
                 }*/
                //下一审核状态
                if(isset($this->lang->component->reviewNodeList[$nextReviewStage])){
                    $status = $this->lang->component->reviewNodeStatusList[$nextReviewStage];
                }
                $this->dao->update(TABLE_COMPONENT)->set('reviewStage = reviewStage+' . $add)->set('status')->eq($status)->where('id')->eq($componentId)->exec();
                $this->loadModel('consumed')->record('component', $componentId, 0, $this->app->user->account, $component->status, $status, $this->post->mailto,"");
                $next = $this->dao->select('id')->from(TABLE_REVIEWNODE)->where('objectType')->eq('component')
                    ->andWhere('objectID')->eq($componentId)
                    ->andWhere('version')->eq($component->changeVersion)
                    ->andWhere('status')->eq('wait')->orderBy('stage,id')->fetch('id');
                if($next)
                {
                    $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('confirming')->where('id')->eq($next)->exec();
                    $this->dao->update(TABLE_REVIEWER)->set('status')->eq('confirming')->where('node')->eq($next)->exec();

                    $handle = $this->dao->select('reviewer')->from(TABLE_REVIEWER)->where('node')->eq($next)->fetch();
                    if(empty($handle)){
                        $this->dao->update(TABLE_COMPONENT)->set('dealUser')->eq('')->where('id')->eq($componentId)->exec();
                    }else{
                        $this->dao->update(TABLE_COMPONENT)->set('dealUser')->eq($handle->reviewer)->where('id')->eq($componentId)->exec();
                    }
                    $actionID = $this->loadModel('action')->create('component', $componentId, 'reviewed', "审批结论：通过<br>操作备注：".$this->post->dealcomment);
                }
            }
        }else if($result == 'reject') {

            $this->dao->update(TABLE_COMPONENT)->set('reviewStage')->eq('')->set('status')->eq('reject')->set('dealUser')->eq('')->set("finalstatetime")->eq(helper::now())->where('id')->eq($componentId)->exec();
            $this->loadModel('consumed')->record('component', $componentId, 0, $this->app->user->account, $component->status, 'reject', $this->post->mailto,"");
            $actionID = $this->loadModel('action')->create('component', $componentId, 'reviewed', "审批结论：不通过<br>不通过原因：".$this->post->rejectReason."<br>操作备注：".$this->post->dealcomment);
        }else if($result == 'appoint'){
            $add = 1;
            //下一审核节点
            $nextReviewStage = $component->reviewStage + $add;
            //下一审核状态
            if(isset($this->lang->component->reviewNodeList[$nextReviewStage])){
                $status = $this->lang->component->reviewNodeStatusList[$nextReviewStage];
            }
            //将评审人员和架构部确认状态修改为wait
            $teamMemverNode = $this->dao->select('id')->from(TABLE_REVIEWNODE)->where('objectType')->eq('component')
                ->andWhere('objectID')->eq($componentId)
                ->andWhere('version')->eq($component->changeVersion)
                ->andWhere('stage')->eq('3')->fetch('id');
            $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('wait')->where('id')->eq($teamMemverNode)->exec();
            $this->dao->update(TABLE_REVIEWER)->set('status')->eq('wait')->where('node')->eq($teamMemverNode)->exec();
            $architreviewNode = $this->dao->select('id')->from(TABLE_REVIEWNODE)->where('objectType')->eq('component')
                ->andWhere('objectID')->eq($componentId)
                ->andWhere('version')->eq($component->changeVersion)
                ->andWhere('stage')->eq('4')->fetch('id');
            $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('wait')->where('id')->eq($architreviewNode)->exec();
            $this->dao->update(TABLE_REVIEWER)->set('status')->eq('wait')->where('node')->eq($architreviewNode)->exec();

            //添加评审人员
            $teamMemverStr = trim(implode(",", $this->post->teamMember),',');
            $this->appointMember($componentId,$component->changeVersion,$teamMemverStr);

            $this->dao->update(TABLE_COMPONENT)->set('reviewStage = reviewStage+' . $add)->set('status')->eq($status)->where('id')->eq($componentId)->exec();
            $this->loadModel('consumed')->record('component', $componentId, 0, $this->app->user->account, $component->status, $status, $this->post->mailto,"");
            $next = $this->dao->select('id')->from(TABLE_REVIEWNODE)->where('objectType')->eq('component')
                ->andWhere('objectID')->eq($componentId)
                ->andWhere('version')->eq($component->changeVersion)
                ->andWhere('status')->eq('wait')->orderBy('stage,id')->fetch('id');
            if($next)
            {
                $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('pending')->where('id')->eq($next)->exec();
                $this->dao->update(TABLE_REVIEWER)->set('status')->eq('pending')->where('node')->eq($next)->exec();
                $reviewers = $this->loadModel('review')->getReviewer('component', $componentId, $component->changeVersion, $nextReviewStage);
                $this->dao->update(TABLE_COMPONENT)->set('dealUser')->eq($reviewers)->where('id')->eq($componentId)->exec();
                $actionID = $this->loadModel('action')->create('component', $componentId, 'reviewed', "审批结论：指派评审小组<br>操作备注：".$this->post->dealcomment);
            }
        }else if($result == 'incorporate'){
            $attachcomponent = $this->loadModel("componentpublic")->getSimpleByID($this->post->cid);
            if($component->level == 'dept'){
                $this->dao->update(TABLE_COMPONENT)->set('reviewTime')->eq(helper::now())->set('status')->eq('incorporate')->set('dealUser')->eq('')->set('cid')->eq($this->post->cid)->set('isattach')->eq(1)->set("finalstatetime")->eq(helper::now())->where('id')->eq($componentId)->exec();
                $this->loadModel('consumed')->record('component', $componentId, 0, $this->app->user->account, $component->status, 'incorporate', $this->post->mailto,"");
                $actionID = $this->loadModel('action')->create('component', $componentId, 'reviewed', "审批结论：纳入现有组件<br>组件名称：".$attachcomponent->name."<br>操作备注：".$this->post->dealcomment);
            }else{
                $add = 3;
                //下一审核节点
                $nextReviewStage = $component->reviewStage + $add;
                //下一审核状态
                if(isset($this->lang->component->reviewNodeList[$nextReviewStage])){
                    $status = $this->lang->component->reviewNodeStatusList[$nextReviewStage];
                }
                $this->dao->update(TABLE_COMPONENT)->set('reviewStage = reviewStage+' . $add)->set('status')->eq($status)->set('cid')->eq($this->post->cid)->set('isattach')->eq(1)->where('id')->eq($componentId)->exec();
                $this->loadModel('consumed')->record('component', $componentId, 0, $this->app->user->account, $component->status, $status, $this->post->mailto,"");
                $next = $this->dao->select('id')->from(TABLE_REVIEWNODE)->where('objectType')->eq('component')
                    ->andWhere('objectID')->eq($componentId)
                    ->andWhere('version')->eq($component->changeVersion)
                    ->andWhere('status')->eq('wait')->orderBy('stage,id')->fetch('id');
                if($next)
                {
                    $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('confirming')->where('id')->eq($next)->exec();
                    $this->dao->update(TABLE_REVIEWER)->set('status')->eq('confirming')->where('node')->eq($next)->exec();

                    $handle = $this->dao->select('reviewer')->from(TABLE_REVIEWER)->where('node')->eq($next)->fetch();
                    if(empty($handle)){
                        $this->dao->update(TABLE_COMPONENT)->set('dealUser')->eq('')->where('id')->eq($componentId)->exec();
                    }else{
                        $this->dao->update(TABLE_COMPONENT)->set('dealUser')->eq($handle->reviewer)->where('id')->eq($componentId)->exec();
                    }
                    if($result == 'incorporate'){
                        $actionID = $this->loadModel('action')->create('component', $componentId, 'reviewed', "审批结论：纳入现有组件<br>组件名称：".$attachcomponent->name."<br>操作备注：".$this->post->dealcomment);
                    }else{
                        $actionID = $this->loadModel('action')->create('component', $componentId, 'reviewed', "审批结论：纳入现有组件<br>操作备注：".$this->post->dealcomment);
                    }

                }
            }

        }
    }

    /**
     * 架构部确认和架构部领导确认
     * @param $componentId
     * @return void
     */
    public function architReview($componentId){
        $component = $this->getByID($componentId);
        //判断当前状态和审核中传入的状态是否一致，以防有同组中其他人修改
        $res = $this->checkAllowReview($component, $this->post->changeVersion, $this->post->reviewStage, $this->app->user->account);
        if(!$res['result']){
            dao::$errors['statusError'] = $res['message'];
        }
        $this->tryError();
        if(!$this->post->result){
            dao::$errors['result'] = sprintf($this->lang->component->emptyObject, $this->lang->component->result);
        }
        if($component->status == 'toarchitreview' && $this->post->result == 'reject' && !$this->post->rejectReason){
            dao::$errors['rejectReason'] = sprintf($this->lang->component->emptyObject, $this->lang->component->rejectReason);
        }
        if($this->post->result == 'incorporate' && !$this->post->cid){

            dao::$errors['cid'] = $this->lang->component->componetNOTEmpty;


        }
        $this->tryError();
        $is_all_check_pass = false;
        $extraObj = new stdclass();
        $extraObj->involved = $this->post->mailto;
        if($this->post->result == 'reject'){
            $extraObj->rejectReason = $this->post->rejectReason;
        }
        $this->dao->begin();
        $result = $this->check('component', $componentId, $component->changeVersion, $this->post->result, $this->post->dealcomment, '', $extraObj, $is_all_check_pass);
        if($component->status == 'toarchitleaderreview'){
            //架构部指定人员（架构部处理、架构部确认）节点人员
            $productManagerReviewer = array_keys($this->lang->component->productManagerReviewer);
            $data = new stdclass();
            if($this->post->result == 'pass'){
                $data->dealUser = implode(",", $productManagerReviewer);
                $data->status = 'topublish';
                $data->reviewTime = helper::now();
            }elseif($this->post->result == 'reject'){
                $data->status = 'reject';
                $data->dealUser = '';
                $data->finalstatetime = helper::now();
            }elseif($this->post->result == 'incorporate'){
                $data->status = 'incorporate';
                $data->dealUser = '';
                $data->finalstatetime = helper::now();
                $data->reviewTime = helper::now();
                $attachcomponent = $this->loadModel("componentpublic")->getSimpleByID($component->cid);

                if(!$attachcomponent){
                    dao::$errors['cid'] = $this->lang->component->componetNOTEmpty;
                    $this->dao->rollBack();
                    return false;
                }
            }
            $data->reviewStage = '';
            $this->dao->update(TABLE_COMPONENT)->data($data)->where('id')->eq($componentId)->exec();
            $mailtoArray = $this->post->mailto;
            if(empty($mailtoArray)){
                $mailtoArray = array();
            }
            $newMailtoArray = array_keys($this->lang->component->carbonCopyList);
            if($component->type == 'public'){
                $this->loadModel('consumed')->record('component', $componentId, 0, $this->app->user->account, $component->status, $data->status, array_merge($mailtoArray,$newMailtoArray),"");
            }else{
                $this->loadModel('consumed')->record('component', $componentId, 0, $this->app->user->account, $component->status, $data->status, $mailtoArray,"");
            }
            if($this->post->result == 'incorporate'){
                $actionID = $this->loadModel('action')->create('component', $componentId, 'reviewed', "审批结论：纳入现有组件<br />组件名称：".$attachcomponent->name);
            }else{
                $actionID = $this->loadModel('action')->create('component', $componentId, 'reviewed', $this->post->dealcomment);
            }

        }else if($component->status == 'toarchitreview'){

            if($this->post->result == "incorporate"){
                $attachcomponent = $this->loadModel("componentpublic")->getSimpleByID($this->post->cid);

                if(!$attachcomponent){
                    dao::$errors['cid'] = "关联组件错误";
                    $this->dao->rollBack();
                    return false;
                }
            }

            if($component->level == 'dept'){
                if($result == 'pass'){
                    $productManagerReviewer = array_keys($this->lang->component->productManagerReviewer);
                    $dealUser = implode(",", $productManagerReviewer);
                    $this->dao->update(TABLE_COMPONENT)->set('reviewTime')->eq(helper::now())->set('status')->eq('topublish')->set('dealUser')->eq($dealUser)->where('id')->eq($componentId)->exec();
                    $this->loadModel('consumed')->record('component', $componentId, 0, $this->app->user->account, $component->status, 'topublish', $this->post->mailto,"");
                    $actionID = $this->loadModel('action')->create('component', $componentId, 'reviewed', $this->post->dealcomment);
                }else if($result == 'incorporate'){
//                    $productManagerReviewer = array_keys($this->lang->component->productManagerReviewer);
//                    $dealUser = implode(",", $productManagerReviewer);
                    //部门级，纳入现有组件，流程结束
                    $dealUser = "";
                    $this->dao->update(TABLE_COMPONENT)->set('reviewTime')->eq(helper::now())->set('status')->eq('incorporate')->set('dealUser')->eq($dealUser)->set('cid')->eq($this->post->cid)->set('isattach')->eq(1)->where('id')->eq($componentId)->exec();
                    $this->loadModel('consumed')->record('component', $componentId, 0, $this->app->user->account, $component->status, 'incorporate', $this->post->mailto,"审批结论：纳入现有组件<br />组件名称：".$attachcomponent->name);
                    $actionID = $this->loadModel('action')->create('component', $componentId, 'reviewed', $this->post->dealcomment);
                }else if($result == 'reject'){
                    $this->dao->update(TABLE_COMPONENT)->set('status')->eq('reject')->set('dealUser')->eq('')->where('id')->eq($componentId)->exec();
                    $this->loadModel('consumed')->record('component', $componentId, 0, $this->app->user->account, $component->status, 'reject', $this->post->mailto,"");
                    $actionID = $this->loadModel('action')->create('component', $componentId, 'reviewed', "审批结论：不通过<br>不通过原因：".$this->post->rejectReason."<br>操作备注：".$this->post->dealcomment);
                }
            }else{
                $add = 1;
                //下一审核节点
                $nextReviewStage = $component->reviewStage + $add;
                //下一审核状态
                if(isset($this->lang->component->reviewNodeList[$nextReviewStage])){
                    $status = $this->lang->component->reviewNodeStatusList[$nextReviewStage];
                }
                if($this->post->result == "incorporate"){
                    $this->dao->update(TABLE_COMPONENT)->set('reviewStage = reviewStage+' . $add)->set('status')->eq($status)->set('cid')->eq($this->post->cid)->set('isattach')->eq(1)->where('id')->eq($componentId)->exec();
                }else{
                    $this->dao->update(TABLE_COMPONENT)->set('reviewStage = reviewStage+' . $add)->set('status')->eq($status)->where('id')->eq($componentId)->exec();
                }

                $this->loadModel('consumed')->record('component', $componentId, 0, $this->app->user->account, $component->status, $status, $this->post->mailto,"");
                $next = $this->dao->select('id')->from(TABLE_REVIEWNODE)->where('objectType')->eq('component')
                    ->andWhere('objectID')->eq($componentId)
                    ->andWhere('version')->eq($component->changeVersion)
                    ->andWhere('status')->eq('wait')->orderBy('stage,id')->fetch('id');
                if($next)
                {
                    $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('confirming')->where('id')->eq($next)->exec();
                    $this->dao->update(TABLE_REVIEWER)->set('status')->eq('confirming')->where('node')->eq($next)->exec();
                    $handle = $this->dao->select('reviewer')->from(TABLE_REVIEWER)->where('node')->eq($next)->fetch();
                    if(empty($handle)){
                        $this->dao->update(TABLE_COMPONENT)->set('dealUser')->eq('')->where('id')->eq($componentId)->exec();
                    }else{
                        $this->dao->update(TABLE_COMPONENT)->set('dealUser')->eq($handle->reviewer)->where('id')->eq($componentId)->exec();
                    }
                    if ($result == 'pass') {
                        $actionID = $this->loadModel('action')->create('component', $componentId, 'reviewed', $this->post->dealcomment);
                    }else if($result == 'reject') {
                        $actionID = $this->loadModel('action')->create('component', $componentId, 'reviewed', "审批结论：不通过<br>不通过原因：".$this->post->rejectReason."<br>操作备注：".$this->post->dealcomment);
                    }else if($result == 'incorporate'){
                        $actionID = $this->loadModel('action')->create('component', $componentId, 'reviewed', "审批结论：纳入现有组件<br />组件名称：".$attachcomponent->name);
                    }
                }
            }
        }
        //提交事务
        $this->dao->commit();
    }

    /**
     * 审批小组审批
     * @param $componentId
     * @return void
     */
    public function teamreviewReview($componentId){
        $component = $this->getByID($componentId);
        //判断当前状态和审核中传入的状态是否一致，以防有同组中其他人修改
        $res = $this->checkAllowReview($component, $this->post->changeVersion, $this->post->reviewStage, $this->app->user->account);
        if(!$res['result']){
            dao::$errors['statusError'] = $res['message'];
        }
        $this->tryError();
        if(!$this->post->result){
            dao::$errors['result'] = sprintf($this->lang->component->emptyObject, $this->lang->component->result);
        }
        if($this->post->result == 'reject' && !$this->post->rejectReason){
            dao::$errors['rejectReason'] = sprintf($this->lang->component->emptyObject, $this->lang->component->rejectReason);
        }
        $this->tryError();
        $extraObj = new stdclass();
        $extraObj->involved = $this->post->mailto;
        if($this->post->result == 'reject'){
            $extraObj->rejectReason = $this->post->rejectReason;
        }

        $result = $this->checkhalf('component', $componentId, $component->changeVersion, $this->post->result, $this->post->dealcomment, '', $extraObj);
        if($result == 'pass' || $result == 'reject')
        {
            $add = 1;
            //下一审核节点
            $nextReviewStage = $component->reviewStage + $add;
            //下一审核状态
            if(isset($this->lang->component->reviewNodeList[$nextReviewStage])){
                $status = $this->lang->component->reviewNodeStatusList[$nextReviewStage];
            }
            $this->dao->update(TABLE_COMPONENT)->set('reviewStage = reviewStage+' . $add)->set('status')->eq($status)->where('id')->eq($componentId)->exec();
            $this->loadModel('consumed')->record('component', $componentId, 0, $this->app->user->account, $component->status, $status, $this->post->mailto,"");
            $next = $this->dao->select('id')->from(TABLE_REVIEWNODE)->where('objectType')->eq('component')
                ->andWhere('objectID')->eq($componentId)
                ->andWhere('version')->eq($component->changeVersion)
                ->andWhere('status')->eq('wait')->orderBy('stage,id')->fetch('id');
            if($next)
            {
                $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('pending')->where('id')->eq($next)->exec();
                $this->dao->update(TABLE_REVIEWER)->set('status')->eq('pending')->where('node')->eq($next)->exec();

                $reviewers = $this->loadModel('review')->getReviewer('component', $componentId, $component->changeVersion, $nextReviewStage);
                $this->dao->update(TABLE_COMPONENT)->set('dealUser')->eq($reviewers)->where('id')->eq($componentId)->exec();
                if($this->post->result == 'reject'){
                    $actionID = $this->loadModel('action')->create('component', $componentId, 'reviewed', "审批结论：不通过"."<br>不通过原因：".$this->post->rejectReason."<br>操作备注：".$this->post->dealcomment);
                }else{
                    $actionID = $this->loadModel('action')->create('component', $componentId, 'reviewed', "审批结论：通过"."<br>操作备注：".$this->post->dealcomment);
                }
            }
        }else if($result == 'part'){
            $reviewers = $this->loadModel('review')->getReviewer('component', $componentId, $component->changeVersion, $component->reviewStage);
            $this->dao->update(TABLE_COMPONENT)->set('dealUser')->eq($reviewers)->where('id')->eq($componentId)->exec();
            if($this->post->result == 'reject'){
                $actionID = $this->loadModel('action')->create('component', $componentId, 'reviewed', "审批结论：不通过"."<br>不通过原因：".$this->post->rejectReason."<br>操作备注：".$this->post->dealcomment);
            }else{
                $actionID = $this->loadModel('action')->create('component', $componentId, 'reviewed', "审批结论：通过"."<br>操作备注：".$this->post->dealcomment);
            }
        }
    }

    /**
     * 检查用户审批
     * shixuyang
     * @param $component
     * @param $version
     * @param $reviewStage
     * @param $userAccount
     * @return array
     */
    public function checkAllowReview($component, $version = 1,  $reviewStage = 0, $userAccount = '')
    {
        $res = array(
            'result'  => false,
            'message' => '',
        );
        if(!$component){
            $res['message'] = $this->lang->common->errorParamId;
            return $res;
        }
        if(!$userAccount){
            $res['message'] = $this->lang->common->errorParamUser;
            return $res;
        }
        //审核节点已经经过
        if(($version != $component->changeVersion) || ($reviewStage != $component->reviewStage)){
            $reviewerInfo = $this->loadModel('review')->getReviewedUserInfo('component', $component->id, $version, $reviewStage);
            $message = $this->lang->review->statusError;
            if($reviewerInfo){
                $message = str_replace('%', $reviewerInfo->realname, $this->lang->review->statusError);
            }
            $res['message'] = $message;
            return $res;
        }
        //获得当前的的审核人信息
        $reviews =  $this->getReviewer('component', $component->id, $component->changeVersion, $component->reviewStage);
        if(!$reviews){
            $res['message'] = $this->lang->review->reviewEnd;
            return $res;
        }
        $reviews = explode(',', $reviews);
        if(!in_array($userAccount, $reviews)){
            $res['message'] = $this->lang->review->statusUserError;
            return $res;
        }
        $res['result'] = true;
        return  $res;
    }

    /**
     * 更新评审小组成员
     * @param $component
     * @param $version
     * @param $reviewStage
     * @param $member
     * @return void
     */
    public function appointMember($componentId, $version, $memberStr){
        $next = $this->dao->select('*')->from(TABLE_REVIEWNODE)->where('objectType')->eq('component')
            ->andWhere('objectID')->eq($componentId)
            ->andWhere('version')->eq($version)
            ->andWhere('stage')->eq('3')
            ->fetch();
        $reviewerList = $this->dao->select('*')->from(TABLE_REVIEWER)->where('node')->eq($next->id)->fetchAll('id');
        $memberArray = explode(",", $memberStr);
        $maxGradle = 0;
        foreach ($reviewerList as $reviewer){
            if($reviewer->grade >= $maxGradle){
                $maxGradle = $reviewer->grade;
            }
            if(in_array($reviewer->reviewer, $memberArray)){
                //如果修改前后一致保持不变
                $key = array_search($reviewer->reviewer, $memberArray);
                unset($memberArray[$key]);
            }else{
                //删除不存在成员的数据
                $this->dao->delete()->from(TABLE_REVIEWER)->where('id')->eq($reviewer->id)->exec();
            }
        }
        //添加数据
        foreach($memberArray as $member){
            if(!empty($member)){
                $user = new stdClass();
                $user->node        = $next->id;
                $user->status      = $next->status;
                $user->createdBy   = $this->app->user->account;
                $user->createdDate = helper::now();
                $maxGradle = $maxGradle+1;
                $user->grade = $maxGradle;
                $user->reviewer = $member;
                $this->dao->insert(TABLE_REVIEWER)->data($user)->exec();
            }
        }
    }

    /**
     * 审批人多数通过为通过
     * @param $objectType
     * @param $objectID
     * @param $version
     * @param $result
     * @param $comment
     * @param $stage
     * @param $extra
     * @param $is_all_check_pass
     * @return string
     */
    public function checkhalf($objectType, $objectID, $version, $result, $comment, $stage = '', $extra = null)
    {
        //查询是否有待审核的节点
        $node = $this->dao->select('*')->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq($objectType)
            ->andWhere('objectID')->eq($objectID)
            ->andWhere('version')->eq($version)
//            ->beginIF($stage != '')->andWhere('stage')->eq($stage)->fi()
            ->andWhere('status')->eq('pending')
            ->orderBy('stage,id')
            ->fetch();
        if(!$node) return '';

        $lastDealDate = helper::now();
        if(!$extra) $extra = new stdClass();
        //修改当前审核人的状态为操作状态
        $this->dao->update(TABLE_REVIEWER)
            ->set('status')->eq($result)
            ->set('comment')->eq($comment)
            ->set('extra')->eq(json_encode($extra))
            ->set('reviewTime')->eq($lastDealDate)
            ->where('node')->eq($node->id)
            ->andWhere('status')->eq('pending') //当前状态
            ->andWhere('reviewer')->eq($this->app->user->account) //当前审核人
            ->exec();
        //查询该节点下所有的审核人
        $reviews = $this->dao->select('*')->from(TABLE_REVIEWER)
            ->where('node')->eq($node->id)
            ->fetchAll();
        //如果审核结果是通过
        //默认需要全部审核通过
        $all = true;
        $allCount = count($reviews);
        $passCount = 0;
        foreach($reviews as $review)
        {
            if($review->status == 'pending')
            {
                $all = false;
                break;
            }
            if($review->status == 'pass'){
                $passCount = $passCount+1;
            }
        }
        //还有未审人员
        if(!$all) return 'part';
        //判断最终结果
        if($passCount*2 > $allCount){
            $finalResult = 'pass';
        }else{
            $finalResult = 'reject';
        }

        //修改节点审核状态
        $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq($finalResult)
            ->where('id')->eq($node->id)
            ->exec();
        return $finalResult;
    }

    /**
     * 检查评审小组是否评审完成
     * @param $objectType
     * @param $objectID
     * @param $version
     * @param $result
     * @param $comment
     * @param $stage
     * @param $extra
     * @param $is_all_check_pass
     * @return string
     */
    public function checkReview($objectType, $objectID, $version,  $extra = null)
    {
        //查询是否有待审核的节点
        $node = $this->dao->select('*')->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq($objectType)
            ->andWhere('objectID')->eq($objectID)
            ->andWhere('version')->eq($version)
//            ->beginIF($stage != '')->andWhere('stage')->eq($stage)->fi()
            ->andWhere('status')->eq('pending')
            ->orderBy('stage,id')
            ->fetch();
        if(!$node) return '';
        $lastDealDate = helper::now();
        if(!$extra) $extra = new stdClass();
        //查询该节点下所有的审核人
        $reviews = $this->dao->select('*')->from(TABLE_REVIEWER)
            ->where('node')->eq($node->id)
            ->fetchAll();
        //如果审核结果是通过
        //默认需要全部审核通过
        $all = true;
        $allCount = count($reviews);
        $passCount = 0;
        foreach($reviews as $review)
        {
            if($review->status == 'pending')
            {
                $all = false;
                break;
            }
            if($review->status == 'pass'){
                $passCount = $passCount+1;
            }
        }
        //还有未审人员
        if(!$all) return 'part';
        //判断最终结果
        if($passCount*2 > $allCount){
            $finalResult = 'pass';
        }else{
            $finalResult = 'reject';
        }

        //修改节点审核状态
        $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq($finalResult)
            ->where('id')->eq($node->id)
            ->exec();
        //审批流程往前走
        $component = $this->getByID($objectID);
        $add = 1;
        //下一审核节点
        $nextReviewStage = $component->reviewStage + $add;
        //下一审核状态
        if(isset($this->lang->component->reviewNodeList[$nextReviewStage])){
            $status = $this->lang->component->reviewNodeStatusList[$nextReviewStage];
        }
        $this->dao->update(TABLE_COMPONENT)->set('reviewStage = reviewStage+' . $add)->set('status')->eq($status)->where('id')->eq($objectID)->exec();
        $next = $this->dao->select('id')->from(TABLE_REVIEWNODE)->where('objectType')->eq('component')
            ->andWhere('objectID')->eq($objectID)
            ->andWhere('version')->eq($component->changeVersion)
            ->andWhere('status')->eq('wait')->orderBy('stage,id')->fetch('id');
        if($next)
        {
            $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('pending')->where('id')->eq($next)->exec();
            $this->dao->update(TABLE_REVIEWER)->set('status')->eq('pending')->where('node')->eq($next)->exec();

            $reviewers = $this->loadModel('review')->getReviewer('component', $componentId, $component->changeVersion, $nextReviewStage);
            $this->dao->update(TABLE_COMPONENT)->set('dealUser')->eq($reviewers)->where('id')->eq($componentId)->exec();
        }
        return $finalResult;
    }

    /**
     * Project: chengfangjinke
     * Desc:提交审核
     * liuyuhan
     */
    public function submit($componentID){
        $componentOld = $this->getByID($componentID);
        if($componentOld->status != 'tosubmit')
        {
            dao::$errors['statusError'] = $this->lang->component->statusError;
            return false;
        }
        // 部门负责人
        $myDept = $this->loadModel('dept')->getByID( $componentOld->createdDept);
        if(!$myDept){
            dao::$errors['deptmanagererror'] = $this->lang->component->mydeptManageisempty;
            return false;
        }

        $mydeptManager = explode(',', trim($myDept->manager, ','));

        //架构部指定人员（架构部处理、架构部确认）节点人员
        $productManagerReviewer = $this->lang->component->productManagerReviewer;
        //平台结构部领导
        $productManagerReviewerManager = $this->dao->select('id,manager1')->from(TABLE_DEPT)->where('name')->eq('平台架构部')->fetch();
        $pmrm = explode(',', trim($productManagerReviewerManager->manager1, ','));
        //新增各审批节点
        $this->loadModel('review');
        $changeVersion = $componentOld->changeVersion +1;
        $this->review->addNode('component', $componentID, $changeVersion, $mydeptManager, true, 'pending', 1,['nodeCode'=>'todepartreview']);
        $this->review->addNode('component', $componentID, $changeVersion, array_keys($productManagerReviewer), true, 'wait', 2,['nodeCode'=>'toappoint']);
        $this->review->addNode('component', $componentID, $changeVersion, array('teamreviewmember'), true, 'ignore', 3,['nodeCode'=>'toteamreview']);
        $this->review->addNode('component', $componentID, $changeVersion, array_keys($productManagerReviewer), true, 'ignore', 4,['nodeCode'=>'toarchitreview']);
        if($componentOld->level != 'dept'){
            $this->review->addNode('component', $componentID, $changeVersion, $pmrm, true, 'wait', 5,['nodeCode'=>'toarchitleaderreview']);
        }

        $this->tryError();
        //更新数据库
        $this->dao->update(TABLE_COMPONENT)
            ->set('status')->eq('todepartreview')
            ->set('editedBy')->eq($this->app->user->account)
            ->set('editedDate')->eq(helper::now())
            ->set('dealUser')->eq(implode(',',$mydeptManager))
            ->set('reviewStage')->eq('1')
            ->set('changeVersion')->eq($changeVersion)
            ->where('id')->eq($componentID)->exec();
        $this->tryError();
        if(!dao::isError()) {
            //状态流转
            $this->loadModel('consumed')->record('component', $componentID, '0', $this->app->user->account, $componentOld->status, 'todepartreview', array());
        }
        //获取新的数据
        $componentNew = $this->getByID($componentID);
        $this->tryError();
        return common::createChanges($componentOld, $componentNew);
    }

    /**
     * 发布组件
     * shixuyang
     * @param $componentID
     * @return array|false
     */
    public function publish($componentID){
        $componentOld = $this->getByID($componentID);
        if($componentOld->status != 'topublish')
        {
            dao::$errors['statusError'] = $this->lang->component->statusError;
            return false;
        }
        $postData = fixer::input('post')
            ->get();
        $updateData = new stdClass();
        $updateData->status = 'published';
        $updateData->dealUser = '';
        if($componentOld->type == 'thirdParty'){
            if(empty($postData->category) or $postData->category == ''){
                dao::$errors['category'] = sprintf($this->lang->component->emptyObject, $this->lang->component->category);
                return false;
            }

            /*if(empty($postData->chineseClassify) or $postData->chineseClassify == ''){
                dao::$errors['chineseClassify'] = sprintf($this->lang->component->emptyObject, $this->lang->component->chineseClassify);
                return false;
            }*/
            $postData->chineseClassify = '';
            if(empty($postData->englishClassify) or $postData->englishClassify == ''){
                dao::$errors['englishClassify'] = sprintf($this->lang->component->emptyObject, $this->lang->component->englishClassify);
                return false;
            }

            $updateData->category = $postData->category;
        }else if($componentOld->type == 'public'){
            if(empty($postData->category) or $postData->category == ''){
                dao::$errors['category'] = sprintf($this->lang->component->emptyObject, $this->lang->component->category);
                return false;
            }
            if(empty($postData->publishStatus) or $postData->publishStatus == ''){
                dao::$errors['publishStatus'] = sprintf($this->lang->component->emptyObject, $this->lang->component->publishStatus);
                return false;
            }
            if($postData->publishType == "incorporate"){
                if(!$postData->cid){
                    dao::$errors['cid'] = sprintf($this->lang->component->emptyObject, $this->lang->component->name);
                    return false;
                }
                $attachcomponent = $this->loadModel("componentpublic")->getSimpleByID($postData->cid);
                if(!$attachcomponent){
                    dao::$errors['cid'] = $this->lang->component->nowComponetNotExist;
                    return false;
                }
                $updateData->isattach = 1;
                $updateData->cid = $postData->cid;
                $updateData->status = 'incorporate';
            }
            $updateData->category = $postData->category;
            $updateData->publishStatus = $postData->publishStatus;
        }

        //增加发布的 终态找时间
        $updateData->finalstatetime = helper::now();
        //更新数据库
        $this->dao->update(TABLE_COMPONENT)
            ->data($updateData)
            ->where('id')->eq($componentID)->exec();
        $this->tryError();
        if(!dao::isError()) {
            if($componentOld->type == 'public' && $postData->publishType == "incorporate"){
                $this->loadModel('consumed')->record('component', $componentID, '0', $this->app->user->account, $componentOld->status, 'incorporate',array());
            }else{
                $this->loadModel('consumed')->record('component', $componentID, '0', $this->app->user->account, $componentOld->status, 'published',array());
            }

            //状态流转

            //$actionID = $this->loadModel('action')->create('component', $componentID, 'reviewed', $this->post->dealcomment);
            //同步到发布组件
            if($componentOld->type == 'public' && $postData->publishType != "incorporate"){
                $this->loadModel('componentpublic')->syncData($componentOld->id);
            }else if($componentOld->type == 'thirdParty'){
                $this->loadModel('componentthird')->syncData($componentOld->id,$postData->chineseClassify, $postData->englishClassify);
            }

        }
        //获取新的数据
        $componentNew = $this->getByID($componentID);
        $this->tryError();
        return common::createChanges($componentOld, $componentNew);
    }


    public function verifycomponent($id,$type='public'){
        return $this->dao->select('*')->from(TABLE_COMPONENT)
            ->where('id')->eq($id)
            ->andWhere("type")->eq($type)
            ->fetch();

    }

    /**
     * Project: chengfangjinke
     * Desc: 发送邮件
     * liuyuhan
     */
    public function sendmail($componentID, $actionID)
    {
        $this->loadModel('mail');
        //邮件显示详细信息
        $component = $this->getById($componentID);
        $projectPlanList = $this->loadModel('project')->getPairs();
        $depts = $this->loadModel('dept')->getOptionMenu();
        $users  = $this->loadModel('user')->getPairs('noletter|noclosed');
        $as = array();
        foreach(explode(',',trim($component->dealUser,',')) as $dealUser){
            $as[] = zget($users, $dealUser);
        }
        $component->dealUser = implode(',',$as);


        /* 获取后台通知中配置的邮件发信。*/
        $this->app->loadLang('custommail');
        $mailConf   = isset($this->config->global->setComponentMail) ? $this->config->global->setComponentMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf   = json_decode($mailConf);
        $browseType = 'component';

        /* 处理邮件发信的标题和日期。*/
        $bestDeal = $this->dao->select('*')->from(TABLE_CONSUMED)->where('objectType')->eq('component')
            ->andWhere('objectID')->eq($componentID)
            ->orderBy('id_desc')
            ->fetch();
        $bestDate=empty($bestDeal) ? '' : $bestDeal->createdDate;
//        $bestDate  = empty($bestDeal) ? '' : substr($bestDeal->createdDate, 0, 10);
        $mailTitle = vsprintf($mailConf->mailTitle, $mailConf->variables);

        /* Get action info. */
        $action          = $this->loadModel('action')->getById($actionID);
        $history         = $this->action->getHistory($actionID);
        $action->history = isset($history[$actionID]) ? $history[$actionID] : array();

        /* Get mail content. */
        $oldcwd     = getcwd();
        $modulePath = $this->app->getModulePath($appName = '', 'component');
        $viewFile   = $modulePath . 'view/sendmail.html.php';
        chdir($modulePath . 'view');

        if(file_exists($modulePath . 'ext/view/sendmail.html.php'))
        {
            $viewFile = $modulePath . 'ext/view/sendmail.html.php';
            chdir($modulePath . 'ext/view');
        }
        //$serverIp = $_SERVER['SERVER_NAME'];

        ob_start();
        include $viewFile;
        foreach(glob($modulePath . 'ext/view/sendmail.*.html.hook.php') as $hookFile) include $hookFile;
        $mailContent = ob_get_contents();
        ob_end_clean();
        chdir($oldcwd);

        $sendUsers = $this->getToAndCcList($component);
        if(!$sendUsers) return;
        list($toList, $ccList) = $sendUsers;
        /* 处理邮件标题。*/
        $subject = $mailTitle;

        /* Send emails. */
        $this->mail->send($toList, $subject, $mailContent, $ccList);
        if($this->mail->isError()) error_log(join("\n", $this->mail->getError()));
    }

    /**
     * Project: chengfangjinke
     * Desc: 获取收件人和抄送人列表
     * liuyuhan
     */
    public function getToAndCcList($component)
    {
        $ccList = '';
        if (in_array($component->status, array('published', 'reject','incorporate'))) {
//            $toList = $component->createdBy;
            // 部门负责人
            $myDept = $this->loadModel('dept')->getByID($component->createdDept);
//            $mydeptManager1 = implode(',',explode(',', trim($myDept->manager1, ',')));
            $toList = $component->createdBy . ',' . $myDept->manager;
            $ccList = $this->getSendMailCcList($component->id, 'component', $component->status, 0);
            if($component->level == 'dept'){
                $productManagerReviewerManager = $this->dao->select('id,manager1')->from(TABLE_DEPT)->where('name')->eq('平台架构部')->fetch();
                $pmrm = trim($productManagerReviewerManager->manager1, ',');
                $toList = trim($toList.','.$pmrm, ',');
            }
        } else if (in_array($component->status, array('todepartreview', 'toappoint', 'toteamreview', 'toarchitreview', 'toarchitleaderreview'))) {
            $toList = $this->review->getReviewer('component', $component->id, $component->changeVersion, $component->reviewStage);
            $ccList = $this->getSendMailCcList($component->id, 'component', $component->status, 0);
        } else if ($component->status == 'topublish') {
            //待发布状态发送给架构部指定人员
            $toList = implode(',', array_keys($this->lang->component->productManagerReviewer));
            $ccList = $this->getSendMailCcList($component->id, 'component', $component->status, 0);
        } else if ($component->status == 'tosubmit'){
            //若部门负责人不通过，则发送邮件给创建人
            $toList = $component->createdBy;
            $ccList = $this->getSendMailCcList($component->id, 'component', $component->status, 0);
         }
        return array($toList, $ccList);
    }

    /**
     * Project: chengfangjinke
     * Desc:重新指派评估小组成员
     * liuyuhan
     */
    public function changeteamreviewer($componentId){
        $component = $this->getByID($componentId);
        //判断当前状态和审核中传入的状态是否一致，以防有同组中其他人修改
//        $res = $this->checkAllowReview($component, $this->post->changeVersion, $this->post->reviewStage, $this->app->user->account);
//        if(!$res['result']){
//            dao::$errors['statusError'] = $res['message'];
//        }
        $this->tryError();
        //选择评审人员校验
        if(!$this->post->teamMember){
            dao::$errors['teamMember'] = sprintf($this->lang->component->emptyObject, $this->lang->component->teamMember);
        }else{
            $teamMemberStr = trim(implode(",", $this->post->teamMember),',');
            $teamMemberCount = count(explode(",",$teamMemberStr));
            if($teamMemberCount%2==0){
                dao::$errors['teamMember'] = $this->lang->component->teamMemberCountError;
            }
        }
        $this->tryError();

        //更改评审人员
        $teamMemverStr = trim(implode(",", $this->post->teamMember),',');
        $this->appointMember($componentId,$component->changeVersion,$teamMemverStr);
        //记录状态流转和抄送人员名单
        $this->loadModel('consumed')->record('component', $componentId, 0, $this->app->user->account, $component->status, $component->status, $this->post->mailto,"");
        $reviewers = $this->loadModel('review')->getReviewer('component', $componentId, $component->changeVersion, $component->reviewStage);
        $this->dao->update(TABLE_COMPONENT)->set('dealUser')->eq($reviewers)->where('id')->eq($componentId)->exec();
        $actionID = $this->loadModel('action')->create('component', $componentId, 'changeteamreviewer', "重新指派评估小组成员<br>操作备注：".$this->post->dealcomment);
        //检查当前评估小组成员评估状态
        $this->checkReview('component',$componentId,$component->changeVersion);
    }

    /**
     * Project: chengfangjinke
     * Desc: 获取邮件抄送人
     * liuyuhan
     */
    public function getSendMailCcList($objectID, $objectType, $before = '', $version = 0){
        $ccList = '';
        $detailList = $this->dao->select('*')->from(TABLE_CONSUMED)
            ->where('objectID')->eq($objectID)
            ->andWhere('objectType')->eq($objectType)
            ->beginIF(is_array($before))->andWhere('`after`')->in($before)->fi()
            ->beginIF(!is_array($before))->andWhere('`after`')->eq($before)->fi()
            ->beginIF($version > 0)->andWhere('`version`')->eq($version)->fi()
            ->fetchAll();
        if(!$detailList){
            return $ccList;
        }
        $list =  $detailList[count($detailList)-1];
        $ccList  = trim($list->mailto,',');
        return $ccList;
    }

    /**
     * Project: chengfangjinke
     * Desc: 获取审核人（pending && confirming状态）
     * liuyuhan
     */
    public function getReviewer($objectType, $objectID, $version = 1, $grade = 0, $extra = null)
    {
        $node = $this->dao->select('*')->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq($objectType)
            ->andWhere('objectID')->eq($objectID)
            ->andWhere('version')->eq($version)
            ->andWhere('status')->in('pending,confirming')
            ->orderBy('stage,id')
            ->fetch();
        if(!$node) return '';

        $reviews = $this->dao->select('reviewer')->from(TABLE_REVIEWER)
            ->where('node')->eq($node->id)
            ->andWhere('status')->in('pending,confirming')
            ->orderBy('id')
            ->fetchPairs();
        if(!$reviews) return '';

        $reviews = array_flip(array_flip($reviews));
        return join(',', $reviews);
    }


    public function check($objectType, $objectID, $version, $result, $comment, $stage = '', $extra = null, $is_all_check_pass = true,$isend=0)
    {
        //查询是否有待审核的节点
        $node = $this->dao->select('*')->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq($objectType)
            ->andWhere('objectID')->eq($objectID)
            ->andWhere('version')->eq($version)
//            ->beginIF($stage != '')->andWhere('stage')->eq($stage)->fi()
            ->andWhere('status')->in('pending,confirming')
            ->orderBy('stage,id')
            ->fetch();
        if(!$node) return '';

        //tangfei 增加审核时间
        $lastDealDate = helper::now();
        if(!$extra) $extra = new stdClass();
        //修改当前审核人的状态为操作状态
        $this->dao->update(TABLE_REVIEWER)
            ->set('status')->eq($result)
            ->set('comment')->eq($comment)
            ->set('extra')->eq(json_encode($extra))
            ->set('reviewTime')->eq($lastDealDate)
            ->where('node')->eq($node->id)
            ->andWhere('status')->in('pending,confirming') //当前状态
            ->andWhere('reviewer')->eq($this->app->user->account) //当前审核人
            ->exec();
        //查询该节点下所有的审核人
        $reviews = $this->dao->select('*')->from(TABLE_REVIEWER)
            ->where('node')->eq($node->id)
            ->fetchAll();
        //如果审核结果是通过
        if($result == 'pass' || $result == 'appoint' || $result == 'incorporate')
        {
            if($is_all_check_pass){ //需要全部审核通过
                //默认需要全部审核通过
                $all = true;
                foreach($reviews as $review)
                {
                    if($review->status == 'reject')
                    {
                        $all = false;
                        break;
                    }
                }
                //要求全部审核通过时才算真正审核通过，此时还有部分人未审核，不修改审核节点状态
                if(!$all) return 'part';
            }else{ //该节点一人审核通过即可
                $unCheckReviews = [];
                foreach ($reviews as $review) {
                    if ($review->status != 'pass' && $review->status != 'appoint' && $review->status != 'incorporate') {
                        $unCheckReviews[] = $review->id; //未审核的人
                    }
                }
                if($unCheckReviews){ //审核通过时，有一人审核通过即可，其他人不用审核
                    $this->dao->update(TABLE_REVIEWER)->set('status')->eq('ignore')
                        ->where('id')->in($unCheckReviews)
                        ->exec();
                }
            }
        }

        //修改节点审核状态
        $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq($result)
            ->where('id')->eq($node->id)
            ->exec();
        //审核状态是拒绝或者挂起
        if($result == 'reject' || $result == 'suspend')
        {
            // 如果拒绝了，当前和以后的节点涉及到的评审人都设为ignore，不需要评审了
            $nodes = $this->dao->select('*')->from(TABLE_REVIEWNODE)
                ->where('objectType')->eq($objectType)
                ->andWhere('objectID')->eq($objectID)
                ->andWhere('version')->eq($version)
                ->orderBy('stage,id')
                ->fetchAll();
            $ns = array();
            //拒绝并且走结束
            if($isend == 1 && $result == 'reject'){
                $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('ignore')
                    ->where('objectType')->eq($objectType)
                    ->andWhere('objectID')->eq($objectID)
                    ->andWhere('version')->eq($version)
                    ->andWhere('status')->in(array('wait', 'pending','confirming'))
                    ->exec();
            }
            foreach($nodes as $node) $ns[] = $node->id;
            if(!empty($ns)){
                $this->dao->update(TABLE_REVIEWER)->set('status')->eq('ignore')
                    ->where('node')->in($ns)
                    ->andWhere('status')->in(array('wait', 'pending'))
                    ->exec();
            }
        }
        return $result;
    }

    public function editcomment($reviewersID,$commentID)
    {
        $postData = fixer::input('post')
            ->stripTags($this->config->component->editor->editcomment['id'], $this->config->allowedTags)
            ->get();
        //富文本框处理
        $postData = $this->loadModel('file')->processImgURL($postData, $this->config->component->editor->editcomment['id'], $this->post->uid);
        $this->dao->update(TABLE_REVIEWER)->set('comment')->eq($postData->reviewOpinion)->where('id')->eq($reviewersID)->exec();
        $this->tryError();
        $actionID = $this->loadModel('action')->create('component', $commentID, 'editcomment', '');
    }

    //查询组件id与name
    public function getComponentPairs(){
        return $this->dao->select('id,name')->from(TABLE_COMPONENT)
            ->where('deleted')->eq('0')
            ->orderBy('id_desc')
            ->fetchPairs();
    }

    public function updatestatus($ID){
        $componet = $this->getSimpleByID($ID);
        $postData = fixer::input('post')->get();

        if(!$postData->status){
            dao::$errors['status'] = '参数异常';
            return false;
        }

        if(!in_array($componet->status,[$this->lang->component->statusKeyList['reject'],$this->lang->component->statusKeyList['tosubmit']])){
            dao::$errors['status'] = '不在 更新范围内';
            return false;
        }

        if($componet->status == $this->lang->component->statusKeyList['reject'] && $postData->status != $this->lang->component->statusKeyList['tosubmit']){
            dao::$errors['status'] = '必须选择 待提交';
            return false;

        }
        if($componet->status == $this->lang->component->statusKeyList['tosubmit'] && $postData->status != $this->lang->component->statusKeyList['reject']){

            dao::$errors['status'] = '必须选择 拒绝';
            return false;
        }
        $upData = [
            'status'=>$postData->status,
        ];

        if($postData->status == $this->lang->component->statusKeyList['reject']){
            $upData['finalstatetime'] = helper::now();
        }
        if($postData->status == $this->lang->component->statusKeyList['reject']){
            $upData['dealUser'] = '';
        }else if($postData->status == $this->lang->component->statusKeyList['tosubmit']){
            $upData['dealUser'] = $componet->createdBy;
        }

        $this->dao->update(TABLE_COMPONENT)->data($upData)->where('id')->eq($ID)->exec();

        $this->loadModel('consumed')->record('component', $ID, 0, $this->app->user->account, $componet->status, $postData->status, '',"");
        $newcomponet = $this->getSimpleByID($ID);

        return common::createNewChanges($componet,$newcomponet);

    }

}