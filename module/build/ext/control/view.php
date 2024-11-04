<?php
include '../../control.php';
class myBuild extends build
{
    /**
     * View a build.
     *
     * @param  int    $buildID
     * @param  string $type
     * @param  string $link
     * @param  string $param
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function view($buildID, $type = 'buildInfo', $link = 'false', $param = '', $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 100, $pageID = 1)
    {
        if($type == 'workWaitList') $type = 'buildInfo';
        $buildID = (int)$buildID;
        $build   = $this->build->getByID($buildID, true);

//        $desc = $this->build->reverseDesc($build);
//        $build->desc = $desc;

        if(!$build) die(js::error($this->lang->notFound) . js::locate('back'));
        $this->session->project = $build->project;

        $this->loadModel('story');
        $this->loadModel('bug');

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        if($this->app->getViewType() == 'mhtml') $recPerPage = 10;

        /* Get product and bugs. */
        $product = $this->loadModel('product')->getById($build->product);
        if(isset($product->type) && $product->type != 'normal') $this->lang->product->branch = sprintf($this->lang->product->branch, $this->lang->product->branchName[ $product->type]);

        $bugPager = new pager($type == 'bug' ? $recTotal : 0, $recPerPage, $type == 'bug' ? $pageID : 1);
        $bugs = $this->dao->select('*')->from(TABLE_BUG)->where('id')->in($build->bugs)->andWhere('deleted')->eq(0)
            ->beginIF($type == 'bug')->orderBy($orderBy)->fi()
            ->page($bugPager)
            ->fetchAll();

        /* Get stories and stages. */
        $storyPager = new pager($type == 'story' ? $recTotal : 0, $recPerPage, $type == 'story' ? $pageID : 1);
        $stories = $this->dao->select('*')->from(TABLE_STORY)->where('id')->in($build->stories)->andWhere('deleted')->eq(0)
            ->beginIF($type == 'story')->orderBy($orderBy)->fi()
            ->page($storyPager)
            ->fetchAll('id');

        $stages = $this->dao->select('*')->from(TABLE_STORYSTAGE)->where('story')->in($build->stories)->andWhere('branch')->eq($build->branch)->fetchPairs('story', 'stage');
        foreach($stages as $storyID => $stage) $stories[$storyID]->stage = $stage;

        /* Set menu. */
        if($this->app->openApp == 'project') $this->loadModel('project')->setMenu($build->project);

        $this->view->title      = "BUILD #$build->id $build->name";
        $this->view->position[] = $this->lang->build->view;
        $this->view->stories    = $stories;
        $this->view->storyPager = $storyPager;

        $generatedBugPager = new pager($type == 'generatedBug' ? $recTotal : 0, $recPerPage, $type == 'generatedBug' ? $pageID : 1);
        $this->view->generatedBugs     = $this->bug->getProjectBugs($build->project, $build->app, $build->product, $build->id, $type, $type == 'generatedBug' ? $orderBy : 'status_desc,id_desc', '', $generatedBugPager);
        $this->view->generatedBugPager = $generatedBugPager;
        $this->executeHooks($buildID);
        $build->secondorder = $this->loadModel('secondorder')->getPairs(explode(',', trim($build->sendlineId,','))) ?? '';
        $problems = $this->loadModel('problem')->getPairsBycode(explode(',',str_replace('\r','',trim($build->problemid,','))));

        $demands = $this->loadModel('demand')->getPairsBycode(explode(',', trim($build->demandid,',')));
        $build->executionName = $this->loadModel('execution')->getExecutionName(array_unique(explode(',',$build->execution)));//isset($executionName) ? $executionName : '';
        $productGroups = array('99999' => '无') + $this->loadModel('product')->getProductNamesByIds(array($build->product)) ?? '';
        $plans = array("" => "",'1'=>'无') + $this->loadModel('productplan')->getPairs( $build->product, 0);
        $this->view->plans         = $plans;
        $consumeds = $this->build->getConsumedsByID($buildID);
        /* Assign. */
        $this->view->canBeChanged = common::canBeChanged('build', $build); // Determines whether an object is editable.
        $this->view->users        = $this->loadModel('user')->getPairs('noletter');
        $this->view->build        = $build;
        $this->view->problems     = $problems;
        $this->view->demands      = $demands;
        $this->view->consumeds    = $consumeds;
        $this->view->buildPairs   = $this->build->getProjectBuildPairs($build->project, 0, 0, 'noempty,notrunk');
        $this->view->actions      = $this->loadModel('action')->getList('build', $buildID);
        $this->view->link         = $link;
        $this->view->param        = $param;
        $this->view->orderBy      = $orderBy;
        $this->view->bugs         = $bugs;
        $this->view->type         = $type;
        $this->view->bugPager     = $bugPager;
        $this->view->products     = $productGroups;
        $this->view->branchName   = $build->productType == 'normal' ? '' : $this->loadModel('branch')->getById($build->branch);
        $this->view->apps         = array('0' => '') + $this->loadModel('application')->getapplicationNameCodePairs();
        $releaseId = $this->dao->select('max(id) id,name')->from(TABLE_RELEASE)->where('build')->eq($buildID)->andWhere('deleted')->eq(0)->fetch();
        $this->view->releaseId    = $releaseId ;
        //安全门禁
        $qualitygateInfo = $this->loadModel('qualitygate')->getQualityGateInfoByBuildId($buildID, 'id, code');
        $this->view->qualitygateInfo = $qualitygateInfo ;
        $this->display();
    }
}
