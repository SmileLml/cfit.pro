<?php
/**
 * The control file of productplan module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     productplan
 * @version     $Id: control.php 4659 2013-04-17 06:45:08Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
class productplan extends control
{
    /**
     * Common actions.
     *
     * @param  int $productID
     * @param  int $branch
     *
     * @access public
     * @return void
     */
    public function commonAction($productID, $branch = 0)
    {
        $product = $this->loadModel('product')->getById($productID);
        if(empty($product)) $this->locate($this->createLink('product', 'create'));

        $this->app->loadConfig('execution');
        $this->product->setMenu($productID, $branch);
        $this->session->set('currentProductType', $product->type);

        $this->view->product  = $product;
        $this->view->branch   = $branch;
        $this->view->branches = $product->type == 'normal' ? array() : $this->loadModel('branch')->getPairs($productID);
        $this->view->position[] = html::a($this->createLink('product', 'browse', "productID={$productID}&branch=$branch"), $product->name);
    }

    /**
     * Create a plan.
     *
     * @param string $productID
     * @param int    $branchID
     *
     * @access public
     * @return void
     */
    public function create($productID = '', $branchID = 0, $parent = 0)
    {
        if(!empty($_POST))
        {
            $planID = $this->productplan->create();
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));
            $this->loadModel('action')->create('productplan', $planID, 'opened');

            $this->executeHooks($planID);

            if(isonlybody()) $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'closeModal' => true, 'callback' => 'parent.refreshPlan()'));
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $this->createLink('productplan', 'browse', "productID=$productID&branch=$branchID")));
        }

        $this->commonAction($productID, $branchID);
        $lastPlan = $this->productplan->getLast($productID, $branchID, $parent);

        if($lastPlan)
        {
            $timestamp = strtotime($lastPlan->end);
            $weekday   = date('w', $timestamp);
            $delta     = 1;
            if($weekday == '5' or $weekday == '6') $delta = 8 - $weekday;

            $begin = date('Y-m-d', strtotime("+$delta days", $timestamp));
        }
        $this->view->begin = $lastPlan ? $begin : date('Y-m-d');
        if($parent) $this->view->parentPlan = $this->productplan->getById($parent);

        $this->view->title      = $this->view->product->name . $this->lang->colon . $this->lang->productplan->create;
        $this->view->position[] = $this->lang->productplan->common;
        $this->view->position[] = $this->lang->productplan->create;

        $this->view->lastPlan = $lastPlan;
        $this->view->branch   = $branchID;
        $this->view->parent   = $parent;
        $this->display();
    }

    /**
     * Edit a plan.
     *
     * @param int $planID
     *
     * @access public
     * @return void
     */
    public function edit($planID)
    {
        if(!empty($_POST))
        {
            $changes = $this->productplan->update($planID);
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));
            if($changes)
            {
                $actionID = $this->loadModel('action')->create('productplan', $planID, 'edited');
                $this->action->logHistory($actionID, $changes);
            }
            $this->executeHooks($planID);
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => inlink('view', "planID=$planID")));
        }

        $plan = $this->productplan->getByID($planID);
        $this->commonAction($plan->product, $plan->branch);
        $this->view->title      = $this->view->product->name . $this->lang->colon . $this->lang->productplan->edit;
        $this->view->position[] = $this->lang->productplan->edit;
        $this->view->plan = $plan;
        $this->display();
    }

    /**
     * Batch edit plan.
     *
     * @param int $productID
     * @param int $branch
     *
     * @access public
     * @return void
     */
    public function batchEdit($productID, $branch = 0)
    {
        if(isset($_POST['planIDList']))
        {
            $this->commonAction($productID, $branch);
            $this->view->title      = $this->lang->productplan->batchEdit;
            $this->view->position[] = html::a(inlink('browse', "productID=$productID&branch=$branch"), $this->lang->productplan->common);
            $this->view->position[] = $this->lang->productplan->batchEdit;

            $this->view->plans = $this->productplan->getByIDList($this->post->planIDList);
            die($this->display());
        }
        elseif($_POST)
        {
            $changes = $this->productplan->batchUpdate($productID);
            $this->loadModel('action');
            foreach($changes as $planID => $change)
            {
                $actionID = $this->action->create('productplan', $planID, 'Edited');
                $this->action->logHistory($actionID, $change);
            }
            $this->loadModel('score')->create('ajax', 'batchOther');
            die(js::locate(inlink('browse', "productID=$productID&branch=$branch"), 'parent'));
        }
        die(js::locate('back'));
    }

    /**
     * Delete a plan.
     *
     * @param  int    $planID
     * @param  string $confirm  yes|no
     * @access public
     * @return void
     */
    public function delete($planID, $confirm = 'no')
    {
        $plan = $this->productplan->getById($planID);
        if($plan->parent < 0) die(js::alert($this->lang->productplan->cannotDeleteParent));

        if($confirm == 'no')
        {
            die(js::confirm($this->lang->productplan->confirmDelete, $this->createLink('productPlan', 'delete', "planID=$planID&confirm=yes")));
        }
        else
        {
            $this->productplan->delete(TABLE_PRODUCTPLAN, $planID);
            if($plan->parent > 0) $this->productplan->changeParentField($planID);
            $this->executeHooks($planID);

            /* if ajax request, send result. */
            if($this->server->ajax)
            {
                if(dao::isError())
                {
                    $response['result']  = 'fail';
                    $response['message'] = dao::getError();
                }
                else
                {
                    $response['result']  = 'success';
                    $response['message'] = '';
                }
                $this->send($response);
            }
            die(js::locate(inlink('browse', "productID=$plan->product&branch=$plan->branch"), 'parent'));
        }
    }

    /**
     * Browse plans.
     *
     * @param  int    $productID
     * @param  int    $branch
     * @param  string $orderBy
     * @param  string $browseType
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function browse($productID = 0, $branch = 0, $browseType = 'all', $orderBy = 'begin_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1 )
    {
        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        /* Append id for secend sort. */
        $sort = $this->loadModel('common')->appendOrder($orderBy);
        $this->session->set('productPlanList', $this->app->getURI(true), 'product');

        $this->commonAction($productID, $branch);
        $products               = $this->product->getPairs();
        $this->view->title      = $products[$productID] . $this->lang->colon . $this->lang->productplan->browse;
        $this->view->position[] = $this->lang->productplan->browse;
        $this->view->productID  = $productID;
        $this->view->branch     = $branch;
        $this->view->browseType = $browseType;
        $this->view->orderBy    = $orderBy;
        $this->view->plans      = $this->productplan->getList($productID, $branch, $browseType, $pager, $sort);
        $this->view->pager      = $pager;
        $this->view->projects   = $this->product->getProjectPairsByProduct($productID, $branch);
        $this->display();
    }

    /**
     * View plan.
     *
     * @param  int    $planID
     * @param  string $type
     * @param  string $orderBy
     * @param  string $link
     * @param  string $param
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     *
     * @access public
     * @return void
     */
    public function view($planID = 0, $type = 'story', $orderBy = 'id_desc', $link = 'false', $param = '', $recTotal = 0, $recPerPage = 100, $pageID = 1)
    {
        $planID = (int)$planID;
        $plan   = $this->productplan->getByID($planID, true);
        if(!$plan) die(js::error($this->lang->notFound) . js::locate('back'));

        $this->session->set('storyList', $this->app->getURI(true) . '&type=' . 'story', 'product');
        $this->session->set('bugList', $this->app->getURI(true) . '&type=' . 'bug', 'qa');

        /* Determines whether an object is editable. */
        $canBeChanged = common::canBeChanged('plan', $plan);

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        if($this->app->getViewType() == 'mhtml') $recPerPage = 10;

        /* Append id for secend sort. */
        $sort = $this->loadModel('common')->appendOrder($orderBy);

        $this->commonAction($plan->product, $plan->branch);
        $products = $this->product->getProductPairsByProject($this->session->project);

        $bugPager   = new pager(0, $recPerPage, $type == 'bug' ? $pageID : 1);
        $storyPager = new pager(0, $recPerPage, $type == 'story' ? $pageID : 1);

        /* Get stories of plan. */
        $this->loadModel('story');
        $planStories = $this->story->getPlanStories($planID, 'all', $type == 'story' ? $sort : 'id_desc', $storyPager);

        $this->executeHooks($planID);
        if($plan->parent > 0)     $this->view->parentPlan    = $this->productplan->getById($plan->parent);
        if($plan->parent == '-1') $this->view->childrenPlans = $this->productplan->getChildren($plan->id);

        $this->loadModel('datatable');
        $this->view->modulePairs  = $this->loadModel('tree')->getOptionMenu($plan->product, 'story');
        $this->view->title        = "PLAN #$plan->id $plan->title/" . zget($products, $plan->product, '');
        $this->view->position[]   = $this->lang->productplan->view;
        $this->view->planStories  = $planStories;
        $this->view->planBugs     = $this->loadModel('bug')->getPlanBugs($planID, 'all', $type == 'bug' ? $sort : 'id_desc', $bugPager);
        $this->view->products     = $products;
        $this->view->summary      = $this->product->summary($this->view->planStories);
        $this->view->plan         = $plan;
        $this->view->actions      = $this->loadModel('action')->getList('productplan', $planID);
        $this->view->users        = $this->loadModel('user')->getPairs('noletter');
        $this->view->plans        = $this->productplan->getPairsOfProductName($plan->product);
        $this->view->modules      = $this->loadModel('tree')->getOptionMenu($plan->product);
        $this->view->type         = $type;
        $this->view->orderBy      = $orderBy;
        $this->view->link         = $link;
        $this->view->param        = $param;
        $this->view->storyPager   = $storyPager;
        $this->view->bugPager     = $bugPager;
        $this->view->canBeChanged = $canBeChanged;
        $this->display();
    }

    /**
     * Ajax: Get product plans.
     *
     * @param  int    $productID
     * @param  int    $branch
     * @param  string $number
     *
     * @access public
     * @return void
     */
    public function ajaxGetProductplans($productID, $branch = 0, $number = '')
    {
        $plans = $this->productplan->getPairs($productID, $branch);

        $planName = $number === '' ? 'plan' : "plan[$number]";
        $plans    = empty($plans) ? array('' => '') : $plans;
        die(html::select($planName, $plans, '', "class='form-control'"));
    }

    
    /**
     * Ajax: Get product plans.
     *
     * @param  int    $productID
     * @param  int    $branch
     * @param  string $number
     *
     * @access public
     * @return void
     */
    public function ajaxGetPlansByProduct($productID, $branch = 0, $number = '', $buildIDs = '')
    {
        
        $plans    = $this->productplan->getPairs($productID, $branch);
        $planName = 'linkPlan[]';

        if(!empty($buildIDs))
        {
            $builds = $this->dao->select('version')->from(TABLE_BUILD)
                ->where('id')->in($buildIDs)
                ->fetchAll('version');
            $number = array_keys($builds);
        }
        die(html::select($planName, $plans, $number, "class='form-control chosen' multiple=multiple id='planIdBox'"));
    }

     /**
     * Ajax: Get product plans.
     *
     * @param  int    $productID
     * @param  int    $branch
     * @param  string $number
     *
     * @access public
     * @return void
     */
    public function ajaxGetPlansByProductOnList($productID, $branch = 0, $number = '', $buildIDs = '')
    {
        
        $plans = $this->productplan->getPairs($productID, $branch);
        $planName = $number === '' ? 'linkPlan[]' : "linkPlan[$number][]";
        $plans    = empty($plans) ? array('' => '') : $plans;

        if(!empty($buildIDs))
        {
            $builds = $this->dao->select('version')->from(TABLE_BUILD)
            ->where('id')->in($buildIDs)
            ->fetchAll('version');
            $number = array_keys($builds);
        }
        die(html::select($planName, $plans, $number, "class='form-control chosen control-plan' multiple=multiple"));
    }
    

    /**
     * Sort story for productplan.
     *
     * @param int $planID
     *
     * @access public
     * @return bool
     */
    public function ajaxStorySort($planID = 0)
    {
        if(empty($planID)) return true;

        /* Get story id list. */
        $storyIDList = explode(',', trim($this->post->stories, ','));

        /* Update the story order according to the plan. */
        $this->loadModel('story')->sortStoriesOfPlan($planID, $storyIDList, $this->post->orderBy, $this->post->pageID, $this->post->recPerPage);
    }

    /**
     * Link stories.
     *
     * @param int    $planID
     * @param string $browseType
     * @param int    $param
     * @param string $orderBy
     * @param int    $recTotal
     * @param int    $recPerPage
     * @param int    $pageID
     *
     * @access public
     * @return void
     */
    public function linkStory($planID = 0, $browseType = '', $param = 0, $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 100, $pageID = 1)
    {
        if(!empty($_POST['stories']))
        {
            $this->productplan->linkStory($planID);
            die(js::locate(inlink('view', "planID=$planID&type=story&orderBy=$orderBy"), 'parent'));
        }

        $this->session->set('storyList', inlink('view', "planID=$planID&type=story&orderBy=$orderBy&link=true&param=" . helper::safe64Encode("&browseType=$browseType&queryID=$param")), 'product');

        $this->loadModel('story');
        $this->loadModel('tree');
        $plan = $this->productplan->getByID($planID);
        $this->commonAction($plan->product, $plan->branch);
        $products = $this->product->getProductPairsByProject($this->session->project);

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        /* Build search form. */
        $queryID = ($browseType == 'bySearch') ? (int)$param : 0;
        unset($this->config->product->search['fields']['product']);
        $this->config->product->search['actionURL'] = $this->createLink('productplan', 'view', "planID=$planID&type=story&orderBy=$orderBy&link=true&param=" . helper::safe64Encode('&browseType=bySearch&queryID=myQueryID'));
        $this->config->product->search['queryID']   = $queryID;
        $this->config->product->search['style']     = 'simple';
        $this->config->product->search['params']['product']['values'] = $products + array('all' => $this->lang->product->allProductsOfProject);
        $this->config->product->search['params']['linkPlan']['values'] = $this->productplan->getForProducts(array($plan->product => $plan->product));
        $this->config->product->search['params']['module']['values']  = $this->tree->getOptionMenu($plan->product, $viewType = 'story', $startModuleID = 0);
        $storyStatusList = $this->lang->story->statusList;
        unset($storyStatusList['closed']);
        $this->config->product->search['params']['status'] = array('operator' => '=', 'control' => 'select', 'values' => $storyStatusList);
        if($this->session->currentProductType == 'normal')
        {
            unset($this->config->product->search['fields']['branch']);
            unset($this->config->product->search['params']['branch']);
        }
        else
        {
            $this->config->product->search['fields']['branch'] = $this->lang->product->branch;
            $branches = array('' => '') + $this->loadModel('branch')->getPairs($plan->product, 'noempty');
            if($plan->branch) $branches = array('' => '', $plan->branch => $branches[$plan->branch]);
            $this->config->product->search['params']['branch']['values'] = $branches;
        }
        $this->loadModel('search')->setSearchParams($this->config->product->search);

        $planStories = $this->story->getPlanStories($planID);

        if($browseType == 'bySearch')
        {
            $allStories = $this->story->getBySearch($plan->product, $plan->branch, $queryID, 'id', '', 'story', array_keys($planStories), $pager);
        }
        else
        {
            $allStories = $this->story->getProductStories($this->view->product->id, $plan->branch ? "0,{$plan->branch}" : 0, $moduleID = '0', $status = 'draft,active,changed', 'story', 'id_desc', $hasParent = false, array_keys($planStories), $pager);
        }

        $this->view->allStories  = $allStories;
        $this->view->planStories = $planStories;
        $this->view->products    = $products;
        $this->view->plan        = $plan;
        $this->view->plans       = $this->dao->select('id, end')->from(TABLE_PRODUCTPLAN)->fetchPairs();
        $this->view->users       = $this->loadModel('user')->getPairs('noletter');
        $this->view->browseType  = $browseType;
        $this->view->modules     = $this->loadModel('tree')->getOptionMenu($plan->product);
        $this->view->param       = $param;
        $this->view->orderBy     = $orderBy;
        $this->view->pager       = $pager;
        $this->display();
    }

    /**
     * Unlink story
     *
     * @param  int    $storyID
     * @param  int    $planID
     * @param  string $confirm  yes|no
     * @access public
     * @return void
     */
    public function unlinkStory($storyID, $planID, $confirm = 'no')
    {
        if($confirm == 'no')
        {
            die(js::confirm($this->lang->productplan->confirmUnlinkStory, $this->createLink('productplan', 'unlinkstory', "storyID=$storyID&planID=$planID&confirm=yes")));
        }
        else
        {
            $this->productplan->unlinkStory($storyID, $planID);

            /* if ajax request, send result. */
            if($this->server->ajax)
            {
                if(dao::isError())
                {
                    $response['result']  = 'fail';
                    $response['message'] = dao::getError();
                }
                else
                {
                    $response['result']  = 'success';
                    $response['message'] = '';
                }
                $this->send($response);
            }
            die(js::reload('parent'));
        }
    }

    /**
     * Batch unlink story.
     *
     * @param int    $planID
     * @param string $orderBy
     *
     * @access public
     * @return void
     */
    public function batchUnlinkStory($planID, $orderBy = 'id_desc')
    {
        foreach($this->post->storyIdList as $storyID) $this->productplan->unlinkStory($storyID, $planID);
        die(js::locate($this->createLink('productplan', 'view', "planID=$planID&type=story&orderBy=$orderBy"), 'parent'));
    }

    /**
     * Link bugs.
     *
     * @param  int    $planID
     * @param  string $browseType
     * @param  int    $param
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function linkBug($planID = 0, $browseType = '', $param = 0, $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 100, $pageID = 1)
    {
        if(!empty($_POST['bugs']))
        {
            $this->productplan->linkBug($planID);
            die(js::locate(inlink('view', "planID=$planID&type=bug&orderBy=$orderBy"), 'parent'));
        }

        /* Load module and set session. */
        $this->loadModel('bug');
        $this->session->set('bugList', inlink('view', "planID=$planID&type=bug&orderBy=$orderBy&link=true&param=" . helper::safe64Encode("&browseType=$browseType&queryID=$param")), 'qa');

        /* Init vars. */
        $executions = $this->app->user->view->sprints . ',0';
        $plan       = $this->productplan->getByID($planID);
        $productID  = $plan->product;
        $queryID    = ($browseType == 'bysearch') ? (int)$param : 0;

        /* Set drop menu. */
        $this->commonAction($productID, $plan->branch);

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        /* Build the search form. */
        $this->config->bug->search['actionURL'] = $this->createLink('productplan', 'view', "planID=$planID&type=bug&orderBy=$orderBy&link=true&param=" . helper::safe64Encode('&browseType=bySearch&queryID=myQueryID'));
        $this->config->bug->search['queryID']   = $queryID;
        $this->config->bug->search['style']     = 'simple';
        $this->config->bug->search['params']['plan']['values']          = $this->productplan->getForProducts(array($productID => $productID));
        $this->config->bug->search['params']['module']['values']        = $this->loadModel('tree')->getOptionMenu($productID, $viewType = 'bug', $startModuleID = 0);
        $this->config->bug->search['params']['project']['values']       = $this->product->getExecutionPairsByProduct($productID);
        $this->config->bug->search['params']['openedBuild']['values']   = $this->loadModel('build')->getProductBuildPairs($productID, $branch = 0, $params = '');
        $this->config->bug->search['params']['resolvedBuild']['values'] = $this->build->getProductBuildPairs($productID, $branch = 0, $params = '');

        unset($this->config->bug->search['fields']['product']);
        if($this->session->currentProductType == 'normal')
        {
            unset($this->config->bug->search['fields']['branch']);
            unset($this->config->bug->search['params']['branch']);
        }
        else
        {
            $this->config->bug->search['fields']['branch'] = $this->lang->product->branch;
            $branches = array('' => '') + $this->loadModel('branch')->getPairs($productID, 'noempty');
            if($plan->branch) $branches = array('' => '', $plan->branch => $branches[$plan->branch]);
            $this->config->bug->search['params']['branch']['values'] = $branches;
        }
        $this->loadModel('search')->setSearchParams($this->config->bug->search);

        $planBugs = $this->bug->getPlanBugs($planID);

        if($browseType == 'bySearch')
        {
            $allBugs = $this->bug->getBySearch($productID, $plan->branch, $queryID, 'id_desc', array_keys($planBugs), $pager);
        }
        else
        {
            $allBugs = $this->bug->getActiveBugs($this->view->product->id, $plan->branch, $executions, array_keys($planBugs), $pager);
        }

        $this->view->allBugs    = $allBugs;
        $this->view->planBugs   = $planBugs;
        $this->view->plan       = $plan;
        $this->view->users      = $this->loadModel('user')->getPairs('noletter');
        $this->view->browseType = $browseType;
        $this->view->param      = $param;
        $this->view->orderBy    = $orderBy;
        $this->view->pager      = $pager;
        $this->display();
    }

    /**
     * Unlink story
     *
     * @param  int    $bugID
     * @param  string $confirm  yes|no
     * @access public
     * @return void
     */
    public function unlinkBug($bugID, $confirm = 'no')
    {
        if($confirm == 'no')
        {
            die(js::confirm($this->lang->productplan->confirmUnlinkBug, $this->createLink('productplan', 'unlinkbug', "bugID=$bugID&confirm=yes")));
        }
        else
        {
            $this->productplan->unlinkBug($bugID);

            /* if ajax request, send result. */
            if($this->server->ajax)
            {
                if(dao::isError())
                {
                    $response['result']  = 'fail';
                    $response['message'] = dao::getError();
                }
                else
                {
                    $response['result']  = 'success';
                    $response['message'] = '';
                }
                $this->send($response);
            }
            die(js::reload('parent'));
        }
    }

    /**
     * Batch unlink story.
     *
     * @param        $planID
     * @param string $orderBy
     *
     * @access public
     * @return void
     */
    public function batchUnlinkBug($planID, $orderBy = 'id_desc')
    {
        foreach($this->post->unlinkBugs as $bugID) $this->productplan->unlinkBug($bugID);
        die(js::locate($this->createLink('productplan', 'view', "planID=$planID&type=bug&orderBy=$orderBy"), 'parent'));
    }

    /**
     * 导出数据
     *
     * @param int $productID
     * @param int $branch
     * @param string $browseType
     * @param string $orderBy
     */
    public function export($productID = 0, $branch = 0, $browseType = 'all', $orderBy = 'begin_desc'){
        if($_POST) {
            $sort = $this->loadModel('common')->appendOrder($orderBy);
            $this->loadModel('file');
            $productPlanLang   = $this->lang->productplan;
            $productPlanConfig = $this->config->productplan;

            /* Create field lists. */
            $fields = $this->post->exportFields ? $this->post->exportFields : explode(',', $productPlanConfig->list->exportFields);
            foreach ($fields as $key => $fieldName) {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = isset($productPlanLang->$fieldName) ? $productPlanLang->$fieldName : $fieldName;
                unset($fields[$key]);
            }
            $productInfo = $this->loadModel('product')->getById($productID);
            $appInfo = new stdClass();
            $app = zget($productInfo, 'app');
            if($app){
                $appInfo = $this->loadModel('application')->getAppNameById($app);
            }
            
            $data = $this->productplan->getList($productID, $branch, $browseType, null, $sort);

            foreach ($data as $value){
                $value->app = zget($appInfo,'name');
                $value->product = $productInfo->name;
                $value->productCode = $productInfo->code;
                $value->begin == '2030-01-01' ? $this->lang->productplan->future : $value->begin;
                $value->end   == '2030-01-01' ? $this->lang->productplan->future : $value->end;
                $value->desc = strip_tags($value->desc);
            }
            $this->post->set('fields', $fields);
            $this->post->set('rows', $data);
            $this->post->set('kind', '产品版本');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }
        $this->view->fileName        = $this->lang->productplan->common;
        $this->view->allExportFields = $this->config->productplan->list->exportFields;
        $this->view->customExport    = true;
        $this->display();
    }
}
