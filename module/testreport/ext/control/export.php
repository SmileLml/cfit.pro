<?php
helper::import(realpath('../../control.php'));
class mytestreport extends testreport
{
    public function export($reportID)
    {
        $report = $this->testreport->getById($reportID);
        if($_POST)
        {
            $data   = fixer::input('post')->get();
            if(empty($data->fileName)) die(js::alert($this->lang->testreport->fileNameNotEmpty));

            if($data->fileType == 'word')
            {
                $this->exportWord($reportID);
            }
            elseif($data->fileType == 'html')
            {
                $jqueryCode = "$(function(){\n";
                foreach($_POST as $chart => $base64)
                {
                    if(strpos($chart, 'chart') === false) continue;
                    $jqueryCode .= "$('#$chart').replaceWith(\"<image src='$base64' />\");\n";
                }
                $jqueryCode .= "})\n";
                $this->session->set('notHead', true);
                $output = $this->fetch('testreport', 'view', array('reportID' =>$reportID));
                $this->session->set('notHead', false);
                $css    = '<style>' . $this->getCSS('testreport', 'export') . '</style>';
                $js     = '<script>' . $this->getJS('testreport', 'export') . $jqueryCode. '</script>';
                $jsFile = $this->app->getWwwRoot() . 'js/jquery/lib.js';
                $jquery = '<script>' . file_get_contents($jsFile) . '</script>';
                $exportNotice = "<p style='text-align:right;color:grey'>" . $this->lang->testreport->exportNotice . '</p>';
                $content = "<!DOCTYPE html>\n<html lang='zh-cn'>\n<head>\n<meta charset='utf-8'>\n<title>{$report->title}</title>\n$jquery\n$css\n$js\n</head>\n<body onload='tab()'>\n<h1>{$report->title}</h1>\n$output\n$exportNotice</body></html>";
                $this->fetch('file',  'sendDownHeader', array('fileName' => $data->fileName, 'fileType' => $data->fileType, 'content' =>$content));
            }
        }

        $this->view->report = $report;
        $this->display();
    }

    public function exportWord($reportID)
    {
        $report  = $this->testreport->getById($reportID);
        $project = $this->loadModel('project')->getById($report->project);
        $product = $this->loadModel('product')->getById($report->product);

        $stories = $report->stories ? $this->story->getByList($report->stories) : array();
        $results = $this->dao->select('*')->from(TABLE_TESTRESULT)->where('run')->in($report->tasks)->andWhere('`case`')->in($report->cases)->fetchAll();

        $tasks   = $report->tasks ? $this->testtask->getByList($report->tasks) : array();;
        $builds  = $report->builds ? $this->build->getByList($report->builds) : array();
        $cases   = $this->testreport->getTaskCases($tasks, $report->begin, $report->end, $report->cases);
        $bugs    = $report->bugs ? $this->bug->getByList($report->bugs) : array();
        $bugInfo = $this->testreport->getBugInfo($tasks, $report->applicationID, $report->product, $report->begin, $report->end, $builds);

        $storySummary = $this->product->summary($stories);
        $caseSummary  = $this->testreport->getResultSummary($tasks, $cases, $report->begin, $report->end);

        $legacyBugs = $bugInfo['legacyBugs'];
        unset($bugInfo['legacyBugs']);

        $projectProfile  = $storySummary . "<br />";
        $projectProfile .= sprintf($this->lang->testreport->buildSummary, empty($builds) ? 1 : count($builds)) . $caseSummary . "<br />";
        $projectProfile .= sprintf($this->lang->testreport->bugSummary, $bugInfo['foundBugs'], count($legacyBugs), $bugInfo['countBugByTask'] , $bugInfo['bugConfirmedRate'] . '%', $bugInfo['bugCreateByCaseRate'] . '%');
        unset($bugInfo['countBugByTask']); unset($bugInfo['bugConfirmedRate']); unset($bugInfo['bugCreateByCaseRate']); unset($bugInfo['foundBugs']);

        foreach($bugInfo as $infoKey => $infoValue)
        {
            $sum = 0;
            foreach($infoValue as $value) $sum += $value->value;

            $list = $infoValue;
            if($infoKey == 'bugSeverityGroups')   $list = $this->lang->bug->severityList;
            if($infoKey == 'bugStatusGroups')     $list = $this->lang->bug->statusList;
            if($infoKey == 'bugResolutionGroups') $list = $this->lang->bug->resolutionList;
            foreach($list as $listKey => $listValue)
            {
                if(!isset($infoValue[$listKey]))
                {
                    $infoValue[$listKey] = new stdclass();
                    $infoValue[$listKey]->name  = $listValue;
                    $infoValue[$listKey]->value = 0;
                }
                if(empty($infoValue[$listKey]->name) and empty($infoValue[$listKey]->value))
                {
                    unset($infoValue[$listKey]);
                    continue;
                }
                $infoValue[$listKey]->percent = $sum == 0 ? '0' : round($infoValue[$listKey]->value / $sum, 2);
            }
            $bugInfo[$infoKey] = $infoValue;
        }

        if($report->objectType == 'testtask')
        {
            $this->setChartDatas($report->objectID);
        }
        elseif($tasks)
        {
            foreach($tasks as $task) $this->setChartDatas($task->id);
        }

        $this->post->set('charts', $this->view->charts);
        $this->post->set('datas', $this->view->datas);

        $this->post->set('report', $report);
        $this->post->set('applicationName', zget($this->applicationList, $report->applicationID, $report->applicationID));
        $this->post->set('productName', empty($product) ? $this->lang->naProduct : $product->name);
        $this->post->set('projectName', empty($project) ? '' : $project->name);
        $this->post->set('projectDesc', empty($project) ? '' : $project->desc);
        $this->post->set('stories', $stories);
        $this->post->set('bugs', $bugs);
        $this->post->set('builds', $builds);
        $this->post->set('cases', $cases);
        $this->post->set('projectProfile', $projectProfile);
        $this->post->set('legacyBugs', $legacyBugs);
        $this->post->set('bugInfo', $bugInfo);
        $this->post->set('kind', 'testreport');

        $this->fetch('file', 'exporttestreport', $_POST);
    }
}
