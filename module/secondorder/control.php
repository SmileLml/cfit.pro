<?php
class secondorder extends control
{
    /**
     * Method: browse
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
        $depts = $this->loadModel('dept')->getOptionMenu();
        $depts[0] = '';
        $this->config->secondorder->search['params']['createdDept']['values'] = $depts;
        $this->config->secondorder->search['params']['acceptDept']['values']  = $depts;

        $this->loadModel('application');
        $apps = $this->application->getPairs();
        $this->config->secondorder->search['params']['app']['values'] = $apps;


        $queryID = ($browseType == 'bysearch')  ? (int)$param : 0;
        $actionURL = $this->createLink('secondorder', 'browse', "browseType=bySearch&param=myQueryID");

        $this->app->loadLang("opinion");
        $this->config->secondorder->search['params']['union']['values'] = $this->lang->opinion->unionList;
        $this->config->secondorder->search['params']['team']['values'] = $this->lang->application->teamList;
        $childTypeList = $this->secondorder->getChildTypeTileList();
        $this->config->secondorder->search['params']['subtype']['values'] = $childTypeList;

        $this->secondorder->buildSearchForm($queryID, $actionURL);

        /* 设置详情页面返回的url连接。*/
        $this->session->set('secondorderList', $this->app->getURI(true));
        $this->session->set('common_back_url', $this->app->getURI(true),'backlog');

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);
        $secondorders = $this->secondorder->getList($browseType, $queryID, $orderBy, $pager);
        $this->view->title      = $this->lang->secondorder->common;
        $apps  = $this->loadModel('application')->getapplicationNameCodePairs();
        $this->view->apps       = $apps;
        $this->view->orderBy    = $orderBy;
        $this->view->pager      = $pager;
        $this->view->param      = $param;
        $this->view->browseType = $browseType;
        $this->view->depts      = $this->loadModel('dept')->getOptionMenu();
        $this->view->users      = $this->loadModel('user')->getPairs('noletter|noclosed');
        $this->view->secondorders = $secondorders;
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: create
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:52
     * Desc: This is the code comment. This method is called create.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     */
    public function create()
    {
        if($_POST)
        {
            $secondorderID = $this->secondorder->create();

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $this->loadModel('action')->create('secondorder', $secondorderID, 'created', $this->post->comment);
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = inlink('browse');

            $this->send($response);
        }

        $this->view->title         = $this->lang->secondorder->create;
        $this->view->users         = $this->loadModel('user')->getPairs('noclosed|noletter');
        $this->view->executive     = $this->loadModel('dept')->getById($this->app->user->dept)->executive;
        $this->view->apps          = array('' => '') + $this->loadModel('application')->getapplicationNameCodePairs();
        $this->view->outsideplan  = array('' => '') + $this->dao->select('id,name')->from(TABLE_OUTSIDEPLAN)
                ->where('deleted')->ne(1)->fetchPairs();
        $this->view->childTypeList = array();
        $this->app->loadLang('opinion');
        $this->app->loadLang('application');
        $this->display();
    }

    /**
     * Edit a secondorder.
     * 
     * @param  int $secondorderID 
     * @access public
     * @return void
     */
    public function edit($secondorderID = 0)
    {
        if($_POST)
        {
            $changes = $this->secondorder->update($secondorderID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $actionID = $this->loadModel('action')->create('secondorder', $secondorderID, 'edited', $this->post->comment);
            $this->action->logHistory($actionID, $changes);

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = inlink('browse');

            $this->send($response);
        }

        $this->view->title   = $this->lang->secondorder->edit;
        $this->view->users   = $this->loadModel('user')->getPairs('noclosed,noletter');
        $this->view->executive = $this->loadModel('dept')->getById($this->app->user->dept)->executive;
        $secondorder = $this->secondorder->getByID($secondorderID);
        $childTypeList = !isset($this->lang->secondorder->delTypeList[$secondorder->type]) ? $this->secondorder->getChildTypeList($secondorder->type) : [];
        if($secondorder->type == 'consult'){
            unset($childTypeList['a5']);
        }
        $this->view->childTypeList = $childTypeList;
        $this->view->apps  = array('' => '') + $this->loadModel('application')->getapplicationNameCodePairs();
        $this->view->secondorder = $secondorder;
        $this->view->outsideplan = array('' => '') + $this->dao->select('id,name')
                ->from(TABLE_OUTSIDEPLAN)->where('deleted')->ne(1)->fetchPairs();
        $this->app->loadLang('opinion');
        $this->app->loadLang('application');
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: editAssignedTo
     * Product: PhpStorm
     * @param int $secondorderID
     */
    public function editAssignedTo($secondorderID = 0)
    {
        if($_POST)
        {
            $changes = $this->secondorder->editAssignedTo($secondorderID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $actionID = $this->loadModel('action')->create('secondorder', $secondorderID, 'editAssignTo', $this->post->comment);
            $this->action->logHistory($actionID, $changes);

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $this->view->title      = $this->lang->secondorder->editAssignedTo;
        $this->view->secondorder    = $this->secondorder->getByID($secondorderID);
        $this->view->users      = $this->loadModel('user')->getPairs('noclosed|noletter');

        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: view
     * Product: PhpStorm
     * @param int $secondorderID
     */
    public function view($secondorderID = 0)
    {
        $secondorder = $this->secondorder->getByID($secondorderID);
        if(in_array($secondorder->type, array('consult', 'test'))) $Res = $secondorder->type . 'Res';
        else $Res = 'dealRes';
        $secondorder->Res = $secondorder->$Res;
        $consumeds = $this->secondorder->getConsumedsByID($secondorder->id);
        $this->view->relevantUsers = $this->secondorder->getrelevantUsers($consumeds);

        $this->view->title   = $this->lang->secondorder->view;
        $this->view->users   = $this->loadModel('user')->getPairs('noclosed|noletter');
        $this->view->actions = $this->loadModel('action')->getList('secondorder', $secondorderID);
        $secondorder->deliverFiles = $this->loadModel('file')->getByObject('secondorderDeliver', $secondorderID);
        $this->view->secondorder = $secondorder;
        $this->view->consumeds  = $consumeds;
        $this->view->Res = $this->lang->secondorder->$Res;
        $this->view->apps    = $this->loadModel('application')->getapplicationInfo();
        $this->view->depts   = $this->loadModel('dept')->getOptionMenu();
        $this->view->childTypeList = $this->secondorder->getChildTypeList($secondorder->type);
        $this->app->loadLang('opinion');
        $this->view->task = $this->loadModel('demand')->getTaskName(0,$secondorder->app,0,0,$secondorderID,0,'secondorder');
        if($this->view->task){
            $projectid = $this->dao->select('project')->from(TABLE_TASK_DEMAND_PROBLEM)->where('id')->eq($this->view->task->typeid)->fetch();
            $this->view->projectid = $projectid;
        }
        $this->app->loadLang('sectransfer');
        $this->view->buildAndRelease = $this->loadModel('demand')->getBuildRelease($this->view->task ? $this->view->task->id : 0);
        $this->view->protransferDesc = $this->secondorder->getProtransferById($secondorderID);
        //征信交付
        $creditList =  $this->loadModel('credit')->getSecondorderRelatedCreditList($secondorderID);
        $this->view->creditList = $creditList;
        $this->view->projectList = array('' => '') + $this->loadModel('projectplan')->getAliveProjects($secondorder->implementationForm == 'second');
        $this->view->outsideplan = array('' => '') + $this->dao->select('id,name')
                ->from(TABLE_OUTSIDEPLAN)->where('deleted')->ne(1)->fetchPairs();
        $this->view->executions  = $this->getExecutionSelect(
            $secondorder->internalProject,
            $secondorder->execution,
            $secondorder->implementationForm
        );

        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: deal
     * Product: PhpStorm
     * @param $secondorderID
     */
    public function deal($secondorderID)
    {
        if($_POST) {
            $ret = $this->secondorder->deal($secondorderID);

            if(dao::isError() or isset($ret->noPrj)) {
                $response['result']  = 'fail';
                $response['message'] = isset($ret->noPrj) ? $ret->noPrj : dao::getError();
                $this->send($response);
            }

            list($isTransfer, $changes, $flag) = $ret;
            if($flag){
                $actionID = $this->loadModel('action')->create('secondorder', $secondorderID, 'deal', $this->post->notAcceptReason);
                $this->loadModel('action')->create('secondorder', $secondorderID, 'closed', $this->post->notAcceptReason,'', 'guestjk');
            }else{
                $actionID = $this->loadModel('action')->create('secondorder', $secondorderID, 'deal', $this->post->notAcceptReason);
            }
            if($changes) $this->action->logHistory($actionID, $changes);

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            if($isTransfer){
                $response['locate'] = $this->createLink('secondorder', 'ajaxTransfer', 'secondorderID=' . $secondorderID);
            }

            $this->send($response);
        }

        $secondorder = $this->loadModel('secondorder')->getByID($secondorderID);
        if($secondorder->formType == 'external'){
            $this->config->secondorder->editor->deal = ['id' => '', 'tools' => 'simpleTools'];
        }
        if(empty($secondorder->completeStatus))
        {
            $secondorder->ifAccept = '1';
        }
        $this->view->childTypeList = $this->secondorder->getChildTypeTileList();
        $secondorder->overDate   = $secondorder->overDate != '0000-00-00' ? $secondorder->overDate : '';
        $secondorder->startDate  = $secondorder->startDate != '0000-00-00' ? $secondorder->startDate : '';
        $this->view->title       = $this->lang->secondorder->deal;
        $this->view->users       = $this->loadModel('user')->getPairs('noclosed|noletter');
        $this->view->apps        = array('' => '') + $this->loadModel('application')->getapplicationNameCodePairs();
        $this->view->projectList = array('' => '') + $this->loadModel('projectplan')->getAliveProjects(false);
        $this->view->outsideplan = array('' => '') + $this->dao->select('id,name')
                ->from(TABLE_OUTSIDEPLAN)->where('deleted')->ne(1)->fetchPairs();
        if($secondorder->subtype == 'a5'){
            if(empty($secondorder->implementationForm)){
                $secondorder->implementationForm = 'second';
                if(empty($secondorder->internalProject)){
                    $internalProject = $this->dao->select('t1.project')->from(TABLE_PROJECTPLAN)->alias('t1')
                        ->leftjoin(TABLE_PROJECT)->alias('t2')
                        ->on('t1.project=t2.id')
                        ->where('t1.deleted')->eq(0)
                        ->andwhere('t2.status')->ne('closed')
                        ->andwhere('t1.year')->eq('2022')
                        ->andwhere('t1.code')->like('%EX')
                        ->andWhere('t1.secondLine')->eq('1')
                        ->andWhere('t1.bearDept')->eq($this->app->user->dept)
                        ->fetch('project');
                    $secondorder->internalProject = $internalProject;
                }
            }
        }
        $this->view->secondorder = $secondorder;
        $this->display();
    }


    /**
     * Project: chengfangjinke
     * Method: close
     * @param int $secondorderID
     */
    public function close($secondorderID = 0)
    {
        if($_POST)
        {
            $this->secondorder->close($secondorderID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $this->loadModel('action')->create('secondorder', $secondorderID, 'closed', $this->post->comment);

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $this->view->title   = $this->lang->secondorder->close;
        $this->view->secondorder = $this->loadModel('secondorder')->getByID($secondorderID);
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: export
     * @param string $orderBy
     * @param string $browseType
     */
    public function export($orderBy = 'id_desc', $browseType = 'all')
    {
        /* format the fields of every secondorder in order to export data. */
        if($_POST)
        {
            $this->loadModel('file');
            $secondorderLang   = $this->lang->secondorder;
            $secondorderConfig = $this->config->secondorder;
            $this->app->loadLang('opinion');
            $this->app->loadLang('application');

            /* Create field lists. */
            $fields = $this->post->exportFields ? $this->post->exportFields : explode(',', $secondorderConfig->list->exportFields);
            foreach($fields as $key => $fieldName)
            {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = isset($secondorderLang->$fieldName) ? $secondorderLang->$fieldName : $fieldName;
                unset($fields[$key]);
            }
            /* Get secondorders. */
            $secondorders = array();
            if($this->session->secondorderOnlyCondition)
            {
                $secondorders = $this->dao->select('*')->from(TABLE_SECONDORDER)->where($this->session->secondorderQueryCondition)
                    ->andWhere('deleted')->ne('1')
                    ->beginIF($this->post->exportType == 'selected')->andWhere('id')->in($this->cookie->checkedItem)->fi()
                    ->orderBy($orderBy)->fetchAll('id');
            }
            else
            {
                $stmt = $this->dbh->query($this->session->secondorderQueryCondition . ($this->post->exportType == 'selected' ? " AND id IN({$this->cookie->checkedItem})" : '') . " ORDER BY " . strtr($orderBy, '_', ' '));
                while($row = $stmt->fetch()) $secondorders[$row->id] = $row;
            }
            $secondorderIdList = array_keys($secondorders);

            /* Get users, products and executions. */
            $users = $this->loadModel('user')->getPairs('noletter|noletter');
            $apps  = $this->loadModel('application')->getapplicationNameCodePairs();
            $depts = $this->loadModel('dept')->getTopPairs();
            $childTypeList = $this->secondorder->getChildTypeTileList();
            $internalProject = $this->loadModel('projectplan')->getAliveProjects(false);
            $internalSecond = $this->loadModel('projectplan')->getAliveProjects(true);
            $cbpProject = $this->dao->select('id,name')->from(TABLE_OUTSIDEPLAN)->where('deleted')->ne(1)->fetchPairs();

            foreach($secondorders as $secondorder)
            {
                $secondorder->status    = $secondorderLang->statusList[$secondorder->status];
                $secondorder->source    = $secondorderLang->sourceList[$secondorder->source];
                $secondorder->type      = $secondorderLang->typeList[$secondorder->type];
                $secondorder->subtype   = $childTypeList[$secondorder->subtype];
                //迭代二十六-过滤部门第一个'/'
                $secondorder->createdDept = ltrim($depts[$secondorder->createdDept], '/');
                $secondorder->createdBy   = zget($users, $secondorder->createdBy, '');
                $secondorder->app         = zget($apps, $secondorder->app, '');
                $secondorder->acceptUser  = zget($users, $secondorder->acceptUser, '');
                //迭代二十六-过滤部门第一个'/'
                $secondorder->acceptDept  = ltrim($depts[$secondorder->acceptDept], '/');

                $secondorder->dealUser    = zget($users, $secondorder->dealUser, '');
                $secondorder->editedBy    = zget($users, $secondorder->editedBy, $secondorder->editedBy);
                $secondorder->closedBy    = zget($users, $secondorder->closedBy, $secondorder->closedBy);
                $secondorder->contacts    = zget($users, $secondorder->contacts, $secondorder->contacts);
                if(!empty($secondorder->ifAccept) || $secondorder->ifAccept === '0'){
                    $secondorder->ifAccept =  zget($this->lang->secondorder->ifAcceptList, $secondorder->ifAccept, '');
                }elseif (!empty($secondorder->ifReceived)){
                    $secondorder->ifAccept = zget($this->lang->secondorder->ifReceivedList, $secondorder->ifReceived, '');
                }else{
                    $secondorder->ifAccept =  '';
                }
                $secondorder->team        = zget($this->lang->application->teamList, $secondorder->team, '');
                $secondorder->union       = zget($this->lang->opinion->unionList, $secondorder->union);
                $secondorder->progress    = preg_filter('/\<.*?\>/','',$secondorder->progress);
                $secondorder->consultRes  = strip_tags($secondorder->consultRes);
                $secondorder->testRes     = strip_tags($secondorder->testRes);
                $secondorder->dealRes     = strip_tags($secondorder->dealRes);
                $secondorder->desc        = strip_tags($secondorder->desc);
                $secondorder->cc          = zget($users, $secondorder->ccList, '');

                $secondorder->sourceBackground = $secondorderLang->sourceBackgroundList[$secondorder->sourceBackground];
                $secondorder->ifReceived = $secondorderLang->ifReceivedList[$secondorder->ifReceived];
                $secondorder->internalProject = $secondorder->implementationForm == 'second' ? zget($internalSecond, $secondorder->internalProject) : zget($internalProject, $secondorder->internalProject);
                $secondorder->implementationForm = $secondorderLang->implementationFormList[$secondorder->implementationForm];
                $secondorder->cbpProject = zget($cbpProject, $secondorder->cbpProject);
                $secondorder->rejectUser = zget($users, $secondorder->rejectUser);
                $secondorder->taskIdentification = zget($this->lang->secondorder->taskIdentificationList, $secondorder->taskIdentification);
                $secondorder->externalStatus = zget($this->lang->secondorder->externalStatusList, $secondorder->externalStatus);
                $secondorder->handoverMethod = zget($this->lang->secondorder->handoverMethodList, $secondorder->handoverMethod);
                $secondorder->requestCategory = zget($this->lang->secondorder->requestCategoryList, $secondorder->requestCategory);
                $secondorder->urgencyLevel = zget($this->lang->secondorder->urgencyDegreeList, $secondorder->urgencyLevel);
            }

            $this->post->set('fields', $fields);
            $this->post->set('rows', $secondorders);
            $this->post->set('kind', 'secondorder');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        $this->view->fileName        = $this->lang->secondorder->exportName;
        $this->view->allExportFields = $this->config->secondorder->list->exportFields;
        $this->view->customExport    = true;
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: delete
     * Product: PhpStorm
     * @param $secondorderID
     */
    public function delete($secondorderID)
    {
        if(!empty($_POST))
        {
            if($this->post->comment == '')
            {
                die(js::alert(sprintf($this->lang->secondorder->emptyObject, $this->lang->secondorder->comment)));
            }else {
                $this->dao->update(TABLE_SECONDORDER)->set('deleted')->eq('1')->where('id')->eq($secondorderID)->exec();
                $actionID = $this->loadModel('action')->create('secondorder', $secondorderID, 'deleted', $this->post->comment);

//            // 删除与二线管理单子的关联关系
//            $sql = "delete from zt_secondline where (objectType='modify'or objectType='fix' or objectType='gain') and relationID=$secondorderID  and relationType='secondorder';";
//            $this->dao->query($sql);

                //删除二线工单更新任务名
                $secondorder = $this->secondorder->getByID($secondorderID);
                /*$task = $this->loadModel('demand')->getTaskName(0,$secondorder->app,0,0,$secondorderID,0,'secondorder');
                if($task){
                    $this->loadModel('task')->deleteCodeUpdateTask($task,$secondorder->code);
                }*/
                $projectId = $this->secondorder->getProjectPlanInfo($secondorder->acceptDept);
                $this->loadModel('task')->checkCodeExist($projectId, $secondorder->app, 'secondorder', $secondorder->code, $secondorder, 1);

                $backUrl =  $this->session->secondorderList ? $this->session->secondorderList : inLink('browse');
                if(isonlybody()) die(js::closeModal('parent.parent', $backUrl));
                die(js::reload('parent'));
            }
        }

        $secondorder = $this->secondorder->getByID($secondorderID);
        $this->view->actions = $this->loadModel('action')->getList('secondorder', $secondorderID);
        $this->view->secondorder = $secondorder;
        $this->view->users   = $this->loadModel('user')->getPairs();
        $this->display();
    }


    /**
     * Project: chengfangjinke
     * Method: exportWord
     * Product: PhpStorm
     * @param $secondorderID
     */
    public function exportWord($secondorderID)
    {
        $secondorder = $this->secondorder->getById($secondorderID);
        $users   = $this->loadModel('user')->getPairs('noletter|noletter');

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

        $section->addTitle($this->lang->secondorder->exportTitle, 1);
        $section->addText($this->lang->secondorder->code . ' ' . $secondorder->code, 'font_default', 'align_right');

        $tableStyle = array(
            'unit' => \PhpOffice\PhpWord\Style\Table::WIDTH_PERCENT,
            'width' => 100 * 50,
            'cellMargin' => 50,
            'borderSize' => 10,
            'borderColor' => '000000',
        );
        $cellStyle = array();
        $table = $section->addTable($tableStyle);
        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->secondorder->abstract);
        $table->addCell(1000, $cellStyle)->addText($secondorder->abstract);
        $table->addCell(1000, $cellStyle)->addText($this->lang->secondorder->code);
        $table->addCell(1000, $cellStyle)->addText($secondorder->code);

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->secondorder->type);
        $table->addCell(1000, $cellStyle)->addText(zget($this->lang->secondorder->typeList, $secondorder->type, ''));
        $table->addCell(1000, $cellStyle)->addText($this->lang->secondorder->status);
        $table->addCell(1000, $cellStyle)->addText(zget($this->lang->secondorder->statusList, $secondorder->status, ''));

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->secondorder->createdBy);
        $table->addCell(1000, $cellStyle)->addText(zget($users, $secondorder->createdBy, ''));
        $table->addCell(1000, $cellStyle)->addText($this->lang->secondorder->createdDate);
        $table->addCell(1000, $cellStyle)->addText($secondorder->createdDate);

        $apps = $this->loadModel('application')->getapplicationNameCodePairs();
        $as = array();
        foreach(explode(',', $secondorder->app) as $app)
        {
            if(!$app) continue;
            $as[] = zget($apps, $app);
        }
        $secondorder->app = implode(',', $as);

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->secondorder->app);
        $table->addCell(3000, array('gridSpan' => 3))->addText($secondorder->app);

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->secondorder->desc);
        $table->addCell(3000, array('gridSpan' => 3))->addText(strip_tags($secondorder->desc));

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->secondorder->reason);
        $table->addCell(3000, array('gridSpan' => 3))->addText(strip_tags($secondorder->reason));

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->secondorder->solution);
        $table->addCell(3000, array('gridSpan' => 3))->addText(strip_tags($secondorder->solution));

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->secondorder->state);
        $table->addCell(3000, array('gridSpan' => 3))->addText(strip_tags($secondorder->state));

        // Obtain the receiver.
        $acceptUser = $this->dao->select('account')->from(TABLE_CONSUMED)
             ->where('objectType')->eq('secondorder')
             ->andWhere('objectID')->eq($secondorderID)
             ->andWhere('`before`')->eq('assigned')
             ->fetch('account');

        $acceptUserName = $acceptUser ? zget($users, $acceptUser, '') : '';
        $acceptDeptID   = 0;
        $acceptDeptName = '';
        if($acceptUser)   $acceptDeptID   = $this->dao->select('dept')->from(TABLE_USER)->where('account')->eq($acceptUser)->fetch('dept');
        if($acceptDeptID) $acceptDeptName = $this->dao->select('name')->from(TABLE_DEPT)->where('id')->eq($acceptDeptID)->fetch('name');

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->secondorder->acceptUser);
        $table->addCell(3000, array('gridSpan' => 3))->addText($acceptUserName);

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->secondorder->acceptDept);
        $table->addCell(3000, array('gridSpan' => 3))->addText($acceptDeptName);

        /* Review. */
        $table->addRow();
        $table->addCell(4000, array('gridSpan' => 4))->addText($this->lang->secondorder->consumedTitle, 'font_bold', array('align' => 'center'));

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->secondorder->nodeUser);
        $table->addCell(1000, $cellStyle)->addText($this->lang->secondorder->consumed);
        $table->addCell(1000, $cellStyle)->addText($this->lang->secondorder->before);
        $table->addCell(1000, $cellStyle)->addText($this->lang->secondorder->after);

        foreach($secondorder->consumed as $c)
        {
            $table->addRow();
            $table->addCell(1000, $cellStyle)->addText(zget($users, $c->createdBy, ''));
            $table->addCell(1000, $cellStyle)->addText($c->consumed . '' . $this->lang->hour);
            $table->addCell(1000, $cellStyle)->addText(zget($this->lang->secondorder->statusList, $c->before, '-'));
            $table->addCell(1000, $cellStyle)->addText(zget($this->lang->secondorder->statusList, $c->after, '-'));
        }

        $this->loadModel('file')->export2Word($this->lang->secondorder->exportTitle . $secondorder->code, $phpWord);
    }

    public function copy($secondorderID = 0)
    {
        if($_POST)
        {
            $secondorderID = $this->secondorder->create();

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $this->loadModel('action')->create('secondorder', $secondorderID, 'created', $this->post->comment);
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = inlink('browse');

            $this->send($response);
        }
        $this->view->title   = $this->lang->secondorder->copy;
        $this->view->users   = $this->loadModel('user')->getPairs('noclosed,noletter');
        $this->view->executive = $this->loadModel('dept')->getById($this->app->user->dept)->executive;
        $secondorder = $this->secondorder->getByID($secondorderID);
        $secondorder->overDate = $secondorder->overDate != '0000-00-00' ? $secondorder->overDate : '';
        $secondorder->startDate = $secondorder->startDate != '0000-00-00' ? $secondorder->startDate : '';
        $secondorder->exceptDoneDate = $secondorder->exceptDoneDate != '0000-00-00' ? $secondorder->exceptDoneDate : '';
        $this->view->secondorder = $secondorder;
        $childTypeList = !isset($this->lang->secondorder->delTypeList[$secondorder->type]) ? $this->secondorder->getChildTypeList($this->view->secondorder->type) : [];
        if($secondorder->type == 'consult'){
            unset($childTypeList['a5']);
        }
        $this->view->childTypeList = $childTypeList;
        $this->view->apps  = array('' => '') + $this->loadModel('application')->getapplicationNameCodePairs();
        $this->view->outsideplan = array('' => '') + $this->dao->select('id,name')
                ->from(TABLE_OUTSIDEPLAN)->where('deleted')->ne(1)->fetchPairs();
        $this->app->loadLang('opinion');
        $this->app->loadLang('application');
        $this->display();
    }

    /**
     * @param string $type
     * 获取子类型
     */
    public function ajaxGetChildTypeList($type = '')
    {
        $list = $this->secondorder->getChildTypeList($type);
        unset($list['a5']);
        die(html::select('subtype', $list, '', 'class=form-control'));
    }

    /**
     * 获取(外部)项目/任务
     * @return void
     */
    public function ajaxGetCbpProjectList()
    {
        $outsideplan  = array('' => '') + $this->dao->select('id,name')->from(TABLE_OUTSIDEPLAN)
                ->where('deleted')->ne(1)->fetchPairs();

        die(html::select('cbpProject', $outsideplan, '', 'class=form-control'));
    }

    /**
     * @param int $secondorderID
     * @param int $consumedID
     */
    public function statusedit($secondorderID = 0, $consumedID = 0)
    {
        if($_POST)
        {
            $changes = $this->secondorder->statusedit($secondorderID, $consumedID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $actionID = $this->loadModel('action')->create('secondorder', $secondorderID, 'statusedit', $this->post->comment);
            $this->action->logHistory($actionID, $changes);

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $this->view->title    = $this->lang->secondorder->statusedit;
        $this->view->secondorder  = $this->secondorder->getByID($secondorderID);
        $this->view->users    = $this->loadModel('user')->getPairs('noclosed,noletter');

        $consumed = $this->secondorder->getConsumedByID($consumedID);
        $this->view->consumed = $consumed;
        $this->display();
    }

    /**
     * @param string $app
     * 获取子类型
     */
    public function ajaxGetUnion($app)
    {
        $this->app->loadLang('opinion');
        $selected = '';
        if($app){
            $list = $this->loadModel('application')->getByID($app);
            $selected = $list->fromUnit;
        }
        die(html::select('union', $this->lang->opinion->unionList, $selected, 'class=form-control chosen'));
    }

    /**
     * @param string $app
     * 获取子类型
     */
    public function ajaxGetTeam($app)
    {
        $this->app->loadLang('application');
        $selected = '';
        if($app){
            $list = $this->loadModel('application')->getByID($app);
            $selected = $list->team;
        }
        die(html::select('team', $this->lang->application->teamList, $selected, 'class=form-control chosen'));
    }
    /**
     * 设置session
     */
    public function ajaxGetProjectId($project)
    {
        global $app;
        if($app->session->taskList){
            $uri ="/project-execution-all-$project.html";
            $app->session->set('taskList', $uri, 'project');
        }
        $this->session->set('project', (int)$project);
    }
   /**
    * 设置session
    */
     public function ajaxGetProjectSession($project)
     {
         global $app;
         if($app->session->releaseList){
             $uri ="/projectrelease-browse-$project.html";
             $app->session->set('releaseList', $uri, 'project');
         }
         $this->session->set('project', (int)$project);
     }

    /**
     * 设置session
     */
     public function ajaxGetProjectBuild($project)
     {

        if($this->session->buildList){
           $uri ="/project-build-$project.html";
           $this->session->set('buildList', $uri, 'project');
        }
        $this->session->set('project', (int)$project);
     }

    /**
     * 确认二线工单
     * @param $secondorderID
     * @return void
     */
    public function confirmed($secondorderID)
    {
        if(!empty($_POST))
        {
            $oldsecondorder = $this->secondorder->getByID($secondorderID);

            $data = ['ifReceived' => $this->post->ifReceived,];
            switch ($this->post->ifReceived){
                case 1:
                    if($this->post->notReceiveReason == ''){
                        dao::$errors['notReceiveReason'] = sprintf($this->lang->secondorder->emptyObject, $this->lang->secondorder->notReceiveReason);
                        break;
                    }
                    //如果是外部同步单状态为未受理，否则状态为已关闭
                    if($oldsecondorder->formType == 'external'){
                        $data['status']   = 'backed';
                        $data['dealUser'] = $oldsecondorder->createdBy;
                        $data['acceptUser'] = '';
                        $data['acceptDept'] = '0';
                        $data['implementationForm'] = '';
                        $data['internalProject'] = '';
                        $data['completeStatus'] = '';
                        $data['planstartDate'] = '';
                        $data['planoverDate'] = '';
                        $data['startDate'] = '';
                        $data['overDate'] = '';
                    }else{
                        $data['status']     = 'closed';
                        $data['closedBy']   = 'guestjk';
                        $data['closedDate'] = date('y-m-d H:i:s');
                        $data['dealUser']   = '';
                    }
                    $data['ifAccept']         = 0;
                    $data['notReceiveReason'] = $this->post->notReceiveReason;
                    break;
                case 2:
                    if($this->post->dealUser == ''){
                        dao::$errors['dealUser'] = sprintf($this->lang->secondorder->emptyObject, $this->lang->secondorder->dealUser);
                        break;
                    }
                    $data['status']       = 'assigned';
                    $data['dealUser']     = $this->post->dealUser;
                    $data['acceptUser']   = $this->post->dealUser;
                    $data['acceptDept']   = $this->secondorder->getDeptByUser($this->post->dealUser);
                    $data['notReceiveReason'] = '';
                    break;
                default:
                    dao::$errors['ifReceived'] = sprintf($this->lang->secondorder->emptyObject, $this->lang->secondorder->ifReceived);
            }

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
            }else{
                $this->dao->begin();  //开启事务
                $this->dao->update(TABLE_SECONDORDER)
                    ->data($data)
                    ->where('id')->eq($secondorderID)
                    ->exec();
                if($this->post->ifReceived == 1){
                    $this->post->comment = "确认结果：未接受<br/>未接受原因：{$this->post->notReceiveReason}";
                }

                $this->secondorder->tryError(1);
                //若是外部单需同步状态到清总
                if($this->post->ifReceived == 1 and $this->post->formType == 'external' && 'guestcn' == $oldsecondorder->createdBy){
                    if($oldsecondorder->subtype != 'a5'){
                        $requestClass = $this->secondorder->pushFeedback($secondorderID);
                    }else{
                        $requestClass = $this->secondorder->pushUniversalFeedback($secondorderID);
                    }
                    $this->secondorder->tryErrorRequest(1, $requestClass);
                    //保存发送日志
                    $this->loadModel('requestlog')->saveRequestLog($requestClass->url, $requestClass->object, $requestClass->objectType, $requestClass->method,
                        $requestClass->pushData, $requestClass->response, $requestClass->status, $requestClass->extra, $requestClass->id);
                }elseif ($this->post->ifReceived == 1 and $this->post->formType == 'external' && 'guestjx' == $oldsecondorder->createdBy){
                    $requestClass = $this->secondorder->pushFeedBackJx($secondorderID);
                    $this->secondorder->tryErrorRequest(1, $requestClass);
                    //保存发送日志
                    $this->loadModel('requestlog')->saveRequestLog($requestClass->url, $requestClass->object, $requestClass->objectType, $requestClass->method,
                        $requestClass->pushData, $requestClass->response, $requestClass->status, $requestClass->extra, $requestClass->id);
                }
                $this->dao->commit(); //提交事务

                //内部自建工单未受理状态置为已关闭，关闭人为成方金科，添加状态流转、历史记录
                $this->loadModel('action')->create('secondorder', $secondorderID, 'reviewedconfirm', $this->post->comment);
                if('closed' == $data['status']){
                    $this->loadModel('action')->create('secondorder', $secondorderID, 'closed', '','', 'guestjk');
                    $this->loadModel('consumed')->record('secondorder', $secondorderID, 0, $this->app->user->account, $oldsecondorder->status, 'backed', array());
                    $this->loadModel('consumed')->record('secondorder', $secondorderID, 0, 'guestjk', 'backed', $data['status'], array());
                }else{
                    $this->loadModel('consumed')->record('secondorder', $secondorderID, 0, $this->app->user->account, $oldsecondorder->status, $data['status'], array());
                }

                $response['result']  = 'success';
                $response['message'] = $this->lang->saveSuccess;
                $response['locate']  = 'parent';
            }

            $this->send($response);
        }

        $secondorder = $this->secondorder->getByID($secondorderID);
        $this->view->actions = $this->loadModel('action')->getList('secondorder', $secondorderID);
        $this->view->secondorder = $secondorder;
        $this->view->users   = $this->loadModel('user')->getPairs();
        $this->display();
    }

    public function ajaxGetSecondLine($fixType)
    {
        $secondLineType = $fixType == 'second';
        $projects = array('' => '') +  $this->loadModel('projectplan')->getAliveProjects($secondLineType);
        echo html::select('internalProject', $projects, '',"class='form-control chosen'");
    }

    /**
     * 跳转对外移交工单
     * @param $secondorderID
     * @return void
     */
    public function ajaxTransfer($secondorderID = 0)
    {
        if($_POST) {
            $this->dao->update(TABLE_SECONDORDER)
                ->set('status')->eq('todelivered')
                ->where('id')->eq($secondorderID)
                ->exec();

            $this->loadModel('consumed')
                ->record('secondorder', $secondorderID, 0, $this->app->user->account, 'solved', 'todelivered', array());

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = $this->post->status != 1 ? 'parent' : $this->createLink('sectransfer', 'create', 'secondorderId=' . $secondorderID);

            $this->send($response);
        }

        $this->view->title   = $this->lang->secondorder->linkTransfer;
        $this->view->secondorderID =$secondorderID;
        $this->display();
    }

    /**
     * 总中心退回处理
     * @param $secondorderID
     * @return void
     */
    public function returned($secondorderID = 0)
    {
        if($_POST) {
            $changes = $this->secondorder->returned($secondorderID);

            if(dao::isError() or isset($changes->noPrj))
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $this->loadModel('action')->create('secondorder', $secondorderID, 'dealReturned', $this->post->comment);

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = is_array($changes) ? 'parent' : $changes;

            $this->send($response);
        }

        $secondorder = $this->secondorder->getByID($secondorderID);
        $secondorder->deliverList = $this->loadModel('file')->getByObject('secondorderDeliver', $secondorderID);
        $this->view->secondorder = $secondorder;
        $this->view->task = $this->loadModel('demand')->getTaskName(0,$secondorder->app,0,0,$secondorderID,0,'secondorder');
        $this->view->title   = $this->lang->secondorder->returned;
        $this->display();
    }

    /**
     * QA追加当前进展
     * @param $secondorderID
     * @return void
     */
    public function editSpecialQA($secondorderID = 0)
    {
        $secondorder = $this->loadModel('secondorder')->getByID($secondorderID);
        if($_POST) {
            $changes = $this->secondorder->editSpecialQA($secondorderID);

            if(dao::isError()) {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes) {
                $actionID = $this->loadModel('action')->create('secondorder', $secondorderID, 'editspecialed');
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $this->view->secondorder = $secondorder;
        $this->display();
    }

    public function getExecutionSelect($projectID, $executionID = 0,$fixtype = null,$app = null)
    {
        $this->loadModel('project');
        $defaults =  [];
        if(!empty($projectID)) {
            $defaults = $this->project->getExecutionByAvailable($projectID);
        }
        $this->app->loadLang('task');
        $gd = isset($this->lang->task->stageList['sendgd']) ? $this->lang->task->stageList['sendgd'] : '' ;
        if($fixtype == 'second'){
            if($app) {
                $apps = explode(',', $app);
                $new = array();
                foreach ($apps as $app) {
                    if($app == '') continue;
                    $appname = $this->dao->select('concat(code,"_",name) as name')->from(TABLE_APPLICATION)->where('id')->eq($app)->fetch('name');
                    $defaults = array_filter($defaults);
                    $where = "readonly = 'readonly'";
                    if(empty($defaults) && $projectID){
                        die(html::input('execution', "", "class= form-control executionClass' notype=1 $where " ));
                    }else if($defaults && $projectID){
                        foreach ($defaults as $key=>$default) {
                            //过滤二线工单
                            if(strstr($default,$gd) !== false){
                                continue;
                            }
                            $defa = trim(strrchr($default,'/'),'/');
                            if($defa == $appname){
                                $executionID = $key;
                                unset($defaults);
                                $new = array($key=>$default);
                                $defaults = $new;
                                break;
                            }
                        }
                    }
                    if($new){
                        break;
                    }
                }
                if($executionID == 'undefined' && !$new){
                    return [];
                }
                if($projectID && $defaults ){
                    return $defaults;
                }
            }
        }

        return $defaults;
    }

    public function assignBYUser($secondorderId)
    {
        $secondorder = $this->secondorder->getById($secondorderId);

        if(!empty($_POST))
        {
            $this->loadModel('action');
            $dealUser = $this->post->dealUser;
            if(empty($dealUser)){
                $response['result']  = 'fail';
                $response['message'] = sprintf($this->lang->secondorder->emptyObject, $this->lang->secondorder->assignTo);
                $this->send($response);
            }
            if($dealUser == $secondorder->dealUser){
                $response['result']  = 'fail';
                $response['message'] = $this->lang->secondorder->assignToFail;
                $this->send($response);
            }

            $this->dao->update(TABLE_SECONDORDER)->set('dealUser')->eq($dealUser)->where('id')->eq($secondorderId)->exec();
            $this->action->create('secondorder', $secondorderId, 'assigned', $this->post->comment, $dealUser);

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }

        $this->view->title         = $this->lang->secondorder->assignedByUser;
        $this->view->users         = $this->loadModel('user')->getPairs('nodeleted|nofeedback', $secondorder->dealUser);
        $this->view->secondorder   = $secondorder;
        $this->view->secondorderId = $secondorderId;
        $this->view->actions       = $this->loadModel('action')->getList('secondorder', $secondorderId);

        $this->display();
    }

    public function changeStatus()
    {
        try {
            $res['secondOrderChange'] = $this->loadModel('secondorder')->changestatus();
        } catch (Exception $e) {
            $res['secondOrderChange'] = $e;
        }
        a($res);
    }
    /**
     * 为了加进展跟踪信息权限
     */
    public function getProgressInfo()
    {
        return true;
    }

    /**编辑是否最终对外移交
     * @param int $secondorderID
     */
    public function editFinallyHandOver($secondorderID = 0)
    {
        if($_POST)
        {
            $changes = $this->secondorder->editFinallyHandOver($secondorderID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $actionID = $this->loadModel('action')->create('secondorder', $secondorderID, 'editFinallyHandOvered', $this->post->comment);
            $this->action->logHistory($actionID, $changes);

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }
        $this->app->loadLang('sectransfer');
        $this->view->title      = $this->lang->secondorder->editFinallyHandOver;
        $this->view->secondorder    = $this->secondorder->getByID($secondorderID);
        $this->view->users      = $this->loadModel('user')->getPairs('noclosed|noletter');

        $this->display();
    }
}
