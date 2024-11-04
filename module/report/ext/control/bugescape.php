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
    public function bugEscape($queryType = 'default', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $this->lang->report->menu->test['alias'] .= ',bugescape';

        /* Get query conditions. */
        $projectList = '';
        $deptList    = '';

        if($queryType == 'page')
        {
            $params = $this->session->bugEscapeQueryData;
            $params = helper::safe64Decode($params);
            $params = json_decode($params, true);

            $deptList    = $params['dept'];
            $projectList = $params['project'];

            $this->post->set('dept', $deptList);
            $this->post->set('project', $projectList);
        }

        /* Load pager */
        $this->app->loadClass('pager', true);
        $pager = pager::init($recTotal, $recPerPage, $pageID);

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
        }

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
                ->page($pager)
                ->fetchAll('id');

            /* When there is no active query, the data will not be displayed. */
            if(empty($deptList) and empty($projectList)) $projectDataList = array();

            $projectDataList = $this->report->getBugEscapeList($projectDataList);
        }

        $param     = array('dept' => $deptList, 'project' => $projectList);
        $queryData = helper::safe64Encode(json_encode($param));
        $this->session->set('bugEscapeQueryData', $queryData);
        $this->app->rawParams['queryType'] = 'page';

        $this->view->title      = $this->lang->report->bugEscape;
        $this->view->position[] = $this->lang->report->bugEscape;

        $this->view->queryType       = $queryType;
        $this->view->pager           = $pager;
        $this->view->projectDataList = $projectDataList;
        $this->view->projects        = array('' => '') + $this->loadModel('project')->getPairsCodeName();
        $this->view->depts           = array('' => '') + $this->loadModel('dept')->getSpecifyLevelDeptList();
        $this->view->projectList     = $projectList;
        $this->view->deptList        = $deptList;
        $this->view->submenu         = 'test';

        $this->display();
    }
}
