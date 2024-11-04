<?php
class epgprocess extends control
{
    /**
     * Browse EPG list.
     *
     * @param  string $browseType
     * @param  int    $param
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function browse($browseType = 'all', $param = 0, $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $browseType = strtolower($browseType);

        /* By search. */
        $queryID   = ($browseType == 'bysearch') ? (int)$param : 0;
        $actionURL = $this->createLink('epgprocess', 'browse', "browseType=bySearch&param=myQueryID");
        $this->epgprocess->buildSearchForm($queryID, $actionURL);

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $this->view->title        = $this->lang->epgprocess->browse;
        $this->view->orderBy      = $orderBy;
        $this->view->pager        = $pager;
        $this->view->browseType   = $browseType;
        $this->view->users        = $this->loadModel('user')->getPairs('noclosed|noletter');
        $this->view->processList  = $this->epgprocess->getList($browseType, $param, $orderBy, $pager);

        $this->display();
    }

    /**
     * Create a EPG.
     *
     * @access public
     * @return void
     */
    public function create()
    {
        if($_POST)
        {
            $processImproveID = $this->epgprocess->create();

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $this->loadModel('action')->create('epgprocess', $processImproveID, 'created', $this->post->comment);

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = inlink('browse');

            $this->send($response);
        }

        $this->view->title = $this->lang->epgprocess->create;

        $this->display();
    }

    /**
     * Edit a EPG.
     *
     * @param  int $processID
     * @access public
     * @return void
     */
    public function edit($processID = 0)
    {
        if($_POST)
        {
            $changes = $this->epgprocess->update($processID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes || $this->post->comment)
            {
                $actionID = $this->loadModel('action')->create('epgprocess', $processID, 'edited', $this->post->comment);
                $this->action->logHistory($processID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = inlink('view', "processID=$processID");

            $this->send($response);
        }

        $this->view->title = $this->lang->epgprocess->edit;

        $this->view->EPGList = $this->epgprocess->getByID($processID);

        $this->display();
    }

    /**
     * View a EPG.
     *
     * @param  int    $processID
     * @access public
     * @return void
     */
    public function view($processID)
    {
        $this->view->title = $this->lang->epgprocess->view;
        $this->view->users = $this->loadModel('user')->getPairs('noclosed|noletter');

        $this->view->actions    = $this->loadModel('action')->getList('epgprocess', $processID);
        $this->view->epgprocess = $this->epgprocess->getByID($processID);

        $this->display();
    }

    /**
     * Delete EPG.
     *
     * @param  int    $processID
     * @param  string $confirm    yes|no
     * @access public
     * @return void
     */
    public function delete($processID, $confirm = 'no')
    {
        if($confirm == 'no')
        {
            die(js::confirm($this->lang->epgprocess->confirmDelete, $this->createLink('epgprocess', 'delete', "processID=$processID&confirm=yes")));
        }
        else
        {
            $this->dao->delete()->from(TABLE_EPGPROCESS)->where('id')->eq($processID)->exec();

            die(js::locate($this->createLink('epgprocess', 'browse'), 'parent'));
        }
    }
}
