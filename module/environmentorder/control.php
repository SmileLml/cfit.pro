<?php

class environmentorder extends control
{
    /**
     * Project: chengfangjinke
     * Method: browse
     * @param $browseType
     * @param $param
     * @param $orderBy
     * @param $recTotal
     * @param $recPerPage
     * @param $pageID
     */
    public function browse($browseType = 'all', $param = 0, $orderBy = 'priority_desc,id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $browseType = strtolower($browseType);

        /* By search. */
        $firstElement = [''=>''];   // 数组第一个元素为空，搜索框重置按钮清除选定内容
        $this->config->environmentorder->search['params']['reviewer']['values'] = array_merge($firstElement, $this->lang->environmentorder->reviewerList);
        $this->config->environmentorder->search['params']['createdBy']['values'] = array_merge($firstElement, $this->lang->environmentorder->createByList);
        $this->config->environmentorder->search['params']['executor']['values'] = array_merge($firstElement, $this->lang->environmentorder->executorList);
        $this->config->environmentorder->search['params']['dealUser']['values'] = array_merge($firstElement, $this->lang->environmentorder->reviewerList, $this->lang->environmentorder->createByList, $this->lang->environmentorder->executorList);
        $this->config->environmentorder->search['params']['editedBy']['values'] = array_merge($firstElement, $this->lang->environmentorder->reviewerList);

        $queryID = ($browseType == 'bysearch') ? (int)$param : 0;
        $actionURL = $this->createLink('environmentorder', 'browse', "browseType=bySearch&param=myQueryID");

        $this->environmentorder->buildSearchForm($queryID, $actionURL);

        /* 设置详情页面返回的url连接。*/
        $this->session->set('environmentorderList', $this->app->getURI(true));
        $this->session->set('common_back_url', $this->app->getURI(true),'backlog');

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);
        $environmentorders = $this->environmentorder->getList($browseType, $queryID, $orderBy, $pager);
        $this->view->title = $this->lang->environmentorder->browse;
        $this->view->orderBy = $orderBy;
        $this->view->pager = $pager;
        $this->view->param = $param;
        $this->view->browseType = $browseType;
        $this->view->users = $this->loadModel('user')->getPairs('noletter|noclosed');
        $this->view->environmentorders = $environmentorders;
        $this->session->set('common_back_url', $this->app->getURI(true),'backlog');
        $this->display();
    }
    /**
     * Project: chengfangjinke
     * Method: view
     * @param $environmentorderID
     */
    public function view($environmentorderId = 0,$type="form")
    {
        $baseInfo = $this->environmentorder->getByID($environmentorderId);
        $iwfpModel          = $this->loadModel('iwfp');
        $this->view->title = $this->lang->environmentorder->view;
        $this->view->users = $this->loadModel('user')->getPairs('noletter|noclosed');
        $actions= $this->loadModel('action')->getList($this->config->environmentorder->objectType, $environmentorderId);
        $this->view->environmentorder = $baseInfo;
        $this->view->actions =$actions;
        $nodes = $iwfpModel->getCurrentVersionReviewNodes($baseInfo->processInstanceId, $baseInfo->version);
        $this->view->nodes          = $nodes;
        //获得流程图
        $reviewFlowInfo = new  stdClass();
        $flowImgInfo = '';
        if($baseInfo->processInstanceId){
            $reviewFlowInfo =  $this->loadModel('iwfp')->queryProcessTrackImage($baseInfo->processInstanceId);
            $flowImgInfo = $reviewFlowInfo->procBPMN;
        }
        $this->view->flowImg = $flowImgInfo;
        $this->view->reviewFlowInfo = $reviewFlowInfo; //审核流程
        $this->view->type = $type;
        $this->display();
    }
   public  function showHistoryNodes($environmentorderID){
        $this->view->title = $this->lang->environmentorder->showHistoryNodes;
        $environmentorderInfo = $this->environmentorder->getByID($environmentorderID);
        $nodes = $this->loadModel('iwfp')->getAllVersionReviewNodes($environmentorderInfo->processInstanceId);
        if($nodes){
            $users =  $this->loadModel('user')->getPairs('noletter');
            $this->view->users = $users;
        }
        $this->view->nodes = $nodes;
        $this->view->info = $environmentorderInfo;
        $this->display();
    }
    public function create()
    {
        $this->view->title = $this->lang->environmentorder->create;
        if ($_POST) {
            $recordId = $this->environmentorder->create();
            if (dao::isError()) {
                $response['result'] = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $response['result'] = 'success';
            $response['message'] = $this->lang->saveSuccess;;
            $response['locate'] = inlink('browse');
            $response['id'] = $recordId;
            $this->send($response);
        }

        $this->display();
    }
    /**
     * 编辑信息
     *
     * @param $environmentorderId
     */
    public function edit($environmentorderId,$source="list")
    {
        $this->view->title = $this->lang->environmentorder->edit;
        $info = $this->environmentorder->getById($environmentorderId);

        if($_POST) {
            $ret = $this->environmentorder->update($environmentorderId,$info);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $url = $this->session->common_back_url ? $this->session->common_back_url : inLink('browse');
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;;
            $response['locate']  = $url;
            $response['id']       = $environmentorderId;
            $this->send($response);
        }
        $this->view->info = $info;
        $this->view->source = $source;
        $this->display();
    }
    /**
     * 删除操作
     *
     * @param $environmentorderId
     * @param $source
     */
    public function delete($environmentorderId, $confirm = 'no',$source = 'list')
    {
        $cancelURL  = $this->server->HTTP_REFERER;
        if($confirm == 'no') {
            echo js::confirm("确定删除吗？", $this->createLink('environmentorder', 'delete', "environmentorderId=$environmentorderId&confirm=yes"), $cancelURL,'parent', 'parent');
            exit;
        }
        else {
            $info = $this->environmentorder->getByID($environmentorderId);
            $checkRes = $this->environmentorder->checkIsAllowDelete($info, $this->app->user->account);
            if(!$checkRes['result']){
                echo js::alert($checkRes['message']);
                exit;
            }else{
                $this->environmentorder->deleteData($info);
                if(dao::isError())
                {
                    $response['result']  = 'fail';
                    $response['message'] = dao::getError();
                    $this->send($response);
                }
                //历史记录
                $objectType = $this->config->environmentorder->objectType;
                $actionID = $this->loadModel('action')->create($objectType, $environmentorderId, 'deleted');
                die(js::locate($this->session->common_back_url ));
            }
        }
    }
            public function deal($environmentorderId)
            {
                $info = $this->environmentorder->getByID($environmentorderId);

                $this->view->info = $info;
//                审核
                if (in_array($info->status, $this->lang->environmentorder->allowApprovalStatusArray)){
                    if($_POST){
//                        进行审批
                        $ret=$this->environmentorder->approval($info);
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
//                    $user=$this->environmentorder->getImplementUser();
//                    $this->view->executor=$user;
                    $this->display('environmentorder','approval');
                }
//                任务确认
                elseif (in_array($info->status, $this->lang->environmentorder->allowConfirmStatusArray)){
                    if($_POST){
//                        确认执行的任务
                        $ret=$this->environmentorder->confirm($info);
                        if(dao::isError())
                        {
                            $response['result']  = 'fail';
                            $response['message'] = dao::getError();
//                            a($response['message']);die;
                            $this->send($response);
                        }
                        $response['result']  = 'success';
                        $response['message'] = $this->lang->submitSuccess;
                        $response['locate']  = 'parent';
                        $this->send($response);

                    }
                    // 指派的人员，去除以及在执行中的人
                    $user=$this->lang->environmentorder->executorList;
                    $executor=explode(',',$info->executor);
                    foreach ($user as $k=>$v){
                        foreach ($executor as $k1=>$v1){
                                if($k==$v1){
                                    unset($user[$k]);
                                }
                        }
                    }
                    $this->view->executor=$user;
                    $this->display('environmentorder','confirm');
                }
                //                任务实施
                elseif (in_array($info->status, $this->lang->environmentorder->allowImplementStatusArray)){
                    if($_POST){
//                        确认执行的任务
                        $ret=$this->environmentorder->implement($info);
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
                    $this->display('environmentorder','implement');
                }
//                任务核验
                elseif (in_array($info->status, $this->lang->environmentorder->allowVerifyStatusArray)){
                    if($_POST){
//                        核验执行的任务
                        $ret=$this->environmentorder->verify($info);
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
                    $this->display('environmentorder','verify');
                }
            }
    /**
     * 提交
     * @param $environmentorderId
     */
    function submit($environmentorderId, $confirm = 'no',$source='list'){
        $cancelURL  = $this->server->HTTP_REFERER;
        if($confirm == 'no') {
            echo js::confirm($this->lang->environmentorder->submitConfirm, $this->createLink('environmentorder', 'submit', "environmentorderId=$environmentorderId&confirm=yes&source=view"), $cancelURL,'self', 'parent');
            exit;
        } else {
            $ret = $this->environmentorder->submit($environmentorderId);
            if (dao::isError()) {
                $response['result'] = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            if($source=='view'){
                die(js::reload('parent'));
            }
            die(js::locate($this->session->common_back_url ));
        }
    }

}