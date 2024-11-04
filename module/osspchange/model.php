<?php
/**
 * The model file of osspchange module of ZenTaoPMS.
 *
 */
class osspchangeModel extends model
{
    // 列表搜索
    public function buildSearchForm($queryID, $actionURL)
    {
        $this->config->osspchange->search['actionURL']     = $actionURL;
        $this->config->osspchange->search['queryID']       = $queryID;
        $this->config->osspchange->search['params']['systemProcess']['values']    = array('' => '') +$this->lang->osspchange->systemProcessList;
        $this->config->osspchange->search['params']['systemVersion']['values']    = array('' => '') +$this->lang->osspchange->systemVersionList;

        $this->loadModel('search')->setSearchParams($this->config->osspchange->search);
    }

    // 列表数据查询
    public function getList($browseType, $queryID, $orderBy, $pager){
        $osspchangeQuery = '';
        if($browseType == 'bysearch')
        {
            $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : '';
            if($query)
            {
                $this->session->set('osspchangeQuery', $query->sql);
                $this->session->set('osspchangeForm', $query->form);
            }

            if($this->session->osspchangeQuery == false) $this->session->set('osspchangeQuery', ' 1 = 1');
            $osspchangeQuery = $this->session->osspchangeQuery;

        }
        $osspchangeQuery = str_replace("`ifAccept` = ''","`ifAccept` = '0'", $osspchangeQuery);

        // 待提交映射待提交和退回到发起人提交
        if (strpos($osspchangeQuery, 'waitApply')) {
            $osspchangeQuery = str_replace("= 'waitApply'", "in ('waitApply','rejectToStart')", $osspchangeQuery);
        }

        // 已提交映射已提交和退回到接口人确认
        if (strpos($osspchangeQuery, 'waitConfirm')) {
            $osspchangeQuery = str_replace("= 'waitConfirm'", "in ('waitConfirm','rejectToConfirm')", $osspchangeQuery);
        }

        $osspchanges = $this->dao->select('*')->from(TABLE_OSSPCHANGE)
            ->where('deleted')->ne('1')
            ->beginIF($browseType == 'waitapply')->andWhere('status')->in('waitApply,rejectToStart')->fi()
            ->beginIF($browseType == 'waitconfirm')->andWhere('status')->in('waitConfirm,rejectToConfirm')->fi()
            ->beginIF($browseType != 'all' and $browseType != 'bysearch' and $browseType != 'waitapply' and $browseType != 'waitconfirm')->andWhere('status')->eq($browseType)->fi()
            ->beginIF($browseType == 'bysearch')->andWhere($osspchangeQuery)->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll();

        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'osspchange', $browseType != 'bysearch');
        return $osspchanges;
    }

    // 赋值字段数据
    public function printCell($col, $osspchange, $users,$status,$orderBy,$pager)
    {
        $id = $col->id;

        $params = "&statusNew=$status"."&orderBy=$orderBy"."&recTotal=$pager->recTotal"."&recPerPage=$pager->recPerPage"."&pageID=$pager->pageID";
        if($col->show)
        {
            $class = "c-$id";
            $title  = '';$result = '';
            if($id == 'title')
            {
                $class .= ' text-left';
                $title  = "title='{$osspchange->title}'";
            }
            if($id == 'createdDate')
            {
                $title  = "title='{$osspchange->createdDate}'";
            }
            if($id == 'dealuser')
            {
                $title  = "title='".zmget($users, $osspchange->dealuser)."'";
            }
            if($id == 'systemProcess')
            {
                $title  = "title='".zmget($this->lang->osspchange->systemProcessList, $osspchange->systemProcess)."'";
            }
            if($id == 'systemVersion')
            {
                $title  = "title='".zmget($this->lang->osspchange->systemVersionList, $osspchange->systemVersion)."'";
            }
            if($id == 'status')
            {
                $title  = "title='".zmget($this->lang->osspchange->statusNameList, $osspchange->status)."'";
            }
            if($id == 'closeResult')
            {
                $result = $this->getOsspResult($osspchange);
                $title  = "title='".$result."'";
            }
            echo "<td class='" . $class . "' $title>";
            switch($id)
            {
                case 'id':
                    echo $osspchange->id;
                    break;
                case 'code':
                    echo html::a(helper::createLink('osspchange', 'view', "osspchangeID=$osspchange->id"),'<div title="' . $osspchange->code . '">' . $osspchange->code .'</div>');
                    break;
                case 'proposer':
                    echo zget($users, $osspchange->proposer);
                    break;
                case 'createdDate':
                    echo $osspchange->createdDate;
                    break;
                case 'title':
                    echo html::a(helper::createLink('osspchange', 'view', "osspchangeID=$osspchange->id"),'<div title="' . $osspchange->title . '">' . $osspchange->title .'</div>');
                    break;
                case 'systemProcess':
                    echo zget($this->lang->osspchange->systemProcessList, $osspchange->systemProcess);
                    break;
                case 'systemVersion':
                    echo zget($this->lang->osspchange->systemVersionList, $osspchange->systemVersion);
                    break;
                case 'closeResult':
                    echo $result;
                    break;
                case 'status':
                    echo zget($this->lang->osspchange->statusNameList, $osspchange->status);
                    break;
                case 'dealuser':
                    echo zmget($users, $osspchange->dealuser);
                    break;
                case 'actions':
                    $recTotal = $pager->recTotal;
                    $recPerPage = $pager->recPerPage;
                    $pageID = $pager->pageID;
                    $param = "osspchangeID=$osspchange->id&status=$status&orderBy=$orderBy&recTotal=$recTotal&recPerPage=$recPerPage&pageID=$pageID";
                    common::hasPriv('osspchange','edit') ? common::printIcon('osspchange', 'edit', $param, $osspchange, 'list') : '';
//                    common::hasPriv('osspchange','submit') ? common::printIcon('osspchange', 'submit', "osspchangeID=$osspchange->id", $osspchange,'list','play', 'hiddenwin', '') : '';
                    common::hasPriv('osspchange','confirm') ? common::printIcon('osspchange', 'confirm', $param, $osspchange, 'list','play','', 'iframe',true, "data-width=80%") :'';
                    common::hasPriv('osspchange','review') ? common::printIcon('osspchange', 'review', $param, $osspchange, 'list','glasses','', 'iframe',true, "data-width=50%") :'';
                    common::hasPriv('osspchange','close') ? common::printIcon('osspchange', 'close', $param, $osspchange, 'list','off','', 'iframe',true, "data-width=80%") : '';
                    common::hasPriv('osspchange','delete') ? common::printIcon('osspchange', 'delete', "osspchangeID=$osspchange->id", $osspchange, 'list', 'trash', 'hiddenwin') : '';
            }
            echo '</td>';
        }

    }

    /**
     * 新增ossp变更
     *
     * @access public
     * @return int|bool
     */
    public function create()
    {
        $version = 0;
        $data = fixer::input('post')
            ->add('version', $version)
            ->add('createdBy', $this->app->user->account)
            ->add('createdDate', helper::now())
            ->add('dealuser', $this->app->user->account)
            ->remove('uid,files')
            ->stripTags($this->config->osspchange->editor->create['id'], $this->config->allowedTags)
            ->get();

        // 保存时 状态流转到待提交
        if( $data->type == 'save' ){
            $status  = $this->lang->osspchange->statusList['waitApply'];
        }else{
            // 提交时 状态流转到待确认
            $status  = $this->lang->osspchange->statusList['waitConfirm'];
            if(is_array($this->post->files) && count($this->post->files)){
                return dao::$errors = array('files' => $this->lang->osspchange->filesEmpty);
            }
        }

        // 查询表中最新序号
        $lastData = $this->dao->select('status,code')->from(TABLE_OSSPCHANGE)->orderBy('id_desc')->limit(1)->fetch();

        // 如果有并且年份和当前年份相等 则在上一条序号基础上+1(否则从新的一年001开始)
        if(!empty($lastData->code) && substr($lastData->code, 5, 4) == helper::currentYear()){
            $number = helper::currentYear().substr($lastData->code,-3);
            $number++;
        }else{
            $number = helper::currentYear().'001';
        }

        // 序号规则 OSSP-年份-001起
        $data->code   = 'OSSP-'.$number;
        $data->status = $status;
        $type         = $data->type;
        unset($data->type);
        $data = $this->loadModel('file')->processImgURL($data, $this->config->osspchange->editor->create['id'], $this->post->uid);

        // 仅保存
        if($type == 'save' && $data->title){
            // 插入数据(不校验必填)
            $this->dao->insert(TABLE_OSSPCHANGE)->data($data)->exec();

            // 获取新建id
            $lastId = $this->dao->lastInsertID();

            if(!dao::isError())
            {
                $this->loadModel('file')->updateObjectID($this->post->uid, $lastId, 'osspchange');
                $this->file->saveUpload('osspchange', $lastId);
                return $lastId;
            }
        }elseif($type == 'submit'){
            $data->dealuser = $this->config->osspchange->interfacePerson;
            // 插入数据
            $this->dao->insert(TABLE_OSSPCHANGE)->data($data)
                ->autoCheck()
                ->batchCheck($this->config->osspchange->create->requiredFields, 'notempty')
                ->exec();

            // 获取新建id
            $lastId = $this->dao->lastInsertID();

            // 生成待接口人确认节点
            $reviewStatus = 'pending';
            $stage        = '0';
            $extParams    = [
                'nodeCode' => $status,
            ];
            $this->loadModel('review')->addNode('osspchange', $lastId, $version, explode(',',$this->config->osspchange->interfacePerson), true, $reviewStatus, $stage, $extParams);

            // 保存流程状态
            $this->loadModel('consumed')->record('osspchange', $lastId, '0', $this->app->user->account, '', $status, array());

            if(!dao::isError())
            {
                $this->loadModel('file')->updateObjectID($this->post->uid, $lastId, 'osspchange');
                $this->file->saveUpload('osspchange', $lastId);
                return $lastId;
            }
        }else{
            return false;
        }
        return false;
    }

    /**
     * 通过评审ID获得评审信息的所有字段信息
     *
     * @param $reviewId
     * @return bool
     */
    public function getByID($osspchange){
        $data = false;
        if(!$osspchange){
            return $data;
        }
        $ret = $this->dao->select('*')->from(TABLE_OSSPCHANGE)
            ->where('id')->eq($osspchange)
            ->andWhere('deleted')->eq('0')
            ->fetch();
        if($ret){
            $ret = $this->loadModel('file')->replaceImgURL($ret, 'background,content,advise,fileInfo,closeComment');
            $objectType = $this->lang->osspchange->objectType;
            //文件
            $ret->files = $this->loadModel('file')->getByObject($objectType, $osspchange);
            //状态流转
            $ret->consumed =  $this->loadModel('consumed')->getConsumed($objectType, $osspchange);
            $data = $ret;
        }
        return $data;
    }

    /**
     * Judge button if can clickable.
     *
     * @param  object $review
     * @param  string $action
     * @access public
     * @return void
     */
    public static function isClickable($osspchange, $action)
    {
        global $app;
        $action = strtolower($action);

        $osspchangeModel = new osspchangeModel();
        $interfacePerson = $osspchangeModel->config->osspchange->interfacePerson;

        $dealUsers  = explode(',',$osspchange->dealuser);
        if($action == 'edit')      return    ($osspchange->createdBy == $app->user->account && in_array($osspchange->status,['waitApply'])) || (in_array($osspchange->status,['rejectToStart']) && in_array($app->user->account,$dealUsers));
//        if($action == 'submit')    return    in_array($osspchange->status,['waitApply']) && in_array($app->user->account,$dealUsers);
        if($action == 'confirm')   return    in_array($osspchange->status,['waitConfirm','rejectToConfirm']) && in_array($app->user->account,$dealUsers);
        if($action == 'review')    return    in_array($osspchange->status,['waitDeptApprove','waitQMDApprove','waitMaxLeaderApprove']) && in_array($app->user->account,$dealUsers);
        if($action == 'close')     return    in_array($app->user->account,$dealUsers) && in_array($osspchange->status,['waitClosed']);
        if($action == 'delete')    return    ($osspchange->createdBy == $app->user->account && in_array($osspchange->status,['waitApply'])) || (in_array($app->user->account,explode(',',$interfacePerson))) || ($app->user->account == 'admin');
        return true;
    }

    // 接口人确认
    public function confirm($oldData){
        // 校验数据必填项
        $data = $this->checkAllowConfirm();
        $type = $data->type;
        unset($data->type);
        if($type == 'save'){
            // 直接保存
            $data = $this->loadModel('file')->processImgURL($data, $this->config->osspchange->editor->confirm['id'], $this->post->uid);
            $data->status = $this->lang->osspchange->statusList['rejectToConfirm'];
            $this->dao->update(TABLE_OSSPCHANGE)->data($data)->where('id')->eq($oldData->id)->exec();

            return common::createChanges($oldData, $data);
        }
        $objectType = 'osspchange';

        // result为后台自定义配置 1-通过,2-需修改,3-暂不采纳
        if($data->result == 1){
            if( $data->systemManager == $data->QMDmanager || $data->systemDept == 3){
                $data->status   = $this->lang->osspchange->statusList['waitQMDApprove'];
                $data->dealuser = $data->QMDmanager;
                // 插入跳过归口部门负责人审批节点
                $reviewStatus = 'ignore';
                $stage = $this->loadModel('review')->getNodeStage($objectType, $oldData->id, $oldData->version, $this->lang->osspchange->statusList['waitDeptApprove']);
                $stage++;
                $extParams    = [
                    'nodeCode' => $this->lang->osspchange->statusList['waitDeptApprove'],
                ];
                $this->loadModel('review')->addNode($objectType, $oldData->id, $oldData->version, explode(',',$data->systemManager), true, $reviewStatus, $stage, $extParams);
            }else{
                $data->status   = $this->lang->osspchange->statusList['waitDeptApprove'];
                $data->dealuser = $data->systemManager;
            }
            $result = 'pass';
        }elseif($data->result == 2){
            // 需修改时状态回到发起人处重新编辑
            $data->status   = $this->lang->osspchange->statusList['rejectToStart'];
            $data->dealuser = $oldData->proposer;
            $result = 'reject';
        }else{
            // 暂不采纳时状态流转到待接口人关闭
            $data->status   = $this->lang->osspchange->statusList['waitClosed'];
            $data->dealuser = $this->config->osspchange->interfacePerson;
            $result = 'pass';
        }

        // 处理当前节点以及待处理人状态
        $extra = [
            'reviewedDate' => helper::now(),
            'reviewInfo' => $data->result,
        ];
        $res = $this->loadModel('review')->check($objectType, $oldData->id, $oldData->version, $result, '','',$extra, false);

        if($res == 'pass'){
            // 增加下一节点数据
            $reviewStatus = 'pending';
            $stage = $this->loadModel('review')->getNodeStage($objectType, $oldData->id, $oldData->version, $oldData->status);
            $stage++;
            $extParams    = [
                'nodeCode' => $data->status,
            ];
            $this->loadModel('review')->addNode($objectType, $oldData->id, $oldData->version, explode(',',$data->dealuser), true, $reviewStatus, $stage, $extParams);
        }
        // 插入接口人确认数据
        $data = $this->loadModel('file')->processImgURL($data, $this->config->osspchange->editor->confirm['id'], $this->post->uid);
        $this->dao->update(TABLE_OSSPCHANGE)->data($data)->where('id')->eq($oldData->id)->exec();

        // 保存流程状态
        $this->loadModel('consumed')->record($objectType, $oldData->id, '0', $this->app->user->account, $oldData->status, $data->status);

        if(!dao::isError())
        {
            return $oldData->id;
        }
        return false;
    }

    // 编辑
    public function update($oldData){

        $data = fixer::input('post')
            ->add('editedBy', $this->app->user->account)
            ->add('editedDate', helper::now())
            ->remove('uid,files')
            ->stripTags($this->config->osspchange->editor->create['id'], $this->config->allowedTags)
            ->get();

        $type = $data->type;
        unset($data->type);
        $data = $this->loadModel('file')->processImgURL($data, $this->config->osspchange->editor->create['id'], $this->post->uid);

        // 编辑过后状态修改
        $data->status = $type != 'save' ? $this->lang->osspchange->statusList['waitConfirm'] : $this->lang->osspchange->statusList['waitApply'];

        // 仅保存
        if($type == 'save' && $data->title){
            // 更新数据(不校验必填)
            $this->dao->update(TABLE_OSSPCHANGE)->data($data)->where('id')->eq($oldData->id)->exec();
        }elseif($type == 'submit'){
            if($oldData->status == 'rejectToStart') $data->version = $oldData->version +1;
            $data->dealuser = $this->config->osspchange->interfacePerson;
            if(is_array($this->post->files) && count($this->post->files) && empty($oldData->files)){
                return dao::$errors = array('files' => $this->lang->osspchange->filesEmpty);
            }

            // 插入数据
            $this->dao->update(TABLE_OSSPCHANGE)->data($data)->where('id')->eq($oldData->id)
                ->autoCheck()
                ->batchCheck($this->config->osspchange->create->requiredFields, 'notempty')
                ->exec();

            // 生成待接口人确认节点
            $reviewStatus = 'pending';
            $stage        = '0';
            $extParams    = [
                'nodeCode' => $data->status,
            ];
            $this->loadModel('review')->addNode('osspchange', $oldData->id, $data->version, explode(',',$data->dealuser), true, $reviewStatus, $stage, $extParams);

            // 保存流程状态
            $this->loadModel('consumed')->record('osspchange', $oldData->id, '0', $this->app->user->account, $oldData->status, $data->status, array());
        }else{
            return false;
        }

        if(!dao::isError())
        {
            $this->loadModel('file')->updateObjectID($this->post->uid, $oldData->id, 'osspchange');
            $this->file->saveUpload('osspchange', $oldData->id);
            if($type == 'submit'){
                $this->loadModel('consumed')->record('osspchange',$oldData->id, 0, $this->app->user->account, $oldData->status, $data->status, array(), '');
            }
            return common::createChanges($oldData, $data);
        }
        return false;
    }

    // 校验是否允许接口人确认
    public function checkAllowConfirm(){
         // 接收数据
         $data = fixer::input('post')
             ->add('confirmBy', $this->app->user->account)
             ->add('confirmDate', helper::now())
             ->remove('uid,proposer,title,background,content')
             ->stripTags($this->config->osspchange->editor->confirm['id'], $this->config->allowedTags)
             ->get();

         // 仅保存时不校验必填
         if($data->type == 'save') return $data;

         if(empty($data->systemProcess)){
             dao::$errors[] = $this->lang->osspchange->systemProcessError;
             return false;
         }
        if(empty($data->systemVersion)){
            dao::$errors[] = $this->lang->osspchange->systemVersionError;
            return false;
        }
        if(empty($data->advise)){
            dao::$errors[] = $this->lang->osspchange->adviseError;
            return false;
        }
        if(empty($data->result)){
            dao::$errors[] = $this->lang->osspchange->resultError;
            return false;
        }
        if(empty($data->changeNotice)){
            dao::$errors[] = $this->lang->osspchange->changeNoticeError;
            return false;
        }
        if(empty($data->systemDept)){
            dao::$errors[] = $this->lang->osspchange->systemDeptError;
            return false;
        }
        if(empty($data->systemManager)){
            dao::$errors[] = $this->lang->osspchange->systemManagerError;
            return false;
        }
        if(empty($data->QMDmanager)){
            dao::$errors[] = $this->lang->osspchange->QMDmanagerError;
            return false;
        }
        return $data;
    }

    // 评审
    public function review($oldData){
        $objectType = 'osspchange';
        // 检查是否允许评审
        $res = $this->checkAllowReview($oldData, $this->post->version,  $this->post->status, $this->app->user->account);
        if(!$res['result']){
            dao::$errors['statusError'] = $res['message'];
            return false;
        }
        if(empty($_POST['comment']))
        {
            dao::$errors[] = $this->lang->osspchange->commentError;
            return false;
        }
        if(empty($_POST['result']))
        {
            dao::$errors[] = $this->lang->osspchange->reviewResultError;
            return false;
        }

        // 处理审核操作
        $returnRes  = $this->getResultAndNextStatusAndDealuser($oldData, $_POST['result']);
        $result     = $returnRes['result'];
        $nextStatus = $returnRes['nextStatus'];
        $dealuser   = $returnRes['dealuser'];
        $extra = [
            'reviewedDate' => helper::now(),
            'reviewInfo' => $_POST['result'],
        ];

        // 处理当前节点状态
        $res = $this->loadModel('review')->check($objectType, $oldData->id, $oldData->version, $result, $_POST['comment'], '',$extra);
        if($res == 'pass'){
            // 增加下一节点数据
            $reviewStatus = 'pending';
            $stage = $this->loadModel('review')->getNodeStage($objectType, $oldData->id, $oldData->version, $oldData->status);
            $stage++;
            $extParams    = [
                'nodeCode' => $nextStatus,
            ];
            $this->loadModel('review')->addNode($objectType, $oldData->id, $oldData->version, explode(',',$dealuser), true, $reviewStatus, $stage, $extParams);

            // 修改主表数据
            $updateData = new stdClass();
            $updateData->status = $nextStatus;
            $updateData->dealuser = $dealuser;
            $updateData->lastReviewedBy = $this->app->user->account;
            $updateData->lastReviewedDate = helper::now();
            $this->dao->update(TABLE_OSSPCHANGE)->data($updateData)->where('id')->eq($oldData->id)->exec();
        }else{
            // 需升版本回退
            $updateData = new stdClass();
            $updateData->status = $nextStatus;
            $updateData->dealuser = $dealuser;
            $updateData->lastReviewedBy = $this->app->user->account;
            $updateData->lastReviewedDate = helper::now();
            $updateData->version = $oldData->version+1;

            $this->dao->update(TABLE_OSSPCHANGE)->data($updateData)->where('id')->eq($oldData->id)->exec();
            $newInfo = $this->getByID($oldData->id);
            if($nextStatus == $this->lang->osspchange->statusList['rejectToConfirm']){
                $this->addNewVersionReviewNodes($newInfo, $oldData->status);
            }
        }
        // 保存流程状态
        $this->loadModel('consumed')->record($objectType, $oldData->id, '0', $this->app->user->account, $oldData->status, $nextStatus);

        $logChange = common::createChanges($oldData, $updateData);
        return $logChange;
    }

    // 校验是否允许评审
    public function checkAllowReview($data, $version = 0,  $status = '', $userAccount = ''){
        $res = array(
            'result'  => false,
            'message' => '',
        );
        if(!$data){
            $res['message'] = $this->lang->common->errorParamId;
            return $res;
        }
        if(!$userAccount){
            $res['message'] = $this->lang->common->errorParamUser;
            return $res;
        }
        //审核节点已经经过
        if(($version != $data->version) || ($status != $data->status)){
            $res['message'] = $this->lang->osspchange->nowStatusError;
            return $res;
        }
        // 当前用户不允许审批
        if(!in_array($userAccount, explode(',',$data->dealuser))){
            $res['message'] = $this->lang->osspchange->dealuserError;
            return $res;
        }
        // 当前状态不允许审批
        if(!in_array($data->status, $this->lang->osspchange->allowReviewList)){
            $res['message'] = $this->lang->osspchange->stateReviewError;
            return $res;
        }

        $res['result'] = true;
        return  $res;
    }

    // 获取下一个状态信息
    public function getResultAndNextStatusAndDealuser($data, $result){
        $returnData = [];$nextStatus = '';$res = '';$dealuser = '';
        if(!($data && $result)){
            return $returnData;
        }
        //当前记录状态
        $status = $data->status;
        if($status == $this->lang->osspchange->statusList['waitDeptApprove']){ // 归口部门负责人审批
            if($result == 1){
                // 1 通过 流转到质量部负责人审核
                $nextStatus = $this->lang->osspchange->statusList['waitQMDApprove'];
                $res        = 'pass';
                $dealuser   = $data->QMDmanager;
            }elseif ($result == 2){
                // 2 不通过 回退至发起人重新编辑
                $nextStatus = $this->lang->osspchange->statusList['rejectToStart'];
                $res        = 'reject';
                $dealuser   = $data->proposer;
            }elseif ($result == 3){
                // 3 不通过 回退到接口人重新编辑
                $nextStatus = $this->lang->osspchange->statusList['rejectToConfirm'];
                $res        = 'reject';
                $dealuser   = $this->config->osspchange->interfacePerson;
            }else{
                // 4不通过 流转至待关闭
                $nextStatus = $this->lang->osspchange->statusList['waitClosed'];
                $res        = 'pass';
                $dealuser   = $this->config->osspchange->interfacePerson;
            }
        }elseif($status == $this->lang->osspchange->statusList['waitQMDApprove']){ // 质量部负责人审批
            if($result == 1){
                // 1 通过 直接流转到体系接口人关闭
                $nextStatus = $this->lang->osspchange->statusList['waitClosed'];
                $res        = 'pass';
                $dealuser   = $this->config->osspchange->interfacePerson;
            }elseif ($result == 2){
                // 2 通过 流转到总经理审批
                $nextStatus = $this->lang->osspchange->statusList['waitMaxLeaderApprove'];
                $res        = 'pass';
                $dealuser   = $this->lang->osspchange->maxLeader;
            }elseif ($result == 3){
                // 3 不通过 回退到发起人重新编辑
                $nextStatus = $this->lang->osspchange->statusList['rejectToStart'];
                $res        = 'reject';
                $dealuser   = $data->proposer;
            }elseif ($result == 4){
                // 4 不通过 回退到接口人重新编辑
                $nextStatus = $this->lang->osspchange->statusList['rejectToConfirm'];
                $res        = 'reject';
                $dealuser   = $this->config->osspchange->interfacePerson;
            }else{
                // 4不通过 流转至待关闭
                $nextStatus = $this->lang->osspchange->statusList['waitClosed'];
                $res        = 'pass';
                $dealuser   = $this->config->osspchange->interfacePerson;
            }
        }elseif ($status == $this->lang->osspchange->statusList['waitMaxLeaderApprove']){ // 总经理审批
            if($result == 1){
                // 1 通过 直接流转到体系接口人关闭
                $nextStatus = $this->lang->osspchange->statusList['waitClosed'];
                $res        = 'pass';
                $dealuser   = $this->config->osspchange->interfacePerson;
            }elseif ($result == 2){
                // 2 不通过 回退到发起人重新编辑
                $nextStatus = $this->lang->osspchange->statusList['rejectToStart'];
                $res        = 'reject';
                $dealuser   = $data->proposer;
            }elseif ($result == 3){
                // 3 通过 (接口人修改后直接发布)
                $nextStatus = $this->lang->osspchange->statusList['waitClosed'];
                $res        = 'pass';
                $dealuser   = $this->config->osspchange->interfacePerson;
            }elseif ($result == 4){
                // 4 不通过 回退到接口人重新编辑
                $nextStatus = $this->lang->osspchange->statusList['waitClosed'];
                $res        = 'pass';
                $dealuser   = $this->config->osspchange->interfacePerson;
            }else{
                // 5通过 请组织经理专题会/经理办公会
                $nextStatus = $this->lang->osspchange->statusList['waitClosed'];
                $res        = 'pass';
                $dealuser   = $this->config->osspchange->interfacePerson;
            }
        }
        $returnData['nextStatus'] = $nextStatus;
        $returnData['result']     = $res;
        $returnData['dealuser']   = $dealuser;
        return $returnData;
    }

    // 关闭
    public function close($oldData){
        $objectType = 'osspchange';
        // 检查是否允许评审
        $data = $this->checkAllowClose($oldData, $this->app->user->account);

        // 处理当前节点状态
        $extra = [
            'reviewedDate' => helper::now(),
            'reviewInfo' => $data->closeResult,
        ];
        $this->loadModel('review')->check($objectType, $oldData->id, $oldData->version, 'pass', $data->closeComment,'', $extra, false);

        // 修改主表数据
        $data->status = $this->lang->osspchange->statusList['closed'];
        $data->dealuser = '';
        $data = $this->loadModel('file')->processImgURL($data, $this->config->osspchange->editor->close['id'], $this->post->uid);
        $this->dao->update(TABLE_OSSPCHANGE)->data($data)->where('id')->eq($oldData->id)->exec();

        // 保存流程状态
        $this->loadModel('consumed')->record($objectType, $oldData->id, '0', $this->app->user->account, $oldData->status, $data->status);

        return $oldData->id;
    }

    public function checkAllowClose($oldData){
        // 接收数据
        $data = fixer::input('post')
            ->add('closedBy', $this->app->user->account)
            ->add('closedDate', helper::now())
            ->remove('uid')
            ->stripTags($this->config->osspchange->editor->close['id'], $this->config->allowedTags)
            ->get();

        if($oldData->status != $this->lang->osspchange->statusList['waitClosed']){
            dao::$errors[] = $this->lang->osspchange->stateCloseError;
            return false;
        }
        if(empty($data->fileInfo)){
            dao::$errors[] = $this->lang->osspchange->fileInfoError;
            return false;
        }
        if(empty($data->closeResult)){
            dao::$errors[] = $this->lang->osspchange->closeResultError;
            return false;
        }
        if(empty($data->closeComment)){
            dao::$errors[] = $this->lang->osspchange->closeCommentError;
            return false;
        }
        if(empty($data->notifyPerson)){
            dao::$errors[] = $this->lang->osspchange->notifyPersonError;
            return false;
        }
        return $data;
    }

    // 升级评审版本
    public function addNewVersionReviewNodes($newInfo, $status){
        $res = false;
        if(!$newInfo){
            return $res;
        }
        $objectType     = $this->lang->osspchange->objectType;
        $osspchangeID   = $newInfo->id;
        $version        = $newInfo->version;

        //将上一版本的pending和wait的状态置为ignore
        $maxVersion = $this->loadModel('review')->getObjectReviewNodeMaxVersion($osspchangeID, $objectType);
        $needDealIgnoreIds = $this->loadModel('review')->getUnDealReviewNodes($objectType, $osspchangeID, $maxVersion);
        if(!empty($needDealIgnoreIds)){
            $this->loadModel('review')->ignoreReviewNodeAndReviewers($needDealIgnoreIds);
        }

        $stage = 1; // 退回到待接口人确认节点 新版本节点都从待确认开始
        //获得历史节点
        $historyReviews = $this->loadModel('review')->getHistoryReviewers($objectType, $osspchangeID, $maxVersion, $stage);
        if(!empty($historyReviews)){
            foreach ($historyReviews as $currentNodeInfo){
                $currentNodeReviewers = $currentNodeInfo->reviewers;
                unset($currentNodeInfo->reviewers);
                unset($currentNodeInfo->id);
                $currentNodeInfo->version = $version;
                $currentNodeInfo->status  = 'pending';
                //新增审核节点
                $this->dao->insert(TABLE_REVIEWNODE)->data($currentNodeInfo)->exec();
                $newNodeID = $this->dao->lastInsertID();
                foreach ($currentNodeReviewers as $currentNodeReviewer){
                    $currentNodeReviewer->node   = $newNodeID;
                    $currentNodeReviewer->status = 'pending';
                    unset($currentNodeReviewer->id);
                    $this->dao->insert(TABLE_REVIEWER)->data($currentNodeReviewer)->exec();
                }
            }
        }
        return true;
    }


    /**
     * Send mail
     *
     * @param  int    $osspchangeID
     * @param  int    $actionID
     * @access public
     * @return void
     */
    public function sendmail($id, $actionID)
    {
        $this->loadModel('mail');
        //邮件显示详细信息
        $data = $this->getById($id);

        // 仅为保存时不发邮件
        if($data->status == $this->lang->osspchange->statusList['waitApply']){
            return false;
        }
        $users  = $this->loadModel('user')->getPairs('noletter|noclosed');

        /* 获取后台通知中配置的邮件发信。*/
        $this->app->loadLang('custommail');
        $mailConf   = isset($this->config->global->setOsspchangeMail) ? $this->config->global->setOsspchangeMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf   = json_decode($mailConf);

        /* 处理邮件发信的标题和日期。*/
        $mailTitle = vsprintf($mailConf->mailTitle, $mailConf->variables);

        /* Get action info. */
        $action          = $this->loadModel('action')->getById($actionID);
        $history         = $this->action->getHistory($actionID);
        $action->history = isset($history[$actionID]) ? $history[$actionID] : array();

        /* Get mail content. */
        $oldcwd     = getcwd();
        $modulePath = $this->app->getModulePath($appName = '', 'osspchange');
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

        /* 处理邮件标题。*/
        $subject = $mailTitle;

        if($data->status == $this->lang->osspchange->statusList['closed']){
            $toList = $data->notifyPerson;
        }else{
            $toList = $data->dealuser;
        }

        /* Send emails. */
        $this->mail->send($toList, $subject, $mailContent, '');
        if($this->mail->isError()) error_log(join("\n", $this->mail->getError()));
    }

    //喧喧发信
    public function getXuanxuanTargetUser($obj,$objectType, $objectID, $actionType, $actionID, $actor = '')
    {
        // 查询发送人
        $data = $this->getById($objectID);

        // 仅为保存时不发邮件
        if($data->status == $this->lang->osspchange->statusList['waitApply']){
            return false;
        }

        if($data->status == $this->lang->osspchange->statusList['closed']){
            $toList = $data->notifyPerson;
        }else{
            $toList = $data->dealuser;
        }
        /* Get action info. */
        $action          = $this->loadModel('action')->getById($actionID);
        $history         = $this->action->getHistory($actionID);
        $action->history = isset($history[$actionID]) ? $history[$actionID] : array();

        $mailConf   = isset($this->config->global->setOsspchangeMail) ? $this->config->global->setOsspchangeMail : '{"mailTitle":"","variables":[],"mailContent":""}';

        $server   = $this->loadModel('im')->getServer('zentao');
        $url = $server.'/osspchange-view-'.$objectID.'.html';
        $subcontent = [];
        $subcontent['headTitle']    = '';
        $subcontent['headSubTitle'] = '';
        $subcontent['count']       = 0;
        $subcontent['id']       = 0;
        $subcontent['parent']       = '';
        $subcontent['parentURL']    = "";
        $subcontent['cardURL']      = $url;
        $subcontent['name']      = '序号：'.$data->code;
        //标题
        $title = '';
        $actions = [];

        return ['toList'=>$toList,'subcontent'=>$subcontent,'url'=>$url,'title'=>$title,'actions'=>$actions,'mailconf'=>$mailConf];
    }

    // 获取审批结果
    public function getReviewResultList($objectType, $objectID, $version, $status, $select = '*', $extra = null){
        $data = [];
        if(!($objectID && $status)){
            return $data;
        }
        $node = $this->dao->select('id, nodeCode')->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq($objectType)
            ->andWhere('objectID')->eq($objectID)
            ->andWhere('version')->eq($version)
            ->andWhere('status')->eq($status)
            ->orderBy('stage_desc,id_desc')
            ->fetch();
        if(!$node) {
            return  $data;
        }
        $dataList = $this->dao->select($select)->from(TABLE_REVIEWER)
            ->where('node')->eq($node->id)
            ->beginIF($extra)->andWhere('extra')->like('%'.$extra.'%')->fi()
            ->fetchAll();

        if($dataList) {
            $data['node'] = $node->nodeCode;
            $data['list'] = $dataList;
        }
        return $data;

    }

    // 获取当前单子处理结果
    public function getOsspResult($data){
        $result = '';
        $resultList        = $this->lang->osspchange->resultList;         //确认页处理结果
        $systemManagerList = $this->lang->osspchange->systemManagerList;  //归口部门负责人审批结果
        $QMDmanagerList    = $this->lang->osspchange->QMDmanagerList;     //质量部负责人审批结果
        $maxLeaderList     = $this->lang->osspchange->maxLeaderList;      //总经理审批结果
        $closedList        = $this->lang->osspchange->interfaceClosedList;//接口人关闭处理结果

        if($data->status == $this->lang->osspchange->statusList['waitDeptApprove']){
            $result = zget($resultList, $data->result, '');
        }elseif($data->status == $this->lang->osspchange->statusList['waitQMDApprove']){
            $reviewInfo = $this->getReviewResultList('osspchange', $data->id, $data->version, $this->lang->osspchange->passStatus, $select = 'extra', $extra = 'reviewInfo');
            $upNode     = $reviewInfo['node'];
            $extra      = json_decode($reviewInfo['list'][0]->extra, true);
            $reviewInfo = $extra['reviewInfo'];
            if($upNode == $this->lang->osspchange->statusList['waitConfirm']){
                $results = $resultList;
            }elseif($upNode == $this->lang->osspchange->statusList['waitDeptApprove']){
                $results = $systemManagerList;
            }
            $result = zget($results, $reviewInfo, '');
        }elseif($data->status == $this->lang->osspchange->statusList['waitMaxLeaderApprove']){
            $reviewInfo = $this->getReviewResultList('osspchange', $data->id, $data->version, $this->lang->osspchange->passStatus, $select = 'extra', $extra = 'reviewInfo');
            $extra = json_decode($reviewInfo['list'][0]->extra, true);
            $reviewInfo = $extra['reviewInfo'];
            $result = zget($QMDmanagerList, $reviewInfo, '');
        }elseif($data->status == $this->lang->osspchange->statusList['waitClosed']){
            $reviewInfo = $this->getReviewResultList('osspchange', $data->id, $data->version, $this->lang->osspchange->passStatus, $select = 'extra', $extra = 'reviewInfo');
            $upNode     = $reviewInfo['node'];
            $extra      = json_decode($reviewInfo['list'][0]->extra, true);
            $reviewInfo = $extra['reviewInfo'];
            if($upNode == $this->lang->osspchange->statusList['waitConfirm']){
                $results = $resultList;
            }elseif($upNode == $this->lang->osspchange->statusList['waitDeptApprove']){
                $results = $systemManagerList;
            }elseif($upNode == $this->lang->osspchange->statusList['waitQMDApprove']){
                $results = $QMDmanagerList;
            }elseif($upNode == $this->lang->osspchange->statusList['waitMaxLeaderApprove']){
                $results = $maxLeaderList;
            }
            $result = zget($results, $reviewInfo, '');
        }elseif($data->status == $this->lang->osspchange->statusList['rejectToStart']){
            $version = !empty($data->version) ? --$data->version : $data->version;// 查找上一版本退回节点
            $reviewInfo = $this->getReviewResultList('osspchange', $data->id, $version, $this->lang->osspchange->rejectStatus, $select = 'extra', $extra = 'reviewInfo');
            $upNode     = $reviewInfo['node'];
            $extra      = json_decode($reviewInfo['list'][0]->extra, true);
            $reviewInfo = $extra['reviewInfo'];
            if($upNode == $this->lang->osspchange->statusList['waitConfirm']){
                $results = $resultList;
            }elseif($upNode == $this->lang->osspchange->statusList['waitDeptApprove']){
                $results = $systemManagerList;
            }elseif($upNode == $this->lang->osspchange->statusList['waitQMDApprove']){
                $results = $QMDmanagerList;
            }elseif($upNode == $this->lang->osspchange->statusList['waitMaxLeaderApprove']){
                $results = $maxLeaderList;
            }
            $result = zget($results, $reviewInfo, '');
        }elseif($data->status == $this->lang->osspchange->statusList['rejectToConfirm']){
            $version = !empty($data->version) ? --$data->version : $data->version;// 查找上一版本退回节点
            $reviewInfo = $this->getReviewResultList('osspchange', $data->id, $version, $this->lang->osspchange->rejectStatus, $select = 'extra', $extra = 'reviewInfo');
            $upNode     = $reviewInfo['node'];
            $extra      = json_decode($reviewInfo['list'][0]->extra, true);
            $reviewInfo = $extra['reviewInfo'];
            if($upNode == $this->lang->osspchange->statusList['waitDeptApprove']){
                $results = $systemManagerList;
            }elseif($upNode == $this->lang->osspchange->statusList['waitQMDApprove']){
                $results = $QMDmanagerList;
            }
            $result = zget($results, $reviewInfo, '');
        }elseif($data->status == $this->lang->osspchange->statusList['closed']){
            $result = zget($closedList, $data->closeResult, '');
        }

        return $result;
    }

}
