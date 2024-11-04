<?php

class testingrequest extends control
{
    public function browse($browseType = 'all', $param = 0, $orderBy = 'code_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
      $this->loadModel('outwarddelivery');
      $this->view->title        = $this->lang->testingrequest->browse;
      $this->view->users = $this->loadModel('user')->getPairs('noletter');
      $this->view->depts = $this->loadModel('dept')->getTopPairs();
      $this->view->dmap  = $this->dao->select('account,realname,dept')->from(TABLE_USER)->fetchAll('account');
      $apps = $this->loadModel('application')->getapplicationNameCodePairs();
      $productList = $this->loadModel('product')->getPairs();
      $projectList = $this->loadModel('projectplan')->getAllProjects();

      $browseType = strtolower($browseType);

      $queryID = ($browseType == 'bysearch')  ? (int)$param : 0;
      $actionURL = $this->createLink('testingrequest', 'browse', "browseType=bySearch&param=myQueryID");
      $this->testingrequest->buildSearchForm($queryID, $actionURL);

      /* Load pager. */
      $this->app->loadClass('pager', $static = true);
      $pager = new pager($recTotal, $recPerPage, $pageID);
        /* 设置详情页面返回的url连接。*/
      $this->session->set('testingrequestList', $this->app->getURI(true));
      $this->view->testingrequest = $this->testingrequest->getList($browseType,$queryID,$orderBy,$pager);
      foreach ($this->view->testingrequest as $item){
        $appName = array();
        foreach (explode(',', $item->app) as $app){
          if(!empty($app)){
            $appName[] = zget($apps, $app);
          }
        }
        $item->appName = implode('，', $appName);

        $testProduct = array();
        foreach (explode(',', $item->productId) as $product){
          if(!empty($product)){
            $testProduct[] = zget($productList, $product);
          }
        }
        $item->testProduct = implode('，', $testProduct);
        $item->projectPlanId = zget($projectList, $item->projectPlanId);
      }

      $this->view->orderBy    = $orderBy;
      $this->view->pager      = $pager;
      $this->view->param      = $param;
      $this->view->browseType = $browseType;
      $this->display();
    }

    /*public function setNew()
    {
        $data = $this->testingrequest->setNew(); //ext more
        die($data);
    }*/

    public function view($testingrequestID = 0)
    {
        $this->app->loadLang('release');
        $this->app->loadLang('projectrelease');
        $this->app->loadLang('outwarddelivery');
        $this->app->loadLang('application');
        $this->app->loadLang('file');
        $this->app->loadLang('api');

        $this->view->testingrequest    = $this->testingrequest->getByID($testingrequestID);
        $this->view->title             = $this->lang->testingrequest->view;

        $this->view->allLines          = $this->loadModel('productline')->getPairs();
        $this->view->allProductNames   = $this->loadModel('product')->getNamePairs();
        $this->view->allProductCodes   = $this->loadModel('product')->getCodePairs();
        $this->view->depts             = $this->loadModel('dept')->getDeptPairs();
        $this->view->users             = $this->loadModel('user')->getPairs('noletter');
        $this->view->projects          = array('' => '') + $this->loadModel('projectplan')->getProject($this->view->testingrequest->implementationForm == 'second');//更新获取所属项目的方法
        #$this->view->releases         = array('' => '') + $this->loadModel('project')->getReleases($this->view->outwarddelivery->projectPlanId);
        $this->view->testingrequest->appsInfo    = (Object)$this->loadModel('outwarddelivery')->getAppInfo(explode(',',$this->view->testingrequest->app));
        $this->view->testingrequest->CBPInfo     = $this->loadModel('outwarddelivery')->getCBPInfo($this->view->testingrequest->CBPprojectId);
        $this->view->releaseInfoList                 = $this->loadModel('outwarddelivery')->getReleaseInfoInIds($this->view->testingrequest->release);
        $this->view->releasePushLogs             = $this->loadModel('release')->getPushLog($this->view->testingrequest->release);
        $this->view->demand         = $this->loadModel('demand')->getPairsByIds(explode(',', $this->view->testingrequest->demandId));
        $this->view->problem        = $this->loadModel('problem')->getPairsByIds(explode(',', $this->view->testingrequest->problemId));
        $this->view->secondorder         = $this->loadModel('secondorder')->getPairsByIds(explode(',', $this->view->testingrequest->secondorderId));
        $this->view->requirement    = $this->loadModel('requirement')->getPairsByIds(explode(',', $this->view->testingrequest->requirementId));

        $this->view->outwarddeliveryPairs  = $this->loadModel('outwarddelivery')->getDetailPairs();
        $this->view->testingrequestPairs   = $this->testingrequest->getCodePairs();
        $this->view->productenrollPairs    = $this->loadModel('productenroll')->getCodePairs();
        $this->view->modifycnccPairs       = $this->loadModel('modifycncc')->getCodePairs();

        $this->view->TRlog      = $this->testingrequest->getRequestLog($testingrequestID);

        $this->view->parentId   = $this->loadModel('outwarddelivery')->getOutwardDeliveryByTypeId('testingrequest',$testingrequestID);
        $this->view->allRelations   = $this->loadModel('outwarddelivery')->getTypeRelations('testingrequest',$testingrequestID);
        $this->view->nodes      = $this->loadModel('review')->getNodes('outwardDelivery', $this->view->parentId, $this->view->testingrequest->version);
        $this->view->parent     = $this->loadModel('outwarddelivery')->getByID($this->view->parentId);
        $this->view->actions    = $this->loadModel('action')->getList('testingrequest', $testingrequestID );

        $this->display();
    }

    public function export($orderBy = 'id_desc', $browseType = 'all')
    {
      if($_POST)
      {
        $this->loadModel('file');
        $testingrequestLang   = $this->lang->testingrequest;
        $testingrequestConfig = $this->config->testingrequest;

        /* Create field lists. */
        $fields = $this->post->exportFields ? $this->post->exportFields : explode(',', $testingrequestConfig->list->exportFields);
        foreach($fields as $key => $fieldName)
        {
          $fieldName = trim($fieldName);
          $fields[$fieldName] = isset($testingrequestLang->$fieldName) ? $testingrequestLang->$fieldName : $fieldName;
          unset($fields[$key]);
        }

        /* Get testingrequests. */
        $testingrequests = array();
        if($this->session->testingRequestOnlyCondition)
        {
          $testingrequests = $this->dao->select('*')->from(TABLE_TESTINGREQUEST)->where($this->session->testingRequestQueryCondition)
            ->beginIF($this->post->exportType == 'selected')->andWhere('id')->in($this->cookie->checkedItem)->fi()
            ->orderBy($orderBy)->fetchAll('id');
        }
        else
        {
          $stmt = $this->dbh->query($this->session->testingRequestQueryCondition . ($this->post->exportType == 'selected' ? " AND id IN({$this->cookie->checkedItem})" : '') . " ORDER BY " . strtr($orderBy, '_', ' '));
          while($row = $stmt->fetch()) $testingrequests[$row->id] = $row;
        }

        $requirementIds = array_column($testingrequests,'requirementId');
        foreach ($requirementIds as $rk=>$rv) {
            $requirementIds[$rk] = trim($rv,',');
        }
        $users = $this->loadModel('user')->getPairs('noletter');
        $depts = $this->loadModel('dept')->getTopPairs();
        $dmap  = $this->dao->select('account,realname,dept')->from(TABLE_USER)->fetchAll('account');
        $apps  = $this->loadModel('application')->getapplicationNameCodePairsWithisPayment();
        $isPaymentPairs = $this->dao->select('`key`,value')->from(TABLE_LANG)->where('module')->eq('application')->andWhere('section')->eq('isPaymentList')->fetchPairs();
        $teamPairs = $this->dao->select('`key`,value')->from(TABLE_LANG)->where('module')->eq('application')->andWhere('section')->eq('teamList')->fetchPairs();
        $appList          = array_column($apps, 'name','id');;
        $isPaymentList    = array_column($apps, 'isPayment','id');
        $teamList         = array_column($apps, 'team','id');
        $requirementList  = $this->dao->select('id,concat(concat(concat(code,"（"),name),"）")')->from(TABLE_REQUIREMENT)->where('entriesCode')->like('requirements%')->fetchPairs();
        $requireList = $this->loadModel('requirement')->getPairsByIds($requirementIds);
        $cbpprojectList   = $this->dao->select('code,concat(concat(concat(code,"（"),name),"）")')->from(TABLE_CBPPROJECT)->fetchPairs();
        $productList      = $this->loadModel('product')->getList();
        $projectList      = $this->loadModel('projectplan')->getAllProjects();
        $problemList      = $this->loadModel('problem')->getPairsAbstract();
        $productNameList  = array_column($productList, 'name' , 'id');
        $demandList       = $this->loadModel('demand')->getPairsTitle('noclosed');
        $secondorderList     =  $this->loadModel('secondorder')->getNamePairs();

        foreach ($testingrequests as $testingrequest) {
          $outwardDeliveryId = $this->loadModel('outwarddelivery')->getOutwardDeliveryByTypeId('testingrequest',$testingrequest->id);
          $outwardDelivery   = $this->loadModel('outwarddelivery')->getByID($outwardDeliveryId);
          $productEnroll     = $this->loadModel('productenroll')->getByID($outwardDelivery->productEnrollId);
          $modifycncc        = $this->loadModel('modifycncc')->getByID($outwardDelivery->modifycnccId);
          $createdBy = $testingrequest->createdBy;
//          $testingrequest->status                 = $testingrequestLang->statusList[$testingrequest->status];
          $testingrequest->status                 = $testingrequest->closed == '1' ? $this->lang->testingrequest->labelList['closed'] :zget($this->lang->testingrequest->statusList, $testingrequest->status);;
          $testingrequest->createdBy              = zget($users, $createdBy,$createdBy);
          //迭代二十六-导出删除部门第一个'/'
          $testingrequest->createdDept            = ltrim($depts[$dmap[$createdBy]->dept], '/');
          $testingrequest->implementationForm     = zget($testingrequestLang->implementationFormList,$testingrequest->implementationForm);
          $testingrequest->acceptanceTestType     = zget($this->lang->testingrequest->acceptanceTestTypeList, $testingrequest->acceptanceTestType);

          $testingrequest->createdDate = substr($testingrequest->createdDate,0, 10);//创建时间
          $testingrequest->editedDate  = substr($testingrequest->editedDate,0, 10);// 编辑时间

          $app = array();
          $isPayment = array();
          $team = array();
          foreach (explode(',', $testingrequest->app) as $item){
            if(!empty($item)) {
              $app[] = zget($appList, $item);
              $isPayment[] = $isPaymentPairs[zget($isPaymentList, $item, '')];
              $team[] = $teamPairs[zget($teamList, $item, '')];
            }
          }
          $testingrequest->app        = implode('，', $app);
          $testingrequest->isPayment  = implode('，', $isPayment);
          $testingrequest->team       = implode('，', $team);

          $products = explode(',', $testingrequest->productId);
          $productNames = array();
          foreach ($products as $product){
            if(!empty($product)){
              $productNames[] = zget($productNameList, $product);
            }
          }
          $testingrequest->testProductName = implode(',', $productNames);
          $relations = $this->loadModel('outwarddelivery')->getRelations($testingrequest->id,'testingRequest');
          $objects = array();
          $outwardDeliveryCodeList = array();
          foreach($relations as $relation) {
            $objects[$relation['relationType']][$relation['relationID']] = $relation['relationship'];
          }
          foreach($objects['outwardDelivery'] as $objectID){
            $outwardDeliveryCodeList[] = $objectID['code'];
          }
          $testingrequest->relatedOutwardDelivery = implode(',', $outwardDeliveryCodeList);
          $testingrequest->relatedProductEnroll   = $productEnroll->code;
          $testingrequest->relatedModifycncc      = $modifycncc->code;

          $testingrequest->projectName = zget($projectList, $testingrequest->projectPlanId);


          foreach(explode(',', $testingrequest->requirementId) as $requirementId){
            if(!empty($requirementId)) {
//              $testingrequest->requirementId = zget($requirementList, $requirementId, '') . ',';
              $testingrequest->requirementId = $requireList->$requirementId['code']."（".$requireList->$requirementId['name']."）" . ',';
            }
          };
          $testingrequest->requirementId = rtrim($testingrequest->requirementId,',');
          foreach(explode(',', $testingrequest->CBPprojectId) as $CBPprojectId){
            if(!empty($CBPprojectId)) {
              $testingrequest->CBPprojectId = zget($cbpprojectList, $CBPprojectId, '') . ',';
            }
          };
          $secondorderIds = explode(',', $testingrequest->secondorderId);
          $secondorderNameList = array();
          foreach ($secondorderIds as $secondorder){
              if(!empty($secondorder)){
                  $secondorderNameList[] = zget($secondorderList, $secondorder,'');
              }
          }
          $testingrequest->secondorderId             = implode(',',$secondorderNameList);
          $problems = explode(',', $testingrequest->problemId);
          $problemId = '';
          foreach ($problems as $problem){
            if(!empty($problem)){
              $problemId .= zget($problemList, $problem,'').PHP_EOL;
            }
          }
          $testingrequest->problemId             = $problemId;

          $demands = explode(',', $testingrequest->demandId);
          $demandId = '';
          foreach ($demands as $demand){
            if(!empty($demand)){
              $demandId .= zget($demandList, $demand).PHP_EOL;
            }
          }
          $testingrequest->demandId = $demandId;
          $testingrequest->dealUserContact = $testingrequest->contactTel;
          $testingrequest->closedBy = $outwardDelivery->closedBy;
          $testingrequest->closedDate = $outwardDelivery->closedDate;
          $testingrequest->closedReason = $outwardDelivery->closedReason;
          $testingrequest->isCentralizedTest         = zget($this->lang->testingrequest->isCentralizedTestList,$testingrequest->isCentralizedTest);
        }

        $this->post->set('fields', $fields);
        $this->post->set('rows', $testingrequests);
        $this->post->set('kind', 'testingRequest');
        $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
      }
  
      $this->view->fileName        = $this->lang->testingrequest->exportName;
      $this->view->allExportFields = $this->config->testingrequest->list->exportFields;
      $this->view->customExport    = true;
      $this->display();
    }

    public function setNew($outwardId)
    {
        $this->loadModel('testingrequest')->pushTestingrequest($outwardId);
    }

    /**
     * 编辑退回次数
     * @param $testingrequestID
     * @return void
     */
    public function editreturntimes($outwardDeliveryId = 0){
        if($_POST)
        {
            $this->testingrequest->editreturntimes($outwardDeliveryId);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }


            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }
        $this->view->title             = $this->lang->testingrequest->editreturntimes;
        $this->display();
    }
    public function showHistoryNodes($id){
        $modify = $this->loadModel('outwarddelivery')->getByID($id);
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
            }
            $nodes[$version]['nodes'] = $data;
        }
        foreach ($nodes as $key=>$node) {
            $nodes[$key]['countNodes'] = count($node['nodes']);
            if (isset($reviewFailReason[$key][0]) && !empty($reviewFailReason[$key][0])){
                $nodes[$key]['countNodes']++;
            }
            if (isset($reviewFailReason[$key][1]) && !empty($reviewFailReason[$key][1])){
                $nodes[$key]['countNodes']++;
            }
            foreach ($node['nodes'] as $v2){
                //$this->lang->outwarddelivery->reviewNodeList :4,6,7不显示父级节点（主单历史节点）
                if (in_array($v2->stage,[4,5,6,7])){
                    $nodes[$key]['countNodes']--;
                }
            }
        }
        $this->view->nodes      = $nodes;
        $this->view->outwarddelivery     = $modify;
        $this->view->users      = $this->loadModel('user')->getPairs('noletter');
        $this->view->reviewFailReason      = $reviewFailReason;
        $this->display();
    }
    public function ajaxdeliverystatistics($start = '2024_01_01',$end='2024_01_31'){
        $start = str_replace('_','-',$start).' 00:00:00';
        $end   = str_replace('_','-',$end).' 23:59:59';
        // 所属项目、部门
        $projects = $this->dao->select('t1.project,t2.name,t1.bearDept,t3.name as deptName')->from(TABLE_PROJECTPLAN)->alias('t1')
            ->leftjoin(TABLE_PROJECT)->alias('t2')
            ->on('t1.project=t2.id')
            ->leftjoin(TABLE_DEPT)->alias('t3')
            ->on('t1.bearDept=t3.id')
            ->where('t1.deleted')->eq(0)
//            ->andwhere('t2.status')->ne('closed')
            ->fetchAll();
        $list = $this->dao->select("objectID,count(objectID) as num")->from(TABLE_ACTION)
            ->where('objectType')->eq('testingrequest')
            ->andWhere('action')->in(['synctestrequest','edittestrequest'])
            ->andWhere('date')->ge($start)
            ->andWhere('date')->le($end)
            ->groupBy('objectID')
            ->fetchall();

        $ids = array_values(array_unique(array_column($list,'objectID')));
        $tests = $this->dao->select('t1.id,t1.code,t1.status,t2.code as tcode,t1.projectPlanId,t1.implementationForm')->from(TABLE_TESTINGREQUEST)->alias("t1")
            ->leftjoin(TABLE_OUTWARDDELIVERY)->alias("t2")
            ->on("t1.id=t2.testingRequestId")
            ->where('t1.id')->in($ids)
            ->andWhere("t2.isNewTestingRequest")->eq('1')
            ->andWhere("t1.deleted")->eq('0')
//            ->andWhere('t1.status')->eq('testingrequestpass')
            ->fetchall();
        $data = [];
        foreach ($tests as $key => $test) {
            foreach ($projects as $project) {
                if ($project->project == $test->projectPlanId){
                    $tests[$key]->deptName = $project->deptName;
                    $tests[$key]->deptID   = $project->bearDept;
                }
            }
            foreach ($list as $v){
                if ($v->objectID == $test->id){
                    $tests[$key]->num = $v->num;
                }
            }
        }

        $depts = array_column($tests,'deptID');
        foreach ($depts as $dept) {
            $data[$dept] = [];
        }
        foreach ($data as $k2=>$v2){
            foreach ($tests as $key => $test) {
                if ($test->deptID == $k2){
                    $data[$test->deptID]['deptName'] = $test->deptName;
                    // 項目实现
                    if ($test->implementationForm == 'project'){
                        $data[$test->deptID]['projectNum'] += $test->num;
                        $data[$test->deptID]['projectCode'][] = $test->code."【".$test->num."】" . "【".$this->lang->testingrequest->statusList[$test->status]."】";
                    }
                    // 二线实现
                    if ($test->implementationForm == 'second'){
                        $data[$test->deptID]['secondNum'] += $test->num;
                        $data[$test->deptID]['secondCode'][] = $test->code."【".$test->num."】" . "【".$this->lang->testingrequest->statusList[$test->status]."】";
                    }

                }
                $data[$test->deptID]['projectCode'] = array_unique($data[$test->deptID]['projectCode']);
                $data[$test->deptID]['secondCode']  = array_unique($data[$test->deptID]['secondCode']);
            }
        }
        $consumeds = $this->dao->select('*')->from(TABLE_CONSUMED)
            ->where('deleted')->eq('0')
            ->andWhere('objectType')->eq('outwarddelivery')
            ->andWhere('after')->eq('testingrequestpass')
            ->andWhere('extra')->eq('测试申请单')
            ->andWhere('createdDate')->ge($start)
            ->andWhere('createdDate')->le($end)
            ->fetchall();
        $ids = array_values(array_unique(array_column($consumeds,'objectID')));
        $tests = $this->dao->select('t1.id,t2.id as tid,t1.code,t1.status,t2.code as tcode,t1.projectPlanId,t1.implementationForm,t1.returnTimes')->from(TABLE_TESTINGREQUEST)->alias("t1")
            ->leftjoin(TABLE_OUTWARDDELIVERY)->alias("t2")
            ->on("t1.id=t2.testingRequestId")
            ->where('t2.id')->in($ids)
            ->andWhere("t2.isNewTestingRequest")->eq('1')
            ->andWhere("t1.deleted")->eq('0')
//            ->andWhere('t1.status')->eq('testingrequestpass')
            ->fetchall();
        $list = $this->dao->select("objectID,count(objectID) as num")->from(TABLE_CONSUMED)
            ->where('deleted')->eq('0')
            ->andWhere('objectType')->eq('outwarddelivery')
            ->andWhere('after')->eq('testingrequestpass')
            ->andWhere('extra')->eq('测试申请单')
            ->andWhere('createdDate')->ge($start)
            ->andWhere('createdDate')->le($end)
            ->groupby('objectID')
            ->fetchall();
        foreach ($tests as $key => $test) {
            foreach ($projects as $project) {
                if ($project->project == $test->projectPlanId){
                    $tests[$key]->deptName = $project->deptName;
                    $tests[$key]->deptID   = $project->bearDept;
                }
            }
            foreach ($list as $v){
                if ($v->objectID == $test->tid){
                    $tests[$key]->num = $v->num;
                }
            }
        }
//        a($tests);
        $depts = array_column($tests,'deptID');
        foreach ($depts as $dept) {
            if (!isset($data[$dept])) $data[$dept] = [];

        }

        foreach ($data as $k2=>$dept){
            foreach ($tests as $key => $test) {
                if ($test->deptID == $k2){
                    $data[$test->deptID]['deptName'] = $test->deptName;
                    // 項目实现
                    if ($test->implementationForm == 'project'){
                        $returnTimes = $test->returnTimes+1;

                        $data[$test->deptID]['projectPassNum'] ++;
                        $data[$test->deptID]['projectPassSum'] += $returnTimes;
                        $data[$test->deptID]['projectCode2'][] = $test->code . "【". $returnTimes ."】" . "【".$this->lang->testingrequest->statusList[$test->status]."】";
                        if ($returnTimes == 1){
                            $data[$test->deptID]['projectOne']++;
                        }
                        if ($returnTimes == 2){
                            $data[$test->deptID]['projectTwo']++;
                        }
                        if ($returnTimes >= 3){
                            $data[$test->deptID]['projectThree']++;
                        }
                    }
                    // 二线实现
                    if ($test->implementationForm == 'second'){
                        $returnTimes = $test->returnTimes+1;
                        $data[$test->deptID]['secondCode2'][] = $test->code."【". $returnTimes ."】" . "【".$this->lang->testingrequest->statusList[$test->status]."】";
                        $data[$test->deptID]['secondPassNum'] ++;
                        $data[$test->deptID]['secondPassSum'] += $returnTimes;
                        if ($returnTimes == 1){
                            $data[$test->deptID]['secondOne']++;
                        }
                        if ($returnTimes == 2){
                            $data[$test->deptID]['secondTwo']++;
                        }
                        if ($returnTimes >= 3){
                            $data[$test->deptID]['secondThree']++;
                        }
                    }

                }
                $data[$test->deptID]['projectCode2'] = array_unique($data[$test->deptID]['projectCode2']);
                $data[$test->deptID]['secondCode2']  = array_unique($data[$test->deptID]['secondCode2']);
            }
        }
        $consumeds = $this->dao->select('*')->from(TABLE_CONSUMED)
            ->where('deleted')->eq('0')
            ->andWhere('objectType')->eq('outwarddelivery')
            ->andWhere('after')->in(['testingrequestreject','cancel','reject'])
            ->andWhere('extra')->eq('测试申请单')
            ->andWhere('createdDate')->ge($start)
            ->andWhere('createdDate')->le($end)
            ->fetchall();
        $ids = array_values(array_unique(array_column($consumeds,'objectID')));
        $tests = $this->dao->select('t1.id,t2.id as tid,t1.code,t1.status,t2.code as tcode,t1.projectPlanId,t1.implementationForm')->from(TABLE_TESTINGREQUEST)->alias("t1")
            ->leftjoin(TABLE_OUTWARDDELIVERY)->alias("t2")
            ->on("t1.id=t2.testingRequestId")
            ->where('t2.id')->in($ids)
            ->andWhere("t2.isNewTestingRequest")->eq('1')
            ->andWhere("t1.deleted")->eq('0')
//            ->andWhere('t1.status')->eq('testingrequestpass')
            ->fetchall();
        $list = $this->dao->select("objectID,count(objectID) as num")->from(TABLE_CONSUMED)
            ->where('deleted')->eq('0')
            ->andWhere('objectType')->eq('outwarddelivery')
            ->andWhere('after')->in(['testingrequestreject','cancel','reject'])
            ->andWhere('extra')->eq('测试申请单')
            ->andWhere('createdDate')->ge($start)
            ->andWhere('createdDate')->le($end)
            ->groupby('objectID')
            ->fetchall();
        foreach ($tests as $key => $test) {
            foreach ($projects as $project) {
                if ($project->project == $test->projectPlanId){
                    $tests[$key]->deptName = $project->deptName;
                    $tests[$key]->deptID   = $project->bearDept;
                }
            }
            foreach ($list as $v){
                if ($v->objectID == $test->tid){
                    $tests[$key]->num = $v->num;
                }
            }
        }
        $depts = array_column($tests,'deptID');
        foreach ($depts as $dept) {
            if (!isset($data[$dept])) $data[$dept] = [];

        }
        foreach ($data as $k2=>$dept){
            foreach ($tests as $key => $test) {
                if ($test->deptID == $k2){
                    // 項目实现
                    if ($test->implementationForm == 'project'){
                        $data[$test->deptID]['projectRejectNum'] += $test->num;
//                        $data[$test->deptID]['projectPassSum'] += $test->num;
                        $data[$test->deptID]['projectCode3'][] = $test->code . "【".$this->lang->testingrequest->statusList[$test->status]."】";
                    }
                    // 二线实现
                    if ($test->implementationForm == 'second'){
                        $data[$test->deptID]['secondPassSum'] += $test->num;
                        $data[$test->deptID]['secondCode3'][] = $test->code . "【".$this->lang->testingrequest->statusList[$test->status]."】";
                        $data[$test->deptID]['secondRejectNum'] += $test->num;
                    }

                }
                $data[$test->deptID]['projectCode3'] = array_unique($data[$test->deptID]['projectCode3']);
                $data[$test->deptID]['secondCode3']  = array_unique($data[$test->deptID]['secondCode3']);
            }
        }
        $data = array_values($data);
        foreach ($data as $k2=>$v2){
            $data[$k2]['projectCode'] = implode($v2['projectCode'],',');
            $data[$k2]['secondCode']  = implode($v2['secondCode'],',');
            $data[$k2]['projectCode2'] = implode($v2['projectCode2'],',');
            $data[$k2]['secondCode2']  = implode($v2['secondCode2'],',');
            $data[$k2]['projectCode3'] = implode($v2['projectCode3'],',');
            $data[$k2]['secondCode3']  = implode($v2['secondCode3'],',');
            $data[$k2] = (object)$data[$k2];
        }
        $_POST['exportFields'] = $this->config->testingrequest->export->templateFields;
        $_POST['title'] = '默认模板';
        $_POST['fileType'] = 'xlsx';
        $_POST['fileName'] = 'UAT';
        $_POST['exportType'] = 'all';
        $_POST['template'] = '0';
        foreach($this->config->testingrequest->export->templateFields as $field) $fields[$field] = $this->lang->testingrequest->exportFileds->$field;

        $this->post->set('fields', $fields);
        $this->post->set('kind', 'testingrequest');
        $this->post->set('rows', $data);
        $this->post->set('fileName', 'UAT_'.date('Y_m_d_H_i'));
        $this->fetch('file', 'export2' . $this->post->fileType, $_POST);

    }
    // 生产变更统计
    public function ajaxmodifystatistics($start='2024_01_01',$end='2024_01_31')
    {
        $start = str_replace('_','-',$start).' 00:00:00';
        $end   = str_replace('_','-',$end).' 23:59:59';
        // 所属项目、部门
        $projects = $this->dao->select('t1.project,t2.name,t1.bearDept,t3.name as deptName')->from(TABLE_PROJECTPLAN)->alias('t1')
            ->leftjoin(TABLE_PROJECT)->alias('t2')
            ->on('t1.project=t2.id')
            ->leftjoin(TABLE_DEPT)->alias('t3')
            ->on('t1.bearDept=t3.id')
            ->where('t1.deleted')->eq(0)
//            ->andwhere('t2.status')->ne('closed')
            ->fetchAll();
        // 金信生产变更交付次数
        $deliveryModifys = $this->dao->select("objectID,count(objectID) as num")->from(TABLE_CONSUMED)
            ->where('deleted')->eq('0')
            ->andWhere('objectType')->eq('modify')
            ->andWhere('after')->eq('withexternalapproval')
            ->andWhere('`before`')->eq('waitqingzong')
            ->andWhere('createdDate')->ge($start)
            ->andWhere('createdDate')->le($end)
            ->groupby('objectID')
            ->fetchall('objectID');
        // 清总生产变更交付次数
        $deliveryModifycnccs = $this->dao->select("objectID,count(objectID) as num")->from(TABLE_CONSUMED)
            ->where('deleted')->eq('0')
            ->andWhere('objectType')->eq('outwarddelivery')
            ->andWhere('after')->eq('centrepmreview')
            ->andWhere('`before`')->eq('waitqingzong')
            ->andWhere('createdDate')->ge($start)
            ->andWhere('createdDate')->le($end)
            ->groupby('objectID')
            ->fetchall('objectID');
        // 金信生产变更 成功+异常
        $modifysuccess = ['modifysuccess','modifysuccesspart'];
        $modifyerror   = ['modifyfail','modifyerror','modifyrollback','modifycancel','modifyreject','jxsynfailed','reject'];
        $modifyStatus = $modifysuccess + $modifyerror;
        // 金信生产变更 成功+异常
        $modifycnccsuccess = ['modifysuccess','modifysuccesspart'];
        $modifycnccerror   = ['reject','qingzongsynfailed','modifycancel','modifyreject','modifyfail'];
        $modifycnccStatus = $modifycnccsuccess + $modifycnccerror;

        $finalModifys = $this->dao->select("objectID")->from(TABLE_CONSUMED)
            ->where('deleted')->eq('0')
            ->andWhere('objectType')->eq('modify')
            ->andWhere('after')->in($modifyStatus)
            ->andWhere('createdDate')->ge($start)
            ->andWhere('createdDate')->le($end)
//            ->groupby('objectID')
            ->fetchall('objectID');

        $finalModifycnccs = $this->dao->select("objectID")->from(TABLE_CONSUMED)
            ->where('deleted')->eq('0')
            ->andWhere('objectType')->eq('outwarddelivery')
            ->andWhere('after')->in($modifycnccStatus)
            ->andWhere('createdDate')->ge($start)
            ->andWhere('createdDate')->le($end)
//            ->groupby('objectID')
            ->fetchall('objectID');

        $deliveryModifyIds = array_values(array_unique(array_column($deliveryModifys, 'objectID')));
        $finalModifyIds    = array_values(array_unique(array_column($finalModifys, 'objectID')));
        $ids = array_unique(array_merge($deliveryModifyIds,$finalModifyIds));
        $modifys = $this->dao->select('id,code,status,projectPlanId,implementationForm,returnTime')->from(TABLE_MODIFY)
            ->where('id')->in($ids)
            ->andWhere("status")->ne('deleted')
            ->fetchall();

        $deliveryModifycnccIds = array_values(array_unique(array_column($deliveryModifycnccs, 'objectID')));
        $finalModifycnccIds    = array_values(array_unique(array_column($finalModifycnccs, 'objectID')));
        $ids = array_unique(array_merge($deliveryModifycnccIds,$finalModifycnccIds));
        $modifycnccs = $this->dao->select('t2.id,t1.code,t1.status,t2.projectPlanId,t2.implementationForm,t1.returnTimes')->from(TABLE_MODIFYCNCC)->alias("t1")
            ->leftjoin(TABLE_OUTWARDDELIVERY)->alias("t2")
            ->on("t1.id=t2.modifycnccId")
            ->where('t2.id')->in($ids)
            ->andWhere("t1.status")->ne('deleted')
            ->andWhere("t1.deleted")->eq('0')
            ->fetchall();

        $finalModifycnccs = $this->dao->select("objectID")->from(TABLE_CONSUMED)
            ->where('deleted')->eq('0')
            ->andWhere('objectType')->eq('outwarddelivery')
            ->andWhere('objectID')->in($finalModifycnccIds)
            ->andWhere('after')->eq('centrepmreview')
            ->andWhere('`before`')->eq('waitqingzong')
//            ->andWhere('createdDate')->ge($start)
//            ->andWhere('createdDate')->le($end)
//            ->groupby('objectID')
            ->fetchall();
//a($finalModifycnccs);exit;
        $finalModifys = $this->dao->select("objectID")->from(TABLE_CONSUMED)
            ->where('deleted')->eq('0')
            ->andWhere('objectType')->eq('modify')
            ->andWhere('objectID')->in($finalModifyIds)
            ->andWhere('after')->eq('withexternalapproval')
            ->andWhere('`before`')->eq('waitqingzong')
//            ->andWhere('createdDate')->ge($start)
//            ->andWhere('createdDate')->le($end)
//            ->groupby('objectID')
            ->fetchall();

        // 有数据的部门
        $depts = [];
        foreach ($modifys as $key => $modify) {
            foreach ($projects as $project) {
                if ($modify->projectPlanId == $project->project){
                    $depts[$project->bearDept] = $project->deptName;
                    $modifys[$key]->deptId = $project->bearDept;
                }
            }
            foreach ($deliveryModifys as $deliveryModify) {
                if ($deliveryModify->objectID == $modify->id){
                    $modifys[$key]->num = $deliveryModify->num;
                }
            }
        }
        foreach ($modifycnccs as $key => $modifycncc) {
            foreach ($projects as $project) {
                if ($modifycncc->projectPlanId == $project->project){
                    $depts[$project->bearDept] = $project->deptName;
                    $modifycnccs[$key]->deptId = $project->bearDept;
                }
            }
            foreach ($deliveryModifycnccs as $deliveryModifycncc) {
                if ($deliveryModifycncc->objectID == $modifycncc->id){
                    $modifycnccs[$key]->num = $deliveryModifycncc->num;
                }
            }
        }
        $data = [];
//a($finalModifycnccs);exit;
        $fields = $this->config->testingrequest->export->templateFields;
        foreach ($depts as $k2 => $dept){
            foreach ($fields as $field) {
                $data[$k2][$field] = 0;
                $data[$k2]['deptName']      = '';
                $data[$k2]['projectCode']   = [];
                $data[$k2]['projectCode']   = [];
                $data[$k2]['projectCode2']  = [];
                $data[$k2]['projectCode3']  = [];
                $data[$k2]['secondCode']    = [];
                $data[$k2]['secondCode2']   = [];
                $data[$k2]['secondCode3']   = [];
            }
        }
        $this->app->loadLang('modify');
        $this->app->loadLang('modifycncc');
        foreach ($depts as $k2 => $dept){
            foreach ($modifys as $modify) {
                if ($modify->deptId == $k2){
                    // 交付次数
                    $data[$k2]['deptName'] = $dept;
                    if (in_array($modify->id,$deliveryModifyIds)){
                        // 項目实现
                        if ($modify->implementationForm == 'project'){
                            $data[$k2]['projectNum'] += $modify->num;
                            $data[$k2]['projectCode'][] = $modify->code."【".$modify->num."】" . "【".$this->lang->modify->statusList[$modify->status]."】";
                        }
                        // 二线实现
                        if ($modify->implementationForm == 'second'){
                            $data[$k2]['secondNum'] += $modify->num;
                            $data[$k2]['secondCode'][] = $modify->code."【".$modify->num."】" . "【".$this->lang->modify->statusList[$modify->status]."】";
                        }
                    }
                    // 终态单子数
                    if (in_array($modify->id,$finalModifyIds)){
                        if (in_array($modify->status,$modifysuccess)){
                            // 項目实现
                            if ($modify->implementationForm == 'project'){
                                $data[$k2]['projectArray'] = [];
                                foreach ($finalModifys as $finalModify) {
                                    if ($finalModify->objectID == $modify->id){
                                        $data[$k2]['projectArray'][] = $modify->code;
                                    }
                                }
                                $count = count($data[$k2]['projectArray']);
                                $count = $modify->returnTime + 1;
                                $data[$k2]['projectPassNum']++;

                                $data[$k2]['projectCode2'][] = $modify->code."【".$count."】" . "【".$this->lang->modify->statusList[$modify->status]."】";
                                if ($count == 1){
                                    $data[$k2]['projectOne']++;
                                }
                                if ($count == 2){
                                    $data[$k2]['projectTwo']++;
                                }
                                if ($count >= 3){
                                    $data[$k2]['projectThree']++;
                                }
                            }
                            // 二线实现
                            if ($modify->implementationForm == 'second'){
                                $data[$k2]['secondArray'] = [];
                                foreach ($finalModifys as $finalModify) {
                                    if ($finalModify->objectID == $modify->id){
                                        $data[$k2]['secondArray'][] = $modify->code;
                                    }
                                }
                                $count = count($data[$k2]['secondArray']);
                                $count = $modify->returnTime + 1;
                                $data[$k2]['secondPassNum']++;
                                $data[$k2]['secondCode2'][] =$modify->code."【".$count."】" . "【".$this->lang->modify->statusList[$modify->status]."】";
                                if ($count == 1){
                                    $data[$k2]['secondOne']++;
                                }
                                if ($count == 2){
                                    $data[$k2]['secondTwo']++;
                                }
                                if ($count >= 3){
                                    $data[$k2]['secondThree']++;
                                }
                            }
                        }
                        if (in_array($modify->status,$modifyerror)){
                            if ($modify->implementationForm == 'project'){
                                $rejectArray = [];
                                foreach ($finalModifys as $finalModify) {
                                    if ($finalModify->objectID == $modify->id){
                                        $rejectArray[] = $modify->code;
                                    }
                                }
                                $data[$k2]['projectRejectNum'] = count(array_unique($rejectArray));
                                $data[$k2]['projectCode3'][$modify->code] = $modify->code . "【".$this->lang->modify->statusList[$modify->status]."】";
                            }
                            // 二线实现
                            if ($modify->implementationForm == 'second'){
                                $rejectArray = [];
                                foreach ($finalModifys as $finalModify) {
                                    if ($finalModify->objectID == $modify->id){
                                        $rejectArray[] = $modify->code;
                                    }
                                }
                                $data[$k2]['secondRejectNum'] = count(array_unique($rejectArray));

                                $data[$k2]['secondCode3'][] =$modify->code . "【".$this->lang->modify->statusList[$modify->status]."】";
                            }
                        }
                    }
                }
            }
            foreach ($modifycnccs as $modifycncc) {
                if ($modifycncc->deptId == $k2){
                    // 交付次数
                    $data[$k2]['deptName'] = $dept;
                    if (in_array($modifycncc->id,$deliveryModifycnccIds)){
                        // 項目实现
                        if ($modifycncc->implementationForm == 'project'){
                            $data[$k2]['projectNum'] += $modifycncc->num;
                            $data[$k2]['projectCode'][] = $modifycncc->code."【".$modifycncc->num."】" . "【".$this->lang->modifycncc->statusList[$modifycncc->status]."】";
                        }
                        // 二线实现
                        if ($modify->implementationForm == 'second'){
                            $data[$k2]['secondNum'] += $modifycncc->num;
                            $data[$k2]['secondCode'][] = $modifycncc->code."【".$modifycncc->num."】" . "【".$this->lang->modifycncc->statusList[$modifycncc->status]."】";
                        }
                    }
                    // 终态单子数
                    if (in_array($modifycncc->id,$finalModifycnccIds)){
                        if (in_array($modifycncc->status,$modifycnccsuccess)){
                            // 項目实现
                            if ($modifycncc->implementationForm == 'project'){
                                $projectArray = [];
                                foreach ($finalModifycnccs as $finalModifycncc) {
                                    if ($finalModifycncc->objectID == $modifycncc->id){
                                        $projectArray[] = $modifycncc->code;
                                    }
                                }
//                                $count = count($projectArray);
                                $count = $modifycncc->returnTimes + 1;
                                $data[$k2]['projectPassNum']++;

                                $data[$k2]['projectCode2'][] = $modifycncc->code."【".$count."】" . "【".$this->lang->modifycncc->statusList[$modifycncc->status]."】";
                                if ($count == 1){
                                    $data[$k2]['projectOne']++;
                                }
                                if ($count == 2){
                                    $data[$k2]['projectTwo']++;
                                }
                                if ($count >= 3){
                                    $data[$k2]['projectThree']++;
                                }
                            }
                            // 二线实现
                            if ($modifycncc->implementationForm == 'second'){
                                $secondArray = [];
                                foreach ($finalModifycnccs as $finalModifycncc) {
                                    if ($finalModifycncc->objectID == $modifycncc->id){
                                        $secondArray[] = $modifycncc->code;
                                    }
                                }
//                                $count = count($secondArray);
                                $count = $modifycncc->returnTimes + 1;
                                $data[$k2]['secondPassNum']++;
                                $data[$k2]['secondCode2'][] = $modifycncc->code."【".$count."】" . "【".$this->lang->modifycncc->statusList[$modifycncc->status]."】";
                                if ($count == 1){
                                    $data[$k2]['secondOne']++;
                                }
                                if ($count == 2){
                                    $data[$k2]['secondTwo']++;
                                }
                                if ($count >= 3){
                                    $data[$k2]['secondThree']++;
                                }
                            }
                        }
                        if (in_array($modifycncc->status,$modifyerror)){
                            if ($modifycncc->implementationForm == 'project'){
                                $rejectArray = [];
                                foreach ($finalModifycnccs as $finalModifycncc) {
                                    if ($finalModifycncc->objectID == $modifycncc->id){
                                        $rejectArray[] = $modifycncc->code;
                                    }
                                }
                                $data[$k2]['projectRejectNum'] += count(array_unique($rejectArray));
                                $data[$k2]['projectCode3'][$data[$k2]['projectRejectNum']->code] = $data[$k2]['projectRejectNum']->code . "【".$this->lang->modifycncc->statusList[$data[$k2]['projectRejectNum']->status]."】";
                            }
                            // 二线实现
                            if ($data[$k2]['projectRejectNum']->implementationForm == 'second'){
                                $rejectArray = [];
                                foreach ($finalModifycnccs as $finalModifycncc) {
                                    if ($finalModifycncc->objectID == $data[$k2]['projectRejectNum']->id){
                                        $rejectArray[] = $data[$k2]['projectRejectNum']->code;
                                    }
                                }
                                $data[$k2]['secondRejectNum'] += count(array_unique($rejectArray));

                                $data[$k2]['secondCode3'][] =$data[$k2]['projectRejectNum']->code . "【".$this->lang->$data[$k2]['projectRejectNum']->statusList[$modify->status]."】";
                            }
                        }
                    }
                }
            }
        }
        foreach ($data as $k=>$v) {
            $data[$k]['projectCode'] = implode(array_unique($v['projectCode']),',');
            $data[$k]['secondCode']  = implode(array_unique($v['secondCode']),',');
            $data[$k]['projectCode2'] = implode(array_unique($v['projectCode2']),',');
            $data[$k]['secondCode2']  = implode(array_unique($v['secondCode2']),',');
            $data[$k]['projectCode3'] = implode(array_unique($v['projectCode3']),',');
            $data[$k]['secondCode3']  = implode(array_unique($v['secondCode3']),',');
            $data[$k] = (object)$data[$k];
        }
        $_POST['exportFields'] = $this->config->testingrequest->export->templateFields;
        $_POST['title'] = '默认模板';
        $_POST['fileType'] = 'xlsx';
        $_POST['exportType'] = 'all';
        $_POST['template'] = '0';
        $fields = [];
        foreach($this->config->testingrequest->export->templateFields as $field) $fields[$field] = $this->lang->testingrequest->exportFileds->$field;
        $this->post->set('fields', $fields);
        $this->post->set('kind', 'testingrequest');
        $this->post->set('rows', $data);
        $this->post->set('fileType', 'xlsx');
        $this->post->set('fileName', '生产变更_'.date('Y_m_d_H_i'));
        $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
    }
    // 生产变更统计
    public function ajaxmodifystatisticsdetail($start='2024_01_01',$end='2024_01_31')
    {
        $this->app->loadLang('modify');
        $this->app->loadLang('modifycncc');
        $start = str_replace('_','-',$start).' 00:00:00';
        $end   = str_replace('_','-',$end).' 23:59:59';
        // 所属项目、部门
        $projects = $this->dao->select('t1.project,t1.id,t2.name,t1.bearDept,t3.name as deptName,t1.mark')->from(TABLE_PROJECTPLAN)->alias('t1')
            ->leftjoin(TABLE_PROJECT)->alias('t2')
            ->on('t1.project=t2.id')
            ->leftjoin(TABLE_DEPT)->alias('t3')
            ->on('t1.bearDept=t3.id')
            ->where('t1.deleted')->eq(0)
            ->fetchAll();


        list($modifys,$modifycnccs,$depts) = $this->testingrequest->getModifysAndModifycnccs($start,$end,$projects);
        // 获取投产移交单
        list($putproductions,$depts) = $this->testingrequest->getPutproduction($start,$end,$projects,$depts);
        list($credits,$depts) = $this->testingrequest->getCredit($start,$end,$projects,$depts);

        $data = array_merge($modifys,$modifycnccs,$putproductions,$credits);
        $_POST['exportFields'] = $this->config->testingrequest->export->detailFields;
        $_POST['title'] = '默认模板';
        $_POST['fileType'] = 'xlsx';
        $_POST['exportType'] = 'all';
        $_POST['template'] = '0';
        $fields = [];
        foreach($this->config->testingrequest->export->detailFields as $field) $fields[$field] = $this->lang->testingrequest->exportDetailFileds->$field;
        $this->post->set('fields', $fields);
        $this->post->set('kind', 'testingrequest');
        $this->post->set('rows', $data);
        $this->post->set('fileType', 'xlsx');
        $this->post->set('fileName', '生产变更明细_'.date('Y_m_d_H_i'));
//        a($data);
        $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
    }
    // 测试申请交付次数明细导出
    public function ajaxdeliverydetail($start = '2024_01_01',$end='2024_01_31'){
        $start = str_replace('_','-',$start).' 00:00:00';
        $end   = str_replace('_','-',$end).' 23:59:59';
        // 所属项目、部门
        $projects = $this->dao->select('t1.project,t2.name,t1.bearDept,t3.name as deptName,t1.mark')->from(TABLE_PROJECTPLAN)->alias('t1')
            ->leftjoin(TABLE_PROJECT)->alias('t2')
            ->on('t1.project=t2.id')
            ->leftjoin(TABLE_DEPT)->alias('t3')
            ->on('t1.bearDept=t3.id')
            ->where('t1.deleted')->eq(0)
            ->fetchAll();
        $tests = $this->testingrequest->getTestingrequests($start,$end,$projects);
        $_POST['exportFields'] = $this->config->testingrequest->export->detailFields;
        $_POST['title'] = '默认模板';
        $_POST['fileType'] = 'xlsx';
        $_POST['exportType'] = 'all';
        $_POST['template'] = '0';
        $fields = [];
        foreach($this->config->testingrequest->export->detailFields as $field) $fields[$field] = $this->lang->testingrequest->exportDetailFileds->$field;
        $this->post->set('fields', $fields);
        $this->post->set('kind', 'testingrequest');
        $this->post->set('rows', $tests);
        $this->post->set('fileType', 'xlsx');
        $this->post->set('fileName', 'UAT明细_'.date('Y_m_d_H_i'));
//        a($data);
        $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
    }
}