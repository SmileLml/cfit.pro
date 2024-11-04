<?php
/**
 * The model file of risk module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2020 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Yuchun Li <liyuchun@cnezsoft.com>
 * @package     risk
 * @version     $Id: model.php 5079 2020-09-04 09:08:34Z lyc $
 * @link        http://www.zentao.net
 */
?>
<?php
class riskModel extends model
{
    const FRAMEWORK_DEPT     = 2;    //架构部 部门id
    /**
     * Create a risk.
     *
     * @param  int  $projectID 
     * @access public
     * @return int|bool
     */
    public function create($projectID = 0)
    {
        $risk = fixer::input('post')
            ->add('project', $projectID)
            ->add('createdBy', $this->app->user->account)
            ->add('createdDate', helper::now())
            ->stripTags($this->config->risk->editor->create['id'], $this->config->allowedTags)
            ->remove('uid')
            ->get();

        $risk = $this->loadModel('file')->processImgURL($risk, $this->config->risk->editor->create['id'], $this->post->uid);
        $this->dao->insert(TABLE_RISK)->data($risk)->autoCheck()->batchCheck($this->config->risk->create->requiredFields, 'notempty')->exec();
        $riskID = $this->dao->lastInsertId();
        $this->file->updateObjectID($this->post->uid, $riskID, 'risk');
        $this->file->saveUpload('risk', $riskID);
        if(!dao::isError()) return $riskID;
        return false;
    }

    /**
     * Batch create risk.
     *
     * @param  int  $projectID 
     * @access public
     * @return bool
     */
    public function batchCreate($projectID = 0)
    {
        $data = fixer::input('post')->get();

        $this->loadModel('action');
        foreach($data->name as $i => $name)
        {
            if(!$name) continue;

            $risk = new stdclass();
            $risk->name        = $name;
            $risk->source      = $data->source[$i];
            $risk->category    = $data->category[$i];
            $risk->strategy    = $data->strategy[$i];
            $risk->project     = $projectID;
            $risk->createdBy   = $this->app->user->account;
            $risk->createdDate = helper::today();

            $this->dao->insert(TABLE_RISK)->data($risk)->autoCheck()->exec();

            $riskID = $this->dao->lastInsertID();
            $this->action->create('risk', $riskID, 'Opened');
        }

        return true;
    }

    /**
     * Update a risk.
     *
     * @param  int    $riskID
     * @access public
     * @return array|bool
     */
    public function update($riskID)
    {
        $oldRisk = $this->getByID($riskID);

        $risk = fixer::input('post')
            ->add('editedBy', $this->app->user->account)
            ->add('editedDate', helper::now())
            ->stripTags($this->config->risk->editor->edit['id'], $this->config->allowedTags)
            ->remove('uid')
            ->get();

        $risk = $this->loadModel('file')->processImgURL($risk, $this->config->risk->editor->edit['id'], $this->post->uid);
        $this->dao->update(TABLE_RISK)->data($risk)->autoCheck()->batchCheck($this->config->risk->edit->requiredFields, 'notempty')->where('id')->eq((int)$riskID)->exec();

        $this->file->updateObjectID($this->post->uid, $riskID, 'risk');
        $this->file->saveUpload('risk', $riskID);
        if(!dao::isError()) return common::createChanges($oldRisk, $risk);
        return false;
    }

    /**
     * Track a risk.
     *
     * @param  int    $riskID
     * @access public
     * @return array|bool
     */
    public function track($riskID)
    {
        $oldRisk = $this->dao->select('*')->from(TABLE_RISK)->where('id')->eq((int)$riskID)->fetch();

        $risk = fixer::input('post')
            ->add('editedBy', $this->app->user->account)
            ->add('editedDate', helper::today())
            ->stripTags($this->config->risk->editor->track['id'], $this->config->allowedTags)
            ->remove('isChange,comment,uid,files,label')
            ->get();

        $this->dao->update(TABLE_RISK)->data($risk)->autoCheck()->where('id')->eq((int)$riskID)->exec();

        if(!dao::isError()) return common::createChanges($oldRisk, $risk);
        return false;
    }

    /**
     * Get risks List.
     *
     * @param  int    $projectID
     * @param  string $browseType
     * @param  string $param
     * @param  string $orderBy
     * @param  int    $pager
     * @access public
     * @return object
     */
    public function getList($projectID, $browseType = '', $param = '', $orderBy = 'id_desc', $pager = null)
    {
        if($browseType == 'bysearch') return $this->getBySearch($projectID, $param, $orderBy, $pager);

        return $this->dao->select('*')->from(TABLE_RISK)
            ->where('deleted')->eq(0)
            ->beginIF($browseType != 'all' and $browseType != 'assignTo')->andWhere('status')->eq($browseType)->fi()
            ->beginIF($browseType == 'assignTo')->andWhere('assignedTo')->eq($this->app->user->account)->fi()
            ->andWhere('project')->eq($projectID)
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');
    }

    /**
     * Get risks by search
     *
     * @param  int    $projectID
     * @param  string $queryID
     * @param  string $orderBy
     * @param  int    $pager
     * @access public
     * @return object
     */
    public function getBySearch($projectID, $queryID = '', $orderBy = 'id_desc', $pager = null)
    {
        if($queryID && $queryID != 'myQueryID')
        {
            $query = $this->loadModel('search')->getQuery($queryID);
            if($query)
            {
                $this->session->set('riskQuery', $query->sql);
                $this->session->set('riskForm', $query->form);
            }
            else
            {
                $this->session->set('riskQuery', ' 1 = 1');
            }
        }
        else
        {
            if($this->session->riskQuery == false) $this->session->set('riskQuery', ' 1 = 1');
        }

        $riskQuery = $this->session->riskQuery;

        return $this->dao->select('*')->from(TABLE_RISK)
            ->where($riskQuery)
            ->andWhere('deleted')->eq('0')
            ->andWhere('project')->eq($projectID)
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');
    }

    /**
     * Get risks of pairs
     *
     * @param  int    $projectID
     * @access public
     * @return object
     */
    public function getPairs($projectID)
    {
        return $this->dao->select('id, name')->from(TABLE_RISK)
            ->where('deleted')->eq(0)
            ->andWhere('project')->eq($projectID)
            ->fetchPairs();
    }

    /**
     * Get risk by ID
     *
     * @param  int    $riskID
     * @access public
     * @return object
     */
    public function getByID($riskID)
    {
        $risk = $this->dao->select('*')->from(TABLE_RISK)->where('id')->eq((int)$riskID)->fetch();
        $risk = $this->loadModel('file')->replaceImgURL($risk, $this->config->risk->editor->edit['id']);
        $files = $this->loadModel('file')->getByObject('risk', $riskID);
        $risk->files    = $files;
        return  $risk;
    }

    /**
     * Get block risks
     *
     * @param  int    $projectID
     * @param  string $browseType
     * @param  int    $limit
     * @param  string $orderBy
     * @access public
     * @return object
     */
    public function getBlockRisks($projectID, $browseType = 'all', $limit = 15, $orderBy = 'id_desc')
    {
        return $this->dao->select('*')->from(TABLE_RISK)
            ->where('project')->eq($projectID)
            ->beginIF($browseType != 'all' and $browseType != 'assignTo')->andWhere('status')->eq($browseType)->fi()
            ->beginIF($browseType == 'assignTo')->andWhere('assignedTo')->eq($this->app->user->account)->fi()
            ->andWhere('deleted')->eq('0')
            ->orderBy($orderBy)
            ->limit($limit)
            ->fetchAll();
    }

    /**
     * Get user risks.
     *
     * @param  string $type    open|assignto|closed|suspended|canceled
     * @param  string $account
     * @param  string $orderBy
     * @param  object $pager
     * @access public
     * @return object
     */
    public function getUserRisks($type = 'assignedTo', $account = '', $orderBy = 'id_desc', $pager)
    {
        if(empty($account)) $account = $this->app->user->account;

        $riskList = $this->dao->select('*')->from(TABLE_RISK)
            ->where('deleted')->eq('0')
            ->andWhere("($type= '$account' or frameworkUser = '$account')")
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll();

        return $riskList;
    }

    /**
     * Get risk pairs of a user.
     *
     * @param  string $account
     * @param  int    $limit
     * @param  string $status all|active|closed|hangup|canceled
     * @param  array  $skipProjectIDList
     * @access public
     * @return array
     */
    public function getUserRiskPairs($account, $limit = 0, $status = 'all', $skipProjectIDList = array())
    {
        $stmt = $this->dao->select('t1.id, t1.name, t2.name as project')
            ->from(TABLE_RISK)->alias('t1')
            ->leftjoin(TABLE_PROJECT)->alias('t2')->on('t1.project = t2.id')
            ->where('t1.assignedTo')->eq($account)
            ->andWhere('t1.deleted')->eq(0)
            ->beginIF($status != 'all')->andWhere('t1.status')->in($status)->fi()
            ->beginIF(!empty($skipProjectIDList))->andWhere('t1.project')->notin($skipProjectIDList)->fi()
            ->beginIF($limit)->limit($limit)->fi()
            ->query();

        $risks = array();
        while($risk = $stmt->fetch())
        {
            $risks[$risk->id] = $risk->project . ' / ' . $risk->name;
        }
        return $risks;
    }

    /**
     * Print assignedTo html
     *
     * @param  int    $risk
     * @param  int    $users
     * @access public
     * @return string
     */
    public function printAssignedHtml($risk, $users)
    {
        $btnTextClass   = '';
        $assignedToText = zget($users, $risk->assignedTo);

        if(empty($risk->assignedTo))
        {
            $btnTextClass   = 'text-primary';
            $assignedToText = $this->lang->risk->noAssigned;
        }
        if($risk->assignedTo == $this->app->user->account) $btnTextClass = 'text-red';

        $btnClass     = $risk->assignedTo == 'closed' ? ' disabled' : '';
        $btnClass     = "iframe btn btn-icon-left btn-sm {$btnClass}";
        $assignToLink = helper::createLink('risk', 'assignTo', "riskID=$risk->id", '', true);
        $assignToHtml = html::a($assignToLink, "<i class='icon icon-hand-right'></i> <span title='" . zget($users, $risk->assignedTo) . "' class='{$btnTextClass}'>{$assignedToText}</span>", '', "class='$btnClass'");

        echo !common::hasPriv('risk', 'assignTo', $risk) ? "<span style='padding-left: 21px' class='{$btnTextClass}'>{$assignedToText}</span>" : $assignToHtml;
    }

    /**
     * Assign a risk.
     *
     * @param  int    $riskID
     * @access public
     * @return array|bool
     */
    public function assign($riskID)
    {
        $this->app->loadLang('issue');
        $oldRisk = $this->getByID($riskID);

        $risk = fixer::input('post')
            ->add('editedBy', $this->app->user->account)
            ->add('editedDate', helper::today())
            ->setDefault('assignedDate', helper::today())
            ->join('mailTo',',')
            ->stripTags($this->config->risk->editor->assignto['id'], $this->config->allowedTags)
            ->remove('uid,comment,files,label')
            ->get();

        $this->dao->update(TABLE_RISK)->data($risk)->autoCheck()->batchCheck($this->config->risk->assignto->requiredFields, 'notempty')->where('id')->eq((int)$riskID)->exec();

        if($risk->assignedTo){
            $assingToInfo = $this->loadModel('user')->getUserInfoListByAccounts($risk->assignedTo);
            //架构部
            if($assingToInfo[$risk->assignedTo]->dept == self::FRAMEWORK_DEPT ){
                $frameworkLeader = array_filter(explode(',',trim($this->lang->issue->assignToList['frameworkLeader'],',')));
                //架构部后台配置人员加入白名单
                if($frameworkLeader){
                    //检查是否有项目权限
                    foreach ($frameworkLeader as $item){
                        $res = $this->loadModel('project')->checkOwnProjectPermission($oldRisk->project, $item);
                        if(!$res){
                            $this->app->loadLang('project');
                            $reason = $this->lang->project->whiteReasonRisk;
                            $res = $this->loadModel('project')->addProjectWhitelistInfo($oldRisk->project,  $item, $riskID, $reason);
                        }
                    }
                }
            }
        }
        //加入白名单
        if($risk->assignedTo){
            //检查是否有项目权限
            $res = $this->loadModel('project')->checkOwnProjectPermission($oldRisk->project, $oldRisk->assignedTo);
            if(!$res){
                $this->app->loadLang('project');
                $reason = $this->lang->project->whiteReasonRisk;
                $res = $this->loadModel('project')->addProjectWhitelistInfo($oldRisk->project,  $oldRisk->assignedTo, $riskID, $reason);
            }
        }

        if(!dao::isError()) return common::createChanges($oldRisk, $risk);
        return false;
    }

    /**
     * Update assignor.
     *
     * @param  int    $riskID
     * @access public
     * @return array
     */
    public function assignedToFrameWork($riskID)
    {
        $oldRisk = $this->getByID($riskID);
        $now   = helper::now();
        $risk = fixer::input('post')
            ->remove('comment')
            ->add('assignedToFrameWorkBy', $this->app->user->account)
            ->add('assignedToFrameWorkDate', $now)
            ->add('editedBy', $this->app->user->account)
            ->add('editedDate', $now)
            ->join('mailTo',',')
            ->get();

        $this->dao->update(TABLE_RISK)->data($risk)->batchCheck($this->config->risk->assignedtoframework->requiredFields, 'notempty')->where('id')->eq($riskID)->exec();

        if($risk->frameworkUser){
            //架构部人员加入白名单
            //检查是否有项目权限
            $res = $this->loadModel('project')->checkOwnProjectPermission($oldRisk->project, $risk->frameworkUser);
            if(!$res){
                $this->app->loadLang('project');
                $reason = $this->lang->project->whiteReasonIssue;
                $res = $this->loadModel('project')->addProjectWhitelistInfo($oldRisk->project,  $risk->frameworkUser, $riskID, $reason);
            }
        }
        return common::createChanges($oldRisk, $risk);
    }
    /**
     * Cancel a risk.
     *
     * @param  int    $riskID
     * @access public
     * @return array|bool
     */
    public function cancel($riskID)
    {
        $oldRisk = $this->getByID($riskID);

        $risk = fixer::input('post')
            ->setDefault('status','canceled')
            ->add('editedBy', $this->app->user->account)
            ->add('editedDate', helper::today())
            ->stripTags($this->config->risk->editor->cancel['id'], $this->config->allowedTags)
            ->remove('uid,comment')
            ->get();

        $this->dao->update(TABLE_RISK)->data($risk)->autoCheck()->where('id')->eq((int)$riskID)->exec();

        if(!dao::isError()) return common::createChanges($oldRisk, $risk);
        return false;
    }

    /**
     * Close a risk.
     *
     * @param  int    $riskID
     * @access public
     * @return array|bool
     */
    public function close($riskID)
    {
        $oldRisk = $this->getByID($riskID);

        $risk = fixer::input('post')
            ->setDefault('status','closed')
            ->add('editedBy', $this->app->user->account)
            ->add('editedDate', helper::today())
            ->add('closedBy', $this->app->user->account)
            ->add('closedDate', helper::today())
            ->add('assignedTo', 'closed')
            ->add('frameworkUser', 'closed')
            ->stripTags($this->config->risk->editor->close['id'], $this->config->allowedTags)
            ->remove('uid,comment')
            ->get();

        $this->dao->update(TABLE_RISK)->data($risk)->autoCheck()->where('id')->eq((int)$riskID)->exec();

        if(!dao::isError()) return common::createChanges($oldRisk, $risk);
        return false;
    }

    /**
     * Hangup a risk.
     *
     * @param  int    $riskID
     * @access public
     * @return array|bool
     */
    public function hangup($riskID)
    {
        $oldRisk = $this->getByID($riskID);

        $risk = fixer::input('post')
            ->setDefault('status','hangup')
            ->add('editedBy', $this->app->user->account)
            ->add('editedDate', helper::today())
            ->get();

        $this->dao->update(TABLE_RISK)->data($risk)->autoCheck()->where('id')->eq((int)$riskID)->exec();

        if(!dao::isError()) return common::createChanges($oldRisk, $risk);
        return false;
    }

    /**
     * Activate a risk.
     *
     * @param  int    $riskID
     * @access public
     * @return array|bool
     */
    public function activate($riskID)
    {
        $oldRisk = $this->getByID($riskID);

        $risk = fixer::input('post')
            ->setDefault('status','active')
            ->add('activateBy', $this->app->user->account)
            ->add('activateDate',$_POST['activateDate'] ? $_POST['activateDate'] :  helper::today())
            ->get();

        $this->dao->update(TABLE_RISK)->data($risk)->autoCheck()->where('id')->eq((int)$riskID)->exec();

        if(!dao::isError()) return common::createChanges($oldRisk, $risk);
        return false;
    }

    /**
     * Adjust the action is clickable.
     *
     * @param  int    $risk
     * @param  int    $action
     * @static
     * @access public
     * @return bool
     */
    public static function isClickable($risk, $action)
    {
        global $app;
        $action = strtolower($action);

        if($action == 'cancel' or $action == 'close') return $risk->status != 'canceled' and $risk->status != 'closed';
        if($action == 'hangup')   return $risk->status == 'active';
        if($action == 'activate') return $risk->status != 'active';
        if($action == 'edit') return $risk->status != 'closed';
        if($action == 'track') return $risk->status != 'closed';
        if($action == 'assignedtoframework') return (!in_array($risk->status, ['closed', 'canceled']) && $app->user->account == $risk->frameworkUser);
        return true;
    }

    /**
     * Build search form.
     *
     * @param  int    $queryID
     * @param  string $actionURL
     * @access public
     * @return void
     */
    public function buildSearchForm($queryID, $actionURL)
    {
        $this->config->risk->search['actionURL'] = $actionURL;
        $this->config->risk->search['queryID']   = $queryID;

        $this->loadModel('search')->setSearchParams($this->config->risk->search);
    }

    /**发送邮件
     * @param $riskID
     * @param $actionID
     */
    public function sendmail($riskID, $actionID)
    {
        $this->loadModel('mail');
        $risk = $this->getByID($riskID);
        $users  = $this->loadModel('user')->getPairs('noletter');

        $this->app->loadLang('issue');
        /* 获取后台通知中配置的邮件发信。*/
        $this->app->loadLang('custommail');
        $mailConf   = isset($this->config->global->setRiskMail) ? $this->config->global->setRiskMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf   = json_decode($mailConf);

        /* 处理邮件发信的标题和日期*/
        $mailTitle = vsprintf($mailConf->mailTitle, $mailConf->variables);

        /* Get action info. */
        $action          = $this->loadModel('action')->getById($actionID);
        $history         = $this->action->getHistory($actionID);
        $action->history = isset($history[$actionID]) ? $history[$actionID] : array();

        /* Get mail content. */
        $oldcwd     = getcwd();
        $modulePath = $this->app->getModulePath($appName = '', 'risk');
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
        $toList = $action->action == 'assigned' ? $risk->assignedTo : $risk->frameworkUser;
        $ccList = $action->action == 'assigned' ? $risk->mailTo.','.$risk->frameworkUser : '';
        //指派给是架构部  当前操作人不是架构部 给配置的架构部人员发邮件
       /* $assingToInfo = $this->loadModel('user')->getUserInfoListByAccounts($risk->assignedTo);
        if($assingToInfo[$risk->assignedTo]->dept == self::FRAMEWORK_DEPT && $this->app->user->dept != self::FRAMEWORK_DEPT){
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
    public function printFrameworkHtml($risk, $users)
    {
        $btnTextClass   = '';
        $frameworkUserText = zget($users, $risk->frameworkUser);

        if(empty($risk->frameworkUser))
        {
            $btnTextClass   = 'text-primary';
            $frameworkUserText = $this->lang->risk->noAssigned;
        }
        if($risk->frameworkUser == $this->app->user->account) $btnTextClass = 'text-red';

        $btnClass     = $risk->frameworkUser == 'closed' ? ' disabled' : '';
        $btnClass     = "iframe btn btn-icon-left btn-sm {$btnClass}";
        $assignToLink = helper::createLink('risk', 'assignedToFrameWork', "riskID=$risk->id", '', true);
        $assignToHtml = empty($risk->frameworkUser) ? "<span>{$frameworkUserText}</span>" : html::a($assignToLink, "<i class='icon icon-hand-right'></i> <span title='" . zget($users, $risk->frameworkUser) . "' class='{$btnTextClass}'>{$frameworkUserText}</span>", '', "class='$btnClass'");
        echo common::hasPriv('risk', 'assignedToFrameWork', $risk) && $this->app->user->account == $risk->frameworkUser ?  $assignToHtml : "<span style='padding-left: 21px' class='{$btnTextClass}'>{$frameworkUserText}</span>";
    }
}
