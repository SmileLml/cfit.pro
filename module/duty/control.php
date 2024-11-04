<?php
class duty extends control
{
    /**
     * Project: chengfangjinke
     * Method: browse
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:26
     * Desc: This is the code comment. This method is called browse.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
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

        /* By search. */
        $queryID = ($browseType == 'bysearch')  ? (int)$param : 0;
        $actionURL = $this->createLink('duty', 'browse', "browseType=bySearch&param=myQueryID");
        $this->duty->buildSearchForm($queryID, $actionURL);

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $this->view->title      = $this->lang->duty->common;
        $this->view->dutys      = $this->duty->getList($browseType, $queryID, $orderBy, $pager);
        $this->view->orderBy    = $orderBy;
        $this->view->pager      = $pager;
        $this->view->browseType = $browseType;
        $this->view->depts      = $this->loadModel('dept')->getOptionMenu();
        $this->view->users      = $this->loadModel('user')->getPairs('noletter|noclosed');
        $this->view->appList    = array('') + $this->loadModel('application')->getapplicationNameCodePairs();
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: create
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:26
     * Desc: This is the code comment. This method is called create.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param string $date
     */
    public function create($date = '')
    {
        if(!empty($date))
        {
            $year  = substr($date, 0, 4);
            $month = substr($date, 4, 2);
            $day   = substr($date, 6, 2);
            $date  = $year . '-' . $month . '-' . $day;
        }

        if($_POST)
        {
            $dutyID = $this->duty->create();

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $this->loadModel('action')->create('duty', $dutyID, 'created');
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = isonlybody() ? 'parent' : inlink('browse');

            $this->send($response);
        }

        $this->view->title   = $this->lang->duty->create;
        $this->view->users   = $this->loadModel('user')->getPairs('noletter|noclosed');
        $this->view->appList = array('') + $this->loadModel('application')->getapplicationNameCodePairs();
        $this->view->date    = $date;
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: batchCreate
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:26
     * Desc: This is the code comment. This method is called batchCreate.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param string $date
     */
    public function batchCreate($date = '')
    {
        if(!empty($date))
        {
            $year  = substr($date, 0, 4);
            $month = substr($date, 4, 2);
            $day   = substr($date, 6, 2);
            $date  = $year . '-' . $month . '-' . $day;
        }

        if(!empty($_POST))
        {
            $this->duty->batchCreate();

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = isonlybody() ? 'parent' : $this->createLink('duty', 'browse');

            $this->send($response);
        }

        $this->view->title = $this->lang->duty->batchCreate;
        $this->view->date  = $date;
        $this->view->users = $this->loadModel('user')->getPairs('noletter|noclosed');

        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: edit
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:26
     * Desc: This is the code comment. This method is called edit.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $dutyID
     */
    public function edit($dutyID = 0)
    {
        if($_POST)
        {
            $changes = $this->duty->update($dutyID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $actionID = $this->loadModel('action')->create('duty', $dutyID, 'edited');
            $this->action->logHistory($actionID, $changes);

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            if(isonlybody())
            {
                $response['locate']  = 'parent';
            }
            else
            {
                $response['locate']  = $this->createLink('duty', 'browse');
            }

            $this->send($response);
        }

        $this->view->title   = $this->lang->duty->edit;
        $this->view->users   = $this->loadModel('user')->getPairs('noletter|noclosed');
        $this->view->duty    = $this->loadModel('duty')->getByID($dutyID);
        $this->view->appList = array('') + $this->loadModel('application')->getapplicationNameCodePairs();

        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: view
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:26
     * Desc: This is the code comment. This method is called view.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $dutyID
     */
    public function view($dutyID = 0)
    {
        $duty     = $this->duty->getByID($dutyID);
        $userList = $this->loadModel('user')->getList();

        $userPhone = array();
        foreach($userList as $user)
        {
            foreach(explode(',', $duty->user) as $account)
            {
                if($user->account == $account) $userPhone[$account] = $user->phone;
            }
        }

        $actualUser = array();
        foreach($userList as $user)
        {
            foreach(explode(',', $duty->actualUser) as $account)
            {
                if($user->account == $account) $actualUser[$account] = $user->phone;
            }
        }

        $this->view->title      = $this->lang->duty->view;
        $this->view->users      = $this->loadModel('user')->getPairs('noletter|noclosed');
        $this->view->actions    = $this->loadModel('action')->getList('duty', $dutyID);
        $this->view->duty       = $duty;
        $this->view->userPhone  = $userPhone;
        $this->view->actualUser = $actualUser;
        $this->view->appList    = array('') + $this->loadModel('application')->getapplicationNameCodePairs();

        $this->display();
    }

    /**
     * Delete duty.
     *
     * @param  int    $dutyID
     * @param  string $confirm    yes|no
     * @access public
     * @return void
     */
    public function delete($dutyID, $confirm = 'no')
    {
        if($confirm == 'no')
        {
            die(js::confirm($this->lang->duty->confirmDelete, $this->createLink('duty', 'delete', "dutyID=$dutyID&confirm=yes")));
        }
        else
        {
            $this->dao->delete()->from(TABLE_DUTY)->where('id')->eq($dutyID)->exec();

            die(js::locate($this->createLink('duty', 'browse'), 'parent'));
        }
    }

    /**
     * calendar.
     *
     * @access public
     * @return void
     */
    public function calendar()
    {
        $this->view->title     = $this->lang->duty->calendar;
        $this->view->dutyCount = $this->duty->getCount();
        $this->display();
    }

    /**
     * Ajax get duty list.
     *
     * @param  string $year
     * @access public
     * @return void
     */
    public function ajaxGetDutyList($year = '')
    {
        die($this->duty->getDuties4Calendar($year));
    }

    /**
     * Project: chengfangjinke
     * Method: export
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:27
     * Desc: This is the code comment. This method is called export.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param string $orderBy
     * @param string $browseType
     */
    public function export($orderBy = 'planDate_desc', $browseType = 'all')
    {
        /* format the fields of every duty in order to export data. */
        if($_POST)
        {
            $this->loadModel('file');
            $dutyLang   = $this->lang->duty;
            $dutyConfig = $this->config->duty;

            /* Create field lists. */
            $fields = $this->post->exportFields ? $this->post->exportFields : explode(',', $dutyConfig->list->exportFields);
            foreach($fields as $key => $fieldName)
            {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = isset($dutyLang->$fieldName) ? $dutyLang->$fieldName : $fieldName;
                unset($fields[$key]);
            }

            /* Get dutys. */
            $dutys = array();
            if($this->session->dutyOnlyCondition)
            {
                $dutys = $this->dao->select('*')->from(TABLE_DUTY)->where($this->session->dutyQueryCondition)
                    ->beginIF($this->post->exportType == 'selected')->andWhere('id')->in($this->cookie->checkedItem)->fi()
                    ->orderBy($orderBy)->fetchAll('id');
            }
            else
            {
                $stmt = $this->dbh->query($this->session->dutyQueryCondition . ($this->post->exportType == 'selected' ? " AND id IN({$this->cookie->checkedItem})" : '') . " ORDER BY " . strtr($orderBy, '_', ' '));
                while($row = $stmt->fetch()) $dutys[$row->id] = $row;
            }
            $dutyIdList = array_keys($dutys);

            /* Get users, products and executions. */
            $users = $this->loadModel('user')->getPairs('noletter');
            $apps  = $this->loadModel('application')->getapplicationNameCodePairs();

            foreach($dutys as $duty)
            {
                $us = array();
                foreach(explode(',', trim($duty->user, ',')) as $user)
                {
                    if($user and isset($users[$user])) $us[] = $users[$user];
                }
                $duty->user = implode(',', $us);

                $us = array();
                foreach(explode(',', trim($duty->actualUser, ',')) as $user)
                {
                    if($user and isset($users[$user])) { var_dump($user); $us[] = $users[$user];}
                }
                $duty->actualUser = implode(',', $us);

                if(isset($apps[$duty->application])) $duty->application = $apps[$duty->application];
                if(isset($dutyLang->importantTimeList[$duty->importantTime])) $duty->importantTime = $dutyLang->importantTimeList[$duty->importantTime];
            }

            $this->post->set('fields', $fields);
            $this->post->set('rows', $dutys);
            $this->post->set('kind', 'duty');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        $this->view->fileName        = $this->lang->duty->exportName;
        $this->view->allExportFields = $this->config->duty->list->exportFields;
        $this->view->customExport    = true;
        $this->display();
    }
}
