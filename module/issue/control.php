<?php
/**
 * The control file of issue module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Yong Lei <leiyong@easycorp.ltd>
 * @package     issue
 * @version     $Id$
 * @link        http://www.zentao.net
 */
class issue extends control
{
    const FRAMEWORK_DEPT     = 2;    //架构部 部门id
    /**
     * Get issue list data.
     *
     * @param  int    $projectID
     * @param  string $browseType bySearch|open|assignTo|closed|suspended|canceled
     * @param  int    $param
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function browse($projectID = 0, $browseType = 'all', $param = 0, $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $this->loadModel('project')->setMenu($projectID);
        $uri = $this->app->getURI(true);
        $this->session->set('issueList', $uri, 'project');

        /* Load pager */
        $this->app->loadClass('pager', true);
        $pager = pager::init($recTotal, $recPerPage, $pageID);

        /* Build the search form. */
        $browseType = strtolower($browseType);
        $queryID    = ($browseType == 'bysearch') ? (int)$param : 0;
        $actionURL  = $this->createLink('issue', 'browse', "projectID=$projectID&browseType=bysearch&queryID=myQueryID");
        $this->issue->buildSearchForm($actionURL, $queryID);

        $this->view->title      = $this->lang->issue->common . $this->lang->colon . $this->lang->issue->browse;
        $this->view->position[] = $this->lang->issue->browse;

        $this->view->pager      = $pager;
        $this->view->param      = $param;
        $this->view->orderBy    = $orderBy;
        $this->view->browseType = $browseType;
        $this->view->projectID  = $projectID;
        $this->view->issueList  = $this->issue->getList($projectID, $browseType, $queryID, $orderBy, $pager);
        $this->view->users      = $this->loadModel('user')->getPairs('noletter|pofirst|nodeleted');

        $this->display();
    }

    /**
     * Create an issue.
     *
     * @param  int    $projectID
     * @param  string $from  issue|stakeholder
     * @param  string $owner
     * @access public
     * @return void
     */
    public function create($projectID = 0, $from = 'issue', $owner = '')
    {
        $this->loadModel('project')->setMenu($projectID);

        if($_POST)
        {
            $issueID = $this->issue->create($projectID);
            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));
            $this->loadModel('action')->create('issue', $issueID, 'Opened');
            $link = inLink('browse', "projectID=$projectID&browseType=all");
            if($from == 'stakeholder')
            {
                $stakeholderID = $this->dao->select('id')->from(TABLE_STAKEHOLDER)
                    ->where('objectType')->eq('project')
                    ->andwhere('objectID')->eq($projectID)
                    ->andwhere('user')->eq($owner)
                    ->fetch('id');

                $link = $this->createLink('stakeholder', 'userIssue', "userID=$stakeholderID");
            }
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $link));
        }

        $this->view->title      = $this->lang->issue->common . $this->lang->colon . $this->lang->issue->create;
        $this->view->position[] = $this->lang->issue->common;
        $this->view->position[] = $this->lang->issue->create;

        $this->view->users     = $this->loadModel('user')->getPairs('noclosed|nodeleted');
        $this->view->owners    = $this->loadModel('stakeholder')->getStakeholders4Issue();
        $this->view->projectID = $projectID;
        $this->view->from      = $from;
        $this->view->owner     = $owner;
        $this->view->assignUsers     = $this->issue->getAssignUsers();

        $this->display();
    }

    /**
     * Batch create issues.
     *
     * @param  int    $projectID
     * @access public
     * @return void
     */
    public function batchCreate($projectID)
    {
        $this->loadModel('project')->setMenu($projectID);

        if($_POST)
        {
            $issues = $this->issue->batchCreate($projectID);
            foreach($issues as $issue) $this->loadModel('action')->create('issue', $issue, 'Opened');

            die(js::locate($this->inLink('browse', "projectID=$projectID"), 'parent'));
        }

        $this->view->title      = $this->lang->issue->common . $this->lang->colon . $this->lang->issue->batchCreate;
        $this->view->position[] = $this->lang->issue->common;
        $this->view->position[] = $this->lang->issue->batchCreate;

        $this->view->projectID = $projectID;
        $this->view->users     = $this->loadModel('user')->getPairs('noclosed|nodeleted');
        $this->view->assignUsers     = $this->issue->getAssignUsers();
        $this->display();
    }

    /**
     * Delete an issue.
     *
     * @param  int    $issueID
     * @param  string $confirm yes|no
     * @access public
     * @return void
     */
    public function delete($issueID = 0, $confirm = 'no')
    {
        if($confirm == 'no')
        {
            die(js::confirm($this->lang->issue->confirmDelete, inLink('delete', "issueID=$issueID&confirm=yes")));
        }
        else
        {
            $projectID = $this->dao->select('project')->from(TABLE_ISSUE)->where('id')->eq($issueID)->fetch('project');
            $this->issue->delete(TABLE_ISSUE, $issueID);
            die(js::locate(inLink('browse', "projectID=$projectID"), 'parent'));
        }
    }

    /**
     * Edit an issue.
     *
     * @param  int    $issueID
     * @access public
     * @return void
     */
    public function edit($issueID)
    {
        $issue = $this->issue->getByID($issueID);
        $this->loadModel('project')->setMenu($issue->project);

        if($_POST)
        {
            $changes = $this->issue->update($issueID);
            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $actionID = $this->loadModel('action')->create('issue', $issueID, 'Edited');
            $this->action->logHistory($actionID, $changes);
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => inLink('view', "issueID=$issueID")));
        }

        $this->view->title      = $this->lang->issue->common . $this->lang->colon . $this->lang->issue->edit;
        $this->view->position[] = $this->lang->issue->common;
        $this->view->position[] = $this->lang->issue->edit;

        $this->view->users = $this->loadModel('user')->getPairs('noclosed|nodeleted');
        $this->view->issue = $issue;

        $this->display();
    }

    /**
     * Assign an issue.
     *
     * @param  int    $issueID
     * @access public
     * @return void
     */
    public function assignTo($issueID)
    {
        if($_POST)
        {
            $changes = $this->issue->assignTo($issueID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $actionID = $this->loadModel('action')->create('issue', $issueID, 'Assigned', $this->post->comment, $this->post->assignedTo);

            $this->action->logHistory($actionID, $changes);
           // die(js::closeModal('parent.parent', 'this'));
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $response['closeModal'] = true;

            $this->send($response);
        }

        $this->view->issue = $this->issue->getByID($issueID);
        $this->view->users     = $this->loadModel('user')->getPairs('noclosed|nodeleted');
        $this->view->assignUsers     = $this->issue->getAssignUsers();
        $this->view->frameworkUsers  = $this->lang->issue->frameworkToList['frameworkPerson'] ? array('' => '') + $this->loadModel('user')->getUserListByAccounts(explode(',',$this->lang->issue->frameworkToList['frameworkPerson'])) :'';

        $this->display();
    }

    /**
     * Assign an issue.
     *
     * @param  int    $issueID
     * @access public
     * @return void
     */
    public function assignedToFrameWork($issueID)
    {
        if($_POST)
        {
            $changes = $this->issue->assignedToFrameWork($issueID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $actionID = $this->loadModel('action')->create('issue', $issueID, 'assignedtoframeworked', $this->post->comment, $this->post->frameworkUser);

            $this->action->logHistory($actionID, $changes);
            // die(js::closeModal('parent.parent', 'this'));
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $response['closeModal'] = true;

            $this->send($response);
        }

        $this->view->issue = $this->issue->getByID($issueID);
        $this->view->users     = $this->loadModel('user')->getPairs('noclosed|nodeleted');
        $this->view->frameworkUsers  = array('' => '') + $this->loadModel('user')->getUsersNameByDept(self::FRAMEWORK_DEPT); //只查架构部人员

        $this->display();
    }

    /**
     * Close an issue.
     *
     * @param  int    $issueID
     * @access public
     * @return void
     */
    public function close($issueID)
    {
        if($_POST)
        {
            $changes = $this->issue->close($issueID);

            if(dao::isError()) die(js::error(dao::getError()));
            $actionID = $this->loadModel('action')->create('issue', $issueID, 'Closed');

            $this->action->logHistory($actionID, $changes);
            die(js::closeModal('parent.parent', 'this'));
        }

        $this->view->issue = $this->issue->getByID($issueID);
        $this->display();
    }

    /**
     * Confirm the issue.
     *
     * @param  int    $issueID
     * @access public
     * @return void
     */
    public function confirm($issueID)
    {
        if($_POST)
        {
            $changes = $this->issue->confirm($issueID);

            if(dao::isError()) die(js::error(dao::getError()));
            $actionID = $this->loadModel('action')->create('issue', $issueID, 'issueConfirmed');

            $this->action->logHistory($actionID, $changes);
            die(js::closeModal('parent.parent', 'this'));
        }

        $this->view->issue = $this->issue->getByID($issueID);
        $this->display();
    }

    /**
     * Cancel an issue.
     *
     * @param  int    $issueID
     * @access public
     * @return void
     */
    public function cancel($issueID)
    {
        if($_POST)
        {
            $changes = $this->issue->cancel($issueID);

            if(dao::isError()) die(js::error(dao::getError()));
            $actionID = $this->loadModel('action')->create('issue', $issueID, 'Canceled');

            $this->action->logHistory($actionID, $changes);
            die(js::closeModal('parent.parent', 'this'));
        }

        $this->view->issue = $this->issue->getByID($issueID);

        $this->display();
    }

    /**
     * Activate an issue.
     *
     * @param  int    $issueID
     * @access public
     * @return void
     */
    public function activate($issueID)
    {
        if($_POST)
        {
            $changes = $this->issue->activate($issueID);

            if(dao::isError()) die(js::error(dao::getError()));
            $actionID = $this->loadModel('action')->create('issue', $issueID, 'Activated', $this->post->comment, $this->post->assignedTo);

            $this->action->logHistory($actionID, $changes);
            die(js::closeModal('parent.parent', 'this'));
        }

        $this->view->issue = $this->issue->getByID($issueID);
        $this->view->users = $this->loadModel('user')->getPairs('noclosed|nodeleted');
        $this->view->assignUsers     = $this->issue->getAssignUsers();
        $this->display();
    }

    /**
     * Resolve an issue.
     *
     * @param  int    $issueID
     * @access public
     * @return void
     */
    public function resolve($issueID)
    {
        $issue = $this->issue->getByID($issueID);

        if($_POST)
        {
            $data = fixer::input('post')
                ->stripTags($this->config->issue->editor->resolve['id'], $this->config->allowedTags)
                ->get();
            $resolution = $data->resolution;
            unset($_POST['resolution'], $_POST['resolvedBy'], $_POST['resolvedDate']);

            $objectID = '';
            /*
            if($resolution == 'totask')
            {
                $objectID = $this->issue->createTask($issueID);
                if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));

                $objectLink = html::a($this->createLink('task', 'view', "id=$objectID"), $this->post->name);
                $comment    = sprintf($this->lang->issue->logComments[$resolution], $objectLink, "data-toggle='modal'");

                $this->loadModel('action')->create('task', $objectID, 'Opened', '');
                $this->loadModel('action')->create('issue', $issueID, 'Resolved', $comment);
            }

            if($resolution == 'tostory')
            {
                unset($_POST['project']);
                $objectID   = $this->issue->createStory($issueID);
                if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));
                $objectLink = html::a($this->createLink('story', 'view', "id=$objectID"), $this->post->title, "data-toggle='modal'");
                $comment    = sprintf($this->lang->issue->logComments[$resolution], $objectLink);

                $this->loadModel('action')->create('story', $objectID, 'Opened', '');
                $this->loadModel('action')->create('issue', $issueID, 'Resolved', $comment);
            }

            if($resolution == 'tobug')
            {
                $objectID   = $this->issue->createBug($issueID);
                if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));
                $objectLink = html::a($this->createLink('bug', 'view', "id=$objectID"), $this->post->title, "data-toggle='modal'");
                $comment    = sprintf($this->lang->issue->logComments[$resolution], $objectLink);

                $this->loadModel('action')->create('bug', $objectID, 'Opened', '');
                $this->loadModel('action')->create('issue', $issueID, 'Resolved', $comment);
            }

            if($resolution == 'torisk')
            {
                $objectID   = $this->issue->createRisk($issueID);
                if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));
                $objectLink = html::a($this->createLink('risk', 'view', "id=$objectID"), $this->post->name, '', "class='iframe'");
                $comment    = sprintf($this->lang->issue->logComments[$resolution], $objectLink);

                $this->loadModel('action')->create('risk', $objectID, 'Opened', '');
                $this->loadModel('action')->create('issue', $issueID, 'Resolved', $comment);
            }
            */

            $this->issue->resolve($issueID, $data);
            if(dao::isError()) {
                if(isonlybody()){
                    $response['result']  = 'fail';
                    $response['message'] = dao::getError();
                    $this->send($response);
                    $this->send($response);
                }
                die(js::error(dao::getError()));
            }
            //if($resolution == 'resolved')
            $this->loadModel('action')->create('issue', $issueID, 'Resolved');
            $this->dao->update(TABLE_ISSUE)->set('objectID')->eq($objectID)->where('id')->eq($issueID)->exec();

            if(isonlybody()) $this->send(array('locate' => 'parent', 'message' => $this->lang->saveSuccess, 'result' => 'success'));
            die(js::locate(inLink('browse', "projectID=$issue->project"), 'parent'));
        }

        $this->view->title = $this->lang->issue->resolve;
        $this->view->issue = $issue;
        $this->view->users = $this->loadModel('user')->getPairs('noclosed|nodeleted');

        $this->display();
    }

    /**
     * Get different types of resolution forms.
     *
     * @access public
     * @return void
     */
    public function ajaxGetResolveForm()
    {
        $data  = fixer::input('post')->get();
        $issue = $this->issue->getByID($data->issueID);
        $users = $this->loadModel('user')->getPairs('noclosed|nodeleted');

        $task = new stdClass();
        $task->module     = 0;
        $task->assignedTo = '';
        $task->name       = $issue->title;
        $task->type       = '';
        $task->estimate   = '';
        $task->desc       = $issue->desc;
        $task->estStarted = '';
        $task->deadline   = '';

        $this->view->resolution = $data->mode;
        $this->view->issue      = $issue;
        $this->view->users      = $users;
        $this->view->task       = $task;

        if(in_array($data->mode, array('tostory', 'tobug', 'totask')))
        {
            $this->loadModel('task');
            $this->loadModel('tree');
            $this->loadModel('project');

            $projectID  = $this->session->project;
            $projects   = $this->loadModel('product')->getProjectPairsByProduct($this->session->product);
            $executions = $this->loadModel('product')->getExecutionPairsByProduct($this->session->product, 0, 'id_desc', $projectID);

            $moduleOptionMenu = array('' => '/');
            if($data->mode == 'totask') $moduleOptionMenu = $this->tree->getOptionMenu($projectID, 'task');

            $this->view->moduleOptionMenu = $moduleOptionMenu;
            $this->view->showAllModule    = 'allModule';
            $this->view->projects         = $projects;
            $this->view->executions       = $executions;
            $this->view->projectID        = $projectID;
            $this->view->moduleID         = 0;
            $this->view->branch           = 0;
        }

        if(in_array($data->mode, array('tostory', 'tobug')))
        {
            $products  = $this->loadModel('product')->getProductPairsByProject($this->session->project);
            $productID = $this->session->product;
            $productID = isset($products[$productID]) ? $productID : key($products);
            $branches  = $this->loadModel('branch')->getPairs($productID, 'noempty');

            $module = $data->mode == 'tostory' ? 'story' : 'bug';
            $moduleOptionMenu = $this->tree->getOptionMenu($productID, $module);

            $this->view->moduleOptionMenu = $moduleOptionMenu;
            $this->view->branches         = $branches;
            $this->view->products         = $products;
            $this->view->productID        = $productID;
        }

        switch($data->mode)
        {
            case 'totask':
                $this->loadModel('story');

                $this->view->project    = $this->project->getById($projectID);
                $this->view->members    = $this->loadModel('user')->getTeamMemberPairs($projectID, 'project', 'nodeleted');
                $this->view->stories    = $this->story->getExecutionStoryPairs($projectID, 0, 0);
                $this->view->showFields = $this->config->task->custom->createFields;

                $this->display('issue', 'taskform');
                break;
            case 'tobug':
                $this->loadModel('bug');
                $this->view->builds     = $this->loadModel('build')->getProductBuildPairs($productID, '', 'noempty,noterminate,nodone');
                $this->view->buildID    = 0;
                $this->view->showFields = $this->config->bug->custom->createFields;

                $this->display('issue', 'bugform');
                break;
            case 'tostory':
                $this->loadModel('story');
                $this->view->plans      = $this->loadModel('productplan')->getPairsForStory($productID, key($branches), true);
                $this->view->showFields = $this->config->story->custom->createFields;
                $this->display('issue', 'storyform');
                break;
            case 'torisk':
                $this->app->loadLang('risk');
                $this->display('issue', 'riskform');
                break;
            case 'resolved':
                $this->display('issue', 'resolveform');
                break;
            default:
                $this->display('issue', 'resolveerrorform');
                break;
        }
    }

    /**
     * AJAX: return issues of a user in html select.
     *
     * @param  int    $userID
     * @param  string $id
     * @param  string $status
     * @access public
     * @return void
     */
    public function ajaxGetUserIssues($userID = '', $id = '', $status = 'all')
    {
        if($userID == '') $userID = $this->app->user->id;
        $user    = $this->loadModel('user')->getById($userID, 'id');
        $account = $user->account;

        $issues = $this->issue->getUserIssuePairs($account, 0, $status);

        if($id) die(html::select("issues[$id]", $issues, '', 'class="form-control"'));
        die(html::select('issue', $issues, '', 'class=form-control'));
    }

    /**
     *  View an issue.
     *
     * @param  int    $issueID
     * @access public
     * @return void
     */
    public function view($issueID)
    {
        /* Set actions and get issue by id. */
        $issueID = (int)$issueID;
        $issue   = $this->issue->getByID($issueID);
        if(!$issue) die(js::error($this->lang->notFound) . js::locate('back'));
        $this->loadModel('project')->setMenu($issue->project);

        $this->session->project = $issue->project;
        $this->commonAction($issueID, 'issue');

        $this->view->title      = $this->lang->issue->common . $this->lang->colon . $issue->title;
        $this->view->position[] = $this->lang->issue->common;
        $this->view->position[] = $this->lang->issue->basicInfo;

        $this->view->users = $this->loadModel('user')->getPairs('noletter|pofirst|nodeleted');
        $this->view->issue = $issue;
        $this->view->progressInfo = $this->loadModel('action')->getActionInfoList('issue', $issueID, 'assigned');
        $this->display();
    }

    /**
     * Common actions of issue module.
     *
     * @param  int    $issueID
     * @param  int    $object
     * @access public
     * @return void
     */
    public function commonAction($issueID, $object)
    {
        $this->view->actions = $this->loadModel('action')->getList($object, $issueID);
    }
}
