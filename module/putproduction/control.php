<?php
/**
 * The control file of release module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     release
 * @version     $Id: control.php 4178 2013-01-20 09:32:11Z wwccss $
 * @link        http://www.zentao.net
 */
class putproduction extends control
{
    public function __construct()
    {
        parent::__construct();
        // 上海分公司审核节点名称修改
        // 上海分公司审核节点名称修改
        if (in_array($this->app->getMethodName(),['create','copy'])){
            $this->putproduction->resetNodeAndReviewerName();
        }
    }
    /**
     * 投产列表
     *
     */
    public function browse($browseType = 'all', $param = 0,  $orderBy = 'code_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $this->view->title = $this->lang->putproduction->browse;
        $browseType = strtolower($browseType);
        $users = array('' => '') + $this->loadModel('user')->getPairs('noletter');
        $outsideProjectList =  array('' => '') + $this->loadModel('outsideplan')->getPairs();//(外部)项目/任务
        $inProjectList =  array('' => '') + $this->loadModel('projectplan')->getRelatedOutInPlanNameList(); //(外部)项目/任务关联的内部项目
        //投产系统
        $apps =  $this->loadModel('application')->getApplicationCodePairsSyncJinx();
        $appList = array('' => '') + array_column($apps, 'name', 'id');

        //关联需求条目
        $exWhere = " status in ('feedbacked', 'changeabnormal') and sourceDemand = '1'";
        $demandList =  array('' => '') + $this->loadModel('demand')->getPairsTitle('noclosed', $exWhere);
        //产品
        $products = $this->loadModel('product')->getList();
        $productList = array('' => '') +  array_column($products, 'name' , 'id');
        $this->config->putproduction->search['params']['outsidePlanId']['values'] = $outsideProjectList;
        $this->config->putproduction->search['params']['inProjectIds']['values'] = $inProjectList;
        $this->config->putproduction->search['params']['app']['values'] = $appList;
        $this->config->putproduction->search['params']['productId']['values'] = [];
        $this->config->putproduction->search['params']['demandId']['values']  = $demandList;
        $this->config->putproduction->search['params']['productId']['values']  = $productList;

        $queryID = ($browseType == 'bysearch')  ? (int)$param : 0;
        $actionURL = $this->createLink('putproduction', 'browse', "browseType=bySearch&param=myQueryID");
        $this->putproduction->buildSearchForm($queryID, $actionURL);
        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);
        $data = $this->putproduction->getList($browseType, $queryID, $orderBy, $pager);
        $this->view->orderBy    = $orderBy;
        $this->view->pager      = $pager;
        $this->view->param      = $param;
        $this->view->browseType = $browseType;
        $this->view->users = $users;

        $this->view->outsideProjectList = $outsideProjectList;
        $this->view->appList = $appList;
        //$this->view->oproductList = $productList;
        //$this->view->demandList = $demandList;
        $this->view->data = $data;
        $this->session->set('putproductionList', $this->app->getURI(true),'backlog');
        $this->display();
    }


    /**
     * 详情
     * @param $putproductionId
     * $type 选项卡
     *
     * @param $putproductionId
     */
    public function view($putproductionId, $type = 'putproductionForm')
    {
        $this->app->loadLang('release');
        $this->app->loadLang('file');
        $this->app->loadLang('api');
        $this->app->loadLang('consumed');
        $type = $type == 'workWaitList'?  'putproductionForm': $type;
        $this->view->title = $this->lang->putproduction->view;
        $putproductionInfo = $this->putproduction->getByID($putproductionId);
        $users =  $this->loadModel('user')->getPairs('noletter');
        $outsideProjectList = $this->loadModel('outsideplan')->getPairs();//(外部)项目/任务
        //投产系统
        $apps =  $this->loadModel('application')->getApplicationCodePairsSyncJinx();
        $appList =  array_column($apps, 'name', 'id');
        $demandList = [];
        if($putproductionInfo->demandId){
            //关联需求条目
            $demandIds = array_filter(explode(',', $putproductionInfo->demandId));
            $exWhere = " id In ( ".implode(',', $demandIds).")";
            $demandList = $this->loadModel('demand')->getPairsTitle('noclosed', $exWhere);
        }

        //产品
        $products = $this->loadModel('product')->getList();
        $productList =  array_column($products, 'name' , 'id');
        $this->view->actions  = $this->loadModel('action')->getList($this->config->putproduction->objectType, $putproductionId);
        //获得流程图
        $reviewFlowInfo = new  stdClass();
        $flowImgInfo = '';
        if($putproductionInfo->workflowId){
            $reviewFlowInfo =  $this->loadModel('iwfp')->queryProcessTrackImage($putproductionInfo->workflowId);
            $flowImgInfo = $reviewFlowInfo->procBPMN;

        }
        $nodes = $this->loadModel('iwfp')->getCurrentVersionReviewNodes($putproductionInfo->workflowId, $putproductionInfo->version);
        $this->view->putproduction = $putproductionInfo;
        $this->view->reviewFlowInfo = $reviewFlowInfo; //审核流程
        $this->view->flowImg = $flowImgInfo;
        $this->view->type  = $type;
        $this->view->users = $users;

        $this->view->nodes = $nodes;

        $this->view->outsideProjectList = $outsideProjectList;
        $this->view->appList = $appList;
        $this->view->demandList = $demandList;
        $this->view->productList = $productList;
        $inProjectList = $this->loadModel('projectplan')->getPlanNameListByOutID($putproductionInfo->outsidePlanId);
        $this->view->inProjectList = $inProjectList;
        $endStatusList = $this->lang->putproduction->endStatusList;
        if($putproductionInfo->isOnlyFistStage){
            $endStatusList[] = 'filepass';
        }
        $this->view->endStatusList = $endStatusList;
        $this->display();
    }

    /**
     * 查看历史审批意见
     *
     * @param $id
     */
    function showHistoryNodes($id){
        $this->view->title = $this->lang->putproduction->showHistoryNodes;
        $putproductionInfo = $this->putproduction->getByID($id);
        $nodes = $this->loadModel('iwfp')->getAllVersionReviewNodes($putproductionInfo->workflowId);
        if($nodes){
            $users =  $this->loadModel('user')->getPairs('noletter');
            $this->view->users = $users;
        }
        $this->view->nodes = $nodes;
        $this->view->putproductionInfo = $putproductionInfo;
        $this->display();
    }

    /**
     * 创建投产
     *
     */
    public function create(){
        $objectType = $this->config->putproduction->objectType;
        if($_POST)
        {
            $issubmit = $_POST['issubmit'];
            $putproductionID = $this->putproduction->create();
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $this->loadModel('action')->create('putproduction', $putproductionID, 'created', $this->post->remark);
            $successType = $issubmit.'Success';
            $response['result']  = 'success';
            $response['message'] = $this->lang->$successType;
            $response['locate']  = inlink('browse');
            $response['id']      = $putproductionID;
            $this->send($response);
        }

        $this->view->title = $this->lang->putproduction->create;
        $outsideProjectList =  array('' => '') + $this->loadModel('outsideplan')->getPairs();//(外部)项目/任务
        $apps =  $this->loadModel('application')->getApplicationCodePairsSyncJinx();
        $appList = array('' => '') + array_column($apps, 'name', 'id');
        //需求条目
        $demandList = array('' => '') + $this->loadModel('demand')->modifySelect($objectType);
        //定义阶段投产
        $exWhere = " status = 'filepass'";
        $firstStagePutProductionList =  array('' => '') + $this->putproduction->getOnlyFirstStagePutProductionList($exWhere);
        $reviewers = $this->putproduction->getReviewers();
        $reviewerAccounts  = $this->loadModel('review')->getReviewerAccounts($reviewers);  //审核节点下的审核人列表
        $this->view->appList = $appList;
        $this->view->outsideProjectList = $outsideProjectList;
        $this->view->demandList = $demandList;
        $this->view->firstStagePutProductionList = $firstStagePutProductionList;
        $this->view->reviewers = $reviewers;
        $this->view->reviewerAccounts = $reviewerAccounts;
        $this->view->productList = [];
        $this->display();
    }

    /**
     * 编辑投产信息
     *
     * @param $putproductionId
     */
    public function edit($putproductionId)
    {
        $this->view->title = $this->lang->putproduction->edit;
        $objectType = $this->config->putproduction->objectType;
        $putproductionInfo = $this->putproduction->getByID($putproductionId);
        if($_POST) {
            $issubmit = $_POST['issubmit'];
            $changes = $this->putproduction->update($putproductionId);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $actionID = $this->loadModel('action')->create('putproduction', $putproductionId, 'edited', $this->post->remark);
            $this->action->logHistory($actionID, $changes);
            $successType = $issubmit.'Success';
            $response['result']  = 'success';
            $response['message'] = $this->lang->$successType;
            $response['locate']  = inlink('browse');
            $response['locate']  = inlink('browse');
            $response['status']  = $putproductionInfo->status;
            $this->send($response);
        }
        $checkRes = $this->putproduction->checkIsAllowEdit($putproductionInfo, $this->app->user->account);
        if($checkRes['result']){
            $outsideProjectList =  array('' => '') + $this->loadModel('outsideplan')->getPairs();//(外部)项目/任务
            $apps =  $this->loadModel('application')->getApplicationCodePairsSyncJinx();
            $appList = array('' => '') + array_column($apps, 'name', 'id');
            //需求条目
            $demandList = array('' => '') + $this->loadModel('demand')->modifySelect($objectType, $putproductionId);
            //定义阶段投产
            $exWhere = " status = 'filepass'";
            $firstStagePutProductionList =  array('' => '') + $this->putproduction->getOnlyFirstStagePutProductionList($exWhere);
            $reviewers = $this->putproduction->getReviewers($putproductionInfo->createdDept);
            $reviewerAccounts = json_decode($putproductionInfo->reviewerInfo, true);;  //审核用户
            $this->view->putproductionInfo = $putproductionInfo;
            $this->view->appList = $appList;
            $this->view->outsideProjectList = $outsideProjectList;
            $this->view->demandList = $demandList;
            $this->view->firstStagePutProductionList = $firstStagePutProductionList;
            $this->view->reviewers = $reviewers;
            $this->view->reviewerAccounts = $reviewerAccounts;
            $this->view->productList   = array('' => '') + $this->loadModel('product')->getProductWithCodeName('noclosed',0,'',$putproductionInfo->app);
            //(外部)项目/任务关联内部项目
            $inProjectList = $this->loadModel('projectplan')->getPlanNameListByOutID($putproductionInfo->outsidePlanId);
            if(empty($inProjectList)){
                $inProjectList = ['' => '无'];
            }
            $this->view->inProjectList = $inProjectList;
        }
        $this->view->checkRes = $checkRes;
        $this->display();
    }

    /**
     * 提交
     * @param $putproductionId
     */
    function submit($putproductionId){
        $this->view->title = $this->lang->putproduction->submit;
        $putproductionInfo = $this->putproduction->getByID($putproductionId);
        if($_POST) {
            $dealResult = '1';
            $dealUser = $this->app->user->account;
            $changes = $this->putproduction->submit($putproductionId, $dealUser, $dealResult, $this->post->comment);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $actionID = $this->loadModel('action')->create('putproduction', $putproductionId, 'submited', $this->post->comment);
            //$this->action->logHistory($actionID, $changes);

            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = 'parent';
            $response['status']  = $putproductionInfo->status;
            $this->send($response);
        }
        $checkRes = $this->putproduction->checkIsAllowSubmit($putproductionInfo, $this->app->user->account);
        if($checkRes['result']){
            $checkRes = $this->putproduction->checkInfoIsIntegrity($putproductionInfo);
        }
        $this->view->checkRes = $checkRes;
        $this->view->putproduction = $putproductionInfo;
        $this->display();
    }

    /**
     * 复制操作
     *
     * @param $putproductionId
     */
    public function copy($putproductionId){
        $objectType = $this->config->putproduction->objectType;
        if($_POST)
        {
            $issubmit = $_POST['issubmit'];
            $putproductionId = $this->putproduction->create();
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $this->loadModel('action')->create('putproduction', $putproductionId, 'created', $this->post->remark);
            $successType = $issubmit.'Success';
            $response['result']  = 'success';
            $response['message'] = $this->lang->$successType;
            $response['locate']  = inlink('browse');
            $response['id']      = $putproductionId;
            $this->send($response);
        }
        $this->view->title = $this->lang->putproduction->copy;
        $putproductionInfo = $this->putproduction->getByID($putproductionId);
        $outsideProjectList =  array('' => '') + $this->loadModel('outsideplan')->getPairs();//(外部)项目/任务
        $apps =  $this->loadModel('application')->getApplicationCodePairsSyncJinx();
        $appList = array('' => '') + array_column($apps, 'name', 'id');
        //需求条目
        $demandList = array('' => '') + $this->loadModel('demand')->modifySelect($objectType, $putproductionId);
        //定义阶段投产
        $exWhere = " status = 'filepass'";
        $firstStagePutProductionList =  array('' => '') + $this->putproduction->getOnlyFirstStagePutProductionList($exWhere);
        $reviewers = $this->putproduction->getReviewers();
        $reviewerAccounts  = $this->loadModel('review')->getReviewerAccounts($reviewers);  //审核节点下的审核人列表
        $this->view->putproductionInfo = $putproductionInfo;
        $this->view->appList = $appList;
        $this->view->outsideProjectList = $outsideProjectList;
        $this->view->demandList = $demandList;
        $this->view->firstStagePutProductionList = $firstStagePutProductionList;
        $this->view->reviewers = $reviewers;
        $this->view->reviewerAccounts = $reviewerAccounts;
        $this->view->productList   = array('' => '') + $this->loadModel('product')->getProductWithCodeName('noclosed',0,'',$putproductionInfo->app);
        //(外部)项目/任务关联内部项目
        $inProjectList = $this->loadModel('projectplan')->getPlanNameListByOutID($putproductionInfo->outsidePlanId);
        if(empty($inProjectList)){
            $inProjectList = ['' => '无'];
        }
        $this->view->inProjectList = $inProjectList;
        $this->display();
    }

    /**
     * @Notes:指派
     * @Date: 2024/1/9
     * @Time: 15:55
     * @Interface assignment
     * @param $id
     */
    public function assignment($id)
    {
        if($_POST)
        {
            $changes = $this->putproduction->assignment($id);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $actionID = $this->loadModel('action')->create('putproduction', $id, 'Assigned', $this->post->remark, $this->post->dealUser);
            $this->action->logHistory($actionID, $changes);

            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = 'parent';

            $this->send($response);

        }
        $this->view->users   = $this->loadModel('user')->getPairs('noclosed|noletter');
        $this->display();
    }

    /**
     * 审批操作
     *
     * @param $putproductionID
     */
    public function review($putproductionID){
        $putproductionInfo = $this->putproduction->getByID($putproductionID);
        if($_POST)
        {
            $this->putproduction->review($putproductionInfo);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }

        $this->view->title          = $this->lang->putproduction->review;
        $this->view->position[]     = $this->lang->putproduction->review;
        $this->view->putproduction       = $putproductionInfo;
        if($putproductionInfo->status == 'waitcm' && $putproductionInfo->stage != '1'){
            $this->view->releases = array('' => '') + $this->dao->select('id,name')->from(TABLE_RELEASE)
                    ->where('deleted')->eq(0)
                    ->andWhere('app')->in($putproductionInfo->app)
                    ->orderBy('id_desc')
                    ->fetchPairs();;
        }
        $this->display();
    }

    /**
     * 重新推送
     *
     * @param $putproductionID
     */
    public function repush($putproductionID){
        $putproductionInfo = $this->putproduction->getByID($putproductionID);
        if($_POST)
        {
            $this->putproduction->repush($putproductionInfo);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }

        $this->view->title          = $this->lang->putproduction->repush;
        $this->view->position[]     = $this->lang->putproduction->repush;
        $this->view->putproduction       = $putproductionInfo;
        $this->display();
    }


    /**
     * 删除操作
     *
     * @param $id
     */
    public function delete($id)
    {
        $info = $this->putproduction->getDefineFieldByID($id,'id,code');
        if(!empty($_POST))
        {
            $this->dao->update(TABLE_PUTPRODUCTION)->set('deleted')->eq(1)->where('id')->eq($id)->exec();
            $this->loadModel('action')->create('putproduction', $id, 'Deleted', $this->post->remark);

            $backUrl =  $this->session->putproductionList ? $this->session->putproductionList : inLink('browse');
            if(isonlybody())
            {
                die(js::closeModal('parent.parent', $backUrl));
            }
            else{
                die(js::locate(inLink('browse'),'parent.parent'));
            }
            die(js::reload('parent'));
        }
        $this->view->users   = $this->loadModel('user')->getPairs('noclosed|noletter');
        $this->view->actions = $this->loadModel('action')->getList('putproduction', $id);
        $this->view->info = $info;
        $this->display();
    }

    /**
     * @Notes:取消
     * @Date: 2024/1/9
     * @Time: 17:08
     * @Interface cancel
     * @param $id
     */
    public function cancel($id)
    {
        $info = $this->putproduction->getDefineFieldByID($id,'id,status,workflowId');
        if($_POST)
        {
            $this->putproduction->cancel($info);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $this->loadModel('action')->create('putproduction', $id, 'Canceld', $this->post->remark);
            $this->loadModel('consumed')->record('putproduction', $id, 0, $this->app->user->account, $info->status, 'cancel');
            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }
        $this->view->users   = $this->loadModel('user')->getPairs('noclosed|noletter');
        $this->view->actions = $this->loadModel('action')->getList('putproduction', $id);
        $this->display();
    }


    /**
     * 导出
     *
     * @param string $orderBy
     */
    public function export($orderBy = 'code_desc')
    {
        if($_POST)
        {
            $putproductionLang   = $this->lang->putproduction;
            $putproductionConfig = $this->config->putproduction;

            /* Create field lists. */
            $fields = $this->post->exportFields ? $this->post->exportFields : explode(',', $putproductionConfig->list->exportFields);
            foreach($fields as $key => $fieldName)
            {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = isset($putproductionLang->$fieldName) ? $putproductionLang->$fieldName : $fieldName;
                unset($fields[$key]);
            }
            /* Get changes. */
            $putproductions = array();
            if($this->session->putproductionOnlyCondition)
            {
                $putproductions = $this->dao->select('*')->from(TABLE_PUTPRODUCTION)->where($this->session->putproductionQueryCondition)
                    ->beginIF($this->post->exportType == 'selected')->andWhere('id')->in($this->cookie->checkedItem)->fi()
                    ->orderBy($orderBy)->fetchAll('id');

            }
            else
            {
                $stmt = $this->dbh->query($this->session->putproductionQueryCondition . ($this->post->exportType == 'selected' ? " AND id IN({$this->cookie->checkedItem})" : '') . " ORDER BY " . strtr($orderBy, '_', ' '));
                while($row = $stmt->fetch()) $putproductions[$row->id] = $row;
            }

            if($putproductions){
                $users = $this->loadModel('user')->getPairs('noletter');
                $outsideProjectList = $this->loadModel('outsideplan')->getPairs();//(外部)项目/任务
                //投产系统
                $apps =  $this->loadModel('application')->getApplicationCodePairsSyncJinx();
                $appList =  array_column($apps, 'name', 'id');
                //关联需求条目
                $exWhere = " status in ('feedbacked', 'changeabnormal') and sourceDemand = '1'";
                $demandList = $this->loadModel('demand')->getPairsTitle('noclosed', $exWhere);

                //产品
                $products = $this->loadModel('product')->getList();
                $productList =  array_column($products, 'name' , 'id');

                //投产
                foreach($putproductions as $val) {
                    $val->outsidePlanId = zget($outsideProjectList, $val->outsidePlanId);
                    $val->app           = zmget($appList, $val->app);
                    $val->productId     = zmget($productList, $val->productId);
                    $val->demandId      = zmget($demandList, $val->demandId);
                    $val->level         = zget($putproductionLang->levelList, $val->level);
                    $val->property      = zmget($putproductionLang->propertyList, $val->property);
                    $val->createdBy     = zget($users, $val->createdBy);
                    $val->stage         = zmget($putproductionLang->stageList, $val->stage);
                    $val->status        = zget($putproductionLang->statusList, $val->status);
                    $val->dealUser      = zmget($users, $val->dealUser);
                    $val->stage         = zget($putproductionLang->stageList, $val->stage);
                    $val->dataCenter    = zmget($putproductionLang->dataCenterList, $val->dataCenter);
                    $val->isPutCentralCloud = zget($putproductionLang->isPutCentralCloudList, $val->isPutCentralCloud);
                    $val->isReview          = zget($putproductionLang->isReviewList, $val->isReview);
                    $val->isBusinessCoopera = zget($putproductionLang->isBusinessCooperaList, $val->isBusinessCoopera);
                    $val->isBusinessAffect  = zget($putproductionLang->isBusinessAffectList, $val->isBusinessAffect);
                    $val->opResult  = zget($putproductionLang->opResultList, $val->opResult);

                }

            }

            $this->post->set('fields', $fields);
            $this->post->set('rows', $putproductions);
            $this->post->set('kind', 'putproduction');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }
        $this->view->fileName        = $this->lang->putproduction->exportName;
        $this->view->allExportFields = $this->config->putproduction->list->exportFields;
        $this->view->customExport    = true;
        $this->display();
    }

    /**
     * 编辑流程节点
     *
     * @param int $id
     * @param int $consumedID
     */
    public function workloadedit($putproductionID = 0, $consumedId = 0)
    {
        $putproductionInfo = $this->putproduction->getByID($putproductionID);
        if($_POST)
        {
            $this->putproduction->workloadedit($putproductionInfo, $consumedId);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $actionID = $this->loadModel('action')->create('putproduction', $putproductionID, 'workloadedit', $this->post->comment);
            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }

        $this->view->title          = $this->lang->putproduction->workloadedit;
        $this->view->position[]     = $this->lang->putproduction->workloadedit;
        $consumedList = $putproductionInfo->consumed;
        $consumedChose = '';
        $isLast = $this->loadModel('consumed')->checkIsLastConsumed($consumedId, $putproductionInfo->id, 'putproduction');
        foreach ($consumedList as $consumed){
            if($consumed->id == $consumedId){
                $consumedChose = $consumed;
            }
        }
        if($isLast){
            $returnStatusList = array();
            if(!empty($putproductionInfo->workflowId)){
                $statusList = $this->loadModel('iwfp')->getFreeJumpNodeList($putproductionInfo->workflowId);
                foreach ($statusList as $status){
                    $returnStatusList[$status->xmlTaskId] = $status->xmlTaskName;
                }
            }
            $this->view->enableChoseStatus = $returnStatusList;
        }else{
            $this->view->enableChoseStatus = $this->lang->putproduction->statusList;
        }
        $this->view->consumed = $consumedChose;
        $this->display();
    }

    public function pushData(){
        $res['putproductionPush'] = $this->loadModel('putproduction')->getUnPushedAndPush();
        echo $res['putproductionPush'];
    }

    /**
     * 编辑退回次数
     *
     * @param $putproductionID
     */
    public function editReturnCount($putproductionID){
        if($_POST) {
            $changes = $this->putproduction->editReturnCount($putproductionID);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $actionID = $this->loadModel('action')->create('putproduction', $putproductionID, 'edited', $this->post->comment);
            if($changes){
                $this->action->logHistory($actionID, $changes);
            }
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }
        $this->view->title  = $this->lang->putproduction->editreturntimes;
        $this->display();
    }

    /**
     * 同步发布信息到发布表
     * type 不为1时 同步为cm
     */
    public function syncReleaseInfo(){
        $data = $this->dao->select("id, `code`, `releaseId`, createdBy, createdDate, createdDept, `status`")
            ->from(TABLE_PUTPRODUCTION)
            ->where("status")->in(['success', 'successpart'])
            ->andwhere('releaseSyncStatus')->eq(1)
            ->andwhere('`releaseId`')->ne('')
            ->andwhere('createdDate')->ge('2021-01-01 00:00:00')
            ->fetchAll();
        if(!$data) die('没有数据需要同步');

        $releaseIds = [];
        foreach ($data as $key => $val){
            $currentReleaseIds = explode(',', trim($val->releaseId, ','));
            $val->releaseIds = $currentReleaseIds;
            $data[$key] = $val;
            $releaseIds = array_unique(array_merge($releaseIds, $currentReleaseIds));
        }

        $select = 'id, status, dealUser, version, syncObjectCreateTime,syncStateTimes';
        $releaseList = $this->loadModel('projectrelease')->getValidListByIds($releaseIds, $select,false);
        if(!$releaseList) die('没有数据需要同步');

        $releaseList = array_column($releaseList, null, 'id');
        $data        = array_column($data, null, 'id');

        //要操作的发布信息
        $tempReleaseList = [];
        foreach ($data as $val){
            $currentReleaseIds = $val->releaseIds;
            foreach ($currentReleaseIds as $releaseId){
                $releaseInfo = zget($releaseList, $releaseId, '');
                if(!$releaseInfo) continue;

                $releaseInfo->syncPutProductId = $val->id;
                $tempReleaseList[]             = $releaseInfo;
            }
        }
        if(!$tempReleaseList) die('没有数据需要同步');

        $i = 0;
        $updateParams = new stdClass();
        $updateParams->releaseSyncStatus = 2;
        foreach ($tempReleaseList as $val){
            $syncPutProductId = $val->syncPutProductId;
            $putProductInfo = zget($data, $syncPutProductId);
            if($putProductInfo->createdDate > $val->syncObjectCreateTime && $putProductInfo->createdBy){
                $dealUser = $putProductInfo->createdBy;
                $putProductInfo->status = zget($this->lang->putproduction->statusList, $putProductInfo->status);
                $this->loadModel('projectrelease')->syncObjectInfo($val, 'putProduct', $syncPutProductId, $dealUser, $putProductInfo->createdDate, $putProductInfo);
                $i++;
            }
            $this->dao->update(TABLE_PUTPRODUCTION)->data($updateParams)->where('id')->eq($syncPutProductId)->exec();
        }
        die('处理了'.$i.'条数据');
    }

    /**
     * 根据(外部)项目/任务id获得对应内部项目
     *
     * @param $outsidePlanId
     */
    public function ajaxGetInProjectPlanList($outsidePlanId){
        $projectPlanList = [''=> '无'];
        $projectPlanIds = '';
        if($outsidePlanId){
            $ret = $this->loadModel('projectplan')->getPlanNameListByOutID($outsidePlanId);
            if($ret){
                $projectPlanList = $ret;
                $projectPlanIds = implode(',', array_keys($projectPlanList));
            }
        }
        $data = html::select('inProjectIds[]', $projectPlanList, $projectPlanIds, "class='form-control chosen' multiple");
        echo json_encode($data);
    }

    public function ajaxGetApplicationInfo($app){
        $app = trim($app,',');
        $appList = explode(',', $app);
        foreach ($appList as $appId){
            $application = $this->dao->findByID($appId)->from(TABLE_APPLICATION)->fetch();
            if(!empty($application->ciKey)){
                die(json_encode($application));
            }
        }
        die();
    }
}
