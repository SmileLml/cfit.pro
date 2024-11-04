<?php
include '../../control.php';
class myReport extends report
{
    /**
     * Browse the bug tester report.
     *
     * @param  string $queryType
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function bugTester($queryType = 'default', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $this->lang->report->menu->test['alias'] .= ',bugtester';

        /* Get query conditions. */
        $userAccountList = array();
        $end             = '';
        $begin           = '';
        $accountList     = '';
        $deptList        = '';
        $projectList     = '';

        $newBegin = '';
        $newEnd   = '';

        if($queryType == 'page')
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
            $data = fixer::input('post')->get();

            if(!empty($data->begin)) $begin = $data->begin;
            if(!empty($data->end)) $end = $data->end;

            $data->begin   = !empty($data->begin)   ? $data->begin   : '2020-01-01';
            $data->end     = !empty($data->end)     ? $data->end     : date('Y-m-d');
            $data->account = !empty($data->account) ? $data->account : array();
            $data->dept    = !empty($data->dept)    ? $data->dept    : array();
            $data->project = !empty($data->project) ? $data->project : array();

            $data->account = array_filter($data->account, function($value){return !empty($value);});
            $data->dept    = array_filter($data->dept,    function($value){return !empty($value);});
            $data->project = array_filter($data->project, function($value){return !empty($value);});

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
        }


        /* When there is no active query, the data will not be displayed. */
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

        /* Load pager */
        $this->app->loadClass('pager', true);
        $pager = pager::init($recTotal, $recPerPage, $pageID);
        $userInfoList = $this->dao->select('account,realname,dept')->from(TABLE_USER)
            ->where('account')->in($queryAccountList)
            ->andWhere('deleted')->eq('0')
            ->page($pager)
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

        $param     = array('begin' => $begin, 'end' => $end, 'account' => $accountList, 'dept' => $deptList, 'project' => $projectList);
        $queryData = helper::safe64Encode(json_encode($param));
        $this->session->set('bugTesterQueryData', $queryData);
        $this->app->rawParams['queryType'] = 'page';

        $depts = $this->loadModel('dept')->getOptionMenu();
        unset($depts[0]);

        $this->view->title      = $this->lang->report->bugTester;
        $this->view->position[] = $this->lang->report->bugTester;

        $this->view->userInfoList = $userInfoList;
        $this->view->queryType    = $queryType;
        $this->view->pager        = $pager;
        $this->view->users        = $this->loadModel('user')->getPairs('noletter|noclosed|nodeleted');
        $this->view->depts        = $depts;
        $this->view->projects     = array('' => '') + $this->loadModel('project')->getPairsCodeName();
        $this->view->begin        = $begin;
        $this->view->end          = $end;
        $this->view->deptList     = $deptList;
        $this->view->accountList  = $accountList;
        $this->view->projectList  = $projectList;
        $this->view->submenu      = 'test';

        $this->display();
    }
}
