<?php
include '../../control.php';
class myReport extends report
{
    public function exportPersonnelWorkload($projectID = 0, $param = '')
    {
        $param = helper::safe64Decode($param);
        $param = json_decode($param, true);

        $project = $this->loadModel('projectplan')->getPlanByProjectID($projectID);

        if($_POST)
        {
            $this->loadModel('project');

            // 获取搜索条件。
            $begin   = $param['begin'];
            $end     = $param['end'];
            $account = $param['account'];

            $depts = $this->loadModel('dept')->getTopPairs();

            // 定义导出的表头。
            $fields                       = array();
            $fields['personnelID']        = $this->lang->report->personnelID;
            $fields['personnelStageID']   = $this->lang->report->personnelStageID;
            $fields['personnelStageName'] = $this->lang->report->personnelStageName;
            $fields['personnelTaskID']    = $this->lang->report->personnelTaskID;
            $fields['personnelTaskName']  = $this->lang->report->personnelTaskName;
            $fields['personnelRealname']  = $this->lang->report->personnelRealname;
            $fields['personnelAccount']  = $this->lang->report->account;
            $fields['personnelEmployeeNumber']  = $this->lang->report->employeeNumber;
            $fields['personnelContent']   = $this->lang->report->personnelContent;
            $fields['personnelConnectDept']   = $this->lang->report->personnelConnectDept;
            $fields['personnelDate']      = $this->lang->report->personnelDate;
            $fields['personnelOpenDate']   = $this->lang->report->personnelOpenDate;
            $fields['personnelConsumed']  = $this->lang->report->personnelConsumed;
            $fields['personnelLeft']      = $this->lang->report->personnelLeft;
            $fields['personnelProgress']  = $this->lang->report->personnelProgress;
            $fields['personnelStart']     = $this->lang->report->personnelStart;
            $fields['personnelDeadline']  = $this->lang->report->personnelDeadline;

            $workloadList = $this->report->getPersonnelWorkloadDetail($projectID, $begin, $end, $account);

            $i = 0;
            foreach($workloadList as $workload)
            {
                $data[$i]                     = new stdclass();
                $data[$i]->personnelID        = $workload->id;
                $data[$i]->personnelStageID   = $workload->execution;
                $data[$i]->personnelStageName = $workload->executionName;
                $data[$i]->personnelTaskID    = $workload->objectID;
                $data[$i]->personnelTaskName  = $workload->taskName;
                $data[$i]->personnelRealname  = $workload->realname;
                $data[$i]->personnelAccount  = $workload->account;
                $data[$i]->personnelEmployeeNumber  = $workload->employeeNumber;
                $data[$i]->personnelContent   = $workload->work;
                $data[$i]->personnelDate      = $workload->date;
                $data[$i]->personnelConsumed  = $workload->consumed;
                $data[$i]->personnelLeft      = $workload->left;
                $data[$i]->personnelProgress  = $workload->progress . '%';
                $data[$i]->personnelStart     = $workload->estStarted;
                $data[$i]->personnelDeadline  = $workload->deadline;
                $data[$i]->personnelConnectDept  = $depts[$workload->deptID];
                $data[$i]->personnelOpenDate  = $workload->realDate;

                $i ++;
            }

            if(isset($rowspan)) $this->post->set('rowspan', $rowspan);
            $this->post->set('fields', $fields);
            $this->post->set('rows', $data);

            if(empty($_POST['fileName']))  $this->post->set('fileName', 'null');
            $this->post->set('kind', 'sheet1');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
            die();
        }

        $this->view->projectID = $projectID;
        $this->view->fileName  = $project->mark . '_' . $this->lang->report->participantWorkload;

        $this->display();
    }
}
