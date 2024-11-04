<?php
/**
 * The control file of ldap of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2010 QingDao Nature Easy Soft Network Technology Co,LTD (www.cnezsoft.com)
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Yidong Wang <yidong@cnezsoft.com>
 * @package     ldap
 * @version     $Id$
 * @link        http://www.zentao.net
 */
class ldap extends control
{
    public function index()
    {
        $this->locate(inlink('set'));
    }

    public function set()
    {
        if($_POST)
        {
            $this->ldap->saveSettings();
            if(dao::isError()) die(js::error(dao::getError()));
            echo js::alert($this->lang->ldap->successSave);
            die(js::reload('parent'));
        }

        $this->view->title      = $this->lang->ldap->common;
        $this->view->groups     = $this->loadModel('group')->getPairs();
        $this->view->ldapConfig = empty($this->app->config->ldap) ? '' : $this->app->config->ldap;
        $this->display();
    }

    public function dept()
    {
        $deptRelation = array();

        $ldapData = $this->dao->select('id,ldapName')->from(TABLE_DEPT)->fetchPairs();
        $deptData = $this->loadModel('dept')->getOptionMenu();
        unset($deptData[0]);
        foreach($deptData as $deptID => $deptName)
        {
            $deptRelation[] = array('id' => $deptID, 'ldapName' => $ldapData[$deptID], 'deptName' => $deptName);
        }

        $this->view->title        = $this->lang->ldap->common;
        $this->view->deptRelation = $deptRelation;
        $this->display();
    }

    public function noticeConf()
    {
        if($_POST)
        {
            $this->ldap->setLdapNoticeConf();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        /* 用于LDAP发信测试。*/
        //$this->ldap->sendMail();

        $mailConf = isset($this->config->global->setLdapMail) ? $this->config->global->setLdapMail : '{"sendUser":"","mailTitle":"","mailContent":""}';
        $mailConf = json_decode($mailConf);

        $this->view->title      = $this->lang->ldap->common;
        $this->view->position[] = $this->lang->ldap->common;
        $this->view->mailConf   = $mailConf;
        $this->view->users      = $this->loadModel('user')->getPairs('noletter');

        $this->display();
    }

    public function syncHistory($orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);
        $historyList = $this->ldap->getSyncHistory($orderBy, $pager);

        $this->view->title       = $this->lang->ldap->common;
        $this->view->position[]  = $this->lang->ldap->common;
        $this->view->historyList = $historyList;
        $this->view->pager       = $pager;

        $this->display();
    }
}
