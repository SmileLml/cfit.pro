<?php
/**
 * The model file of testreport module of ZenTaoCMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Yidong Wang <yidong@cnezsoft.com>
 * @package     testreport
 * @version     $Id$
 * @link        http://www.zentao.net
 */
class testreportModel extends model
{
    /**
     * Create report.
     *
     * @access public
     * @return int
     */
    public function create()
    {
        $data = fixer::input('post')
            ->stripTags($this->config->testreport->editor->create['id'], $this->config->allowedTags)
            ->add('createdBy', $this->app->user->account)
            ->add('createdDate', helper::now())
            ->join('stories', ',')
            ->join('builds', ',')
            ->join('bugs', ',')
            ->join('cases', ',')
            ->join('members', ',')
            ->remove('files,labels,uid')
            ->get();
        $data->members = trim($data->members, ',');

        $data = $this->loadModel('file')->processImgURL($data, $this->config->testreport->editor->create['id'], $this->post->uid);
        $this->dao->insert(TABLE_TESTREPORT)->data($data)->autocheck()
             ->batchCheck($this->config->testreport->create->requiredFields, 'notempty')
             ->batchCheck('begin,end', 'notempty')
             ->check('end', 'ge', $data->begin)
             ->exec();
        if(dao::isError()) return false;
        $reportID = $this->dao->lastInsertID();
        $this->file->updateObjectID($this->post->uid, $reportID, 'testreport');
        $this->file->saveUpload('testreport', $reportID);
        return $reportID;
    }

    /**
     * Update report.
     *
     * @param  int    $reportID
     * @access public
     * @return array
     */
    public function update($reportID)
    {
        $report = $this->getById($reportID);
        $data   = fixer::input('post')
            ->stripTags($this->config->testreport->editor->edit['id'], $this->config->allowedTags)
            ->join('stories', ',')
            ->join('builds', ',')
            ->join('bugs', ',')
            ->join('cases', ',')
            ->join('members', ',')
            ->remove('files,labels,uid')
            ->get();
        $data->members = trim($data->members, ',');
        if(empty($data->bugs)) $data->bugs = '';

        $data = $this->loadModel('file')->processImgURL($data, $this->config->testreport->editor->edit['id'], $this->post->uid);
        $this->dao->update(TABLE_TESTREPORT)->data($data)->autocheck()
             ->batchCheck($this->config->testreport->edit->requiredFields, 'notempty')
             ->batchCheck('begin,end', 'notempty')
             ->check('end', 'ge', $data->begin)
             ->where('id')->eq($reportID)
             ->exec();
        if(dao::isError()) return false;

        $this->file->updateObjectID($this->post->uid, $reportID, 'testreport');
        return common::createChanges($report, $data);
    }

    /**
     * Get report by id.
     *
     * @param  int    $reportID
     * @access public
     * @return object
     */
    public function getById($reportID)
    {
        $report = $this->dao->select('*')->from(TABLE_TESTREPORT)->where('id')->eq($reportID)->fetch();
        if(!$report) return false;

        $report = $this->loadModel('file')->replaceImgURL($report, 'report');
        $report->files = $this->file->getByObject('testreport', $reportID);
        return $report;
    }

    /**
     * Get report list.
     *
     * @param  int    $objectID
     * @param  string $objectType
     * @param  string $extra
     * @param  string $orderBy
     * @param  object $pager
     * @access public
     * @return array
     */
    public function getList($applicationID, $productID, $objectType, $extra = '', $orderBy = 'id_desc', $pager = null)
    {
        return $this->dao->select('*')->from(TABLE_TESTREPORT)
            ->where('applicationID')->eq($applicationID)
            ->andWhere('deleted')->eq(0)
            ->beginIF($objectType == 'product' and $extra)->andWhere('objectID')->eq((int)$extra)->andWhere('objectType')->eq('testtask')->fi()
            ->beginIF($objectType == 'product' and empty($extra))->andWhere('product')->in($productID)->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');
    }

    /**
     * Get bug info.
     *
     * @param  array  $tasks
     * @param  array  $productIdList
     * @param  string $begin
     * @param  string $end
     * @param  array  $builds
     * @access public
     * @return array
     */
    public function getBugInfo($tasks, $applicationID, $productIdList, $begin, $end, $builds)
    {
        $generatedBugs = $this->dao->select('*')->from(TABLE_BUG)
            ->where('applicationID')->eq($applicationID)
            ->andWhere('product')->in($productIdList)
            ->andWhere('openedDate')->ge($begin)
            ->andWhere('openedDate')->le("$end 23:59:59")
            ->andWhere('deleted')->eq(0)
            ->fetchAll();

        $foundBugs     = array();
        $legacyBugs    = array();
        $byCaseNum     = 0;
        $buildIdList   = array_keys($builds);
        $taskIdList    = array_keys($tasks);

        foreach($generatedBugs as $bug)
        {
            if(array_intersect(explode(',', $bug->openedBuild), $buildIdList))
            {
                $foundBugs[$bug->id] = $bug;
                if($bug->status == 'active' or $bug->resolvedDate > "$end 23:59:59") $legacyBugs[$bug->id] = $bug;
                if($bug->case and !empty($bug->testtask) and in_array($bug->testtask, $taskIdList)) $byCaseNum ++;
            }
        }

        $severityGroups = $statusGroups = $openedByGroups = $resolvedByGroups = $resolutionGroups = $moduleGroups = $typeGroups = array();
        $resolvedBugs   = 0;
        foreach($foundBugs as $bug)
        {
            $severityGroups[$bug->severity] = isset($severityGroups[$bug->severity]) ? $severityGroups[$bug->severity] + 1 : 1;
            $typeGroups[$bug->type]         = isset($typeGroups[$bug->type])         ? $typeGroups[$bug->type]         + 1 : 1;
            $statusGroups[$bug->status]     = isset($statusGroups[$bug->status])     ? $statusGroups[$bug->status]     + 1 : 1;
            $openedByGroups[$bug->openedBy] = isset($openedByGroups[$bug->openedBy]) ? $openedByGroups[$bug->openedBy] + 1 : 1;
            $moduleGroups[$bug->module]     = isset($moduleGroups[$bug->module])     ? $moduleGroups[$bug->module]     + 1 : 1;

            if($bug->resolvedBy) $resolvedByGroups[$bug->resolvedBy] = isset($resolvedByGroups[$bug->resolvedBy]) ? $resolvedByGroups[$bug->resolvedBy] + 1 : 1;
            if($bug->resolution) $resolutionGroups[$bug->resolution] = isset($resolutionGroups[$bug->resolution]) ? $resolutionGroups[$bug->resolution] + 1 : 1;
            if($bug->status == 'resolved' or $bug->status == 'closed') $resolvedBugs ++;
        }

        $bugInfo['foundBugs']           = count($foundBugs);
        $bugInfo['legacyBugs']          = $legacyBugs;
        $bugInfo['countBugByTask']      = $byCaseNum;
        $bugInfo['bugConfirmedRate']    = empty($resolvedBugs) ? 0 : round((zget($resolutionGroups, 'fixed', 0) + zget($resolutionGroups, 'postponed', 0)) / $resolvedBugs * 100, 2);
        $bugInfo['bugCreateByCaseRate'] = empty($byCaseNum) ? 0 : round($byCaseNum / count($foundBugs) * 100, 2);

        $this->app->loadLang('bug');
        $users = $this->loadModel('user')->getPairs('noclosed|noletter|nodeleted');
        $data  = array();
        foreach($severityGroups as $severity => $count)
        {
            $data[$severity] = new stdclass();
            $data[$severity]->name  = zget($this->lang->bug->severityList, $severity);
            $data[$severity]->value = $count;
        }
        $bugInfo['bugSeverityGroups'] = $data;

        $data = array();
        foreach($typeGroups as $type => $count)
        {
            $data[$type] = new stdclass();
            $data[$type]->name  = zget($this->lang->bug->typeList, $type);
            $data[$type]->value = $count;
        }
        $bugInfo['bugTypeGroups'] = $data;

        $data = array();
        foreach($statusGroups as $status => $count)
        {
            $data[$status] = new stdclass();
            $data[$status]->name  = zget($this->lang->bug->statusList, $status);
            $data[$status]->value = $count;
        }
        $bugInfo['bugStatusGroups'] = $data;

        $data = array();
        foreach($resolutionGroups as $resolution => $count)
        {
            $data[$resolution] = new stdclass();
            $data[$resolution]->name  = zget($this->lang->bug->resolutionList, $resolution);
            $data[$resolution]->value = $count;
        }
        $bugInfo['bugResolutionGroups'] = $data;

        $data = array();
        foreach($openedByGroups as $openedBy => $count)
        {
            $data[$openedBy] = new stdclass();
            $data[$openedBy]->name  = zget($users, $openedBy);
            $data[$openedBy]->value = $count;
        }
        $bugInfo['bugOpenedByGroups'] = $data;

        $this->loadModel('tree');
        $modules = array();
        $data    = array();
        if(is_string($productIdList)) $productIdList = explode(',', $productIdList);
        foreach($productIdList as $productID) $modules += $this->tree->getOptionMenu($productID, $viewType = 'bug');
        foreach($moduleGroups as $moduleID => $count)
        {
            $data[$moduleID] = new stdclass();
            $data[$moduleID]->name  = zget($modules, $moduleID);
            $data[$moduleID]->value = $count;
        }
        $bugInfo['bugModuleGroups'] = $data;

        $data = array();
        foreach($resolvedByGroups as $resolvedBy => $count)
        {
            $data[$resolvedBy] = new stdclass();
            $data[$resolvedBy]->name  = zget($users, $resolvedBy);
            $data[$resolvedBy]->value = $count;
        }
        $bugInfo['bugResolvedByGroups'] = $data;

        return $bugInfo;
    }

    /**
     * Get task cases.
     *
     * @param  array  $tasks
     * @param  string $begin
     * @param  string $end
     * @param  string $idList
     * @param  object $pager
     * @access public
     * @return array
     */
    public function getTaskCases($tasks, $begin, $end, $idList = '', $pager = null)
    {
        $cases = $this->dao->select('t2.*,t1.task,t1.assignedTo,t1.status')->from(TABLE_TESTRUN)->alias('t1')
            ->leftJoin(TABLE_CASE)->alias('t2')->on('t1.case=t2.id')
            ->where('t1.task')->in(array_keys($tasks))
            ->beginIF($idList)->andWhere('t2.id')->in($idList)->fi()
            ->andWhere('t2.deleted')->eq(0)
            ->page($pager)
            ->fetchAll('id');

        $results = $this->dao->select('t1.*')->from(TABLE_TESTRESULT)->alias('t1')
            ->leftJoin(TABLE_TESTRUN)->alias('t2')->on('t1.run=t2.id')
            ->where('t2.task')->in(array_keys($tasks))
            ->andWhere('t1.`case`')->in(array_keys($cases))
            ->andWhere('t1.date')->ge($begin)
            ->andWhere('t1.date')->le($end . " 23:59:59")
            ->orderBy('date')
            ->fetchAll('case');

        foreach($cases as $caseID => $case)
        {
            $case->lastRunner    = '';
            $case->lastRunDate   = '';
            $case->lastRunResult = '';
            $case->status        = 'wait';
            if(isset($results[$caseID]))
            {
                $result = $results[$caseID];
                $case->lastRunner    = $result->lastRunner;
                $case->lastRunDate   = $result->date;
                $case->lastRunResult = $result->caseResult;
                $case->status        = $result->caseResult == 'blocked' ? 'blocked' : 'done';
            }
        }

        return $cases;
    }

    /**
     * Get caseID list.
     *
     * @param  int    $reportID
     * @access public
     * @return array
     */
    public function getCaseIdList($reportID)
    {
        $caseIdList = $this->dao->select('t2.case')->from(TABLE_TESTREPORT)->alias('t1')
            ->leftJoin(TABLE_TESTRUN)->alias('t2')->on('t1.tasks=t2.task')
            ->leftJoin(TABLE_CASE)->alias('t3')->on('t2.case=t3.id')
            ->where('t1.id')->eq($reportID)
            ->andWhere('t1.deleted')->eq(0)
            ->andWhere('t3.deleted')->eq(0)
            ->fetchPairs('case');

        return $caseIdList;
    }

    /**
     * Get result summary.
     *
     * @param  array    $tasks
     * @param  array    $cases
     * @param  string   $begin
     * @param  string   $end
     * @access public
     * @return string
     */
    public function getResultSummary($tasks, $cases, $begin, $end)
    {
        $results = $this->dao->select('t1.*')->from(TABLE_TESTRESULT)->alias('t1')
            ->leftJoin(TABLE_TESTRUN)->alias('t2')->on('t1.run=t2.id')
            ->where('t2.task')->in(array_keys($tasks))
            ->andWhere('t1.`case`')->in(array_keys($cases))
            ->andWhere('t1.date')->ge($begin)
            ->andWhere('t1.date')->le($end . " 23:59:59")
            ->orderBy('date')
            ->fetchAll('id');

        $failResults = array();
        $runCasesNum = array();
        foreach($results as $result)
        {
            $runCasesNum[$result->case] = $result->case;
            if($result->caseResult == 'fail') $failResults[$result->case] = $result->case;
        }
        return sprintf($this->lang->testreport->caseSummary, count($cases), count($runCasesNum), count($results), count($failResults));
    }

    /**
     * Get per run result for testreport.
     *
     * @param  array    $tasks
     * @param  array    $cases
     * @param  string   $begin
     * @param  string   $end
     * @access public
     * @return string
     */
    public function getPerCaseResult4Report($tasks, $cases, $begin, $end)
    {
        $datas = $this->dao->select("t1.caseResult AS name, COUNT('t1.*') AS value")->from(TABLE_TESTRESULT)->alias('t1')
            ->leftJoin(TABLE_TESTRUN)->alias('t2')
            ->on('t1.run= t2.id')
            ->where('t2.task')->in(array_keys($tasks))
            ->andwhere('t1.date = t2.lastRunDate')
            ->andWhere('t1.`case`')->in(array_keys($cases))
            ->andWhere('t1.date')->ge($begin)
            ->andWhere('t1.date')->le($end . " 23:59:59")
            ->groupBy('name')
            ->orderBy('value DESC')
            ->fetchAll('name');

        if(!$datas) return array();

        $this->app->loadLang('testcase');
        foreach($datas as $result => $data) $data->name = isset($this->lang->testcase->resultList[$result])? $this->lang->testcase->resultList[$result] : $this->lang->testtask->unexecuted;

        return $datas;
    }

    /**
     * Get per case runner for testreport.
     *
     * @param  array    $tasks
     * @param  array    $cases
     * @param  string   $begin
     * @param  string   $end
     * @access public
     * @return string
     */
    public function getPerCaseRunner4Report($tasks, $cases, $begin, $end)
    {
        $datas = $this->dao->select("t1.lastRunner AS name, COUNT('t1.*') AS value")->from(TABLE_TESTRESULT)->alias('t1')
            ->leftJoin(TABLE_TESTRUN)->alias('t2')
            ->on('t1.run= t2.id')
            ->where('t2.task')->in(array_keys($tasks))
            ->andwhere('t1.date = t2.lastRunDate')
            ->andWhere('t1.`case`')->in(array_keys($cases))
            ->andWhere('t1.date')->ge($begin)
            ->andWhere('t1.date')->le($end . " 23:59:59")
            ->groupBy('name')
            ->orderBy('value DESC')
            ->fetchAll('name');

        if(!$datas) return array();

        $users = $this->loadModel('user')->getPairs('noclosed|noletter');
        foreach($datas as $result => $data) $data->name = $result ? zget($users, $result, $result) : $this->lang->testtask->unexecuted;

        return $datas;
    }

    /**
     * Get bugs for test
     *
     * @param  array  $builds
     * @param  array  $product
     * @param  string $begin
     * @param  string $end
     * @param  string $type
     * @access public
     * @return void
     */
    public function getBugs4Test($builds, $applicationID, $product, $begin, $end, $type = 'build')
    {
        $bugIdList = '';
        if(is_array($builds))
        {
            foreach($builds as $build)
            {
                if(!trim($build->bugs, ',')) continue;
                $bugIdList .= $build->bugs . ',';
            }
        }

        return $this->dao->select('*')->from(TABLE_BUG)
            ->where('deleted')->eq(0)
            ->andWhere('applicationID')->eq($applicationID)
            ->andWhere('product')->in($product)
            ->andWhere('openedDate')->lt($begin)
            ->beginIF(is_array($builds) and $type == 'build')->andWhere('id')->in(trim($bugIdList, ','))->fi()
            ->beginIF(!is_array($builds) and $type == 'build')->andWhere("(resolvedBuild = 'trunk' and resolvedDate >= '$begin' and resolvedDate <= '$end 23:59:59')")->fi()
            ->beginIF($type == 'project')->andWhere("(id " . helper::dbIN(trim($bugIdList, ',')) . " OR (resolvedBuild = 'trunk' and resolvedDate >= '$begin' and resolvedDate <= '$end 23:59:59'))")
            ->fetchAll('id');
    }

    /**
     * Get stories for test
     *
     * @param  array  $builds
     * @return void
     */
    public function getStories4Test($builds)
    {
        $storyIdList = '';
        foreach($builds as $build) $storyIdList .= $build->stories . ',';

        return $this->dao->select('*')->from(TABLE_STORY)->where('deleted')->eq(0)
            ->andWhere('id')->in(trim($storyIdList, ','))
            ->fetchAll('id');
    }

    /**
     * Print cell data
     *
     * @param object $col
     * @param object $report
     * @param array  $users
     * @param string $mode
     * @param array  $projects
     * @param array  $products
     * @param array  $tasks
     * @access public
     * @return void
     */
    public function printCell($col, $report, $users, $mode = 'datatable', $projects = [], $products = [], $tasks)
    {
        $viewLink = helper::createLink('testreport', 'view', "reportID=$report->id");

        $id = $col->id;
        if($col->show)
        {
            $class = 'c-' . $id;
            $title = '';
            if($id == 'title')
            {
                $class .= ' text-left';
                $title = "title='{$report->title}'";
            }
            if($id == 'status')
            {
                $class .= $report->status;
                $title = "title='" . $this->processStatus('testreport', $report) . "'";
            }
            if($id == 'actions') $class .= ' c-actions text-right';

            echo "<td class='{$class}' {$title}>";
            if(isset($this->config->bizVersion))
            {
                $this->loadModel('flow')->printFlowCell('testreport', $report, $id);
            }

            $product = $report->product;
            if(!$product) $product = 'na';

            $productName = zget($products, $product, '');

            $taskName = '';
            foreach(explode(',', $report->tasks) as $taskID)
            {
                $taskName .= $tasks[$taskID] . ' ';
            }

            switch($id)
            {
                case 'id':
                    echo html::a($viewLink, sprintf('%03d', $report->id));
                    break;
                case 'title':
                    echo html::a($viewLink, $report->title, '', "data-app='{$this->app->openApp}'");
                    break;
                case 'product':
                    echo $productName;
                    break;
                case 'project':
                    echo zget($projects, $report->project, '');
                    break;
                case 'createdBy':
                    echo zget($users, $report->createdBy);
                    break;
                case 'createdDate':
                    echo $report->createdDate;
                    break;
                case 'testtask':
                    echo $taskName;
                    break;
                case 'actions':
                    if(common::canBeChanged('report', $report))
                    {
                        common::printIcon('testreport', 'edit', "id=$report->id", '', 'list');
                        common::printIcon('testreport', 'delete', "id=$report->id", '', 'list', 'trash', 'hiddenwin');
                    }
                    break;
            }
            echo '</td>';
        }
    }
}
