<?php

class demandDemandcollection extends demandcollectionModel
{
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
    public function syncCreated()
    {
        $this->app->loadLang('opinioninside');
        $this->app->loadLang('requirementinside');
        $this->app->loadLang('demand');
        $this->app->loadLang('demandinside');
        $this->app->loadConfig('demandinside');

        /**
         * @var requirementinsideModel $requirementModel
         * @var opinioninsideModel $opinionModel
         */
        $opinionModel = $this->loadModel('opinioninside');
        $requirementModel = $this->loadModel('requirementinside');

        $data = fixer::input('post')
            ->remove('files,labels,consumed,uid,mailto,flag,executionid,syncType,demandId')
            ->stripTags($this->config->demandinside->editor->create['id'], $this->config->allowedTags)
            ->get();


        $requirementObj   = $requirementModel->getByID($data->requirementID);

        if (99999 == $data->product) {
            $data->productPlan = 1;
        }//产品选无 版本也是无
        if ($data->product > 0 && 0 == $data->productPlan) {
            return dao::$errors['productPlan'] = $this->lang->demandinside->productPlanEmpty;
        }
        if (!isset($data->app)) {
            return dao::$errors['app'] = $this->lang->demandinside->appEmpty;
        }
        //计划完成时间
        if (!$this->loadModel('common')->checkJkDateTime($data->end)) {
            return dao::$errors['end'] = sprintf($this->lang->demandinside->emptyObject, $this->lang->demandinside->end);
        }
        //计划完成时间
        if (!$this->loadModel('common')->checkJkDateTime($data->end)) {
            return dao::$errors['end'] = sprintf($this->lang->demand->emptyObject, $this->lang->demand->end);
        }
        //计划完成时间不允许大于所属任务的计划完成时间 只针对内部自建
        if (strtotime($data->end) > strtotime($requirementObj->planEnd)) {
            return dao::$errors['end'] = $this->lang->requirementinside->editEndSubdivideDemandTip;
        }

        $data->createdBy   = $this->app->user->account;
        $data->createdDate = helper::today();
        $data->createdDept = $this->app->user->dept;
        $data->rcvDate     = helper::now();

        if (!empty($data->opinionID)) {
            $opinion      = $opinionModel->getByID($data->opinionID);
            $data->type   = empty($opinion->sourceMode) ? '' : $opinion->sourceMode;
            $data->source = empty($opinion->sourceName) ? '' : $opinion->sourceName;
            $data->union  = empty($opinion->union) ? '' : $opinion->union;
        }

        //实施部门需要根据实施责任人查询，构造数据
        if (!empty($data->acceptUser)) {
            $acceptUser       = $this->loadModel('user')->getByAccount($data->acceptUser);
            $data->acceptDept = $acceptUser->dept;
        }

        //解决数据库类型自动检测的问题，需要是整型
        if (empty($data->productPlan)) {
            $data->productPlan = 0;
        }

        //所属项目
        if (empty($data->project)) {
            return dao::$errors['project'] = sprintf($this->lang->demandinside->emptyObject, $this->lang->demandinside->project);
        }

        //由于下一节点处理人提示为待处理人，需单独处理
        if (empty($data->dealUser)) {
            return dao::$errors['dealUser'] = sprintf($this->lang->demandinside->emptyObject, $this->lang->demandinside->PO);
        }

        if (!$this->loadModel('common')->checkJkDateTime($data->endDate)) {
            return dao::$errors['endDate'] = sprintf($this->lang->demandinside->emptyObject, $this->lang->demandinside->endDate);
        }
        if (!$this->loadModel('common')->checkJkDateTime($data->end)) {
            return dao::$errors['end'] = sprintf($this->lang->demandinside->emptyObject, $this->lang->demandinside->end);
        }

        if ($data->progress) {
            $users          = $this->loadModel('user')->getPairs('noclosed');
            $data->progress = '<span style="background-color: #ffe9c6">' . helper::now() . ' 由<strong>' . zget($users, $this->app->user->account, '') . '</strong>新增' . '<br></span>' . $data->progress;
        }

        $data->lastDealDate = date('Y-m-d');
        $data               = $this->loadModel('file')->processImgURL($data, $this->config->demandinside->editor->create['id'], $this->post->uid);

        // 倒挂状态修改
        $opinionObj     = $this->loadModel('opinioninside')->getByID($data->opinionID);
        $requirementObj = $this->loadModel('requirementinside')->getByID($data->requirementID);

        //产品和版本不是无，应用系统只能选择一个
        if ('99999' != $this->post->product && '1' != $this->post->productPlan) {
            $apps = explode(',', trim($data->app, ','));
            if (count($apps) > 1) {
                return dao::$errors['app'] = $this->lang->demandinside->productAndPlanTips;
            }
        }
        $data->secondLineDevelopmentRecord = 2;
        // 迭代三十四 二线实现项目 二线月报跟踪标记位标记为纳入 项目实现为不纳入
        if ('second' == $data->fixType) {
            $data->secondLineDevelopmentRecord = 1;
            // 判断二线实现的解决方案必须为二线项目。
            $plan = $this->dao->select('secondLine')->from(TABLE_PROJECTPLAN)->where('deleted')->eq('0')->andWhere('project')->eq($data->project)->fetch();
            if (empty($plan->secondLine)) {
                return dao::$errors = ['' => $this->lang->demandinside->noSecondLinse];
            }
        }
        // 根据【所属应用系统】处理系统分类字段的值。
        $paymentIdList = [];
        if (isset($data->app)) {
            foreach (explode(',', $data->app) as $appID) {
                if (!$appID) {
                    continue;
                }
                $paymentType = $this->dao->select('isPayment')->from(TABLE_APPLICATION)->where('id')->eq($appID)->fetch('isPayment');
                if ($paymentType) {
                    $paymentIdList[] = $paymentType;
                }
            }
            $data->isPayment = implode(',', $paymentIdList);
        }
        $data->sourceDemand = 2; //内部
        $data->collectionId = ',' . $data->collectionId . ',';
        //需求任务待处理人为空的时候，倒挂成功后将倒挂人显示为需求任务的待处理人
        if (in_array($requirementObj->status, ['delivered', 'onlined'])) {
            $this->dao->update(TABLE_REQUIREMENT)->set('dealUser')->eq($this->app->user->account)->where('id')->eq($data->requirementID)->exec();
        }
        $this->dao->insert(TABLE_DEMAND)->data($data)
            ->batchCheck($this->config->demandinside->create->requiredFields, 'notempty')
            ->exec();
        $demandID = $this->dao->lastInsertId();

        $date   = date('Y-m-d');
        $number = $this->dao->select('count(id) c')->from(TABLE_DEMAND)->where('createdDate')->eq($date)->andWhere('sourceDemand')->eq(2)->fetch('c');
        $code   = 'CFIT-WD-' . date('Ymd-') . sprintf('%02d', $number);
        $this->dao->update(TABLE_DEMAND)->set('code')->eq($code)->where('id')->eq($demandID)->exec();

        // 同步更新需求任务和需求意向的状态
        //只有非已拆分时才更新状态为已拆分，增加状态流转
        if ('splited' != $requirementObj->status) {
            $this->loadModel('requirementinside')->updateRequirement($data->requirementID, 'splited');
            $this->loadModel('consumed')->record('requirement', $data->requirementID, 0, $this->app->user->account, $requirementObj->status, 'splited');
        }

        //只有非已拆分时才更新状态为已拆分，增加状态流转以及历史记录
        if ('subdivided' != $opinionObj->status) {
            $this->loadModel('opinioninside')->updateStatusById('subdivided', $data->opinionID);
            $this->loadModel('consumed')->record('opinion', $data->opinionID, 0, $this->app->user->account, $opinionObj->status, 'subdivided');
            $subdividedArray = $this->loadModel('requirementinside')->insertActionArray($data->opinionID, $requirementObj->code, $opinionObj->status);
            if (!empty($subdividedArray)) {
                $langStatus = $this->lang->opinioninside->statusList;
                $this->loadModel('action')->createActions('opinion', $subdividedArray, 'subdividedactual', $langStatus, 2);
            }
        }

        if (!dao::isError()) {
            $this->loadModel('consumed')->record('demand', $demandID, 0, $this->app->user->account, '', 'wait', $this->post->mailto);

            $this->loadModel('file')->updateObjectID($this->post->uid, $demandID, 'demand');

            //同步需求收集附件
            $this->syncFile(trim($data->collectionId, ','), $demandID);

            $this->file->saveUpload('demand', $demandID);

            $this->dao->update(TABLE_DEMANDCOLLECTION)->set('demandId')->eq($demandID)->where('id')->eq(trim($data->collectionId, ','))->exec();
        }

        return $demandID;
    }

    /**
     * 合并已有需求条目
     * @param $demandID
     * @return array|string
     */
    public function syncUpdate($demandID)
    {
        $this->app->loadLang('requirementinside');
        $this->app->loadLang('demand');

        /**
         * @var requirementinsideModel $requirementModel
         * @var demandinsideModel $demandModel
         */
        $requirementModel = $this->loadModel('requirementinside');
        $demandModel = $this->loadModel('demandinside');

        $oldDemand = $demandModel->getByID($demandID);
        $requirementObj = $requirementModel->getByID($oldDemand->requirementID);

        $demand = fixer::input('post')
            ->join('app', ',')
            ->join('requirementinside', ',')
            ->remove('uid,files,labels,consumed,mailto,flag,executionid,opinionID,requirementID,syncType,demandId')
            ->stripTags($this->config->demandinside->editor->edit['id'], $this->config->allowedTags)
            ->get();

        if($demand->product == 99999) $demand->productPlan = 1;//产品选无 版本也是无
        if ($demand->product > 0 && $demand->productPlan == 0) {
            return dao::$errors['productPlan'] = $this->lang->demandinside->productPlanEmpty;
        }
        if (!isset($demand->app)) {
            return dao::$errors['app'] = $this->lang->demandinside->appEmpty;
        }

        //期望完成时间
        if(!$this->loadModel('common')->checkJkDateTime($demand->endDate)) {
            return dao::$errors['endDate'] =  sprintf($this->lang->demandinside->emptyObject, $this->lang->demandinside->endDate);
        }

        //计划完成时间
        if(!$this->loadModel('common')->checkJkDateTime($demand->end)) {
            return dao::$errors['end'] =  sprintf($this->lang->demandinside->emptyObject, $this->lang->demandinside->end);
        }

        //计划完成时间不允许大于所属任务的计划完成时间 只针对内部自建
        if (strtotime($demand->end) > strtotime($requirementObj->planEnd)) {
            return dao::$errors['end'] = $this->lang->requirementinside->editEndSubdivideDemandTip;
        }

        //所属项目
        if (empty($demand->project)) {
            return dao::$errors['project'] =  sprintf($this->lang->demandinside->emptyObject, $this->lang->demandinside->project);
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
            return dao::$errors['dealUser'] = sprintf($this->lang->demandinside->emptyObject, $this->lang->demandinside->PO);
        }

        $demand->editedBy = $this->app->user->account;
        $demand->editedDate = helper::now();
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
                return dao::$errors['app'] = $this->lang->demandinside->productAndPlanTips;
            }
        }

        $demand->secondLineDevelopmentRecord = 2;
        /*迭代三十四 二线实现项目 二线月报跟踪标记位标记为纳入 项目实现为不纳入*/
        if($demand->fixType == 'second') {
            $demand->secondLineDevelopmentRecord = 1;
            // 判断二线实现的解决方案必须为二线项目。
            $plan = $this->dao->select('*')->from(TABLE_PROJECTPLAN)->where('deleted')->eq(0)->andWhere('project')->eq($demand->project)->fetch();
            if(empty($plan->secondLine)) return dao::$errors = array('' => $this->lang->demandinside->noSecondLinse);
        }

        // 根据【所属应用系统】处理系统分类字段的值。
        $paymentIdList = array();
        if(isset($demand->app)) {
            foreach(explode(',', $demand->app) as $appID) {
                if(!$appID) continue;
                $paymentType = $this->dao->select('isPayment')->from(TABLE_APPLICATION)->where('id')->eq($appID)->fetch('isPayment');
                if($paymentType) $paymentIdList[] = $paymentType;
            }
            $demand->isPayment = implode(',', $paymentIdList);
        }

        //待处理人发生变化，忽略自动恢复
        if($oldDemand->dealUser != $demand->dealUser) {
            $demand->ignoreStatus = 0;
        }

        $collectionIds = explode(',', trim($oldDemand->collectionId, ','));
        if(empty($collectionIds)){
            $demand->collectionId = ','. $demand->collectionId . ',';
        }elseif(!in_array($demand->collectionId, $collectionIds)){
            $demand->collectionId = $oldDemand->collectionId . $demand->collectionId . ',';
        }else{
            unset($demand->collectionId);
        }
        $demand = $this->loadModel('file')->processImgURL($demand, $this->config->demandinside->editor->edit['id'], $this->post->uid);

        $this->dao->update(TABLE_DEMAND)->data($demand)
            ->batchCheck($this->config->demandinside->edit->requiredFields, 'notempty')
            ->where('id')->eq($demandID)
            ->exec();

        if(dao::isError()) {
            return dao::$errors;
        }

        $this->dao->update(TABLE_DEMANDCOLLECTION)->set('demandId')->eq($demandID)->where('id')->eq($this->post->collectionId)->exec();

        if($oldDemand->status != 'wait'){
            $this->loadModel('consumed')->record('demand', $demandID, 0, $this->app->user->account, $oldDemand->status, $demand->status);
        }
        $this->loadModel('file')->updateObjectID($this->post->uid, $demandID, 'demand');
        $this->file->saveUpload('demand', $demandID);
        $this->syncFile($this->post->collectionId, $demandID);

        return common::createChanges($oldDemand, $demand);
    }

    /**
     * 获取需求意向下拉框
     * @return mixed
     */
    public function getPairsByOpinion()
    {
        $this->app->loadLang('opinioninside');

        $statusList = $this->lang->opinioninside->statusList;

        unset($statusList['deleted'], $statusList['created'], $statusList['reject'], $statusList['waitupdate']); //未删除
         //已录入
        //审核未通过
        //待更新

        $list = $this->dao->select("id,concat(code,'_',IFNULL(name,'')) as code")->from(TABLE_OPINION)
            ->where('status')->in(array_keys($statusList))
            ->andWhere('sourceOpinion')->eq('2')
            ->orderBy('id_desc')
            ->fetchPairs();

        return ['0' => ''] + $list;
    }

    /**
     * 获取需求下拉框
     * @param  mixed $opinionId
     * @return mixed
     */
    public function getPairsByRequirement($opinionId = 0)
    {
        $list = $this->dao->select("id,concat(code,'_',IFNULL(name,'')) as code")->from(TABLE_REQUIREMENT)
            ->where('status')->notIn(['deleted'])
            ->andWhere('sourceRequirement')->eq('2')
            ->beginIF($opinionId > 0)->andWhere('opinion')->eq($opinionId)->fi()
            ->orderBy('id_desc')
            ->fetchPairs();

        return ['0' => ''] + $list;
    }

    /**
     * 获取需求条目下拉框
     * @param string[] $status
     * @return mixed
     */
    public function getPairsByDemand($status)
    {
        $list = $this->dao->select("id,concat(code,'_',IFNULL(title,'')) as code")->from(TABLE_DEMAND)
            ->where('status')->notIn($status)
            ->andWhere('sourceDemand')->eq('2')
            ->orderBy('id_desc')
            ->fetchPairs();

        return ['0' => ''] + $list;
    }

    /**
     * 同步附件
     * @param $collectionId
     * @param $demandId
     * @return true
     */
    private function syncFile($collectionId, $demandId)
    {
        $list = $this->dao
            ->select('pathname,title,extension,size,extra,extension')
            ->from(TABLE_FILE)
            ->where('objectType')->eq('demandcollection')
            ->andWhere('objectID')->eq($collectionId)
            ->andWhere('deleted')->eq('0')
            ->fetchAll();
        if(empty($list)){
            return true;
        }


        $syncList = $this->dao
            ->select('title,id')
            ->from(TABLE_FILE)->where('objectType')->eq('demand')
            ->andWhere('objectID')->eq($demandId)
            ->andWhere('deleted')->eq('0')
            ->fetchPairss('title', 'id');

        foreach ($list as $item){
            if(isset($syncList[$item->title])){
                $this->dao->update(TABLE_FILE)->data($item)->where('id')->eq($syncList[$item->title])->exec();
                continue;
            }

            $item->objectType = 'demand';
            $item->objectID   = $demandId;
            $item->addedBy = $this->app->user->account;
            $item->addedDate = helper::now();
            $this->dao->insert(TABLE_FILE)->data($item)->exec();
        }

        return true;
    }

    /**
     * 需求收集状态联动
     * @param $demandId
     * @return true
     */
    public function statusChange($demandId)
    {
        /**
         * @var demandinsideModel $demandModel
         */
        $demandModel = $this->loadModel('demandinside');
        $demand = $demandModel->getByID($demandId);

        if(empty($demand->collectionId) || $demand->status != 'onlinesuccess'){
            return true;
        }

        $collectionIds = array_unique(explode(',', trim($demand->collectionId,',')));
        if(empty($collectionIds)){
            return true;
        }

        $this->dao
            ->update(TABLE_DEMANDCOLLECTION)
            ->set('state')->eq('5')
            ->set('launchDate')->eq($demand->actualOnlineDate)
            ->where('id')->in($collectionIds)
            ->exec();

        foreach ($collectionIds as $collectionId){
            $this->loadModel('action')->create('demandcollection', $collectionId, 'syncState', '');
        }

        return true;
    }


    /**
     * 解绑需求收集
     * @param $demand
     * @param $collectionId
     * @return void
     */
    public function updateCollection($demand, $collectionId)
    {
        if(empty($collectionId)){
            $collectionId = [];
        }

        $oldCollectionId = array_filter(explode(',', trim($demand->collectionId, ',')));
        $diff = array_diff($oldCollectionId, $collectionId);

        if(!empty($diff)){
            $this->dao->update(TABLE_DEMANDCOLLECTION)->set('demandId')->eq('')->where('id')->in($diff)->exec();

            foreach ($diff as $value){
                $this->loadModel('action')->create('demandcollection', $value, 'updateCollection', '解除需求条目：' . $demand->code);
            }
        }

    }
}
