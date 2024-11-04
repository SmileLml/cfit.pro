<?php
include '../../control.php';
class myProjectrelease extends projectrelease
{
    /**
     * Project: chengfangjinke
     * Method: view
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:22
     * Desc: This is the code comment. This method is called view.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $releaseID
     * @param string $type
     * @param string $link
     * @param string $param
     * @param string $orderBy
     * @param int $recTotal
     * @param int $recPerPage
     * @param int $pageID
     */
    public function view($releaseID, $type = 'releaseInfo', $link = 'false', $param = '', $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 100, $pageID = 1)
    {
        //$typeTemp = $type;
        if($type == 'workWaitList'){
            $type = 'releaseInfo';
        }
        $this->session->set('buildList', $this->app->getURI(true), 'execution');
        $this->session->set('storyList', $this->app->getURI(true), $this->app->openApp);

        $this->loadModel('story');
        $this->loadModel('bug');

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        if($this->app->getViewType() == 'mhtml') $recPerPage = 10;

        $release = $this->projectrelease->getByID((int)$releaseID, true);
        if(!$release) die(js::error($this->lang->notFound) . js::locate('back'));

        $storyPager = new pager($type == 'story' ? $recTotal : 0, $recPerPage, $type == 'story' ? $pageID : 1);
        $stories = $this->dao->select('*')->from(TABLE_STORY)->where('id')->in($release->stories)->andWhere('deleted')->eq(0)
                ->beginIF($type == 'story')->orderBy($orderBy)->fi()
                ->page($storyPager)
                ->fetchAll('id');

        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'story');
        $stages = $this->dao->select('*')->from(TABLE_STORYSTAGE)->where('story')->in($release->stories)->andWhere('branch')->eq($release->branch)->fetchPairs('story', 'stage');
        foreach($stages as $storyID => $stage)$stories[$storyID]->stage = $stage;

        $bugPager = new pager($type == 'bug' ? $recTotal : 0, $recPerPage, $type == 'bug' ? $pageID : 1);
        $bugs = $this->dao->select('*')->from(TABLE_BUG)->where('id')->in($release->bugs)->andWhere('deleted')->eq(0)
            ->beginIF($type == 'bug')->orderBy($orderBy)->fi()
            ->page($bugPager)
            ->fetchAll();
        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'linkedBug');

        $leftBugPager = new pager($type == 'leftBug' ? $recTotal : 0, $recPerPage, $type == 'leftBug' ? $pageID : 1);
        $leftBugs = $this->dao->select('*')->from(TABLE_BUG)->where('id')->in($release->leftBugs)->andWhere('deleted')->eq(0)
            ->beginIF($type == 'leftBug')->orderBy($orderBy)->fi()
            ->page($leftBugPager)
            ->fetchAll();
        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'leftBugs');

        $this->commonAction($release->project, $release->product);
        $product = $this->product->getById($release->product);
        $baseLineList = $this->projectrelease->getBaseLineLogList($releaseID);

        /* Set menu. */
        $this->project->setMenu($release->project);

        $this->executeHooks($releaseID);

        $this->view->title        = "RELEASE #$release->id $release->name/" . ($release->product == '99999' ? '无' :$product->name);
        $this->view->position[]   = $this->lang->release->view;
        $this->view->release      = $release;
        $this->view->stories      = $stories;
        $this->view->bugs         = $bugs;
        $this->view->leftBugs     = $leftBugs;
        $this->view->actions      = $this->loadModel('action')->getList('release', $releaseID);
        $this->view->users        = $this->loadModel('user')->getPairs('noletter');
        $this->view->type         = $type;
        $this->view->link         = $link;
        $this->view->param        = $param;
        $this->view->orderBy      = $orderBy;
        $this->view->branchName   = $release->productType == 'normal' ? '' : $this->loadModel('branch')->getById($release->branch);
        $this->view->storyPager   = $storyPager;
        $this->view->bugPager     = $bugPager;
        $this->view->leftBugPager = $leftBugPager;
        $this->view->projectID    = $release->project;
        $this->view->plans       = array("" => "",'1'=>'无') + $this->loadModel('productplan')->getPairs($release->product, 0);
        $this->view->apps        = array('0' => '') + $this->loadModel('application')->getapplicationNameCodePairs();
        $this->view->baseLineList = $baseLineList;
        $this->display();
    }
}
