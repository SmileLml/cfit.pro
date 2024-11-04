<?php
class requirementModel extends model
{
    /**
     *
     * @return int|null 成功返回ID, 失败返回null
     */
    public function create()
    {

        //校验创建权限 bool
        $checkAuth = $this->checkAuthCreate();
        if(!$checkAuth)
        {
            $response['result']  = 'fail';
            $response['message'] = $this->lang->requirement->noCreateAuth;
            $this->send($response);
        }

        $this->loadModel('action');
        /* 由control.php中的create方法调用，获取表单提交的数据插入到数据库中。*/
        $requirements = fixer::input('post')
            ->stripTags($this->config->requirement->editor->create['id'], $this->config->allowedTags)
            ->join('app',',')
            ->join('dealUser',',')
            ->get();

        if(empty($_POST['opinionID']))
        {
            dao::$errors['opinionID'] = sprintf($this->lang->requirement->emptyObject, $this->lang->requirement->opinionID);
        }

        if(empty($requirements->dealUser))
        {
            dao::$errors['dealUser'] = sprintf($this->lang->requirement->emptyObject, $this->lang->requirement->dealUser);
        }

        //期望完成时间
        if(!$this->loadModel('common')->checkJkDateTime($requirements->deadLine))
        {
            dao::$errors['deadLine'] =  sprintf($this->lang->requirement->emptyObject, $this->lang->requirement->deadLine);
            return;
        }

        //计划完成时间
        if(!$this->loadModel('common')->checkJkDateTime($requirements->planEnd))
        {
            dao::$errors['planEnd'] =  sprintf($this->lang->requirement->emptyObject, $this->lang->requirement->planEnd);
            return;
        }

        /*
        //计划完成时间不允许大于期望完成时间
        if(strtotime($requirements->planEnd) > strtotime($requirements->deadLine))
        {
            $errors[''] = $this->lang->requirement->editEndTip;
            return dao::$errors = $errors;
        }
        */

        $this->tryError();

        $opinionID = $requirements->opinionID;
        $opinionObj = $this->loadModel('opinion')->getByID($opinionID);
        if(empty($opinionObj->category)){
            dao::$errors['categoryEmpty'] = "所属意向未补充完整请联系产品经理进行补充";
        }
          //迭代二十五要求暂时注释 预留后续使用
//        $expDate = date('Y-m-d h:i:s', strtotime('-3 months'));
//        if($opinionObj->onlineTimeByDemand != "" && $opinionObj->onlineTimeByDemand < $expDate ){
//            dao::$errors['expired'] = "上线成功3个月之后不允许进行倒挂，请按照新的需求处理";
//        }
        $this->tryError();


        // 更新需求代号。
        $date   = helper::today();
        $codeBefore = substr( $date, 0, 4) . sprintf('%03d', $opinionID);
        $number = $this->dao->select('count(id) c')
            ->from(TABLE_REQUIREMENT)
            ->where('code')
            ->like($codeBefore.'%')
            ->andWhere('sourceRequirement')
            ->eq(1)
            ->fetch('c');

        $code   = $codeBefore . '-' . sprintf('%02d', $number+1);
        $data = new stdClass();
        $data->name        = $requirements->name ?? '';
        $data->desc        = $requirements->desc ?? '';
        $data->status      = 'published';
        $data->opinion     = $opinionID;
        $data->code        = $code;
        $data->deadLine       = $requirements->deadLine ?? '';
        $data->planEnd        = $requirements->planEnd ?? '';
        $data->createdBy   = $this->app->user->account;
        $data->dealUser   = $requirements->dealUser;
        $data->comment   = $requirements->comment;
        $data->createdDate = helper::now();
        $data->productManager   = $this->app->user->account;
        $data->projectManager   = $requirements->dealUser;
        $data->startTime   = helper::now();

        // 增加所属应用系统字段
        $data->app = $requirements->app ?? '';
        $data = $this->loadModel('file')->processImgURL($data, $this->config->requirement->editor->create['id'], $this->post->uid);
        //同步需求意向字段
        $data->sourceMode = $opinionObj->sourceMode;
        $data->sourceName = $opinionObj->sourceName;
        $data->acceptTime = $opinionObj->sourceMode=='8'?helper::now():$opinionObj->receiveDate;
        $data->union = $opinionObj->union;
        $data->deadlineByOpinion = $opinionObj->deadline;
        $data->dateByOpinion = $opinionObj->date;
        $data->nameByOpinion = $opinionObj->name;

        //需求意向待处理人为空的时候，倒挂成功后将倒挂人显示为需求意向的待处理人
        if(in_array($opinionObj->status,['delivery','online']))
        {
            $this->dao->update(TABLE_OPINION)->set('dealUser')->eq($this->app->user->account)->where('id')->eq($opinionID)->exec();
        }
        $this->dao->insert(TABLE_REQUIREMENT)->data($data)
            ->batchCheck($this->config->requirement->create->requiredFields, 'notempty')
            ->autoCheck()->exec();
        $requirementID = $this->dao->lastInsertID();
        $this->updateNewPublishedTime($requirementID);
        //只有非已拆分时才更新状态为已拆分，增加状态流转
        if(!in_array($opinionObj->status,['subdivide','underchange']))
        {
            $this->loadModel('opinion')->updateStatusById('subdivided',$opinionID);
            $this->loadModel('consumed')->record('opinion', $opinionID, 0, $this->app->user->account, $opinionObj->status, 'subdivided');
        }
        if(dao::isError()) return false;

        $this->loadModel('consumed');
        $this->consumed->record('requirement', $requirementID, 0, $this->app->user->account, '', 'published', array());

        $spec = new stdClass();
        $spec->name        = $requirements->name;
        $spec->requirement = $requirementID;
        $spec->desc =  $requirements->desc;
        $spec->code = $code;
        $spec->createdBy   = $this->app->user->account;
        $spec->createdDate = helper::now();
        $this->dao->insert(TABLE_REQUIREMENTSPEC)->data($spec)->exec();
        if(dao::isError()) return false;

        $this->loadModel('file')->updateObjectID($this->post->uid, $requirementID, 'requirement');
        $this->file->saveUpload('requirement', $requirementID);

        // $actionID = $this->action->create('requirement', $requirementID, 'created');
        //$this->sendmail($requirementID, $actionID);
        return $requirementID;

    }

    /**
     * Desc:api同步接口创建数据
     * User: wangshusen
     * Date: 2022/6/7
     * Time: 17:21
     *
     *
     */
    public function createApi()
    {
        $this->app->loadLang('demand');
        /* 由control.php中的create方法调用，获取表单提交的数据插入到数据库中。*/
        $requirementData = fixer::input('post')
            ->stripTags($this->config->requirement->editor->create['id'], $this->config->allowedTags)
            ->get();
        $this->app->loadLang('opinion');
        $requirementData->dealUser = !empty($this->lang->opinion->apiDealUserList['userAccount'])?$this->lang->opinion->apiDealUserList['userAccount']:'litianzi';
        $requirementData->isImprovementServices = array_search($requirementData->isImprovementServices,$this->lang->requirement->isImprovementServicesList);
        //外部是超时开始和结束时间
        $days = $this->lang->demand->expireDaysList['outsideDays'];
        $requirementData->feekBackStartTimeOutside = helper::now();
        $requirementData->feekBackEndTimeOutSide = helper::getTrueWorkDay(helper::now(),$days,true).substr(helper::now(),10);
        //计划完成时间
        if(!isset($requirementData->planEnd)){
            if(isset($requirementData->end)){
                $requirementData->planEnd = $requirementData->end;
            }
        }
        $this->dao->insert(TABLE_REQUIREMENT)->data($requirementData)->exec();
        $changeOrderNumber = $requirementData->changeOrderNumber;
        if(!dao::isError())
        {
            $requirementID = $this->dao->lastInsertId();

            $requirementData->requirement = $requirementID;
            unset($requirementData->opinion);
            unset($requirementData->status);
            unset($requirementData->dealUser);
            unset($requirementData->feedbackStatus);
            unset($requirementData->acceptTime);
            unset($requirementData->changeOrderNumber);
            unset($requirementData->canceled);
            unset($requirementData->isImprovementServices);
            unset($requirementData->estimateWorkload);
            unset($requirementData->ChildName);
            unset($requirementData->feekBackStartTimeOutside);
            unset($requirementData->feekBackEndTimeOutSide);
            unset($requirementData->type);
            unset($requirementData->requireStartTime);
            if(isset($requirementData->planEnd)){
                unset($requirementData->planEnd);
            }
            $this->dao->insert(TABLE_REQUIREMENTSPEC)->data($requirementData)->exec();
            if ($changeOrderNumber != ''){
                $change = $this->dao->select("*")->from(TABLE_REQUIREMENTCHANGE)->where('changeNumber')->eq($changeOrderNumber)->fetch();
                $changeCode = explode(',',$change->changeEntry);
                $changeCode[] = $requirementData->entriesCode;
                $codeStr = implode(',',array_unique($changeCode));
                $changeInfo = new stdClass();
                $changeInfo->editDate = date('Y-m-d H:i:s',time());
                $changeInfo->changeEntry = trim($codeStr,',');
                $this->dao->update(TABLE_REQUIREMENTCHANGE)->data($changeInfo)->where('changeNumber')->eq($changeOrderNumber)->exec();
            }
            return $requirementID;
        }
    }

    /**
     * Project: chengfangjinke
     * Method: update
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:24
     * Desc: This is the code comment. This method is called update.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $requirementID
     * @return array|false
     */
    public function update($requirementID)
    {
        $this->app->loadLang('demand');
        /* 获取旧的需求条目数据，获取post请求参数进行处理，更新需求条目信息，更新成功后记录需求条目版本，返回改动的字段信息。*/
        $oldRequirement = $this->getByID($requirementID);
        $requirement = fixer::input('post')
            ->join('app',',')
            ->join('dealUser',',')
            ->stripTags($this->config->requirement->editor->edit['id'], $this->config->allowedTags)
            ->remove('uid,files,labels')
            ->get();
        if($oldRequirement->opinion != $requirement->opinion){
            $opinionObj = $this->loadModel('opinion')->getByID($requirement->opinion);
            if(empty($opinionObj->category)){
                dao::$errors['categoryEmpty'] = "所属意向未补充完整请联系产品经理进行补充";
            }
              //迭代二十五要求暂时注释 预留后续使用
//            $expDate = date('Y-m-d h:i:s', strtotime('-3 months'));
//            if($opinionObj->onlineTimeByDemand != "" && $opinionObj->onlineTimeByDemand < $expDate ){
//                dao::$errors['expired'] = "上线成功3个月之后不允许进行倒挂，请按照新的需求处理";
//            }
        }
        if(empty($requirement->dealUser))
        {
            dao::$errors['dealUser'] = sprintf($this->lang->requirement->emptyObject, $this->lang->requirement->dealUser);
        }

        //清总创建且待反馈状态，增加反馈单待处理人不可为空
        if($oldRequirement->createdBy == 'guestcn' && $oldRequirement->feedbackStatus == 'tofeedback'){
            if(empty($requirement->feedbackDealUser))
            {
                dao::$errors['feedbackDealUser'] = sprintf($this->lang->requirement->emptyObject, $this->lang->requirement->feedbackDealuser);
            }
        }

        if(empty($requirement->opinion))
        {
            dao::$errors['opinion'] = sprintf($this->lang->requirement->emptyObject, $this->lang->requirement->opinionID);
        }

        //期望完成时间
        if(!$this->loadModel('common')->checkJkDateTime($requirement->deadLine))
        {
            dao::$errors['deadLine'] =  sprintf($this->lang->requirement->emptyObject, $this->lang->requirement->deadLine);
            return;
        }

        //计划完成时间检查(内部自建非清总且拆分之前可以修改计划完成时间)
        if(($oldRequirement->createdBy != 'guestcn') && (in_array($oldRequirement->status,['published']))){
            if(!$this->loadModel('common')->checkJkDateTime($requirement->planEnd))
            {
                dao::$errors['planEnd'] =  sprintf($this->lang->requirement->emptyObject, $this->lang->requirement->planEnd);
                return;
            }
        }


        $this->tryError();

        $opinionID = $requirement->opinion;
        $opinionObj = $this->loadModel('opinion')->getByID($opinionID);
        $data = new stdClass();
        if(empty($oldRequirement->entriesCode)){
            $data->name        = $requirement->name ?? '';
        }else{
            $data->name        = $oldRequirement->name;
        }
        $data->desc        = $requirement->desc ?? '';
        if($oldRequirement->status != 'topublish'){
            $data->status = $oldRequirement->status;
        }else{
            $data->status      = 'published';
            $data->nextDealuser   = $requirement->dealUser;
            $data->startTime   = helper::now();
        }
        $data->opinion     = $opinionID;
        $data->deadLine       = $requirement->deadLine ?? '';
        $data->planEnd        = $requirement->planEnd ?? '';
        $data->editedBy    = $this->app->user->account;
        $data->editedDate    = helper::now();
        $data->productManager   = $this->app->user->account;
        $data->projectManager   = $requirement->dealUser;
        $data->dealUser   = $requirement->dealUser;

        if($oldRequirement->createdBy == 'guestcn' && $oldRequirement->feedbackStatus == 'tofeedback')
        {
            $data->feedbackDealUser   = $requirement->feedbackDealUser;
        }

        $data->comment   = $requirement->comment;
        // 增加所属应用系统字段
        $data->app = $requirement->app ?? '';
        //同步需求意向字段
        $data->sourceMode = $opinionObj->sourceMode;
        $data->sourceName = $opinionObj->sourceName;
        $data->union = $opinionObj->union;
        $data->deadlineByOpinion = $opinionObj->deadline;
        $data->dateByOpinion = $opinionObj->date;
        $data->nameByOpinion = $opinionObj->name;
        if($oldRequirement->dealUser != $requirement->dealUser){
            $data->ignoreStatus = 0;
        }

        //更新最新发布时间
        if(in_array($oldRequirement->status,['topublish','published']))
        {
            $this->updateNewPublishedTime($oldRequirement->id);
        }

        //迭代三十四 内部反馈开始和截止时间修改为指定反馈人的落库时间（如果反馈人不发生变化不更新） 待反馈状态
        if($requirement->feedbackDealUser != $oldRequirement->feedbackDealUser && $oldRequirement->isUpdateOverStatus == 1 && $oldRequirement->feedbackStatus == 'tofeedback')
        {
            $startTime = date('Y-m-d H:i:s',time());
            $hms = substr($startTime,10);
            $daysInside = $this->lang->demand->expireDaysList['insideDays'];
            $data->feekBackStartTime = $startTime;
            $data->feekBackEndTimeInside  = helper::getTrueWorkDay($startTime,$daysInside,true).$hms; //内部结束时间
        }

//        $startTime = date('Y-m-d H:i:s',time());
//        $hms = substr($startTime,10);
//        //待发布->已发布  待发布无法发起变更审批流程，故不会有审批通过的情况
//        if($oldRequirement->status == 'topublish' && $oldRequirement->isUpdateOverStatus == 1){
//            $daysInside = $this->lang->demand->expireDaysList['insideDays'];
//            $data->feekBackStartTime = $startTime;
//            $data->feekBackEndTimeInside  = helper::getTrueWorkDay($startTime,$daysInside,true).$hms; //内部结束时间
//        }
//
//        //已发布->已发布 更新反馈开始和截止时间  若点击反馈后，又进行指派导致出现的已发布→已发布，该场景数据过滤掉
//        if($oldRequirement->status == 'published' && $oldRequirement->isUpdateOverStatus == 1)
//        {
//            $editedUpdate = $this->checkFeedBack('requirement',$requirementID,'edited','id_desc','总中心接口同步更新');
//            //有更新 查询更新时间后 首次
//            if($editedUpdate)
//            {
//                $baseTime = $editedUpdate->date;
//                //查询更新后的首次反馈后时间
//                $feedBackTime = $this->checkFeedBack('requirement',$requirementID,'createfeedbacked','id_asc','',$baseTime);
//                if(!$feedBackTime)
//                {
//                    $daysInside = $this->lang->demand->expireDaysList['insideDays'];
//                    $data->feekBackStartTime = $startTime;
//                    $data->feekBackEndTimeInside  = helper::getTrueWorkDay($startTime,$daysInside,true).$hms; //内部结束时间
//                }
//            }else{ //无更新 查询首次反馈
//                //查询更新后的首次反馈后时间
//                $feedBackTime = $this->checkFeedBack('requirement',$requirementID,'createfeedbacked','id_asc','');
//                if(!$feedBackTime)
//                {
//                    $daysInside = $this->lang->demand->expireDaysList['insideDays'];
//                    $data->feekBackStartTime = $startTime;
//                    $data->feekBackEndTimeInside  = helper::getTrueWorkDay($startTime,$daysInside,true).$hms; //内部结束时间
//                }
//            }
//        }
        //清总需求任务反馈前计划完成时间等于期望完成时间，所以当期望完成时间修改时，计划完成时间也跟着修改
        if(isset($data->deadLine)){
            $isGuestcn = $this->getIsGuestcn($oldRequirement->createdBy);
            if($isGuestcn && ($oldRequirement->feedbackStatus != 'feedbacksuccess')){ //清总且没有审核通过
                $data->planEnd = $data->deadLine;
            }
        }

        $this->dao->update(TABLE_REQUIREMENT)
            ->data($data)
            ->where('id')->eq($requirementID)
            ->batchCheck($this->config->requirement->edit->requiredFields, 'notempty')
            ->autoCheck()->exec();
        //更新工时
        $this->dealConsumed($requirementID,0,$this->app->user->account,$oldRequirement->status);
        unset($requirement->consumed);
        $requirement = $this->loadModel('file')->processImgURL($requirement, $this->config->requirement->editor->edit['id'], $this->post->uid);
        if(!dao::isError())
        {
            $this->dao->update(TABLE_REQUIREMENTSPEC)->set('`desc`')->eq($requirement->desc)
                ->where('requirement')->eq($requirementID)
                ->andWhere('version')->eq($oldRequirement->version)
                ->exec();

            $this->loadModel('file')->updateObjectID($this->post->uid, $requirementID, 'requirement');
            $this->file->saveUpload('requirement', $requirementID);
            return common::createChanges($oldRequirement, $requirement);
        }

        return false;
    }

    /**
     * @Notes:编辑退回的变更单
     * @Date: 2023/7/13
     * @Time: 14:10
     * @Interface editchange
     * @param $changeID
     * @param $requirementID
     */
    public function editchange($changeID,$requirementID)
    {
        $oldRequirement = $this->getByID($requirementID);
        //必须选择变更事项才可提交
        if(!isset($_POST['alteration']))
        {
            dao::$errors = $this->lang->requirement->chooseAlteration;
            return;
        }
        $alterationData = $_POST['alteration'];
        //变更后-需求任务主题
        if(in_array('changeTitle',$alterationData) && empty($_POST['changeTitle']))
        {
            dao::$errors['changeTitle'] =  sprintf($this->lang->requirement->error->empty, $this->lang->requirement->changeTitle);
            return;
        }
        //期望完成时间
        if(in_array('requirementDeadline',$alterationData) && !$this->loadModel('common')->checkJkDateTime($_POST['changeDeadline']))
        {
            dao::$errors['changeDeadline'] =  sprintf($this->lang->requirement->error->empty, $this->lang->requirement->changeDeadline);
            return;
        }
        //变更后-需求任务概述
        if(in_array('requirementOverview',$alterationData) && empty($_POST['changeOverview']))
        {
            dao::$errors['changeOverview'] =  sprintf($this->lang->requirement->error->empty, $this->lang->requirement->changeOverview);
            return;
        }
        if($_POST['affectDemandCheck'] == 'yes' && !isset($_POST['affectDemand'])){
            dao::$errors['affectDemand'] =  sprintf($this->lang->requirement->error->empty, $this->lang->requirement->affectDemand);
            return;
        }

        if($_POST['affectDemandCheck'] == 'no'  && isset($_POST['affectDemand']))
        {
            unset($_POST['affectDemand']);
        }
        //变更原因
        if(empty($_POST['changeReason'])){
            dao::$errors['changeReason'] =  sprintf($this->lang->requirement->error->empty, $this->lang->requirement->changeReason);
            return;
        }
        //产品经理和部门管理层必填
        if(empty($_POST['po'])){
            dao::$errors['manage'] =  sprintf($this->lang->requirement->error->empty, $this->lang->requirement->manage);
            return;
        }
        if(empty($_POST['deptLeader'])){
            dao::$errors['deptLeader'] = sprintf($this->lang->requirement->error->empty, $this->lang->requirement->deptLeader);
            return;
        }
        //变更后-需求任务主题
        if(in_array('requirementFile',$alterationData) && empty($_FILES['files']))
        {
            dao::$errors['changeFile'] =  sprintf($this->lang->requirement->error->empty, $this->lang->requirement->changeFile);
            return;
        }

        /** 计划完成时间不能大于期望完成时间*/
        $hadRequirementDeadline = in_array('requirementDeadline',$alterationData);//期望完成时间 填写为true
        $hadRequirementEnd      = in_array('requirementEnd',$alterationData);//计划完成时间 填写为true
        //①期望完成时间和计划完成时间均填写
        if($hadRequirementEnd && $hadRequirementDeadline)
        {
            /*
            if(strtotime($_POST['changePlanEnd']) > strtotime($_POST['changeDeadline']))
            {
                dao::$errors[''] =  $this->lang->requirement->editEndTip;
                return;
            }
            */
        }
        //②计划完成时间填写，其完完成时间未填写。  用原期望完成时间作对比
        if($hadRequirementEnd && !$hadRequirementDeadline)
        {
            /*
            if(strtotime($_POST['changePlanEnd']) > strtotime($oldRequirement->deadLine))
            {
                dao::$errors[''] =  $this->lang->requirement->editEndTip;
                return;
            }
            */
        }
        $postData = fixer::input('post')
            ->stripTags($this->config->requirement->editor->editchange['id'], $this->config->allowedTags)
            ->get();
        $postData = $this->loadModel('file')->processImgURL($postData, $this->config->requirement->editor->editchange['id'], $this->post->uid);
        return $this->dealEditchange($changeID, (array)$postData, $requirementID);
    }

    /**
     * Desc:api同步接口创建数据
     * User: wangshusen
     * Date: 2022/6/7
     * Time: 19:20
     *
     * @param $requirementID
     * @return false
     *
     */
    public function updateApi($requirementID)
    {
        $this->app->loadLang('demand');
        /* 获取旧的需求条目数据，获取post请求参数进行处理，更新需求条目信息，更新成功后记录需求条目版本，返回改动的字段信息。*/
        $oldRequirement = $this->getByID($requirementID);
        $requirement = fixer::input('post')
            ->stripTags($this->config->requirement->editor->edit['id'], $this->config->allowedTags)
            ->add('feedbackStatus', 'tofeedback')
            ->add('feedbackDealUser', $oldRequirement->feedbackBy)
            ->remove('uid,files,labels')
            ->get();
        $requirement->version = $oldRequirement->version;
        $requirement->status = $oldRequirement->status;
        $requirement->ifOutUpdate = 2;//标记外部单位更新过
        $requirement->isImprovementServices = array_search($requirement->isImprovementServices,$this->lang->requirement->isImprovementServicesList);
        $changeOrderNumber = '';
        if ($oldRequirement->status != 'topublish' && $requirement->changeOrderNumber != ''){
            $requirement->version = $oldRequirement->version+1;
            $requirement->dealUser = !empty($this->lang->opinion->apiDealUserList['userAccount'])?$this->lang->opinion->apiDealUserList['userAccount']:'litianzi';
            $requirement->changedDate = helper::now();
            $requirement->reviewStage = 1;
            $requirement->changedTimes = $requirement->version - 1;
            $requirement->status = 'topublish';
        }
        if ($requirement->changeOrderNumber != ''){
            $changeOrderNumber = $requirement->changeOrderNumber;

            $oldCode = explode(',',$oldRequirement->changeOrderNumber);
            $oldCode[] = trim($requirement->changeOrderNumber,',');
            $requirement->changeOrderNumber = implode(',',array_unique($oldCode));
        }
        $requirement->changeOrderNumber = trim($requirement->changeOrderNumber,',');
        $requirement = $this->loadModel('file')->processImgURL($requirement, $this->config->requirement->editor->edit['id'], $this->post->uid);
        //迭代二十八增加变更次数和最新变更时间 迭代二十九增加超时逻辑处理
        if(!empty($requirement->changeOrderNumber))
        {
            $requirement->requirementChangeTimes = ($oldRequirement->requirementChangeTimes) +1;
            $nowDate = helper::now();
            $requirement->lastChangeTime = $nowDate;
            //内外部是否超时均需初始化状态
            $requirement->ifOverDate = 1;
            $requirement->ifOverTimeOutSide = 1;
            $requirement->feekBackStartTime = null;
            $requirement->feekBackEndTimeInside = null;
            $requirement->feekBackStartTimeOutside = $nowDate;
            $requirement->deptPassTime = null;
            $requirement->innovationPassTime = null;
            $requirement->isUpdateOverStatus = 1;

            $hms = substr($nowDate,10);
            //截止时间
            $days = $this->lang->demand->expireDaysList['outsideDays'];
            $requirement->feekBackEndTimeOutSide = helper::getTrueWorkDay($nowDate,$days,true).$hms; //结束时间
        }
        //变更单号为空只允许修改所属研发子项
        if ($requirement->changeOrderNumber == ''){
            $requirement = new stdClass();
            if(isset($_POST['ChildName'])){
                $requirement->ChildName = $_POST['ChildName'];
            }
            if(isset($_POST['end'])){
                $requirement->end = $_POST['end'];
            }
            $requirement->status = $oldRequirement->status;
        }
        $this->dao->update(TABLE_REQUIREMENT)->data($requirement)->where('id')->eq($requirementID)->exec();

        //重新修改计划完后成时间
        $newRequirement = $this->getByID($requirementID);
        $requirementTemp = new stdClass();
        $end = $newRequirement->end;
        if($this->loadModel('common')->checkJkDateTime($end)){
            $requirementTemp->planEnd = $end;
        }else{
            $requirementTemp->planEnd = $newRequirement->deadLine;
        }
        $this->dao->update(TABLE_REQUIREMENT)->data($requirementTemp)->where('id')->eq($requirementID)->exec();


        if(!dao::isError())
        {

            $this->loadModel('consumed')->record('requirement', $requirementID, 0, 'guestcn',
                $oldRequirement->status, $requirement->status, array(), "updateApi",$requirement->version);
            if ($changeOrderNumber != ''){
                $this->dao->update(TABLE_REQUIREMENTSPEC)->set('`desc`')->eq($requirement->desc)
                    ->where('requirement')->eq($requirementID)
                    ->andWhere('version')->eq($requirement->version)
                    ->exec();

                $this->loadModel('file')->updateObjectID($this->post->uid, $requirementID, 'requirement');


                $change = $this->dao->select("*")->from(TABLE_REQUIREMENTCHANGE)->where('changeNumber')->eq($changeOrderNumber)->fetch();
                $changeCode = explode(',',$change->changeEntry);
                $changeCode[] = $oldRequirement->entriesCode;
                $codeStr = implode(',',array_unique($changeCode));
                $changeInfo = new stdClass();
                $changeInfo->editDate = date('Y-m-d H:i:s',time());
                $changeInfo->changeEntry = trim($codeStr,',');
                $this->dao->update(TABLE_REQUIREMENTCHANGE)->data($changeInfo)->where('changeNumber')->eq($changeOrderNumber)->exec();
            }

            unset($requirement->changedTimes);
            return common::createChanges($oldRequirement, $requirement);
        }

        return false;
    }

    /**
     * Notes:暂时处理迭代八工时问题
     * User: wangshusen
     * Date: 2022/5/29
     * Time: 20:15
     *
     * @param $requirementID
     * @param $consumed
     *
     */
    public function dealConsumed($requirementID,$consumed,$dealUser, $status)
    {
        $consumedModel = $this->loadModel('consumed');
        $consumedDetail = $consumedModel->getObjectByID($requirementID,'requirement','published');
        if($consumedDetail){
            $data = new stdClass();
            $data->objectType = 'requirement';
            $data->objectID = $requirementID;
            $data->consumed = $consumed;
            $data->before = $status;
            $data->after =  $status;
            $data->account = $dealUser;
            $data->details = '';
            $data->mailto = '';
            $data->createdBy = $this->app->user->account;
            $data->createdDate =  helper::now();
            //清总同步变更数据，编辑后将历史记录记为 待发布到已发布
            if($status == 'topublish')
            {
                $data->before = 'topublish';
                $data->after =  'published';
            }
            $this->dao->insert(TABLE_CONSUMED)->data($data)->exec();
        }else{
            $data = new stdClass();
            $data->objectType = 'requirement';
            $data->objectID = $requirementID;
            $data->consumed = $consumed;
            $data->before = 'topublish';
            $data->after = 'published';
            $data->account = $dealUser;
            $data->details = '';
            $data->mailto = '';
            $data->createdBy = $this->app->user->account;
            $data->createdDate =  helper::now();
            $this->dao->insert(TABLE_CONSUMED)->data($data)->exec();
        }
    }

    /**
     * Project: chengfangjinke
     * Method: confirm
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:24
     * Desc: This is the code comment. This method is called confirm.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $requirementID
     * @return array
     */
    public function confirm($requirementID)
    {
        $this->tryError();

        /* 获取旧的需求条目信息，获取form表单提交的参数进行处理，更新需求条目信息，返回有差异的字段。*/
        $oldRequirement = $this->getByID($requirementID);
        $requirement = fixer::input('post')
            ->add('status', 'confirmed')
            ->stripTags($this->config->requirement->editor->confirm['id'], $this->config->allowedTags)
            ->remove('uid,files,labels,consumed')
            ->get();
        $requirement = $this->loadModel('file')->processImgURL($requirement, $this->config->requirement->editor->confirm['id'], $this->post->uid);
        $this->dao->update(TABLE_REQUIREMENT)
            ->data($requirement)
            ->where('id')->eq($requirementID)
            ->autoCheck()->batchCheck($this->config->requirement->confirm->requiredFields, 'notempty')
            ->exec();

        $this->loadModel('file')->updateObjectID($this->post->uid, $requirementID, 'requirement');
        $this->file->saveUpload('requirement', $requirementID);
        return common::createChanges($oldRequirement, $requirement);
    }

    /**
     * @Notes:内部自建任务编辑计划完成时间
     * @Date: 2024/4/8
     * @Time: 14:34
     * @Interface editEnd
     * @param $requirement
     */
    public function editEnd($requirement)
    {
        $data = fixer::input('post')->get();
        //为空校验
        if(empty($data->planEnd) || $data->planEnd == '0000-00-00')
        {
            dao::$errors['planEnd'] =  sprintf($this->lang->requirement->error->empty, $this->lang->requirement->planEnd);
            return;
        }

        //计划完成时间不允许大于期望完成时间
        $deadLine = $requirement->deadLine;
        if(strtotime($data->planEnd) > strtotime($deadLine))
        {
            dao::$errors[] = $this->lang->requirement->editEndTip;
            return;
        }

        $diffData = new stdClass();
        $diffData->planEnd = $requirement->planEnd;
        $diffData->comment = '';

        $this->dao->update(TABLE_REQUIREMENT)->set('`end`')->eq($data->end)->where('id')->eq($requirement->id)->exec();
        return common::createChanges($diffData, $data);
    }

    /**
     * Project: chengfangjinke
     * Method: feedback
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:24
     * Desc: This is the code comment. This method is called feedback.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $requirementID
     * @return array
     */
    public function feedback($requirementID){
        $oldRequirement = $this->getByID($requirementID);
        if(empty($oldRequirement->entriesCode)){
            $errors[''] = '清总同步的需求任务才能反馈';
            return dao::$errors = $errors;
        }

        if($oldRequirement->changeLock == 2){
            $response['result']  = 'fail';
            $response['message'] = $this->lang->requirement->changeIng;
            $this->send($response);
        }

        $nowTime = date('Y-m-d');
        /*$deptInfo = $this->loadModel('dept')->getByID($this->app->user->dept);
        if($deptInfo != null){
            $feedbackToHandle = $deptInfo->manager;
        }else{
            //若没有部门，就设置本身作为审批人
            $feedbackToHandle = $this->app->user->account;
        }*/
        $requirement = fixer::input('post')
            ->add('feedbackStatus', 'todepartapproved')
            ->add('feedbackBy', $this->app->user->account)
            /*->add('feedbackDealUser', $feedbackToHandle)*/
            ->join('feedbackDealUser', ',')
            ->add('reviewComments', '')
            ->join('product', ',')
            ->join('line', ',')
            ->join('app', ',')
            //->stripTags($this->config->requirement->noeditor->feedback['id'], $this->config->allowedTags)
            ->remove('uid,labels')
            ->get();
        //期望完成时间
        if(!$this->loadModel('common')->checkJkDateTime($requirement->end))
        {
            dao::$errors['end'] =  sprintf($this->lang->requirement->emptyObject, $this->lang->requirement->end);
            return;
        }
        if(empty($requirement->feedbackDealUser)){
            dao::$errors['feedbackDealUser'] =  sprintf($this->lang->requirement->emptyObject, $this->lang->requirement->feedbackDealUser);
            return;
        }

        $owneruser = $this->loadModel('user')->getById($requirement->owner, 'account');
        $requirement->dept = $owneruser->dept ?? '';
        $requirement->reviewStage = '1';
        $requirement->version     =  $oldRequirement->version+1;
        $this->dao->update(TABLE_REQUIREMENT)
            ->data($requirement)
            ->where('id')->eq($requirementID)
            ->autoCheck()
            ->batchCheck($this->config->requirement->feedback->requiredFields, 'notempty')
            ->exec();
        if(!dao::isError())
        {
            $this->loadModel('file')->updateObjectID($this->post->uid, $requirementID, 'requirement');
            $this->file->saveUpload('requirement', $requirementID);
            $this->loadModel('consumed')->record('requirement', $requirementID, 0, $this->app->user->account,
                $oldRequirement->feedbackStatus, $requirement->feedbackStatus, array(), "requirementFeedback");

            $apiUser  =  $this->dao->select('value')->from(TABLE_LANG)->where('module')->eq('problem')->andWhere('section')->eq('apiDealUserList')->fetch()->value;
            $this->loadModel('review');
            $this->review->addNode('requirement', $requirementID, $requirement->version, explode(',',$requirement->feedbackDealUser), true, 'pending', 1);
//            $this->review->addNode('requirement', $requirementID, $requirement->version, explode(',',$apiUser), true, 'wait', 2);
            $this->review->addNode('requirement', $requirementID, $requirement->version, explode(',','guestjk'), true, 'wait', 3);
            $this->review->addNode('requirement', $requirementID, $requirement->version, explode(',','guestcn'), true, 'wait', 4);
        }

        return common::createChanges($oldRequirement, $requirement);
    }
    /*public function feedback($requirementID)
    {*/
        /*
        非推送需求条目，直接走原有流程。
        推送的需求条目，已确认   =》 反馈后 =》 审核中。
        推送的需求条目，审核失败 =》 反馈后 =》 审核中。
        推送的需求条目，审核成功 =》 反馈后 =》 审核成功。
        */

        /* 获取旧的需求条目信息和表单提交信息，更新需求条目。*/
        /*$oldRequirement = $this->getByID($requirementID);

        $status = '';
        if(empty($oldRequirement->entriesCode))
        {
            $status = 'feedbacked';
        }
        else
        {
            $status = 'reviewing';
        }

        $nowTime = date('Y-m-d');
        $requirement = fixer::input('post')
            ->add('status', $status)
            ->add('feedbackBy', $this->app->user->account)
            ->add('feedbackDate', $nowTime)
            ->join('product', ',')
            ->join('line', ',')
            ->join('app', ',')
            ->stripTags($this->config->requirement->editor->feedback['id'], $this->config->allowedTags)
            ->remove('uid,labels')
            ->get();

        $this->dao->update(TABLE_REQUIREMENT)
             ->data($requirement)
             ->where('id')->eq($requirementID)
             ->autoCheck()
             ->batchCheck($this->config->requirement->feedback->requiredFields, 'notempty')
             ->exec();

        $pushEnable = $this->config->global->pushEnable;
        if($status == 'reviewing' and $pushEnable == 'enable')
        {
            $url           = $this->config->global->pushUrl;
            $pushAppId     = $this->config->global->pushAppId;
            $pushAppSecret = $this->config->global->pushAppSecret;
            $pushUsername  = $this->config->global->pushUsername;
            $requirement = $this->loadModel('file')->processImgURL($requirement, $this->config->requirement->editor->change['id'], $this->post->uid);

            $headers = array();
            $headers[] = 'App-Id: ' . $pushAppId;
            $headers[] = 'App-Secret: ' . $pushAppSecret;

            $deptList = $this->loadModel('dept')->getOptionMenu();
            $users    = $this->loadmodel('user')->getPairs('noletter');
            $projects = $this->loadModel('projectplan')->getPairs();

            $pushData = array();
            $pushData['Project_team']            = zget($deptList, $requirement->dept, ''); // 项目组
            $pushData['Planned_completion_time'] = strtotime($requirement->end) . '000'; // 计划完成时间
            $pushData['Jinke_Responsible']       = zget($users, $requirement->owner, ''); // 责任人
            $pushData['Attribution_item']        = zget($projects, $requirement->project, ''); // 归属项目
            $pushData['Feedback_number']         = empty($oldRequirement->feedbackCode) ? '' : $oldRequirement->feedbackCode; // 需求反馈单编号
            $pushData['Contact_telephone']       = $requirement->contact; // 联系人电话
            $pushData['Jinke_Feedback_person']   = zget($users, $this->app->user->account, ''); // 金科反馈人
            $pushData['Feedback_date']           = strtotime($nowTime) . '000'; // 反馈日期
            $pushData['Requirement_item_number'] = $oldRequirement->entriesCode; // 需求条目编号

            $method = zget($this->lang->requirement->methodList, $requirement->method);
            $method = str_replace('实现', '', $method);
            $pushData['Implementation_mode']       = $method; // 实现方式
            $pushData['Requirement_item_analysis'] = $requirement->analysis; // 需求条目分析
            $pushData['Handling_suggestions']      = $requirement->handling; // 处理建议
            $pushData['Implementation']            = $requirement->implement; // 实施情况

            $pushData['Planned_completion_time'] = (int)$pushData['Planned_completion_time'];
            $pushData['Feedback_date'] = (int)$pushData['Feedback_date'];
            $pushData['Project_team']= trim($pushData['Project_team'], '/');

            $object     = 'requirement';
            $objectType = 'feedback';
            $request    = 'POST';
            $params     = $pushData;
            $response   = '';
            $status     = 'fail';
            $extra      = '';

            $result = $this->loadModel('requestlog')->http($url, $pushData, 'POST', 'json', array(), $headers);
            if(!empty($result))
            {
                $resultData = json_decode($result);
                if(isset($resultData->code) and $resultData->code == '200')
                {
                    $status = 'success';
                    $this->dao->update(TABLE_REQUIREMENT)->set('feedbackCode')->eq($resultData->data->Feedback_number)->where('id')->eq($requirementID)->exec();
                } else {
                    $this->dao->update(TABLE_REQUIREMENT)->set('status')->eq('syncfail')->where('id')->eq($requirementID)->exec();
                    $this->loadModel('action')->create('requirement', $requirementID, '同步清总失败', $resultData->message);
                }

                $response = $result;
            } else {
                $this->dao->update(TABLE_REQUIREMENT)->set('status')->eq('pushedfail')->where('id')->eq($requirementID)->exec();
                $this->loadModel('action')->create('requirement', $requirementID, '推送清总失败', "网络不通");
            }
            $this->requestlog->saveRequestLog($url, $object, $objectType, 'POST', $pushData, $response, $status, $extra);
        }

        if(!dao::isError())
        {
            if($status == 'feedback' or $status == 'approved')
            {
                /* 删除需求条目所属的产品记录，重新计算需求条目属于那些产品。*/
                /*$this->dao->delete()->from(TABLE_PRODUCTREQUIREMENT)->where('requirement')->eq($requirementID)->exec();
                if(isset($requirement->product) and $requirement->product)
                {
                    foreach(explode(',', $requirement->product) as $product)
                    {
                        if(!$product) continue;

                        $data = new stdClass();
                        $data->requirement = $requirementID;
                        $data->product     = $product;
                        $this->dao->insert(TABLE_PRODUCTREQUIREMENT)->data($data)->exec();
                    }
                }
            }

            return common::createChanges($oldRequirement, $requirement);
        }
        return false;
    }*/

    /**
     * Project: chengfangjinke
     * Method: change
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:24
     * Desc: This is the code comment. This method is called change.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $requirementID
     * @return array
     */
    public function changeV1($requirementID)
    {
        /* 获取旧的需求条目信息，获取post接收的参数信息和需求条目版本信息，将需求条目信息记录到需求版本记录。*/
        $oldRequirement = $this->getByID($requirementID);
        $requirement = fixer::input('post')
            ->join('product', ',')
            ->join('line', ',')
            ->join('app', ',')
            ->stripTags($this->config->requirement->editor->change['id'], $this->config->allowedTags)
            ->remove('uid,files,labels,mailto')
            ->get();
        $dealUser = implode(',',$requirement->reviewer);
        //更新下一节点处理人
        $this->dao->update(TABLE_REQUIREMENT)
            ->set('dealUser')->eq($dealUser)
            ->where('id')->eq($requirementID)
            ->exec();

        unset($requirement->deadLine);
        unset($requirement->reviewer);

        $version = $this->dao->select('version')->from(TABLE_REQUIREMENTSPEC)
            ->where('requirement')->eq($requirementID)
            ->orderBy('version desc')
            ->fetch('version');
        $version = $version ? $version + 1 : 1;

        /* 当为推送的需求条目时，去除评审人必填。*/
        if(!empty($requirement->entriesCode)) $this->config->requirement->change->requiredFields = 'dept,end,owner,contact,method,analysis,handling';

        $requirement->version     = $version;
        $requirement->requirement = $requirementID;
        $requirement->createdBy   = $this->app->user->account;
        $requirement->createdDate = helper::now();
        $requirement->changedDate = $requirement->createdDate;

        $this->loadModel('requirementspec')->insertByData($requirement);

        // 新增评审节点，当前变更的版本，成功后再更新version字段。
        $this->loadModel('review')->addNode('requirement', $requirementID, $version, $this->post->reviewer, true, 'pending');

        /* 将状态进行更新。*/
        if(!empty($requirement->entriesCode))
        {
            $requirement->status = 'changeReviewing';
        }
        else
        {
            $requirement->status = 'reviewing';
        }

        $mailto = '';
        if(isset($_POST['mailto'])){
            $mailto = implode(',', $this->post->mailto);
        }
        $this->dao->update(TABLE_REQUIREMENT)->set('changeVersion')->eq($version)
            ->set('changedTimes')->eq($version - 1)
            ->set('status')->eq($requirement->status)
            ->set('mailto')->eq($mailto)
            ->set('changedDate')->eq($requirement->changedDate)
            ->set('app')->eq($requirement->app)
            ->where('id')->eq($requirementID)
            ->exec();

        $pushEnable = $this->config->global->pushEnable;
        if(!empty($requirement->entriesCode) and $pushEnable == 'enable')
        {
            $url           = $this->config->global->pushUrl;
            $pushAppId     = $this->config->global->pushAppId;
            $pushAppSecret = $this->config->global->pushAppSecret;
            $pushUsername  = $this->config->global->pushUsername;

            $requirement = $this->loadModel('file')->processImgURL($requirement, $this->config->requirement->editor->change['id'], $this->post->uid);

            $headers = array();
            $headers[] = 'App-Id: ' . $pushAppId;
            $headers[] = 'App-Secret: ' . $pushAppSecret;

            $deptList = $this->loadModel('dept')->getOptionMenu();
            $users    = $this->loadmodel('user')->getPairs('noletter');
            $projects = $this->loadModel('projectplan')->getPairs();

            $nowTime = date('Y-m-d');
            $pushData = array();
            $pushData['Project_team']            = zget($deptList, $requirement->dept, ''); // 项目组
            $pushData['Planned_completion_time'] = strtotime($requirement->end) . '000'; // 计划完成时间
            $pushData['Jinke_Responsible']       = zget($users, $requirement->owner, ''); // 责任人
            $pushData['Attribution_item']        = zget($projects, $requirement->project, ''); // 归属项目
            $pushData['Feedback_number']         = empty($oldRequirement->feedbackCode) ? '' : $oldRequirement->feedbackCode; // 需求反馈单编号
            $pushData['Contact_telephone']       = $requirement->contact; // 联系人电话
            $pushData['Jinke_Feedback_person']   = zget($users, $this->app->user->account, ''); // 金科反馈人
            $pushData['Feedback_date']           = strtotime($nowTime) . '000'; // 反馈日期
            $pushData['Requirement_item_number'] = $oldRequirement->entriesCode; // 需求条目编号

            $method = zget($this->lang->requirement->methodList, $requirement->method);
            $method = str_replace('实现', '', $method);
            $pushData['Implementation_mode']       = $method; // 实现方式
            $pushData['Requirement_item_analysis'] = $requirement->analysis; // 需求条目分析
            $pushData['Handling_suggestions']      = $requirement->handling; // 处理建议
            $pushData['Implementation']            = $requirement->implement; // 实施情况

            $pushData['Planned_completion_time'] = (int)$pushData['Planned_completion_time'];
            $pushData['Feedback_date'] = (int)$pushData['Feedback_date'];
            $pushData['Project_team']  = trim($pushData['Project_team'], '/');

            $object     = 'requirement';
            $objectType = 'change';
            $request    = 'POST';
            $params     = $pushData;
            $response   = '';
            $status     = 'fail';
            $extra      = '';

            $result = $this->loadModel('requestlog')->http($url, $pushData, 'POST', 'json', array(), $headers);
            if(!empty($result))
            {
                $resultData = json_decode($result);
                if(isset($resultData->code) and $resultData->code == '200')
                {
                    $status = 'success';
                    $this->dao->update(TABLE_REQUIREMENT)->set('feedbackCode')->eq($resultData->data->Feedback_number)->where('id')->eq($requirementID)->exec();
                }

                $response = $result;
            }
            $this->requestlog->saveRequestLog($url, $object, $objectType, 'POST', $pushData, $response, $status, $extra);
        }

        return $this->createChanges($oldRequirement, $requirement);
    }

    /**
     * 判断是否是清总
     *
     * @param $createdBy
     * @return bool
     */
    public function getIsGuestcn($createdBy){
        $isGuestcn = false;
        if($createdBy == 'guestcn'){
            $isGuestcn = true;
        }
        return $isGuestcn;
    }

    public function change($requirementID)
    {
        $oldRequirement = $this->getByID($requirementID);
        if($oldRequirement->changeLock == 2){
            $response['result']  = 'fail';
            $response['message'] = $this->lang->requirement->changeIng;
            $this->send($response);
        }
        //必须选择变更事项才可提交
        if(!isset($_POST['alteration']))
        {
            dao::$errors = $this->lang->requirement->chooseAlteration;
            return;
        }
        $alterationData = $_POST['alteration'];
        $isGuestcn = $this->getIsGuestcn($oldRequirement->createdBy);
        if($isGuestcn){ //清总需求任务变更
            $checkRes = $this->checkIsAllowEditFeedbackEnd($oldRequirement, $this->app->user->account);
            if(!$checkRes['result']){
                dao::$errors[] =  $checkRes['message'];
                return;
            }
            //计划完成时间
            if(in_array('requirementEnd',$alterationData) && !$this->loadModel('common')->checkJkDateTime($_POST['changePlanEnd']))
            {
                dao::$errors['changePlanEnd'] =  sprintf($this->lang->requirement->error->empty, $this->lang->requirement->changePlanEnd);
                return;
            }
            //变更原因
            if(empty($_POST['changeReason'])){
                dao::$errors['changeReason'] =  sprintf($this->lang->requirement->error->empty, $this->lang->requirement->changeReason);
                return;
            }
            //产品经理和部门管理层必填
            if(empty($_POST['po'])){
                dao::$errors['manage'] =  sprintf($this->lang->requirement->error->empty, $this->lang->requirement->manage);
                return;
            }
            unset($_POST['affectDemand']);
        }else{ //非清总需求任务变更
            //变更后-需求任务主题
            if(in_array('changeTitle',$alterationData) && empty($_POST['changeTitle']))
            {
                dao::$errors['changeTitle'] =  sprintf($this->lang->requirement->error->empty, $this->lang->requirement->changeTitle);
                return;
            }
            //期望完成时间
            if(in_array('requirementDeadline',$alterationData) && !$this->loadModel('common')->checkJkDateTime($_POST['changeDeadline']))
            {
                dao::$errors['changeDeadline'] =  sprintf($this->lang->requirement->error->empty, $this->lang->requirement->changeDeadline);
                return;
            }
            //计划完成时间
            if(in_array('requirementEnd',$alterationData) && !$this->loadModel('common')->checkJkDateTime($_POST['changePlanEnd']))
            {
                dao::$errors['changePlanEnd'] =  sprintf($this->lang->requirement->error->empty, $this->lang->requirement->changePlanEnd);
                return;
            }

            /** 计划完成时间不能大于期望完成时间*/
            $hadRequirementDeadline = in_array('requirementDeadline',$alterationData);//期望完成时间 填写为true
            $hadRequirementEnd      = in_array('requirementEnd',$alterationData);//计划完成时间 填写为true
            //①期望完成时间和计划完成时间均填写
            if($hadRequirementEnd && $hadRequirementDeadline)
            {
                /*
                if(strtotime($_POST['changePlanEnd']) > strtotime($_POST['changeDeadline']))
                {
                    dao::$errors[''] =  $this->lang->requirement->editEndTip;
                    return;
                }
                */
            }
            //②计划完成时间填写，其完完成时间未填写。  用原期望完成时间作对比
            if($hadRequirementEnd && !$hadRequirementDeadline)
            {
                /*
                if(strtotime($_POST['changePlanEnd']) > strtotime($oldRequirement->deadLine))
                {
                    dao::$errors[''] =  $this->lang->requirement->editEndTip;
                    return;
                }
                */
            }

            //变更后-需求任务概述
            if(in_array('requirementOverview',$alterationData) && empty($_POST['changeOverview']))
            {
                dao::$errors['changeOverview'] =  sprintf($this->lang->requirement->error->empty, $this->lang->requirement->changeOverview);
                return;
            }
            //附件 如果没有选择附件则会有一个files字段，如果传递则无该字段
            if(in_array('requirementFile',$alterationData) && isset($_POST['files']))
            {
                dao::$errors['file'] =  sprintf($this->lang->requirement->error->empty, $this->lang->requirement->changeFile);
                return;
            }

            if($_POST['affectDemandCheck'] == 'yes' && !isset($_POST['affectDemand'])){
                dao::$errors['affectDemand'] =  sprintf($this->lang->requirement->error->empty, $this->lang->requirement->affectDemand);
                return;
            }

            if($_POST['affectDemandCheck'] == 'no'  && isset($_POST['affectDemand']))
            {
                unset($_POST['affectDemand']);
            }
            //变更原因
            if(empty($_POST['changeReason'])){
                dao::$errors['changeReason'] =  sprintf($this->lang->requirement->error->empty, $this->lang->requirement->changeReason);
                return;
            }
            //产品经理和部门管理层必填
            if(empty($_POST['po'])){
                dao::$errors['manage'] =  sprintf($this->lang->requirement->error->empty, $this->lang->requirement->manage);
                return;
            }
            if(empty($_POST['deptLeader'])){
                dao::$errors['deptLeader'] = sprintf($this->lang->requirement->error->empty, $this->lang->requirement->deptLeader);
                return;
            }
        }

        /* 处理入库数据 ①变更单数据 ②审批节点数据*/
        //①构造变更数据入库
        $postData = fixer::input('post')
            ->stripTags($this->config->requirement->editor->change['id'], $this->config->allowedTags)
            ->get();
        $postData = $this->loadModel('file')->processImgURL($postData, $this->config->requirement->editor->change['id'], $this->post->uid);
        $this->dealChangeDate($requirementID, (array)$postData, $oldRequirement);
        return true;
    }

    /**
     * @Notes:审批变更单 需更新requirement主表待处理人等信息，需要更新变更单表下一节点处理人，状态等信息
     * @Date: 2023/6/30
     * @Time: 13:43
     * @Interface reviewchange
     * @param $id
     * @param $requirementID
     */
    public function reviewchange($id,$requirementID)
    {
        /**
         * @var reviewModel $reviewModel
         * @var opinionModel $opinionModel
         * @var requirementModel $requirementModel
         */
        $post = fixer::input('post')->get();
        $changeInfo   = $this->getChangeInfoByChangeId($id);
        $reviewModel = $this->loadModel('review');
        $opinionModel = $this->loadModel('opinion');
        $requirementInfo  = $this->getByID($requirementID);

        $updateChangeInfo  = new stdClass();
        $updateRequirementInfo = new stdClass();
        if(empty($post->status))
        {
            dao::$errors['status'] =  sprintf($this->lang->requirement->error->empty, $this->lang->requirement->dealResult);
            return;
        }
        //获取受影响需求条目id集合
        $demandIDs = explode(',',$changeInfo->affectDemand);
        $affectsIdsList = [];


        switch ($changeInfo->nextDealNode)
        {

            //产品经理
            case $this->lang->requirement->changeReviewList['po']:
                //审核结果通过
                if($post->status == 'pass')
                {
                    $isGuestcn = $this->getIsGuestcn($requirementInfo->createdBy);
                    //更新当前节点状态
                    $reviewModel->check('requirementchange', $id, $changeInfo->version, 'pass', $this->post->comment);
                    if($isGuestcn){ //迭代三十四 清总变更计划结束时间产品经理审核通过即可
                        $requirementChangeTimes = $requirementInfo->requirementChangeTimes +1;//变更审批次数 审批通过才加1
                        //①构造变更单需更新的数据
                        $updateChangeInfo->nextDealUser = '';
                        $updateChangeInfo->nextDealNode = '';
                        $updateChangeInfo->status = 'pass';
                        //②构造requirement主表数据
                        $updateRequirementInfo->changeDealUser = '';
                        //$updateRequirementInfo->lastChangeTime = helper::now();
                        $updateRequirementInfo->requirementChangeStatus = 1;//标识变更审批完成
                        $updateRequirementInfo->changeLock = 1;
                        //$updateRequirementInfo->status = $requirementInfo->beforeStatus;
                        $updateRequirementInfo->beforeStatus = '';
                        $updateRequirementInfo->requirementChangeTimes  = $requirementChangeTimes;
                        $updateRequirementInfo->startTime  = helper::now();
                        //③处理附件问题
                        $this->dealFile($changeInfo);

                        //根据变更单的数据判断那些字段需要更新
                        $alteration = explode(',',$changeInfo->alteration);
                        if(in_array('changeTitle',$alteration))          $updateRequirementInfo->name = $changeInfo->changeTitle;
                        //期望完成时间
                        if(in_array('requirementDeadline',$alteration))  $updateRequirementInfo->deadLine = $changeInfo->changeDeadline;
                        //期望完成时间
                        if(in_array('requirementEnd',$alteration))  $updateRequirementInfo->planEnd = $changeInfo->changePlanEnd;
                        //变更后-需求任务概述
                        if(in_array('requirementOverview',$alteration))  $updateRequirementInfo->desc   = $changeInfo->changeOverview;
                        $affectsIdsList = $this->selectAffectIds($demandIDs);

                        $this->dao->begin();  //开启事务
                        $this->dao->update(TABLE_REQUIREMENT)->data($updateRequirementInfo)->where('id')->eq($requirementID)->exec();
                        //增加变更中流转状态
                        $this->loadModel('consumed')->record('requirement', $requirementID, 0, $this->app->user->account,'underchange',$requirementInfo->beforeStatus);

                        $this->dao->update(TABLE_REQUIREMENTCHANGEOUTSIDE)->data($updateChangeInfo)->where('id')->eq($id)->exec();
                        //④处理变更锁相关
                        if(!empty($affectsIdsList))
                        {
                            if(!empty($demandIDs)) $this->dao->update(TABLE_DEMAND)->set('changeLock')->eq(1)->where('id')->in($demandIDs)->exec();
                            //更新交付管理
                            $opinionModel->dealChangeLock($affectsIdsList,1);
                        }
                        $this->dao->commit();
                        $this->loadModel('action')->create('requirement', $requirementID, 'reviewchange', $this->post->comment,$changeInfo->changeCode.' 结果为：'.$this->lang->requirement->resultList['pass']);

                    }else{
                        /*构造部门管理层审批节点（待处理人为发起变更时选择的人员以及ningxiang作为处理人）*/
                        //①构造下一个审批节点数据
                        $reviewer = explode(',',$changeInfo->deptLeader);//待处理人为发起变更时选择的人员以及ningxiang作为处理人
                        $reviewStage = 2;
                        $param = array();
                        $param['nodeCode'] = 'deptLeader';
                        $reviewModel->addNode('requirementchange', $id, $changeInfo->version, $reviewer, true, 'pending',$reviewStage,$param);

                        //②构造变更单需更新的数据
                        $updateChangeInfo->reportLeader = 2;//迭代三十二 必须上报状态
                        $updateChangeInfo->nextDealUser = implode(',',$reviewer);
                        $updateChangeInfo->nextDealNode = $this->lang->requirement->changeReviewList['deptLeader'];

                        //③构造requirement主表数据
                        $updateRequirementInfo->changeDealUser = $changeInfo->deptLeader;
                        $this->dao->begin();  //开启事务
                        $this->dao->update(TABLE_REQUIREMENT)->data($updateRequirementInfo)->where('id')->eq($requirementID)->exec();
                        $this->dao->update(TABLE_REQUIREMENTCHANGEOUTSIDE)->data($updateChangeInfo)->where('id')->eq($id)->exec();
                        $this->dao->commit();
                        $this->loadModel('action')->create('requirement', $requirementID, 'reviewchange', $this->post->comment,$changeInfo->changeCode.' 结果为：'.$this->lang->requirement->resultList['pass']);
                    }

                }else{
                    /*审核不通过*/
                    //选择不通过，本次操作备注必填
                    if(empty($post->comment)){
                        dao::$errors['comment'] =  sprintf($this->lang->requirement->error->empty, $this->lang->requirement->suggestions);
                        return;
                    }
                    $reviewModel->check('requirementchange', $id, $changeInfo->version, 'reject', $this->post->comment);
                    //①构造变更单需更新的数据
                    $updateChangeInfo->nextDealUser = '';
                    $updateChangeInfo->nextDealNode = '';
                    $updateChangeInfo->status = 'back';

                    //②构造requirement主表数据
                    $updateRequirementInfo->changeDealUser = $changeInfo->createdBy;
                    $updateRequirementInfo->requirementChangeStatus = 3;//审批完成
                    $this->dao->begin();  //开启事务
                    $this->dao->update(TABLE_REQUIREMENT)->data($updateRequirementInfo)->where('id')->eq($requirementID)->exec();
                    $this->dao->update(TABLE_REQUIREMENTCHANGEOUTSIDE)->data($updateChangeInfo)->where('id')->eq($id)->exec();
                    $this->dao->commit();
                    $this->loadModel('action')->create('requirement', $requirementID, 'reviewchange', $this->post->comment,$changeInfo->changeCode.' 结果为：'.$this->lang->requirement->reviewList['reject']);
                }
                break;
            //部门管理层
            case $this->lang->requirement->changeReviewList['deptLeader']:
                if($post->status == 'pass')
                {
                    $nextDealUser = array_flip(explode(',',$changeInfo->nextDealUser));
                    unset($nextDealUser[$this->app->user->account]);
                    //更新当前节点状态
//                    $result = $reviewModel->checkRequirementAndOpinion('requirementchange', $id, $changeInfo->version, 2,$this->post->comment);
                    $result = $reviewModel->check('requirementchange', $id, $changeInfo->version, 'pass', $this->post->comment);
                    //判断是否全部通过
                    if($result == 'part') //部分通过
                    {
                        $nextDealUser = array_flip(explode(',',$changeInfo->nextDealUser));
                        unset($nextDealUser[$this->app->user->account]);
                        $insertDealUser = implode(',',array_keys($nextDealUser));
                        //①构造变更单需更新的数据
                        $updateChangeInfo->nextDealUser = $insertDealUser;
                        //②构造requirement主表数据
                        $updateRequirementInfo->changeDealUser = $insertDealUser;
                    }
                    if($result == 'pass')
                    {
                        $requirementChangeTimes = $requirementInfo->requirementChangeTimes +1;//变更审批次数 审批通过才加1
                        //①构造变更单需更新的数据
                        $updateChangeInfo->nextDealUser = '';
                        $updateChangeInfo->nextDealNode = '';
                        $updateChangeInfo->status = 'pass';
                        //②构造requirement主表数据
                        $updateRequirementInfo->changeDealUser = '';
                        $updateRequirementInfo->lastChangeTime = helper::now();
                        $updateRequirementInfo->requirementChangeStatus = 1;//标识变更审批完成
                        $updateRequirementInfo->changeLock = 1;
                        $updateRequirementInfo->status = $requirementInfo->beforeStatus;
                        $updateRequirementInfo->beforeStatus = '';
                        $updateRequirementInfo->requirementChangeTimes  = $requirementChangeTimes;
                        $updateRequirementInfo->startTime  = helper::now();
                        //③处理附件问题
                        $this->dealFile($changeInfo);

                        //根据变更单的数据判断那些字段需要更新
                        $alteration = explode(',',$changeInfo->alteration);
                        if(in_array('changeTitle',$alteration))          $updateRequirementInfo->name = $changeInfo->changeTitle;
                        //期望完成时间
                        if(in_array('requirementDeadline',$alteration))  $updateRequirementInfo->deadLine = $changeInfo->changeDeadline;
                        //期望完成时间
                        if(in_array('requirementEnd',$alteration))  $updateRequirementInfo->planEnd = $changeInfo->changePlanEnd;
                        //变更后-需求任务概述
                        if(in_array('requirementOverview',$alteration))  $updateRequirementInfo->desc   = $changeInfo->changeOverview;
                        $affectsIdsList = $this->selectAffectIds($demandIDs);
                    }
                    $this->dao->begin();  //开启事务
                    $this->dao->update(TABLE_REQUIREMENT)->data($updateRequirementInfo)->where('id')->eq($requirementID)->exec();
                    //增加变更中流转状态
                    $this->loadModel('consumed')->record('requirement', $requirementID, 0, $this->app->user->account,'underchange',$requirementInfo->beforeStatus);

                    $this->dao->update(TABLE_REQUIREMENTCHANGEOUTSIDE)->data($updateChangeInfo)->where('id')->eq($id)->exec();
                    //④处理变更锁相关
                    if(!empty($affectsIdsList))
                    {
                        if(!empty($demandIDs)) $this->dao->update(TABLE_DEMAND)->set('changeLock')->eq(1)->where('id')->in($demandIDs)->exec();
                        //更新交付管理
                        $opinionModel->dealChangeLock($affectsIdsList,1);
                    }
                    $this->dao->commit();
                    $this->loadModel('action')->create('requirement', $requirementID, 'reviewchange', $this->post->comment,$changeInfo->changeCode.' 结果为：'.$this->lang->requirement->resultList['pass']);
                }else{
                    /*审核不通过*/
                    //选择不通过，本次操作备注必填
                    if(empty($post->comment)){
                        dao::$errors['comment'] =  sprintf($this->lang->requirement->error->empty, $this->lang->requirement->suggestions);
                        return;
                    }
                    $reviewModel->check('requirementchange', $id, $changeInfo->version, 'reject', $this->post->comment);
                    //①构造变更单需更新的数据
                    $updateChangeInfo->nextDealUser = '';
                    $updateChangeInfo->nextDealNode = '';
                    $updateChangeInfo->status = 'back';

                    //②构造requirement主表数据
                    $updateRequirementInfo->changeDealUser = $changeInfo->createdBy;
                    $updateRequirementInfo->requirementChangeStatus = 3;//审批完成
                    $this->dao->begin();  //开启事务
                    $this->dao->update(TABLE_REQUIREMENT)->data($updateRequirementInfo)->where('id')->eq($requirementID)->exec();
                    $this->dao->update(TABLE_REQUIREMENTCHANGEOUTSIDE)->data($updateChangeInfo)->where('id')->eq($id)->exec();
                    $this->dao->commit();
                    $this->loadModel('action')->create('requirement', $requirementID, 'reviewchange', $this->post->comment,$changeInfo->changeCode.' 结果为：'.$this->lang->requirement->reviewList['reject']);
                }
                break;
        }
    }

    public function reviewchangeCopy($id,$requirementID)
    {
        /**
         * @var reviewModel $reviewModel
         * @var opinionModel $opinionModel
         * @var requirementModel $requirementModel
         * @var demandModel $demandModel
         */
        $post = fixer::input('post')->get();
        $changeInfo   = $this->getChangeInfoByChangeId($id);
        $reviewModel = $this->loadModel('review');
        $opinionModel = $this->loadModel('opinion');
        $demandModel = $this->loadModel('demand');
        $requirementInfo  = $this->getByID($requirementID);

        $updateChangeInfo  = new stdClass();
        $updateRequirementInfo = new stdClass();
        if(empty($post->status))
        {
            dao::$errors['status'] =  sprintf($this->lang->requirement->error->empty, $this->lang->requirement->dealResult);
            return;
        }
        //获取需求条目id集合
        $demandsInfo = $demandModel->getDemandsByRequirementIds($requirementID,'id');
        $demandIDs = array_column($demandsInfo,'id');
        $affectsIdsList = [];

        switch ($changeInfo->nextDealNode)
        {
            //产品经理
            case $this->lang->requirement->changeReviewList['po']:
                //审核结果通过
                if($post->status == 'pass')
                {
                    //更新当前节点状态
                    $reviewModel->check('requirementchange', $id, $changeInfo->version, 'pass', $this->post->comment);
                    $requirementChangeTimes = $requirementInfo->requirementChangeTimes +1;//变更审批次数 审批通过才加1
                    if(isset($post->reportLeader))
                    {
                        /*选择上报部门管理层（待处理人为发起变更时选择的人员以及ningxiang作为处理人）*/
                        //①构造下一个审批节点数据
                        $reviewModel = $this->loadModel('review');
                        $reviewer = explode(',',$changeInfo->deptLeader);//待处理人为发起变更时选择的人员以及ningxiang作为处理人
                        $reviewStage = 2;
                        $param = array();
                        $param['nodeCode'] = 'deptLeader';
                        $reviewModel->addNode('requirementchange', $id, $changeInfo->version, $reviewer, true, 'pending',$reviewStage,$param);

                        //②构造变更单需更新的数据
                        $updateChangeInfo->reportLeader = 2;//更新变更单是否上报状态
                        $updateChangeInfo->nextDealUser = implode(',',$reviewer);
                        $updateChangeInfo->nextDealNode = $this->lang->requirement->changeReviewList['deptLeader'];

                        //③构造requirement主表数据
                        $updateRequirementInfo->changeDealUser = $changeInfo->deptLeader;
                        $this->dao->begin();  //开启事务
                        $this->dao->update(TABLE_REQUIREMENT)->data($updateRequirementInfo)->where('id')->eq($requirementID)->exec();
                        $this->dao->update(TABLE_REQUIREMENTCHANGEOUTSIDE)->data($updateChangeInfo)->where('id')->eq($id)->exec();
                        $this->loadModel('action')->create('requirement', $requirementID, 'reviewchange', $this->post->comment,$changeInfo->changeCode.' 结果为：'.$this->lang->requirement->resultList['pass']);
                        $this->dao->commit();
                    }else{
                        /*不选择上报部门领导,则流程节点结束 ①需更新主表状态、需求最新变更时间、待处理人置空、变更次数。 无需新增流程节点*/
                        //①构造变更单需更新的数据
                        $updateChangeInfo->nextDealUser = '';
                        $updateChangeInfo->nextDealNode = '';
                        $updateChangeInfo->status = 'pass';

                        //②构造requirement主表数据
                        $updateRequirementInfo->changeDealUser = '';
                        $updateRequirementInfo->lastChangeTime = helper::now();
                        $updateRequirementInfo->requirementChangeStatus = 1;//标识变更审批完成
                        $updateRequirementInfo->changeLock = 1;
                        $updateRequirementInfo->requirementChangeTimes  = $requirementChangeTimes;

                        //根据变更单的数据判断那些字段需要更新
                        $alteration = explode(',',$changeInfo->alteration);
                        if(in_array('changeTitle',$alteration))          $updateRequirementInfo->name = $changeInfo->changeTitle;
                        //期望完成时间
                        if(in_array('requirementDeadline',$alteration))  $updateRequirementInfo->deadLine = $changeInfo->changeDeadline;
                        //变更后-需求任务概述
                        if(in_array('requirementOverview',$alteration))  $updateRequirementInfo->desc   = $changeInfo->changeOverview;

                        $this->dao->begin();  //开启事务
                        //③处理附件问题
                        $this->dealFile($changeInfo);
                        $this->dao->update(TABLE_REQUIREMENT)->data($updateRequirementInfo)->where('id')->eq($requirementID)->exec();
                        $this->dao->update(TABLE_REQUIREMENTCHANGEOUTSIDE)->data($updateChangeInfo)->where('id')->eq($id)->exec();

                        //④处理变更锁相关
                        $affectsIdsList = $this->selectAffectIds($demandIDs);
                        if(!empty($affectsIdsList))
                        {
                            if(!empty($demandIDs)) $this->dao->update(TABLE_DEMAND)->set('changeLock')->eq(1)->where('id')->in($demandIDs)->exec();
                            //更新交付管理
                            $opinionModel->dealChangeLock($affectsIdsList,1);
                        }

                        $this->loadModel('action')->create('requirement', $requirementID, 'reviewchange', $this->post->comment,$changeInfo->changeCode.' 结果为：'.$this->lang->requirement->resultList['pass']);
                        $this->dao->commit();
                    }

                }else{
                    /*审核不通过*/
                    //选择不通过，本次操作备注必填
                    if(empty($post->comment)){
                        dao::$errors['comment'] =  sprintf($this->lang->requirement->error->empty, $this->lang->requirement->suggestions);
                        return;
                    }
                    $reviewModel->check('requirementchange', $id, $changeInfo->version, 'reject', $this->post->comment);
                    //①构造变更单需更新的数据
                    $updateChangeInfo->nextDealUser = '';
                    $updateChangeInfo->nextDealNode = '';
                    $updateChangeInfo->status = 'back';

                    //②构造requirement主表数据
                    $updateRequirementInfo->changeDealUser = $changeInfo->createdBy;
                    $updateRequirementInfo->requirementChangeStatus = 3;//审批完成
                    $this->dao->begin();  //开启事务
                    $this->dao->update(TABLE_REQUIREMENT)->data($updateRequirementInfo)->where('id')->eq($requirementID)->exec();
                    $this->dao->update(TABLE_REQUIREMENTCHANGEOUTSIDE)->data($updateChangeInfo)->where('id')->eq($id)->exec();
                    $this->loadModel('action')->create('requirement', $requirementID, 'reviewchange', $this->post->comment,$changeInfo->changeCode.' 结果为：'.$this->lang->requirement->reviewList['reject']);
                    $this->dao->commit();
                }
                break;
            //部门管理层
            case $this->lang->requirement->changeReviewList['deptLeader']:
                if($post->status == 'pass')
                {
                    $nextDealUser = array_flip(explode(',',$changeInfo->nextDealUser));
                    unset($nextDealUser[$this->app->user->account]);
                    $insertDealUser = implode('',array_keys($nextDealUser));

                    //更新当前节点状态
                    $result = $reviewModel->checkRequirementAndOpinion('requirementchange', $id, $changeInfo->version, 2,$this->post->comment);
//                    $result = $reviewModel->check('requirementchange', $id, $changeInfo->version, 'pass', $this->post->comment);
                    //判断是否全部通过
                    if($result == 'part') //部分通过
                    {
                        $nextDealUser = array_flip(explode(',',$changeInfo->nextDealUser));
                        unset($nextDealUser[$this->app->user->account]);
                        $insertDealUser = implode('',array_keys($nextDealUser));
                        //①构造变更单需更新的数据
                        $updateChangeInfo->nextDealUser = $insertDealUser;
                        //②构造requirement主表数据
                        $updateRequirementInfo->changeDealUser = $insertDealUser;
                    }
                    if($result == 'pass')
                    {
                        $requirementChangeTimes = $requirementInfo->requirementChangeTimes +1;//变更审批次数 审批通过才加1
                        //①构造变更单需更新的数据
                        $updateChangeInfo->nextDealUser = '';
                        $updateChangeInfo->nextDealNode = '';
                        $updateChangeInfo->status = 'pass';
                        //②构造requirement主表数据
                        $updateRequirementInfo->changeDealUser = $changeInfo->createdBy;
                        $updateRequirementInfo->lastChangeTime = helper::now();
                        $updateRequirementInfo->requirementChangeStatus = 1;//标识变更审批完成
                        $updateRequirementInfo->changeLock = 1;
                        $updateRequirementInfo->requirementChangeTimes  = $requirementChangeTimes;
                        //③处理附件问题
                        $this->dealFile($changeInfo);

                        //根据变更单的数据判断那些字段需要更新
                        $alteration = explode(',',$changeInfo->alteration);
                        if(in_array('changeTitle',$alteration))          $updateRequirementInfo->name = $changeInfo->changeTitle;
                        //期望完成时间
                        if(in_array('requirementDeadline',$alteration))  $updateRequirementInfo->deadLine = $changeInfo->changeDeadline;
                        //变更后-需求任务概述
                        if(in_array('requirementOverview',$alteration))  $updateRequirementInfo->desc   = $changeInfo->changeOverview;
                        $affectsIdsList = $this->selectAffectIds($demandIDs);
                    }
                    $this->dao->begin();  //开启事务
                    $this->dao->update(TABLE_REQUIREMENT)->data($updateRequirementInfo)->where('id')->eq($requirementID)->exec();
                    $this->dao->update(TABLE_REQUIREMENTCHANGEOUTSIDE)->data($updateChangeInfo)->where('id')->eq($id)->exec();
                    //④处理变更锁相关
                    if(!empty($affectsIdsList))
                    {
                        if(!empty($demandIDs)) $this->dao->update(TABLE_DEMAND)->set('changeLock')->eq(1)->where('id')->in($demandIDs)->exec();
                        //更新交付管理
                        $opinionModel->dealChangeLock($affectsIdsList,1);
                    }
                    $this->loadModel('action')->create('requirement', $requirementID, 'reviewchange', $this->post->comment,$changeInfo->changeCode.' 结果为：'.$this->lang->requirement->resultList['pass']);
                    $this->dao->commit();
                }else{
                    /*审核不通过*/
                    //选择不通过，本次操作备注必填
                    if(empty($post->comment)){
                        dao::$errors['comment'] =  sprintf($this->lang->requirement->error->empty, $this->lang->requirement->suggestions);
                        return;
                    }
                    $reviewModel->check('requirementchange', $id, $changeInfo->version, 'reject', $this->post->comment);
                    //①构造变更单需更新的数据
                    $updateChangeInfo->nextDealUser = '';
                    $updateChangeInfo->nextDealNode = '';
                    $updateChangeInfo->status = 'back';

                    //②构造requirement主表数据
                    $updateRequirementInfo->changeDealUser = $changeInfo->createdBy;
                    $updateRequirementInfo->requirementChangeStatus = 3;//审批完成
                    $this->dao->begin();  //开启事务
                    $this->dao->update(TABLE_REQUIREMENT)->data($updateRequirementInfo)->where('id')->eq($requirementID)->exec();
                    $this->dao->update(TABLE_REQUIREMENTCHANGEOUTSIDE)->data($updateChangeInfo)->where('id')->eq($id)->exec();
                    $this->loadModel('action')->create('requirement', $requirementID, 'reviewchange', $this->post->comment,$changeInfo->changeCode.' 结果为：'.$this->lang->requirement->reviewList['reject']);
                    $this->dao->commit();
                }
                break;
        }


//        $this->loadModel('consumed')->record('requirement', $id, 0, $this->app->user->account, $oldRequirement->status, $requirement->status);
    }

    /**
     * @Notes:变更审核通过附件处理
     * @Date: 2023/8/23
     * @Time: 18:16
     * @Interface dealFile
     * @param $changeInfo
     */
    public function dealFile($changeInfo)
    {
        /**
         * @var fileModel $fileModel
         */
        $fileModel = $this->loadModel('file');
        if(!empty($changeInfo->changeFile))
        {
            $updateFilesIds = explode(',',$changeInfo->changeFile);
            $fileModel->updateFileObjectType($changeInfo->requirementID,'requirement',$updateFilesIds);
            if(!empty($changeInfo->requirementFile))
            {
                $fileDeleteIds = explode(',',$changeInfo->requirementFile);
                $fileModel->deleteAllFile($fileDeleteIds);
            }
        }
    }

    /**
     * @Notes:撤销变更
     * @Date: 2023/6/26
     * @Time: 18:14
     * @Interface revoke
     * @param $changeID
     */
    public function revoke($changeID)
    {
        /**
         * @var demandModel $demandModel
         * @var opinionModel $opinionModel
         */
        $demandModel = $this->loadModel('demand');
        $opinionModel = $this->loadModel('opinion');
        $changeInfo = $this->dao->select('*')->from(TABLE_REQUIREMENTCHANGEOUTSIDE)->where('id')->eq($changeID)->fetch();
        $requirementInfo  = $this->getByID($changeInfo->requirementID);

        //只有审核中允许撤销
        if(!in_array($changeInfo->status,['back']))
        {
            dao::$errors = $this->lang->requirement->revokeAlert;
            return;
        }

        if(empty($_POST['revokeRemark'])){
            dao::$errors['revokeRemark'] =  sprintf($this->lang->requirement->error->empty, $this->lang->requirement->revokeComment);
            return;
        }
        //构造变更数据
        $data = new stdClass();
        $data->revokeRemark = $this->post->revokeRemark;
        $data->revokeDate   = helper::now();
        $data->status       = 'revoke';

        //构造主表数据
        $requirementData = new stdClass();
        $requirementData->requirementChangeStatus = 1;
        $requirementData->changeDealUser = '';
        $requirementData->changeLock = 1;
        $requirementData->status = $requirementInfo->beforeStatus;
        $requirementData->beforeStatus = '';

        //处理变更锁相关
        $demandsInfo = $demandModel->getDemandsByRequirementIds($changeInfo->requirementID,'id');
        $demandIDs = array_column($demandsInfo,'id');
        $affectsIdsList = [];
        if(empty(!$demandIDs))
        {
            $affectsIdsList = $this->selectAffectIds($demandIDs);
        }
        $this->dao->begin();
        $this->dao->update(TABLE_REQUIREMENTCHANGEOUTSIDE)->data($data)->where('id')->eq($changeID)->exec();
        //更新完成状态
        $this->dao->update(TABLE_REQUIREMENT)->data($requirementData)->where('id')->eq($changeInfo->requirementID)->exec();
        //增加变更中流转状态
        $this->loadModel('consumed')->record('requirement', $requirementInfo->id, 0, $this->app->user->account, 'underchange',$requirementInfo->beforeStatus);
        if(!empty($demandIDs)) $this->dao->update(TABLE_DEMAND)->set('changeLock')->eq(1)->where('id')->in($demandIDs)->exec();
        if(!empty($affectsIdsList))
        {
            //更新交付管理
            $opinionModel->dealChangeLock($affectsIdsList,1);
        }
        $this->dao->commit();
        return true;
    }

    /**
     * @Notes: 处理变数据
     * @Date: 2023/6/26
     * @Time: 15:22
     * @Interface dealChangeDate
     * @param $requirementID
     * @param $postData
     * @param $oldRequirement
     */
    public function dealChangeDate($requirementID,$postData,$oldRequirement)
    {
        $this->app->loadLang('demand');
        $this->app->loadConfig('set');

        //若有在途的生产变更单，则提示不可变更
        $affectDemand = isset($postData['affectDemand']) ? implode(',',$postData['affectDemand']) : '';
        if(!empty($affectDemand))
        {
            $codeTip = $this->checkAllowChange($affectDemand);
            if($codeTip)
            {
                $codeTip = implode(',',$codeTip);
                dao::$errors[] = "受影响需求条目".$codeTip."存在在途交付流程，若该条目涉及需求变更，请先取消在途交付流程后才可发起需求变更。";
                return;
            }

        }

        $changeInfo = $this->getChangeInfoByRequirementId($requirementID);
        $version = count($changeInfo) + 1;
        //自定义配置部门管理层审核人 必须审核
        $this->app->loadLang('demand');
        $deptReviewer = $this->lang->demand->deptReviewList['reviewer'];
        $deptLeader = implode(',',array_unique(array_merge($postData['deptLeader'],[$deptReviewer])));

        $data = new stdClass();
        $data->requirementID        = $requirementID;
        $data->alteration           = implode(',',$postData['alteration']);
        $data->requirementTitle     = $oldRequirement->name;
        $data->changeTitle          = $postData['changeTitle'];
        $data->requirementOverview  = $oldRequirement->desc;
        $data->changeOverview       = $postData['changeOverview'];
        $data->requirementDeadline  = $oldRequirement->deadLine;
        $data->changeDeadline       = $postData['changeDeadline'];
        $data->requirementEnd       = $oldRequirement->planEnd;
        $data->changePlanEnd        = $postData['changePlanEnd'];
        $data->changeReason         = $postData['changeReason'];
        $data->po                   = $postData['po'];
        $data->deptLeader           = $deptLeader;
        $data->nextDealUser         = $postData['po'];
        $data->nextDealNode         = 'po';
        $data->status               = 'pending';//默认审核中
        $data->version              = $version;
        $data->createdBy            = $this->app->user->account;
        $data->createdDate          = helper::now();
        $data->affectDemand         = isset($postData['affectDemand']) ? implode(',',$postData['affectDemand']) : '';
        $this->dao->begin();  //开启事务

        if($oldRequirement->changeLock == 2){
            $response['result']  = 'fail';
            $response['message'] = $this->lang->requirement->changeIng;
            $this->send($response);
        }

        //入库变更主表数据数据
        $this->dao->insert(TABLE_REQUIREMENTCHANGEOUTSIDE)->data($data)->exec();
        //变更单号处理
        $requirementChangeId = $this->dao->lastInsertID();
        $changeCode = $oldRequirement->code .'-'. sprintf('%02d', count($changeInfo)+1);
        $this->dao->update(TABLE_REQUIREMENTCHANGEOUTSIDE)->set('changeCode')->eq($changeCode)->where('id')->eq($requirementChangeId)->exec();

        /* @var fileModel $fileModel
         * 处理附件
         */
        $fileModel = $this->loadModel('file');
        //获取原附件id集合
        $filesBefore = $fileModel->getFileInfo('requirement',$requirementID);
        $requirementFile = '';
        if(!empty($filesBefore))
        {
            $requirementFile = implode(',',array_column($filesBefore,'id'));
        }

        $this->file->saveUpload('rtchangeoutside', $requirementChangeId);
        $this->loadModel('file')->updateObjectID($this->post->uid, $requirementChangeId, 'rtchangeoutside');

        //变更后附件
        $changeFileInfo = $fileModel->getFileInfo('rtchangeoutside',$requirementChangeId);
        if(!empty($changeFileInfo))
        {
            $changeFile = implode(',',array_column($changeFileInfo,'id'));
            $this->dao->update(TABLE_REQUIREMENTCHANGEOUTSIDE)->set('requirementFile')->eq($requirementFile)->set('changeFile')->eq($changeFile)->where('id')->eq($requirementChangeId)->exec();
        }

        //②入库审批节点数据
        /**@var reviewModel $reviewModel */
        $reviewModel = $this->loadModel('review');
        $reviewer = array($postData['po']);
        $reviewStage = 1;
        $param = array();
        $param['nodeCode'] = $this->lang->requirement->changeReviewList['po']; //用nodeCode标识审批节点，第一个节点是产品经理
        $reviewModel->addNode('requirementchange', $requirementChangeId, $version, $reviewer, true, 'pending',$reviewStage,$param);

        //③更新requirement主表 变更单待处理人
        $requirementData = new stdClass();
        $requirementData->changeDealUser = $postData['po'];
        $requirementData->requirementChangeStatus = 2;
        //$requirementData->status = 'underchange';
        $isGuestcn = $this->getIsGuestcn($oldRequirement->createdBy);

        if(!$isGuestcn){ //非清总变更
            $requirementData->status = 'underchange';
            if(isset($this->config->changeSwitch) && $this->config->changeSwitch == 1)
            {
                $requirementData->changeLock = 2; //增加变更锁
            }
        }
        $requirementData->beforeStatus = $oldRequirement->status;

        $this->dao->update(TABLE_REQUIREMENT)->data($requirementData)->where('id')->eq($requirementID)->exec();
        if(!$isGuestcn) { //非清总变更
            //增加变更中流转状态
            $this->loadModel('consumed')->record('requirement', $requirementID, 0, $this->app->user->account, $oldRequirement->status, 'underchange');
        }

        /**
         * @var opinionModel $opinionModel
         */
        $opinionModel = $this->loadModel('opinion');
        //迭代三十二： 涉及需求条目则增加变更锁
        if($postData['affectDemandCheck'] == 'yes')
        {
            $demandIDs = $postData['affectDemand'];
            $affectsIdsList = $this->selectAffectIds($demandIDs); //受影响任务相关ids集合
            if(!empty($affectsIdsList) && isset($this->config->changeSwitch) && $this->config->changeSwitch == 1)
            {
                $this->dao->update(TABLE_DEMAND)->set('changeLock')->eq(2)->where('id')->in($demandIDs)->exec();
                //更新交付管理 加锁
                $opinionModel->dealChangeLock($affectsIdsList,2);
            }
        }

        if(!$isGuestcn){ //非清总变更
            //当前登录人为第一节点产品经理，默认审核通过 非清总同步
            if($this->app->user->account == $postData['po'])
            {
                $this->poDefaultPassNode($requirementChangeId,$requirementID);
            }
        }

        $this->dao->commit();
        return true;

    }

    /**
     * @Notes:产品经理提交默认通过
     * @Date: 2024/5/20
     * @Time: 16:54
     * @Interface poDefaultPassNode
     * @param $id
     * @param $requirementID
     */
    public function poDefaultPassNode($id,$requirementID)
    {
        /**
         * @var reviewModel $reviewModel
         * @var requirementModel $requirementModel
         * @var reviewModel $reviewModel
         */
        $changeInfo   = $this->getChangeInfoByChangeId($id);
        $reviewModel = $this->loadModel('review');

        $updateChangeInfo  = new stdClass();
        $updateRequirementInfo = new stdClass();
        //更新当前节点状态
        $reviewModel->check('requirementchange', $id, $changeInfo->version, 'pass', '');
        /*构造部门管理层审批节点（待处理人为发起变更时选择的人员以及ningxiang作为处理人）*/
        //①构造下一个审批节点数据
        $reviewModel = $this->loadModel('review');
        $reviewer = explode(',',$changeInfo->deptLeader);//待处理人为发起变更时选择的人员以及ningxiang作为处理人
        $reviewStage = 2;
        $param = array();
        $param['nodeCode'] = 'deptLeader';
        $reviewModel->addNode('requirementchange', $id, $changeInfo->version, $reviewer, true, 'pending',$reviewStage,$param);

        //②构造变更单需更新的数据
        $updateChangeInfo->reportLeader = 2;//迭代三十二 必须上报状态
        $updateChangeInfo->nextDealUser = implode(',',$reviewer);
        $updateChangeInfo->nextDealNode = $this->lang->requirement->changeReviewList['deptLeader'];

        //③构造requirement主表数据
        $updateRequirementInfo->changeDealUser = $changeInfo->deptLeader;
        $this->dao->update(TABLE_REQUIREMENT)->data($updateRequirementInfo)->where('id')->eq($requirementID)->exec();
        $this->dao->update(TABLE_REQUIREMENTCHANGEOUTSIDE)->data($updateChangeInfo)->where('id')->eq($id)->exec();

    }

    /**
     * @Notes: 在途生产变更单数据构造
     * @Date: 2023/12/6
     * @Time: 11:14
     * @Interface checkAllowChange
     * @param $affectDemand
     * @return array
     */
    public function checkAllowChange($affectDemand)
    {
        /**
         * @var demandModel $demandModel
         * @var modifyModel $modifyModel
         * @var outwarddeliveryModel $outwardDeliveryModel
         * @var modifycnccModel $modifycnccModel
         */
        $demandModel = $this->loadModel('demand');
        $modifyModel = $this->loadModel('modify');
        $outwardDeliveryModel = $this->loadModel('outwarddelivery');
        $modifyCnccModel = $this->loadModel('modifycncc');
        /*终态包括 变更异常、变更取消、变更回退、变更成功、部分成功、变更失败、已关闭*/
        $codeTip = [];
        if(!empty($affectDemand))
        {
            $demandIDs = explode(',',$affectDemand);
            $demandInfo = $demandModel->getPairsByIds($demandIDs);
            foreach ($demandInfo as $demandId => $demand)
            {
                $affectsIdsList = $this->selectAffectIds($demandId); //受影响任务相关ids集合
                if(!empty($affectsIdsList))
                {
                    $modifyIds = $affectsIdsList['modifyIds'];
                    $outwardDeliveryIdsIds = $affectsIdsList['outwardDeliveryIdsIds'];
                    //金信生产变更单
                    if(!empty($modifyIds))
                    {
                        $modifyInfo = $modifyModel->getByIds($modifyIds,'id,code,status');
                        foreach ($modifyInfo as $key => $value)
                        {
                            if(in_array($value->status,$this->lang->modify->transitList))
                            {
                                unset($modifyInfo[$key]);
                            }
                        }
                        if($modifyInfo)
                        {
                            $modifyCode = $demand['code'].'('.implode(',', array_column($modifyInfo,'code')).')';
                            $codeTip[] = $modifyCode;
                        }

                    }
                    //清总生产变更单
                    if(!empty($outwardDeliveryIdsIds))
                    {
                        $outwardDeliveryInfo = $outwardDeliveryModel->getByids($outwardDeliveryIdsIds,'id,modifycnccId');
                        if($outwardDeliveryInfo)
                        {
                            $modifycnccIds = array_column($outwardDeliveryInfo,'modifycnccId');
                            $modifycnccInfo = $modifyCnccModel->getByIds($modifycnccIds,'id,code,status');
                            foreach ($modifycnccInfo as $key => $value)
                            {
                                if(in_array($value->status,$this->lang->modifycncc->transitList))
                                {
                                    unset($modifycnccInfo[$key]);
                                }
                            }
                            if($modifycnccInfo)
                            {
                                $modifycnccCode = $demand['code'].'('.implode(',', array_column($modifycnccInfo,'code')).')';
                                $codeTip[] = $modifycnccCode;
                            }

                        }
                    }
                }
            }

        }

        return $codeTip;

    }

    /**
     * @Notes:所属意向如果发起变更且该任务受影响则不允许再次发起
     * @Date: 2023/12/6
     * @Time: 15:51
     * @Interface followChange
     * @param $requirementID
     * @param $opinionID
     * @return bool
     */
    public function followChange($requirementID,$opinionID)
    {
        $followOpinion = true;
        $opinionInfo = $this->dao->select('id,changeLock')->from(TABLE_OPINION)->where('id')->eq($opinionID)->fetch();
        $opinionChangeInfo = $this->dao->select('*')->from(TABLE_OPINIONCHANGE)
            ->where("version = (select max(version) from zt_opinionchange where opinionID = '{$opinionID}')")
            ->andWhere('opinionID')->eq($opinionID)
            ->andWhere('FIND_IN_SET("'.$requirementID.'",affectRequirement)')
            ->fetch();
        if($opinionInfo->changeLock == 2 && $opinionChangeInfo)
        {
            $followOpinion = false;
        }
        return $followOpinion;
    }

    /**
     * @Notes: 编辑退回的变更单
     * @Date: 2023/7/13
     * @Time: 11:03
     * @Interface dealEditChange
     * @param $changeID
     * @param $postData
     * @param $requirementID
     * @return array
     */
    public function dealEditChange($changeID,$postData,$requirementID)
    {
        /**
         * @var opinionModel $opinionModel
         * @var fileModel $fileModel
         */
        $this->app->loadConfig('set');
        $opinionModel = $this->loadModel('opinion');
        $this->app->loadLang('demand');
        $fileModel = $this->loadModel('file');
        $changeInfo = $this->getChangeInfoByChangeId($changeID);
        $requirementCreated = $this->dao->select('id,createdBy')->from(TABLE_REQUIREMENT)->where('id')->eq($requirementID)->fetch();
        $version = $changeInfo->version;
        //编辑后数据构造
        $alteration = $postData['alteration'];
        //自定义配置部门管理层审核人 必须审核
        $deptReviewer = $this->lang->demand->deptReviewList['reviewer'];
        $deptLeader = implode(',',array_unique(array_merge($postData['deptLeader'],[$deptReviewer])));

        $data = new stdClass();

        if(in_array('changeTitle',$alteration))
        {
            $data->changeTitle  = $postData['changeTitle'];
        }else{
            $data->changeTitle = '';
        }
        //变更后-需求任务概述
        if(in_array('requirementOverview',$alteration))
        {
            $data->changeOverview = $postData['changeOverview'];
        }else{
            $data->changeOverview = '';
        }
        //期望完成时间
        if(in_array('requirementDeadline',$alteration))
        {
            $data->changeDeadline  = $postData['changeDeadline'];
        }else{
            $data->changeDeadline = '';
        }
        //期望完成时间
        if(in_array('requirementEnd',$alteration))
        {
            $data->changePlanEnd  = $postData['changePlanEnd'];
        }else{
            $data->changePlanEnd = '';
        }
        $affectDemand = isset($postData['affectDemand']) ? implode(',',$postData['affectDemand']) : '';
        if(!empty($affectDemand))
        {
            $codeTip = $this->checkAllowChange($affectDemand);
            if($codeTip)
            {
                $codeTip = implode(',',$codeTip);
                dao::$errors[] = "受影响需求条目".$codeTip."存在在途交付流程，若该条目涉及需求变更，请先取消在途交付流程后才可发起需求变更。";
                return;
            }

        }


        $nextDealUser = $postData['po'];
        $nextDealNode = 'po';
        if($requirementCreated->createdBy != 'guestcn'){ //非清总变更
            //当前登录人为第一节点产品经理，默认审核通过 非清总同步
            if($this->app->user->account == $postData['po'])
            {
                $nextDealUser = $deptLeader;
                $nextDealNode = 'deptLeader';
            }
        }

        $data->requirementID        = $requirementID;
        $data->alteration           = implode(',',$postData['alteration']);
        $data->changeReason         = $postData['changeReason'];
        $data->po                   = $postData['po'];
        $data->deptLeader           = $deptLeader;
        $data->nextDealUser         = $nextDealUser;
        $data->nextDealNode         = $nextDealNode;
        $data->status               = 'pending';//默认审核中
        $data->version              = $version;
        $data->createdBy            = $this->app->user->account;
        $data->createdDate          = helper::now();
        $data->affectDemand         = $affectDemand;

        /*受影响需求条目发生变化，需要将之前的条目进行解锁,新的加变更锁*/
        $affectsIdsListOld = [];
        $affectsIdsListNew = [];
        if($changeInfo->affectDemand != $affectDemand){
            $affectsIdsListOld = $this->selectAffectIds($changeInfo->affectDemand);
            $affectsIdsListNew = $this->selectAffectIds($affectDemand);
        }
        $this->dao->begin();  //开启事务
        if(isset($this->config->changeSwitch) && $this->config->changeSwitch == 1) //变更总开关是打开状态
        {
            //旧条目以及相应二线解除变更锁
            if(!empty($affectsIdsListOld))
            {
                $this->dao->update(TABLE_DEMAND)->set('changeLock')->eq(1)->where('id')->in($changeInfo->affectDemand)->exec();
                //解除交付管理锁
                $opinionModel->dealChangeLock($affectsIdsListOld,1);
            }

            //新条目以及相应二线加变更锁
            if(!empty($affectsIdsListNew))
            {
                $this->dao->update(TABLE_DEMAND)->set('changeLock')->eq(2)->where('id')->in($affectDemand)->exec();
                //加交付管理锁
                $opinionModel->dealChangeLock($affectsIdsListNew,2);
            }
        }

        //①更新变更数据表
        $this->dao->update(TABLE_REQUIREMENTCHANGEOUTSIDE)->data($data)->where('id')->eq($changeID)->exec();

        //②更新审批节点
        /**@var reviewModel $reviewModel */
        $reviewModel = $this->loadModel('review');
        $backNodeInfo = $reviewModel->getNodes('requirementchange', $changeID, $version);
        $nodeInfo = array_column($backNodeInfo,'id');

        //清总变更 只有产品经理审批
//        $reviewer = $postData['po'];
//        if($requirementCreated->createdBy == 'guestcn'){
//            $reviewer = $deptLeader;
//            $nodeID = $nodeInfo[1];
//        }

        //1、更新reviewnode数据构造
        $nodeID = $nodeInfo[0];
        $updateNode = new stdClass();
        $updateNode->status = 'pending';
        $updateNode->createdDate = helper::today();

        //2、更新reviewer数据构造
        $updateReviewer = new stdClass();
        $updateReviewer->reviewer = $postData['po'];
        $updateReviewer->status   = 'pending';
        $updateReviewer->comment  = NULL;
        $updateReviewer->extra    = NULL;
        $updateReviewer->reviewTime  = '';
        $updateReviewer->createdDate = helper::today();

        //产品经理节点通过，部门管理层审批节点不通过，需要删除,并更新第一节点
        if(count($nodeInfo) == 2)
        {
            $needDeleteID = $nodeInfo[1];
            $this->dao->delete()->from(TABLE_REVIEWNODE)->where('id')->eq($needDeleteID)->exec();
            $this->dao->delete()->from(TABLE_REVIEWER)->where('node')->eq($needDeleteID)->exec();
        }
        $this->dao->update(TABLE_REVIEWNODE)->data($updateNode)->where('id')->eq($nodeID)->exec();
        $this->dao->update(TABLE_REVIEWER)->data($updateReviewer)->where('node')->eq($nodeID)->exec();

        //③更新requirement主表 变更单待处理人
        $this->dao->update(TABLE_REQUIREMENT)->set('changeDealUser')->eq($nextDealUser)->set('requirementChangeStatus')->eq(2)->where('id')->eq($requirementID)->exec();

        if($requirementCreated->createdBy != 'guestcn'){ //非清总变更
            //当前登录人为第一节点产品经理，默认审核通过 非清总同步
            if($this->app->user->account == $postData['po'])
            {
                $this->poDefaultPassNode($changeID,$requirementID);
            }
        }
        $this->dao->commit();


        //变更前附件删除
        $this->dao->update(TABLE_FILE)->set('deleted')->eq(1)->where('id')->in($changeInfo->changeFile)->exec();
        //变更后附件
        $fileModel->saveUpload('rtchangeoutside', $changeID);
        $fileModel->updateObjectID($this->post->uid, $changeID, 'rtchangeoutside');

        $changeFile = '';
        $changeFileInfo = $fileModel->getFileInfo('rtchangeoutside',$changeID);
        if(!empty($changeFileInfo))
        {
            $changeFile = implode(',',array_column($changeFileInfo,'id'));
        }

        //重新修改附件
        $data->changeFile = $changeFile;
        $tempData = new  stdClass();
        $tempData->changeFile = $changeFile;
        $this->dao->update(TABLE_REQUIREMENTCHANGEOUTSIDE)->data($tempData)->where('id')->eq($changeID)->exec();

        unset($changeInfo->createdDate);
        $newChangeInfo = $this->getChangeInfoByChangeId($changeID);
        return common::createChanges($changeInfo, $newChangeInfo);

    }

    /**
     * 根据项目查找需求任务
     *
     * @param $projectId
     * @return array
     */
    public function getRequirementIdsByProject($projectId){
        $data = [];
        if(!$projectId){
            return $data;
        }
        $leftProjectId  = "',".$projectId."'";
        $rightProjectId = "'".$projectId.  ",'";
        $allProjectId   =  "',".$projectId.  ",'";
        $projectIdArray = [$projectId, $leftProjectId, $rightProjectId, $allProjectId];

        $ret = $this->dao->select('id')
            ->from(TABLE_REQUIREMENT)
            ->where(1)
            ->andWhere('status')->notIN('deleted')
            ->andWhere('project')->in($projectIdArray)
            ->fetchAll();
        if($ret){
            $data = array_column($ret, 'id');
        }
        return $data;
    }

    /**
     * 获得需求任务不包含项目的需求任务ids
     *
     * @param $requirementIds
     * @param $projectId
     * @return array
     */
    public function getRequirementIdsByNotEqProject($requirementIds, $projectId){
        $data = [];
        if(!($requirementIds && $projectId)){
            return $data;
        }
        $ret = $this->dao->select('id')
            ->from(TABLE_REQUIREMENT)
            ->where(1)
            ->andWhere('status')->notIN('deleted')
            ->andWhere('id')->in($requirementIds)
            ->andWhere('project')->ne('')
            ->andWhere('project')->ne($projectId)
            ->fetchAll();
        if($ret){
            $data = array_column($ret, 'id');
        }
        return $data;
    }


    /**
     * Get requirement list.
     *
     * @param  string $status all|deleted|confirmed|waiting|feedback(已反馈)
     * @param  string $orderBy
     * @param  object $pager
     * @access public
     * @return array
     */
    public function getList($browseType, $queryID, $orderBy, $pager = null)
    {
        /* 当$browseType为bysearch时，处理搜索的SQL查询条件。*/
        $requirementQuery = '';
        if($browseType == 'bysearch')
        {
            $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : '';
            if($query)
            {
                $this->session->set('requirementQuery', $query->sql);
                $this->session->set('requirementForm', $query->form);
            }

            if($this->session->requirementQuery == false) $this->session->set('requirementQuery', ' 1 = 1');
            $requirementQuery = $this->session->requirementQuery;
            $requirementQuery = str_replace('AND `', ' AND `t1.', $requirementQuery);
            $requirementQuery = str_replace('AND (`', ' AND (`t1.', $requirementQuery);

            $requirementQuery = str_replace('OR `', ' OR `t1.', $requirementQuery);
            $requirementQuery = str_replace('OR (`', ' OR (`t1.', $requirementQuery);

            $requirementQuery = str_replace('`', '', $requirementQuery);

            if(strpos($requirementQuery, 'sourceMode') !== false)
            {
                $requirementQuery = str_replace('t1.sourceMode', "t2.sourceMode", $requirementQuery);
            }

            if(strpos($requirementQuery, 'sourceName') !== false)
            {
                $requirementQuery = str_replace('t1.sourceName', "t2.sourceName", $requirementQuery);
            }

            if(strpos($requirementQuery, 'project ') !== false){
                $searchStr = "t1.project";
                $res = preg_replace_callback('/t1.project.*?(\d+)\'/',function ($matches) use($searchStr){
                    $projectId = intval($matches[1]);
                    $requirementIds = $this->getRequirementIdsByProject($projectId);
                    $demandRequirementIds = $this->loadModel('demand')->getRequirementIdsByProject($projectId);
                    if($demandRequirementIds){
                        $requirementIds = array_merge($requirementIds, $demandRequirementIds);
                        $tempRequirementIds = $this->getRequirementIdsByNotEqProject($demandRequirementIds, $projectId);
                    }
                    if($requirementIds) {
                        $tempDemandRequirementIds = $this->loadModel('demand')->getRequirementIdsByNotEqProject($requirementIds, $projectId);
                        if ($tempDemandRequirementIds) {
                            $requirementIds = array_diff($requirementIds, $tempDemandRequirementIds);
                        }
                        if($requirementIds && (isset($tempRequirementIds) && !empty($tempRequirementIds))){
                            $requirementIds = array_diff($requirementIds, $tempRequirementIds);
                        }
                    }
                    if($requirementIds){
                        $tempQuery = "( t1.id in (".implode(',', $requirementIds)."))";
                    }else{
                        $tempQuery = "( t1.id in (null))";
                    }

                    return $tempQuery;
                }, $requirementQuery);
                $requirementQuery = $res;

                $res = preg_replace_callback('/t1.project.*?(\d+)\%\'/',function ($matches) use($searchStr){
                    $projectId = intval($matches[1]);
                    $requirementIds = $this->loadModel('demand')->getRequirementIdsByProject($projectId);
                    if($requirementIds){
                        $tempQuery = "(find_in_set($projectId, $searchStr) OR t1.id in (".implode(',', $requirementIds)."))";
                    }else{
                        $tempQuery = "find_in_set($projectId, $searchStr)";
                    }
                    return $tempQuery;
                }, $requirementQuery);
                $requirementQuery = $res;
            }
        }
//        $assigntomeQuery = '(( 1    AND  FIND_IN_SET("'.$this->app->user->account.'",t1.dealUser) AND (t1.`status` NOT IN ("delivered","onlined","closed") OR t1.`feedbackStatus` = "tofeedback"))  OR ( 1   AND FIND_IN_SET("'.$this->app->user->account.'",t1.feedbackDealUser)) OR ( 1   AND FIND_IN_SET("'.$this->app->user->account.'",t1.changeDealUser)))';
        $assigntomeQuery = '(( 1  AND  (FIND_IN_SET("'.$this->app->user->account.'",t1.dealUser) AND t1.`status` NOT IN ("delivered","onlined","closed")) OR FIND_IN_SET("'.$this->app->user->account.'",t1.feedbackDealUser) OR (FIND_IN_SET("'.$this->app->user->account.'",t1.changeDealUser))))';
        $statusQuery = '(1 AND 
        ((((t1.`feedbackStatus` != "feedbacksuccess" OR t1.`status` != "onlined")  AND ( t1.`status` != "delivered"  OR t1.`feedbackStatus` != "feedbacksuccess") AND ( t1.`status` != "delivered"  OR t1.`feedbackStatus` != "toexternalapproved") AND ( t1.`status` != "onlined"  OR t1.`feedbackStatus` != "toexternalapproved"))) OR (t1.`createdBy` = "guestcn" and FIND_IN_SET("'.$this->app->user->account.'",t1.changeDealUser)))
        )';
        /* 查询需求数据，调用process方法获取评审人。*/
        $requirements = $this->dao->select('t1.*')->from(TABLE_REQUIREMENT)->alias('t1')
            ->innerJoin(TABLE_OPINION)->alias('t2')
            ->on('t1.opinion=t2.id')
            ->where(1)
            ->andWhere('t1.`status`')->notIN('deleted')
            ->andWhere('t1.sourceRequirement')->eq(1)
            ->beginIF($browseType == 'ignore')->andWhere('t1.ignoreStatus')->eq('1')->andWhere('t1.ignoredBy')->like("%{$this->app->user->account}%")->fi()
            ->beginIF($browseType == 'reviewing')->andWhere('t1.`status`')->eq('reviewing')->fi()
            ->beginIF($browseType == 'assigntome')->andWhere('t1.`status`')->notIN('closed,deleteout')->fi()
            ->beginIF($browseType == 'assigntome')->andWhere($assigntomeQuery)->andWhere($statusQuery)->fi()
            ->beginIF($browseType != 'all' and $browseType != 'ignore' and $browseType != 'bysearch' and $browseType != 'reviewing' and ($browseType == 'topublish' or $browseType == 'published' or $browseType == 'splited' or $browseType == 'underchange' or $browseType == 'delivered' or $browseType == 'onlined' or $browseType == 'closed' or $browseType == 'deleteout'))->andWhere('t1.status')->eq($browseType)->fi()
            ->beginIF($browseType != 'all' and ($browseType == 'tofeedback' or $browseType == 'todepartapproved' or $browseType == 'toinnovateapproved' or $browseType == 'toexternalapproved' or $browseType == 'syncfail' or $browseType == 'syncsuccess' or $browseType == 'feedbacksuccess' or $browseType == 'feedbackfail' or $browseType == 'returned'))->andWhere('t1.feedbackStatus')->eq($browseType)->fi()
            ->beginIF($browseType == 'bysearch')->andWhere($requirementQuery)->fi()
            ->beginIF($browseType == 'assigntome')->orderBy("ignoreStatus_asc, t1.".$orderBy)->fi()
            ->beginIF($browseType != 'assigntome')->orderBy($orderBy)->fi()
            ->page($pager,'t1.id')
            ->fetchAll('id');
        /* 保存查询条件并查询子需求条目。*/
        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'requirement', $browseType != 'bysearch');
        // return $this->process($requirements);
        return $requirements;
    }

    public function getPairs($orderBy = 'id_desc')
    {
        $requirements = $this->dao->select('id,name')->from(TABLE_REQUIREMENT)
            ->where('status')->ne('deleted')
            ->andWhere('sourceRequirement')->eq(1)
            ->orderBy($orderBy)
            ->fetchPairs();
        return $requirements;
    }

    /**
     * @Notes:获取内外部全部需求任务数据
     * @Date: 2023/5/26
     * @Time: 19:03
     * @Interface getAllPairs
     * @param string $orderBy
     * @return mixed
     */
    public function getAllPairs($orderBy = 'id_desc')
    {
        $requirements = $this->dao->select('id,name')->from(TABLE_REQUIREMENT)
            ->where('status')->ne('deleted')
            ->orderBy($orderBy)
            ->fetchPairs();
        return $requirements;
    }

    public function getCodePairs($orderBy = 'id_desc')
    {
        $requirements = $this->dao->select('id,code')->from(TABLE_REQUIREMENT)
            ->where('status')->ne('deleted')
            ->andWhere('sourceRequirement')->eq(1)
            ->orderBy($orderBy)
            ->fetchPairs();
        return $requirements;
    }

    /**
     * TongYanQi 2022/12/19
     * 所有状态 统计用
     */
    public function getAllStatus()
    {
        $opinions = $this->dao->select('id,name,`status`,opinion')->from(TABLE_REQUIREMENT)
            ->where('status')->ne('deleted')
            ->andWhere('sourceRequirement')->eq(1)
            ->fetchAll();
        return $opinions;
    }

    /**
     * 根据多个id获取信息
     * @param array $ids
     * @return array
     */
    public function getPairsByIds($ids, $orderBy = 'id_desc')
    {
        if(empty($ids)) return null;
        $info = $this->dao->select('id,code,name,sourceRequirement')->from(TABLE_REQUIREMENT)
            ->where('status')->ne('deleted')
            ->andwhere('id')->in($ids)
            ->orderBy($orderBy)
            ->fetchall();
        $requirements = new stdClass();
        foreach ($info as $item)
        {
            $id = $item->id;
            $requirements->$id = ['code'=>$item->code, 'name' =>$item->name, 'sourceRequirement' =>$item->sourceRequirement];
        }
        return $requirements;
    }
    /**
     * Project: chengfangjinke
     * Method: getByOpinion
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:24
     * Desc: This is the code comment. This method is called getByOpinion.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $opinionID
     * @return mixed
     */
    public function getByOpinion($opinionID)
    {
        /* 获取一条需求意向下的所有需求任务数据。*/
        return $this->dao->select('*')->from(TABLE_REQUIREMENT)
            ->where('opinion')->eq($opinionID)
            ->andWhere('status')->ne('deleted')
            ->fetchAll('id');
    }

    /**
     * Project: chengfangjinke
     * Method: process
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:24
     * Desc: This is the code comment. This method is called process.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $requirements
     * @return mixed
     */
    private function process($requirements)
    {
        /* 获取需求条目相关的评审人员数据。*/
        $this->loadModel('review');
        foreach($requirements as $requirement)
        {
            $reviewer = $this->review->getReviewer('requirement', $requirement->id, $requirement->changeVersion);
            $requirement->reviewer = $reviewer ? ',' . $reviewer . ',' : '';
        }

        return $requirements;
    }

    /**
     * Project: chengfangjinke
     * Method: getByProduct
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:24
     * Desc: This is the code comment. This method is called getByProduct.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $productID
     * @param $browseType
     * @param $queryID
     * @param $orderBy
     * @param null $pager
     * @return mixed
     */
    public function getByProduct($productID, $browseType, $queryID, $orderBy, $pager = null)
    {
        /* 判断是表单搜索时，处理查询SQL条件。*/
        $requirementQuery = '';
        if($browseType == 'bysearch')
        {
            $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : '';
            if($query)
            {
                $this->session->set('requirementQuery', $query->sql);
                $this->session->set('requirementForm', $query->form);
            }

            if($this->session->requirementQuery == false) $this->session->set('requirementQuery', ' 1 = 1');

            $requirementQuery = $this->session->requirementQuery;
        }

        /* 查询产品下的用户需求数据。*/
        return $this->dao->select('t2.*')->from(TABLE_PRODUCTREQUIREMENT)->alias('t1')->leftJoin(TABLE_REQUIREMENT)->alias('t2')
            ->on('t1.requirement = t2.id')
            ->where('t1.product')->eq($productID)
            ->beginIF($browseType != 'all' and $browseType != 'bysearch')->andWhere('status')->eq($browseType)->fi()
            ->beginIF($browseType == 'all')->andWhere('status')->ne('deleted')->fi()
            ->beginIF($browseType == 'bysearch')->andWhere($requirementQuery)->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');
    }

    /**
     * Project: chengfangjinke
     * Method: getByID
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:24
     * Desc: This is the code comment. This method is called getByID.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $requirementID
     * @param int $version
     * @return mixed
     */
    public function getByID($requirementID, $version = 0)
    {
        /* 获取需求条目信息，如果参数传了版本号，则根据版本号进行查询。*/
        $requirement = $this->dao->findByID($requirementID)->from(TABLE_REQUIREMENT)->fetch();
        if($requirement->deadLine == '0000-00-00'){
            $requirement->deadLine = '';
        }
        if($requirement->end == '0000-00-00'){
            $requirement->end = '';
        }
        if($version === 'latest') $version = $requirement->changeVersion;

        if($version)
        {
            $spec = $this->dao->select('*')->from(TABLE_REQUIREMENTSPEC)->where('requirement')->eq($requirementID)->andWhere('`version`')->eq($version)->fetch();
            $requirement->name    = isset($spec->name)  ? $spec->name : '';
            $requirement->desc    = isset($spec->desc)  ? $spec->desc : '';
            $requirement->app     = isset($spec->app)  ? $spec->app : '';
            $requirement->project = isset($spec->project)  ? $spec->project : '';
            $requirement->product = isset($spec->product)  ? $spec->product: '';
            $requirement->line    = isset($spec->line)  ? $spec->line : '';
            $requirement->end     = isset($spec->end)  ? $spec->end : '';
            $requirement->owner   = isset($spec->owner)  ? $spec->owner : '';
            $requirement->dept    = isset($spec->dept)  ? $spec->dept : '';

            $requirement->contact   = isset($spec->contact) ? $spec->contact : '';
            $requirement->analysis  = isset($spec->analysis) ? $spec->analysis : '';
            $requirement->handling  = isset($spec->handling) ? $spec->handling : '';
            $requirement->implement = isset($spec->implement) ? $spec->implement : '';
        }

        /* 查询需求条目附件和评审人信息。*/
        $requirement->files = $this->loadModel('file')->getByObject('requirement', $requirementID);

        $reviewer = $this->loadModel('review')->getReviewer('requirement', $requirement->id, $requirement->changeVersion);
        $requirement->reviewer = $reviewer ? ',' . $reviewer . ','  : '';

        $requirement = $this->loadModel('file')->replaceImgURL($requirement, 'desc');

        //需求任务流转状态
        $consumedInfo = $this->dao->select('*')->from(TABLE_CONSUMED)->where('objectType')->eq('requirement') //状态流转 工作量
            ->andWhere('objectID')->eq($requirement->id)
            ->andWhere('deleted')->eq(0)
//            ->andWhere('extra')->ne('requirementFeedBack')
            ->orderBy('id_asc')
            ->fetchAll();
        //需求任务流转状态数据构造
        $consumedArray = [];
        //反馈单流转状态数据构造
        $feedBackStatusArray = [];
        foreach ($consumedInfo as $key=>$val)
        {
            //需求任务流转状态
            if(in_array($val->before,$this->config->requirement->consumedStatusArr) || in_array($val->after,$this->config->requirement->consumedStatusArr))
            {
                $consumedArray[$key]['id']    = $val->id;
                $consumedArray[$key]['before']    = $val->before;
                $consumedArray[$key]['after']    = $val->after;
                $consumedArray[$key]['account']    = $val->account;
            }
            //反馈单流转状态
            if(in_array($val->before,$this->config->requirement->feedBackStatusArr) || in_array($val->after,$this->config->requirement->feedBackStatusArr))
            {

                $feedBackStatusArray[$key]['id']      = $val->id;
                $feedBackStatusArray[$key]['before']  = $val->before;
                $feedBackStatusArray[$key]['after']   = $val->after;
                $feedBackStatusArray[$key]['account'] = $val->account;
            }
        }
        $requirement->consumed = $consumedArray;
        $requirement->feedBackStatusInfo = $feedBackStatusArray;
        return $requirement;
    }

    public function getByIdSimple($requirementID = 0)
    {
        $res = $this->dao->findByID($requirementID)->from(TABLE_REQUIREMENT)->fetch();
        return $res;
    }

    public function getByCode($entriesCode)
    {
        $requirement = $this->dao->select('*')->from(TABLE_REQUIREMENT)->where('entriesCode')->eq($entriesCode)->fetch();
        return $requirement;
    }

    public function getByFeedbackCode($feedbackCode)
    {
        $requirement = $this->dao->select('*')->from(TABLE_REQUIREMENT)->where('feedbackCode')->eq($feedbackCode)->fetch();
        return $requirement;
    }

    /**
     * Desc:拆分需求任务
     * User: wangshusen
     * Date: 2022/5/19
     * Time: 15:58
     *
     * @param int $opinionID
     *
     */
    public function subdivide($opinionID = 0)
    {
        $this->loadModel('action');
        $requirements = fixer::input('post')
            ->stripTags($this->config->requirement->editor->create['id'], $this->config->allowedTags)
            ->get();
        //判断空处理
        if(count($_POST['nextUser']) == 1 && $_POST['nextUser'][0] == '')
        {
            dao::$errors[] = sprintf($this->lang->requirement->emptyObject, $this->lang->requirement->dealUser);
        }
        $this->tryError();
        $demandTitle = $_POST['demandTitle'];
        $demandDesc = $_POST['demandDesc'];
        $deadlines = $_POST['deadlines'];
        $end = $_POST['planEnd'];
        $nextUser = $_POST['nextUser'];
        $progress = $_POST['progress'];
        foreach ($demandTitle as $title){
            if($title == '')
            {
                $errors['demandTitle'] = sprintf($this->lang->requirement->emptyObject, $this->lang->requirement->name);
                return dao::$errors = $errors;
            }
        }
        
        for ($i=0; $i < count($demandTitle); $i++){
            $app = $i == 0 ? 'app' : 'app'.$i;
            if(!isset($_POST[$app]))
            {
                $errors[''] = sprintf($this->lang->requirement->emptyObject, $this->lang->requirement->app);
                return dao::$errors = $errors;
            }
        }

        foreach ($deadlines as $deadline) {
            if(!$this->loadModel('common')->checkJkDateTime($deadline))
            {
                dao::$errors['deadlines'] =  sprintf($this->lang->requirement->emptyObject, $this->lang->requirement->deadLineDate);
                return;
            }
        }

        foreach ($end as $item) {
            if(!$this->loadModel('common')->checkJkDateTime($item))
            {
                dao::$errors['planEnd'] =  sprintf($this->lang->requirement->emptyObject, $this->lang->requirement->planEnd);
                return;
            }
        }

        foreach ($demandDesc as $desc){
            if($desc == '')
            {
                $errors['demandDesc'] = sprintf($this->lang->requirement->emptyObject, $this->lang->requirement->desc);
                return dao::$errors = $errors;
            }
        }

        //计划完成时间不允许大于期望完成时间
        foreach ($demandTitle as $i => $value){
           /*
            if(strtotime($end[$i]) > strtotime($deadlines[$i]))
            {
                $errors[] = $this->lang->requirement->editEndTip;
                return dao::$errors = $errors;
            }
           */
        }


        if(dao::isError()) return false;
        $opinionData = $this->loadModel('opinion')->getOwnerDefineFieldById($opinionID);
        foreach ($demandTitle as $i => $title){
            /* 获取单个需求意向拆分为需求条目的表单数据，进行需求条目拆分，拆分时会记录一个默认的需求版本记录，并记录需求意向操作动作。*/
            $requirementIdList = array();
            $uid = $requirements->uid;
            $requirementDescList = $demandDesc;
            $this->loadModel('consumed');
            if(!$title) unset($requirements->demandTitle[$i]);
            // 更新需求代号。
            $date   = helper::today();
            $codeBefore = substr( $date, 0, 4) . sprintf('%03d', $opinionID);
            $number = $this->dao->select('count(id) c')
                ->from(TABLE_REQUIREMENT)
                ->where('code')
                ->like($codeBefore.'%')
                ->fetch('c');

            $code   = $codeBefore . '-' . sprintf('%02d', $number+1);
            $data = new stdClass();
            $data->name        = $title;
            $data->status      = 'published';
            $data->opinion     = $opinionID;
            $data->code        = $code;
            $data->deadLine       = $deadlines[$i] ?? '';
            $data->planEnd        = $end[$i] ?? '';
            $data->comment       = $progress[$i] ?? '';
            $data->dealUser       = join(',',$requirements->nextUser) ?? '';
            $data->createdBy   = $this->app->user->account;
            $data->createdDate = helper::now();
            $app = $i ? $_POST['app' . $i] : $_POST['app'];
            $data->app = $app ? join(',',$app) : '';
            $data->productManager = $this->app->user->account;
            $data->projectManager = trim(implode(',',$nextUser), ',');
            $data->startTime   = helper::now();

            // 需求意向主题，业务需求单位，需求提出时间
            $data->nameByOpinion = $opinionData->name;
            $data->union = $opinionData->union;
            $data->dateByOpinion = $opinionData->createdDate;
            $data->acceptTime = $opinionData->sourceMode=='8'?helper::now():$opinionData->receiveDate;
            //需求任务最新发布时间
            $data->newPublishedTime = helper::now();
            // 处理富文本字段内容。
            $_POST['desc'] = $requirementDescList[$i];
            $postData = fixer::input('post')->stripTags('desc', $this->config->allowedTags)->get();
            $data->desc = $postData->desc;
            $this->dao->insert(TABLE_REQUIREMENT)
                ->data($data)
                ->autoCheck()
                ->exec();
            $requirementID = $this->dao->lastInsertID();
            $this->loadModel('file')->updateObjectID($uid . $i, $requirementID, 'requirement');
            $this->file->saveUpload('requirement', $requirementID, '', 'files' . $i);

            if(dao::isError()) return false;

            $spec = new stdClass();
            $spec->name        = $title;
            $spec->requirement = $requirementID;
            $spec->createdBy   = $this->app->user->account;
            $spec->createdDate = helper::now();
            $this->dao->insert(TABLE_REQUIREMENTSPEC)->data($spec)->exec();

            // 记录工时,需求意向拆分人取录入，需求任务默认0。
//            $this->consumed->record('opinion', $opinionID, $requirements->consumed, $data->createdBy, '', 'subdivided', array());
            $this->consumed->record('requirement', $requirementID, 0, $this->app->user->account, '', 'published', array());
            if(dao::isError()) return false;

            $actionID = $this->action->create('requirement', $requirementID, 'subdivided');
//            $this->sendmail($requirementID,$actionID);
            $requirementIdList[] = $requirementID;
        }

        if($opinionData->status != 'underchange')
        {
            if(!empty($requirementIdList))
            {
                $this->dao->update(TABLE_OPINION)->set('status')->eq('subdivided')
                    ->where('id')->eq($opinionID)
                    ->exec();
                $this->loadModel('consumed')->record('opinion', $opinionID, 0, $this->app->user->account, $opinionData->status, 'subdivided');
            }
        }
        return $requirementIdList;
    }

    /**
     * Project: chengfangjinke
     * Method: batchCreate
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:24
     * Desc: This is the code comment. This method is called batchCreate.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $opinionID
     * @return array|false
     */
    public function batchCreate($opinionID = 0)
    {
        $this->loadModel('action');
        $requirements = fixer::input('post')->get();
        /* 获取单个需求意向拆分为需求条目的表单数据，进行需求条目拆分，拆分时会记录一个默认的需求版本记录，并记录需求意向操作动作。*/
        $requirementIdList = array();
        foreach($requirements->name as $i => $name)
        {
            if(empty($name)) continue;
            $data = new stdClass();
            $data->name        = $name;
            $data->status      = 'wait';
            $data->opinion     = $opinionID;
            $data->code        = $requirements->code[$i];
            $data->method      = $requirements->method[$i];
            $data->createdBy   = $this->app->user->account;
            $data->createdDate = helper::now();

            $this->dao->insert(TABLE_REQUIREMENT)->data($data)->autoCheck()->exec();
            if(dao::isError()) return false;
            $requirementID = $this->dao->lastInsertID();

            $spec = new stdClass();
            $spec->name        = $name;
            $spec->requirement = $requirementID;
            $spec->createdBy   = $this->app->user->account;
            $spec->createdDate = helper::now();
            $this->dao->insert(TABLE_REQUIREMENTSPEC)->data($spec)->exec();
            if(dao::isError()) return false;

            $this->action->create('requirement', $requirementID, 'subdivided');
            $requirementIdList[] = $requirementID;
        }

        return $requirementIdList;
    }

    /**
     * Project: chengfangjinke
     * Method: buildSearchForm
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:24
     * Desc: This is the code comment. This method is called buildSearchForm.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $queryID
     * @param $actionURL
     */
    public function buildSearchForm($queryID, $actionURL)
    {
        /* 为需求条目搜索表单构建参数信息。*/
        $this->config->requirement->search['actionURL'] = $actionURL;
        $this->config->requirement->search['queryID']   = $queryID;
        //$this->config->requirement->search['params']['project']['values'] = array('' => '') + $this->loadModel('projectplan')->getPairs();
        $this->config->requirement->search['params']['project']['values'] = array('' => '') + $this->loadModel('project')->getPairs();
        $this->config->requirement->search['params']['line']['values']    = array('' => '') + $this->loadModel('product')->getLinePairs();
        $this->config->requirement->search['params']['product']['values'] = array('' => '') + $this->product->getCodeNamePairs();

        $this->loadModel('search')->setSearchParams($this->config->requirement->search);
    }

    /**
     * Project: chengfangjinke
     * Method: review
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:24
     * Desc: This is the code comment. This method is called review.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $requirementID
     * @param $version
     * @param $result
     * @param $comment
     * @return array|void
     */
    public function review($requirementID, $version, $result, $comment)
    {
        /* 需求评审时，判断需求评审结果和评审意见是否为空。*/
        if(!$result)
        {
            dao::$errors['result'] = $this->lang->requirement->resultEmpty;
            return;
        }
        if(!$comment)
        {
            dao::$errors['comment'] = $this->lang->requirement->commentEmpty;
            return;
        }
        $oldRequirement = $this->getByID($requirementID);
        $result = $this->loadModel('review')->check('requirement', $requirementID, $version, $result, $comment);
        if($result === 'pass')
        {
            /* 如果需求条目评审通过，则更新需求条目信息并记录需求条目版本记录，并处理需求关联的产品。*/
            $spec = $this->dao->select('*')->from(TABLE_REQUIREMENTSPEC)
                ->where('requirement')->eq($requirementID)
                ->andWhere('version')->eq($version)
                ->fetch();
            $data = new stdClass();
            $data->name    = $spec->name;
            $data->version = $version;
            $data->line    = $spec->line;
            $data->app     = $spec->app;
            $data->project = $spec->project;
            $data->product = $spec->product;
            $data->dept    = $spec->dept;
            $data->desc    = $spec->desc;
            $data->method  = $spec->method;
            $data->owner   = $spec->owner;
            $data->end     = $spec->end;

            $data->contact   = $spec->contact;
            $data->analysis  = $spec->analysis;
            $data->handling  = $spec->handling;
            $data->implement = $spec->implement;
            $data->status    = 'approved';
            $this->dao->update(TABLE_REQUIREMENT)->data($data)->where('id')->eq($requirementID)->exec();
            $this->dao->delete()->from(TABLE_PRODUCTREQUIREMENT)->where('requirement')->eq($requirementID)->exec();
            $this->loadModel('opinion')->updatePlanDeadline($requirementID,$spec->end);// 记录最大的计划完成时间。
            if($spec->product)
            {
                foreach(explode(',', $spec->product) as $product)
                {
                    if(!$product) continue;

                    $data = new stdClass();
                    $data->requirement = $requirementID;
                    $data->product     = $product;
                    $this->dao->insert(TABLE_PRODUCTREQUIREMENT)->data($data)->exec();
                }
            }
        }
        else if($result === 'reject')
        {
            /* 如果需求评审不通过，则重新设置变更版本字段的值。*/
            $this->dao->update(TABLE_REQUIREMENT)->set('changeVersion = version')->set('status')->eq('failed')->where('id')->eq($requirementID)->exec();
        }
        $newRequirement = $this->getByID($requirementID);

        return common::createChanges($oldRequirement, $newRequirement);
    }

    /**
     * 审批反馈单
     * @param $problemID
     * @return false|void
     */
    public function reviewfeedback($requirementID,$extra = '')
    {
        $oldRequirement = $this->getByID($requirementID);
        //判断当前状态和审核中传入的状态是否一致，以防有同组中其他人修改
        $res = $this->checkAllowReview($oldRequirement, $this->post->version, $this->post->reviewStage, $this->app->user->account);
        if(!$res['result']){
            dao::$errors['statusError'] = $res['message'];
        }

        if(empty($_POST['result'])){
            dao::$errors['result'] = sprintf($this->lang->requirement->emptyObject, $this->lang->requirement->result);
        }


        $this->tryError();


        $is_all_check_pass = false;
        $result = $this->loadModel('review')->check('requirement', $requirementID, $oldRequirement->version, $this->post->result, $this->post->approveComm, $oldRequirement->reviewStage, '', $is_all_check_pass);
        if($result == 'pass')
        {
            $add = 1;
            $currreview = $this->dao->select('*')->from(TABLE_REVIEWNODE)->where('objectType')->eq('requirement')
                ->andWhere('objectID')->eq($requirementID)
                ->andWhere('version')->eq($oldRequirement->version)
                ->andWhere('status')->eq('wait')->orderBy('stage,id')->fetch();

            //下一审核节点
            if($currreview){
                $nextReviewStage = $currreview->stage;
            }else{
                $nextReviewStage = $oldRequirement->reviewStage + $add;
            }

            //下一审核状态
            if(isset($this->lang->requirement->reviewNodeList[$nextReviewStage])){
                $status = $this->lang->requirement->reviewNodeList[$nextReviewStage];
            }
            //部门审批通过，增加审批通过时间  未勾选不再更新
            if($oldRequirement->reviewStage == 1 && ($oldRequirement->deptPassTime == '0000-00-00 00:00:00' || empty($oldRequirement->deptPassTime)) && $oldRequirement->isUpdateOverStatus == 1)
            {
                $this->dao->update(TABLE_REQUIREMENT)->set('deptPassTime')->eq(helper::now())->where('id')->eq($requirementID)->exec();
            }

            $this->dao->update(TABLE_REQUIREMENT)->set('reviewStage')->eq($nextReviewStage)->set('feedbackStatus')->eq($status)->where('id')->eq($requirementID)->exec();
            $this->loadModel('consumed')->record('requirement', $requirementID, 0, $this->app->user->account, $oldRequirement->feedbackStatus, $status, array(),"requirementFeedBack");

            $next = $this->dao->select('id')->from(TABLE_REVIEWNODE)->where('objectType')->eq('requirement')
                ->andWhere('objectID')->eq($requirementID)
                ->andWhere('version')->eq($oldRequirement->version)
                ->andWhere('status')->eq('wait')->orderBy('stage,id')->fetch('id');
            if($next)
            {
                $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('pending')->where('id')->eq($next)->exec();
                $this->dao->update(TABLE_REVIEWER)->set('status')->eq('pending')->where('node')->eq($next)->exec();

                $feedbackToHandle = $this->dao->select('reviewer')->from(TABLE_REVIEWER)->where('node')->eq($next)->fetch();
                if(empty($feedbackToHandle)){
                    $this->dao->update(TABLE_REQUIREMENT)->set('feedbackDealUser')->eq('')->where('id')->eq($requirementID)->exec();
                }else{
                    $this->dao->update(TABLE_REQUIREMENT)->set('feedbackDealUser')->eq($feedbackToHandle->reviewer)->where('id')->eq($requirementID)->exec();
                }
                $action = 'toinnovateapproved' == $oldRequirement->feedbackStatus ? 'deal' : 'reviewed';
                $actionID = $this->loadModel('action')->create('requirement', $requirementID, $action, $this->post->comment,$extra);
                //如果下一个状态为带外部审批就推送反馈单
                if($status == 'toexternalapproved')
                {
                    $this->pushfeedback($requirementID);
                    if(!dao::isError()) {
                        $requirement = $this->getByID($requirementID);
                        if ($requirement->feedbackStatus == 'feedbacksuccess') {
                            /* 删除需求条目所属的产品记录，重新计算需求条目属于那些产品。*/
                            $this->dao->delete()->from(TABLE_PRODUCTREQUIREMENT)->where('requirement')->eq($requirementID)->exec();
                            if (isset($requirement->product) and $requirement->product) {
                                foreach (explode(',', $requirement->product) as $product) {
                                    if (!$product) continue;
                                    $data = new stdClass();
                                    $data->requirement = $requirementID;
                                    $data->product = $product;
                                    $this->dao->insert(TABLE_PRODUCTREQUIREMENT)->data($data)->exec();
                                }
                            }
                        }
                    }
                }
            }
        }else if($result == 'reject')
        {
            $this->dao->update(TABLE_REQUIREMENT)->set('feedbackStatus')->eq('returned')->set('feedbackDealUser')->eq($oldRequirement->feedbackBy)->where('id')->eq($requirementID)->exec();
            $this->loadModel('consumed')->record('requirement', $requirementID, 0, $this->app->user->account, $oldRequirement->feedbackStatus, 'returned', array(),"requirementFeedBack");
            $action = 'toinnovateapproved' == $oldRequirement->feedbackStatus ? 'deal' : 'reviewed';
            $actionID = $this->loadModel('action')->create('requirement', $requirementID, $action, $this->post->comment);
        }
    }

    public function checkAllowReview($requirement, $version = 1,  $reviewStage = 0, $userAccount = '')
    {
        $res = array(
            'result'  => false,
            'message' => '',
        );
        if(!$requirement){
            $res['message'] = $this->lang->common->errorParamId;
            return $res;
        }
        if(!$userAccount){
            $res['message'] = $this->lang->common->errorParamUser;
            return $res;
        }
        //审核节点已经经过
        if(($version != $requirement->version) || ($reviewStage != $requirement->reviewStage) ){
            $reviewerInfo = $this->loadModel('review')->getReviewedUserInfo('requirement', $requirement->id, $version, $reviewStage-1);
            $message = $this->lang->review->statusError;
            if($reviewerInfo){
                $message = str_replace('%', $reviewerInfo->realname, $this->lang->review->statusError);
            }
            $res['message'] = $message;
            return $res;
        }
        //获得当前的的审核人信息
        $reviews =  $this->loadModel('review')->getReviewer('requirement', $requirement->id, $requirement->version, $requirement->reviewStage);
        if(!$reviews){
            $res['message'] = $this->lang->review->reviewEnd;
            return $res;
        }
        $reviews = explode(',', $reviews);
        if(!in_array($userAccount, $reviews)){
            $res['message'] = $this->lang->review->statusUserError;
            return $res;
        }
        $res['result'] = true;
        return  $res;
    }

    /**
     * 推送反馈单
     * @return void
     */
    public function pushfeedback($requirementID){
        $requirement = $this->getByID($requirementID);
        $pushEnable = $this->config->global->pushEnable;
        if($requirement->feedbackStatus == 'toexternalapproved' and $pushEnable == 'enable')
        {
            $url           = $this->config->global->pushUrl;
            $pushAppId     = $this->config->global->pushAppId;
            $pushAppSecret = $this->config->global->pushAppSecret;
            $pushUsername  = $this->config->global->pushUsername;
            //$requirement = $this->loadModel('file')->processImgURL($requirement, $this->config->requirement->editor->change['id'], $this->post->uid);

            $headers = array();
            $headers[] = 'App-Id: ' . $pushAppId;
            $headers[] = 'App-Secret: ' . $pushAppSecret;

            $deptList = $this->loadModel('dept')->getOptionMenu();
            $users    = $this->loadmodel('user')->getPairs('noletter');
            $projects     = $this->loadModel('project')->getPairs();
            $nowTime = date('Y-m-d');
            $pushData = array();
            $pushData['Project_team']            = zget($deptList, $requirement->dept, ''); // 项目组
            $pushData['Planned_completion_time'] = strtotime($requirement->end) . '000'; // 计划完成时间
            $pushData['Jinke_Responsible']       = zget($users, $requirement->owner, ''); // 责任人
            $pushData['Attribution_item']        = zget($projects, $requirement->project, ''); // 归属项目
            $pushData['Feedback_number']         = empty($requirement->feedbackCode) ? '' : $requirement->feedbackCode; // 需求反馈单编号
            $pushData['Contact_telephone']       = $requirement->contact; // 联系人电话
            $pushData['Jinke_Feedback_person']   = zget($users, $requirement->feedbackBy, ''); // 金科反馈人
            $pushData['Feedback_date']           = strtotime($nowTime) . '000'; // 反馈日期
            $pushData['Requirement_item_number'] = $requirement->entriesCode; // 需求条目编号

            $method = zget($this->lang->requirement->methodList, $requirement->method);
            $method = str_replace('实现', '', $method);
            $pushData['Implementation_mode']       = $method; // 实现方式
            $pushData['Requirement_item_analysis'] = str_replace("\n","<br/>",$requirement->analysis); // 需求条目分析
            $pushData['Handling_suggestions']      = str_replace("\n","<br/>",$requirement->handling); // 处理建议
            $pushData['Implementation']            = str_replace("\n","<br/>",$requirement->implement); // 实施情况

            $pushData['Planned_completion_time'] = (int)$pushData['Planned_completion_time'];
            $pushData['Feedback_date'] = (int)$pushData['Feedback_date'];
            $pushData['Project_team']= trim($pushData['Project_team'], '/');

            $object     = 'requirement';
            $objectType = 'feedback';
            $request    = 'POST';
            $params     = $pushData;
            $response   = '';
            $status     = 'fail';
            $extra      = '';

            $result = $this->loadModel('requestlog')->http($url, $pushData, 'POST', 'json', array(), $headers);
            $node = $this->dao->select('*')->from(TABLE_REVIEWNODE)
                ->where('objectType')->eq('requirement')
                ->andWhere('objectID')->eq($requirement->id)
                ->andWhere('version')->eq($requirement->version)
                ->andWhere('stage')->eq('3')
                ->orderBy('stage,id')
                ->fetch();
            $updateStatus = '';
            $updateComment = '';

//            $status = 'success';
//            $this->dao->update(TABLE_REQUIREMENT)->set('feedbackDealUser')->eq('')->set('feedbackStatus')->eq('toexternalapproved')->set('feedbackCode')->eq('requirements-648')->set('feedbackDate')->eq(helper::now())->where('id')->eq($requirementID)->exec();
//            $this->loadModel('action')->create('requirement', $requirementID, 'syncsuccess', '临时修改测试接口功能');
//            $this->loadModel('consumed')->record('requirement', $requirementID, 0, 'guestjk', $requirement->feedbackStatus, 'syncsuccess', array(),"requirementFeedBack");
//            $updateStatus = 'syncsuccess';

            if(!empty($result))
            {
                $resultData = json_decode($result);
                if(isset($resultData->code) and $resultData->code == '200')
                {
                    $status = 'success';
                    $this->dao->update(TABLE_REQUIREMENT)->set('feedbackDealUser')->eq('')->set('feedbackStatus')->eq('toexternalapproved')->set('feedbackCode')->eq($resultData->data->Feedback_number)->set('feedbackDate')->eq(helper::now())->where('id')->eq($requirementID)->exec();
                    $this->loadModel('action')->create('requirement', $requirementID, 'syncsuccess', $resultData->message);
                    $this->loadModel('consumed')->record('requirement', $requirementID, 0, 'guestjk', $requirement->feedbackStatus, 'syncsuccess', array(),"requirementFeedBack");
                    $updateStatus = 'syncsuccess';
                } else {
                    $this->dao->update(TABLE_REQUIREMENT)->set('feedbackDealUser')->eq($requirement->feedbackBy)->set('feedbackStatus')->eq('syncfail')->set('reviewComments')->eq($resultData->message)->set('feedbackDate')->eq(helper::now())->where('id')->eq($requirementID)->exec();
                    $this->loadModel('action')->create('requirement', $requirementID, 'qingzongsynfailed', $resultData->message);
                    $this->loadModel('consumed')->record('requirement', $requirementID, 0, 'guestjk', $requirement->feedbackStatus, 'syncfail', array(),"requirementFeedBack");
                    $updateStatus = 'syncfail';
                    $updateComment = $resultData->message;
                }
                $response = $result;
            } else {
                $this->dao->update(TABLE_REQUIREMENT)->set('feedbackDealUser')->eq($requirement->feedbackBy)->set('feedbackStatus')->eq('syncfail')->set('reviewComments')->eq("网络不通")->set('feedbackDate')->eq(helper::now())->where('id')->eq($requirementID)->exec();
                $this->loadModel('action')->create('requirement', $requirementID, 'qingzongsynfailed', "网络不通");
                $this->loadModel('consumed')->record('requirement', $requirementID, 0, 'guestjk', $requirement->feedbackStatus, 'syncfail', array(),"requirementFeedBack");
                $updateStatus = 'syncfail';
                $updateComment = '网络不通';
            }

            //迭代二十九 外部通过时间取推送清总成功的时间 且标识需更新的数据  不勾选不再更新
            if($updateStatus == 'syncsuccess' && $requirement->isUpdateOverStatus == 1)
            {
                $this->dao->update(TABLE_REQUIREMENT)->set('innovationPassTime')->eq(helper::now())->where('id')->eq($requirementID)->exec();
            }

            $this->dao->update(TABLE_REVIEWER)
                ->set('status')->eq($updateStatus)
                ->set('comment')->eq($updateComment)
                ->set('reviewTime')->eq(helper::now())
                ->where('node')->eq($node->id)
                ->andWhere('reviewer')->eq('guestjk') //当前审核人
                ->exec();

            $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq($updateStatus)
                ->where('id')->eq($node->id)
                ->exec();




            $this->loadModel('requestlog')->saveRequestLog($url, $object, $objectType, 'POST', $pushData, $response, $status, $extra);
        }
    }

    /**
     * Project: chengfangjinke
     * Method: isClickable
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:24
     * Desc: This is the code comment. This method is called isClickable.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $requirement
     * @param $action
     * @return bool
     */
    public static function isClickable($requirement, $action)
    {
        global $app;
        $requirementModel = new requirementModel();
        //单子删除后，所有按钮不可见
        if($requirement->status == 'deleted'){
            return false;
        }
        /* 迭代三十三重新梳理 将操作动作换行小写后，计算该操作是否返回true，返回true则说明当前操作可以进行。*/
        $action = strtolower($action);
        //迭代三十三重新梳理 内部自建 已发布、变更中、已拆分 创建人可编辑  清总同步 待发布：待处理人 已发布、已拆分 产品经理
        if($action == 'edit')     return  $app->user->account == 'admin' or ((in_array($requirement->status,array('published','splited','underchange')) and strstr($requirement->createdBy, $app->user->account) !== false and $requirement->createdBy != 'guestcn') or ((in_array($requirement->status,array('published','splited')) and $requirement->productManager == $app->user->account) or (in_array($requirement->status,array('topublish')) and (strstr($requirement->dealUser, $app->user->account) !== false )) and $requirement->createdBy == 'guestcn'));
        //指派 待发布、已发布、已拆分 待处理人
        if($action == 'assignto') return  $app->user->account == 'admin' or (in_array($requirement->status,array('topublish','published','splited','underchange')) and strstr($requirement->dealUser, $app->user->account) !== false);
        //拆分 已发布、已拆分 待处理人
        if($action == 'subdivide')return  $app->user->account == 'admin' or (in_array($requirement->status,array('published','splited'))  and (strstr($requirement->dealUser, $app->user->account) !== false ));
        //变更 迭代二十七新增 需求任务状态为：已发布、已拆分 任务研发责任人
        if($action == 'change') {
            if($requirement->createdBy == 'guestcn'){//清总任务单
                $checkRes = $requirementModel->checkIsAllowEditFeedbackEnd($requirement, $app->user->account);
                return $checkRes['result'];

            }else{ //非清总任务单
                return $app->user->account == 'admin' or (in_array($requirement->status,array('splited','published')) and !$requirement->entriesCode);
            }
        }
        //反馈 已发布、已拆分、已交付、上线成功 待处理人 迭代三十二 任务列表权限：反馈单待处理人、反馈人才能反馈。
        if($action == 'feedback') return ((strstr($requirement->feedbackBy, $app->user->account) !== false or strstr($requirement->feedbackDealUser, $app->user->account) !== false) and in_array($requirement->status,array('published','splited','delivered','onlined')) and ($requirement->feedbackStatus == 'tofeedback' or $requirement->feedbackStatus == 'returned' or $requirement->feedbackStatus == 'syncfail' or $requirement->feedbackStatus == 'feedbackfail')) or $app->user->account == 'admin';
        //审批/审核反馈单 已发布、已拆分、已交付、上线成功 反馈单待处理人
        if($action == 'review')   return (($requirement->feedbackStatus == 'todepartapproved' || $requirement->feedbackStatus == 'toinnovateapproved') and strstr($requirement->feedbackDealUser, $app->user->account) !== false) or $app->user->account == 'admin';
        //挂起 已拆分
        if($action == 'close')    return  $app->user->account == 'admin' or in_array($requirement->status,array('splited'));
        //激活
        if($action == 'activate') return  $app->user->account == 'admin' or (in_array($requirement->status,array('splited','closed')) and $requirement->closedBy == $app->user->account);
        //忽略 待发布、已发布、已拆分 待处理人
        if($action == 'ignore')   return $app->user->account == 'admin' or (in_array($requirement->status,array('topublish','published','splited','underchange')) and strstr($requirement->dealUser, $app->user->account) !== false);
        //激活 待发布、已发布、已拆分 待处理人 忽略人
        if($action == 'recover')   return $app->user->account == 'admin' or (in_array($requirement->status,array('topublish','published','splited','underchange')) and strstr($requirement->dealUser, $app->user->account) !== false) or (in_array($requirement->status,array('topublish','published','splited','underchange')) and $app->user->account == $requirement->ignoredBy);
        //删除 已发布 创建人
        if($action == 'delete')   return $app->user->account == 'admin' or ($requirement->status == 'published' and $app->user->account == $requirement->createdBy);
        //推送 产创二线专员  已发布、已拆分、已交付、上线成功 产创部二线专员
        if($action == 'push')     return $app->user->account == 'admin' or (in_array($requirement->status,array('published','splited','delivered','onlined','closed')) and in_array($requirement->feedbackStatus,array('syncfail')));
        //清总增加反馈维护按钮
        if($action == 'defend')   return $app->user->account == 'admin' and $requirement->createdBy == 'guestcn' or ($requirement->createdBy == 'guestcn');
        //编辑计划完成时间
        if($action == 'editend')  return $app->user->account == 'admin' or strstr($requirement->dealUser, $app->user->account) !== false;

        return true;
    }

    /**
     * TongYanQi 2023/1/13
     * 根据用户个人情况获取需求任务列表 用于倒挂
     */
    public function getRequirementByUser(){
        //已发布、已拆分、已交付、上线成功
        $requirements = $this->dao->select('id,name,status,closedBy, dealUser,createdBy,closedDate,owner,projectManager,changeLock')->from(TABLE_REQUIREMENT)
            ->where('status')->in('published,splited,underchange,delivered,onlined')
            ->andWhere('sourceRequirement')->eq(1)
            ->orderBy('id_desc')
            ->fetchAll();
        $demands = $this->dao->select('id,requirementID,acceptUser')->from(TABLE_DEMAND)->where('status')->notIN('closed,deleted')
            ->andWhere('sourceDemand')->eq(1)
            ->andWhere('requirementID')->ne(0)
            ->fetchAll();
        $combine = [];
        foreach ($demands as $demand)
        {
            $combine[$demand->requirementID][] = $demand->id;
        }
        $list = [];

        foreach ($requirements as $requirement){
            //责任人需要根据需求条目并集获取
            $requirement->owner = '';
            if(isset($combine[$requirement->id])){
                $acceptUserFromDemands = $this->loadModel('demand')->getByIdListNew($combine[$requirement->id]);
                $acceptUserArr = array_filter(array_unique(array_column($acceptUserFromDemands,'acceptUser')));
                $requirement->owner = implode(',',$acceptUserArr);
            }

            $dealUserArr = empty($requirement->dealUser)? [] : explode(',', $requirement->dealUser);
            $projectManagerArr = empty($requirement->projectManager)? [] : explode(',', $requirement->projectManager);
            //待处理人和需求负责人并项目经理集取值
            if(!in_array($this->app->user->account, $dealUserArr) && strstr($requirement->owner,  $this->app->user->account) == false && !in_array($this->app->user->account, $projectManagerArr))
            {
                continue;
            }
            $list[$requirement->id] = $requirement->name;
        }
        return $list;
    }

    /**
     * sendmail
     *
     * @param  int    $requirementID
     * @param  int    $actionID
     * @access public
     * @return void
     */
    public function sendmail($requirementID, $actionID)
    {
        /* 加载mail模块用于发信通知，获取需求意向和人员信息。*/
        $users = $this->loadModel('user')->getPairs('noletter');
        $this->loadModel('mail');
        $requirement = $this->getById($requirementID);
        $this->app->loadLang('opinion');
        $labelList = $this->lang->requirement->labelList;
        $opinion = $this->loadModel('opinion')->getById($requirement->opinion);
        $requirement->opinionName = $opinion->name;
        //待处理人
        $dealUserArray = explode("," , $requirement->dealUser);
        $feedbackDealUserArray = explode("," , $requirement->feedbackDealUser);
        $dealUserArray = array_merge($dealUserArray, $feedbackDealUserArray);
        $dealUserArray = array_unique($dealUserArray);
        $dealUserChnList = array();
        foreach ($dealUserArray as $dealUser) {
            if(!empty($dealUser)){
                array_push($dealUserChnList, zget($users, $dealUser, ''));
            }
        }
        $requirement->pending = rtrim(implode(",", $dealUserChnList), ",");
        //状态
        $requirement->statusChn = zget($this->lang->requirement->statusList,$requirement->status,'');
        //业务需求单位
        $unionList = explode("," , $opinion->union);
        $unionChnList = array();
        foreach ($unionList as $union){
            if(!empty($union)){
                $unionChn = zget($this->lang->opinion->unionList, $union, '');
                array_push($unionChnList, $unionChn);
            }
        }
        $requirement->union = rtrim(implode(",", $unionChnList), ",");
        $requirement->date = $opinion->date;
        if($requirement->end = '0000-00-00'){
            $requirement->end = '';
        }
        //反馈单状态
        $requirement->feedbackStatusChn = zget($this->lang->requirement->statusList,$requirement->feedbackStatus,'');
        $demands = $this->getDemandByRequirement($requirementID);
        $lastEndTime = '';
        foreach ($demands as $demand){

            if(empty($lastEndTime)){
                $lastEndTime = $demand->end;
            }else{
                if(strtotime($lastEndTime) < strtotime($demand->end)){
                    $lastEndTime = $demand->end;
                }
            }
        }
        if(empty($requirement->entriesCode)){
            $requirement->end = $lastEndTime;
        }

        /* 获取后台通知中配置的邮件发信。*/
        $this->app->loadLang('custommail');
        $mailConf   = isset($this->config->global->setEntriesMail) ? $this->config->global->setEntriesMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf   = json_decode($mailConf);
        $browseType = 'entries';

        /* 处理邮件发信的标题和日期。*/
        $bestDate  = empty($requirement->changedDate) ? '' : $requirement->changedDate;
        $mailTitle = vsprintf($mailConf->mailTitle, $mailConf->variables);

        /* Get action info. */
        /* 当前需求条目的操作记录。*/
        $action          = $this->loadModel('action')->getById($actionID);
        $history         = $this->action->getHistory($actionID);
        $action->history = isset($history[$actionID]) ? $history[$actionID] : array();
        /* Get mail content. */
        /* 获取当前模块路径，然后获取发信模板，为发信模板赋值。*/
        $modulePath = $this->app->getModulePath($appName = '', 'requirement');
        $oldcwd     = getcwd();
        $viewFile = $modulePath . 'view/sendmail.html.php';
        chdir($modulePath . 'view');
        if(file_exists($modulePath . 'ext/view/sendmail.html.php'))
        {
            $viewFile = $modulePath . 'ext/view/sendmail.html.php';
            chdir($modulePath . 'ext/view');
        }

        if($action->action == 'created' || $action->action == 'edited' || $action->action == 'subdivide' || $action->action == 'assigned' || $action->action == 'subdivided'|| $action->action == 'splited')
        {
            /* 获取已确认下一节点审核人*/
            $reviewer = $requirement->dealUser;
            //迭代三十二 增加反馈单待处理人
            if($requirement->createdBy == 'guestcn' && $action->action == 'edited'){
                $dealUser = array_filter(explode(',',$reviewer));
                $feedbackDealUser = explode(',',$requirement->feedbackDealUser);
                $reviewer = array_unique(array_merge($dealUser,$feedbackDealUser));
                $reviewer = implode(',',$reviewer);
            }

            $toList   = trim($reviewer, ',');
        }else if($action->action == 'syncstatus'){
            $reviewer = $requirement->feedbackBy;
            $toList   = trim($reviewer, ',');
        }
        else{
            $reviewer = $requirement->feedbackDealUser;
            $toList   = trim($reviewer, ',');
        }
        $ccList = '';

        //创建人，研发责任人，待处理人
        $createdUser = explode(',',$requirement->createdBy);
        $dealUser = explode(',',$requirement->dealUser);
        $productManager = explode(',',$requirement->productManager);
        $demandsOther = $this->getDemandByRequirement($requirement->id);
        $acceptUser = [];

        if($action->action == 'deleted'){
            if(!empty($demandsOther)){
                $acceptUser = array_column($demandsOther,'acceptUser');
            }
            $totalToList = array_filter(array_merge($createdUser,$dealUser,$acceptUser,$productManager));
            if(!empty($totalToList)){
                $toList = implode(',',array_unique($totalToList));
            }
            $subject = $this->lang->requirement->deleteMaile;
        }else{
            $subject = vsprintf($mailConf->mailTitle, $mailConf->variables);
        }

        //清总反馈状态发送通知邮件
        if($action->action == 'syncstatus')
        {
            $subject = $this->lang->requirement->qzFeedbackMail;
            $mailConf->mailContent = '<p><strong>请进入【地盘】</strong><span><strong>-</strong></span><strong>【待处理】</strong><span><strong>-</strong></span><strong>【审批】</strong><span><strong>-</strong></span><strong>【需求任务】或【需求池】</strong><span><strong>-</strong></span><strong>【需求任务】查看<span style="color:#E53333;">需求任务</span></strong><strong>，具体信息如下：</strong><span></span></p>';
        }

        //清总同步删除 通知人 需求意向负责人、需求任务研发责任人、需求任务产品经理、二线专员
        if($action->action == 'deleteout')
        {

            $opinionAssignTo = explode(',',$opinion->assignedTo);

            if(!empty($demandsOther)){
                $acceptUser = array_column($demandsOther,'acceptUser');
            }
            $executiveList = $this->loadModel('dept')->getExecutiveUser();//二线专员
            $totalToList = array_filter(array_unique(array_merge($opinionAssignTo,$acceptUser,$productManager,$executiveList)));
            if(!empty($totalToList)){
                $toList = implode(',',array_unique($totalToList));
            }
            $subject = $this->lang->requirement->deleteMaile;
        }

        $changeInfo = $this->getChangeInfoByRequirementIdInStatus($requirementID);
        //需求意向变更邮件
        if($action->action == 'changed' || $action->action == 'editchanged' || $action->action == 'reviewchange')
        {
            $toList = $requirement->changeDealUser;

            if(!empty($changeInfo))
            {
                if($changeInfo->status == 'pending'){
                    $toList = $changeInfo->nextDealUser;
                }
                if($changeInfo->status == 'back')
                {
                    $toList = $changeInfo->createdBy;
                }
            }
            $mailConf = isset($this->config->global->setRequirementOwnChangeMail) ? $this->config->global->setRequirementOwnChangeMail : '{"mailTitle":"","variables":[],"mailContent":""}';
            $mailConf = json_decode($mailConf);
            $subject = vsprintf($mailConf->mailTitle, $mailConf->variables);
            $mailTitle = vsprintf($mailConf->mailTitle, $mailConf->variables);
            $viewFile = $modulePath . 'view/changesendmail.html.php';
        }

        ob_start();
        include $viewFile;
        foreach(glob($modulePath . 'ext/view/sendmail.*.html.hook.php') as $hookFile) include $hookFile;
        $mailContent = ob_get_contents();
        ob_end_clean();
        chdir($oldcwd);

        if(empty($toList)) return false;

        /* Send mail. */
        /* 调用mail模块的send方法进行发信。*/
        $this->mail->send($toList, $subject, $mailContent, $ccList);
        if($this->mail->isError()){
            trigger_error(join("\n", $this->mail->getError()));
        }

        //如果需求意向变更不通过需要向审批人之外的部门管理层发送通知邮件
        if('reviewchange' == $action->action && 'back' == $changeInfo->status){
            $node = $this->dao
                ->select('objectID')
                ->from(TABLE_REVIEWNODE)
                ->where('objectType')->eq('requirementchange')
                ->andWhere('objectID')->eq($changeInfo->id)
                ->andwhere('status')->eq('reject')
                ->andwhere('nodeCode')->eq('deptLeader')
                ->fetch();
            if(!empty($node)){
                $deptLeader = explode(',', $changeInfo->deptLeader);
                $toList = array_diff($deptLeader, [$this->app->user->account]);
                if(!empty($toList)){
                    $mailConf = $this->config->global->setRequirementNoticeMail ?? '{"mailTitle":"","variables":[],"mailContent":""}';
                    $mailConf = json_decode($mailConf);
                    $subject  = $mailTitle = vsprintf($mailConf->mailTitle, $mailConf->variables);
                    $viewFile = $modulePath . 'view/changesendmail.html.php';

                    ob_start();
                    include $viewFile;
                    foreach(glob($modulePath . 'ext/view/sendmail.*.html.hook.php') as $hookFile) include $hookFile;
                    $mailContent = ob_get_contents();
                    ob_end_clean();
                    chdir($oldcwd);
                    $this->mail->send(implode(',', $toList), $subject, $mailContent, '');
                    if ($this->mail->isError()) trigger_error(join("\n", $this->mail->getError()));
                }
            }
        }
    }

    /**
     * Get mail subject.
     *
     * @param  object $requirement
     * @param  string $actionType created|edited
     * @access public
     * @return string
     */
    public function getSubject($requirement, $actionType)
    {
        /* 处理发信的处理信息。*/
        return sprintf($this->lang->requirement->mail->$actionType, $this->app->user->realname, $requirement->id, $requirement->name);
    }

    /**
     * Get toList and ccList.
     *
     * @param  object     $requirement
     * @access public
     * @return bool|array
     */
    public function getToAndCcList($requirement)
    {
        /* Set toList and ccList. */
        /* 获取发信通知的发信人和抄送人。*/
        $toList = '';
        $ccList = str_replace(' ', '', trim($requirement->mailto, ','));

        if(empty($toList))
        {
            /* 删除和评审操作，获取通知人中第一个人为收件人，其他人为抄送人。*/
            if(empty($ccList)) return false;
            if(strpos($ccList, ',') === false)
            {
                $toList = $ccList;
                $ccList = '';
            }
            else
            {
                $commaPos = strpos($ccList, ',');
                $toList   = substr($ccList, 0, $commaPos);
                $ccList   = substr($ccList, $commaPos + 1);
            }
        }
        return array($toList, $ccList);
    }


    /**
     * @Notes: 编辑时需求日条目同步联动需求任务和意向的状态 $oldRequirementID用于编辑
     * @Desc ①编辑时未倒挂其他需求任务，此时只需修改需求回退需求条目的状态即可。 编辑权限限制，无需更改任务、意向状态
     *       ②倒挂其他需求任务：1)原需求任务所有需求条目的状态判断 2)新的需求任务下所有需求条目状态判断（同时处理需求意向）
     * @Date: 2023/4/10
     * @Time: 16:05
     * @Interface getStatusByID
     * @param int $requirementID
     * @param int $oldRequirementID
     */
    public function getStatusByID($requirementID = 0,$oldRequirementID = 0)
    {
        /**
         * @var demandModel $demandModel
         * @var opinionModel $opinionModel
         */
        $demandModel = $this->loadModel('demand');
        $opinionModel = $this->loadModel('opinion');
        //倒挂到新的任务
        $requirementObj = $this->getByIdSimple($requirementID);
        $demandInfo = $demandModel->getByRequirementID('*',$requirementID);
        $paramsArray = $this->changeRequirementStatus($demandInfo,$requirementObj);//获取最终状态
        $requirementStatus = !empty($paramsArray) ? $paramsArray['requirementStatus'] : '';

        $this->dao->begin(); //调试完逻辑最后开启事务
        /*更新新需求任务*/
        if($requirementObj->status != $requirementStatus && $requirementObj->status != 'underchange')
        {
            $this->updateRequirement($requirementID,$requirementStatus,$requirementObj);//更新任务requirement库
            $this->loadModel('consumed')->record('requirement', $requirementID, 0, $this->app->user->account, $requirementObj->status, $requirementStatus);
        }
        $this->loadModel('action')->create('requirement', $requirementID, 'createdemand');
        /*修改需求意向*/
        $opinionId = $requirementObj->opinion;
        $opinionObj = $opinionModel->getByID($opinionId);
        $paramsArray = $opinionModel->changeOpinionStatus($this->getRequirementInfoByOpinionID($opinionId),$opinionObj);
        $opinionStatus = !empty($paramsArray) ? $paramsArray['opinionStatus'] : '';

        //更新新需求意向
        $langStatusOpinion = $this->lang->opinion->statusList;
        if($opinionObj->status != $opinionStatus  && $requirementObj->status != 'underchange')
        {
            $opinionModel->updateOpinion($opinionId,$opinionStatus);//更新意向requirement库
            $opinionArray = $this->insertActionArray($opinionId,$paramsArray['code'],$opinionObj->status);
            $this->loadModel('action')->createActions('opinion', $opinionArray, $opinionStatus.'actual',$langStatusOpinion,2);
            $this->loadModel('consumed')->record('opinion', $opinionId, 0, $this->app->user->account, $opinionObj->status, $opinionStatus);
        }
        $this->tryError(1); //检查报错 1= 需要rollback
        $this->dao->commit(); //调试完逻辑最后提交事务
    }

    /**
     * @Notes: 构造历史记录数组
     * @Date: 2023/4/12
     * @Time: 16:36
     * @Interface actionArray
     * @param $id
     * @param $code
     * @param $oldStatus
     * @return array
     */
    public function insertActionArray($id,$code,$oldStatus): array
    {
        $actionArray = [];
        $actionArray['id'] = $id;
        $actionArray['code'][] = $code;
        $actionArray['oldStatus'] = $oldStatus;
        return array($actionArray);
    }

    /**
     * @Notes: 更新需求任务
     * @Date: 2023/4/11
     * @Time: 14:57
     * @Interface updateRequirement
     * @param $requirementID
     * @param $requirementStatus
     * @param $onlineTimeByDemand
     */
    public function updateRequirement($requirementID,$requirementStatus,$onlineTimeByDemand = '')
    {
        if($requirementStatus != 'onlined'){
            $this->dao->update(TABLE_REQUIREMENT)->set('status')->eq($requirementStatus)->set('onlineTimeByDemand')->eq('')->where('id')->eq($requirementID)->exec();
        }else{
            $this->dao->update(TABLE_REQUIREMENT)->set('status')->eq($requirementStatus)->set('onlineTimeByDemand')->eq($onlineTimeByDemand)->where('id')->eq($requirementID)->exec();
        }
    }

    /**
     * @Notes:需求任务关联需求条目
     * @Date: 2023/4/10
     * @Time: 18:07
     * @Interface changeRequirementStatus
     * @param array $demandInfo
     * @param array $requirementObj
     * @return array
     */
    public function changeRequirementStatus($demandInfo = [],$requirementObj=[]): array
    {
        $paramsArray = [];
        $statusList = array_unique(array_column($demandInfo,'status'));
        $source     = array_unique(array_column($demandInfo,'sourceDemand'));
        if ($source[0] == 1){
            //外部需求条目要联动的状态 去掉,'build','released'
            $spliteStatusArray = ['wait','feedbacked','chanereturn'];
        }else{
            //内部需求条目
            $spliteStatusArray = ['wait','feedbacked','build','released'];
        }
        $code = '';
        //任务本身处于变更中、外部删除不被联动
        if(!in_array($requirementObj->status,['underchange','deleteout']))
        {
            if(count($statusList) == 0)//如果没有需求条目且任务状态不为待发布或者已挂起则需求任务为<已发布>
            {
                if(!in_array($requirementObj->status,['topublish','closed']))
                {
                    $code = '未查到需求条目';
                    $paramsArray = $this->codeAndOtherParams($code,'published');
                }
            }
            else if(count($statusList) == 1)
            {
                $code = $demandInfo[0]->code;
                $demandStatus = $statusList[0];

                if(in_array($demandStatus,$spliteStatusArray)) //1.需求条目全部状态为<已录入、开发中、变更单退回>时，需求任务联动为<已拆分>。
                {
                    $paramsArray = $this->codeAndOtherParams($code,'splited');
                }
                if($demandStatus == 'delivery') //2.需求条目全部状态为<已交付>时，需求任务联动为<已交付>。
                {
                    $paramsArray = $this->codeAndOtherParams($code,'delivered');
                }
                if($demandStatus == 'onlinesuccess') //3.需求条目全部状态为<上线成功>时，需求任务联动为<上线成功>。
                {
                    //需要获取上线时间做大的条目的id对应的code
                    $onlineTimeByDemand = max(array_column($demandInfo,'actualOnlineDate'));
                    foreach ($demandInfo as $item)
                    {
                        if($item->actualOnlineDate == $onlineTimeByDemand)
                        {
                            $code = $item->code;
                        }
                    }
                    $paramsArray = $this->codeAndOtherParams($code,'onlined',$onlineTimeByDemand);
                }
                //4.若需求条目状态只存在已挂起、已关闭两种状态的情况，则联动需求任务时判断该任务是否为已挂起/待发布，任务也为已挂起/待发布则不更新任务状态，反之需求任务更新为已发布。
                if($demandStatus == 'suspend' or $demandStatus == 'closed')
                {
                    if(!in_array($requirementObj->status,['topublished','closed']))
                    {
                        $paramsArray = $this->codeAndOtherParams($code,'published');
                    }
                }
            }
            else
            {
                //1、已录入、开发中、变更单退回 需求任务为<已拆分>
                if(in_array('wait',$statusList) or in_array('feedbacked',$statusList)  or in_array('chanereturn',$statusList))
                {
                    foreach ($demandInfo as $item)
                    {
                        if(in_array($item->status,$spliteStatusArray))
                        {
                            $code = $item->code;
                            continue;
                        }
                    }
                    $paramsArray = $this->codeAndOtherParams($code,'splited');
                }
                else if(in_array('delivery',$statusList)) //2.除上状态只要存在已交付，需求任务为<已交付>
                {
                    foreach ($demandInfo as $item)
                    {
                        if($item->status == 'delivery'){
                            $code = $item->code;
                            continue;
                        }
                    }
                    $paramsArray = $this->codeAndOtherParams($code,'delivered');
                }
                else if (in_array('onlinesuccess',$statusList)) //3.除上状态只要存在上线成功，需求任务为<上线成功>
                {
                    $actualOnlineDate = max(array_column($demandInfo,'actualOnlineDate'));
                    foreach ($demandInfo as $item)
                    {
                        if($item->status == 'onlinesuccess' && $item->actualOnlineDate == $actualOnlineDate){
                            $code = $item->code;
                        }
                    }
                    $paramsArray = $this->codeAndOtherParams($code,'onlined',$actualOnlineDate);
                }
                else //4.只存在已挂起和已关闭两种状态的，并且既有已挂起和已关闭 无其他状态 则联动需求任务时判断该任务是否为已挂起/待发布，任务也为已挂起/待发布则不更新任务状态，反之需求任务更新为已发布。
                {
                    $diff = array_diff(array('suspend','closed'),$statusList);
                    if(empty($diff) && !in_array($requirementObj->status,['topublished','closed']))
                    {
                        $code = '未查到需求条目';
                        $paramsArray = $this->codeAndOtherParams($code,'published');
                    }
                }
            }
        }

        return $paramsArray;
    }

    /**
     * @Notes: 获取最终code,status等数据
     * @Date: 2023/4/17
     * @Time: 14:37
     * @Interface codeAndOtherParams
     * @param string $code
     * @param string $status
     * @param string $actualOnlineDate
     * @return array
     */
    public function codeAndOtherParams($code='',$status = '',$actualOnlineDate='')
    {
        $returnArray = [];
        $returnArray['code'] = $code;
        $returnArray['requirementStatus'] = $status;
        $returnArray['actualOnlineDate'] = $actualOnlineDate;
        return $returnArray;
    }
    /**
     * @Notes: 获取需求意向关联的所有需求任务数据
     * @Date: 2023/4/11
     * @Time: 16:32
     * @Interface getRequirementInfoByOpinionID
     * @param $opinionID
     * @param string $field
     */
    public function getRequirementInfoByOpinionID($opinionID,$field = '*')
    {
       return $this->dao->select($field)->from(TABLE_REQUIREMENT)->where('opinion')->eq($opinionID)->andWhere('status')->ne('deleted')->fetchAll();
    }

    /**
     * 定时更新需求任务状态
     */
    public function changeStatus()
    {
        $requirementInfo = $this->geRequirementInfoAboutStatus();
        /**@var demandModel $demandModel*/
        $demandModel = $this->loadModel('demand');
        //用于返回记录
        $enterIds = [];
        $splitIds = [];
        $deliverIds = [];
        $onlineIds = [];
        //历史记录
        $langStatus = $this->lang->requirement->statusList;
        foreach ($requirementInfo as $item)
        {
            $demandInfo = $demandModel->getByRequirementIdLink('*',$item->id);
            $paramsArray = $this->changeRequirementStatus($demandInfo,$item);//处理需求任务的最终状态
            $requirementStatus = !empty($paramsArray) ? $paramsArray['requirementStatus'] : '';
            switch($requirementStatus)
            {
                case 'published': //已发布
                    if($item->status != 'published')
                    {
                        $enterArray = $this->insertActionArray($item->id,$paramsArray['code'],$item->status);
                        $enterIds[] = $item->id;
                        $this->updateStatusById('published',$item->id);
                        $this->loadModel('consumed')->record('requirement', $item->id, 0, 'guestjk', $item->status, 'published');
                        //更新最新发布时间
                        $this->updateNewPublishedTime($item->id);
                        if(!empty($enterArray))
                        {
                            $this->loadModel('action')->createActions('requirement', $enterArray, 'publishedscript',$langStatus,1);
                        }
                    }
                    break;
                case 'splited': //已拆分
                    if($item->status != 'splited')
                    {
                        $splitArray = $this->insertActionArray($item->id,$paramsArray['code'],$item->status);
                        $splitIds[] = $item->id;
                        $this->updateStatusById('splited',$item->id);
                        $this->loadModel('consumed')->record('requirement', $item->id, 0, 'guestjk', $item->status, 'splited');
                        if(!empty($splitArray))
                        {
                            $this->loadModel('action')->createActions('requirement', $splitArray, 'splitedscript',$langStatus,1);
                        }
                    }

                    break;
                case 'delivered': //已交付
                    if($item->status != 'delivered')
                    {
                        $deliverArray = $this->insertActionArray($item->id,$paramsArray['code'],$item->status);
                        $deliverIds[] = $item->id;
                        $this->updateStatusById('delivered',$item->id);
                        $this->loadModel('consumed')->record('requirement', $item->id, 0, 'guestjk', $item->status, 'delivered');
                        if(!empty($deliverArray))
                        {
                            $this->loadModel('action')->createActions('requirement', $deliverArray, 'deliveredscript',$langStatus,1);
                        }
                    }
                    break;
                case 'onlined': //上线成功
                    if($item->status != 'onlined')
                    {
                        $onlineStatusArray = $this->insertActionArray($item->id,$paramsArray['code'],$item->status);
                        $onlineIds[] = $item->id;
                        $onlineTimeByDemand = $paramsArray['actualOnlineDate'];
                        $this->updateStatusById('onlined',$item->id,$onlineTimeByDemand);
                        $this->loadModel('consumed')->record('requirement', $item->id, 0, 'guestjk', $item->status, 'onlined');
                        if(!empty($onlineStatusArray))
                        {
                            $this->loadModel('action')->createActions('requirement', $onlineStatusArray, 'onlinedscript',$langStatus,1);
                        }
                    }else{
                        //原状态为上线成功需将 二线单子更大的时间更新
                        $onlineTimeByDemand = $paramsArray['actualOnlineDate'];
                        $this->updateStatusById('onlined',$item->id,$onlineTimeByDemand);
                    }
                    break;
            }
            //更新交付时间
            $this->dealSolveTime($item->id);
        }
        $return = ["published requirementIds:" . implode(',', $enterIds),
                "splited requirementIds:" . implode(',', $splitIds),
                "delivered requirementIds:" . implode(',', $deliverIds),
                "onlined requirementIds:" . implode(',', $onlineIds)
        ];
        return $return;

    }

    /**
     * @Notes:获取近一年需求任务数据，用于状态联动
     * @Date: 2023/4/12
     * @Time: 18:27
     * @Interface geRequirementInfoAboutStatus
     * @return mixed
     */
    public function geRequirementInfoAboutStatus()
    {
        //迭代二十九要求需求任务状态为待发布不进行状态联动
        $requirementInfo = $this->dao->select('id,status,code')
            ->from(TABLE_REQUIREMENT)
            ->where('status')
            ->notIN("deleted,topublish,closed,underchange,deleteout")
            ->fetchAll();
        return $requirementInfo;
    }

    //更新状态
    public function updateStatusById($status,$id,$onlineTimeByDemand = '')
    {
        if(empty($onlineTimeByDemand)){
            $this->dao->update(TABLE_REQUIREMENT)->set('status')->eq($status)->set('onlineTimeByDemand')->eq(NULL)->where('id')->eq($id)->exec();
        }else{
            $this->dao->update(TABLE_REQUIREMENT)->set('status')->eq($status)->set('onlineTimeByDemand')->eq($onlineTimeByDemand)->where('id')->eq($id)->exec();
        }
    }

    /**
     * 7.2.2.1
     * 需求条目流程状态 not in （已关闭、待上线、上线成功、上线失败） > 0 时，需求任务状态需要联动为“已拆分”，
     * 联动之前需要判断需求任务的流程状态是否满足条件：需求任务流程状态 in (已发布、已拆分、已关闭、已交付、上线成功）
     */
    public function changeToSplited()
    {
        $requirements =  $this->dao->select('id,status')
            ->from(TABLE_REQUIREMENT)
            ->where('status')
            ->in("published, delivered, onlined, closed") //已发布、（已拆分 splited 已经是的就不用更新了）、已关闭、已交付、上线成功
            ->andWhere('createdDate')
            ->gt(date('Y-m-d', strtotime("-1 year")))
            ->fetchAll('id');
        $requirementIds = array_keys($requirements);
        $updateIdList = [];
        foreach ($requirementIds as $requirementId){
            $count =  $this->dao->select('id')
                ->from(TABLE_DEMAND)
                ->where('requirementID')
                ->eq($requirementId)
                ->andwhere('status')
                ->notin("deleted, closed, suspend, delivery, onlinesuccess, onlinefailed") //已关闭、待上线、上线成功、上线失败
                ->count();
            if($count > 0){
                $updateIdList[] = $requirementId;
                $this->loadModel('consumed')->recordAuto('requirement', $requirementId, 0, $requirements[$requirementId]->status, 'splited');
            }
        }
        if(count($updateIdList) > 1000){ $updateIdList = array_slice($updateIdList, 0, 1000);} //in 最多1000，多出的下次再处理
        $this->dao->update(TABLE_REQUIREMENT)->set('status')->eq('splited')->where('id')->in($updateIdList)->andwhere('status')->ne("splited")->exec();
        $this->loadModel('action')->createActions('requirement', $updateIdList, 'splited');
        return "splited requirementIds:" . implode(',', $updateIdList);
    }

    /**
     * 7.2.2.3
     * 当需求任务下未删除的需求条目数量=0，则需要联动需求任务的流程状态为“已发布”，如果需求任务状态本身是“已发布”，则不需要update
     */
    public function changeToPublished()
    {
        $requirements =  $this->dao->select('id,status')
            ->from(TABLE_REQUIREMENT)
            ->where('status')
            ->in("splited,onlined,delivered")
            ->andWhere('createdDate')
            ->gt(date('Y-m-d', strtotime("-1 year")))
            ->fetchAll('id');
        $requirementIds = array_keys($requirements);
        $updateIdList = [];
        foreach ($requirementIds as $requirementId){
            $count =  $this->dao->select('id')
                ->from(TABLE_DEMAND)
                ->where('requirementID')
                ->eq($requirementId)
                ->andwhere('status')
                ->ne("deleted")
                ->count();
            if($count == 0){
                $updateIdList[] = $requirementId;
                $this->loadModel('consumed')->recordAuto('requirement', $requirementId, 0, $requirements[$requirementId]->status, 'published');
            }
        }
        if(count($updateIdList) > 1000){ $updateIdList = array_slice($updateIdList, 0, 1000);} //in 最多1000，多出的下次再处理
        $this->dao->update(TABLE_REQUIREMENT)->set('status')->eq('published')->where('id')->in($updateIdList)->exec();
        $this->loadModel('action')->createActions('requirement', $updateIdList, 'published');
        return "published requirementIds:" . implode(',', $updateIdList);
    }

    /**
     * 当需求任务下的需求条目状态除了已关闭的，其他全部为“待上线”&& 需求任务“已拆分”时，自动流转“需求任务”状态为“已交付”，待处理人为置空，
     * 比如需求条目A和B，如果A已关闭，B为待上线，则需求任务要update 为已交付，状态联动忽略已关闭的。
     */
    public function changeToDelivered()
    {
        $requirements =  $this->dao->select('id,status')
            ->from(TABLE_REQUIREMENT)
            ->where('status')
            ->eq("splited")
            ->andWhere('createdDate')
            ->gt(date('Y-m-d', strtotime("-1 year")))
            ->fetchAll('id');
        $requirementIds = array_keys($requirements);
        $updateIdList = [];
        foreach ($requirementIds as $requirementId){
            $count =  $this->dao->select('id')
                ->from(TABLE_DEMAND)
                ->where('requirementID')
                ->eq($requirementId)
                ->andwhere('status')
                ->notin("closed, delivery, onlinesuccess, deleted") //已关闭的（和已删除），其他全部为“待上线 or onlined
                ->count();
            $countDelivery =  $this->dao->select('id')
                ->from(TABLE_DEMAND)
                ->where('requirementID')
                ->eq($requirementId)
                ->andwhere('status')
                ->eq("delivery") //已关闭的（和已删除），其他全部为“待上线
                ->count();
            if($count == 0 && $countDelivery > 0){
                $updateIdList[] = $requirementId;
                $this->loadModel('consumed')->recordAuto('requirement', $requirementId, 0, $requirements[$requirementId]->status, 'delivered');
            }
        }
        if(count($updateIdList) > 1000){ $updateIdList = array_slice($updateIdList, 0, 1000);} //in 最多1000，多出的下次再处理
        $this->dao->update(TABLE_REQUIREMENT)->set('status')->eq('delivered')->set('dealUser')->eq('')->where('id')->in($updateIdList)->exec();
        $this->loadModel('action')->createActions('requirement', $updateIdList, 'delivered');
        return "delivered requirementIds:" . implode(',', $updateIdList);
    }

    /**
     * 7.4.3.4
     * 当需求任务下的需求条目状态除了已关闭的，其他全部为“上线成功”&& 需求任务“已交付”时，自动流转“需求任务”状态为“上线成功”，待处理人为置空，
     * 比如需求条目A和B，如果A已关闭，B为上线成功，则需求任务要update 为“上线成功”，状态联动忽略已关闭的。需求任务流程状态不需要联动“上线失败”。
     */
    public function changeToOnlineByDemand()
    {
        $requirements =  $this->dao->select('id,status')
            ->from(TABLE_REQUIREMENT)
            ->where('status')
            ->in("delivered, splited")
            ->andWhere('createdDate')
            ->gt(date('Y-m-d', strtotime("-1 year")))
            ->fetchAll('id');
        if(empty($requirements)) return "";
        $requirementIds = array_keys($requirements);
        $updateIdList = [];
        foreach ($requirementIds as $requirementId){
            $count =  $this->dao->select('id')
                ->from(TABLE_DEMAND)
                ->where('requirementId')
                ->eq($requirementId)
                ->andwhere('status')
                ->notin("closed, onlinesuccess, deleted")
                ->count();
            $onlineDate = $this->dao->select('actualOnlineDate') //至少有一条成功的才算 取最新上线时间
                ->from(TABLE_DEMAND)
                ->where('requirementId')
                ->eq($requirementId)
                ->andwhere('status')
                ->eq("onlinesuccess")
                ->orderby("actualOnlineDate_desc")
                ->fetch('actualOnlineDate');
            if($count == 0 && $onlineDate){
                $updateIdList[] = $requirementId;
                $this->dao->update(TABLE_REQUIREMENT)->set('status')->eq('onlined')->set('dealUser')->eq('')->set('onlineTimeByDemand ')->eq($onlineDate)->where('id')->eq($requirementId)->exec();
                $this->loadModel('consumed')->recordAuto('requirement', $requirementId, 0, $requirements[$requirementId]->status, 'onlined');
            }
        }
//        if(count($updateIdList) > 1000){ $updateIdList = array_slice($updateIdList, 0, 1000);} //in 最多1000，多出的下次再处理
//        $this->dao->update(TABLE_REQUIREMENT)->set('status')->eq('onlined')->set('dealUser')->eq('')->where('id')->in($updateIdList)->exec(); //需要更新上线时间不能统一处理了
        $this->loadModel('action')->createActions('requirement', $updateIdList, 'onlined');
        return "onlined requirementIds:" . implode(',', $updateIdList);
    }
    /**
     * Project: chengfangjinke
     * Method: createChanges
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:24
     * Desc: This is the code comment. This method is called createChanges.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $old
     * @param $new
     * @return array
     */
    public function createChanges($old, $new)
    {
        /* 加载需求条目涉及的部分、用户和项目等相关对象信息。*/
        $depts    = $this->loadModel('dept')->getOptionMenu();
        $users    = $this->loadModel('user')->getPairs('noletter');
        $projects = $this->loadModel('projectplan')->getPairs();
        $apps     = $this->loadModel('application')->getapplicationNameCodePairs();
        $lines    = $this->loadModel('product')->getLinePairs();
        $products = $this->product->getPairs();

        /* 通过各字段新值与旧值进行对比，找出差异记录。*/
        $changes = array();
        foreach($new as $key => $value)
        {
            switch($key)
            {
            case 'dept':
                $value     = zget($depts, $value, $value);
                $old->$key = zget($depts, $old->$key, $old->$key);
                break;
            case 'owner':
                $value     = zget($users, $value, $value);
                $old->$key = zget($users, $old->$key, $old->$key);
                break;
            case 'project':
                $value     = zget($projects, $value, $value);
                $old->$key = zget($projects, $old->$key, $old->$key);
                break;
            case 'app':
                $value     = zget($apps, $value, $value);
                $old->$key = zget($apps, $old->$key, $old->$key);
                break;
            case 'product':
                if($value)
                {
                    $arrValues = explode(',', $value);
                    $value     = '';
                    foreach($arrValues as $arrValue)
                    {
                        if($arrValue == '') continue;
                        $value   .= zget($products, $arrValue, $arrValue) . ','; 
                    }
                    $value = trim($value, ',');
                }
                if($old->$key)
                {
                    $arrValues = explode(',', $old->$key);
                    $old->$key = '';
                    foreach($arrValues as $arrValue)
                    {
                        if($arrValue == '') continue;
                        $old->$key .= zget($products, $arrValue, $arrValue) . ',';
                    }
                    $old->$key = trim($old->$key, ',');
                }
                break;
            case 'line':
                if($value)
                {
                    $arrValues = explode(',', $value);
                    $value     = '';
                    foreach($arrValues as $arrValue)
                    {
                        if($arrValue == '') continue;
                        $value   .= zget($lines, $arrValue, $arrValue) . ','; 
                    }
                    $value = trim($value, ',');
                }
                if($old->$key)
                {
                    $arrValues = explode(',', $old->$key);
                    $old->$key = '';
                    foreach($arrValues as $arrValue)
                    {
                        if($arrValue == '') continue;
                        $old->$key .= zget($lines, $arrValue, $arrValue) . ',';
                    }
                    $old->$key = trim($old->$key, ',');
                }
                break;
            }

            if(isset($old->$key) and $value != stripslashes($old->$key))
            {
                $diff = '';
                if(substr_count($value, "\n") > 1     or
                    substr_count($old->$key, "\n") > 1 or
                    strpos('name,title,desc,spec,steps,content,digest,verify,report', strtolower($key)) !== false)
                {
                    $diff = commonModel::diff($old->$key, $value);
                }
                $changes[] = array('field' => $key, 'old' => $old->$key, 'new' => $value, 'diff' => $diff);
            }
        }
        return $changes;
    }

    public function assignTo($requirementID){
        $this->app->loadLang('demand');
        $assignedTo = $_POST['assignedTo'];
        if(empty($assignedTo))
        {
            dao::$errors['assignedTo'] = sprintf($this->lang->requirement->emptyObject, $this->lang->requirement->assignedTo);
        }

        $comment = $_POST['comment'];
        if(empty($comment))
        {
            dao::$errors['comment'] = sprintf($this->lang->requirement->emptyObject, $this->lang->requirement->comment);
        }
        $this->tryError();
        $requirement = $this->getById($requirementID);
        $dealUserStr = $requirement->dealUser;
        $dealUsers = explode(',' , $dealUserStr);
        foreach ($dealUsers as $k=>$dealUser){
            if($dealUser == $this->app->user->account){
                unset($dealUsers[$k]);
            }
            if($dealUser == $assignedTo){
                unset($dealUsers[$k]);
            }
        }


        //已发布更新最新发布时间
        if($requirement->status == 'published')
        {
            $this->updateNewPublishedTime($requirementID);
        }

        array_push($dealUsers, $assignedTo);
        $dealUserStr = implode(',',$dealUsers);
        // 外部单子待确认指派产品经理
        if($requirement->status == 'topublish' and !empty($requirement->entriesCode)){
            $this->dao->update(TABLE_REQUIREMENT)
                ->set('productManager')->eq($assignedTo)
                ->where('id')->eq($requirementID)
                ->exec();
        }
        //迭代三十四 内部反馈开始和截止时间修改为指定反馈人的落库时间（如果反馈人不发生变化不更新） 取消原发布落库时间
        //迭代二十九 已发布->已发布 更新反馈开始和截止时间  若存在反馈后又出现的已发布→已发布，该场景数据过滤掉
//        $startTime = date('Y-m-d H:i:s',time());
//        $hms = substr($startTime,10);
//        $data = new stdClass();
//        if($requirement->status == 'published' && $requirement->createdBy == 'guestcn' && $requirement->isUpdateOverStatus == 1)
//        {
//            $editedUpdate = $this->checkFeedBack('requirement',$requirementID,'edited','id_desc','总中心接口同步更新');
//            //有更新 查询更新时间后 首次
//            if($editedUpdate)
//            {
//                $baseTime = $editedUpdate->date;
//                //查询更新后的首次反馈后时间
//                $feedBackTime = $this->checkFeedBack('requirement',$requirementID,'createfeedbacked','id_asc','',$baseTime);
//                if(!$feedBackTime)
//                {
//                    $daysInside = $this->lang->demand->expireDaysList['insideDays'];
//                    $data->feekBackStartTime = $startTime;
//                    $data->feekBackEndTimeInside  = helper::getTrueWorkDay($startTime,$daysInside,true).$hms; //内部结束时间
//                }
//            }else{ //无更新 查询首次反馈
//                //查询更新后的首次反馈后时间
//                $feedBackTime = $this->checkFeedBack('requirement',$requirementID,'createfeedbacked','id_asc','');
//                if(!$feedBackTime)
//                {
//                    $daysInside = $this->lang->demand->expireDaysList['insideDays'];
//                    $data->feekBackStartTime = $startTime;
//                    $data->feekBackEndTimeInside  = helper::getTrueWorkDay($startTime,$daysInside,true).$hms; //内部结束时间
//                }
//            }
//            if(isset($data->feekBackStartTime))
//            {
//                $this->dao->update(TABLE_REQUIREMENT)->data($data)->where('id')->eq($requirementID)->exec();
//            }
//
//        }

        if((empty($requirement->entriesCode) and $requirement->status == 'topublish') or $requirement->status == 'published' or $requirement->status == 'splited'){
            $projectManagers = $requirement->projectManager;
            if(strpos($requirement->projectManager, $assignedTo) === false){
                $projectManagers = $requirement->projectManager.','.$assignedTo;
            }
            $this->dao->update(TABLE_REQUIREMENT)
                ->set('projectManager')->eq($projectManagers)
                ->where('id')->eq($requirementID)
                ->exec();
        }
        if($dealUserStr != $requirement->dealUser){
            $this->dao->update(TABLE_REQUIREMENT)
                ->set('dealUser')->eq($dealUserStr)
                ->set('ignoreStatus')->eq(0)
                ->where('id')->eq($requirementID)
                ->exec();
        }else{
            $this->dao->update(TABLE_REQUIREMENT)
                ->set('dealUser')->eq($dealUserStr)
                ->where('id')->eq($requirementID)
                ->exec();
        }

        $this->loadModel('file')->updateObjectID($this->post->uid, $requirementID, 'requirement');
        $this->file->saveUpload('requirement', $requirementID);
        $newRequirement = $this->getByID($requirementID);

        $this->loadModel('consumed');
        $this->consumed->record('requirement', $requirementID, 0, $this->app->user->account, $requirement->status, $requirement->status, array());


        return common::createChanges($requirement, $newRequirement);
    }

    /**
     * @param $requirementID
     * @return array
     * 拆分需求任务->需求条目
     */
    public function subdivideDemand($requirementID){
        //校验数据
        $data = $_POST;
        $uid = $this->post->uid;
        //查询需求任务信息
        $requirementObj = $this->getByID($requirementID);
        //查询需求意向信息
        $opinionObj = $this->loadModel("opinion")->getByID($requirementObj->opinion);
        //清总同步的数据拆分需求任务需要校验 所属需求意向的下一节点处理人和需求分类
        if($requirementObj->createdBy == 'guestcn'){
            if(empty($opinionObj->category)){
                return dao::$errors['infoEmpty'] = "所属意向未补充完整请联系产品经理进行补充";
            }
        }
        //拼装数据
        for ($i = 0; $i <= $data['descIndex']; $i++){
           /* if($data['flag' . $i] != '1'){
                $data['execution' . $i] = isset($data['execution' . $i]) ? $data['execution' . $i] : $data['executionid' . $i];
                if(!isset($this->lang->requirement->subdivideRequired['execution'])) $this->lang->requirement->subdivideRequired['execution'] =  '所属阶段';
            }else {
                unset($this->lang->requirement->subdivideRequired['execution']);
            }*/
            $data['app' . $i] = isset($data['app' . $i]) ? explode(',',$data['app' . $i]) : '';
            foreach ($this->lang->requirement->subdivideRequired as $parms => $desc){
                if($data[$parms . $i] == '' or empty($data[$parms . $i]) or ($parms == 'app' and count($data[$parms . $i]) == 1 and $data[$parms . $i][0] == ''))
                {
                    return dao::$errors[$parms . $i] = sprintf($this->lang->requirement->emptyObject, $desc);
                }
            }

            $demand = new stdclass();
            $demand->rcvDate = helper::now();
            //产品和版本不是无，应用系统只能选择一个
            if($data['product' . $i] != '99999' && $data['productPlan' . $i] != '1' ){
                $apps = explode(',',trim(implode(',', $data['app' . $i]),','));
                if(count($apps) > 1){
                    return dao::$errors['app' . $i] = $this->lang->requirement->productAndPlanTips;
                }
            }
            if($data['fixType' . $i] == 'second')
            {
                // 判断二线实现的解决方案必须为二线项目。
                $plan = $this->dao->select('secondLine')->from(TABLE_PROJECTPLAN)->where('deleted')->eq(0)->andWhere('project')->eq($data['project' . $i])->fetch();
                if(empty($plan->secondLine))  return dao::$errors['project' . $i] = $this->lang->requirement->noSecondLinse;
            }
            // 根据【所属应用系统】处理系统分类字段的值。
            $paymentIdList = array();
            if(isset($data['app' . $i]))
            {
                foreach($data['app' . $i] as $appID)
                {
                    if(!$appID) continue;
                    $paymentType = $this->dao->select('isPayment')->from(TABLE_APPLICATION)->where('id')->eq($appID)->fetch('isPayment');
                    if($paymentType) $paymentIdList[] = $paymentType;
                }
                $demand->isPayment = implode(',', $paymentIdList);
            }

//            if(!$this->loadModel('common')->checkJkDateTime($data['endDate' . $i]))
//            {
//                dao::$errors['endDate'.$i] =  sprintf($this->lang->requirement->emptyObject, $this->lang->requirement->endDate);
//                return;
//            }

            if(!$this->loadModel('common')->checkJkDateTime($data['end' . $i]))
            {
                dao::$errors['end'.$i] =  sprintf($this->lang->requirement->emptyObject, $this->lang->requirement->planEnd);
                return;
            }

            //计划完成时间不允许大于所属任务的计划完成时间
            if($requirementObj->planEnd != '0000-00-00'){
                if(strtotime($data['end' . $i]) > strtotime($requirementObj->planEnd))
                {
                    $errors[''] = $this->lang->requirement->editEndSubdivideDemandTip;
                    return dao::$errors = $errors;
                }
            }

            //下一节点处理人
            $nextUser = $data['dealUser'];
            if(empty($nextUser))
            {
                return dao::$errors['dealUser'] = sprintf($this->lang->requirement->emptyObject, $this->lang->requirement->nextUser);
            }
        }
        for ($i = 0; $i <= $data['descIndex']; $i++){
            $this->tryError();

            if(dao::isError()) continue;
            $demand->opinionID   = $requirementObj->opinion;
            $demand->title       = $data['title' . $i];
            $demand->type        = $opinionObj->sourceMode;
            $demand->source      = $opinionObj->sourceName;
            $demand->union       = $opinionObj->union;
            $demand->rcvDate     = $opinionObj->receiveDate;
//            $demand->endDate     = $data['endDate' . $i];
            $demand->end         = $data['end' . $i];
            $demand->dealUser    = $nextUser;
            $demand->createdBy   = $this->app->user->account;
            $demand->createdDate = helper::now();
            $demand->createdDept = $this->app->user->dept;
            $demand->lastDealDate = date('Y-m-d');
            $demand->fixType = $data['fixType' . $i];
            $demand->project = $data['project' . $i];
            $demand->execution = isset($data['execution' . $i]) ? $data['execution' . $i] : '';
            $demand->app = implode(',', $data['app' . $i]);
            $demand->product = $data['product' . $i];
            if(is_numeric($data['productPlan' . $i])){
                $demand->productPlan = $data['productPlan' . $i];
            }else{
                $demand->productPlan = 0;
            }
            $demand->acceptUser = $data['acceptUser' . $i];
            //实施部门需要根据实施责任人查询，构造数据
            if(!empty($demand->acceptUser)){
                $responsiblePersonObj = $this->loadModel('user')->getByAccount($demand->acceptUser);
                $demand->acceptDept = $responsiblePersonObj->dept;
            }
            $demand->requirementID = $requirementObj->id;

            /*迭代三十四 二线实现项目 二线月报跟踪标记位标记为纳入 项目实现为不纳入*/
            $demand->secondLineDevelopmentRecord = 2;
            if($data['fixType' . $i] == 'second')
            {
                $demand->secondLineDevelopmentRecord = 1;
            }

            // 处理富文本字段内容。
            unset($_POST);
            $_POST['desc'] = $data['desc' . $i];
            $postData = fixer::input('post')->stripTags('desc', $this->config->allowedTags)->get();
            $demand->desc = $postData->desc;
            $demand = $this->loadModel('file')->processImgURL($demand, 'desc', $uid . $i);

            $_POST['reason'] = $data['reason' . $i];
            $postData = fixer::input('post')->stripTags('reason', $this->config->allowedTags)->get();
            $demand->reason = $postData->reason;
            $demand = $this->loadModel('file')->processImgURL($demand, 'reason', $uid . $i);

            $_POST['progress'] = $data['progress' . $i];
            $postData = fixer::input('post')->stripTags('progress', $this->config->allowedTags)->get();
            if($postData->progress){
                $users = $this->loadModel('user')->getPairs('noclosed');
                $demand->progress = '<span style="background-color: #ffe9c6">' .helper::now()." 由<strong>".zget($users,$this->app->user->account,'')."</strong>新增".'<br></span>'.$postData->progress;
            }

            $demand = $this->loadModel('file')->processImgURL($demand, 'progress', $uid . $i);

            if($requirementObj->changeLock == 2){
                $response['result']  = 'fail';
                $response['message'] = $this->lang->requirement->changeIng;
                $this->send($response);
            }

            $this->dao->insert(TABLE_DEMAND)->data($demand)->autoCheck()->exec();

            $demandID = $this->dao->lastInsertID();

            $this->loadModel('file')->updateObjectID($uid . $i, $demandID, 'demand');
            $this->file->saveUpload('demand', $demandID, '', 'files' . $i);

            // 更新需求代号。
            $date = date('Y-m-d');
            $number = $this->dao->select('count(id) c')->from(TABLE_DEMAND)->where('createdDate')->eq($date)->andWhere('sourceDemand')->eq(1)->fetch('c');
            $code = 'CFIT-D-' . date('Ymd-') . sprintf('%02d', $number);
            $this->dao->update(TABLE_DEMAND)->set('code')->eq($code)->where('id')->eq($demandID)->exec();

            // 将本地拆分工时平均后，记录到需求的工时中。 --迭代22去掉工时
            $this->loadModel('consumed')->record('demand', $demandID, 0, $this->app->user->account, '', 'wait', $data['mailto']);

            // 记录拆分的动作。
            $actionId = $this->loadModel('action')->create('demand', $demandID, 'created');
//            $this->loadModel('demand')->sendmail($demandID, $actionId);
            $demandIdList[] = $demandID;
        }
        /* 将需求意向的状态修改为已拆分。*/
        if(!empty($demandIdList))
        {
            $this->dao->update(TABLE_REQUIREMENT)->set('status')->eq('splited')
                ->where('id')->eq($requirementID)
                ->exec();
            $this->loadModel('action')->create('requirement', $requirementID, 'splited', "");
            $this->loadModel('consumed')->record('requirement', $requirementID, 0, $this->app->user->account, $requirementObj->status, 'splited', $data['mailto']);
        }
        return $demandIdList;
    }

    /**
     * 尝试报错 或需要rollback
     */
    public function tryError($rollBack = 0)
    {
        if(dao::isError())
        {
            if($rollBack == 1){
                $this->dao->rollBack();
            }
            $response['result']  = 'fail';
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

    /**
     * 根据需求任务获取需求
     * @param $requirementID
     * @param $select
     *
     * @return void
     */
    public function getDemandByRequirement($requirementID, $select = '*'){
        $demands =  $this->dao->select($select)
            ->from(TABLE_DEMAND)
            ->where('requirementID')->eq("$requirementID")
            ->andWhere('status')->notIN('closed,deleted')
            ->fetchAll('id');
        return $demands;
    }

    /**
     * Desc: 根据id获取任务数据
     * Date: 2022/8/12
     * Time: 9:52
     *
     * @param $requirementID
     * @return mixed
     *
     */
    public function getByRequirementID($requirementID)
    {
        return $this->dao->select('id,deadLine,entriesCode,end,planEnd,analysis,`desc`,app,changeLock')->from(TABLE_REQUIREMENT)->where('id')->eq($requirementID)->fetch();
    }

    /**
     * 导出模板数据源
     * @return void
     */
    public function setListValue()
    {
        /* 导出需求意向数据调用该方法设置下拉选项的可选值。*/
        $users = $this->loadModel('user')->getPairs('noletter|noclosed');
        $newUsers = array();
        foreach ($users as $account => $name) {
            if (!$account) continue;
            $newUsers[$account] = $name . "(#$account)";
        }

        $opinions = $this->loadModel('opinion')->getPairsByRequmentBrowse();
        $newOpinions = array();
        foreach ($opinions as $id => $name) {
            $newOpinions[$id] = $name . "(#$id)";
        }

        $apps = $this->loadModel('application')->getapplicationNameCodePairs();
        $newApps = array();
        foreach ($apps as $id => $name) {
            $newApps[$id] = $name . "(#$id)";
        }

        $statusList = $this->lang->requirement->searchstatusList;

        $this->post->set('statusList', array_values($statusList));
        $this->post->set('createdByList', array_values($newUsers));
        $this->post->set('opinionIDList', array_values($newOpinions));
        $this->post->set('dealUserList', array_values($newUsers));
        $this->post->set('appList',array_values($newApps));
        $this->post->set('listStyle', $this->config->requirement->exportlist->listFields);
        $this->post->set('extraNum', 0);
    }

    /**
     * 导入数据
     * @return void
     */
    public function createFromImport()
    {
        /* 加载action、opinion和file模块，并获取导入数据。*/
        $this->loadModel('action');
        $this->loadModel('requirement');
        $this->loadModel('file');
        $now = helper::now();
        $data = fixer::input('post')->get();

        /* 加载purifier富文本过滤器。*/
        $this->app->loadClass('purifier', true);
        $purifierConfig = HTMLPurifier_Config::createDefault();
        $purifierConfig->set('Filter.YouTube', 1);
        $purifier = new HTMLPurifier($purifierConfig);

        /* 获取旧的需求意向数据。*/
        if (!empty($_POST['id'])) {
            $oldRequirements = $this->dao->select('*')->from(TABLE_REQUIREMENT)->where('id')->in(($_POST['id']))->fetchAll('id');
        }

        /* 初始化导入数据变量。*/
        $requirements = array();
        $line = 1;
        
        $names = array();
        foreach ($data->opinionID as $key => $opinionID) {
            /* 定义一个导入数据对象，如果name参数为空，则跳过该行数据。*/
            $requirementData = new stdclass();
            $specData = new stdclass();
            /*if (!$opinionID) continue;*/
            if(array_search($data->name[$key],$names)){
                dao::$errors[] = sprintf($this->lang->requirement->duplicateNameError, array_search($data->name[$key],$names), $line);
            }else{
                $names[$line] = $data->name[$key];
            }

            /* 将页面获取到的数据赋值给对象。*/
            $requirementData->opinion = $opinionID;
            // $requirementData->deadLine = date('Y-m-d',strtotime($data->deadLineDate[$key]));
            $requirementData->name = $data->name[$key];
            $requirementData->app = $data->app[$key];
            $requirementData->desc = $data->desc[$key];
            $requirementData->comment = $data->comment[$key];
            $requirementData->dealUser = $data->dealUser[$key];

            $requirementData->createdBy = $data->createdBy[$key];
            $requirementData->onlineTimeByDemand = $data->onlineTimeByDemand[$key];
            $requirementData->projectManager = $data->projectManager[$key];
            $requirementData->status = $data->status[$key];
            $requirementData->sourceRequirement = 1;


            if(empty($opinionID))
            {
                dao::$errors[] = sprintf($this->lang->requirement->noRequire, $line,$this->lang->requirement->opinionID);
            }

            /* 判断那些字段是必填的。*/
            if (isset($this->config->requirement->import->requiredFields)) {
                $requiredFields = explode(',', $this->config->requirement->import->requiredFields);
                foreach ($requiredFields as $requiredField) {
                    $requiredField = trim($requiredField);
                    if (empty($requirementData->$requiredField)) dao::$errors[] = sprintf($this->lang->requirement->noRequire, $line, $this->lang->requirement->$requiredField);
                }
            }
            unset($requirementData->consumed);
            $requirements[$key]['requirementData'] = $requirementData;
            $line++;
        }

        /* 判断是否由必填项，如果有，则提示错误信息。*/
        if (dao::isError()) die(js::error(dao::getError()));

        /* 进行导入数据处理。*/
        $this->dao->begin(); //调试完逻辑最后开启事务
        foreach ($requirements as $key => $newRequirement) {
            /* 判断当前数据是否已存在，不存在的则为$opinionID赋值为0。*/
            $requirementID = 0;
            $requirementData = $newRequirement['requirementData'];
            if (!empty($_POST['id'][$key]) and empty($_POST['insert'])) {
                $requirementID = $data->id[$key];
                if (!isset($oldRequirements[$requirementID])) $requirementID = 0;
            }
            /* 如果$opinionID有值，则说明需求意向已存在，按照更新的情况来处理。*/
            if ($requirementID) {
                unset($requirementData->createdBy);
                $oldRequirement = $oldRequirements[$requirementID];
                $requirementChanges = common::createChanges($oldRequirement, $requirementData);

                if ($requirementChanges) {
                    $this->dao->update(TABLE_REQUIREMENT)
                        ->data($requirementData)
                        ->autoCheck()
                        ->batchCheck($this->config->requirement->create->requiredFields, 'notempty')
                        ->where('id')->eq((int)$requirementID)->exec();

                    if (!dao::isError()) {
                        if ($requirementChanges) {
                            $this->dao->update(TABLE_REQUIREMENTSPEC)->set('`desc`')->eq($requirementData->desc)
                                ->where('requirement')->eq($requirementID)
                                ->andWhere('version')->eq($oldRequirement->version)
                                ->exec();
                            $actionID = $this->loadModel('action')->create('requirement', $requirementID, 'import', '','',$requirementData->createdBy);
                            $this->action->logHistory($actionID, $requirementChanges);
                            //更新工时
                            $this->dealConsumed($requirementID,0,$this->app->user->account,$oldRequirement->status);
                        }
                    }else{
                        $this->dao->rollBack();
                        die(js::error(dao::getError()));
                    }
                }
            } else {
                
                $opinionObj = $this->loadModel('opinion')->getByID($opinionID);
                // 更新需求代号。
                $date   = helper::today();
                $codeBefore = substr( $date, 0, 4) . sprintf('%03d', $requirementData->opinion);
                $number = $this->dao->select('count(id) c')
                    ->from(TABLE_REQUIREMENT)
                    ->where('code')
                    ->like($codeBefore.'%')
                    ->andWhere('sourceRequirement')
                    ->eq(1)
                    ->fetch('c');

                $code   = $codeBefore . '-' . sprintf('%02d', $number+1);
                /* 如果是全新插入的需求意向，处理好数据后，执行SQL进行数据插入。*/
                $requirementData->createdDate = $now;
                $requirementData->code        = $code;
                $requirementData->createdDate = helper::now();
                $requirementData->productManager   = $requirementData->createdBy;
                if(empty($requirementData->dealUser)){
                    $requirementData->dealUser = $requirementData->projectManager;
                }
                if($requirementData->status == 'delivered' || $requirementData->status == 'onlined'){
                    unset($requirementData->dealUser);
                }
                        //同步需求意向字段
                $requirementData->sourceMode = $opinionObj->sourceMode;
                $requirementData->sourceName = $opinionObj->sourceName;
                $requirementData->union = $opinionObj->union;
                $requirementData->deadline = $opinionObj->deadline;
                $requirementData->dateByOpinion = $opinionObj->date;
                $requirementData->nameByOpinion = $opinionObj->name;
                $this->dao->insert(TABLE_REQUIREMENT)->data($requirementData)
                   /* ->batchCheck($this->config->requirement->create->requiredFields, 'notempty')*/
                    ->autoCheck()->exec();
                if (!dao::isError()) {
                    $requirementID = $this->dao->lastInsertID();
                    $spec = new stdClass();
                    $spec->name        = $requirementData->name;
                    $spec->requirement = $requirementID;
                    $spec->desc =  $requirementData->desc;
                    $spec->code = $code;
                    $spec->createdBy   = $requirementData->createdBy;
                    $spec->createdDate = helper::now();
                    $this->dao->insert(TABLE_REQUIREMENTSPEC)->data($spec)->exec();
                    $actionID = $this->loadModel('action')->create('requirement',$requirementID, 'import', '');
                    $this->loadModel('consumed');
                    $this->consumed->record('requirement', $requirementID, 0, $requirementData->createdBy, '', $requirementData->status, array());
                }else{
                    $this->dao->rollBack();
                    die(js::error(dao::getError()));
                }
            }
        }
        $this->dao->commit();
        /* 判断数据是否处理完毕，处理完毕则删除导入文件，并清除session信息。*/
        if ($this->post->isEndPage) {
            unlink($this->session->fileImport);
            unset($_SESSION['fileImport']);
        }
    }

    function validateDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
    public function closeApi($requirementID,$dealcomment,$userCount=''){
        if ($userCount == ''){
            $userCount = $this->app->user->account;
        }
        $requirement = $this->getByID($requirementID);
        if(empty($dealcomment)){
            dao::$errors['dealcomment'] = sprintf($this->lang->requirement->emptyObject, $this->lang->requirement->dealcomment);
            $response['result']  = 'fail';
            $response['message'] = dao::getError();
            $this->send($response);
        }
        /* 当请求方式为post时，更新需求条目的状态为关闭。判断所属需求意向下的需求条目都关闭时，关闭需求意向。*/
        $this->dao->update(TABLE_REQUIREMENT)->set('status')->eq('closed')->set('lastStatus')->eq($requirement->status)->set('closedBy')->eq($userCount)->set('closedDate')->eq(helper::now())->where('id')->eq($requirementID)->exec();

        $demands = $this->loadModel("demand")->getByRequirementID('*',$requirementID);
        $data1 = new stdclass();
        foreach($demands as $demand){
            if($demand->status == 'suspend'){
                continue;
            }
            $data1->lastStatus = $demand->status; // 记录关闭前状态
            $data1->status = 'suspend';
            $data1->closedBy = $userCount;
            $data1->closedDate = helper::today();
            $this->dao->update(TABLE_DEMAND)
                ->data($data1)
                ->where('id')->eq($demand->id)
                ->exec();
            if(!dao::isError())
            {
                $this->loadModel('action')->create('demand', $demand->id, 'suspended', $dealcomment,'',$userCount);
                $this->loadModel('consumed')->record('demand', $demand->id, 0, $userCount, $demand->status, 'suspended');
            }
        }
        $this->loadModel('consumed')->record('requirement', $requirementID, 0, $userCount, $requirement->status, 'closed', array());
        $this->loadModel('action')->create('requirement', $requirementID, 'suspenditem', $dealcomment,'',$userCount);
        return true;
    }
    public function getByCodes($entriesCodes)
    {
        $requirements = $this->dao->select('*')->from(TABLE_REQUIREMENT)->where('entriesCode')->in($entriesCodes)->fetchAll();
//        $sql = "select * from ".TABLE_REQUIREMENT." where find_in_set('".$entriesCodes."',entriesCode)";
//        $requirements = $this->dao->query($sql)->fetchall();
        return $requirements;
    }

    /**
     * 解除变更锁
     * @Interface updateLock
     * @param $requirementID
     */
    public function updateLock($requirementID)
    {
        /**
         * @var opinionModel $opinionModel
         * @var demandModel $demandModel
         */
        $post = fixer::input('post')->get();
        $opinionModel = $this->loadModel('opinion');
        $demandModel = $this->loadModel('demand');
        //获取需求条目id集合
        $demandsInfo = $demandModel->getDemandsByRequirementIds($requirementID,'id');
        $demandIDs = array_column($demandsInfo,'id');
        if($post)
        {
            $affectsIdsList = $this->selectAffectIds($demandIDs); //受影响任务相关ids集合

            $this->dao->begin();
            $this->dao->update(TABLE_REQUIREMENT)->set('changeLock')->eq(1)->where('id')->eq($requirementID)->exec();
            $this->dao->update(TABLE_DEMAND)->set('changeLock')->eq(1)->where('id')->in($demandIDs)->exec();
            if(!empty($affectsIdsList))
            {
                //更新交付管理
                $opinionModel->dealChangeLock($affectsIdsList,1);
            }
            $this->dao->commit();

        }
        return true;
    }

    /**
     * @Notes: 更新内部反馈是否超时 只针对清总同步数据 ifOverDate内部 ifOverTimeOutSide外部
     *         迭代二十九新修改
     * @Date: 2023/8/10
     * @param $field
     * @param $requirementID
     * @Interface updateRequirementIfOverDate
     */
    public function updateRequirementIfOverDate($field,$requirementID = 0)
    {
        /**
         * 获取内部反馈需更新的数据
         * @var consumedModel $consumedModel
         * @var reviewModel $reviewModel
         */
        $requirementInfo = $this->dao->select('id,feekBackStartTime,status,feekBackStartTimeOutside,feekBackEndTimeInside,feekBackEndTimeOutSide,deptPassTime,innovationPassTime,ifOverDate,version,ifOverTimeOutSide,createdDate')->from(TABLE_REQUIREMENT)
            ->where('createdBy')->eq('guestcn')
            ->beginIF(!empty($requirementID))->andWhere('id')->eq($requirementID)
            ->fetchAll();
        $finalIdList = []; //超期数组
        $idNoOverList = [];//未超期数组
        if($requirementInfo){
            foreach ($requirementInfo as $value) {
                $id = $value->id;
                //内部反馈超时处理 待发布状态不做 状态更新
                if($field == 'ifOverDate' && $value->status != 'topublish'){
                    $endTime = $value->feekBackEndTimeInside; //内部反馈截止时间
                    $passTime = date('Y-m-d H:i:s',time()); //若没有进入反馈单审核取系统当前时间进行比较
                    //部门审核通过的时间
                    $deptPassTime = $value->deptPassTime;
                    if(!empty($deptPassTime) && $deptPassTime != '0000-00-00 00:00:00')
                    {
                        $passTime = $deptPassTime;
                    }

                    //取不到待发布到已发布 或 已发布到已发布 默认时否
                    if(empty($endTime) || $endTime == '0000-00-00 00:00:00'){
                        $idNoOverList[] = $id;
                    }else{
                        if($passTime > $endTime){ //已超时
                            $finalIdList[] = $id;
                        }else{
                            $idNoOverList[] = $id;
                        }
                    }

                }

                //外部超时处理
                if($field == 'ifOverTimeOutSide'){
                    $endTime = $value->feekBackEndTimeOutSide; //外部反馈截止时间
                    $passTime = date('Y-m-d H:i:s',time()); //：反馈单【接口首次调用成功时间】，若没有进入反馈单审核取系统当前时间进行比较
                    $innovationPassTime = $value->innovationPassTime;
                    if(!empty($innovationPassTime) && $innovationPassTime != '0000-00-00 00:00:00')
                    {
                        $passTime = $innovationPassTime;
                    }

                    if($passTime > $endTime){ //已超时
                        $finalIdList[] = $id;
                    }else{
                        $idNoOverList[] = $id;
                    }
                }

            }
            $this->updateRequirementOverDate($field,2,$finalIdList);
            $this->updateRequirementOverDate($field,1,$idNoOverList);

        }
        return $finalIdList;
    }

    /**
     * @Notes: 更新内外部是否超时
     * @Date: 2023/4/19
     * @Interface updateRequirementOverDate
     * @param $field
     * @param $result
     * @param $ids
     */
    public function updateRequirementOverDate($field,$result,$ids)
    {
        $this->dao->update(TABLE_REQUIREMENT)->set($field)->eq($result)->where('id')->in($ids)->exec();
    }

    //喧喧发信
    public function getXuanxuanTargetUser($obj,$objectType, $objectID, $actionType, $actionID, $actor = '')
    {
        $requirement  = $obj;
        /* Get action info. */
        $action          = $this->loadModel('action')->getById($actionID);
        $history         = $this->action->getHistory($actionID);
        $action->history = isset($history[$actionID]) ? $history[$actionID] : array();

        $toList = '';

        if($action->action == 'created' || $action->action == 'edited' || $action->action == 'subdivide' || $action->action == 'assigned' || $action->action == 'subdivided')
        {
            /* 获取已确认下一节点审核人*/
            $reviewer = $requirement->dealUser;
            $toList   = trim($reviewer, ',');
        }else if($action->action == 'syncstatus'){
            $reviewer = $requirement->feedbackBy;
            $toList   = trim($reviewer, ',');
        }elseif($action->action == 'deleted'){
            $reviewer = $requirement->productManager.','.$requirement->projectManager.','.$requirement->dealUser.','.$requirement->createdBy;
            $toList   = trim($reviewer, ',');
        }
        else{
            $reviewer = $requirement->feedbackDealUser;
            $toList   = trim($reviewer, ',');
        }

        $changeInfo = $this->getChangeInfoByRequirementIdInStatus($requirement->id);
        //需求意向变更邮件
        if($action->action == 'changed' || $action->action == 'editchanged' || $action->action == 'reviewchange')
        {
            $toList = $requirement->changeDealUser;

            if(!empty($changeInfo))
            {
                if($changeInfo->status == 'pending')
                {
                    $toList = $changeInfo->nextDealUser;
                }
                if($changeInfo->status == 'back')
                {
                    $toList = $changeInfo->createdBy;
                }
            }
        }

        $url = '';
        if($requirement->sourceRequirement == 2){
            $server   = $this->loadModel('im')->getServer('zentao');
            $url = $server . helper::createLink($objectType.'inside', 'view', "id=$objectID", 'html').'#app=backlog';
        }
        $subcontent = [];
        $subcontent['headTitle']    = '';
        $subcontent['headSubTitle'] = '';
        $subcontent['count']       = 0;
        $subcontent['id']       = 0;
        $subcontent['parent']       = '';
        $subcontent['parentURL']    = "";
        $subcontent['cardURL']      = $url;
        $subcontent['name']      = '['.$requirement->code.']';
        //标题
        $title = '';
        $actions = [];
        $mailConf   = isset($this->config->global->setRequirementMail) ? $this->config->global->setRequirementMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf   = json_decode($mailConf);
        if($action->action == 'deleted')
        {
            $mailConf->mailTitle = $this->lang->requirement->deleteMaile;
        }

        return ['toList'=>$toList,'subcontent'=>$subcontent,'url'=>$url,'title'=>$title,'actions'=>$actions,'mailconfig'=>json_encode($mailConf)];
    }


    /**
     * @Notes: 获取需求任务 pending back状态数据
     * @Date: 2023/7/18
     * @Time: 15:19
     * @Interface getChangeInfoByRequirementIdInStatus
     * @param $requirementID
     * @return mixed
     */
    public function getChangeInfoByRequirementIdInStatus($requirementID)
    {
        return $this->dao->select('*')->from(TABLE_REQUIREMENTCHANGEOUTSIDE)->where('requirementID')->eq($requirementID)->andWhere('`status`')->in(['pending','back'])->andWhere('`delete`')->eq(1)->orderBy('id desc')->fetch();
    }

    /**
     * @Notes:获取需求任务变更单
     * @Date: 2023/6/26
     * @Time: 17:31
     * @Interface getChangeInfoByRequirementId
     * @param $requirementID
     * @return mixed
     */
    public function getChangeInfoByRequirementId($requirementID)
    {
        $ret = $this->dao->select('*')->from(TABLE_REQUIREMENTCHANGEOUTSIDE)->where('requirementID')->in($requirementID)->andWhere('`delete`')->eq(1)->fetchAll();
        if($ret){
            $ret = $this->loadModel('file')->replaceImgURL($ret, $this->config->requirement->editor->change['id']);
        }
        return $ret;
    }

    /**
     * @Notes:获取需求任务变更单
     * @Date: 2023/6/27
     * @Time: 17:11
     * @Interface getChangeInfoByChangeId
     * @param $changeID
     * @param $isFormatFile
     * @return mixed
     */
    public function getChangeInfoByChangeId($changeID, $isFormatFile =  true)
    {
        $ret = $this->dao->select('*')->from(TABLE_REQUIREMENTCHANGEOUTSIDE)->where('id')->eq($changeID)->andWhere('`delete`')->eq(1)->fetch();
        if($isFormatFile && $ret){
            $ret = $this->loadModel('file')->replaceImgURL($ret, $this->config->requirement->editor->change['id']);
        }
        return $ret;
    }

    /**
     * @Notes:获取评审中的变更单
     * @Date: 2023/6/30
     * @Time: 13:59
     * @Interface getPendingOrderByRequirementId
     * @param $requirementID
     */
    public function getPendingOrderByRequirementId($requirementID)
    {
       $ret = $this->dao->select('*')->from(TABLE_REQUIREMENTCHANGEOUTSIDE)->where('requirementID')->eq($requirementID)->andWhere('`status`')->eq('pending')->andWhere('`delete`')->eq(1)->fetch();
       if($ret){
           $ret = $this->loadModel('file')->replaceImgURL($ret, $this->config->requirement->editor->change['id']);
       }
       return $ret;
    }

    /**
     * @Notes: 变更锁受影响的交付单
     * @Date: 2023/8/25
     * @Time: 14:28
     * @Interface selectAffectIds
     * @param $demandIDs
     */
    public function selectAffectIds($demandIDs)
    {
        /**
         * 获取该需求条目关联的所有二线单
         * @var secondLineModel $secondLineModel
         */
        $secondLineModel = $this->loadModel('secondline');
        $modifyIds = '';
        $gainIds = '';
        $outwardDeliveryIds = '';
        if (!empty($demandIDs)) {
            /*查询 交付管理金信-生产变更、数据获取 清总交付-对外交付 id集合*/
            //金信-生产变更 modify
            $modifyInfo = $secondLineModel->getSecondInfo($demandIDs, 'demand', 'modify');
            //金信-数据获取 gain
            $gainInfo = $secondLineModel->getSecondInfo($demandIDs, 'demand', 'gain');
            //清总-对外交付 outwardDelivery
            $outwardDeliveryInfo = $secondLineModel->getSecondInfo($demandIDs, 'demand', 'outwardDelivery');

            if (!empty($modifyInfo)) $modifyIds = implode(',', array_column($modifyInfo, 'relationID'));
            if (!empty($gainInfo)) $gainIds = implode(',', array_column($gainInfo, 'relationID'));
            if (!empty($outwardDeliveryInfo)) $outwardDeliveryIds = implode(',', array_column($outwardDeliveryInfo, 'relationID'));
        }
        return ['modifyIds' => $modifyIds, 'gainIds' => $gainIds, 'outwardDeliveryIdsIds' => $outwardDeliveryIds];
    }
    /*
     * @Notes:据opinionID分类构造数据
     * @Date: 2023/8/4
     * @Time: 11:21
     * @Interface allRequirementsGroupOpinionID
     * @param string $field
     * @return array
     */
    public function allRequirementsGroupOpinionID($field="*")
    {
        $requirementsInfo = $this->getAllRequirements($field);
        $requirementsArray = array();
        foreach ($requirementsInfo as $key => $requirement){
            if($requirement->opinion != '0'){
                $requirementsArray[$requirement->opinion][] = $requirement;
            }
        }
        return $requirementsArray;
    }

    /**
     * @Notes: 获取所有需求任务数据
     * @Date: 2023/8/4
     * @Time: 11:21
     * @Interface getAllRequirements
     * @param string $field
     * @return mixed
     */
    public function getAllRequirements($field='*')
    {
         return $this->dao->select($field)->from(TABLE_REQUIREMENT)->where('`status`')->ne('deleted')->andWhere('sourceRequirement')->eq(1)->fetchAll();
    }

    /**
     * @Notes: 反馈期限维护
     * @Date: 2023/8/11
     * @Time: 14:02
     * @Interface defend
     * @param $requirementID
     */
    public function defend($requirement)
    {
        $this->app->loadLang('demand');
        $requirementID = $requirement->id;
        $info = fixer::input('post')->get();

        $updateInfo = new stdClass();
        $deptPassTime = $info->deptPassTime;
        $innovationPassTime = $info->innovationPassTime;
        $feekBackStartTime = $info->feekBackStartTime;
        $feekBackStartTimeOutside = $info->feekBackStartTimeOutside;

        if(isset($info->isUpdateOverStatus))
        {
            //2标记为不再更新
            $updateInfo->isUpdateOverStatus = 2;
        }else{
            $updateInfo->isUpdateOverStatus = 1;
        }

        //内部反馈期限
        if(!empty($deptPassTime) && $deptPassTime != '0000-00-00 00:00:00')
        {
            if(strtotime($deptPassTime))
            {
                $updateInfo->deptPassTime = $deptPassTime;
            }else{
                dao::$errors['deptPassTime'] = sprintf($this->lang->requirement->legalObject, $this->lang->requirement->deptPassTime);
            }
        }else{
            $updateInfo->deptPassTime = null;
        }

        //外部反馈期限
        if(!empty($innovationPassTime) && $innovationPassTime != '0000-00-00 00:00:00')
        {
            if(strtotime($innovationPassTime))
            {
                $updateInfo->innovationPassTime = $innovationPassTime;
            }else{
                dao::$errors['innovationPassTime'] = sprintf($this->lang->requirement->legalObject, $this->lang->requirement->innovationPassTime);
            }
        }else{
            $updateInfo->innovationPassTime = null;
        }

        //内部开始时间
        if(!empty($feekBackStartTime) && $feekBackStartTime != '0000-00-00 00:00:00')
        {
            if(strtotime($feekBackStartTime))
            {
                $updateInfo->feekBackStartTime = $feekBackStartTime;

                $days = $this->lang->demand->expireDaysList['insideDays'];
                $feekBackEndTimeInside = helper::getTrueWorkDay($feekBackStartTime,$days,true).substr($feekBackStartTime,10);
                $updateInfo->feekBackEndTimeInside = $feekBackEndTimeInside;
            }else{
                dao::$errors['feekBackStartTime'] = sprintf($this->lang->requirement->legalObject, $this->lang->requirement->feekBackStartTime);
            }
        }else{
            $updateInfo->feekBackStartTime = null;
            $updateInfo->feekBackEndTimeInside = null;
        }
        //外部开始时间
        if(!empty($feekBackStartTimeOutside) && $feekBackStartTimeOutside != '0000-00-00 00:00:00')
        {
            if(strtotime($feekBackStartTimeOutside))
            {
                $updateInfo->feekBackStartTimeOutside = $feekBackStartTimeOutside;

                $days = $this->lang->demand->expireDaysList['outsideDays'];
                $feekBackEndTimeOutSide = helper::getTrueWorkDay($feekBackStartTimeOutside,$days,true).substr($feekBackStartTimeOutside,10);
                $updateInfo->feekBackEndTimeOutSide = $feekBackEndTimeOutSide;
            }else{
                dao::$errors['feekBackStartTimeOutside'] = sprintf($this->lang->requirement->legalObject, $this->lang->requirement->feekBackStartTimeOutside);
            }
        }else{
            $updateInfo->feekBackStartTimeOutside = null;
            $updateInfo->feekBackEndTimeOutSide = null;
        }
        //用于历史记录比较
        $oldRequirement = new stdClass();
        $oldRequirement->deptPassTime = $requirement->deptPassTime;
        $oldRequirement->innovationPassTime = $requirement->innovationPassTime;
        $oldRequirement->feekBackStartTime = $requirement->feekBackStartTime;
        $oldRequirement->feekBackEndTimeInside = $requirement->feekBackEndTimeInside;
        $oldRequirement->feekBackStartTimeOutside = $requirement->feekBackStartTimeOutside;
        $oldRequirement->feekBackEndTimeOutSide = $requirement->feekBackEndTimeOutSide;
        $oldRequirement->isUpdateOverStatus = $requirement->isUpdateOverStatus;
        $this->dao->update(TABLE_REQUIREMENT)->data($updateInfo)->where('id')->eq($requirementID)->exec();

        return common::createChanges($oldRequirement, $updateInfo);
    }

    /**
     * @Notes: 接口更新过后，是否有反馈
     * @Date: 2023/8/18
     * @Time: 9:54
     * @Interface checkFeedBack
     * @param $objectType
     * @param $objectID
     * @param $action
     * @param $order
     * @param $comment
     * @param string $date
     * @return mixed
     */
    public function checkFeedBack($objectType,$objectID,$action,$order,$comment,$date='')
    {
        $action = $this->dao->select('*')->from(TABLE_ACTION)
            ->where('objectType')->eq($objectType)
            ->andWhere('objectID')->eq($objectID)
            ->andWhere('action')->eq($action)
            ->beginIF(!empty($comment))->andWhere('comment')->eq($comment)->fi()
            ->beginIF(!empty($date))->andWhere('`date`')->gt($date)->fi()
            ->orderBy($order)
            ->fetch();

        return $action;
    }


    /**
     * @Notes:月报统计需求任务基础数据
     * @Date: 2023/10/13
     * @Time: 17:35
     * @Interface monthReportBaseDataAboutRequirement
     * @return mixed
     * @param $date
     */
    public function monthReportBaseDataAboutRequirement($endtime,$starttime,$dtype)
    {
        /* @var secondmonthreportModel $reportModel*/
        $reportModel = $this->loadModel('secondmonthreport');
        $startAndEndDate = $reportModel->getTimeFrame($endtime,$starttime,$dtype);
        $start = $startAndEndDate['startdate'] ?? '';
        $end = $startAndEndDate['enddate'] ?? '';
        $field = "t1.id,t1.`code`,t1.`status`,t1.createdDate,t1.app,t1.dealUser,t1.feedbackStatus,t1.feedbackBy,t1.feedbackDate,t1.ifOverDate,t1.ifOverTimeOutSide,t1.feekBackStartTime,t1.feekBackStartTimeOutside,t1.innovationPassTime,t1.deptPassTime,t1.feedbackOver,t1.feedbackDealUser,t2.dept";
        $info = $this->dao->select($field)->from(TABLE_REQUIREMENT)->alias('t1')
            ->leftJoin(TABLE_USER)->alias('t2')->on('t1.feedbackBy = t2.account')
            ->where('t1.`status`')->ne('deleted')
            ->andWhere('t1.`status`')->ne('deleteout')
            ->andWhere('t1.createdBy')->eq('guestcn')
            ->andWhere('t1.sourceRequirement')->eq('1')
            ->andWhere('t1.createdDate')->between($start, $end)
            ->fetchAll();
        $userDepts = $this->dao->select('account,dept')->from(TABLE_USER)->where('deleted')->eq(0)->fetchPairs();

        //待反馈状态取 需求任务待处理人第一个的所属部门
        foreach ($info as $key => $value){
            if($value->feedbackStatus == 'tofeedback')
            {
                if($value->feedbackDealUser){
                    /*$userDept = $this->loadModel('user')->getByAccount();
                    $userDept->dept;*/
                    $info[$key]->dept = $userDepts[$value->feedbackDealUser];
                }else{

                    $info[$key]->dept = '';
                }
            }
        }
        return $info;
    }

    /**
     * @Notes:需求任务交付时间更新
     * 需求任务状态为【已交付、上线成功】时详情页面、导出添加【需求任务交付时间】取值逻辑为该任务下所有已交付的需求条目的交付时间取最大值。
     * 若需求任务状态不为【已交付、上线成功】时则需求任务的交付时间置空
     * @Date: 2023/11/7
     * @Time: 15:44
     * @Interface dealSolveTime
     * @param int $requirementID
     * @param array|object $requirement
     */
    public function dealSolveTime($requirementID = 0)
    {
        /* @var demandModel $demandModel*/
        $demandModel = $this->loadModel('demand');
        $requirement = $this->getByIdSimple($requirementID);
        $baseRequirementArr = ['delivered','onlined'];
        /*
         * 需求条目联动任务已交付、上线成功情况
         * ①所有条目状态只包含上线成功或者已交付
         * ②包含一个上线成功或已交付 其他是已挂起或者已关闭
         */
        $baseDemandArr      = ['delivery','onlinesuccess','suspend','closed'];
        $isNeedUpdate = true;
        if(in_array($requirement->status,$baseRequirementArr))
        {
            $demandInfo = $demandModel->getByRequirementID('id,`status`,solvedTime',$requirementID);
            $demandStatus = $demandInfo ? array_unique(array_column($demandInfo,'status')) : [];
            $demandSolvedTime = array_column($demandInfo,'solvedTime');
            //判断需求条目合集
            if(!empty($demandStatus))
            {
                //需求条目全部在baseDemandArr中的状态
                foreach ($demandStatus as $status)
                {
                    if(!in_array($status,$baseDemandArr))
                    {
                        $isNeedUpdate = false;
                    }
                }

                //中间是否有已挂起或已关闭
                if(in_array('suspend',$demandStatus) || in_array('closed',$demandStatus))
                {
                    if(!in_array('delivery',$demandStatus) && !in_array('onlinesuccess',$demandStatus))
                    {
                        $isNeedUpdate = false;
                    }
                }

            }
            if($isNeedUpdate === true)
            {
                $this->dao->update(TABLE_REQUIREMENT)->set('solvedTime')->eq(max($demandSolvedTime))->where('id')->eq($requirementID)->exec();
            }

        }else{
            if(!empty($requirement->solvedTime) && $requirement->solvedTime != '0000-00-00 00:00:00')
            {
                $this->dao->update(TABLE_REQUIREMENT)->set('solvedTime')->eq(null)->where('id')->eq($requirementID)->exec();
            }
        }

    }


    /**
     * @Notes:更新最新发布时间字段
     * @Date: 2023/11/7
     * @Time: 11:09
     * @Interface updateNewPublishedTime
     * @param int $requirementID
     */
    public function updateNewPublishedTime($requirementID = 0)
    {
        return $this->dao->update(TABLE_REQUIREMENT)->set('newPublishedTime')->eq(helper::now())->where('id')->eq($requirementID)->exec();
    }

    /**
     * @Notes:需求意向与任务的状态联动不关联变更中的任务
     * @Date: 2023/12/12
     * @Time: 16:07
     * @Interface getNoChangeRequirementInfoByOpinionID
     * @param $opinionID
     * @param string $field
     * @return mixed
     */
    public function getNoChangeRequirementInfoByOpinionID($opinionID,$field = '*')
    {
        return $this->dao->select($field)->from(TABLE_REQUIREMENT)->where('opinion')->eq($opinionID)->andWhere('status')->andWhere('changeLock')->eq(1)->fetchAll();
    }

    /**
     * @Notes:校验添加权限
     * @Date: 2024/1/17
     * @Time: 10:35
     * @Interface checkAuthCreate
     */
    public function checkAuthCreate()
    {
        $this->app->loadLang('demand');
        //迭代三十三 创建按钮权限  后台自定义产品经理
        $productManagerList = array_filter(array_keys($this->lang->demand->productManagerList));
        $productManagerList = array_merge(['admin'],$productManagerList);
        $createButton = false;
        if(in_array($this->app->user->account,$productManagerList))
        {
            $createButton = true;
        }
        return $createButton;
    }


    /**
     * @Notes:获取需求任务数据
     * @Date: 2024/1/26
     * @Time: 16:35
     * @Interface getRequirementInfos
     * @param $strIds
     */
    public function getRequirementInfos($strIds)
    {
        $this->app->loadLang('demand');
        $deleteOutDataStr = '';
        $requirementIds   = [];
        $requirementInfoByDemandIds = $this->dao->select('id,requirementID,code,`status`,title')->from(TABLE_DEMAND)->where('id')->in($strIds)->fetchAll();
        if($requirementInfoByDemandIds)
        {
            $requirementIdsByDemandIds = array_column($requirementInfoByDemandIds,'requirementID');
            $requirementInfoByRids =  $this->dao->select('id,code,status')->from(TABLE_REQUIREMENT)->where('id')->in($requirementIdsByDemandIds)->fetchAll();
            $deleteOutData = [];
            //获取符合条件的任务id集合
            foreach ($requirementInfoByRids as $requirementInfo)
            {
                if($requirementInfo->status == 'deleteout')
                {
                    $requirementIds[] = $requirementInfo->id;
                }
            }
            //构造提示语 单号（标题）
            if(!empty($requirementIds))
            {
                foreach ($requirementInfoByDemandIds as $demand)
                {
                    if(in_array($demand->requirementID,$requirementIds))
                    {
                        $deleteOutData[] = $demand->code.'('.$demand->title.')';
                    }
                }
            }
            //转换为字符串
            if(!empty($deleteOutData))
            {
                $deleteOutDataStr = implode(',',$deleteOutData);
            }
        }
        return $deleteOutDataStr;

    }


    /**
     * 是否允许修改反馈单结束时间
     *
     * @param $requirementInfo
     * @param $account
     * @return array
     */
    public function checkIsAllowEditFeedbackEnd($requirementInfo, $account){
        $res = array(
            'result'  => false,
            'message' => '',
        );
        if(!($requirementInfo && $account)){
            $res['message'] = $this->lang->common->errorParamId;
            return $res;
        }

        if($requirementInfo->feedbackStatus != 'feedbacksuccess'){
            $res['message'] = $this->lang->requirement->editFeedbackEndTips['statusError'];
            return $res;
        }
        $users = ['admin', $requirementInfo->feedbackBy];
        if(!in_array($account, $users)){
            $res['message'] = $this->lang->requirement->editFeedbackEndTips['userError'];
            return $res;
        }
        $res['result'] = true;
        return $res;
    }
    /**
     * @Notes: 处理时间0000的情况
     * @Date: 2023/6/8
     * @Time: 16:45
     * @Interface dealEmptyTime
     * @param $requirement
     * @return mixed
     */
    public function dealEmptyTime($requirement)
    {
        //上线时间
        if($requirement->onlineTimeByDemand == '0000-00-00 00:00:00' || $requirement->onlineTimeByDemand == '0000-00-00' || empty($requirement->onlineTimeByDemand))
        {
            $requirement->onlineTimeByDemand = '';
        }
        //计划完成时间
        if($requirement->end == '0000-00-00 00:00:00' || $requirement->end == '0000-00-00' || empty($requirement->end))
        {
            $requirement->end = '';
        }else{
            $requirement->end = date('Y-m-d',strtotime($requirement->end));
        }

        //期望完成时间
//        if($requirement->deadLine == '0000-00-00 00:00:00' || $requirement->deadLine == '0000-00-00' || empty($requirement->deadLine))
//        {
//            $requirement->deadLine = '';
//            if($requirement->createdBy == 'guestcn'){
//                $opinionInfo = $this->loadModel('opinion')->getByID($requirement->opinion);
//                $requirement->deadLine = date('Y-m-d',strtotime($opinionInfo->deadline));
//            }
//        }else{
//            $requirement->deadLine = date('Y-m-d',strtotime($requirement->deadLine));
//        }
        //任务首次接收时间
        if($requirement->acceptTime == '0000-00-00 00:00:00' || $requirement->acceptTime == '0000-00-00' || empty($requirement->acceptTime))
        {
            $requirement->acceptTime = '';
        }
        //任务最新变更时间
        if($requirement->lastChangeTime == '0000-00-00 00:00:00' || $requirement->lastChangeTime == '0000-00-00' || empty($requirement->lastChangeTime))
        {
            $requirement->lastChangeTime = '';
        }
        //反馈同步成功日期
        if($requirement->feedbackDate == '0000-00-00 00:00:00' || $requirement->feedbackDate == '0000-00-00' || empty($requirement->feedbackDate))
        {
            $requirement->feedbackDate = '';
        }
        //反馈开始时间
        if($requirement->feekBackStartTime == '0000-00-00 00:00:00' || $requirement->feekBackStartTime == '0000-00-00' || empty($requirement->feekBackStartTime))
        {
            $requirement->feekBackStartTime = '';
        }
        //部门通过时间
        if($requirement->deptPassTime == '0000-00-00 00:00:00' || $requirement->deptPassTime == '0000-00-00' || empty($requirement->deptPassTime))
        {
            $requirement->deptPassTime = '';
        }
        //产创通过时间
        if($requirement->innovationPassTime == '0000-00-00 00:00:00' || $requirement->innovationPassTime == '0000-00-00' || empty($requirement->innovationPassTime))
        {
            $requirement->innovationPassTime = '';
        }

        if($requirement->feekBackStartTimeOutside == '0000-00-00 00:00:00' || $requirement->feekBackStartTimeOutside == '0000-00-00' || empty($requirement->feekBackStartTimeOutside))
        {
            $requirement->feekBackStartTimeOutside = '';
        }

        if(empty($requirement->feekBackStartTime) && empty($requirement->deptPassTime))
        {
            $requirement->feekBackBetweenTimeInside = '';
        }else{
            $requirement->feekBackBetweenTimeInside = '('.$requirement->feekBackStartTime.'~'.$requirement->deptPassTime.')';
        }
        //外部反馈区间
        $requirement->feekBackBetweenOutSide = '('.$requirement->feekBackStartTimeOutside.'~'.$requirement->innovationPassTime.')';
        if(empty($requirement->feekBackStartTimeOutside) && empty($requirement->innovationPassTime))
        {
            $requirement->feekBackBetweenOutSide = '';
        }

        if($requirement->ifOverTimeOutSide == 100)
        {
            $requirement->feekBackBetweenOutSide = '否'.'('.$requirement->feekBackStartTimeOutside.'~'.$requirement->innovationPassTime.')';
            if(empty($requirement->feekBackStartTimeOutside) && empty($requirement->innovationPassTime))
            {
                $requirement->feekBackBetweenOutSide = '否';
            }
        }

        if($requirement->ifOverDate == 100)
        {
            $requirement->feekBackBetweenTimeInside = '否'.'('.$requirement->feekBackStartTime.'~'.$requirement->deptPassTime.')';
            if(($requirement->feekBackStartTime == '0000-00-00 00:00:00' || empty($requirement->feekBackStartTime)) && ($requirement->deptPassTime == '0000-00-00 00:00:00' || empty($requirement->deptPassTime))){
                $requirement->feekBackBetweenTimeInside = '否';
            }
        }
        //内部反馈截止时间
        if($requirement->feekBackEndTimeInside == '0000-00-00 00:00:00' || $requirement->feekBackEndTimeInside == '0000-00-00' || empty($requirement->feekBackEndTimeInside)){
            $requirement->feekBackEndTimeInside = '';
        }
        //外部反馈截止时间
        if($requirement->feekBackEndTimeOutSide == '0000-00-00 00:00:00' || $requirement->feekBackEndTimeOutSide == '0000-00-00' || empty($requirement->feekBackEndTimeOutSide)){
            $requirement->feekBackEndTimeOutSide = '';
        }
        //交付时间
        if($requirement->solvedTime == '0000-00-00 00:00:00' || $requirement->solvedTime == '0000-00-00' || empty($requirement->solvedTime)){
            $requirement->solvedTime = '';
        }


        return $requirement;
    }

    /**
     * 超时考核信息是否可见
     *
     * @param $account
     * @return bool
     */
    public function getIsOverDateInfoVisible($account){
        $isOverDateInfoVisible = false;
        $overDateInfoVisibleUsers = explode(',', $this->config->requirement->overDateInfoVisible);
        $overDateInfoVisibleUsers[] = 'admin';
        if(in_array($account, $overDateInfoVisibleUsers)){
            $isOverDateInfoVisible = true;
        }
        return $isOverDateInfoVisible;
    }
}
