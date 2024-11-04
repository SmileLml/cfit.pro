<?php

class authorityuserviewpoint extends control
{

    /**
     * 权限管理列表 - 用户视角
     * @param string $browseType
     * @param int $param
     * @param string $orderBy
     * @param int $recTotal
     * @param int $recPerPage
     * @param int $pageID
     * @param string $type
     */
    public function browse($dept = 0,$type = 'all',$browseType = 'all',$param = 0, $orderBy = 'id_asc', $recTotal = 0, $recPerPage = 20, $pageID = 1){

        $this->app->loadLang('myauthority');
        $this->app->loadLang('authoritysystemviewpoint');
        $this->app->loadLang('authorityapply');
       /* if ($_SERVER['QUERY_STRING'] != ''){
            $paramsArray = explode('&',$_SERVER['QUERY_STRING']);
            $params = [];
            foreach ($paramsArray as $item) {
                $arr = explode('=',$item);
                $params[$arr[0]] = $arr[1];
                ${$arr[0]} = $arr[1];
            }
        }

        $browseType = isset($browseType) ? $browseType : 'all';
        $type = isset($name) ? $name : $type;
        $search = isset($search) ? $search : '';
        $search = $search ?  htmlspecialchars(trim(urldecode($search))) : null;*/

        $browseType = isset($_POST['browseType'])  && $_POST['browseType'] ? $_POST['browseType'] : $browseType;
        $type = isset($_POST['name']) ? $_POST['name'] : $type;
        $search = isset($_POST['search']) ? $_POST['search'] : '';
        $search = $search ?  htmlspecialchars(trim(urldecode($search))) : null;

        // 清空搜索的条件和字段
        if($browseType == 'all'){
            $this->session->set('userQuery', '');
            $this->session->set('filed', '');
            $this->session->set('viewsub', '');
        }
        if($_POST){
            $this->session->set('filed', '');
            $this->session->set('userQuery', '');
        }

        $fileds =  $this->session->filed;
        $this->app->rawParams['browseType'] = isset($_POST['browseType']) || $browseType == 'bySearch' ? 'bySearch' : $browseType;

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $subSystem              = $this->loadModel('authoritysystemviewpoint')->canLookAccess();
        //$subSystem = array_diff($this->lang->myauthority->subSystem,array_diff($this->lang->myauthority->subSystem,$subSystem));//为了保证顺序
        $subSystem = array_diff($this->lang->authorityapply->subSystemList,array_diff($this->lang->authorityapply->subSystemList,$subSystem));//为了保证顺序
        $subSystem = array_merge($subSystem,array('all' =>'all'));
        $type =  in_array($type,array_keys($subSystem)) || $this->app->user->account == 'admin' ? $type : key($subSystem);

        $type = $this->session->viewsub ? $this->session->viewsub : $type;
        $this->view->title      = $this->lang->authorityuserviewpoint->common;
        $this->view->orderBy    = $orderBy;
        $this->view->pager      = $pager;
        $this->view->pageID     = $pageID;
        $this->view->param      = $param;
        $this->view->browseType = $browseType;
        $this->view->type       = $this->session->viewsub ? $this->session->viewsub : $type;
        $this->view->list       =  $this->authorityuserviewpoint->getList($browseType, $pager ,$type,$dept);
        $this->view->search     =  $fileds ? $fileds->search : $search;
        //$this->view->subSystem  = $this->app->user->account == 'admin' ? array("all" => "全部") + $this->lang->myauthority->subSystem :  array("all" => "全部") +$subSystem;
        $this->view->subSystem  = $this->app->user->account == 'admin' ? array("all" => "全部") + $this->lang->authorityapply->subSystemList :  array("all" => "全部") +$subSystem;
        $this->view->deptTree    = $this->authorityuserviewpoint->getTreeMenu($rootDeptID = 0, array('authorityuserviewpointModel', 'createMemberLink'));
        $this->view->deptID = $dept;


        $this->display();
    }

    /**
     * 详情页
     * @param $account
     * @param string $type
     * @param int $param
     * @param string $orderBy
     * @param int $recTotal
     * @param int $recPerPage
     * @param int $pageID
     */

    public function view($account,$type ='all',$browseType = 'all',$roles = null ,$param = 0, $orderBy = 'id_asc', $recTotal = 0, $recPerPage = 20, $pageID = 1){
        $this->app->loadLang('myauthority');
        $this->app->loadLang('authoritysystemviewpoint');
        $this->app->loadLang('user');
        $this->app->loadLang('authorityapply');

        $browseType = isset($_POST['browseType'])  && $_POST['browseType'] ? $_POST['browseType'] : $browseType;
        $type = isset($_POST['type']) ? $_POST['type'] : $type;
        $viewtype = isset($_POST['viewtype']) ? $_POST['viewtype'] : $type;
       // $role = isset($_POST['role']) ? htmlspecialchars(trim($_POST['role'])) : '';
        $permsissionName = isset($_POST['permsissionName']) ? htmlspecialchars(trim($_POST['permsissionName'])) : '';

        // 清空搜索的条件和字段
        if($browseType == 'all'){
            $this->session->set('viewQuery', '');
            $this->session->set('filed', '');
            $this->session->set('viewsub', '');
        }
        if($_POST){
            $this->session->set('filed', '');
            $this->session->set('viewQuery', '');
            $this->session->set('viewsub', '');
        }

        $fileds =  unserialize($this->session->filed);
        $this->app->rawParams['browseType'] = isset($_POST['browseType']) || $browseType == 'bySearch' ? 'bySearch' : $browseType;

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $subSystem              = $this->loadModel('authoritysystemviewpoint')->canLookAccess();
        //$subSystem = array_diff($this->lang->myauthority->subSystem,array_diff($this->lang->myauthority->subSystem,$subSystem));//为了保证顺序
        $subSystem = array_diff($this->lang->authorityapply->subSystemList,array_diff($this->lang->authorityapply->subSystemList,$subSystem));//为了保证顺序

        $this->view->title      = $this->lang->authorityuserviewpoint->common;
        $this->view->orderBy    = $orderBy;
        $this->view->pager      = $pager;
        $this->view->pageID     = $pageID;
        $this->view->param      = $param;
        $this->view->type       = $type;
        $this->view->viewtype   = $this->session->viewsub ? $this->session->viewsub : $viewtype;
        //$this->view->role       = $fileds ? $fileds->role : $role;
        $this->view->permsissionName = $fileds ?  $fileds->permsissionName :$permsissionName;
        $this->view->info       = $this->authorityuserviewpoint->getByAccountInfo($type,$account,$browseType,$pager,$roles);
        $this->view->userInfo   = $this->loadModel('user')->getUserInfoListByAccounts($account);
        $this->view->depts      = $this->loadModel('dept')->getOptionMenu();
        $this->view->account    = $account;
        $this->view->roles       = $roles;
       // $this->view->subSystem  = $this->app->user->account == 'admin' ? array("all" => "-请选择-") + $this->lang->myauthority->subSystem :  array("all" => "-请选择-") +$subSystem;
        $this->view->subSystem  = $this->app->user->account == 'admin' ? array("all" => "-请选择-") + $this->lang->authorityapply->subSystemList :  array("all" => "-请选择-") +$subSystem;
        $this->display();
    }

}
