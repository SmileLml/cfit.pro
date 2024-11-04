<?php
class monitorserviceModel extends model
{
    public function pushOverTime(){
        $this->app->loadLang('sectransfer');
        $overDataList = array();
        foreach ($this->config->monitorservice->module as $key=>$value){
            //查询处于带同步状态的单号
            $dataList = $this->dao->select('*')->from('`' . $this->config->db->prefix . $key.'`')->where($value['key'])->eq($value['value'])->andWhere('createdDate')->gt('2024-01-01 00:00:00')
                ->fetchAll('id');
            foreach ($dataList as $dataId=>$data){
                $consumedInfo = $this->loadModel('consumed')->getCreatedDate($key, $dataId, '', $value['value']);
                if($consumedInfo){
                    if(strtotime(helper::now()) - strtotime($consumedInfo->createdDate) >= 10*60 and strtotime(helper::now()) - strtotime($consumedInfo->createdDate) < 30*60){
                        $overData = array();
                        $overData['name'] = $value['name'];
                        $filed = $value['field'];
                        $overData['id'] = $data->$filed;
                        array_push($overDataList, $overData);
                    }
                }
            }
        }
        $outwarddelievyOverDataList = $this->getOutwarddelivery();
        $overDataList = array_merge($overDataList, $outwarddelievyOverDataList);

        foreach ($this->config->monitorservice->failModule as $key=>$value){
            $dataList = $this->dao->select('*')->from('`' . $this->config->db->prefix . $key.'`')->where($value['key'])->eq($value['value'])->andWhere('createdDate')->gt('2024-01-01 00:00:00')
                ->fetchAll('id');
            foreach ($dataList as $dataId=>$data){
                if($key == 'testingrequest'){
                    $outwardDelivery =  $this->dao->select('*')->from(TABLE_OUTWARDDELIVERY)->where('isNewTestingRequest')->ge(1)->andwhere('testingRequestId')->eq($dataId)->fetch();
                    $consumedInfo = $this->loadModel('consumed')->getCreatedDate('outwarddelivery',$outwardDelivery->id, '', $value['value']);
                }else if($key == 'productenroll'){
                    $outwardDelivery =  $this->dao->select('*')->from(TABLE_OUTWARDDELIVERY)->where('isNewProductEnroll')->eq(1)->andwhere('productEnrollId')->eq($dataId)->fetch();
                    $consumedInfo = $this->loadModel('consumed')->getCreatedDate('outwarddelivery',$outwardDelivery->id, '', $value['value']);
                }else if($key == 'modifycncc'){
                    $outwardDelivery =  $this->dao->select('*')->from(TABLE_OUTWARDDELIVERY)->where('isNewModifycncc')->eq(1)->andwhere('modifycnccId')->eq($dataId)->fetch();
                    $consumedInfo = $this->loadModel('consumed')->getCreatedDate('outwarddelivery',$outwardDelivery->id, '', $value['value']);
                }else {
                    $consumedInfo = $this->loadModel('consumed')->getCreatedDate($key, $dataId, '', $value['value']);
                }
                if(strtotime(helper::now()) - strtotime($consumedInfo->createdDate) < 30*60){
                    $overData = array();
                    $overData['name'] = $value['name'];
                    $filed = $value['field'];
                    $overData['id'] = $data->$filed;
                    array_push($overDataList, $overData);
                }
            }
        }
        if(!empty($overDataList)){
            $this->sendmail($overDataList);
        }
    }

    //对外交付不适用于上面的查询规则
    public function getOutwarddelivery(){
        $overDataList = array();
        //查询测试申请单
        $unPushedTestingRequestIds = $this->dao->select('*')->from(TABLE_TESTINGREQUEST)->where('status')->eq('waitqingzong')->andwhere('pushStatus')->notin([1,-1])->andwhere('pushFailTimes')->le(10)->andWhere('createdDate')->gt('2024-01-01 00:00:00')->fetchALl('id');
        if(!empty($unPushedTestingRequestIds)){
            //查询对应的交付单
            $outwardDeliveryArray =  $this->dao->select('*')->from(TABLE_OUTWARDDELIVERY)->where('isNewTestingRequest')->ge(1)->andwhere('testingRequestId')->in(array_keys($unPushedTestingRequestIds))->fetchALl();
            foreach ($outwardDeliveryArray as $outwardDelivery){
                $testingRequestId = $outwardDelivery->testingRequestId;
                $testingRequest =  $unPushedTestingRequestIds[$testingRequestId];
                $overDataList = $this->getOverdata($overDataList, 'outwarddelivery', $outwardDelivery->id, 'withexternalapproval', '清总-测试申请', $testingRequest->code);
            }
        }

        //查询产品登记
        $unPushedProductEnrollIds = $this->dao->select('id, pushFailTimes, code, pushStatus')->from(TABLE_PRODUCTENROLL)->where('status')->eq('waitqingzong')->andwhere('pushStatus')->notin([1,-1])->andwhere('pushFailTimes')->le(10)->andWhere('createdDate')->gt('2024-01-01 00:00:00')->fetchALl('id');  //选取没推送成功的产品登记
        if(!empty($unPushedProductEnrollIds)){
            //取产品登记所关联的测试申请单
            $outwardDeliveryProductArray =  $this->dao->select('id, testingRequestId, productEnrollId, code, version, reviewStage,`release`')->from(TABLE_OUTWARDDELIVERY)->where('isNewProductEnroll')->eq(1)->andwhere('productEnrollId')->in(array_keys($unPushedProductEnrollIds))->fetchALl();
            foreach ($outwardDeliveryProductArray as $outwardDelivery)
            {
                $producterollId = $outwardDelivery->productEnrollId;
                $producteroll = $unPushedProductEnrollIds[$producterollId];
                if(empty($outwardDelivery->testingRequestId)) {
                    $overDataList = $this->getOverdata($overDataList, 'outwarddelivery', $outwardDelivery->id, 'withexternalapproval', '清总-产品登记', $producteroll->code);
                }else{
                    $passedTestingRequestId = $this->dao->select('id')->from(TABLE_TESTINGREQUEST)->where('cardStatus')->eq(1)->andWhere('id')->eq($outwardDelivery->testingRequestId)->fetchALl('id');
                    if($passedTestingRequestId){
                        $overDataList = $this->getOverdata($overDataList, 'outwarddelivery', $outwardDelivery->id, 'withexternalapproval', '清总-产品登记', $producteroll->code);
                    }
                }
            }
        }

        //查询生产变更单
        //选取没推送成功的生产变更
        $unPushedModifycnccIds  = $this->dao->select('id,pushFailTimes')->from(TABLE_MODIFYCNCC)->where('status')->eq('waitqingzong')->andwhere('pushStatus')->notin([1,-1])->andwhere('pushFailTimes')->le(10)->andWhere('createdDate')->gt('2024-01-01 00:00:00')->fetchALl('id');
        if(!empty($unPushedModifycnccIds)){
            $checkList              = [];
            //取本对外交付关联的 测试申请和产品登记
            $outwardDeliveryModifycnccArray   =  $this->dao->select('id, testingRequestId, productEnrollId, modifycnccId, version, reviewStage,`release`')->from(TABLE_OUTWARDDELIVERY)->where('isNewModifycncc')->eq(1)->andwhere('modifycnccId')->in(array_keys($unPushedModifycnccIds))->fetchALl();
            foreach ($outwardDeliveryModifycnccArray as $outwardDelivery)
            {
                $modifycnccId = $outwardDelivery->modifycnccId;
                $modifycncc = $unPushedModifycnccIds[$modifycnccId];
                if(empty($outwardDelivery->testingRequestId) && empty($outwardDelivery->productEnrollId)) {
                    $overDataList = $this->getOverdata($overDataList, 'outwarddelivery', $outwardDelivery->id, 'withexternalapproval', '清总-生产变更', $modifycncc->code);
                    continue;
                }
                $checkList[$outwardDelivery->modifycnccId]['testingRequestPassed'] = 0;
                if($outwardDelivery->testingRequestId){
                    //查看是否通过
                    $passedTestingRequestId = $this->dao->select('id')->from(TABLE_TESTINGREQUEST)->where('cardStatus')->eq(1)->andWhere('id')->eq($outwardDelivery->testingRequestId)->fetch('id');
                    //如果通过标记该生产变更的测试申请通过了
                    if($passedTestingRequestId) $checkList[$outwardDelivery->modifycnccId]['testingRequestPassed'] = 1; //该生产变更需要检查测试申请
                    //如果没有产品等级 直接通过
                    if(empty($outwardDelivery->productEnrollId) && $passedTestingRequestId){
                        $overDataList = $this->getOverdata($overDataList, 'outwarddelivery', $outwardDelivery->id, 'withexternalapproval', '清总-生产变更', $modifycncc->code);
                    }
                }
                //查产品登记单号
                if($outwardDelivery->productEnrollId){
                    $passedProductEnrollId = $this->dao->select('id')->from(TABLE_PRODUCTENROLL)->where('id')->eq($outwardDelivery->productEnrollId)->andwhere('status')->in('giteepass,emispass')->fetch('id');
                    //如果测试申请已通过 或者 没有测试申请 通过
                    if($passedProductEnrollId && ($checkList[$outwardDelivery->modifycnccId]['testingRequestPassed'] == 1 || empty($outwardDelivery->testingRequestId)) ){
                        $overDataList = $this->getOverdata($overDataList, 'outwarddelivery', $outwardDelivery->id, 'withexternalapproval', '清总-生产变更', $modifycncc->code);
                    }
                }
            }
        }
        return $overDataList;
    }

    /**
     * Send mail.
     *
     * @access public
     * @return void
     */
    public function sendmail($overDataList)
    {
        $this->loadModel('mail');
        $this->app->loadLang('monitorservice');
        $this->app->loadLang('requestlog');
        $to = $this->lang->requestlog->userList['user'];
        /* 获取后台通知中设置的邮件发信*/
        $this->app->loadLang('custommail');
        $mailConf   = '{"mailTitle":"【通知】您有超时未推送单子","variables":[],"mailContent":"超时不推送单号："}';
        $mailConf   = json_decode($mailConf);
        $mailTitle = vsprintf($mailConf->mailTitle, $mailConf->variables);
        /* Get mail content. */
        $oldcwd     = getcwd();
        $modulePath = $this->app->getModulePath($appName = '', 'monitorservice');
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
        $toList = $to;
        $ccList = '';
        list($toList, $ccList) = array($toList,$ccList);

        /* 处理邮件标题*/
        $subject = $mailTitle;
        /* Send emails. */
        if($toList){
            $this->mail->send($toList, $subject, $mailContent, $ccList);
        }
        if($this->mail->isError()) error_log(join("\n", $this->mail->getError()));

    }

    private function getOverdata($overDataList, $objectType, $objectId, $status, $name, $childId){
        $consumedInfo = $this->loadModel('consumed')->getCreatedDate($objectType, $objectId, '', $status);
        if($consumedInfo){
            if(strtotime(helper::now()) - strtotime($consumedInfo->createdDate) >= 10*60 and strtotime(helper::now()) - strtotime($consumedInfo->createdDate) < 30*60){
                $overData = array();
                $overData['name'] =  $name;
                $overData['id'] = $childId;
                array_push($overDataList, $overData);
            }
        }
        return $overDataList;
    }
}