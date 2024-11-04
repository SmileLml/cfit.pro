<?php
include '../../control.php';
class myApi extends api
{
    const CODE_SUCCESS   = 0; //成功
    const PARAMS_MISSING = 1001; //缺少参数
    const PARAMS_ERROR   = 1002; //参数错误
    public function problemReviewed()
    {
        $this->loadModel('problem');
        $this->app->loadLang('problem');

        /* 保存请求日志并检查请求参数。 */
        $logID = $this->loadModel('requestlog')->insideSaveRequestLog('problem' , 'review');
        $this->requestlog->judgeRequestMode($logID);

        // 金信同步处理
        $issue = 'IssueId';
        $account = 'guestcn';
        if(isset($_POST['idUnique']))
        {
            $this->checkApiToken();
            $_POST['IssueId'] = $_POST['idUnique'];
            unset($_POST['idUnique']);
            $issue = 'idUnique';
            $account = 'guestjx';
        }

        /* 判断所需字段是否存在。*/
        $data = fixer::input('post')
            ->get();

        if($_POST['ReviewResult']=='2')
        {
            $this->lang->problem->apiFeedbackItems['ReasonOfIssueRejecting']['required'] = 1;
        }
        $errMsg = $this->checkInput($issue);
        if(!empty($errMsg)) {
            $this->requestlog->response('fail', implode(',',$errMsg).'不可以空', [], $logID, self::PARAMS_MISSING);
        }

        /* 判断是否存在问题单。*/
        $problem = $this->dao->select('*')->from(TABLE_PROBLEM)->where('IssueId')->eq($_POST['IssueId'])->fetch();
        if(empty($problem))
        {
            $code = 'IssueId' == $issue ? self::CODE_SUCCESS : self::PARAMS_MISSING;
            $feedbackEmpty = sprintf($this->lang->api->feedbackEmpty, $_POST['IssueId']);
            $this->requestlog->response('fail', $issue.$feedbackEmpty, array(), $logID, $code);
        }
        //已关闭问题单不接收其他状态
        if('closed' == $problem->ReviewResult || 'closed' == $problem->status){
            $code = 'guestcn' == $problem->createdBy ? self::CODE_SUCCESS : self::PARAMS_MISSING;
            $this->requestlog->response('fail', $this->lang->api->problem->closeStatusError, array(), $logID, $code);
        }
        //金信反馈单状态为【外部已通过】，清总反馈单状态为【最终解决反馈通过】，只接收【已关闭】状态
        //金信
        if('guestjx' == $problem->createdBy && 'approvesuccess' == $problem->ReviewStatus && '3' != $data->ReviewResult){
            $this->requestlog->response('fail', $this->lang->api->problem->jxFeedbackStatusError, array(), $logID, self::PARAMS_MISSING);
        }
        //清总
        if('guestcn' == $problem->createdBy && 'finalpassed' == $problem->ReviewStatus && '3' != $data->ReviewResult){
            $this->requestlog->response('fail', $this->lang->api->problem->qzFeedbackStatusError, array(), $logID);
        }

        $node = $this->dao->select('*')->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq('problem')
            ->andWhere('objectID')->eq($problem->id)
            ->andWhere('version')->eq($problem->version)
            ->andWhere('stage')->eq('4')
            ->orderBy('stage,id')
            ->fetch();

        /* 判断评审结果是否通过。*/
        if($data->ReviewResult == '2')
        {
            //如果金信反馈单被退回，退回原因不能少于10个字
            if($account == 'guestjx' && mb_strlen($data->ReasonOfIssueRejecting) < $this->lang->problem->rejectingMinLength['rejectingMinLength']){
                $this->lang->api->rejectingShort = sprintf($this->lang->api->rejectingShort, $this->lang->problem->rejectingMinLength['rejectingMinLength']);
                $this->requestlog->response('fail', $this->lang->api->rejectingShort, [], $logID, self::PARAMS_MISSING);
            }
            /* 更新问题反馈单状态和记录操作日志。*/
            $updateData = new stdClass();
            $updateData->ReviewStatus = 'externalsendback';
            $updateData->firstPush = 0;
            $updateData->ReasonOfIssueRejecting = $data->ReasonOfIssueRejecting;
            $updateData->feedbackToHandle = '';
            if($problem->SolutionFeedback == 5 && $problem->status != 'assigned') {
                $updateData->status = 'assigned';
                $updateData->dealUser = $problem->acceptUser;
            }
            $updateData->feedbackToHandle = $problem->acceptUser;
            $updateData->approverName = $data->approverName ?? "";
            $this->dao->update(TABLE_PROBLEM)->data($updateData)->where('id')->eq($problem->id)->exec();

            $changes = common::createChanges($problem, $updateData);
            if($account == 'guestjx') {
                $actionID = $this->loadModel('action')->create('problem', $problem->id, 'syncstatus', $this->lang->api->jxsyncUpdate . ',' . $this->lang->problem->feedbackStatusList['externalsendback'],'',$account);
            }else {
                $actionID = $this->loadModel('action')->create('problem', $problem->id, 'syncstatus', $this->lang->api->syncUpdate . ',' . $this->lang->problem->feedbackStatusList['externalsendback'],'',$account);
            }
            $this->action->logHistory($actionID, $changes);
            $this->app->user->account = $account;
            //$this->loadModel('review')->check('problem', $problem->id, $problem->version, $updateData->ReviewStatus, 'sdfsdfdsfsd', $problem->reviewStage, '', false);
            $this->dao->begin();  //开启事务
            try {
                $this->dao->update(TABLE_REVIEWER)
                    ->set('status')->eq($updateData->ReviewStatus)
                    ->set('comment')->eq($updateData->ReasonOfIssueRejecting)
                    ->set('reviewTime')->eq(helper::now())
                    ->where('node')->eq($node->id)
                    ->andWhere('reviewer')->eq($account) //当前审核人
                    ->exec();

                $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq($updateData->ReviewStatus)
                    ->where('id')->eq($node->id)
                    ->exec();
            }catch (Exception $exception){
                $this->dao->rollBack();
                $this->requestlog->response('fail', $this->lang->api->networkError, [], $logID, self::PARAMS_MISSING);
            }
            $this->dao->commit();
            if(isset($updateData->status) && $updateData->status != $problem->status){
                $this->loadModel('consumed')->record('problem', $problem->id, 0, 'guestjk', $problem->status, $updateData->status, '');
            }
            $this->loadModel('consumed')->record('problem', $problem->id, 0, $account, $problem->ReviewStatus, $updateData->ReviewStatus, array(),"problemFeedBack");
            $this->requestlog->response('success', $this->lang->api->successful, array('id' => $problem->id), $logID);
        }
        else
        {
            $updateData = new stdClass();
            if($data->ReviewResult == '1')
            {
                $updateData->ReviewStatus = 'approvesuccess';
                $updateData->feedbackNum  = $problem->feedbackNum+1;
                // 5 代表非应用问题
                if($problem->SolutionFeedback == 5 && $problem->status != 'closed') {
                    $updateData->status = 'closed';
                    //$updateData->dealUser = '';
                }
                //问题处理类型是 不是问题。如果外部审通过，内部没有关闭，则需要程序处理
                //20240430 增加重复问题
                if(($problem->type == "noproblem" ||$problem->type == 'repeat')&& $problem->status != 'closed') {
                    $updateData->status = 'closed';
                    //$updateData->dealUser = '';
                }
            }elseif ($data->ReviewResult == '3')
            {
                $updateData->ReviewStatus = $updateData->status = 'closed';
                //$updateData->dealUser = '';
            }elseif ($data->ReviewResult == '4')
            {
                $updateData->ReviewStatus = 'suspend';
            }elseif ($data->ReviewResult == '5')
            {
                $updateData->ReviewStatus = 'feedbackedext';
            }elseif ($data->ReviewResult == '6'){
            $updateData->ReviewStatus = 'firstpassed';
            }elseif ($data->ReviewResult == '7') {
            $updateData->ReviewStatus = 'finalpassed';
            //问题单状态与状态流转状态不一致，经分析由于解决方式为“非应用问题”时，状态更新为待分析，现改为待关闭（迭代34）
                if($problem->SolutionFeedback == 5 && $problem->status != 'toclose') {
                    $updateData->status = 'toclose';
                    $updateData->dealUser = '';
                }
            } else{
                //未知的审批状态，后续可以将反馈单审批状态优化为后台可配置
                $updateData->ReviewStatus = $data->ReviewResult;
            }
            $updateData->firstPush = 0;
            $updateData->feedbackToHandle = '';
            if($updateData->ReviewStatus == 'closed' || $updateData->status == 'closed'){
                /** @var problemModel $problemModel*/
                $problemModel =  $this->loadModel('problem');
//                $problemModel->updateProblemSolvedTime($problem->id,helper::now());
                //外部审批关闭 内部状态同步关闭
                if($problem->status != 'closed'){
                    $problemData = new stdClass();
                    $problemData->status   = 'closed';
                    $problemData->closedBy = $account;
                    $problemData->closedDate = helper::today();
                    if(empty($problem->solvedTime)){
                        $problemData->solvedTime = helper::now();
                    }
                   $this->dao->update(TABLE_PROBLEM)->data($problemData)->where('id')->eq($problem->id)->exec();
                   //$this->loadModel('action')->create('problem', $problem->id, 'closed', '外部反馈关闭');
                    //联动关闭问题单，状态流转操作人固定为成方金科
                    //$this->loadModel('consumed')->record('problem', $problem->id, 0, 'guestjk', $problem->status, $problemData->status, '');
                }
            }
            /* 更新问题反馈单状态和记录操作日志。*/
            //$this->dao->update(TABLE_PROBLEM)->set('ReviewStatus')->eq('pass')->where('id')->eq($problem->id)->exec();
            $this->dao->update(TABLE_PROBLEM)->data($updateData)->where('id')->eq($problem->id)->exec();

            $changes = common::createChanges($problem, array('ReviewStatus' => $updateData->ReviewStatus));
            if($account == 'guestjx') {
                $actionID = $this->loadModel('action')->create('problem', $problem->id, 'syncstatus', $this->lang->api->jxsyncUpdate . ',' . $this->lang->problem->feedbackStatusList[$updateData->ReviewStatus],'',$account);
            }else {
                $actionID = $this->loadModel('action')->create('problem', $problem->id, 'syncstatus', $this->lang->api->syncUpdate . ',' . $this->lang->problem->feedbackStatusList[$updateData->ReviewStatus],'',$account);
            }
            $this->action->logHistory($actionID, $changes);
            $this->app->user->account = $account;
            //$this->loadModel('review')->check('problem', $problem->id, $problem->version, $updateData->ReviewStatus, '', $problem->reviewStage, '', false);
            $this->dao->update(TABLE_REVIEWER)
                ->set('status')->eq($updateData->ReviewStatus)
                ->set('comment')->eq('')
                ->set('reviewTime')->eq(helper::now())
                ->where('node')->eq($node->id)
                ->andWhere('reviewer')->eq($account) //当前审核人
                ->exec();

            $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq($updateData->ReviewStatus)
                ->where('id')->eq($node->id)
                ->exec();

            if(isset($updateData->status) && $updateData->status != $problem->status){
                $this->loadModel('consumed')->record('problem', $problem->id, 0, 'guestjk', $problem->status, $updateData->status, '');
            }
            $this->loadModel('consumed')->record('problem', $problem->id, 0, $account, $problem->ReviewStatus, $updateData->ReviewStatus, array(),"problemFeedBack");

            if ($issue == 'IssueId'){
                $this->requestlog->response('success', $this->lang->api->successful, array('IssueId' => $problem->IssueId), $logID);
            }else {
                $this->requestlog->response('success', $this->lang->api->successful, array('idUnique' => $problem->IssueId), $logID);
            }
        }
    }


    /**
     * 校验
     * @return array
     */
    private function checkInput($issue)
    {
        $this->loadModel('problem');
        $errMsg = [];
        foreach ($this->lang->problem->apiFeedbackItems as $k => $v)
        {
            if($v['required'] && $this->post->$k == ''){
                $errMsg[] = $k.$v['name'].$this->post->$k;
            }
            if($v['target'] != $k)
            {
                $_POST[$v['target']] = $this->post->$k;
                unset($_POST[$k]);
            }
            if($issue == 'idUnique' && in_array('IssueId外部单号',$errMsg)) {
                $errMsg[array_search('IssueId外部单号',$errMsg)] = 'idUnique外部单号';
            }
        }
        return $errMsg;
    }
}
