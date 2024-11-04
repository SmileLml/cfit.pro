<?php
include '../../control.php';
class myReport extends report
{
    /**
     * Export the bug discovery rate report.
     *
     * @param  string $queryType
     * @access public
     * @return void
     */
    public function exportBugEscape($queryType = 'default')
    {
        $this->lang->report->menu->test['alias'] .= ',bugdiscovery';

        /* Get query conditions. */
        $projectList = '';
        $deptList    = '';

        if($queryType == 'export')
        {
            $params = $this->session->bugEscapeQueryData;
            $params = helper::safe64Decode($params);
            $params = json_decode($params, true);

            $deptList    = $params['dept'];
            $projectList = $params['project'];

            $this->post->set('dept', $deptList);
            $this->post->set('project', $projectList);
        }

        if(!empty($_POST))
        {
            /* Get query conditions. */
            $data = fixer::input('post')->get();
            $data->dept    = !empty($data->dept)    ? $data->dept    : array();
            $data->project = !empty($data->project) ? $data->project : array();

            $data->dept    = array_filter($data->dept,    function($value){return !empty($value);});
            $data->project = array_filter($data->project, function($value){return !empty($value);});

            $deptList    = !empty($data->dept)    ? $data->dept    : array();
            $projectList = !empty($data->project) ? $data->project : array();

            $userAccountList = '';
            if(!empty($deptList))
            {
                $this->loadModel('dept');
                $queryAccount = array();
                $queryDept    = array();
                if($deptList)
                {
                    foreach($deptList as $deptID)
                    {
                        if(empty($deptID)) continue;
                        $childDepts = $this->dept->getAllChildID($deptID);
                        foreach($childDepts as $childDeptID) $queryDept[$childDeptID] = $childDeptID;
                    }
                    $deptUserList = $this->dept->getUserPairsByDeptID($queryDept);
                    foreach($deptUserList as $account => $realname) $queryAccount[$account] = $account;
                }

                $userAccountList = array_keys($queryAccount);
            }

            if(!empty($deptList) and empty($userAccountList))
            {
                $projectDataList = array();
            }
            else
            {
                $projectDataList = $this->dao->select('id,name,PM')->from(TABLE_PROJECT)
                    ->where('deleted')->eq('0')
                    ->andWhere('`type`')->eq('project')
                    ->beginIF(!empty($projectList))->andWhere('id')->in($projectList)->fi()
                    ->beginIF(!empty($userAccountList))->andWhere('PM')->in($userAccountList)->fi()
                    ->orderBy('id_asc')
                    ->fetchAll('id');
                $projectDataList = $this->report->getBugEscapeList($projectDataList);
            }

            $depts    = $this->loadModel('dept')->getSpecifyLevelDeptList();;
            $projects = $this->loadModel('project')->getPairs();

            $fields                = array();
            $fields['projectName'] = $this->lang->report->projectName;
            $fields['deptName']    = $this->lang->report->deptOptions;
            $fields['defectBug']   = $this->lang->report->defectBug;
            $fields['escapeBug']   = $this->lang->report->escapeBug;
            $fields['escapeRate']  = $this->lang->report->escapeRate;

            $i = 0;
            foreach($projectDataList as $project)
            {
                $datas[$i]              = new stdclass();
                $datas[$i]->projectName = $project->name;
                $datas[$i]->deptName    = zget($depts, $project->dept, '');
                $datas[$i]->defectBug   = $project->defectTotal;
                $datas[$i]->escapeBug   = $project->bugTotal;
                $datas[$i]->escapeRate  = $project->rate;
                $i++;
            }

            $widths = array('projectName' => 30, 'deptName' => 30, 'defectBug' => 30, 'escapeBug' => 30, 'escapeRate' => 30);

            $this->post->set('fields', $fields);
            $this->post->set('rows', $datas);
            $this->post->set('width', $widths);

            if(empty($_POST['fileName'])) $this->post->set('fileName', 'null');
            $this->post->set('kind', 'sheet1');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
            die();
        }

        $this->view->fileName = $this->lang->report->bugEscape;

        $this->display();
    }
}
