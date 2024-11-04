<?php
include '../../control.php';
class myProblem extends problem
{
    public function deal($problemID){
        $this->app->loadLang('api');
        $problem = $this->loadModel('problem')->getByID($problemID);
        $flag    = $this->problem->isClickable($problem, 'deal');
        if(!$flag ){
            $response['result']  = 'fail';
            $response['message'] = $this->lang->problem->authStatusError;
            $response['locate']  = inlink('view', "problemID=$problemID");
            $this->send($response);
        }
        //迭代34 UAT优化 自动获取处理人联系方式
        if(empty($problem->TeleOfIssueHandler)){
            $userInfo = $this->dao->select('*')->from(TABLE_USER)->where('account')->eq($problem->acceptUser)->fetch();
            $problem->TeleOfIssueHandler = $userInfo->mobile ?? '';
        }
        //查询有无在途的变更
        $message = $this->loadModel('problem')->closeCheckDelay($problem);
        if(!empty($message) && $problem->status == 'toclose'){
            echo js::alert($message);
            die(js::reload('parent'));
        }

        if($_POST)
        {
            $oldProblem = $problem;
            //待分析且 合并反馈单处理 问题类型为【不是问题】【重复问题】，产品
            if($oldProblem->status == 'assigned' && ($_POST['type'] == 'noproblem' || $_POST['type'] == 'repeat')) {
                $_POST['product'] = [];
                $_POST['productPlan'] = [];
            }
            if(1 == $_POST['IfultimateSolution']){
                $_POST['Tier1Feedback'] = "无";
            }
            //待分析且 清总(合并反馈单处理) 问题类型为【不是问题】
            /*if($oldProblem->status == 'assigned' && $oldProblem->createdBy == 'guestcn' && $_POST['type'] == 'noproblem') {
                $_POST['problemGrade'] = 'notSerious';
                $_POST['SolutionFeedback'] = 5;
            }*/
            //待分析且 清总或金信(合并反馈单处理)
            if(
                $oldProblem->status == 'assigned'
                && ($oldProblem->createdBy == 'guestjx' || $oldProblem->createdBy == 'guestcn')
                && $oldProblem->ReviewStatus == "tofeedback"
                && empty($oldProblem->execution)
            ) {
                if ($_POST['SolutionFeedback'] == 5) { //非应用问题 默认内容
                    /*$_POST['IfultimateSolution'] = 1;
                    $_POST['standardVerify'] = 'no';
                    $_POST['Tier1Feedback'] = "无";
                    $_POST['reason'] = "无";*/
                    $_POST['ChangeSolvingTheIssue'] = "无";
                    /*$_POST['PlannedTimeOfChange'] = date('Y-m-d H:i');
                    $_POST['PlannedDateOfChangeReport'] = date('Y-m-d');
                    $_POST['PlannedDateOfChange'] = date('Y-m-d');
                    $_POST['CorresProduct'] = "无";
                    $_POST['EditorImpactscope'] = "无";*/
                    $_POST['ReasonOfIssueRejecting'] = "无";
                    /*$_POST['product'] = [];
                    $_POST['productPlan'] = [];*/
                }

            }
            //迭代34增加退回逻辑
            if('confirmed' == $oldProblem->status && 'returned' == $_POST['status']){
                $changes = $this->dealByReturn($problem);

                if(dao::isError()) {
                    $response['result']  = 'fail';
                    $response['message'] = dao::getError();
                    $this->send($response);
                }

                $this->loadModel('consumed')->record('problem', $problem->id, 0, $this->app->user->account, $problem->status, 'returned');
                if($problem->createdBy == 'guestjx'){
                    $this->loadModel('consumed')->record('problem', $problem->id, 0, $this->app->user->account, $problem->ReviewStatus, 'approvesuccess',[],'problemFeedBack');
                    $this->loadModel('consumed')->record('problem', $problem->id, 0, $this->app->user->account, 'returned', 'closed');
                }
                $actionID = $this->loadModel('action')->create('problem', $problemID, 'deal', $this->post->comment);
                if($problem->createdBy == 'guestjx'){
                    $actionID = $this->loadModel('action')->create(
                        'problem',
                        $problem->id,
                        'syncstatus',
                        $this->lang->api->jxsyncUpdate . ',' . $this->lang->problem->feedbackStatusList['approvesuccess'],
                        '',$problem->createdBy
                    );
                }
                if($changes) $this->action->logHistory($actionID, $changes);

                $response['result']  = 'success';
                $response['message'] = $this->lang->saveSuccess;
                $response['locate']  = 'parent';

                $this->send($response);
            }

            $changes = $this->problem->deal($problemID);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $actionID = $this->loadModel('action')->create('problem', $problemID, 'deal', $this->post->comment);
            if(
                $oldProblem->status == 'assigned'
                && ($oldProblem->createdBy == 'guestjx' || $oldProblem->createdBy == 'guestcn')
                && empty($oldProblem->execution)
                && $oldProblem->ReviewStatus == "tofeedback"
            ){

                $this->loadModel('action')->create('problem', $problemID, 'createfeedback', $this->post->comment);
            }
            if($changes) $this->action->logHistory($actionID, $changes);
            //$this->problem->sendmail($problemID, $actionID);

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            //$this->send($response);
            die(print(json_encode($response)).$this->fetch('problem','ajaxGetCreateProblemManyTask',array('problemID'=>$problemID,'oldProblem'=>$oldProblem)));
        }

        if(!in_array($problem->status, $this->problem::$_dealStatus)){
            die('当前状态不能处理');
        }
        $statusList = array('' => '');
        switch($problem->status)
        {
            case 'confirmed':
                $statusList['assigned'] = $this->lang->problem->statusList['assigned'];
                break;
            case 'assigned':
                $statusList['feedbacked'] = $this->lang->problem->statusList['feedbacked'];
                break;
            case 'toclose':
                $statusList['closed'] = $this->lang->problem->statusList['closed'];
                break;
        }
        /* Get executions. */
        $executions = array('' => '');
        $this->view->depts      = $this->loadModel('dept')->getOptionMenu();
        $this->view->title      = $this->lang->problem->edit;
        $this->view->users      = $this->loadModel('user')->getPairs('noclosed');
        if($problem->feedbackExpireTime == ""){
            $problem->feedbackExpireTime = $this->problem->getDateAfter($this->lang->problem->expireDaysList['days'],true);
        }
        //获取部门领导account
        $deptInfo = $this->loadModel('dept')->getByID($this->app->user->dept);
        if($deptInfo != null){
            $problem->feedbackToHandle = explode(',',$deptInfo->manager);
        }else{
            //若没有部门，就设置本身作为审批人
            $problem->feedbackToHandle = $this->app->user->account;
        }
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

        $this->view->problem    = $problem;
        $this->view->statusList = $statusList;
        $this->view->status = $problem->status != 'toclose' ? '' : 'closed';
        //根据项目实现和二线实现，默认获取对应所属项目list
        $this->view->plans      = array('0' => '') + $this->loadModel('projectplan')->getAliveProjectIDs($problem->fixType == 'second');
       // $this->view->plans      = array('0' => '') + $this->loadModel('projectplan')->getPairs();
        $this->view->appAll = $this->loadModel('application')->getapplicationNameCodePairsWithisPayment();
        $this->view->apps = array('' => '') + array_column($this->view->appAll, 'name', 'id');
         $this->view->products   = array('0' => '') ;
        if($problem->product){
            $this->view->productplan      = array('0' => '') + $this->loadModel('productplan')->getPairs($problem->product);
        }else{
            $this->view->productplan = array('0' => '','1' =>'无');
        }
        $this->view->executions       = $executions;
        $this->view->productList = $problem->app ? array('0' => '','99999'=>'无') + $this->loadModel('product')->getCodeNamePairsByApp($problem->app) :array('0' => '','99999'=>'无');
        //$this->view->productList = array('0' => '','99999'=>'无') + $this->loadModel('product')->getPairs();

        //反复分析获取产品和版本
        $this->view->details = $this->problem->getProductAndPlan($problem->product,$problem->productPlan);
        $this->view->repeatProblem = array('' => '') + $this->problem->getAllCode($problemID);

        //待分析且 清总或金信(合并反馈单处理)
        if($problem->status == 'assigned' && ($problem->createdBy == 'guestjx' || $problem->createdBy == 'guestcn')){
            // 处理金信单
            if($this->view->problem->createdBy == 'guestjx') {
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
        }

        if('confirmed' == $problem->status){
            $this->display('problem', 'confirmed');
        }elseif('guestcn' == $problem->createdBy && 'tofeedback' == $problem->ReviewStatus){
            $this->display('problem', 'dealfeedback');
        }else{
            $this->display();
        }
    }

    /**
     * 二线退回问题单
     * @param $oldProblem
     * @return array|string
     */
    public function dealByReturn($oldProblem)
    {
        $data = fixer::input('post')->remove('app,application,user,uid ,comment')->get();
        if(empty($data->ReasonOfIssueRejecting)){
            return dao::$errors['ReasonOfIssueRejecting'] = sprintf($this->lang->problem->emptyObject, $this->lang->problem->ReasonOfIssueRejecting);
        }

        $data->lastDealDate = helper::now();
        $data->dealUser     = $oldProblem->createdBy != 'guestjx' ? $oldProblem->createdBy : '';
        $data->ifReturn = '1'; //是否受理问题 不受理
        if($oldProblem->createdBy == 'guestjx'){
            $this->syncJXByReturn($oldProblem, $data);
        }else{
            $this->dao->update(TABLE_PROBLEM)->data($data)->autoCheck()
                ->batchCheck($this->config->problem->deal->requiredFields, 'notempty')
                ->where('id')->eq($oldProblem->id)
                ->exec();
        }

        return common::createChanges($oldProblem, $data);
    }

    /**
     * 问题单退回同步金信
     * @param $problem
     * @param $data
     * @return string|true
     */
    public function syncJXByReturn($problem, $data)
    {
        $pushEnable = $this->config->global->jxProblemFeedbackEnable;
        if ($pushEnable == 'enable') {
            $url           = $this->config->global->jxProblemRejectFeedbackUrl;
            $pushAppId     = $this->config->global->jxProblemFeedbackAppId;
            $pushAppSecret = $this->config->global->jxProblemFeedbackAppSecret;
            $uuid          = $this->problem->create_guid();
            $ts            = time();
            $sign          = md5('appId='.$pushAppId.'&nonce='.$uuid.'&ts='.$ts.'&appSecret='.$pushAppSecret);
            $headers       = [
                'appId: ' . $pushAppId,
                'appSecret: ' . $pushAppSecret,
                'ts: ' . $ts,
                'nonce: ' . $uuid,
                'sign: ' . $sign
            ];
            $pushData['data'] = [
                'idUnique' => $problem->IssueId,
                'approvalOpinion' => $data->ReasonOfIssueRejecting,
                'id' => $problem->extId,
                'isAfterSubmit' => $problem->isAfterSubmit,
            ];

            $result = $this->loadModel('requestlog')->http($url, $pushData, 'PATCH', 'json', array(), $headers);

            if(!empty($result))
            {
                $resultData = json_decode($result);
                if($resultData->code == '0') {
                    $data->status = 'closed';
                    $data->ReviewStatus = 'approvesuccess';
                    $data->closedBy = 'guestjk';
                    $data->feedbackEndTimeInside = $data->innovationPassTime = $data->closedDate = $data->solvedTime = helper::now();
                    $status = 'success';
                    $this->dao->update(TABLE_PROBLEM)->data($data)->autoCheck()
                        ->where('id')->eq($problem->id)
                        ->exec();
                } else {
                    return dao::$errors[] = !empty($resultData->error) ? $resultData->error : $resultData->data;
                }
            } else {
                return dao::$errors[] = '网络不通';
            }

            $this->loadModel('requestlog')->saveRequestLog($url, 'problem', 'feedback', 'POST', $pushData, $result, $status, '');
        }

        return true;
    }
}