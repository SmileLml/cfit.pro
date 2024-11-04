<?php
/**
 * The control file of project module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     project
 * @version     $Id
 * @link        http://www.zentao.net
 */
class project extends control
{
    /**
     * Project create guide.
     *
     * @param  int    $projectID
     * @param  string $from
     * @access public
     * @return void
     */
    public function createGuide($projectID = 0, $from = 'project')
    {
        $this->view->from      = $from;
        $this->view->projectID = $projectID;
        $this->display();
    }

    /**
     * Update children user view.
     *
     * @param  int    $projectID
     * @param  array  $account
     * @access public
     * @return void
     */
    public function updateChildUserView($projectID = 0, $account = array())
    {
        $childPrograms = $this->dao->select('id')->from(TABLE_PROJECT)->where('path')->like("%,$projectID,%")->andWhere('type')->eq('project')->fetchPairs();
        $childProjects = $this->dao->select('id')->from(TABLE_PROJECT)->where('path')->like("%,$projectID,%")->andWhere('type')->eq('project')->fetchPairs();
        $childProducts = $this->dao->select('id')->from(TABLE_PRODUCT)->where('project')->eq($projectID)->fetchPairs();

        if(!empty($childPrograms)) $this->user->updateUserView($childPrograms, 'project',  array($account));
        if(!empty($childProjects)) $this->user->updateUserView($childProjects, 'project',  array($account));
        if(!empty($childProducts)) $this->user->updateUserView($childProducts, 'product', array($account));
    }

    /**
     * Export project.
     *
     * @param  string $status
     * @param  string $orderBy
     * @access public
     * @return void
     */
    public function export($status, $orderBy)
    {
        if($_POST)
        {
            $projectLang   = $this->lang->project;
            $projectConfig = $this->config->project;

            /* Create field lists. */
            $fields = $this->post->exportFields ? $this->post->exportFields : explode(',', $projectConfig->list->exportFields);
            foreach($fields as $key => $fieldName)
            {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = zget($projectLang, $fieldName);
                unset($fields[$key]);
            }

            $projects = $this->project->getList($status, $orderBy, null);
            $users    = $this->loadModel('user')->getPairs('noletter');
            foreach($projects as $i => $project)
            {
                $project->PM       = zget($users, $project->PM);
                $project->status   = $this->processStatus('project', $project);
                $project->model    = zget($projectLang->modelList, $project->model);
                $project->product  = zget($projectLang->productList, $project->product);
                $project->budget   = $project->budget . zget($projectLang->unitList, $project->budgetUnit);

                if($this->post->exportType == 'selected')
                {
                    $checkedItem = $this->cookie->checkedItem;
                    if(strpos(",$checkedItem,", ",{$project->id},") === false) unset($projects[$i]);
                }
            }

            if(isset($this->config->bizVersion)) list($fields, $projectStats) = $this->loadModel('workflowfield')->appendDataFromFlow($fields, $projectStats);

            $this->post->set('fields', $fields);
            $this->post->set('rows', $projects);
            $this->post->set('kind', 'project');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        $this->display();
    }

    /**
     * Ajax get project drop menu.
     *
     * @param  int     $projectID
     * @param  string  $module
     * @param  string  $method
     * @access public
     * @return void
     */
    public function ajaxGetDropMenu($projectID = 0, $module, $method)
    {
        /* Load module. */
        $this->loadModel('program');

        /* Sort project. */
        $programs        = array();
        $orderedProjects = array();
        $objects         = $this->program->getList('all', 'order_asc', null, true);
        //已经关闭的列表
        $closedObjects   =  $this->program->getClosedList('order_asc', null, true);


        $projectCode = $this->dao->select('project,mark')->from(TABLE_PROJECTPLAN)->where('project')->in(array_merge(array_keys($objects), array_keys($closedObjects)))->fetchPairs();

        foreach($objects as $objectID => $object)
        {
            $object->code = isset($projectCode[$object->id]) ? $projectCode[$object->id] : '';
            if(!empty( $object->code)){
                $object->name =  $object->code."_".$object->name;
            }
            if($object->type == 'program')
            {
                $programs[$objectID] = $object->name;
            }
            else
            {
                $object->parent = $this->program->getTopByID($object->parent);
                $orderedProjects[] = $object;
                unset($objects[$object->id]);
            }
        }
        //已经关闭的列表
        $closedProjects = [];
        foreach ($closedObjects as $objectID => $object){
            $object->code = isset($projectCode[$object->id]) ? $projectCode[$object->id] : '';
            if(!empty( $object->code)){
                $object->name =  $object->code."_".$object->name;
            }
            if($object->type == 'program') {
                $programs[$objectID] = $object->name;
            }else {
                $object->parent = $this->program->getTopByID($object->parent);
                $closedProjects[] = $object;
                unset($closedObjects[$object->id]);
            }
        }

        $this->view->projectID = $projectID;
        $this->view->projects  = $orderedProjects;
        $this->view->module    = $module;
        $this->view->method    = $method;
        $this->view->programs  = $programs;
        $this->view->closedProjects  = $closedProjects; //已关闭项目

        $this->display();
    }

    /**
     * Ajax get projects.
     *
     * @access public
     * @return void
     */
    public function ajaxGetCopyProjects()
    {
        $data = fixer::input('post')->get();
        $projects = $this->dao->select('id, name')->from(TABLE_PROJECT)
            ->where('type')->eq('project')
            ->andWhere('deleted')->eq(0)
            ->beginIF(!$this->app->user->admin)->andWhere('id')->in($this->app->user->view->projects)->fi()
            ->beginIF(trim($data->name))->andWhere('name')->like("%$data->name%")->fi()
            ->fetchPairs();

        $html = empty($projects) ? "<div class='text-center'>{$this->lang->noData}</div>" : '';
        foreach($projects as $id => $name)
        {
            $active = $data->cpoyProjectID == $id ? 'active' : '';
            $html .= "<div class='col-md-4 col-sm-6'><a href='javascript:;' data-id=$id class='nobr $active'>" . html::icon($this->lang->icons['project'], 'text-muted') . $name . "</a></div>";
        }
        echo $html;
    }

    /**
     * Project index view.
     *
     * @param  int    $projectID
     * @access public
     * @return void
     */
    public function index($projectID = 0)
    {
        $projectID = $this->project->saveState($projectID, $this->project->getPairsByProgram());

        if($projectID == 0 and common::hasPriv('project', 'create')) $this->locate($this->createLink('project', 'create'));
        if($projectID == 0 and !common::hasPriv('project', 'create')) $this->locate($this->createLink('project', 'browse'));

        $this->project->setMenu($projectID);

        $project = $this->project->getByID($projectID);
        if(empty($project) || $project->type != 'project') die(js::error($this->lang->notFound) . js::locate('back'));

        if(!$projectID) $this->locate($this->createLink('project', 'browse'));
        setCookie("lastProject", $projectID, $this->config->cookieLife, $this->config->webRoot, '', false, true);

        $this->view->title      = $this->lang->project->common . $this->lang->colon . $this->lang->project->index;
        $this->view->position[] = $this->lang->project->index;
        $this->view->project    = $project;
        $this->session->set('common_back_url', $this->app->getURI(true),'backlog');
        $this->display();
    }

    /**
     * Project list.
     *
     * @param  int    $programID
     * @param  string $browseType
     * @param  int    $param
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function browse($programID = 0, $browseType = 'doing', $param = 0, $orderBy = 'order_asc', $recTotal = 0, $recPerPage = 15, $pageID = 1)
    {
        $this->loadModel('datatable');
        $this->loadModel('execution');
        $this->session->set('projectList', $this->app->getURI(true), 'project');

        /* Load pager and get tasks. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $queryID = ($browseType == 'bysearch') ? (int)$param : 0;
        $programTitle = $this->loadModel('setting')->getItem('owner=' . $this->app->user->account . '&module=project&key=programTitle');
        $projectStats = $this->loadModel('program')->getProjectStats($programID, $browseType, $queryID, $orderBy, $pager, $programTitle);
        $this->view->title      = $this->lang->project->browse;
        $this->view->position[] = $this->lang->project->browse;

        $this->view->projectStats = $projectStats;
        $this->view->pager        = $pager;
        $this->view->programID    = $programID;
        $this->view->program      = $this->program->getByID($programID);
        $this->view->programTree  = $this->project->getTreeMenu(0, array('projectmodel', 'createManageLink'), 0, 'list');
        $this->view->users        = $this->loadModel('user')->getPairs('noletter|pofirst|nodeleted');
        $this->view->browseType   = $browseType;
        $this->view->param        = $param;
        $this->view->orderBy      = $orderBy;

        $this->display();
    }

    /**
     * Set module display mode.
     *
     * @access public
     * @return void
     */
    public function programTitle()
    {
        $this->loadModel('setting');
        if($_POST)
        {
            $programTitle = $this->post->programTitle;
            $this->setting->setItem($this->app->user->account . '.project.programTitle', $programTitle);
            die(js::reload('parent.parent'));
        }

        $status = $this->setting->getItem('owner=' . $this->app->user->account . '&module=project&key=programTitle');
        $this->view->status = empty($status) ? '0' : $status;
        $this->display();
    }

    /**
     * Create a project.
     *
     * @param  string $model
     * @param  int    $programID
     * @param  int    $copyProjectID
     * @access public
     * @return void
     */
    public function create($model = 'scrum', $programID = 0, $copyProjectID = 0)
    {
        $this->loadModel('execution');

        if($_POST)
        {
            $projectID = $this->project->create();
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $this->loadModel('action')->create('project', $projectID, 'opened');

            /* Link the plan stories. */
            if(!empty($_POST['plans']))
            {
                foreach($_POST['plans'] as $planID)
                {
                    $planStories = $planProducts = array();
                    $planStory   = $this->loadModel('story')->getPlanStories($planID);
                    if(!empty($planStory))
                    {
                        foreach($planStory as $id => $story)
                        {
                            if($story->status == 'draft')
                            {
                                unset($planStory[$id]);
                                continue;
                            }
                            $planProducts[$story->id] = $story->product;
                        }
                        $planStories = array_keys($planStory);
                        $this->execution->linkStory($projectID, $planStories, $planProducts);
                    }
                }
            }

            if($this->app->openApp == 'program')
            {
                $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $this->createLink('program', 'browse')));
            }
            elseif($this->app->openApp == 'doc')
            {
                $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $this->createLink('doc', 'objectLibs', "type=project&objectID=$projectID")));
            }
            else
            {
                if($model == 'waterfall')
                {
                    $productID = $this->loadModel('product')->getProductIDByProject($projectID, true);
                    $this->session->set('projectPlanList', $this->createLink('programplan', 'browse', "projectID=$projectID&productID=$productID&type=lists", '', '', $projectID), 'project');
                    $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $this->createLink('programplan', 'create', "projectID=$projectID", '', '', $projectID)));
                }

                $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $this->createLink('project', 'browse', "programID=0&browseType=all", '', '', $projectID)));
            }
        }

        if($this->app->openApp == 'program') $this->loadModel('program')->setMenu($programID);

        $name      = '';
        $code      = '';
        $team      = '';
        $whitelist = '';
        $acl       = 'private';
        $auth      = 'extend';

        $products      = array();
        $productPlans  = array();
        $parentProgram = $this->loadModel('program')->getByID($programID);

        if($copyProjectID)
        {

            $team        = $copyProject->team;
            $acl         = $copyProject->acl;
            $auth        = $copyProject->auth;
            $whitelist   = $copyProject->whitelist;
            $programID   = $copyProject->parent;
            $model       = $copyProject->model;

            $products = $this->project->getProducts($copyProjectID);
            foreach($products as $product)
            {
                $productPlans[$product->id] = $this->loadModel('productplan')->getPairs($product->id);
            }
        }

        $this->view->title      = $this->lang->project->create;
        $this->view->position[] = $this->lang->project->create;

        $this->view->pmUsers         = $this->loadModel('user')->getPairs('noclosed|nodeleted|pmfirst');
        $this->view->users           = $this->user->getPairs('noclosed|nodeleted');
        $this->view->copyProjects    = $this->project->getPairsByModel();
        $this->view->products        = $products;
        $this->view->allProducts     = array('0' => '') + $this->program->getProductPairs($programID);
        $this->view->productPlans    = array('0' => '') + $productPlans;
        $this->view->branchGroups    = $this->loadModel('branch')->getByProducts(array_keys($products));
        $this->view->programID       = $programID;
        $this->view->model           = $model;
        $this->view->name            = $name;
        $this->view->code            = $code;
        $this->view->team            = $team;
        $this->view->acl             = $acl;
        $this->view->auth            = $auth;
        $this->view->whitelist       = $whitelist;
        $this->view->copyProjectID   = $copyProjectID;
        $this->view->programList     = $this->program->getParentPairs();
        $this->view->parentProgram   = $parentProgram;
        $this->view->URSRPairs       = $this->loadModel('custom')->getURSRPairs();
        $this->view->availableBudget = $this->program->getBudgetLeft($parentProgram);
        $this->view->budgetUnitList  = $this->program->getBudgetUnitList();

        $this->display();
    }

    /**
     * Edit a project.
     *
     * @param  int    $projectID
     * @param  string $from  project|program|programProject
     * @access public
     * @return void
     */
    public function edit($projectID = 0, $from = 'project')
    {
        $this->loadModel('action');
        $this->loadModel('custom');
        $this->loadModel('productplan');
        $this->loadModel('user');
        $this->loadModel('program');
        $this->loadModel('execution');

        $project   = $this->project->getByID($projectID);
        $programID = $project->parent;
        $this->project->setMenu($projectID);

        if($_POST)
        {
            $oldPlans = $this->dao->select('plan')->from(TABLE_PROJECTPRODUCT)->where('project')->eq($projectID)->fetchPairs('plan');
            $oldPlanStories = $this->dao->select('t1.story')->from(TABLE_PROJECTSTORY)->alias('t1')
                ->leftJoin(TABLE_PROJECTPRODUCT)->alias('t2')->on('t1.project=t2.project')
                ->where('t1.project')->eq($projectID)
                ->andWhere('t2.plan')->in(array_keys($oldPlans))
                ->fetchAll('story');
            $diffResult = array_diff($oldPlans, $_POST['plans']);

            $oldProducts = $this->project->getProducts($projectID);
            $oldProducts  = array_keys($oldProducts);

            $changes = $this->project->update($projectID);
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $newProducts  = $this->project->getProducts($projectID);
            $newProducts  = array_keys($newProducts);
            $diffProducts = array_merge(array_diff($oldProducts, $newProducts), array_diff($newProducts, $oldProducts));

            if($changes)
            {
	        $comment = '';
		$extra = '';
                if($diffProducts) {
                    $comment = '维护产品:<br>原产品：'.implode(',',$oldProducts).'<br>'.'更新后：'.implode(',',$newProducts);
                    $extra   = $comment;
                }
                $actionID = $this->action->create('project', $projectID, 'edited',$comment,$extra);
                $this->action->logHistory($actionID, $changes);
            }

            /* Link the plan stories. */
            if(!empty($_POST['plans']) and !empty($diffResult))
            {
                $this->loadModel('productplan')->linkProject($projectID, $_POST['plans'], $oldPlanStories);
            }

            $locateLink = $this->session->projectList ? $this->session->projectList : inLink('view', "projectID=$projectID");
            if($from == 'projectView')    $locateLink = $this->createLink('project', 'view', "projectID=$projectID");
            if($from == 'program')        $locateLink = $this->createLink('program', 'browse');
            if($from == 'programProject') $locateLink = $this->session->programProject ? $this->session->programProject : $this->createLink('program', 'project', "projectID=$projectID");
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $locateLink));
        }

        $linkedBranches = array();
        $productPlans   = array(0 => '');
        $allProducts    = $this->program->getProductPairs($project->parent, 'assign', 'noclosed');
        $linkedProducts = $this->project->getProducts($projectID);
        $parentProject  = $this->program->getByID($project->parent);

        /* If the story of the product which linked the project, you don't allow to remove the product. */
        $unmodifiableProducts = array();
        foreach($linkedProducts as $productID => $linkedProduct)
        {
            $projectStories = $this->dao->select('*')->from(TABLE_PROJECTSTORY)->where('project')->eq($projectID)->andWhere('product')->eq($productID)->fetchAll('story');
            if(!empty($projectStories)) array_push($unmodifiableProducts, $productID);
        }

        foreach($linkedProducts as $product)
        {
            if(!isset($allProducts[$product->id])) $allProducts[$product->id] = $product->name;
            if($product->branch) $linkedBranches[$product->branch] = $product->branch;
        }

        foreach($linkedProducts as $product)
        {
            $productPlans[$product->id] = $this->productplan->getPairs($product->id);
        }

        $projectPlan = $this->dao->select("*")->from(TABLE_PROJECTPLAN)->where('project')->eq($projectID)->andWhere('deleted')->eq(0)->fetch();
        if(empty($project->planWorkload)){
            $creation = $this->loadModel('projectplan')->getCreationByID($projectPlan->id);
            $project->planWorkload = $creation->workload;
        }
        $deptObj = $this->loadModel('dept')->getByID($projectPlan->bearDept);
        $this->view->deptObj = $deptObj;
        $this->view->title      = $this->lang->project->edit;
        $this->view->projectPlan      = $projectPlan;
        $this->view->position[] = $this->lang->project->edit;

        $this->view->PMUsers              = $this->user->getPairs('noclosed|nodeleted|pmfirst',  $project->PM);
        $this->view->users                = $this->user->getPairs('noclosed|nodeleted');
        $this->view->project              = $project;
        $this->view->programList          = $this->program->getParentPairs();
        $this->view->projectID            = $projectID;
        $this->view->allProducts          = array('0' => '') + $allProducts;
        $this->view->productPlans         = $productPlans;
        $this->view->linkedProducts       = $linkedProducts;
        $this->view->unmodifiableProducts = $unmodifiableProducts;
        $this->view->branchGroups         = $this->loadModel('branch')->getByProducts(array_keys($linkedProducts), '', $linkedBranches);
        $this->view->URSRPairs            = $this->custom->getURSRPairs();
        $this->view->from                 = $from;
        $this->view->parentProject        = $parentProject;
        $this->view->parentProgram        = $this->program->getByID($project->parent);
        $this->view->availableBudget      = $this->program->getBudgetLeft($parentProject) + (float)$project->budget;
        $this->view->budgetUnitList       = $this->project->getBudgetUnitList();

        $this->display();
    }

    /**
     * Batch edit projects.
     *
     * @param  string $from
     * @param  int    $projectID
     * @access public
     * @return void
     */
    public function batchEdit($from = 'browse', $projectID = 0)
    {
        $this->loadModel('action');
        $this->loadModel('execution');

        if($this->post->names)
        {
            $allChanges = $this->project->batchUpdate();

            if(!empty($allChanges))
            {
                foreach($allChanges as $projectID => $changes)
                {
                    if(empty($changes)) continue;

                    $actionID = $this->action->create('project', $projectID, 'Edited');
                    $this->action->logHistory($actionID, $changes);
                }
            }
            die(js::locate($this->session->projectList, 'parent'));
        }

        $projectIdList = $this->post->projectIdList ? $this->post->projectIdList : die(js::locate($this->session->projectList, 'parent'));
        $projects      = $this->dao->select('*')->from(TABLE_PROJECT)->where('id')->in($projectIdList)->fetchAll('id');

        foreach($projects as $project) $appendPMUsers[$project->PM] = $project->PM;

        $this->view->title      = $this->lang->project->batchEdit;
        $this->view->position[] = $this->lang->project->batchEdit;

        $this->view->projects      = $projects;
        $this->view->programList   = $this->loadModel('program')->getParentPairs();
        $this->view->PMUsers       = $this->loadModel('user')->getPairs('noclosed|nodeleted|pmfirst',  $appendPMUsers);

        $this->display();
    }

    /**
     * View a project.
     *
     * @param  int    $projectID
     * @access public
     * @return void
     */
    public function view($projectID = 0)
    {
        $projectID = (int)$projectID;
        $project   = $this->project->getById($projectID);
        if(empty($project) || strpos('scrum,waterfall', $project->model) === false) die(js::error($this->lang->notFound) . js::locate('back'));

        $this->project->setMenu($projectID);

        $products = $this->loadModel('product')->getProducts($projectID);
        $linkedBranches = array();
        foreach($products as $product)
        {
            if($product->branch) $linkedBranches[$product->branch] = $product->branch;
        }

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager(0, 30, 1);

        $this->view->title        = $this->lang->project->view;
        $this->view->position     = $this->lang->project->view;
        $this->view->projectID    = $projectID;
        $this->view->project      = $project;
        $this->view->products     = $products;
        $this->view->actions      = $this->loadModel('action')->getList('project', $projectID);
        $this->view->users        = $this->loadModel('user')->getPairs('noletter');
        $this->view->teamMembers  = $this->project->getTeamMembers($projectID);
        $this->view->statData     = $this->project->getStatData($projectID);
        $this->view->workhour     = $this->project->getWorkhour($projectID);
        $this->view->planGroup    = $this->loadModel('execution')->getPlans($products);;
        $this->view->branchGroups = $this->loadModel('branch')->getByProducts(array_keys($products), '', $linkedBranches);
        $this->view->dynamics     = $this->loadModel('action')->getDynamic('all', 'all', 'date_desc', $pager, 'all', $projectID);

        $this->display();
    }

    /**
     * Project browse groups.
     *
     * @param  int    $projectID
     * @param  int    $programID
     * @access public
     * @return void
     */
    public function group($projectID = 0, $programID = 0)
    {
        $this->loadModel('group');
        $this->project->setMenu($projectID);

        $title      = $this->lang->company->orgView . $this->lang->colon . $this->lang->group->browse;
        $position[] = $this->lang->group->browse;

        $groups     = $this->group->getList($projectID);
        $groupUsers = array();
        foreach($groups as $group) $groupUsers[$group->id] = $this->group->getUserPairs($group->id);

        $this->view->title      = $title;
        $this->view->position   = $position;
        $this->view->groups     = $groups;
        $this->view->project    = $this->dao->findById($projectID)->from(TABLE_PROJECT)->fetch();
        $this->view->projectID  = $projectID;
        $this->view->programID  = $programID;
        $this->view->groupUsers = $groupUsers;

        $this->display();
    }

    /**
     * Project create a group.
     *
     * @param  int    $projectID
     * @access public
     * @return void
     */
    public function createGroup($projectID = 0)
    {
        $this->loadModel('group');

        if(!empty($_POST))
        {
            $_POST['project'] = $projectID;
            $this->group->create();
            if(dao::isError()) die(js::error(dao::getError()));
            die(js::closeModal('parent.parent'));
        }

        $this->view->title      = $this->lang->company->orgView . $this->lang->colon . $this->lang->group->create;
        $this->view->position[] = $this->lang->group->create;

        $this->display('group', 'create');
    }

    /**
     * Project dynamic.
     *
     * @param  int    $projectID
     * @param  string $type
     * @param  string $param
     * @param  int    $recTotal
     * @param  string $date
     * @param  string $direction  next|pre
     * @access public
     * @return void
     */
    public function dynamic($projectID = 0, $type = 'today', $param = '', $recTotal = 0, $date = '', $direction = 'next')
    {
        $this->project->setMenu($projectID);

        /* Save session. */
        $uri = $this->app->getURI(true);
        $this->session->set('productList',     $uri, 'product');
        $this->session->set('productPlanList', $uri, 'product');
        $this->session->set('releaseList',     $uri, 'product');
        $this->session->set('storyList',       $uri, 'product');
        $this->session->set('taskList',        $uri, 'execution');
        $this->session->set('buildList',       $uri, 'execution');
        $this->session->set('bugList',         $uri, 'qa');
        $this->session->set('caseList',        $uri, 'qa');
        $this->session->set('testtaskList',    $uri, 'qa');

        if(isset($this->config->maxVersion))
        {
            $this->session->set('riskList', $uri, 'project');
            $this->session->set('issueList', $uri, 'project');
        }

        /* Append id for secend sort. */
        $orderBy = $direction == 'next' ? 'date_desc' : 'date_asc';
        $sort    = $this->loadModel('common')->appendOrder($orderBy);

        /* Set the pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage = 50, $pageID = 1);

        /* Set the user and type. */
        $account = 'all';
        if($type == 'account')
        {
            $user = $this->loadModel('user')->getById($param, 'account');
            if($user) $account = $user->account;
        }
        $period  = $type == 'account' ? 'all'  : $type;
        $date    = empty($date) ? '' : date('Y-m-d', $date);
        $actions = $this->loadModel('action')->getDynamic($account, $period, $sort, $pager, 'all', $projectID, 'all', $date, $direction);

        /* The header and position. */
        $project = $this->project->getByID($projectID);
        $this->view->title      = $project->name . $this->lang->colon . $this->lang->project->dynamic;
        $this->view->position[] = html::a($this->createLink('project', 'browse', "projectID=$projectID"), $project->name);
        $this->view->position[] = $this->lang->project->dynamic;

        $this->view->userIdPairs  = $this->loadModel('user')->getTeamMemberPairs($projectID, 'project');
        $this->view->accountPairs = $this->user->getPairs('noletter|nodeleted');

        /* Assign. */
        $this->view->projectID  = $projectID;
        $this->view->type       = $type;
        $this->view->orderBy    = $orderBy;
        $this->view->pager      = $pager;
        $this->view->account    = $account;
        $this->view->param      = $param;
        $this->view->dateGroups = $this->action->buildDateGroup($actions, $direction, $type);
        $this->view->direction  = $direction;
        $this->display();
    }

    /**
     * Execution list.
     *
     * @param  string $status
     * @param  int    $projectID
     * @param  string $orderBy
     * @param  int    $productID
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function execution($status = 'all', $projectID = 0, $orderBy = 'id_desc', $productID = 0, $recTotal = 0, $recPerPage = 10, $pageID = 1)
    {
        $uri = $this->app->getURI(true);
        $this->app->session->set('executionList', $uri, 'project');
        $this->session->set('common_back_url', $this->app->getURI(true),'backlog');

        echo $this->fetch('execution', 'all', "status=$status&projectID=$projectID&orderBy=$orderBy&productID=$productID&recTotal=$recTotal&recPerPage=$recPerPage&pageID=$pageID");
    }

    /**
     * Project qa dashboard.
     *
     * @param  int $projectID
     * @access public
     * @return void
     */
    public function qa($projectID = 0)
    {
        $this->project->setMenu($projectID);
        $this->view->title = $this->lang->project->qa;
        $this->display();
    }

    public function ajaxSelectProductToBug($projectID, $objectType)
    {
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

        $products       = array();
        $linkedProducts = $this->dao->select('id,name')->from(TABLE_PRODUCT)->where('id')->in($productIdList)->andWhere('deleted')->eq('0')->fetchPairs();
        foreach($productPairs as $product)
        {
            $key = $product->app . '-' . $product->id;
            $products[$key] = zget($linkedProducts, $product->id, $product->id);
        }

        $objectTypeTable = $objectType;
        
        if($objectType == 'bugBatchCreate')      $objectTypeTable = 'bug';
        if($objectType == 'testcaseBatchCreate') $objectTypeTable = 'testcase';
        
         $this->project->setMenu($projectID);
        // $products = $this->loadModel('rebirth')->getProjectLinkProductPairs($projectID, 0, $objectTypeTable);
        // unset($products['0-all']);

        $this->view->projectID  = $projectID;
        $this->view->objectType = $objectType;
        $this->view->products   = array('0' => '') + $products;
        $this->display();
    }

    /**
     * Project bug list.
     *
     * @param  int    $projectID
     * @param  int    $productID
     * @param  string $browseType
     * @param  string $branch
     * @param  string $build
     * @param  string $orderBy
     * @param  int    $param
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function bug($projectID = 0, $applicationID = 0, $productID = 'all', $browseType = 'unclosed', $branch = 0, $build = 0, $orderBy = 'status,id_desc', $param = 0, $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $this->loadModel('datatable');
        /* Load these two models. */
        $this->loadModel('bug');
        $this->loadModel('user');
        $this->loadModel('tree');
        $this->loadModel('rebirth');
        /* Set browse type. */
        $browseType = strtolower($browseType);

        /* Set browseType, productID, moduleID and queryID. */
        setcookie('preProductID', $productID, $this->config->cookieLife, $this->config->webRoot, '', $this->config->cookieSecure, true);

        if($this->cookie->preProductID != $productID)
        {
            $_COOKIE['bugModule'] = 0;
            setcookie('bugModule', 0, 0, $this->config->webRoot, '', $this->config->cookieSecure, false);
        }
        if($browseType == 'bymodule') setcookie('bugModule', (int)$param, 0, $this->config->webRoot, '', $this->config->cookieSecure, false);
        if($browseType == 'bysuite')  setcookie('caseSuite', (int)$param, 0, $this->config->webRoot, '', $this->config->cookieSecure, true);
        if($browseType != 'bymodule') $this->session->set('projectBugBrowseType', $browseType);

        $moduleID = ($browseType == 'bymodule') ? (int)$param : ($browseType == 'bysearch' ? 0 : ($this->cookie->bugModule ? $this->cookie->bugModule : 0));
        $queryID  = ($browseType == 'bysearch') ? (int)$param : 0;

        /* Save session. */
        $this->session->set('bugList', $this->app->getURI(true), 'project');
        $this->session->set('projectID', $projectID);
        $this->session->set('applicationID', $applicationID);
        $this->session->set('productID', $productID);
        $this->session->set('moduleID', $moduleID);
        $this->session->set('browseType', $browseType);
        $taskID = 0;
        if(strpos($projectID, 'testtask') === 0)
        {
            $this->app->setMethodName('testtask');

            $taskID   = str_replace('testtask', '', $projectID);
            $testtask = $this->loadModel('testtask')->getById($taskID);

            $projectID     = $testtask->project;
            $productID     = $testtask->product;
            $applicationID = $testtask->applicationID;
        }

        $projects      = $this->project->getPairsByProgram();
        $projectID     = $this->project->saveState($projectID, $projects);
        $productIdList = $this->rebirth->getProductIdList($applicationID, $productID);
        $productsPairs = $this->rebirth->getProductPairs($applicationID, true);
        $products      = $this->rebirth->getProjectLinkProductPairs($projectID, $applicationID, 'bug');

        $specialKey = $applicationID . '-' . $productID;
        if(!isset($products[$specialKey]))
        {
            $firstData     = key($products);
            $keyList       = explode('-', $firstData);
            $applicationID = $keyList[0];
            $productID     = $keyList[1];
        }

        if(empty($productIdList)){
            $linkedList    = $this->loadModel('rebirth')->getProjectLinkProductList($projectID, $applicationID, 'bug');
            $productIdList = $linkedList['productIdList'];
            $productsPairs = $linkedList['productsPairs'];
            if(isset($productsPairs[0])) $productsPairs['na'] = $productsPairs[0];
        }

        $this->project->setMenu($projectID, array('projectID' => $projectID, 'applicationID' => $applicationID, 'productID' => $productID));

        $project = $this->project->getByID($projectID);

        $this->lang->modulePageNav = $this->rebirth->selectProduct($projectID, $applicationID, $productID, 'bug');

        /* 获取固定排序字段。 */
        if(isset($this->config->project->bug->fixedSort)) $orderBy = $this->config->project->bug->fixedSort;

        /* Load pager and get bugs, user. */
        $this->app->loadClass('pager', $static = true);
        $users = $this->user->getPairs('noletter');
        $pager = new pager($recTotal, $recPerPage, $pageID);
        $sort  = $this->loadModel('common')->appendOrder($orderBy);
        $bugs  = $this->bug->getBugs($productIdList, $branch, $browseType, $moduleID, $queryID, $sort, $pager, $projectID, $applicationID);

        /* Process the sql, get the conditon partion, save it to session. */
        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'bug', $browseType == 'needconfirm' ? false : true);

        /* Process the openedBuild and resolvedBuild fields. */
        $bugs = $this->bug->processBuildForBugs($bugs);
        $bugs = $this->bug->processPlanForBugs($bugs);
        /* Get story and task id list. */
        $storyIdList = $taskIdList = array();
        foreach($bugs as $bug)
        {
            if($bug->story)  $storyIdList[$bug->story] = $bug->story;
            if($bug->task)   $taskIdList[$bug->task]   = $bug->task;
            if($bug->toTask) $taskIdList[$bug->toTask] = $bug->toTask;
        }

        $storyList = $storyIdList ? $this->loadModel('story')->getByList($storyIdList) : array();
        $taskList  = $taskIdList  ? $this->loadModel('task')->getByList($taskIdList)   : array();

        /* team member pairs. */
        $memberPairs   = array();
        $memberPairs[] = "";
        $teamMembers   = $this->project->getTeamMembers($projectID);
        foreach($teamMembers as $key => $member) $memberPairs[$key] = $member->realname;

        $testtasks = $this->loadModel('testtask')->getPairs(0, 0, '', '', "oddNumber", $applicationID);
        $this->config->bug->search['params']['linkTesttask']['values'] = $testtasks;

        /* Build the search form. */
        $actionURL = $this->createLink('project', 'bug', "projectID=$projectID&applicationID=$applicationID&productID=$productID&browseType=bysearch&branch=0&build=$build&orderBy=$orderBy&queryID=myQueryID");
        $this->config->bug->search['onMenuBar'] = 'yes';
        $this->loadModel('execution')->buildBugSearchForm($productsPairs, $queryID, $actionURL, $productID, 'project');

        /* Get module tree.*/
        $moduleTree  = '';
        $modulePairs = array();
        $modules     = array();
        $showModule  = !empty($this->config->datatable->projectBug->showModule) ? $this->config->datatable->projectBug->showModule : '';
        if(is_numeric($productID))
        {
            $viewType = 'bug';
            $treeFunc = array('treeModel', 'createProjectBugLink');
            if(!is_null($taskID)) $treeFunc = array('treeModel', 'createProjectTestTaskBugLink');
            $moduleTree  = $this->tree->getTreeMenu($productID, $viewType, $startModuleID = 0, $treeFunc, '', $branch);
            $modulePairs = $showModule ? $this->tree->getModulePairs($productID, $viewType, $showModule) : array();
            $modules     = $this->tree->getOptionMenu($productID, $viewType, $startModuleID = 0, $branch);
        }

        $projects = $this->project->getPairsByProgram();
        $tree     = $moduleID ? $this->tree->getByID($moduleID) : '';

        $productstr = implode(',', $productIdList);

        /* Assign. */
        $this->view->title         = $project->name . $this->lang->colon . $this->lang->bug->common;
        $this->view->products      = $products;
        $this->view->projectID     = $projectID;
        $this->view->bugs          = $bugs;
        $this->view->summary       = $this->bug->summary($bugs);
        $this->view->tabID         = 'bug';
        $this->view->build         = $this->loadModel('build')->getById($build);
        $this->view->builds        = $this->loadModel('build')->getProductBuildPairs($productstr);
        $this->view->planInfo      = $this->loadModel('productplan')->getPairs($productstr);
        $this->view->buildID       = $this->view->build ? $this->view->build->id : 0;
        $this->view->pager         = $pager;
        $this->view->orderBy       = $orderBy;
        $this->view->branches      = array();
        $this->view->testtasks     = $testtasks;
        $this->view->plans         = $this->loadModel('productplan')->getPairs($productIdList);
        $this->view->projects      = $projects;
        $this->view->productsPairs = $productsPairs;
        $this->view->stories       = $storyList;
        $this->view->tasks         = $taskList;
        $this->view->users         = $users;
        $this->view->applicationID = $applicationID;
        $this->view->productID     = $productID;
        $this->view->project       = $project;
        $this->view->branch        = $branch;
        $this->view->memberPairs   = $memberPairs;
        $this->view->param         = $param;
        $this->view->typeTileList  = $this->bug->getChildTypeTileList();
        $this->view->browseType    = $browseType;
        $this->view->modules       = $modules;
        $this->view->moduleTree    = $moduleTree;
        $this->view->moduleName    = $moduleID ? $tree->name : $this->lang->tree->all;
        $this->view->moduleID      = $moduleID;
        $this->view->modulePairs   = $modulePairs;
        $this->view->setModule     = true;
        $this->display();
    }

    /**
     * Project case list.
     *
     * @param  int    $projectID
     * @param  int    $productID
     * @param  int    $branch
     * @param  string $browseType
     * @param  int    $param
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function testcase($projectID = 0, $applicationID = 0, $productID = 'all', $branch = 0, $browseType = 'all', $param = 0, $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $this->loadModel('datatable');
        $this->loadModel('testcase');
        $this->loadModel('testtask');
        $this->loadModel('tree');

        /* Set browse type. */
        $browseType = strtolower($browseType);

        /* Set browseType, productID, moduleID and queryID. */
        setcookie('preProductID', $productID, $this->config->cookieLife, $this->config->webRoot, '', $this->config->cookieSecure, true);

        if($this->cookie->preProductID != $productID)
        {
            $_COOKIE['caseModule'] = 0;
            setcookie('caseModule', 0, 0, $this->config->webRoot, '', $this->config->cookieSecure, false);
        }
        if($browseType == 'bymodule') setcookie('caseModule', (int)$param, 0, $this->config->webRoot, '', $this->config->cookieSecure, false);
        if($browseType == 'bysuite')  setcookie('caseSuite', (int)$param, 0, $this->config->webRoot, '', $this->config->cookieSecure, true);
        if($browseType != 'bymodule') $this->session->set('caseBrowseType', $browseType);

        $moduleID = ($browseType == 'bymodule') ? (int)$param : ($browseType == 'bysearch' ? 0 : ($this->cookie->caseModule ? $this->cookie->caseModule : 0));
        $suiteID  = ($browseType == 'bysuite') ? (int)$param : ($browseType == 'bymodule' ? ($this->cookie->caseSuite ? $this->cookie->caseSuite : 0) : 0);
        $queryID  = ($browseType == 'bysearch') ? (int)$param : 0;

        $this->session->set('caseList', $this->app->getURI(true), $this->app->openApp);
        $this->session->set('productID', $productID);
        $this->session->set('moduleID', $moduleID);
        $this->session->set('applicationID', $applicationID);
        $this->session->set('browseType', $browseType);
        $this->session->set('orderBy', $orderBy);

        /* Save session. */
        $this->session->set('caseList', $this->app->getURI(true), 'project');

        $projects  = $this->project->getPairsByProgram();
        $projectID = $this->project->saveState($projectID, $projects);

        // 判断平移过来的产品是否存在，不存在则取第一个。
        $products   = $this->loadModel('rebirth')->getProjectLinkProductPairs($projectID, $applicationID, 'testcase');

        // 这里的productID要求为all或数字，不支持na
        $specialKey = $applicationID . '-' . $productID;
        if(!isset($products[$specialKey]))
        {
            $firstData     = key($products);
            $keyList       = explode('-', $firstData);
            $applicationID = $keyList[0];
            $productID     = $keyList[1];
        }

        $linkedList = $this->loadModel('rebirth')->getProjectLinkProductList($projectID, $applicationID, 'testcase');

        $productIdList = $linkedList['productIdList'];

        if($productID != 'all')  $productIdList = [$productID];

        $this->project->setMenu($projectID, array('projectID' => $projectID, 'applicationID' => $applicationID, 'productID' => $productID));

        $project  = $this->project->getByID($projectID);
        $this->lang->modulePageNav = $this->rebirth->selectProduct($projectID, $applicationID, $productID, 'testcase');

        /* 获取固定排序字段。 */
        if(isset($this->config->project->testcase->fixedSort)) $orderBy = $this->config->project->testcase->fixedSort;

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = pager::init($recTotal, $recPerPage, $pageID);
        $sort  = $this->loadModel('common')->appendOrder($orderBy);

        $cases = $this->testcase->getTestCases($applicationID, $productIdList, $branch, $browseType, $browseType == 'bysearch' ? $queryID : $suiteID, $moduleID, $sort, $pager, 'no', $projectID);
        if(empty($cases) and $pageID > 1)
        {
            $pager = pager::init(0, $recPerPage, 1);
            $cases = $this->testcase->getTestCases($applicationID, $productIdList, $branch, $browseType, $browseType == 'bysearch' ? $queryID : $suiteID, $moduleID, $sort, $pager, 'no', $projectID);
        }

        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'testcase', false);

        /* Process case for check story changed. */
        $cases = $this->loadModel('story')->checkNeedConfirm($cases);
        $cases = $this->testcase->appendData($cases);

        /* Build the search form. */
        $currentModule = 'project';
        $currentMethod = 'testcase';
        $actionURL     = $this->createLink($currentModule, $currentMethod, "projectID=$projectID&" . "applicationID=$applicationID&productID=$productID&branch=$branch&browseType=bySearch&queryID=myQueryID");
        $this->config->testcase->search['module']    = 'projectTestcase';
        $this->config->testcase->search['onMenuBar'] = 'yes';

        $this->testcase->buildSearchForm($queryID, $actionURL, $applicationID, $productID, $products);

        /* Get module tree.*/
        $moduleTree  = '';
        $modulePairs = array();
        $modules     = array();
        $showModule  = !empty($this->config->datatable->projectTestcase->showModule) ? $this->config->datatable->projectTestcase->showModule : '';

        if(is_numeric($productID) && $productID != 0)
        {
            $viewType    = 'case';
            $moduleTree  = $this->tree->getTreeMenu($productID, $viewType, $startModuleID = 0, array('treeModel', 'createProjectCaseLink'), '', $branch);
            $modulePairs = $showModule ? $this->tree->getModulePairs($productID, $viewType, $showModule) : array();
            $modules     = $this->tree->getOptionMenu($productID, $viewType, $startModuleID = 0, $branch);
        }

        $tree            = $moduleID ? $this->tree->getByID($moduleID) : '';
        $suiteLinkedList = $this->loadModel('rebirth')->getProjectLinkProductList($projectID, $applicationID, 'testsuite');

        if($productID != 'all'){
            $suiteLinkedList['applicationIDList'] = [$applicationID];
            $suiteLinkedList['productIdList']     = [$productID];
        }

        $this->view->title         = $projects[$projectID] . $this->lang->colon . $this->lang->testcase->common;
        $this->view->project       = $project;
        $this->view->projectID     = $projectID;
        $this->view->applicationID = $applicationID;
        $this->view->productID     = $productID;
        $this->view->modules       = $modules;
        $this->view->moduleTree    = $moduleTree;
        $this->view->moduleName    = $moduleID ? $tree->name : $this->lang->tree->all;
        $this->view->moduleID      = $moduleID;
        $this->view->modulePairs   = $modulePairs;
        $this->view->summary       = $this->testcase->summary($cases);
        $this->view->pager         = $pager;
        $this->view->projects      = $projects;
        $this->view->users         = $this->loadModel('user')->getPairs('noletter');
        $this->view->orderBy       = $orderBy;
        $this->view->browseType    = $browseType;
        $this->view->param         = $param;
        $this->view->cases         = $cases;
        $this->view->branchID      = $branch;
        $this->view->products      = $products;
        $this->view->setModule     = true;
        $this->view->branches      = array();
        $this->view->suiteID       = $suiteID;
        $this->view->suiteList     = $this->loadModel('testsuite')->getSuites($suiteLinkedList['applicationIDList'], $suiteLinkedList['productIdList']);

        $this->display();
    }

    /**
     * Project case list.
     *
     * @param  int    $projectID
     * @param  int    $productID
     * @param  int    $branch
     * @param  string $browseType
     * @param  int    $param
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function testsuite($projectID = 0, $applicationID = 0, $productID = 'all', $branch = 0, $browseType = 'all', $param = 0, $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $this->loadModel('datatable');
        $this->loadModel('rebirth');
        $this->loadModel('testsuite');
        /* Save session. */
        $this->session->set('testsuiteList', $this->app->getURI(true), 'project');
        $queryID = ($browseType == 'bysearch') ? (int)$param : 0;

        $projects  = $this->project->getPairsByProgram();
        $projectID = $this->project->saveState($projectID, $projects);

        // 判断平移过来的产品是否存在，不存在则取第一个。
        $products   = $this->loadModel('rebirth')->getProjectLinkProductPairs($projectID, $applicationID, 'testsuite');
        $specialKey = $applicationID . '-' . $productID;
        if(!isset($products[$specialKey]))
        {
            $firstData     = key($products);
            $keyList       = explode('-', $firstData);
            $applicationID = $keyList[0];
            $productID     = $keyList[1];
        }

        $linkedList = $this->loadModel('rebirth')->getProjectLinkProductList($projectID, $applicationID, 'testsuite');

        $applicationIDList = $linkedList['applicationIDList'];
        $productIdList     = $linkedList['productIdList'];

        if($productID != 'all')
        {
            $productIdList     = [$productID];
            $applicationIDList = [$applicationID];
        }

        $this->project->setMenu($projectID, array('projectID' => $projectID, 'applicationID' => $applicationID, 'productID' => $productID));

        $project  = $this->project->getByID($projectID);
        $this->lang->modulePageNav = $this->rebirth->selectProduct($projectID, $applicationID, $productID, 'testsuite');

        /* 获取固定排序字段。 */
        if(isset($this->config->project->testsuite->fixedSort)) $orderBy = $this->config->project->testsuite->fixedSort;

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = pager::init($recTotal, $recPerPage, $pageID);
        $sort  = $this->loadModel('common')->appendOrder($orderBy);

        $suites = $this->testsuite->getSuites($applicationIDList, $productIdList, $sort, $pager);
        if(empty($suites) and $pageID > 1)
        {
            $pager  = pager::init(0, $recPerPage, 1);
            $suites = $this->testsuite->getSuites($applicationIDList, $productIdList, $sort, $pager);
        }

        $this->view->title         = $projects[$projectID] . $this->lang->colon . $this->lang->testsuite->common;
        $this->view->project       = $project;
        $this->view->projectID     = $projectID;
        $this->view->applicationID = $applicationID;
        $this->view->productID     = $productID;
        $this->view->pager         = $pager;
        $this->view->projects      = $projects;
        $this->view->users         = $this->loadModel('user')->getPairs('noletter');
        $this->view->orderBy       = $orderBy;
        $this->view->browseType    = $browseType;
        $this->view->param         = $param;
        $this->view->suites        = $suites;
        $this->view->branchID      = $branch;
        $this->view->products      = $products;

        $this->display();
    }

    /**
     * add testreport method
     *
     * @param int $projectID
     * @param int $applicationID
     * @param int $productID
     * @param string $objectType
     * @param string $extra
     * @param string $orderBy
     * @param int $recTotal
     * @param int $recPerPage
     * @param int $pageID
     * @return void
     * @access public
     */
    public function testreport($projectID = 0, $applicationID = 0, $productID = 'all',  $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        /* load model. */
        $this->loadModel('rebirth');
        $this->loadModel('testreport');
        $this->loadModel('datatable');

        /* set params. */
        $projects   = $this->project->getPairsByProgram();
        $products   = $this->rebirth->getProjectLinkProductPairs($projectID,$applicationID,'testreport');
        $projectID  = $this->project->saveState($projectID, $projects);
        $title      = $projects[$projectID];

        $specialKey = $applicationID . '-' . $productID;

        if(!isset($products[$specialKey]))
        {
            $firstData     = key($products);
            $keyList       = explode('-', $firstData);
            $applicationID = $keyList[0];
            $productID     = $keyList[1];
        }

        foreach($products as $key => $productName)
        {
            $keyList             = explode('-', $key);
            $applicationIDList[] = $keyList[0];
            $productIdList[]     = $keyList[1];
        }
        $applicationIDList = array_unique($applicationIDList);

        if($productID != 'all')
        {
            $productIdList     = [$productID];
            $applicationIDList = [$applicationID];
        }

        $this->lang->modulePageNav = $this->rebirth->selectProduct($projectID, $applicationID, $productID, 'testreport');
        $this->project->setMenu($projectID, array('projectID' => $projectID, '$applicationID' => $applicationID, 'productID' => $productID));

        /* get fix columns. */
        if(isset($this->config->project->testreport->fixedSort)) $orderBy = $this->config->project->testreport->fixedSort;

        /* load Pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $projects = array();
        $tasks    = array();

        $reports = $this->project->getTestreportListForProject($applicationID, $productID, $projectID, $orderBy, $pager);
        foreach($reports as $report)
        {
            $projects[$report->project] = $report->project;
            foreach(explode(',', $report->tasks) as $taskID) $tasks[$taskID] = $taskID;
        }

        $this->session->set('reportList', $this->app->getURI(true), 'project');
        $productList = $this->rebirth->getProductPairs($applicationID, true);

        if($projects) $projects = $this->dao->select('id,name')->from(TABLE_PROJECT)->where('id')->in($projects)->andWhere('`type`')->eq('project')->fetchPairs();
        if($tasks)    $tasks    = $this->dao->select('id,name')->from(TABLE_TESTTASK)->where('id')->in($tasks)->fetchPairs();

        $this->view->title         = $title . $this->lang->colon . $this->lang->testreport->common;
        $this->view->reports       = $reports;
        $this->view->orderBy       = $orderBy;
        $this->view->applicationID = $applicationID;
        $this->view->productID     = $productID;
        $this->view->products      = $products;
        $this->view->projects      = $projects;
        $this->view->projectID     = $projectID;
        $this->view->pager         = $pager;
        $this->view->tasks         = $tasks;
        $this->view->users         = $this->loadModel('user')->getPairs('noclosed|nodeleted');
        $this->view->productList   = $productList;

        $this->display();
    }

    /**
     * Project test task list.
     *
     * @param  int    $projectID
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function testtask($projectID = 0, $applicationID = 0, $productID = 'all', $browseType = 'local,totalStatus', $param = 0, $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $this->loadModel('datatable');
        $this->loadModel('testtask');
        $this->app->loadLang('testreport');

        /* Save session. */
        $this->session->set('testtaskList', $this->app->getURI(true), 'project');
        $this->session->set('caseList', $this->app->getURI(true), 'project');
        $this->session->set('buildList', $this->app->getURI(true), 'project');

        $scopeAndStatus = explode(',', $browseType);

        $this->session->set('testTaskVersionScope', $scopeAndStatus[0]);
        $this->session->set('testTaskVersionStatus', $scopeAndStatus[1]);

        $projects  = $this->project->getPairsByProgram();
        $projectID = $this->project->saveState($projectID, $projects);

        /* 获取固定排序字段。 */
        if(isset($this->config->project->testtask->fixedSort)) $orderBy = $this->config->project->testtask->fixedSort;

        // 判断平移过来的产品是否存在，不存在则取第一个。
        $products = $this->loadModel('rebirth')->getProjectLinkProductPairs($projectID, $applicationID, 'testtask');
        $project  = $this->project->getByID($projectID);

        $this->project->setMenu($projectID, array('projectID' => $projectID, 'applicationID' => $applicationID, 'productID' => $productID));

        $this->lang->modulePageNav = $this->rebirth->selectProduct($projectID, $applicationID, $productID, 'testtask');
        $users = $this->loadModel('user')->getPairs('noclosed|noletter');

         /* Build the search form. */
        $queryID = 0;
        if($scopeAndStatus[1] == 'bySearch')
        {
            $queryID = (int)$param;
        }

        $scope = $this->session->testTaskVersionScope;

        $actionURL = $this->createLink('project', 'testtask', "projectID=$projectID&applicationID=$applicationID&productID=$productID&browseType=$scope,bySearch&queryID=myQueryID");
        $this->config->testtask->search['module'] = 'projecttesttask';
        unset($this->config->testtask->search['fields']['project']);
        $this->testtask->buildSearchForm($queryID, $actionURL, $applicationID, $productID, $projectID);

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = pager::init($recTotal, $recPerPage, $pageID);
        $tasks = $this->testtask->getProjectTasks($projectID, $orderBy, $pager, $applicationID, $productID, $scopeAndStatus, $queryID);

        $this->view->title         = $project->name . $this->lang->colon . $this->lang->project->common;
        $this->view->project       = $project;
        $this->view->projectID     = $projectID;
        $this->view->applicationID = $applicationID;
        $this->view->productID     = $productID;
        $this->view->pager         = $pager;
        $this->view->orderBy       = $orderBy;
        $this->view->tasks         = $tasks;
        $this->view->users         = $users;
        $this->view->products      = $products;
        $this->view->param         = $param;
        $this->view->browseType    = $browseType;
        $this->session->set('common_back_url', $this->app->getURI(true),'backlog');

        $this->display();
    }

    /**
     * Browse builds of a project.
     *
     * @param  string $type      all|product|bysearch
     * @param  int    $param
     * @access public
     * @return void
     */
    public function build($projectID = 0, $type = 'all', $param = 0)
    {
        /* Load module and get project. */
        $this->loadModel('build');
        $project = $this->project->getByID($projectID);
        $this->project->setMenu($projectID);

        $this->session->set('buildList', $this->app->getURI(true), 'project');

        /* Get products' list. */
        $products = $this->project->getProducts($projectID, false);
        $products = array('' => '') + $products;

        /* Build the search form. */
        $type      = strtolower($type);
        $queryID   = ($type == 'bysearch') ? (int)$param : 0;
        $actionURL = $this->createLink('project', 'build', "projectID=$projectID&type=bysearch&queryID=myQueryID");

        $executions = $this->loadModel('execution')->getByProject($projectID, 'all', '', true);
        $this->config->build->search['fields']['execution'] = $this->project->lang->executionCommon;
        $this->config->build->search['params']['execution'] = array('operator' => '=', 'control' => 'select', 'values' => array('' => '') + $executions);

        $this->project->buildProjectBuildSearchForm($products, $queryID, $actionURL, 'project');

        if($type == 'bysearch')
        {
            $builds = $this->build->getProjectBuildsBySearch((int)$projectID, (int)$param);
        }
        else
        {
            $builds = $this->build->getProjectBuilds((int)$projectID, $type, $param);
        }

        /* Set project builds. */
        $projectBuilds = array();
        if(!empty($builds))
        {
            foreach($builds as $build) $projectBuilds[$build->product][] = $build;
        }

        /* Header and position. */
        $this->view->title      = $project->name . $this->lang->colon . $this->lang->execution->build;
        $this->view->position[] = $this->lang->execution->build;

        $this->view->users         = $this->loadModel('user')->getPairs('noletter');
        $this->view->buildsTotal   = count($builds);
        $this->view->projectBuilds = $projectBuilds;
        $this->view->product       = $type == 'product' ? $param : 'all';
        $this->view->projectID     = $projectID;
        $this->view->project       = $project;
        $this->view->products      = $products;
        $this->view->executions    = $executions;
        $this->view->type          = $type;
        $this->display();
    }

    /**
     * Project manage view.
     *
     * @param  int    $groupID
     * @param  int    $projectID
     * @param  int    $programID
     * @access public
     * @return void
     */
    public function manageView($groupID, $projectID, $programID)
    {
        $this->loadModel('group');
        if($_POST)
        {
            $this->group->updateView($groupID);
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => inlink('group', "projectID=$projectID&programID=$programID")));
        }

        $this->project->setMenu($projectID);

        $group = $this->group->getById($groupID);

        $this->view->title      = $group->name . $this->lang->colon . $this->lang->group->manageView;
        $this->view->position[] = $group->name;
        $this->view->position[] = $this->lang->group->manageView;

        $this->view->group    = $group;
        $this->view->products = $this->loadModel('product')->getProductPairsByProject($projectID);
        $this->view->projects = $this->dao->select('*')->from(TABLE_PROJECT)->where('deleted')->eq('0')->andWhere('id')->eq($group->project)->orderBy('order_desc')->fetchPairs('id', 'name');

        $this->display();
    }

    /**
     * Manage privleges of a group.
     *
     * @param  int       $projectID
     * @param  string    $type
     * @param  int       $param
     * @param  string    $menu
     * @param  string    $version
     * @access public
     * @return void
     */
    public function managePriv($projectID, $type = 'byGroup', $param = 0, $menu = '', $version = '')
    {
        $this->loadModel('group');
        if($type == 'byGroup')
        {
            $groupID = $param;
            $group   = $this->group->getById($groupID);
        }

        $this->view->type = $type;
        foreach($this->lang->resource as $moduleName => $action)
        {
            if($this->group->checkMenuModule($menu, $moduleName) or $type != 'byGroup') $this->app->loadLang($moduleName);
        }

        if(!empty($_POST))
        {
            if($type == 'byGroup')  $result = $this->group->updatePrivByGroup($groupID, $menu, $version);
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => inlink('group', "projectID=$group->project")));
        }

        $this->project->setMenu($projectID);

        if($type == 'byGroup')
        {
            $this->group->sortResource();
            $groupPrivs = $this->group->getPrivs($groupID);

            $this->view->title      = $group->name . $this->lang->colon . $this->lang->group->managePriv;
            $this->view->position[] = $group->name;
            $this->view->position[] = $this->lang->group->managePriv;

            /* Join changelog when be equal or greater than this version.*/
            $realVersion = str_replace('_', '.', $version);
            $changelog = array();
            foreach($this->lang->changelog as $currentVersion => $currentChangeLog)
            {
                if(version_compare($currentVersion, $realVersion, '>=')) $changelog[] = join(',', $currentChangeLog);
            }

            $this->view->group      = $group;
            $this->view->changelogs = ',' . join(',', $changelog) . ',';
            $this->view->groupPrivs = $groupPrivs;
            $this->view->groupID    = $groupID;
            $this->view->projectID  = $projectID;
            $this->view->menu       = $menu;
            $this->view->version    = $version;

            /* Unset not project privs. */
            $project = $this->project->getByID($group->project);
            foreach($this->lang->resource as $method => $label)
            {
                if(!in_array($method, $this->config->programPriv->{$project->model})) unset($this->lang->resource->$method);
            }
        }

        $this->display();
    }

    /**
     * Manage project members.
     *
     * @param  int    $projectID
     * @param  int    $dept
     * @access public
     * @return void
     */
    public function manageMembers($projectID, $dept = '')
    {
        /* Load model. */
        $this->loadModel('user');
        $this->loadModel('dept');
        $this->loadModel('execution');
        $this->loadModel('weeklyreport');
        $this->project->setMenu($projectID);

        if(!empty($_POST))
        {
            $this->project->manageMembers($projectID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $link = $this->createLink('project', 'manageMembers', "projectID=$projectID");
            $this->send(array('message' => $this->lang->saveSuccess, 'result' => 'success', 'locate' => $link));
        }

        $project   = $this->project->getById($projectID);
        $users     = $this->user->getPairs('noclosed|nodeleted|devfirst|nofeedback');
        $roles     = $this->user->getUserRoles(array_keys($users));
        $deptUsers = $dept === '' ? array() : $this->dept->getDeptUserPairs($dept);

        $this->view->title      = $this->lang->project->manageMembers . $this->lang->colon . $project->name;
        $this->view->position[] = $this->lang->project->manageMembers;

        $this->view->project        = $project;
        $this->view->users          = $users;
        $this->view->deptUsers      = $deptUsers;
        $this->view->roles          = $roles;
        $this->view->dept           = $dept;
        $this->view->depts          = array('' => '') + $this->dept->getOptionMenu();
        $this->view->currentMembers = $this->project->getTeamMembers($projectID);
        $this->view->statusSelects  = $this->weeklyreport->getSelects();
        $this->display();
    }

    /**
     * Manage members of a group.
     *
     * @param  int    $groupID
     * @param  int    $deptID
     * @access public
     * @return void
     */
    public function manageGroupMember($groupID, $deptID = 0)
    {
        $this->loadModel('group');
        if(!empty($_POST))
        {
            $this->group->updateUser($groupID);
            if(isonlybody()) die(js::closeModal('parent.parent', 'this'));
            die(js::locate($this->createLink('group', 'browse'), 'parent'));
        }

        $group      = $this->group->getById($groupID);
        $groupUsers = $this->group->getUserPairs($groupID);
        $allUsers   = $this->loadModel('dept')->getDeptUserPairs($deptID);
        $otherUsers = array_diff_assoc($allUsers, $groupUsers);

        $title      = $group->name . $this->lang->colon . $this->lang->group->manageMember;
        $position[] = $group->name;
        $position[] = $this->lang->group->manageMember;

        $this->view->title      = $title;
        $this->view->position   = $position;
        $this->view->group      = $group;
        $this->view->deptTree   = $this->loadModel('dept')->getTreeMenu($rooteDeptID = 0, array('deptModel', 'createGroupManageMemberLink'), $groupID);
        $this->view->groupUsers = $groupUsers;
        $this->view->otherUsers = $otherUsers;

        $this->display('group', 'manageMember');
    }

    /**
     * Project copy a group.
     *
     * @param  int    $groupID
     * @access public
     * @return void
     */
    public function copyGroup($groupID)
    {
        $this->loadModel('group');
        if(!empty($_POST))
         {
             $group = $this->group->getByID($groupID);
             $_POST['project'] = $group->project;
             $this->group->copy($groupID);
             if(dao::isError()) die(js::error(dao::getError()));
             die(js::closeModal('parent.parent', 'this'));
         }

         $this->view->title      = $this->lang->company->orgView . $this->lang->colon . $this->lang->group->copy;
         $this->view->position[] = $this->lang->group->copy;
         $this->view->group      = $this->group->getById($groupID);

         $this->display('group', 'copy');
    }

    /**
     * Project edit a group.
     *
     * @param  int    $groupID
     * @access public
     * @return void
     */
    public function editGroup($groupID)
    {
        $this->loadModel('group');
        if(!empty($_POST))
        {
            $this->group->update($groupID);
            die(js::closeModal('parent.parent', 'this'));
        }

        $this->view->title      = $this->lang->company->orgView . $this->lang->colon . $this->lang->group->edit;
        $this->view->position[] = $this->lang->group->edit;
        $this->view->group      = $this->group->getById($groupID);

        $this->display('group', 'edit');
    }

    /**
     * Start project.
     *
     * @param  int    $projectID
     * @access public
     * @return void
     */
    public function start($projectID)
    {
        $this->loadModel('action');
        $project = $this->project->getByID($projectID);

        if(!empty($_POST))
        {
            $changes = $this->project->start($projectID);
            if(dao::isError()) die(js::error(dao::getError()));

            if($this->post->comment != '' or !empty($changes))
            {
                $actionID = $this->action->create('project', $projectID, 'Started', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }

            /* Start all superior projects. */
            if($project->parent)
            {
                $path = explode(',', $project->path);
                $path = array_filter($path);
                foreach($path as $projectID)
                {
                    if($projectID == $projectID) continue;
                    $project = $this->project->getPGMByID($projectID);
                    if($project->status == 'wait' || $project->status == 'suspended')
                    {
                        $changes = $this->project->start($projectID);
                        if(dao::isError()) die(js::error(dao::getError()));

                        if($this->post->comment != '' or !empty($changes))
                        {
                            $actionID = $this->action->create('project', $projectID, 'Started', $this->post->comment);
                            $this->action->logHistory($actionID, $changes);
                        }
                    }
                }
            }

            $this->executeHooks($projectID);
            die(js::reload('parent.parent'));
        }

        $this->view->title      = $this->lang->project->start;
        $this->view->position[] = $this->lang->project->start;
        $this->view->project    = $project;
        $this->view->users      = $this->loadModel('user')->getPairs('noletter');
        $this->view->actions    = $this->action->getList('project', $projectID);
        $this->display();
    }

    /**
     * Suspend a project.
     *
     * @param  int     $projectID
     * @access public
     * @return void
     */
    public function suspend($projectID)
    {
        $this->loadModel('action');

        if(!empty($_POST))
        {
            $changes = $this->project->suspend($projectID);
            if(dao::isError()) die(js::error(dao::getError()));

            if($this->post->comment != '' or !empty($changes))
            {
                $actionID = $this->action->create('project', $projectID, 'Suspended', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }
            $this->executeHooks($projectID);
            die(js::reload('parent.parent'));
        }

        $this->view->title      = $this->lang->project->suspend;
        $this->view->position[] = $this->lang->project->suspend;
        $this->view->users      = $this->loadModel('user')->getPairs('noletter');
        $this->view->actions    = $this->action->getList('project', $projectID);
        $this->view->project    = $this->project->getByID($projectID);

        $this->display();
    }

    /**
     * Close a project.
     *
     * @param  int     $projectID
     * @access public
     * @return void
     */
    public function close($projectID)
    {
        $this->loadModel('action');

        if(!empty($_POST))
        {
            $changes = $this->project->close($projectID);
            if(dao::isError()) die(js::error(dao::getError()));

            if($this->post->comment != '' or !empty($changes))
            {
                $actionID = $this->action->create('project', $projectID, 'Closed', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }
            $this->executeHooks($projectID);
            die(js::reload('parent.parent'));
        }

        $this->view->title      = $this->lang->project->close;
        $this->view->position[] = $this->lang->project->close;
        $this->view->project    = $this->project->getByID($projectID);
        $this->view->users      = $this->loadModel('user')->getPairs('noletter');
        $this->view->actions    = $this->action->getList('project', $projectID);

        $this->display();
    }

    /**
     * Activate a project.
     *
     * @param  int     $projectID
     * @access public
     * @return void
     */
    public function activate($projectID)
    {
        $this->loadModel('action');
        $this->app->loadLang('execution');
        $project = $this->project->getByID($projectID);

        if(!empty($_POST))
        {
            $changes = $this->project->activate($projectID);
            if(dao::isError()) die(js::error(dao::getError()));

            if($this->post->comment != '' or !empty($changes))
            {
                $actionID = $this->action->create('project', $projectID, 'Activated', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }
            $this->executeHooks($projectID);
            die(js::reload('parent.parent'));
        }

        $newBegin = date('Y-m-d');
        $dateDiff = helper::diffDate($newBegin, $project->begin);
        $newEnd   = date('Y-m-d', strtotime($project->end) + $dateDiff * 24 * 3600);

        $this->view->title      = $this->lang->project->activate;
        $this->view->position[] = $this->lang->project->activate;
        $this->view->users      = $this->loadModel('user')->getPairs('noletter');
        $this->view->actions    = $this->action->getList('project', $projectID);
        $this->view->newBegin   = $newBegin;
        $this->view->newEnd     = $newEnd;
        $this->view->project    = $project;

        $this->display();
    }

    /**
     * Delete a project.
     *
     * @param  int     $projectID
     * @param  string  $from
     * @access public
     * @return void
     */
    public function delete($projectID, $confirm = 'no')
    {
        if($confirm == 'no')
        {
            $project = $this->project->getByID($projectID);
            echo js::confirm(sprintf($this->lang->project->confirmDelete, $project->name), $this->createLink('project', 'delete', "projectID=$projectID&confirm=yes"));
            die();
        }
        else
        {
            $this->loadModel('user');
            $this->loadModel('action');

            $this->project->delete(TABLE_PROJECT, $projectID);
            $this->dao->update(TABLE_DOCLIB)->set('deleted')->eq(1)->where('execution')->eq($projectID)->exec();
            $this->user->updateUserView($projectID, 'project');

            /* Delete the execution under the project. */
            $executionIdList = $this->loadModel('execution')->getByProject($projectID);
            if(empty($executionIdList)) die(js::reload('parent'));

            $this->dao->update(TABLE_EXECUTION)->set('deleted')->eq(1)->where('id')->in(array_keys($executionIdList))->exec();
            foreach($executionIdList as $executionID => $execution) $this->action->create('execution', $executionID, 'deleted', '', ACTIONMODEL::CAN_UNDELETED);
            $this->user->updateUserView($executionIdList, 'sprint');

            $this->session->set('project', '');
            die(js::reload('parent'));
        }
    }

    /**
     * Update projects order.
     *
     * @access public
     * @return void
     */
    public function updateOrder()
    {
        $idList  = explode(',', trim($this->post->projects, ','));
        $orderBy = $this->post->orderBy;
        if(strpos($orderBy, 'order') === false) return false;

        $projects = $this->dao->select('id,`order`')->from(TABLE_PROJECT)->where('id')->in($idList)->orderBy($orderBy)->fetchPairs('order', 'id');
        foreach($projects as $order => $id)
        {
            $newID = array_shift($idList);
            if($id == $newID) continue;
            $this->dao->update(TABLE_PROJECT)
                ->set('`order`')->eq($order)
                ->set('lastEditedBy')->eq($this->app->user->account)
                ->set('lastEditedDate')->eq(helper::now())
                ->where('id')->eq($newID)
                ->exec();
        }
    }

    /**
     * Get white list personnel.
     *
     * @param  int    $projectID
     * @param  string $module
     * @param  string $from  project|program|programProject
     * @param  string $objectType
     * @param  string $orderby
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function whitelist($projectID = 0, $module = 'project',$objectType = 'project', $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1, $from = 'project')
    {
        echo $this->fetch('personnel', 'whitelist', "objectID=$projectID&module=$module&objectType=$objectType&orderBy=$orderBy&recTotal=$recTotal&recPerPage=$recPerPage&pageID=$pageID&projectID=$projectID&from=$from");
    }

    /**
     * Adding users to the white list.
     *
     * @param  int     $projectID
     * @param  int     $deptID
     * @param  int     $programID
     * @param  int     $from
     * @access public
     * @return void
     */
    public function addWhitelist($projectID = 0, $deptID = 0, $programID = 0, $from = 'project')
    {
        echo $this->fetch('personnel', 'addWhitelist', "objectID=$projectID&dept=$deptID&objectType=project&module=project&programID=$programID&from=$from");
    }

    /*
     * Removing users from the white list.
     *
     * @param  int     $id
     * @param  string  $confirm
     * @access public
     * @return void
     */
    public function unbindWhitelist($id = 0, $confirm = 'no')
    {
        echo $this->fetch('personnel', 'unbindWhitelist', "id=$id&confirm=$confirm");
    }

    /**
     * Manage products.
     *
     * @param  int    $projectID
     * @param  string $from  project|program|programproject
     * @access public
     * @return void
     */
    public function manageProducts($projectID, $from = 'project')
    {
        $this->loadModel('product');
        $this->loadModel('program');

        if(!empty($_POST))
        {
            if(!isset($_POST['products']))
            {
                dao::$errors['message'][] = $this->lang->project->errorNoProducts;
                $this->send(array('result' => 'fail', 'message' => dao::getError()));
            }

            $oldProducts = $this->project->getProducts($projectID);
            $this->project->updateProducts($projectID);
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $oldProducts  = array_keys($oldProducts);
            $newProducts  = $this->project->getProducts($projectID);
            $newProducts  = array_keys($newProducts);
            $diffProducts = array_merge(array_diff($oldProducts, $newProducts), array_diff($newProducts, $oldProducts));
            if($diffProducts) $this->loadModel('action')->create('project', $projectID, 'Managed', '', !empty($_POST['products']) ? join(',', $_POST['products']) : '');

            $locateLink = inLink('manageProducts', "projectID=$projectID");
            if($from == 'program')  $locateLink = $this->createLink('program', 'browse');
            if($from == 'programproject') $locateLink = $this->session->programProject ? $this->session->programProject : inLink('programProject', "projectID=$projectID");
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $locateLink));
        }

        $project = $this->project->getById($projectID);
        if($this->app->openApp == 'program')
        {
            $this->program->setMenu($project->parent);
        }
        else if($this->app->openApp == 'project')
        {
            $this->project->setMenu($projectID);
        }

        $allProducts    = $this->program->getProductPairs($project->parent, 'assign', 'noclosed');
        $linkedProducts = $this->product->getProducts($project->id);
        $linkedBranches = array();

        /* If the story of the product which linked the project, you don't allow to remove the product. */
        $unmodifiableProducts = array();
        foreach($linkedProducts as $productID => $linkedProduct)
        {
            $projectStories = $this->dao->select('*')->from(TABLE_PROJECTSTORY)->where('project')->eq($projectID)->andWhere('product')->eq($productID)->fetchAll('story');
            if(!empty($projectStories)) array_push($unmodifiableProducts, $productID);
        }

        /* Merge allProducts and linkedProducts for closed product. */
        foreach($linkedProducts as $product)
        {
            if(!isset($allProducts[$product->id])) $allProducts[$product->id] = $product->name;
            if(!empty($product->branch)) $linkedBranches[$product->branch] = $product->branch;
        }

        /* Assign. */
        $this->view->title                = $this->lang->project->manageProducts . $this->lang->colon . $project->name;
        $this->view->position[]           = $this->lang->project->manageProducts;
        $this->view->allProducts          = $allProducts;
        $this->view->linkedProducts       = $linkedProducts;
        $this->view->unmodifiableProducts = $unmodifiableProducts;
        $this->view->branchGroups         = $this->loadModel('branch')->getByProducts(array_keys($allProducts), '', $linkedBranches);

        $this->display();
    }

    /**
     * AJAX: Check products.
     *
     * @param  int    $programID
     * @param  int    $projectID
     * @access public
     * @return void
     */
    public function ajaxCheckProduct($programID, $projectID)
    {
        /* Set vars. */
        $project   = $this->project->getByID($projectID);
        $oldTopPGM = $this->loadModel('program')->getTopByID($project->parent);
        $newTopPGM = $this->program->getTopByID($programID);

        if($oldTopPGM == $newTopPGM) die();

        $response  = array();
        $response['result']  = true;
        $response['message'] = $this->lang->project->changeProgramTip;

        $multiLinkedProducts = $this->project->getMultiLinkedProducts($projectID);
        if($multiLinkedProducts)
        {
            $multiLinkedProjects = array();
            foreach($multiLinkedProducts as $productID => $product)
            {
                $multiLinkedProjects[$productID] = $this->loadModel('product')->getProjectPairsByProduct($productID);
            }
            $response['result']              = false;
            $response['message']             = $multiLinkedProducts;
            $response['multiLinkedProjects'] = $multiLinkedProjects;
        }
        die(json_encode($response));
    }

    public function ajaxGetExecutionSelect($projectID, $executionID = 0)
    {
        $defaults = array('0' => '');
        if(!empty($projectID))
        {
            $executions = $this->project->getExecutionByAvailable($projectID);

            if(!empty($executions)) $defaults += $executions;
        }
        die(html::select('execution', $defaults, $executionID, 'class=form-control'));
    }

    /**
     * 编辑信息
     *
     * @param $projectID
     * @param $field
     */
    public function editProjectInfo($projectID, $field){
        if(!empty($_POST))
        {
            $this->project->updateProjectInfo($projectID);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $url = $this->session->common_back_url ? $this->session->common_back_url : inLink('browse');
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;;
            $response['locate']  = 'parent';
            $response['id']       = $projectID;
            $this->send($response);
        }
        $project = $this->project->getProjectInfoById($projectID);
        $this->view->title      = $this->lang->project->edit;
        $this->view->position[] = $this->lang->project->edit;
        $this->view->project    = $project;
        $this->view->projectID  = $projectID;
        $this->view->field = $field;
        $this->display();
    }
}

