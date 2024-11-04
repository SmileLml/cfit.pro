<?php
include '../../control.php';
class myReport extends report
{
    /**
     * Browse the bug discovery rate report.
     *
     * @param  string $queryType
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function bugDiscovery($queryType = 'default', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $this->lang->report->menu->test['alias'] .= ',bugdiscovery';

        /* Get query conditions. */
        $userAccountList = array();
        $accountList     = '';
        $deptList        = '';
        $productList     = '';
        $projectList     = '';

        if($queryType == 'page')
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

        /* Load pager */
        $this->app->loadClass('pager', true);
        $pager = pager::init($recTotal, $recPerPage, $pageID);

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
        }

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
                ->page($pager)
                ->fetchAll('account');

            /* When there is no active query, the data will not be displayed. */
            if(empty($accountList) and empty($deptList) and empty($productList) and empty($projectList)) $userList = array();

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

        $param     = array('account' => $accountList, 'dept' => $deptList, 'project' => $projectList, 'product' => $productList);
        $queryData = helper::safe64Encode(json_encode($param));
        $this->session->set('bugDiscoveryQueryData', $queryData);
        $this->app->rawParams['queryType'] = 'page';

        $depts = $this->loadModel('dept')->getOptionMenu();
        unset($depts[0]);

        $this->view->title      = $this->lang->report->bugDiscovery;
        $this->view->position[] = $this->lang->report->bugDiscovery;

        $this->view->queryType   = $queryType;
        $this->view->pager       = $pager;
        $this->view->userList    = $userList;
        $this->view->users       = $this->loadModel('user')->getPairs('noletter|noclosed|nodeleted');
        $this->view->depts       = $depts;
        $this->view->projects    = array('' => '') + $this->loadModel('project')->getPairs();
        $this->view->products    = array('' => '') + $this->loadModel('product')->getPairs();
        $this->view->accountList = $accountList;
        $this->view->deptList    = $deptList;
        $this->view->productList = $productList;
        $this->view->projectList = $projectList;
        $this->view->submenu     = 'test';

        $this->display();
    }
}
