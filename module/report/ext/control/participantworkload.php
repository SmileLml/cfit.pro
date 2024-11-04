<?php
include '../../control.php';
class myReport extends report
{
    public function participantWorkload($projectID = 0)
    {
        $this->loadModel('project')->setMenu($projectID);
        //查询项目信息
        $project = $this->dao->select('*')->from(TABLE_PROJECT)->where('id')->eq($projectID)->fetch();
        // 获取搜索条件。
        $begin   = $this->post->begin   ? $this->post->begin : '';
        $end     = $this->post->end     ? $this->post->end   : '';
        $account = $this->post->account ? $this->post->account   : array();
        $accountType = $this->post->accountType ? $this->post->accountType   : array();

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
        //$amount['user']   = count($members);
        foreach($members as $user)
        {
            if(!empty($list[$user->account])){
                foreach($list[$user->account] as $dept => $workNum) {
                    // 获取项目团队人员键值对信息; 指定用户搜索时进行处理。
                    $participants[$user->account] = $user->realname;
                    if (!empty($account) and !in_array($user->account, $account)) continue;
                    if (!empty($accountType) and !in_array($user->staffType, $accountType)) continue;

                    $user->deptName = zget($deptMap, $dept, '');
                    $user->total = $workNum;//zget($workloadTotal, $user->account, 0);
                    $user->perMonth = round(($user->total / $project->workHours) / 8, 2);
                    $clone = clone($user);
                    $userWorkloadList[$dept][] = $clone;

                    $amount['user'] += 1;
                    $amount['total'] += $user->total;
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

        $this->view->title      = $this->lang->report->participantWorkload;
        $this->view->position[] = $this->lang->report->participantWorkload;

        $this->view->submenu   = 'program';
        $this->view->projectID = $projectID;

        $this->view->members      = $userWorkloadList;
        $this->view->amount       = $amount;
        $this->view->begin        = $begin;
        $this->view->end          = $end;
        $this->view->participants = $participants;
        $this->view->account      = $account;
        $this->view->accountType      = $accountType;

        $param = json_encode(array('begin' => $begin, 'end' => $end, 'account' => $account, 'accountType' => $accountType));
        $this->view->param        = helper::safe64Encode($param);

        $this->display();
    }
}
