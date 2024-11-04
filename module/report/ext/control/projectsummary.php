<?php
include '../../control.php';
class myReport extends report
{
    public function projectSummary($projectID = 0)
    {
        $this->app->loadLang('product');
        $this->app->loadLang('productplan');
        $this->app->loadLang('story');

        $this->loadModel('project')->setMenu($projectID);

        $this->report->buildReportList($projectID);
        $stages = $this->report->getStagesByProjectID($projectID);

        $this->view->title      = $this->lang->report->projectSummary;
        $this->view->position[] = $this->lang->report->projectSummary;
        $this->view->users      = $this->loadModel('user')->getPairs('noletter|noclosed');
        $this->view->submenu    = 'program';
        $this->view->projectID  = $projectID;

        // 获取参与人数、工作量估算偏差、工作量估算偏差率。
        $projectIdList = array_keys($stages);
        $staffList     = $this->report->getExecutionStaff($projectIdList);
        $pvList        = $this->report->getPV($projectIdList);
        $evList        = $this->report->getEV($projectIdList);

        foreach($stages as $stage)
        {
            if(!isset($staffList[$stage->parent])) $staffList[$stage->parent] = 0;
            if(!isset($staffList[$stage->id]))     $staffList[$stage->id]     = 0;
            if(!isset($pvList[$stage->parent]))    $pvList[$stage->parent]    = 0;
            if(!isset($pvList[$stage->id]))        $pvList[$stage->id]        = 0;
            if(!isset($evList[$stage->parent]))    $evList[$stage->parent]    = 0;
            if(!isset($evList[$stage->id]))        $evList[$stage->id]        = 0;

            if($stage->parent) $staffList[$stage->parent] += $staffList[$stage->id];
            if($stage->parent) $pvList[$stage->parent]    += $pvList[$stage->id];
            if($stage->parent) $evList[$stage->parent]    += $evList[$stage->id];
        }

        $this->view->staffList = $staffList;
        $this->view->pvList    = $pvList;
        $this->view->evList    = $evList;
        $this->view->stages    = $stages;

        $this->display();
    }
}
