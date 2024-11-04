<?php
/**
 * The control file of effort module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2012 青岛易软天创网络科技有限公司 (QingDao Nature Easy Soft Network Technology Co,LTD www.cnezsoft.com)
 * @license     business(商业软件)
 * @author      Yangyang Shi <shiyangyang@cnezsoft.com>
 * @package     effort
 * @version     $Id$
 * @link        http://www.zentao.net
 */
class effort extends control
{
    /**
     * Construct function, load model of task, bug, my.
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->loadModel('todo');
        $this->loadModel('my')->setMenu();
    }

    /**
     * Batch create efforts.
     *
     * @param  string|date $date
     * @param  int         $userID
     * @access public
     * @return void
     */
    public function batchCreate($date = 'today', $userID = '')
    {
        if($date == 'today') $date   = date(DT_DATE1, time());
        if($userID == '')    $userID = $this->app->user->id;
        $user    = $this->loadModel('user')->getById($userID, 'id');
        $account = $user->account;
        if(!empty($_POST))
        {
            $this->effort->batchCreate();
            if(dao::isError()) die(js::error(dao::getError()));
            if(isonlybody()) die(js::reload('parent.parent'));
            die(js::locate($this->createLink('my', 'effort'), 'parent'));
        }

        $actions = $this->effort->getActions($date, $account);

        $typeList = array();
        if(isset($actions['typeList'])) $typeList += $actions['typeList'];

        $executionTask = array();
        if(isset($actions['executionTask'])) $executionTask += $actions['executionTask'];

        $appendExecutions = empty($actions['executions']) ? array() : $actions['executions'];

        unset($actions['typeList']);
        unset($actions['executionTask']);
        unset($actions['executions']);

        $this->view->title      = $this->lang->my->common . $this->lang->colon . $this->lang->effort->create;
        $this->view->position[] = $this->lang->effort->create;

        $this->view->date        = !is_numeric($date) ? $date : substr($date, 0, 4) . '-' . substr($date, 4, 2) . '-' . substr($date, 6, 2);
        $this->view->actions     = $actions;
        $this->view->typeList    = array(0 => '') + $typeList;
        $this->view->executions    = array(0 => '') + $this->effort->getRecentlyExecutions() + $appendExecutions;
        $this->view->executionTask = $executionTask;
        $this->display();
    }

    /**
     * create a effort for a object.
     *
     * @param  string      $objectType
     * @param  int         $objectID
     * @access public
     * @return void
     */
    public function createForObject($objectType, $objectID)
    {
        if(!empty($_POST))
        {
            $this->effort->batchCreate();
            if(dao::isError()) die(js::error(dao::getError()));
            if($this->app->viewType == 'mhtml')
            {
                die(js::locate($this->createLink($objectType, 'view', "{$objectType}ID=$objectID"), 'parent'));
            }

            if(isonlybody()) die(js::closeModal('parent.parent', 'this'));
            die(js::reload('parent'));
        }

        $date    = date(DT_DATE1);
        $efforts = $this->effort->getByObject($objectType, $objectID);

        if(isset($efforts['typeList'])) $this->view->typeList = $efforts['typeList'];
        unset($efforts['typeList']);

        $this->session->set('effortList', $this->app->getURI(true));

        $this->view->title      = $this->lang->my->common . $this->lang->colon . $this->lang->effort->create;
        $this->view->position[] = $this->lang->effort->create;

        $this->view->task       = $objectType == 'task' ? $this->loadModel('task')->getById($objectID) : '';
        $this->view->date       = $date;
        $this->view->efforts    = $efforts;
        $this->view->objectType = $objectType;
        $this->view->objectID   = $objectID;
        $this->display();
    }

    /**
     * Edit a effort.
     *
     * @param  int    $effortID
     * @access public
     * @return void
     */
    public function edit($effortID)
    {
        if(!empty($_POST))
        {
            $changes = $this->effort->update($effortID);
            if(dao::isError()) die(js::error(dao::getError()));
            if($changes)
            {
                $actionID = $this->loadModel('action')->create('effort', $effortID, 'edited');
                $this->action->logHistory($actionID, $changes);
            }

            if(dao::isError()) die(js::error(dao::getError()));
            if(isonlybody())   die(js::reload('parent.parent'));

            $url = $this->session->effortList ? $this->session->effortList : inlink('view', "effortID=$effortID");
            die(js::locate($url, 'parent'));
        }

        /* Judge a private effort or not, If private, die. */
        $effort       = $this->effort->getById($effortID);
        $effort->date = (int)$effort->date == 0 ? $effort->date : substr($effort->date, 0, 4) . '-' . substr($effort->date, 4, 2) . '-' . substr($effort->date, 6, 2);
        $executions   = $this->loadModel('execution')->getPairs($this->session->project, 'all', 'noclosed');

        /* Get the id of the latest date effort. */
        $recentDateID = 0;
        if($effort->objectType === 'task')
        {
            $recentDateID = $this->dao->select('*')
              ->from(TABLE_EFFORT)
              ->where('objectType')->eq('task')
              ->andWhere('objectID')->eq($effort->objectID)
              ->andWhere('deleted')->eq(0)
              ->orderBy('`date` desc,`id` desc')
              ->fetch('id');
            $executions = $this->execution->getPairs($effort->project, 'all', 'noclosed');
        }

        if($effort->objectType == 'doc')
        {
            $doc        = $this->dao->findById($effort->objectID)->from(TABLE_DOC)->fetch();
            $executions = $this->execution->getPairs($doc->project, 'all', 'noclosed');
        }

        if($effort->execution)
        {
            $execution = $this->execution->getByID($effort->execution);
            if($execution->status == 'closed') $executions += array($execution->id => $execution->name);
        }

        $this->view->title      = $this->lang->my->common . $this->lang->colon . $this->lang->effort->edit;
        $this->view->position[] = $this->lang->effort->edit;
        $this->view->products   = $this->loadModel('product')->getPairs();
        $this->view->executions = $executions;

        $this->view->effort = $effort;
        $this->view->recentDateID  = $recentDateID;
        $this->display();
    }

    /**
     * Batch edit a effort.
     *
     * @param  int    $effortID
     * @access public
     * @return void
     */
    public function batchEdit($from = 'browse', $userID = '')
    {
        if($userID == '') $userID = $this->app->user->id;
        $user    = $this->loadModel('user')->getById($userID, 'id');
        $account = $user->account;
        if(!empty($_POST) and $from == 'batchEdit')
        {
            $this->effort->batchUpdate();
            if(dao::isError()) die(js::error(dao::getError()));

            $effortType = isset($_SESSION['effortType']) ? $_SESSION['effortType'] : 'today';

            $url = $this->session->effortList ? $this->session->effortList : $this->createLink('my', 'effort', "type=$effortType");
            die(js::locate($url, 'parent'));
        }

        if(empty($_POST['effortIDList'])) $this->post->set('effortIDList', array());
        /* Judge a private effort or not, If private, die. */
        $efforts = $this->effort->getByAccount($_POST['effortIDList'], $account);
        if(isset($efforts['typeList']))
        {
            $typeList = $efforts['typeList'];
            unset($efforts['typeList']);
            $typeList['custom']   = '';
            $this->view->typeList = $typeList;
        }

        $this->view->title      = $this->lang->my->common . $this->lang->colon . $this->lang->effort->batchEdit;
        $this->view->position[] = $this->lang->effort->batchEdit;
        $this->view->products   = $this->loadModel('product')->getPairs();
        $this->view->executions = $this->loadModel('execution')->getPairs($this->session->project);

        $this->view->efforts = $efforts;
        $this->display();
    }

    /**
     * View a effort.
     *
     * @param  int    $effortID
     * @param  string $from     my|company
     * @access public
     * @return void
     */
    public function view($effortID, $from = 'company')
    {
        $effort = $this->effort->getById($effortID);
        if(!$effort) die(js::error($this->lang->notFound) . js::locate('back'));

        $this->view->title      = $this->lang->effort->view;
        $this->view->position[] = $this->lang->effort->view;
        $this->view->effort     = $effort;
        $this->view->work       = $this->effort->getWork($effort->objectType, $effort->objectID);
        $this->view->users      = $this->loadModel('user')->getPairs('noletter');
        $this->view->actions    = $this->loadModel('action')->getList('effort', $effortID);
        $this->view->from       = $from;
        $this->view->user       = $this->user->getById($effort->account);

        $this->display();
    }

    /**
     * Delete a effort.
     *
     * @param  int    $effortID
     * @param  string $confirm yes|no
     * @access public
     * @return void
     */
    public function delete($effortID, $confirm = 'no')
    {
        if($confirm == 'no')
        {
            die(js::confirm($this->lang->effort->confirmDelete, $this->createLink('effort', 'delete', "effortID=$effortID&confirm=yes")));
        }
        else
        {
            $effort = $this->effort->getByID($effortID);
            $this->effort->delete(TABLE_EFFORT, $effortID);
            if($effort->objectType == 'task')
            {
                $lastEffort = $this->dao->select('*')->from(TABLE_EFFORT)
                    ->where('objectType')->eq('task')
                    ->andWhere('objectID')->eq($effort->objectID)
                    ->andWhere('deleted')->eq(0)
                    ->orderBy('id desc')
                    ->limit(1)
                    ->fetch();

                $effort->last = true;
                if($lastEffort)$effort->left = $lastEffort->left;
                $this->effort->changeTaskConsumed($effort, 'delete');
            }

            die(js::reload('parent'));
        }
    }

    /**
     * Get data to export
     *
     * @param  int    $userID
     * @param  string $orderBy
     * @access public
     * @return void
     */
    public function export($userID, $orderBy = 'id_desc')
    {
        if($_POST)
        {
            $effortLang   = $this->lang->effort;
            $effortConfig = $this->config->effort;

            /* Create field lists. */
            $fields = $this->post->exportFields ? $this->post->exportFields : explode(',', $effortConfig->list->defaultFields);
            foreach($fields as $key => $fieldName)
            {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = isset($effortLang->$fieldName) ? $effortLang->$fieldName : $fieldName;
                unset($fields[$key]);
            }

            /* Get efforts. */
            $efforts = $this->dao->select('t1.*,t2.dept')->from(TABLE_EFFORT)->alias('t1')
                ->leftJoin(TABLE_USER)->alias('t2')->on('t1.account=t2.account')
                ->where($this->session->effortReportCondition)
                ->andWhere('t1.deleted')->eq(0)
                ->beginIF($this->post->exportType == 'selected')->andWhere('t1.id')->in($this->cookie->checkedItem)->fi()
                ->orderBy("$orderBy, account_desc")->fetchAll('id');

            /* Get users, bugs, tasks and times. */
            $users      = $this->loadModel('user')->getPairs('noletter');
            $products   = $this->loadModel('product')->getPairs();
            $executions = $this->loadModel('execution')->getPairs($this->session->project);
            $depts      = $this->loadModel('dept')->getOptionMenu();

            $objectTypes = array();
            foreach($efforts as $effort)
            {
                if(isset($fields['dept'])) $effort->dept = zget($depts, $effort->dept, '');
                if(isset($fields['execution'])) $effort->execution = zget($executions, $effort->execution, '');
                if(isset($fields['product']))
                {
                    $effortProducts  = explode(',', trim($effort->product, ','));
                    $effort->product = '';
                    foreach($effortProducts as $productID) $effort->product .= zget($products, $productID, '') . ' ';
                }

                if(empty($effort->objectType)) continue;
                if($effort->objectType == 'custom') continue;
                if(!isset($objectTypes[$effort->objectType])) $objectTypes[$effort->objectType]['table'] = $this->config->objectTables[$effort->objectType];
                $objectTypes[$effort->objectType]['id'][] = $effort->objectID;
            }

            $objectTitles = array();
            foreach($objectTypes as $type => $objectType) $objectTitles[$type] = $this->dao->select('*')->from($objectType['table'])->where('id')->in($objectType['id'])->fetchAll('id');

            if(isset($objectTitles['todo']))
            {
                $linkTodoObjects = array();
                foreach($objectTitles['todo'] as $todoid => $todo)
                {
                    if($todo->type == 'bug' or $todo->type == 'task')$linkTodoObjects[$todo->type][] = $todo->idvalue;
                }

                $todoTitles = array();
                foreach($linkTodoObjects as $type => $linkObjectIDs) $todoTitles[$type] = $this->dao->select('*')->from('`' . $this->config->db->prefix . $type . '`')->where('id')->in($linkObjectIDs)->fetchAll('id');
            }

            foreach($efforts as $effort)
            {
                /* fill some field with useful value. */
                if(isset($users[$effort->account])) $effort->account = $users[$effort->account];
                $effort->work = htmlspecialchars_decode($effort->work);

                if($effort->objectType != 'custom')
                {
                    if(strpos(',story,bug,case,doc,productplan,', ',' . $effort->objectType . ',') !==false)
                    {
                        $objectTitle = isset($objectTitles[$effort->objectType][$effort->objectID]) ? $objectTitles[$effort->objectType][$effort->objectID]->title : '';
                    }
                    elseif(strpos(',release,task,build,testtask', ',' . $effort->objectType . ',') !==false)
                    {
                        $objectTitle = isset($objectTitles[$effort->objectType][$effort->objectID]) ? $objectTitles[$effort->objectType][$effort->objectID]->name : '';
                    }
                    elseif($effort->objectType == 'todo')
                    {
                        $objectTitle = ' ';
                        if(!empty($objectTitles[$effort->objectType][$effort->objectID]))
                        {
                            $todo        = $objectTitles[$effort->objectType][$effort->objectID];
                            $objectTitle = $todo->name;
                            if($todo->type != 'custom')
                            {
                                if($todo->type == 'bug') $objectTitle = isset($todoTitles['bug'][$todo->idvalue]) ? $todoTitles['bug'][$todo->idvalue]->title : $objectTitle;
                                if($todo->type == 'task') $objectTitle = isset($todoTitles['task'][$todo->idvalue]) ? $todoTitles['task'][$todo->idvalue]->name : $objectTitle;
                            }
                        }
                    }
                    if(isset($effortLang->objectTypeList[$effort->objectType])) $effort->objectType = $effortLang->objectTypeList[$effort->objectType] . " : #{$effort->objectID} " . $objectTitle;
                }
                else
                {
                    $effort->objectType = $effortLang->objectTypeList[$effort->objectType];
                }
            }

            $width['account']    = 11;
            $width['date']       = 11;
            $width['consumed']   = 15;
            $width['left']       = 15;
            $width['work']       = 40;
            $width['objectType'] = 40;

            if(isset($this->config->bizVersion)) list($fields, $efforts) = $this->loadModel('workflowfield')->appendDataFromFlow($fields, $efforts);

            $this->post->set('fields', $fields);
            $this->post->set('rows', $efforts);
            $this->post->set('kind', $this->lang->effort->common);
            $this->post->set('width', $width);
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        $this->view->fileName        = $this->app->user->account . ' - ' . $this->lang->effort->common;
        $this->view->allExportFields = $this->config->effort->list->exportFields;
        $this->view->selectedFields  = $this->config->effort->list->defaultFields;
        $this->view->customExport    = true;
        $this->display();
    }

    /**
     * Remind not record.
     *
     * @access public
     * @return void
     */
    public function remindNotRecord()
    {
        $users = $this->loadModel('user')->getPairs('nodeleted|noclosed|noempty|noletter');

        $timestamp = strtotime('yesterday');
        $yesterday = date('Y-m-d', $timestamp);
        $efforts   = $this->dao->select('distinct account')->from(TABLE_EFFORT)->where('date')->eq($yesterday)->andWhere('deleted')->eq(0)->fetchPairs('account', 'account');

        $this->loadModel('sso');
        if($this->config->sso->turnon)
        {
            $leaveUsers = $this->effort->getRanzhiLeaveUsers();
            if(!empty($leaveUsers))
            {
                $linkedZentaoUsers = $this->dao->select('*')->from(TABLE_USER)->where('ranzhi')->in($leaveUsers)->fetchPairs('account', 'account');
                foreach($linkedZentaoUsers as $account) unset($users[$account]);
            }
        }

        $noRecordUsers = array_diff(array_keys($users), array_keys($efforts));

        $this->loadModel('mail');
        $subject = $this->lang->effort->remindSubject;
        $domain  = zget($this->config->mail, 'domain', common::getSysURL());
        $link    = $domain . $this->createLink('effort', 'batchCreate', 'date=' . date('Ymd', $timestamp));
        $content = sprintf($this->lang->effort->remindContent, $link);

        foreach($noRecordUsers as $toList)
        {
            echo "Send to $toList\n";
            $this->mail->send($toList, $subject, $content);
            if($this->mail->isError()) error_log(join("\n", $this->mail->getError()));
        }
    }
}
