<?php
include '../../control.php';
class myReport extends report
{
    public function exportBuildVerify($projectID = 0, $param = '')
    {
        $param = helper::safe64Decode($param);
        $param = json_decode($param, true);

        $this->app->loadLang('build');
        if($_POST)
        {
            $this->loadModel('project');

            // 获取搜索条件。

            $appName           = $param['appName'] ;
            $verifyActionDate  = $param['verifyActionDate'];
            $verifyDealUser    = $param['verifyDealUser'];


            // 定义导出的表头。
            $fields                       = array();

            $fields['id']                = $this->lang->build->buildID;
            $fields['projectCode']       = $this->lang->project->code;
            $fields['projectName']       = $this->lang->project->name;
            $fields['appName']           = $this->lang->build->appName;
            $fields['appCode']           = $this->lang->build->appNameCode;
            $fields['verifyActionDate']  = $this->lang->build->verifyActionDate;
            $fields['verifyDealUser']    = $this->lang->build->verifyDealUser;
            $fields['status']            = $this->lang->build->status;
            $fields['actualVerifyDate']  = $this->lang->build->verifyCompleteDate;
            $fields['actualVerifyUser']  = $this->lang->build->actualVerifyUser;


            $workloadList = $this->report->getBuildWorkload(0, $appName, $verifyActionDate, $verifyDealUser);
            $users      = $this->loadModel('user')->getPairs('noletter|noclosed');//array(''=>'') + $this->dao->select('account,realname')->from(TABLE_USER)->where('deleted')->eq(0)->andWhere('dept')->eq(12)->fetchPairs();
            

            $i = 0;
            foreach($workloadList as $workload)
            {
                $data[$i]                     = new stdclass();
                $data[$i]->id                = $workload->id;
                $data[$i]->projectCode       = $workload->projectCode;
                $data[$i]->projectName       = $workload->projectName;
                $data[$i]->appName           = $workload->appName;
                $data[$i]->appCode           = $workload->appCode;
                $data[$i]->verifyActionDate  = $workload->verifyActionDate;
                $data[$i]->verifyDealUser    = zget($users,$workload->verifyDealUser);
                $data[$i]->status            = zget($this->lang->build->changestatus,$workload->status);
                $data[$i]->actualVerifyDate  = $workload->actualVerifyDate;
                $data[$i]->actualVerifyUser  = zmget($users,$workload->actualVerifyUser);
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
        $this->view->fileName  =  $this->lang->report->buildVerify;

        $this->display();
    }
}
