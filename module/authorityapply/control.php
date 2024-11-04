<?php

class authorityapply extends control
{
    public function __construct()
    {
        parent::__construct();
        $this->svnAuthority = $this->loadModel('myauthority')->getAuthorizeUrl('svn');
        $this->jenkinsAuthority = $this->loadModel('myauthority')->getAuthorizeUrl('jenkins');
        $this->gitlabAuthority = $this->loadModel('myauthority')->getAuthorizeUrl('gitlab');
        $this->appList = array('' => '') + $this->loadModel('application')->getapplicationNameCodePairs();
        $this->deptList = $this->loadModel('dept')->getDeptPairs();
        $this->projectList = array('' => '') + $this->loadModel('project')->getPairsCodeName();
        $userList = array('' => '') + $this->loadModel('user')->getAllUserList();
        $this->userList = array_column($userList, 'realname', 'account');
        $svnPermission = [];
        $gitLabPermission = [];
        $jenkinsPermission = [];
        foreach ($this->lang->authorityapply->svnPermission as $k => $v) {
            $object = [
                "value" => $k,
                "name" => $v,
            ];
            $svnPermission[] = $object;
        }
        $this->svnPermission = json_encode($svnPermission, JSON_UNESCAPED_UNICODE);

        foreach ($this->lang->authorityapply->gitLabPermission as $k => $v) {
            $object = [
                "value" => $k,
                "name" => $v,
            ];
            $gitLabPermission[] = $object;
        }
        $this->gitLabPermission = json_encode($gitLabPermission, JSON_UNESCAPED_UNICODE);

        foreach ($this->lang->authorityapply->jenkinsPermission as $k => $v) {
            $object = [
                "value" => $k,
                "name" => $v,
            ];
            $jenkinsPermission[] = $object;
        }
        $this->jenkinsPermission = json_encode($jenkinsPermission, JSON_UNESCAPED_UNICODE);
//        $selflURL = $this->server->HTTP_REFERER;
//        $dipanUrl = $this->createLink('my-work-audit', 'authorityapply');
        $this->returnUrl = $this->createLink('authorityapply', 'browse');
        if(strstr($this->session->common_back_url,'myQueryID')){
            $this->returnUrl=$this->session->common_back_url;
        }
//        if (strstr($selflURL, 'work')) {
//            $this->returnUrl = $dipanUrl;
//        }
    }

    /**
     * 我的权限列表
     */
    public function browse($browseType = 'all', $param = 0, $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $this->config->authorityapply->search['params']['dealUser'] = ['operator' => 'include', 'control' => 'select', 'values' => array(0 => '') + $this->userList];
        $this->config->authorityapply->search['params']['createdBy'] = ['operator' => '=', 'control' => 'select', 'values' => array(0 => '') + $this->userList];
        $browseType = strtolower($browseType);
        $queryID = ($browseType == 'all') ? (int)$param : 0;
        $actionURL = $this->createLink('authorityapply', 'browse', "browseType=bySearch&param=myQueryID");
        $this->authorityapply->buildSearchForm($queryID, $actionURL);
        $this->session->set('common_back_url', $this->app->getURI(true), 'backlog');
        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);
        $this->view->deptList = $this->deptList;
        $this->view->userList = $this->userList;
        $this->view->title = $this->lang->authorityapply->common;
        $this->view->subSystem = $this->lang->authorityapply->subSystem;
        $this->view->orderBy = $orderBy;
        $this->view->pager = $pager;
        $this->view->pageID = $pageID;
        $this->view->param = $param;
        $this->view->browseType = $browseType;
        $this->view->toDealCount = $this->authorityapply->getTodealAuthorityapplyCount();
        $listData = $this->authorityapply->getList($browseType, $queryID, $orderBy, $pager);
        $this->view->listData = $listData;
        $this->view->returnUrl = $this->returnUrl;

        $this->display();
    }

    public function create()
    {
        $this->view->title = $this->lang->authorityapply->create;
        if ($_POST) {
            $recordId = $this->authorityapply->create();
            if (dao::isError()) {
                $response['result'] = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $response['result'] = 'success';
            $response['message'] = $this->lang->saveSuccess;;
            $response['locate'] = $this->returnUrl;
            $response['id'] = $recordId;
            $this->send($response);
        }

        $this->view->svnAuthority = $this->svnAuthority;
        $this->view->gitlabAuthority = $this->gitlabAuthority;
        $this->view->jenkinsAuthority = $this->jenkinsAuthority;
        $this->view->appList = $this->appList;
        $this->view->deptList = $this->deptList;
        $this->view->projectList = $this->projectList;
        $this->view->productList = [];
        $this->view->title = $this->lang->authorityapply->create;
        $this->view->userList = $this->userList;
        $deptLeader = $this->loadModel('dept')->getdeptLeader([$this->app->user->dept]);
        $this->view->manager1 = $deptLeader['manager1'];
        $this->view->cm = $deptLeader['cm'];
        $this->view->svnPermission = $this->svnPermission;
        $this->view->gitLabPermission = $this->gitLabPermission;
        $this->view->jenkinsPermission = $this->jenkinsPermission;
        $this->view->returnUrl = $this->returnUrl;
        $this->display();
    }

    /**
     * 编辑信息
     *
     * @param $authorityapplyId
     */
    public function edit($authorityapplyId, $source = "list")
    {
        $this->view->title = $this->lang->authorityapply->edit;
        $info = $this->authorityapply->getById($authorityapplyId);
        if ($_POST) {
            $this->authorityapply->update($authorityapplyId, $info);
            if (dao::isError()) {
                $response['result'] = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $response['result'] = 'success';
            $response['message'] = $this->lang->saveSuccess;;
            $issubmit = $_POST['issubmit']; //提交还是保存
            if ($issubmit == 'submit') { //提交
                $response['locate'] = $this->returnUrl;
            }
            $response['id'] = $authorityapplyId;
            $this->send($response);
        }

        $this->view->svnAuthority = $this->svnAuthority;
        $this->view->gitlabAuthority = $this->gitlabAuthority;
        $this->view->jenkinsAuthority = $this->jenkinsAuthority;
        $this->view->appList = $this->appList;
        $this->view->deptList = $this->deptList;
        $this->view->projectList = $this->projectList;
        $this->view->projectList = $this->projectList;
        $productList = $this->loadModel('product')->getCodeNamePairsByApp($info->application);
        $this->view->productList = $productList;
        $this->view->title = $this->lang->authorityapply->edit;
        $this->view->userList = $this->userList;
        $this->view->info = $info;
        $this->view->source = $source;
        $this->view->svnPermission = $this->svnPermission;
        $this->view->gitLabPermission = $this->gitLabPermission;
        $this->view->jenkinsPermission = $this->jenkinsPermission;
        $this->view->returnUrl = $this->returnUrl;
        $this->display();
    }

    /**
     * 删除操作
     *
     * @param $authorityapplyId
     * @param $source
     */
    public function delete($authorityapplyId, $confirm = 'no', $source = 'list')
    {

        $cancelURL = $this->server->HTTP_REFERER;

        if ($confirm == 'no') {
            echo js::confirm("确定删除吗？", $this->createLink('authorityapply', 'delete', "authorityapplyId=$authorityapplyId&confirm=yes"), $cancelURL, 'parent', 'parent');
            exit;
        } else {
            $info = $this->authorityapply->getByID($authorityapplyId);
            $checkRes = $this->authorityapply->checkIsAllowDelete($info, $this->app->user->account);
            if (!$checkRes['result']) {
                echo js::alert($checkRes['message']);
                exit;
            } else {
                $this->authorityapply->deleteData($info);
                if (dao::isError()) {
                    $response['result'] = 'fail';
                    $response['message'] = dao::getError();
                    $this->send($response);
                }
                //历史记录
                $objectType = $this->config->authorityapply->objectType;
                $this->loadModel('action')->create($objectType, $authorityapplyId, 'deleted');
                die(js::locate($this->session->common_back_url));
            }
        }
    }

    /**
     * 提交
     * @param $authorityapplyId
     */
    function submit($authorityapplyId, $confirm = 'no', $source = 'list')
    {
        $cancelURL = $this->server->HTTP_REFERER;
        if ($confirm == 'no') {
            echo js::confirm($this->lang->authorityapply->submitConfirm, $this->createLink('authorityapply', 'submit', "authorityapplyId=$authorityapplyId&confirm=yes&source=view"), $cancelURL, 'self', 'parent');
            exit;
        } else {
            $ret = $this->authorityapply->submit($authorityapplyId);
            if (dao::isError()) {
                $response['result'] = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            if ($source == 'view') {
                die(js::reload('parent'));
            }
            die(js::locate($this->returnUrl));
        }
    }


//   获取分管领导
    function ajaxGetChargeLeaderByUser()
    {
        if ($_POST) {
            $postData = fixer::input('post')
                ->get();
            $openPermissionPerson = $postData->openPermissionPerson;
            $deptLeader = $this->loadModel('user')->getLeaderByUser($openPermissionPerson);
//            判断是否有实习生/厂商/外单位
            $flag = false;
            foreach ($deptLeader as $k => $v) {
                if (
                    stristr($v->realname, '厂商') ||
                    stristr($v->realname, '实习') ||
                    stristr($v->realname, '金电') ||
                    stristr($v->realname, 'c_') ||
                    stristr($v->realname, 'cj_') ||
                    stristr($v->realname, 'zz_')) {
                    $flag = true;
                    break;
                }
            }
            if ($flag) {
                $deptLeader = $this->loadModel('dept')->getdeptLeader([$postData->applyDepartment]);
                $userList = array('' => '') + $this->loadModel('user')->getAllUserList();
                $userList = array_column($userList, 'realname', 'account');
//            当前申请部门的分管领导
                $deptLeader = json_encode(['account' => $deptLeader['leader1'], 'realname' => zget($userList, $deptLeader['leader1'])]);
            } else {
                $deptLeader = '';
            }
            echo $deptLeader;

        }
    }

    function ajaxGetManagerByUser()
    {
        if ($_POST) {
            $postData = fixer::input('post')
                ->get();

            $approvalDepartment = is_array($postData->approvalDepartment) ? implode(',', $postData->approvalDepartment) : $postData->approvalDepartment;
            if ($approvalDepartment) {
//                部门负责人(申请部门负责人+非申请部门的负责人)
                $deptLeader = $this->loadModel('dept')->getdeptLeader([$approvalDepartment]);
                $userList = array('' => '') + $this->loadModel('user')->getAllUserList();
                $userList = array_column($userList, 'realname', 'account');
                $applyDepartmentLeader = $this->loadModel('dept')->getdeptLeader([$postData->applyDepartment]);
                $thisDeptLeader = $applyDepartmentLeader['manager1'];
                $thatDeptLeader = array_diff(explode(',', $deptLeader['manager1']), [$thisDeptLeader]);
//            当前申请部门+非申请部门的负责人
                $managerData = [
                    'realname' => zmget($userList, $deptLeader['manager1']),
                    'thisDeptLeader' => $thisDeptLeader,
                    'thatDeptLeader' => $thatDeptLeader ? implode(',', $thatDeptLeader) : '',

                ];

                $cmData = [
                    'account' => $deptLeader['cm'],
                    'realname' => zmget($userList, $deptLeader['cm']),
                ];

                $leader1 = [
                    'account' => $applyDepartmentLeader['leader1'],
                    'realname' => zmget($userList, $applyDepartmentLeader['leader1']),
                ];

                $deptLeader = json_encode(['manager1' => $managerData, 'cm' => $cmData, 'leader1' => $leader1]);
                echo $deptLeader;
            } else {
                echo '';
            }

        }
    }

//    通过应用获取产品
    function ajaxGetProductByAppId()
    {
        if ($_POST) {
            $postData = fixer::input('post')
                ->get();
            $product = $this->loadModel('product')->getCodeNamePairsByApp(implode(',', $postData->application));
            $product = json_encode($product);
            echo $product;
        }
    }

//处理
    public function deal($authorityapplyId, $type = "form", $source = 'list')
    {
        $ztPermissionObj = $this->loadModel('group')->getAllGroup();
        $info = $this->authorityapply->getByID($authorityapplyId);
        $this->view->svnAuthority = $this->svnAuthority;
        $this->view->gitlabAuthority = $this->gitlabAuthority;
        $this->view->jenkinsAuthority = $this->jenkinsAuthority;
        $this->view->ztPermission = $ztPermissionObj;
        $this->view->svnPermission = $this->svnPermission;
        $this->view->gitLabPermission = $this->gitLabPermission;
        $this->view->jenkinsPermission = $this->jenkinsPermission;
        $this->view->appList = $this->appList;
        $this->view->deptList = $this->deptList;
        $this->view->projectList = $this->projectList;
        $productList = $this->loadModel('product')->getCodeNamePairsByApp($info->application);
        $this->view->productList = $productList;
        $this->view->title = $this->lang->authorityapply->deal;
        $this->view->userList = $this->userList;
        $this->view->users = $this->userList;
        $this->view->info = $info;
        $this->view->source = $source;
        $actions = $this->loadModel('action')->getList($this->config->authorityapply->objectType, $authorityapplyId);
        $this->view->actions = $actions;
        $this->view->currentUser = $this->app->user->account;
        $iwfpModel = $this->loadModel('iwfp');
        $nodes = $iwfpModel->getCurrentVersionReviewNodes($info->processInstanceId, $info->version);
        $this->view->nodes = $nodes;
        //获得流程图
        $reviewFlowInfo = new  stdClass();
        $flowImgInfo = '';
        if ($info->processInstanceId) {
            $reviewFlowInfo = $this->loadModel('iwfp')->queryProcessTrackImage($info->processInstanceId);
            $flowImgInfo = $reviewFlowInfo->procBPMN;
        }
        $this->view->flowImg = $flowImgInfo;
        $this->view->reviewFlowInfo = $reviewFlowInfo; //审核流程
        $this->view->returnUrl = $this->returnUrl;
        $this->view->noticeList = json_decode($info->noticeList);
        $this->view->type = $type;
//                审核
        if ($_POST) {
//                        进行审批
            $ret = $this->authorityapply->approval($info);
            if (dao::isError()) {
                $response['result'] = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $response['result'] = 'success';
            $issubmit = $_POST['issubmit']; //提交还是保存
            if ($issubmit == 'submit') { //提交
                $response['locate'] = $this->returnUrl;
                $response['message'] = $this->lang->submitSuccess;
            } else {
                $response['message'] = "暂存成功";
            }
            $this->send($response);
        }
        $this->display('authorityapply', 'deal');
    }

//详情
    public function view($authorityapplyId = 0, $type = "form")
    {
        $ztPermissionObj = $this->loadModel('group')->getAllGroup();
        $this->view->svnAuthority = $this->svnAuthority;
        $this->view->gitlabAuthority = $this->gitlabAuthority;
        $this->view->jenkinsAuthority = $this->jenkinsAuthority;
        $this->view->ztPermission = $ztPermissionObj;
        $this->view->svnPermission = $this->svnPermission;
        $this->view->gitLabPermission = $this->gitLabPermission;
        $this->view->jenkinsPermission = $this->jenkinsPermission;
        $iwfpModel = $this->loadModel('iwfp');
        $info = $this->authorityapply->getByID($authorityapplyId);
        $this->view->appList = $this->appList;
        $this->view->deptList = $this->deptList;
        $this->view->projectList = $this->projectList;
        $productList = $this->loadModel('product')->getCodeNamePairsByApp($info->application);
        $this->view->productList = $productList;
        $this->view->userList = $this->userList;
        $this->view->users = $this->userList;
        $this->view->info = $info;
        $this->view->title = $this->lang->authorityapply->view;
        $actions = $this->loadModel('action')->getList($this->config->authorityapply->objectType, $authorityapplyId);
        $this->view->info = $info;
        $this->view->returnUrl = $this->returnUrl;
        $this->view->actions = $actions;
        $nodes = $iwfpModel->getCurrentVersionReviewNodes($info->processInstanceId, $info->version);
        $this->view->nodes = $nodes;
        $this->view->noticeList = json_decode($info->noticeList);

        //获得流程图
        $reviewFlowInfo = new  stdClass();
        $flowImgInfo = '';
        if ($info->processInstanceId) {
            $reviewFlowInfo = $this->loadModel('iwfp')->queryProcessTrackImage($info->processInstanceId);
            $flowImgInfo = $reviewFlowInfo->procBPMN;
        }
        $this->view->flowImg = $flowImgInfo;
        $this->view->reviewFlowInfo = $reviewFlowInfo; //审核流程
        $this->view->type = $type;
        $this->display();
    }

//历史节点记录
    public function showHistoryNodes($authorityapplyID)
    {
        $this->view->title = $this->lang->authorityapply->showHistoryNodes;
        $authorityapplyInfo = $this->authorityapply->getByID($authorityapplyID);
        $nodes = $this->loadModel('iwfp')->getAllVersionReviewNodes($authorityapplyInfo->processInstanceId);
        if ($nodes) {
            $users = $this->loadModel('user')->getPairs('noletter');
            $this->view->users = $users;
        }
        $this->view->nodes = $nodes;
        $this->view->info = $authorityapplyInfo;
        $this->display();
    }

//    public function sendmail()
//    {
//        $this->loadModel('authorityapply')->sendmail(73, 3295918);
//        $this->display();
//
//    }
}
