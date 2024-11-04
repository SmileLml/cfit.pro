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
    public function exportBugDiscovery($queryType = 'default')
    {
        $this->lang->report->menu->test['alias'] .= ',bugdiscovery';

        /* Get query conditions. */
        $userAccountList = array();
        $accountList     = '';
        $deptList        = '';
        $productList     = '';
        $projectList     = '';

        if($queryType == 'export')
        {
            $params = $this->session->bugDiscoveryQueryData;
            $params = helper::safe64Decode($params);
            $params = json_decode($params, true);

            $accountList = $params['account'];
            $deptList    = $params['dept'];
            $productList = $params['product'];
            $projectList = $params['project'];

            $userAccountList = $accountList;

            $this->post->set('account', $accountList);
            $this->post->set('dept', $deptList);
            $this->post->set('product', $productList);
            $this->post->set('project', $projectList);
        }

        if(!empty($_POST))
        {
            /* Get query conditions. */
            $data = fixer::input('post')->get();
            $data->account = !empty($data->account) ? $data->account : array();
            $data->dept    = !empty($data->dept)    ? $data->dept    : array();
            $data->product = !empty($data->product) ? $data->product : array();
            $data->project = !empty($data->project) ? $data->project : array();

            $data->account = array_filter($data->account, function($value){return !empty($value);});
            $data->dept    = array_filter($data->dept,    function($value){return !empty($value);});
            $data->product = array_filter($data->product, function($value){return !empty($value);});
            $data->project = array_filter($data->project, function($value){return !empty($value);});

            $accountList = !empty($data->account) ? $data->account : array();
            $deptList    = !empty($data->dept)    ? $data->dept    : array();
            $productList = !empty($data->product) ? $data->product : array();
            $projectList = !empty($data->project) ? $data->project : array();

            $this->loadModel('dept');
            $queryAccount = array();
            $queryDept    = array();
            if($accountList) $queryAccount = $accountList;
            $queryAccount = array_filter($queryAccount);
            $queryAccount = array_flip($queryAccount);
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

            if(!empty($deptList) and empty($userAccountList))
            {
                $userList = array();
            }
            else
            {
                $userList = $this->dao->select('id,account,realname,dept')->from(TABLE_USER)
                    ->where('deleted')->eq('0')
                    ->beginIF(!empty($userAccountList))->andWhere('account')->in($userAccountList)->fi()
                    ->orderBy('id_asc')
                    ->fetchAll('account');

                $queryProjectIdList = $projectList;
                $queryProductIdList = $productList;
                if(empty($projectList))
                {
                    $queryProjectIdList = $this->dao->select("group_concat(id) as id")->from(TABLE_PROJECT)->where('type')->eq('project')->andWhere('deleted')->eq('0')->fetch('id');
                }

                $queryProductList = $productList;
                if(empty($productList))
                {
                    $queryProductIdList = $this->dao->select("group_concat(id) as id")->from(TABLE_PRODUCT)->where('deleted')->eq('0')->fetch('id');
                }

                $userList = $this->report->getBugDiscoveryToCreate($userList,  $queryProductIdList, $queryProjectIdList);
                $userList = $this->report->getBugDiscoveryToAssign($userList,  $queryProductIdList, $queryProjectIdList);
                $userList = $this->report->getBugDiscoveryToConfirm($userList, $queryProductIdList, $queryProjectIdList);
            }

            $depts = $this->dept->getOptionMenu();
            unset($depts[0]);

            $users    = $this->loadModel('user')->getPairs('noletter|noclosed|nodeleted');
            $depts    = $depts;
            $projects = array('' => '') + $this->loadModel('project')->getPairs();
            $products = array('' => '') + $this->loadModel('product')->getPairs();

            $fields                      = array();
            $fields['dept']              = $this->lang->report->deptOptions;
            $fields['realname']          = $this->lang->report->accountOptions;
            $fields['projects']          = $this->lang->report->projectOptions;
            $fields['createBugTotal']    = $this->lang->report->discoveryBug;
            $fields['discoveryBugTotal'] = $this->lang->report->discoveryBugTest;
            $fields['defectTotal']       = $this->lang->report->defectUAT;
            $fields['discoveryBugRate']  = $this->lang->report->discoveryBugRate;

            $i = 0;
            foreach($userList as $user)
            {
                $datas[$i]                    = new stdclass();
                $datas[$i]->dept              = zget($depts, $user->dept, '');
                $datas[$i]->realname          = $user->realname;
                $datas[$i]->projects          = $this->report->calculateProject($projects, $user->projects);
                $datas[$i]->createBugTotal    = $user->createBugTotal;
                $datas[$i]->discoveryBugTotal = $user->discoveryBugTotal;
                $datas[$i]->defectTotal       = $user->defectTotal;
                $datas[$i]->discoveryBugRate  = $this->report->calculatePercentage($user->createBugTotal, $user->discoveryBugTotal + $user->createBugTotal + $user->defectTotal);
                $i++;
            }

            $widths = array('dept' => 30, 'realname' => 30, 'projects' => 150, 'createBugTotal' => 20, 'discoveryBugTotal' => 20, 'defectTotal' => 20,'discoveryBugRate' => 20);

            $this->post->set('fields', $fields);
            $this->post->set('rows', $datas);
            $this->post->set('width', $widths);

            if(empty($_POST['fileName'])) $this->post->set('fileName', 'null');
            $this->post->set('kind', 'sheet1');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
            die();
        }

        $this->view->fileName = $this->lang->report->bugDiscovery;

        $this->display();
    }
}
