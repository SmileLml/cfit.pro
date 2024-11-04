<?php
include '../../control.php';
class myReport extends report
{
    /**
     * Browse the bug trend report.
     *
     * @param  string $queryType
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function bugTrend($queryType = 'default')
    {
        $this->lang->report->menu->test['alias'] .= ',bugtrend';

        /* Get query conditions. */
        $end             = '';
        $begin           = date('Y-m-d', strtotime('-1 month'));
        $projectList     = '';
        $chartMode       = '';
        $testtaskList    = array();
        $testtasks       = array();

        if($queryType == 'page')
        {
            $params = $this->session->bugTrendQueryData;
            $params = helper::safe64Decode($params);
            $params = json_decode($params, true);

            $begin        = $params['begin'];
            $end          = $params['end'];
            $projectList  = $params['project'];
            $testtaskList = $params['testtask'];
            $chartMode    = $params['chartMode'];

            $this->post->set('begin', $begin);
            $this->post->set('end', $end);
            $this->post->set('project', $projectList);
            $this->post->set('testtask', $testtaskList);
            $this->post->set('chartMode', $chartMode);
        }

        if(!empty($_POST))
        {
            /* Get query conditions. */
            $data            = fixer::input('post')->get();
            $data->begin     = !empty($data->begin)     ? $data->begin     : '';
            $data->end       = !empty($data->end)       ? $data->end       : '';
            $data->project   = !empty($data->project)   ? $data->project   : array();
            $data->testtask  = !empty($data->testtask)  ? $data->testtask  : array();
            $data->chartMode = !empty($data->chartMode) ? $data->chartMode : '';

            $data->project  = array_filter($data->project, function($value){return !empty($value);});
            $data->testtask = array_filter($data->testtask, function($value){return !empty($value);});

            $begin        = $data->begin;
            $end          = $data->end;
            $projectList  = !empty($data->project)  ? $data->project  : array();
            $testtaskList = !empty($data->testtask) ? $data->testtask : array();
            $chartMode    = $data->chartMode;
        }

        /* When there is no active query, the data will not be displayed. */
        if(empty($projectList) and empty($testtaskList))
        {
            $trendData = array();
        }
        else
        {
            $trendData = $this->report->getTrendData($begin, $end, $projectList, $testtaskList, $chartMode);
            $chartMode = $trendData['chartMode'];
        }

        $param     = array('begin' => $begin, 'end' => $end, 'project' => $projectList, 'testtask' => $testtaskList);
        $queryData = helper::safe64Encode(json_encode($param));
        $this->session->set('bugTrendQueryData', $queryData);
        $this->app->rawParams['queryType'] = 'page';

        if(!empty($projectList)) $testtasks = $this->loadModel('testtask')->getProjectTestTasks($projectList);

        $this->view->title      = $this->lang->report->bugTrend;
        $this->view->position[] = $this->lang->report->bugTrend;

        $this->view->trendData    = $trendData;
        $this->view->projects     = array('' => '') + $this->loadModel('project')->getPairsCodeName();
        $this->view->testtasks    = $testtasks;
        $this->view->begin        = $begin;
        $this->view->end          = $end;
        $this->view->projectList  = $projectList;
        $this->view->testtaskList = $testtaskList;
        $this->view->submenu      = 'test';
        $this->view->chartMode    = $chartMode;

        $this->display();
    }
}
