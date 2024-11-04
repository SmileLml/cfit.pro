<?php
class componentpublicModel extends model
{
    /**
     * 公共技术组件同步
     * @param $componentId
     * @return void
     */
    public function syncData($componentId){
        $component = $this->loadModel('component')->getByID($componentId);
        if(!empty($component)){
            $componentpublic = new stdClass();
            $componentpublic->name = $component->name;
            $componentpublic->latestVersion = $component->version;
            $componentpublic->type = 'public';
            $componentpublic->level = $component->level;
            $componentpublic->category = $component->category;
            $componentpublic->developLanguage = $component->developLanguage;
            $componentpublic->status = $component->publishStatus;
            $componentpublic->maintainer = $component->maintainer;
            $componentpublic->maintainerDept = $component->createdDept;
            $componentpublic->functionDesc = $component->functionDesc;
            $componentpublic->location = $component->location;
            $componentpublic->componentId = $component->id;
            $componentpublic->gitlab = $component->gitlab;
            $componentpublic->publishTime = helper::now();
            $this->dao->insert(TABLE_COMPONENT_RELEASE)
                ->data($componentpublic)->autoCheck()
                ->exec();
            if(!dao::isError()){
                $componentpublicId = $this->dao->lastInsertId();
                $componentVersion = new stdClass();
                $componentVersion->version = $component->version;
                $componentVersion->updatedDate = helper::now();
                $componentVersion->componentReleaseId = $componentpublicId;
                $this->dao->insert(TABLE_COMPONENT_VERSION)
                    ->data($componentVersion)->autoCheck()
                    ->exec();
                $actionID = $this->loadModel('action')->create('componentpublic', $componentpublicId, 'publish', $this->post->comment);
            }
        }
    }

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
        $this->app->loadLang('component');
        $componentpublicQuery = '';
        if($browseType == 'bysearch')
        {
            $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : '';
            if($query)
            {
                $this->session->set('componentpublicQuery', $query->sql);
                $this->session->set('componentpublicForm', $query->form);
            }

            if($this->session->componentpublicQuery == false) $this->session->set('componentpublicQuery', ' 1 = 1');

            $componentpublicQuery = $this->session->componentpublicQuery;

        }

        $componentpublics = $this->dao->select('*')->from(TABLE_COMPONENT_RELEASE)
            ->where('deleted')->eq('0')->andWhere('type')->eq('public')
            ->beginIF($browseType != 'all' and $browseType != 'bysearch')->andWhere('level')->eq($browseType)->fi()
            ->beginIF($browseType == 'bysearch')->andWhere($componentpublicQuery)->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');
        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'componentpublic', $browseType != 'bysearch');
        //每个数据添加架构部处理人，方便按钮高亮
        //架构部指定人员（架构部处理、架构部确认）节点人员
        $productManagerReviewer = array_keys($this->lang->component->productManagerReviewer);
        //平台结构部领导
        $productManagerReviewerManager = $this->dao->select('id,manager1')->from(TABLE_DEPT)->where('name')->eq('平台架构部')->fetch();
        $pmrm = explode(',', trim($productManagerReviewerManager->manager1, ','));
        $pmrm = array_merge($productManagerReviewer, $pmrm);
        foreach ($componentpublics as $componentpublic){
            $componentpublic->pmrm = $pmrm;
            $componentpublicList = $this->dao->select('*')->from(TABLE_COMPONENT_PUBLIC_ACCOUNT)->where('deleted')->eq('0')->andWhere('componentId')->eq($componentpublic->id)->fetchAll();
            $componentpublic->usedNum = count($componentpublicList);
        }


        return $componentpublics;
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
        $this->config->componentpublic->search['actionURL'] = $actionURL;
        $this->config->componentpublic->search['queryID']   = $queryID;

        $this->loadModel('search')->setSearchParams($this->config->componentpublic->search);
    }

    /**
     * Project: chengfangjinke
     * Desc: 新建组件申请
     * shixuyang
     */
    public function create(){
        $postData = fixer::input('post')
            ->stripTags($this->config->componentpublic->editor->create['id'], $this->config->allowedTags)
            ->get();
        if(isset($postData->gitlab)){
            $postData->gitlab = json_encode($postData->gitlab,JSON_UNESCAPED_UNICODE);
        }else{
            $postData->gitlab = json_encode([]);
        }
        //富文本框处理
        $postData = $this->loadModel('file')->processImgURL($postData, $this->config->componentpublic->editor->create['id'], $this->post->uid);
        //进行申请数据的校验
        $this->filterApplicationForm($postData);

        //需要根据“组件名称”做判重验证
        $componentPublicName = $this->dao->select('name')->from(TABLE_COMPONENT_RELEASE)->where('name')->eq($postData->name)->fetch();
        $componentName = $this->dao->select('name')->from(TABLE_COMPONENT)->where('name')->eq($postData->name)->fetch();
        if (!empty($componentName) or !empty($componentPublicName)){
            dao::$errors['name'] =  sprintf($this->lang->componentpublic->nameRepeatError, $this->lang->componentpublic->name);
        }
        $this->tryError();
        $user = $this->loadModel('user')->getById($postData->maintainer);
        $postData->maintainerDept =  $user->dept;
        $postData->type = 'public';
        if($postData->status == 'publish'){
            $postData->publishTime = helper::now();
        }

        $this->dao->insert(TABLE_COMPONENT_RELEASE)
            ->data($postData)->autoCheck()
            ->exec();

        //存入数据库
        if(!dao::isError()){
            $lastId =  $this->dao->lastInsertID();

            $componentVersion = new stdClass();
            $componentVersion->version = $postData->latestVersion;
            $componentVersion->updatedDate = helper::now();
            $componentVersion->componentReleaseId = $lastId;
            $this->dao->insert(TABLE_COMPONENT_VERSION)
                ->data($componentVersion)->autoCheck()
                ->exec();
            //附件上传
            $this->loadModel('file')->updateObjectID($this->post->uid, $lastId, 'componentpublic');
            $this->loadModel('file')->saveUpload('componentpublic', $lastId);
            if(!dao::isError()) return $lastId;
        }
        return false;
    }

    /**
     * Project: chengfangjinke
     * Desc: 检查必填项是否为空
     * shixuyang
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
                $itemName = $this->lang->componentpublic->$item ?? $item;
                dao::$errors[$item] =  sprintf($this->lang->componentpublic->emptyObject, $itemName);
            }
        }
    }

    /**
     * Project: chengfangjinke
     * Desc: 进行申请数据的校验
     * shixuyang
     */
    private function filterBasicApplicationForm($data){
        //判断是否为中文
        $reg_licenseType = '/[\x80-\xff]/';
        $reg_version = $reg_licenseType;
        //第三方组件-新引入输入检查
        $this->checkParamsNotEmpty($data, $this->config->componentpublic->editinfo->requiredFields);
        if (preg_match($reg_version,$data->latestVersion)){
            dao::$errors['latestVersion'] =sprintf($this->lang->componentpublic->versionError,$this->lang->componentpublic->latestVersion);
        }
    }

    /**
     * Project: chengfangjinke
     * Desc: 进行申请数据的校验
     * shixuyang
     */
    private function filterBasicPmrmApplicationForm($data){
        //判断是否为中文
        $reg_licenseType = '/[\x80-\xff]/';
        $reg_version = $reg_licenseType;
        //第三方组件-新引入输入检查
        $this->checkParamsNotEmpty($data, $this->config->componentpublic->editpmrminfo->requiredFields);
        if (preg_match($reg_version,$data->latestVersion)){
            dao::$errors['latestVersion'] =sprintf($this->lang->componentpublic->versionError,$this->lang->componentpublic->latestVersion);
        }
    }

    /**
     * Project: chengfangjinke
     * Desc: 进行申请数据的校验
     * shixuyang
     */
    private function filterApplicationForm($data){
        //判断是否为中文
        $reg_licenseType = '/[\x80-\xff]/';
        $reg_version = $reg_licenseType;
        //第三方组件-新引入输入检查
        $this->checkParamsNotEmpty($data, $this->config->componentpublic->create->requiredFields);
        if (preg_match($reg_version,$data->latestVersion)){
            dao::$errors['latestVersion'] =sprintf($this->lang->componentpublic->versionError,$this->lang->componentpublic->latestVersion);
        }
    }

    /**
     * Project: chengfangjinke
     * Desc: 版本进行申请数据的校验
     * shixuyang
     */
    private function filterVersionApplicationForm($data){
        //判断是否为中文
        $reg_licenseType = '/[\x80-\xff]/';
        $reg_version = $reg_licenseType;
        //第三方组件-新引入输入检查
        $this->checkParamsNotEmpty($data, $this->config->componentpublic->createversion->requiredFields);
        if (preg_match($reg_version,$data->version)){
            dao::$errors['version'] =sprintf($this->lang->componentpublic->versionError,$this->lang->componentpublic->version);
        }
    }

    /**
     * Project: chengfangjinke
     * Desc: 版本进行编辑数据的校验
     * shixuyang
     */
    private function filterEditVersionApplicationForm($data){
        //第三方组件-新引入输入检查
        $this->checkParamsNotEmpty($data, $this->config->componentpublic->editversion->requiredFields);
    }

    /**
     * 获取单个数据
     * shixuyang
     * @param $requirementID
     * @param $version
     * @return mixed
     */
    public function getByID($componentpublicID)
    {
        $this->app->loadLang('component');
        $componentpublic = $this->dao->findByID($componentpublicID)->from(TABLE_COMPONENT_RELEASE)->fetch();
        $componentpublic = $this->loadModel('file')->replaceImgURL($componentpublic, 'functionDesc');
        //获取架构部领导和机构部处理人
        //架构部指定人员（架构部处理、架构部确认）节点人员
        $productManagerReviewer = array_keys($this->lang->component->productManagerReviewer);
        //平台结构部领导
        $productManagerReviewerManager = $this->dao->select('id,manager1')->from(TABLE_DEPT)->where('name')->eq('平台架构部')->fetch();
        $pmrm = explode(',', trim($productManagerReviewerManager->manager1, ','));
        $pmrm = array_merge($productManagerReviewer, $pmrm);
        $componentpublic->pmrm = $pmrm;

        return $componentpublic;
    }

    public function getSimpleByID($componentpublicID)
    {
        $this->app->loadLang('component');
        $componentpublic = $this->dao->findByID($componentpublicID)->from(TABLE_COMPONENT_RELEASE)->fetch();
        $componentpublic = $this->loadModel('file')->replaceImgURL($componentpublic, 'functionDesc');


        return $componentpublic;
    }



    /**
     * 编辑
     * shixuyang
     * @return false
     */
    public function update($componentpublicID = 0){
        $componentpublicOld = $this->getByID($componentpublicID);

        $postData = fixer::input('post')
            ->stripTags($this->config->componentpublic->editor->create['id'], $this->config->allowedTags)
            ->get();
        $postData->maintainerDept = $postData->hiddenMaintainerDept;
        unset($postData->hiddenMaintainerDept);
        if(isset($postData->gitlab)){
            $postData->gitlab = json_encode($postData->gitlab,JSON_UNESCAPED_UNICODE);
        }else{
            $postData->gitlab = json_encode([]);
        }
        //富文本框处理
        $postData = $this->loadModel('file')->processImgURL($postData, $this->config->componentpublic->editor->create['id'], $this->post->uid);

        //进行申请数据的校验
        $this->filterApplicationForm($postData);

        //需要根据“组件名称”做判重验证
        $componentPublicName = $this->dao->select('name')->from(TABLE_COMPONENT_RELEASE)->where('name')->eq($postData->name)->andWhere('id')->ne($componentpublicID)->fetch();
        $componentName = $this->dao->select('name')->from(TABLE_COMPONENT)->where('name')->eq($postData->name)->andWhere('id')->ne($componentpublicOld->componentId)->fetch();
        if (!empty($componentName) or !empty($componentPublicName)){
            dao::$errors['name'] =  sprintf($this->lang->componentpublic->nameRepeatError, $this->lang->componentpublic->name);
        }
        if($postData->status == 'publish' && (!$postData->publishTime || $postData->publishTime == '0000-00-00 00:00:00')){
            $postData->publishTime = helper::now();
        }
        $this->tryError();
        //存入数据库
        $this->dao->update(TABLE_COMPONENT_RELEASE)->data($postData)->autoCheck()
            ->where('id')->eq($componentpublicID)
            ->exec();
        if(!dao::isError()){
            $versionName = $this->dao->select('version')->from(TABLE_COMPONENT_VERSION)->where('version')->eq($postData->latestVersion)->andWhere('componentReleaseId')->eq($componentpublicID)->fetch();
            if(empty($versionName)){
                $componentVersion = new stdClass();
                $componentVersion->version = $postData->latestVersion;
                $componentVersion->updatedDate = helper::now();
                $componentVersion->componentReleaseId = $componentpublicID;
                $this->dao->insert(TABLE_COMPONENT_VERSION)
                    ->data($componentVersion)->autoCheck()
                    ->exec();
            }

            //附件上传
            $this->loadModel('file')->updateObjectID($this->post->uid, $componentpublicID, 'componentpublic');
            $this->loadModel('file')->saveUpload('componentpublic', $componentpublicID);
        }
        //获取新的数据
        $componentpublicNew = $this->getByID($componentpublicID);
        $this->tryError();
        return common::createChanges($componentpublicOld, $componentpublicNew);
    }

    /**
     * 编辑基础信息
     * shixuyang
     * @return false
     */
    public function updateinfo($componentpublicID = 0){
        $componentpublicOld = $this->getByID($componentpublicID);

        $postData = fixer::input('post')
            ->stripTags($this->config->componentpublic->editor->editinfo['id'], $this->config->allowedTags)
            ->get();
        $postData->maintainerDept = $postData->hiddenMaintainerDept;
        unset($postData->hiddenMaintainerDept);
        if(isset($postData->gitlab)){
            $postData->gitlab = json_encode($postData->gitlab,JSON_UNESCAPED_UNICODE);
        }else{
            $postData->gitlab = json_encode([]);
        }
        //富文本框处理
        $postData = $this->loadModel('file')->processImgURL($postData, $this->config->componentpublic->editor->editinfo['id'], $this->post->uid);

        //进行申请数据的校验
        if(in_array($this->app->user->account, $componentpublicOld->pmrm)){
            $this->filterBasicPmrmApplicationForm($postData);
            $componentPublicName = $this->dao->select('name')->from(TABLE_COMPONENT_RELEASE)->where('name')->eq($postData->name)->andWhere('id')->ne($componentpublicID)->fetch();
            $componentName = $this->dao->select('name')->from(TABLE_COMPONENT)->where('name')->eq($postData->name)->andWhere('id')->ne($componentpublicOld->componentId)->fetch();
            if (!empty($componentName) or !empty($componentPublicName)){
                dao::$errors['name'] =  sprintf($this->lang->componentpublic->nameRepeatError, $this->lang->componentpublic->name);
            }
        }else{
            $this->filterBasicApplicationForm($postData);
        }
        if($postData->status == 'publish' && (!$postData->publishTime || $postData->publishTime == '0000-00-00 00:00:00')){
            $postData->publishTime = helper::now();
        }

        $this->tryError();
        //存入数据库
        $this->dao->update(TABLE_COMPONENT_RELEASE)->data($postData)->autoCheck()
            ->where('id')->eq($componentpublicID)
            ->exec();
        if(!dao::isError()){
            //附件上传
            $this->loadModel('file')->updateObjectID($this->post->uid, $componentpublicID, 'componentpublic');
            $this->loadModel('file')->saveUpload('componentpublic', $componentpublicID);
        }
        //获取新的数据
        $componentpublicNew = $this->getByID($componentpublicID);
        $this->tryError();
        return common::createChanges($componentpublicOld, $componentpublicNew);
    }

    /**
     * Project: chengfangjinke
     * Desc: 新建组件版本
     * shixuyang
     */
    public function createversion($componentpublicID){
        $postData = fixer::input('post')
            ->add('componentReleaseId', $componentpublicID)
            ->remove('files')
            ->stripTags($this->config->componentpublic->editor->createversion['id'], $this->config->allowedTags)
            ->get();
        //富文本框处理
        $postData = $this->loadModel('file')->processImgURL($postData, $this->config->componentpublic->editor->createversion['id'], $this->post->uid);
        //进行申请数据的校验
        $this->filterVersionApplicationForm($postData);

        //需要根据“组件名称”做判重验证
        $versionName = $this->dao->select('version')->from(TABLE_COMPONENT_VERSION)->where('version')->eq($postData->version)->andWhere('componentReleaseId')->eq($postData->componentReleaseId)->fetch();
        if (!empty($versionName)){
            dao::$errors['version'] =  $this->lang->componentpublic->versionRepeatError;
        }
        $this->tryError();
        $this->dao->insert(TABLE_COMPONENT_VERSION)
            ->data($postData)->autoCheck()
            ->exec();

        //存入数据库
        if(!dao::isError()){
            $lastId =  $this->dao->lastInsertID();
            //附件上传
            $this->loadModel('file')->updateObjectID($this->post->uid, $lastId, 'componentversion');
            $this->loadModel('file')->saveUpload('componentversion', $lastId);
            if(!dao::isError()) return $lastId;
        }
        return false;
    }

    /**
     * Project: chengfangjinke
     * Desc: 编辑组件版本
     * shixuyang
     */
    public function editversion($versionID){
        $versionOld = $this->getVersionByID($versionID);
        $postData = fixer::input('post')
            ->remove('files')
            ->stripTags($this->config->componentpublic->editor->createversion['id'], $this->config->allowedTags)
            ->get();
        //富文本框处理
        $postData = $this->loadModel('file')->processImgURL($postData, $this->config->componentpublic->editor->createversion['id'], $this->post->uid);
        //进行申请数据的校验
        $this->filterEditVersionApplicationForm($postData);

        $this->tryError();
        $this->dao->update(TABLE_COMPONENT_VERSION)
            ->data($postData)->autoCheck()
            ->where('id')->eq($versionID)
            ->exec();

        //存入数据库
        if(!dao::isError()){
            //附件上传
            $this->loadModel('file')->updateObjectID($this->post->uid, $versionID, 'componentversion');
            $this->loadModel('file')->saveUpload('componentversion', $versionID);
        }
        //获取新的数据
        $versionNew = $this->getVersionByID($versionID);
        $this->tryError();
        return common::createChanges($versionOld, $versionNew);
    }

    /**
     * Project: chengfangjinke
     * Desc: 获取版本
     * shixuyang
     */
    public function getVersions($componentpublicID){
        $versionList = $this->dao->select('*')->from(TABLE_COMPONENT_VERSION)->where('componentReleaseId')->eq($componentpublicID)->andWhere('deleted')->eq('0')->orderby("updatedDate desc")->fetchAll('id');
        $i = 0;
        foreach ($versionList as $version){
            $i = $i+1;
            $version->code = $i;
            $version->files = $this->loadModel('file')->getByObject('componentversion', $version->id);
            $version->updatedDate = date("Y-m-d", strtotime($version->updatedDate));
//            $componentpublicList = $this->dao->select('*')->from(TABLE_COMPONENT_ACCOUNT)->where('deleted')->eq('0')->andWhere('componentVersionId')->eq($version->id)->fetchAll();
            $componentpublicList = $this->dao->select('*')->from(TABLE_COMPONENT_PUBLIC_ACCOUNT)->where('deleted')->eq('0')->andWhere('componentId')->eq($componentpublicID)->andWhere('componentVersion')->eq($version->id)->fetchAll();
            $version->usedNum = count($componentpublicList);
        }
        return  $versionList;
    }

    /**
     * 获取版本单个数据
     * shixuyang
     * @param $requirementID
     * @param $version
     * @return mixed
     */
    public function getVersionByID($versionID)
    {
        $version = $this->dao->findByID($versionID)->from(TABLE_COMPONENT_VERSION)->fetch();
        $version = $this->loadModel('file')->replaceImgURL($version, 'desc');
        $version->files = $this->loadModel('file')->getByObject('componentversion', $versionID);
        $version->updatedDate = date("Y-m-d", strtotime($version->updatedDate));
        $componentpublicList = $this->dao->select('*')->from(TABLE_COMPONENT_ACCOUNT)->where('deleted')->eq('0')->andWhere('componentVersionId')->eq($version->id)->fetchAll();
        $version->usedNum = count($componentpublicList);
        return $version;
    }

    /**
     * 按钮权限控制
     * @param
     * @param $action
     * @return bool
     */
    public static function isClickable($componentpublic, $action)
    {
        global $app;
        $action = strtolower($action);

        if($action == 'edit') return in_array($app->user->account,$componentpublic->pmrm);
        if($action == 'editinfo') return ($app->user->account == $componentpublic->maintainer or in_array($app->user->account,$componentpublic->pmrm));
        if($action == 'createversion') return ($app->user->account == $componentpublic->maintainer or in_array($app->user->account,$componentpublic->pmrm));
        if($action == 'editversion') return ($app->user->account == $componentpublic->maintainer or in_array($app->user->account,$componentpublic->pmrm));
        if($action == 'deleteversion') return ($app->user->account == $componentpublic->maintainer or in_array($app->user->account,$componentpublic->pmrm));
        if($action == 'delete') return ($app->user->account == $componentpublic->maintainer or in_array($app->user->account,$componentpublic->pmrm));
        return true;
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
     * 公共组件下拉框
     * @param $programID
     * @return mixed
     */
    public function getPairs()
    {
        return $this->dao->select('id,name')->from(TABLE_COMPONENT_RELEASE)
            ->where('deleted')->eq(0)
            ->andWhere('type')->eq('public')
            ->orderBy('id_desc')
            ->fetchPairs();
    }
    /**
     * 公共组件下拉框 根据维护部门筛选
     * @param $programID
     * @return mixed
     */
    public function getByDeptPairs($deptID)
    {
        return $this->dao->select('id,name')->from(TABLE_COMPONENT_RELEASE)
            ->where('deleted')->eq(0)
            ->andWhere('type')->eq('public')
            ->andWhere('maintainerDept')->eq($deptID)
            ->orderBy('id_desc')
            ->fetchPairs();
    }

    /**
     * Project: chengfangjinke
     * Desc: 获取版本
     *
     */
    public function getNewVersionPairs($componentpublicID){
        $versionList = $this->dao->select('id, version')->from(TABLE_COMPONENT_VERSION)->where('componentReleaseId')->eq($componentpublicID)->andWhere('deleted')->eq('0')->fetchPairs();
        return  $versionList;
    }
    public function getAllVersionPairs(){
        $versionList = $this->dao->select('id, version')->from(TABLE_COMPONENT_VERSION)->where('deleted')->eq('0')->fetchPairs();
        return  $versionList;
    }

    /**
     * Project: chengfangjinke
     * Desc: 获取版本
     * shixuyang
     */
    public function getVersionPairs($componentpublicID){
        $versionList = $this->dao->select('version, version')->from(TABLE_COMPONENT_VERSION)->where('componentReleaseId')->eq($componentpublicID)->andWhere('deleted')->eq('0')->fetchPairs();
        return  $versionList;
    }

    public function getPublicComponetList(){

        $publicCompnetList = $this->dao->select('id,name')->from(TABLE_COMPONENT_RELEASE)
            ->where('type')->eq('public')
            ->andWhere('deleted')->eq(0)
//            ->andWhere('status')->eq('published')
            ->orderBy('id desc')
            ->fetchAll('id');
        $publicCompnetList = array_column($publicCompnetList,'name','id');
        return $publicCompnetList;
    }
}