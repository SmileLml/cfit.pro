<?php

class authorityuserviewpointModel extends model
{

    /**
     * 子系统视角列表
     * @param $browseType
     * @param $orderBy
     * @param null $pager
     * @param $type
     */
    public function getList($browseType,$pager = null ,$type, $dept){

        $authoritys = array();
        $access = array_filter(array_keys($this->canLookAccess()));//当前用户配置的可查看的子系统
        array_push($access,'all');
        $data = fixer::input('post')
            ->get();

       // $name = isset($data->name) ? $data->name : '';
        $search = isset($data->search) ? $data->search : '';
        if($browseType == 'bySearch')
        {
            $query = '';
            if($type == 'dpmp'){
                if($search){
                    $query .= $query  ? " and (user.realname like '%$search ? %' or user.account like '%$search%' or user.email like '%$search%' or user.mobile like '%$search%')" :" (user.realname like '%$search%' or user.account like '%$search%' or user.email like '%$search%' or user.mobile like '%$search%')";
                }
            }else{
                if($search){
                    $query .=  $query  ? " and (user.realname like '%$search%' or user.account like '%$search%' or user.email like '%$search%' or user.mobile like '%$search%')" : "  (user.realname like '%$search%' or user.account like '%$search%' or user.email like '%$search%' or user.mobile like '%$search%')";
                }
            }
            $query = !$query && $this->session->userQuery ? $this->session->userQuery : $query;
            $this->session->set('userQuery', $query);

            $data = !isset($data->name) && $this->session->filed ? $this->session->filed : $data;

            $this->session->set('filed', $data);
            $this->session->set('viewsub', $type);
        }

        $userQuery = $this->session->userQuery;

        //禅道权限
        //if((in_array('dpmp',$access)  ||  $this->app->user->account == 'admin') && in_array($type,array('dpmp','all'))){
       // if((($type == 'all' && in_array('dpmp',$access) ) || ($type =='dpmp' && in_array('dpmp',$access)))   || ($this->app->user->account == 'admin' && in_array($type,array('dpmp','all')))){
        if((in_array($type,array('dpmp','all'))  && in_array('dpmp',$access) ) || ($this->app->user->account == 'admin' && in_array($type,array('dpmp','all')))){

            $dpmpauthoritys = $this->dao->select("user.account,realname,dept.name deptName,(case when ugroup.account !=''  then 'dpmp' else '' end) authtype")->from(TABLE_USER)->alias('user')
                ->leftJoin(TABLE_USERGROUP)->alias('ugroup')
                ->on("user.account = ugroup.account")
                ->leftJoin(TABLE_DEPT)->alias('dept')
                ->on("dept.id = user.dept")
                ->where('user.deleted')->eq(0)
                ->beginIF($browseType == 'bySearch' && $userQuery)->andWhere($userQuery)->fi()
                ->beginIF(!empty($dept))->andWhere("find_in_set($dept,dept.path)")->fi()
               // ->beginIF($search)->andWhere("(user.realname like '%$search%' or user.account like '%$search%' or user.email like '%$search%' or user.mobile like '%$search%')")->fi()
                ->groupBy("ugroup.account ")
                ->fetchAll('account');

        }
        $otherauthoritys = $this->dao->select("user.account,realname,dept.name deptName,group_concat(distinct third.type ) authtype")->from(TABLE_USER)->alias('user')
            ->leftJoin(TABLE_THIRDPARTY_PRIVILEGE)->alias('third')
            ->on("user.account = third.account")
            ->leftJoin(TABLE_DEPT)->alias('dept')
            ->on("dept.id = user.dept")
            ->where('user.deleted')->eq(0)
            ->andWhere('third.deleted')->eq(0)
            ->beginIF($type == 'all' && $this->app->user->account != 'admin') ->andWhere('third.type')->in($access)->fi()
            ->beginIF(!empty($dept))->andWhere("find_in_set($dept,dept.path)")->fi()
            ->beginIF($type != 'all' )->andWhere('third.type')->in($type)->fi()
            //->beginIF($search)->andWhere("(user.realname like '%$search%' or user.account like '%$search%' or user.email like '%$search%' or user.mobile like '%$search%')")->fi()
           ->beginIF($browseType == 'bySearch' && $userQuery)->andWhere($userQuery)->fi()
            ->groupBy("user.account")
            ->fetchAll('account');

        if(isset($dpmpauthoritys) && $otherauthoritys){
            foreach ($dpmpauthoritys as $key => $dpmpauthority) {
                if(isset($otherauthoritys[$key])){
                    $dpmpauthoritys[$key]->authtype .=','. $otherauthoritys[$key]->authtype;
                }
            }
            $authoritys = $dpmpauthoritys;
        }else if(isset($dpmpauthoritys) && !$otherauthoritys){
            $authoritys = $dpmpauthoritys;
        }else{
            $authoritys = $otherauthoritys;
        }
        $pager->recTotal = count($authoritys);
        $start = ($pager->pageID - 1) * $pager->recPerPage;
        $authoritys = array_slice($authoritys, $start, $pager->recPerPage);
        return $authoritys  ;
     }

    /**
     * 详情信息
     * @param $type
     * @param $account
     * @param null $pager
     * @return array
     */
     public function getByAccountInfo($type,$account,$browseType,$pager = null ,$roles){

         $data = fixer::input('post')
             ->get();
         //$role =  isset($data->role) ? $data->role : '';
         $permsissionName = isset($data->permsissionName) ? htmlspecialchars(trim($data->permsissionName)) : '';
         if( strpos($permsissionName,'只读') !==  false || in_array($permsissionName,array('只读','只','读'))){
             $permsissionName  = array_flip($this->lang->myauthority->svnAuthority )['只读'];
         }
         if( strpos($permsissionName,'读写') !==  false  || in_array($permsissionName,array('读写','写'))){
             $permsissionName  = array_flip($this->lang->myauthority->svnAuthority )['读写'];
         }
         $searchFlag = isset($data->searchFlag) ? $data->searchFlag :'';
         $viewtype = isset($data->viewtype) ? $data->viewtype :'';
         $access = array_filter(array_keys($this->canLookAccess()));//当前用户配置的可查看的子系统
         array_push($access,'all');

         $roles = explode(',',$roles);
         if($type == 'all')array_push($roles,'all');
         $subSystems =  $this->lang->authorityapply->subSystemList;

         if($browseType == 'bySearch')
         {
             $query = '';
             if($type == 'dpmp'){
                /* if($role){
                     $query .= $query  ? " and tgroup.name like '$role'" :" tgroup.name like '$role'";
                 }*/
                 if($permsissionName){
                     $query .= $query  ? " and tgroup.name like '$permsissionName'" :" tgroup.name like '$permsissionName'";
                 }
             }else{
                /* if($role){
                     $query .= $query  ? " and tgroup.name like '$role'" :" tgroup.name like '$role'";
                 }*/
                 if($permsissionName){
                     $query .= $query  ? " and permsission like '%$permsissionName%'" :"  permsission like '%$permsissionName%'";
                 }
             }

             $query = !$query && $this->session->viewQuery ? $this->session->viewQuery : $query;
             $this->session->set('viewQuery', $query);
            // $data = !$search && $this->session->filed ? $this->session->filed : $search;

             $nowdata = (isset($data->permsissionName) && empty($data->permsissionName)  ||  !isset($data->permsissionName)) ? $this->session->filed : serialize($data);
             $this->session->set('filed', $nowdata);

             $viewtype = !$viewtype && $this->session->viewsub ? $this->session->viewsub : $viewtype;
             $this->session->set('viewsub', $viewtype);

         }

         $viewQuery = $this->session->viewQuery;

         $authoritys = array();
         if(($type == 'all' && in_array('dpmp',$access) && in_array('dpmp',$roles))   || ($this->app->user->account == 'admin' && in_array($type,array('dpmp','all'))  ) || (in_array($type,$roles)&& $type == 'dpmp')){
             if($viewtype && in_array($viewtype,array('dpmp','all'))){
                 $dpmpauthoritys = $this->dao->select("ugroup.account,ugroup.project,tgroup.name,tgroup.desc,(case when ugroup.account !=''  then 'dpmp' else '' end) type")->from(TABLE_GROUP)->alias('tgroup')
                     ->leftJoin(TABLE_USERGROUP)->alias('ugroup')
                     ->on('tgroup.id = ugroup.group')
                     ->where('ugroup.account')->eq($account)
                    // ->beginIF($role)->andWhere('tgroup.name')->like("%$role%")->fi()
                    // ->beginIF($permsissionName)->andWhere('tgroup.name')->like("%$permsissionName%")->fi()
                     ->beginIF($browseType == 'bySearch' && $viewQuery)->andWhere(str_replace('permsission','tgroup.name',$viewQuery))->fi()
                     ->fetchAll();
             }else if(!$viewtype){

                 $dpmpauthoritys = $this->dao->select("ugroup.account,ugroup.project,tgroup.name,tgroup.desc,(case when ugroup.account !=''  then 'dpmp' else '' end) type")->from(TABLE_GROUP)->alias('tgroup')
                     ->leftJoin(TABLE_USERGROUP)->alias('ugroup')
                     ->on('tgroup.id = ugroup.group')
                     ->where('ugroup.account')->eq($account)
                    // ->beginIF($role)->andWhere('tgroup.name')->like("%$role%")->fi()
                    // ->beginIF($permsissionName)->andWhere('tgroup.name')->like("%$permsissionName%")->fi()
                    ->beginIF($browseType == 'bySearch' && $viewQuery)->andWhere( str_replace('permsission','tgroup.name',$viewQuery))->fi()
                    ->fetchAll();

             }
         }

         $otherauthoritys = $this->dao->select("account,projectOrRepository as project,`role` as name,roleDesc as `desc`,`type`,permsission,expires")->from(TABLE_THIRDPARTY_PRIVILEGE)
             ->where('account')->eq($account)
             ->andWhere('1 = 1')
             //->beginIF($type == 'all' && $this->app->user->account != 'admin') ->andWhere('type')->in($access)->fi()
             ->beginIF($roles && !$viewtype)->andWhere('type')->in($roles)->fi()
             ->beginIF($type != 'all' && $searchFlag)->andWhere('type')->eq($type)->fi()
             //->beginIF($role)->andWhere('`role`')->like("%$role%")->fi()
            // ->beginIF($permsissionName)->andWhere('permsission')->like("%$permsissionName%")->fi()
             ->beginIF($browseType == 'bySearch' && $viewQuery)->andWhere($viewQuery)->fi()
             ->beginIF($viewtype && $viewtype != 'all')->andWhere('type')->in($viewtype)->fi()
             ->beginIF($viewtype && $viewtype == 'all')->andWhere('type')->in($roles)->fi()
             ->andWhere('deleted')->eq(0)
             ->fetchAll();

         if(isset($dpmpauthoritys) && $otherauthoritys){
             $authoritys = array_merge($dpmpauthoritys,$otherauthoritys);
         }else if(isset($dpmpauthoritys) && !$otherauthoritys){
             $authoritys = $dpmpauthoritys;
         }else{
             $authoritys = $otherauthoritys;
         }
         $pager->recTotal = count($authoritys);
         $start = ($pager->pageID - 1) * $pager->recPerPage;
         $authoritys = array_slice($authoritys, $start, $pager->recPerPage);
         return $authoritys;
     }


    /**
     * 用户已配置的数据权限
     * @return mixed
     */
    public function canLookAccess()
    {
        //查询已配置权限的子系统
        $access = $this->dao->select("*")->from(TABLE_DATA_ACCESS)
            ->where('deleted')->eq(0)
            ->andWhere('account')->eq($this->app->user->account)
            ->fetchPairs('type','typeName');

        $this->app->loadLang('authorityapply');
        // 获取后台自定义配置的子系统
        $subSystem = $this->lang->authorityapply->subSystemList;
        //比较，并获取后台自定义的子系统存在且权限已配置的子系统
        $access = array_intersect_key($subSystem,$access);

        return $access;
    }

    /**
     * Get the treemenu of departments.
     *
     * @param  int        $rootDeptID
     * @param  string     $userFunc
     * @param  int        $param
     * @access public
     * @return string
     */
    public function getTreeMenu($rootDeptID = 0, $userFunc, $param = 0)
    {
        $deptMenu = array();
        $stmt = $this->dbh->query($this->loadModel('dept')->buildMenuQuery($rootDeptID));
        while ($dept = $stmt->fetch()) {
            $linkHtml = call_user_func($userFunc, $dept, $param);
            if (isset($deptMenu[$dept->id]) and !empty($deptMenu[$dept->id])) {
                if (!isset($deptMenu[$dept->parent])) $deptMenu[$dept->parent] = '';
                $deptMenu[$dept->parent] .= "<li>$linkHtml";
                $deptMenu[$dept->parent] .= "<ul>" . $deptMenu[$dept->id] . "</ul>\n";
            } else {
                if (isset($deptMenu[$dept->parent]) and !empty($deptMenu[$dept->parent])) {
                    $deptMenu[$dept->parent] .= "<li>$linkHtml\n";
                } else {
                    $deptMenu[$dept->parent] = "<li>$linkHtml\n";
                }
            }
            $deptMenu[$dept->parent] .= "</li>\n";
        }

        krsort($deptMenu);
        $deptMenu = array_pop($deptMenu);
        $lastMenu = "<ul class='tree' data-ride='tree' data-name='tree-dept'>{$deptMenu}</ul>\n";
        return $lastMenu;
    }

    /**
     * Create the member link.
     *
     * @param  int    $dept
     * @access public
     * @return string
     */
    public function createMemberLink($dept)
    {
        $linkHtml = html::a(helper::createLink('authorityuserviewpoint', 'browse', "dept={$dept->id}"), $dept->name, '_self', "id='dept{$dept->id}'");
        return $linkHtml;
    }
}

