<?php

include '../../control.php';
class myReport extends report
{
    public function bugTrendExport($queryType = 'xlsx', $chartMode = '', $begin = '', $end = '', $project = '', $testtask = '')
    {
        $begin = str_replace('/', '-', $begin);
        $end   = str_replace('/', '-', $end);

        $projects  = explode(',', $project);
        $testtasks = explode(',', $testtask);

        $projects  = array_filter($projects, function ($value) {return !empty($value); });
        $testtasks = array_filter($testtasks, function ($value) {return !empty($value); });

        $fields = [];
        $datas  = [];
        $widths = [];
        $rowspan = [];

        $fields['projectName']    = $this->lang->report->projectName;
        $fields['date']           = $this->lang->report->date;
        $fields['create']         = $this->lang->report->create;
        $fields['resolve']        = $this->lang->report->resolve;
        $fields['activate']       = $this->lang->report->activate;
        $fields['close']          = $this->lang->report->close;
        $fields['totalCreate']    = $this->lang->report->totalCreate;
        $fields['totalToResolve'] = $this->lang->report->totalToResolve;
        $fields['totalToClose']   = $this->lang->report->totalToClose;
        $fields['totalActivate']  = $this->lang->report->totalActivate;

        $i = 0;
        $projectIndex = 0;
        foreach($projects as $projectID)
        {
            $project     = $this->loadModel('project')->getById($projectID);
            $projectName = $project->name;
            if(!empty($project->code))
            {
                $projectName = $project->code . '_' . $projectName;
            }

            $projectTrendData = $this->report->getTrendData($begin, $end, [$projectID], $testtasks, $chartMode);

            $labels = $projectTrendData['lables'];

            foreach($labels as $labelIndex => $label)
            {
                $datas[$i]                 = new stdclass();
                $datas[$i]->projectName    = $projectName;
                $datas[$i]->create         = $projectTrendData['createPairs'][$labelIndex];
                $datas[$i]->resolve        = $projectTrendData['resolvedPairs'][$labelIndex];
                $datas[$i]->activate       = $projectTrendData['activatedPairs'][$labelIndex];
                $datas[$i]->close          = $projectTrendData['closedPairs'][$labelIndex];
                $datas[$i]->totalCreate    = $projectTrendData['totalCreateParis'][$labelIndex];
                $datas[$i]->totalToResolve = $projectTrendData['totalToResolveParis'][$labelIndex];
                $datas[$i]->totalToClose   = $projectTrendData['totalToCloseParis'][$labelIndex];
                $datas[$i]->totalActivate  = $projectTrendData['totalActivatedParis'][$labelIndex];

                $date = str_replace('-', '/', $label);
                $date = str_replace('~', '-', $date);
                $date = str_replace("\n", '', $date);
                $date = str_replace(' ', '', $date);

                $datas[$i]->date = $date;

                if(!isset($rowspan[$projectIndex]['rows']['projectName'])) $rowspan[$projectIndex]['rows']['projectName'] = 0; 
                $rowspan[$projectIndex]['rows']['projectName']++;

                $i++;
            }
            $projectIndex += count($labels);
        }

        $widths['projectName']    = 30;
        $widths['date']           = 20;
        $widths['create']         = 10;
        $widths['resolve']        = 10;
        $widths['activate']       = 10;
        $widths['close']          = 10;
        $widths['totalCreate']    = 10;
        $widths['totalActivate']  = 10;
        $widths['totalToClose']   = 10;
        $widths['totalToResolve'] = 10;

        $this->post->set('fields', $fields);
        $this->post->set('rows', $datas);
        $this->post->set('width', $widths);
        $this->post->set('rowspan', $rowspan);

        $this->post->set('fileName', 'bugTrendExport');
        $this->fetch('file', 'export2' . $queryType, $_POST);
    }
}
