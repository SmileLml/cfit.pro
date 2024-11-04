<?php
/**
 * The model file of dept module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     dept
 * @version     $Id: model.php 4210 2013-01-22 01:06:12Z zhujinyonging@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php
class deptModel extends model
{
    /**
     * Get a department by id.
     *
     * @param  int    $deptID
     * @access public
     * @return object
     */
    public function getByID($deptID)
    {
        return $this->dao->findById($deptID)->from(TABLE_DEPT)->fetch();
    }
    /**
     * Get departments by ids.
     * tongyanqi 2022-04-19
     * @param  int    $deptID
     * @access public
     * @return object
     */
    public function getByIDs($deptIDs)
    {
        return $this->dao->query("select * from " . TABLE_DEPT. " where id in ($deptIDs)")->fetchall();
    }
    /**
     * Get all department names.
     *
     * @param  int   $deptID
     * @access public
     * @return object
     */
    public function getDeptPairs($deptID = 0)
    {
        return $this->dao->select('id,name')->from(TABLE_DEPT)->beginIF($deptID)->where('id')->eq($deptID)->fi()->fetchPairs();
    }

    /**
     * Build the query.
     *
     * @param  int    $rootDeptID
     * @access public
     * @return string
     */
    public function buildMenuQuery($rootDeptID)
    {
        $rootDept = $this->getByID($rootDeptID);
        if(!$rootDept)
        {
            $rootDept = new stdclass();
            $rootDept->path = '';
        }

        return $this->dao->select('*')->from(TABLE_DEPT)
            ->beginIF($rootDeptID > 0)->where('path')->like($rootDept->path . '%')->fi()
            ->orderBy('grade desc, `order`')
            ->get();
    }

    /**
     * Get option menu of departments.
     *
     * @param  int    $rootDeptID
     * @access public
     * @return array
     */
    public function getOptionMenu($rootDeptID = 0)
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
        $topMenu = array_pop($deptMenu);
        $topMenu = explode("\n", trim($topMenu));
        $lastMenu[] = '/';
        foreach($topMenu as $menu)
        {
            if(!strpos($menu, '|')) continue;
            list($label, $deptID) = explode('|', $menu);
            $lastMenu[$deptID] = $label;
        }
        return $lastMenu;
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
        $stmt = $this->dbh->query($this->buildMenuQuery($rootDeptID));
        while($dept = $stmt->fetch())
        {
            $linkHtml = call_user_func($userFunc, $dept, $param);

            if(isset($deptMenu[$dept->id]) and !empty($deptMenu[$dept->id]))
            {
                if(!isset($deptMenu[$dept->parent])) $deptMenu[$dept->parent] = '';
                $deptMenu[$dept->parent] .= "<li>$linkHtml";
                $deptMenu[$dept->parent] .= "<ul>".$deptMenu[$dept->id]."</ul>\n";
            }
            else
            {
                if(isset($deptMenu[$dept->parent]) and !empty($deptMenu[$dept->parent]))
                {
                    $deptMenu[$dept->parent] .= "<li>$linkHtml\n";
                }
                else
                {
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
     * Update dept.
     *
     * @param  int    $deptID
     * @access public
     * @return void
     */
    public function update($deptID)
    {
        $dept   = fixer::input('post')->get();
        $self   = $this->getById($deptID);
        $parent = $this->getById($this->post->parent);
        $childs = $this->getAllChildId($deptID);
        $dept->grade = $parent ? $parent->grade + 1 : 1;
        $dept->path  = $parent ? $parent->path . $deptID . ',' : ',' . $deptID . ',';
        $this->dao->update(TABLE_DEPT)->data($dept)->autoCheck()->check('name', 'notempty')->where('id')->eq($deptID)->exec();
        $this->dao->update(TABLE_DEPT)->set('grade = grade + 1')->where('id')->in($childs)->andWhere('id')->ne($deptID)->exec();
        $this->dao->update(TABLE_DEPT)->set('manager')->eq($this->post->manager)->where('id')->in($childs)->andWhere('manager')->eq('')->exec();
        $this->dao->update(TABLE_DEPT)->set('manager')->eq($this->post->manager)->where('id')->in($childs)->andWhere('manager')->eq($self->manager)->exec();
        $this->fixDeptPath();
    }

    /**
     * Create the manage link.
     *
     * @param  object    $dept
     * @access public
     * @return string
     */
    public function createManageLink($dept)
    {
        $linkHtml  = $dept->name;
        if(common::hasPriv('dept', 'edit')) $linkHtml .= ' ' . html::a(helper::createLink('dept', 'edit', "deptid={$dept->id}"), $this->lang->edit, '', 'data-toggle="modal" data-type="ajax"');
        if(common::hasPriv('dept', 'browse')) $linkHtml .= ' ' . html::a(helper::createLink('dept', 'browse', "deptid={$dept->id}"), $this->lang->dept->manageChild);
        if(common::hasPriv('dept', 'delete')) $linkHtml .= ' ' . html::a(helper::createLink('dept', 'delete', "deptid={$dept->id}"), $this->lang->delete, 'hiddenwin');
        if(common::hasPriv('dept', 'updateOrder')) $linkHtml .= ' ' . html::input("orders[$dept->id]", $dept->order, 'style="width:30px;text-align:center"');
        return $linkHtml;
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
        $linkHtml = html::a(helper::createLink('company', 'browse', "browseType=inside&dept={$dept->id}"), $dept->name, '_self', "id='dept{$dept->id}'");
        return $linkHtml;
    }

    /**
     * Create the traingoal member link.
     *
     * @param  int    $dept
     * @access public
     * @return string
     */
    public function traingoalMemberLink($dept, $planID)
    {
        $linkHtml = html::a(helper::createLink('traingoal', 'browse', "goalID={$planID}&type=company&dept={$dept->id}&"), $dept->name, '_self', "id='dept{$dept->id}'");
        return $linkHtml;
    }

    /**
     * Create the group manage members link.
     *
     * @param  int    $dept
     * @param  int    $groupID
     * @access public
     * @return string
     */
    public function createGroupManageMemberLink($dept, $groupID)
    {
        return html::a(helper::createLink('group', 'managemember', "groupID=$groupID&deptID={$dept->id}"), $dept->name, '_self', "id='dept{$dept->id}'");
    }

    /**
     * Create the group manage program admin link.
     *
     * @param  int    $dept
     * @param  int    $groupID
     * @access public
     * @return string
     */
    public function createManageProjectAdminLink($dept, $groupID)
    {
        return html::a(helper::createLink('group', 'manageProjectAdmin', "groupID=$groupID&deptID={$dept->id}"), $dept->name, '_self', "id='dept{$dept->id}'");
    }

    /**
     * Get sons of a department.
     *
     * @param  int    $deptID
     * @access public
     * @return array
     */
    public function getSons($deptID)
    {
        return $this->dao->select('*')->from(TABLE_DEPT)->where('parent')->eq($deptID)->orderBy('`order`')->fetchAll();
    }

    /**
     * Get all childs.
     *
     * @param  int    $deptID
     * @access public
     * @return array
     */
    public function getAllChildId($deptID)
    {
        if($deptID == 0) return array();
        $dept = $this->getById($deptID);
        $childs = $this->dao->select('id')->from(TABLE_DEPT)->where('path')->like($dept->path . '%')->fetchPairs();
        return array_keys($childs);
    }

    /**
     * Get parents.
     *
     * @param  int    $deptID
     * @access public
     * @return array
     */
    public function getParents($deptID)
    {
        if($deptID == 0) return array();
        $path = $this->dao->select('path')->from(TABLE_DEPT)->where('id')->eq($deptID)->fetch('path');
        $path = substr($path, 1, -1);
        if(empty($path)) return array();
        return $this->dao->select('*')->from(TABLE_DEPT)->where('id')->in($path)->orderBy('grade')->fetchAll();
    }

    public function getCompleteName($deptID)
    {
        if($deptID == 0) return '';
        $curdept = $this->dao->select('path,name')->from(TABLE_DEPT)->where('id')->eq($deptID)->fetch();
        if(!$curdept){
            return '';
        }
        $deptpath = trim($curdept->path,',');
        if(count(explode(',',$deptpath)) == 1){
            return $curdept->name;
        }
        $path = substr($curdept->path, 1, -1);

        $parentDepts = $this->dao->select('*')->from(TABLE_DEPT)->where('id')->in($path)->orderBy('grade')->fetchAll();
        $deptname = '/';
        foreach ($parentDepts as $dept){
            $deptname .= $dept->name.'/';
        }
        return rtrim($deptname,'/');
    }

    /**
     * Update order.
     *
     * @param  int    $orders
     * @access public
     * @return void
     */
    public function updateOrder($orders)
    {
        foreach($orders as $deptID => $order) $this->dao->update(TABLE_DEPT)->set('`order`')->eq($order)->where('id')->eq($deptID)->exec();
    }

    /**
     * Manage childs.
     *
     * @param  int    $parentDeptID
     * @param  string $childs
     * @access public
     * @return void
     */
    public function manageChild($parentDeptID, $childs)
    {
        $parentDept = $this->getByID($parentDeptID);
        if($parentDept)
        {
            $grade      = $parentDept->grade + 1;
            $parentPath = $parentDept->path;
        }
        else
        {
            $grade      = 1;
            $parentPath = ',';
        }

        $i = 1;
        foreach($childs as $deptID => $deptName)
        {
            if(empty($deptName)) continue;
            if(is_numeric($deptID))
            {
                $dept->name   = strip_tags($deptName);
                $dept->parent = $parentDeptID;
                $dept->grade  = $grade;
                $dept->order  = $this->post->maxOrder + $i * 10;
                $this->dao->insert(TABLE_DEPT)->data($dept)->exec();
                $deptID = $this->dao->lastInsertID();
                $childPath = $parentPath . "$deptID,";
                $this->dao->update(TABLE_DEPT)->set('path')->eq($childPath)->where('id')->eq($deptID)->exec();
                $i++;
            }
            else
            {
                $deptID = str_replace('id', '', $deptID);
                $this->dao->update(TABLE_DEPT)->set('name')->eq(strip_tags($deptName))->where('id')->eq($deptID)->exec();
            }
        }
    }

    /**
     * Get users of a deparment.
     *
     * @param  varchar $browseType inside|outside|all
     * @param  int     $deptID
     * @param  object  $pager
     * @param  varchar $orderBy
     * @access public
     * @return array
     */
    public function getUsers($browseType = 'inside', $deptID, $pager = null, $orderBy = 'id')
    {
        return $this->dao->select('*')->from(TABLE_USER)
            ->where('deleted')->eq(0)
            ->beginIF($browseType == 'inside')->andWhere('type')->eq('inside')->fi()
            ->beginIF($browseType == 'outsideExpertType')->andWhere('type')->eq('inside')->andWhere('expertType')->eq('outside')->fi()
            ->beginIF($browseType == 'outside')->andWhere('type')->eq('outside')->fi()
            ->beginIF($deptID)->andWhere('dept')->in($deptID)->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll();
    }

    /**
     * Get user pairs of a department.
     *
     * @param  int    $deptID
     * @param  string $params  userid|outside|all
     * @access public
     * @return array
     */
    public function getDeptUserPairs($deptID = 0, $params = '')
    {
        $childDepts = $this->getAllChildID($deptID);
        $keyField   = strpos($params, 'useid') !== false ? 'id' : 'account';
        $type       = (strpos($params, 'outside') !== false) ? 'outside' : 'inside';

        return $this->dao->select("$keyField, realname")->from(TABLE_USER)
            ->where('deleted')->eq(0)
            ->beginIF(strpos($params, 'all') === false)->andWhere('type')->eq($type)->fi()
            ->beginIF($deptID)->andWhere('dept')->in($childDepts)->fi()
            ->orderBy('account')
            ->fetchPairs();
    }

    /**
     * Get user pairs of a department id.
     *
     * @param  array  $deptIdList
     * @param  string $params  userid|outside|all
     * @access public
     * @return array
     */
    public function getUserPairsByDeptID($deptIdList = array(), $params = '')
    {
        $keyField = strpos($params, 'useid')   !== false ? 'id' : 'account';
        $type     = strpos($params, 'outside') !== false ? 'outside' : 'inside';

        return $this->dao->select("$keyField, realname")->from(TABLE_USER)
            ->where('deleted')->eq(0)
            ->beginIF(strpos($params, 'all') === false)->andWhere('type')->eq($type)->fi()
            ->beginIF($deptIdList)->andWhere('dept')->in($deptIdList)->fi()
            ->orderBy('id_asc')
            ->fetchPairs();
    }

    /**
     * Delete a department.
     *
     * @param  int    $deptID
     * @param  null   $null      compatible with that of model::delete()
     * @access public
     * @return void
     */
    public function delete($deptID, $null = null)
    {
        $this->dao->delete()->from(TABLE_DEPT)->where('id')->eq($deptID)->exec();
    }

    /**
     * Fix dept path.
     *
     * @access public
     * @return void
     */
    public function fixDeptPath()
    {
        /* Get all depts grouped by parent. */
        $groupDepts = $this->dao->select('id, parent')->from(TABLE_DEPT)->fetchGroup('parent', 'id');
        $depts      = array();

        /* Cycle the groupDepts until it has no item any more. */
        while(count($groupDepts) > 0)
        {
            $oldCounts = count($groupDepts);    // Record the counts before processing.
            foreach($groupDepts as $parentDeptID => $childDepts)
            {
                /* If the parentDept doesn't exsit in the depts, skip it. If exists, compute it's child depts. */
                if(!isset($depts[$parentDeptID]) and $parentDeptID != 0) continue;
                if($parentDeptID == 0)
                {
                    $parentDept = new stdclass();
                    $parentDept->grade = 0;
                    $parentDept->path  = ',';
                }
                else
                {
                    $parentDept = $depts[$parentDeptID];
                }

                /* Compute it's child depts. */
                foreach($childDepts as $childDeptID => $childDept)
                {
                    $childDept->grade = $parentDept->grade + 1;
                    $childDept->path  = $parentDept->path . $childDept->id . ',';
                    $depts[$childDeptID] = $childDept;    // Save child dept to depts, thus the child of child can compute it's grade and path.
                }
                unset($groupDepts[$parentDeptID]);    // Remove it from the groupDepts.
            }
            if(count($groupDepts) == $oldCounts) break;   // If after processing, no dept processed, break the cycle.
        }

        /* Save depts to database. */
        foreach($depts as $dept)
        {
            $this->dao->update(TABLE_DEPT)->data($dept)->where('id')->eq($dept->id)->exec();
        }
    }

    /**
     * Get data structure
     * @access public
     * @return array
     */
    public function getDataStructure()
    {
        $users      = $this->loadModel('user')->getPairs('noletter|noclosed|nodeleted|all');
        $treeGroups = $this->dao->select('*')->from(TABLE_DEPT)->orderBy('grade_desc,`order`')->fetchGroup('parent', 'id');
        $tree       = array();
        foreach($treeGroups as $parent => $groups)
        {
            foreach($groups as $deptID => $node)
            {
                $node->managerName = zget($users, $node->manager);
                if(isset($tree[$deptID]))
                {
                    $node->children = $tree[$deptID];
                    $node->actions = array('delete' => false);
                    unset($tree[$deptID]);
                }
                $tree[$node->parent][] = $node;
            }
        }

        krsort($tree);
        return array_pop($tree);
    }

    /**
     * Desc:讲获取的拼音字符串转换为key拼音，value中文的数组
     * Date: 2022/6/28
     * Time: 10:36
     *
     * @param String $string
     * @return array
     *
     */
    public function getRenameListByAccountStr($string)
    {
        $list = [];
        if(!empty($string)){
            $array = explode(',',$string);
            $arrayOfRename = $this->loadModel('user')->getRealNameAndEmails($array);
            if($arrayOfRename){
                foreach ($arrayOfRename as $key=>$value){
                    $list[$key] = $value->realname;
                }
            }
        }
        return $list;
    }

    /**
     *获得所有部门列表
     *
     * @param string $select
     * @return mixed
     */
    public function getAllDeptList($select = '*'){
        return $this->dao->select($select)->from(TABLE_DEPT)
            ->fetchAll();
    }

    /**
     * @Notes: 获取部门下数据
     * @Date: 2023/6/26
     * @Time: 13:57
     * @Interface getFieldByDeptId
     * @param string $field
     * @param $deptId
     * @return mixed
     */
    public function getFieldByDeptId($field = '*',$deptId)
    {
        return $this->dao->select($field)->from(TABLE_DEPT)->where('id')->eq($deptId)->fetch();
    }

    //获取部门的所属父级部门
    public function getDeptAndChild(){
        $deptList = $this->dao->select("id,path")->from(TABLE_DEPT)->fetchAll();
        $deptChildArr = [];
        foreach ($deptList as $dept){
            $tempPath = trim($dept->path,',');
            $tempPathArr = explode(',',$tempPath);
            $deptChildArr[$dept->id] = $tempPathArr[0];
        }
        //部门为空
        $deptChildArr[-1] = -1;
        return $deptChildArr;
    }

    public function getDeptHasChild(){
        $deptList = $this->dao->select("id,path,parent")->from(TABLE_DEPT)->fetchAll();
        $deptHasArr = [];

        foreach ($deptList as $dept){
            if($dept->parent){
                $tempPath = trim($dept->path,',');
                $tempPathArr = explode(',',$tempPath);
                $deptHasArr[$tempPathArr[0]][] = $dept->id;
            }else{
                $deptHasArr[$dept->id] = [$dept->id];
            }

        }


        return $deptHasArr;
    }

    //获取排序部门后的部门列表
    public function getDeptByOrder(){

        $deptList = $this->dao->select("id,name")->from(TABLE_DEPT)->orderBy('order_asc')->fetchAll();
        $deptArr = [];
        $deptArr[0] = $this->lang->dept->deptAll;
        foreach ($deptList as $dept){
            $deptArr[$dept->id] = $dept->name;
        }
        $deptArr[-1] = $this->lang->dept->deptNull;

        return $deptArr;
    }

    /**
     * 获得递归子级部门id
     *
     * @param $rootDeptID
     * @return mixed
     */
    public function getRecurveSubDeptIds($rootDeptID){
        $data = [];
        $rootDept = $this->getByID($rootDeptID);
        if(!$rootDept)
        {
            $rootDept = new stdclass();
            $rootDept->path = '';
        }

        $rootDept->path = trim($rootDept->path,',');

        $ret =  $this->dao->select('id')->from(TABLE_DEPT)
            ->beginIF($rootDeptID > 0)->where("FIND_IN_SET('{$rootDept->path}', path)")
            ->orderBy('grade desc, `order`')
            ->fetchAll();
        if($ret){
            $data = array_column($ret, 'id');
        }
        return $data;
    }

    /**
     * 获得部门列表
     *
     * @param string $exWhere
     * @param string $select
     * @return array
     */
    public function getDeptList($exWhere = '', $select = '*'){
        $data = [];
        $ret =  $this->dao->select($select)
            ->from(TABLE_DEPT)
            ->where('1')
            ->beginIF($exWhere)->andWhere($exWhere)->fi()
            ->fetchAll();
        if($ret){
            $data = $ret;
        }
        return $data;
    }

    /**
     * 判断是否属于上海分公司
     *
     * @param $deptId
     * @return bool
     */
    public function getIsShanghaiDept($deptId){
        $isShanghaiDept = false;
        $shanghaiDeptId = $this->lang->dept->shanghaiDeptId;
        $shanghaiDeptIds = $this->getRecurveSubDeptIds($shanghaiDeptId);
        if (in_array($deptId, $shanghaiDeptIds)) {   // 上海分公司特殊处理
            $isShanghaiDept = true;
        }
        return $isShanghaiDept;
    }
    //    获取指定部门的部门领导，分管领导，cm
    public function getdeptLeader($deptId=[])
    {
        $data = [];
        $deptId=implode(',',$deptId);
        $ret = $this->dao->select('leader1,manager1,cm')->from(TABLE_DEPT)->where("FIND_IN_SET(id,'{$deptId}')")->fetchAll();

        if ($ret) {
            $leader = array_column($ret, 'leader1');
            $leader = implode(',', $leader);
            $data['leader1'] = $leader;

            $manager1 = array_column($ret, 'manager1');
            $manager1 = implode(',', $manager1);
            $data['manager1'] = $manager1;

            $cm = array_column($ret, 'cm');
            $cm = implode(',', $cm);
            $cm = explode(',', $cm);
            $cm = array_unique($cm);
            $cm = implode(',', $cm);
            $data['cm'] = $cm;
        }
        return $data;
    }
}
