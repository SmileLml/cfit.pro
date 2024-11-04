<?php
class modifycnccModel extends model
{
    const SYSTEMNODE = 3;
    public function pushmodifycncc($modifycnccId){
        unset($this->lang->modifycncc->implementModalityNewList[0]);
        $this->lang->modifycncc->implementModalityList = $this->lang->modifycncc->implementModalityList + $this->lang->modifycncc->implementModalityNewList;
        $this->loadModel('problem');
        //返回响应结果
        $result = '';
        //查询需要同步的数据
        $pushmodifycncc = $this->dao->select("*")->from(TABLE_MODIFYCNCC)->where('id')->eq($modifycnccId)->fetch();
        $users = $this->loadModel('user')->getPairs('noletter|noclosed');
        $pushEnable = $this->config->global->pushModifycnccEnable;
        if($pushEnable == 'enable'){
            $url = $this->config->global->pushModifycnccUrl;
            $pushAppId = $this->config->global->pushModifycnccAppId;
            $pushAppSecret = $this->config->global->pushModifycnccAppSecret;
            $pushUsername = $this->config->global->pushModifycnccUsername;
            $fileIP       = $this->config->global->pushModifycnccFileIP;
            $headers = array();
            $headers[] = 'App-Id: ' . $pushAppId;
            $headers[] = 'App-Secret: ' . $pushAppSecret;



            $pushData = array();
            $pushData['changeOrderId']               = $pushmodifycncc->code;
            $pushData['unautoReaon']                  = $pushmodifycncc->aadsReason;
            //key值
            $pushData['isEmergent']                    = zget($this->lang->modifycncc->typeList,$pushmodifycncc->type)=='紧急'?'1':'0';
            //value值
            $pushData['changeLevel']                  = zget($this->lang->modifycncc->levelList,$pushmodifycncc->level);
            //key值
            $pushData['isTemp']                          = zget($this->lang->modifycncc->propertyList, $pushmodifycncc->property)=='是'?'1':'0';
            //key值
            $pushData['changeCategory']             = zget($this->lang->modifycncc->classifyList,$pushmodifycncc->classify);

            $outwarddelivery = $this->dao->select("*")->from(TABLE_OUTWARDDELIVERY)->where('modifycnccId')->eq($modifycnccId)->fetch();
            $this->dao->update(TABLE_MODIFYCNCC)->set('ifMediumChanges')->eq($outwarddelivery->ifMediumChanges)->set('release')->eq($outwarddelivery->release)->where('id')->eq($modifycnccId)->exec();

            if(!empty($pushmodifycncc->node)){
                $value = '';
                $nodeList = $this->lang->modifycncc->nodeList;
                foreach(explode(',', $pushmodifycncc->node) as $node)
                {
                    $value = $value  . zget($nodeList,$node). ',';
                }
                $pushData['dataCenterNameList'] = rtrim($value, ",");
            }else{
                $pushData['dataCenterNameList']      = '';
            }
            $pushData['expectedStartTime']         = strtotime($pushmodifycncc->planBegin)*1000;
            $pushData['expectedEndTime']           = strtotime($pushmodifycncc->planEnd)*1000;

            $problems   =   $this->dao->select('IssueId')->from(TABLE_PROBLEM)->where('id')->in(explode(',',$pushmodifycncc->problem))->fetchAll('IssueId');
            if(!empty($problems)){
                $value = '';
                foreach( $problems as $problem)
                {
                    if ($problem->IssueId != ''){
                        $value = $value  . $problem->IssueId. ',';
                    }
                }
                $pushData['problemIdUniqueToResolve'] = trim($value, ",");
            }else{
                $pushData['problemIdUniqueToResolve']      = '';
            }
            //取 problem的 IssueId  by problem
            //$pushData['problemIdUniqueToResolve']            = $this->dao->select('IssueId')->from(TABLE_PROBLEM)->where('id')->eq($pushmodifycncc->problem)->fetch();
            $pushData['changeSummary']                       = html_entity_decode(strip_tags(str_replace("<br />","\n",strval(htmlspecialchars_decode($pushmodifycncc->desc,ENT_QUOTES)))));//富文本7
            $pushData['reasonForChange']                     = html_entity_decode(strip_tags(str_replace("<br />","\n",strval(htmlspecialchars_decode($pushmodifycncc->reason,ENT_QUOTES)))));//富文本6
            $pushData['changeObjectives']                    = html_entity_decode(strip_tags(str_replace("<br />","\n",strval(htmlspecialchars_decode($pushmodifycncc->target,ENT_QUOTES)))));//富文本5
            $pushData['productionSystemAffect']              = html_entity_decode(strip_tags(str_replace("<br />","\n",strval(htmlspecialchars_decode($pushmodifycncc->effect,ENT_QUOTES)))));//富文本4
            $pushData['analysisStateExplanation']            = html_entity_decode(strip_tags(str_replace("<br />","\n",strval(htmlspecialchars_decode($pushmodifycncc->risk,ENT_QUOTES)))));//富文本3
            $pushData['testReport']                          = html_entity_decode(strip_tags(str_replace("<br />","\n",strval(htmlspecialchars_decode($pushmodifycncc->test,ENT_QUOTES)))));//富文本2
            $pushData['changeProcedure']                     = html_entity_decode(strip_tags(str_replace("<br />","\n",strval(htmlspecialchars_decode($pushmodifycncc->step,ENT_QUOTES)))));//富文本1
            $pushData['changeApplicant']                     = zget($users, $pushmodifycncc->createdBy, '');
            $depts                                           = $this->loadModel('dept')->getTopPairs();
            $pushData['applicationDepartment']               = $depts[$pushmodifycncc->createdDept];
            $pushData['applyUsercontact']                    = $pushmodifycncc->applyUsercontact;

            if($pushData['isTemp']=='1'){
                $pushData['backspaceExpectedStartTime']      = '';
                if (strpos($pushmodifycncc->backspaceExpectedStartTime,'0000') === false){
                    $pushData['backspaceExpectedStartTime']          = strtotime($pushmodifycncc->backspaceExpectedStartTime)*1000;
                }
                $pushData['backspaceExpectedEndTime']            = '';
                if (strpos($pushmodifycncc->backspaceExpectedEndTime,'0000') === false){
                    $pushData['backspaceExpectedEndTime']          = strtotime($pushmodifycncc->backspaceExpectedEndTime)*1000;
                }

            }

            $pushData['backupDataCenterChangeSyncDesc']      = html_entity_decode(strip_tags(str_replace("<br />","\n",strval(htmlspecialchars_decode($pushmodifycncc->backupDataCenterChangeSyncDesc,ENT_QUOTES)))));//富文本9
            $pushData['businessFunctionAffect']              = html_entity_decode(strip_tags(str_replace("<br />","\n",strval(htmlspecialchars_decode($pushmodifycncc->businessFunctionAffect,ENT_QUOTES)))));//富文本10
            $pushData['changeContentAndMethod']              = html_entity_decode(strip_tags(str_replace("<br />","\n",strval(htmlspecialchars_decode($pushmodifycncc->changeContentAndMethod,ENT_QUOTES)))));//富文本8
            //value值
            //$pushData['feasibilityAnalysis']                           =
            if(!empty($pushmodifycncc->feasibilityAnalysis)){
                $value = '';
                foreach(explode(',', $pushmodifycncc->feasibilityAnalysis) as $feasibilityAnalysis)
                {
                    $value = $value . ',' . zget($this->lang->modifycncc->feasibilityAnalysisList,$feasibilityAnalysis);
                }
                $pushData['feasibilityAnalysis']  = trim($value, ",");
            }else{
                $pushData['feasibilityAnalysis']                           = '';
            }

            //value值
            $pushData['changeSource']                                = zget($this->lang->modifycncc->changeSourceList,$pushmodifycncc->changeSource);
            //value值
            $pushData['changeStage']                                 = zget($this->lang->modifycncc->changeStageList,$pushmodifycncc->changeStage);
            //value值
            $pushData['operationType']                               = '分区修改类变更';//zget($this->lang->modifycncc->operationTypeList,$pushmodifycncc->operationType);
            $pushData['controlTableFile']                            = trim($pushmodifycncc->controlTableFile);
            $pushData['controlTableSteps']                           = html_entity_decode(strip_tags(str_replace("<br />","\n",strval(htmlspecialchars_decode($pushmodifycncc->controlTableSteps,ENT_QUOTES)))));//富文本11

            $as2 = [];
            foreach(explode(',', $pushmodifycncc->cooperateDepNameList) as $cooperateDepName)
            {
                if(!$cooperateDepName) continue;
                $as2[] = zget($this->lang->modifycncc->cooperateDepNameListList, $cooperateDepName );
            }
            $cooperateDepNameList = implode(',', $as2);
            $pushData['cooperateDepNameList']                        = htmlspecialchars_decode($cooperateDepNameList);
            $pushData['emergencyManageAffect']                       = html_entity_decode(strip_tags(str_replace("<br />","\n",strval(htmlspecialchars_decode($pushmodifycncc->emergencyManageAffect,ENT_QUOTES)))));//富文本16
            //value值
            $pushData['implementModality']                           = zget($this->lang->modifycncc->implementModalityList,$pushmodifycncc->implementModality);
            //value值
            $pushData['isBusinessAffect']                            = zget($this->lang->modifycncc->isBusinessAffectList,$pushmodifycncc->isBusinessAffect)=='是'?'1':'0';
            $pushData['businessAffect']                              = html_entity_decode(strip_tags(str_replace("<br />","\n",strval(htmlspecialchars_decode($pushmodifycncc->businessAffect,ENT_QUOTES)))));//富文本12
            //value值
            $pushData['isBusinessCooperate']                         = zget($this->lang->modifycncc->isBusinessCooperateList,$pushmodifycncc->isBusinessCooperate)=='是'?'1':'0';
            //value值
            $pushData['isBusinessJudge']                             = zget($this->lang->modifycncc->isBusinessJudgeList,$pushmodifycncc->isBusinessJudge)=='是'?'1':'0';
            //value值 默认传否0
            $pushData['isPerAuthorization']                          = zget($this->lang->modifycncc->isPerAuthorizationList,$pushmodifycncc->isPerAuthorization)=='是'?'1':'0';
            $pushData['judgeDep']                                    = zget($this->lang->modifycncc->judgeDepList,$pushmodifycncc->judgeDep);
            $pushData['judgePlan']                                   = html_entity_decode(strip_tags(str_replace("<br />","\n",strval(htmlspecialchars_decode($pushmodifycncc->judgePlan,ENT_QUOTES)))));//富文本13
            $pushData['productRegistrationCode']                     = $outwarddelivery->productInfoCode!=''?$outwarddelivery->productInfoCode:'无';


            //$pushData['emergencyBackWay_riskAnalysis']  = '[{"emergencyBackWay"}:"应急回退方式内容","riskAnalysis":"风险点分析内容"]';


            $pushData['techniqueCheck']                              = html_entity_decode(strip_tags(str_replace("<br />","\n",strval(htmlspecialchars_decode($pushmodifycncc->techniqueCheck,ENT_QUOTES)))));//富文本14
            //value值
            $pushData['benchmarkVerificationType']                   = zget($this->lang->modifycncc->benchmarkVerificationTypeList,$pushmodifycncc->benchmarkVerificationType);
            $pushData['verificationResults']                         = $pushmodifycncc->verificationResults;
            $pushData['projectIdUnique']                             = trim($pushmodifycncc->CNCCprojectIdUnique,',');
            $pushData['businessCooperateContent']		             = html_entity_decode(strip_tags(str_replace("<br />","\n",strval(htmlspecialchars_decode($pushmodifycncc->businessCooperateContent,ENT_QUOTES)))));//富文本15

            $pushData['applyReasonOutWindow']		             = html_entity_decode(strip_tags(str_replace("<br />","\n",strval(htmlspecialchars_decode($pushmodifycncc->applyReasonOutWindow,ENT_QUOTES)))));//富文本15
            $pushData['keyGuaranteePeriodApplyReason']		             = html_entity_decode(strip_tags(str_replace("<br />","\n",strval(htmlspecialchars_decode($pushmodifycncc->keyGuaranteePeriodApplyReason,ENT_QUOTES)))));//富文本15

            $applicationInfo = $this->dao->select('id,code,name')->from(TABLE_APPLICATION)->fetchAll('id');
            $partitions = $this->dao->select('name,ip')->from(TABLE_PARTITION)
                ->where('deleted')->eq('0')->fetchAll('name');
            //app存储格式：[["448"/"SHN-M-COR-KVM"],["450"/"SHN-M-COR-NFSs2"],["450"/"SHN-M-COR-NFSs1"]]
            $pushmodifycncc->app = substr($pushmodifycncc->app,1,strlen($pushmodifycncc->app)-2); // ["448"/"SHN-M-COR-KVM"],["450"/"SHN-M-COR-NFSs2"],["450"/"SHN-M-COR-NFSs1"]
            $as = [];
            $businessSystemNameList2 = '';
            $businessSystemNameList  = [];
            $ips = [];
            $i = 0;
            foreach(explode(',', $pushmodifycncc->app) as $app)
            {
                //["448"/"SHN-M-COR-KVM"]
                if(!$app) continue;
                $app = substr($app,1,strlen($app)-2);
                $app = explode('"/"', $app);
                $app[0] = trim($app[0],'"'); //系统id 448或code

                $app[1] = trim($app[1],'"'); // 分区英文名name SHN-M-COR-KVM
                //历史数据处理，如果是应用系统id就查询application表
                $applicationCode = is_numeric($app[0])?zget($applicationInfo, $app[0] , "",$applicationInfo[$app[0]]->code):$app[0];
                $as[$i] = $applicationCode; //根据系统id取系统英文名code

                $ips[$i]= $partitions[$app[1]]->ip;//根据分区英文名name取分区ip
                $i++;
                //系统英文名/分区英文名,多个逗号隔开      KVM/SHN-M-COR-KVM,NFS/SHN-M-COR-NFSs2,NFS/SHN-M-COR-NFSs1,
                $businessSystemNameList2 = $businessSystemNameList2.$applicationCode.'/'.$app[1].',';
                //系统英文名组成的数组  不用字符串是为了用array_unique方法对系统英文名去重
                $businessSystemNameList[]  = $applicationCode;
            }
            //
            $businessSystemNameList2 = trim($businessSystemNameList2,',');
            //系统英文名加逗号
            $applicationtype =  '[';
            $middle = '';
            foreach ($as as $key=>$value){
                $middle = $middle. '['. '"'. $value. '"'. ','. '"'. $ips[$key]. '"'. "]". ',';
            }
            $middle = trim($middle,',');
            $applicationtype = $applicationtype.$middle.']';
            //todo , 2.系统和区分英文名
            $pushData['businessSystemNameList2']				     = $businessSystemNameList2;
            //系统英文名去重
            $businessSystemNameList = array_unique($businessSystemNameList);
            $pushData['businessSystemNameList']                      = trim(implode(',', $businessSystemNameList),',');
            //todo ，1.系统英文名加逗号
            $pushData['businessSystemIdList']                        = $applicationtype;


            $changeRelation = [];
            foreach ($this->lang->modifycncc->relateTypeList as $key => $value)
            {
                $sql = "SELECT relationID from zt_secondline zs where objectType='modifycncc' and objectID=$modifycnccId and relationType='modifycncc' and relationship = '$key'";
                $beforeids  =     $this->dao->query($sql)->fetchAll();
                if(!empty($beforeids)){
                    foreach ($beforeids as $id){
                        $beforeModifycncc = $this->dao->select('code')->from(TABLE_MODIFYCNCC)->where('id')->eq($id->relationID)->fetch();
                        $changeRelation[$value] = $changeRelation[$value].$beforeModifycncc->code.',';
                    }
                    $changeRelation[$value] = trim($changeRelation[$value],',');
                }
            }
            //todo   3.关联变更单列表
            $pushData['changeRelation']                              = $changeRelation;

            //todo   4.风险分析说应急处置
            $pushData['riskAnalysisEmergencyHandle']                 = $pushmodifycncc->riskAnalysisEmergencyHandle;
            //todo   5.关联需求单号，需要对数据进行转换
            $pushData['relatedDemandNum'] 					         = $pushmodifycncc->relatedDemandNum;

            $requirements   =   $this->dao->select('entriesCode')->from(TABLE_REQUIREMENT)->where('id')->in(explode(',',$pushmodifycncc->relatedDemandNum))->fetchAll('entriesCode');
            if(!empty($requirements)){
                $value = '';
                foreach( $requirements as $requirement)
                {
                    $value = $value  . $requirement->entriesCode. ',';
                }
                $pushData['relatedDemandNum'] = rtrim($value, ",");
            }else{
                $pushData['relatedDemandNum']      = '';
            }

            $this->loadModel("outwarddelivery")->lang;
            $relatedTestApplication         =   $this->dao->select('giteeId')->from(TABLE_TESTINGREQUEST)->where('id')->eq($outwarddelivery->testingRequestId)->andWhere('deleted')->eq('0')->fetch()->giteeId;
            $relatedProductRegistrations    =   $this->dao->select('giteeId')->from(TABLE_PRODUCTENROLL)->where('id')->eq($outwarddelivery->productEnrollId)->andWhere('deleted')->eq('0')->fetch()->giteeId;
            //迭代11新增字段,单选
            $pushData['relatedTestApplication']         =   $relatedTestApplication;
            $pushData['relatedProductRegistrations']    =   $relatedProductRegistrations;
            $pushData['isMediaChanged']                 =   zget($this->lang->outwarddelivery->isMediaChangedList,$outwarddelivery->ifMediumChanges)=='是'?'1':'0';
            //迭代32新增任务单字段
            $relatedTaskId = array();
            if(!empty($outwarddelivery->secondorderId)){
                $secondorderIdArray = explode(',',$outwarddelivery->secondorderId);
                foreach ($secondorderIdArray as $key){
                    $relatedTask = $this->dao->select('externalCode')->from(TABLE_SECONDORDER)->where('id')->eq($key)->fetch();
                    if(!empty($relatedTask)){
                        array_push($relatedTaskId, $relatedTask->externalCode);
                    }
                }

            }
            // 迭代34新增字段 紧急来源 紧急原因
            $pushData['emergentSource'] = zget($this->lang->modifycncc->urgentSourceList,$pushmodifycncc->urgentSource,'');
            $pushData['emergentReason'] = $pushmodifycncc->urgentReason;
            // 需求收集4597
            $pushData['suppExplan']     = $pushmodifycncc->changeImpactAnalysis;

            $pushData['relatedTaskId']         =   trim(implode(',',$relatedTaskId),",");
            //todo 记录
            //记录日志
            $action = 'syncmodifycncc';
            if($pushmodifycncc->isSyncModifycncc == '1'){
                $action = 'editmodifycncc';
            }
            $this->loadModel('action')->create('modifycncc', $pushmodifycncc->id, $action, '', '', 'guestjk');
            $this->loadModel('action')->create('outwarddelivery', $outwarddelivery->id, $action, '', '', 'guestjk');

            //todo 附件
            $RelationFiles  =   array();
            $modifycncc2 = $this->getByID($modifycnccId);

            $downloadUrl = $this->config->global->downloadIP;
            foreach($modifycncc2->releases as $release){
                $path_arr = explode('/',$release->path);
                $filename2push=rtrim(end($path_arr),' ');
                array_push($RelationFiles, array('url'=> $release->remotePathQz, 'md5'=> $release->md5, 'fileName' =>$filename2push ));
            }
            //'fileName' 去掉urlencode
            $pushData['relationFiles'] = $RelationFiles;
            $pushData['changeForm']     = zget($this->lang->modifycncc->changeFormList,$pushmodifycncc->changeForm,'');
            $pushData['autoTool']       = zget($this->lang->modifycncc->automationToolsList,$pushmodifycncc->automationTools,'');
//            a($pushData);exit;
            $object = 'modifycncc';
            $objectType = 'pushmodifycncc';
            $response = '';
            $status = 'fail';
            $extra = '';

            /**
             * 2023.03.21判断MD5值为空，不再推送
             */
            if (!empty($pushData['relationFiles'])){
                foreach ($pushData['relationFiles'] as $relationFile) {
                    if ($relationFile['md5'] == ''){
                        $res = ['status'=>'fail','msg'=>'MD5值不能为空'];
                        $this->loadModel('requestlog')->saveRequestLog($url, $object, $objectType, 'POST', $pushData, json_encode($res), $status, $extra, $modifycnccId);
                        return 'md5empty';
                    }
                }
            }

            $result = $this->loadModel('requestlog')->http($url, $pushData, 'POST', 'json', array(), $headers);
            $pushDate = helper::now();
            $this->dao->update(TABLE_MODIFYCNCC)->set('pushDate')->eq($pushDate)->where('id')->eq($modifycnccId)->exec();
            if (!empty($result)) {
                $resultData = json_decode($result);
                // || $resultData->isSave == 1
                if ($resultData->code == '200') { //200 = 成功的 isSave == 1 代表成功保存 比如第一次没响应 再次请求
                    $status = 'success';
                    if(!empty($resultData->data->key)) { //重新推送返回的是 $resultData->data->item->key 这样就不更新了
                        $this->dao->update(TABLE_MODIFYCNCC)->set('isSyncModifycncc')->eq('1')->set('giteeId')->eq($resultData->data->key)->set('status')->eq('centrepmreview')->where('id')->eq($pushmodifycncc->id)->exec();
                    }
                }
                $response = $result;
            } else {
                $response = "对方无响应";
            }
            $this->requestlog->saveRequestLog($url, $object, $objectType, 'POST', $pushData, $response, $status, $extra, $modifycnccId);

        }
        return $result;
    }

    /**
     * 下载文件签名
     * @param $filename
     * @return int
     */
    public function getSign($filename)
    {
        return $this->loadModel('downloads')->getSign($filename);
    }

    /**
     *检查是否允许驳回
     *
     * @param $info
     * @return bool
     */
    public function checkAllowReject($modifycncc){
        $res = false;
        if(in_array($modifycncc->status, $this->lang->modifycncc->allowRejectStatusList)){
            $res = true;
        }
        $actions    = $this->loadModel('action')->getList('modifycncc', $modifycncc->id);

        $date = '';
        foreach ($actions as $action){
            if($action->action == 'sync' or $action->action == 'update'){
                $date = $action->date;
            }
        }
        if($date != '' and $date < $modifycncc->feedbackDate){
            $res = true;
        }

        return  $res;
    }

    /**
     * Project: chengfangjinke
     * Method: review
     * User: Tony Stark
     * Year: 2021
     * Date: 2022/05/28
     * Time: 14:44
     * Desc: This is the code comment. This method is called review.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $infoID
     * @return false|void
     */
    public function reject($modifycnccID){
        $modifycncc = $this->getByID($modifycnccID);
        //判断当前状态和审核中传入的状态是否一致，以防有同组中其他人修改
        $res = $this->checkAllowReject($modifycncc);
        if(!$res){
            dao::$errors['statusError'] = $this->lang->modifycncc->rejectError;
            return false;
        }
        $comment = trim($this->post->comment);
        if(!$comment){
            dao::$errors['statusError'] = $this->lang->modifycncc->rejectCommentEmpty;
            return false;
        }
        $lastDealDate = date('Y-m-d');
        $this->dao->update(TABLE_MODIFYCNCC)->set('status')->eq('reject')->set('lastDealDate')->eq($lastDealDate)->where('id')->eq($modifycnccID)->exec();
        $this->loadModel('consumed')->record('modifycncc', $modifycnccID, '0', $this->app->user->account, $modifycncc->status, 'reject', array());
        return true;
    }


    /**
     * Project: chengfangjinke
     * Method: getList
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:45
     * Desc: This is the code comment. This method is called getList.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $action
     * @param $browseType
     * @param $queryID
     * @param $orderBy
     * @param null $pager
     * @return mixed
     */
    public function getList($browseType, $queryID, $orderBy, $pager = null)
    {
        $modifycnccQuery = '';
        if($browseType == 'bysearch')
        {
            $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : '';
            if($query)
            {
                $this->session->set('modifycnccQuery', $query->sql);
                $this->session->set('modifycnccForm', $query->form);
            }

            if($this->session->modifycnccQuery == false) $this->session->set('modifycnccQuery', ' 1 = 1');

            $modifycnccQuery = $this->session->modifycnccQuery;
            $modifycnccQuery = str_replace('AND `', ' AND `t1.', $modifycnccQuery);
            $modifycnccQuery = str_replace('AND (`', ' AND (`t1.', $modifycnccQuery);
            $modifycnccQuery = str_replace('`', '', $modifycnccQuery);


        //   // 处理受影响的业务系统搜索字段
        //     if(strpos($modifycnccQuery, '`app`') !== false)
        //     {
        //         $modifycnccQuery = str_replace('`app`', "CONCAT(',', `app`, ',')", $modifycnccQuery);
        //     }

        //     // 处理[系统分类]搜索字段
        //     if(strpos($modifycnccQuery, '`isPayment`') !== false)
        //     {
        //         $modifycnccQuery = str_replace('`isPayment`', "CONCAT(',', `isPayment`, ',')", $modifycnccQuery);
        //     }

        //     // 处理[支持人员]搜索字段
        //     if(strpos($modifycnccQuery, '`supply`') !== false)
        //     {
        //         $modifycnccQuery = str_replace('`supply`', "CONCAT(',', `supply`, ',')", $modifycnccQuery);
        //     }

        //     // 处理[变更节点]搜索字段
        //     if(strpos($modifycnccQuery, '`node`') !== false)
        //     {
        //         $modifycnccQuery = str_replace('`node`', "CONCAT(',', `node`, ',')", $modifycnccQuery);
        //     }
            if(strpos($modifycnccQuery, 't1.desc') !== false)
            {
                $modifycnccQuery = str_replace('t1.desc', "t1.`desc`", $modifycnccQuery);
            }

            if(strpos($modifycnccQuery, 't1.relatedOutwardDelivery') !== false)
            {
                $modifycnccQuery = str_replace('t1.relatedOutwardDelivery', "t2.code", $modifycnccQuery);
            }

            if(strpos($modifycnccQuery, 't1.belongedOutwardDelivery') !== false)
            {
                $modifycnccQuery = str_replace('t1.belongedOutwardDelivery', "t2.isNewModifycncc = 1 AND t2.code", $modifycnccQuery);
            }

            if(strpos($modifycnccQuery, 't1.relatedTestingRequest') !== false)
            {
                $modifycnccQuery = str_replace('t1.relatedTestingRequest', "t3.code", $modifycnccQuery);
            }

            if(strpos($modifycnccQuery, 't1.relatedProductEnroll') !== false)
            {
                $modifycnccQuery = str_replace('t1.relatedProductEnroll', "t4.code", $modifycnccQuery);
            }

            if(strpos($modifycnccQuery, 't1.changeRelation') !== false)
            {
                $modifycnccQuery = str_replace('t1.changeRelation', "t5.relationID", $modifycnccQuery);
            }
            if(strpos($modifycnccQuery, 't1.productId') !== false)
            {
                $modifycnccQuery = str_replace('t1.productId', "t2.productId", $modifycnccQuery);
            }
        }
        if(strpos($orderBy, 'desc_') !== false){
            $orderBy = str_replace('desc_', "`desc`_", $orderBy);
        }

        $modifycnccs = $this->dao->select('distinct t1.*,t2.code as outwarddeliveryCode,t2.id as outwarddeliveryId')->from(TABLE_MODIFYCNCC)->alias('t1')
            ->leftJoin(TABLE_OUTWARDDELIVERY)->alias('t2')
            ->on('t1.id=t2.modifycnccId and t2.isNewModifycncc=1')
            ->leftJoin(TABLE_TESTINGREQUEST)->alias('t3')
            ->on('t2.testingRequestId=t3.id')
            ->leftJoin(TABLE_PRODUCTENROLL)->alias('t4')
            ->on('t2.productEnrollId=t4.id')
            ->leftJoin(TABLE_SECONDLINE)->alias('t5')
            ->on('t1.id=t5.objectID and t5.objectType="modifycncc" and t5.relationType="modifycncc"')
//            两次联查和一次结果目前看上去没有却别，但是效率差很多，所以暂时注释，如有问题可以再放开
//            ->leftJoin(TABLE_OUTWARDDELIVERY)->alias('t6')
//            ->on('(t2.productEnrollId = t6.productEnrollId and t6.isNewProductEnroll = 1) or (t2.testingRequestId = t6.testingRequestId and t6.isNewTestingRequest = 1)')
            ->where('t1.status')->ne('deleted')
            ->andWhere('t1.deleted')->ne('1')
            ->beginIF($browseType != 'all' and $browseType != 'bysearch')->andWhere('t1.status')->eq($browseType)->andWhere('t1.closed')->ne(1)->fi()
            ->beginIF($browseType == 'bysearch')->andWhere($modifycnccQuery)->fi()
            ->orderBy($orderBy)
            ->page($pager, 't1.id')
            ->fetchAll('id');
        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'modifycncc', $browseType != 'bysearch');

        $accountList = array();
        $this->loadModel('review');
        foreach($modifycnccs as $key => $modifycncc)
        {
            $accountList[$modifycncc->createdBy] = $modifycncc->createdBy;
            if($modifycnccs[$key]->status == 'closing')
            {
                $stage = 0;
                foreach($this->lang->modifycncc->reviewerList as $review){
                    $stage++;
                    if($review == '产创部二线专员')
                    {
                        break;
                    }
                }
                $modifycnccs[$key]->reviewers = $this->review->getLastPendingPeople('modifycncc', $modifycncc->id, $modifycncc->version, $stage);
            }
            else
            {
                $modifycnccs[$key]->reviewers = $this->review->getReviewer('modifycncc', $modifycncc->id, $modifycncc->version, $modifycncc->reviewStage);
            }
        }

        // User dept list.
        $dmap = $this->dao->select('account,realname,dept')->from(TABLE_USER)->where('account')->in($accountList)->fetchAll('account');
        foreach($modifycnccs as $key => $modifycncc)
        {
            //$modifycnccs[$key]->createdDept = isset($dmap[$modifycncc->createdBy]) ? $dmap[$modifycncc->createdBy]->dept : '';
            $modifycnccs[$key]->createdUser = isset($dmap[$modifycncc->createdBy]) ? $dmap[$modifycncc->createdBy]->realname : '';
        }

        return $modifycnccs;
    }

    /**
     * Project: chengfangjinke
     * Method: buildSearchForm
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:45
     * Desc: This is the code comment. This method is called buildSearchForm.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $queryID
     * @param $actionURL
     */
    public function buildSearchForm($queryID, $actionURL)
    {
        $this->config->modifycncc->search['actionURL'] = $actionURL;
        $this->config->modifycncc->search['queryID']   = $queryID;

        $this->config->modifycncc->search['params']['createdBy']['values']                  = array('' => '') + $this->loadModel('user')->getPairs('noletter|noclosed');
        $this->config->modifycncc->search['params']['createdDept']['values']                = array('' => '') + $this->loadModel('dept')->getOptionMenu();
        $this->config->modifycncc->search['params']['project']['values']                    = array('' => '') + $this->loadModel('projectplan')->getAllProjects();
        $this->config->modifycncc->search['params']['problem']['values']                    = array('' => '') + $this->loadModel('problem')->getPairsAbstract();
        $this->config->modifycncc->search['params']['demand']['values']                     = array('' => '') + $this->loadModel('demand')->getPairsTitle('noclosed');
        $this->config->modifycncc->search['params']['relatedDemandNum']['values']           = array('' => '') + $this->dao->select('id,concat(concat(concat(code,"（"),name),"）")')->from(TABLE_REQUIREMENT)->where('entriesCode')->like('requirements%')->andWhere('status')->ne('deleted')->fetchPairs();
        $this->config->modifycncc->search['params']['CNCCprojectIdUnique']['values']        = array('' => '') + $this->dao->select('code,concat(concat(concat(code,"（"),name),"）")')->from(TABLE_CBPPROJECT)->where('deleted')->eq('0')->fetchPairs();
        $this->config->modifycncc->search['params']['changeRelation']['values']             = array('' => '') + $this->dao->select('id,code')->from(TABLE_MODIFYCNCC)->where('deleted')->eq('0')->fetchPairs();
        $this->config->modifycncc->search['params']['app']['values']                        = array('' => '') + $this->loadModel('application')->getapplicationNameCodePairsWithPartition();
        $this->config->modifycncc->search['params']['secondorderId']['values'] = array('' => '') + $this->loadModel('secondorder')->getNamePairs();
        $this->config->modifycncc->search['params']['urgentSource']['values']  = array('' => '') + $this->lang->modifycncc->urgentSourceList;;
        unset($this->lang->modifycncc->implementModalityNewList[0]);
        $this->config->modifycncc->search['params']['implementModality']['values'] = $this->lang->modifycncc->implementModalityList + $this->lang->modifycncc->implementModalityNewList;
        $products = $this->loadModel('product')->getList();
        $this->config->modifycncc->search['params']['productId']['values'] = array('' => '') +  array_column($products, 'name' , 'id');
        $this->loadModel('search')->setSearchParams($this->config->modifycncc->search);
    }

    /**
     * Project: chengfangjinke
     * Method: getReviewers
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:45
     * Desc: This is the code comment. This method is called getReviewers.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $deptId
     * @return array
     */
    public function getReviewers($deptId = 0)
    {
        $users = $this->loadModel('user')->getPairs('noletter|noclosed');
        $reviewers = array();
        if(!$deptId){
            $deptId = $this->app->user->dept;
        }
        $myDept = $this->loadModel('dept')->getByID($deptId);

        // 质量部CM
        $cms = explode(',', trim($myDept->cm, ','));
        $us = array('' => '');
        foreach($cms as $c)
        {
            $us[$c] = $users[$c];
        }
        $reviewers[] = $us;

        //申请部门组长审批
        $groupUsers = explode(',', trim($myDept->groupleader, ','));
        $us = array('' => '');
        if(!empty($groupUsers)){
            foreach($groupUsers as $c)
            {
                $us[$c] = $users[$c];
            }
        }
        $reviewers[] = $us;

        // 部门负责人
        $cms = explode(',', trim($myDept->manager, ','));
        $us  = array('' => '');
        foreach($cms as $c)
        {
            $us[$c] = $users[$c];
        }
        $reviewers[] = $us;

        // 系统部
        $sysDept = $this->dao->select('id,manager')->from(TABLE_DEPT)->where('name')->eq('系统部')->fetch();
        $cms = explode(',', trim($sysDept->manager, ','));
        $us = array('' => '');
        foreach($cms as $c)
        {
            $us[$c] = $users[$c];
        }
        $reviewers[] = $us;

        // 产品经理
        $cms = explode(',', trim($myDept->po, ','));
        $us  = array('' => '');
        foreach($cms as $c)
        {
            $us[$c] = $users[$c];
        }
        $reviewers[] = $us;

        // 部门分管领导
        $cms = explode(',', trim($myDept->leader, ','));
        $us  = array('' => '');
        foreach($cms as $c)
        {
            $us[$c] = $users[$c];
        }
        $reviewers[] = $us;

        // 总经理
        $depts = $this->loadModel('dept')->getRecurveSubDeptIds(30);
        if (in_array($this->app->user->dept,$depts)){
            // 上海分公司特殊处理
            $this->app->loadConfig('modify');
            $reviewers[] = [$this->config->modify->branchManagerList => $users[$this->config->modify->branchManagerList]];
        }else{
            $reviewer = $this->dao->select('account')->from(TABLE_USER)->where('role')->eq('ceo')->fetch('account');
            $reviewers[] = array($reviewer => $users[$reviewer]);
        }

        // 产创部二线专员
        $cms = explode(',', trim($myDept->executive, ','));
        $us = array('' => '');
        foreach($cms as $c)
        {
            $us[$c] = $users[$c];
        }
        $reviewers[] = $us;

        return $reviewers;
    }

    /**
     * Project: chengfangjinke
     * Method: submitReview
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:45
     * Desc: This is the code comment. This method is called submitReview.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $modifycnccID
     * @param $version
     * @param $level
     */
    private function submitReview($modifycnccID, $version, $level)
    {
        $myDept = $this->loadModel('dept')->getByID($this->app->user->dept);
        $status = 'pending';
        $nodes = $this->post->nodes;
        $stage = 1;
        if($level == 2){
            $nodes[6] = [];
        }elseif ($level == 3){
            $nodes[5] = [];
            $nodes[6] = [];
        }
        //从小到大排序
        ksort($nodes);

        foreach($nodes as $key => $currentNodes)
        {
            if(!is_array($currentNodes)){
                $currentNodes = array($currentNodes);
            }
            $currentNodes = array_filter($currentNodes);
            $this->loadModel('review')->addNode('modifycncc', $modifycnccID, $version, $currentNodes, true, $status, $stage);
            $status = 'wait';
            $stage++;
        }
    }


    /**
     * 生产变更提交审核 对外交付专用
     * @param $modifycnccID
     * @param $version
     * @param $level
     */
    public function submitReviewOutwardDelivery($modifycnccID, $version, $level, $type)
    {
        $myDept = $this->loadModel('dept')->getByID($this->app->user->dept);
        $status = 'pending';
        $nodes = $this->post->nodes;
        $stage = 1;
        $nodes[4] = [];
        if($type == 'modifycncc'){
            if($level == 2){
                $nodes[6] = [];
            }elseif ($level == 3){
                $nodes[5] = [];
                $nodes[6] = [];
            }
        }elseif($type == 'productenroll'){
            $nodes[3] = [];
            $nodes[5] = [];
            $nodes[6] = [];
        }elseif($type == 'testingrequest'){
            $nodes[3] = [];
            $nodes[5] = [];
            $nodes[6] = [];
        }
        if(empty($nodes[1])){
            $nodes[1] = [];
        }
        //从小到大排序
        ksort($nodes);
        //保存不校验必填，节点没有选择审核人员会造成数据错乱
        for ($i = 0;$i <= 7;$i++){
            if(!isset($nodes[$i])){
                $nodes[$i] = [];
            }
        }
        ksort($nodes);
        foreach($nodes as $key => $currentNodes)
        {
            if(!is_array($currentNodes)){
                $currentNodes = array($currentNodes);
            }
            $currentNodes = array_filter($currentNodes);
            $this->loadModel('review')->addNode('outwardDelivery', $modifycnccID, $version, $currentNodes, true, $status, $stage);
            $status = 'wait';
            $stage++;
        }
    }
    /**
     * Project: chengfangjinke
     * Method: submitEditReview
     * User: wangjiurong
     * Year: 2022
     * Date: 2022/2/15
     * Time: 15:43
     * Desc: 提交编辑审核信息.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $modifycnccID
     * @param $version
     * @param $level
     * @return bool
     */

    public function submitEditReview($modifycnccID, $version, $level){
        $objectType = 'modifycncc';
        //原审核节点及审核人
        $oldNodes = $this->loadModel('review')->getNodes($objectType, $modifycnccID, $version);
        //编辑后审核结点的审核人
        $nodes = $this->post->nodes;
        if($level == 2){
            $nodes[6] = [];
        }elseif ($level == 3){
            $nodes[5] = [];
            $nodes[6] = [];
        }
        ksort($nodes);

        $withGrade = true;
        foreach($nodes as $key => $currentReviews) {
            if (!is_array($currentReviews)) {
                $currentReviews = array($currentReviews);
            }
            $currentReviews = array_filter($currentReviews);
            //审核节点
            $oldNodeInfo = $oldNodes[$key];
            $oldReviewInfoList = $oldNodeInfo->reviewers;
            //原来节点审核人
            $oldReviews = [];
            if(!empty($oldReviewInfoList)){
                $oldReviews = array_column($oldReviewInfoList, 'reviewer');
            }
            //编辑前后当前节点审核人信息有变化
            if(array_diff($currentReviews, $oldReviews) || array_diff($oldReviews, $currentReviews)){
                $nodeID = $oldNodeInfo->id;

                //删除审核节点原来审核人
                if(!empty($oldReviews)){
                    $oldIds = array_column($oldReviewInfoList, 'id');
                    $res = $this->loadModel('review')->delReviewers($oldIds);
                }

                //新增节点本次编辑设置的
                if(!empty($currentReviews)) {
                    $status = $oldNodeInfo->status;
                    if($oldNodeInfo->status == 'ignore'){
                        $status = 'wait';
                        $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq($status)->where('id')->eq($oldNodeInfo->id)->exec();
                    }
                    $res = $this->loadModel('review')->addNodeReviewers($nodeID, $currentReviews, $withGrade, $status);
                }
            }else{
                if($currentReviews){
                    if($oldNodeInfo->status == 'ignore'){
                        $status = 'wait';
                        $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq($status)->where('id')->eq($oldNodeInfo->id)->exec();
                        $this->dao->update(TABLE_REVIEWER)->set('status')->eq($status)->where('node')->eq($oldNodeInfo->id)->exec();
                    }
                }
            }
        }
        return true;
    }

    /**
     * 提交编辑审核信息. 对外交付专用
     * @param $outwardDeliveryId
     * @param $version
     * @param $level
     * @return bool
     */
    public function submitEditReviewOutwardDelivery($outwardDeliveryId, $version, $level, $type){
        $objectType = 'outwardDelivery';
        //原审核节点及审核人
        $oldNodes = $this->loadModel('review')->getNodes($objectType, $outwardDeliveryId, $version);
        //编辑后审核结点的审核人
        $nodes = $this->post->nodes;
        if($type == 'modifycncc'){
            if($level == 2){
                $nodes[6] = [];
            }elseif ($level == 3){
                $nodes[5] = [];
                $nodes[6] = [];
            }
        }elseif($type == 'productenroll'){
            $nodes[3] = [];
            $nodes[5] = [];
            $nodes[6] = [];
        }elseif($type == 'testingrequest'){
            $nodes[3] = [];
            $nodes[4] = [];
            $nodes[5] = [];
            $nodes[6] = [];
        }
        if(empty($nodes[1])){
            $nodes[1] = [];
        }
        ksort($nodes);



        $withGrade = true;
        foreach($nodes as $key => $currentReviews) {
            if (!is_array($currentReviews)) {
                $currentReviews = array($currentReviews);
            }
            $currentReviews = array_filter($currentReviews);
            //审核节点
            $oldNodeInfo = $oldNodes[$key];
            $oldReviewInfoList = $oldNodeInfo->reviewers;
            //原来节点审核人
            $oldReviews = [];
            if(!empty($oldReviewInfoList)){
                $oldReviews = array_column($oldReviewInfoList, 'reviewer');
            }
            //编辑前后当前节点审核人信息有变化
            if(array_diff($currentReviews, $oldReviews) || array_diff($oldReviews, $currentReviews)){
                $nodeID = $oldNodeInfo->id;

                //删除审核节点原来审核人
                if(!empty($oldReviews)){
                    $oldIds = array_column($oldReviewInfoList, 'id');
                    $res = $this->loadModel('review')->delReviewers($oldIds);
                }

                //新增节点本次编辑设置的
                if(!empty($currentReviews)) {
                    $status = $oldNodeInfo->status;
                    if($oldNodeInfo->status == 'ignore'){
                        $status = 'wait';
                        $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq($status)->where('id')->eq($oldNodeInfo->id)->exec();
                    }
                    $res = $this->loadModel('review')->addNodeReviewers($nodeID, $currentReviews, $withGrade, $status);
                }
            }else{
                if($currentReviews){
                    if($oldNodeInfo->status == 'ignore'){
                        $status = 'wait';
                        $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq($status)->where('id')->eq($oldNodeInfo->id)->exec();
                        $this->dao->update(TABLE_REVIEWER)->set('status')->eq($status)->where('node')->eq($oldNodeInfo->id)->exec();
                    }
                }
            }
        }
        return true;
    }
    /**
     * Project: chengfangjinke
     * Method: create
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:45
     * Desc: This is the code comment. This method is called create.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @return mixed
     */
    public function create()
    {
        //检查审核信息
        $checkRes = $this->checkReviewerNodesInfo($this->post->level, $this->post->nodes);
        //有错误返回
        if(!$checkRes || dao::isError()){
            return dao::$errors;
        }
        // 判断工时
        $consumed = $this->post->consumed;
        if(empty($consumed))
        {
            return dao::$errors['consumed'] = $this->lang->modifycncc->emptyConsumed;
        }
        if(!is_numeric($this->post->consumed)){
          return dao::$errors['consumed'] = $this->lang->modifycncc->consumedNumber;
        }
        //变更可行性分析
        if(!$this->post->feasibilityAnalysis){
          return dao::$errors['feasibilityAnalysis'] = $this->lang->modifycncc->emptyFeasibilityAnalysis;
        }



        $data = fixer::input('post')
            ->join('problem', ',')
            ->join('demand', ',')
            ->join('secondorderId', ',')
            ->join('project', ',')
            ->join('emergencyBackWay', ',')
            ->join('riskAnalysis', ',')
            ->join('relate', ',')
            ->join('relateNum', ',')
            ->join('node', ',')
            ->join('relatedDemandNum', ',')
            ->join('CNCCprojectIdUnique', ',')
            ->join('feasibilityAnalysis', ',')
            ->setIF($this->post->isBusinessCooperate=='1', 'cooperateDepNameList', '')
            ->setIF($this->post->isBusinessCooperate=='1', 'businessCooperateContent', '')
            ->setIF($this->post->isBusinessJudge=='1', 'judgeDep', '')
            ->setIF($this->post->isBusinessJudge=='1', 'judgePlan', '')
            ->setIF($this->post->isBusinessAffect=='1', 'businessAffect', '')
            ->setIF($this->post->property=='2', 'backspaceExpectedStartTime', '')
            ->setIF($this->post->property=='2', 'backspaceExpectedEndTime', '')
            ->setIF($this->post->changeSource!='1', 'controlTableFile', '')
            ->setIF($this->post->changeSource!='1', 'controlTableSteps', '')
            ->remove('consumed')
            ->remove('nodes,productCode,assignProduct,versionNumber,supportPlatform,hardwarePlatform,uid,partition,relate,relateNum,emergencyBackWay,riskAnalysis,appOnly')
            ->stripTags($this->config->modifycncc->editor->create['id'], $this->config->allowedTags)
            ->get();

        /* 处理系统和分区 */
        $isNPC = false;
        $NPCKey = array_keys($this->lang->modifycncc->nodeNPCList);
        if(!$this->post->node){
          return dao::$errors['node'] = $this->lang->modifycncc->emptyNode;
        }
        foreach($this->post->node as $item)
        {
          if(in_array($item, $NPCKey)){
            $isNPC = true;
          }
        }
        if(!$isNPC&&!$this->post->appOnly){
          return dao::$errors[] = $this->lang->modifycncc->emptyApp ;
        }
        if(!$isNPC){
          $transformApp = array();
          foreach($this->post->appOnly as $index => $app){
            $transformApp[$index] = array($app);
          }
          $applicationInfo = $this->loadModel('application')->getapplicationInfo();
          $as = [];
          foreach($this->post->appOnly as $apptype)
          {
            if(!$apptype) continue;
            $as[] = zget($applicationInfo, $apptype,"",$applicationInfo[$apptype]->isPayment);
          }
          $applicationtype = implode(',', $as);
          if(!empty($applicationtype))$applicationtype=",".$applicationtype;
          $data->isPayment = $applicationtype;
          $data->app = json_encode($transformApp);
        }
        else
        {
          $appPartition = '[';
          foreach ($this->post->app as $index=>$app)
          {
            if($index!=0)
            {
              $appPartition .= ',';
            }
            if(empty($this->post->partition[$index])){
              $appPartition .= '["'.$app.'"]';
            }else{
              foreach ($this->post->partition[$index] as $innerIndex=>$partition)
              {
                if($innerIndex!=0)
                {
                  $appPartition .= ',';
                }
                $appPartition .= '["'.$app.'"/"'.$partition.'"]';
              }
            }
          }
          $appPartition .= ']';

          $data->app = $appPartition;
        }

        /* 处理风险分析和应急处置 */
        $isNull = false;
        foreach ($this->post->emergencyBackWay as $key => $emergencyBackWay)
        {
          if($emergencyBackWay == '' or $this->post->riskAnalysis[$key] == '')
          {
            $isNull = true;
          }
        }
        if($isNull)
        {
          return dao::$errors[] = $this->lang->modifycncc->emptyRiskAnalysisEmergencyHandle;
        }
        else
        {
          $riskAnalysisEmergencyHandle = array();
          for($i=0;$i<count($this->post->emergencyBackWay);++$i)
          {
            $obj = new stdclass();
            $obj->emergencyBackWay = $this->post->emergencyBackWay[$i];
            $obj->riskAnalysis = $this->post->riskAnalysis[$i];
            $riskAnalysisEmergencyHandle[$i] = $obj;
          }
          $data->riskAnalysisEmergencyHandle = json_encode($riskAnalysisEmergencyHandle);
        }


        /* 判断是否关联了问题单或者需求单 */
        $flag = false;
        foreach($this->post->problem as $problem)
        {
            if(!empty($problem)) $flag = true;
        }
        foreach($this->post->demand  as $demand)
        {
            if(!empty($demand))  $flag = true;
        }
        foreach($this->post->secondorderId  as $secondorder)
        {
            if(!empty($secondorder))  $flag = true;
        }
        if(!$flag) return dao::$errors[] = $this->lang->modifycncc->emptyDemandProblem;

        /* 判断产品编号是否需要处理。*/
        $codeList = array();
        if($this->post->productCode)
        {
            foreach($this->post->productCode as $codeIndex => $code)
            {
                if(empty($code)) return dao::$errors[] = $this->lang->modifycncc->emptyProductCode;

                if(empty($this->post->assignProduct[$codeIndex]))    return dao::$errors[] = $this->lang->modifycncc->emptyAssignProduct;
                if(empty($this->post->versionNumber[$codeIndex]))    return dao::$errors[] = $this->lang->modifycncc->emptyVersionNumber;
                if(empty($this->post->supportPlatform[$codeIndex]))  return dao::$errors[] = $this->lang->modifycncc->emptySupportPlatform;
                //if(empty($this->post->hardwarePlatform[$codeIndex])) return dao::$errors[] = $this->lang->modifycncc->emptyHardwarePlatform;
                $codeList[] = array(
                    'assignProduct'    => $this->post->assignProduct[$codeIndex],
                    'versionNumber'    => $this->post->versionNumber[$codeIndex],
                    'supportPlatform'  => $this->post->supportPlatform[$codeIndex],
                    'hardwarePlatform' => $this->post->hardwarePlatform[$codeIndex]
                );
            }
        }

        $data->productCode = json_encode($codeList);

        $data->project              = trim($data->project, ',');

        $data->status      = 'wait';
        $data->createdBy   = $this->app->user->account;
        $data->createdDate = helper::now();
        $data->createdDept = $this->app->user->dept;
        $data->editedBy    = $this->app->user->account;
        $data->editedDate  = helper::now();

        /* isAppend不取值的时候给1(false) */
        $data->isAppend = $this->post->isAppend ? : 1;

        $data = $this->loadModel('file')->processImgURL($data, $this->config->modifycncc->editor->create['id'], $this->post->uid);
        $this->dao->insert(TABLE_MODIFYCNCC)->data($data)->autoCheck()
        ->checkIF($data->isBusinessCooperate=='2','cooperateDepNameList','notempty')
        ->checkIF($data->isBusinessCooperate=='2','businessCooperateContent','notempty')
        ->checkIF($data->isBusinessJudge=='2','judgeDep','notempty')
        ->checkIF($data->isBusinessJudge=='2','judgePlan','notempty')
        ->checkIF($data->isBusinessAffect=='2','businessAffect','notempty')
        ->checkIF($data->property=='1','backspaceExpectedStartTime','notempty')
        ->checkIF($data->property=='1','backspaceExpectedEndTime','notempty')
        ->checkIF($data->changeSource=='1','controlTableFile','notempty')
        ->checkIF($data->changeSource=='1','controlTableSteps','notempty')
        ->checkIF(in_array($data->changeSource,array('1','2')),'CNCCprojectIdUnique','notempty')
        ->batchCheck($this->config->modifycncc->create->requiredFields, 'notempty')->exec();
        $modifycnccID = $this->dao->lastInsertId();

        if(!dao::isError())
        {
            $this->loadModel('file')->updateObjectID($this->post->uid, $modifycnccID, 'modifycncc');
            $this->file->saveUpload('modifycncc', $modifycnccID);

            $this->submitReview($modifycnccID, 1, $data->level);

            /* Record the relationship between the associated issue and the requisition. */
            $this->loadModel('secondline')->saveRelationship($modifycnccID, 'modifycncc', $data->problem, 'problem');
            $this->secondline->saveRelationship($modifycnccID, 'modifycncc', $data->demand, 'demand');
            $this->secondline->saveRelationship($modifycnccID, 'projectModifycncc', $data->project, 'project');
            $this->secondline->saveRelationship($modifycnccID, 'modifycncc', $data->relatedDemandNum, 'requirement');
            if($data->problem)
            {
                $problemIdList = $data->problem;
                if(!is_array($problemIdList)) $problemIdList = explode(',', $problemIdList);
                foreach($problemIdList as $relationID)
                {
                    if(empty($relationID)) continue;
                    $this->secondline->saveRelationship($relationID, 'projectProblem', $data->project, 'project');
                }
            }
            if($data->demand)
            {
                $demandIdList = $data->demand;
                if(!is_array($demandIdList)) $demandIdList = explode(',', $demandIdList);
                foreach($demandIdList as $relationID)
                {
                    if(empty($relationID)) continue;
                    $this->secondline->saveRelationship($relationID, 'projectDemand', $data->project, 'project');
                }
            }

            // 处理关联变更单关系
            foreach ($this->post->relate as $index => $relation)
            {
              if($relation !='' and $this->post->relateNum[$index] != '') {
                $this->secondline->saveModifycnccRelationship($modifycnccID, $this->post->relateNum[$index], $relation);
                if ($relation === 'before') {
                  $this->secondline->saveModifycnccRelationship($this->post->relateNum[$index], $modifycnccID, 'after');
                } elseif ($relation === 'after') {
                  $this->secondline->saveModifycnccRelationship($this->post->relateNum[$index], $modifycnccID, 'before');
                } elseif ($relation === 'test') {
                  $this->secondline->saveModifycnccRelationship($this->post->relateNum[$index], $modifycnccID, 'generalization');
                } elseif ($relation === 'generalization') {
                  $this->secondline->saveModifycnccRelationship($this->post->relateNum[$index], $modifycnccID, 'test');
                } elseif ($relation === 'relation') {
                  $this->secondline->saveModifycnccRelationship($this->post->relateNum[$index], $modifycnccID, 'relation');
                } elseif ($relation === 'synchronous'){
                  $this->secondline->saveModifycnccRelationship($this->post->relateNum[$index], $modifycnccID, 'synchronous');
                } elseif ($relation === 'include'){
                  $this->secondline->saveModifycnccRelationship($this->post->relateNum[$index], $modifycnccID, 'beInclude');
                }
              }
            }


            $date   = date('-Ymd-');
            $number = $this->dao->select('count(id) c')->from(TABLE_MODIFYCNCC)->where('code')->like("%$date%")->fetch('c') + 1;
            $code   = 'CFIT-CQ-' . date('Ymd-') . sprintf('%02d', $number);

            $this->dao->update(TABLE_MODIFYCNCC)->set('code')->eq($code)->where('id')->eq($modifycnccID)->exec();
            $this->loadModel('consumed')->record('modifycncc', $modifycnccID, $this->post->consumed, $this->app->user->account, '', 'wait', array());

            foreach($codeList as $code)
            {
                $recordCode = new stdClass();
                $recordCode->product = $code['assignProduct'];
                $recordCode->modifycncc  = $modifycnccID;
                $recordCode->code    = json_encode($code);
                $this->dao->insert(TABLE_PRODUCTCODE)->data($recordCode)->exec();
            }
        }

        return $modifycnccID;
    }

    private function getCode()
    {
        $number = $this->dao->select('count(id) c')->from(TABLE_MODIFYCNCC)->where('code')->like('CFIT-CQ-' . date('Ymd-')."%")->fetch('c') + 1;
        $code   = 'CFIT-CQ-' . date('Ymd-') . sprintf('%02d', $number);
        return $code;
    }

    /**
     * Project: chengfangjinke
     * Method: update
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:45
     * Desc: This is the code comment. This method is called update.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $modifycnccID
     * @return array
     */
    public function update($modifycnccID)
    {
        // 判断工时
        $consumed = $this->post->consumed;
        if(empty($consumed))
        {
          return dao::$errors['consumed'] = $this->lang->modifycncc->emptyConsumed;
        }
        if(!is_numeric($this->post->consumed)){
          return dao::$errors['consumed'] = $this->lang->modifycncc->consumedNumber;
        }
        //变更可行性分析
        if(!$this->post->feasibilityAnalysis){
          return dao::$errors['feasibilityAnalysis'] = $this->lang->modifycncc->emptyFeasibilityAnalysis;
        }
        $oldmodifycncc = $this->getByID($modifycnccID);
        $modifycncc = fixer::input('post')
            ->add('editedBy', $this->app->user->account)
            ->add('editedDate', helper::now())
            ->join('problem', ',')
            ->join('secondorderId', ',')
            ->join('demand', ',')
            ->join('project', ',')
            ->join('node', ',')
            ->join('relatedDemandNum', ',')
            ->join('CNCCprojectIdUnique', ',')
            ->join('feasibilityAnalysis', ',')
            ->setIF($this->post->isBusinessCooperate=='1', 'cooperateDepNameList', '')
            ->setIF($this->post->isBusinessCooperate=='1', 'businessCooperateContent', '')
            ->setIF($this->post->isBusinessJudge=='1', 'judgeDep', '')
            ->setIF($this->post->isBusinessJudge=='1', 'judgePlan', '')
            ->setIF($this->post->isBusinessAffect=='1', 'businessAffect', '')
            ->setIF($this->post->property=='2', 'backspaceExpectedStartTime', '')
            ->setIF($this->post->property=='2', 'backspaceExpectedEndTime', '')
            ->setIF($this->post->changeSource!='1', 'controlTableFile', '')
            ->setIF($this->post->changeSource!='1', 'controlTableSteps', '')
            ->remove('consumed')
            ->remove('nodes,productCode,assignProduct,versionNumber,supportPlatform,hardwarePlatform,uid,partition,relate,relateNum,emergencyBackWay,riskAnalysis,appOnly')
            ->stripTags($this->config->modifycncc->editor->edit['id'], $this->config->allowedTags)
            ->get();

       /* 判断是否关联了问题单或者需求单 */
        $flag = false;
        foreach($this->post->problem as $problem)
        {
            if(!empty($problem)) $flag = true;
        }
        foreach($this->post->demand  as $demand)
        {
            if(!empty($demand))  $flag = true;
        }
        foreach($this->post->secondorderId  as $secondorder)
        {
            if(!empty($secondorder))  $flag = true;
        }
        if(!$flag) return dao::$errors[] = $this->lang->modifycncc->emptyDemandProblem;

        /* 判断产品编号是否需要处理。*/
        $codeList = array();
        if($this->post->productCode)
        {
            foreach($this->post->productCode as $codeIndex => $code)
            {
                if(empty($code)) return dao::$errors[] = $this->lang->modifycncc->emptyProductCode;

                if(empty($this->post->assignProduct[$codeIndex]))    return dao::$errors[] = $this->lang->modifycncc->emptyAssignProduct;
                if(empty($this->post->versionNumber[$codeIndex]))    return dao::$errors[] = $this->lang->modifycncc->emptyVersionNumber;
                if(empty($this->post->supportPlatform[$codeIndex]))  return dao::$errors[] = $this->lang->modifycncc->emptySupportPlatform;
                //if(empty($this->post->hardwarePlatform[$codeIndex])) return dao::$errors[] = $this->lang->modifycncc->emptyHardwarePlatform;
                $codeList[] = array(
                    'assignProduct'    => $this->post->assignProduct[$codeIndex],
                    'versionNumber'    => $this->post->versionNumber[$codeIndex],
                    'supportPlatform'  => $this->post->supportPlatform[$codeIndex],
                    'hardwarePlatform' => $this->post->hardwarePlatform[$codeIndex]
                );
            }
        }
        $modifycncc->productCode = json_encode($codeList);

        //检查审核信息
        $checkRes = $this->checkReviewerNodesInfo($this->post->level, $this->post->nodes);
        //有错误返回
        if(!$checkRes || dao::isError()){
            return dao::$errors;
        }

        if($oldmodifycncc->status == 'reject')
        {
            $modifycncc->status      = 'wait';
            $modifycncc->version     = $oldmodifycncc->version + 1;
            $modifycncc->reviewStage = 0;
            $this->submitReview($modifycnccID, $modifycncc->version, $modifycncc->level);
        }else{
            $this->submitEditReview($modifycnccID, $oldmodifycncc->version, $modifycncc->level);
        }

        /* 处理系统和分区 */
        $isNPC = false;
        $NPCKey = array_keys($this->lang->modifycncc->nodeNPCList);
        if(!$this->post->node){
          return dao::$errors[] = $this->lang->modifycncc->emptyNode;
        }
        foreach($this->post->node as $item)
        {
          if(in_array($item, $NPCKey)){
            $isNPC = true;
          }
        }
        if(!$isNPC&&!$this->post->appOnly){
          return dao::$errors[] = $this->lang->modifycncc->emptyApp ;
        }
        if(!$isNPC){
          $transformApp = array();
          foreach($this->post->appOnly as $index => $app){
            $transformApp[$index] = array($app);
          }
          $applicationInfo = $this->loadModel('application')->getapplicationInfo();
          $as = [];
          foreach($this->post->appOnly as $apptype)
          {
            if(!$apptype) continue;
            $as[] = zget($applicationInfo, $apptype,"",$applicationInfo[$apptype]->isPayment);
          }
          $applicationtype = implode(',', $as);
          if(!empty($applicationtype))$applicationtype=",".$applicationtype;
          $modifycncc->isPayment = $applicationtype;
          $modifycncc->app = json_encode($transformApp);
        }else{
          $appPartition = '[';
          foreach ($this->post->app as $index=>$app)
          {
            if($index!=0)
            {
              $appPartition .= ',';
            }
            if(empty($this->post->partition[$index])){
              $appPartition .= '["'.$app.'"]';
            }else{
              foreach ($this->post->partition[$index] as $innerIndex=>$partition)
              {
                if($innerIndex!=0)
                {
                  $appPartition .= ',';
                }
                $appPartition .= '["'.$app.'"/"'.$partition.'"]';
              }
            }
          }
          $appPartition .= ']';
        }
        $modifycncc->app = $appPartition;

        /* 处理风险分析和应急处置 */
        $isNull = false;
        foreach ($this->post->emergencyBackWay as $key => $emergencyBackWay)
        {
          if($emergencyBackWay == '' or $this->post->riskAnalysis[$key] == '')
          {
            $isNull = true;
          }
        }
        if($isNull)
        {
          return dao::$errors[] = $this->lang->modifycncc->emptyRiskAnalysisEmergencyHandle;
        }else {
          $riskAnalysisEmergencyHandle = array();
          for ($i = 0; $i < count($this->post->emergencyBackWay); ++$i) {
            $obj = new stdclass();
            $obj->emergencyBackWay = $this->post->emergencyBackWay[$i];
            $obj->riskAnalysis = $this->post->riskAnalysis[$i];
            $riskAnalysisEmergencyHandle[$i] = $obj;
          }
          $modifycncc->riskAnalysisEmergencyHandle = json_encode($riskAnalysisEmergencyHandle);
        }

        /* isAppend不取值的时候给1(false) */
        $modifycncc->isAppend = $this->post->isAppend ? : 1;

        $modifycncc = $this->loadModel('file')->processImgURL($modifycncc, $this->config->modifycncc->editor->edit['id'], $this->post->uid);
        $this->dao->update(TABLE_MODIFYCNCC)->data($modifycncc)->autoCheck()
          ->checkIF($modifycncc->isBusinessCooperate=='2','cooperateDepNameList','notempty')
          ->checkIF($modifycncc->isBusinessCooperate=='2','businessCooperateContent','notempty')
          ->checkIF($modifycncc->isBusinessJudge=='2','judgeDep','notempty')
          ->checkIF($modifycncc->isBusinessJudge=='2','judgePlan','notempty')
          ->checkIF($modifycncc->isBusinessAffect=='2','businessAffect','notempty')
          ->checkIF($modifycncc->property=='1','backspaceExpectedStartTime','notempty')
          ->checkIF($modifycncc->property=='1','backspaceExpectedEndTime','notempty')
          ->checkIF($modifycncc->changeSource=='1','controlTableFile','notempty')
          ->checkIF($modifycncc->changeSource=='1','controlTableSteps','notempty')
          ->checkIF(in_array($modifycncc->changeSource,array('1','2')),'CNCCprojectIdUnique','notempty')
          ->batchCheck($this->config->modifycncc->edit->requiredFields, 'notempty')
          ->where('id')->eq($modifycnccID)
          ->exec();

        if(!dao::isError()) {

          $this->loadModel('file')->updateObjectID($this->post->uid, $modifycnccID, 'modifycncc');
          $this->file->saveUpload('modifycncc', $modifycnccID);

          /* Record the relationship between the associated issue and the requisition. */
          $this->loadModel('secondline')->saveRelationship($modifycnccID, 'modifycncc', $modifycncc->problem, 'problem');
          $this->secondline->saveRelationship($modifycnccID, 'modifycncc', $modifycncc->demand, 'demand');
          $this->secondline->saveRelationship($modifycnccID, 'projectModifycncc', $modifycncc->project, 'project');
          $this->secondline->saveRelationship($modifycnccID, 'modifycncc', $modifycncc->relatedDemandNum, 'requirement');

          if ($oldmodifycncc->problem) {
            $problemIdList = $oldmodifycncc->problem;
            if (!is_array($problemIdList)) $problemIdList = explode(',', $problemIdList);
            foreach ($problemIdList as $relationID) {
              if (empty($relationID)) continue;
              $this->secondline->saveRelationship($relationID, 'projectProblem', '', 'project');
            }
          }

          if ($oldmodifycncc->demand) {
            $demandIdList = $oldmodifycncc->demand;
            if (!is_array($demandIdList)) $demandIdList = explode(',', $demandIdList);
            foreach ($demandIdList as $relationID) {
              if (empty($relationID)) continue;
              $this->secondline->saveRelationship($relationID, 'projectDemand', '', 'project');
            }
          }

          if ($modifycncc->problem) {
            $problemIdList = $modifycncc->problem;
            if (!is_array($problemIdList)) $problemIdList = explode(',', $problemIdList);
            foreach ($problemIdList as $relationID) {
              if (empty($relationID)) continue;
              $this->secondline->saveRelationship($relationID, 'projectProblem', $modifycncc->project, 'project');
            }
          }

          if ($modifycncc->demand) {
            $demandIdList = $modifycncc->demand;
            if (!is_array($demandIdList)) $demandIdList = explode(',', $demandIdList);
            foreach ($demandIdList as $relationID) {
              if (empty($relationID)) continue;
              $this->secondline->saveRelationship($relationID, 'projectDemand', $modifycncc->project, 'project');
            }
          }

          // 处理关联变更单关系
          //此处逻辑有点绕，因为变更单相互之间存在关系，并且关系不唯一，因此只能同时删掉所有关联的关系(不包括beInclude)。
          $this->secondline->cleanModifycncc($modifycnccID);
          foreach ($this->post->relate as $index => $relation) {
            if ($relation != '' and $this->post->relateNum[$index] != '') {
              $this->secondline->saveModifycnccRelationship($modifycnccID, $this->post->relateNum[$index], $relation);
              if ($relation === 'before') {
                $this->secondline->saveModifycnccRelationship($this->post->relateNum[$index], $modifycnccID, 'after');
              } elseif ($relation === 'after') {
                $this->secondline->saveModifycnccRelationship($this->post->relateNum[$index], $modifycnccID, 'before');
              } elseif ($relation === 'test') {
                $this->secondline->saveModifycnccRelationship($this->post->relateNum[$index], $modifycnccID, 'generalization');
              } elseif ($relation === 'generalization') {
                $this->secondline->saveModifycnccRelationship($this->post->relateNum[$index], $modifycnccID, 'test');
              } elseif ($relation === 'relation') {
                $this->secondline->saveModifycnccRelationship($this->post->relateNum[$index], $modifycnccID, 'relation');
              } elseif ($relation === 'synchronous') {
                $this->secondline->saveModifycnccRelationship($this->post->relateNum[$index], $modifycnccID, 'synchronous');
              } elseif ($relation === 'include') {
                $this->secondline->saveModifycnccRelationship($this->post->relateNum[$index], $modifycnccID, 'beInclude');
              }
            }
          }


          $this->loadModel('consumed')->record('modifycncc', $modifycnccID, $this->post->consumed, $this->app->user->account, '', 'wait', array());
          /* Handle the relationship between products and production changes. */
          $this->dao->delete()->from(TABLE_PRODUCTCODE)->where('modifycncc')->eq($modifycnccID)->exec();
          foreach ($codeList as $code) {
            $recordCode = new stdClass();
            $recordCode->product = $code['assignProduct'];
            $recordCode->modifycncc = $modifycnccID;
            $recordCode->code = json_encode($code);
            $this->dao->insert(TABLE_PRODUCTCODE)->data($recordCode)->exec();
          }
        }
        $oldmodifycncc->riskAnalysisEmergencyHandle = stripslashes(json_encode($modifycncc->riskAnalysisEmergencyHandle));
        return common::createChanges($oldmodifycncc, $modifycncc);
    }

    public function updateByData($modifycnccID, $modifycncc, $outwardId)
    {
        $this->app->loadLang('outwarddelivery');
        $oldData = $this->dao->select('cardStatus,`status`')->from(TABLE_MODIFYCNCC)->where('id')->eq($modifycnccID)->fetch();
        if(!in_array($oldData->status,$this->lang->outwarddelivery->alloweditStatus)) {  //不是待提交 和被拒绝的 不能编辑 新增内部未通过状态
            return true;
        }
        if($oldData->cardStatus == 1) { //外部通过 不能编辑
            return true;
        }
        $modifycncc = (object)$modifycncc;

        if ($_POST['issubmit'] != 'save'){
            // 判断工时
           /* $consumed = $this->post->consumed;
            if(empty($consumed))
            {
                return dao::$errors['consumed'] = $this->lang->modifycncc->emptyConsumed;
            }
            if(!is_numeric($this->post->consumed)){
                return dao::$errors['consumed'] = $this->lang->modifycncc->consumedNumber;
            }*/
            //变更可行性分析
            if(!$this->post->feasibilityAnalysis){
                return dao::$errors['feasibilityAnalysis'] = $this->lang->modifycncc->emptyFeasibilityAnalysis;
            }
            if ($this->post->changeStage == 2){
                foreach ($this->post->relate as $index => $relation) {
                    if ($relation == '' || $this->post->relateNum[$index] == '') {
                        return dao::$errors['relate'] = $this->lang->modifycncc->emptyRelate;
                    }
                }
            }
        }
        $oldmodifycncc = $this->getByID($modifycnccID);
        $status = $oldmodifycncc->status;
        if($status == 'cmconfirmed' || $status == 'groupsuccess' || $status == 'managersuccess' || $status == 'systemsuccess'
            || $status == 'posuccess' || $status == 'leadersuccess' || $status == 'gmsuccess' || $status == 'productsuccess'
            || $status == 'closing' || $status == 'closed' || $status == 'waitqingzong' || $status == 'withexternalapproval'
            || $status == 'modifysuccesspart' || $status == 'modifysuccess') { //外部通过 不更新
            return true;
        }
        /* 判断是否关联了问题单或者需求单 */
        $flag = false;
        foreach($this->post->problemId as $problem)
        {
            if(!empty($problem)) $flag = true;
        }
        foreach($this->post->demandId  as $demand)
        {
            if(!empty($demand))  $flag = true;
        }
        foreach($this->post->secondorderId  as $secondorder)
        {
            if(!empty($secondorder))  $flag = true;
        }
        if ($_POST['issubmit'] != 'save'){
            if(!$flag) return dao::$errors[] = $this->lang->modifycncc->emptyDemandProblem;
            if(!$this->post->node){
                return dao::$errors[] = $this->lang->modifycncc->emptyNode;
            }
        }

        /* 判断产品编号是否需要处理。*/
        //产品信息
        $codeProduct = array();
        if($this->post->productId){
            $productObj = $this->loadModel('product')->getByID($this->post->productId[0]);
            if($this->post->productEnrollId){
                $productenrollObj  =  $this->loadModel('productenroll')->getByID($this->post->productEnrollId);
            }
            $codeProduct = array(
                'assignProduct'    => $this->post->productId[0],
                'versionNumber'    => !empty($productenrollObj) ? $productenrollObj->versionNum : '',
                'supportPlatform'  => $productObj->os,
                'hardwarePlatform' => $productObj->arch
            );
        }


        /* 处理系统和分区 */
        // $isNPC = false;
        // $NPCKey = array_keys($this->lang->modifycncc->nodeNPCList);

        // foreach($this->post->node as $item)
        // {
        //     if(in_array($item, $NPCKey)){
        //         $isNPC = true;
        //     }
        // }
        // if(!$isNPC&&!$this->post->appOnly){
        //     return dao::$errors[] = $this->lang->modifycncc->emptyApp ;
        // }
        // if(!$isNPC){
        //     $transformApp = array();
        //     foreach($this->post->appOnly as $index => $app){
        //         $transformApp[$index] = array($app);
        //     }
        //     $applicationInfo = $this->loadModel('application')->getapplicationInfo();
        //     $as = [];
        //     foreach($this->post->appOnly as $apptype)
        //     {
        //         if(!$apptype) continue;
        //         $as[] = zget($applicationInfo, $apptype,"",$applicationInfo[$apptype]->isPayment);
        //     }
        //     $applicationtype = implode(',', $as);
        //     if(!empty($applicationtype))$applicationtype=",".$applicationtype;
        //     $modifycncc->isPayment = $applicationtype;
        //     $modifycncc->app = json_encode($transformApp);
        // }else{
            $appPartition = '[';
            foreach ($this->post->appmodify as $index=>$app)
            {
                if($index!=0)
                {
                    $appPartition .= ',';
                }
                if(empty($this->post->partition[$index])){
                    $appPartition .= '["'.$app.'"]';
                }else{
                    foreach ($this->post->partition[$index] as $innerIndex=>$partition)
                    {
                        if($innerIndex!=0)
                        {
                            $appPartition .= ',';
                        }
                        $appPartition .= '["'.$app.'"/"'.$partition.'"]';
                    }
                }
            }
            $appPartition .= ']';
           //2022-10-12 shixuyang 生产变更单业务系统和分区长度超过限制
           if(strlen($appPartition)>5000){
              return dao::$errors[] = $this->lang->modifycncc->appOverLength;
           }
            $modifycncc->app = $appPartition;
        // }


        /* 处理风险分析和应急处置 */
        $isNull = false;
        foreach ($this->post->emergencyBackWay as $key => $emergencyBackWay)
        {
            if($emergencyBackWay == '' or $this->post->riskAnalysis[$key] == '')
            {
                $isNull = true;
            }
        }
        if($isNull)
        {
            if ($_POST['issubmit'] != 'save'){
                return dao::$errors[] = $this->lang->modifycncc->emptyRiskAnalysisEmergencyHandle;
            }
        }else {
            $riskAnalysisEmergencyHandle = array();
            for ($i = 0; $i < count($this->post->emergencyBackWay); ++$i) {
                $obj = new stdclass();
                $obj->emergencyBackWay = $this->post->emergencyBackWay[$i];
                $obj->riskAnalysis = $this->post->riskAnalysis[$i];
                $riskAnalysisEmergencyHandle[$i] = $obj;
            }
            $modifycncc->riskAnalysisEmergencyHandle = json_encode($riskAnalysisEmergencyHandle);
        }

        /* isAppend不取值的时候给1(false) */
        $modifycncc->isAppend = $this->post->isAppend ? : 1;
        unset($modifycncc->emergencyBackWay); //这两个已经在riskAnalysisEmergencyHandle存json
        unset($modifycncc->riskAnalysis);
        //$modifycncc = $this->loadModel('file')->processImgURL($modifycncc, $this->config->modifycncc->editor->edit['id'], $this->post->uid);


        $this->dao->update(TABLE_MODIFYCNCC)->data($modifycncc)
//            ->autoCheck()
            ->checkIF($modifycncc->isBusinessCooperate=='2','cooperateDepNameList','notempty')
            ->checkIF($modifycncc->isBusinessCooperate=='2','businessCooperateContent','notempty')
            ->checkIF($modifycncc->isBusinessJudge=='2','judgeDep','notempty')
            ->checkIF($modifycncc->isBusinessJudge=='2','judgePlan','notempty')
            ->checkIF($modifycncc->isBusinessAffect=='2','businessAffect','notempty')
            ->checkIF($modifycncc->property=='1','backspaceExpectedStartTime','notempty')
            ->checkIF($modifycncc->property=='1','backspaceExpectedEndTime','notempty')
            ->checkIF($modifycncc->changeSource=='1','controlTableFile','notempty')
            ->checkIF($modifycncc->changeSource=='1','controlTableSteps','notempty')
            //->checkIF(in_array($modifycncc->changeSource,array('1','2')),'CNCCprojectIdUnique','notempty')
            ->batchCheckIF($_POST['issubmit'] != 'save',$this->config->modifycncc->edit->requiredFields, 'notempty')
            ->where('id')->eq($modifycnccID)
            ->exec();

        if(!dao::isError()) {

            $this->loadModel('file')->updateObjectID($this->post->uid, $outwardId, 'outwardDelivery');
            $this->file->saveUpload('outwardDelivery', $outwardId);

            $this->loadModel('secondline')->saveRelationship($modifycnccID, 'modifycncc', $modifycncc->problem, 'problem');
            $this->secondline->saveRelationship($modifycnccID, 'modifycncc', $modifycncc->demand, 'demand');
            $this->secondline->saveRelationship($modifycnccID, 'modifycncc', $modifycncc->secondorderId, 'secondorder');
            $this->secondline->saveRelationship($modifycnccID, 'projectModifycncc', $modifycncc->project, 'project');
            $this->secondline->saveRelationship($modifycnccID, 'modifycncc', $modifycncc->relatedDemandNum, 'requirement');

//            if ($oldmodifycncc->problem) {
//                $problemIdList = $oldmodifycncc->problem;
//                if (!is_array($problemIdList)) $problemIdList = explode(',', $problemIdList);
//                foreach ($problemIdList as $relationID) {
//                    if (empty($relationID)) continue;
//                    $this->secondline->saveRelationship($relationID, 'projectProblem', '', 'project');
//                }
//            }
            if($modifycncc->demand)
            {
                $demandIdList = $modifycncc->demand;
                if(!is_array($demandIdList)) $demandIdList = explode(',', $demandIdList);
                foreach($demandIdList as $relationID)
                {
                    if(empty($relationID)) continue;
                    $this->secondline->saveRelationship($relationID, 'projectDemand', $modifycncc->project, 'project');
                }
            }

            // 处理关联变更单关系
            //此处逻辑有点绕，因为变更单相互之间存在关系，并且关系不唯一，因此只能同时删掉所有关联的关系(不包括beInclude)。
            $this->loadModel('secondline')->cleanModifycncc($modifycnccID);
            foreach ($this->post->relate as $index => $relation) {
                if ($relation != '' and $this->post->relateNum[$index] != '') {
                    $this->secondline->saveModifycnccRelationship($modifycnccID, $this->post->relateNum[$index], $relation);
                    if ($relation === 'before') {
                        $this->secondline->saveModifycnccRelationship($this->post->relateNum[$index], $modifycnccID, 'after');
                    } elseif ($relation === 'after') {
                        $this->secondline->saveModifycnccRelationship($this->post->relateNum[$index], $modifycnccID, 'before');
                    } elseif ($relation === 'test') {
                        $this->secondline->saveModifycnccRelationship($this->post->relateNum[$index], $modifycnccID, 'generalization');
                    } elseif ($relation === 'generalization') {
                        $this->secondline->saveModifycnccRelationship($this->post->relateNum[$index], $modifycnccID, 'test');
                    } elseif ($relation === 'relation') {
                        $this->secondline->saveModifycnccRelationship($this->post->relateNum[$index], $modifycnccID, 'relation');
                    } elseif ($relation === 'synchronous') {
                        $this->secondline->saveModifycnccRelationship($this->post->relateNum[$index], $modifycnccID, 'synchronous');
                    } elseif ($relation === 'include') {
                        $this->secondline->saveModifycnccRelationship($this->post->relateNum[$index], $modifycnccID, 'beInclude');
                    }
                }
            }


            $this->loadModel('consumed')->record('modifycncc', $modifycnccID, '0', $this->app->user->account, '', 'wait', array());
            /* Handle the relationship between products and production changes. */
            $this->dao->delete()->from(TABLE_PRODUCTCODE)->where('modifycncc')->eq($modifycnccID)->exec();

            $recordCode = new stdClass();
            $recordCode->product = $codeProduct['assignProduct'];
            $recordCode->modifycncc  = $modifycnccID;
            $recordCode->code    = json_encode($codeProduct);
            $this->dao->insert(TABLE_PRODUCTCODE)->data($recordCode)->exec();
        }
        $oldmodifycncc->riskAnalysisEmergencyHandle = stripslashes(json_encode($modifycncc->riskAnalysisEmergencyHandle));
        return common::createChanges($oldmodifycncc, $modifycncc);
    }
    /**
     * Project: chengfangjinke
     * Method: getByID
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:45
     * Desc: This is the code comment. This method is called getByID.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $modifycnccID
     * @return mixed
     */
    public function getByID($modifycnccID)
    {
        if(empty($modifycnccID)) return null;
        $modifycncc = $this->dao->select("*")->from(TABLE_MODIFYCNCC)->where('id')->eq($modifycnccID)->fetch();
        $modifycncc = $this->loadModel('file')->replaceImgURL($modifycncc, 'reason,desc,target,effect,plan,
        step,risk,checkList,result,test，backupDataCenterChangeSyncDesc，businessFunctionAffect，changeContentAndMethod，
        controlTableSteps，emergencyManageAffect，businessAffect，judgePlan,techniqueCheck');

        //获取关联变更单信息
        $relation = $this->loadModel('secondLine')->getByID($modifycnccID,'modifycncc');
        $relationArr = array();
        foreach(array_keys($relation['modifycncc']) as $key)
        {
            if(!empty($relation['modifycncc'][$key])) {
                foreach($relation['modifycncc'][$key] as $modifycnccNum) {
                    $relationArr[] = array($key, $modifycnccNum[0]);
                }
            }
        }
        $modifycncc->relation = $relationArr;

        $modifycncc->appName = '';
        $modifycncc->appTeam = '';
        if($modifycncc->app)
        {
            $appOnly = substr($modifycncc->app,1,strlen($modifycncc->app)-2); // ["448"/"SHN-M-COR-KVM"],["450"/"SHN-M-COR-NFSs2"],["450"/"SHN-M-COR-NFSs1"]
            foreach(explode(',', $appOnly) as $app)
            {
                //["448"/"SHN-M-COR-KVM"]
                if(!$app) continue;
                $app = substr($app,1,strlen($app)-2);
                $app = explode('"/"', $app);
                $item = array();
                $item[] = trim($app[0],'"');
                $item[] = isset($app[1]) ? trim($app[1],'"'):'';
                $modifycncc->appWithPartition[] = $item; //系统id 448或code
                $modifycncc->appOnly[] = trim($app[0],'"'); //系统id 448或code
            }
            // 处理历史数据，如果app为数字就查application表
            if(is_numeric($modifycncc->appOnly[0])){
                $apps = $this->dao->select('id,code,name')->from(TABLE_APPLICATION)->where('id')->in($modifycncc->appOnly)->fetchAll();
            }else{
                $apps = $this->dao->select('distinct application as id,application as code,applicationName as name')
                ->from(TABLE_PARTITION)
                ->where('application')->in($modifycncc->appOnly)
                ->andWhere('deleted')->eq('0')
                ->fetchAll();
            }
            $appsInfo=array();
            foreach($apps as $index=>$app)
            {
                $appsInfo[$app->id]=$app;
                $appsInfo[$app->id]->index=$index;
                $appsInfo[$app->id]->partition=array();
                $partitionList = $this->dao->select('name,name')->from(TABLE_PARTITION)
                ->where('application')->eq($app->code)
                ->andWhere('deleted')->eq('0')->fetchPairs();
                foreach ($partitionList as $key=>$item) {
                    $partitionList[$key] = strtolower($item);
                    $oldpartitionList[$key] = strtolower($item);
                }
                sort($partitionList);
                $newpartitionList = [];
                foreach ($partitionList as $k1=>$v1){
                    $newpartitionList[array_search($v1,$oldpartitionList)] = array_search($v1,$oldpartitionList);
                }
                $appsInfo[$app->id]->partitionList=$newpartitionList;
            }
            foreach ($modifycncc->appWithPartition as $appPartition){
                if(!empty($appPartition[1])) {
                  $appsInfo[$appPartition[0]]->partition[] = $appPartition[1];
                }
            }
            $modifycncc->appsInfo=$appsInfo;
        }
        if($modifycncc->CNCCprojectIdUnique)
        {
            $modifycncc->CBPProjectCode = $this->dao->select('code,name')->from(TABLE_CBPPROJECT)->where('code')->in(explode(',', $modifycncc->CNCCprojectIdUnique))->fetchAll();
        }
        $modifycncc->reviewers = $this->loadModel('review')->getReviewer('modifycncc', $modifycnccID, $modifycncc->version, $modifycncc->reviewStage);

        $modifycncc->releases = [];    #查看是否关联了产品
        if($modifycncc->release)
        {
            $releases = $this->loadModel('project')->getReleasesList($modifycncc->project);
            foreach(explode(',', $modifycncc->release) as $r)
            {
                if(!$r) continue;

                $files = $this->dao->select('*')->from(TABLE_FILE)->where('objectType')->eq('release')
                     ->andWhere('objectID')->eq($r)
                     ->andWhere('deleted')->eq(0)
                     ->fetchAll();

                $release = new stdclass();
                $release->id    = $r;
                $release->name  = $releases[$r]->name;
                $release->path  = $releases[$r]->path;
                $release->remotePathQz  = $releases[$r]->remotePathQz;
                $release->remotePathJx  = $releases[$r]->remotePathJx;
                $release->md5  = $releases[$r]->md5;
                $release->files = $files;

                $modifycncc->releases[] = $release;
            }
        }

        $cs = $this->dao->select('*')->from(TABLE_CONSUMED)->where('objectType')->eq('modifycncc')
            ->andWhere('objectID')->eq($modifycnccID)
            ->fetchAll();
        $modifycncc->consumed = $cs;

        // Processing version number.
        /*$modifycncc->productCodeList = array();
        if($modifycncc->productCode)
        {
            $codeList = json_decode($modifycncc->productCode);
            foreach($codeList as $code)
            {
                $product = $this->dao->select('id,code,line')->from(TABLE_PRODUCT)->where('id')->eq($code->assignProduct)->fetch();
                $line    = $this->dao->select('id,code')->from(TABLE_PRODUCTLINE)->where('id')->eq($product->line)->fetch();
                $codeTitle = $product->code . '-' . $code->versionNumber . '-for-' . $code->supportPlatform;
                if(trim($code->hardwarePlatform)) $codeTitle .= '-' . $code->hardwarePlatform;
                $modifycncc->productCodeList[] = $codeTitle;
            }
        }*/
        if ($modifycncc->actualDeliveryTime == '0000-00-00 00:00:00') $modifycncc->actualDeliveryTime = '';
        #$modifycncc->emergencyBackWay_riskAnalysis = array();
        if($modifycncc->riskAnalysisEmergencyHandle)
        {
            $modifycncc->riskAnalysisEmergencyHandle = json_decode($modifycncc->riskAnalysisEmergencyHandle);
        }
        $this->resetNodeAndReviewerName($modifycncc->createdDept);
        return $modifycncc;
    }

    public function getCodeById($id)
    {
        return $this->dao->select('code')->from(TABLE_MODIFYCNCC)->where('id')->eq($id)->fetch('code') . "";
    }

    /**
     * TongYanQi 2022/11/26
     * 获取全部
     */
    public function getAll()
    {
        return $this->dao->select("*")->from(TABLE_MODIFYCNCC)
            ->fetchall();
    }
    /**
     * Project: chengfangjinke
     * Method: close
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:45
     * Desc: This is the code comment. This method is called close.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $modifycnccID
     */
    public function close($modifycnccID)
    {
        $data = new stdclass();
        if($this->post->result == 'closed'){
            $data->status = 'closed';
            $data->closeBy = $this->app->user->account;
            $data->closeDate = helper::now();
        }
        if($this->post->result == 'feedbacked') $data->status = 'productsuccess';

        $this->dao->update(TABLE_MODIFYCNCC)->data($data)->where('id')->eq($modifycnccID)->exec();
    }

    /**
     * Project: chengfangjinke
     * Method: review
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:45
     * Desc: This is the code comment. This method is called review.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $modifycnccID
     * @return false|void
     */
    public function review($modifycnccID)
    {
        $modifycncc = $this->getByID($modifycnccID);
        //判断当前状态和审核中传入的状态是否一致，以防有同组中其他人修改
        $res = $this->checkAllowReview($modifycncc, $this->post->version, $this->post->reviewStage, $this->app->user->account);
        if(!$res['result']){
            dao::$errors['statusError'] = $res['message'];
            return false;
        }

        $extra  = new stdClass();
        if($modifycncc->reviewStage == 2)
        {
            if(!$this->post->isNeedSystem)
            {
                dao::$errors['isNeedSystem'] = $this->lang->modifycncc->systemEmpty;
                return false;
            }

            $extra = $this->post->isNeedSystem == 'yes' ? true : false;
        }

        $is_all_check_pass = false;
        $result = $this->loadModel('review')->check('modifycncc', $modifycnccID, $modifycncc->version, $this->post->result, $this->post->comment, $modifycncc->reviewStage, $extra, $is_all_check_pass);
        if($result == 'pass')
        {
            if($modifycncc->reviewStage == 2 and $this->post->isNeedSystem == 'no')
            {
                $next = $this->dao->select('id')->from(TABLE_REVIEWNODE)->where('objectType')->eq('modifycncc')
                    ->andWhere('objectID')->eq($modifycnccID)
                    ->andWhere('version')->eq($modifycncc->version)
                    ->andWhere('status')->eq('wait')->orderBy('stage,id')->fetch();
                $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('ignore')->where('id')->eq($next->id)->exec();
                $add = 2;
            }
            else
            {
                $add = 1;
            }

            $status = '';
            /*
            switch($modifycncc->reviewStage)
            {
            case 1:
                $status = 'managersuccess';
                break;
            case 2:
                $status = 'systemsuccess';
                break;
            case 3:
                $status = 'posuccess';
                break;
            case 4:
                $status = 'leadersuccess';
                break;
            case 5:
                $status = 'gmsuccess';
                break;
            case 6:
                $status = 'productsuccess';
                break;
            }

            if($add == 2) $status = 'systemsuccess';
            */

            //下一审核节点
            $nextReviewStage = $modifycncc->reviewStage + $add;
            //下一审核状态
            if(isset($this->lang->modifycncc->reviewBeforeStatusList[$nextReviewStage])){
                $status = $this->lang->modifycncc->reviewBeforeStatusList[$nextReviewStage];
            }
            if($modifycncc->reviewStage == 4 and $modifycncc->level == 3) $status = 'gmsuccess';
            if($modifycncc->reviewStage == 5 and $modifycncc->level == 2) $status = 'gmsuccess';

            $lastDealDate = date('Y-m-d');
            $this->dao->update(TABLE_MODIFYCNCC)->set('reviewStage = reviewStage+' . $add)->set('status')->eq($status)->set('lastDealDate')->eq($lastDealDate)->where('id')->eq($modifycnccID)->exec();
            $this->loadModel('consumed')->record('modifycncc', $modifycnccID, $this->post->consumed, $this->app->user->account, $modifycncc->status, $status, array());

            //审批完后设置为带外部审批
            if($status == 'productsuccess') $this->dao->update(TABLE_MODIFYCNCC)->set('changeStatus')->eq('1')->where('id')->eq($modifycnccID)->exec();

            // level = 3, 不需要部门分管领导和总经理审批
            if($modifycncc->reviewStage == 4 and $modifycncc->level == 3)
            {
                $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('ignore')->where('objectType')->eq('modifycncc')
                    ->andWhere('objectID')->eq($modifycnccID)
                    ->andWhere('version')->eq($modifycncc->version)
                    ->andWhere('status')->eq('wait')->orderBy('stage,id')->limit(2)->exec();
                $this->dao->update(TABLE_MODIFYCNCC)->set('reviewStage = reviewStage+2')->where('id')->eq($modifycnccID)->exec();
            }

            // level = 2, 不需要总经理审批
            if($modifycncc->reviewStage == 5 and $modifycncc->level >= 2)
            {
                $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('ignore')->where('objectType')->eq('modifycncc')
                    ->andWhere('objectID')->eq($modifycnccID)
                    ->andWhere('version')->eq($modifycncc->version)
                    ->andWhere('status')->eq('wait')->orderBy('stage,id')->limit(1)->exec();
                $this->dao->update(TABLE_MODIFYCNCC)->set('reviewStage = reviewStage+1')->where('id')->eq($modifycnccID)->exec();
            }

            $next = $this->dao->select('id')->from(TABLE_REVIEWNODE)->where('objectType')->eq('modifycncc')
                ->andWhere('objectID')->eq($modifycnccID)
                ->andWhere('version')->eq($modifycncc->version)
                ->andWhere('status')->eq('wait')->orderBy('stage,id')->fetch('id');

            if($next)
            {
                $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('pending')->where('id')->eq($next)->exec();
                $this->dao->update(TABLE_REVIEWER)->set('status')->eq('pending')->where('node')->eq($next)->exec();
            }
        }
        elseif($result == 'reject')
        {
            $lastDealDate = date('Y-m-d');
            $this->dao->update(TABLE_MODIFYCNCC)->set('status')->eq('reject')->set('lastDealDate')->eq($lastDealDate)->where('id')->eq($modifycnccID)->exec();
            $this->loadModel('consumed')->record('modifycncc', $modifycnccID, $this->post->consumed, $this->app->user->account, $modifycncc->status, 'reject', array());
        }
    }

    /**
     * Project: chengfangjinke
     * Method: feedback
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:45
     * Desc: This is the code comment. This method is called feedback.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $modifycnccID
     * @return array
     */
    public function feedback($modifycnccID)
    {
        $oldProblem = $this->getByID($modifycnccID);

        $data = fixer::input('post')->get();
        $data->status = 'feedbacked';
        $this->dao->update(TABLE_MODIFYCNCC)->data($data)->where('id')->eq($modifycnccID)->exec();

        return common::createChanges($oldProblem, $data);
    }

    /**
     * Project: chengfangjinke
     * Method: run
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:45
     * Desc: This is the code comment. This method is called run.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $modifycnccID
     */
    public function run($modifycnccID)
    {
        $data = new stdClass();
        $data = fixer::input('post')
            ->join('internalSupply', ',')
            ->add('status', 'closing')
            ->remove('uid,consumed')
            ->stripTags($this->config->modifycncc->editor->run['id'], $this->config->allowedTags)
            ->get();

        $consumed = $_POST['consumed'];
        if(empty($consumed))
        {
            $errors['consumed'] = sprintf($this->lang->modifycncc->emptyObject, $this->lang->modifycncc->consumed );
            return dao::$errors = $errors;
        }
        else
        {
            if(!is_numeric($consumed))
            {
                $errors['consumed'] = sprintf($this->lang->modifycncc->noNumeric, $this->lang->modifycncc->consumed );
                return dao::$errors = $errors;
            }
        }

        $result = $_POST['result'];
        if(empty($result))
        {
            $errors['result'] = sprintf($this->lang->modifycncc->emptyObject, $this->lang->modifycncc->result );
            return dao::$errors = $errors;
        }

        $data = $this->loadModel('file')->processImgURL($data, $this->config->modifycncc->editor->run['id'], $this->post->uid);
        $this->dao->update(TABLE_MODIFYCNCC)->data($data)->where('id')->eq($modifycnccID)->exec();

        $this->loadModel('file')->updateObjectID($this->post->uid, $modifycnccID, 'modifycncc');
        $this->file->saveUpload('modifycncc', $modifycnccID);

        $this->loadModel('consumed')->record('modifycncc', $modifycnccID, $this->post->consumed, $this->app->user->account, 'productsuccess', 'closing', array());
    }

    /**
     * Project: chengfangjinke
     * Method: link
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:46
     * Desc: This is the code comment. This method is called link.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $modifycnccID
     * @return false|void
     */
    public function link($modifycnccID)
    {
        $modifycncc = $this->getByID($modifycnccID);
        //判断当前状态和审核中传入的状态是否一致，以防有同组中其他人修改
        $res = $this->checkAllowReview($modifycncc, $this->post->version,  $this->post->reviewStage, $this->app->user->account);
        if(!$res['result']){
            dao::$errors['statusError'] = $res['message'];
            return false;
        }

        if(!$this->post->release)
        {
            dao::$errors['release'] = $this->lang->modifycncc->releaseEmpty;
            return false;
        }

        if(!$this->post->consumed)
        {
            dao::$errors['consumed'] = $this->lang->modifycncc->consumedEmpty;
            return false;
        }

        $data = new stdClass();
        $data->release      = trim(implode(',', $this->post->release), ',');
        $data->reviewStage  = 1;
        $data->status       = 'cmconfirmed';
        $data->lastDealDate = date('Y-m-d');

        $this->dao->update(TABLE_MODIFYCNCC)->data($data)->autoCheck()->batchCheck($this->config->modifycncc->link->requiredFields, 'notempty')
             ->where('id')->eq($modifycnccID)->exec();
        //一个人审核通过就可以
        $is_all_check_pass = false;
        $this->loadModel('review')->check('modifycncc', $modifycnccID, $modifycncc->version, 'pass', $this->post->comment, 0, null, $is_all_check_pass);

        /* 下个节点设为pending */
        $next = $this->dao->select('id')->from(TABLE_REVIEWNODE)->where('objectType')->eq('modifycncc')
                     ->andWhere('objectID')->eq($modifycnccID)
                     ->andWhere('version')->eq($modifycncc->version)
                     ->andWhere('status')->eq('wait')->orderBy('stage,id')->fetch();
        $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('pending')->where('id')->eq($next->id)->exec();
        $this->dao->update(TABLE_REVIEWER)->set('status')->eq('pending')->where('node')->eq($next->id)->exec();

        $this->loadModel('consumed')->record('modifycncc', $modifycnccID, $this->post->consumed, $this->app->user->account, 'wait', 'cmconfirmed', array());
    }

    /**
     * Project: chengfangjinke
     * Method: isClickable
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:46
     * Desc: This is the code comment. This method is called isClickable.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $modifycncc
     * @param $action
     * @return bool
     */
    public static function isClickable($modifycncc, $action)
    {
        global $app;
        $action = strtolower($action);

        if($action == 'edit') return $modifycncc->status == 'wait' or $modifycncc->status == 'reject';
        if($action == 'reject') {
            $res = (new modifycnccModel())->checkAllowReject($modifycncc);
            return  $res;
        }
        if($action == 'link') return $modifycncc->reviewStage == 0 and strpos(",$modifycncc->reviewers,", ",{$app->user->account},") !== false;
        if($action == 'review') return $modifycncc->reviewStage != 0 and strpos(",$modifycncc->reviewers,", ",{$app->user->account},") !== false and $modifycncc->status != 'productsuccess' and $modifycncc->status != 'reject';
        if($action == 'run') return $modifycncc->status == 'productsuccess';
        if($action == 'close') return $modifycncc->status == 'closing';

        return true;
    }

    /**
     * Send mail.
     *
     * @param  int    $modifycnccID
     * @param  int    $actionID
     * @access public
     * @return void
     */
    public function sendmail($modifycnccID, $actionID)
    {
        $this->loadModel('mail');
        $modifycncc = $this->getById($modifycnccID);
        $users  = $this->loadModel('user')->getPairs('noletter');

        /* 获取后台通知中配置的邮件发信。*/
        $this->app->loadLang('custommail');
        $mailConf   = isset($this->config->global->setModifycnccMail) ? $this->config->global->setModifycnccMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf   = json_decode($mailConf);
        $browseType = 'modifycncc';

        /* 处理邮件发信的标题和日期。*/
        $bestDeal = $this->dao->select('*')->from(TABLE_CONSUMED)->where('objectType')->eq('modifycncc')
            ->andWhere('objectID')->eq($modifycnccID)
            ->orderBy('id_desc')
            ->fetch();
        $bestDate  = empty($bestDeal) ? '' : substr($bestDeal->createdDate, 0, 10);
        $mailTitle = vsprintf($mailConf->mailTitle, $mailConf->variables);

        /* Get action info. */
        $action          = $this->loadModel('action')->getById($actionID);
        $history         = $this->action->getHistory($actionID);
        $action->history = isset($history[$actionID]) ? $history[$actionID] : array();

        /* Get mail content. */
        $oldcwd     = getcwd();
        $modulePath = $this->app->getModulePath($appName = '', 'modifycncc');
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

        $sendUsers = $this->getToAndCcList($modifycncc);
        if(!$sendUsers) return;
        list($toList, $ccList) = $sendUsers;

        /* 处理邮件标题。*/
        //$subject = $this->getSubject($modifycncc);
        $subject = $mailTitle;

        /* Send emails. */
        $this->mail->send($toList, $subject, $mailContent, $ccList);
        if($this->mail->isError()) error_log(join("\n", $this->mail->getError()));
    }

    /**
     * Project: chengfangjinke
     * Method: getToAndCcList
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:46
     * Desc: This is the code comment. This method is called getToAndCcList.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $object
     * @return array
     */
    public function getToAndCcList($object)
    {
        /* Set toList and ccList. */
        $status = $object->status;
        if($status == 'reject'){
            $toList = $object->createdBy;  //创建者
        }else{
            $toList = $this->review->getReviewer('modifycncc', $object->id, $object->version, $object->reviewStage);;
        }
        $ccList = '';
        return array($toList, $ccList);
    }

    /**
     * Get mail subject.
     *
     * @param  object
     * @access public
     * @return string
     */
    public function getSubject($object)
    {
        return $this->lang->modifycncc->common  . '#' . $object->id . '-' . $object->code;
    }

    /**
     * Project: chengfangjinke
     * Method: getToAndCcList
     * User: wangjiurong
     * Year: 2022
     * Date: 2022/02/23
     * Time: 14:44
     * Desc: 检查信息是否允许当前用户审核.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $modifycncc
     * @param $version
     * @param $reviewStage
     * @param $userAccount
     * @return array
     */
    public function checkAllowReview($modifycncc, $version = 1,  $reviewStage = 0, $userAccount = '')
    {
        $res = array(
            'result'  => false,
            'message' => '',
        );
        if(!$modifycncc){
            $res['message'] = $this->lang->common->errorParamId;
            return $res;
        }
        if(!$userAccount){
            $res['message'] = $this->lang->common->errorParamUser;
            return $res;
        }
        //审核节点已经经过
        if(($version != $modifycncc->version) || ($reviewStage != $modifycncc->reviewStage) || ($modifycncc->status == 'reject')){
            $reviewerInfo = $this->loadModel('review')->getReviewedUserInfo('modifycncc', $modifycncc->id, $version, $reviewStage);
            $message = $this->lang->review->statusError;
            if($reviewerInfo){
                $message = str_replace('%', $reviewerInfo->realname, $this->lang->review->statusError);
            }
            $res['message'] = $message;
            return $res;
        }
        //获得当前的的审核人信息
        $reviews =  $this->loadModel('review')->getReviewer('modifycncc', $modifycncc->id, $modifycncc->version, $modifycncc->reviewStage);
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
     *检查审核节点的审核人
     *
     * @param $level
     * @param $nodes
     * @param array $skipReviewNode
     * @return false
     */
    public function checkReviewerNodesInfo($level, $nodes){
        //检查结果
        $checkRes = true;
        $requiredReviewerKeys = $this->lang->modifycncc->requiredReviewerList[$level];
        $nodeKeys = array();
        foreach($nodes as $key => $currentNodes)
        {
            //去除空元素
            $currentNodes = array_filter($currentNodes);
            if(!empty($currentNodes))
            {
                $nodeKeys[] = $key;
            }
        }
        $k = array_search(self::SYSTEMNODE, $requiredReviewerKeys);
        unset($requiredReviewerKeys[$k]);
        //必选审核人，却没有选
        $diffKeys = array_diff($requiredReviewerKeys, $nodeKeys);
        if(!empty($diffKeys)){
            foreach ($diffKeys as  $nodeKey){
                dao::$errors[] =  $this->lang->modifycncc->reviewerEmpty;
                break;
            }
        }

        if(dao::isError()){
            $checkRes = false;
        }
        return $checkRes;
    }

    /**
     * 成功变更数据整合
     * @param int $update
     * @return array
     */
    public function fixModifycnccData($update = 0)
    {

        $postData = fixer::input('post')
            ->join('problemId', ',')
            ->join('demandId', ',')
            ->join('secondorderId', ',')
            ->join('projectPlanId', ',')
            ->join('emergencyBackWay', ',')
            ->join('riskAnalysis', ',')
            ->join('relate', ',')
            ->join('relateNum', ',')
            ->join('node', ',')
            ->join('app', ',')
            ->join('feasibilityAnalysis', ',')
            ->join('productCode', ',')
            ->join('assignProduct', ',')
            ->join('versionNumber', ',')
            ->join('supportPlatform', ',')
            ->join('hardwarePlatform', ',')
            ->join('relatedDemandNum', ',')
            ->join('CBPprojectId', ',')
            ->join('emergencyBackWay', ',')
            ->join('requirementId', ',')
            ->join('fixDefect', ',')
            ->join('leaveDefect', ',')
            ->join('reviewReport', ',')
            ->setIF($this->post->isBusinessCooperate=='1', 'cooperateDepNameList', '')
            ->setIF($this->post->isBusinessCooperate=='1', 'businessCooperateContent', '')
            ->setIF($this->post->isBusinessJudge=='1', 'judgeDep', '')
            ->setIF($this->post->isBusinessJudge=='1', 'judgePlan', '')
            ->setIF($this->post->isBusinessAffect=='1', 'businessAffect', '')
            ->setIF($this->post->property=='2', 'backspaceExpectedStartTime', '')
            ->setIF($this->post->property=='2', 'backspaceExpectedEndTime', '')
            ->setIF($this->post->changeSource!='1', 'controlTableFile', '')
            ->setIF($this->post->changeSource!='1', 'controlTableSteps', '')
//            ->remove('nodes,productCode,assignProduct,versionNumber,supportPlatform,hardwarePlatform,uid,partition,relate,relateNum,emergencyBackWay,riskAnalysis,appOnly')
            //->stripTags($this->config->modifycncc->editor->create['id'], $this->config->allowedTags)
            ->get();
        if($update == 0){
            $fixedData['createdBy'] = $this->app->user->account;
            $fixedData['createdDate'] = helper::now();
            $fixedData['version'] = 1;
//            $fixedData['code'] = $this->getCode();
        }
        $fixedData['editedBy'] = $this->app->user->account;
        $fixedData['editedDate'] = helper::now();
        //$fixedData['problem'] = $postData->problemId ?? 0;
        if(!empty($postData->problemId)){
            $problemIdArray = explode(',', str_replace(' ', '', $postData->problemId));
            $problemIds = ",";
            foreach ($problemIdArray as $item) {
                if(!empty($item)){
                    $problemIds =  $problemIds.$item.",";
                }
            }
            $fixedData['problem'] = $problemIds;
        }else{
            $fixedData['problem'] = '';
        }

        if(!empty($postData->secondorderId)){
            $secondorderIdArray = explode(',', str_replace(' ', '', $postData->secondorderId));
            $secondorderIds = ",";
            foreach ($secondorderIdArray as $item) {
                if(!empty($item)){
                    $secondorderIds =  $secondorderIds.$item.",";
                }
            }
            $fixedData['secondorderId'] = $secondorderIds;
        }else{
            $fixedData['secondorderId'] = '';
        }

        //$fixedData['demand'] = $postData->demandId ?? 0;
        if(!empty($postData->demandId)){
            $demandIdArray = explode(',', str_replace(' ', '', $postData->demandId));
            $demandIds = ",";
            foreach ($demandIdArray as $item) {
                if(!empty($item)){
                    $demandIds =  $demandIds.$item.",";
                }
            }
            $fixedData['demand'] = $demandIds;
        }else{
            $fixedData['demand'] = '';
        }
        $fixedData['project'] = $postData->projectPlanId ?? 0;
        $fixedData['productCode'] = $postData->productInfoCode ?? '';
        $fixedData['emergencyBackWay'] = $postData->emergencyBackWay ?? '';
        $fixedData['riskAnalysis'] = $postData->riskAnalysis ?? '';
//        $fixedData['relate'] = $postData->relate ?? '';
//        $fixedData['relateNum'] = $postData->relateNum ?? '';
        $fixedData['node'] = $postData->node ?? '';
        //$fixedData['relatedDemandNum'] = $postData->requirementId ?? '';
        if(!empty($postData->requirementId)){
            $requirementIdArray = explode(',', str_replace(' ', '', $postData->requirementId));
            $requirementIds = ",";
            foreach ($requirementIdArray as $item) {
                if(!empty($item)){
                    $requirementIds =  $requirementIds.$item.",";
                }
            }
            $fixedData['relatedDemandNum'] = $requirementIds;
        }else{
            $fixedData['relatedDemandNum'] = '';
        }
        $fixedData['CNCCprojectIdUnique'] = $postData->CBPprojectId ?? '';
        //$fixedData['feasibilityAnalysis'] = $postData->feasibilityAnalysis ?? '';
        if(!empty($postData->feasibilityAnalysis)){
            $feasibilityAnalysisArray = explode(',', str_replace(' ', '', $postData->feasibilityAnalysis));
            $feasibilityAnalysiss = ",";
            foreach ($feasibilityAnalysisArray as $item) {
                if(!empty($item)){
                    $feasibilityAnalysiss =  $feasibilityAnalysiss.$item.",";
                }
            }
            $fixedData['feasibilityAnalysis'] = $feasibilityAnalysiss;
        }else{
            $fixedData['feasibilityAnalysis'] = '';
        }
        $fixedData['fixType'] = $postData->implementationForm ?? '';
        $fixedData['level'] = $postData->level ?? '';
        $fixedData['productRegistrationCode'] = "";
        if(!empty($postData->productEnrollId)){
            $fixedData['productRegistrationCode'] =  $this->loadModel('productenroll')->getCodeById($postData->productEnrollId);
        }
        $fixedData['mode'] = $postData->mode ?? '';
        //$fixedData['appOnly'] = $postData->appOnly ?? '';
        $fixedData['changeSource'] = $postData->changeSource ?? '';
        $fixedData['changeStage'] = $postData->changeStage ?? '';
        $fixedData['implementModality'] = $postData->implementModality ?? '';
        $fixedData['type'] = $postData->type ?? '';
        $fixedData['isBusinessCooperate'] = $postData->isBusinessCooperate ?? '';
        $fixedData['isBusinessJudge'] = $postData->isBusinessJudge ?? '';
        $fixedData['isBusinessAffect'] = $postData->isBusinessAffect ?? '';
        $fixedData['property'] = $postData->property ?? '';
        $fixedData['isAppend'] = $postData->isAppend ?? '';
        $fixedData['desc'] = $postData->desc ?? '';
        $fixedData['planBegin'] = $postData->planBegin ?? '';
        $fixedData['planEnd'] = $postData->planEnd ?? '';
        $fixedData['target'] = $postData->target ?? '';
        $fixedData['reason'] = $postData->reason ?? '';
        $fixedData['changeContentAndMethod'] = $postData->changeContentAndMethod  ?? '';
        $fixedData['step'] = $postData->step ?? '';
        $fixedData['techniqueCheck'] = $postData->techniqueCheck ?? '';
        $fixedData['test'] = $postData->test ?? '';
        $fixedData['checkList'] = $postData->checkList ?? '';
        $fixedData['cooperateDepNameList'] = $postData->cooperateDepNameList ?? '';
        $fixedData['businessCooperateContent'] = $postData->businessCooperateContent ?? '';
        $fixedData['judgeDep'] = $postData->judgeDep ?? '';
        $fixedData['judgePlan'] = $postData->judgePlan ?? '';
        $fixedData['controlTableFile'] = trim($postData->controlTableFile) ?? '';
        $fixedData['controlTableSteps'] = trim($postData->controlTableSteps) ?? '';
        $fixedData['risk'] = $postData->risk ?? '';
        $fixedData['effect'] = $postData->effect ?? '';
        $fixedData['businessFunctionAffect'] = $postData->businessFunctionAffect ?? '';
        $fixedData['backupDataCenterChangeSyncDesc'] = $postData->backupDataCenterChangeSyncDesc ?? '';
        $fixedData['emergencyManageAffect'] = $postData->emergencyManageAffect ?? '';
        $fixedData['businessAffect'] = $postData->businessAffect ?? '';
        $fixedData['benchmarkVerificationType'] = $postData->benchmarkVerificationType ?? '';
        $fixedData['verificationResults'] = $postData->verificationResults ?? '';
        $fixedData['applyUsercontact'] = $postData->applyUsercontact ?? '';
        $fixedData['classify'] = $postData->classify ?? 0;
        $fixedData['status']    = $postData->status ?? "waitsubmitted";
        $fixedData['pushStatus']    = 0;
        $fixedData['backspaceExpectedStartTime']    = $postData->backspaceExpectedStartTime ?? "";
        $fixedData['backspaceExpectedEndTime']    = $postData->backspaceExpectedEndTime ?? "";
        $fixedData['reasonCNCC']    = "";
        $fixedData['feedbackDate']    = "";
        $fixedData['operationType']    = $postData->operationType ?? "1";
        $fixedData['fixDefect'] = $postData->fixDefect ?? '';
        $fixedData['leaveDefect'] = $postData->leaveDefect ?? '';
        $fixedData['isReview'] = $postData->isReview ?? 0;
        $fixedData['aadsReason'] = '';
        if (in_array($fixedData['implementModality'],[1,3,6])){
            $fixedData['aadsReason'] = $postData->aadsReason;
        }
        $fixedData['urgentSource'] = '';
        $fixedData['urgentReason'] = '';
        if ($this->post->type == '1'){
            $fixedData['urgentSource'] = $postData->urgentSource;
            $fixedData['urgentReason'] = $postData->urgentReason;
        }
        $fixedData['isMakeAmends']         = $postData->isMakeAmends;
        $fixedData['actualDeliveryTime']   = $postData->isMakeAmends == 'yes' ? $postData->actualDeliveryTime : null;

        if ($fixedData['level'] != 1){
            $fixedData['isReview'] = 0;
            $fixedData['reviewReport'] = '';
            $postData->isReview = 0;
        }
        if($postData->isReview == 1){
            $this->config->modifycncc->create->requiredFields = $this->config->modifycncc->create->requiredFields.',isReview,reviewReport';
            //$fixedData['isReviewPass'] = $postData->isReviewPass ?? '';
            $fixedData['reviewReport'] = trim($postData->reviewReport,',') ?? '';
        }else{
            //$fixedData['isReviewPass'] = '';
            $fixedData['reviewReport'] = '';
        }
        if ($fixedData['isReview'] == 2){
            $fixedData['reviewReport'] = '';
        }
        if(!empty($postData->app)){
            $appArray = explode(',', str_replace(' ', '', $postData->app));
            $apps = ",";
            foreach ($appArray as $item) {
                if(!empty($item)){
                    $apps =  $apps.$item.",";
                }
            }
            $fixedData['belongedApp']= $apps;
        }else{
            $fixedData['belongedApp'] = '';
        }
        $fixedData['applyReasonOutWindow']          = $postData->applyReasonOutWindow ?? '';
        $fixedData['keyGuaranteePeriodApplyReason'] = $postData->keyGuaranteePeriodApplyReason ?? '';
        $fixedData['changeForm']                    = $postData->changeForm ?? '';
        $fixedData['changeImpactAnalysis']          = $postData->changeImpactAnalysis ?? '';
        $fixedData['automationTools']               = '';
        if (in_array($fixedData['implementModality'],[4,5])){
            $fixedData['automationTools'] = $postData->automationTools;
        }

        /*if(!empty($postData->productId)){
            $productIdArray = $postData->productId;
            $appArray = array();
            foreach ($productIdArray as $item) {
                if(!empty($item)){
                    $product = $this->dao->select('line,app')->from(TABLE_PRODUCT)
                        ->where('id')->eq($item)
                        ->fetch();
                    if(!in_array($product->app, $appArray)){
                        array_push($appArray, $product->app);
                    }
                }
            }
            $fixedData['belongedApp'] = implode(',',$appArray);
        }else{
            $fixedData['belongedApp'] = '';
        }*/
        if ($postData->issubmit != 'save'){
            $this->checkParams($fixedData, $this->config->modifycncc->create->requiredFields);
        }
        return $fixedData;
    }

    /**
     * 对外交付创建方法
     * @return array
     */
    public function createByData($fixModifycnccData)
    {
        if ($_POST['issubmit'] != 'save'){
            //检查审核信息
            $checkRes = $this->checkReviewerNodesInfo($this->post->level, $this->post->nodes);

            //有错误返回
            if(!$checkRes || dao::isError()){
                return dao::$errors;
            }
            // 判断工时
           /* $consumed = $this->post->consumed;
            if(empty($consumed))
            {
                return dao::$errors['consumed'] = $this->lang->modifycncc->emptyConsumed;
            }
            if(!is_numeric($this->post->consumed)){
                return dao::$errors['consumed'] = $this->lang->modifycncc->consumedNumber;
            }*/
            //变更可行性分析
            if(!$this->post->feasibilityAnalysis){
                return dao::$errors['feasibilityAnalysis'] = $this->lang->modifycncc->emptyFeasibilityAnalysis;
            }
            if(!$this->post->node){
                return dao::$errors['node'] = $this->lang->modifycncc->emptyNode;
            }
            if ($this->post->changeStage == 2){
                foreach ($this->post->relate as $index => $relation) {
                    if ($relation == '' || $this->post->relateNum[$index] == '') {
                        return dao::$errors['relate'] = $this->lang->modifycncc->emptyRelate;
                    }
                }
            }
        }

        $data = (object)$fixModifycnccData;
        /* 处理系统和分区 */
        // $isNPC = false;
        // $NPCKey = array_keys($this->lang->modifycncc->nodeNPCList);

        // foreach($this->post->node as $item)
        // {
        //     if(in_array($item, $NPCKey)){
        //         $isNPC = true;
        //     }
        // }
        // if(!$isNPC&&!$this->post->appOnly){
        //     return dao::$errors[] = $this->lang->modifycncc->emptyApp ;
        // }
        // if(!$isNPC){
        //     $transformApp = array();
        //     foreach($this->post->appOnly as $index => $app){
        //         $transformApp[$index] = array($app);
        //     }
        //     $applicationInfo = $this->loadModel('application')->getapplicationInfo();
        //     $as = [];
        //     foreach($this->post->appOnly as $apptype)
        //     {
        //         if(!$apptype) continue;
        //         $as[] = zget($applicationInfo, $apptype,"",$applicationInfo[$apptype]->isPayment);
        //     }
        //     $applicationtype = implode(',', $as);
        //     if(!empty($applicationtype))$applicationtype=",".$applicationtype;
        //     $data->isPayment = $applicationtype;
        //     $data->app = json_encode($transformApp);
        // }
        // else
        // {
            $appPartition = '[';
            foreach ($this->post->appmodify as $index=>$app)
            {
                if($index!=0)
                {
                    $appPartition .= ',';
                }
                if(empty($this->post->partition[$index])){
                    $appPartition .= '["'.$app.'"]';
                }else{
                    foreach ($this->post->partition[$index] as $innerIndex=>$partition)
                    {
                        if($innerIndex!=0)
                        {
                            $appPartition .= ',';
                        }
                        $appPartition .= '["'.$app.'"/"'.$partition.'"]';
                    }
                }
            }
            $appPartition .= ']';

            //2022-10-12 shixuyang 生产变更单业务系统和分区长度超过限制
            if(strlen($appPartition)>5000 && $_POST['issubmit'] != 'save'){
                return dao::$errors[] = $this->lang->modifycncc->appOverLength;
            }

            $data->app = $appPartition;
        // }

        /* 处理风险分析和应急处置 */
        $isNull = false;
        foreach ($this->post->emergencyBackWay as $key => $emergencyBackWay)
        {
            if($emergencyBackWay == '' or $this->post->riskAnalysis[$key] == '')
            {
                $isNull = true;
            }
        }
        if($isNull && $_POST['issubmit'] != 'save')
        {
            return dao::$errors[] = $this->lang->modifycncc->emptyRiskAnalysisEmergencyHandle;
        }
        else
        {
            $riskAnalysisEmergencyHandle = array();
            for($i=0;$i<count($this->post->emergencyBackWay);++$i)
            {
                $obj = new stdclass();
                $obj->emergencyBackWay = $this->post->emergencyBackWay[$i];
                $obj->riskAnalysis = $this->post->riskAnalysis[$i];
                $riskAnalysisEmergencyHandle[$i] = $obj;
            }
            $data->riskAnalysisEmergencyHandle = json_encode($riskAnalysisEmergencyHandle);
        }


        /* 判断是否关联了问题单或者需求单 */
        $flag = false;
        foreach($this->post->problemId as $problem)
        {
            if(!empty($problem)) $flag = true;
        }
        foreach($this->post->demandId  as $demand)
        {
            if(!empty($demand))  $flag = true;
        }
        foreach($this->post->secondorderId  as $secondorder)
        {
            if(!empty($secondorder))  $flag = true;
        }
        if(!$flag && $_POST['issubmit'] != 'save') return dao::$errors[] = $this->lang->modifycncc->emptyDemandProblem;

        /* 判断产品编号是否需要处理。*/
        /*$codeList = array();
        if($this->post->productCode)
        {
            foreach($this->post->productCode as $codeIndex => $code)
            {
                if(empty($code)) return dao::$errors[] = $this->lang->modifycncc->emptyProductCode;

                if(empty($this->post->assignProduct[$codeIndex]))    return dao::$errors[] = $this->lang->modifycncc->emptyAssignProduct;
                if(empty($this->post->versionNumber[$codeIndex]))    return dao::$errors[] = $this->lang->modifycncc->emptyVersionNumber;
                if(empty($this->post->supportPlatform[$codeIndex]))  return dao::$errors[] = $this->lang->modifycncc->emptySupportPlatform;
                //if(empty($this->post->hardwarePlatform[$codeIndex])) return dao::$errors[] = $this->lang->modifycncc->emptyHardwarePlatform;
                $codeList[] = array(
                    'assignProduct'    => $this->post->assignProduct[$codeIndex],
                    'versionNumber'    => $this->post->versionNumber[$codeIndex],
                    'supportPlatform'  => $this->post->supportPlatform[$codeIndex],
                    'hardwarePlatform' => $this->post->hardwarePlatform[$codeIndex]
                );
            }
        }*/
        //产品信息
        $codeProduct = array();
        if($this->post->productId){
            $productObj = $this->loadModel('product')->getByID($this->post->productId[0]);
            if($this->post->productEnrollId){
                $productenrollObj  =  $this->loadModel('productenroll')->getByID($this->post->productEnrollId);
            }
            $codeProduct = array(
                'assignProduct'    => $this->post->productId[0],
                'versionNumber'    => !empty($productenrollObj) ? $productenrollObj->versionNum : '',
                'supportPlatform'  => $productObj->os,
                'hardwarePlatform' => $productObj->arch
            );
        }
        //$data->productCode = $this->post->productInfoCode;

        $data->project              = trim($data->project, ',');

        $data->status      = 'waitsubmitted';
        $data->createdBy   = $this->app->user->account;
        $data->createdDate = helper::now();
        $data->createdDept = $this->app->user->dept;
        $data->editedBy    = $this->app->user->account;
        $data->editedDate  = helper::now();

        /* isAppend不取值的时候给1(false) */
        $data->isAppend = $this->post->isAppend ? : 1;

        unset($data->emergencyBackWay); //这两个已经在riskAnalysisEmergencyHandle存json
        unset($data->riskAnalysis);
        //$data = $this->loadModel('file')->processImgURL($data, $this->config->modifycncc->editor->create['id'], $this->post->uid);
        $this->dao->insert(TABLE_MODIFYCNCC)->data($data)
            ->checkIF($data->isBusinessCooperate=='2','cooperateDepNameList','notempty')
            ->checkIF($data->isBusinessCooperate=='2','businessCooperateContent','notempty')
            ->checkIF($data->isBusinessJudge=='2','judgeDep','notempty')
            ->checkIF($data->isBusinessJudge=='2','judgePlan','notempty')
            ->checkIF($data->isBusinessAffect=='2','businessAffect','notempty')
            ->checkIF($data->property=='1','backspaceExpectedStartTime','notempty')
            ->checkIF($data->property=='1','backspaceExpectedEndTime','notempty')
            ->checkIF($data->changeSource=='1','controlTableFile','notempty')
            ->checkIF($data->changeSource=='1','controlTableSteps','notempty')
            ->batchCheckIF($_POST['issubmit'] != 'save',$this->config->modifycncc->create->requiredFields, 'notempty')->exec();
        $modifycnccID = $this->dao->lastInsertId();

        if(!dao::isError())
        {
            $this->loadModel('file')->updateObjectID($this->post->uid, $modifycnccID, 'modifycncc');
            $this->file->saveUpload('modifycncc', $modifycnccID);


            /* Record the relationship between the associated issue and the requisition. */
            $this->loadModel('secondline')->saveRelationship($modifycnccID, 'modifycncc', $data->secondorderId, 'secondorder');
            $this->loadModel('secondline')->saveRelationship($modifycnccID, 'modifycncc', $data->problem, 'problem');
            $this->secondline->saveRelationship($modifycnccID, 'modifycncc', $data->demand, 'demand');
            $this->secondline->saveRelationship($modifycnccID, 'projectModifycncc', $data->project, 'project');
            $this->secondline->saveRelationship($modifycnccID, 'modifycncc', $data->relatedDemandNum, 'requirement');
            if($data->problem)
            {
                $problemIdList = $data->problem;
                if(!is_array($problemIdList)) $problemIdList = explode(',', $problemIdList);
                foreach($problemIdList as $relationID)
                {
                    if(empty($relationID)) continue;
                    $this->secondline->saveRelationship($relationID, 'projectProblem', $data->project, 'project');
                }
            }
            if($data->demand)
            {
                $demandIdList = $data->demand;
                if(!is_array($demandIdList)) $demandIdList = explode(',', $demandIdList);
                foreach($demandIdList as $relationID)
                {
                    if(empty($relationID)) continue;
                    $this->secondline->saveRelationship($relationID, 'projectDemand', $data->project, 'project');
                }
            }

            // 处理关联变更单关系
            foreach ($this->post->relate as $index => $relation)
            {
                if($relation !='' and $this->post->relateNum[$index] != '') {
                    $this->secondline->saveModifycnccRelationship($modifycnccID, $this->post->relateNum[$index], $relation);
                    if ($relation === 'before') {
                        $this->secondline->saveModifycnccRelationship($this->post->relateNum[$index], $modifycnccID, 'after');
                    } elseif ($relation === 'after') {
                        $this->secondline->saveModifycnccRelationship($this->post->relateNum[$index], $modifycnccID, 'before');
                    } elseif ($relation === 'test') {
                        $this->secondline->saveModifycnccRelationship($this->post->relateNum[$index], $modifycnccID, 'generalization');
                    } elseif ($relation === 'generalization') {
                        $this->secondline->saveModifycnccRelationship($this->post->relateNum[$index], $modifycnccID, 'test');
                    } elseif ($relation === 'relation') {
                        $this->secondline->saveModifycnccRelationship($this->post->relateNum[$index], $modifycnccID, 'relation');
                    } elseif ($relation === 'synchronous'){
                        $this->secondline->saveModifycnccRelationship($this->post->relateNum[$index], $modifycnccID, 'synchronous');
                    } elseif ($relation === 'include'){
                        $this->secondline->saveModifycnccRelationship($this->post->relateNum[$index], $modifycnccID, 'beInclude');
                    }
                }
            }

            $number = $this->dao->select('count(id) c')->from(TABLE_MODIFYCNCC)->where('code')->like("CFIT-CQ-" . date('Ymd-') ."%")->fetch('c') + 1;
            $code   = 'CFIT-CQ-' . date('Ymd-') . sprintf('%02d', $number);

            $this->dao->update(TABLE_MODIFYCNCC)->set('code')->eq($code)->where('id')->eq($modifycnccID)->exec();

            $recordCode = new stdClass();
            $recordCode->product = $codeProduct['assignProduct'];
            $recordCode->modifycncc  = $modifycnccID;
            $recordCode->code    = json_encode($codeProduct);
            $this->dao->insert(TABLE_PRODUCTCODE)->data($recordCode)->exec();

            // 绑定缺陷
            $defectIds =  array_merge($_POST['fixDefect'], $_POST['leaveDefect']);
            $defects = $this->dao->select('id,modifycnccId,ifTest')->from(TABLE_DEFECT)->where('id')->in($defectIds)->fetchAll();
            if($defects) {
                foreach ($defects as $defect) {
                    $data = new stdClass();
                    if (!isset($defect->ifTest)) $data->ifTest = '0';
                    $data->modifycnccId = $defect->modifycnccId . ',' . $modifycnccID;
                    $this->dao->update(TABLE_DEFECT)->data($data)->where('id')->eq($defect->id)->exec();
                }
            }
        }

        return $modifycnccID;
    }

    /**
     * 检查必填项
     * @param $data
     */
    private function checkParams($data, $fields)
    {
        $fieldArray = explode(',', str_replace(' ', '', $fields));
        foreach ($fieldArray as $item)
        {
            if(empty(strip_tags(str_replace("&nbsp;"," ",htmlspecialchars_decode($data[$item]))))){
                $itemName = $this->lang->modifycncc->$item ?? $item;
                dao::$errors[$item] =  sprintf($this->lang->modifycncc->emptyObject, $itemName);
            }
        }
        if(mb_strlen(strip_tags(str_replace("&nbsp;"," ",htmlspecialchars_decode($data['effect'])))) > 200){
            dao::$errors['effect'] =  '【给生产系统带来的影响变化】长度不能超过200';
        }
        if(mb_strlen(strip_tags(str_replace("&nbsp;"," ",htmlspecialchars_decode($data['businessFunctionAffect'])))) > 200){
            dao::$errors['businessFunctionAffect'] =  '【给业务功能带来的影响】长度不能超过200';
        }
        if(mb_strlen(strip_tags(str_replace("&nbsp;"," ",htmlspecialchars_decode($data['backupDataCenterChangeSyncDesc'])))) > 200){
            dao::$errors['backupDataCenterChangeSyncDesc'] =  '【主备数据中心变更同步情况说明】长度不能超过200';
        }
        if(mb_strlen(strip_tags(str_replace("&nbsp;"," ",htmlspecialchars_decode($data['emergencyManageAffect'])))) > 200){
            dao::$errors['emergencyManageAffect'] =  '【对应急处置策略的影响（对故障处置策略自动化切换等的影响）】长度不能超过200';
        }
        if(mb_strlen(strip_tags(str_replace("&nbsp;"," ",htmlspecialchars_decode($data['changeImpactAnalysis'])))) > 2000){
            dao::$errors['changeImpactAnalysis'] =  '【变更关联影响分析】长度不能超过2000';
        }
        if($data['planBegin'] >= $data['planEnd']){
            dao::$errors[] =  '【预计开始时间】应该在【预计结束时间】之前';
        }

        if($data['property'] == 1){
            if($data['backspaceExpectedStartTime'] >= $data['backspaceExpectedEndTime']){
                dao::$errors[] =  '【预计回退开始时间】应该在【预计回退结束时间】之前';
            }
        }
        if(($data['changeSource'] == 1 || $data['changeSource'] == 2) && empty($data['CNCCprojectIdUnique'])){
            dao::$errors[] =  "变更来源是项目投产或项目实施的时候，所属CBP项目字段必填";
        }
         if($data['changeSource'] == 1 && empty(strip_tags(str_replace("&nbsp;"," ",htmlspecialchars_decode($data['controlTableSteps']))))){
             dao::$errors['controlTableSteps'] =  sprintf($this->lang->modifycncc->emptyObject, $this->lang->modifycncc->controlTableSteps);
         }
         if($data['isBusinessAffect'] == 2 && empty(strip_tags(str_replace("&nbsp;"," ",htmlspecialchars_decode($data['businessAffect']))))){
             dao::$errors['businessAffect'] =  sprintf($this->lang->modifycncc->emptyObject, $this->lang->modifycncc->businessAffect);
         }
         if($data['isBusinessJudge'] == 2 && empty(strip_tags(str_replace("&nbsp;"," ",htmlspecialchars_decode($data['judgePlan']))))){
             dao::$errors['judgePlan'] =  sprintf($this->lang->modifycncc->emptyObject, $this->lang->modifycncc->judgePlan);
         }
         if($data['isBusinessCooperate'] == 2 && empty(strip_tags(str_replace("&nbsp;"," ",htmlspecialchars_decode($data['businessCooperateContent']))))){
             dao::$errors['businessCooperateContent'] =  sprintf($this->lang->modifycncc->emptyObject, $this->lang->modifycncc->businessCooperateContent);
         }

        //单独判断业务系统
        // $isNPC = false;
        // $NPCKey = array_keys($this->lang->modifycncc->nodeNPCList);
        if(!empty($data['node'])){
            // foreach($this->post->node as $item)
            // {
            //     if(in_array($item, $NPCKey)){
            //         $isNPC = true;
            //     }
            // }
            if(!$this->post->appmodify){
                dao::$errors['appmodify'] = sprintf($this->lang->modifycncc->emptyObject, '涉及业务系统');
            }
            // if($isNPC){
                foreach ($this->post->appmodify as $index=>$app){
                    if(empty($this->post->partition[$index])){
                        dao::$errors['partition'] = sprintf($this->lang->modifycncc->emptyObject, '变更参数-分区');
                    }
                }
            // }
        }
        /* 处理风险分析和应急处置 */
        $isNull = false;
        if(empty($data['emergencyBackWay']) || empty($data['riskAnalysis'])){
            dao::$errors['riskAnalysis'] = sprintf($this->lang->modifycncc->emptyObject, '处理风险分析和应急处置');
        }else{
            foreach ($this->post->emergencyBackWay as $key => $emergencyBackWay)
            {
                if($emergencyBackWay == '' or $this->post->riskAnalysis[$key] == '')
                {
                    $isNull = true;
                }
            }
            if($isNull)
            {
                dao::$errors['riskAnalysis'] = sprintf($this->lang->modifycncc->emptyObject, '处理风险分析和应急处置');
            }
        }
    }

    public function getCodePairs()
    {
        return $this->dao->select('id,code')->from(TABLE_MODIFYCNCC)
//            ->where('deleted')->ne(1)
            ->orderBy('id_desc')
            ->fetchPairs();
    }

    public function getCodeGiteePairs()
    {
      return $this->dao->select('id,concat(code,"（",IFNULL(feedBackId,"")  ,"）")')->from(TABLE_MODIFYCNCC)
        ->where('deleted')->ne(1)
        ->orderBy('id_desc')
        ->fetchPairs('id');
    }

    //是否介质满住条件
    public function checkMediaFails($outwardDelivery)
    {
        /** @var outwarddeliveryModel $outwardDeliveryModel */
        $outwardDeliveryModel = $this->loadModel('outwarddelivery');
        $reviewers = $this->lang->outwarddelivery->apiDealUserList['userAccount'];
        $pushFailMax = $this->loadModel('release')->getFailsQz($outwardDelivery->release);
        if($pushFailMax){ //介质推送失败多次 直接跳过 这里需要把子表单状态改了
            $action                 = 'qingzongsynfailed';
            $outwardDeliveryModel->setOutwardDeliverySyncFail($outwardDelivery->id); //更改同步失败状态
            $this->loadModel('consumed')->record('outwarddelivery', $outwardDelivery->id, '0', 'guestjk', 'waitqingzong', 'qingzongsynfailed', array(),'介质多次同步失败');
            $this->loadModel('action')->create('outwarddelivery', $outwardDelivery->id, $action, '介质多次同步失败','', $reviewers);

            $update['pushStatus']   = -1;
            $update['status']       = 'qingzongsynfailed'; //3次失败后 改为同步失败 不再重复发
            $this->dao->update(TABLE_MODIFYCNCC)->data($update)->where('id')->eq($outwardDelivery->modifycnccId)->exec();
            $this->loadModel('action')->create('modifycncc', $outwardDelivery->modifycnccId, $action, '介质同步失败多次','guestjk');

            return false;
        }
        //是否有介质未处理完 未处理完不推单子 （同时如果介质没标注需要推送 标注介质需要推送 pushStatusQz =1）
        if($this->loadModel('release')->ifReleasesPushed($outwardDelivery->release) == false) {
            return false;
        }
        return true;
    }

    /**
     * 查看没推送的生产变更所关联的测试申请 和 产品交付单 是否通过
     * 如通过 将关联测试申请&产品登记的生产变更单推送出去
     */
    public function getUnPushedAndPush()
    {
        /** @var outwarddeliveryModel $outwardDeliveryModel */
        $outwardDeliveryModel = $this->loadModel('outwarddelivery');
        //选取没推送成功的生产变更
        $unPushedModifycnccIds  = $this->dao->select('id,pushFailTimes')->from(TABLE_MODIFYCNCC)->where('status')->eq('waitqingzong')->andwhere('pushStatus')->notin([1,-1])->andwhere('pushFailTimes')->le(10)->fetchALl('id');
        if(empty($unPushedModifycnccIds)) return [];
        //取本对外交付关联的 测试申请和产品登记
        $outwardDeliveryArray   =  $this->dao->select('id, testingRequestId, productEnrollId, modifycnccId, version, reviewStage,`release`')->from(TABLE_OUTWARDDELIVERY)->where('isNewModifycncc')->eq(1)->andwhere('modifycnccId')->in(array_keys($unPushedModifycnccIds))->fetchALl();
        //测试申请通过的
        $checkList              = [];
        //全部通过的（测试申请和产品登记）
        $allPassedList          = [];
        $outwardDeliveryList    = [];

        foreach ($outwardDeliveryArray as $outwardDelivery)
        {
            //没有关联测试申请和产品登记可以直接发
            if(empty($outwardDelivery->testingRequestId) && empty($outwardDelivery->productEnrollId)) {
                if($this->checkMediaFails($outwardDelivery) == false) continue; //是否介质满住条件
                $allPassedList[$outwardDelivery->modifycnccId] = $outwardDelivery->modifycnccId;
                continue;
            }
            //用于查看该生产变更前置的测试申请是否通过
            $checkList[$outwardDelivery->modifycnccId]['testingRequestPassed'] = 0;
            //测试申请单号
            if($outwardDelivery->testingRequestId){
                //查看是否通过
                $passedTestingRequestId = $this->dao->select('id')->from(TABLE_TESTINGREQUEST)->where('cardStatus')->eq(1)->andWhere('id')->eq($outwardDelivery->testingRequestId)->fetch('id');
                //如果通过标记该生产变更的测试申请通过了
                if($passedTestingRequestId) $checkList[$outwardDelivery->modifycnccId]['testingRequestPassed'] = 1; //该生产变更需要检查测试申请
                //如果没有产品等级 直接通过
                if(empty($outwardDelivery->productEnrollId) && $passedTestingRequestId){
                    if($this->checkMediaFails($outwardDelivery) == false) continue; //是否介质满住条件
                    $allPassedList[$outwardDelivery->modifycnccId] = $outwardDelivery->modifycnccId; //给个key防止重复
                    $outwardDeliveryList[$outwardDelivery->modifycnccId] = $outwardDelivery; //以productid为key 的对外交付信息
                }
            }
            //查产品登记单号
            if($outwardDelivery->productEnrollId){
                $passedProductEnrollId = $this->dao->select('id')->from(TABLE_PRODUCTENROLL)->where('id')->eq($outwardDelivery->productEnrollId)->andwhere('status')->in('giteepass,emispass')->fetch('id');
                //如果测试申请已通过 或者 没有测试申请 通过
                if($passedProductEnrollId && ($checkList[$outwardDelivery->modifycnccId]['testingRequestPassed'] == 1 || empty($outwardDelivery->testingRequestId)) ){
                    if($this->checkMediaFails($outwardDelivery )== false) continue; //是否介质满住条件
                    $allPassedList[$outwardDelivery->modifycnccId] = $outwardDelivery->modifycnccId;
                    $outwardDeliveryList[$outwardDelivery->modifycnccId] = $outwardDelivery; //以productid为key 的对外交付信息
                }
            }
        }
        $res = [];

        $this->loadModel('problem');
        foreach ($allPassedList as $modifycnccId){

            $result                 = $this->pushmodifycncc($modifycnccId);
            $common = '';
            if ($result == 'md5empty'){
                $common = 'MD5值不能为空';
                $result = '';
            }
            $outwarddelivery        = $this->dao->select("*")->from(TABLE_OUTWARDDELIVERY)->where('modifycnccId')->eq($modifycnccId)->fetch();

            $run['modifycnccId']    = $modifycnccId;
            $run['response']        = $result;
            if (!empty($result)) { //有结果请求成功
                $resultData             = json_decode($result);
                // || $resultData->isSave == 1
                if ($resultData->code == '200') { //业务成功 兼容再次请求
                    $update['pushStatus']   = 1; //标记推送成功
                    $update['status']       = 'centrepmreview'; //更新状态
                    $this->loadModel('consumed')->record('outwarddelivery', $outwarddelivery->id, '0', 'guestjk', 'waitqingzong', 'centrepmreview', array(),'生产变更单');
                    $this->dao->update(TABLE_OUTWARDDELIVERY)->set('status')->eq('centrepmreview')->set('dealUser')->eq('guestcn')->where('id')->eq($outwarddelivery->id)->exec();
                    $this->loadModel('demand')->changeBySecondLineV4($outwardDelivery->id,'outwarddelivery');
                }elseif(isset($resultData->code) && $resultData->code != 200){
                    $update['pushStatus']   = -1;  //已返回业务错误 不重复发
                    $update['status']       = 'qingzongsynfailed';
                    $outwardDeliveryModel->setOutwardDeliverySyncFail($outwarddelivery->id); //更改同步失败状态
                    // 发送邮件
                    $this->loadModel('action')->create('outwarddelivery', $outwarddelivery->id, 'qingzongsynfailed', '', '', 'guestjk');
                    $this->loadModel('action')->create('modifycncc', (int)$modifycnccId, 'qingzongsynfailed', '', '', 'guestjk');
                    $this->loadModel('consumed')->record('outwarddelivery', $outwarddelivery->id, '0', 'guestjk', 'waitqingzong', 'qingzongsynfailed', array(),'生产变更单');
                } else {
                    //推送成功其他错误 重发 如4xx 5xx
                    $update['pushStatus']       = 2;
                    $update['pushFailTimes']    = $unPushedModifycnccIds[$modifycnccId]->pushFailTimes + 1; //失败次数+1
                    if($update['pushFailTimes'] >= 3)
                    {
                        $update['pushStatus']   = -1;
                        $update['status']       = 'qingzongsynfailed'; //十次失败后 改为同步失败 不再重复发
                        $outwardDeliveryModel->setOutwardDeliverySyncFail($outwarddelivery->id); //更改同步失败状态
                        // 发送邮件
                        $this->loadModel('action')->create('outwarddelivery', $outwarddelivery->id, 'qingzongsynfailed', $resultData->message, '', 'guestjk');
                        $this->loadModel('action')->create('modifycncc', (int)$modifycnccId, 'qingzongsynfailed', '', '', 'guestjk');
                        $this->loadModel('consumed')->record('outwarddelivery', $outwarddelivery->id, '0', 'guestjk', 'waitqingzong', 'qingzongsynfailed', array(),'生产变更单');
                        $this->dao->update(TABLE_MODIFYCNCC)->data($update)->where('id')->eq((int)$modifycnccId)->exec();
                    }
                }
            }else {
                $update['pushStatus']   = -1;  //数据问题 没发出去 不重发
                $update['status']       = 'qingzongsynfailed';
                $outwardDeliveryModel->setOutwardDeliverySyncFail($outwarddelivery->id); //更改同步失败状态
                //// 发送邮件
                $this->loadModel('action')->create('modifycncc', (int)$modifycnccId, 'qingzongsynfailed', $common, '', 'guestjk');
                $this->loadModel('action')->create('outwarddelivery', $outwarddelivery->id, 'qingzongsynfailed', $common, '', 'guestjk');
                //tangfei 增加状态流转
                $this->loadModel('consumed')->record('outwarddelivery', $outwarddelivery->id, '0', 'guestjk', 'waitqingzong', 'qingzongsynfailed', array(),'生产变更单');
            }

            $this->dao->update(TABLE_MODIFYCNCC)->data($update)->where('id')->eq((int)$modifycnccId)->exec();
            $res[] = $run;
        }
        return $res;
    }

    /**
     * 接口请求最后一个记录
     * @param $id
     */
    public function getRequestLog($id)
    {
        $log = $this->dao->select('id,`status`,response,requestDate')->from(TABLE_REQUESTLOG)->where('objectType')->eq('modifycncc')->andWhere('objectId')->eq($id)->andWhere('purpose')->eq('pushmodifycncc')->orderBy('id_desc')->fetch();
        if(isset($log->response)){
            $log->response = json_decode($log->response);
        }
        return $log;
    }

    /**
     * @param $id
     * @param $status
     * @return void
     * 保存删除状态
     */
    public function updateDeleteStatus($id, $status){
        $res = $this->dao->update(TABLE_MODIFYCNCC)
            ->set('deleted')->eq($status)
            ->where('id')->eq((int)$id)->exec();
        return $res;
    }

    /**
     * 编辑退回次数
     * @param $id
     * @return void
     */
    public function editreturntimes($outwardDeliveryId){
        //工作量验证
        $rejectTimes = $_POST['modifycnccrejectTimes'];
        if($rejectTimes=='' || $rejectTimes==null)
        {
            dao::$errors['modifycnccrejectTimes'] = sprintf($this->lang->modifycncc->emptyObject, $this->lang->modifycncc->modifycnccrejectTimes);
        }else if(!is_numeric($rejectTimes) || (int)$rejectTimes<0 || strpos($rejectTimes,".")!==false) {
            dao::$errors['modifycnccrejectTimes'] = sprintf($this->lang->modifycncc->noNumeric, $this->lang->modifycncc->modifycnccrejectTimes);
        }

        $comment = $_POST['comment'];
        if(empty($comment))
        {
            dao::$errors['comment'] = sprintf($this->lang->modifycncc->emptyObject, $this->lang->comment);
        }

        $this->tryError();

        $outwardDelivery = $this->loadModel("outwardDelivery")->getByID($outwardDeliveryId);

        /* 当请求方式为post时，更新需求条目的状态为关闭。判断所属需求意向下的需求条目都关闭时，关闭需求意向。*/
        $this->dao->update(TABLE_MODIFYCNCC)->set('returnTimes')->eq($rejectTimes)->where('id')->eq($outwardDelivery->modifycnccId)->exec();
        $this->loadModel('action')->create('outwarddelivery', $outwardDeliveryId, 'editmodifycnccreturntimes', $comment);
        $this->loadModel('action')->create('modifycncc', $outwardDelivery->modifycnccId, 'editmodifycnccreturntimes', $comment);
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
     * @Notes:根据id集合获取清总生产变更单数据
     * @Date: 2023/12/6
     * @Time: 10:29
     * @Interface getByIds
     * @param array $ids
     * @param string $field
     * @return mixed
     */
    public function getByIds($ids = [], $field = '*')
    {
        return $this->dao->select($field)->from(TABLE_MODIFYCNCC)->where('id')->in($ids)->fetchAll();
    }
    /**
     * @param int $id 部门id
     * 修改上海分公司节点名称
     */
    public function resetNodeAndReviewerName($id=0){
        $depts = $this->loadModel('dept')->getRecurveSubDeptIds(30);
        if ((in_array($this->app->user->dept,$depts) &&  in_array($this->app->getMethodName(),['create','copy'])) || (in_array($id,$depts) && !in_array($this->app->getMethodName(),['create','copy']))){
            $this->lang->modifycncc->reviewerList['5'] = '上海分公司领导';
            $this->lang->modifycncc->reviewerList['6'] = '上海分公司总经理';

            $this->lang->modifycncc->reviewNodeList['5'] = '上海分公司领导';
            $this->lang->modifycncc->reviewNodeList['6'] = '上海分公司总经理';
        }

    }
}
