<?php

class weeklyreportinModel extends model
{
    /**
     * 获取内部周报列表
     * @param $browseType
     * @param $pager
     * @param $orderBy
     * @return mixed
     */
    public function getList($browseType, $pager, $orderBy = 'id_desc')
    {
        $qa = $this->loadModel('weeklyreport')->getUserQADept($this->app->user->account);
        /*if($qa['isogQA'] == 0 && empty($qa['depts'])){
            return [];
        }*/

        $objDao = $this->dao->select('*')->from(TABLE_PROJECTWEEKLYREPORT)
            ->where('deleted')->eq('0')
            ->beginIF('all' != $browseType)->andWhere('projectStage')->eq($browseType)->fi();
//            ->beginIF(!empty($qa['depts']))->andwhere()->markleft(1);
//        if(!empty($qa['depts'])){
//            foreach ($qa['depts'] as $key => $dept){
//                if($key > 0){
//                    $objDao->orwhere(" FIND_IN_SET('{$dept->id}',`devDept`) ");
//                }else{
//                    $objDao->where(" FIND_IN_SET('{$dept->id}',`devDept`) ");
//                }
//            }
//        }
//        $projectweeklyreports = $objDao->markright(1)->fi()
           $projectweeklyreports = $objDao->orderBy($orderBy)
            ->page($pager)
            ->fetchAll();
        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'secondorder', 'bysearch' != $browseType);

        return $projectweeklyreports;
    }

/*    public function getByID($outreportID)
    {
        $report = $this->dao->select('*')->from(TABLE_OUTSIDEPROJECTWEEKLYREPORT)
            ->where('id')->eq($outreportID)
            ->andwhere('deleted')->eq(0)
            ->fetch();
        if ($report) {
            $report->outmediuListInfo = json_decode(base64_decode($report->outmediuListInfo));
            $report->outmileListInfo  = json_decode(base64_decode($report->outmileListInfo));
        }

        return $report;
    }*/

    /**
     * 周报确认
     * @param $weekNum
     * @return string|true
     */
    public function confirm($weekNum)
    {

        if(!$weekNum){
            dao::$errors[] = "周数错误";
            return false;
        }

        $weeklyReportData = $this->dao
            ->select('id,outPlanId,reportStartDate,reportEndDate')
            ->from(TABLE_PROJECTWEEKLYREPORT)
            ->where('weeknum')->eq($weekNum)
            ->andWhere('produceStatus')->eq(0)
            ->andWhere('deleted')->eq(0)
            ->fetchAll();

        if (empty($weeklyReportData)) {
            return true;
        }

        $arr = $this->dao
            ->select('id')
            ->from(TABLE_OUTSIDEPROJECTWEEKLYREPORT_QUEUE)
            ->where('weeknum')->eq($weekNum)
            ->fetch();

        $this->dao->begin();  //开启事务
        //如果(外部)项目/任务周报中没有该【周序号】则创建
        if (empty($arr)) {
            $data = [
                'outplanID'          => 0,
                'weeknum'            => $weekNum,
                'outreportStartDate' => $weeklyReportData[0]->reportStartDate,
                'outreportEndDate'   => $weeklyReportData[0]->reportEndDate,
                'createTime'         => date('Y-m-d H:i:s'),
            ];
            $ret = $this->dao->insert(TABLE_OUTSIDEPROJECTWEEKLYREPORT_QUEUE)->data($data)->exec();
            if (!$ret) {
                $this->dao->rollBack();

                return dao::$errors[] = '操作失败';
            }
        }

        $ret = $this->dao
            ->update(TABLE_PROJECTWEEKLYREPORT)
            ->set('produceStatus')->eq(1)
            ->where('id')->in(array_column($weeklyReportData, 'id'))
            ->exec();
        if (!$ret) {
            $this->dao->rollBack();

            return dao::$errors[] = '操作失败';
        }

        $this->dao->commit();

        foreach ($weeklyReportData as $item) {
            $this->loadModel('action')->create('weeklyreport', $item->id, 'confirm', $this->post->comment);
        }

        return true;
    }

    /**
     * Project: chengfangjinke
     * Desc: 发送邮件
     * shixuyang
     */
    public function sendmail()
    {
        $this->loadModel('mail');
        $this->loadModel('project');
        //邮件显示详细信息
        $users = $this->loadModel('user')->getPairs('noletter|noclosed');

        // 获取后台通知中配置的邮件发信。
        $this->app->loadLang('custommail');
        $mailConf   = $this->config->global->setWeeklyreportinMail ?? '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf   = json_decode($mailConf);
        $browseType = 'weeklyreportin';

//        $bestDate  = empty($bestDeal) ? '' : substr($bestDeal->createdDate, 0, 10);
        $mailTitle = vsprintf($mailConf->mailTitle, $mailConf->variables);

        // Get mail content.
        $oldcwd     = getcwd();
        $modulePath = $this->app->getModulePath($appName = '', 'weeklyreportin');
        $viewFile   = $modulePath . 'view/sendmail.html.php';
        chdir($modulePath . 'view');

        if (file_exists($modulePath . 'ext/view/sendmail.html.php')) {
            $viewFile = $modulePath . 'ext/view/sendmail.html.php';
            chdir($modulePath . 'ext/view');
        }
        //$serverIp = $_SERVER['SERVER_NAME'];

        ob_start();
        include $viewFile;
        foreach (glob($modulePath . 'ext/view/sendmail.*.html.hook.php') as $hookFile) {
            include $hookFile;
        }
        $mailContent = ob_get_contents();
        ob_end_clean();
        chdir($oldcwd);

        $toList = array();
        foreach ($this->lang->project->pushWeeklyreportQingZong as $key => $value) {
            array_push($toList, $key);
        }
        $toList = trim(implode(',', $toList), ',');
        $ccList = '';

        // 处理邮件标题。
        $subject = $mailTitle;

        // Send emails.
        $this->mail->send($toList, $subject, $mailContent, $ccList);
        if ($this->mail->isError()) {
            error_log(implode("\n", $this->mail->getError()));
        }
    }
}
