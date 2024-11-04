<?php
include '../../control.php';
class myReport extends report
{
    public function exportProjectStageSummary($projectID = 0)
    {
        $project = $this->loadModel('projectplan')->getPlanByProjectID($projectID);

        if($_POST)
        {
            $this->app->loadLang('execution');

            $stages = $this->report->getStagesByProjectID($projectID);
            $users  = $this->loadModel('user')->getPairs('noletter|noclosed');

            // 获取参与人数、工作量估算偏差、工作量估算偏差率。
            $projectIdList = array_keys($stages);
            $pvList    = $this->report->getPV($projectIdList);
            $evList    = $this->report->getEV($projectIdList);
            $staffList = $this->report->getExecutionStaff($projectIdList);

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

            // 定义导出的表头。
            $fields                     = array();
            $fields['mark']             = '项目代号';
            $fields['name']             = '阶段名称';
            $fields['plannedStartDate'] = $this->lang->report->plannedStartDate;
            $fields['actualStartDate']  = $this->lang->report->actualStartDate;
            $fields['plannedEndDate']   = $this->lang->report->plannedEndDate;
            $fields['actualEndDate']    = $this->lang->report->actualEndDate;
            $fields['plannedWorkload']  = $this->lang->report->plannedWorkload;
            $fields['actualWorkload']   = $this->lang->report->actualWorkload;
            $fields['taskCount']        = $this->lang->execution->taskCount;
            $fields['staff']            = $this->lang->report->titleList['staff'];
            $fields['dv']               = $this->lang->report->titleList['dv'];
            $fields['dvrate']           = $this->lang->report->titleList['dvrate'];

            $i = 0;
            $rowspan[$i]['rows']['mark'] = count($stages);
            $total = 0;
            foreach($stages as $stage)
            {
                $data[$i] = new stdclass();
                if($i == 0) $data[$i]->mark = $project->mark;

                $pv = zget($pvList, $stage->id, 0);
                $ev = zget($evList, $stage->id, 0);
                $data[$i]->name  = $stage->parent ? '[子]' . $stage->name : $stage->name;
                $data[$i]->plannedStartDate = $stage->begin;
                $data[$i]->actualStartDate = $stage->realBegan;
                $data[$i]->plannedEndDate = $stage->end;
                $data[$i]->actualEndDate = $stage->realEnd;
                $data[$i]->plannedWorkload = $pv;
                $data[$i]->actualWorkload = $ev;
                $data[$i]->taskCount = $stage->tasks;
                $data[$i]->staff = zget($staffList, $stage->id, 0);
                $total += ($stage->grade == 1 ? $ev : 0);
                $data[$i]->dv = $ev - $pv;

                if($pv == 0)
                {
                    $dvrate = '0.00%';
                }
                else
                {
                    $dvrate = ($ev - $pv) / $pv * 100;
                    $dvrate = sprintf('%.2f', $dvrate) . '%';
                }
                $data[$i]->dvrate = $dvrate;
                $i ++;
            }
            $data[$i]->name = $this->lang->report->actualWorkloadTotal;
            $data[$i]->actualWorkload = $total;
            if(isset($rowspan)) $this->post->set('rowspan', $rowspan);
            $this->post->set('fields', $fields);
            $this->post->set('rows', $data);

            if(empty($_POST['fileName']))  $this->post->set('fileName', 'null');
            $this->post->set('kind', 'sheet1');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
            die();
        }

        $this->view->projectID = $projectID;
        $this->view->fileName  = $project->mark . '_' . $this->lang->report->projectSummary;

        $this->display();
    }
}
