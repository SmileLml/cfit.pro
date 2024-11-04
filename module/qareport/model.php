<?php
/**
 * The model file of qareport module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     qareport
 * @version     $Id: model.php 5079 2013-07-10 00:44:34Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
class qareportModel extends model
{
    /**
     * Set report menu.
     *
     * @access public
     * @return void
     */
    public function setMenu()
    {
        $this->loadModel('rebirth');
        $this->loadModel('qa');

        $applicationList = $this->rebirth->getApplicationPairs();
        $applicationID   = $this->session->applicationID ? $this->session->applicationID : 0;
        $productID       = 'all';

        $applicationID = $this->rebirth->saveState($applicationList, $applicationID, $productID);
        $application   = $this->rebirth->getApplicationByID($applicationID);
        $this->rebirth->setMenu($applicationID, $productID);
    }

    /**
     * Get post pairs.
     *
     * @access public
     * @return array
     */
    public function getPostPairs()
    {
        $data = fixer::input('post')->get();
        if(isset($data->end) and !empty($data->end)) $data->end .= ' 23:59:59';
        return (array)$data;
    }

    /**
     * Merge the default chart settings and the settings of current chart.
     *
     * @param  string    $reportType
     * @access public
     * @return void
     */
    public function mergeChartOption($reportType)
    {
        $chartOption  = $this->lang->qareport->report->$reportType;
        $commonOption = $this->lang->qareport->report->options;

        $chartOption->graph->caption = $this->lang->qareport->report->charts[$reportType];
        if(!isset($chartOption->type))   $chartOption->type   = $commonOption->type;
        if(!isset($chartOption->width))  $chartOption->width  = $commonOption->width;
        if(!isset($chartOption->height)) $chartOption->height = $commonOption->height;

        foreach($commonOption->graph as $key => $value) if(!isset($chartOption->graph->$key)) $chartOption->graph->$key = $value;
    }

    /**
     * Compute percent of every item.
     *
     * @param  array    $datas
     * @access public
     * @return array
     */
    public function computePercent($datas)
    {
        $sum = 0;
        foreach($datas as $data) $sum += $data->value;

        $totalPercent = 0;
        foreach($datas as $i => $data)
        {
            $data->percent = round($data->value / $sum, 4);
            $totalPercent += $data->percent;
        }
        if(isset($i)) $datas[$i]->percent = round(1 - $totalPercent + $datas[$i]->percent, 4);
        return $datas;
    }

    /**
     * Get report data of bugs per execution.
     *
     * @access public
     * @return array
     */
    public function getDataOfBugsPerExecution()
    {
        $data = $this->getPostPairs();
        extract($data);
        if(empty($application) && empty($product) && empty($project)) return array();

        $datas = $this->dao->select('execution as name, count(id) as value')->from(TABLE_BUG)
            ->where('deleted')->eq('0')
            ->beginIF(!empty($application))->andWhere('applicationID')->eq($application)->fi()
            ->beginIF(!empty($product))->andWhere('product')->eq($product)->fi()
            ->beginIF(!empty($project))->andWhere('project')->eq($project)->fi()
            ->groupBy('execution')
            ->orderBy('value DESC')
            ->fetchAll('name');

        if(!$datas) return array();

        $executionIdList = array_keys($datas);
        $executions      = $this->loadModel('execution')->getPairsByID($executionIdList);

        $maxLength = 12;
        if(common::checkNotCN()) $maxLength = 22;
        foreach($datas as $executionID => $data)
        {
            $data->name  = isset($executions[$executionID]) ? $executions[$executionID] : $this->lang->qareport->undefined;
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
        $data = $this->getPostPairs();
        extract($data);
        if(empty($application) && empty($product) && empty($project)) return array();

        $datas = $this->dao->select('openedBuild as name, count(openedBuild) as value')
            ->from(TABLE_BUG)
            ->where('deleted')->eq('0')
            ->beginIF(!empty($application))->andWhere('applicationID')->eq($application)->fi()
            ->beginIF(!empty($product))->andWhere('product')->eq($product)->fi()
            ->beginIF(!empty($project))->andWhere('project')->eq($project)->fi()
            ->groupBy('openedBuild')
            ->orderBy('value DESC')
            ->fetchAll('name');

        if(empty($datas)) return array();

        $buildIdList = array();
        foreach($datas as $buildIDList => $data)
        {
            if(empty($buildIDList)) continue;
            $openBuildIDList = explode(',', $buildIDList);
            foreach($openBuildIDList as $buildId) $buildIdList[$buildId] = $buildId;
        }
        $builds  = $this->loadModel('build')->getPairsById($buildIdList);
        $builds += array('trunk' => $this->lang->qareport->trank);

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
            $data->name = isset($builds[$buildID]) ? $builds[$buildID] : $this->lang->qareport->undefined;
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
        $data = $this->getPostPairs();
        extract($data);
        if(empty($application) && empty($product) && empty($project)) return array();

        $datas = $this->dao->select('module as name, count(module) as value')->from(TABLE_BUG)
            ->where('deleted')->eq('0')
            ->beginIF(!empty($application))->andWhere('applicationID')->eq($application)->fi()
            ->beginIF(!empty($product))->andWhere('product')->eq($product)->fi()
            ->beginIF(!empty($project))->andWhere('project')->eq($project)->fi()
            ->groupBy('module')
            ->orderBy('value DESC')
            ->fetchAll('name');

        if(empty($datas)) return array();

        $moduleIdList = array_keys($datas);
        $modules = $this->loadModel('tree')->getModulesName($moduleIdList, true, true);
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
        $data = $this->getPostPairs();
        extract($data);
        if(empty($application) && empty($product) && empty($project)) return array();

        $hasStartDate = !empty($begin) && empty($end) ? true : false;
        $hasEndDate   = !empty($end) && empty($begin) ? true : false;
        $hasAllDate   = !empty($begin) && !empty($end) ? true : false;

        $datas = $this->dao->select('DATE_FORMAT(openedDate, "%Y-%m-%d") AS name, COUNT(*) AS value')->from(TABLE_BUG)
            ->where('deleted')->eq('0')
            ->beginIF(!empty($application))->andWhere('applicationID')->eq($application)->fi()
            ->beginIF(!empty($product))->andWhere('product')->eq($product)->fi()
            ->beginIF(!empty($project))->andWhere('project')->eq($project)->fi()
            ->beginIF($hasStartDate)->andWhere('openedDate')->ge($begin)->fi()
            ->beginIF($hasEndDate)->andWhere('openedDate')->le($end)->fi()
            ->beginIF($hasAllDate)->andWhere('openedDate')->between($begin, $end)->fi()
            ->groupBy('name')
            ->orderBy('openedDate')
            ->fetchAll();

        return $datas;
    }

    /**
     * Get report data of resolved bugs per day.
     *
     * @access public
     * @return array
     */
    public function getDataOfResolvedBugsPerDay()
    {
        $data = $this->getPostPairs();
        extract($data);
        if(empty($application) && empty($product) && empty($project)) return array();

        $hasStartDate = !empty($begin) && empty($end) ? true : false;
        $hasEndDate   = !empty($end) && empty($begin) ? true : false;
        $hasAllDate   = !empty($begin) && !empty($end) ? true : false;

        $datas = $this->dao->select('DATE_FORMAT(resolvedDate, "%Y-%m-%d") AS name, COUNT(*) AS value')->from(TABLE_BUG)
            ->where('deleted')->eq('0')
            ->beginIF(!empty($application))->andWhere('applicationID')->eq($application)->fi()
            ->beginIF(!empty($product))->andWhere('product')->eq($product)->fi()
            ->beginIF(!empty($project))->andWhere('project')->eq($project)->fi()
            ->beginIF($hasStartDate)->andWhere('resolvedDate')->ge($begin)->fi()
            ->beginIF($hasEndDate)->andWhere('resolvedDate')->le($end)->fi()
            ->beginIF($hasAllDate)->andWhere('resolvedDate')->between($begin, $end)->fi()
            ->groupBy('name')
            ->having('name != 0000-00-00')
            ->orderBy('resolvedDate')
            ->fetchAll();

        return $datas;
    }

    /**
     * Get report data of closed bugs per day.
     *
     * @access public
     * @return array
     */
    public function getDataOfClosedBugsPerDay()
    {
        $data = $this->getPostPairs();
        extract($data);
        if(empty($application) && empty($product) && empty($project)) return array();

        $hasStartDate = !empty($begin) && empty($end) ? true : false;
        $hasEndDate   = !empty($end) && empty($begin) ? true : false;
        $hasAllDate   = !empty($begin) && !empty($end) ? true : false;

        $datas = $this->dao->select('DATE_FORMAT(closedDate, "%Y-%m-%d") AS name, COUNT(*) AS value')->from(TABLE_BUG)
            ->where('deleted')->eq('0')
            ->beginIF(!empty($application))->andWhere('applicationID')->eq($application)->fi()
            ->beginIF(!empty($product))->andWhere('product')->eq($product)->fi()
            ->beginIF(!empty($project))->andWhere('project')->eq($project)->fi()
            ->beginIF($hasStartDate)->andWhere('closedDate')->ge($begin)->fi()
            ->beginIF($hasEndDate)->andWhere('closedDate')->le($end)->fi()
            ->beginIF($hasAllDate)->andWhere('closedDate')->between($begin, $end)->fi()
            ->groupBy('name')
            ->having('name != 0000-00-00')
            ->orderBy('closedDate')
            ->fetchAll();

        return $datas;
    }

    /**
     * Get report data of openeded bugs per user.
     *
     * @access public
     * @return array
     */
    public function getDataOfOpenedBugsPerUser()
    {
        $data = $this->getPostPairs();
        extract($data);
        if(empty($application) && empty($product) && empty($project)) return array();

        $hasStartDate = !empty($begin) && empty($end) ? true : false;
        $hasEndDate   = !empty($end) && empty($begin) ? true : false;
        $hasAllDate   = !empty($begin) && !empty($end) ? true : false;

        $datas = $this->dao->select('openedBy AS name, COUNT(*) AS value')->from(TABLE_BUG)
            ->where('deleted')->eq('0')
            ->beginIF(!empty($application))->andWhere('applicationID')->eq($application)->fi()
            ->beginIF(!empty($product))->andWhere('product')->eq($product)->fi()
            ->beginIF(!empty($project))->andWhere('project')->eq($project)->fi()
            ->beginIF($hasStartDate)->andWhere('openedDate')->ge($begin)->fi()
            ->beginIF($hasEndDate)->andWhere('openedDate')->le($end)->fi()
            ->beginIF($hasAllDate)->andWhere('openedDate')->between($begin, $end)->fi()
            ->groupBy('name')
            ->orderBy('value DESC')
            ->fetchAll('name');

        if(empty($datas)) return array();

        $users = $this->loadModel('user')->getPairs('noletter');
        foreach($datas as $account => $data) if(isset($users[$account])) $data->name = $users[$account];

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
        $data = $this->getPostPairs();
        extract($data);
        if(empty($application) && empty($product) && empty($project)) return array();

        $hasStartDate = !empty($begin) && empty($end) ? true : false;
        $hasEndDate   = !empty($end) && empty($begin) ? true : false;
        $hasAllDate   = !empty($begin) && !empty($end) ? true : false;

        $datas = $this->dao->select('resolvedBy AS name, COUNT(*) AS value')->from(TABLE_BUG)
            ->where('deleted')->eq('0')
            ->beginIF(!empty($application))->andWhere('applicationID')->eq($application)->fi()
            ->beginIF(!empty($product))->andWhere('product')->eq($product)->fi()
            ->beginIF(!empty($project))->andWhere('project')->eq($project)->fi()
            ->beginIF($hasStartDate)->andWhere('resolvedDate')->ge($begin)->fi()
            ->beginIF($hasEndDate)->andWhere('resolvedDate')->le($end)->fi()
            ->beginIF($hasAllDate)->andWhere('resolvedDate')->between($begin, $end)->fi()
            ->andWhere('resolvedBy')->ne('')
            ->groupBy('name')
            ->orderBy('value DESC')
            ->fetchAll('name');

        if(empty($datas)) return array();

        $users = $this->loadModel('user')->getPairs('noletter');
        foreach($datas as $account => $data) if(isset($users[$account])) $data->name = $users[$account];

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
        $data = $this->getPostPairs();
        extract($data);
        if(empty($application) && empty($product) && empty($project)) return array();

        $hasStartDate = !empty($begin) && empty($end) ? true : false;
        $hasEndDate   = !empty($end) && empty($begin) ? true : false;
        $hasAllDate   = !empty($begin) && !empty($end) ? true : false;

        $datas = $this->dao->select('closedBy AS name, COUNT(*) AS value')->from(TABLE_BUG)
            ->where('deleted')->eq('0')
            ->beginIF(!empty($application))->andWhere('applicationID')->eq($application)->fi()
            ->beginIF(!empty($product))->andWhere('product')->eq($product)->fi()
            ->beginIF(!empty($project))->andWhere('project')->eq($project)->fi()
            ->beginIF($hasStartDate)->andWhere('closedDate')->ge($begin)->fi()
            ->beginIF($hasEndDate)->andWhere('closedDate')->le($end)->fi()
            ->beginIF($hasAllDate)->andWhere('closedDate')->between($begin, $end)->fi()
            ->andWhere('closedBy')->ne('')
            ->groupBy('name')
            ->orderBy('value DESC')
            ->fetchAll('name');

        if(empty($datas)) return array();

        $users = $this->loadModel('user')->getPairs('noletter');
        foreach($datas as $account => $data) if(isset($users[$account])) $data->name = $users[$account];

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
        $data = $this->getPostPairs();
        extract($data);
        if(empty($application) && empty($product) && empty($project)) return array();

        $hasStartDate = !empty($begin) && empty($end) ? true : false;
        $hasEndDate   = !empty($end) && empty($begin) ? true : false;
        $hasAllDate   = !empty($begin) && !empty($end) ? true : false;

        $datas = $this->dao->select('severity AS name, COUNT(*) AS value')->from(TABLE_BUG)
            ->where('deleted')->eq('0')
            ->beginIF(!empty($application))->andWhere('applicationID')->eq($application)->fi()
            ->beginIF(!empty($product))->andWhere('product')->eq($product)->fi()
            ->beginIF(!empty($project))->andWhere('project')->eq($project)->fi()
            ->beginIF($hasStartDate)->andWhere('openedDate')->ge($begin)->fi()
            ->beginIF($hasEndDate)->andWhere('openedDate')->le($end)->fi()
            ->beginIF($hasAllDate)->andWhere('openedDate')->between($begin, $end)->fi()
            ->groupBy('name')
            ->orderBy('value DESC')
            ->fetchAll('name');

        if(empty($datas)) return array();

        $this->loadModel('bug');
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
        $data = $this->getPostPairs();
        extract($data);
        if(empty($application) && empty($product) && empty($project)) return array();

        $hasStartDate = !empty($begin) && empty($end) ? true : false;
        $hasEndDate   = !empty($end) && empty($begin) ? true : false;
        $hasAllDate   = !empty($begin) && !empty($end) ? true : false;

        $datas = $this->dao->select('resolution AS name, COUNT(*) AS value')->from(TABLE_BUG)
            ->where('deleted')->eq('0')
            ->beginIF(!empty($application))->andWhere('applicationID')->eq($application)->fi()
            ->beginIF(!empty($product))->andWhere('product')->eq($product)->fi()
            ->beginIF(!empty($project))->andWhere('project')->eq($project)->fi()
            ->beginIF($hasStartDate)->andWhere('openedDate')->ge($begin)->fi()
            ->beginIF($hasEndDate)->andWhere('openedDate')->le($end)->fi()
            ->beginIF($hasAllDate)->andWhere('openedDate')->between($begin, $end)->fi()
            ->andWhere('resolution')->ne('')
            ->groupBy('name')->orderBy('value DESC')
            ->fetchAll('name');

        if(empty($datas)) return array();

        $this->loadModel('bug');
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
        $data = $this->getPostPairs();
        extract($data);
        if(empty($application) && empty($product) && empty($project)) return array();

        $hasStartDate = !empty($begin) && empty($end) ? true : false;
        $hasEndDate   = !empty($end) && empty($begin) ? true : false;
        $hasAllDate   = !empty($begin) && !empty($end) ? true : false;

        $datas = $this->dao->select('status AS name, COUNT(*) AS value')->from(TABLE_BUG)
            ->where('deleted')->eq('0')
            ->beginIF(!empty($application))->andWhere('applicationID')->eq($application)->fi()
            ->beginIF(!empty($product))->andWhere('product')->eq($product)->fi()
            ->beginIF(!empty($project))->andWhere('project')->eq($project)->fi()
            ->beginIF($hasStartDate)->andWhere('openedDate')->ge($begin)->fi()
            ->beginIF($hasEndDate)->andWhere('openedDate')->le($end)->fi()
            ->beginIF($hasAllDate)->andWhere('openedDate')->between($begin, $end)->fi()
            ->groupBy('name')
            ->orderBy('value DESC')
            ->fetchAll('name');

        if(empty($datas)) return array();

        $this->loadModel('bug');
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
        $data = $this->getPostPairs();
        extract($data);
        if(empty($application) && empty($product) && empty($project)) return array();

        $hasStartDate = !empty($begin) && empty($end) ? true : false;
        $hasEndDate   = !empty($end) && empty($begin) ? true : false;
        $hasAllDate   = !empty($begin) && !empty($end) ? true : false;

        $datas = $this->dao->select('pri AS name, COUNT(*) AS value')->from(TABLE_BUG)
            ->where('deleted')->eq('0')
            ->beginIF(!empty($application))->andWhere('applicationID')->eq($application)->fi()
            ->beginIF(!empty($product))->andWhere('product')->eq($product)->fi()
            ->beginIF(!empty($project))->andWhere('project')->eq($project)->fi()
            ->beginIF($hasStartDate)->andWhere('openedDate')->ge($begin)->fi()
            ->beginIF($hasEndDate)->andWhere('openedDate')->le($end)->fi()
            ->beginIF($hasAllDate)->andWhere('openedDate')->between($begin, $end)->fi()
            ->groupBy('name')
            ->orderBy('value DESC')
            ->fetchAll('name');
        if(empty($datas)) return array();

        $this->loadModel('bug');
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
        $data = $this->getPostPairs();
        extract($data);
        if(empty($application) && empty($product) && empty($project)) return array();

        $hasStartDate = !empty($begin) && empty($end) ? true : false;
        $hasEndDate   = !empty($end) && empty($begin) ? true : false;
        $hasAllDate   = !empty($begin) && !empty($end) ? true : false;

        $datas = $this->dao->select('activatedCount AS name, COUNT(*) AS value')->from(TABLE_BUG)
            ->where('deleted')->eq('0')
            ->beginIF(!empty($application))->andWhere('applicationID')->eq($application)->fi()
            ->beginIF(!empty($product))->andWhere('product')->eq($product)->fi()
            ->beginIF(!empty($project))->andWhere('project')->eq($project)->fi()
            ->beginIF($hasStartDate)->andWhere('openedDate')->ge($begin)->fi()
            ->beginIF($hasEndDate)->andWhere('openedDate')->le($end)->fi()
            ->beginIF($hasAllDate)->andWhere('openedDate')->between($begin, $end)->fi()
            ->groupBy('name')
            ->orderBy('value DESC')
            ->fetchAll('name');

        if(empty($datas)) return array();

        $this->loadModel('bug');
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
        $data = $this->getPostPairs();
        extract($data);
        if(empty($application) && empty($product) && empty($project)) return array();

        $hasStartDate = !empty($begin) && empty($end) ? true : false;
        $hasEndDate   = !empty($end) && empty($begin) ? true : false;
        $hasAllDate   = !empty($begin) && !empty($end) ? true : false;

        $datas = $this->dao->select('type AS name, COUNT(*) AS value')->from(TABLE_BUG)
            ->where('deleted')->eq('0')
            ->beginIF(!empty($application))->andWhere('applicationID')->eq($application)->fi()
            ->beginIF(!empty($product))->andWhere('product')->eq($product)->fi()
            ->beginIF(!empty($project))->andWhere('project')->eq($project)->fi()
            ->beginIF($hasStartDate)->andWhere('openedDate')->ge($begin)->fi()
            ->beginIF($hasEndDate)->andWhere('openedDate')->le($end)->fi()
            ->beginIF($hasAllDate)->andWhere('openedDate')->between($begin, $end)->fi()
            ->groupBy('name')
            ->orderBy('value DESC')
            ->fetchAll('name');

        if(empty($datas)) return array();

        $this->loadModel('bug');
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
        $data = $this->getPostPairs();
        extract($data);
        if(empty($application) && empty($product) && empty($project)) return array();

        $hasStartDate = !empty($begin) && empty($end) ? true : false;
        $hasEndDate   = !empty($end) && empty($begin) ? true : false;
        $hasAllDate   = !empty($begin) && !empty($end) ? true : false;

        $datas = $this->dao->select('assignedTo AS name, COUNT(*) AS value')->from(TABLE_BUG)
            ->where('deleted')->eq('0')
            ->beginIF(!empty($application))->andWhere('applicationID')->eq($application)->fi()
            ->beginIF(!empty($product))->andWhere('product')->eq($product)->fi()
            ->beginIF(!empty($project))->andWhere('project')->eq($project)->fi()
            ->beginIF($hasStartDate)->andWhere('openedDate')->ge($begin)->fi()
            ->beginIF($hasEndDate)->andWhere('openedDate')->le($end)->fi()
            ->beginIF($hasAllDate)->andWhere('openedDate')->between($begin, $end)->fi()
            ->groupBy('name')
            ->orderBy('value DESC')
            ->fetchAll('name');

        if(empty($datas)) return array();

        $users = $this->loadModel('user')->getPairs('noletter');
        $users = array_filter($users);
        foreach($datas as $account => $data) $data->name = isset($users[$account]) ? $users[$account] : $this->lang->qareport->undefined;

        return $datas;
    }

    public function buildReportList()
    {
        $reportList = $this->loadModel('report')->getReportList('test');
        foreach($reportList as $report)
        {
            $name = json_decode($report->name, true);
            if(!is_array($name) or empty($name)) $name[$this->app->getClientLang()] = $report->name;
            if(empty($report->module)) continue;
            $modules = explode(',', trim($report->module, ','));
            $reportName = !isset($name[$this->app->getClientLang()]) ? $name['en'] : $name[$this->app->getClientLang()];
            $reportName = $this->loadModel('report')->replace4Workflow($reportName);
            foreach($modules as $module) $this->lang->qareportList->{$module}->lists[] = $reportName . "|qareport|show|reportID={$report->id}&reportModule=$module";
        }
    }
}
