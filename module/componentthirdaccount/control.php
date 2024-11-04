<?php
class componentthirdaccount extends control
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
    public function browse($browseType = 'all', $param = 0, $orderBy = 'componentReleaseId_asc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $this->app->loadLang('component');
        $this->app->loadLang('componentthird');
        $browseType = strtolower($browseType);
        //搜索框的值
        $depts = $this->loadModel('dept')->getOptionMenu();
        $depts[0] = '';
        $this->config->componentthirdaccount->search['params']['productdept']['values'] = $depts;

        $apps = $this->loadModel('application')->getPairs();
        $this->config->componentthirdaccount->search['params']['appname']['values'] = array('' => '') + $apps;

        $products = $this->loadModel('product')->getNamePairs();
        $this->config->componentthirdaccount->search['params']['productname']['values'] = array('' => '') + $products;

        $components = $this->loadModel('componentthird')->getPairs();
        $this->config->componentthirdaccount->search['params']['componentname']['values'] = array('' => '') + $components;

        $this->config->componentthirdaccount->search['params']['vulnerabilityLevel']['values'] = array('' => '') + $this->lang->componentthird->vulnerabilityLevelList;

        $querystr = $this->session->componentthirdaccountQuery;
        $queryForm = $this->session->componentthirdaccountForm;

        if(strpos($querystr, 'appname') !== false ){
            foreach ($queryForm as $key=>$value){
                if($value == 'appname'){
                    $index = explode("field", $key);
                    $appId = $queryForm['value'.$index[1]];
                    break;
                }
            }
            $products = $this->dao->select('id,name')
                ->from(TABLE_PRODUCT)
                ->where('deleted')->eq(0)
                ->andWhere('app')->eq($appId)
                ->orderBy('id_desc')
                ->fetchPairs('id', 'name');
            $products = array('' => '') +  $products;
            $this->config->componentthirdaccount->search['params']['productname']['values'] = array('' => '') + $products;
        }

        if(strpos($querystr, 'productversion') !== false or strpos($querystr, 'productname') !== false){
            foreach ($queryForm as $key=>$value){
                if($value == 'productname'){
                    $index = explode("field", $key);
                    $productId = $queryForm['value'.$index[1]];
                    break;
                }
            }
            $plans = $this->dao->select('id,title')
                ->from(TABLE_PRODUCTPLAN)
                ->where('deleted')->eq(0)
                ->andWhere('product')->eq($productId)
                ->orderBy('id_desc')
                ->fetchPairs('id', 'title');
            $plans = array('' => '') +  $plans;
            $this->config->componentthirdaccount->search['params']['productversion']['values'] = array('' => '') + $plans;
        }

        if(strpos($querystr, 'componentversion') !== false or strpos($querystr, 'componentname') !== false){
            foreach ($queryForm as $key=>$value){
                if($value == 'componentname'){
                    $index = explode("field", $key);
                    $componentId = $queryForm['value'.$index[1]];
                    break;
                }
            }
            $versions = $this->dao->select('id,version')
                ->from(TABLE_COMPONENT_VERSION)
                ->where('deleted')->eq(0)
                ->andWhere('componentReleaseId')->eq($componentId)
                ->orderBy('id_desc')
                ->fetchPairs('id', 'version');
            $versions = array('' => '') +  $versions;
            $this->config->componentthirdaccount->search['params']['componentversion']['values'] = array('' => '') + $versions;
        }


        $queryID = ($browseType == 'bysearch')  ? (int)$param : 0;
        $actionURL = $this->createLink('componentthirdaccount', 'browse', "browseType=bySearch&param=myQueryID");
        $this->componentthirdaccount->buildSearchForm($queryID, $actionURL);

        /* 设置详情页面返回的url连接。*/
        $this->session->set('componentthirdaccountList', $this->app->getURI(true));
        $this->session->set('common_back_url', $this->app->getURI(true),'backlog');
        $this->session->set('componentthirdaccountHistory', $this->app->getURI(true));

        /* 构建pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);
        $componentthirdaccountList = $this->componentthirdaccount->getList($browseType, $queryID, $orderBy, $pager,$param);
        $code = $recPerPage*($pageID-1)+1;
        foreach($componentthirdaccountList as $item){
            $item->code = $code;
            $code = $code + 1;
        }
        $this->view->title      = $this->lang->componentthirdaccount->common;
        $this->view->orderBy    = $orderBy;
        $this->view->pager      = $pager;
        $this->view->param      = $param;
        $this->view->browseType = $browseType;
        $this->view->datas   = $componentthirdaccountList;
        $this->display();
    }

    /**
     * 新建公共组件台账
     * @return void
     */
    public function create(){
        if($_POST){
            $this->componentthirdaccount->create();
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }


            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = inlink('browse');
            $this->send($response);

        }
        $this->view->title       = $this->lang->componentthirdaccount->create;
        $apps = $this->loadModel('application')->getPairs();
        $this->view->apps       = array('' => '') + $apps;
        $products = $this->loadModel('product')->getNamePairs();
        $this->view->products = array('' => '') + $products;
        $components = $this->loadModel('componentthird')->getPairs();
        $this->view->components = array('' => '') + $components;
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
     * 新建页面根据产品获取版本
     * @param $id
     * @return void
     */
    public function ajaxGetVersionByProductcreate($id){
        $plans = $this->dao->select('id,title')
            ->from(TABLE_PRODUCTPLAN)
            ->where('deleted')->eq(0)
            ->andWhere('product')->eq($id)
            ->orderBy('id_desc')
            ->fetchPairs('id', 'title');
        $plans = array('' => '') +  $plans;
        echo html::select('productversion', $plans, '',"class='form-control chosen' onchange='selectProductVersion(this.value)'");
    }

    /**
     * 新建根据组件获取版本
     * @param $id
     * @return void
     */
    public function ajaxGetVersionByComponentcreate($id, $index){
        $plans = $this->dao->select('id,version')
            ->from(TABLE_COMPONENT_VERSION)
            ->where('deleted')->eq(0)
            ->andWhere('componentReleaseId')->eq($id)
            ->orderBy('id_desc')
            ->fetchPairs('id', 'version');
        $plans = array('' => '') +  $plans;
        echo html::select('componentversion['.$index.']', $plans, '',"class='form-control chosen'");
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
            ->andWhere('t2.type')->eq('third')
            ->orderBy('t1.id_desc')
            ->fetchAll('id');

        $cunstomaccounts = $this->dao->select('t1.*')
            ->from(TABLE_COMPONENT_ACCOUNT)->alias('t1')
            ->where('t1.deleted')->eq(0)
            ->andWhere('t1.productVersionId')->eq($id)
            ->andWhere('t1.customComponent')->isNotNull()
            ->andWhere('t1.customComponent')->ne('')
            ->orderBy('t1.id_desc')
            ->fetchAll('id');
        $components = $this->loadModel('componentthird')->getPairs();
        $components = array('' => '') + $components;
        $str = "";
        if(!empty($accounts)){
            //找到目标台账
            foreach($accounts as $i=>$item){
                $str = $this->buildHtmlByData($i, $accounts[$i], $components,$str);
            }
        }else if(empty($cunstomaccounts)){
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
                        foreach ($productplanList as $i=>$item){
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
                            ->andWhere('t2.type')->eq('third')
                            ->orderBy('t1.id_desc')
                            ->fetchAll('id');

                        $cunstomaccountLasts = $this->dao->select('t1.*')
                            ->from(TABLE_COMPONENT_ACCOUNT)->alias('t1')
                            ->where('t1.deleted')->eq(0)
                            ->andWhere('t1.productVersionId')->eq($item->id)
                            ->andWhere('t1.customComponent')->isNotNull()
                            ->andWhere('t1.customComponent')->ne('')
                            ->orderBy('t1.id_desc')
                            ->fetchAll('id');
                        if (!empty($accountLasts) or !empty($cunstomaccountLasts)) {
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
     * 新建获取台账信息
     * @param $id
     * @return void
     */
    public function ajaxGetCustomAccount($id,$isConnect){
        $cunstomaccounts = $this->dao->select('t1.*')
            ->from(TABLE_COMPONENT_ACCOUNT)->alias('t1')
            ->where('t1.deleted')->eq(0)
            ->andWhere('t1.productVersionId')->eq($id)
            ->andWhere('t1.customComponent')->isNotNull()
            ->andWhere('t1.customComponent')->ne('')
            ->orderBy('t1.id_desc')
            ->fetchAll('id');
        $accounts = $this->dao->select('t1.*')
            ->from(TABLE_COMPONENT_ACCOUNT)->alias('t1')
            ->innerJoin(TABLE_COMPONENT_RELEASE)->alias('t2')->on('t1.componentReleaseId = t2.id')
            ->where('t1.deleted')->eq(0)
            ->andWhere('t2.deleted')->eq(0)
            ->andWhere('t1.productVersionId')->eq($id)
            ->andWhere('t2.type')->eq('third')
            ->orderBy('t1.id_desc')
            ->fetchAll('id');
        $str = "";
        if(!empty($cunstomaccounts)){
            //找到目标台账
            foreach($cunstomaccounts as $i=>$item){
                $str = $this->buildCustomHtmlByData($i, $cunstomaccounts[$i],$str);
            }
        }else if(empty($accounts)){
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
                        foreach ($productplanList as $i=>$item){
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
                            ->where('t1.deleted')->eq(0)
                            ->andWhere('t1.productVersionId')->eq($item->id)
                            ->andWhere('t1.customComponent')->isNotNull()
                            ->andWhere('t1.customComponent')->ne('')
                            ->orderBy('t1.id_desc')
                            ->fetchAll('id');
                        $accountcomponents = $this->dao->select('t1.*')
                            ->from(TABLE_COMPONENT_ACCOUNT)->alias('t1')
                            ->innerJoin(TABLE_COMPONENT_RELEASE)->alias('t2')->on('t1.componentReleaseId = t2.id')
                            ->where('t1.deleted')->eq(0)
                            ->andWhere('t2.deleted')->eq(0)
                            ->andWhere('t1.productVersionId')->eq($item->id)
                            ->andWhere('t2.type')->eq('third')
                            ->orderBy('t1.id_desc')
                            ->fetchAll('id');
                        if (!empty($accountLasts) or !empty($accountcomponents)) {
                            foreach ($accountLasts as $i=>$item) {
                                $str = $this->buildCustomHtmlByData($i, $accountLasts[$i],$str);
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
            $item->customComponent = '';
            $item->customComponentVersion = '';
            $item->comment = '';
            $str = $this->buildCustomHtmlByData(0, $item,$str);
        }
        echo $str;
    }

    /**
     * 构建自定义组件html
     * @param $i
     * @param $item
     * @param $components
     * @param $plans
     * @return string
     */
    public function buildCustomHtmlByData($i, $item,$str){
        $str = $str."<div class='table-row custom-component-partitions'>";
        $str = $str."<div class='table-col w-250px'>";
        $str = $str."<div class='input-group'>";
        $str = $str."<span class='input-group-addon'>";
        $str = $str.$this->lang->componentthirdaccount->customComponent;
        $str = $str."</span>";
        $str = $str.html::input('customComponent['.$i.']', $item->customComponent, "id='customComponent".$i."' data-index='".$i."' class='form-control initCommentClass'");
        $str = $str."</div>";
        $str = $str."</div>";
        $str = $str."<div class='table-col w-250px'>";
        $str = $str."<div class='input-group'>";
        $str = $str."<span class='input-group-addon'>";
        $str =  $str.$this->lang->componentthirdaccount->customComponentVersion;
        $str = $str."</span>";
        $str = $str.html::input('customComponentVersion['.$i.']', $item->customComponentVersion, "id='customComponentVersion".$i."' data-index='".$i."' class='form-control initCommentClass'");
        $str = $str."</div>";
        $str = $str."</div>";
        $str = $str."<div class='table-col w-400px'>";
        $str = $str."<div class='input-group'>";
        $str = $str."<span class='input-group-addon fix-border fix-padding'>";
        $str =  $str.$this->lang->componentthirdaccount->comment;
        $str = $str."</span>";
        $str = $str.html::input('customcomment['.$i.']', $item->comment, "maxlength='40' id='customcomment0".$i."' data-index='".$i."' class='form-control initCommentClass'");
        $str = $str."<a class='input-group-btn' href='javascript:void(0)' onclick='addCustomPartition(this)' data-id='".$i."' id='addItem".$i."' class='btn btn-link'><i class='icon-plus'></i></a>";
        $str = $str."<a class='input-group-btn' href='javascript:void(0)' onclick='delCustomPartition(this)' class='btn btn-link'><i class='icon-close'></i></a>";
        $str = $str."</div>";
        $str = $str."</div>";
        $str = $str."</div>";
        return $str;
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
        $str = $str.$this->lang->componentthirdaccount->createcomponentname;
        $str = $str."</span>";
        $str = $str.html::select('componentname['.$i.']', $components, $item->componentReleaseId, "id='componentname".$i."' data-index='".$i."' class='form-control chosen initClass' onchange='selectComponent(this.value,this.id)'");
        $plans = $this->dao->select('id,version')
            ->from(TABLE_COMPONENT_VERSION)
            ->where('deleted')->eq(0)
            ->andWhere('componentReleaseId')->eq($item->componentReleaseId)
            ->orderBy('id_desc')
            ->fetchPairs('id', 'version');
        $plans = array(''=>'')+$plans;
        $str = $str."</div>";
        $str = $str."</div>";
        $str = $str."<div class='table-col w-250px'>";
        $str = $str."<div class='input-group'>";
        $str = $str."<span class='input-group-addon'>";
        $str =  $str.$this->lang->componentthirdaccount->createcomponentversion;
        $str = $str."</span>";
        $str = $str.html::select('componentversion['.$i.']', $plans, $item->componentVersionId, "id='componentversion".$i."' class='form-control chosen initClass'");
        $str = $str."</div>";
        $str = $str."</div>";
        $str = $str."<div class='table-col w-400px'>";
        $str = $str."<div class='input-group'>";
        $str = $str."<span class='input-group-addon fix-border fix-padding'>";
        $str =  $str.$this->lang->componentthirdaccount->comment;
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
        $cunstomaccounts = $this->dao->select('t1.*')
            ->from(TABLE_COMPONENT_ACCOUNT)->alias('t1')
            ->where('t1.deleted')->eq(0)
            ->andWhere('t1.productVersionId')->eq($id)
            ->andWhere('t1.customComponent')->isNotNull()
            ->andWhere('t1.customComponent')->ne('')
            ->orderBy('t1.id_desc')
            ->fetchAll('id');
        $accounts = $this->dao->select('t1.*')
            ->from(TABLE_COMPONENT_ACCOUNT)->alias('t1')
            ->innerJoin(TABLE_COMPONENT_RELEASE)->alias('t2')->on('t1.componentReleaseId = t2.id')
            ->where('t1.deleted')->eq(0)
            ->andWhere('t2.deleted')->eq(0)
            ->andWhere('t1.productVersionId')->eq($id)
            ->andWhere('t2.type')->eq('third')
            ->orderBy('t1.id_desc')
            ->fetchAll('id');
        if(empty($accounts) and empty($cunstomaccounts)){
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
                    foreach ($productplanList as $i => $item){
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
                        ->andWhere('t2.type')->eq('third')
                        ->orderBy('t1.id_desc')
                        ->fetchAll('id');
                    $cunstomaccountLasts = $this->dao->select('t1.*')
                        ->from(TABLE_COMPONENT_ACCOUNT)->alias('t1')
                        ->where('t1.deleted')->eq(0)
                        ->andWhere('t1.productVersionId')->eq($item->id)
                        ->andWhere('t1.customComponent')->isNotNull()
                        ->andWhere('t1.customComponent')->ne('')
                        ->orderBy('t1.id_desc')
                        ->fetchAll('id');
                    if (!empty($accountLasts) or !empty($cunstomaccountLasts)) {
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
    public function export($orderBy = 'id_desc', $browseType = 'all')
    {
        /* format the fields of every data in order to export data. */
        $this->app->loadLang('component');
        $this->app->loadLang('componentthird');
        unset($this->lang->exportTypeList['selected']);
        $this->lang->exportTypeList['all'] = '全部查询结果';
        if($_POST)
        {
            $this->loadModel('file');
            $componentthirdaccountLang   = $this->lang->componentthirdaccount;
            $componentthirdaccountConfig = $this->config->componentthirdaccount;

            /* Create field lists. */
            $fields = $this->post->exportFields ? $this->post->exportFields : explode(',', $componentthirdaccountConfig->list->exportFields);
            foreach($fields as $key => $fieldName)
            {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = isset($componentthirdaccountLang->$fieldName) ? $componentthirdaccountLang->$fieldName : $fieldName;
                unset($fields[$key]);
            }

            /* Get datas. */
            $datas = array();
            if($this->session->componentthirdaccountOnlyCondition)
            {
                $datas = $this->dao->select('*')->from(TABLE_COMPONENT_ACCOUNT)->where($this->session->componentthirdaccountOnlyCondition)
                    ->andWhere('deleted')->eq('0')
                    ->andWhere('type')->eq('third')
                    ->beginIF($this->post->exportType == 'selected')->andWhere('id')->in($this->cookie->checkedItem)->fi()
                    ->orderBy('id_desc')->fetchAll('id');
            }
            else
            {
                $stmt = $this->dbh->query($this->session->componentthirdaccountExportQuery . ($this->post->exportType == 'selected' ? " AND id IN({$this->cookie->checkedItem})" : '') . " ORDER BY " . strtr('id_desc', '_', ' '));
                while($row = $stmt->fetch()) $datas[$row->id] = $row;
            }

            foreach ($datas as $k=>$data)
            {
                //组件
                if(!empty($data->componentReleaseId)){
                    $component = $this->dao->findByID($data->componentReleaseId)->from(TABLE_COMPONENT_RELEASE)->fetch();
                    if($component->type == 'third'){
                        $data->componentname = $component->name;
                        $data->componentlevel = zget($this->lang->component->levelList, $component->level);
                        $data->componentcategory = zget($this->lang->component->categoryList, $component->category);

                        //系统名称
                        $application = $this->dao->findByID($data->appId)->from(TABLE_APPLICATION)->fetch();
                        $data->appname = $application->name;
                        //产品名称
                        $product = $this->dao->findById($data->productId)->from(TABLE_PRODUCT)->fetch();
                        $data->productname = $product->name;
                        //产品版本
                        $plan = $this->dao->findByID((int)$data->productVersionId)->from(TABLE_PRODUCTPLAN)->fetch();
                        $data->productversion = $plan->title;
                        //产品联系人和产品所属部门
                        $user = $this->dao->select('*')->from(TABLE_USER)->where("`account`")->eq($product->PO)->fetch();
                        if($user){
                            $data->productconnect = $user->realname;
                            $deptname = $this->loadModel('dept')->getCompleteName($user->dept);
                            $data->productdept = $deptname;
                        }else{
                            $data->productconnect = '';

                            $data->productdept = '';
                        }

                        //组件版本
                        if(!empty($data->componentVersionId)){
                            $componentversion = $this->dao->findByID($data->componentVersionId)->from(TABLE_COMPONENT_VERSION)->fetch();
                            $data->componentversion = $componentversion->version;
                            $data->vulnerabilityLevel = zget($this->lang->componentthird->vulnerabilityLevelList, $componentversion->vulnerabilityLevel);
                        }else{
                            $data->componentversion = '';
                            $data->vulnerabilityLevel = '';
                        }

                    }else{
                        unset($datas[$k]);
                    }
                }else{
                    $data->componentname = $data->customComponent;
                    $data->componentversion = $data->customComponentVersion;

                    //系统名称
                    $application = $this->dao->findByID($data->appId)->from(TABLE_APPLICATION)->fetch();
                    $data->appname = $application->name;
                    //产品名称
                    $product = $this->dao->findById($data->productId)->from(TABLE_PRODUCT)->fetch();
                    $data->productname = $product->name;
                    //产品版本
                    $plan = $this->dao->findByID((int)$data->productVersionId)->from(TABLE_PRODUCTPLAN)->fetch();
                    $data->productversion = $plan->title;
                    //产品联系人和产品所属部门
                    $user = $this->dao->select('*')->from(TABLE_USER)->where("`account`")->eq($product->PO)->fetch();
                    if($user){
                        $data->productconnect = $user->realname;
                        $deptname = $this->loadModel('dept')->getCompleteName($user->dept);
                        $data->productdept = $deptname;
                    }else{
                        $data->productconnect = '';

                        $data->productdept = '';
                    }
                    /*$dept = $this->dao->findById($user->dept)->from(TABLE_DEPT)->fetch();
                    $data->productdept = $dept->name;*/
                }

            }
            $ids = array_column($datas, 'componentlevel');
            array_multisort($ids, SORT_ASC, $datas);

            $this->post->set('fields', $fields);
            $this->post->set('rows', $datas);
            $this->post->set('kind', 'componentthirdaccount');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        $this->view->fileName        = $this->lang->componentthirdaccount->exportExcel.'-'.time();
        $this->view->allExportFields = $this->config->componentthirdaccount->list->exportFields;
        $this->view->customExport    = true;
        $this->display();
    }
}