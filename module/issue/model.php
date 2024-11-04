<?php
/**
 * The model file of issue module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Yong Lei <leiyong@easycorp.ltd>
 * @package     issue
 * @version     $Id$
 * @link        http://www.zentao.net
 */
?>
<?php
class issueModel extends model
{
    const FRAMEWORK_DEPT     = 2;    //架构部 部门id
    /**
     * Create an issue.
     *
     * @param  int $projectID 
     * @access public
     * @return int
     */
    public function create($projectID = 0)
    {
        $now   = helper::now();
        $issue = fixer::input('post')
            ->join('owner', ',')
            ->add('createdBy', $this->app->user->account)
            ->add('createdDate', $now)
            ->add('status', 'unconfirmed')
            ->add('project', $projectID)
            ->remove('labels,files')
            ->addIF($this->post->assignedTo, 'assignedBy', $this->app->user->account)
            ->addIF($this->post->assignedTo, 'assignedDate', $now)
            ->stripTags($this->config->issue->editor->create['id'], $this->config->allowedTags)
            ->get();
        if(!isset($issue->owner) || empty($issue->owner)){
            return dao::$errors['owner'] = $this->lang->issue->ownerEmpty;
        }
        $issue = $this->loadModel('file')->processImgURL($issue, $this->config->issue->editor->create['id'], $this->post->uid);

        $this->dao->insert(TABLE_ISSUE)->data($issue)->batchCheck($this->config->issue->create->requiredFields, 'notempty')->exec();
        $issueID = $this->dao->lastInsertID();
        $this->loadModel('file')->saveUpload('issue', $issueID);
        //加入白名单
        if($issue->assignedTo){
            //检查是否有项目权限
            $res = $this->loadModel('project')->checkOwnProjectPermission($projectID, $issue->assignedTo);
            if(!$res){
                $this->app->loadLang('project');
                $reason = $this->lang->project->whiteReasonIssue;
                $res = $this->loadModel('project')->addProjectWhitelistInfo($projectID,  $issue->assignedTo, $issueID, $reason);
            }
        }
        return $issueID;
    }

    /**
     * Get stakeholder issue list data.
     *
     * @param  string $owner
     * @param  string $activityID
     * @param  object $pager
     * @access public
     * @return object
     */
    public function getStakeholderIssue($owner = '', $activityID = 0, $pager = null)
    {
        $issueList = $this->dao->select('*')->from(TABLE_ISSUE)
            ->where('deleted')->eq('0')
            ->beginIF($owner)->andWhere('owner')->eq($owner)->fi()
            ->beginIF($activityID)->andWhere('activity')->eq($activityID)->fi()
            ->orderBy('id_desc')
            ->page($pager)
            ->fetchAll();

        return $issueList;
    }

    /**
     * Get a issue details.
     *
     * @param  int    $issueID
     * @access public
     * @return object|bool
     */
    public function getByID($issueID)
    {
        $issue = $this->dao->select('*')->from(TABLE_ISSUE)->where('id')->eq($issueID)->fetch();
        if(!$issue) return false;
        $issue = $this->loadModel('file')->replaceImgURL($issue, 'desc,resolutionComment');

        $issue->files = $this->loadModel('file')->getByObject('issue', $issue->id);
        return $issue;
    }

    /**
     * Get issue list data.
     *
     * @param  int       $projectID
     * @param  string    $browseType bySearch|open|assignTo|closed|suspended|canceled
     * @param  int       $queryID
     * @param  string    $orderBy
     * @param  object    $pager
     * @access public
     * @return object
     */
    public function getList($projectID = 0, $browseType = 'all', $queryID = 0, $orderBy = 'id_desc', $pager = null)
    {
        $issueQuery = '';
        if($browseType == 'bysearch')
        {
            $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : '';
            if($query)
            {
                $this->session->set('issueQuery', $query->sql);
                $this->session->set('issueForm', $query->form);
            }
            if($this->session->issueQuery == false) $this->session->set('issueQuery', ' 1=1');
            $issueQuery = $this->session->issueQuery;
        }

        $issueList = $this->dao->select('*')->from(TABLE_ISSUE)
            ->where('deleted')->eq('0')
            ->beginIF($projectID)->andWhere('project')->eq($projectID)->fi()
            ->beginIF($browseType == 'open')->andWhere('status')->eq('active')->fi()
            ->beginIF($browseType == 'assignto')->andWhere('assignedTo')->eq($this->app->user->account)->fi()
            ->beginIF($browseType == 'closed')->andWhere('status')->eq('closed')->fi()
            ->beginIF($browseType == 'suspended')->andWhere('status')->eq('suspended')->fi()
            ->beginIF($browseType == 'canceled')->andWhere('status')->eq('canceled')->fi()
            ->beginIF($browseType == 'bysearch')->andWhere($issueQuery)->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll();

        return $issueList;
    }

    /**
     * Get the issue in the block.
     *
     * @param  int    $projectID
     * @param  string $browseType open|assignto|closed|suspended|canceled
     * @param  int    $limit
     * @param  string $orderBy
     * @access public
     * @return array
     */
    public function getBlockIssues($projectID = 0, $browseType = 'all', $limit = 15, $orderBy = 'id_desc')
    {
        $issueList = $this->dao->select('*')->from(TABLE_ISSUE)
            ->where('deleted')->eq('0')
            ->beginIF($projectID)->andWhere('project')->eq($projectID)->fi()
            ->beginIF($browseType == 'open')->andWhere('status')->eq('active')->fi()
            ->beginIF($browseType == 'assignto')->andWhere('assignedTo')->eq($this->app->user->account)->fi()
            ->beginIF($browseType == 'closed')->andWhere('status')->eq('closed')->fi()
            ->beginIF($browseType == 'suspended')->andWhere('status')->eq('suspended')->fi()
            ->beginIF($browseType == 'canceled')->andWhere('status')->eq('canceled')->fi()
            ->orderBy($orderBy)
            ->limit($limit)
            ->fetchAll();

        return $issueList;
    }

    /**
     * Get user issues.
     *
     * @param  string $browseType open|assignto|closed|suspended|canceled
     * @param  string $account
     * @param  string $orderBy
     * @param  object $pager
     * @access public
     * @return array
     */
    public function getUserIssues($type = 'assignedTo', $account = '', $orderBy = 'id_desc', $pager)
    {
        if(empty($account)) $account = $this->app->user->account;
        $issueList = $this->dao->select('*')->from(TABLE_ISSUE)
            ->where('deleted')->eq('0')
            ->andWhere("($type= '$account' or frameworkUser = '$account')")
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll();

        return $issueList;
    }

    /**
     * Get activity list.
     *
     * @access public
     * @return object
     */
    public function getActivityPairs()
    {
        return $this->dao->select('id,name')->from(TABLE_ACTIVITY)->where('deleted')->eq('0')->orderBy('id_desc')->fetchPairs();
    }

    /**
     * Get issue pairs of a user.
     *
     * @param  string $account
     * @param  int    $limit
     * @param  string $status all|unconfirmed|active|suspended|resolved|closed|canceled
     * @param  array  $skipProjectIDList
     * @access public
     * @return array
     */
    public function getUserIssuePairs($account, $limit = 0, $status = 'all', $skipProjectIDList = array())
    {
        $stmt = $this->dao->select('t1.id, t1.title, t2.name as project')
            ->from(TABLE_ISSUE)->alias('t1')
            ->leftjoin(TABLE_PROJECT)->alias('t2')->on('t1.project = t2.id')
            ->where('t1.assignedTo')->eq($account)
            ->andWhere('t1.deleted')->eq(0)
            ->beginIF($status != 'all')->andWhere('t1.status')->in($status)->fi()
            ->beginIF(!empty($skipProjectIDList))->andWhere('t1.project')->notin($skipProjectIDList)->fi()
            ->beginIF($limit)->limit($limit)->fi()
            ->query();

        $issues = array();
        while($issue = $stmt->fetch())
        {
            $issues[$issue->id] = $issue->project . ' / ' . $issue->title;
        }
        return $issues;
    }

    /**
     * Update an issue.
     *
     * @param  int    $issueID
     * @access public
     * @return array
     */
    public function update($issueID)
    {
        $oldIssue = $this->getByID($issueID);

        $now   = helper::now();
        $issue = fixer::input('post')
            ->join('owner', ',')
            ->add('editedBy', $this->app->user->account)
            ->add('editedDate', $now)
            ->remove('labels,files')
            ->addIF($this->post->assignedTo, 'assignedBy', $this->app->user->account)
            ->addIF($this->post->assignedTo, 'assignedDate', $now)
            ->stripTags($this->config->issue->editor->edit['id'], $this->config->allowedTags)
            ->get();

        if(!isset($issue->owner) || empty($issue->owner)){
            return dao::$errors['owner'] = $this->lang->issue->ownerEmpty;
        }
        $issue = $this->loadModel('file')->processImgURL($issue, $this->config->issue->editor->edit['id'], $this->post->uid);
        $this->dao->update(TABLE_ISSUE)->data($issue)
            ->where('id')->eq($issueID)
            ->batchCheck($this->config->issue->edit->requiredFields, 'notempty')
            ->exec();

        $this->loadModel('file')->saveUpload('issue', $issueID);
        $this->file->updateObjectID($this->post->uid, $issueID, 'issue');
        //加入白名单
        if($issue->assignedTo){
            //检查是否有项目权限
            $res = $this->loadModel('project')->checkOwnProjectPermission($oldIssue->project, $issue->assignedTo);
            if(!$res){
                $this->app->loadLang('project');
                $reason = $this->lang->project->whiteReasonIssue;
                $res = $this->loadModel('project')->addProjectWhitelistInfo($oldIssue->project,  $issue->assignedTo, $issueID, $reason);
            }
        }
        return common::createChanges($oldIssue, $issue);
    }

    /**
     * Update assignor.
     *
     * @param  int    $issueID
     * @access public
     * @return array
     */
    public function assignTo($issueID)
    {
        $oldIssue = $this->getByID($issueID);
        $now   = helper::now();
        $issue = fixer::input('post')
            ->remove('comment')
            ->add('assignedBy', $this->app->user->account)
            ->add('assignedDate', $now)
            ->add('editedBy', $this->app->user->account)
            ->add('editedDate', $now)
            ->join('mailTo',',')
            ->get();

        $this->dao->update(TABLE_ISSUE)->data($issue)->batchCheck($this->config->issue->assignto->requiredFields, 'notempty')->where('id')->eq($issueID)->exec();

        if($issue->assignedTo){
            $assingToInfo = $this->loadModel('user')->getUserInfoListByAccounts($issue->assignedTo);
            //架构部
            if($assingToInfo[$issue->assignedTo]->dept == self::FRAMEWORK_DEPT ){
                $frameworkLeader = array_filter(explode(',',trim($this->lang->issue->assignToList['frameworkLeader'],',')));
                //架构部后台配置人员加入白名单
                if($frameworkLeader){
                    //检查是否有项目权限
                    foreach ($frameworkLeader as $item){
                        $res = $this->loadModel('project')->checkOwnProjectPermission($oldIssue->project, $item);
                        if(!$res){
                            $this->app->loadLang('project');
                            $reason = $this->lang->project->whiteReasonIssue;
                            $res = $this->loadModel('project')->addProjectWhitelistInfo($oldIssue->project,  $item, $issueID, $reason);
                        }
                    }
                }
            }
        }
        //加入白名单
        if($issue->assignedTo){
            //检查是否有项目权限
            $res = $this->loadModel('project')->checkOwnProjectPermission($oldIssue->project, $issue->assignedTo);
            if(!$res){
                $this->app->loadLang('project');
                $reason = $this->lang->project->whiteReasonIssue;
                $res = $this->loadModel('project')->addProjectWhitelistInfo($oldIssue->project,  $issue->assignedTo, $issueID, $reason);
            }
        }
        return common::createChanges($oldIssue, $issue);
    }


    /**
     * Update assignor.
     *
     * @param  int    $issueID
     * @access public
     * @return array
     */
    public function assignedToFrameWork($issueID)
    {
        $oldIssue = $this->getByID($issueID);
        $now   = helper::now();
        $issue = fixer::input('post')
            ->remove('comment')
            ->add('assignedToFrameWorkBy', $this->app->user->account)
            ->add('assignedToFrameWorkDate', $now)
            ->add('editedBy', $this->app->user->account)
            ->add('editedDate', $now)
            ->join('mailTo',',')
            ->get();

        $this->dao->update(TABLE_ISSUE)->data($issue)->batchCheck($this->config->issue->assignedtoframework->requiredFields, 'notempty')->where('id')->eq($issueID)->exec();

        if($issue->frameworkUser){
            //架构部人员加入白名单
            //检查是否有项目权限
            $res = $this->loadModel('project')->checkOwnProjectPermission($oldIssue->project, $issue->frameworkUser);
            if(!$res){
                 $this->app->loadLang('project');
                 $reason = $this->lang->project->whiteReasonIssue;
                 $res = $this->loadModel('project')->addProjectWhitelistInfo($oldIssue->project,  $issue->frameworkUser, $issueID, $reason);
            }
         }
        return common::createChanges($oldIssue, $issue);
    }
    /**
     * Close an issue.
     *
     * @param  int    $issueID
     * @access public
     * @return array
     */
    public function close($issueID)
    {
        $oldIssue = $this->getByID($issueID);
        $issue    = fixer::input('post')
            ->add('closedBy', $this->app->user->account)
            ->add('status', 'closed')
            ->add('assignedTo', 'closed')
            ->add('frameworkUser', 'closed')
            ->add('editedBy', $this->app->user->account)
            ->add('editedDate', helper::now())
            ->get();

        $this->dao->update(TABLE_ISSUE)->data($issue)->where('id')->eq($issueID)->exec();

        return common::createChanges($oldIssue, $issue);
    }

    /**
     * Confirm an issue.
     *
     * @param  int    $issueID
     * @access public
     * @return array
     */
    public function confirm($issueID)
    {
        $oldIssue = $this->getByID($issueID);
        $issue    = fixer::input('post')
            ->add('status', 'confirmed')
            ->add('editedBy', $this->app->user->account)
            ->add('editedDate', helper::now())
            ->stripTags($this->config->issue->editor->confirm['id'], $this->config->allowedTags)
            ->get();

        $issue = $this->loadModel('file')->processImgURL($issue, $this->config->issue->editor->confirm['id'], $this->post->uid);
        $this->dao->update(TABLE_ISSUE)->data($issue)->batchCheck($this->config->issue->confirm->requiredFields, 'notempty')->where('id')->eq($issueID)->exec();

        $this->loadModel('file')->saveUpload('issue', $issueID);
        $this->file->updateObjectID($this->post->uid, $issueID, 'issue');

        return common::createChanges($oldIssue, $issue);
    }

    /**
     * Cancel an issue.
     *
     * @param  int    $issueID
     * @access public
     * @return array
     */
    public function cancel($issueID)
    {
        $oldIssue = $this->getByID($issueID);
        $issue    = fixer::input('post')
            ->add('status', 'canceled')
            ->add('assignedTo', 'closed')
            ->add('frameworkUser', 'closed')
            ->add('editedBy', $this->app->user->account)
            ->add('editedDate', helper::now())
            ->stripTags($this->config->issue->editor->cancel['id'], $this->config->allowedTags)
            ->get();
        $issue = $this->loadModel('file')->processImgURL($issue, $this->config->issue->editor->cancel['id'], $this->post->uid);
        $this->dao->update(TABLE_ISSUE)->data($issue)->where('id')->eq($issueID)->exec();
        $this->loadModel('file')->saveUpload('issue', $issueID);
        $this->file->updateObjectID($this->post->uid, $issueID, 'issue');

        return common::createChanges($oldIssue, $issue);
    }

    /**
     * Activate an issue.
     *
     * @param  int    $issueID
     * @access public
     * @return array
     */
    public function activate($issueID)
    {
        $oldIssue = $this->getByID($issueID);

        $now   = helper::now();
        $issue = fixer::input('post')
            ->remove('comment')
            ->add('status', 'active')
            ->add('activateBy', $this->app->user->account)
            ->add('editedBy', $this->app->user->account)
            ->add('editedDate', $now)
            ->add('assignedBy', $this->app->user->account)
            ->add('assignedDate', $now)
            ->addIF($this->post->assignedTo == '', 'assignedTo', $this->app->user->account)
            ->get();

        $this->dao->update(TABLE_ISSUE)->data($issue)->where('id')->eq($issueID)->exec();
        //加入白名单
        if($issue->assignedTo){
            //检查是否有项目权限
            $res = $this->loadModel('project')->checkOwnProjectPermission($oldIssue->project, $issue->assignedTo);
            if(!$res){
                $this->app->loadLang('project');
                $reason = $this->lang->project->whiteReasonIssue;
                $res = $this->loadModel('project')->addProjectWhitelistInfo($oldIssue->project,  $issue->assignedTo, $issueID, $reason);
            }
        }

        return common::createChanges($oldIssue, $issue);
    }

    /**
     * Batch create issue.
     *
     * @param  int $projectID 
     * @access public
     * @return array
     */
    public function batchCreate($projectID = 0)
    {
        $now  = helper::now();
        $data = fixer::input('post')->get();

        $issues = array();
        foreach($data->dataList as $index => $issue)
        {
            if(!trim($issue['title'])) continue;

            $issue['createdBy']   = $this->app->user->account;
            $issue['createdDate'] = $now;
            $issue['project']     = $projectID;
            $issue['status']      = 'unconfirmed';

            if($issue['assignedTo'])
            {
                $issue['assignedBy']   = $this->app->user->account;
                $issue['assignedDate'] = $now;
            }

            if(empty($issue['title']))    die(js::error(sprintf($this->lang->issue->titleEmpty, $index)));
            if(empty($issue['owner']))    die(js::error(sprintf($this->lang->issue->batchOpOwnerEmpty, $index)));
            if(empty($issue['type']))     die(js::error(sprintf($this->lang->issue->typeEmpty, $index)));
            if(empty($issue['severity'])) die(js::error(sprintf($this->lang->issue->severityEmpty, $index)));
            if(empty($issue['pri'])) die(js::error(sprintf($this->lang->issue->batchOpPriEmpty, $index)));

            $issue['owner'] = implode(',', $issue['owner']);
            $issues[] = $issue;
        }

        $issueIdList = array();
        foreach($issues as $issue)
        {
            $this->dao->insert(TABLE_ISSUE)->data($issue)->batchCheck($this->config->issue->create->requiredFields, 'notempty')->exec();
            $issueID = $this->dao->lastInsertId();
            //加入白名单
            if($issue['assignedTo']){
                //检查是否有项目权限
                $res = $this->loadModel('project')->checkOwnProjectPermission($projectID, $issue['assignedTo']);
                if(!$res){
                    $this->app->loadLang('project');
                    $reason = $this->lang->project->whiteReasonIssue;
                    $res = $this->loadModel('project')->addProjectWhitelistInfo($projectID,  $issue['assignedTo'], $issueID, $reason);
                }
            }
            $issueIdList[] = $issueID;
        }

        return $issueIdList;
    }

    /**
     * Resolve an issue.
     *
     * @param  int    $issueID
     * @param  object $data
     * @access public
     * @return void
     */
    public function resolve($issueID, $data)
    {
        $issue = new stdClass();
        $issue->resolution        = $data->resolution;
        $issue->resolutionComment = isset($data->resolutionComment) ? $data->resolutionComment : '';
        $issue->resolvedBy        = $data->resolvedBy;
        $issue->resolvedDate      = $data->resolvedDate;
        $issue->status            = 'resolved';
        $issue->editedBy          = $this->app->user->account;
        $issue->editedDate        = helper::now();
        $issue = $this->loadModel('file')->processImgURL($issue, $this->config->issue->editor->resolve['id'], $this->post->uid);
        $this->dao->update(TABLE_ISSUE)->data($issue)->batchCheck($this->config->issue->resolve->requiredFields, 'notempty')->where('id')->eq($issueID)->exec();
        $this->loadModel('file')->saveUpload('issue', $issueID);
        $this->file->updateObjectID($this->post->uid, $issueID, 'issue');
    }

    /**
     * Create an task.
     *
     * @access public
     * @return object
     */
    public function createTask()
    {
        $projectID = $this->post->project;
        $tasks     = $this->loadModel('task')->create($projectID);
        if(dao::isError()) return false;

        $task = current($tasks);
        return $task['id'];
    }

    /**
     * Create a story.
     *
     * @access public
     * @return int
     */
    public function createStory()
    {
        $storyResult = $this->loadModel('story')->create();
        if(dao::isError()) return false;
        return $storyResult['id'];
    }

    /**
     * Create a bug.
     *
     * @access public
     * @return int
     */
    public function createBug()
    {
        $bugResult = $this->loadModel('bug')->create();
        if(dao::isError()) return false;
        return $bugResult['id'];
    }

    /**
     * Create a risk.
     *
     * @param  int    $issueID
     * @access public
     * @return int
     */
    public function createRisk($issueID)
    {
        $issue  = $this->getByID($issueID);
        $riskID = $this->loadModel('risk')->create($issue->project);
        if(dao::isError()) return false;
        return $riskID;
    }

   /**
     * Build issue search form.
     *
     * @param  string $actionURL
     * @param  int    $queryID
     * @access public
     * @return void
     */
    public function buildSearchForm($actionURL, $queryID)
    {
        $this->config->issue->search['actionURL'] = $actionURL;
        $this->config->issue->search['queryID']   = $queryID;

        $this->loadModel('search')->setSearchParams($this->config->issue->search);
    }

    /**
     * Adjust the action is clickable.
     *
     * @param  object  $issue
     * @param  string  $action
     *
     * @access public
     * @return bool
     */
    public static function isClickable($issue, $action)
    {
        global $app;
        $action = strtolower($action);
        $owner = $issue->owner;
        $ownerArray = explode(',', $owner);
        $createdBy = $issue->createdBy;
        $assignedTo = $issue->assignedTo;
        $assignedToframeworkUser = $issue->frameworkUser;
        $users = [
            'admin',
            $createdBy,
        ];
        if($assignedTo){
            $users[] = $assignedTo;
        }
        if($assignedToframeworkUser){
            $users[] = $assignedToframeworkUser;
        }
        if($ownerArray){
            $users = array_merge($users, $ownerArray);
        }
        $userCount = $app->user->account;

        if($action == 'confirm')  return (in_array($issue->status, ['unconfirmed', 'active']) && in_array($userCount, $users));
        if($action == 'resolve')  return (($issue->status == 'active' || $issue->status == 'confirmed' || $issue->status == 'unconfirmed') && in_array($userCount, $users));
        if($action == 'close')    return $issue->status != 'closed'  && $issue->status != 'unconfirmed' && $issue->status != 'confirmed' && $issue->status != 'canceled';
        if($action == 'activate') return $issue->status == 'closed' || $issue->status == 'canceled';
        if($action == 'cancel')   return ($issue->status != 'canceled' && $issue->status != 'closed' && $issue->status != 'resolved' && in_array($userCount, $users));
        if($action == 'assignto') return (in_array($issue->status, ['unconfirmed', 'confirmed', 'resolved','active']) && in_array($userCount, $users));
        if($action == 'assignedtoframework') return (in_array($issue->status, ['unconfirmed', 'confirmed', 'resolved','active']) &&  $userCount == $assignedToframeworkUser);
        if($action == 'edit') return ((in_array($issue->status, ['unconfirmed', 'confirmed', 'active']))  && in_array($userCount, $users));
        if($action == 'delete') {
            $isAllowOp =  self::checkIsAllowDelete($issue);
            return $isAllowOp;
        }

        return true;
    }

    /**
     * 查询是否允许删除
     *
     * @param $issue
     * @return bool
     */
    public static function checkIsAllowDelete($issue){
        global $app;
        $isAllowOp = false;
        if(!$issue){
            return $isAllowOp;
        }
        $owner = $issue->owner;
        $ownerArray = explode(',', $owner);
        $createdBy = $issue->createdBy;
        $assignedTo = $issue->assignedTo;
        $users = [
            'admin',
            $createdBy,
        ];
        if($assignedTo){
            $users[] = $assignedTo;
        }
        if($ownerArray){
            $users = array_merge($users, $ownerArray);
        }
        $userCount = $app->user->account;
        if(in_array($issue->status, ['unconfirmed', 'confirmed', 'resolved'])  && in_array($userCount, $users)){
            $isAllowOp = true;
        }
        return $isAllowOp;
    }

    /**
     * 获取指派给下拉框数据
     * @return mixed
     */
    public function getAssignUsers(){
        //指派，架构部可指派所有人；其他只能指派除架构部外的人员
        $deptName = $this->loadModel('dept')->getByID($this->app->user->dept);
        if(isset($deptName->name) && $deptName->name == '平台架构部' || $this->app->user->account == 'admin'){
            /*in_array($this->app->user->account,array_filter(explode(',',trim($this->lang->issue->leaderList['deptLeader'],','))))||
            $this->app->user->account == 'admin'){*/
            $users = $this->loadModel('user')->getPairs('noclosed|nodeleted');
        }else{
            $users = $this->loadModel('user')->getUserExclude('noclosed|nodeleted',self::FRAMEWORK_DEPT);//2 架构部
        }
        return $users;
    }

    /**发送邮件
     * @param $issueID
     * @param $actionID
     */
    public function sendmail($issueID, $actionID)
    {
        $this->loadModel('mail');
        $issue = $this->getByID($issueID);
        $users  = $this->loadModel('user')->getPairs('noletter');


        /* 获取后台通知中配置的邮件发信。*/
        $this->app->loadLang('custommail');
        $mailConf   = isset($this->config->global->setIssueMail) ? $this->config->global->setIssueMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf   = json_decode($mailConf);

        /* 处理邮件发信的标题和日期*/
        $mailTitle = vsprintf($mailConf->mailTitle, $mailConf->variables);

        /* Get action info. */
        $action          = $this->loadModel('action')->getById($actionID);
        $history         = $this->action->getHistory($actionID);
        $action->history = isset($history[$actionID]) ? $history[$actionID] : array();

        /* Get mail content. */
        $oldcwd     = getcwd();
        $modulePath = $this->app->getModulePath($appName = '', 'issue');
        $viewFile   = $modulePath . 'view/sendmail.html.php';
        chdir($modulePath . 'view');

        if(file_exists($modulePath . 'ext/view/sendmail.html.php'))
        {
            $viewFile = $modulePath . 'ext/view/sendmail.html.php';
            chdir($modulePath . 'ext/view');
        }

        ob_start();
        include $viewFile;
        foreach(glob($modulePath . 'ext/view/sendmail.*.html.hook.php') as $hookFile) include $hookFile;
        $mailContent = ob_get_contents();
        ob_end_clean();

        chdir($oldcwd);

        /*处理收件人*/
        $toList = $action->action == 'assigned' ? $issue->assignedTo : $issue->frameworkUser;
        $ccList = $action->action == 'assigned' ? $issue->mailTo.','.$issue->frameworkUser : '';
        //指派给是架构部  当前操作人不是架构部 给配置的架构部人员发邮件
        /*$assingToInfo = $this->loadModel('user')->getUserInfoListByAccounts($issue->assignedTo);
        if($assingToInfo[$issue->assignedTo]->dept == self::FRAMEWORK_DEPT && $this->app->user->dept != self::FRAMEWORK_DEPT){
            $frameworkLeader = trim($this->lang->issue->assignToList['frameworkLeader'],',');
            $ccList .= $frameworkLeader;
        }*/
        $ccList = trim($ccList,',');
        /* 处理邮件标题*/
        $subject = $mailTitle;
        /* Send emails. */
        $this->mail->send($toList, $subject, $mailContent, $ccList);

        if($this->mail->isError()) error_log(join("\n", $this->mail->getError()));
    }

    /**
     * 架构部指派
     * @param $risk
     * @param $users
     */
    public function printFrameworkHtml($issue, $users)
    {
        $btnTextClass   = '';
        $frameworkUserText = zget($users, $issue->frameworkUser);

        if(empty($issue->frameworkUser))
        {
            $btnTextClass   = 'text-primary';
            $frameworkUserText = $this->lang->issue->noAssigned;
        }
        if($issue->frameworkUser == $this->app->user->account) $btnTextClass = 'text-red';

        $btnClass     = $issue->frameworkUser == 'closed' ? ' disabled' : '';
        $btnClass     = "iframe btn btn-icon-left btn-sm {$btnClass}";
        $assignToLink = helper::createLink('issue', 'assignedToFrameWork', "issueID=$issue->id", '', true);
        $assignToHtml = empty($issue->frameworkUser) ? "<span>{$frameworkUserText}</span>" : html::a($assignToLink, "<i class='icon icon-hand-right'></i> <span title='" . zget($users, $issue->frameworkUser) . "' class='{$btnTextClass}'>{$frameworkUserText}</span>", '', "class='$btnClass'");
        echo common::hasPriv('issue', 'assignedToFrameWork', $issue) && $this->app->user->account == $issue->frameworkUser ?   $assignToHtml :"<span style='padding-left: 21px' class='{$btnTextClass}'>{$frameworkUserText}</span>";
    }
}
