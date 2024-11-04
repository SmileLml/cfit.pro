<?php
class deptorder extends control
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
        $this->config->deptorder->search['params']['createdDept']['values'] = $depts;
        $this->config->deptorder->search['params']['acceptDept']['values']  = $depts;
//        $this->config->deptorder->search['params']['ifAccept']['values']  += array();

        $this->loadModel('application');
        $apps = $this->application->getPairs();
        $this->config->deptorder->search['params']['app']['values'] = $apps;


        $queryID = ($browseType == 'bysearch')  ? (int)$param : 0;
        $actionURL = $this->createLink('deptorder', 'browse', "browseType=bySearch&param=myQueryID");

        //$this->app->loadLang("opinion");
        $this->config->deptorder->search['params']['union']['values'] = $this->lang->deptorder->unionList;
        //$this->config->deptorder->search['params']['team']['values'] = $this->lang->application->teamList;
        $childTypeList = $this->deptorder->getChildTypeTileList();
        $this->config->deptorder->search['params']['subtype']['values'] = $childTypeList;

        $this->deptorder->buildSearchForm($queryID, $actionURL);

        /* 设置详情页面返回的url连接。*/
        $this->session->set('deptorderList', $this->app->getURI(true));
        $this->session->set('common_back_url', $this->app->getURI(true),'backlog');

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $deptorders = $this->deptorder->getList($browseType, $queryID, $orderBy, $pager);

        $this->view->title      = $this->lang->deptorder->common;
        $apps  = $this->loadModel('application')->getPairs();
        $this->view->apps       = $apps;
        $this->view->orderBy    = $orderBy;
        $this->view->pager      = $pager;
        $this->view->param      = $param;
        $this->view->browseType = $browseType;
        $this->view->depts      = $this->loadModel('dept')->getOptionMenu();
        $this->view->users      = $this->loadModel('user')->getPairs('noletter|noclosed');
        $this->view->deptorders = $deptorders;
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
            $deptorderID = $this->deptorder->create();

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $this->loadModel('action')->create('deptorder', $deptorderID, 'created', $this->post->comment);
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = inlink('browse');

            $this->send($response);
        }

        $this->view->title = $this->lang->deptorder->create;
        $this->view->users = $this->loadModel('user')->getPairs('noclosed');
        $this->view->apps  = array('' => '') + $this->loadModel('application')->getPairs();
        $this->view->childTypeList = array();
        //$this->app->loadLang('opinion');
        $this->app->loadLang('application');
        $this->display();
    }

    /**
     * Edit a deptorder.
     * 
     * @param  int $deptorderID
     * @access public
     * @return void
     */
    public function edit($deptorderID = 0)
    {
        if($_POST)
        {
            $changes = $this->deptorder->update($deptorderID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

//            if($changes || $this->post->comment)
//            {
                $actionID = $this->loadModel('action')->create('deptorder', $deptorderID, 'edited', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
//            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = inlink('view', "deptorderID=$deptorderID");

            $this->send($response);
        }

        $this->view->title   = $this->lang->deptorder->edit;
        $this->view->users   = $this->loadModel('user')->getPairs('noclosed,noletter');
        $deptorder = $this->deptorder->getByID($deptorderID);
        $this->view->childTypeList = $this->deptorder->getChildTypeList($deptorder->type);
        $this->view->apps  = array('' => '') + $this->loadModel('application')->getPairs();
        $this->view->deptorder = $deptorder;
        //$this->app->loadLang('opinion');
        $this->app->loadLang('application');
        $this->display();
    }




    /**
     * Project: chengfangjinke
     * Method: editAssignedTo
     * Product: PhpStorm
     * @param int $deptorderID
     */
    public function editAssignedTo($deptorderID = 0)
    {
        if($_POST)
        {
            $changes = $this->deptorder->editAssignedTo($deptorderID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $actionID = $this->loadModel('action')->create('deptorder', $deptorderID, 'editAssignTo', $this->post->comment);
            $this->action->logHistory($actionID, $changes);

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $this->view->title      = $this->lang->deptorder->editAssignedTo;
        $this->view->deptorder    = $this->deptorder->getByID($deptorderID);
        $this->view->users      = $this->loadModel('user')->getPairs('noclosed');

        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: view
     * Product: PhpStorm
     * @param int $deptorderID
     */
    public function view($deptorderID = 0)
    {
        $deptorder = $this->deptorder->getByID($deptorderID);
        if(in_array($deptorder->type, array('consult', 'test'))) $Res = $deptorder->type . 'Res';
        else $Res = 'dealRes';
        $deptorder->Res = $deptorder->$Res;
        $consumeds = $this->deptorder->getConsumedsByID($deptorder->id);
        $this->view->relevantUsers = $this->deptorder->getrelevantUsers($consumeds);

        $this->view->title   = $this->lang->deptorder->view;
        $this->view->users   = $this->loadModel('user')->getPairs('noclosed|noletter');
        $this->view->actions = $this->loadModel('action')->getList('deptorder', $deptorderID);
        $this->view->deptorder = $deptorder;
        $this->view->consumeds  = $consumeds;
        $this->view->Res = $this->lang->deptorder->$Res;
        $this->view->apps    = $this->loadModel('application')->getapplicationInfo();
        $this->view->depts   = $this->loadModel('dept')->getOptionMenu();
        $this->view->childTypeList = $this->deptorder->getChildTypeList($deptorder->type);
        //$this->app->loadLang('opinion');
        $this->view->task = $this->loadModel('demand')->getTaskName(0,$deptorder->app,0,0,$deptorderID,0,'deptorder');
        if($this->view->task){
            $projectid = $this->dao->select('project')->from(TABLE_TASK_DEMAND_PROBLEM)->where('id')->eq($this->view->task->typeid)->fetch();
            $this->view->projectid = $projectid;
        }
        $this->view->buildAndRelease = $this->loadModel('demand')->getBuildRelease($this->view->task ? $this->view->task->id : 0);
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: deal
     * Product: PhpStorm
     * @param $deptorderID
     */
    public function deal($deptorderID)
    {
        if($_POST)
        {
            $changes = $this->deptorder->deal($deptorderID);

            if(dao::isError() or isset($changes->noPrj))
            {
                $response['result']  = 'fail';
                $response['message'] = isset($changes->noPrj) ? $changes->noPrj : dao::getError();
                $this->send($response);
            }

            $actionID = $this->loadModel('action')->create('deptorder', $deptorderID, 'deal', $this->post->comment);
            if($changes) $this->action->logHistory($actionID, $changes);

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $deptorder = $this->loadModel('deptorder')->getByID($deptorderID);
        if(empty($deptorder->completeStatus))
        {
            $deptorder->ifAccept = '1';
        }
        $deptorder->overDate = $deptorder->overDate != '0000-00-00' ? $deptorder->overDate : '';
        $deptorder->startDate = $deptorder->startDate != '0000-00-00' ? $deptorder->startDate : '';
        $this->view->title      = $this->lang->deptorder->deal;
        $this->view->users      = $this->loadModel('user')->getPairs('noclosed');
        $this->view->apps  = array('' => '') + $this->loadModel('application')->getPairs();
        $this->view->deptorder    = $deptorder;
        $this->display();
    }


    /**
     * Project: chengfangjinke
     * Method: close
     * @param int $deptorderID
     */
    public function close($deptorderID = 0)
    {
        if($_POST)
        {
            $this->deptorder->close($deptorderID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $this->loadModel('action')->create('deptorder', $deptorderID, 'closed', $this->post->comment);

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $this->view->title   = $this->lang->deptorder->close;
        $this->view->deptorder = $this->loadModel('deptorder')->getByID($deptorderID);
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
        /* format the fields of every deptorder in order to export data. */
        if($_POST)
        {
            $this->loadModel('file');
            $deptorderLang   = $this->lang->deptorder;
            $deptorderConfig = $this->config->deptorder;
            //$this->app->loadLang('opinion');
            $this->app->loadLang('application');

            /* Create field lists. */
            $fields = $this->post->exportFields ? $this->post->exportFields : explode(',', $deptorderConfig->list->exportFields);
            foreach($fields as $key => $fieldName)
            {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = isset($deptorderLang->$fieldName) ? $deptorderLang->$fieldName : $fieldName;
                unset($fields[$key]);
            }
            /* Get deptorders. */
            $deptorders = array();
            if($this->session->deptorderOnlyCondition)
            {
                $deptorders = $this->dao->select('*')->from(TABLE_DEPTORDER)->where($this->session->deptorderQueryCondition)
                    ->andWhere('deleted')->ne('1')
                    ->beginIF($this->post->exportType == 'selected')->andWhere('id')->in($this->cookie->checkedItem)->fi()
                    ->orderBy($orderBy)->fetchAll('id');
            }
            else
            {
                $stmt = $this->dbh->query($this->session->deptorderQueryCondition . ($this->post->exportType == 'selected' ? " AND id IN({$this->cookie->checkedItem})" : '') . " ORDER BY " . strtr($orderBy, '_', ' '));
                while($row = $stmt->fetch()) $deptorders[$row->id] = $row;
            }
            $deptorderIdList = array_keys($deptorders);

            /* Get users, products and executions. */
            $users = $this->loadModel('user')->getPairs('noletter');
            $apps  = $this->loadModel('application')->getPairs();
            $depts = $this->loadModel('dept')->getTopPairs();
            $childTypeList = $this->deptorder->getChildTypeTileList();

            foreach($deptorders as $deptorder)
            {
                $userList = '';
                $deptorder->status    = $deptorderLang->statusList[$deptorder->status];
                $deptorder->source    = $deptorderLang->sourceList[$deptorder->source];
                $deptorder->type      = $deptorderLang->typeList[$deptorder->type];
                $deptorder->subtype   = $childTypeList[$deptorder->subtype];
                $deptorder->createdDept = $depts[$deptorder->createdDept];
                $deptorder->createdBy   = zget($users, $deptorder->createdBy, '');
                $deptorder->app         = zget($apps, $deptorder->app, '');
                $deptorder->acceptUser  = zget($users, $deptorder->acceptUser, '');
                $deptorder->acceptDept  = $depts[$deptorder->acceptDept];

                $deptorder->dealUser    = zget($users, $deptorder->dealUser, '');
                $deptorder->editedBy    = zget($users, $deptorder->editedBy, '');
                $deptorder->closedBy    = zget($users, $deptorder->closedBy, '');
                $deptorder->ifAccept    = zget($deptorderLang->ifAcceptList, $deptorder->ifAccept, '');
                foreach(explode(',', trim($deptorder->team, ',')) as $user) $userList .= $users[$user] . ',';
                $deptorder->team        = trim($userList, ',');
                $deptorder->union       = zget($this->lang->deptorder->unionList, $deptorder->union);
                $deptorder->progress = preg_filter('/\<.*?\>/','',$deptorder->progress);
                $deptorder->consultRes = strip_tags($deptorder->consultRes);
                $deptorder->testRes = strip_tags($deptorder->testRes);
                $deptorder->dealRes = strip_tags($deptorder->dealRes);
                $deptorder->desc = strip_tags($deptorder->desc);

            }

            $this->post->set('fields', $fields);
            $this->post->set('rows', $deptorders);
            $this->post->set('kind', 'deptorder');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        $this->view->fileName        = $this->lang->deptorder->exportName;
        $this->view->allExportFields = $this->config->deptorder->list->exportFields;
        $this->view->customExport    = true;
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: delete
     * Product: PhpStorm
     * @param $deptorderID
     */
    public function delete($deptorderID)
    {
        if(!empty($_POST))
        {
            if($this->post->comment == '')
            {
                die(js::alert(sprintf($this->lang->deptorder->emptyObject, $this->lang->deptorder->comment)));
            }else {
                $this->dao->update(TABLE_DEPTORDER)->set('deleted')->eq('1')->where('id')->eq($deptorderID)->exec();
                $actionID = $this->loadModel('action')->create('deptorder', $deptorderID, 'deleted', $this->post->comment);

//            // 删除与二线管理单子的关联关系
//            $sql = "delete from zt_secondline where (objectType='modify'or objectType='fix' or objectType='gain') and relationID=$deptorderID  and relationType='deptorder';";
//            $this->dao->query($sql);

                //删除二线工单更新任务名
                $deptorder = $this->deptorder->getByID($deptorderID);
               /* $task = $this->loadModel('demand')->getTaskName(0,$deptorder->app,0,0,$deptorderID,0);
                if($task){
                    $this->loadModel('task')->deleteCodeUpdateTask($task,$deptorder->code);
                }*/
                $projectId = $this->deptorder->getProjectPlanInfo($deptorder->acceptDept);
                $this->loadModel('task')->checkCodeExist($projectId, $deptorder->app, 'deptorder', $deptorder->code, $deptorder, 1);

                $backUrl =  $this->session->deptorderList ? $this->session->deptorderList : inLink('browse');
                if(isonlybody()) die(js::closeModal('parent.parent', $backUrl));
                die(js::reload('parent'));
            }
        }

        $deptorder = $this->deptorder->getByID($deptorderID);
        $this->view->actions = $this->loadModel('action')->getList('deptorder', $deptorderID);
        $this->view->deptorder = $deptorder;
        $this->view->users   = $this->loadModel('user')->getPairs();
        $this->display();
    }


    /**
     * Project: chengfangjinke
     * Method: exportWord
     * Product: PhpStorm
     * @param $deptorderID
     */
    public function exportWord($deptorderID)
    {
        $deptorder = $this->deptorder->getById($deptorderID);
        $users   = $this->loadModel('user')->getPairs('noletter');

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

        $section->addTitle($this->lang->deptorder->exportTitle, 1);
        $section->addText($this->lang->deptorder->code . ' ' . $deptorder->code, 'font_default', 'align_right');

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
        $table->addCell(1000, $cellStyle)->addText($this->lang->deptorder->abstract);
        $table->addCell(1000, $cellStyle)->addText($deptorder->abstract);
        $table->addCell(1000, $cellStyle)->addText($this->lang->deptorder->code);
        $table->addCell(1000, $cellStyle)->addText($deptorder->code);

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->deptorder->type);
        $table->addCell(1000, $cellStyle)->addText(zget($this->lang->deptorder->typeList, $deptorder->type, ''));
        $table->addCell(1000, $cellStyle)->addText($this->lang->deptorder->status);
        $table->addCell(1000, $cellStyle)->addText(zget($this->lang->deptorder->statusList, $deptorder->status, ''));

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->deptorder->createdBy);
        $table->addCell(1000, $cellStyle)->addText(zget($users, $deptorder->createdBy, ''));
        $table->addCell(1000, $cellStyle)->addText($this->lang->deptorder->createdDate);
        $table->addCell(1000, $cellStyle)->addText($deptorder->createdDate);

        $apps = $this->loadModel('application')->getPairs();
        $as = array();
        foreach(explode(',', $deptorder->app) as $app)
        {
            if(!$app) continue;
            $as[] = zget($apps, $app);
        }
        $deptorder->app = implode(',', $as);

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->deptorder->app);
        $table->addCell(3000, array('gridSpan' => 3))->addText($deptorder->app);

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->deptorder->desc);
        $table->addCell(3000, array('gridSpan' => 3))->addText(strip_tags($deptorder->desc));

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->deptorder->reason);
        $table->addCell(3000, array('gridSpan' => 3))->addText(strip_tags($deptorder->reason));

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->deptorder->solution);
        $table->addCell(3000, array('gridSpan' => 3))->addText(strip_tags($deptorder->solution));

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->deptorder->state);
        $table->addCell(3000, array('gridSpan' => 3))->addText(strip_tags($deptorder->state));

        // Obtain the receiver.
        $acceptUser = $this->dao->select('account')->from(TABLE_CONSUMED)
             ->where('objectType')->eq('deptorder')
             ->andWhere('objectID')->eq($deptorderID)
             ->andWhere('`before`')->eq('assigned')
             ->fetch('account');

        $acceptUserName = $acceptUser ? zget($users, $acceptUser, '') : '';
        $acceptDeptID   = 0;
        $acceptDeptName = '';
        if($acceptUser)   $acceptDeptID   = $this->dao->select('dept')->from(TABLE_USER)->where('account')->eq($acceptUser)->fetch('dept');
        if($acceptDeptID) $acceptDeptName = $this->dao->select('name')->from(TABLE_DEPT)->where('id')->eq($acceptDeptID)->fetch('name');

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->deptorder->acceptUser);
        $table->addCell(3000, array('gridSpan' => 3))->addText($acceptUserName);

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->deptorder->acceptDept);
        $table->addCell(3000, array('gridSpan' => 3))->addText($acceptDeptName);

        /* Review. */
        $table->addRow();
        $table->addCell(4000, array('gridSpan' => 4))->addText($this->lang->deptorder->consumedTitle, 'font_bold', array('align' => 'center'));

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->deptorder->nodeUser);
        $table->addCell(1000, $cellStyle)->addText($this->lang->deptorder->consumed);
        $table->addCell(1000, $cellStyle)->addText($this->lang->deptorder->before);
        $table->addCell(1000, $cellStyle)->addText($this->lang->deptorder->after);

        foreach($deptorder->consumed as $c)
        {
            $table->addRow();
            $table->addCell(1000, $cellStyle)->addText(zget($users, $c->createdBy, ''));
            $table->addCell(1000, $cellStyle)->addText($c->consumed . '' . $this->lang->hour);
            $table->addCell(1000, $cellStyle)->addText(zget($this->lang->deptorder->statusList, $c->before, '-'));
            $table->addCell(1000, $cellStyle)->addText(zget($this->lang->deptorder->statusList, $c->after, '-'));
        }

        $this->loadModel('file')->export2Word($this->lang->deptorder->exportTitle . $deptorder->code, $phpWord);
    }

    public function copy($deptorderID = 0)
    {
        if($_POST)
        {
            $deptorderID = $this->deptorder->create();

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $this->loadModel('action')->create('deptorder', $deptorderID, 'created', $this->post->comment);
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = inlink('browse');

            $this->send($response);
        }
        $this->view->title   = $this->lang->deptorder->copy;
        $this->view->users   = $this->loadModel('user')->getPairs('noclosed,noletter');
        $deptorder = $this->deptorder->getByID($deptorderID);
        $deptorder->overDate = $deptorder->overDate != '0000-00-00' ? $deptorder->overDate : '';
        $deptorder->startDate = $deptorder->startDate != '0000-00-00' ? $deptorder->startDate : '';
        $deptorder->exceptDoneDate = $deptorder->exceptDoneDate != '0000-00-00' ? $deptorder->exceptDoneDate : '';
        $this->view->deptorder = $deptorder;
        $this->view->childTypeList = $this->deptorder->getChildTypeList($this->view->deptorder->type);
        $this->view->apps  = array('' => '') + $this->loadModel('application')->getPairs();
        //$this->app->loadLang('opinion');
        $this->app->loadLang('application');
        $this->display();
    }

    /**
     * @param string $type
     * 获取子类型
     */
    public function ajaxGetChildTypeList($type = '')
    {
        $list = $this->deptorder->getChildTypeList($type);
        die(html::select('subtype', $list, '', 'class=form-control'));
    }

    /**
     * @param int $deptorderID
     * @param int $consumedID
     */
    public function statusedit($deptorderID = 0, $consumedID = 0)
    {
        if($_POST)
        {
            $changes = $this->deptorder->statusedit($deptorderID, $consumedID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $actionID = $this->loadModel('action')->create('deptorder', $deptorderID, 'statusedit', $this->post->comment);
            $this->action->logHistory($actionID, $changes);

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $this->view->title    = $this->lang->deptorder->statusedit;
        $this->view->deptorder  = $this->deptorder->getByID($deptorderID);
        $this->view->users    = $this->loadModel('user')->getPairs('noclosed,noletter');

        $consumed = $this->deptorder->getConsumedByID($consumedID);
        $this->view->consumed = $consumed;
        $this->display();
    }

    /**
     * @param string $app
     * 获取子类型
     */
    public function ajaxGetUnion($app)
    {
        //$this->app->loadLang('opinion');
        $selected = '';
        if($app){
            $list = $this->loadModel('application')->getByID($app);
            $selected = $list->fromUnit;
        }
        die(html::select('union', $this->lang->deptorder->unionList, $selected, 'class=form-control chosen'));
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
     * 追加进展跟踪
     * @param $secondorderID
     * @return void
     */
    public function editSpecialQA($deptorderID = 0)
    {
        $deptorder = $this->loadModel('deptorder')->getByID($deptorderID);
        if($_POST) {
            $deptorderNew = fixer::input('post')
                ->remove('uid')
                //->stripTags($this->config->secondorder->editor->editspecial['id'], $this->config->allowedTags)
                ->get();

            $this->dao->update(TABLE_DEPTORDER)
               ->data($deptorderNew)
                ->where('id')->eq($deptorderID)
                ->exec();

            $changes = common::createChanges($deptorder, $deptorderNew);

            if(dao::isError()) {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes) {
                $actionID = $this->loadModel('action')->create('deptorder', $deptorderID, 'editspecialed');
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $this->view->deptorder = $deptorder;
        $this->display();
    }

    /**
     * 为了加进展跟踪信息权限
     */
    public function getProgressInfo()
    {
        return true;
    }

    /**
     * 导入进展跟踪相关信息
     * @return void
     * @throws PHPExcel_Exception
     * @throws PHPExcel_Reader_Exception
     */
    public function importByQA()
    {
        if($_FILES)
        {
            $this->loadModel('common');

            $res = $this->common->importProgress('deptorder');

            die(json_encode($res));
        }

        $this->display();
    }
}
