<?php
class requirementinsideModel extends model
{
    /**
     *
     * @return int|null 成功返回ID, 失败返回null
     */
    public function create()
    {

        $this->loadModel('action');
        /* 由control.php中的create方法调用，获取表单提交的数据插入到数据库中。*/
        $requirements = fixer::input('post')
            ->stripTags($this->config->requirementinside->editor->create['id'], $this->config->allowedTags)
            ->join('app',',')
            ->join('dealUser',',')
            ->get();

        if(empty($_POST['opinionID']))
        {
            dao::$errors['opinionID'] = sprintf($this->lang->requirementinside->emptyObject, $this->lang->requirementinside->opinionID);
        }

        if(empty($requirements->dealUser))
        {
            dao::$errors['dealUser'] = sprintf($this->lang->requirementinside->emptyObject, $this->lang->requirementinside->dealUser);
        }

        //期望完成时间
        if(!$this->loadModel('common')->checkJkDateTime($requirements->deadLine))
        {
            dao::$errors['deadLine'] =  sprintf($this->lang->requirementinside->emptyObject, $this->lang->requirementinside->deadLine);
            return;
        }

        //计划完成时间
        if(!$this->loadModel('common')->checkJkDateTime($requirements->planEnd))
        {
            dao::$errors['planEnd'] =  sprintf($this->lang->requirementinside->emptyObject, $this->lang->requirementinside->planEnd);
            return;
        }
       /*
        //计划完成时间不允许大于期望完成时间
        if(strtotime($requirements->planEnd) > strtotime($requirements->deadLine))
        {
            $errors[''] = $this->lang->requirementinside->editEndTip;
            return dao::$errors = $errors;
        }
       */

        $this->tryError();

        $opinionID = $requirements->opinionID;
        $opinionObj = $this->loadModel('opinioninside')->getByID($opinionID);
        if(empty($opinionObj->category)){
            dao::$errors['categoryEmpty'] = "所属意向未补充完整请联系产品经理进行补充";
        }
          //迭代二十五要求暂时注释 预留后续使用
//        $expDate = date('Y-m-d h:i:s', strtotime('-3 months'));
//        if($opinionObj->onlineTimeByDemand != "" && $opinionObj->onlineTimeByDemand < $expDate ){
//            dao::$errors['expired'] = "上线成功3个月之后不允许进行倒挂，请按照新的需求处理";
//        }
        $this->tryError();

        $date   = helper::today();
        $codeBefore = substr( $date, 0, 4) . sprintf('%03d', $opinionID);
        $number = $this->dao->select('count(id) c')
            ->from(TABLE_REQUIREMENT)
            ->where('code')
            ->like('CFIT-W-'.$codeBefore.'%')
            ->andWhere('sourceRequirement')
            ->eq(2)
            ->fetch('c');
        $code   = 'CFIT-W-'.$codeBefore . '-' . sprintf('%02d', $number+1);
        $data = new stdClass();
        $data->name        = $requirements->name ?? '';
        $data->desc        = $requirements->desc ?? '';
        $data->status      = 'published';
        $data->opinion     = $opinionID;
        $data->code        = $code;
        $data->deadLine       = $requirements->deadLine ?? '';
        $data->planEnd        = $requirements->planEnd ?? '';
    //            $data->method      = $requirements->method[$i];
        $data->createdBy   = $this->app->user->account;
        $data->dealUser   = $requirements->dealUser;
        $data->comment   = $requirements->comment;
        $data->createdDate = helper::now();
        $data->productManager   = $this->app->user->account;
        $data->projectManager   = $requirements->dealUser;

        // 增加所属应用系统字段
        $data->app = $requirements->app ?? '';
        $data = $this->loadModel('file')->processImgURL($data, $this->config->requirementinside->editor->create['id'], $this->post->uid);
        //同步需求意向字段
        $data->sourceMode = $opinionObj->sourceMode;
        $data->sourceName = $opinionObj->sourceName;
        $data->acceptTime = $opinionObj->sourceMode=='8'?helper::now():$opinionObj->receiveDate;
        $data->union = $opinionObj->union;
        $data->deadlineByOpinion = $opinionObj->deadline;
        $data->dateByOpinion = $opinionObj->date;
        $data->nameByOpinion = $opinionObj->name;
        $data->sourceRequirement = 2;
        //需求意向待处理人为空的时候，倒挂成功后将倒挂人显示为需求意向的待处理人
        if(in_array($opinionObj->status,['delivery','online']))
        {
            $this->dao->update(TABLE_OPINION)->set('dealUser')->eq($this->app->user->account)->where('id')->eq($opinionID)->exec();
        }

        $this->dao->insert(TABLE_REQUIREMENT)->data($data)
            ->batchCheck($this->config->requirementinside->create->requiredFields, 'notempty')
            ->autoCheck()->exec();
        $requirementID = $this->dao->lastInsertID();

        //只有非已拆分时才更新状态为已拆分，增加状态流转
        if($opinionObj->status != 'subdivided')
        {
            $this->loadModel('opinioninside')->updateStatusById('subdivided',$opinionID);
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
        /* 由control.php中的create方法调用，获取表单提交的数据插入到数据库中。*/
        $requirementData = fixer::input('post')
            ->stripTags($this->config->requirementinside->editor->create['id'], $this->config->allowedTags)
            ->get();
        $this->app->loadLang('opinion');
        $requirementData->dealUser = !empty($this->lang->opinion->apiDealUserList['userAccount'])?$this->lang->opinion->apiDealUserList['userAccount']:'litianzi';
        $requirementData->isImprovementServices = array_search($requirementData->isImprovementServices,$this->lang->requirementinside->isImprovementServices);
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
        /* 获取旧的需求条目数据，获取post请求参数进行处理，更新需求条目信息，更新成功后记录需求条目版本，返回改动的字段信息。*/
        $oldRequirement = $this->getByID($requirementID);
        $requirement = fixer::input('post')
            ->join('app',',')
            ->join('dealUser',',')
            ->stripTags($this->config->requirementinside->editor->edit['id'], $this->config->allowedTags)
            ->remove('uid,files,labels')
            ->get();
        if($oldRequirement->opinion != $requirement->opinion){
            $opinionObj = $this->loadModel('opinioninside')->getByID($requirement->opinion);
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
            dao::$errors['dealUser'] = sprintf($this->lang->requirementinside->emptyObject, $this->lang->requirementinside->dealUser);
        }

        if(empty($requirement->opinion))
        {
            dao::$errors['opinion'] = sprintf($this->lang->requirementinside->emptyObject, $this->lang->requirementinside->opinionID);
        }
        //期望完成时间
        if(!$this->loadModel('common')->checkJkDateTime($requirement->deadLine))
        {
            dao::$errors['deadLine'] =  sprintf($this->lang->requirementinside->emptyObject, $this->lang->requirementinside->deadLine);
            return;
        }

        //计划完成时间检查(内部自建非清总且拆分之前可以修改计划完成时间)
        if(($oldRequirement->createdBy != 'guestcn') && (in_array($oldRequirement->status,['published']))){
            if(!$this->loadModel('common')->checkJkDateTime($requirement->planEnd))
            {
                dao::$errors['planEnd'] =  sprintf($this->lang->requirementinside->emptyObject, $this->lang->requirementinside->planEnd);
                return;
            }
        }


        $this->tryError();

        $opinionID = $requirement->opinion;
        $opinionObj = $this->loadModel('opinioninside')->getByID($opinionID);
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
        }
        $data->opinion     = $opinionID;
        $data->deadLine       = $requirement->deadLine ?? '';
        $data->planEnd       = $requirement->planEnd ?? '';
        $data->editedBy    = $this->app->user->account;
        $data->editedDate    = helper::now();
        $data->productManager   = $this->app->user->account;
        $data->projectManager   = $requirement->dealUser;
        $data->dealUser   = $requirement->dealUser;
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

        //需求意向待处理人为空的时候，倒挂成功后将倒挂人显示为需求意向的待处理人
        if(in_array($opinionObj->status,['delivery','online']))
        {
            $this->dao->update(TABLE_OPINION)->set('dealUser')->eq($this->app->user->account)->where('id')->eq($opinionID)->exec();
        }

        $this->dao->update(TABLE_REQUIREMENT)
            ->data($data)
            ->where('id')->eq($requirementID)
            ->batchCheck($this->config->requirementinside->edit->requiredFields, 'notempty')
            ->autoCheck()->exec();
        //更新工时
        $this->dealConsumed($requirementID,0,$this->app->user->account,$oldRequirement->status);
        unset($requirement->consumed);
        $requirement = $this->loadModel('file')->processImgURL($requirement, $this->config->requirementinside->editor->edit['id'], $this->post->uid);
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
        if(empty($data->end) || $data->end == '0000-00-00')
        {
            dao::$errors['end'] =  sprintf($this->lang->requirementinside->error->empty, $this->lang->requirementinside->end);
            return;
        }

        //计划完成时间不允许大于期望完成时间
        $deadLine = $requirement->deadLine;
        if(strtotime($data->end) > strtotime($deadLine))
        {
            dao::$errors[] = $this->lang->requirementinside->editEndTip;
            return;
        }

        $diffData = new stdClass();
        $diffData->end = $requirement->end;
        $diffData->comment = '';

        $this->dao->update(TABLE_REQUIREMENT)->set('`end`')->eq($data->end)->where('id')->eq($requirement->id)->exec();
        return common::createChanges($diffData, $data);
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
        /* 获取旧的需求条目数据，获取post请求参数进行处理，更新需求条目信息，更新成功后记录需求条目版本，返回改动的字段信息。*/
        $oldRequirement = $this->getByID($requirementID);
        $requirement = fixer::input('post')
            ->stripTags($this->config->requirementinside->editor->edit['id'], $this->config->allowedTags)
            ->add('feedbackStatus', 'tofeedback')
            ->add('feedbackDealUser', $oldRequirement->feedbackBy)
            ->remove('uid,files,labels')
            ->get();
        $requirement->version = $oldRequirement->version;
        $requirement->status = $oldRequirement->status;
        if ($oldRequirement->status != 'topublish'){
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
        $requirement = $this->loadModel('file')->processImgURL($requirement, $this->config->requirementinside->editor->edit['id'], $this->post->uid);
        $this->dao->update(TABLE_REQUIREMENT)->data($requirement)->where('id')->eq($requirementID)->exec();

        if(!dao::isError())
        {
            $this->dao->update(TABLE_REQUIREMENTSPEC)->set('`desc`')->eq($requirement->desc)
                ->where('requirement')->eq($requirementID)
                ->andWhere('version')->eq($requirement->version)
                ->exec();

            $this->loadModel('file')->updateObjectID($this->post->uid, $requirementID, 'requirement');

            $this->loadModel('consumed')->record('requirement', $requirementID, 0, 'guestcn',
                $oldRequirement->status, $requirement->status, array(), "updateApi",$requirement->version);

            if ($changeOrderNumber != ''){
                $change = $this->dao->select("*")->from(TABLE_REQUIREMENTCHANGE)->where('changeNumber')->eq($changeOrderNumber)->fetch();
                $changeCode = explode(',',$change->changeEntry);
                $changeCode[] = $oldRequirement->entriesCode;
                $codeStr = implode(',',array_unique($changeCode));
                $changeInfo = new stdClass();
                $changeInfo->editDate = date('Y-m-d H:i:s',time());
                $changeInfo->changeEntry = trim($codeStr,',');
                $this->dao->update(TABLE_REQUIREMENTCHANGE)->data($changeInfo)->where('changeNumber')->eq($changeOrderNumber)->exec();
            }
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
            ->stripTags($this->config->requirementinside->editor->confirm['id'], $this->config->allowedTags)
            ->remove('uid,files,labels,consumed')
            ->get();
        $requirement = $this->loadModel('file')->processImgURL($requirement, $this->config->requirementinside->editor->confirm['id'], $this->post->uid);
        $this->dao->update(TABLE_REQUIREMENT)
            ->data($requirement)
            ->where('id')->eq($requirementID)
            ->autoCheck()->batchCheck($this->config->requirementinside->confirm->requiredFields, 'notempty')
            ->exec();

        $this->loadModel('file')->updateObjectID($this->post->uid, $requirementID, 'requirement');
        $this->file->saveUpload('requirement', $requirementID);
        return common::createChanges($oldRequirement, $requirement);
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
        $nowTime = date('Y-m-d');
        $deptInfo = $this->loadModel('dept')->getByID($this->app->user->dept);
        if($deptInfo != null){
            $feedbackToHandle = $deptInfo->manager;
        }else{
            //若没有部门，就设置本身作为审批人
            $feedbackToHandle = $this->app->user->account;
        }
        $requirement = fixer::input('post')
            ->add('feedbackStatus', 'todepartapproved')
            ->add('feedbackBy', $this->app->user->account)
            ->add('feedbackDealUser', $feedbackToHandle)
            ->add('reviewComments', '')
            ->join('product', ',')
            ->join('line', ',')
            ->join('app', ',')
            //->stripTags($this->config->requirementinside->noeditor->feedback['id'], $this->config->allowedTags)
            ->remove('uid,labels')
            ->get();
        $owneruser = $this->loadModel('user')->getById($requirement->owner, 'account');
        $requirement->dept = $owneruser->dept;
        $requirement->reviewStage = '1';
        $requirement->version     =  $oldRequirement->version+1;
        $this->dao->update(TABLE_REQUIREMENT)
            ->data($requirement)
            ->where('id')->eq($requirementID)
            ->autoCheck()
            ->batchCheck($this->config->requirementinside->feedback->requiredFields, 'notempty')
            ->exec();
        if(!dao::isError())
        {
            $this->loadModel('file')->updateObjectID($this->post->uid, $requirementID, 'requirement');
            $this->file->saveUpload('requirement', $requirementID);
            $this->loadModel('consumed')->record('requirement', $requirementID, 0, $this->app->user->account,
                $oldRequirement->feedbackStatus, $requirement->feedbackStatus, array(), "requirementFeedback");

            $apiUser  =  $this->dao->select('value')->from(TABLE_LANG)->where('module')->eq('problem')->andWhere('section')->eq('apiDealUserList')->fetch()->value;
            $this->loadModel('review');
            $this->review->addNode('requirement', $requirementID, $requirement->version, explode(',',$feedbackToHandle), true, 'pending', 1);
            $this->review->addNode('requirement', $requirementID, $requirement->version, explode(',',$apiUser), true, 'wait', 2);
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
            ->stripTags($this->config->requirementinside->editor->feedback['id'], $this->config->allowedTags)
            ->remove('uid,labels')
            ->get();

        $this->dao->update(TABLE_REQUIREMENT)
             ->data($requirement)
             ->where('id')->eq($requirementID)
             ->autoCheck()
             ->batchCheck($this->config->requirementinside->feedback->requiredFields, 'notempty')
             ->exec();

        $pushEnable = $this->config->global->pushEnable;
        if($status == 'reviewing' and $pushEnable == 'enable')
        {
            $url           = $this->config->global->pushUrl;
            $pushAppId     = $this->config->global->pushAppId;
            $pushAppSecret = $this->config->global->pushAppSecret;
            $pushUsername  = $this->config->global->pushUsername;
            $requirement = $this->loadModel('file')->processImgURL($requirement, $this->config->requirementinside->editor->change['id'], $this->post->uid);

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

            $method = zget($this->lang->requirementinside->methodList, $requirement->method);
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
    public function change($requirementID)
    {
        /* 获取旧的需求条目信息，获取post接收的参数信息和需求条目版本信息，将需求条目信息记录到需求版本记录。*/
        $oldRequirement = $this->getByID($requirementID);
        $requirement = fixer::input('post')
            ->join('product', ',')
            ->join('line', ',')
            ->join('app', ',')
            ->stripTags($this->config->requirementinside->editor->change['id'], $this->config->allowedTags)
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
        if(!empty($requirement->entriesCode)) $this->config->requirementinside->change->requiredFields = 'dept,end,owner,contact,method,analysis,handling';

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

            $requirement = $this->loadModel('file')->processImgURL($requirement, $this->config->requirementinside->editor->change['id'], $this->post->uid);

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

            $method = zget($this->lang->requirementinside->methodList, $requirement->method);
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
        $requirementInsideQuery = '';
        if($browseType == 'bysearch')
        {
            $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : '';
            if($query)
            {
                $this->session->set('requirementinsideQuery', $query->sql);
                $this->session->set('requirementinsideForm', $query->form);
            }

            if($this->session->requirementinsideQuery == false) $this->session->set('requirementinsideQuery', ' 1 = 1');
            $requirementInsideQuery = $this->session->requirementinsideQuery;
            $requirementInsideQuery = str_replace('AND `', ' AND `t1.', $requirementInsideQuery);
            $requirementInsideQuery = str_replace('AND (`', ' AND (`t1.', $requirementInsideQuery);

            $requirementInsideQuery = str_replace('OR `', ' OR `t1.', $requirementInsideQuery);
            $requirementInsideQuery = str_replace('OR (`', ' OR (`t1.', $requirementInsideQuery);

            $requirementInsideQuery = str_replace('`', '', $requirementInsideQuery);

            if(strpos($requirementInsideQuery, 'sourceMode') !== false)
            {
                $requirementInsideQuery = str_replace('t1.sourceMode', "t2.sourceMode", $requirementInsideQuery);
            }

            if(strpos($requirementInsideQuery, 'sourceName') !== false)
            {
                $requirementInsideQuery = str_replace('t1.sourceName', "t2.sourceName", $requirementInsideQuery);
            }
            if(strpos($requirementInsideQuery, 'owner') !== false)
            {
                $requirementInsideQuery = str_replace('t1.owner', "t3.acceptUser", $requirementInsideQuery);
            }

            if(strpos($requirementInsideQuery, 'project ') !== false){
                $searchStr = "t1.project";
                $res = preg_replace_callback('/t1.project.*?(\d+)\'/',function ($matches) use($searchStr){
                    $projectId = intval($matches[1]);
                    $requirementIds = $this->loadModel('requirement')->getRequirementIdsByProject($projectId);
                    $demandRequirementIds = $this->loadModel('demand')->getRequirementIdsByProject($projectId);
                    if($demandRequirementIds){
                        $requirementIds = array_merge($requirementIds, $demandRequirementIds);
                        $tempRequirementIds = $this->loadModel('requirement')->getRequirementIdsByNotEqProject($demandRequirementIds, $projectId);
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
                }, $requirementInsideQuery);
                $requirementInsideQuery = $res;

                $res = preg_replace_callback('/t1.project.*?(\d+)\%\'/',function ($matches) use($searchStr){
                    $projectId = intval($matches[1]);
                    $requirementIds = $this->loadModel('demand')->getRequirementIdsByProject($projectId);
                    if($requirementIds){
                        $tempQuery = "(find_in_set($projectId, $searchStr) OR t1.id in (".implode(',', $requirementIds)."))";
                    }else{
                        $tempQuery = "find_in_set($projectId, $searchStr)";
                    }
                    return $tempQuery;
                }, $requirementInsideQuery);
                $requirementInsideQuery = $res;
            }
        }

        $assigntomeQuery = '( 1    AND  FIND_IN_SET("'.$this->app->user->account.'",t1.dealUser))';
        /* 查询需求数据，调用process方法获取评审人。*/
        $requirements = $this->dao->select('t1.*')->from(TABLE_REQUIREMENT)->alias('t1')
            ->innerJoin(TABLE_OPINION)->alias('t2')
            ->on('t1.opinion=t2.id')
            ->leftJoin(TABLE_DEMAND)->alias('t3')->on("t3.requirementID=t1.id and t3.status not in ('deleted', 'closed')")
            ->where(1)
            ->andWhere('t1.status')->ne('deleted')
            ->andWhere('t1.sourceRequirement')->eq(2)
            ->beginIF($browseType == 'ignore')->andWhere('t1.ignoreStatus')->eq('1')->andWhere('t1.ignoredBy')->like("%{$this->app->user->account}%")->fi()
            ->beginIF($browseType == 'reviewing')->andWhere('t1.status')->eq('reviewing')->fi()
            ->beginIF($browseType == 'assigntome')->andWhere($assigntomeQuery)->andWhere('t1.status')->in('published,splited,closed,ignore')->fi()
            ->beginIF($browseType != 'all' and $browseType != 'ignore' and $browseType != 'bysearch' and $browseType != 'reviewing' and ($browseType == 'topublish' or $browseType == 'published' or $browseType == 'splited' or $browseType == 'delivered' or $browseType == 'onlined' or $browseType == 'closed'))->andWhere('t1.status')->eq($browseType)->fi()
            ->beginIF($browseType != 'all' and ($browseType == 'tofeedback' or $browseType == 'todepartapproved' or $browseType == 'toinnovateapproved' or $browseType == 'toexternalapproved' or $browseType == 'syncfail' or $browseType == 'syncsuccess' or $browseType == 'feedbacksuccess' or $browseType == 'feedbackfail' or $browseType == 'returned'))->andWhere('t1.feedbackStatus')->eq($browseType)->fi()
            ->beginIF($browseType == 'bysearch')->andWhere($requirementInsideQuery)->fi()
            ->groupBy('t1.id')
            ->beginIF($browseType == 'assigntome')->orderBy("ignoreStatus_asc, t1.".$orderBy)->fi()
            ->beginIF($browseType != 'assigntome')->orderBy($orderBy)->fi()
            ->page($pager,'t1.id')
            ->fetchAll('id');
        /* 保存查询条件并查询子需求条目。*/
        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'requirementinside', $browseType != 'bysearch');
        // return $this->process($requirements);
        return $requirements;
    }

    public function getPairs($orderBy = 'id_desc')
    {
        $requirements = $this->dao->select('id,name')->from(TABLE_REQUIREMENT)
            ->where('status')->ne('deleted')
            ->andWhere('sourceRequirement')->eq(2)
            ->orderBy($orderBy)
            ->fetchPairs();
        return $requirements;
    }

    public function getCodePairs($orderBy = 'id_desc')
    {
        $requirements = $this->dao->select('id,code')->from(TABLE_REQUIREMENT)
            ->where('status')->ne('deleted')
            ->andWhere('sourceRequirement')->eq(2)
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
            ->andWhere('sourceRequirement')->eq(2)
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
        $info = $this->dao->select('id,code,name')->from(TABLE_REQUIREMENT)
            ->where('status')->ne('deleted')
            ->andwhere('id')->in($ids)
            ->orderBy($orderBy)
            ->fetchall();
        $requirements = new stdClass();
        foreach ($info as $item)
        {
            $id = $item->id;
            $requirements->$id = ['code'=>$item->code, 'name' =>$item->name];
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

        $cs = $this->dao->select('*')->from(TABLE_CONSUMED)->where('objectType')->eq('requirement') //状态流转 工作量
        ->andWhere('objectID')->eq($requirement->id)
            ->andWhere('deleted')->ne(1)
            ->orderBy('id_asc')
            ->fetchAll();
        $requirement->consumed = $cs;

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
            ->stripTags($this->config->requirementinside->editor->create['id'], $this->config->allowedTags)
            ->get();

        //判断空处理
        if(count($_POST['nextUser']) == 1 && $_POST['nextUser'][0] == '')
        {
            dao::$errors[] = sprintf($this->lang->requirementinside->emptyObject, $this->lang->requirementinside->dealUser);
        }

        $this->tryError();
        $demandTitle = $_POST['demandTitle'];
        $demandDesc = $_POST['demandDesc'];
        $deadlines = $_POST['deadlines'];
        $end = $_POST['end'];
        $nextUser = $_POST['nextUser'];
        $progress = $_POST['progress'];

        foreach ($demandTitle as $title){
            if($title == '')
            {
                $errors['demandTitle'] = sprintf($this->lang->requirementinside->emptyObject, $this->lang->requirementinside->name);
                return dao::$errors = $errors;
            }
        }
        
        for ($i=0; $i < count($demandTitle); $i++){
            $app = $i == 0 ? 'app' : 'app'.$i;
            if(!isset($_POST[$app]))
            {
                $errors[''] = sprintf($this->lang->requirementinside->emptyObject, $this->lang->requirementinside->app);
                return dao::$errors = $errors;
            }
        }

        foreach ($deadlines as $deadline) {
            if(!$this->loadModel('common')->checkJkDateTime($deadline))
            {
                dao::$errors['deadlines'] =  sprintf($this->lang->requirementinside->emptyObject, $this->lang->requirementinside->deadLineDate);
                return;
            }
        }

        foreach ($end as $item) {
            if(!$this->loadModel('common')->checkJkDateTime($item))
            {
                dao::$errors['end'] =  sprintf($this->lang->requirementinside->emptyObject, $this->lang->requirementinside->planEnd);
                return;
            }
        }

        foreach ($demandDesc as $desc){
            if($desc == '')
            {
                $errors['demandDesc'] = sprintf($this->lang->requirementinside->emptyObject, $this->lang->requirementinside->desc);
                return dao::$errors = $errors;
            }
        }

        //计划完成时间不允许大于期望完成时间
        /*
        foreach ($demandTitle as $i => $value){
            if(strtotime($end[$i]) > strtotime($deadlines[$i]))
            {
                $errors[] = $this->lang->requirementinside->editEndTip;
                return dao::$errors = $errors;
            }
        }
        */

        if(dao::isError()) return false;
        $opinionData = $this->loadModel('opinioninside')->getOwnerDefineFieldById($opinionID);

        foreach ($demandTitle as $i => $title){
            /* 获取单个需求意向拆分为需求条目的表单数据，进行需求条目拆分，拆分时会记录一个默认的需求版本记录，并记录需求意向操作动作。*/
            $requirementIdList = array();
            $uid = $requirements->uid;
            $requirementDescList = $demandDesc;
            $this->loadModel('consumed');
            if(!$title) unset($requirements->demandTitle[$i]);

            $data = new stdClass();
            $data->name        = $title;
            $data->status      = 'published';
            $data->opinion     = $opinionID;
            $data->deadLine       = $deadlines[$i] ?? '';
            $data->planEnd          = $end[$i] ?? '';
            $data->comment       = $progress[$i] ?? '';
            $data->dealUser       = join(',',$requirements->nextUser) ?? '';
            $data->createdBy   = $this->app->user->account;
            $data->createdDate = helper::now();
            $app = $i ? $_POST['app' . $i] : $_POST['app'];
            $data->app = $app ? join(',',$app) : '';
            $data->productManager = $this->app->user->account;
            $data->projectManager = trim(implode(',',$nextUser), ',');

            // 需求意向主题，业务需求单位，需求提出时间
            $data->nameByOpinion = $opinionData->name;
            $data->union = $opinionData->union;
            $data->dateByOpinion = $opinionData->createdDate;
            $data->acceptTime = $opinionData->sourceMode=='8'?helper::now():$opinionData->receiveDate;
            // 处理富文本字段内容。
            $_POST['desc'] = $requirementDescList[$i];
            $postData = fixer::input('post')->stripTags('desc', $this->config->allowedTags)->get();
            $data->desc = $postData->desc;
            $data->nameByOpinion = $opinionData->name;
            $data->sourceRequirement = 2;

            $this->dao->insert(TABLE_REQUIREMENT)
                ->data($data)
                ->autoCheck()
                ->exec();
            $requirementID = $this->dao->lastInsertID();
            $this->loadModel('file')->updateObjectID($uid . $i, $requirementID, 'requirement');
            $this->file->saveUpload('requirement', $requirementID, '', 'files' . $i);
            $date   = helper::today();
            $codeBefore = substr( $date, 0, 4) . sprintf('%03d', $opinionID);
            $number = $this->dao->select('count(id) c')
                ->from(TABLE_REQUIREMENT)
                ->where('code')
                ->like('CFIT-W-'.$codeBefore.'%')
                ->andWhere('sourceRequirement')
                ->eq(2)
                ->fetch('c');

            $code   = 'CFIT-W-'.$codeBefore . '-' . sprintf('%02d', $number+1);
            $this->dao->update(TABLE_REQUIREMENT)->set('code')->eq($code)->where('id')->eq($requirementID)->exec();

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
//            $this->loadModel('requirement')->sendmail($requirementID,$actionID);
            $requirementIdList[] = $requirementID;
        }
        if(!empty($requirementIdList))
        {
            $this->dao->update(TABLE_OPINION)->set('status')->eq('subdivided')
                ->where('id')->eq($opinionID)
                ->exec();
            $this->loadModel('consumed')->record('opinion', $opinionID, 0, $this->app->user->account, $opinionData->status, 'subdivided');
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
        $this->config->requirementinside->search['actionURL'] = $actionURL;
        $this->config->requirementinside->search['queryID']   = $queryID;
        //$this->config->requirementinside->search['params']['project']['values'] = array('' => '') + $this->loadModel('projectplan')->getPairs();
        $this->config->requirementinside->search['params']['project']['values'] = array('' => '') + $this->loadModel('project')->getPairs();
        $this->config->requirementinside->search['params']['line']['values']    = array('' => '') + $this->loadModel('product')->getLinePairs();
        $this->config->requirementinside->search['params']['product']['values'] = array('' => '') + $this->product->getCodeNamePairs();
        $this->loadModel('search')->setSearchParams($this->config->requirementinside->search);
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
            dao::$errors['result'] = $this->lang->requirementinside->resultEmpty;
            return;
        }
        if(!$comment)
        {
            dao::$errors['comment'] = $this->lang->requirementinside->commentEmpty;
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
            $this->loadModel('opinioninside')->updatePlanDeadline($requirementID,$spec->end);// 记录最大的计划完成时间。
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
    public function reviewfeedback($requirementID)
    {
        $oldRequirement = $this->getByID($requirementID);
        //判断当前状态和审核中传入的状态是否一致，以防有同组中其他人修改
        $res = $this->checkAllowReview($oldRequirement, $this->post->version, $this->post->reviewStage, $this->app->user->account);
        if(!$res['result']){
            dao::$errors['statusError'] = $res['message'];
        }

        if(empty($_POST['result'])){
            dao::$errors['result'] = sprintf($this->lang->requirementinside->emptyObject, $this->lang->requirementinside->result);
        }


        $this->tryError();


        $is_all_check_pass = false;
        $result = $this->loadModel('review')->check('requirement', $requirementID, $oldRequirement->version, $this->post->result, $this->post->approveComm, $oldRequirement->reviewStage, '', $is_all_check_pass);
        if($result == 'pass')
        {
            $add = 1;
            //下一审核节点
            $nextReviewStage = $oldRequirement->reviewStage + $add;
            //下一审核状态
            if(isset($this->lang->requirementinside->reviewNodeList[$nextReviewStage])){
                $status = $this->lang->requirementinside->reviewNodeList[$nextReviewStage];
            }
            $this->dao->update(TABLE_REQUIREMENT)->set('reviewStage = reviewStage+' . $add)->set('feedbackStatus')->eq($status)->where('id')->eq($requirementID)->exec();
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
                $actionID = $this->loadModel('action')->create('requirement', $requirementID, 'reviewed', $this->post->comment);
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
            $actionID = $this->loadModel('action')->create('requirement', $requirementID, 'reviewed', $this->post->comment);
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
            //$requirement = $this->loadModel('file')->processImgURL($requirement, $this->config->requirementinside->editor->change['id'], $this->post->uid);

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

            $method = zget($this->lang->requirementinside->methodList, $requirement->method);
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
            if(!empty($result))
            {
                $resultData = json_decode($result);
                if(isset($resultData->code) and $resultData->code == '200')
                {
                    $status = 'success';
                    $this->dao->update(TABLE_REQUIREMENT)->set('feedbackDealUser')->eq('')->set('feedbackStatus')->eq('toexternalapproved')->set('feedbackCode')->eq($resultData->data->Feedback_number)->set('feedbackDate')->eq(helper::now())->where('id')->eq($requirementID)->exec();
                    $this->loadModel('action')->create('requirement', $requirementID, 'syncsuccess', $resultData->message);
                    $this->loadModel('consumed')->record('requirement', $requirementID, 0, 'guestjk', $requirement->feedbackStatus, 'syncsuccess', array());
                    $updateStatus = 'syncsuccess';
                } else {
                    $this->dao->update(TABLE_REQUIREMENT)->set('feedbackDealUser')->eq($requirement->feedbackBy)->set('feedbackStatus')->eq('syncfail')->set('reviewComments')->eq($resultData->message)->set('feedbackDate')->eq(helper::now())->where('id')->eq($requirementID)->exec();
                    $this->loadModel('action')->create('requirement', $requirementID, 'qingzongsynfailed', $resultData->message);
                    $this->loadModel('consumed')->record('requirement', $requirementID, 0, 'guestjk', $requirement->feedbackStatus, 'syncfail', array());
                    $updateStatus = 'syncfail';
                    $updateComment = $resultData->message;
                }
                $response = $result;
            } else {
                $this->dao->update(TABLE_REQUIREMENT)->set('feedbackDealUser')->eq($requirement->feedbackBy)->set('feedbackStatus')->eq('syncfail')->set('reviewComments')->eq("网络不通")->set('feedbackDate')->eq(helper::now())->where('id')->eq($requirementID)->exec();
                $this->loadModel('action')->create('requirement', $requirementID, 'qingzongsynfailed', "网络不通");
                $this->loadModel('consumed')->record('requirement', $requirementID, 0, 'guestjk', $requirement->feedbackStatus, 'syncfail', array());
                $updateStatus = 'syncfail';
                $updateComment = '网络不通';
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

        /* 将操作动作换行小写后，计算该操作是否返回true，返回true则说明当前操作可以进行。*/
        $action = strtolower($action);
        //迭代二十六重新梳理：①待发布 待处理人 ②已发布、已拆分 产品经理（详情页产品经理字段取值）
        if($action == 'edit')     return  $app->user->account == 'admin' or ((in_array($requirement->status,array('topublish')) and strstr($requirement->dealUser, $app->user->account) !== false) or (in_array($requirement->status,array('published','splited')) and $requirement->productManager == $app->user->account));
        //指派 待发布、已发布、已拆分 待处理人
        if($action == 'assignto') return  $app->user->account == 'admin' or (in_array($requirement->status,array('topublish','published','splited')) and strstr($requirement->dealUser, $app->user->account) !== false);
        //拆分 已发布、已拆分 待处理人
        if($action == 'subdivide')return  $app->user->account == 'admin' or (in_array($requirement->status,array('published','splited'))  and (strstr($requirement->dealUser, $app->user->account) !== false ));
        //挂起 已拆分
        if($action == 'close')    return  $app->user->account == 'admin' or in_array($requirement->status,array('splited'));
        //激活
        if($action == 'activate') return  $app->user->account == 'admin' or in_array($requirement->status,array('splited','closed')) and $requirement->closedBy == $app->user->account;
        //忽略 待发布、已发布、已拆分 待处理人
        if($action == 'ignore')   return $app->user->account == 'admin' or (in_array($requirement->status,array('topublish','published','splited')) and strstr($requirement->dealUser, $app->user->account) !== false);
        //激活 待发布、已发布、已拆分 待处理人 忽略人
        if($action == 'ignore')   return $app->user->account == 'admin' or (in_array($requirement->status,array('topublish','published','splited')) and strstr($requirement->dealUser, $app->user->account) !== false) or (in_array($requirement->status,array('topublish','published','splited')) and $app->user->account == $requirement->ignoredBy);
        //删除 已发布 创建人
        if($action == 'delete')   return $app->user->account == 'admin' or ($requirement->status == 'published' and $app->user->account == $requirement->createdBy);
        //编辑计划完成时间
        if($action == 'editEnd')  return $app->user->account == 'admin' or (strstr($requirement->dealUser, $app->user->account) !== false);
        return true;
    }

    /**
     * TongYanQi 2023/1/13
     * 根据用户个人情况获取需求任务列表 用于倒挂
     */
    public function getRequirementByUser(){
        $requirements = $this->dao->select('id,code,name,status,closedBy, dealUser,createdBy,closedDate,owner,projectManager')->from(TABLE_REQUIREMENT)
        ->where('status')->ne('deleted')
        ->andWhere('status')->ne('topublish')
        ->andWhere('sourceRequirement')->eq(2)
        ->orderBy('id_desc')
        ->fetchAll();
        $demands = $this->dao->select('id,requirementID,acceptUser')->from(TABLE_DEMAND)->where('status')->notIN('closed,deleted')
            ->andWhere('sourceDemand')->eq(2)
            ->andWhere('requirementID')->ne(0)
            ->fetchAll();
        $combine = [];
        foreach ($demands as $demand)
        {
            $combine[$demand->requirementID][] = $demand->id;
        }
        $list = [];
        $acceptUserFromDemands = [];
//        $expDate = date('Y-m-d h:i:s', strtotime('-3 months'));
        foreach ($requirements as $requirement){
            //责任人需要根据需求条目并集获取

            //如果是已挂起 大于3个月的 不显示 status = closed 之前修正为已挂起（不是已关闭 需要注意）
//            if($opinion->status == 'closed' && $opinion->closedDate < $expDate)
//            {
//                continue;
//            }
//            //如果是已挂起 并且不是挂起人 也不是待处理人 不显示
//            if($opinion->status == 'closed' && $opinion->closedBy != $this->app->user->account && !in_array($this->app->user->account, $dealUserArr) && $opinion->createdBy != $this->app->user->account)
//            {
//                continue;
//            }
            if(isset($combine[$requirement->id])){
                $acceptUserFromDemands = $this->loadModel('demand')->getByIdListNew($combine[$requirement->id]);
                $acceptUserArr = array_filter(array_unique(array_column($acceptUserFromDemands,'acceptUser')));
                $requirement->owner = implode(',',$acceptUserArr);
            }

            $dealUserArr = empty($requirement->dealUser)? [] : explode(',', $requirement->dealUser);
            $projectManagerArr = empty($requirement->projectManager)? [] : explode(',', $requirement->projectManager);
            //如果是已挂起 去除非创建人、挂起人、待处理人、研发责任人、项目经理之外的数据
            if($requirement->status == 'closed' && $requirement->closedBy != $this->app->user->account && strstr($requirement->owner,  $this->app->user->account) == false && !in_array($this->app->user->account, $dealUserArr) && $requirement->createdBy != $this->app->user->account && !in_array($this->app->user->account, $projectManagerArr))
            {
                continue;
            }

            //待处理人和需求负责人并集取值
            if($requirement->status != 'closed' && !in_array($this->app->user->account, $dealUserArr) && strstr($requirement->owner,  $this->app->user->account) == false && !in_array($this->app->user->account, $projectManagerArr))
            {
                continue;
            }
            $requirement->name = $requirement->code . '_' . $requirement->name;
            if($requirement->status == 'closed'){ //todo 方便查看已挂起项
                $requirement->name = $requirement->name ."[已挂起]";
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
        $labelList = $this->lang->requirementinside->labelList;
        $opinion = $this->loadModel('opinioninside')->getById($requirement->opinion);
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
        $requirement->statusChn = zget($this->lang->requirementinside->statusList,$requirement->status,'');
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
        $modulePath = $this->app->getModulePath($appName = '', 'requirementinside');
        $oldcwd     = getcwd();
        $viewFile = $modulePath . 'view/sendmail.html.php';
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

        if($action->action == 'created' || $action->action == 'edited' || $action->action == 'subdivide' || $action->action == 'assigned' || $action->action == 'subdivided'|| $action->action == 'splited')
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
        if(empty($toList)) return false;
        $ccList = '';
        if($action->action == 'deleted'){
            $subject = "您有一个【需求任务-".$requirement->code."】已被删除，请知晓";
        }else{
            $subject = vsprintf($mailConf->mailTitle, $mailConf->variables);
        }
        /* Send mail. */
        /* 调用mail模块的send方法进行发信。*/
        $this->mail->send($toList, $subject, $mailContent, $ccList);
        if($this->mail->isError()){
            trigger_error(join("\n", $this->mail->getError()));
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
        return sprintf($this->lang->requirementinside->mail->$actionType, $this->app->user->realname, $requirement->id, $requirement->name);
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
        $opinionModel = $this->loadModel('opinioninside');
        //倒挂到新的任务
        $requirementObj = $this->getByIdSimple($requirementID);
        $demandInfo = $demandModel->getByRequirementID('*',$requirementID);
        $paramsArray = $this->changeRequirementStatus($demandInfo,$requirementObj);//获取最终状态
        $requirementStatus = !empty($paramsArray) ? $paramsArray['requirementStatus'] : '';

        $this->dao->begin(); //调试完逻辑最后开启事务
        /*更新新需求任务*/
        if($requirementObj->status != $requirementStatus)
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
        if($opinionObj->status != $opinionStatus)
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
        $code = '';
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
            if(in_array($demandStatus,['wait','feedbacked','build','released'])) //1.需求条目全部状态为<已录入、开发中、测试中、已发布>时，需求任务联动为<已拆分>。
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
            //1、已录入、开发中、测试中、已发布 需求任务为<已拆分>
            if(in_array('wait',$statusList) or in_array('feedbacked',$statusList) or in_array('build',$statusList) or in_array('released',$statusList))
            {
                foreach ($demandInfo as $item)
                {
                    if(in_array($item->status,['wait','feedbacked','build','released']))
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
        $enterArray = [];  //已录入
        $splitArray = []; //已拆分
        $deliverArray = []; //已交付
        $onlineStatusArray = []; //上线成功 需要单条进行处理
        //用于返回记录
        $enterIds = [];
        $splitIds = [];
        $deliverIds = [];
        $onlineIds = [];
        //历史记录
        $langStatus = $this->lang->requirementinside->statusList;
        foreach ($requirementInfo as $item)
        {
            $demandInfo = $demandModel->getByRequirementID('*',$item->id);
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
        $requirementInfo = $this->dao->select('id,status,code')
            ->from(TABLE_REQUIREMENT)
            ->where('status')
            ->ne("deleted")
            ->andWhere('createdDate')
            ->gt(date('Y-m-d', strtotime("-1 year")))
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
        $apps     = $this->loadModel('application')->getPairs();
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
        $assignedTo = $_POST['assignedTo'];
        if(empty($assignedTo))
        {
            dao::$errors['assignedTo'] = sprintf($this->lang->requirementinside->emptyObject, $this->lang->requirementinside->assignedTo);
        }
        //迭代二十六 待处理人发生变化 忽略自动恢复
//        if($oldOpinion->dealUser != $opinion->dealUser){
//            $opinion->ignore = '';
//        }
        $comment = $_POST['comment'];
        if(empty($comment))
        {
            dao::$errors['comment'] = sprintf($this->lang->requirementinside->emptyObject, $this->lang->requirementinside->comment);
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
        array_push($dealUsers, $assignedTo);  
        $dealUserStr = implode(',',$dealUsers);
        // 外部单子待确认指派产品经理
        if($requirement->status == 'topublish' and !empty($requirement->entriesCode)){
            $this->dao->update(TABLE_REQUIREMENT)
                ->set('productManager')->eq($assignedTo)
                ->where('id')->eq($requirementID)
                ->exec();
        }
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
        $data = $_POST;
        $this->app->loadLang('demandinside');
        $uid = $this->post->uid;
        //查询需求任务信息
        $requirementObj = $this->getByID($requirementID);
        //查询需求意向信息
        $opinionObj = $this->loadModel("opinioninside")->getByID($requirementObj->opinion);
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
                if(!isset($this->lang->requirementinside->subdivideRequired['execution'])) $this->lang->requirementinside->subdivideRequired['execution'] =  '所属阶段';
            }else {
                unset($this->lang->requirementinside->subdivideRequired['execution']);
            }*/
            $data['app' . $i] = isset($data['app' . $i]) ? explode(',',$data['app' . $i]) : '';
            foreach ($this->lang->requirementinside->subdivideRequired as $parms => $desc){
                if($data[$parms . $i] == '' or empty($data[$parms . $i]) or ($parms == 'app' and count($data[$parms . $i]) == 1 and $data[$parms . $i][0] == ''))
                {
                    return dao::$errors[$parms . $i] = sprintf($this->lang->requirementinside->emptyObject, $desc);
                }
            }

            $demand = new stdclass();
            $demand->rcvDate = helper::now();
            //产品和版本不是无，应用系统只能选择一个
            if($data['product' . $i] != '99999' && $data['productPlan' . $i] != '1' ){
                $apps = explode(',',trim(implode(',', $data['app' . $i]),','));
                if(count($apps) > 1){
                    return dao::$errors['app' . $i] = $this->lang->requirementinside->productAndPlanTips;
                }
            }
            if($data['fixType' . $i] == 'second')
            {
                // 判断二线实现的解决方案必须为二线项目。
                $plan = $this->dao->select('secondLine')->from(TABLE_PROJECTPLAN)->where('deleted')->eq(0)->andWhere('project')->eq($data['project' . $i])->fetch();
                if(empty($plan->secondLine))  return dao::$errors['project' . $i] = $this->lang->requirementinside->noSecondLinse;
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
            if(!$this->loadModel('common')->checkJkDateTime($data['endDate' . $i]))
            {
                dao::$errors['endDate'.$i] =  sprintf($this->lang->requirementinside->emptyObject, $this->lang->requirementinside->endDate);
                return;
            }

            if(!$this->loadModel('common')->checkJkDateTime($data['end' . $i]))
            {
                dao::$errors['end'.$i] =  sprintf($this->lang->requirementinside->emptyObject, $this->lang->requirementinside->end);
                return;
            }

            if(strtotime($data['end' . $i]) > strtotime($requirementObj->planEnd))
            {
                $errors[''] = $this->lang->requirementinside->editEndSubdivideDemandTip;
                return dao::$errors = $errors;
            }
            //下一节点处理人
            $nextUser = $data['dealUser'];
            if(empty($nextUser))
            {
                return dao::$errors['dealUser'] = sprintf($this->lang->demandinside->emptyObject, $this->lang->demandinside->nextUser);
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
            $demand->endDate     = $data['endDate' . $i];
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

            $demand->sourceDemand = 2;//标记内部
            $this->dao->insert(TABLE_DEMAND)->data($demand)->autoCheck()->exec();

            $demandID = $this->dao->lastInsertID();

            $this->loadModel('file')->updateObjectID($uid . $i, $demandID, 'demand');
            $this->file->saveUpload('demand', $demandID, '', 'files' . $i);

            // 更新需求代号。
            $date = date('Y-m-d');
            $number = $this->dao->select('count(id) c')->from(TABLE_DEMAND)->where('createdDate')->eq($date)->andWhere('sourceDemand')->eq(2)->fetch('c');
            $code = 'CFIT-WD-' . date('Ymd-') . sprintf('%02d', $number);
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
     * @return void
     */
    public function getDemandByRequirement($requirementID){
        $demands =  $this->dao->select('*')
            ->from(TABLE_DEMAND)
            ->where('requirementID')->eq("$requirementID")
            ->andWhere('status')->notIN('closed,deleted')
            ->fetchAll('id');
        return $demands;
    }

    /**
     * Desc: 根据id获取任务数据
     * User: wangshusen
     * Date: 2022/8/12
     * Time: 9:52
     *
     * @param $requirementID
     * @return mixed
     *
     */
    public function getByRequirementID($requirementID)
    {
        return $this->dao->select('id,deadLine,entriesCode,end,analysis,`desc`,app')->from(TABLE_REQUIREMENT)->where('id')->eq($requirementID)->fetch();
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

        $opinions = $this->loadModel('opinioninside')->getPairsByRequmentBrowse();
        $newOpinions = array();
        foreach ($opinions as $id => $name) {
            $newOpinions[$id] = $name . "(#$id)";
        }

        $apps = $this->loadModel('application')->getapplicationNameCodePairs();
        $newApps = array();
        foreach ($apps as $id => $name) {
            $newApps[$id] = $name . "(#$id)";
        }

        $statusList = $this->lang->requirementinside->searchstatusList;

        $this->post->set('statusList', array_values($statusList));
        $this->post->set('createdByList', array_values($newUsers));
        $this->post->set('opinionIDList', array_values($newOpinions));
        $this->post->set('dealUserList', array_values($newUsers));
        $this->post->set('appList',array_values($newApps));
        $this->post->set('listStyle', $this->config->requirementinside->exportlist->listFields);
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
                dao::$errors[] = sprintf($this->lang->requirementinside->duplicateNameError, array_search($data->name[$key],$names), $line);
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
            $requirementData->sourceRequirement = 2;

            if(empty($opinionID))
            {
                dao::$errors[] = sprintf($this->lang->requirementinside->noRequire, $line,$this->lang->requirementinside->opinionID);
            }

            /* 判断那些字段是必填的。*/
            if (isset($this->config->requirementinside->import->requiredFields)) {
                $requiredFields = explode(',', $this->config->requirementinside->import->requiredFields);
                foreach ($requiredFields as $requiredField) {
                    $requiredField = trim($requiredField);
                    if (empty($requirementData->$requiredField)) dao::$errors[] = sprintf($this->lang->requirementinside->noRequire, $line, $this->lang->requirementinside->$requiredField);
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
                        ->batchCheck($this->config->requirementinside->create->requiredFields, 'notempty')
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
                
                $opinionObj = $this->loadModel('opinioninside')->getByID($opinionID);
                // 更新需求代号。
                $date   = helper::today();
                $codeBefore = substr( $date, 0, 4) . sprintf('%03d', $requirementData->opinion);
                $number = $this->dao->select('count(id) c')
                    ->from(TABLE_REQUIREMENT)
                    ->where('code')
                    ->like('CFIT-W-'.$codeBefore.'%')
                    ->andWhere('sourceRequirement')
                    ->eq(2)
                    ->fetch('c');
                $code   = 'CFIT-W-'.$codeBefore . '-' . sprintf('%02d', $number+1);
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
                   /* ->batchCheck($this->config->requirementinside->create->requiredFields, 'notempty')*/
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
            dao::$errors['dealcomment'] = sprintf($this->lang->requirementinside->emptyObject, $this->lang->requirementinside->dealcomment);
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
     * @Notes: 更新内部反馈是否超时 只针对清总同步数据 ifOverDate内部 ifOverTimeOutSide外部
     * @Date: 2023/4/19
     * @Time: 11:02
     * @param $field
     * @param $ifOverDate 100表示需修改，通过脚本修改 1：否 2：是'
     * @param $before
     * @param $after
     * @Interface updateRequirementIfOverDate
     */
    public function updateRequirementIfOverDate($field, $ifOverDate, $before, $after)
    {
        /**
         * 获取内部反馈需更新的数据
         * @var consumedModel $consumedModel
         * @var reviewModel $reviewModel
         */
        $requirementInfo = $this->dao->select('id,ifOverDate,version,ifOverTimeOutSide')->from(TABLE_REQUIREMENT)
            ->where('createdBy')->eq('guestcn')
            ->andWhere($field)->eq($ifOverDate)
            ->fetchAll();
        $consumedModel = $this->loadModel('consumed');
        $reviewModel = $this->loadModel('review');
        $finalIdList = [];
        if($requirementInfo){
            foreach ($requirementInfo as $value) {
                $id = $value->id;
                $finalTime = '';
                $endTime = '';
                $consumedInfo = $consumedModel->getCreatedDate('requirement', $id, $before, $after);
                if($consumedInfo){
                    $startTime = $consumedInfo->createdDate;
                    $hms = substr($startTime,10);
                    $this->app->loadLang('demand');
                    //内部
                    if($field == 'ifOverDate'){
                        $days = $this->lang->demand->expireDaysList['insideDays'];
                        $endTime = helper::getTrueWorkDay($startTime,$days,true).$hms; //结束时间
                        $finalTime = date('Y-m-d H:i:s',time()); //若部门审核一致未通过，或未到达部门审核节点，则取当前系统时间对比
                        //部门审核通过的时间
                        $node = $reviewModel->getNodeInfoByParams('id','pass',$value->version,'requirement',$id,1);
                        if($node){
                            $reviewTime = $reviewModel->getReviewerInfoByParams('id,reviewTime','pass',$node->id);
                            if($reviewTime){
                                $finalTime = $reviewTime->reviewTime;
                            }
                        }
                    }
                    if($field == 'ifOverTimeOutSide'){
                        //外部
                        $days = $this->lang->demand->expireDaysList['outsideDays'];
                        $endTime = helper::getTrueWorkDay($startTime,$days,true).$hms; //结束时间
                        $node = $reviewModel->getNodeInfoByParams('id','pass',$value->version,'requirement',$id,2);
                        $finalTime = date('Y-m-d H:i:s',time()); //若部门审核一致未通过，或未到达部门审核节点，则取当前系统时间对比
                        if($node){
                            $reviewTime = $reviewModel->getReviewerInfoByParams('id,reviewTime','pass',$node->id);
                            if($reviewTime){
                                $finalTime = $reviewTime->reviewTime;
                            }
                        }
                    }
                    //处理入库
                    if($finalTime > $endTime){ //已超时
                        $this->updateRequirementOverDate($field,2,$id);
                        $finalIdList[] = $id;
                    }else{
                        if($value->$field != 1){ //未超时 原状态不为未超时更新
                            $this->updateRequirementOverDate($field,1,$id);
                            $finalIdList[] = $id;
                        }
                    }
                }else{
                    //取不到待发布到已发布的节点 不超时
                    if($value->$field != 1){ //未超时 原状态不为未超时更新
                        $this->updateRequirementOverDate($field,1,$id);
                        $finalIdList[] = $id;
                    }
                }

            }
        }
        return $finalIdList;
    }

    /**
     * @Notes: 更新内外部是否超时
     * @Date: 2023/4/19
     * @Time: 15:11
     * @Interface updateRequirementOverDate
     * @param $field
     * @param $result
     * @param $id
     */
    public function updateRequirementOverDate($field,$result,$id)
    {
        $this->dao->update(TABLE_REQUIREMENT)->set($field)->eq($result)->where('id')->eq($id)->exec();
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

        $url = '';
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

        return ['toList'=>$toList,'subcontent'=>$subcontent,'url'=>$url,'title'=>$title,'actions'=>$actions];
    }
}
