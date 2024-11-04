<?php
class rebirth extends control
{
    public function ajaxGetDropMenu($applicationID, $productID, $module, $method, $extra = '', $from = '')
    {
        if($from == 'qa')
        {
            $this->app->loadConfig('qa');
            foreach($this->config->qa->menuList as $menu) $this->lang->navGroup->$menu = 'qa';
        }

        $applications = $this->rebirth->getApplicationPairs();

        // 过滤掉没有被产品关联的所属系统。
        $applicationIdList = array_keys($applications);
        $applicationIdList = implode(',', $applicationIdList);
        $groupData = $this->dao->query("select app,count(id) as count from zt_product where app in ($applicationIdList) and deleted = '0' group by app;")->fetchAll();

        $applicationData = array();
        foreach($groupData as $app) $applicationData[$app->app] = $app->count;
        foreach($applications as $id => $name) if(!isset($applicationData[$id])) unset($applications[$id]);

        $this->view->link          = $this->rebirth->getApplicationLink($module, $method);
        $this->view->applicationID = $applicationID;
        $this->view->productID     = $productID;
        $this->view->module        = $module;
        $this->view->method        = $method;
        $this->view->extra         = $extra;
        $this->view->applications  = $applications;
        $this->view->openApp       = $this->app->openApp;

        $this->display();
    }

    public function ajaxGetDropMenuProduct($applicationID, $productID, $module, $method, $extra = '', $from = '')
    {
        if($from == 'qa')
        {
            $this->app->loadConfig('qa');
            foreach($this->config->qa->menuList as $menu) $this->lang->navGroup->$menu = 'qa';
        }

        $products = $this->rebirth->getProductPairs($applicationID);

        $this->view->link          = $this->rebirth->getProductLink($module, $method, $extra);
        $this->view->applicationID = $applicationID;
        $this->view->productID     = $productID;
        $this->view->module        = $module;
        $this->view->method        = $method;
        $this->view->extra         = $extra;
        $this->view->products      = $products;
        $this->view->openApp       = $this->app->openApp;

        $this->display();
    }

    public function ajaxGetDropMenuProject($projectID, $applicationID, $productID, $module, $method, $objectType = '')
    {
        $products = $this->rebirth->getProjectLinkProductPairs($projectID, $applicationID, $objectType);

        $this->view->products      = $products;
        $this->view->link          = $this->rebirth->getProjectLink($module, $method);
        $this->view->projectID     = $projectID;
        $this->view->applicationID = $applicationID;
        $this->view->productID     = $productID;
        $this->view->module        = $module;
        $this->view->method        = $method;
        $this->view->objectType    = $objectType;
        $this->view->openApp       = $this->app->openApp;

        $this->display();
    }

    public function ajaxGetProductByApplication($applicationID, $browseType = '', $showAll = 'show', $showNa = 'show')
    {
        $products  = $this->rebirth->getProductPairs($applicationID);
        $extraAttr = '';
        if($browseType == 'edit')     $extraAttr = "onchange='loadAll(this.value)'";
        if($browseType == 'resolved') $extraAttr = "onchange='ajaxProjectByProduct(this.value)'";
        if('show' != $showAll) unset($products['all']);
        if('show' != $showNa)  unset($products['na']);
        die(html::select('product', array('' => '') + $products, '', "class='form-control' $extraAttr"));
    }

    public function ajaxProjectByProduct($applicationID, $productID = 0, $browseType = '', $projectID = 0)
    {
        $projects = array('' => '');
        if(!empty($productID)) $projects += $this->rebirth->getProductLinkProjectPairs($applicationID, $productID);
        $extraAttr = '';

        $productID = (int)$productID;
        if($browseType == 'bugCommon') $extraAttr = "onchange='loadProductExecutions($productID, this.value)'";
        if($browseType == 'testcase')  $extraAttr = "onchange='getProjectExecutions(this.value)'";

        die(html::select('project', $projects, $projectID, "class='form-control' $extraAttr"));
    }
}
