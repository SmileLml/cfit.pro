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
class localesupport extends control
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
    public function browse($browseType = 'all', $param = 0,  $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $this->view->title = $this->lang->localesupport->browse;
        $browseType = strtolower($browseType);
        $users    = array('' => '') + $this->loadModel('user')->getPairs('noletter');
        $appList  =  array('' => '') + $this->loadModel('application')->getPairs();
        $deptList =  array('' => '')+ $this->loadModel('dept')->getTopPairs();
        $this->config->localesupport->search['params']['appIds']['values']  = $appList;
        $this->config->localesupport->search['params']['deptIds']['values'] = $deptList;

        $queryID = ($browseType == 'bysearch')  ? (int)$param : 0;
        $actionURL = $this->createLink('localesupport', 'browse', "browseType=bySearch&param=myQueryID");
        $this->localesupport->buildSearchForm($queryID, $actionURL);
        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);
        $data = $this->localesupport->getList($browseType, $queryID, $orderBy, $pager);
        $this->view->users      = $users;
        $this->view->appList    = $appList;
        $this->view->deptList   = $deptList;
        $this->view->data       = $data;
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
     * @param $localesupportId
     * @param string $type
     */
    public function view($localesupportId, $type = 'localesupportForm')
    {
        //基本信息
        $this->view->title = $this->lang->localesupport->view;
        $baseInfo = $this->localesupport->getById($localesupportId);
        $taskIds = [];
        $baseInfo->workReportList = $this->localesupport->array_val_chunk((array)$baseInfo->workReportList);
        if($baseInfo->status == $this->lang->localesupport->statusArray['pass']){ //审批通过
            $taskIds = $this->localesupport->getTaskIds($localesupportId);
        }

        $users    = $this->loadModel('user')->getPairs('noletter');
        $appList  =  $this->loadModel('application')->getPairs();
        $deptList =  $this->loadModel('dept')->getTopPairs();
        $this->view->users      = $users;
        $this->view->appList    = $appList;
        $this->view->deptList   = $deptList;
        //处理意见
        $objectType = $this->config->localesupport->objectType;
        $reviewList = $this->loadModel('review')->getReviewListByVersion($objectType, $localesupportId, $baseInfo->version);
        $reviewList = $this->localesupport->getFormatReviewList($reviewList);
        $this->view->reviewList  = $reviewList;
        $taskList = [];
        if($taskIds){
            $taskList = $this->loadModel('task')->getListByIds($taskIds, 'id,name');
        }
        $this->view->taskList = $taskList;
        $this->view->actions  = $this->loadModel('action')->getList($this->config->localesupport->objectType, $localesupportId);
        $consumedList = $this->localesupport->getConsumedList([$localesupportId]);
        $this->view->consumedList = $consumedList;
        $this->view->localesupportId = $localesupportId;
        $this->view->baseInfo = $baseInfo;
        //是否允许编辑
        $checkEditRes = $this->localesupport->checkIsAllowEdit($baseInfo, $this->app->user->account);
        $this->view->isAllowEdit = $checkEditRes['result'];
        $this->display();
    }

    /**
     * 获得历史审核节点
     *
     * @param $id
     */
    function showHistoryNodes($id){
        $this->view->title = $this->lang->localesupport->showHistoryNodes;
        $localesupportInfo = $this->localesupport->getBasicInfoById($id);
        $objectType = $this->config->localesupport->objectType;
        $reviewList = $this->loadModel('review')->getAllVersionReviewList($objectType, $id);
        $reviewList = $this->localesupport->getFormatAllVersionReviewList($reviewList);
        if($reviewList){
            $users    = $this->loadModel('user')->getPairs('noletter');
            $deptList =  $this->loadModel('dept')->getTopPairs();
            $this->view->users      = $users;
            $this->view->deptList   = $deptList;
        }
        $this->view->nodes = $reviewList;
        $this->view->localesupportInfo = $localesupportInfo;
        $this->display();
    }

    /**
     * 创建投产
     *
     */
    public function create(){
        $this->view->title = $this->lang->localesupport->create;
        if($_POST)
        {
            $recordId = $this->localesupport->create();
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
//            if(dao::isWarn()){
//                $message = dao::getWarn() . $this->lang->localesupport->warnDefaultOp;
//                $this->send(array('result' => 'success', 'callback' =>'confirmSave("'.$message.'")'));
//            }
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;;
            $response['locate']  = inlink('browse');
            $response['id']       = $recordId;
            $this->send($response);
        }
        $this->app->loadLang('application');
        //系统名称
        $deptId              = $this->app->user->dept;
        $appList             = array('' => '') + $this->loadModel('application')->getapplicationNameCodePairs();
        $appDataList = $this->loadModel('application')->getAppListByIds('', 'id,team,fromUnit');
        if($appDataList){
            $appDataList = array_column($appDataList, null, 'id');
        }

        $deptList            = array('' => '') + $this->loadModel('dept')->getDeptPairs();
        $supportUsersList    = array('' => '');
        $managerUserList     = array('' => '') + $this->loadModel('user')->getManagerUsersNameByDept($deptId);
        $users               = array('' => '') + $this->localesupport->getAllowSupportUsers();
        $this->view->appList  = $appList;
        $this->view->appDataList  = $appDataList;
        $this->view->deptList = $deptList;
        $this->view->supportUsersList = $supportUsersList;
        $this->view->managerUserList  = $managerUserList;
        $this->view->users            = $users;
        $this->view->isAllReportWork  = true;
        $minStartDate = $this->localesupport->getMinStartDate();
        $this->view->minStartDate  = $minStartDate;
        $this->display();
    }

    /**
     * 编辑信息
     *
     * @param $localesupportId
     */
    public function edit($localesupportId)
    {
        $this->view->title = $this->lang->localesupport->edit;
        if($_POST) {
            $ret = $this->localesupport->update($localesupportId);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
//            if(dao::isWarn()){
//                $message = dao::getWarn() . $this->lang->localesupport->warnDefaultOp;
//                $this->send(array('result' => 'success', 'callback' =>'confirmSave("'.$message.'")'));
//            }

            $url = $this->session->common_back_url ? $this->session->common_back_url : inLink('browse');
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;;
            $response['locate']  = $url;
            $response['id']       = $localesupportId;
            $this->send($response);
        }
        $this->app->loadLang('application');
        $info = $this->localesupport->getById($localesupportId);
        $res = $this->localesupport->checkIsAllowEdit($info, $this->app->user->account);
        if(!$res['result']){ //不允许编辑
            $response['result']  = 'fail';
            $response['message'] = $res['message'];
            $this->send($response);
        }
        //编辑
        $deptIds             = explode(',', $info->deptIds);
        $appList             = array('' => '') + $this->loadModel('application')->getapplicationNameCodePairs();
        $appDataList = $this->loadModel('application')->getAppListByIds('', 'id,team,fromUnit');
        if($appDataList){
            $appDataList = array_column($appDataList, null, 'id');
        }

        $deptList            = array('' => '') + $this->loadModel('dept')->getDeptPairs();
        $supportUsers = explode(',', $info->supportUsers);
        $supportUsersList = $this->loadModel('user')->getUserInfoListByAccounts($supportUsers, 'account,realname');
        if($supportUsersList){
            $supportUsersList = array('' => '') +  array_column($supportUsersList, 'realname', 'account');
        }
        $managerUserList     = array('' => '') + $this->loadModel('user')->getManagerUsersNameByDept($deptIds);
        $users               = array('' => '') + $this->localesupport->getAllowSupportUsers();
        $this->view->appList  = $appList;
        $this->view->appDataList  = $appDataList;
        $this->view->deptList = $deptList;
        $this->view->supportUsersList = $supportUsersList;
        $this->view->managerUserList  = $managerUserList;
        $this->view->users  = $users;
        $this->view->info = $info;
        $this->view->isAllReportWork  = true;
        $minStartDate = $this->localesupport->getMinStartDate($info);
        $this->view->minStartDate = $minStartDate;
        $this->display();
    }



    /**
     * 提交
     * @param $localesupportId
     */
    function submit($localesupportId, $confirm = 'no'){
        if($confirm == 'no') {
            echo js::confirm($this->lang->localesupport->submitConfirm, $this->createLink('localesupport', 'submit', "localesupportId=$localesupportId&confirm=yes"), '');
            exit;
        } else {
            $ret = $this->localesupport->submit($localesupportId);
            if (dao::isError()) {
                $response['result'] = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
           die(js::reload('parent'));
        }
    }

    /**
     * 审批操作
     *
     * @param $localesupportId
     */
    public function review($localesupportId){
        $this->view->title = $this->lang->localesupport->review;
        if($_POST) {
            $changes = $this->localesupport->review($localesupportId);
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
        $info = $this->localesupport->getById($localesupportId);
        $checkRes = $this->localesupport->checkIsAllowReview($info, $this->app->user->account);
        $this->view->info = $info;
        $this->view->checkRes = $checkRes;
        $this->display();
    }
    //批量审批
    public function batchReview($localesupportIds){
        $this->view->title = $this->lang->localesupport->batchReview;
        if($_POST) {
            $res = $this->localesupport->batchReview($localesupportIds);
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

        $checkRes = $this->localesupport->checkIsAllowBatchReview($localesupportIds, $this->app->user->account);
        $this->view->checkRes = $checkRes;
        $this->view->localesupportIds = $localesupportIds;
        $this->display();
    }

    /**
     * 删除操作
     *
     * @param $localesupportId
     * @param $source
     */
    public function delete($localesupportId, $confirm = 'no',$source = 'list')
    {
        if($confirm == 'no') {
            echo js::confirm("确定删除吗？", $this->createLink('localesupport', 'delete', "localesupportId=$localesupportId&confirm=yes"), '');
            exit;
        }
        else {
            $info = $this->localesupport->getByID($localesupportId);
            $checkRes = $this->localesupport->checkIsAllowDelete($info, $this->app->user->account);
            if(!$checkRes['result']){
                echo js::alert($checkRes['message']);
                exit;
            }else{
                $this->localesupport->deleteData($localesupportId);
                if(dao::isError())
                {
                    $response['result']  = 'fail';
                    $response['message'] = dao::getError();
                    $this->send($response);
                }
                //历史记录
                $objectType = $this->config->localesupport->objectType;
                $actionID = $this->loadModel('action')->create($objectType, $localesupportId, 'deleted');

                if(isonlybody())
                {
                    if($source == 'view'){ //详情页删除以后不存在，返回列表页
                        die(js::closeModal('parent.parent', $this->session->common_back_url));
                    }else{
                        die(js::closeModal('parent.parent', 'this'));
                    }
                } else{
                    $url = $this->session->common_back_url ? $this->session->common_back_url : inLink('browse');
                    die(js::locate($url,'parent.parent'));
                }
                die(js::reload('parent'));
            }
        }
    }
    /**
     * 现场支持报工
     *
     * @param $localesupportId
     */
    public function reportWork($localesupportId){
        $this->view->title = $this->lang->localesupport->reportWork;
        if($_POST)
        {
            $changes = $this->localesupport->reportWork();
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $objectType = $this->config->localesupport->objectType;
            $this->loadModel('action')->create($objectType, $localesupportId, 'reportwork');

            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }
        $info = $this->localesupport->getById($localesupportId);
        $res = $this->localesupport->checkIsAllowReportWork($info, $this->app->user->account);
        if($res['result']){
            $users = array('' => '') + $this->loadModel('user')->getPairs('noletter');
            $supportUsersList = $this->loadModel('user')->getUserInfoListByAccounts($info->supportUsers, 'account,realname');
            if($supportUsersList){
                $supportUsersList = array('' => '') + array_column($supportUsersList, 'realname', 'account');
            }
            //是否允许填报所有人的
            $isAllReportWork = $this->localesupport->isAllReportWork($info, $this->app->user->account);
            $this->view->users = $users;
            $this->view->supportUsersList = $supportUsersList;
            $this->view->isAllReportWork = $isAllReportWork;
            $users    =  $this->loadModel('user')->getPairs('noletter');
            $appList  =  $this->loadModel('application')->getPairs();
            $deptList =  $this->loadModel('dept')->getTopPairs();
            $this->view->users      = $users;
            $this->view->appList    = $appList;
            $this->view->deptList   = $deptList;
        }
        //报工信息格式化
        $workReportData = $this->localesupport->getFormatWorkReportData($info->workReportList);
        $this->view->info = $info;
        $this->view->workReportData = $workReportData;
        $this->view->checkRes = $res;
        $minStartDate = $this->localesupport->getMinStartDate($info);
        $this->view->minStartDate = $minStartDate;
        $this->display();
    }

    /**
     * 导出
     *
     * @param string $orderBy
     */
    public function export($orderBy = 'id_desc'){
        if($_POST) {
            $localesupportLang = $this->lang->localesupport;
            $localesupportConfig = $this->config->localesupport;

            /* Create field lists. */
            $fields = $this->post->exportFields ? $this->post->exportFields : explode(',', $localesupportConfig->list->exportFields);
            foreach ($fields as $key => $fieldName) {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = isset($localesupportLang->$fieldName) ? $localesupportLang->$fieldName : $fieldName;
                unset($fields[$key]);
            }
            /* Get changes. */
            $localesupports = array();
            if ($this->session->localesupportOnlyCondition) {
                $localesupports = $this->dao->select('*')->from(TABLE_LOCALESUPPORT)->where($this->session->localesupportQueryCondition)
                    ->beginIF($this->post->exportType == 'selected')->andWhere('id')->in($this->cookie->checkedItem)->fi()
                    ->orderBy($orderBy)->fetchAll('id');

            } else {
                $stmt = $this->dbh->query($this->session->localesupportQueryCondition . ($this->post->exportType == 'selected' ? " AND id IN({$this->cookie->checkedItem})" : '') . " ORDER BY " . strtr($orderBy, '_', ' '));
                while ($row = $stmt->fetch()) $localesupports[$row->id] = $row;
            }
            if ($localesupports) {
                $this->app->loadLang('application');
                $users = $this->loadModel('user')->getPairs('noletter');
                //系统
                $appList = array('' => '') + $this->loadModel('application')->getPairs(0);
                //部门列表
                $deptList = $this->loadModel('dept')->getTopPairs();

                //工时
                $ids = array_column($localesupports, 'id');
                $consumedList = $this->localesupport->getConsumedList($ids);
                foreach ($localesupports as $val) {
                    $supportId = $val->id;
                    $owndeptList = json_decode($val->owndept, true);
                    $sjList    = json_decode($val->sj, true);
                    $deptIds =  explode(',', $val->deptIds);
                    $val->area = zget($localesupportLang->areaList, $val->area);
                    $val->stype = zget($localesupportLang->stypeList, $val->stype);
                    $val->appIds = zmget($appList, $val->appIds);
                    //承建单位
                    if(!empty($owndeptList) && is_array($owndeptList)){
                        $tempData = [];
                        foreach ($owndeptList as $appId => $owndep){
                            $team = zget($this->lang->application->teamList, $owndep);
                            $tempData[] = $team;
                        }
                        $val->owndept = implode(",", $tempData);
                    }
                    //业务司局
                    if(!empty($sjList) && is_array($sjList)){
                        $tempData = [];
                        foreach ($sjList as $appId => $sj){
                            $fromUnit = zget($this->lang->application->fromUnitList, $sj);
                            $tempData[] = $fromUnit;
                        }
                        $val->sj = implode(",", $tempData);
                    }
                    //部门
                    if(!empty($deptIds)){
                        $tempData = [];
                        foreach ($deptIds as $deptId){
                            $deptName = trim(zget($deptList, $deptId), '/');
                            $tempData[] = $deptName;
                        }
                        $val->deptIds =  implode(',', $tempData);;
                    }

                    $val->deptManagers = zmget($users, $val->deptManagers);
                    $val->supportUsers = zmget($users, $val->supportUsers);
                    if(isset($consumedList[$supportId])){
                        $val->consumedTotal = $consumedList[$supportId];
                    }else{
                        $val->consumedTotal = 0;
                    }
                    $val->createdBy = zget($users, $val->createdBy);
                    $val->createdDept = trim(zget($deptList, $val->createdDept), '/');
                    $val->status = zget($localesupportLang->statusList, $val->status);
                    $val->dealUsers = zmget($users, $val->dealUsers);
                    $val =  $this->loadModel('file')->replaceImgURL($val, $this->config->localesupport->editor->create['id']);
                    $val->reason = strip_tags($val->reason);
                    $val->remark = strip_tags($val->remark);

                }
                $this->post->set('fields', $fields);
                $this->post->set('rows', $localesupports);
                $this->post->set('kind', 'localesupport');
                $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
            }
        }
        $this->view->fileName = $this->lang->localesupport->exportName;
        $this->view->allExportFields = $this->config->localesupport->list->exportFields;
        $this->view->customExport = true;
        $this->display();
    }

    /**
     * 导出工作量明细
     * @param string $orderBy
     */
    public function exportDetail($orderBy = 'id_desc'){
        if($_POST) {
            $localesupportLang = $this->lang->localesupport;
            $localesupportConfig = $this->config->localesupport;

            /* Create field lists. */
            $fields = $this->post->exportDetailFields ? $this->post->exportDetailFields : explode(',', $localesupportConfig->list->exportDetailFields);
            foreach ($fields as $key => $fieldName) {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = isset($localesupportLang->$fieldName) ? $localesupportLang->$fieldName : $fieldName;
                unset($fields[$key]);
            }
            /* Get changes. */
            $localesupports = array();
            if ($this->session->localesupportOnlyCondition) {
                $localesupports = $this->dao->select('*')->from(TABLE_LOCALESUPPORT)->where($this->session->localesupportQueryCondition)
                    ->beginIF($this->post->exportType == 'selected')->andWhere('id')->in($this->cookie->checkedItem)->fi()
                    ->orderBy($orderBy)->fetchAll('id');

            } else {
                $stmt = $this->dbh->query($this->session->localesupportQueryCondition . ($this->post->exportType == 'selected' ? " AND id IN({$this->cookie->checkedItem})" : '') . " ORDER BY " . strtr($orderBy, '_', ' '));
                while ($row = $stmt->fetch()) $localesupports[$row->id] = $row;
            }
            if ($localesupports) {
                $supportIds = array_column($localesupports, 'id');
                $workreportList = $this->localesupport->getWorkreportList($supportIds);
                if($workreportList){
                    $supportCodeList = array_column($localesupports, 'code', 'id');
                    $users = $this->loadModel('user')->getPairs('noletter');

                    //部门列表
                    $deptList = $this->loadModel('dept')->getTopPairs();
                    foreach ($workreportList as $val){
                        $supportId = $val->supportId;
                        $code = zget($supportCodeList, $supportId);
                        $val->code = $code;
                        $val->deptId = trim(zget($deptList, $val->deptId), '/');
                        $val->supportUser = zget($users, $val->supportUser);
                    }
                }

                $this->post->set('fields', $fields);
                $this->post->set('rows', $workreportList);
                $this->post->set('kind', 'localesupport');
                $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
            }
        }
        $this->view->fileName = $this->lang->localesupport->exportDetailName;
        $this->view->allExportFields = $this->config->localesupport->list->exportDetailFields;
        $this->view->customExport = true;
        $this->display();
    }

    /**
     * 获得部门用户
     *
     * @param $deptIds
     */
    public function ajaxGetUsersByDeptIds($deptIds){
        $deptIds = trim($deptIds,',');
        $managerUsers = [];
        if ((int)$deptIds == 0){
            $userList = [];
            $managerUserList = [];
        }else{
            $deptIds = explode(',', $deptIds);
            $userList   = array('' => '') + $this->loadModel('user')->getUsersNameByDept($deptIds);
            $managerUserList = $this->loadModel('user')->getManagerUsersNameByDept($deptIds);
            $managerUsers = array_keys($managerUserList);
            $managerUserList = array('' => '') + $managerUserList;
        }
        $data[0] = html::select('supportUsers[]', $userList, '', 'class="form-control chosen" onchange="changeSupportUsers();" multiple');
        $data[1] = html::select('deptManagers[]', $managerUserList, $managerUsers, "class='form-control chosen' multiple");
        echo json_encode($data);
    }

    /**
     * 获得用户的部门和用户所在的部门的负责人
     */
    function ajaxGetDeptAndManagersUsers($supportUsers){
        $supportUsers = trim($supportUsers,',');
        $managerUserList =  array('' => '');
        $managerUsers  = '';
        $supportUserList = array('' => '');
        $deptIds = '';
        if($supportUsers){
            $deptIds = [];
            $supportUsers = explode(',', $supportUsers);
            $userList = $this->loadModel('user')->getUserInfoListByAccounts($supportUsers, 'account,realname,dept');
            if($userList){
                $deptIds = array_flip(array_flip(array_column($userList, 'dept')));
                $supportUserList = array_column($userList, 'realname', 'account');
            }
            $managerUserList = $this->loadModel('user')->getManagerUsersNameByDept($deptIds);
            $managerUsers = array_keys($managerUserList);
            $managerUserList =  array('' => '') + $managerUserList;
            $supportUserList =  array('' => '') + $supportUserList;
            $deptIds = implode(',', $deptIds);
        }
        $data = [];
        $data[0] = html::select('deptManagers[]', $managerUserList, $managerUsers, "class='form-control chosen' node-dept='{$deptIds}' multiple");
        $data[1] = html::select('supportUser[]',  $supportUserList,  '', " id='supportUser0' data-index='0' class='form-control chosen'");
        echo json_encode($data);
    }

    public function ajaxGetAppInfo($appId){
        $data = new stdClass();
        if($appId){
            $appList =  $this->loadModel('application')->getAppListByIds([$appId], 'id,team,fromUnit');
            if($appList){
                $data = $appList[0];
            }
        }
        $res = [
            'data' => $data,
        ];
        echo json_encode($res);
    }
}
