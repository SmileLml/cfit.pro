<?php

class environmentorderModel extends model
{

    /**
     * Method: getList
     * @param $browseType
     * @param $queryID
     * @param $orderBy
     * @param null $pager
     * @return mixed
     */
    public function getList($browseType, $queryID, $orderBy, $pager = null)
    {
        $environmentorderQuery = '';
        if ($browseType == 'bysearch') {
            $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : '';
            if ($query) {
                $this->session->set('environmentorderQuery', $query->sql);
                $this->session->set('environmentorderForm', $query->form);
            }

            if ($this->session->environmentorderQuery == false) $this->session->set('environmentorderQuery', ' 1 = 1');
            $environmentorderQuery = $this->session->environmentorderQuery;
        }
        $environmentorders = $this->dao->select('*')->from(TABLE_ENVIRONMENTORDER)
            ->where('deleteTime is null')
            ->beginIF($browseType == 'tomedeal')->andWhere("FIND_IN_SET('{$this->app->user->account}', dealUser)")->fi()
            ->beginIF($browseType == 'bysearch')->andWhere($environmentorderQuery)->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');
        return $environmentorders;
    }


    /**
     * Project: chengfangjinke
     * Method: buildSearchForm
     * @param $queryID
     * @param $actionURL
     */
    public function buildSearchForm($queryID, $actionURL)
    {
        $this->config->environmentorder->search['actionURL'] = $actionURL;
        $this->config->environmentorder->search['queryID'] = $queryID;
        $this->loadModel('search')->setSearchParams($this->config->environmentorder->search);
    }

    /**
     * Project: chengfangjinke
     * Method: diffColorPriority
     * @param $priority
     * @return string
     */
    public function diffColorPriority($priority) {
        $priorityClass = '';
        switch ($priority) {
            case '1':
                $priorityClass = 'label-success';
                break;
            case '2':
                $priorityClass = 'label-warning';
                break;
            case '3':
                $priorityClass = 'label-danger';
                break;
        }
        return $priorityClass;
    }
    public static function isClickable($environmentorder, $action)
    {
        global $app;
        global $lang;
        $action = strtolower($action);
        switch (strtolower($action)){
            case 'create': //创建
                return in_array($app->user->account, array_keys($lang->environmentorder->createByList));
            case 'submit': //提交
                return in_array($environmentorder->status, $lang->environmentorder->allowSubmitStatusArray) and $app->user->account == $environmentorder->createdBy;
            case 'edit': //编辑
                return in_array($environmentorder->status, $lang->environmentorder->allowEditStatusArray) and $app->user->account == $environmentorder->createdBy;
            case 'deal': //处理
                $dealUserArr = explode(',', str_replace(' ', '', $environmentorder->dealUser));
                return in_array($environmentorder->status, $lang->environmentorder->allowDealStatusArray) and (in_array($app->user->account, $dealUserArr));
            case 'delete': //删除
                return $app->user->account == $environmentorder->createdBy || $app->user->account == 'admin'and (in_array($environmentorder->status, $lang->environmentorder->allowDeleteStatusArray));
            default:
                return true;
        }
    }
    /**
     * 创建工单
     *
     * @return array
     */
    public function create()
    {
        $isWarn = $_POST['isWarn']; //是否需要发出警告信息
        $issubmit = $_POST['issubmit']; //提交还是保存
        $postData = fixer::input('post')
            ->remove('isWarn,issubmit,files,uid')
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
        $status = $this->lang->environmentorder->statusArray['waitsubmit'];//待提交
        $dealUser = $currentUser;
        $postData->createdBy = $currentUser;
//        $postData->createdDept = $this->app->user->dept;
        $postData->createdTime = helper::now();
        $postData->status = $status;
        $postData->dealUser = $dealUser;
        $postData->code = $this->getCode();
//        $postData = $this->loadModel('file')->processImgURL($postData, $this->config->environmentorder->editor->create['id'], $this->post->uid);

        $this->dao->begin();
        $this->dao->insert(TABLE_ENVIRONMENTORDER)->data($postData)->batchCheckIF($issubmit != 'save', $this->config->environmentorder->create->requiredFields, 'notempty')->exec();

        $recordId = $this->dao->lastInsertId();

        if (!dao::isError()) {
            $objectType = $this->config->environmentorder->objectType;
            //图片、附件信息
            $this->loadModel('file')->saveUpload($objectType, $recordId);
            $this->file->updateObjectID($this->post->uid, $recordId, $objectType);

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

    function getFormatPostData($postData, $op = "create")
    {

//    部署信息列表
        if (!empty($postData->ip) || !empty($postData->remark)) {
            $list = [];
            foreach ($postData->ip as $k=>$v){
                $list[$k]['ip'] = $postData->ip[$k]??'';
                $list[$k]['remark'] = $postData->remark[$k]??"";
//                上传文件
                if ($op == "create") {
                    if (isset($_FILES['files']['tmp_name'][$k])) {
                        $list[$k]['material'] = hash_file('md5', $_FILES['files']['tmp_name'][$k]);
                    } else {
                        $list[$k]['material'] = '';
                    }
                } else {
                    if (isset($postData->material[$k])&&!empty($postData->material[$k])) {
                        $list[$k]['material'] = $postData->material[$k];
                    } else {

                        if (isset($_FILES['files']['tmp_name'][$k])) {
                            $list[$k]['material'] = hash_file('md5', $_FILES['files']['tmp_name'][$k]);
                        } else {
                            $list[$k]['material'] = '';
                        }
                    }
                }
            }
            $postData->list = !empty($list)?json_encode(array_values($list)):[];
            unset($postData->ip);
            unset($postData->remark);
            unset($postData->material);
        }

        return $postData;
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
        $requiredFields = explode(',', $this->config->environmentorder->create->requiredFields);
        foreach ($requiredFields as $requiredField) {
            if (!isset($params->$requiredField) || empty($params->$requiredField)) {
                $errorData[$requiredField] = sprintf($this->lang->error->notempty, $this->lang->environmentorder->$requiredField);
            }
        }
        //基本信息验证
        if($errorData){
            $data['errorData'] = $errorData;
        }else{
            $checkRes = true;
            $data['checkRes'] = $checkRes;
        }

        return $data;
    }

//    获取详情
    public function getById($id)
    {
        $ret = $this->dao->select('*')
            ->from(TABLE_ENVIRONMENTORDER)
            ->where('deleteTime is null')
            ->andWhere('id')->eq($id)
            ->fetch();
        if (!$ret) {
            return false;
        }
//        $ret = $this->loadModel('file')->replaceImgURL($ret, $this->config->environmentorder->editor->create['id']);
        $objectType = $this->config->environmentorder->objectType;
        $ret->files = $this->loadModel('file')->getByObject($objectType, $ret->id);
        if ($ret->list) {
            $list = json_decode($ret->list, true);
            foreach ($list as $k => &$v) {
                foreach ($ret->files as $file) {
                    if ($v['material'] == $file->extra) {
                        $list[$k]['fileUrl'] = $file->webPath;
                        $list[$k]['file']=$file;
                    }
                }
            }
            $ret->list = json_encode($list);
        }
        return $ret;
    }

    /**
     * 编辑
     *
     * @return array
     */
    public function update($environmentorderId,$info)
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
        $isWarn = $_POST['isWarn'];
        $issubmit = $_POST['issubmit']; //提交还是保存
        $postData = fixer::input('post')
            ->remove('uid,files,isWarn,issubmit')
//            ->stripTags($this->config->environmentorder->editor->edit['id'], $this->config->allowedTags)
            ->get();
        $postData = $this->getFormatPostData($postData, $op);

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
        $this->dao->update(TABLE_ENVIRONMENTORDER)
            ->data($postData)->batchCheckIF($issubmit != 'save', $this->config->environmentorder->edit->requiredFields, 'notempty')->where('id')->eq($environmentorderId)
            ->exec();
        if (!dao::isError()) {
            //状态流转
            $objectType = $this->config->environmentorder->objectType;
            //图片、附件信息
            $this->loadModel('file')->saveUpload($objectType, $environmentorderId);
            $this->file->updateObjectID($this->post->uid, $environmentorderId, $objectType);
            $changes = common::createChanges($info, $postData);
            $actionID = $this->loadModel('action')->create($objectType, $environmentorderId, 'edited');
            if ($changes) {
                $this->action->logHistory($actionID, $changes);
            }
            if ($issubmit == 'submit') {
                $res = $this->submit($environmentorderId,true);
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

    public function getCode($createTime = '')
    {
        if (!$createTime) {
            $createTime = strtotime(helper::today());
        }
        $codePrefix = 'CFIT-EO-';
        $createDay = date('Ymd-', $createTime);
        $codeTemp = $codePrefix . $createDay;
        $number = $this->dao->select('count(id) c')->from(TABLE_ENVIRONMENTORDER)->where('code')->like($codeTemp . "%")->fetch('c');
        $number = intval($number) + 1;
        $code = $codeTemp . sprintf('%02d', $number);
        return $code;
    }
//    检查是否允许编辑
    public function checkIsAllowEdit($info, $account){
        $res = array(
            'result'  => false,
            'message' => '',
        );
        if(!$info){
            $res['message'] = $this->lang->common->errorParamId;
            return $res;
        }
        //当前状态
        $status = $info->status;
        if(!in_array($status, $this->lang->environmentorder->allowEditStatusArray)){
            $statusDesc = zget($this->lang->environmentorder->statusList, $status);
            $res['message'] = sprintf($this->lang->environmentorder->checkOpResultList['statusError'], $statusDesc, $this->lang->environmentorder->edit);
            return $res;
        }

        $allowUsers = ['admin', $info->createdBy];
        if(!in_array($account, $allowUsers)){
            $res['message'] = sprintf($this->lang->environmentorder->checkOpResultList['userError'], $this->lang->environmentorder->edit);
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

    public function checkIsAllowDelete($info, $account){
        $res = array(
            'result'  => false,
            'message' => '',
        );
        if(!$info){
            $res['message'] = $this->lang->common->errorParamId;
            return $res;
        }
        $allowUsers = ['admin'];
        //当前状态
        $status = $info->status;
        if(!in_array($status, $this->lang->environmentorder->allowDeleteStatusArray)){
            $statusDesc = zget($this->lang->environmentorder->statusList, $status);
            $res['message'] = sprintf($this->lang->environmentorder->checkOpResultList['statusError'], $statusDesc, $this->lang->environmentorder->delete);
            return $res;
        }
        $dealUser = $info->dealUser;
        $dealUser = explode(',', $dealUser);
        $allowUsers = array_merge($allowUsers, $dealUser);
        if(!in_array($account, $allowUsers)){
            $res['message'] = sprintf($this->lang->environmentorder->checkOpResultList['userError'], $this->lang->environmentorder->delete);
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
        $data->deleteTime  = date('Y-m-d H:i:s');
        $res = $this->dao->update(TABLE_ENVIRONMENTORDER)->data($data)->where('id')->eq($info->id)->exec();
        return $res;
    }
    /**
     *是否允许审核
     *
     * @param $info
     * @param $account
     * @return array
     */

    public function checkIsAllowApproval($info, $account){
        $res = array(
            'result'  => false,
            'message' => '',
        );
        if(!$info){
            $res['message'] = $this->lang->common->errorParamId;
            return $res;
        }
        $allowUsers = ['admin'];
        //当前状态
        $status = $info->status;
        if(!in_array($status, $this->lang->environmentorder->allowApprovalStatusArray)){
            $statusDesc = zget($this->lang->environmentorder->statusList, $status);
            $res['message'] = sprintf($this->lang->environmentorder->checkOpResultList['statusError'], $statusDesc, $this->lang->environmentorder->approve);
            return $res;
        }
        $dealUser = $info->dealUser;
        $dealUser = explode(',', $dealUser);
        $allowUsers = array_merge($allowUsers, $dealUser);
        if(!in_array($account, $allowUsers)){
            $res['message'] = sprintf($this->lang->environmentorder->checkOpResultList['userError'], $this->lang->environmentorder->approve);
            return $res;
        }
        $res['result'] = true;
        return $res;
    }
    /**
     *是否允许任务确认
     *
     * @param $info
     * @param $account
     * @return array
     */

    public function checkIsAllowConfirm($info, $account){
        $res = array(
            'result'  => false,
            'message' => '',
        );
        if(!$info){
            $res['message'] = $this->lang->common->errorParamId;
            return $res;
        }
        //当前状态
        $status = $info->status;
        if(!in_array($status, $this->lang->environmentorder->allowConfirmStatusArray)){
            $statusDesc = zget($this->lang->environmentorder->statusList, $status);
            $res['message'] = sprintf($this->lang->environmentorder->checkOpResultList['statusError'], $statusDesc, $this->lang->environmentorder->confirm);
            return $res;
        }
        $dealUser = $info->dealUser;
        $dealUser = explode(',', $dealUser);
        if(!in_array($account, $dealUser)){
            $res['message'] = sprintf($this->lang->environmentorder->checkOpResultList['userError'], $this->lang->environmentorder->confirm);
            return $res;
        }
        $res['result'] = true;
        return $res;
    }
    public function checkIsAllowVerify($info, $account){
        $res = array(
            'result'  => false,
            'message' => '',
        );
        if(!$info){
            $res['message'] = $this->lang->common->errorParamId;
            return $res;
        }
        //当前状态
        $status = $info->status;
        if(!in_array($status, $this->lang->environmentorder->allowVerifyStatusArray)){
            $statusDesc = zget($this->lang->environmentorder->statusList, $status);
            $res['message'] = sprintf($this->lang->environmentorder->checkOpResultList['statusError'], $statusDesc, $this->lang->environmentorder->verify);
            return $res;
        }
        $dealUser = $info->dealUser;
        $dealUser = explode(',', $dealUser);
        if(!in_array($account, $dealUser)){
            $res['message'] = sprintf($this->lang->environmentorder->checkOpResultList['userError'], $this->lang->environmentorder->verify);
            return $res;
        }
        $res['result'] = true;
        return $res;
    }
    /**
     *是否允许任务确认
     *
     * @param $info
     * @param $account
     * @return array
     */

    public function checkIsAllowImplement($info, $account){
        $res = array(
            'result'  => false,
            'message' => '',
        );
        if(!$info){
            $res['message'] = $this->lang->common->errorParamId;
            return $res;
        }
        //当前状态
        $status = $info->status;
        if(!in_array($status, $this->lang->environmentorder->allowImplementStatusArray)){
            $statusDesc = zget($this->lang->environmentorder->statusList, $status);
            $res['message'] = sprintf($this->lang->environmentorder->checkOpResultList['statusError'], $statusDesc, $this->lang->environmentorder->implement);
            return $res;
        }
        $dealUser = $info->dealUser;
        $dealUser = explode(',', $dealUser);
        if(!in_array($account, $dealUser)){
            $res['message'] = sprintf($this->lang->environmentorder->checkOpResultList['userError'], $this->lang->environmentorder->implement);
            return $res;
        }
        $res['result'] = true;
        return $res;
    }
    /**
     *是否允许任务提交
     *
     * @param $info
     * @param $account
     * @return array
     */

    public function checkIsAllowSubmit($info, $account){
        $res = array(
            'result'  => false,
            'message' => '',
        );
        if(!$info){
            $res['message'] = $this->lang->common->errorParamId;
            return $res;
        }
        $allowUsers = ['admin'];
        //当前状态
        $status = $info->status;
        if(!in_array($status, $this->lang->environmentorder->allowSubmitStatusArray)){
            $statusDesc = zget($this->lang->environmentorder->statusList, $status);
            $res['message'] = sprintf($this->lang->environmentorder->checkOpResultList['statusError'], $statusDesc, $this->lang->environmentorder->submit);
            return $res;
        }
        $dealUser = $info->dealUser;
        $dealUser = explode(',', $dealUser);
        $allowUsers = array_merge($allowUsers, $dealUser);
        if(!in_array($account, $allowUsers)){
            $res['message'] = sprintf($this->lang->environmentorder->checkOpResultList['userError'], $this->lang->environmentorder->submit);
            return $res;
        }
        $res['result'] = true;
        return $res;
    }
    /**
     * 提交操作
     *
     * @param $environmentorderId
     * @param $source
     * @return array|bool
     */
    public function submit($environmentorderId, $source = 'submit'){
        $account = $this->app->user->account;
        $info = $this->getByID($environmentorderId);
        //检查是否允许提交
        $res = $this->checkIsAllowSubmit($info, $account);
        if(!$res['result']){
            dao::$errors[] = $res['message'];
            return false;
        }
        //校验基本信息
        if($source == 'submit') { //单独提交需求验证，新增和编辑的提交已经验证
            //检查基本信息
            $res = $this->checkPostParamsInfo($info);
            if (!$res['checkRes']) {
                dao::$errors = $res['errorData'];
                return dao::$errors;
            }
        }
        $processInstanceId = '';
        $version = $info->version;
        if(in_array($info->status, $this->lang->environmentorder->needUpdateVersionStatusArray)){
            $version = $info->version + 1;
            $processInstanceId=$info->processInstanceId;
        }
        $nodeDealUser = $this->getAllReviewerInfo($info);
        $res =$this->loadModel('iwfp')->startWorkFlow_V2($this->config->environmentorder->objectType, $info->id, $info->code??$info->id, $info->createdBy, $nodeDealUser, $version, $this->lang->environmentorder->statusLogList, $processInstanceId);
        if($res){

            $processInstanceId = $res->processInstanceId;//流程审批ID
            $firstNode = $this->loadModel('iwfp')->completeTaskWithClaim_V2($processInstanceId, $account, '',1, '', $version);
            if(dao::isError()) {
                return $firstNode;
            }
            //更新表已经提交
            $updateParams = new stdClass();
            $nextStatus = $firstNode->toXmlTask;
            $nextUsers  = is_array($firstNode->dealUser) ? implode(',', $firstNode->dealUser):$firstNode->dealUser;
            $updateParams->status   = $nextStatus;
            $updateParams->dealUser = $nextUsers;
            $updateParams->version  = $version;
            $updateParams->processInstanceId = $processInstanceId;
            $this->dao->update(TABLE_ENVIRONMENTORDER)->data($updateParams)->where('id')->eq($environmentorderId)->exec();

            if(dao::isError()) {
                return dao::getError();
            }

            //添加状态流转
            $this->loadModel('consumed')->record('environmentorder', $environmentorderId, '0', $account, $info->status, $nextStatus);
            //返回
            $changes = common::createChanges($info, $updateParams);
            $actionID = $this->loadModel('action')->create($this->config->environmentorder->objectType, $environmentorderId, 'submited', $this->post->comment);
            if($changes) {
                $this->action->logHistory($actionID, $changes);
            }
        }

        return $res;
    }

    public function getAllReviewerInfo($info){
//        审批人
//        $queryApprovalSql="SELECT account FROM" . TABLE_USERGROUP . " WHERE `group` =". $this->config->environmentorder->approvalGroupId;
//        $waitapproval=$this->dao->query($queryApprovalSql)->fetchAll();
//          $approvalList=[];
//          if($waitapproval){
//              foreach ($waitapproval as $v){
//                  $approvalList[]=$v->account;
//              }
//          }
        $allReviewerInfo['waitsubmit']   =  [$info->createdBy];
        $allReviewerInfo['rejectapproval']   = [$info->createdBy];
        $allReviewerInfo['rejectimplement']   =  [$info->createdBy];
        $allReviewerInfo['waitapproval']   =  array_keys($this->lang->environmentorder->reviewerList);
        $allReviewerInfo['rejectconfirm']   =  array_keys($this->lang->environmentorder->reviewerList);
        $allReviewerInfo['waitconfirm']   =  [];
        $allReviewerInfo['waitimplement']   =  [];
        $allReviewerInfo['waitverify']   =  [$info->createdBy];
        $allReviewerInfo['rejectverify']   =  [];
//        $allReviewerInfo=json_encode($allReviewerInfo);
        return $allReviewerInfo;
    }

//    系统部工单执行人员
//        public function getImplementUser()
//        {
//            $queryExecutorSql="SELECT u.account,u.realname FROM " . TABLE_USERGROUP . " as ug left join ".TABLE_USER." as u on u.account=ug.account WHERE `group` =". $this->config->environmentorder->exectorGroupId;
//            $userList=$this->dao->query($queryExecutorSql)->fetchAll();
//            $executor=array_column($userList,'realname','account');
//            return $executor;
//        }
//        工单审核
        public function approval($info)
        {
            $account = $this->app->user->account;
            //检查是否允许审批
            $res = $this->checkIsAllowApproval($info, $account);
            if(!$res['result']){
                dao::$errors[] = $res['message'];
                return false;
            }
            //提交参数
            $postData = fixer::input('post')
                ->get();
            $res = $this->checkReviewParams($postData);
            if(!$res['checkRes']){
                dao::$errors = $res['errorData'];
                return false;
            }
            $dealResult = $postData->dealResult;
            $comment = isset($_POST['comment'])? $_POST['comment'] :'';
            $updateParams = new stdClass();
            if($dealResult==1){
                $dealUser = $postData->executor;//下一节点处理人
                $updateParams->reviewer = $account;
                $updateParams->executor = is_array($postData->executor) ? implode(',', $postData->executor):$postData->executor;;
            }else{
                $dealUser =[$info->createdBy];
                $updateParams->executor = '';
                $updateParams->reviewer = '';
            }
            $approvalNode = $this->loadModel('iwfp')->completeTaskWithClaim_V2($info->processInstanceId, $account, $comment, $dealResult, '', $info->version,$dealUser);
            if(dao::isError()) {
                return $approvalNode;
            }
            //更新表已经提交
            $nextStatus = $approvalNode->toXmlTask;
            $nextUsers  = is_array($approvalNode->dealUser) ? implode(',', $approvalNode->dealUser):$approvalNode->dealUser;
            $updateParams->status   = $nextStatus;
            $updateParams->dealUser = $nextUsers;
            $this->dao->update(TABLE_ENVIRONMENTORDER)->data($updateParams)->where('id')->eq($info->id)->exec();

            if(dao::isError()) {
                return dao::getError();
            }

            //添加状态流转
            $this->loadModel('consumed')->record('environmentorder', $info->id, '0', $account, $info->status, $nextStatus);
            //$info->id
            $changes = common::createChanges($info, $updateParams);
            $actionID = $this->loadModel('action')->create($this->config->environmentorder->objectType, $info->id, 'deal', $this->post->comment);
            if($changes) {
                $this->action->logHistory($actionID, $changes);
            }
            return $res;
        }

            //任务确认
            public function confirm($info)
            {
                $account = $this->app->user->account;
                //检查是否允许确认
                $res = $this->checkIsAllowConfirm($info, $account);
                if(!$res['result']){
                    dao::$errors[] = $res['message'];
                    return false;
                }
                //提交参数
                $postData = fixer::input('post')
                    ->get();
                $res = $this->checkConfirmParams($postData);
                if(!$res['checkRes']){
                    dao::$errors = $res['errorData'];
                    return false;
                }
                $updateParams = new stdClass();
                $dealResult = $postData->dealResult;
                $assignResult = $postData->assignResult;
                $comment = isset($_POST['comment'])? $_POST['comment'] :'';
                $dealUser=[];
                // 判断是否受理
                if($dealResult==1){
                // 判断是否指派
                    if($assignResult==2){
//                        不指派那就是同意该任务
                         $dealUser =explode(',', $info->dealUser);//下一处理人
                            $key=array_search($account,$dealUser);
                            if($key!==false){
                                unset($dealUser[$key]);
                            }
                            if(empty($dealUser)){$dealUser =explode(',', $info->executor);}
                        $updateParams->dealUser = implode(',',$dealUser);
                    }else{
                        //指派
                    $this->loadModel('iwfp')->changeAssigneek($info->processInstanceId, $account, $postData->executor,$info->version);
                    if(dao::isError()) {
                        return false;
                    }

                    $this->loadModel('action')->create($this->config->environmentorder->objectType, $info->id, 'assigned','', $postData->executor,$account);

                    $arr=explode(',',$info->executor);
                   foreach ($arr as $k=>&$v){
                       if($v==$account){
                           $v=$postData->executor;
                       }
                   }
//                   新的待处理人
                        $arr1=explode(',',$info->dealUser);
                        foreach ($arr1 as $k=>&$v){
                            if($v==$account){
                                $v=$postData->executor;
                            }
                        }

                    $data=new stdClass();
                    $data->executor=implode(',',$arr);
                    $data->dealUser=implode(',',$arr1);
                    $this->dao->update(TABLE_ENVIRONMENTORDER)->data($data)->where('id')->eq($info->id)->exec();
                    if(!dao::isError()) {
                        return true;
                    }
                    return true;
                    }

                }else{
                       //不受理时执行人中删掉自己
                        $arr=explode(',',$info->executor);
                        $key=array_search($account,$arr);
                        if($key!==false){
                            unset($arr[$key]);
                        }
                        $executor=implode(',',$arr);
                        $updateParams->executor=$executor;
                        //不受理时处理人中删掉自己
                        $dealUser=explode(',',$info->dealUser);
                        $key = array_search($account, $dealUser);
                        if ($key !== false) {
                            unset($dealUser[$key]);
                        }
                    if(empty($dealUser)&&!empty($arr)){
                        $dealUser =$arr;
                    }
                    $updateParams->dealUser = implode(',',$dealUser);
                }
                $confirmNode = $this->loadModel('iwfp')->completeTaskWithClaim_V2($info->processInstanceId, $account, $comment, $dealResult, '', $info->version,$dealUser);
                if(dao::isError()) {
                    return $confirmNode;
                }
                //更新表已经提交
                $nextStatus = $confirmNode->toXmlTask;
                $updateParams->status   = $nextStatus;
                if($nextStatus==$this->lang->environmentorder->statusArray['rejectconfirm']){
                    $updateParams->dealUser = implode(',', array_keys($this->lang->environmentorder->reviewerList));
                    $updateParams->executor='';
                    $updateParams->reviewer='';
                }
                $this->dao->update(TABLE_ENVIRONMENTORDER)->data($updateParams)->where('id')->eq($info->id)->exec();
                if(dao::isError()) {
                    return dao::getError();
                }

                //添加状态流转
                $this->loadModel('consumed')->record('environmentorder', $info->id, '0', $account, $info->status, $nextStatus);
                //$info->id
                $changes = common::createChanges($info, $updateParams);
                $actionID = $this->loadModel('action')->create($this->config->environmentorder->objectType, $info->id, 'deal', $this->post->comment);
                if($changes) {
                    $this->action->logHistory($actionID, $changes);
                }
                return $res;
            }
            //任务实施
            public function implement($info)
            {
                $account = $this->app->user->account;
                //检查是否允许实施
                $res = $this->checkIsAllowImplement($info, $account);
                if(!$res['result']){
                    dao::$errors[] = $res['message'];
                    return false;
                }
                //提交参数
                $postData = fixer::input('post')
                    ->get();
                $res = $this->checkImplementParams($postData);
                if(!$res['checkRes']){
                    dao::$errors = $res['errorData'];
                    return false;
                }
                $dealResult = $postData->dealResult;
                $comment = isset($_POST['comment'])? $_POST['comment'] :'';
              //实施完成记录工时
                $updateParams = new stdClass();
                if($dealResult==1){
                       // 记录工时，追加
                        $newWokrHourArray=json_decode($info->workHour,true);
                        $newWokrHourObj=["name"=>$account,"workHour"=>$postData->workHour];
                        $newWokrHourArray[]=$newWokrHourObj;
                        $updateParams->workHour=json_encode($newWokrHourArray);
                }else{
                    //不受理时执行人中删掉自己
                    $arr=explode(',',$info->executor);
                    if(count($arr)>1){
                        $key=array_search($account,$arr);
                        if($key!==false){
                            unset($arr[$key]);
                        }
                        $executor=implode(',',$arr);
                        $updateParams->executor=$executor;
                    }
                }
                //处理人
                $dealUser=explode(',',$info->dealUser);
                if(count($dealUser)>1){
                    $key=array_search($account,$dealUser);
                    if($key!==false){
                        unset($dealUser[$key]);
                    }
                }else{
                    $dealUser=[$info->createdBy];//下一节点处理人

                }
                $confirmNode = $this->loadModel('iwfp')->completeTaskWithClaim_V2($info->processInstanceId, $account, $comment, $dealResult, '', $info->version,$dealUser);
                if(dao::isError()) {
                    return $confirmNode;
                }
                //更新表已经提交
                $nextStatus = $confirmNode->toXmlTask;
                $updateParams->status   = $nextStatus;
                if($nextStatus==$this->lang->environmentorder->statusArray['rejectimplement']){
                    //清空工时
                    $updateParams->workHour = '[]';
                    $updateParams->executor='';
                    $updateParams->reviewer='';
                }
                $updateParams->dealUser =implode(',',$dealUser);
                $this->dao->update(TABLE_ENVIRONMENTORDER)->data($updateParams)->where('id')->eq($info->id)->exec();
                if(dao::isError()) {
                    return dao::getError();
                }

                //添加状态流转
                $this->loadModel('consumed')->record('environmentorder', $info->id, '0', $account, $info->status, $nextStatus);
                //$info->id
                $changes = common::createChanges($info, $updateParams);
                $actionID = $this->loadModel('action')->create($this->config->environmentorder->objectType, $info->id, 'deal', $this->post->comment);
                if($changes) {
                    $this->action->logHistory($actionID, $changes);
                }
                return $res;
            }
            //任务核验
            public function verify($info)
            {
                $account = $this->app->user->account;
                //检查是否允许确认
                $res = $this->checkIsAllowVerify($info, $account);
                if(!$res['result']){
                    dao::$errors[] = $res['message'];
                    return false;
                }
                //提交参数
                $postData = fixer::input('post')
                    ->get();
                $res = $this->checkVerifyParams($postData);
                if(!$res['checkRes']){
                    dao::$errors = $res['errorData'];
                    return false;
                }
                $dealResult = $postData->dealResult;
                $comment = isset($_POST['comment'])? $_POST['comment'] :'';
                if($dealResult==2){
                    $dealUser =explode(',', $info->executor);//下一节点处理人
                    //清空工时
                    $wk = new stdClass();
                    $wk->workHour = '[]';
                    $this->dao->update(TABLE_ENVIRONMENTORDER)->data($wk)->where('id')->eq($info->id)->exec();
                }else{
                    $dealUser = [];
                }
                $verifyNode = $this->loadModel('iwfp')->completeTaskWithClaim_V2($info->processInstanceId, $account, $comment, $dealResult, '', $info->version,$dealUser);
                if(dao::isError()) {
                    return $verifyNode;
                }
                //更新表已经提交
                $updateParams = new stdClass();
                $nextStatus = $verifyNode->toXmlTask;
//                $nextUsers  = is_array($verifyNode->dealUser) ? implode(',', $verifyNode->dealUser):$verifyNode->dealUser;
                $nextUsers  = is_array($dealUser) ? implode(',', $dealUser):$dealUser;
                $updateParams->status   = $nextStatus;
                if($verifyNode->isEnd==1){
                    $updateParams->status =$this->lang->environmentorder->statusArray['archived'] ;
                }
                $updateParams->dealUser = $nextUsers;
        //            $updateParams->mailto   = $data->mailto;
                $this->dao->update(TABLE_ENVIRONMENTORDER)->data($updateParams)->where('id')->eq($info->id)->exec();

                if(dao::isError()) {
                    return dao::getError();
                }

                //添加状态流转
                $this->loadModel('consumed')->record('environmentorder', $info->id, '0', $account, $info->status, $nextStatus);
                //$info->id
                $changes = common::createChanges($info, $updateParams);
                $actionID = $this->loadModel('action')->create($this->config->environmentorder->objectType, $info->id, 'deal', $this->post->comment);
                if($changes) {
                    $this->action->logHistory($actionID, $changes);
                }
                return $res;
            }
            /**
             * 校验评审信息
             *
             * @param $postData
             * @return array
             */
            public function checkReviewParams($postData){
                $checkRes   = false;
                $errorData  = [];
                $data = [
                    'checkRes'  => $checkRes,
                    'errorData' => $errorData,
                ];
                $dealResult = $postData->dealResult;
                $comment = isset($postData->comment) ? $postData->comment :'';
                $executor = isset($postData->executor) ? $postData->executor :'';
                if(!$dealResult){
                    $errorData['dealResult'] = sprintf($this->lang->error->notempty, $this->lang->environmentorder->dealResult);
                }
                if($dealResult == '2'){ //审批不通过
                    if($comment == ''){
                        $errorData['comment'] = $this->lang->environmentorder->reviewCommentEmpty;
                    }
                }
                if($dealResult == '1'){
                    if($executor == ''){
                        $errorData['executor'] = $this->lang->environmentorder->reviewExecutorEmpty;
                    }
                }
                if(!empty($errorData)){
                    $checkRes = false;
                }else{
                    $checkRes = true;
                }
                $data['checkRes'] = $checkRes;
                $data['errorData'] = $errorData;
                return $data;
            }
            public function checkConfirmParams($postData){
                $checkRes   = false;
                $errorData  = [];
                $data = [
                    'checkRes'  => $checkRes,
                    'errorData' => $errorData,
                ];
                $dealResult = $postData->dealResult;
                $assignResult = $postData->assignResult;
                $comment = isset($postData->comment) ? $postData->comment :'';
                if(!$dealResult){
                    $errorData['dealResult'] = sprintf($this->lang->error->notempty, $this->lang->environmentorder->isConfirm);
                }
                // 判断是否受理
                if($dealResult==1){
                            if(!$assignResult){
                            $errorData['assignResult'] = sprintf($this->lang->error->notempty, $this->lang->environmentorder->isAssign);
                        }
                        if($assignResult==1){
                            if(!$postData->executor){
                            $errorData['executor'] = sprintf($this->lang->error->notempty, $this->lang->environmentorder->executorEmpty);
                        }
                    }
                }else{
                    if($comment == ''){
                            $errorData['comment'] =sprintf($this->lang->error->notempty,  $this->lang->environmentorder->reviewCommentEmpty);
                        }
                }

                if(!empty($errorData)){

                    $checkRes = false;
                }else{
                    $checkRes = true;
                }
                $data['checkRes'] = $checkRes;
                $data['errorData'] = $errorData;
                return $data;
            }
            public function checkImplementParams($postData){
                $checkRes   = false;
                $errorData  = [];
                $data = [
                    'checkRes'  => $checkRes,
                    'errorData' => $errorData,
                ];
                $dealResult = $postData->dealResult;
                $comment = isset($postData->comment) ? $postData->comment :'';
                $workHour= isset($postData->workHour) ? $postData->workHour :'';
                if(!$dealResult){
                    $errorData['dealResult'] = sprintf($this->lang->error->notempty, $this->lang->environmentorder->dealResult);
                }
                if($dealResult == '2'){ //审批不通过
                    if($comment == ''){
                        $errorData['comment'] = $this->lang->environmentorder->reviewCommentEmpty;
                    }
                }
                if($dealResult == '1'){ //审批通过
                    if($workHour == ''){
                        $errorData['workHour'] = $this->lang->environmentorder->workHourEmpty;
                    }
                }
                if(!empty($errorData)){
                    $checkRes = false;
                }else{
                    $checkRes = true;
                }
                $data['checkRes'] = $checkRes;
                $data['errorData'] = $errorData;
                return $data;
            }
            public function checkVerifyParams($postData){
                $checkRes   = false;
                $errorData  = [];
                $data = [
                    'checkRes'  => $checkRes,
                    'errorData' => $errorData,
                ];
                $dealResult = $postData->dealResult;
                $comment = isset($postData->comment) ? $postData->comment :'';
                if(!$dealResult){
                    $errorData['dealResult'] = sprintf($this->lang->error->notempty, $this->lang->environmentorder->dealResult);
                }
                if($dealResult == '2'){ //审批不通过
                    if($comment == ''){
                        $errorData['comment'] = $this->lang->environmentorder->reviewCommentEmpty;
                    }
                }

                if(!empty($errorData)){
                    $checkRes = false;
                }else{
                    $checkRes = true;
                }
                $data['checkRes'] = $checkRes;
                $data['errorData'] = $errorData;
                return $data;
            }
            //发送邮件通知
            public function sendmail($environmentorderID, $actionID)
            {
                /* 加载mail模块用于发信通知，获取需求意向和人员信息。*/
                $this->loadModel('mail');
                $environmentorder = $this->getById($environmentorderID);
                $action          = $this->loadModel('action')->getById($actionID);
                $history = $this->action->getHistory($actionID);
                $action->history = isset($history[$actionID]) ? $history[$actionID] : array();
                $users = $this->loadModel('user')->getPairs('noletter');
        
                /* 获取后台通知中配置的邮件发信。*/
                $this->app->loadLang('custommail');
                $mailConf   = isset($this->config->global->setEnvironmentorderMail) ? $this->config->global->setEnvironmentorderMail : '{"mailTitle":"","variables":[],"mailContent":""}';
                $mailConf   = json_decode($mailConf);
                $browseType = 'environmentorder';
                $mailTitle = vsprintf($mailConf->mailTitle, $mailConf->variables);
        
                $oldcwd     = getcwd();
                $modulePath = $this->app->getModulePath($appName = '', 'environmentorder');
                $viewFile   = $modulePath . 'view/sendmail.html.php';
                chdir($modulePath . 'view');
        
                if(file_exists($modulePath . 'ext/view/sendmail.html.php'))
                {
                    $viewFile = $modulePath . 'ext/view/sendmail.html.php';
                    chdir($modulePath . 'ext/view');
                }
        
                ob_start();
                include $viewFile;
                foreach(glob($modulePath . 'ext/view/sendmail.*.html.hook.php') as $hookFile) include $hookFile;
                $mailContent = ob_get_contents();
                ob_end_clean();
                chdir($oldcwd);
        
                /* 获取发信人和抄送人数据。*/
                $sendUsers = $this->getToAndCcList($environmentorder,$action);
                if (!$sendUsers) return;
                list($toList, $ccList) = $sendUsers;
                $subject = $mailTitle;
        
                /* Send mail. */
                /* 调用mail模块的send方法进行发信。*/
                $this->loadModel('mail')->send($toList, $subject, $mailContent, $ccList);
                if ($this->loadModel('mail')->isError()) trigger_error(join("\n", $this->loadModel('mail')->getError()));
            }

            public function getToAndCcList($info,$action)
            {
                $consumed = $this->loadModel('consumed')->getObjectByID($info->id, 'environmentorder', $info->status);
                /* Set toList and ccList. */
                /* 初始化收信人和抄送人变量，获取发信人和抄送人数据。*/
                $ccList = '';
                $toList = '';
                $status = $info->status;
                if($status=='archived'){
                    return false;
                }elseif(($consumed->before==$consumed->after)&&$action->action!='assigned'){
                    return false;
                }elseif($action->action=='assigned'){
                    $toList =$action->extra;
                    $ccList= implode(',',array_keys($this->lang->environmentorder->reviewerList));
                }else{
                    if(in_array($status,$this->lang->environmentorder->allowSendMailStatusArray)){
                        $toList = $info->dealUser;
                    }
                }
                return array($toList, $ccList);
            }

}
