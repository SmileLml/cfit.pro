<?php
/**
 * The model file of sectransfer module of ZenTaoPMS.
 *
 * @package     sectransfer
 */
class sectransferModel extends model
{
    const MAXNODE           = 5;   //审批节点最大值是5

    /**
     * @param  string $browseType
     * @param  string $orderBy
     * @param  object $pager
     * @access public
     * @return object
     */
    public function getList($browseType, $queryID, $orderBy, $pager = null)
    {
        $sectransferQuery = '';
        if($browseType == 'bysearch')
        {
            $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : '';
            if($query)
            {
                $this->session->set('sectransferQuery', $query->sql);
                $this->session->set('sectransferForm', $query->form);
            }

            if($this->session->sectransferQuery == false) $this->session->set('sectransferQuery', ' 1 = 1');
            $sectransferQuery = $this->session->sectransferQuery;
        }
        $sectransferQuery = str_replace("`ifAccept` = ''","`ifAccept` = '0'", $sectransferQuery);
        $sectransfers = $this->dao->select('*')->from(TABLE_SECTRANSFER)
            ->where('deleted')->ne('1')
            ->beginIF(!in_array($browseType, ['all', 'bysearch', 'reject']))->andWhere('status')->eq($browseType)->fi()
            ->beginIF($browseType == 'reject')->andWhere('status')->in(['centerReject', 'externalReject'])->fi()
            ->beginIF($browseType == 'bysearch')->andWhere($sectransferQuery)->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll();

        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'sectransfer', $browseType != 'bysearch');
        return $sectransfers;
    }


    /**
     * 获取详细信息.
     *
     * @param  int   $id
     * @return array
     */
    public function getByID($id, $select = '*')
    {
        $sectransfer = $this->dao->select($select)->from(TABLE_SECTRANSFER)
            ->where('id')->eq($id)
            ->andWhere('deleted')->eq(0)
            ->fetch();

        $sectransfer = $this->loadModel('file')->replaceImgURL($sectransfer, 'desc');
        $sectransfer->files = $this->loadModel('file')->getByObject('sectransfer', $id);

        $cs = $this->dao->select('*')->from(TABLE_CONSUMED)->where('objectType')->eq('sectransfer') //状态流转 工作量
        ->andWhere('objectID')->eq($id)
            ->andWhere('deleted')->ne(1)
            ->orderBy('id_asc')
            ->fetchAll();
        $sectransfer->consumed = $cs;
        $this->resetNodeAndReviewerName($sectransfer->dept);
        return $sectransfer;
    }

    // 获取二线工单列表
    public function getSecondorderPairs(){
        return $this->dao->select('id,code')->from(TABLE_SECONDORDER)->where('deleted')->ne('1')->orderBy('id_desc')->fetchPairs();
    }


    /**
     * Add a issue to the review.
     *
     * @access public
     * @return int|bool
     */
    public function create()
    {
        if($this->post->jftype == 1 && empty($_POST['externalRecipient'])){
            dao::$errors[] = $this->lang->sectransfer->externalRecipientError;
            return false;
        }
        if($this->post->jftype == 1 && $this->post->externalRecipient == $this->lang->sectransfer->qszzx && empty($_POST['transferNum'])){
            dao::$errors[] = $this->lang->sectransfer->transferNumError;
            return false;
        }
        $status  = $this->lang->sectransfer->statusList['waitApply'];
        $remove = $this->post->jftype == 1 ? 'uid,subType,secondorderId' : 'uid,transitionPhase,outproject,inproject,externalRecipient,lastTransfer,transferNum,iscode';
        $version = 1;
        $data = fixer::input('post')
            ->add('status', $status)
            ->add('reviewStage', '0')
            ->add('version', $version)
            ->add('createdBy', $this->app->user->account)
            ->add('createdDate', helper::now())
            ->add('assignedTo', $this->app->user->account)
            ->add('apply', $this->app->user->account)
            ->add('dept', $this->app->user->dept)
            ->add('approver', $this->app->user->account)
            ->add('files', '')
            ->remove($remove)
           // ->stripTags($this->config->sectransfer->editor->create['id'], $this->config->allowedTags)
            ->get();

        // 校验备注必填
        if(isset($data->transferNum) && $data->transferNum < 0){
            dao::$errors[] = $this->lang->sectransfer->transferNumError;
            return false;
        }

        // 是否包含源码为否时或交付类型为工单类型时,去除默认值
        if($data->jftype == 2 || $data->iscode == 2){
            unset($data->maxleader);
        }else{
            if(empty($data->maxleader)){
                dao::$errors[] = $this->lang->sectransfer->maxleaderError;
                return false;
            }
        }
        if($data->jftype == 2) unset($data->leader);

        // 判断工单是否已关联
        if($data->jftype == 2){

            $secondorderId = $this->dao->select('id')->from(TABLE_SECTRANSFER)
                ->Where('deleted')->eq('0')
                ->andWhere('secondorderId')->eq($data->secondorderId)
                ->fetch();
            if(empty($data->secondorderId)){
                dao::$errors[] = $this->lang->sectransfer->secondOrderSelectError;
                return false;
            }
            //查询是否被关联
            $secondorderIds = array_filter(explode(',', $data->secondorderId));
            $secondorderCheckRes = $this->loadModel('secondorder')->checkSecondorderIdsIsAllowUse($secondorderIds, 'sectransfer');
            if(!$secondorderCheckRes['checkRes']){
                dao::$errors[] = implode(' ', $secondorderCheckRes['errorData']);
                return false;
            }
	        $secondorder = $this->loadModel('secondorder')->getByID($data->secondorderId);
            //工单已关联对外移交且工单 是否最终移交 为 ：是 弹出提示
            if(!empty($secondorderId->id) && $secondorder->finallyHandOver == '1'){
                dao::$errors[] = $this->lang->sectransfer->secondOrderError;
                return false;
            }
            //内部工单 是否最终移交 必填
            if(isset($secondorder->formType) && $secondorder->formType == 'internal' && empty($data->finallyHandOver)){
                dao::$errors['finallyHandOver'] = $this->lang->sectransfer->finallyHandOverError;
                return false;
            }
        }else{
            $data->secondorderId = 0;
        }
        $data->leader    = isset($data->leader) ? $data->leader : '';
        $data->maxleader = isset($data->maxleader) ? $data->maxleader : '';
        // 获取评审人员
        $nodes = [$data->CM,$data->own,$data->leader,$data->maxleader,$data->sec];

        // 获取移交类型
        $type = $data->jftype;
        $requiredFields = empty($type) ? $this->config->sectransfer->create->requiredFields : ($type == 1 ? $this->config->sectransfer->create->requiredFieldsXm : $this->config->sectransfer->create->requiredFieldsGd);
        if($type == 1 && $data->externalRecipient == $this->lang->sectransfer->qszzx && empty($data->transitionPhase)){
            dao::$errors['transitionPhase'] = sprintf($this->lang->sectransfer->emptyObject, $this->lang->sectransfer->transferStage );
            return false;
        }
        if(empty($data->transferNum)) $data->transferNum = 0;
        $finallyHandOver = isset($data->finallyHandOver) ? $data->finallyHandOver : '0';
        $data->finallyHandOver = isset($data->finallyHandOver) && !empty($data->finallyHandOver) ? $data->finallyHandOver : 0;

       // unset($data->finallyHandOver);

        // 插入数据
        $this->dao->insert(TABLE_SECTRANSFER)->data($data)
            ->autoCheck()
            ->batchCheck($requiredFields, 'notempty')
            ->exec();

        // 获取新建移交单id
        $lastId = $this->dao->lastInsertID();
        $this->submitReviewsectransfer($lastId, $nodes, $type, $version); //提交审批


        //若是外部工单移交并且状态是待交付， 修改工单的状态为交付审批中
        if($data->jftype == 2 && !empty($data->secondorderId)){
            /*$secondorder = $this->loadModel('secondorder')->getById($data->secondorderId);
            if($secondorder->status == 'todelivered'){
                $this->dao->update(TABLE_SECONDORDER)->set('status')->eq('indelivery')
                    ->set('dealUser')->eq('')
                    ->where('id')->eq($secondorder->id)
                    ->exec();
                $this->loadModel('action')->create('secondorder', $secondorder->id, 'syncstatusbyprotransfer');
                $this->loadModel('consumed')->record('secondorder', $secondorder->id, '0', $this->app->user->account, $secondorder->status, 'indelivery', array());
            }*/
           // $this->loadModel('secondorder')->syncSectransferToSecondStatus($secondorder->id, $lastId);//联动工单状态
            $this->loadModel('secondorder')->syncSecondorderStatus($secondorder->id, 'sectransfer', $lastId, 'waitApply', $lastId, 'indelivery');//新建对外移交联动工单状态
        }

        // 保存流程状态
        $this->loadModel('consumed')->record('sectransfer', $lastId, '0', $this->app->user->account, '', $status, array());

        if(!dao::isError()) 
        {
            $this->loadModel('file')->updateObjectID($this->post->uid, $lastId, 'sectransfer');
            $this->file->saveUpload('sectransfer', $lastId);
            return $lastId;
        }
        return false;
    }


    /**
     * Update a issue.
     *
     * @access public
     * @return array|bool
     */
    public function update($sectransferID)
    {
        if($this->post->jftype == 1 && empty($_POST['externalRecipient'])){
            dao::$errors[] = $this->lang->sectransfer->externalRecipientError;
            return false;
        }
        if($this->post->jftype == 1 && $this->post->externalRecipient == $this->lang->sectransfer->qszzx && empty($_POST['transferNum'])){
            dao::$errors[] = $this->lang->sectransfer->transferNumError;
            return false;
        }
        $oldTransfer = $this->getByID($sectransferID);
        $remove = $this->post->jftype == 1 ? 'uid,subType,secondorderId' : 'uid,transitionPhase,outproject,inproject,externalRecipient,lastTransfer,transferNum,iscode';
        $data = fixer::input('post')
            ->add('files', ' ')
            ->remove($remove)
            ->stripTags($this->config->sectransfer->editor->edit['id'], $this->config->allowedTags)
            ->get();

        // 移交单类型改变时要删除相对类型的字段的值
        if($oldTransfer->jftype != $data->jftype){
            if($data->jftype == 1){
                $data->subType           = '';
                $data->secondorderId     = 0;
            }else{
                $data->transitionPhase   = '';
                $data->outproject        = '';
                $data->inproject         = '';
                $data->externalRecipient = '';
                $data->lastTransfer      = '';
                $data->transferNum       = 0;
                $data->iscode            = '';
            }
        }

        // 校验备注必填
        if($this->post->jftype == 1 && $data->transferNum < 0){
            dao::$errors[] = $this->lang->sectransfer->transferNumError;
            return false;
        }

        // 是否包含源码为否时或交付类型为工单类型时,去除默认值
        if($data->iscode == 2 || $data->jftype == 2){
            unset($data->maxleader);
        }else{
            if(empty($data->maxleader)){
                dao::$errors[] = $this->lang->sectransfer->maxleaderError;
                return false;
            }
        }
        if($data->jftype == 2) unset($data->leader);

        // 判断工单是否已关联
        if($data->jftype == 2){
            if(empty($data->secondorderId)){
                dao::$errors[] = $this->lang->sectransfer->secondOrderSelectError;
                return false;
            }

            $secondorderIds = array_filter(explode(',', $data->secondorderId));
            $ignoreId = $sectransferID; //忽略id
            $secondorderCheckRes = $this->loadModel('secondorder')->checkSecondorderIdsIsAllowUse($secondorderIds, 'sectransfer', $ignoreId);
            if(!$secondorderCheckRes['checkRes']){
                dao::$errors[] = implode(' ', $secondorderCheckRes['errorData']);
                return false;
            }

            $secondorderId = $this->dao->select('id')->from(TABLE_SECTRANSFER)
                ->Where('deleted')->eq('0')
                ->andWhere('secondorderId')->eq($data->secondorderId)
                ->fetch();
            $secondorder = $this->loadModel('secondorder')->getByID($data->secondorderId);
            //工单已关联对外移交且工单 是否最终移交 为 ：是 弹出提示
            if(!empty($secondorderId->id) && $secondorder->finallyHandOver == '1' && $oldTransfer->secondorderId != $data->secondorderId){
                dao::$errors[] = $this->lang->sectransfer->secondOrderError;
                return false;
            }
            //内部工单 是否最终移交 必填
            if(isset($secondorder->formType) && $secondorder->formType == 'internal' && empty($data->finallyHandOver)){
                dao::$errors['finallyHandOver'] = $this->lang->sectransfer->finallyHandOverError;
                return false;
            }

        }

        $data = $this->loadModel('file')->processImgURL($data, $this->config->sectransfer->editor->create['id'], $this->post->uid);
        $data->editedBy = $this->app->user->account;
        $data->editedDate = date('Y-m-d H:i:s');
        // 获取评审人员
        $nodes = [$data->CM,$data->own,$data->leader,$data->maxleader,$data->sec];

        // 获取移交类型
        $type = $data->jftype;
        $requiredFields = $type == 1 ? $this->config->sectransfer->create->requiredFieldsXm : $this->config->sectransfer->create->requiredFieldsGd;
        if($type == 1 && $data->externalRecipient == $this->lang->sectransfer->qszzx && empty($data->transitionPhase)){
            dao::$errors['transitionPhase'] = sprintf($this->lang->sectransfer->emptyObject, $this->lang->sectransfer->transferStage );
            return false;
        }

        if(($oldTransfer->status == 'approveReject' || $oldTransfer->status == 'externalReject') && $oldTransfer->isAddVersion == 1){
            $data->version = $oldTransfer->version+1;
            $data->status = $oldTransfer->status;
            $data->isAddVersion = 0;
        }
        $finallyHandOver = isset($data->finallyHandOver) ? $data->finallyHandOver : '0';
        $data->finallyHandOver = isset($data->finallyHandOver) && !empty($data->finallyHandOver) ? $data->finallyHandOver : 0;

        $this->dao->update(TABLE_SECTRANSFER)->data($data)
            ->where('id')->eq($sectransferID)
            ->autoCheck()
            ->batchCheck($requiredFields, 'notempty')
            ->exec();

        //更新工单是否最终对外移交
        if($finallyHandOver){
            $nowData = $this->getByID($sectransferID);
            $this->updateSecondOrderFinallyHand($nowData->secondorderId,$sectransferID,$nowData,$oldTransfer);
        }

        if(($oldTransfer->status == 'approveReject' || $oldTransfer->status == 'externalReject') && $oldTransfer->isAddVersion == 1){
            $this->submitReviewsectransfer($sectransferID, $nodes, $type, $data->version);
        }else{
            $this->submitEditReviewsectransfer($sectransferID, $nodes, $type, $oldTransfer->version);
        }
        $newTransfer = $this->getByID($sectransferID); //获取编辑后最新的数据
        //若是外部工单移交并且状态是待交付， 修改工单的状态为交付审批中
        if($oldTransfer->jftype == 1){
            if($data->jftype == 2 && !empty($data->secondorderId)){
                $secondorder = $this->loadModel('secondorder')->getById($data->secondorderId);
              /*  if($secondorder->status == 'todelivered'){
                    $this->dao->update(TABLE_SECONDORDER)->set('status')->eq('indelivery')
                        ->set('dealUser')->eq('')
                        ->where('id')->eq($secondorder->id)
                        ->exec();
                    $this->loadModel('action')->create('secondorder', $secondorder->id, 'syncstatusbyprotransfer');
                    $this->loadModel('consumed')->record('secondorder', $secondorder->id, '0', $this->app->user->account, $secondorder->status, 'indelivery', array());
                }*/
               // $this->loadModel('secondorder')->syncSectransferToSecondStatus($secondorder->id, $sectransferID);//联动工单状态
                $this->loadModel('secondorder')->syncSecondorderStatus($secondorder->id, 'sectransfer', $sectransferID, $newTransfer->status, $sectransferID, 'indelivery');//编辑对外移交联动工单状态
            }
        }else{
            if($data->jftype == 1) {
                $secondorder = $this->loadModel('secondorder')->getById($oldTransfer->secondorderId);
                /*if ($secondorder->status == 'indelivery') {
                    $this->dao->update(TABLE_SECONDORDER)->set('status')->eq('todelivered')
                        ->set('dealUser')->eq($secondorder->acceptUser)
                        ->where('id')->eq($secondorder->id)
                        ->exec();
                    $this->loadModel('action')->create('secondorder', $secondorder->id, 'syncstatusbyprotransfer');
                    $this->loadModel('consumed')->record('secondorder', $secondorder->id, '0', $this->app->user->account, $secondorder->status, 'todelivered', array());
                }*/
                //$this->loadModel('secondorder')->syncEditSectransferToSecondStatus($secondorder->id, $sectransferID, $oldTransfer);//联动工单状态
                $this->loadModel('secondorder')->syncSecondorderStatus($secondorder->id, 'sectransfer', $sectransferID, $newTransfer->status, $sectransferID, 'todelivered');//编辑对外移交联动工单状态
            }else if ($data->jftype != 1) {
                    if ($oldTransfer->secondorderId != $data->secondorderId) {
                        $secondorder = $this->loadModel('secondorder')->getById($oldTransfer->secondorderId);
                        /*$this->dao->update(TABLE_SECONDORDER)->set('status')->eq('todelivered')
                            ->set('dealUser')->eq($secondorder->acceptUser)
                            ->where('id')->eq($secondorder->id)
                            ->exec();
                        $this->loadModel('action')->create('secondorder', $secondorder->id, 'syncstatusbyprotransfer');
                        $this->loadModel('consumed')->record('secondorder', $secondorder->id, '0', $this->app->user->account, $secondorder->status, 'todelivered', array());*/
                        //$this->loadModel('secondorder')->syncEditSectransferToSecondStatus($secondorder->id, $sectransferID, $oldTransfer);//联动工单状态
                        $this->loadModel('secondorder')->syncSecondorderStatus($secondorder->id, 'sectransfer', $sectransferID, $newTransfer->status, $sectransferID, 'todelivered');//编辑对外移交联动工单状态 解绑 状态回滚
                        $secondorderNew = $this->loadModel('secondorder')->getById($data->secondorderId);
                      /*  if($secondorderNew->status == 'todelivered'){
                            $this->dao->update(TABLE_SECONDORDER)->set('status')->eq('indelivery')
                                ->set('dealUser')->eq('')
                                ->where('id')->eq($secondorderNew->id)
                                ->exec();
                            $this->loadModel('action')->create('secondorder', $secondorderNew->id, 'syncstatusbyprotransfer');
                            $this->loadModel('consumed')->record('secondorder', $secondorderNew->id, '0', $this->app->user->account, $secondorderNew->status, 'indelivery', array());
                        }*/
                       // $this->loadModel('secondorder')->syncSectransferToSecondStatus($secondorder->id, $sectransferID);//联动工单状态
                        $this->loadModel('secondorder')->syncSecondorderStatus($secondorderNew->id, 'sectransfer', $sectransferID, $newTransfer->status, $sectransferID, 'indelivery');//编辑对外移交联动工单状态 绑定新的
                    }
            }
        }


        if(!dao::isError()) 
        {
            $this->file->updateObjectID($this->post->uid, $sectransferID, 'sectransfer');
            $this->file->saveUpload('sectransfer', $sectransferID);
            if(($oldTransfer->status == $this->lang->sectransfer->statusList['approveReject'] || $oldTransfer->status == $this->lang->sectransfer->statusList['externalReject']) && $oldTransfer->isAddVersion == 1){
                $this->loadModel('consumed')->record('sectransfer',$sectransferID, 0, $this->app->user->account, $oldTransfer->status, $data->status, array(), '');
            }
            return common::createChanges($oldTransfer, $data);
        }
        return false;
    }

    public static function isClickable($sectransfer, $action)
    {
        global $app;
        $action = strtolower($action);

        $dealUsers  = explode(',',$sectransfer->approver);
        if($action == 'edit')      return    $sectransfer->createdBy == $app->user->account && in_array($sectransfer->status,['waitApply','approveReject','externalReject']);
        if($action == 'deal')      return    in_array($sectransfer->status,['waitApply','centerReject','approveReject','externalReject']) && in_array($app->user->account,$dealUsers) && $sectransfer->isAddVersion == 0;
        if($action == 'review')    return    in_array($sectransfer->status,['waitOwnApprove','waitCMApprove','waitLeaderApprove','waitMaxLeaderApprove','waitSecApprove']) && in_array($app->user->account,$dealUsers);
        if($action == 'delete')    return    $sectransfer->createdBy == $app->user->account && in_array($sectransfer->status,['waitApply','approveReject','externalReject']);
        return true;
    }

    // 固定列表构建
    public function printCell($col, $sectransfer, $users, $depts, $status, $orderBy, $pager)
    {
        $id = $col->id;
        $apps = $this->loadModel('application')->getapplicationNameCodePairs();

        $params = "&statusNew=$status"."&orderBy=$orderBy"."&recTotal=$pager->recTotal"."&recPerPage=$pager->recPerPage"."&pageID=$pager->pageID";
        if($col->show)
        {
            $class = "c-$id";
            $title  = '';
            if($id == 'protransferDesc')
            {
                $class .= ' text-left';
                $title  = "title='{$sectransfer->protransferDesc}'";
            }
            if($id == 'publish')
            {
                $class .= ' text-left text-ellipsis';
                $title  = "title='{$sectransfer->publish}'";
            }
            if($id == 'inproject')
            {
                $class .= ' text-left';
                $title  = "title='".zget($this->getInprojects(), $sectransfer->inproject)."'";
            }
            if($id == 'app')
            {
                $class .= ' text-left text-ellipsis';
                $title  = "title='".zget($apps, $sectransfer->app)."'";
            }
            if($id == 'department')
            {
                $class .= ' text-left';
                $title  = "title='".zget($this->lang->application->teamList, $sectransfer->department)."'";
            }
            if($id == 'createdDate')
            {
                $title  = "title='{$sectransfer->createdDate}'";
            }
            if($id == 'reason')
            {
                $class .= ' text-ellipsis text-left';
                $title  = "title='{$sectransfer->reason}'";
            }
            if($id == 'dept')
            {
                $title  = "title='".zget($depts, $sectransfer->dept)."'";
            }
            $viewClass = 'actions' != $id ? ' viewClick' : '';
            echo "<td class='" . $class . $viewClass . "' $title sectransferId='".$sectransfer->id."'>";

            switch($id)
            {
                case 'id':
                    echo html::a(helper::createLink('sectransfer', 'view', "transferID=$sectransfer->id"),'<div class="protransferDesc" title="' . $sectransfer->id . '">' . $sectransfer->id .'</div>');
                    break;
                case 'protransferDesc':
                    echo html::a(helper::createLink('sectransfer', 'view', "transferID=$sectransfer->id"),'<div class="protransferDesc" title="' . $sectransfer->protransferDesc . '">' . $sectransfer->protransferDesc .'</div>');
                    break;
                case 'publish':
                    echo $sectransfer->publish;
                    break;
                case 'inproject':
                    echo zget($this->getInprojects(), $sectransfer->inproject);
                    break;
                case 'jftype':
                    echo zget($this->lang->sectransfer->transferTypeList, $sectransfer->jftype);
                    break;
                case 'app':
                    echo zget($apps, $sectransfer->app);
                    break;
                case 'department':
                    echo zget($this->lang->application->teamList, $sectransfer->department);
                    break;
                case 'reason':
                    echo $sectransfer->reason;
                    break;
                case 'iscode':
                    echo zget($this->lang->sectransfer->oldOrNotList, $sectransfer->iscode);
                    break;
                case 'createdDate':
                    echo $sectransfer->createdDate;
                    break;
                case 'status':
                    echo zget($this->lang->sectransfer->statusListName, $sectransfer->status);
                    break;
                case 'approver':
                    $dealUser = explode(',', str_replace(' ', '', $sectransfer->approver));
                    $txt = '';
                    foreach($dealUser as $account)
                        $txt .= zget($users, $account,'') . " &nbsp;";
                    echo '<div class="ellipsis" title="' . $txt . '">' . $txt .'</div>';
                    break;
                case 'apply':
                    echo zget($users, $sectransfer->apply,'');
                    break;
                case 'dept':
                    echo zget($depts, $sectransfer->dept,'');
                    break;
                case 'actions':
                    $recTotal = $pager->recTotal;
                    $recPerPage = $pager->recPerPage;
                    $pageID = $pager->pageID;
                    $param = "transferID=$sectransfer->id&status=$status&orderBy=$orderBy&recTotal=$recTotal&recPerPage=$recPerPage&pageID=$pageID";
                    common::hasPriv('sectransfer','edit') ? common::printIcon('sectransfer', 'edit', $param, $sectransfer, 'list') : '';
                    if($sectransfer->status == 'centerReject'){
                        common::hasPriv('sectransfer','deal') ? common::printIcon('sectransfer', 'deal', "transferID=$sectransfer->id", $sectransfer,'list','play', '', 'iframe', true, 'data-position = "50px" data-toggle="modal" data-type="iframe" ') : '';
                    }else{
                        common::hasPriv('sectransfer','deal') ? common::printIcon('sectransfer', 'deal', "transferID=$sectransfer->id", $sectransfer,'list','play', 'hiddenwin', '') : '';
                    }
                    common::hasPriv('sectransfer','review') ? common::printIcon('sectransfer', 'review', $param, $sectransfer, 'list','glasses','', 'iframe',true, 'data-position = "50px"') :'';
                    common::hasPriv('sectransfer','copy') ? common::printIcon('sectransfer', 'copy', $param, $sectransfer, 'list','copy') : '';
                    common::hasPriv('sectransfer','delete') ? common::printIcon('sectransfer', 'delete', "transferID=$sectransfer->id", $sectransfer, 'list', 'trash', 'hiddenwin') : '';
            }
            echo '</td>';
        }

    }


    public function buildSearchForm($queryID, $actionURL)
    {
        $apps = $this->loadModel('application')->getapplicationNameCodePairs();
        $this->loadModel('application');
        $this->config->sectransfer->search['actionURL']     = $actionURL;
        $this->config->sectransfer->search['queryID']       = $queryID;
        $this->config->sectransfer->search['params']['app']['values']           = array('' => '') + $apps;
        $this->config->sectransfer->search['params']['inproject']['values']     = array('' => '') +$this->getInprojects();
        $this->config->sectransfer->search['params']['department']['values']    = array('' => '') +$this->lang->application->teamList;
        $this->config->sectransfer->search['params']['dept']['values']          = array('' => '') +$this->loadModel('dept')->getDeptPairs();

        $this->loadModel('search')->setSearchParams($this->config->sectransfer->search);
    }

    //获得所有应用系统
    public function getApps(){
        return $this->dao->select('id, name')->from(TABLE_APPLICATION)
            ->Where('deleted')->eq('0')
            ->orderBy('id_desc')
            ->fetchPairs();
    }

    //获得所有内部项目名称
    public function getInprojects(){
        return $this->dao->select('id,concat(code,"_",mark,"_",name) as name')->from(TABLE_PROJECTPLAN)
            ->where('status')->ne('deleted')
            ->orderBy('id_desc')
            ->fetchPairs();
    }

    /**
     * 获取介质传输成功，未发送接口的数据
     * @return void
     */
    public function getUnPushData(){
        $unPushedData = $this->dao->select('*')->from(TABLE_SECTRANSFER)
            ->where('pushStatus')->eq('mediaSuccess')
            ->andWhere('status')->eq('waitDeliver')
            ->andWhere('deleted')->eq(0)->fetchALl('id');  //获取未推送的数据
        if(empty($unPushedData)) return [];
        $res = [];
        foreach ($unPushedData as $data)
        {
            $code = $this->getOrderUser($data);
            if($code != 1){
                continue;
            }
            if($data->jftype == '1'){   //项目移交
                $response = $this->sendsectransfer($data);
                $resultData = json_decode($response);
            }else if($data->jftype == '2'){     //工单移交
                $response = $this->sendSecondOrder($data);
                $resultData = json_decode($response);
                $resultData = $resultData->data;
            }
            //如果成功修改移交单发送状态
            if(!empty($resultData) and !empty($resultData->code) and $resultData->code == '200'){
                $responseData = json_decode($resultData->data);
                //只有项目移交才有外部单号   工单移交使用工单的外部单号
                if($data->jftype == '1'){
                    $externalId = $responseData->objectId;
                }else{
                    $externalId = '';
                    $this->dao->update(TABLE_SECONDORDER)
                        ->set('status')->eq('delivered')
                        ->set('dealUser')->eq('')
                        ->set('pushDate')->eq(helper::now())
                        ->where('id')->eq($data->secondorderId)
                        ->exec();
                    $this->loadModel('action')->create('secondorder',$data->secondorderId, 'syncstatusbyprotransfer');
                    $this->loadModel('consumed')->record('secondorder', $data->secondorderId, 0, 'guestjk', 'indelivery', 'delivered', array(), '');
                }
                $this->dao->update(TABLE_SECTRANSFER)
                    ->set('pushStatus')->eq('success')
                    ->set('approver')->eq('guestcn')
                    ->set('status')->eq('alreadyEdliver')
                    ->set('externalId')->eq($externalId)
                    ->where('id')->eq($data->id)
                    ->exec();
                $this->loadModel('action')->create('sectransfer',$data->id, 'syncsuccess', $resultData->message);
                $this->loadModel('consumed')->record('sectransfer', $data->id, 0, 'guestjk', 'waitDeliver', 'alreadyEdliver', array(), '');
                //更新审批流程
                $next = $this->dao->select('id')->from(TABLE_REVIEWNODE)->where('objectType')->eq('sectransfer')   //查找下一节点的状态
                ->andWhere('objectID')->eq($data->id)
                    ->andWhere('version')->eq($data->version)
                    ->andWhere('stage')->eq('6')->fetch('id');
                if($next)
                {
                    $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('syncsuccess')->where('id')->eq($next)->exec();  //更新下一节点的状态为confirming
                    $this->dao->update(TABLE_REVIEWER)->set('status')->eq('syncsuccess')->set('comment')->eq('同步清算总中心成功')->set('reviewTime')->eq(helper::now())->where('node')->eq($next)->exec();
                }
                $next = $this->dao->select('id')->from(TABLE_REVIEWNODE)->where('objectType')->eq('sectransfer')   //查找下一节点的状态
                ->andWhere('objectID')->eq($data->id)
                    ->andWhere('version')->eq($data->version)
                    ->andWhere('stage')->eq('7')->fetch('id');
                if($next)
                {
                    $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('suspend')->where('id')->eq($next)->exec();  //更新下一节点的状态为confirming
                    $this->dao->update(TABLE_REVIEWER)->set('status')->eq('suspend')->set('comment')->eq('')->set('reviewTime')->eq(null)->where('node')->eq($next)->exec();
                }
            }else{
                //失败次数超过五次，就不重推了
                $failReason = empty($resultData->mesage)?'':$resultData->message;
                if(is_array($failReason)){
                    $failReason = json_encode($failReason);
                }
                if($data->pushNum+1 >= 5){
                    $this->dao->update(TABLE_SECTRANSFER)
                        ->set('pushStatus')->eq('fail')
                        ->set('status')->eq('askCenterFailed')
                        ->set('approver')->eq($data->sec)//回退到二线专员
                        ->set('pushNum')->eq($data->pushNum+1)
                        ->set('sendFailReason')->eq($failReason)
                        ->where('id')->eq($data->id)
                        ->exec();
                    $this->loadModel('action')->create('sectransfer',$data->id, 'syncfail', $failReason);
                    $this->loadModel('consumed')->record('sectransfer', $data->id, 0, 'guestjk', 'waitDeliver', 'askCenterFailed', array(), '');
                    //更新审批流程
                    $next = $this->dao->select('id')->from(TABLE_REVIEWNODE)->where('objectType')->eq('sectransfer')   //查找下一节点的状态
                    ->andWhere('objectID')->eq($data->id)
                        ->andWhere('version')->eq($data->version)
                        ->andWhere('stage')->eq('6')->fetch('id');
                    if($next)
                    {
                        $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('syncfail')->where('id')->eq($next)->exec();  //更新下一节点的状态为confirming
                        $this->dao->update(TABLE_REVIEWER)->set('status')->eq('syncfail')->set('comment')->eq('同步清算总中心失败')->set('reviewTime')->eq(helper::now())->where('node')->eq($next)->exec();
                    }
                }else{
                    $this->dao->update(TABLE_SECTRANSFER)
                        ->set('pushNum')->eq($data->pushNum+1)
                        ->where('id')->eq($data->id)
                        ->exec();
                    $this->loadModel('action')->create('sectransfer',$data->id, 'qingzongsynfailed', $failReason);
                }
            }

            $response = json_decode($response);
            $run['sectransfer']    = $data->id;
            $run['response']            = $response;

            $res[] = $run;
        }
        return $res;

    }

    /**
     * 工单移交接口发送
     * @param $data
     * @return void
     */
    public function sendSecondOrder($data){
        $this->loadModel('requestlog');
        //获取二线工单
        $secondorder = $this->dao->select("*")->from(TABLE_SECONDORDER)->where('id')->eq($data->secondorderId)->fetch();

        $pushEnable = $this->config->global->secondorderEnable;
        $response = '';
        //判断是否开启发送反馈
        if ($pushEnable == 'enable') {
            $url = $this->config->global->secondorderFeedbackUrl;
            $pushAppId = $this->config->global->secondorderAppId;
            $pushAppSecret = $this->config->global->secondorderAppSecret;
            $fileIP       = $this->config->global->secondorderFileIP;
            //请求头
            $headers = array();
            $headers[] = 'App-Id: ' . $pushAppId;
            $headers[] = 'App-Secret: ' . $pushAppSecret;
            //数据体
            $pushData = array();
            //外部单号
            $pushData['taskListId']               = $secondorder->externalCode;
            //支持人员
            $users = $this->loadModel('user')->getPairs('noletter|noclosed');
            $pushData['supportStaff']               = zget($users,$data->createdBy,'');

            //介质传输
            $processFileInfoList = array();
            $remoteFileStr = $data->remotePath;
            $arr=explode("/", $remoteFileStr);
            $lastName=$arr[count($arr)-1];
            array_push($processFileInfoList, array('url'=> $remoteFileStr, 'md5'=> $data->mediaMd5, 'name' => $lastName));

            $pushData['feedbackFileFromJinKe'] = $processFileInfoList;
            //完成情况
            if($secondorder->type == 'consult'){
                $pushData['completionStatus'] = html_entity_decode(strip_tags(str_replace("<br />","\n",strval(htmlspecialchars_decode($secondorder->consultRes,ENT_QUOTES)))));
            }else if($secondorder->type == 'test'){
                $pushData['completionStatus'] = html_entity_decode(strip_tags(str_replace("<br />","\n",strval(htmlspecialchars_decode($secondorder->testRes,ENT_QUOTES)))));
            }else{
                $pushData['completionStatus'] = html_entity_decode(strip_tags(str_replace("<br />","\n",strval(htmlspecialchars_decode($secondorder->dealRes,ENT_QUOTES)))));
            }
            //状态
            $pushData['status']               = '已完成';


            //请求类型
            $object = 'secondorder';
            $objectType = 'secondorderFeedback';
            $method = 'POST';


            $status = 'fail';
            $extra = '';
            $result = $this->loadModel('requestlog')->http($url, $pushData, $method, 'json', array(), $headers);
            //若清总未返回结果或结果失败，就报错
            if (!empty($result)) {
                $resultData = json_decode($result);
                $data = $resultData->data;
                if ($data->code == '200') {
                    $status = 'success';
                }
                $response = $result;
            } else {
                $response = '对方无响应';
            }
            $this->requestlog->saveRequestLog($url, $object, $objectType, 'POST', $pushData, $response, $status, $extra, $data->secondorderId);
        }
        return $response;
    }

    /**
     * 项目移交接口发送
     * @param $data
     * @return void
     */
    public function sendsectransfer($data){
        $this->loadModel('requestlog');

        $pushEnable = $this->config->global->sectransferEnable;
        $response = '';
        //判断是否开启发送反馈
        if ($pushEnable == 'enable') {
            $url = $this->config->global->sectransferFeedbackUrl;
            $pushAppId = $this->config->global->sectransferAppId;
            $pushAppSecret = $this->config->global->sectransferAppSecret;
            $fileIP       = $this->config->global->sectransferFileIP;
            //请求头
            $headers = array();
            $headers[] = 'App-Id: ' . $pushAppId;
            $headers[] = 'App-Secret: ' . $pushAppSecret;
            //数据体
            $pushData = array();
            //项目移交编号
            $pushData['handoverId']               = $data->id;
            //cbp项目编号
            $outProject = $this->dao->select('*')->from(TABLE_OUTSIDEPLAN)->where('id')->eq($data->outproject)->fetch();
            $pushData['cbp']         = $outProject->code;
            //移交阶段
            switch ($data->transitionPhase){
                case 'design' :
                    $transitionPhase = '设计阶段移交';
                    break;
                case 'test' :
                    $transitionPhase = '测试阶段移交';
                    break;
                case 'operation' :
                    $transitionPhase = '投产阶段移交';
                    break;
                case 'transition' :
                    $transitionPhase = '过渡阶段移交';
                    break;
                default:
                    $transitionPhase = '';
            }
            $pushData['handoverPhase']               = $transitionPhase;
            //是否是最后一次移交
            $pushData['isLastHandover']               = $this->lang->sectransfer->orNotList[$data->lastTransfer];
            //移交说明
            $pushData['handInstruction']               = html_entity_decode(strip_tags(str_replace("<br />","\n",strval(htmlspecialchars_decode($data->reason,ENT_QUOTES)))));
            //本项目第几次移交
            $pushData['numberOfHandover']               = $data->transferNum;

            //介质传输
            $processFileInfoList = array();
            $remoteFileStr = $data->remotePath;
            $arr=explode("/", $remoteFileStr);
            $lastName=$arr[count($arr)-1];
            array_push($processFileInfoList, array('url'=> $remoteFileStr, 'md5'=> $data->mediaMd5, 'name' => $lastName));
            $pushData['relationFiles'] = $processFileInfoList;

            //请求类型
            $object = 'sectransfer';
            $objectType = 'sectransfersync';
            $method = 'POST';


            $status = 'fail';
            $extra = '';
            $result = $this->loadModel('requestlog')->http($url, $pushData, $method, 'json', array(), $headers);
            //若清总未返回结果或结果失败，就报错
            if (!empty($result)) {
                $resultData = json_decode($result);
                $data = $resultData;
                if ($data->code == '200') {
                    $status = 'success';
                }
                $response = $result;
            } else {
                $response = '对方无响应';
            }
            $this->requestlog->saveRequestLog($url, $object, $objectType, 'POST', $pushData, $response, $status, $extra, $data->id);
        }
        return $response;
    }

    // 审批
    public function review($transferID){

        $transfer = $this->getByID($transferID);

        // 检查是否允许评审
        $res = $this->checkAllowReview($transfer, $this->post->version,  $this->post->reviewStage, $this->app->user->account);
        if(!$res['result']){
            dao::$errors['statusError'] = $res['message'];
            return false;
        }
        if(empty($_POST['result']))
        {
            dao::$errors[] = $this->lang->sectransfer->resultError;
            return false;
        }
        if($this->post->result == 'reject' && empty($_POST['suggest']))
        {
            dao::$errors[] = $this->lang->sectransfer->suggestError;
            return false;
        }
        if($this->post->result == 'pass' && $transfer->status == $this->lang->sectransfer->statusList['waitCMApprove'] && empty($_POST['sftpPath']))
        {
            dao::$errors[] = $this->lang->sectransfer->sftpError;
            return false;
        }
        if($this->post->result == 'pass' && $transfer->status == $this->lang->sectransfer->statusList['waitCMApprove']){
            if (substr($_POST['sftpPath'], -4) !=='.zip'){
                dao::$errors['sftpPath'] = $this->lang->sectransfer->sftpFormat;
                return false;
            }
            $this->checkRemoteFile($_POST['sftpPath']);
            if(dao::isError()) {
                return false;
            }else{
                $this->dao->update(TABLE_SECTRANSFER)->set('openFile')->eq('true')->set('remoteFileList')->eq('')->where('id')->eq($transferID)->exec();
            }
        }

        $result = $this->loadModel('review')->check('sectransfer', $transferID, $transfer->version, $_POST['result'], $_POST['suggest']);

        if($result == 'pass')
        {
            // 待CM审核到待二线专员审核
            if($transfer->reviewStage < self::MAXNODE-1){
                $afterStage = $transfer->reviewStage + 1;  //审批通过，自动前进一步
                while($afterStage < self::MAXNODE){
                    if($transfer->status == $this->lang->sectransfer->statusList['waitOwnApprove'] && $transfer->jftype == '2'){// 部门负责人审批类型不为项目移交时直接到二线专员阶段
                        $afterStage += 2;
                        break;
                    }elseif($transfer->status == $this->lang->sectransfer->statusList['waitLeaderApprove'] && $transfer->iscode == '2') {//分管领导审批不包含源码时直接到二线专员阶段
                        $afterStage ++;
                        break;
                    }elseif(in_array($afterStage, explode(',',$transfer->requiredReviewNode) )) {  //不跳过,跳出循环
                        break;
                    }else{  //跳到下一阶段
                        $afterStage ++;
                    }
                }

                $next = $this->dao->select('id')->from(TABLE_REVIEWNODE)->where('objectType')->eq('sectransfer')   //查找下一节点的状态
                    ->andWhere('objectID')->eq($transfer->id)
                    ->andWhere('version')->eq($transfer->version)
                    ->andWhere('status')->eq('wait')->orderBy('stage,id')->fetch('id');

                if($next)
                {
                    $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('pending')->where('id')->eq($next)->exec();  //更新下一节点的状态为pending
                    $this->dao->update(TABLE_REVIEWER)->set('status')->eq('pending')->where('node')->eq($next)->exec();

                    $this->loadModel('review');
                    $reviewers = $this->review->getReviewer('sectransfer', $transferID, $transfer->version, $afterStage);
                    $this->dao->update(TABLE_SECTRANSFER)->set('approver')->eq($reviewers)->where('id')->eq($transferID)->exec();
                }

                //更新状态
                if(isset($this->lang->sectransfer->reviewBeforeStatusList[$afterStage])){
                    $status = $this->lang->sectransfer->reviewBeforeStatusList[$afterStage];
                }

                if($transfer->status == $this->lang->sectransfer->statusList['waitCMApprove']){
                    $this->dao->update(TABLE_SECTRANSFER)->set('reviewStage')->eq($afterStage)->set('status')->eq($status)->set('sftpPath')->eq($this->post->sftpPath)->where('id')->eq($transferID)->exec();
                }else{
                    $this->dao->update(TABLE_SECTRANSFER)->set('reviewStage')->eq($afterStage)->set('status')->eq($status)->where('id')->eq($transferID)->exec();
                }
                $this->loadModel('consumed')->record('sectransfer', $transferID, '0', $this->app->user->account, $transfer->status, $status, array());
            }else{
                $externalCode = new stdClass();
                if($transfer->jftype == '2'){
                    $externalCode = $this->dao->select('formType,externalCode')->from(TABLE_SECONDORDER)->where('id')->eq($transfer->secondorderId)->fetch();
                }
                // 项目移交时外部接收方为清总时状态转为待交付, 工单移交时工单类型为外部同步单并且外部单号不为空时状态转为待交付
                if(
                    ($transfer->jftype == '1' && $transfer->externalRecipient == $this->lang->sectransfer->qszzx)
                    || ($transfer->jftype == '2' && $externalCode->formType == $this->lang->sectransfer->external && !empty($externalCode->externalCode))
                ){
                    $this->dao->update(TABLE_SECTRANSFER)
                        ->set('status')->eq($this->lang->sectransfer->statusList['waitDeliver'])
                        ->set('approver')->eq('')
                        ->where('id')->eq($transferID)
                        ->exec();
                    $this->loadModel('consumed')->record('sectransfer', $transferID, '0', $this->app->user->account, $transfer->status, $this->lang->sectransfer->statusList['waitDeliver'], array());
                    //修改移交单的发送状态
                    $this->dao->update(TABLE_SECTRANSFER)
                        ->set('pushStatus')->eq('tosend')
                        ->set('approver')->eq('guestjk')
                        ->set('pushNum')->eq('0')
                        ->set('rejectUser')->eq('')
                        ->set('rejectReason')->eq('')
                        ->set('externalStatus')->eq('')
                        ->set('externalTime')->eq('')
                        ->where('id')->eq($transferID)
                        ->exec();
                    //修改流程状态
                    $next = $this->dao->select('id')->from(TABLE_REVIEWNODE)->where('objectType')->eq('sectransfer')   //查找下一节点的状态
                    ->andWhere('objectID')->eq($transfer->id)
                        ->andWhere('version')->eq($transfer->version)
                        ->andWhere('status')->eq('wait')->orderBy('stage,id')->fetch('id');

                    if($next)
                    {
                        $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('confirming')->where('id')->eq($next)->exec();  //更新下一节点的状态为confirming
                        $this->dao->update(TABLE_REVIEWER)->set('status')->eq('confirming')->where('node')->eq($next)->exec();
                    }
                }else{
                    //$isDeliveredByOrder = $this->isDeliveredByOrder($transferID, $transfer->secondorderId);
                    $this->dao->update(TABLE_SECTRANSFER)
                        ->set('status')->eq($this->lang->sectransfer->statusList['alreadyEdliver'])
                        ->set('approver')->eq('')
                        ->where('id')->eq($transferID)
                        ->exec();
                    $this->loadModel('consumed')->record('sectransfer', $transferID, '0', $this->app->user->account, $transfer->status, $this->lang->sectransfer->statusList['alreadyEdliver'], array());
                    if($transfer->jftype == '2' ){
                        /*  $this->dao->update(TABLE_SECONDORDER)
                            ->set('status')->eq('delivered')
                             ->set('dealUser')->eq('')
                             ->where('id')->eq($transfer->secondorderId)
                             ->exec();
                         $this->loadModel('action')->create('secondorder',$transfer->secondorderId, 'syncstatusbyprotransfer');
                         $this->loadModel('consumed')->record('secondorder', $transfer->secondorderId, 0, 'guestjk', 'indelivery', 'delivered', array(), '');*/
                        //$this->loadModel('secondorder')->syncSectransferToSecondStatus($transfer->secondorderId, $transferID);//联动工单状态
                        $this->loadModel('secondorder')->syncSecondorderStatus($transfer->secondorderId, 'sectransfer', $transferID, 'alreadyEdliver', $transferID, 'delivered');//处理对外移交联动工单状态
                    }
                }
            }
        }else{
            $this->dao->update(TABLE_SECTRANSFER)
                ->set('status')->eq($this->lang->sectransfer->statusList['approveReject'])
                ->set('approver')->eq($transfer->createdBy)
                ->set('isAddVersion')->eq(1)
                ->where('id')->eq($transferID)
                ->exec();
            if($transfer->jftype == '2' ){
                /*  $this->dao->update(TABLE_SECONDORDER)
                    ->set('status')->eq('delivered')
                     ->set('dealUser')->eq('')
                     ->where('id')->eq($transfer->secondorderId)
                     ->exec();
                 $this->loadModel('action')->create('secondorder',$transfer->secondorderId, 'syncstatusbyprotransfer');
                 $this->loadModel('consumed')->record('secondorder', $transfer->secondorderId, 0, 'guestjk', 'indelivery', 'delivered', array(), '');*/
                //$this->loadModel('secondorder')->syncSectransferToSecondStatus($transfer->secondorderId, $transferID);//联动工单状态
                $this->loadModel('secondorder')->syncSecondorderStatus($transfer->secondorderId, 'sectransfer', $transferID, 'approveReject', $transferID, 'indelivery');//处理对外移交联动工单状态
            }
            $this->loadModel('consumed')->record('sectransfer', $transferID, '0', $this->app->user->account, $transfer->status, $this->lang->sectransfer->statusList['approveReject'], array());
        }

        //更新工单是否最终对外移交
        $nowData = $this->getByID($transferID);
        if($nowData->secondorderId){
            $this->updateSecondOrderFinallyHand($nowData->secondorderId,$transferID,$nowData);
        }

    }

    // 检查是否允许审核
    public function checkAllowReview($transfer, $version = 1,  $reviewStage = 0, $userAccount = '')
    {
        $res = array(
            'result'  => false,
            'message' => '',
        );
        if(!$transfer){
            $res['message'] = $this->lang->common->errorParamId;
            return $res;
        }
        if(!$userAccount){
            $res['message'] = $this->lang->common->errorParamUser;
            return $res;
        }
        //审核节点已经经过
        if(($version != $transfer->version) || ($reviewStage != $transfer->reviewStage)){
            $res['message'] = $this->lang->sectransfer->nowStageError;
            return $res;
        }
        // 当前用户不允许审批
        if($userAccount != $transfer->approver){
            $res['message'] = $this->lang->sectransfer->approverError;
            return $res;
        }
        // 当前状态不允许审批
        if(!in_array($transfer->status, $this->lang->sectransfer->allowReviewList)){
            $res['message'] = $this->lang->sectransfer->stateReviewError;
            return $res;
        }

        $res['result'] = true;
        return  $res;
    }


    /**
     * 对外移交提交审核
     * @param $transferID
     * @param $nodes
     * @param $type
     */
    public function submitReviewsectransfer($transferID, $nodes ,$type, $version, $pass = '')
    {
        // 第一阶段状态为待处理
        $status = 'pending';
        $stage = 1;

        // 如果移交类型为工单移交则没有部门领导和二线专员节点
        if($type == 2){
            $nodes[3] = $nodes[2];
            unset($nodes[2]);
        }

        // 清总审批节点
        $sectransfer = $this->dao->select('*')->from(TABLE_SECTRANSFER)
            ->where('id')->eq($transferID)
            ->andWhere('deleted')->eq(0)
            ->fetch();
        //判断工单是否来自清总
        if($type == 2){
            $secondorder = $this->dao->select("*")->from(TABLE_SECONDORDER)->where('id')->eq($sectransfer->secondorderId)->fetch();
            if(!empty($secondorder->externalCode)){
                $nodes[5] = 'guestjk';
                $nodes[6] = $secondorder->createdBy;
            }
        }else{
            if($sectransfer->externalRecipient == $this->lang->sectransfer->qszzx){
                $nodes[5] = 'guestjk';
                $nodes[6] = 'guestcn';
            }
        }
        for ($i = 0; $i < self::MAXNODE +2; $i++)
        {
            $reviewer = array();
            if(empty($nodes[$i])) $status = 'ignore';
            /*if(!empty($pass) && $i == 0) $status = 'pass';
            if(!empty($pass) && $i == 1) $status = 'pending';*/
            if(isset($nodes[$i]) && !is_array($nodes[$i])){
                $reviewer = array($nodes[$i]);
            }
            $this->loadModel('review')->addNode('sectransfer', $transferID, $version, $reviewer, true, $status, $stage);
            $status = 'wait';
            $stage++;
        }
    }

    public function submitEditReviewsectransfer($transferID, $nodes ,$type, $version, $pass = ''){
        //先删除旧数据
        $ret = $this->dao->select('id')->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq('sectransfer')
            ->andWhere('objectID')->eq($transferID)
            ->andWhere('version')->eq($version)
            ->fetchAll();
        if(!empty($ret)){
            foreach ($ret as $key=>$value){
                $node = $value->id;
                $this->dao->delete()->from(TABLE_REVIEWNODE)->where('id')->eq($node)->exec();
                $this->dao->delete()->from(TABLE_REVIEWER)->where('node')->eq($node)->exec();
            }
        }

        // 第一阶段状态为待处理
        $status = 'pending';
        $stage = 1;

        // 如果移交类型为工单移交则没有部门领导和二线专员节点
        if($type == 2){
            $nodes[3] = $nodes[2];
            unset($nodes[2]);
        }

        // 清总审批节点
        $sectransfer = $this->dao->select('*')->from(TABLE_SECTRANSFER)
            ->where('id')->eq($transferID)
            ->andWhere('deleted')->eq(0)
            ->fetch();
        //判断工单是否来自清总
        if($type == 2){
            $secondorder = $this->dao->select("*")->from(TABLE_SECONDORDER)->where('id')->eq($sectransfer->secondorderId)->fetch();
            if(!empty($secondorder->externalCode)){
                $nodes[5] = 'guestjk';
                $nodes[6] = 'guestcn';
            }
        }else{
            if($sectransfer->externalRecipient == $this->lang->sectransfer->qszzx){
                $nodes[5] = 'guestjk';
                $nodes[6] = 'guestcn';
            }
        }
        for ($i = 0; $i < self::MAXNODE +2; $i++)
        {
            if(empty($nodes[$i])) $status = 'ignore';
            /*if(!empty($pass) && $i == 0) $status = 'pass';
            if(!empty($pass) && $i == 1) $status = 'pending';*/
            if(!is_array($nodes[$i])){
                $reviewer = array($nodes[$i]);
            }
            $this->loadModel('review')->addNode('sectransfer', $transferID, $version, $reviewer, true, $status, $stage);
            $status = 'wait';
            $stage++;
        }
    }

    // 处理
    public function deal($sectransferID){
        if(empty($_POST['comment'])){
            dao::$errors[] = $this->lang->sectransfer->commentError;
            return false;
        }
        $oldTransfer = $this->getByID($sectransferID);
        $version = $oldTransfer->version;
        // 内部退回(二线退回给创建人)
        if($this->post->suggestRadio == 2){
            // 校验备注必填
            if(empty($_POST['comment'])){
                dao::$errors[] = $this->lang->sectransfer->commentError;
                return false;
            }
            $data = fixer::input('post')
                ->remove('suggestRadio')
                ->remove('comment')
                ->remove('transitionPhase')
                ->remove('reason')
                ->remove('lastTransfer')
                ->remove('transferNum')
                ->remove('files')
                ->remove('uid')
                ->stripTags($this->config->sectransfer->editor->create['id'], $this->config->allowedTags)
                ->get();

            // 退回给创建人,重新走流程,原版本pending和wait改ignore
            $this->loadModel('review')->check('sectransfer', $sectransferID, $oldTransfer->version, 'reject', '');

            // 生成新版本节点
            $nodes = [$oldTransfer->CM,$oldTransfer->own,$oldTransfer->leader,$oldTransfer->maxleader,$oldTransfer->sec];
            $newVersion = $oldTransfer->version+1;

            $this->submitReviewsectransfer($sectransferID, $nodes, $oldTransfer->jftype, $newVersion);
            $version = $newVersion;
        }else{
            // 直接反馈(二线处理成待交付)
            if($oldTransfer->jftype == 1){
                $data = fixer::input('post')
                    ->remove('suggestRadio')
                    ->remove('comment')
                    ->remove('files')
                    ->remove('uid')
                    ->stripTags($this->config->sectransfer->editor->create['id'], $this->config->allowedTags)
                    ->get();
                // 校验备注必填
                if($data->transferNum < 0){
                    dao::$errors[] = $this->lang->sectransfer->transferNumError;
                    return false;
                }
            }else{
                $data = fixer::input('post')
                    ->remove('suggestRadio')
                    ->remove('comment')
                    ->remove('transitionPhase')
                    ->remove('lastTransfer')
                    ->remove('transferNum')
                    ->remove('files')
                    ->remove('uid')
                    ->stripTags($this->config->sectransfer->editor->create['id'], $this->config->allowedTags)
                    ->get();
            }
            // 校验备注必填
            if(empty($data->reason)){
                dao::$errors[] = $this->lang->sectransfer->reasonError;
                return false;
            }
            $data->pushStatus = 'tosend';
            $data->pushNum = 0;
        }

        // 修改待处理人和状态
        $data->editedBy   = $this->app->user->account;
        $data->editedDate = helper::now();
        $data->approver   = $_POST['suggestRadio'] == 2 ? $oldTransfer->createdBy : 'guestjk';
        $data->status     = $_POST['suggestRadio'] == 2 ? $this->lang->sectransfer->statusList['externalReject'] : $this->lang->sectransfer->statusList['waitDeliver'];
        $data->version = $version;
        $this->dao->update(TABLE_SECTRANSFER)->data($data)->where('id')->eq($sectransferID)->autoCheck()->exec();

        // 保存流程状态
        $this->loadModel('consumed')->record('sectransfer', $sectransferID, '0', $this->app->user->account, $oldTransfer->status, $data->status, array());

        if(!dao::isError())
        {
//            $this->loadModel('file')->updateObjectID($this->post->uid, $sectransferID, 'sectransfer');
//            $this->file->saveUpload('sectransfer', $sectransferID);
            return common::createChanges($oldTransfer, $data);
        }
        return false;
    }


    /**
     * Project: chengfangjinke
     * Desc: 发送邮件
     * liuyuhan
     */
    public function sendmail($id, $actionID)
    {
        $this->loadModel('mail');
        //邮件显示详细信息
        $sectransfer = $this->getById($id);
        $users  = $this->loadModel('user')->getPairs('noletter|noclosed');


        /* 获取后台通知中配置的邮件发信。*/
        $this->app->loadLang('custommail');
        $mailConf   = isset($this->config->global->setSectransferMail) ? $this->config->global->setSectransferMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf   = json_decode($mailConf);
        $browseType = 'sectransfer';

        /* 处理邮件发信的标题和日期。*/
        $bestDeal = $this->dao->select('*')->from(TABLE_CONSUMED)->where('objectType')->eq('sectransfer')
            ->andWhere('objectID')->eq($id)
            ->orderBy('id_desc')
            ->fetch();
        $bestDate=empty($bestDeal) ? '' : $bestDeal->createdDate;
//        $bestDate  = empty($bestDeal) ? '' : substr($bestDeal->createdDate, 0, 10);
        $mailTitle = vsprintf($mailConf->mailTitle, $mailConf->variables);

        /* Get action info. */
        $action          = $this->loadModel('action')->getById($actionID);
        $history         = $this->action->getHistory($actionID);
        $action->history = isset($history[$actionID]) ? $history[$actionID] : array();

        /* Get mail content. */
        $oldcwd     = getcwd();
        $modulePath = $this->app->getModulePath($appName = '', 'sectransfer');
        $viewFile   = $modulePath . 'view/sendmail.html.php';
        chdir($modulePath . 'view');

        if(file_exists($modulePath . 'ext/view/sendmail.html.php'))
        {
            $viewFile = $modulePath . 'ext/view/sendmail.html.php';
            chdir($modulePath . 'ext/view');
        }
        //$serverIp = $_SERVER['SERVER_NAME'];

        ob_start();
        include $viewFile;
        foreach(glob($modulePath . 'ext/view/sendmail.*.html.hook.php') as $hookFile) include $hookFile;
        $mailContent = ob_get_contents();
        ob_end_clean();
        chdir($oldcwd);

        $sendUsers = $this->getToAndCcList($sectransfer);
        if(!$sendUsers) return;
        list($toList, $ccList) = $sendUsers;
        /* 处理邮件标题。*/
        $subject = $mailTitle;

        /* Send emails. */
        $this->mail->send($toList, $subject, $mailContent, $ccList);
        if($this->mail->isError()) error_log(join("\n", $this->mail->getError()));
    }

    /**
     * Project: chengfangjinke
     * Desc: 获取收件人和抄送人列表
     * liuyuhan
     */
    public function getToAndCcList($sectransfer)
    {
        $ccList = '';
        $toList = $sectransfer->approver;
        return array($toList, $ccList);
    }

    //喧喧发信
    public function getXuanxuanTargetUser($obj,$objectType, $objectID, $actionType, $actionID, $actor = '')
    {
        $sectransfer  = $obj;
        /* Get action info. */
        $action          = $this->loadModel('action')->getById($actionID);
        $history         = $this->action->getHistory($actionID);
        $action->history = isset($history[$actionID]) ? $history[$actionID] : array();



        $sendUsers = $this->getToAndCcList($sectransfer);

        if(!$sendUsers) return;
        list($toList, $ccList) = $sendUsers;

        $mailConf   = isset($this->config->global->setSectransferMail) ? $this->config->global->setSectransferMail : '{"mailTitle":"","variables":[],"mailContent":""}';

        $server   = $this->loadModel('im')->getServer('zentao');
        $url = $server.'/sectransfer-view-'.$objectID.'.html';
        $subcontent = [];
        $subcontent['headTitle']    = '';
        $subcontent['headSubTitle'] = '';
        $subcontent['count']       = 0;
        $subcontent['id']       = 0;
        $subcontent['parent']       = '';
        $subcontent['parentURL']    = "";
        $subcontent['cardURL']      = $url;
        $subcontent['name']      = '';
        //标题
        $title = '';
        $actions = [];

        return ['toList'=>$toList,'subcontent'=>$subcontent,'url'=>$url,'title'=>$title,'actions'=>$actions,'mailconf'=>$mailConf];
    }

    //校验sftp文件夹是否存在和遍历文件
    function checkRemoteFile($remoteFile){
        //线上环境sftp是从/ftpdatas开始，过滤掉/ftpdatas
        if(strpos($remoteFile,'/ftpdatas') === 0) {
            $remoteFile = substr($remoteFile,9);
        }
        $config         = $this->dao->select('`key`,`value`')->from(TABLE_LANG)->where('section')->eq('sftpList')->fetchPairs('key');
        $conn = ssh2_connect($config['host'], $config['port']);   //登陆远程服务器
        //用户名密码验证
        if(!ssh2_auth_password($conn, $config['username'], $config['password'])) {
            dao::$errors['path'] = 'sftp用户名密码配置错误';
            return false;
        }
        $sftp           = ssh2_sftp($conn);                     //打开sftp
        //将文件名中文转码
        //$fileInfoArr = explode('/', $remoteFile);
        //$zipName = end($fileInfoArr);
        //$remoteFile = iconv('UTF-8','GB2312',$remoteFile);
        //检查文件夹是否存在
        $resource = "ssh2.sftp://{$sftp}" . $remoteFile;    //远程文件地址md5
        if (!file_exists($resource)) {
            dao::$errors['path'] =  '文件在sftp上不存在';
            return false;
        }
        //检查md5文件存不存在
        $arr = explode('.', $remoteFile);
        $ext = end($arr);
        $extLen = strlen($ext);
        $localFileMd5 = substr($remoteFile, 0, -$extLen) . 'md5';
        $resource = "ssh2.sftp://{$sftp}" . $localFileMd5;    //远程文件地址md5
        if (!file_exists($resource)) {
            $arr = explode('.', $remoteFile);
            $ext = end($arr);
            $extLen = strlen($ext);
            $localFileMd5 = substr($remoteFile, 0, -$extLen) . 'org';
            $resource = "ssh2.sftp://{$sftp}" . $localFileMd5;    //远程文件地址md5
            if (!file_exists($resource)) {
                $arr = explode('/', $remoteFile);
                $arr[sizeof($arr)-1]='md5.org';
                $localFileMd5 =  rtrim(implode('/',$arr),'/');
                $resource = "ssh2.sftp://{$sftp}" . $localFileMd5;    //远程文件地址md5
                if (!file_exists($resource)) {
                    dao::$errors['path'] =  'md5文件在sftp上不存在';
                    return false;
                }
            }
        }
    }

    /**
     * 编辑工作流程
     * @param $sectransferID
     * @param $consumedID
     * @return array
     */
    public function workloadEdit($sectransferID, $consumedID)
    {

        //返回信息
        $res = array();

        $consumed = fixer::input('post')->remove('comment, relevantUser, workload, dealUser')->get();
        $isLast = $this->loadModel('consumed')->checkIsLastConsumed($consumedID, $sectransferID, 'sectransfer');
        if($this->post->before == ''){
            $errors['before'] = sprintf($this->lang->sectransfer->emptyObject, $this->lang->sectransfer->before);
            return dao::$errors = $errors;
        }
        if($this->post->after == ''){
            $errors['after'] = sprintf($this->lang->sectransfer->emptyObject, $this->lang->sectransfer->after);
            return dao::$errors = $errors;
        }
        /*if($isLast){
            //最后一个节点时没有设置处理人
            $dealUser = $this->post->dealUser;
            if(!$dealUser){
                $errors['dealUser'] = sprintf($this->lang->sectransfer->emptyObject, $this->lang->sectransfer->approver);
                return dao::$errors = $errors;
            }
        }*/
        //获得相关配合人员工作量信息
        $consumed->details = $this->loadModel('consumed')->getPostDetails();

        $this->dao->update(TABLE_CONSUMED)->data($consumed)->autoCheck() //编辑工作量
            ->where('id')->eq($consumedID)
            ->exec();

        $actions = $this->dao->select('*')->from(TABLE_ACTION)
            ->where('objectType')->eq('sectransfer')
            ->andWhere('objectID')->eq($sectransferID)
            ->andWhere('action')->eq('deal')
            ->orderBy('id_asc')
            ->fetchAll();

        $consumeds = $this->getConsumedList($sectransferID);

        //最后一个工作量节点修改需求单的待处理状态和待处理人
        if($isLast) {
            $oldsectransfer = $this->getByID($sectransferID);
            if(($oldsectransfer->status != $consumed->after)){
                //修改审批节点
                if(in_array($oldsectransfer->status, $this->lang->sectransfer->allowReviewList) ||  $oldsectransfer->status == 'askCenterFailed' ||  $oldsectransfer->status == 'waitDeliver' ||  $oldsectransfer->status == 'approveReject'){
                    $newStage = 0;
                    foreach ($this->lang->sectransfer->reviewNodeStatusList as $key=>$value){
                        if($value == $consumed->after){
                            $newStage = $key;
                            break;
                        }
                    }
                    $dealUser = $oldsectransfer->approver;
                    if($oldsectransfer->status == 'askCenterFailed' ||  $oldsectransfer->status == 'waitDeliver'){
                        $oldsectransfer->reviewStage = 5;
                    }
                    if($newStage<=$oldsectransfer->reviewStage){
                        if($newStage != 0){
                            $pendingnode = $this->dao->select('*')->from(TABLE_REVIEWNODE)->where('objectType')->eq('sectransfer')->andWhere('objectID')->eq($sectransferID)
                                ->andWhere('version')->eq($oldsectransfer->version)->andWhere('stage')->in($newStage)->andWhere('status')->in(array('pending','pass','reject','syncfail'))->fetch();
                            $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('pending')->where('objectType')->eq('sectransfer')->andWhere('objectID')->eq($sectransferID)
                                ->andWhere('version')->eq($oldsectransfer->version)->andWhere('stage')->in($newStage)->andWhere('status')->in(array('pending','pass','reject','syncfail'))->exec();
                            $reviewerList = $this->dao->select('*')->from(TABLE_REVIEWER)->where('node')->eq($pendingnode->id)->fetchAll();
                            $reviewerArray = array();
                            foreach ($reviewerList as $reviewer){
                                array_push($reviewerArray,$reviewer->reviewer);
                            }
                            $dealUser = implode(',', $reviewerArray);
                            $this->dao->update(TABLE_REVIEWER)->set('status')->eq('pending')->set('comment')->eq('')->set('extra')->eq('')->set('reviewTime')->eq('')
                                ->where('node')->eq($pendingnode->id)->exec();
                            $this->dao->update(TABLE_SECTRANSFER)->set('reviewStage')->eq($newStage-1)->where('id')->eq($sectransferID)->exec();
                        }else{
                            $dealUser = $oldsectransfer->createdBy;
                            $this->dao->update(TABLE_SECTRANSFER)->set('reviewStage')->eq(0)->where('id')->eq($sectransferID)->exec();
                        }

                        $stageArray = array();
                        for($stage = $newStage+1; $stage<=$oldsectransfer->reviewStage+1; $stage++){
                            array_push($stageArray, $stage);
                        }
                        $nodes = $this->dao->select('*')->from(TABLE_REVIEWNODE)->where('objectType')->eq('sectransfer')->andWhere('objectID')->eq($sectransferID)
                            ->andWhere('version')->eq($oldsectransfer->version)->andWhere('stage')->in($stageArray)->andWhere('status')->in(array('pending','pass','reject','syncfail'))->fetchAll();
                        $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('wait')->where('objectType')->eq('sectransfer')->andWhere('objectID')->eq($sectransferID)
                            ->andWhere('version')->eq($oldsectransfer->version)->andWhere('stage')->in($stageArray)->andWhere('status')->in(array('pending','pass','reject','syncfail'))->exec();
                        foreach ($nodes as $node){
                            $this->dao->update(TABLE_REVIEWER)->set('status')->eq('wait')->set('comment')->eq('')->set('extra')->eq('')->set('reviewTime')->eq('')
                                ->where('node')->eq($node->id)->exec();
                        }
                    }
                    $this->dao->update(TABLE_SECTRANSFER)->set('status')->eq($consumed->after)->set('approver')->eq($dealUser)->where('id')->eq($sectransferID)->exec();
                    $data = new stdClass();
                    $data->status   = $consumed->after;
                    $data->dealUser = $dealUser;
                    $res = common::createChanges($oldsectransfer, $data);
                }
            }
        }
        $actionID = 0;
        array_splice($consumeds, 0, 1); // Remove the first work record.

        foreach($consumeds as $index => $cs)
        {
            if($cs->id == $consumedID) $actionID = $actions[$index]->id;
        }

        if($actionID)
        {
            $this->dao->update(TABLE_ACTION)->set('actor')->eq($consumed->account)->where('id')->eq($actionID)->exec();
        }

        /* 处理相关配合人员的记录（增删改） */
        $this->loadModel('consumed')->dealRelevantUser($consumedID);

        return $res;
    }

    /**
     * 删除工作流程
     * @param $sectransferID
     * @param $consumedID
     * @return array
     */
    public function workloadDelete($sectransferID, $consumedID)
    {
        $actions = $this->dao->select('*')->from(TABLE_ACTION)
            ->where('objectType')->eq('sectransfer')
            ->andWhere('objectID')->eq($sectransferID)
            ->andWhere('action')->eq('deal')
            ->orderBy('id_asc')
            ->fetchAll();

        $consumeds = $this->getConsumedList($sectransferID);

        /* Judge whether the current work record is the last one. */
        $total  = count($consumeds) - 1;
        $isLast = false;
        $previousID = 0;
        foreach($consumeds as $index => $cs)
        {
            if($cs->id == $consumedID)
            {
                $isLast = $index == $total ? true : false;
                $previousID = $consumeds[$total - 1]->id;
            }
        }

        if($isLast and $previousID)
        {
            $consumed = $this->getConsumedByID($previousID);
            $oldsectransfer = $this->getByID($sectransferID);
            //修改审批节点
            if(in_array($oldsectransfer->status, $this->lang->sectransfer->allowReviewList)){
                $newStage = '';
                foreach ($this->lang->sectransfer->reviewNodeStatusList as $key=>$value){
                    if($value == $consumed->after){
                        $newStage = $key;
                        break;
                    }
                }
                $dealUser = $oldsectransfer->approver;
                if($newStage<=$oldsectransfer->reviewStage){
                    $pendingnode = $this->dao->select('*')->from(TABLE_REVIEWNODE)->where('objectType')->eq('sectransfer')->andWhere('objectID')->eq($sectransferID)
                        ->andWhere('version')->eq($oldsectransfer->version)->andWhere('stage')->in($newStage)->andWhere('status')->in(array('pending','pass','reject','syncfail'))->fetch();
                    $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('pending')->where('objectType')->eq('sectransfer')->andWhere('objectID')->eq($sectransferID)
                        ->andWhere('version')->eq($oldsectransfer->version)->andWhere('stage')->in($newStage)->andWhere('status')->in(array('pending','pass','reject','syncfail'))->exec();
                    $reviewerList = $this->dao->select('*')->from(TABLE_REVIEWER)->where('node')->eq($pendingnode->id)->fetchAll();
                    $reviewerArray = array();
                    foreach ($reviewerList as $reviewer){
                        array_push($reviewerArray,$reviewer->reviewer);
                    }
                    $dealUser = implode(',', $reviewerArray);
                    $this->dao->update(TABLE_REVIEWER)->set('status')->eq('pending')->set('comment')->eq('')->set('extra')->eq('')->set('reviewTime')->eq('')
                        ->where('node')->eq($pendingnode->id)->exec();
                    $this->dao->update(TABLE_SECTRANSFER)->set('reviewStage')->eq($newStage-1)->where('id')->eq($sectransferID)->exec();
                    $stageArray = array();
                    for($stage = $newStage+1; $stage<=$oldsectransfer->reviewStage+1; $stage++){
                        array_push($stageArray, $stage);
                    }
                    $nodes = $this->dao->select('*')->from(TABLE_REVIEWNODE)->where('objectType')->eq('sectransfer')->andWhere('objectID')->eq($sectransferID)
                        ->andWhere('version')->eq($oldsectransfer->version)->andWhere('stage')->in($stageArray)->andWhere('status')->in(array('pending','pass','reject','syncfail'))->fetchAll();
                    $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('wait')->where('objectType')->eq('sectransfer')->andWhere('objectID')->eq($sectransferID)
                        ->andWhere('version')->eq($oldsectransfer->version)->andWhere('stage')->in($stageArray)->andWhere('status')->in(array('pending','pass','reject','syncfail'))->exec();
                    foreach ($nodes as $node){
                        $this->dao->update(TABLE_REVIEWER)->set('status')->eq('wait')->set('comment')->eq('')->set('extra')->eq('')->set('reviewTime')->eq('')
                            ->where('node')->eq($node->id)->exec();
                    }
                }
            }
            $this->dao->update(TABLE_SECTRANSFER)->set('status')->eq($consumed->after)->set('approver')->eq($dealUser)->where('id')->eq($sectransferID)->exec();
        }

        /* Get the corresponding relationship between work record and operation record. */
        $actionID = 0;
        array_splice($consumeds, 0, 1); // Remove the first work record.

        foreach($consumeds as $index => $cs)
        {
            if($cs->id == $consumedID) $actionID = $actions[$index]->id;
        }

        if($actionID) $this->dao->delete()->from(TABLE_ACTION)->where('id')->eq($actionID)->exec();

        /* 逻辑删除 */
        $this->dao->update(TABLE_CONSUMED)->set('deleted')->eq(1)->where('id')->eq($consumedID)->exec(); //逻辑删除
        /* 删除相关配合人员记录 */
        $this->dao->update(TABLE_CONSUMED)->set('deleted')->eq(1)->where('parentID')->eq($consumedID)->exec(); //删除相关配合人员记录

        return array();
    }

    public function getConsumedByID($consumedID)
    {
        return $this->dao->select('*')->from(TABLE_CONSUMED)->where('id')->eq($consumedID)->fetch();
    }

    public function getConsumedList($problemID)
    {
        $cs = $this->dao->select('*')->from(TABLE_CONSUMED)->where('objectType')->eq('sectransfer')
            ->andWhere('objectID')->eq($problemID)
            ->andWhere('parentID')->eq('0')
            ->andWhere('deleted')->eq(0)
            ->orderBy('id_asc')
            ->fetchAll();
        return $cs;
    }

    /**
     * 退回
     * @param $sectransferID
     * @param $consumedID
     * @return array
     */
    public function reject($sectransferID)
    {
        $oldsectransfer = $this->getByID($sectransferID);
        $this->dao->update(TABLE_SECTRANSFER)
            ->set('status')->eq('externalReject')
            ->set('isAddVersion')->eq(1)
            ->set('approver')->eq($oldsectransfer->createdBy)
            ->where('id')->eq($sectransferID)->exec();
       /* $this->dao->update(TABLE_SECONDORDER)
            ->set('status')->eq('indelivery')
            ->set('dealUser')->eq('')
            ->where('id')->eq($oldsectransfer->secondorderId)
            ->exec();
        $this->loadModel('action')->create('secondorder',$oldsectransfer->secondorderId, 'syncstatusbyprotransfer');
        $this->loadModel('consumed')->record('secondorder', $oldsectransfer->secondorderId, 0, $this->app->user->account, 'delivered', 'indelivery', array(), '');*/
        //$this->loadModel('secondorder')->syncSectransferToSecondStatus($oldsectransfer->secondorderId, $sectransferID);//联动工单状态
        $this->loadModel('secondorder')->syncSecondorderStatus($oldsectransfer->secondorderId, 'sectransfer', $sectransferID, 'externalReject', $sectransferID, 'indelivery');//退回对外移交联动工单状态

        $this->loadModel('action')->create('sectransfer',$oldsectransfer->id, 'reject', $this->post->rejectReason);
        $this->loadModel('consumed')->record('sectransfer', $oldsectransfer->id, 0, $this->app->user->account, 'alreadyEdliver', 'externalReject', array(), '');
    }
    public function getWaitListApi($search='',$orderBy='id_desc')
    {
        $account     = $this->app->user->account;
        $assigntomeQuery = '( 1    AND  FIND_IN_SET("'.$account.'",approver))';
        $sectransfers = $this->dao->select('*')->from(TABLE_SECTRANSFER)
            ->where($assigntomeQuery)
            ->andWhere('deleted')->ne('1')
            ->andWhere('status')->in($this->lang->sectransfer->mobileStatus)
            ->beginIF($search != '')->andwhere(" ( `id` like '%$search%' or `protransferDesc` like '%$search%' )")->fi()
            ->orderBy($orderBy)
            ->fetchAll('id');
        return $sectransfers;
    }
    /***
     * @param string $search 关键字搜索
     * @param string $orderBy
     * 手机端获取已办列表接口
     */
    public function getCompletedListApi($pager,$search='',$orderBy='id_desc'){

        $consumeds =  $this->dao->select('id,objectID')->from(TABLE_CONSUMED)
            ->where('objectType')->eq('sectransfer')
            ->andWhere('deleted')->eq('0')
            ->andWhere('createdBy')->eq($this->app->user->account)
            ->andWhere('createdDate')->ge('2024-01-01 00:00:00')
            ->fetchAll();
        $consumedID = array_unique(array_column($consumeds,'objectID'));
        $str = '"proxy":"'.$this->app->user->account.'",';
        $reviews = $this->dao->select("objectID")->from(TABLE_REVIEWER)->alias("t1")
            ->leftjoin(TABLE_REVIEWNODE)->alias('t2')
            ->on("t1.node=t2.id")
            ->where( "(reviewer = '".$this->app->user->account."' or t1.extra like '%$str%')")
            ->andWhere('t1.status')->in(['pass','reject'])
            ->andWhere('reviewTime')->ge('2024-01-01 00:00:00')
            ->andWhere('objectType')->eq('sectransfer')
            ->fetchAll();
        $reviewID = array_unique(array_column($reviews,'objectID'));

        $ids = array_unique(array_merge($consumedID,$reviewID));

        $data = $this->dao->select("id,protransferDesc,createdDate,createdBy")->from(TABLE_SECTRANSFER)
            ->where('id')->in($ids)
            ->andWhere('`status`')->ne('deleted')
            ->beginIF($search != '')->andwhere(" ( `id` like '%$search%' or protransferDesc like '%$search%' )")->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchall();

        return $data;
    }

    /**
     * 判断工单所属用户为清总还是金信
     * @param $data
     * @return int
     */
    public function getOrderUser($data)
    {
        if ('1' == $data->jftype) {//项目移交
            if($this->lang->sectransfer->qszzx == $data->externalRecipient){
                return 1;
            }elseif ($this->lang->sectransfer->cfjx == $data->externalRecipient){
                return 2;
            }
        }else{
            $info = $this->dao->select('*')->from(TABLE_SECONDORDER)->where('id')->eq($data->secondorderId)->fetch();
            if(!empty($info) && 'guestcn' == $info->createdBy){
                return 1;
            }elseif (!empty($info) && 'guestjx' == $info->createdBy){
                return 2;
            }
        }

        return 0;
    }

    /**
     * 对外移交更新工单是否最终移交内容
     * @param $data
     * @param $ID
     */
    public function updateSecondOrderFinallyHandDrop($data,$ID,$finallyHandOver){

        if($data){
            $oldSecondeOrder = $this->loadModel('secondorder')->getByID($data->secondorderId);
            $this->dao->update(TABLE_SECONDORDER)->set('finallyHandOver')->eq($finallyHandOver)
                ->where('id')->eq($data->secondorderId)
                ->exec();
            $arr = new stdClass();
            $arr->finallyHandOver = $finallyHandOver;
            $changes = common::createChanges($oldSecondeOrder, $arr);
            if($changes){
                $actionID = $this->loadModel('action')->create('secondorder', $data->secondorderId, 'editFinallyHandOvered', sprintf($this->lang->sectransfer->updateFinallyHandOver, $ID));
                $this->loadModel('action')->logHistory($actionID, $changes);
            }
        }
    }

    /**
     * 更新工单是否对外移交字段
     * @param $secondOrderID 工单
     * @param $ID  对外移交单ID
     * @param $nowData 移交单现数据
     * @param null $oldData 移交单原数据
     */
    public function updateSecondOrderFinallyHand($secondOrderID,$ID,$nowData,$oldData = array()){

        //原数据数组为空，代表新建、复制、删除方法调用
        if(!$oldData){
            //查询工单关联的所有对外移交(不包含待提交的)
            /*$allSectransfer = $this->dao->select('id,finallyHandOver')->from(TABLE_SECTRANSFER)
                ->where('deleted')->eq('0')
                ->andWhere('secondorderId')->eq($secondOrderID)
                ->andWhere('status')->ne('waitApply')
                ->fetchAll('id');
            $handOver = '0'; //是否最终移交状态
            $whichSecTransfer = '';//影响工单的对外移交单号
            //是否最终移交集合
            $finallyHandOvers = isset($allSectransfer) ? array_filter(array_column($allSectransfer,'finallyHandOver','id'),function($item){return $item !== 0;}) : '';
            if($finallyHandOvers){
                foreach ($finallyHandOvers as $key => $finallyHandOver) {
                    //是 最终移交
                    if($finallyHandOver == 1){
                        $handOver = $finallyHandOver;
                        $whichSecTransfer = $key;
                        break;
                    }else if($finallyHandOver == 2){
                        // 否
                        $handOver = $finallyHandOver;
                        $whichSecTransfer = $key;
                    }
                }
            }*/
            //查询工单关联的所有对外移交(不包含待提交的)
            $end = $this->getEndFinallyHandOver($secondOrderID);
            //更新工单并记录历史
            $this->updateHistoryAndSecond($secondOrderID,$end,$ID);
            /*$oldSecondeOrder = $this->loadModel('secondorder')->getByID($secondOrderID);
            //工单是否最终移交和目前的不一致，处理更新
            if($oldSecondeOrder->finallyHandOver != $end->handOver){
                $this->dao->update(TABLE_SECONDORDER)->set('finallyHandOver')->eq($end->handOver)
                    ->where('id')->eq($secondOrderID)
                    ->exec();
                $arr = new stdClass();
                $arr->finallyHandOver = $end->handOver;
                $changes = common::createChanges($oldSecondeOrder, $arr);
                if($changes){
                    //$whichSecTransfer 对外移交影响最终移交状态的单号为空，就获取传过来的ID记录历史
                    $actionID = $this->loadModel('action')->create('secondorder', $secondOrderID, 'editFinallyHandOvered', sprintf($this->lang->sectransfer->updateFinallyHandOver, $end->whichSecTransfer ? $end->whichSecTransfer : $ID));
                    $this->loadModel('action')->logHistory($actionID, $changes);
                }
            }*/

        }else{
            //编辑 调用
            $oldSecond = $oldData->secondorderId; //原工单
            $newSecond = $nowData->secondorderId; //现工单
            /**工单改变：
             *    1、原来没有绑定工单，原工单是空；现在绑定工单，更新现工单是否最终移交值
             *    2、原来绑定工单，现在工单是空；更新原来工单最终移交值
             *    3、原来和现在都绑定工单，不一致。更新原来、现在工单最终移交值
             * 工单未改变：
             *    一致且不为空
             */
            //不一致
            if($oldSecond != $newSecond){
                //第一点
                if(empty($oldSecond) && $newSecond){
                    //查询工单关联的所有对外移交(不包含待提交的)
                    $newEnd = $this->getEndFinallyHandOver($newSecond);
                    //更新工单并记录历史
                    $this->updateHistoryAndSecond($newSecond,$newEnd,$ID);
                }else if($oldSecond && empty($newSecond)){
                    //第二点
                    //查询工单关联的所有对外移交(不包含待提交的)
                    $oldEnd = $this->getEndFinallyHandOver($oldSecond);
                    //更新工单并记录历史
                    $this->updateHistoryAndSecond($oldSecond,$oldEnd,$ID);
                }else{
                    //第三点
                    //查询工单关联的所有对外移交(不包含待提交的)
                    $oldEnd = $this->getEndFinallyHandOver($oldSecond);
                    //更新工单并记录历史
                    $this->updateHistoryAndSecond($oldSecond,$oldEnd,$ID);
                    $newEnd = $this->getEndFinallyHandOver($newSecond);
                    //更新工单并记录历史
                    $this->updateHistoryAndSecond($newSecond,$newEnd,$ID);
                }
            }else{
                //一致，且不为空
               if($oldSecond && $newSecond){
                   //查询工单关联的所有对外移交(不包含待提交的)
                   $newEnd = $this->getEndFinallyHandOver($newSecond);
                   //更新工单并记录历史
                   $this->updateHistoryAndSecond($newSecond,$newEnd,$ID);
               }
            }
        }
    }

    /**
     * 查询工单的关联所有对外移交的最终移交并集终态(并集中有"是"，就取"是"；反之，取"否")
     * @param $secondOrderID
     * @param $sectransferId
     * @return stdClass
     */
    public function getEndFinallyHandOver($secondOrderID, $sectransferId = 0){
        //查询工单关联的所有对外移交(不包含待提交的)
        $allSectransfer = $this->dao->select('id,finallyHandOver')->from(TABLE_SECTRANSFER)
            ->where('deleted')->eq('0')
            ->andWhere('secondorderId')->eq($secondOrderID)
            ->andWhere('status')->ne('waitApply')
            ->beginIF($sectransferId > 0)->andWhere('id')->ne($sectransferId)->fi()
            ->fetchAll('id');
        $end = new stdClass();
        $end->handOver = '0'; //是否最终移交状态
        $end->whichSecTransfer = '';//影响工单的对外移交单号
        //是否最终移交集合
        $finallyHandOvers = isset($allSectransfer) ? array_filter(array_column($allSectransfer,'finallyHandOver','id'),function($item){return $item !== 0;}) : '';
        if($finallyHandOvers){
            foreach ($finallyHandOvers as $key => $finallyHandOver) {
                //是 最终移交
                if($finallyHandOver == 1){
                    $end->handOver = $finallyHandOver;
                    $end->whichSecTransfer = $key;
                    break;
                }else if($finallyHandOver == 2){
                    // 否
                    $end->handOver = $finallyHandOver;
                    $end->whichSecTransfer = $key;
                }
            }
        }
        return $end;
    }

    /**
     * 更新工单且记录历史
     * @param $secondOrderID
     * @param $end
     * @param $ID
     */
    public function updateHistoryAndSecond($secondOrderID,$end,$ID){
        $oldSecondeOrder = $this->loadModel('secondorder')->getByID($secondOrderID);
        //工单是否最终移交和目前的不一致，处理更新
        if($oldSecondeOrder->finallyHandOver != $end->handOver){
            $this->dao->update(TABLE_SECONDORDER)->set('finallyHandOver')->eq($end->handOver)
                ->where('id')->eq($secondOrderID)
                ->exec();
            $arr = new stdClass();
            $arr->finallyHandOver = $end->handOver;
            $changes = common::createChanges($oldSecondeOrder, $arr);
            if($changes){
                //$whichSecTransfer 对外移交影响最终移交状态的单号为空，就获取传过来的ID记录历史
                $actionID = $this->loadModel('action')->create('secondorder', $secondOrderID, 'editFinallyHandOvered', sprintf($this->lang->sectransfer->updateFinallyHandOver, $end->whichSecTransfer ? $end->whichSecTransfer : $ID));
                $this->loadModel('action')->logHistory($actionID, $changes);
            }
        }
    }
    /**
     * 获得参与状态联动的对外移交
     *
     * @param $ids
     * @param $linkType
     * @return array
     */
    public function getTakeLinkStatusChangeSectransferList($ids, $linkType){
        $data = [];
        if(!($ids && $linkType)){
            return $data;
        }
        $ret = $this->dao->select('*')
            ->from(TABLE_SECTRANSFER)
            ->where('id')->in($ids)
            ->andWhere('deleted')->eq(0)//不包含删除的
            ->fetchAll();
        if($ret){
            $data = $ret;
        }
        return $data;
    }

    /**
     *  获得绑定工单的对外移交列表
     *
     * @param $secondorderId
     * @param $select
     * @return array
     */
    public function getListBySecondorderId($secondorderId, $select = '*'){
        $data = [];
        if(!$secondorderId){
            return $data;
        }
        $ret = $this->dao->select($select)
            ->from(TABLE_SECTRANSFER)
            ->where('secondorderId')->eq($secondorderId)
            ->andWhere('deleted')->eq(0)//不包含删除的
            ->fetchAll();
        if($ret){
            $data = $ret;
        }
        return $data;
    }
    /**
     * @param int $id 部门id
     * 修改上海分公司节点名称
     */
    public function resetNodeAndReviewerName($id=0){
        $depts = $this->loadModel('dept')->getRecurveSubDeptIds(30);
        if ((in_array($this->app->user->dept,$depts) &&  in_array($this->app->getMethodName(),['create','copy'])) || (in_array($id,$depts) && !in_array($this->app->getMethodName(),['create','copy']))){
            $this->lang->sectransfer->reviewerList['leader'] = '上海分公司领导';
            $this->lang->sectransfer->reviewerList['maxleader'] = '上海分公司总经理';

            $this->lang->sectransfer->reviewNodeStatusLableList['waitLeaderApprove'] = '上海分公司领导';
            $this->lang->sectransfer->reviewNodeStatusLableList['waitMaxLeaderApprove'] = '上海分公司总经理';

            $this->lang->sectransfer->reviewerListNum['2'] = '分管领导';
            $this->lang->sectransfer->reviewerListNum['3'] = '总经理';
        }

    }
}
