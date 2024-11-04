<?php
class demandcollection extends control
{
    public function browse($browseType = 'all', $param = 0, $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $browseType = strtolower($browseType);

        $labelList=$this->lang->demandcollection->labelList + $this->lang->demandcollection->statusList;
        
        $depts = $this->loadModel('dept')->getOptionMenu();
        $depts[0] = '';
        $this->loadModel('datatable');
        $this->loadModel('story');
        $productplanList = $this->loadModel('productplan')->getProductPlanList();
        $demandList = $this->demandcollection->getPairsByDemand(['deleted']);
        $this->config->demandcollection->search['params']['priority']['values'] = $this->lang->story->priList;
        $this->config->demandcollection->search['params']['dept']['values'] = $depts;
        $this->config->demandcollection->search['params']['Implementation']['values']  = $depts;
        $this->config->demandcollection->search['params']['Expected']['values']  = $productplanList;
        $this->config->demandcollection->search['params']['Actual']['values']  = $productplanList;
        $this->config->demandcollection->search['params']['demandId']['values']  = $demandList;

        /* By search. */
        $queryID = ($browseType == 'bysearch')  ? (int)$param : 0; 
        $actionURL = $this->createLink('demandcollection', 'browse', "browseType=bySearch&param=myQueryID");
        $this->demandcollection->buildSearchForm($queryID, $actionURL);


        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $demandcollections = $this->demandcollection->getList($browseType,$queryID,$orderBy,$pager);
        $demandId = array_column($demandcollections,'id');
        $card = $this->loadModel('kanban')->checkCard($demandId,'demandcollection','',$fields="id,fromID");
        foreach ($demandcollections as $k=>$v){
            $v->cardID = 0;
            foreach ($card as $item) {
                if ($item->fromID == $v->id){
                    $v->cardID = $item->id;
                }
            }
        }
        $this->view->plans      = $this->loadModel('productplan')->getPairs('1', '0', '', true);
        $this->view->demandcollections = $demandcollections;
        $this->view->depts    = $this->loadModel('dept')->getOptionMenu();
        $this->view->users = $this->loadModel('user')->getPairs('noclosed');
        $this->view->orderBy      = $orderBy;
        $this->view->pager        = $pager;
        $this->view->browseType   = $browseType;
        $this->view->labelList   = $labelList;
        $this->view->title       = $this->lang->demandcollection->browse;
        $this->display();
    }

    public function create()
    {
        if($_POST)
        {
            $demandcollectionId = $this->demandcollection->create();
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $actionID = $this->loadModel('action')->create('demandcollection', $demandcollectionId, 'created', $this->post->comment);
            $this->demandcollection->sendmail($demandcollectionId, $actionID);
            
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = inlink('browse');

            $this->send($response);
        }

        $this->view->users = $this->loadModel('user')->getPairs('noclosed');
        $this->view->submitter=$this->app->user->account;
        $this->view->correctionReasonList = $this->lang->demandcollection->correctionReasonList;
        $this->view->title       = $this->lang->demandcollection->create;
        //所有平台信息
        $belongPlatformList = $this->loadModel('common')->getLangDataList('demandcollection', 'belongPlatform');
        $belongPlatformExtendList = array_column($belongPlatformList, 'extendInfo', 'key');
        $this->view->belongPlatformExtendList = $belongPlatformExtendList;
        $this->display();
    }

    public function edit($demandcollectionId=0)
    {
        if($_POST)
        {
            $changes = $this->demandcollection->update($demandcollectionId,'edit');

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes || $this->post->comment)
            {
                $actionID = $this->loadModel('action')->create('demandcollection', $demandcollectionId, 'edited', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = inlink('browse');
            
            $this->send($response);
        }

        $this->view->correctionReasonList = $this->lang->demandcollection->correctionReasonList;
        $this->view->users = $this->loadModel('user')->getPairs('noclosed');
        $this->view->title       = $this->lang->demandcollection->edit;
        $this->view->submitter=$this->app->user->account;
        $this->view->demandcollection=$this->demandcollection->getByID($demandcollectionId,true);
        $this->view->filterBelongPlatform = $this->demandcollection->getChildTypeList($this->view->demandcollection->belongPlatform);
        //所有平台信息
        $belongPlatformList = $this->loadModel('common')->getLangDataList('demandcollection', 'belongPlatform');
        $belongPlatformExtendList = array_column($belongPlatformList, 'extendInfo', 'key');
        $this->view->belongPlatformExtendList = $belongPlatformExtendList;
        $this->display();
    }

    public function deal($demandcollectionId = 0)
    {
        if($_POST)
        {
            $changes = $this->demandcollection->update($demandcollectionId, 'deal');


           if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $params = array_column($changes,'field');
            $state = array_search('state',$params);

            if($changes || $this->post->comment)
            {
                $actionID = $this->loadModel('action')->create('demandcollection', $demandcollectionId, 'deal', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }

            // 状态为已上线 发送邮件
            if(is_numeric($state) && $_POST['state'] == 5){
                $this->demandcollection->sendmail($demandcollectionId,$actionID);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = inlink('browse');
            
            $this->send($response);
        }
        $this->view->correctionReasonList = $this->lang->demandcollection->correctionReasonList;
        $this->view->plans      = $this->loadModel('productplan')->getPairs('1', '0', '', true);
        $this->loadModel('story');
        $this->view->prioritys = $this->lang->story->priList;
        $this->view->depts = $this->loadModel('dept')->getTopPairs();
        $this->view->users = $this->loadModel('user')->getPairs('noclosed');
        $this->view->demandcollection = $this->demandcollection->getByID($demandcollectionId,true);
        $this->view->title       = $this->lang->demandcollection->deal;
        $this->view->filterBelongPlatform = $this->demandcollection->getChildTypeList($this->view->demandcollection->belongPlatform);
        //产品信息
        $productList =  array('' => '') + $this->loadModel('product')->getProductWithCodeName();
        $this->view->productList = $productList;
        $this->display();
    }

    public function confirmed($demandcollectionId=0){
        if($_POST)
        {
            $changes = $this->demandcollection->update($demandcollectionId,'confirmed');

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes || $this->post->comment)
            {
                $actionID = $this->loadModel('action')->create('demandcollection', $demandcollectionId, 'confirmed', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            
            $this->send($response);
        }

        $this->view->users = $this->loadModel('user')->getPairs('noclosed');
        $this->view->submitter=$this->app->user->account;
        $this->view->demandcollection=$this->demandcollection->getByID($demandcollectionId,true);
        $this->display();
    }

    public function closed($demandcollectionId=0)
    {
        if($_POST)
        {
            $changes = $this->demandcollection->update($demandcollectionId,'closed');

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes || $this->post->comment)
            {
                $actionID = $this->loadModel('action')->create('demandcollection', $demandcollectionId, 'closed', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            
            $this->send($response);
        }
        $demandcollection = $this->demandcollection->getByID($demandcollectionId, true);
        $productIds = array_filter(explode(',', $demandcollection->product));
        $this->view->plans =  $this->loadModel('productplan')->getProductPlanList($productIds, '0', '', true);

        $this->view->depts = $this->loadModel('dept')->getTopPairs();
        $this->view->users = $this->loadModel('user')->getPairs('noclosed');
        $this->view->submitter=$this->app->user->account;
        $this->view->demandcollection = $demandcollection;
        $this->display();
    }

    public function view($demandcollectionId=0)
    {
        $this->view->actions  = $this->loadModel('action')->getList('demandcollection', $demandcollectionId);
        $this->view->title       = $this->lang->demandcollection->view;

        $this->view->depts = $this->loadModel('dept')->getTopPairs();
        $this->view->users = $this->loadModel('user')->getPairs('noclosed');
        $this->view->submitter=$this->app->user->account;
        $this->view->correctionReasonList = $this->lang->demandcollection->correctionReasonList;
        $demandcollection = $this->demandcollection->getByID($demandcollectionId,true);
        $this->view->demandcollection = $demandcollection;
        //产品信息
        $productIds = array_filter(explode(',', $demandcollection->product));
        if($productIds){
            $productList =  $this->loadModel('product')->getProductWithCodeName('', 0, '', 0, $productIds);
            $this->view->productList = $productList;
        }
        //版本
        $productPlanList = $this->loadModel('productplan')->getProductPlanList(0, '0', '', true);
        $this->view->productPlanList = $productPlanList;
        $this->view->demandList = $this->demandcollection->getPairsByDemand(['deleted']);
        $this->display();
    }

    public function export($orderBy = 'id_desc', $browseType = 'all')
    {
        /* format the fields of every modify in order to export data. */
        if($_POST)
        {

            $this->loadModel('file');
            $demandcollectionLang   = $this->lang->demandcollection;
            $demandcollectionConfig = $this->config->demandcollection;

            /* Create field lists. */
            $fields = $this->post->exportFields ? $this->post->exportFields : explode(',', $demandcollectionConfig->list->exportFields);

            foreach($fields as $key => $fieldName)
            {
                $fieldName = trim($fieldName);
                if($fieldName == 'belongPlatform'){
                    $fields['belongPlatform'] = $demandcollectionLang->bPlatform;
                }elseif($fieldName == 'belongModel'){
                    $fields['belongModel'] = $demandcollectionLang->bModel;
                }else{
                    $fields[$fieldName] = isset($demandcollectionLang->$fieldName) ? $demandcollectionLang->$fieldName : $fieldName;
                }
                unset($fields[$key]);
            }

            /* Get demandcollections. */
            $demandcollections = array();

            if($this->session->demandcollectionOnlyCondition)
            {
                $demandcollections = $this->dao->select('*')->from(TABLE_DEMANDCOLLECTION)->where($this->session->demandcollectionOnlyCondition)
                    ->beginIF($this->post->exportType == 'selected')->andWhere('id')->in($this->cookie->checkedItem)->fi()
                    ->orderBy($orderBy)->fetchAll('id');
            }
            else
            {
                $stmt = $this->dbh->query($this->session->demandcollectionQueryCondition . ($this->post->exportType == 'selected' ? " AND id IN({$this->cookie->checkedItem})" : '') . " ORDER BY " . strtr($orderBy, '_', ' '));
                while($row = $stmt->fetch()) $demandcollections[$row->id] = $row;
            }

            /* Get users and depts. */
            $depts = $this->loadModel('dept')->getTopPairs();
            $users = $this->loadModel('user')->getPairs('noletter');
            $plans =  $this->loadModel('productplan')->getProductPlanList();
            $correctionReasonList = $this->lang->demandcollection->correctionReasonList;
            $demandList = $this->demandcollection->getPairsByDemand(['deleted']);
            foreach($demandcollections as $demandcollection)
            {
                $demandcollection->dept=$depts[$demandcollection->dept];
                $demandcollection->Implementation=$depts[$demandcollection->Implementation];
                $demandcollection->submitter=$users[$demandcollection->submitter];
                $demandcollection->belongPlatform = $this->lang->demandcollection->belongPlatform[$demandcollection->belongPlatform];
                $demandcollection->belongModel = $this->lang->demandcollection->belongModel[$demandcollection->belongModel];
                $demandcollection->type=zget($this->lang->demandcollection->typeList,$demandcollection->type,$demandcollection->type);
                $demandcollection->state=zget($this->lang->demandcollection->statusList,$demandcollection->state,$demandcollection->state);
                $demandcollection->dealUser=$users[$demandcollection->dealuser];
                $demandcollection->createBy=$users[$demandcollection->createBy];
                $demandcollection->updateBy=$users[$demandcollection->updateBy];
                $demandcollection->handoverBy=$users[$demandcollection->handoverBy];
                $demandcollection->confirmBy = $users[$demandcollection->confirmBy];
                $demandcollection->closedBy = $users[$demandcollection->closedBy];
                $demandcollection->productmanager=$users[$demandcollection->productmanager];
                $demandcollection->Developer=$users[$demandcollection->Developer];
                $demandcollection->Expected = zmget($plans, $demandcollection->Expected);
                $demandcollection->Actual = zmget($plans,$demandcollection->Actual);
                $demandcollection->commConfirmBy = zmget($users, $demandcollection->commConfirmBy);
                $demandcollection->correctionReason=$correctionReasonList[$demandcollection->correctionReason];
                $demandcollection->commConfirmRecord=strip_tags($demandcollection->commConfirmRecord);
                $demandcollection->demandId = zget($demandList,$demandcollection->demandId);

                $story = $this->dao->select('id,sourceNote')->from(TABLE_STORY)->where('sourceNote')->eq($demandcollection->id)->andWhere('source')->eq('tb')->fetch();
                $demandcollection->storyId   = isset($story->id)   ? $story->id   : '';
            }

            $this->post->set('fields', $fields);
            $this->post->set('rows', $demandcollections);
            $this->post->set('kind', 'demandcollection');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        $this->view->fileName        = $this->lang->demandcollection->exportName;
        $this->view->allExportFields = $this->config->demandcollection->list->exportFields;

        $this->display();
    }


    public function  test($feedbackDate = '2022-12-14 23:00:00', $handoverDate = '2022-12-17 00:00:00'){
        $feedbackDay = substr($feedbackDate, 0, 10);
        $handoverDay = substr($handoverDate, 0, 10);
        if($feedbackDay == $handoverDay){
            $diffTime = strtotime($feedbackDate) - strtotime($handoverDate);
            $diffWorkDay = bcdiv($diffTime, 86400, 2);
        }else{
            $diffWorkDay = helper::diffDate2($handoverDate, $feedbackDate) - 1;
        }
        echo $diffWorkDay;

    }
    /***
     * @param $ids
     * Desc:同步需求收集 单条/同步
     * songdi
     */
    public function selectspace($ids){
        $ids = trim($ids,',');
        $this->loadModel('kanban');
        $demandID = explode(',',$ids);
        if ($_POST){
            $res = $this->kanban->batchDemandcollection($ids,'demandcollection');
            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));
            foreach ($demandID as $v) {
                $actionID = $this->loadModel('action')->create('demandcollection', $v, 'batchkanban', '');
            }
            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess,  'locate' => 'parent'));
        }
        $space = $this->kanban->getSpaceList('all');
        $data = [];
        foreach ($space as $item) {
            $data[$item->id] = $item->name;
        }
        $this->view->space = $data;
        $this->view->selectSpaceID = 4;//默认选中空间id
        $this->view->ids = $ids;
        $this->display();
    }

    /**
     * @param string $type
     * 获取子类型
     */
    public function ajaxGetChildTypeList($type = '')
    {
        $list = $this->demandcollection->getChildTypeList($type);
        die(html::select('belongModel', $list, '', 'class=form-control'));
    }

    /**
     * 获得产品版本列表
     *
     * @param $productIds
     */
    public function ajaxGetProductPlanList($productIds){
        $productPlanList = $this->loadModel('productplan')->getProductPlanList($productIds, '0', '', true);
        $data[0] = html::select('Expected[]', $productPlanList, '', "class='form-control chosen' multiple");
        $data[1] = html::select('Actual[]', $productPlanList, '', "class='form-control chosen' multiple");
        echo json_encode($data);

    }

    public function ajaxGetPairsByOpinion(){
        $list = $this->demandcollection->getPairsByOpinion();
        die( html::select('opinionID', $list, '', "class='form-control chosen'"));
    }

    public function ajaxGetPairsByRequirement($opinionId){
        $list = $this->demandcollection->getPairsByRequirement($opinionId);
        die( html::select('requirementID', $list, '', "class='form-control chosen'"));
    }

    public function ajaxGetPairsByDemand(){
        $list = $this->demandcollection->getPairsByDemand();
        die( html::select('demandId', $list, '', "class='form-control chosen'"));
    }

    public function ajaxIsClickable($id, $action)
    {
        $info = $this->loadModel('demandcollection')->getByID($id);

        $res = 0;
        if(!empty($info->demandId)){
            $demand = $this->loadModel('demandinside')->getByID($info->demandId);

            if('onlinesuccess' == $demand->status){
                $res = $this->lang->demandcollection->statusOnlinesuccessdError;
            }

            if('closed' == $demand->status){
                $res = $this->lang->demandcollection->statusClosedError;
            }

            if('suspend' == $demand->status){
                $res = $this->lang->demandcollection->statusSuspendError;
            }

            if(!empty($res)){
                die(json_encode(['code' => 0, 'message' => 'success', 'data' => $res]));
            }
        }

        $res = $this->loadModel('problem')->isClickable($info, $action) ? 0 : $this->lang->demandcollection->authStatusError;

        die(json_encode(['code' => 0, 'message' => 'success', 'data' => $res]));
    }
}

