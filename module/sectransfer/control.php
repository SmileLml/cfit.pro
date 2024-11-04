<?php
class sectransfer extends control
{
    public function __construct()
    {
        parent::__construct();
        // 上海分公司审核节点名称修改
        if (in_array($this->app->getMethodName(),['create','copy'])){
            $this->sectransfer->resetNodeAndReviewerName();
        }
    }

    /**
     * Get list of sectransfers.
     *
     * @param string $browseType
     * @param string $orderBy
     * @param int $recTotal
     * @param int $recPerPage
     * @param int $pageID
     * @access public
     * @return void
     */
    public function browse($browseType = 'all', $param = 0, $orderBy = "id_desc", $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $browseType = strtolower($browseType);
        $this->loadModel('datatable');

        /* By search. */
        $queryID = ($browseType == 'bysearch') ? (int)$param : 0;
        $params = "&browseType=bySearch&param=myQueryID&orderBy=$orderBy";
        $actionURL = $this->createLink('sectransfer', 'browse', $params);
        $this->sectransfer->buildSearchForm($queryID, $actionURL);

        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $sectransfers = $this->sectransfer->getList($browseType, $queryID, $orderBy, $pager);

        $this->view->title              = $this->lang->sectransfer->common;
        $this->view->pager              = $pager;
        $this->view->users              = $this->loadModel('user')->getPairs('noclosed|noletter');
        $this->view->depts              = $this->loadModel('dept')->getDeptPairs();
        $this->view->orderBy            = $orderBy;
        $this->view->browseType         = $browseType;
        $this->view->status             = $browseType;
        $this->view->sectransfers       = $sectransfers;
        $this->session->set('common_back_url', $this->app->getURI(true),'backlog');
        $this->session->set('sectransferList', $this->app->getURI(true), 'backlog');

        $this->display();
    }

    /**
     * @param int $secondorderID
     * @access public
     * @return void
     */
    public function create($secondorderID = 0, $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 10, $pageID = 1)
    {
        $this->loadModel('application');
        $this->app->loadLang('opinion');
        $params = "&browseType=all&param=0&orderBy=$orderBy" . "&recTotal=$recTotal" . "&recPerPage=$recPerPage" . "&pageID=$pageID";
        if ($_POST) {
            $transferID = $this->sectransfer->create();
            if (dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $this->loadModel('action')->create('sectransfer', $transferID, 'created', $this->post->comment);
            if(isonlybody()) $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'parent'));
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $this->createLink('sectransfer', 'browse', $params)));
        }

        // 当从二线工单跳转来时
        if(!empty($secondorderID)){
            // 查询二线工单数据
            $secondorder  = $this->loadModel('secondorder')->getByID($secondorderID);

            // 查询对应承建单位
            $department   = $secondorder->app ? $this->ajaxGetDepartment($secondorder->app, 'selected') : '';
            $this->view->secondorder    = $secondorder;
            $this->view->department     = $department;
        }

        // 查询分管领导和总经理
        $deptLeader  = $this->dao->select('manager1,leader1')->from(TABLE_DEPT)->where('id')->eq($this->app->user->dept)->fetch();

        // 查询对应领导
        $reviewers            = $this->loadModel('modify')->getReviewers();
        $users['CM']  = $reviewers[0];
        $users['own'] = $reviewers[2];
        $users['leader'] = $reviewers[5];
        $users['maxleader'] = $reviewers[6];
        $users['sec'] = $reviewers[7];

        // 返回数据
        $this->view->apps           = array('' => '') + $this->loadModel('application')->getapplicationNameCodePairs();
        $this->view->inprojects     = array('' => '') + $this->sectransfer->getInprojects();
        $this->view->plans          = array('' => '') + $this->loadModel('outsideplan')->getPairs();
        $this->view->title          = $this->lang->sectransfer->create;
        $this->view->secondorderID  = $secondorderID;
        $this->view->secondorders   = array('0' => '') + $this->sectransfer->getSecondorderPairs();
        $this->view->users          = $users;
        $this->view->deptManager    = $deptLeader->manager1;
        $this->view->deptLeader     = $deptLeader->leader1;
        $this->display();
    }



    //处理
    public function deal($transferID = 0, $confirm = 'no')
    {
        $transferInfo = $this->sectransfer->getByID($transferID);
        $users = $this->loadModel('user')->getPairs('noletter|noclosed');
        $this->view->sectransfer   = $transferInfo;
        $this->view->users         = $users;
        if($transferInfo->status == $this->lang->sectransfer->statusList['centerReject']){//总中心退回
            if ($_POST) {
                $changes = $this->sectransfer->deal($transferID);
                if ($changes) {
                    $actionID = $this->loadModel('action')->create('sectransfer', $transferID, 'dealed',$this->post->comment);
                    $this->action->logHistory($actionID, $changes);
                }

                if (dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));
                //更新工单是否最终对外移交
                $nowData = $this->sectransfer->getByID($transferID);
                if($nowData->secondorderId){
                    $this->sectransfer->updateSecondOrderFinallyHand($nowData->secondorderId,$transferID,$nowData);
                }
                $result['result']  = 'success';
                $result['message'] = $this->lang->saveSuccess;
                $result['locate']  = 'parent';
                $this->send($result);
            }else{
                $this->display();
            }
        }else{
            if($confirm == 'no')
            {
                echo js::confirm($this->lang->sectransfer->confirmDeal, $this->createLink('sectransfer', 'deal', "transferID=$transferID&confirm=yes"), '');
                exit;
            }
            else
            {
                if('qszzx' == $transferInfo->department && 1 == $transferInfo->jftype){
                    $planInfo = $this->dao->select('*')->from(TABLE_OUTSIDEPLAN)->where('id')->eq($transferInfo->outproject)->fetch();
                    if(empty($planInfo->code)){
                        $this->send(['result' => 'fail', 'message' => [''=>$this->lang->sectransfer->objectEmptyError]]);
                    }
                }
                $this->checkSecondOrderHandOver($transferInfo->secondorderId, $transferID);
                if (dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

                $status = $this->lang->sectransfer->statusList['waitCMApprove'];
                $requiredReviewNode = $transferInfo->jftype == 1 ? $this->lang->sectransfer->xmRequiredNodes : $this->lang->sectransfer->gdRequiredNodes;
                $version = $transferInfo->version;

                /*if($transferInfo->status == $this->lang->sectransfer->statusList['approveReject']){
                    // 改旧版本节点状态
                    $this->loadModel('review')->check('sectransfer', $transferID, $transferInfo->version, 'reject', '');

                    // 生成新版本节点
                    $nodes = [$transferInfo->CM,$transferInfo->own,$transferInfo->leader,$transferInfo->maxleader,$transferInfo->sec];
                    $version = $version + 1;
                    $this->sectransfer->submitReviewsectransfer($transferID, $nodes, $transferInfo->jftype, $version);
                }*/

                // 修改移交单状态和待处理人
                $this->dao->update(TABLE_SECTRANSFER)
                    ->set('status')->eq($status)
                    ->set('approver')->eq($transferInfo->CM)
                    ->set('version')->eq($version)
                    ->set('reviewStage')->eq('0')
                    ->set('submitBy')->eq($this->app->user->account)
                    ->set('submitDate')->eq(helper::now())
                    ->set('requiredReviewNode')->eq($requiredReviewNode)
                    ->where('id')->eq($transferID)
                    ->exec();

                // 记录操作历史和状态流程
                $this->loadModel('action')->create('sectransfer', $transferID, 'dealed');
                $this->loadModel('consumed')->record('sectransfer', $transferID, '0', $this->app->user->account, $transferInfo->status, $status, array());
                //更新工单是否最终对外移交
                $nowData = $this->sectransfer->getByID($transferID);
                if($nowData->secondorderId){
                    $this->sectransfer->updateSecondOrderFinallyHand($nowData->secondorderId,$transferID,$nowData);
                }
                if (isonlybody()) {
                    die(js::closeModal('parent.parent', $this->session->common_back_url));
                }
                die(js::reload('parent'));
            }
        }
    }

    //删除
    public function delete($transferID = 0, $confirm = 'no')
    {
        if (!empty($transferID)) {
            if($confirm == 'no')
            {
                echo js::confirm($this->lang->sectransfer->confirmDelete, $this->createLink('sectransfer', 'delete', "transferID=$transferID&confirm=yes"), '');
                exit;
            }
            else
            {
                $sectransfer = $this->dao->select('*')->from(TABLE_SECTRANSFER)
                    ->where('id')->eq($transferID)
                    ->fetch();
                $this->dao->update(TABLE_SECTRANSFER)
                    ->set('deleted')->eq('1')
                    ->where('id')->eq($transferID)->exec();
                $this->loadModel('action')->create('sectransfer', $transferID, 'deleted');

                //更新工单是否最终对外移交
                if($sectransfer->finallyHandOver != 0){
                    $nowData = $this->sectransfer->getByID($transferID);
                    $this->sectransfer->updateSecondOrderFinallyHand($sectransfer->secondorderId,$transferID,$nowData);
                }

                //若是外部工单移交并且状态是交付审批中， 修改工单的状态为待交付
                if($sectransfer->jftype == '2' && !empty($sectransfer->secondorderId)){
                    $secondorder = $this->loadModel('secondorder')->getById($sectransfer->secondorderId);
                   /* if($secondorder->status == 'indelivery'){
                        $this->dao->update(TABLE_SECONDORDER)->set('status')->eq('todelivered')
                            ->set('dealUser')->eq($secondorder->acceptUser)
                            ->where('id')->eq($secondorder->id)
                            ->exec();
                        $this->loadModel('action')->create('secondorder', $secondorder->id, 'syncstatusbyprotransfer');
                        $this->loadModel('consumed')->record('secondorder', $secondorder->id, '0', $this->app->user->account, $secondorder->status, 'todelivered', array());
                    }*/
                    //$this->loadModel('secondorder')->syncSectransferToSecondStatus($secondorder->id, $transferID) ;// 联动工单状态
                    $this->loadModel('secondorder')->syncSecondorderStatus($secondorder->id, 'sectransfer', $transferID, '', $transferID, 'todelivered');//删除对外移交联动工单状态
                }

                if (isonlybody()) {
                    die(js::closeModal('parent.parent', $this->session->common_back_url));
                }
                die(js::reload('parent'));
            }
        }
    }

    //退回
    public function reject($transferID = 0)
    {
        if ($_POST) {
            $this->sectransfer->reject($transferID);
            if (dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));
            $result['result']  = 'success';
            $result['message'] = $this->lang->saveSuccess;
            $result['locate']  = 'parent';
            $this->send($result);
        }

        $this->view->title          = $this->lang->sectransfer->reject;
        $this->display();
    }


    //edit
    public function edit($transferID = 0)
    {
        $this->loadModel('application');
        $this->app->loadLang('opinion');
        $transfer = $this->sectransfer->getByID($transferID);

        if ($_POST) {
            $changes = $this->sectransfer->update($transferID);

            if (dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $actionID = $this->loadModel('action')->create('sectransfer', $transferID, 'edited');
            $this->action->logHistory($actionID, $changes);

            $result['result']  = 'success';
            $result['message'] = $this->lang->saveSuccess;
            $result['locate']  = inlink('browse');
            $this->send($result);
        }

        // 查询对应领导
        $reviewers            = $this->loadModel('modify')->getReviewers();
        $users['CM']  = $reviewers[0];
        $users['own'] = $reviewers[2];
        $users['leader'] = $reviewers[5];
        $users['maxleader'] = $reviewers[6];
        $users['sec'] = $reviewers[7];

        $this->view->title          = $this->lang->sectransfer->edit;
        $this->view->transfer       = $transfer;
        $this->view->users          = $users;
        $this->view->hidden         = empty($transfer->protransferDesc) && $transfer->jftype == 2 ? 'hidden' : '';
        $this->view->apps           = array('' => '') + $this->loadModel('application')->getapplicationNameCodePairs();
        $this->view->inprojects     = array('' => '') + $this->sectransfer->getInprojects();
        $this->view->secondorders   = array('0' => '') + $this->sectransfer->getSecondorderPairs();
        $this->view->plans          = array('' => '') + $this->loadModel('outsideplan')->getPairs();
        $this->display();
    }

    // 复制移交单
    public function copy($transferID = 0)
    {
        $this->loadModel('application');
        $this->app->loadLang('opinion');
        if ($_POST) {
            $transferID = $this->sectransfer->create();
            if (dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));
            $this->loadModel('action')->create('sectransfer', $transferID, 'created', $this->post->comment);


            $result['result']  = 'success';
            $result['message'] = $this->lang->saveSuccess;
            $result['locate']  = inlink('browse');
            $this->send($result);
        }

        // 查询对应领导
        $reviewers            = $this->loadModel('modify')->getReviewers();
        $users['CM']  = $reviewers[0];
        $users['own'] = $reviewers[2];
        $users['leader'] = $reviewers[5];
        $users['maxleader'] = $reviewers[6];
        $users['sec'] = $reviewers[7];

        $this->view->title          = $this->lang->sectransfer->copy;
        $this->view->transfer       = $this->sectransfer->getByID($transferID);

        $this->view->users          = $users;
        $this->view->apps           = array('' => '') + $this->loadModel('application')->getapplicationNameCodePairs();
        $this->view->inprojects     = array('' => '') + $this->sectransfer->getInprojects();
        $this->view->secondorders   = array('0' => '') + $this->sectransfer->getSecondorderPairs();
        $this->view->plans          = array('' => '') + $this->loadModel('outsideplan')->getPairs();
        $this->display();
    }

    /**
     * @param string $app
     * 获取子类型
     */
    public function ajaxGetDepartment($app, $param = '')
    {
        $this->app->loadLang('application');
        $selected = '';
        if($app){
            $list = $this->loadModel('application')->getByID($app);
            $selected = $list->team;
        }
        if(!empty($param)){
            return $selected;
        }
        die(html::select('department', $this->lang->application->teamList, $selected, 'class=form-control chosen'));
    }




    /**
     * 详情页面.
     * shixuyang
     * @param  int    $id
     * @access public
     * @return void
     */
    public function view($id = 0)
    {
        $this->loadModel('application');
        $this->app->loadLang('opinion');
        $data = $this->sectransfer->getByID($id);

        $this->view->title       = $this->lang->sectransfer->view;
        $this->view->users       = $this->loadmodel('user')->getPairs('noletter|noclosed');
        $this->view->depts       = $this->loadModel('dept')->getDeptPairs();
        $this->view->actions     = $this->loadmodel('action')->getList('sectransfer', $id);
        $this->view->sectransfer = $data;
        $this->view->consumed = $data->consumed;
        $this->view->inprojectList = $this->sectransfer->getInprojects();
        $this->view->outprojectList =array('' => '') + $this->loadModel('outsideplan')->getPairs();
        $this->view->apps = $this->loadModel('application')->getapplicationNameCodePairs();
        $this->view->nodes       = $this->loadModel('review')->getNodes('sectransfer', $id, $data->version);
        if($data->secondorderId != 0){
            $this->view->secondorder = $this->loadModel('secondorder')->getById($data->secondorderId);
        }
        $this->display();
    }



    //导出数据
    public function export($orderBy = 'id_desc', $browseType = 'all')
    {
        $this->app->loadLang('sectransfer');
        $this->app->loadLang('application');
        unset($this->lang->exportTypeList['selected']);
        $this->lang->exportTypeList['all'] = '全部查询结果';
        if($_POST)
        {
            $this->loadModel('file');
            $sectransferLang   = $this->lang->sectransfer;
            $sectransferConfig = $this->config->sectransfer;

            /* Create field lists. */
            $fields = $this->post->exportFields ? $this->post->exportFields : explode(',', $sectransferConfig->list->exportFields);
            foreach($fields as $key => $fieldName)
            {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = isset($sectransferLang->$fieldName) ? $sectransferLang->$fieldName : $fieldName;
                unset($fields[$key]);
            }

            /* Get datas. */
            $datas = array();

            if($this->session->sectransferOnlyCondition)
            {
                $datas = $this->dao->select('*')->from(TABLE_SECTRANSFER)->where($this->session->sectransferQueryCondition)
                    ->andWhere('deleted')->eq('0')
                    ->beginIF($this->post->exportType == 'selected')->andWhere('id')->in($this->cookie->checkedItem)->fi()
                    ->orderBy('id_desc')->fetchAll('id');
            }
            else
            {
                $stmt = $this->dbh->query($this->session->sectransferQueryCondition . ($this->post->exportType == 'selected' ? " AND id IN({$this->cookie->checkedItem})" : '') . " ORDER BY " . strtr($orderBy, '_', ' '));
                while($row = $stmt->fetch()) $datas[$row->id] = $row;
            }

            $users         = $this->loadModel('user')->getPairs('noletter|noclosed');
            $depts         = $this->loadModel('dept')->getDeptPairs();
            $inprojectList = $this->sectransfer->getInprojects();
            if(!empty($datas)){
                $apps = $this->loadModel('application')->getapplicationNameCodePairs();
                foreach ($datas as $k=>$data)
                {
                    $data->inproject = zget($inprojectList, $data->inproject);
                    $data->jftype = zget($this->lang->sectransfer->transferTypeList, $data->jftype,'');
                    $data->app = zget($apps, $data->app,'');
                    $data->department = zget($this->lang->application->teamList, $data->department,'');
                    $data->iscode = zget($this->lang->sectransfer->oldOrNotList, $data->iscode,'');
                    $data->status = zget($this->lang->sectransfer->statusListName, $data->status, '');
                    $data->apply = zget($users, $data->apply, '');
                    $data->dept = zget($depts, $data->dept, '');
                    $dealUserTitle = '';
                    $dealUsersTitles = '';
                    if (!empty($data->approver)) {
                        foreach (explode(',', $data->approver) as $dealUser) {
                            if (!empty($dealUser)) $dealUserTitle .= zget($users, $dealUser, $dealUser) . ',';
                        }
                    }
                    $dealUsersTitles = trim($dealUserTitle, ',');
                    $data->approver = $dealUsersTitles;
                }
            }


            $this->post->set('fields', $fields);
            $this->post->set('rows', $datas);
            $this->post->set('kind', 'sectransfer');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        $this->view->fileName        = $this->lang->sectransfer->common.'-'.time();
        $this->view->allExportFields = $this->config->sectransfer->list->exportFields;
        $this->view->customExport    = false;

        $this->display();

    }


    // 审批
    public function review($transferID){
        $transfer       = $this->sectransfer->getByID($transferID);
        if($_POST)
        {
            $logChanges = $this->sectransfer->review($transferID);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $commit = $this->lang->sectransfer->reviewResult.'：'.$this->lang->sectransfer->reviewList[$this->post->result].'<br>'.$this->lang->sectransfer->dealOpinion.'：'.$this->post->suggest;
            if($this->post->sftpPath != ''){
                $commit = $commit.'<br>'.$this->lang->sectransfer->sftpPath.'：'.$this->post->sftpPath;
            }
            $action = in_array($transfer->status, ['waitCMApprove', 'waitSecApprove']) ? 'dealed' : 'reviewed';
            $actionID = $this->loadModel('action')->create('sectransfer', $transferID, $action, $commit);
            $this->action->logHistory($actionID, $logChanges);

            $response['result']  = 'success';
            // 二线专员通过时新增提示语
            if($transfer->status == $this->lang->sectransfer->statusList['waitSecApprove'] && $this->post->result == 'pass'){
                //如果状态为待交付，提示等待后台脚本执行
                $sectransfer = $this->dao->select('status')->from(TABLE_SECTRANSFER)
                    ->where('id')->eq($transferID)
                    ->andWhere('deleted')->eq(0)
                    ->fetch();
                $response['message'] = 'waitDeliver' == $sectransfer->status ? $this->lang->sectransfer->secNotice : $this->lang->saveSuccess;
            }else{
                $response['message'] = $this->lang->saveSuccess;
            }
            $response['locate']  = 'parent';
            $this->send($response);
        }

        // 二线专员审批时文案显示调整
        if($transfer->status == $this->lang->sectransfer->statusList['waitSecApprove']){
            $examine = $this->lang->sectransfer->dealed;
        }else{
            $examine = in_array($transfer->status,$this->lang->sectransfer->examineList) ? $this->lang->sectransfer->examine : $this->lang->sectransfer->leaderExamine;
        }
        //$this->view->title          = $this->lang->review->submit;
       // $this->view->position[]     = $this->lang->review->submit;
        $this->view->transfer       = $transfer;
        $this->view->examine        = $examine;
        $this->display();

    }

    public function pushUnacceptedFeedback($path){
        a($this->sectransfer->checkRemoteFile($path.'.zip'));
    }

    /**
     * 编辑工作流程
     * @param $sectransferID
     * @param $consumedID
     * @return void
     */
    public function workloadEdit($sectransferID = 0, $consumedID = 0)
    {
        if($_POST)
        {
            $changes = $this->sectransfer->workloadEdit($sectransferID, $consumedID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }


            $actionID = $this->loadModel('action')->create('sectransfer', $sectransferID, 'workloadedit', $this->post->comment);
            $this->action->logHistory($actionID, $changes);

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $this->view->title    = $this->lang->sectransfer->workloadEdit;
        $this->view->sectransfer  = $this->sectransfer->getByID($sectransferID);
        $this->view->users    = $this->loadModel('user')->getPairs('noclosed');

        $consumed = $this->sectransfer->getConsumedByID($consumedID);
        //相关配合人员详情信息
        $consumed->details = $this->loadModel('consumed')->getConsumedDetailsArray($consumed->details);
        $this->view->consumed = $consumed;

        //检查是否是最后一条工作量信息
        $isLastConsumed =  $this->loadModel('consumed')->checkIsLastConsumed($consumedID, $sectransferID, 'sectransfer');
        $this->view->isLastConsumed = $isLastConsumed;
        $this->display();
    }

    /**
     * 删除工作流程
     * @param $problemID
     * @param $consumedID
     * @return void
     */
    public function workloadDelete($sectransferID = 0, $consumedID = 0)
    {
        if($_POST)
        {
            $changes = $this->sectransfer->workloadDelete($sectransferID, $consumedID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $actionID = $this->loadModel('action')->create('sectransfer', $sectransferID, 'workloaddelete', $this->post->comment);
            $this->action->logHistory($actionID, $changes);

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $this->view->title   = $this->lang->sectransfer->workloadDelete;
        $this->view->problem = $this->sectransfer->getByID($sectransferID);
        $this->display();
    }

    /**
     * 同步失败重新推送
     * @param $id
     */
    public function push($id)
    {
        $data['status']         = 'waitDeliver';
        $data['pushStatus']     = 'tosend';
        $data['pushNum']  = 0;
        $data['pushTime']     = "";
        $data['approver']     = "guestjk";
        $this->dao->update(TABLE_SECTRANSFER)->data($data)->where('id')->eq($id)->exec();
        $this->loadModel('action')->create('sectransfer', $id, 'repush', "重新推送");
        // 保存流程状态
        $this->loadModel('consumed')->record('sectransfer', $id, '0', $this->app->user->account, 'askCenterFailed', 'waitDeliver', array());
        // 修改流程
        //修改流程状态
        $sectransfer = $this->dao->select('*')->from(TABLE_SECTRANSFER)
            ->where('id')->eq($id)
            ->andWhere('deleted')->eq(0)
            ->fetch();;
        $next = $this->dao->select('id')->from(TABLE_REVIEWNODE)->where('objectType')->eq('sectransfer')   //查找下一节点的状态
            ->andWhere('objectID')->eq($id)
            ->andWhere('version')->eq($sectransfer->version)
            ->andWhere('stage')->eq('6')->fetch('id');
        if($next)
        {
            $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('confirming')->where('id')->eq($next)->exec();  //更新下一节点的状态为confirming
            $this->dao->update(TABLE_REVIEWER)->set('status')->eq('confirming')->set('comment')->eq('')->set('reviewTime')->eq(null)->where('node')->eq($next)->exec();
        }
        die(js::locate($this->createLink('sectransfer', 'view', "sectransferId=$id"), 'parent.parent'));
    }

    /**
     * 查询历史数据
     * @param $id
     * @return void
     */
    public function showHistoryNodes($id){
        $res = $this->dao->select('version')->from(TABLE_REVIEWNODE)->where('objectType')->eq('sectransfer')->andWhere('objectID')->eq($id)->groupby('version')->fetchall();
        $versions = array_column($res,'version');
        foreach ($versions as $version) {
            $data = $this->loadModel('review')->getNodes('sectransfer', $id, $version);
            foreach ($data as $k=>$v){
                if ($v->status == 'wait' || !(is_array($v->reviewers) && !empty($v->reviewers))){
                    unset($data[$k]);
                }
            }
            $nodes[$version]['nodes'] = $data;
        }
        foreach ($nodes as $key=>$node) {
            $nodes[$key]['countNodes'] = count($node['nodes']);
        }
        $sectransfer = $this->sectransfer->getByID($id);
        if($sectransfer->secondorderId != 0){
            $this->view->secondorder = $this->loadModel('secondorder')->getById($sectransfer->secondorderId);
        }
        $this->view->nodes       = $nodes;
        $this->view->sectransfer = $sectransfer;
        $this->view->users       = $this->loadModel('user')->getPairs('noletter');
        $this->display();
    }

    /**
     * 获取任务单信息
     * @param $orderId
     * @return void
     */
    public function ajaxGetSecondOrder($orderId,$secondOrderID = null){
        $secondOrderObj  =  $this->loadModel('secondorder')->getById($orderId);
        $secondorderId = $this->dao->select('id')->from(TABLE_SECTRANSFER)
            ->Where('deleted')->eq('0')
            ->beginIF(empty($secondOrderID) || ($secondOrderID && $orderId != $secondOrderID))->andWhere('secondorderId')->eq($orderId)->fi()
            ->beginIF($secondOrderID && $orderId == $secondOrderID)->andWhere('secondorderId')->ne($orderId)->fi()
            ->fetch();
        //查询工单关联的所有对外移交(不包含待提交的)
      /*  $allSectransfer = $this->dao->select('id,finallyHandOver')->from(TABLE_SECTRANSFER)
            ->Where('deleted')->eq('0')
            ->andWhere('secondorderId')->eq($orderId)
            ->andWhere('status')->ne('waitApply')
            ->fetchAll('id');

        $handOver = '0'; //是否最终移交状态
        $whichSecTransfer = '';//影响工单的对外移交单号
        //是否最终移交集合
        $finallyHandOvers = isset($allSectransfer) ? array_filter(array_column($allSectransfer,'finallyHandOver','id'),function($item){return $item !== 0;}) : '';
        if($finallyHandOvers){
            foreach ($finallyHandOvers as $key => $finallyHandOver) {
                //是 最终移交
                if($finallyHandOver == 1){
                    $handOver = $finallyHandOver;
                    $whichSecTransfer = $key;
                    break;
                }else if($finallyHandOver == 2){
                    // 否
                    $handOver = $finallyHandOver;
                    $whichSecTransfer = $key;
                }
            }
        }*/
        //查询工单关联的所有对外移交(不包含待提交的)
        $end = $this->sectransfer->getEndFinallyHandOver($orderId);
        $secondOrderObj->handOver = $end->handOver; //最终移交状态
        $secondOrderObj->whichSecTransfer = $end->whichSecTransfer;//最终移交状态的对外移交id
        $secondOrderObj->secondOrderId = isset($secondorderId->id) ? $secondorderId->id : '';

        die(json_encode($secondOrderObj));
    }

    public function getUnPushDataJx()
    {
        $res[] = $this->loadModel('sectransfer')->getUnPushData();
        $res[] = $this->loadModel('sectransfer')->getUnPushDataJx();
        a($res);
    }

    /**
     * 产品登记、数据获取、对外移交报表
     * @param $start
     * @param $end
     * @param $type
     * @return void
     */
    public function ajaxMonthReport($start, $end, $type)
    {
        $this->loadModel('file');

        $data = $this->loadModel('sectransfer')->monthReport($start, $end, $type);
        foreach ($data as $key => $val){
            $data[$key] = (object)$val;
        }
        $fileName = ($type == 1 ? '对外交付' : '对外移交') . $start . '~' . $end;

        $this->post->set('fileType', 'xlsx');
        $this->post->set('fileName', $fileName);
        $this->post->set('fields', $this->lang->sectransfer->exportFileds);
        $this->post->set('rows', $data);
        $this->post->set('kind', 'sectransfer');
        $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
    }

    /**
     * 检查任务工单是否最终移交字段
     * @param $orderID
     * @param $sectransferId
     * @return bool
     */
    public function checkSecondOrderHandOver($orderID, $sectransferId)
    {
        $orderInfo = $this->dao->select('*')->from(TABLE_SECONDORDER)->where('id')->eq($orderID)->fetch();
        $end = $this->sectransfer->getEndFinallyHandOver($orderID, $sectransferId);

        if($orderInfo->formType == 'internal' && in_array($orderInfo->status, ['todelivered' ,'indelivery', 'delivered']) &&  $end->handOver == '1'){
            dao::$errors[] = sprintf($this->lang->sectransfer->secondOrderEndError, $end->whichSecTransfer);

            return false;
        }

        return true;
    }

    /**
     * 产品登记、数据获取、对外移交报表
     * @param $start
     * @param $end
     * @param $type
     * @return void
     */
    public function ajaxMonthReportByOrder($start, $end, $type)
    {
        $this->loadModel('file');

        $data = $this->loadModel('sectransfer')->monthReportByOrder($start, $end, $type);
        foreach ($data as $key => $val){
            $val['fixType'] = zget(['project'=>'项目','second'=>'二线'], $val['fixType']);
            $data[$key] = (object)$val;
        }
        $fileName = ($type == 1 ? '对外交付单' : '对外移交单') . $start . '~' . $end;

        $this->post->set('fileType', 'xlsx');
        $this->post->set('fileName', $fileName);
        $this->post->set('fields', $this->lang->sectransfer->exportFiledsByOrder);
        $this->post->set('rows', $data);
        $this->post->set('kind', 'sectransfer');
        $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
    }

}
