<?php
/**
 * Project: chengfangjinke
 * Method: grantUserView
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/9
 * Time: 7:51
 * Desc: This is the code comment. This method is called grantUserView.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param string $account
 * @param array $acls
 * @param string $projects
 * @return array|mixed
 */
public function grantUserView($account = '', $acls = array(), $projects = '')
{
    if(empty($account)) $account = $this->session->user->account;
    if(empty($account)) return array();
    if(empty($acls) and !empty($this->session->user->rights['acls']))  $acls     = $this->session->user->rights['acls'];
    if(!$projects and isset($this->session->user->rights['projects'])) $projects = $this->session->user->rights['projects'];

    /* If userview is empty, init it. */
    $userView = $this->dao->select('*')->from(TABLE_USERVIEW)->where('account')->eq($account)->fetch();
    if(empty($userView)) $userView = $this->computeUserView($account);

    /* Get opened projects, programs, products and set it to userview. */
    $openedPrograms = $this->dao->select('id')->from(TABLE_PROJECT)->where('acl')->eq('open')->andWhere('type')->eq('program')->fetchAll('id');
    $openedProjects = $this->dao->select('id')->from(TABLE_PROJECT)->where('acl')->eq('open')->andWhere('type')->eq('project')->fetchAll('id');
    $openedProducts = $this->dao->select('id')->from(TABLE_PRODUCT)->where('acl')->eq('open')->fetchAll('id');

    $openedPrograms = join(',', array_keys($openedPrograms));
    $openedProducts = join(',', array_keys($openedProducts));
    $openedProjects = join(',', array_keys($openedProjects));

    /* 成方金科定制，产品根据产品线的承建部门选择。 */
    /* Get productline depts. */
    $deptProducts = [];
    if(isset($this->session->user->dept))
    {
        /*
        $prds = $this->dao->select('t1.id,t2.depts')->from(TABLE_PRODUCT)->alias('t1')
            ->leftJoin(TABLE_PRODUCTLINE)->alias('t2')->on('t1.line = t2.id')
            ->fetchAll();
        foreach($prds as $product)
        {
            if(in_array($this->session->user->dept, explode(',', $product->depts)))
            {
                $deptProducts[] = $product->id;
            }
        }
        */
        //2023-10-11 对上述注释代码进行了优化
        $lineList = $this->dao->select('id')->from(TABLE_PRODUCTLINE)->where("FIND_IN_SET('{$this->session->user->dept}',depts)")->fetchAll();
        if($lineList){
            $lineIds = array_column($lineList, 'id');
            $productList = $this->dao->select('id')->from(TABLE_PRODUCT)->where('line')->in($lineIds)->fetchAll();
            if($productList){
                $deptProducts = array_column($productList, 'id');
            }
        }
    }
    $deptProducts = join(',', $deptProducts);

    $userView->programs = rtrim($userView->programs, ',') . ',' . $openedPrograms;
    $userView->products = rtrim($userView->products, ',') . ',' . $openedProducts . ',' . $deptProducts;
    $userView->projects = rtrim($userView->projects, ',') . ',' . $openedProjects;

    if(isset($_SESSION['user']->admin)) $isAdmin = $this->session->user->admin;
    if(!isset($isAdmin)) $isAdmin = strpos($this->app->company->admins, ",{$account},") !== false;

    if(!empty($acls['programs']) and !$isAdmin)
    {
        $grantPrograms = '';
        foreach($acls['programs'] as $programID)
        {
            if(strpos(",{$userView->programs},", ",{$programID},") !== false) $grantPrograms .= ",{$programID}";
        }
        $userView->programs = $grantPrograms;
    }
    if(!empty($acls['projects']) and !$isAdmin)
    {
        $grantProjects = '';
        /* If is project admin, set projectID to userview. */
        if($projects) $acls['projects'] = array_merge($acls['projects'], explode(',', $projects));
        foreach($acls['projects'] as $projectID)
        {
            if(strpos(",{$userView->projects},", ",{$projectID},") !== false) $grantProjects .= ",{$projectID}";
        }
        $userView->projects = $grantProjects;
    }
    if(!empty($acls['products']) and !$isAdmin)
    {
        $grantProducts = '';
        foreach($acls['products'] as $productID)
        {
            if(strpos(",{$userView->products},", ",{$productID},") !== false) $grantProducts .= ",{$productID}";
        }
        $userView->products = $grantProducts;
    }

    /* Set opened sprints and stages into userview. */
    $openedSprints = $this->dao->select('id')->from(TABLE_PROJECT)
                                             ->where('acl')->eq('open')
                                             ->beginIF($this->config->systemMode == 'new')->andWhere('type')->in('sprint,stage')->fi()
                                             ->beginIF($this->config->systemMode == 'new')->andWhere('project')->in($userView->projects)->fi()
                                             ->fetchAll('id');

    $openedSprints     = join(',', array_keys($openedSprints));
    $userView->sprints = rtrim($userView->sprints, ',')  . ',' . $openedSprints;

    if(!empty($acls['sprints']) and !$isAdmin)
    {
        $grantSprints= '';
        foreach($acls['sprints'] as $sprintID)
        {
            if(strpos(",{$userView->sprints},", ",{$sprintID},") !== false) $grantSprints .= ",{$sprintID}";
        }
        $userView->sprints = $grantSprints;
    }

    $userView->products = trim($userView->products, ',');
    $userView->programs = trim($userView->programs, ',');
    $userView->projects = trim($userView->projects, ',');
    $userView->sprints  = trim($userView->sprints, ',');

    return $userView;
}

/**
 * Project: chengfangjinke
 * Method: getUserDeptName
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/9
 * Time: 7:51
 * Desc: This is the code comment. This method is called getUserDeptName.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param string $account
 * @return mixed
 */
public function getUserDeptName($account = '')
{
    $user = $this->dao->select('id,account,dept,company')->from(TABLE_USER)->where('account')->eq($account)->fetch();
    $deptName = $this->dao->select('name')->from(TABLE_DEPT)->where('id')->eq($user->dept)->fetch('name');
    $user->deptName = $deptName;
    return $user;
}

/**
 * Project: chengfangjinke
 * Method: getUserInfo
 * User: Tony Stark
 * Year: 2022
 * Date: 2022/02/23
 * Time: 7:51
 * Desc: 根据用户账号获得用户信息.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 *
 * @param string $account
 * @param $select
 * @return array|mixed
 */
public function getUserInfo($account = '', $select = '*'){
    $data = array();
    if(!$account){
        return $data;
    }
    $user = $this->dao->select($select)->from(TABLE_USER)->where('account')->eq($account)->fetch();
    if(!empty($user)){
        $data = $user;
    }
    return $data;
}

/**
 * Project: chengfangjinke
 * Method: getUserListByDeptId
 * User: wangjiurong
 * Year: 2022
 * Date: 2022/04/13
 * Time: 7:51
 * Desc: 根据用户部门获取用户列表
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 *
 * @param int $deptId
 * @param string $select
 * @return array
 */
public function getUserListByDeptId($deptId, $select = '*'){
    $data = array();
    if(!$deptId){
        return $data;
    }
    $ret = $this->dao->select($select)->from(TABLE_USER)->where('dept')->eq($deptId)->andWhere('deleted')->eq('0')->fetchAll();
    if(!empty($ret)){
        $data = $ret;
    }
    return $data;
}

/**
 *获得用户列表按照部门分组
 *
 * @param $deptIds
 * @return array
 */
public function getUserListGroupDeptId($deptIds){
    $data = array();
    if(!$deptIds){
        return $data;
    }
    $ret = $this->dao->select('dept, account, realname')
        ->from(TABLE_USER)
        ->where('dept')->in($deptIds)
        ->andWhere('deleted')->eq('0')
        ->fetchAll();
    if(!empty($ret)){
        foreach ($ret as $val){
            $dept     = $val->dept;
            $account  = $val->account;
            $realname = $val->realname;
            $data[$dept][$account] = $realname;
        }
    }
    return $data;
}

/**
 * Get user's avatar pairs.
 *
 * @param  string $params
 * @access public
 * @return array
 */
public function getAvatarPairs($params = 'nodeleted')
{
    $avatarPairs = array();
    $userList    = $this->getList($params);
    foreach($userList as $user) $avatarPairs[$user->account] = $user->avatar;

    return $avatarPairs;
}



