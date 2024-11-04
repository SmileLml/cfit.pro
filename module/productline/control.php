<?php
class productline extends control
{
    /**
     * Browse productline list.
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
    public function browse($browseType = 'all', $param = 0, $orderBy = 'code_asc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $this->loadModel('requirement');
        $browseType = strtolower($browseType);
        $depts = $this->loadModel('dept')->getOptionMenu();
        $this->config->productline->search['params']['depts']['values'] = $depts;

        /* By search. */
        $queryID = ($browseType == 'bysearch')  ? (int)$param : 0; 
        $actionURL = $this->createLink('productline', 'browse', "browseType=bySearch&param=myQueryID");
        $this->productline->buildSearchForm($queryID, $actionURL);

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $this->view->title        = $this->lang->productline->browse;
        $this->view->productlines = $this->productline->getList($browseType, $queryID, $orderBy, $pager);
        $this->view->depts        = $this->loadModel('dept')->getDeptPairs();
        $this->view->orderBy      = $orderBy;
        $this->view->pager        = $pager;
        $this->view->browseType   = $browseType;
        $this->view->users        = $this->loadModel('user')->getPairs('noletter|noclosed');
        $this->display();
    }

    /**
     * Create a productline.
     * 
     * @access public
     * @return void
     */
    public function create()
    {
        if($_POST)
        {
            $lineID = $this->productline->create();

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $this->loadModel('action')->create('productline', $lineID, 'created', $this->post->comment);

            if(isonlybody()) $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'closeModal' => true, 'callback' => "parent.loadApps($lineID)"));

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = inlink('browse');

            $this->send($response);
        }

        $this->view->title = $this->lang->productline->create;
        $this->view->users = $this->loadModel('user')->getPairs('noclosed');
        $this->view->depts = $this->loadModel('dept')->getTopPairs();
        $this->display();
    }

    /**
     * Edit a productline.
     * 
     * @param  int $lineID 
     * @access public
     * @return void
     */
    public function edit($lineID = 0)
    {
        if($_POST)
        {
            $changes = $this->productline->update($lineID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes || $this->post->comment)
            {
                $actionID = $this->loadModel('action')->create('productline', $lineID, 'edited', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = inlink('view', "lineID=$lineID");

            $this->send($response);
        }

        $line = $this->productline->getByID($lineID);

        $this->view->title       = $this->lang->productline->edit;
        $this->view->users       = $this->loadModel('user')->getPairs('noclosed');
        $this->view->productline = $line;
        $this->view->depts       = $this->loadModel('dept')->getTopPairs();
        $this->display();
    }

    /**
     * View productline.
     * 
     * @param  int    $lineID 
     * @access public
     * @return void
     */
    public function view($lineID)
    {
        $this->view->title       = $this->lang->productline->view;
        $this->view->users       = $this->loadModel('user')->getPairs('noclosed');
        $this->view->actions     = $this->loadModel('action')->getList('productline', $lineID);
        $this->view->depts       = $this->loadModel('dept')->getTopPairs();
        $this->view->productline = $this->loadModel('productline')->getByID($lineID);
        $this->view->productline->code = $this->view->productline->code.($this->view->productline->emisId?'（'.$this->view->productline->emisId.'）':'');
        $this->display();
    }

    /**
     * Delete productline.
     * 
     * @param  int    $lineID 
     * @param  string $confirm    yes|no
     * @access public
     * @return void
     */
    public function delete($lineID, $confirm = 'no')
    {
        if($confirm == 'no')
        {
            die(js::confirm($this->lang->productline->confirmDelete, $this->createLink('productline', 'delete', "lineID=$lineID&confirm=yes")));
        }
        else
        {
            $line = $this->productline->getByID($lineID);
            $this->productline->delete(TABLE_PRODUCTLINE, $lineID);
            $this->session->set('productline', '');
            die(js::locate($this->createLink('productline', 'browse', "programID=$line->program"), 'parent'));
        }
    }

    /**
     * Project: chengfangjinke
     * Method: ajaxGetOwner
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:57
     * Desc: This is the code comment. This method is called ajaxGetOwner.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $groupID
     */
    public function ajaxGetOwner($groupID)
    {
        die(zget($this->lang->opinion->ownerList, $groupID, ''));
    }
}
