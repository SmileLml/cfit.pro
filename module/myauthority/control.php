<?php

class myauthority extends control
{

    /**
     * 我的权限列表
     * @param string $browseType
     * @param int $param
     * @param string $orderBy
     * @param int $recTotal
     * @param int $recPerPage
     * @param int $pageID
     * @param string $type
     */
    public function browse($type = '',$browseType = 'all', $param = 0, $orderBy = 'projectOrRepository_asc', $recTotal = 0, $recPerPage = 20, $pageID = 1){

        $this->app->loadLang('authorityapply');

        $browseType = isset($_POST['browseType']) && $_POST['browseType']? $_POST['browseType'] : $browseType;
        $customType = array_unique(array_filter(array_keys($this->lang->authorityapply->subSystemList)));
        $type = $customType &&  $type ? $type : reset($customType);
        $projectOrRepository = isset($_POST['projectOrRepository']) ? htmlspecialchars(trim($_POST['projectOrRepository'])) : '';
        $role                = isset($_POST['role']) ? htmlspecialchars(trim($_POST['role'])) : '';
        $permsission         = isset($_POST['permsission']) ? htmlspecialchars(trim($_POST['permsission'])) : '';
        $permsissionName         = isset($_POST['permsissionName']) ? htmlspecialchars(trim($_POST['permsissionName'])) : '';

        // 清空搜索的条件和字段
        if($browseType == 'all'){
            $this->session->set('authQuery', '');
            $this->session->set('filed', '');
        }
        if($_POST){
            $this->session->set('filed', '');
            $this->session->set('authQuery', '');
        }
        $fileds =  $this->session->filed;
        $this->app->rawParams['browseType'] = isset($_POST['browseType']) || $browseType == 'bySearch' ? 'bySearch' : $browseType;

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);


        $authority = $this->myauthority->getList($browseType, $orderBy, $pager, $type);;
        $this->view->title      = $this->lang->myauthority->common;
        $this->view->subSystem  = $this->lang->authorityapply->subSystemList;//$this->lang->myauthority->subSystem;
        $this->view->orderBy    = $orderBy;
        $this->view->pager      = $pager;
        $this->view->pageID     = $pageID;
        $this->view->param      = $param;
        $this->view->browseType = $browseType;
        $this->view->type       =  $type;
        $this->view->authority  =  $authority;
        $this->view->projectOrRepository  =  $fileds ? $fileds->projectOrRepository : $projectOrRepository;
        $this->view->role  =  $fileds ? $fileds->role :$role;
        $this->view->permsission  =  $fileds ? $fileds->permsission :$permsission;
        $this->view->permsissionName  =  $fileds ? zget($this->lang->myauthority->svnAuthority,$fileds->permsissionName) :$permsissionName;

        $this->display();
    }

    public function getsvn(){
        $this->myauthority->getSvnAuthorityModel();
    }

    public function getjenkins(){
        $this->myauthority->getJenkinsAuthorityModel();
    }

    public function getgitlab(){
        $this->myauthority->getGitlabAuthorityModel();
    }

    public function getjenkinsUrl(){
        a(json_encode($this->myauthority->getAuthorizeUrl('jenkins')));
    }

    public function getsvnUrl(){
        a(json_encode($this->myauthority->getAuthorizeUrl('svn')));
    }

    public function getgitlabUrl(){
        a(json_encode($this->myauthority->getgitlabUrl('svn')));
    }
}
