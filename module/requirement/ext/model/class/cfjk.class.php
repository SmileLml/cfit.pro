<?php

class cfjkrequirement extends requirementModel
{
    /**
     * 发送需求任务超时提醒-内部
     *
     * @return array
     * @access public
     */
    public function sendmailByOutTime()
    {
        $this->loadModel('common');
        //是否有发送邮件权限
        if(!$this->common->isSetMessage('mail', 'requirement', 'sendmailByOutTimeInside')){
            return [];
        }

        if(!helper::isWorkDay(date('Y-m-d H:i:s'))){
            return true;
        }
        $this->app->loadLang('demand');

        //按需求任务发布时间计算，待发布、已发布状态下，2天后，还未反馈，则每天邮件提醒反馈即将超期（如1号为创建时间，则3号触发邮件，即跟随3号触发的定时任务触发邮件）
        $toOut = $this->lang->demand->demandOutTime['requireToOutTime'] ?? 2;
        $out   = $this->lang->demand->demandOutTime['requireOutTime'] ?? 5;
        $start = helper::getWorkDay(date('Y-m-d'),-($out - 1)) . ' 00:00:00';
        $end   = helper::getWorkDay(date('Y-m-d'),-($toOut)) . ' 23:59:59';

        $aboutToIds = $this->outTime($start, $end, 'setRequirementToOutTimeMail',false);

        //若5个工作日后还未反馈，则提醒一次反馈已超时，之后不再邮件提醒。
        $start = helper::getWorkDay(date('Y-m-d 00:00:00'), -($out)) . ' 00:00:00';
        $start = $this->getStartTime($start);
        $end   = helper::getWorkDay(date('Y-m-d 23:59:59'), -($out)) . ' 23:59:59';
        $outTimeIds = $this->outTime($start, $end, 'setRequirementOutTimeMail',true);

        return ['aboutTo' => $aboutToIds, 'outTime' => $outTimeIds];
    }

    /**
     * 发送需求任务超时提醒-外部
     *
     * @return array
     * @access public
     */
    public function sendmailByOutTimeOutSide()
    {
        $this->loadModel('common');
        //是否有发送邮件权限
        if(!$this->common->isSetMessage('mail', 'requirement', 'sendmailByOutTimeOutSide')){
            return [];
        }

        if(!helper::isWorkDay(date('Y-m-d H:i:s'))){
            return true;
        }
        $this->app->loadLang('demand');

        //邮件提醒：创建时间（【存在外部变更的】，按照最新接口变更时间）开始5个工作日后（反馈单状态还处于待反馈、待部门审核、待产创审核）开始邮件提醒即将超期，8个工作日后提醒超期。
        $toOut = $this->lang->demand->demandOutTime['requireToOut'] ?? 5;
        $out   = $this->lang->demand->demandOutTime['requireOut'] ?? 8;
        $start = helper::getWorkDay(date('Y-m-d'),-($out - 1)) . ' 00:00:00';
        $end   = helper::getWorkDay(date('Y-m-d'),-($toOut)) . ' 23:59:59';

        $aboutToIds = $this->outTimeOutside($start, $end, 'setRtToOutTimeOutsideMail',false);

        //8个工作日后提醒超期，则提醒一次反馈已超时，之后不再邮件提醒。
        $start = helper::getWorkDay(date('Y-m-d 00:00:00'), -($out)) . ' 00:00:00';
        $start = $this->getStartTime($start);
        $end   = helper::getWorkDay(date('Y-m-d 23:59:59'), -($out)) . ' 23:59:59';

        $outTimeIds = $this->outTimeOutside($start, $end, 'setRtOutTimeOutsideMail',true);

        return ['aboutTo' => $aboutToIds, 'outTime' => $outTimeIds];
    }

    /**
     * @Notes: 需求任务 内部处理
     * @Date: 2023/8/14
     * @Time: 15:49
     * @Interface outTime
     * @param $start
     * @param $end
     * @param $setMail
     * @param bool $isOut
     * @return array
     */
    public function outTime($start, $end, $setMail, $isOut = true)
    {
        $ids = [];
        $this->app->loadLang('demand');
        $data = $this->dao
            ->select('*')
            ->from(TABLE_REQUIREMENT)
            ->where('createdBy')->eq('guestcn')
            ->andWhere('status')->ne('deleteout')
            ->andWhere('feedbackStatus')->in(['tofeedback','todepartapproved'])
            ->andWhere('feekBackStartTime')->between($start, $end)
            ->andWhere('sourceRequirement')->eq(1)
            ->fetchAll('id');
        if(!empty($data)){
            /*
             * 即将超期通知：需求任务的反馈单待处理人+接收清总需求任务接口人（待处理人主送，已处理的抄送）
             * 已超期通知：需求任务的反馈单待处理人+接收清总需求任务接口人（待处理人主送，已处理的抄送）。+抄送后台可配置（王丽姣）
             */
            foreach ($data as $key => $item){
                $toList = trim($item->feedbackDealUser, ',');
                if(empty($toList)){
                    continue;
                }
                $reviewer = ['litianzi'];
                //获取已审核过的人
                $node= $this->dao->select('*')->from(TABLE_REVIEWNODE)
                    ->where('objectType')->eq('requirement')
                    ->andWhere('status')->in($this->lang->requirement->sendmailStatusList)
                    ->andWhere('objectID')->eq($item->id)
                    ->fetchALl();
                if($node)
                {
                    $nodeIds = array_column($node,'id');
                    $reviewerInfo = $this->dao->select('*')->from(TABLE_REVIEWER)->where('node')->in($nodeIds)->andWhere('`status`')->in($this->lang->requirement->sendmailStatusList)->fetchALL();
                    $reviewer = array_filter(array_unique(array_merge($reviewer,array_column($reviewerInfo,'reviewer'))));
                }
                //已超期通知：需求任务的反馈单待处理人+接收清总需求任务接口人（待处理人主送，已处理的抄送）。+抄送后台可配置（王丽姣）
                if($isOut)
                {
                    $reviewer = array_filter(array_unique(array_merge($reviewer,array_keys($this->lang->demand->outTimeList))));
                }

                $ccList = implode(',', $reviewer);

                $item->inOrout = 'in';//标识内部 用于邮件反馈期限取值
                $this->sendmailBase($item, $setMail, 'requirement', $toList, $ccList, 'requirementouttime');
                $ids[] = $key;
            }
        }

        return $ids;
    }

    /**
     * @Notes: 需求任务 外部处理
     * @Date: 2023/8/14
     * @Time: 15:49
     * @Interface outTime
     * @param $start
     * @param $end
     * @param $setMail
     * @param bool $isOut
     * @return array
     */
    public function outTimeOutside($start, $end, $setMail, $isOut = true)
    {
        $ids = [];
        $this->app->loadLang('demand');
        $data = $this->dao
            ->select('*')
            ->from(TABLE_REQUIREMENT)
            ->where('createdBy')->eq('guestcn')
            ->andWhere('status')->ne('deleteout')
            ->andWhere('feedbackStatus')->in(['tofeedback','todepartapproved','toinnovateapproved'])
            ->andWhere('feekBackStartTimeOutside')->between($start, $end)
            ->andWhere('sourceRequirement')->eq(1)
            ->fetchAll('id');
        if(!empty($data)){
            /*
             * 即将超期通知：需求任务的反馈单待处理人+接收清总需求任务接口人（待处理人主送，已处理的抄送）
             * 已超期通知：需求任务的反馈单待处理人+接收清总需求任务接口人（待处理人主送，已处理的抄送）。+抄送后台可配置（王丽姣）
             */
            foreach ($data as $key => $item){
                $toList = trim($item->feedbackDealUser, ',');
                //待反馈状态下反馈单待处理人为任务单子的待处理人
                if($item->feedbackStatus == 'tofeedback')
                {
                    $toList = trim($item->dealUser, ',');
                }
                if(empty($toList)){
                    continue;
                }
                $reviewer = ['litianzi'];
                //获取已审核过的人
                $node = $this->dao->select('*')->from(TABLE_REVIEWNODE)
                    ->where('objectType')->eq('requirement')
                    ->andWhere('status')->in($this->lang->requirement->sendmailStatusList)
                    ->andWhere('objectID')->eq($item->id)
                    ->fetchALl();
                if($node)
                {
                    $nodeIds = array_column($node,'id');
                    $reviewerInfo = $this->dao->select('*')->from(TABLE_REVIEWER)->where('node')->in($nodeIds)->andWhere('`status`')->in($this->lang->requirement->sendmailStatusList)->fetchALL();
                    $reviewer = array_filter(array_unique(array_merge($reviewer,array_column($reviewerInfo,'reviewer'))));
                }
                //已超期通知：需求任务的反馈单待处理人+接收清总需求任务接口人（待处理人主送，已处理的抄送）。+抄送后台可配置（王丽姣）
                if($isOut)
                {
                    $reviewer = array_filter(array_unique(array_merge($reviewer,array_keys($this->lang->demand->outTimeList))));
                }

                $ccList = implode(',', $reviewer);
                $item->inOrout = 'out';//标识外部 用于邮件反馈期限取值
                $this->sendmailBase($item, $setMail, 'requirement', $toList, $ccList, 'requirementouttime');
                $ids[] = $key;
            }
        }

        return $ids;
    }

    function sendmailBase($requirement, $setMail, $browseType, $toList, $ccList, $viewName, $mailTitle = false)
    {
        $this->loadModel('mail');
        $users   = $this->loadModel('user')->getPairs('noletter');

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

        //获取后台自定义配置人 反馈人所属部门
        $this->loadModel('demand');
        $deptLeadersList = $this->config->demand->deptLeadersList;
        $userParam = $requirement->feedbackBy;
        if($requirement->feedbackStatus == 'tofeedback')
        {
            $userParam = $requirement->feedbackDealUser;
        }
        $user = $this->loadModel('user')->getById($userParam);
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

        if($this->mail->isError()) error_log(join("\n", $this->mail->getError()));
    }

    /**
     * 获取超时提醒开始时间范围
     * @param $start
     * @return string
     */
    public function getStartTime($start)
    {
        do{
            $date = date('Y-m-d 00:00:00', strtotime('-1 day',strtotime($start)));

            try {
                $flag = !helper::isWorkDay($date);
            } catch (Exception $e) {
                return $start;
            }

            if($flag){
                $start = $date;
            }

        }while($flag);

        return $start;
    }
}
