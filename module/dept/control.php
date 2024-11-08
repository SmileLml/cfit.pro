<?php
/**
 * The control file of dept module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     dept
 * @version     $Id: control.php 4157 2013-01-20 07:09:42Z wwccss $
 * @link        http://www.zentao.net
 */
class dept extends control
{
    const NEW_CHILD_COUNT = 10;

    /**
     * Construct function, set menu. 
     * 
     * @access public
     * @return void
     */
    public function __construct($moduleName = '', $methodName = '')
    {
        parent::__construct($moduleName, $methodName);
        $this->loadModel('company')->setMenu();
    }

    /**
     * Browse a department.
     * 
     * @param  int    $deptID 
     * @access public
     * @return void
     */
    public function browse($deptID = 0)
    {
        $parentDepts = $this->dept->getParents($deptID);
        $this->view->title       = $this->lang->dept->manage . $this->lang->colon . $this->app->company->name;
        $this->view->position[]  = $this->lang->dept->manage;
        $this->view->deptID      = $deptID;
        $this->view->depts       = $this->dept->getTreeMenu($rootDeptID = 0, array('deptmodel', 'createManageLink'));
        $this->view->parentDepts = $parentDepts;
        $this->view->sons        = $this->dept->getSons($deptID);
        $this->view->tree        = $this->dept->getDataStructure();
        $this->display();
    }

    /**
     * Update the departments order.
     * 
     * @access public
     * @return void
     */
    public function updateOrder()
    {
        if(!empty($_POST))
        {
            $this->dept->updateOrder($_POST['orders']);
            die(js::reload('parent'));
        }
    }

    /**
     * Manage childs.
     * 
     * @access public
     * @return void
     */
    public function manageChild()
    {
        if(!empty($_POST))
        {
            $this->dept->manageChild($_POST['parentDeptID'], $_POST['depts']);
            die(js::reload('parent'));
        }
    }

    /**
     * Edit dept. 
     * 
     * @param  int    $deptID 
     * @access public
     * @return void
     */
    public function edit($deptID)
    {
        if(!empty($_POST))
        {
            $this->dept->update($deptID);
            die(js::alert($this->lang->dept->successSave) . js::reload('parent'));
        }

        $dept  = $this->dept->getById($deptID);
        $users = $this->loadModel('user')->getPairs('noletter|noclosed|nodeleted|all');

        $this->view->optionMenu = $this->dept->getOptionMenu();

        $this->view->deptID  = $deptID;
        $this->view->dept  = $dept;
        $this->view->users = $users;

        /* Remove self and childs from the $optionMenu. Because it's parent can't be self or childs. */
        $childs = $this->dept->getAllChildId($deptID);
        foreach($childs as $childModuleID) unset($this->view->optionMenu[$childModuleID]);

        die($this->display());
    }

    /**
     * Delete a department.
     * 
     * @param  int    $deptID 
     * @param  string $confirm  yes|no
     * @access public
     * @return void
     */
    public function delete($deptID, $confirm = 'no')
    {
        /* Check this dept when delete. */
        $sons  = $this->dept->getSons($deptID);
        $users = $this->dept->getUsers('all', $deptID);
        if($sons)  die(js::alert($this->lang->dept->error->hasSons));
        if($users) die(js::alert($this->lang->dept->error->hasUsers));

        if($confirm == 'no')
        {
            die(js::confirm($this->lang->dept->confirmDelete, $this->createLink('dept', 'delete', "deptID=$deptID&confirm=yes")));
        }
        else
        {
            $this->dept->delete($deptID);
            die(js::reload('parent'));
        }
    }

    /**
     * Ajax get users 
     * 
     * @param  int    $dept 
     * @param  string $user 
     * @access public
     * @return void
     */
    public function ajaxGetUsers($dept, $user = '')
    {
        $users = array('' => '') + $this->dept->getDeptUserPairs($dept);
        die(html::select('user', $users, $user, "class='form-control chosen'"));
    }

    /**
     * Ajax get users 
     * 
     * @param  int    $dept 
     * @param  string $user 
     * @param  bool   $isMultiple
     * @param  string $placeholder
     * @param  string $noEmptyResultHint
     * @access public
     * @return void
     */
    public function ajaxGetUsersPicker($dept, $user = '', $isMultiple = false, $placeholder = '', $noEmptyResultHint = '')
    {
        $users    = ['' => ''] + $this->dept->getUserPairsByDeptID($dept);
        $name     = $isMultiple ? 'account[]' : 'account';
        $multiple = $isMultiple ? 'multiple' : '';

        die(html::select($name, $users, $user, "class='form-control picker-select' $multiple placeholder='{$placeholder}' data-empty-result-hint='{$noEmptyResultHint}'"));
    }

    // 人员部门数据修正
    public function batchUpdate()
    {
        $users = $this->loadModel('user')->getPairs('noclosed');
        $depts = $this->loadModel('dept')->getOptionMenu();
        if (!empty($_POST)){
            if (!empty($_POST['user']) && !empty($_POST['starttime']) && !empty($_POST['endtime']) && !empty($_POST['dept'])) {
                if (strtotime($_POST['starttime']) >= strtotime($_POST['endtime'])) {
                    $response['result']  = 'fail';
                    $response['message'] = $this->lang->dept->dateCompare;
                    $this->send($response);
                }
                $this->dao->update(TABLE_EFFORT)->set('deptID')->eq($_POST['dept'])->where('account')->eq($_POST['user'])->andWhere('date')->ge($_POST['starttime'])->andWhere('date')->lt($_POST['endtime'])->exec();
                $this->dao->update(TABLE_CONSUMED)->set('deptId')->eq($_POST['dept'])->where('account')->eq($_POST['user'])->andWhere('createdDate')->between($_POST['starttime'], $_POST['endtime'])->exec();
                $response['result']  = 'success';
                $response['message'] = $this->lang->dept->successSave;
                $response['locate'] = $this->createLink('dept', 'browse');
                $this->send($response);
            } else {
                $response['result']  = 'fail';
                $response['message'] = $this->lang->dept->paramEntire;
                $this->send($response);
            }
        }
        $this->view->title       = $this->lang->dept->updateTitle . $this->lang->colon . $this->app->company->name;
        $this->view->position[]  = $this->lang->dept->updateTitle;
        $this->view->depts  = $depts;
        $this->view->users  = $users;
        $this->display();
    }
}
