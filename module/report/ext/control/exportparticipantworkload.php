<?php
include '../../control.php';
class myReport extends report
{
    public function exportParticipantWorkload($projectID = 0, $param = '')
    {
        $param = helper::safe64Decode($param);
        $param = json_decode($param, true);

        $project = $this->loadModel('projectplan')->getPlanByProjectID($projectID);
        $workHours = $this->dao->select('workHours')->from(TABLE_PROJECT)->where('id')->eq($projectID)->fetch();

        if($_POST)
        {
            $this->loadModel('project');

            // 获取搜索条件。
            $begin   = $param['begin'];
            $end     = $param['end'];
            $account = $param['account'];
            $accountType = $param['accountType'];

            // 定义导出的表头。
            $fields             = array();
            $fields['mark']     = '项目代号';
            $fields['deptName'] = $this->lang->project->blockDeptName;
            $fields['realname'] = $this->lang->project->blockMember;
            $fields['account'] = $this->lang->report->account;
            $fields['employeeNumber'] = $this->lang->report->employeeNumber;
            $fields['staffType'] = $this->lang->report->accountType;
            $fields['total']    = $this->lang->project->blockTotal;
            $fields['perMonth'] = $this->lang->project->blockPerMonth;

            // 获取部门、项目团队成员、用户工作量数据。
            $deptMap = $this->loadModel('dept')->getOptionMenu();
            $workloadTotal = $this->project->getEffortByProject($projectID, $begin, $end);

            // 只要报工过的人就算。
            $accounts = array();
            foreach($workloadTotal as $user => $workload)
            {
                $key = explode('/',$user);
                $accounts[] = $key[0];
                $list[$key[0]][$key[1]] = $workload;
            }
            $members = $this->dao->select('account,realname,dept,staffType,employeeNumber')->from(TABLE_USER)->where('account')->in($accounts)->fetchAll('account');

            // 项目团队成员用户。
            $teams = $this->project->getMembersByProject($projectID);
            $teamMembers = array();
            foreach($teams as $team) $teamMembers[$team->account] = $team;

            // 将项目团队成员和报工过的人数据合并。
            $members = array_merge($teamMembers, $members);

            $amount = array('count' => '合计','user' => 0, 'total' => 0,'perMonth' => 0);
            $userWorkloadList = array();
            $participants     = array('' => '');
            if(empty($account[0])){
                unset($account[0]);
            }
            /*$amount['user']   = count($members);*/
            foreach($members as $user)
            {
                if(!empty($list[$user->account])){
                    foreach($list[$user->account] as $dept => $workNum) {
                        // 获取项目团队人员键值对信息; 指定用户搜索时进行处理。
                        $participants[$user->account] = $user->realname;
                        if(!empty($account) and !in_array($user->account, $account)) continue;
                        if (!empty($accountType) and !in_array($user->staffType, $accountType)) continue;

                        $user->deptName = zget($deptMap, $dept, '');
                        $user->total    = $workNum;
                        $user->perMonth = round(($user->total / $workHours->workHours) / 8, 2);
                        $clone = clone($user);
                        $userWorkloadList[$dept][] = $clone;

                        $amount['user']     += 1;
                        $amount['total']    += $user->total;
                        $amount['perMonth'] += $user->perMonth;
                    }
                }else{
                    // 获取项目团队人员键值对信息; 指定用户搜索时进行处理。
                    $participants[$user->account] = $user->realname;
                    if(!empty($account) and !in_array($user->account, $account)) continue;
                    if (!empty($accountType) and !in_array($user->staffType, $accountType)) continue;

                    $user->deptName = zget($deptMap, $user->dept, '');
                    $user->total    = 0;
                    $user->perMonth = 0;

                    $userWorkloadList[$user->dept][] = $user;

                    $amount['user']     += 1;
                    $amount['total']    += $user->total;
                    $amount['perMonth'] += $user->perMonth;
                }
            }

            $i = 0;
            $rowspan[$i]['rows']['mark'] = $amount['user'];
            foreach($userWorkloadList as $users)
            {
                foreach($users as $index => $user)
                {
                    if(empty($index)) $rowspan[$i]['rows']['deptName'] = count($users);

                    $data[$i] = new stdclass();

                    if($i == 0) $data[$i]->mark = $project->mark;

                    $data[$i]->deptName = $user->deptName;
                    $data[$i]->realname = $user->realname;
                    $data[$i]->account  = $user->account;
                    $data[$i]->employeeNumber = $user->employeeNumber;
                    $data[$i]->staffType = zget($this->lang->user->staffTypeList, $user->staffType,'');
                    $data[$i]->total    = $user->total;
                    $data[$i]->perMonth = $user->perMonth;

                    $i ++;
                }
            }

            $summary = (object)array('deptName' => $amount['count'], 'realname' => $amount['user'], 'total' => $amount['total'], 'perMonth' => $amount['perMonth']);
            array_push($data, $summary);

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
