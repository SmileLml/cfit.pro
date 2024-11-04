<?php
/**
 * The model file of milestone module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2020 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Qiyu Xie <xieqiyu@easycorp.ltd>
 * @package     milestone
 * @version     $Id: model.php 5107 2020-09-09 09:46:12Z xieqiyu@easycorp.ltd $
 * @link        http://www.zentao.net
 */
class milestoneModel extends model
{
    public function getPageNav($projectID, $executionID, $productID)
    {
        $milestones       = $this->loadModel('programplan')->getMilestones($projectID);
        if(empty($milestones)) return false;
        $current          = zget($milestones, $executionID) ? zget($milestones, $executionID) : current($milestones);
        $currentProjectID = $executionID ? $executionID : key($milestones);
        $project          = $this->loadModel('execution')->getByID($projectID);

        $selectHtml = '';
        $products         = $this->loadModel('product')->getProductPairsByProject($projectID);
        $currentProductID = $productID ? $productID : $this->product->getProductIDByProject($currentProjectID);
        if(!$currentProductID) $currentProductID = key($products);
        $productName      = $this->dao->findByID($currentProductID)->from(TABLE_PRODUCT)->fetch('name');
        $pinYin           = common::convert2Pinyin($products);

        $selectHtml    .= "<div class='btn-group angle-btn'>";
        $selectHtml    .= "<a data-toggle='dropdown' class='btn' title=$productName>" . $productName . " <span class='caret'></span></a>";
        $selectHtml    .= '<div id="dropMenu" class="dropdown-menu search-list load-indicator" data-ride="searchList">';
        $selectHtml    .= '<div class="input-control search-box has-icon-left has-icon-right search-example"><input type="search" class="form-control search-input" /><label class="input-control-icon-left search-icon"><i class="icon icon-search"></i></label><a class="input-control-icon-right search-clear-btn"><i class="icon icon-close icon-sm"></i></a></div>';
        $selectHtml    .= '<div class="list-group"><div class="table-row"><div class="table-col col-left"><div class="list-group">';
        foreach($products as $id => $name)
        {
            $selectHtml .= html::a(helper::createLink('milestone', 'index', "project={$projectID}&execution=0&productID=$id"), "<i class='icon icon-folder-outline'></i> " . $name, '', "title='{$name}' data-key='" . zget($pinYin, $name, '') . "'");
        }
        $selectHtml .='</div></div></div></div></div></div>';

        $milestones = $this->loadModel('programplan')->getMilestoneByProduct($currentProductID);
        $current    = zget($milestones, $executionID) ? zget($milestones, $executionID) : current($milestones);
        $currentProjectID = $executionID ? $executionID : key($milestones);
        if(!$current) $current = $this->lang->noData;

        $pinYin = common::convert2Pinyin($milestones);

        $selectHtml    .= "<div class='btn-group angle-btn'>";
        $selectHtml    .= "<a data-toggle='dropdown' class='btn' title=$current>" . $current . " <span class='caret'></span></a>";
        $selectHtml    .= '<div id="dropMenu" class="dropdown-menu search-list load-indicator" data-ride="searchList">';
        $selectHtml    .= '<div class="input-control search-box has-icon-left has-icon-right search-example"><input type="search" class="form-control search-input" /><label class="input-control-icon-left search-icon"><i class="icon icon-search"></i></label><a class="input-control-icon-right search-clear-btn"><i class="icon icon-close icon-sm"></i></a></div>';
        $selectHtml    .= '<div class="list-group"><div class="table-row"><div class="table-col col-left"><div class="list-group">';
        foreach($milestones as $id => $name)
        {
            $selectHtml .= html::a(helper::createLink('milestone', 'index', "project={$projectID}&execution=$id"), "<i class='icon icon-folder-outline'></i> " . $name, '', "title='{$name}' data-key='" . zget($pinYin, $name, '') . "'");
        }
        $selectHtml .='</div></div></div></div></div></div>';
        return array($selectHtml, $currentProjectID);
    }

    public function getBasicInfo($projectID, $executionID)
    {
        $project = $this->loadModel('execution')->getByID($projectID);
        $execution = $this->loadModel('execution')->getByID($executionID);

        /* Get startedWeeks and finishedWeeks.*/
        $execution->startedWeeks  = $execution->realBegan == '0000-00-00' ? 0 : ceil((strtotime(helper::today()) - strtotime($execution->realBegan )) / 3600 / 24 / 7);
        $execution->finishedWeeks = $execution->realEnd   == '0000-00-00' ? 0 : ceil((strtotime(helper::today()) - strtotime($execution->realEnd)) / 3600 / 24 / 7);
        $execution->offset        = $execution->realEnd   == '0000-00-00' ? 0 : helper::diffDate($execution->end, $execution->realEnd);

        $basicInfo = new stdclass();
        $basicInfo->project = $project;
        $basicInfo->execution = $execution;
        return $basicInfo;
    }

    public function getProcess($projectID, $executionID)
    {
        $process = new stdclass();
        $project = $this->loadModel('execution')->getByID($projectID);
        $execution = $this->execution->getByID($executionID);
        $productID     = $this->loadModel('product')->getProductIDByProject($executionID);
        $executionIdList = $this->product->getProjectPairsByProduct($productID);
        $executionIdList = array_keys($executionIdList);
        $executionBegin  = $execution->begin;
        $executionEnd    = $execution->end;
        $projectBegin  = $project->begin;
        $today         = helper::today();

        $process->milestonePV = $this->getPV($executionID, $executionBegin, $executionEnd);
        $process->nowPV       = $this->getPV($executionIdList, $projectBegin, $executionEnd);

        $process->milestoneEV = $this->getEV($executionID, $executionBegin, $executionEnd);
        $process->nowEV       = $this->getEV($executionIdList, $projectBegin, $executionEnd);

        $process->milestoneAC = $this->getAC($executionID, $executionBegin, $executionEnd);
        $process->nowAC       = $this->getAC($executionIdList, $executionBegin, $executionEnd);

        $process->milestoneSPI = $process->milestonePV == 0 ? 0 : round($process->milestoneEV / $process->milestonePV, 2);
        $process->nowSPI       = $process->nowPV == 0 ? 0 : round($process->nowEV / $process->nowPV, 2);

        $process->milestoneCPI = $process->milestoneAC == 0 ? 0 : round($process->milestoneEV / $process->milestoneAC, 2);
        $process->nowCPI       = $process->nowAC == 0 ? 0 : round($process->nowEV / $process->nowAC, 2);

        $process->milestoneSV  = $process->milestonePV == 0 ? 0 : round(($process->milestoneEV - $process->milestonePV) / $process->milestonePV, 2) * 100;
        $process->nowSV        = $process->nowPV == 0 ? 0 : round(($process->nowEV - $process->nowPV) / $process->nowPV, 2) * 100;

        $process->milestoneCV  = $process->milestoneAC == 0 ? 0 : round(($process->milestoneEV - $process->milestoneAC) / $process->milestoneAC, 2) * 100;
        $process->nowCV        = $process->nowAC == 0 ? 0 : round(($process->nowEV - $process->nowAC) / $process->nowAC, 2) * 100;

        $process->spiMin = '';
        $process->spiMax = '';
        $process->svMin  = '';
        $process->svMax  = '';
        $process->cpiMin = '';
        $process->cpiMax = '';
        $process->cvMin  = '';
        $process->cvMax  = '';
        $process->cvMax  = '';
        $process->cvMax  = '';
        $process->cvMax  = '';
        $process->nowSpiTip       = '';
        $process->nowCpiTip       = '';
        $process->milestoneSpiTip = '';
        $process->milestoneCpiTip = '';
        $spiTip = isset($this->config->custom->SPI) ? json_decode($this->config->custom->SPI->progressTip) : new stdclass();
        $svTip  = isset($this->config->custom->SV)  ? json_decode($this->config->custom->SV->progressTip) : new stdclass();
        $cpiTip = isset($this->config->custom->CPI) ? json_decode($this->config->custom->CPI->costTip) : new stdclass();
        $cvTip  = isset($this->config->custom->CV)  ? json_decode($this->config->custom->CV->costTip) : new stdclass();

        foreach($spiTip as $tip)
        {
            if($tip->min <= $process->milestoneSPI and $process->milestoneSPI < $tip->max) $process->milestoneSpiTip = $tip->tip;
            if($tip->min <= $process->nowSPI and $process->nowSPI < $tip->max) $process->nowSpiTip = $tip->tip;
            if($tip->range)
            {
                $process->spiMin = $tip->min;
                $process->spiMax = $tip->max;
            }
        }

        foreach($svTip as $tip)
        {
            if($tip->range)
            {
                $process->svMin = $tip->min;
                $process->svMax = $tip->max;
            }
        }

        foreach($cpiTip as $tip)
        {
            if($tip->min <= $process->milestoneCPI and $process->milestoneCPI < $tip->max) $process->milestoneCpiTip = $tip->tip;
            if($tip->min <= $process->nowCPI and $process->nowCPI < $tip->max) $process->nowCpiTip = $tip->tip;
            if($tip->range)
            {
                $process->cpiMin = $tip->min;
                $process->cpiMax = $tip->max;
            }
        }

        foreach($cvTip as $tip)
        {
            if($tip->range)
            {
                $process->cvMin = $tip->min;
                $process->cvMax = $tip->max;
            }
        }

        return $process;
    }

    public function getCharts($projectID, $executionID)
    {
        $this->loadModel('weekly');
        $charts        = array();
        $project       = $this->loadModel('execution')->getByID($projectID);
        $execution       = $this->loadModel('execution')->getByID($executionID);
        $productID     = $this->loadModel('product')->getProductIDByProject($executionID);
        $executionIdList = $this->product->getProjectPairsByProduct($productID);
        $executionIdList = array_keys($executionIdList);
        $today         = helper::today();
        $begin         = $project->begin;
        $executionEnd    = $execution->end;
        $end           = $today > $executionEnd ? $executionEnd : date('Y-m-d', strtotime("$today + 7 days"));

        $charts['PV'] = '[';
        $charts['EV'] = '[';
        $charts['AC'] = '[';
        $i = 1;
        $start = $begin;
        while($start < $end)
        {
            $charts['labels'][] = $this->lang->milestone->chart->time . $i . $this->lang->milestone->chart->week;
            $sunday             = $this->weekly->getThisSunday($start);
            $charts['PV']      .= $this->getPV($executionIdList, $begin, $sunday) . ',';
            $charts['EV']      .= $this->getEV($executionIdList, $begin, $sunday) . ',';
            $charts['AC']      .= $this->getAC($executionIdList, $begin, $sunday) . ',';
            $start              = date('Y-m-d', strtotime("$start + 7 days"));
            $i ++;
        }

        $charts['PV'] .= ']';
        $charts['EV'] .= ']';
        $charts['AC'] .= ']';

        return $charts;
    }

    public function getPV($executionID, $begin, $end)
    {
        $tasks = $this->dao->select('*')->from(TABLE_TASK)
            ->where('execution')->in($executionID)
            ->andWhere('estStarted')->ge($begin)
            ->andWhere("(estStarted < '$end' or estStarted='0000-00-00')")
            ->andWhere('deleted')->eq(0)
            ->fetchAll('id');

        $PV = 0;
        foreach($tasks as $task)
        {
            if($task->estStarted == '0000-00-00') $task->estStarted = date('Y-m-d', strtotime($task->openedDate));
            if($task->deadline < $end)
            {
                $PV += $task->estimate;
                continue;
            }

            $fullDays   = $this->loadModel('holiday')->getActualWorkingDays($task->estStarted, $task->deadline);
            $passedDays = $this->loadModel('holiday')->getActualWorkingDays($task->estStarted, $end);

            $PV += round(count($passedDays) * $task->estimate / count($fullDays), 2);
        }

        return $PV;
    }

    public function getEV($executionID, $begin, $end)
    {
        $tasks = $this->dao->select('*')->from(TABLE_TASK)
            ->where('estStarted')->ge($begin)
            ->andWhere('estStarted')->lt($end)
            ->andWhere('consumed')->gt(0)
            ->andWhere('status')->ne('cancel')
            ->andWhere('execution')->in($executionID)
            ->fetchAll('id');

        $EV = 0;
        foreach($tasks as $task)
        {
            if($task->status == 'done' or $task->closedReason == 'done')
            {
                $EV += $task->estimate;
            }
            else
            {
                $task->progress = round($task->consumed / ($task->consumed + $task->left), 2) * 100;
                $EV += round($task->estimate * $task->progress / 100, 2);
            }
        }
        return $EV;
    }

    public function getAC($executionID, $begin, $end)
    {
        $consumed = $this->dao->select('sum(consumed) as consumed')
            ->from(TABLE_EFFORT)
            ->where('objectType')->eq('task')
            ->andWhere('date')->ge($begin)
            ->andWhere('date')->lt($end)
            ->andWhere('execution')->in($executionID)
            ->fetch('consumed');
        if(!$consumed) $consumed = 0;

        return round($consumed, 2);
    }

    public function getProductQuality($projectID, $executionID)
    {
        $productID = $this->loadModel('product')->getProductIDByProject($executionID);
        $stages    = $this->loadModel('programplan')->getPairs($projectID, $productID);
        $reviews   = $this->loadModel('review')->getPairs($projectID, $productID);
        unset($stages[0]);
        foreach($reviews as $reviewID => $review)
        {
            foreach($stages as $stageID => $stageName)
            {
                $productQuality['stages'][$stageID]['total'] = 0;
                $bugs = $this->dao->select("count(*) as bugs")->from(TABLE_BUG)
                    ->where('execution')->eq($stageID)
                    ->andWhere('identify')->eq($reviewID)
                    ->andWhere('resolution')->notin('bydesign,duplicate,notrepro,willnotfix')
                    ->andWhere('deleted')->eq(0)
                    ->fetch('bugs');

                $issues = $this->dao->select("count(*) as issues")->from(TABLE_REVIEWISSUE)
                    ->where('injection')->eq($stageID)
                    ->andWhere('review')->eq($reviewID)
                    ->andWhere('resolution')->notin('bydesign,duplicate,notrepro,willnotfix')
                    ->andWhere('deleted')->eq(0)
                    ->fetch('issues');

                $productQuality['stages'][$stageID]['name']   = $stageName;
                $productQuality['stages'][$stageID][$reviewID]['counts'] = ($bugs + $issues)  == 0 ? '' : (int)($bugs + $issues);
                //$productQuality['stages'][$stageID]['estimate'] = $this->dao->select('estimate')->from(TABLE_PROJECT)->where('id')->eq($stageID)->fetch('estimate');
            }
        }

        if(isset($productQuality['stages']))
            foreach($productQuality['stages'] as $stageID => $stages)
            {
                $total = 0;
                foreach($stages as $reviewID => $stage) $total += (int) zget($stage, 'counts', 0);
                $productQuality['stages'][$stageID]['total'] = $total;
            }

        $productQuality['reviews'] = $reviews;
        return $productQuality;
    }

    public function getWorkhours($projectID, $executionID)
    {
        $productID = $this->loadModel('product')->getProductIDByProject($executionID);
        $stages    = $this->loadModel('programplan')->getPairs($projectID, $productID);
        unset($stages[0]);
        $dev    = 0;
        $to     = 0;
        $review = 0;
        $qa     = 0;
        foreach($stages as $stageID => $stageName)
        {
            $workhours[$stageID]['name']     = $stageName;
            $workhours[$stageID]['dev']      = $this->getWorkhourByType($stageID, 'devel');
            $workhours[$stageID]['to']       = $this->getTo($stageID);
            $workhours[$stageID]['review']   = $this->getReviewHours($stageID, $executionID);
            $workhours[$stageID]['qa']       = $this->getWorkhourByType($stageID, 'test');;
            $workhours[$stageID]['count']    = $workhours[$stageID]['dev'] + $workhours[$stageID]['to'] + $workhours[$stageID]['review'] + $workhours[$stageID]['qa'];
            $workhours[$stageID]['qaToDev']  = ($workhours[$stageID]['dev'] + $workhours[$stageID]['to']) == 0 ? 0 : round($workhours[$stageID]['qa'] / ($workhours[$stageID]['dev'] + $workhours[$stageID]['to']), 2);

            $dev    += $workhours[$stageID]['dev'];
            $to     += $workhours[$stageID]['to'];
            $review += $workhours[$stageID]['review'];
            $qa     += $workhours[$stageID]['qa'];
        }

        $workhours['count']['dev']    = $dev;
        $workhours['count']['to']     = $to;
        $workhours['count']['review'] = $review;
        $workhours['count']['qa']     = $qa;
        $workhours['count']['total']  = $dev + $to + $review + $qa;

        return $workhours;
    }

    public function getWorkhourByType($stageID, $type)
    {
        $consumed = $this->dao->select('sum(consumed) as consumed')->from(TABLE_TASK)->where('execution')->eq($stageID)->andWhere('type')->eq($type)->fetch('consumed');
        return round($consumed, 2);
    }

    public function getReviewHours($stageID, $executionID = 0)
    {
        $productID = $this->loadModel('product')->getProductIDByProject($executionID);
        $stage     = $this->loadModel('programplan')->getByID($stageID);
        $consumed  = 0;
        $consumed += $this->getWorkhourByType($stageID, 'review');
        $attribute = isset($this->config->milestone->{$stage->attribute}) ? $this->config->milestone->{$stage->attribute} : '';

        $reviewConsumed = $this->dao->select('sum(t1.consumed) as consumed')->from(TABLE_REVIEWRESULT)->alias('t1')
            ->leftJoin(TABLE_REVIEW)->alias('t2')->on('t1.review=t2.id')
            ->leftJoin(TABLE_OBJECT)->alias('t3')->on('t2.object=t3.id')
            ->where('t3.category')->in($attribute)
            ->andWhere('t3.product')->eq($productID)
            ->fetch('consumed');

        $consumed += $reviewConsumed;
        return round($consumed, 2);
    }

    public function getTo($stageID)
    {
        $tasks = $this->dao->select('id, activatedDate')->from(TABLE_TASK)->where('execution')->eq($stageID)->andWhere('activatedDate')->ne('0000-00-00')->fetchPairs();
        $to = 0;
        foreach($tasks as $taskID => $activatedDate)
        {
            $consumed = $this->dao->select('sum(consumed) as consumed')->from(TABLE_EFFORT)
                ->where('objectType')->eq('task')
                ->andWhere('objectID')->eq($taskID)
                ->andWhere('date')->ge($activatedDate)
                ->fetch('consumed');
            $to += $consumed;
        }

        return round($to, 2);
    }

    public function getProjectRisk($projectID)
    {
        return $this->dao->select('*,rate * 1 as rate')->from(TABLE_RISK)
            ->where('status')->eq('active')
            ->andWhere('project')->eq($projectID)
            ->andWhere('deleted')->eq(0)
            ->orderBy('rate_desc')
            ->limit(5)
            ->fetchAll();
    }

    public function getStageDemand($projectID, $executionID, $productID, $stageList = array())
    {
        $productList = array();
        foreach($stageList as $stageID => $name) $productList[$stageID] = $productID;
        $stages = $this->loadModel('programplan')->getPlans($projectID, $productID);

        $originStory = array();
        $afterStory  = array();
        $changeStory = array();

        foreach($stages as $id => $stage)
        {
            $productID = $productList[$id];
            if($productID === 0) continue;

            $originStory[$id] = $this->dao->select('count(id) as total')->from(TABLE_STORY)
                ->where('product')->eq($productID)
                ->andWhere('type')->eq('requirement')
                ->andWhere('openedDate')->between($stage->begin, $stage->end)
                ->fetch('total');

            $afterStory[$id] = $this->dao->select('count(id) as total')->from(TABLE_STORY)
                ->where('product')->eq($productID)
                ->andWhere('type')->eq('requirement')
                ->andWhere('openedDate')->between($stage->begin, $stage->end)
                ->andWhere('deleted')->eq(0)
                ->fetch('total');

            $sql  = 'select count(id) as total from ' . TABLE_STORY;
            $sql .= ' where (product = ' . $productID . ' and type = "requirement" and openedDate between "' . $stage->begin . '" and "' . $stage->end . '" and deleted = "1")';
            $sql .= ' or (product = ' . $productID . ' and type = "requirement" and openedDate between "' . $stage->begin . '" and "' . $stage->end . '" and version > 1)';
            $changeStory[$id] = $this->dao->query($sql)->fetch();

            foreach($stage->children as $stage)
            {
                $id        = $stage->id;
                $productID = $productList[$id];
                if($productID === 0) continue;

                $originStory[$id] = $this->dao->select('count(id) as total')->from(TABLE_STORY)
                    ->where('product')->eq($productID)
                    ->andWhere('type')->eq('requirement')
                    ->andWhere('openedDate')->between($stage->begin, $stage->end)
                    ->fetch('total');

                $afterStory[$id] = $this->dao->select('count(id) as total')->from(TABLE_STORY)
                    ->where('product')->eq($productID)
                    ->andWhere('type')->eq('requirement')
                    ->andWhere('openedDate')->between($stage->begin, $stage->end)
                    ->andWhere('deleted')->eq(0)
                    ->fetch('total');

                $sql  = 'select count(id) as total from ' . TABLE_STORY;
                $sql .= ' where (product = ' . $productID . ' and type = "requirement" and openedDate between "' . $stage->begin . '" and "' . $stage->end . '" and deleted = "1")';
                $sql .= ' or (product = ' . $productID . ' and type = "requirement" and openedDate between "' . $stage->begin . '" and "' . $stage->end . '" and version > 1)';
                $changeStory[$id] = $this->dao->query($sql)->fetch();
            }
        }

        $stageInfo = array('origin' => array(), 'after' => array(), 'change' => array());
        $beginID   = 0;

        foreach($stageList as $key => $stage)
        {
            $beginID === 0 ? $stageInfo['origin'][$key] = $originStory[$key] :  $stageInfo['origin'][$key] = $afterStory[$beginID];
            $stageInfo['after'][$key]  = $afterStory[$key];
            $stageInfo['change'][$key] = $changeStory[$key]->total;
            $beginID = $key;
        }

        return $stageInfo;
    }

    public function getMeasures($projectID, $executionID)
    {
        if(empty($executionID)) return array();
        return $this->dao->select('id,contents')->from(TABLE_SOLUTIONS)
            ->where('execution')->eq($projectID)
            ->andWhere('execution')->eq($executionID)
            ->andWhere('type')->eq('measures')
            ->andWhere('deleted')->eq(0)
            ->fetchPairs('id', 'contents');
    }

    public function ajaxAddMeasures($data)
    {
        $this->dao->update(TABLE_SOLUTIONS)
            ->set('deleted')->eq(1)
            ->where('execution')->eq($data->projectID)
            ->andWhere('execution')->eq($data->executionID)
            ->andWhere('type')->eq('measures')
            ->exec();

        foreach($data->measures as $item)
        {
            $item = trim($item);
            if(empty($item)) continue;

            $addData = new stdClass();
            $addData->execution       = $data->projectID;
            $addData->execution   = $data->executionID;
            $addData->contents  = $item;
            $addData->type      = 'measures';
            $addData->addedBy   = $this->app->user->account;
            $addData->addedDate = helper::now();
            $addData->deleted   = 0;

            $this->dao->insert(TABLE_SOLUTIONS)->data($addData)->autoCheck()->exec();
        }
        return 1;
    }

    public function saveOtherProblem()
    {
        $data = fixer::input('post')->get();

        $this->dao->update(TABLE_SOLUTIONS)
            ->set('deleted')->eq(1)
            ->where('execution')->eq($data->projectID)
            ->andWhere('execution')->eq($data->executionID)
            ->andWhere('type')->eq('otherproblem')
            ->exec();

        foreach($data->contents as $key => $contents){
            $addData = new stdClass();
            $addData->execution   = $data->projectID;
            $addData->execution = $data->executionID;
            $addData->contents  = $contents;
            $addData->support   = $data->support[$key];
            $addData->measures  = $data->measures[$key];
            $addData->type      = 'otherproblem';
            $addData->addedBy   = $this->app->user->account;
            $addData->addedDate = helper::now();
            $addData->deleted   = 0;

            $this->dao->insert(TABLE_SOLUTIONS)->data($addData)->autoCheck()->exec();
        }
    }

    public function otherProblemsList($projectID, $executionID)
    {
        $list = $this->dao->select('*')
            ->from(TABLE_SOLUTIONS)
            ->where('execution')->eq($projectID)
            ->andWhere('execution')->eq($executionID)
            ->andWhere('type')->eq('otherproblem')
            ->andWhere('deleted')->eq(0)
            ->fetchAll();

        return $list;
   }

    public function getNextMilestone($projectID, $executionID, $stageList)
    {
        $nextID = $this->dao->select('min(id) as id')->from(TABLE_PROJECT)
            ->where('id')->gt($executionID)
            ->andWhere('project')->eq($projectID)
            ->andWhere('type')->eq('stage')
            ->andWhere('milestone')->eq(1)
            ->fetch('id');

        $stageID = array_keys($stageList);
        $nextID  = in_array($nextID, $stageID) ? $nextID : 0;

        $totalDays = $this->dao->select('sum(days) as days')->from(TABLE_PROJECT)
            ->where('id')->in($stageID)
            ->andWhere('project')->eq($projectID)
            ->andWhere('deleted')->eq(0)
            ->fetch('days');

        $totalHours = $this->dao->select('sum(days * hours) as totalHours')->from(TABLE_TEAM)
            ->where('root')->in($stageID)
            ->fetch('totalHours');

        $nextHours = 0;
        $nextDays  = 0;
        if($nextID)
        {
            $nextDays = $this->dao->select('days')->from(TABLE_PROJECT)
                ->where('id')->eq($nextID)
                ->andWhere('project')->eq($projectID)
                ->andWhere('deleted')->eq(0)
                ->fetch('days');

            $nextHours = $this->dao->select('sum(days * hours) as totalHours')->from(TABLE_TEAM)
                ->where('root')->eq($nextID)
                ->fetch('totalHours');
        }

        $result             = new stdClass();
        $result->nextDays   = empty($nextDays)   ? 0 : $nextDays;
        $result->nextHours  = empty($nextHours)  ? 0 : $nextHours;
        $result->totalDays  = empty($totalDays)  ? 0 : $totalDays;
        $result->totalHours = empty($totalHours) ? 0 : $totalHours;
        return $result;
    }

    public function ajaxSaveEstimate($taskID,$estimate)
    {
        $this->dao->update(TABLE_PROJECT)
            ->set('estimate')
            ->eq($estimate)
            ->where('id')->eq($taskID)
            ->exec();

        if(dao::isError())
        {
            echo js::error(dao::getError());
        }
    }
}
