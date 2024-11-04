<?php
/**
 * The model file of case module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     case
 * @version     $Id: model.php 5108 2013-07-12 01:59:04Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php
class testcaseModel extends model
{
    /**
     * Set menu.
     *
     * @param  array  $products
     * @param  int    $productID
     * @param  int    $branch
     * @param  int    $moduleID
     * @param  int    $suiteID
     * @param  string $orderBy
     * @access public
     * @return void
     */
    public function setMenu($products, $productID, $branch = 0, $moduleID = 0, $suiteID = 0, $orderBy = 'id_desc')
    {
        $this->loadModel('qa')->setMenu($products, $productID, $branch, $moduleID, 'case');
    }

    /**
     * Create a case.
     *
     * @param int $bugID
     * @access public
     * @return void
     */
    function create($bugID)
    {
        $now    = helper::now();
        $status = $this->getStatus('create');
        $case   = fixer::input('post')
            ->add('status', $status)
            ->add('version', 1)
            ->add('fromBug', $bugID)
            ->setDefault('openedBy', $this->app->user->account)
            ->setDefault('openedDate', $now)
            ->setIF($this->post->story != false, 'storyVersion', $this->loadModel('story')->getVersion((int)$this->post->story))
            ->remove('steps,expects,files,labels,stepType,forceNotReview')
            ->setDefault('story', 0)
            ->cleanInt('story,product,branch,module')
            ->join('stage', ',')
            ->get();

        $param = '';
        if(!empty($case->lib))     $param = "lib={$case->lib}";
        if(!empty($case->product)) $param = "product={$case->product}";
        $result = $this->loadModel('common')->removeDuplicate('case', $case, $param);
        if($result['stop']) return array('status' => 'exists', 'id' => $result['duplicate']);

        /* Value of story may be showmore. */
        $case->story = (int)$case->story;
        $this->dao->insert(TABLE_CASE)->data($case)->autoCheck()->batchCheck($this->config->testcase->create->requiredFields, 'notempty')->exec();
        if(!$this->dao->isError())
        {
            $caseID = $this->dao->lastInsertID();
            $this->loadModel('file')->saveUpload('testcase', $caseID);
            $parentStepID = 0;
            $this->loadModel('score')->create('testcase', 'create', $caseID);
            foreach($this->post->steps as $stepID => $stepDesc)
            {
                if(empty($stepDesc)) continue;
                $stepType      = $this->post->stepType;
                $step          = new stdClass();
                $step->type    = ($stepType[$stepID] == 'item' and $parentStepID == 0) ? 'step' : $stepType[$stepID];
                $step->parent  = ($step->type == 'item') ? $parentStepID : 0;
                $step->case    = $caseID;
                $step->version = 1;
                $step->desc    = htmlspecialchars($stepDesc);
                $step->expect  = $step->type == 'group' ? '' : htmlspecialchars($this->post->expects[$stepID]);
                $this->dao->insert(TABLE_CASESTEP)->data($step)->autoCheck()->exec();
                if($step->type == 'group') $parentStepID = $this->dao->lastInsertID();
                if($step->type == 'step')  $parentStepID = 0;
            }

            /* If the story is linked project, make the case link the project. */
            $this->syncCase2Project($case, $caseID);

            return array('status' => 'created', 'id' => $caseID);
        }
    }

    /**
     * Batch create cases.
     *
     * @param  int    $productID
     * @param  int    $storyID
     * @access public
     * @return void
     */
    function batchCreate($applicationID, $productID, $branch, $storyID)
    {
        $productID = (int)$productID;
        $branch    = (int)$branch;
        $now       = helper::now();
        $cases     = fixer::input('post')->get();

        $result = $this->loadModel('common')->removeDuplicate('case', $cases, "product={$productID}");
        $cases  = $result['data'];

        foreach($cases->title as $i => $title)
        {
            if(!empty($cases->title[$i]) and empty($cases->type[$i])) die(js::alert(sprintf($this->lang->error->notempty, $this->lang->testcase->type)));
        }

        $module = 0;
        $story  = 0;
        $type   = '';
        $pri    = 3;
        foreach($cases->title as $i => $title)
        {
            $module = $cases->module[$i] == 'ditto' ? $module : $cases->module[$i];
            $story  = $cases->story[$i] == 'ditto'  ? $story  : $cases->story[$i];
            $type   = $cases->type[$i] == 'ditto'   ? $type   : $cases->type[$i];
            $pri    = $cases->pri[$i] == 'ditto'    ? $pri    : $cases->pri[$i];
            $cases->module[$i] = (int)$module;
            $cases->story[$i]  = !empty($storyID) ? $storyID : (int)$story;
            $cases->type[$i]   = $type;
            $cases->pri[$i]    = $pri;
        }

        $this->loadModel('story');
        $extendFields   = $this->getFlowExtendFields();
        $storyVersions  = array();
        $forceNotReview = $this->forceNotReview();
        $data           = array();
        foreach($cases->title as $i => $title)
        {
            if(empty($title)) continue;

            $data[$i] = new stdclass();
            $data[$i]->product      = $productID;
            $data[$i]->project      = $cases->project[$i];
            $data[$i]->branch       = $cases->branch[$i];
            $data[$i]->module       = $cases->module[$i];
            $data[$i]->type         = $cases->type[$i];
            $data[$i]->pri          = $cases->pri[$i];
            $data[$i]->stage        = empty($cases->stage[$i]) ? '' : implode(',', $cases->stage[$i]);
            $data[$i]->story        = $cases->story[$i];
            $data[$i]->color        = $cases->color[$i];
            $data[$i]->title        = $cases->title[$i];
            $data[$i]->precondition = $cases->precondition[$i];
            $data[$i]->keywords     = $cases->keywords[$i];
            $data[$i]->openedBy     = $this->app->user->account;
            $data[$i]->openedDate   = $now;
            $data[$i]->status       = $forceNotReview || $cases->needReview[$i] == 0 ? 'normal' : 'wait';
            $data[$i]->version      = 1;

            $caseStory = $data[$i]->story;
            $data[$i]->storyVersion = isset($storyVersions[$caseStory]) ? $storyVersions[$caseStory] : 0;
            if($caseStory and !isset($storyVersions[$caseStory]))
            {
                $data[$i]->storyVersion = $this->story->getVersion($caseStory);
                $storyVersions[$caseStory] = $data[$i]->storyVersion;
            }

            foreach($extendFields as $extendField)
            {
                $data[$i]->{$extendField->field} = htmlspecialchars($this->post->{$extendField->field}[$i]);
                $message = $this->checkFlowRule($extendField, $data[$i]->{$extendField->field});
                if($message) die(js::alert($message));
            }

            foreach(explode(',', $this->config->testcase->create->requiredFields) as $field)
            {
                $field = trim($field);
                if($field and empty($data[$i]->$field)) die(js::alert(sprintf($this->lang->error->notempty, $this->lang->testcase->$field)));
            }
        }

        foreach($data as $i => $case)
        {
            $case->applicationID = $applicationID;
            $this->dao->insert(TABLE_CASE)->data($case)
                ->autoCheck()
                ->batchCheck($this->config->testcase->create->requiredFields, 'notempty')
                ->exec();

            if(dao::isError())
            {
                echo js::error(dao::getError());
                die(js::reload('parent'));
            }

            $caseID = $this->dao->lastInsertID();

            /* If the story is linked project, make the case link the project. */
            $this->syncCase2Project($case, $caseID);
            $this->executeHooks($caseID);

            $this->loadModel('score')->create('testcase', 'create', $caseID);
            $actionID = $this->loadModel('action')->create('case', $caseID, 'Opened');
        }
        if(!dao::isError()) $this->loadModel('score')->create('ajax', 'batchCreate');
    }

    /**
     * Get cases of a module.
     *
     * @param  int    $productID
     * @param  int    $moduleIdList
     * @param  string $orderBy
     * @param  object $pager
     * @param  string $auto   no|unit
     * @access public
     * @return array
     */
    public function getModuleCases($applicationID, $productIdList, $branch = 0, $moduleIdList = 0, $orderBy = 'id_desc', $pager = null, $browseType = '', $auto = 'no')
    {
        return $this->dao->select('t1.*, t2.title as storyTitle')->from(TABLE_CASE)->alias('t1')
            ->leftJoin(TABLE_STORY)->alias('t2')->on('t1.story=t2.id')
            ->where('t1.applicationID')->eq($applicationID)
            ->andWhere('t1.product')->in($productIdList)
            ->beginIF($this->app->openApp == 'project')->andWhere('t1.project')->eq($this->session->project)->fi()
            ->beginIF($branch)->andWhere('t1.branch')->eq($branch)->fi()
            ->beginIF($moduleIdList)->andWhere('t1.module')->in($moduleIdList)->fi()
            ->beginIF($browseType == 'wait')->andWhere('t1.status')->eq($browseType)->fi()
            ->beginIF($auto == 'unit')->andWhere('t1.auto')->eq('unit')->fi()
            ->beginIF($auto != 'unit')->andWhere('t1.auto')->ne('unit')->fi()
            ->andWhere('t1.deleted')->eq('0')
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');
    }

    /**
     * Get project cases of a module.
     *
     * @param  int|array    $productID
     * @param  int          $moduleIdList
     * @param  string       $orderBy
     * @param  object       $pager
     * @param  string       $auto   no|unit
     * @access public
     * @return array
     */
    public function getModuleProjectCases($projectID, $applicationID, $productID, $branch = 0, $moduleIdList = 0, $orderBy = 'id_desc', $pager = null, $browseType = '', $auto = 'no')
    {
        $result = $this->dao->select('t2.*')->from(TABLE_CASE)->alias('t2')
            ->where('1=1')
            ->beginIF($productID != 'all')->andWhere('t2.product')->in($productID)->fi()
            ->beginIF(!empty($applicationID))->andWhere('t2.applicationID')->eq((int)$applicationID)->fi()
            ->andWhere('t2.project')->eq($projectID)
            ->beginIF($branch)->andWhere('t2.branch')->eq($branch)->fi()
            ->beginIF($moduleIdList)->andWhere('t2.module')->in($moduleIdList)->fi()
            ->beginIF($browseType == 'wait')->andWhere('t2.status')->eq($browseType)->fi()
            ->beginIF($auto == 'unit')->andWhere('t2.auto')->eq('unit')->fi()
            ->beginIF($auto != 'unit')->andWhere('t2.auto')->ne('unit')->fi()
            ->andWhere('t2.deleted')->eq('0')
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');

        return $result;
    }

    /**
     * Get project cases.
     *
     * @param  int    $projectID
     * @param  string $orderBy
     * @param  object $pager
     * @param  string $browseType
     * @access public
     * @return array
     */
    public function getProductProjectCases($applicationID, $productID, $projectID, $orderBy = 'id_desc', $browseType = '')
    {
        $result = $this->dao->select('id,title')->from(TABLE_CASE)
            ->where('applicationID')->eq((int)$applicationID)
            ->andWhere('product')->eq((int)$productID)
            ->andWhere('project')->eq((int)$projectID)
            ->beginIF($browseType != 'all')->andWhere('status')->eq($browseType)->fi()
            ->andWhere('auto')->ne('unit')
            ->andWhere('deleted')->eq('0')
            ->orderBy($orderBy)
            ->fetchPairs();
        return $result;
    }

    /**
     * Get by suite.
     *
     * @param  int    $productID
     * @param  int    $branch
     * @param  int    $suiteID
     * @param  array  $moduleIdList
     * @param  string $orderBy
     * @param  object $pager
     * @param  string $auto    no|unit
     * @access public
     * @return void
     */
    public function getBySuite($applicationID, $productIdList, $branch = 0, $suiteID, $moduleIdList = 0, $orderBy = 'id_desc', $pager = null, $auto = 'no')
    {
        return $this->dao->select('t1.*, t2.title as storyTitle, t3.version as version')->from(TABLE_CASE)->alias('t1')
            ->leftJoin(TABLE_STORY)->alias('t2')->on('t1.story=t2.id')
            ->leftJoin(TABLE_SUITECASE)->alias('t3')->on('t1.id=t3.case')
            ->where(1)
            ->beginIF($applicationID)->andWhere('t1.applicationID')->eq((int)$applicationID)->fi()
            ->andWhere('t1.product')->in($productIdList)
            ->beginIF($this->app->openApp == 'project')->andWhere('t1.project')->eq($this->session->project)->fi()
            ->andWhere('t3.suite')->eq((int)$suiteID)
            ->beginIF($branch)->andWhere('t1.branch')->eq($branch)->fi()
            ->beginIF($moduleIdList)->andWhere('t1.module')->in($moduleIdList)->fi()
            ->beginIF($auto == 'unit')->andWhere('t1.auto')->eq('unit')->fi()
            ->beginIF($auto != 'unit')->andWhere('t1.auto')->ne('unit')->fi()
            ->andWhere('t1.deleted')->eq('0')
            ->orderBy($orderBy)->page($pager)->fetchAll('id');
    }

    /**
     * Get case info by ID.
     *
     * @param  int    $caseID
     * @param  int    $version
     * @access public
     * @return object|bool
     */
    public function getById($caseID, $version = 0)
    {
        $case = $this->dao->findById($caseID)->from(TABLE_CASE)->fetch();
        if(!$case) return false;
        foreach($case as $key => $value) if(strpos($key, 'Date') !== false and !(int)substr($value, 0, 4)) $case->$key = '';
        if($case->story)
        {
            $story = $this->dao->findById($case->story)->from(TABLE_STORY)->fields('title, status, version')->fetch();
            $case->storyTitle         = $story->title;
            $case->storyStatus        = $story->status;
            $case->latestStoryVersion = $story->version;
        }
        if($case->fromBug) $case->fromBugTitle      = $this->dao->findById($case->fromBug)->from(TABLE_BUG)->fields('title')->fetch('title');

        $case->toBugs = array();
        $toBugs       = $this->dao->select('id, title')->from(TABLE_BUG)->where('`case`')->eq($caseID)->fetchAll();
        foreach($toBugs as $toBug) $case->toBugs[$toBug->id] = $toBug->title;

        if($case->linkCase or $case->fromCaseID) $case->linkCaseTitles = $this->dao->select('id,title')->from(TABLE_CASE)->where('id')->in($case->linkCase)->orWhere('id')->eq($case->fromCaseID)->fetchPairs();
        if($version == 0) $version = $case->version;
        $case->steps = $this->dao->select('*')->from(TABLE_CASESTEP)->where('`case`')->eq($caseID)->andWhere('version')->eq($version)->orderBy('id')->fetchAll('id');
        $case->files = $this->loadModel('file')->getByObject('testcase', $caseID);
        $case->currentVersion = $version ? $version : $case->version;
        return $case;
    }

    /**
     * Get case list.
     *
     * @param  int|array|string $caseIDList
     * @access public
     * @return array
     */
    public function getByList($caseIDList = 0)
    {
        return $this->dao->select('*')->from(TABLE_CASE)
            ->where('deleted')->eq(0)
            ->beginIF($caseIDList)->andWhere('id')->in($caseIDList)->fi()
            ->fetchAll('id');
    }

    /**
     * Get test cases.
     *
     * @param  int    $productIdList
     * @param  int    $branch
     * @param  string $browseType
     * @param  int    $queryID
     * @param  int    $moduleID
     * @param  string $sort
     * @param  object $pager
     * @param  string $auto   no|unit
     * @access public
     * @return array
     */
    public function getTestCases($applicationID, $productIdList, $branch, $browseType, $queryID, $moduleID, $sort, $pager, $auto = 'no', $projectID = 0)
    {
        /* Set modules and browse type. */
        $modules    = $moduleID ? $this->loadModel('tree')->getAllChildId($moduleID) : '0';
        $browseType = ($browseType == 'bymodule' and $this->session->caseBrowseType and $this->session->caseBrowseType != 'bysearch') ? $this->session->caseBrowseType : $browseType;
        $group      = $this->lang->navGroup->testcase;

        /* By module or all cases. */
        $cases = array();
        if($browseType == 'bymodule' or $browseType == 'all' or $browseType == 'wait')
        {
            if($projectID)
            {
                $cases = $this->getModuleProjectCases($projectID, $applicationID, $productIdList, $branch, $modules, $sort, $pager, $browseType, $auto);
            }
            else
            {
                $cases = $this->getModuleCases($applicationID, $productIdList, $branch, $modules, $sort, $pager, $browseType, $auto);
            }
        }
        elseif($browseType == 'needconfirm')
        {
            /* Cases need confirmed. */
            $cases = $this->dao->select('t1.*, t2.title AS storyTitle')->from(TABLE_CASE)->alias('t1')
                ->leftJoin(TABLE_STORY)->alias('t2')->on('t1.story = t2.id')
                ->where("t2.status = 'active'")
                ->beginIF(!empty($productIdList))->andWhere('t1.product')->in($productIdList)->fi()
                ->beginIF($applicationID)->andWhere('t1.applicationID')->eq($applicationID)->fi()
                ->andWhere('t1.deleted')->eq(0)
                ->andWhere('t2.version > t1.storyVersion')
                ->beginIF($projectID)->andWhere('t1.project')->eq($projectID)->fi()
                ->beginIF($branch)->andWhere('t1.branch')->eq($branch)->fi()
                ->beginIF($modules)->andWhere('t1.module')->in($modules)->fi()
                ->beginIF($auto != 'unit')->andWhere('t1.auto')->ne('unit')->fi()
                ->beginIF($auto == 'unit')->andWhere('t1.auto')->eq('unit')->fi()
                ->orderBy($sort)
                ->page($pager)
                ->fetchAll();
        }
        elseif($browseType == 'bysuite')
        {
            $cases = $this->getBySuite($applicationID, $productIdList, $branch, $queryID, $modules, $sort, $pager, $auto);
        }
        /* By search. */
        elseif($browseType == 'bysearch')
        {
            if($projectID)
            {
                $cases = $this->getByProjectSearch($projectID, $applicationID, $productIdList, $queryID, $sort, $pager, $branch, $auto);
            }
            else
            {
                $cases = $this->getBySearch($applicationID, $productIdList, $queryID, $sort, $pager, $branch, $auto);
            }
        }

        return $cases;
    }

    /**
     * Get cases by search.
     *
     * @param  int    $productID
     * @param  int    $queryID
     * @param  string $orderBy
     * @param  object $pager
     * @param  string $auto   no|unit
     * @access public
     * @return array
     */
    public function getBySearch($applicationID, $productIdList, $queryID, $orderBy, $pager = null, $branch = 0, $auto = 'no')
    {
        $withJoin = false;

        if($queryID)
        {
            $query = $this->loadModel('search')->getQuery($queryID);
            if($query)
            {
                $this->session->set('testcaseQuery', $query->sql);
                $this->session->set('testcaseForm', $query->form);
            }
            else
            {
                $this->session->set('testcaseQuery', ' 1 = 1');
            }
        }
        else
        {
            if($this->session->testcaseQuery == false) $this->session->set('testcaseQuery', ' 1 = 1');
        }
        $caseQuery = $this->session->testcaseQuery;

        // 是否出现`linkTesttask`
        if(strpos($caseQuery, '`linkTesttask`') !== false) $withJoin = true;

        if($withJoin) $orderBy  = 't1.' . $orderBy;

        /* 搜索添加中所属产品等于NA产品时额外处理。 */
        if(strpos($caseQuery,"`product` = 'na'") !== false)
        {
            $caseQuery = str_replace("`product` = 'na'", '1', $caseQuery);
            $caseQuery = $caseQuery . " AND `product` = '0'";
        }

        /* 搜索条件中不包含所属产品这个搜索条件时。 */
        if(is_array($productIdList)) $productIdList = helper::dbIN($productIdList);
        if(strpos($caseQuery, '`product` =') === false) $caseQuery .= ' AND `product` ' . $productIdList;

        /* 搜索条件中包含所属产品等于全部产品这个条件时。 */
        $allProduct = "`product` = 'all'";
        if(strpos($this->session->testcaseQuery, $allProduct) !== false)
        {
            $products = $this->loadModel('rebirth')->getAllProductIdList($applicationID);
            $products = implode(',', $products);
            $caseQuery = str_replace($allProduct, '1', $caseQuery);
            $caseQuery = $caseQuery . ' AND `product` ' . helper::dbIN($products);
        }

        if($withJoin) $caseQuery = $this->processCaseQuery($caseQuery);
        $pagerField = $withJoin ? 't1.id' : '';

        $cases = $this->dao
            ->beginIF($withJoin)->select('distinct t1.*')->fi()
            ->beginIF(!$withJoin)->select('*')->fi()
            ->from(TABLE_CASE)->beginIF($withJoin)->alias('t1')->fi()
            ->beginIF($withJoin)->leftJoin(TABLE_TESTRUN)->alias('t2')->on('t2.case=t1.id')->fi()
            ->where($caseQuery)
            ->andWhere('applicationID')->eq((int)$applicationID)
            ->beginIF($this->app->openApp == 'project')->andWhere('project')->eq($this->session->project)->fi()
            ->beginIF($auto != 'unit')->andWhere('auto')->ne('unit')->fi()
            ->beginIF($auto == 'unit')->andWhere('auto')->eq('unit')->fi()
            ->andWhere('deleted')->eq(0)
            ->orderBy($orderBy)->page($pager, $pagerField)->fetchAll('id');

        return $cases;
    }

    /**
     * Get cases by search under project app.
     *
     * @param  int    $projectID
     * @param  int    $productID
     * @param  int    $queryID
     * @param  string $orderBy
     * @param  object $pager
     * @param  string $auto   no|unit
     * @access public
     * @return array
     */
    public function getByProjectSearch($projectID ,$applicationID, $productIdList, $queryID, $orderBy, $pager = null, $branch = 0, $auto = 'no')
    {
        $withJoin = false;

        $products = $this->loadModel('rebirth')->getProjectLinkProductPairs($projectID, $applicationID, 'testcase');

        if($queryID)
        {
            $query = $this->loadModel('search')->getQuery($queryID);
            if($query)
            {
                $this->session->set('projectTestcaseQuery', $query->sql);
                $this->session->set('projectTestcaseForm', $query->form);
            }
            else
            {
                $this->session->set('projectTestcaseQuery', ' 1 = 1');
            }
        }
        else
        {
            if($this->session->projectTestcaseQuery == false) $this->session->set('projectTestcaseQuery', ' 1 = 1');
        }
        $caseQuery = $this->session->projectTestcaseQuery;

        // 是否出现`linkTesttask`
        if(strpos($caseQuery, '`linkTesttask`') !== false) $withJoin = true;

        if($withJoin) $orderBy  = 't1.' . $orderBy;

        // 正则匹配是否出现`product` = '数字-数字'，如果出现，那么数字就是applicationID，第二个数字就是产品ID
        // 该判断技能查询出 系统-产品 的情况， 也能查询出 系统-na 的情况， 因为na在提交列表中是0而不是na字符串
        if(preg_match("/`product` = '(\d+)-(\d+)'/", $caseQuery, $matches))
        {
            $applicationID = $matches[1];
            $productID     = $matches[2];
            $caseQuery     = str_replace("`product` = '{$applicationID}-{$productID}'", '1', $caseQuery);
            $caseQuery     = $caseQuery . " AND `product` = '{$productID}'";
        }

        /* 搜索条件中不包含所属产品这个搜索条件时。 */
        if(is_array($productIdList)) $productIdList = helper::dbIN($productIdList);
        if(strpos($caseQuery, '`product` =') === false) $caseQuery .= ' AND `product` ' . $productIdList;

        /* 搜索条件中包含所属产品等于全部产品这个条件时。 */
        $allProduct = "`product` = '0-all'";
        if(strpos($this->session->testcaseQuery, $allProduct) !== false)
        {
            $productIdList = [];
            $applicationID = [];

            foreach($products as $key => $productName)
            {
                $keyList         = explode('-', $key);
                $applicationID[] = $keyList[0];
                $productIdList[] = $keyList[1];
            }

            $caseQuery = str_replace($allProduct, '1', $caseQuery);
            $caseQuery = $caseQuery . ' AND `product` ' . helper::dbIN($productIdList);
        }

        if($withJoin) $caseQuery = $this->processCaseQuery($caseQuery);
        $pagerField = $withJoin ? 't1.id' : '';

        $cases = $this->dao
            ->beginIF($withJoin)->select('distinct t1.*')->fi()
            ->beginIF(!$withJoin)->select('*')->fi()
            ->from(TABLE_CASE)->beginIF($withJoin)->alias('t1')->fi()
            ->beginIF($withJoin)->leftJoin(TABLE_TESTRUN)->alias('t2')->on('t2.case=t1.id')->fi()
            ->where($caseQuery)
            ->beginIF($applicationID)->andWhere('applicationID')->in($applicationID)->fi()
            ->andWhere('project')->eq($projectID)
            ->beginIF($auto != 'unit')->andWhere('auto')->ne('unit')->fi()
            ->beginIF($auto == 'unit')->andWhere('auto')->eq('unit')->fi()
            ->andWhere('deleted')->eq(0)
            ->orderBy($orderBy)->page($pager, $pagerField)->fetchAll('id');

        return $cases;
    }

    public function processCaseQuery($caseQuery)
    {
        $caseQuery = str_replace('`linkTesttask`', 't2.task', $caseQuery);

        $fields = $this->config->testcase->search['fields'];

        foreach($fields as $field => $name)
        {
            $caseQuery = str_replace("`$field`", 't1.' . $field, $caseQuery);
        }

        return $caseQuery;
    }

    /**
     * Get cases by assignedTo.
     *
     * @param  string $account
     * @param  string $orderBy
     * @param  object $pager
     * @param  string $auto  no|unit|skip
     * @access public
     * @return array
     */
    public function getByAssignedTo($account, $orderBy = 'id_desc', $pager = null, $auto = 'no', $from = '')
    {
        if($from == 'my')
        {
            $result = $this->dao->select('t1.*,t2.applicationID,t2.project,t2.pri,t2.title,t2.type,t2.openedBy,t2.color,t2.product,t2.branch,t2.module,t2.status,t3.name as taskName')
                ->from(TABLE_TESTRUN)->alias('t1')
                ->leftJoin(TABLE_CASE)->alias('t2')->on('t1.case = t2.id')
                ->leftJoin(TABLE_TESTTASK)->alias('t3')->on('t1.task = t3.id')
                ->where('(t1.assignedTo')->eq($account)->orWhere('t1.lastRunner')->eq($account)->orWhere('t2.openedBy')->eq($account)->markRight(1)
                ->andWhere('t3.deleted')->eq(0)
                ->andWhere('t2.deleted')->eq(0)
                ->beginIF($auto != 'skip' and $auto != 'unit')->andWhere('t2.auto')->ne('unit')->fi()
                ->beginIF($auto == 'unit')->andWhere('t2.auto')->eq('unit')->fi()
                ->orderBy($orderBy)->page($pager)->fetchAll('id');
            return $result;
        }
        else
        {
            return $this->dao->select('t1.*,t2.project,t2.pri,t2.title,t2.type,t2.openedBy,t2.color,t2.product,t2.branch,t2.module,t2.status,t3.name as taskName')
                ->from(TABLE_TESTRUN)->alias('t1')
                ->leftJoin(TABLE_CASE)->alias('t2')->on('t1.case = t2.id')
                ->leftJoin(TABLE_TESTTASK)->alias('t3')->on('t1.task = t3.id')
                ->where('t1.assignedTo')->eq($account)
                ->andWhere('t3.deleted')->eq(0)
                ->andWhere('t2.deleted')->eq(0)
                ->beginIF($auto != 'skip' and $auto != 'unit')->andWhere('t2.auto')->ne('unit')->fi()
                ->beginIF($auto == 'unit')->andWhere('t2.auto')->eq('unit')->fi()
                ->orderBy($orderBy)->page($pager)->fetchAll('id');
        }
    }

    /**
     * Get cases by openedBy
     *
     * @param  string $account
     * @param  string $orderBy
     * @param  object $pager
     * @param  string $auto   no|unit|skip
     * @access public
     * @return array
     */
    public function getByOpenedBy($account, $orderBy = 'id_desc', $pager = null, $auto = 'no')
    {
        return $this->dao->findByOpenedBy($account)->from(TABLE_CASE)
            ->beginIF($auto != 'skip')->andWhere('product')->ne(0)->fi()
            ->andWhere('deleted')->eq(0)
            ->beginIF($auto != 'skip' and $auto != 'unit')->andWhere('auto')->ne('unit')->fi()
            ->beginIF($auto == 'unit')->andWhere('auto')->eq('unit')->fi()
            ->orderBy($orderBy)->page($pager)->fetchAll();
    }

    /**
     * Get cases of a story.
     *
     * @param  int    $storyID
     * @access public
     * @return array
     */
    public function getStoryCases($storyID)
    {
        return $this->dao->select('id, project, title, pri, type, status, lastRunner, lastRunDate, lastRunResult')
            ->from(TABLE_CASE)
            ->where('story')->eq((int)$storyID)
            ->andWhere('deleted')->eq(0)
            ->fetchAll('id');
    }

    /**
     * Get counts of some stories' cases.
     *
     * @param  array  $stories
     * @access public
     * @return int
     */
    public function getStoryCaseCounts($stories)
    {
        $caseCounts = $this->dao->select('story, COUNT(*) AS cases')
            ->from(TABLE_CASE)
            ->where('story')->in($stories)
            ->andWhere('deleted')->eq(0)
            ->groupBy('story')
            ->fetchPairs();
        foreach($stories as $storyID) if(!isset($caseCounts[$storyID])) $caseCounts[$storyID] = 0;
        return $caseCounts;
    }

    /**
     * Update a case.
     *
     * @param  int    $caseID
     * @access public
     * @return void
     */
    public function update($caseID)
    {
        $now     = helper::now();
        $oldCase = $this->getById($caseID);

        $result = $this->getStatus('update', $oldCase);
        if(!$result or !is_array($result)) return $result;

        list($stepChanged, $status) = $result;

        $version = $stepChanged ? $oldCase->version + 1 : $oldCase->version;

        $case = fixer::input('post')
            ->add('version', $version)
            ->setIF($this->post->story != false and $this->post->story != $oldCase->story, 'storyVersion', $this->loadModel('story')->getVersion($this->post->story))
            ->setIF(!$this->post->linkCase, 'linkCase', '')
            ->setDefault('lastEditedBy',   $this->app->user->account)
            ->add('lastEditedDate', $now)
            ->setDefault('story,branch', 0)
            ->join('stage', ',')
            ->join('linkCase', ',')
            ->setForce('status', $status)
            ->cleanInt('story,product,branch,module')
            ->remove('comment,steps,expects,files,labels,stepType')
            ->get();

        $requiredFields = $this->config->testcase->edit->requiredFields;
        if($oldCase->lib != 0)
        {
            /* Remove the require field named story when the case is a lib case.*/
            $requiredFieldsArr = explode(',', $requiredFields);
            $fieldIndex        = array_search('story', $requiredFieldsArr);
            array_splice($requiredFieldsArr, $fieldIndex, 1);
            $requiredFields    = implode(',', $requiredFieldsArr);
        }
        $this->dao->update(TABLE_CASE)->data($case)->autoCheck()->batchCheck($requiredFields, 'notempty')->where('id')->eq((int)$caseID)->exec();
        if(!$this->dao->isError())
        {
            $isLibCase    = ($oldCase->lib and empty($oldCase->product));
            $titleChanged = ($case->title != $oldCase->title);
            if($isLibCase and $titleChanged) $this->dao->update(TABLE_CASE)->set('`title`')->eq($case->title)->where('`fromCaseID`')->eq($caseID)->exec();

            $this->updateCase2Project($oldCase, $case, $caseID);

            if($stepChanged)
            {
                $parentStepID = 0;
                if($isLibCase)
                {
                    $fromcaseVersion  = $this->dao->select('fromCaseVersion')->from(TABLE_CASE)->where('fromCaseID')->eq($caseID)->fetch('fromCaseVersion');
                    $fromcaseVersion += 1;
                    $this->dao->update(TABLE_CASE)->set('`fromCaseVersion`')->eq($fromcaseVersion)->where('`fromCaseID`')->eq($caseID)->exec();
                }

                /* Ignore steps when post has no steps. */
                if($this->post->steps)
                {
                    foreach($this->post->steps as $stepID => $stepDesc)
                    {
                        if(empty($stepDesc)) continue;
                        $stepType = $this->post->stepType;
                        $step = new stdclass();
                        $step->type    = ($stepType[$stepID] == 'item' and $parentStepID == 0) ? 'step' : $stepType[$stepID];
                        $step->parent  = ($step->type == 'item') ? $parentStepID : 0;
                        $step->case    = $caseID;
                        $step->version = $version;
                        $step->desc    = htmlspecialchars($stepDesc);
                        $step->expect  = $step->type == 'group' ? '' : htmlspecialchars($this->post->expects[$stepID]);
                        $this->dao->insert(TABLE_CASESTEP)->data($step)->autoCheck()->exec();
                        if($step->type == 'group') $parentStepID = $this->dao->lastInsertID();
                        if($step->type == 'step')  $parentStepID = 0;
                    }
                }
                else
                {
                    foreach($oldCase->steps as $step)
                    {
                        unset($step->id);
                        $step->version = $version;
                        $this->dao->insert(TABLE_CASESTEP)->data($step)->autoCheck()->exec();
                    }
                }
            }


            /* Join the steps to diff. */
            if($stepChanged and $this->post->steps)
            {
                $oldCase->steps = $this->joinStep($oldCase->steps);
                $case->steps    = $this->joinStep($this->getById($caseID, $version)->steps);
            }
            else
            {
                unset($oldCase->steps);
            }
            return common::createChanges($oldCase, $case);
        }
    }

    /**
     * Review case
     *
     * @param  int    $caseID
     * @access public
     * @return bool | array
     */
    public function review($caseID)
    {
        if($this->post->result == false) die(js::alert($this->lang->testcase->mustChooseResult));

        $oldCase = $this->getById($caseID);

        $now    = helper::now();
        $status = $this->getStatus('review', $oldCase);
        $case   = fixer::input('post')
            ->remove('result,comment')
            ->setDefault('reviewedDate', substr($now, 0, 10))
            ->setDefault('lastEditedBy', $this->app->user->account)
            ->setDefault('lastEditedDate', $now)
            ->setForce('status', $status)
            ->join('reviewedBy', ',')
            ->get();

        $this->dao->update(TABLE_CASE)->data($case)->autoCheck()->where('id')->eq($caseID)->exec();

        if(dao::isError()) return false;

        return common::createChanges($oldCase, $case);
    }

    /**
     * Batch review cases.
     *
     * @param  array   $caseIDList
     * @access public
     * @return array
     */
    public function batchReview($caseIdList, $result)
    {
        $now     = helper::now();
        $actions = array();
        $this->loadModel('action');

        $oldCases = $this->getByList($caseIdList);
        foreach($caseIdList as $caseID)
        {
            $oldCase = $oldCases[$caseID];
            if($oldCase->status != 'wait') continue;

            $case = new stdClass();
            $case->reviewedBy     = $this->app->user->account;
            $case->reviewedDate   = substr($now, 0, 10);
            $case->lastEditedBy   = $this->app->user->account;
            $case->lastEditedDate = $now;
            if($result == 'pass') $case->status = 'normal';
            $this->dao->update(TABLE_CASE)->data($case)->autoCheck()->where('id')->eq($caseID)->exec();
            $actions[$caseID] = $this->action->create('case', $caseID, 'Reviewed', '', ucfirst($result));
        }

        return $actions;
    }

    /**
     * Get cases to link.
     *
     * @param  int    $caseID
     * @param  string $browseType
     * @param  int    $queryID
     * @access public
     * @return array
     */
    public function getCases2Link($caseID, $browseType = 'bySearch', $queryID)
    {
        if($browseType == 'bySearch')
        {
            $case       = $this->getById($caseID);
            $productIdList = $this->rebirth->getProductIdList($case->applicationID, $case->product);
            $cases2Link = $this->getBySearch($case->applicationID, $productIdList, $queryID, 'id', null, 0);
            foreach($cases2Link as $key => $case2Link)
            {
                if($case2Link->id == $caseID) unset($cases2Link[$key]);
                if(in_array($case2Link->id, explode(',', $case->linkCase))) unset($cases2Link[$key]);
            }
            return $cases2Link;
        }
        else
        {
            return array();
        }
    }

    /**
     * Batch update testcases.
     *
     * @access public
     * @return array
     */
    public function batchUpdate()
    {
        $cases      = array();
        $allChanges = array();
        $now        = helper::now();
        $data       = fixer::input('post')->get();
        $caseIDList = $this->post->caseIDList;

        /* Process data if the value is 'ditto'. */
        foreach($caseIDList as $caseID)
        {
            if($data->pris[$caseID]  == 'ditto') $data->pris[$caseID]  = isset($prev['pri'])  ? $prev['pri']  : 3;
            if($data->types[$caseID] == 'ditto') $data->types[$caseID] = isset($prev['type']) ? $prev['type'] : '';

            $prev['pri']    = $data->pris[$caseID];
            $prev['type']   = $data->types[$caseID];
        }

        /* Initialize cases from the post data.*/
        $extendFields = $this->getFlowExtendFields();
        foreach($caseIDList as $caseID)
        {
            $case = new stdclass();
            $case->lastEditedBy   = $this->app->user->account;
            $case->lastEditedDate = $now;
            $case->pri            = $data->pris[$caseID];
            $case->status         = $data->statuses[$caseID];
            $case->color          = $data->color[$caseID];
            $case->title          = $data->title[$caseID];
            $case->precondition   = $data->precondition[$caseID];
            $case->keywords       = $data->keywords[$caseID];
            $case->type           = $data->types[$caseID];
            $case->stage          = empty($data->stages[$caseID]) ? '' : implode(',', $data->stages[$caseID]);

            /* 用例库批量编辑的时候，可以更换模块。*/
            if(isset($data->modules)) $case->module = $data->modules[$caseID];

            foreach($extendFields as $extendField)
            {
                $case->{$extendField->field} = htmlspecialchars($this->post->{$extendField->field}[$caseID]);
                $message = $this->checkFlowRule($extendField, $case->{$extendField->field});
                if($message) die(js::alert($message));
            }

            $cases[$caseID] = $case;
            unset($case);
        }

        /* Update cases. */
        foreach($cases as $caseID => $case)
        {
            $oldCase = $this->getByID($caseID);
            $case->project = $oldCase->project;
            $case->product = $oldCase->product;

            $this->dao->update(TABLE_CASE)->data($case)
                ->autoCheck()
                ->batchCheck($this->config->testcase->edit->requiredFields, 'notempty')
                ->where('id')->eq($caseID)
                ->exec();

            if(!dao::isError())
            {
                $isLibCase    = ($oldCase->lib and empty($oldCase->product));
                $titleChanged = ($case->title != $oldCase->title);
                if($isLibCase and $titleChanged) $this->dao->update(TABLE_CASE)->set('`title`')->eq($case->title)->where('`fromCaseID`')->eq($caseID)->exec();

                $this->updateCase2Project($oldCase, $case, $caseID);

                $this->executeHooks($caseID);

                unset($oldCase->steps);
                $allChanges[$caseID] = common::createChanges($oldCase, $case);
            }
            else
            {
                die(js::error('case#' . $caseID . dao::getError(true)));
            }
        }

        return $allChanges;
    }

    /**
     * Batch change the module of case.
     *
     * @param  array  $caseIDList
     * @param  int    $moduleID
     * @access public
     * @return array
     */
    public function batchChangeModule($caseIDList, $moduleID)
    {
        $now        = helper::now();
        $allChanges = array();
        $oldCases   = $this->getByList($caseIDList);
        foreach($caseIDList as $caseID)
        {
            $oldCase = $oldCases[$caseID];
            if($moduleID == $oldCase->module) continue;

            $case = new stdclass();
            $case->lastEditedBy   = $this->app->user->account;
            $case->lastEditedDate = $now;
            $case->module         = $moduleID;

            $this->dao->update(TABLE_CASE)->data($case)->autoCheck()->where('id')->eq((int)$caseID)->exec();
            if(!dao::isError()) $allChanges[$caseID] = common::createChanges($oldCase, $case);
        }

        return $allChanges;
    }

    /**
     * Batch case type change.
     *
     * @param  array   $caseIDList
     * @param  string  $result
     * @access public
     * @return array
     */
    public function batchCaseTypeChange($caseIdList, $result)
    {
        $now     = helper::now();
        $actions = array();
        $this->loadModel('action');

        foreach($caseIdList as $caseID)
        {
            $case = new stdClass();
            $case->lastEditedBy   = $this->app->user->account;
            $case->lastEditedDate = $now;
            $case->type           = $result;

            $this->dao->update(TABLE_CASE)->data($case)->autoCheck()->where('id')->eq($caseID)->exec();
            $this->action->create('case', $caseID, 'Edited', '', ucfirst($result));
        }
    }

    /**
     * Join steps to a string, thus can diff them.
     *
     * @param  array   $steps
     * @access public
     * @return string
     */
    public function joinStep($steps)
    {
        $return = '';
        if(empty($steps)) return $return;
        foreach($steps as $step) $return .= $step->desc . ' EXPECT:' . $step->expect . "\n";
        return $return;
    }

    /**
     * Create case steps from a bug's step.
     *
     * @param  string    $steps
     * @access public
     * @return array
     */
    function createStepsFromBug($steps)
    {
        $steps        = strip_tags($steps);
        $caseSteps    = array((object)array('desc' => $steps, 'expect' => ''));   // the default steps before parse.
        $lblStep      = strip_tags($this->lang->bug->tplStep);
        $lblResult    = strip_tags($this->lang->bug->tplResult);
        $lblExpect    = strip_tags($this->lang->bug->tplExpect);
        $lblStepPos   = strpos($steps, $lblStep);
        $lblResultPos = strpos($steps, $lblResult);
        $lblExpectPos = strpos($steps, $lblExpect);

        if($lblStepPos === false or $lblResultPos === false or $lblExpectPos === false) return $caseSteps;

        $caseSteps  = substr($steps, $lblStepPos + strlen($lblStep), $lblResultPos - strlen($lblStep) - $lblStepPos);
        $caseExpect = substr($steps, $lblExpectPos + strlen($lblExpect));
        $caseSteps  = trim($caseSteps);
        $caseExpect = trim($caseExpect);

        $caseSteps = explode("\n", trim($caseSteps));
        $stepCount = count($caseSteps);
        foreach($caseSteps as $key => $caseStep)
        {
            $expect = $key + 1 == $stepCount ? $caseExpect : '';
            $caseSteps[$key] = (object)array('desc' => trim($caseStep), 'expect' => $expect, 'type' => 'item');
        }
        return $caseSteps;
    }

    /**
     * Adjust the action is clickable.
     *
     * @param  object $case
     * @param  string $action
     * @access public
     * @return void
     */
    public static function isClickable($case, $action)
    {
        $action = strtolower($action);

        if($action == 'createbug') return $case->caseFails > 0;
        if($action == 'review') return isset($case->caseStatus) ? $case->caseStatus == 'wait' : $case->status == 'wait';

        return true;
    }

    /**
     * Get fields for import.
     *
     * @access public
     * @return array
     */
    public function getImportFields()
    {
        $caseLang   = $this->lang->testcase;
        $caseConfig = $this->config->testcase;
        $fields     = explode(',', $caseConfig->exportFields);
        foreach($fields as $key => $fieldName)
        {
            $fieldName = trim($fieldName);
            $fields[$fieldName] = isset($caseLang->$fieldName) ? $caseLang->$fieldName : $fieldName;
            unset($fields[$key]);
        }

        return $fields;
    }

    /**
     * Import case from Lib.
     *
     * @param  int    $productID
     * @access public
     * @return void
     */
    public function importFromLib($applicationID, $productID, $projectID = 0)
    {
        $data = fixer::input('post')->get();

        $prevModule = 0;
        foreach($data->module as $i => $module)
        {
            if($module != 'ditto') $prevModule = $module;
            if($module == 'ditto') $data->module[$i] = $prevModule;
        }

        $libCases = $this->dao->select('*')->from(TABLE_CASE)->where('deleted')->eq(0)->andWhere('id')->in($data->caseIdList)->fetchAll('id');
        $libSteps = $this->dao->select('*')->from(TABLE_CASESTEP)->where('`case`')->in($data->caseIdList)->orderBy('id')->fetchGroup('case');
        $libFiles = $this->dao->select('*')->from(TABLE_FILE)->where('objectID')->in($data->caseIdList)->andWhere('objectType')->eq('testcase')->fetchGroup('objectID', 'id');
        foreach($libCases as $libCaseID => $case)
        {
            $case->fromCaseID      = $case->id;
            $case->fromCaseVersion = $case->version;
            $case->product         = $productID;
            $case->project         = $projectID;
            $case->applicationID   = $applicationID;
            if(isset($data->module[$case->id])) $case->module = $data->module[$case->id];
            if(isset($data->branch[$case->id])) $case->branch = $data->branch[$case->id];
            unset($case->id);

            $this->dao->insert(TABLE_CASE)->data($case)->autoCheck()->exec();

            if(!dao::isError())
            {
                $caseID = $this->dao->lastInsertID();
                if(isset($libSteps[$libCaseID]))
                {
                    foreach($libSteps[$libCaseID] as $step)
                    {
                        $step->case = $caseID;
                        unset($step->id);
                        $this->dao->insert(TABLE_CASESTEP)->data($step)->exec();
                    }
                }

                /* If under the project module, the cases is imported need linking to the project. */
                if($this->app->openApp == 'project')
                {
                    $lastOrder = (int)$this->dao->select('*')->from(TABLE_PROJECTCASE)->where('project')->eq($this->session->project)->orderBy('order_desc')->limit(1)->fetch('order');

                    $this->dao->insert(TABLE_PROJECTCASE)
                        ->set('project')->eq($this->session->project)
                        ->set('product')->eq($case->product)
                        ->set('case')->eq($caseID)
                        ->set('version')->eq($case->version)
                        ->set('order')->eq(++ $lastOrder)
                        ->exec();
                }

                /* Fix bug #1518. */
                $oldFiles = zget($libFiles, $libCaseID, array());
                foreach($oldFiles as $fileID => $file)
                {
                    $file->objectID  = $caseID;
                    $file->addedBy   = $this->app->user->account;
                    $file->addedDate = helper::now();
                    $file->downloads = 0;
                    unset($file->id);
                    $this->dao->insert(TABLE_FILE)->data($file)->exec();
                }
                $this->loadModel('action')->create('case', $caseID, 'fromlib', '', $case->lib);
            }
        }
    }

    /**
     * Build search form.
     *
     * @param  int    $queryID
     * @param  string $actionURL
     * @param  int    $applicationID
     * @param  int    $productID
     * @param  array  $products
     * @param  array  $productsPairs
     * @access public
     * @return void
     */
    public function buildSearchForm($queryID, $actionURL, $applicationID, $productID, $productsPairs = [])
    {
        $isProjectApp = $this->app->openApp == 'project';

        $this->loadModel('rebirth');

        $products = array();
        if(!empty($productsPairs))
        {
            $products = $productsPairs;
        }
        else
        {
            if($applicationID) $products = $this->rebirth->getProductPairs($applicationID);
        }
        $this->config->testcase->search['params']['product']['values'] = array(0 => '') + $products;

        $modules = array(0 => '');
        if(is_numeric($productID)) $modules = $this->loadModel('tree')->getOptionMenu($productID, 'case', 0);

        $this->config->testcase->search['params']['module']['values'] = $modules;
        $this->config->testcase->search['params']['lib']['values']    = $this->loadModel('caselib')->getLibraries();

        if(!$this->config->testcase->needReview) unset($this->config->testcase->search['params']['status']['values']['wait']);

        $projectID = 0;
        if($isProjectApp)
        {
            unset($this->config->testcase->search['fields']['project']);
            unset($this->config->testcase->search['params']['project']);
            $projectID = $this->session->project;
        }
        else
        {
            $projects = array(0 => '');
            $projects += $this->rebirth->getProductLinkProjectPairs($applicationID, $productID);
            $this->config->testcase->search['params']['project']['values'] = array(0 => '') + $projects;
        }
        $testtasks = $this->loadModel('testtask')->getPairs(0, 0, '', '', "oddNumber", $applicationID, $projectID);
        
        $this->config->testcase->search['actionURL'] = $actionURL;
        $this->config->testcase->search['queryID']   = $queryID;

        $this->config->testcase->search['params']['linkTesttask']['values'] = $testtasks;

        $this->loadModel('search')->setSearchParams($this->config->testcase->search);
    }

    /**
     * Print cell data
     *
     * @param object $col
     * @param object $case
     * @param array  $users
     * @param array  $branches
     * @param array  $modulePairs
     * @param string $browseType
     * @param string $mode
     * @param array  $projects
     * @param array  $products
     * @access public
     * @return void
     */
    public function printCell($col, $case, $users, $branches, $modulePairs = [], $browseType = '', $mode = 'datatable', $projects = [], $products = [])
    {
        /* Check the product is closed. */
        $canBeChanged = common::canBeChanged('case', $case);

        $canBatchRun                = common::hasPriv('testtask', 'batchRun');
        $canBatchEdit               = common::hasPriv('testcase', 'batchEdit');
        $canBatchDelete             = common::hasPriv('testcase', 'batchDelete');
        $canBatchCaseTypeChange     = common::hasPriv('testcase', 'batchCaseTypeChange');
        $canBatchConfirmStoryChange = common::hasPriv('testcase', 'batchConfirmStoryChange');
        $canBatchChangeModule       = common::hasPriv('testcase', 'batchChangeModule');

        $canBatchAction = ($canBatchRun or $canBatchEdit or $canBatchDelete or $canBatchCaseTypeChange or $canBatchConfirmStoryChange or $canBatchChangeModule);

        $canView    = common::hasPriv('testcase', 'view');
        $caseLink   = helper::createLink('testcase', 'view', "caseID=$case->id&version=$case->version");
        $account    = $this->app->user->account;
        $fromCaseID = $case->fromCaseID;
        $id         = $col->id;
        if($col->show)
        {
            $class = 'c-' . $id;
            $title = '';
            if($id == 'title')
            {
                $class .= ' text-left';
                $title  = "title='{$case->title}'";
            }
            if($id == 'status')
            {
                $class .= $case->status;
                $title  = "title='" . $this->processStatus('testcase', $case) . "'";
            }
            if($id == 'actions') $class .= ' c-actions';
            if($id == 'lastRunResult') $class .= " {$case->lastRunResult}";
            if(strpos(',product,stage,precondition,keywords,story,', ",{$id},") !== false) $class .= ' text-ellipsis';

            echo "<td class='{$class}' {$title}>";
            if(isset($this->config->bizVersion)) $this->loadModel('flow')->printFlowCell('testcase', $case, $id);
            switch($id)
            {
            case 'id':
                if($canBatchAction)
                {
                    $disabled = $canBeChanged ? '' : 'disabled';
                    echo html::checkbox('caseIDList', array($case->id => ''), '', $disabled) . html::a(helper::createLink('testcase', 'view', "caseID=$case->id"), sprintf('%03d', $case->id), '', "data-app='{$this->app->openApp}'");
                }
                else
                {
                    printf('%03d', $case->id);
                }
                break;
            case 'pri':
                echo "<span class='label-pri label-pri-" . $case->pri . "' title='" . zget($this->lang->testcase->priList, $case->pri, $case->pri) . "'>";
                echo zget($this->lang->testcase->priList, $case->pri, $case->pri);
                echo "</span>";
                break;
            case 'title':
                if($case->branch) echo "<span class='label label-info label-outline'>{$branches[$case->branch]}</span> ";
                if($modulePairs and $case->module) echo "<span class='label label-gray label-badge'>{$modulePairs[$case->module]}</span> ";
                echo $canView ? ($fromCaseID ? html::a($caseLink, $case->title, null, "style='color: $case->color' data-app='{$this->app->openApp}'") . html::a(helper::createLink('testcase', 'view', "caseID=$fromCaseID"), "[<i class='icon icon-share' title='{$this->lang->testcase->fromCase}'></i>#$fromCaseID]", '', "data-app='{$this->app->openApp}'") : html::a($caseLink, $case->title, null, "style='color: $case->color' data-app='{$this->app->openApp}'")) : "<span style='color: $case->color'>$case->title</span>";
                break;
            case 'product':
                $product = $case->product;
                if(!$product) $product = 'na';
                echo zget($products, $product, '');
                break;
            case 'project':
                echo zget($projects, $case->project, '');
                break;
            case 'branch':
                echo $branches[$case->branch];
                break;
            case 'type':
                echo $this->lang->testcase->typeList[$case->type];
                break;
            case 'stage':
                $stages = '';
                foreach(explode(',', trim($case->stage, ',')) as $stage) $stages .= $this->lang->testcase->stageList[$stage] . ',';
                $stages = trim($stages, ',');
                echo "<span title='$stages'>$stages</span>";
                break;
            case 'status':
                if($case->needconfirm)
                {
                    print("<span class='status-story status-changed' title='{$this->lang->story->changed}'>{$this->lang->story->changed}</span>");
                }
                elseif(isset($case->fromCaseVersion) and $case->fromCaseVersion > $case->version and !$case->needconfirm)
                {
                    print("<span class='status-story status-changed' title='{$this->lang->testcase->changed}'>{$this->lang->testcase->changed}</span>");
                }
                else
                {
                    print("<span class='status-testcase status-{$case->status}'>" . $this->processStatus('testcase', $case) . "</span>");
                }
                break;
            case 'story':
                static $stories = array();
                if(empty($stories)) $stories = $this->dao->select('id,title')->from(TABLE_STORY)->where('deleted')->eq('0')->andWhere('product')->eq($case->product)->fetchPairs('id', 'title');
                if($case->story and isset($stories[$case->story])) echo html::a(helper::createLink('story', 'view', "storyID=$case->story"), $stories[$case->story]);
                break;
            case 'precondition':
                echo $case->precondition;
                break;
            case 'keywords':
                echo $case->keywords;
                break;
            case 'version':
                echo $case->version;
                break;
            case 'openedBy':
                echo zget($users, $case->openedBy);
                break;
            case 'openedDate':
                echo substr($case->openedDate, 5, 11);
                break;
            case 'reviewedBy':
                echo zget($users, $case->reviewedBy);
                break;
            case 'reviewedDate':
                echo substr($case->reviewedDate, 5, 11);
                break;
            case 'lastEditedBy':
                echo zget($users, $case->lastEditedBy);
                break;
            case 'lastEditedDate':
                echo substr($case->lastEditedDate, 5, 11);
                break;
            case 'lastRunner':
                echo zget($users, $case->lastRunner);
                break;
            case 'lastRunDate':
                if(!helper::isZeroDate($case->lastRunDate)) echo date(DT_MONTHTIME1, strtotime($case->lastRunDate));
                break;
            case 'lastRunResult':
                $class = 'result-' . $case->lastRunResult;
                $lastRunResultText = $case->lastRunResult ? zget($this->lang->testcase->resultList, $case->lastRunResult, $case->lastRunResult) : $this->lang->testcase->unexecuted;
                echo "<span class='$class'>" . $lastRunResultText . "</span>";
                break;
            case 'bugs':
                echo (common::hasPriv('testcase', 'bugs') and $case->bugs) ? html::a(helper::createLink('testcase', 'bugs', "runID=0&caseID={$case->id}"), $case->bugs, '', "class='iframe'") : $case->bugs;
                break;
            case 'results':
                echo (common::hasPriv('testtask', 'results') and $case->results) ? html::a(helper::createLink('testtask', 'results', "runID=0&caseID={$case->id}"), $case->results, '', "class='iframe'") : $case->results;
                break;
            case 'stepNumber':
                echo $case->stepNumber;
                break;
            case 'actions':
                if($canBeChanged)
                {
                    if($case->needconfirm or $browseType == 'needconfirm')
                    {
                        common::printIcon('testcase', 'confirmstorychange',  "caseID=$case->id", $case, 'list', 'confirm', 'hiddenwin', '', '', '', $this->lang->confirm);
                        break;
                    }

                    common::printIcon('testtask', 'results', "runID=0&caseID=$case->id", $case, 'list', '', '', 'iframe', true, "data-width='95%'");
                    common::printIcon('testtask', 'runCase', "runID=0&caseID=$case->id&version=$case->version", $case, 'list', 'play', '', 'runCase iframe', false, "data-width='95%'");
                    common::printIcon('testcase', 'edit',    "caseID=$case->id", $case, 'list');
                    if($this->config->testcase->needReview or !empty($this->config->testcase->forceReview)) common::printIcon('testcase', 'review',  "caseID=$case->id", $case, 'list', 'glasses', '', 'iframe');
                    common::printIcon('testcase', 'createBug', "applicationID=$case->applicationID&product=$case->product&branch=$case->branch&extra=caseID=$case->id,version=$case->version,runID=", $case, 'list', 'bug', '', 'iframe', '', "data-width='90%'");
                    common::printIcon('testcase', 'create',  "applicationID=$case->applicationID&productID=$case->product&branch=$case->branch&moduleID=$case->module&from=testcase&param=$case->id", $case, 'list', 'copy');
                }

                break;
            }
            echo '</td>';
        }
    }

    /**
     * Append bugs and results.
     *
     * @param  array    $cases
     * @param  string   $type
     * @access public
     * @return array
     */
    public function appendData($cases, $type = 'case')
    {
        if($type != 'case')
        {
            foreach($cases as $key => $case)
            {
                $case->bugs       = $case->taskBugs;
                $case->results    = $case->taskResults;
                $case->caseFails  = $case->taskCaseFails;
                $case->stepNumber = $case->taskStepNumber;
            }
        }

        return $cases;
    }

    /**
     * Check whether force not review.
     *
     * @access public
     * @return bool
     */
    public function forceNotReview()
    {
        if(empty($this->config->testcase->needReview))
        {
            if(!isset($this->config->testcase->forceReview)) return true;
            if(strpos(",{$this->config->testcase->forceReview},", ",{$this->app->user->account},") === false) return true;
        }
        if($this->config->testcase->needReview && isset($this->config->testcase->forceNotReview) && strpos(",{$this->config->testcase->forceNotReview},", ",{$this->app->user->account},") !== false) return true;

        return false;
    }

    public function summary($cases)
    {
        $executed = 0;
        foreach($cases as $case)
        {
            if($case->lastRunResult != '') $executed ++;
        }

        return sprintf($this->lang->testcase->summary, count($cases), $executed);
    }

    /**
     * Sync case to project.
     *
     * @param  object $case
     * @param  int    $caseID
     * @access public
     * @return void
     */
    public function syncCase2Project($case, $caseID)
    {
        /* 用例库用例跳过。*/
        if(isset($case->lib)) return false;

        $lastOrder = (int)$this->dao->select('*')->from(TABLE_PROJECTCASE)->where('project')->eq($case->project)->orderBy('order_desc')->limit(1)->fetch('order');

        $data = new stdclass();
        $data->project = $case->project;
        $data->product = $case->product;
        $data->case    = $caseID;
        $data->version = 1;
        $data->order   = ++ $lastOrder;
        $this->dao->replace(TABLE_PROJECTCASE)->data($data)->exec();
    }

    /**
     * Deal with the relationship between the case and project when edit the case.
     *
     * @param  object  $oldCase
     * @param  object  $case
     * @param  int     $caseID
     * @access public
     * @return void
     */
    public function updateCase2Project($oldCase, $case, $caseID)
    {
        $this->dao->delete()->from(TABLE_PROJECTCASE)->where('`case`')->eq($oldCase->id)->exec();

        $lastOrder = (int)$this->dao->select('*')->from(TABLE_PROJECTCASE)->where('project')->eq($case->project)->orderBy('order_desc')->limit(1)->fetch('order');

        $data = new stdclass();
        $data->project = $case->project;
        $data->product = $case->product;
        $data->case    = $caseID;
        $data->version = $case->version;
        $data->order   = ++ $lastOrder;
        $this->dao->insert(TABLE_PROJECTCASE)->data($data)->exec();
    }

    /**
     * Get status for different method.
     *
     * @param  string $methodName
     * @param  object $case
     * @access public
     * @return mixed    string | bool | array
     */
    public function getStatus($methodName, $case = null)
    {
        if($methodName == 'create')
        {
            if($this->forceNotReview() || $this->post->forceNotReview) return 'normal';
            return 'wait';
        }

        if($methodName == 'review')
        {
            $status = zget($case, 'status', $status);

            if($this->post->result == 'pass') return 'normal';

            return $status;
        }

        if($methodName == 'update')
        {
            if(!empty($_POST['lastEditedDate']) and $case->lastEditedDate != $this->post->lastEditedDate)
            {
                dao::$errors[] = $this->lang->error->editedByOther;
                return false;
            }

            $status      = $this->post->status ? $this->post->status : $case->status;
            $stepChanged = false;
            $steps       = array();

            /* ---------------- Judge steps changed or not.-------------------- */

            /* Remove the empty setps in post. */
            if($this->post->steps)
            {
                $data = fixer::input('post')->get();
                foreach($data->steps as $key => $desc)
                {
                    $desc     = trim($desc);
                    $stepType = isset($data->stepType[$key]) ? $data->stepType[$key] : 'step';
                    if(!empty($desc)) $steps[] = array('desc' => $desc, 'type' => $stepType, 'expect' => trim($data->expects[$key]));
                }

                /* If step count changed, case changed. */
                if(count($case->steps) != count($steps))
                {
                    $stepChanged = true;
                }
                else
                {
                    /* Compare every step. */
                    $i = 0;
                    foreach($case->steps as $key => $oldStep)
                    {
                        if(trim($oldStep->desc) != trim($steps[$i]['desc']) or trim($oldStep->expect) != $steps[$i]['expect'] or trim($oldStep->type) != $steps[$i]['type'])
                        {
                            $stepChanged = true;
                            break;
                        }
                        $i++;
                    }
                }
            }

            if(!$this->forceNotReview() and $stepChanged) $status = 'wait';

            return array($stepChanged, $status);
        }

        return '';
    }

    /**
     * Get xmind config.
     *
     * @access public
     * @return array
     */
    function getXmindConfig()
    {
        $configItems = $this->dao->select("`key`,value")->from(TABLE_CONFIG)
            ->where('owner')->eq($this->app->user->account)
            ->andWhere('module')->eq('testcase')
            ->andWhere('section')->eq('xmind')
            ->fetchAll();

        $config = array();
        foreach($configItems as $item) $config[$item -> key] = $item -> value;

        if(!isset($config['module'])) $config['module'] = 'M';
        if(!isset($config['case']))   $config['case']   = 'C';
        if(!isset($config['pri']))    $config['pri']    = 'P';
        if(!isset($config['group']))  $config['group']  = 'G';

        return $config;
    }

    /**
     * Save xmind config.
     *
     * @access public
     * @return array
     */
    function saveXmindConfig()
    {
        $configList = array();

        $module = $this->post->module;
        if(isset($module) && !empty($module))
        {
            if(!$this->checkConfigValue($module)) return array('result' => 'fail', 'message' => '模块特征字符串只能是1-10个字母');
            $configList[] = array('key'=>'module','value'=>$module);
        }

        $case = $this->post->case;
        if(isset($case) && !empty($case))
        {
            if(!$this->checkConfigValue($case)) return array('result' => 'fail', 'message' => '测试用例特征字符串只能是1-10个字母');
            $configList[] = array('key'=>'case','value'=>$case);
        }

        $pri = $this->post->pri;
        if(isset($pri) && !empty($pri))
        {
            if(!$this->checkConfigValue($pri)) return array('result' => 'fail', 'message' => '优先级特征字符串只能是1-10个字母');
            $configList[] = array('key'=>'pri','value'=>$pri);
        }

        $group = $this->post->group;
        if(isset($group) && !empty($group))
        {
            if(!$this->checkConfigValue($group)) return array('result' => 'fail', 'message' => '步骤分组特征字符串只能是1-10个字母');
            $configList[] = array('key'=>'group','value'=>$group);
        }

        $map = array();
        $map[strtolower($module)] = true;
        $map[strtolower($case)]   = true;
        $map[strtolower($pri)]    = true;
        $map[strtolower($group)]  = true;

        if(count($map) < 4) return array('result' => 'fail', 'message' => '特征字符串不能重复');

        $this->dao->begin();

        $this->dao->delete()->from(TABLE_CONFIG)
            ->where('owner')->eq($this->app->user->account)
            ->andWhere('module')->eq('testcase')
            ->andWhere('section')->eq('xmind')
            ->exec();

        foreach($configList as $one)
        {
            $config = new stdclass();

            $config->module  = 'testcase';
            $config->section = 'xmind';
            $config->key     = $one['key'];
            $config->value   = $one['value'];
            $config->owner   = $this->app->user->account;

            $this->dao->insert(TABLE_CONFIG)->data($config)->autoCheck()->exec();

            if($this->dao->isError())
            {
                $this->dao->rollBack();
                return array('result' => 'fail', 'message' => $this->dao->getError(true));
            }
        }

        $this->dao->commit();

        return array("result" => "success", "message" => 1);
    }

    /**
     * Check config.
     *
     * @param  string $str
     * @access public
     * @return bool
     */
    function checkConfigValue($str)
    {
        return preg_match("/^[a-zA-Z]{1,10}$/",$str);
    }

    /**
     * Get export data.
     *
     * @param  int $productID
     * @param  int $moduleID
     * @param  int $branch
     * @param  int $projectID
     * @access public
     * @return array
     */
    public function getXmindExport($productID, $moduleID, $branch, $projectID)
    {
        $caseList   = $this->getCaseByProductAndModule($productID, $moduleID, $projectID);
        $stepList   = $this->getStepByProductAndModule($productID, $moduleID, $caseList);
        $moduleList = $this->getModuleByProductAndModel($productID, $moduleID, $branch);

        $config = $this->getXmindConfig();

        return array(
                'caseList'  =>$caseList,
                'stepList'  =>$stepList,
                'moduleList'=>$moduleList,
                'config'    =>$config
            );
    }

    /**
     * Get module by product.
     *
     * @param  int $productID
     * @param  int $moduleID
     * @param  int $branch
     * @access public
     * @return array
     */
    function getModuleByProductAndModel($productID, $moduleID, $branch)
    {
        $moduleList = array();

        if($moduleID > 0)
        {
            $module = $this->loadModel('tree')->getByID($moduleID);

            $moduleList[$module->id] = $module->name;
        }
        else
        {
            $moduleList = $this->loadModel('tree')->getOptionMenu($productID, $viewType = 'case', $startModuleID = 0, ($branch === 'all' or !isset($branches[$branch])) ? 0 : $branch);

            unset($moduleList['0']);
        }

        return $moduleList;
    }

    /**
     * Get case by product and module.
     *
     * @param  int $productID
     * @param  int $moduleID
     * @param  int $projectID
     * @access public
     * @return array
     */
    function getCaseByProductAndModule($productID, $moduleID, $projectID)
    {
        $fields = "t2.id as productID,"
            . "t2.`name` as productName,"
            . "t3.id as moduleID,"
            . "t3.`name` as moduleName,"
            . "t1.id as testcaseID,"
            . "t1.title as `name`,"
            . "t1.pri";

        $caseList = $this->dao->select($fields)->from(TABLE_CASE)->alias('t1')
            ->leftJoin(TABLE_PRODUCT)->alias('t2')->on('t1.product = t2.id')
            ->leftJoin(TABLE_MODULE)->alias('t3')->on('t1.module = t3.id')
            ->where('t1.deleted')->eq(0)
            ->andWhere('t1.product')->eq($productID)
            ->beginIF($projectID > 0)->andWhere('t1.project')->eq($projectID)->fi()
            ->beginIF($moduleID > 0)->andWhere('t1.module')->eq($moduleID)->fi()
            ->fetchAll('testcaseID');

        return $caseList;
    }

    /**
     * Get step by product and module.
     *
     * @param  int   $productID
     * @param  int   $moduleID
     * @param  array $moduleID
     * @access public
     * @return array
     */
    function getStepByProductAndModule($productID, $moduleID, $caseList = array())
    {
        $fields = "cs.`case` as testcaseID,"
            . "cs.id as stepID,"
            . "cs.type,"
            . "cs.parent as parentID,"
            . "cs.`desc`,"
            . "cs.expect";

        $caseIdList = array_keys($caseList);

        $stepList = $this->dao->select($fields)->from(TABLE_CASESTEP)->alias('cs')
            ->where('cs.`case`')->in($caseIdList)
            ->andWhere('cs.version = (SELECT MAX(version) FROM zt_casestep sub WHERE sub.`case` = cs.`case`)')
            ->fetchAll();

        return $stepList;
    }

    /**
     * Save xmind file content to database.
     *
     * @access public
     * @return array
     */
    public function saveXmindImport($applicationID)
    {
        $this->dao->begin();

        $sceneIds     = array();
        $testcaseList = $this->post->testcaseList;
        foreach($testcaseList as $testcase)
        {
            $tmpId  = $testcase['tmpId'];
            $tmpPId = $testcase['tmpPId'];

            $result = $this->saveTestcase($testcase, $sceneIds, $applicationID);
            if($result['result'] == 'fail')
            {
                $this->dao->rollBack();
                return $result;
            }

            $sceneIds[$tmpId] = array('id' => $result['testcaseID'], 'tmpPId' => $tmpPId);
        }

        $this->dao->commit();

        return array('result' => 'success','message' => 1);
    }

    /**
     * Save test case.
     *
     * @param  array $testcaseData
     * @param  array $sceneIds
     * @access public
     * @return array
     */
    public function saveTestcase($testcaseData, $sceneIds, $applicationID)
    {
        $tmpPId = $testcaseData['tmpPId'];

        $id         = isset($testcaseData['id']) ? $testcaseData['id'] : -1;
        $module     = $testcaseData['module'];
        $product    = $testcaseData['product'];
        $branch     = $testcaseData['branch'];
        $title      = $testcaseData['name'];
        $pri        = $testcaseData['pri'];
        $now        = helper::now();
        $testcaseID = -1;
        $version    = 1;
        $projectID  = $this->session->project ? $this->session->project : 0;

        if(!isset($testcaseData['id']))
        {
            $testcase                = new stdclass();
            $testcase->module        = $module;
            $testcase->product       = $product;
            $testcase->applicationID = $applicationID;
            $testcase->project       = $projectID;
            $testcase->branch        = $branch;
            $testcase->title         = $title;
            $testcase->pri           = $pri;
            $testcase->type          = 'feature';
            $testcase->status        = 'normal';
            $testcase->version       = $version;
            $testcase->openedBy      = $this->app->user->account;
            $testcase->openedDate    = $now;

            $this->dao->insert(TABLE_CASE)->data($testcase)->autoCheck()->exec();
            $testcaseID = $this->dao->lastInsertID();
        }
        else
        {
            $oldCase = $this->dao->select('version,id')->from(TABLE_CASE)->where('id')->eq((int)$id)->fetch();

            if(isset($oldCase->id))
            {
                if(!isset($oldCase->version)) return array('result' => 'fail', 'message' => 'not exist testcase');

                $version  = $oldCase->version + 1;

                $testcase                 = new stdclass();
                $testcase->id             = $id;
                $testcase->module         = $module;
                $testcase->product        = $product;
                $testcase->applicationID  = $applicationID;
                $testcase->project        = $projectID;
                $testcase->branch         = $branch;
                $testcase->title          = $title;
                $testcase->pri            = $pri;
                $testcase->version        = $version;
                $testcase->lastEditedBy   = $this->app->user->account;
                $testcase->lastEditedDate = $now;

                $testcaseID = $id;
                $this->dao->update(TABLE_CASE)->data($testcase)->where('id')->eq((int)$id)->exec();
            }
            else
            {
                $testcase                = new stdclass();
                $testcase->module        = $module;
                $testcase->product       = $product;
                $testcase->applicationID = $applicationID;
                $testcase->project       = $projectID;
                $testcase->branch        = $branch;
                $testcase->title         = $title;
                $testcase->pri           = $pri;
                $testcase->type          = 'feature';
                $testcase->status        = 'normal';
                $testcase->version       = $version;
                $testcase->openedBy      = $this->app->user->account;
                $testcase->openedDate    = $now;

                $this->dao->insert(TABLE_CASE)->data($testcase)->autoCheck()->exec();

                $testcaseID = $this->dao->lastInsertID();
            }
        }

        if(dao::isError()) return array('result' => 'fail', 'message' => $this->dao->getError(true));

        $stepList = isset($testcaseData['stepList']) ? $testcaseData['stepList'] : array();
        if(isset($stepList))
        {
            foreach($stepList as $step)
            {
                $tmpPId = $step['tmpPId'];
                $pObj   = isset($sceneIds[$tmpPId]) ? $sceneIds[$tmpPId] : array();

                $parent = 0;
                if(isset($sceneIds[$tmpPId])) $parent = $pObj['id'];

                $case   = $testcaseID;
                $type   = $step['type'];
                $desc   = $step['desc'];
                $expect = isset($step['expect']) ? $step['expect'] : '';

                $casestep            = new stdclass();
                $casestep->case      = $case;
                $casestep->version   = $version;
                $casestep->type      = $type;
                $casestep->parent    = $parent;
                $casestep->desc      = $desc;
                $casestep->expect    = $expect;

                $this->dao->insert(TABLE_CASESTEP)->data($casestep)->autoCheck()->exec();
                $casestepID = $this->dao->lastInsertID();

                if(dao::isError()) return array('result' => 'fail', 'message' => $this->dao->getError(true));

                $sceneIds[$step['tmpId']] = array('id' => $casestepID, 'tmpPId' => $tmpPId);
            }
        }

        return array('result' => 'success', 'message' => 1, 'testcaseID' => $testcaseID);
    }
}
