<?php
class datamanagementModel extends model
{
    /**
     * 从数据获取同步数据
     * shixuyang
     * @param $infoId
     * @param $source
     * @return string|void
     */
    public function syncData($infoId ='', $source=''){
        if(empty($infoId) || empty($source)){
            return '';
        }
        if($source == 'info'){
            $infoData = $this->loadModel('info')->getByID($infoId);
        }else if($source == 'infoqz'){
            $infoData = $this->loadModel('infoqz')->getByID($infoId);
        }else{
            return '';
        }
        //新增数据
        if(!empty($infoData)){
            $datamanagement = $this->dao->select("*")->from(TABLE_DATAUSE)->where('infoId')->eq($infoId)->andWhere('source')->eq($source)->andWhere('infoCode')->eq($infoData->code)->fetch();
            //判断数据是否是新版本
            if(empty($infoData->isJinke)){
                return '';
            }
            //判断是否进入金科-如果否逻辑删除数据
            if($infoData->isJinke == 2 and !empty($datamanagement)){
                $deleteData = new stdclass();
                $deleteData->deleted = 1;
                $this->dao->update(TABLE_DATAUSE)->data($deleteData)
                    ->where('id')->eq($datamanagement->id)
                    ->exec();
                if(!dao::isError()){
                    $changes = common::createChanges($datamanagement, $deleteData);
                    $actionID = $this->loadModel('action')->create('datamanagement', $datamanagement->id, 'sync', $this->post->comment);
                    $this->action->logHistory($actionID, $changes);
                }
                return $datamanagement->code;
            }
            if($infoData->isJinke == 2){
                return '';
            }
            //如果不存在就新增
            if(empty($datamanagement)){
                $addData = new stdclass();
                $addData->infoId = $infoId;
                $addData->infoCode = $infoData->code;
                $addData->type = $infoData->type;
                $addData->isJk = $infoData->isJinke;
                $addData->desensitizeType = zget($this->lang->datamanagement->desensitizeTypeInfoToDatamanagement, $infoData->desensitizationType);
                $addData->isDeadline = $infoData->isDeadline;
                if($addData->isDeadline == 1){
                    $addData->useDeadline = '';
                }else{
                    $addData->useDeadline = date('Y-m-d H:i:s',strtotime("+23 hours 59 minutes 59 seconds", strtotime($infoData->deadline)));
                }
                $addData->isDesensitize = $infoData->isDesensitize;
                $addData->createdBy = $infoData->createdBy;
                $addData->createdDate = $infoData->createdDate;
                $addData->desc = $infoData->desc;
                $addData->reason = $infoData->reason;
                $addData->source = $source;
                if($infoData->status != 'closed' or $infoData->status != 'closing'){
                    $addData->status = zget($this->lang->datamanagement->statusInfoToDatamanagement, $infoData->status);
                }
                //待审批、未获取都不需要待处理人
               /* if($addData->isDeadline == 1 or $addData->desensitizeType == 'all'){
                    $addData->dealUser = '';
                }else{
                    $addData->dealUser = $infoData->createdBy;
                }*/

                $this->dao->insert(TABLE_DATAUSE)
                    ->data($addData)->autoCheck()
                    ->exec();
                if(!dao::isError()){
                    $datauseID = $this->dao->lastInsertId();
                    $date   = date('Y-m-d');
                    $number = $this->dao->select('count(id) c')->from(TABLE_DATAUSE)->where("date_format(createdDate,'%Y-%m-%d')")->eq($date)->andWhere('source')->eq($source)->fetch('c');
                    $codetype   = $source == 'info' ? 'UJ' : 'UQ';
                    $code   = "CFIT-$codetype-" . date('Ymd-') . sprintf('%02d', $number);
                    $this->dao->update(TABLE_DATAUSE)->set('code')->eq($code)->where('id')->eq($datauseID)->exec();

                    $actionID = $this->loadModel('action')->create('datamanagement', $datauseID, 'sync', $this->post->comment);
                    $this->loadModel('consumed')->record('datamanagement', $datauseID, 0, $this->app->user->account, '', $addData->status, array());
                    return $code;
                }
            }else{
                //更新数据
                $updateData = new stdclass();
                $updateData->infoId = $infoId;
                $updateData->infoCode = $infoData->code;
                $updateData->type = $infoData->type;
                $updateData->isJk = $infoData->isJinke;
                $updateData->desensitizeType = zget($this->lang->datamanagement->desensitizeTypeInfoToDatamanagement, $infoData->desensitizationType);
                $updateData->isDeadline = $infoData->isDeadline;
                if($updateData->isDeadline == 1){
                    $updateData->useDeadline = '';
                }else{
                    $updateData->useDeadline = date('Y-m-d H:i:s',strtotime("+23 hours 59 minutes 59 seconds", strtotime($infoData->deadline)));
                }
                $updateData->isDesensitize = $infoData->isDesensitize;
                $updateData->createdBy = $infoData->createdBy;
                $updateData->createdDate = $infoData->createdDate;
                $updateData->desc = $infoData->desc;
                $updateData->reason = $infoData->reason;
                $updateData->source = $source;
                if($infoData->status != 'closed' or $infoData->status != 'closing'){
                    $updateData->status = zget($this->lang->datamanagement->statusInfoToDatamanagement, $infoData->status);
                }
                //待审批、未获取都不需要待处理人
                /*if($updateData->isDeadline == 1 or $updateData->desensitizeType == 'all'){
                    $updateData->dealUser = '';
                }else{
                    $updateData->dealUser = $infoData->createdBy;
                }*/
                $updateData->deleted = 0;
                $this->dao->update(TABLE_DATAUSE)->data($updateData)
                    ->where('id')->eq($datamanagement->id)
                    ->exec();
                if(!dao::isError()){
                    //如果状态回到待审批，清空消息备案
                    if($updateData->status == 'toreview'){
                        $this->dao->update(TABLE_TOREAD)->set('deleted')->eq(1)
                            ->where('objectType')->eq('datamanagement')->andWhere('objectId')->eq($datamanagement->id)
                            ->exec();
                    }
                    $changes = common::createChanges($datamanagement, $updateData);
                    $actionID = $this->loadModel('action')->create('datamanagement', $datamanagement->id, 'sync', $this->post->comment);
                    $this->action->logHistory($actionID, $changes);
                    $this->loadModel('consumed')->record('datamanagement', $datamanagement->id, 0, $this->app->user->account, $datamanagement->status, $updateData->status, array());
                    return $datamanagement->code;
                }
            }
        }
    }

    /**
     * 从数据获取同步数据状态
     * shixuyang
     * @param $infoId
     * @param $source
     * @return string|void
     */
    public function syncDataStatus($infoId ='', $source='',$account=''){
        if(empty($infoId) || empty($source)){
            return '';
        }
        if($source == 'info'){
            $infoData = $this->loadModel('info')->getByID($infoId);
        }else if($source == 'infoqz'){
            $infoData = $this->loadModel('infoqz')->getByID($infoId);
        }else{
            return '';
        }
        $datamanagement = $this->dao->select("*")->from(TABLE_DATAUSE)->where('infoId')->eq($infoId)->andWhere('source')->eq($source)->andWhere('infoCode')->eq($infoData->code)->fetch();
        if(!empty($datamanagement)){
            //更新数据
            $updateData = new stdclass();
            if($infoData->status != 'closed' and $infoData->status != 'closing' and $infoData->status != 'deleted' and $infoData->status != 'fetchclose'){
                $updateData->status = zget($this->lang->datamanagement->statusInfoToDatamanagement, $infoData->status,$datamanagement->status);
            }else{
                $updateData->status = $datamanagement->status;
            }
            if($infoData->status == 'deleted'){
                $updateData->deleted = 1;
            }
            if($infoData->status == 'fetchsuccess' or $infoData->status == 'fetchfail'){
                $updateData->actualEndTime = $infoData->actualEnd;
            }
            if($updateData->status == 'gainsuccess'){
                if($infoData->isDeadline == 1 or $datamanagement->desensitizeType == 'all'){
                    $updateData->dealUser = '';
                }else{
                    $updateData->dealUser = $infoData->createdBy;
                }
            }else{
                $updateData->dealUser = '';
            }
            $this->dao->update(TABLE_DATAUSE)->data($updateData)
                ->where('id')->eq($datamanagement->id)
                ->exec();
            if(!dao::isError()){
                $changes = common::createChanges($datamanagement, $updateData);
                if($updateData->status != $datamanagement->status){
                    $accountNew = empty($account)? $this->app->user->account:$account;
                    $actionID = $this->loadModel('action')->create('datamanagement', $datamanagement->id, 'syncstatus', $this->post->comment,'',$accountNew);
                    $this->action->logHistory($actionID, $changes);
                    $this->loadModel('consumed')->record('datamanagement', $datamanagement->id, 0, $accountNew, $datamanagement->status, $updateData->status, array());
                    //如果状态回到待审批，清空消息备案
                    if($updateData->status == 'toreview'){
                        $this->dao->update(TABLE_TOREAD)->set('deleted')->eq(1)
                            ->where('objectType')->eq('datamanagement')->andWhere('objectId')->eq($datamanagement->id)
                            ->exec();
                    }
                    //向待读列表增加数据-审批成功
                    if($datamanagement->status == 'toreview' and $updateData->status == 'togain'){
                        $this->addToRead($datamanagement->id, 'reviewed',$actionID);
                    }else if($datamanagement->status == 'togain' and $updateData->status == 'gainsuccess'){
                        $this->addToRead($datamanagement->id, 'gained',$actionID);
                    }
                }
                return $datamanagement->code;
            }
        }
    }

    /**
     * 新建待读数据
     * @param $id
     * @return void
     */
    public function addToRead($id,$type,$actionID){
        $addData = new stdclass();
        $addData->objectType = 'datamanagement';
        $addData->objectId = $id;
        $addData->status = 'toread';
        $addData->messageType = $type;
        $testDepartReviewerList = array_keys($this->lang->datamanagement->testDepartReviewer);
        //如果存在待读数据逻辑删除并保存最新的
        $toreadList = $this->dao->select('*')->from(TABLE_TOREAD)->where('objectType')->eq('datamanagement')->andWhere('objectId')->eq($id)
            ->andWhere('messageType')->eq($type)->andWhere('deleted')->ne(1)->fetchAll('id');
        foreach ($toreadList as $toread){
            $this->dao->update(TABLE_TOREAD)->set('deleted')->eq(1)->where('id')->eq($toread->id)
                ->exec();
        }
        foreach ($testDepartReviewerList as $testDepartReviewer){
            $addData->dealUser = $testDepartReviewer;
            $this->dao->insert(TABLE_TOREAD)
                ->data($addData)
                ->exec();
        }
        $this->sendmailToread($id,$actionID);
    }

    /**
     * Project: chengfangjinke
     * Desc: 获取列表
     * liuyuhan
     */
     public function getList($browseType, $queryID, $orderBy, $pager = null)
     {
         $dataQuery = '';
         if ($browseType == 'bysearch') {
             $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : '';
             if ($query) {
                 $this->session->set('datamanagementQuery', $query->sql);
                 $this->session->set('datamanagementForm', $query->form);
             }

             if ($this->session->datamanagementQuery == false) $this->session->set('datamanagementQuery', ' 1 = 1');

             $dataQuery = $this->session->datamanagementQuery;

             //若为长期使用，需修改查询sql
             //若查询【使用日期至】
             if (strpos($dataQuery, 'useDeadline') ){
                 if (strpos($dataQuery, "`useDeadline` = '长期'")) {
                     $dataQuery = str_replace('useDeadline', 'isDeadline', $dataQuery);
                     $dataQuery = str_replace($this->lang->datamanagement->longTerm, $this->lang->datamanagement->longTermUseFlag, $dataQuery);
                 }else{
                     $addQuery = " `isDeadline` = 2 AND `useDeadline`";
                     $dataQuery =  str_replace("`useDeadline`", $addQuery, $dataQuery);
                 }
             }
             //若查询【延期日期】
             if (strpos($dataQuery, 'delayDeadline') ){
                 if (strpos($dataQuery, "`delayDeadline` = '长期'")) {
                     $dataQuery = str_replace('长期', '0000-00-00 00:00:00', $dataQuery);
                 }else{
                     $addTempQuery = " `isDeadline` = 2 AND `delayDeadline`";
                     $dataQuery =  str_replace("`delayDeadline`", $addTempQuery, $dataQuery);
                 }
             }

             //金信数据无【是否脱敏】字段
             if (strpos($dataQuery, 'isDesensitize')) {
                 $tempAdd = " `source` = 'infoqz' AND `isDesensitize`";
                 $dataQuery =  str_replace("`isDesensitize`", $tempAdd, $dataQuery);
             }
         }
         if (strstr($orderBy,'useDeadline')){
             $temp = strstr($orderBy,'desc') ? 'isDeadline_asc,' : 'isDeadline_desc,';
             $orderBy = $temp.$orderBy;
         }
         //数据获取描述字段与mysql-desc关键字重名，需要转义
         if(!empty($orderBy)){
             $orderByList = explode("_", $orderBy);
             $orderByList[0] = "`".$orderByList[0]."`";
             $orderBy = implode("_", $orderByList);
         }
        
         $datas = $this->dao->select('*')->from(TABLE_DATAUSE)
             ->where('deleted')->eq('0')
             ->beginIF($browseType != 'all' and $browseType != 'bysearch')->andWhere('status')->eq($browseType)->fi()
             ->beginIF($browseType == 'bysearch')->andWhere($dataQuery)->fi()
             ->orderBy($orderBy)
             ->page($pager)
             ->fetchAll('id');
         //用于导出数据构建查询
         $datamanagementExportQuery = $this->dao->sqlobj->select('*')->from(TABLE_DATAUSE)
             ->where('deleted')->eq('0')
             ->beginIF($browseType != 'all' and $browseType != 'bysearch')->andWhere('status')->eq($browseType)->fi()
             ->beginIF($browseType == 'bysearch')->andWhere($dataQuery)->fi();
         $this->session->set('datamanagementExportQuery', $datamanagementExportQuery->sql);

         //获取清总/金信数据获取单单号
         foreach ($datas as $data){
//             $infoCode = '';
//             if ((!is_null($data->source))&&($data->source == 'info')){
//                 $infoCode = $this->dao->select('code')->from(TABLE_INFO)
//                     ->where('id')->eq($data->infoId)->fetch();
//
//             }elseif((!is_null($data->source))&&($data->source == 'infoqz')){
//                 $infoCode = $this->dao->select('code')->from(TABLE_INFO_QZ)
//                     ->where('id')->eq($data->infoId)->fetch();
//             }
//             $data->infoCode= empty($infoCode->code)?$infoCode:$infoCode->code;

             //增加消息通知
             $toreadList = $this->dao->select('*')->from(TABLE_TOREAD)->where('objectType')->eq('datamanagement')->andWhere('objectId')->eq($data->id)->andWhere('status')->eq('toread')
                 ->andWhere('dealUser')->eq($this->app->user->account)->andWhere('deleted')->ne(1)->fetchAll('id');
             $toreadNum = 0;
             $reviewedBoolean = false;
             $gainedBoolean = false;
             $destroyedBoolean = false;
             foreach ($toreadList as $toread){
                 if($toread->messageType == 'reviewed'){
                     if(!$reviewedBoolean){
                         $reviewedBoolean = true;
                         $toreadNum = $toreadNum + 1;
                     }
                     $data->toreadreviewed = $toread;
                 }else if($toread->messageType == 'gained'){
                     if(!$gainedBoolean){
                         $gainedBoolean = true;
                         $toreadNum = $toreadNum + 1;
                     }
                     $data->toreadgained = $toread;
                 }else if($toread->messageType == 'destroyed'){
                     if(!$destroyedBoolean){
                         $destroyedBoolean = true;
                         $toreadNum = $toreadNum + 1;
                     }
                     $data->toreaddestroyed = $toread;
                 }
             }
             $data->toreadNum = $toreadNum;
//             $data->infoCode= empty($infoCode->code)?'':$infoCode->code;
         }
         $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'datamanagement', $browseType != 'bysearch');
         return $datas;
     }

    /**
     * Project: chengfangjinke
     * Desc: 构建搜索框
     * liuyuhan
     */
    public function buildSearchForm($queryID, $actionURL)
    {
        $this->config->datamanagement->search['actionURL'] = $actionURL;
        $this->config->datamanagement->search['queryID']   = $queryID;

        $this->loadModel('search')->setSearchParams($this->config->datamanagement->search);
    }

    /**
     * 获取单个数据使用
     * @param $id
     * @return void
     */
     public function getById($id){
         $datamanagement = $this->dao->findByID($id)->from(TABLE_DATAUSE)->fetch();
         $cs = $this->dao->select('*')->from(TABLE_CONSUMED)->where('objectType')->eq('datamanagement') //状态流转 工作量
         ->andWhere('objectID')->eq($id)
             ->andWhere('deleted')->ne(1)
             ->orderBy('id_asc')
             ->fetchAll();
         $datamanagement->consumed = $cs;
         $reviewer = $this->loadModel('review')->getReviewer('datamanagement', $datamanagement->id, $datamanagement->changeVersion);
         $datamanagement->reviewer = $reviewer ? ',' . $reviewer . ','  : '';

         //增加消息通知
         $toreadList = $this->dao->select('*')->from(TABLE_TOREAD)->where('objectType')->eq('datamanagement')->andWhere('objectId')->eq($id)->andWhere('status')->eq('toread')
             ->andWhere('dealUser')->eq($this->app->user->account)->andWhere('deleted')->ne(1)->fetchAll('id');
         $toreadNum = 0;
         $reviewedBoolean = false;
         $gainedBoolean = false;
         $destroyedBoolean = false;
         foreach ($toreadList as $toread){
             if($toread->messageType == 'reviewed'){
                 if(!$reviewedBoolean){
                     $reviewedBoolean = true;
                     $toreadNum = $toreadNum + 1;
                 }
                 $datamanagement->toreadreviewed = $toread;
             }else if($toread->messageType == 'gained'){
                 if(!$gainedBoolean){
                     $gainedBoolean = true;
                     $toreadNum = $toreadNum + 1;
                 }
                 $datamanagement->toreadgained = $toread;
             }else if($toread->messageType == 'destroyed'){
                 if(!$destroyedBoolean){
                     $destroyedBoolean = true;
                     $toreadNum = $toreadNum + 1;
                 }
                 $datamanagement->toreaddestroyed = $toread;
             }
         }
         $datamanagement->toreadNum = $toreadNum;

         return $datamanagement;
     }

    /**
     * 定时提醒
     * shixuyang
     * @return void
     */
     public function timeRemind(){
        //查找状态为获取成功-数据脱敏类型为部分脱敏或未脱敏数据
         $datamanagementList = $this->dao->select('*')->from(TABLE_DATAUSE)->where('status')->eq('gainsuccess')
            ->andWhere('desensitizeType')->in('part,not')
            ->andWhere('isDeadline')->ne(1)
            ->andWhere('deleted')->ne(1)
            ->andWhere('reviewStage',true)->ne('1')->orWhere('reviewStage')->isNull()->markRight(1)
            ->fetchAll();
        foreach ($datamanagementList as $datamanagement){
            if($this->timeDiff($datamanagement) <= 2){
                $this->sendmailRemind($datamanagement);
            }
        }
     }

    /**
     * 判断两个日期相距多少个工作日
     * @param $datamanagement
     * @return int
     */
     public function timeDiff($datamanagement){
         $workdays = explode(",", "1,1,1,1,1,0,0");
         $datamanagementSecond = strtotime($datamanagement->useDeadline) > strtotime($datamanagement->delayDeadline) ? strtotime($datamanagement->useDeadline):strtotime($datamanagement->delayDeadline);
         $nowSecond = strtotime(helper::now());
         $days = 0;
         while($nowSecond < $datamanagementSecond){
             $day_of_week = date("N", $nowSecond) - 1;
             if($workdays[$day_of_week] == 1){
                 $days++;
             }
             $nowSecond = strtotime("+1 day", $nowSecond);
         }
         return $days;
     }

    /**
     * Project: chengfangjinke
     * Desc: 发送提醒销毁邮件
     * shixuyang
     */
    public function sendmailRemind($datamanagement)
    {
        $this->loadModel('mail');

        /* 获取后台通知中配置的邮件发信。*/
        $this->app->loadLang('custommail');
        $mailConf   = isset($this->config->global->setDatamanagementMail) ? $this->config->global->setDatamanagementMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf   = json_decode($mailConf);


        $mailTitle = vsprintf($mailConf->mailTitle, array('待办','数据使用','待申请销毁','处理'));
        //判断邮件类型
        $mailType = 'remind';
        /* Get mail content. */
        $oldcwd     = getcwd();
        $modulePath = $this->app->getModulePath($appName = '', 'datamanagement');
        $viewFile   = $modulePath . 'view/sendmail.html.php';
        chdir($modulePath . 'view');

        if(file_exists($modulePath . 'ext/view/sendmail.html.php'))
        {
            $viewFile = $modulePath . 'ext/view/sendmail.html.php';
            chdir($modulePath . 'ext/view');
        }
        //$serverIp = $_SERVER['SERVER_NAME'];

        ob_start();
        include $viewFile;
        foreach(glob($modulePath . 'ext/view/sendmail.*.html.hook.php') as $hookFile) include $hookFile;
        $mailContent = ob_get_contents();
        ob_end_clean();
        chdir($oldcwd);



        $toList = $datamanagement->createdBy;
        $ccList = '';
        /* 处理邮件标题。*/
        $subject = $mailTitle;

        /* Send emails. */
        $this->mail->send($toList, $subject, $mailContent, $ccList);
        if($this->mail->isError()) {
            error_log(join("\n", $this->mail->getError()));
        }
    }

    /**
     * Project: chengfangjinke
     * Desc: 发送消息备案邮件
     * shixuyang
     */
    public function sendmailToread($id, $actionID)
    {
        $this->loadModel('mail');
        //邮件显示详细信息
        $datamanagement = $this->getById($id);


        /* 获取后台通知中配置的邮件发信。*/
        $this->app->loadLang('custommail');
        $mailConf   = isset($this->config->global->setDatamanagementMail) ? $this->config->global->setDatamanagementMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf   = json_decode($mailConf);
        $browseType = 'datamanagement';

        /* 处理邮件发信的标题和日期。*/
        $bestDeal = $this->dao->select('*')->from(TABLE_CONSUMED)->where('objectType')->eq('datamanagement')
            ->andWhere('objectID')->eq($id)
            ->orderBy('id_desc')
            ->fetch();
        $bestDate=empty($bestDeal) ? '' : $bestDeal->createdDate;
//        $bestDate  = empty($bestDeal) ? '' : substr($bestDeal->createdDate, 0, 10);

        /* Get action info. */
        $action          = $this->loadModel('action')->getById($actionID);
        //$history         = $this->action->getHistory($actionID);
        //$action->history = isset($history[$actionID]) ? $history[$actionID] : array();

        //备案通知
        $mailType = 'syncstatus';
        $testDepartReviewerList = array_keys($this->lang->datamanagement->testDepartReviewer);
        $datamanagement->toList = implode(",", $testDepartReviewerList);
        $datamanagement->ccList = '';
        $userDeptId = $this->loadModel('user')->getUserDeptIds($datamanagement->createdBy);
        $depts = $this->loadModel('dept')->getOptionMenu();
        $users = $this->loadModel('user')->getPairs('noletter|noclosed');
        if($datamanagement->source == 'info'){
            $infoData = $this->loadModel('info')->getByID($datamanagement->infoId);
        }else{
            $infoData = $this->loadModel('infoqz')->getByID($datamanagement->infoId);
        }
        $apps = $this->loadModel('application')->getapplicationNameCodePairs();
        foreach(explode(',', $infoData->app) as $app)
        {
            if(!$app) continue;
            $as[] = zget($apps, $app);
        }
        $app = implode('/', $as);
        $datamanagement->message = zget($depts,$userDeptId[0]).zget($users, $datamanagement->createdBy).'已提交'.$app.'（业务系统）数据获取申请！数据获取备案信息如下：';
        if($datamanagement->status == 'togain'){
            $mailTitle =  vsprintf($mailConf->mailTitle, array('通知','数据使用','备案通知审批通过','查看'));
        }else if($datamanagement->status == 'gainsuccess'){
            $mailTitle =  vsprintf($mailConf->mailTitle, array('通知','数据使用','备案通知获取成功','查看'));
        }else if($datamanagement->status == 'destroyed'){
            $mailTitle =  vsprintf($mailConf->mailTitle, array('通知','数据使用','备案通知销毁成功','查看'));
        }


        /* Get mail content. */
        $oldcwd     = getcwd();
        $modulePath = $this->app->getModulePath($appName = '', 'datamanagement');
        $viewFile   = $modulePath . 'view/sendmail.html.php';
        chdir($modulePath . 'view');

        if(file_exists($modulePath . 'ext/view/sendmail.html.php'))
        {
            $viewFile = $modulePath . 'ext/view/sendmail.html.php';
            chdir($modulePath . 'ext/view');
        }
        //$serverIp = $_SERVER['SERVER_NAME'];

        ob_start();
        include $viewFile;
        foreach(glob($modulePath . 'ext/view/sendmail.*.html.hook.php') as $hookFile) include $hookFile;
        $mailContent = ob_get_contents();
        ob_end_clean();
        chdir($oldcwd);

        $toList = $datamanagement->toList;
        $ccList = $datamanagement->ccList;
        /* 处理邮件标题。*/
        $subject = $mailTitle;

        /* Send emails. */
        $this->mail->send($toList, $subject, $mailContent, $ccList);
        if($this->mail->isError()) error_log(join("\n", $this->mail->getError()));
    }

    /**
     * Project: chengfangjinke
     * Desc: 发送销毁执行邮件
     * shixuyang
     */
    public function sendmailDestroy($id, $actionID)
    {
        $this->loadModel('mail');
        //邮件显示详细信息
        $datamanagement = $this->getById($id);


        /* 获取后台通知中配置的邮件发信。*/
        $this->app->loadLang('custommail');
        $mailConf   = isset($this->config->global->setDatamanagementMail) ? $this->config->global->setDatamanagementMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf   = json_decode($mailConf);
        $browseType = 'datamanagement';

        /* 处理邮件发信的标题和日期。*/
        $bestDeal = $this->dao->select('*')->from(TABLE_CONSUMED)->where('objectType')->eq('datamanagement')
            ->andWhere('objectID')->eq($id)
            ->orderBy('id_desc')
            ->fetch();
        $bestDate=empty($bestDeal) ? '' : $bestDeal->createdDate;
//        $bestDate  = empty($bestDeal) ? '' : substr($bestDeal->createdDate, 0, 10);

        /* Get action info. */
        $action          = $this->loadModel('action')->getById($actionID);
        //$history         = $this->action->getHistory($actionID);
        //$action->history = isset($history[$actionID]) ? $history[$actionID] : array();

        $users = $this->loadModel('user')->getPairs('noletter|noclosed');

        //备案通知
        $mailType = 'destroy';
        $datamanagement->toList = $datamanagement->dealUser;
        $datamanagement->ccList = '';
        $mailTitle =  vsprintf($mailConf->mailTitle, array('待办','数据使用','待处理','处理'));

        /* Get mail content. */
        $oldcwd     = getcwd();
        $modulePath = $this->app->getModulePath($appName = '', 'datamanagement');
        $viewFile   = $modulePath . 'view/sendmail.html.php';
        chdir($modulePath . 'view');

        if(file_exists($modulePath . 'ext/view/sendmail.html.php'))
        {
            $viewFile = $modulePath . 'ext/view/sendmail.html.php';
            chdir($modulePath . 'ext/view');
        }
        //$serverIp = $_SERVER['SERVER_NAME'];

        ob_start();
        include $viewFile;
        foreach(glob($modulePath . 'ext/view/sendmail.*.html.hook.php') as $hookFile) include $hookFile;
        $mailContent = ob_get_contents();
        ob_end_clean();
        chdir($oldcwd);

        $toList = $datamanagement->toList;
        $ccList = $datamanagement->ccList;
        /* 处理邮件标题。*/
        $subject = $mailTitle;

        /* Send emails. */
        $this->mail->send($toList, $subject, $mailContent, $ccList);
        if($this->mail->isError()) error_log(join("\n", $this->mail->getError()));
    }

    /**
     * Project: chengfangjinke
     * Desc: 发送邮件
     * shixuyang
     */
    public function sendmail($id, $actionID)
    {
        $this->loadModel('mail');
        //邮件显示详细信息
        $datamanagement = $this->getById($id);

        $users = $this->loadModel('user')->getPairs('noletter|noclosed');


        /* 获取后台通知中配置的邮件发信。*/
        $this->app->loadLang('custommail');
        $mailConf   = isset($this->config->global->setDatamanagementMail) ? $this->config->global->setDatamanagementMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf   = json_decode($mailConf);
        $browseType = 'datamanagement';

        /* 处理邮件发信的标题和日期。*/
        $bestDeal = $this->dao->select('*')->from(TABLE_CONSUMED)->where('objectType')->eq('datamanagement')
            ->andWhere('objectID')->eq($id)
            ->orderBy('id_desc')
            ->fetch();
        $bestDate=empty($bestDeal) ? '' : $bestDeal->createdDate;
//        $bestDate  = empty($bestDeal) ? '' : substr($bestDeal->createdDate, 0, 10);

        /* Get action info. */
        $action          = $this->loadModel('action')->getById($actionID);
        //$history         = $this->action->getHistory($actionID);
        //$action->history = isset($history[$actionID]) ? $history[$actionID] : array();

        $mailTitle = $this->buildMail($datamanagement, $mailConf, $action);
        $mailType = $datamanagement->mailType;


        /* Get mail content. */
        $oldcwd     = getcwd();
        $modulePath = $this->app->getModulePath($appName = '', 'datamanagement');
        $viewFile   = $modulePath . 'view/sendmail.html.php';
        chdir($modulePath . 'view');

        if(file_exists($modulePath . 'ext/view/sendmail.html.php'))
        {
            $viewFile = $modulePath . 'ext/view/sendmail.html.php';
            chdir($modulePath . 'ext/view');
        }
        //$serverIp = $_SERVER['SERVER_NAME'];

        ob_start();
        include $viewFile;
        foreach(glob($modulePath . 'ext/view/sendmail.*.html.hook.php') as $hookFile) include $hookFile;
        $mailContent = ob_get_contents();
        ob_end_clean();
        chdir($oldcwd);

        $toList = $datamanagement->toList;
        $ccList = $datamanagement->ccList;
        /* 处理邮件标题。*/
        $subject = $mailTitle;

        /* Send emails. */
        $this->mail->send($toList, $subject, $mailContent, $ccList);
        if($this->mail->isError()) error_log(join("\n", $this->mail->getError()));
    }

    public function buildMail(&$datamanagement, $mailConf, $action){
        //销毁成功
        if($datamanagement->status == 'destroyed' and $action->action == 'destroyreview'){
            $datamanagement->mailType = 'destroyed';
            $datamanagement->toList = $datamanagement->createdBy;
            $datamanagement->ccList = '';
            return vsprintf($mailConf->mailTitle, array('通知','数据使用','销毁成功','处理'));
        }else if($datamanagement->status == 'destroyreviewing' and $action->action == 'destroyexecution'){
            //销毁复核
            $datamanagement->mailType = 'destroyreviewing';
            $datamanagement->toList = $datamanagement->dealUser;
            $datamanagement->ccList = '';
            return vsprintf($mailConf->mailTitle, array('待办','数据使用','待处理','处理'));
        }else if($action->action == 'delayed'){
            //销毁申请通过/退回
            $datamanagement->mailType = 'delayed';
            $datamanagement->toList = $datamanagement->createdBy;
            $datamanagement->ccList = '';
            $extraObj = $this->getReviewerExtraInfosByEmail($datamanagement->id, 1, $datamanagement->changeVersion);
            $datamanagement->delayReason = $extraObj->delayReason;
            $datamanagement->reviewOpinion = $extraObj->reviewOpinion;
            if(strpos($action->comment, '不通过') !== false){
                return vsprintf($mailConf->mailTitle, array('通知','数据使用','延期申请退回','处理'));
            }else{
                return vsprintf($mailConf->mailTitle, array('通知','数据使用','延期申请通过','处理'));
            }
        }else if($action->action == 'delay'){
            //延期待审批
            $datamanagement->mailType = 'delay';
            $datamanagement->toList = $datamanagement->dealUser;
            $datamanagement->ccList = '';
            $extraObj = $this->getReviewerExtraInfos($datamanagement->id, $datamanagement->reviewStage, $datamanagement->changeVersion);
            $datamanagement->delayReason = $extraObj->delayReason;
            return vsprintf($mailConf->mailTitle, array('待办','数据使用','待审批','处理'));
        }else if($action->action == 'destroy'){
            //销毁待审批
            $datamanagement->mailType = 'destroy';
            $datamanagement->toList = $datamanagement->dealUser;
            $datamanagement->ccList = '';
            $extraObj = $this->getReviewerExtraInfos($datamanagement->id, $datamanagement->reviewStage, $datamanagement->changeVersion);
            $datamanagement->destroyedReason = $extraObj->destroyReason;
            return vsprintf($mailConf->mailTitle, array('待办','数据使用','待审批','处理'));
        }else if($action->action == 'destroyreviewed'){
            //销毁申请通过/退回
            $datamanagement->mailType = 'destroyreviewed';
            $datamanagement->ccList = '';
            $datamanagement->toList = $datamanagement->createdBy;
            $extraObj = $this->getReviewerExtraInfosByEmail($datamanagement->id, 2, $datamanagement->changeVersion);
            $datamanagement->destroyedReason = $extraObj->destroyReason;
            if(strpos($action->comment, '不通过') !== false){
                $datamanagement->rejectReason = $extraObj->rejectReason;
                $datamanagement->isResult = 'reject';
                return vsprintf($mailConf->mailTitle, array('通知','数据使用','销毁申请退回','处理'));
            }else{
                $datamanagement->reviewOpinion = $extraObj->reviewOpinion;
                $datamanagement->isResult = 'pass';
                return vsprintf($mailConf->mailTitle, array('通知','数据使用','销毁申请通过','处理'));
            }
        }
    }

    /**
     * 填写数据销毁执行
     * shixuyang
     * @param $id
     * @return void
     */
    public function destroyexecution($id){
        $oldDataManagement = $this->getById($id);

        if($oldDataManagement->status != 'destroying'){
            dao::$errors[] = $this->lang->datamanagement->statuserror;
        }

        if(!in_array($this->app->user->account,explode(',',$oldDataManagement->dealUser))){
            dao::$errors[] = $this->lang->datamanagement->dealusererror;
        }

        //工作量验证
       /* $consumed = $_POST['consumed'];
        $reg = '/^(([1-9][0-9]*)|(([0]\.\d{1}|[1-9][0-9]*\.\d{1})))$/';
        if(empty($consumed))
        {
            dao::$errors['consumed'] = sprintf($this->lang->datamanagement->emptyObject, $this->lang->datamanagement->consumed);
        }else if(!is_numeric($consumed)) {
            dao::$errors['consumed'] = sprintf($this->lang->datamanagement->noNumeric, $this->lang->datamanagement->consumed);
        }else if(!preg_match($reg, $consumed))
        {
            dao::$errors['consumed'] = sprintf($this->lang->datamanagement->consumedError, $this->lang->datamanagement->consumed);
        }*/

        $this->tryError();

        $updateData = new stdclass();
        $updateData->status = 'destroyreviewing';
        $updateData->dealUser = $oldDataManagement->reviewedDeal;
        $updateData->destroyedBy = $this->app->user->account;
        $updateData->destroyedDate = helper::now();

        $this->dao->update(TABLE_DATAUSE)->data($updateData)
            ->where('id')->eq($id)
            ->exec();
        if(!dao::isError()){
            $changes = common::createChanges($oldDataManagement, $updateData);
            $actionID = $this->loadModel('action')->create('datamanagement', $id, 'destroyexecution', $this->post->comment);
            $this->action->logHistory($actionID, $changes);
            $this->loadModel('consumed')->record('datamanagement', $id, '0', $this->app->user->account, $oldDataManagement->status, $updateData->status, array());
        }
    }

    /**
     * 填写数据销毁复核
     * shixuyang
     * @param $id
     * @return void
     */
    public function destroyreview($id){
        $oldDataManagement = $this->getById($id);

        if($oldDataManagement->status != 'destroyreviewing'){
            dao::$errors[] = $this->lang->datamanagement->statuserror;
        }

        if(!in_array($this->app->user->account,explode(',',$oldDataManagement->dealUser))){
            dao::$errors[] = $this->lang->datamanagement->dealusererror;
        }

        //工作量验证
       /* $consumed = $_POST['consumed'];
        $reg = '/^(([1-9][0-9]*)|(([0]\.\d{1}|[1-9][0-9]*\.\d{1})))$/';
        if(empty($consumed))
        {
            dao::$errors['consumed'] = sprintf($this->lang->datamanagement->emptyObject, $this->lang->datamanagement->consumed);
        }else if(!is_numeric($consumed)) {
            dao::$errors['consumed'] = sprintf($this->lang->datamanagement->noNumeric, $this->lang->datamanagement->consumed);
        }else if(!preg_match($reg, $consumed))
        {
            dao::$errors['consumed'] = sprintf($this->lang->datamanagement->consumedError, $this->lang->datamanagement->consumed);
        }*/

        $this->tryError();

        $updateData = new stdclass();
        $updateData->status = 'destroyed';
        $updateData->dealUser = '';
        $updateData->reviewedBy = $this->app->user->account;
        $updateData->reviewedDate = helper::now();

        $this->dao->update(TABLE_DATAUSE)->data($updateData)
            ->where('id')->eq($id)
            ->exec();
        if(!dao::isError()){
            $changes = common::createChanges($oldDataManagement, $updateData);
            $actionID = $this->loadModel('action')->create('datamanagement', $id, 'destroyreview', $this->post->comment);
            $this->action->logHistory($actionID, $changes);
            $this->loadModel('consumed')->record('datamanagement', $id, '0', $this->app->user->account, $oldDataManagement->status, $updateData->status, array());
            //增加待读数据
            $this->addToRead($id, 'destroyed',$actionID);
        }
    }

    /**
     * 已读操作
     * shixuyang
     * @param $id
     * @return void
     */
    public function readmessage($id){
        $oldDataManagement = $this->getById($id);
        $typeList = array();
        $filingNoticeList = $_POST['filingNotice'];

        if(empty($filingNoticeList)){
            dao::$errors['filingNotice'] = sprintf($this->lang->datamanagement->emptyObject, $this->lang->datamanagement->filingNotice);
        }

        $this->tryError();

        foreach ($filingNoticeList as $filingNotice){
           if($filingNotice == 'reviewed'){
                array_push($typeList, 'reviewed');
            }else if($filingNotice == 'gained'){
                array_push($typeList, 'gained');
            }else if($filingNotice == 'destroyed'){
                array_push($typeList, 'destroyed');
            }
        }
        $type = implode(",", $typeList);


        $toreadList = $this->dao->select('*')->from(TABLE_TOREAD)->where('objectType')->eq('datamanagement')->andWhere('objectId')->eq($id)
            ->andWhere('messageType')->in($type)->andWhere('status')->eq('toread')->andWhere('dealUser')->eq($this->app->user->account)->andWhere('deleted')->ne(1)->fetchAll();
        if(empty($toreadList)){
            dao::$errors[''] = $this->lang->datamanagement->toreaderror;
        }
        $this->tryError();

        $idArray = array();
        foreach ($toreadList as $toread){
            array_push($idArray, $toread->id);
        }
        $updateData = new stdclass();
        $updateData->status = 'readed';
        $updateData->comment = $this->post->comment;
        $updateData->dealDate = helper::now();

        $this->dao->update(TABLE_TOREAD)->data($updateData)
            ->where('id')->in(implode(",", $idArray))
            ->exec();
        if(!dao::isError()){
            $actionID = $this->loadModel('action')->create('datamanagement', $id, 'readmessage', $this->post->comment);
        }
    }

    /**
     * 尝试报错 或需要rollback
     */
    public function tryError($rollBack = 0)
    {
        if(dao::isError())
        {
            if($rollBack == 1){
                $this->dao->rollBack();
            }
            $response['result']  = 'fail';
            $response['message'] = dao::getError();
            $this->send($response);
        }
    }

    /**
     * 直接输出data数据
     * @access public
     */
    private function send($data)
    {
        die(json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    /**
     * 按钮权限控制
     * @param
     * @param $action
     * @return bool
     */
    public static function isClickable($datamanagement, $action)
    {
        global $app;
        $action = strtolower($action);
        if($action == 'destroyexecution') return ($datamanagement->status == 'destroying' or $datamanagement->status == 'destroyreviewing') and (in_array($app->user->account,explode(',',$datamanagement->dealUser))) ;
        //if($action == 'destroyreview') return ($datamanagement->status == 'destroyreviewing') and (in_array($app->user->account,explode(',',$datamanagement->dealUser))) ;
        //数据延期申请需【状态】为获取成功，不为长期使用，且 脱敏类型不为全部脱敏，操作人为发起人
        if($action == 'delay') return ($datamanagement->status == 'gainsuccess') and ($datamanagement->isDeadline == '2') and ($datamanagement->desensitizeType != 'all')
                and($app->user->account == $datamanagement->createdBy) and (is_null($datamanagement->reviewStage) or $datamanagement->reviewStage=='');
        if($action == 'review') return(in_array($datamanagement->status, array('gainsuccess','todestroy'))) and (in_array($datamanagement->reviewStage, array('1','2')))
            and (in_array($app->user->account,explode(',',$datamanagement->dealUser)));
        if($action == 'destroy') return(in_array($datamanagement->status, array('gainsuccess'))) and ($datamanagement->isDeadline == '2') and ($datamanagement->desensitizeType != 'all')
            and ($app->user->account == $datamanagement->createdBy);
        if($action == 'readmessage') return (!empty($datamanagement->toreadreviewed) or !empty($datamanagement->toreadgained) or !empty($datamanagement->toreaddestroyed));

        return true;
    }

    /**
     * Project: chengfangjinke
     * Desc: 申请数据使用延期
     * liuyuhan
     */
    public function delay($datamanagement){

        //校验数据
        if($datamanagement->status != 'gainsuccess'){
            dao::$errors[] = '数据使用状态不为【获取成功】，请刷新后重试';
        }
        //$this->checkConsumed();
        $this->checkParamsNotEmpty($_POST, $this->config->datamanagement->delay->requiredFields);
        if ($this->post->useDeadline == 'custom'){
            if (!$this->post->deadline) {
                dao::$errors['useDeadline'] = sprintf($this->lang->datamanagement->emptyObject, $this->lang->datamanagement->useDeadline);
            }else if($this->post->deadline <= substr($datamanagement->useDeadline,0, 10)){
                dao::$errors['useDeadline'] = sprintf($this->lang->datamanagement->deadlineError, $this->lang->datamanagement->useDeadline);
            }else if($this->post->deadline <= helper::today()){
                dao::$errors['useDeadline'] = sprintf($this->lang->datamanagement->deadlineTodayError, $this->lang->datamanagement->useDeadline);
            }
        }
        $postData = fixer::input('post')
            ->stripTags($this->config->datamanagement->editor->delay['id'], $this->config->allowedTags)
            ->get();
        if(!dao::isError()) {
            //富文本框处理
            $this->loadModel('file')->processImgURL($postData, $this->config->datamanagement->editor->delay['id'], $this->post->uid);
            // 部门负责人
            $myDept = $this->loadModel('dept')->getByID($this->app->user->dept);
            $mydeptManager = explode(',', trim($myDept->manager, ','));
            //暂存延期原因和延期日期（longterm为长期）
            $reviewerExtParams = new stdClass();
            $reviewerExtParams->delayReason = $this->post->delayReason;
            $reviewerExtParams->submitComment = $this->post->comment;
            if ($this->post->useDeadline == 'custom') $reviewerExtParams->delayDeadline = date('Y-m-d H:i:s',strtotime("+23 hours 59 minutes 59 seconds", strtotime($this->post->deadline)));
            if ($this->post->useDeadline == 'longterm') $reviewerExtParams->delayDeadline = 'longterm';
            $extra = new stdClass();
            $extra->extra = json_encode($reviewerExtParams);
            $extraObj = array('reviewerExtParams'=> $extra);

            $updateData = new stdclass();
            $updateData->dealUser = implode(',',$mydeptManager);
            $updateData->reviewStage = 1;
            $updateData->changeVersion = (is_null($datamanagement->changeVersion) or $datamanagement->changeVersion=='') ? 1 : ($datamanagement->changeVersion + 1);

            //新增各审批节点
            $this->loadModel('review');
            $this->review->addNode('datamanagement', $datamanagement->id, $updateData->changeVersion, $mydeptManager, true, 'pending', $updateData->reviewStage, $extraObj);
            $this->tryError();
            //更新数据库
            $this->dao->update(TABLE_DATAUSE)->data($updateData)->where('id')->eq($datamanagement->id)->exec();
            $this->tryError();

            if (!dao::isError()) {
                //状态流转
                $this->loadModel('consumed')->record('datamanagement', $datamanagement->id, '0', $this->app->user->account, $datamanagement->status, $datamanagement->status, array());
            }

            //获取新的数据
            $datamanagementNew = $this->getByID($datamanagement->id);
            $this->tryError();
            return common::createChanges($datamanagement, $datamanagementNew);
        }
        return false;
    }

    /**
     * Project: chengfangjinke
     * Desc:工作量输入验证
     */
    public function checkConsumed(){
        //工作量验证,输入小数点后保留1位小数
        $consumed = $_POST['consumed'];
        $reg = '/^(([1-9][0-9]*)|(([0]\.\d{1}|[1-9][0-9]*\.\d{1})))$/';
        if(empty($consumed))
        {
            dao::$errors['consumed'] = sprintf($this->lang->datamanagement->emptyObject, $this->lang->datamanagement->consumed);
        }else if(!is_numeric($consumed)) {
            dao::$errors['consumed'] = sprintf($this->lang->datamanagement->noNumeric, $this->lang->datamanagement->consumed);
        }else if(!preg_match($reg, $consumed))
        {
            dao::$errors['consumed'] = sprintf($this->lang->datamanagement->consumedError, $this->lang->datamanagement->consumed);
        }
    }

    /**
     * Project: chengfangjinke
     * Desc: 检查必填项是否为空
     * liuyuhan
     */
    private function checkParamsNotEmpty($data, $fields)
    {
        if(!is_array($data)) {
            if (!is_object($data)) $data = (object)$data;
            $data = (array)$data;
        }
        $fieldArray = explode(',', str_replace(' ', '', $fields));
        foreach ($fieldArray as $item)
        {
            if(is_null($data[$item]) || $data[$item] == '' || $data[$item] == ' ' ){
                $itemName = $this->lang->datamanagement->$item ?? $item;
                dao::$errors[$item] =  sprintf($this->lang->datamanagement->emptyObject, $itemName);
            }
        }
    }

    /**
     * Project: chengfangjinke
     * Desc: 数据使用延期审批
     * liuyuhan
     */
    public function delayreview($datamanagementID){

        $datamanagement = $this ->getById($datamanagementID);
        //校验数据
       // $this->checkConsumed();
        $this->checkParamsNotEmpty($_POST, $this->config->datamanagement->delayreview->requiredFields);
        $this->tryError();
        if(!dao::isError()) {
            //判断当前状态和审核中传入的状态是否一致，以防有同组中其他人修改
            $res = $this->checkAllowReview($datamanagement, $this->post->changeVersion, $this->post->reviewStage, $this->app->user->account);
        }
        if(!$res['result']){
            dao::$errors['statusError'] = $res['message'];
        }
        $this->tryError();
        $postData = fixer::input('post')
            ->stripTags($this->config->datamanagement->editor->review['id'], $this->config->allowedTags)
            ->get();
        //富文本框处理
        $this->loadModel('file')->processImgURL($postData, $this->config->datamanagement->editor->review['id'], $this->post->uid);
        //保存审核意见加到$extraObj中
        $extraObj = $this->getReviewerExtraInfos($datamanagementID, $datamanagement->reviewStage, $datamanagement->changeVersion);
        $extraObj->reviewOpinion = $this->post->reviewOpinion;
        $is_all_check_pass = false;
        $result = $this->loadModel('review')->check('datamanagement', $datamanagementID, $datamanagement->changeVersion, $this->post->result, $this->post->comment, '', $extraObj, $is_all_check_pass);
        if($result == 'pass')
        {
            $updateData = new stdclass();
            $updateData->delayReason = $extraObj->delayReason;
            $updateData->delayedBy = $datamanagement->createdBy;
            $updateData->delayedDate = helper::now();
            if ($extraObj->delayDeadline=='longterm'){
                //若长期使用，则无待处理人
                $updateData->isDeadline = $this->lang->datamanagement->longTermUseFlag;
                $updateData->delayDeadline = '0000-00-00 00:00:00'; //delayDeadline全0，表示延长为长期
                $updateData->dealUser = null;
            }else{
                $updateData->dealUser = $datamanagement->createdBy;
                $updateData->delayDeadline = $extraObj->delayDeadline;
                $updateData->useDeadline = $extraObj->delayDeadline;
            }
            $updateData->reviewStage = null;
            //更新数据库
            $this->dao->update(TABLE_DATAUSE)->data($updateData)->where('id')->eq($datamanagementID)->exec();
            $this->loadModel('consumed')->record('datamanagement', $datamanagementID, '0', $this->app->user->account, $datamanagement->status, $datamanagement->status, array());
            $historyComment = (is_null(trim(str_replace('&nbsp;','',$this->post->comment))) or (trim(str_replace('&nbsp;','',$this->post->comment))=='')) ?  "审批结论：通过" : "审批结论：通过<br>本次操作备注：".$this->post->comment;
            $actionID = $this->loadModel('action')->create('datamanagement', $datamanagementID, 'delayed', $historyComment);
        }else if ($result == 'reject'){
            $this->dao->update(TABLE_DATAUSE)->set('reviewStage')->eq('')->set('dealUser')->eq($datamanagement->createdBy)->where('id')->eq($datamanagementID)->exec();
            $this->loadModel('consumed')->record('datamanagement', $datamanagementID, '0', $this->app->user->account, $datamanagement->status, $datamanagement->status, array());
            $historyComment = (is_null(trim(str_replace('&nbsp;','',$this->post->comment))) or (trim(str_replace('&nbsp;','',$this->post->comment))=='')) ?  "审批结论：不通过" : "审批结论：不通过<br>本次操作备注：".$this->post->comment;
            $actionID = $this->loadModel('action')->create('datamanagement', $datamanagementID, 'delayed', $historyComment);

        }


    }

    /**
     * 检查用户审批
     * @param $datamanagement
     * @param $version
     * @param $reviewStage
     * @param $userAccount
     * @return array
     */
    public function checkAllowReview($datamanagement, $version = 1,  $reviewStage = 0, $userAccount = '')
    {
        $res = array(
            'result'  => false,
            'message' => '',
        );
        if(!$datamanagement){
            $res['message'] = $this->lang->common->errorParamId;
            return $res;
        }
        if(!$userAccount){
            $res['message'] = $this->lang->common->errorParamUser;
            return $res;
        }
        //审核节点已经经过
        if(($version != $datamanagement->changeVersion) || ($reviewStage != $datamanagement->reviewStage)){
//            $reviewerInfo = $this->loadModel('review')->getReviewedUserInfo('datamanagement', $datamanagement->id, $version, $reviewStage);
            $message = $this->lang->datamanagement->dealError;
//            if($reviewerInfo){
//                $message = str_replace('%', $reviewerInfo->realname, $this->lang->review->statusError);
//            }
            $res['message'] = $message;
            return $res;
        }
        //获得当前的的审核人信息
        $reviews =  $this->loadModel('review')->getReviewer('datamanagement', $datamanagement->id, $datamanagement->changeVersion, $datamanagement->reviewStage);
        if(!$reviews){
            $res['message'] = $this->lang->review->reviewEnd;
            return $res;
        }
        $reviews = explode(',', $reviews);
        if(!in_array($userAccount, $reviews)){
            $res['message'] = $this->lang->review->statusUserError;
            return $res;
        }
        $res['result'] = true;
        return  $res;
    }

    /**
     * Project: chengfangjinke
     * Desc: 获取reviewer表中记录的extra中的具体信息
     * 包括 延期原因和使用期限
     * liuyuhan
     */
    public function getReviewerExtraInfos($datamanagementID, $reviewStage, $changeVersion){

        $node = $this->dao->select('*')->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq('datamanagement')
            ->andWhere('objectID')->eq($datamanagementID)
            ->andWhere('version')->eq($changeVersion)
            ->andWhere('stage')->eq($reviewStage)
            ->orderBy('stage,id')
            ->fetch();
        if(!$node) return '';
        $reviewers = $this->dao->select('*')->from(TABLE_REVIEWER)
            ->where('node')->eq($node->id)
            ->andWhere('status')->eq('pending')
            ->orderBy('id')
            ->fetchAll();

        if(!$reviewers) return '';
        $extraObj = $reviewers[0]->extra;
        return json_decode($extraObj);
    }

    /**
     * Project: chengfangjinke
     * Desc: 获取reviewer表中记录的extra中的具体信息-发送邮件
     * 包括 延期原因和使用期限
     * shixuyang
     */
    public function getReviewerExtraInfosByEmail($datamanagementID, $reviewStage, $changeVersion){

        $node = $this->dao->select('*')->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq('datamanagement')
            ->andWhere('objectID')->eq($datamanagementID)
            ->andWhere('version')->eq($changeVersion)
            ->andWhere('stage')->eq($reviewStage)
            ->orderBy('stage,id')
            ->fetch();
        if(!$node) return '';
        $reviewers = $this->dao->select('*')->from(TABLE_REVIEWER)
            ->where('node')->eq($node->id)
            ->orderBy('id')
            ->fetchAll();

        if(!$reviewers) return '';
        $extraObj = $reviewers[0]->extra;
        return json_decode($extraObj);
    }

    /**
     * Project: chengfangjinke
     * Desc:数据销毁申请
     * liuyuhan
     */
    public function destroy($datamanagementID){
        $datamanagement = $this->getById($datamanagementID);
        //校验数据
        if($datamanagement->status != 'gainsuccess'){
            dao::$errors[] = '数据使用状态不为【获取成功】，请刷新后重试';
        }
        //$this->checkConsumed();
        $this->checkParamsNotEmpty($_POST, $this->config->datamanagement->destroy->requiredFields);
        $this->tryError();
        //校验是否存在数据申请延期，若有，则终止数据延期申请
        if ($datamanagement->reviewStage == '1'){
           $this->setNodeDelayStopped($datamanagement);
        }
        $this->tryError();
        $postData = fixer::input('post')
            ->stripTags($this->config->datamanagement->editor->destroy['id'], $this->config->allowedTags)
            ->get();
        //富文本框处理
        $this->loadModel('file')->processImgURL($postData, $this->config->datamanagement->editor->destroy['id'], $this->post->uid);
        // 部门负责人
        $myDept = $this->loadModel('dept')->getByID($this->app->user->dept);
        $mydeptManager = explode(',', trim($myDept->manager, ','));

        //暂存销毁原因
        $reviewerExtParams = new stdClass();
        $reviewerExtParams->destroyReason = $this->post->destroyReason;
        $reviewerExtParams->submitComment = $this->post->comment;
        $extra = new stdClass();
        $extra->extra = json_encode($reviewerExtParams);
        $extraObj = array('reviewerExtParams'=> $extra);

        $updateData = new stdclass();
        $updateData->status = 'todestroy';
        $updateData->dealUser = implode(',',$mydeptManager);
        $updateData->reviewStage = 2;
        $updateData->changeVersion = (is_null($datamanagement->changeVersion) or $datamanagement->changeVersion=='') ? 1 : ($datamanagement->changeVersion + 1);

        //新增各审批节点
        $this->loadModel('review')->addNode('datamanagement', $datamanagementID, $updateData->changeVersion, $mydeptManager, true, 'pending', $updateData->reviewStage, $extraObj);
        $this->tryError();

        //更新数据库
        $this->dao->update(TABLE_DATAUSE)->data($updateData)->where('id')->eq($datamanagement->id)->exec();
        $this->tryError();

        //获取新的数据
        $datamanagementNew = $this->getByID($datamanagement->id);
        if (!dao::isError()) {
            //状态流转
            $this->loadModel('consumed')->record('datamanagement', $datamanagement->id, '0', $this->app->user->account, $datamanagement->status, $datamanagementNew->status, array());
        }
        $this->tryError();
        return common::createChanges($datamanagement, $datamanagementNew);

    }

    /**
     * Project: chengfangjinke
     * Desc: 延期审核节点进行【延期终止】处理
     * liuyuhan
     */
    public function setNodeDelayStopped($datamanagement){
        //审核意见置空，并加到$extraObj中
        $extraObj = $this->getReviewerExtraInfos($datamanagement->id, $datamanagement->reviewStage, $datamanagement->changeVersion);
        $extraObj->reviewOpinion = '';
        $node = $this->dao->select('*')->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq('datamanagement')
            ->andWhere('objectID')->eq($datamanagement->id)
            ->andWhere('version')->eq($datamanagement->changeVersion)
            ->andWhere('status')->eq('pending')
            ->orderBy('stage,id')
            ->fetch();
        if(!$node) return '';
        //修改当前节点下的所有审核人的状态为【延期终止】，审批意见为【空】，本次操作备注为【空】，操作时间【记录当前申请销毁的时间】
        $this->dao->update(TABLE_REVIEWER)
            ->set('status')->eq('delaystopped')
            ->set('comment')->eq('')
            ->set('extra')->eq(json_encode($extraObj))
            ->set('reviewTime')->eq(helper::now())
            ->where('node')->eq($node->id)
            ->andWhere('status')->eq('pending') //当前状态
            ->exec();
        //修改节点审核状态
        $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('delaystopped')
            ->where('id')->eq($node->id)
            ->exec();

    }

    /**
     * Project: chengfangjinke
     * Desc: 数据销毁审批
     * liuyuhan
     */
    public function destroyreviewed($datamanagementID){

        $datamanagement = $this ->getById($datamanagementID);
        //校验数据
        //$this->checkConsumed();
        $requiredFields = $this->post->result == 'pass' ? $this->config->datamanagement->destroyreview->pass->requiredFields :  $this->config->datamanagement->destroyreview->reject->requiredFields;
        $this->checkParamsNotEmpty($_POST, $requiredFields);
        $this->tryError();
        if($this->post->result == 'pass') {
            if ($this->post->executor == $this->post->checker) {
                //执行人和审核人不能是同一个人
                dao::$errors['repeatSelectError'] = $this->lang->datamanagement->repeatSelectError;
            }
        }
        $this->tryError();
        if(!dao::isError()) {
            //判断当前状态和审核中传入的状态是否一致，以防有同组中其他人修改
            $res = $this->checkAllowReview($datamanagement, $this->post->changeVersion, $this->post->reviewStage, $this->app->user->account);
        }
        if(!$res['result']){
            dao::$errors['statusError'] = $res['message'];
        }
        $this->tryError();
        $postData = fixer::input('post')
            ->stripTags($this->config->datamanagement->editor->review['id'], $this->config->allowedTags)
            ->get();
        //富文本框处理
        $this->loadModel('file')->processImgURL($postData, $this->config->datamanagement->editor->review['id'], $this->post->uid);
        //若【不通过】保存退回原因到$extraObj中
        $extraObj = $this->getReviewerExtraInfos($datamanagementID, $datamanagement->reviewStage, $datamanagement->changeVersion);
        if ($this->post->result == 'reject'){
            $extraObj->rejectReason = $this->post->rejectReason;
        }
        $is_all_check_pass = false;
        $result = $this->loadModel('review')->check('datamanagement', $datamanagementID, $datamanagement->changeVersion, $this->post->result, $this->post->comment, '', $extraObj, $is_all_check_pass);

        if($result == 'pass')
        {
            $updateData = new stdclass();
            $updateData->status='destroying';
            $updateData->destroyedDeal = $this->post->executor;
            $updateData->reviewedDeal = $this->post->checker;
            $updateData->dealUser = $this->post->executor;
            $updateData->destroyedReason = $extraObj->destroyReason;
            $updateData->reviewStage = null;
            //更新数据库
            $this->dao->update(TABLE_DATAUSE)->data($updateData)->where('id')->eq($datamanagementID)->exec();
            $this->loadModel('consumed')->record('datamanagement', $datamanagementID, '0', $this->app->user->account, $datamanagement->status, 'destroying', array());
            $historyComment = (is_null(trim(str_replace('&nbsp;','',$this->post->comment))) or (trim(str_replace('&nbsp;','',$this->post->comment))=='')) ?  "审批结论：通过" : "审批结论：通过<br>本次操作备注：".$this->post->comment;
            $actionID = $this->loadModel('action')->create('datamanagement', $datamanagementID, 'destroyreviewed', $historyComment);
            $this->sendmailDestroy($datamanagementID, $actionID);
        }else if ($result == 'reject'){
            $updateData = new stdclass();
            $updateData->status='gainsuccess';
            $updateData->dealUser = $datamanagement->createdBy;
            $updateData->reviewStage = null;
            //更新数据库
            $this->dao->update(TABLE_DATAUSE)->data($updateData)->where('id')->eq($datamanagementID)->exec();
            $this->loadModel('consumed')->record('datamanagement', $datamanagementID, '0', $this->app->user->account, $datamanagement->status, 'gainsuccess', array());
            $historyComment = (is_null(trim(str_replace('&nbsp;','',$this->post->comment))) or (trim(str_replace('&nbsp;','',$this->post->comment))=='')) ?  "审批结论：不通过" : "审批结论：不通过<br>本次操作备注：".$this->post->comment;
            $actionID = $this->loadModel('action')->create('datamanagement', $datamanagementID, 'destroyreviewed', $historyComment);
        }
    }
    /**
     * 获取延期申请评审记录
     * @param $objectType
     * @param $objectID
     * @param $version
     * @return array
     */
    public function getDelayNodes($objectID)
    {
        $users = $this->loadModel('user')->getPairs('noletter|noclosed');
        $nodes = $this->dao->select('*')->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq('datamanagement')
            ->andWhere('objectID')->eq($objectID)
            ->andWhere('stage')->eq(1)
            ->orderBy('version,id')->fetchAll('id');
        $reviewers = $this->dao->select('*')->from(TABLE_REVIEWER)->where('node')->in(array_keys($nodes))->fetchAll();
        $map = array();
        foreach($reviewers as $r)
        {
            if(!isset($map[$r->node]))
            {
                $info = new stdClass();
                $info->reviewers = '';
                $info->delayComment = '';
                $info->reviewComment = '';
                $info->delayReason = '';
                $info->delayDeadline = '';
                $info->reviewResult = '';
                $info->reviewOpinion = '';
                $info->reviewDate = '';
                $map[$r->node] = $info;
            }

            //延期原因-延期日期-提交备注
            $delayObject = json_decode($r->extra);
            $map[$r->node]->delayReason = $delayObject->delayReason;
            $map[$r->node]->delayDeadline = $delayObject->delayDeadline == 'longterm'? '长期':$delayObject->delayDeadline;
            if(!empty($delayObject->submitComment)){
                $map[$r->node]->delayComment = $delayObject->submitComment;
            }

            //审批人
            $map[$r->node]->reviewers = trim($map[$r->node]->reviewers.','.zget($users, $r->reviewer), ",");
            if($r->status == 'pass' or $r->status == 'reject'){
                $map[$r->node]->reviewResult = zget($this->lang->datamanagement->confirmList , $r->status)."（".zget($users, $r->reviewer)."）";
                //审批意见
                if(!empty($delayObject->reviewOpinion)){
                    $map[$r->node]->reviewOpinion = $delayObject->reviewOpinion;
                }
                //审批操作备注
                $map[$r->node]->reviewComment = $r->comment;
                //操作日期
                $map[$r->node]->reviewDate = $r->reviewTime;
            }else if($r->status == 'delaystopped'){
                $map[$r->node]->reviewResult = zget($this->lang->datamanagement->confirmResultList , $r->status);
                //审批意见
                if(!empty($delayObject->reviewOpinion)){
                    $map[$r->node]->reviewOpinion = $delayObject->reviewOpinion;
                }
                //审批操作备注
                $map[$r->node]->reviewComment = $r->comment;
                //操作日期
                $map[$r->node]->reviewDate = $r->reviewTime;
            }

        }

        $data = [];
        foreach($nodes as $key => $node)
        {
            $node->delayReason     = isset($map[$node->id]) ? $map[$node->id]->delayReason : '';
            $node->delayDeadline = isset($map[$node->id]) ? $map[$node->id]->delayDeadline : '';
            $node->delayComment = isset($map[$node->id]) ? $map[$node->id]->delayComment : '';
            $node->reviewers = isset($map[$node->id]) ? $map[$node->id]->reviewers : '';
            $node->reviewResult = isset($map[$node->id]) ? $map[$node->id]->reviewResult : '';
            $node->reviewOpinion = isset($map[$node->id]) ? $map[$node->id]->reviewOpinion : '';
            $node->reviewComment = isset($map[$node->id]) ? $map[$node->id]->reviewComment : '';
            $node->reviewDate = isset($map[$node->id]) ? $map[$node->id]->reviewDate : '';
            $node->delayApplicant     = zget($users, $node->createdBy);
            $data[] = $node;
        }

        return $data;
    }

    /**
     * 获取销毁评审记录
     * @param $objectType
     * @param $objectID
     * @param $version
     * @return array
     */
    public function getDestroyNodes($objectID)
    {
        $users = $this->loadModel('user')->getPairs('noletter|noclosed');
        $nodes = $this->dao->select('*')->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq('datamanagement')
            ->andWhere('objectID')->eq($objectID)
            ->andWhere('stage')->eq(2)
            ->orderBy('version,id')->fetchAll('id');
        $reviewers = $this->dao->select('*')->from(TABLE_REVIEWER)->where('node')->in(array_keys($nodes))->fetchAll();
        $map = array();
        foreach($reviewers as $r)
        {
            if(!isset($map[$r->node]))
            {
                $info = new stdClass();
                $info->reviewers = '';
                $info->destroyComment = '';
                $info->reviewComment = '';
                $info->destroyReason = '';
                $info->reviewResult = '';
                $info->reviewOpinion = '';
                $info->reviewDate = '';
                $map[$r->node] = $info;
            }

            //销毁原因-销毁日期-提交备注
            $destroyObject = json_decode($r->extra);
            $map[$r->node]->destroyReason = $destroyObject->destroyReason;
            if(!empty($destroyObject->submitComment)){
                $map[$r->node]->destroyComment = $destroyObject->submitComment;
            }

            //审批人
            $map[$r->node]->reviewers = trim($map[$r->node]->reviewers.','.zget($users, $r->reviewer), ",");
            if($r->status == 'pass' or $r->status == 'reject'){
                $map[$r->node]->reviewResult = zget($this->lang->datamanagement->confirmList , $r->status)."（".zget($users, $r->reviewer)."）";
                //审批意见
                if($r->status == 'reject' and !empty($destroyObject->rejectReason)){
                    $map[$r->node]->reviewOpinion = $destroyObject->rejectReason;
                }else if($r->status == 'pass'){
                    $datamanage = $this->getById($objectID);
                    $map[$r->node]->reviewOpinion = "销毁执行人：".zget($users, $datamanage->destroyedDeal)."<br>销毁复核人：".zget($users, $datamanage->reviewedDeal);
                }
                //审批操作备注
                $map[$r->node]->reviewComment = $r->comment;
                //操作日期
                $map[$r->node]->reviewDate = $r->reviewTime;
            }

        }

        $data = [];
        foreach($nodes as $key => $node)
        {
            $node->destroyReason     = isset($map[$node->id]) ? $map[$node->id]->destroyReason : '';
            $node->destroyComment = isset($map[$node->id]) ? $map[$node->id]->destroyComment : '';
            $node->reviewers = isset($map[$node->id]) ? $map[$node->id]->reviewers : '';
            $node->reviewResult = isset($map[$node->id]) ? $map[$node->id]->reviewResult : '';
            $node->reviewOpinion = isset($map[$node->id]) ? $map[$node->id]->reviewOpinion : '';
            $node->reviewComment = isset($map[$node->id]) ? $map[$node->id]->reviewComment : '';
            $node->reviewDate = isset($map[$node->id]) ? $map[$node->id]->reviewDate : '';
            $node->destroyApplicant     = zget($users, $node->createdBy);
            $data[] = $node;
        }

        return $data;
    }

}