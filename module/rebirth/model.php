<?php
class rebirthModel extends model
{
    public function getApplicationByID($applicationID)
    {
        return $this->dao->select('*')->from(TABLE_APPLICATION)
            ->where('id')->eq($applicationID)
            ->fetch();
    }

    public function getApplicationPairs()
    {
        $applications = $this->dao->select('id,name')->from(TABLE_APPLICATION)
            ->where('deleted')->eq(0)
            ->orderBy('id_desc')
            ->fetchPairs();

        // 过滤掉没有被产品关联的所属系统。
        $applicationIdList = array_keys($applications);
        $applicationIdList = implode(',', $applicationIdList);
        $groupData = $this->dao->query("select app,count(id) as count from zt_product where app in ($applicationIdList) and deleted = '0' group by app;")->fetchAll();

        $applicationData = array();
        foreach($groupData as $app) $applicationData[$app->app] = $app->count;
        foreach($applications as $id => $name) if(!isset($applicationData[$id])) unset($applications[$id]);

        return $applications;
    }

    public function getProductPairs($applicationID, $showAll = false)
    {
        $productList        = array();
        $productList['all'] = $this->lang->rebirth->allProduct;
        $productList['na']  = $this->lang->rebirth->naProduct;

        $productPairs = $this->dao->select('id,name')->from(TABLE_PRODUCT)
            ->where('app')->eq($applicationID)
            ->andWhere('deleted')->eq(0)
            ->orderBy('id_desc')
            ->fetchPairs();
        if(count($productPairs) == 1) return $productPairs;
        $productList += $productPairs;

        if($showAll) unset($productList['all']);
        return $productList;
    }

    public function getProjectProductPairs($applicationID, $projectID)
    {
        $productList       = array();
        $productList['na'] = $this->lang->rebirth->naProduct;

        // 判断所属应用系统下有多少产品与当前项目关联，并判断是否显示NA产品。
        $appProductPairs = $this->dao->select('id,name')->from(TABLE_PRODUCT)
            ->where('app')->eq($applicationID)
            ->andWhere('deleted')->eq(0)
            ->orderBy('id_desc')
            ->fetchPairs();

        $productIdList  = array_keys($appProductPairs);
        $linkedProducts = $this->dao->select('product')->from(TABLE_PROJECTPRODUCT)->where('project')->eq($projectID)->andWhere('product')->in($productIdList)->fetchPairs();
        $productPairs   = $this->dao->select('id,name')->from(TABLE_PRODUCT)->where('id')->in($linkedProducts)->fetchPairs();

        if(count($appProductPairs) > 1) $productPairs += $productList;

        return $productPairs;
    }

    public function getProjectProductPairsByProjectID($projectID)
    {
        $productList        = array();
        $productList['all'] = $this->lang->rebirth->allProduct;
        $productList['na']  = $this->lang->rebirth->naProduct;

        $linkedProducts = $this->dao->select('product')->from(TABLE_PROJECTPRODUCT)->where('project')->eq($projectID)->fetchPairs();
        $productPairs   = $this->dao->select('id,name')->from(TABLE_PRODUCT)->where('id')->in($linkedProducts)->fetchPairs();

        return $productList + $productPairs;
    }

    public function getAllProductIdList($applicationID, $addZero = true)
    {
        $productList = $this->dao->select('id,name')->from(TABLE_PRODUCT)
            ->where('app')->eq($applicationID)
            ->andWhere('deleted')->eq(0)
            ->orderBy('id_desc')
            ->fetchPairs();

        $idList = array_keys($productList);
        if(count($idList) > 1 and $addZero) $idList[] = 0;

        return $idList;
    }

    public function getProductIdList($applicationID, $productID = 'all')
    {
        if($productID == 'na')
        {
            $idList = array(0);
        }
        elseif($productID == 'all')
        {
            $productList = $this->dao->select('id,name')->from(TABLE_PRODUCT)
                ->where('app')->eq($applicationID)
                ->andWhere('deleted')->eq(0)
                ->orderBy('id_desc')
                ->fetchPairs();

            $idList = array_keys($productList);
            if(count($idList) > 1) $idList[] = 0;
        }
        else
        {
            $idList = array($productID);
        }

        return $idList;
    }

    /**
     * Set menu.
     *
     * @param  int    $applicationID
     * @param  int    $productID
     * @param  int    $branch
     * @param  string $extra
     * @access public
     * @return void
     */
    public function setMenu($applicationID, $productID = 'all', $branch = 0, $extra = '')
    {
        if(!in_array($this->app->rawModule, $this->config->qa->noDropMenuModule)) $this->lang->switcherMenu = $this->getSwitcher($applicationID, $productID, $branch, $extra);
        common::setMenuVars('qa', $applicationID, array('applicationID' => $applicationID, 'productID' => $productID));
    }

    /**
     * Save the application id user last visited to session.
     *
     * @param  array $applications
     * @param  int   $applicationID
     * @param  array $productID
     * @access public
     * @return int
     */
    public function saveState($applications, $applicationID, $productID = 0)
    {
        if($applicationID > 0)                                          $this->session->set('applicationID', (int)$applicationID);
        if($applicationID == 0 and $this->cookie->lastApplicationID)    $this->session->set('applicationID', (int)$this->cookie->lastApplicationID);
        if($applicationID == 0 and $this->session->applicationID == '') $this->session->set('applicationID', key($applications));

        if(!isset($applications[$this->session->applicationID]))
        {
            $this->session->set('applicationID', key($applications));
        }

        $applicationID = $this->session->applicationID;
        setcookie("lastApplicationID", $applicationID, $this->config->cookieLife, $this->config->webRoot, '', $this->config->cookieSecure, true);

        return $applicationID;
    }

    /*
     * Get product switcher.
     *
     * @param  int    $applicationID
     * @param  int    $productID
     * @param  string $extra
     * @param  int    $branch
     * @access public
     * @return void
     */
    public function getSwitcher($applicationID, $productID = 0, $branch = 0, $extra = '')
    {
        $currentModule = $this->app->moduleName;
        $currentMethod = $this->app->methodName;

        /* Init currentModule and currentMethod for report and story. */
        if($currentModule == 'story')
        {
            $storyMethods = ",track,create,batchcreate,batchclose,zerocase,";
            if (strpos($storyMethods, "," . $currentMethod . ",") === false) $currentModule = 'product';
            if ($currentMethod == 'view' or $currentMethod == 'change' or $currentMethod == 'review') $currentMethod = 'browse';
        }

        if($currentModule == 'testcase' and $currentMethod == 'view') $currentMethod = 'browse';
        if($currentMethod == 'report') $currentMethod = 'browse';
        if($currentModule == 'tree' and $currentMethod == 'browse')
        {
            if($extra == 'bug')
            {
                $currentModule = 'bug';
                $currentMethod = 'browse';
            }
            elseif($extra == 'case')
            {
                $currentModule = 'testcase';
                $currentMethod = 'browse';
            }
        }

        $application        = $this->dao->select('id,name')->from(TABLE_APPLICATION)->where('id')->eq($applicationID)->fetch();
        $currentProductName = $application->name;

        $fromModule   = $this->lang->navGroup->qa == 'qa' ? 'qa' : '';
        $dropMenuLink = helper::createLink('rebirth', 'ajaxGetDropMenu', "applicationID={$applicationID}&productID={$productID}&module=$currentModule&method=$currentMethod&extra=$extra&from=$fromModule");

        $output = "<div class='btn-group header-btn' id='swapper'><button data-toggle='dropdown' type='button' class='btn' id='currentItem' title='{$currentProductName}'><span class='text'>{$currentProductName}</span> <span class='caret' style='margin-top: 3px'></span></button><div id='dropMenu' class='dropdown-menu search-list' data-ride='searchList' data-url='$dropMenuLink'>";
        $output .= '<div class="input-control search-box has-icon-left has-icon-right search-example"><input type="search" class="form-control search-input" /><label class="input-control-icon-left search-icon"><i class="icon icon-search"></i></label><a class="input-control-icon-right search-clear-btn"><i class="icon icon-close icon-sm"></i></a></div>';
        $output .= "</div></div>";

        $dropMenuLink = helper::createLink('rebirth', 'ajaxGetDropMenuProduct', "applicationID={$applicationID}&productID={$productID}&module=$currentModule&method=$currentMethod&extra=$extra&from=$fromModule");
        $productPairs = $this->getProductPairs($applicationID);
        if(empty($productID) or count($productPairs) === 1) $productID = key($productPairs);
        $productName  = zget($productPairs, $productID, '');

        $output .= "<div class='btn-group header-btn'><button id='currentBranch' data-toggle='dropdown' type='button' class='btn'><span class='text' title='{$productName}'>{$productName}</span> <span class='caret' style='margin-top: 3px'></span></button><div id='dropMenu' class='dropdown-menu search-list' data-ride='searchList' data-url='$dropMenuLink'>";
        $output .= '<div class="input-control search-box has-icon-left has-icon-right search-example"><input type="search" class="form-control search-input" /><label class="input-control-icon-left search-icon"><i class="icon icon-search"></i></label><a class="input-control-icon-right search-clear-btn"><i class="icon icon-close icon-sm"></i></a></div>';
        $output .= "</div></div>";

        return $output;
    }

    public function selectProduct($projectID, $applicationID = 0, $productID = 'all', $objectType = '')
    {
        $currentModule = 'project';
        $currentMethod = $objectType;

        if($productID == 'all')
        {
            $productName = $this->lang->rebirth->allProduct;
        }
        elseif($productID)
        {
            $product = $this->dao->select('id,name')->from(TABLE_PRODUCT)->where('id')->eq($productID)->fetch();
            $productName = $product->name;
        }
        elseif($productID == '0')
        {
            $productName = $this->lang->naProduct;
        }

        $output = '';
        if($applicationID)
        {
            $application = $this->dao->select('id,name')->from(TABLE_APPLICATION)->where('id')->eq($applicationID)->fetch();
            $output .= '<div class="btn-group angle-btn"><div class="btn-group">';
            $output .= '<button data-toggle="dropdown" type="button" class="btn btn-limit" id="currentItem" title="' . $application->name . '" style="border-radius: 2px;">';
            $output .= '<div class="nobr">' . $application->name . '</div></div></div>';
        }

        $dropMenuLink = helper::createLink('rebirth', 'ajaxGetDropMenuProject', "projectID=$projectID&applicationID=$applicationID&productID=$productID&module=$currentModule&method=$currentMethod&objectType=$objectType");
        $output .= "<div class='btn-group angle-btn'><div class='btn-group'><button data-toggle='dropdown' type='button' class='btn btn-limit' id='currentItem' title='{$productName}'><span class='text'>{$productName}</span> <span class='caret'></span></button><div id='dropMenu' class='dropdown-menu search-list' data-ride='searchList' data-url='$dropMenuLink'>";

        $output .= '<div class="input-control search-box has-icon-left has-icon-right search-example"><input type="search" class="form-control search-input" /><label class="input-control-icon-left search-icon"><i class="icon icon-search"></i></label><a class="input-control-icon-right search-clear-btn"><i class="icon icon-close icon-sm"></i></a></div>';
        $output .= "</div></div>";

        $output .= '</div>';

        return $output;
    }

    public function getApplicationLink($module, $method)
    {
        $link = '';
        $link = helper::createLink($module, 'browse', "applicationID=%s");
        return $link;
    }

    public function setParamsForLink($module, $link, $applicationID)
    {
        $linkHtml = sprintf($link, $applicationID);
        return $linkHtml;
    }

    public function getProductLink($module, $method, $extra)
    {
        $link = helper::createLink($module, $method, "applicationID=%s&productID=%s");
        if($module == 'bug' && in_array($method, array('view', 'edit', 'batchedit', 'browse')))
        {
            if($extra == 'testtaskbug')
            {
                $link = helper::createLink('testtask', 'browse', "applicationID=%s&productID=%s");
            }
            else
            {
                $link = helper::createLink('bug', 'browse', "applicationID=%s&productID=%s");
            }
        }
        elseif($module == 'testcase' && in_array($method, array('view', 'edit', 'batchedit', 'importfromlib')))
        {
            $link = helper::createLink('testcase', 'browse', "applicationID=%s&productID=%s");
        }
        elseif($module == 'testtask' && in_array($method, array('view', 'edit', 'cases', 'linkcase', 'linkbug')))
        {
            $link = helper::createLink('testtask', 'browse', "applicationID=%s&productID=%s");
        }
        elseif($module == 'testsuite' && in_array($method, array('view', 'edit', 'linkcase')))
        {
            $link = helper::createLink('testsuite', 'browse', "applicationID=%s&productID=%s");
        }
        elseif($module == 'testreport' && in_array($method, array('view', 'edit')))
        {
            $link = helper::createLink('testreport', 'browse', "applicationID=%s&productID=%s");
        }
        elseif($module == 'tree' && in_array($extra, array('bug', 'case')))
        {
            $link = helper::createLink($extra, 'browse', "applicationID=%s&productID=%s");
        }

        return $link;
    }

    public function getProjectLink($module, $method)
    {
        $link = helper::createLink($module, $method, "projectID=%s&applicationID=%s&productID=%s");
        if($module == 'project' && in_array($method, array('bug')))
        {
            $link = helper::createLink($module, $method, "projectID=%s&applicationID=%s&productID=%s");
        }

        return $link;
    }

    public function setParamsForProjectLink($module, $link, $projectID, $applicationID, $productID)
    {
        $linkHtml = sprintf($link, $projectID, $applicationID, $productID);
        return $linkHtml;
    }

    public function setProductParamsForLink($module, $link, $applicationID, $productID)
    {
        $linkHtml = sprintf($link, $applicationID, $productID);
        return $linkHtml;
    }

    public function getProductIdByApplication($applicationID, $productID)
    {
        $productIdList = $this->getAllProductIdList($applicationID);
        if(count($productIdList) == 1) $productID = $productIdList[0];
        return $productID;
    }

    public function getProductLinkProjectPairs($applicationID, $productID = 'all')
    {
        $projects = array();
        if($productID == 'na' or $productID == 'all' or empty($productID))
        {
            $productIdList = $this->getAllProductIdList($applicationID);
            $projects = $this->dao->select('t2.id,t2.name')->from(TABLE_PROJECTPRODUCT)->alias('t1')
                ->leftJoin(TABLE_PROJECT)->alias('t2')->on('t1.project = t2.id')
                ->where('t1.product')->in($productIdList)
                ->andWhere('t2.type')->eq('project')
                ->andWhere('t2.deleted')->eq('0')
                ->fetchPairs();
        }
        elseif(!empty($productID))
        {
            $projects = $this->dao->select('t2.id,t2.name')->from(TABLE_PROJECTPRODUCT)->alias('t1')
                ->leftJoin(TABLE_PROJECT)->alias('t2')->on('t1.project = t2.id')
                ->where('t1.product')->in($productID)
                ->andWhere('t2.type')->eq('project')
                ->andWhere('t2.deleted')->eq('0')
                ->fetchPairs();
        }

        return $projects;
    }

    public function getProjectLinkProductPairs($projectID, $applicationID, $objectType)
    {
        $objectTables = array('bug' => TABLE_BUG, 'testcase' => TABLE_CASE, 'testtask' => TABLE_TESTTASK, 'testreport' => TABLE_TESTREPORT);
        $queryTable   = zget($objectTables, $objectType);
        
        $products = array('0-all' => $this->lang->rebirth->allProduct);
        if($objectType != 'testsuite')
        {
            $groupData = $this->dao->query("select applicationID,product from $queryTable where project = $projectID and deleted = '0' group by applicationID,product;")->fetchAll();

            /* 从数据对象中获取的关联产品。*/
            $productIdList     = array();
            $applicationIdList = array();
            foreach($groupData as $group)
            {
                $productIdList[]     = $group->product;
                $applicationIdList[] = $group->applicationID;
            }

            $objectApplications = $this->dao->select('id,name')->from(TABLE_APPLICATION)->where('id')->in($applicationIdList)->fetchPairs();
            $objectProducts     = $this->dao->select('id,name')->from(TABLE_PRODUCT)->where('id')->in($productIdList)->andWhere('deleted')->eq('0')->fetchPairs();

            foreach($groupData as $group)
            {
                $key = $group->applicationID . '-' . $group->product;

                if($group->product)
                {
                    $products[$key] = zget($objectProducts, $group->product, $group->product);
                }
                else
                {
                    $products[$key] = $this->lang->naProduct . ' - ' . zget($objectApplications, $group->applicationID);
                }
            }
        }

        /* 获取项目直接关联的产品。*/
        $linkedProducts = $this->dao->select('product')->from(TABLE_PROJECTPRODUCT)->where('project')->eq($projectID)->fetchPairs();
        $productPairs   = $this->dao->select('id,app')->from(TABLE_PRODUCT)->where('id')->in($linkedProducts)->andWhere('deleted')->eq('0')->fetchAll();

        $productIdList     = array();
        $applicationIdList = array();
        foreach($productPairs as $product)
        {
            $productIdList[]     = $product->id;
            $applicationIdList[] = $product->app;
        }

        $linkedProducts     = $this->dao->select('id,name')->from(TABLE_PRODUCT)->where('id')->in($productIdList)->andWhere('deleted')->eq('0')->fetchPairs();
        foreach($productPairs as $product)
        {
            $key = $product->app . '-' . $product->id;
            $products[$key] = zget($linkedProducts, $product->id, $product->id);
        }

        return $products;
    }

    public function getProjectLinkProductList($projectID, $applicationID, $objectType)
    {
        $products = $this->getProjectLinkProductPairs($projectID, $applicationID, $objectType);

        $applicationIDList = [];
        $productIdList     = [];
        $productsPairs     = [];

        foreach($products as $key => $productName)
        {
            $keyList = explode('-', $key);

            $applicationIDList[]        = $keyList[0];
            $productIdList[]            = $keyList[1];
            $productsPairs[$keyList[1]] = $productName;
        }

        $applicationIDList = array_unique($applicationIDList);

        return [
            'applicationIDList' => $applicationIDList,
            'productIdList'     => $productIdList,
            'productsPairs'     => $productsPairs
        ];
    }

    public function getShowProductPairs($projectID, $applicationID, $objectType)
    {
        $objectTables = array('bug' => TABLE_BUG, 'testcase' => TABLE_CASE);
        $queryTable   = zget($objectTables, $objectType);
        
        $products = array();
        if($objectType != 'testsuite')
        {
            $groupData = $this->dao->query("select applicationID,product from $queryTable where project = $projectID and deleted = '0' group by applicationID,product;")->fetchAll();

            /* 从数据对象中获取的关联产品。*/
            $productIdList = array();
            foreach($groupData as $group) $productIdList[] = $group->product;

            $objectProducts = $this->dao->select('id,name')->from(TABLE_PRODUCT)->where('id')->in($productIdList)->andWhere('deleted')->eq('0')->fetchPairs();
            $products = $objectProducts;
        }

        /* 获取项目直接关联的产品。*/
        $linkedProducts = $this->dao->select('product')->from(TABLE_PROJECTPRODUCT)->where('project')->eq($projectID)->fetchPairs();
        if(!empty($linkedProducts))
        {
            $linkedProducts = $this->dao->select('id,name')->from(TABLE_PRODUCT)->where('id')->in($linkedProducts)->andWhere('deleted')->eq('0')->fetchPairs();
            foreach($linkedProducts as $id => $name) $products[$id] = $name;
        }

        return $products;
    }
}
