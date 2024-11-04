<?php
class componentthirdaccountModel extends model
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
    public function getList($browseType, $queryID, $orderBy, $pager = null, $param = null)
    {
        $this->app->loadLang('component');
        $this->app->loadLang('componentthird');
        $componentthirdaccountQuery = '';

        $componentVersionTable = 0;
        $productTable = 0;
        $userTable = 0;
        $productVersionTable = 0;


        //搜索拼接sql语句
        if($browseType == 'bysearch')
        {
            $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : '';
            if($query)
            {
                $this->session->set('componentthirdaccountQuery', $query->sql);
                $this->session->set('componentthirdaccountForm', $query->form);
            }

            if($this->session->componentthirdaccountQuery == false) $this->session->set('componentthirdaccountQuery', ' 1 = 1');

            $componentthirdaccountQuery = $this->session->componentthirdaccountQuery;
            $componentthirdaccountQuery = str_replace('AND `', ' AND `t1.', $componentthirdaccountQuery);
            $componentthirdaccountQuery = str_replace('AND (`', ' AND (`t1.', $componentthirdaccountQuery);

            $componentthirdaccountQuery = str_replace('OR `', ' OR `t1.', $componentthirdaccountQuery);
            $componentthirdaccountQuery = str_replace('OR (`', ' OR (`t1.', $componentthirdaccountQuery);

            $componentthirdaccountQuery = str_replace('`', '', $componentthirdaccountQuery);

            if(strpos($componentthirdaccountQuery, 'productname') !== false)
            {
                $componentthirdaccountQuery = str_replace('t1.productname', "t4.id", $componentthirdaccountQuery);
                $productTable = 1;
            }

            if(strpos($componentthirdaccountQuery, 'productconnect') !== false)
            {
                $componentthirdaccountQuery = str_replace('t1.productconnect', "t4.PO", $componentthirdaccountQuery);
                $productTable = 1;
            }

            if(strpos($componentthirdaccountQuery, 'productversion') !== false)
            {
                $componentthirdaccountQuery = str_replace('t1.productversion', "t5.id", $componentthirdaccountQuery);
                $productVersionTable = 1;
            }

            if(strpos($componentthirdaccountQuery, 'productdept') !== false)
            {
                $componentthirdaccountQuery = str_replace('t1.productdept', "t6.dept", $componentthirdaccountQuery);
                $userTable = 1;
                $productTable = 1;
            }

            if(strpos($componentthirdaccountQuery, 'componentname') !== false)
            {
                $componentthirdaccountQuery = str_replace('t1.componentname', "t2.id", $componentthirdaccountQuery);
            }

            if(strpos($componentthirdaccountQuery, 'componentversion') !== false)
            {
                $componentthirdaccountQuery = str_replace('t1.componentversion', "t3.id", $componentthirdaccountQuery);
                $componentVersionTable = 1;
            }

            if(strpos($componentthirdaccountQuery, 'vulnerabilityLevel') !== false)
            {
                $componentthirdaccountQuery = str_replace('t1.vulnerabilityLevel', "t3.vulnerabilityLevel", $componentthirdaccountQuery);
                $componentVersionTable = 1;
            }


            if(strpos($componentthirdaccountQuery, 'appname') !== false)
            {
                $componentthirdaccountQuery = str_replace('t1.appname', "t1.appId", $componentthirdaccountQuery);
            }


        }

        if(strpos($orderBy, 'productdept') !== false){
            $orderBy = str_replace('productdept', "t6.dept", $orderBy);
            $userTable = 1;
            $productTable = 1;
        }else if(strpos($orderBy, 'productconnect') !== false){
            $orderBy = str_replace('productconnect', "t4.createdBy", $orderBy);
            $productTable = 1;
        }else if(strpos($orderBy, 'vulnerabilityLevel') !== false){
            $orderBy = str_replace('vulnerabilityLevel', "t3.vulnerabilityLevel", $orderBy);
            $componentVersionTable = 1;
        }else if(strpos($orderBy, 'appname') !== false){
            $orderBy = str_replace('appname', "t1.appId", $orderBy);
        }else if(strpos($orderBy, 'productname') !== false){
            $orderBy = str_replace('productname', "t1.productId", $orderBy);
        }else if(strpos($orderBy, 'productversion') !== false){
            $orderBy = str_replace('productversion', "t1.productVersionId", $orderBy);
        }else if(strpos($orderBy, 'componentname') !== false){
            $orderBy = str_replace('componentname', "t1.componentReleaseId", $orderBy);
        }else if(strpos($orderBy, 'componentversion') !== false){
            $orderBy = str_replace('componentversion', "t1.componentVersionId", $orderBy);
        }else{
            $orderBy = 't1.'.$orderBy;
        }

        $componentthirdaccounts = $this->dao->select('t1.*')->from(TABLE_COMPONENT_ACCOUNT)->alias('t1')
            ->leftJoin(TABLE_COMPONENT_RELEASE)->alias('t2')->on('t1.componentReleaseId = t2.id')
            ->beginIF($componentVersionTable == 1)->innerJoin(TABLE_COMPONENT_VERSION)->alias('t3')->on('t1.componentVersionId = t3.id')->fi()
            ->beginIF($productTable == 1)->leftJoin(TABLE_PRODUCT)->alias('t4')->on('t1.productId = t4.id')->fi()
            ->beginIF($productVersionTable == 1)->leftJoin(TABLE_PRODUCTPLAN)->alias('t5')->on('t1.productVersionId = t5.id')->fi()
            ->beginIF($userTable == 1)->leftJoin(TABLE_USER)->alias('t6')->on('t4.PO = t6.account')->fi()
            ->where('t1.deleted')->eq('0')->andWhere('t1.type')->eq('third')
            ->beginIF($browseType == 'bysearch')->andWhere($componentthirdaccountQuery)->fi()
            ->beginIF($browseType == 'componentreleaseid')->andWhere('t1.componentReleaseId')->eq($param)->fi()
            ->beginIF($browseType == 'componentversionid')->andWhere('t1.componentVersionId')->eq($param)->fi()
            ->orderBy($orderBy.", t2.name_asc")
            ->page($pager,'t1.id')
            ->fetchAll('id');


        //用于导出数据构建查询
        $componentthirdaccountExportQuery = $this->dao->sqlobj->select('t1.*')->from(TABLE_COMPONENT_ACCOUNT)->alias('t1')
            ->leftJoin(TABLE_COMPONENT_RELEASE)->alias('t2')->on('t1.componentReleaseId = t2.id')
            ->beginIF($componentVersionTable == 1)->innerJoin(TABLE_COMPONENT_VERSION)->alias('t3')->on('t1.componentVersionId = t3.id')->fi()
            ->beginIF($productTable == 1)->leftJoin(TABLE_PRODUCT)->alias('t4')->on('t1.productId = t4.id')->fi()
            ->beginIF($productVersionTable == 1)->leftJoin(TABLE_PRODUCTPLAN)->alias('t5')->on('t1.productVersionId = t5.id')->fi()
            ->beginIF($userTable == 1)->leftJoin(TABLE_USER)->alias('t6')->on('t4.createdBy = t6.account')->fi()
            ->where('t1.deleted')->eq('0')->andWhere('t1.type')->eq('third')
            ->beginIF($browseType == 'bysearch')->andWhere($componentthirdaccountQuery)->fi()
            ->beginIF($browseType == 'componentreleaseid')->andWhere('t1.componentReleaseId')->eq($param)->fi()
            ->beginIF($browseType == 'componentversionid')->andWhere('t1.componentVersionId')->eq($param)->fi();
        $this->session->set('componentthirdaccountExportQuery', $componentthirdaccountExportQuery->sql);


        //每个数据添加架构部处理人，方便按钮高亮
        //架构部指定人员（架构部处理、架构部确认）节点人员
        $productManagerReviewer = array_keys($this->lang->component->productManagerReviewer);
        //平台结构部领导
        $productManagerReviewerManager = $this->dao->select('id,manager1')->from(TABLE_DEPT)->where('name')->eq('平台架构部')->fetch();
        $pmrm = explode(',', trim($productManagerReviewerManager->manager1, ','));
        $pmrm = array_merge($productManagerReviewer, $pmrm);
        foreach ($componentthirdaccounts as $componentthirdaccount){
            $componentthirdaccount->pmrm = $pmrm;
            //系统名称
            $application = $this->dao->findByID($componentthirdaccount->appId)->from(TABLE_APPLICATION)->fetch();
            $componentthirdaccount->appname = $application->name;
            //产品名称
            $product = $this->dao->findById($componentthirdaccount->productId)->from(TABLE_PRODUCT)->fetch();
            $componentthirdaccount->productname = $product->name;
            //产品版本
            $plan = $this->dao->findByID((int)$componentthirdaccount->productVersionId)->from(TABLE_PRODUCTPLAN)->fetch();
            $componentthirdaccount->productversion = $plan->title;
            //产品联系人和产品所属部门
            $user = $this->dao->select('*')->from(TABLE_USER)->where("`account`")->eq($product->PO)->fetch();
            if($user){
                $componentthirdaccount->productconnect = $user->realname;
//                $dept = $this->dao->findById($user->dept)->from(TABLE_DEPT)->fetch();
                $deptname = $this->loadModel('dept')->getCompleteName($user->dept);
                $componentthirdaccount->productdept = $deptname;
            }else{
                $componentthirdaccount->productconnect = '';

                $componentthirdaccount->productdept = '';
            }

            //组件
            if(!empty($componentthirdaccount->customComponent)){
                $componentthirdaccount->componentname = $componentthirdaccount->customComponent;
            }else{
                $component = $this->dao->findByID($componentthirdaccount->componentReleaseId)->from(TABLE_COMPONENT_RELEASE)->fetch();
                $componentthirdaccount->componentname = $component->name;
            }

            //组件版本
            if(!empty($componentthirdaccount->componentVersionId)){
                $componentversion = $this->dao->findByID($componentthirdaccount->componentVersionId)->from(TABLE_COMPONENT_VERSION)->fetch();
                $componentthirdaccount->componentversion = $componentversion->version;
                $componentthirdaccount->vulnerabilityLevel = zget($this->lang->componentthird->vulnerabilityLevelList, $componentversion->vulnerabilityLevel);
            }else{
                $componentthirdaccount->componentversion = '';
                $componentthirdaccount->vulnerabilityLevel = '';
            }
            if(!empty($componentthirdaccount->customComponentVersion)){
                $componentthirdaccount->componentversion = $componentthirdaccount->customComponentVersion;
            }
        }

        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'componentthirdaccount', $browseType != 'bysearch');
        return $componentthirdaccounts;
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
        $this->config->componentthirdaccount->search['actionURL'] = $actionURL;
        $this->config->componentthirdaccount->search['queryID']   = $queryID;

        $this->loadModel('search')->setSearchParams($this->config->componentthirdaccount->search);
    }

    /**
     * Project: chengfangjinke
     * Desc: 新建组件申请台账
     * shixuyang
     */
    public function create(){
        $postData = fixer::input('post')
            ->get();

        //进行申请数据的校验
        if(empty($postData->appname)){
            dao::$errors['appname'] =  sprintf($this->lang->componentthirdaccount->emptyObject, $this->lang->componentthirdaccount->appname);
        }
        if(empty($postData->productname)){
            dao::$errors['productname'] =  sprintf($this->lang->componentthirdaccount->emptyObject, $this->lang->componentthirdaccount->productname);
        }
        if(empty($postData->productversion)){
            dao::$errors['productversion'] =  sprintf($this->lang->componentthirdaccount->emptyObject, $this->lang->componentthirdaccount->productversion);
        }
        /*if(empty($postData->componentname) || count($postData->componentname) == 0){
            dao::$errors['createcomponentname'] =  sprintf($this->lang->componentthirdaccount->emptyObject, $this->lang->componentthirdaccount->createcomponentname);
        }
        if(empty($postData->componentversion) || count($postData->componentversion) == 0){
            dao::$errors['createcomponentversion'] =  sprintf($this->lang->componentthirdaccount->emptyObject, $this->lang->componentthirdaccount->createcomponentversion);
        }*/
        $this->tryError();
        //组装数据
        $createArray = array();
        $componentArray = array();
        $customcomponentArray = array();
        $componentCount = count($postData->componentname);
        $customComponentCount = count($postData->componentname);
        if($componentCount == 1 and (current($postData->componentname) || current($postData->componentversion))){

            if(!current($postData->componentname)){
                dao::$errors['createcomponentname'] =  sprintf($this->lang->componentthirdaccount->emptyObject, $this->lang->componentthirdaccount->createcomponentname);
            }elseif (!current($postData->componentversion)){
                dao::$errors['createcomponentversion'] =  sprintf($this->lang->componentthirdaccount->emptyObject, $this->lang->componentthirdaccount->createcomponentversion);
            }
        }
        if($customComponentCount == 1 and (current($postData->customComponent) || current($postData->customComponentVersion))){

            if(!current($postData->customComponent)){
                dao::$errors['createcomponentname'] =  sprintf($this->lang->componentthirdaccount->emptyObject, $this->lang->componentthirdaccount->customComponent);
            }elseif (!current($postData->customComponentVersion)){
                dao::$errors['createcomponentversion'] =  sprintf($this->lang->componentthirdaccount->emptyObject, $this->lang->componentthirdaccount->customComponentVersion);
            }
        }

        $this->tryError();

        foreach ($postData->componentname as $i=>$item){
            if($componentCount > 1){
                if(empty($postData->componentname[$i])){
                    dao::$errors['createcomponentname'] =  sprintf($this->lang->componentthirdaccount->emptyObject, $this->lang->componentthirdaccount->createcomponentname);
                    break;
                }
                if(empty($postData->componentversion[$i])){
                    dao::$errors['createcomponentversion'] =  sprintf($this->lang->componentthirdaccount->emptyObject, $this->lang->componentthirdaccount->createcomponentversion);
                    break;
                }
            }

            if(!empty($postData->componentname[$i]) and !empty($postData->componentversion[$i])){
                if(!in_array($postData->componentname[$i],$componentArray)){
                    $createData = new stdClass();
                    $createData->appId = $postData->appname;
                    $createData->productId = $postData->productname;
                    $createData->productVersionId = $postData->productversion;
                    $createData->componentReleaseId = $postData->componentname[$i];
                    $createData->componentVersionId = $postData->componentversion[$i];
                    $createData->comment = $postData->comment[$i];
                    $createData->type = 'third';
                    array_push($createArray, $createData);
                    array_push($componentArray, $postData->componentname[$i]);
                    $componentObj = $this->loadModel('componentthird')->getByID($postData->componentname[$i]);
                    array_push($customcomponentArray,$componentObj->name);
                }else{
                    dao::$errors[''] =  $this->lang->componentthirdaccount->createrepeat;
                    break;
                }
            }
        }

        $customcreateArray = array();

        foreach ($postData->customComponent as $i=>$item){
            if($customComponentCount > 1){
                if(empty($postData->customComponent[$i])){
                    dao::$errors['createcomponentname'] =  sprintf($this->lang->componentthirdaccount->emptyObject, $this->lang->componentthirdaccount->customComponent);
                    break;
                }
                if(empty($postData->customComponentVersion[$i])){
                    dao::$errors['createcomponentversion'] =  sprintf($this->lang->componentthirdaccount->emptyObject, $this->lang->componentthirdaccount->customComponentVersion);
                    break;
                }
            }

            if(!empty($postData->customComponent[$i])){
                if(!in_array($postData->customComponent[$i],$customcomponentArray)){
                    $createData = new stdClass();
                    $createData->appId = $postData->appname;
                    $createData->productId = $postData->productname;
                    $createData->productVersionId = $postData->productversion;
                    $createData->customComponent = $postData->customComponent[$i];
                    $createData->customComponentVersion = $postData->customComponentVersion[$i];
                    $createData->comment = $postData->customcomment[$i];
                    $createData->type = 'third';
                    array_push($customcreateArray, $createData);
                    array_push($customcomponentArray, $postData->customComponent[$i]);
                }else{
                    dao::$errors[''] =  $this->lang->componentthirdaccount->createrepeat;
                    break;
                }
            }
        }
        $this->tryError();

        //先删除之前数据
        $this->dao->begin();
        $accountLasts = $this->dao->select('t1.*')
            ->from(TABLE_COMPONENT_ACCOUNT)->alias('t1')
            ->leftJoin(TABLE_COMPONENT_RELEASE)->alias('t2')->on('t1.componentReleaseId = t2.id')
            ->where('t1.deleted')->eq(0)
            ->andWhere('t2.deleted')->eq(0)
            ->andWhere('t1.productVersionId')->eq($postData->productversion)
            ->andWhere('t1.appId')->eq($postData->appname)
            ->andWhere('t1.productId')->eq($postData->productname)
            ->andWhere('t1.type')->eq('third')
            ->orderBy('t1.id_desc')
            ->fetchAll('id');
        $ids = array_column($accountLasts, 'id');
        $this->dao->delete()->from(TABLE_COMPONENT_ACCOUNT)->where('id')->in(implode(',',$ids))->exec();

        $accountLasts = $this->dao->select('t1.*')
            ->from(TABLE_COMPONENT_ACCOUNT)->alias('t1')
            ->where('t1.deleted')->eq(0)
            ->andWhere('t1.productVersionId')->eq($postData->productversion)
            ->andWhere('t1.appId')->eq($postData->appname)
            ->andWhere('t1.productId')->eq($postData->productname)
            ->andWhere('t1.customComponent')->isNotNull()
            ->andWhere('t1.customComponent')->ne('')
            ->andWhere('t1.type')->eq('third')
            ->orderBy('t1.id_desc')
            ->fetchAll('id');
        $ids = array_column($accountLasts, 'id');
        $this->dao->delete()->from(TABLE_COMPONENT_ACCOUNT)->where('id')->in(implode(',',$ids))->exec();

        foreach ($createArray as $createData){
            $this->dao->insert(TABLE_COMPONENT_ACCOUNT)
                ->data($createData)->autoCheck()
                ->exec();
        }
        foreach ($customcreateArray as $createData){
            $this->dao->insert(TABLE_COMPONENT_ACCOUNT)
                ->data($createData)->autoCheck()
                ->exec();
        }

        $this->tryError(1);
        $this->dao->commit();
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
}
