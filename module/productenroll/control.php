<?php

class productenroll extends control
{
    public function browse($browseType = 'all', $param = 0, $orderBy = 'code_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
      $this->loadModel('outwarddelivery');
      $this->view->title        = $this->lang->productenroll->browse;
      $this->view->users = $this->loadModel('user')->getPairs('noletter');
      $this->view->depts = $this->loadModel('dept')->getTopPairs();
      $this->view->dmap  = $this->dao->select('account,realname,dept')->from(TABLE_USER)->fetchAll('account');
      $projectList = $this->loadModel('projectplan')->getAllProjects();
      $apps  = $this->loadModel('application')->getapplicationNameCodePairsWithisPayment(); 
      $this->view->apps = array();
      foreach($apps as $app){
        $this->view->apps[$app->id] = $app->name;
      }

      $browseType = strtolower($browseType);

      $queryID = ($browseType == 'bysearch')  ? (int)$param : 0;
      $actionURL = $this->createLink('productenroll', 'browse', "browseType=bySearch&param=myQueryID");
      $this->productenroll->buildSearchForm($queryID, $actionURL);

      /* Load pager. */
      $this->app->loadClass('pager', $static = true);
      $pager = new pager($recTotal, $recPerPage, $pageID);
      /* 设置详情页面返回的url连接。*/
      $this->session->set('productenrollList', $this->app->getURI(true));
      $this->view->productenroll = $this->productenroll->getList($browseType,$queryID,$orderBy,$pager);
      foreach ($this->view->productenroll as $item) {
        $item->projectPlanId = zget($projectList, $item->projectPlanId);
      }
      $this->view->orderBy    = $orderBy;
      $this->view->pager      = $pager;
      $this->view->param      = $param;
      $this->view->browseType = $browseType;
      $this->display();
    }

    public function view($productenrollID)
    {
        $this->app->loadLang('release');
        $this->app->loadLang('projectrelease');
        $this->app->loadLang('outwarddelivery');
        $this->app->loadLang('application');
        $this->app->loadLang('file');
        $this->app->loadLang('api');

        $this->view->productenroll   = $this->productenroll->getByID($productenrollID);
        $this->view->title           = $this->lang->productenroll->view;

        $this->view->allLines             = $this->loadModel('productline')->getPairs();
        $this->view->allProductNames      = $this->loadModel('product')->getNamePairs();
        $this->view->allProductCodes      = $this->loadModel('product')->getCodePairs();
        $this->view->depts                = $this->loadModel('dept')->getDeptPairs();
        $this->view->users                = $this->loadModel('user')->getPairs('noletter');
        $this->view->projects             = array('' => '') + $this->loadModel('projectplan')->getProject($this->view->productenroll->implementationForm == 'second');//更新获取所属项目的方法

        $this->view->productenroll->appsInfo      = (Object)$this->loadModel('outwarddelivery')->getAppInfo(explode(',',$this->view->productenroll->app));
        $this->view->productenroll->CBPInfo       = $this->loadModel('outwarddelivery')->getCBPInfo($this->view->productenroll->CBPprojectId);
        $this->view->releaseInfoList              = $this->loadModel('outwarddelivery')->getReleaseInfoInIds($this->view->productenroll->release);

        $this->view->releasePushLogs              = $this->loadModel('release')->getPushLog($this->view->productenroll->release);
        $this->view->demand         = $this->loadModel('demand')->getPairsByIds(explode(',', $this->view->productenroll->demandId));
        $this->view->problem        = $this->loadModel('problem')->getPairsByIds(explode(',', $this->view->productenroll->problemId));
        $this->view->requirement    = $this->loadModel('requirement')->getPairsByIds(explode(',', $this->view->productenroll->requirementId));
        $this->view->secondorder         = $this->loadModel('secondorder')->getPairsByIds(explode(',', $this->view->productenroll->secondorderId));

        $this->view->outwarddeliveryPairs  = $this->loadModel('outwarddelivery')->getDetailPairs();
        $this->view->testingrequestPairs   = $this->loadModel('testingrequest')->getCodePairs();
        $this->view->productenrollPairs    = $this->productenroll->getCodePairs();
        $this->view->modifycnccPairs       = $this->loadModel('modifycncc')->getCodePairs();

        $this->view->parentId       = $this->loadModel('outwarddelivery')->getOutwardDeliveryByTypeId('productenroll',$productenrollID);
        $this->view->allRelations   = $this->loadModel('outwarddelivery')->getTypeRelations('productenroll',$productenrollID);
        $this->view->nodes      = $this->loadModel('review')->getNodes('outwardDelivery', $this->view->parentId, $this->view->productenroll->version);
        $this->view->parent     = $this->loadModel('outwarddelivery')->getByID($this->view->parentId);
        $this->view->actions    = $this->loadModel('action')->getList('productenroll', $productenrollID );

        $PElog      = $this->productenroll->getRequestLog($productenrollID);
        if(empty($PElog)){
            $PElog = new stdClass();
        }
        $this->view->PElog = $PElog;

        $this->display();
    }

    public function setNew($id)
    {
      $outwarddelivery  = $this->loadModel("outwarddelivery")->getByID($id);
      $this->productenroll->pushproductEnroll($outwarddelivery->code);
      $this->display();
    }

    public function export($orderBy = 'id_desc', $browseType = 'all')
    {
      if($_POST)
      {
        $this->loadModel('file');
        $productEnrollLang   = $this->lang->productenroll;
        $productEnrollConfig = $this->config->productenroll;

        /* Create field lists. */
        $fields = $this->post->exportFields ? $this->post->exportFields : explode(',', $productEnrollConfig->list->exportFields);
        foreach($fields as $key => $fieldName)
        {
          $fieldName = trim($fieldName);
          $fields[$fieldName] = isset($productEnrollLang->$fieldName) ? $productEnrollLang->$fieldName : $fieldName;
          unset($fields[$key]);
        }

        /* Get productenrolls. */
        $productEnrolls = array();
        if($this->session->productEnrollOnlyCondition)
        {
          $productEnrolls = $this->dao->select('*')->from(TABLE_PRODUCTENROLL)->where($this->session->productEnrollQueryCondition)
            ->beginIF($this->post->exportType == 'selected')->andWhere('id')->in($this->cookie->checkedItem)->fi()
            ->orderBy($orderBy)->fetchAll('id');
        }
        else
        {
          $stmt = $this->dbh->query($this->session->productEnrollQueryCondition . ($this->post->exportType == 'selected' ? " AND id IN({$this->cookie->checkedItem})" : '') . " ORDER BY " . strtr($orderBy, '_', ' '));
          while($row = $stmt->fetch()) $productEnrolls[$row->id] = $row;
        }


        $users = $this->loadModel('user')->getPairs('noletter');
        $depts = $this->loadModel('dept')->getTopPairs();
        $dmap  = $this->dao->select('account,realname,dept')->from(TABLE_USER)->fetchAll('account');
        $apps  = $this->loadModel('application')->getapplicationNameCodePairsWithisPayment();
        $appList          = array_column($apps, 'name','id');;
        $isPaymentList    = array_column($apps, 'isPayment','id');
        $teamList         = array_column($apps, 'team','id');
        $isPaymentPairs = $this->dao->select('`key`,value')->from(TABLE_LANG)->where('module')->eq('application')->andWhere('section')->eq('isPaymentList')->fetchPairs();
        $teamPairs = $this->dao->select('`key`,value')->from(TABLE_LANG)->where('module')->eq('application')->andWhere('section')->eq('teamList')->fetchPairs();
        $productLineList = $this->loadModel('productline')->getPairs();
        $requirementList = $this->dao->select('id,concat(concat(concat(code,"（"),name),"）")')->from(TABLE_REQUIREMENT)->where('entriesCode')->like('requirements%')->fetchPairs();
        $cbpprojectList = $this->dao->select('code,concat(concat(concat(code,"（"),name),"）")')->from(TABLE_CBPPROJECT)->fetchPairs();
        $problemList      = $this->loadModel('problem')->getPairsAbstract();
        $demandList       = $this->loadModel('demand')->getPairsTitle('noclosed');
        $projectList      = $this->loadModel('projectplan')->getAllProjects();
        $secondorderList     =  $this->loadModel('secondorder')->getNamePairs();

        date_default_timezone_set('prc');
        foreach ($productEnrolls as $productEnroll) {
          $outwardDeliveryId = $this->loadModel('outwarddelivery')->getOutwardDeliveryByTypeId('productenroll',$productEnroll->id);
          $outwardDelivery   = $this->loadModel('outwarddelivery')->getByID($outwardDeliveryId);
          $testingRequest    = $this->loadModel('testingrequest')->getByID($outwardDelivery->testingRequestId);
          $modifycncc        = $this->loadModel('modifycncc')->getByID($outwardDelivery->modifycnccId);
          $createdBy = $productEnroll->createdBy;
          $productEnroll->result                = $productEnrollLang->resultList[$productEnroll->result];
          $productEnroll->isPlan                = $productEnrollLang->isPlanList[$productEnroll->isPlan];

          $productEnroll->createdDate = substr($productEnroll->createdDate,0, 10);//创建时间
          $productEnroll->editedDate  = substr($productEnroll->editedDate,0, 10);// 编辑时间

          $productEnroll->platform              = $productEnrollLang->appList[$productEnroll->platform];
          $productEnroll->checkDepartment       = $productEnrollLang->checkDepartmentList[$productEnroll->checkDepartment];
          $productEnroll->installNode           = $productEnrollLang->installNodeList[$productEnroll->installNode];
          $productEnroll->optionSystem          = $productEnrollLang->optionSystemList[$productEnroll->optionSystem];
          $productEnroll->softwareProductPatch  = $productEnrollLang->softwareProductPatchList[$productEnroll->softwareProductPatch];
          $productEnroll->softwareCopyrightRegistration = $productEnrollLang->softwareCopyrightRegistrationList[$productEnroll->softwareCopyrightRegistration];

          $productEnroll->createdBy             = zget($users, $createdBy,'');
//          $productEnroll->status                = $productEnrollLang->statusList[$productEnroll->status];
          $productEnroll->status                =  $productEnroll->closed == '1' ? $this->lang->productenroll->labelList['closed'] :zget($this->lang->productenroll->statusList, $productEnroll->status);;
          //迭代二十六-删除部门第一个'/'
          $productEnroll->createdDepts          = ltrim($depts[$dmap[$createdBy]->dept], '/');
          $productEnroll->relatedTestingRequest = $testingRequest->code;
          $productEnroll->relatedModifycncc     = $modifycncc->code;
          $allRelations = $this->loadModel('outwarddelivery')->getTypeRelations('productEnroll',$productEnroll->id);
          $outwardDeliveryCodeList = array();
          foreach($allRelations['parents'] as $object){
            $outwardDeliveryCodeList[] = $object['code'];
          }
          $productEnroll->relatedOutwardDelivery = implode(PHP_EOL, $outwardDeliveryCodeList);
          $app = array();
          $isPayment = array();
          $team = array();
          foreach (explode(',', $productEnroll->app) as $item){
            if(!empty($item)) {
              $app[] = zget($appList, $item);
              $isPayment[] = $isPaymentPairs[zget($isPaymentList, $item, '')];
              $team[] = $teamPairs[zget($teamList, $item, '')];
            }
          }
          $productEnroll->app        = implode('，', $app);
          $productEnroll->isPayment  = implode('，', $isPayment);
          $productEnroll->team       = implode('，', $team);

          $problems = explode(',', $productEnroll->problemId);
          $problemId = '';
          foreach ($problems as $problem){
            if(!empty($problem)){
              $problemId .= zget($problemList, $problem,'').PHP_EOL;
            }
          }
          $productEnroll->problemId             = $problemId;

          $demands = explode(',', $productEnroll->demandId);
          $demandId = '';
          foreach ($demands as $demand){
            if(!empty($demand)){
              $demandId .= zget($demandList, $demand).PHP_EOL;
            }
          }

          $secondorderIds = explode(',', $productEnroll->secondorderId);
          $secondorderNameList = array();
          foreach ($secondorderIds as $secondorder){
              if(!empty($secondorder)){
                  $secondorderNameList[] = zget($secondorderList, $secondorder,'');
              }
          }
          $productEnroll->secondorderId             = implode(',',$secondorderNameList);

          $productEnroll->demandId = $demandId;
          $productEnroll->implementationForm    = $productEnrollLang->implementationFormList[$productEnroll->implementationForm];
          $productEnroll->projectPlanId = zget($projectList, $productEnroll->projectPlanId);
          $productEnroll->dealUserContact = $productEnroll->contactTel;
          $productEnroll->productLine           = zget($productLineList, $productEnroll->productLine,'');
          $productEnroll->CBPprojectId          = zget($cbpprojectList, $productEnroll->CBPprojectId,'');
          $requirements = explode(',', $productEnroll->requirementId);
          $requirementNameList = array();
          foreach ($requirements as $requirement){
            if(!empty($requirement)){
              $requirementNameList[] = zget($requirementList, $requirement);
            }
          }
          $productEnroll->requirementId              = implode(',', $requirementNameList);
          $productEnroll->installationNode           = zget($productEnrollLang->installNodeList,$productEnroll->installationNode);
          $productEnroll->planDistributionTime       = date('Y-m-d H:i:s',substr($productEnroll->planDistributionTime,0,10));
          $productEnroll->planUpTime                 = date('Y-m-d H:i:s',substr($productEnroll->planUpTime,0,10));
          $productEnroll->applyTime                  = $productEnroll->applyTime;
          $productEnroll->closedBy                   = $outwardDelivery->closedBy;
          $productEnroll->closedDate                 = $outwardDelivery->closedDate;
          $productEnroll->closedReason               = zget($productEnrollLang->closedReasonList,$outwardDelivery->closedReason);
          $mediaInfo = '';
          foreach (json_decode($productEnroll->mediaInfo,true) as $item){
            $mediaInfo .= '文件名'.':'.$item['name'].'字节数:'.$item['bytes'].';'.PHP_EOL;
          }
          $productEnroll->mediaInfo = $mediaInfo;
        }

        $this->post->set('fields', $fields);
        $this->post->set('rows', $productEnrolls);
        $this->post->set('kind', 'productEnroll');
        $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
      }

      $this->view->fileName        = $this->lang->productenroll->exportName;
      $this->view->allExportFields = $this->config->productenroll->list->exportFields;
      $this->view->customExport    = true;
      $this->display();
    }

    /**
     * 编辑退回次数
     * @param $testingrequestID
     * @return void
     */
    public function editreturntimes($outwardDeliveryId = 0){
        if($_POST)
        {
            $this->productenroll->editreturntimes($outwardDeliveryId);
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
        $this->view->title             = $this->lang->productenroll->editreturntimes;
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
            if (isset($reviewFailReason[$key][2]) && !empty($reviewFailReason[$key][2])){
                $nodes[$key]['countNodes']++;
            }
            if (isset($reviewFailReason[$key][3]) && !empty($reviewFailReason[$key][3])){
                $nodes[$key]['countNodes']++;
            }
            foreach ($node['nodes'] as $v2){
                //$this->lang->outwarddelivery->reviewNodeList :4,6,7不显示父级节点（主单历史节点）
                if (in_array($v2->stage,[4,6,7])){
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
}