<?php
class componentthirdModel extends model
{
    /**
     * 第三方组件同步
     * @param $componentId
     * @return void
     */
    public function syncData($componentId, $chineseClassify, $englishClassify){
        $component = $this->loadModel('component')->getByID($componentId);
        if(!empty($component)){
            $componentthird = new stdClass();
            $componentthird->name = $component->name;
            $componentthird->type = 'third';
            $componentthird->category = $component->category;
            $componentthird->developLanguage = $component->developLanguage;
            $componentthird->status = $component->publishStatus;
            $componentthird->licenseType = $component->licenseType;
            $componentthird->componentId = $component->id;
            $componentthird->chineseClassify = $chineseClassify;
            $componentthird->englishClassify = $englishClassify;
            $this->dao->insert(TABLE_COMPONENT_RELEASE)
                ->data($componentthird)->autoCheck()
                ->exec();
            if(!dao::isError()){
                $componentthirdId = $this->dao->lastInsertId();
                $componentVersion = new stdClass();
                $componentVersion->version = $component->version;
                $componentVersion->updatedDate = helper::now();
                $componentVersion->componentReleaseId = $componentthirdId;
                $this->dao->insert(TABLE_COMPONENT_VERSION)
                    ->data($componentVersion)->autoCheck()
                    ->exec();
                $actionID = $this->loadModel('action')->create('componentthird', $componentthirdId, 'publish', $this->post->comment);
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
        $componentthirdQuery = '';
        if($browseType == 'bysearch')
        {
            $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : '';
            if($query)
            {
                $this->session->set('componentthirdQuery', $query->sql);
                $this->session->set('componentthirdForm', $query->form);
            }

            if($this->session->componentthirdQuery == false) $this->session->set('componentthirdQuery', ' 1 = 1');

            $componentthirdQuery = $this->session->componentthirdQuery;

            $componentthirdQuery = str_replace('AND `', ' AND `t1.', $componentthirdQuery);
            $componentthirdQuery = str_replace('AND (`', ' AND (`t1.', $componentthirdQuery);

            $componentthirdQuery = str_replace('OR `', ' OR `t1.', $componentthirdQuery);
            $componentthirdQuery = str_replace('OR (`', ' OR (`t1.', $componentthirdQuery);

            $componentthirdQuery = str_replace('`', '', $componentthirdQuery);

            if(strpos($componentthirdQuery, 'recommendVersion') !== false)
            {
                $componentthirdQuery = str_replace('t1.recommendVersion', "t2.version", $componentthirdQuery);
            }

            if(strpos($componentthirdQuery, 'versionDate') !== false)
            {
                $componentthirdQuery = str_replace('t1.versionDate', "t2.updatedDate", $componentthirdQuery);
            }
        }
        if(strpos($orderBy, 'versionDate') !== false){
            $orderBy = str_replace('versionDate', "t2.updatedDate", $orderBy);
        }else if(strpos($orderBy, 'recommendVersion') !== false){
            $orderBy = str_replace('recommendVersion', "t2.version", $orderBy);
        }else{
            $orderBy = 't1.'.$orderBy;
        }

        $componentthirds = $this->dao->select('t1.*')->from(TABLE_COMPONENT_RELEASE)->alias('t1')
            ->leftJoin(TABLE_COMPONENT_VERSION)->alias('t2')
            ->on('t1.recommendVersion = t2.id')
            ->where('t1.deleted')->eq('0')->andWhere('t1.type')->eq('third')
            ->beginIF($browseType != 'all' and $browseType != 'bysearch')->andWhere('t1.category')->eq($browseType)->fi()
            ->beginIF($browseType == 'bysearch')->andWhere($componentthirdQuery)->fi()
            ->orderBy($orderBy.", t1.id_desc")
            ->page($pager,'t1.id')
            ->fetchAll('id');
        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'componentthird', $browseType != 'bysearch');
        //每个数据添加架构部处理人，方便按钮高亮
        //架构部指定人员（架构部处理、架构部确认）节点人员
        $productManagerReviewer = array_keys($this->lang->component->productManagerReviewer);
        //平台结构部领导
        $productManagerReviewerManager = $this->dao->select('id,manager1')->from(TABLE_DEPT)->where('name')->eq('平台架构部')->fetch();
        $pmrm = explode(',', trim($productManagerReviewerManager->manager1, ','));
        $pmrm = array_merge($productManagerReviewer, $pmrm);
        foreach ($componentthirds as $componentthird){
            $componentthird->pmrm = $pmrm;
            $version = $this->getVersionByID($componentthird->recommendVersion);
            if(!empty($version)){
                $componentthird->recommendVersion = $version->version;
                $componentthird->versionDate = date("Y-m-d", strtotime($version->updatedDate));
            }else{
                $version = $this->getVersionByName($componentthird->recommendVersion, $componentthird->id);
                if(!empty($version)){
                    $componentthird->recommendVersion = $version->version;
                    $componentthird->recommendVersionId = $version->id;
                    $componentthird->versionDate = date("Y-m-d", strtotime($version->updatedDate));
                }else{
                    $componentthird->recommendVersion = $componentthird->recommendVersion;
                    $componentthird->versionDate = '';
                    $componentthird->recommendVersionId = $componentthird->recommendVersion;
                }
            }
            $componentthirdList = $this->dao->select('*')->from(TABLE_COMPONENT_ACCOUNT)->where('deleted')->eq('0')->andWhere('componentReleaseId')->eq($componentthird->id)->fetchAll();
            $componentthird->usedNum = count($componentthirdList);
        }


        return $componentthirds;
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
        $this->config->componentthird->search['actionURL'] = $actionURL;
        $this->config->componentthird->search['queryID']   = $queryID;

        $this->loadModel('search')->setSearchParams($this->config->componentthird->search);
    }

    /**
     * Project: chengfangjinke
     * Desc: 新建第三方组件
     * shixuyang
     */
    public function create(){
        $postData = fixer::input('post')
            ->get();

        //进行申请数据的校验
        $this->checkParamsNotEmpty($postData, $this->config->componentthird->create->requiredFields);

        //需要根据“组件名称”做判重验证
        $componentthirdName = $this->dao->select('name')->from(TABLE_COMPONENT_RELEASE)->where('name')->eq($postData->name)->fetch();
        $componentName = $this->dao->select('name')->from(TABLE_COMPONENT)->where('name')->eq($postData->name)->fetch();
        if (!empty($componentName) or !empty($componentthirdName)){
            dao::$errors['name'] =  sprintf($this->lang->componentthird->nameRepeatError, $this->lang->componentthird->name);
        }
        $this->tryError();

        $postData->type = 'third';

        $this->dao->insert(TABLE_COMPONENT_RELEASE)
            ->data($postData)->autoCheck()
            ->exec();

        //存入数据库
        if(!dao::isError()){
            $lastId =  $this->dao->lastInsertID();
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
        $reg_licenseType = '/[\x80-\xff]/';
        if(!is_array($data)) {
            if (!is_object($data)) $data = (object)$data;
            $data = (array)$data;
        }
        $fieldArray = explode(',', str_replace(' ', '', $fields));
        foreach ($fieldArray as $item)
        {
            if(is_null($data[$item]) || $data[$item] == ''){
                $itemName = $this->lang->componentthird->$item ?? $item;
                dao::$errors[$item] =  sprintf($this->lang->componentthird->emptyObject, $itemName);
            }
        }
        if (preg_match($reg_licenseType,$data['licenseType'])){
            dao::$errors['licenseType'] =$this->lang->componentthird->licenseTypeError;
        }
    }

    /**
     * Project: chengfangjinke
     * Desc: 检查必填项是否为空
     * shixuyang
     */
    private function checkVersionParamsNotEmpty($data, $fields)
    {
        $reg_licenseType = '/[\x80-\xff]/';
        if(!is_array($data)) {
            if (!is_object($data)) $data = (object)$data;
            $data = (array)$data;
        }
        $fieldArray = explode(',', str_replace(' ', '', $fields));
        foreach ($fieldArray as $item)
        {
            if(is_null($data[$item]) || $data[$item] == ''){
                $itemName = $this->lang->componentthird->$item ?? $item;
                dao::$errors[$item] =  sprintf($this->lang->componentthird->emptyObject, $itemName);
            }
        }
    }

    /**
     * 获取单个数据
     * shixuyang
     * @param $requirementID
     * @param $version
     * @return mixed
     */
    public function getByID($componentthirdID)
    {
        $this->app->loadLang('component');
        $componentthird = $this->dao->findByID($componentthirdID)->from(TABLE_COMPONENT_RELEASE)->fetch();
        //获取架构部领导和机构部处理人
        //架构部指定人员（架构部处理、架构部确认）节点人员
        $productManagerReviewer = array_keys($this->lang->component->productManagerReviewer);
        //平台结构部领导
        $productManagerReviewerManager = $this->dao->select('id,manager1')->from(TABLE_DEPT)->where('name')->eq('平台架构部')->fetch();
        $pmrm = explode(',', trim($productManagerReviewerManager->manager1, ','));
        $pmrm = array_merge($productManagerReviewer, $pmrm);
        $componentthird->pmrm = $pmrm;
        $version = $this->getVersionByID($componentthird->recommendVersion);
        if(!empty($version)){
            $componentthird->recommendVersion = $version->version;
            $componentthird->recommendVersionId = $version->id;
            $componentthird->versionDate = date("Y-m-d", strtotime($version->updatedDate));
        }else{
            $version = $this->getVersionByName($componentthird->recommendVersion, $componentthirdID);
            if(!empty($version)){
                $componentthird->recommendVersion = $version->version;
                $componentthird->recommendVersionId = $version->id;
                $componentthird->versionDate = date("Y-m-d", strtotime($version->updatedDate));
            }else{
                $componentthird->recommendVersion = $componentthird->recommendVersion;
                $componentthird->versionDate = '';
                $componentthird->recommendVersionId = $componentthird->recommendVersion;
            }
        }

        return $componentthird;
    }

    /**
     * 编辑
     * shixuyang
     * @return false
     */
    public function update($componentthirdID = 0){
        $componentthirdOld = $this->getByID($componentthirdID);

        $postData = fixer::input('post')
            ->get();

        //进行申请数据的校验
        $this->checkParamsNotEmpty($postData, $this->config->componentthird->create->requiredFields);

        //需要根据“组件名称”做判重验证
        $componentthirdName = $this->dao->select('name')->from(TABLE_COMPONENT_RELEASE)->where('name')->eq($postData->name)->andWhere('id')->ne($componentthirdID)->fetch();
        $componentName = $this->dao->select('name')->from(TABLE_COMPONENT)->where('name')->eq($postData->name)->andWhere('id')->ne($componentthirdOld->componentId)->fetch();
        if (!empty($componentName) or !empty($componentthirdName)){
            dao::$errors['name'] =  sprintf($this->lang->componentthird->nameRepeatError, $this->lang->componentthird->name);
        }

        $this->tryError();
        //存入数据库
        $this->dao->update(TABLE_COMPONENT_RELEASE)->data($postData)->autoCheck()
            ->where('id')->eq($componentthirdID)
            ->exec();

        //获取新的数据
        $componentthirdNew = $this->getByID($componentthirdID);
        $this->tryError();
        return common::createChanges($componentthirdOld, $componentthirdNew);
    }

    /**
     * Project: chengfangjinke
     * Desc: 获取版本
     * shixuyang
     */
    public function getVersions($componentthirdID){
        $versionList = $this->dao->select('*')->from(TABLE_COMPONENT_VERSION)->where('componentReleaseId')->eq($componentthirdID)->andWhere('deleted')->eq('0')->orderby("updatedDate desc")->fetchAll('id');
        $i = 0;
        foreach ($versionList as $version){
            $i = $i+1;
            $version->code = $i;
            $version->files = $this->loadModel('file')->getByObject('componentversion', $version->id);
            $version->updatedDate = date("Y-m-d", strtotime($version->updatedDate));
            $componentthirdList = $this->dao->select('*')->from(TABLE_COMPONENT_ACCOUNT)->where('deleted')->eq('0')->andWhere('componentVersionId')->eq($version->id)->fetchAll();
            $version->usedNum = count($componentthirdList);
        }
        return  $versionList;
    }

    /**
     * Project: chengfangjinke
     * Desc: 获取版本
     * shixuyang
     */
    public function getVersionPairs($componentthirdID){
        $versionList = $this->dao->select('id, version')->from(TABLE_COMPONENT_VERSION)->where('componentReleaseId')->eq($componentthirdID)->andWhere('deleted')->eq('0')->fetchPairs();
        return  $versionList;
    }

    /**
     * 编辑基础信息
     * shixuyang
     * @return false
     */
    public function updateinfo($componentthirdID = 0){
        $componentthirdOld = $this->getByID($componentthirdID);

        $postData = fixer::input('post')
            ->get();

        if(!$postData->baseline){
            dao::$errors['baseline'] =  sprintf($this->lang->componentthird->emptyObject, $this->lang->componentthird->baseline);
            return false;
        }
        //进行申请数据的校验
        $this->checkParamsNotEmpty($postData, $this->config->componentthird->create->requiredFields);

        //如果状态是退出，推荐版本设置为空
        if($postData->status == 'signout'){
            $postData->recommendVersion = '';
        }


        $this->tryError();
        //存入数据库
        $this->dao->update(TABLE_COMPONENT_RELEASE)->data($postData)->autoCheck()
            ->where('id')->eq($componentthirdID)
            ->exec();

        //获取新的数据
        $componentthirdNew = $this->getByID($componentthirdID);
        $this->tryError();
        return common::createChanges($componentthirdOld, $componentthirdNew);
    }

    /**
     * Project: chengfangjinke
     * Desc: 新建组件版本
     * shixuyang
     */
    public function createversion($componentthirdID){
        $postData = fixer::input('post')
            ->add('componentReleaseId', $componentthirdID)
            ->get();
        
        //进行申请数据的校验
        $this->filterVersionApplicationForm($postData);

        //需要根据“组件名称”做判重验证
        $versionName = $this->dao->select('version')->from(TABLE_COMPONENT_VERSION)->where('version')->eq($postData->version)->andWhere('componentReleaseId')->eq($postData->componentReleaseId)->fetch();
        if (!empty($versionName)){
            dao::$errors['version'] =  $this->lang->componentthird->versionRepeatError;
        }
        $this->tryError();
        $this->dao->insert(TABLE_COMPONENT_VERSION)
            ->data($postData)->autoCheck()
            ->exec();

        //存入数据库
        if(!dao::isError()){
            $lastId =  $this->dao->lastInsertID();
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
            ->get();
        //进行申请数据的校验
        $this->filterEditVersionApplicationForm($postData);

        $this->tryError();
        $this->dao->update(TABLE_COMPONENT_VERSION)
            ->data($postData)->autoCheck()
            ->where('id')->eq($versionID)
            ->exec();

        //获取新的数据
        $versionNew = $this->getVersionByID($versionID);
        $this->tryError();
        return common::createChanges($versionOld, $versionNew);
    }

    /**
     * Project: chengfangjinke
     * Desc: 版本进行编辑数据的校验
     * shixuyang
     */
    private function filterEditVersionApplicationForm($data){
        //第三方组件-新引入输入检查
        $this->checkVersionParamsNotEmpty($data, $this->config->componentthird->editversion->requiredFields);
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
        $this->checkParamsNotEmpty($data, $this->config->componentthird->createversion->requiredFields);
        if (preg_match($reg_version,$data->version)){
            dao::$errors['version'] =sprintf($this->lang->componentthird->versionError,$this->lang->componentthird->version);
        }
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
        $version = '';
        if(is_numeric($versionID)){
            $version = $this->dao->select('*')->from(TABLE_COMPONENT_VERSION)->where('id')->eq($versionID)->andWhere('deleted')->eq('0')->fetch();
            if(!empty($version)){
                $version->updatedDate = date("Y-m-d", strtotime($version->updatedDate));
                $componentthirdList = $this->dao->select('*')->from(TABLE_COMPONENT_ACCOUNT)->where('deleted')->eq('0')->andWhere('componentVersionId')->eq($version->id)->fetchAll();
                $version->usedNum = count($componentthirdList);
            }
        }
        return $version;
    }

    /**
     * 获取版本单个数据-通过名称
     * shixuyang
     * @param $requirementID
     * @param $version
     * @return mixed
     */
    public function getVersionByName($version,$componentthirdID)
    {
        $version = $this->dao->select('*')->from(TABLE_COMPONENT_VERSION)->where('version')->eq($version)->andWhere('componentReleaseId')->eq($componentthirdID)->andWhere('deleted')->eq('0')->fetch();
        if(!empty($version)){
            $version->updatedDate = date("Y-m-d", strtotime($version->updatedDate));
            $componentthirdList = $this->dao->select('*')->from(TABLE_COMPONENT_ACCOUNT)->where('deleted')->eq('0')->andWhere('componentVersionId')->eq($version->id)->fetchAll();
            $version->usedNum = count($componentthirdList);
        }
        return $version;
    }

    /**
     * 按钮权限控制
     * @param
     * @param $action
     * @return bool
     */
    public static function isClickable($componentthird, $action)
    {
        global $app;
        $action = strtolower($action);

        if($action == 'edit') return true/*(in_array($app->user->account,$componentthird->pmrm))*/;
        if($action == 'editinfo') return true/*(in_array($app->user->account,$componentthird->pmrm))*/;
        if($action == 'createversion') return true/*(in_array($app->user->account,$componentthird->pmrm))*/;
        if($action == 'editversion') return true/*(in_array($app->user->account,$componentthird->pmrm))*/;
        if($action == 'deleteversion') return true/*(in_array($app->user->account,$componentthird->pmrm))*/;
        if($action == 'delete') return true/*(in_array($app->user->account,$componentthird->pmrm))*/;
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
     * 第三方组件下拉框
     * @param $programID
     * @return mixed
     */
    public function getPairs()
    {
        return $this->dao->select('id,name')->from(TABLE_COMPONENT_RELEASE)
            ->where('deleted')->eq(0)
            ->andWhere('type')->eq('third')
            ->orderBy('id_desc')
            ->fetchPairs();
    }
}