<?php

class authoritysystemviewpointModel extends model
{

    /**
     * 子系统视角列表
     * @param $browseType
     * @param $orderBy
     * @param null $pager
     * @param $type
     */
    public function getList($browseType,$pager = null ,$type,$subSystem){
        $authoritys = array();
        $where = "where 1 = 1";
        $data = fixer::input('post')
            ->get();

        $groupName = isset($data->name) ? htmlspecialchars(trim($data->name)) : '';
        $realName                =  isset($data->realName) ? htmlspecialchars(trim($data->realName)) : '';
        $projectOrRepository = isset($data->projectOrRepository) ? htmlspecialchars(trim($data->projectOrRepository)) : '';
        $role                = isset($data->role) ? htmlspecialchars(trim($data->role)) : '';
        $account             = isset($data->realName) ? htmlspecialchars(trim($data->realName)) : '';
        if(!$subSystem && $this->app->user->account != 'admin'){
            return $authoritys;
        }
        if($browseType == 'bySearch')
        {
            $query = '';
            if($type == 'dpmp'){
                if(isset($groupName) && $groupName){
                    $query .= " and groupUser.name like '%$groupName%'" ;
                }
                if(isset($realName) && $realName){
                    $query .= " and groupUser.users like '%$realName%'"  ;
                }
            }else{
                if(strpos($role,'只读') !==  false || in_array($role,array('只读','只','读'))){
                    $role  = array_flip($this->lang->myauthority->svnAuthority )['只读'];
                }
                if( strpos($role,'读写') !==  false || in_array($role,array('读写','写'))){
                    $role  = array_flip($this->lang->myauthority->svnAuthority )['读写'];
                }

                if(isset($projectOrRepository) && $projectOrRepository){
                    $query .=  " and thirds.projectOrRepository like '%$projectOrRepository%'" ;
                }
                if(isset($role) && $role){
                    $query .=  " and thirds.permsission like '%$role%'"  ;
                }
                if(isset($account) && $account){
                    $query .=  " and thirds.users like '%$account%'" ;

                }

            }

            $query = !$query && $this->session->systemQuery ? $this->session->systemQuery : $query;
            $this->session->set('systemQuery', $query);
            $data = !isset($data->projectOrRepository) && $this->session->filed ? $this->session->filed : $data;
            $this->session->set('filed', $data);
        }
        if($this->session->systemQuery == false) $this->session->set('systemQuery', ' ');
        $systemQuery = ' where 1 = 1 '.$this->session->systemQuery;

        if(in_array($type,array_keys($subSystem)) || $this->app->user->account == 'admin'){
            if($type == 'dpmp'){
               /* $groupName = isset($data->name) ? htmlspecialchars(trim($data->name)) : '';
                $realName                =  isset($data->realName) ? htmlspecialchars(trim($data->realName)) : '';
                $where .= $groupName ? " and groupUser.name like '%$groupName%'" : '';
                $where .= $realName ? " and groupUser.users like '%$realName%'" : '';*/
                $authoritys =  $this->dao->query("select groupUser.id,groupUser.name,groupUser.desc,groupUser.accounts,groupUser.users from (SELECT tgroup.id,tgroup.name,tgroup.desc,group_concat(distinct ugroup.account)accounts,group_concat(distinct user.realname) users 
               FROM `zt_group` AS tgroup LEFT JOIN `zt_usergroup` AS ugroup 
               ON tgroup.id = ugroup.group 
               LEFT JOIN `zt_user` AS user 
               ON user.account = ugroup.account 
               and user.deleted ='0' 
               wHeRe 1 = 1 
               gRoUp bY tgroup.id ) groupUser $systemQuery oRdEr bY `id` asc")->fetchAll();

                $pager->recTotal = count($authoritys);
                $start = ($groupName|| $realName) ? 0:($pager->pageID - 1) * $pager->recPerPage;
                $authoritys = array_slice($authoritys, $start, $pager->recPerPage);
            }else{
              /*  $projectOrRepository = isset($data->projectOrRepository) ? htmlspecialchars(trim($data->projectOrRepository)) : '';
                $role                = isset($data->role) ? htmlspecialchars(trim($data->role)) : '';
                $account             = isset($data->realName) ? htmlspecialchars(trim($data->realName)) : '';

                if( strpos($role,'只读') !==  false ){
                    $role  = array_flip($this->lang->myauthority->svnAuthority )['只读'];
                }
                if( strpos($role,'读写') !==  false ){
                    $role  = array_flip($this->lang->myauthority->svnAuthority )['读写'];
                }
                $where .= $projectOrRepository ? " and thirds.projectOrRepository like '%$projectOrRepository%'" : '';
                $where .= $role ? " and thirds.permsission like '%$role%'" : '';
                $where .= $account ? " and thirds.users like '%$account%'" : '';*/

                $authoritys = $this->dao->query("select thirds.* from (SELECT third.*,group_concat(distinct user.realname) users FROM `zt_thirdparty_privilege` AS third 
                 LEFT JOIN `zt_user` AS user 
                 ON third.account = user.account and user.deleted ='0' 
                 wHeRe third.type = '$type'  
                 AND third.deleted = '0' 
                 gRoUp bY third.projectOrRepository,third.role,third.permsission) thirds $systemQuery oRdEr bY `projectOrRepository` asc")->fetchAll();

                $pager->recTotal = count($authoritys);
                $start = ($projectOrRepository|| $role) ? 0:($pager->pageID - 1) * $pager->recPerPage;
                $authoritys = array_slice($authoritys, $start, $pager->recPerPage);
            }
        }

        return $authoritys  ;
     }

    /**
     * @param $project
     * @param $role
     * @param $type
     * @param $search
     * @param null $pager
     * @return mixed
     */
     public function authorityUsers($project, $role,  $type,$search,$permsissions, $pager = null ){

         $search = $search ? htmlspecialchars(trim($search)) : '';
         $authoritys = $this->dao->select("third.id,user.account,user.realname,dept.name deptName")->from( TABLE_USER)->alias('user')
             ->leftJoin(TABLE_THIRDPARTY_PRIVILEGE)->alias('third')
             ->on('third.account = user.account and user.deleted ="0"')
             ->leftJoin(TABLE_DEPT)->alias('dept')
             ->on('dept.id = user.dept')
             ->where('third.type')->eq($type)
             ->andWhere('third.permsission')->eq($permsissions)
             ->andWhere('third.role')->eq($role)
             ->andWhere('third.projectOrRepository')->eq($project)
             ->beginIF($search)->andWhere("(user.realname like '%$search%' or user.account like '%$search%' )")->fi()
             ->andWhere('third.deleted')->eq(0)
             ->page($pager)
             ->fetchAll();

         return $authoritys;
     }
    /**
     * 获取研发过程组内用户
     * @param $groupID
     * @return mixed
     */
     public function groupUsers($groupID){
         $groupUsers =  $this->dao->select("tgroup.id,tgroup.name,tgroup.desc,group_concat(distinct ugroup.account)accounts,group_concat(distinct user.realname) users")->from(TABLE_GROUP)->alias('tgroup')
             ->leftJoin(TABLE_USERGROUP)->alias('ugroup')
             ->on('tgroup.id = ugroup.group ')
             ->leftJoin(TABLE_USER)->alias('user')
             ->on('user.account = ugroup.account and user.deleted ="0"')
             ->where("1=1")
             ->andWhere("tgroup.id")->eq($groupID)
             ->groupBy('tgroup.id')
             ->fetch();
         return $groupUsers;
     }
    /**
     * 数据权限配置列表
     * @param $type
     * @param $searchFlag
     * @return mixed
     */
    public function dataGetList($type,$searchFlag)
    {
        $search = $searchFlag ? htmlspecialchars(trim($searchFlag)) : '';
        $access = $this->dao->select("dataaccess.*,user.role,user.employeeNumber,user.staffType")->from(TABLE_DATA_ACCESS)->alias('dataaccess')
            ->leftJoin(TABLE_USER)->alias('user')->on('dataaccess.account = user.account')
            ->where('dataaccess.type')->eq($type)
            ->beginIF($searchFlag)->andWhere("(dataaccess.realname like '%$search%' or dataaccess.account like '%$search%' or dataaccess.deptName like '%$search%')")->fi()
            ->andWhere('dataaccess.deleted')->eq(0)
            ->fetchAll();
        return $access;
    }

    /**
     * 保存数据权限
     * @param $type
     * @return array
     */
    public function saveConfig($type){
        $this->app->loadLang('myauthority');
        $data = fixer::input('post')
            ->get();
        $users = $this->loadModel('user')->getPairs('noclosed|noletter');
        $configIDs = array();
        if($data ){
            $this->dao->update(TABLE_DATA_ACCESS)->set('deleted')->eq('1')
                ->set('deletedBy')->eq($this->app->user->account)
                ->where('type')->eq($type)->exec();
        }
        foreach ($data->ids as $key => $value) {
            if(!$data->realname[$key]){
                continue;
            }
            $config = new stdClass();
            $config->type     = $type;
            $config->typeName = zget($this->lang->authorityapply->subSystemList,$type);
            $config->realname = zget($users,$data->realname[$key]);
            $config->account  = $data->account[$key];
            $config->deptName = $data->deptName[$key];
            $config->createdBy = $this->app->user->account;
            $this->dao->insert(TABLE_DATA_ACCESS)->data($config)->autoCheck()->exec();
            $id = $this->dao->lastInsertID();
            $configIDs[] = $id;
        }
        return $configIDs;
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

    public function getByThirdpatryID($ID){
        return $this->dao->findByID($ID)->from(TABLE_THIRDPARTY_PRIVILEGE)->fetch();
    }
}

