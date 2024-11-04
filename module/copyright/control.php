<?php
class copyright extends control
{
    public function browse($browseType = 'all', $param = 0, $orderBy = 'createdTime_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $browseType = strtolower($browseType);
        //搜索框的值
        $queryID = ($browseType == 'bysearch')  ? (int)$param : 0;
        $actionURL = $this->createLink('copyright', 'browse', "browseType=bySearch&param=myQueryID");
        $this->copyright->buildSearchForm($queryID, $actionURL);


        /* 设置详情页面返回的url连接。*/
        $this->session->set('copyrightList', $this->app->getURI(true));
        $this->session->set('common_back_url', $this->app->getURI(true),'backlog');
        $this->session->set('copyrightHistory', $this->app->getURI(true));

        /* 构建pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);
        $datas = $this->copyright->getList($browseType, $queryID, $orderBy, $pager);

        $depts = array(''=>'') + $this->loadModel('dept')->getOptionMenu();

        $this->view->title    = $this->lang->copyright->common;
        $this->view->orderBy    = $orderBy;
        $this->view->pager      = $pager;
        $this->view->param      = $param;
        $this->view->browseType = $browseType;
        $this->view->datas      = $datas;
        $this->view->users      = $this->loadModel('user')->getPairs('noletter|noclosed');
        $this->view->depts      = $depts;
        $this->display();
    }

    public function view($copyrightId = 0)
    {
        $copyright = $this->copyright->getByID($copyrightId);
        if ($copyright->deleted){
            $response['result']  = 'success';
            $response['locate']  = inlink('browse');
            $this->send($response);
        }else{
             $copyright = $this->loadModel('file')->replaceImgURL($copyright, '');
            //判断状态流转中是否前后状态一致，若操作后状态一样，则仅保留【操作时间】最近的那一次记录
//            $consumedFix = $copyright->consumed;
//            $temp = '';
//            for ($i=count($consumedFix)-1; $i>=0; $i--){
//                if (($consumedFix[$i]->before==$consumedFix[$i]->after) && ($consumedFix[$i]->after == $temp)){
//                    unset($consumedFix[$i]);
//                }else{
//                    $temp = $consumedFix[$i]->after;
//                }
//            }
            /* 查询需求条目及其相关的信息。*/
            $applications = $this->loadModel('application')->getapplicationNameCodePairs();
            $this->view->systemList = array('' => '') + $applications;
            $this->view->users       = $this->loadmodel('user')->getPairs('noletter|noclosed');
            $this->view->actions     = $this->loadmodel('action')->getList('copyright', $copyrightId);
            $this->view->copyright = $copyright;
            $this->view->consumed = $copyright->consumed;
            $this->view->depts       = $this->loadModel('dept')->getOptionMenu();
            $this->view->buildDepts  = $this->lang->application->teamList;
            $this->view->nodes       = $this->loadModel('review')->getNodes('copyright', $copyrightId, $copyright->changeVersion);
            $this->view->projectPlanList     = $this->loadModel('project')->getPairs();
            $copyright->fullname = implode(',', array_column($copyright->productList,'fullname'));
            $copyright->version = implode(',', array_column($copyright->productList,'version'));
            if(!empty(array_filter(array_column($copyright->productList,'shortName')))){
                $copyright->shortName = implode(',', array_column($copyright->productList,'shortName'));
            }else{
                $copyright->shortName = '';
            }
            //复选框
            $this->view->identityMaterial   =  $this->copyright->getItemsValue($copyright->identityMaterial, $this->lang->copyright->identityMaterialList);
            $this->view->generalDeposit   =$this->copyright->getItemsValue($copyright->generalDeposit, $this->lang->copyright->generalDepositList);
            $this->view->exceptionalDeposit   = $this->copyright->getItemsValue($copyright->exceptionalDeposit, $this->lang->copyright->exceptionalDepositList);
            $this->view->devLanguage   = $this->copyright->getItemsValue($copyright->devLanguage, $this->lang->copyright->devLanguageList);
            $this->view->softwareType   = $this->copyright->getItemsValue($copyright->softwareType, $this->lang->copyright->softwareTypeList);
            $this->view->techFeatureType   = $this->copyright->getItemsValue($copyright->techFeatureType, $this->lang->copyright->techFeatureTypeList);
        }
        $this->view->title       = $this->lang->copyright->view;
        $this->display();
    }

    public function exportviewexcel($copyrightId = 0)
    {
        /* format the fields of every data in order to export data. */
        $data = $this->dao->findByID($copyrightId)->from(TABLE_COPYRIGHT)->fetch();
        $this->loadModel('file');
        $copyrightLang = $this->lang->copyright;
        $copyrightConfig = $this->config->copyright;

        /* Create field lists. */
        $fields = explode(',', $copyrightConfig->list->exportFields);
        foreach($fields as $key => $fieldName)
        {
            $fieldName = trim($fieldName);
            $fields[$fieldName] = isset($copyrightLang->$fieldName) ? $copyrightLang->$fieldName : $fieldName;
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

        $softwareInfo = json_decode($data->softwareInfo);
        $data->fullname = implode(',', array_column($softwareInfo,'fullname'));
        $data->shortName = implode(',', array_column($softwareInfo,'shortName'));
        $data->version = implode(',', array_column($softwareInfo,'version'));
        $data->createdBy   = $users[$data->createdBy];
        //迭代二十六-删除部门第一个'/'
        $data->createdDept   = ltrim($depts[$data->createdDept], '/');
        $data->descType   = $copyrightLang->descTypeList[$data->descType];
        $data->publishStatus   = $copyrightLang->publishStatusList[$data->publishStatus];
        $data->firstPublicCountry   = $copyrightLang->firstPublicCountryList[$data->firstPublicCountry];
        $data->devMode   = $copyrightLang->devModeList[$data->devMode];
        $data->rightObtainMethod   = $copyrightLang->rightObtainMethodList[$data->rightObtainMethod];
        $data->isOriRegisNumChanged   = $copyrightLang->isOriRegisNumChangedList[$data->isOriRegisNumChanged];
        $data->isRegister   = $copyrightLang->isRegisterList[$data->isRegister];
        $data->rightRange   = $copyrightLang->rightRangeList[$data->rightRange];
        $applications = $this->loadModel('application')->getapplicationNameCodePairs();
        $systemList = array('' => '') + $applications;
        $data->buildDept = $this->lang->application->teamList[$data->buildDept];
        $data->system = $systemList[$data->system];
        //复选框
        $data->identityMaterial   =  $this->copyright->getItemsValue($data->identityMaterial, $copyrightLang->identityMaterialList);
        $data->generalDeposit   =$this->copyright->getItemsValue($data->generalDeposit, $copyrightLang->generalDepositList);
        $data->exceptionalDeposit   = $this->copyright->getItemsValue($data->exceptionalDeposit, $copyrightLang->exceptionalDepositList);
        $data->devLanguage   = $this->copyright->getItemsValue($data->devLanguage, $copyrightLang->devLanguageList);
        $data->softwareType   = $this->copyright->getItemsValue($data->softwareType, $copyrightLang->softwareTypeList);
        $data->techFeatureType   = $this->copyright->getItemsValue($data->techFeatureType, $copyrightLang->techFeatureTypeList);
        //处理时间
        $data->devFinishedTime = substr($data->devFinishedTime,0, 10);
        $data->firstPublicTime = substr($data->firstPublicTime,0, 10);
        //判断关联产品登记是否已删除，若删除，则不展示，显示已删除
        $modify = $this->dao->select('status')->from(TABLE_MODIFY)->where('id')->eq($data->modifyId)->fetch();
        $data->modifyCode = $modify->status!='deleted'? $data->modifyCode : $this->lang->copyright->modifyDeleted;

        $tableHeader = array('name' => '参数名', 'value'=>'值');
        $rows = array();
        foreach($fields as $dataKey => $name){
            if(!empty($data->$dataKey)){
                $row = new stdClass();
                $row->name = $name;
                $row->value = $data->$dataKey;
                $rows[] = $row;
            }
        }
        $this->post->set('fields', $tableHeader);
        $this->post->set('rows', $rows);
        $this->post->set('kind', 'copyright');
        $this->post->set('fileName', $data->code);
        $this->fetch('file', 'export2' . 'xlsx', $_POST);


    }

    public function review($copyrightId, $changeVersion = 1, $reviewStage = 0)
    {
        if($_POST)
        {
            $this->copyright->review($copyrightId);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $response['result']  = 'success';
            $response['message'] =  $this->lang->copyright->submitsuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }

        $this->view->title       = $this->lang->copyright->review;
        $this->view->copyright   = $this->copyright->getByID($copyrightId);
        $this->display();
    }

    public function export($action, $orderBy = 'id_desc', $browseType = 'all')
    {
        /* format the fields of every data in order to export data. */
        if($_POST)
        {
            $this->loadModel('file');
            $copyrightLang   = $this->lang->copyright;
            $copyrightConfig = $this->config->copyright;

            /* Create field lists. */
            $fields = $this->post->exportFields ? $this->post->exportFields : explode(',', $copyrightConfig->list->exportFields);
            foreach($fields as $key => $fieldName)
            {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = isset($copyrightLang->$fieldName) ? $copyrightLang->$fieldName : $fieldName;
                unset($fields[$key]);
            }

            /* Get datas. */
            $datas = array();
            $stmt = $this->dao->query($this->session->copyrightExportQuery . ($this->post->exportType == 'selected' ? " AND id IN({$this->cookie->checkedItem})" : '') . " ORDER BY " . strtr($orderBy, '_', ' '));
            $datas = $stmt->fetchAll();
//            if($this->session->copyrightOnlyCondition)
//            {
//                $datas = $this->dao->select('*')->from(TABLE_COPYRIGHT)->where($this->session->copyrightQueryCondition)
//                    ->andWhere('deleted')->eq('0')
//                    ->beginIF($this->post->exportType == 'selected')->andWhere('id')->in($this->cookie->checkedItem)->fi()
//                    ->orderBy($orderBy)->fetchAll('id');
//            }
//            else
//            {
//                $stmt = $this->dbh->query($this->session->copyrightQueryCondition . ($this->post->exportType == 'selected' ? " AND id IN({$this->cookie->checkedItem})" : '') . " ORDER BY " . strtr($orderBy, '_', ' '));
//                while($row = $stmt->fetch()) $datas[$row->id] = $row;
//            }
            /* Get users, products and executions. */
            $users = $this->loadModel('user')->getPairs('noletter');
            $depts = $this->loadModel('dept')->getOptionMenu();
            $applications = $this->loadModel('application')->getapplicationNameCodePairs();
            $systemList = array('' => '') + $applications;
            foreach($datas as $data)
            {
                $softwareInfo = json_decode($data->softwareInfo);
                $data->fullname = implode(',', array_column($softwareInfo,'fullname'));
                $data->shortName = implode(',', array_column($softwareInfo,'shortName'));
                $data->version = implode(',', array_column($softwareInfo,'version'));
                //单选
                $data->createdBy   = $users[$data->createdBy];
                //迭代二十六-删除部门第一个'/'
                $data->createdDept   = ltrim($depts[$data->createdDept], '/');
                $data->descType   = $copyrightLang->descTypeList[$data->descType];
                $data->publishStatus   = $copyrightLang->publishStatusList[$data->publishStatus];
                $data->firstPublicCountry   = $copyrightLang->firstPublicCountryList[$data->firstPublicCountry];
                $data->devMode   = $copyrightLang->devModeList[$data->devMode];
                $data->rightObtainMethod   = $copyrightLang->rightObtainMethodList[$data->rightObtainMethod];
                $data->isOriRegisNumChanged   = $copyrightLang->isOriRegisNumChangedList[$data->isOriRegisNumChanged];
                $data->isRegister   = $copyrightLang->isRegisterList[$data->isRegister];
                $data->rightRange   = $copyrightLang->rightRangeList[$data->rightRange];
                $data->system   = $systemList[$data->system];

                $data->devFinishedTime = substr($data->devFinishedTime,0, 10);
                $data->firstPublicTime = substr($data->firstPublicTime,0, 10);
                //复选框
                $data->identityMaterial   =  $this->copyright->getItemsValue($data->identityMaterial, $copyrightLang->identityMaterialList);
                $data->generalDeposit   =$this->copyright->getItemsValue($data->generalDeposit, $copyrightLang->generalDepositList);
                $data->exceptionalDeposit   = $this->copyright->getItemsValue($data->exceptionalDeposit, $copyrightLang->exceptionalDepositList);
                $data->devLanguage   = $this->copyright->getItemsValue($data->devLanguage, $copyrightLang->devLanguageList);
                $data->softwareType   = $this->copyright->getItemsValue($data->softwareType, $copyrightLang->softwareTypeList);
                $data->techFeatureType   = $this->copyright->getItemsValue($data->techFeatureType, $copyrightLang->techFeatureTypeList);


            }
            $this->post->set('fields', $fields);
            $this->post->set('rows', $datas);
            $this->post->set('kind', 'copyright');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        $this->view->fileName        = '自主知识产权列表';
        $this->view->allExportFields = $this->config->copyright->list->exportFields;
        $this->view->customExport    = true;
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Desc: 新建页面
     * 提交isSave=0，保存isSave=1
     * liuyuhan
     */
    public function create($isSave=0){
        if($_POST)
        {
            $copyrightID = $this->copyright->create($isSave=='1');

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $this->loadModel('action')->create('copyright', $copyrightID, $isSave=='1'?'created':'createdsubmit');
            $response['result']  = 'success';
            $response['message'] = $isSave=='1'?$this->lang->saveSuccess:$this->lang->copyright->submitSuccess;
            $response['locate']  = inlink('browse');

            $this->send($response);
        }
        $applications = $this->loadModel('application')->getapplicationNameCodePairs();

        $this->view->title = $this->lang->copyright->create;
        $modifyCodeList = $this->dao->select('modifyCode')->from(TABLE_COPYRIGHT)->where('deleted')->eq('0')->fetchAll('modifyCode');
        $codeList = $this->dao->select('code')->from(TABLE_COPYRIGHT)->where('deleted')->eq('0')->fetchPairs('code','code');
        $this->view->modifyCodeWithoutUse = array(''=>'') + $this->dao->select('code')->from(TABLE_MODIFY)->where('code')->notin(array_keys($modifyCodeList))->andWhere('status')->ne('deleted')->fetchPairs('code','code');
        $this->view->codeList = array(''=>'') + $codeList;
        $this->view->users = $this->loadModel('user')->getPairs('noclosed');
        $this->view->depts =$this->lang->application->teamList;
        $this->view->systemList = array('' => '') + $applications;
        $this->view->creator = $this->app->user->account;
        //使用清总说明文档
        $this->view->docDownload = $this->loadModel('file')->getByObject('copyrightqz', 0);
        //审核节点下的审核人列表
        $reviewers            = $this->copyright->getReviewers();
        //审核节点下的审核人列表
        $reviewerAccounts     = $this->loadModel('review')->getReviewerAccounts($reviewers);
        $this->view->reviewers            = $reviewers;
        $this->view->reviewerAccounts     = $reviewerAccounts;
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Desc:编辑页面
     * liuyuhan
     */
    public function edit ($id = 0,$isSave=0){
        if($_POST)
        {
            $changes = $this->copyright->update($id,$isSave=='1');

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $actionID = $this->loadModel('action')->create('copyright', $id, $isSave=='1'?'edited':'editedsubmit');
            $this->action->logHistory($actionID, $changes);

            $response['result']  = 'success';
            $response['message'] = $isSave=='1'?$this->lang->saveSuccess:$this->lang->copyright->submitSuccess;
            $response['locate']  = inlink('browse');

            $this->send($response);
        }


        $copyright = $this->copyright->getByID($id);
        //承建部门和系统名称
        $applications = $this->loadModel('application')->getapplicationNameCodePairs();


        $modifyCodeList = $this->dao->select('modifyCode')->from(TABLE_COPYRIGHT)->where('modifyCode')->ne($copyright->modifyCode)->andwhere('deleted')->eq('0')->fetchAll('modifyCode');
        $codeList = $this->dao->select('code')->from(TABLE_COPYRIGHT)->where('deleted')->eq('0')->fetchPairs('code','code');

        $this->view->title = $this->lang->copyright->edit;
        $this->view->copyright = $copyright;
        $this->view->modifyCodeWithoutUse = array(''=>'') + $this->dao->select('code')->from(TABLE_MODIFY)->where('code')->notin(array_keys($modifyCodeList))->andWhere('status')->ne('deleted')->fetchPairs('code','code');
        $this->view->codeList = array(''=>'') + $codeList;
        $this->view->users = $this->loadModel('user')->getPairs('noclosed');
        $this->view->depts = $this->lang->application->teamList;
        $this->view->systemList = array('' => '') + $applications;
        //审核节点下的审核人列表
        $reviewers            = $this->copyright->getReviewers();
        //审核节点下的审核人列表
        $reviewerAccounts     = $this->loadModel('review')->getReviewerAccounts($reviewers);
        // 当前选择
        $nodesReviewers = $this->loadModel('review')->getAllNodeReviewers('copyright', $id, $copyright->changeVersion);
        //使用清总说明文档
        $this->view->docDownload = $this->loadModel('file')->getByObject('copyrightqz', 0);
        $this->view->reviewers            = $reviewers;
        $this->view->reviewerAccounts     = $reviewerAccounts;
        $this->view->nodesReviewers = $nodesReviewers;
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Desc:删除著作权登记
     * liuyuhan
     */
    public function delete($copyrightId=0)
    {
        if($_POST)
        {
            $changes = $this->copyright->deleted($copyrightId);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $actionID = $this->loadModel('action')->create('copyright', $copyrightId, 'deleted', $this->post->comment);
            $this->action->logHistory($actionID, $changes);

            $response['result']  = 'success';
            $response['message'] = $this->lang->copyright->submitSuccess ;
            $response['locate']  =  'parent';
            $this->send($response);
        }
        $this->view->title = $this->lang->copyright->delete;
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Desc: 查询生产变更单产品名称
     * liuyuhan
     */
    public function ajaxGetProduct ($modifyCode){
        //替换“_”回“-"
        $modifyCode = strtr($modifyCode, '_', '-');
        $data = $this->dao->select('productId')->from(TABLE_MODIFY)
            ->where('code')->eq($modifyCode)
            ->fetch();
        $allProductNames    = $this->loadModel('product')->getNamePairs();
        $productName = array();
        if ($data->productId) {
            foreach (explode(',', $data->productId) as $productID) {
                if ($productID) {
                    $productName[] = $allProductNames[$productID];
                }
            }
        }
        $product = new stdClass();
        $product->productName = $productName;
        echo json_encode($product);
    }
}