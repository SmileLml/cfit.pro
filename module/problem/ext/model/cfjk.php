<?php
/**
 * 重写方法  新增逻辑 当前状态 待开发时 所属应用系统必填
 * @param $problemID
 * @return array
 */
public function deal($problemID)
{
    $oldProblem = $this->getByID($problemID);
    //状态是待分配页面时工作量默认为 0
    $stat = array('assigned', 'feedbacked', 'solved', 'closed', '');
    if (in_array($this->post->status, $stat)) {
        $this->post->consumed = 0;
    }
    //工作量必须是数字
    if (!$this->post->consumed && !in_array($this->post->status, $stat) ) {
        return dao::$errors['consumed'] = $this->lang->problem->consumedEmpty;
    }
    $reg = '/^(([1-9][0-9]*)|(([0]\.\d{1}|[1-9][0-9]*\.\d{1})))$/';
    if (!preg_match($reg, $this->post->consumed) && !in_array($this->post->status, $stat)) {
        dao::$errors['consumed'] = $this->lang->problem->consumedError;
        return false;
    }
    //增加配合人员不能重复 的验证
    if (!in_array($this->post->status, $stat)) {
        $count = count($this->post->relevantUser);
        $uniquecount = count(array_unique($this->post->relevantUser));
        if ($count > $uniquecount) {
            return dao::$errors['relevantUser'] = $this->lang->problem->relevantDeptRepeat;
        }
    }

    $data = fixer::input('post')
        ->join('belongapp', ',')
        ->join('app', ',')
        ->join('product', ',')
        ->join('productPlan', ',')
        ->join('repeatProblem', ',')
        ->join('coordinators', ',')
        ->cleanInt('projectPlan')
        ->join('feedbackToHandle', ',')
        ->stripTags($this->config->problem->editor->deal['id'], $this->config->allowedTags)
        ->remove('relevantUser,workload,user,consumed,mailto,uid ,files,PlannedTimeOfChangeDisabled,comment')
        ->get();

    //迭代26 新增问题引起原因
    if ($data->status == 'feedbacked' and $data->type != 'noproblem' and $data->type != 'repeat') {
        if(!$data->problemCause){
            return dao::$errors = array('problemCause' => $this->lang->problem->problemCauseEmpty);
        }
        //非二线，不纳入跟踪
        if($data->fixType != 'second'){
           $data->secondLineDevelopmentRecord = '2';
        }else{
           $data->secondLineDevelopmentRecord = '1';
        }
    }
   if ($data->status == 'feedbacked' and ($data->type == 'noproblem' or $data->type == 'repeat')) {
        //不纳入跟踪
        $data->secondLineDevelopmentRecord = '2';

    }
//  2022-5-17 api问题当产品经理操作时改ReviewStatus状态为tofeedback
    if ($this->post->status == 'assigned' && $oldProblem->IssueId) {
        $data->ReviewStatus = 'tofeedback';
    }
    $data->productPlan = empty($data->productPlan) ? 0 : $data->productPlan;
    /**
     * 逻辑统一调整为：
     * 1.待分配环节分析问题时，受理人受理部门，取下一节点处理人（分析人员），及所在部门。
     * 2.待分析节点处理后，取当前处理人再次更新受理人受理部门。
     */
    if ($this->post->status == 'assigned') {
        $acceptUser = $this->loadModel('user')->getByAccount($this->post->dealUser);
        $data->acceptDept = $acceptUser->dept;
        $data->acceptUser = $this->post->dealUser;
        if($oldProblem->IssueId){
            $data->feedbackToHandle = $this->post->dealUser; //待反馈人
        }
        //只记录第一次 之后不再更新
        //if(empty($oldProblem->dealAssigned) || strpos($oldProblem->dealAssigned,'0000-00-00') !== false){
        $data->dealAssigned = helper::now(); // 记录待分析 时间
        //}
        if($oldProblem->isChangeFeedbackTime == 0){
            $data->feedbackStartTimeInside = helper::now(); //内部反馈开始时间
            $data->feedbackEndTimeInside   = ''; //内部反馈截止时间
        }
    }

    if ($this->post->status == 'feedbacked') {
        $acceptUser = $this->loadModel('user')->getByAccount($this->app->user->account);
        $data->acceptDept = $acceptUser->dept;
        $data->acceptUser = $this->app->user->account;
    }
    /* 当状态为已分析，已解决，必填项所属项目和问题类型。*/
    if (($this->post->status == 'feedbacked' and $this->post->type != 'noproblem' and $this->post->type != 'repeat') or $this->post->status == 'solved') {
        /* 必填判断所属产品*/
        if (empty($data->product) && $data->SolutionFeedback != 5) return dao::$errors = array('product' => $this->lang->problem->productEmpty);
        /* 必填判断所属项目*/
        if (empty($data->projectPlan)) return dao::$errors = array('projectPlan' => $this->lang->problem->projectPlanEmpty);

        if ($data->fixType == 'second') {
            // 判断二线实现的解决方案必须为二线项目。
            // $plan = $this->dao->select('*')->from(TABLE_PROJECTPLAN)->where('deleted')->eq(0)->andWhere('id')->eq($data->projectPlan)->fetch();
            $plan = $this->dao->select('*')->from(TABLE_PROJECTPLAN)->where('deleted')->eq(0)->andWhere('project')->eq($data->projectPlan)->fetch();
            if (empty($plan->secondLine)) return dao::$errors = array('' => $this->lang->problem->noSecondLinse);
        }
        if ($data->product) {
            if (empty($data->productPlan)) return dao::$errors = array('' => $this->lang->problem->noProductPlan);
        }
        /* 必填增加问题类型。*/
        $this->config->problem->deal->requiredFields .= ',fixType,type';
    }

    //待分析后同步挂载到年度计划
    if($this->post->status == 'feedbacked')
    {
        $planId = $data->projectPlan;
        /** @var projectplanModel $projectplanModel*/
        $projectplanModel = $this->loadModel('projectplan');
        $projectplanModel->insertProblemProjectPlan($oldProblem,$planId);
    }
    //判断产品和产品版本是否重复 、是否为空 产品版本只能存在一个无
    if ($data->status == 'feedbacked' and $data->type != 'noproblem' and $data->type != 'repeat') {
        if(!isset($data->SolutionFeedback) || $data->SolutionFeedback != 5){
            $flag = $this->loadModel('problem')->checkProductAndPlanOnly($this->post->product, $this->post->productPlan);
            if ($flag == 'fail') {
                return dao::$errors = array('' => $this->lang->problem->productOnly);
            } else if ($flag == 'no') {
                return dao::$errors = array('' => $this->lang->problem->productAndPlanEmpty);
            } else if ($flag == 'wu') {
                return dao::$errors = array('' => $this->lang->problem->wuError);
            }
        }
        //迭代26 新增问题引起原因
        if(!$data->problemCause){
            return dao::$errors = array('problemCause' => $this->lang->problem->problemCauseEmpty);
        }
    }
    if ($data->status == 'feedbacked' and $data->type == 'repeat') {
        if (!$data->repeatProblem) {
            return dao::$errors['repeatProblem'] = $this->lang->problem->repeatEmpty;
        }

    }

    // 判断处理后的状态是否为【已关闭】，如果是则记录关闭人和关闭时间。
    $today = helper::today();
    if ($this->post->status == 'closed') {
        $data->closedBy = $this->app->user->account;//$this->post->user;
        $data->closedDate = $today;
        //更新需求和问题解决时间
        if(empty($oldProblem->solvedTime) || strpos($oldProblem->solvedTime, '0000') !== false){
            /** @var problemModel $problemModel */
            $problemModel = $this->loadModel('problem');
            $problemModel->getAllSecondSolveTime($problemID,'problem');
        }
        if ($oldProblem->status == 'toclose') {
            $data->dealUser = '';
            $this->config->problem->deal->requiredFields = 'progress,status,consumed';
        }
    }
    //20220311 新增 当前状态 待开发时  处理后待制版 所属应用系统必填
    if ($this->post->status == 'solved') {
        /* 必填所属应用系统。*/
        if (empty($data->app)) return dao::$errors = array('app' => $this->lang->problem->belongappEmpty);
        //20220427 tangfei
        if (empty($data->plateMakAp)) return dao::$errors = array('plateMakAp' => $this->lang->problem->plateMakApEmpty);
        //20220314 新增 勾选需要 验证人员、测试人员必填
        if ($this->post->systemverify) {
            if (empty($data->verifyperson)) {
                return dao::$errors = array('verifyperson' => $this->lang->problem->verifypersonEmpty);
            }
            if (empty($data->laboratorytest)) {
                return dao::$errors = array('laboratorytest' => $this->lang->problem->laboratorytestEmpty);
            }
        }
    }

    if (!$data->systemverify) {
        $data->verifyperson = '';
    }
    // 判断[buildTimes制版次数]是否自增。
    if ($this->post->status == 'build') {
        //20220427 tangfei
        if (empty($data->plateMakInfo)) return dao::$errors = array('plateMakInfo' => $this->lang->problem->plateMakInfoEmpty);
        $data->buildTimes = $oldProblem->buildTimes + 1;
    }
    $data->lastDealDate = helper::now();
    $data = $this->loadModel('file')->processImgURL($data, $this->config->problem->editor->deal['id'], $this->post->uid);

    if ($this->post->status == 'feedbacked' && $this->post->type != 'noproblem' && $this->post->type != 'repeat' && $this->post->flag != '1') {
        /* 必填所属应用系统。*/
        if (empty($data->app)) return dao::$errors = array('app' => $this->lang->problem->belongappEmpty);
    }
    unset($data->flag);
    unset($data->executionid);
    if ($this->post->status == 'feedbacked' && $this->post->type != 'noproblem' && $this->post->type != 'repeat') {
        /* 必填所属应用系统。*/
        if (empty($data->app)) return dao::$errors = array('app' => $this->lang->problem->belongappEmpty);
    }
    if ($this->post->status == 'feedbacked' && ($this->post->type == 'noproblem' || $this->post->type == 'repeat')) {
        //如果没有关联二线解决时间，解决时间更新为待关闭时间
        if(!$this->isSecondSolve($problemID, 'problem')){
            $data->solvedTime = helper::now();
        }
        $data->status = 'toclose';
    }
    $application = $this->post->application;
    unset($data->application);
    //待分析-待开发计划解决时间必填
    if($this->post->status == 'feedbacked' &&
        (($oldProblem->createdBy != 'guestcn' && $oldProblem->createdBy != 'guestjx') || ($oldProblem->execution > 0 || $oldProblem->ReviewStatus != "tofeedback"))
    ) {
        if (!$this->checkTimeFormat($data->PlannedTimeOfChange)) {
            return dao::$errors['PlannedTimeOfChange'] = $this->lang->problem->timeError;
        }
    }
    //迭代26反馈和分析合并 第一次
    if($this->post->status == 'feedbacked' && ($oldProblem->createdBy == 'guestjx' || $oldProblem->createdBy == 'guestcn') && empty($oldProblem->execution) && $oldProblem->ReviewStatus == "tofeedback"){
            if($this->post->ifReturn != '1' && !$this->checkTimeFormat($data->PlannedTimeOfChange)){
                return dao::$errors['PlannedTimeOfChange'] = $this->lang->problem->timeError;
            }
            if(isset($data->PlannedDateOfChangeReport) && !$this->checkDateFormat($data->PlannedDateOfChangeReport) && $oldProblem->createdBy != 'guestjx'){
                return dao::$errors['PlannedDateOfChangeReport'] = $this->lang->problem->dateError;
            }
            if( isset($data->PlannedDateOfChange) && !$this->checkDateFormat($data->PlannedDateOfChange) && $oldProblem->createdBy != 'guestjx'){
                return dao::$errors['PlannedDateOfChange'] = $this->lang->problem->dateError;
            }
            if(($oldProblem->ReviewStatus == 'firstpassed' || $oldProblem->lastReviewStatus == 'firstpassed') && $data->IfultimateSolution == '0' && $oldProblem->createdBy != 'guestjx'){
                return dao::$errors['IfultimateSolution'] = $this->lang->problem->ifultimateSolutionError;
            }
            if(($oldProblem->ReviewStatus == 'externalsendback' || $oldProblem->lastReviewStatus == 'externalsendback') && $oldProblem->IfultimateSolution == '1' && $data->IfultimateSolution == '0' && $oldProblem->createdBy != 'guestjx'){
                return dao::$errors['IfultimateSolution'] = $this->lang->problem->ifultimateSolutionBackError;
            }
            //问题分级
            if(isset($data->problemGrade) && !$data->problemGrade  && $oldProblem->createdBy != 'guestjx'){
                return dao::$errors['problemGrade'] = $this->lang->problem->problemGradeEmpty;
            }
            //最终方案 是时 是否基准验证必填
            if($data->IfultimateSolution  && $data->IfultimateSolution == "1" && $oldProblem->createdBy != 'guestjx'){
                if(!$data->standardVerify){
                   return  dao::$errors['standardVerify'] = $this->lang->problem->standardVerifyEmpty;
                }
            }
            if(empty($data->feedbackToHandle)){
                $errors['feedbackToHandle'] = sprintf($this->lang->problem->emptyObject, $this->lang->problem->feedbackToHandle);
                return dao::$errors = $errors;
            }
            //拼接对外产品及版本内容
            if(isset($data->ifReturn) && $data->ifReturn == '0'){
                $products = explode(',',$data->product);
                $plans     = explode(',',$data->productPlan) ;
                $CorresProductAll = '';
                $productList = array('0' => '','99999'=>'无') + $this->loadModel('product')->getCodeNamePairsByApp($data->app) ;
                if($products && $plans){
                    foreach ($products as $key => $product) {
                        $planList      = array('0' => '','1' =>'无') + $this->loadModel('productplan')->getPairs($product);
                        $CorresProductAll .= zget($productList,$product,'')."(".zget($planList,$plans[$key],'').")" .",";
                    }
                }
                $data->CorresProduct = trim($CorresProductAll,',') == '()' ? '无' : trim($CorresProductAll,',');
            }
            if($data->ifReturn == '0'){
                $data->ReasonOfIssueRejecting = null;
                if($data->SolutionFeedback != '1'){
                    $data->ChangeSolvingTheIssue = null;
                }
            }else if($data->ifReturn == '1'){
                $data->Tier1Feedback = null;
                $data->SolutionFeedback = null;
                $data->PlannedTimeOfChange = null;
                $data->CorresProduct = null;
                $data->PlannedDateOfChangeReport = null;
                $data->PlannedDateOfChange = null;
                $data->ChangeSolvingTheIssue = null;
                $data->EditorImpactscope = null;
                $data->solution = null;
                $data->reason = null;
                $data->IfultimateSolution = '1';
            }

            $data->acceptUser   = $this->app->user->account;
            $data->acceptDept   = $this->app->user->dept;
            //反馈单状态待审批
            $data->ReviewStatus = "todeptapprove";
            //保存重要状态-初次通过和外部退回
            if($oldProblem->ReviewStatus == 'firstpassed' || $oldProblem->ReviewStatus == 'externalsendback'){
                $data->lastReviewStatus = $oldProblem->ReviewStatus;
            }

            //2022.5.17 反馈单审核步骤id,反馈单版本版本
            $data->reviewStage = '1';
            $data->version     =  $oldProblem->version+1;
            $data->ReviewStatus     = "todeptapprove"; //用户点击反馈按键 重新走流程 不再重试发送清总失败反馈
            $data->syncFailTimes    =  0;

            $problemObject = $this->getByID($problemID);
            if($problemObject->ReviewStatus != 'tofeedback' and $problemObject->ReviewStatus != 'todeptapprove' and $problemObject->ReviewStatus != 'sendback'  and $problemObject->ReviewStatus != 'syncfail' and $problemObject->ReviewStatus != 'jxsyncfail' and $problemObject->ReviewStatus != 'firstpassed' and $problemObject->ReviewStatus != 'externalsendback' and $problemObject->ReviewStatus != 'approvesuccess'){
               return dao::$errors[''] = $this->lang->problem->editError;
            }
    }

    if (!$this->post->dealUser and $oldProblem->status != 'toclose' and ($this->post->type != 'noproblem' && $this->post->type != 'repeat')) {
        return dao::$errors['dealUser'] = $this->lang->problem->nextUserEmpty;
    }
    if($this->post->type == 'noproblem' || $this->post->type == 'repeat'){
        $this->config->problem->deal->requiredFields = str_replace('dealUser,','',$this->config->problem->deal->requiredFields);
    }
    $data->ifReturn = $data->ifReturn ?? '0'; // 是否受理问题 -受理
    $flag = $this->post->status == 'feedbacked' && ($oldProblem->createdBy == 'guestjx' || $oldProblem->createdBy == 'guestcn') && empty($oldProblem->execution) && $oldProblem->ReviewStatus == "tofeedback";

    // 解决内容中有特殊字符导致截取问题（<= ）
    if(isset($data->reason) && $data->reason){
        $data->reason = htmlentities($data->reason);
    }
    if(isset($data->solution) && $data->solution){
        $data->solution = htmlentities($data->solution);
    }

    $this->dao->update(TABLE_PROBLEM)->data($data)->autoCheck()
        ->batchCheck($this->config->problem->deal->requiredFields, 'notempty')
        ->beginIF($flag)
        ->checkIF($flag && isset($data->ifReturn) && $data->ifReturn == '0', 'Tier1Feedback', 'notempty')
        ->checkIF($flag && isset($data->ifReturn) &&  $data->ifReturn == '0', 'SolutionFeedback', 'notempty')
        ->checkIF($flag && isset($data->ifReturn) &&  $data->ifReturn == '0', 'PlannedTimeOfChange', 'notempty')
        /*->checkIF(isset($data->ifReturn) && $data->ifReturn == '0', 'CorresProduct', 'notempty')*/
        ->checkIF($flag && isset($data->ifReturn) && $data->ifReturn == '0' && $oldProblem->createdBy != 'guestjx', 'PlannedDateOfChangeReport', 'notempty')
        ->checkIF($flag && isset($data->ifReturn) && $data->ifReturn == '0' && $oldProblem->createdBy != 'guestjx', 'PlannedDateOfChange', 'notempty')
        ->check('TeleOfIssueHandler', 'notempty')
        ->check('feedbackToHandle', 'notempty')
        /*->checkIF($problem->ifReturn == '0'  and $problem->SolutionFeedback == '1', 'ChangeSolvingTheIssue', 'notempty')*/
        ->checkIF($flag && isset($data->ifReturn) && $data->ifReturn == '0', 'EditorImpactscope', 'notempty')
        ->checkIF($flag && isset($data->ifReturn) && $data->ifReturn == '0', 'solution', 'notempty')
        ->checkIF($flag && isset($data->ifReturn) && $data->ifReturn == '0', 'reason', 'notempty')
        ->checkIF($flag && isset($data->ifReturn) && $data->ifReturn == '0', 'solution', 'length',2000,0)
        ->checkIF($flag && isset($data->ifReturn) && $data->ifReturn == '1', 'ReasonOfIssueRejecting', 'notempty')
        ->fi()
        ->check('reason', 'length',2000,0)
        ->where('id')->eq($problemID)
        ->exec();
    unset($data->coordinators);
    $this->loadModel('consumed')->record('problem', $problemID, 0,$this->app->user->account, $oldProblem->status, $data->status, $this->post->mailto);

    $this->loadModel('file')->updateObjectID($this->post->uid, $problemID, 'problem');
    $this->file->saveUpload('problem', $problemID);

   if(!dao::isError()){
        if($data->status == 'feedbacked' and $data->type != 'noproblem' && $this->post->type != 'repeat') {
            //查看所属项目是否和产品关联
            $linkedProducts = $this->loadModel('product')->getProducts($data->projectPlan);
            $products = array_column($linkedProducts, 'id');
            $dataproducts = array_unique(explode(',', $data->product));
            $noexits = array();
            foreach ($dataproducts as $dataproduct) {
                if (in_array($dataproduct, $products) === false && $dataproduct != '99999') {
                    $noexits[] = $dataproduct;
                }
            }
            $product = array_filter($noexits);
            if(!empty($product)){
               //只处理新增的产品
               $this->loadModel('demand')->bindProduct($data->projectPlan, $product,'problem',$problemID);
            }
        }
        if((
            ($data->status == 'feedbacked' and $data->type != 'noproblem' && $this->post->type != 'repeat')
            || ($data->status == 'toclose' && ($data->type == 'noproblem' || $data->type == 'repeat')))
            && ($oldProblem->createdBy == 'guestjx' || $oldProblem->createdBy == 'guestcn')
            && empty($oldProblem->execution) && $oldProblem->ReviewStatus = "tofeedback"){
           $this->loadModel('file')->updateObjectID($this->post->uid, $problemID, 'problemFeedback');
           $this->file->saveUpload('problemFeedback', $problemID);
           $this->loadModel('consumed')->record('problem', $problemID, $this->post->consumed, $this->app->user->account,
               $oldProblem->ReviewStatus, $data->ReviewStatus, array(), "problemFeedBack");

           //外部反馈单在内部中的状态也需要接口同步 待反馈 待部门审核 待产创审核
            $statusName = zget($this->lang->problem->consumedstatusList,'todeptapprove','');
            $this->loadModel('problem')->syncFeedBackStatus($problemID,$statusName);
           //获取二线专员
           $apiUser = $oldProblem->createdBy == 'guestjx' ? $this->lang->problem->apiDealUserList['jxDealAccount'] : $this->lang->problem->apiDealUserList['userAccount'];

           $this->loadModel('review');
           $this->review->addNode('problem', $problemID, $data->version, explode(',', $data->feedbackToHandle), true, 'pending', 1);
           $this->review->addNode('problem', $problemID, $data->version, explode(',', $apiUser), true, 'wait', 2);
           $this->review->addNode('problem', $problemID, $data->version, explode(',', 'guestjk'), true, 'wait', 3);
           $this->review->addNode('problem', $problemID, $data->version, explode(',', $oldProblem->createdBy), true, 'wait', 4);
       }
    }
    return common::createChanges($oldProblem, $data);
}

public function getExecutivePairs($deptId = '')
{
    return $this->loadExtension('cfjk')->getExecutivePairs($deptId);
}

/**
 * 问题单延期申请单
 * @param $id
 * @return mixed
 */
public function delay($id)
{
    return $this->loadExtension('cfjk')->delay($id);
}

/**
 * 延期申请流程
 * @param $id
 * @return mixed
 */
public function delayReview($id)
{
    return $this->loadExtension('cfjk')->delayReview($id);
}

/**
 * 检查是否允许申请延期
 * @param $problem
 * @return mixed
 */
public function delayCheck($problem)
{
    return $this->loadExtension('cfjk')->delayCheck($problem);
}

/**
 * 关闭的时候检查是否有在途的变更
 * @param $problem
 * @return mixed
 */
public function closeCheckDelay($problem)
{
    return $this->loadExtension('cfjk')->closeCheckDelay($problem);
}
public function insideFeedback()
{
    return $this->loadExtension('cfjk')->insideFeedback();
}

public function outsideFeedback()
{
    return $this->loadExtension('cfjk')->outsideFeedback();
}

/**
 * 发送超时提醒邮件
 * @return mixed
 */
public function sendmailBySolvingOutTime()
{
    return $this->loadExtension('cfjk')->sendmailBySolvingOutTime();
}

/**
 * 获取内外部反馈超时时间
 * @param $problem
 * @return mixed
 */
public function getIfOverDate($problem)
{
    return $this->loadExtension('cfjk')->getIfOverDate($problem);
}

/**
 * 获取解决时间
 * @param $problem
 * @return mixed
 */
public function getSolvedTime($problem)
{
    return $this->loadExtension('cfjk')->getSolvedTime($problem);
}

public function changeBySecondLineV3()
{
    return $this->loadExtension('change')->changeBySecondLineV3();
}
/**
 * 解决时间是否由二线同步的
 * @param $problemID
 * @param $type
 * @return bool
 */
public function isSecondSolve($problemID, $type)
{
    $allSecond =  $this->getAllSecond($problemID,$type);

    $allSolveTime = array();
    $hasTime      = array();
    foreach ($allSecond as $key => $item)
    {
        if($key == 'count') continue;
        if(!$item) continue;
        $secondType = $key;
        //获取每一个二线解决时间
        $allSolve       = $this->getOneSecondWhertherPass($secondType,$item);
        $allSolveTime[] = $allSolve['solve'];//解决时间
        $hasTime[$key]  = $allSolve['hasTime'];//所有有解决时间的二线

    }
    $newArr = array();
    foreach ($allSolveTime as $alls)
    {
        foreach ($alls as $key =>$all) {
            $newArr[$key] = $all;
        }
    }

    //所有二线全部审批通过
    return ($allSecond['count'] == count($newArr)) && count($newArr) != 0;
}

//获取问题反馈单同步接口首次成功时间
public function getInnovationPassTime($problem)
{
    return $this->loadExtension('cfjk')->getInnovationPassTime($problem);
}

//获取问题交付是否超期
public function getIsExceed($problem)
{
    return $this->loadExtension('cfjk')->getIsExceed($problem);
}

public function getOverDate($date, $monthNum)
{
    return $this->loadExtension('cfjk')->getOverDate($date, $monthNum);
}

/**
 * 获取问题单交付是否超期（只对比时间）
 * @param $problem
 * @return mixed
 */
public function getIsExceedByTime($problem)
{
    return $this->loadExtension('cfjk')->getIsExceedByTime($problem);
}

public function updateIsExceedByTime()
{
    return $this->loadExtension('update')->updateIsExceedByTime();
}

public function updateIfOverDateInsideNew()
{
    return $this->loadExtension('update')->updateIfOverDateInsideNew();
}

/**
 * 创建指派样式
 * @param $problem
 * @param $users
 * @return mixed
 */
public function printAssignedHtml($problem,$users)
{
    return $this->loadExtension('update')->printAssignedHtml($problem, $users);
}

public function isAssigned($problem)
{
    return $this->loadExtension('update')->isAssigned($problem);
}

/**
 * 更新问题是否按计划完成
 * @return mixed
 */
public function getCompletedPlan($problem = null)
{
    return $this->loadExtension('cfjk')->getCompletedPlan($problem);
}

/**
 * 更新问题考核结果
 * @return mixed
 */
public function getExaminationResult($problem = null)
{
    return $this->loadExtension('cfjk')->getExaminationResult($problem);
}