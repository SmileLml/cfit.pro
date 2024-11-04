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
class qualitygate extends control
{

    /**
     * 列表页
     *
     * @param int $projectId
     * @param string $browseType
     * @param int $param
     * @param string $orderBy
     * @param int $recTotal
     * @param int $recPerPage
     * @param int $pageID
     */
    public function browse($projectId = 0, $browseType = 'all', $param = 0,  $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        // 配置项目头部导航栏
        $this->loadModel('project')->setMenu($projectId);

        $this->view->title = $this->lang->qualitygate->browse;
        $browseType = strtolower($browseType);
        $users = array('' => '') + $this->loadModel('user')->getPairs('noletter');
        //产品
        $products = $this->loadModel('product')->getList();
        $productList = array('' => '') +  array_column($products, 'name' , 'id');
        /* 产品版本列表 */
        $plans = $this->loadModel('productplan')->getSimplePairs();

        //制版状态
        $buildstatusList = $this->loadModel('build')->lang->build->statusList;
        $index = array_search('-', $buildstatusList); // 查找 '-' 在数组中的位置
        if ($index !== false) {
            $buildstatusList[$index] = ''; // 将找到的元素设置为空字符串
        }
        $firstElement = [''=>''];   // 数组第一个元素为空，搜索框重置按钮清除选定内容
        $this->config->qualitygate->search['params']['buildStatus']['values'] = array_merge($firstElement, $buildstatusList);
        // search
        $queryID = ($browseType == 'bysearch')  ? (int)$param : 0;
        $actionURL = $this->createLink('qualitygate', 'browse', "projectId=$projectId&browseType=bySearch&param=myQueryID");
        $this->qualitygate->buildSearchForm($queryID, $actionURL);
        /* Set session. */
        $this->session->set('qualitygateList', $this->app->getURI(true) . '#app=' . $this->app->openApp, $this->app->openApp);
        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);
        $data = $this->qualitygate->getList($projectId, $browseType, $queryID, $orderBy, $pager);

        $this->view->projectId  = $projectId;
        $this->view->productList  = $productList;
        $this->view->productVersionList = array('' => '', '1 '=> '无') + $plans;
        $this->view->buildstatusList = $buildstatusList;
        $this->view->orderBy    = $orderBy;
        $this->view->pager      = $pager;
        $this->view->param      = $param;
        $this->view->browseType = $browseType;
        $this->view->users   = $users;
        $this->view->data = $data;
        $this->display();
    }


    /**
     * 详情
     *
     * @param $qualityGateId
     */
    public function view($qualityGateId)
    {
        $this->view->title = $this->lang->qualitygate->view;
        $qualityGateId = (int)$qualityGateId;
        $qualityGate = $this->qualitygate->getById($qualityGateId);
        $projectId = $qualityGate->projectId;
        // 配置项目头部导航栏
        $this->loadModel('project')->setMenu($projectId);
        //制版状态
        $buildStatusList = $this->loadModel('build')->lang->build->statusList;
        /* Update action. */
        $this->loadModel('action')->read('qualitygate', $qualityGateId);
        $this->view->buildstatusList = $buildStatusList;

        $this->view->deptInfo = $this->loadModel('dept')->getFieldByDeptId('id,name', $qualityGate->createdDept);
        $this->view->actions = $this->action->getList('qualitygate', $qualityGateId);
        $this->view->users = $this->loadModel('user')->getPairs('noletter|noclosed');
        $this->view->qualitygate = $qualityGate;
        $this->display();
    }



    /**
     * 创建
     * @param $projectId
     * @param $productId
     *
     */
    public function create($projectId, $productId = 0){
        // 配置项目头部导航栏
        $this->loadModel('project')->setMenu($projectId);
        $currentUser = $this->app->user->account;
        $this->view->title = $this->lang->qualitygate->create;
        if($_POST)
        {
            $postData = fixer::input('post')->get();
            $postData->projectId = $projectId;
            $recordId = $this->qualitygate->create($postData, $projectId);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $params = "projectId={$projectId}";
            $response['locate']  = inlink('browse', $params);
            $response['id']       = $recordId;
            $this->send($response);
        }

        $isAllowCreate = $this->qualitygate->checkIsAllowCreate($projectId, $currentUser);
        if($isAllowCreate){ //允许创建
            $productsList = $this->loadModel('project')->getProductList($projectId);
            //安全测试工程师
            $severityTestUsers = $this->qualitygate->getProjectTeamSeverityUsers($projectId, true);
            $severityGateResult = $this->qualitygate->getSeverityGateResult($projectId, $productId, 0, 0);

            $this->view->productList = array('0' => '') + $productsList;
            $this->view->severityTestUsers = array('' => '') + $severityTestUsers;
            $this->view->severityGateResult = $severityGateResult;
            $this->view->productVersionList = array('' => '', '1 '=> '无');
        }
        $this->view->isAllowCreate = $isAllowCreate;
        $this->view->projectId = $projectId;
        $this->view->productId = $productId;
        $this->display();
    }

    /**
     * 编辑信息
     *
     * @param $qualityGateId
     */
    public function edit($qualityGateId){
        $currentUser = $this->app->user->account;
        $this->view->title = $this->lang->qualitygate->edit;
        if($_POST)
        {
            $postData = fixer::input('post')->get();
            $recordId = $this->qualitygate->update($qualityGateId, $postData);
            if(dao::isError()) {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }
        //获得信息
        $info = $this->qualitygate->getBasicInfoById($qualityGateId);
        $projectId = $info->projectId;
        $productId = $info->productId;
        $productVersion = $info->productVersion;
        $checkRes = $this->qualitygate->checkIsAllowEdit($info, $currentUser);
        if($checkRes['result']){ //允许编辑
            $productsList = $this->loadModel('project')->getProductList($projectId);
            //安全测试工程师
            $severityTestUsers = $this->qualitygate->getProjectTeamSeverityUsers($projectId, true, $qualityGateId);
            $severityGateResult = $this->qualitygate->getSeverityGateResult($projectId, $productId, $productVersion, $info->buildId);

            $this->view->productList = array('0' => '') + $productsList;
            $this->view->severityTestUsers = array('' => '') + $severityTestUsers;
            $this->view->severityGateResult = $severityGateResult;
            $plans = $this->loadModel('productplan')->getPairs($productId, 0);
            $this->view->productVersionList = array('' => '', '1 '=> '无') + $plans;
        }
        $this->view->checkRes = $checkRes;
        $this->view->projectId = $projectId;
        $this->view->productId = $productId;
        $this->view->info = $info;
        $this->display();
    }

    /**
     * 获得产品版本
     *
     * @param int $productID
     */
    public function ajaxGetProductVersion($productID = 0)
    {
        $plans = $this->loadModel('productplan')->getPairs($productID, 0);
        $planName = 'productVersion';
        $plans    =  array('' => '', '1 '=> '无') + $plans;
        $data =  html::select($planName, $plans, '', "class='form-control  chosen' onchange='changeProductVersion();'");
        echo json_encode($data);
    }

    /**
     * 获得安全门禁结果
     *
     * @param $projectId
     * @param int $productId
     * @param $productVersion
     * @param $buildId
     * @return mixed
     */
    public function ajaxGetSeverityGateResult($projectId, $productId = 0, $productVersion = 0, $buildId = 0){
        $severityGateResult = $this->qualitygate->getSeverityGateResult($projectId, $productId, $productVersion, $buildId);
        echo $this->qualitygate->diffSeverityGateResult($severityGateResult);
    }

    /**
     * Project: chengfangjinke
     * Method: assignedTo
     * @param $qualitygateId
     */
    public function assignedTo($qualitygateId)
    {
        $qualitygate = $this->qualitygate->getById($qualitygateId);

        if(!empty($_POST))
        {
            $errorMsg = $this->qualitygate->isAssigned($qualitygate);
            if(!empty($errorMsg)){
                $response['result']  = 'fail';
                $response['message'] = $errorMsg;
                $this->send($response);
            }

            $dealUser = $this->post->dealUser;
            if(empty($dealUser)){
                $response['result']  = 'fail';
                $response['message'] = sprintf($this->lang->qualitygate->emptyObject, $this->lang->qualitygate->dealUser);
                $this->send($response);
            }
            if($dealUser == $qualitygate->dealUser){
                $response['result']  = 'fail';
                $response['message'] = $this->lang->qualitygate->assignToFail;
                $this->send($response);
            }

            $data = new stdClass();
            $data->dealUser = $dealUser;

            $res = $this->loadModel('iwfp')->changeAssigneek($qualitygate->workflowId, $qualitygate->dealUser, $dealUser, $this->lang->qualitygate->approvalVersion);
            if(dao::isError()) {
                $response['result']  = 'fail';
                $response['message'] = 'iwfp:'.$res;
                $this->send($response);
            }

            $this->dao->update(TABLE_QUALITYGATE)->data($data)
                ->where('id')->eq($qualitygateId)
                ->exec();

            $changes = common::createChanges($qualitygate, $data);
            $this->loadModel('action');
            $actionId = $this->action->create('qualitygate', $qualitygateId, 'assigned', $this->post->comment, $dealUser, '', true, true, $changes);

            // 发送邮件
            //$this->qualitygate->sendmail($qualitygateId, $actionId);

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }

        $this->view->title     = $this->lang->qualitygate->assignedTo;
        $this->view->users     = $this->loadModel('user')->getPairs('nodeleted|nofeedback', $qualitygate->dealUser);
        $this->view->assignToUsers     = $this->qualitygate->getProjectTeamSeverityUsers($qualitygate->projectId, true, $qualitygateId);
        $this->view->qualitygate   = $qualitygate;
        $this->view->qualitygateId = $qualitygateId;
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: deal
     * @param $qualitygateId
     */
    public function deal($qualitygateId)
    {
        $qualitygate = $this->qualitygate->getById($qualitygateId);

        if(!empty($_POST))
        {
            if (isset($qualitygate->status)) {
                if (!in_array($qualitygate->status, $this->lang->qualitygate->allowDealStatusArr)) {
                    $response['result']  = 'fail';
                    $response['message'] = $this->lang->qualitygate->canNotChanged;
                    $this->send($response);
                }
            }
            $changes = $this->qualitygate->deal($qualitygateId);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            if(!$changes) {
                $response['result']  = 'fail';
                $response['message'] = $this->lang->qualitygate->unchanged;
                $this->send($response);
            }
            $this->loadModel('action');
            $actionId = $this->action->create('qualitygate', $qualitygateId, 'deal');
            if($changes) $this->action->logHistory($actionId, $changes);

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }

        $this->view->title     = $this->lang->qualitygate->todeal;
        $this->view->qualitygate   = $qualitygate;
        $this->view->qualitygateId = $qualitygateId;
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: delete
     * @param $qualitygateId
     */
    public function delete($qualitygateId) {
        if(!empty($_POST))
        {
            $this->dao->update(TABLE_QUALITYGATE)->set('deleted')->eq('1')->where('id')->eq($qualitygateId)->exec();
            $this->loadModel('action')->create('qualitygate', $qualitygateId, 'deleted', $this->post->comment);
            if(isonlybody()) die(js::closeModal('parent.parent', 'this'));
            die(js::reload('parent'));
//            die(js::reload('parent.parent'));
        }

        $qualitygate = $this->qualitygate->getByID($qualitygateId);
        $this->view->actions = $this->loadModel('action')->getList('qualitygate', $qualitygateId);
        $this->view->qualitygate = $qualitygate;
        $this->view->users   = $this->loadModel('user')->getPairs();
        $this->display();
    }
}
