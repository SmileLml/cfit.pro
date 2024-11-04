<?php
class secondorderModel extends model
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
        $secondorderQuery = '';
        if($browseType == 'bysearch')
        {
            $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : '';
            if($query)
            {
                $this->session->set('secondorderQuery', $query->sql);
                $this->session->set('secondorderForm', $query->form);
            }

            if($this->session->secondorderQuery == false) $this->session->set('secondorderQuery', ' 1 = 1');
            $secondorderQuery = $this->session->secondorderQuery;


        }
        $secondorderQuery = str_replace("`ifAccept` = ''","`ifAccept` = '0'", $secondorderQuery);
        $secondorders = $this->dao->select('*')->from(TABLE_SECONDORDER)
            ->where('deleted')->ne('1')
            ->beginIF($browseType != 'all' and $browseType != 'bysearch'  and $browseType != 'tomedeal')->andWhere('status')->eq($browseType)->fi()
            ->beginIF($browseType == 'tomedeal')->andWhere("FIND_IN_SET('{$this->app->user->account}', dealUser)")->fi()
            ->beginIF($browseType == 'bysearch')->andWhere($secondorderQuery)->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');
        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'secondorder', $browseType != 'bysearch');
        return $secondorders;
    }

    /**
     * Project: chengfangjinke
     * Method: buildSearchForm
     * @param $queryID
     * @param $actionURL
     */
    public function buildSearchForm($queryID, $actionURL)
    {
        $this->config->secondorder->search['actionURL'] = $actionURL;
        $this->config->secondorder->search['queryID']   = $queryID;
        $this->loadModel('search')->setSearchParams($this->config->secondorder->search);
    }

    /**
     * Method: create
     * @return mixed
     */
    public function create()
    {
        $data = fixer::input('post')
            ->remove('files,comment')
            ->join('ccList',',')
            ->stripTags($this->config->secondorder->editor->create['id'], $this->config->allowedTags)->get();

        if(empty($data->dealUser))
        {
            return dao::$errors['dealUser'] = $this->lang->secondorder->nextUserEmpty;
        }
        if($data->sourceBackground == 'project'){
            $this->config->secondorder->create->requiredFields .= ',cbpProject';
        }
        $data->createdBy    = $this->app->user->account;
        $data->createdDate  = date('Y-m-d H:i:s');
        $data->acceptUser   = $data->dealUser;
        $data->acceptDept   = $this->getDeptByUser($data->dealUser);
        $data->createdDept  = $this->app->user->dept;
        //如果创建人是二线创建者工单状态为【待分析】，否则为【待确认】
        $data->status       = isset($this->lang->secondorder->secondUserList[$this->app->user->account]) ? 'assigned' : 'toconfirmed';
        $data->formType     = 'internal';
        $this->dao->insert(TABLE_SECONDORDER)->data($data)->autoCheck()->batchCheck($this->config->secondorder->create->requiredFields, 'notempty')->exec();
        $secondorderID = $this->dao->lastInsertId();
        if(!dao::isError())
        {
            $date   = date('Y-m-d');
            $number = $this->dao->select('count(id) c')->from(TABLE_SECONDORDER)->where('createdDate')->gt($date)->fetch('c');
            $code   = 'CFIT-T-' . date('Ymd-') . sprintf('%02d', $number);

            $this->dao->update(TABLE_SECONDORDER)->set('code')->eq($code)->where('id')->eq($secondorderID)->exec();
            $this->loadModel('consumed')->record('secondorder', $secondorderID, 0, $this->app->user->account, '', $data->status, array());

            $this->loadModel('file')->updateObjectID($this->post->uid, $secondorderID, 'secondorder');
            $this->file->saveUpload('secondorder', $secondorderID);
        }

        return $secondorderID;
    }

    /**
     * Method: update
     * Product: PhpStorm
     * @param $secondorderID
     * @return array
     */
    public function update($secondorderID)
    {
        $oldsecondorder = $this->getByID($secondorderID);
        $secondorder = fixer::input('post')
            ->remove('uid,files,comment')
            ->join('ccList',',')
            ->striptags($this->config->secondorder->editor->edit['id'], $this->config->allowedTags)
            ->get();
        if(empty($secondorder->dealUser))
        {
            return dao::$errors['dealUser'] = $this->lang->secondorder->nextUserEmpty;
        }
        if($secondorder->sourceBackground == 'project'){
            $this->config->secondorder->edit->requiredFields .= ',cbpProject';
        }
        if(!in_array($oldsecondorder->status, ['toconfirmed', 'backed'])){
            return dao::$errors[] = $this->lang->secondorder->statusError;
        }
        if($oldsecondorder->formType == 'internal' && $oldsecondorder->status == 'backed'){
            $secondorder->status = 'assigned';
            $secondorder->ifAccept = '';
        }else{
            $secondorder->status = $oldsecondorder->status;
        }
        $secondorder->editedBy   = $this->app->user->account;
        $secondorder->editedDate = date('Y-m-d H:i:s');
        $secondorder->acceptUser = $secondorder->dealUser;
        $secondorder->acceptDept = $this->getDeptByUser($secondorder->dealUser);
        $secondorder->cbpProject = $this->post->sourceBackground == 'project' ? $this->post->cbpProject : '';
        $secondorder = $this->loadModel('file')->processImgURL($secondorder, $this->config->secondorder->editor->edit['id'], $this->post->uid);

        $this->dao->update(TABLE_SECONDORDER)->data($secondorder)->autoCheck()
            ->batchCheck($this->config->secondorder->edit->requiredFields, 'notempty')
            ->where('id')->eq($secondorderID)
            ->exec();
        if($this->getLastAction($secondorderID) != 'edited' && !dao::isError()) {
            $this->loadModel('consumed')->record('secondorder', $secondorderID, 0, $this->app->user->account, $oldsecondorder->status, $secondorder->status, array());
        }

        $this->loadModel('file')->updateObjectID($this->post->uid, $secondorderID, 'secondorder');
        $this->file->saveUpload('secondorder', $secondorderID);

        return common::createChanges($oldsecondorder, $secondorder);
    }

    /**
     * Project: chengfangjinke
     * Method: editAssignedTo
     * @param $secondorderID
     * @return array
     */
    public function editAssignedTo($secondorderID)
    {

        $oldsecondorder = $this->getByID($secondorderID);
        $secondorder    = array();

        if(empty($_POST['acceptUser']))
        {
            return dao::$errors['acceptUser'] = $this->lang->secondorder->acceptUserEmpty;
        }
        else
        {
            $acceptDept =  $this->dao->select('dept')->from(TABLE_USER)->where('account')->eq($_POST['acceptUser'])->fetch();
            $this->dao->update(TABLE_SECONDORDER)
                 ->set('acceptUser')->eq($_POST['acceptUser'])
                 ->set('acceptDept')->eq($acceptDept->dept)
                 ->where('id')->eq($secondorderID)
                 ->exec();
        }

        return common::createChanges($oldsecondorder, $secondorder);
    }

    /**
     * Project: chengfangjinke
     * Method: getConsumedByID
     * @param $consumedID
     * @return mixed
     */
    public function getConsumedByID($consumedID)
    {
        return $this->dao->select('*')->from(TABLE_CONSUMED)->where('id')->eq($consumedID)->fetch();
    }

    /**
     * Project: chengfangjinke
     * Method: getConsumedList
     * @param $secondorderID
     * @return mixed
     */
    public function getConsumedList($secondorderID)
    {
        $cs = $this->dao->select('*')->from(TABLE_CONSUMED)->where('objectType')->eq('secondorder')
            ->andWhere('objectID')->eq($secondorderID)
            ->andWhere('parentID')->eq('0')
            ->andWhere('deleted')->eq(0)
            ->orderBy('id_asc')
            ->fetchAll();
        return $cs;
    }

    /**
     * Project: chengfangjinke
     * Method: getByID
     * @param $secondorderID
     * @return mixed
     */
    public function getByID($secondorderID)
    {
        $secondorder = $this->dao->select("*")->from(TABLE_SECONDORDER)->where('id')->eq($secondorderID)->fetch();
        $secondorder = $this->loadModel('file')->replaceImgURL($secondorder, 'desc,progress,dealRes,consultRes,testRes');
        $secondorder->files = $this->loadModel('file')->getByObject('secondorder', $secondorder->id);
        return $secondorder;
    }

    /**
     * 通过id获得基本信息
     *
     * @param $id
     * @param string $select
     * @return mixed
     */
    public function getBasicInfoById($id, $select = '*'){
        if(!$id){
            return  false;
        }
        $ret = $this->dao->select($select)
            ->from(TABLE_SECONDORDER)
            ->where('deleted')->eq('0')
            ->andWhere('id')->eq($id)
            ->fetch();
        return $ret;
    }

    public function getByIdList($secondorderIdList, $isPairs = false)
    {
        if(empty($secondorderIdList)) return array();

        $secondorders = $this->dao->select("*")->from(TABLE_SECONDORDER)->where('id')->in($secondorderIdList)->fetchAll();
        if($isPairs)
        {
            $pairs = array();
            foreach($secondorders as $secondorder)
            {
                $pairs[$secondorder->id] = $secondorder->code;
            }
            $secondorders = $pairs;
        }
        return $secondorders;
    }

    /* 获取制版次数。*/
    public function getBuild($secondorderID)
    {
        $buildTotal = $this->dao->select('count(*) as total')->from(TABLE_CONSUMED)
            ->where('objectType')->eq('secondorder')
            ->andWhere('objectID')->eq($secondorderID)
            ->andWhere('after')->eq('build')
            ->fetch('total');
        return empty($buildTotal) ? 0 : $buildTotal;
    }

    /**
     * Project: chengfangjinke
     * Method: deal
     * @param $secondorderID
     * @return array|string
     */
    public function deal($secondorderID)
    {
        $isTransfer = false;
        $oldsecondorder = $this->getByID($secondorderID);

        $data = fixer::input('post')->stripTags($this->config->secondorder->editor->deal['id'], $this->config->allowedTags)
            ->remove('uid,files,comment,relevantUser')
            ->get();
        if(!isset($data->ifAccept)) $data->ifAccept = 1;

        if($data->completeStatus == '' && $data->ifAccept != '0')
        {
            return dao::$errors['completeStatus'] = sprintf($this->lang->secondorder->emptyObject, $this->lang->secondorder->completeStatus);
        }

        if(in_array($oldsecondorder->status, array("todelivered","indelivery","delivered","closed"))){
            return dao::$errors[''] = $this->lang->secondorder->dealError;
        }

        if($data->ifAccept == '0') {
            $data->status = 'backed';
            $data->dealUser = $oldsecondorder->createdBy;
            $data->app = $oldsecondorder->app;
            $data->planstartDate = $oldsecondorder->planstartDate;
            $data->planoverDate = $oldsecondorder->planoverDate;
            $data->startDate = $oldsecondorder->startDate;
            $data->overDate = $oldsecondorder->overDate;
            $data->consultRes = $oldsecondorder->consultRes;
            $data->acceptUser = '';
            $data->acceptDept = '0';
            $data->implementationForm = '';
            $data->internalProject = '';
            $data->completeStatus = '';
            $data->planstartDate = '';
            $data->planoverDate = '';
            $data->startDate = '';
            $data->overDate = '';
            $this->config->secondorder->deal->requiredFields = 'notAcceptReason';
            if($oldsecondorder->formType == 'external'){
                $pushFeedback = true;
            }
        }else {
            if($this->post->completeStatus == '1'){
                if($oldsecondorder->subtype == 'a5'){
                    $data->startDate = '0000-00-00';
                    $data->overDate = '0000-00-00';
                    $data->planstartDate = '0000-00-00';
                    $data->planoverDate = '0000-00-00';
                    $this->config->secondorder->deal->requiredFields = 'app,implementationForm,internalProject,taskIdentification';
                }else{
                    $this->config->secondorder->deal->requiredFields = 'app,startDate,overDate,planstartDate,planoverDate,implementationForm,internalProject,taskIdentification';
                }
                if(($oldsecondorder->formType == 'internal' && $oldsecondorder->type != 'support') or ($oldsecondorder->formType != 'internal' && in_array($oldsecondorder->type, array("other","consult")) && $oldsecondorder->subtype != 'a5')){
                    if(empty($data->handoverMethod)){
                        return dao::$errors['handoverMethod'] = $this->lang->secondorder->handoverMethodError;
                    }
                }
                //内部新建的单子
                if($oldsecondorder->formType == 'internal'){
                    if($oldsecondorder->type != 'support'){
                        if($data->handoverMethod == 'sectransfer'){
                            $sectransfer = $this->dao->select('*')->from(TABLE_SECTRANSFER)
                                ->where('secondorderId')->eq($oldsecondorder->id)
                                ->andWhere('deleted')->eq(0)
                                ->fetch();
                            //若有已关联的移交单就直接修改状态为交付审批中
                            if(!empty($sectransfer)){
                                $data->status = 'indelivery';
                                $data->dealUser = '';
                                $data->handoverMethod = 'sectransfer';
                            }else{
                                $data->status = 'todelivered';
                                $isTransfer   = true;
                                $data->dealUser = $oldsecondorder->dealUser;
                                $data->handoverMethod = 'sectransfer';
                            }
                        }else{
                            $data->status = 'closed';
                            $data->dealUser = '';
                            $data->closedBy = 'guestjk';
                            $data->closedDate = helper::now();
                            $data->handoverMethod = 'order';
                        }
                    }else{
                        $data->status = 'closed';
                        $data->dealUser = '';
                        $data->closedBy = 'guestjk';
                        $data->closedDate = helper::now();
                        $data->handoverMethod = 'order';
                    }
                }else{
                    //外部的单子
                    if(in_array($oldsecondorder->type, array("other","consult"))){
                        if($data->handoverMethod == 'sectransfer'){
                            $sectransfer = $this->dao->select('*')->from(TABLE_SECTRANSFER)
                                ->where('secondorderId')->eq($oldsecondorder->id)
                                ->andWhere('deleted')->eq(0)
                                ->fetch();
                            //若有已关联的移交单就直接修改状态为交付审批中
                            if(!empty($sectransfer)){
                                $data->status = 'indelivery';
                                $data->dealUser = '';
                                $data->handoverMethod = 'sectransfer';
                            }else{
                                $data->status = 'todelivered';
                                $isTransfer   = true;
                                $data->dealUser = $oldsecondorder->dealUser;
                                $data->handoverMethod = 'sectransfer';
                            }
                        }else{
                            $data->status = 'todelivered';
                            $pushFeedback   = true;
                            $data->dealUser = $oldsecondorder->dealUser;
                            $data->handoverMethod = 'order';
                        }
                    }else if($oldsecondorder->type != "support"){
                        $sectransfer = $this->dao->select('*')->from(TABLE_SECTRANSFER)
                            ->where('secondorderId')->eq($oldsecondorder->id)
                            ->andWhere('deleted')->eq(0)
                            ->fetch();
                        //若有已关联的移交单就直接修改状态为交付审批中
                        if(!empty($sectransfer)){
                            $data->status = 'indelivery';
                            $data->dealUser = '';
                            $data->handoverMethod = 'sectransfer';
                        }else{
                            $data->status = 'todelivered';
                            $isTransfer   = true;
                            $data->dealUser = $oldsecondorder->dealUser;
                            $data->handoverMethod = 'sectransfer';
                        }
                    }else{
                        $data->status = 'todelivered';
                        $pushFeedback   = true;
                        $data->dealUser = $oldsecondorder->dealUser;
                        $data->handoverMethod = 'order';
                    }
                }
            }else{
                if($oldsecondorder->subtype == 'a5'){
                    $this->config->secondorder->deal->requiredFields = 'app,implementationForm,internalProject,taskIdentification,progress';
                    $data->startDate = '0000-00-00';
                    $data->overDate = '0000-00-00';
                    $data->planstartDate = '0000-00-00';
                    $data->planoverDate = '0000-00-00';
                }else{
                    $this->config->secondorder->deal->requiredFields .= ',progress';
                }
                unset($data->startDate);
                unset($data->overDate);
                if(!$this->post->dealUser) {
                    return dao::$errors['dealUser'] = $this->lang->secondorder->nextUserEmpty;
                }
                if(!$this->post->progress) {
                    return dao::$errors['progress'] = sprintf($this->lang->secondorder->emptyObject, $this->lang->secondorder->progress);
                }
                $data->status     = 'tosolve';
                //$data->acceptUser = $this->post->dealUser;//迭代33:任务工单受理人、受理部门取值为待分析环节的处理人，及处理人所在部门。
                //$data->acceptDept = $this->getDeptByUser($this->post->dealUser);
                if($oldsecondorder->formType != 'internal' && $oldsecondorder->status == 'assigned' && $oldsecondorder->createdBy == 'guestcn'){
                    if($oldsecondorder->subtype != 'a5'){
                        $pushFeedback   = true;
                    }
                }
                $data->handoverMethod = '';
            }
            if(!($oldsecondorder->status == 'tosolve' && $oldsecondorder->formType == 'external')){
                $this->config->secondorder->deal->requiredFields .= ',acceptanceCondition';
            }
            //非二线，不纳入跟踪
            if($data->implementationForm != 'second'){
               $data->secondLineDevelopmentRecord = '2';
            }else{
               $data->secondLineDevelopmentRecord = '1';
            }
        }
        //$data->completeStatus != '1'  已完成 ，计划开始和计划完成隐藏 不需要验证，只验证实际开始和实际完成，即可保证计划开始和计划完成正确
        if($data->completeStatus != '1' && $data->ifAccept == '1' and $this->post->planoverDate and strtotime($this->post->planstartDate) > strtotime($this->post->planoverDate))
        {
            return dao::$errors['planoverDate'] = sprintf($this->lang->error->gt,$this->lang->secondorder->planoverDate,$this->lang->secondorder->planstartDate);
        }
        if($data->ifAccept == '1' and $this->post->overDate and strtotime($this->post->startDate) > strtotime($this->post->overDate))
        {
            return dao::$errors['overDate'] = sprintf($this->lang->error->gt,$this->lang->secondorder->overDate,$this->lang->secondorder->startDate);
        }

        //20221011 当前进展追加
        if($data->progress){
            $users = $this->loadModel('user')->getPairs('noclosed');
            $progress = '<span style="background-color: #ffe9c6">' . helper::now() . ' 由<strong>' . zget($users,$this->app->user->account,'') . '</strong>新增' . '</span><br>' . $data->progress;
            $data->progress = $oldsecondorder->progress .'<br>'.$progress;
        }else{
            unset($data->progress);
        }
        if($data->completeStatus == '1' && $data->ifAccept == '1'){
            if(in_array($oldsecondorder->type, array('consult', 'test'))) $this->config->secondorder->deal->requiredFields .= ',' . $oldsecondorder->type . 'Res';
            else $this->config->secondorder->deal->requiredFields .= ',dealRes';
        }
        $this->dao->begin();  //开启事务
        $data = $this->loadModel('file')->processImgURL($data, $this->config->secondorder->editor->deal['id'], $this->post->uid);
        $this->dao->update(TABLE_SECONDORDER)->data($data)->autoCheck()
             ->batchCheck($this->config->secondorder->deal->requiredFields, 'notempty')
             ->where('id')->eq($secondorderID)
             ->exec();
        //对外移交方式，不保存文件
        if($data->handoverMethod != 'sectransfer'){
            $this->loadModel('file')->updateObjectID($this->post->uid, $secondorderID, 'secondorderDeliver');
            $this->file->saveUpload('secondorderDeliver', $secondorderID);
        }
        $commentBak = $this->post->comment;
        $this->tryError(1);
        //外部单并且是咨询评估类发送结果给清总
        if($pushFeedback){
            if('guestjx' == $oldsecondorder->createdBy){
                $requestClass = $this->pushFeedbackJX($secondorderID);
            }else{
                if($oldsecondorder->subtype != 'a5'){
                    $requestClass = $this->pushFeedback($secondorderID);
                }else{
                    if($data->status != 'tosolve'){
                        $requestClass = $this->pushUniversalFeedback($secondorderID);
                    }
                }
            }
            $this->tryErrorRequest(1, $requestClass);
            //保存发送日志
            $this->loadModel('requestlog')->saveRequestLog($requestClass->url, $requestClass->object, $requestClass->objectType, $requestClass->method,
                $requestClass->pushData, $requestClass->response, $requestClass->status, $requestClass->extra, $requestClass->id);
            if($data->status == 'todelivered'){
                //发送结果给清总成功，工单状态变为【已交付总中心】
                $this->dao->update(TABLE_SECONDORDER)
                    ->set('status')->eq('delivered')
//                    ->set('dealUser')->eq('guestcn')
                    ->set('dealUser')->eq($oldsecondorder->createdBy)
                    ->set('pushDate')->eq(helper::now())
                    ->where('id')->eq($secondorderID)
                    ->exec();
                $data->status = 'delivered';
            }
        }
        //如果内部自建工单已完成后状态置为已关闭，状态流转需要记录两个动作
        if($oldsecondorder->formType == 'internal' && $data->status == 'closed'){
            $this->loadModel('consumed')->record('secondorder', $secondorderID, 0, $this->app->user->account, $oldsecondorder->status, 'solved');
            $this->loadModel('consumed')->record('secondorder', $secondorderID, 0, 'guestjk', 'solved', $data->status);
        }else{
            $this->loadModel('consumed')->record('secondorder', $secondorderID, 0, $this->app->user->account, $oldsecondorder->status, $data->status);
        }
        $this->dao->commit(); //提交事务
        if(
            (($data->ifAccept == '1' and ($oldsecondorder->status == 'assigned' or $oldsecondorder->status == 'tosolve')) or
            ($oldsecondorder->ifAccept == '1' and $data->ifAccept == '0')) and $oldsecondorder->type != 'support'
        ) {
            $data->type = $oldsecondorder->type;
            $data->code = $oldsecondorder->code;
            $data->project = $data->internalProject;
            $data->dealUser = $data->dealUser == '' ? $this->app->user->account : $data->dealUser;
            $data->product = '99999';
            $data->productPlan = '1';
            $data->lastDealDate = helper::now();
            $task =  $this->loadModel('problem')->toTaskProblemDemand($data,$secondorderID,'secondorder');//新增关联
            /** @var taskModel $taskModel */
            $taskModel = $this->loadModel('task');
            if($task and $data->ifAccept == '1'){
                $data->id = $secondorderID;
                $data->product = '99999';
                $data->productPlan = '1';
                $data->fixType = $data->implementationForm;
                $data->projectPlan = $data->internalProject;
               // $this->loadModel('task')->checkStageAndTask($data->internalProject, $data->app,'secondorder',$data,0);//创建任务
                $taskModel->assignedAutoCreateStageTask($data->internalProject,'secondorder',$data->app,$data->code,$data);
       
            }
        }
        $newsecondorder = $this->getByID($secondorderID);
        $_POST['comment'] = $commentBak;
        $changes = common::createChanges($oldsecondorder, $newsecondorder);;

        return [$isTransfer, $changes, $oldsecondorder->formType == 'internal' && $data->status == 'closed'];
    }

    /**
     * Project: chengfangjinke
     * Method: close
     * @param $secondorderID
     */
    public function close($secondorderID)
    {
        $oldsecondorder = $this->getByID($secondorderID);
        $data = new stdClass();
        $data->status   = 'closed';
        $data->dealUser = '';
        $data->closeReason = $this->post->closeReason;
        $data->closedBy    = $this->app->user->account;
        $data->closedDate  = date('Y-m-d  H:i:s');
        if($this->post->comment == '')
        {
            return dao::$errors['comment'] = sprintf($this->lang->secondorder->emptyObject, $this->lang->secondorder->comment);
        }
        $data = $this->loadModel('file')->processImgURL($data, $this->config->secondorder->editor->close['id'], $this->post->uid);
        $this->dao->update(TABLE_SECONDORDER)->data($data)->autoCheck()
            ->batchCheck($this->config->secondorder->close->requiredFields, 'notempty')
            ->where('id')->eq($secondorderID)->exec();

        if(!dao::isError())
        {
            $this->loadModel('consumed')->record('secondorder', $secondorderID, 0, $this->app->user->account, $oldsecondorder->status, 'closed', array());
        }
    }

    /**
     * Project: chengfangjinke
     * Method: isClickable
     * Product: PhpStorm
     * @param $secondorder
     * @param $action
     * @return bool
     */
    public static function isClickable($secondorder, $action)
    {
        global $app;
        $action = strtolower($action);
        //单子删除后，所有按钮不可见
        if($secondorder->deleted){
            return false;
        }
        switch (strtolower($action)){
            case 'edit': //编辑
                $statusList = $secondorder->formType == 'external' ? [
                    'toconfirmed'
                ] : [
                    'toconfirmed', 'backed'
                ];
                return $secondorder->formType == 'internal' and in_array($secondorder->status,$statusList) and $app->user->account == $secondorder->createdBy;
            case 'confirmed': //确认
                $dealUser = explode(',', trim($secondorder->dealUser, ','));
                return $secondorder->status == 'toconfirmed' and (in_array($app->user->account, $dealUser) || $app->user->account == 'admin');
            case 'deal': //处理
                $statusList = ['assigned', 'tosolve'];
                $dealUser = explode(',', trim($secondorder->dealUser, ','));
                return in_array($secondorder->status,$statusList) and (in_array($app->user->account, $dealUser) || $app->user->account == 'admin');
            case 'returned': //退回处理
                $statusList = ['returned'];
                $dealUser = explode(',', trim($secondorder->dealUser, ','));
                return in_array($secondorder->status,$statusList) and (in_array($app->user->account, $dealUser) || $app->user->account == 'admin');
            case 'copy': //复制
                return $secondorder->formType == 'internal' ;
            case 'close': //关闭
                return $secondorder->formType == 'internal' and $secondorder->status != 'closed' and $app->user->account == $secondorder->createdBy;
            case 'delete': //删除
                return $secondorder->formType == 'internal';
            default:
                return true;
        }
    }

    /**
     * Project: chengfangjinke
     * Method: getPairs
     * @return mixed
     */
    public function getPairs($code = '')
    {
        return $this->dao->select('id,code')->from(TABLE_SECONDORDER)
            ->where('deleted')->ne('1')
            ->andwhere('code')->in($code)
            ->orderBy('id_desc')
            ->fetchAll();
    }

    /**
     * Project: chengfangjinke
     * Method: getPairs
     * @return mixed
     */
    public function getNamePairs()
    {
        return $this->dao->select("id,concat(code,'（',IFNULL(summary,''),'）') as code")->from(TABLE_SECONDORDER)
            ->where('deleted')->ne('1')
            ->orderBy('id_desc')
            ->fetchPairs();
    }

    /**
     * Project: chengfangjinke
     * Method: getPairs
     * @return mixed
     */
    public function getNamePairsAll()
    {
        return $this->dao->select("id,concat(code,'（',IFNULL(summary,''),'）') as code")->from(TABLE_SECONDORDER)
            ->orderBy('id_desc')
            ->fetchPairs();
    }

    /**
     * 根据多个id获取信息
     * @param array $ids
     * @return stdClass
     */
    public function getPairsByIds($ids)
    {
        if(empty($ids)) return null;
        $info = $this->dao->select('id,code,`summary`')->from(TABLE_SECONDORDER)
            ->where('status')->ne('deleted')
            ->andwhere('id')->in($ids)
            ->orderBy('id_desc')
            ->fetchall();
        $secondorders = new stdClass();
        foreach ($info as $item)
        {
            $id = $item->id;
            $secondorders->$id = ['code'=>$item->code, 'desc' =>$item->summary];
        }
        return  $secondorders;
    }

    /**
     * Send mail.
     *
     * @param  int    $secondorderID
     * @param  int    $actionID
     * @access public
     * @return void
     */
    public function sendmail($secondorderID, $actionID)
    {
        $this->loadModel('mail');
        $secondorder = $this->getById($secondorderID);
        $users   = $this->loadModel('user')->getPairs('noletter');

        /* 获取后台通知中配置的邮件发信。*/
        $this->app->loadLang('custommail');
        $mailConf   = isset($this->config->global->setSecondorderMail) ? $this->config->global->setSecondorderMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf   = json_decode($mailConf);
        $browseType = 'secondorder';

        /* 处理邮件发信的标题和日期。*/
        $bestDeal = $this->dao->select('*')->from(TABLE_CONSUMED)->where('objectType')->eq('secondorder')
            ->andWhere('objectID')->eq($secondorderID)
            ->orderBy('id_desc')
            ->fetch();
        $bestDate  = empty($bestDeal) ? '' : substr($bestDeal->createdDate, 0, 10);
        $mailTitle = vsprintf($mailConf->mailTitle, $mailConf->variables);

        /* Get action info. */
        $action          = $this->loadModel('action')->getById($actionID);
        $history         = $this->action->getHistory($actionID);
        $action->history = isset($history[$actionID]) ? $history[$actionID] : array();

        /* Get mail content. */
        $oldcwd     = getcwd();
        $modulePath = $this->app->getModulePath($appName = '', 'secondorder');
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

        $toList = $secondorder->dealUser;
        $ccList = '';
        if(in_array($action->action, array('created','edited')))
        {
            $ccList = $secondorder->ccList ?? '';
            $ccDeptList = $this->config->secondorder->ccDeptList;
            $user = $this->loadModel('user')->getById($secondorder->dealUser);
            $actionUser = $this->loadModel('user')->getById($this->app->user->account);
            if($actionUser->dept != $user->dept){
                $dept = $user->dept;
                $ccList = $ccList.','.$ccDeptList->$dept;
            }
        }

        // 完成状态处理
        $tome = false;
        if($secondorder->ifAccept == '1' and $secondorder->status == 'closed')
        {
            $mailTitle = sprintf($this->lang->secondorder->ccMailTitle, $secondorder->code);
            list($toList, $ccList) = $this->getToAndCcList($secondorder);
            $tome = true;
        }

        if($action->action == 'reviewedconfirm' or $action->action == 'editAssignTo' or $action->action == 'deal'){
            $ccDeptList = $this->config->secondorder->ccDeptList;
            $user = $this->loadModel('user')->getById($secondorder->dealUser);
            $actionUser = $this->loadModel('user')->getById($this->app->user->account);
            if($actionUser->dept != $user->dept){
                $dept = $user->dept;
                $ccList = $ccDeptList->$dept;
            }
        }
        /* 处理邮件标题。*/
        $subject = $mailTitle;

        /* Send emails. */
        $this->mail->send($toList, $subject, $mailContent, $ccList, $tome);
        if($this->mail->isError()) error_log(join("\n", $this->mail->getError()));
    }

    /**
     * Project: chengfangjinke
     * Method: getToAndCcList
     * Product: PhpStorm
     * @param $object
     * @return array
     */
    public function getToAndCcList($object)
    {
        /* Set toList and ccList. */
        $toList = $this->dao->select('actor')->from(TABLE_ACTION)->where('objectType')->eq('secondorder')->andWhere('objectID')->eq($object->id)->andWhere('action')->eq('deal')->fetchAll();
        $toArr = Array();
        Array_push($toArr, $this->app->user->account);
        foreach ($toList as $to)
        {
            if(!in_array($to->actor, $toArr))
            {
                Array_push($toArr, $to->actor);
            }
        }

        $details = $this->loadModel('dept')->getByID($object->acceptDept);
        $ccList  = trim($details->manager, ',');

        return array(implode(',',$toArr), $ccList);
    }

    /**
     * 根据拼音首字母取系统id
     * @param $code
     * @return mixed
     */
    public function getAppIdByAppCode($codes)
    {
        $apps = $this->dao->select('id')->from(TABLE_APPLICATION)->where('code')->in($codes)->fetchAll('id');
        if(empty($apps)) return '';
        return implode(',', array_keys($apps));
    }

    function checkTimeFormat($date)
    {
        if(date('Y-m-d H:i:s', strtotime($date)) == $date ){
            return true;
        }
        if(date('Y-m-d H:i', strtotime($date)) == $date ){
            return true;
        }
        return false;
    }

    function checkDateFormat($date)
    {
        if(date('Y-m-d', strtotime($date)) == $date ){
            return true;
        }
        return false;
    }

    // 获取分类下的子类数据。
    public function getChildTypeList($assignType = '')
    {
        // 自定义的secondorder子类数据。
        $typeList      = $this->lang->secondorder->typeList;
        $childTypeList = isset($this->lang->secondorder->childTypeList) ? $this->lang->secondorder->childTypeList['all'] : '[]';
        $childTypeList = json_decode($childTypeList, true);
        $customList    = empty($childTypeList[$assignType]) ? array('0' => '') : $childTypeList[$assignType];
        if(!empty($customList)) $customList = array('0' => '') + $customList;
        return $customList;
    }

    // 获取分类下的子类数据键值对。
    public function getChildTypeTileList($firstNull = false)
    {
        // 自定义的secondorder子类数据。
        $typeList      = $this->lang->secondorder->typeList;
        $childTypeList = isset($this->lang->secondorder->childTypeList) ? $this->lang->secondorder->childTypeList['all'] : '[]';
        $childTypeList = json_decode($childTypeList, true);

        $customList = array();
        if($firstNull) $customList[0] = '';
        foreach($childTypeList as $type => $items)
        {
            // secondorder分类不存在的话，子类也不展示了。
            if(empty($typeList[$type])) continue;

            foreach($items as $key => $value)
            {
                $customList[$key] = $value;
            }
        }

        return $customList;
    }

    // 获取分类下的子类数据(父分类)。
    public function getChildTypeParentList()
    {
        // 自定义的secondorder子类数据。
        $typeList      = $this->lang->secondorder->typeList;
        $childTypeList = isset($this->lang->secondorder->childTypeList) ? $this->lang->secondorder->childTypeList['all'] : '[]';
        $childTypeList = json_decode($childTypeList, true);
        return $childTypeList;
    }

    public function getChildTypeParentNameList()
    {
        $childTypeList = $this->getChildTypeTileList(true);
        $allTypeList   = $this->getChildTypeParentList();
        foreach($childTypeList as $index => $name)
        {
            if(empty($index))
            {
                $childTypeList[$index] = $name;
                continue;
            }

            foreach($allTypeList as $typeKey => $typeData)
            {
                $typeName = $this->lang->secondorder->typeList[$typeKey];
                foreach($typeData as $childIndex => $childValue)
                {
                    if($index == $childIndex) $childTypeList[$index] = $typeName . '-' . $name;
                }
            }
        }
        return $childTypeList;
    }

    // 获取分类下的子类数据(父分类)。
    public function getAllChildTypeList()
    {
        // 自定义的secondorder子类数据。
        $typeList      = $this->lang->secondorder->typeList;
        $childTypeList = isset($this->lang->secondorder->childTypeList) ? $this->lang->secondorder->childTypeList['all'] : '[]';
        $childTypeList = json_decode($childTypeList, true);

        $data = array();
        foreach($typeList as $key => $type)
        {
            if(empty($key)) continue;
            if(empty($childTypeList[$key]))
            {
                $data[$key]['name']  = $type;
                $data[$key]['child'] = array();
            }
            else
            {
                $data[$key]['name']  = $type;
                $data[$key]['child'] = $childTypeList[$key];
            }
        }
        return $data;
    }

    /**
     * @param $account
     * 获取人员部门
     */
    public function getDeptByUser($account)
    {
        $dept = $this->dao->select('dept')->from(TABLE_USER)->where('account')->eq($account)->fetch('dept');

        return $dept;
    }

    /**
     * @param $id
     * 获得最后的action记录
     */
    public function getLastAction($id)
    {
        $lastAction = $this->dao->select('action')
                        ->from(TABLE_ACTION)
                        ->where('objectType')->eq('secondorder')
                        ->andWhere('objectID')->eq($id)
                        ->orderBy('action_desc')
                        ->fetch('action');
        return $lastAction;
    }

    /**
     * @param $objectID
     * @return mixed
     */
    public function getConsumedsByID($objectID)
    {
        $details = $this->dao->select('*')->from(TABLE_CONSUMED)
            ->where('objectID')->eq($objectID)
            ->andWhere('objectType')->eq('secondorder')
            ->andWhere('deleted')->eq(0)
            ->fetchAll();

        return $details;
    }

    /**
     * @param $secondorderID
     * @param $consumedID
     * @return array
     * 编辑流程状态
     */
    public function statusedit($secondorderID, $consumedID)
    {

        $res = array();

        $consumed = fixer::input('post')->remove('comment, relevantUser, workload, dealUser')->get();
        $isLast = $this->loadModel('consumed')->checkIsLastConsumed($consumedID, $secondorderID, 'secondorder');
        if($isLast){
            //最后一个节点时没有设置处理人
            $dealUser = $this->post->dealUser;
//            if(!$dealUser){
//                $errors['dealUser'] = sprintf($this->lang->secondorder->emptyObject, $this->lang->secondorder->dealUser);
//                return dao::$errors = $errors;
//            }
        }

        $this->dao->update(TABLE_CONSUMED)->data($consumed)->where('id')->eq($consumedID)->exec();

        $oldsecondorder = $this->getByID($secondorderID);
        if(($oldsecondorder->status != $consumed->after) || ($oldsecondorder->dealUser != $dealUser)) {
            $this->dao->update(TABLE_SECONDORDER)->set('status')->eq($consumed->after)->set('dealUser')->eq($dealUser)->where('id')->eq($secondorderID)->exec();
            $data = new stdClass();
            $data->status   = $consumed->after;
            $data->dealUser = $dealUser;
            $res = common::createChanges($oldsecondorder, $data);
        }

        return $res;
    }

    /**
     * @param $secondorderID
     * @return mixed
     * 获取历次当前进展
     */
    public function getProgress($secondorderID)
    {
        $progress =  $this->dao->select('actor,date,comment')->from(TABLE_ACTION)->where('objectType')->eq('secondorderProgress')->andWhere('objectID')->eq($secondorderID)->fetchAll();
        return $progress;
    }


    /**
     * 获取项目
     * @param $deptID
     * @return mixed
     */
    public function getProjectPlanInfo($deptID)
    {
        $projectPlanId = $this->dao->select('project')->from(TABLE_PROJECTPLAN)->where('secondLine')->eq('1')->andWhere('year')->eq('2022')->andWhere('code')->like('%EX')->andWhere('bearDept')->eq($deptID)->fetch('project');
        return $projectPlanId;
    }

    /**
     * 获取配合人员
     * @param $consumeds
     * @return mixed
     */
    public function getrelevantUsers($consumeds)
    {
        $relevantUsers = [];
        $users   = $this->loadModel('user')->getPairs('noclosed|noletter');
        foreach ($consumeds as $consumed)
        {
            if(empty($consumed->details)) continue;
            $details = $this->loadModel('consumed')->getConsumedDetailsArray($consumed->details);

            foreach ($details as $detail){
                $relevantUsers[$detail->account] = zget($users, $detail->account);
            }
        }

        return implode('，', $relevantUsers);
    }

    public function getXuanxuanTargetUser($obj,$objectType, $objectID, $actionType, $actionID, $actor = ''){
        $toList = '';

        /* Get action info. */
        $action          = $this->loadModel('action')->getById($actionID);
        $history         = $this->action->getHistory($actionID);
        $action->history = isset($history[$actionID]) ? $history[$actionID] : array();

        $toList = $obj->dealUser;
        $ccList = '';
        if(in_array($action->action, array('created','edited')))
        {
            $ccList = $obj->ccList ?? '';
        }

        // 完成状态处理
        $tome = false;
        if($obj->ifAccept == '1' and $obj->status == 'solved')
        {
            list($toList, $ccList) = $this->getToAndCcList($obj);
            $tome = true;
        }

        if(is_array($toList)){
            $toList =  implode(",",$toList);
        }
        $server   = $this->loadModel('im')->getServer('zentao');
        //$url = $server.helper::createLink($objectType, 'view', "id=$objectID", 'html');
        $url = $server.'/secondorder-view-'.$objectID.'.html';
        $subcontent = [];
        $subcontent['headTitle']    = '';

        $subcontent['headSubTitle'] = '';


        $subcontent['count']       = 0;
        $subcontent['id']       = 0;
        $subcontent['parent']       = '';
        $subcontent['parentURL']    = "";
        $subcontent['cardURL']      = $url;
        $subcontent['name']      = '';//消息体 编号后边位置 标题
        //标题
        $title = '';
        $actions = [];

        return ['toList'=>$toList,'subcontent'=>$subcontent,'url'=>$url,'title'=>$title,'actions'=>$actions];

    }

    /**
     * 通过外部单号获取信息
     * @param $externalCode
     * @return mixed
     */
    public function getByExternalCode($externalCode)
    {
        $data = $this->dao->select('*')->from(TABLE_SECONDORDER)
            ->where('externalCode')->eq($externalCode)
            ->fetch();
        return $data;
    }

    /**
     * 总中心退回处理
     * @param $secondorderID
     * @return array
     */
    public function returned($secondorderID)
    {
        $oldsecondorder = $this->getByID($secondorderID);

        switch ($this->post->returnedConfirm){
            case 2:
                $status = 'assigned';
                $this->dao->update(TABLE_SECONDORDER)
                    ->set('status')->eq($status)
                    ->set('dealUser')->eq($oldsecondorder->acceptUser)
                    ->set('completeStatus')->eq('')
                    ->set('startDate')->eq('')
                    ->set('overDate')->eq('')
                    ->set('planstartDate')->eq('')
                    ->set('planoverDate')->eq('')
                    ->where('id')->eq($secondorderID)
                    ->exec();
                break;
            case 1:
                if($oldsecondorder->type == 'consult' && $this->post->consultRes == ''){
                    dao::$errors['consultRes'] = sprintf($this->lang->secondorder->emptyObject, $this->lang->secondorder->consultRes);
                }elseif ($oldsecondorder->type == 'test' && $this->post->testRes == ''){
                    dao::$errors['testRes'] = sprintf($this->lang->secondorder->emptyObject, $this->lang->secondorder->testRes);
                }elseif ($oldsecondorder->type != 'consult' && $oldsecondorder->type != 'test' && $this->post->dealRes == ''){
                    $value =  $oldsecondorder->type == 'support' ? $this->lang->secondorder->supportRes : $this->lang->secondorder->dealRes;
                    dao::$errors['dealRes'] = sprintf($this->lang->secondorder->emptyObject, $value);
                }
                $status = 'delivered';
                $this->dao->begin();  //开启事务
                $this->dao->update(TABLE_SECONDORDER)
                    ->set('status')->eq($status)
                    ->set('dealUser')->eq($oldsecondorder->createdBy)
                    ->beginIF($oldsecondorder->type == 'consult')
                    ->set('consultRes')->eq($this->post->consultRes)
                    ->FI()
                    ->beginIF($oldsecondorder->type == 'test')
                    ->set('testRes')->eq($this->post->testRes)
                    ->FI()
                    ->beginIF($oldsecondorder->type != 'test' && $oldsecondorder->type != 'consult')
                    ->set('dealRes')->eq($this->post->dealRes)
                    ->FI()
                    ->where('id')->eq($secondorderID)
                    ->exec();
                $this->loadModel('file')->updateObjectID($this->post->uid, $secondorderID, 'secondorderDeliver');
                $this->file->saveUpload('secondorderDeliver', $secondorderID);
                $this->tryError(1);
                //外部单并且是咨询评估类发送结果给清总
                if($oldsecondorder->formType == 'external' and ($oldsecondorder->type == 'consult' or $oldsecondorder->type == 'other' or $oldsecondorder->type == 'support')){
                    if($oldsecondorder->subtype == 'a5'){
                        $requestClass = $this->pushUniversalFeedback($secondorderID);
                    }else{
                        $requestClass = 'guestcn' == $oldsecondorder->createdBy ? $this->pushFeedback($secondorderID) : $this->pushFeedbackJX($secondorderID);
                    }
                    $this->tryErrorRequest(1, $requestClass);
                    //保存发送日志
                    $this->loadModel('requestlog')->saveRequestLog($requestClass->url, $requestClass->object, $requestClass->objectType, $requestClass->method,
                        $requestClass->pushData, $requestClass->response, $requestClass->status, $requestClass->extra, $requestClass->id);
                }
                $this->dao->commit(); //提交事务
                break;
            default:
                dao::$errors['returnedConfirm'] = sprintf($this->lang->secondorder->emptyObject, $this->lang->secondorder->returnedConfirm);
                return [];
        }

        $this->loadModel('consumed')
            ->record('secondorder', $secondorderID, 0, $this->app->user->account, $oldsecondorder->status, $status, array());
        $newsecondorder = $this->getByID($secondorderID);

        return common::createChanges($oldsecondorder, $newsecondorder);
    }

    /**
     * 发送二线工单反馈信息-未受理-实时推送
     * @param $externalCode
     * @return mixed
     */
    public function pushFeedback($id){
        $this->loadModel('requestlog');
        //获取二线工单
        $secondorder = $this->dao->select("*")->from(TABLE_SECONDORDER)->where('id')->eq($id)->fetch();

        $pushEnable = $this->config->global->secondorderEnable;
        $requestClass = new stdClass();
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
            $pushData['supportStaff'] = zget($users,$this->app->user->account,'');

            //状态为未受理
            if($secondorder->status == 'backed'){
                //未受理原因
                $pushData['unacceptedReason'] = !empty($secondorder->notReceiveReason)?$secondorder->notReceiveReason:$secondorder->notAcceptReason;
                //状态
                $pushData['status']           = '未受理';
            }else if($secondorder->status == 'tosolve'){    //状态为已受理
                //文件
                /*$files = $this->loadModel('file')->getByObject('secondorderDeliver', $id);
                $processFileInfoList = array();
                foreach ($files as $file){
                    if($file->extension){
                        $tail = strlen($file->extension) + 1;
                    }
                    $realRemotePath = substr($fileIP.'/api.php?m=api&f=getfile&code=jinke1problem&time=1&token=1&filename='.$file->pathname, 0, -$tail); //实际存的附件没有后缀 需要去掉
                    $localRealFile =  $file->realPath; //实际存的附件
                    $md5 = md5_file($localRealFile);
                    array_push($processFileInfoList, array('url'=> $realRemotePath, 'md5'=> $md5, 'name' => $file->title));
                }
                $pushData['feedbackFileFromJinKe'] = $processFileInfoList;*/
                //受理情况
                $pushData['acceptanceStatus'] = $secondorder->acceptanceCondition;
                //状态
                $pushData['status'] = '已受理';
            }else if($secondorder->status == 'solved' or $secondorder->status == 'delivered' or $secondorder->status == 'todelivered'){
                //文件
                $files = $this->loadModel('file')->getByObject('secondorderDeliver', $id);
                $processFileInfoList = $this->loadModel('common')->sendFileBySftp($files,'secondorder',$secondorder->code);
                if (dao::isError()) {
                    return false;
                }
                /*$processFileInfoList = array();
                foreach ($files as $file){
                    if($file->extension){
                        $tail = strlen($file->extension) + 1;
                    }
                    $realRemotePath = substr($fileIP.'/api.php?m=api&f=getfile&code=jinke1problem&time=1&token=1&filename='.$file->pathname, 0, -$tail); //实际存的附件没有后缀 需要去掉
                    $localRealFile =  $file->realPath; //实际存的附件
                    $md5 = md5_file($localRealFile);
                    array_push($processFileInfoList, array('url'=> $realRemotePath, 'md5'=> $md5, 'name' => $file->title));
                }*/
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
                $pushData['status'] = '已完成';
            }

            //请求类型
            $object = 'secondorder';
            $objectType = 'secondorderFeedback';
            $method = 'POST';

            $response = '';
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
            $requestClass->url = $url;
            $requestClass->object = $object;
            $requestClass->objectType = $objectType;
            $requestClass->method = $method;
            $requestClass->pushData = $pushData;
            $requestClass->response = $response;
            $requestClass->status = $status;
            $requestClass->extra = $extra;
            $requestClass->id = $id;
            if (empty($result) or $data->code != '200') {
                dao::$errors[] = $this->lang->secondorder->syncQzFail;
            }
        }else{
            dao::$errors[] = $this->lang->secondorder->enableFail;
        }
        return $requestClass;
    }

    public function pushFeedBackJx($id)
    {
        $this->loadModel('requestlog');
        //获取二线工单
        $secondorder = $this->dao->select("*")->from(TABLE_SECONDORDER)->where('id')->eq($id)->fetch();

        $pushEnable = $this->config->global->secondorderEnableJx;
        $requestClass = new stdClass();
        //判断是否开启发送反馈
        if ($pushEnable == 'enable') {
            $url = $this->config->global->secondorderFeedbackUrlJx;
            $pushAppId = $this->config->global->secondorderAppIdJx;
            $pushAppSecret = $this->config->global->secondorderAppSecretJx;
            $ts = time();
            $uuid = common::create_guid();
            $sign = md5('appId='.$pushAppId.'&nonce='.$uuid.'&ts='.$ts.'&appSecret='.$pushAppSecret);
            //请求头
            $headers = [
                'appId: ' . $pushAppId,
                'appSecret: ' . $pushAppSecret,
                'ts: ' . $ts,
                'nonce: ' . $uuid,
                'sign: ' . $sign,
            ];
            $users = $this->loadModel('user')->getPairs('noletter|noclosed');
            //数据体
            $pushData = [
                'processName' => '任务单',
                'idUnique' => $secondorder->externalCode,
                'nodeName' => '研效平台受理任务单',
                'isAgree'  => 1,
                'comment'  => '',
                'nodeDataMap' => [
                    'operator' => zget($users,$this->app->user->account,''),
                    'completeStatus' => '',
                ]
            ];

            //状态为未受理
            $resultFlag = true;
            if($secondorder->status == 'backed'){
                $pushData['comment'] = !empty($secondorder->notReceiveReason)?$secondorder->notReceiveReason:$secondorder->notAcceptReason;//未受理原因
                $pushData['isAgree'] = 0;//状态
            }else if($secondorder->status == 'delivered' || $secondorder->status == 'todelivered'){
                $result = $this->loadModel('sectransfer')->pushFileJx($secondorder);//上传文件
                $resultFlag = $result === true || (isset($result->code) && $result->code == 0);
                //完成情况
                if($secondorder->type == 'consult'){
                    $pushData['nodeDataMap']['completeStatus'] = html_entity_decode(strip_tags(str_replace("<br />","\n",strval(htmlspecialchars_decode($secondorder->consultRes,ENT_QUOTES)))));
                }else if($secondorder->type == 'test'){
                    $pushData['nodeDataMap']['completeStatus'] = html_entity_decode(strip_tags(str_replace("<br />","\n",strval(htmlspecialchars_decode($secondorder->testRes,ENT_QUOTES)))));
                }else{
                    $pushData['nodeDataMap']['completeStatus'] = html_entity_decode(strip_tags(str_replace("<br />","\n",strval(+htmlspecialchars_decode($secondorder->dealRes,ENT_QUOTES)))));
                }
            }
            //请求类型
            $object = 'secondorder';
            $objectType = 'secondorderFeedback';
            $method = 'POST';
            $status = 'fail';
            $extra = '';
            $result = $resultFlag ? $this->loadModel('requestlog')->http($url, $pushData, $method, 'json', array(), $headers) : json_encode(($result ?? []));
            if (!empty($result)) {
                $resultData = json_decode($result);
                if (isset($resultData->code) && $resultData->code == '0') {
                    $status = 'success';
                }
                $response = $result;
            } else {
                $response = '对方无响应';
            }
            $requestClass->url = $url;
            $requestClass->object = $object;
            $requestClass->objectType = $objectType;
            $requestClass->method = $method;
            $requestClass->pushData = $pushData;
            $requestClass->response = $response;
            $requestClass->status = $status;
            $requestClass->extra = $extra;
            $requestClass->id = $id;
            if (empty($result) or $resultData->code != '0') {
                dao::$errors[] = $this->lang->secondorder->syncQzFail;
            }
        }else{
            dao::$errors[] = $this->lang->secondorder->enableFail;
        }
        return $requestClass;
    }

    /**
     * 发送通用型服务反馈信息-未受理-实时推送
     * @param $externalCode
     * @return mixed
     */
    public function pushUniversalFeedback($id){
        $this->loadModel('requestlog');
        //获取二线工单
        $secondorder = $this->dao->select("*")->from(TABLE_SECONDORDER)->where('id')->eq($id)->fetch();

        $pushEnable = $this->config->global->secondorderEnable;
        $requestClass = new stdClass();
        //判断是否开启发送反馈
        if ($pushEnable == 'enable') {
            $url = $this->config->global->universalFeedbackUrl;
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
            $pushData['key']               = $secondorder->externalCode;
            //支持人员
            $users = $this->loadModel('user')->getPairs('noletter|noclosed');
            $depts   = $this->loadModel('dept')->getOptionMenu();
            $pushData['supportStaff'] = zget($users,$this->app->user->account,'');
            $pushData['timestamp'] = helper::now();

            //状态为未受理
            if($secondorder->status == 'backed'){
                //未受理原因
                $pushData['unacceptedReason'] = !empty($secondorder->notReceiveReason)?$secondorder->notReceiveReason:$secondorder->notAcceptReason;
                //状态
                $pushData['status']           = '未受理';
            }else if($secondorder->status == 'tosolve'){    //状态为已受理
                //受理情况
                $pushData['acceptanceStatus'] = $secondorder->acceptanceCondition;
                //状态
                $pushData['status'] = '已受理';
            }else if($secondorder->status == 'solved' or $secondorder->status == 'delivered' or $secondorder->status == 'todelivered'){
                //文件
                $files = $this->loadModel('file')->getByObject('secondorderDeliver', $id);
                $processFileInfoList = $this->loadModel('common')->sendFileBySftp($files,'secondorder',$secondorder->code);
                if (dao::isError()) {
                    return false;
                }
                $pushData['feedbackAttachment'] = $processFileInfoList;
                //完成情况
                $pushData['requestFeedback'] = html_entity_decode(strip_tags(str_replace("<br />","\n",strval(htmlspecialchars_decode($secondorder->consultRes,ENT_QUOTES)))));
                //状态
                $pushData['status'] = '已受理';
                //完成时间
                $pushData['finishAt'] = helper::now();
                //受理人
                $pushData['acceptor'] = zget($users, $secondorder->acceptUser, '');
                //受理部门
                $pushData['acceptorDept'] = zget($depts, $secondorder->acceptDept, '');
                //受理人联系方式
                $user = $this->loadModel('user')->getById($secondorder->acceptUser);
                $pushData['acceptorContact'] = $user->mobile;
            }

            //请求类型
            $object = 'secondorder';
            $objectType = 'secondorderFeedback';
            $method = 'POST';

            $response = '';
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
            $requestClass->url = $url;
            $requestClass->object = $object;
            $requestClass->objectType = $objectType;
            $requestClass->method = $method;
            $requestClass->pushData = $pushData;
            $requestClass->response = $response;
            $requestClass->status = $status;
            $requestClass->extra = $extra;
            $requestClass->id = $id;
            if (empty($result) or $data->code != '200') {
                dao::$errors[] = $this->lang->secondorder->syncQzFail;
            }
        }else{
            dao::$errors[] = $this->lang->secondorder->enableFail;
        }
        return $requestClass;
    }

    /**
     * 尝试报错 或需要rollback 保存请求日志
     */
    public function tryErrorRequest($rollBack = 0, $requestClass)
    {
        if (dao::isError()) {
            if ($rollBack == 1) {
                $this->dao->rollBack();
            }
            $response['result'] = 'fail';
            $response['message'] = dao::getError();
            //保存发送日志
            $this->loadModel('requestlog')->saveRequestLog($requestClass->url, $requestClass->object, $requestClass->objectType, $requestClass->method,
                $requestClass->pushData, $requestClass->response, $requestClass->status, $requestClass->extra, $requestClass->id);
            $this->send($response);
        }
    }

    /**
     * 尝试报错 或需要rollback
     */
    public function tryError($rollBack = 0)
    {
        if (dao::isError()) {
            if ($rollBack == 1) {
                $this->dao->rollBack();
            }
            $response['result'] = 'fail';
            $response['message'] = dao::getError();
            $this->send($response);
        }
    }

    /**
     * 直接输出data数据
     * @access public
     */
    private function send($data)
    {
        die(json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    public function getProtransferById($id)
    {
        return $this->dao
            ->select('id, protransferDesc')
            ->from(TABLE_SECTRANSFER)
            ->where('secondorderId')->eq($id)
            ->andWhere('deleted')->eq(0)
            ->fetchpairs();
    }

    /**
     * 定时任务-超过30天未收到结果就设置为关闭
     * @return void
     */
    public function changestatus(){
        $now = time();
        $secondorders = $this->dao->select('*')->from(TABLE_SECONDORDER)
            ->where('deleted')->ne('1')
            ->andWhere('status')->eq('delivered')
            ->fetchAll('id');
        foreach ($secondorders as $scondorder){
            $flag = false;
            if(!empty($scondorder->pushDate)){
                if('guestjx' == $scondorder->createdBy){
                    $deadline = $this->lang->secondorder->noFeedBackCloseDate['JX'] ?? 10;
                    $deadline = strtotime(helper::getWorkDay($scondorder->pushDate, $deadline));
                }else if($scondorder->createdBy == 'guestcn'){
                    $deadline = $this->lang->secondorder->noFeedBackCloseDate['QZ'] ?? 30;
                    $deadline = strtotime($scondorder->pushDate) + ($deadline * 86400);
                }
                $flag = $now > $deadline;
            }
//            if(!empty($scondorder->pushDate) and floor((strtotime(helper::now()) - strtotime($scondorder->pushDate))/86400) > 30){
            if($flag){
                $this->dao->update(TABLE_SECONDORDER)
                    ->set('status')->eq('closed')
                    ->set('closedBy')->eq('guestjk')
                    ->set('closedDate')->eq(helper::now())
                    ->set('closeReason')->eq($this->lang->secondorder->syncstatusbycrontab)
                    ->set('dealUser')->eq('')
                    ->where('id')->eq($scondorder->id)
                    ->exec();
                $this->loadModel('action')->create('secondorder',$scondorder->id, 'syncstatusbycrontab');
                $this->loadModel('consumed')->record('secondorder', $scondorder->id, 0, 'guestjk', $scondorder->status, 'closed', array(), '');
            }else if(empty($scondorder->externalCode) && $scondorder->finallyHandOver == '1'){ //是否最终移交为是
                $sectransfer = $this->dao->select('*')->from(TABLE_SECTRANSFER)
                    ->where('secondorderId')->eq($scondorder->id)
                    ->andWhere('deleted')->eq(0)
                    ->fetch();
                if($sectransfer->status == 'alreadyEdliver'){
                    $node = $this->dao->select('*')->from(TABLE_REVIEWNODE)
                        ->where('objectType')->eq('sectransfer')
                        ->andWhere('objectID')->eq($sectransfer->id)
                        ->andWhere('version')->eq($sectransfer->version)
                        ->andWhere('stage')->eq('5')
                        ->orderBy('stage,id')->fetch();
                    $reviewers = $this->dao->select('*')->from(TABLE_REVIEWER)->where('node')->eq($node->id)->fetchAll();
                    foreach ($reviewers as $reviewer){
                        if($reviewer->status == 'pass'){
                            if(floor((strtotime(helper::now()) - strtotime($reviewer->reviewTime))/86400) > 30){
                                $this->dao->update(TABLE_SECONDORDER)
                                    ->set('status')->eq('closed')
                                    ->set('closedBy')->eq('guestjk')
                                    ->set('closedDate')->eq(helper::now())
                                    ->set('closeReason')->eq($this->lang->secondorder->syncstatusinnerbycrontab)
                                    ->set('dealUser')->eq('')
                                    ->where('id')->eq($scondorder->id)
                                    ->exec();
                                $this->loadModel('action')->create('secondorder',$scondorder->id, 'syncstatusinnerbycrontab');
                                $this->loadModel('consumed')->record('secondorder', $scondorder->id, 0, 'guestjk', $scondorder->status, 'closed', array(), '');
                                break;
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * qA追加工作进展
     * @param $problemID
     * @return array
     */
    public function editSpecialQA($secondorderID)
    {
        $oldSecondorder = $this->getByID($secondorderID);
        $secondorder = fixer::input('post')
            ->remove('uid')
            //->stripTags($this->config->secondorder->editor->editspecial['id'], $this->config->allowedTags)
            ->get();

//        if(empty($secondorder->progressQA)){
//            return dao::$errors[] = $this->lang->secondorder->progressError;
//        }

        $this->dao->update(TABLE_SECONDORDER)
            ->data($secondorder)
            ->where('id')->eq($secondorderID)
            ->exec();

        return common::createChanges($oldSecondorder, $secondorder);
    }

    /**
     * 获得工单名称列表
     *
     * @param string $exWhere
     * @return array
     */
    public function getNameList($exWhere = ''){
        $data = [];
        $ret = $this->dao->select('id,concat(concat(concat(code,"（"),summary),"）")')
            ->from(TABLE_SECONDORDER)
            ->where('deleted')->eq('0')
            ->beginIF($exWhere)->andWhere($exWhere)->fi()
            ->orderBy('id_desc')
            ->fetchPairs();
        if($ret){
            $data = $ret;
        }
        return $data;
    }

    /**
     * 获得列表
     *
     * @param $secondorderIds
     * @param string $select
     * @return array
     */
    public function getListByIds($secondorderIds, $select = '*'){
        $data = [];
        if(!($secondorderIds)){
            return $data;
        }
        $ret = $this->dao->select($select)
            ->from(TABLE_SECONDORDER)
            ->where('deleted')->eq('0')
            ->andWhere('id')->in($secondorderIds)
            ->fetchAll('id');
        if($ret){
            $data = $ret;
        }
        return $data;
    }

    /**
     *获得被对外移交使用的工单ids
     *
     * @param $secondorderIds
     * @param string $objectType
     * @param int $ignoreObjectID
     * @return array
     */
    public function getUsedSecondorderIdsBySectransfer($secondorderIds, $objectType, $ignoreObjectID = 0){
        $data = [];
        if(!$secondorderIds){
            return $data;
        }
        $ret = $this->dao->select('secondorderId')
            ->from(TABLE_SECTRANSFER)
            ->Where('deleted')->eq('0')
            ->andWhere('secondorderId')->in($secondorderIds)
            ->beginIF($ignoreObjectID && $objectType == 'sectransfer')->andWhere('id')->ne($ignoreObjectID)->fi()
            ->fetchAll();
        if($ret){
            $data = array_column($ret, 'secondorderId');
        }
        return $data;
    }

    /**
     * 获得被征信交付使用的工单ids
     *
     * @param $secondorderIds
     * @param string $objectType
     * @param int $ignoreObjectID
     * @return array
     */
    public function getUsedSecondorderIdsByCredit($secondorderIds, $objectType, $ignoreObjectID = 0){
        $data = [];
        if(!$secondorderIds){
            return $data;
        }
        $this->app->loadLang('credit');
        $ret = $this->dao->select('t1.relationID')
            ->from(TABLE_SECONDLINE)->alias('t1')
            ->leftJoin(TABLE_CREDIT)->alias('t2')->on('t1.objectID=t2.id and t1.objectType = "credit"')
            ->Where('t1.relationType')->eq('secondorder')
            ->andWhere('t1.relationID')->in($secondorderIds)
            ->andWhere('t1.objectType')->eq('credit')
            ->andWhere('t1.deleted')->eq('0')
            ->andWhere('t2.deleted')->eq('0')
            ->andWhere('t2.status')->notin('waitsubmit,cancel')
            ->andWhere('t2.abnormalId')->eq(0)
            ->andWhere('t2.secondorderCancelLinkage')->eq(0)
            ->beginIF($ignoreObjectID && $objectType == 'credit')
            ->andWhere('t1.objectID')->ne($ignoreObjectID)
            ->andWhere('t2.id')->ne($ignoreObjectID)
            ->fi()
            ->fetchAll();
        if($ret){
            $data = array_column($ret, 'relationID');
        }
        return $data;
    }


    /**
     *  获得被占用的二线工单
     *
     * @param $secondorderIds
     * @param $objectType
     * @param int $ignoreObjectID
     * @return array
     */
    public function getUsedListByIds($secondorderIds, $objectType, $ignoreObjectID = 0){
        $data = [];
        if(!($secondorderIds && $objectType)){
            return $data;
        }
        //查询被对外移交占用的工单
        $userdIdsBySectransfer = $this->getUsedSecondorderIdsBySectransfer($secondorderIds, $objectType, $ignoreObjectID);

        //查询被征信交付对外占用的
        $userdIdsByCredit = $this->getUsedSecondorderIdsByCredit($secondorderIds, $objectType, $ignoreObjectID);
        if($objectType == 'sectransfer'){ //本次是对外移交
            if(!empty($userdIdsByCredit)){ //被征信交付使用的
                $this->app->loadLang('credit');
                $endStatusArray = array_keys($this->lang->credit->endStatusList);
                foreach ($userdIdsByCredit as $tempKey => $relateId){ //查询再对外交付中的关联状态
                    $creditList = $this->loadModel('credit')->getCreditListByRelatedId('secondorder', $relateId, 'status');
                    if($creditList){
                        $creditStatusArray = array_column($creditList, 'status');
                            $creditStatusArray = array_unique($creditStatusArray);
                            $diffStatus = array_diff($creditStatusArray, $endStatusArray);

                            if(empty($diffStatus)){ //只有上线成功和上线异常
                                unset($userdIdsByCredit[$tempKey]); //去除
                            }
                    }
                }
                sort($userdIdsByCredit);
            }
        }


        $data = array_flip(array_flip(array_merge($userdIdsBySectransfer, $userdIdsByCredit)));
        return $data;
    }

    /**
     * 检查工单id是否可以被使用
     *
     * @param $secondorderIds
     * @param string $objectType
     * @param int $ignoreObjectID
     * @return array
     */
    public function checkSecondorderIdsIsAllowUse($secondorderIds, $objectType = '', $ignoreObjectID = 0){
        $checkRes = false;
        $errorData = [];
        $data = [
            'checkRes'  => $checkRes,
            'errorData' => $errorData,
        ];
        if(!$secondorderIds){
            return $data;
        }

        //工单列表
        $secondorderList = $this->getListByIds($secondorderIds, 'id,code,summary,type,status,formType,ifAccept');

        //被占用的二线工单列表
        $usedSecondorderIds = $this->getUsedListByIds($secondorderIds, $objectType, $ignoreObjectID);

        foreach ($secondorderIds as $secondorderId){
            if(!isset($secondorderList[$secondorderId])){
                $errorData[] = $this->lang->secondorder->idEmpty;
            }
            $secondorderInfo = $secondorderList[$secondorderId];
            $code = $secondorderInfo->code;
            $formType = $secondorderInfo->formType; //内部工单还是外部工单
            $status = $secondorderInfo->status;
            if($formType == 'external'){ //外部工单
                if($usedSecondorderIds && in_array($secondorderId, $usedSecondorderIds) ){
                    $errorData[] = sprintf($this->lang->secondorder->idUsedError, $code);
                }
                if(in_array($status, $this->lang->secondorder->unDoneStatusArray )){
                    $errorData[] = sprintf($this->lang->secondorder->unDoneError, $code);
                }
               /* if(in_array($status, $this->lang->secondorder->deliveredStatusArray )){
                    $errorData[] = sprintf($this->lang->secondorder->deliveredError, $code);
                }*/
                if($status ==  'closed'){
                    if(!$secondorderInfo->ifAccept){
                        $errorData[] = sprintf($this->lang->secondorder->acceptError, $code);
                    }else{
                        $errorData[] = sprintf($this->lang->secondorder->closedError, $code);
                    }
                }

            }else{ //内部工单
                if(in_array($status, $this->lang->secondorder->unDoneStatusArray )){
                    $errorData[] = sprintf($this->lang->secondorder->unDoneError, $code);
                }
            }
        }
        //返回
        if(empty($errorData)){
            $checkRes = true;
        }
        $data['checkRes']  = $checkRes;
        $data['errorData'] = $errorData;
        return $data;
    }

    /**
     * 检查工单id是否可以被联动
     *
     * @param $secondorderIds
     * @param string $objectType
     * @param int $ignoreObjectID
     * @return array
     */
    public function checkSecondorderIdsIsAllowLink($secondorderIds, $objectType = '', $ignoreObjectID = 0){
        $checkRes = false;
        $errorData = [];
        $data = [
            'checkRes'  => $checkRes,
            'errorData' => $errorData,
        ];
        if(!$secondorderIds){
            return $data;
        }

        //工单列表
        $secondorderList = $this->getListByIds($secondorderIds, 'id,code,summary,type,status,formType,ifAccept');

        //被占用的二线工单列表
        $usedSecondorderIds = $this->getUsedListByIds($secondorderIds,  $objectType, $ignoreObjectID);

        foreach ($secondorderIds as $secondorderId){
            if(!isset($secondorderList[$secondorderId])){
                $errorData[] = $this->lang->secondorder->idEmpty;
            }
            $secondorderInfo = $secondorderList[$secondorderId];
            $code = $secondorderInfo->code;
            $formType = $secondorderInfo->formType; //内部工单还是外部工单
            $status = $secondorderInfo->status;
            if($formType == 'external'){ //外部工单
                if($usedSecondorderIds && in_array($secondorderId, $usedSecondorderIds) ){
                    $errorData[] = sprintf($this->lang->secondorder->idUsedError, $code);
                }
                if(in_array($status, $this->lang->secondorder->unDoneStatusArray )){
                    $errorData[] = sprintf($this->lang->secondorder->unDoneError, $code);
                }
                if($status ==  'closed'){
                    if(!$secondorderInfo->ifAccept){
                        $errorData[] = sprintf($this->lang->secondorder->acceptError, $code);
                    }else{
                        $errorData[] = sprintf($this->lang->secondorder->closedError, $code);
                    }
                }

            }else{ //内部工单
                if(in_array($status, $this->lang->secondorder->unDoneStatusArray )){
                    $errorData[] = sprintf($this->lang->secondorder->unDoneError, $code);
                }

            }
        }
        //返回
        if(empty($errorData)){
            $checkRes = true;
        }
        $data['checkRes']  = $checkRes;
        $data['errorData'] = $errorData;
        return $data;
    }

    /**
     *获得被生产变更占用的需求条目ids
     *
     * @param string $type
     * @param int $id
     * @param array $demandIds
     * @return array
     */
    public function getUsedDemandIdsByModify($type = '', $id = 0, $demandIds = []){
        $data = [];
        $ret = $this->dao->select('distinct(t1.relationID)')
            ->from(TABLE_SECONDLINE)->alias('t1')
            ->leftJoin(TABLE_MODIFY)->alias('t2')->on('t1.objectID=t2.id and t1.objectType = "modify"')
            ->Where('t1.relationType')->eq('demand')
            ->beginIF($demandIds)
            ->andWhere('t1.relationID')->in($demandIds)
            ->fi()
            ->andWhere('t1.objectType')->eq('modify')
            ->andWhere('t1.deleted')->eq('0')
            ->andWhere('t2.status')->notin(['modifysuccesspart', 'modifyerror', 'modifyrollback', 'modifyfail', 'modifycancel','deleted','cancel'] )
            ->beginIF($type == 'modify' && $id > 0)->andWhere('id')->ne($id)->fi()
            ->andWhere('t1.objectID')->ne($id)
            ->andWhere('t2.id')->ne($id)
            ->fi()
            ->fetchAll();
        if($ret){
            $data = array_column($ret, 'relationID');
        }
        return $data;
    }

    /**
     *获得被清总对外交付占用的需求条目ids
     *
     * @param string $type
     * @param int $id
     * @param array $demandIds
     * @return array
     */
    public function getUsedDemandIdsByOutwardDelivery($type = '', $id = 0, $demandIds = []){
        $data = [];
        $this->app->loadLang('demand');
        $ret = $this->dao->select('distinct(t1.relationID)')
            ->from(TABLE_SECONDLINE)->alias('t1')
            ->leftJoin(TABLE_OUTWARDDELIVERY)->alias('t2')->on('t1.objectID=t2.id and t1.objectType = "outwarddelivery"')
            ->Where('t1.relationType')->eq('demand')
            ->beginIF($demandIds)
            ->andWhere('t1.relationID')->in($demandIds)
            ->fi()
            ->andWhere('t1.objectType')->eq('outwarddelivery')
            ->andWhere('t1.deleted')->eq('0')
            ->andWhere('t2.deleted')->eq('0')
            ->andWhere('t2.status')->notin(['modifysuccesspart','modifyfail','modifycancel','deleted','cancel'] )
            ->beginIF(in_array($type, $this->lang->demand->mutexIsNewModifycnccModules))
            ->andWhere('t2.isNewModifycncc')->eq('1')
            ->fi()
            ->beginIF($type == 'outwarddelivery' && $id > 0)->andWhere('id')->ne($id)->fi()
            ->andWhere('t1.objectID')->ne($id)
            ->andWhere('t2.id')->ne($id)
            ->fi()
            ->fetchAll();
        if($ret){
            $data = array_column($ret, 'relationID');
        }
        return $data;
    }

    /**
     * 获得被征信交付使用的需求条目ids
     *
     * @param string $type
     * @param int $id
     * @param array $demandIds
     * @return array
     */
    public function getUsedDemandIdsByCredit($type = '', $id = 0, $demandIds = []){
        $data = [];
        $this->app->loadLang('credit');
        $ret = $this->dao->select('distinct(t1.relationID)')
            ->from(TABLE_SECONDLINE)->alias('t1')
            ->leftJoin(TABLE_CREDIT)->alias('t2')->on('t1.objectID=t2.id and t1.objectType = "credit"')
            ->Where('t1.relationType')->eq('demand')
            ->beginIF($demandIds)
            ->andWhere('t1.relationID')->in($demandIds)
            ->fi()
            ->andWhere('t1.objectType')->eq('credit')
            ->andWhere('t1.deleted')->eq('0')
            ->andWhere('t2.deleted')->eq('0')
            ->andWhere('t2.status')->notin($this->lang->credit->reissueStatusArray)
            ->beginIF($type == 'credit' && $id > 0)
            ->andWhere('t1.objectID')->ne($id)
            ->andWhere('t2.id')->ne($id)
            ->fi()
            ->fetchAll();
        if($ret){
            $data = array_column($ret, 'relationID');
        }
        return $data;
    }

    /**
     *
     * 获得工单联动状态
     *
     * @param $secondorderInfo
     * @param $objectType
     * @param $objectInfoStatus
     * @param $objectInfoCode
     * @param $secondorderStatus //同步到指定状态用于编辑或者取消
     * @return array
     */
    public function getSecondorderLinkageStatusInfo($secondorderInfo, $objectType, $objectInfoStatus, $objectInfoCode, $secondorderStatus = ''){
        $linkageStatus = '';
        $res = [
            'linkageStatus' => $linkageStatus,
            'objectType' => $objectType,
            'objectInfoCode' => $objectInfoCode,
        ];

        $formType = $secondorderInfo->formType;
        if($formType == 'external'){ //外部工单
            if($secondorderStatus){
                $linkageStatus = $secondorderStatus;
            }else{
                $linkageStatus = $this->getLinkageStatus($objectType, $objectInfoStatus);
            }
            $res['linkageStatus'] = $linkageStatus;

        }else{ //内部工单
            //获取最小联动状态
            $secondorderId = $secondorderInfo->id;
            $res = $this->getSecondorderLinkageMinStatusInfo($secondorderId, $objectType, $objectInfoCode, $secondorderStatus);
        }
        return $res;
    }


    /**
     * 获得工单联动的最小状态
     *
     * @param $secondorderId
     * @param $linkObjectType
     * @param $linkObjectCode
     * @param $secondorderStatus
     * @return array
     */
    public function getSecondorderLinkageMinStatusInfo($secondorderId, $linkObjectType, $linkObjectCode, $secondorderStatus = ''){
        $linkageStatus = '';
        $res = [
            'linkageStatus'  => $linkageStatus,
            'objectType'      => '',
            'objectInfoCode' => '',
        ];
        if(!$secondorderId){
            return $res;
        }
        $relationTypes = $this->lang->secondorder->syncStatusObjectTypes;
        $relationList = $this->loadModel('secondline')->getRelationList('secondorder', $secondorderId, $relationTypes);
        //没有需要状态联动的数据
        $isHasLink = false;

        $creditIds = [];
        //直接从对外移交表中查询
        $sectransferIds = [];
        //区分数据
        if(!empty($relationList)){
            foreach ($relationList as $val){
                $relationType = $val->relationType; //关联的类型
                $relationID = $val->relationID;
                if($relationType == 'sectransfer'){
                    $sectransferIds[] = $relationID;
                }elseif ($relationType == 'credit'){
                    $creditIds[] = $relationID;
                }
            }
        }

        //按照从小到大数据联动
        $linkageStatusArray = array_keys($this->lang->secondorder->linkageStatusList['credit']);

        //初始化数组
        foreach ($linkageStatusArray as $linkageStatus){
            $currentListName = $linkageStatus.'List'; //数组名称
            $$currentListName = []; //数组名称引用变量
        }
        $sectransferList = [];
        $creditList      = [];

        if(!empty($sectransferIds)){ //对外移交
            $sectransferList = $this->loadModel('sectransfer')->getTakeLinkStatusChangeSectransferList($sectransferIds, 'secondorder');
        }else{
            $select = 'status, concat(concat(id,"_"),protransferDesc) as code';
            $sectransferList = $this->loadModel('sectransfer')->getListBySecondorderId($secondorderId, $select);
        }
        if(!empty($sectransferList)){
            $currentObjectType = 'sectransfer';
            foreach ($sectransferList as $val){
                $status = $val->status;
                $code   = $val->code;
                $linkageStatus = $this->getLinkageStatus($currentObjectType, $status);
                if($linkageStatus){
                    $currentListName = $linkageStatus.'List'; //数组名称
                    if(!isset($$currentListName[$currentObjectType])){
                        $$currentListName[$currentObjectType] = [];
                    }
                    $$currentListName[$currentObjectType][] = $code;
                }
            }
        }
        //征信交付列表
        if(isset($creditIds) && !empty($creditIds)){
            $creditList = $this->loadModel('credit')->getTakeLinkStatusChangeCreditList($creditIds, 'secondorder');
        }
        if(!empty($creditList)){
            $currentObjectType = 'credit';
            foreach ($creditList as $val){
                $status = $val->status;
                $code   = $val->code;
                $linkageStatus = $this->getLinkageStatus($currentObjectType, $status);
                if($linkageStatus){
                    $currentListName = $linkageStatus.'List'; //数组名称
                    if(!isset($$currentListName[$currentObjectType])){
                        $$currentListName[$currentObjectType] = [];
                    }
                    $$currentListName[$currentObjectType][] = $code;

                }
            }
        }
        foreach ($linkageStatusArray as $linkageStatus){
            $currentListName = $linkageStatus.'List'; //数组名称
            if(isset($$currentListName) && !empty($$currentListName)){ //数组不为空
                $isHasLink = true; //有状态联动
                $objectTypeList = array_keys($$currentListName);
                $objectType = $objectTypeList[0];
                $objectTypeData = $$currentListName[$objectType];
                $objectInfoCode = $objectTypeData[0];
                //返回数据
                $res['linkageStatus']  = $linkageStatus;
                $res['objectType']     =  $objectType;
                $res['objectInfoCode'] = $objectInfoCode;
                break;
            }
        }

        if((!$isHasLink) && $secondorderStatus){
            //返回数据
            $res['linkageStatus']  = $secondorderStatus;
            $res['objectType']     =  $linkObjectType;
            $res['objectInfoCode'] = $linkObjectCode;
        }
        return $res;
    }

    /**
     * 反查获得联动状态
     *
     * @param $objectType
     * @param $status
     * @return int|string
     */
    public function getLinkageStatus($objectType, $status){
        $linkageStatus = '';
        if(!($objectType && $status)){
            return $linkageStatus;
        }
        $currentObjectLinkageStatusList = zget($this->lang->secondorder->linkageStatusList, $objectType, []);
        if(empty($currentObjectLinkageStatusList)){
            return $linkageStatus;
        }
        foreach ($currentObjectLinkageStatusList as $key => $statusList){
            if(in_array($status, $statusList)){
                $linkageStatus = $key;
                break;
            }
        }
        return $linkageStatus;
    }

    /**
     * 同步工单状态
     *
     * @param $secondorderId
     * @param $objectType
     * @param $objectId
     * @param $objectInfoStatus
     * @param $objectInfoCode
     * @param $secondorderStatus 指定工单状态
     * @return bool
     */
    public function syncSecondorderStatus($secondorderId, $objectType, $objectId, $objectInfoStatus, $objectInfoCode, $secondorderStatus = ''){
        if(!($secondorderId && $objectId && $objectType  && $objectInfoCode)){
            return false;
        }
        if(!in_array($objectType, $this->lang->secondorder->syncStatusObjectTypes)){
            return false;
        }
        //是否被关联，被关联也不允许修改
        $secondorderIds = [$secondorderId];
        $secondorderCheckRes = $this->checkSecondorderIdsIsAllowLink($secondorderIds, $objectType, $objectId);
        if(!$secondorderCheckRes['checkRes']){
            return false; //被其他模块占用不允许修改
        }

        $secondorderInfo = $this->getBasicInfoById($secondorderId);
        if(!$secondorderInfo){
            return false;
        }

        //获得工单联动状态
        $res = $this->getSecondorderLinkageStatusInfo($secondorderInfo, $objectType, $objectInfoStatus, $objectInfoCode, $secondorderStatus);
        if(!$res['linkageStatus']){
            return false;
        }
        $secondorderStatus = $res['linkageStatus'];
        $objectType        = $res['objectType'];
        $objectInfoCode    = $res['objectInfoCode'];

        $oldStatus = $secondorderInfo->status;
        if($oldStatus == $secondorderStatus){
            return true; //状态相同无需修改
        }

        //是否允许同步状态
        $isAllowSync = false;
        if($oldStatus == 'closed'){ //原来状态关闭不需要关联
            return true;
        }

        if($secondorderStatus == 'todelivered'){ //修改成待交付
//            if($oldStatus != 'onlinesuccess'){
//                $isAllowSync = true;
//            }
            $isAllowSync = true;
        } elseif($secondorderStatus == 'indelivery'){ //同步到 交付审批中
//            if($oldStatus == 'todelivered' || $oldStatus == 'exception'){ //只有待交付、上线异常的单子才允许同步到交付审批中
//                $isAllowSync = true;
//            }elseif ($oldStatus == 'delivered'){ //原来状态是已交付
//                if($objectInfoStatus == 'reject'){ //当前操作是变更退回
//                    $isAllowSync = true;
//                }
//            }
            $isAllowSync = true;
        }elseif ($secondorderStatus == 'delivered'){ //同步到 已交付
//            if($oldStatus == 'indelivery'){ //只有交付审批中的单子才允许同步到已交付
//                $isAllowSync = true;
//            }
            $isAllowSync = true;
        } elseif ($secondorderStatus == 'onlinesuccess'){ //同步到 上线成功
//            if($oldStatus == 'delivered'){ //只有已交付的单子才会同步到上线成功
//                $isAllowSync = true;
//            }
            $isAllowSync = true;
        } elseif ($secondorderStatus == 'exception'){ //同步到 上线异常
//            if($oldStatus == 'delivered'){ //只有已交付的单子才会同步到上线 上线异常
//                $isAllowSync = true;
//            }
            $isAllowSync = true;
        }

        if(!$isAllowSync){
            return false; //当前状态不允许同步
        }
        $currentAccount = $this->app->user->account;
        $dealUser = $this->getDealUserByStatus($secondorderInfo, $secondorderStatus);
        //变更状态
        $updateParams = new stdClass();
        $updateParams->status = $secondorderStatus;
        $updateParams->dealUser = $dealUser;

        if( $secondorderStatus == 'delivered'){ //已交付
                $updateParams->pushDate = helper::now();
        }
        $this->dao->update(TABLE_SECONDORDER)->data($updateParams)
            ->where('id')->eq($secondorderId)
            ->exec();
        //状态流转
        $this->loadModel('consumed')->record('secondorder', $secondorderId, '0', $currentAccount, $oldStatus, $secondorderStatus, array());
        //日志
        $changes = common::createChanges($secondorderInfo, $updateParams);
        $this->app->loadLang($objectType);
        $opObjectType = 'syncstatusby'. $objectType;
        $comment = isset($this->lang->$objectType->code) ? $this->lang->$objectType->code . $objectInfoCode : $this->lang->$objectType->id. $objectInfoCode;
        if($objectType == 'sectransfer' && $secondorderStatus == 'todelivered'){
            $comment = $comment.$this->lang->sectransfer->deleteStatusTip ;
        }
        $actionID = $this->loadModel('action')->create('secondorder', $secondorderId, $opObjectType, $comment);
        if($changes){
            $this->loadModel('action')->logHistory($actionID, $changes);
        }
        return true;
    }

    /**
     * 修改到指定状态
     *
     * @param $secondorderId
     * @param $status
     * @param $objectType
     * @param $objectId
     * @param $objectInfoCode
     * @return bool
     */
    public function editSecondorderStatus($secondorderId, $status, $objectType, $objectId, $objectInfoCode){
        if(!($secondorderId && $status && $objectType  && $objectInfoCode)){
            return false;
        }
        //是否被关联，被关联也不允许修改
        $secondorderIds = [$secondorderId];
        $secondorderCheckRes = $this->checkSecondorderIdsIsAllowUse($secondorderIds, $objectType, $objectId);
        if(!$secondorderCheckRes['checkRes']){
            return false; //被其他模块占用不允许修改
        }
        
        $secondorderInfo = $this->getBasicInfoById($secondorderId);
        if(!$secondorderInfo){
            return false;
        }
        $oldStatus = $secondorderInfo->status;
        if($oldStatus == $status){
            return true; //状态相同无需修改
        }


        //不允许修改
        $isEditStatus = false;
        if($status == 'todelivered'){ //修改成待交付
            if($oldStatus == 'indelivery' || $oldStatus == 'delivered'){
                $isEditStatus = true;
            }
        }
        if(!$isEditStatus){ //不允许修改退出
            return false;
        }
        $currentAccount = $this->app->user->account;
        $dealUser = $this->getDealUserByStatus($secondorderInfo, $status);
        //变更状态
        $updateParams = new stdClass();
        $updateParams->status = $status;
        $updateParams->dealUser = $dealUser;

        if( $status == 'delivered'){ //已交付
            $updateParams->pushDate = helper::now();
        }
        $this->dao->update(TABLE_SECONDORDER)->data($updateParams)
            ->where('id')->eq($secondorderId)
            ->exec();
        //状态流转
        $this->loadModel('consumed')->record('secondorder', $secondorderId, '0', $currentAccount, $oldStatus, $status, array());
        //日志
        $changes = common::createChanges($secondorderInfo, $updateParams);
        $this->app->loadLang($objectType);
        $opObjectType = 'cancelsyncstatusby'.$objectType;
        $comment = $this->lang->$objectType->code . $objectInfoCode;
        $actionID = $this->loadModel('action')->create('secondorder', $secondorderId, $opObjectType, $comment);
        if($changes){
            $this->loadModel('action')->logHistory($actionID, $changes);
        }
        return true;
    }


    /**
     * 根据装填获取待处理人
     *
     * @param $secondorderInfo
     * @param $status
     * @return string
     */
    public function getDealUserByStatus($secondorderInfo, $status){
        $dealUser = '';
        switch ($status){
            case 'todelivered': //待交付
                $dealUser = $secondorderInfo->acceptUser;
                break;

            case 'indelivery': //交付审批中
                $dealUser = '';
                break;

            case 'delivered': //已交付
                $dealUser = '';
                break;

            case 'onlinesuccess': //上线成功
                $dealUser = '';
                break;

            case 'exception':  //上线异常
                $dealUser = '';
                break;

            default:
                break;
        }
        return $dealUser;
    }

    /**
     * 编辑是否最终对外移交
     * @param $secondorderID
     * @return array
     */
    public function editFinallyHandOver($secondorderID)
    {

        $oldsecondorder = $this->getByID($secondorderID);
        $secondorder = fixer::input('post')
            ->remove('uid,comment')
            ->striptags($this->config->secondorder->editor->editfinallyhandover['id'], $this->config->allowedTags)
            ->get();

        $this->dao->update(TABLE_SECONDORDER)
            ->data($secondorder)
            ->autoCheck()->batchCheck($this->config->secondorder->editfinallyhandover->requiredFields ,'notempty')
            ->where('id')->eq($secondorderID)
            ->exec();

        return common::createChanges($oldsecondorder, $secondorder);
    }

    /**
     *
     * 根据对外移交更新任务工单状态
     * @param $secondOrderID
     * @param $sectransferID
     */
    public function syncSectransferToSecondStatus($secondOrderID, $sectransferID){

        /**
         * 1、新建 待交付 -> 交付审批中（todelivered -> indelivery）                        对外移交状态 （waitApply）
         * 2、编辑 绑定新的工单
         *        1、原旧工单 交付审批中 -> 待交付 （indelivery -> todelivered）            对外移交状态 （waitApply，approveReject）
         *        2、关联新的 待交付 ->交付审批中 （todelivered -> indelivery）
         *        3、工单不变 状态不变
         *        4、编辑三种情况：项目移交 -> 工单移交 、工单移交-> 项目移交  、工单移交
         * 3、二线审批 交付审批中-> 已交付                                                  对外移交状态 （waitDeliver)
         * 4、退回     已交付 -> 交付审批中                                                 对外移交状态 （externalReject)
         */
       /* 'sectransfer' => [
            'todelivered' => ['approveReject','externalReject'],
            'indelivery  '=> ['waitApply'],
            'delivered'   => ['waitDeliver'],
        ],*/
       //状态从小到大  待交付 -> 交付审批中 -> 已交付

        $statusList = $this->lang->secondorder->linkageStatusList['sectransfer'];
        $statusValueList = array_merge($statusList['todelivered'],$statusList['indelivery'],$statusList['delivered']);//状态列表
        //查询工单
        $second = $this->loadModel('secondorder')->getByID($secondOrderID);
        $oldStatus = $second->status;
        //查询对外移交
        $sectransfer = $this->dao->select('id,status')->from(TABLE_SECTRANSFER)
            ->where('deleted')->eq('0')
            ->andWhere('secondorderId')->eq($secondOrderID)
            ->fetchAll();
        $sectransferStatus = isset($sectransfer) ? array_flip(array_unique(array_column($sectransfer,'status','id'))) :'';//所有对外移交当前状态
        $sectransferNewStatus = array();
        $endstatus = '';
        $type = '';
        if($sectransferStatus){
            //获取当前状态在配置状态列表中的位置
            foreach ($statusValueList as $key => $item) {
                if(isset($sectransferStatus[$item])){
                    $sectransferNewStatus[$key] = $item;
                }
            }
            if(!$sectransferNewStatus){ //对外移交的状态 未处于状态联动中，不联动
                return true;
            }
            ksort($sectransferNewStatus);//排序
            $minStatus = array_shift($sectransferNewStatus);//获取第一个value,(在状态列表最小的状态)
            //最小状态在配置中对应的key ,工单状态
            foreach ($statusList as $key => $status) {
                if(in_array($minStatus,$status)){
                    $endStatus = $key;
                }
            }
            if($oldStatus == $endStatus){ //状态一致
                return true;
            }
            if($oldStatus == 'todelivered' && in_array($minStatus,$statusList['indelivery'])){ //工单状态待交付
                $endstatus = 'indelivery';
                $sectransferID = $sectransferStatus[$minStatus];
            }
            else if($oldStatus == 'indelivery' && in_array($minStatus,$statusList['delivered'])){ //工单状态交付审批中
                $endstatus = 'delivered';
                $sectransferID = $sectransferStatus[$minStatus];
            }
            else if($oldStatus == 'delivered' && in_array($minStatus,$statusList['indelivery'])){ //工单状态已交付
                $endstatus = 'indelivery';
                $sectransferID = $sectransferStatus[$minStatus];
            }
            $type = 'syncstatusbyprotransfer';
        }else{
            $endstatus = 'todelivered'; //工单没有关联的对外移交
            if($oldStatus == $endstatus){
                return true;
            }
            $type = 'cancelsyncstatusbyprotransfer';
        }
        $currentAccount = $this->app->user->account;
        $dealUser = $this->getDealUserByStatus($second, $endstatus);
        //变更状态
        $updateParams = new stdClass();
        $updateParams->status = $endstatus;
        $updateParams->dealUser = $dealUser;

        if( $endstatus == 'delivered'){ //已交付
            $updateParams->pushDate = helper::now();
        }
        $this->dao->update(TABLE_SECONDORDER)->data($updateParams)
            ->where('id')->eq($secondOrderID)
            ->exec();
        //状态流转
        $this->loadModel('consumed')->record('secondorder', $secondOrderID, '0', $currentAccount, $oldStatus, $endstatus, array());
        //日志
        $changes = common::createChanges($second, $updateParams);

        $comment = sprintf($this->lang->secondorder->sectrabsferToSecond  ,$sectransferID,zget($this->lang->secondorder->statusList,$oldStatus,''),zget($this->lang->secondorder->statusList,$endstatus,''))  ;
        $actionID = $this->loadModel('action')->create('secondorder', $secondOrderID, $type, $comment);
        if($changes){
            $this->loadModel('action')->logHistory($actionID, $changes);
        }
    }

    /**
     * 编辑对外移交 联动工单状态
     * @param $secondOrderID
     * @param $sectransferID
     * @param $oldSectransfer
     * @return bool
     */
    public function syncEditSectransferToSecondStatus($secondOrderID, $sectransferID, $oldSectransfer,$del = null){
        $statusList = $this->lang->secondorder->linkageStatusList['sectransfer'];
        $statusValueList = array_merge($statusList['todelivered'],$statusList['indelivery'],$statusList['delivered']);//状态列表
        //查询工单
        $second = $this->loadModel('secondorder')->getByID($secondOrderID);
        $oldStatus = $second->status;
        $endStatus = '';
        //查询对外移交
        $sectransfer = $this->dao->select('id,status,secondorderId')->from(TABLE_SECTRANSFER)
            ->where('id')->eq($sectransferID)
            ->andWhere('deleted')->eq('0')
            ->fetch();

        foreach ($statusList as $key => $status) {
            if(in_array($sectransfer->status,$status)){
                $endStatus = $key; //$sectransfer->status;
                break;
            }
        }
        if($oldStatus == $endStatus){
            return true;
        }
        if(($oldStatus == 'indelivery' || $oldSectransfer->secondorderId != $sectransfer->secondorderId) && in_array($endStatus,$statusList['indelivery'])){
            $endStatus = 'todelivered';
        }
        $currentAccount = $this->app->user->account;
        $dealUser = $this->getDealUserByStatus($second, $endStatus);
        //变更状态
        $updateParams = new stdClass();
        $updateParams->status = $endStatus;
        $updateParams->dealUser = $dealUser;

        if( $endStatus == 'delivered'){ //已交付
            $updateParams->pushDate = helper::now();
        }
        $this->dao->update(TABLE_SECONDORDER)->data($updateParams)
            ->where('id')->eq($secondOrderID)
            ->exec();
        //状态流转
        $this->loadModel('consumed')->record('secondorder', $secondOrderID, '0', $currentAccount, $oldStatus, $endStatus, array());
        //日志
        $changes = common::createChanges($second, $updateParams);

        $comment = sprintf($this->lang->secondorder->sectrabsferToSecond  ,$sectransferID,zget($this->lang->secondorder->statusList,$oldStatus,''),zget($this->lang->secondorder->statusList,$endStatus,''))  ;
        $actionID = $this->loadModel('action')->create('secondorder', $secondOrderID, 'cancelsyncstatusbyprotransfer', $comment);
        if($changes){
            $this->loadModel('action')->logHistory($actionID, $changes);
        }
    }
}
