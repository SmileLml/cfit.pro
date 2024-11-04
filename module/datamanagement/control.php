<?php
class datamanagement extends control
{
    /**
     * Project: chengfangjinke
     * Desc: 列表
     * liuyuhan
     */
    public function browse($browseType = 'all', $param = 0, $orderBy = 'createdDate_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $browseType = strtolower($browseType);
        $queryID = ($browseType == 'bysearch')  ? (int)$param : 0;
        $actionURL = $this->createLink('datamanagement', 'browse', "browseType=bySearch&param=myQueryID");
        $this->datamanagement->buildSearchForm($queryID, $actionURL);


        /* 设置详情页面返回的url连接。*/
        $this->session->set('datamanagementList', $this->app->getURI(true));
        $this->session->set('common_back_url', $this->app->getURI(true),'backlog');
        $this->session->set('datamanagementHistory', $this->app->getURI(true));

        /* 构建pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);
        $datas = $this->datamanagement->getList($browseType, $queryID, $orderBy, $pager);

        $this->view->title    = $this->lang->datamanagement->datause;
        $this->view->orderBy    = $orderBy;
        $this->view->pager      = $pager;
        $this->view->param      = $param;
        $this->view->browseType = $browseType;
        $this->view->datas      = $datas;
        $this->view->users      = $this->loadModel('user')->getPairs('noletter|noclosed');
        $this->display();
    }

    public function syncTest($infoId,$infoType){
        $this->loadModel('datamanagement')->syncData($infoId, $infoType);
    }

    public function syncCli(){
        $this->loadModel('datamanagement')->timeRemind();
    }

    /**
     * 数据获取详情页
     * @param $id
     * @return void
     */
    public function view($id){
        $datause = $this->datamanagement->getByID($id);
        if($datause->source == 'info'){
            $infoData = $this->loadModel('info')->getByID($datause->infoId);
        }else if($datause->source == 'infoqz') {
            $infoData = $this->loadModel('infoqz')->getByID($datause->infoId);
        }

        if($datause->isDeadline == 1){
            $datause->useDeadline = '长期';
        }
        if($datause->isDeadline == 1 and $datause->delayDeadline){
            $datause->delayDeadline = '长期';
        }
        $users = $this->loadModel('user')->getPairs('noletter|noclosed');

        //备案记录
        $filingNoticeNodes = new stdClass();
        $reviewedNode = new stdClass();
        $reviewedNode->dealUser = '';
        $gainedNode = new stdClass();
        $gainedNode->dealUser = '';
        $destroyedNode = new stdClass();
        $destroyedNode->dealUser = '';
        $toreadList = $this->dao->select('*')->from(TABLE_TOREAD)->where('deleted')->ne('1')->andWhere('objectType')->eq('datamanagement')
            ->andWhere('objectId')->eq($id)->fetchAll('id');
        if(!empty($toreadList)){
            foreach ($toreadList as $toread){
                if($toread->messageType == 'reviewed'){
                    $reviewedNode->dealUser = trim($reviewedNode->dealUser.','.zget($users,$toread->dealUser),',');
                    if($toread->status == 'readed'){
                        if(empty($reviewedNode->dealDate)){
                            $reviewedNode->dealDate = $toread->dealDate;
                            $reviewedNode->dealComment = $toread->comment;
                            $reviewedNode->dealResult = zget($this->lang->datamanagement->todoStatusList,$toread->status)."（".zget($users,$toread->dealUser)."）";
                        }else{
                            if(strtotime($toread->dealDate) < strtotime($reviewedNode->dealDate)){
                                $reviewedNode->dealDate = $toread->dealDate;
                                $reviewedNode->dealComment = $toread->comment;
                                $reviewedNode->dealResult = zget($this->lang->datamanagement->todoStatusList,$toread->status)."（".zget($users,$toread->dealUser)."）";
                            }
                        }
                    }
                }
                if($toread->messageType == 'gained'){
                    $gainedNode->dealUser = trim($gainedNode->dealUser.','.zget($users,$toread->dealUser),',');
                    if($toread->status == 'readed'){
                        if(empty($gainedNode->dealDate)){
                            $gainedNode->dealDate = $toread->dealDate;
                            $gainedNode->dealComment = $toread->comment;
                            $gainedNode->dealResult = zget($this->lang->datamanagement->todoStatusList,$toread->status)."（".zget($users,$toread->dealUser)."）";
                        }else{
                            if(strtotime($toread->dealDate) < strtotime($gainedNode->dealDate)){
                                $gainedNode->dealDate = $toread->dealDate;
                                $gainedNode->dealComment = $toread->comment;
                                $gainedNode->dealResult = zget($this->lang->datamanagement->todoStatusList,$toread->status)."（".zget($users,$toread->dealUser)."）";
                            }
                        }
                    }
                }
                if($toread->messageType == 'destroyed'){
                    $destroyedNode->dealUser = trim($destroyedNode->dealUser.','.zget($users,$toread->dealUser),',');
                    if($toread->status == 'readed'){
                        if(empty($destroyedNode->dealDate)){
                            $destroyedNode->dealDate = $toread->dealDate;
                            $destroyedNode->dealComment = $toread->comment;
                            $destroyedNode->dealResult = zget($this->lang->datamanagement->todoStatusList,$toread->status)."（".zget($users,$toread->dealUser)."）";
                        }else{
                            if(strtotime($toread->dealDate) < strtotime($destroyedNode->dealDate)){
                                $destroyedNode->dealDate = $toread->dealDate;
                                $destroyedNode->dealComment = $toread->comment;
                                $destroyedNode->dealResult = zget($this->lang->datamanagement->todoStatusList,$toread->status)."（".zget($users,$toread->dealUser)."）";
                            }
                        }
                    }
                }
            }
            $reviewedNode->dealUser = trim($reviewedNode->dealUser, ",");
            $gainedNode->dealUser = trim($gainedNode->dealUser, ",");
            $destroyedNode->dealUser = trim($destroyedNode->dealUser, ",");
            $filingNoticeNodes->reviewedNode = $reviewedNode;
            $filingNoticeNodes->gainedNode = $gainedNode;
            $filingNoticeNodes->destroyedNode = $destroyedNode;
        }



        $this->view->title    = $this->lang->datamanagement->view;
        $this->view->users    = $users;
        $this->view->actions  = $this->loadModel('action')->getList('datamanagement', $id);
        $this->view->datamanagement  = $datause;
        $this->view->infoData  = $infoData;
        $this->view->filingNoticeNodes    = $filingNoticeNodes;
        //数据使用延期记录
        $this->view->delayReview    = $this->datamanagement->getDelayNodes($id);

        //数据销毁记录
        $this->view->destroyReview    = $this->datamanagement->getDestroyNodes($id);

        $this->display();
    }

    /**
     * 导出详情页数据
     * shixuyang
     * @param $infoID
     * @return void
     */
    public function exportWord($id)
    {
        $datause  = $this->datamanagement->getById($id);
        if($datause->source == 'info'){
            $infoData = $this->loadModel('info')->getByID($datause->infoId);
        }else if($datause->source == 'infoqz') {
            $infoData = $this->loadModel('infoqz')->getByID($datause->infoId);
        }
        $users = $this->loadModel('user')->getPairs('noletter|noclosed');

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

        $section->addTitle($this->lang->datamanagement->exportDatause, 1);
        $section->addText($this->lang->datamanagement->code . ' ' . $datause->code, 'font_default', 'align_right');

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
        $table->addCell(1000, $cellStyle)->addText($this->lang->datamanagement->type);
        $table->addCell(1000, $cellStyle)->addText(zget($this->lang->datamanagement->typeList, $datause->type, ''));
        $table->addCell(1000, $cellStyle)->addText($this->lang->datamanagement->isJk);
        $table->addCell(1000, $cellStyle)->addText(zget($this->lang->datamanagement->isJkList, $datause->isJk, ''));

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->datamanagement->desensitizeType);
        $table->addCell(1000, $cellStyle)->addText(zget($this->lang->datamanagement->desensitizeTypeList, $datause->desensitizeType, ''));
        $table->addCell(1000, $cellStyle)->addText($this->lang->datamanagement->deadline);
        $table->addCell(1000, $cellStyle)->addText($datause->useDeadline);


        if($datause->source == 'info'){
            $table->addRow();
            $table->addCell(1000, $cellStyle)->addText($this->lang->datamanagement->source);
            $table->addCell(1000, $cellStyle)->addText(zget($this->lang->datamanagement->sourceList, $datause->source, ''));
            $table->addCell(1000, $cellStyle)->addText($this->lang->datamanagement->infoCode);
            $table->addCell(1000, $cellStyle)->addText($infoData->code);
        }else{
            $table->addRow();
            $table->addCell(1000, $cellStyle)->addText($this->lang->datamanagement->isDesensitize);
            $table->addCell(1000, $cellStyle)->addText(zget($this->lang->datamanagement->isDesensitizeList, $datause->isDesensitize, ''));
            $table->addCell(1000, $cellStyle)->addText($this->lang->datamanagement->infoCode);
            $table->addCell(1000, $cellStyle)->addText($infoData->code);
            $table->addRow();
            $table->addCell(1000, $cellStyle)->addText($this->lang->datamanagement->source);
            $table->addCell(3000, array('gridSpan' => 3))->addText(zget($this->lang->datamanagement->sourceList, $datause->source, ''));
        }


        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->datamanagement->createdBy);
        $table->addCell(1000, $cellStyle)->addText(zget($users, $datause->createdBy, ''));
        $table->addCell(1000, $cellStyle)->addText($this->lang->datamanagement->createdDate);
        $table->addCell(1000, $cellStyle)->addText($datause->createdDate);

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->datamanagement->delayedBy);
        $table->addCell(1000, $cellStyle)->addText(zget($users, $datause->delayedBy, ''));
        $table->addCell(1000, $cellStyle)->addText($this->lang->datamanagement->delayDeadline);
        $table->addCell(1000, $cellStyle)->addText($datause->delayDeadline);

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->datamanagement->destroyedBy);
        $table->addCell(1000, $cellStyle)->addText(zget($users, $datause->destroyedBy, ''));
        $table->addCell(1000, $cellStyle)->addText($this->lang->datamanagement->destroyedDate);
        $table->addCell(1000, $cellStyle)->addText($datause->destroyedDate);

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->datamanagement->reviewedBy);
        $table->addCell(1000, $cellStyle)->addText(zget($users, $datause->reviewedBy, ''));
        $table->addCell(1000, $cellStyle)->addText($this->lang->datamanagement->reviewedDate);
        $table->addCell(1000, $cellStyle)->addText($datause->reviewedDate);

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->datamanagement->actualEndTime);
        $table->addCell(3000, array('gridSpan' => 3))->addText($datause->actualEndTime);

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->datamanagement->desc);
        $table->addCell(3000, array('gridSpan' => 3))->addText(strip_tags($datause->desc));

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->datamanagement->reason);
        $table->addCell(3000, array('gridSpan' => 3))->addText(strip_tags($datause->reason));



        $this->loadModel('file')->export2Word($this->lang->datamanagement->exportDatause . $datause->code, $phpWord);
    }

    /**
     * Project: chengfangjinke
     * Desc: 导出列表页数据 Excel
     * liuyuhan
     */
    public function export($action, $orderBy = 'id_desc', $browseType = 'all')
    {
        /* format the fields of every data in order to export data. */
        if($_POST)
        {
            $this->loadModel('file');
            $datamanagementLang   = $this->lang->datamanagement;
            $datamanagementConfig = $this->config->datamanagement;

            /* Create field lists. */
            $fields = $this->post->exportFields ? $this->post->exportFields : explode(',', $datamanagementConfig->list->exportFields);
            foreach($fields as $key => $fieldName)
            {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = isset($datamanagementLang->$fieldName) ? $datamanagementLang->$fieldName : $fieldName;
                unset($fields[$key]);
            }

            /* Get datas. */
            $datas = array();
            $stmt = $this->dao->query($this->session->datamanagementExportQuery . ($this->post->exportType == 'selected' ? " AND id IN({$this->cookie->checkedItem})" : '') . " ORDER BY " . strtr($orderBy, '_', ' '));
            $datas = $stmt->fetchAll();
//            if($this->session->datamanagementOnlyCondition)
//            {
//                $datas = $this->dao->select('*')->from(TABLE_DATAUSE)->where($this->session->datamanagementOnlyCondition)
//                    ->andWhere('deleted')->eq('0')
//                    ->beginIF($this->post->exportType == 'selected')->andWhere('id')->in($this->cookie->checkedItem)->fi()
//                    ->orderBy($orderBy)->fetchAll('id');
//            }
//            else
//            {
//                $stmt = $this->dbh->query($this->session->datamanagementExportQuery . ($this->post->exportType == 'selected' ? " AND id IN({$this->cookie->checkedItem})" : '') . " ORDER BY " . strtr($orderBy, '_', ' '));
//                while($row = $stmt->fetch()) $datas[$row->id] = $row;
//            }
            /* Get users, products and executions. */
            $users = $this->loadModel('user')->getPairs('noletter');

            foreach($datas as $data)
            {
//                //获取清总/金信数据获取单单号
//                $infoCode = '';
//                if ((!is_null($data->source))&&($data->source == 'info')){
//                    $infoCode = $this->dao->select('code')->from(TABLE_INFO)
//                        ->where('id')->eq($data->infoId)->fetch();
//                }elseif((!is_null($data->source))&&($data->source == 'infoqz')){
//                    $infoCode = $this->dao->select('code')->from(TABLE_INFO_QZ)
//                        ->where('id')->eq($data->infoId)->fetch();
//                }
//                $data->infoCode= empty($infoCode->code)?$infoCode:$infoCode->code;

                $data->type   = $datamanagementLang->typeList[$data->type];
                $data->desensitizeType    = $datamanagementLang->desensitizeTypeList[$data->desensitizeType];
                $data->isDesensitize     = $data->source=='info' ? null : $datamanagementLang->isDesensitizeList[$data->isDesensitize];
                $data->source     = $datamanagementLang->sourceList[$data->source];
                $data->isJk     = $datamanagementLang->isJkList[$data->isJk];

                $data->createdBy   = $users[$data->createdBy];
                $data->destroyedBy   = $users[$data->destroyedBy];
                $data->reviewedBy   = $users[$data->reviewedBy];
                $data->destroyedDate  = empty($data->destroyedDate)? '' : substr($data->destroyedDate,0, 10);

                //是否长期使用
                if($data->isDeadline == $datamanagementLang->longTermUseFlag){
                    $data->useDeadline =$datamanagementLang->longTerm;
                }else{
                    $data->useDeadline  = substr($data->useDeadline,0, 10);
                }
                $data->reason = strip_tags(htmlspecialchars_decode($data->reason));

            }
            $this->post->set('fields', $fields);
            $this->post->set('rows', $datas);
            $this->post->set('kind', 'datamanagement');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        $this->view->fileName        = $this->lang->datamanagement->exportExcel;
        $this->view->allExportFields = $this->config->datamanagement->list->exportFields;
        $this->view->customExport    = true;
        $this->display();
    }

    /**
     * 数据销毁执行
     * shixuyang
     * @access public
     * @return void
     */
    public function destroyexecution($datamanagementID)
    {
        $datamanage = $this->loadModel('datamanagement')->getById($datamanagementID);
        if($_POST)
        {
            if($datamanage->status == 'destroying'){
                $changes = $this->loadModel('datamanagement')->destroyexecution($datamanagementID);
            }else if($datamanage->status == 'destroyreviewing'){
                $changes = $this->loadModel('datamanagement')->destroyreview($datamanagementID);
            }

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            //$this->action->logHistory($actionID, $changes);

            $response['result']  = 'success';
            $response['message'] = $this->lang->datamanagement->submitsuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }
        $this->view->title       = $this->lang->datamanagement->destroyexecution;
        $this->view->datamanagement = $datamanage;
        $this->display();
    }

    /**
     * 数据销毁复核
     * shixuyang
     * @access public
     * @return void
     */
    public function destroyreview($datamanagementID)
    {
        $datamanage = $this->loadModel('datamanagement')->getById($datamanagementID);
        if($_POST)
        {
            $changes = $this->loadModel('datamanagement')->destroyreview($datamanagementID);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            //$this->action->logHistory($actionID, $changes);

            $response['result']  = 'success';
            $response['message'] = $this->lang->datamanagement->submitsuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }
        $this->view->title       = $this->lang->datamanagement->destroyreview;
        $this->view->datamanagement = $datamanage;
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Desc:数据使用延期申请
     * liuyuhan
     */
    public function delay($datamanagementID){
        $datamanagement = $this->datamanagement->getById($datamanagementID);
        if($_POST)
        {
            $changes = $this->datamanagement->delay($datamanagement);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            if($changes)
            {
                $actionID = $this->loadModel('action')->create('datamanagement', $datamanagementID, 'delay', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }
            $response['result']  = 'success';
            $response['message'] = $this->lang->datamanagement->submitsuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }
        $this->view->title       = $this->lang->datamanagement->delay;
        $this->view->datamanagement = $datamanagement;
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Desc:审批
     * liuyuhan
     */
    public function review($datamanagementID, $changeVersion = 1, $reviewStage = 0){
        if($_POST)
        {
            if($reviewStage == 1) {
                //数据使用延期审批
                $this->datamanagement->delayreview($datamanagementID);
            }else if ($reviewStage == 2){
                //数据销毁延期审批
                $this->datamanagement->destroyreviewed($datamanagementID);
            }

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $response['result']  = 'success';
            $response['message'] =  $this->lang->datamanagement->submitsuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }

        $datamanagement = $this->datamanagement->getById($datamanagementID);
        $userInfoList = $this->loadModel('dept')->getUsers('inside',$this->app->user->dept);
        $this->view->title       = $this->lang->datamanagement->review;
        $this->view->datamanagement = $datamanagement;
        $this->view->extraObj = $this->datamanagement->getReviewerExtraInfos($datamanagementID, $datamanagement->reviewStage, $datamanagement->changeVersion);
        $this->view->users    = array('' => '') + array_column($userInfoList,'realname','account');
        $this->display();
    }

    /**
     * 备案消息
     * shixuyang
     * @access public
     * @return void
     */
    public function readmessage($datamanagementID)
    {
        $datamanage = $this->loadModel('datamanagement')->getById($datamanagementID);
        if($_POST)
        {
            $changes = $this->loadModel('datamanagement')->readmessage($datamanagementID);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            //$this->action->logHistory($actionID, $changes);

            $response['result']  = 'success';
            $response['message'] = $this->lang->datamanagement->submitsuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }
        $this->view->title       = $this->lang->datamanagement->destroyreview;
        $this->view->datamanagement = $datamanage;
        if(!empty($datamanage->toreadreviewed) and !empty($datamanage->toreadgained) and !empty($datamanage->toreaddestroyed)){
            $this->view->filingNoticeList = $this->lang->datamanagement->filingNoticeList;
        }else{
            if(empty($datamanage->toreadreviewed)){
                unset($this->lang->datamanagement->filingNoticeList['reviewed']);
            }
            if(empty($datamanage->toreadgained)){
                unset($this->lang->datamanagement->filingNoticeList['gained']);
            }
            if(empty($datamanage->toreaddestroyed)){
                unset($this->lang->datamanagement->filingNoticeList['destroyed']);
            }
            $this->view->filingNoticeList = $this->lang->datamanagement->filingNoticeList;
        }
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Desc:数据使用销毁申请
     * liuyuhan
     */
    public function destroy($datamanagementID){

        if($_POST)
        {
            $changes = $this->datamanagement->destroy($datamanagementID);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes)
            {
                $actionID = $this->loadModel('action')->create('datamanagement', $datamanagementID, 'destroy', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }
            $response['result']  = 'success';
            $response['message'] = $this->lang->datamanagement->submitsuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }
        $datamanagement = $this->datamanagement->getById($datamanagementID);
        $this->view->title       = $this->lang->datamanagement->destroy;
        $this->view->datamanagement = $datamanagement;
        $this->display();
    }
}