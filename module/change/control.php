<?php
class change extends control
{
    /**
     * Project: chengfangjinke
     * Method: browse
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 11:28
     * Desc: This is the code comment. This method is called browse.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $projectID
     * @param string $browseType
     * @param int $param
     * @param string $orderBy
     * @param int $recTotal
     * @param int $recPerPage
     * @param int $pageID
     */
    public function browse($projectID = 0, $browseType = 'all', $param = 0, $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $this->loadModel('project')->setMenu($projectID);

        $browseType = strtolower($browseType);

        /* By search. */
        $depts = $this->loadModel('dept')->getOptionMenu();
        $this->config->change->search['params']['createdDept']['values'] = $depts;

        $queryID = ($browseType == 'bysearch')  ? (int)$param : 0;
        $actionURL = $this->createLink('change', 'browse', "projectID=$projectID&browseType=bySearch&param=myQueryID");
        $this->change->buildSearchForm($queryID, $actionURL);

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $this->view->title      = $this->lang->change->common;
        $this->view->changes    = $this->change->getList($projectID, $browseType, $queryID, $orderBy, $pager);
        $this->view->orderBy    = $orderBy;
        $this->view->param      = $param;
        $this->view->projectID  = $projectID;
        $this->view->pager      = $pager;
        $this->view->apps       = $this->loadModel('application')->getapplicationNameCodePairs();
        $this->view->browseType = $browseType;
        $this->view->depts      = $this->loadModel('dept')->getOptionMenu();
        $this->view->users      = $this->loadModel('user')->getPairs('noletter|noclosed');
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: create
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 11:28
     * Desc: This is the code comment. This method is called create.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $projectID
     */
    public function create($projectID = 0)
    {
        if($_POST)
        {
            $changeID = $this->change->create($projectID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $this->loadModel('action')->create('change', $changeID, 'created', $this->post->comment);
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = inlink('browse', "projectID=$projectID");

            $this->send($response);
        }

        $this->loadModel('project')->setMenu($projectID);

        $this->view->title     = $this->lang->change->create;
        $this->view->apps      = $this->loadModel('application')->getapplicationNameCodePairs();
        $this->view->users     = $this->loadModel('user')->getPairs('noclosed');
        $this->view->problems  = array('' => '') + $this->loadModel('problem')->getPairs('noclosed');
        $this->view->demands   = array('' => '') + $this->loadModel('demand')->getPairs('noclosed');
        $this->view->projects  = array('' => '') + $this->loadModel('project')->getPairs('noclosed');
        $reviewers = $this->change->getReviewers($projectID);
        $projectplan = $this->loadModel('projectplan')->getPlanMainInfoByProjectID($projectID);
        $this->view->projectplantext = (object)[
            'innerprojectname'=>$projectplan->name,
            'projectowner'=>$projectplan->owner,
            'ownerphone'=>$projectplan->phone
        ];
        //审核节点下的审核人列表
        $reviewerAccounts = $this->loadModel('review')->getReviewerAccounts($reviewers);
        $this->view->reviewers = $reviewers;
        $this->view->reviewerAccounts = $reviewerAccounts;

        $this->display();
    }

    /**
     * Edit a change.
     *
     * @param  int $changeID
     * @access public
     * @return void
     */
    public function edit($changeID = 0){
        if($_POST)
        {
            $changes = $this->change->update($changeID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes || $this->post->comment)
            {
                $actionID = $this->loadModel('action')->create('change', $changeID, 'edited', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = inlink('view', "changeID=$changeID");

            $this->send($response);
        }

        $change = $this->loadModel('change')->getByID($changeID);

        $this->loadModel('project')->setMenu($change->project);

        $this->view->title     = $this->lang->change->edit;
        $this->view->users     = $this->loadModel('user')->getPairs('noclosed');
        $this->view->problems  = array('' => '') + $this->loadModel('problem')->getPairs('noclosed');
        $this->view->demands   = array('' => '') + $this->loadModel('demand')->getPairs('noclosed');
        $this->view->projects  = array('' => '') + $this->loadModel('project')->getPairs('noclosed');
        $this->view->apps      = $this->loadModel('application')->getapplicationNameCodePairs();
        $this->view->change    = $change;
        $this->view->reviewers = $this->change->getReviewers($change->project, $change->createdDept, $change);

        //年度计划
        $projectplantextfield = json_decode($change->projectplantext);
        if(!$projectplantextfield){
            $projectplan = $this->loadModel('projectplan')->getPlanMainInfoByProjectID($change->project);
            $this->view->projectplantext = (object)[
                'innerprojectname'=>$projectplan->name,
                'projectowner'=>$projectplan->owner,
                'ownerphone'=>$projectplan->phone
            ];
        }else{
            $this->view->projectplantext = $projectplantextfield;
        }


        //审核节点以及审核节点的审核人
        $nodesReviewers = $this->loadModel('review')->getChangeAllNodeReviewers('change', $changeID, $change->version);
        $nodesReviewers = count($nodesReviewers) ? $nodesReviewers: json_decode($change->reviewer, true);
        $this->view->nodesReviewers = $nodesReviewers;
        $this->view->skipReviewNodes = explode(',', $change->skipReviewNode);
        $categoryList = $this->change->getCategoryList($change->level);
        $this->view->categoryList = $categoryList;
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: view
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 11:29
     * Desc: This is the code comment. This method is called view.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $changeID
     */
    public function view($changeID = 0)
    {
        $this->app->loadLang('release');
        $change = $this->change->getByID($changeID);
        $this->loadModel('project')->setMenu($change->project);

        $this->view->title    = $this->lang->change->view;
        $this->view->users    = $this->loadModel('user')->getPairs('noclosed');
        $this->view->actions  = $this->loadModel('action')->getList('change', $changeID);
        $this->view->problems = array('' => '') + $this->loadModel('problem')->getPairs('noclosed');
        $this->view->demands  = array('' => '') + $this->loadModel('demand')->getPairs('noclosed');
        $this->view->projects = array('' => '') + $this->loadModel('project')->getPairs('noclosed');
        $this->view->change   = $change;
        $this->view->level    = $change->level;
        $this->view->nodes    = $this->loadModel('review')->getNodesGroupByNodeCode('change', $changeID, $change->version);

        $this->view->releases = array('' => '') + $this->loadModel('project')->getReleases($change->project);
        $this->view->apps     = $this->loadModel('application')->getapplicationNameCodePairs();
        $this->view->condition = $this->lang->change->condition;
        //评审归档信息
        $archiveList = $this->loadModel('archive')->getArchiveList('change', $changeID);
        //评审打基线信息
        $baseLineList = $this->change->getBaseLineInfo($change);
        if(!empty($baseLineList)){
            $this->app->loadLang('cm');
            $this->view->baseLineTypelist = $this->lang->cm->typeList;
        }
        $this->view->archiveList = $archiveList;
        $this->view->baseLineList = $baseLineList;

        $this->view->isShangHai   = $this->change->isShangHaiProject($change->project); //是否上海项目
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: review
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 11:29
     * Desc: This is the code comment. This method is called review.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $changeID
     * @param int $version
     * @param int $reviewStage
     */
    public function review($changeID = 0, $version = 1, $reviewStage = 0)
    {
        if($_POST)
        {
            $logChanges = $this->change->review($changeID);
            if(dao::isError()) {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $extra = '';
            $reviewResult = common::getLogChangesSpecialInfo($logChanges, 'reviewResult');
            if($reviewResult == 'reject'){
                $extra = '退回';
            }

            $actionID = $this->loadModel('action')->create('change', $changeID, 'reviewed', $this->post->comment, $extra);
            if($logChanges) {
                $this->action->logHistory($actionID, $logChanges);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }
        $change = $this->loadModel('change')->getByID($changeID);
        $this->app->loadLang('cm');
        $this->view->depts   = $this->loadModel('dept')->getOptionMenu();
        $this->view->title   = $this->lang->change->edit;
        $this->view->users   = $this->loadModel('user')->getPairs('noclosed');


        //检查是否允许审核
        $res = $this->loadModel('change')->checkAllowReview($change, $version, $change->status, $this->app->user->account);
        $this->view->change = $change;
        $this->view->res = $res;
        $this->view->typelist = array(''=>'') + $this->lang->cm->typeList;

        //如果是 产创部和架构部则查询下 有没有经办人和经办人反馈意见。
        if(in_array($change->status,['managersuccess','productmanagersuccess'])){
            $this->view->appointUser = $this->change->getAppointUsers($changeID);
        }else{
            $this->view->appointUser = [];
        }
        //当前操作节点
        $nodeCode = $this->change->getReviewNodeCodeByStatus($change->status);
        $this->view->nodeCode = $nodeCode;
        $this->view->SvnList = $this->loadModel('archive')->getArchiveAllList($change->project, 'change', $changeID);
        $this->display();
    }

    public function appoint($changeID = 0){

        if($this->server->REQUEST_METHOD == 'POST')
        {
            $postAppointUser = $this->change->appoint($changeID);

            if(dao::isError()) {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            //发邮件
            $_SESSION["mailUser_".$changeID] = $postAppointUser;
            $_SESSION["xuanxuanUser_".$changeID] = $postAppointUser;
            $actionID = $this->loadModel('action')->create('change', $changeID, 'appoint',$postAppointUser);


            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';


            $this->send($response);
        }

        $appointUser = $this->change->getAppointUsers($changeID);
        $this->view->appointUser = array_column($appointUser,'reviewer');

        $this->view->change = $this->change->getByID($changeID);
        $this->view->users   = $this->loadModel('user')->getPairs('noclosed');
        $this->display();
    }
    /**
     * Project: chengfangjinke
     * Method: link
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 11:29
     * Desc: This is the code comment. This method is called link.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $changeID
     */
    public function link($changeID = 0)
    {
        if($_POST)
        {
            $this->change->link($changeID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $this->loadModel('action')->create('change', $changeID, 'linkrelease', $this->post->comment);

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $this->view->depts    = $this->loadModel('dept')->getOptionMenu();
        $this->view->title    = $this->lang->change->edit;
        $this->view->users    = $this->loadModel('user')->getPairs('noclosed');
        $this->view->change   = $this->loadModel('change')->getByID($changeID);
        $this->view->releases = array('' => '') + $this->loadModel('project')->getReleases($this->view->change->project);
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: feedback
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 11:29
     * Desc: This is the code comment. This method is called feedback.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $changeID
     */
    public function feedback($changeID)
    {
        if($_POST)
        {
            $changes = $this->change->feedback($changeID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes || $this->post->comment)
            {
                $actionID = $this->loadModel('action')->create('change', $changeID, 'feedbacked', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $this->view->depts   = $this->loadModel('dept')->getOptionMenu();
        $this->view->title   = $this->lang->change->edit;
        $this->view->users   = $this->loadModel('user')->getPairs('noclosed');
        $this->view->change = $this->loadModel('change')->getByID($changeID);
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: close
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 11:29
     * Desc: This is the code comment. This method is called close.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $changeID
     */
    public function close($changeID = 0)
    {
        if($_POST)
        {
            $this->change->close($changeID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($this->post->comment)
            {
                $this->loadModel('action')->create('change', $changeID, 'closed', $this->post->comment);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $this->view->depts   = $this->loadModel('dept')->getOptionMenu();
        $this->view->title   = $this->lang->change->edit;
        $this->view->users   = $this->loadModel('user')->getPairs('noclosed');
        $this->view->change = $this->loadModel('change')->getByID($changeID);
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: run
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 11:29
     * Desc: This is the code comment. This method is called run.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $changeID
     */
    public function run($changeID = 0)
    {
        if($_POST)
        {
            $this->change->run($changeID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $this->loadModel('action')->create('change', $changeID, 'applychange',$this->post->comment);

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $this->view->title  = $this->lang->change->run;
        $this->view->users  = $this->loadModel('user')->getPairs('noclosed');
        $this->view->change = $this->change->getByID($changeID);
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: export
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 11:30
     * Desc: This is the code comment. This method is called export.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param string $orderBy
     * @param string $browseType
     */
    public function export($orderBy = 'id_desc', $browseType = 'all')
    {
        /* format the fields of every change in order to export data. */
        if($_POST)
        {
            $this->loadModel('file');
            $changeLang   = $this->lang->change;
            $changeConfig = $this->config->change;

            /* Create field lists. */
            $fields = $this->post->exportFields ? $this->post->exportFields : explode(',', $changeConfig->list->exportFields);
            foreach($fields as $key => $fieldName)
            {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = isset($changeLang->$fieldName) ? $changeLang->$fieldName : $fieldName;
                unset($fields[$key]);
            }

            /* Get changes. */
            $changes = array();
            if($this->session->changeOnlyCondition)
            {
                $changes = $this->dao->select('*')->from(TABLE_CHANGE)->where($this->session->changeQueryCondition)
                    ->beginIF($this->post->exportType == 'selected')->andWhere('id')->in($this->cookie->checkedItem)->fi()
                    ->orderBy($orderBy)->fetchAll('id');
            }
            else
            {
                $stmt = $this->dbh->query($this->session->changeQueryCondition . ($this->post->exportType == 'selected' ? " AND id IN({$this->cookie->checkedItem})" : '') . " ORDER BY " . strtr($orderBy, '_', ' '));
                while($row = $stmt->fetch()) $changes[$row->id] = $row;
            }


            /* Get users, products and executions. */
            if($changes){
                $users = $this->loadModel('user')->getPairs('noletter');
                $projectIds = array_column($changes, 'project');
                $changeIdList = array_keys($changes);

                $projectList = $this->loadModel('project')->getListByIds($projectIds, 'id,`name`');
                $projectNameList = [];
                if($projectList){
                    $projectNameList = array_column($projectList, 'name', 'id');
                }


                //评审归档信息
                $allArchiveList = $this->loadModel('archive')->getArchiveListByObjectIds('change', $changeIdList);
                $this->app->loadLang('cm');
                $baseLineTypeList = $this->lang->cm->typeList;
                foreach($changes as $change) {
                    $changId = $change->id;
                    $change = $this->loadModel('file')->replaceImgURL($change, 'reason,content,effect');
                    $change->createdBy = zget($users, $change->createdBy);
                    $change->status    = $changeLang->statusList[$change->status];
                    $change->type      = $changeLang->typeList[$change->type];
                    $change->category      = $changeLang->categoryList[$change->category];
                    $change->level     = $changeLang->levelList[$change->level];
                    $change->reason  =  preg_replace("/&[a-zA-Z;]*/", '', strip_tags($change->reason));
                    $change->content =  preg_replace("/&[a-zA-Z;]*/", '', strip_tags($change->content));
                    $change->effect  =  preg_replace("/&[a-zA-Z;]*/", '', strip_tags($change->effect));
                    $change->isInteriorPro = zget($this->lang->change->isInteriorProList, $change->isInteriorPro, '');
                    $change->isMasterPro   = zget($this->lang->change->isMasterProList, $change->isMasterPro, '');
                    $change->project       = zget($projectNameList, $change->project);
                    $change->mailUsers     = zmget($users, $change->mailUsers);
                    $archiveList           = zget($allArchiveList, $changId, []);
                    $baseLineList          = $this->change->getBaseLineInfo($change);
                    $change->archiveInfo   = '';
                    $change->baseLineInfo  = '';
                    if($archiveList){
                        foreach ($archiveList as $val){
                            $change->archiveInfo .= $val->svnUrl ."  {$this->lang->change->archiveSvnVersion}：". $val->svnVersion . "\n";
                        }
                    }
                    if($baseLineList){
                        foreach ($baseLineList as $val){
                            $change->baseLineInfo .= zget($baseLineTypeList, $val->baseLineType) .'：'. $val->baseLinePath . "\n";
                        }
                    }
                }
            }

            $this->post->set('fields', $fields);
            $this->post->set('rows', $changes);
            $this->post->set('kind', 'change');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        $this->view->fileName        = $this->lang->change->exportName;
        $this->view->allExportFields = $this->config->change->list->exportFields;
        $this->view->customExport    = true;
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: delete
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 11:30
     * Desc: This is the code comment. This method is called delete.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $changeID
     */
    public function delete($changeID)
    {
        if(!empty($_POST))
        {
            $oldChange = $this->change->getByID($changeID);
            $this->dao->update(TABLE_CHANGE)->set('status')->eq('deleted')->where('id')->eq($changeID)->exec();
            $this->loadModel('consumed')->record('change', $changeID, 0, $this->app->user->account, $oldChange->status, 'deleted', array(), '');
            $actionID = $this->loadModel('action')->create('change', $changeID, 'deleted', $this->post->comment);
            //删除项目变更相关白名单
            $reason = 1003;
            $this->loadModel('review')->deleteWhiteList($changeID, $reason);

            if(isonlybody()) die(js::closeModal('parent.parent', 'this'));
            die(js::reload('parent'));
        }

        $change = $this->change->getByID($changeID);
        $this->view->actions = $this->loadModel('action')->getList('change', $changeID);
        $this->view->change = $change;
        $this->view->users   = $this->loadModel('user')->getPairs();
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: fix
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 11:30
     * Desc: This is the code comment. This method is called fix.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     */
    public function fix()
    {
        return;

        //创建部门
        $umap = array();

        $users = $this->dao->select('*')->from(TABLE_USER)->fetchAll();
        foreach($users as $user)
        {
            $umap[$user->account] = $user->dept;
        }
        $modifies = $this->dao->select('*')->from(TABLE_MODIFY)->fetchAll();
        foreach($modifies as $m)
        {
            $this->dao->update(TABLE_MODIFY)->set('createdDept')->eq($umap[$m->createdBy])->where('id')->eq($m->id)->exec();
        }
        $infos = $this->dao->select('*')->from(TABLE_INFO)->fetchAll();
        foreach($infos as $m)
        {
            $this->dao->update(TABLE_INFO)->set('createdDept')->eq($umap[$m->createdBy])->where('id')->eq($m->id)->exec();
        }
        $problems = $this->dao->select('*')->from(TABLE_PROBLEM)->fetchAll();
        foreach($problems as $m)
        {
            $this->dao->update(TABLE_PROBLEM)->set('createdDept')->eq($umap[$m->createdBy])->where('id')->eq($m->id)->exec();
        }
        $demands = $this->dao->select('*')->from(TABLE_DEMAND)->fetchAll();
        foreach($demands as $m)
        {
            $this->dao->update(TABLE_DEMAND)->set('createdDept')->eq($umap[$m->createdBy])->where('id')->eq($m->id)->exec();
        }

        echo "<p>创建部门修改完成</p>";

        //添加产品经理节点
        $nodes = $this->dao->select('*')->from(TABLE_REVIEWNODE)->where('objectType')->in('change,info')->orderBy('id')->fetchAll();
        $map = array();
        foreach($nodes as $node)
        {
            if(!isset($map[$node->objectType . '-' . $node->objectID . '-' . $node->version])) $map[$node->objectType . '-' . $node->objectID . '-' . $node->version] = array();
            $map[$node->objectType . '-' . $node->objectID . '-' . $node->version][] = $node;
        }

        foreach($map as $ns)
        {
            $stage = 0;
            $reviewStage = 0;
            $type = $ns[0]->objectType;
            $oid  = $ns[0]->objectID;
            foreach($ns as $key => $n)
            {
                $stage++;
                $this->dao->update(TABLE_REVIEWNODE)->set('stage')->eq($stage)->where('id')->eq($n->id)->exec();
                if($n->status == 'pending') $reviewStage = $stage;

                if($key == 2)
                {
                    $stage++;

                    $data = new stdclass();
                    $data->status = ($n->status == 'wait' or $n->status == 'pending') ? 'wait' : 'ignore';
                    $data->objectType  = $n->objectType;
                    $data->objectID    = $n->objectID;
                    $data->stage       = $stage;
                    $data->createdBy   = $n->createdBy;
                    $data->createdDate = $n->createdDate;
                    $data->version     = $n->version;
                    $this->dao->insert(TABLE_REVIEWNODE)->data($data)->exec();

                    $insertID = $this->dao->lastInsertID();
                    $r = new stdclass();
                    $r->node        = $insertID;
                    $r->reviewer    = '';
                    $r->status      = $data->status;
                    $r->createdBy   = $n->createdBy;
                    $r->createdDate = $n->createdDate;
                    $this->dao->insert(TABLE_REVIEWER)->data($r)->exec();
                }
            }
            if($reviewStage != 0)
            {
                if($type == 'change') $this->dao->update(TABLE_MODIFY)->set('reviewStage')->eq($reviewStage-1)->where('id')->eq($oid)->exec();
                if($type == 'info') $this->dao->update(TABLE_INFO)->set('reviewStage')->eq($reviewStage-1)->where('id')->eq($oid)->exec();
            }
        }
        echo '产品经理节点添加完成';
    }

    /**
     * Project: chengfangjinke
     * Method: exportWord
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 11:30
     * Desc: This is the code comment. This method is called exportWord.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $changeID
     */
    public function exportWord($changeID)
    {
        $change   = $this->change->getById($changeID);
        $users    = $this->loadModel('user')->getPairs();
        $projects = array('' => '') + $this->loadModel('project')->getPairs();

        $this->app->loadClass('phpword', true);
        $phpWord = new PhpOffice\PhpWord\PHPWord();
        $section = $phpWord->addSection();

        $phpWord->addParagraphStyle('pStyle', array('spacing'=>100));
        $phpWord->addTitleStyle(1, array('size' => 15, 'name' => '黑体'), array('space' => array('before' => 100, 'after' => 200), 'align' => 'center'));
        $phpWord->addTitleStyle(2, array('size' => 10, 'name' => '黑体'), array('space' => array('before' => 100, 'after' => 100)));
        $phpWord->addTitleStyle(3, array('size' => 10, 'name' => '黑体'), array('space' => array('before' => 100, 'after' => 100)));

        $phpWord->addParagraphStyle('align_right', array('lineHeight' => "1.2", 'spaceBefore' => 0, 'spaceAfter' => 0, 'align' => 'right'));
        $phpWord->addFontStyle('font_default', array('name'=>'Arial', 'size'=>11, 'color'=>'37363a'));
        $phpWord->addFontStyle('font_bold', array('name'=>'Arial', 'size'=>11, 'color'=>'000000', 'bold'=> true));

        $section->addTitle($this->lang->change->exportTitle, 1);
        $section->addText($this->lang->change->code . ' ' . $change->code, 'font_default', 'align_right');

        $tableStyle = array(
            'unit' => \PhpOffice\PhpWord\Style\Table::WIDTH_PERCENT,
            'width' => 100 * 50,
            'cellMargin' => 50,
            'borderSize' => 10,
            'borderColor' => '000000',
        );
        $cellStyle = array();
        $cellRowSpan     = array('vMerge' => 'restart', 'valign' => 'center');
        $cellRowContinue = array('vMerge' => 'continue');
        $table = $section->addTable($tableStyle);
        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->change->level);
        $table->addCell(1000, $cellStyle)->addText(zget($this->lang->change->levelList, $change->level, ''));
        $table->addCell(1000, $cellStyle)->addText($this->lang->change->type);
        $table->addCell(1000, $cellStyle)->addText(zget($this->lang->change->typeList, $change->type, ''));

        if($change->subCategory){
            $table->addRow();
            $table->addCell(1000, $cellStyle)->addText($this->lang->change->category);
            $table->addCell(1000, $cellStyle)->addText(zmget($this->lang->change->categoryList, $change->category, ''));
            $table->addCell(1000, $cellStyle)->addText($this->lang->change->subCategory);
            $table->addCell(1000, $cellStyle)->addText(zmget($this->lang->change->subCategoryList, $change->subCategory, ''));

            $table->addRow();
            $table->addCell(1000, $cellStyle)->addText($this->lang->change->isInteriorPro);
            $table->addCell(3000, array('gridSpan' => 3))->addText(zget($this->lang->change->isInteriorProList, $change->isInteriorPro, ''));

        }else{
            $table->addRow();
            $table->addCell(1000, $cellStyle)->addText($this->lang->change->category);
            $table->addCell(1000, $cellStyle)->addText(zmget($this->lang->change->categoryList, $change->category, ''));
            $table->addCell(1000, $cellStyle)->addText($this->lang->change->isInteriorPro);
            $table->addCell(1000, $cellStyle)->addText(zget($this->lang->change->isInteriorProList, $change->isInteriorPro, ''));
        }

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->change->isMasterPro);
        $table->addCell(1000, $cellStyle)->addText(zmget($this->lang->change->isMasterProList, $change->isMasterPro, ''));
        $table->addCell(1000, $cellStyle)->addText($this->lang->change->isSlavePro);
        $table->addCell(1000, $cellStyle)->addText(zget($this->lang->change->isSlaveProList, $change->isSlavePro, ''));

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->change->project);
        $table->addCell(1000, $cellStyle)->addText(zget($projects, $change->project, ''));
        $table->addCell(1000, $cellStyle)->addText($this->lang->change->status);
        $table->addCell(1000, $cellStyle)->addText(zget($this->lang->change->statusList, $change->status, ''));

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->change->createdBy);
        $table->addCell(1000, $cellStyle)->addText(zget($users, $change->createdBy, ''));
        $table->addCell(1000, $cellStyle)->addText($this->lang->change->createdDate);
        $table->addCell(1000, $cellStyle)->addText(substr($change->createdDate, 0, 10));

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->change->mailUsers);
        $table->addCell(3000, array('gridSpan' => 3))->addText(zmget($users, $change->mailUsers));

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->change->reason);
        $table->addCell(3000, array('gridSpan' => 3))->addText(strip_tags($change->reason));

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->change->content);
        $table->addCell(3000, array('gridSpan' => 3))->addText(strip_tags($change->content));

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->change->effect);
        $table->addCell(3000, array('gridSpan' => 3))->addText(strip_tags($change->effect));

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->change->result);
        $table->addCell(3000, array('gridSpan' => 3))->addText(strip_tags(isset($change->result) ? $change->result : ''));

        /* Review. */
        $table->addRow();
        $table->addCell(4000, array('gridSpan' => 4))->addText($this->lang->change->reviewComment, 'font_bold', array('align' => 'center'));

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->change->reviewNode);
        $table->addCell(1000, $cellStyle)->addText($this->lang->change->reviewer);
        $table->addCell(1000, $cellStyle)->addText($this->lang->change->reviewResult);
        $table->addCell(1000, $cellStyle)->addText($this->lang->change->reviewComment);

        $nodes = $this->loadModel('review')->getNodes('change', $changeID, $change->version);
        if($nodes){
            foreach($nodes as $key => $node)
            {
                //if($key == 0) continue;
                if($node->status == 'wait' or $node->status == 'ignore') continue;

                $reviewers = [];
                if(is_array($node->reviewers) && !empty($node->reviewers)){
                    $reviewers = array_column($node->reviewers, 'reviewer');
                }
                $countUser = count($reviewers);
                //当前审核节点
                $nodeCode = $node->nodeCode;

                if(in_array($nodeCode, $this->lang->change->needIndependShowUsersNodeCodeList) && ($countUser > 1)){

                    foreach ($node->reviewers as $key => $reviewerInfo){
                        $table->addRow();
                        if($key == 0){
                            $table->addCell(1000, $cellRowSpan)->addText(zget($this->lang->change->reviewNodeCodeDescList, $nodeCode));
                        }else{
                            $table->addCell(null, $cellRowContinue);
                        }
                        $table->addCell(1000, $cellStyle)->addText(zget($users, $reviewerInfo->reviewer));
                        $table->addCell(1000, $cellStyle)->addText(zget($this->lang->change->confirmResultList, $reviewerInfo->status, ''));
                        $table->addCell(1000, $cellStyle)->addText(strip_tags($reviewerInfo->comment));
                    }
                }else{
                    $table->addRow();
                    //所有审核人
                    $reviewerUsers    = getArrayValuesByKeys($users, $reviewers);
                    $reviewerUsersStr = implode(',', $reviewerUsers);
                    $realReviewerInfo = $this->loadModel('review')->getRealReviewerInfo($node->status, $node->reviewers);
                    $realReviewerInfo->reviewerUserName = '';
                    if(isset($realReviewerInfo->reviewer)){
                        $realReviewerInfo->reviewerUserName = '（'.zget($users, $realReviewerInfo->reviewer).'）';
                    }

                    $table->addCell(1000, $cellStyle)->addText(zget($this->lang->change->reviewNodeCodeDescList, $nodeCode));
                    $table->addCell(1000, $cellStyle)->addText($reviewerUsersStr);
                    $table->addCell(1000, $cellStyle)->addText(zget($this->lang->change->confirmResultList, $realReviewerInfo->status, ''). $realReviewerInfo->reviewerUserName);
                    $table->addCell(1000, $cellStyle)->addText(strip_tags($realReviewerInfo->comment));
                }

            }
        }

        /* Consumed. */
        $table->addRow();
        $table->addCell(4000, array('gridSpan' => 4))->addText($this->lang->consumedTitle, 'font_bold', array('align' => 'center'));

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->change->nodeUser);
        //$table->addCell(1000, $cellStyle)->addText($this->lang->change->consumed);
        $table->addCell(1000, $cellStyle)->addText($this->lang->change->before);
        $table->addCell(2000, array('gridSpan' => 2))->addText($this->lang->change->after);

        foreach($change->consumed as $c)
        {
            $table->addRow();
            $table->addCell(1000, $cellStyle)->addText(zget($users, $c->createdBy, ''));
            //$table->addCell(1000, $cellStyle)->addText($c->consumed . '' . $this->lang->hour);
            $table->addCell(1000, $cellStyle)->addText(zget($this->lang->change->statusList, $c->before, '-'));
            $table->addCell(2000, array('gridSpan' => 2))->addText(zget($this->lang->change->statusList, $c->after, '-'));
        }

        $this->loadModel('file')->export2Word($this->lang->change->exportTitle . $change->code, $phpWord);
    }

    /**
     * 撤回操作
     * @param $changeID
     */
    public function recall($changeID)
    {

        if($_POST)
        {
            $logChanges = $this->change->recall($changeID);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $actionID = $this->loadModel('action')->create('change', $changeID, 'Recall', $this->post->comment);
            if($logChanges) {
                $this->action->logHistory($actionID, $logChanges);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }
        $change = $this->loadModel('change')->getByID($changeID);
        $this->view->change = $change;
        $this->display();

    }

    /**
     * 查询历史数据
     * @param $id
     * @return void
     */
    public function showHistoryNodes($id){
        $res = $this->dao->select('version')->from(TABLE_REVIEWNODE)->where('objectType')->eq('change')->andWhere('objectID')->eq($id)->groupby('version')->fetchall();
        $versions = array_column($res,'version');
        foreach ($versions as $version) {
            $data = $this->loadModel('review')->getNodes('change', $id, $version);
            foreach ($data as $k=>$v){
                if ($v->status == 'wait' || !(is_array($v->reviewers) && !empty($v->reviewers))){
                    unset($data[$k]);
                }
            }
            $nodes[$version]['nodes'] = $data;
        }

        foreach ($nodes as $key => $node) {
            $countNodes = 0;
            foreach ($node['nodes'] as $nodeReview){
                $nodeCode = $nodeReview->nodeCode;
                if(in_array($nodeCode, $this->lang->change->needIndependShowUsersNodeCodeList)){
                    $countNodes += $nodeReview->reviewedCount;
                }else{
                    $countNodes++;
                }
            }
            $nodes[$key]['countNodes'] = $countNodes;
        }


        $this->view->historyNodes       = $nodes;
        $this->view->users              = $this->loadModel('user')->getPairs('noletter');
        $change = $this->change->getByID($id);
        $this->view->change   = $change;
        $this->view->level    = $change->level;
        $this->view->nodes    = $this->loadModel('review')->getNodesGroupByNodeCode('change', $id, $change->version);
        $this->display();
    }

    /**
     * 获得项目分类列表
     *
     * @param $level
     */
    public function ajaxGetCategoryList($level, $category = ''){
        $categoryList = $this->change->getCategoryList($level);
        echo html::select('category', $categoryList, $category, "class='form-control chosen' onchange='changeCategory();'");
    }
}
