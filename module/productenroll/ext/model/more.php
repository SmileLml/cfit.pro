<?php
public function setNew()
    {
        return 'new100';
    }
public function pushproductEnroll($outwarddeliveryCode)
  {

    $outwarddelivery  = $this->loadModel("outwarddelivery")->getByCode($outwarddeliveryCode);
    $productEnroll    = $this->getByID($outwarddelivery->productEnrollId);
    $pushEnable = $this->config->global->pushOutwarddeliveryEnable;
    if(!$pushEnable)
    {
      return false;
    }
    $pushData = array();
    $pushData['idFromJinke']                    = $productEnroll->code; //必填

    // 获取需求code-array
    $requireIds = explode(',', $productEnroll->requirementId);
    $requireIdSTRs = array();
    foreach ($requireIds as $requireId){
        if(!empty($requireId)){
            $requireObj = $this->loadModel("requirement")->getByIdSimple($requireId);
            if(!empty($requireObj->entriesCode)){
                array_push($requireIdSTRs, $requireObj->entriesCode);
            }
        }
    }
    $pushData['demandKey']                 =  $requireIdSTRs;

    //问题单列表
    $problemIds = explode(',',  $productEnroll->problemId);
    $issueIds = array();
    foreach ($problemIds as $problemId){
      $problem = $this->loadModel("problem")->getByID($problemId);
      if(!empty($problem->IssueId)){
        $issueIds[] = $problem->IssueId;
      }
    }
    $pushData['issueId']                        =   $issueIds; //['aaaaa','bbbb']
    $testRequestKey = $this->dao->select('giteeId')->from(TABLE_TESTINGREQUEST)->where('id')->eq($outwarddelivery->testingRequestId)->fetch('giteeId');
    $pushData['testRequestKey']                 = $testRequestKey?$testRequestKey:'';
    $pushData['contactName']                    = $productEnroll->contactName; //联系人姓名 必填
    $pushData['Contact_telephone']              = $productEnroll->contactTel; //联系方式 必填
    $pushData['mailingAddress']                 = $productEnroll->contactEmail ?? "guchaonan@cfit.cn"; // 联系邮箱 必须有值
    $depts = $this->loadModel('dept')->getTopPairs();
    $pushData['responsibilityDep']              = $depts[$productEnroll->createdDept];
    $pushData['dynacommEn']                     = html_entity_decode($productEnroll->dynacommEn);
    $pushData['dynacommCn']                     = $productEnroll->dynacommCn;
    $pushData['Text_banbenhao']                 = $productEnroll->versionNum;
    $pushData['projectIdentifier']              = $productEnroll->CBPprojectId;
    $pushData['Isplan']                         = html_entity_decode(strip_tags(str_replace("<br />","\n",strval(htmlspecialchars_decode($productEnroll->isPlan,ENT_QUOTES)))));  //是否计划内软件
    $pushData['planSoftwareName']               = $productEnroll->planSoftwareName;  //计划软件名称
    $pushData['platform']                       = html_entity_decode(strip_tags(str_replace("<br />","\n",strval(htmlspecialchars_decode($productEnroll->platform,ENT_QUOTES)))));  //所属平台
    $pushData['Lastnum']                        = $productEnroll->lastVersionNum ?? '无';  //上一版本号
    $pushData['Checkdepartment']                = html_entity_decode(strip_tags(str_replace("<br />","\n",strval(htmlspecialchars_decode($productEnroll->checkDepartment,ENT_QUOTES)))));  //检测单位  后台配置
    $pushData['Result']                         = html_entity_decode(strip_tags(str_replace("<br />","\n",strval(htmlspecialchars_decode($productEnroll->result,ENT_QUOTES)))));  //结论
    $pushData['installationNode']               = html_entity_decode(strip_tags(str_replace("<br />","\n",strval(htmlspecialchars_decode($productEnroll->installationNode,ENT_QUOTES)))));  //安装节点
    $productLine                                = $this->loadModel('productline')->getByID($productEnroll->productLine);
    $pushData['productLine']                    = $productLine->emisId;//软件产品线
    $pushData['implementationForm']             = $productEnroll->implementationForm == 'project' ? "0" : "1";  //实施形式

    $app                                        = $this->loadModel('application')->getByID(trim($productEnroll->app,','));
    $pushData['SoftwareSystem']                 = $app->code;  //业务系统

    $pushData['planDistributionTime']           = strtotime($productEnroll->planDistributionTime).'000';  //计划发布时间
    $pushData['planUpTime']                     = strtotime($productEnroll->planUpTime).'000';  //计划上线时间
    $pushData['applicationPlanTime']            = time().'000';
    $pushData['softwareProductPatch']           = html_entity_decode(strip_tags(str_replace("<br />","\n",strval(htmlspecialchars_decode($productEnroll->softwareProductPatch,ENT_QUOTES)))));  //软件产品补丁
    $pushData['reasonFromJinke']                = $productEnroll->reasonFromJinke;  //理由
    $pushData['introductionToFunctionsAndUses'] = html_entity_decode(strip_tags(str_replace("<br />","\n",strval(htmlspecialchars_decode($productEnroll->introductionToFunctionsAndUses,ENT_QUOTES)))));  //主要功能及用途简介
    $pushData['remark']                         = html_entity_decode(strip_tags(str_replace("<br />","\n",strval(htmlspecialchars_decode($productEnroll->remark,ENT_QUOTES)))));  //备注
    $pushData['productMediaNameAndBytes']       = $productEnroll->mediaInfo;//产品介质名称和字节数
    $pushData['ifMediumChanges']                = Strval(htmlspecialchars_decode($productEnroll->ifMediumChanges,ENT_QUOTES)); //介质是否变化

    $remoteFileList = array();
    if(!empty($productEnroll->release)){
        $releaseIdList = explode(',' , $productEnroll->release);
        foreach ($releaseIdList as $releaseId){
            $releaseObj = $this->loadModel("release")->getByID($releaseId);
            if(!empty($releaseObj)){
                $remoteFileStr = $releaseObj->remotePathQz;
                $arr=explode("/", $remoteFileStr);
                $lastName=$arr[count($arr)-1];
                $urlObject = $this->loadModel("outwarddelivery")->getRelationFileLinkArray($lastName, $remoteFileStr, $releaseObj->md5);
                if(!empty($urlObject)){
                    array_push($remoteFileList, $urlObject);
                }
            }
        }
    }
    $pushData['relationFiles'] = $remoteFileList;

    $pushData['softwareCopyrightRegistration']  = Strval(htmlspecialchars_decode($productEnroll->softwareCopyrightRegistration,ENT_QUOTES)); //申请计算机软件著作权登记

    $url = $this->config->global->productEnrollPushUrl;
    $status = 'fail';
    $extra = '';
    $object = 'productenroll';
    $objectType = 'pushproductenroll';
    if (!empty($remoteFileList)){
        foreach ($pushData['relationFiles'] as $relationFile) {
            if ($relationFile['md5'] == ''){
                $res = ['status'=>'fail','msg'=>'MD5值不能为空'];
                $this->loadModel('requestlog')->saveRequestLog($url, $object, $objectType, 'POST', $pushData, json_encode($res), $status, $extra, $productEnroll->id);
                return 'md5empty';
            }
        }
    }
    $headers = array();
    $headers[] = 'App-Id: ' . $this->config->global->pushOutwarddeliveryAppId;
    $headers[] = 'App-Secret: ' . $this->config->global->pushOutwarddeliveryAppSecret;
    $result = $this->loadModel('requestlog')->http($url, $pushData, 'POST', 'json', array(), $headers);
    $response = '';

    if (!empty($result)) {
      $resultData = json_decode($result);
//        || $resultData->isSave == 1
      if ($resultData->code == '200') { //200 = 成功的 isSave == 1 代表成功保存 比如第一次没响应
        $status = 'success';
        isset($resultData->key) && $data['giteeId']   = $resultData->key;
        $data['applyTime'] = date('Y-m-d H:i:s',substr($pushData['applicationPlanTime'],0,10));
        $this->dao->update(TABLE_PRODUCTENROLL)->data($data)->where('code')->eq($productEnroll->code)->exec();
      }
      $response = $resultData;
    }

    $this->requestlog->saveRequestLog($url, $object, $objectType, 'POST', $pushData, $result, $status, $extra, $productEnroll->id);



    return $response;
  }

public function buildSearchForm($queryID, $actionURL)
  {
    $this->config->productenroll->search['actionURL'] = $actionURL;
    $this->config->productenroll->search['queryID']   = $queryID;
    $this->config->productenroll->search['params']['createdDept']['values'] = array('' => '') + $this->loadModel('dept')->getOptionMenu();
    $apps = $this->loadModel('application')->getapplicationNameCodePairsWithisPayment();
    $this->config->productenroll->search['params']['app']['values'] = array(''=>'') + array_column($apps, 'name', 'id');
    $this->config->productenroll->search['params']['problemId']['values'] = array('' => '') + $this->loadModel('problem')->getPairsAbstract();
    $this->config->productenroll->search['params']['demandId']['values'] = array('' => '') + $this->loadModel('demand')->getPairsTitle('noclosed');
    $this->config->productenroll->search['params']['projectPlanId']['values'] = array('' => '') + $this->loadModel('projectplan')->getAllProjects();
    $this->config->productenroll->search['params']['productLine']['values'] = array('' => '') +  $this->loadModel('productline')->getPairs();
    $this->config->productenroll->search['params']['CBPprojectId']['values'] = array('' => '') + $this->dao->select('code,concat(concat(concat(code,"（"),name),"）")')->from(TABLE_CBPPROJECT)->where('deleted')->eq('0')->fetchPairs();
    $this->config->productenroll->search['params']['requirementId']['values'] = array('' => '') + $this->dao->select('id,concat(concat(concat(code,"（"),name),"）")')->from(TABLE_REQUIREMENT)->where('entriesCode')->like('requirements%')->andWhere('status')->ne('deleted')->fetchPairs();
    $this->config->productenroll->search['params']['isPayment']['values'] = array(''=>'') + $this->dao->select('`key`,value')->from(TABLE_LANG)->where('module')->eq('application')->andWhere('section')->eq('isPaymentList')->fetchPairs();
    $this->config->productenroll->search['params']['team']['values'] = array(''=>'') + $this->dao->select('`key`,value')->from(TABLE_LANG)->where('module')->eq('application')->andWhere('section')->eq('teamList')->fetchPairs();
    $this->config->productenroll->search['params']['secondorderId']['values'] = array('' => '') + $this->loadModel('secondorder')->getNamePairs();
    $this->loadModel('search')->setSearchParams($this->config->productenroll->search);
  }