<?php
class productionchangeModel extends model
{

    /**
     * Method: getList
     * @param $browseType
     * @param $queryID
     * @param $orderBy
     * @param null $pager
     * @return mixed
     */
    public function getList($browseType, $queryID, $orderBy, $pager = null)
    {
        $preproductionQuery = '';
        if($browseType == 'bysearch')
        {
            $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : '';
            if($query)
            {
                $this->session->set('productionchangeQuery', $query->sql);
                $this->session->set('productionchangeForm', $query->form);
            }

            if($this->session->productionchangeQuery == false) $this->session->set('productionchangeQuery', ' 1 = 1');
            $preproductionQuery = $this->session->productionchangeQuery;

        }
        $dealUserQuery = '';
        if($browseType == 'my'){
            $dealUserQuery = "FIND_IN_SET('".$this->app->user->account."',dealUser)";
        }

        $info = $this->dao->select('*')->from(TABLE_PRODUCTIONCHANGE)
            ->where('deleted')->eq('0')
            ->beginIF($browseType == 'my')->andWhere($dealUserQuery)->fi()
            ->beginIF($browseType != 'all' and $browseType != 'my' and $browseType != 'bysearch')->andWhere('status')->eq($browseType)->fi()
            ->beginIF($browseType == 'bysearch')->andWhere($preproductionQuery)->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');
//        foreach ($info as $key => $value)
//        {
//            //	待实施反馈+复核确认 待处理是实施人和复核人的合集
//            if($value->status == 'feedbackAndRepeatConfirm')
//            {
//                $executanter = explode(',',$value->executanter);
//                $reviewPerson = explode(',',$value->reviewPerson);
//                $dealUserArr = array_unique(array_filter(array_merge($executanter,$reviewPerson)));
//
//                $info[$key]->dealUser = implode(',',$dealUserArr);
//            }
//        }
        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'productionchange', $browseType != 'bysearch');

        return $info;
    }

    /**
     * @Notes:构造筛选条件
     * @Date: 2024/4/24
     * @Time: 16:05
     * @Interface buildSearchForm
     * @param $queryID
     * @param $actionURL
     */
    public function buildSearchForm($queryID, $actionURL)
    {
        $this->config->productionchange->search['actionURL'] = $actionURL;
        $this->config->productionchange->search['queryID']   = $queryID;
        $this->config->productionchange->search['params']['applicant']['values']        = array('' => '') +$this->loadModel('user')->getPairs('noletter|noclosed');
        $this->config->productionchange->search['params']['applicantDept']['values']    = array('' => '') +$this->loadModel('dept')->getOptionMenu();
        $this->config->productionchange->search['params']['dealUser']['values']         = array('' => '') +$this->loadModel('user')->getPairs('noletter|noclosed');
        $this->config->productionchange->search['params']['createdBy']['values']        = array('' => '') +$this->loadModel('user')->getPairs('noletter|noclosed');
        $this->config->productionchange->search['params']['application']['values']      = array('' => '') +$this->loadModel('application')->getPairs();
        $this->config->productionchange->search['params']['correlationDemand']['values']  = array('' => '') +$this->loadModel('demandinside')->getPairs('noclosed');
        $this->config->productionchange->search['params']['correlationProblem']['values'] = array('' => '') +$this->loadModel('problem')->getPairs('noclosed');
        $this->config->productionchange->search['params']['correlationSecondorder']['values'] = array('' => '') +$this->loadModel('secondorder')->getNamePairsAll();
        $this->config->productionchange->search['params']['deptConfirmPerson']['values']      = array('' => '') +$this->loadModel('user')->getPairs('noletter|noclosed');
        $this->config->productionchange->search['params']['interfacePerson']['values']        = array('' => '') +$this->loadModel('user')->getPairs('noletter|noclosed');
        $this->config->productionchange->search['params']['operationPerson']['values']        = array('' => '') +$this->loadModel('user')->getPairs('noletter|noclosed');

        $this->loadModel('search')->setSearchParams($this->config->productionchange->search);
    }

    /**
     * @Notes:创建
     * @Date: 2024/4/24
     * @Time: 16:05
     * @Interface create
     */
    public function create()
    {
        $account = $this->app->user->account;
        $data = fixer::input('post')
            ->remove('uid,comment,files')
            ->add('status', 'wait')
            ->add('createdBy', $account)
            ->add('dealUser', $account)
            ->add('version', 1)
            ->join('application', ',')
            ->join('correlationPublish', ',')
            ->join('space', ',')
            ->join('releaseRecord', ',')
            ->join('correlationDemand', ',')
            ->join('correlationProblem', ',')
            ->join('correlationSecondorder', ',')
            ->join('deptConfirmPerson', ',')
            ->join('interfacePerson', ',')
            ->join('operationPerson', ',')
            ->join('mailto', ',')
            ->join('defaultMailto', ',')
            ->stripTags($this->config->productionchange->editor->create['id'], $this->config->allowedTags)
            ->get();

        /*if(!$this->loadModel('common')->checkJkDateTime($data->onlineStart))
        {
            dao::$errors['onlineStart'] =  sprintf($this->lang->productionchange->emptyObject, $this->lang->productionchange->onlineStart);
            return;
        }

        if(!$this->loadModel('common')->checkJkDateTime($data->onlineEnd))
        {
            dao::$errors['onlineEnd'] =  sprintf($this->lang->productionchange->emptyObject, $this->lang->productionchange->onlineEnd);
            return;
        }*/

        /*
         *上线计划实施时间开始时间限制：
         *若选择上线申请类型为投产，则开始时间最早时间不能早于发起时间15个工作日。即3.1发起投产申请，开始时间最早为15个工作日之后（含）。（投产暂时不加该限制）
         *计划变更：提前3个工作日（含）
         *紧急变更：无限制
        */
        /*if($data->onlineType == 1) { //投产
            $wordDayTime =  strtotime(helper::getWorkDay(date('Y-m-d'),15).' 23:59:59');
            if(strtotime($data->onlineStart) < $wordDayTime)
            {
                dao::$errors['onlineStart'] =  $this->lang->productionchange->lessFifteenTip;
                return;
            }
        }elseif($data->onlineType == 2){ //计划变更
            $wordDayTime =  strtotime(helper::getWorkDay(date('Y-m-d'),2).' 23:59:59');
            if(strtotime($data->onlineStart) < $wordDayTime)
            {
                dao::$errors['onlineStart'] =  $this->lang->productionchange->lessThreeTip;
                return;
            }
        }
        //上线计划实施结束时间校验
        if(strtotime($data->onlineStart) > strtotime($data->onlineEnd))
        {
            dao::$errors[''] =  $this->lang->productionchange->onlineTimeTip;
            return;
        }
        //是否影响管理系统校验
        if($data->ifEffectSystem == 1 && empty($data->effectSystemExplain))
        {
            dao::$errors['effectSystemExplain'] = sprintf($this->lang->productionchange->emptyObject, $this->lang->productionchange->effectSystemExplain);
            return;
        }
        //需求条目、问题、工单最少关联一种
        if(empty($data->correlationDemand) && empty($data->correlationProblem) && empty($data->correlationSecondorder))
        {
            dao::$errors[''] = $this->lang->productionchange->moreOne;
            return;
        }
        //部门确认责任人校验
        if($data->ifReport == 1 && empty($data->deptConfirmPerson))
        {
            dao::$errors['deptConfirmPerson'] =  sprintf($this->lang->productionchange->emptyObject, $this->lang->productionchange->deptConfirmPerson);
            return;
        }*/

        //处理影响关联系统说明、部门确认责任人
        if($data->ifEffectSystem == 2) $data->effectSystemExplain = '';
        if($data->ifReport == 2) $data->deptConfirmPerson = '';

        //校验附件
        /*if(is_array($this->post->files) && count($this->post->files)){
            return dao::$errors = array('files' => $this->lang->productionchange->filesEmpty);
        }*/

        $this->dao->insert(TABLE_PRODUCTIONCHANGE)->data($data)->autoCheck()
            //->batchCheck($this->config->productionchange->create->requiredFields, 'notempty')
            ->exec();
        $preproductionID = $this->dao->lastInsertId();
        if(!dao::isError())
        {
            //构造code
            $date   = date('Y-m-d');
            $number = $this->dao->select('count(id) c')->from(TABLE_PRODUCTIONCHANGE)->where('createdDate')->gt($date)->fetch('c');
            $code   = 'CFIT-iTB-' . date('Ymd-') . sprintf('%02d', $number);

            $this->dao->update(TABLE_PRODUCTIONCHANGE)->set('code')->eq($code)->where('id')->eq($preproductionID)->exec();
            $this->loadModel('consumed')->record('productionchange', $preproductionID, 0, $account, '', $data->status, array());

            //处理附件
            $this->loadModel('file')->updateObjectID($this->post->uid, $preproductionID, 'productionchange');
            $this->file->saveUpload('productionchange', $preproductionID);
        }
        return $preproductionID;

    }

    /**
     * @Notes:更新
     * @Date: 2024/4/25
     * @Time: 9:22
     * @Interface update
     * @param $preproductionID
     */
    public function update($preproductionID)
    {
        $originalData = $this->getByID($preproductionID);
        $data = fixer::input('post')
            ->remove('uid,comment,files')
            ->join('application', ',')
            ->join('correlationPublish', ',')
            ->join('space', ',')
            ->join('releaseRecord', ',')
            ->join('correlationDemand', ',')
            ->join('correlationProblem', ',')
            ->join('correlationSecondorder', ',')
            ->join('deptConfirmPerson', ',')
            ->join('interfacePerson', ',')
            ->join('operationPerson', ',')
            ->join('mailto', ',')
            ->setIF($this->post->correlationDemand == '', 'correlationDemand', '')
            ->setIF($this->post->correlationProblem == '', 'correlationProblem', '')
            ->setIF($this->post->correlationSecondorder == '', 'correlationSecondorder', '')
            ->stripTags($this->config->productionchange->editor->create['id'], $this->config->allowedTags)
            ->get();
        /*if(!$this->loadModel('common')->checkJkDateTime($data->onlineStart))
        {
            dao::$errors['onlineStart'] =  sprintf($this->lang->productionchange->emptyObject, $this->lang->productionchange->onlineStart);
            return;
        }

        if(!$this->loadModel('common')->checkJkDateTime($data->onlineEnd))
        {
            dao::$errors['onlineEnd'] =  sprintf($this->lang->productionchange->emptyObject, $this->lang->productionchange->onlineEnd);
            return;
        }

        //是否影响管理系统校验
        if($data->ifEffectSystem == 1 && empty($data->effectSystemExplain))
        {
            dao::$errors['effectSystemExplain'] = sprintf($this->lang->productionchange->emptyObject, $this->lang->productionchange->effectSystemExplain);
            return;
        }*/
        //部门确认责任人校验
        if(isset($data->ifReport))
        {
//            if($data->ifReport == 1 && empty($data->deptConfirmPerson))
//            {
//                dao::$errors['deptConfirmPerson'] =  sprintf($this->lang->productionchange->emptyObject, $this->lang->productionchange->deptConfirmPerson);
//                return;
//            }
            //部门确认责任人
            if($data->ifReport == 2) $data->deptConfirmPerson = '';
        }

        /*
         *上线计划实施时间开始时间限制：
         *若选择上线申请类型为投产，则开始时间最早时间不能早于发起时间15个工作日。即3.1发起投产申请，开始时间最早为15个工作日之后（含）。（投产暂时不加该限制）
         *计划变更：提前3个工作日（含）
         *紧急变更：无限制
        */
        /*if($data->onlineType == 1) { //投产
            $wordDayTime =  strtotime(helper::getWorkDay(date('Y-m-d'),15).' 23:59:59');
            if(strtotime($data->onlineStart) < $wordDayTime)
            {
                dao::$errors['onlineStart'] =  $this->lang->productionchange->lessFifteenTip;
                return;
            }
        }elseif($data->onlineType == 2){ //计划变更
            $wordDayTime =  strtotime(helper::getWorkDay(date('Y-m-d'),2).' 23:59:59');
            if(strtotime($data->onlineStart) < $wordDayTime)
            {
                dao::$errors['onlineStart'] =  $this->lang->productionchange->lessThreeTip;
                return;
            }
        }

        if(strtotime($data->onlineStart) > strtotime($data->onlineEnd))
        {
            dao::$errors[''] =  $this->lang->productionchange->onlineTimeTip;
            return;
        }
        //需求条目、问题、工单最少关联一种
        if(empty($data->correlationDemand) && empty($data->correlationProblem) && empty($data->correlationSecondorder))
        {
            dao::$errors[''] = $this->lang->productionchange->moreOne;
            return;
        }*/
        //处理影响关联系统说明
        if($data->ifEffectSystem == 2) $data->effectSystemExplain = '';
        //校验附件
//        if(is_array($this->post->files) && count($this->post->files)){
//            return dao::$errors = array('files' => $this->lang->productionchange->filesEmpty);
//        }
        $data = $this->loadModel('file')->processImgURL($data, $this->config->productionchange->editor->edit['id'], $this->post->uid);
        $this->dao->update(TABLE_PRODUCTIONCHANGE)->data($data)
            //->batchCheck($this->config->productionchange->edit->requiredFields, 'notempty')
            ->where('id')->eq($preproductionID)
            ->exec();

        $this->loadModel('file')->updateObjectID($this->post->uid, $preproductionID, 'productionchange');
        $this->file->saveUpload('productionchange', $preproductionID);

        return common::createChanges($originalData, $data);
    }

    /**
     * @Notes:提交审批
     * @Date: 2024/4/29
     * @Time: 10:56
     * @Interface deal
     * @param $preproductionID
     */
    public function deal($preproductionID)
    {
        /**
         * @var iwfpModel $iwfpModel
         */
        $iwfpModel = $this->loadModel('iwfp');
        $preproductionInfo = $this->getByID($preproductionID);

//        //校验是否已经提交
//        $iwfpIfHad = $this->checkSubmit($preproductionInfo);
//        if($iwfpIfHad)
//        {
//            dao::$errors[''] =  $this->lang->productionchange->hadSubmit;
//            return;
//        }

        $account = $this->app->user->account;
        $data = fixer::input('post')
            ->remove('uid')
            ->join('mailto',',')
            ->stripTags($this->config->productionchange->editor->create['id'], $this->config->allowedTags)
            ->get();
        $data = $this->loadModel('file')->processImgURL($data, $this->config->productionchange->editor->edit['id'], $this->post->uid);
        $allReviewerInfo = $this->getAllReviewerInfo($preproductionInfo);

        $version = $preproductionInfo->version;
        if($preproductionInfo->returnTimes == $preproductionInfo->version)
        {
            $version = $preproductionInfo->version + 1;
        }

        //提交审批
        $res = $iwfpModel->startWorkFlow_V2('productionchange', $preproductionInfo->id, $preproductionInfo->code, $preproductionInfo->createdBy, $allReviewerInfo, $version, $this->lang->productionchange->reviewNodeCodeNameList, $preproductionInfo->processInstanceId);

        if($res)
        {
            //更新主表数据
            $updateParams = new stdClass();
            $processInstanceId = $res->processInstanceId;//流程审批ID
            $userVariableList = new stdClass();

            //创建时如果选择上报，则下一节点建设方部门审核，否则到业务方接口人审核
            if ($preproductionInfo->ifReport == 1)
            {
                $nextDealUser = $preproductionInfo->deptConfirmPerson;
                $nextStatus = 'constructionDeptConfirm';
                $userVariableList->toConDept = 1;
            }else{
                $nextDealUser = $preproductionInfo->interfacePerson;
                $nextStatus = 'vocalInterfacePerson';
                $userVariableList->toConDept = 2;
            }

            $firstNode = $iwfpModel->completeTaskWithClaim_V2($processInstanceId, $account, $data->comment,1, $userVariableList, $version);
            if(dao::isError()) {
                return $firstNode;
            }
            //更新表已经提交
            $updateParams = new stdClass();
            $nextStatus = $firstNode->toXmlTask;
            $nextUsers  = is_array($firstNode->dealUser) ? implode(',', $firstNode->dealUser):$firstNode->dealUser;
            $updateParams->status   = $nextStatus;
            $updateParams->dealUser = $nextUsers;
            $updateParams->version  = $version;
            $updateParams->mailto   = $data->mailto;
            $updateParams->processInstanceId = $processInstanceId;
            $this->dao->update(TABLE_PRODUCTIONCHANGE)->data($updateParams)->where('id')->eq($preproductionID)->exec();

            if(dao::isError()) {
                return dao::getError();
            }

            //添加状态流转
            $this->loadModel('consumed')->record('productionchange', $preproductionID, '0', $account, $preproductionInfo->status, $nextStatus);

        }

        return $res;

    }

    /**
     * @Notes:节点审批
     * @Date: 2024/5/6
     * @Time: 15:48
     * @Interface review
     * @param $preproductionInfo
     */
    public function review($preproductionInfo)
    {
        /**
         * @var iwfpModel $iwfpModel
         */
        $preproductionID = $preproductionInfo->id;
        $iwfpModel = $this->loadModel('iwfp');

        $account = $this->app->user->account;
        $data = fixer::input('post')
            ->remove('uid')
            ->join('mailto',',')
            ->join('defaultMailto', ',')
            ->stripTags($this->config->productionchange->editor->review['id'], $this->config->allowedTags)
            ->get();
        //校验必填
        //复核人员做必填校验，验证人员不做校验
        if($preproductionInfo->personType == 2)
        {
            if(empty($data->result))
            {
                dao::$errors['result'] =  sprintf($this->lang->productionchange->emptyObject, $this->lang->productionchange->dealResult);
                return;
            }
        }

        //不通过 处理意见必填
        if(isset($data->result) && $data->result == 2 && isset($data->comment) && empty($data->comment))
        {
            dao::$errors['comment'] =  sprintf($this->lang->productionchange->emptyObject, $this->lang->productionchange->dealComment);
            return;
        }
        $data = $this->loadModel('file')->processImgURL($data, $this->config->productionchange->editor->edit['id'], $this->post->uid);
        $userVariableList = new stdClass();
        $res = false;
        switch ($preproductionInfo->status)
        {
            case 'vocalInterfacePerson':
                if($data->result == 6) //选择上报,重新提交所有节点人处理
                {
                    $updatePer = new stdClass();
                    $updatePer->interfaceDeptPerson   = implode(',',$data->vocalDeptPerson);
                    $this->dao->update(TABLE_PRODUCTIONCHANGE)->data($updatePer)->where('id')->eq($preproductionID)->exec();

                    $toDealUserList = array_merge(array_filter($data->vocalDeptPerson));
                    $res = $iwfpModel->completeTaskWithClaim_V2($preproductionInfo->processInstanceId, $account, $data->comment, 6, $userVariableList, $preproductionInfo->version, $toDealUserList);
                }else{
                    $res = $iwfpModel->completeTaskWithClaim_V2($preproductionInfo->processInstanceId, $account, $data->comment, $data->result, $userVariableList, $preproductionInfo->version);
                    //不通过更新版本
                    if($data->result == 2)  $res->returnTimes = $preproductionInfo->returnTimes + 1;
                }
                break;
            case 'implementInterfacePerson':
                if($data->result == 1) //复核人员处理 流转节点
                {
                    //复核人员为空提醒
                    $reviewPerson = implode(',',$data->reviewPerson);
                    if(empty($reviewPerson))
                    {
                        dao::$errors['reviewPerson'] =  sprintf($this->lang->productionchange->emptyObject, $this->lang->productionchange->reviewPerson);
                        return;
                    }
                    $updatePer = new stdClass();
                    $updatePer->executanter     = empty($data->executanter) ? implode(',',$data->executanter) : '';
                    $updatePer->reviewPerson    = $reviewPerson;
                    $this->dao->update(TABLE_PRODUCTIONCHANGE)->data($updatePer)->where('id')->eq($preproductionID)->exec();
                    $toDealUserList = array_merge(array_filter($data->reviewPerson));
                    $res = $iwfpModel->completeTaskWithClaim_V2($preproductionInfo->processInstanceId, $account, $data->comment, $data->result, $userVariableList, $preproductionInfo->version, $toDealUserList);
                }else if($data->result == 2){ //不通过重新发起
                    $res = $iwfpModel->completeTaskWithClaim_V2($preproductionInfo->processInstanceId, $account, $data->comment, $data->result, $userVariableList, $preproductionInfo->version);
                    //不通过更新版本
                    $res->returnTimes = $preproductionInfo->returnTimes + 1;
                }else{ //不通过（跳过业务审核）
                    if(empty($data->comment))
                    {
                        dao::$errors['comment'] =  sprintf($this->lang->productionchange->emptyObject, $this->lang->productionchange->dealComment);
                        return;
                    }
                    $res = $iwfpModel->completeTaskWithClaim_V2($preproductionInfo->processInstanceId, $account, $data->comment, $data->result, $userVariableList, $preproductionInfo->version);
                    $res->toXmlTask = 'feedback';
                    //不通过更新版本
                    $res->returnTimes = $preproductionInfo->returnTimes + 1;
                }
            break;
            case 'feedbackAndRepeatConfirm':
                //实施人员审核 只保存数据，不流转节点
                $updateInfo = new stdClass();
                if($preproductionInfo->personType == 2)
                {
                    if($data->result == 6) //复核人员 上报
                    {
                        $updateInfo->operationDept          = implode(',',$data->operationDept);
                        $toDealUserList = array_merge(array_filter($data->operationDept));
                        //上报后 为空项校验
                        if(empty($toDealUserList))
                        {
                            dao::$errors['operationDept'] =  sprintf($this->lang->productionchange->emptyObject, $this->lang->productionchange->operationDept);
                            return;
                        }
                        //实际上线时间
                        if(!$this->loadModel('common')->checkJkDateTime($data->actualOnlineTime))
                        {
                            dao::$errors['actualOnlineTime'] =  sprintf($this->lang->productionchange->emptyObject, $this->lang->productionchange->actualOnlineTime);
                            return;
                        }
                        $res = $iwfpModel->completeTaskWithClaim_V2($preproductionInfo->processInstanceId, $account, $data->remark, $data->result, $userVariableList, $preproductionInfo->version, $toDealUserList);
                    }else if($data->result == 1){ //复核人员选择上线成功
                        $res = $iwfpModel->completeTaskWithClaim_V2($preproductionInfo->processInstanceId, $account, $data->remark, $data->result, $userVariableList, $preproductionInfo->version);
                    }else{ //上线失败
                        $res = $iwfpModel->completeTaskWithClaim_V2($preproductionInfo->processInstanceId, $account, $data->remark, $data->result, $userVariableList, $preproductionInfo->version);
                        if($res->isEnd ==1){
                            $res->toXmlTask = 'onlinefail';
                        }
                    }
                }else{
                    $updateInfo->operationDept = implode(',',$data->operationDept);
                }

                $updateInfo->record = $data->record;
                $updateInfo->remark = $data->remark;
                $updateInfo->actualOnlineTime = $data->actualOnlineTime;
                $updateInfo->mailto = $data->mailto;
                $updateInfo->defaultMailto = $data->remark;
                $this->dao->update(TABLE_PRODUCTIONCHANGE)->data($updateInfo)->where('id')->eq($preproductionID)->exec();

            break;
            case 'waitValidate': //待验证
                $res = $iwfpModel->completeTaskWithClaim_V2($preproductionInfo->processInstanceId, $account, $data->comment, $data->result, $userVariableList, $preproductionInfo->version);
                if($data->result == 1){
                    if($res->isEnd ==1) $res->toXmlTask = 'validateSuccess';
                }else{
                    //不通过更新版本
                    $res->returnTimes = $preproductionInfo->returnTimes + 1;
                }
            break;
            default:
                $res = $iwfpModel->completeTaskWithClaim_V2($preproductionInfo->processInstanceId, $account, $data->comment, $data->result, $userVariableList, $preproductionInfo->version);
                if($data->result == 2) $res->returnTimes = $preproductionInfo->returnTimes + 1;
                break;
        }

        if(dao::isError()){
            return false;
        }

        if($res){
            $updateData = array();
            //增加变更次数，重新发起增加版本
            if(isset($res->returnTimes) && !empty($res->returnTimes))
            {
                $updateData['returnTimes'] = $res->returnTimes;
            }
            //处理退回时的admin问题
            $dealUser = $res->dealUser;
            if($data->result == 2 && in_array('admin',$dealUser))
            {
                foreach ($dealUser as $key => $value)
                {
                    if($value == 'admin') unset($dealUser[$key]);
                }
            }

            //实施人员和复核人员处理
            if($preproductionInfo->status == 'implementInterfacePerson' && $data->result == 1)
            {
                $updateData['status'] = $res->toXmlTask;
                $dealUser = array_merge(array_unique(array_filter(array_merge($data->executanter,$data->reviewPerson))));
            }
            $updateData['mailto'] = $data->mailto;
            $updateData['status'] = $res->toXmlTask;
            $updateData['dealUser'] = implode(',', $dealUser);
            $this->dao->update(TABLE_PRODUCTIONCHANGE)->data($updateData)->where('id')->eq($preproductionID)->exec();
            //如果投产/变更单验证成功联动需求条目状态
            if('validateSuccess' == $res->toXmlTask){
                $this->loadModel('demandinside')->collectionStatus($preproductionID);
            }
            //添加状态流转
            $this->loadModel('consumed')->record('productionchange', $preproductionID, '0', $account, $preproductionInfo->status, $res->toXmlTask);
            return $res;
        }

        return true;

    }

    /**
     * @Notes:上传附件
     * @Date: 2024/5/11
     * @Time: 9:24
     * @Interface uploadFiles
     * @param $preproductionID
     */
    function uploadFile($preproductionID)
    {
        //校验附件
        if(is_array($this->post->files) && count($this->post->files)){
            return dao::$errors = array('files' => $this->lang->productionchange->filesEmpty);
        }
        //处理附件
        $this->loadModel('file')->updateObjectID($this->post->uid, $preproductionID, 'productionchange');
        $this->file->saveUpload('productionchange', $preproductionID);

        return true;
    }

    /**
     * Project: 权限
     * Method: isClickable
     * Product: PhpStorm
     * @param $preproduction
     * @param $action
     * @return bool
     */
    public static function isClickable($preproduction, $action)
    {
        global $app;
        $action = strtolower($action);
        $account = $app->user->account;
        if($action == 'edit')    return $preproduction->createdBy == $account and in_array($preproduction->status,array('wait','feedback'));
        //如果要修改权限，需要同步修改列表页和详情页判断逻辑
        if($action == 'deal')    return in_array($preproduction->status,array('wait','feedback'))  and strstr($preproduction->dealUser, $account) !== false;
        if($action == 'review')  return !in_array($preproduction->status,array('wait','feedback')) and strstr($preproduction->dealUser, $account) !== false;

        return true;
    }

    /**
     * @Notes:sendmail
     * @Date: 2024/5/11
     * @Time: 18:13
     * @Interface sendmail
     * @param $preproductionID
     * @param $actionID
     */
    public function sendmail($preproductionID, $actionID)
    {
        /* 加载mail模块用于发信通知，获取需求意向和人员信息。*/
        $this->loadModel('mail');
        $preproduction = $this->getById($preproductionID);
        $action          = $this->loadModel('action')->getById($actionID);
        $users = $this->loadModel('user')->getPairs('noletter');

        /* 获取后台通知中配置的邮件发信。*/
        $this->app->loadLang('custommail');
        $mailConf   = isset($this->config->global->setProductionchangeMail) ? $this->config->global->setProductionchangeMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf   = json_decode($mailConf);
        $browseType = 'productionchange';
        $mailTitle = vsprintf($mailConf->mailTitle, $mailConf->variables);

        $oldcwd     = getcwd();
        $modulePath = $this->app->getModulePath($appName = '', 'productionchange');
        $viewFile   = $modulePath . 'view/sendmail.html.php';
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

        /* 获取发信人和抄送人数据。*/
        $sendUsers = $this->getToAndCcList($preproduction);
        if (!$sendUsers) return;
        list($toList, $ccList) = $sendUsers;
        $subject = $mailTitle;


        /* Send mail. */
        /* 调用mail模块的send方法进行发信。*/
        $this->mail->send($toList, $subject, $mailContent, $ccList);
        if ($this->mail->isError()) trigger_error(join("\n", $this->mail->getError()));
    }

    /**
     * Get toList and ccList.
     *
     * @param object $opinion
     * @access public
     * @return bool|array
     */
    public function getToAndCcList($data)
    {
        /* Set toList and ccList. */
        /* 初始化发信人和抄送人变量，获取发信人和抄送人数据。*/

        $toList = $data->dealUser;
        $ccListArr = array_filter(array_unique(array_merge($this->post->mailto,$this->post->defaultMailto)));
        $ccList = implode(',',$ccListArr);
        $consumed = $this->loadModel('consumed')->getLastConsumed($data->id, 'productionchange');

        if($consumed->before == 'waitValidate' && $this->post->result == 1)
        {
            $toList = '';
            $ccList = '';
        }

        //验证通过不发邮件
        if($data->status == 'validateSuccess')
        {
            $toList = '';
            $ccList = '';
        }
        return array($toList, $ccList);
    }


    /**
     * @Notes:获取默认抄送人
     * @Date: 2024/5/14
     * @Time: 16:54
     * @Interface getDefaultMailto
     * @param $preproduction
     */
    public function getDefaultMailto($preproduction)
    {
        $defaultMailto = '';
        $vocalInterfacePersonArr = explode(',',$preproduction->interfacePerson);//业务方接口人
        $operationPersonArr = explode(',',$preproduction->operationPerson);//运维方接口人
        $deptConfirmPersonArr = explode(',',$preproduction->deptConfirmPerson);//建设方/业务方/运维方部门负责人
        $interfacePersonArr = explode(',',$preproduction->interfacePerson);//业务方部门负责人
        $operationDeptArr = explode(',',$preproduction->operationDept);//运维方部门负责人

        //建设方/业务方/运维方部门负责人（多人）、用户选择的其他抄送人员 去重取并集
        $mailtoArrList = array_unique(array_filter(array_merge($operationPersonArr,$deptConfirmPersonArr,$interfacePersonArr,$operationDeptArr,$vocalInterfacePersonArr)));
        $defaultMailto = implode(',',$mailtoArrList);
        return $defaultMailto;
    }


    /**
     * @Notes:所有节点及处理人
     * @Date: 2024/4/29
     * @Time: 17:45
     * @Interface getAllReviewerInfo
     * @param $preproductionInfo
     * @return array
     */
    public function getAllReviewerInfo($preproductionInfo){
        $interfacePerson = explode(',',$preproductionInfo->interfacePerson);
        $createdBy = [$preproductionInfo->createdBy];
        $waitValidate = array_merge(array_unique(array_filter(array_merge($interfacePerson,$createdBy))));
        $allReviewerInfo = [];
        $allReviewerInfo['wait'] = $preproductionInfo->applicant ? explode(',',trim($preproductionInfo->applicant,',')) : [];
        $allReviewerInfo['constructionDeptConfirm'] = $preproductionInfo->deptConfirmPerson ? explode(',',trim($preproductionInfo->deptConfirmPerson,',')) : [];//建设方部门处理
        $allReviewerInfo['vocalInterfacePerson'] = $preproductionInfo->interfacePerson ?  explode(',',trim($preproductionInfo->interfacePerson,',')) : [];//业务方接口人
        $allReviewerInfo['vocalDeptPerson'] = [];
        $allReviewerInfo['implementInterfacePerson'] = $preproductionInfo->operationPerson ? explode(',',trim($preproductionInfo->operationPerson,',')) : [];//实施接口人
        $allReviewerInfo['feedbackAndRepeatConfirm'] = [];
        $allReviewerInfo['operationDeptConfirm'] =  [];
        $allReviewerInfo['waitValidate'] =  $waitValidate;
        $allReviewerInfo['onlinesuccess'] = [];
        $allReviewerInfo['onlinefail'] = [];
        $allReviewerInfo['feedback']   =  $createdBy;//

        return $allReviewerInfo;
    }

    /**
     * @Notes:工作流信息
     * @Date: 2024/4/29
     * @Time: 11:09
     * @Interface iwfpSave
     * @param $preproductionInfo
     * @param $allReviewerInfo
     * @param $version
     * @return bool
     */
    public function iwfpSave($preproductionInfo, $allReviewerInfo, $version){
        /**
         * @var iwfpModel $iwfpModel
         */
        $iwfpModel = $this->loadModel('iwfp');
        return $res;
    }

    /**
     * @Notes:根据ID获取数据
     * @Date: 2024/4/24
     * @Time: 17:31
     * @Interface getByID
     * @param $preproductionID
     * @param $showFile
     * @return mixed
     */
    public function getByID($preproductionID,$showFile = false)
    {
        $data =$this->dao->select('*')->from(TABLE_PRODUCTIONCHANGE)->where('id')->eq($preproductionID)->andWhere('deleted')->eq(0)->fetch();
        if($showFile) $data->files = $this->loadModel('file')->getByObject('productionchange', $preproductionID);
        return $data;
    }

    /**
     * @param $objectID
     * @return mixed
     */
    public function getConsumedByID($objectID)
    {
        $info = $this->dao->select('*')->from(TABLE_CONSUMED)
            ->where('objectID')->eq($objectID)
            ->andWhere('objectType')->eq('productionchange')
            ->andWhere('deleted')->eq(0)
            ->fetchAll();

        return $info;
    }

    /**
     * @Notes:校验重复提交/审批
     * @Date: 2024/5/6
     * @Time: 15:15
     * @Interface checkSubmit
     * @param $preproductionInfo
     */
    public function checkSubmit($preproductionInfo)
    {
        $res = $this->dao->select('id')->from(TABLE_IWFP)
            ->where('objectID')->eq($preproductionInfo->id)
            ->andWhere('objectType')->eq('productionchange')
            ->andWhere('processXmlTaskId')->eq($preproductionInfo->status)
            ->andWhere('processTaskId')->eq($preproductionInfo->processInstanceId)
            ->fetch();
        return $res;
    }

    public function checkRequired($info)
    {
        $requiredFields = explode(',', $this->config->productionchange->edit->requiredFields);
        foreach ($requiredFields as $field){
            if(empty($info->$field)){
                dao::$errors[$field] =  sprintf($this->lang->productionchange->emptyObject, $this->lang->productionchange->$field);
                return;
            }
        }
        if(!$this->loadModel('common')->checkJkDateTime($info->onlineStart)) {
            dao::$errors['onlineStart'] =  sprintf($this->lang->productionchange->emptyObject, $this->lang->productionchange->onlineStart);
            return;
        }

        if(!$this->loadModel('common')->checkJkDateTime($info->onlineEnd)) {
            dao::$errors['onlineEnd'] =  sprintf($this->lang->productionchange->emptyObject, $this->lang->productionchange->onlineEnd);
            return;
        }

        /*
         *上线计划实施时间开始时间限制：
         *若选择上线申请类型为投产，则开始时间最早时间不能早于发起时间15个工作日。即3.1发起投产申请，开始时间最早为15个工作日之后（含）。（投产暂时不加该限制）
         *计划变更：提前3个工作日（含）
         *紧急变更：无限制
        */
        if($info->onlineType == 1) { //投产
            $wordDayTime =  strtotime(helper::getWorkDay(date('Y-m-d'),15).' 23:59:59');
            if(strtotime($info->onlineStart) < $wordDayTime) {
                dao::$errors['onlineStart'] =  $this->lang->productionchange->lessFifteenTip;
                return;
            }
        }elseif($info->onlineType == 2){ //计划变更
            $wordDayTime =  strtotime(helper::getWorkDay(date('Y-m-d'),2).' 23:59:59');
            if(strtotime($info->onlineStart) < $wordDayTime) {
                dao::$errors['onlineStart'] =  $this->lang->productionchange->lessThreeTip;
                return;
            }
        }
        //上线计划实施结束时间校验
        if(strtotime($info->onlineStart) > strtotime($info->onlineEnd)) {
            dao::$errors[''] =  $this->lang->productionchange->onlineTimeTip;
            return;
        }
        //是否影响管理系统校验
        if($info->ifEffectSystem == 1 && empty($info->effectSystemExplain)) {
            dao::$errors['effectSystemExplain'] = sprintf($this->lang->productionchange->emptyObject, $this->lang->productionchange->effectSystemExplain);
            return;
        }
        //需求条目、问题、工单最少关联一种
        if(empty($info->correlationDemand) && empty($info->correlationProblem) && empty($info->correlationSecondorder)) {
            dao::$errors[''] = $this->lang->productionchange->moreOne;
            return;
        }
        //部门确认责任人校验
        if($info->ifReport == 1 && empty($info->deptConfirmPerson)) {
            dao::$errors['deptConfirmPerson'] =  sprintf($this->lang->productionchange->emptyObject, $this->lang->productionchange->deptConfirmPerson);
            return;
        }

        //校验附件
        if(empty($info->files)){
            return dao::$errors = array('files' => $this->lang->productionchange->filesEmpty);
        }

        return true;
    }

}

