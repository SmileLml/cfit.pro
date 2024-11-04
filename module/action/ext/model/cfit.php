<?php
/**
 * Get histories of an action.
 *
 * @param  int    $actionID
 * @access public
 * @return array
 */
public function getHistoryByActionID($actionID)
{
    return $this->dao->select('*')->from(TABLE_HISTORY)->where('action')->eq($actionID)->fetchAll();
}

/**
 * Trigger to update the field.
 *
 * @param  object $action
 * @param  int    $actionID
 * @access public
 * @return void
 */
public function afterBug($action, $actionID)
{
    if(in_array($action->action, array('opened', 'edited', 'deleted', 'undeleted')))
    {
        $bug      = $this->dao->select('id,`case`,result')->from(TABLE_BUG)->where('id')->eq($action->objectID)->fetch();
        $caseID   = $bug->case;
        $resultID = $bug->result;
        $this->computeLinkedBugs($caseID, $resultID);
        $this->session->set('calllbackActionList', array($actionID => 'afterBugCallback'));
    }
}

/**
 * Trigger to update the field.
 *
 * @param  int    $actionID
 * @access public
 * @return void
 */
public function afterBugCallback($actionID)
{
    $action    = $this->getByID($actionID);
    $histories = $this->getHistoryByActionID($actionID);
    $caseID    = 0;
    $resultID  = 0;
    foreach($histories as $history)
    {
        if($history->field == 'case')   $caseID   = $history->old;
        if($history->field == 'result') $resultID = $history->old;
    }
    $this->computeLinkedBugs($caseID, $resultID);
}

/**
 * Count the number of bugs associated with a case.
 *
 * @param  int    $caseID
 * @param  int    $resultID
 * @access public
 * @return void
 */
public function computeLinkedBugs($caseID, $resultID)
{
    if($caseID)
    {
        $bugs = $this->dao->select('count(*) as count')->from(TABLE_BUG)->where('`case`')->eq($caseID)->andWhere('deleted')->eq('0')->fetch('count');
        $bugs = $bugs ? $bugs : 0;
        $this->dao->update(TABLE_CASE)->set('bugs')->eq($bugs)->where('id')->eq($caseID)->exec();
    }

    $testRun = $this->dao->select('id,task,`case`')->from(TABLE_TESTRUN)->where('id')->eq($resultID)->fetch();
    if(!empty($testRun))
    {
        $taskBugs = $this->dao->select('count(*) as count')->from(TABLE_BUG)->where('result')->eq($resultID)->andWhere('`case`')->eq($caseID)->andWhere('deleted')->eq('0')->fetch('count');
        $taskBugs = $taskBugs ? $taskBugs : 0;
        $this->dao->update(TABLE_TESTRUN)->set('taskBugs')->eq($taskBugs)->where('id')->eq($resultID)->exec();
    }
}

/**
 * Trigger to update the field.
 *
 * @param  object $action
 * @param  int    $actionID
 * @access public
 * @return void
 */
public function afterCase($action)
{
    $objectID = $action->objectID;
    if(in_array($action->action, array('opened', 'edited', 'deleted', 'undeleted', 'linked2testtask', 'unlinkedfromtesttask', 'confirmchange')))
    {
        $case  = $this->dao->select('id,version')->from(TABLE_CASE)->where('id')->eq($objectID)->fetch();
        $steps = $this->dao->select('count(*) as count')->from(TABLE_CASESTEP)->where('`case`')->eq($objectID)->andWhere('version')->eq($case->version)->fetch('count');
        $steps = $steps ? $steps : 0;
        $this->dao->update(TABLE_CASE)->set('stepNumber')->eq($steps)->where('id')->eq($objectID)->exec();

        $testrunList = $this->dao->select('*')->from(TABLE_TESTRUN)->where('`case`')->eq($objectID)->fetchAll();
        foreach($testrunList as $testrun)
        {
            $taskSteps = $this->dao->select('count(distinct t1.id) as count')->from(TABLE_CASESTEP)->alias('t1')
                ->leftJoin(TABLE_TESTRUN)->alias('t2')->on('t1.`case`=t2.`case`')
                ->where('t2.id')->eq($testrun->id)
                ->andWhere('t1.`case`')->eq($testrun->case)
                ->andWhere('t1.type')->ne('group')
                ->andWhere('t1.version=t2.version')
                ->fetch('count');

            $taskSteps = $taskSteps ? $taskSteps : 0;
            $this->dao->update(TABLE_TESTRUN)->set('taskStepNumber')->eq($taskSteps)->where('id')->eq($testrun->id)->exec();
        }
    }

    if($action->action == 'run')
    {
        $executions = $this->dao->select('count(*) as count')->from(TABLE_TESTRESULT)->where('`case`')->eq($objectID)->fetch('count');
        $fails      = $this->dao->select('count(*) as count')->from(TABLE_TESTRESULT)->where('`case`')->eq($objectID)->andWhere('caseResult')->eq('fail')->fetch('count');
        $executions = $executions ? $executions : 0;
        $fails      = $fails ? $fails : 0;
        $this->dao->update(TABLE_CASE)->set('results')->eq($executions)->set('caseFails')->eq($fails)->where('id')->eq($objectID)->exec();

        $testrunList = $this->dao->select('*')->from(TABLE_TESTRUN)->where('`case`')->eq($objectID)->fetchAll();
        foreach($testrunList as $testrun)
        {
            $taskExecutions = $this->dao->select('count(*) as count')->from(TABLE_TESTRESULT)->where('`run`')->eq($testrun->id)->fetch('count');
            $taskFails      = $this->dao->select('count(*) as count')->from(TABLE_TESTRESULT)->where('`run`')->eq($testrun->id)->andWhere('caseResult')->eq('fail')->fetch('count');
            $taskExecutions = $taskExecutions ? $taskExecutions : 0;
            $taskFails      = $taskFails ? $taskFails : 0;
            $this->dao->update(TABLE_TESTRUN)->set('taskResults')->eq($taskExecutions)->set('taskCaseFails')->eq($taskFails)->where('id')->eq($testrun->id)->exec();
        }
    }
}

/**
 * Get actions of an object.
 *
 * @param  int    $objectType
 * @param  int    $objectID
 * @access public
 * @return array
 */
public function getListDesc($objectType, $objectID)
{
    $commiters = $this->loadModel('user')->getCommiters();
    $actions   = $this->dao->select('*')->from(TABLE_ACTION)
        ->beginIF($objectType == 'project')
        ->where("objectType IN('project', 'testtask', 'build')")
        ->andWhere('project')->eq((int)$objectID)
        ->fi()
        ->beginIF($objectType != 'project')
        ->where('objectType')->eq($objectType)
        ->beginIF(is_array($objectID))->andWhere('objectID')->in($objectID)->fi()
        ->beginIF(!is_array($objectID))->andWhere('objectID')->eq((int)$objectID)->fi()
        ->fi()
        ->orderBy('date_desc, id_desc')
        ->fetchAll('id');

    $histories = $this->getHistory(array_keys($actions));
    $this->loadModel('file');

    if($objectType == 'project')
    {
        $this->app->loadLang('build');
        $this->app->loadLang('testtask');
        $actions = $this->processProjectActions($actions);
    }

    foreach($actions as $actionID => $action)
    {
        $actionName = strtolower($action->action);
        if($actionName == 'svncommited' and isset($commiters[$action->actor]))
        {
            $action->actor = $commiters[$action->actor];
        }
        elseif($actionName == 'gitcommited' and isset($commiters[$action->actor]))
        {
            $action->actor = $commiters[$action->actor];
        }
        elseif($actionName == 'linked2execution')
        {
            $name = $this->dao->select('name')->from(TABLE_PROJECT)->where('id')->eq($action->extra)->fetch('name');
            if($name) $action->extra = common::hasPriv('execution', 'view') ? html::a(helper::createLink('execution', 'view', "executionID=$action->execution"), $name) : $name;
        }
        elseif($actionName == 'linked2project')
        {
            $name      = $this->dao->select('name')->from(TABLE_PROJECT)->where('id')->eq($action->extra)->fetch('name');
            $productID = trim($action->product, ',');
            if($name) $action->extra = common::hasPriv('project', 'view') ? html::a(helper::createLink('project', 'view', "projectID=$action->project"), $name) : $name;
        }
        elseif($actionName == 'linked2plan')
        {
            $title = $this->dao->select('title')->from(TABLE_PRODUCTPLAN)->where('id')->eq($action->extra)->fetch('title');
            if($title) $action->extra = common::hasPriv('productplan', 'view') ? html::a(helper::createLink('productplan', 'view', "planID=$action->extra"), $title) : $title;
        }
        elseif($actionName == 'linked2build')
        {
            $name = $this->dao->select('name')->from(TABLE_BUILD)->where('id')->eq($action->extra)->fetch('name');
            if($name) $action->extra = common::hasPriv('build', 'view') ? html::a(helper::createLink('build', 'view', "builID=$action->extra&type={$action->objectType}"), $name) : $name;
        }
        elseif($actionName == 'linked2bug')
        {
            $name = $this->dao->select('name')->from(TABLE_BUILD)->where('id')->eq($action->extra)->fetch('name');
            if($name) $action->extra = common::hasPriv('build', 'view') ? html::a(helper::createLink('build', 'view', "builID=$action->extra&type={$action->objectType}"), $name) : $name;
        }
        elseif($actionName == 'linked2release')
        {
            $name = $this->dao->select('name')->from(TABLE_RELEASE)->where('id')->eq($action->extra)->fetch('name');
            if($name) $action->extra = common::hasPriv('release', 'view') ? html::a(helper::createLink('release', 'view', "releaseID=$action->extra&type={$action->objectType}"), $name) : $name;
        }
        elseif($actionName == 'moved')
        {
            $name = $this->dao->select('name')->from(TABLE_PROJECT)->where('id')->eq($action->extra)->fetch('name');
            if($name) $action->extra = common::hasPriv('project', 'task') ? html::a(helper::createLink('project', 'task', "projectID=$action->extra"), "#$action->extra " . $name) : "#$action->extra " . $name;
        }
        elseif($actionName == 'frombug' and common::hasPriv('bug', 'view'))
        {
            $action->extra = html::a(helper::createLink('bug', 'view', "bugID=$action->extra"), $action->extra);
        }
        elseif($actionName == 'unlinkedfromexecution')
        {
            $name = $this->dao->select('name')->from(TABLE_PROJECT)->where('id')->eq($action->extra)->fetch('name');
            if($name) $action->extra = common::hasPriv('project', 'story') ? html::a(helper::createLink('project', 'story', "projectID=$action->extra"), "#$action->extra " . $name) : "#$action->extra " . $name;
        }
        elseif($actionName == 'unlinkedfromproject')
        {
            $name      = $this->dao->select('name')->from(TABLE_PROJECT)->where('id')->eq($action->extra)->fetch('name');
            $productID = trim($action->product, ',');
            if($name) $action->extra = common::hasPriv('projectstory', 'story') ? html::a(helper::createLink('projectstory', 'story', "projectID=$action->execution&productID=$productID"), "#$action->extra " . $name) : "#$action->extra " . $name;
        }
        elseif($actionName == 'unlinkedfrombuild')
        {
            $name = $this->dao->select('name')->from(TABLE_BUILD)->where('id')->eq($action->extra)->fetch('name');
            if($name) $action->extra = common::hasPriv('build', 'view') ? html::a(helper::createLink('build', 'view', "builID=$action->extra&type={$action->objectType}"), $name) : $name;
        }
        elseif($actionName == 'unlinkedfromrelease')
        {
            $name = $this->dao->select('name')->from(TABLE_RELEASE)->where('id')->eq($action->extra)->fetch('name');
            if($name) $action->extra = common::hasPriv('release', 'view') ? html::a(helper::createLink('release', 'view', "releaseID=$action->extra&type={$action->objectType}"), $name) : $name;
        }
        elseif($actionName == 'unlinkedfromplan')
        {
            $title = $this->dao->select('title')->from(TABLE_PRODUCTPLAN)->where('id')->eq($action->extra)->fetch('title');
            if($title) $action->extra = common::hasPriv('productplan', 'view') ? html::a(helper::createLink('productplan', 'view', "planID=$action->extra"), "#$action->extra " . $title) : "#$action->extra " . $title;
        }
        elseif($actionName == 'tostory')
        {
            $title = $this->dao->select('title')->from(TABLE_STORY)->where('id')->eq($action->extra)->fetch('title');
            if($title) $action->extra = common::hasPriv('story', 'view') ? html::a(helper::createLink('story', 'view', "storyID=$action->extra"), "#$action->extra " . $title) : "#$action->extra " . $title;
        }
        elseif($actionName == 'createchildren')
        {
            $names = $this->dao->select('id,name')->from(TABLE_TASK)->where('id')->in($action->extra)->fetchPairs('id', 'name');
            $action->extra = '';
            if($names)
            {
                foreach($names as $id => $name) $action->extra .= common::hasPriv('task', 'view') ? html::a(helper::createLink('task', 'view', "taskID=$id"), "#$id " . $name) . ', ' : "#$id " . $name . ', ';
            }
            $action->extra = trim(trim($action->extra), ',');
        }
        /* Code for waterfall. */
        elseif($actionName == 'createrequirements')
        {
            $names = $this->dao->select('id,title')->from(TABLE_STORY)->where('id')->in($action->extra)->fetchPairs('id', 'title');
            $action->extra = '';
            if($names)
            {
                foreach($names as $id => $name) $action->extra .= common::hasPriv('story', 'view') ? html::a(helper::createLink('story', 'view', "storyID=$id"), "#$id " . $name) . ', ' : "#$id " . $name . ', ';
            }
            $action->extra = trim(trim($action->extra), ',');
        }
        elseif($actionName == 'totask' or $actionName == 'linkchildtask' or $actionName == 'unlinkchildrentask' or $actionName == 'linkparenttask' or $actionName == 'unlinkparenttask' or $actionName == 'deletechildrentask')
        {
            $name = $this->dao->select('name')->from(TABLE_TASK)->where('id')->eq($action->extra)->fetch('name');
            if($name) $action->extra = common::hasPriv('task', 'view') ? html::a(helper::createLink('task', 'view', "taskID=$action->extra"), "#$action->extra " . $name) : "#$action->extra " . $name;
        }
        elseif($actionName == 'linkchildstory' or $actionName == 'unlinkchildrenstory' or $actionName == 'linkparentstory' or $actionName == 'unlinkparentstory' or $actionName == 'deletechildrenstory')
        {
            $name = $this->dao->select('title')->from(TABLE_STORY)->where('id')->eq($action->extra)->fetch('title');
            if($name) $action->extra = common::hasPriv('story', 'view') ? html::a(helper::createLink('story', 'view', "storyID=$action->extra"), "#$action->extra " . $name) : "#$action->extra " . $name;
        }
        elseif($actionName == 'buildopened')
        {
            $name = $this->dao->select('name')->from(TABLE_BUILD)->where('id')->eq($action->objectID)->fetch('name');
            if($name) $action->extra = common::hasPriv('build', 'view') ? html::a(helper::createLink('build', 'view', "buildID=$action->objectID"), "#$action->objectID " . $name) : "#$action->objectID " . $name;
        }
        elseif($actionName == 'testtaskopened' or $actionName == 'testtaskstarted' or $actionName == 'testtaskclosed')
        {
            $name = $this->dao->select('name')->from(TABLE_TESTTASK)->where('id')->eq($action->objectID)->fetch('name');
            if($name) $action->extra = common::hasPriv('testtask', 'view') ? html::a(helper::createLink('testtask', 'view', "testtaskID=$action->objectID"), "#$action->objectID " . $name) : "#$action->objectID " . $name;
        }
        elseif($actionName == 'fromlib' and $action->objectType == 'case')
        {
            $name = $this->dao->select('name')->from(TABLE_TESTSUITE)->where('id')->eq($action->extra)->fetch('name');
            if($name) $action->extra = common::hasPriv('caselib', 'browse') ? html::a(helper::createLink('caselib', 'browse', "libID=$action->extra"), $name) : $name;
        }
        elseif(($actionName == 'closed' and $action->objectType == 'story') or ($actionName == 'resolved' and $action->objectType == 'bug'))
        {
            $action->appendLink = '';
            if(strpos($action->extra, ':')!== false)
            {
                list($extra, $id) = explode(':', $action->extra);
                $action->extra    = $extra;
                if($id)
                {
                    $table = $action->objectType == 'story' ? TABLE_STORY : TABLE_BUG;
                    $name  = $this->dao->select('title')->from($table)->where('id')->eq($id)->fetch('title');
                    if($name) $action->appendLink = html::a(helper::createLink($action->objectType, 'view', "id=$id"), "#$id " . $name);
                }
            }
        }
        elseif($actionName == 'subdivide' and $objectType == 'opinion')
        {
            $names = $this->dao->select('id,name')->from(TABLE_REQUIREMENT)->where('id')->in($action->extra)->fetchPairs('id', 'name');
            $action->extra = '';
            if($names)
            {
                foreach($names as $id => $name) $action->extra .= common::hasPriv('requirement', 'view') ? html::a(helper::createLink('requirement', 'view', "id=$id"), "#$id " . $name) . ', ' : "#$id " . $name . ', ';
            }
            $action->extra = trim(trim($action->extra), ',');
        }
        elseif($actionName == 'finished' and $objectType == 'todo')
        {
            $action->appendLink = '';
            if(strpos($action->extra, ':')!== false)
            {
                list($extra, $id) = explode(':', $action->extra);
                $action->extra    = strtolower($extra);
                if($id)
                {
                    $table     = $this->config->objectTables[$action->extra];
                    $field     = $this->config->action->objectNameFields[$action->extra];
                    $object    = $this->dao->select($field . ',project')->from($table)->where('id')->eq($id)->fetch();
                    $name      = $object->$field;
                    $projectID = $object->project;
                    if($name) $action->appendLink = html::a(helper::createLink($action->extra, 'view', "id=$id", '', '', $projectID), "#$id " . $name);
                }
            }
        }
        elseif(($actionName == 'opened' or $actionName == 'managed' or $actionName == 'edited') and ($objectType == 'execution' || $objectType == 'project'))
        {
            $this->app->loadLang('execution');
            $linkedProducts = $this->dao->select('id,name')->from(TABLE_PRODUCT)->where('id')->in($action->extra)->fetchPairs('id', 'name');
            $action->extra  = '';
            if($linkedProducts)
            {
                foreach($linkedProducts as $productID => $productName) $linkedProducts[$productID] = html::a(helper::createLink('product', 'browse', "productID=$productID"), "#{$productID} {$productName}");
                $action->extra = sprintf($this->lang->execution->action->extra, '<strong>' . join(', ', $linkedProducts) . '</strong>');
            }
        }
        $action->history = isset($histories[$actionID]) ? $histories[$actionID] : array();

        $actionName = strtolower($action->action);
        if($actionName == 'svncommited')
        {
            foreach($action->history as $history)
            {
                if($history->field == 'subversion') $history->diff = str_replace('+', '%2B', $history->diff);
            }
        }
        elseif($actionName == 'gitcommited')
        {
            foreach($action->history as $history)
            {
                if($history->field == 'git') $history->diff = str_replace('+', '%2B', $history->diff);
            }
        }

        $action->comment = $this->file->setImgSize($action->comment, $this->config->action->commonImgSize);

        $actions[$actionID] = $action;
    }

    return $actions;
}
