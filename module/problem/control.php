<?php
class problem extends control
{
    /**
     * Project: chengfangjinke
     * Method: browse
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:52
     * Desc: This is the code comment. This method is called browse.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param string $browseType
     * @param int $param
     * @param string $orderBy
     * @param int $recTotal
     * @param int $recPerPage
     * @param int $pageID
     */
    public function browse($browseType = 'all', $param = 0, $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1,$flag = false)
    {
        if(isset($this->lang->problem->isExtendedUserList[$this->app->user->account]) || $this->app->user->account == 'admin'){
            $this->config->problem->search['fields']['isExtended']     = $this->lang->problem->isExtended;
            $this->config->problem->search['params']['isExtended']     = ['operator' => '=', 'control' => 'select', 'values' => $this->lang->problem->isExtendedList];
            $this->config->problem->search['fields']['isBackExtended'] = $this->lang->problem->isBackExtended;
            $this->config->problem->search['params']['isBackExtended'] = ['operator' => '=', 'control' => 'select', 'values' => $this->lang->problem->isBackExtendedList];
        }
        $browseType = strtolower($browseType);

        /* By search. */
        $depts = $this->loadModel('dept')->getOptionMenu();
        $depts[0] = '';
        $this->config->problem->search['params']['createdDept']['values'] = $depts;
        $this->config->problem->search['params']['acceptDept']['values']  = $depts;
        $this->config->problem->search['params']['ifReturn']['values']  = $this->lang->problem->ifReturnList;

        $projectPlanList = $this->loadModel('project')->getPairs();
        $productList     = array('0' => '') + $this->loadModel('product')->getPairs();
        $productPlanList = array('0' => '') + $this->loadModel('productplan')->getSimplePairs();

        if(!empty($projectPlanList))
        {
            $this->config->problem->search['params']['projectPlan']['values'] += $projectPlanList;
        }
        if(!empty($productList))     $this->config->problem->search['params']['product']['values']     = $productList;
        if(!empty($productPlanList)) $this->config->problem->search['params']['productPlan']['values']     = $productPlanList;
        $this->loadModel('application');
        $apps = $this->application->getPairs();

        if(!empty($apps))
        {
            $appList = array();
            foreach($apps as $key => $app)
            {
                $appList[',' . $key . ','] = $app;
            }
            $this->config->problem->search['params']['app']['values'] += $appList;
        }

        $isPaymentList = array();
        foreach($this->lang->application->isPaymentList as $paymentID => $paymentValue)
        {
            if(!$paymentID) continue;
            $isPaymentList[',' . $paymentID . ','] = $paymentValue;
        }
        $this->config->problem->search['params']['isPayment']['values'] += $isPaymentList;

        $queryID = ($browseType == 'bysearch')  ? (int)$param : 0;
        $actionURL = $this->createLink('problem', 'browse', "browseType=bySearch&param=myQueryID");
        $this->problem->buildSearchForm($queryID, $actionURL);

        /* 设置详情页面返回的url连接。*/
        $this->session->set('problemList', $this->app->getURI(true));
        $this->session->set('common_back_url', $this->app->getURI(true),'backlog');

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);
        $problems = $this->problem->getList($browseType, $queryID, $orderBy, $pager, $flag);
        $this->view->title      = $this->lang->problem->common;

        $this->view->apps       = $apps;
        $this->view->orderBy    = $orderBy;
        $this->view->pager      = $pager;
        $this->view->pageID      = $pageID;
        $this->view->param      = $param;
        $this->view->browseType = $browseType;
        $this->view->depts      = $this->loadModel('dept')->getOptionMenu();
        $this->view->executives = $this->dept->getExecutiveUser();
        $this->view->users      = $this->loadModel('user')->getPairs('noletter|noclosed');
        $this->view->plans = $this->loadModel('project')->getPairs();
        //拼接处理人员和反馈单处理人员
        foreach($problems as $problem){
            $handleruser = array();
            $dealUserNname = "";
            if($problem->dealUser != null){
                array_push($handleruser,$problem->dealUser);
                if(isset($this->view->users[$problem->dealUser])){
                    $dealUserNname = $this->view->users[$problem->dealUser];
                }
            }

            if($problem->feedbackToHandle != null){
                $myArray = explode(',', $problem->feedbackToHandle);
                foreach ($myArray as $account) {
                    if(!in_array($account, $handleruser)){
                        array_push($handleruser,$account);
                        $dealUserNname .= ",";
                        if(isset($this->view->users[$account])){
                            $dealUserNname .= $this->view->users[$account];
                        }
                    }
                }
            }
//            $problem->dealUsers = trim($dealUserNname,',');
            $problem->delayDealUser = !empty($problem->delayDealUser) ? ','.trim($problem->delayDealUser, ',') : '';
            $problem->changeDealUser = !empty($problem->changeDealUser) ? ','.trim($problem->changeDealUser, ',') : '';
            $problem->dealUsers = zmget($this->view->users, $problem->dealUser.','.$problem->feedbackToHandle.$problem->delayDealUser.$problem->changeDealUser);

            //组成反馈单处理人员数组
            $approver = array();
            if($problem->feedbackToHandle != null){
                $countArray = explode(',', $problem->feedbackToHandle);
                foreach ($countArray as $account) {
                    $approver[$account] = $account;
                }
            }
            $problem->approver   = $approver;
        }
        $this->view->problems   = $problems;
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: create
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:52
     * Desc: This is the code comment. This method is called create.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     */
    public function create()
    {
        if($_POST)
        {
            $problemID = $this->problem->create();

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $this->loadModel('action')->create('problem', $problemID, 'created', $this->post->comment);
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = inlink('browse');

            $this->send($response);
        }

        $this->view->title = $this->lang->problem->create;
        $this->view->appAll  =  $this->loadModel('application')->getapplicationNameCodePairsWithisPayment();
        $this->view->apps = array('' => '') + array_column($this->view->appAll, 'name', 'id');
        $this->view->executives = $this->problem->getExecutivePairs(); // $this->loadModel('user')->getPairs('noclosed');
        $this->view->dept  = $this->loadModel('dept')->getByID($this->app->user->dept);
        $this->view->productList     = array('0' => '') + $this->loadModel('product')->getCodeNamePairs();
        $this->display();
    }

    /**
     * Edit a problem.
     * 
     * @param  int $problemID 
     * @access public
     * @return void
     */
    public function edit($problemID = 0)
    {
        $problem = $this->problem->getByID($problemID);
        $flag    = $this->problem->isClickable($problem, 'edit');
        if(!$flag){
            $response['result']  = 'fail';
            $response['message'] = $this->lang->problem->authStatusError;
            $response['locate']  = inlink('view', "problemID=$problemID");
            $this->send($response);
        }
        if($_POST)
        {
            $changes = $this->problem->update($problemID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes || $this->post->comment)
            {
                $this->loadModel('consumed')->record('problem', $problemID, 0, $this->app->user->account, $problem->status, 'confirmed', '');
                $actionID = $this->loadModel('action')->create('problem', $problemID, 'edited', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = inlink('view', "problemID=$problemID");

            $this->send($response);
        }

        $this->view->title   = $this->lang->problem->edit;
        $this->view->executives = $this->problem->getExecutivePairs(); // $this->loadModel('user')->getPairs('noclosed,noletter');
        $this->view->problem = $this->problem->getByID($problemID);
        if($this->view->problem->createdBy == 'guestjx') {
            // 金信业务系统处理
            $arrApp = explode(',',$this->view->problem->app);
            $this->view->apps    = array_combine($arrApp,$arrApp);
        }else {
            $this->view->apps    = $this->loadModel('application')->getapplicationNameCodePairs();
        }
        $this->view->readOnly = empty($this->view->problem->IssueId) ? "" : "readonly='readonly'";
        $this->view->disabled = empty($this->view->problem->IssueId) ? "" : "disabled";
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: editSpecial
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:52
     * Desc: This is the code comment. This method is called editSpecial.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $problemID
     */
    public function editSpecial($problemID = 0)
    {
        $problem = $this->loadModel('problem')->getByID($problemID);
        $flag    = $this->problem->isClickable($problem, 'editSpecial');
        if(!$flag){
            $response['result']  = 'fail';
            $response['message'] = $this->lang->problem->authStatusError;
            $response['locate']  = inlink('view', "problemID=$problemID");
            $this->send($response);
        }
        if($_POST)
        {
            $changes = $this->problem->editSpecial($problemID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes)
            {
                $actionID = $this->loadModel('action')->create('problem', $problemID, 'edited', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $this->view->title   = $problem;
        $this->view->problem = $this->problem->getByID($problemID);
        $this->display();
    }
    public function editSpecialQA($problemID = 0)
    {
        $problem = $this->loadModel('problem')->getByID($problemID);

        if($_POST)
        {
            $changes = $this->problem->editSpecialQA($problemID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes)
            {
                $actionID = $this->loadModel('action')->create('problem', $problemID, 'editspecialed');
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $this->view->title   = $problem;
        $this->view->problem = $this->problem->getByID($problemID);
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: workloadEdit
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:52
     * Desc: This is the code comment. This method is called workloadEdit.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $problemID
     * @param int $consumedID
     */
    public function workloadEdit($problemID = 0, $consumedID = 0)
    {
        $problem = $this->loadModel('problem')->getByID($problemID);
        $flag    = $this->problem->isClickable($problem, 'workloadEdit');
        if(!$flag){
            $response['result']  = 'fail';
            $response['message'] = $this->lang->problem->authStatusError;
            $response['locate']  = inlink('view', "problemID=$problemID");
            $this->send($response);
        }
        if($_POST)
        {
            $changes = $this->problem->workloadEdit($problemID, $consumedID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes || $this->post->comment != '')
            {
                $actionID = $this->loadModel('action')->create('problem', $problemID, 'edited', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $this->view->title    = $this->lang->problem->workloadEdit;
        $this->view->problem  = $problem;
        $this->view->users    = $this->loadModel('user')->getPairs('noclosed');

        $consumed = $this->problem->getConsumedByID($consumedID);
        //相关配合人员详情信息
        $consumed->details = $this->loadModel('consumed')->getConsumedDetailsArray($consumed->details);
        $this->view->consumed = $consumed;

        //检查是否是最后一条工作量信息
        $isLastConsumed =  $this->loadModel('consumed')->checkIsLastConsumed($consumedID, $problemID, 'problem');
        $this->view->isLastConsumed = $isLastConsumed;
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: workloadDelete
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:53
     * Desc: This is the code comment. This method is called workloadDelete.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $problemID
     * @param int $consumedID
     */
    public function workloadDelete($problemID = 0, $consumedID = 0)
    {
        $problem = $this->loadModel('problem')->getByID($problemID);
        $flag    = $this->problem->isClickable($problem, 'workloadDelete');
        if(!$flag){
            $response['result']  = 'fail';
            $response['message'] = $this->lang->problem->authStatusError;
            $response['locate']  = inlink('view', "problemID=$problemID");
            $this->send($response);
        }
        if($_POST)
        {
            $changes = $this->problem->workloadDelete($problemID, $consumedID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes || $this->post->comment != '')
            {
                $actionID = $this->loadModel('action')->create('problem', $problemID, 'edited', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $this->view->title   = $this->lang->problem->workloadDelete;
        $this->view->problem = $problem;
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: workloadDetails
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:53
     * Desc: This is the code comment. This method is called workloadDetails.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $problemID
     * @param int $consumedID
     */
    public function workloadDetails($problemID = 0, $consumedID = 0)
    {
        $this->view->title    = $this->lang->problem->workloadDetails;
        $this->view->users    = $this->loadModel('user')->getPairs('noletter');
        $this->view->details  = $this->loadModel('consumed')->getWorkloadDetails($consumedID);
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: editAssignedTo
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:53
     * Desc: This is the code comment. This method is called editAssignedTo.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $problemID
     */
    public function editAssignedTo($problemID = 0)
    {
        $problem = $this->loadModel('problem')->getByID($problemID);
        $flag    = $this->problem->isClickable($problem, 'editAssignedTo');
        if(!$flag){
            $response['result']  = 'fail';
            $response['message'] = $this->lang->problem->authStatusError;
            $response['locate']  = inlink('view', "problemID=$problemID");
            $this->send($response);
        }
        if($_POST)
        {
            $changes = $this->problem->editAssignedTo($problemID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes || $this->post->comment != '')
            {
                $actionID = $this->loadModel('action')->create('problem', $problemID, 'editedassignto', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        // Obtain the receiver.
        $acceptUser = $this->dao->select('account')->from(TABLE_CONSUMED)
             ->where('objectType')->eq('problem')
             ->andWhere('objectID')->eq($problemID)
             ->andWhere('`before`')->eq('assigned')
             ->fetch('account');

        $this->view->title      = $this->lang->problem->editAssignedTo;
        $this->view->problem    = $problem;
        $this->view->users      = $this->loadModel('user')->getPairs('noclosed');
        $this->view->acceptUser = $acceptUser;

        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: view
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:53
     * Desc: This is the code comment. This method is called view.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $problemID
     */
    public function view($problemID = 0)
    {
        $problem = $this->problem->getByID($problemID);

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
        $this->view->title   = $this->lang->problem->view;
        $this->view->users   = $this->loadModel('user')->getPairs('noclosed|noletter');
        $this->view->actions = $this->loadModel('action')->getList('problem', $problemID);
        $this->view->problem = $problem;
        $this->view->apps    = $this->loadModel('application')->getapplicationInfo();
        $this->view->objects = $this->loadModel('secondline')->getByID($problemID, 'problem');

        $this->view->depts   = $this->loadModel('dept')->getOptionMenu();
        $nodes = $this->loadModel('review')->getNodes('problem', $problemID,$problem->version);
        //修正历史数据，原来是三节点，现在是四节点，所以要创建一个同步清总的数据
        if(count($nodes) == 3){
            $childNodeList = array();
            $childNode = new stdClass();
            $childNode->reviewer = $problem->createdBy == 'guestjx' ? 'guestjx' : 'guestjk';
            $node = new stdClass();
            $node->type = '1';
            $node->stage = '3';
            //重组数组
            $newNodes = array();
            foreach ($nodes as $key => $oldnode ){
                if($key == 2){
                    if($nodes[$key-1]->status == 'pass'){
                        $childNode->status = 'syncsuccess';
                        $childNode->comment = '反馈单数据同步成功';
                        array_push($childNodeList, $childNode);
                        $node->status = 'syncsuccess';
                        $node->reviewers = $childNodeList;
                    }else{
                        array_push($childNodeList, $childNode);
                        $node->status = 'wait';
                        $node->reviewers = $childNodeList;
                    }
                    array_push($newNodes, $node);
                }
                array_push($newNodes, $oldnode);
            }
            $nodes = $newNodes;
        }
        $this->view->nodes = $nodes;
        $this->view->allNodes = $nodes;
        if($problem->product){
            $productid = explode(',',$problem->product);
            foreach ($productid as $item) {
               $products[] = $item == '99999' ? array('name'=>'无') : $this->loadModel('product')->getByID($item);
            }
            $this->view->productName  = implode('，',array_column($products,'name'));
        }

        if($problem->productPlan){
            $productPlanid = explode(',',$problem->productPlan);
            foreach ($productPlanid as $item) {
                $productPlans[] =  $item == '1' ? array('title'=>'无') :$this->loadModel('productplan')->getByID($item);
            }
            $this->view->productPlan = implode('，',array_column($productPlans,'title'));
        }
        if($problem->projectPlan){
            $id = $this->dao->select('id')->from(TABLE_PROJECTPLAN)->where('project')->eq($problem->projectPlan)->fetch();
            $this->view->plan = $this->loadModel('projectplan')->getByID($id->id);
            $this->view->projectplanid = $id->id;
            //所属阶段
            $this->view->executions = $this->problem->getExecution($problem->projectPlan);
        }

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
if(!$myArray[0]){
            foreach ($myArray as $account) {
                if($userName == ""){
                    $userName .= $this->view->users[$account];
                }else{
                    $userName .= ",";
                    $userName .= $this->view->users[$account];
                }
            }
}
            $this->view->problem->feedbackToHandle = $userName;
        }
        //外部关闭时间
        if(!empty($problem->TimeOfClosing)){
            if($problem->TimeOfClosing == '1970-01-01 08:00:00' || $problem->TimeOfClosing == '0000-00-00 00:00:00'){
                $problem->TimeOfClosing = null;
            }
        }

        //问题单状态
        $acceptToUser = $this->dao->select('*')->from(TABLE_CONSUMED)
            ->where('objectType')->eq('problem')
            ->andWhere('objectID')->eq($problemID)
            ->andWhere('`before`')->eq('assigned')
            ->fetch();
        $this->view->problem->acceptToUser = $acceptToUser;

        // 详情页审批表格
        $this->lang->problem->reviewNodeLabelList['2'] = $problem->createdBy == 'guestjx' ? '同步金信' : '同步清总';
        $this->view->executions =  $this->getExecution($problem->projectPlan);
        $plans = explode(',',$problem->productPlan);
        $products = explode(',',$problem->product);
        $task = array();
        foreach ($products as $key=>$product) {
            $task[$key] =  $this->loadModel('demand')->getTaskName($problem->projectPlan,trim($problem->app,','),$product,$plans[$key],$problemID,1,'problem');
        }
        $this->view->task = $task ;//$this->loadModel('demand')->getTaskName($problem->projectPlan,trim($problem->app,','),$problem->product,$problem->productPlan,$problemID,1);
        $release = array();
        $taskid = array_column($this->view->task,'id');

        //$taskid = count($taskid) == 1 ? implode(',',$taskid).',' : implode(',',$taskid);
        foreach ($taskid as $key => $item) {
            $release[$key] = $this->loadModel('demand')->getBuildRelease($item);
        }

        $this->view->buildAndRelease = $release;//$this->loadModel('demand')->getBuildRelease($this->view->task ? $this->view->task->id : 0);
        $this->view->repeat = $this->problem->getCodes($problem->repeatProblem);

        $consumed = $this->loadModel('consumed')->getConsumed('problem',$problemID);
        $progressLook = implode(',',array_filter(array_unique(array_column($consumed,'account')))).','.$problem->dealUser.','.$problem->feedbackToHandle;
        $this->view->progressLook = explode(',',$progressLook);
        $endTime = '';
        if($problem->createdBy == 'guestcn'){
            $endTime = strpos($problem->dealAssigned,'0000-00-00') !== false  ? '' : helper::getTrueWorkDay($problem->dealAssigned ,$this->lang->problem->expireDaysList['days'],true).' '.date('H:i:s',strtotime($problem->dealAssigned));
        }else if($problem->createdBy == 'guestjx'){
            $endTime = strpos($problem->dealAssigned,'0000-00-00') !== false  ? '' : date('Y-m-d H:i:s',strtotime("+".$this->lang->problem->expireDaysList['jxExpireDays'].' day',strtotime($problem->dealAssigned))) ;
        }
        $this->view->endTime = $endTime && strpos($endTime,'0000-00-00') === false ? $endTime :'';
        $this->view->delayNodes    = $this->loadModel('review')->getNodesByStage('problemDelay', $problem->id, $problem->delayVersion);
        $this->view->changeNodes   = $this->loadModel('review')->getNodesByStage('problemChange', $problem->id, $problem->changeVersion);
        $this->view->feedbackFlag  = $this->problem->checkAllowReview($problem, $problem->version, $problem->reviewStage, $this->app->user->account)['result'];
        $this->view->delayErrorMsg = $this->problem->delayCheck($problem);
        $this->view->changeInfo    = $this->problem->getChangeInfo($problemID);
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: deal
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:53
     * Desc: This is the code comment. This method is called deal.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $problemID
     */
    public function deal($problemID)
    {
        if($_POST)
        {
            $changes = $this->problem->deal($problemID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $actionID = $this->loadModel('action')->create('problem', $problemID, 'deal', $this->post->comment);
            if($changes) $this->action->logHistory($actionID, $changes);
            //$this->problem->sendmail($problemID, $actionID);

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $problem = $this->loadModel('problem')->getByID($problemID);
        if(!in_array($problem->status, ['confirmed','assigned'])){
            $response['result']  = 'fail';
            $response['message'] = "状态不开处理";  //到开发中就不能人工处理了
            $this->send($response);
        }
        $statusList = array('' => '');
        switch($problem->status)
        {
        case 'confirmed':
            $statusList['assigned'] = $this->lang->problem->statusList['assigned'];
            break;
        case 'assigned':
            $statusList['feedbacked'] = $this->lang->problem->statusList['feedbacked']; //到开发中就不能人工处理了
            break;
        }

        $this->view->depts      = $this->loadModel('dept')->getOptionMenu();
        $this->view->title      = $this->lang->problem->edit;
        $this->view->users      = $this->loadModel('user')->getPairs('noclosed');
        $this->view->problem    = $problem;
        $this->view->statusList = $statusList;
        $this->view->plans      = array('0' => '') + $this->loadModel('projectplan')->getPairs();
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: feedback
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:53
     * Desc: This is the code comment. This method is called feedback.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $problemID
     */
    public function feedback($problemID)
    {
        if($_POST)
        {
            $changes = $this->problem->feedback($problemID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes || $this->post->comment)
            {
                $actionID = $this->loadModel('action')->create('problem', $problemID, 'feedbacked', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $this->view->depts   = $this->loadModel('dept')->getOptionMenu();
        $this->view->title   = $this->lang->problem->edit;
        $this->view->users   = $this->loadModel('user')->getPairs('noclosed');
        $this->view->problem = $this->problem->getByID($problemID);
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: suspend
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:53
     * Desc: This is the code comment. This method is called suspend.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $problemID
     */
    public function suspend($problemID = 0)
    {
        if($_POST)
        {
            $changes = $this->problem->suspend($problemID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes || $this->post->comment != '')
            {
                $actionID = $this->loadModel('action')->create('problem', $problemID, 'suspended', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $this->view->title   = $this->lang->problem->suspend;
        $this->view->problem = $this->problem->getByID($problemID);
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: start
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:53
     * Desc: This is the code comment. This method is called start.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $problemID
     */
    public function start($problemID = 0)
    {
        if($_POST)
        {
            $changes = $this->problem->start($problemID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes || $this->post->comment != '')
            {
                $actionID = $this->loadModel('action')->create('problem', $problemID, 'started', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $this->view->title   = $this->lang->problem->start;
        $this->view->problem = $this->problem->getByID($problemID);
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: close
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:53
     * Desc: This is the code comment. This method is called close.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $problemID
     */
    public function close($problemID = 0)
    {
        $problem = $this->loadModel('problem')->getByID($problemID);
        $flag    = $this->problem->isClickable($problem, 'close');
        if(!$flag){
            $response['result']  = 'fail';
            $response['message'] = $this->lang->problem->authStatusError;
            $response['locate']  = inlink('view', "problemID=$problemID");
            $this->send($response);
        }
        //查询有无在途的变更
        $message = $this->loadModel('problem')->closeCheckDelay($problem);
        if(!empty($message) ){
            echo js::alert($message);
            die(js::reload('parent'));
        }
        if($_POST)
        {
            $this->problem->close($problemID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($this->post->comment)
            {
                $this->loadModel('action')->create('problem', $problemID, 'closed', $this->post->comment);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $this->view->depts   = $this->loadModel('dept')->getOptionMenu();
        $this->view->title   = $this->lang->problem->edit;
        $this->view->users   = $this->loadModel('user')->getPairs('noclosed');
        $this->view->problem = $problem;
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: export
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:53
     * Desc: This is the code comment. This method is called export.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param string $orderBy
     * @param string $browseType
     */
    public function export($orderBy = 'id_desc', $browseType = 'all')
    {
        /* format the fields of every problem in order to export data. */
        if($_POST)
        {
            $this->loadModel('file');
            $problemLang   = $this->lang->problem;
            $problemConfig = $this->config->problem;

            /* Create field lists. */
            $fields = $this->post->exportFields ? $this->post->exportFields : explode(',', $problemConfig->list->exportFields);
            foreach($fields as $key => $fieldName)
            {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = isset($problemLang->$fieldName) ? $problemLang->$fieldName : $fieldName;
                unset($fields[$key]);
            }
            /* Get problems. */
            $problems = array();
            if($this->session->problemOnlyCondition)
            {
                $problems = $this->dao->select('*')->from(TABLE_PROBLEM)->where($this->session->problemQueryCondition)
                    ->andWhere('status')->ne('deleted')
                    ->beginIF($this->post->exportType == 'selected')->andWhere('id')->in($this->cookie->checkedItem)->fi()
                    ->orderBy($orderBy)->fetchAll('id');
            }
            else
            {
                $stmt = $this->dbh->query($this->session->problemQueryCondition . ($this->post->exportType == 'selected' ? " AND id IN({$this->cookie->checkedItem})" : '') . " ORDER BY " . strtr($orderBy, '_', ' '));
                while($row = $stmt->fetch()) $problems[$row->id] = $row;
            }
            $problemIdList = array_keys($problems);

            /* Get users, products and executions. */
            $users = $this->loadModel('user')->getPairs('noletter');
            $apps  = $this->loadModel('application')->getapplicationNameCodePairs();
            $depts = $this->loadModel('dept')->getTopPairs();
            $dmap  = $this->dao->select('account,dept')->from(TABLE_USER)->fetchAll('account');
            $plans = $this->loadModel('project')->getPairs();

            $productPairs     = $this->loadModel('product')->getSimplePairs();
            $productPlanPairs = $this->loadModel('productplan')->getSimplePairs();
            // Obtain the receiver.
            $cmap = $this->dao->select('objectID, account')->from(TABLE_CONSUMED)
                 ->where('objectType')->eq('problem')
                 ->andWhere('`before`')->eq('assigned')
                 ->orderBy('id')
                 ->fetchPairs();

            $this->loadModel('secondline');
            foreach($problems as $problem)
            {
                $problem->status    = $problemLang->statusList[$problem->status];
                $problem->source    = $problemLang->sourceList[$problem->source];
                $problem->type      = $problemLang->typeList[$problem->type];
                $problem->severity  = $problemLang->severityList[$problem->severity];
                $problem->pri       = $problemLang->priList[$problem->pri];
                $problem->IssueStatus = zget($this->lang->problem->IssueStatusList,$problem->IssueStatus,$problem->IssueStatus);
                $problem->createdDept = $depts[$dmap[$problem->createdBy]->dept];
                $problem->createdBy   = $users[$problem->createdBy];
                $problem->fixType     = zget($problemLang->fixTypeList, $problem->fixType, '');

                $problem->acceptUser  = isset($cmap[$problem->id]) ? $users[$cmap[$problem->id]] : '';
                $problem->acceptDept  = isset($cmap[$problem->id]) ? $depts[$dmap[$cmap[$problem->id]]->dept] : '';

                $problem->projectPlan = zget($plans, $problem->projectPlan, '');
                if(in_array($problemLang->statusList[$problem->status], ['feedbacked','build','released','delivery','onlinesuccess','closed'])) {
                    $problem->dealUser = '';
                }else{
                    $problem->dealUser    = zmget($users, $problem->dealUser);
                }
                $problem->editedBy    = zget($users, $problem->editedBy, '');
                $problem->closedBy    = zget($users, $problem->closedBy, '');




                // 处理所属应用系统。
                if($problem->app)
                {
                    $as = array();
                    foreach(explode(',', $problem->app) as $app)
                    {
                        if(!$app) continue;
                        $as[] = zget($apps, $app);
                    }
                    $problem->app = implode(',', $as);
                }

                // 处理系统分类。
                if($problem->isPayment)
                {
                    $as = array();
                    foreach(explode(',', $problem->isPayment) as $paymentID)
                    {
                        if(!$paymentID) continue;
                        $as[] =  zget($this->lang->application->isPaymentList, $paymentID, $paymentID);
                    }
                    $isPayment = implode(',', $as);
                    $problem->isPayment = $isPayment;
                }
                /* 获取所属产品。*/
                if($problem->product) $problem->product = zget($productPairs, $problem->product, $problem->product);

                /* 获取所属产品计划。*/
                if($problem->productPlan) $problem->productPlan = zget($productPlanPairs, $problem->productPlan, $problem->productPlan);
                /* 获取制版次数。不需要查询获取了，有字段累计增加。*/
                //$problem->buildTimes = $this->problem->getBuild($problem->id);

                /* 获取关联的生产变更，数据修正，数据获取。*/
                $problem->relationModify = '';
                $problem->relationFix    = '';
                $problem->relationGain   = '';
                $relationObject = $this->secondline->getByID($problem->id, 'problem');
                foreach($relationObject['modify'] as $objectID => $object)
                {
                    $problem->relationModify .= $object . "\r\n";
                }

                foreach($relationObject['fix'] as $objectID => $object)
                {
                    $problem->relationFix .= $object . "\r\n";
                }

                foreach($relationObject['gain'] as $objectID => $object)
                {
                    $problem->relationGain .= $object . "\r\n";
                }
            }

            $this->post->set('fields', $fields);
            $this->post->set('rows', $problems);
            $this->post->set('kind', 'problem');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        $this->view->fileName        = $this->lang->problem->exportName;
        $this->view->allExportFields = $this->config->problem->list->exportFields;
        $this->view->customExport    = true;
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: delete
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:53
     * Desc: This is the code comment. This method is called delete.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $problemID
     */
    public function delete($problemID)
    {
        $problem = $this->problem->getByID($problemID);
        $flag    = $this->problem->isClickable($problem, 'delete');
        if(!$flag){
            $response['result']  = 'fail';
            $response['message'] = $this->lang->problem->authStatusError;
            $response['locate']  = inlink('view', "problemID=$problemID");
            $this->send($response);
        }
        if(!empty($_POST))
        {
            $this->dao->update(TABLE_PROBLEM)->set('status')->eq('deleted')->where('id')->eq($problemID)->exec();
            $actionID = $this->loadModel('action')->create('problem', $problemID, 'deleted', $this->post->comment);

            //2022.4.27 tangfei 删除与二线管理单子的关联关系
            $sql = "delete from zt_secondline where (objectType='modify'or objectType='fix' or objectType='gain') and relationID=$problemID  and relationType='problem';";
            $this->dao->query($sql);
            //删除问题更新任务名
            $problem = $this->problem->getByID($problemID);
            /*$task = $this->loadModel('demand')->getTaskName($problem->projectPlan,$problem->app,$problem->product,$problem->productPlan,$problemID,1);
            if($task){
               $this->dao->update(TABLE_PROBLEM)->set('execution')->eq('')->where('id')->eq($problemID)->exec();
                $this->loadModel('task')->deleteCodeUpdateTask($task,$problem->code);
            }*/
            $this->loadModel('task')->checkCodeExist($problem->projectPlan, $problem->app, 'problem', $problem->code, $problem, 1);
            $backUrl =  $this->session->problemList ? $this->session->problemList : inLink('browse');
            if(isonlybody()) die(js::closeModal('parent.parent', $backUrl));
            die(js::reload('parent'));
        }

        $this->view->actions = $this->loadModel('action')->getList('problem', $problemID);
        $this->view->problem = $problem;
        $this->view->users   = $this->loadModel('user')->getPairs();
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: exportTemplate
     * User: tangfei
     * Date: 2022/2/20
     * Time: 14:53
     * Desc: This is the code comment. This method is called exportTemplate.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     */
    public function exportTemplate()
    {
        if($_POST)
        {
            $this->problem->setListValue();

            foreach($this->config->problem->export->templateFields as $field) $fields[$field] = $this->lang->problem->$field;
            $this->post->set('fields', $fields);
            $this->post->set('kind', 'problem');
            $this->post->set('rows', array());
            $this->post->set('extraNum', $this->post->num);
            $this->post->set('fileName', 'problemTemplate');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: exportWord
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:53
     * Desc: This is the code comment. This method is called exportWord.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $problemID
     */
    public function exportWord($problemID)
    {
        $problem = $this->problem->getById($problemID);
        $users   = $this->loadModel('user')->getPairs('noletter');

        $this->app->loadClass('phpword', true);
        $phpWord = new PhpOffice\PhpWord\PHPWord();
        $section = $phpWord->addSection();

        $phpWord->addParagraphStyle('pStyle', array('spacing'=>100));
        $phpWord->addTitleStyle(1, array('size' => 15, 'name' => '黑体'), array('space' => array('before' => 100, 'after' => 200), 'align' => 'center'));
        $phpWord->addTitleStyle(2, array('size' => 10, 'name' => '黑体'), array('space' => array('before' => 100, 'after' => 100)));
        $phpWord->addTitleStyle(3, array('size' => 10, 'name' => '黑体'), array('space' => array('before' => 100, 'after' => 100)));

        $phpWord->addParagraphStyle('align_right', array('lineHeight' => "1.2", 'spaceBefore' => 0, 'spaceAfter' => 0, 'align' => 'right'));
        $phpWord->addFontStyle('font_default', array('name'=>'Arial', 'size'=>11, 'color'=>'37363a'));
        $phpWord->addFontStyle('font_bold', array('name'=>'Arial', 'size'=>11, 'color'=>'000000', 'bold'=> true));

        $section->addTitle($this->lang->problem->exportTitle, 1);
        $section->addText($this->lang->problem->code . ' ' . $problem->code, 'font_default', 'align_right');

        $tableStyle = array(
            'unit' => \PhpOffice\PhpWord\Style\Table::WIDTH_PERCENT,
            'width' => 100 * 50,
            'cellMargin' => 50,
            'borderSize' => 10,
            'borderColor' => '000000',
        );
        $cellStyle = array();
        $table = $section->addTable($tableStyle);
        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->problem->abstract);
        $table->addCell(1000, $cellStyle)->addText($problem->abstract);
        $table->addCell(1000, $cellStyle)->addText($this->lang->problem->code);
        $table->addCell(1000, $cellStyle)->addText($problem->code);

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->problem->type);
        $table->addCell(1000, $cellStyle)->addText(zget($this->lang->problem->typeList, $problem->type, ''));
        $table->addCell(1000, $cellStyle)->addText($this->lang->problem->status);
        $table->addCell(1000, $cellStyle)->addText(zget($this->lang->problem->statusList, $problem->status, ''));

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->problem->createdBy);
        $table->addCell(1000, $cellStyle)->addText(zget($users, $problem->createdBy, ''));
        $table->addCell(1000, $cellStyle)->addText($this->lang->problem->createdDate);
        $table->addCell(1000, $cellStyle)->addText($problem->createdDate);

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->problem->changeVersion);
        $table->addCell(1000, $cellStyle)->addText($problem->changeVersion);
        $table->addCell(1000, $cellStyle)->addText($this->lang->problem->successVersion);
        $table->addCell(1000, $cellStyle)->addText($problem->successVersion);

        $apps = $this->loadModel('application')->getapplicationNameCodePairs();
        $as = array();
        foreach(explode(',', $problem->app) as $app)
        {
            if(!$app) continue;
            $as[] = zget($apps, $app);
        }
        $problem->app = implode(',', $as);

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->problem->app);
        $table->addCell(3000, array('gridSpan' => 3))->addText($problem->app);

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->problem->desc);
        $table->addCell(3000, array('gridSpan' => 3))->addText(strip_tags($problem->desc));

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->problem->reason);
        $table->addCell(3000, array('gridSpan' => 3))->addText(strip_tags($problem->reason));

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->problem->solution);
        $table->addCell(3000, array('gridSpan' => 3))->addText(strip_tags($problem->solution));

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->problem->state);
        $table->addCell(3000, array('gridSpan' => 3))->addText(strip_tags($problem->state));

        // Obtain the receiver.
        $acceptUser = $this->dao->select('account')->from(TABLE_CONSUMED)
             ->where('objectType')->eq('problem')
             ->andWhere('objectID')->eq($problemID)
             ->andWhere('`before`')->eq('assigned')
             ->fetch('account');

        $acceptUserName = $acceptUser ? zget($users, $acceptUser, '') : '';
        $acceptDeptID   = 0;
        $acceptDeptName = '';
        if($acceptUser)   $acceptDeptID   = $this->dao->select('dept')->from(TABLE_USER)->where('account')->eq($acceptUser)->fetch('dept');
        if($acceptDeptID) $acceptDeptName = $this->dao->select('name')->from(TABLE_DEPT)->where('id')->eq($acceptDeptID)->fetch('name');

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->problem->acceptUser);
        $table->addCell(3000, array('gridSpan' => 3))->addText($acceptUserName);

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->problem->acceptDept);
        $table->addCell(3000, array('gridSpan' => 3))->addText($acceptDeptName);

        /* Review. */
        $table->addRow();
        $table->addCell(4000, array('gridSpan' => 4))->addText( $this->lang->problem->statusMove, 'font_bold', array('align' => 'center'));

        $table->addRow();
        $table->addCell(1000, $cellStyle)->addText($this->lang->problem->nodeUser);
       // $table->addCell(1000, $cellStyle)->addText($this->lang->problem->consumed);
        $table->addCell(1000, $cellStyle)->addText($this->lang->problem->before);
        $table->addCell(2000, array('gridSpan' => 2))->addText($this->lang->problem->after);

        foreach($problem->consumed as $c)
        {
            $table->addRow();
            $table->addCell(1000, $cellStyle)->addText(zget($users, $c->createdBy, ''));
           // $table->addCell(1000, $cellStyle)->addText($c->consumed . '' . $this->lang->hour);
            $table->addCell(1000, $cellStyle)->addText(zget($this->lang->problem->statusList, $c->before, '-'));
            $table->addCell(2000, array('gridSpan' => 2))->addText(zget($this->lang->problem->statusList, $c->after, '-'));
        }
        //增加外部问题单信息
        if($problem->IssueId != null){
            $table->addRow();
            $table->addCell(4000, array('gridSpan' => 4))->addText($this->lang->problem->sysncInfo, 'font_bold', array('align' => 'center'));

            $table->addRow();
            $table->addCell(1000, $cellStyle)->addText($this->lang->problem->IssueId);
            $table->addCell(1000, $cellStyle)->addText($problem->IssueId);
            $table->addCell(1000, $cellStyle)->addText($this->lang->problem->DepIdofIssueCreator);
            $table->addCell(1000, $cellStyle)->addText($problem->DepIdofIssueCreator);

            $table->addRow();
            $table->addCell(1000, $cellStyle)->addText($this->lang->problem->RecoveryTime);
            $table->addCell(1000, $cellStyle)->addText($problem->RecoveryTime);
            $table->addCell(1000, $cellStyle)->addText($this->lang->problem->IssueCreator);
            $table->addCell(1000, $cellStyle)->addText($problem->IssueCreator);

            $table->addRow();
            $table->addCell(1000, $cellStyle)->addText($this->lang->problem->TeleNoOfCreator);
            $table->addCell(1000, $cellStyle)->addText($problem->TeleNoOfCreator);
            $table->addCell(1000, $cellStyle)->addText($this->lang->problem->severity);
            $table->addCell(1000, $cellStyle)->addText(zget($this->lang->problem->severityList, $problem->severity, $problem->severity));

            $table->addRow();
            $table->addCell(1000, $cellStyle)->addText($this->lang->problem->NodeIdOfIssue);
            $table->addCell(1000, $cellStyle)->addText($problem->NodeIdOfIssue);
            $table->addCell(1000, $cellStyle)->addText($this->lang->problem->TimeOfOccurrence);
            $table->addCell(1000, $cellStyle)->addText($problem->occurDate);

            $table->addRow();
            $table->addCell(1000, $cellStyle)->addText($this->lang->problem->TimeOfReport);
            $table->addCell(1000, $cellStyle)->addText($problem->TimeOfReport);
            $table->addCell(1000, $cellStyle)->addText($this->lang->problem->IssueStatus);
            $table->addCell(1000, $cellStyle)->addText(zget($this->lang->problem->IssueStatusList,$problem->IssueStatus,$problem->IssueStatus));

            $table->addRow();
            $table->addCell(1000, $cellStyle)->addText($this->lang->problem->source);
            $table->addCell(1000, $cellStyle)->addText(zget($this->lang->problem->sourceList, $problem->source, $problem->source));
            $table->addCell(1000, $cellStyle)->addText($this->lang->problem->ProblemSource);
            $table->addCell(1000, $cellStyle)->addText($problem->ProblemSource);

            $table->addRow();
            $table->addCell(1000, $cellStyle)->addText($this->lang->problem->TimeOfClosing);
            $table->addCell(3000, array('gridSpan' => 3))->addText($problem->TimeOfClosing);

            $table->addRow();
            $table->addCell(4000, array('gridSpan' => 4))->addText($this->lang->problem->problemFeedbackInfor, 'font_bold', array('align' => 'center'));

            $table->addRow();
            $table->addCell(1000, $cellStyle)->addText($this->lang->problem->ReviewStatus);
            $table->addCell(1000, $cellStyle)->addText(zget($this->lang->problem->feedbackStatusList, $problem->ReviewStatus));
            $table->addCell(1000, $cellStyle)->addText($this->lang->problem->feedbackNum);
            $table->addCell(1000, $cellStyle)->addText($problem->feedbackNum);

            $table->addRow();
            $table->addCell(1000, $cellStyle)->addText($this->lang->problem->feedbackToHandle);
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
            $table->addCell(1000, $cellStyle)->addText($problem->feedbackToHandle);
            $table->addCell(1000, $cellStyle)->addText($this->lang->problem->problemFeedbackId);
            $table->addCell(1000, $cellStyle)->addText($problem->IssueId);

            $table->addRow();
            $table->addCell(1000, $cellStyle)->addText($this->lang->problem->ifReturn);
            $table->addCell(1000, $cellStyle)->addText(zget($this->lang->problem->ifReturnList, $problem->ifReturn));
            $table->addCell(1000, $cellStyle)->addText($this->lang->problem->IfultimateSolution);
            $table->addCell(1000, $cellStyle)->addText(zget($this->lang->problem->ifultimateSolutionList, $problem->IfultimateSolution));

            $table->addRow();
            $table->addCell(1000, $cellStyle)->addText($this->lang->problem->TeleOfIssueHandler);
            $table->addCell(1000, $cellStyle)->addText($problem->TeleOfIssueHandler);
            $table->addCell(1000, $cellStyle)->addText($this->lang->problem->PlannedTimeOfChange);
            $table->addCell(1000, $cellStyle)->addText($problem->PlannedTimeOfChange);

            $table->addRow();
            $table->addCell(1000, $cellStyle)->addText($this->lang->problem->PlannedDateOfChangeReport);
            $table->addCell(1000, $cellStyle)->addText($problem->PlannedDateOfChangeReport);
            $table->addCell(1000, $cellStyle)->addText($this->lang->problem->PlannedDateOfChange);
            $table->addCell(1000, $cellStyle)->addText($problem->PlannedDateOfChange);

            $table->addRow();
            $table->addCell(1000, $cellStyle)->addText($this->lang->problem->CorresProduct);
            $table->addCell(3000, array('gridSpan' => 3))->addText($problem->CorresProduct);

            $table->addRow();
            $table->addCell(1000, $cellStyle)->addText($this->lang->problem->EffectOfService);
            $table->addCell(3000, array('gridSpan' => 3))->addText(strip_tags($problem->EffectOfService));

            $table->addRow();
            $table->addCell(1000, $cellStyle)->addText($this->lang->problem->ChangeIdRelated);
            $table->addCell(3000, array('gridSpan' => 3))->addText(strip_tags($problem->ChangeIdRelated));

            $table->addRow();
            $table->addCell(1000, $cellStyle)->addText($this->lang->problem->IncidentIdRelated);
            $table->addCell(3000, array('gridSpan' => 3))->addText(strip_tags($problem->IncidentIdRelated));

            $table->addRow();
            $table->addCell(1000, $cellStyle)->addText($this->lang->problem->DrillCausedBy);
            $table->addCell(3000, array('gridSpan' => 3))->addText(strip_tags($problem->DrillCausedBy));

            $table->addRow();
            $table->addCell(1000, $cellStyle)->addText($this->lang->problem->Optimization);
            $table->addCell(3000, array('gridSpan' => 3))->addText(strip_tags($problem->Optimization));

            $table->addRow();
            $table->addCell(1000, $cellStyle)->addText($this->lang->problem->Tier1Feedback);
            $table->addCell(3000, array('gridSpan' => 3))->addText(strip_tags($problem->Tier1Feedback));

            $table->addRow();
            $table->addCell(1000, $cellStyle)->addText($this->lang->problem->solution);
            $table->addCell(3000, array('gridSpan' => 3))->addText(strip_tags($problem->solution));

            $table->addRow();
            $table->addCell(1000, $cellStyle)->addText($this->lang->problem->ChangeSolvingTheIssue);
            $table->addCell(3000, array('gridSpan' => 3))->addText(strip_tags($problem->ChangeSolvingTheIssue));

            $table->addRow();
            $table->addCell(1000, $cellStyle)->addText($this->lang->problem->ReasonOfIssueRejecting);
            $table->addCell(3000, array('gridSpan' => 3))->addText(strip_tags($problem->ReasonOfIssueRejecting));

            $table->addRow();
            $table->addCell(1000, $cellStyle)->addText($this->lang->problem->EditorImpactscope);
            $table->addCell(3000, array('gridSpan' => 3))->addText(strip_tags($problem->EditorImpactscope));

            $table->addRow();
            $table->addCell(1000, $cellStyle)->addText($this->lang->problem->revisionRecord);
            $table->addCell(3000, array('gridSpan' => 3))->addText(strip_tags($problem->revisionRecord));
        }

        $this->loadModel('file')->export2Word($this->lang->problem->exportTitle . $problem->code, $phpWord);
    }

    /**
     * Project: chengfangjinke
     * Method: import
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:53
     * Desc: This is the code comment. This method is called import.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     */
    public function import()
    {
        if($_FILES)
        {   
            $file = $this->loadModel('file')->getUpload('file');
            $file = $file[0];
            if($file['extension'] != 'xlsx') die(js::alert($this->lang->file->onlySupportXLSX));

            $fileName = $this->file->savePath . $this->file->getSaveName($file['pathname']);
            move_uploaded_file($file['tmpname'], $fileName);

            $phpExcel  = $this->app->loadClass('phpexcel');
            $phpReader = new PHPExcel_Reader_Excel2007(); 
            if(!$phpReader->canRead($fileName))
            {   
                $phpReader = new PHPExcel_Reader_Excel5(); 
                if(!$phpReader->canRead($fileName))die(js::alert($this->lang->excel->canNotRead));
            }
            $phpExcel     = $phpReader->load($fileName);
            $currentSheet = $phpExcel->getSheet(1);
            $allRows      = $currentSheet->getHighestRow();
            $allColumns   = $currentSheet->getHighestColumn();

            $seMap   = array('一级' => 1, '二级' => 2, '三级' => 3, '四级' => 4);
            $typeMap = array_flip($this->lang->problem->typeList);
            $priMap  = array_flip($this->lang->problem->priList);

            $userMap = array();
            $users   = $this->loadModel('user')->getPairs('noletter');
            foreach($users as $key => $u) $userMap[$u] = $key;

            $deptMap = array();
            $depts   = $this->loadModel('dept')->getTopPairs();
            foreach($depts as $key => $d) $deptMap[trim($d, '/')] = $key;

            $appMap = array();
            $apps   = $this->loadModel('application')->getapplicationNameCodePairs();
            foreach($apps as $key => $a) $appMap[$a] = $key;

            /* 检查应用系统，部门和人员 */
            $error = false;
            for($currentRow = 2; $currentRow <= $allRows; $currentRow++)
            {
                if(!$this->getCalculatedValue($currentSheet, 'A' . $currentRow)) break;

                $app = $this->getCalculatedValue($currentSheet, 'F' . $currentRow);
                if($app and !isset($appMap[$app]))
                {
                    a("缺少应用系统: " . $this->getCalculatedValue($currentSheet, 'F' . $currentRow));
                    $error = true;
                }

                $createdDept = $this->getCalculatedValue($currentSheet, 'B' . $currentRow);
                if(!isset($deptMap[$createdDept]))
                {
                    a("缺少发起部门: " . $this->getCalculatedValue($currentSheet, 'B' . $currentRow));
                    $error = true;
                }

                $acceptDept = $this->getCalculatedValue($currentSheet, 'C' . $currentRow);
                if($acceptDept and !isset($deptMap[$acceptDept]))
                {
                    a("缺少受理部门: " . $this->getCalculatedValue($currentSheet, 'C' . $currentRow));
                    $error = true;
                }

                $createdUser = trim($this->getCalculatedValue($currentSheet, 'D' . $currentRow));
                if(!isset($userMap[$createdUser]))
                {
                    a("缺少创建人: " . $this->getCalculatedValue($currentSheet, 'D' . $currentRow));
                    $error = true;
                }

                $acceptUser = trim($this->getCalculatedValue($currentSheet, 'E' . $currentRow));
                if($acceptUser and !isset($userMap[$acceptUser]))
                {
                    a("缺少受理人: " . $this->getCalculatedValue($currentSheet, 'E' . $currentRow));
                    $error = true;
                }

                $acceptUser = trim($this->getCalculatedValue($currentSheet, 'R' . $currentRow));
                if($acceptUser and !isset($userMap[$acceptUser]))
                {
                    a("缺少提交人: " . $this->getCalculatedValue($currentSheet, 'R' . $currentRow));
                    $error = true;
                }

                $acceptUser = trim($this->getCalculatedValue($currentSheet, 'T' . $currentRow));
                if($acceptUser and !isset($userMap[$acceptUser]))
                {
                    a("缺少分配人: " . $this->getCalculatedValue($currentSheet, 'T' . $currentRow));
                    $error = true;
                }

                $acceptUser = trim($this->getCalculatedValue($currentSheet, 'V' . $currentRow));
                if($acceptUser and !isset($userMap[$acceptUser]))
                {
                    a("缺少分析人: " . $this->getCalculatedValue($currentSheet, 'V' . $currentRow));
                    $error = true;
                }

                $acceptUser = trim($this->getCalculatedValue($currentSheet, 'X' . $currentRow));
                if($acceptUser and !isset($userMap[$acceptUser]))
                {
                    a("缺少解决人: " . $this->getCalculatedValue($currentSheet, 'X' . $currentRow));
                    $error = true;
                }

                $acceptUser = trim($this->getCalculatedValue($currentSheet, 'Z' . $currentRow));
                if($acceptUser and !isset($userMap[$acceptUser]))
                {
                    a("缺少制版人: " . $this->getCalculatedValue($currentSheet, 'Z' . $currentRow));
                    $error = true;
                }

                $acceptUser = trim($this->getCalculatedValue($currentSheet, 'AB' . $currentRow));
                if($acceptUser and !isset($userMap[$acceptUser]))
                {
                    a("缺少验证人: " . $this->getCalculatedValue($currentSheet, 'AB' . $currentRow));
                    $error = true;
                }

                $acceptUser = trim($this->getCalculatedValue($currentSheet, 'AD' . $currentRow));
                if($acceptUser and !isset($userMap[$acceptUser]))
                {
                    a("缺少受理人: " . $this->getCalculatedValue($currentSheet, 'AD' . $currentRow));
                    $error = true;
                }

                $acceptUser = trim($this->getCalculatedValue($currentSheet, 'AF' . $currentRow));
                if($acceptUser and !isset($userMap[$acceptUser]))
                {
                    a("缺少发布人: " . $this->getCalculatedValue($currentSheet, 'AF' . $currentRow));
                    $error = true;
                }

                /*
                $acceptUser = trim($this->getCalculatedValue($currentSheet, 'AH' . $currentRow));
                if($acceptUser and !isset($userMap[$acceptUser]))
                {
                    a("缺少交付人: " . $this->getCalculatedValue($currentSheet, 'AH' . $currentRow));
                    $error = true;
                }
                 */
            }

            if($error) exit;

            /* 开始插入 */
            for($currentRow = 2; $currentRow <= $allRows; $currentRow++)
            {
                if(!$this->getCalculatedValue($currentSheet, 'A' . $currentRow)) break;

                $problem = new stdClass();
                $problem->code        = $this->getCalculatedValue($currentSheet, 'A' . $currentRow);
                $problem->createdDept = $deptMap[trim($this->getCalculatedValue($currentSheet, 'B' . $currentRow))];
                $problem->acceptDept  = $deptMap[trim($this->getCalculatedValue($currentSheet, 'C' . $currentRow))];
                $problem->createdBy   = $userMap[trim($this->getCalculatedValue($currentSheet, 'D' . $currentRow))];
                $problem->acceptUser  = $userMap[trim($this->getCalculatedValue($currentSheet, 'E' . $currentRow))];

                $app = $this->getCalculatedValue($currentSheet, 'F' . $currentRow);
                if($app and !isset($appMap[$app]))
                {
                    echo "app error\n";
                    a($this->getCalculatedValue($currentSheet, 'F' . $currentRow));
                    exit;
                }

                $problem->app         = $app ? $appMap[$app] : '';
                $problem->createdDate = $this->getCalculatedValue($currentSheet, 'G' . $currentRow);
                $problem->abstract    = trim($this->getCalculatedValue($currentSheet, 'H' . $currentRow));
                $problem->desc        = $this->getCalculatedValue($currentSheet, 'I' . $currentRow);
                $problem->reason      = $this->getCalculatedValue($currentSheet, 'J' . $currentRow);
                $problem->solution    = $this->getCalculatedValue($currentSheet, 'K' . $currentRow);
                $problem->result      = $this->getCalculatedValue($currentSheet, 'L' . $currentRow);
                $problem->progress    = $this->getCalculatedValue($currentSheet, 'N' . $currentRow);
                $problem->pri         = $priMap[$this->getCalculatedValue($currentSheet, 'O' . $currentRow)];
                $problem->type        = $typeMap[$this->getCalculatedValue($currentSheet, 'P' . $currentRow)];
                $problem->severity    = $seMap[$this->getCalculatedValue($currentSheet, 'Q' . $currentRow)];
                $problem->dealUser    = 'zhangyun';
                $problem->status      = 'released';

                $closedDate = $this->getCalculatedValue($currentSheet, 'H' . $currentRow);
                $closedDate = str_replace('日', '', $closedDate);
                $closedDate = str_replace('年', '-', $closedDate);
                $closedDate = str_replace('月', '-', $closedDate);

                if(!$problem->createdDept or !$problem->createdBy or !$problem->acceptDept or !$problem->acceptUser)
                {
                    echo "user error\n";
                    a($problem);
                    exit;
                }

                if(!$problem->type)
                {
                    a($this->getCalculatedValue($currentSheet, 'O' . $currentRow));
                    echo "other error\n";
                    a($problem);
                    exit;
                }

                $this->dao->insert(TABLE_PROBLEM)->data($problem)->exec();
                $problemID = $this->dao->lastInsertId();

                $user     = trim($this->getCalculatedValue($currentSheet, 'R' . $currentRow));
                $consumed = $this->getCalculatedValue($currentSheet, 'S' . $currentRow);
                if($user)
                {
                    $this->createConsumed($problemID, '', 'confirmed', $userMap[$user], $consumed, $problem->createdDate);
                }

                $user     = trim($this->getCalculatedValue($currentSheet, 'T' . $currentRow));
                $consumed = $this->getCalculatedValue($currentSheet, 'U' . $currentRow);
                if($user)
                {
                    $this->createConsumed($problemID, 'confirmed', 'assigned', $userMap[$user], $consumed, $problem->createdDate);
                }

                $user     = trim($this->getCalculatedValue($currentSheet, 'V' . $currentRow));
                $consumed = $this->getCalculatedValue($currentSheet, 'W' . $currentRow);
                if($user)
                {
                    $this->createConsumed($problemID, 'assigned', 'feedback', $userMap[$user], $consumed, $problem->createdDate);
                }

                $user     = trim($this->getCalculatedValue($currentSheet, 'X' . $currentRow));
                $consumed = $this->getCalculatedValue($currentSheet, 'Y' . $currentRow);
                if($user)
                {
                    $this->createConsumed($problemID, 'feedback', 'solved', $userMap[$user], $consumed, $problem->createdDate);
                }

                $user     = trim($this->getCalculatedValue($currentSheet, 'Z' . $currentRow));
                $consumed = $this->getCalculatedValue($currentSheet, 'AA' . $currentRow);
                if($user)
                {
                    $this->createConsumed($problemID, 'solved', 'build', $userMap[$user], $consumed, $problem->createdDate);
                }

                $user     = trim($this->getCalculatedValue($currentSheet, 'AB' . $currentRow));
                $consumed = $this->getCalculatedValue($currentSheet, 'AC' . $currentRow);
                if($user)
                {
                    $this->createConsumed($problemID, 'build', 'testsuccess', $userMap[$user], $consumed, $problem->createdDate);
                }

                $user     = trim($this->getCalculatedValue($currentSheet, 'AD' . $currentRow));
                $consumed = $this->getCalculatedValue($currentSheet, 'AE' . $currentRow);
                if($user)
                {
                    $this->createConsumed($problemID, 'testsuccess', 'verifysuccess', $userMap[$user], $consumed, $problem->createdDate);
                }

                $user     = trim($this->getCalculatedValue($currentSheet, 'AF' . $currentRow));
                $consumed = $this->getCalculatedValue($currentSheet, 'AG' . $currentRow);
                if($user)
                {
                    $this->createConsumed($problemID, 'verifysuccess', 'released', $userMap[$user], $consumed, $problem->createdDate);
                }

                /*
                $user     = trim($this->getCalculatedValue($currentSheet, 'AH' . $currentRow));
                $consumed = $this->getCalculatedValue($currentSheet, 'AI' . $currentRow);
                if($user)
                {
                    $this->createConsumed($problemID, 'released', 'delivery', $userMap[$user], $consumed, $problem->createdDate);
                }

                $user     = trim($this->getCalculatedValue($currentSheet, 'AL' . $currentRow));
                $consumed = $this->getCalculatedValue($currentSheet, 'AM' . $currentRow);
                if($user)
                {
                    $this->createConsumed($problemID, 'delivery', 'onlinesuccess', $userMap[$user], $consumed, $closedDate);
                }

                $user     = trim($this->getCalculatedValue($currentSheet, 'AN' . $currentRow));
                $consumed = $this->getCalculatedValue($currentSheet, 'AO' . $currentRow);
                if($user)
                {
                    $this->createConsumed($problemID, 'onlinesuccess', 'closed', $userMap[$user], $consumed, $closedDate);
                }
                 */
            }

//            die(js::reload('parent.parent'));
            $this->session->set('fileImport', $fileName);
            die(js::locate(inlink('showImport'), 'parent.parent'));
        }

        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: showImport
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:51
     * Desc: This is the code comment. This method is called showImport.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $pagerID
     * @param int $maxImport
     * @param string $insert
     */
    public function showImport($pagerID = 1, $maxImport = 0, $insert = '')
    {
        $file    = $this->session->fileImport;
        $tmpPath = $this->loadModel('file')->getPathOfImportedFile();
        $tmpFile = $tmpPath . DS . md5(basename($file));

        $users   = $this->loadModel('user')->getPairs('noclosed');
        $apps    = array(0 => '') + $this->loadmodel('application')->getapplicationNameCodePairs();


        if($_POST)
        {
            $this->problem->createFromImport();
            if($this->post->isEndPage)
            {
                unlink($tmpFile);
                die(js::locate($this->createLink('problem','browse'), 'parent'));
            }
            else
            {
                die(js::locate(inlink('showImport', "pagerID=" . ($this->post->pagerID + 1) . "&maxImport=$maxImport&insert=" . zget($_POST, 'insert', '')), 'parent'));
            }
        }

        if(!empty($maxImport) and file_exists($tmpFile))
        {
            $problemData = unserialize(file_get_contents($tmpFile));
        }
        else
        {
            $pagerID = 1;
            $problemLang   = $this->lang->problem;
            $problemConfig = $this->config->problem;
            $fields      = explode(',',  $problemConfig->list->exportFields);
            foreach($fields as $key => $fieldName)
            {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = isset($problemLang->$fieldName) ? $problemLang->$fieldName : $fieldName;
                unset($fields[$key]);
            }

            $rows = $this->file->getRowsFromExcel($file);
            $problemData = array();
            foreach($rows as $key => $row)
            {
                if($key == 1) continue;
                if(!$row[0]) continue;

                $source = '';
                foreach($this->lang->problem->sourceList as $key => $t)
                {
                    if($row[1] == $t)
                    {
                        $source = $key;
                        break;
                    }
                }

                $severity = '';
                foreach($this->lang->problem->severityList as $key => $s)
                {
                    if($row[2] == $s)
                    {
                        $severity = $key;
                        break;
                    }
                }

                $app = '';
                $row[3] = explode(',',$row[3]);
                foreach($row[3] as $a1 )
                {
                    foreach($apps as $key => $a2)
                    {
                        if($a1 == $a2)
                        {
                            $app = $app.','.$key;
                            break;
                        }
                    }
                }


                $pri = '';
                foreach($this->lang->problem->priList as $key => $n)
                {
                    if($row[4] == $n)
                    {
                        $pri = $key;
                        break;
                    }
                }

                $dealUser = '';
                foreach($users as $key => $n)
                {
                    $n = substr($n,2);
                    if($row[7] == $n)
                    {
                        $dealUser = $key;
                        break;
                    }
                }


                $problem = new stdclass();
                $problem->abstract      = $row[0];
                $problem->source        = $source;
                $problem->severity      = $severity;
                $problem->app           = $app;
                $problem->pri           = $pri;
                $problem->occurDate     = $row[5];
                $problem->consumed      = $row[6];
                $problem->dealUser      = $dealUser;
                $problem->desc          = $row[8];

                $problemData[] = $problem;
            }
        }

        if(empty($problemData))
        {
            unlink($this->session->fileImport);
            unset($_SESSION['fileImport']);
            echo js::alert($this->lang->excel->noData);
            die(js::locate($this->createLink('problem','browse')));
        }

        $allCount = count($problemData);
        $allPager = 1;
        if($allCount > $this->config->file->maxImport)
        {
            if(empty($maxImport))
            {
                $this->view->allCount  = $allCount;
                $this->view->maxImport = $maxImport;
                die($this->display());
            }

            $allPager  = ceil($allCount / $maxImport);
            $problemData = array_slice($problemData, ($pagerID - 1) * $maxImport, $maxImport, true);
        }
        if(empty($problemData)) die(js::locate($this->createLink('problem','browse')));

        /* Judge whether the editedStories is too large and set session. */
        $countInputVars  = count($problemData) * 11;
        $showSuhosinInfo = common::judgeSuhosinSetting($countInputVars);
        if($showSuhosinInfo) $this->view->suhosinInfo = extension_loaded('suhosin') ? sprintf($this->lang->suhosinInfo, $countInputVars) : sprintf($this->lang->maxVarsInfo, $countInputVars);

        $this->view->title      = $this->lang->problem->common . $this->lang->colon . $this->lang->problem->showImport;
        $this->view->position[] = $this->lang->problem->showImport;

        $this->view->problemData = $problemData;
        $this->view->allCount        = $allCount;
        $this->view->allPager        = $allPager;
        $this->view->pagerID         = $pagerID;
        $this->view->isEndPage       = $pagerID >= $allPager;
        $this->view->maxImport       = $maxImport;
        $this->view->dataInsert      = $insert;
        $this->view->apps            = array(0 => '') + $this->loadmodel('application')->getapplicationNameCodePairs();
        $this->view->users           = $this->loadModel('user')->getPairs('noclosed');
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: createConsumed
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:53
     * Desc: This is the code comment. This method is called createConsumed.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $problemID
     * @param $before
     * @param $after
     * @param $account
     * @param $consumed
     * @param $date
     */
    private function createConsumed($problemID, $before, $after, $account, $consumed, $date)
    {
        if(!$account) return;

        $data = new stdclass();
        $data->objectID    = $problemID;
        $data->objectType  = 'problem';
        $data->before      = $before;
        $data->after       = $after;
        $data->account     = $account;
        $data->consumed    = $consumed;
        $data->createdBy   = $account;
        $data->createdDate = $date;
        $this->dao->insert(TABLE_CONSUMED)->data($data)->exec(); //问题流程加入

        //2022-4-20 更新解决时间 //20220805 废弃
       /* if($after == 'closed' || $after == 'delivery') {
            $this->dao->update(TABLE_PROBLEM)->set('solvedTime')->eq(date('Y-m-d H:i:s'))->where('id')->eq($problemID)->exec();
        }*/

        $data = new stdclass();
        $data->objectID    = $problemID;
        $data->objectType  = 'problem';
        $data->product     = ',0,';
        $data->project     = 0;
        $data->execution   = 0;
        $data->actor       = $account;
        $data->action      = $after == 'confirmed' ? 'created' : 'deal';
        $data->date        = $date;
        $this->dao->insert(TABLE_ACTION)->data($data)->exec();
    }

    /**
     * Project: chengfangjinke
     * Method: getCalculatedValue
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:53
     * Desc: This is the code comment. This method is called getCalculatedValue.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $sheet
     * @param $cell
     * @return mixed|string
     */
    private function getCalculatedValue($sheet, $cell)
    {
        $value = $sheet->getCell($cell)->getCalculatedValue();
        if(strpos($value, '_x000D') !== FALSE)
        {
            $vs = explode('_x000D', $value);
            $value = $vs[0];
        }

        return $value;
    }

    /**
     * Notes:实现方式与所属项目联动
     * Date: 2022/3/29
     * Time: 10:22
     *
     * @param $fixType
     *
     */
    public function ajaxGetSecondLine($fixType,$app = null)
    {
        $secondLineType = $fixType == 'second';
        $plans = array(''=>'') + $this->loadModel('projectplan')->getAliveProjectIDs($secondLineType);
        //$plans[0] = '';
        $where = '';
        if($fixType == 'project'){
            $where = "onchange='loadProductExecutions(this.value)'";
        }else{
            $where = "onchange='loadProductExecutions(this.value,\"$fixType\",\"$app\")'";
        }
        echo html::select('projectPlan', $plans, 0, "class='form-control' $where");
    }

    public function copy($problemID = 0)
    {
        if($_POST)
        {
            $problemID = $this->problem->create();

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $this->loadModel('action')->create('problem', $problemID, 'created', $this->post->comment);
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = inlink('browse');

            $this->send($response);
        }
        $this->view->title   = $this->lang->problem->edit;
        $this->view->executives = $this->problem->getExecutivePairs(); //$this->view->users   = $this->loadModel('user')->getPairs('noclosed,noletter');
        $this->view->apps    = $this->loadModel('application')->getapplicationNameCodePairs();
        $this->view->problem = $this->problem->getByID($problemID);
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: createFeedback
     * User: shixuyang
     * Date: 2022/5/9
     * Desc: 创建反馈单.
     */
    public function createfeedback($problemID = 0)
    {
        $problem = $this->problem->getByID($problemID);

        $flag    = $this->problem->isClickable($problem, 'createfeedback');
        if(!$flag){
            $response['result']  = 'fail';
            $response['message'] = $this->lang->problem->authStatusError;
            $response['locate']  = inlink('view', "problemID=$problemID");
            $this->send($response);
        }

        //查询有无在途的变更
        $message = $this->loadModel('problem')->delayCheck($problem);
        if(!empty($message) && $problem->ReviewStatus == 'firstpassed'){
            echo js::alert($message);
            die(js::reload('parent'));
        }

        //迭代34 UAT优化 自动获取处理人联系方式
        if(empty($problem->TeleOfIssueHandler)){
            $userInfo = $this->dao->select('*')->from(TABLE_USER)->where('account')->eq($problem->acceptUser)->fetch();
            $problem->TeleOfIssueHandler = $userInfo->mobile ?? '';
        }

        if($_POST)
        {
            if(1 == $_POST['IfultimateSolution']){
                $_POST['Tier1Feedback'] = "无";
            }
            if($_POST['SolutionFeedback'] == 5){ //非应用问题 默认内容
                /*$_POST['IfultimateSolution']           = 1;
                $_POST['standardVerify']               = 'no';
                $_POST['Tier1Feedback']                = "无";
                $_POST['reason']                       = "无";
                $_POST['ChangeSolvingTheIssue']        = "无";
                $_POST['PlannedTimeOfChange']          = date('Y-m-d H:i');
                $_POST['PlannedDateOfChangeReport']    = date('Y-m-d');
                $_POST['PlannedDateOfChange']          = date('Y-m-d');
                $_POST['CorresProduct']                = "无";
                $_POST['EditorImpactscope']            = "无";
                $_POST['revisionRecord']               = "无";*/
                $_POST['ReasonOfIssueRejecting']       = "无";
            }
            $changes = $this->problem->createfeedback($problemID);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes || $this->post->comment)
            {
                $actionID = $this->loadModel('action')->create('problem', $problemID, 'createfeedback', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
                /*$this->problem->sendmail($problemID, $actionID);*/
            }
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }
        //获取部门领导account
        if($problem->ReviewStatus == 'tofeedback'){
            $deptInfo = $this->loadModel('dept')->getByID($this->app->user->dept);
            if($deptInfo != null){
                $problem->feedbackToHandle = explode(',',$deptInfo->manager);
            }else{
                //若没有部门，就设置本身作为审批人
                $problem->feedbackToHandle = $this->app->user->account;
            }
        }
        $deptInfo = $this->loadModel('dept')->getByID($this->app->user->dept);
        $ownuser = $this->loadModel('user')->getById($this->app->user->account);
        $managerUser = array();
        if($deptInfo != null){
            $managerList = explode(',',$deptInfo->manager);
            foreach ($managerList as $manager){
                $managerValue = $this->loadModel('user')->getById($manager);
                $managerUser[$manager] = $managerValue->realname;
            }
        }else{
            //若没有部门，就设置本身作为审批人
            $managerUser[$this->app->user->account] = $ownuser->realname;
        }
        if(!empty($ownuser->partDept)){
            $partDeptArray = explode(',',$ownuser->partDept);
            foreach ($partDeptArray as $partDept){
                $deptInfo = $this->loadModel('dept')->getByID($partDept);
                if($deptInfo != null){
                    $managerList = explode(',',$deptInfo->manager);
                    foreach ($managerList as $manager){
                        $managerValue = $this->loadModel('user')->getById($manager);
                        $managerUser[$manager] = $managerValue->realname;
                    }
                }
            }
        }
        $this->view->managerUser       = $managerUser;


        $this->view->title = $this->lang->problem->createfeedback;
        $this->view->users = $this->loadModel('user')->getPairs('noclosed');
        $this->view->dept  = $this->loadModel('dept')->getByID($this->app->user->dept);
        $this->view->problem = $problem;

        // 处理金信单
        if($this->view->problem->createdBy == 'guestjx') {
            $this->view->title = $this->lang->problem->jxcreatefeedback;
            unset($this->lang->problem->solutionFeedbackList['5']);
            $this->view->problem->IfultimateSolution = '1';
            //迭代34 产创提出删除金信默认值为当前时间+2个月逻辑
            //$this->view->problem->PlannedTimeOfChange = $this->problem->setMonthAfterTime(date('Y-m-d H:i:s'),2);
        }else if($this->view->problem->PlannedTimeOfChange == '0000-00-00 00:00:00'){
            $this->view->problem->PlannedTimeOfChange = null;
        }elseif (!empty($this->view->problem->PlannedTimeOfChange)){
            $this->view->problem->PlannedTimeOfChange = date('Y-m-d H:i', strtotime($this->view->problem->PlannedTimeOfChange));
        }
        if($this->view->problem->PlannedDateOfChange == '0000-00-00'){
            $this->view->problem->PlannedDateOfChange = null;
        }
        if($this->view->problem->PlannedDateOfChangeReport == '0000-00-00'){
            $this->view->problem->PlannedDateOfChangeReport = null;
        }
        if($this->view->problem->ifReturn == null){
            $this->view->problem->ifReturn = '0';
        }
        if($this->view->problem->IfultimateSolution == null){
            $this->view->problem->IfultimateSolution = '0';
        }

        $this->display();
    }

    /**
     * User: TongYanQi
     * Date: 2022/8/24
     * ajax 获取解决方案内容单选框
     */
    public function ajaxGetIfUltimateSolutionTd($SolutionFeedback = 1)
    {
       if($SolutionFeedback == 5){
           $this->lang->problem->ifultimateSolutionList =  [1 => '是'];
        echo html::select('IfultimateSolution', $this->lang->problem->ifultimateSolutionList, 1, "onchange='ifultimateChanged(this.value)' class='form-control chosen' disabled");
        die();
        }
        echo html::select('IfultimateSolution', $this->lang->problem->ifultimateSolutionList, 0, "onchange='ifultimateChanged(this.value)' class='form-control chosen'  ");
    }

    public function approvefeedback($problemID = 0){
        $problem = $this->problem->getByID($problemID);
        $flag    = $this->problem->checkAllowReview($problem, $problem->version, $problem->reviewStage, $this->app->user->account)['result'];
        if(!$flag){
            $response['result']  = 'fail';
            $response['message'] = $this->lang->problem->authStatusError;
            $response['locate']  = inlink('view', "problemID=$problemID");
            $this->send($response);
        }
        $res = $this->problem->checkAllowReview($problem, $problem->version, $problem->reviewStage, $this->app->user->account);
        if(!$res['result']){
            $response['result']  = 'fail';
            $response['message'] = $res['message'];
            $this->send($response);
        }
        if($_POST)
        {
            $this->problem->review($problemID);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            //$this->loadModel('action')->create('problem', $problemID, 'review', $this->post->comment);

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }
        $this->view->users   = $this->loadModel('user')->getPairs('noclosed');
        $this->view->problem = $problem;
        $res = $this->problem->checkAllowReview($problem, $problem->version, $problem->reviewStage, $this->app->user->account);
        $this->view->res = $res;
        $this->display();
    }
    /**
     * 处理页面选择产品
     */
    public function ajaxGetProductZone($app)
    {
        //$this->view->productList = array('0' => '','99999'=>'无') + $this->loadModel('product')->getPairs();
        $this->view->productList = $app ? array('0' => '','99999'=>'无') + $this->loadModel('product')->getCodeNamePairsByApp($app) :array('0' => '','99999'=>'无');
        $this->view->productPlan = array('' => '','1' => '无');
        $this->display();
    }

    public function ajaxGetProblem($id){
        $problemObj  =  $this->problem->getByID($id);
        $apiUser        =  $this->dao->select('value')->from(TABLE_LANG)->where('module')->eq('problem')->andWhere('section')->eq('apiDealUserList')->fetch()->value;
        $headOffUser =  $this->dao->select('value')->from(TABLE_LANG)->where('module')->eq('problem')->andWhere('section')->eq('headOfficeApiDealUserList')->fetch()->value;
        $closeUser  =  $this->dao->select('id,`key`,value')->from(TABLE_LANG)->where('module')->eq('problem')->andWhere('section')->eq('closePersonList')->fetchAll('key');
        $jxCloseUser = isset($closeUser['jxDealAccount']->value) ? $closeUser['jxDealAccount']->value : '';
        $qzCloseUser = isset($closeUser['qzDealAccount']->value) ? $closeUser['qzDealAccount']->value : '';
        die(json_encode(array('IssueId'=>$problemObj->IssueId,'status' => $problemObj->status,'apiUser' => $apiUser,'headOffUser'=>$headOffUser,'createdBy'=>$problemObj->createdBy,'jxCloseUser' =>$jxCloseUser,'qzCloseUser' =>$qzCloseUser)));
    }

    /**
     * 同步失败重新推送
     * @param $id
     */
    public function push($id)
    {
        $problem = $this->loadModel('problem')->getByID($id);
        $flag    = $problem->ReviewStatus == 'syncfail' || $problem->ReviewStatus == 'jxsyncfail';
        if(!$flag){
            $response['result']  = 'fail';
            $response['message'] = $this->lang->problem->authStatusError;
            $response['locate']  = inlink('view', "problemID=$id");
            $this->send($response);
        }
        if($problem->createdBy == 'guestjx' && $problem->ifReturn == '1') {
            $this->loadModel('problem')->rejectJxFeedback($id, $problem);
        }elseif($problem->createdBy == 'guestjx'){
            $this->loadModel('problem')->pushJxFeedback($id, $problem);
        }else {
            $this->loadModel('problem')->pushFeedback($id, $problem);
        }
        $response['result']  = 'success';
        $response['message'] = '重新推送';
        $response['locate']  = $this->createLink('problem', 'view', array('problemID' => $id));
        $this->loadModel('action')->create('problem', $id, 'repush', "重新推送");
        die(js::locate($this->createLink('problem', 'view', "problemID=$id"), 'parent.parent'));
    }

    /**
     * 获取计划阶段
     * @param $projectID
     */
    public function getExecution($projectID){
        $this->loadModel('project');
        $defaults = array('' => '');
        if(!empty($projectID))
        {
            $executions = $this->project->getExecutionByAvailable($projectID);

            if(!empty($executions)) $defaults += $executions;
        }
        return $defaults;
    }
    //生成任务
    public function ajaxGetCreateProblemManyTask($problemID,$oldProblem){
        $data = fixer::input('post')
            ->join('belongapp', ',')
            ->join('app', ',')
            ->join('product', ',')
            ->join('productPlan', ',')
            ->join('repeatProblem',',')
            ->stripTags($this->config->problem->editor->deal['id'], $this->config->allowedTags)
            ->remove('relevantUser,workload,user,consumed,uid')
            ->get();
        $data->execution = isset($_POST['execution']) ? $this->post->execution : $this->post->executionid;
        $this->loadModel('problem')->createProblemManyTask($data,$oldProblem,$problemID);
    }

    public function ajaxIsClickable($id, $action)
    {
        $problem = $this->loadModel('problem')->getByID($id);
        $res = $this->loadModel('problem')->isClickable($problem, $action) ? 1 : 0;
        die(json_encode(['code' => 0, 'message' => 'success', 'data' => $res]));
    }

    public function sendmailByOutTime()
    {
//        $res = $this->problem->sendmailByOutTime();
        $res['inside'] = $this->problem->insideFeedback();
        $res['outside'] = $this->problem->outsideFeedback();

        a($res);
    }
    public function sendmailBySolvingOutTime()
    {
        $res = $this->problem->sendmailBySolvingOutTime();

        a($res);
    }

    public function changeBySecondLine()
    {
        $res = $this->problem->changeBySecondLineV3();

        a($res);
    }

    public function updateIsExceedByTime()
    {
//        $this->config->debug = 2; //启动报错
//        $res[] = $this->loadModel('problem')->updateifOverDateInsideNew();
        $res = $this->loadModel('problem')->updateIsExceedByTime();

        a($res);
        return $res;
    }

    /**
     * 第一次按照计划完成时间提醒
     */
    public function remindToEndMailFirst(){
        $this->problem->remindToEndMailFirst();
    }

    /**
     * 第二次按照计划完成时间提醒
     */
    public function remindToEndMailSecond(){
        $this->problem->remindToEndMailSecond();
    }
}
