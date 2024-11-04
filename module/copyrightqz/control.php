<?php
class copyrightqz extends control
{
    /**
     * Project: chengfangjinke
     * Desc: 列表展示
     * liuyuhan
     */
    public function browse($browseType = 'all', $param = 0, $orderBy = 'createdTime_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $browseType = strtolower($browseType);
        //搜索框的值
        $depts = $this->loadModel('dept')->getOptionMenu();
        $depts[0] = '';
        $this->config->copyrightqz->search['params']['applicantDept']['values'] = $depts;
        $queryID = ($browseType == 'bysearch')  ? (int)$param : 0;
        $actionURL = $this->createLink('copyrightqz', 'browse', "browseType=bySearch&param=myQueryID");
        $this->copyrightqz->buildSearchForm($queryID, $actionURL);


        /* 设置详情页面返回的url连接。*/
        $this->session->set('copyrightqzList', $this->app->getURI(true));
        $this->session->set('common_back_url', $this->app->getURI(true),'backlog');
        $this->session->set('copyrightqzHistory', $this->app->getURI(true));

        /* 构建pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);
        $datas = $this->copyrightqz->getList($browseType, $queryID, $orderBy, $pager);

        $this->view->title    = $this->lang->copyrightqz->common;
        $this->view->orderBy    = $orderBy;
        $this->view->pager      = $pager;
        $this->view->param      = $param;
        $this->view->browseType = $browseType;
        $this->view->datas      = $datas;
        $this->view->users      = $this->loadModel('user')->getPairs('noletter|noclosed');
        $this->view->depts      = $depts;
        $this->display();
    }

    public function create($isSave=0){
        if($_POST)
        {
            $copyrightqzID = $this->copyrightqz->create($isSave=='1');

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $this->loadModel('action')->create('copyrightqz', $copyrightqzID, $isSave=='1'?'created':'createdsubmit');
            $response['result']  = 'success';
            $response['message'] = $isSave=='1'?$this->lang->saveSuccess:'提交成功';
            $response['locate']  = inlink('browse');

            $this->send($response);
        }
        $usedEmisCodeList = $this->dao->select('emisCode')->from(TABLE_COPYRIGHTQZ)->where('deleted')->ne('1')->fetchall('emisCode');
        $this->view->title = $this->lang->copyrightqz->create;
        $this->view->emisCodeListWithoutUse = array(''=>'') + $this->dao->select('emisRegisterNumber')->from(TABLE_PRODUCTENROLL)->where('emisRegisterNumber')->notin(array_keys($usedEmisCodeList))->andWhere('deleted')->ne('1')->fetchPairs('emisRegisterNumber','emisRegisterNumber');
        $this->view->emisCodeList = array(''=>'') + $this->dao->select('emisRegisterNumber')->from(TABLE_PRODUCTENROLL)->where('emisRegisterNumber')->ne('')->fetchPairs('emisRegisterNumber','emisRegisterNumber');
        $this->view->users = $this->loadModel('user')->getPairs('noclosed');
        $this->view->creator = $this->app->user->account;
        $this->view->docDownload = $this->loadModel('file')->getByObject('copyrightqz', 0);
        //审核节点下的审核人列表
        $reviewers            = $this->copyrightqz->getReviewers();
        //审核节点下的审核人列表
        $reviewerAccounts     = $this->loadModel('review')->getReviewerAccounts($reviewers);
        $this->view->reviewers            = $reviewers;
        $this->view->reviewerAccounts     = $reviewerAccounts;

        $this->display();
    }

    public function edit ($id = 0,$isSave=0){
        // $this->copyrightqz->pushcopyrightqz($id);
        if($_POST)
        {
            $changes = $this->copyrightqz->update($id,$isSave=='1');

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $actionID = $this->loadModel('action')->create('copyrightqz', $id, $isSave=='1'?'edited':'editedsubmit');
            $this->action->logHistory($actionID, $changes);

            $response['result']  = 'success';
            $response['message'] = $isSave=='1'?$this->lang->saveSuccess:'提交成功';
            $response['locate']  = inlink('browse');

            $this->send($response);
        }
        $copyrightqz = $this->copyrightqz->getByID($id);
        $this->view->copyrightqz = $copyrightqz;
        $this->view->docDownload = $this->loadModel('file')->getByObject('copyrightqz', 0);
        $this->view->title = $this->lang->copyrightqz->edit;
        $emisCodeList = $this->dao->select('emisCode')->from(TABLE_COPYRIGHTQZ)->where('emisCode')->ne($this->view->copyrightqz->emisCode)->andwhere('deleted')->ne('1')->fetchall('emisCode');
        $this->view->emisCodeListWithoutUse = array(''=>'') + $this->dao->select('emisRegisterNumber')->from(TABLE_PRODUCTENROLL)->where('emisRegisterNumber')->notin(array_keys($emisCodeList))->andWhere('deleted')->ne('1')->fetchPairs('emisRegisterNumber','emisRegisterNumber');
        $this->view->emisCodeList = array(''=>'') + $this->dao->select('emisRegisterNumber,emisRegisterNumber')->from(TABLE_PRODUCTENROLL)->where('emisRegisterNumber')->ne('')->fetchPairs('emisRegisterNumber','emisRegisterNumber');
        $this->view->users = $this->loadModel('user')->getPairs('noclosed');
        $this->view->creator = $this->app->user->account;
        //审核节点下的审核人列表
        $reviewers            = $this->copyrightqz->getReviewers();
        //审核节点下的审核人列表
        $reviewerAccounts     = $this->loadModel('review')->getReviewerAccounts($reviewers);
        // 当前选择
        $nodesReviewers = $this->loadModel('review')->getAllNodeReviewers('copyrightqz', $id, $copyrightqz->changeVersion);
        $this->view->reviewers            = $reviewers;
        $this->view->reviewerAccounts     = $reviewerAccounts;
        $this->view->nodesReviewers = $nodesReviewers;

        $this->display();
    }

    public function updatedoc(){
        if($_POST)
        {
            $this->copyrightqz->updatedoc();

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = inlink('browse');

            $this->send($response);
        }

        $this->view->docDownload = $this->loadModel('file')->getByObject('copyrightqz', 0);
        $this->display();
    }

    public function handlepush($id){
        $res = $this->copyrightqz->pushcopyrightqz($id);
        if($res){
            $this->view->item = '推送成功';
        }else{
            $this->view->item = '推送失败';
        }
        $this->display();
    }

    public function delete($id=0)
    {
        if($_POST)
        {
            $this->dao->update(TABLE_COPYRIGHTQZ)->set('deleted')->eq('1')->where('id')->eq($id)->exec();
            $actionID = $this->loadModel('action')->create('copyrightqz', $id, 'deleted', $this->post->comment);
            if(isonlybody()) echo js::closeModal('parent.parent', 'this');
            die(js::locate(inlink('browse')));
        }

        $item = $this->copyrightqz->getByID($id);
        $this->view->actions = $this->loadModel('action')->getList('copyrightqz', $id);
        $this->view->item = $item;
        $this->view->users   = $this->loadModel('user')->getPairs();
        $this->display();
    }

    public function reject($id = 0)
    {
        if($_POST)
        {
            $this->copyrightqz->reject($id);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $actionID = $this->loadModel('action')->create('copyrightqz', $id, 'reject', $this->post->comment);
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }
        //数据信息
        $data = $this->copyrightqz->getByID($id);
        $this->view->depts = $this->loadModel('dept')->getOptionMenu();
        $this->view->title = $this->lang->copyrightqz->reject;
        $this->view->users = $this->loadModel('user')->getPairs('noclosed');
        $this->view->item  = $data;
        $this->view->res   = ($data->status == 'feedbackFailed' or $data->status == 'synFailed');
        $this->display();
    }

    public function ajaxGetProduct ($emisRegisterNumber){
        $data = $this->dao->select('dynacommCn,versionNum')->from(TABLE_PRODUCTENROLL)->where('emisRegisterNumber')->eq($emisRegisterNumber)->fetch();
        echo json_encode($data);
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
            $copyrightqzLang   = $this->lang->copyrightqz;
            $copyrightqzConfig = $this->config->copyrightqz;

            /* Create field lists. */
            $fields = $this->post->exportFields ? $this->post->exportFields : explode(',', $copyrightqzConfig->list->exportFields);
            foreach($fields as $key => $fieldName)
            {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = isset($copyrightqzLang->$fieldName) ? $copyrightqzLang->$fieldName : $fieldName;
                unset($fields[$key]);
            }

            /* Get datas. */
            $datas = array();
            $stmt = $this->dao->query($this->session->copyrightqzExportQuery . ($this->post->exportType == 'selected' ? " AND id IN({$this->cookie->checkedItem})" : '') . " ORDER BY " . strtr($orderBy, '_', ' '));
            $datas = $stmt->fetchAll();
//            if($this->session->copyrightqzOnlyCondition)
//            {
//                $datas = $this->dao->select('*')->from(TABLE_COPYRIGHTQZ)->where($this->session->copyrightqzQueryCondition)
//                    ->andWhere('deleted')->eq('0')
//                    ->beginIF($this->post->exportType == 'selected')->andWhere('id')->in($this->cookie->checkedItem)->fi()
//                    ->orderBy($orderBy)->fetchAll('id');
//            }
//            else
//            {
//                $stmt = $this->dao->query($this->session->copyrightqzExportQuery . ($this->post->exportType == 'selected' ? " AND id IN({$this->cookie->checkedItem})" : '') . " ORDER BY " . strtr($orderBy, '_', ' '));
//                while($row = $stmt->fetch) $datas[$row->id] = $row;
//            }
            /* Get users, products and executions. */
            $users = $this->loadModel('user')->getPairs('noletter');
            $depts = $this->loadModel('dept')->getOptionMenu();
            foreach($datas as $data)
            {
                if(helper::isZeroDate($data->devFinishedTime)) $data->devFinishedTime=null;
                if(helper::isZeroDate($data->firstPublicTime)) $data->firstPublicTime=null;
                if(helper::isZeroDate($data->outsideReviewTime)) $data->outsideReviewTime=null;
                if(helper::isZeroDate($data->synDate)) $data->synDate=null;
                //单选
                $data->applicant   = $users[$data->applicant];
                //迭代二十六-删除部门第一个'/'
                $data->applicantDept   = ltrim($depts[$data->applicantDept], '/');
                $data->descType   = $copyrightqzLang->descTypeList[$data->descType];
                $data->publishStatus   = $copyrightqzLang->publishStatusList[$data->publishStatus];
                $data->firstPublicCountry   = $copyrightqzLang->firstPublicCountryList[$data->firstPublicCountry];
                $data->devMode   = $copyrightqzLang->devModeList[$data->devMode];
                $data->rightObtainMethod   = $copyrightqzLang->rightObtainMethodList[$data->rightObtainMethod];
                $data->isOriRegisNumChanged   = $copyrightqzLang->isOriRegisNumChangedList[$data->isOriRegisNumChanged];
                $data->isRegister   = $copyrightqzLang->isRegisterList[$data->isRegister];
                $data->rightRange   = $copyrightqzLang->rightRangeList[$data->rightRange];
                $data->system   = $copyrightqzLang->systemList[$data->system];
                //复选框
                $data->identityMaterial   =  $this->copyrightqz->getItemsValue($data->identityMaterial, $copyrightqzLang->identityMaterialList);
                $data->generalDeposit   =$this->copyrightqz->getItemsValue($data->generalDeposit, $copyrightqzLang->generalDepositList);
                $data->exceptionalDeposit   = $this->copyrightqz->getItemsValue($data->exceptionalDeposit, $copyrightqzLang->exceptionalDepositList);
                $data->devLanguage   = $this->copyrightqz->getItemsValue($data->devLanguage, $copyrightqzLang->devLanguageList);
                $data->softwareType   = $this->copyrightqz->getItemsValue($data->softwareType, $copyrightqzLang->softwareTypeList);
                $data->techFeatureType   = $this->copyrightqz->getItemsValue($data->techFeatureType, $copyrightqzLang->techFeatureTypeList);
                //处理时间
                $data->devFinishedTime = substr($data->devFinishedTime,0, 10);
                $data->firstPublicTime = substr($data->firstPublicTime,0, 10);
                //判断关联产品登记是否已删除，若删除，则不展示，显示已删除
                $productenroll = $this->dao->select('deleted')->from(TABLE_PRODUCTENROLL)->where('id')->eq($data->productenrollId)->fetch();
                $data->productenrollCode = $productenroll->deleted=='0'? $data->productenrollCode : $this->lang->copyrightqz->productenrollDeleted;
            }
            $this->post->set('fields', $fields);
            $this->post->set('rows', $datas);
            $this->post->set('kind', 'copyrightqz');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        $this->view->fileName        =  '清总知识产权列表';
        $this->view->allExportFields = $this->config->copyrightqz->list->exportFields;
        $this->view->customExport    = true;
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Desc: 详情页面
     * liuyuhan
     */
    public function view($copyrightqzId = 0)
    {
        $copyrightqz = $this->copyrightqz->getByID($copyrightqzId);
        if ($copyrightqz->deleted){
            $response['result']  = 'success';
            $response['locate']  = inlink('browse');
            $this->send($response);
        }else {
            $copyrightqz = $this->loadModel('file')->replaceImgURL($copyrightqz, '');
//            //判断状态流转中是否前后状态一致，若操作后状态一样，则仅保留【操作时间】最近的那一次记录
//            $consumedFix = $copyrightqz->consumed;
//            $temp = '';
//            for ($i = count($consumedFix) - 1; $i >= 0; $i--) {
//                if (($consumedFix[$i]->before == $consumedFix[$i]->after) && ($consumedFix[$i]->after == $temp)) {
//                    unset($consumedFix[$i]);
//                } else {
//                    $temp = $consumedFix[$i]->after;
//                }
//            }

            /* 查询需求条目及其相关的信息。*/
            $this->view->users = $this->loadmodel('user')->getPairs('noletter|noclosed');
            $this->view->actions = $this->loadmodel('action')->getList('copyrightqz', $copyrightqzId);
            $this->view->copyrightqz = $copyrightqz;
            $this->view->consumed = $copyrightqz->consumed;
            $this->view->depts = $this->loadModel('dept')->getOptionMenu();
            $this->view->nodes = $this->loadModel('review')->getNodes('copyrightqz', $copyrightqzId, $copyrightqz->changeVersion);
            $this->view->projectPlanList = $this->loadModel('project')->getPairs();
            //复选框
            $this->view->identityMaterial = $this->copyrightqz->getItemsValue($copyrightqz->identityMaterial, $this->lang->copyrightqz->identityMaterialList);
            $this->view->generalDeposit = $this->copyrightqz->getItemsValue($copyrightqz->generalDeposit, $this->lang->copyrightqz->generalDepositList);
            $this->view->exceptionalDeposit = $this->copyrightqz->getItemsValue($copyrightqz->exceptionalDeposit, $this->lang->copyrightqz->exceptionalDepositList);
            $this->view->devLanguage = $this->copyrightqz->getItemsValue($copyrightqz->devLanguage, $this->lang->copyrightqz->devLanguageList);
            $this->view->softwareType = $this->copyrightqz->getItemsValue($copyrightqz->softwareType, $this->lang->copyrightqz->softwareTypeList);
            $this->view->techFeatureType = $this->copyrightqz->getItemsValue($copyrightqz->techFeatureType, $this->lang->copyrightqz->techFeatureTypeList);
        }
        $this->view->title = $this->lang->copyrightqz->view;
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Desc: 导出详情页数据 Excel
     * liuyuhan
     */
    public function exportviewexcel($copyrightqzId = 0)
    {
        /* format the fields of every data in order to export data. */
        $data = $this->dao->findByID($copyrightqzId)->from(TABLE_COPYRIGHTQZ)->fetch();
        $this->loadModel('file');
        $copyrightqzLang = $this->lang->copyrightqz;
        $copyrightqzConfig = $this->config->copyrightqz;

        /* Create field lists. */
        $fields = explode(',', $copyrightqzConfig->list->exportFields);
        foreach($fields as $key => $fieldName)
        {
            $fieldName = trim($fieldName);
            $fields[$fieldName] = isset($copyrightqzLang->$fieldName) ? $copyrightqzLang->$fieldName : $fieldName;
            unset($fields[$key]);
        }
        /* Get users, products and executions. */
        $users = $this->loadModel('user')->getPairs('noletter');
        $depts = $this->loadModel('dept')->getOptionMenu();
        if(helper::isZeroDate($data->devFinishedTime)) $data->devFinishedTime=null;
        if(helper::isZeroDate($data->firstPublicTime)) $data->firstPublicTime=null;
        if(helper::isZeroDate($data->outsideReviewTime)) $data->outsideReviewTime=null;
        if(helper::isZeroDate($data->synDate)) $data->synDate=null;
        //单选
        $data->applicant   = $users[$data->applicant];
        //迭代二十六-删除部门第一个'/'
        $data->applicantDept   = ltrim($depts[$data->applicantDept], '/');
        $data->descType   = $copyrightqzLang->descTypeList[$data->descType];
        $data->publishStatus   = $copyrightqzLang->publishStatusList[$data->publishStatus];
        $data->firstPublicCountry   = $copyrightqzLang->firstPublicCountryList[$data->firstPublicCountry];
        $data->devMode   = $copyrightqzLang->devModeList[$data->devMode];
        $data->rightObtainMethod   = $copyrightqzLang->rightObtainMethodList[$data->rightObtainMethod];
        $data->isOriRegisNumChanged   = $copyrightqzLang->isOriRegisNumChangedList[$data->isOriRegisNumChanged];
        $data->isRegister   = $copyrightqzLang->isRegisterList[$data->isRegister];
        $data->rightRange   = $copyrightqzLang->rightRangeList[$data->rightRange];
        $data->system   = $copyrightqzLang->systemList[$data->system];
        //复选框
        $data->identityMaterial   =  $this->copyrightqz->getItemsValue($data->identityMaterial, $copyrightqzLang->identityMaterialList);
        $data->generalDeposit   =$this->copyrightqz->getItemsValue($data->generalDeposit, $copyrightqzLang->generalDepositList);
        $data->exceptionalDeposit   = $this->copyrightqz->getItemsValue($data->exceptionalDeposit, $copyrightqzLang->exceptionalDepositList);
        $data->devLanguage   = $this->copyrightqz->getItemsValue($data->devLanguage, $copyrightqzLang->devLanguageList);
        $data->softwareType   = $this->copyrightqz->getItemsValue($data->softwareType, $copyrightqzLang->softwareTypeList);
        $data->techFeatureType   = $this->copyrightqz->getItemsValue($data->techFeatureType, $copyrightqzLang->techFeatureTypeList);
        //处理时间
        $data->devFinishedTime = substr($data->devFinishedTime,0, 10);
        $data->firstPublicTime = substr($data->firstPublicTime,0, 10);
        //判断关联产品登记是否已删除，若删除，则不展示，显示已删除
        $productenroll = $this->dao->select('deleted')->from(TABLE_PRODUCTENROLL)->where('id')->eq($data->productenrollId)->fetch();
        $data->productenrollCode = $productenroll->deleted=='0'? $data->productenrollCode : $this->lang->copyrightqz->productenrollDeleted;

        $tableHeader = array('name' => '参数名', 'value'=>'值');
        $rows = array();
        foreach($data as $dataKey=>$dataValue)
        {
            if (!empty($fields[$dataKey])){
                $row = new stdClass();
                $row->name = $fields[$dataKey];
                $row->value = $dataValue;
                $rows[] = $row;
            }
        }
        $this->post->set('fields', $tableHeader);
        $this->post->set('rows', $rows);
        $this->post->set('kind', 'copyrightqz');
        $this->post->set('fileName', $data->code);
        $this->fetch('file', 'export2' . 'xlsx', $_POST);


    }

    /**
     * Project: chengfangjinke
     * Desc:审批
     * liuyuhan
     */
    public function review($copyrightqzId, $changeVersion = 1, $reviewStage = 0){
        if($_POST)
        {
            $this->copyrightqz->review($copyrightqzId);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $response['result']  = 'success';
            $response['message'] =  $this->lang->copyrightqz->submitsuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }

        $this->view->title       = $this->lang->copyrightqz->review;
        $this->view->copyrightqz = $this->copyrightqz->getById($copyrightqzId);
        $this->display();
    }
}
