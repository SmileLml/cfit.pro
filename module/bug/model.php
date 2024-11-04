<?php
/**
 * The model file of bug module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     bug
 * @version     $Id: model.php 5079 2013-07-10 00:44:34Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php
class bugModel extends model
{
    /**
     * Set menu.
     *
     * @param  array  $products
     * @param  int    $productID
     * @param  int    $branch
     * @param  int    $moduleID
     * @param  string $browseType
     * @param  string $orderBy
     * @access public
     * @return void
     */
    public function setMenu($products, $productID, $branch = 0, $moduleID = 0, $browseType = 'unclosed', $orderBy = '')
    {
        $this->loadModel('product')->setMenu($productID, $branch, $moduleID, 'bug');
        if($this->lang->navGroup->testcase == 'project' and $this->app->methodName == 'browse') $products = array(0 => $this->lang->bug->allProduct) + $products;
        $selectHtml = $this->product->select($products, $productID, 'bug', 'browse', '', $branch, $moduleID, 'bug');

        $pageNav     = '';
        $pageActions = '';
        $isMobile    = $this->app->viewType == 'mhtml';
        if($isMobile)
        {
            $this->app->loadLang('qa');
            $pageNav = html::a(helper::createLink('qa', 'index'), $this->lang->qa->index) . $this->lang->colon;
        }
        $pageNav .= $selectHtml;

        $this->lang->modulePageNav = $pageNav;
        $this->lang->TRActions     = $pageActions;
    }

    /**
     * Create a bug.
     *
     * @param  string $from   object that is transfered to bug.
     * @access public
     * @return array|bool
     */
    public function create($from = '')
    {
        $now = helper::now();
        $bug = fixer::input('post')
            ->setDefault('openedBy', $this->app->user->account)
            ->setDefault('openedDate', $now)
            ->setDefault('product,project,execution,story,task', 0)
            ->setDefault('openedBuild', '')
            ->setDefault('deadline', '0000-00-00')
            ->setIF($this->config->systemMode == 'new' && $this->lang->navGroup->bug != 'qa', 'project', $this->session->project)
            ->setIF(strpos($this->config->bug->create->requiredFields, 'deadline') !== false, 'deadline', $this->post->deadline)
            ->setIF($this->post->assignedTo != '', 'assignedDate', $now)
            ->setIF($this->post->story != false, 'storyVersion', $this->loadModel('story')->getVersion($this->post->story))
            ->setIF(strpos($this->config->bug->create->requiredFields, 'execution') !== false, 'execution', $this->post->execution)
            ->stripTags($this->config->bug->editor->create['id'], $this->config->allowedTags)
            ->cleanInt('execution,module,severity')
            ->cleanINT('product')
            ->cleanINT('project')
            ->join('openedBuild', ',')
            ->join('mailto', ',')
            ->join('linkTesttask', ',')
            ->remove('files, labels,uid,oldTaskID,contactListMenu')
            ->get();

        /* Check repeat bug. */
        $result = $this->loadModel('common')->removeDuplicate('bug', $bug, "product={$bug->product}");
        if($result['stop']) return array('status' => 'exists', 'id' => $result['duplicate']);

        $bug = $this->loadModel('file')->processImgURL($bug, $this->config->bug->editor->create['id'], $this->post->uid);

        $this->dao->insert(TABLE_BUG)->data($bug)->autoCheck()->batchCheck($this->config->bug->create->requiredFields, 'notempty')->exec();
        if(!dao::isError())
        {
            $bugID = $this->dao->lastInsertID();
            $this->file->updateObjectID($this->post->uid, $bugID, 'bug');
            $this->file->saveUpload('bug', $bugID);
            empty($bug->case) ? $this->loadModel('score')->create('bug', 'create', $bugID) : $this->loadModel('score')->create('bug', 'createFormCase', $bug->case);

            /* Callback the callable method to process the related data for object that is transfered to bug. */
            if($from && is_callable(array($this, $this->config->bug->fromObjects[$from]['callback']))) call_user_func(array($this, $this->config->bug->fromObjects[$from]['callback']), $bugID);

            return array('status' => 'created', 'id' => $bugID);
        }
        return false;
    }

    /**
     * Batch create
     *
     * @param  int    $applicationID
     * @param  int    $productID
     * @param  int    $branch
     * @access public
     * @return void
     */
    public function batchCreate($applicationID, $productID, $branch = 0)
    {
        /* Load module and init vars. */
        $this->loadModel('action');
        $branch    = (int)$branch;
        $productID = (int)$productID;
        $now       = helper::now();
        $actions   = array();
        $data      = fixer::input('post')->get();

        $result = $this->loadModel('common')->removeDuplicate('bug', $data, "product={$productID}");
        $data   = $result['data'];

        /* Get pairs(moduleID => moduleOwner) for bug. */
        $stmt         = $this->dbh->query($this->loadModel('tree')->buildMenuQuery($productID, 'bug', $startModuleID = 0, $branch));
        $moduleOwners = array();
        while($module = $stmt->fetch()) $moduleOwners[$module->id] = $module->owner;

        $project   = 0;
        $execution = 0;
        $module    = 0;
        $pri       = 0;
        $severitie = 0;
        $os        = '';
        $browser   = '';
        foreach($data->title as $i => $title)
        {
            if($data->projects[$i] != 'ditto')   $project   = $data->projects[$i];
            if($data->executions[$i] != 'ditto') $execution = $data->executions[$i];
            if($data->modules[$i]  != 'ditto')   $module    = $data->modules[$i];
            if($data->pris[$i]     != 'ditto')   $pri       = $data->pris[$i];
            if($data->severities[$i] != 'ditto') $severitie = $data->severities[$i];
            if($data->oses[$i]     != 'ditto')   $os        = $data->oses[$i];
            if($data->browsers[$i] != 'ditto')   $browser   = $data->browsers[$i];

            $data->projects[$i]   = (int)$project;
            $data->executions[$i] = (int)$execution;
            $data->modules[$i]    = (int)$module;
            $data->pris[$i]       = $pri;
            $data->severities[$i]  = $severitie;
            $data->oses[$i]       = $os;
            $data->browsers[$i]   = $browser;
        }

        /* Get bug data. */
        if(isset($data->uploadImage)) $this->loadModel('file');
        $extendFields = $this->getFlowExtendFields();
        $bugs = array();
        foreach($data->title as $i => $title)
        {
            if(empty($title)) continue;

            $bug = new stdClass();
            $bug->openedBy    = $this->app->user->account;
            $bug->openedDate  = $now;
            $bug->product     = (int)$productID;
            $bug->project     = (int)$data->projects[$i];
            $bug->execution   = (int)$data->executions[$i];
            $bug->module      = (int)$data->modules[$i];

            if(empty($data->openedBuilds[$i]))
            {
                $openedBuild = '';
            }
            else
            {
                $openedBuild = implode(',', $data->openedBuilds[$i]);
            }
            $bug->openedBuild = $openedBuild;

            if(empty($data->branches[$i]))
            {
                $branch = 0;
            }
            else
            {
                $branch = (int)$data->branches[$i];
            }
            $bug->branch = $branch;

            $bug->title       = $data->title[$i];
            $bug->color       = $data->color[$i];
            $bug->steps       = nl2br($data->stepses[$i]);
            $bug->type        = $data->types[$i];
            $bug->childType   = $data->childTypes[$i];
            $bug->deadline    = $data->deadlines[$i];
            $bug->pri         = $data->pris[$i];
            $bug->severity    = $data->severities[$i];
            $bug->os          = $data->oses[$i];
            $bug->browser     = $data->browsers[$i];
            $bug->keywords    = $data->keywords[$i];

            /* Assign the bug to the person in charge of the module. */
            if(!empty($moduleOwners[$bug->module]))
            {
                $bug->assignedTo   = $moduleOwners[$bug->module];
                $bug->assignedDate = $now;
            }

            foreach($extendFields as $extendField)
            {
                $bug->{$extendField->field} = htmlspecialchars($this->post->{$extendField->field}[$i]);
                $message = $this->checkFlowRule($extendField, $bug->{$extendField->field});
                if($message) die(js::alert($message));
            }

            /* Required field check. */
            foreach(explode(',', $this->config->bug->create->requiredFields) as $field)
            {
                $field = trim($field);
                if($field and empty($bug->$field)) die(js::alert(sprintf($this->lang->error->notempty, $this->lang->bug->$field)));
            }

            $bugs[$i] = $bug;
        }

        /* When the bug is created by uploading an image, add the image to the step of the bug. */
        foreach($bugs as $i => $bug)
        {
            if(!empty($data->uploadImage[$i]))
            {
                $fileName = $data->uploadImage[$i];
                $file     = $this->session->bugImagesFile[$fileName];

                $realPath = $file['realpath'];
                unset($file['realpath']);
                if(rename($realPath, $this->file->savePath . $this->file->getSaveName($file['pathname'])))
                {
                    if(in_array($file['extension'], $this->config->file->imageExtensions))
                    {
                        $file['addedBy']    = $this->app->user->account;
                        $file['addedDate']  = $now;
                        $this->dao->insert(TABLE_FILE)->data($file)->exec();

                        $fileID = $this->dao->lastInsertID();
                        $bug->steps .= '<img src="{' . $fileID . '.' . $file['extension'] . '}" alt="" />';
                    }
                }
                else
                {
                    unset($file);
                }
            }

            $bug->applicationID = $applicationID;
            $this->dao->insert(TABLE_BUG)->data($bug)
                ->autoCheck()
                ->batchCheck($this->config->bug->create->requiredFields, 'notempty')
                ->exec();
            if(dao::isError()) die(js::error(dao::getError()));

            $bugID = $this->dao->lastInsertID();

            $this->executeHooks($bugID);

            /* When the bug is created by uploading the image, add the image to the file of the bug. */
            $this->loadModel('score')->create('bug', 'create', $bugID);
            if(!empty($data->uploadImage[$i]) and !empty($file))
            {
                $file['objectType'] = 'bug';
                $file['objectID']   = $bugID;
                $file['addedBy']    = $this->app->user->account;
                $file['addedDate']  = $now;
                $this->dao->insert(TABLE_FILE)->data($file)->exec();
                unset($file);
            }

            if(dao::isError()) die(js::error('bug#' . ($i+1) . dao::getError(true)));
            $actions[$bugID] = $this->action->create('bug', $bugID, 'Opened');
        }

        /* Remove upload image file and session. */
        if(!empty($data->uploadImage) and $this->session->bugImagesFile)
        {
            $classFile = $this->app->loadClass('zfile');
            $file = current($_SESSION['bugImagesFile']);
            $realPath = dirname($file['realpath']);
            if(is_dir($realPath)) $classFile->removeDir($realPath);
            unset($_SESSION['bugImagesFile']);
        }
        if(!dao::isError()) $this->loadModel('score')->create('ajax', 'batchCreate');
        return $actions;
    }

    /**
     * Get bugs.
     *
     * @param  array  $productIDList
     * @param  int    $branch
     * @param  string $browseType
     * @param  int    $moduleID
     * @param  int    $queryID
     * @param  string $sort
     * @param  object $pager
     * @param  int    $projectID
     * @access public
     * @return array
     */
    public function getBugs($productIDList, $branch, $browseType, $moduleID, $queryID, $sort, $pager, $projectID, $applicationID)
    {
        /* Set modules and browse type. */
        $modules    = $moduleID ? $this->loadModel('tree')->getAllChildId($moduleID) : '0';
        $browseType = ($browseType == 'bymodule' and $this->session->bugBrowseType and $this->session->bugBrowseType != 'bysearch') ? $this->session->bugBrowseType : $browseType;
        $browseType = $browseType == 'bybranch' ? 'bymodule' : $browseType;
        /* Get bugs by browse type. */
        $bugs = array();
        if($browseType == 'all')               $bugs = $this->getAllBugs($productIDList, $branch, $modules, $sort, $pager, $projectID, $applicationID);
        elseif($browseType == 'bymodule')      $bugs = $this->getModuleBugs($productIDList, $branch, $modules, $sort, $pager, $projectID, $applicationID);
        elseif($browseType == 'assigntome')    $bugs = $this->getByAssigntome($productIDList, $branch, $modules, $sort, $pager, $projectID, $applicationID);
        elseif($browseType == 'openedbyme')    $bugs = $this->getByOpenedbyme($productIDList, $branch, $modules, $sort, $pager, $projectID, $applicationID);
        elseif($browseType == 'resolvedbyme')  $bugs = $this->getByResolvedbyme($productIDList, $branch, $modules, $sort, $pager, $projectID, $applicationID);
        elseif($browseType == 'assigntonull')  $bugs = $this->getByAssigntonull($productIDList, $branch, $modules, $sort, $pager, $projectID, $applicationID);
        elseif($browseType == 'unconfirmed')   $bugs = $this->getUnconfirmed($productIDList, $branch, $modules, $sort, $pager, $projectID, $applicationID);
        elseif($browseType == 'unresolved')    $bugs = $this->getByStatus($productIDList, $branch, $modules, 'unresolved', $sort, $pager, $projectID, $applicationID);
        elseif($browseType == 'unclosed')      $bugs = $this->getByStatus($productIDList, $branch, $modules, 'unclosed', $sort, $pager, $projectID, $applicationID);
        elseif($browseType == 'toclosed')      $bugs = $this->getByStatus($productIDList, $branch, $modules, 'toclosed', $sort, $pager, $projectID, $applicationID);
        elseif($browseType == 'longlifebugs')  $bugs = $this->getByLonglifebugs($productIDList, $branch, $modules, $sort, $pager, $projectID, $applicationID);
        elseif($browseType == 'postponedbugs') $bugs = $this->getByPostponedbugs($productIDList, $branch, $modules, $sort, $pager, $projectID, $applicationID);
        elseif($browseType == 'needconfirm')   $bugs = $this->getByNeedconfirm($productIDList, $branch, $modules, $sort, $pager, $projectID, $applicationID);
        elseif($browseType == 'bysearch')      $bugs = $this->getBySearchAboutDefect($productIDList, $branch, $queryID, $sort, '', $pager, $projectID, $applicationID);
        elseif($browseType == 'overduebugs')   $bugs = $this->getOverdueBugs($productIDList, $branch, $modules, $sort, $pager, $projectID, $applicationID);

        return $this->checkDelayBugs($bugs);
    }

    /**
     * Get bugs in testtask.
     *
     * @param  int    $branch
     * @param  string $browseType
     * @param  int    $moduleID
     * @param  int    $queryID
     * @param  string $sort
     * @param  object $pager
     * @param  int    $taskID
     * @return void
     */
    public function getTesttaskBugs($branch, $browseType, $moduleID, $queryID, $sort, $pager, $taskID, $applicationID = 0)
    {
        /* Set modules and browse type. */
        $modules    = $moduleID ? $this->loadModel('tree')->getAllChildId($moduleID) : '0';
        $browseType = ($browseType == 'bymodule' and $this->session->bugBrowseType and $this->session->bugBrowseType != 'bysearch') ? $this->session->bugBrowseType : $browseType;
        $browseType = $browseType == 'bybranch' ? 'bymodule' : $browseType;

        /* Get bugs by browse type. */
        $bugs = array();
        if($browseType == 'all')               $bugs = $this->getAllBugsInTesttask($taskID, $branch, $modules, $sort, $pager);
        elseif($browseType == 'bymodule')      $bugs = $this->getModuleBugsInTesttask($taskID, $branch, $modules, $sort, $pager);
        elseif($browseType == 'assigntome')    $bugs = $this->getByAssigntomeInTesttask($taskID, $branch, $modules, $sort, $pager);
        elseif($browseType == 'openedbyme')    $bugs = $this->getByOpenedbymeInTesttask($taskID, $branch, $modules, $sort, $pager);
        elseif($browseType == 'resolvedbyme')  $bugs = $this->getByResolvedbymeInTesttask($taskID, $branch, $modules, $sort, $pager);
        elseif($browseType == 'assigntonull')  $bugs = $this->getByAssigntonullInTesttask($taskID, $branch, $modules, $sort, $pager);
        elseif($browseType == 'unconfirmed')   $bugs = $this->getUnconfirmedInTesttask($taskID, $branch, $modules, $sort, $pager);
        elseif($browseType == 'unresolved')    $bugs = $this->getByStatusInTesttask($taskID, $branch, $modules, 'unresolved', $sort, $pager);
        elseif($browseType == 'unclosed')      $bugs = $this->getByStatusInTesttask($taskID, $branch, $modules, 'unclosed', $sort, $pager);
        elseif($browseType == 'toclosed')      $bugs = $this->getByStatusInTesttask($taskID, $branch, $modules, 'toclosed', $sort, $pager);
        elseif($browseType == 'longlifebugs')  $bugs = $this->getByLonglifebugsInTesttask($taskID, $branch, $modules, $sort, $pager);
        elseif($browseType == 'postponedbugs') $bugs = $this->getByPostponedbugsInTesttask($taskID, $branch, $modules, $sort, $pager);
        elseif($browseType == 'needconfirm')   $bugs = $this->getByNeedconfirmInTesttask($taskID, $branch, $modules, $sort, $pager);
        elseif($browseType == 'bysearch')      $bugs = $this->getBySearchAboutDefectInTesttask($taskID, $branch, $queryID, $sort, '', $pager, $applicationID);
        elseif($browseType == 'overduebugs')   $bugs = $this->getOverdueBugsInTesttask($taskID, $branch, $modules, $sort, $pager);

        return $this->checkDelayBugs($bugs);
    }

    /**
     * Check delay bugs.
     *
     * @param  array  $bugs
     * @access public
     * @return array
     */
    public function checkDelayBugs($bugs)
    {
        foreach ($bugs as $bug) $bug = $this->checkDelayBug($bug);

        return $bugs;
    }

    /**
     * Check delay bug.
     *
     * @param  array  $bug
     * @access public
     * @return array
     */
    public function checkDelayBug($bug)
    {
        // Delayed or not?.
        if(!helper::isZeroDate($bug->deadline))
        {
            if($bug->resolvedDate and !helper::isZeroDate($bug->resolvedDate))
            {
                $delay = helper::diffDate(substr($bug->resolvedDate, 0, 10), $bug->deadline);
            }
            elseif($bug->status == 'active')
            {
                $delay = helper::diffDate(helper::today(), $bug->deadline);
            }

            if(isset($delay) and $delay > 0) $bug->delay = $delay;
        }

        return $bug;
    }

    /**
     * Get bugs of a module.
     *
     * @param  int|array       $productIDList
     * @param  int             $branch
     * @param  string|array    $moduleIdList
     * @param  string          $orderBy
     * @param  object          $pager
     * @param  int             $projectID
     * @access public
     * @return array
     */
    public function getModuleBugs($productIDList, $branch = 0, $moduleIdList = 0, $orderBy = 'id_desc', $pager = null, $projectID, $applicationID)
    {
        return $this->dao->select('*')->from(TABLE_BUG)
            ->where('product')->in($productIDList)
            ->beginIF(!empty($branch))->andWhere('branch')->eq($branch)->fi()
            ->beginIF(!empty($moduleIdList))->andWhere('module')->in($moduleIdList)->fi()
            ->beginIF($projectID)->andWhere('project')->eq($projectID)->fi()
            ->beginIF($applicationID)->andWhere('applicationID')->eq($applicationID)->fi()
            ->andWhere('deleted')->eq(0)
            ->orderBy($orderBy)->page($pager)->fetchAll();
    }

    /**
     * Get bug list of a plan.
     *
     * @param  int    $planID
     * @param  string $status
     * @param  string $orderBy
     * @param  object $pager
     * @access public
     * @return void
     */
    public function getPlanBugs($planID, $status = 'all', $orderBy = 'id_desc', $pager = null)
    {
        $bugs = $this->dao->select('*')->from(TABLE_BUG)
            ->where('plan')->eq((int)$planID)
            ->beginIF(!$this->app->user->admin)->andWhere('execution')->in('0,' . $this->app->user->view->sprints)->fi()
            ->beginIF($status != 'all')->andWhere('status')->in($status)->fi()
            ->andWhere('deleted')->eq(0)
            ->orderBy($orderBy)->page($pager)->fetchAll('id');

        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'bug');

        return $bugs;
    }

    /**
     * Get info of a bug.
     *
     * @param  int    $bugID
     * @param  bool   $setImgSize
     * @access public
     * @return object
     */
    public function getById($bugID, $setImgSize = false)
    {
        $bug = $this->dao->select('t1.*, t2.name AS executionName, t3.title AS storyTitle, t3.status AS storyStatus, t3.version AS latestStoryVersion, t4.name AS taskName, t5.title AS planName')
            ->from(TABLE_BUG)->alias('t1')
            ->leftJoin(TABLE_EXECUTION)->alias('t2')->on('t1.execution = t2.id')
            ->leftJoin(TABLE_STORY)->alias('t3')->on('t1.story = t3.id')
            ->leftJoin(TABLE_TASK)->alias('t4')->on('t1.task = t4.id')
            ->leftJoin(TABLE_PRODUCTPLAN)->alias('t5')->on('t1.plan = t5.id')
            ->where('t1.id')->eq((int)$bugID)->fetch();
        if(!$bug) return false;

        $bug->linkTesttaskOddNumbers = [];

        $bug = $this->loadModel('file')->replaceImgURL($bug, 'steps,analysis');
        if($setImgSize) $bug->steps = $this->file->setImgSize($bug->steps);
        foreach($bug as $key => $value) if(strpos($key, 'Date') !== false and !(int)substr($value, 0, 4)) $bug->$key = '';

        if($bug->duplicateBug) $bug->duplicateBugTitle = $this->dao->findById($bug->duplicateBug)->from(TABLE_BUG)->fields('title')->fetch('title');
        if($bug->case)         $bug->caseTitle         = $this->dao->findById($bug->case)->from(TABLE_CASE)->fields('title')->fetch('title');
        if($bug->linkBug)      $bug->linkBugTitles     = $this->dao->select('id,title')->from(TABLE_BUG)->where('id')->in($bug->linkBug)->fetchPairs();
        if($bug->toStory > 0)  $bug->toStoryTitle      = $this->dao->findById($bug->toStory)->from(TABLE_STORY)->fields('title')->fetch('title');
        if($bug->toTask > 0)   $bug->toTaskTitle       = $this->dao->findById($bug->toTask)->from(TABLE_TASK)->fields('name')->fetch('name');
        
        if($bug->linkTesttask) $bug->linkTesttaskOddNumbers = $this->dao->select("id,concat(oddNumber, '(', name, ')') as oddNumber")->from(TABLE_TESTTASK)->where('id')->in($bug->linkTesttask)->fetchPairs();

        $bug->toCases = array();
        $toCases      = $this->dao->select('id, title')->from(TABLE_CASE)->where('`fromBug`')->eq($bugID)->fetchAll();
        foreach($toCases as $toCase) $bug->toCases[$toCase->id] = $toCase->title;

        $bug->files = $this->loadModel('file')->getByObject('bug', $bugID);

        return $this->checkDelayBug($bug);
    }

    /**
     * Get bug list.
     *
     * @param  int|array|string    $bugIDList
     * @param  string              $fields
     * @access public
     * @return array
     */
    public function getByList($bugIDList = 0, $fields = '*')
    {
        return $this->dao->select($fields)->from(TABLE_BUG)
            ->where('deleted')->eq(0)
            ->beginIF($bugIDList)->andWhere('id')->in($bugIDList)->fi()
            ->fetchAll('id');
    }

    /**
     * getActiveBugs
     *
     * @param  array    $products
     * @param  int      $branch
     * @param  array    $executions
     * @param  array    $excludeBugs
     * @param  object   $pager
     * @access public
     * @return array
     */
    public function getActiveBugs($products, $branch, $executions, $excludeBugs, $pager = null)
    {
        return $this->dao->select('*')->from(TABLE_BUG)
            ->where('status')->eq('active')
            ->andWhere('tostory')->eq(0)
            ->andWhere('toTask')->eq(0)
            ->beginIF(!empty($products))->andWhere('product')->in($products)->fi()
            ->beginIF($branch)->andWhere('branch')->in("0,$branch")->fi()
            ->beginIF(!empty($executions))->andWhere('execution')->in($executions)->fi()
            ->beginIF($excludeBugs)->andWhere('id')->notIN($excludeBugs)->fi()
            ->andWhere('deleted')->eq(0)
            ->orderBy('id desc')
            ->page($pager)
            ->fetchAll();
    }

    /**
     * Get active and postponed bugs.
     *
     * @param  int    $products
     * @param  int    $executionID
     * @param  int    $pager
     * @access public
     * @return void
     */
    public function getActiveAndPostponedBugs($products, $executionID, $pager = null)
    {
        return $this->dao->select('t1.*')->from(TABLE_BUG)->alias('t1')
            ->leftJoin(TABLE_PROJECTPRODUCT)->alias('t2')->on('t1.product = t2.product')
            ->where("((t1.status = 'resolved' AND t1.resolution = 'postponed') OR (t1.status = 'active'))")
            ->andWhere('t1.toTask')->eq(0)
            ->andWhere('t1.toStory')->eq(0)
            ->beginIF(!empty($products))->andWhere('t1.product')->in($products)->fi()
            ->beginIF(empty($products))->andWhere('t1.execution')->eq($executionID)->fi()
            ->andWhere('t2.project')->eq($executionID)
            ->andWhere("(t2.branch = '0' OR t1.branch = '0' OR t2.branch = t1.branch)")
            ->andWhere('t1.deleted')->eq(0)
            ->orderBy('id desc')
            ->page($pager)
            ->fetchAll();
    }

    /**
     * Get module owner.
     *
     * @param  int    $moduleID
     * @param  int    $productID
     * @access public
     * @return string
     */
    public function getModuleOwner($moduleID, $productID)
    {
        $users = $this->loadModel('user')->getPairs('nodeleted');
        $owner = $this->dao->findByID($productID)->from(TABLE_PRODUCT)->fetch('QD');
        $owner = isset($users[$owner]) ? $owner : '';

        if($moduleID)
        {
            $module = $this->dao->findByID($moduleID)->from(TABLE_MODULE)->andWhere('root')->eq($productID)->fetch();
            if(empty($module)) return $owner;

            if($module->owner and isset($users[$module->owner])) return $module->owner;

            $moduleIDList = explode(',', trim(str_replace(",$module->id,", ',', $module->path), ','));
            krsort($moduleIDList);
            if($moduleIDList)
            {
                $modules = $this->dao->select('*')->from(TABLE_MODULE)->where('id')->in($moduleIDList)->andWhere('deleted')->eq(0)->fetchAll('id');
                foreach($moduleIDList as $moduleID)
                {
                    if(isset($modules[$moduleID]))
                    {
                        $module = $modules[$moduleID];
                        if($module->owner and isset($users[$module->owner])) return $module->owner;
                    }
                }
            }
        }

        return $owner;
    }

    /**
     * Update a bug.
     *
     * @param  int    $bugID
     * @access public
     * @return void
     */
    public function update($bugID)
    {
        $oldBug = $this->dao->select('*')->from(TABLE_BUG)->where('id')->eq((int)$bugID)->fetch();
        if(!empty($_POST['lastEditedDate']) and $oldBug->lastEditedDate != $this->post->lastEditedDate)
        {
            dao::$errors[] = $this->lang->error->editedByOther;
            return false;
        }

        $now = helper::now();
        $bug = fixer::input('post')
            ->cleanInt('applicationID,product,module,severity,project,execution,story,task,branch')
            ->stripTags($this->config->bug->editor->edit['id'], $this->config->allowedTags)
            ->setDefault('applicationID,product,module,execution,story,task,duplicateBug,branch', 0)
            ->setDefault('openedBuild', '')
            ->setDefault('plan', 0)
            ->setDefault('deadline', '0000-00-00')
            ->setDefault('resolvedDate', '0000-00-00 00:00:00')
            ->setDefault('lastEditedBy', $this->app->user->account)
            ->add('lastEditedDate', $now)
            ->setIF(strpos($this->config->bug->edit->requiredFields, 'deadline') !== false, 'deadline', $this->post->deadline)
            ->join('openedBuild', ',')
            ->join('mailto', ',')
            ->join('linkBug', ',')
            ->join('linkTesttask', ',')
            ->setIF($this->post->assignedTo  != $oldBug->assignedTo, 'assignedDate', $now)
            ->setIF($this->post->resolvedBy  != '' and $this->post->resolvedDate == '', 'resolvedDate', $now)
            ->setIF($this->post->resolution  != '' and $this->post->resolvedDate == '', 'resolvedDate', $now)
            ->setIF($this->post->resolution  != '' and $this->post->resolvedBy   == '', 'resolvedBy',   $this->app->user->account)
            ->setIF($this->post->closedBy    != '' and $this->post->closedDate   == '', 'closedDate',   $now)
            ->setIF($this->post->closedDate  != '' and $this->post->closedBy     == '', 'closedBy',     $this->app->user->account)
            ->setIF($this->post->closedBy    != '' or  $this->post->closedDate   != '', 'assignedTo',   'closed')
            ->setIF($this->post->closedBy    != '' or  $this->post->closedDate   != '', 'assignedDate', $now)
            ->setIF($this->post->resolution  != '' or  $this->post->resolvedDate != '', 'status',       'resolved')
            ->setIF($this->post->closedBy    != '' or  $this->post->closedDate   != '', 'status',       'closed')
            ->setIF(($this->post->resolution != '' or  $this->post->resolvedDate != '') and $this->post->assignedTo == '', 'assignedTo', $oldBug->openedBy)
            ->setIF(($this->post->resolution != '' or  $this->post->resolvedDate != '') and $this->post->assignedTo == '', 'assignedDate', $now)
            ->setIF($this->post->resolution  == '' and $this->post->resolvedDate =='', 'status', 'active')
            ->setIF($this->post->resolution  != '', 'confirmed', 1)
            ->setIF($this->post->story != false and $this->post->story != $oldBug->story, 'storyVersion', $this->loadModel('story')->getVersion($this->post->story))
            ->setIF(!$this->post->linkBug, 'linkBug', '')
            ->remove('comment,files,labels,uid,contactListMenu')
            ->get();

        // 清空该字段
        if(!isset($bug->linkTesttask)) $bug->linkTesttask = '';

        $bug = $this->loadModel('file')->processImgURL($bug, $this->config->bug->editor->edit['id'], $this->post->uid);
        $this->dao->update(TABLE_BUG)->data($bug)
            ->autoCheck()
            ->batchCheck($this->config->bug->edit->requiredFields, 'notempty')
            ->checkIF($bug->resolvedBy, 'resolution', 'notempty')
            ->checkIF($bug->closedBy,   'resolution', 'notempty')
            ->checkIF($bug->resolution == 'duplicate', 'duplicateBug', 'notempty')
            ->where('id')->eq((int)$bugID)
            ->exec();

        if(!dao::isError())
        {
            if(!empty($bug->resolvedBy)) $this->loadModel('score')->create('bug', 'resolve', $bugID);
            $this->file->updateObjectID($this->post->uid, $bugID, 'bug');
            return common::createChanges($oldBug, $bug);
        }
    }

    /**
     * Batch update bugs.
     *
     * @access public
     * @return array
     */
    public function batchUpdate()
    {
        $bugs       = array();
        $allChanges = array();
        $now        = helper::now();
        $data       = fixer::input('post')->get();
        $bugIDList  = $this->post->bugIDList ? $this->post->bugIDList : array();

        if(!empty($bugIDList))
        {
            /* Process the data if the value is 'ditto'. */
            foreach($bugIDList as $bugID)
            {
                if($data->types[$bugID]       == 'ditto') $data->types[$bugID]       = isset($prev['type'])       ? $prev['type']       : '';
                if($data->childTypes[$bugID]  == 'ditto') $data->childTypes[$bugID]  = isset($prev['childType'])  ? $prev['childType']  : '';
                if($data->severities[$bugID]  == 'ditto') $data->severities[$bugID]  = isset($prev['severity'])   ? $prev['severity']   : 3;
                if($data->pris[$bugID]        == 'ditto') $data->pris[$bugID]        = isset($prev['pri'])        ? $prev['pri']        : 0;
                if($data->assignedTos[$bugID] == 'ditto') $data->assignedTos[$bugID] = isset($prev['assignedTo']) ? $prev['assignedTo'] : '';
                if($data->resolvedBys[$bugID] == 'ditto') $data->resolvedBys[$bugID] = isset($prev['resolvedBy']) ? $prev['resolvedBy'] : '';
                if($data->resolutions[$bugID] == 'ditto') $data->resolutions[$bugID] = isset($prev['resolution']) ? $prev['resolution'] : '';
                if($data->os[$bugID]          == 'ditto') $data->os[$bugID]          = isset($prev['os'])         ? $prev['os'] : '';
                if($data->browsers[$bugID]    == 'ditto') $data->browsers[$bugID]    = isset($prev['browser'])    ? $prev['browser'] : '';
                if(isset($data->branches[$bugID]) and $data->branches[$bugID] == 'ditto') $data->branches[$bugID] = isset($prev['branch']) ? $prev['branch'] : 0;

                $prev['type']       = $data->types[$bugID];
                $prev['childType']  = $data->childTypes[$bugID];
                $prev['severity']   = $data->severities[$bugID];
                $prev['pri']        = $data->pris[$bugID];
                $prev['branch']     = isset($data->branches[$bugID]) ? $data->branches[$bugID] : '';
                $prev['assignedTo'] = $data->assignedTos[$bugID];
                $prev['resolvedBy'] = $data->resolvedBys[$bugID];
                $prev['resolution'] = $data->resolutions[$bugID];
                $prev['os']         = $data->os[$bugID];
                $prev['browser']    = $data->browsers[$bugID];
            }

            /* Initialize bugs from the post data.*/
            $extendFields = $this->getFlowExtendFields();
            $oldBugs = $bugIDList ? $this->getByList($bugIDList) : array();
            foreach($bugIDList as $bugID)
            {
                $oldBug = $oldBugs[$bugID];

                $bug = new stdclass();
                $bug->lastEditedBy   = $this->app->user->account;
                $bug->lastEditedDate = $now;
                $bug->type           = $data->types[$bugID];
                $bug->childType      = $data->childTypes[$bugID];
                $bug->severity       = $data->severities[$bugID];
                $bug->pri            = $data->pris[$bugID];
                $bug->status         = $data->statuses[$bugID];
                $bug->color          = $data->colors[$bugID];
                $bug->title          = $data->titles[$bugID];
                $bug->branch         = empty($data->branches[$bugID]) ? 0 : $data->branches[$bugID];
                $bug->assignedTo     = $data->assignedTos[$bugID];
                $bug->deadline       = $data->deadlines[$bugID];
                $bug->resolvedBy     = $data->resolvedBys[$bugID];
                $bug->keywords       = $data->keywords[$bugID];
                $bug->os             = $data->os[$bugID];
                $bug->browser        = $data->browsers[$bugID];
                $bug->resolution     = $data->resolutions[$bugID];
                $bug->duplicateBug   = $data->duplicateBugs[$bugID] ? $data->duplicateBugs[$bugID] : $oldBug->duplicateBug;

                if($bug->assignedTo  != $oldBug->assignedTo)           $bug->assignedDate = $now;
                if(($bug->resolvedBy != '' or $bug->resolution != '') and $oldBug->status != 'resolved' and $bug->status != 'closed') $bug->resolvedDate = $now;
                if($bug->resolution  != '' and $bug->resolvedBy == '') $bug->resolvedBy   = $this->app->user->account;
                if($bug->resolution  != '' and $bug->status != 'closed')
                {
                    $bug->status    = 'resolved';
                    $bug->confirmed = 1;
                }
                if($bug->resolution  != '' and $bug->assignedTo == '')
                {
                    $bug->assignedTo   = $oldBug->openedBy;
                    $bug->assignedDate = $now;
                }

                foreach($extendFields as $extendField)
                {
                    $bug->{$extendField->field} = htmlspecialchars($this->post->{$extendField->field}[$bugID]);
                    $message = $this->checkFlowRule($extendField, $bug->{$extendField->field});
                    if($message) die(js::alert($message));
                }

                $bugs[$bugID] = $bug;
                unset($bug);
            }

            /* Update bugs. */
            foreach($bugs as $bugID => $bug)
            {
                $oldBug = $oldBugs[$bugID];

                $this->dao->update(TABLE_BUG)->data($bug)
                    ->autoCheck()
                    ->batchCheck($this->config->bug->edit->requiredFields, 'notempty')
                    ->checkIF($bug->resolvedBy, 'resolution', 'notempty')
                    ->checkIF($bug->resolution == 'duplicate', 'duplicateBug', 'notempty')
                    ->where('id')->eq((int)$bugID)
                    ->exec();

                if(!dao::isError())
                {
                    if(!empty($bug->resolvedBy)) $this->loadModel('score')->create('bug', 'resolve', $bugID);

                    $this->executeHooks($bugID);

                    $allChanges[$bugID] = common::createChanges($oldBug, $bug);
                }
                else
                {
                    die(js::error('bug#' . $bugID . dao::getError(true)));
                }
            }
        }
        if(!dao::isError()) $this->loadModel('score')->create('ajax', 'batchEdit');
        return $allChanges;
    }

    /**
     * Batch active bugs.
     *
     * @access public
     * @return array
     */
    public function batchActivate()
    {
        $now  = helper::now();
        $data = fixer::input('post')->get();

        $activateBugs = array();
        $bugIDList    = $data->bugIDList ? $data->bugIDList : array();

        if(empty($bugIDList)) return $activateBugs;

        foreach($bugIDList as $bugID)
        {
            if($data->statusList[$bugID] == 'active') continue;

            $activateBugs[$bugID]['assignedTo']  = $data->assignedToList[$bugID];
            $activateBugs[$bugID]['openedBuild'] = $data->openedBuildList[$bugID];
            $activateBugs[$bugID]['comment']     = $data->commentList[$bugID];

            $activateBugs[$bugID]['activatedDate']  = $now;
            $activateBugs[$bugID]['assignedDate']   = $now;
            $activateBugs[$bugID]['resolution']     = '';
            $activateBugs[$bugID]['status']         = 'active';
            $activateBugs[$bugID]['resolvedDate']   = '0000-00-00';
            $activateBugs[$bugID]['resolvedBy']     = '';
            $activateBugs[$bugID]['resolvedBuild']  = '';
            $activateBugs[$bugID]['closedBy']       = '';
            $activateBugs[$bugID]['closedDate']     = '0000-00-00';
            $activateBugs[$bugID]['duplicateBug']   = 0;
            $activateBugs[$bugID]['toTask']         = 0;
            $activateBugs[$bugID]['toStory']        = 0;
            $activateBugs[$bugID]['lastEditedBy']   = $this->app->user->account;
            $activateBugs[$bugID]['lastEditedDate'] = $now;
        }

        /* Update bugs. */
        foreach($activateBugs as $bugID => $bug)
        {
            $oldBug = $bugs[$bugID];
            $this->dao->update(TABLE_BUG)->data($bug, $skipFields = 'comment')->autoCheck()->where('id')->eq((int)$bugID)->exec();
            if(dao::isError()) die(js::error('bug#' . $bugID . dao::getError(true)));

            $this->dao->update(TABLE_BUG)->set('activatedCount = activatedCount + 1')->where('id')->eq((int)$bugID)->exec();
        }

        return $activateBugs;
    }

    /**
     * Assign a bug to a user again.
     *
     * @param  int    $bugID
     * @access public
     * @return string
     */
    public function assign($bugID)
    {
        $now = helper::now();
        $oldBug = $this->getById($bugID);
        $bug = fixer::input('post')
            ->setDefault('lastEditedBy', $this->app->user->account)
            ->setDefault('lastEditedDate', $now)
            ->setDefault('assignedDate', $now)
            ->remove('comment,showModule')
            ->stripTags($this->config->bug->editor->assignto['id'], $this->config->allowedTags)
            ->join('mailto', ',')
            ->get();

        $bug = $this->loadModel('file')->processImgURL($bug, $this->config->bug->editor->assignto['id'], $this->post->uid);
        $this->dao->update(TABLE_BUG)
            ->data($bug)
            ->autoCheck()
            ->where('id')->eq($bugID)->exec();

        if(!dao::isError())
        {
            $this->file->updateObjectID($this->post->uid, $bugID, 'bug');
            $this->file->saveUpload('bug', $bugID);

            return common::createChanges($oldBug, $bug);
        }
    }

    /**
     * Confirm a bug.
     *
     * @param  int    $bugID
     * @access public
     * @return void
     */
    public function confirm($bugID)
    {
        $now    = helper::now();
        $oldBug = $this->getById($bugID);

        $bug = fixer::input('post')
            ->setDefault('confirmed', 1)
            ->setDefault('lastEditedBy', $this->app->user->account)
            ->setDefault('lastEditedDate', $now)
            ->setDefault('assignedDate', $now)
            ->remove('comment')
            ->join('mailto', ',')
            ->get();

        $this->dao->update(TABLE_BUG)->data($bug)->where('id')->eq($bugID)->exec();

        if(!dao::isError())
        {
            $this->loadModel('score')->create('bug', 'confirmBug', $oldBug);
            return common::createChanges($oldBug, $bug);
        }
    }

    /**
     * defect a bug.
     *
     * @param  int    $bugID
     * @access public
     * @return void
     */
    public function defect($bugID)
    {
        $this->loadModel('action');
        $date   = helper::today();
        $number = $this->dao->select('count(id) c')->from(TABLE_DEFECT)->where('createdDate')->gt($date)->fetch('c');
        $code   = 'CFIT-UB-' . date('Ymd-') . sprintf('%03d', $number+1);

        $oldBug = $this->getById($bugID);
        $data = fixer::input('post')
            ->add('status','tosolve')//默认缺陷为待处理状态
            ->setDefault('bugId', $bugID)
            ->setDefault('source', 1) //数据来源，内部bug转缺陷
            ->setDefault('code', $code)
            ->setDefault('uatId', $code)
            ->setDefault('createdBy', $this->app->user->account)
            ->setDefault('createdDate', helper::now())
            ->stripTags($this->config->bug->editor->defectbug['id'], $this->config->allowedTags)
            ->remove('uid,files')
            ->join('cc', ',')
            ->get();
        if($data->project == ''){
            dao::$errors[] = '[所属项目] 不能为空！';
            return;
        }
        if($data->product == ''){
            dao::$errors[] = '[所属产品] 不能为空！';
            return;
        }

        if($data->issues == ''){
            dao::$errors[] = '[重现步骤] 不能为空！';
            return;
        }

        if($data->severity == ''){
            dao::$errors[] = '[严重程度] 不能为空！';
            return;
        }
        if($data->frequency == ''){
            dao::$errors[] = '[出现频次] 不能为空！';
            return;
        }
        if($data->dealUser == ''){
            dao::$errors[] = '[指派给] 不能为空！';
            return;
        }

        /* Can create build when resolve bug. */
        if(isset($data->createBuild))
        {
            $buildData = new stdclass();
            $buildData->product   = (int)$oldBug->product;
            $buildData->branch    = (int)$oldBug->branch;
            $buildData->project   = $this->dao->select('project')->from(TABLE_EXECUTION)->where('id')->eq($data->buildExecution)->fetch('project');
            $buildData->execution = $data->buildExecution;
            $buildData->name      = $data->buildName;
            $buildData->date      = date('Y-m-d');
            $buildData->builder   = $this->app->user->account;

            $this->dao->insert(TABLE_BUILD)->data($buildData)->autoCheck()
                ->check('name', 'unique', "product = {$buildData->product} AND branch = {$buildData->branch} AND deleted = '0'")
                ->exec();

            if(dao::isError()) return false;

            $buildID = $this->dao->lastInsertID();
            $this->loadModel('action')->create('build', $buildID, 'opened');
            $data->resolvedBuild = $buildID;
        }

        $data->type = $data->defectType;
        $usersDept = $this->loadModel('user')->getUserDeptIds($data->developer);
        $deptId = 0;
        if(!empty($usersDept)){
            $deptId = $usersDept[0];
        }
        $data->dept = $deptId;
        unset($data->defectType);
        unset($data->createBuild);
        unset($data->buildExecution);
        unset($data->buildName);
        $this->app->loadLang('defect');

        //获取项目经理字段
        $planId = $this->dao->select('id')->from(TABLE_PROJECTPLAN)->where('project')->eq($data->project)->fetch('id');
        $PM = '';
        if($planId){
            $PM = $this->dao->select('PM')->from(TABLE_PROJECTCREATION)->where('plan')->eq($planId)->fetch('PM');
        }
        $data->projectManager = $PM;

        $hadDefect = $this->dao->select('id')->from(TABLE_DEFECT)->where('bugId')->eq($bugID)->fetchAll();
        if(empty($hadDefect)){
            $this->dao->insert(TABLE_DEFECT)->data($data)->autocheck()->batchCheck($this->config->bug->defectbug->requiredFields, 'notempty')->exec();
            $defectID = $this->dao->lastInsertID();
            //更新bug状态为已关闭
            $bug=new stdClass();
            $bug->status = 'closed';
            $bug->closedBy = $this->app->user->account;
            $bug->closedDate = helper::now();
            $bug->assignedTo = 'closed';
            $this->dao->update(TABLE_BUG)->data($bug)->where('id')->eq($bugID)->exec();
        }else{
            dao::$errors[] = '该实验室缺陷已转为清总缺陷，无法重复操作！';
            return;
        }
        if(!dao::isError())
        {
            if(!dao::isError()) $this->action->create('defect', $defectID, 'created', $this->lang->bug->defectbug);
            return array('status' => 'defect', 'id' => $bugID);
        }
        return false;
    }
    /**
     *
     * @param $defectID
     * @return mixed
     */
    public function getOneBybugId($bugId)
    {
        return $this->dao->select('id,code')->from(TABLE_DEFECT)->where('bugId')->eq($bugId)->fetch();
    }
    /**
     * Batch confirm bugs.
     *
     * @param  array $bugIDList
     * @access public
     * @return void
     */
    public function batchConfirm($bugIDList)
    {
        $now  = helper::now();
        $bugs = $this->getByList($bugIDList);
        foreach($bugIDList as $bugID)
        {
            if($bugs[$bugID]->confirmed) continue;

            $bug = new stdclass();
            $bug->assignedTo     = $this->app->user->account;
            $bug->lastEditedBy   = $this->app->user->account;
            $bug->lastEditedDate = $now;
            $bug->confirmed      = 1;

            $this->dao->update(TABLE_BUG)->data($bug)->where('id')->eq($bugID)->exec();
            $this->executeHooks($bugID);
        }
    }

    /**
     * Resolve a bug.
     *
     * @param  int    $bugID
     * @access public
     * @return void
     */
    public function resolve($bugID)
    {
        $now    = helper::now();
        $oldBug = $this->getById($bugID);
        $bug    = fixer::input('post')
            ->add('status', 'resolved')
            ->add('confirmed', 1)
            ->cleanINT('project')
            ->setDefault('lastEditedBy',   $this->app->user->account)
            ->setDefault('lastEditedDate', $now)
            ->setDefault('resolvedBy',     $this->app->user->account)
            ->setDefault('assignedDate',   $now)
            ->setDefault('resolvedDate',   $now)
            ->setDefault('assignedTo',     $oldBug->openedBy)
            ->removeIF($this->post->resolution != 'duplicate', 'duplicateBug')
            ->remove('files,labels')
            ->stripTags($this->config->bug->editor->resolve['id'], $this->config->allowedTags)
            ->cleanINT('product')
            ->cleanINT('project')
            ->get();

        /* Set comment lang for alert error. */
        $this->lang->bug->comment = $this->lang->comment;

        $bug = $this->loadModel('file')->processImgURL($bug, $this->config->bug->editor->resolve['id'], $this->post->uid);
        $this->dao->update(TABLE_BUG)->data($bug, 'buildName,createBuild,buildExecution,comment')
            ->autoCheck()
            ->batchCheck($this->config->bug->resolve->requiredFields, 'notempty')
            ->checkIF($bug->resolution == 'duplicate', 'duplicateBug', 'notempty')
            ->checkIF($bug->resolution == 'fixed',     'resolvedBuild','notempty')
            ->where('id')->eq((int)$bugID)
            ->exec();

        if(!dao::isError())
        {
            $this->file->updateObjectID($this->post->uid, $bugID, 'bug');
            $this->file->saveUpload('bug', $bugID);

            $this->loadModel('score')->create('bug', 'resolve', $oldBug);

            /* Link bug to build and release. */
            $this->linkBugToBuild($bugID, $bug->resolvedBuild);

            return common::createChanges($oldBug, $bug);
        }

        return false;
    }

    /**
     * Batch change the module of bug.
     *
     * @param  array  $bugIDList
     * @param  int    $moduleID
     * @access public
     * @return array
     */
    public function batchChangeModule($bugIDList, $moduleID)
    {
        $now        = helper::now();
        $allChanges = array();
        $oldBugs    = $this->getByList($bugIDList);
        foreach($bugIDList as $bugID)
        {
            $oldBug = $oldBugs[$bugID];
            if($moduleID == $oldBug->module) continue;

            $bug = new stdclass();
            $bug->lastEditedBy   = $this->app->user->account;
            $bug->lastEditedDate = $now;
            $bug->module         = $moduleID;

            $this->dao->update(TABLE_BUG)->data($bug)->autoCheck()->where('id')->eq((int)$bugID)->exec();
            if(!dao::isError()) $allChanges[$bugID] = common::createChanges($oldBug, $bug);
        }
        return $allChanges;
    }

    /**
     * Batch resolve bugs.
     *
     * @param  array    $bugIDList
     * @param  string   $resolution
     * @param  string   $resolvedBuild
     * @access public
     * @return void
     */
    public function batchResolve($bugIDList, $resolution, $resolvedBuild)
    {
        $now  = helper::now();
        $bugs = $this->getByList($bugIDList);

        $bug       = reset($bugs);
        $productID = $bug->product;
        $users     = $this->loadModel('user')->getPairs();
        $product   = $this->dao->findById($productID)->from(TABLE_PRODUCT)->fetch();
        $stmt      = $this->dao->query($this->loadModel('tree')->buildMenuQuery($productID, 'bug'));
        $modules   = array();
        while($module = $stmt->fetch()) $modules[$module->id] = $module;

        $changes = array();
        foreach($bugIDList as $i => $bugID)
        {
            $oldBug = $bugs[$bugID];
            if($oldBug->resolution == 'fixed')
            {
                unset($bugIDList[$i]);
                continue;
            }
            if($oldBug->status != 'active') continue;

            $assignedTo = $oldBug->openedBy;
            if(!isset($users[$assignedTo]))
            {
                $assignedTo = '';
                $module     = isset($modules[$oldBug->module]) ? $modules[$oldBug->module] : '';
                while($module)
                {
                    if($module->owner and isset($users[$module->owner]))
                    {
                        $assignedTo = $module->owner;
                        break;
                    }
                    $module = isset($modules[$module->parent]) ? $modules[$module->parent] : '';
                }
                if(empty($assignedTo)) $assignedTo = $product->QD;
            }

            $bug = new stdClass();
            $bug->resolution     = $resolution;
            $bug->resolvedBuild  = $resolution == 'fixed' ? $resolvedBuild : '';
            $bug->resolvedBy     = $this->app->user->account;
            $bug->resolvedDate   = $now;
            $bug->status         = 'resolved';
            $bug->confirmed      = 1;
            $bug->assignedTo     = $assignedTo;
            $bug->assignedDate   = $now;
            $bug->lastEditedBy   = $this->app->user->account;
            $bug->lastEditedDate = $now;

            $this->dao->update(TABLE_BUG)->data($bug)->where('id')->eq($bugID)->exec();
            $this->executeHooks($bugID);

            $changes[$bugID] = common::createChanges($oldBug, $bug);
        }

        /* Link bug to build and release. */
        $this->linkBugToBuild($bugIDList, $resolvedBuild);

        return $changes;
    }

    /**
     * Activate a bug.
     *
     * @param  int    $bugID
     * @access public
     * @return void
     */
    public function activate($bugID)
    {
        $oldBug = $this->getById($bugID);
        $now = helper::now();
        $bug = fixer::input('post')
            ->setDefault('assignedTo',     $oldBug->resolvedBy)
            ->setDefault('assignedDate',   $now)
            ->setDefault('lastEditedBy',   $this->app->user->account)
            ->setDefault('lastEditedDate', $now)
            ->setDefault('activatedDate',  $now)
            ->setDefault('activatedCount', (int)$oldBug->activatedCount)
            ->add('resolution', '')
            ->add('status', 'active')
            ->add('resolvedDate', '0000-00-00')
            ->add('resolvedBy', '')
            ->add('resolvedBuild', '')
            ->add('closedBy', '')
            ->add('closedDate', '0000-00-00')
            ->add('duplicateBug', 0)
            ->add('toTask', 0)
            ->add('toStory', 0)
            ->join('openedBuild', ',')
            ->join('linkPlan', ',')
            ->remove('comment,files,labels')
            ->get();

        $this->dao->update(TABLE_BUG)->data($bug)->autoCheck()->where('id')->eq((int)$bugID)->exec();
        $this->dao->update(TABLE_BUG)->set('activatedCount = activatedCount + 1')->where('id')->eq((int)$bugID)->exec();

        $openedBuilds = $this->post->openedBuild;
        if($openedBuilds)
        {
            $this->loadModel('build');
            foreach($openedBuilds as $openedBuild)
            {
                $build = $this->build->getByID($openedBuild);
                $build->bugs = trim(str_replace(",$bugID,", ',', ",$build->bugs,"), ',');
                $this->dao->update(TABLE_BUILD)->set('bugs')->eq($build->bugs)->where('id')->eq((int)$openedBuild)->exec();
            }
        }

        $bug->activatedCount += 1;
        return common::createChanges($oldBug, $bug);
    }

    /**
     * Close a bug.
     *
     * @param  int    $bugID
     * @access public
     * @return void
     */
    public function close($bugID)
    {
        $now    = helper::now();
        $oldBug = $this->getById($bugID);
        $bug    = fixer::input('post')
            ->add('assignedTo', 'closed')
            ->add('status',     'closed')
            ->add('confirmed',  1)
            ->setDefault('assignedDate',   $now)
            ->setDefault('lastEditedBy',   $this->app->user->account)
            ->setDefault('lastEditedDate', $now)
            ->setDefault('closedBy',       $this->app->user->account)
            ->setDefault('closedDate',     $now)
            ->remove('comment')
            ->get();

        $this->dao->update(TABLE_BUG)->data($bug)->autoCheck()->where('id')->eq((int)$bugID)->exec();

        return common::createChanges($oldBug, $bug);
    }

    /**
     * Get bugs to link.
     *
     * @param  int    $bugID
     * @param  string $browseType
     * @param  int    $queryID
     * @access public
     * @return array
     */
    public function getBugs2Link($bugID, $browseType = 'bySearch', $queryID)
    {
        if($browseType == 'bySearch')
        {
            $bug       = $this->getById($bugID);
            $productIdList = $this->rebirth->getProductIdList($bug->applicationID, $bug->product);
            $bugs2Link = $this->getBySearch($productIdList, 0, $queryID, 'id', '', null, 0, $bug->applicationID);
            foreach($bugs2Link as $key => $bug2Link)
            {
                if($bug2Link->id == $bugID) unset($bugs2Link[$key]);
                if(in_array($bug2Link->id, explode(',', $bug->linkBug))) unset($bugs2Link[$key]);
            }
            return $bugs2Link;
        }
        else
        {
            return array();
        }
    }

    /**
     * Build search form.
     *
     * @param  int    $queryID
     * @param  string $actionURL
     * @param  int    $applicationID
     * @param  int    $productID
     * @access public
     * @return void
     */
    public function buildSearchForm($queryID, $actionURL, $applicationID = 0, $productID = 0)
    {
        $this->loadModel('rebirth');
        $products = array();
        if($applicationID) $products = $this->rebirth->getProductPairs($applicationID, $showAll = false, $naToZero = true);

        $modules = array();
        $plans   = array();
        $builds  = array();
        if(is_numeric($productID))
        {
            $modules = $this->loadModel('tree')->getOptionMenu($productID, 'bug', 0);
            $plans   = $this->loadModel('productplan')->getPairsOfProductName($productID);
            $builds  = $this->loadModel('build')->getProductBuildPairs($productID, 0, $params = '');
        }
        else if($productID == 'all')
        {
            $productIdList = $this->rebirth->getProductIdList($applicationID);
            $productIdList = array_filter($productIdList, function($value) { return $value > 0; });
            $plans         = $this->loadModel('productplan')->getPairsOfProductName($productIdList);
        }

        $projects = $this->rebirth->getProductLinkProjectPairs($applicationID, $productID);

        $this->config->bug->search['actionURL']                         = $actionURL;
        $this->config->bug->search['queryID']                           = $queryID;
        $this->config->bug->search['params']['project']['values']       = array(0 => '') + $projects;
        $this->config->bug->search['params']['product']['values']       = array(0 => '') + $products;
        $this->config->bug->search['params']['severity']['values']      = array(0 => '') + $this->lang->bug->severityList;
        $this->config->bug->search['params']['module']['values']        = array(0 => '') + $modules;
        $this->config->bug->search['params']['linkPlan']['values']      = array(0 => '') + $plans;
        $this->config->bug->search['params']['openedBuild']['values']   = array(0 => '') + $builds;
        $this->config->bug->search['params']['resolvedBuild']['values'] = array(0 => '') + $builds;

        $childTypeList = $this->getChildTypeParentNameList();
        $this->config->bug->search['params']['childType']['values'] = $childTypeList;

        $this->loadModel('search')->setSearchParams($this->config->bug->search);
    }

    /**
     * Process the openedBuild and resolvedBuild fields for bugs.
     *
     * @param  array  $bugs
     * @access public
     * @return array
     */
    public function processBuildForBugs($bugs)
    {
        $productIdList = array();
        foreach($bugs as $bug) $productIdList[$bug->id] = $bug->product;
        $builds = $this->loadModel('build')->getProductBuildPairs(array_unique($productIdList), 0, $params = '');

        /* Process the openedBuild and resolvedBuild fields. */
        foreach($bugs as $key => $bug)
        {
            $openBuildIdList = explode(',', $bug->openedBuild);
            $openedBuild = '';
            foreach($openBuildIdList as $buildID)
            {
                $openedBuild .= isset($builds[$buildID]) ? $builds[$buildID] : $buildID;
                $openedBuild .= ',';
            }
            $bug->openedBuild   = rtrim($openedBuild, ',');
            $bug->resolvedBuild = isset($builds[$bug->resolvedBuild]) ? $builds[$bug->resolvedBuild] : $bug->resolvedBuild;
        }

        return $bugs;
    }

    /**
     * Process the plan
     *
     * @param  array  $bugs
     * @access public
     * @return array
     */
    public function processPlanForBugs($bugs)
    {
        $productIdList = array();
        foreach($bugs as $bug) if(!isset($productIdList[$bug->product])) $productIdList[$bug->product] = $bug->product;
        $plans = $this->loadModel('productplan')->getPairs($productIdList);
        foreach($bugs as $key => $bug)
        {
            $PlanIdList = explode(',', $bug->linkPlan);
            $plan       = '';
            foreach($PlanIdList as $planId)
            {
                $plan .= isset($plans[$planId]) ? $plans[$planId] : $planId;
                $plan .= ',';
            }
            $bug->linkPlans = rtrim($plan, ',');
        }
        return $bugs;
    }

    /**
     * Extract accounts from some bugs.
     *
     * @param  int    $bugs
     * @access public
     * @return array
     */
    public function extractAccountsFromList($bugs)
    {
        $accounts = array();
        foreach($bugs as $bug)
        {
            if(!empty($bug->openedBy))     $accounts[] = $bug->openedBy;
            if(!empty($bug->assignedTo))   $accounts[] = $bug->assignedTo;
            if(!empty($bug->resolvedBy))   $accounts[] = $bug->resolvedBy;
            if(!empty($bug->closedBy))     $accounts[] = $bug->closedBy;
            if(!empty($bug->lastEditedBy)) $accounts[] = $bug->lastEditedBy;
        }
        return array_unique($accounts);
    }

    /**
     * Extract accounts from a bug.
     *
     * @param  object    $bug
     * @access public
     * @return array
     */
    public function extractAccountsFromSingle($bug)
    {
        $accounts = array();
        if(!empty($bug->openedBy))     $accounts[] = $bug->openedBy;
        if(!empty($bug->assignedTo))   $accounts[] = $bug->assignedTo;
        if(!empty($bug->resolvedBy))   $accounts[] = $bug->resolvedBy;
        if(!empty($bug->closedBy))     $accounts[] = $bug->closedBy;
        if(!empty($bug->lastEditedBy)) $accounts[] = $bug->lastEditedBy;
        return array_unique($accounts);
    }

    /**
     * Get user bugs.
     *
     * @param  string $account
     * @param  string $type
     * @param  string $orderBy
     * @param  int    $limit
     * @param  object $pager
     * @param  int    $executionID
     * @access public
     * @return void
     */
    public function getUserBugs($account, $type = 'assignedTo', $orderBy = 'id_desc', $limit = 0, $pager = null, $executionID = 0)
    {
        if(!$this->loadModel('common')->checkField(TABLE_BUG, $type)) return array();
        $bugs = $this->dao->select('t1.*,t2.name as applicationName')->from(TABLE_BUG)->alias('t1')
            ->leftJoin(TABLE_APPLICATION)->alias('t2')->on('t1.applicationID = t2.id')
            ->where('t1.deleted')->eq(0)
            ->beginIF($executionID)->andWhere('t1.execution')->eq($executionID)->fi()
            ->beginIF($type != 'closedBy' and $this->app->moduleName == 'block')->andWhere('t1.status')->ne('closed')->fi()
            ->beginIF($type != 'all')->andWhere("t1.`$type`")->eq($account)->fi()
            ->orderBy($orderBy)
            ->beginIF($limit > 0)->limit($limit)->fi()
            ->page($pager)
            ->fetchAll();
        return $bugs ? $bugs : array();
    }

    /**
     * Get bug pairs of a user.
     *
     * @param  int    $account
     * @param  bool   $appendProduct
     * @param  int    $limit
     * @param  array  $skipProductIDList
     * @param  array  $skipExecutionIDList
     * @access public
     * @return array
     */
    public function getUserBugPairs($account, $appendProduct = true, $limit = 0, $skipProductIDList = array(), $skipExecutionIDList = array())
    {
        $bugs = array();
        $stmt = $this->dao->select('t1.id, t1.title, t2.name as product')
            ->from(TABLE_BUG)->alias('t1')
            ->leftJoin(TABLE_PRODUCT)->alias('t2')
            ->on('t1.product=t2.id')
            ->where('t1.assignedTo')->eq($account)
            ->beginIF(!empty($skipProductIDList))->andWhere('t1.product')->notin($skipProductIDList)->fi()
            ->beginIF(!empty($skipExecutionIDList))->andWhere('t1.execution')->notin($skipExecutionIDList)->fi()
            ->andWhere('t1.deleted')->eq(0)
            ->orderBy('id desc')
            ->beginIF($limit > 0)->limit($limit)->fi()
            ->query();
        while($bug = $stmt->fetch())
        {
            if($appendProduct) $bug->title = $bug->product . ' / ' . $bug->title;
            $bugs[$bug->id] = $bug->title;
        }
        return $bugs;
    }


    /**
     * Get bugs of a project.
     *
     * @param  int    $projectID
     * @param  int    $productID
     * @param  int    $build
     * @param  string $type
     * @param  int    $param
     * @param  string $orderBy
     * @param  string $excludeBugs
     * @param  object $pager
     * @param  int    $taskID
     * @access public
     * @return array
     */
    public function getProjectBugsJoinDefect($projectID,$applicationID, $productID = 0, $build = 0, $type = '', $param = 0, $orderBy = 'id_desc', $excludeBugs = '', $pager = null, $taskID = 0)
    {
        $type = strtolower($type);
        if($type == 'bysearch')
        {
            $queryID = (int)$param;
            if($this->session->projectBugQuery == false) $this->session->set('projectBugQuery', ' 1 = 1');
            if($queryID)
            {
                $query = $this->loadModel('search')->getQuery($queryID);
                if($query)
                {
                    $this->session->set('projectBugQuery', $query->sql);
                    $this->session->set('projectBugForm', $query->form);
                }
            }

            $allProduct = "`product` = 'all'";
            $bugQuery   = $this->session->projectBugQuery;
            if(strpos($this->session->projectBugQuery, $allProduct) !== false)
            {
                $bugQuery = str_replace($allProduct, '1', $this->session->projectBugQuery);
            }

            if (strpos($bugQuery, 'linkDefect')) {
                $bugQuery = str_replace('AND (`', ' AND (`t2.', $bugQuery);
                $bugQuery = str_replace('AND `', ' AND `t2.', $bugQuery);
                $bugQuery = str_replace('OR (`', ' OR (`t2.', $bugQuery);
                $bugQuery = str_replace('OR `', ' OR `t2.', $bugQuery);
                $bugQuery = str_replace('t2.linkDefect', 't2.code', $bugQuery);
                $bugQuery = str_replace('`', '', $bugQuery);
            }else{
                $bugQuery = str_replace('OR (`', ' OR (`t1.', $bugQuery);
                $bugQuery = str_replace('OR `', ' OR `t1.', $bugQuery);
                $bugQuery = str_replace('`', '', $bugQuery);
            }
            $bugs = $this->dao->select('t1.id,t1.applicationID,t1.color,t1.branch,t1.confirmed,t1.status,t1.severity,t1.pri,t1.title,t1.product,t1.type,t1.childType,t1.openedBy,t1.assignedTo,t1.resolvedBy,t1.resolution,t2.id as defectId,t2.title as defectTitle')->from(TABLE_BUG)->alias("t1")
                ->leftJoin(TABLE_DEFECT)->alias('t2')->on('t1.id = t2.bugId')
                ->where($bugQuery)
                ->andWhere('t1.project')->eq((int)$projectID)
                ->beginIF(!empty($applicationID))->andWhere('t1.applicationID')->eq($applicationID)->fi()
                ->andWhere('t1.deleted')->eq(0)
                ->beginIF($excludeBugs)->andWhere('t1.id')->notIN($excludeBugs)->fi()
                ->orderBy($orderBy)
                ->page($pager)
                ->fetchAll();
        }
        else
        {
            $bugs = $this->dao->select('t1.id,t1.applicationID,t1.color,t1.branch,t1.confirmed,t1.status,t1.severity,t1.pri,t1.title,t1.product,t1.type,t1.childType,t1.openedBy,t1.assignedTo,t1.resolvedBy,t1.resolution,t2.id as defectId,t2.title as defectTitle')->from(TABLE_BUG)->alias("t1")
                ->leftJoin(TABLE_DEFECT)->alias('t2')->on('t1.id = t2.bugId')
                ->where('t1.deleted')->eq(0)
                ->andWhere('t1.project')->eq($projectID)
                ->beginIF(!empty($applicationID))->andWhere('t1.applicationID')->eq($applicationID)->fi()
                ->beginIF($productID != 'all')->andWhere('t1.product')->eq($productID)->fi()
                ->beginIF($type == 'unresolved')->andWhere('t1.status')->eq('active')->fi()
                ->beginIF($type == 'noclosed')->andWhere('t1.status')->ne('closed')->fi()
                ->beginIF($build)->andWhere("CONCAT(',', t1.openedBuild, ',') like '%,$build,%'")->fi()
                ->beginIF($taskID)->andWhere("CONCAT(',', t1.linkTesttask, ',') like '%,$taskID,%'")->fi()
                ->beginIF($excludeBugs)->andWhere('t1.id')->notIN($excludeBugs)->fi()
                ->orderBy($orderBy)
                ->page($pager)
                ->fetchAll();
        }

        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'bug');

        return $bugs;
    }

    /**
     * Get bugs of a project.
     *
     * @param  int    $projectID
     * @param  int    $applicationID
     * @param  int    $productID
     * @param  int    $build
     * @param  string $type
     * @param  int    $param
     * @param  string $orderBy
     * @param  string $excludeBugs
     * @param  object $pager
     * @access public
     * @return array
     */
    public function getProjectBugs($projectID,$applicationID, $productID = 0, $build = 0, $type = '', $param = 0, $orderBy = 'id_desc', $excludeBugs = '', $pager = null)
    {
        $type = strtolower($type);
        if($type == 'bysearch')
        {
            $queryID = (int)$param;
            if($this->session->projectBugQuery == false) $this->session->set('projectBugQuery', ' 1 = 1');
            if($queryID)
            {
                $query = $this->loadModel('search')->getQuery($queryID);
                if($query)
                {
                    $this->session->set('projectBugQuery', $query->sql);
                    $this->session->set('projectBugForm', $query->form);
                }
            }

            $allProduct = "`product` = 'all'";
            $bugQuery   = $this->session->projectBugQuery;
            if(strpos($this->session->projectBugQuery, $allProduct) !== false)
            {
                $bugQuery = str_replace($allProduct, '1', $this->session->projectBugQuery);
            }

            $bugs = $this->dao->select('*')->from(TABLE_BUG)
                ->where($bugQuery)
                ->andWhere('project')->eq((int)$projectID)
                ->beginIF(!empty($applicationID))->andWhere('applicationID')->eq($applicationID)->fi()
                ->andWhere('deleted')->eq(0)
                ->beginIF($excludeBugs)->andWhere('id')->notIN($excludeBugs)->fi()
                ->orderBy($orderBy)
                ->page($pager)
                ->fetchAll('id');
        }
        else
        {
            $bugs = $this->dao->select('*')->from(TABLE_BUG)
                ->where('deleted')->eq(0)
                ->andWhere('project')->eq($projectID)
                ->beginIF(!empty($applicationID))->andWhere('applicationID')->eq($applicationID)->fi()
                ->beginIF($productID != 'all')->andWhere('product')->eq($productID)->fi()
                ->beginIF($type == 'unresolved')->andWhere('status')->eq('active')->fi()
                ->beginIF($type == 'noclosed')->andWhere('status')->ne('closed')->fi()
                ->beginIF($build)->andWhere("CONCAT(',', openedBuild, ',') like '%,$build,%'")->fi()
                ->beginIF($excludeBugs)->andWhere('id')->notIN($excludeBugs)->fi()
                ->orderBy($orderBy)->page($pager)->fetchAll();
        }

        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'bug');

        return $bugs;
    }

    /**
     * Get bugs of a execution.
     *
     * @param  int    $executionID
     * @param  int    $productID
     * @param  int    $build
     * @param  string $type
     * @param  int    $param
     * @param  string $orderBy
     * @param  string $excludeBugs
     * @param  object $pager
     * @access public
     * @return array
     */
    public function getExecutionBugs($executionID, $productID = 0, $build = 0, $type = '', $param = 0, $orderBy = 'id_desc', $excludeBugs = '', $pager = null)
    {
        $type = strtolower($type);
        if($type == 'bysearch')
        {
            $queryID = (int)$param;
            if($this->session->executionBugQuery == false) $this->session->set('executionBugQuery', ' 1 = 1');
            if($queryID)
            {
                $query = $this->loadModel('search')->getQuery($queryID);
                if($query)
                {
                    $this->session->set('executionBugQuery', $query->sql);
                    $this->session->set('executionBugForm', $query->form);
                }
            }

            $allProduct = "`product` = 'all'";
            $bugQuery   = $this->session->executionBugQuery;
            if(strpos($this->session->executionBugQuery, $allProduct) !== false)
            {
                $bugQuery = str_replace($allProduct, '1', $this->session->executionBugQuery);
            }

            $bugs = $this->dao->select('*')->from(TABLE_BUG)
                ->where($bugQuery)
                ->andWhere('execution')->eq((int)$executionID)
                ->andWhere('deleted')->eq(0)
                ->beginIF($excludeBugs)->andWhere('id')->notIN($excludeBugs)->fi()
                ->orderBy($orderBy)
                ->page($pager)
                ->fetchAll('id');
        }
        else
        {
            $bugs = $this->dao->select('*')->from(TABLE_BUG)
                ->where('deleted')->eq(0)
                ->beginIF(empty($build))->andWhere('execution')->eq($executionID)->fi()
                ->beginIF(!empty($productID))->andWhere('product')->eq($productID)->fi()
                ->beginIF($type == 'unresolved')->andWhere('status')->eq('active')->fi()
                ->beginIF($type == 'noclosed')->andWhere('status')->ne('closed')->fi()
                ->beginIF($build)->andWhere("CONCAT(',', openedBuild, ',') like '%,$build,%'")->fi()
                ->beginIF($excludeBugs)->andWhere('id')->notIN($excludeBugs)->fi()
                ->orderBy($orderBy)->page($pager)->fetchAll();
        }

        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'bug');

        return $bugs;
    }

    /**
     * Get product left bugs.
     *
     * @param  int    $build
     * @param  int    $productID
     * @param  int    $branch
     * @param  string $linkedBugs
     * @param  object $pager
     * @access public
     * @return array
     */
    public function getProductLeftBugs($build, $productID, $branch = 0, $linkedBugs = '', $pager = null)
    {
        $build = $this->dao->select('*')->from(TABLE_BUILD)->where('id')->eq($build)->fetch();
        if(empty($build->execution)) return array();

        $execution    = $this->dao->select('*')->from(TABLE_EXECUTION)->where('id')->eq($build->execution)->fetch();
        $beforeBuilds = $this->dao->select('t1.id')->from(TABLE_BUILD)->alias('t1')
            ->leftJoin(TABLE_EXECUTION)->alias('t2')->on('t1.execution=t2.id')
            ->where('t1.product')->eq($productID)
            ->andWhere('t2.status')->ne('done')
            ->andWhere('t2.deleted')->eq(0)
            ->andWhere('t1.deleted')->eq(0)
            ->andWhere('t1.date')->lt($execution->begin)
            ->fetchPairs('id', 'id');

        $bugs = $this->dao->select('*')->from(TABLE_BUG)->where('deleted')->eq(0)
            ->andWhere('product')->eq($productID)
            ->andWhere('toStory')->eq(0)
            ->andWhere('openedDate')->ge($execution->begin)
            ->andWhere('openedDate')->le($execution->end)
            ->andWhere("(status = 'active' OR resolvedDate > '{$execution->end}')")
            ->andWhere('openedBuild')->notin($beforeBuilds)
            ->beginIF($linkedBugs)->andWhere('id')->notIN($linkedBugs)->fi()
            ->beginIF($branch)->andWhere('branch')->in("0,$branch")->fi()
            ->page($pager)
            ->fetchAll();

        return $bugs;
    }

    /**
     * get Product Bug Pairs
     *
     * @param  int    $productID
     * @access public
     * @return void
     */
    public function getProductBugPairs($productID)
    {
        $bugs = array('' => '');
        $data = $this->dao->select('id, title')->from(TABLE_BUG)
            ->where('product')->eq((int)$productID)
            ->beginIF(!$this->app->user->admin)->andWhere('execution')->in('0,' . $this->app->user->view->sprints)->fi()
            ->andWhere('deleted')->eq(0)
            ->orderBy('id desc')
            ->fetchAll();
        foreach($data as $bug)
        {
            $bugs[$bug->id] = $bug->id . ':' . $bug->title;
        }
        return $bugs;
    }

    public function getProductMemberPairs($productID)
    {
        $projects = $this->loadModel('product')->getProjectPairsByProduct($productID);

        $users = $this->dao->select("t2.id, t2.account, t2.realname")->from(TABLE_TEAM)->alias('t1')
            ->leftJoin(TABLE_USER)->alias('t2')->on('t1.account = t2.account')
            ->where('t1.root')->in(array_keys($projects))
            ->andWhere('t1.type')->eq('project')
            ->andWhere('t2.deleted')->eq(0)
            ->fi()
            ->fetchAll('account');

        if(!$users) return array('' => '');

        foreach($users as $account => $user)
        {
            $firstLetter = ucfirst(substr($user->account, 0, 1)) . ':';
            if(!empty($this->config->isINT)) $firstLetter = '';
            $users[$account] =  $firstLetter . ($user->realname ? $user->realname : $user->account);
        }
        return array('' => '') + $users;
    }

    /**
     * Get bugs according to buildID and productID.
     *
     * @param  int    $buildID
     * @param  int    $productID
     * @access public
     * @return object
     */
    public function getReleaseBugs($buildID, $productID, $branch = 0, $linkedBugs = '', $pager = null)
    {
        $execution = $this->dao->select('t1.id,t1.begin')->from(TABLE_EXECUTION)->alias('t1')
            ->leftJoin(TABLE_BUILD)->alias('t2')->on('t1.id = t2.execution')
            ->where('t2.id')->eq($buildID)
            ->fetch();
        $bugs = $this->dao->select('*')->from(TABLE_BUG)
            ->where('resolvedDate')->ge($execution->begin)
            ->andWhere('resolution')->ne('postponed')
            ->andWhere('product')->eq($productID)
            ->beginIF($linkedBugs)->andWhere('id')->notIN($linkedBugs)->fi()
            ->beginIF($branch)->andWhere('branch')->in("0,$branch")->fi()
            ->andWhere("(execution != '$execution->id' OR (execution = '$execution->id' and openedDate < '$execution->begin'))")
            ->andWhere('deleted')->eq(0)
            ->orderBy('openedDate ASC')
            ->page($pager)
            ->fetchAll();
        return $bugs;
    }

    /**
     * Get bugs of a story.
     *
     * @param  int    $storyID
     * @param  int    $executionID
     * @access public
     * @return array
     */
    public function getStoryBugs($storyID, $executionID = 0)
    {
        return $this->dao->select('id, title, pri, type, status, assignedTo, resolvedBy, resolution')
            ->from(TABLE_BUG)
            ->where('story')->eq((int)$storyID)
            ->beginIF($executionID)->andWhere('execution')->eq($executionID)->fi()
            ->andWhere('deleted')->eq(0)
            ->fetchAll('id');
    }

    /**
     * Get case bugs.
     *
     * @param  int    $runID
     * @param  int    $caseID
     * @param  int    $version
     * @access public
     * @return void
     */
    public function getCaseBugs($runID, $caseID = 0, $version = 0)
    {
        return $this->dao->select('*')->from(TABLE_BUG)
            ->where('1=1')
            ->beginIF($runID)->andWhere('`result`')->eq($runID)->fi()
            ->beginIF($runID == 0 and $caseID)->andWhere('`case`')->eq($caseID)->fi()
            ->beginIF($version)->andWhere('`caseVersion`')->eq($version)->fi()
            ->andWhere('deleted')->eq(0)
            ->fetchAll('id');
    }

    /**
     * Get counts of some stories' bugs.
     *
     * @param  array  $stories
     * @param  int    $executionID
     * @access public
     * @return int
     */
    public function getStoryBugCounts($stories, $executionID = 0)
    {
        $bugCounts = $this->dao->select('story, COUNT(*) AS bugs')
            ->from(TABLE_BUG)
            ->where('story')->in($stories)
            ->andWhere('deleted')->eq(0)
            ->beginIF($executionID)->andWhere('execution')->eq($executionID)->fi()
            ->groupBy('story')
            ->fetchPairs();
        foreach($stories as $storyID) if(!isset($bugCounts[$storyID])) $bugCounts[$storyID] = 0;
        return $bugCounts;
    }

    /**
     * Get bug info from a result.
     *
     * @param  int    $resultID
     * @param  int    $caseID
     * @param  int    $version
     * @access public
     * @return array
     */
    public function getBugInfoFromResult($resultID, $caseID = 0, $version = 0, $stepIdList = '')
    {
        $title       = '';
        $bugSteps    = '';
        $steps       = explode('_', trim($stepIdList, '_'));
        $projectID   = '';
        $executionID = '';

        $result = $this->dao->findById($resultID)->from(TABLE_TESTRESULT)->fetch();
        if($caseID > 0)
        {
            $run = new stdclass();
            $run->case = $this->loadModel('testcase')->getById($caseID, $result->version);
        }
        else
        {
            $run = $this->loadModel('testtask')->getRunById($result->run);
        }

        $projectID   = $run->case->project;
        $executionID = $run->case->execution;
        $title       = $run->case->title;
        $caseSteps   = $run->case->steps;
        $stepResults = unserialize($result->stepResults);
        if($run->case->precondition != '')
        {
            $bugSteps = "<p>[" . $this->lang->testcase->precondition . "]</p>" . "\n" . $run->case->precondition;
        }
        if(!empty($stepResults))
        {
            $i = 1;
            $bugStep = '';
            $bugResult = '';
            $bugExpect = '';
            $bugFile = '';
            $bugFrequency = '';
            $bugEnvironment = '';
            $bugData = '';
            foreach($steps as $stepId)
            {
                if(!isset($caseSteps[$stepId])) continue;
                $step = $caseSteps[$stepId];

                $stepResult = (!isset($stepResults[$stepId]) or empty($stepResults[$stepId]['real'])) ? '' : $stepResults[$stepId]['real'];
                $bugStep   .= $i . '. ' . $step->desc . "<br />";
                $bugResult .= $i . '. ' . $stepResult . "<br />";
                $bugExpect .= $i . '. ' . $step->expect . "<br />";

                $i++;
            }

            $bugSteps .= $bugStep   ? str_replace('<br/>', '', $this->lang->bug->tplStep)   . $bugStep   : $this->lang->bug->tplStep;
            $bugSteps .= $bugResult ? str_replace('<br/>', '', $this->lang->bug->tplResult) . $bugResult : $this->lang->bug->tplResult;
            $bugSteps .= $bugExpect ? str_replace('<br/>', '', $this->lang->bug->tplExpect) . $bugExpect : $this->lang->bug->tplExpect;

            $bugSteps .= $bugFile ? str_replace('<br/>', '', $this->lang->bug->tplFile) . $bugFile : $this->lang->bug->tplFile;
            $bugSteps .= $bugFrequency ? str_replace('<br/>', '', $this->lang->bug->tplFrequency) . $bugFrequency : $this->lang->bug->tplFrequency;
            $bugSteps .= $bugEnvironment ? str_replace('<br/>', '', $this->lang->bug->tplEnvironment) . $bugEnvironment : $this->lang->bug->tplEnvironment;
            $bugSteps .= $bugData ? str_replace('<br/>', '', $this->lang->bug->tplData) . $bugData : $this->lang->bug->tplData;

        }
        else
        {
            $bugSteps .= $this->lang->bug->tplStep;
            $bugSteps .= $this->lang->bug->tplResult;
            $bugSteps .= $this->lang->bug->tplExpect;
            $bugSteps .= $this->lang->bug->tplFile;
            $bugSteps .= $this->lang->bug->tplFrequency;
            $bugSteps .= $this->lang->bug->tplEnvironment;
            $bugSteps .= $this->lang->bug->tplData;
        }

        return array('projectID' => $projectID, 'executionID' => $executionID, 'title' => $title, 'steps' => $bugSteps, 'storyID' => $run->case->story, 'moduleID' => $run->case->module, 'version' => $run->case->version);
    }

    /**
     * Get report data of bugs per execution.
     *
     * @access public
     * @return array
     */
    public function getDataOfBugsPerExecution()
    {
        $datas = $this->dao->select('execution as name, count(execution) as value')->from(TABLE_BUG)->where($this->reportCondition())->groupBy('execution')->orderBy('value DESC')->fetchAll('name');
        if(!$datas) return array();
        $executions = $this->loadModel('execution')->getPairs($this->session->project);

        $maxLength = 12;
        if(common::checkNotCN()) $maxLength = 22;
        foreach($datas as $executionID => $data)
        {
            $data->name  = isset($executions[$executionID]) ? $executions[$executionID] : $this->lang->report->undefined;
            $data->title = $data->name;
            if(mb_strlen($data->name) > $maxLength) $data->name = mb_substr($data->name, 0, $maxLength) . '...';
        }
        return $datas;
    }

    /**
     * Get report data of bugs per build.
     *
     * @access public
     * @return void
     */
    public function getDataOfBugsPerBuild()
    {
        $datas = $this->dao->select('openedBuild as name, count(openedBuild) as value')->from(TABLE_BUG)->where($this->reportCondition())->groupBy('openedBuild')->orderBy('value DESC')->fetchAll('name');
        if(!$datas) return array();
        /* Judge if all product or not. */
        $products = $this->session->product;
        preg_match('/`product` IN \((?P<productIdList>.+)\)/', $this->reportCondition(), $matchs);
        if(!empty($matchs) and isset($matchs['productIdList'])) $products = str_replace('\'', '', $matchs['productIdList']);
        $builds = $this->loadModel('build')->getProductBuildPairs($products, $branch = 0, $params = '');

        /* Deal with the situation that a bug maybe associate more than one openedBuild. */
        foreach($datas as $buildIDList => $data)
        {
            $openBuildIDList = explode(',', $buildIDList);
            if(count($openBuildIDList) > 1)
            {
                foreach($openBuildIDList as $buildID)
                {
                    if(isset($datas[$buildID]))
                    {
                        $datas[$buildID]->value += $data->value;
                    }
                    else
                    {
                        if(!isset($datas[$buildID])) $datas[$buildID] = new stdclass();
                        $datas[$buildID]->name  = $buildID;
                        $datas[$buildID]->value = $data->value;
                    }
                }
                unset($datas[$buildIDList]);
            }
        }

        foreach($datas as $buildID => $data)
        {
            $data->name = isset($builds[$buildID]) ? $builds[$buildID] : $this->lang->report->undefined;
        }
        ksort($datas);
        return $datas;
    }

    /**
     * Get report data of bugs per module
     *
     * @access public
     * @return array
     */
    public function getDataOfBugsPerModule()
    {
        $datas = $this->dao->select('module as name, count(module) as value')->from(TABLE_BUG)->where($this->reportCondition())->groupBy('module')->orderBy('value DESC')->fetchAll('name');
        if(!$datas) return array();
        $modules = $this->loadModel('tree')->getModulesName(array_keys($datas), true, true);
        foreach($datas as $moduleID => $data) $data->name = isset($modules[$moduleID]) ? $modules[$moduleID] : '/';
        return $datas;
    }

    /**
     * Get report data of opened bugs per day.
     *
     * @access public
     * @return array
     */
    public function getDataOfOpenedBugsPerDay()
    {
        return $this->dao->select('DATE_FORMAT(openedDate, "%Y-%m-%d") AS name, COUNT(*) AS value')->from(TABLE_BUG)->where($this->reportCondition())->groupBy('name')->orderBy('openedDate')->fetchAll();
    }

    /**
     * Get report data of resolved bugs per day.
     *
     * @access public
     * @return array
     */
    public function getDataOfResolvedBugsPerDay()
    {
        return $this->dao->select('DATE_FORMAT(resolvedDate, "%Y-%m-%d") AS name, COUNT(*) AS value')->from(TABLE_BUG)
            ->where($this->reportCondition())->groupBy('name')
            ->having('name != 0000-00-00')
            ->orderBy('resolvedDate')
            ->fetchAll();
    }

    /**
     * Get report data of closed bugs per day.
     *
     * @access public
     * @return array
     */
    public function getDataOfClosedBugsPerDay()
    {
        return $this->dao->select('DATE_FORMAT(closedDate, "%Y-%m-%d") AS name, COUNT(*) AS value')->from(TABLE_BUG)
            ->where($this->reportCondition())->groupBy('name')
            ->having('name != 0000-00-00')
            ->orderBy('closedDate')->fetchAll();
    }

    /**
     * Get report data of openeded bugs per user.
     *
     * @access public
     * @return array
     */
    public function getDataOfOpenedBugsPerUser()
    {
        $datas = $this->dao->select('openedBy AS name, COUNT(*) AS value')->from(TABLE_BUG)->where($this->reportCondition())->groupBy('name')->orderBy('value DESC')->fetchAll('name');
        if(!$datas) return array();
        if(!isset($this->users)) $this->users = $this->loadModel('user')->getPairs('noletter');
        foreach($datas as $account => $data) if(isset($this->users[$account])) $data->name = $this->users[$account];
        return $datas;
    }

    /**
     * Get report data of resolved bugs per user.
     *
     * @access public
     * @return array
     */
    public function getDataOfResolvedBugsPerUser()
    {
        $datas = $this->dao->select('resolvedBy AS name, COUNT(*) AS value')
            ->from(TABLE_BUG)->where($this->reportCondition())
            ->andWhere('resolvedBy')->ne('')
            ->groupBy('name')
            ->orderBy('value DESC')->fetchAll('name');
        if(!$datas) return array();
        if(!isset($this->users)) $this->users = $this->loadModel('user')->getPairs('noletter');
        foreach($datas as $account => $data) if(isset($this->users[$account])) $data->name = $this->users[$account];
        return $datas;
    }

    /**
     * Get report data of closed bugs per user.
     *
     * @access public
     * @return array
     */
    public function getDataOfClosedBugsPerUser()
    {
        $datas = $this->dao->select('closedBy AS name, COUNT(*) AS value')
            ->from(TABLE_BUG)
            ->where($this->reportCondition())
            ->andWhere('closedBy')->ne('')
            ->groupBy('name')
            ->orderBy('value DESC')->fetchAll('name');
        if(!$datas) return array();
        if(!isset($this->users)) $this->users = $this->loadModel('user')->getPairs('noletter');
        foreach($datas as $account => $data) if(isset($this->users[$account])) $data->name = $this->users[$account];
        return $datas;
    }

    /**
     * Get report data of bugs per severity.
     *
     * @access public
     * @return array
     */
    public function getDataOfBugsPerSeverity()
    {
        $datas = $this->dao->select('severity AS name, COUNT(*) AS value')->from(TABLE_BUG)->where($this->reportCondition())->groupBy('name')->orderBy('value DESC')->fetchAll('name');
        if(!$datas) return array();
        foreach($datas as $severity => $data) if(isset($this->lang->bug->severityList[$severity])) $data->name = $this->lang->bug->severityList[$severity];
        return $datas;
    }

    /**
     * Get report data of bugs per resolution.
     *
     * @access public
     * @return array
     */
    public function getDataOfBugsPerResolution()
    {
        $datas = $this->dao->select('resolution AS name, COUNT(*) AS value')
            ->from(TABLE_BUG)
            ->where($this->reportCondition())
            ->andWhere('resolution')->ne('')
            ->groupBy('name')->orderBy('value DESC')
            ->fetchAll('name');
        if(!$datas) return array();
        foreach($datas as $resolution => $data) if(isset($this->lang->bug->resolutionList[$resolution])) $data->name = $this->lang->bug->resolutionList[$resolution];
        return $datas;
    }

    /**
     * Get report data of bugs per status.
     *
     * @access public
     * @return array
     */
    public function getDataOfBugsPerStatus()
    {
        $datas = $this->dao->select('status AS name, COUNT(*) AS value')->from(TABLE_BUG)->where($this->reportCondition())->groupBy('name')->orderBy('value DESC')->fetchAll('name');
        if(!$datas) return array();
        foreach($datas as $status => $data) if(isset($this->lang->bug->statusList[$status])) $data->name = $this->lang->bug->statusList[$status];
        return $datas;
    }

    /**
     * Get report data of bugs per pri
     *
     * @access public
     * @return array
     */
    public function getDataOfBugsPerPri()
    {
        $datas = $this->dao->select('pri AS name, COUNT(*) AS value')->from(TABLE_BUG)->where($this->reportCondition())->groupBy('name')->orderBy('value DESC')->fetchAll('name');
        if(!$datas) return array();
        foreach($datas as $status => $data) $data->name = $this->lang->bug->report->bugsPerPri->graph->xAxisName . ':' . zget($this->lang->bug->priList, $data->name);
        return $datas;
    }

    /**
     * Get report data of bugs per status.
     *
     * @access public
     * @return array
     */
    public function getDataOfBugsPerActivatedCount()
    {
        $datas = $this->dao->select('activatedCount AS name, COUNT(*) AS value')->from(TABLE_BUG)->where($this->reportCondition())->groupBy('name')->orderBy('value DESC')->fetchAll('name');
        if(!$datas) return array();
        foreach($datas as $data) $data->name = $this->lang->bug->report->bugsPerActivatedCount->graph->xAxisName . ':' . $data->name;
        return $datas;
    }

    /**
     * Get report data of bugs per type.
     *
     * @access public
     * @return array
     */
    public function getDataOfBugsPerType()
    {
        $datas = $this->dao->select('type AS name, COUNT(*) AS value')->from(TABLE_BUG)->where($this->reportCondition())->groupBy('name')->orderBy('value DESC')->fetchAll('name');
        if(!$datas) return array();
        foreach($datas as $type => $data) if(isset($this->lang->bug->typeList[$type])) $data->name = $this->lang->bug->typeList[$type];
        return $datas;
    }

    /**
     * getDataOfBugsPerAssignedTo
     *
     * @access public
     * @return void
     */
    public function getDataOfBugsPerAssignedTo()
    {
        $datas = $this->dao->select('assignedTo AS name, COUNT(*) AS value')
            ->from(TABLE_BUG)->where($this->reportCondition())
            ->groupBy('name')
            ->orderBy('value DESC')->fetchAll('name');
        if(!$datas) return array();
        if(!isset($this->users)) $this->users = $this->loadModel('user')->getPairs('noletter');
        foreach($datas as $account => $data) if(isset($this->users[$account])) $data->name = $this->users[$account];
        return $datas;
    }

    /**
     * Merge the default chart settings and the settings of current chart.
     *
     * @param  string    $chartType
     * @access public
     * @return void
     */
    public function mergeChartOption($chartType)
    {
        $chartOption  = $this->lang->bug->report->$chartType;
        $commonOption = $this->lang->bug->report->options;

        $chartOption->graph->caption = $this->lang->bug->report->charts[$chartType];
        if(!isset($chartOption->type))    $chartOption->type    = $commonOption->type;
        if(!isset($chartOption->width))  $chartOption->width  = $commonOption->width;
        if(!isset($chartOption->height)) $chartOption->height = $commonOption->height;

        /* 合并配置。*/
        foreach($commonOption->graph as $key => $value) if(!isset($chartOption->graph->$key)) $chartOption->graph->$key = $value;
    }

    /**
     * Return the file => label pairs of some fields.
     *
     * @param  string    $fields
     * @access public
     * @return array
     */
    public function getFieldPairs($fields)
    {
        $fields = explode(',', $fields);
        foreach($fields as $key => $field)
        {
            $field = trim($field);
            $fields[$field] = $this->lang->bug->$field;
            unset($fields[$key]);
        }
        return $fields;
    }

    /**
     * Get all bugs.
     *
     * @param  array  $productIDList
     * @param  int    $branch
     * @param  array  $modules
     * @param  string $orderBy
     * @param  object $pager
     * @param  int    $projectID
     * @access public
     * @return array
     */
    public function getAllBugs($productIDList, $branch, $modules, $orderBy, $pager = null, $projectID, $applicationID)
    {
        $bugs = $this->dao->select('t1.*, t2.title as planTitle')->from(TABLE_BUG)->alias('t1')
            ->leftJoin(TABLE_PRODUCTPLAN)->alias('t2')->on('t1.plan = t2.id')
            ->where('t1.product')->in($productIDList)
            ->beginIF($branch)->andWhere('t1.branch')->eq($branch)->fi()
            ->beginIF($modules)->andWhere('t1.module')->in($modules)->fi()
            ->beginIF($projectID)->andWhere('t1.project')->eq($projectID)->fi()
            ->beginIF($applicationID)->andWhere('t1.applicationID')->eq($applicationID)->fi()
            ->andWhere('t1.deleted')->eq(0)
            ->orderBy($orderBy)->page($pager)->fetchAll();

        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'bug');

        return $bugs;
    }

    /**
     * Get bugs of assign to me.
     *
     * @param  array  $productIDList
     * @param  int    $branch
     * @param  array  $modules
     * @param  string $orderBy
     * @param  object $pager
     * @param  int    $projectID
     * @access public
     * @return array
     */
    public function getByAssigntome($productIDList, $branch, $modules, $orderBy, $pager, $projectID, $applicationID)
    {
        return $this->dao->findByAssignedTo($this->app->user->account)->from(TABLE_BUG)
            ->andWhere('product')->in($productIDList)
            ->beginIF($branch)->andWhere('branch')->in($branch)->fi()
            ->beginIF($modules)->andWhere('module')->in($modules)->fi()
            ->beginIF($projectID)->andWhere('project')->eq($projectID)->fi()
            ->beginIF($applicationID)->andWhere('applicationID')->eq($applicationID)->fi()
            ->andWhere('deleted')->eq(0)
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll();
    }

    /**
     * Get bugs of opened by me.
     *
     * @param  array  $productIDList
     * @param  int    $branch
     * @param  array  $modules
     * @param  string $orderBy
     * @param  object $pager
     * @param  int    $projectID
     * @access public
     * @return array
     */
    public function getByOpenedbyme($productIDList, $branch, $modules, $orderBy, $pager, $projectID, $applicationID)
    {
        return $this->dao->findByOpenedBy($this->app->user->account)->from(TABLE_BUG)
            ->andWhere('product')->in($productIDList)
            ->beginIF($branch)->andWhere('branch')->in($branch)->fi()
            ->beginIF($modules)->andWhere('module')->in($modules)->fi()
            ->beginIF($projectID)->andWhere('project')->eq($projectID)->fi()
            ->beginIF($applicationID)->andWhere('applicationID')->eq($applicationID)->fi()
            ->andWhere('deleted')->eq(0)
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll();
    }

    /**
     * Get bugs of resolved by me.
     *
     * @param  array  $productIDList
     * @param  int    $branch
     * @param  array  $modules
     * @param  string $orderBy
     * @param  object $pager
     * @param  int    $projectID
     * @access public
     * @return array
     */
    public function getByResolvedbyme($productIDList, $branch, $modules, $orderBy, $pager, $projectID, $applicationID)
    {
        return $this->dao->findByResolvedBy($this->app->user->account)->from(TABLE_BUG)
            ->andWhere('product')->in($productIDList)
            ->beginIF($branch)->andWhere('branch')->in($branch)->fi()
            ->beginIF($modules)->andWhere('module')->in($modules)->fi()
            ->beginIF($projectID)->andWhere('project')->eq($projectID)->fi()
            ->beginIF($applicationID)->andWhere('applicationID')->eq($applicationID)->fi()
            ->andWhere('deleted')->eq(0)
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll();
    }

    /**
     * Get bugs of nobody to do.
     *
     * @param  array  $productIDList
     * @param  int    $branch
     * @param  array  $modules
     * @param  string $orderBy
     * @param  object $pager
     * @param  int    $projectID
     * @access public
     * @return array
     */
    public function getByAssigntonull($productIDList, $branch, $modules, $orderBy, $pager, $projectID, $applicationID)
    {
        return $this->dao->findByAssignedTo('')->from(TABLE_BUG)
            ->andWhere('product')->in($productIDList)
            ->beginIF($branch)->andWhere('branch')->in($branch)->fi()
            ->beginIF($modules)->andWhere('module')->in($modules)->fi()
            ->beginIF($projectID)->andWhere('project')->eq($projectID)->fi()
            ->beginIF($applicationID)->andWhere('applicationID')->eq($applicationID)->fi()
            ->andWhere('deleted')->eq(0)
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll();
    }

    /**
     * Get unconfirmed bugs.
     *
     * @param  array  $productIDList
     * @param  int    $branch
     * @param  array  $modules
     * @param  string $orderBy
     * @param  object $pager
     * @param  int    $projectID
     * @access public
     * @return void
     */
    public function getUnconfirmed($productIDList, $branch, $modules, $orderBy, $pager, $projectID, $applicationID)
    {
        return $this->dao->select('*')->from(TABLE_BUG)
            ->where('confirmed')->eq(0)
            ->andWhere('product')->in($productIDList)
            ->beginIF($branch)->andWhere('branch')->in($branch)->fi()
            ->beginIF($modules)->andWhere('module')->in($modules)->fi()
            ->beginIF($projectID)->andWhere('project')->eq($projectID)->fi()
            ->beginIF($applicationID)->andWhere('applicationID')->eq($applicationID)->fi()
            ->andWhere('deleted')->eq(0)
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll();
    }

    /**
     * Get bugs the status is active or unclosed.
     *
     * @param  array  $productIDList
     * @param  int    $branch
     * @param  array  $modules
     * @param  string $status
     * @param  string $orderBy
     * @param  object $pager
     * @param  int    $projectID
     * @access public
     * @return array
     */
    public function getByStatus($productIDList, $branch, $modules, $status, $orderBy, $pager, $projectID, $applicationID)
    {
        return $this->dao->select('*')->from(TABLE_BUG)
            ->where('product')->in($productIDList)
            ->beginIF($branch)->andWhere('branch')->in($branch)->fi()
            ->beginIF($modules)->andWhere('module')->in($modules)->fi()
            ->beginIF($status == 'unclosed')->andWhere('status')->ne('closed')->fi()
            ->beginIF($status == 'unresolved')->andWhere('status')->eq('active')->fi()
            ->beginIF($status == 'toclosed')->andWhere('status')->eq('resolved')->fi()
            ->beginIF($projectID)->andWhere('project')->eq($projectID)->fi()
            ->beginIF($applicationID)->andWhere('applicationID')->eq($applicationID)->fi()
            ->andWhere('deleted')->eq(0)
            ->orderBy($orderBy)->page($pager)
            ->fetchAll();
    }

    /**
     * Get unclosed bugs for long time.
     *
     * @param  array  $productIDList
     * @param  int    $branch
     * @param  array  $modules
     * @param  string $orderBy
     * @param  object $pager
     * @param  int    $projectID
     * @access public
     * @return array
     */
    public function getByLonglifebugs($productIDList, $branch, $modules, $orderBy, $pager, $projectID,$applicationID)
    {
        $lastEditedDate = date(DT_DATE1, time() - $this->config->bug->longlife * 24 * 3600);
        return $this->dao->findByLastEditedDate("<", $lastEditedDate)->from(TABLE_BUG)
            ->andWhere('product')->in($productIDList)
            ->beginIF($branch)->andWhere('branch')->in($branch)->fi()
            ->beginIF($modules)->andWhere('module')->in($modules)->fi()
            ->beginIF($projectID)->andWhere('project')->eq($projectID)->fi()
            ->beginIF($applicationID)->andWhere('applicationID')->eq($applicationID)->fi()
            ->andWhere('openedDate')->lt($lastEditedDate)
            ->andWhere('deleted')->eq(0)
            ->andWhere('status')->ne('closed')
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll();
    }

    /**
     * Get postponed bugs.
     *
     * @param  array  $productIDList
     * @param  int    $branch
     * @param  array  $modules
     * @param  string $orderBy
     * @param  object $pager
     * @param  int    $projectID
     * @access public
     * @return array
     */
    public function getByPostponedbugs($productIDList, $branch, $modules, $orderBy, $pager, $projectID, $applicationID)
    {
        return $this->dao->findByResolution('postponed')->from(TABLE_BUG)
            ->andWhere('product')->in($productIDList)
            ->beginIF($branch)->andWhere('branch')->in($branch)->fi()
            ->beginIF($modules)->andWhere('module')->in($modules)->fi()
            ->beginIF($projectID)->andWhere('project')->eq($projectID)->fi()
            ->beginIF($applicationID)->andWhere('applicationID')->eq($applicationID)->fi()
            ->andWhere('deleted')->eq(0)
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll();
    }

    /**
     * Get bugs need confirm.
     *
     * @param  array  $productIDList
     * @param  int    $branch
     * @param  array  $modules
     * @param  string $orderBy
     * @param  object $pager
     * @param  int    $projectID
     * @access public
     * @return array
     */
    public function getByNeedconfirm($productIDList, $branch, $modules, $orderBy, $pager, $projectID, $applicationID)
    {
        return $this->dao->select('t1.*, t2.title AS storyTitle')->from(TABLE_BUG)->alias('t1')
            ->leftJoin(TABLE_STORY)->alias('t2')->on('t1.story = t2.id')
            ->where("t2.status = 'active'")
            ->andWhere('t1.product')->in($productIDList)
            ->beginIF($branch)->andWhere('t1.branch')->in($branch)->fi()
            ->beginIF($modules)->andWhere('t1.module')->in($modules)->fi()
            ->beginIF($projectID)->andWhere('t1.project')->eq($projectID)->fi()
            ->beginIF($applicationID)->andWhere('t1.applicationID')->eq($applicationID)->fi()
            ->andWhere('t2.version > t1.storyVersion')
            ->andWhere('t1.deleted')->eq(0)
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll();
    }

    /**
     * Get bugs by search.
     *
     * @param  array  $productIDList
     * @param  int    $branch
     * @param  int    $queryID
     * @param  string $orderBy
     * @param  string $excludeBugs
     * @param  object $pager
     * @param  int    $projectID
     * @access public
     * @return array
     */
    public function getBySearch($productIdList, $branch = 0, $queryID, $orderBy, $excludeBugs = '', $pager = null, $projectID = 0, $applicationID = 0)
    {
        if($queryID)
        {
            $query = $this->loadModel('search')->getQuery($queryID);
            if($query)
            {
                $this->session->set('bugQuery', $query->sql);
                $this->session->set('bugForm', $query->form);
            }
            else
            {
                $this->session->set('bugQuery', ' 1 = 1');
            }
        }
        else
        {
            if($this->session->bugQuery == false) $this->session->set('bugQuery', ' 1 = 1');
        }

        $bugQuery = $this->session->bugQuery;

        /* 搜索添加中所属产品等于NA产品时额外处理。 */
        if(strpos($bugQuery,"`product` = 'na'") !== false)
        {
            $bugQuery = str_replace("`product` = 'na'", '1', $bugQuery);
            $bugQuery = $bugQuery . " AND `product` = '0'";
        }

        /* 搜索条件中不包含所属产品这个搜索条件时。 */
        if(is_array($productIdList)) $productIdList = implode(',', $productIdList);
        if(strpos($bugQuery, '`product` =') === false) $bugQuery .= ' AND `product` in (' . $productIdList . ')';

        /* 搜索条件中包含所属产品等于全部产品这个条件时。 */
        $allProduct = "`product` = 'all'";
        if(strpos($bugQuery, $allProduct) !== false)
        {
            $products = $this->loadModel('rebirth')->getAllProductIdList($applicationID);
            $products = implode(',', $products);
            $bugQuery = str_replace($allProduct, '1', $bugQuery);
            $bugQuery = $bugQuery . ' AND `product` ' . helper::dbIN($products);
        }

        /* Fix bug #2878. */
        if(strpos($bugQuery, ' `resolvedDate` ') !== false) $bugQuery = str_replace(' `resolvedDate` ', " `resolvedDate` != '0000-00-00 00:00:00' AND `resolvedDate` ", $bugQuery);
        if(strpos($bugQuery, ' `closedDate` ') !== false)   $bugQuery = str_replace(' `closedDate` ', " `closedDate` != '0000-00-00 00:00:00' AND `closedDate` ", $bugQuery);

        $bugs = $this->dao->select('*')->from(TABLE_BUG)->where($bugQuery)
            ->beginIF($applicationID)->andWhere('applicationID')->eq($applicationID)->fi()
            ->beginIF($projectID)->andWhere('project')->eq($projectID)->fi()
            ->beginIF($excludeBugs)->andWhere('id')->notIN($excludeBugs)->fi()
            ->andWhere('deleted')->eq(0)
            ->orderBy($orderBy)->page($pager)->fetchAll();
        return $bugs;
    }

    /**
     * Get bugs the overdueBugs is active or unclosed.
     *
     * @param  array  $productIDList
     * @param  int    $branch
     * @param  array  $modules
     * @param  string $status
     * @param  string $orderBy
     * @param  object $pager
     * @param  int    $projectID
     * @access public
     * @return array
     */
    public function getOverdueBugs($productIDList, $branch, $modules, $orderBy, $pager, $projectID, $applicationID)
    {
        return $this->dao->select('*')->from(TABLE_BUG)
            ->where('product')->in($productIDList)
            ->beginIF($branch)->andWhere('branch')->in($branch)->fi()
            ->beginIF($modules)->andWhere('module')->in($modules)->fi()
            ->beginIF($projectID)->andWhere('project')->eq($projectID)->fi()
            ->beginIF($applicationID)->andWhere('applicationID')->eq($applicationID)->fi()
            ->andWhere('status')->eq('active')
            ->andWhere('deleted')->eq(0)
            ->andWhere('deadline')->ne('0000-00-00')
            ->andWhere('deadline')->lt(helper::today())
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll();
    }


    /**
     * 搜索增加关联缺陷id
     *
     * @param  array  $productIDList
     * @param  int    $branch
     * @param  int    $queryID
     * @param  string $orderBy
     * @param  string $excludeBugs
     * @param  object $pager
     * @param  int    $projectID
     * @access public
     * @return array
     */
    public function getBySearchAboutDefect($productIdList, $branch = 0, $queryID, $orderBy, $excludeBugs = '', $pager = null, $projectID = 0, $applicationID = 0)
    {
        $orderByList = explode(',', $orderBy);
        foreach ($orderByList as &$orderByItem) $orderByItem = 't1.' . $orderByItem;
        $orderBy = implode(',', $orderByList);

        $bugQueryKey = 'bugQuery';
        $bugFormKey  = 'bugForm';
        if($this->app->openApp == 'project') $bugQueryKey = 'projectBugQuery';
        if($this->app->openApp == 'project') $bugFormKey  = 'projectBugForm';

        if($queryID)
        {
            $query = $this->loadModel('search')->getQuery($queryID);
            if($query)
            {
                $this->session->set($bugQueryKey, $query->sql);
                $this->session->set($bugFormKey,  $query->form);
            }
            else
            {
                $this->session->set($bugQueryKey, ' 1 = 1');
            }
        }
        else
        {
            if($this->session->{$bugQueryKey} == false) $this->session->set($bugQueryKey, ' 1 = 1');
        }

        $bugQuery   = $this->session->{$bugQueryKey};



        /* 搜索添加中所属产品等于NA产品时额外处理。 */
        if(strpos($bugQuery,"`product` = 'na'") !== false)
        {
            $bugQuery = str_replace("`product` = 'na'", '1', $bugQuery);
            $bugQuery = $bugQuery . " AND `product` = '0'";
        }

        /* 搜索条件中不包含所属产品这个搜索条件时。 */
        if(is_array($productIdList)) $productIdList = implode(',', $productIdList);
        if(strpos($bugQuery, '`product` =') === false) $bugQuery .= ' AND `product` ' . helper::dbIN($productIdList);

        /* 搜索条件中包含所属产品等于全部产品这个条件时。 */
        $allProduct = "`product` = 'all'";
        if(strpos($bugQuery, $allProduct) !== false)
        {
            $products = $this->loadModel('rebirth')->getAllProductIdList($applicationID);
            $products = implode(',', $products);
            $bugQuery = str_replace($allProduct, '1', $bugQuery);
            $bugQuery = $bugQuery . ' AND `product` ' . helper::dbIN($products);
        }
 
        $allBranch = "`branch` = 'all'";
        if($branch and strpos($bugQuery, '`branch` =') === false) $bugQuery .= " AND `branch` in('0','$branch')";
        if(strpos($bugQuery, $allBranch) !== false) $bugQuery = str_replace($allBranch, '1', $bugQuery);

        /* Fix bug #2878. */
        if(strpos($bugQuery, ' `resolvedDate` ') !== false) $bugQuery = str_replace(' `resolvedDate` ', " `resolvedDate` != '0000-00-00 00:00:00' AND `resolvedDate` ", $bugQuery);
        if(strpos($bugQuery, ' `closedDate` ') !== false)   $bugQuery = str_replace(' `closedDate` ', " `closedDate` != '0000-00-00 00:00:00' AND `closedDate` ", $bugQuery);

        if (strpos($bugQuery, 'linkDefect')) {
            $bugQuery = str_replace('linkDefect', 't2.code', $bugQuery);
            $bugQuery = str_replace('product', 't1.product', $bugQuery);
            $bugQuery = str_replace('`', '', $bugQuery);
        }else{
            $bugQuery = str_replace('AND (`', ' AND (`t1.', $bugQuery);
            $bugQuery = str_replace('AND `', ' AND `t1.', $bugQuery);
            $bugQuery = str_replace('OR (`', ' OR (`t1.', $bugQuery);
            $bugQuery = str_replace('OR `', ' OR `t1.', $bugQuery);
            $bugQuery = str_replace('`', '', $bugQuery);
        }

        $bugs = $this->dao->select('t1.*,t2.id as defectId,t2.title as defectTitle')->from(TABLE_BUG)->alias('t1')
            ->leftJoin(TABLE_DEFECT)->alias('t2')->on('t1.id = t2.bugId')
            ->where($bugQuery)
            ->beginIF($applicationID)->andWhere('t1.applicationID')->eq($applicationID)->fi()
            ->beginIF($projectID)->andWhere('t1.project')->eq($projectID)->fi()
            ->beginIF($excludeBugs)->andWhere('t1.id')->notIN($excludeBugs)->fi()
            ->andWhere('t1.deleted')->eq(0)
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll();
        return $bugs;
    }

    /**
     * Form customed bugs.
     *
     * @param  array    $bugs
     * @access public
     * @return array
     */
    public function formCustomedBugs($bugs)
    {
        /* Get related objects id lists. */
        $relatedModuleIdList   = array();
        $relatedStoryIdList    = array();
        $relatedTaskIdList     = array();
        $relatedCaseIdList     = array();
        $relatedExecutionIdList  = array();

        foreach($bugs as $bug)
        {
            $relatedModuleIdList[$bug->module]       = $bug->module;
            $relatedStoryIdList[$bug->story]         = $bug->story;
            $relatedTaskIdList[$bug->task]           = $bug->task;
            $relatedCaseIdList[$bug->case]           = $bug->case;
            $relatedExecutionIdList[$bug->execution] = $bug->execution;

            /* Get related objects title or names. */
            $relatedModules    = $this->dao->select('id, name')->from(TABLE_MODULE)->where('id')->in($relatedModuleIdList)->fetchPairs();
            $relatedStories    = $this->dao->select('id, title')->from(TABLE_STORY) ->where('id')->in($relatedStoryIdList)->fetchPairs();
            $relatedTasks      = $this->dao->select('id, name')->from(TABLE_TASK)->where('id')->in($relatedTaskIdList)->fetchPairs();
            $relatedCases      = $this->dao->select('id, title')->from(TABLE_CASE)->where('id')->in($relatedCaseIdList)->fetchPairs();
            $relatedExecutions = $this->dao->select('id, name')->from(TABLE_EXECUTION)->where('id')->in($relatedExecutionIdList)->fetchPairs();

            /* fill some field with useful value. */
            if(isset($relatedModules[$bug->module]))       $bug->module    = $relatedModules[$bug->module];
            if(isset($relatedStories[$bug->story]))        $bug->story     = $relatedStories[$bug->story];
            if(isset($relatedTasks[$bug->task]))           $bug->task      = $relatedTasks[$bug->task];
            if(isset($relatedCases[$bug->case]))           $bug->case      = $relatedCases[$bug->case];
            if(isset($relatedExecutions[$bug->execution])) $bug->execution = $relatedExecutions[$bug->execution];
        }
        return $bugs;
    }

    /**
     * Adjust the action is clickable.
     *
     * @param  string $bug
     * @param  string $action
     * @access public
     * @return void
     */
    public static function isClickable($object, $action)
    {
        global $app;
        $action = strtolower($action);

        if($action == 'confirmbug') return $object->status == 'active' and $object->confirmed == 0;
        if($action == 'resolve')    return $object->status == 'active';
        if($action == 'close')      return $object->status == 'resolved';
        if($action == 'activate')   return $object->status != 'active';
        if($action == 'tostory')    return $object->status == 'active';
        if($action == 'delete')     return $app->user->account == $object->openedBy;
        if($action == 'defectbug')  return $object->status == 'resolved'  && ($app->user->account == $object->openedBy || $app->user->account == $object->assignedTo);
        return true;
    }

    /**
     * Get report condition from session.
     *
     * @access public
     * @return void
     */
    public function reportCondition()
    {
        if(isset($_SESSION['bugQueryCondition']))
        {
            if(!$this->session->bugOnlyCondition) return 'id in (' . preg_replace('/SELECT .* FROM/', 'SELECT t1.id FROM', $this->session->bugQueryCondition) . ')';
            return $this->session->bugQueryCondition;
        }
        return true;
    }

    /**
     * Link bug to build and release
     *
     * @param  string|array    $bugs
     * @param  int    $resolvedBuild
     * @access public
     * @return bool
     */
    public function linkBugToBuild($bugs, $resolvedBuild)
    {
        if(empty($resolvedBuild) or $resolvedBuild == 'trunk') return true;
        if(is_array($bugs)) $bugs = join(',', $bugs);

        $buildBugs  = $this->dao->select('bugs')->from(TABLE_BUILD)->where('id')->eq($resolvedBuild)->fetch('bugs');
        $buildBugs .= ',' . $bugs;
        $buildBugs  = explode(',', trim($buildBugs, ','));
        $buildBugs  = array_unique($buildBugs);
        $this->dao->update(TABLE_BUILD)->set('bugs')->eq(join(',', $buildBugs))->where('id')->eq($resolvedBuild)->exec();

        $release = $this->dao->select('id,bugs')->from(TABLE_RELEASE)->where('build')->eq($resolvedBuild)->andWhere('deleted')->eq('0')->fetch();
        if($release)
        {
            $releaseBugs = $release->bugs .',' . $bugs;
            $releaseBugs = explode(',', trim($releaseBugs, ','));
            $releaseBugs = array_unique($releaseBugs);
            $this->dao->update(TABLE_RELEASE)->set('bugs')->eq(join(',', $releaseBugs))->where('id')->eq($release->id)->exec();
        }

        return true;
    }
    /**
     * Print cell data.
     *
     * @param  object $col
     * @param  object $bug
     * @param  array  $users
     * @param  array  $builds
     * @param  array  $branches
     * @param  array  $modulePairs
     * @param  array  $projects
     * @param  array  $plans
     * @param  array  $stories
     * @param  array  $tasks
     * @param  array  $typeTileList
     * @param  array  $products
     * @param  string $mode
     * @param  array  $testtasks
     * @access public
     * @return void
     */
    public function printCell($col, $bug, $users, $builds, $branches, $modulePairs, $projects = array(), $plans = array(), $stories = array(), $tasks = array(), $typeTileList = array(), $products, $mode = 'datatable', $testtasks = [],$planInfo = [])
    {
        /* Check the product is closed. */
        $canBeChanged = common::canBeChanged('bug', $bug);

        $canBatchEdit         = ($canBeChanged and common::hasPriv('bug', 'batchEdit'));
        $canBatchConfirm      = ($canBeChanged and common::hasPriv('bug', 'batchConfirm'));
        $canBatchChangeModule = ($canBeChanged and common::hasPriv('bug', 'batchChangeModule'));
        $canBatchAssignTo     = ($canBeChanged and common::hasPriv('bug', 'batchAssignTo'));
        //$canBatchResolve      = ($canBeChanged and common::hasPriv('bug', 'batchResolve'));
        //$canBatchClose        = common::hasPriv('bug', 'batchClose');
        //$canBatchActivate     = ($canBeChanged and common::hasPriv('bug', 'batchActivate'));

        $canBatchAction = ($canBatchEdit or $canBatchConfirm or $canBatchChangeModule or $canBatchAssignTo);

        $canView = common::hasPriv('bug', 'view');

        $hasCustomSeverity = false;
        foreach($this->lang->bug->severityList as $severityKey => $severityValue)
        {
            if(!empty($severityKey) and (string)$severityKey != (string)$severityValue)
            {
                $hasCustomSeverity = true;
                break;
            }
        }
        $bugLink = helper::createLink('bug', 'view', "bugID=$bug->id");
        $account = $this->app->user->account;
        $id = $col->id;
        if($col->show)
        {
            $class = "c-$id";
            $title = '';
            if($id == 'id')     $class .= ' cell-id';
            if($id == 'status')
            {
                $class .= ' bug-' . $bug->status;
                $title  = "title='" . $this->processStatus('bug', $bug) . "'";
            }
            if($id == 'confirmed')
            {
                $class .= ' text-center';
            }
            if($id == 'title')
            {
                $class .= ' text-left';
                $title  = "title='{$bug->title}'";
            }
            if($id == 'type')
            {
                $title  = "title='" . zget($this->lang->bug->typeList, $bug->type) . "'";
            }
            if($id == 'childType')
            {
                $title  = "title='" . zget($typeTileList, $bug->childType, '') . "'";
            }
            if($id == 'assignedTo')
            {
                $class .= ' has-btn text-left';
                if($bug->assignedTo == $account) $class .= ' red';
            }
            if($id == 'resolvedBy')
            {
                $class .= ' c-user';
                $title  = "title='" . zget($users, $bug->resolvedBy) . "'";
            }
            if($id == 'linkPlan')
            {
                $class .= ' text-ellipsis';
            }
            if($id == 'linkTesttask')
            {
                $title = "title='" . zmget($testtasks, $bug->linkTesttask, '') . "'";
            }
            if($id == 'actions') $class .= ' c-actions text-right';
            if($id == 'deadline' && isset($bug->delay)) $class .= ' delayed';
            if(strpos(',product,type,project,story,plan,task,openedBuild,linkTesttask,linkPlan', ",{$id},") !== false) $class .= ' text-ellipsis';

            echo "<td class='" . $class . "' $title>";
            if(isset($this->config->bizVersion)) $this->loadModel('flow')->printFlowCell('bug', $bug, $id);
            switch($id)
            {
            case 'id':
                if($canBatchAction)
                {
                    echo html::checkbox('bugIDList', array($bug->id => '')) . html::a(helper::createLink('bug', 'view', "bugID=$bug->id"), sprintf('%03d', $bug->id), null, "data-app='{$this->app->openApp}'");
                }
                else
                {
                    printf('%03d', $bug->id);
                }
                break;
            case 'severity':
                $severityValue     = zget($this->lang->bug->severityList, $bug->severity);
                $hasCustomSeverity = !is_numeric($severityValue);
                if($hasCustomSeverity)
                {
                    echo "<span class='label-severity-custom' data-severity='{$bug->severity}' title='" . $severityValue . "'>" . $severityValue . "</span>";
                }
                else
                {
                    echo "<span class='label-severity' data-severity='{$bug->severity}' title='" . $severityValue . "'></span>";
                }
                break;
            case 'pri':
                echo "<span class='label-pri label-pri-" . $bug->pri . "' title='" . zget($this->lang->bug->priList, $bug->pri, $bug->pri) . "'>";
                echo zget($this->lang->bug->priList, $bug->pri, $bug->pri);
                echo "</span>";
                break;
            case 'confirmed':
                $class = 'confirm' . $bug->confirmed;
                echo "<span class='$class'>" . zget($this->lang->bug->confirmedList, $bug->confirmed, $bug->confirmed) . "</span> ";
                break;
            case 'title':
                if($bug->module and isset($modulePairs[$bug->module])) echo "<span class='label label-gray label-badge'>{$modulePairs[$bug->module]}</span> ";
                if($canView)
                {
                    echo html::a($bugLink, $bug->title, null, "style='color: $bug->color' data-app={$this->app->openApp}");
                }
                else
                {
                    echo "<span style='color: $bug->color'>{$bug->title}</span>";
                }
                break;
            case 'branch':
                echo zget($branches, $bug->branch, '');
                break;
            case 'product':
                $product = $bug->product;
                if(!$product) $product = 'na';
                echo zget($products, $product, '');
                break;
            case 'project':
                echo zget($projects, $bug->project, '');
                break;
            case 'linkTesttask':
                echo zmget($testtasks, $bug->linkTesttask, '');
                break;
            case 'story':
                if(isset($stories[$bug->story]))
                {
                    $story = $stories[$bug->story];
                    echo common::hasPriv('story', 'view') ? html::a(helper::createLink('story', 'view', "storyID=$story->id", 'html', true), $story->title, '', "class='iframe'") : $story->title;
                }
                break;
            case 'task':
                if(isset($tasks[$bug->task]))
                {
                    $task = $tasks[$bug->task];
                    echo common::hasPriv('task', 'view') ? html::a(helper::createLink('task', 'view', "taskID=$task->id", 'html', true), $task->name, '', "class='iframe'") : $task->name;
                }
                break;
            case 'toTask':
                if(isset($tasks[$bug->toTask]))
                {
                    $task = $tasks[$bug->toTask];
                    echo common::hasPriv('task', 'view') ? html::a(helper::createLink('task', 'view', "taskID=$task->id", 'html', true), $task->name, '', "class='iframe'") : $task->name;
                }
                break;
            case 'type':
                echo zget($this->lang->bug->typeList, $bug->type);
                break;
            case 'childType':
                echo zget($typeTileList, $bug->childType, '');
                break;
            case 'status':
                echo "<span class='status-bug status-{$bug->status}'>";
                echo $this->processStatus('bug', $bug);
                echo  '</span>';
                break;
            case 'activatedCount':
                echo $bug->activatedCount;
                break;
            case 'activatedDate':
                echo substr($bug->activatedDate, 5, 11);
                break;
            case 'keywords':
                echo $bug->keywords;
                break;
            case 'os':
                echo zget($this->lang->bug->osList, $bug->os);
                break;
            case 'browser':
                echo zget($this->lang->bug->browserList, $bug->browser);
                break;
            case 'mailto':
                $mailto = explode(',', $bug->mailto);
                foreach($mailto as $account)
                {
                    $account = trim($account);
                    if(empty($account)) continue;
                    echo zget($users, $account) . " &nbsp;";
                }
                break;
            case 'found':
                echo zget($users, $bug->found);
                break;
            case 'openedBy':
                echo zget($users, $bug->openedBy);
                break;
            case 'openedDate':
                echo substr($bug->openedDate, 5, 11);
                break;
                
            case 'linkPlan':
                $plans = explode(',', $bug->linkPlan);
                foreach($plans as $plan)
                {
                    if($plan) if (isset($planInfo[$plan])) echo html::a(helper::createLink('productplan', 'view', "planID=$plan"), $planInfo[$plan], '', "title='" . trim($bug->linkPlans,',') . "'");                    
                }
                break;
            case 'openedBuild':
                $builds = array_flip($builds);
                foreach(explode(',', $bug->openedBuild) as $build)
                {
                    $buildID = zget($builds, $build, '');
                    if($buildID == 'trunk')
                    {
                        echo $build;
                    }
                    elseif($buildID and common::hasPriv('build', 'view'))
                    {
                        echo html::a(helper::createLink('build', 'view', "buildID=$buildID"), $build, '', "title='$bug->openedBuild'");
                    }
                }
                break;
            case 'assignedTo':
                $this->printAssignedHtml($bug, $users);
                break;
            case 'assignedDate':
                echo substr($bug->assignedDate, 5, 11);
                break;
            case 'deadline':
                echo $bug->deadline;
                break;
            case 'resolvedBy':
                echo zget($users, $bug->resolvedBy, $bug->resolvedBy);
                break;
            case 'resolution':
                echo zget($this->lang->bug->resolutionList, $bug->resolution);
                break;
            case 'resolvedDate':
                echo substr($bug->resolvedDate, 5, 11);
                break;
            case 'resolvedBuild':
                echo $bug->resolvedBuild;
                break;
            case 'closedBy':
                echo zget($users, $bug->closedBy);
                break;
            case 'closedDate':
                echo substr($bug->closedDate, 5, 11);
                break;
            case 'lastEditedBy':
                echo zget($users, $bug->lastEditedBy);
                break;
            case 'lastEditedDate':
                echo substr($bug->lastEditedDate, 5, 11);
                break;
            case 'actions':
                $params     = "bugID=$bug->id";
                $createLink = "applicationID=$bug->applicationID&product=$bug->product&branch=$bug->branch&extra=$params";
                if($this->app->openApp == 'project') $createLink.",projectID=".$this->session->project;
                
                if($canBeChanged)
                {
                    common::printIcon('bug', 'confirmBug', $params, $bug, 'list', 'ok',      '', 'iframe', true);
                    common::printIcon('bug', 'resolve',    $params, $bug, 'list', 'checked', '', 'iframe', true);
                    common::printIcon('bug', 'close',      $params, $bug, 'list', '',        '', 'iframe', true);
                    common::printIcon('bug', 'edit',       $params, $bug, 'list');
                    common::printIcon('bug', 'create',     $createLink, $bug, 'list', 'copy');
                    common::printIcon('bug', 'defectbug',  $params,$bug,'list','exchange','','iframe', true);
                }
                else
                {
                    common::printIcon('bug', 'close', $params, $bug, 'list', '', '', 'iframe', true);
                }
                break;
            }
            echo '</td>';
        }
    }

    /**
     * Print assigned html.
     *
     * @param  object $bug
     * @param  array  $users
     * @access public
     * @return void
     */
    public function printAssignedHtml($bug, $users)
    {
        $btnTextClass   = '';
        $assignedToText = !empty($bug->assignedTo) ? zget($users, $bug->assignedTo) : $this->lang->bug->noAssigned;
        $btnTextClass   = 'text-primary';
        if($bug->assignedTo == $this->app->user->account) $btnTextClass = 'text-red';

        $btnClass     = $bug->assignedTo == 'closed' ? ' disabled' : '';
        $btnClass     = "iframe btn btn-icon-left btn-sm {$btnClass}";

        $assignToLink = helper::createLink('bug', 'assignTo', "bugID=$bug->id", '', true);
        $assignToHtml = html::a($assignToLink, "<i class='icon icon-hand-right'></i> <span title='" . zget($users, $bug->assignedTo) . "' class='{$btnTextClass}'>{$assignedToText}</span>", '', "class='$btnClass'");

        echo !common::hasPriv('bug', 'assignTo', $bug) ? "<span style='padding-left: 21px' class='{$btnTextClass}'>{$assignedToText}</span>" : $assignToHtml;
    }

    /**
     * Send mail
     *
     * @param  int    $bugID
     * @param  int    $actionID
     * @access public
     * @return void
     */
    public function sendmail($bugID, $actionID)
    {
        $this->loadModel('mail');
        $bug   = $this->getByID($bugID);
        $users = $this->loadModel('user')->getPairs('noletter');

        /* Get action info. */
        $action             = $this->loadModel('action')->getById($actionID);
        $history            = $this->action->getHistory($actionID);
        $action->history    = isset($history[$actionID]) ? $history[$actionID] : array();
        $action->appendLink = '';
        if(strpos($action->extra, ':') !== false)
        {
            list($extra, $id) = explode(':', $action->extra);
            if($id and is_numeric($id))
            {
                $action->extra = $extra;

                $name = $this->dao->select('title')->from(TABLE_BUG)->where('id')->eq($id)->fetch('title');
                if($name) $action->appendLink = html::a(zget($this->config->mail, 'domain', common::getSysURL()) . helper::createLink($action->objectType, 'view', "id=$id", 'html'), "#$id " . $name);
            }
        }

        /* Get mail content. */
        $modulePath = $this->app->getModulePath($appName = '', 'bug');
        $oldcwd     = getcwd();
        $viewFile   = $modulePath . 'view/sendmail.html.php';
        chdir($modulePath . 'view');
        if(file_exists($modulePath . 'ext/view/sendmail.html.php'))
        {
            $viewFile = $modulePath . 'ext/view/sendmail.html.php';
            chdir($modulePath . 'ext/view');
        }
        ob_start();
        include $viewFile;
        foreach(glob($modulePath . 'ext/view/sendmail.*.html.hook.php') as $hookFile) include $hookFile;
        $mailContent = ob_get_contents();
        ob_end_clean();
        chdir($oldcwd);

        $sendUsers = $this->getToAndCcList($bug);
        if(!$sendUsers) return;
        list($toList, $ccList) = $sendUsers;
        $subject = $this->getSubject($bug);

        /* Send it. */
        $this->mail->send($toList, $subject, $mailContent, $ccList);
        if($this->mail->isError()) error_log(join("\n", $this->mail->getError()));
    }

    /**
     * Get subject.
     *
     * @param  object    $bug
     * @access public
     * @return string
     */
    public function getSubject($bug)
    {
        if($bug->product)
        {
            $product = $this->dao->select('id,name')->from(TABLE_PRODUCT)->where('id')->eq($bug->product)->fetch();
            $productName = $product->name;
        }
        else
        {
            $application = $this->dao->select('id,name')->from(TABLE_APPLICATION)->where('id')->eq($bug->applicationID)->fetch();
            $productName = $this->lang->naProduct . ' - ' . $application->name;
        }
        return $this->lang->bug->innerDefect. ' #'. $bug->id . ' ' . $bug->title . ' - ' . $productName;
    }

    /**
     * Get toList and ccList.
     *
     * @param  object    $bug
     * @access public
     * @return bool|array
     */
    public function getToAndCcList($bug)
    {
        /* Set toList and ccList. */
        $toList = $bug->assignedTo;
        $ccList = trim($bug->mailto, ',');
        if(empty($toList))
        {
            if(empty($ccList)) return false;
            if(strpos($ccList, ',') === false)
            {
                $toList = $ccList;
                $ccList = '';
            }
            else
            {
                $commaPos = strpos($ccList, ',');
                $toList = substr($ccList, 0, $commaPos);
                $ccList = substr($ccList, $commaPos + 1);
            }
        }
        elseif(strtolower($toList) == 'closed')
        {
            $toList = $bug->resolvedBy;
        }

        return array($toList, $ccList);
    }

    /**
     * Summary
     *
     * @param  array    $bugs
     * @access public
     * @return string
     */
    public function summary($bugs)
    {
        $unresolved = 0;
        foreach($bugs as $bug)
        {
            if($bug->status != 'resolved' && $bug->status != 'closed') $unresolved++;
        }

        return sprintf($this->lang->bug->summary, count($bugs), $unresolved);
    }

    // 获取Bug分类下的子类数据。
    public function getChildTypeList($assignType = '')
    {
        // 自定义的Bug子类数据。
        $typeList      = $this->lang->bug->typeList;
        $childTypeList = isset($this->lang->bug->childTypeList) ? $this->lang->bug->childTypeList['all'] : '[]';
        $childTypeList = json_decode($childTypeList, true);
        $customList    = empty($childTypeList[$assignType]) ? array('0' => '') : $childTypeList[$assignType];
        if(!empty($customList)) $customList = array('0' => '') + $customList;
        return $customList;
    }

    // 获取Bug分类下的子类数据键值对。
    public function getChildTypeTileList($firstNull = false)
    {
        // 自定义的Bug子类数据。
        $typeList      = $this->lang->bug->typeList;
        $childTypeList = isset($this->lang->bug->childTypeList) ? $this->lang->bug->childTypeList['all'] : '[]';
        $childTypeList = json_decode($childTypeList, true);

        $customList = array();
        if($firstNull) $customList[0] = '';
        foreach($childTypeList as $type => $items)
        {
            // Bug分类不存在的话，子类也不展示了。
            if(empty($typeList[$type])) continue;

            foreach($items as $key => $value)
            {
                $customList[$key] = $value;
            }
        }

        return $customList;
    }

    // 获取Bug分类下的子类数据(父分类)。
    public function getChildTypeParentList()
    {
        // 自定义的Bug子类数据。
        $typeList      = $this->lang->bug->typeList;
        $childTypeList = isset($this->lang->bug->childTypeList) ? $this->lang->bug->childTypeList['all'] : '[]';
        $childTypeList = json_decode($childTypeList, true);
        return $childTypeList;
    }

    public function getChildTypeParentNameList()
    {
        $childTypeList = $this->getChildTypeTileList(true);
        $allTypeList   = $this->getChildTypeParentList();
        foreach($childTypeList as $index => $name)
        {
            if(empty($index))
            {
                $childTypeList[$index] = $name;
                continue;
            }

            foreach($allTypeList as $typeKey => $typeData)
            {
                $typeName = $this->lang->bug->typeList[$typeKey];
                foreach($typeData as $childIndex => $childValue)
                {
                    if($index == $childIndex) $childTypeList[$index] = $typeName . '-' . $name;
                }
            }
        }
        return $childTypeList;
    }

    // 获取Bug分类下的子类数据(父分类)。
    public function getAllChildTypeList()
    {
        // 自定义的Bug子类数据。
        $typeList      = $this->lang->bug->typeList;
        $childTypeList = isset($this->lang->bug->childTypeList) ? $this->lang->bug->childTypeList['all'] : '[]';
        $childTypeList = json_decode($childTypeList, true);

        $data = array();
        foreach($typeList as $key => $type)
        {
            if(empty($key)) continue;
            if(empty($childTypeList[$key]))
            {
                $data[$key]['name']  = $type;
                $data[$key]['child'] = array();
            }
            else
            {
                $data[$key]['name']  = $type;
                $data[$key]['child'] = $childTypeList[$key];
            }
        }
        return $data;
    }

    /**
     * Get bugs of module in testtask.
     *
     * @param  int          $taskID
     * @param  int          $branch
     * @param  string|array $moduleIdList
     * @param  string       $orderBy
     * @param  object       $pager
     * @return array
     */
    public function getModuleBugsInTesttask($taskID, $branch = 0, $moduleIdList = 0, $orderBy = 'id_desc', $pager = null)
    {
        return $this->dao->select('*')->from(TABLE_BUG)
            ->where("FIND_IN_SET('{$taskID}', LinkTestTask)")
            ->beginIF(!empty($branch))->andWhere('branch')->eq($branch)->fi()
            ->beginIF(!empty($moduleIdList))->andWhere('module')->in($moduleIdList)->fi()
            ->andWhere('deleted')->eq(0)
            ->orderBy($orderBy)->page($pager)->fetchAll();
    }

    /**
     * Get All Bugs in testtask.
     *
     * @param  int    $taskID
     * @param  int    $branch
     * @param  array  $modules
     * @param  string $orderBy
     * @param  object $pager
     * @return array
     */
    public function getAllBugsInTesttask($taskID, $branch, $modules, $orderBy, $pager = null)
    {
        $bugs = $this->dao->select('t1.*, t2.title as planTitle')->from(TABLE_BUG)->alias('t1')
            ->leftJoin(TABLE_PRODUCTPLAN)->alias('t2')->on('t1.plan = t2.id')
            ->where("FIND_IN_SET({$taskID},linkTesttask)")
            ->beginIF($branch)->andWhere('t1.branch')->eq($branch)->fi()
            ->beginIF($modules)->andWhere('t1.module')->in($modules)->fi()
            ->andWhere('t1.deleted')->eq(0)
            ->orderBy($orderBy)->page($pager)->fetchAll();

        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'bug');

        return $bugs;
    }

    /**
     * Get bugs opened by me in testtask.
     *
     * @param  int    $taskID
     * @param  int    $branch
     * @param  array  $modules
     * @param  string $orderBy
     * @param  object $pager
     * @return array
     */
    public function getByOpenedbymeInTesttask($taskID, $branch, $modules, $orderBy, $pager)
    {
        return $this->dao->findByOpenedBy($this->app->user->account)->from(TABLE_BUG)
            ->andWhere("FIND_IN_SET({$taskID},linkTesttask)")
            ->beginIF($branch)->andWhere('branch')->in($branch)->fi()
            ->beginIF($modules)->andWhere('module')->in($modules)->fi()
            ->andWhere('deleted')->eq(0)
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll();
    }

    /**
     * Get bugs assigned to me in testtask.
     *
     * @param  int    $taskID
     * @param  int    $branch
     * @param  array  $modules
     * @param  string $orderBy
     * @param  object $pager
     * @return array
     */
    public function getByAssigntomeInTesttask($taskID, $branch, $modules, $orderBy, $pager)
    {
        return $this->dao->findByAssignedTo($this->app->user->account)->from(TABLE_BUG)
            ->andWhere("FIND_IN_SET({$taskID},linkTesttask)")
            ->beginIF($branch)->andWhere('branch')->in($branch)->fi()
            ->beginIF($modules)->andWhere('module')->in($modules)->fi()
            ->andWhere('deleted')->eq(0)
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll();
    }

    /**
     * Get bugs resolved by me in testtask.
     *
     * @param  int    $taskID
     * @param  int    $branch
     * @param  array  $modules
     * @param  string $orderBy
     * @param  object $pager
     * @return array
     */
    public function getByResolvedbymeInTesttask($taskID, $branch, $modules, $orderBy, $pager)
    {
        return $this->dao->findByResolvedBy($this->app->user->account)->from(TABLE_BUG)
            ->andWhere("FIND_IN_SET({$taskID},linkTesttask)")
            ->beginIF($branch)->andWhere('branch')->in($branch)->fi()
            ->beginIF($modules)->andWhere('module')->in($modules)->fi()
            ->andWhere('deleted')->eq(0)
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll();
    }

    /**
     * Get bugs of nobody to do in the testtask.
     *
     * @param int    $taskID
     * @param int    $branch
     * @param array  $modules
     * @param string $orderBy
     * @param object $pager
     * @access public
     * @return array
     */
    public function getByAssigntonullInTesttask($taskID, $branch, $modules, $orderBy, $pager)
    {
        return $this->dao->findByAssignedTo('')->from(TABLE_BUG)
            ->andWhere("FIND_IN_SET({$taskID},linkTesttask)")
            ->beginIF($branch)->andWhere('branch')->in($branch)->fi()
            ->beginIF($modules)->andWhere('module')->in($modules)->fi()
            ->andWhere('deleted')->eq(0)
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll();
    }

    /**
     * Get unconfirmed bugs in the testtask.
     *
     * @param int    $taskID
     * @param int    $branch
     * @param array  $modules
     * @param string $orderBy
     * @param object $pager
     * @access public
     * @return void
     */
    public function getUnconfirmedInTesttask($taskID, $branch, $modules, $orderBy, $pager)
    {
        return $this->dao->select('*')->from(TABLE_BUG)
            ->where('confirmed')->eq(0)
            ->andWhere("FIND_IN_SET({$taskID},linkTesttask)")
            ->beginIF($branch)->andWhere('branch')->in($branch)->fi()
            ->beginIF($modules)->andWhere('module')->in($modules)->fi()
            ->andWhere('deleted')->eq(0)
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll();
    }

    /**
     * Get bugs the status is active or unclosed in the testtask.
     *
     * @param int    $taskID
     * @param int    $branch
     * @param array  $modules
     * @param string $status
     * @param string $orderBy
     * @param object $pager
     * @access public
     * @return array
     */
    public function getByStatusInTesttask($taskID, $branch, $modules, $status, $orderBy, $pager)
    {
        return $this->dao->select('*')->from(TABLE_BUG)
            ->where("FIND_IN_SET({$taskID},linkTesttask)")
            ->beginIF($branch)->andWhere('branch')->in($branch)->fi()
            ->beginIF($modules)->andWhere('module')->in($modules)->fi()
            ->beginIF($status == 'unclosed')->andWhere('status')->ne('closed')->fi()
            ->beginIF($status == 'unresolved')->andWhere('status')->eq('active')->fi()
            ->beginIF($status == 'toclosed')->andWhere('status')->eq('resolved')->fi()
            ->andWhere('deleted')->eq(0)
            ->orderBy($orderBy)->page($pager)
            ->fetchAll();
    }

    /**
     * Get unclosed bugs for long time in the testtask.
     *
     * @param int    $taskID
     * @param int    $branch
     * @param array  $modules
     * @param string $orderBy
     * @param object $pager
     * @access public
     * @return array
     */
    public function getByLonglifebugsInTesttask($taskID, $branch, $modules, $orderBy, $pager)
    {
        $lastEditedDate = date(DT_DATE1, time() - $this->config->bug->longlife * 24 * 3600);
        return $this->dao->findByLastEditedDate("<", $lastEditedDate)->from(TABLE_BUG)
            ->andWhere("FIND_IN_SET({$taskID},linkTesttask)")
            ->beginIF($branch)->andWhere('branch')->in($branch)->fi()
            ->beginIF($modules)->andWhere('module')->in($modules)->fi()
            ->andWhere('openedDate')->lt($lastEditedDate)
            ->andWhere('deleted')->eq(0)
            ->andWhere('status')->ne('closed')
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll();
    }

    /**
     * Get postponed bugs in the testtask.
     *
     * @param int    $taskID
     * @param int    $branch
     * @param array  $modules
     * @param string $orderBy
     * @param object $pager
     * @access public
     * @return array
     */
    public function getByPostponedbugsInTesttask($taskID, $branch, $modules, $orderBy, $pager)
    {
        return $this->dao->findByResolution('postponed')->from(TABLE_BUG)
            ->andWhere("FIND_IN_SET({$taskID},linkTesttask)")
            ->beginIF($branch)->andWhere('branch')->in($branch)->fi()
            ->beginIF($modules)->andWhere('module')->in($modules)->fi()
            ->andWhere('deleted')->eq(0)
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll();
    }

    /**
     * Get bugs need confirm in the testtask.
     *
     * @param int    $taskID
     * @param int    $branch
     * @param array  $modules
     * @param string $orderBy
     * @param object $pager
     * @access public
     * @return array
     */
    public function getByNeedconfirmInTesttask($taskID, $branch, $modules, $orderBy, $pager)
    {
        return $this->dao->select('t1.*, t2.title AS storyTitle')->from(TABLE_BUG)->alias('t1')
            ->leftJoin(TABLE_STORY)->alias('t2')->on('t1.story = t2.id')
            ->where("t2.status = 'active'")
            ->andWhere("FIND_IN_SET({$taskID},linkTesttask)")
            ->beginIF($branch)->andWhere('t1.branch')->in($branch)->fi()
            ->beginIF($modules)->andWhere('t1.module')->in($modules)->fi()
            ->andWhere('t2.version > t1.storyVersion')
            ->andWhere('t1.deleted')->eq(0)
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll();
    }

    /**
     * Get bugs the overdueBugs is active or unclosed in the testtask.
     *
     * @param int    $taskID
     * @param int    $branch
     * @param array  $modules
     * @param string $orderBy
     * @param object $pager
     * @access public
     * @return array
     */
    public function getOverdueBugsInTesttask($taskID, $branch, $modules, $orderBy, $pager)
    {
        return $this->dao->select('*')->from(TABLE_BUG)
            ->where("FIND_IN_SET({$taskID},linkTesttask)")
            ->beginIF($branch)->andWhere('branch')->in($branch)->fi()
            ->beginIF($modules)->andWhere('module')->in($modules)->fi()
            ->andWhere('status')->eq('active')
            ->andWhere('deleted')->eq(0)
            ->andWhere('deadline')->ne('0000-00-00')
            ->andWhere('deadline')->lt(helper::today())
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll();
    }

    /**
     * 搜索测试单中的增加关联缺陷id
     *
     * @param int    $taskID
     * @param int    $branch
     * @param int    $queryID
     * @param string $orderBy
     * @param string $excludeBugs
     * @param object $pager
     * @param int    $applicationID
     * @access public
     * @return array
     */
    public function getBySearchAboutDefectInTesttask($taskID, $branch = 0, $queryID, $orderBy, $excludeBugs = '', $pager = null, $applicationID = 0)
    {
        if($queryID)
        {
            $query = $this->loadModel('search')->getQuery($queryID);
            if($query)
            {
                $this->session->set('bugQuery', $query->sql);
                $this->session->set('bugForm', $query->form);
            }
            else
            {
                $this->session->set('bugQuery', ' 1 = 1');
            }
        }
        else
        {
            if($this->session->bugQuery == false)
            {
                $this->session->set('bugQuery', ' 1 = 1');
            }
        }

        $bugQuery = $this->session->bugQuery;

        /* 搜索添加中所属产品等于NA产品时额外处理。 */
        if(strpos($bugQuery, "`product` = 'na'") !== false)
        {
            $bugQuery = str_replace("`product` = 'na'", '1', $bugQuery);
            $bugQuery = $bugQuery . " AND `product` = '0'";
        }

        /* 搜索条件中包含所属产品等于全部产品这个条件时。 */
        $allProduct = "`product` = 'all'";
        if(strpos($bugQuery, $allProduct) !== false)
        {
            $products = $this->loadModel('rebirth')->getAllProductIdList($applicationID);
            $products = implode(',', $products);
            $bugQuery = str_replace($allProduct, '1', $bugQuery);
            $bugQuery = $bugQuery . ' AND `product` ' . helper::dbIN($products);
        }

        $allBranch = "`branch` = 'all'";
        if($branch and strpos($bugQuery, '`branch` =') === false)
        {
            $bugQuery .= " AND `branch` in('0','$branch')";
        }
        if(strpos($bugQuery, $allBranch) !== false)
        {
            $bugQuery = str_replace($allBranch, '1', $bugQuery);
        }

        /* Fix bug #2878. */
        if(strpos($bugQuery, ' `resolvedDate` ') !== false)
        {
            $bugQuery = str_replace(' `resolvedDate` ', " `resolvedDate` != '0000-00-00 00:00:00' AND `resolvedDate` ", $bugQuery);
        }
        if(strpos($bugQuery, ' `closedDate` ') !== false)
        {
            $bugQuery = str_replace(' `closedDate` ', " `closedDate` != '0000-00-00 00:00:00' AND `closedDate` ", $bugQuery);
        }

        if (strpos($bugQuery, 'linkDefect'))
        {
            $bugQuery = str_replace('linkDefect', 't2.code', $bugQuery);
            $bugQuery = str_replace('product', 't1.product', $bugQuery);
            $bugQuery = str_replace('`', '', $bugQuery);
        }
        else
        {
            $bugQuery = str_replace('AND (`', ' AND (`t1.', $bugQuery);
            $bugQuery = str_replace('AND `', ' AND `t1.', $bugQuery);
            $bugQuery = str_replace('OR (`', ' OR (`t1.', $bugQuery);
            $bugQuery = str_replace('OR `', ' OR `t1.', $bugQuery);
            $bugQuery = str_replace('`', '', $bugQuery);
        }

        $bugs = $this->dao->select('t1.*,t2.id as defectId,t2.title as defectTitle')->from(TABLE_BUG)->alias('t1')
            ->leftJoin(TABLE_DEFECT)->alias('t2')->on('t1.id = t2.bugId')
            ->where($bugQuery)
            ->andWhere("FIND_IN_SET({$taskID},linkTesttask)")
            ->beginIF($taskID)->andWhere("FIND_IN_SET({$taskID},linkTesttask)")->fi()
            ->beginIF($excludeBugs)->andWhere('t1.id')->notIN($excludeBugs)->fi()
            ->andWhere('t1.deleted')->eq(0)
            ->orderBy('t1.status,t1.id_desc')
            ->fetchAll();
        return $bugs;
    }
}
