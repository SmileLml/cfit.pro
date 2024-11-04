<?php

include '../../control.php';
class myBuild extends build
{
    /**
     *
     * @param $buildID
     */
    public function deal($buildID)
    {
        if($_POST)
        {
            $changes = $this->build->deal($buildID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            //警告信息
            if(dao::isWarn()){
                $message = dao::getWarn() . $this->lang->build->warnDefaultOp;
                $this->send(array('result' => 'success', 'callback' =>'confirmSave("'.$message.'")'));
            }

            $actionID = $this->loadModel('action')->create('build', $buildID, 'deal');
            if($changes) $this->action->logHistory($actionID, $changes);

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $build = $this->loadModel('build')->getByID($buildID);
        $this->app->loadLang('review');
        $this->app->loadConfig('review');
        $userDeal = explode(',',$build->dealuser);

        if(!in_array($this->app->user->account,$userDeal)){
            die($this->lang->build->nowStatusError);
        }
        $projectId = $build->project;
        $productId = $build->product;
        $productVersion = $build->version;
        $statusList = array('' => '');
        switch($build->status)
        {
            case 'build':
                $statusList['waittest'] = $this->lang->build->statusList['waittest'];
               // $statusList = 'waittest';
                break;
            case 'waittest': //待测试
                $statusList['waitverify'] = $this->lang->build->testsuccess;
                $statusList['testfailed'] = $this->lang->build->statusList['testfailed'];
                //是否需要展示安全门禁
                $isQualityGate = $this->build->getIsQualityGate($build);
                if($isQualityGate){
                    $this->loadModel('qualitygate');
                    $qualityGateInfo    = $this->qualitygate->getQualityGateInfoByBuildId($build->id, 'id, status');
                    $severityTestResult = $qualityGateInfo->status;
                    $this->view->severityTestResult = $severityTestResult; //安全测试结果
                    if($severityTestResult == $this->lang->qualitygate->statusArray['finish']){
                        $severityGateResult = $this->qualitygate->getSeverityGateResult($projectId, $productId, $productVersion, $buildID);
                        if($severityGateResult == 2){ //质量门禁未通过
                            $statusList = array('' => '');
                            $statusList['waitdeptmanager'] = $this->lang->build->testsuccess;
                            $statusList['testfailed'] = $this->lang->build->statusList['testfailed'];
                        }
                        $this->view->severityGateResult = $severityGateResult; //安全门禁结果
                    }
                }
                $this->view->isQualityGate = $isQualityGate;
                break;

            case 'waitdeptmanager': //待部门领导审批
                $this->loadModel('qualitygate');
                $statusList['waitverify'] = $this->lang->build->reviewStatusList['pass'];
                $statusList['testfailed'] = $this->lang->build->reviewStatusList['reject'];
                $severityGateResult = $this->qualitygate->getSeverityGateResult($projectId, $productId, $productVersion, $buildID);
                $this->view->severityGateResult = $severityGateResult; //安全门禁结果
                break;

            case 'waitverify':
                /*if($build->systemverify){
                    // 勾选需要， 待验证
                    $statusList['testsuccess'] = $this->lang->build->versionsuccess;
                }else{*/
                    //勾选不需要  待发布
                    $statusList['verifysuccess'] = $this->lang->build->versionsuccess;
                //}
               // $statusList['testsuccess'] = $this->lang->build->versionsuccess;
                $statusList['versionfailed'] = $this->lang->build->statusList['versionfailed'];
                break;
           /* case 'testsuccess':
                $statusList['waitverifyapprove'] = $this->lang->build->waitverifyapprove ;
                $statusList['verifyfailed'] = $this->lang->build->statusList['verifyfailed'];
                break;
            case 'waitverifyapprove':
                $statusList['verifysuccess'] = $this->lang->build->verifysuccess ;
                $statusList['verifyrejectbacksystem']  = $this->lang->build->statusList['verifyrejectbacksystem'];//审批不通过（退回系统部验证人员修改）
                $statusList['verifyrejectsubmit']      = $this->lang->build->statusList['verifyrejectsubmit'];//审批不通过（退回发起人）
                break;*/
            case 'verifysuccess':
                $statusList['released'] = $this->lang->build->statusList['released'];
                break;
            case 'testfailed':
           // case 'verifyfailed':
            case 'versionfailed':
                $statusList['build'] = $this->lang->build->statusList['build'];
                 $statusList['waittest'] = $this->lang->build->statusList['waittest'];
                break;
        }
        $this->view->depts      = $this->loadModel('dept')->getOptionMenu();
        $this->view->title      = $this->lang->build->deal;
        $this->view->users      = $this->loadModel('user')->getPairs('noclosed');
        $this->view->build      = $build;
        $this->view->statusList = $statusList;
        $this->display();
    }
}