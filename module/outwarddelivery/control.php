<?php

class outwarddelivery extends control
{
    public function __construct()
    {
        parent::__construct();
        // 上海分公司审核节点名称修改
        if (in_array($this->app->getMethodName(),['create','copy'])){
            $this->outwarddelivery->resetNodeAndReviewerName();
        }
    }
    public function browse($browseType = 'all', $param = 0, $orderBy = 'code_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
      $this->view->title        = $this->lang->outwarddelivery->browse;
      $this->view->users        = $this->loadModel('user')->getPairs('noletter');
      $this->view->depts        = $this->loadModel('dept')->getTopPairs();
      $this->view->dmap         = $this->dao->select('account,realname,dept')->from(TABLE_USER)->fetchAll('account');
      $this->view->projectList  = $this->loadModel('projectplan')->getAllProjects();

      $apps  = $this->loadModel('application')->getapplicationNameCodePairsWithisPayment();
      $appList = array();
      foreach($apps as $app){
        $appList[$app->id] = $app->name;
      }

      $browseType = strtolower($browseType);

      $queryID = ($browseType == 'bysearch')  ? (int)$param : 0;
      $actionURL = $this->createLink('outwarddelivery', 'browse', "browseType=bySearch&param=myQueryID");
      $this->outwarddelivery->buildSearchForm($queryID, $actionURL);

      /* Load pager. */
      $this->app->loadClass('pager', $static = true);
      $pager = new pager($recTotal, $recPerPage, $pageID);

        /* 设置详情页面返回的url连接。*/
      $this->session->set('outwarddeliveryList', $this->app->getURI(true));
      $this->view->outwarddelivery = $this->outwarddelivery->getList($browseType,$queryID,$orderBy,$pager);
      foreach ($this->view->outwarddelivery as $item){
        $apps = array();
        foreach(explode(',',$item->app)  as $app){
          if(!empty($app)){
            $apps[] = zget($appList,$app);
          }
        }
        $item->app = implode('，',$apps);
        $totalReturn    = 0;
        $childrenCode   = array();

        if(!empty($item->testingRequestId)){
          //$testingRequest = $this->loadModel('testingrequest')->getByID($item->testingRequestId);
            if($item->isNewTestingRequest == 1){
                $testingRequest = $this->dao->select('*')->from(TABLE_TESTINGREQUEST)
                  ->where('id')->eq($item->testingRequestId)
                  ->andwhere('deleted')->eq(0)
                  ->fetch();
                if(!empty($testingRequest)) {
                    $totalReturn += $testingRequest->returnTimes;
                    $childrenCode[] ='测试申请';
                }
          }
        }
        
        if(!empty($item->productEnrollId)) {
          //$productenroll  = $this->loadModel('productenroll')->getByID($item->productEnrollId);
            if($item->isNewProductEnroll == 1){
                $productenroll = $this->dao->select('*')->from(TABLE_PRODUCTENROLL)
                    ->where('id')->eq($item->productEnrollId)
                    ->andwhere('deleted')->eq(0)
                    ->fetch();
                if (!empty($productenroll)) {
                    $totalReturn += $productenroll->returnTimes;
                    $childrenCode[] ='产品登记';
                }
            }

        }

        if(!empty($item->modifycnccId)){
          //$modifycncc = $this->loadModel('modifycncc')->getByID($item->modifycnccId);
            if($item->isNewModifycncc == 1){
                $modifycncc = $this->dao->select('*')->from(TABLE_MODIFYCNCC)
                    ->where('id')->eq($item->modifycnccId)
                    ->andwhere('deleted')->eq(0)
                    ->fetch();
                if(!empty($modifycncc)){
                    $totalReturn += $modifycncc->returnTimes;
                    $childrenCode[] ='生产变更';
                }

            }

        }



        $item->totalReturn  = $totalReturn;
        $item->childrenCode = implode(',', $childrenCode);
        $item->currentReview = zget($this->lang->outwarddelivery->currentReviewList,$item->currentReview);

        //授权管理
        $item->dealUser = $this->loadModel('common')->getAuthorizer('outwarddelivery', $item->dealUser,$item->status, $this->lang->outwarddelivery->authorizeStatusList);
      }

      $this->view->orderBy    = $orderBy;
      $this->view->pager      = $pager;
      $this->view->param      = $param;
      $this->view->browseType = $browseType;
      $this->display();
    }

    public function export($orderBy = 'id_desc', $browseType = 'all')
    {
      if($_POST)
      {
        /*$testingrequestList = $this->loadModel('testingrequest')->getAll();
        $productenrollList  = $this->loadModel('productenroll')->getAll();
        $modifycnccList     = $this->loadModel('modifycncc')->getAll();*/
        $this->loadModel('file');
        $outwarddeliveryLang   = $this->lang->outwarddelivery;
        $outwarddeliveryConfig = $this->config->outwarddelivery;

        /* Create field lists. */
        $fields = $this->post->exportFields ? $this->post->exportFields : explode(',', $outwarddeliveryConfig->list->exportFields);
        foreach($fields as $key => $fieldName)
        {
          $fieldName = trim($fieldName);
          $fields[$fieldName] = isset($outwarddeliveryLang->$fieldName) ? $outwarddeliveryLang->$fieldName : $fieldName;
          unset($fields[$key]);
        }

        /* Get outwarddeliverys. */
        $outwarddeliverys = array();
        if($this->session->outwardDeliveryOnlyCondition)
        {
          $outwarddeliverys = $this->dao->select('*')->from(TABLE_OUTWARDDELIVERY)->where($this->session->outwardDeliveryQueryCondition)
            ->beginIF($this->post->exportType == 'selected')->andWhere('id')->in($this->cookie->checkedItem)->fi()
            ->orderBy($orderBy)->fetchAll('id');
        }
        else
        {
          $stmt = $this->dbh->query($this->session->outwardDeliveryQueryCondition . ($this->post->exportType == 'selected' ? " AND id IN({$this->cookie->checkedItem})" : '') . " ORDER BY " . strtr($orderBy, '_', ' '));
          while($row = $stmt->fetch()) $outwarddeliverys[$row->id] = $row;
        }
          $requirementIds = array_column($outwarddeliverys,'requirementId');
          foreach ($requirementIds as $rk=>$rv) {
              $requirementIds[$rk] = trim($rv,',');
          }
        $this->loadModel('review');
        $users = $this->loadModel('user')->getPairs('noletter');
        $depts = $this->loadModel('dept')->getTopPairs();
        $dmap  = $this->dao->select('account,realname,dept')->from(TABLE_USER)->fetchAll('account');
        $apps  = $this->loadModel('application')->getapplicationNameCodePairsWithisPayment();
        $isPaymentPairs = $this->dao->select('`key`,value')->from(TABLE_LANG)->where('module')->eq('application')->andWhere('section')->eq('isPaymentList')->fetchPairs();
        $teamPairs = $this->dao->select('`key`,value')->from(TABLE_LANG)->where('module')->eq('application')->andWhere('section')->eq('teamList')->fetchPairs();
        $productLineList = $this->loadModel('productline')->getPairs();
        $productList     = $this->loadModel('product')->getList();
        $projectPlanList = $this->loadModel('projectplan')->getAllProjects();
        $productNameList = array_column($productList, 'name' , 'id');
        $productCodeList = array_column($productList, 'code' , 'id');
        $cbpprojectList = $this->dao->select('code,concat(concat(concat(code,"（"),name),"）")')->from(TABLE_CBPPROJECT)->where('deleted')->eq('0')->fetchPairs();
        $problemList    = $this->loadModel('problem')->getPairsAbstract();
        $demandList     = $this->loadModel('demand')->getPairsTitle('noclosed');
        $secondorderList     =  $this->loadModel('secondorder')->getNamePairs();
        $requirementList= $this->dao->select('id,concat(concat(concat(code,"（"),name),"）")')->from(TABLE_REQUIREMENT)->where('entriesCode')->like('requirements%')->andWhere('status')->ne('deleted')->fetchPairs();
          $requireList = $this->loadModel('requirement')->getPairsByIds($requirementIds);
        $appList        = array_column($apps, 'name','id');
        $isPaymentList  = array_column($apps, 'isPayment','id');
        $teamList       = array_column($apps, 'team','id');
        $release        = array('' => '') + $this->loadModel('release')->getNamePairs();
        $reviewReportList = $this->loadModel('review')->getPairs('','');

        unset($this->lang->modifycncc->implementModalityNewList[0]);
        $this->lang->modifycncc->implementModalityList = $this->lang->modifycncc->implementModalityList + $this->lang->modifycncc->implementModalityNewList;

        foreach ($outwarddeliverys as $outwarddelivery) {
          $createdBy = $outwarddelivery->createdBy;
          $status = $outwarddelivery->status;
//          $outwarddelivery->status                = zget($outwarddeliveryLang->statusList, $outwarddelivery->status, '');
          $outwarddelivery->status = $outwarddelivery->closed == '1' ? $this->lang->outwarddelivery->labelList['closed']:zget($this->lang->outwarddelivery->statusList, $outwarddelivery->status, '');
          $apps = array();
          $isPayments = array();
          $teams = array();
          foreach(explode(',',$outwarddelivery->app) as $app){
            if(!empty($app)){
              $apps[] = zget($appList, $app,'');
              $isPayments[] = $isPaymentPairs[zget($isPaymentList, $app,'')];
              $teams[] = $teamPairs[zget($teamList, $app,'')];
            }
          }
          $outwarddelivery->app  = implode(',', $apps);
          $outwarddelivery->isPayment = implode(',', $isPayments);
          $outwarddelivery->team = implode(',', $teams);


          //迭代二十六-导出部门字段删除第一个“/”
          $outwarddelivery->createdDepts          = ltrim($depts[$dmap[$createdBy]->dept], '/');

          if($status=='waitsubmitted' or $status=='reject')
          {
            $outwarddelivery->dealUser = $createdBy;
          }elseif($status=='withexternalapproval')
          {
              $outwarddelivery->dealUser = 'guestcn';
          }
          $reviewersArray = explode(',', $outwarddelivery->dealUser);
          //所有审核人
          $reviewerUsers    = getArrayValuesByKeys($users, $reviewersArray);
          $outwarddelivery->dealUser = implode(',', $reviewerUsers);


          $outwarddelivery->createdBy             = zget($users, $createdBy,'');
          $products = explode(',', $outwarddelivery->productId);
          $productNames = array();
          $productCodes = array();
          foreach ($products as $product){
            if(!empty($product)){
              $productNames[] = zget($productNameList, $product);
              $productCodes[] = zget($productCodeList, $product);
            }
          }
          $outwarddelivery->productName           = implode(',', $productNames);
          $outwarddelivery->productCode           = $outwarddelivery->productInfoCode;
          $outwarddelivery->implementationForm    = zget($outwarddeliveryLang->implementationFormList,$outwarddelivery->implementationForm,'');
          $outwarddelivery->projectPlanId         = zget($projectPlanList, $outwarddelivery->projectPlanId,'');
          $outwarddelivery->productLine           = zget($productLineList, $outwarddelivery->productLine,'');
          $outwarddelivery->CBPprojectId          = zget($cbpprojectList, $outwarddelivery->CBPprojectId,'');
          $secondorderIds = explode(',', $outwarddelivery->secondorderId);
          $secondorderNameList = array();
          foreach ($secondorderIds as $secondorder){
              if(!empty($secondorder)){
                  $secondorderNameList[] = zget($secondorderList, $secondorder,'');
              }
          }
          $outwarddelivery->secondorderId             = implode(',',$secondorderNameList);
          $problems = explode(',', $outwarddelivery->problemId);
          $problemNameList = array();
          foreach ($problems as $problem){
            if(!empty($problem)){
              $problemNameList[] = zget($problemList, $problem,'');
            }
          }
          $outwarddelivery->problemId             = implode(',',$problemNameList);
          $demands = explode(',', $outwarddelivery->demandId);
          $demandNameList = array();
          foreach ($demands as $demand){
            if(!empty($demand)){
              $demandNameList[] = zget($demandList, $demand);
            }
          }
          $outwarddelivery->demandId              = implode(',', $demandNameList);
          $requirements = explode(',', $outwarddelivery->requirementId);
          $requirementNameList = array();
          foreach ($requirements as $requirement){
            if(!empty($requirement)){
//              $requirementNameList[] = zget($requirementList, $requirement);
                $requirementNameList[] = $requireList->$requirement['code']."（".$requireList->$requirement['name']."）";
            }
          }
          $outwarddelivery->requirementId              = implode(',', $requirementNameList);
          $testingRequest = $this->loadModel('testingrequest')->getByID($outwarddelivery->testingRequestId);
          $productEnroll  = $this->loadModel('productenroll')->getByID($outwarddelivery->productEnrollId);
          $modifycncc     = $this->loadModel('modifycncc')->getByID($outwarddelivery->modifycnccId);

            $outwarddelivery->createdDate = substr($outwarddelivery->createdDate,0, 10);//创建时间
            $outwarddelivery->editedDate  = substr($outwarddelivery->editedDate,0, 10);// 编辑时间
          $outwarddelivery->currentReview              = zget($outwarddeliveryLang->currentReviewList,$outwarddelivery->currentReview);
          $outwarddelivery->relatedTestingRequest      = $outwarddelivery->isNewTestingRequest==0?$testingRequest->code.($testingRequest->giteeId?('（'.$testingRequest->giteeId.'）'):''):'';
          $outwarddelivery->relatedProductEnroll       = $outwarddelivery->isNewProductEnroll==0?$productEnroll->code.($productEnroll->giteeId?('（'.$productEnroll->giteeId.'）'):''):'';
          $outwarddelivery->relatedModifycncc          = $outwarddelivery->isNewModifycncc==0?$modifycncc->code.($modifycncc->giteeId?('（'.$modifycncc->giteeId.'）'):''):'';
          $outwarddelivery->testingRequestReturnTimes  = $testingRequest->returnTimes;
          $outwarddelivery->productEnrollReturnTimes   = $productEnroll->returnTimes;
          $outwarddelivery->modifycnccReturnTimes      = $modifycncc->returnTimes;
          $outwarddelivery->dealUserContact            = $outwarddelivery->contactTel;
          $outwarddelivery->editedBy                   = zget($users, $outwarddelivery->editedBy,'');
          $outwarddelivery->closedBy                   = zget($users, $outwarddelivery->closedBy,'');
          $outwarddelivery->release                    = zget($release,$outwarddelivery->release);
          $outwarddelivery->isMediaChanged             = zget($outwarddeliveryLang->isMediaChangedList, $outwarddelivery->ifMediumChanges);
          $RORData = array();
          foreach ($outwarddelivery->ROR as $index => $item){
            if(!empty($item)) {
              $RORData[] = $index.'.'.$item['RORDate'].$item['RORContent'];
            }
          }
          $RORData = '';
          if(!empty($outwarddelivery->ROR)) {
            foreach (json_decode($outwarddelivery->ROR,true) as $index => $item) {
              if (!empty($item)) {
                $RORData .= $index+1 . '. ' . $item['RORDate'] .' '. $item['RORContent'].PHP_EOL;
              }
            }
          }
          $outwarddelivery->ROR                       = $RORData;
          $outwarddelivery->testSummary               = $testingRequest->testSummary;

          $outwarddelivery->testTarget                = $testingRequest->testTarget;
          $outwarddelivery->acceptanceTestType        = zget($this->lang->testingrequest->acceptanceTestTypeList,$testingRequest->acceptanceTestType);
          $outwarddelivery->currentStage              = $testingRequest->currentStage;
          $outwarddelivery->os                        = $testingRequest->os;
          $outwarddelivery->db                        = $testingRequest->db;
          $outwarddelivery->content                   = $testingRequest->content;
          $outwarddelivery->env                       = $testingRequest->env;
          $outwarddelivery->isCentralizedTest         = zget($this->lang->testingrequest->isCentralizedTestList,$testingRequest->isCentralizedTest);

          $outwarddelivery->productenrollDesc             = $productEnroll->productenrollDesc;
          $outwarddelivery->isPlan                        = zget($this->lang->productenroll->isPlanList,$productEnroll->isPlan) ;
          $outwarddelivery->planProductName               = $productEnroll->planProductName;
          $outwarddelivery->dynacommCn                    = $productEnroll->dynacommCn;
          $outwarddelivery->dynacommEn                    = $productEnroll->dynacommEn;
          $outwarddelivery->versionNum                    = $productEnroll->versionNum;
          $outwarddelivery->lastVersionNum                = $productEnroll->lastVersionNum;
          $outwarddelivery->checkDepartment               = zget($this->lang->productenroll->checkDepartmentList,$productEnroll->checkDepartment);
          $outwarddelivery->result                        = zget($this->lang->productenroll->resultList,$productEnroll->result);
          $outwarddelivery->installationNode              = zget($this->lang->productenroll->installNodeList,$productEnroll->installationNode);
          $outwarddelivery->softwareProductPatch          = zget($this->lang->productenroll->softwareProductPatchList,$productEnroll->softwareProductPatch);
          $outwarddelivery->softwareCopyrightRegistration = zget($this->lang->productenroll->softwareCopyrightRegistrationList,$productEnroll->softwareCopyrightRegistration);
          $outwarddelivery->planDistributionTime          = $productEnroll->planDistributionTime;
          $outwarddelivery->planUpTime                    = $productEnroll->planUpTime;
          $outwarddelivery->platform                      = zget($this->lang->productenroll->appList,$productEnroll->platform);
          $outwarddelivery->reasonFromJinke               = $productEnroll->reasonFromJinke;
          $outwarddelivery->introductionToFunctionsAndUses= $productEnroll->introductionToFunctionsAndUses;
          $outwarddelivery->remark                        = $productEnroll->remark;

          $outwarddelivery->desc                          = $modifycncc->desc;
          $outwarddelivery->target                        = $modifycncc->target;
          $outwarddelivery->reason                        = $modifycncc->reason;
          $outwarddelivery->changeContentAndMethod        = $modifycncc->changeContentAndMethod;
          $outwarddelivery->step                          = $modifycncc->step;
          $outwarddelivery->techniqueCheck                = $modifycncc->techniqueCheck;
          $outwarddelivery->test                          = $modifycncc->test;
          $outwarddelivery->checkList                     = $modifycncc->checkList;
          $outwarddelivery->cooperateDepNameList          = zget($this->lang->modifycncc->cooperateDepNameListList, $modifycncc->cooperateDepNameList);
          $outwarddelivery->businessCooperateContent      = $modifycncc->businessCooperateContent;
          $outwarddelivery->judgeDep                      = zget($this->lang->modifycncc->judgeDepList, $modifycncc->judgeDep);
          $outwarddelivery->judgePlan                     = $modifycncc->judgePlan;
          $outwarddelivery->controlTableFile              = $modifycncc->controlTableFile;
          $outwarddelivery->controlTableSteps             = $modifycncc->controlTableSteps;
          $outwarddelivery->feasibilityAnalysis           = '';
          foreach(explode(',',$modifycncc->feasibilityAnalysis) as $item){
            if(!empty($item)){
              $outwarddelivery->feasibilityAnalysis .= zget($this->lang->modifycncc->feasibilityAnalysisList,$item).PHP_EOL;
            }
          }
          $outwarddelivery->risk                          = $modifycncc->risk;
          $outwarddelivery->effect                        = $modifycncc->effect;
          $outwarddelivery->businessFunctionAffect        = $modifycncc->businessFunctionAffect;
          $outwarddelivery->backupDataCenterChangeSyncDesc= $modifycncc->backupDataCenterChangeSyncDesc;
          $outwarddelivery->emergencyManageAffect         = $modifycncc->emergencyManageAffect;
          $outwarddelivery->businessAffect                = $modifycncc->businessAffect;
          $outwarddelivery->benchmarkVerificationType     = zget($this->lang->modifycncc->benchmarkVerificationTypeList, $modifycncc->benchmarkVerificationType);
          $outwarddelivery->verificationResults           = $modifycncc->verificationResults;

          $outwarddelivery->feedBackId                        = $modifycncc->feedBackId;
          $outwarddelivery->operationName                     = $modifycncc->operationName;
          $outwarddelivery->feedBackOperationType             = $modifycncc->feedBackOperationType;
          $outwarddelivery->depOddName                        = $modifycncc->depOddName;
          $outwarddelivery->actualBegin                       = $modifycncc->actualBegin;
          $outwarddelivery->actualEnd                         = $modifycncc->actualEnd;
          $outwarddelivery->supply                            = $modifycncc->supply;
          $outwarddelivery->changeNum                         = $modifycncc->changeNum;
          $outwarddelivery->operationStaff                    = $modifycncc->operationStaff;
          $outwarddelivery->executionResults                  = $modifycncc->executionResults;

          $outwarddelivery->problemDescription                = $modifycncc->problemDescription;
          $outwarddelivery->resolveMethod                     = $modifycncc->resolveMethod;

          $outwarddelivery->closedReason                      = zget($this->lang->outwarddelivery->closedReasonList, $outwarddelivery->closedReason);
          $revertReason       = '';
          $revertReasonChild  = '';
            if(!empty($outwarddelivery->revertReason)) {
                $childTypeList = json_decode($this->lang->outwarddelivery->childTypeList['all'],true);
                $childType = [];
                foreach ($childTypeList as $k=>$v){
                    $childType += $v;
                }

                foreach (json_decode($outwarddelivery->revertReason,true) as $index => $item) {
                    if (!empty($item)) {
                        $revertReason       .= $item['RevertDate'] .':'. zget($this->lang->outwarddelivery->revertReasonList, $item['RevertReason']).PHP_EOL;
                        if (isset($childType[$item['RevertReasonChild']])){
                            $revertReasonChild  .= $item['RevertDate'] .':'. $childType[$item['RevertReasonChild']].PHP_EOL;
                        }
                    }
                }
            }
          $outwarddelivery->revertReason                      = $revertReason;
          $outwarddelivery->revertReasonChild                 = $revertReasonChild;
          $outwarddelivery->isReview                      = zget($this->lang->modifycncc->isReviewList, $modifycncc->isReview);
          $outwarddelivery->isReviewPass                      = zget($this->lang->modifycncc->isReviewPassList, $modifycncc->isReviewPass);
          $outwarddelivery->reviewReport                      = zget($reviewReportList, $modifycncc->reviewReport,'');
          $outwarddelivery->urgentSource                      = zget($this->lang->modifycncc->urgentSourceList,$modifycncc->urgentSource,'');
          $outwarddelivery->urgentReason                      = $modifycncc->urgentReason;
            // 手否后补流程、实际交付时间
          $outwarddelivery->actualDeliveryTime = $modifycncc->isMakeAmends == 'yes' ? $modifycncc->actualDeliveryTime : '';
          $outwarddelivery->isMakeAmends = zget($this->lang->modify->isMakeAmendsList,$modifycncc->isMakeAmends,'');
          $outwarddelivery->changeForm   = zget($this->lang->modifycncc->changeFormList,$modifycncc->changeForm,'');
          $outwarddelivery->automationTools = zget($this->lang->modifycncc->automationToolsList,$modifycncc->automationTools,'');
          $outwarddelivery->changeImpactAnalysis = $modifycncc->changeImpactAnalysis;
        }
        $this->post->set('fields', $fields);
        $this->post->set('rows', $outwarddeliverys);
        $this->post->set('kind', 'outwarddelivery');
        $this->loadModel('file')->setExcelWidth(20);
        $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
      }

      $this->view->fileName        = $this->lang->outwarddelivery->exportName;
      $this->view->allExportFields = $this->config->outwarddelivery->list->exportFields;
      $this->view->customExport    = true;
      $this->display();
    }

    public function create()
    {
        if($_POST)
        {
            $outwarddeliveryID = $this->outwarddelivery->create();
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $outwarddeliveryObj = $this->outwarddelivery->getByID($outwarddeliveryID);
            if($outwarddeliveryObj->isNewTestingRequest == 1){
                $this->loadModel('action')->create('testingrequest', $outwarddeliveryObj->testingRequestId, 'created', $this->post->comment);
            }
            if($outwarddeliveryObj->isNewProductEnroll == 1){
                $this->loadModel('action')->create('productenroll', $outwarddeliveryObj->productEnrollId, 'created', $this->post->comment);
            }
            if($outwarddeliveryObj->isNewModifycncc == 1){
                $this->loadModel('action')->create('modifycncc', $outwarddeliveryObj->modifycnccId, 'created', $this->post->comment);
            }
            if ($_POST['abnormalCode']){
                $modify = $this->loadModel('modifycncc')->getByID($_POST['abnormalCode']);
                $this->post->comment = $this->lang->outwarddelivery->associaitonOrder.'：'.$modify->code.'<br/>'.$this->post->comment;
            }
            $actionId = $this->loadModel('action')->create('outwarddelivery', $outwarddeliveryID, 'created', $this->post->comment);
            //$this->outwarddelivery->sendmail($outwarddeliveryID, $actionId);
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = inlink('browse');
            $response['status']  = $outwarddeliveryObj->status;
            $response['id']      = $outwarddeliveryID;
            if ($_POST['issubmit'] == 'submit'){
                $response['message'] = $this->lang->submitSuccess;
            }
            if ($response['status'] == 'waitsubmitted' && $_POST['issubmit'] == 'submit'){
                $response['iframeUrl']  = $this->createLink('outwarddelivery', 'submit', "id=".$outwarddeliveryID."&linkType=2",'',true);
            }
            $this->send($response);
        }
        $demandLang = $this->app->loadLang('demand')->demand;
        $this->app->loadLang('modify');
        $this->loadModel("modifycncc");
        //获取异常变更单(可以多次被关联)
        $abnormalList = array('' => '') + $this->outwarddelivery->getModifyAbnormal();
        $this->view->abnormalList = $abnormalList;
        //标题
        $this->view->title = $this->lang->outwarddelivery->create;
        //申请测试单键值对
        $this->view->testingrequestList = array('' => '') + $this->loadModel("testingrequest")->getPairs();
        //产品等级键值对
        $this->view->productenrollList = array('' => '') + $this->loadModel("productenroll")->getPairs();
        //产品线键值对
        $this->view->productlineList = array('' => '') + $this->loadModel('productline')->getPairs();
        //所属系统
        $this->view->appAll  =  $this->loadModel('application')->getApplicationCodePairsSyncQz();
        $this->view->appList = array('' => '') + array_column($this->view->appAll, 'name', 'id');
        //产品名称
//        $this->view->productList   = array('' => '') + $this->loadModel('product')->getProductWithCodeName('noclosed');
        $this->view->productList   = [];
        //所属项目
        $this->view->projectList       = array('' => '') + $this->loadModel('projectplan')->getAliveProjects(false);
        //关联问题
        $this->view->problemList       = array('' => '') + $this->loadModel('problem')->getPairsAbstract('noclosed');
        //关联需求
        $this->view->demandList        = array('' => '') + $this->loadModel('demand')->modifySelect('outwarddelivery', 0, 0);
        //关联二线工单
        $this->view->secondorderList        = array('' => '') + $this->loadModel('secondorder')->getNamePairs();
        //关联需求任务
        $this->view->requirementList    = array('' => '') + $this->dao->select('id,concat(concat(concat(code,"（"),name),"）")')->from(TABLE_REQUIREMENT)->where('status')->ne('deleted')->fetchPairs();
        //所属CBP项目
        $this->view->cbpprojectList     = array('' => '') + $this->dao->select('code,concat(concat(concat(code,"（"),name),"）")')->from(TABLE_CBPPROJECT)->where('deleted')->ne(1)->fetchPairs();
        //产品变更
        $this->view->apps           = array('' => '') + $this->loadModel('application')->getapplicationNameCodePairsWithPartition2();
        $this->view->modifycnccList = array('' => '') + $this->dao->select('id,concat(concat(concat(code,"（"),substring(`desc`,1,255)),"）")')->from(TABLE_MODIFYCNCC)->where('deleted')->eq('0')->fetchPairs();
        //审核节点下的审核人列表
        $reviewers            = $this->modifycncc->getReviewers();
        //审核节点下的审核人列表
        $reviewerAccounts     = $this->loadModel('review')->getReviewerAccounts($reviewers);
        //审核节点下默认设置审核节点人
        $defChosenReviewNodes = $this->config->modifycncc->create->setDefChosenReviewNodes;
        $this->view->reviewers            = $reviewers;
        $this->view->reviewerAccounts     = $reviewerAccounts;
        $this->view->defChosenReviewNodes = $defChosenReviewNodes;
        $this->view->leaveDefectList = array('' => '');
        $this->view->fixDefectList = array('' => '');
        $this->display();
    }

    public function edit($outwardID = 0,$source='')
    {
        $this->app->loadLang('modify');
        if($_POST)
        {
            $changes = $this->outwarddelivery->edit($outwardID,$source);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $changesOutwarddelivery = $changes['outwarddelivery'];
            $outwarddeliveryObj = $this->outwarddelivery->getByID($outwardID);

            if($outwarddeliveryObj->isNewTestingRequest == 1){
                if($changes['testingrequest'] != 'create'){
                    $actionID = $this->loadModel('action')->create('testingrequest', $outwarddeliveryObj->testingRequestId, 'edited', $this->post->comment);
                    $this->action->logHistory($actionID, $changes['testingrequest']);
                    $changesOutwarddelivery = array_merge($changesOutwarddelivery, $changes['testingrequest']);
                }else{
                    $this->loadModel('action')->create('testingrequest', $outwarddeliveryObj->testingRequestId, 'created', $this->post->comment);
                }
            }
            if($outwarddeliveryObj->isNewProductEnroll == 1){
                if($changes['productenroll'] != 'create'){
                    $actionID = $this->loadModel('action')->create('productenroll', $outwarddeliveryObj->productEnrollId, 'edited', $this->post->comment);
                    $this->action->logHistory($actionID, $changes['productenroll']);
                    $changesOutwarddelivery = array_merge($changesOutwarddelivery, $changes['productenroll']);
                }else{
                    $this->loadModel('action')->create('productenroll', $outwarddeliveryObj->productEnrollId, 'created', $this->post->comment);
                }
            }
            if($outwarddeliveryObj->isNewModifycncc == 1){
                if($changes['modifycncc'] != 'create'){
                    $actionID = $this->loadModel('action')->create('modifycncc', $outwarddeliveryObj->modifycnccId, 'edited', $this->post->comment);
                    $this->action->logHistory($actionID, $changes['modifycncc']);
                    $changesOutwarddelivery = array_merge($changesOutwarddelivery, $changes['modifycncc']);
                }else{
                    $this->loadModel('action')->create('modifycncc', $outwarddeliveryObj->modifycnccId, 'created', $this->post->comment);
                }
            }
            if ($_POST['abnormalCode'] && $_POST['abnormalCode'] != $outwarddeliveryObj->abnormalCode){
                $modify = $this->loadModel('modifycncc')->getByID($_POST['abnormalCode']);
                $this->post->comment = $this->lang->outwarddelivery->associaitonOrder.'：'.$modify->code.'<br/>'.$this->post->comment;
            }
            $actionID = $this->loadModel('action')->create('outwarddelivery', $outwardID, 'edited', $this->post->comment);
            $this->action->logHistory($actionID, $changesOutwarddelivery);
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = inlink('browse');
            if ($_POST['issubmit'] == 'submit'){
                $response['message'] = $this->lang->submitSuccess;
                $response['iframeUrl']  = $this->createLink('outwarddelivery', 'submit', "id=".$outwardID."&linkType=2",'',true);
            }
            $this->send($response);
        }
        $this->loadModel("modifycncc");
        //获取对外交付信息
        $outwarddelivery = $this->loadModel('outwarddelivery')->getByID($outwardID);
        //获取关联。被关联的变更单
        $abnormalOrder = $this->getAbnormalById($outwarddelivery);
        $outwarddelivery->abnormalCode = $abnormalOrder['nowOrder']->id;
        //获取未被关联的异常变更单
//        $abnormalList = array('' => '') + array($abnormalOrder['nowOrder']->id => $abnormalOrder['nowOrder']->code) + $this->outwarddelivery->getModifyAbnormal();
        $abnormalList = array('' => '') + $this->outwarddelivery->getModifyAbnormal();
        if(empty($outwarddelivery->ROR)){
            $outwarddelivery->RORList = array();
        }else{
            $outwarddelivery->RORList = json_decode(json_encode($outwarddelivery->ROR),true);
        }
        if(!empty($outwarddelivery->consumed)){
            $outwarddelivery->consumed = array_pop($outwarddelivery->consumed)->consumed;
        }

        $this->view->outwarddelivery   = $outwarddelivery;
        if($outwarddelivery->isNewTestingRequest == 1){
            $testingrequest = $this->loadModel('testingrequest')->getByID($outwarddelivery->testingRequestId);
            if($testingrequest->status == 'cmconfirmed' || $testingrequest->status == 'groupsuccess' || $testingrequest->status == 'managersuccess'
                || $testingrequest->status == 'systemsuccess' || $testingrequest->status == 'posuccess' || $testingrequest->status == 'leadersuccess'
                || $testingrequest->status == 'gmsuccess' || $testingrequest->status == 'withexternalapproval' || $testingrequest->status == 'productsuccess'
                || $testingrequest->status == 'closed' || $testingrequest->status == 'waitqingzong' || $testingrequest->status == 'testingrequestpass'){
                $testingrequest->disable = true;
                if($outwarddelivery->isNewTestingRequest == 1 && $outwarddelivery->isNewProductEnroll != 1 && $outwarddelivery->isNewModifycncc != 1){
                    $testingrequest->isOnly = true;
                }else{
                    $testingrequest->isOnly = false;
                }
            }else{
                $testingrequest->disable = false;
                $testingrequest->isOnly = false;
            }
            $this->view->testingrequest   = $testingrequest;
        }
        if($outwarddelivery->isNewProductEnroll == 1){
            $productenroll   = $this->loadModel('productenroll')->getByID($outwarddelivery->productEnrollId);
            $productenroll->mediaInfoList =  json_decode(json_encode($productenroll->mediaInfo));
            if($productenroll->status == 'cmconfirmed' || $productenroll->status == 'groupsuccess' || $productenroll->status == 'managersuccess'
                || $productenroll->status == 'systemsuccess' || $productenroll->status == 'posuccess' || $productenroll->status == 'leadersuccess'
                || $productenroll->status == 'gmsuccess' || $productenroll->status == 'withexternalapproval' || $productenroll->status == 'productsuccess'
                || $productenroll->status == 'closed' || $productenroll->status == 'waitqingzong' || $productenroll->status == 'productenrollpass'
                || $productenroll->status == 'emispass' || $productenroll->status == 'giteepass'){
                $productenroll->disable = true;
                if(($outwarddelivery->isNewTestingRequest != 1 && $outwarddelivery->isNewProductEnroll == 1 && $outwarddelivery->isNewModifycncc != 1)
                    || ($outwarddelivery->isNewTestingRequest == 1 && $outwarddelivery->isNewProductEnroll == 1 && $outwarddelivery->isNewModifycncc != 1 && $testingrequest->disable)){
                    $productenroll->isOnly = true;
                }else{
                    $productenroll->isOnly = false;
                }
            }else{
                $productenroll->isOnly = false;
                $productenroll->disable = false;
            }
            $this->view->productenroll = $productenroll;
        }
        if($outwarddelivery->isNewModifycncc == 1){
            $modifycncc = $this->loadModel('modifycncc')->getByID($outwarddelivery->modifycnccId);
            if($modifycncc->status == 'cmconfirmed' || $modifycncc->status == 'groupsuccess' || $modifycncc->status == 'managersuccess'
                || $modifycncc->status == 'systemsuccess' || $modifycncc->status == 'posuccess' || $modifycncc->status == 'leadersuccess'
                || $modifycncc->status == 'gmsuccess' || $modifycncc->status == 'withexternalapproval' || $modifycncc->status == 'productsuccess'
                || $modifycncc->status == 'closed' || $modifycncc->status == 'waitqingzong' || $modifycncc->status == 'modifysuccesspart'
                || $modifycncc->status == 'modifysuccess'){
                $modifycncc->disable = true;
            }else{
                $modifycncc->disable = false;
            }

            $this->view->modifycncc   = $modifycncc;
//            if (isset($this->lang->modifycncc->implementModalityList[$modifycncc->implementModality]) && (int)$modifycncc->implementModality > 0){
//                $this->lang->modifycncc->implementModalityNewList[$modifycncc->implementModality] = $this->lang->modifycncc->implementModalityList[$modifycncc->implementModality];
//            }
        }

        $abnormalList[$abnormalOrder['nowOrder']->id] = $abnormalOrder['nowOrder']->code;
        $this->view->abnormalList = $abnormalList;
        $this->view->reviewReportList = array('' => '') + $this->dao->select('id,title')->from(TABLE_REVIEW)
            ->where('project')->eq($outwarddelivery->projectPlanId)
            ->andWhere('status')->eq('reviewpass')
            ->andWhere('deleted')->eq(0)
            ->orderBy('id_desc')
            ->fetchPairs();
        //标题
        $this->view->title = $this->lang->outwarddelivery->edit;
        //申请测试单键值对
        $this->view->testingrequestList = array('' => '') + $this->loadModel("testingrequest")->getPairs($outwardID);
        //产品等级键值对
        $this->view->productenrollList = array('' => '') + $this->loadModel("productenroll")->getPairs($outwardID);
        //产品线键值对
        $this->view->productlineList = array('' => '') + $this->loadModel('productline')->getPairs();
        //所属系统
        $this->view->appAll  =  $this->loadModel('application')->getApplicationCodePairsSyncQz();
        $this->view->appList = array('' => '') + array_column($this->view->appAll, 'name', 'id');
        //产品名称
        $app = 0;
        if ($outwarddelivery->createdDate > "2023-05-31 23:59:59"){
            $app = trim($outwarddelivery->app,',');
        }
        $this->view->productList   = array('' => '') + $this->loadModel('product')->getProductWithCodeName('noclosed',0,'',$app);
        //产品名称被选中的列
        $this->view->productSelectList   = $this->dao->select('id,name')
                                            ->from(TABLE_PRODUCT)
                                            ->where('deleted')->eq(0)->andwhere('id')->in($outwarddelivery->productId)
                                            ->fetchPairs('id', 'name');
        //编辑页面回显所属项目查询
        $implementationForm = false;
        if($outwarddelivery->implementationForm == 'second'){
            $implementationForm = $outwarddelivery->implementationForm;
        }
        //所属项目
        $this->view->projectList       = array('' => '') + $this->loadModel('projectplan')->getAliveProjects($implementationForm);
        //关联问题
        $this->view->problemList       = array('' => '') + $this->loadModel('problem')->getPairsAbstract('noclosed','',array_filter(explode(',',$outwarddelivery->problemId)));
        //关联二线工单
        $this->view->secondorderList        = array('' => '') + $this->loadModel('secondorder')->getNamePairs();
        //获取问题选中列
        $this->view->problemSelectList     = $this->dao->select("id,concat(code,'（',IFNULL(abstract,''),'）') as code")->from(TABLE_PROBLEM)
                                        ->where('status')->ne('deleted')->andwhere('id')->in($outwarddelivery->problemId)
                                        ->orderBy('id_desc')
                                        ->fetchPairs();;
        //关联需求
        $this->view->demandList = $this->loadModel('demand')->modifySelectByEdit($outwarddelivery->demandId, 'outwarddelivery', $outwardID, $outwarddelivery->isNewModifycncc);
        //关联需求任务
        if(empty($outwarddelivery->demandId)){
            $requirementList = array('' => '') + $this->dao->select('id,concat(concat(concat(code,"（"),name),"）")')->from(TABLE_REQUIREMENT)->where('status')->ne('deleted')->fetchPairs();
        }else{
            if ($outwarddelivery->createdDate <= "2023-07-04 23:59:59"){
                $opinionId = $this->dao->select('distinct opinionID')->from(TABLE_DEMAND)->where('id')->in(explode(',',$outwarddelivery->demandId))->fetchAll();
                $requirementList = array('' => '') + $this->dao->select('id,concat(concat(concat(code,"（"),name),"）")')->from(TABLE_REQUIREMENT)->where('opinion')->in(array_column($opinionId,'opinionID'))->andWhere('status')->ne('deleted')->fetchpairs();
            }else{
                $opinionId = $this->dao->select('distinct requirementID')->from(TABLE_DEMAND)->where('id')->in(explode(',',$outwarddelivery->demandId))->fetchAll();
                $requirementList = array('' => '') + $this->dao->select('id,concat(concat(concat(code,"（"),name),"）")')->from(TABLE_REQUIREMENT)->where('id')->in(array_column($opinionId,'requirementID'))->andWhere('status')->ne('deleted')->fetchpairs();
            }
        }

        $this->view->requirementList   = $requirementList;
        //获取关联需求任务选中列
        $this->view->requirementSelectList     = $this->dao->select('id,concat(concat(concat(code,"（"),name),"）")')->from(TABLE_REQUIREMENT)
            ->where('status')->ne('deleted')->andwhere('id')->in($outwarddelivery->requirementId)
            ->orderBy('id_desc')
            ->fetchPairs();;
        //所属CBP项目
        $this->view->cbpprojectList     = array('' => '') + $this->dao->select('code,concat(concat(concat(code,"（"),name),"）")')->from(TABLE_CBPPROJECT)->where('deleted')->ne(1)->fetchPairs();
        //产品变更
        $this->view->apps           = array('' => '') + $this->loadModel('application')->getapplicationNameCodePairsWithPartition2();
        $this->view->modifycnccList = array('' => '') + $this->dao->select('id,concat(concat(concat(code,"（"),substring(`desc`,1,255)),"）")')->from(TABLE_MODIFYCNCC)->where('deleted')->eq('0')->fetchPairs();
        //审核节点下的审核人列表
        $reviewers            = $this->modifycncc->getReviewers();
        //审核节点下的审核人列表
        $reviewerAccounts     = $this->loadModel('review')->getReviewerAccounts($reviewers);
        //审核节点下默认设置审核节点人
        $defChosenReviewNodes = $this->config->modifycncc->create->setDefChosenReviewNodes;
        $this->view->reviewers            = $reviewers;
        $this->view->reviewerAccounts     = $reviewerAccounts;
        $this->view->defChosenReviewNodes = $defChosenReviewNodes;
        //审核节点以及审核节点的审核人
        $nodesReviewers = $this->loadModel('review')->getAllNodeReviewers('outwarddelivery', $outwardID, $outwarddelivery->version);
        $this->view->nodesReviewers = $nodesReviewers;

        // 缺陷 根据系统查询
        $oldleaveDefects = $this->dao->select('id,dealSuggest,changeStatus,title')->from(TABLE_DEFECT)->where('id')->in(explode(',', $outwarddelivery->leaveDefect))->andWhere('source')->eq('1')->fetchAll();
        $newleaveDefectList = array('' => '') + $this->dao->select('id, concat(concat(code, "-"), title)')->from(TABLE_DEFECT)
                ->where('app')->eq(trim($outwarddelivery->app,','))
                ->andWhere('dealSuggest')->in(array('suggestClose', 'nextFix'))
                ->andWhere('status')->eq('tofeedback')
                ->andWhere('syncStatus')->eq(0)
                ->fetchPairs();
        foreach ($oldleaveDefects as $oldleaveDefect) {
            foreach ($newleaveDefectList as $k=>$item) {
                if ($oldleaveDefect->id != $k){
                    $newleaveDefectList[$oldleaveDefect->id] = $oldleaveDefect->code.$oldleaveDefect->title;
                }
            }
        }
        ksort($newleaveDefectList);
        $oldfixDefects          = $this->dao->select('id,dealSuggest,changeStatus,code,title')->from(TABLE_DEFECT)->where('id')->in(explode(',', $outwarddelivery->fixDefect))->andWhere('source')->eq('1')->fetchAll();

        $newfixDefectList   = array('' => '') + $this->dao->select('id, concat(concat(code, "-"), title)')->from(TABLE_DEFECT)
                ->where('app')->eq(trim($outwarddelivery->app,','))
                ->andWhere('dealSuggest')->eq('fix')
                ->andWhere('status')->eq('tofeedback')
                ->andWhere('syncStatus')->eq(0)
                ->fetchPairs();
        foreach ($oldfixDefects as $oldfixDefect) {
            foreach ($newfixDefectList as $k2=>$item) {
                if ($oldfixDefect->id != $k2){
                    $newfixDefectList[$oldfixDefect->id] = $oldfixDefect->code.$oldfixDefect->title;
                }
            }
        }
        ksort($newfixDefectList);
        $this->view->leaveDefectList  = $newleaveDefectList;
        $this->view->fixDefectList    = $newfixDefectList;
//        $this->view->leaveDefectList = array('' => '') + $this->dao->select('id, concat(concat(code, "-"), title)')->from(TABLE_DEFECT)
//                ->where('product')->in(explode(',', $outwarddelivery->productId))
//                ->andWhere('project')->eq($outwarddelivery->projectPlanId)
//                ->andWhere('dealSuggest')->in(array('suggestClose', 'nextFix'))
//                ->andWhere('status')->eq('tofeedback')
//                ->andWhere('syncStatus')->eq(0)
//                ->fetchPairs();
//        $this->view->fixDefectList = array('' => '') + $this->dao->select('id, concat(concat(code, "-"), title)')->from(TABLE_DEFECT)
//                ->where('product')->in(explode(',', $outwarddelivery->productId))
//                ->andWhere('project')->eq($outwarddelivery->projectPlanId)
//                ->andWhere('dealSuggest')->eq('fix')
//                ->andWhere('status')->eq('tofeedback')
//                ->andWhere('syncStatus')->eq(0)
//                ->fetchPairs();

        $this->display();
    }

    /**
     * 复制功能
     * @param $outwardID
     * @return void
     */
    public function copy($outwardID = 0)
    {
        $this->app->loadLang('modify');
        if($_POST)
        {
            $outwarddeliveryID = $this->outwarddelivery->create();

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $outwarddeliveryObj = $this->outwarddelivery->getByID($outwarddeliveryID);
            if($outwarddeliveryObj->isNewTestingRequest == 1){
                $this->loadModel('action')->create('testingrequest', $outwarddeliveryObj->testingRequestId, 'created', $this->post->comment);
            }
            if($outwarddeliveryObj->isNewProductEnroll == 1){
                $this->loadModel('action')->create('productenroll', $outwarddeliveryObj->productEnrollId, 'created', $this->post->comment);
            }
            if($outwarddeliveryObj->isNewModifycncc == 1){
                $this->loadModel('action')->create('modifycncc', $outwarddeliveryObj->modifycnccId, 'created', $this->post->comment);
            }
            if ($_POST['abnormalCode']){
                $modify = $this->loadModel('modifycncc')->getByID($_POST['abnormalCode']);
                $this->post->comment = $this->lang->outwarddelivery->associaitonOrder.'：'.$modify->code.'<br/>'.$this->post->comment;
            }
            $this->loadModel('action')->create('outwarddelivery', $outwarddeliveryID, 'created', $this->post->comment);
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = inlink('browse');
            $response['status']  = $outwarddeliveryObj->status;
            $response['id']      = $outwarddeliveryID;
            if ($_POST['issubmit'] == 'submit'){
                $response['message'] = $this->lang->submitSuccess;
            }
            if ($response['status'] == 'waitsubmitted' && $_POST['issubmit'] == 'submit'){
                $response['iframeUrl']  = $this->createLink('outwarddelivery', 'submit', "id=".$outwarddeliveryID."&linkType=2",'',true);
            }
            $this->send($response);
        }
        //获取未被关联的异常变更单
        $abnormalList = array('' => '') + $this->outwarddelivery->getModifyAbnormal();
        $this->view->abnormalList = $abnormalList;
        $demandLang = $this->app->loadLang('demand')->demand;
        $this->loadModel("modifycncc");
        //获取对外交付信息
        $outwarddelivery = $this->loadModel('outwarddelivery')->getByID($outwardID);
        if(empty($outwarddelivery->ROR)){
            $outwarddelivery->RORList = array();
        }else{
            $outwarddelivery->RORList = json_decode(json_encode($outwarddelivery->ROR),true);
        }
        if(!empty($outwarddelivery->consumed)){
            $outwarddelivery->consumed = array_pop($outwarddelivery->consumed)->consumed;
        }

        $this->view->outwarddelivery   = $outwarddelivery;
        if($outwarddelivery->isNewTestingRequest == 1){
            $testingrequest = $this->loadModel('testingrequest')->getByID($outwarddelivery->testingRequestId);
            $testingrequest->isOnly = false;
            $testingrequest->disable = false;
            $this->view->testingrequest   = $testingrequest;
        }
        if($outwarddelivery->isNewProductEnroll == 1){
            $productenroll   = $this->loadModel('productenroll')->getByID($outwarddelivery->productEnrollId);
            $productenroll->mediaInfoList =  json_decode(json_encode($productenroll->mediaInfo));
            $productenroll->isOnly = false;
            $productenroll->disable = false;
            $this->view->productenroll = $productenroll;
        }
        if($outwarddelivery->isNewModifycncc == 1){
            $modifycncc = $this->loadModel('modifycncc')->getByID($outwarddelivery->modifycnccId);
            $modifycncc->isOnly = false;
            $modifycncc->disable = false;
            $this->view->modifycncc   = $modifycncc;
        }
        $this->view->reviewReportList = array('' => '') + $this->dao->select('id,title')->from(TABLE_REVIEW)
                ->where('project')->eq($outwarddelivery->projectPlanId)
                ->andWhere('status')->eq('reviewpass')
                ->andWhere('deleted')->eq(0)
                ->orderBy('id_desc')
                ->fetchPairs();
        //标题
        $this->view->title = $this->lang->outwarddelivery->copy;
        //申请测试单键值对
        $this->view->testingrequestList = array('' => '') + $this->loadModel("testingrequest")->getPairs();
        //产品等级键值对
        $this->view->productenrollList = array('' => '') + $this->loadModel("productenroll")->getPairs();
        //产品线键值对
        $this->view->productlineList = array('' => '') + $this->loadModel('productline')->getPairs();
        //所属系统
        $this->view->appAll  =  $this->loadModel('application')->getApplicationCodePairsSyncQz();
        $this->view->appList = array('' => '') + array_column($this->view->appAll, 'name', 'id');
        //产品名称
        $app = 0;
        if ($outwarddelivery->createdDate > "2023-05-31 23:59:59"){
            $app = trim($outwarddelivery->app,',');
        }
        $this->view->productList   = array('' => '') + $this->loadModel('product')->getProductWithCodeName('noclosed',0,'',$app);
        //所属项目
        $this->view->projectList       = array('' => '') + $this->loadModel('projectplan')->getAliveProjects(false);
        //关联问题
        $this->view->problemList       = array('' => '') + $this->loadModel('problem')->getPairsAbstract('noclosed');
        //关联二线工单
        $this->view->secondorderList        = array('' => '') + $this->loadModel('secondorder')->getNamePairs();
        //关联需求
        $singleUsageFlag = isset($this->config->singleUsage) && 'on' == $this->config->singleUsage;
        $this->view->demandList        = array('' => '') + $this->loadModel('demand')->modifySelect('outwarddelivery', 0, $outwarddelivery->isNewModifycncc);
        //关联需求任务
        if(empty($outwarddelivery->demandId)){
            $requirementList = array('' => '') + $this->dao->select('id,concat(concat(concat(code,"（"),name),"）")')->from(TABLE_REQUIREMENT)->where('status')->ne('deleted')->fetchPairs();
        }else{
            if ($outwarddelivery->createdDate <= "2023-07-04 23:59:59"){
                $opinionId = $this->dao->select('distinct opinionID')->from(TABLE_DEMAND)->where('id')->in(explode(',',$outwarddelivery->demandId))->fetchAll();
                $requirementList = array('' => '') + $this->dao->select('id,concat(concat(concat(code,"（"),name),"）")')->from(TABLE_REQUIREMENT)->where('opinion')->in(array_column($opinionId,'opinionID'))->andWhere('status')->ne('deleted')->fetchpairs();
            }else{
                $opinionId = $this->dao->select('distinct requirementID')->from(TABLE_DEMAND)->where('id')->in(explode(',',$outwarddelivery->demandId))->fetchAll();
                $requirementList = array('' => '') + $this->dao->select('id,concat(concat(concat(code,"（"),name),"）")')->from(TABLE_REQUIREMENT)->where('id')->in(array_column($opinionId,'requirementID'))->andWhere('status')->ne('deleted')->fetchpairs();
            }
        }
        $this->view->requirementList   = $requirementList;
        //所属CBP项目
        $this->view->cbpprojectList     = array('' => '') + $this->dao->select('code,concat(concat(concat(code,"（"),name),"）")')->from(TABLE_CBPPROJECT)->fetchPairs();
        //产品变更
        $this->view->apps           = array('' => '') + $this->loadModel('application')->getapplicationNameCodePairsWithPartition2();
        $this->view->modifycnccList = array('' => '') + $this->dao->select('id,concat(concat(concat(code,"（"),substring(`desc`,1,255)),"）")')->from(TABLE_MODIFYCNCC)->where('deleted')->eq('0')->fetchPairs();
        //审核节点下的审核人列表
        $reviewers            = $this->modifycncc->getReviewers();
        //审核节点下的审核人列表
        $reviewerAccounts     = $this->loadModel('review')->getReviewerAccounts($reviewers);
        //审核节点下默认设置审核节点人
        $defChosenReviewNodes = $this->config->modifycncc->create->setDefChosenReviewNodes;
        $this->view->reviewers            = $reviewers;
        $this->view->reviewerAccounts     = $reviewerAccounts;
        $this->view->defChosenReviewNodes = $defChosenReviewNodes;
        //审核节点以及审核节点的审核人
        //$nodesReviewers = $this->loadModel('review')->getAllNodeReviewers('outwarddelivery', $outwardID, $outwarddelivery->version);
        //$this->view->nodesReviewers = $nodesReviewers;
        $this->view->leaveDefectList = $leaveDefectList = array('' => '') + $this->dao->select('id, concat(concat(code, "-"), title)')->from(TABLE_DEFECT)
                ->where('app')->eq(trim($outwarddelivery->app,','))
                ->andWhere('dealSuggest')->in(array('suggestClose', 'nextFix'))
                ->andWhere('status')->eq('tofeedback')
                ->andWhere('syncStatus')->eq(0)
                ->fetchPairs();
        $this->view->fixDefectList   = array('' => '') + $this->dao->select('id, concat(concat(code, "-"), title)')->from(TABLE_DEFECT)
                ->where('app')->eq(trim($outwarddelivery->app,','))
                ->andWhere('dealSuggest')->eq('fix')
                ->andWhere('status')->eq('tofeedback')
                ->andWhere('syncStatus')->eq(0)
                ->fetchPairs();

        $this->display();
    }

    public function setNew($outwardId)
    {
        $this->loadModel('testingrequest')->pushTestingrequest($outwardId);
    }

    /**
     * Project: chengfangjinke
     * Method: review
     * User: tangfei
     * Year: 2021
     * Date: 2022/6/20
     * Time: 14:45
     * Desc: This is the code comment. This method is called review.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $outwarddeliveryID
     * @param int $version
     * @param int $reviewStage
     */
    public function review($outwarddeliveryID = 0, $version = 1, $reviewStage = 0)
    {
        $outwarddelivery = $this->loadModel('outwarddelivery')->getByID($outwarddeliveryID);
        //检查是否允许审核
        $res = $this->loadModel('outwarddelivery')->checkAllowReview($outwarddelivery, $version,  $reviewStage, $this->app->user->account);
        if($res['result']){
            $this->loadModel('demand')->isSingleUsage($outwarddelivery->demandId, 'outwarddelivery', $outwarddeliveryID, $outwarddelivery->isNewModifycncc);
            if(dao::isError()){
                $res = ['result' => false, 'message' => implode('<br />', dao::getError())];
            }
        }
        $release = explode(',', trim($outwarddelivery->release,','));
        $outwarddelivery->checkSystemPass = $this->loadModel('build')->checkSystemPass($release);
        $this->view->users    = $this->loadModel('user')->getPairs('noclosed|noletter');
        if($reviewStage == 0){
            if($_POST)
            {
                $this->outwarddelivery->link($outwarddeliveryID);

                if(dao::isError())
                {
                    $response['result']  = 'fail';
                    $response['message'] = dao::getError();
                    $this->send($response);
                }

                if($res['reviewAuthorize'] == $this->app->user->account){
                    $this->loadModel('action')->create('outwarddelivery', $outwarddeliveryID, 'linkrelease', $this->post->comment);
                }else{
                    $authorizeComment = sprintf($this->lang->outwarddelivery->authorizeComment,zget($this->view->users, $this->app->user->account), zget($this->view->users, $res['reviewAuthorize']));
                    $this->loadModel('action')->create('outwarddelivery', $outwarddeliveryID, 'linkrelease', $this->post->comment.'<br>'.$authorizeComment);
                }



                $response['result']  = 'success';
                $response['message'] = $this->lang->saveSuccess;
                $response['locate']  = 'parent';

                $this->send($response);
            }


            $this->view->depts    = $this->loadModel('dept')->getOptionMenu();
            $this->view->title    = $this->lang->outwarddelivery->edit;
            $this->view->outwarddelivery   = $outwarddelivery;
            $this->view->res      = $res;
            $this->view->multiple      = ($outwarddelivery->productEnrollId || $outwarddelivery->modifycnccId) ? "" : "multiple";
            $this->view->releases = array('' => '') + $this->loadModel('project')->getReleases($this->view->outwarddelivery->projectPlanId);
            $this->display();
        }else{
            if($_POST)
            {
                $outInfo = $this->outwarddelivery->getByID($outwarddeliveryID);
                $info    = $this->outwarddelivery->review($outwarddeliveryID);

                if(dao::isError())
                {
                    $response['result']  = 'fail';
                    $response['message'] = dao::getError();
                    $this->send($response);
                }
                $action = 'cmconfirmed' == $outInfo->status || 'gmsuccess' == $outInfo->status ? 'deal' : 'review';
                if($res['reviewAuthorize'] == $this->app->user->account){
                    $actionID = $this->loadModel('action')->create('outwarddelivery', $outwarddeliveryID, $action, $this->post->comment);
                }else{
                    $authorizeComment = sprintf($this->lang->outwarddelivery->authorizeComment,zget($this->view->users, $this->app->user->account), zget($this->view->users, $res['reviewAuthorize']));
                    $actionID = $this->loadModel('action')->create('outwarddelivery', $outwarddeliveryID, $action, $this->post->comment.'<br>'.$authorizeComment);
                }


                if(isset($info['mediaPush']) && $info['mediaPush'] == 1){
                    $this->dao->update(TABLE_RELEASE)
                        ->set('pushStatusQz')->eq(1)
                        ->set('pushFailsQz')->eq(0) //重发 失败归零 不重置remotePathQz 因为发送要校验是否最新并成功
                        ->set('md5')->eq("")
                        ->where('id')->in(explode(',', trim($outwarddelivery->release,',')))
                        ->exec();

                    $this->loadModel('action')->create('outwarddelivery', $outwarddelivery->id, 'pushmedia', "推送介质到清总", "", "guestjk");
                }
                $response['result']  = 'success';
                $response['message'] = $this->lang->saveSuccess;
                $response['locate']  = 'parent';

                $this->send($response);
            }
            //检查是否允许审核
            //$res = $this->loadModel('outwarddelivery')->checkAllowReview($outwarddelivery, $version, $reviewStage, $this->app->user->account);
            // $res['result'] = true;
            $this->view->depts   = $this->loadModel('dept')->getOptionMenu();
            $this->view->title   = $this->lang->outwarddelivery->edit;
            //$this->view->users   = $this->loadModel('user')->getPairs('noclosed|noletter');
            $this->view->outwarddelivery  = $outwarddelivery;
            $this->view->res     = $res;
            $this->display();
        }
    }

    public function view($outwarddeliveryID = 0){
        $this->app->loadLang('release');
        $this->app->loadLang('projectrelease');
        $this->app->loadLang('testingrequest');
        $this->app->loadLang('productenroll');
        $this->app->loadLang('modifycncc');
        $this->app->loadLang('application');
        $this->app->loadLang('file');
        $this->app->loadLang('api');
        $this->app->loadLang('defect');
        $this->app->loadLang('modify');

        unset($this->lang->modifycncc->implementModalityNewList[0]);
        $this->lang->modifycncc->implementModalityList = $this->lang->modifycncc->implementModalityList + $this->lang->modifycncc->implementModalityNewList;

        $allInfo          = $this->outwarddelivery->getAllInfo($outwarddeliveryID);
        $outwarddelivery  = (Object)$allInfo['outwardDelivery'];

        $this->view->title                   = $this->lang->outwarddelivery->view;

        $this->view->testingrequest          = $allInfo['testingRequest'];
        $this->view->productenroll           = $allInfo['productEnroll'];
        $this->view->modifycncc              = $allInfo['modifycncc'];
        $this->view->reviewReportList              = $allInfo['reviewReportList'];

        $this->view->demand                  = $allInfo['demand'];
        $this->view->problem                 = $allInfo['problem'];
        $this->view->secondorder                 = $allInfo['secondorder'];
        $this->view->requirement             = $allInfo['requirement'];
        $this->view->relations               = $this->outwarddelivery->getAllRelations($outwarddeliveryID);

        $this->view->allLines                = $this->loadModel('productline')->getPairs();
        $this->view->allProductNames         = $this->loadModel('product')->getNamePairs();
        $this->view->allProductCodes         = $this->loadModel('product')->getCodePairs();
        $this->view->depts                   = $this->loadModel('dept')->getDeptPairs();
        $this->view->users                   = $this->loadModel('user')->getPairs('noletter');
        $this->view->projects                = array('' => '') + $this->loadModel('projectplan')->getProject($outwarddelivery->implementationForm == 'second');//更新获取所属项目的方法
        $outwarddelivery->appsInfo           = (Object)$this->outwarddelivery->getAppInfo(explode(',',$outwarddelivery->app));
        $outwarddelivery->CBPInfo            = $this->outwarddelivery->getCBPInfo($outwarddelivery->CBPprojectId);
        $this->view->releaseInfoList             = $this->outwarddelivery->getReleaseInfoInIds($outwarddelivery->release);
        $this->view->releasePushLogs             = $this->loadModel('release')->getPushLog($outwarddelivery->release);
        $this->view->actions                 = $this->loadModel('action')->getList('outwarddelivery', $outwarddeliveryID );

        if($outwarddelivery->isNewTestingRequest){
            $TRlog = $this->loadModel('testingrequest')->getRequestLog($outwarddelivery->testingRequestId);
            if(empty($TRlog)){
                $TRlog = new stdClass();
            }
            $this->view->TRlog = $TRlog;
        }
        if($outwarddelivery->isNewProductEnroll){
            $PElog = $this->loadModel('productenroll')->getRequestLog($outwarddelivery->productEnrollId);
            if(empty($PElog)){
                $PElog = new stdClass();
            }
            $this->view->PElog = $PElog;
        }
        if($outwarddelivery->isNewModifycncc) {
            $modifycncc = $this->loadModel('modifycncc')->getByID($outwarddelivery->modifycnccId);
            $MClog = $this->loadModel('modifycncc')->getRequestLog($outwarddelivery->modifycnccId);
            if(empty($MClog)){
                $MClog = new stdClass();
            }
            if(!empty($modifycncc->returnLog)){
                $modifycncc->returnLogArray = json_decode($modifycncc->returnLog);
                $verificationReturnNum = 0;
                foreach ($modifycncc->returnLogArray as $key=>$value){
                    if($value->node == '基准审核中' || $value->node == '基准实验室审核'){
                        $verificationReturnNum++;
                    }
                }
                $modifycncc->verificationReturnNum = $verificationReturnNum;
            }
            $this->view->modifycnccList = $this->dao->select('id,concat(concat(concat(code,"（"),substring(`desc`,1,255)),"）")')->from(TABLE_MODIFYCNCC)->fetchPairs();
            $this->view->MClog = $MClog;
            $this->view->modifycncc   = $modifycncc;
        }
        $this->view->nodes                    = $this->loadModel('review')->getNodes('outwardDelivery', $outwarddeliveryID, $outwarddelivery->version);
        $this->view->currentReviewers         = $this->loadModel('review')->getReviewer('outwardDelivery', $outwarddeliveryID, $outwarddelivery->version, $outwarddelivery->reviewStage);
        //tangfei 详情页面增加处理人变量，审批按钮显示条件
        $outwarddelivery->reviewers = $this->view->currentReviewers;
        $secondlineStage                      = array_search('产创部二线专员',$this->lang->outwarddelivery->reviewerList);
        $this->view->secondlineReviewer       = $this->loadModel('review')->getLastPendingPeople('outwardDelivery',$outwarddeliveryID, $outwarddelivery->version, $secondlineStage+1);

        //授权管理转化
        $outwarddelivery->dealUser = $this->loadModel('common')->getAuthorizer('outwarddelivery', $outwarddelivery->dealUser,$outwarddelivery->status, $this->lang->outwarddelivery->authorizeStatusList);
        $this->view->outwarddelivery          = $outwarddelivery;
        $this->view->leaveDefects          = $this->dao->select('id,dealSuggest,changeStatus,code')->from(TABLE_DEFECT)->where('id')->in(explode(',', $outwarddelivery->leaveDefect))->andWhere('source')->eq('1')->fetchAll();
        $this->view->fixDefects          = $this->dao->select('id,dealSuggest,changeStatus,code')->from(TABLE_DEFECT)->where('id')->in(explode(',', $outwarddelivery->fixDefect))->andWhere('source')->eq('1')->fetchAll();
        $this->view->uatDefects          = $this->dao->select('id,dealSuggest,changeStatus,code')->from(TABLE_DEFECT)->where('outwarddeliveryId')->eq($outwarddeliveryID)->andWhere('source')->eq('2')->fetchAll();
        //获取关联。被关联的变更单
        $abnormalOrder = $this->getAbnormalById($outwarddelivery);
//        $outwarddelivery->abnormalCode = $abnormalOrder['nowOrder']->id;
        $this->view->abnormalOrder = $abnormalOrder;
        //获取未被关联的异常变更单
        $abnormalList = array('' => '') + $this->outwarddelivery->getModifyAbnormal();
        $this->view->abnormalList = $abnormalList;
        $this->display();
    }

    /**
     * Desc:产品登记与产品进行联动
     * User: shixuyang
     * Date: 2022/6/22
     *
     * @param string $fixType
     *
     */
    public function ajaxGetproductenroll($productenrollId)
    {
        //所属系统
        $productenrollObj  =  $this->loadModel('productenroll')->getByID($productenrollId);
        //产品信息
        $productObj = $this->loadModel('product')->getByID($productenrollObj->productId);
        //实现方式
        $secondLineType = $productenrollObj->implementationForm == 'second';
        $projects = array('' => '') +  $this->loadModel('projectplan')->getAliveProjects($secondLineType);
        $projects = html::select('projectPlanId', $projects, '',"class='form-control chosen'");
        die(json_encode(array('productLine'=>$productenrollObj->productLine, 'CBPprojectId'=>$productenrollObj->CBPprojectId, 'requirementId' => $productenrollObj->requirementId, 'demandId' => $productenrollObj->demandId, 'problemId' => $productenrollObj->problemId,'app' => explode("," , $productenrollObj->app), 'productId' => explode("," , $productenrollObj->productId),'productCode' => !empty($productObj)?$productObj->code:''
                ,'productVerson' => $productenrollObj->versionNum,'productOs' => !empty($productObj)?$productObj->os:'','productArch' => !empty($productObj)?$productObj->arch:'','implementationForm' => $productenrollObj->implementationForm, 'projectPlanId' => $productenrollObj->projectPlanId,'projects' => $projects, 'secondorderId' => $productenrollObj->secondorderId)));
    }

    /**
     * Desc:测试申请联动
     * User: shixuyang
     * Date: 2022/6/22
     *
     * @param string $fixType
     *
     */
    public function ajaxGetTestRequest($testRequestId){
        $testRequestObj  =  $this->loadModel('testingrequest')->getByID($testRequestId);
        $secondLineType = $testRequestObj->implementationForm == 'second';
        $projects = array('' => '') +  $this->loadModel('projectplan')->getAliveProjects($secondLineType);
        $projects = html::select('projectPlanId', $projects, '',"class='form-control chosen'");
        die(json_encode(array('CBPprojectId'=>$testRequestObj->CBPprojectId,'implementationForm' => $testRequestObj->implementationForm, 'projectPlanId' => $testRequestObj->projectPlanId,'projects' => $projects)));
    }

    /**
     * Desc: 获取系统信息
     * User: shixuyang
     * Date: 2022/6/22
     * Time: 16:00
     *
     * @param $applicationcode
     *
     */
    public function ajaxGetSecondLine($fixType)
    {
        $secondLineType = $fixType == 'second';
        $projects = array('' => '') +  $this->loadModel('projectplan')->getAliveProjects($secondLineType);
        echo html::select('projectPlanId', $projects, '',"class='form-control chosen' onchange='getDefectList()'");
    }

    /**
     * Desc: 获取产品信息
     * User: shixuyang
     * Date: 2022/6/22
     * Time: 16:00
     *
     * @param $applicationcode
     *
     */
    public function ajaxGetProduct($productId){
        $productObj  =  $this->loadModel('product')->getById($productId);
        die(json_encode($productObj));
    }

    /**
     * Desc: 获取产品编号
     * User: shixuyang
     * Date: 2022/7/21
     * Time: 16:00
     *
     * @param $applicationcode
     *
     */
    public function ajaxGetProductInfoCode($productIdListStr){
        $productInfoCode = '';
        $productIdList = explode(',',$productIdListStr);
        for($i=0; $i<count($productIdList); $i++){
            $productObj  =  $this->loadModel('product')->getById($productIdList[$i]);
            if(!empty($productObj)){
                if(!empty($productInfoCode)){
                    $productInfoCode = $productInfoCode.',';
                }
                $productInfoCode = $productInfoCode.$productObj->code;
                $productInfoCode = $productInfoCode.'-'.'V'.'-for';
                if(empty($productObj->os) && empty($productObj->arch)){
                    $productInfoCode = $productInfoCode.'-';
                }else{
                    $productInfoCode = $productInfoCode.'-'.$productObj->os;
                    $productInfoCode = $productInfoCode.'-'.$productObj->arch;
                }
            }
        }
        die($productInfoCode);
    }

    /**
     * Desc: 获取需求任务
     * User: chendongcheng
     * Date: 2022/8/24
     * Time: 16:00
     *
     * @param $demandIds
     *
     */
    public function ajaxGetOpinionByDemand($demandIds,$disabled=''){
        if(empty($demandIds)){
            $requirementPairs = array('' => '') + $this->dao->select('id,concat(concat(concat(code,"（"),name),"）")')->from(TABLE_REQUIREMENT)->where('status')->ne('deleted')->fetchPairs();
            $arr = [];
        }else{
//            $opinionId = $this->dao->select('distinct opinionID')->from(TABLE_DEMAND)->where('id')->in(explode(',',$demandIds))->fetchAll();
//            $requirementPairs = array('' => '') + $this->dao->select('id,concat(concat(concat(code,"（"),name),"）")')->from(TABLE_REQUIREMENT)->where('opinion')->in(array_column($opinionId,'opinionID'))->andWhere('status')->ne('deleted')->fetchpairs();
            $opinionId = $this->dao->select('distinct requirementID')->from(TABLE_DEMAND)->where('id')->in(explode(',',$demandIds))->fetchAll();
            $requirementPairs = array('' => '') + $this->dao->select('id,concat(concat(concat(code,"（"),name),"）")')->from(TABLE_REQUIREMENT)->where('id')->in(array_column($opinionId,'requirementID'))->andWhere('status')->ne('deleted')->fetchpairs();
            $arr  =[];
            foreach ($requirementPairs as $key=>$requirementPair) {
                if ($key != ''){
                    $arr[] = $key;
                }
            }
        }
        echo html::select('requirementId[]', $requirementPairs, $arr, "class='form-control chosen requirementClass' multiple $disabled");
    }

    /**
     * Project: chengfangjinke
     * Method: review
     * User: Tony Stark
     * Year: 2021
     * Date: 2022/05/22
     * Time: 14:43
     * Desc: 驳回操作
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $outwarddeliveryID
     */
    public function reject($outwardDeliveryID = 0)
    {
        if($_POST)
        {
            $this->outwarddelivery->reject($outwardDeliveryID);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $actionID = $this->loadModel('action')->create('outwarddelivery', $outwardDeliveryID, 'reject', $this->post->comment);
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }
        //数据信息
        $outwarddelivery = $this->outwarddelivery->getByID($outwardDeliveryID);
        //检查是否允许驳回
        $res = $this->outwarddelivery->checkAllowReject($outwarddelivery);

        $this->view->depts      = $this->loadModel('dept')->getOptionMenu();
        $this->view->title      = $this->lang->outwarddelivery->reject;
        $this->view->users      = $this->loadModel('user')->getPairs('noclosed');
        $this->view->modifycncc = $outwarddelivery;

        $deptId = $outwarddelivery->createdDept;
        $this->view->reviewers = $this->outwarddelivery->getReviewers($deptId);
        $nodesReviewers = $this->loadModel('review')->getAllNodeReviewers('outwarddelivery', $outwardDeliveryID, $outwarddelivery->version);
        $this->view->nodesReviewers = $nodesReviewers;
        //允许跳过的节点
        $this->view->allowSkipReviewerNodes = $this->lang->outwarddelivery->allowSkipReviewerNodes;

        //是否允许审核
        $this->view->res        = $res;
        $this->display();
    }

    public function submit($outwarddeliveryID = 0,$linkType=1)
    {
        if($_POST)
        {
            $this->outwarddelivery->submit($outwarddeliveryID);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $this->loadModel('action')->create('outwarddelivery', $outwarddeliveryID, 'submitexamine', $this->post->comment);
            if ($linkType != '1'){
                $url = explode('?',$this->createLink('outwarddelivery', 'view','id='.$outwarddeliveryID));
                die(js::locate($url[0],'parent'));
            }
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }
        //审核节点以及审核节点的审核人
        $outwarddelivery = $this->outwarddelivery->getByID($outwarddeliveryID);
        $this->view->outwarddelivery = $outwarddelivery;
        $nodesReviewers = $this->loadModel('review')->getAllNodeReviewers('outwarddelivery', $outwarddeliveryID, $outwarddelivery->version);
        $this->view->nodesReviewers = $nodesReviewers;
        //允许跳过的节点
        if($outwarddelivery->version == 1){
            $this->view->allowSkipReviewerNodes = array();
        }else{
            $allowSkip = explode(',',$outwarddelivery->approvedNode);
            //如果是外部退回，二线专员选择不可能编辑
            if($outwarddelivery->isOutsideReject == 1){
                $requiredNode = explode(',', $outwarddelivery->requiredReviewNode);
                $requiredNode = array_diff($requiredNode, array('0','1','3'));
                $allowSkip = array_diff($allowSkip, $requiredNode);
            }
            $allowSkip = array_diff($allowSkip, array('2'));
            $this->view->allowSkipReviewerNodes = $allowSkip;
        }
        $this->view->linkType = $linkType;
        $this->display();
    }

    public function delete($outwarddeliveryID)
    {
        if(!empty($_POST))
        {
            $this->outwarddelivery->deleted($outwarddeliveryID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::$errors['statusError'];
                $this->send($response);
            }
            $backUrl =  $this->session->outwarddeliveryList ? $this->session->outwarddeliveryList : inLink('browse');
            if(isonlybody()) die(js::closeModal('parent.parent', $backUrl));
            die(js::reload('parent'));
        }

        $outwarddelivery = $this->outwarddelivery->getByID($outwarddeliveryID);
        $this->view->actions = $this->loadModel('action')->getList('outwarddelivery', $outwarddeliveryID);
        $this->view->outwarddelivery = $outwarddelivery;
        $this->view->users   = $this->loadModel('user')->getPairs();
        $this->display();
    }

    public function close($outwarddeliveryID = 0)
    {
        //变更取消按钮开关
        //$changeFlag      = isset($this->config->changeCloseSwitch) && $this->config->changeCloseSwitch == 1;
        $changeFlag      = false;
        $outwarddelivery = $this->outwarddelivery->getByID($outwarddeliveryID);
        if($_POST)
        {
            $this->outwarddelivery->close($outwarddeliveryID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $outwarddelivery = $this->outwarddelivery->getByID($outwarddeliveryID);

            /*$this->loadModel('action')->create('outwarddelivery', $outwarddeliveryID, 'closed', $this->post->comment, zget($this->lang->outwarddelivery->closedReasonList, $this->post->closedReason));
            $this->loadModel('action')->create('modifycncc', $outwarddelivery->modifycnccId, 'closed', $this->post->comment, zget($this->lang->outwarddelivery->closedReasonList, $this->post->closedReason));
            $this->loadModel('action')->create('productenroll', $outwarddelivery->productEnrollId, 'closed', $this->post->comment, zget($this->lang->outwarddelivery->closedReasonList, $this->post->closedReason));
            $this->loadModel('action')->create('testingrequest', $outwarddelivery->testingRequestId, 'closed', $this->post->comment, zget($this->lang->outwarddelivery->closedReasonList, $this->post->closedReason));*/

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }
        $this->view->title      = $this->lang->outwarddelivery->close;
        $this->view->outwarddelivery = $outwarddelivery;
        //判断单子状态是否满足关闭状态
        //判断单子是否只有一个子单
        $childNum = 0;
        $lastChild = '';
        if($outwarddelivery->isNewTestingRequest == 1){
            $childNum += 1;
            $lastChild = 'testingRequest';
        }
        if($outwarddelivery->isNewProductEnroll == 1){
            $childNum += 1;
            $lastChild = 'productEnroll';
        }
        if($outwarddelivery->isNewModifycncc == 1){
            $childNum += 1;
            $lastChild = 'modifycncc';
        }
        $this->view->closeEnable = true;
        //若只有一个子单
        if($childNum <= 1){
            //若子单已经外部审批，不能进行关闭
            if($lastChild == 'testingRequest'){
                $testingRequest = $this->loadModel('testingrequest')->getByID($outwarddelivery->testingRequestId);
                if(!$changeFlag && !empty($testingRequest->giteeId)){
                    $this->view->closeNotice = $this->lang->outwarddelivery->closeNotice;
                    $this->view->closeEnable = false;
                }
                //终态不能取消 [测试申请通过，已关闭，已取消]
                if($changeFlag && in_array($testingRequest->status, ['testingrequestpass', 'closed', 'cancel'])){
                    $this->view->closeNotice = $this->lang->outwarddelivery->statusEndNotice;
                    $this->view->closeEnable = false;
                }
                //外部状态不能取消 [待关联版本,待提交,内部未通过,已退回,待组长处理,待本部门审批,待系统部审批,待分管领导审批,待总经理审批,待产创部处理,待同步清总,测试申请不通过,同步清总失败]
                $insideStatus = ['wait','waitsubmitted','reviewfailed','reject','cmconfirmed','groupsuccess','managersuccess',
                    'posuccess','leadersuccess','gmsuccess','testingrequestreject','qingzongsynfailed','waitqingzong'];
                if($changeFlag && !in_array($testingRequest->status, $insideStatus)){
                    $this->view->closeNotice = $this->lang->outwarddelivery->closeNotice;
                    $this->view->closeEnable = false;
                }
            }else if($lastChild == 'productEnroll'){
                $productenroll= $this->loadModel('productenroll')->getByID($outwarddelivery->productEnrollId);
                if(!$changeFlag && !empty($productenroll->giteeId)){
                    $this->view->closeNotice = $this->lang->outwarddelivery->closeNotice;
                    $this->view->closeEnable = false;
                }
                //终态不能取消
                if($changeFlag && in_array($productenroll->status, ['emispass', 'giteepass', 'closed', 'cancel'])){
                    $this->view->closeNotice = $this->lang->outwarddelivery->statusEndNotice;
                    $this->view->closeEnable = false;
                }
                //外部状态不能取消 [待关联版本,待提交,内部未通过,已退回,待组长处理,待本部门审批,待系统部审批,待分管领导审批,待总经理审批,待产创部处理,待同步清总,产品登记不通过,同步清总失败]
                $insideStatus = ['wait','waitsubmitted','reviewfailed','reject','cmconfirmed','groupsuccess','managersuccess',
                    'posuccess','leadersuccess','gmsuccess','productenrollreject','qingzongsynfailed','waitqingzong'];
                if($changeFlag && !in_array($productenroll->status, $insideStatus)){
                    $this->view->closeNotice = $this->lang->outwarddelivery->closeNotice;
                    $this->view->closeEnable = false;
                }
            }else if($lastChild == 'modifycncc'){
                $modifycncc= $this->loadModel('modifycncc')->getByID($outwarddelivery->modifycnccId);
                if(!$changeFlag && !empty($modifycncc->giteeId)){
                    $this->view->closeNotice = $this->lang->outwarddelivery->closeNotice;
                    $this->view->closeEnable = false;
                }
                //终态不能取消
                if($changeFlag && in_array($modifycncc->status, ['modifycancel', 'modifysuccess', 'closed'])){
                    $this->view->closeNotice = $this->lang->outwarddelivery->statusEndNotice;
                    $this->view->closeEnable = false;
                }
                //外部状态不能取消 [待关联版本,待提交,内部未通过,已退回,待组长处理,待本部门审批,待系统部审批,待分管领导审批,待总经理审批,
                //待产创部处理,待同步清总,产品登记不通过,同步清总失败,变更失败,部分成功,带外部审批]
                $insideStatus = ['wait','waitsubmitted','reviewfailed','reject','cmconfirmed','groupsuccess','managersuccess',
                    'posuccess','leadersuccess','gmsuccess','productenrollreject','qingzongsynfailed','waitqingzong','modifyfail',
                    'modifysuccesspart','modifyreject','withexternalapproval'];
                if($changeFlag && !in_array($modifycncc->status, $insideStatus)){
                    $this->view->closeNotice = $this->lang->outwarddelivery->closeNotice;
                    $this->view->closeEnable = false;
                }
            }
        }else{
            //若最后的子单进入外部审批，不能进行关闭
            if($lastChild == 'productEnroll'){
                $productenroll= $this->loadModel('productenroll')->getByID($outwarddelivery->productEnrollId);
                if(!$changeFlag && !empty($productenroll->giteeId)){
                    $this->view->closeNotice = $this->lang->outwarddelivery->closeNotice;
                    $this->view->closeEnable = false;
                }
                //终态不能取消
                if($changeFlag && in_array($productenroll->status, ['emispass', 'giteepass', 'closed', 'cancel'])){
                    $this->view->closeNotice = $this->lang->outwarddelivery->statusEndNotice;
                    $this->view->closeEnable = false;
                }
                //外部状态不能取消 [待关联版本,待提交,内部未通过,已退回,待组长处理,待本部门审批,待系统部审批,待分管领导审批,待总经理审批,待产创部处理,待同步清总,产品登记不通过,同步清总失败]
                $insideStatus = ['wait','waitsubmitted','reviewfailed','reject','cmconfirmed','groupsuccess','managersuccess',
                    'posuccess','leadersuccess','gmsuccess','productenrollreject','qingzongsynfailed','waitqingzong'];
                if($changeFlag && !in_array($productenroll->status, $insideStatus)){
                    $this->view->closeNotice = $this->lang->outwarddelivery->closeNotice;
                    $this->view->closeEnable = false;
                }
            }else if($lastChild == 'modifycncc'){
                if(!$changeFlag && !empty($modifycncc->giteeId)){
                    $this->view->closeNotice = $this->lang->outwarddelivery->closeNotice;
                    $this->view->closeEnable = false;
                }
                //终态不能取消
                if($changeFlag && in_array($modifycncc->status, ['modifycancel', 'modifysuccess', 'closed'])){
                    $this->view->closeNotice = $this->lang->outwarddelivery->statusEndNotice;
                    $this->view->closeEnable = false;
                }
                //外部状态不能取消 [待关联版本,待提交,内部未通过,已退回,待组长处理,待本部门审批,待系统部审批,待分管领导审批,待总经理审批,
                //待产创部处理,待同步清总,产品登记不通过,同步清总失败,变更失败,部分成功,带外部审批]
                $insideStatus = ['wait','waitsubmitted','reviewfailed','reject','cmconfirmed','groupsuccess','managersuccess',
                    'posuccess','leadersuccess','gmsuccess','productenrollreject','qingzongsynfailed','waitqingzong','modifyfail',
                    'modifysuccesspart','modifyreject','withexternalapproval'];
                if($changeFlag && !in_array($modifycncc->status, $insideStatus)){
                    $this->view->closeNotice = $this->lang->outwarddelivery->closeNotice;
                    $this->view->closeEnable = false;
                }
            }
        }

        $this->display();
    }

    //查定时日志
    public function catlog($y, $m, $d, $model ='outwardDelivery', $fuc = 'cli')
    {
        $path = $this->app->getAppRoot() . "www/data/log/". $y . $m.'/cron-'.$y.'-'. $m.'-'. $d.'-'. $model.'-'. $fuc.'.log';
        $text = file_get_contents($path);
        echo nl2br($text);
    }

    /**
     * 同步失败重新推送 单号+备注必填
     * @param $id
     */
    public function push($id)
    {
        $outwarddelivery = $this->outwarddelivery->getByid($id);
        if($_POST) {

            if($this->post->code == '')
            {
                $response['result']  = 'fail';
                $response['message'] = "请选择操作单号";
                $this->send($response);
            }
            if(empty(trim($this->post->remark)))
            {
                $response['result']  = 'fail';
                $response['message'] = "请填写操作备注";
                $this->send($response);
            }

            list($type, $code) = explode(":", $this->post->code);

            //重新推送也重推介质
            if($outwarddelivery->release){
                $this->dao->update(TABLE_RELEASE)
                    ->set('pushStatusQz')->eq(1)
                    ->set('pushFailsQz')->eq(0) //重发 失败归零
//                    ->set('remotePathQz')->eq("")
                    ->set('md5')->eq("")
                    ->where('id')->in(explode(',', trim($outwarddelivery->release,',')))
//                    ->andwhere('pushStatusQz')->notin([1,2,3]) //1=等待发 2=正在发 3=已经成功
                    ->exec();
            }

            $this->outwarddelivery->rePush($type, $code, $id);
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->loadModel('action')->create('outwarddelivery', $id, 'repush', "重新推送：".$this->post->code."<br>操作备注：".$this->post->remark);
            $this->send($response);
        }
        $this->view->list = [];


        if($outwarddelivery->testingRequestId) {
            $testingRequest = $this->loadModel('testingRequest')->getByid($outwarddelivery->testingRequestId);
            if(isset($testingRequest->pushStatus) && $testingRequest->pushStatus == -1) { //同步失败的
                $this->view->list['testingRequest:'.$testingRequest->code] = $testingRequest->code;
            }
            if($testingRequest->status == 'testingrequestreject'){ //已退回的测试申请
                $this->view->list['testingRequest:'.$testingRequest->code] = $testingRequest->code;
            }
        }
        if($outwarddelivery->productEnrollId) { //已退回的生产变更
            $productEnroll = $this->loadModel('productEnroll')->getByid($outwarddelivery->productEnrollId);
            if(isset($productEnroll->pushStatus) && $productEnroll->pushStatus == -1) {
                $this->view->list['productEnroll:'.$productEnroll->code] = $productEnroll->code;
            }
            if($productEnroll->status == 'giteeback' || $productEnroll->status == 'testingrequestreject' || $productEnroll->status == 'productenrollreject'){ //可能生效的只有productenrollreject
                $this->view->list['productEnroll:'.$productEnroll->code] = $productEnroll->code;
            }
        }
        if($outwarddelivery->modifycnccId) { //已退回的生产变更
            $modifycncc = $this->loadModel('modifycncc')->getByid($outwarddelivery->modifycnccId);
            if(isset($modifycncc->pushStatus) && $modifycncc->pushStatus == -1) {
                $this->view->list['modifycncc:'.$modifycncc->code] = $modifycncc->code;
            }
            if($modifycncc->status == 'giteeback'){
                $this->view->list['modifycncc:'.$modifycncc->code] = $modifycncc->code;
            }
        }

        $this->display();
    }

    /**
     * @param $products
     * @param $project
     * 获取遗留缺陷
     */
    public function ajaxGetLeaveDefects($products, $project) {
        $leaveDefectList = array('' => '') + $this->dao->select('id, concat(concat(code, "-"), title)')->from(TABLE_DEFECT)
            ->where('product')->in($products)
            ->andWhere('project')->eq($project)
                ->andWhere('dealSuggest')->in(array('suggestClose', 'nextFix'))
                ->andWhere('status')->eq('tofeedback')
                ->andWhere('syncStatus')->eq(0)
                ->fetchPairs();
        die(html::select('leaveDefect[]', $leaveDefectList,'', "class='form-control chosen'multiple"));
    }
    /**
     * @param $products
     * @param $project
     * 获取修复缺陷
     */
    public function ajaxGetfixDefects($products, $project) {
        $fixDefectList = array('' => '') + $this->dao->select('id, concat(concat(code, "-"), title)')->from(TABLE_DEFECT)
            ->where('product')->in($products)
            ->andWhere('project')->eq($project)
                ->andWhere('dealSuggest')->eq('fix')
                ->andWhere('status')->eq('tofeedback')
                ->andWhere('syncStatus')->eq(0)
                ->fetchPairs();
        die(html::select('fixDefect[]', $fixDefectList,'', "class='form-control chosen'multiple"));
    }

    /**
     * @param $products
     * @param $project
     * 获取评审数据
     */
    public function ajaxGetReview($project) {
        $reviewList = array('' => '') + $this->dao->select('id,title')->from(TABLE_REVIEW)
            ->where('project')->eq($project)
            ->andWhere('status')->eq('reviewpass')
            ->andWhere('deleted')->eq(0)
            ->orderBy('id_desc')
            ->fetchPairs();
        die(html::select('reviewReport', $reviewList,'', "class='form-control chosen' multiple"));
    }

    /**
     * @param $defect
     * 检查绑定缺陷
     */
    public function ajaxCheckDefects($defects, $outwarddeliveryId = '') {
        $alldefects = $this->dao->select('code,outwarddeliveryId,testrequestId,productenrollId')->from(TABLE_DEFECT)->where('id')->in($defects)->fetchAll();
        $res = new stdClass();
        $relatedId = '';
        $relatedDefect = '';
        $res->relatedId = $relatedId;
        if(count($alldefects))
        {
            $codePairs = $this->outwarddelivery->getCodePairs();
            foreach ($alldefects as $defect) {
                if((!empty($defect->testrequestId) or !empty($defect->productenrollId)) and $outwarddeliveryId != $defect->outwarddeliveryId)
                {
                    $relatedId .= zget($codePairs, $defect->outwarddeliveryId) . ',';
                    $relatedDefect .= $defect->code . ',';
                }
            }
            $res->msg = '缺陷' . $relatedDefect . '关联了其他的对外交付单' . $relatedId . '关联新的对外交付单会和之前的对外交付单的关联关系取消';
        }
        $res->relatedId = $relatedId;
        die(json_encode($res));
    }

    public function ajaxUnbinkDefect($defectIds) {
        $alldefects = $this->dao->select('id,testrequestId,productenrollId,outwarddeliveryId')->from(TABLE_DEFECT)->where('id')->in($defectIds)->fetchAll();
        if(count($alldefects))
        {
            foreach ($alldefects as $defect) {
                if(!empty($defect->testrequestId))
                {
                    $tdata = $this->dao->select('id,leaveDefect,fixDefect')->from(TABLE_TESTINGREQUEST)->where('id')->eq($defect->testrequestId)->fetch();
                    $leaveDefect1 = explode(',', $tdata->leaveDefect);
                    foreach ($leaveDefect1 as $key=>$item)
                    {
                        if($item == $defect->id) unset($leaveDefect1[$key]);
                    }

                    $fixDefect1 = explode(',', $tdata->fixDefect);
                    foreach ($fixDefect1 as $key=>$item)
                    {
                        if($item == $defect->id) unset($fixDefect1[$key]);
                    }
                    $this->dao->update(TABLE_TESTINGREQUEST)->set('leaveDefect')->eq(implode(',', $leaveDefect1))->set('fixDefect')->eq(implode(',', $fixDefect1))->where('id')->eq($defect->testrequestId)->exec();
                }

                if(!empty($defect->productenrollId))
                {
                    $pdata = $this->dao->select('id,leaveDefect,fixDefect')->from(TABLE_PRODUCTENROLL)->where('id')->eq($defect->productenrollId)->fetch();
                    $leaveDefect = explode(',', $pdata->leaveDefect);
                    foreach ($leaveDefect as $key=>$item)
                    {
                        if($item == $defect->id) unset($leaveDefect[$key]);
                    }

                    $fixDefect = explode(',', $pdata->fixDefect);
                    foreach ($fixDefect as $key=>$item)
                    {
                        if($item == $defect->id) unset($fixDefect[$key]);
                    }
                    $this->dao->update(TABLE_PRODUCTENROLL)->set('leaveDefect')->eq(implode(',', $leaveDefect))->set('fixDefect')->eq(implode(',', $fixDefect))->where('id')->eq($defect->productenrollId)->exec();
                }

                if(!empty($defect->outwarddeliveryId))
                {
                    $wdata = $this->dao->select('id,leaveDefect,fixDefect')->from(TABLE_OUTWARDDELIVERY)->where('id')->eq($defect->outwarddeliveryId)->fetch();
                    $leaveDefect = explode(',', $wdata->leaveDefect);
                    foreach ($leaveDefect as $key=>$item)
                    {
                        if($item == $defect->id) unset($leaveDefect[$key]);
                    }

                    $fixDefect = explode(',', $wdata->fixDefect);
                    foreach ($fixDefect as $key=>$item)
                    {
                        if($item == $defect->id) unset($fixDefect[$key]);
                    }
                    $this->dao->update(TABLE_OUTWARDDELIVERY)->set('leaveDefect')->eq(implode(',', $leaveDefect))->set('fixDefect')->eq(implode(',', $fixDefect))->where('id')->eq($defect->outwarddeliveryId)->exec();
                }
            }
        }
    }

    /**
     * @param $type 一级分类 key值
     * @param $module 模块名称
     * description 公共 获取退回原因子类
     */
    public function ajaxGetChildType($type,$module='outwarddelivery'){
        $list = $this->outwarddelivery->getChildTypeList($type,$module);
        die(html::select('revertReasonChild', $list, '', 'class="form-control chosen"'));
    }

    /**
     * 所属系统
     *
     * @param $app 所属系统
     * @param string $name
     */
    public function ajaxGetProductWithCodeName($app, $name = 'productId'){
        $app = trim($app,',');
        if ((int)$app == 0){
            $productList = [];
        }else{
            $productList   = array('' => '') + $this->loadModel('product')->getProductWithCodeName('noclosed',0,'',$app);
        }

        $data[0] = html::select($name.'[]', $productList, '', "class='form-control chosen outwarddeliveryProduct1' onchange='selectProductId(this.value)'");
        $data[1] = html::select($name.'[]', $productList, '', "class='form-control chosen outwarddeliveryProduct2' multiple onchange='selectProductMultId()'");
        echo json_encode($data);
    }
    public function showHistoryNodes($id){
        $modify = $this->outwarddelivery->getByID($id);
        $reviewFailReason = json_decode($modify->reviewFailReason,true);
        $this->app->loadLang('outwarddelivery');
        $res = $this->dao->select('version')->from(TABLE_REVIEWNODE)->where('objectType')->eq('outwarddelivery')->andWhere('objectID')->eq($id)->groupby('version')->fetchall();
        $versions = array_column($res,'version');
        foreach ($versions as $version) {
            $data = $this->loadModel('review')->getNodes('outwarddelivery', $id, $version);
            foreach ($data as $k=>$v){
                if ($v->status == 'wait' || !(is_array($v->reviewers) && !empty($v->reviewers))){
                    unset($data[$k]);
                }
                // 后续不再显示系统部审核节点，去掉
                if ($v->stage == 4 && isset($modify->createdDate) && $modify->createdDate > "2024-04-02 23:59:59"){
                    unset($data[$k]);
                }
                if (in_array($v->stage-1, $this->lang->outwarddelivery->skipNodes) and (!in_array($v->status,['pass','reject']))) {
                    unset($data[$k]);
                }
            }

            $nodes[$version]['nodes'] = $data;
        }
        foreach ($nodes as $key=>$node) {
            $nodes[$key]['countNodes'] = count($node['nodes']);
            if(isset($reviewFailReason[$key]) && !empty($reviewFailReason[$key])){
                foreach ($reviewFailReason[$key] as $value){
                    $nodes[$key]['countNodes'] += count($value);
                }
            }
            /*if (isset($reviewFailReason[$key][0]) && !empty($reviewFailReason[$key][0])){
                $nodes[$key]['countNodes']++;
            }
            if (isset($reviewFailReason[$key][1]) && !empty($reviewFailReason[$key][1])){
                $nodes[$key]['countNodes']++;
            }
            if (isset($reviewFailReason[$key][2]) && !empty($reviewFailReason[$key][2])){
                $nodes[$key]['countNodes']++;
            }
            if (isset($reviewFailReason[$key][3]) && !empty($reviewFailReason[$key][3])){
                $nodes[$key]['countNodes']++;
            }
            if (isset($reviewFailReason[$key][4]) && !empty($reviewFailReason[$key][4])){
                $nodes[$key]['countNodes']++;
            }
            if (isset($reviewFailReason[$key][5]) && !empty($reviewFailReason[$key][5])){
                $nodes[$key]['countNodes']++;
            }*/
        }
        $this->view->nodes      = $nodes;
        $this->view->outwarddelivery     = $modify;
        $this->view->users      = $this->loadModel('user')->getPairs('noletter');
        $this->view->reviewFailReason      = $reviewFailReason;
        $this->view->modifycncc = $modify->isNewModifycncc ? $this->loadModel('modifycncc')->getByID($modify->modifycnccId) : new stdClass();
        $this->display();
    }
    /**
     * @param $app
     * 根据系统获取遗留缺陷
     */
    public function ajaxGetLeaveDefectsByApp($app) {
        $leaveDefectList = array('' => '') + $this->dao->select('id, concat(concat(code, "-"), title)')->from(TABLE_DEFECT)
                ->where('app')->eq($app)
                ->andWhere('dealSuggest')->in(array('suggestClose', 'nextFix'))
                ->andWhere('status')->eq('tofeedback')
                ->andWhere('syncStatus')->eq(0)
                ->fetchPairs();
        die(html::select('leaveDefect[]', $leaveDefectList,array_column($leaveDefectList,'id'), "class='form-control chosen'multiple"));
    }
    /**
     * @param $app
     * 根据系统获取修复缺陷
     */
    public function ajaxGetfixDefectsByApp($app) {
        $fixDefectList = array('' => '') + $this->dao->select('id, concat(concat(code, "-"), title)')->from(TABLE_DEFECT)
                ->where('app')->eq($app)
                ->andWhere('dealSuggest')->eq('fix')
                ->andWhere('status')->eq('tofeedback')
                ->andWhere('syncStatus')->eq(0)
                ->fetchPairs();
        die(html::select('fixDefect[]', $fixDefectList,array_column($fixDefectList,'id'), "class='form-control chosen'multiple"));
    }

    /**
     * @param $productEnrollId
     */
    public function ajaxGetCheckList($productEnrollId){
        /**
         * @var productEnrollModel $productEnrollModel
         * 获取产品登记详情
         */
        $productEnrollModel = $this->loadModel('productEnroll');
        $info = $productEnrollModel->getByID($productEnrollId);
        echo json_encode($info);
    }
    /**
     * @param $id
     * 根据异常变更单获取该变更单关联的问题单、需求条目
     */
    public function ajaxGetorderByabnormalId($id,$isAbnormal,$isDisable=''){

        //关联问题
//        $problemList       = array('' => '') + $this->loadModel('problem')->getPairsAbstract('noclosed');

//        $demandList        = array('' => '') + $this->loadModel('demand')->getPairsTitle('noclosed');
        $secondorderList   = array('' => '') + $this->loadModel('secondorder')->getNamePairs();
        $str = '';
        if ($isAbnormal == 1){
            /**
             * @var modifycnccModel $modifycnccModel
             * 获取生产变更详情
             */
            $modifycnccModel = $this->loadModel('modifycncc');
            $out = $this->dao->select("id")->from(TABLE_OUTWARDDELIVERY)->where('modifycnccId')->eq($id)->fetch();
            $info = $this->outwarddelivery->getByID($out->id);
            $str = 'disabled';
        }else{
            $info = $this->outwarddelivery->getByID($id);
        }
        $demandList = $this->loadModel('demand')->modifySelectByEdit($info->demandId, 'outwarddelivery', $id, $info->isNewModifycncc);
        $problemIds = [];
        if ($info->problemId){
            $problemIds = array_filter(explode(',',$info->problemId));
        }
        $problemList       = array('' => '') + $this->loadModel('problem')->getPairsAbstract('noclosed','',$problemIds);

        if ($info){
            $data[2] = html::select('secondorderId[]', $secondorderList, $info->secondorderId, "class='form-control chosen secondorderIdClass' multiple $str");
            if ($str == '') $str = $isDisable;
            $data[0] = html::select('demandId[]', $demandList, $info->demandId, "class='form-control chosen demandIdClass' onchange='selectDemand()' multiple $str");
            $data[1] = html::select('problemId[]', $problemList, $info->problemId,"class='form-control chosen problemIdClass' multiple $str");
        }else{
            $data[2] = html::select('secondorderId[]', $secondorderList, [], "class='form-control chosen secondorderIdClass' multiple $str");
            if ($str == '') $str = $isDisable;
            $data[0] = html::select('demandId[]', $demandList, [], "class='form-control chosen demandIdClass' onchange='selectDemand()' multiple $str");
            $data[1] = html::select('problemId[]', $problemList, [],"class='form-control chosen problemIdClass' multiple $str");
        }
        echo json_encode($data);
    }
    /**
     * @param $modify 对外交付数据信息
     * 获取变更单被关联的信息,以及关联的变更单
     */
    public function getAbnormalById($modify){
        $res = $this->dao->select('id,code')->from(TABLE_MODIFYCNCC)->where('id')->in($modify->abnormalCode)->fetchall();
        $ret = [];
        if ($modify->modifycnccId > 0){
            $findInSet = '(FIND_IN_SET("'.$modify->modifycnccId.'",abnormalCode))';
            $ret = $this->dao->select('id,modifycnccId')->from(TABLE_OUTWARDDELIVERY)->where($findInSet)->fetch();
            $ret = $this->dao->select('id,code')->from(TABLE_MODIFYCNCC)->where('id')->eq($ret->modifycnccId)->fetch();
        }
        return ['newOrder'=>$res,'nowOrder'=>$ret];
    }
    /**
     * 异常变更单重新发起
     * @param $outwardID
     * @return void
     */
    public function reissue($outwardID = 0)
    {
        $this->app->loadLang('modify');
        if($_POST)
        {
            $outwarddeliveryID = $this->outwarddelivery->create();

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $outwarddeliveryObj = $this->outwarddelivery->getByID($outwarddeliveryID);
            if($outwarddeliveryObj->isNewTestingRequest == 1){
                $this->loadModel('action')->create('testingrequest', $outwarddeliveryObj->testingRequestId, 'created', $this->post->comment);
            }
            if($outwarddeliveryObj->isNewProductEnroll == 1){
                $this->loadModel('action')->create('productenroll', $outwarddeliveryObj->productEnrollId, 'created', $this->post->comment);
            }
            if($outwarddeliveryObj->isNewModifycncc == 1){
                $this->loadModel('action')->create('modifycncc', $outwarddeliveryObj->modifycnccId, 'created', $this->post->comment);
            }
            if ($_POST['abnormalCode']){
                $modify = $this->loadModel('modifycncc')->getByID($_POST['abnormalCode']);
                $this->post->comment = $this->lang->outwarddelivery->associaitonOrder.'：'.$modify->code.'<br/>'.$this->post->comment;
            }
            $this->loadModel('action')->create('outwarddelivery', $outwarddeliveryID, 'reissue', $this->post->comment);

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = inlink('browse');
            $response['status']  = $outwarddeliveryObj->status;
            $response['id']      = $outwarddeliveryID;
            if ($_POST['issubmit'] == 'submit'){
                $response['message'] = $this->lang->submitSuccess;
            }
            if ($response['status'] == 'waitsubmitted' && $_POST['issubmit'] == 'submit'){
                $response['iframeUrl']  = $this->createLink('outwarddelivery', 'submit', "id=".$outwarddeliveryID."&linkType=2",'',true);
            }
            $this->send($response);
        }
        $demandLang = $this->app->loadLang('demand')->demand;
        $this->loadModel("modifycncc");
        $abnormalList = array('' => '') + $this->outwarddelivery->getModifyAbnormal();

        //获取对外交付信息
        $outwarddelivery = $this->loadModel('outwarddelivery')->getByID($outwardID);
        if(empty($outwarddelivery->ROR)){
            $outwarddelivery->RORList = array();
        }else{
            $outwarddelivery->RORList = json_decode(json_encode($outwarddelivery->ROR),true);
        }
        if(!empty($outwarddelivery->consumed)){
            $outwarddelivery->consumed = array_pop($outwarddelivery->consumed)->consumed;
        }

        $this->view->outwarddelivery   = $outwarddelivery;
        if($outwarddelivery->isNewTestingRequest == 1){
            $testingrequest = $this->loadModel('testingrequest')->getByID($outwarddelivery->testingRequestId);
            $testingrequest->isOnly = false;
            $testingrequest->disable = false;
            $this->view->testingrequest   = $testingrequest;
        }
        if($outwarddelivery->isNewProductEnroll == 1){
            $productenroll   = $this->loadModel('productenroll')->getByID($outwarddelivery->productEnrollId);
            $productenroll->mediaInfoList =  json_decode(json_encode($productenroll->mediaInfo));
            $productenroll->isOnly = false;
            $productenroll->disable = false;
            $this->view->productenroll = $productenroll;
        }
        if($outwarddelivery->isNewModifycncc == 1){
            $modifycncc = $this->loadModel('modifycncc')->getByID($outwarddelivery->modifycnccId);
            $modifycncc->isOnly = false;
            $modifycncc->disable = false;
            $this->view->modifycncc   = $modifycncc;
            $abnormalList[$outwarddelivery->modifycnccId] = $modifycncc->code;
//            if (isset($this->lang->modifycncc->implementModalityList[$modifycncc->implementModality]) && (int)$modifycncc->implementModality > 0){
//                $this->lang->modifycncc->implementModalityNewList[$modifycncc->implementModality] = $this->lang->modifycncc->implementModalityList[$modifycncc->implementModality];
//            }
        }
        $this->view->reviewReportList = array('' => '') + $this->dao->select('id,title')->from(TABLE_REVIEW)
                ->where('project')->eq($outwarddelivery->projectPlanId)
                ->andWhere('status')->eq('reviewpass')
                ->andWhere('deleted')->eq(0)
                ->orderBy('id_desc')
                ->fetchPairs();
        $this->view->abnormalList = $abnormalList;

        //标题
        $this->view->title = $this->lang->outwarddelivery->copy;
        //申请测试单键值对
        $this->view->testingrequestList = array('' => '') + $this->loadModel("testingrequest")->getPairs();
        //产品等级键值对
        $this->view->productenrollList = array('' => '') + $this->loadModel("productenroll")->getPairs();
        //产品线键值对
        $this->view->productlineList = array('' => '') + $this->loadModel('productline')->getPairs();
        //所属系统
        $this->view->appAll  =  $this->loadModel('application')->getApplicationCodePairsSyncQz();
        $this->view->appList = array('' => '') + array_column($this->view->appAll, 'name', 'id');
        //产品名称
        $app = 0;
        if ($outwarddelivery->createdDate > "2023-05-31 23:59:59"){
            $app = trim($outwarddelivery->app,',');
        }
        $this->view->productList   = array('' => '') + $this->loadModel('product')->getProductWithCodeName('noclosed',0,'',$app);
        //所属项目
        $this->view->projectList       = array('' => '') + $this->loadModel('projectplan')->getAliveProjects(false);
        //关联问题
        $this->view->problemList       = array('' => '') + $this->loadModel('problem')->getPairsAbstract('noclosed','',array_filter(explode(',',$outwarddelivery->problemId)));
        //关联二线工单
        $this->view->secondorderList        = array('' => '') + $this->loadModel('secondorder')->getNamePairs();
        //关联需求
        $singleUsageFlag = isset($this->config->singleUsage) && 'on' == $this->config->singleUsage;
        $this->view->demandList = array('' => '') + $this->loadModel('demand')->modifySelect('outwarddelivery', 0, $outwarddelivery->isNewModifycncc);
        //关联需求任务
        if(empty($outwarddelivery->demandId)){
            $requirementList = array('' => '') + $this->dao->select('id,concat(concat(concat(code,"（"),name),"）")')->from(TABLE_REQUIREMENT)->where('status')->ne('deleted')->fetchPairs();
        }else{
            if ($outwarddelivery->createdDate <= "2023-07-04 23:59:59"){
                $opinionId = $this->dao->select('distinct opinionID')->from(TABLE_DEMAND)->where('id')->in(explode(',',$outwarddelivery->demandId))->fetchAll();
                $requirementList = array('' => '') + $this->dao->select('id,concat(concat(concat(code,"（"),name),"）")')->from(TABLE_REQUIREMENT)->where('opinion')->in(array_column($opinionId,'opinionID'))->andWhere('status')->ne('deleted')->fetchpairs();
            }else{
                $opinionId = $this->dao->select('distinct requirementID')->from(TABLE_DEMAND)->where('id')->in(explode(',',$outwarddelivery->demandId))->fetchAll();
                $requirementList = array('' => '') + $this->dao->select('id,concat(concat(concat(code,"（"),name),"）")')->from(TABLE_REQUIREMENT)->where('id')->in(array_column($opinionId,'requirementID'))->andWhere('status')->ne('deleted')->fetchpairs();
            }
        }
        $this->view->requirementList   = $requirementList;
        //所属CBP项目
        $this->view->cbpprojectList     = array('' => '') + $this->dao->select('code,concat(concat(concat(code,"（"),name),"）")')->from(TABLE_CBPPROJECT)->fetchPairs();
        //产品变更
        $this->view->apps           = array('' => '') + $this->loadModel('application')->getapplicationNameCodePairsWithPartition2();
        $this->view->modifycnccList = array('' => '') + $this->dao->select('id,concat(concat(concat(code,"（"),substring(`desc`,1,255)),"）")')->from(TABLE_MODIFYCNCC)->where('deleted')->eq('0')->fetchPairs();
        //审核节点下的审核人列表
        $reviewers            = $this->modifycncc->getReviewers();
        //审核节点下的审核人列表
        $reviewerAccounts     = $this->loadModel('review')->getReviewerAccounts($reviewers);
        //审核节点下默认设置审核节点人
        $defChosenReviewNodes = $this->config->modifycncc->create->setDefChosenReviewNodes;
        $this->view->reviewers            = $reviewers;
        $this->view->reviewerAccounts     = $reviewerAccounts;
        $this->view->defChosenReviewNodes = $defChosenReviewNodes;
        //审核节点以及审核节点的审核人
        //$nodesReviewers = $this->loadModel('review')->getAllNodeReviewers('outwarddelivery', $outwardID, $outwarddelivery->version);
        //$this->view->nodesReviewers = $nodesReviewers;
        $this->view->leaveDefectList = $leaveDefectList = array('' => '') + $this->dao->select('id, concat(concat(code, "-"), title)')->from(TABLE_DEFECT)
                ->where('app')->eq(trim($outwarddelivery->app,','))
                ->andWhere('dealSuggest')->in(array('suggestClose', 'nextFix'))
                ->andWhere('status')->eq('tofeedback')
                ->andWhere('syncStatus')->eq(0)
                ->fetchPairs();
        $this->view->fixDefectList   = array('' => '') + $this->dao->select('id, concat(concat(code, "-"), title)')->from(TABLE_DEFECT)
                ->where('app')->eq(trim($outwarddelivery->app,','))
                ->andWhere('dealSuggest')->eq('fix')
                ->andWhere('status')->eq('tofeedback')
                ->andWhere('syncStatus')->eq(0)
                ->fetchPairs();

        $this->display();
    }
    /**
     * @param $id
     * 修改关联的异常变更单
     */
    public function editabnormalorder($id){
        $outwarddelivery = $this->outwarddelivery->getByID($id);
        if ($_POST){
            $changes = $this->outwarddelivery->editabnormalorder($id);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            if ($_POST['abnormalCode'] && $_POST['abnormalCode'] != $outwarddelivery->abnormalCode){
                $info = $this->loadModel('modifycncc')->getByID($_POST['abnormalCode']);
                $this->post->comment = $this->lang->outwarddelivery->associaitonOrder.'：'.$info->code.'<br/>'.$this->post->comment;
            }
            $actionID = $this->loadModel('action')->create('outwarddelivery', $id, 'editabnormalorder', $this->post->comment);
            $this->action->logHistory($actionID, $changes);
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
            exit;
        }
        $abnormalOrder = $this->getAbnormalById($outwarddelivery);
        $outwarddelivery->abnormalCode = $abnormalOrder['nowOrder']->id;
        //获取未被关联的异常变更单
        $abnormalList = array('' => '') + $this->outwarddelivery->getModifyAbnormal();
        unset($abnormalList[$outwarddelivery->modifycnccId]);
        //获取关联。被关联的变更单
        $this->view->abnormalOrder              = $abnormalOrder;
        $this->view->abnormalList               = $abnormalList;
        $this->display();
    }
}