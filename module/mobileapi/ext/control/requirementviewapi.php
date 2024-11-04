<?php
include '../../control.php';
class myMobileApi extends mobileapi{
    public function requirementViewApi(){
        $errMsg = $this->checkInput();
        if (!empty($errMsg)) {
            $this->loadModel('mobileapi')->response('fail', implode(',', $errMsg), array(),  0, 203,'requirementViewApi');
        }
        $requirementID = $_POST['id'];
        //更新是否超时状态
        $this->loadModel('requirement')->updateRequirementIfOverDate('ifOverDate',$requirementID); //内部反馈超时 未做过处理的
        $this->loadModel('requirement')->updateRequirementIfOverDate('ifOverTimeOutSide',$requirementID); //外部反馈超时 未做过处理的

        $this->app->loadLang('demand');
        $this->app->loadLang('opinion');
        /** @var requirementModel $requirementModel*/
        $requirementModel = $this->loadModel('requirement');
        $requirement = $requirementModel->getByID($requirementID);
        /**
         * @var projectPlanModel $projectPlanModel
         * @var opinionModel $opinionModel
         */
        $projectPlanModel = $this->loadModel('projectplan');
        $opinionModel = $this->loadModel('opinion');
        $opinion = $opinionModel->getByID($requirement->opinion);
        //外部同步需求任务接收时间取创建时间，内部需求任务取需求意向接收时间
        if($requirement->createdBy == 'guestcn'){
            $requirement->acceptTime = $requirement->createdDate;
            $requirement->newPublishedTime = $requirement->feekBackStartTime != '0000-00-00 00:00:00' ? $requirement->feekBackStartTime : '';
        }else{
            $requirement->acceptTime = $opinion->receiveDate ?? '';
            $requirement->newPublishedTime = $requirement->newPublishedTime != '0000-00-00 00:00:00' ? $requirement->newPublishedTime : '';

        }
        $users = $this->loadmodel('user')->getPairs('noletter|noclosed');
        /**
         * 迭代三十四 增加距内外部反馈超期剩余天数
         * ①如果反馈期限大于当前时间则计算剩余天数
         * ②如果反馈期限小区当前时间且状态为否则无需计算天数
         * ifOverDate 1:否 2:是
         * ifOverTimeOutSide 1:否 2:是
         */
        $now = date('Y-m-d',strtotime(helper::now()));
        $insideDays = 0;
        if($requirement->feekBackEndTimeInside != '0000-00-00 00:00:00' && $requirement->feekBackEndTimeInside != '0000-00-00' && !empty($requirement->feekBackEndTimeInside))
        {
            $insideStartDate = date('Y-m-d',strtotime($requirement->feekBackEndTimeInside));
            $insideDays = $this->loadModel('review')->getDiffDate($now,$insideStartDate);
        }

        if(empty($insideDays))
        {
            $insideDays = 0;
        }else{
            if($insideDays >= 0)  $insideDays = $insideDays.' (工作日)';
            if($requirement->ifOverDate == 2)   $insideDays = '已超期';
            if($insideDays < 0 && $requirement->ifOverDate == 1)   $insideDays = 0;
        }
        $outsideDays = 0;
        //外部
        if($requirement->feekBackEndTimeOutSide != '0000-00-00 00:00:00' && $requirement->feekBackEndTimeOutSide != '0000-00-00' && !empty($requirement->feekBackEndTimeOutSide))
        {
            $outsideStartDate = date('Y-m-d',strtotime($requirement->feekBackEndTimeOutSide));
            $outsideDays = $this->loadModel('review')->getDiffDate($now,$outsideStartDate);
        }
        //外部
        if(empty($outsideDays))
        {
            $outsideDays = 0;
        }else{
            if($outsideDays >= 0)  $outsideDays = $outsideDays.' (工作日)';
            if($requirement->ifOverTimeOutSide == 2)   $outsideDays = '已超期';
            if($outsideDays < 0 && $requirement->ifOverTimeOutSide == 1)   $outsideDays = 0;
        }
        //处理时间0000的情况
        $requirement = $requirementModel->dealEmptyTime($requirement);
//            $this->fetch('requirement', 'dealEmptyTime', $requirement);

        if(in_array($requirement->status,['delivered','onlined','deleteout'])){
            $requirement->reviewer = '';
            $requirement->dealUser = '';
        }
        $requirement  = $this->loadModel('file')->replaceImgURL($requirement, 'analysis,handling,implement,comment');
        $apps         = array('' => '') + $this->loadmodel('application')->getapplicationNameCodePairs();
        $depts        = $this->loadModel('dept')->getOptionMenu();

        if($this->app->openApp == 'product')
        {
            $this->app->openApp = 'backlog';
            $productID = $requirement->product;
            $branch    = 0;
            $this->loadModel('product')->setMenu($productID, $branch);

            $product = $this->product->getById($productID);
            //if(empty($product)) $this->locate($this->createLink('product', 'create'));
            $this->view->product    = $product;
            $this->view->branch     = $branch;
            $this->view->branches   = $product->type == 'normal' ? array() : $this->loadModel('branch')->getPairs($product->id);
            $this->view->productID  = $productID;
        }
        //需求任务的所属项目取需求条目的并集 project字段存在  xxx,xxx 需单独处理 迭代二十五要求年度计划与需求条目并集
        $demands = $this->requirement->getDemandByRequirement($requirementID);
        $ownProjectArr = !empty($requirement->project) ? explode(',',$requirement->project): [];
        $demandProjectArr = array_column($demands,'project');
        $mergeProjectArr = array_merge($ownProjectArr,$demandProjectArr);
        /**
         * @var projectPlanModel $projectPlanModel
         * @var opinionModel $opinionModel
         */
        $projectPlanModel = $this->loadModel('projectplan');
        $opinionModel = $this->loadModel('opinion');
        $projectArray = array_filter(array_unique($mergeProjectArr));
        $projects = [];
        if(!empty($projectArray)){
            $projectList = $projectPlanModel->getPlanInProjectIDs($projectArray);
            foreach ($projectList as $projectID => $item) {
                if ($projectID) {
                    $projects[] = $item->name;
                }
            }
        }
        $requirement->projects = htmlspecialchars_decode(implode('<br/>',$projects));
        $lastTaskTime = '';
        $productIds = '';
        $lastEndTime = '';

        foreach ($demands as $demand){
            //研发部门，研发责任人根据需求条目获取
            if(empty($lastTaskTime)){
                $lastTaskTime = $demand->onlineDate;
            }else{
                if(strtotime($lastTaskTime) < strtotime($demand->onlineDate)){
                    $lastTaskTime = $demand->onlineDate;
                }
            }
        }
        $acceptUserArr = array_filter(array_unique(array_column($demands,'acceptUser')));
        $acceptDeptArr = array_filter(array_unique(array_column($demands,'acceptDept')));
        $requirement->demandOwner = implode(',',$acceptUserArr);
        $requirement->dept = implode(',',$acceptDeptArr);
        //转换多行文本换行显示问题
        $requirement->desc = str_replace(chr(13),'<br>',$requirement->desc);

        //查询需求变更单信息
        /**
         * @var requirementChangeModel $requirementChangeModel
         * @var demandModel $demandModel
         */
        $requirementChangeModel = $this->loadModel('requirementchange');
        $demandModel            = $this->loadModel('demand');
//        $requirementChangeInfo = $requirementChangeModel->getByDemandNumber($requirement->changeOrderNumber);
        $requirementChangeInfo = $requirementChangeModel->getChangesByEntries($requirement->entriesCode);

        //后台配置挂起人
        $this->loadModel('demand');
        $suspender = array_filter($this->lang->demand->requirementSuspendList);
        $suspendList = [];
        if(!empty($suspender))
        {
            foreach ($suspender as $key=>$value){
                $suspendList[$key] = $key;
            }
        }
        $secondLinePersonList = $this->loadModel('dept')->getExecutiveUser();//二线专员

        // 变更单信息
        $changeInfo = $requirementModel->getChangeInfoByRequirementId($requirementID);
        foreach ($changeInfo as $item) {
            $item->createdYear = date("Y-m-d",strtotime($item->createdDate));
            $item->status_txtx = zget($this->lang->requirement->changeStatusList,$item->status);
        }
        $requirement->changeInfo = $changeInfo;
        $reviewInfo = $requirementModel->getPendingOrderByRequirementId($requirementID);
        //变更单下一节点处理人
        $requirement->changeNextDealuser = $reviewInfo->nextDealUser ?? '';
        // 当前流程状态
        $requirement->status_text  = zget($this->lang->requirement->statusList, $requirement->status, '');


        //自定义变更解锁人
        $unLock = array_filter(array_keys($this->lang->demand->unLockList));
        $dealUserArray = explode(",", $requirement->dealUser);
        $dealUserChnArray = array();
        foreach ($dealUserArray as $dealUser) {
            array_push($dealUserChnArray, zget($users, $dealUser, ''));
        }
        $dealUserChn = trim(implode(",", $dealUserChnArray),',');
        // 待处理人
        $requirement->dealUserCn   = trim(implode(",", $dealUserChnArray),',');
        $appNames = [];
        $appList = explode(',', $requirement->app);
        foreach ($appList as $app) {
            if ($app) $appNames[] = zget($apps, $app, '');
        }
        // 所属应用系统
        $requirement->appNames   = implode('<br/>',$appNames);
        $requirement->planEnd    = ($requirement->planEnd == '0000-00-00 00:00:00' || $requirement->planEnd == '0000-00-00' || empty($requirement->planEnd)) ? '': $requirement->planEnd;
        $requirement->deadLine   = ($requirement->deadLine == '0000-00-00 00:00:00' || $requirement->deadLine == '0000-00-00' || empty($requirement->deadLine)) ? '': $requirement->deadLine;
        if(!in_array($requirement->status,['delivered','onlined'])) $requirement->solvedTime = '';
        // 产品经理
        $requirement->productManagerCn  = zmget($users,  $requirement->productManager, '');
        $requirement->deptCn            = zmget($depts, $requirement->dept, '');
        // 研发责任人
        $requirement->demandOwnerCn     = zmget($users, $requirement->demandOwner, '');
        $requirement->changeLock_text   = zget($this->lang->requirement->lockStatusList,$requirement->changeLock,'');
        $requirement->sourceMode_text   = zget($this->lang->opinion->sourceModeList, $opinion->sourceMode, '');
        $requirement->sourceMode        = $opinion->sourceMode;
        $requirement->sourceName        = $opinion->sourceName;
        // 所属产品线
        $lines = $this->loadModel('product')->getLinePairs();
        $lineList = explode(',', str_replace(' ', '', $requirement->line));
        $lineArray = [];
        foreach ($lineList as $lineID) {
            if ($lineID) {
                $lineArray[] = ' ' . zget($lines, $lineID, '');
            }
        }
        $requirement->line_text        = implode('<br/>',$lineArray);
        // 所属产品
        $products = $this->loadModel('product')->getPairs();
        $productList = explode(',', str_replace(' ', '', $requirement->product));
        $productArray = [];
        foreach ($productList as $productID) {
            if ($productID) {
                $productArray[] = ' ' . zget($products, $productID, '');
            }
        }
        $requirement->product_text        = implode('<br/>',$productArray);
        $requirement->isImprovementServices = $this->lang->requirement->isImprovementServicesList[$requirement->isImprovementServices];
        //项目经理
        $projectManagerArray = explode(",", $requirement->projectManager);
        $projectManagerChnArray = array();
        foreach ($projectManagerArray as $projectManager) {
            array_push($projectManagerChnArray, zget($users, $projectManager, ''));
        }
        $requirement->projectManagerChn        = trim(implode(",", $projectManagerChnArray),',');
        $requirement->feedbackDealUser_text    = zmget($users,$requirement->feedbackDealUser);
        $requirement->feedbackStatus_text      = zget($this->lang->requirement->feedbackStatusList, $requirement->feedbackStatus, '');
        $requirement->feedbackByCn             = zget($users, $requirement->feedbackBy, '');
        $requirement->ownerCn                  = zget($users, $requirement->owner, '');
        $requirement->method_text              = zget($this->lang->requirement->methodList, $requirement->method, '');
        $requirement->feedbackOver_text        = zget($this->lang->requirement->feedbackOverList,$requirement->feedbackOver,'');
        // 内部反馈是否超时
        $requirement->ifOverDate_text          = zget($this->lang->requirement->ifOverDateList, $requirement->ifOverDate, '').$requirement->feekBackBetweenTimeInside;
        $requirement->insideDays               = $insideDays;
        // 外部反馈是否超时
        $requirement->ifOverTimeOutSide_text   = zget($this->lang->requirement->ifOverDateList, $requirement->ifOverTimeOutSide, '').$requirement->feekBackBetweenOutSide;
        $requirement->outsideDays              = $outsideDays;
        $requirement->flag = $requirementModel->checkAllowReview($requirement, $requirement->version, $requirement->reviewStage, $this->app->user->account);


        $this->loadModel('mobileapi')->response('success', '', $requirement ,  0, 200,'requirementViewApi');
    }
    private function checkInput()
    {
        $errMsg = [];
        if(!isset($_POST['id'])){
            $errMsg[] = "缺少『id』参数";
            return $errMsg;
        }

        if( isset($_POST['id']) && !$_POST['id']){
            $errMsg[] = '『需求任务ID』不能为空';
            return $errMsg;
        }
        if(isset($_POST['id']) && $_POST['id']){
            if(!preg_match("/^[1-9][0-9]*$/",$_POST['id'])){
                $errMsg = '『需求任务ID』只能正整数';
                return $errMsg;
            }
        }
        return $errMsg;
    }
}