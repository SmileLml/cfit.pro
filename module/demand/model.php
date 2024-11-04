<?php

class demandModel extends model
{
    public static $_dealStatus = ['wait','confirmed','assigned'];
    /**
     * Project: chengfangjinke
     * Method: getList
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:14
     * Desc: This is the code comment. This method is called getList.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $browseType
     * @param $queryID
     * @param $orderBy
     * @param null $pager
     * @return mixed
     */
    public function getList($browseType, $queryID, $orderBy, $pager = null)
    {
        $demandQuery = '';
        if ($browseType == 'bysearch') {
            $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : '';
            if ($query) {
                $this->session->set('demandQuery', $query->sql);
                $this->session->set('demandForm', $query->form);
            }

            if ($this->session->demandQuery == false) $this->session->set('demandQuery', ' 1 = 1');

            $demandQuery = $this->session->demandQuery;

            // 处理[所属业务系统]搜索字段
            if (strpos($demandQuery, '`app`') !== false) {
                $demandQuery = str_replace('`app`', "CONCAT(',', `app`, ',')", $demandQuery);
            }

            // 处理[系统分类]搜索字段
            if (strpos($demandQuery, '`isPayment`') !== false) {
                $demandQuery = str_replace('`isPayment`', "CONCAT(',', `isPayment`, ',')", $demandQuery);
            }
        }
        $dealUserQuery = '';
        if($browseType == 'my'){
            $dealUserQuery = "((dealUser = '".$this->app->user->account."' and status in ('wait')) or (FIND_IN_SET('".$this->app->user->account."',delayDealUser) and status not in ('deleteout','closed')))";
        }
        $demands = $this->dao->select('*')->from(TABLE_DEMAND)
            ->where('status')->ne('deleted')
            ->andWhere('sourceDemand')->eq(1) //查询外部的数据
            ->beginIF($browseType == 'ignore')->andWhere('ignoreStatus')->eq('1')->andWhere('ignoredBy')->like("%{$this->app->user->account}%")->fi()
            ->beginIF($browseType != 'all' and $browseType != 'ignore' and $browseType != 'my' and $browseType != 'bysearch')->andWhere('status')->eq($browseType)->fi()
            ->beginIF($browseType == 'bysearch')->andWhere($demandQuery)->fi()
            /*->beginIF($browseType == 'my')->andWhere('dealUser')->eq($this->app->user->account)->andWhere('status')->iN('wait,suspend')->fi()*/
            ->beginIF($browseType == 'my')->andWhere($dealUserQuery)->fi()
            ->beginIF($browseType == 'my')->orderBy('ignoreStatus_asc,'.$orderBy)->fi()
            ->beginIF($browseType != 'my')->orderBy($orderBy)->fi()
            ->page($pager)
            ->fetchAll('id');
//            $this->dao->printSQL();die;
        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'demand', $browseType != 'bysearch');

        $dmap = $this->dao->select('account,dept')->from(TABLE_USER)->fetchAll('account');
        foreach ($demands as $key => $demand) {
            //开发中  已交付 上线成功 处理人置空显示 已关闭也不显示待处理人 迭代三十取消测试中 已发布 增加变更单异常、变更单回退
            if(in_array($demand->status, ['feedbacked','changeabnormal','chanereturn','delivery','onlinesuccess','closed','suspend'])){
                $demands[$key]->dealUser  = '';
            }
            if(isset($dmap[$demand->createdBy])){
                $demands[$key]->createdDept = $dmap[$demand->createdBy]->dept;
            }
            $demands[$key]->creatorCanEdit = 0;
            if($this->checkCreatorPri($demand)){
                $demands[$key]->creatorCanEdit = 1;
            }
            if($demand->actualOnlineDate == '0000-00-00 00:00:00') $demand->actualOnlineDate = '';
            if($demand->end == '0000-00-00') $demand->end = '';
            if($demand->endDate == '0000-00-00') $demand->endDate = '';
        }

        return $demands;
    }

    /**
     * Project: chengfangjinke
     * Method: buildSearchForm
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:14
     * Desc: This is the code comment. This method is called buildSearchForm.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $queryID
     * @param $actionURL
     */
    public function buildSearchForm($queryID, $actionURL)
    {
        $this->config->demand->search['actionURL'] = $actionURL;
        $this->config->demand->search['queryID'] = $queryID;
        $this->config->demand->search['params']['createdBy']['values'] = array('' => '') + $this->loadModel('user')->getPairs('noletter|noclosed');

        $this->loadModel('search')->setSearchParams($this->config->demand->search);
    }

    /**
     * Project: chengfangjinke
     * Method: create
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:14
     * Desc: This is the code comment. This method is called create.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @return mixed
     */
    public function create()
    {

        $data = fixer::input('post')
            ->remove('files,labels,consumed,uid,mailto,flag,executionid')
            ->join('app', ',')
            ->join('requirement', ',')
            ->stripTags($this->config->demand->editor->create['id'], $this->config->allowedTags)
            ->get();
        /**
         * @var requirementModel $requirementModel
         * 倒挂需求任务时，登录人为需求任务的创建人和待处理人倒挂校验
         */
        $this->app->loadLang('requirement');
        $requirementModel = $this->loadModel('requirement');
        // 倒挂状态修改
        $opinionObj = $this->loadModel('opinion')->getByID($data->opinionID);
        $requirementObj = $this->loadModel('requirement')->getByID($data->requirementID);
        $checkRequirement = $requirementModel->getRequirementByUser();
        if($checkRequirement)
        {
            if(!in_array($data->requirementID,array_keys($checkRequirement)))
            {
                return dao::$errors[] = $this->lang->demand->checkSubmitTip;
            }
        }

        if($data->product == 99999) $data->productPlan = 1;//产品选无 版本也是无
        if ($data->product > 0 && $data->productPlan == 0) {
            return dao::$errors['productPlan'] = $this->lang->demand->productPlanEmpty;
        }
        if (!isset($data->app)) {
            return dao::$errors['app'] = $this->lang->demand->appEmpty;
        }
        /*迭代22优化*/
//        $checkRes = $this->loadModel('consumed')->checkConsumedInfo($this->post->consumed);
//        if (!$checkRes) {
//            return false;
//        }

        //期望完成时间
//        if(!$this->loadModel('common')->checkJkDateTime($data->endDate))
//        {
//            dao::$errors['endDate'] =  sprintf($this->lang->demand->emptyObject, $this->lang->demand->endDate);
//            return;
//        }

        //计划完成时间
        if(!$this->loadModel('common')->checkJkDateTime($data->end)){
            return dao::$errors['end'] =  sprintf($this->lang->demand->emptyObject, $this->lang->demand->end);
        }

        //计划完成时间不允许大于所属任务的计划完成时间
        if (strtotime($data->end) > strtotime($requirementObj->planEnd)) {
            $errors['end'] = $this->lang->requirement->editEndSubdivideDemandTip;
            return dao::$errors = $errors;
        }


        $data->createdBy = $this->app->user->account;
        $data->createdDate = helper::today();
        $data->createdDept = $this->app->user->dept;
        $data->rcvDate = helper::now();

        if (!empty($data->opinionID)) {
            $opinion = $this->loadModel('opinion')->getByID($data->opinionID);
            $data->type = empty($opinion->sourceMode) ? '' : $opinion->sourceMode;
            $data->source = empty($opinion->sourceName) ? '' : $opinion->sourceName;
            $data->union = empty($opinion->union) ? '' : $opinion->union;
        }

        //实施部门需要根据实施责任人查询，构造数据
        if (!empty($data->acceptUser)) {
            $acceptUser = $this->loadModel('user')->getByAccount($data->acceptUser);
            $data->acceptDept = $acceptUser->dept;
        }

        //解决数据库类型自动检测的问题，需要是整型
        if (empty($data->productPlan)) {
            $data->productPlan = 0;
        }

        //二线项目不校验
        /*$info = $this->loadModel('projectplan')->getByProjectID($data->project);
        if($info->secondLine == 0){
            //所属阶段
            if (empty($data->execution)) {
                dao::$errors['execution'] =  sprintf($this->lang->demand->emptyObject, $this->lang->demand->execution);
                return;
            }
        }*/

        //由于下一节点处理人提示为待处理人，需单独处理
        if (empty($data->dealUser)) {
            return dao::$errors['dealUser'] = $this->lang->demand->nextUserEmpty;
        }

        /*迭代22优化*/
//        if (empty(strip_tags($data->desc))) {
//            return dao::$errors['desc'] = $this->lang->demand->descEmpty;
//        }

        if($data->progress){
            $users = $this->loadModel('user')->getPairs('noclosed');
            $data->progress = '<span style="background-color: #ffe9c6">' .helper::now()." 由<strong>".zget($users,$this->app->user->account,'')."</strong>新增".'<br></span>'.$data->progress;
        }

        $data->lastDealDate = date('Y-m-d');
        $data = $this->loadModel('file')->processImgURL($data, $this->config->demand->editor->create['id'], $this->post->uid);


        //清总同步的数据
        if($requirementObj->createdBy == 'guestcn'){
            if(empty($opinionObj->category)){
                return dao::$errors['infoEmpty'] = "所属意向未补充完整请联系产品经理进行补充";
            }
        }

        //迭代二十五要求暂时注释 预留后续使用
//        $expDate = date('Y-m-d h:i:s', strtotime('-3 months'));
//        if($requirementObj->status == 'onlined'){
//            if($requirementObj->status == 'onlined' && $requirementObj->onlineTimeByDemand  != '0000-00-00 00:00:00' && $requirementObj->onlineTimeByDemand  != "" && $requirementObj->onlineTimeByDemand < $expDate){
//                return dao::$errors['expired'] = "上线成功3个月之后不允许进行倒挂，请按照新的需求处理";
//            }
//        }

        //产品和版本不是无，应用系统只能选择一个
        if($this->post->product != '99999' && $this->post->productPlan != '1' ){
            $apps = explode(',',trim($data->app,','));
            if(count($apps) > 1){
                return dao::$errors['app'] = $this->lang->demand->productAndPlanTips;
            }
        }
        $data->secondLineDevelopmentRecord = 2;
        /*迭代三十四 二线实现项目 二线月报跟踪标记位标记为纳入 项目实现为不纳入*/
        if($data->fixType == 'second')
        {
            $data->secondLineDevelopmentRecord = 1;
            // 判断二线实现的解决方案必须为二线项目。
            $plan = $this->dao->select('secondLine')->from(TABLE_PROJECTPLAN)->where('deleted')->eq('0')->andWhere('project')->eq($data->project)->fetch();
            if(empty($plan->secondLine)) return dao::$errors = array('' => $this->lang->demand->noSecondLinse);
        }
        // 根据【所属应用系统】处理系统分类字段的值。
        $paymentIdList = array();
        if(isset($data->app))
        {
            foreach(explode(',', $data->app) as $appID)
            {
                if(!$appID) continue;
                $paymentType = $this->dao->select('isPayment')->from(TABLE_APPLICATION)->where('id')->eq($appID)->fetch('isPayment');
                if($paymentType) $paymentIdList[] = $paymentType;
            }
            $data->isPayment = implode(',', $paymentIdList);
        }
       /* if($this->post->flag != '1')
        {
            $data->execution = isset($_POST['execution']) ? $this->post->execution : $this->post->executionid;
        }*/

        //需求任务待处理人为空的时候，倒挂成功后将倒挂人显示为需求任务的待处理人
        if(in_array($requirementObj->status,['delivered','onlined']))
        {
            $this->dao->update(TABLE_REQUIREMENT)->set('dealUser')->eq($this->app->user->account)->where('id')->eq($data->requirementID)->exec();
        }

        $this->dao->insert(TABLE_DEMAND)->data($data)
            ->autoCheck()->batchCheck($this->config->demand->create->requiredFields, 'notempty')
            ->exec();
        $demandID = $this->dao->lastInsertId();

        $date = date('Y-m-d');
        $number = $this->dao->select('count(id) c')->from(TABLE_DEMAND)->where('createdDate')->eq($date)->andWhere('sourceDemand')->eq(1)->fetch('c');
        $code = 'CFIT-D-' . date('Ymd-') . sprintf('%02d', $number);
        $this->dao->update(TABLE_DEMAND)->set('code')->eq($code)->where('id')->eq($demandID)->exec();

        /*同步更新需求任务和需求意向的状态 变更中不实时更新状态*/
        //只有非已拆分时才更新状态为已拆分，增加状态流转
        if($requirementObj->status != 'underchange') {
            if ($requirementObj->status != 'splited') {
                $this->loadModel('requirement')->updateRequirement($data->requirementID, 'splited');
                $this->loadModel('consumed')->record('requirement', $data->requirementID, 0, $this->app->user->account, $requirementObj->status, 'splited');
            }
            //只有非已拆分时才更新状态为已拆分，增加状态流转以及历史记录
            if (!in_array($opinionObj->status,['subdivided','underchange'])) {
                $this->loadModel('opinion')->updateStatusById('subdivided', $data->opinionID);
                $this->loadModel('consumed')->record('opinion', $data->opinionID, 0, $this->app->user->account, $opinionObj->status, 'subdivided');
                $subdividedArray = $this->loadModel('requirement')->insertActionArray($data->opinionID, $requirementObj->code, $opinionObj->status);
                if (!empty($subdividedArray)) {
                    $langStatus = $this->lang->opinion->statusList;
                    $this->loadModel('action')->createActions('opinion', $subdividedArray, 'subdividedactual', $langStatus, 2);
                }
            }
        }
        
        if (!dao::isError())
        {
            $this->loadModel('consumed')->record('demand', $demandID, 0, $this->app->user->account, '', 'wait', $this->post->mailto);

            $this->loadModel('file')->updateObjectID($this->post->uid, $demandID, 'demand');
            $this->file->saveUpload('demand', $demandID);
        }
        return $demandID;
    }

    /**
     * Project: chengfangjinke
     * Method: update
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:14
     * Desc: This is the code comment. This method is called update.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $demandID
     * @return array
     */
    public function update($demandID)
    {
        $this->app->loadLang('requirement');
        $oldDemand = $this->getByID($demandID);
        $demand = fixer::input('post')
            ->join('app', ',')
            ->join('requirement', ',')
            ->remove('uid,files,labels,consumed,mailto,flag,executionid')
            ->stripTags($this->config->demand->editor->edit['id'], $this->config->allowedTags)
            ->get();
        /**@var requirementModel $requirementModel*/
        $requirementModel = $this->loadModel('requirement');
        $requirementObj = $requirementModel->getByID($oldDemand->requirementID);
        $res = $this->checkAllowEdit($oldDemand);
        if (!$res){
            return dao::$errors[] = $this->lang->demand->noAllowEdit;
        }

        if($demand->product == 99999) $demand->productPlan = 1;//产品选无 版本也是无
        if ($demand->product > 0 && $demand->productPlan == 0) {
            return dao::$errors['productPlan'] = $this->lang->demand->productPlanEmpty;
        }
        if (!isset($demand->app)) {
            return dao::$errors['app'] = $this->lang->demand->appEmpty;
        }
//        //期望完成时间
//        if(!$this->loadModel('common')->checkJkDateTime($demand->endDate))
//        {
//            dao::$errors['endDate'] =  sprintf($this->lang->demand->emptyObject, $this->lang->demand->endDate);
//            return;
//        }

        //计划完成时间
        if(!$this->loadModel('common')->checkJkDateTime($demand->end))
        {
            dao::$errors['end'] =  sprintf($this->lang->demand->emptyObject, $this->lang->demand->end);
            return;
        }

        //计划完成时间不允许大于所属任务的计划完成时间
        if (strtotime($demand->end) > strtotime($requirementObj->planEnd)) {
            $errors['end'] = $this->lang->requirement->editEndSubdivideDemandTip;
            return dao::$errors = $errors;
        }

        if($demand->requirementID != $oldDemand->requirementID ){
            // 倒挂状态修改
            $opinionObj = $this->loadModel('opinion')->getByID($demand->opinionID);

            if(empty($opinionObj->category)){
               return dao::$errors['infoEmpty'] = "所属意向未补充完整请联系产品经理进行补充";
            }

        }

        //实施部门需要根据实施责任人查询，构造数据
        if (!empty($demand->acceptUser)) {
            $acceptUser = $this->loadModel('user')->getByAccount($demand->acceptUser);
            $demand->acceptDept = $acceptUser->dept;
        }
        
        //解决数据库类型自动检测的问题，需要是整型
        if (empty($demand->productPlan)) {
            $demand->productPlan = 0;
        }

        //由于下一节点处理人提示为待处理人，需单独处理
        if (empty($demand->dealUser)) {
            return dao::$errors['dealUser'] = $this->lang->demand->nextUserEmpty;
        }

        $demand->editedBy = $this->app->user->account;
        $demand->editedDate = date('Y-m-d');
        $demand->status = "wait";//编辑完成后数据流程状态为【已录入】
        if($demand->progress){
            $users = $this->loadModel('user')->getPairs('noclosed');
            $progress = '<span style="background-color: #ffe9c6">' .helper::now()." 由<strong>".zget($users,$this->app->user->account,'')."</strong>新增".'<br></span>'.$demand->progress;
            $demand->progress = $oldDemand->progress .'<br>'.$progress;
        }else{
            unset($demand->progress);
        }

        //产品和版本不是无，应用系统只能选择一个
        if($this->post->product != '99999' && $this->post->productPlan != '1' ){
            $apps = explode(',',trim($demand->app,','));
            if(count($apps) > 1){
                return dao::$errors['app'] = $this->lang->demand->productAndPlanTips;
            }
        }

        $demand->secondLineDevelopmentRecord = 2;
        /*迭代三十四 二线实现项目 二线月报跟踪标记位标记为纳入 项目实现为不纳入*/
        if($demand->fixType == 'second')
        {
            $demand->secondLineDevelopmentRecord = 1;
            // 判断二线实现的解决方案必须为二线项目。
            $plan = $this->dao->select('*')->from(TABLE_PROJECTPLAN)->where('deleted')->eq(0)->andWhere('project')->eq($demand->project)->fetch();
            if(empty($plan->secondLine)) return dao::$errors = array('' => $this->lang->demand->noSecondLinse);
        }
        // 根据【所属应用系统】处理系统分类字段的值。
        $paymentIdList = array();
        if(isset($demand->app))
        {
            foreach(explode(',', $demand->app) as $appID)
            {
                if(!$appID) continue;
                $paymentType = $this->dao->select('isPayment')->from(TABLE_APPLICATION)->where('id')->eq($appID)->fetch('isPayment');
                if($paymentType) $paymentIdList[] = $paymentType;
            }
            $demand->isPayment = implode(',', $paymentIdList);
        }
        //如果是select，则获取相关数据
        /*if($this->post->flag != '1'){
            $demand->execution = isset($_POST['execution']) ? $this->post->execution : $this->post->executionid;
        }*/
        //待处理人发生变化，忽略自动恢复
        if($oldDemand->dealUser != $demand->dealUser) {
            $demand->ignoreStatus = 0;
        }

        $demand = $this->loadModel('file')->processImgURL($demand, $this->config->demand->editor->edit['id'], $this->post->uid);
        $this->dao->update(TABLE_DEMAND)->data($demand)->autoCheck()
            ->batchCheck($this->config->demand->edit->requiredFields, 'notempty')
            ->where('id')->eq($demandID)
            ->exec();
        //如果是input不可编辑，则置空阶段 避免再次分析时阶段获取错误
        /*if($this->post->flag == '1'){
            $this->dao->update(TABLE_DEMAND)->set('execution')->eq('')
                ->where('id')->eq($demandID)
                ->exec();
        }*/
        if(!dao::isError() && $oldDemand->status != 'wait') {
            $this->loadModel('consumed')->record('demand', $demandID, 0, $this->app->user->account, $oldDemand->status, $demand->status);
        }

//        //修改工作量
//        $this->dao->update(TABLE_CONSUMED)->set('consumed')->eq($consumed) //修改工作量
//        ->where('id')->eq(end($oldDemand->consumed)->id)
//        ->exec();

        $this->loadModel('file')->updateObjectID($this->post->uid, $demandID, 'demand');
        $this->file->saveUpload('demand', $demandID);

        if($demand->requirementID != $oldDemand->requirementID) {

//            $requirementOldObj = $this->loadModel('requirement')->getByID($oldDemand->requirementID);

            //条目、任务、意向的状态联动处理
            $requirementModel->getStatusByID($demand->requirementID,$oldDemand->requirementID);
        }
        return common::createChanges($oldDemand, $demand);
    }

    public function assignment($id)
    {
        $oldDemand = $this->getByID($id);
        $demand = fixer::input('post')
            ->remove('comment')
            ->get();

        $reg = '/^(([1-9][0-9]*)|(([0]\.\d{1}|[1-9][0-9]*\.\d{1})))$/';
        if(empty($demand->dealUser)){
            dao::$errors['dealUser'] =  sprintf($this->lang->demand->emptyObject, $this->lang->demand->assignTo);
            return;
        }        

        if($oldDemand->dealUser != $demand->dealUser)
        {
            $demand->ignoreStatus = 0;
        }
        $this->dao->update(TABLE_DEMAND)->data($demand)
            ->batchCheck($this->config->demand->assignment->requiredFields, 'notempty')
            ->where('id')->eq($id)
            ->exec();

        $this->loadModel('consumed')->record('demand', $id, 0, $this->app->user->account, $oldDemand->status, $oldDemand->status);
        if (!dao::isError()) return common::createChanges($oldDemand, $demand);
    }

    /**
     * Project: chengfangjinke
     * Method: editSpecial
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:14
     * Desc: This is the code comment. This method is called editSpecial.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $demandID
     * @return array
     */
    public function editSpecial($demandID)
    {
        $oldDemand = $this->getByID($demandID);
        $demand = fixer::input('post')
            ->remove('uid')
            ->get();
        $demand->conclusion = trim($demand->conclusion);
        $demand->secondLineDevelopmentRecord = trim($demand->secondLineDevelopmentRecord);
        $this->dao->update(TABLE_DEMAND)->data($demand)->where('id')->eq($demandID)->exec();

        return common::createChanges($oldDemand, $demand);
    }

    /**
     * Project: chengfangjinke
     * Method: editAssignedTo
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:14
     * Desc: This is the code comment. This method is called editAssignedTo.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $demandID
     * @return array
     */
    public function editAssignedTo($demandID)
    {
        // Obtain the receiver. 张二欢5月31日提出屏蔽此逻辑
        /*$acceptUser = $this->dao->select('*')->from(TABLE_CONSUMED)
             ->where('objectType')->eq('demand')
             ->andWhere('objectID')->eq($demandID)
             ->andWhere('`before`')->eq('assigned')
             ->fetch();
        if(empty($acceptUser)) return dao::$errors['acceptStatusEmpty'] = $this->lang->demand->acceptStatusEmpty;*/

        $oldDemand = $this->getByID($demandID);
        $demand = array();

        if (empty($_POST['acceptUser'])) {
            return dao::$errors['acceptUser'] = $this->lang->demand->acceptUserEmpty;
        } else {
            $this->dao->update(TABLE_CONSUMED) //编辑分配人
            ->set('account')->eq($_POST['acceptUser'])
                ->where('objectType')->eq('demand')
                ->andWhere('objectID')->eq($demandID)
                ->andWhere('`before`')->eq('assigned')
                ->exec();

            $this->dao->update(TABLE_DEMAND)
                ->set('acceptUser')->eq($_POST['acceptUser'])
                ->where('id')->eq($demandID)
                ->exec();
        }

        return common::createChanges($oldDemand, $demand);
    }

    /**
     * Project: chengfangjinke
     * Method: getConsumedByID
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:14
     * Desc: This is the code comment. This method is called getConsumedByID.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $consumedID
     * @return mixed
     */
    public function getConsumedByID($consumedID)
    {
        return $this->dao->select('*')->from(TABLE_CONSUMED)->where('id')->eq($consumedID)->fetch();
    }

    /**
     * Desc:获取流程关闭时的时间
     * Date: 2022/3/28
     * Time: 17:44
     *
     * @param $problemID
     *
     */
    public function getDate($demandID)
    {
        return $this->dao->select('lastDealDate')->from(TABLE_DEMAND)->where('id')->eq($demandID)->fetch();;
    }

    /**
     * Project: chengfangjinke
     * Method: getConsumedList
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:14
     * Desc: This is the code comment. This method is called getConsumedList.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $demandID
     * @return mixed
     */
    public function getConsumedList($demandID)
    {
        $cs = $this->dao->select('*')->from(TABLE_CONSUMED)->where('objectType')->eq('demand')
            ->andWhere('objectID')->eq($demandID)
            ->andWhere('parentID')->eq('0')
            ->andWhere('deleted')->eq(0)
            ->orderBy('id_asc')
            ->fetchAll();
        return $cs;
    }

    /**
     * Project: chengfangjinke
     * Method: workloadDelete
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:14
     * Desc: This is the code comment. This method is called workloadDelete.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $demandID
     * @param $consumedID
     * @return array
     */
    public function workloadDelete($demandID, $consumedID)
    {
        $actions = $this->dao->select('*')->from(TABLE_ACTION)
            ->where('objectType')->eq('demand')
            ->andWhere('objectID')->eq($demandID)
            ->andWhere('action')->eq('deal')
            ->orderBy('id_asc')
            ->fetchAll();

        $consumeds = $this->getConsumedList($demandID);

        /* Judge whether the current work record is the last one. */
        $total = count($consumeds) - 1;
        $isLast = false;
        $previousID = 0;
        foreach ($consumeds as $index => $cs) {
            if ($cs->id == $consumedID) {
                $isLast = $index == $total ? true : false;
                $previousID = $consumeds[$total - 1]->id; //上一条
            }
        }

        if ($isLast and $previousID) {
            $consumed = $this->getConsumedByID($previousID); //获得上一条的工作量信息
            $this->dao->update(TABLE_DEMAND)->set('status')->eq($consumed->after)->where('id')->eq($demandID)->exec(); //只是修改了下一个处理状态
        }

        /* Get the corresponding relationship between work record and operation record. */
        $actionID = 0;
        array_splice($consumeds, 0, 1); // Remove the first work record.

        foreach ($consumeds as $index => $cs) {
            if ($cs->id == $consumedID) $actionID = $actions[$index]->id;
        }

        if ($actionID) $this->dao->delete()->from(TABLE_ACTION)->where('id')->eq($actionID)->exec();

        /* 逻辑删除 */
        $this->dao->update(TABLE_CONSUMED)->set('deleted')->eq(1)->where('id')->eq($consumedID)->exec(); //逻辑删除
        /* 删除相关配合人员记录 */
        $this->dao->update(TABLE_CONSUMED)->set('deleted')->eq(1)->where('parentID')->eq($consumedID)->exec(); //删除相关配合人员记录

        return array();
    }

    /**
     * Project: chengfangjinke
     * Method: workloadEdit
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:14
     * Desc: This is the code comment. This method is called workloadEdit.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $demandID
     * @param $consumedID
     * @return array
     */
    public function workloadEdit($demandID, $consumedID)
    {
        //返回信息
        $res = array();
        //检查时间信息
        $consumedTime = $this->post->consumed;
        if($consumedTime != '0'){
            $checkRes = $this->loadModel('consumed')->checkConsumedInfo($consumedTime);
            if (!$checkRes) {
                return dao::$errors;
            }
        }
        //检查关配合人员工作量信息
        $checkRes = $this->loadModel('consumed')->checkPostDetails();
        if (!$checkRes) {
            return dao::$errors;
        }

        //工作量节点信息
        $consumed = fixer::input('post')->remove('comment, relevantUser, workload, dealUser')->get();
        //2022-4-21 更新解决时间
        if ($consumed->after == 'closed' || $consumed->after == 'delivery') {
            $this->dao->update(TABLE_DEMAND)->set('solvedTime')->eq(date('Y-m-d H:i:s'))->where('id')->eq($demandID)->exec();
        }
        /* Judge whether the current work record is the last one. */
        $isLast = $this->loadModel('consumed')->checkIsLastConsumed($consumedID, $demandID, 'demand');
        if ($isLast) {
            //最后一个节点时没有设置处理人
            $dealUser = $this->post->dealUser;
            if (!$dealUser) {
                $errors['dealUser'] = sprintf($this->lang->demand->emptyObject, $this->lang->demand->dealUser);
                return dao::$errors = $errors;
            }
        }


        $consumed->details = $this->loadModel('consumed')->getPostDetails();
        //检查信息
        $this->dao->update(TABLE_CONSUMED)->data($consumed)->autoCheck() //更新工作量
        ->batchCheck($this->config->demand->workloadedit->requiredFields, 'notempty')
            ->where('id')->eq($consumedID)
            ->exec();

        $actions = $this->dao->select('*')->from(TABLE_ACTION)
            ->where('objectType')->eq('demand')
            ->andWhere('objectID')->eq($demandID)
            ->andWhere('action')->eq('deal')
            ->orderBy('id_asc')
            ->fetchAll();
        $consumeds = $this->getConsumedList($demandID);

        //最后一个工作量节点修改需求单的待处理状态和待处理人
        if ($isLast) {
            $oldDemand = $this->getByID($demandID);
            if (($oldDemand->status != $consumed->after) || ($oldDemand->dealUser != $dealUser)) {
                $this->dao->update(TABLE_DEMAND)->set('status')->eq($consumed->after)->set('dealUser')->eq($dealUser)->where('id')->eq($demandID)->exec();

                $data = new stdClass();
                $data->status = $consumed->after;
                $data->dealUser = $dealUser;
                $res = common::createChanges($oldDemand, $data);
            }
        }

        /* Get the corresponding relationship between work record and operation record. */
        $actionID = 0;
        array_splice($consumeds, 0, 1); // Remove the first work record.

        foreach ($consumeds as $index => $cs) {
            if ($cs->id == $consumedID) $actionID = $actions[$index]->id;
        }

        if ($actionID) {
            $this->dao->update(TABLE_ACTION)->set('actor')->eq($consumed->account)->where('id')->eq($actionID)->exec();
        }

        /* 处理相关配合人员的记录（增删改） */
        $this->loadModel('consumed')->dealRelevantUser($consumedID);

        return $res;
    }

    /**
     * Project: chengfangjinke
     * Method: getByID
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:14
     * Desc: This is the code comment. This method is called getByID.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $demandID
     * @return mixed
     */
    public function getByID($demandID, $showFile = false)
    {
        $demand = $this->dao->select("*")->from(TABLE_DEMAND)->where('id')->eq($demandID)->fetch();
        $demand = $this->loadModel('file')->replaceImgURL($demand, 'desc,reason,progress,solution,conclusion,plateMakAp,plateMakInfo,comment');
        $demand = $this->getConsumed($demand);
        if ($showFile) $demand->files = $this->loadModel('file')->getByObject('demand', $demand->id);
        return $demand;
    }

    public function getByIdList($demandIdList, $isPairs = false)
    {
        if (empty($demandIdList)) return array();

        $demands = $this->dao->select("*")->from(TABLE_DEMAND)->where('id')->in($demandIdList)->fetchAll();
        if ($isPairs) {
            $pairs = array();
            foreach ($demands as $demand) {
                $pairs[$demand->id] = $demand->code;
            }
            $demands = $pairs;
        }
        return $demands;
    }
    
    public function getByIdListNew($demandIdList)
    {
        if (empty($demandIdList)) return array();

        $demands = $this->dao->select("id,code,sourceDemand,acceptUser")->from(TABLE_DEMAND)->where('id')->in($demandIdList)->fetchAll();
        return $demands;
    }

    /* 获取工时投入信息和制版次数。*/
    public function getConsumed($demand)
    {
        if (empty($demand)) return array();

        $cs = $this->dao->select('*')->from(TABLE_CONSUMED)->where('objectType')->eq('demand')
            ->andWhere('objectID')->eq($demand->id)
            ->andWhere('parentID')->eq('0')
            ->andWhere('deleted')->eq(0)
            ->fetchAll();
        $demand->buildTimes = 0;
        foreach ($cs as $c) {
            if ($c->after === 'build') $demand->buildTimes++;
        }
        $demand->consumed = $cs;

        $dc = $this->dao->select('*')->from(TABLE_CONSUMED)->where('objectType')->eq('demandDelay')
            ->andWhere('objectID')->eq($demand->id)
            ->andWhere('parentID')->eq('0')
            ->andWhere('deleted')->eq(0)
            ->fetchAll();
        $demand->delayConsumed = $dc;
        return $demand;
    }

    /* 获取制版次数。*/
    public function getBuild($demandID)
    {
        $buildTotal = $this->dao->select('count(*) as total')->from(TABLE_CONSUMED)
            ->where('objectType')->eq('demand')
            ->andWhere('objectID')->eq($demandID)
            ->andWhere('after')->eq('build')
            ->fetch('total');
        return empty($buildTotal) ? 0 : $buildTotal;
    }

    /**
     * Project: chengfangjinke
     * Method: feedback
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:14
     * Desc: This is the code comment. This method is called feedback.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $demandID
     * @return array
     */
    public function feedback($demandID)
    {
        $oldProblem = $this->getByID($demandID);

        $data = fixer::input('post')->stripTags($this->config->demand->editor->feedback['id'], $this->config->allowedTags)->get();
        $data->status = 'feedbacked';
        $this->dao->update(TABLE_DEMAND)->data($data)->where('id')->eq($demandID)->exec();

        return common::createChanges($oldProblem, $data);
    }

    //自动更改状态
//    public function changeStatus()
//    {
//        $this->changeBySecondLine();
//    }

    /**
     * 7.3.3.1
     * 如果生产变更单、数据修正、数据获取单状态为“待上线”，则自动回填关联的需求条目或问题单的状态为“待上线”，待处理人置空
     * 如果生产变更单、数据修正、数据获取单状态为上线成功类的状态，则自动回填关联的需求条目或问题单的状态为“上线成功”，待处理人置空
     * 如果生产变更单、数据修正、数据获取单状态为上线失败类的状态，则自动回填关联的需求条目或问题单的状态为“上线失败”，待处理人置空
     */
    public function changeBySecondLine()
    {
        $demands = $this->dao->select('id, status, actualOnlineDate')
            ->from(TABLE_DEMAND)
            ->where('status')
            ->notIN("closed,deleted") //onlinesuccess, delivery,
            ->andWhere('createdDate')
            ->gt(date('Y-m-d', strtotime("-1 year")))
            ->fetchAll('id');
        $demandIds = array_keys($demands);
        $deliveryIdList = [];
        $onlineIdList = [];
        $onlineFailIdList = [];
        foreach ($demandIds as $demandId) {
            //取本单最后一个二线关联
            $relation = $this->dao->select('relationID as last_relation_id, relationType')
                ->from(TABLE_SECONDLINE)
                ->where('objectType')
                ->eq('demand')
                ->andwhere('objectID')
                ->eq($demandId)
                ->andwhere('deleted') //选非删除的二线关联
                ->eq(0)
                ->andwhere('relationType')
                ->in('fix,gain,gainQz,modify,modifycncc')
                ->orderBY("id_desc")
                ->fetch();
            if (empty($relation)) continue;
            if ($relation->relationType == 'fix') { //如果是数据修正
                $info = $this->dao->select('status, actualEnd')
                    ->from(TABLE_INFO)
                    ->where('id')
                    ->eq($relation->last_relation_id)
                    ->andwhere('action')
                    ->eq('fix')
                    ->fetch();
                if (empty($info)) continue;
                if ($info->status == 'productsuccess') {  //productsuccess = 待上线;
                    if ($demands[$demandId]->status != 'delivery') {
                        $deliveryIdList[] = $demandId;      //delivery = 待上线
                        $this->loadModel('consumed')->recordAuto('demand', $demandId, 0, $demands[$demandId]->status, 'delivery');
                    }
                } elseif ($info->status == 'closing') {
                    if ($demands[$demandId]->status != 'onlinesuccess') {
                        $onlineIdList[] = $demandId;
                        $this->loadModel('consumed')->recordAuto('demand', $demandId, 0, $demands[$demandId]->status, 'onlinesuccess');
                    }
                    if ($demands[$demandId]->actualOnlineDate < substr($info->actualEnd, 0, 10)) $this->setActEndTime($info->actualEnd, $demandId);
                }
            } elseif ($relation->relationType == 'gain') {
                $info = $this->dao->select('status, actualEnd')
                    ->from(TABLE_INFO)
                    ->where('id')
                    ->eq($relation->last_relation_id)
                    ->andwhere('action')
                    ->eq('gain')
                    ->fetch();
                if (empty($info)) continue;
                if ($info->status == 'productsuccess') {
                    if ($demands[$demandId]->status != 'delivery') {
                        $deliveryIdList[] = $demandId;      //delivery = 待上线
                        $this->loadModel('consumed')->recordAuto('demand', $demandId, 0, $demands[$demandId]->status, 'delivery');
                    }
                }
                if ($info->status == 'fetchsuccess') {
                    if ($demands[$demandId]->status != 'onlinesuccess') {
                        $onlineIdList[] = $demandId;
                        $this->loadModel('consumed')->recordAuto('demand', $demandId, 0, $demands[$demandId]->status, 'onlinesuccess');
                    }
                    if ($demands[$demandId]->actualOnlineDate < substr($info->actualEnd, 0, 10)) $this->setActEndTime($info->actualEnd, $demandId);
                }
                if ($info->status == 'fetchfail') {
                    if ($demands[$demandId]->status != 'onlinefailed') {
                        $onlineFailIdList[] = $demandId;
                        $this->loadModel('consumed')->recordAuto('demand', $demandId, 0, $demands[$demandId]->status, 'onlinefailed');
                    }
                    if ($demands[$demandId]->actualOnlineDate < substr($info->actualEnd, 0, 10)) $this->setActEndTime($info->actualEnd, $demandId);
                }
            } elseif ($relation->relationType == 'gainQz') {
                $info = $this->dao->select('status, externalStatus, actualEnd')
                    ->from(TABLE_INFO_QZ)
                    ->where('id')
                    ->eq($relation->last_relation_id)
                    ->andwhere('action')
                    ->eq('gain')
                    ->fetch();
                if (empty($info)) continue;
                if ($info->status == 'pass') {
                    if ($demands[$demandId]->status != 'delivery') {
                        $deliveryIdList[] = $demandId;      //delivery = 待上线
                        $this->loadModel('consumed')->recordAuto('demand', $demandId, 0, $demands[$demandId]->status, 'delivery');
                    }
                }
                $this->loadModel('infoqz'); //if ($info->status == 'closing') {
                if ($info->externalStatus == $this->lang->infoqz->externalStatusSuccess) {
                    if ($demands[$demandId]->status != 'onlinesuccess') {
                        $onlineIdList[] = $demandId;
                        $this->loadModel('consumed')->recordAuto('demand', $demandId, 0, $demands[$demandId]->status, 'onlinesuccess');
                    }
                    if ($demands[$demandId]->actualOnlineDate < substr($info->actualEnd, 0, 10)) $this->setActEndTime($info->actualEnd, $demandId);
                } elseif ($info->externalStatus == $this->lang->infoqz->externalStatusfailed) {
                    if ($demands[$demandId]->status != 'onlinefailed') {
                        $onlineFailIdList[] = $demandId;
                        $this->loadModel('consumed')->recordAuto('demand', $demandId, 0, $demands[$demandId]->status, 'onlinefailed');
                    }
//                    if ($demands[$demandId]->actualOnlineDate < substr($info->actualEnd, 0, 10)) $this->setActEndTime($info->actualEnd, $demandId);
                }
            } elseif ($relation->relationType == 'modify') {
                $info = $this->dao->select('status, actualEnd, realEndTime')
                    ->from(TABLE_MODIFY)
                    ->where('id')
                    ->eq($relation->last_relation_id)
                    ->fetch();
                if ($info->status == 'withexternalapproval') { //bug 19537
                    if ($demands[$demandId]->status != 'delivery') {
                        $deliveryIdList[] = $demandId;      //delivery = 待上线
                        $this->loadModel('consumed')->recordAuto('demand', $demandId, 0, $demands[$demandId]->status, 'delivery');
                    }
                } elseif ($info->status == 'modifysuccess') { //bug 19537
                    if ($demands[$demandId]->status != 'onlinesuccess') {
                        $onlineIdList[] = $demandId;
                        $this->loadModel('consumed')->recordAuto('demand', $demandId, 0, $demands[$demandId]->status, 'onlinesuccess');
                    }
                    if ($demands[$demandId]->actualOnlineDate < substr($info->realEndTime, 0, 10)) $this->setActEndTime($info->realEndTime, $demandId);
                }
            } elseif ($relation->relationType == 'modifycncc') {
                $info = $this->dao->select('status, actualEnd')
                    ->from(TABLE_MODIFYCNCC)
                    ->where('id')
                    ->eq($relation->last_relation_id)
                    ->fetch();
                if ($info->status == 'withexternalapproval') { //待提交
                    if ($demands[$demandId]->status != 'delivery') {
                        $deliveryIdList[] = $demandId;      //delivery = 待上线
                        $this->loadModel('consumed')->recordAuto('demand', $demandId, 0, $demands[$demandId]->status, 'delivery');
                    }
                } elseif ($info->status == 'modifysuccess') {
                    if ($demands[$demandId]->status != 'onlinesuccess') {
                        $onlineIdList[] = $demandId;
                        $this->loadModel('consumed')->recordAuto('demand', $demandId, 0, $demands[$demandId]->status, 'onlinesuccess');
                    }
                    if ($demands[$demandId]->actualOnlineDate < substr($info->actualEnd, 0, 10)) $this->setActEndTime($info->actualEnd, $demandId);
                } elseif ($info->status == 'modifyfail') {
                    if ($demands[$demandId]->status != 'onlinefailed') {
                        $onlineFailIdList[] = $demandId;
                        $this->loadModel('consumed')->recordAuto('demand', $demandId, 0, $demands[$demandId]->status, 'onlinefailed');
                    }
                    if ($demands[$demandId]->actualOnlineDate < substr($info->actualEnd, 0, 10)) $this->setActEndTime($info->actualEnd, $demandId);
                }
            }
        }

        if ($deliveryIdList) {
            $this->dao->update(TABLE_DEMAND)->set('status')->eq('delivery')->where('id')->in($deliveryIdList)->exec();
            $this->setNoReview($deliveryIdList);
            $this->loadModel('action')->createActions('demand', $deliveryIdList, 'delivery');
        }
        if ($onlineIdList) {
            $onlineDate = date('Y-m-d');
            $this->dao->update(TABLE_DEMAND)->set('status')->eq('onlinesuccess')->set('onlineDate')->eq($onlineDate)->where('id')->in($onlineIdList)->exec();
            $this->setNoReview($onlineIdList);
            $this->loadModel('action')->createActions('demand', $onlineIdList, 'onlinesuccess');
        }
        if ($onlineFailIdList) {
            $this->dao->update(TABLE_DEMAND)->set('status')->eq('onlinefailed')->where('id')->in($onlineFailIdList)->exec();
            $this->loadModel('action')->createActions('demand', $onlineFailIdList, 'onlinefailed');
            $this->setReviewers($onlineFailIdList);
        }

        return ['demandSecondLine onlinesuccess' => implode(',', $onlineIdList), 'demandSecondLine delivery' => implode(',', $deliveryIdList), 'demandSecondLine onlinefailed' => implode(',', $onlineFailIdList),];
    }

     /**
      * TongYanQi 2022/12/18
      * 升级版状态联动 所有关联状态一样才改状态
      */
    public function changeBySecondLineV2()
    {
        //取所有有效需求条目
        $demands = $this->dao->select('id, status, code, dealUser, actualOnlineDate')
            ->from(TABLE_DEMAND)
            ->where('status')->notIN("closed,deleted") //onlinesuccess, delivery,
            ->andWhere('createdDate')->gt(date('Y-m-d', strtotime("-1 year")))
//            ->andwhere('id')->eq(1132)
            ->fetchAll('id');
        $demandIds = array_keys($demands);
        $releaseIdList = [];  //已发布 数组
        $deliveryIdList = []; //已交付
        $onlineIdList = [];   //已上线
        $onlineTimeList = []; //上线时间
        $testingIdList = [];  //测试中

        foreach ($demandIds as $demandId) {
            $statusList[$demandId]['release'] = 0;
            $statusList[$demandId]['delivery'] = 0;
            $statusList[$demandId]['online'] = 0;
            $statusList[$demandId]['testing'] = 0;
            $dealUserList[$demandId]['testing']    = ""; //只有测试中更新处理人
            //获取该需求条目关联的所有二线单
            $relations = $this->dao->select('relationID as last_relation_id, relationType')
                ->from(TABLE_SECONDLINE)
                ->where('objectType')->eq('demand')
                ->andwhere('objectID')->eq($demandId)
                ->andwhere('deleted')->eq(0)
                ->andwhere('relationType')->in('fix,gain,gainQz,modify,outwardDelivery')
                ->orderBY("id_desc")
                ->fetchAll();
            //取所有二线
            foreach ($relations as $relation){

                if (empty($relation)) continue;
                if (in_array($relation->relationType, ['fix','gain'])) { //如果是金信数据修正 或者获取
                    $info = $this->dao->select('status, actualEnd')
                        ->from(TABLE_INFO)
                        ->where('id')->eq($relation->last_relation_id)
                        ->andwhere('action')->in(['fix','gain'])
                        ->fetch();
                    if (empty($info)) continue;
                    if (in_array($info->status, ['productsuccess','fetchfail'])) {
                        $statusList[$demandId]['delivery'] ++;
                    }
                    if ($info->status == 'fetchsuccess') {
                        $statusList[$demandId]['online'] ++;
                        if(empty($onlineTimeList[$demandId]) || $info->actualEnd  > $onlineTimeList[$demandId]) { $onlineTimeList[$demandId] = $info->actualEnd; }
                    }
                }
                elseif ($relation->relationType == 'modify') { //如果是金信生产变更

                    $info = $this->dao->select('id, status, actualEnd, realEndTime')
                        ->from(TABLE_MODIFY)
                        ->where('id')
                        ->eq($relation->last_relation_id)
                        ->fetch();
                    if (empty($info)) continue;
                    if (in_array($info->status,['withexternalapproval','waitqingzong','jxsynfailed','modifysuccesspart','modifyfail','modifyrollback','modifyreject','modifyerror','waitImplement'])) {
                        $statusList[$demandId]['delivery'] ++;
                    }
                    if ($info->status == 'modifysuccess') {
                        $statusList[$demandId]['online'] ++;
                        $realEndTime = substr($info->realEndTime, 0, 10);
                        if(empty($onlineTimeList[$demandId]) || $realEndTime  > $onlineTimeList[$demandId]) { $onlineTimeList[$demandId] = $realEndTime; }
                    }
                }
                elseif ($relation->relationType == 'gainQz') {  //清总数据获取
                    $info = $this->dao->select('status, externalStatus, actualEnd')
                        ->from(TABLE_INFO_QZ)
                        ->where('id')
                        ->eq($relation->last_relation_id)
                        ->andwhere('action')
                        ->eq('gain')
                        ->fetch();
                    if (empty($info)) continue;
                    if (in_array($info->status,['withexternalapproval','pass','qingzongsynfailed','fetchsuccesspart','fetchfail','outreject'])) {
                        $statusList[$demandId]['delivery'] ++;
                    }
                    if ($info->status == 'fetchsuccess') {
                        $statusList[$demandId]['online'] ++;
                        if(empty($onlineTimeList[$demandId]) || $info->actualEnd  > $onlineTimeList[$demandId]) { $onlineTimeList[$demandId] = $info->actualEnd; }
                    }

                }
                elseif (strtolower($relation->relationType) == 'outwarddelivery') { //清总对外交付

                    $info = $this->dao->select('status,closed,productEnrollId,testingRequestId,modifycnccId')
                        ->from(TABLE_OUTWARDDELIVERY)
                        ->where('id')
                        ->eq($relation->last_relation_id)
                        ->fetch();

                    if (empty($info)) continue;
                    if ($info->closed){  //已关闭 略过
                        continue;
                    }
                    if($info->modifycnccId) {
                        if (in_array($info->status, ['withexternalapproval', 'qingzongsynfailed', 'testingrequestreject', 'testingrequestpass',
                            'productenrollpass', 'productenrollreject', 'modifysuccesspart', 'modifyfail', 'modifyreject'])) {
                            $statusList[$demandId]['delivery']++;
                        }
                        if ($info->status == 'modifysuccess') {
                            $statusList[$demandId]['online']++;
                            $lastDealDate = $this->dao->select('actualEnd')->from(TABLE_MODIFYCNCC)->where('id')->eq($info->modifycnccId)->fetch('actualEnd');
                            if(empty($onlineTimeList[$demandId]) || $lastDealDate  > $onlineTimeList[$demandId]) { $onlineTimeList[$demandId] = $lastDealDate; }
                        }
                    }

                }


            }

            //所有相关制版
           $builds = $this->dao->select('t.id,t.`name`,t.`status`,t.dealuser')
               ->from(TABLE_BUILD)->alias('t')
               ->where('t.demandid')->like("%{$demands[$demandId]->code}%")
               ->andWhere("id =(select max(id) from zt_build where project = t.project and app = t.app and product = t.product and version = t.version and demandid like '%{$demands[$demandId]->code}%')")
               ->andwhere('t.deleted')->eq(0)
               ->groupBy('t.project,t.app,t.product,t.version')
               ->fetchAll('id');
            foreach ($builds as $build) {
                if($build->status == 'released') {
                    $statusList[$demandId]['release'] ++;
                } else {
                    $statusList[$demandId]['testing'] ++;
                    if(empty($dealUserList[$demandId]['testing'])) {
                        $dealUserList[$demandId]['testing']    =  $build->dealuser;
                    }
                    else {
                        if($build->dealuser) $dealUserList[$demandId]['testing']    .=  ','.$build->dealuser; //只有测试中更新处理人
                    }

                }
            }
            //判断是否取状态更新
            if ($statusList[$demandId]['testing'] > 0) {
                $nowDealUsers = $this->getNowDealUserString($dealUserList[$demandId]['testing']);
//                if($demands[$demandId]->status != 'testing' && $demands[$demandId]->dealUser != $nowDealUsers){ //如果状态和待处理人都没变化
                if(!in_array($demands[$demandId]->status,['wait','build']) && $demands[$demandId]->dealUser != $nowDealUsers){ //如果状态和待处理人都没变化
                    $this->dao->update(TABLE_DEMAND)->set('status')->eq('build')->set('dealUser')->eq($nowDealUsers)->set('actualOnlineDate')->eq(null)->where('id')->eq($demandId)->exec();
                    $testingIdList[$demandId] = $demandId;
                    $this->loadModel('consumed')->recordAuto('demand', $demandId, 0, $demands[$demandId]->status, 'build');
                }
                continue;
            }
//            echo json_encode($statusList[$demandId]);
            //如果当前状态不是已发布 && 存在已发布状态 改为已发布状态
            if($statusList[$demandId]['release'] > 0  && $statusList[$demandId]['delivery'] == 0 && $statusList[$demandId]['online'] == 0 ) {
                    if($demands[$demandId]->status != 'released' || $demands[$demandId]->dealUser != ""){ //已发布 需要处理人置空
                        if($demands[$demandId]->status != 'wait'){
                        $releaseIdList[$demandId] = $demandId;
                            $this->loadModel('consumed')->recordAuto('demand', $demandId, 0, $demands[$demandId]->status, 'released');
                        }
                    }
                    continue;

            }
            //如果当前状态不是已交付 && 本需求条目无其他关联状态 改为已交付状态
            if($statusList[$demandId]['delivery'] > 0){
                if($demands[$demandId]->status != 'delivery') {
                    $deliveryIdList[$demandId] = $demandId;
                    $this->loadModel('consumed')->recordAuto('demand', $demandId, 0, $demands[$demandId]->status, 'delivery');
                }
                continue;
            }
            //如果当前状态不是已上线 && 本需求条目无其他关联状态 改为上线状态
            if($statusList[$demandId]['online'] > 0){
                if($demands[$demandId]->status != 'onlinesuccess') {
                    $onlineIdList[$demandId] = $demandId;
                    $this->loadModel('consumed')->recordAuto('demand', $demandId, 0, $demands[$demandId]->status, 'onlinesuccess');
                    $this->dao->update(TABLE_DEMAND)->set('status')->eq('onlinesuccess')->set('actualOnlineDate')->eq($onlineTimeList[$demandId])->set('dealUser')->eq('')->where('id')->eq($demandId)->exec();
//                  $this->setActEndTime($onlineTimeList[$demandId], $demandId);
                }
                continue;
            }

        }
//        echo json_encode(['releaseIdList' => $releaseIdList, 'deliveryIdList' => $deliveryIdList, 'onlineIdList' => $onlineIdList]);

        if ($testingIdList) { //在上面单独更新了
//            $this->dao->update(TABLE_DEMAND)->set('status')->eq('build')->set('actualOnlineDate')->eq(null)->where('id')->in($testingIdList)->exec();
            $this->loadModel('action')->createActions('demand', $testingIdList, 'testing');
        }
        if ($releaseIdList) {
            $this->dao->update(TABLE_DEMAND)->set('status')->eq('released')->set('actualOnlineDate')->eq(null)->set('dealUser')->eq('')->where('id')->in($releaseIdList)->exec();
            $this->loadModel('action')->createActions('demand', $releaseIdList, 'released');
        }
        if ($deliveryIdList) {
            $this->dao->update(TABLE_DEMAND)->set('status')->eq('delivery')->set('dealUser')->eq('')->set('actualOnlineDate')->eq(null)->where('id')->in($deliveryIdList)->exec();
            $this->loadModel('action')->createActions('demand', $deliveryIdList, 'delivery');
        }
        if ($onlineIdList) { //在上面单独更新了
//            $onlineDate = date('Y-m-d');
//            $this->dao->update(TABLE_DEMAND)->set('status')->eq('onlinesuccess')->set('onlineDate')->eq($onlineDate)->set('dealUser')->eq('')->where('id')->in($onlineIdList)->exec();
            $this->loadModel('action')->createActions('demand', $onlineIdList, 'onlinesuccess');
        }
        return ['demand onlinesuccess' =>  implode(',', $onlineIdList), 'demand delivery' =>  implode(',', $deliveryIdList),'demand released' =>  implode(',', $releaseIdList), 'demand testing' =>  implode(',', $testingIdList),];

    }

    /**
     * @Notes: 迭代二十五状态联动更新版
     * @Date: 2023/4/14
     * @Time: 9:47
     * @Interface changeBySecondLineV3
     * @return array
     */
    public function changeBySecondLineV3()
    {
        //取所有有效需求条目  只查询内部需求条目进行联动
        $demands = $this->getEffectiveDemandInfo(2);
        $demandIds = array_keys($demands);
        $testingIdList = [];  //测试中
        $feedbackedIdList = [];  //开发中
        $releaseIdList = [];  //已发布
        $deliveryIdList = []; //已交付
        $onlineIdList = [];   //已上线
        $onlineTimeList = []; //上线时间
        $demandLang = $this->lang->demand->statusArr;
        foreach ($demandIds as $demandId) {
            $statusList[$demandId]['testing'] = 0; //测试中 关联数量
            $statusList[$demandId]['feedbacked'] = 0; //开发中 关联数量
            $statusList[$demandId]['releaseSecond'] = 0;  //二线的已发布 关联数量
            $statusList[$demandId]['releaseBuild'] = 0;  //制版的已发布 关联数量
            $statusList[$demandId]['delivery'] = 0; //已交付 关联数量
            $statusList[$demandId]['onlinesuccess'] = 0; //上次成功 关联数量

            /**
             * 获取该需求条目关联的所有二线单
             * @var secondLineModel $secondLineModel
             * @var infoModel $infoModel
             * @var modifyModel $modifyModel
             * @var infoQzModel $infoQzModel
             * @var outwardDeliveryModel $outwardDeliveryModel
             */
            $secondLineModel = $this->loadModel('secondline');
            $infoModel = $this->loadModel('info');
            $modifyModel = $this->loadModel('modify');
            $infoQzModel = $this->loadModel('infoqz');
            $outwardDeliveryModel = $this->loadModel('outwarddelivery');
            $relations = $secondLineModel->getEffectiveSecondLineInfo($demandId);
            //取所有二线
            foreach ($relations as $relation){
                if (empty($relation)) continue;
                #region 二线金信
                if (in_array($relation->relationType, ['gain']))
                {
                    $info = $infoModel->getEffectiveInfoData($relation->last_relation_id); //金信数据获取
                    if (empty($info)) continue;
                    //待关联版本 已退回 待组长审批 待本部门审批 待系统部审批 待分管领导审批 待总经理审批 待产创部审核 联动为<已发布>
                    if (in_array($info->status, $demandLang['releaseGainType']))
                    {
                        $statusList[$demandId]['releaseSecond']++;
                        $releaseIdList[$demandId]['codeSecond'] = $info->code."($info->id)";
                    }
                    //待上线 获取失败 联动为<已交付>
                    if (in_array($info->status, $demandLang['deliveryGainType']))
                    {
                        $statusList[$demandId]['delivery']++;
                        $deliveryIdList[$demandId]['codeSecond'] = $info->code."($info->id)";
                    }
                    //获取成功 联动为上线成功
                    if ($info->status == 'fetchsuccess')
                    {
                        $statusList[$demandId]['onlinesuccess']++;
                        $onlineIdList[$demandId]['codeSecond'] = $info->code."($info->id)";
                        //取最新上线时间 迭代二十五 数据获取不取上线时间 预留防止需求变化
//                        if(empty($onlineTimeList[$demandId]) || $info->actualEnd  > $onlineTimeList[$demandId])
//                        {
//                            $onlineTimeList[$demandId] = $info->actualEnd;
//                        }
                    }
                }
                elseif ($relation->relationType == 'modify') { //金信生产变更
                    $info = $modifyModel->getEffectiveModifyData($relation->last_relation_id);
                    if (empty($info)) continue;
                    //待同步金信 同步金信失败 待关联版本 已退回 待组长审批 待本部门审批 待系统部审批 待分管领导审批 待总经理审批 待产创部审核 联动为<已发布>
                    if (in_array($info->status, $demandLang['releaseModifyType']))
                    {
                        $statusList[$demandId]['releaseSecond']++;
                        $releaseIdList[$demandId]['codeSecond'] = $info->code."($info->id)";
                    }
                    //待外部审批 部分成功 变更失败 变更退回 变更回退 变更异常 待变更实施 待上线、待关闭、受理人受理变更并审核、生产调度部变更经理排期并提交实施、取消变更同步金信失败、已取消、取消退回、取消待同步金信、取消成功、取消待审批 联动为<已交付>
                    if (in_array($info->status, $demandLang['deliveryModifyType']))
                    {
                        $statusList[$demandId]['delivery']++;
                        $deliveryIdList[$demandId]['codeSecond'] = $info->code."($info->id)";
                    }
                    //变更成功 联动为上线成功
                    if (in_array($info->status,['modifysuccess','closed']))
                    {
                        $statusList[$demandId]['onlinesuccess']++;
                        $onlineIdList[$demandId]['codeSecond'] = $info->code."($info->id)";
                        if(empty($onlineTimeList[$demandId]) || $info->realEndTime  > $onlineTimeList[$demandId])
                        {
                            $onlineTimeList[$demandId] = $info->realEndTime;
                        }
                    }
                }
                #endregion
                #region 二线清总
                elseif ($relation->relationType == 'gainQz') {  //清总数据获取
                    //已关闭、数据获取关闭、数据获取取消 不在联动范围内
                    $info = $infoQzModel->getEffectiveInfoQzData($relation->last_relation_id);
                    if (empty($info)) continue;
                    //待关联版本 已退回 待组长审批 待本部门审批 待系统部审批 待分管领导审批 待产创部审核 待同步清总 同步清总失败 联动为<已发布>
                    if (in_array($info->status, $demandLang['releaseQzType']))
                    {
                        $statusList[$demandId]['releaseSecond']++;
                        $releaseIdList[$demandId]['codeSecond'] = $info->code."($info->id)";
                    }

                    //待外部审批 获取部分成功 数据获取失败 数据获取退回联动为<已交付>
                    if (in_array($info->status, $demandLang['deliveryQzType']))
                    {
                        $statusList[$demandId]['delivery']++;
                        $deliveryIdList[$demandId]['codeSecond'] = $info->code."($info->id)";
                    }

                    //数据获取成功 联动为上线成功
                    if ($info->status == 'fetchsuccess')
                    {
                        $statusList[$demandId]['onlinesuccess']++;
                        $onlineIdList[$demandId]['codeSecond'] = $info->code."($info->id)";
                        //取最新上线时间
//                        if(empty($onlineTimeList[$demandId]) || $info->actualEnd  > $onlineTimeList[$demandId]) {
//                            $onlineTimeList[$demandId] = $info->actualEnd;
//                        }
                    }
                }
                elseif (strtolower($relation->relationType) == 'outwarddelivery') { //清总对外交付
                    //待提交、已关闭、变更取消 不在联动范围内
                    $info = $outwardDeliveryModel->getEffectiveOutwardDeliveryQzData($relation->last_relation_id);
                    if (empty($info)) continue;
                    if ($info->closed) continue; //如果已关闭 忽略该条
                    if ($info->modifycnccId > 0) { //对外交付只处理生产变更
                        //待关联版本 已退回 待组长审批 待本部门审批 待系统部审批 待分管领导审批 待总经理审批 待产创部审核 待同步清总 同步清总失败 联动为<已发布>
                        if (in_array($info->status,$demandLang['releaseOutwarddeliveryType']))
                        {
                            $statusList[$demandId]['releaseSecond']++;
                            $releaseIdList[$demandId]['codeSecond'] = $info->code."($info->id)";
                        }

                        //待外部审批 总中心产品经理审批 基准实验室审核 gitee审核通过 部分成功 变更失败 变更退回  联动为<已交付>
                        if (in_array($info->status, $demandLang['deliveryOutwarddeliveryType']))
                        {
                            $statusList[$demandId]['delivery']++;
                            $deliveryIdList[$demandId]['codeSecond'] = $info->code."($info->id)";
                        }

                        //变更成功 联动为上线成功
                        if ($info->status == 'modifysuccess')
                        {
                            $statusList[$demandId]['onlinesuccess']++;
                            $onlineIdList[$demandId]['codeSecond'] = $info->code."($info->id)";
                            //取最新上线时间
                            $lastDealDate = $this->dao->select('actualEnd')->from(TABLE_MODIFYCNCC)->where('id')->eq($info->modifycnccId)->fetch('actualEnd');
                            if(empty($onlineTimeList[$demandId]) || $lastDealDate  > $onlineTimeList[$demandId])
                            {
                                $onlineTimeList[$demandId] = $lastDealDate;
                            }
                        }
                    }
                }
                #endregion
            }
            #region 关联制版状态联动
            //取所有关联的任务制版
            $builds = $this->dao->select('t.id,t.`name`,t.`status`,t.dealuser')
                ->from(TABLE_BUILD)->alias('t')
                ->where('t.demandid')->like("%{$demands[$demandId]->code}%")
                ->andwhere('t.`status`')->ne('wait')
                ->andWhere("id in (select max(id) from zt_build where project = t.project and app = t.app and product = t.product and version = t.version and demandid like '%{$demands[$demandId]->code}%'  and  deleted = '0' group by taskid)")
                ->andwhere('t.deleted')->eq(0)
                ->fetchAll('id');
            if($builds)
            {
                foreach ($builds as $build) {
                    if ($build->status == 'released') {
                        $statusList[$demandId]['releaseBuild']++;
                        $releaseIdList[$demandId]['code'][] = $build->name."($build->id)";
                    } else {
                        $statusList[$demandId]['testing']++;
                        $testingIdList[$demandId]['code'][] = $build->name."($build->id)";

                    }
                }
            }
            else{
                $statusList[$demandId]['feedbacked'] ++;
                $feedbackedIdList[$demandId]['code'][] = "(无制版和二线)";
            }
            //重点：如果制版和二线同时存在 ,制版不是已发布 ，则取制版状态。反之，取二线状态
            if(($statusList[$demandId]['releaseBuild'] || $statusList[$demandId]['testing']) && ($statusList[$demandId]['releaseSecond'] ||$statusList[$demandId]['delivery']||$statusList[$demandId]['onlinesuccess']))
            {
                /*制版和二线同时存在*/
                //如果制版 有测试中的 状态就是测试(build)
                if ($statusList[$demandId]['testing'])
                {
                    //如果状态不是测试中 更新为测试中
                    if($demands[$demandId]->status != 'build' )
                    {
                        //为备注创建
                        $testingIdList = $this->createToArr($demandId,$testingIdList,$demands[$demandId]->status,'build');
                        $this->dao->update(TABLE_DEMAND)->set('status')->eq('build')->set('actualOnlineDate')->eq(null)->where('id')->eq($demandId)->exec();
                        $this->loadModel('consumed')->recordAuto('demand', $demandId, 0, $demands[$demandId]->status, 'build');
                    }
                    //清除不满足数组条件的
                    $clearArr =  $this->clearToArr($onlineIdList,$deliveryIdList,$releaseIdList,$testingIdList,$feedbackedIdList,$demandId);
                    $onlineIdList = $clearArr['online'];
                    $deliveryIdList  = $clearArr['delivery'];
                    $releaseIdList = $clearArr['release'];
                    $testingIdList = $clearArr['testing'];
                    $feedbackedIdList = $clearArr['feedbacked'];
                    continue;
                }
                //如果有制版发布 && 有二线已发布.则需求条目状态为 二线已发布
                if($statusList[$demandId]['releaseBuild'] && $statusList[$demandId]['releaseSecond'])
                {
                    if($demands[$demandId]->status != 'released')
                    {
                        unset($releaseIdList[$demandId]['code']);
                        $releaseIdList = $this->createToArr($demandId,$releaseIdList,$demands[$demandId]->status,'released');
                        $releaseIdList[$demandId]['code'][]  = isset($releaseIdList[$demandId]['codeSecond'] ) ? $releaseIdList[$demandId]['codeSecond'] :'';
                        $this->loadModel('consumed')->recordAuto('demand', $demandId, 0, $demands[$demandId]->status, 'released');
                    }
                    $clearArr =  $this->clearToArr($onlineIdList,$deliveryIdList,$releaseIdList,$testingIdList,$feedbackedIdList,$demandId);
                    $onlineIdList = $clearArr['online'];
                    $deliveryIdList  = $clearArr['delivery'];
                    $releaseIdList = $clearArr['release'];
                    $testingIdList = $clearArr['testing'];
                    $feedbackedIdList = $clearArr['feedbacked'];
                    continue;
                }
                //如果有发布,且所有制版状态都是已发布 &&没有二线已发布.有二线已交付 则需求条目状态为 已交付
                if($statusList[$demandId]['releaseBuild'] && !$statusList[$demandId]['releaseSecond'] && $statusList[$demandId]['delivery'])
                {
                    //如果状态不是已发布
                    if(count($builds) == $statusList[$demandId]['releaseBuild'] && count($builds) != 0 && $demands[$demandId]->status != 'delivery')
                    {
                        $deliveryIdList = $this->createToArr($demandId,$deliveryIdList,$demands[$demandId]->status,'delivery');
                        $deliveryIdList[$demandId]['code'][]  = isset($deliveryIdList[$demandId]['codeSecond'] ) ? $deliveryIdList[$demandId]['codeSecond'] :'';
                        $this->loadModel('consumed')->recordAuto('demand', $demandId, 0, $demands[$demandId]->status, 'delivery');
                    }
                    $clearArr =  $this->clearToArr($onlineIdList,$deliveryIdList,$releaseIdList,$testingIdList,$feedbackedIdList,$demandId);
                    $onlineIdList = $clearArr['online'];
                    $deliveryIdList  = $clearArr['delivery'];
                    $releaseIdList = $clearArr['release'];
                    $testingIdList = $clearArr['testing'];
                    $feedbackedIdList = $clearArr['feedbacked'];
                    continue;
                }
                //如果有发布,且所有制版状态都是已发布 &&没有二线已发布.有二线上线成功 则需求单状态为 上线成功
                if($statusList[$demandId]['releaseBuild'] && !$statusList[$demandId]['releaseSecond'] && $statusList[$demandId]['onlinesuccess'] )
                {
                    //如果状态不是已发布
                    if(count($builds) == $statusList[$demandId]['releaseBuild'] && count($builds) != 0 && $demands[$demandId]->status != 'onlinesuccess')
                    {
                        $onlineIdList = $this->createToArr($demandId,$onlineIdList,$demands[$demandId]->status,'onlinesuccess');
                        $onlineIdList[$demandId]['code'][]  = isset($onlineIdList[$demandId]['codeSecond'] ) ? $onlineIdList[$demandId]['codeSecond'] :'';
                        $this->dao->update(TABLE_DEMAND)->set('status')->eq('onlinesuccess')->set('actualOnlineDate')->eq($onlineTimeList[$demandId])->where('id')->eq($demandId)->exec();
                        $this->loadModel('consumed')->recordAuto('demand', $demandId, 0, $demands[$demandId]->status, 'onlinesuccess');
                    }
                    //防止原状态也为上线成功
                    if(!empty($onlineTimeList[$demandId]) && $onlineTimeList[$demandId] > $demands[$demandId]->actualOnlineDate){
                        $this->dao->update(TABLE_DEMAND)->set('actualOnlineDate')->eq($onlineTimeList[$demandId])->where('id')->eq($demandId)->exec();
                    }
//                    $this->dao->update(TABLE_DEMAND)->set('actualOnlineDate')->eq($onlineTimeList[$demandId])->where('id')->eq($demandId)->exec();
                    $clearArr =  $this->clearToArr($onlineIdList,$deliveryIdList,$releaseIdList,$testingIdList,$feedbackedIdList,$demandId);
                    $onlineIdList = $clearArr['online'];
                    $deliveryIdList  = $clearArr['delivery'];
                    $releaseIdList = $clearArr['release'];
                    $testingIdList = $clearArr['testing'];
                    $feedbackedIdList = $clearArr['feedbacked'];
                    continue;
                }
            }else if(($statusList[$demandId]['releaseBuild'] || $statusList[$demandId]['testing']) && ($statusList[$demandId]['releaseSecond'] == 0 && $statusList[$demandId]['delivery'] == 0 && $statusList[$demandId]['onlinesuccess'] == 0)){
                /*只有制版*/
                //如果制版 有测试中的 状态就是测试(build)
                if ($statusList[$demandId]['testing'])
                {
                    //如果状态不是测试中
                    if($demands[$demandId]->status != 'build' )
                    {
                        $testingIdList = $this->createToArr($demandId,$testingIdList,$demands[$demandId]->status,'build');
                        $testingIdList[$demandId]['code'][]  = isset($testingIdList[$demandId]['codeSecond']) ? $testingIdList[$demandId]['codeSecond'] :'';
                        $this->dao->update(TABLE_DEMAND)->set('status')->eq('build')->set('actualOnlineDate')->eq(null)->where('id')->eq($demandId)->exec();
                        $this->loadModel('consumed')->recordAuto('demand', $demandId, 0, $demands[$demandId]->status, 'build');
                    }
                    $clearArr =  $this->clearToArr($onlineIdList,$deliveryIdList,$releaseIdList,$testingIdList,$feedbackedIdList,$demandId);
                    $onlineIdList = $clearArr['online'];
                    $deliveryIdList  = $clearArr['delivery'];
                    $releaseIdList = $clearArr['release'];
                    $testingIdList = $clearArr['testing'];
                    $feedbackedIdList = $clearArr['feedbacked'];
                    continue;
                }
                //如果有发布,且所有制版状态都是已发布 &&没有二线已发布.则需求条目状态为 已发布
                if($statusList[$demandId]['releaseBuild'])
                {
                    //如果状态不是已发布
                    if(count($builds) == $statusList[$demandId]['releaseBuild'] && count($builds) != 0 && $demands[$demandId]->status != 'released')
                    {
                        $releaseIdList = $this->createToArr($demandId,$releaseIdList,$demands[$demandId]->status,'released');
                        $releaseIdList[$demandId]['code'][]  = isset($releaseIdList[$demandId]['codeSecond'] ) ? $releaseIdList[$demandId]['codeSecond'] :'';
                        $this->loadModel('consumed')->recordAuto('demand', $demandId, 0, $demands[$demandId]->status, 'released');
                    }
                    $clearArr =  $this->clearToArr($onlineIdList,$deliveryIdList,$releaseIdList,$testingIdList,$feedbackedIdList,$demandId);
                    $onlineIdList = $clearArr['online'];
                    $deliveryIdList  = $clearArr['delivery'];
                    $releaseIdList = $clearArr['release'];
                    $testingIdList = $clearArr['testing'];
                    $feedbackedIdList = $clearArr['feedbacked'];
                    continue;
                }
            }else if(($statusList[$demandId]['releaseSecond'] ||$statusList[$demandId]['delivery']||$statusList[$demandId]['onlinesuccess']) && ($statusList[$demandId]['releaseBuild'] == 0 && $statusList[$demandId]['testing'] == 0)){
                //只有二线
                if(isset($feedbackedIdList[$demandId]) && !isset($feedbackedIdList[$demandId]['status']))
                {
                    unset($feedbackedIdList[$demandId]);
                }
                //如果有二线已发布.则需求条目状态为 二线已发布
                if($statusList[$demandId]['releaseSecond'])
                {
                    if($demands[$demandId]->status != 'released')
                    {
                        if(isset($releaseIdList[$demandId]['code'])) unset($releaseIdList[$demandId]['code']);
                        $releaseIdList = $this->createToArr($demandId,$releaseIdList,$demands[$demandId]->status,'released');
                        $releaseIdList[$demandId]['code'][]  = isset($releaseIdList[$demandId]['codeSecond'] ) ? $releaseIdList[$demandId]['codeSecond'] :'';
                        $this->loadModel('consumed')->recordAuto('demand', $demandId, 0, $demands[$demandId]->status, 'released');
                    }
                    $clearArr =  $this->clearToArr($onlineIdList,$deliveryIdList,$releaseIdList,$testingIdList,$feedbackedIdList,$demandId);
                    $onlineIdList = $clearArr['online'];
                    $deliveryIdList  = $clearArr['delivery'];
                    $releaseIdList = $clearArr['release'];
                    $testingIdList = $clearArr['testing'];
                    $feedbackedIdList = $clearArr['feedbacked'];
                    continue;
                }
                //如果当前状态不是已交付 && 无其他关联状态 改为已交付状态
                if ($statusList[$demandId]['delivery'] > 0)
                {
                    //如果状态 没变化
                    if($demands[$demandId]->status != 'delivery')
                    {
                        $deliveryIdList = $this->createToArr($demandId,$deliveryIdList,$demands[$demandId]->status,'delivery');
                        $deliveryIdList[$demandId]['code'][]  = isset($deliveryIdList[$demandId]['codeSecond'] ) ? $deliveryIdList[$demandId]['codeSecond'] :'';
                        $this->loadModel('consumed')->recordAuto('demand', $demandId, 0, $demands[$demandId]->status, 'delivery');
                    }
                    $clearArr =  $this->clearToArr($onlineIdList,$deliveryIdList,$releaseIdList,$testingIdList,$feedbackedIdList,$demandId);
                    $onlineIdList = $clearArr['online'];
                    $deliveryIdList  = $clearArr['delivery'];
                    $releaseIdList = $clearArr['release'];
                    $testingIdList = $clearArr['testing'];
                    $feedbackedIdList = $clearArr['feedbacked'];
                    continue;
                }
                //如果其他都没有只有上线成功 状态改为上线成功
                if ($statusList[$demandId]['onlinesuccess'] > 0)
                {
                    if($demands[$demandId]->status != 'onlinesuccess')
                    {
                        $onlineIdList = $this->createToArr($demandId,$onlineIdList,$demands[$demandId]->status,'onlinesuccess');
                        $onlineIdList[$demandId]['code'][]  = isset($onlineIdList[$demandId]['codeSecond'] ) ? $onlineIdList[$demandId]['codeSecond'] :'';
                        $this->dao->update(TABLE_DEMAND)->set('status')->eq('onlinesuccess')->set('actualOnlineDate')->eq($onlineTimeList[$demandId])->where('id')->eq($demandId)->exec();
                        $this->loadModel('consumed')->recordAuto('demand', $demandId, 0, $demands[$demandId]->status, 'onlinesuccess');
                    }
                    //防止原状态也为上线成功
                    if(!empty($onlineTimeList[$demandId]) && $onlineTimeList[$demandId] > $demands[$demandId]->actualOnlineDate)
                    {
                        $this->dao->update(TABLE_DEMAND)->set('actualOnlineDate')->eq($onlineTimeList[$demandId])->where('id')->eq($demandId)->exec();
                    }
                    $clearArr =  $this->clearToArr($onlineIdList,$deliveryIdList,$releaseIdList,$testingIdList,$feedbackedIdList,$demandId);
                    $onlineIdList = $clearArr['online'];
                    $deliveryIdList  = $clearArr['delivery'];
                    $releaseIdList = $clearArr['release'];
                    $testingIdList = $clearArr['testing'];
                    $feedbackedIdList = $clearArr['feedbacked'];
                    continue;
                }
            }else{
                //制版和二线都不存在
                //如果没有制版，需求条目状态更新为开发中（迭代25）
                if($statusList[$demandId]['feedbacked'])
                {
                    if($demands[$demandId]->status != 'feedbacked')
                    {
                        $feedbackedIdList = $this->createToArr($demandId,$feedbackedIdList,$demands[$demandId]->status,'feedbacked');
                        $feedbackedIdList[$demandId]['code'][]  = isset($feedbackedIdList[$demandId]['codeSecond'] ) ? $feedbackedIdList[$demandId]['codeSecond'] :'';
                        $this->dao->update(TABLE_DEMAND)->set('status')->eq('feedbacked')->set('actualOnlineDate')->eq(null)->where('id')->eq($demandId)->exec();
                        $this->loadModel('consumed')->recordAuto('demand', $demandId, 0, $demands[$demandId]->status, 'feedbacked');
                    }
                    $clearArr =  $this->clearToArr($onlineIdList,$deliveryIdList,$releaseIdList,$testingIdList,$feedbackedIdList,$demandId);
                    $onlineIdList = $clearArr['online'];
                    $deliveryIdList  = $clearArr['delivery'];
                    $releaseIdList = $clearArr['release'];
                    $testingIdList = $clearArr['testing'];
                    $feedbackedIdList = $clearArr['feedbacked'];
                    continue;
                }
            }
            #endregion
        }
        //开发中
        if($feedbackedIdList)
        {
            $this->loadModel('action')->createActions('demand', $feedbackedIdList, 'feedback',$this->lang->demand->statusConsumedList);
        }
        //测试中
        if($testingIdList)
        {
            $this->loadModel('action')->createActions('demand', $testingIdList, 'build',$this->lang->demand->statusConsumedList);
        }
        //已交付
        if($deliveryIdList)
        {
            $this->dao->update(TABLE_DEMAND)->set('status')->eq('delivery')->set('actualOnlineDate')->eq(null)->where('id')->in(array_filter(array_unique(array_keys($deliveryIdList))))->exec();
            $this->loadModel('action')->createActions('demand', $deliveryIdList, 'delivery',$this->lang->demand->statusConsumedList);
        }
        //上线成功
        if($onlineIdList)
        {
            $this->loadModel('action')->createActions('demand', $onlineIdList, 'onlinesuccess',$this->lang->demand->statusConsumedList);
        }
        //已发布统一处理
        if($releaseIdList)
        {
            $this->dao->update(TABLE_DEMAND)->set('status')->eq('released')->set('actualOnlineDate')->eq(null)->where('id')->in(array_filter(array_unique(array_keys($releaseIdList))))->exec();
            $this->loadModel('action')->createActions('demand', $releaseIdList, 'released',$this->lang->demand->statusConsumedList);
        }
        return ['demand onlinesuccess' => $onlineIdList, 'demand delivery' => $deliveryIdList,'demand released' => $releaseIdList, 'demand testing' => $testingIdList, 'demand feedback' => $feedbackedIdList];

    }

    /**
     * 为备注创建数组
     * @param $demandID
     * @param $typeIdList
     * @param $oldStatus
     * @param $status
     * @return mixed
     */
    public function createToArr($demandID,$typeIdList,$oldStatus,$status){
        $typeIdList[$demandID]['id'] = $demandID;
        $typeIdList[$demandID]['oldStatus'] = $oldStatus;
        $typeIdList[$demandID]['status'] = $status;
        return $typeIdList;
    }

    /**
     * 清除不满足要求数组
     * @param $onlineIdList   上线成功
     * @param $deliveryIdList 已交付
     * @param $releaseIdList  已发布
     * @param $testingIdList  测试中
     * @param $feedbackedIdList 开发中
     * @param $demandID
     */
    public function clearToArr($onlineIdList,$deliveryIdList,$releaseIdList,$testingIdList,$feedbackedIdList,$demandID){
        if(isset($onlineIdList[$demandID]) && !isset($onlineIdList[$demandID]['status'])){
            unset($onlineIdList[$demandID]);
        }
        if(isset($deliveryIdList[$demandID]) && !isset($deliveryIdList[$demandID]['status'])){
            unset($deliveryIdList[$demandID]);
        }
        if(isset($releaseIdList[$demandID]) && !isset($releaseIdList[$demandID]['status'])){
            unset($releaseIdList[$demandID]);
        }
        if(isset($testingIdList[$demandID]) && !isset($testingIdList[$demandID]['status'])){
            unset($testingIdList[$demandID]);
        }
        if(isset($feedbackedIdList[$demandID]) && !isset($feedbackedIdList[$demandID]['status'])){
            unset($feedbackedIdList[$demandID]);
        }
        return array('online' => $onlineIdList,'delivery' =>$deliveryIdList,'release' =>$releaseIdList,'testing' =>$testingIdList,'feedbacked' =>$feedbackedIdList);
    }

    /**
     * @Notes: 获取需要联动的有效的需求条目数据 已挂起、已录入、已关闭的不做联动
     * @param: $type 1：外部  2：内部
     * @Date: 2023/4/13
     * @Time: 10:46
     * @Interface getEffectiveDemandInfo
     */
    public function getEffectiveDemandInfo($type=0)
    {
        return $this->dao->select('id, status, code, dealUser, actualOnlineDate')
            ->from(TABLE_DEMAND)
            ->where('status')->notIN("wait,closed,suspend,deleted")
            ->beginIF(in_array($type,[1,2]))->andWhere('sourceDemand')->eq($type)->fi()
            ->andWhere('createdDate')->gt(date('Y-m-d', strtotime("-1 year")))
            ->andWhere('secureStatusLinkage')->eq('0')
            ->fetchAll('id');
    }

    /**
     * 待处理人置空
     * @param $problemID
     */
    public function setNoReview($demandIDs)
    {
        if (empty($demandIDs)) return false;
        return $this->dao->update(TABLE_DEMAND)
            ->set('dealUser')->eq("")
            ->where('id')->in($demandIDs)
            ->exec();
    }

    /**
     * User: TongYanQi
     * Date: 2022/8/30
     * 更新实际上线时间
     */
    public function setActEndTime($datetime, $demandID)
    {        
        if (empty($datetime)) return;
        $data = $this->dao->select('requirementID,opinionID')->from(TABLE_DEMAND)->where('id')->eq($demandID)->fetch();
        $opinionOnlineTimeByDemand = $this->dao->select('onlineTimeByDemand')->from(TABLE_OPINION)->where('id')->eq($data->opinionID)->fetch('onlineTimeByDemand');
        if(!isset($opinionOnlineTimeByDemand)||(strtotime($datetime)>strtotime($opinionOnlineTimeByDemand))){
            $this->dao->update(TABLE_OPINION)->set('onlineTimeByDemand')->eq($datetime)->where('id')->eq($data->opinionID)->exec();
        }
        $requirementOnlineTimeByDemand = $this->dao->select('onlineTimeByDemand')->from(TABLE_REQUIREMENT)->where('id')->eq($data->requirementID)->fetch('onlineTimeByDemand');
        if(!isset($requirementOnlineTimeByDemand)||(strtotime($datetime)>strtotime($requirementOnlineTimeByDemand))){
            $this->dao->update(TABLE_REQUIREMENT)->set('onlineTimeByDemand')->eq($datetime)->where('id')->eq($data->requirementID)->exec();
        }
        $this->dao->update(TABLE_DEMAND)->set('actualOnlineDate')->eq($datetime)->where('id')->eq($demandID)->exec();
    }

    /**
     * 待处理人重置
     * @param $problemID
     */
    public function setReviewers($problemIDs)
    {
        if (empty($problemIDs)) return false;
        return $this->dao->update(TABLE_PROBLEM)
            ->set('dealUser = acceptUser')
            ->where('id')->in($problemIDs)
            ->exec();
    }

    /**
     * Project: chengfangjinke
     * Method: suspend
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:14
     * Desc: This is the code comment. This method is called suspend.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $demandID
     * @return array
     */
    public function suspend($demandID)
    {
        $oldDemand = $this->getByID($demandID);
        //只有项目实现才能挂起
        if($oldDemand->fixType == 'second')
        {
            return dao::$errors[] = $this->lang->demand->secondCloseTip;
        }
        $demand = fixer::input('post')
            ->remove('comment')
            ->get();

        $this->dao->update(TABLE_DEMAND)->data($demand)
            ->where('id')->eq($demandID)
            ->exec();

        $this->loadModel('consumed')->record('demand', $demandID, 0, $this->app->user->account, $oldDemand->status, 'suspend');

        return common::createChanges($oldDemand, $demand);
    }

    /**
     * Project: chengfangjinke
     * Method: start
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:14
     * Desc: This is the code comment. This method is called start.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $demandID
     * @return array
     */
    public function start($demandID)
    {
        /*迭代三十三 激活需求条目需判断所属任务是否为已挂起，已挂起不允许激活*/
        $oldDemand = $this->getByID($demandID);
        $requirementStatus = $this->dao->select('status')->from(TABLE_REQUIREMENT)->where('id')->eq($oldDemand->requirementID)->fetch('status');
        if($requirementStatus == 'closed')
        {
            dao::$errors[] = $this->lang->demand->parentClosed;
            return false;
        }

        //所属任务为外部已删除不允许激活

        if($requirementStatus == 'deleteout')
        {
            dao::$errors[] = $this->lang->demand->parentDeleteout;
            return false;
        }
        $demand = fixer::input('post')
            ->remove('comment')
            ->get();

        $this->dao->update(TABLE_DEMAND)->data($demand)
            ->where('id')->eq($demandID)
            ->exec();
        return common::createChanges($oldDemand, $demand);
    }

    /**
     * Desc: 忽略
     * Date: 2022/8/11
     * Time: 16:12
     *
     * @param $demandID
     * @return mixed
     *
     */
    public function ignore($demandID)
    {
        $oldDemand = $this->getByID($demandID);
        $data = new stdClass();
        $data->ignoreStatus = 1;
        $data->ignoredBy = $this->app->user->account;
        $data->ignoredDate = date('Y-m-d H:i:s');

        $this->dao->update(TABLE_DEMAND)->data($data)
            ->autoCheck()
            ->batchCheck($this->config->demand->ignore->requiredFields, 'notempty')
            ->where('id')->eq($demandID)
            ->exec();
        return common::createChanges($oldDemand, $data);
    }

    /**
     * Desc: 恢复
     * Date: 2022/8/11
     * Time: 16:12
     *
     * @param $demandID
     * @return mixed
     *
     */
    public function recoveryed($demandID)
    {
        $oldDemand = $this->getByID($demandID);
        $data = new stdClass();
        $data->ignoreStatus = 0;
        $data->recoveryedBy = $this->app->user->account;
        $data->recoveryedDate = date('Y-m-d H:i:s');

        $this->dao->update(TABLE_DEMAND)->data($data)
            ->autoCheck()
            ->batchCheck($this->config->demand->recoveryed->requiredFields, 'notempty')
            ->where('id')->eq($demandID)
            ->exec();
        return common::createChanges($oldDemand, $data);
    }

    /**
     * Project: chengfangjinke
     * Method: close
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:15
     * Desc: This is the code comment. This method is called close.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $demandID
     */
    public function close($demandID)
    {
        $oldDemand = $this->getByID($demandID);
        //只有二线实现才能关闭
        if($oldDemand->fixType == 'project')
        {
            return dao::$errors[] = $this->lang->demand->projectCloseTip;
        }

        //迭代三十三 如果有延期流程需提示，不允许关闭
        if(in_array($oldDemand->delayStatus,$this->lang->demand->suspendStatusDelayList)) {
            return dao::$errors[] = sprintf($this->lang->demand->delaySuspendTip , $oldDemand->code);
        }
        $data = new stdClass();
        $data->status = 'closed';
        $data->closedBy = $this->app->user->account;
        $data->closedDate = helper::today();
        if(empty($oldDemand->solvedTime)){
            $data->solvedTime = helper::now();
        }
        // 由于关闭后不能激活,但是挂起可以关闭因此不能记录上一次状态,以免记录了挂起无法恢复
        // $data->lastStatus = $oldDemand->status;
        $this->dao->update(TABLE_DEMAND)->data($data)->autoCheck()
            ->batchCheck($this->config->demand->close->requiredFields, 'notempty')
            ->where('id')->eq($demandID)->exec();
        //更新需求和问题解决时间
        /** @var problemModel $problemModel */
        $problemModel = $this->loadModel('problem');
        $problemModel->getAllSecondSolveTime($demandID,'demand');
        return common::createChanges($oldDemand, $data);
    }

    /**
     * Project: chengfangjinke
     * Method: isClickable
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:15
     * Desc: This is the code comment. This method is called isClickable.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $demand
     * @param $action
     * @return bool
     */
    public static function isClickable($demand, $action)
    {
        //迭代三十三重新梳理权限
        global $app;
        $demandModel = new self();
        $action = strtolower($action);
        //单子删除后，所有按钮不可见
        if($demand->status == 'deleted'){
          return false;
        }
        if ($action == 'edit'){
            $res = $demandModel->checkAllowEdit($demand);
            return $res ? $res : false;
        }
        if ($action == 'deal')          return $app->user->account == 'admin' or (in_array($demand->status,array('wait')) and $app->user->account == $demand->dealUser);
        if ($action == 'assignment')    return $app->user->account == 'admin' or (in_array($demand->status,array('wait')) and $app->user->account == $demand->dealUser);
        if ($action == 'ignore')        return $app->user->account == 'admin' or (in_array($demand->status,array('wait')) and $app->user->account == $demand->dealUser);
        if ($action == 'suspend')       return $app->user->account == 'admin' or in_array($demand->status,array('feedbacked')) and $demand->fixType == 'project';
        if ($action == 'start')         return $app->user->account == 'admin' or in_array($demand->status,array('feedbacked','suspend')) and $demand->fixType == 'project';
        if ($action == 'recoveryed')    return in_array($app->user->account,['admin',$demand->dealUser,$demand->ignoredBy]);
        if ($action == 'delete')        return $app->user->account == 'admin' or ($demand->status == 'wait' and $app->user->account == $demand->createdBy);
        if ($action == 'close')         return $app->user->account == 'admin' or in_array($demand->status,array('wait','feedbacked')) and $demand->fixType == 'second';
        if ($action == 'reviewdelay')   return $app->user->account == 'admin' or !in_array($demand->status,array('deleteout'));
        if ($action == 'copy')   return $app->user->account == 'admin' or !in_array($demand->status,array('deleteout'));
        return true;
    }

    /**
     * Project: chengfangjinke
     * Method: getPairs
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:15
     * Desc: This is the code comment. This method is called getPairs.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @return mixed
     */
    public function getPairs()
    {
        return $this->dao->select('id,code')->from(TABLE_DEMAND)
            ->where('status')->ne('deleted')
            ->orderBy('id_desc')
            ->fetchPairs();
    }

    public function getPairsBycode($codeList = array())
    {
        return $this->dao->select('id,code')->from(TABLE_DEMAND)
            ->where('status')->ne('deleted')
            ->beginIF(is_array($codeList) &&  count($codeList))->andWhere('code')->in($codeList)->fi()
            ->orderBy('id_desc')
            ->fetchPairs();
    }

    /**
     * TongYanQi 2022/12/19
     * 所有状态 统计用
     */
    public function getAllStatus()
    {
        $opinions = $this->dao->select('id,title,status,`acceptDept`')->from(TABLE_DEMAND)
            ->where('status')->ne('deleted')
            ->andwhere('status')->ne('suspend')
            ->fetchAll('id');
        return $opinions;
    }
    /**
     * 根据多个id获取信息
     * @param array $ids
     * @return stdClass
     */
    public function getPairsByIds(array $ids)
    {
        if (empty($ids)) return null;
        $info = $this->dao->select('id,code,title,sourceDemand')->from(TABLE_DEMAND)
            ->where('status')->ne('deleted')
            ->andwhere('id')->in($ids)
            ->orderBy('id_desc')
            ->fetchAll();
        $demands = new stdClass();
        foreach ($info as $item) {
            $id = $item->id;
            $formatTitle = $item->code . '('. $item->title.')';
            $demands->$id = ['code' => $item->code, 'title' => $item->title, 'sourceDemand' => $item->sourceDemand,  'formatTitle' => $formatTitle];
        }
        return $demands;
    }

    /**
     * Project: chengfangjinke
     * Method: getPairsAbstract
     * User: Tony Stark
     * Year: 2022
     * Date: 2022/2/15
     * Time: 14:54
     * Desc: This is the code comment. This method is called getPairs.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param bool $status
     * @param string $exWhere
     * @return mixed
     */
    public function getPairsTitle($status=false, $exWhere = '')
    {
        return $this->dao->select("id,concat(code,'（',IFNULL(title,''),'）') as code")->from(TABLE_DEMAND)
            ->where('status')->ne('deleted')
//            ->beginIF(!$status)->where('status')->ne('deleted')->fi()
//            ->beginIF($status)->where('status')->in(['onlinesuccess','suspend','released','delivery'])->fi()
            ->beginIF($exWhere)->andwhere($exWhere)->fi()
            ->orderBy('id_desc')
            ->fetchPairs();
    }

    /**
     * Project: chengfangjinke
     * Method: deal
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:15
     * Desc: This is the code comment. This method is called deal.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $demandID
     * @return array
     */
    public function deal($demandID)
    {
        if (!$this->post->consumed) {
            return dao::$errors['consumed'] = $this->lang->demand->consumedEmpty;
        }

        $oldDemand = $this->getByID($demandID);

        $data = fixer::input('post')
            ->join('app', ',')
            ->join('coordinators', ',')
            ->stripTags($this->config->demand->editor->deal['id'], $this->config->allowedTags)
            ->remove('uid,relevantUser,workload,user,consumed,mailto')
            ->get();
        $data->productPlan = empty($data->productPlan) ? 0 : $data->productPlan;

        /* 旧的备注之后更新一下。当状态为已分析时，将处理人部门处理成实施部门，将处理人处理成负责人。*/
        if ($this->post->status == 'feedbacked') {
            $acceptUser = $this->loadModel('user')->getByAccount($this->post->user);
            $data->acceptDept = $acceptUser->dept;
            $data->acceptUser = $this->post->user;
        }
        /* 旧的备注之后更新一下。当状态为已分析，已解决，必填项所属项目和实现方式。*/
        if ($this->post->status == 'feedbacked' or $this->post->status == 'solved') {
            /* 必填判断所属项目。*/
            // if (empty($data->projectPlan)) return dao::$errors = array('projectPlan' => $this->lang->demand->projectPlanEmpty);

            if ($data->fixType == 'second') {
                // 判断二线实现的解决方案必须为二线项目。
                $plan = $this->dao->select('*')->from(TABLE_PROJECTPLAN)->where('deleted')->eq(0)->andWhere('id')->eq($data->projectPlan)->fetch();
                if (empty($plan->secondLine)) return dao::$errors = array('' => $this->lang->demand->noSecondLinse);
            }

            if ($data->product) {
                if (empty($data->productPlan)) return dao::$errors = array('' => $this->lang->demand->noProductPlan);
            }

            /* 必填项增加实现方式，计划完成时间。*/
            $this->config->demand->deal->requiredFields .= ',fixType,end';
        }

        // 判断处理后的状态是否为【已关闭】，如果是则记录关闭人和关闭时间。
        $today = helper::today();
        if ($this->post->status == 'closed') {
            $data->closedBy = $this->post->user;
            $data->closedDate = $today;
        }

        $data->lastDealDate = $today;
        $data = $this->loadModel('file')->processImgURL($data, $this->config->demand->editor->deal['id'], $this->post->uid);

        // 根据【所属应用系统】处理系统分类字段的值。
        $paymentIdList = array();
        $data->isPayment = $oldDemand->isPayment;
        if (isset($data->app)) {
            foreach (explode(',', $data->app) as $appID) {
                if (!$appID) continue;
                $paymentType = $this->dao->select('isPayment')->from(TABLE_APPLICATION)->where('id')->eq($appID)->fetch('isPayment');
                if ($paymentType) $paymentIdList[] = $paymentType;
            }
            $data->isPayment = implode(',', $paymentIdList);
        }
        $this->dao->update(TABLE_DEMAND)->data($data)->autoCheck()
            ->batchCheck($this->config->demand->deal->requiredFields, 'notempty')
            ->where('id')->eq($demandID)
            ->exec();
        $this->loadModel('consumed')->record('demand', $demandID, $this->post->consumed, $this->post->user, $oldDemand->status, $this->post->status, $this->post->mailto);

        $this->loadModel('file')->updateObjectID($this->post->uid, $demandID, 'demand');
        $this->file->saveUpload('demand', $demandID);

        return common::createChanges($oldDemand, $data);
    }

    /**
     * Send mail.
     *
     * @param int $demandID
     * @param int $actionID
     * @access public
     * @return void
     */
    public function sendmail($demandID, $actionID)
    {
        $this->loadModel('mail');
        $demand = $this->getById($demandID);
        $users = $this->loadModel('user')->getPairs('noletter');

        $demand->newDealUser = zmget($users,trim($demand->dealUser,','),'');
        $status = $this->lang->demand->statusList;
        if($demand->status == 'deleted'){
            $demand->statusCN = '已删除';
        }else{
            $demand->statusCN = $status[$demand->status];
        }
        $demand->delayReason = html_entity_decode(strip_tags(str_replace("<br />","\n",strval(htmlspecialchars_decode($demand->delayReason,ENT_QUOTES)))));
        /* 获取后台通知中配置的邮件发信。*/
        $this->app->loadLang('custommail');
        $this->app->loadLang('opinion');
        $mailConf = isset($this->config->global->setDemandMail) ? $this->config->global->setDemandMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);
        $browseType = 'demand';

        /* 处理邮件发信的标题和日期。*/
        $bestDeal = $this->dao->select('*')->from(TABLE_CONSUMED)->where('objectType')->eq('demand')
            ->andWhere('objectID')->eq($demandID)
            ->orderBy('id_desc')
            ->fetch();
        $bestDate = empty($bestDeal) ? '' : substr($bestDeal->createdDate, 0, 10);
        $mailTitle = vsprintf($mailConf->mailTitle, $mailConf->variables);
        $mailContentConfig = $mailConf->mailContent;

        /* Get action info. */
        $action = $this->loadModel('action')->getById($actionID);
        $history = $this->action->getHistory($actionID);
        $action->history = isset($history[$actionID]) ? $history[$actionID] : array();
        if($action->action == 'demanddelay' || $action->action == 'reviewdelay'){
            $demand->delay = "true";
            $demand->newDealUser = zmget($users, $demand->delayDealUser, '');
            $mailTitle = $this->lang->demand->delayMaile;
            if($demand->delayStatus == 'success' || $demand->delayStatus == 'fail'){
                $mailTitle = $this->lang->demand->delayNoticeMaile;
                $mailContentConfig = $this->lang->demand->delayNotice;
            }
        }

        /* Get mail content. */
        $oldcwd = getcwd();
        $modulePath = $this->app->getModulePath($appName = '', 'demand');
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

        $sendUsers = $this->getToAndCcList($demand,$action->action);
        if (!$sendUsers) return;
        list($toList, $ccList) = $sendUsers;
        if($action->action == 'deleted'){
            //创建人，研发责任人，待处理人
            $acceptUser = explode(',',$demand->acceptUser);
            $createdUser = explode(',',$demand->createdBy);
            $dealUser = explode(',',$demand->dealUser);
            $totalToList = array_merge($acceptUser,$createdUser,$dealUser);
            if(!empty($totalToList)){
                $toList = implode(',',array_unique($totalToList));
            }
        }

        /* 处理邮件标题。*/
        //$subject = $this->getSubject($demand);
        $subject = $mailTitle;
        if($action->action == 'deleted'){
            $subject = $this->lang->demand->deleteMaile;
        }
        if($action->action == 'demanddelay' || $action->action == 'reviewdelay'){
            if($demand->delayStatus == 'success' || $demand->delayStatus == 'fail'){
                $toList = $demand->delayUser;
                if($demand->delayStatus == 'success'){
                    $user = $this->loadModel('user')->getUserInfo($demand->delayUser);
                    $myDept = $this->loadModel('dept')->getByID($user->dept);
                    $toList = $toList.','.trim($myDept->executive, ',');

                    $this->app->loadLang('problem');
                    $ccList = implode(',', array_filter(array_keys($this->lang->problem->delayCCUserList)));
                }
                $subject = $this->lang->demand->delayNoticeMaile;
            }else{
                $toList = $demand->delayDealUser;
                $subject = $this->lang->demand->delayMaile;
            }
        }
        $status = array('wait','assigned','deleted'); //20220930 待确认或待分析发邮件,删除，其他不发
        if(in_array($demand->status,$status) || ($action->action == 'demanddelay' || $action->action == 'reviewdelay')){
            $this->mail->send($toList, $subject, $mailContent, $ccList);
        }
        if ($this->mail->isError()) error_log(join("\n", $this->mail->getError()));
    }

    /**
     * Project: chengfangjinke
     * Method: getToAndCcList
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:15
     * Desc: This is the code comment. This method is called getToAndCcList.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $object
     * @return array
     */
    public function getToAndCcList($object,$type)
    {
        /* Set toList and ccList. */
        $toList = $object->dealUser;
        $details = $this->loadModel('consumed')->getObjectByID($object->id, 'demand', $object->status);
        if($type == 'edited')
        {
            $assignedTo = $this->dao->select('assignedTo')->from(TABLE_OPINION)->where('id')->eq($object->opinionID)->fetch('assignedTo');
            $toList .= ','.trim($assignedTo,',');
        }
        $ccList = isset($details->mailto) ? trim($details->mailto, ',') : '';
        return array($toList, $ccList);
    }

    /**
     * Project: chengfangjinke
     * Method: getSubject
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:15
     * Desc: This is the code comment. This method is called getSubject.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $object
     * @return string
     */
    public function getSubject($object)
    {
        return $this->lang->demand->common . '#' . $object->id . '-' . $object->code;
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
        /* 获取一条需求意向下的所有需求条目数据。*/
        return $this->dao->select('*')->from(TABLE_DEMAND)
            ->where('opinionID')->eq($opinionID)
            ->andWhere('status')->ne('deleted')
            ->fetchAll();
    }

    public function getByRequirementID($field = '*',$requirementID)
    {
        return $this->dao->select($field)->from(TABLE_DEMAND)
            ->where('requirementID')->eq($requirementID)
            ->andWhere('status')->ne('deleted')
            ->fetchAll();
    }

    public function getBrowesByRequirementID($requirementID)
    {
        return $this->dao->select('*')->from(TABLE_DEMAND)
            ->where('requirementID')->eq($requirementID)
            ->andWhere('status')->notIN('deleted')
            ->fetchAll();
    }

    public function getALlDemands($field = '*')
    {
        return $this->dao->select($field)->from(TABLE_DEMAND)->where('status')->ne('deleted')->andWhere('sourceDemand')->eq(1)->fetchAll();
    }

    /**
     * @Notes:根据opinionID分类构造数据
     * @Date: 2023/8/4
     * @Time: 10:27
     * @Interface allDemandsGroupOpinionID
     * @param $field
     * @return array
     */
    public function allDemandsGroupOpinionID($field)
    {
        $demandsInfo = $this->getAllDemands($field);
        $demandArray = array();
        foreach ($demandsInfo as $key => $demand){
            if($demand->opinionID != '0'){
                $demandArray[$demand->opinionID][] = $demand;
            }
        }
        return $demandArray;
    }


    /**
     * 批量获取多个需求条目
     * @param $ids
     * @return mixed
     */
    public function getManyDemands($ids)
    {
        return $this->dao->select('id, opinionID, requirementID, title, product, productPlanm')->from(TABLE_DEMAND)
            ->where('id')->in($ids)
            ->andWhere('status')->ne('deleted')
            ->fetchAll();
    }

    /**
     * @Notes: 根据需求任务id集合获取条目
     * @Date: 2023/8/25
     * @Time: 15:02
     * @Interface getDemandsByRequirementIds
     * @param $requirementIDs
     * @param string $field
     */
    public function getDemandsByRequirementIds($requirementIDs,$field = '*')
    {
        return $this->dao->select($field)->from(TABLE_DEMAND)
            ->where('requirementID')->in($requirementIDs)
            ->andWhere('status')->ne('deleted')
            ->fetchAll();
    }

    public function setListValue()
    {
        $this->app->loadLang('demand');
        $statusList = $this->lang->demand->statusList;//状态
        $fixTypeList = $this->lang->demand->fixTypeList;

        //所属需求意向
        $requirementsList = array('' => '') + $this->loadModel('requirement')->getPairs();
        $requirementsArray = array();
        foreach ($requirementsList as $id => $name) {
            $requirementsArray[$id] = $name . "(#$id)";
        }

        //所属需求任务
        $opinionList = array('0' => '') + $this->loadModel('opinion')->getOpinionList();
        $opinionArray = array();
        foreach ($opinionList as $id => $name) {
            $opinionArray[$id] = $name . "(#$id)";
        }

        //责任人所在部门
        $deptList = array('0' => '') + $this->loadModel('dept')->getPairs();
        $deptArray = array();
        foreach ($deptList as $id => $name) {
            $deptArray[$id] = $name . "(#$id)";
        }
        //所属产品
        $productList = array('0' => '无') + $this->loadModel('product')->getPairs();
        $productArray = array();
        foreach ($productList as $id => $name) {
            $productArray[$id] = $name . "(#$id)";
        }

        //所属应用系统
        $appList = array('0' => '') + $this->loadModel('application')->getapplicationNameCodePairs();
        $appArray = array();
        foreach ($appList as $id => $name) {
            $appArray[$id] = $name . "(#$id)";
        }
        $this->post->set('requirementIDList', array_values($requirementsArray));
        $this->post->set('opinionIDList', array_values($opinionArray));
        $this->post->set('productList', array_values($productArray));
        $this->post->set('acceptDeptList', array_values($deptArray));
        $this->post->set('appList', array_values($appArray));
        $this->post->set('statusList', array_values($statusList));
        $this->post->set('fixTypeList', join(',', $fixTypeList));
        $this->post->set('width', 60);

        $this->post->set('listStyle', $this->config->demand->export->listFields);
        $this->post->set('extraNum', 0);
    }

    public function createFromImport()
    {
        $this->loadModel('action');
        $this->loadModel('file');
        $data = fixer::input('post')->get();
        $this->app->loadClass('purifier', true);
        $demandList = array();
        $line = 1;
        $date = date('Y-m-d');
        foreach ($data->title as $key => $value) {
            if ($value != '') {
                $demandData = new stdclass();
                $demandData->title = $data->title[$key];
                $demandData->opinionID = $data->opinionID[$key];
                $demandData->requirementID = $data->requirementID[$key];
                $demandData->desc = $data->desc[$key];
                $demandData->endDate = $data->endDate[$key];
                $demandData->acceptUser = $data->acceptUser[$key];
                if (!empty($data->acceptUser)) {
                    $acceptUser = $this->loadModel('user')->getByAccount($data->acceptUser[$key]);
                    $demandData->acceptDept = $acceptUser->dept;
                }
                $demandData->app = $data->app[$key];
                $demandData->fixType = $data->fixType[$key];
                $demandData->product = $data->product[$key];
                if (!empty($data->product[$key])) {
                    $productPlan = $this->dao->select('id')->from(TABLE_PRODUCTPLAN)->where('product')->eq($data->product[$key])->andWhere('title')->eq(trim($data->productPlan[$key]))->fetch('id');
                }
                if($productPlan == ''){
                    $productPlan = 0;
                }
                $demandData->productPlan = $productPlan;
                $demandData->status = $data->status[$key];
                $demandData->createdBy = $data->createdBy[$key];
                $demandData->createdDate = helper::today();
                $demandData->actualOnlineDate = $data->actualOnlineDate[$key] != '0000-00-00'  ? $data->actualOnlineDate[$key] : '';
                $demandData->dealUser = $data->dealUser[$key];
                $demandData->comment = $data->comment[$key];
                $demandData->sourceDemand = 1;

                if (isset($this->config->demand->import->requiredFields)) {
                    $requiredFields = explode(',', $this->config->demand->import->requiredFields);
                    foreach ($requiredFields as $requiredField) {
                        $requiredField = trim($requiredField);
                        if (empty($demandData->$requiredField))
                            dao::$errors[] = sprintf($this->lang->demand->noRequire, $line, $this->lang->demand->$requiredField);
                    }
                }
                $demandList[] = $demandData;
                $line++;
            }
            /*多行数据第一行评审标题未填提醒*/
            if ($key == 1 && $value == '') {
                dao::$errors[] = sprintf($this->lang->demand->firstDemand, $line, '需求条目主题');
            }
        }
        if (empty($demandList)) die(js::alert($this->lang->demand->emptyDemandMsg, true));
        if (dao::isError()) die(js::error(dao::getError()));
        foreach ($demandList as $insertData) {
            $number = $this->dao->select('count(id) c')->from(TABLE_DEMAND)->where('createdDate')->eq($insertData->createdDate)->andWhere('sourceDemand')->eq(1)->fetch('c');
            $code = 'CFIT-D-' . date('Ymd-',strtotime($insertData->createdDate)) . sprintf('%02d', $number+1);
            $insertData->code = $code;
            $this->dao->insert(TABLE_DEMAND)->data($insertData)->autoCheck()->exec();
            if (!dao::isError()) {
                $demandId = $this->dao->lastInsertID();
                $this->action->create('demand', $demandId, 'import', '');
                $this->loadModel('consumed')->record('demand', $demandId, 0, $insertData->createdBy,'', $insertData->status);
            }
            if (dao::isError()) die(js::error(dao::getError()));
        }
        if ($this->post->isEndPage) {
            unlink($this->session->fileImport);
            unset($_SESSION['fileImport']);
        }
    }

    /**
     * 获取任务
     * @param $data
     * @return string
     */
    public function getTaskName($projectID,$app,$productID,$version,$id,$flag = 0,$type = '')
    {
       //查询关联关系
         $build_task = $this->dao->select('*')->from(TABLE_TASK_DEMAND_PROBLEM)
             ->where('deleted')->eq('0')
             ->beginIF($flag)->andWhere('project')->eq((int)$projectID)->fi()
             //->beginIF($version != '1')->andWhere('application')->eq((int)$app)->fi()
             ->beginIF($version != '1')->andWhere('application')->in($app)->fi()
             ->beginIF($flag)->andWhere('product')->eq((int)$productID)->fi()
             ->beginIF($flag)->andWhere('version')->eq((int)$version)->fi()
             ->andWhere('typeid')->eq((int)$id)
             ->andWhere('type')->eq($type)
             ->fetchAll();
        $list = array();
        $taskName = new stdClass();
        if ($build_task) {
         foreach ($build_task as $item) {
            $taskid = $item->taskid;
            if($type == 'deptorder'){
                $tasks = $this->dao->select('t1. name,t1.id,t1.execution ')->from(TABLE_TASK)->alias('t1')
                    ->where('t1.deleted')->eq('0')
                    ->andWhere('t1.id')->eq($taskid)
                    ->fetch();
            }else{
                $tasks = $this->dao->select('concat(t1.name,"/",t2.name) name,t2.id,t1.execution ')->from(TABLE_TASK)->alias('t1')
                    ->leftJoin(TABLE_TASK)->alias('t2')
                    ->on('t1.id = t2.parent')
                    ->where('t1.deleted')->eq('0')
                    ->andWhere('t2.id')->eq($taskid)
                    ->fetch();
            }
            $executions = $this->dao->select('concat(t1.name,"/",t2.name) name,t1.id')->from(TABLE_EXECUTION)->alias('t1')
                ->leftJoin(TABLE_EXECUTION)->alias('t2')
                ->on('t1.id = t2.parent')
                ->where('t1.deleted')->eq('0')
                ->andWhere('t2.id')->eq($tasks->execution)
                ->fetch();
            if($taskid){
               $taskName->typeid = $item->id;
               $taskName->id = $taskid;
               $taskName->taskName = $executions->name . '/' . $tasks->name;
               $list = $taskName;
            }
        }
        }
        return $list;
    }

    public function checkCreatorPri($demandObj)
    {
        $userAccount = $this->app->user->account;
         if(empty($userAccount) || $userAccount != $demandObj->createdBy){
             return false;
         }
         //【已交付 上线成功 上线失败 已关闭 已挂起】状态不可编辑
        if(in_array($demandObj->status, ['delivery','onlinesuccess','onlinefailed','closed','suspend'])){
            return false;
        }
        return true;
    }
    /**
     * TongYanQi 2022/12/29
     * 整里待处理人
     */
    private function getNowDealUserString ($nowDealUsers): string
    {
        $nowDealUsers = trim($nowDealUsers ,',');
        $nowDealUsers = explode(',', $nowDealUsers );
        $nowDealUsers = array_unique($nowDealUsers);
        return ','.implode(',', $nowDealUsers).',';
    }

    /**查询制版名称 、发布名称
     * @param $taskid
     */
    public function getBuildRelease($taskid){
       /* $build = $this->dao->select('t1.id bid,t1.name buildname,t2.id rid,t2.name releasename')->from(TABLE_BUILD)->alias('t1')
            ->leftJoin(TABLE_RELEASE)->alias('t2')
            ->on('t1.id = t2.build')
            ->where('t1.taskid')->eq($taskid)
            ->andWhere('t1.deleted')->eq('0')
            ->andWhere('t2.deleted')->eq('0')
            ->orderBy('t1.id desc limit 1')
            ->fetch();*/
        if(!$taskid) return;
        $taskid = trim($taskid,',');
        $build = $this->dao->select('t1.id bid,t1.name buildname,t2.id rid,t2.name releasename')->from(TABLE_BUILD)->alias('t1')
            ->leftJoin(TABLE_RELEASE)->alias('t2')
            ->on('t1.id = t2.build')
            ->where("t1.id = (select max(id) from zt_build where taskid like ('%$taskid%') and deleted = '0')" )
            ->orderBy('t1.id,t2.id desc limit 1')
            ->fetch();
        //以下逻辑为了解决任务按照报工重新生成后，原有任务关联的制版和发布可以正常显示
        if(!$build){
            $task = $this->dao->select('*')->from(TABLE_TASK_DEMAND_PROBLEM)->where('taskid')->eq($taskid)->fetch();
            $build_task = $this->dao->select('taskid')->from(TABLE_TASK_DEMAND_PROBLEM)
                ->where('typeid')->eq((int)$task->typeid)
                ->andWhere('type')->eq($task->type)
                ->andWhere('product')->eq($task->product)
                ->andWhere('project')->eq($task->project)
                ->andWhere('version')->eq($task->version)
                ->andWhere('application')->in($task->application)
                ->andWhere('deleted')->eq('1')
                ->orderBy('id_desc')
                ->limit(1)
                ->fetch();
                if(isset($build_task->taskid)){
                    $build = $this->dao->select('t1.id bid,t1.name buildname,t2.id rid,t2.name releasename')->from(TABLE_BUILD)->alias('t1')
                        ->leftJoin(TABLE_RELEASE)->alias('t2')
                        ->on('t1.id = t2.build')
                        ->where("t1.id = (select max(id) from zt_build where taskid like ('%$build_task->taskid%') and deleted = '0')" )
                        ->orderBy('t1.id,t2.id desc limit 1')
                        ->fetch();
                }else{
                    $build = '';
                }

        }
        return $build;
    }

    /**
     * 所属项目关联产品
     * @param $projectID
     */
    public function bindProduct($projectID,$product,$type,$id){

            $oldProducts = $this->loadModel('project')->getProducts($projectID);
            $_POST['products'] = $product;
            //项目绑定产品
            $this->loadModel('project')->updateBindProducts($projectID);
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $oldProducts  = array_keys($oldProducts);
            // 2022-4-27 tongyanqi 删除已有的年度计划关联关系。
            /*foreach ($oldProducts as $oldProductId)
            {
                if(!in_array($oldProductId, $_POST['products'])){
                   // echo $this->dao->delete()->from(TABLE_RELATIONPLAN)->where('project')->eq($projectID)->andWhere('product')->eq($oldProductId)->printSQL();
                }
            }*/
            $record = $this->dao->select('*')->from(TABLE_PROJECTPLANRELATION)->where('projectId')->eq($projectID)->fetchOne();
            if(!empty($record)){
                $planRelations = json_decode($record[0]->planRelation, 1);
                $newPlanRelations = [];
                foreach ($planRelations as $planRelation)
                {
                    if(in_array($planRelation['id'], $_POST['products'])) { $newPlanRelations[] = $planRelation; }
                }
                if(!empty($newPlanRelations)){
                    $this->dao->update(TABLE_PROJECTPLANRELATION)->set('PlanRelation')->eq(json_encode($newPlanRelations))->where('projectId')->eq($projectID)->exec();
                }
            }
            // end 2022-4-27 tongyanqi 删除已有的年度计划关联关系。

            $newProducts  = $this->loadModel('project')->getProducts($projectID);
            $newProducts  = array_keys($newProducts);
            $diffProducts = array_merge(array_diff($oldProducts, $newProducts), array_diff($newProducts, $oldProducts));
            $type = ($type == 'demand' ? '由需求条目' : '由问题单')."($id)维护".'<br>';
            if($diffProducts) $this->loadModel('action')->create('project', $projectID, 'Managed', $type.'原产品：'.implode(',',$oldProducts).'<br>'.'更新后：'.implode(',',$newProducts),'原产品：'.implode(',',$oldProducts).'<br>'.'更新后：'.implode(',',$newProducts));

            // 查询项目下的执行，为这些执行同步关联产品。
            $executionIdList = $this->dao->select('id')->from(TABLE_EXECUTION)->where('project')->eq($projectID)->andWhere('deleted')->eq('0')->fetchAll();
            foreach($executionIdList as $execution)
            {
                $this->loadModel('execution')->updateProducts($execution->id);
            }
    }

    /**
     * 根据需求意向id获取demand数据集
     */
    public function getDemandByOpinionID($opinionID)
    {
        $demands =  $this->dao->select('*')
            ->from(TABLE_DEMAND)
            ->where('opinionID')->eq("$opinionID")
            ->andWhere('status')->ne('closed')
            ->fetchAll('id');
        return $demands;
    }


    /**
     * 二线单子解决时间同步demand
     * @param $demandId
     * @param $solvedTimeAboutSecondLine
     */
    public function updateDemandSolvedTime($demandId,$solvedTimeAboutSecondLine){
        $demandIdArr = array_filter(explode(',',$demandId));
        if($demandIdArr){
            foreach ($demandIdArr as $demandId){
                $demandInfo = $this->loadModel('demand')->getByID($demandId);
                $solvedTime = $demandInfo ? $demandInfo->solvedTime : '';
                if($solvedTimeAboutSecondLine > $solvedTime){
                    $this->dao->update(TABLE_DEMAND)->set('solvedTime')->eq($solvedTimeAboutSecondLine)->where('id')->eq($demandId)->exec();
                }
            }
        }
    }


    /**
     * 确认条目，同步将条目、任务、意向挂载到年度计划
     *
     * @param $project
     *
     */
    public function insertProjectPlan($oldDemand){
        //查询本项目下的意向、任务、条目
        $projectPlan = $this->dao->select('id,opinion,requirement,demand')->from(TABLE_PROJECTPLAN)
            ->where('project')->eq($oldDemand->project)
            ->andWhere('deleted')->eq(0)
            ->fetch();
        //查询条目否在其他项目中挂载，去除关系。同时意向、任务下没有条目的话，也去掉
        $projectPlanOther = $this->dao->select('id,opinion,requirement,demand')->from(TABLE_PROJECTPLAN)
            ->where('project')->ne($oldDemand->project)
            ->andWhere('demand')->like("%,".$oldDemand->id.',%')
            ->andWhere('deleted')->eq(0)
            ->fetchAll();
        if($projectPlanOther){
            //查询去除条目后内容
            foreach ($projectPlanOther as $item) {
                if(!$item) continue;
                $demands[$item->id] = str_replace(',,',',',str_replace($oldDemand->id,'',$item->demand));
            }
            //重新查询意向、任务
            foreach ($demands as $id=>$demand) {
                $demandTable = $this->dao->select('group_concat(opinionID) opinion ,group_concat(requirementID) requirement')->from(TABLE_DEMAND)
                    ->where('id')->in($demand)
                    ->andWhere('status')->ne('deleted')
                    ->fetch();
                $demandTable->opinion     = isset($demandTable->opinion) ? ','.$demandTable->opinion.',' :'';
                $demandTable->requirement = isset($demandTable->requirement) ?','.$demandTable->requirement.',' : '';
                $demandTable->demand      = isset($demand) ?','.$demand.',' : '';
                $this->dao->update(TABLE_PROJECTPLAN)->data($demandTable)->where('id')->eq($id)->exec();

            }
        }
        $opinion = ($projectPlan->opinion ? $projectPlan->opinion .','.$oldDemand->opinionID :','.$oldDemand->opinionID).',' ;
        $requirement = ($projectPlan->requirement ? $projectPlan->requirement .','.$oldDemand->requirementID :','.$oldDemand->requirementID).',' ;
        $demand = (($projectPlan->demand != '-1' && !empty($projectPlan->demand)) ? $projectPlan->demand .','.$oldDemand->id :','.$oldDemand->id).',' ;
        $data = new stdClass();
        $data->opinion = array_filter(array_unique(explode(',',$opinion))) ? ','.implode(',',array_filter(array_unique(explode(',',$opinion)))).',' :'';
        $data->requirement = array_filter(array_unique(explode(',',$requirement))) ? ','.implode(',',array_filter(array_unique(explode(',',$requirement)))).',' :'';
        $data->demand = array_filter(array_unique(explode(',',$demand)))? ','.implode(',',array_filter(array_unique(explode(',',$demand)))).',' :'';
        $this->dao->update(TABLE_PROJECTPLAN)->data($data)->where('id')->eq($projectPlan->id)->exec();
    }


    //喧喧发信
    public function getXuanxuanTargetUser($obj,$objectType, $objectID, $actionType, $actionID, $actor = '')
    {
        $demand  = $obj;
        /* Get action info. */
        $action          = $this->loadModel('action')->getById($actionID);
        $history         = $this->action->getHistory($actionID);
        $action->history = isset($history[$actionID]) ? $history[$actionID] : array();

        $sendUsers = $this->getToAndCcList($demand,$action->action);
        if (!$sendUsers) return;
        list($toList, $ccList) = $sendUsers;

        /* Send emails. */
        $status = array('wait'); //已录入发邮件，其他不发
        if(in_array($demand->status,$status)){
            $toList = $toList;
        }else{
            $toList = '';
        }

        $url = '';
        if($demand->sourceDemand == 2){
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
        $subcontent['name']      = '['.$demand->code.']';
        //标题
        $title = '';
        $actions = [];

        $mailConf   = isset($this->config->global->setDemandMail) ? $this->config->global->setDemandMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf   = json_decode($mailConf);
        if($action->action == 'deleted')
        {
            $mailConf->mailTitle = $this->lang->demand->deleteMaile;
        }
        if($action->action == 'demanddelay' || $action->action == 'reviewdelay'){
            if($demand->delayStatus != 'success' and $demand->delayStatus != 'fail'){
                $toList = $demand->delayDealUser;
            }
        }
        return ['toList'=>$toList,'subcontent'=>$subcontent,'url'=>$url,'title'=>$title,'actions'=>$actions,'mailconfig'=>json_encode($mailConf)];
    }

    /**
     * 解除状态联动
     * @param $problemID
     * @return array
     */
    public function updateLinkage($demandID)
    {
        $oldDemand = $this->getByID($demandID);
        $demand = fixer::input('post')
            ->remove('comment')
            ->get();
        $this->dao->update(TABLE_DEMAND)->data($demand)->autoCheck()
            ->where('id')->eq($demandID)
            ->exec();
        return common::createChanges($oldDemand, $demand);
    }

    /**
     * 解除变更锁
     * @Interface updateLock
     * @param $demandID
     */
    public function updateLock($demandID)
    {
        /**
         * @var opinionModel $opinionModel
         * @var requirementModel $requirementModel
         * @var demandModel $demandModel
         */
        $post = fixer::input('post')->get();
        $opinionModel = $this->loadModel('opinion');
        $requirementModel = $this->loadModel('requirement');
        if($post)
        {
            $affectsIdsList = $requirementModel->selectAffectIds($demandID); //受影响任务相关ids集合
            if(!empty($affectsIdsList))
            {
                //更新交付管理
                $opinionModel->dealChangeLock($affectsIdsList,1);
            }
            $this->dao->update(TABLE_DEMAND)->set('changeLock')->eq(1)->where('id')->eq($demandID)->exec();
        }
        return true;
    }

    /**
     * 延期
     * @param $demandID
     * @return array
     */
    public function delay($demandID)
    {
        $oldDemand = $this->getByID($demandID);
        $data = fixer::input('post')
            ->stripTags($this->config->demand->editor->delay['id'], $this->config->allowedTags)
            ->remove('uid,end')
            ->get();

        if(strtotime($data->delayResolutionDate) <= strtotime($data->originalResolutionDate)){
            $errors['delayResolutionDate'] = $this->lang->demand->delayResolutionDateError;
            return dao::$errors = $errors;
        }
        $reviewers            = $this->loadModel('modify')->getReviewers();

        if(empty($oldDemand->delayVersion)){
            $version = 1;
        }else{
            $version = $oldDemand->delayVersion+1;
        }

        //延期审批状态
        $data->delayStatus = $this->lang->demand->reviewNodeStatusList['100'];
        //延期审批版本
        $data->delayVersion = $version;
        //延期审批阶段
        $data->delayStage = 100;
        //延期申请人
        $data->delayUser = $this->app->user->account;
        //延期申请时间
        $data->delayDate = helper::now();
        //延期审批待处理人
        $data->delayDealUser = implode(',',array_keys($reviewers[2]));
        //开启事务
        $this->dao->begin();
        $this->dao->update(TABLE_DEMAND)->data($data)->autoCheck()
            ->batchCheck($this->config->demand->delay->requiredFields, 'notempty')
            ->where('id')->eq($demandID)
            ->exec();
        //部门负责人
        $this->loadModel('review')->addNode('demandDelay', $demandID, $version, array_keys($reviewers[2]), true, 'pending', 100, ['nodeCode'=>$this->lang->demand->reviewNodeStatusList['100']]);
        //分管领导
        $this->loadModel('review')->addNode('demandDelay', $demandID, $version, array_keys($reviewers[5]), true, 'wait', 200, ['nodeCode'=>$this->lang->demand->reviewNodeStatusList['200']]);
        $this->loadModel('consumed')->record('demandDelay', $demandID, '0', $this->app->user->account, '', $data->delayStatus, array());
        $this->tryError(1);
        $this->dao->commit();
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
            die(json_encode($response, JSON_UNESCAPED_UNICODE));
        }
    }

    // 延期审批
    public function delayReview($demandID){

        $oldDemand = $this->getByID($demandID);

        // 检查是否允许评审
        $res = $this->checkAllowReview($oldDemand, $this->post->delayVersion,  $this->post->delayStage, $this->app->user->account);
        if(!$res['result']){
            dao::$errors['statusError'] = $res['message'];
            return false;
        }
        if(empty($_POST['result']))
        {
            dao::$errors[] = $this->lang->demand->resultError;
            return false;
        }
        if($this->post->result == 'reject' && empty($_POST['suggest']))
        {
            dao::$errors[] = $this->lang->demand->suggestError;
            return false;
        }

        $result = $this->loadModel('review')->check('demandDelay', $demandID, $oldDemand->delayVersion, $_POST['result'], $_POST['suggest'],'','',false);
        if($result == 'pass')
        {
            //审批未到最后的节点
            if($oldDemand->delayStage < 200){
                $afterStage = $this->lang->demand->reviewNodeOrderList[$oldDemand->delayStage];  //审批通过，自动前进一步

                $next = $this->dao->select('id')->from(TABLE_REVIEWNODE)->where('objectType')->eq('demandDelay')   //查找下一节点的状态
                ->andWhere('objectID')->eq($demandID)
                    ->andWhere('version')->eq($oldDemand->delayVersion)
                    ->andWhere('status')->eq('wait')->orderBy('stage,id')->fetch('id');

                if($next)
                {
                    $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('pending')->where('id')->eq($next)->exec();  //更新下一节点的状态为pending
                    $this->dao->update(TABLE_REVIEWER)->set('status')->eq('pending')->where('node')->eq($next)->exec();

                    $this->loadModel('review');
                    $reviewers = $this->review->getReviewer('demandDelay', $demandID, $oldDemand->delayVersion, $afterStage);
                    $this->dao->update(TABLE_DEMAND)->set('delayDealUser')->eq($reviewers)->where('id')->eq($demandID)->exec();
                }

                //更新状态
                if(isset($this->lang->demand->reviewNodeStatusList[$afterStage])){
                    $status = $this->lang->demand->reviewNodeStatusList[$afterStage];
                }

                $this->dao->update(TABLE_DEMAND)->set('delayStage')->eq($afterStage)->set('delayStatus')->eq($status)->where('id')->eq($demandID)->exec();
                $this->loadModel('consumed')->record('demandDelay', $demandID, '0', $this->app->user->account, $oldDemand->delayStatus, $status, array());
            }else{
                $this->dao->update(TABLE_DEMAND)->set('delayStatus')->eq('success')->set('delayDealUser')->eq('')->set('isExtended')->eq(1)->where('id')->eq($demandID)->exec();
                $this->loadModel('consumed')->record('demandDelay', $demandID, '0', $this->app->user->account, $oldDemand->delayStatus, 'success', array());
            }
        }else{
            $this->dao->update(TABLE_DEMAND)->set('delayStatus')->eq('fail')->set('delayDealUser')->eq('')->where('id')->eq($demandID)->exec();
            $this->loadModel('consumed')->record('demandDelay', $demandID, '0', $this->app->user->account, $oldDemand->delayStatus, 'fail', array());
        }
    }

    // 检查是否允许审核
    public function checkAllowReview($demand, $version = 1,  $reviewStage = 0, $userAccount = '')
    {
        $res = array(
            'result'  => false,
            'message' => '',
        );
        if(!$demand){
            $res['message'] = $this->lang->common->errorParamId;
            return $res;
        }
        if(!$userAccount){
            $res['message'] = $this->lang->common->errorParamUser;
            return $res;
        }
        //审核节点已经经过
        if(($version != $demand->delayVersion) || ($reviewStage != $demand->delayStage)){
            $res['message'] = $this->lang->demand->nowStageError;
            return $res;
        }
        // 当前用户不允许审批
        if(!in_array($userAccount, explode(',', $demand->delayDealUser))){
            $res['message'] = $this->lang->demand->approverError;
            return $res;
        }
        // 当前状态不允许审批
        if(!in_array($demand->delayStatus, $this->lang->demand->allowReviewList)){
            $res['message'] = $this->lang->sectransfer->stateReviewError;
            return $res;
        }

        $res['result'] = true;
        return  $res;
    }
    /**
     * @param $id
     * @param string $type
     * @desc  迭代30需求条目-交付管理状态联动（实时）
     */
    public function changeBySecondLineV4($id, $type = 'modify'){
        $commentStr = '';

        $modifyLinkStatus     = $this->lang->demand->linkage['modify'];
        $modifylinkStatusAll  = array_merge($modifyLinkStatus['delivery'],$modifyLinkStatus['changeabnormal'],$modifyLinkStatus['chanereturn'],$modifyLinkStatus['onlinesuccess']);

        $outwardDeliveryLinkStatus     = $this->lang->demand->linkage['outwarddelivery'];
        $outwardDeliverylinkStatusAll  = array_merge($outwardDeliveryLinkStatus['delivery'],$outwardDeliveryLinkStatus['changeabnormal'],$outwardDeliveryLinkStatus['chanereturn'],$outwardDeliveryLinkStatus['onlinesuccess']);

        //征信交付
        $creditLinkStatus  = $this->lang->demand->linkage['credit'];
        $creditLinkStatusAll = array_merge($creditLinkStatus['delivery'], $creditLinkStatus['changeabnormal'], $creditLinkStatus['chanereturn'], $creditLinkStatus['onlinesuccess']);

        $onlineTimeList = [];//上线时间
        $onlineIdList = [];
        $statusKeys = array_keys($modifyLinkStatus); //通用
        $fileds = 'id,demandId,createdDate,code,status';
        $creditFields = 'id,demandIds as demandId,createdDate,code,status'; //征信交付查询字段
        $currentFiled = 'demandId';
        if ($type == 'modify'){
            $table = TABLE_MODIFY;
        }else if ($type == 'outwarddelivery'){
            $table = TABLE_OUTWARDDELIVERY;
        }
        if($type == 'credit'){ //todo
            $table = TABLE_CREDIT;
            $data = $this->dao->select($creditFields)->from($table)
                ->where('id')->eq($id)
                ->andWhere('demandCancelLinkage')->eq('0')
                ->andWhere('demandLinked')->eq('0')
                ->andWhere('status')->ne('waitsubmit')
                ->fetch();
        }else{
            $data = $this->dao->select($fileds)->from($table)
                ->where('id')->eq($id)
                ->andWhere('demandCancelLinkage')->eq('0')
                ->andWhere('demandLinked')->eq('0')
                ->andWhere('status')->ne('waitsubmitted')
                ->beginIF($type == 'outwarddelivery')->andWhere('isNewModifycncc')->eq('1')->fi()
                ->fetch();
        }

        if (empty($data)) return true;


        $demandId     = trim($data->$currentFiled,',');
        if ($demandId == ''){
            return true;
        }
        //查询关联的条目
        $demandList = $this->dao->select('id, status, code, dealUser, actualOnlineDate')->from(TABLE_DEMAND)
            ->where('id')->in($demandId)
            ->andwhere('status')->notIN("wait,closed,suspend,deleted,deleteout")
            ->andWhere('sourceDemand')->eq(1)
            ->andWhere('secureStatusLinkage')->eq('0')
            ->fetchall('id');
        if (empty($demandList) || $demandId == ''){
            return true;
        }
        //关联模块
        $statusLinkedModules = $this->lang->demand->statusLinkedModules;
        $secondLine = $this->dao->select('relationID as last_relation_id, relationType,objectID')
            ->from(TABLE_SECONDLINE)
            ->where('objectType')->eq('demand')
            ->andwhere('objectID')->in($demandId)
            ->andwhere('deleted')->eq(0)
            ->andwhere('relationType')->in($statusLinkedModules)
//            ->andwhere('relationID')->eq($id)
            ->orderBY("id_asc")
            ->fetchAll();
        if(empty($secondLine)){
            return true;
        }

        $arr = [];
        $modifyIds          = [];//金信变更单id
        $outwardDeliveryIds = [];//清总交付单id
        $creditIds          = [];
        foreach ($secondLine as $item) {
            if ($item->relationType == 'modify'){
                $modifyIds[] = $item->last_relation_id;
            }elseif ($item->relationType == 'outwardDelivery'){
                $outwardDeliveryIds[] = $item->last_relation_id;
            } elseif ($item->relationType == 'credit'){
                $creditIds[] = $item->last_relation_id;
            }
            $arr[$item->objectID][] = $item->last_relation_id; //按照需求条目id分组
        }
        $modifyRes = ['demandIdArr' => [],'data'=>[],'demandUnsetArr'=>[]];
        if (!empty($modifyIds)){
            $modifys = $this->dao->select($fileds.',realEndTime,externalId')->from(TABLE_MODIFY)
                ->where('id')->in($modifyIds)
                ->andWhere('demandCancelLinkage')->eq('0')
                ->andWhere('demandLinked')->eq('0')
                ->andWhere('status')->ne('waitsubmitted')
                ->fetchall('id');
            //过滤金信生产变更内部取消的数据
            foreach ($modifys as $key => $value)
            {
                if(empty($value->externalId) && $value->status == 'modifycancel')
                {
                    unset($modifys[$key]);
                }
            }
            $modifyRes = $this->getDemandArray($modifys,$modifylinkStatusAll,$demandId);
        }

        $outwardDeliveryRes = ['demandIdArr' => [],'data'=>[],'demandUnsetArr'=>[]];
        if (!empty($outwardDeliveryIds)){
            $outwardDeliverys = $this->dao->select($fileds.',modifycnccId')->from(TABLE_OUTWARDDELIVERY)
                ->where('id')->in($outwardDeliveryIds)
                ->andWhere('demandCancelLinkage')->eq('0')
                ->andWhere('demandLinked')->eq('0')
                ->andWhere('status')->ne('waitsubmitted')
                ->andWhere('deleted')->eq('0')
                ->andWhere('status')->ne('waitsubmitted')
                ->andWhere('isNewModifycncc')->eq('1')->fetchall('id');

            //查询生产变更单表，过滤内部取消的单子
            if($outwardDeliverys) {
                foreach ($outwardDeliverys as $outwardId => $outwardDelivery) {
                    $modifycnccInfoByOutwardId = $this->dao->select('id,giteeId,status')->from(TABLE_MODIFYCNCC)->where('id')->eq($outwardDelivery->modifycnccId)->andWhere('status')->eq('modifycancel')->fetch();
                    if ($modifycnccInfoByOutwardId && empty($modifycnccInfoByOutwardId->giteeId)) {
                        unset($outwardDeliverys[$outwardId]);
                    }
                }
            }
            $outwardDeliveryRes = $this->getDemandArray($outwardDeliverys,$outwardDeliverylinkStatusAll,$demandId,$modifyRes['demandUnsetArr']);
        }

        //征信交付
        $creditRes = ['demandIdArr' => [],'data'=>[],'demandUnsetArr'=>[]];
        if (!empty($creditIds)){
            $credits = $this->dao->select($creditFields.',deliveryTime,onlineTime')
                ->from(TABLE_CREDIT)
                ->where('id')->in($creditIds)
                ->andWhere('demandCancelLinkage')->eq('0')
                ->andWhere('demandLinked')->eq('0')
                ->andWhere('status')->ne('waitsubmit')
                ->fetchall('id');
            //过滤金信生产变更内部取消的数据
            foreach ($credits as $key => $value)
            {
                if($value->status == 'cancel')
                {
                    unset($credits[$key]);
                }
            }
            $creditRes = $this->getDemandArray($credits, $creditLinkStatusAll, $demandId, $modifyRes['demandUnsetArr']);
        }

        if (empty($modifyRes['demandIdArr']) && empty($outwardDeliveryRes['demandIdArr']) && empty($creditRes['demandIdArr'])){
            //没有要进行联动的数据
            return true;
        }
        $newDemand = [];
        $modifyAbnormalCode = [];
        if (!empty($modifyRes['demandIdArr']) && !empty($modifyRes['data'])){
            foreach ($modifyRes['demandIdArr'] as $demandIdVal) {
                foreach ($modifyRes['data'] as $modifyVal) {
                    if (in_array($demandIdVal,$modifyVal->demandIdArray)){
                        foreach ($modifyLinkStatus as $modifyLinkStatusKey => $modifyLinkStatusVal) {
                            if (in_array($modifyVal->status,$modifyLinkStatusVal)){
                                $modifystatusKey = array_search($modifyLinkStatusKey, $statusKeys);
                                $newDemand[$demandIdVal][] = $modifystatusKey;
                                //变更成功 联动为上线成功
                                if (!isset($onlineIdList[$demandIdVal]['status']) || $modifystatusKey < $onlineIdList[$demandIdVal]['status']){
                                    $onlineIdList[$demandIdVal]['codeStr'] = $modifyVal->code."($modifyVal->id)";
                                    $onlineIdList[$demandIdVal]['status']  = $modifystatusKey;
                                }
                                //变更异常
                                if (in_array($modifyVal->status,$modifyLinkStatus['changeabnormal'])){
                                    $modifyAbnormalCode[] = $modifyVal->code;
                                }
                                if (in_array($modifyVal->status,$modifyLinkStatus['onlinesuccess'])){
                                    if(empty($onlineTimeList[$demandIdVal]) || $modifyVal->realEndTime  > $onlineTimeList[$demandIdVal]){
                                        $onlineTimeList[$demandIdVal] = $modifyVal->realEndTime;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $outwardDeliveryAbnormalCode = [];
        if (!empty($outwardDeliveryRes['demandIdArr']) && !empty($outwardDeliveryRes['data'])){
            foreach ($outwardDeliveryRes['demandIdArr'] as $demandIdVal) {
                foreach ($outwardDeliveryRes['data'] as $outwardDeliveryVal) {
                    if (in_array($demandIdVal,$outwardDeliveryVal->demandIdArray)){
                        foreach ($outwardDeliveryLinkStatus as $outwardDeliveryLinkStatuskey => $outwardDeliveryLinkStatusVal) {
                            if (in_array($outwardDeliveryVal->status,$outwardDeliveryLinkStatusVal)){
                                $outwardDeliverystatuskey = array_search($outwardDeliveryLinkStatuskey,$statusKeys);
                                $newDemand[$demandIdVal][] = $outwardDeliverystatuskey;

                                $modifycnccInfo = $this->dao->select('id,code,actualEnd')->from(TABLE_MODIFYCNCC)->where('id')->eq($outwardDeliveryVal->modifycnccId)->fetch();
                                if (!isset($onlineIdList[$demandIdVal]['status']) || $outwardDeliverystatuskey < $onlineIdList[$demandIdVal]['status']){
                                    $onlineIdList[$demandIdVal]['codeStr'] = $modifycnccInfo->code."($modifycnccInfo->id)";
                                    $onlineIdList[$demandIdVal]['status']  = $outwardDeliverystatuskey;
                                }
                                //变更异常
                                if (in_array($outwardDeliveryVal->status,$outwardDeliveryLinkStatus['changeabnormal'])){
                                    $outwardDeliveryAbnormalCode[] = $outwardDeliveryVal->code;
                                }
                                if (in_array($outwardDeliveryVal->status,$outwardDeliveryLinkStatus['onlinesuccess'])){
                                    if(empty($onlineTimeList[$demandIdVal]) || $modifycnccInfo->actualEnd > $onlineTimeList[$demandIdVal]){
                                        $onlineTimeList[$demandIdVal] = $modifycnccInfo->actualEnd;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        //征信交付
        $creditAbnormalCode = [];
        if (!empty($creditRes['demandIdArr']) && !empty($creditRes['data'])){
            foreach ($creditRes['demandIdArr'] as $demandIdVal) {
                foreach ($creditRes['data'] as $creditVal) {
                    if (in_array($demandIdVal, $creditVal->demandIdArray)){
                        foreach ($creditLinkStatus as $creditLinkStatusKey => $creditLinkStatusVal) {
                            if (in_array($creditVal->status, $creditLinkStatusVal)){
                                $creditstatusKey = array_search($creditLinkStatusKey, $statusKeys);
                                $newDemand[$demandIdVal][] = $creditstatusKey;

                                //变更成功 联动为上线成功
                                if (!isset($onlineIdList[$demandIdVal]['status']) || $creditstatusKey < $onlineIdList[$demandIdVal]['status']){
                                    $onlineIdList[$demandIdVal]['codeStr'] = $creditVal->code."($creditVal->id)";
                                    $onlineIdList[$demandIdVal]['status']  = $creditstatusKey;
                                }
                                //变更异常
                                if (in_array($creditVal->status, $creditLinkStatus['changeabnormal'])){
                                    $creditAbnormalCode[] = $creditVal->code;
                                }
                                if (in_array($creditVal->status, $creditLinkStatus['onlinesuccess'])){
                                    if(empty($onlineTimeList[$demandIdVal]) || $creditVal->onlineTime  > $onlineTimeList[$demandIdVal]){
                                        $onlineTimeList[$demandIdVal] = $creditVal->onlineTime;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $unsetDemand = array_merge($modifyRes['demandUnsetArr'],$outwardDeliveryRes['demandUnsetArr'], $creditRes['demandUnsetArr']);
        $demandListId = array_column($demandList,'id');
        $this->app->loadLang("action");
        if (!empty($unsetDemand)){
            foreach ($unsetDemand as $uk=>$uv) {
                unset($newDemand[$uv]);
            }
        }
        if (empty($newDemand)) return true;
        foreach ($newDemand as $k=>$v) {
            //取最小状态值
            $status = $statusKeys[min($v)];

            if ($status != $demandList[$k]->status && in_array($k, $demandListId)){
                //状态不一致在进行联动
                $params = new stdClass();
                $params->status              = $status;
                $params->actualOnlineDate    = null;
                if ($status == 'onlinesuccess'){
                    $params->actualOnlineDate    = $onlineTimeList[$k];
                }
                $this->dao->update(TABLE_DEMAND)->data($params)->where('id')->eq($k)->exec();
                $this->loadModel('consumed')->recordAuto('demand', $k, 0, $demandList[$k]->status, $status);
                $code = $onlineIdList[$k]['codeStr'];
                $comment = sprintf($this->lang->action->actionNotesDesc,$code.' : '.$this->lang->demand->statusList[$demandList[$k]->status], $this->lang->demand->statusList[$status]);
                $this->loadModel('action')->create('demand', $k, 'linkagestatus', $comment,'','guestjk');
            }

//            //当需求条目不为已交付、上线成功、变更单异常、变更单退回
//            if (!in_array($status,['delivery','onlinesuccess','changeabnormal','chanereturn'])){
//                $this->dao->update(TABLE_DEMAND)->set('solvedTime')->eq(null)->where('id')->eq($k)->exec();
////                    $problemModel->dealSolveTime($outward->demandId,'demand',$outward->code);
//            }
            //更新需求和问题解决时间
            /** @var problemModel $problemModel */
            $problemModel = $this->loadModel('problem');
            $problemModel->getAllSecondSolveTime($k,'demand');

        }
        //异常联动过一次后就不再参与状态联动
        if (!empty($modifyAbnormalCode)){
            $this->dao->update(TABLE_MODIFY)->set('demandLinked')->eq('1')->where('code')->in(array_unique($modifyAbnormalCode))->exec();
        }
        if (!empty($outwardDeliveryAbnormalCode)){
            $this->dao->update(TABLE_OUTWARDDELIVERY)->set('demandLinked')->eq('1')->where('code')->in(array_unique($outwardDeliveryAbnormalCode))->exec();
        }
        if (!empty($creditAbnormalCode)){
            $this->dao->update(TABLE_CREDIT)->set('demandLinked')->eq('1')->where('code')->in(array_unique($creditAbnormalCode))->exec();
        }
    }

    /**
     * @desc 获取要进行联动的需求条目和交付管理单子数据
     * @param $data 交付管理列表
     * @param $statusList 要进行联动的状态
     * @param $demandId 需求条目id
     * @return array
     */
    public function getDemandArray($data,$statusList,$demandId,$demandUnsetArr = []){
        $demandIdArr  = explode(',',$demandId);
        $demandUnsetArr = [];
        foreach ($data as $modifyKey => $modify) {
            $modify->demandIdArray = explode(',',trim($modify->demandId,','));
            //如果存在不需要进行联动的单子 则条目不再进行联动
            if (!in_array($modify->status,$statusList)){
                foreach (explode(',',$demandId) as $item) {
                    if (in_array($item,$modify->demandIdArray)){
                        $demandKey = array_search($item, $demandIdArr);
                        unset($data[$modifyKey]);
                        if ($demandKey >= 0 && $demandKey !== false){
                            $demandUnsetArr[] = $demandIdArr[$demandKey];
                            unset($demandIdArr[$demandKey]);
                        }
                    }
                }
            }
        }
        //第二次处理的数据需要将第一次删除不需要处理的条目同步删除
        if ($demandUnsetArr){
            foreach ($demandUnsetArr as $item) {
                unset($demandIdArr[$item]);
            }
        }
        return ['demandIdArr' => $demandIdArr,'data'=>$data,'demandUnsetArr'=>$demandUnsetArr];
    }
    //状态联动，查询未删除，不包含变更单异常的
    public function getByRequirementIdLink($field = '*',$requirementID)
    {
        return $this->dao->select($field)->from(TABLE_DEMAND)
            ->where('requirementID')->eq($requirementID)
            ->andWhere('status')->notIN("deleted,changeabnormal")
            ->fetchAll();
    }
    //判断当前状态是否允许编辑
    public function checkAllowEdit($demand){
        if ($this->app->user->account =='admin') return true;
        if ($this->app->user->account == $demand->createdBy){
            if (in_array($demand->status,array('wait','chanereturn'))) return true;
            if ($demand->status == 'feedbacked'){
                $allowEditStatus = ['waitsubmitted','reviewfailed','reject','deleted'];
                $findInSet = '(FIND_IN_SET("'.$demand->id.'",demandId))';
                $modify  = $this->dao->select('id')->from(TABLE_MODIFY)->where($findInSet)->andWhere('status')->notIN($allowEditStatus)->fetch();
                $outward = $this->dao->select('id')->from(TABLE_OUTWARDDELIVERY)->where($findInSet)->andWhere('status')->notIN($allowEditStatus)->andWhere('deleted')->eq('0')->fetch();
                if (empty($modify) && empty($outward)){
                    return true;
                }
            }
        }
    }

    /**
     * @Notes:查找变更已锁的需求条目
     * @Date: 2023/8/31
     * @Time: 17:31
     * @Interface getDemandLockByIds
     * @param $demandIDs
     * @param $select
     * @return mixed
     */
    public function getDemandLockByIds($demandIDs, $select = '*')
    {
        return $this->dao->select($select)->from(TABLE_DEMAND)->where('id')->in($demandIDs)->andWhere('changeLock')->eq(2)->fetchAll('id');
    }
    //判断当前状态是否允许挂起、关闭
    public function checkAllowSuspend($demand){
        $allowStatus = ['waitsubmitted','reviewfailed','reject','wait','cmconfirmed','groupsuccess','managersuccess'];
        $findInSet = '(FIND_IN_SET("'.$demand->id.'",demandId))';
        $modify  = $this->dao->select('id')->from(TABLE_MODIFY)->where($findInSet)->andWhere('status')->notIN($allowStatus)->fetch();
        $outward = $this->dao->select('id')->from(TABLE_OUTWARDDELIVERY)->where($findInSet)->andWhere('status')->notIN($allowStatus)->andWhere('deleted')->eq('0')->fetch();
        if (empty($modify) && empty($outward)){
            return true;
        }
        return false;
    }


    /**
     * @Notes:投产移交单和需求条目的状态联动
     * ①只联动第二阶段
     * 待外部审批-》已交付
     * 材料退回-》变更单退回
     * 投产失败、部分成功-》变更单异常
     * 投产成功-》上线成功
     *
     * @Date: 2024/2/4
     * @Time: 15:29
     * @Interface putproductionAndDemandStatusChange
     * @param $demandIds
     * @param $putproductionInfo
     * @param $putproductionStatus
     */
    public function putproductionAndDemandStatusChange($demandIds,$putproductionInfo,$putproductionStatus)
    {
        $this->app->loadLang('action');
        $putproductionCode   = $putproductionInfo->code;
        $demandInfo = $this->dao->select('id,status')->from(TABLE_DEMAND)->where('id')->in($demandIds)->andWhere('status')->notIn("closed,suspend,deleted,deleteout")->fetchAll();
        if($demandInfo)
        {
            //构造符合入库的数据
            $data = new stdClass();
            $demandStatus = '';
            switch ($putproductionStatus)
            {
                case 'waitexternalreview'://待外部审批
                    //二线处理通过前一个节点时间
                    $nodes = $this->loadModel('iwfp')->getAllVersionReviewNodes($putproductionInfo->workflowId);
                    $count = count($nodes);
                    $solvedTime = '';
                    if($count > 0)
                    {
                        $foreachData = $nodes[$count] ?? [];
                        foreach ($foreachData as $key => $foreach)
                        {
                            //二线审批且通过
                            if($foreach['nodeName'] == 'waitproduct' && $foreach['result'] == 'pass')
                            {
                                //获取二线通过前一节点
                                for($t = $key-1; $t >= 0; $t--)
                                {
                                    if($foreachData[$t]['result'] == 'pass')
                                    {
                                        $solvedTime = $foreachData[$t]['dealDate'];
                                        break;
                                    }
                                }
                            }

                        }

                    }

                    $demandStatus = 'delivery';
                    $data->solvedTime = $solvedTime;
                    break;
                case 'filereturn'://材料退回
                    $demandStatus = 'chanereturn';
                    break;
                case 'putproductionfail'://投产失败
                    $demandStatus = 'changeabnormal';
                    break;
                case 'successpart'://部分成功
                    $demandStatus = 'changeabnormal';
                    break;
                case 'success'://投产成功
                    $demandStatus = 'onlinesuccess';
                    $data->actualOnlineDate = $putproductionInfo->realEndTime;
                    $data->dealUser = '';
                    break;
            }

            $data->status = $demandStatus;
            //入库
            if(!empty($demandStatus))
            {
                foreach($demandInfo as $demand)
                {
                    $this->dao->update(TABLE_DEMAND)->data($data)->where('id')->eq($demand->id)->exec();
                    $comment = sprintf($this->lang->action->actionNotesDesc,$putproductionCode.' : '.$this->lang->demand->statusList[$demand->status], $this->lang->demand->statusList[$demandStatus]);
                    $this->loadModel('action')->create('demand', $demand->id, 'linkagestatus', $comment,'','guestjk');
                    $this->loadModel('consumed')->record('demand', $demand->id, 0, 'guestjk', $demand->status, $demandStatus);
                }
            }


        }


    }

    /**
     * 获得列表
     *
     * @param $demandIds
     * @param $select
     * @return array
     */
    public function getListByIds($demandIds, $select){
        $data = [];
        if(!($demandIds)){
            return $data;
        }
        $ret = $this->dao->select($select)
            ->from(TABLE_DEMAND)
            ->where('status')->ne('deleted')
            ->andWhere('id')->in($demandIds)
            ->orderBy('id_desc')
            ->fetchPairs();
        if($ret){
            $data = $ret;
        }
        return $data;
    }



    /**
     * 第一次按照计划完成时间提前提醒
     *
     * @return bool
     * @throws Exception
     */
    public function remindToEndMailFirst(){
        //是否有发送邮件权限
        if(!$this->loadModel('common')->isSetMessage('mail', 'demand', 'toEndRemindMailFirst')){
            return false;
        }

        if(!helper::isWorkDay(date('Y-m-d H:i:s'))){
            return false;
        }
        $toEndDay = $this->lang->demand->demandOutTime['demandToOutReferPlanEnd_1'] ? $this->lang->demand->demandOutTime['demandToOutReferPlanEnd_1']: 0 ; //即将超时天数
        if($toEndDay <= 0){
            return false;
        }
        $this->remindToEndMail($toEndDay);
        return true;
    }

    /**
     * 第二次按照计划完成时间提前提醒
     *
     * @return bool
     * @throws Exception
     */
    public function remindToEndMailSecond(){
        if(!$this->loadModel('common')->isSetMessage('mail', 'demand', 'toEndRemindMailSecond')){
            return false;
        }

        if(!helper::isWorkDay(date('Y-m-d H:i:s'))){
            return false;
        }
        $toEndDay = $this->lang->demand->demandOutTime['demandToOutReferPlanEnd_2'] ? $this->lang->demand->demandOutTime['demandToOutReferPlanEnd_2']: 0 ; //即将超时天数
        if($toEndDay <= 0){
            return false;
        }
        $this->remindToEndMail($toEndDay);
        return true;
    }

    /**
     * 提示即将超期邮件
     *
     * @param int $toEndDay
     * @return bool
     */
    public function remindToEndMail($toEndDay = 0){
        $today = helper::today();
        $minEndDate = helper::getTrueWorkDay($today, $toEndDay, true);
        $maxEndDate = helper::getTrueWorkDay($today, $toEndDay +1, true);
        $requirementList = $this->dao->select('id,code,name,planEnd,status')
            ->from(TABLE_REQUIREMENT)
            ->where('status')->notIN('deleted,deleteout')
            ->andWhere('planEnd')->ge($minEndDate)
            ->andWhere('planEnd')->lt($maxEndDate)
            ->fetchAll('id');
        if(empty($requirementList)){
            return true;
        }
        $requirementIds = array_column($requirementList, 'id');
        //需求条目列表
        $demandList = $this->dao->select('id,requirementID,code,title,status,createdBy,dealUser,acceptUser,acceptDept')
            ->from(TABLE_DEMAND)
            ->where('requirementID')->in($requirementIds)
            ->andWhere('status')->notin('suspend,closed,deleteout,onlinesuccess,deleted') //产品确认状态
            ->fetchAll();
        if(empty($demandList)){
            return true;
        }
        $userAccounts = array_column($demandList, 'acceptUser');
        $acceptUserList = $this->loadModel('user')->getUserInfoListByAccounts($userAccounts, 'account,realname');
        //加载需求任务语言
        $this->app->loadLang('requirement');
        $mailAcceptUserData  = [];
        $mailCreatedUserData = [];
        $mailAcceptDeptData  = [];
        foreach ($demandList as $val){
            $demandId = $val->id;
            $val->demandStatus = zget($this->lang->demand->statusList, $val->status);
            $requirementID   = $val->requirementID;
            $requirementInfo = zget($requirementList, $requirementID);
            $requirementCode = $requirementInfo->code;
            $requirementName = $requirementInfo->name;
            $requirementPlanEnd = $requirementInfo->planEnd;
            $requirementStatus = $requirementInfo->status;
            $val->requirementCode = $requirementCode;
            $val->requirementName = $requirementName;
            $val->requirementPlanEnd = $requirementPlanEnd;
            $val->requirementStatus  = zget($this->lang->requirement->statusList, $requirementStatus);
            $userInfo = zget($acceptUserList, $val->acceptUser, new stdClass());
            $val->acceptUserName = zget($userInfo, 'realname');
            $createdBy  = $val->createdBy;
            $acceptUser = $val->acceptUser;
            $acceptDept = $val->acceptDept;
            if($acceptUser){
                $mailAcceptUserData[$acceptUser][$demandId] = $val;
            }
            if($acceptDept){
                $mailAcceptDeptData[$acceptDept][$demandId] = $val;
            }
            //创建人
            if($createdBy){
                if(!isset($mailAcceptUserData[$createdBy][$demandId])){ //研发责任人和创建人是同一人时，按照研发责任人发送即可
                    $mailCreatedUserData[$createdBy][$demandId] = $val;
                }
            }
        }

        $setMail = 'remindtoendmail';
        $mailTitle = sprintf($this->lang->demand->remindToEndMail, $toEndDay);
        if($mailAcceptUserData){
            $ccList = '';
            foreach ($mailAcceptUserData as $account => $demandData){
                $toList = $account;
                $this->sendmailSummary($demandData, $setMail, 'demand', $toList, $ccList, 'remindtoendmail', $mailTitle);
            }
        }

        //给创建人发邮件
        $isMailCreateUser = $this->lang->demand->demandOutTime['demandToOutReferPlanEndIsCreateUser'];
        if($isMailCreateUser == '1' && $mailCreatedUserData){
            $ccList = '';
            foreach ($mailCreatedUserData as $account => $demandData){
                $toList = $account;
                $this->sendmailSummary($demandData, $setMail, 'demand', $toList, $ccList, 'remindtoendmail', $mailTitle);
            }
        }

        //给部门领导发邮件
        $isManagerCreateUser = $this->lang->demand->demandOutTime['demandToOutReferPlanEndIsManagerUser'];
        if($isManagerCreateUser == '1' && $mailAcceptDeptData){
            $ccList = '';
            $deptIds  = array_keys($mailAcceptDeptData);
            $deptList = $this->loadModel('dept')->getDeptListByIds($deptIds, 'id,name');
            if($deptList){
                $deptList = array_column($deptList, null, 'id');
            }
            foreach ($mailAcceptDeptData as $deptId => $demandData){
                $deptInfo = zget($deptList, $deptId, new stdClass());
                $deptName = zget($deptInfo, 'name');
                $mailTitle = sprintf($this->lang->demand->remindManagerToEndMail, $deptName, $toEndDay);
                $toList = trim(zget($this->config->demand->deptLeadersList, $deptId, ''), ',');
                $this->sendmailSummary($demandData, $setMail, 'demand', $toList, $ccList, 'remindmanagertoendmail', $mailTitle);
            }
        }
        return true;
    }

    /**
     * 发送汇总邮件
     *
     * @param $data
     * @param $setMail
     * @param $browseType
     * @param $toList
     * @param $ccList
     * @param $viewName
     * @param bool $mailTitle
     * @return bool
     */
    function sendmailSummary($data, $setMail, $browseType, $toList, $ccList, $viewName, $mailTitle = false)
    {
        if(!($toList || $ccList)){
            return false;
        }
        $this->loadModel('mail');
        $users = $this->loadModel('user')->getPairs('noletter');

        /* 获取后台通知中配置的邮件发信。*/
        $this->app->loadLang('custommail');
        $mailConf   = $this->config->global->$setMail ?? '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf   = json_decode($mailConf);

        //邮件标题
        $mailTitle = !$mailTitle ? vsprintf($mailConf->mailTitle, $mailConf->variables) : $mailTitle;

        //邮件内容
        $oldcwd     = getcwd();
        $modulePath = $this->app->getModulePath($appName = '', $browseType);
        $viewFile   = $modulePath . 'view/' . $viewName . '.html.php';
        chdir($modulePath . 'view');
        if(file_exists($modulePath . 'ext/view/' . $viewName . '.html.php')) {
            $viewFile = $modulePath . 'ext/view/' . $viewName . '.html.php';
            chdir($modulePath . 'ext/view');
        }

        ob_start();
        include $viewFile;
        foreach(glob($modulePath . 'ext/view/sendmail.*.html.hook.php') as $hookFile) include $hookFile;
        $mailContent = ob_get_contents();
        ob_end_clean();
        chdir($oldcwd);
        //邮件扩展信息
        $extendInfo = new  stdClass();
        $extendInfo->objectType = $browseType;
        $extendInfo->actionType = $viewName;
        $this->mail->send($toList, $mailTitle, $mailContent, $ccList, false, $extendInfo);

        if($this->mail->isError()) error_log(join("\n", $this->mail->getError()));
    }

    /**
     * 获得需求任务id
     *
     * @param $projectId
     * @return array
     */
    public function getRequirementIdsByProject($projectId){
        $data = [];
        if(!$projectId){
            return $data;
        }
        $ret = $this->dao->select('requirementID')
            ->from(TABLE_DEMAND)
            ->where('status')->notIN('closed,deleted')
            ->andWhere('project')->eq($projectId)
            ->groupBy('requirementID')
            ->fetchAll();
        if($ret){
            $data = array_column($ret, 'requirementID');
        }
        return $data;
    }

    /**
     * 获得不包含项目的需求条目
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
        $ret = $this->dao->select('requirementID')
            ->from(TABLE_DEMAND)
            ->where('status')->notIN('closed,deleted')
            ->andWhere('requirementID')->in($requirementIds)
            ->andWhere('project')->ne('')
            ->andWhere('project')->ne($projectId)
            ->groupBy('requirementID')
            ->fetchAll();
        if($ret){
            $data = array_column($ret, 'requirementID');
        }
        return $data;
    }

    /**
     * 超时考核信息是否可见
     *
     * @param $account
     * @return bool
     */
    public function getIsOverDateInfoVisible($account){
        $isOverDateInfoVisible = false;
        $overDateInfoVisibleUsers = explode(',', $this->config->demand->overDateInfoVisible);
        $overDateInfoVisibleUsers[] = 'admin';
        if(in_array($account, $overDateInfoVisibleUsers)){
            $isOverDateInfoVisible = true;
        }
        return $isOverDateInfoVisible;
    }
}
