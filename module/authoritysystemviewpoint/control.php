<?php

class authoritysystemviewpoint extends control
{

    /**
     * 权限管理列表
     * @param string $browseType
     * @param int $param
     * @param string $orderBy
     * @param int $recTotal
     * @param int $recPerPage
     * @param int $pageID
     * @param string $type
     */
    public function browse($type = '',$browseType = 'all',$param = 0, $orderBy = 'id_asc', $recTotal = 0, $recPerPage = 20, $pageID = 1){

        $this->app->loadLang('myauthority');
        $this->app->loadLang('authorityapply');
        $browseType = isset($_POST['browseType']) && $_POST['browseType'] ? $_POST['browseType']   : $browseType;
        $customType = array_unique(array_filter(array_keys($this->lang->authorityapply->subSystemList)));
        $type = $customType &&  $type ? $type : reset($customType);
        $groupName = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name'])) : '';
        $realName  = isset($_POST['realName']) ? htmlspecialchars(trim($_POST['realName'])) : '';
        $role      = isset($_POST['role']) ? htmlspecialchars(trim($_POST['role'])) : '';
        $projectOrRepository                = isset($_POST['projectOrRepository'])  ? htmlspecialchars(trim($_POST['projectOrRepository']))  : '';

        // 清空搜索的条件和字段
        if($browseType == 'all'){
            $this->session->set('systemQuery', '');
            $this->session->set('filed', '');
        }
        if($_POST){
            $this->session->set('filed', '');
            $this->session->set('systemQuery', '');
        }
        $fileds =  $this->session->filed;
        $this->app->rawParams['browseType'] = isset($_POST['browseType']) || $browseType == 'bySearch' ? 'bySearch' : $browseType;



        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $subSystem              = $this->authoritysystemviewpoint->canLookAccess();

        //$subSystem = array_diff($this->lang->myauthority->subSystem,array_diff($this->lang->myauthority->subSystem,$subSystem));//为了保证顺序
        $subSystem = array_diff($this->lang->authorityapply->subSystemList,array_diff($this->lang->authorityapply->subSystemList,$subSystem));//为了保证顺序
        $type =  in_array($type,array_keys($subSystem)) || $this->app->user->account == 'admin' ? $type : key($subSystem);

        $this->view->title      = $this->lang->authoritysystemviewpoint->common;
        $this->view->orderBy    = $orderBy;
        $this->view->pager      = $pager;
        $this->view->pageID     = $pageID;
        $this->view->param      = $param;
        $this->view->browseType = $browseType;
        $this->view->type       = $type;
        $this->view->list       =  $this->authoritysystemviewpoint->getList($browseType, $pager ,$type,$subSystem);
        $this->view->groupName      =  $fileds ? $fileds->name : $groupName;
        $this->view->realName       = $fileds ? $fileds->realName : $realName;
        $this->view->role           =  $fileds ? $fileds->role : $role;
        $this->view->projectOrRepository   =  $fileds ? $fileds->projectOrRepository : $projectOrRepository;
        //$this->view->subSystem  = $this->app->user->account == 'admin' ? $this->lang->myauthority->subSystem :$subSystem;
        $this->view->subSystem  = $this->app->user->account == 'admin' ? $this->lang->authorityapply->subSystemList :$subSystem;
        $this->display();
    }

    /**
     * 获取研发过程组内用户
     * @param $groupID
     */
    public function groupUsers($groupID){
        $this->view->list  = $this->authoritysystemviewpoint->groupUsers( $groupID);
        $this->display();
    }

    /**
     * 获取gitlab|svn|jenkins 组内用户
     * @param $project
     * @param $role
     * @param $type
     * @param null $search
     * @param int $param
     * @param string $orderBy
     * @param int $recTotal
     * @param int $recPerPage
     * @param int $pageID
     */
    public function authorityUsers( $type,$thirdid,$search = null, $param = 0, $orderBy = 'id_asc', $recTotal = 0, $recPerPage = 20, $pageID = 1){

        $this->app->loadLang('myauthority');
        $this->app->loadLang('authorityapply');
        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);



        $third = $this->authoritysystemviewpoint->getByThirdpatryID($thirdid);
        $permsissions = isset($third->permsission) ?  $third->permsission : '';
        $project = isset($third->projectOrRepository) ? $third->projectOrRepository : '';
        $role = isset($third->role) ? $third->role : '';
        $search  = urldecode($search);

        $this->view->orderBy    = $orderBy;
        $this->view->pager      = $pager;
        $this->view->pageID     = $pageID;
        $this->view->param      = $param;
        $this->view->subSystem  = $this->lang->authorityapply->subSystemList;//$this->lang->myauthority->subSystem ;
        $this->view->type       = $type;
        $this->view->project    = $project;
        $this->view->permsissions       = $permsissions;//zget($this->lang->authoritysystemviewpoint->roleToStr,trim($role));
        $this->view->role       = $role;
        $this->view->search     = $search;
        $this->view->thirdid   = $thirdid;
        $this->view->list  = $this->authoritysystemviewpoint->authorityUsers($project, $role,$type,$search,$permsissions,$pager);
        $this->display();
    }
    /**
     * 数据权限配置
     * @param string $type
     */
    public function dataAccessConfig($type = '',$search = null){
        $this->app->loadLang('myauthority');
        $this->app->loadLang('authorityapply');
        $this->app->loadLang('user');

        $customType = array_unique(array_filter(array_keys($this->lang->authorityapply->subSystemList)));
        $type = $customType &&  $type ? $type : reset($customType);

        $search = $search ?  htmlspecialchars(trim(urldecode($search))) : null;
        if($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['realname'])){
            $this->dao->update(TABLE_DATA_ACCESS)->set('deleted')->eq('1')
                ->set('deletedBy')->eq($this->app->user->account)
                ->where('type')->eq($type)->exec();
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = inlink('dataAccessConfig');

            $this->send($response);
        }
        if($_POST){
            $config = $this->authoritysystemviewpoint->saveConfig($type);
;
            if(dao::isError()) {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = inlink('dataAccessConfig');

            $this->send($response);
        }

        $this->view->subSystem  = $this->lang->authorityapply->subSystemList;//$this->lang->myauthority->subSystem;
        $this->view->type  = $type;
        $this->view->searchName      = $search;
        $this->view->dataAccess      = $this->authoritysystemviewpoint->dataGetList($type,$search);
        $this->view->users = $this->loadModel('user')->getPairs('noclosed|noletter');
        $this->display();
    }

    /**
     * 根据用户账号，查询名字和部门
     * @param $account
     */
    public function ajaxGetNameAndDept($account){

        $this->app->loadLang('user');
        $userInfo = $this->loadModel('user')->getUserInfoListByAccounts($account,'realname,dept,account,role,employeeNumber,staffType');
        $deptName = $this->loadModel('dept')->getDeptPairs(($userInfo[$account])->dept);
        $deptName = trim(implode(',',array_values($deptName)),',');
        $userInfo[$account]->dept = $deptName;
        $userInfo[$account]->role = zget($this->lang->user->roleList,$userInfo[$account]->role,'');
        $userInfo[$account]->staffType = zget($this->lang->user->staffTypeList,$userInfo[$account]->staffType,'');
        die(json_encode($userInfo[$account]));
    }



}
