<?php
public function getStages($programID = 0)
{
    $stages = $this->dao->select('id, name, begin, end, PM')->from(TABLE_PROJECT)
        ->where('type')->eq('stage')
        ->andWhere('project')->eq($programID)
        ->andWhere('deleted')->eq(0)
        ->fetchAll('id');

    foreach($stages as $id => $stage)
    {
        $tasks = $this->dao->select('count(*) as tasks')->from(TABLE_TASK)->where('execution')->eq($id)->andWhere('deleted')->eq(0)->fetch();
        $stage->tasks = $tasks->tasks;
    }

    return $stages;
}

public function getStaff($executionIdList)
{
    return $this->dao->select('count(distinct account) as count, execution')
        ->from(TABLE_EFFORT)
        ->where('objectType')->eq('task')
        ->andWhere('execution')->in($executionIdList)
        ->andWhere('deleted')->eq('0')
        ->groupBy('execution')
        ->fetchAll('execution');
}

public function getPV($executionIdList)
{
    $pvList = $this->dao->select('execution,cast(sum(estimate) as decimal(10, 2)) as estimate')->from(TABLE_TASK)
        ->where('execution')->in($executionIdList)
        ->andWhere('status')->ne('cancel')
        ->andWhere('parent')->eq('0')
        ->andWhere('deleted')->eq('0')
        ->groupBy('execution')
        ->fetchPairs();
    return $pvList;
}

public function getEV($executionIdList)
{
    $evList = $this->dao->select('execution,cast(sum(consumed) as decimal(10, 2)) as consumed')->from(TABLE_TASK)
        ->where('execution')->in($executionIdList)
        ->andWhere('status')->ne('cancel')
        ->andWhere('parent')->eq('0')
        ->andWhere('deleted')->eq('0')
        ->groupBy('execution')
        ->fetchPairs();
    return $evList;
}

public function getMeasReportByID($reportID)
{
    return $this->dao->select('*')->from(TABLE_PROGRAMREPORT)->where('id')->eq($reportID)->fetch();
}

public function getMeasReports($programID = 0, $templateID = 0)
{
    return $this->dao->select('*')
        ->from(TABLE_PROGRAMREPORT)
        ->where('deleted')->eq(0)
        ->beginIF($programID)->andWhere('project')->eq($programID)->fi()
        ->beginIF($templateID)->andWhere('template')->eq($templateID)->fi()
        ->fetchAll();
}

public function buildReportList($programID = 0)
{
    if(common::hasPriv('report', 'instanceTemplate'))
    {
        $templates = $this->loadModel('measurement')->getTemplatePairs();
        foreach($templates as $templateID => $templateName)
        {
            $this->lang->reportList->program->lists[] = $templateName . "|report|customeredreport|program={$programID}&templateID=$templateID";
        }
    }
    if(common::hasPriv('report', 'show'))
    {
        $reportList = $this->getReportList('cmmi');
        foreach($reportList as $report)
        {
            $name = json_decode($report->name, true);
            if(!is_array($name) or empty($name)) $name[$this->app->getClientLang()] = $report->name;
            if(empty($report->module)) continue;
            $reportName = !isset($name[$this->app->getClientLang()]) ? $name['en'] : $name[$this->app->getClientLang()];
            $reportName = $this->replace4Workflow($reportName);
            $this->lang->reportList->program->lists[] = $reportName . "|report|show|reportID={$report->id}&reportModule=program";
        }
    }
}

public function saveMeasReport($programID = 0, $templateID = 0, $content = '')
{
    $report = fixer::input('post')
        ->add('project', $programID)
        ->add('template', $templateID)
        ->add('createdBy', $this->app->user->account)
        ->add('createdDate', helper::now())
        ->remove('parseContent, saveReport')
        ->get();
    $report->params  = json_encode($report->params);
    $report->content = $content;

    $this->dao->insert(TABLE_PROGRAMREPORT)
        ->data($report)
        ->check('name', 'notempty')
        ->autocheck()
        ->exec();

    if(dao::isError()) return false;

    return $this->dao->lastInsertID();
}
