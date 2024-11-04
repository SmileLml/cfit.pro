<?php
include '../../control.php';
class myReport extends report
{
    public function exportStageParticipantWorkload($projectID = 0, $param = '')
    {
        $param = helper::safe64Decode($param);
        $param = json_decode($param, true);

        $project = $this->loadModel('projectplan')->getPlanByProjectID($projectID);

        if($_POST)
        {
            $this->loadModel('project');
            $this->app->loadLang('execution');

            // 获取搜索条件。
            $begin   = $param['begin'];
            $end     = $param['end'];
            $account = $param['account'];
            $stage   = $param['stage'];

            // 定义导出的表头。
            $fields             = array();
            $fields['name']     = '阶段名称';
            $fields['dept']     = $this->lang->project->blockDeptName;
            $fields['realname'] = $this->lang->project->blockMember;
            $fields['account'] = $this->lang->report->account;
            $fields['employeeNumber'] = $this->lang->report->employeeNumber;
            $fields['total']    = $this->lang->project->blockTotal;
            $fields['perMonth'] = $this->lang->project->blockPerMonth;

            $queryStages = $this->project->getConditionStagePairsByProject($projectID);

            $stages = $this->project->getReportStageOrderByProject($projectID, $stage);
            $stageWorkloadList = $this->report->getChildrenStagePersonnelWorkload($stages, $begin, $end, $account, $projectID);
            $deptMap = $this->loadModel('dept')->getOptionMenu();

            $workloadList  = $stageWorkloadList['stageWorkloadList'];
            $rowspanParent = $stageWorkloadList['rowspanParent'];
            $rowspanChild  = $stageWorkloadList['rowspanChild'];

            $i = 0;
            foreach($stages as $stage)
            {
                if($stage->parent == 0)
                {
                    $data[$i] = new stdclass();
                    $data[$i]->name     = $stage->name;
                    $data[$i]->dept     = '';
                    $data[$i]->realname = '';
                    $data[$i]->account = '';
                    $data[$i]->employeeNumber = '';
                    $data[$i]->total    = '';
                    $data[$i]->perMonth = '';
                    $totalUser = 0;
                    $perMonth = 0;
                    foreach ($stage->childres as $children){
                        foreach($stageWorkloadList['stageWorkloadList'][$children->id] as $deptID => $users){
                            foreach($users as $index => $user){
                                $totalUser += $user->total;
                                $perMonth += $user->perMonth;
                            }
                        }
                    }
                    if($totalUser != 0){
                        $data[$i]->total = $totalUser;
                    }
                    if($perMonth != 0){
                        $data[$i]->perMonth = $perMonth;
                    }

                    $i ++;
                }
                else
                {
                    if($rowspanChild[$stage->id]['dept'] == 1 and $rowspanChild[$stage->id]['user'] == 1)
                    {
                        // 阶段只有一个部门一个用户时。
                        $data[$i] = new stdclass();
                        $data[$i]->name = '[子]' . $stage->name;
                        foreach($workloadList[$stage->id] as $deptID => $users)
                        {
                            foreach($users as $index => $user)
                            {
                                $data[$i]->dept     = zget($deptMap, $deptID);
                                $data[$i]->realname = $user->realname;
                                $data[$i]->account = $user->account;
                                $data[$i]->employeeNumber = $user->employeeNumber;
                                $data[$i]->total    = $user->total;
                                $data[$i]->perMonth = $user->perMonth;
                            }
                        }

                        $i ++;
                    }
                    elseif($rowspanChild[$stage->id]['dept'] == 0)
                    {
                        // 阶段没有部门时。
                        $data[$i] = new stdclass();
                        $data[$i]->name     = ' [子]' . $stage->name;
                        $data[$i]->dept     = '';
                        $data[$i]->realname = '';
                        $data[$i]->account = '';
                        $data[$i]->employeeNumber = '';
                        $data[$i]->total    = '';
                        $data[$i]->perMonth = '';

                        $i ++;
                    }
                    elseif($rowspanChild[$stage->id]['dept'] >= 1)
                    {
                        $data[$i] = new stdclass();

                        $rowspanTotal = 0;
                        if($rowspanChild[$stage->id]['user'] > 1) $rowspanTotal += $rowspanChild[$stage->id]['user'];
                        $init = 0;
                        if($init == 0)
                        {
                            if($rowspanTotal > 1) $rowspan[$i]['rows']['name'] = $rowspanTotal;
                            $data[$i]->name = ' [子]' . $stage->name;
                            $init = 1;
                        }

                        $originIndex = $i;
                        foreach($workloadList[$stage->id] as $deptID => $users)
                        {
                            $rowspan[$i]['rows']['dept'] = count($users);
                            foreach($users as $index => $user)
                            {
                                if($originIndex == $i)
                                {
                                    if(empty($index)) $data[$i]->dept = zget($deptMap, $deptID);
                                    $data[$i]->realname = $user->realname;
                                    $data[$i]->account = $user->account;
                                    $data[$i]->employeeNumber = $user->employeeNumber;
                                    $data[$i]->total    = $user->total;
                                    $data[$i]->perMonth = $user->perMonth;
                                }
                                else
                                {
                                    $data[$i] = new stdClass();
                                    if(empty($index)) $data[$i]->dept = zget($deptMap, $deptID);
                                    $data[$i]->realname = $user->realname;
                                    $data[$i]->account = $user->account;
                                    $data[$i]->employeeNumber = $user->employeeNumber;
                                    $data[$i]->total    = $user->total;
                                    $data[$i]->perMonth = $user->perMonth;
                                }

                                $i ++;
                            }
                        }
                    }
                }
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
        $this->view->fileName  = $project->mark . '_' . $this->lang->report->stageparticipantWorkload;

        $this->display();
    }
}
