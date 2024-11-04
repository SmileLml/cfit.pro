<?php
class processimprove extends control
{
    /**
     * Browse process improve list.
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

        $depts = $this->loadModel('dept')->getOptionMenu();
        $this->config->processimprove->search['params']['createdDept']['values'] = $depts;

        /* By search. */
        $queryID   = ($browseType == 'bysearch') ? (int)$param : 0;
        $actionURL = $this->createLink('processimprove', 'browse', "browseType=bySearch&param=myQueryID");
        $this->processimprove->buildSearchForm($queryID, $actionURL);

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $this->view->title        = $this->lang->processimprove->browse;
        $this->view->processList  = $this->processimprove->getList($browseType, $param, $orderBy, $pager);
        $this->view->orderBy      = $orderBy;
        $this->view->pager        = $pager;
        $this->view->browseType   = $browseType;
        $this->view->users        = $this->loadModel('user')->getPairs('noletter|noclosed');
        $this->view->depts        = $this->loadModel('dept')->getOptionMenu();

        $this->display();
    }

    /**
     * Create a process improve.
     *
     * @access public
     * @return void
     */
    public function create()
    {
        if($_POST)
        {
            $processImproveID = $this->processimprove->create();

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $this->loadModel('action')->create('processimprove', $processImproveID, 'created', $this->post->comment);

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = inlink('browse');

            $this->send($response);
        }

        $this->view->title = $this->lang->processimprove->create;
        $this->view->users = $this->loadModel('user')->getPairs('noletter|noclosed');

        $this->display();
    }

    /**
     * Edit a process improve.
     *
     * @param  int $processID
     * @access public
     * @return void
     */
    public function edit($processID = 0)
    {
        if($_POST)
        {
            $changes = $this->processimprove->update($processID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes || $this->post->comment)
            {
                $actionID = $this->loadModel('action')->create('processimprove', $processID, 'edited', $this->post->comment);
                $this->action->logHistory($processID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = isonlybody() ? 'parent' : inlink('view', "processID=$processID");

            $this->send($response);
        }

        $this->view->title = $this->lang->processimprove->edit;
        $this->view->users = $this->loadModel('user')->getPairs('noletter|noclosed');

        $this->view->processImprove = $this->processimprove->getByID($processID);

        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: feedback
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:55
     * Desc: This is the code comment. This method is called feedback.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $processID
     */
    public function feedback($processID = 0)
    {
        if($_POST)
        {
            $changes = $this->processimprove->feedback($processID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes || $this->post->comment)
            {
                $actionID = $this->loadModel('action')->create('processimprove', $processID, 'feedbacked', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $this->view->title = $this->lang->processimprove->feedback;
        $this->view->users = $this->loadModel('user')->getPairs('noletter|noclosed');

        $this->view->processImprove = $this->processimprove->getByID($processID);

        $this->display();
    }

    /**
     * View a process improve.
     *
     * @param  int    $processID
     * @access public
     * @return void
     */
    public function view($processID)
    {
        $this->view->title = $this->lang->processimprove->view;
        $this->view->users = $this->loadModel('user')->getPairs('noletter|noclosed');

        $this->view->actions        = $this->loadModel('action')->getList('processimprove', $processID);
        $this->view->processimprove = $this->processimprove->getByID($processID);
        $this->view->depts        = $this->loadModel('dept')->getOptionMenu();

        $this->display();
    }

    /**
     * Delete process improve.
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
            die(js::confirm($this->lang->processimprove->confirmDelete, $this->createLink('processimprove', 'delete', "processID=$processID&confirm=yes")));
        }
        else
        {
            $this->dao->delete()->from(TABLE_PROCESSIMPROVE)->where('id')->eq($processID)->exec();

            die(js::locate($this->createLink('processimprove', 'browse'), 'parent'));
        }
    }

    /**
     * close.
     *
     * @param  int    $processID
     * @access public
     * @return void
     */
    public function close($processID = 0)
    {
        if($_POST)
        {
            $this->processimprove->close($processID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($this->post->comment)
            {
                $this->loadModel('action')->create('processimprove', $processID, 'closed', $this->post->comment);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $this->view->title = $this->lang->processimprove->close;

        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: export
     * User: Tony Stark
     * Year: 2022
     * Date: 2022/3/8
     * Time: 14:51
     * Desc: This is the code comment. This method is called export.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param string $orderBy
     * @param string $browseType
     */
     public function export($orderBy = 'id_desc', $browseType = 'all')
     {
         if($_POST)
         {
             $this->loadModel('file');
             $processimproveLang   = $this->lang->processimprove;
             $processimproveConfig = $this->config->processimprove;
 
             /* Create field lists. */
             $fields = $this->post->exportFields ? $this->post->exportFields : explode(',', $processimproveConfig->list->exportFields);
             foreach($fields as $key => $fieldName)
             {
                 $fieldName = trim($fieldName);
                 $fields[$fieldName] = isset($processimproveLang->$fieldName) ? $processimproveLang->$fieldName : $fieldName;
                 unset($fields[$key]);
             }
 
             /* Get processimproves. */
             $oprocessimproves = array();
             if($this->session->processimproveOnlyCondition)
             {
                 $processimproves = $this->dao->select('*')->from(TABLE_PROCESSIMPROVE)->where($this->session->processimproveQueryCondition)
                     ->beginIF($this->post->exportType == 'selected')->andWhere('id')->in($this->cookie->checkedItem)->fi()
                     ->orderBy($orderBy)->fetchAll('id');
             }
             else
             {
                $stmt = $this->dbh->query($this->session->processimproveQueryCondition . ($this->post->exportType == 'selected' ? " AND id IN({$this->cookie->checkedItem})" : '') . " ORDER BY " . strtr($orderBy, '_', ' '));
                // $stmt = $this->dbh->query("select * from zt_processimprove where 1 ". $this->session->processimproveQueryCondition . ($this->post->exportType == 'selected' ? " AND id IN({$this->cookie->checkedItem})" : '') . " order by 'id' desc");
                while($row = $stmt->fetch()) 
                {
                    $processimproves[$row->id] = $row;
                }
            }
             $processimproveIdList = array_keys($processimproves);
 
             /* Get users and depts. */
             $users = $this->loadModel('user')->getPairs('noletter');
             $depts = $this->loadModel('dept')->getOptionMenu();
  
 
             foreach($processimproves as $processimprove)
             {
                $processimprove->source    = $processimproveLang->sourceList[$processimprove->source]; 
                $processimprove->pri       = $processimproveLang->priorityList[$processimprove->pri];
                $processimprove->process   = $processimproveLang->processList[$processimprove->process];
                $processimprove->createdDate = substr($processimprove->createdDate, 0, 10);
                $processimprove->involved   = $processimproveLang->involvedList[$processimprove->involved];
                $processimprove->isAccept   = $processimproveLang->isAcceptList[$processimprove->isAccept];
                $processimprove->isDeploy   = $processimproveLang->isAcceptList[$processimprove->isDeploy];
                $processimprove->status   = $processimproveLang->statusList[$processimprove->status];

                $processimprove->reviewedBy    = $users[$processimprove->reviewedBy];
                $processimprove->createdBy    = $users[$processimprove->createdBy];
                $processimprove->createdDept  = $depts[$processimprove->createdDept];
             }
 
             $this->post->set('fields', $fields);
             $this->post->set('rows', $processimproves);
             $this->post->set('kind', 'processimprove');
             $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
         }
 
         $this->view->fileName        = $this->lang->processimprove->common;
         $this->view->allExportFields = $this->config->processimprove->list->exportFields;
         $this->view->customExport    = true;
         $this->display();
     }
}
