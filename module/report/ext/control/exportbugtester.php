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
    public function exportBugTester($queryType = 'default')
    {
        $this->lang->report->menu->test['alias'] .= ',bugtester';

        /* Get query conditions. */
        $userAccountList = array();
        $end             = '';
        $begin           = '';
        $accountList     = '';
        $deptList        = '';
        $projectList     = '';

        if($queryType == 'export')
        {
            $params = $this->session->bugTesterQueryData;
            $params = helper::safe64Decode($params);
            $params = json_decode($params, true);

            $begin       = $params['begin'];
            $end         = $params['end'];
            $accountList = $params['account'];
            $deptList    = $params['dept'];
            $projectList = $params['project'];

            $userAccountList = $accountList;

            $this->post->set('begin', $begin);
            $this->post->set('end', $end);
            $this->post->set('account', $accountList);
            $this->post->set('dept', $deptList);
            $this->post->set('project', $projectList);
        }

        if(!empty($_POST))
        {
            /* Get query conditions. */
            $data          = fixer::input('post')->get();
            $data->begin   = !empty($data->begin)   ? $data->begin   : '2020-01-01';
            $data->end     = !empty($data->end)     ? $data->end     : date('Y-m-d');
            $data->account = !empty($data->account) ? $data->account : array();
            $data->dept    = !empty($data->dept)    ? $data->dept    : array();
            $data->project = !empty($data->project) ? $data->project : array();

            $data->account = array_filter($data->account, function($value){return !empty($value);});
            $data->dept    = array_filter($data->dept,    function($value){return !empty($value);});
            $data->project = array_filter($data->project, function($value){return !empty($value);});

            $begin       = $data->begin;
            $end         = $data->end;
            $accountList = !empty($data->account) ? $data->account : array();
            $deptList    = !empty($data->dept)    ? $data->dept    : array();
            $projectList = !empty($data->project) ? $data->project : array();

            $this->loadModel('dept');
            $queryAccount = array();
            $queryDept    = array();
            if($accountList) $queryAccount = $accountList;
            $queryAccount = array_filter($queryAccount);
            $queryAccount = array_flip($queryAccount);

            $deptUserAccountList = [];
            if($deptList)
            {
                foreach($deptList as $deptID)
                {
                    if(empty($deptID)) continue;
                    $childDepts = $this->dept->getAllChildID($deptID);
                    foreach($childDepts as $childDeptID) $queryDept[$childDeptID] = $childDeptID;
                }
                $deptUserList = $this->dept->getUserPairsByDeptID($queryDept);
                foreach($deptUserList as $account => $realname) $deptUserAccountList[] = $account;
            }

            if(empty($accountList))
            {
                $userAccountList = $deptUserAccountList;
            }
            else
            {
                $userAccountList = $accountList;
            }

            if(!empty($deptList))
            {
                $userAccountList = array_intersect($userAccountList, $deptUserAccountList);
            }

            $newBegin = $data->begin . ' 00:00:00';
            $newEnd   = $data->end   . ' 23:59:59';

            if(empty($accountList) and empty($deptList) and empty($projectList))
            {
                $queryAccountList = array();
            }
            else
            {
                $skipUserAccountList = false;
                if(empty($accountList) && empty($projectList) && empty($deptList)) $skipUserAccountList = true;
                $createBugs = $this->report->getTesterBugList($userAccountList, $projectList, $newBegin, $newEnd, $skipUserAccountList);
                $effectBugs = $this->report->getTesterEffectiveBugList($userAccountList, $projectList, $newBegin, $newEnd, $skipUserAccountList);
                $cases      = $this->report->getTesterCaseList($userAccountList, $projectList, $newBegin, $newEnd, $skipUserAccountList);
                $runs       = $this->report->getTesterCaseRunList($userAccountList, $projectList, $newBegin, $newEnd, $skipUserAccountList);

                $userList = $this->report->mergeDataByOpenedBy(array(), $userAccountList, $createBugs, $effectBugs, $cases, $runs);
                $queryAccountList = array_keys($userList);
            }

            $userInfoList = $this->dao->select('account,realname,dept')->from(TABLE_USER)
                ->where('account')->in($queryAccountList)
                ->andWhere('deleted')->eq('0')
                ->fetchAll();

            $queryAccountList = array();
            foreach($userInfoList as $user)
            {
                $validUser            = $userList[$user->account];
                $user->caseTotal      = $validUser->caseTotal;
                $user->runs           = $validUser->runTotal;
                $user->bugTotal       = $validUser->createBugTotal;
                $user->effectiveTotal = $validUser->effectBugTotal;
                $user->projects       = $validUser->projects;

                $queryAccountList[] = $user->account;
            }

            $depts = $this->loadModel('dept')->getOptionMenu();
            $projects = $this->loadModel('project')->getPairsCodeName();

            $fields                   = array();
            $fields['realname']       = $this->lang->report->fullname;
            $fields['dept']           = $this->lang->report->deptOptions;
            $fields['projectName']    = $this->lang->report->participateProject;
            $fields['caseTotal']      = $this->lang->report->writtenCases;
            $fields['runs']           = $this->lang->report->executedCases;
            $fields['bugTotal']       = $this->lang->report->submittedBugs;
            $fields['effectiveTotal'] = $this->lang->report->effectiveBugs;

            $i = 0;
            foreach($userInfoList as $user)
            {
                $datas[$i]                 = new stdclass();
                $datas[$i]->realname       = $user->realname;
                $datas[$i]->dept           = zget($depts, $user->dept, '');
                $datas[$i]->projectName    = $this->report->calculateProject($projects, $user->projects);
                $datas[$i]->caseTotal      = $user->caseTotal;
                $datas[$i]->runs           = $user->runs;
                $datas[$i]->bugTotal       = $user->bugTotal;
                $datas[$i]->effectiveTotal = $user->effectiveTotal;
                $datas[$i]->autoCases      = $user->categories;
                $datas[$i]->rate           = $user->rate;
                $i++;
            }

            $widths = array('realname' => 20, 'dept' => 20, 'projectName' => 60, 'caseTotal' => 20, 'runs' => 20, 'bugTotal' => 20, 'autoCases' => 20, 'effectiveTotal' => 20, 'rate' => 20);

            $this->post->set('fields', $fields);
            $this->post->set('rows', $datas);
            $this->post->set('width', $widths);

            if(empty($_POST['fileName'])) $this->post->set('fileName', 'null');
            $this->post->set('kind', 'sheet1');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
            die();
        }

        $this->view->fileName = $this->lang->report->bugTester;

        $this->display();
    }
}
