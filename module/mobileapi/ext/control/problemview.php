<?php
include '../../control.php';
class myMobileApi extends mobileapi
{

    /**
     * 问题单详情页
     */
    public function problemView(){
        $this->app->loadLang('problem');
        $errMsg = $this->checkInput();
        if (!empty($errMsg)) {
            $this->loadModel('mobileapi')->response('fail', implode(',', $errMsg), array(),  0, 203,'problemView');
        }
        $problemID = $_POST['id'];
        $problem = $this->loadModel('problem')->getByID($problemID);

        $problem->attachments = []; //问题单自己的附件
        $problem->apiFiles = []; //清总传来的文件
        foreach ($problem->files as $file){
            if($file->apiFile){
                $problem->apiFiles[] =  $file;
            } else {
                $problem->attachments[] = $file;
            }
        }
        $problem->delayDealUser = !empty($problem->delayDealUser) ? ',' . trim($problem->delayDealUser, ',') : '';
        $users   = $this->loadModel('user')->getPairs('noclosed|noletter');
        $apps    = $this->loadModel('application')->getapplicationInfo();
        $objects = $this->loadModel('secondline')->getByID($problemID, 'problem');
        $depts   = $this->loadModel('dept')->getOptionMenu();

        // 引发该问题的变更
        $problem->ChangeIdRelated      = !empty($problem->ChangeIdRelated) ? html_entity_decode(str_replace("\n",PHP_EOL,$problem->ChangeIdRelated)) : '';
        // 引发该问题的事件
        $problem->IncidentIdRelated    = !empty($problem->IncidentIdRelated) ? html_entity_decode(str_replace("\n",PHP_EOL,$problem->IncidentIdRelated)) : '';
        // 引发该问题的演练
        $problem->DrillCausedBy        = !empty($problem->DrillCausedBy) ? html_entity_decode(str_replace("\n",PHP_EOL,$problem->DrillCausedBy)) : '';
        // 优化及改进建议
        $problem->Optimization         = !empty($problem->Optimization) ? html_entity_decode(str_replace("\n",PHP_EOL,$problem->Optimization)) : '';
        // 初步反馈
        $problem->Tier1Feedback        = !empty($problem->Tier1Feedback) ? nl2br($problem->Tier1Feedback) : '';
        // 解决方案
        $problem->solution             = !empty($problem->solution) ? html_entity_decode(str_replace("\n",PHP_EOL,$problem->solution)) : '';
        // 解决该问题的变更
        $problem->ChangeSolvingTheIssue  = !empty($problem->ChangeSolvingTheIssue) ? nl2br($problem->ChangeSolvingTheIssue) : '';
        // 退回原因
        $problem->ReasonOfIssueRejecting = !empty($problem->ReasonOfIssueRejecting) ? nl2br($problem->ReasonOfIssueRejecting) : '';
        // 影响范围
        $problem->EditorImpactscope      = !empty($problem->EditorImpactscope) ? nl2br($problem->EditorImpactscope) : '';
        // 修订记录
        $problem->revisionRecord         = !empty($problem->revisionRecord) ? nl2br($problem->revisionRecord) : '';
        // 制版申请
        $problem->plateMakAp             = !empty($problem->plateMakAp) ? html_entity_decode(str_replace("\n",PHP_EOL,$problem->plateMakAp)) : '';
        // 制版信息
        $problem->plateMakInfo           = !empty($problem->plateMakInfo) ? html_entity_decode(str_replace("<br>",PHP_EOL,$problem->plateMakInfo)) : '';
        $problem->DepIdofIssueCreator    = !empty($problem->DepIdofIssueCreator) ? html_entity_decode(str_replace("\n",PHP_EOL,$problem->DepIdofIssueCreator)) : '';
        // 问题类型
        $problem->type                   = zget($this->lang->problem->typeList, $problem->type, $problem->type);
        // 问题引起原因
        $problem->problemCause           = zget($this->lang->problem->problemCauseList, $problem->problemCause, '');
        // 重複問題單
        $problem->repeat                 = $this->problem->getCodes($problem->repeatProblem);
        // 问题来源
        $problem->source                 = zget($this->lang->problem->sourceList, $problem->source, $problem->source);
        // 受影响业务系统
        $as = [];
        $oldApp = $problem->app;
        foreach(explode(',', $problem->app) as $app)
        {
            if(!$app) continue;
            $as[] = zget($apps, $app , "",$apps[$app]->name);
        }
        $problem->app                   = implode(', ', $as);
        // 问题级别
        $problem->severity = zget($this->lang->problem->severityList, $problem->severity, $problem->severity);
        // 外部问题单状态
        $problem->IssueStatus   = zget($this->lang->problem->IssueStatusList,$problem->IssueStatus,$problem->IssueStatus);
        // 反馈单状态
        $problem->ReviewStatus  = zget($this->lang->problem->feedbackStatusList, $problem->ReviewStatus);

        //组成反馈单处理人员数组
        $approver = array();
        if($problem->feedbackToHandle != null){
            $countArray = explode(',', $problem->feedbackToHandle);
            foreach ($countArray as $account) {
                $approver[$account] = $account;
            }
        }
        $problem->approver   = $approver;
        //转换待处理人
        if($problem->feedbackToHandle != null){
            $userName = "";
            $myArray = explode(',', $problem->feedbackToHandle);
            foreach ($myArray as $account) {
                if($userName == ""){
                    $userName .= $users[$account];
                }else{
                    $userName .= ",";
                    $userName .= $users[$account];
                }
            }
            $problem->feedbackToHandle = $userName;
        }
        //外部关闭时间
        if(!empty($problem->TimeOfClosing)){
            if($problem->TimeOfClosing == '1970-01-01 08:00:00' || $problem->TimeOfClosing == '0000-00-00 00:00:00'){
                $problem->TimeOfClosing = null;
            }
        }
        // 是否最终方案
        $problem->IfultimateSolution  = zget($this->lang->problem->ifultimateSolutionList, $problem->IfultimateSolution);
        $problem->feedbackNum         = $problem->feedbackNum > 0  ?$problem->feedbackNum - 1 : 0;
        $problem->dealUser            = zget($users, $problem->dealUser, '');
        // 流程状态
        $problem->status_text         = zget($this->lang->problem->statusList, $problem->status, '');
        // 系统分类
        $as = [];
        foreach(explode(',', $problem->isPayment) as $apptype)
        {
            if(!$apptype) continue;
            $as[] = zget($this->lang->application->isPaymentList, $apptype, "");
        }
        $problem->applicationtype     = implode(',', $as);
        // 实现方式
        $problem->fixType             = zget($this->lang->problem->fixTypeList, $problem->fixType, '');
        $problem->planName = '';
        $problem->executionName = '';
        if($problem->projectPlan){
            $id = $this->dao->select('id')->from(TABLE_PROJECTPLAN)->where('project')->eq($problem->projectPlan)->fetch();
            $problem->plan           = $this->loadModel('projectplan')->getByID($id->id);
            $problem->planName       = $problem->plan->name;
            $problem->projectplanid  = $id->id;
            //所属阶段
            $executions     = $this->problem->getExecution($problem->projectPlan);
            $problem->executionName     = $executions[$problem->execution];

        }

        $plans = explode(',',$problem->productPlan);
        $products = explode(',',$problem->product);
        $task = array();
        foreach ($products as $key=>$product) {
            $task[$key] =  $this->loadModel('demand')->getTaskName($problem->projectPlan,trim($oldApp,','),$product,$plans[$key],$problemID,1,'problem');
        }
        // 所属任务
        $taskNames = [];
        if($task)  {
            foreach ($task as $key => $item) {
                if(!$item) continue;
                $taskNames[] = $item->taskName;
            }
        }
        $problem->taskName = implode(PHP_EOL,$taskNames);
        $release = array();
        $taskid = array_column($task,'id');

        foreach ($taskid as $key => $item) {
            $release[$key] = $this->loadModel('demand')->getBuildRelease($item);
        }
        $buildNames = [];
        $releaseNames = [];
        foreach ($release as $item){
            if(isset($item->buildname) && $item->buildname){
                $buildNames[] = $item->buildname;
            }
            if(isset($item->releasename) && $item->releasename){
                $releaseNames[] = $item->releasename;
            }
        }
        $problem->buildName     = implode(PHP_EOL,$buildNames);
        $problem->releaseName   = implode(PHP_EOL,$releaseNames);
        if($problem->product){
            $productid = explode(',',$problem->product);
            foreach ($productid as $item) {
                $products[] = $item == '99999' ? array('name'=>'无') : $this->loadModel('product')->getByID($item);
            }
            $problem->productName  = implode('，',array_column($products,'name'));
        }
        if($problem->productPlan){
            $productPlanid = explode(',',$problem->productPlan);
            foreach ($productPlanid as $item) {
                $productPlans[] =  $item == '1' ? array('title'=>'无') :$this->loadModel('productplan')->getByID($item);
            }
            $problem->productPlan = implode('，',array_column($productPlans,'title'));
        }else{
            $problem->productPlan = '';
        }
        $problem->systemverify         = zget($this->lang->problem->needOptions, $problem->systemverify, '');
        // 验证人员
        $problem->verifyperson         = zget($users, $problem->verifyperson, '');
        // 实验室测试
        $problem->laboratorytest       = zget($users, $problem->laboratorytest, '');

        // 关联生产变更（金信+清总）
        $modifys = [];
        if(isset($objects['modify'])){
            foreach($objects['modify'] as $objectID => $object){
                $modifys[] = $object;
            }
        }
        if(isset($objects['modifycncc'])){
            foreach($objects['modifycncc'] as $objectID => $object){
                $modifys[] = $object;
            }
        }
        // 对外交付
        $outwardDeliverys = [];
        if(isset($objects['outwardDelivery'])){
            foreach($objects['outwardDelivery'] as $objectID => $object){
                $outwardDeliverys[] = $object;
            }
        }
        // 数据获取
        $fixs = [];
        if(isset($objects['outwardDelivery']) && isset($objects['fix']) && $objects['fix']){
            foreach($objects['fix'] as $objectID => $object){
                $fixs[] = $object;
            }
        }
        $problem->modifys             = implode(PHP_EOL,$modifys);
        $problem->outwardDeliverys    = implode(PHP_EOL,$outwardDeliverys);
        $problem->fixs                = implode(PHP_EOL,$fixs);
        // 数据获取
        $gains = [];
        if(isset($objects['gain'])){
            foreach($objects['gain'] as $objectID => $object){
                $gains[] = $object;
            }
        }
//        if(isset($objects['infoQz'])){
//            foreach($objects['infoQz'] as $objectID => $object){
//                $gains[] = $object;
//            }
//        }
        if(isset($objects['gainQz'])){
            foreach($objects['gainQz'] as $objectID => $object){
                $gains[] = $object;
            }
        }
        $problem->gains               = implode(PHP_EOL,$gains);
        // 受理人
        $problem->acceptUser          = zget($users, $problem->acceptUser, '');
        // 受理部门
        $problem->acceptDept          = zget($depts, $problem->acceptDept, '');
        $problem->createdByName       = zget($users, $problem->createdBy, '');
        $problem->editedBy            = zget($users, $problem->editedBy, '');
        $problem->closedBy            = zget($users, $problem->closedBy, '');
        // 是否解除状态联动
        $problem->secureStatusLinkage = zget($this->lang->problem->secureStatusLinkageList,$problem->secureStatusLinkage,'');
        // 是否纳入交付超期
        $problem->isExtended          =  zget($this->lang->problem->isExtendedList,$problem->isExtended,'');
        // 是否纳入反馈超期
        $problem->isBackExtended      = zget($this->lang->problem->isBackExtendedList,$problem->isBackExtended,'');
        $problem->dealAssigned        = strpos($problem->dealAssigned,'0000-00-00') === false ? $problem->dealAssigned : '';
        $problem->solvedTime          = strpos($problem->solvedTime,'0000-00-00') === false ? $problem->solvedTime : '';
        $problem->actualOnlineDate    = strpos($problem->actualOnlineDate,'0000-00-00') === false ? $problem->actualOnlineDate : '';
        $problem->flag                = $this->problem->checkAllowReview($problem, $problem->version, $problem->reviewStage, $this->app->user->account);

        $this->mobileapi->response('success', '', $problem ,  0, 200,'problemView');

    }
    private function checkInput()
    {
        $errMsg = [];
        if(!isset($_POST['id'])){
            $errMsg[] = "缺少『id』参数";
            return $errMsg;
        }

        if( isset($_POST['id']) && !$_POST['id']){
            $errMsg[] = '『问题单ID』不能为空';
            return $errMsg;
        }
        if(isset($_POST['id']) && $_POST['id']){
            if(!preg_match("/^[1-9][0-9]*$/",$_POST['id'])){
                $errMsg = '『问题单ID』只能正整数';
                return $errMsg;
            }
        }
        return $errMsg;
    }
}