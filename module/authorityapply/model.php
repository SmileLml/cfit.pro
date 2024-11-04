<?php

class authorityapplyModel extends model
{
//列表
    public function getList($browseType, $queryID, $orderBy, $pager = null)
    {
        $authorityapplyQuery = '';
        if ($browseType == 'bysearch') {
            $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : '';
            if ($query) {
                $this->session->set('authorityapplyQuery', $query->sql);
                $this->session->set('authorityapplyForm', $query->form);
            }

            if ($this->session->authorityapplyQuery == false) $this->session->set('authorityapplyQuery', ' 1 = 1');
            $authorityapplyQuery = $this->session->authorityapplyQuery;

//审批中的状态
            $approvalStatus="'".implode("','",$this->lang->authorityapply->approvalStatus)."'";
            $res=preg_replace_callback('/\=\s+\'waitapproval\'/',function ($matches) use($approvalStatus){
                return "in ($approvalStatus)";
            },$authorityapplyQuery);
            $authorityapplyQuery=$res;
            $b="content";
//权限分布子系统查询
            $res=preg_replace_callback('/`subSystem.*?%(\w+)%/',function ($matches) use($b){
                $subSystem=$matches[1];
                return <<<EOF
            $b REGEXP '^\\\{.*?"[\^"]+":"$subSystem"[,\\\}].*$
EOF;
            },$authorityapplyQuery);

            $authorityapplyQuery=$res;
            $res=preg_replace_callback('/`subSystem.*?\'(\w+)/',function ($matches) use($b){
                $subSystem=$matches[1];
                $reg = <<<EOF
            $b  NOT REGEXP  '"subSystem":"(?!$subSystem")'  and content <> '
EOF;
                return $reg;
            },$authorityapplyQuery);
            $authorityapplyQuery=$res;

        }
        $list = $this->dao->select('*')->from(TABLE_AUTHORITYAPPLY)
            ->where('deleteTime is null')
            ->beginIF($browseType == 'tomedeal')->andWhere("FIND_IN_SET('{$this->app->user->account}', dealUser)")->fi()
            ->beginIF($browseType == 'myapply')->andWhere("`createdBy`")->eq($this->app->user->account)->fi()
            ->beginIF($browseType == 'bysearch')->andWhere($authorityapplyQuery)->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');
        return $list;
    }
    public function getTodealAuthorityapplyCount(){
        $counts = $this->dao->select('count(*) as counts')->from(TABLE_AUTHORITYAPPLY)
            ->where('deleteTime is null')
            ->andWhere("FIND_IN_SET('{$this->app->user->account}', dealUser)")
            ->fetch('counts');
        return $counts;
    }

    /**
     * Project: chengfangjinke
     * Method: buildSearchForm
     * @param $queryID
     * @param $actionURL
     */
    public function buildSearchForm($queryID, $actionURL)
    {
        $this->config->authorityapply->search['actionURL'] = $actionURL;
        $this->config->authorityapply->search['queryID'] = $queryID;
        $this->loadModel('search')->setSearchParams($this->config->authorityapply->search);
    }


//    获取详情
    public function getById($id)
    {
        $ret = $this->dao->select('*')
            ->from(TABLE_AUTHORITYAPPLY)
            ->where('deleteTime is null')
            ->andWhere('id')->eq($id)
            ->fetch();
        if (!$ret) {
            return false;
        }
        $ret->deptLeader = $ret->thatDeptLeader ? $ret->thisDeptLeader . ',' . $ret->thatDeptLeader : $ret->thisDeptLeader;
        $ret = $this->loadModel('file')->replaceImgURL($ret, $this->config->authorityapply->editor->create['id']);
        return $ret;
    }

//    创建
    public function create()
    {
        $issubmit = $_POST['issubmit']; //提交还是保存
        $postData = fixer::input('post')
            ->remove('issubmit,files,uid')
            ->get();
        $postData = $this->getFormatPostData($postData);
        if ($issubmit == 'submit') { //提交需要验证
            //检查基本信息

            $res = $this->checkPostParamsInfo($postData);
            if (!$res['checkRes']) {
                dao::$errors = $res['errorData'];
                return dao::$errors;
            }
        }
        $currentUser = $this->app->user->account;
        $status = $this->lang->authorityapply->statusArray['waitsubmit'];//待提交
        $postData->status = $status;
        $postData->createdBy = $currentUser;
        $postData->dealUser = $currentUser;
        $postData->createdTime = helper::now();
        $postData->code = $this->getCode();
        $postData = $this->loadModel('file')->processImgURL($postData, $this->config->authorityapply->editor->create['id'], $this->post->uid);
        $this->dao->begin();
        $this->dao->insert(TABLE_AUTHORITYAPPLY)->data($postData)->batchCheckIF($issubmit != 'save', $this->config->authorityapply->create->requiredFields, 'notempty')->exec();

        $recordId = $this->dao->lastInsertId();

        if (!dao::isError()) {
            $objectType = $this->config->authorityapply->objectType;
            //日志
            $this->loadModel('action')->create($objectType, $recordId, 'created');
            if ($issubmit == 'submit') { //提交
                if ($issubmit == 'submit') {
                    $res = $this->submit($recordId, true);
                }
            } else {
                $this->loadModel('consumed')->record($objectType, $recordId, 0, $currentUser, '', $status, array());
            }
//        回滚
            if (dao::isError()) {
                $this->dao->rollback();
                return false;
            }
        }
//        提交
        $this->dao->commit();
        return $recordId;
    }

    public function update($authorityapplyId, $info)
    {
        $op = 'edit';
        $account = $this->app->user->account;
        //检查是否允许更新
        $res = $this->checkIsAllowEdit($info, $account);
        if (!$res['result']) {
            dao::$errors[] = $res['message'];
            return false;
        }
        //是否需要发出警告信息
        $issubmit = $_POST['issubmit']; //提交还是保存
        $postData = fixer::input('post')
            ->remove('uid,issubmit')
            ->stripTags($this->config->authorityapply->editor->edit['id'], $this->config->allowedTags)
            ->get();
        $postData = $this->getFormatPostData($postData, $op);
        $postData->createdBy = $this->app->user->account;
        if ($issubmit == 'submit') { //提交需要验证
            //检查基本信息
            $res = $this->checkPostParamsInfo($postData, $op);
            if (!$res['checkRes']) {
                dao::$errors = $res['errorData'];
                return dao::$errors;
            }
        }
        //编辑信息
        $this->dao->begin();
        $this->dao->update(TABLE_AUTHORITYAPPLY)
            ->data($postData)->batchCheckIF($issubmit != 'save', $this->config->authorityapply->edit->requiredFields, 'notempty')->where('id')->eq($authorityapplyId)
            ->exec();
        if (!dao::isError()) {
            //状态流转
            $objectType = $this->config->authorityapply->objectType;
            $changes = common::createChanges($info, $postData);
            $actionID = $this->loadModel('action')->create($objectType, $authorityapplyId, 'edited');
            if ($changes) {
                $this->action->logHistory($actionID, $changes);
            }
            if ($issubmit == 'submit') {
                $res = $this->submit($authorityapplyId, true);
            }
            //回滚
            if (dao::isError()) {
                $this->dao->rollback();
                return false;
            }
        }
        //提交
        $this->dao->commit();
        return true;
    }

//数据检查
    function checkPostParamsInfo($params)
    {
        //检查结果
        $checkRes = false;
        $errorData = [];
        $data = [
            'checkRes' => $checkRes,
            'errorData' => $errorData,
        ];
        $requiredFields = explode(',', $this->config->authorityapply->create->requiredFields);
        foreach ($requiredFields as $requiredField) {
            if (!isset($params->$requiredField) || empty($params->$requiredField)) {
                $errorData[$requiredField] = sprintf($this->lang->error->notempty, $this->lang->authorityapply->$requiredField);
            }
        }

//        验证申请内容
        if ($params->content) {
            $contentData = json_decode($params->content, true);

            foreach ($contentData as $k => $v) {
                if ($v['subSystem'] == 'dpmp' && (!isset($v['permissionContent']) ||$v['permissionContent']=='')) {
                    $errorData['permissionContent' . $k] = sprintf($this->lang->error->notempty, $this->lang->authorityapply->permissionContent);
                }
                if ($v['subSystem'] == 'other' && (!isset($v['permissionContent']) || $v['permissionContent']=='')) {
                    $errorData['permissionContent' . $k] = sprintf($this->lang->error->notempty, $this->lang->authorityapply->permissionContent);
                }
                if ($v['subSystem'] == 'svn' && (!isset($v['svnPermissionContent']) || $v['svnPermissionContent']=='')) {
                    $errorData['svnPermissionContent' . $k] = sprintf($this->lang->error->notempty, $this->lang->authorityapply->permissionContent);
                }
                if ($v['subSystem'] == 'svn' && (!isset($v['svnPermission']) ||$v['svnPermission']=='')) {
                    $errorData['svnPermission' . $k] = sprintf($this->lang->error->notempty, $this->lang->authorityapply->permissionContentOperate);
                }
                if ($v['subSystem'] == 'gitlab' && (!isset($v['gitLabPermissionContent']) || $v['gitLabPermissionContent']=='')) {
                    $errorData['gitLabPermissionContent' . $k] = sprintf($this->lang->error->notempty, $this->lang->authorityapply->permissionContent);
                }
                if ($v['subSystem'] == 'gitlab' && (!isset($v['gitLabPermission']) || $v['gitLabPermission']=='')) {
                    $errorData['gitLabPermission' . $k] = sprintf($this->lang->error->notempty, $this->lang->authorityapply->permissionContentOperate);
                }
                if ($v['subSystem'] == 'jenkins' && (!isset($v['jenkinsPermissionContent']) || $v['jenkinsPermissionContent']=='')) {
                    $errorData['jenkinsPermissionContent' . $k] = sprintf($this->lang->error->notempty, $this->lang->authorityapply->permissionContent);
                    $errorData['jenkinsPermission' . $k] = sprintf($this->lang->error->notempty, $this->lang->authorityapply->permissionContentOperate);
                }
                if ($v['subSystem'] == 'jenkins' && (!isset($v['jenkinsPermission']) || $v['jenkinsPermission']=='')) {
                    $errorData['jenkinsPermission' . $k] = sprintf($this->lang->error->notempty, $this->lang->authorityapply->permissionContentOperate);
                }
                if (!isset($v['openPermissionPerson']) || $v['openPermissionPerson']=='') {
                    $errorData['openPermissionPerson' . $k] = sprintf($this->lang->error->notempty, $this->lang->authorityapply->openPermissionPerson);
                }
            }
        }
        //富文本
        $searchParam = ['&nbsp;', ' '];
        $replaceParam = ['', ''];
        $tempReason = str_replace($searchParam, $replaceParam, strip_tags($params->reason, '<img>'));
        if ($tempReason == '') {
            $errorData['reason'] = sprintf($this->lang->error->notempty, $this->lang->authorityapply->reason);
        }
        //基本信息验证
        if ($errorData) {
            $data['errorData'] = $errorData;
        } else {
            $checkRes = true;
            $data['checkRes'] = $checkRes;
        }
        return $data;
    }

    public function getFormatPostData($postData, $op = "create")
    {
        $requiredFields = explode(',', $this->config->authorityapply->$op->requiredFields);
        foreach ($requiredFields as $requiredField) {
            if (!isset($postData->$requiredField)) {
                $postData->$requiredField = '';
            }
        }
        foreach ($this->config->authorityapply->multipleSelectFields as $multipleSelectField) {
            if (isset($postData->$multipleSelectField) && !empty($postData->$multipleSelectField)) {
                if (is_array($postData->$multipleSelectField)) {
                    $postData->$multipleSelectField = implode($postData->$multipleSelectField, ',');
                }
                $postData->$multipleSelectField = trim($postData->$multipleSelectField, ',');
            } else {
                $postData->$multipleSelectField = '';
            }

        }

//        处理申请权限内容为json,还要检验内容核开通人员一一对应是否为空
        if (isset($postData->subSystem)) {
            foreach ($postData->subSystem as $k => $v) {
                $contentData[$k]['subSystem'] = $postData->subSystem[$k];
                $contentData[$k]['permissionContent'] = $postData->permissionContent[$k] ?? '';
                $contentData[$k]['svnPermissionContent'] = $postData->svnPermissionContent[$k] ?? '';
                $contentData[$k]['svnPermission'] = $postData->svnPermission[$k] ?? '';
                $contentData[$k]['gitLabPermissionContent'] = $postData->gitLabPermissionContent[$k] ?? '';
                $contentData[$k]['gitLabPermission'] = $postData->gitLabPermission[$k] ?? '';
                $contentData[$k]['jenkinsPermissionContent'] = $postData->jenkinsPermissionContent[$k] ?? '';
                $contentData[$k]['jenkinsPermission'] = $postData->jenkinsPermission[$k] ?? '';
                $contentData[$k]['openPermissionPerson'] = $postData->openPermissionPerson[$k] ?? '';
            }
        }


        unset($postData->subSystem);
        unset($postData->permissionContent);
        unset($postData->openPermissionPerson);
        unset($postData->svnPermissionContent);
        unset($postData->svnPermission);
        unset($postData->gitLabPermissionContent);
        unset($postData->gitLabPermission);
        unset($postData->jenkinsPermissionContent);
        unset($postData->jenkinsPermission);
        if (!empty($contentData)) {
            $postData->content = json_encode($contentData);
        }
        return $postData;
    }

    public function getFormatRealPermission($postData)
    {
        if (isset($postData->involveSubSystem)) {
            foreach ($postData->involveSubSystem as $k => $v) {
                $contentData[$k]['involveSubSystem'] = $postData->involveSubSystem[$k];
                $contentData[$k]['realOpenPermissionPerson'] = $postData->realOpenPermissionPerson[$k] ?? '';
                $contentData[$k]['realZtPermissionOperate'] = $postData->realZtPermissionOperate[$k] ?? '';

                $contentData[$k]['realSvnPermissionPath'] = $postData->realSvnPermissionPath[$k] ?? '';
                $contentData[$k]['realSvnPermissionOperate'] = $postData->realSvnPermissionOperate[$k] ?? '';

                $contentData[$k]['realGitLabPermissionPath'] = $postData->realGitLabPermissionPath[$k] ?? '';
                $contentData[$k]['realGitLabPermissionOperate'] = $postData->realGitLabPermissionOperate[$k] ?? '';

                $contentData[$k]['realJenkinsPermissionPath'] = $postData->realJenkinsPermissionPath[$k] ?? '';
                $contentData[$k]['realJenkinsPermissionOperate'] = $postData->realJenkinsPermissionOperate[$k] ?? '';

                $contentData[$k]['realOtherPermissionOperate'] = $postData->realOtherPermissionOperate[$k] ?? '';

            }
            unset($postData->involveSubSystem);

            unset($postData->realOpenPermissionPerson);

            unset($postData->realZtPermissionOperate);

            unset($postData->realSvnPermissionPath);
            unset($postData->realSvnPermissionOperate);

            unset($postData->realGitLabPermissionPath);
            unset($postData->realGitLabPermissionOperate);

            unset($postData->realJenkinsPermissionPath);
            unset($postData->realJenkinsPermissionOperate);

            unset($postData->realOtherPermissionOperate);
        }
        $realPermission = '';
        if (!empty($contentData)) {
            $realPermission = json_encode($contentData);
        }
        return $realPermission;

    }

    public function getCode($createTime = '')
    {
        if (!$createTime) {
            $createTime = strtotime(helper::today());
        }
        $codePrefix = 'CFIT-AP-';
        $createDay = date('Ymd-', $createTime);
        $codeTemp = $codePrefix . $createDay;
        $number = $this->dao->select('count(id) c')->from(TABLE_AUTHORITYAPPLY)->where('code')->like($codeTemp . "%")->fetch('c');
        $number = intval($number) + 1;
        $code = $codeTemp . sprintf('%02d', $number);
        return $code;
    }

    //    检查是否允许编辑
    public function checkIsAllowEdit($info, $account)
    {
        $res = array(
            'result' => false,
            'message' => '',
        );
        if (!$info) {
            $res['message'] = $this->lang->common->errorParamId;
            return $res;
        }
        //当前状态
        $status = $info->status;
        if (!in_array($status, $this->lang->authorityapply->allowEditStatusArray)) {
            $statusDesc = zget($this->lang->authorityapply->statusList, $status);
            $res['message'] = sprintf($this->lang->authorityapply->checkOpResultList['statusError'], $statusDesc, $this->lang->authorityapply->edit);
            return $res;
        }

        $allowUsers = ['admin', $info->createdBy];
        if (!in_array($account, $allowUsers)) {
            $res['message'] = sprintf($this->lang->authorityapply->checkOpResultList['userError'], $this->lang->authorityapply->edit);
            return $res;
        }
        $res['result'] = true;
        return $res;
    }

    /**
     *是否允许删除
     *
     * @param $info
     * @param $account
     * @return array
     */

    public function checkIsAllowDelete($info, $account)
    {
        $res = array(
            'result' => false,
            'message' => '',
        );
        if (!$info) {
            $res['message'] = $this->lang->common->errorParamId;
            return $res;
        }
        $allowUsers = ['admin'];
        //当前状态
        $status = $info->status;
        if (!in_array($status, $this->lang->authorityapply->allowDeleteStatusArray)) {
            $statusDesc = zget($this->lang->authorityapply->statusList, $status);
            $res['message'] = sprintf($this->lang->authorityapply->checkOpResultList['statusError'], $statusDesc, $this->lang->authorityapply->delete);
            return $res;
        }
        $dealUser = $info->dealUser;
        $dealUser = explode(',', $dealUser);
        $allowUsers = array_merge($allowUsers, $dealUser);
        if (!in_array($account, $allowUsers)) {
            $res['message'] = sprintf($this->lang->authorityapply->checkOpResultList['userError'], $this->lang->authorityapply->delete);
            return $res;
        }
        $res['result'] = true;
        return $res;
    }

    //删除操作
    public function deleteData($info)
    {
        $account = $this->app->user->account;
        //检查是否允许更新
        $res = $this->checkIsAllowDelete($info, $account);
        if (!$res['result']) {
            dao::$errors[] = $res['message'];
            return false;
        }
        $data = new stdClass();
        $data->deleteTime = date('Y-m-d H:i:s');
        $res = $this->dao->update(TABLE_AUTHORITYAPPLY)->data($data)->where('id')->eq($info->id)->exec();
        return $res;
    }

    /**
     * 提交操作
     *
     * @param $authorityapplyId
     * @param $source
     * @return array|bool
     */
    public function submit($authorityapplyId, $source = 'submit')
    {
        $account = $this->app->user->account;
        $info = $this->getByID($authorityapplyId);
        //检查是否允许提交
        $res = $this->checkIsAllowSubmit($info, $account);
        if (!$res['result']) {
            dao::$errors[] = $res['message'];
            return false;
        }
        //校验基本信息
        if ($source == 'submit') { //单独提交需求验证，新增和编辑的提交已经验证
            //检查基本信息
            $res = $this->checkPostParamsInfo($info);
            if (!$res['checkRes']) {
                dao::$errors = $res['errorData'];
                return dao::$errors;
            }
        }
        $processInstanceId = '';
        $version = $info->version;
        if (in_array($info->status, $this->lang->authorityapply->needUpdateVersionStatusArray)) {
            $version = $info->version + 1;
            $processInstanceId = $info->processInstanceId;
        }
        $nodeDealUser['waitsubmit'] = [$info->createdBy];
        $nodeDealUser['waitapplyassigned'] = [$info->thisDeptLeader];
        $nodeDealUser['waitpermissionassigned'] = explode(',', $info->thatDeptLeader);
        $nodeDealUser['waitleaderassigned'] = [$info->thisDeptChargeLeader];
        $nodeDealUser['waitcmassigned'] = explode(',', $info->cm);
        $nodeDealUser['returned'] = [$info->createdBy];
        $nodeDealUser['withdrawn'] = [$info->createdBy];
        $res = $this->loadModel('iwfp')->startWorkFlow_V2($this->config->authorityapply->objectType, $info->id, $info->code ?? $info->id, $info->createdBy, $nodeDealUser, $version, $this->lang->authorityapply->statusLogList, $processInstanceId);
        if ($res) {
            $processInstanceId = $res->processInstanceId;//流程审批ID
            $firstNode = $this->loadModel('iwfp')->completeTaskWithClaim_V2($processInstanceId, $account, '', 1, '', $version);
            if (dao::isError()) {
                return $firstNode;
            }
            //更新表已经提交
            $updateParams = new stdClass();
            $nextStatus = $firstNode->toXmlTask;
            $nextUsers = is_array($firstNode->dealUser) ? implode(',', $firstNode->dealUser) : $firstNode->dealUser;
            $updateParams->status = $nextStatus;
            $updateParams->dealUser = $nextUsers;
            $updateParams->version = $version;
            $updateParams->processInstanceId = $processInstanceId;
            $updateParams->noticeList = json_encode($this->lang->authorityapply->noticeList,JSON_UNESCAPED_UNICODE);
            $this->dao->update(TABLE_AUTHORITYAPPLY)->data($updateParams)->where('id')->eq($authorityapplyId)->exec();
            if (dao::isError()) {
                return dao::getError();
            }

            //添加状态流转
            $this->loadModel('consumed')->record('authorityapply', $authorityapplyId, '0', $account, $info->status, $nextStatus);
            //返回
            $changes = common::createChanges($info, $updateParams);
            $actionID = $this->loadModel('action')->create($this->config->authorityapply->objectType, $authorityapplyId, 'submited', $this->post->comment);
            if ($changes) {
                $this->action->logHistory($actionID, $changes);
            }
        }

        return $res;
    }

    public function checkIsAllowSubmit($info, $account)
    {
        $res = array(
            'result' => false,
            'message' => '',
        );
        if (!$info) {
            $res['message'] = $this->lang->common->errorParamId;
            return $res;
        }
        $allowUsers = ['admin'];
        //当前状态
        $status = $info->status;
        if (!in_array($status, $this->lang->authorityapply->allowSubmitStatusArray)) {
            $statusDesc = zget($this->lang->authorityapply->statusList, $status);
            $res['message'] = sprintf($this->lang->authorityapply->checkOpResultList['statusError'], $statusDesc, $this->lang->authorityapply->submit);
            return $res;
        }
        $dealUser = $info->dealUser;
        $dealUser = explode(',', $dealUser);
        $allowUsers = array_merge($allowUsers, $dealUser);
        if (!in_array($account, $allowUsers)) {
            $res['message'] = sprintf($this->lang->authorityapply->checkOpResultList['userError'], $this->lang->authorityapply->submit);
            return $res;
        }
        $res['result'] = true;
        return $res;
    }

//    审批
    public function approval($info)
    {
        $account = $this->app->user->account;
        //检查是否允许审批
        $res = $this->checkIsAllowApproval($info, $account);
        if (!$res['result']) {
            dao::$errors[] = $res['message'];
            return false;
        }
        $isSubmit = $_POST['issubmit'];
        //提交参数
        $postData = fixer::input('post')
            ->remove('issubmit')
            ->get();

        $res = $this->checkReviewParams($postData);
        if (!$res['checkRes']) {
            dao::$errors = $res['errorData'];
            return false;
        }
        $dealResult = $postData->dealResult;
        $comment = isset($_POST['comment']) ? $_POST['comment'] : '';
        $userVariableList = new stdClass();
        $realPermission = new stdClass();
        $updateParams = new stdClass();
        if ($dealResult == 1) {
            //下一节点为 waitpermissionassigned 待其他部门负责人审批 result=9
            if ($info->status == 'waitapplyassigned' && $info->thatDeptLeader != '') {
                $dealResult = 9;
            } //申请部门到申请部门分管领导审批 result=10
            elseif ($info->status == 'waitapplyassigned' && $info->thatDeptLeader == '' && $info->thisDeptChargeLeader != '') {
                $dealResult = 10;
            } //其他部门到申请部门分管领导审批 result=1 level=2
            elseif ($info->status == 'waitpermissionassigned' && $info->thisDeptChargeLeader != '') {
                $userVariableList->level = 2;
            } //            直接通过
            elseif ($info->status == 'waitpermissionassigned' && $info->thisDeptChargeLeader == '') {
                $userVariableList->level = 1;
            } elseif ($info->status == 'waitcmassigned') {//分配权限
                //实际分配的权限数据格式化
                $formatPermissionData = $this->getFormatRealPermission($postData);
                $realPermission->realPermission = $formatPermissionData;
                if ($isSubmit == 'save') {//暂存分配的权限
                    $this->dao->update(TABLE_AUTHORITYAPPLY)
                        ->data($realPermission)->where('id')->eq($info->id)
                        ->exec();
                    if (dao::isError()) {
                        return dao::getError();
                    }
                    return true;
                }else{
//                    校验参数
                   $res = $this->checkRealContentParams($realPermission);
                    if (!$res['checkRes']) {
                        dao::$errors = $res['errorData'];
                        return false;
                    }
                }
            }
        } //        撤回
        elseif ($dealResult == 3) {
            $approvalNode = $this->loadModel('iwfp')->withdraw_V2($info->processInstanceId, $account, $comment, $info->version, '');
            if (dao::isError()) {
                return $approvalNode;
            }

            //更新表已经提交
            $nextStatus = $approvalNode->toXmlTask;
            $nextUsers = is_array($approvalNode->dealUser) ? implode(',', $approvalNode->dealUser) : $approvalNode->dealUser;
            $updateParams->status = $nextStatus;
            $updateParams->dealUser = $nextUsers;
            $this->dao->update(TABLE_AUTHORITYAPPLY)->data($updateParams)->where('id')->eq($info->id)->exec();

            if (dao::isError()) {
                return dao::getError();
            }
            //添加状态流转
            $this->loadModel('consumed')->record('authorityapply', $info->id, '0', $account, $info->status, $nextStatus);
            $changes = common::createChanges($info, $updateParams);
            $actionID = $this->loadModel('action')->create($this->config->authorityapply->objectType, $info->id, 'deal', $this->post->comment);
            if ($changes) {
                $this->action->logHistory($actionID, $changes);
            }
            return true;
        }elseif($dealResult == 2 && (in_array($info->status,$this->lang->authorityapply->allowWithdrawnStatusArray))){
            $approvalNode = $this->loadModel('iwfp')->reject_V2($info->processInstanceId, $account, $comment, $info->version, '');
            if (dao::isError()) {
                return $approvalNode;
            }
            //更新表已经提交
            $nextStatus = $approvalNode->toXmlTask;
            $nextUsers = is_array($approvalNode->dealUser) ? implode(',', $approvalNode->dealUser) : $approvalNode->dealUser;
            $updateParams->status = $nextStatus;
            $updateParams->dealUser = $nextUsers;
            $this->dao->update(TABLE_AUTHORITYAPPLY)->data($updateParams)->where('id')->eq($info->id)->exec();

            if (dao::isError()) {
                return dao::getError();
            }
            //添加状态流转
            $this->loadModel('consumed')->record('authorityapply', $info->id, '0', $account, $info->status, $nextStatus);
            $changes = common::createChanges($info, $updateParams);
            $actionID = $this->loadModel('action')->create($this->config->authorityapply->objectType, $info->id, 'deal', $this->post->comment);
            if ($changes) {
                $this->action->logHistory($actionID, $changes);
            }
            return true;
        }

        $approvalNode = $this->loadModel('iwfp')->completeTaskWithClaim_V2($info->processInstanceId, $account, $comment, $dealResult, $userVariableList, $info->version, '');
        if (dao::isError()) {
            return $approvalNode;
        }
        if ($dealResult == 11) {
            $updateParams->status = 'terminated';
            $updateParams->dealUser = '';
        } else {
            //更新表已经提交
            $nextStatus = $approvalNode->toXmlTask;
            $nextUsers = is_array($approvalNode->dealUser) ? implode(',', $approvalNode->dealUser) : $approvalNode->dealUser;

            $updateParams->status = $nextStatus;
//            CM分配完成，流程结束
            if ($dealResult == 1 && $info->status == 'waitcmassigned') {
                $updateParams->status = 'ended';
                $updateParams->realPermission = $realPermission->realPermission;
            }
            $updateParams->dealUser = $nextUsers;
        }
        $this->dao->update(TABLE_AUTHORITYAPPLY)->data($updateParams)->where('id')->eq($info->id)->exec();

        if (dao::isError()) {
            return dao::getError();
        }
        $this->loadModel('consumed')->record('authorityapply', $info->id, '0', $account, $info->status, $updateParams->status);
        $actionID = $this->loadModel('action')->create($this->config->authorityapply->objectType, $info->id, 'deal', $this->post->comment);
        //添加状态流转
        $changes = common::createChanges($info, $updateParams);
        if ($changes) {
            $this->action->logHistory($actionID, $changes);
        }
        return $res;
    }

//数据检查
    function checkRealContentParams($realPermission)
    {
        //检查结果
        $checkRes = false;
        $errorData = [];
        $data = [
            'checkRes' => $checkRes,
            'errorData' => $errorData,
        ];

//        验证申请内容
        if ($realPermission->realPermission ) {
            $contentData = json_decode($realPermission->realPermission, true);
            foreach ($contentData as $k => $v) {
                if ($v['involveSubSystem'] == 'dpmp') {
                    if(!isset($v['realZtPermissionOperate']) || $v['realZtPermissionOperate']==''){
                        $errorData['realZtPermissionOperate' . $k] = sprintf($this->lang->error->notempty, $this->lang->authorityapply->realPermissionContent);
                    }
                }
                elseif ($v['involveSubSystem'] == 'svn') {
                    if(!isset($v['realSvnPermissionPath']) || $v['realSvnPermissionPath']==''){
                        $errorData['realSvnPermissionPath' . $k] = sprintf($this->lang->error->notempty, $this->lang->authorityapply->realPermissionContent);
                    }
                    if(!isset($v['realSvnPermissionOperate']) || $v['realSvnPermissionOperate']==''){
                        $errorData['realSvnPermissionOperate' . $k] = sprintf($this->lang->error->notempty, $this->lang->authorityapply->permissionContentOperate);
                    }
                }
                elseif ($v['involveSubSystem'] == 'gitlab'){
                    if(!isset($v['realGitLabPermissionPath']) || $v['realGitLabPermissionPath']=='') {
                        $errorData['realGitLabPermissionPath' . $k] = sprintf($this->lang->error->notempty, $this->lang->authorityapply->realPermissionContent);
                    }
                    if(!isset($v['realGitLabPermissionOperate']) || $v['realGitLabPermissionOperate']=='') {
                        $errorData['realGitLabPermissionOperate' . $k] = sprintf($this->lang->error->notempty, $this->lang->authorityapply->permissionContentOperate);
                    }
                }
                elseif ($v['involveSubSystem'] == 'jenkins'){
                    if(!isset($v['realJenkinsPermissionPath']) || $v['realJenkinsPermissionPath']=='') {
                        $errorData['realJenkinsPermissionPath' . $k] = sprintf($this->lang->error->notempty, $this->lang->authorityapply->realPermissionContent);
                    }
                    if(!isset($v['realJenkinsPermissionOperate']) || $v['realJenkinsPermissionOperate']=='') {
                        $errorData['realJenkinsPermissionOperate' . $k] = sprintf($this->lang->error->notempty, $this->lang->authorityapply->permissionContentOperate);
                    }
                }else{
                    if(!isset($v['realOtherPermissionOperate']) || $v['realOtherPermissionOperate']=='') {
                        $errorData['realOtherPermissionOperate' . $k] = sprintf($this->lang->error->notempty, $this->lang->authorityapply->permissionContentOperate);
                    }
                }
                if (!isset($v['realOpenPermissionPerson']) || $v['realOpenPermissionPerson']=='') {
                    $errorData['realOpenPermissionPerson' . $k] = sprintf($this->lang->error->notempty, $this->lang->authorityapply->realOpenPermissionPerson);

                }
            }
        }
        //基本信息验证
        if ($errorData) {
            $data['errorData'] = $errorData;
        } else {
            $checkRes = true;
            $data['checkRes'] = $checkRes;
        }
        return $data;
    }

    /**
     *是否允许审核
     *
     * @param $info
     * @param $account
     * @return array
     */

    public function checkIsAllowApproval($info, $account)
    {
        $res = array(
            'result' => false,
            'message' => '',
        );
        if (!$info) {
            $res['message'] = $this->lang->common->errorParamId;
            return $res;
        }
        $allowUsers = ['admin',$info->createdBy];
        //当前状态
        $status = $info->status;
        if (!in_array($info->status,$this->lang->authorityapply->allowDealStatusArray) ) {
            $statusDesc = zget($this->lang->authorityapply->statusList, $status);
            $res['message'] = sprintf($this->lang->authorityapply->checkOpResultList['statusError'], $statusDesc, $this->lang->authorityapply->approval);
            return $res;
        }
        $dealUser = $info->dealUser;
        $dealUser = explode(',', $dealUser);
        $allowUsers = array_merge($allowUsers, $dealUser);
        if (!in_array($account, $allowUsers)) {
            $res['message'] = sprintf($this->lang->authorityapply->checkOpResultList['userError'], $this->lang->authorityapply->deal);
            return $res;
        }
        $res['result'] = true;
        return $res;
    }

    /**
     * 校验评审信息
     *
     * @param $postData
     * @return array
     */
    public function checkReviewParams($postData)
    {
        $checkRes = false;
        $errorData = [];
        $data = [
            'checkRes' => $checkRes,
            'errorData' => $errorData,
        ];
        $dealResult = $postData->dealResult ?? '';
        $comment = isset($postData->comment) ? $postData->comment : '';
        if (!$dealResult) {
            $errorData['dealResult'] = sprintf($this->lang->error->notempty, $this->lang->authorityapply->reviewResult);
        }
        if ($dealResult == '2'||$dealResult == '1') { //审批不通过
            if ($comment == '') {
                $errorData['comment'] = $this->lang->authorityapply->reviewCommentEmpty;
            }
        } elseif ($dealResult == '3') {
            if ($comment == '') {
                $errorData['comment'] = $this->lang->authorityapply->withdrawnComment;
            }
        } elseif ($dealResult == '11') {
            if ($comment == '') {
                $errorData['comment'] = $this->lang->authorityapply->terminateComment;
            }
        }

        if (!empty($errorData)) {
            $checkRes = false;
        } else {
            $checkRes = true;
        }
        $data['checkRes'] = $checkRes;
        $data['errorData'] = $errorData;
        return $data;
    }

    //发送邮件通知
    public function sendmail($authorityapplyID, $actionID)
    {
        /* 加载mail模块用于发信通知，获取需求意向和人员信息。*/
        $this->loadModel('mail');
        $authorityapply = $this->getById($authorityapplyID);
        $action = $this->loadModel('action')->getById($actionID);
        $history = $this->action->getHistory($actionID);
        $action->history = isset($history[$actionID]) ? $history[$actionID] : array();
        $users = $this->loadModel('user')->getPairs('noletter');

        /* 获取后台通知中配置的邮件发信。*/
        $this->app->loadLang('custommail');
        $mailConf = isset($this->config->global->setAuthorityapplyMail) ? $this->config->global->setAuthorityapplyMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);
        $browseType = 'authorityapply';
        $mailTitle = vsprintf($mailConf->mailTitle, $mailConf->variables);
//        权限申请完成后，发通知邮件
        if($authorityapply->status=='ended'){
            $mailTitle = str_replace('待办','通知',$mailTitle);
            $mailTitle = str_replace('待处理','已完结',$mailTitle);
            $mailTitle = str_replace('进行处理','查看',$mailTitle);
            $mailConf->mailContent = str_replace('权限申请】处理','权限申请】查看',$mailConf->mailContent);
        }

        $oldcwd = getcwd();
        $modulePath = $this->app->getModulePath($appName = '', 'authorityapply');
        $viewFile = $modulePath . 'view/sendmail.html.php';
        chdir($modulePath . 'view');

        if (file_exists($modulePath . 'ext/view/sendmail.html.php')) {
            $viewFile = $modulePath . 'ext/view/sendmail.html.php';
            chdir($modulePath . 'ext/view');
        }

        ob_start();
        include $viewFile;
        foreach (glob($modulePath . 'ext/view/sendmail.*.html.hook.php') as $hookFile) include $hookFile;
        $mailContent = ob_get_contents();
        ob_end_clean();
        chdir($oldcwd);

        /* 获取发信人和抄送人数据。*/
        $sendUsers = $this->getToAndCcList($authorityapply);
        if (!$sendUsers) return;
        list($toList, $ccList) = $sendUsers;
        $subject = $mailTitle;
        /* Send mail. */
        /* 调用mail模块的send方法进行发信。*/
        $this->loadModel('mail')->send($toList, $subject, $mailContent, $ccList);
        if ($this->loadModel('mail')->isError()) trigger_error(join("\n", $this->loadModel('mail')->getError()));
    }

    public function getToAndCcList($info)
    {
        $consumed = $this->loadModel('consumed')->getObjectByID($info->id, 'authorityapply', $info->status,'id desc');

        /* Set toList and ccList. */
        /* 初始化收信人和抄送人变量，获取发信人和抄送人数据。*/
        $ccList = '';
        $toList = '';
        $status = $info->status;

        if (in_array($status, $this->lang->authorityapply->noLetterStatus)||$consumed->before==$consumed->after) {
            return false;
        }
         if($status=='ended'){
                $toList = $info->createdBy;
            }else{
                $toList = $info->dealUser;
         }
        return array($toList, $ccList);
    }

    public static function isClickable($authorityapply, $action)
    {
        global $app;
        global $lang;
        $action = strtolower($action);
        switch (strtolower($action)) {

            case 'submit': //提交
                return in_array($authorityapply->status, $lang->authorityapply->allowSubmitStatusArray) and $app->user->account == $authorityapply->createdBy;
            case 'edit': //编辑
                return in_array($authorityapply->status, $lang->authorityapply->allowEditStatusArray) and $app->user->account == $authorityapply->createdBy;
            case 'deal': //处理
                $dealUserArr = explode(',', str_replace(' ', '', $authorityapply->dealUser));
                if (in_array($authorityapply->status, $lang->authorityapply->allowApprovalStatusArray) && $app->user->account == $authorityapply->createdBy) {
                    return true;
                } else {
                    return in_array($authorityapply->status, $lang->authorityapply->allowDealStatusArray) and (in_array($app->user->account, $dealUserArr));
                }
            case 'delete': //删除
                return $app->user->account == $authorityapply->createdBy || $app->user->account == 'admin' and (in_array($authorityapply->status, $lang->authorityapply->allowDeleteStatusArray));
            default:
                return true;
        }
    }
    /**
     * @Notes:喧喧
     * @Date: 2024/1/10
     * @Time: 17:53
     * @Interface getXuanxuanTargetUser
     * @param $obj
     * @param $objectType
     * @param $objectID
     * @param $actionType
     * @param $actionID
     * @param string $actor
     * @return array|false
     */
    public function getXuanxuanTargetUser($obj, $objectType, $objectID, $actionType, $actionID, $actor = ''){
        $info   = $this->getById($objectID);
        $sendUsers = $this->getToAndCcList($info);

        if(!$sendUsers) return;
        $toList = $sendUsers[0];
        $server   = $this->loadModel('im')->getServer('zentao');
        $url = $server.'/authorityapply-view-'.$objectID.'.html';
        $subcontent = [];
        $subcontent['headTitle']    = '';
        $subcontent['headSubTitle'] = '';
        $subcontent['count']        = 0;
        $subcontent['id']           = 0;
        $subcontent['parent']       = '';
        $subcontent['parentURL']    = "";
        $subcontent['cardURL']      = $url;
        $subcontent['name']         = $obj->code;//消息体 编号后边位置 标题

        //标题
        $title = '';
        $actions = [];

        return ['toList' => $toList,'subcontent' => $subcontent,'url' => $url,'title' => $title,'actions' => $actions];
    }
}


