<?php
class componentpublicaccount extends control
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
    public function browse($browseType = 'all', $param = 0, $orderBy = 'startTime_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $this->app->loadLang('component');
        $browseType = strtolower($browseType);


        //搜索框的值
        $depts = $this->loadModel('dept')->getOptionMenu();
        $depts[0] = '';
        $this->config->componentpublicaccount->search['params']['componentDept']['values']  = $depts;
        $this->config->componentpublicaccount->search['params']['projectDept']['values']    = $depts;

        $this->config->componentpublicaccount->search['params']['componentlevel']['values']    = array('' => '') + $this->lang->component->levelList;
        $this->config->componentpublicaccount->search['params']['componentcategory']['values'] = array('' => '') + $this->lang->component->categoryList;

        $years = $this->dao->select('startYear')->from(TABLE_COMPONENT_PUBLIC_ACCOUNT)->where('deleted')->eq(0)->orderBy('startYear_desc')->fetchPairs('startYear');
        $this->config->componentpublicaccount->search['params']['startYear']['values']         = array('' => '') + $years;
        $this->config->componentpublicaccount->search['params']['startQuarter']['values']      = array('' => '') + $this->lang->componentpublicaccount->quarters;

        $componentNames = $this->dao->select('id,name')->from(TABLE_COMPONENT_RELEASE)->where('deleted')->eq(0)->andWhere('type')->eq('public')->orderBy('id_desc')->fetchPairs();
        $this->config->componentpublicaccount->search['params']['componentname']['values']       = array('' => '') + $componentNames;

        $versions = $this->dao->select('id,version')->from(TABLE_COMPONENT_VERSION)->where('deleted')->eq(0)->orderBy('id_desc')->fetchPairs();
        $this->config->componentpublicaccount->search['params']['componentversion']['values']       = array('' => '') + $versions;

        $projects = $this->dao->select('project,name')->from(TABLE_PROJECTPLAN)->where('deleted')->eq(0)->orderBy('id_desc')->fetchPairs();
        $this->config->componentpublicaccount->search['params']['projectName']['values']       = array('' => '') + $projects;

        $querystr = $this->session->componentpublicaccountQuery;
        $queryForm = $this->session->componentpublicaccountForm;
  
        if(strpos($querystr, 'componentDept') !== false ){
            foreach ($queryForm as $key=>$value){
                if($value == 'componentDept'){
                    $index = explode("field", $key);
                    $searchDeptId = $queryForm['value'.$index[1]];
                    break;
                }
            }
            $componentList = $this->loadModel('componentpublic')->getByDeptPairs($searchDeptId);
            $componentList = array('' => '') +  $componentList;
            $this->config->componentpublicaccount->search['params']['componentname']['values'] = array('' => '') + $componentList;
        }
        if(strpos($querystr, 'componentname') !== false ){
            foreach ($queryForm as $key=>$value){
                if($value == 'componentname'){
                    $index = explode("field", $key);
                    $searchComponentId = $queryForm['value'.$index[1]];
                    break;
                }
            }
            $componentVsersionList = $this->loadModel('componentpublic')->getNewVersionPairs($searchComponentId);
            $componentVsersionList = array('' => '') +  $componentVsersionList;
            $this->config->componentpublicaccount->search['params']['componentversion']['values'] = array('' => '') + $componentVsersionList;
        }

        if(strpos($querystr, 'projectDept') !== false ){
            foreach ($queryForm as $key=>$value){
                if($value == 'projectDept'){
                    $index = explode("field", $key);
                    $searchProjectDeptId = $queryForm['value'.$index[1]];
                    break;
                }
            }
            $searchProjects = $this->dao->select('project,name')
                ->from(TABLE_PROJECTPLAN)
                ->where('deleted')->eq('0')
                ->andWhere("FIND_IN_SET('{$searchProjectDeptId}',`bearDept`)")
                ->andWhere("project")->gt(0)
                ->orderBy('id_desc')
                ->fetchPairs();
            $searchProjects = array('' => '') +  $searchProjects;
            $this->config->componentpublicaccount->search['params']['projectName']['values'] = array('' => '') + $searchProjects;
        }




        $queryID = ($browseType == 'bysearch')  ? (int)$param : 0;
        $actionURL = $this->createLink('componentpublicaccount', 'browse', "browseType=bySearch&param=myQueryID");
        $this->componentpublicaccount->buildSearchForm($queryID, $actionURL);

        /* 设置详情页面返回的url连接。*/
        $this->session->set('componentpublicaccountList', $this->app->getURI(true));
        $this->session->set('common_back_url', $this->app->getURI(true),'backlog');
        $this->session->set('componentpublicaccountHistory', $this->app->getURI(true));

        /* 构建pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);
        $componentpublicaccountList = $this->componentpublicaccount->getListNew($browseType, $queryID, $orderBy, $pager,$param);
        $code = $recPerPage*($pageID-1)+1;
        foreach($componentpublicaccountList as $item){
            $item->code = $code;
            $code = $code + 1;
        }
        $this->view->componentNames     = $componentNames;
        $this->view->title              = $this->lang->componentpublicaccount->common;
        $this->view->orderBy            = $orderBy;
        $this->view->pager              = $pager;
        $this->view->param              = $param;
        $this->view->depts              = $depts;
        $this->view->browseType         = $browseType;
        $this->view->datas              = $componentpublicaccountList;
        $this->view->users              = $this->loadModel('user')->getPairs('noletter');
        $this->view->versions           = $this->dao->select('id,version')->from(TABLE_COMPONENT_VERSION)->where('deleted')->eq('0')->fetchPairs();
        $this->view->projects           = $this->dao->select('project,name')->from(TABLE_PROJECTPLAN)->where('deleted')->eq('0')->fetchPairs();
        $this->display();
    }

    /**
     * 新建公共组件台账
     * @return void
     */
    public function create($componentId = '', $componentVersion = ''){
        $ids = [];
        // 编辑
        if(!empty($componentId) && !empty($componentVersion)){
            $componentList = $this->dao->select('*')->from(TABLE_COMPONENT_PUBLIC_ACCOUNT)->where('deleted')->eq('0')->andWhere('componentId')->eq($componentId)->andWhere('componentVersion')->eq($componentVersion)->orderBy('id_desc')->fetchAll();
            $ids = array_column($componentList, 'id');

            $this->view->componentList     = $componentList;
            $this->view->componentName     = $componentId;
            $this->view->componentVersion  = $componentVersion;
            foreach ($componentList as $key=>$component){
                $component->projects          = [''=>'']+$this->dao->select('project,name')->from(TABLE_PROJECTPLAN)->where('deleted')->eq(0)->andWhere(" find_in_set('{$component->projectDept}',bearDept) ")->andWhere("project")->gt(0)->orderBy('id_desc')->fetchPairs();
            }
//            $this->view->projects          = [''=>'']+$this->dao->select('project,name')->from(TABLE_PROJECTPLAN)->where('deleted')->eq(0)->orderBy('id_desc')->fetchPairs();
            $this->view->maintainer        = $this->dao->select('maintainer')->from(TABLE_COMPONENT_RELEASE)->where('deleted')->eq(0)->andWhere('id')->eq($componentId)->fetch();
//            $this->view->maintainer->maintainer = 'admin';
            $versions          = $this->dao->select('id,version')->from(TABLE_COMPONENT_VERSION)->where('deleted')->eq(0)->andWhere('componentReleaseId')->eq($componentId)->orderBy('id_desc')->fetchPairs();
        }

        if($_POST){

            $this->componentpublicaccount->create($ids);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            //操作记录
            $actionID = $this->loadModel('action')->create('componentpublicaccount', $componentId, 'createdaccount');
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = inlink('browse');
            $this->send($response);

        }
        $this->view->title       = $this->lang->componentpublicaccount->create;

        // 组件
        $componentNames = $this->dao->select('id,name')->from(TABLE_COMPONENT_RELEASE)->where('deleted')->eq(0)->andWhere('type')->eq('public')->orderBy('id_desc')->fetchPairs();
        $this->view->componentNames       = array('' => '') + $componentNames;

        // 部门
        $depts = $this->loadModel('dept')->getOptionMenu();
        unset($depts[0]);
        $this->view->depts = array('' => '请选择') + $depts;

        if(!isset($versions)){
            $versions = [];
        }
        $this->view->versions = $versions;
        $this->view->users       = $this->loadmodel('user')->getPairs('noletter|noclosed');
        if($componentId){
            $this->view->actions     = $this->loadmodel('action')->getList('componentpublicaccount', $componentId);
        }else{
            $this->view->actions     = [];
        }

        // 年份,季度下拉框
        $this->view->years      = $this->lang->componentpublicaccount->years;
        $this->view->quarters   = $this->lang->componentpublicaccount->quarters;
        $this->display();
    }

    /**
     * 搜索框根据产品获取版本
     * @param $id
     * @return void
     */
    public function ajaxGetVersionByProduct($id, $index){
        $plans = $this->dao->select('id,title')
            ->from(TABLE_PRODUCTPLAN)
            ->where('deleted')->eq(0)
            ->andWhere('product')->eq($id)
            ->orderBy('id_desc')
            ->fetchPairs('id', 'title');
        $plans = array('' => '') +  $plans;
        echo html::select('value'.$index, $plans, '',"class='form-control searchInput chosen'");
    }

    /**
     * 搜索框根据组件获取版本
     * @param $id
     * @return void
     */
    public function ajaxGetVersionByComponent($id, $index){
        $plans = $this->dao->select('id,version')
            ->from(TABLE_COMPONENT_VERSION)
            ->where('deleted')->eq(0)
            ->andWhere('componentReleaseId')->eq($id)
            ->orderBy('id_desc')
            ->fetchPairs('id', 'version');
        $plans = array('' => '') +  $plans;
        echo html::select('value'.$index, $plans, '',"class='form-control searchInput chosen'");
    }

    /**
     * 搜索框根据组件获取版本
     * @param $id
     * @return void
     */
    public function ajaxGetproductByApp($id, $index){
        if(empty($id)){
            $plans = $this->loadModel('product')->getNamePairs();
        }else{
            $plans = $this->dao->select('id,name')
                ->from(TABLE_PRODUCT)
                ->where('deleted')->eq(0)
                ->andWhere('app')->eq($id)
                ->orderBy('id_desc')
                ->fetchPairs('id', 'name');
        }
        $plans = array('' => '') +  $plans;
        echo html::select('value'.$index, $plans, '',"class='form-control searchInput chosen'");
    }


    /**
     * 搜索框根据维护部门获取该部门下所有的组件
     * @param $id
     * @return void
     */
    public function ajaxGetComponentByDept($id, $index){
//        $id = 18;

        if(empty($id)){
            $componentList = $this->loadModel('componentpublic')->getPairs();

        }else{
            $componentList = $this->loadModel('componentpublic')->getByDeptPairs($id);
        }

        $componentList = array('' => '') +  $componentList;

        echo html::select('value'.$index, $componentList, '',"class='form-control searchInput chosen'");
    }

    /**
     * 搜索框根据维护部门获取该组件下所有的版本
     * @param $id
     * @return void
     */
    public function ajaxGetComponentVersionByID($id, $index){


        if(empty($id)){
            $componentList = $this->loadModel('componentpublic')->getAllVersionPairs();

        }else{
            $componentList = $this->loadModel('componentpublic')->getNewVersionPairs($id);
        }

        $componentList = array('' => '') +  $componentList;

        echo html::select('value'.$index, $componentList, '',"class='form-control searchInput chosen'");
    }
    /**
     * 根据部门获取项目名称
     * @param $id
     * @return void
     */
    public function ajaxGetSearchProjects($id, $index){
        $projects = $this->dao->select('project,name')
            ->from(TABLE_PROJECTPLAN)
            ->where('deleted')->eq('0')
            ->andWhere("FIND_IN_SET('{$id}',`bearDept`)")
            ->andWhere("project")->gt(0)
            ->orderBy('id_desc')
            ->fetchPairs();
        $projects = array('' => '') +  $projects;
        echo html::select('value'.$index, $projects, '',"class='form-control searchInput chosen'");
//        echo html::select('projectName['.$index.']', $projects, '',"class='form-control chosen'");
    }


    public function ajaxGetVersionByComponentName($id){
        $versions = $this->dao->select('id,version')
            ->from(TABLE_COMPONENT_VERSION)
            ->where('deleted')->eq(0)
            ->andWhere('componentReleaseId')->eq($id)
            ->orderBy('id_desc')
            ->fetchPairs('id', 'version');

        $versions = array('' => '') +  $versions;
        echo html::select('componentVersion', $versions, '',"class='form-control chosen' onchange='changeVersion(this.value)'");
    }

    /**
     * 根据部门获取项目名称
     * @param $id
     * @return void
     */
    public function ajaxGetProjects($id, $index){
        $projects = $this->dao->select('project,name')
            ->from(TABLE_PROJECTPLAN)
            ->where('deleted')->eq('0')
            ->andWhere("FIND_IN_SET('{$id}',`bearDept`)")
            ->andWhere("project")->gt(0)
            ->orderBy('id_desc')
            ->fetchPairs();
        $projects = array('' => '') +  $projects;
        echo html::select('projectName['.$index.']', $projects, '',"class='form-control chosen'");
    }

    /**
     * 新建根据组件获取版本
     * @param $id
     * @return void
     */
    public function ajaxGetproductByAppcreate($id){
        $plans = $this->dao->select('id,name')
            ->from(TABLE_PRODUCT)
            ->where('deleted')->eq(0)
            ->andWhere('app')->eq($id)
            ->orderBy('id_desc')
            ->fetchPairs('id', 'name');
        $plans = array('' => '') +  $plans;
        echo html::select('productname', $plans, '',"class='form-control  chosen' onchange='selectProduct(this.value)'");
    }

    /**
     * 新建获取产品信息
     * @param $id
     * @return void
     */
    public function ajaxGetProductConnect($id){
        $product = $this->dao->select('*')
            ->from(TABLE_PRODUCT)
            ->where('deleted')->eq(0)
            ->andWhere('id')->eq($id)
            ->fetch();
        if(empty($product) or $this->app->user->account == $product->createdBy or $this->app->user->account == $product->PO){
            echo true;
        }
        echo false;
    }

    /**
     * 新建获取台账信息
     * @param $id
     * @return void
     */
    public function ajaxGetAccount($id,$isConnect){
        $accounts = $this->dao->select('t1.*')
            ->from(TABLE_COMPONENT_ACCOUNT)->alias('t1')
            ->innerJoin(TABLE_COMPONENT_RELEASE)->alias('t2')->on('t1.componentReleaseId = t2.id')
            ->where('t1.deleted')->eq(0)
            ->andWhere('t2.deleted')->eq(0)
            ->andWhere('t1.productVersionId')->eq($id)
            ->andWhere('t2.type')->eq('public')
            ->orderBy('t1.id_desc')
            ->fetchAll('id');
        $components = $this->loadModel('componentpublic')->getPairs();
        $components = array('' => '') + $components;
        $str = "";
        if(!empty($accounts)){
            //找到目标台账
            foreach($accounts as $i=>$item){
                $str = $this->buildHtmlByData($i, $accounts[$i], $components,$str);
            }
        }else{
            //如果是产品联系人true,寻找最接近的版本台账；如果是false，不寻找
            if($isConnect == 'true'){
                //寻找版本号最接近的台账
                $productplan = $this->dao->select('*')
                    ->from(TABLE_PRODUCTPLAN)
                    ->where('deleted')->eq(0)
                    ->andWhere('id')->eq($id)
                    ->fetch();
                if(!empty($productplan)) {
                    //找到同产品下的所有版本
                    $productplanList = $this->dao->select('*')
                        ->from(TABLE_PRODUCTPLAN)
                        ->where('deleted')->eq(0)
                        ->andWhere('product')->eq($productplan->product)
                        ->fetchAll();
                    if (!empty($productplanList)) {
                        $versionLast = $productplan->title;
                        //根据与目标版本的差距，排序
                        foreach($productplanList as $i=>$item){
                            preg_match("/(?:version|v|)\s*((?:[0-9]+\.?)+)/i", $productplanList[$i]->title, $matches1);
                            $version1 = $matches1[1];
                            preg_match("/(?:version|v|)\s*((?:[0-9]+\.?)+)/i", $versionLast, $matches2);
                            $version2 = $matches2[1];
                            $versionArray1 = explode(".", $version1);
                            $versionArray2 = explode(".", $version2);
                            $diff = 0;
                            //根据标准版本V1.0.0.1，转化成1*10^9+0*10^6+0*10^3+1*10^0的数字，相减得到绝对值
                            for($temp = 0; $temp<4; $temp=$temp+1){
                                if(empty($versionArray1[$temp])){
                                    $var1 = 0;
                                }else{
                                    $var1 = $versionArray1[$temp];
                                }
                                if(empty($versionArray2[$temp])){
                                    $var2 = 0;
                                }else{
                                    $var2 = $versionArray2[$temp];
                                }
                                $diff = $diff+($var2-$var1)*pow(10,(4-$temp-1)*3);
                            }
                            $productplanList[$i]->diff = abs($diff);
                        }
                        $ids = array_column($productplanList, 'diff');
                        array_multisort($ids, SORT_ASC, $productplanList);
                    }
                    //遍历排序后的数据，得到台账
                    foreach ($productplanList as $item){
                        $accountLasts = $this->dao->select('t1.*')
                            ->from(TABLE_COMPONENT_ACCOUNT)->alias('t1')
                            ->innerJoin(TABLE_COMPONENT_RELEASE)->alias('t2')->on('t1.componentReleaseId = t2.id')
                            ->where('t1.deleted')->eq(0)
                            ->andWhere('t2.deleted')->eq(0)
                            ->andWhere('t1.productVersionId')->eq($item->id)
                            ->andWhere('t2.type')->eq('public')
                            ->orderBy('t1.id_desc')
                            ->fetchAll('id');
                        if (!empty($accountLasts)) {
                            foreach ($accountLasts as $i=>$item) {
                                $str = $this->buildHtmlByData($i, $accountLasts[$i], $components,$str);
                            }
                            break;
                        }
                    }
                }
            }
        }
        if(empty($str)){
            //没有找到台账
            $item = new stdClass();
            $item->componentReleaseId = '';
            $item->componentVersionId = '';
            $item->comment = '';
            $str = $this->buildHtmlByData(0, $item, $components,$str);
        }
        echo $str;
    }

    /**
     * 构建html
     * @param $i
     * @param $item
     * @param $components
     * @param $plans
     * @return string
     */
    public function buildHtmlByData($i, $item,$components,$str){
        $str = $str."<div class='table-row component-partitions'>";
        $str = $str."<div class='table-col w-250px'>";
        $str = $str."<div class='input-group'>";
        $str = $str."<span class='input-group-addon'>";
        $str = $str.$this->lang->componentpublicaccount->createcomponentname;
        $str = $str."</span>";
        $str = $str.html::select('componentname['.$i.']', $components, $item->componentReleaseId, "id='componentname".$i."' data-index='".$i."' class='form-control chosen initClass' onchange='selectComponent(this.value,this.id)'");
        $plans = $this->dao->select('id,version')
            ->from(TABLE_COMPONENT_VERSION)
            ->where('deleted')->eq(0)
            ->andWhere('componentReleaseId')->eq($item->componentReleaseId)
            ->orderBy('id_desc')
            ->fetchPairs('id', 'version');
        $str = $str."</div>";
        $str = $str."</div>";
        $str = $str."<div class='table-col w-250px'>";
        $str = $str."<div class='input-group'>";
        $str = $str."<span class='input-group-addon'>";
        $str =  $str.$this->lang->componentpublicaccount->createcomponentversion;
        $str = $str."</span>";
        $str = $str.html::select('componentversion['.$i.']', $plans, $item->componentVersionId, "id='componentversion".$i."' class='form-control chosen initClass'");
        $str = $str."</div>";
        $str = $str."</div>";
        $str = $str."<div class='table-col w-400px'>";
        $str = $str."<div class='input-group'>";
        $str = $str."<span class='input-group-addon fix-border fix-padding'>";
        $str =  $str.$this->lang->componentpublicaccount->comment;
        $str = $str."</span>";
        $str = $str.html::input('comment['.$i.']', $item->comment, "maxlength='40' id='comment".$i."' data-index='".$i."' class='form-control initCommentClass'");
        $str = $str."<a class='input-group-btn' href='javascript:void(0)' onclick='addPartition(this)' data-id='".$i."' id='addItem".$i."' class='btn btn-link'><i class='icon-plus'></i></a>";
        $str = $str."<a class='input-group-btn' href='javascript:void(0)' onclick='delPartition(this)' class='btn btn-link'><i class='icon-close'></i></a>";
        $str = $str."</div>";
        $str = $str."</div>";
        $str = $str."</div>";
        return $str;
    }

    /**
     * 返回提示语
     * @param $id
     * @return void
     */
    public function ajaxGetTips($id){
        $tips = "";
        $accounts = $this->dao->select('t1.*')
            ->from(TABLE_COMPONENT_ACCOUNT)->alias('t1')
            ->innerJoin(TABLE_COMPONENT_RELEASE)->alias('t2')->on('t1.componentReleaseId = t2.id')
            ->where('t1.deleted')->eq(0)
            ->andWhere('t2.deleted')->eq(0)
            ->andWhere('t1.productVersionId')->eq($id)
            ->andWhere('t2.type')->eq('public')
            ->orderBy('t1.id_desc')
            ->fetchAll('id');
        if(empty($accounts)){
            $productplan = $this->dao->select('*')
                ->from(TABLE_PRODUCTPLAN)
                ->where('deleted')->eq(0)
                ->andWhere('id')->eq($id)
                ->fetch();
            if(!empty($productplan)){
                //同产品下所有的版本
                $productplanList = $this->dao->select('*')
                    ->from(TABLE_PRODUCTPLAN)
                    ->where('deleted')->eq(0)
                    ->andWhere('product')->eq($productplan->product)
                    ->fetchAll();
                //将目标版本的相差进行排序
                if (!empty($productplanList)) {
                    $versionLast = $productplan->title;
                    foreach($productplanList as $i=>$item){
                        preg_match("/(?:version|v|)\s*((?:[0-9]+\.?)+)/i", $productplanList[$i]->title, $matches1);
                        $version1 = $matches1[1];
                        preg_match("/(?:version|v|)\s*((?:[0-9]+\.?)+)/i", $versionLast, $matches2);
                        $version2 = $matches2[1];
                        $versionArray1 = explode(".", $version1);
                        $versionArray2 = explode(".", $version2);
                        $diff = 0;
                        //根据标准版本V1.0.0.1，转化成1*10^9+0*10^6+0*10^3+1*10^0的数字，相减得到绝对值
                        for($temp = 0; $temp<4; $temp=$temp+1){
                            if(empty($versionArray1[$temp])){
                                $var1 = 0;
                            }else{
                                $var1 = $versionArray1[$temp];
                            }
                            if(empty($versionArray2[$temp])){
                                $var2 = 0;
                            }else{
                                $var2 = $versionArray2[$temp];
                            }
                            $diff = $diff+($var2-$var1)*pow(10,(4-$temp-1)*3);
                        }
                        $productplanList[$i]->diff = abs($diff);
                    }
                    $ids = array_column($productplanList, 'diff');
                    array_multisort($ids, SORT_ASC, $productplanList);
                }
                foreach ($productplanList as $item) {
                    $accountLasts = $this->dao->select('t1.*')
                        ->from(TABLE_COMPONENT_ACCOUNT)->alias('t1')
                        ->innerJoin(TABLE_COMPONENT_RELEASE)->alias('t2')->on('t1.componentReleaseId = t2.id')
                        ->where('t1.deleted')->eq(0)
                        ->andWhere('t2.deleted')->eq(0)
                        ->andWhere('t1.productVersionId')->eq($item->id)
                        ->andWhere('t2.type')->eq('public')
                        ->orderBy('t1.id_desc')
                        ->fetchAll('id');
                    if (!empty($accountLasts)) {
                        $product = $this->dao->select('*')
                            ->from(TABLE_PRODUCT)
                            ->where('deleted')->eq(0)
                            ->andWhere('id')->eq($productplan->product)
                            ->fetch();
                        $tips = "产品".$product->name."版本".$productplan->title."还未配置使用台账，检测到历史版本".$item->title."使用台账如下，可在此基础上编辑保存~";
                        break;
                    }
                }
            }

        }
        echo $tips;
    }

    /**
     * 版本排序
     * @param $array
     * @return void
     */
    public function sortVersion($array){
        for($i = 0; $i<count($array); $i=$i+1){
            for($j = $i+1; $j<count($array)-1; $j=$j+1){
                if(!$this->compareVersion($array[$i]->title, $array[$j]->title)){
                    $temp = $array[$i];
                    $array[$i] = $array[$j];
                    $array[$j] = $temp;
                }
            }
        }
    }

    /**
     * 版本比较
     * @param $version1
     * @param $version2
     * @return void
     */
    public function compareVersion($version1, $version2){
        preg_match("/(?:version|v|)\s*((?:[0-9]+\.?)+)/i", $version1, $matches1);
        $version1 = $matches1[1];
        preg_match("/(?:version|v|)\s*((?:[0-9]+\.?)+)/i", $version2, $matches2);
        $version2 = $matches2[1];
        $versionArray1 = explode(".", $version1);
        $versionArray2 = explode(".", $version2);
        for($i=0;$i<count($versionArray1) and $i<count($versionArray2); $i=$i+1){
            if((int)$versionArray1[$i]>(int)$versionArray2[$i]){
                return true;
            }else if((int)$versionArray1[$i]<(int)$versionArray2[$i]){
                return false;
            }
        }
        return count($versionArray1)>=count($versionArray2);
    }

    /**
     * Project: chengfangjinke
     * Desc: 导出列表页数据 Excel
     * liuyuhan
     */
    public function export($orderBy = 'startYear_desc', $browseType = 'all')
    {
        /* format the fields of every data in order to export data. */
        $this->app->loadLang('component');
        unset($this->lang->exportTypeList['selected']);
        $this->lang->exportTypeList['all'] = '全部查询结果';
        if($_POST)
        {
            $this->loadModel('file');
            $componentpublicaccountLang   = $this->lang->componentpublicaccount;
            $componentpublicaccountConfig = $this->config->componentpublicaccount;

            /* Create field lists. */
            $fields = $this->post->exportFields ? $this->post->exportFields : explode(',', $componentpublicaccountConfig->list->exportFields);
            foreach($fields as $key => $fieldName)
            {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = isset($componentpublicaccountLang->$fieldName) ? $componentpublicaccountLang->$fieldName : $fieldName;
                unset($fields[$key]);
            }

            /* Get datas. */
            $datas = array();
            if($this->session->componentpublicaccountOnlyCondition)
            {
                $datas = $this->dao->select('t1.id,t2.maintainerDept,t1.componentId,t1.componentVersion,t1.projectName,t1.projectDept,t3.owner,t2.level,t2.category,t1.startYear,t1.startQuarter,t1.createdDate')->from(TABLE_COMPONENT_PUBLIC_ACCOUNT)->alias('t1')
                    ->leftJoin(TABLE_COMPONENT_RELEASE)->alias('t2')->on('t1.componentId = t2.id')
                    ->leftJoin(TABLE_PROJECTPLAN)->alias('t3')->on('t1.projectName = t3.project')
                    ->where($this->session->componentpublicaccountOnlyCondition)
                    ->andWhere('t1.deleted')->eq('0')
                    ->beginIF($this->post->exportType == 'selected')->andWhere('id')->in($this->cookie->checkedItem)->fi()
                    ->orderBy($orderBy)
                    ->fetchAll();

            }
            else
            {
                $stmt = $this->dbh->query($this->session->componentpublicaccountExportQuery . ($this->post->exportType == 'selected' ? " AND id IN({$this->cookie->checkedItem})" : '') . " ORDER BY " . strtr('startTime_desc', '_', ' '));
                while($row = $stmt->fetch()) $datas[$row->id] = $row;

            }

            $depts              = $this->loadModel('dept')->getOptionMenu();
            $users              = $this->loadModel('user')->getPairs('noletter');
            $versions           = $this->dao->select('id,version')->from(TABLE_COMPONENT_VERSION)->where('deleted')->eq(0)->fetchPairs();
            $projects           = $this->dao->select('project,name')->from(TABLE_PROJECTPLAN)->where('deleted')->eq(0)->fetchPairs();
            $componentNames     = $this->dao->select('id,name')->from(TABLE_COMPONENT_RELEASE)->where('deleted')->eq(0)->orderBy('id_desc')->fetchPairs();
            foreach ($datas as $k=>$data)
            {
                $data->componentDept        = zget($depts, $data->maintainerDept,'');
                $data->componentname        = zget($componentNames, $data->componentId,'');
                $data->componentversion     = zget($versions, $data->componentVersion,'');
                $data->projectName          = zget($projects, $data->projectName,'');
                $data->projectDept          = zget($depts, $data->projectDept,'');
                $data->projectManager       = zmget($users, $data->owner,'');
                $data->componentlevel       = zget($this->lang->componentpublicaccount->levelList, $data->level,'');
                $data->componentcategory    = zget($this->lang->component->categoryList, $data->category,'');
                $data->startTime            = $data->startYear.'年度--第'.$data->startQuarter.'季度';
                $data->createTime           = $data->createdDate;
            }
            $this->post->set('fields', $fields);
            $this->post->set('rows', $datas);
            $this->post->set('kind', 'componentpublicaccount');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        $this->view->fileName        = $this->lang->componentpublicaccount->exportExcel.'-'.time();
        $this->view->allExportFields = $this->config->componentpublicaccount->list->exportFields;
        $this->view->customExport    = true;
        $this->display();
    }
}