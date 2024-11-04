<?php
/**
 * Project: chengfangjinke
 * Method: getTopPairs
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 13:17
 * Desc: This is the code comment. This method is called getTopPairs.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param int $rootDeptID
 * @return array
 */
public function getTopPairs($rootDeptID = 0)
{
    $deptMenu = array();
    $stmt = $this->dbh->query($this->buildMenuQuery($rootDeptID));
    $depts = array();
    while($dept = $stmt->fetch()) $depts[$dept->id] = $dept;

    foreach($depts as $dept)
    {
        $parentDepts = explode(',', $dept->path);
        $deptName = '/';
        foreach($parentDepts as $parentDeptID)
        {
            if(empty($parentDeptID)) continue;
            $deptName .= $depts[$parentDeptID]->name . '/';
        }
        $deptName = rtrim($deptName, '/');
        $deptName .= "|$dept->id\n";

        if(isset($deptMenu[$dept->id]) and !empty($deptMenu[$dept->id]))
        {
            if(isset($deptMenu[$dept->parent]))
            {
                $deptMenu[$dept->parent] .= $deptName;
            }
            else
            {
                $deptMenu[$dept->parent] = $deptName;;
            }
            $deptMenu[$dept->parent] .= $deptMenu[$dept->id];
        }
        else
        {
            if(isset($deptMenu[$dept->parent]) and !empty($deptMenu[$dept->parent]))
            {
                $deptMenu[$dept->parent] .= $deptName;
            }
            else
            {
                $deptMenu[$dept->parent] = $deptName;
            }
        }
    }

    krsort($deptMenu);
    $topMenu  = array_pop($deptMenu);
    $topMenu  = explode("\n", trim($topMenu));
    $lastMenu = array();
    foreach($topMenu as $menu)
    {
        if(!strpos($menu, '|')) continue;
        list($label, $deptID) = explode('|', $menu);
        $lastMenu[$deptID] = $label;
    }
    return $lastMenu;
}

/**
 * @Notes:取消部门开头'/'后的结果
 * @Date: 2023/10/30
 * @Time: 11:35
 * @Interface cancleDept
 * @return mixed
 */
public function cancleDept()
{
    $depts = $this->getTopPairs();
    foreach ($depts as $key => $dept)
    {
        $depts[$key] = substr_replace($dept,'',0,1);
    }
    return $depts;
}

/**
 * Project: chengfangjinke
 * Method: update
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 13:17
 * Desc: This is the code comment. This method is called update.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param $deptID
 */
public function update($deptID)
{
    //20220215 groupleader

    $dept   = fixer::input('post')->join('leader', ',')->join('manager', ',')->join('po', ',')->join('executive', ',')->join('cm', ',')->join('qa', ',')->join('groupleader',',')->join('firstReviewer',',')
        ->join('reviewer',',')->join('testLeader',',')->get();
    $planPerson = $this->post->planPerson;
    if (empty($planPerson) && $deptID == 2) {
        die(js::alert($this->lang->dept->planPersonEmpty));
    }

    $self   = $this->getById($deptID);
    $parent = $this->getById($this->post->parent);
    $childs = $this->getAllChildId($deptID);
    $dept->grade = $parent ? $parent->grade + 1 : 1;
    $dept->path  = $parent ? $parent->path . $deptID . ',' : ',' . $deptID . ',';
    $dept->ldapName = trim($dept->ldapName);
    $this->dao->update(TABLE_DEPT)->data($dept)->autoCheck()->check('name', 'notempty')->where('id')->eq($deptID)->exec();

    $this->dao->update(TABLE_DEPT)->set('grade = grade + 1')->where('id')->in($childs)->andWhere('id')->ne($deptID)->exec();
    $this->dao->update(TABLE_DEPT)->set('manager')->eq($dept->manager)->where('id')->in($childs)->andWhere('manager')->eq('')->exec();
    $this->dao->update(TABLE_DEPT)->set('manager')->eq($dept->manager)->where('id')->in($childs)->andWhere('manager')->eq($self->manager)->exec();
    $this->fixDeptPath();
}

/**
 * 获取二线专员
 * Project: chengfangjinke
 * Method: getExecutiveUser
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 13:17
 * Desc: This is the code comment. This method is called getExecutiveUser.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @return array
 */
public function getExecutiveUser()
{
    $depts = $this->dao->select('id,executive')->from(TABLE_DEPT)->fetchAll();
    $accountList = array();
    foreach($depts as $dept)
    {
        $executiveList = explode(',', $dept->executive);
        foreach($executiveList as $account)
        {
            if(empty($account)) continue;
            $accountList[$account] = $account;
        }
    }
    return $accountList;
}

/**
 * @Notes:获取产品经理
 * @Date: 2023/3/3
 * @Time: 17:26
 * @Interface getPoUser
 * @return array
 */
public function getPoUser()
{
    $depts = $this->dao->select('id,po')->from(TABLE_DEPT)->fetchAll();
    $accountList = array();
    foreach($depts as $dept)
    {
        $poList = explode(',', $dept->po);
        foreach($poList as $account)
        {
            if(empty($account)) continue;
            $accountList[$account] = $account;
        }
    }
    return $accountList;
}

/**
 * @Notes:获取某个部门下的产品经理
 */
public function getPoUserByDeptId($deptId)
{
    $depts = $this->dao->select('id,po')->from(TABLE_DEPT)->where('id')->eq($deptId)->fetchAll();
    $accountList = array();
    foreach($depts as $dept)
    {
        $poList = explode(',', $dept->po);
        foreach($poList as $account)
        {
            if(empty($account)) continue;
            $accountList[$account] = $account;
        }
    }
    return $accountList;
}


/**
 *通过id获得部门列表
 *
 * @author wangjiurong
 * @param $ids
 * @param string $select
 * @return array
 */
public function getDeptListByIds($ids, $select = '*')
{
    $data = [];
    if(!$ids){
        return $data;
    }
    $ret = $this->dao->select($select)->from(TABLE_DEPT)->where('id')->in($ids)->fetchAll();
    if(!empty($ret)){
        $data = $ret;
    }
    return  $data;
}

/**
 * @return array
 * 获取所有产品经理
 */
public function getPoUser2()
{
    $depts = $this->dao->select('id,po')->from(TABLE_DEPT)->fetchAll();
    $accountList = array();
    foreach($depts as $dept)
    {
        $executiveList = explode(',', $dept->po);
        foreach($executiveList as $account)
        {
            if(empty($account)) continue;
            $accountList[$account] = $account;
        }
    }

    return $accountList;
}

/**
 * Get the department at the specified level.
 *
 * @param  int    $level
 * @access public
 * @return void
 */
public function getSpecifyLevelDeptList($level = 1)
{
    return $this->dao->select('id,name')->from(TABLE_DEPT)->where('grade')->eq($level)->orderBy('order asc')->fetchPairs();
}
