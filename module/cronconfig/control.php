<?php
/**
 * The control file of cronconfig of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Yidong Wang <yidong@cnezsoft.com>
 * @package     cronconfig
 * @version     $Id$
 * @link        http://www.zentao.net
 */
class cronconfig extends control
{

    /**
     * browse
     * @param string $browseType
     * @param int $param
     * @param string $orderBy
     * @param int $recTotal
     * @param int $recPerPage
     * @param int $pageID
     */
    public function browse($browseType = 'all', $param = 0, $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $browseType = strtolower($browseType);

        $queryID = ($browseType == 'bysearch')  ? (int)$param : 0;
        $actionURL = $this->createLink('cronconfig', 'browse', "browseType=bySearch&param=myQueryID");
        $this->cronconfig->buildSearchForm($queryID, $actionURL);

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $this->view->title      = $this->lang->cronconfig->common;
        $this->view->position[] = $this->lang->cronconfig->common;
        $this->view->crons      = $this->cronconfig->getList($browseType, $queryID, $orderBy, $pager);
        $this->view->orderBy    = $orderBy;
        $this->view->param      = $param;
        $this->view->pager      = $pager;
        $this->view->browseType = $browseType;
        $this->display();
    }

    /**
     * 详情
     *
     * @param $cronID
     */
    function view($cronID){
        $this->view->title      = $this->lang->cronconfig->view;
        $this->view->position[] = $this->lang->cronconfig->view;
        $info = $this->cronconfig->getById($cronID);
        $this->view->info  = $info;
        $this->view->actions  = $this->loadModel('action')->getList('cronconfig', $cronID);
        $this->view->users    = $this->loadModel('user')->getPairs('noletter|noclosed');
        $this->display();
    }

    /**
     * 创建
     *
     */
    function create(){
        if($_POST)
        {
            $recordID = $this->cronconfig->create();

            if(dao::isError()) {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $this->loadModel('action')->create('cronconfig', $recordID, 'created');
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = inLink('browse');
            $this->send($response);
        }
        $this->view->title      = $this->lang->cronconfig->create;
        $this->view->position[] = $this->lang->cronconfig->create;
        $this->display();
    }


    /**
     * 编辑
     *
     * @param $cronID
     */
    function edit($cronID){
        if($_POST)
        {
            $changes = $this->cronconfig->update($cronID);

            if(dao::isError()) {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes) {
                $actionID = $this->loadModel('action')->create('cronconfig', $cronID, 'edited');
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }

        $this->view->title      = $this->lang->cronconfig->edit;
        $this->view->position[] = $this->lang->cronconfig->edit;
        $info = $this->cronconfig->getById($cronID);
        $this->view->info  = $info;
        $this->display();
    }

    /**
     * 删除操作
     *
     * @param $cronID
     */
    function delete($cronID){
        if($_POST)
        {
            $changes = $this->cronconfig->deleted($cronID);
            if(dao::isError()) {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes) {
                $actionID = $this->loadModel('action')->create('cronconfig', $cronID, 'deleted', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }
        $this->view->title      = $this->lang->cronconfig->edit;
        $this->view->position[] = $this->lang->cronconfig->edit;
        $info = $this->cronconfig->getById($cronID);
        $this->view->info  = $info;
        $this->display();
    }
}
