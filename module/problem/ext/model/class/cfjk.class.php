<?php

class cfjkProblem extends problemModel
{
    public function getExecutivePairs($deptId)
    {
        $deptId = '' == $deptId ? $this->app->user->dept : $deptId;

        $deptInfo = $this->dao->findById($deptId)->from(TABLE_DEPT)->fetch();
        if (empty($deptInfo)) {
            return [];
        }

        return $this->dao
            ->select('account, realname')
            ->from(TABLE_USER)
            ->where('account')->in($deptInfo->executive)
            ->andWhere('deleted')->eq('0')
            ->fetchpairs();
    }

    /**
     * 变更单申请
     * @param $id
     * @return array
     */
    public function delay($id)
    {
        $data = fixer::input('post')
            ->stripTags($this->config->problem->editor->delay['id'], $this->config->allowedTags)
            ->remove('uid,comment')
            ->get();

//        if (strtotime($data->delayResolutionDate) <= strtotime($data->originalResolutionDate)) {
//            $errors['delayResolutionDate'] = $this->lang->problem->delayResolutionDateError;
//
//            return dao::$errors = $errors;
//        }
        if($data->changeResolutionDate==$data->changeOriginalResolutionDate){
            $errors[] = "变更前和变更后的时间不能一样";
            return dao::$errors = $errors;
        }

        $oldProblem = $this->getByID($id);
        $version    = empty($oldProblem->changeVersion) ? 1 : $oldProblem->changeVersion + 1;
        $successVersion = empty($oldProblem->successVersion) ? 0 : $oldProblem->successVersion;

        //添加变更审批节点
        $reviewers = $this->loadModel('modify')->getReviewers();
        //创建人部门对应得部门负责人不能创建变更审批单
        if(empty(array_filter(array_keys($reviewers[2])))){
            $errors[] = $this->lang->problem->reviewError;

            return dao::$errors = $errors;
        }

        $data->changeStatus   = $this->lang->problem->reviewNodeStatusList['100']; //变更审批状态
        $data->changeVersion  = $version; //变更审批版本
        $data->successVersion = $successVersion; //变更审批通过版本
        $data->changeStage    = 100; //变更审批阶段
        $data->changeUser     = $this->app->user->account; //变更申请人
        $data->changeDate     = helper::now(); //变更申请时间
        $data->changeDealUser = implode(',', array_keys($reviewers[2])); //变更审批待处理人
        $data->objectId      = $id;
        $data->objectType    = 'problem';
        $data->changeContent    = sprintf($this->lang->problem->baseChangeContentStr,$oldProblem->PlannedTimeOfChange,$data->changeResolutionDate);
        $data                = $this->loadModel('file')->processImgURL($data, $this->config->problem->editor->delay['id'], $this->post->uid);

        $this->dao->insert(TABLE_PROBLEM_CHANGE)->data($data)->autoCheck()->batchCheck($this->config->problem->delay->requiredFields, 'notempty')->exec();
        $this->loadModel('consumed')->record('problem', $id, '0', $this->app->user->account, '', $data->changeStatus, [], 'problemChange');
        if(!dao::isError()){
            //部门负责人
            $this->loadModel('review')
                ->addNode(
                    'problemChange',
                    $id,
                    $version,
                    array_keys($reviewers[2]),
                    true,
                    'pending',
                    100,
                    ['nodeCode' => $this->lang->problem->reviewNodeStatusList['100']]
                );
            //产创部领导
            $this->loadModel('review')
                ->addNode(
                    'problemChange',
                    $id,
                    $version,
                    ['ningxiang'],
                    true,
                    'wait',
                    200,
                    ['nodeCode' => $this->lang->problem->reviewNodeStatusList['200']]
                );
        }
    }

    /**
     * 变更审批
     * @param $id
     * @return array|false
     */
    public function delayReview($id)
    {
        $oldProblem = $this->getByID($id);

        // 检查是否允许评审
        $res = $this->checkReview($oldProblem, $this->post->changeVersion, $this->post->changeStage, $this->app->user->account);
        if (!$res['result']) {
            dao::$errors['statusError'] = $res['message'];

            return false;
        }
        if (empty($_POST['result'])) {
            dao::$errors['result'] = $this->lang->problem->resultError;

            return false;
        }
        if ('reject' == $this->post->result && empty($_POST['suggest'])) {
            dao::$errors['suggest'] = $this->lang->problem->suggestError;
            return false;
        }
        if ('report' == $this->post->result && empty($_POST['toManager'])) {
            dao::$errors['toManager'] = $this->lang->problem->toManagerError;
            return false;
        }elseif('report' == $this->post->result && !empty($_POST['toManager'])){
            $toManager = $_POST['toManager'];
        }
//        a($oldProblem->changeVersion);die;
        $result = $this->loadModel('review')->check('problemChange', $id, $oldProblem->changeVersion, $this->post->result, $_POST['suggest'], '', '', false);
        if ('pass' == $result) {
            //审批未到最后的节点 部门领导审批
            if ($oldProblem->changeStage < 200) {
                //审批通过，自动前进一步
                $afterStage = $this->lang->problem->reviewNodeOrderList[$oldProblem->changeStage];
                //查找下一节点的状态
                $next = $this->dao
                    ->select('id')
                    ->from(TABLE_REVIEWNODE)
                    ->where('objectType')->eq('problemChange')
                    ->andWhere('objectID')->eq($id)
                    ->andWhere('version')->eq($oldProblem->changeVersion)
                    ->andWhere('status')->eq('wait')->orderBy('stage,id')->fetch('id');

                //更新下一节点的状态为pending
                if ($next) {
                    $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('pending')->where('id')->eq($next)->exec();
                    $this->dao->update(TABLE_REVIEWER)->set('status')->eq('pending')->where('node')->eq($next)->exec();

                    $this->loadModel('review');
                    $reviewers = $this->review->getReviewer('problemChange', $id, $oldProblem->changeVersion, $afterStage);
                    $this->dao
                        ->update(TABLE_PROBLEM_CHANGE)
                        ->set('changeDealUser')->eq($reviewers)
                        ->where('objectId')->eq($id)
                        ->andWhere('objectType')->eq('problem')
                        ->andWhere('id')->eq($oldProblem->changeId)
                        ->exec();
                }

                //更新状态
                if (isset($this->lang->problem->reviewNodeStatusList[$afterStage])) {
                    $status = $this->lang->problem->reviewNodeStatusList[$afterStage];
                    $this->dao
                        ->update(TABLE_PROBLEM_CHANGE)
                        ->set('changeStage')->eq($afterStage)
                        ->set('changeStatus')->eq($status)
                        ->where('objectId')->eq($id)
                        ->andWhere('objectType')->eq('problem')
                        ->andWhere('id')->eq($oldProblem->changeId)
                        ->exec();
                    $this->loadModel('consumed')->record('problem', $id, '0', $this->app->user->account, $oldProblem->changeStatus, $status, [], 'problemChange');
                }
            } else {
                $this->dao
                    ->update(TABLE_PROBLEM_CHANGE)
                    ->set('changeStatus')->eq('success')
                    ->set('changeDealUser')->eq('')
                    ->set('successVersion')->eq($oldProblem->successVersion + 1)
                    ->where('objectId')->eq($id)
                    ->andWhere('objectType')->eq('problem')
                    ->andWhere('id')->eq($oldProblem->changeId)
                    ->exec();
                $this->loadModel('consumed')->record('problem', $id, '0', $this->app->user->account, $oldProblem->changeStatus, 'success', [], 'problemChange');
                //变更成功后更新【计划解决（变更）时间】
                $this->dao->update(TABLE_PROBLEM)->set('PlannedTimeOfChange')->eq($oldProblem->changeResolutionDate)->where('id')->eq($oldProblem->id)->exec();
            }
        }
//        上报
        elseif ('report' == $result){
//            新增公司领导审批节点
            $this->loadModel('review')
                ->addNode(
                    'problemChange',
                    $id,
                    $oldProblem->changeVersion,
                    [$toManager],
                    true,
                    'pending',
                    300,
                    ['nodeCode' => $this->lang->problem->reviewNodeStatusList['300']]
                );
            //更新状态
                $status = $this->lang->problem->reviewNodeStatusList['300'];
                $this->dao
                    ->update(TABLE_PROBLEM_CHANGE)
                    ->set('changeStage')->eq(300)
                    ->set('changeStatus')->eq($status)
                    ->set('changeDealUser')->eq($toManager) //变更审批待处理人
                    ->where('objectId')->eq($id)
                    ->andWhere('objectType')->eq('problem')
                    ->andWhere('id')->eq($oldProblem->changeId)
                    ->exec();
                $this->loadModel('consumed')->record('problem', $id, '0', $this->app->user->account, $oldProblem->changeStatus, $status, [], 'problemChange');

        }else {
            $this->dao
                ->update(TABLE_PROBLEM_CHANGE)
                ->set('changeStatus')->eq('fail')
                ->set('changeDealUser')->eq('')
                ->where('objectId')->eq($id)
                ->andWhere('objectType')->eq('problem')
                ->andWhere('id')->eq($oldProblem->changeId)
                ->exec();
            $this->loadModel('consumed')->record('problem', $id, '0', $this->app->user->account, $oldProblem->changeStatus, 'fail', [], 'problemChange');
        }

        $problem = $this->getByID($id);

        return common::createChanges($oldProblem, $problem);
    }

    /**
     * 检查是否允许申请变更
     * @param $problem
     * @return string
     */
    public function delayCheck($problem)
    {
        $dealUser = $this->dao->select('dealUser')->from(TABLE_PROBLEM)->where('id')->eq($problem->id)->fetch();
        //后台是否分配权限
        if(!common::hasPriv('problem', 'delay')){
            return '没有申请变更单权限';
        }
        //已关闭问题单不能申请变更
//        if ('closed' == $problem->status){
//            return '问题单已关闭';
//        }
        if ('closed' == $problem->status){
            return '问题单已关闭';
        }
        //问题单待处理人或受理人
        $dealUser = array_merge(explode(',', $dealUser->dealUser), [$problem->acceptUser]);
        if(!in_array($this->app->user->account, $dealUser)){
            return '不是该问题单受理人或待处理人';
        }
        //是否有在途变更单
        if(in_array($problem->changeStatus, $this->lang->problem->reviewNodeStatusList)){
            return '存在在途【变更】流程，请在流程结束后进行操作';
        }

        if('guestcn' == $problem->createdBy){//清总
            $flag = 'firstpassed' == $problem->reviewStatus;
            $consumedInfo = $this->dao->select('objectID')->from(TABLE_CONSUMED)
//                ->where(' (`before` = "finalpassed" and `after` = "closed")')
//                ->orWhere('`after`')->eq('finalpassed')
//                ->andwhere('objectType')->eq('problem')
                ->where('objectType')->eq('problem')
                ->andWhere('`after`')->eq('finalpassed')
                ->andWhere('objectID')->eq($problem->id)
                ->andWhere('deleted')->eq('0')
                ->fetch();
            $flagInfo = !empty($consumedInfo);
            if(!$flag && !$flagInfo){
//                return '反馈状态为初步解决反馈通过或问题单已最终解决反馈通过或最终解决反馈通过被关闭的才能申请变更单';
                return '反馈状态为初步解决反馈通过或问题单已最终解决反馈通过才能申请变更单';
            }
        }elseif ('guestjx' == $problem->createdBy){//金信
            $consumedInfo = $this->dao->select('objectID')->from(TABLE_CONSUMED)
//                ->where(' (`before` = "approvesuccess" and `after` = "closed")')
//                ->orWhere('`after`')->eq('approvesuccess')
//                ->andwhere('objectType')->eq('problem')
                ->where('objectType')->eq('problem')
                ->andWhere('`after`')->eq('approvesuccess')
                ->andWhere('objectID')->eq($problem->id)
                ->andWhere('deleted')->eq('0')
                ->fetch();
            if(empty($consumedInfo)){
//                return '外部已通过后或外部通过后被关闭的才能申请变更';
                return '外部已通过后才能申请变更';
            }
        }else{//自建
            $status = ['feedbacked', 'build', 'released', 'delivery', 'onlinesuccess', 'closed', 'returned', 'toclose', 'exception',];
            if(!in_array($problem->status, $status)){
                return '问题单分析后才允许申请变更';
            }
        }

        return '';
    }

    /**
     * 关闭的时候检查是否有在途的变更
     * @param $problem
     * @return string
     */
    public function closeCheckDelay($problem)
    {
        //是否有在途变更单
        if(in_array($problem->changeStatus, $this->lang->problem->reviewNodeStatusList)){
            return '存在在途【变更】流程，请在流程结束后进行操作';
        }
    }

    // 检查是否允许审核
    public function checkReview($problem, $version = 1, $reviewStage = 0, $userAccount = '')
    {
        $res = [
            'result'  => false,
            'message' => '',
        ];
        if (!$problem) {
            $res['message'] = $this->lang->common->errorParamId;

            return $res;
        }
        if (!$userAccount) {
            $res['message'] = $this->lang->common->errorParamUser;

            return $res;
        }
        //审核节点已经经过
        if (($version != $problem->changeVersion) || ($reviewStage != $problem->changeStage)) {
            $res['message'] = $this->lang->problem->nowStageError;

            return $res;
        }
        // 当前用户不允许审批
        if (!in_array($userAccount, explode(',', $problem->changeDealUser))) {
            $res['message'] = $this->lang->problem->approverError;

            return $res;
        }
        // 当前状态不允许审批
        if (!in_array($problem->changeStatus, $this->lang->problem->allowReviewList)) {
            $this->app->loadLang('sectransfer');
            $res['message'] = $this->lang->sectransfer->stateReviewError;

            return $res;
        }

        $res['result'] = true;

        return $res;
    }

    /**
     * 内部反馈超时时间
     * @return array
     */
    public function insideFeedback()
    {
        //是否有发送邮件权限
        $this->loadModel('common');
        if (!$this->common->isSetMessage('mail', 'problem', 'insideFeedback')) {
            return [];
        }

        $field        = 'feedbackStartTimeInside';
        $reviewStatus = ['tofeedback', 'todeptapprove'];
        //清总
        $qzToIds = [];
        $qzIds   = [];
        if (helper::isWorkDay(date('Y-m-d H:i:s'))) {
            $created = 'guestcn';
            $setMail = 'setInFBToTimeMail';
            $toOut   = !empty($this->lang->problem->problemOutTime['inQzFBToTime']) ? $this->lang->problem->problemOutTime['inQzFBToTime'] : 2;
            $out     = !empty($this->lang->problem->problemOutTime['inQzFBOutTime']) ? $this->lang->problem->problemOutTime['inQzFBOutTime'] : 4;

            //即将超时
            $start   = helper::getWorkDay(date('Y-m-d'), -($out - 1)) . ' 00:00:00';
            $start   = $this->getStartTime($start);
            $end     = helper::getWorkDay(date('Y-m-d'), -($toOut)) . ' 23:59:59';
            $qzToIds = $this->sendmailByFeedback($field, $start, $end, $reviewStatus, $created, $setMail, false);

            //超时
            $setMail = 'setInFBOutTimeMail';
            $start   = helper::getWorkDay(date('Y-m-d'), -($out)) . ' 00:00:00';
            $start   = $this->getStartTime($start);
            $end     = helper::getWorkDay(date('Y-m-d'), -($out)) . ' 23:59:59';
            $qzIds   = $this->sendmailByFeedback($field, $start, $end, $reviewStatus, $created, $setMail, true);
        }

        //金信
        $created = 'guestjx';
        $setMail = 'setInFBToTimeMail';
        $toOut   = !empty($this->lang->problem->problemOutTime['inJxFBToTime']) ? $this->lang->problem->problemOutTime['inJxFBToTime'] : 13;
        $out     = !empty($this->lang->problem->problemOutTime['inJxFBOutTime']) ? $this->lang->problem->problemOutTime['inJxFBOutTime'] : 15;
        //即将超时
        $start   = date('Y-m-d 00:00:00', strtotime('-' . $out . ' day'));
        $end     = date('Y-m-d 23:59:59', strtotime('-' . ($toOut + 1) . ' day'));
        $jxToIds = $this->sendmailByFeedback($field, $start, $end, $reviewStatus, $created, $setMail, false);
        //超时
        $setMail = 'setInFBOutTimeMail';
        $start   = date('Y-m-d 00:00:00', strtotime('-' . ($out + 1) . ' day'));
        $end     = date('Y-m-d 23:59:59', strtotime('-' . ($out + 1) . ' day'));
        $jxIds   = $this->sendmailByFeedback($field, $start, $end, $reviewStatus, $created, $setMail, true);

        return ['qzToIds' => $qzToIds, 'qzIds' => $qzIds, 'jxToIds' => $jxToIds, 'jxIds' => $jxIds];
    }

    /**
     * 外部反馈超时时间
     * @return array
     */
    public function outsideFeedback()
    {
        //是否有发送邮件权限
        $this->loadModel('common');
        if (!$this->common->isSetMessage('mail', 'problem', 'outsideFeedback')) {
            return [];
        }

        $field        = 'feedbackStartTimeOutside';
        $reviewStatus = ['tofeedback', 'todeptapprove', 'deptapproved'];
        //清总
        $qzToIds = [];
        $qzIds   = [];
        if (helper::isWorkDay(date('Y-m-d H:i:s'))) {
            $created = 'guestcn';
            $setMail = 'setOutFBToTimeMail';
            $toOut   = !empty($this->lang->problem->problemOutTime['outQzFBToTime']) ? $this->lang->problem->problemOutTime['outQzFBToTime'] : 2;
            $out     = !empty($this->lang->problem->problemOutTime['outQzFBOutTime']) ? $this->lang->problem->problemOutTime['outQzFBOutTime'] : 4;
            //即将超时
            $start   = helper::getWorkDay(date('Y-m-d'), -($out - 1)) . ' 00:00:00';
            $start   = $this->getStartTime($start);
            $end     = helper::getWorkDay(date('Y-m-d'), -($toOut)) . ' 23:59:59';
            $qzToIds = $this->sendmailByFeedback($field, $start, $end, $reviewStatus, $created, $setMail, false);

            //超时
            $setMail = 'setOutFBOutTimeMail';
            $start   = helper::getWorkDay(date('Y-m-d'), -($out)) . ' 00:00:00';
            $start   = $this->getStartTime($start);
            $end     = helper::getWorkDay(date('Y-m-d'), -($out)) . ' 23:59:59';
            $qzIds   = $this->sendmailByFeedback($field, $start, $end, $reviewStatus, $created, $setMail, true);
        }

        //金信
        $created = 'guestjx';
        $setMail = 'setOutFBToTimeMail';
        $toOut   = !empty($this->lang->problem->problemOutTime['outJxFBToTime']) ? $this->lang->problem->problemOutTime['outJxFBToTime'] : 13;
        $out     = !empty($this->lang->problem->problemOutTime['outJxFBOutTime']) ? $this->lang->problem->problemOutTime['outJxFBOutTime'] : 15;
        //即将超时
        $start   = date('Y-m-d 00:00:00', strtotime('-' . $out . ' day'));
        $end     = date('Y-m-d 23:59:59', strtotime('-' . ($toOut + 1) . ' day'));
        $jxToIds = $this->sendmailByFeedback($field, $start, $end, $reviewStatus, $created, $setMail, false);
        //超时
        $setMail = 'setOutFBOutTimeMail';
        $start   = date('Y-m-d 00:00:00', strtotime('-' . ($out + 1) . ' day'));
        $end     = date('Y-m-d 23:59:59', strtotime('-' . ($out + 1) . ' day'));
        $jxIds   = $this->sendmailByFeedback($field, $start, $end, $reviewStatus, $created, $setMail, true);

        return ['qzToIds' => $qzToIds, 'qzIds' => $qzIds, 'jxToIds' => $jxToIds, 'jxIds' => $jxIds];
    }

    /**
     * 发送问题反馈超时邮件
     * @param  mixed $field
     * @param  mixed $start
     * @param  mixed $end
     * @param  mixed $reviewStatus
     * @param  mixed $createdBy
     * @param  mixed $setMail
     * @param  mixed $isOut
     * @return array
     */
    public function sendmailByFeedback($field, $start, $end, $reviewStatus, $createdBy, $setMail, $isOut = true)
    {
        $this->app->loadLang('demand');

        $data = $this->dao
            ->select('*')
            ->from(TABLE_PROBLEM)
            ->where($field)->between($start, $end)
            ->andWhere('ReviewStatus')->in($reviewStatus)
            ->andWhere('createdBy')->eq($createdBy)
            ->andWhere('status')->notin(['deleted', 'closed'])
            ->fetchAll('id');

        $ids = [];
        if (!empty($data)) {
            foreach ($data as $item) {
                $toList = trim($item->feedbackToHandle, ',');
                if (empty($toList)) {
                    $toList = trim($item->dealUser, ',');
                } else {
                    $toList .= ',' . trim($item->dealUser, ',');
                }

                $ccList = $this->dao->select('account')->from(TABLE_CONSUMED)
                    ->where('objectType')->eq('problem')
                    ->andWhere('objectID')->eq($item->id)
                    ->fetchpairs();
                $ccList = implode(',', array_filter(array_unique($ccList)));
                $ccList = $isOut ? $ccList . ',' . implode(',', array_filter(array_unique(array_keys($this->lang->demand->outTimeList)))) : $ccList;

                $item               = $this->getIfOverDate($item);
                $item->feedbackType = $field;
                $this->sendmailBase($item, $setMail, 'problem', $toList, $ccList, 'problemouttime');

                $ids[] = $item->id;
            }
        }

        return $ids;
    }

    /**
     * 发送问题解决超时提醒
     *
     * @return array
     */
    public function sendmailBySolvingOutTime()
    {
        $this->loadModel('common');
        $this->app->loadLang('demand');
        //是否有发送邮件权限
        if (!$this->common->isSetMessage('mail', 'problem', 'sendmailBySolvingOutTime')) {
            return [];
        }

        $aboutToIds = [];
        $outTimeIds = [];
        $date       = date('Y-m-d');
        $outTime    = $this->lang->problem->problemOutTime['problemOutTime']     ?? 2; //解决时间超时月份
        $toOut      = $this->lang->problem->problemOutTime['problemToOutTime'] ?? 5; //即将超时天数
        $data       = $this->dao
            ->select('t1.`id`,t1.`code`,t1.`abstract`,t1.`createdDate`,t1.`dealAssigned`,t1.`feedbackExpireTime`,t1.`status`,t1.`desc`,t1.`dealUser`,t1.`acceptUser`,t2.delayStatus')
            ->from(TABLE_PROBLEM)->alias('t1')
            ->leftJoin(TABLE_DELAY)->alias('t2')
            //->on("t1.id = t2.objectId and t2.objectType = 'problem' and t2.isEnd = '1'")
            ->on("t1.id = t2.objectId and t2.objectType = 'problem' ")
            ->where('t1.status')->in('confirmed,assigned,feedbacked,build,released')
            ->orderBy('id desc')
            ->fetchAll('id');

        foreach ($data as $key => $item) {
            //变更申请单已通过，不发超时提醒
            if ('success' == $item->delayStatus) {
                unset($data[$key]);
                continue;
            }
            //如果【交付周期计算起始时间】为空，不发超期提醒邮件
            if(empty($item->dealAssigned) || strpos($item->dealAssigned, '0000') !== false){
                unset($data[$key]);
                continue;
            }

            $flag    = 0; //1：即将超期；2：已超期；0：未超期
            $end     = $this->getOverDate($item->dealAssigned, $outTime); //超期时间
            $start   = date('Y-m-d', strtotime($end) - 86400 * $toOut);
            $end     = date('Y-m-d', strtotime($end) + 86400);

            if ($date > $start && $date < $end) {
                $flag                  = 1;
                $aboutToIds[$item->id] = $item->id;
            }
            if ($date == $end) {
                $flag                  = 2;
                $outTimeIds[$item->id] = $item->id;
            }
            if (0 == $flag) {
                continue;
            }

            $cs = $this->dao->select('account')->from(TABLE_CONSUMED)
                ->where('objectType')->eq('problem')
                ->andWhere('objectID')->eq($item->id)
                ->andWhere('parentID')->eq('0')
                ->andWhere('deleted')->eq(0)
                ->andWhere('extra')->notIn(['problemFeedBack', 'problemDelay'])
                ->fetchAll();
            $cs     = array_unique(array_merge(explode(',', $item->dealUser), array_column($cs, 'account')));
            $toList = implode(',', $cs);
            if (empty($toList)) {
                unset($data[$key]);
                unset($aboutToIds[$item->id]);
                unset( $outTimeIds[$item->id]);
                continue;
            }
            $ccList = implode(',', array_filter(array_unique(array_keys($this->lang->demand->outTimeList))));
            $ccList = 2 == $flag ? $ccList : '';

            $setMail = $flag == 1 ? 'setProblemToOutTimeMail' : 'setProblemOutTimeMail';
            $this->sendmailBase($item, $setMail, 'problem', $toList, $ccList, 'problemouttime');

        }

        return ['aboutTo' => $aboutToIds, 'outTime' => $outTimeIds];
    }

    /**
     * 发送邮件
     * @param $problem
     * @param $setMail
     * @param $browseType
     * @param $toList
     * @param $ccList
     * @param $viewName
     * @param $mailTitle
     */
    public function sendmailBase($problem, $setMail, $browseType, $toList, $ccList, $viewName, $mailTitle = false)
    {
        $this->loadModel('mail');
        $users = $this->loadModel('user')->getPairs('noletter');

        // 获取后台通知中配置的邮件发信。
        $this->app->loadLang('custommail');
        $mailConf = $this->config->global->{$setMail} ?? '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);

        //邮件标题
        $mailTitle = !$mailTitle ? vsprintf($mailConf->mailTitle, $mailConf->variables) : $mailTitle;

        //邮件内容
        $oldcwd     = getcwd();
        $modulePath = $this->app->getModulePath($appName = '', $browseType);
        $viewFile   = $modulePath . 'view/' . $viewName . '.html.php';
        chdir($modulePath . 'view');
        if (file_exists($modulePath . 'ext/view/' . $viewName . '.html.php')) {
            $viewFile = $modulePath . 'ext/view/' . $viewName . '.html.php';
            chdir($modulePath . 'ext/view');
        }

        ob_start();
        include $viewFile;
        foreach (glob($modulePath . 'ext/view/sendmail.*.html.hook.php') as $hookFile) {
            include $hookFile;
        }
        $mailContent = ob_get_contents();
        ob_end_clean();
        chdir($oldcwd);

        //获取后台自定义配置人 研发部门
        $this->loadModel('demand');
        $deptLeadersList = $this->config->demand->deptLeadersList;
        $user = $this->loadModel('user')->getById($problem->acceptUser);
        if($user)
        {
            $dept = $user->dept;
            $ccListArray = explode(',',$ccList);
            $deptLeaderListArray = explode(',',$deptLeadersList->$dept);
            $mergeCcList = array_filter(array_unique(array_merge($ccListArray,$deptLeaderListArray)));
            if(!empty($mergeCcList))
            {
                $ccList = implode(',',$mergeCcList);
            }
        }

        $this->mail->send($toList, $mailTitle, $mailContent, $ccList);

        if ($this->mail->isError()) {
            error_log(implode("\n", $this->mail->getError()));
        }
    }

    /**
     * 计算内外部反馈超时时间
     * @param $problem
     * @return mixed
     */
    public function getIfOverDate($problem)
    {
        //如果问题单问内部自建内外部反馈超期时间为空
        if (empty($problem->IssueId)) {
            $problem->ifOverDate           = [];
            $problem->ifOverDate['flag']   = '';
            $problem->ifOverDate['string'] = '';
            $problem->ifOverDate['start']  = '';
            $problem->ifOverDate['end']    = '';

            return $problem;
        }

        $problem->ifOverDate           = $problem->ifOverDateInside           = [];
        $problem->ifOverDate['string'] = $problem->ifOverDateInside['string'] = '否';
        $problem->ifOverDate['flag']   = $problem->ifOverDateInside['flag']   = '否';

        //内部反馈开始时间：待分配到待分析的时间
        if (
            !empty($problem->feedbackStartTimeInside)
            && '0000-00-00 00:00:00' != $problem->feedbackStartTimeInside
        ) {//如果内部反馈开始时间有值取当前值
            $feedbackStartTimeInside = $problem->feedbackStartTimeInside;
        } else {//如果内部反馈开始时间和待分配到待分析处理时间都为空则为空
            $feedbackStartTimeInside = '';
        }
        //内部反馈结束时间：清总：内部反馈开始时间 + 3个工作日；金信：内部反馈开始时间 + 15个自然日
        if (
            !empty($problem->feedbackEndTimeInside)
            && '0000-00-00 00:00:00' != $problem->feedbackEndTimeInside
        ) {//如果内部反馈结束时间有值取该值
            $feedbackEndTimeInside = $problem->feedbackEndTimeInside;
        } elseif (!empty($feedbackStartTimeInside)) {//如果内部反馈结束时间为空，内部反馈开始时间有值，计算内部反馈截止时间
            if ('guestcn' == $problem->createdBy) {
                $feedbackEndTimeInside = helper::getWorkDay($feedbackStartTimeInside, $this->lang->problem->expireDaysList['days']) . substr($feedbackStartTimeInside, 10);
            } else {
                $feedbackEndTimeInside = date('Y-m-d H:i:s', strtotime('+' . $this->lang->problem->expireDaysList['jxExpireDays'] . ' day', strtotime($feedbackStartTimeInside)));
            }
        } else {
            $feedbackEndTimeInside = '';
        }

        $problem->deptPassTime = !empty($problem->deptPassTime) && '0000-00-00 00:00:00' != $problem->deptPassTime ? $problem->deptPassTime : '';
        if (!empty($feedbackStartTimeInside)) {
            $deptPassTime = !empty($problem->deptPassTime) ? $problem->deptPassTime : date('Y-m-d H:i:s');

            $problem->ifOverDateInside['flag']   = $feedbackEndTimeInside < $deptPassTime ? '是' : '否';
            $problem->ifOverDateInside['string'] = $problem->ifOverDateInside['flag'] . " ({$feedbackStartTimeInside}~{$problem->deptPassTime})";
            $problem->ifOverDateInside['start']  = $feedbackStartTimeInside;
            $problem->ifOverDateInside['end']    = $feedbackEndTimeInside;

            if($problem->ifOverDateInside['flag'] == '是'){
                $this->dao->update(TABLE_PROBLEM)->set('ifOverDateInside')->eq('1')->where('id')->eq($problem->id)->exec();
            }
        }

        //外部反馈开始时间：问题单【创建时间】
        if (!empty($problem->feedbackStartTimeOutside) && '0000-00-00 00:00:00' != $problem->feedbackStartTimeOutside) {
            $feedbackStartTimeOutside = $problem->feedbackStartTimeOutside;
        } else {
            $feedbackStartTimeOutside = '';
        }
        if (!empty($problem->feedbackEndTimeOutside) && '0000-00-00 00:00:00' != $problem->feedbackEndTimeOutside) {
            $feedbackEndTimeOutside = $problem->feedbackEndTimeOutside;
        } elseif (!empty($feedbackStartTimeOutside)) {
            //外部反馈结束时间：清总：内部反馈开始时间 + 3个工作日；金信：内部反馈开始时间 + 15个自然日
            if ('guestcn' == $problem->createdBy) {
                $feedbackEndTimeOutside = helper::getWorkDay($feedbackStartTimeOutside, $this->lang->problem->expireDaysList['days']) . substr($feedbackStartTimeOutside, 10);
            } else {
                $feedbackEndTimeOutside = date('Y-m-d H:i:s', strtotime('+' . $this->lang->problem->expireDaysList['jxExpireDays'] . ' day', strtotime($feedbackStartTimeOutside)));
            }
        } else {
            $feedbackEndTimeOutside = '';
        }

        $problem->innovationPassTime = !empty($problem->innovationPassTime) && '0000-00-00 00:00:00' != $problem->innovationPassTime ? $problem->innovationPassTime : '';
        if (!empty($feedbackStartTimeOutside)) {
            $innovationPassTime = !empty($problem->innovationPassTime) ? $problem->innovationPassTime : date('Y-m-d H:i:s');

            $problem->ifOverDate['flag']   = $feedbackEndTimeOutside < $innovationPassTime ? '是' : '否';
            $problem->ifOverDate['string'] = $problem->ifOverDate['flag'] . " ({$feedbackStartTimeOutside}~{$problem->innovationPassTime})";
            $problem->ifOverDate['start']  = $feedbackStartTimeOutside;
            $problem->ifOverDate['end']    = $feedbackEndTimeOutside;
        }

        return $problem;
    }

    public function getSolvedTime($problem)
    {
        if (false !== strpos($problem->solvedTime, '0000-00-00')) {
            $problem->solvedTime = '';
        }

        return $problem;
    }

    /**
     * 获取超时提醒开始时间
     * @param $start
     * @return string
     */
    public function getStartTime($start)
    {
        do {
            $date = date('Y-m-d 00:00:00', strtotime('-1 day', strtotime($start)));

            try {
                $flag = !helper::isWorkDay($date);
            } catch (Exception $e) {
                return $start;
            }

            if ($flag) {
                $start = $date;
            }
        } while ($flag);

        return $start;
    }

    /**
     * 获取问题反馈单同步接口首次成功时间
     * @param $problem
     * @return false
     */
    public function getInnovationPassTime($problem)
    {
        $reviewModel = $this->loadModel('review');

        $node = $this->dao->select('id')->from(TABLE_REVIEWNODE)
            ->where('`status`')->in(['syncsuccess', 'jxsyncsuccess'])
            ->andWhere('objectType')->eq('problem')
            ->andWhere('objectID')->eq($problem->id)
            ->andWhere('stage')->eq(3)->fetch();
        if ($node) {
            if ('guestcn' == $problem->createdBy) {
                $nodeOutInfo = $reviewModel->getReviewerInfoByParams('id,reviewTime', 'syncsuccess', $node->id);
            } else {
                $nodeOutInfo = $reviewModel->getReviewerInfoByParams('id,reviewTime', 'jxsyncsuccess', $node->id);
            }

            return $nodeOutInfo->reviewTime;
        }

        return false;
    }

    /**
     * 获取问题单是否交付超期字段
     * @param $problem
     * @return mixed
     */
    public function getIsExceed($problem)
    {
        $monthReport       = $this->loadModel('secondmonthreport');
        $alreadySolve      = ['delivery', 'onlinesuccess', 'closed', 'toclose'];
        $waitSolve         = ['assigned', 'feedbacked', 'released', 'build', 'exception'];
        $now               = helper::now();
        $exceedFlag        = true;
        $problem->isExceed = '否';

        if ('noproblem' != $problem->type && 32 != $problem->source && 1 != $problem->isExtended) {
            $exceedFlag = false;
        }

        $exceedTime = $monthReport->getOverDate($problem->dealAssigned, 2);
        $exceedTime .= substr($problem->dealAssigned, 10);

        //状态为待解决
        if (in_array($problem->status, $waitSolve)) {
            if (false === strpos($problem->dealAssigned, '0000') && $exceedTime < $now && !$exceedFlag) {
                $problem->isExceed = '是';
            }
        }
        //状态为已解决
        if (in_array($problem->status, $alreadySolve)) {
            if (false === strpos($problem->dealAssigned, '0000') && $exceedTime < $problem->solvedTime && !$exceedFlag) {
                $problem->isExceed = '是';
            }
        }

        return $problem;
    }

    /**
     * 获取问题单交付是否超期（只对比时间）
     * @param $problem
     * @return mixed
     */
    public function getIsExceedByTime($problem)
    {
        $monthReport  = $this->loadModel('secondmonthreport');
        $alreadySolve = ['delivery', 'onlinesuccess', 'closed', 'toclose'];
        $waitSolve    = ['assigned', 'feedbacked', 'released', 'build', 'exception'];
        $now          = helper::now();
        $isExceed     = '否';

        $exceedTime = $monthReport->getOverDate($problem->dealAssigned, 2);
        $exceedTime .= substr($problem->dealAssigned, 10);

        //状态为待解决
        if (in_array($problem->status, $waitSolve)) {
            if (false === strpos($problem->dealAssigned, '0000') && $exceedTime < $now) {
                $isExceed = '是';
            }
        }
        //状态为已解决
        if (in_array($problem->status, $alreadySolve)) {
            if (false === strpos($problem->dealAssigned, '0000') && $exceedTime < $problem->solvedTime) {
                $isExceed = '是';
            }
        }

        $this->dao->update(TABLE_PROBLEM)->set('isExceedByTime')->eq($isExceed)->where('id')->eq($problem->id)->exec();

        return $isExceed;
    }

    /**
     * 获取超时时间
     * @param $date
     * @param $monthNum
     * @return false|string
     */
    public function getOverDate($date, $monthNum)
    {
        $monthNum      = (int)$monthNum;
        $dateTimestamp = strtotime($date);
        $startMonth    = (int)date('m', $dateTimestamp);
        $startYear     = (int)date('Y', $dateTimestamp);
        $startDay      = (int)date('j', $dateTimestamp);
        $addMonth      = $startMonth + $monthNum;

        $resultMonth = ($addMonth % 12);
        $resultYear  = ((int)($addMonth / 12)) + $startYear;
        if (0 == $resultMonth) {
            $resultMonth = 12;
            $resultYear  = $resultYear - 1;
        }

        $result          = $resultYear . '-' . $resultMonth;
        $resultTimestamp = strtotime($result);
        $endMonthDays    = date('t', $resultTimestamp);
        if ($startDay > $endMonthDays) {
            $resultDay = $endMonthDays;
        } else {
            $resultDay = $startDay;
        }

        return date('Y-m-d', strtotime($result . '-' . $resultDay));
    }

    /**
     * 查询问题是否按计划完成
     * @param $problemIds
     * @return mixed
     */
    public function getCompletedPlan($problem = null)
    {
       /* 计算规则如下：
        (1)【交付时间】-【计划解决（变更）时间】＞ 0 ,【是否按计划解决】值为否，反之为是；【注意：按日期计算，忽略时分秒】
        (2)【计划解决（变更）时间】为空时，【是否按计划解决】值为是；
        (3)【交付时间】值为空时，当前时间（按日期计算）-【计划解决（变更）时间】≤ 0,【是否按计划解决】值为是，反之为否。
       (4)若经以上计算【是否按计划解决】值为否时，需判断【延期解决时间】是否为空，若为空，不再做计算；若不为空，需按以下规则计算：
            a.【交付时间】-【延期解决时间】与【计划解决（变更）时间】中较大值＞ 0 ,【是否按计划解决】值为否，反之为是；
            b.【交付时间】值为空时，当前时间-【延期解决时间】与【计划解决（变更）时间】中较大值≤ 0,【是否按计划解决】值为是，反之为否。
        */
        $updatearr = array();
        // 查询延期时间，并比较延期期间和计划解决变更时间，取最大的
        $PlannedTimeOfChangeMax = "(select  (case when date(d.delayResolutionDate) > date(p.PlannedTimeOfChange) then d.delayResolutionDate
		           when p.PlannedTimeOfChange is null  then d.delayResolutionDate
		           else p.PlannedTimeOfChange end) from zt_delay d where d.objectId  = p.id and d.delayStatus  = 'success') PlannedTimeOfChangeMax";
        //查询未删除且需要联动的数据
        $problems = $this->dao->select("p.id,solvedTime,PlannedTimeOfChange,code,completedPlan,$PlannedTimeOfChangeMax")->from(TABLE_PROBLEM)->alias('p')
            ->where('status')->ne('deleted')
            ->andWhere('completedPlanFlag')->eq('2')
            ->beginIF($problem)->andWhere('p.id')->eq($problem)->fi()
            ->fetchAll();

        foreach ($problems as $problem) {
            $oldProblem = $this->getByID($problem->id);
            $completedPlan = '';
            $problem->PlannedTimeOfChange = $problem->PlannedTimeOfChangeMax ? $problem->PlannedTimeOfChangeMax : $problem->PlannedTimeOfChange ;
            // (1)【交付时间】-【计划解决（变更）时间】＞ 0 ,【是否按计划解决】值为否，反之为是；【注意：按日期计算，忽略时分秒】
            if($problem->solvedTime && $problem->PlannedTimeOfChange &&  (strpos($problem->solvedTime, '0000') === false && strpos($problem->PlannedTimeOfChange, '0000') === false)){
               $solvedTime = date('Y-m-d',strtotime($problem->solvedTime));
               $PlannedTimeOfChange = date('Y-m-d',strtotime($problem->PlannedTimeOfChange));
               if((strtotime($solvedTime) - strtotime($PlannedTimeOfChange)) > 0){
                   $completedPlan = 2; //否
               }else{
                   $completedPlan = 1; // 是
               }
            }else if(empty($problem->PlannedTimeOfChange) || strpos($problem->PlannedTimeOfChange, '0000') !== false){
                //【计划解决（变更）时间】为空时，【是否按计划解决】值为是；
                $completedPlan = 1; //是
            }else if((empty($problem->solvedTime) || strpos($problem->solvedTime, '0000') !== false) && ($problem->PlannedTimeOfChange && strpos($problem->PlannedTimeOfChange, '0000') === false)){
                //(3)【交付时间】值为空时，当前时间（按日期计算）-【计划解决（变更）时间】≤ 0,【是否按计划解决】值为是，反之为否。
                $now = helper::today();
                if((strtotime($now) - strtotime($problem->PlannedTimeOfChange)) <= 0){
                    $completedPlan = 1; // 是
                }else{
                    $completedPlan = 2; //否
                }
            }
            //更新问题单
            if($problem->completedPlan != $completedPlan){
                $this->dao->update(TABLE_PROBLEM)->set('completedPlan')->eq($completedPlan)->where('id')->in($problem->id)->exec();
                $nowProblem = $this->getByID($problem->id);
                $updatearr[] =  $problem->id;
                $change = common::createChanges($oldProblem, $nowProblem);
                if($change)
                {
                    $actionID = $this->loadModel('action')->create('problem', $problem->id, 'editcompletedplaned','','','guestjk');
                    $this->action->logHistory($actionID, $change);
                }

            }
        }
        return  implode(',',$updatearr);
    }

    /**
     * 查询问题考核结果
     * @param $problemIds
     * @return mixed
     */
    public function getExaminationResult($problem = null)
    {
        /* 计算规则如下：
         【考核结果】字段取值逻辑：【是否按计划解决】值为否，【考核结果】默认为延期，反之为正常
         */
        $updatearr = array();
        //查询未删除且需要联动的数据
        $problems = $this->dao->select('id,examinationResult,completedPlan')->from(TABLE_PROBLEM)
            ->where('status')->ne('deleted')
            ->andWhere('examinationResultFlag')->eq('2')
            ->beginIF($problem)->andWhere('id')->eq($problem)->fi()
            ->fetchAll();

        foreach ($problems as $problem) {
            $oldProblem = $this->getByID($problem->id);
            $examinationResult =  $problem->completedPlan == '1' ? '1' : '2';
            //更新问题单
            if($problem->examinationResult != $examinationResult){
                $this->dao->update(TABLE_PROBLEM)->set('examinationResult')->eq($examinationResult)->where('id')->in($problem->id)->exec();
                $nowProblem = $this->getByID($problem->id);
                $updatearr[] =  $problem->id;
                $change = common::createChanges($oldProblem, $nowProblem);
                if($change)
                {
                    $actionID = $this->loadModel('action')->create('problemexaminationresult', $problem->id, 'editexaminationresulted','','','guestjk');
                    $this->action->logHistory($actionID, $change);
                }

            }
        }
        return implode(',',$updatearr);
    }
}





