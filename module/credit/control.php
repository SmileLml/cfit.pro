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
class credit extends control
{

    /**
     * 列表页
     *
     * @param string $browseType
     * @param int $param
     * @param string $orderBy
     * @param int $recTotal
     * @param int $recPerPage
     * @param int $pageID
     */
    public function browse($browseType = 'all', $param = 0,  $orderBy = 'code_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $this->view->title = $this->lang->credit->browse;
        $browseType = strtolower($browseType);
        $users = array('' => '') + $this->loadModel('user')->getPairs('noletter');
        $appExwhere = "isPayment = 3";
        $appList  =  array('' => '') + $this->loadModel('application')->getPairs(0, $appExwhere);
        $productList =  array('' => '') + $this->loadModel('product')->getProductWithCodeName();
        $projectList  =  array('' => '') + $this->loadModel('projectplan')->getAllProjects();
        $secondorderList =  array('' => '') + $this->loadModel('secondorder')->getNameList();
        //关联问题
        $problemList     = array('' => '') + $this->loadModel('problem')->getPairsAbstract();
        //关联需求
        $demandList   =  array('' => '')+ $this->loadModel('demand')->getPairsTitle('noclosed');
        $abnormalList =  array('' => '') + $this->credit->getAllAbnormalList();
        $deptList  = $this->loadModel('dept')->getTopPairs();
        $this->config->credit->search['params']['appIds']['values']           = $appList;
        $this->config->credit->search['params']['productIds']['values']      = $productList;
        $this->config->credit->search['params']['projectPlanId']['values']  = $projectList;
        $this->config->credit->search['params']['dealUsers']['values']       = $users;
        $this->config->credit->search['params']['createdBy']['values']       = $users;
        $this->config->credit->search['params']['createdDept']['values']     = $deptList;
        $this->config->credit->search['params']['secondorderIds']['values']  = $secondorderList;
        $this->config->credit->search['params']['problemIds']['values']      = $problemList;
        $this->config->credit->search['params']['demandIds']['values']       = $demandList;
        $this->config->credit->search['params']['abnormalId']['values']      = $abnormalList;
        $this->config->credit->search['params']['emergencyType']['values']      = $this->lang->credit->emergencyTypeList;
        $this->config->credit->search['params']['isBusinessAffect']['values']   = $this->lang->credit->isBusinessAffectList;

        $queryID = ($browseType == 'bysearch')  ? (int)$param : 0;
        $actionURL = $this->createLink('credit', 'browse', "browseType=bySearch&param=myQueryID");
        $this->credit->buildSearchForm($queryID, $actionURL);
        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);
        $data = $this->credit->getList($browseType, $queryID, $orderBy, $pager);

        $this->view->users = $users;
        $this->view->appList = $appList;
        $this->view->projectList = $projectList;
        $this->view->deptList = $deptList;
        $this->view->data = $data;
        $this->view->orderBy    = $orderBy;
        $this->view->pager      = $pager;
        $this->view->param      = $param;
        $this->view->browseType = $browseType;
        $this->session->set('common_back_url', $this->app->getURI(true),'backlog');
        $this->display();
    }


    /**
     * 详情
     *
     * @param $creditId
     * @param string $type
     */
    public function view($creditId, $type = 'creditForm')
    {
        $this->app->loadLang('consumed');
        $this->app->loadLang('modify');
        $this->view->title = $this->lang->credit->view;
        $type = $type == 'workWaitList'?  'creditForm': $type;
        $creditInfo = $this->credit->getById($creditId);
        $nodes = $this->loadModel('iwfp')->getCurrentVersionReviewNodes($creditInfo->workflowId, $creditInfo->version);


        $users = $this->loadModel('user')->getPairs('noletter');
        $this->view->actions  = $this->loadModel('action')->getList($this->config->credit->objectType, $creditId);
        //所属系统列表
        $appList = [];
        if($creditInfo->appIds){
            $appIds = array_filter(explode(',', $creditInfo->appIds));
            $appExWhere = " id In ( ".implode(',', $appIds).")";
            $appList =  $this->loadModel('application')->getPairs(0, $appExWhere);
        }

        //所属产品
        $productList = [];
        if($creditInfo->productIds){
            $productIds = array_filter(explode(',', $creditInfo->productIds));
            $productList = $this->loadModel('product')->getProductNamesByIds($productIds);
        }
        //项目
        $projectList = [];
        if($creditInfo->projectPlanId){
            $projectPlanId = array_filter(explode(',', $creditInfo->projectPlanId));
            $exWhere = " project In ( ".implode(',', $projectPlanId).")";
            $projectList = $this->loadModel('projectplan')->getAllProjects(false, $exWhere);
        }
        //任务单
        $secondorderList = [];
        if($creditInfo->secondorderIds){
            $secondorderIds = array_filter(explode(',', $creditInfo->secondorderIds));
            $exWhere = " id In ( ".implode(',', $secondorderIds).")";
            $secondorderList =  array('' => '') + $this->loadModel('secondorder')->getNameList($exWhere);
        }
        //问题单
        $problemList = [];
        if($creditInfo->problemIds){
            $problemIds = array_filter(explode(',', $creditInfo->problemIds));
            $exWhere = " id In ( ".implode(',', $problemIds).")";
            $problemList =  $this->loadModel('problem')->getPairsAbstract('noclosed', $exWhere);
        }
        //需求单
        $demandList = [];
        if($creditInfo->demandIds){
            $demandIds = array_filter(explode(',', $creditInfo->demandIds));
            $exWhere = " id In ( ".implode(',', $demandIds).")";
            $demandList = $this->loadModel('demand')->getPairsTitle('noclosed', $exWhere);
        }
        //获得流程图
        $reviewFlowInfo = new  stdClass();
        $flowImgInfo = '';
        if($creditInfo->workflowId){
            $reviewFlowInfo =  $this->loadModel('iwfp')->queryProcessTrackImage($creditInfo->workflowId);
            $flowImgInfo = $reviewFlowInfo->procBPMN;
        }
        //部门信息
        $deptInfo = $this->loadModel('dept')->getByID($creditInfo->createdDept);
        $this->view->credit = $creditInfo;
        $this->view->users = $users;
        $this->view->nodes = $nodes;
        $this->view->reviewFlowInfo = $reviewFlowInfo; //审核流程
        $this->view->flowImg = $flowImgInfo;
        $this->view->appList = $appList;
        $this->view->productList = $productList;
        $this->view->projectList = $projectList;
        $this->view->type = $type;
        $this->view->secondorderList = $secondorderList;
        $this->view->problemList = $problemList;
        $this->view->demandList = $demandList;
        $this->view->deptInfo = $deptInfo;

        $endStatusList = array_keys($this->lang->credit->endStatusList);
        $this->view->endStatusList = $endStatusList;
        $this->display();
    }

    /**
     * 创建投产
     *
     */
    public function create(){
        $this->view->title = $this->lang->credit->create;
        $objectType = $this->config->credit->objectType;
        $this->app->loadLang('modify');
        if($_POST)
        {
            $issubmit = $_POST['issubmit'];
            $creditID = $this->credit->create();
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $this->loadModel('action')->create($objectType, $creditID, 'created');
            $successType = $issubmit.'Success';
            $response['result']  = 'success';
            $response['message'] = $this->lang->$successType;
            $response['locate']  = inlink('browse');
            $response['id']      = $creditID;
            $this->send($response);
        }
        $demandLang = $this->app->loadLang('demand')->demand;
        $selectArray = array('' => '');
        $appExwhere = "isPayment = 3";
        $appList         =  $selectArray + $this->loadModel('application')->getPairs(0, $appExwhere);
        $secondorderList = $selectArray + $this->loadModel('secondorder')->getNameList();
        $abnormalList    =  $selectArray + $this->credit->getAllowBindAbnormalList();
        //关联问题
        $problemList     = $selectArray + $this->loadModel('problem')->getPairsAbstract('noclosed');
        //关联需求
        $demandList      = $selectArray + $this->loadModel('demand')->modifySelect($objectType);
        $reviewNodeUserList             = $this->credit->getReviewNodeUserList();
        $reviewerUsers                  = $this->loadModel('review')->getReviewerAccounts($reviewNodeUserList);  //审核节点下的审核人列表
        //所属项目
        $projectList = $selectArray + $this->loadModel('projectplan')->getAliveProjects(false);
        $this->view->projectList        = $projectList;
        $this->view->appList            = $appList;
        $this->view->secondorderList    = $secondorderList;
        $this->view->abnormalList       = $abnormalList;
        $this->view->problemList        = $problemList;
        $this->view->demandList         = $demandList;
        $this->view->reviewNodeUserList = $reviewNodeUserList;
        $this->view->reviewerUsers      = $reviewerUsers;
        $this->display();
    }

    /**
     * 编辑信息
     *
     * @param $creditId
     */
    public function edit($creditId)
    {
        $this->view->title = $this->lang->credit->edit;
        $objectType = $this->config->credit->objectType;
        $this->app->loadLang('modify');
        if($_POST)
        {
            $issubmit = $_POST['issubmit'];
            $changes = $this->credit->update($creditId);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $actionID = $this->loadModel('action')->create($objectType, $creditId, 'edited');
            $this->action->logHistory($actionID, $changes);
            $successType = $issubmit.'Success';
            $response['result']  = 'success';
            $response['message'] = $this->lang->$successType;
            $response['locate']  = inlink('browse');
            $response['locate']  = inlink('browse');
            $this->send($response);
        }
        $creditInfo = $this->credit->getById($creditId);
        $checkRes = $this->credit->checkIsAllowEdit($creditInfo, $this->app->user->account);
        if($checkRes['result']){
            $demandLang = $this->app->loadLang('demand')->demand;
            $selectArray = array('' => '');
            $appExwhere = "isPayment = 3";
            $appList         =  $selectArray + $this->loadModel('application')->getPairs(0, $appExwhere);
            $secondorderList = $selectArray + $this->loadModel('secondorder')->getNameList();
            $abnormalList    =  $selectArray + $this->credit->getAllowBindAbnormalList($creditId);
            //关联问题
            $problemList     = $selectArray + $this->loadModel('problem')->getPairsAbstract('noclosed','',array_filter(explode(',',$creditInfo->problemIds)));
            //关联需求
            $demandIds = array_filter(explode(',', $creditInfo->demandIds));
            $demandList = $selectArray + $this->loadModel('demand')->modifySelect($objectType, $creditId, 1, '', $demandIds);
            $reviewNodeUserList = $this->credit->getReviewNodeUserList($creditInfo->createdDept);
            $reviewerUsers = json_decode($creditInfo->reviewerInfo, true);  //审核节点下的审核人列表
            $this->view->appList            = $appList;
            $this->view->secondorderList    = $secondorderList;
            $this->view->abnormalList       = $abnormalList;
            $this->view->problemList        = $problemList;
            $this->view->demandList         = $demandList;
            $this->view->reviewNodeUserList = $reviewNodeUserList;
            $this->view->reviewerUsers      = $reviewerUsers;
        }
        $this->view->creditInfo = $creditInfo;
        $this->view->checkRes = $checkRes;
        $this->display();
    }

    /**
     * 复制操作
     *
     * @param $creditId
     */
    public function copy($creditId){
        $this->view->title = $this->lang->credit->copy;
        $objectType = $this->config->credit->objectType;
        $this->app->loadLang('modify');
        if($_POST)
        {
            $issubmit = $_POST['issubmit'];
            $creditID = $this->credit->create();
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $this->loadModel('action')->create($objectType, $creditID, 'created');
            $successType = $issubmit.'Success';
            $response['result']  = 'success';
            $response['message'] = $this->lang->$successType;
            $response['locate']  = inlink('browse');
            $response['id']      = $creditID;
            $this->send($response);
        }
        $creditInfo = $this->credit->getById($creditId);
        $demandLang = $this->app->loadLang('demand')->demand;
        $selectArray = array('' => '');
        $appExwhere = "isPayment = 3";
        $appList         =  $selectArray + $this->loadModel('application')->getPairs(0, $appExwhere);
        $secondorderList = $selectArray + $this->loadModel('secondorder')->getNameList();
        $abnormalList    =  $selectArray + $this->credit->getAllowBindAbnormalList($creditId);
        //关联问题
        $problemList     = $selectArray + $this->loadModel('problem')->getPairsAbstract('noclosed');
        //关联需求
        $demandList      = $selectArray + $this->loadModel('demand')->modifySelect($objectType, $creditId);
        $reviewNodeUserList             = $this->credit->getReviewNodeUserList();
        $reviewerUsers                  = $this->loadModel('review')->getReviewerAccounts($reviewNodeUserList);  //审核节点下的审核人列表
        $this->view->creditInfo         = $creditInfo;
        $this->view->appList            = $appList;
        $this->view->secondorderList    = $secondorderList;
        $this->view->abnormalList       = $abnormalList;
        $this->view->problemList        = $problemList;
        $this->view->demandList         = $demandList;
        $this->view->reviewNodeUserList = $reviewNodeUserList;
        $this->view->reviewerUsers      = $reviewerUsers;
        $this->display();
    }

    /**
     * 提交
     * @param $creditId
     */
    function submit($creditId){
        $this->view->title = $this->lang->credit->submit;
        $objectType = $this->config->credit->objectType;
        if($_POST){
            $dealResult = '1';
            $dealUser = $this->app->user->account;
            $changes = $this->credit->submit($creditId, $dealUser, $dealResult, $this->post->comment);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $actionID = $this->loadModel('action')->create($objectType, $creditId, 'submited', $this->post->comment);
            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
            
        }
        $info = $this->credit->getBasicInfoById($creditId);
        $checkRes = $this->credit->checkIsAllowSubmit($info, $this->app->user->account);
        if($checkRes['result']){
            $checkInfo = $this->credit->checkPostParamsInfo($info);
            $checkRes = [
                'result'  => $checkInfo['checkRes'],
                'message' => $checkInfo['errorData'],
            ];
        }
        $this->view->checkRes = $checkRes;
        $this->view->credit = $info;
        $this->display();
    }

    /**
     * 审批操作
     *
     * @param $creditId
     */
    public function review($creditId){
        if($_POST)
        {
            $this->credit->review($creditId);
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

        $this->view->title      = $this->lang->credit->review;
        $this->view->position[] = $this->lang->credit->review;
        $info = $this->credit->getBasicInfoById($creditId);
        $checkRes = $this->credit->checkIsAllowReview($info, $this->app->user->account);
        if($checkRes['result']){
            $this->view->reviewView = zget($this->lang->credit->reviewSpecialViewList, $info->status, 'reviewCommonDeal') .  '.html.php';
        }
        $this->view->creditInfo = $info;
        $this->view->checkRes = $checkRes;
        $this->display();
    }




    /**
     * @Notes:取消
     * @Date: 2024/1/9
     * @Time: 17:08
     * @Interface cancel
     * @param $id
     */
    public function cancel($creditId)
    {
        if($_POST)
        {
            $this->credit->cancel($creditId);
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

        $this->view->title      = $this->lang->credit->cancel;
        $this->view->position[] = $this->lang->credit->cancel;
        $info = $this->credit->getBasicInfoById($creditId);
        $checkRes = $this->credit->checkIsAllowCancel($info, $this->app->user->account);
        $this->view->info = $info;
        $this->view->checkRes = $checkRes;
        $this->display();
    }

    /**
     * 删除操作
     *
     * @param $creditId
     * @param $source
     */
    public function delete($creditId, $source = 'list')
    {
        if($_POST)
        {
            $changes = $this->credit->deleteInfo($creditId);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            //历史记录
            $objectType = $this->config->credit->objectType;
            $actionID = $this->loadModel('action')->create($objectType, $creditId, 'deleted', $this->post->remark);
            if($changes){
                $this->action->logHistory($actionID, $changes);
            }

            if(isonlybody())
            {
                if($source == 'view'){ //详情页删除以后不存在，返回列表页
                    die(js::closeModal('parent.parent', $this->session->common_back_url));
                }else{
                    die(js::closeModal('parent.parent', 'this'));
                }
            }
            else{
                die(js::locate(inLink('browse'),'parent.parent'));
            }
            die(js::reload('parent'));
        }
        $this->view->title      = $this->lang->credit->delete;
        $this->view->position[] = $this->lang->credit->delete;
        $info = $this->credit->getByID($creditId);
        $checkRes = $this->credit->checkIsAllowDelete($info, $this->app->user->account);

        $actions  = $this->loadModel('action')->getList($this->config->credit->objectType, $creditId);
        $users = $this->loadModel('user')->getPairs('noletter');
        $this->view->info = $info;
        $this->view->checkRes = $checkRes;
        $this->view->actions = $actions;
        $this->view->users = $users;
        $this->display();
    }


    /**
     * 导出
     *
     * @param string $orderBy
     */
    public function export($orderBy = 'code_desc'){
        $this->app->loadLang('modify');
        if($_POST)
        {
            $creditLang   = $this->lang->credit;
            $creditConfig = $this->config->credit;

            /* Create field lists. */
            $fields = $this->post->exportFields ? $this->post->exportFields : explode(',', $creditConfig->list->exportFields);
            foreach($fields as $key => $fieldName)
            {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = isset($creditLang->$fieldName) ? $creditLang->$fieldName : $fieldName;
                unset($fields[$key]);
            }
            /* Get changes. */
            $credits = array();
            if($this->session->creditOnlyCondition)
            {
                $credits = $this->dao->select('*')->from(TABLE_CREDIT)->where($this->session->creditQueryCondition)
                    ->beginIF($this->post->exportType == 'selected')->andWhere('id')->in($this->cookie->checkedItem)->fi()
                    ->orderBy($orderBy)->fetchAll('id');

            }
            else
            {
                $stmt = $this->dbh->query($this->session->creditQueryCondition . ($this->post->exportType == 'selected' ? " AND id IN({$this->cookie->checkedItem})" : '') . " ORDER BY " . strtr($orderBy, '_', ' '));
                while($row = $stmt->fetch()) $credits[$row->id] = $row;
            }

            if($credits){
                $users = $this->loadModel('user')->getPairs('noletter');
                $projectList  =  $this->loadModel('projectplan')->getAllProjects();
                //产品
                $products = $this->loadModel('product')->getList();
                $productList =  array_column($products, 'name' , 'id');
                //系统
                $appExwhere = "isPayment = 3";
                $appList  =  array('' => '') + $this->loadModel('application')->getPairs(0, $appExwhere);
                //工单
                $secondorderList =  array('' => '') + $this->loadModel('secondorder')->getNameList();
                //关联问题
                $problemList     = array('' => '') + $this->loadModel('problem')->getPairsAbstract();
                //关联需求
                $demandList  =  $this->loadModel('demand')->getPairsTitle('noclosed');
                //关联的异常变更单
                $abnormalList =  $this->credit->getAbnormalList();
                if($abnormalList){
                    $abnormalList = array_column($abnormalList, null, 'abnormalId');
                }
                //部门列表
                $deptList  = $this->loadModel('dept')->getTopPairs();

                foreach($credits as $val) {
                    $id = $val->id;
                    if(isset($abnormalList[$id])){
                        $abnormalInfo = $abnormalList[$id];
                        $val->abnormalId = $abnormalInfo->code;
                    }else{
                        $val->abnormalId = '';
                    }
                    $val->appIds             = zmget($appList, $val->appIds);
                    $val->productIds         = zmget($productList, $val->productIds);
                    $val->implementationForm = zget($creditLang->implementationFormList, $val->implementationForm);
                    $val->projectPlanId      = zget($projectList, $val->projectPlanId);
                    $val->status             = zget($creditLang->statusList, $val->status);
                    $val->dealUsers          = zmget($users, $val->dealUsers);
                    $val->createdBy          = zget($users, $val->createdBy);
                    $val->createdDept        = trim(zget($deptList, $val->createdDept), '/');
                    $val->secondorderIds     = zmget($secondorderList, $val->secondorderIds);
                    $val->problemIds         = zmget($problemList, $val->problemIds);
                    $val->demandIds          = zmget($demandList, $val->demandIds);
                    $val->level              = zget($creditLang->levelList, $val->level);
                    $val->changeNode         = zmget($creditLang->changeNodeList, $val->changeNode);
                    $val->changeSource       = zmget($creditLang->changeSourceList, $val->changeSource);
                    $val->mode               = zget($creditLang->modeList, $val->mode);
                    $val->type               = zmget($creditLang->typeList, $val->type);
                    $val->executeMode        = zmget($creditLang->executeModeList, $val->executeMode);
                    $val->emergencyType      = zget($creditLang->emergencyTypeList, $val->emergencyType);
                    $val->isBusinessAffect   = zget($creditLang->isBusinessAffectList, $val->isBusinessAffect);

                    // 手否后补流程、实际交付时间
                    $val->actualDeliveryTime = $val->isMakeAmends == 'yes' ? $val->actualDeliveryTime : '';
                    $val->isMakeAmends = zget($this->lang->modify->isMakeAmendsList,$val->isMakeAmends,'');

                    if ($val->riskAnalysisEmergencyHandle){
                        $riskAnalysisEmergencyHandle = json_decode($val->riskAnalysisEmergencyHandle);
                        $riskMsg = '';
                        foreach ($riskAnalysisEmergencyHandle as $key => $temp){
                            $keyVal = $key + 1;
                            $riskMsg =  $riskMsg.$keyVal.'、【'.$creditLang->riskAnalysis.'】'.$temp->riskAnalysis.';';
                            $riskMsg =  $riskMsg.' 【'.$creditLang->emergencyBackWay.'】'.$temp->emergencyBackWay."\r\n";
                        }
                        $val->riskAnalysisEmergencyHandle = $riskMsg;
                    }
                }

            }

            $this->post->set('fields', $fields);
            $this->post->set('rows', $credits);
            $this->post->set('kind', 'credit');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }
        $this->view->fileName        = $this->lang->credit->exportName;
        $this->view->allExportFields = $this->config->credit->list->exportFields;
        $this->view->customExport    = true;
        $this->display();
    }

    /**
     * 查看历史审批意见
     *
     * @param $id
     */
    function showHistoryNodes($id){
        $this->view->title = $this->lang->credit->showHistoryNodes;
        $creditInfo = $this->credit->getBasicInfoById($id);
        $nodes = $this->loadModel('iwfp')->getAllVersionReviewNodes($creditInfo->workflowId);
        if($nodes){
            $users =  $this->loadModel('user')->getPairs('noletter');
            $this->view->users = $users;
        }
        $this->view->nodes = $nodes;
        $this->view->creditInfo = $creditInfo;
        $this->display();
    }


    /**
     * 编辑流程节点
     *
     * @param int $creditId
     * @param int $consumedId
     */
    public function workloadedit($creditId = 0, $consumedId = 0){
        $creditInfo = $this->credit->getByID($creditId);
        if($_POST)
        {
            $this->credit->workLoadEdit($creditInfo, $consumedId);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $actionID = $this->loadModel('action')->create('credit', $creditId, 'workloadedit', $this->post->comment);
            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }

        $this->view->title          = $this->lang->credit->workloadedit;
        $this->view->position[]     = $this->lang->credit->workloadedit;
        $consumedList = $creditInfo->consumed;

        $consumedList = array_column($consumedList, null, 'id');
        $consumedChose = zget($consumedList, $consumedId);
        //是否是最后一条
        $isLast = $this->loadModel('consumed')->checkIsLastConsumed($consumedId, $creditInfo->id, 'credit');
        if($isLast){
            $afterEnableChoseStatus = array(
                $consumedChose->after => zget($this->lang->credit->statusList, $consumedChose->after),
            );
            if(!empty($creditInfo->workflowId)){
                $statusList = $this->loadModel('iwfp')->getFreeJumpNodeList($creditInfo->workflowId);
                foreach ($statusList as $status){
                    $afterEnableChoseStatus[$status->xmlTaskId] = $status->xmlTaskName;
                }
            }
            $afterEnableChoseStatus = array_filter(array_filter($afterEnableChoseStatus));
            $this->view->afterEnableChoseStatus = $afterEnableChoseStatus;
        }else{
            $this->view->afterEnableChoseStatus = $this->lang->credit->statusList;
        }
        $this->view->consumed = $consumedChose;
        //操作前可选择状态
        $beforeEnableChoseStatus = array('' => '') + $this->lang->credit->statusList;
        $this->view->beforeEnableChoseStatus = $beforeEnableChoseStatus;
        $this->display();
    }

    /**
     * 根据异常变更单获取该变更单关联的问题单、需求条目
     *
     * @param $id
     * @param $isAbnormal
     */
    public function ajaxGetInfoByAbnormalId($id = 0, $isAbnormal = 1, $creditId = 0){
        $demandLang = $this->app->loadLang('demand')->demand;
        $objectType = $this->config->credit->objectType;
        $select = 'id,problemIds,demandIds,secondorderIds';
        $info = $this->credit->getBasicInfoById($id, $select);
        $str = '';
        $problemExWhere     = '';
        $demandExWhere      = '';
        $secondorderExWhere = '';
        if ($isAbnormal == 1){
            $str = 'disabled';
            $problemIds     = "''";
            $demandIds      = "''";
            $secondorderIds = "''";
            if($info->problemIds){
                $problemIds = trim($info->problemIds, ',');
            }
            if($info->demandIds){
                $demandIds = trim($info->demandIds, ',');
            }
            if($info->secondorderIds){
                $secondorderIds = trim($info->secondorderIds, ',');
            }
            $problemExWhere = 'id IN  ('.$problemIds.')';
            $demandExWhere = 'id IN  ('.$demandIds.')';
            $secondorderExWhere = 'id IN  ('.$secondorderIds.')';
        }

        //关联问题
        $tempDemandIds = [];
        if($creditId){
            $creditInfo = $this->credit->getBasicInfoById($creditId, $select);
            if(isset($creditInfo->demandIds) && $creditInfo->demandIds){
                $tempDemandIds = array_filter(explode(',', $creditInfo->demandIds));
            }
        }
        $problemList     = array('' => '') + $this->loadModel('problem')->getPairsAbstract('noclosed', $problemExWhere);
        $demandList      = array('' => '') + $this->loadModel('demand')->modifySelect($objectType, 0, 1, $demandExWhere, $tempDemandIds);
        $secondorderList = array('' => '') + $this->loadModel('secondorder')->getNameList($secondorderExWhere);

        $data = [];
        if ($info){
            $data[0] = html::select('demandIds[]', $demandList, $info->demandIds, "class='form-control chosen' multiple $str");;
            $data[1] = html::select('problemIds[]', $problemList, $info->problemIds,"class='form-control chosen' multiple $str");
            $data[2] = html::select('secondorderIds[]', $secondorderList, $info->secondorderIds,"class='form-control chosen' multiple $str");
        }else{
            $data[0] = html::select('demandIds[]', $demandList, [], "class='form-control chosen' multiple $str");;
            $data[1] = html::select('problemIds[]', $problemList, [],"class='form-control chosen' multiple $str");
            $data[2] = html::select('secondorderIds[]', $secondorderList, [],"class='form-control chosen' multiple $str");
        }
        echo json_encode($data);
    }

    /**
     * 编辑是否取消状态联动
     *
     * @param int $creditId
     */
    function editSecondorderCancelLinkage($creditId = 0){
        $objectType = $this->config->credit->objectType;
        if($_POST)
        {
            $changes = $this->credit->editSecondorderCancelLinkage($creditId);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $actionID = $this->loadModel('action')->create($objectType, $creditId, 'editsecondordercancellinkage', $this->post->comment);
            $this->action->logHistory($actionID, $changes);
            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }
        $this->view->title      = $this->lang->credit->editSecondorderCancelLinkage;
        $this->view->position[] = $this->lang->credit->editSecondorderCancelLinkage;
        $select = 'id,code,status,secondorderIds, secondorderCancelLinkage';
        $creditInfo =   $this->credit->getBasicInfoById($creditId, $select);
        $this->view->info = $creditInfo;
        $this->display();
    }

}
