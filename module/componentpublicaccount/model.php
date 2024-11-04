<?php
class componentpublicaccountModel extends model
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
        $componentpublicaccountQuery = '';

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
                $this->session->set('componentpublicaccountQuery', $query->sql);
                $this->session->set('componentpublicaccountForm', $query->form);
            }

            if($this->session->componentpublicaccountQuery == false) $this->session->set('componentpublicaccountQuery', ' 1 = 1');

            $componentpublicaccountQuery = $this->session->componentpublicaccountQuery;
            $componentpublicaccountQuery = str_replace('AND `', ' AND `t1.', $componentpublicaccountQuery);
            $componentpublicaccountQuery = str_replace('AND (`', ' AND (`t1.', $componentpublicaccountQuery);

            $componentpublicaccountQuery = str_replace('OR `', ' OR `t1.', $componentpublicaccountQuery);
            $componentpublicaccountQuery = str_replace('OR (`', ' OR (`t1.', $componentpublicaccountQuery);

            $componentpublicaccountQuery = str_replace('`', '', $componentpublicaccountQuery);

            if(strpos($componentpublicaccountQuery, 'productname') !== false)
            {
                $componentpublicaccountQuery = str_replace('t1.productname', "t4.id", $componentpublicaccountQuery);
                $productTable = 1;
            }

            if(strpos($componentpublicaccountQuery, 'productconnect') !== false)
            {
                $componentpublicaccountQuery = str_replace('t1.productconnect', "t4.createdBy", $componentpublicaccountQuery);
                $productTable = 1;
            }

            if(strpos($componentpublicaccountQuery, 'productversion') !== false)
            {
                $componentpublicaccountQuery = str_replace('t1.productversion', "t5.id", $componentpublicaccountQuery);
                $productVersionTable = 1;
            }

            if(strpos($componentpublicaccountQuery, 'productdept') !== false)
            {
                $componentpublicaccountQuery = str_replace('t1.productdept', "t6.dept", $componentpublicaccountQuery);
                $userTable = 1;
                $productTable = 1;
            }

            if(strpos($componentpublicaccountQuery, 'componentname') !== false)
            {
                $componentpublicaccountQuery = str_replace('t1.componentname', "t2.id", $componentpublicaccountQuery);
            }

            if(strpos($componentpublicaccountQuery, 'componentversion') !== false)
            {
                $componentpublicaccountQuery = str_replace('t1.componentversion', "t3.id", $componentpublicaccountQuery);
                $componentVersionTable = 1;
            }

            if(strpos($componentpublicaccountQuery, 'componentlevel') !== false)
            {
                $componentpublicaccountQuery = str_replace('t1.componentlevel', "t2.level", $componentpublicaccountQuery);
            }

            if(strpos($componentpublicaccountQuery, 'componentcategory') !== false)
            {
                $componentpublicaccountQuery = str_replace('t1.componentcategory', "t2.category", $componentpublicaccountQuery);
            }

            if(strpos($componentpublicaccountQuery, 'appname') !== false)
            {
                $componentpublicaccountQuery = str_replace('t1.appname', "t1.appId", $componentpublicaccountQuery);
            }


        }

        if(strpos($orderBy, 'productdept') !== false){
            $orderBy = str_replace('productdept', "t6.dept", $orderBy);
            $userTable = 1;
            $productTable = 1;
        }else if(strpos($orderBy, 'productconnect') !== false){
            $orderBy = str_replace('productconnect', "t4.createdBy", $orderBy);
            $productTable = 1;
        }else if(strpos($orderBy, 'componentlevel') !== false){
            $orderBy = str_replace('componentlevel', "t2.level", $orderBy);
        }else if(strpos($orderBy, 'componentcategory') !== false){
            $orderBy = str_replace('componentcategory', "t2.category", $orderBy);
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

        $componentpublicaccounts = $this->dao->select('t1.*')->from(TABLE_COMPONENT_ACCOUNT)->alias('t1')
            ->leftJoin(TABLE_COMPONENT_RELEASE)->alias('t2')->on('t1.componentReleaseId = t2.id')
            ->beginIF($componentVersionTable == 1)->innerJoin(TABLE_COMPONENT_VERSION)->alias('t3')->on('t1.componentVersionId = t3.id')->fi()
            ->beginIF($productTable == 1)->leftJoin(TABLE_PRODUCT)->alias('t4')->on('t1.productId = t4.id')->fi()
            ->beginIF($productVersionTable == 1)->leftJoin(TABLE_PRODUCTPLAN)->alias('t5')->on('t1.productVersionId = t5.id')->fi()
            ->beginIF($userTable == 1)->leftJoin(TABLE_USER)->alias('t6')->on('t4.createdBy = t6.account')->fi()
            ->where('t1.deleted')->eq('0')->andWhere('t1.type')->eq('public')
            ->beginIF($browseType == 'bysearch')->andWhere($componentpublicaccountQuery)->fi()
            ->beginIF($browseType == 'componentreleaseid')->andWhere('t1.componentReleaseId')->eq($param)->fi()
            ->beginIF($browseType == 'componentversionid')->andWhere('t1.componentVersionId')->eq($param)->fi()
            ->orderBy($orderBy.", t2.name_asc")
            ->page($pager,'t1.id')
            ->fetchAll('id');

        //用于导出数据构建查询
        $componentpublicaccountExportQuery = $this->dao->sqlobj->select('t1.*')->from(TABLE_COMPONENT_ACCOUNT)->alias('t1')
            ->leftJoin(TABLE_COMPONENT_RELEASE)->alias('t2')->on('t1.componentReleaseId = t2.id')
            ->beginIF($componentVersionTable == 1)->innerJoin(TABLE_COMPONENT_VERSION)->alias('t3')->on('t1.componentVersionId = t3.id')->fi()
            ->beginIF($productTable == 1)->leftJoin(TABLE_PRODUCT)->alias('t4')->on('t1.productId = t4.id')->fi()
            ->beginIF($productVersionTable == 1)->leftJoin(TABLE_PRODUCTPLAN)->alias('t5')->on('t1.productVersionId = t5.id')->fi()
            ->beginIF($userTable == 1)->leftJoin(TABLE_USER)->alias('t6')->on('t4.createdBy = t6.account')->fi()
            ->where('t1.deleted')->eq('0')->andWhere('t1.type')->eq('public')
            ->beginIF($browseType == 'bysearch')->andWhere($componentpublicaccountQuery)->fi()
            ->beginIF($browseType == 'componentreleaseid')->andWhere('t1.componentReleaseId')->eq($param)->fi()
            ->beginIF($browseType == 'componentversionid')->andWhere('t1.componentVersionId')->eq($param)->fi();
        $this->session->set('componentpublicaccountExportQuery', $componentpublicaccountExportQuery->sql);

        //每个数据添加架构部处理人，方便按钮高亮
        //架构部指定人员（架构部处理、架构部确认）节点人员
        $productManagerReviewer = array_keys($this->lang->component->productManagerReviewer);
        //平台结构部领导
        $productManagerReviewerManager = $this->dao->select('id,manager1')->from(TABLE_DEPT)->where('name')->eq('平台架构部')->fetch();
        $pmrm = explode(',', trim($productManagerReviewerManager->manager1, ','));
        $pmrm = array_merge($productManagerReviewer, $pmrm);
        foreach ($componentpublicaccounts as $componentpublicaccount){
            $componentpublicaccount->pmrm = $pmrm;
            //系统名称
            $application = $this->dao->findByID($componentpublicaccount->appId)->from(TABLE_APPLICATION)->fetch();
            $componentpublicaccount->appname = $application->name;
            //产品名称
            $product = $this->dao->findById($componentpublicaccount->productId)->from(TABLE_PRODUCT)->fetch();
            $componentpublicaccount->productname = $product->name;
            //产品版本
            $plan = $this->dao->findByID((int)$componentpublicaccount->productVersionId)->from(TABLE_PRODUCTPLAN)->fetch();
            $componentpublicaccount->productversion = $plan->title;
            //产品联系人和产品所属部门
            $user = $this->dao->select('*')->from(TABLE_USER)->where("`account`")->eq($product->createdBy)->fetch();
            $componentpublicaccount->productconnect = $user->realname;
            $dept = $this->dao->findById($user->dept)->from(TABLE_DEPT)->fetch();
            $componentpublicaccount->productdept = $dept->name;
            //组件
            $component = $this->dao->findByID($componentpublicaccount->componentReleaseId)->from(TABLE_COMPONENT_RELEASE)->fetch();
            $componentpublicaccount->componentname = $component->name;
            $componentpublicaccount->componentlevel = zget($this->lang->component->levelList, $component->level);
            $componentpublicaccount->componentcategory = zget($this->lang->component->categoryList, $component->category);
            //组件版本
            $componentversion = $this->dao->findByID($componentpublicaccount->componentVersionId)->from(TABLE_COMPONENT_VERSION)->fetch();
            $componentpublicaccount->componentversion = $componentversion->version;
        }

        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'componentpublicaccount', $browseType != 'bysearch');
        return $componentpublicaccounts;
    }

    // 列表数据查询
    public function getListNew($browseType, $queryID, $orderBy, $pager, $param){
        $componentpublicaccountQuery = '';
        if($browseType == 'bysearch')
        {
            $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : '';
            if($query)
            {
                $this->session->set('componentpublicaccountQuery', $query->sql);
                $this->session->set('componentForm', $query->form);
            }

            if($this->session->componentpublicaccountQuery == false) $this->session->set('componentpublicaccountQuery', ' 1 = 1');
            $componentpublicaccountQuery = $this->session->componentpublicaccountQuery;
            if(strpos($componentpublicaccountQuery, 'componentDept') !== false)
            {
                $componentpublicaccountQuery = str_replace('`componentDept`', "t2.`maintainerDept`", $componentpublicaccountQuery);
            }
            if(strpos($componentpublicaccountQuery, 'componentname') !== false)
            {
                $componentpublicaccountQuery = str_replace('`componentname`', "t1.`componentId`", $componentpublicaccountQuery);
            }
            if(strpos($componentpublicaccountQuery, 'componentlevel') !== false)
            {
                $componentpublicaccountQuery = str_replace('`componentlevel`', "t2.`level`", $componentpublicaccountQuery);
            }
            if(strpos($componentpublicaccountQuery, 'componentcategory') !== false)
            {
                $componentpublicaccountQuery = str_replace('`componentcategory`', "t2.`category`", $componentpublicaccountQuery);
            }
            if(strpos($componentpublicaccountQuery, 'createdDate') !== false)
            {
                $componentpublicaccountQuery = str_replace('`createdDate`', "t1.`createdDate`", $componentpublicaccountQuery);
            }
        }

        if(strpos($orderBy, 'componentDept') !== false){
            $orderBy = str_replace('componentDept', "t2.maintainerDept", $orderBy);
        }else if(strpos($orderBy, 'componentlevel') !== false){
            $orderBy = str_replace('componentlevel', "t2.level", $orderBy);
        }else if(strpos($orderBy, 'componentcategory') !== false){
            $orderBy = str_replace('componentcategory', "t2.category", $orderBy);
        }else if(strpos($orderBy, 'projectManager') !== false){
            $orderBy = str_replace('projectManager', "t3.owner", $orderBy);
        }else if(strpos($orderBy, 'componentname') !== false){
            $orderBy = str_replace('componentname', "t1.componentId", $orderBy);
        }else{
            $orderBy = 't1.'.$orderBy;
        }

        $components = $this->dao->select('t1.id,t2.maintainerDept,t1.componentId,t1.componentVersion,t1.projectName,t1.projectDept,t3.owner,t2.level,t2.category,t1.startYear,t1.startQuarter,t1.createdDate')->from(TABLE_COMPONENT_PUBLIC_ACCOUNT)->alias('t1')
            ->leftJoin(TABLE_COMPONENT_RELEASE)->alias('t2')->on('t1.componentId = t2.id')
            ->leftJoin(TABLE_PROJECTPLAN)->alias('t3')->on('t1.projectName = t3.project')
            ->where('t1.deleted')->eq('0')
            ->beginIF($browseType == 'bysearch')->andWhere($componentpublicaccountQuery)->fi()
            ->beginIF($browseType == 'componentid')->andWhere('t1.componentId')->eq($param)->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll();

        //用于导出数据构建查询
        $componentpublicaccountExportQuery = $this->dao->sqlobj->select('t1.id,t2.maintainerDept,t1.componentId,t1.componentVersion,t1.projectName,t1.projectDept,t3.owner,t2.level,t2.category,t1.startYear,t1.startQuarter,t1.createdDate')->from(TABLE_COMPONENT_PUBLIC_ACCOUNT)->alias('t1')
            ->leftJoin(TABLE_COMPONENT_RELEASE)->alias('t2')->on('t1.componentId = t2.id')
            ->leftJoin(TABLE_PROJECTPLAN)->alias('t3')->on('t1.projectName = t3.project')
            ->where('t1.deleted')->eq('0')
            ->beginIF($browseType == 'bysearch')->andWhere($componentpublicaccountQuery)->fi();
        $this->session->set('componentpublicaccountExportQuery', $componentpublicaccountExportQuery->sql);

        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'component', $browseType != 'bysearch');
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
        $this->config->componentpublicaccount->search['actionURL'] = $actionURL;
        $this->config->componentpublicaccount->search['queryID']   = $queryID;

        $this->loadModel('search')->setSearchParams($this->config->componentpublicaccount->search);
    }

    /**
     * Project: chengfangjinke
     * Desc: 新建组件申请台账
     * shixuyang
     */
    public function createOld(){
        $postData = fixer::input('post')
            ->get();

        //进行申请数据的校验
        if(empty($postData->appname)){
            dao::$errors['appname'] =  sprintf($this->lang->componentpublicaccount->emptyObject, $this->lang->componentpublicaccount->appname);
        }
        if(empty($postData->productname)){
            dao::$errors['productname'] =  sprintf($this->lang->componentpublicaccount->emptyObject, $this->lang->componentpublicaccount->productname);
        }
        if(empty($postData->productversion)){
            dao::$errors['productversion'] =  sprintf($this->lang->componentpublicaccount->emptyObject, $this->lang->componentpublicaccount->productversion);
        }
        if(empty($postData->componentname) || count($postData->componentname) == 0){
            dao::$errors['createcomponentname'] =  sprintf($this->lang->componentpublicaccount->emptyObject, $this->lang->componentpublicaccount->createcomponentname);
        }
        if(empty($postData->componentversion) || count($postData->componentversion) == 0){
            dao::$errors['createcomponentversion'] =  sprintf($this->lang->componentpublicaccount->emptyObject, $this->lang->componentpublicaccount->createcomponentversion);
        }
        $this->tryError();
        //组装数据
        $createArray = array();
        $componentArray = array();
        foreach($postData->componentname as $k=>$item){
            if(empty($postData->componentname[$k])){
                dao::$errors['createcomponentname'] =  sprintf($this->lang->componentpublicaccount->emptyObject, $this->lang->componentpublicaccount->createcomponentname);
                break;
            }
            if(empty($postData->componentversion[$k])){
                dao::$errors['createcomponentversion'] =  sprintf($this->lang->componentpublicaccount->emptyObject, $this->lang->componentpublicaccount->createcomponentversion);
                break;
            }
            if(!in_array($postData->componentname[$k],$componentArray)){
                $createData = new stdClass();
                $createData->appId = $postData->appname;
                $createData->productId = $postData->productname;
                $createData->productVersionId = $postData->productversion;
                $createData->componentReleaseId = $postData->componentname[$k];
                $createData->componentVersionId = $postData->componentversion[$k];
                $createData->comment = $postData->comment[$k];
                $createData->type = 'public';
                array_push($createArray, $createData);
                array_push($componentArray, $postData->componentname[$k]);
            }else{
                dao::$errors['createcomponentname'] =  $this->lang->componentpublicaccount->createrepeat;
                break;
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
            ->andWhere('t2.type')->eq('public')
            ->orderBy('t1.id_desc')
            ->fetchAll('id');
        $ids = array_column($accountLasts, 'id');
        $this->dao->delete()->from(TABLE_COMPONENT_ACCOUNT)->where('id')->in(implode(',',$ids))->exec();

        foreach ($createArray as $createData){
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

    /**
     * 新增组件使用台账
     *
     * @access public
     * @return int|bool
     */
    public function create($ids)
    {
        $data = fixer::input('post')
            ->add('createdBy', $this->app->user->account)
            ->add('createdDate', helper::now())
            ->remove('uid')
            ->get();

        //进行申请数据的校验
        if(empty($data->componentName)){
            dao::$errors[] =  $this->lang->componentpublicaccount->componentNameError;
            return false;
        }
        if(empty($data->componentVersion)){
            dao::$errors[] =  $this->lang->componentpublicaccount->componentVersionError;
            return false;
        }

        // 编辑
        if(!empty($ids)){



            /*if(!array_diff($ids, $data->id) == []) {
                // 需删除
                $diff = array_diff($ids, $data->id);
                foreach ($diff as $v) {
                    $this->dao->update(TABLE_COMPONENT_PUBLIC_ACCOUNT)
                        ->set('deleted')->eq(1)
                        ->where('id')->eq($v)
                        ->exec();
                }
            }*/
            //如果全无值代表删除
            if(count($data->projectDept) == 1){
                foreach ($data->projectDept as $key=>$val){

                    if(empty($data->projectDept[$key]) && empty($data->projectName[$key]) && empty($data->startYear[$key]) && empty($data->startQuarter[$key])){

                        $this->dao->update(TABLE_COMPONENT_PUBLIC_ACCOUNT)
                            ->set('deleted')->eq('1')
                            ->where('id')->in($ids)
                            ->exec();

                        return true;
                    }
                }



            }

            // 校验必填
//            $keys = array_keys($data->projectDept);
            $i=0;
            foreach ($data->projectDept as $key=>$projectDept) {
                $i++;

                if (empty($data->projectDept[$key])) {
                    dao::$errors[''] = [sprintf($this->config->componentpublicaccount->projectDeptEmpty, $i )];
                    return false;
                }
                if (empty($data->projectName[$key])) {
                    dao::$errors[''] = [sprintf($this->config->componentpublicaccount->projectNameEmpty, $i )];
                    return false;
                }
                if (empty($data->startYear[$key])) {
                    dao::$errors[''] = [sprintf($this->config->componentpublicaccount->startYearEmpty, $i )];
                    return false;
                }
                if (empty($data->startQuarter[$key])) {
                    dao::$errors[''] = [sprintf($this->config->componentpublicaccount->startQuarterEmpty, $i )];
                    return false;
                }

            }
            $tempProjectName = [];
            $i = 0;
            foreach ($data->projectName as $key=>$oneprojectName){
                $i++;
                if(in_array($oneprojectName,$tempProjectName)){
                    dao::$errors[''] = [sprintf($this->lang->componentpublicaccount->projectrepeatLineError, $i)];
                    return false;
                }else{
                    $tempProjectName[$key] = $oneprojectName;
                }

            }

            // 查询组件名称下所有项目名称
            $projectNames = $this->dao->select('projectName')->from(TABLE_COMPONENT_PUBLIC_ACCOUNT)->where('componentId')->eq($data->componentName)->andWhere("componentVersion")->eq($data->componentVersion)->andWhere('deleted')->eq(0)->fetchAll('projectName');
            $projectNames = array_keys($projectNames);

            // 有新增
//            if(count($data->projectDept) > count($ids)){
                $idKeys = array_keys($ids);
                // 组装新建数据
                $createArray = array();
                $updateArray = array();
                $existID = [];
                $i=0;
                foreach($data->projectDept as $k=>$item){
                    $i++;
                    //要编辑的
                    if(isset($data->id[$k]) && $data->id[$k]) {
                        $checkRes = $this->checkDate($data->projectName[$k], $data->startYear[$k], $data->startQuarter[$k]);
                        if(!$checkRes){
                            dao::$errors[''] =  vsprintf($this->lang->componentpublicaccount->projectDateLineError,[$i]);
                            return false;
                        }
                        $updateData                     = new stdClass();
                        $updateData->id                 = $data->id[$k];
                        $updateData->componentId        = $data->componentName;
                        $updateData->componentVersion   = $data->componentVersion;
                        $updateData->projectDept        = $data->projectDept[$k];
                        $updateData->projectName        = $data->projectName[$k];
                        $updateData->startYear          = $data->startYear[$k];
                        $updateData->startQuarter       = $data->startQuarter[$k];
                        $updateData->startTime          = $data->startYear[$k].$data->startQuarter[$k];
                        $updateData->comment            = $data->comment[$k];
                        $updateData->editedBy           = $this->app->user->account;
                        $updateData->editedDate         = helper::now();
                        array_push($updateArray, $updateData);
                        $existID[] = $data->id[$k];
                    }else{
                        //新增的
                        /*if(in_array($data->projectName[$k], $projectNames)) {
                            $projects          = $this->dao->select('project,name')->from(TABLE_PROJECTPLAN)->where('deleted')->eq(0)->orderBy('id_desc')->fetchPairs();
                            $str               = zget($projects, $data->projectName[$k]);
                            dao::$errors['']   = '{'.$str.'}'.$this->lang->componentpublicaccount->projectrepeat;
                            return false;
                        }*/
                        $checkRes = $this->checkDate($data->projectName[$k], $data->startYear[$k], $data->startQuarter[$k]);
                        if(!$checkRes){
                            dao::$errors[''] =  vsprintf($this->lang->componentpublicaccount->projectDateLineError,[$i]);
                            return false;
                        }

                        $createData = new stdClass();
                        $createData->componentId        = $data->componentName;
                        $createData->componentVersion   = $data->componentVersion;
                        $createData->projectDept        = $data->projectDept[$k];
                        $createData->projectName        = $data->projectName[$k];
                        $createData->startYear          = $data->startYear[$k];
                        $createData->startQuarter       = $data->startQuarter[$k];
                        $createData->startTime          = $data->startYear[$k].$data->startQuarter[$k];
                        $createData->comment            = $data->comment[$k];
                        $createData->createdBy          = $this->app->user->account;
                        $createData->createdDate        = helper::now();
                        array_push($createArray, $createData);
                        unset($data->projectDept[$k]);
                        unset($data->projectName[$k]);
                        unset($data->startYear[$k]);
                        unset($data->startQuarter[$k]);
                        unset($data->comment[$k]);
                    }
                }

                $chaIDArr = array_diff($ids,$existID);
                /*a('existID');
                a($existID);
                a('delete');
                a($chaIDArr);
                a('insert');
                a($createArray);
                a('update');
                a($updateArray);

                exit();*/

                $this->tryError();
                $this->dao->begin();

                if($chaIDArr){
                    $this->dao->update(TABLE_COMPONENT_PUBLIC_ACCOUNT)
                        ->set('deleted')->eq('1')
                        ->where('id')->in($chaIDArr)
                        ->exec();
                }


                foreach ($createArray as $createData){
                    $this->dao->insert(TABLE_COMPONENT_PUBLIC_ACCOUNT)
                        ->data($createData)->autoCheck()
                        ->exec();
                }
                foreach ($updateArray as $updateData) {
                    $currentId = $updateData->id;
                    unset($updateData->id);
                    $this->dao->update(TABLE_COMPONENT_PUBLIC_ACCOUNT)
                        ->data($updateData)->autoCheck()
                        ->where('id')->eq($currentId)
                        ->exec();
                }
                $this->tryError(1);
                $this->dao->commit();
//            }

            /*$intersect    = array_intersect_assoc($projectNames, $data->projectName);
            if(!empty($intersect)){
                $projects           = $this->dao->select('project,name')->from(TABLE_PROJECTPLAN)->where('deleted')->eq(0)->orderBy('id_desc')->fetchPairs();
                $intersect          = implode(',' ,$intersect);
                $str                = zmget($projects, $intersect);
                dao::$errors['']    = '{'.$str.'}'.$this->lang->componentpublicaccount->projectrepeat;
                return false;
            }
            $unique_arr = array_unique($data->projectName);
            if(count($data->projectName) != count($unique_arr)){
                dao::$errors[''] =  $this->lang->componentpublicaccount->projectrepeat;
                return false;
            }

            // 组装编辑数据
            $updateArray = array();

            $this->tryError();
            $this->dao->begin();


            $this->tryError(1);
            $this->dao->commit();*/

        }else{ // 新建
            $i=0;
            foreach ($data->projectDept as $key=>$projectDept) {
                $i++;
                if (empty($data->projectDept[$key])) {
                    dao::$errors[''] = [sprintf($this->config->componentpublicaccount->projectDeptEmpty, $i )];
                    return false;
                }
                if (empty($data->projectName[$key])) {
                    dao::$errors[''] = [sprintf($this->config->componentpublicaccount->projectNameEmpty, $i )];
                    return false;
                }
                if (empty($data->startYear[$key])) {
                    dao::$errors[''] = [sprintf($this->config->componentpublicaccount->startYearEmpty, $i )];
                    return false;
                }
                if (empty($data->startQuarter[$key])) {
                    dao::$errors[''] = [sprintf($this->config->componentpublicaccount->startQuarterEmpty, $i )];
                    return false;
                }

            }
            $tempProjectName = [];
            $i = 0;
            foreach ($data->projectName as $key=>$oneprojectName){
                $i++;
                if(in_array($oneprojectName,$tempProjectName)){
                    dao::$errors[''] = [sprintf($this->lang->componentpublicaccount->projectrepeatLineError, $i)];
                    return false;
                }else{
                    $tempProjectName[$key] = $oneprojectName;
                }

            }
            // 判断项目名称是否重复
//            $projectNames = $this->dao->select('projectName')->from(TABLE_COMPONENT_PUBLIC_ACCOUNT)->where('componentId')->eq($data->componentName)->andWhere("componentVersion")->eq($data->componentVersion)->andWhere('deleted')->eq(0)->fetchAll('projectName');
            /*$projectNames = array_keys($projectNames);
            $intersect    = array_intersect_assoc($projectNames, $data->projectName);
            if(!empty($intersect)){
                $projects           = $this->dao->select('project,name')->from(TABLE_PROJECTPLAN)->where('deleted')->eq(0)->orderBy('id_desc')->fetchPairs();
                $intersect          = implode(',' ,$intersect);
                $str                = zmget($projects, $intersect);
                dao::$errors['']    = '{'.$str.'}'.$this->lang->componentpublicaccount->projectrepeat;
                return false;
            }
            $unique_arr = array_unique($data->projectName);
            if(count($data->projectName) != count($unique_arr)){
                dao::$errors[''] =  $this->lang->componentpublicaccount->projectrepeat;
                return false;
            }*/

            //组装数据
            $createArray = array();
            $i=0;
            foreach($data->projectDept as $k=>$item){

                $i++;
                $checkRes = $this->checkDate($data->projectName[$k], $data->startYear[$k], $data->startQuarter[$k]);
                if(!$checkRes){
                    dao::$errors[''] =  vsprintf($this->lang->componentpublicaccount->projectDateLineError,[$i]);
                    return false;
                }
                $createData = new stdClass();
                $createData->componentId        = $data->componentName;
                $createData->componentVersion   = $data->componentVersion;
                $createData->projectDept        = $data->projectDept[$k];
                $createData->projectName        = $data->projectName[$k];
                $createData->startYear          = $data->startYear[$k];
                $createData->startQuarter       = $data->startQuarter[$k];
                $createData->startTime          = $data->startYear[$k].$data->startQuarter[$k];
                $createData->comment            = $data->comment[$k];
                $createData->createdBy          = $this->app->user->account;
                $createData->createdDate        = helper::now();
                array_push($createArray, $createData);
            }

            $this->tryError();
            $this->dao->begin();

            foreach ($createArray as $createData){
                $this->dao->insert(TABLE_COMPONENT_PUBLIC_ACCOUNT)
                    ->data($createData)->autoCheck()
                    ->exec();
            }
            $this->tryError(1);
            $this->dao->commit();
        }

    }

    // 判断开始使用时间是否早于项目立项时间
    public function checkDate($project,  $year, $quarter){
        // 项目立项时间
        $projectOpenDate           = $this->dao->select('openedDate')->from(TABLE_PROJECT)->where('id')->eq($project)->fetch();

        // 和所选季度第一天作比较
        if($quarter == 1){
            $date = date($year."-01-01 00:00:00");
        }elseif($quarter == 2){
            $date = date($year."-04-01 00:00:00");
        }elseif($quarter == 3){
            $date = date($year."-07-01 00:00:00");
        }else{
            $date = date($year."-10-01 00:00:00");
        }

        if($date < $projectOpenDate->openedDate){
//            dao::$errors[''] =  $this->lang->componentpublicaccount->projectDateError;
            return false;
        }else{
            return true;
        }
    }
}
