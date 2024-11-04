<?php
/**
 * The model file of effort module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2012 青岛易软天创网络科技有限公司 (QingDao Nature Easy Soft Network Technology Co,LTD www.cnezsoft.com)
 * @license     business(商业软件)
 * @author      Yangyang Shi <shiyangyang@cnezsoft.com>
 * @package     effort
 * @version     $Id$
 * @link        http://www.zentao.net
 */
?>
<?php
class effortModel extends model
{
    const DAY_IN_FUTURE = 20300101;

    /**
     * Time to int
     *
     * @param  string    $date
     * @access public
     * @return int
     */
    public function timeToInt($date)
    {
        $newDate = $date;
        if(strpos($date, ':') !== false)
        {
            list($min, $sec) = explode(':', $date);
            $min     = str_pad($min, 2, '0', STR_PAD_LEFT);
            $sec     = str_pad($sec, 2, '0', STR_PAD_LEFT);
            $newDate = $min . $sec;
        }

        return empty($newDate) ? '2400' : $newDate;
    }

    /**
     * Batch create efforts.
     *
     * @param  date   $date
     * @param  string $account
     * @access public
     * @return void
     */
    public function batchCreate()
    {
        $this->loadModel('task');
        $this->loadModel('action');

        $now        = helper::now();
        $efforts    = fixer::input('post')->get();
        $data       = array();
        $taskIDList = array();
        foreach($efforts->id as $id => $num)
        {
            if(strpos($efforts->objectType[$id], '_') !== false)
            {
                $pos = strpos($efforts->objectType[$id], '_');
                $efforts->objectID[$id]   = substr($efforts->objectType[$id], $pos + 1);
                $efforts->objectType[$id] = substr($efforts->objectType[$id], 0, $pos);
            }
            elseif(empty($efforts->objectID[$id]))
            {
                $efforts->objectType[$id] = 'custom';
                $efforts->objectID[$id]   = 0;
            }

            if(!empty($efforts->work[$id]) or !empty($efforts->consumed[$id]))
            {
                if(empty($efforts->work[$id]))           die(js::alert(sprintf($this->lang->effort->nowork, $efforts->id[$id])));
                if(empty($efforts->consumed[$id]))       die(js::alert($this->lang->effort->common . $efforts->id[$id] . ' : ' . $this->lang->effort->consumed . $this->lang->effort->notEmpty));
                if($efforts->consumed[$id] < 0)          die(js::alert($this->lang->effort->common . $efforts->id[$id] . ' : ' . $this->lang->effort->consumed . $this->lang->effort->notNegative));
                if(!is_numeric($efforts->consumed[$id])) die(js::alert($this->lang->effort->common . $efforts->id[$id] . ' : ' . $this->lang->effort->consumed . $this->lang->effort->isNumber));

                $left = isset($efforts->left[$num]) ? $efforts->left[$num] : '';
                if(!empty($left) and !is_numeric($left)) die(js::alert($this->lang->effort->common . $efforts->id[$id] . ' : ' . $this->lang->effort->left . $this->lang->effort->isNumber));
                if(!empty($left) and $left < 0)          die(js::alert($this->lang->effort->common . $efforts->id[$id] . ' : ' . $this->lang->effort->left . $this->lang->effort->notNegative));
                if($efforts->objectType[$id] == 'task' and empty($left) and !is_numeric($left))  die(js::alert($this->lang->effort->common . $efforts->id[$id] . ' : ' . $this->lang->effort->left . $this->lang->effort->notEmpty));

                $data[$id] = new stdclass();
                $data[$id]->product   = ',0,';
                $data[$id]->execution = 0;
                $data[$id]->objectID  = 0;

                $data[$id]->date       = isset($efforts->dates[$id]) ? $efforts->dates[$id] : $efforts->date;
                $data[$id]->consumed   = $efforts->consumed[$id];
                $data[$id]->account    = $this->app->user->account;
                $data[$id]->deptID     = $this->app->user->dept;
                $data[$id]->work       = $efforts->work[$id];
                $data[$id]->objectType = $efforts->objectType[$id];

                if($data[$id]->date > $now) die(js::alert($this->lang->effort->common . $efforts->id[$id] . ' : ' . $this->lang->effort->left . $this->lang->effort->notFuture));

                if($data[$id]->objectType == 'task')
                {
                    $taskIDList[$efforts->objectID[$id]] = $efforts->objectID[$id];
                    $data[$id]->left = (float)$left;
                }

                if($data[$id]->objectType != 'custom') $data[$id]->objectID = $efforts->objectID[$id];

                if($data[$id]->objectID != 0)
                {
                    $relation    = $this->action->getRelatedFields($data[$id]->objectType, $data[$id]->objectID);
                    $data[$id]->product   = $relation['product'];
                    $data[$id]->project   = (int)$relation['project'];
                    $data[$id]->execution = (int)$relation['execution'];
                }

                if(!empty($efforts->execution[$id])) $data[$id]->execution = (int)$efforts->execution[$id];

                if((!empty($efforts->execution[$id])) && ($data[$id]->objectID == 0))
                {
                    $products = $this->loadModel('product')->getProducts($efforts->execution[$id]);
                    ksort($products);
                    $data[$id]->product = ',' . join(',', array_keys($products)) . ',';
                }
            }
        }

        $tasks = $this->dao->select('*')->from(TABLE_TASK)->where('id')->in($taskIDList)->fetchAll('id');
        $executionTeams = $this->dao->select('*')->from(TABLE_TEAM)->where('root')->in($taskIDList)->andWhere('type')->eq('task')->orderBy('order')->fetchGroup('root', 'account');
        $taskEffortGroups = $this->dao->select('id,account,consumed,`left`,objectID')->from(TABLE_EFFORT)->where('objectType')->eq('task')->andWhere('deleted')->eq(0)->andWhere('objectID')->in($taskIDList)->orderBy('id')->fetchGroup('objectID');

        $lastDate = $this->dao->select('objectID,max(date) as date')->from(TABLE_EFFORT)->where('objectID')->in($taskIDList)->andWhere('objectType')->eq('task')->andWhere('deleted')->eq(0)->fetchPairs('objectID', 'date');

        $consumed = 0;
        $now      = helper::now();
        $errors   = array();

        $this->loadModel('story');
        $this->loadModel('task');
        foreach($data as $id => $effort)
        {
            $this->dao->insert(TABLE_EFFORT)->data($effort)->autoCheck()->batchCheck($this->config->effort->create->requiredFields, 'notempty')->exec();
            if(dao::isError())
            {
                $errors[$id] = dao::getError();
                continue;
            }

            $effortID = $this->dao->lastInsertID();

            $processTask = false;
            $fromAction  = false;
            if($effort->objectType == 'task') $processTask = true;
            if(!empty($_POST['actionID'][$id]) and $effort->objectType == 'task')
            {
                $action = $this->dao->select('*')->from(TABLE_ACTION)->where('id')->eq($this->post->actionID[$id])->fetch();
                if(isset($action->action) and ($action->action == 'opened' or $action->action == 'edited')) $fromAction = true;
            }

            if($processTask)
            {
                $taskEffortGroups[$effort->objectID][] = $effort;
                $task = $tasks[$effort->objectID];

                $newTask = json_encode($task);
                $newTask = json_decode($newTask);

                $newTask->consumed       = $task->consumed + $effort->consumed;
                $newTask->lastEditedBy   = $this->app->user->account;
                $newTask->lastEditedDate = $now;
                if(helper::isZeroDate($task->realStarted)) $newTask->realStarted = $now;

                if(empty($lastDate[$effort->objectID]) or $lastDate[$effort->objectID] <= $effort->date)
                {
                    $newTask->left = $effort->left;
                    $lastDate[$effort->objectID] = $effort->date;
                }

                /* Fix for bug #1853. */
                if($fromAction)
                {
                    $actionID = $this->action->create('task', $effort->objectID, 'RecordEstimate', $effort->work, $effort->consumed);
                }
                elseif($effort->left == 0 and strpos('done,cancel,closed', $task->status) === false)
                {
                    $newTask->status         = 'done';
                    $newTask->assignedTo     = $task->openedBy;
                    $newTask->assignedDate   = $now;
                    $newTask->finishedBy     = $this->app->user->account;
                    $newTask->finishedDate   = $now;
                    $actionID = $this->action->create('task', $effort->objectID, 'Finished', $effort->work);
                }
                elseif($effort->left != 0 and strpos('done,cancel,closed', $task->status) !== false)
                {
                    $newTask->status         = 'doing';
                    $newTask->finishedBy     = '';
                    $newTask->canceledBy     = '';
                    $newTask->closedBy       = '';
                    $newTask->closedReason   = '';
                    $newTask->finishedDate   = '0000-00-00';
                    $newTask->canceledDate   = '0000-00-00';
                    $newTask->closedDate     = '0000-00-00';
                    $actionID = $this->action->create('task', $effort->objectID, 'Activated', $effort->work);
                }
                elseif($task->status == 'wait')
                {
                    $newTask->status       = 'doing';
                    $newTask->assignedTo   = $this->app->user->account;
                    $newTask->assignedDate = $now;
                    $newTask->realStarted  = date('Y-m-d');
                    $actionID = $this->action->create('task', $effort->objectID, 'Started', $effort->work);
                }
                else
                {
                    $actionID = $this->action->create('task', $effort->objectID, 'RecordEstimate', $effort->work, $effort->consumed);
                }

                /* Process multi-person task. Update consumed on team table. */
                if(isset($executionTeams[$effort->objectID]))
                {
                    $executionTeam = $executionTeams[$effort->objectID];
                    $teams         = array_keys($executionTeam);
                    $taskEfforts   = isset($taskEffortGroups[$effort->objectID]) ? $taskEffortGroups[$effort->objectID] : array();

                    $effortGroups = array();
                    foreach($taskEfforts as $taskEffort) $effortGroups[$taskEffort->account][] = $taskEffort;

                    foreach($effortGroups as $account => $taskEffort)
                    {
                        $consumed = 0;
                        foreach($taskEffort as $thisEffort) $consumed += $thisEffort->consumed;
                        $this->dao->update(TABLE_TEAM)->set('consumed')->eq($consumed)->where('type')->eq('task')->andWhere('root')->eq($effort->objectID)->andWhere('account')->eq($account)->exec();
                        $executionTeam[$effort->account]->consumed = $consumed;
                    }
                    $this->dao->update(TABLE_TEAM)->set('left')->eq($effort->left)->where('type')->eq('task')->andWhere('root')->eq($effort->objectID)->andWhere('account')->eq($effort->account)->exec();
                    $executionTeam[$effort->account]->left = $effort->left;
                    $newTask = $this->task->computeHours4Multiple($task, $newTask, $executionTeam);
                }

                $this->dao->update(TABLE_ACTION)->set('efforted')->eq('1')->where('id')->eq($actionID)->exec();
                $changes = common::createChanges($task, $newTask);
                if($changes and !empty($actionID)) $this->action->logHistory($actionID, $changes);

                $this->dao->update(TABLE_TASK)->data($newTask)->where('id')->eq($effort->objectID)->exec();
                if($task->parent > 0) $this->task->updateParentStatus($task->id);
                if($newTask->story) $this->story->setStage($newTask->story);
                $tasks[$effort->objectID] = $newTask;

                if($newTask->parent > 0)
                {
                    if($newTask->status == 'done') $this->loadModel('task')->updateParentStatus($newTask->id, $newTask->parent, 'done');
                    $this->task->computeWorkingHours($newTask->parent);
                }
            }

            if(isset($efforts->actionID[$id]))
            {
                $this->dao->update(TABLE_ACTION)->set('efforted')->eq(1)
                    ->where('id')->le($efforts->actionID[$id])
                    ->andWhere('actor')->eq($this->app->user->account)
                    ->andWhere('objectType')->eq($effort->objectType)
                    ->andWhere('objectID')->eq($effort->objectID)
                    ->andWhere('date')->ge("$effort->date 00:00:00")
                    ->andWhere('date')->le("$effort->date 23:59:59")
                    ->exec();
            }

            if($effort->objectType == 'feedback')
            {
                $newConsumed = $this->dao->select('sum(consumed) as consumed')->from(TABLE_EFFORT)->where('objectID')->eq($effort->objectID)->andWhere('objectType')->eq($effort->objectType)->fetch();

                $oldConsumed = new stdclass();
                $oldConsumed->consumed = $newConsumed->consumed - $effort->consumed;

                $changes = common::createChanges($oldConsumed, $newConsumed);
                if($changes or !empty($effort->work))
                {
                    $actionID = $this->action->create($effort->objectType, $effort->objectID, 'RecordEstimate', $effort->work, $effort->consumed);
                    $this->action->logHistory($actionID, $changes);
                }
            }

            $this->action->create('effort', $effortID, 'created');
        }

        return $errors;
    }

    /**
     * update efforts.
     *
     * @param  date $date
     * @access public
     * @return void
     */
    public function batchUpdate($account = '')
    {
        $this->loadModel('action');
        $efforts      = fixer::input('post')->remove('effortIDList')->get();
        $effortIDList = explode(',', $_POST['effortIDList']);
        $oldEfforts   = $this->dao->select('*')->from(TABLE_EFFORT)->where('id')->in($effortIDList)->andWhere('deleted')->eq(0)->fetchAll('id');

        if(empty($efforts->id)) $efforts->id = array();
        $taskEffortPairs = array();
        foreach($oldEfforts as $effort)
        {
            if($effort->objectType == 'task') $taskEffortPairs[$effort->objectID] = $effort->objectID;
        }
        $lastEffortGroup = $this->dao->select('*')
            ->from(TABLE_EFFORT)
            ->where('objectType')->eq('task')
            ->andWhere('deleted')->eq(0)
            ->andWhere('objectID')->in($taskEffortPairs)
            ->orderBy('date_desc')
            ->fetchGroup('objectID', 'id');

        /* delete efforts.*/
        $deleteIDList = array_diff($effortIDList, $efforts->id);

        if($deleteIDList)
        {
            sort($deleteIDList);

            $taskIDList = array();
            foreach($deleteIDList as $id)
            {
                $effort = $oldEfforts[$id];
                if($effort->objectType == 'task') $taskIDList[] = $effort->objectID;
            }
            $tasks = array();
            if(!empty($taskIDList)) $tasks = $this->loadModel('task')->getByList($taskIDList);

            foreach($deleteIDList as $id)
            {
                $effort = $oldEfforts[$id];
                if($effort->account != $this->app->user->account) continue;
                if($effort->objectType == 'task' and isset($lastEffortGroup[$effort->objectID]))
                {
                    $lastEfforts = $lastEffortGroup[$effort->objectID];

                    reset($lastEfforts);
                    if(key($lastEfforts) == $id and count($lastEfforts) >= 2)
                    {
                        $effort->left = count($lastEfforts) >= 2 ? next($lastEfforts)->left : $effort->left;
                        $effort->last = true;
                        unset($lastEffortGroup[$effort->objectID][$id]);
                    }
                }

                if($effort->objectType == 'task')
                {
                    $this->changeTaskConsumed($effort, 'delete', '', zget($tasks, $effort->objectID, ''));
                    $tasks[$effort->objectID]->consumed -= $effort->consumed;
                }
                $this->dao->delete()->from(TABLE_EFFORT)->where('id')->eq($id)->exec();
                $this->action->create('effort', $effortID, 'Deleted');
            }
        }

        /* update efforts.*/
        $data       = array();
        $taskIDList = array();
        foreach($efforts->id as $id)
        {
            $pos = strpos($efforts->objectType[$id], '_');
            $efforts->objectID[$id]   = substr($efforts->objectType[$id], $pos + 1);
            $efforts->objectType[$id] = substr($efforts->objectType[$id], 0, $pos);

            if(!empty($efforts->work[$id]) and (($efforts->objectType[$id] != 'custom' and $efforts->objectID[$id] != '') or $efforts->objectType[$id] == 'custom'))
            {
                if(empty($efforts->work[$id]))           die(js::alert(sprintf($this->lang->effort->nowork, $efforts->id[$id])));
                if(empty($efforts->consumed[$id]))       die(js::alert($this->lang->effort->common . $efforts->id[$id] . ' : ' . $this->lang->effort->consumed . $this->lang->effort->notEmpty));
                if($efforts->consumed[$id] < 0)          die(js::alert($this->lang->effort->common . $efforts->id[$id] . ' : ' . $this->lang->effort->consumed . $this->lang->effort->notNegative));
                if(!is_numeric($efforts->consumed[$id])) die(js::alert($this->lang->effort->common . $efforts->id[$id] . ' : ' . $this->lang->effort->consumed . $this->lang->effort->isNumber));
                if(!empty($efforts->left[$id]) and !is_numeric($efforts->left[$id])) die(js::alert($this->lang->effort->common . $efforts->id[$id] . ' : ' . $this->lang->effort->left . $this->lang->effort->isNumber));
                if(!empty($efforts->left[$id]) and $efforts->left[$id] < 0)          die(js::alert($this->lang->effort->common . $efforts->id[$id] . ' : ' . $this->lang->effort->left . $this->lang->effort->notNegative));

                $data[$id] = new stdclass();
                $data[$id]->product   = implode($efforts->product[$id], ',');
                $data[$id]->execution = $efforts->execution[$id];

                $data[$id]->date       = $efforts->date[$id];
                $data[$id]->consumed   = $efforts->consumed[$id];
                $data[$id]->left       = $efforts->left[$id];
                $data[$id]->objectID   = $efforts->objectID[$id];
                $data[$id]->objectType = $efforts->objectType[$id];
                $data[$id]->work       = $efforts->work[$id];

                if($data[$id]->date > helper::now()) die(js::alert($this->lang->effort->common . $efforts->id[$id] . ' : ' . $this->lang->effort->left . $this->lang->effort->notFuture));

                if($data[$id]->objectType == 'task') $taskIDList[] = $data[$id]->objectID;
            }
        }

        $tasks = array();
        if(!empty($taskIDList)) $tasks = $this->loadModel('task')->getByList($taskIDList);

        foreach($data as $id => $effort)
        {
            $oldEffort = $oldEfforts[$id];
            $effort->account = $oldEffort->account;
            $this->dao->update(TABLE_EFFORT)->data($effort)->autoCheck()->where('id')->eq($id)->exec();

            $changes = common::createChanges($oldEffort, $effort);
            if($changes)
            {
                $actionID = $this->action->create('effort', $id, 'Edited');
                $this->action->logHistory($actionID, $changes);

                if($effort->objectType == 'task')
                {
                    $this->changeTaskConsumed($effort, 'add', $oldEffort, zget($tasks, $effort->objectID, ''));
                    $tasks[$effort->objectID]->consumed = $tasks[$effort->objectID]->consumed + $effort->consumed;
                    if($oldEffort->objectType == 'task' and $oldEffort->objectID == $effort->objectID) $tasks[$effort->objectID]->consumed -= $oldEffort->consumed;
                }
                if($oldEffort->objectType == 'task' and $oldEffort->objectID != $effort->objectID)
                {
                    $this->changeTaskConsumed($oldEffort, 'delete', '', zget($tasks, $oldEffort->objectID, ''));
                    $tasks[$oldEffort->objectID]->consumed = $tasks[$oldEffort->objectID]->consumed - $oldEffort->consumed;
                }
            }
        }
    }

    /**
     * update a effort.
     *
     * @param  int    $effortID
     * @access public
     * @return void
     */
    public function update($effortID)
    {
        $oldEffort = $this->getById($effortID);
        $effort = fixer::input('post')
            ->setDefault('account', $oldEffort->account)
            ->cleanInt('objectID')
            ->join('product', ',')
            ->get();
        $effort->product  = ',' . $effort->product . ',';

        if($effort->consumed < 0) die(js::alert($this->lang->effort->consumed . $this->lang->effort->notNegative));
        if($effort->left < 0)     die(js::alert($this->lang->effort->left . $this->lang->effort->notNegative));

        if($effort->date > helper::now()) die(js::alert($this->lang->effort->common . $efforts->id[$id] . ' : ' . $this->lang->effort->left . $this->lang->effort->notFuture));

        $this->dao->update(TABLE_EFFORT)->data($effort)
            ->autoCheck()
            ->batchCheck($this->config->effort->edit->requiredFields, 'notempty')
            ->where('id')->eq($effortID)
            ->exec();

        if(!dao::isError())
        {
            $changes = common::createChanges($oldEffort, $effort);
            if($changes) $this->changeTaskConsumed($effort, 'add', $oldEffort);
            if($oldEffort->objectType == 'task' and $oldEffort->objectID != $effort->objectID) $this->changeTaskConsumed($oldEffort, 'delete');
            return $changes;
        }
    }

    /**
     * Get info of a effort.
     *
     * @param  int    $effortID
     * @access public
     * @return object|bool
     */
    public function getById($effortID)
    {
        $effort = $this->dao->findById((int)$effortID)->from(TABLE_EFFORT)->fetch();
        if(!$effort) return false;
        $effort->date = str_replace('-', '', $effort->date);
        return $effort;
    }

    /**
     * Parse date
     *
     * @param  string $date
     * @access public
     * @return array
     */
    public function parseDate($date)
    {
        $this->app->loadClass('date');
        if($date == 'today')
        {
            $begin = date('Y-m-d', time());
            $end   = $begin;
        }
        elseif($date == 'yesterday')
        {
            $begin = date::yesterday();
            $end   = $begin;
        }
        elseif($date == 'thisweek')
        {
            extract(date::getThisWeek());
        }
        elseif($date == 'lastweek')
        {
            extract(date::getLastWeek());
        }
        elseif($date == 'thismonth')
        {
            extract(date::getThisMonth());
        }
        elseif($date == 'lastmonth')
        {
            extract(date::getLastMonth());
        }
        elseif($date == 'all')
        {
            $begin = '1970-01-01';
            $end   = '2109-01-01';
        }
        elseif(is_array($date))
        {
            list($begin, $end) = $date;
        }
        else
        {
            $begin = $date;
            $end   = $date;
        }
        return array(substr($begin, 0, 10), substr($end, 0, 10));
    }

    /**
     * Get effort list of a user.
     *
     * @param  date   $date
     * @param  string $account
     * @param  string $status   all|today|thisweek|lastweek|before, or a date.
     * @param  int    $limit
     * @access public
     * @return void
     */
    public function getList($begin, $end, $account = '', $product = 0, $execution = 0, $dept = 0, $orderBy = 'date_desc', $pager = null)
    {
        $orderBy = empty($orderBy) ? 'date_desc' : $orderBy;
        $efforts = array();
        $users   = array();
        if($dept)   $users = $this->loadModel('dept')->getDeptUserPairs($dept);
        if($account)$users = array($account => $account);

        $efforts = $this->dao->select('t1.*,t2.dept')->from(TABLE_EFFORT)->alias('t1')
            ->leftJoin(TABLE_USER)->alias('t2')->on('t1.account=t2.account')
            ->where(1)
            ->andWhere('t1.deleted')->eq(0)
            ->beginIF($begin)->andWhere("t1.date")->ge($begin)->fi()
            ->beginIF($end)->andWhere("t1.date")->le($end)->fi()
            ->beginIF($users or $dept)->andWhere('t1.account')->in(array_keys($users))->fi()
            ->beginIF($product)->andWhere('t1.product')->like("%,$product,%")->fi()
            ->beginIF($execution)->andWhere('t1.execution')->eq($execution)->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll();

        /* Set session. */
        $sql = explode('WHERE', $this->dao->get());
        $sql = explode('ORDER', $sql[1]);
        $this->session->set('effortReportCondition', $sql[0]);

        $objectIdList = array();
        foreach($efforts as $effort) $objectIdList[$effort->objectType][$effort->objectID] = $effort->objectID;
        list($objectTypeList, $todos) = $this->getEffortTitles($objectIdList);
        foreach($efforts as $effort)
        {
            if(isset($objectTypeList[$effort->objectType]))
            {
                $title = $objectTypeList[$effort->objectType];
                $effort->objectTitle = zget($title, $effort->objectID, '');
                if($effort->objectType == 'todo' and isset($todos[$effort->objectID]))
                {
                    $todo = $todos[$effort->objectID];
                    $effort->objectTitle = $todo->name;
                    if(isset($objectTypeList[$todo->type])) $effort->objectTitle = zget($objectTypeList[$todo->type], $todo->idvalue, '');
                }
                if($effort->objectType == 'case') $effort->objectType = 'testcase';
            }
        }
        return $efforts;
    }

    /**
     * Get actions.
     *
     * @param  int    $date
     * @param  int    $account
     * @param  string $objectType
     * @param  int    $objectID
     * @access public
     * @return array
     */
    public function getActions($date, $account, $objectType = '', $objectID = '')
    {
        /* Get all actions. */
        $date = is_numeric($date) ? substr($date, 0, 4) . '-' . substr($date, 4, 2) . '-' . substr($date, 6, 2) : $date;
        $dateLength = strlen($date);
        $allActions = $this->dao->select('*')->from(TABLE_ACTION)
            ->where('actor')->eq($account)
            ->andWhere("(LEFT(`date`, $dateLength) = '$date')")
            ->beginIF(!empty($objectType))->andWhere('objectType')->eq($objectType)->fi()
            ->beginIF(!empty($objectID))->andWhere('objectID')->eq($objectID)->fi()
            ->andWhere('efforted')->eq(0)
            ->orderBy('id_desc')
            ->limit(30)
            ->fetchAll('id');

        /* Init vars. */
        $taskIdList = array();
        foreach($allActions as $id => $action)
        {
            if($action->objectType == 'task')
            {
                $deleted = $this->dao->select('deleted')->from(TABLE_TASK)->where('`id`')->eq($action->objectID)->fetch('deleted');
                if($deleted == 1)
                {
                    unset($allActions[$id]);
                    continue;
                }

                $taskIdList[$action->objectID] = $action->objectID;
            }
        }
        $teams       = $this->dao->select('id,root,type,account')->from(TABLE_TEAM)->where('root')->in($taskIdList)->andWhere('type')->eq('task')->fetchGroup('root', 'id');
        $parentTasks = $this->dao->select('id,name')->from(TABLE_TASK)->where('`id`')->in($taskIdList)->andWhere('parent')->eq(-1)->fetchGroup('id', 'name');

        $actions     = array();
        $executions  = array();
        $beforeID    = 0;
        $dealActions = array();

        foreach($allActions as $id => $action)
        {
            /* Remove started or finished or multiple or parent task. */
            if($action->objectType == 'task' and ($action->action == 'started' or $action->action == 'finished')) continue;
            if($action->objectType == 'task' and isset($teams[$action->objectID])) continue;
            if($action->objectType == 'task' and isset($parentTasks[$action->objectID])) continue;

            if(isset($dealActions[$action->objectType][$action->objectID])) continue;

            if(isset($this->lang->effort->objectTypeList[$action->objectType]))
            {
                $work = $this->getWork($action->objectType, $action->objectID);

                $key      = $action->objectType . '_' . $action->objectID;
                $objectID = $action->objectID;
                if(!isset($work[$objectID])) continue;
                $typeList[$key] = '[' . zget($this->lang->effort->objectTypeList, $action->objectType, $action->objectType) . ']' . $objectID . ':' . $work[$objectID];
                $action->work   = $this->lang->effort->deal . $this->lang->effort->objectTypeList[$action->objectType] . ' : ' . $work[$objectID];

                $beforeID = $id;
                unset($action->product);

                $actions[$id] = $action;
                $executions[$action->execution] = $action->execution;
                if($action->objectType == 'task') $executionTask[$key] = $action->execution; // Fix bug #1581.
                $dealActions[$action->objectType][$action->objectID] = true;
            }
        }

        $stories = $this->dao->select('id,title')->from(TABLE_STORY)->where('assignedTo')->eq($this->app->user->account)->andWhere('deleted')->eq('0')->fetchAll();
        foreach($stories as $story)
        {
            $key = 'story_' . $story->id;
            $typeList[$key] = "[{$this->lang->effort->objectTypeList['story']}]" . $story->id . ':' . $story->title;
        }

        /* Get tasks and remove multiple or parent tasks. */
        $tasks = $this->dao->select('id,execution,name,parent')->from(TABLE_TASK)->where('assignedTo')->eq($this->app->user->account)->andWhere('deleted')->eq('0')->fetchAll();
        foreach($tasks as $task)
        {
            if(isset($teams[$task->id])) continue;
            if($task->parent < 0) continue;

            $key = 'task_' . $task->id;
            $typeList[$key]               = "[{$this->lang->effort->objectTypeList['task']}]" . $task->id . ':' . $task->name;
            $executionTask[$key]          = $task->execution;
            $executions[$task->execution] = $task->execution;
        }

        $bugs = $this->dao->select('id,title')->from(TABLE_BUG)->where('assignedTo')->eq($this->app->user->account)->andWhere('deleted')->eq(0)->fetchAll();
        foreach($bugs as $bug)
        {
            $key = 'bug_' . $bug->id;
            $typeList[$key] = "[{$this->lang->effort->objectTypeList['bug']}]" . $bug->id . ':' . $bug->title;
        }

        $actions['typeList'] = isset($typeList) ? $typeList : array();
        $executions = $this->loadModel('execution')->getByIdList($executions);
        foreach($executions as $execution) $actions['executions'][$execution->id] = $execution->name;

        if(isset($executionTask)) $actions['executionTask'] = $executionTask;
        return $actions;
    }

    /**
     * Get efforts by account.
     *
     * @param  string    $date
     * @param  string    $account
     * @access public
     * @return object
     */
    public function getByAccount($effortIDList, $account = '')
    {
        $efforts = $this->dao->select('*')->from(TABLE_EFFORT)
            ->where('id')->in($effortIDList)
            ->andWhere('deleted')->eq(0)
            ->beginIF(!empty($account))->andWhere('account')->eq($account)->fi()
            ->fetchAll('id');

        if(!empty($efforts))
        {
            $objectIdList = array();
            foreach($efforts as $effort) $objectIdList[$effort->objectType][$effort->objectID] = $effort->objectID;
            list($objectTypeList, $todos) = $this->getEffortTitles($objectIdList);
            $objectTypeList['user']       = $this->loadModel('user')->getPairs('noletter');
            $objectTypeList['custom'][0]  = $this->lang->effort->objectTypeList['custom'];

            foreach($efforts as $effort)
            {
                $objectType = $effort->objectType;
                $objectID   = $effort->objectID;
                $key = $objectType . '_' . $objectID;
                $typeList[$key] = isset($objectTypeList[$objectType][$objectID]) ? "[$key]:" . $objectTypeList[$objectType][$objectID] : '';
                if($objectType != 'custom' and isset($objectTypeList[$objectType][$objectID]))
                {
                    $typeList[$key] = strtoupper($objectType) . $objectID . ':' . $objectTypeList[$objectType][$objectID];
                }
                if($objectType == 'todo' and isset($objectTypeList[$todo->type]))
                {
                    $todo = $todos[$objectID];
                    $typeList[$key] = strtoupper($objectType) . $objectID . ':' . $objectTypeList[$todo->type][$objectID];
                }
            }

            $stories = $this->dao->select('id,title')->from(TABLE_STORY)->where('assignedTo')->eq($account)->andWhere('deleted')->eq('0')->fetchAll();
            foreach($stories as $story)
            {
                $key = 'story_' . $story->id;
                $typeList[$key] = '[S]' . $story->id . ':' . $story->title;
            }

            $tasks = $this->dao->select('id,name')->from(TABLE_TASK)->where('assignedTo')->eq($account)->andWhere('deleted')->eq('0')->fetchAll();
            foreach($tasks as $task)
            {
                $key = 'task_' . $task->id;
                $typeList[$key] = '[T]' . $task->id . ':' . $task->name;
            }

            $bugs = $this->dao->select('id,title')->from(TABLE_BUG)->where('assignedTo')->eq($account)->andWhere('deleted')->eq('0')->fetchAll();
            foreach($bugs as $bug)
            {
                $key = 'bug_' . $bug->id;
                $typeList[$key] = '[B]' . $bug->id . ':' . $bug->title;
            }

            $efforts['typeList'] = $typeList;
        }
        return $efforts;
    }

    /**
     * Get efforts by object.
     *
     * @param  string    $objectType
     * @param  int       $objectID
     * @access public
     * @return object
     */
    public function getByObject($objectType, $objectID)
    {
        $efforts = $this->dao->select('*')->from(TABLE_EFFORT)->where('objectType')->eq($objectType)->andWhere('objectID')->eq($objectID)->andWhere('deleted')->eq(0)->orderBy('date_asc, id')->fetchAll('id');
        if(!empty($efforts))
        {
            foreach($efforts as $effort) $idList[$objectType][$effort->objectID] = $effort->objectID;
            list($objectTypeList, $todos) = $this->getEffortTitles($idList);
            $objectTypeList['user']       = $this->loadModel('user')->getPairs('noletter');
            $objectTypeList['custom'][0]  = $this->lang->effort->objectTypeList['custom'];

            $typeList = array();
            foreach($efforts as $effort)
            {
                if(!isset($objectTypeList[$effort->objectType])) continue;

                $key = $effort->objectType . '_' . $effort->objectID;
                $typeList[$key] = "[$key]:" . $objectTypeList[$effort->objectType][$effort->objectID];
            }
            $efforts['typeList'] = $typeList;
        }
        return $efforts;
    }

    /**
     * Get work.
     *
     * @param  string $objectType
     * @param  int    $objectID
     * @access public
     * @return array
     */
    public function getWork($objectType, $objectID)
    {
        $work = array();
        /* form begin or end for action.*/
        $idList[$objectType][$objectID] = $objectID;
        list($objectTypeList, $todos)   = $this->getEffortTitles($idList);
        if(isset($objectTypeList[$objectType]))
        {
            $work[$objectID] = $objectTypeList[$objectType][$objectID];
            if($objectType == 'todo')
            {
                $todo = $todos[$objectID];
                if(isset($objectTypeList[$todo->type])) $todo->name = $objectTypeList[$todo->type][$todo->idvalue];
                $work[$objectID] = $todo->name;
            }
        }
        return $work;
    }

    /**
     * Change task consumed.
     *
     * @param  object $effort
     * @param  string $action
     * @param  object $oldEffort
     * @param  object $task
     * @access public
     * @return void
     */
    public function changeTaskConsumed($effort, $action = 'add', $oldEffort = '', $task = '')
    {
        $this->loadModel('task');
        $action = $action == 'add' ? '+' : '-';
        $now    = helper::now();
        if($effort->objectType == 'task')
        {
            $this->loadModel('action');
            if(empty($task)) $task = $this->dao->select('*')->from(TABLE_TASK)->where('id')->eq($effort->objectID)->fetch();
            $teams = $this->dao->select('*')->from(TABLE_TEAM)->where('type')->eq('task')->andWhere('root')->eq($effort->objectID)->orderBy('`order`')->fetchAll('account');
            $effortGroups = $this->dao->select('id,account,consumed,`left`')->from(TABLE_EFFORT)->where('objectType')->eq('task')->andWhere('objectID')->eq($effort->objectID)->andWhere('deleted')->eq(0)->orderBy('id')->fetchGroup('account', 'id');

            $lastDate = $this->dao->select('max(date) as date')->from(TABLE_EFFORT)->where('objectID')->eq($effort->objectID)->andWhere('objectType')->eq('task')->andWhere('deleted')->eq(0)->fetch('date');

            $consumed = 0;
            foreach($effortGroups as $account => $efforts)
            {
                foreach($efforts as $thisEffort) $consumed += $thisEffort->consumed;
            }

            $actionID = 0;
            $newTask  = new stdclass();
            $newTask->consumed       = $consumed;
            $newTask->status         = $task->status;
            $newTask->story          = $task->story;
            $newTask->lastEditedBy   = $this->app->user->account;
            $newTask->lastEditedDate = $now;
            if($lastDate <= $effort->date) $newTask->left = $effort->left;
            if(isset($effort->left) and $effort->left == 0 and strpos('done,cancel,closed', $task->status) === false)
            {
                $newTask->status 		 = 'done';
                $newTask->assignedTo     = $task->openedBy;
                $newTask->assignedDate   = $now;
                $newTask->finishedBy     = $this->app->user->account;
                $newTask->finishedDate   = $now;

                $actionID = $this->action->create('task', $effort->objectID, $action == '+' ? 'Finished' : 'DeleteEstimate', $action == '+' ? $effort->work : '');
            }
            elseif(isset($effort->left) and $effort->left != 0 and strpos('done,cancel,closed', $task->status) !== false)
            {
                //对点击已完成工时删除增加提示
                echo js::alert($this->lang->effort->deleteTip);
                $newTask->status         = 'doing';
                $newTask->finishedBy     = '';
                $newTask->canceledBy     = '';
                $newTask->closedBy       = '';
                $newTask->closedReason   = '';
                $newTask->finishedDate   = '0000-00-00';
                $newTask->canceledDate   = '0000-00-00';
                $newTask->closedDate     = '0000-00-00';

                $actionID = $this->action->create('task', $effort->objectID, $action == '+' ? 'Activated' : 'DeleteEstimate', $action == '+' ? $effort->work : '');
            }
            elseif($task->status == 'wait')
            {
                $newTask->status       = 'doing';
                $newTask->assignedTo   = $this->app->user->account;
                $newTask->assignedDate = $now;
                $newTask->realStarted  = date('Y-m-d');

                $actionID = $this->action->create('task', $effort->objectID, $action == '+' ? 'Started' : 'DeleteEstimate', $action == '+' ? $effort->work : '');
            }
            else
            {
                $comment = isset($_POST['work']) ? $this->post->work : '';
                $actionID = $this->action->create('task', $effort->objectID, $action == '+' ? 'EditEstimate' : 'DeleteEstimate', $comment);
            }

            if(!empty($teams))
            {
                foreach($effortGroups as $account => $efforts)
                {
                    $consumed = 0;
                    foreach($efforts as $thisEffort) $consumed += $thisEffort->consumed;
                    $this->dao->update(TABLE_TEAM)->set('consumed')->eq($consumed)->where('type')->eq('task')->andWhere('root')->eq($effort->objectID)->andWhere('account')->eq($account)->exec();
                    $teams[$account]->consumed = $consumed;
                }
                $this->dao->update(TABLE_TEAM)->set('left')->eq($effort->left)->where('type')->eq('task')->andWhere('root')->eq($effort->objectID)->andWhere('account')->eq($effort->account)->exec();
                $teams[$effort->account]->left = $effort->left;
                $newTask = $this->task->computeHours4Multiple($task, $newTask, $teams);
            }

            if(!empty($actionID))
            {
                $this->dao->update(TABLE_ACTION)->set('efforted')->eq('1')->where('id')->eq($actionID)->exec();

                $changes = common::createChanges($task, $newTask);
                if($changes) $this->action->logHistory($actionID, $changes);
            }
            $this->dao->update(TABLE_TASK)->data($newTask)->where('id')->eq($effort->objectID)->exec();

            $this->loadModel('task')->computeConsumed($task->id);
            if($newTask->story) $this->loadModel('story')->setStage($newTask->story);
        }
    }

    /**
     * Create append link
     *
     * @param  string $objectType
     * @param  int    $objectID
     * @access public
     * @return string
     */
    public function createAppendLink($objectType, $objectID)
    {
        if(!common::hasPriv('effort', 'createForObject')) return false;

        /* Determines whether an object is editable. */
        if($objectType == 'case') $objectType = 'testcase';
        $object = $this->loadModel($objectType)->getByID($objectID);
        if(!common::canBeChanged($objectType, $object)) return false;

        return html::a(helper::createLink('effort', 'createForObject', "objectType=$objectType&objectID=$objectID", '', true), "<i class='icon-green-effort-createForObject icon-time'></i> " . $this->lang->effort->common, '', "class='btn effort iframe'");
    }

    /**
     * Get main depts
     *
     * @access public
     * @return array
     */
    public function getMainDepts()
    {
        $depts = array();
        $mainDepts = $this->dao->select('*')->from(TABLE_DEPT)->where('grade')->eq(1)->fetchAll();
        $depts[0]  = $this->lang->effort->allDept;
        foreach($mainDepts as $mainDept) $depts[$mainDept->id] = $mainDept->name;
        return $depts;
    }

    /**
     * Get all depts.
     *
     * @access public
     * @return array
     */
    public function getAllDepts()
    {
        $depts = array();
        $mainDepts = $this->dao->select('*')->from(TABLE_DEPT)->fetchAll('id');
        $depts[0]  = $this->lang->company->allDept;
        foreach($mainDepts as $mainDept)
        {
            if($mainDept->parent)
            {
                $name = '';
                foreach(explode(',', $mainDept->path) as $pathID)
                {
                    if(!empty($pathID)) $name .= $mainDepts[$pathID]->name . ' / ';
                }
                $depts[$mainDept->id] = rtrim($name, ' / ');
            }
            else
            {
                $depts[$mainDept->id] = $mainDept->name;
            }
        }
        return $depts;
    }

    /**
     * Print cell.
     *
     * @param  object $col
     * @param  object $effort
     * @param  string $mode
     * @access public
     * @return void
     */
    public function printCell($col, $effort, $mode = 'datatable')
    {
        $canView  = common::hasPriv('effort', 'view');
        $account  = $this->app->user->account;
        $id       = $col->id;
        if($col->show)
        {
            $class = '';
            $title = '';
            if($id == 'work') $title = " title='{$effort->work}'";
            if($id == 'objectType' and isset($effort->objectTitle)) $title = " title='{$effort->objectTitle}'";

            if($id == 'work' or $id == 'objectType') $class .= ' c-name';

            if($id == 'product')
            {
                static $products;
                if(empty($products)) $products = $this->loadModel('product')->getPairs();

                $effort->productName = '';
                $effortProducts      = explode(',', trim($effort->product, ','));
                foreach($effortProducts as $productID) $effort->productName .= zget($products, $productID, '') . ' ';
                $title = " title='{$effort->productName}'";
            }

            if($id == 'execution')
            {
                static $executions;
                if(empty($executions)) $executions = $this->loadModel('execution')->getPairs($this->session->project);
                $effort->executionName = zget($executions, $effort->execution, '');
                $title = " title='{$effort->executionName}'";
            }
            if($id == 'dept')
            {
                static $depts;
                if(empty($depts)) $depts = $this->loadModel('dept')->getOptionMenu();
                $effort->deptName = zget($depts, $effort->dept, '');
                $title = " title='{$effort->deptName}'";
            }

            echo "<td class='c-{$id}" . $class . "'" . $title . ">";
            switch($id)
            {
            case 'id':
                if($this->app->getModuleName() == 'my')
                {
                    echo html::checkbox('effortIDList', array($effort->id => sprintf('%03d', $effort->id)));
                }
                else
                {
                    printf('%03d', $effort->id);
                }
                break;
            case 'date':
                echo $effort->date;
                break;
            case 'account':
                static $users;
                if(empty($users)) $users = $this->loadModel('user')->getPairs('noletter');
                echo zget($users, $effort->account);
                break;
            case 'dept':
                echo $effort->deptName;
                break;
            case 'work':
                echo $canView ? html::a(helper::createLink('effort', 'view', "id=$effort->id&from=my", '', true), $effort->work, '', "class='iframe'") : $effort->work;
                break;
            case 'consumed':
                echo $effort->consumed;
                break;
            case 'left':
                echo $effort->objectType == 'task' ? $effort->left : '';
                break;
            case 'objectType':
                if($effort->objectType != 'custom')
                {
                    $viewLink = helper::createLink($effort->objectType, 'view', "id=$effort->objectID");
                    $objectTitle = zget($this->lang->effort->objectTypeList, $effort->objectType, strtoupper($effort->objectType)) . " #{$effort->objectID} " . $effort->objectTitle;
                    echo common::hasPriv($effort->objectType, 'view') ? html::a($viewLink, $objectTitle) : $objectTitle;
                }
                break;
            case 'product':
                echo $effort->productName;
                break;
            case 'execution':
                echo $effort->executionName;
                break;
            case 'actions':
                common::printIcon('effort', 'edit',   "id=$effort->id", $effort, 'list', '', '', 'iframe', true);
                common::printIcon('effort', 'delete', "id=$effort->id", $effort, 'list', 'trash', 'hiddenwin');
                break;
            }
            echo '</td>';
        }
    }

    /**
     * Get ranzhi leave users
     *
     * @access public
     * @return array
     */
    public function getRanzhiLeaveUsers()
    {
        if(!extension_loaded('curl')) return false;

        $address   = $this->config->sso->addr;
        $parsedURL = parse_url($address);

        $ranzhiHost   = $parsedURL['scheme'] . "://" . $parsedURL['host'];
        $ranzhiConfig = commonModel::http($ranzhiHost . '/sys/index.php?mode=getconfig');
        $ranzhiConfig = json_decode($ranzhiConfig);

        $zentaoRequestType = $this->config->requestType;
        $zentaoWebRoot     = $this->config->webRoot;

        $this->config->requestType = $ranzhiConfig->requestType;
        $this->config->webRoot     = '/';
        $getLeaverLink  = $ranzhiHost . '/sys' . helper::createLink('sso', 'leaveUsers');
        $getLeaverLink .= strpos($getLeaverLink, '?') !== false ? '&' : '?';
        $getLeaverLink .= "code={$this->config->sso->code}&key={$this->config->sso->key}";

        $this->config->requestType = $zentaoRequestType;
        $this->config->webRoot     = $zentaoWebRoot;

        $leaveUsers = commonModel::http($getLeaverLink);
        return json_decode($leaveUsers, true);
    }

    /**
     * Get effort count.
     *
     * @param  string $account
     * @access public
     * @return int
     */
    public function getCount($account = '')
    {
        if(empty($account)) $account = $this->app->user->account;
        return $this->dao->select('count(*) as count')->from(TABLE_EFFORT)->where('account')->eq($account)->andWhere('deleted')->eq(0)->fetch('count');
    }

    /**
     * Get recently executions
     *
     * @access public
     * @return array
     */
    public function getRecentlyExecutions()
    {
        $executions = $this->dao->select('id, name')->from(TABLE_EXECUTION)
            ->where('deleted')->eq(0)
            ->andWhere('type')->in('stage,sprint')
            ->beginIF(!$this->app->user->admin)->andWhere('id')->in($this->app->user->view->sprints)->fi()
            ->orderBy('id_desc')
            ->limit(20)
            ->fetchPairs();

        return $executions;
    }

    /**
     * Get effort titles.
     *
     * @param  array  $objectIdList
     * @access public
     * @return array
     */
    public function getEffortTitles($objectIdList)
    {
        $this->app->loadConfig('action');
        $todos = array();
        $objectTypeList = array();
        foreach($objectIdList as $objectType => $idList)
        {
            $table = zget($this->config->objectTables, $objectType, '');
            $field = zget($this->config->action->objectNameFields, $objectType, '');
            if($table and $field)
            {
                $objectTypeList[$objectType] = $this->dao->select("id,$field")->from($table)->where('id')->in($idList)->fetchPairs('id', $field);
                if($objectType == 'todo')
                {
                    $todos = $this->dao->select('*')->from(TABLE_TODO)->where('id')->in($idList)->fetchAll('id');
                    $todoLinkedObject = array();
                    foreach($todos as $todo)
                    {
                        if(!empty($todo->idvalue)) $todoLinkedObject[$todo->type][$todo->idvalue] = $todo->idvalue;
                    }
                    if($todoLinkedObject)
                    {
                        foreach($todoLinkedObject as $linkedType => $linkedIdList)
                        {
                            $table = zget($this->config->objectTables, $linkedType, '');
                            $field = zget($this->config->action->objectNameFields, $linkedType, '');
                            if($table and $field)
                            {
                                $linkedObjects = $this->dao->select("id,$field")->from($table)->where('id')->in($linkedIdList)->fetchPairs('id', $field);
                                if(!isset($objectTypeList[$linkedType])) $objectTypeList[$linkedType] = array();
                                $objectTypeList[$linkedType] += $linkedObjects;
                            }
                        }
                    }
                }
            }
        }
        return array($objectTypeList, $todos);
    }

    /**
     * Convert estimate to effort.
     *
     * @access public
     * @return bool
     */
    public function convertEstToEffort()
    {
        $estimates = $this->dao->select('*')->from(TABLE_TASKESTIMATE)->orderBy('id')->fetchAll();
        $depts     = $this->dao->select('account, dept')->from(TABLE_USER)->where('deleted')->eq(0)->fetchPairs();

        $this->loadModel('action');
        foreach($estimates as $estimate)
        {
            $relation = $this->action->getRelatedFields('task', $estimate->task);

            $effort = new stdclass();
            $effort->objectType = 'task';
            $effort->objectID   = $estimate->task;
            $effort->product    = $relation['product'];
            $effort->project    = (int)$relation['project'];
            $effort->execution  = (int)$relation['execution'];
            $effort->account    = $estimate->account;
            $effort->deptID     = $depts[$estimate->account];
            $effort->work       = empty($estimate->work) ? $this->lang->effort->handleTask : $estimate->work;
            $effort->date       = $estimate->date;
            $effort->left       = $estimate->left;
            $effort->consumed   = $estimate->consumed;

            $this->dao->insert(TABLE_EFFORT)->data($effort)->exec();
            $this->dao->delete()->from(TABLE_TASKESTIMATE)->where('id')->eq($estimate->id)->exec();
        }
        return true;
    }

    /**
     * Convert effort to estimate.
     *
     * @access public
     * @return bool
     */
    public function convertEffortToEst()
    {
        $efforts = $this->dao->select('*')->from(TABLE_EFFORT)->where('objectType')->eq('task')->andWhere('deleted')->eq(0)->orderBy('id')->fetchAll();
        foreach($efforts as $effort)
        {
            $estimate = new stdclass();
            $estimate->task     = $effort->objectID;
            $estimate->account  = $effort->account;
            $estimate->date     = $effort->date;
            $estimate->left     = $effort->left;
            $estimate->consumed = $effort->consumed;
            $estimate->work     = $effort->work;

            $this->dao->insert(TABLE_TASKESTIMATE)->data($estimate)->exec();
        }
        return true;
    }
}
