<?php
class defectModel extends model
{
    /**
     * Method: getList
     * @param $browseType
     * @param $queryID
     * @param $orderBy
     * @param null $pager
     * @return mixed
     */
    public function getList($projectID, $browseType, $queryID, $orderBy, $pager = null)
    {
        $defectQuery = '';
        if($browseType == 'bysearch')
        {
            $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : '';
            if($query)
            {
                $this->session->set('defectQuery', $query->sql);
                $this->session->set('defectForm', $query->form);
            }

            if($this->session->defectQuery == false) $this->session->set('defectQuery', ' 1 = 1');
            $defectQuery = $this->session->defectQuery;


        }
        $defects = $this->dao->select('*')->from(TABLE_DEFECT)
            ->where('deleted')->ne('1')
            ->andWhere('project')->eq($projectID)
            ->beginIF($browseType != 'all' and $browseType != 'bysearch'  and $browseType != 'tomedeal')->andWhere('status')->eq($browseType)->fi()
            ->beginIF($browseType == 'tomedeal')->andWhere('dealUser')->eq($this->app->user->account)->fi()
            ->beginIF($browseType == 'bysearch')->andWhere($defectQuery)->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');
        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'defect', $browseType != 'bysearch');
        return $defects;
    }

    public function getListAll($browseType, $queryID,$orderBy = 'id_desc', $pager = null)
    {
        $defectQuery = '';
        if($browseType == 'bysearch')
        {
            $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : '';
            if($query)
            {
                $this->session->set('defectQuery', $query->sql);
                $this->session->set('defectForm', $query->form);
            }
            if($this->session->defectQuery == false) $this->session->set('defectQuery', ' 1 = 1');
            $defectQuery = $this->session->defectQuery;


        }
        $defects =  $this->dao->select('*')->from(TABLE_DEFECT)
            ->where('deleted')->ne('1')
            ->beginIF($browseType != 'all' and $browseType != 'bysearch'  and $browseType != 'tomedeal')->andWhere('status')->eq($browseType)->fi()
            ->beginIF($browseType == 'tomedeal')->andWhere('dealUser')->eq($this->app->user->account)->fi()
            ->beginIF($browseType == 'bysearch')->andWhere($defectQuery)->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');
        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'defect', $browseType != 'bysearch');
        return $defects;
    }

    /**
     * Project: chengfangjinke
     * Method: buildSearchForm
     * @param $queryID
     * @param $actionURL
     */
    public function buildSearchForm($queryID, $actionURL)
    {
        //所属系统
        $apps = array('' => '') + $this->loadmodel('application')->getapplicationNameCodePairs();
        $this->config->defect->search['params']['app']['values'] = $apps;
        //所属产品
        $products = array('' => '') + $this->loadModel('product')->getPairs();
        $this->config->defect->search['params']['product']['values'] = $products;
        //所属项目
        $projects = array('' => '') + $this->loadModel('project')->getPairs('noclosed');
        $this->config->defect->search['params']['project']['values'] = $projects;
        //优先级
        $this->config->defect->search['params']['pri']['values']    = $this->lang->bug->defectPriList;
        //缺陷分类
        $this->config->defect->search['params']['type']['values']   = $this->lang->bug->typeList;
        //缺陷子类
        $childTypeList = $this->loadModel('bug')->getChildTypeTileList();
        $this->config->defect->search['params']['childType']['values']   = array('' => '') + $childTypeList;
        //严重程度
        $this->config->defect->search['params']['severity']['values']   = $this->lang->bug->defectSeverityList;
        //出现频次
        $this->config->defect->search['params']['frequency']['values']   = $this->lang->bug->defectFrequencyList;
        //解决方案
        $this->config->defect->search['params']['resolution']['values']  = $this->lang->bug->resolutionList;

        $this->config->defect->search['params']['dept']['values']= array('' => '') + $this->loadModel('dept')->getOptionMenu();
        //关联测试申请testrequestCode

        //关联产品登记testrequestCode
        $this->config->defect->search['actionURL'] = $actionURL;
        $this->config->defect->search['queryID']   = $queryID;
        $this->loadModel('search')->setSearchParams($this->config->defect->search);


    }
    /**
     * Method: create
     * @return mixed
     */
    public function create()
    {

    }

    /**
     * Method: update
     * Product: PhpStorm
     * @param $defectID
     * @return array
     */
    public function update($defectID)
    {
        $olddefect = $this->getByID($defectID);
        $defect = fixer::input('post')
            ->stripTags($this->config->defect->editor->edit['id'], $this->config->allowedTags)
            ->remove('uid')
            ->get();
        if(empty($_POST['product']) and $_POST['product'] != '0')
        {
            return dao::$errors['product'] = sprintf($this->lang->defect->emptyObject, $this->lang->defect->product);
        }
//        $defect->editedBy    = $this->app->user->account;
//        $defect->editedDate  = date('Y-m-d H:i:s');

        $defect = $this->loadModel('file')->processImgURL($defect, $this->config->defect->editor->edit['id'], $this->post->uid);

        $this->dao->update(TABLE_DEFECT)->data($defect)
            ->batchCheck($this->config->defect->edit->requiredFields, 'notempty')
            ->where('id')->eq($defectID)
            ->exec();
//        if($this->getLastAction($defectID) != 'edited' && !dao::isError()) {
//            $this->loadModel('consumed')->record('defect', $defectID, 0, $this->app->user->account, $olddefect->status, 'assigned', array());
//        }

//        $this->loadModel('file')->updateObjectID($this->post->uid, $defectID, 'defect');
//        $this->file->saveUpload('defect', $defectID);

        return common::createChanges($olddefect, $defect);
    }

    public function rePush($defectId)
    {
        if(!$_POST['comment'])
        {
            return dao::$errors['comment'] = sprintf($this->lang->defect->emptyObject, $this->lang->defect->comment);
        }
        $this->loadModel('action')->create('defect', $defectId, 'repush', $this->post->comment);

        $this->getUnPushedAndPush(array($defectId));
        return;
    }
    /**
     * 查看待推送缺陷单并推送
     */
    public function getUnPushedAndPush($defectIdList)
    {
        $this->loadModel('requestlog');
        //取测缺陷单
        $unPushedDefects = $this->dao->select('*')->from(TABLE_DEFECT)->where('syncStatus')->in(array(0, -1))->andwhere('id')->in($defectIdList)->fetchAll();
        $res = [];
        foreach ($unPushedDefects as $defect) {
            $pushEnable = $this->config->global->pushDefectEnable;
            //判断请求配置是否可用
            if ($pushEnable == 'enable') {
                $url = $defect->source == 1 ? $this->config->global->defectFeedbackUrl : $this->config->global->defectReFeedbackUrl;
                $pushAppId = $this->config->global->pushDefectAppId;
                $pushAppSecret = $this->config->global->pushDefectAppSecret;


                $headers = array();
                $headers[] = 'App-Id:' . $pushAppId;
                $headers[] = 'App-Secret:' . $pushAppSecret;


                $users = $this->loadmodel('user')->getPairs('noletter');
                $depts = $this->loadModel('dept')->getTopPairs();
                $apps           = $this->loadModel('application')->getapplicationCodePairs();
                $projects      = $this->loadModel('project')->getProjects();
                $products  = $this->loadModel('product')->getSimplePairs();


                $this->app->loadLang('bug');
                $this->app->loadLang('testingrequest');
                $childDefectTypeList = $this->loadModel('bug')->getChildTypeTileList();
                $extDefectType = $this->lang->bug->typeList[$defect->type] == '功能缺陷' ? $this->lang->bug->typeList[$defect->type] . $childDefectTypeList[$defect->childType] : $this->lang->bug->typeList[$defect->type];
                $pushData = array();
                $pushData['testDefectsId'] = $defect->uatId;
                $pushData['shejichanpin'] = $defect->linkProduct;
                $pushData['TheDisposalOfAdvice'] = zget($this->lang->defect->dealSuggestList, $defect->dealSuggest);
                $pushData['TheDisposalInstructions'] = $defect->dealComment;
                $pushData['PlannedDateOfChange'] = $defect->changeDate != '0000-00-00 00:00:00' ? intval(strtotime($defect->changeDate) . '000') : '';
                $pushData['PlannedDateOfChangeReport'] = $defect->submitChangeDate != '0000-00-00 00:00:00' ? intval(strtotime($defect->submitChangeDate) . '000') : '';
                $pushData['EditorImpactscope'] = $defect->EditorImpactscope;
                $pushData['ishistoryproblem'] = zget($this->lang->defect->ifList, $defect->ifHisIssue);
                $pushData['history_project_name'] = $defect->project; // 历史遗留问题项目
                $pushData['isCentralizedTest'] = $defect->ifTest;

                if ($defect->source == '1') {
                    $pushData['sampleVersionNumber'] = $defect->sampleVersionNumber;
                    $pushData['testCase'] = $defect->testCase;
                    $pushData['testEngineer'] = zget($users, $defect->tester);
                    $pushData['BindWorkspace_PROJECT'] = zget($projects, $defect->project); // 项目orＣＢＰ？
                    $pushData['BindWorkspace_product'] = zget($products, $defect->product);;
                    $pushData['reporterId'] = zget($users, $defect->reportUser);
                    $pushData['reportTime'] = $defect->reportDate != '0000-00-00 00:00:00' ? intval(strtotime($defect->reportDate) . '000') : '';
                    $pushData['Questionpriority'] = $this->lang->bug->defectPriList[$defect->pri];
                    $pushData['TestType'] = zget($this->lang->testingrequest->acceptanceTestTypeList, $defect->testType);
                    $pushData['projectManagers'] = zget($users, $defect->projectManager);
                    $pushData['question_identification'] = html_entity_decode(str_replace("\n","<br/>",$defect->issues));
                    $pushData['Dropdown_roud'] = $defect->rounds;
                    $pushData['Dropdown_suspensionreason'] = $defect->Dropdown_suspensionreason;
                    $pushData['Editor_impactscope_16'] = $defect->testAdvice;
                    $pushData['Dropdown_testenvironment'] = zget($this->lang->defect->testEnvironmentList, $defect->testEnvironment);
                    $pushData['Dropdown_defecttype'] = $this->lang->defect->ToExtDefectType[$extDefectType];
                    $pushData['QuestionSeriousness'] = substr(zget($this->lang->bug->defectSeverityList, $defect->severity), 3);
                    $pushData['Dropdown_occurrenfrequency'] = zget($this->lang->bug->defectFrequencyList, $defect->frequency);
                    $pushData['Department'] = zget($depts, $defect->dept);
                    $pushData['User_kafiarenyuan'] = zget($users, $defect->developer);
                    $pushData['OptionSystem'] = $apps[$defect->app ?? $defect->realtedApp];
                    $pushData['DefectVerificationResult'] = zget($this->lang->defect->verificationList, $defect->verification);
                    $pushData['jinkeProcessingProgess'] = $defect->progress;
                    $pushData['jinkeProcessingStatus'] = 1;

            }
                if ($defect->testrequestId) {
                    $tdata = $this->dao->select('giteeId')->from(TABLE_TESTINGREQUEST)
                        ->where('id')->eq($defect->testrequestId)
                        ->andwhere('deleted')->eq(0)
                        ->fetch();;
                    $pushData['relatedTestApplication'] = $tdata->giteeId;
                } else {
                    $pdata = $this->dao->select('giteeId')->from(TABLE_PRODUCTENROLL)
                        ->where('id')->eq($defect->productenrollId)
                        ->andwhere('deleted')->eq(0)
                        ->fetch();;
                    $pushData['relatedProductRegistrations'] = $pdata->giteeId;
                }

                $object = 'defect';
                $objectType = $defect->source == '1' ? 'pushdefect' : 'refeedbackdefect';
                $response = '';
                $status = 'fail';
                $extra = '';

                $result = $this->loadModel('requestlog')->http($url, $pushData, 'POST', 'json', array(), $headers);

                if (!empty($result)) {
                    $resultData = json_decode($result);
                    if ($resultData->isSave == 1) {
                        $now = date('Y-m-d H:i:s');
                        $status = 'success';
                        $this->dao->update(TABLE_DEFECT)
                            ->set('syncStatus')->eq(1)
                            ->where('id')->eq($defect->id)->exec();
                        $this->loadModel('action')->create('defect', $defect->id, 'syncsuccess', $resultData->message);
                    } else {
                        $this->dao->update(TABLE_DEFECT)
                            ->set('syncStatus')->eq(-1)
                            ->where('id')->eq($defect->id)->exec();
                        $this->loadModel('action')->create('defect', $defect->id, 'syncfail', $resultData->message);
                    }

                    $response = $result;
                } else {
                    $this->dao->update(TABLE_DEFECT)
                        ->set('syncStatus')->eq(-1)
                        ->where('id')->eq($defect->id)->exec();
                    $this->loadModel('action')->create('defect', $defect->id, 'syncfail', "网络不通");
                }

                $this->requestlog->saveRequestLog($url, $object, $objectType, 'POST', $pushData, $response, $status, $extra);
            }

        }
    }

    /**
     * @return mixed
     * 接口同步反馈数据
     */
    public function updateByApi()
    {
        $data = fixer::input('post')->get();
        $this->app->loadLang('bug');
        $this->app->loadLang('testingrequest');
        $this->app->loadLang('build');
        $defect = $this->dao->select('id,status')->from(TABLE_DEFECT)->where('uatId')->eq($data->uatId)->fetch();
        $data->pri = array_search($data->pri, $this->lang->bug->defectPriList);
//        $data->rounds = array_search($data->rounds, $this->lang->build->roundsList);
        $data->testEnvironment = array_search($data->testEnvironment, $this->lang->defect->testEnvironmentList);
        $data->severity = array_search($data->severity, $this->lang->defect->defectSeverityList);
        $data->frequency = array_search($data->frequency, $this->lang->bug->defectFrequencyList);
        $apps           = $this->loadModel('application')->getapplicationCodePairs();
        $data->app = array_search($data->app, $apps);
        $products = $this->dao->select('id,code')->from(TABLE_PRODUCT)->where('deleted')->eq('0')->fetchPairs();
        $data->product = array_search($data->product, $products);
        if($data->product == '') return dao::$errors['BindWorkspace_product'] = 'BindWorkspace_product字段无法匹配';
        $data->verification = array_search($data->verification, $this->lang->defect->verificationList);

        //处理缺陷类型 子类型
        $extChildType = $this->lang->defect->extToInChildDefectType[$data->type];
        $defectTypeList = $this->loadModel('bug')->getChildTypeTileList();
        $data->childType = array_search($extChildType, $defectTypeList);

        //一级分类
        $data->type = array_search($this->lang->defect->extToInDefectType[$data->type], $this->lang->bug->typeList);
        $data->reportDate = date('Y-m-d H:i:s', substr($data->reportDate, 0, 10));

        $tdata = array();
        if(!empty($data->testrequestGiteeId)) {
            $tdata = $this->dao->select('id,projectPlanId,createdBy,isCentralizedTest,acceptanceTestType,CBPprojectId,code')->from(TABLE_TESTINGREQUEST)
                ->where('giteeId')->eq($data->testrequestGiteeId)
                ->andWhere('deleted')->eq(0)
                ->fetch();
        }else {
            $pdata = $this->dao->select('id,projectPlanId,createdBy,CBPprojectId,code')->from(TABLE_PRODUCTENROLL)
                ->where('giteeId')->eq($data->productenrollGiteeId)
                ->andwhere('deleted')->eq(0)
                ->fetch();
        }
        if(isset($tdata->id))
        {
            $data->testrequestId = $tdata->id;
            $data->ifTest = $tdata->isCentralizedTest == '1' ? '1' : '0';
            $data->testType = $tdata->acceptanceTestType ?? '';
            $data->project = $tdata->projectPlanId;
            $data->testrequestCode = $tdata->code;
            $data->CBPproject = $tdata->CBPprojectId;
            $data->testrequestCreatedBy = $tdata->createdBy;
            $data->dealUser = $tdata->createdBy;
            $data->outwarddeliveryId = $this->dao->select('id')->from(TABLE_OUTWARDDELIVERY)->where('testingRequestId')->eq($tdata->id)->fetch('id');
        }
        if(isset($pdata->id))
        {
            $data->productenrollId = $pdata->id;
            $data->project = $data->project ?? $pdata->projectPlanId;
            $data->ifTest = $data->ifTest ?? '0';
            $data->CBPproject = $pdata->CBPprojectId;
            $data->productenrollCode = $pdata->code;
            $data->productenrollCreatedBy = $pdata->createdBy;
            $data->dealUser = $data->dealUser ?? $pdata->createdBy;
            $data->outwarddeliveryId = $this->dao->select('id')->from(TABLE_OUTWARDDELIVERY)->where('productenrollId')->eq($pdata->id)->fetch('id');

        }

        $data->status = 'toconfirm';
        $data->source = 2;
        $projectInfo = $this->dao->select('t1.id,t2.dept,t2.PM')
            ->from(TABLE_PROJECTPLAN)->alias('t1')
            ->leftJoin(TABLE_PROJECTCREATION)->alias('t2')->on('t2.plan = t1.id')
            ->where('t1.project')->eq($data->project)
            ->fetch();
        $data->dept = $projectInfo->dept;
        $data->projectManager = $projectInfo->PM;

        if(!empty($defect->id)) {
            $data->syncStatus = '';
            $this->dao->update(TABLE_DEFECT)->data($data)
                ->where('id')->eq($defect->id)
                ->exec();
            $this->loadModel('action')->create('defect', $defect->id, 'edited', '同步','','guestcn');
            $this->loadModel('consumed')->record('defect', $defect->id, 0, 'guestcn', $defect->status, 'toconfirm', array());

        }else {
            $number = $this->dao->select('count(id) c')->from(TABLE_DEFECT)->where('createdDate')->gt(date('Ymd') )->fetch('c');
            $data->code   = 'CFIT-UB-' . date('Ymd-') . sprintf('%03d', $number + 1);
            $data->createdBy = 'guestcn';
            $data->createdDate = helper::now();
            $this->dao->insert(TABLE_DEFECT)->data($data)->exec();
            $id = $this->dao->lastInsertID();
            $this->loadModel('action')->create('defect', $id, 'created', '同步','','guestcn');
            $this->loadModel('consumed')->record('defect', $id, 0, 'guestcn', '', 'toconfirm', array());

        }

    }


    public function getDefectByFixType($fixType)
    {
        return $this->dao->select('id,concat(id, title)')->from(TABLE_DEFECT)->where('dealSuggest')->in($fixType)->fetchAll();
    }

    /**
     * Project: chengfangjinke
     * Method: getConsumedList
     * @param $defectID
     * @return mixed
     */
    public function getConsumedList($defectID)
    {
        $cs = $this->dao->select('*')->from(TABLE_CONSUMED)->where('objectType')->eq('defect')
            ->andWhere('objectID')->eq($defectID)
            ->andWhere('parentID')->eq('0')
            ->andWhere('deleted')->eq(0)
            ->orderBy('id_asc')
            ->fetchAll();
        return $cs;
    }

    /**
     * Project: chengfangjinke
     * Method: getByID
     * @param $defectID
     * @return mixed
     */
    public function getByID($defectID)
    {
        $defect = $this->dao->select("*")->from(TABLE_DEFECT)->where('id')->eq($defectID)->fetch();

        $defect->files = $this->loadModel('file')->getByObject('defect', $defect->id);
        return $defect;
    }

    public function getByIdList($defectIdList, $isPairs = false)
    {
        if(empty($defectIdList)) return array();

        $defects = $this->dao->select("*")->from(TABLE_DEFECT)->where('id')->in($defectIdList)->fetchAll();
        if($isPairs)
        {
            $pairs = array();
            foreach($defects as $defect)
            {
                $pairs[$defect->id] = $defect->code;
            }
            $defects = $pairs;
        }
        return $defects;
    }

    /* 获取制版次数。*/
    public function getBuild($defectID)
    {
        $buildTotal = $this->dao->select('count(*) as total')->from(TABLE_CONSUMED)
            ->where('objectType')->eq('defect')
            ->andWhere('objectID')->eq($defectID)
            ->andWhere('after')->eq('build')
            ->fetch('total');
        return empty($buildTotal) ? 0 : $buildTotal;
    }

    /**
     * Project: chengfangjinke
     * Method: deal
     * @param $defectID
     * @return array
     */
    public function deal($defectID)
    {

        $olddefect = $this->getByID($defectID);

        $data = fixer::input('post')->stripTags($this->config->defect->editor->deal['id'], $this->config->allowedTags)
            ->remove('uid,files,comment,isSave,consumed')
            ->join('cc', ',')
            ->get();

//        $consumed = $this->post->consumed;
//        $checkRes = $this->loadModel('consumed')->checkConsumedInfo($consumed);
//        if (!$checkRes) {
//            return false;
//        }

        if($this->post->isSave != '1') {
            $data->status =  'tofeedback';
            $data->dealUser = 'guestcn';
        }else {
            $data->status =  'tosolve';
        }
        $data->ifHisIssue = $data->ifHisIssue == '1' ? '1' : '0';
        $data->dealedBy = $this->app->user->account;
        $data->dealedDate = helper::now();
        if($olddefect->status == 'nextfix')
        {
            unset($data->ifHisIssue);
            $data->dealSuggest = 'fix';
            $data->syncStatus = 0;
        }
        if($this->post->dealSuggest == 'nextFix') $this->config->defect->deal->requiredFields .= ',changeDate,submitChangeDate';
        $data = $this->loadModel('file')->processImgURL($data, $this->config->defect->editor->deal['id'], $this->post->uid);
        $this->dao->update(TABLE_DEFECT)->data($data)->autoCheck()
             ->batchCheck($this->config->defect->deal->requiredFields, 'notempty')
             ->where('id')->eq($defectID)
             ->exec();
        $actionID = $this->loadModel('action')->create('defect', $defectID, 'deal', $this->post->comment);
        $changes = common::createChanges($olddefect, $data);
        if($changes) $this->action->logHistory($actionID, $changes);
        $this->loadModel('consumed')->record('defect', $defectID, 0, $this->app->user->account, $olddefect->status, $data->status);
        if((!empty($olddefect->changeStatus) or $olddefect->source == '2') and $this->post->isSave != '1') $this->getUnPushedAndPush($defectID);
        $this->post->project = $olddefect->project;
        return $changes;
    }

    /**
     * Project: chengfangjinke
     * Method: change
     * @param $defectID
     * @return array
     */
    public function change($defectID)
    {

        $olddefect = $this->getByID($defectID);

        $data = fixer::input('post')->stripTags($this->config->defect->editor->change['id'], $this->config->allowedTags)
            ->remove('uid,comment,isSave')
            ->get();

//        $consumed = $this->post->consumed;
//        $checkRes = $this->loadModel('consumed')->checkConsumedInfo($consumed);
//        if (!$checkRes) {
//            return false;
//        }

        $data->status =  'tofeedback';
        $data->dealUser = 'guestcn';

        $data->ifHisIssue = $data->ifHisIssue ?? '0';
        $data->syncStatus = 0;
        if($this->post->dealSuggest == 'nextFix') $this->config->defect->change->requiredFields .= ',changeDate,submitChangeDate';
        $data = $this->loadModel('file')->processImgURL($data, $this->config->defect->editor->deal['id'], $this->post->uid);
        $this->dao->update(TABLE_DEFECT)->data($data)->autoCheck()
             ->batchCheck($this->config->defect->change->requiredFields, 'notempty')
             ->where('id')->eq($defectID)
             ->exec();
        $actionID = $this->loadModel('action')->create('defect', $defectID, 'applychange', $this->post->comment);
        $changes = common::createChanges($olddefect, $data);
        if($changes) $this->action->logHistory($actionID, $changes);

        $this->loadModel('consumed')->record('defect', $defectID, 0, $this->app->user->account, $olddefect->status, $data->status);
        $this->getUnPushedAndPush($defectID);
        $this->post->project = $olddefect->project;

        return $changes;
    }

    /**
     * Project: chengfangjinke
     * Method: confirm
     * @param $defectID
     */
    public function confirm($defectID)
    {
//        $consumed = $this->post->consumed;
//        $checkRes = $this->loadModel('consumed')->checkConsumedInfo($consumed);
//        if (!$checkRes) {
//            return false;
//        }
        $olddefect = $this->getByID($defectID);
        $data = new stdClass();
        $data->status   = 'tosolve';
        $data->dealUser = $this->post->dealUser;
        $data->cc       = $this->post->cc;
        $data->confirmedBy       = $this->app->user->account;
        $data->confirmedDate       = helper::now();
        $data = $this->loadModel('file')->processImgURL($data, $this->config->defect->editor->confirm['id'], $this->post->uid);
        $this->dao->update(TABLE_DEFECT)->data($data)->autoCheck()
            ->batchCheck($this->config->defect->confirm->requiredFields, 'notempty')
            ->where('id')->eq($defectID)->exec();
        $this->loadModel('consumed')->record('defect', $defectID, 0, $this->app->user->account, $olddefect->status, 'tosolve', array());
    }

    /**
     * Project: chengfangjinke
     * Method: isClickable
     * Product: PhpStorm
     * @param $defect
     * @param $action
     * @return bool
     */
    public static function isClickable($defect, $action)
    {
        global $app;
        $action = strtolower($action);
        if($action == 'edit')            return ($defect->status == 'tosolve' or $defect->status == 'nextfix' or $defect->status == 'hitback') and ($defect->source == 1);
        if($action == 'confirm')           return ($defect->status == 'toconfirm') and ($app->user->account == $defect->dealUser or  $app->user->account == 'admin');;
        if($action == 'deal')            return ($defect->status == 'tosolve' or $defect->status == 'nextfix' or $defect->status == 'hitback') and ($app->user->account == $defect->dealUser or  $app->user->account == 'admin');
        return true;
    }

    /**
     * Project: chengfangjinke
     * Method: getPairs
     * @return mixed
     */
    public function getPairs($code = '')
    {
        return $this->dao->select('id,code')->from(TABLE_DEFECT)
            ->where('deleted')->ne('1')
            ->andwhere('code')->in($code)
            ->orderBy('id_desc')
            ->fetchAll();
    }

    /**
     * 根据多个id获取信息
     * @param array $ids
     * @return stdClass
     */
    public function getPairsByIds($ids)
    {
        if(empty($ids)) return null;
        $info = $this->dao->select('id,code,`desc`')->from(TABLE_DEFECT)
            ->where('status')->ne('deleted')
            ->andwhere('id')->in($ids)
            ->orderBy('id_desc')
            ->fetchall();
        $defects = new stdClass();
        foreach ($info as $item)
        {
            $id = $item->id;
            $defects->$id = ['code'=>$item->code, 'desc' =>$item->desc];
        }
        return  $defects;
    }

    /**
     * Send mail.
     *
     * @param  int    $defectID
     * @param  int    $actionID
     * @access public
     * @return void
     */
    public function sendmail($defectID, $actionID)
    {
        if($this->post->isSave == '1') return;
        /* Get action info. */
        $action          = $this->loadModel('action')->getById($actionID);
        $history         = $this->action->getHistory($actionID);
        $action->history = isset($history[$actionID]) ? $history[$actionID] : array();
        $this->loadModel('mail');
        $defect = $this->getById($defectID);
        $users   = $this->loadModel('user')->getPairs('noletter');

        if(isset($_POST['syncfail']) and $_POST['syncfail'] == 1 and $defect->status == 'tofeedback') return;
        if($action->action == 'syncfail' or $defect->status == 'solved' or $defect->status == 'nextfix' or $defect->status == 'hitback')
        {
            $mailType = 'setDefectnoticeMail';
            $repStatus = ($defect->status == 'solved' or $defect->status == 'nextfix' or $defect->status == 'hitback') ? $defect->status : 'syncfail';
        }else {
            $mailType = 'setDefectMail';
            $repStatus = $defect->status;
        }


        /* 获取后台通知中配置的邮件发信。*/
        $this->app->loadLang('custommail');
        $mailConf   = isset($this->config->global->$mailType) ? $this->config->global->$mailType : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf   = json_decode($mailConf);
        $browseType = 'defect';

        /* 处理邮件发信的标题和日期。*/
        $bestDeal = $this->dao->select('*')->from(TABLE_CONSUMED)->where('objectType')->eq('defect')
            ->andWhere('objectID')->eq($defectID)
            ->orderBy('id_desc')
            ->fetch();
        $bestDate  = empty($bestDeal) ? '' : substr($bestDeal->createdDate, 0, 10);
        $mailTitle = vsprintf($mailConf->mailTitle, zget($this->lang->defect->statusList, $repStatus));

        /* Get mail content. */
        $oldcwd     = getcwd();
        $modulePath = $this->app->getModulePath($appName = '', 'defect');
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

        $toList = $defect->dealUser;
        if(!$toList && $action->action == 'feedbacked') {
            $toList = $defect->dealedBy;
        }
        $ccList = $this->post->cc ?? '';

        // 完成状态处理
        $tome = false;

        if($action->action == 'syncfail')
        {
            $_POST['syncfail'] = 1;
            $toList = !empty($defect->testrequestCreatedBy) ? $defect->testrequestCreatedBy : $defect->productenrollCreatedBy;
            $tome = true;
        }
        /* 处理邮件标题。*/
        $subject = $mailTitle;
        /* Send emails. */
        $this->mail->send($toList, $subject, $mailContent, $ccList, $tome);
        if($this->mail->isError()) error_log(join("\n", $this->mail->getError()));
    }

    /**
     * Project: chengfangjinke
     * Method: getToAndCcList
     * Product: PhpStorm
     * @param $object
     * @return array
     */
    public function getToAndCcList($object)
    {
        /* Set toList and ccList. */
        $toList = $this->dao->select('actor')->from(TABLE_ACTION)->where('objectType')->eq('defect')->andWhere('objectID')->eq($object->id)->andWhere('action')->eq('deal')->fetchAll();
        $toArr = Array();
        Array_push($toArr, $this->app->user->account);
        foreach ($toList as $to)
        {
            if(!in_array($to->actor, $toArr))
            {
                Array_push($toArr, $to->actor);
            }
        }

        $details = $this->loadModel('dept')->getByID($object->acceptDept);
        $ccList  = trim($details->manager, ',');

        return array(implode(',',$toArr), $ccList);
    }

    /**
     * 根据拼音首字母取系统id
     * @param $code
     * @return mixed
     */
    public function getAppIdByAppCode($codes)
    {
        $apps = $this->dao->select('id')->from(TABLE_APPLICATION)->where('code')->in($codes)->fetchAll('id');
        if(empty($apps)) return '';
        return implode(',', array_keys($apps));
    }

    function checkTimeFormat($date)
    {
        if(date('Y-m-d H:i:s', strtotime($date)) == $date ){
            return true;
        }
        if(date('Y-m-d H:i', strtotime($date)) == $date ){
            return true;
        }
        return false;
    }

    function checkDateFormat($date)
    {
        if(date('Y-m-d', strtotime($date)) == $date ){
            return true;
        }
        return false;
    }

    // 获取分类下的子类数据。
    public function getChildTypeList($assignType = '')
    {
        // 自定义的defect子类数据。
        $typeList      = $this->lang->defect->typeList;
        $childTypeList = isset($this->lang->defect->childTypeList) ? $this->lang->defect->childTypeList['all'] : '[]';
        $childTypeList = json_decode($childTypeList, true);
        $customList    = empty($childTypeList[$assignType]) ? array('0' => '') : $childTypeList[$assignType];
        if(!empty($customList)) $customList = array('0' => '') + $customList;
        return $customList;
    }

    // 获取分类下的子类数据键值对。
    public function getChildTypeTileList($firstNull = false)
    {
        // 自定义的defect子类数据。
        $typeList      = $this->lang->bug->typeList;
        $childTypeList = isset($this->lang->defect->childTypeList) ? $this->lang->defect->childTypeList['all'] : '[]';
        $childTypeList = json_decode($childTypeList, true);

        $customList = array();
        if($firstNull) $customList[0] = '';
        foreach($childTypeList as $type => $items)
        {
            // defect分类不存在的话，子类也不展示了。
            if(empty($typeList[$type])) continue;

            foreach($items as $key => $value)
            {
                $customList[$key] = $value;
            }
        }

        return $customList;
    }

    // 获取分类下的子类数据(父分类)。
    public function getChildTypeParentList()
    {
        // 自定义的defect子类数据。
        $typeList      = $this->lang->defect->typeList;
        $childTypeList = isset($this->lang->defect->childTypeList) ? $this->lang->defect->childTypeList['all'] : '[]';
        $childTypeList = json_decode($childTypeList, true);
        return $childTypeList;
    }

    public function getChildTypeParentNameList()
    {
        $childTypeList = $this->getChildTypeTileList(true);
        $allTypeList   = $this->getChildTypeParentList();
        foreach($childTypeList as $index => $name)
        {
            if(empty($index))
            {
                $childTypeList[$index] = $name;
                continue;
            }

            foreach($allTypeList as $typeKey => $typeData)
            {
                $typeName = $this->lang->defect->typeList[$typeKey];
                foreach($typeData as $childIndex => $childValue)
                {
                    if($index == $childIndex) $childTypeList[$index] = $typeName . '-' . $name;
                }
            }
        }
        return $childTypeList;
    }

    // 获取分类下的子类数据(父分类)。
    public function getAllChildTypeList()
    {
        // 自定义的defect子类数据。
        $typeList      = $this->lang->defect->typeList;
        $childTypeList = isset($this->lang->defect->childTypeList) ? $this->lang->defect->childTypeList['all'] : '[]';
        $childTypeList = json_decode($childTypeList, true);

        $data = array();
        foreach($typeList as $key => $type)
        {
            if(empty($key)) continue;
            if(empty($childTypeList[$key]))
            {
                $data[$key]['name']  = $type;
                $data[$key]['child'] = array();
            }
            else
            {
                $data[$key]['name']  = $type;
                $data[$key]['child'] = $childTypeList[$key];
            }
        }
        return $data;
    }

    /**
     * @param $account
     * 获取人员部门
     */
    public function getDeptByUser($account)
    {
        $dept = $this->dao->select('dept')->from(TABLE_USER)->where('account')->eq($account)->fetch('dept');

        return $dept;
    }

    /**
     * @param $id
     * 获得最后的action记录
     */
    public function getLastAction($id)
    {
        $lastAction = $this->dao->select('action')
                        ->from(TABLE_ACTION)
                        ->where('objectType')->eq('defect')
                        ->andWhere('objectID')->eq($id)
                        ->orderBy('action_desc')
                        ->fetch('action');
        return $lastAction;
    }

    /**
     * @param $objectID
     * @return mixed
     */
    public function getConsumedsByID($objectID)
    {
        $details = $this->dao->select('*')->from(TABLE_CONSUMED)
            ->where('objectID')->eq($objectID)
            ->andWhere('objectType')->eq('defect')
            ->andWhere('deleted')->eq(0)
            ->fetchAll();

        return $details;
    }

    /**
     * @param $defectID
     * @param $consumedID
     * @return array
     * 编辑流程状态
     */
    public function statusedit($defectID, $consumedID)
    {

        $res = array();

        $consumed = fixer::input('post')->remove('comment, relevantUser, workload, dealUser')->get();
        $isLast = $this->loadModel('consumed')->checkIsLastConsumed($consumedID, $defectID, 'defect');
        if($isLast){
            //最后一个节点时没有设置处理人
            $dealUser = $this->post->dealUser;
//            if(!$dealUser){
//                $errors['dealUser'] = sprintf($this->lang->defect->emptyObject, $this->lang->defect->dealUser);
//                return dao::$errors = $errors;
//            }
        }

        $this->dao->update(TABLE_CONSUMED)->data($consumed)->where('id')->eq($consumedID)->exec();

        $olddefect = $this->getByID($defectID);
        if(($olddefect->status != $consumed->after) || ($olddefect->dealUser != $dealUser)) {
            $this->dao->update(TABLE_DEFECT)->set('status')->eq($consumed->after)->set('dealUser')->eq($dealUser)->where('id')->eq($defectID)->exec();
            $data = new stdClass();
            $data->status   = $consumed->after;
            $data->dealUser = $dealUser;
            $res = common::createChanges($olddefect, $data);
        }

        return $res;
    }

    /**
     * @param $defectID
     * @return mixed
     * 获取历次当前进展
     */
    public function getProgress($defectID)
    {
        $progress =  $this->dao->select('actor,date,comment')->from(TABLE_ACTION)->where('objectType')->eq('defectProgress')->andWhere('objectID')->eq($defectID)->fetchAll();
        return $progress;
    }


    /**
     * 获取项目
     * @param $deptID
     * @return mixed
     */
    public function getProjectPlanInfo($deptID)
    {
        $projectPlanId = $this->dao->select('project')->from(TABLE_PROJECTPLAN)->where('secondLine')->eq('1')->andWhere('year')->eq(date('Y'))->andWhere('bearDept')->eq($deptID)->fetch('project');
        return $projectPlanId;
    }

    /**
     * Print cell data
     *
     * @param object $col
     * @param object $defect
     * @param array  $users
     * @param string $mode
     * @param array  $projects
     * @param array  $products
     * @access public
     * @return void
     */
    public function printCellDefect($col, $defect, $users, $mode = 'datatable', $projects = [], $products = [])
    {
        $this->app->loadLang('bug');
        $id         = $col->id;
        if($col->show)
        {
            $class = 'c-' . $id;
            $title = '';
            if($id == 'title')
            {
                $class .= ' text-left';
                $title = "title='{$defect->title}'";
            }

            if($id == 'actions')
            {
                $class .= ' c-actions';
            }

            echo "<td class='{$class}' {$title}>";

            switch($id)
            {
                case 'id':
                    echo common::hasPriv('defect', 'view') ? html::a(helper::createLink('defect', 'view', "defectID=$defect->id"), $defect->code, '', "") : $defect->code;
                    break;
                case 'title':
                    echo $defect->title;
                    break;
                case 'uatId':
                    echo $defect->uatId;
                    break;
                case 'pri':
                    echo "<span class='label-pri label-pri-" . $defect->pri . "' title='" . zget($this->lang->bug->defectPriList, $defect->pri, $defect->pri) . "'>";
                    echo zget($this->lang->bug->defectPriList, $defect->pri, $defect->pri);
                    echo "</span>";
                    break;
                case 'product':
                    $product = $defect->product;
                    if(!$product)
                    {
                        $product = 'na';
                    }
                    echo zget($products, $product, '');
                    break;
                case 'project':
                    echo zget($projects, $defect->project, '');
                    break;

                case 'severity':
                    echo zget(['0' => ''] + $this->lang->bug->defectSeverityList, $defect->severity);
                    break;
                case 'source':
                    echo zget($this->lang->defect->sourceList, $defect->source);
                    break;
                case 'createdDate':
                    echo $defect->createdDate != '0000-00-00 00:00:00' ? $defect->createdDate : '';
                    break;
                case 'status':
                    echo zget($this->lang->defect->statusList, $defect->status);
                    break;
                case 'nextUser':
                    echo zget($users, $defect->dealUser, '');
                    break;
                case 'dealSuggest':
                    echo zget($this->lang->defect->dealSuggestList, $defect->dealSuggest);
                    break;
                case 'syncStatus':
                    echo zget($this->lang->defect->syncStatusList, $defect->syncStatus);
                    break;

                case 'actions':
                    common::printIcon('defect', 'edit', "defectID=$defect->id", $defect, 'list');
                    common::printIcon('defect', 'confirm', "defectID=$defect->id", $defect, 'list', 'ok', '', 'iframe', true);
                    common::printIcon('defect', 'deal', "defectID=$defect->id", $defect, 'list', 'time', '', 'iframe', true);

                    break;
            }
            echo '</td>';
        }
    }

    /**
     * 获得清总缺陷列表
     *
     * @param string $exWhere
     * @param string $select
     * @return array
     */
    public function getDefectList($exWhere = '', $select = '*'){
        $data = [];
        $ret = $this->dao->select($select)
            ->from(TABLE_DEFECT)
            ->where('deleted')->eq('0')
            ->beginIF($exWhere)->andWhere($exWhere)->fi()
            ->fetchAll();
        if($ret){
            $data = $ret;
        }
        return $data;
    }

    /**
     * 提醒PM邮件
     *
     * @return bool
     */
    public function remindProjectManagerMail()
    {
        $today         = helper::today();
        $yesterday     = helper::yesterday();
        $todayTime     = $today. ' 00:00:00';
        $yesterdayTime = $yesterday.' 00:00:00';
        $exWhere = " createdDate >= '{$yesterdayTime}' and createdDate < '{$todayTime}' and projectManager != '' and status != 'solved'";
        $select = 'id,code,title,status,dealUser,projectManager,source,createdBy,createdDate';
        $data = $this->getDefectList($exWhere, $select);
        if(empty($data)){
            return true;
        }
        $mailData = [];
        foreach ($data as $val){
            $defectId = $val->id;
            $projectManager = $val->projectManager;
            $mailData[$projectManager][$defectId] = $val;
        }
        $setMail = 'setDefectnoticeMail';
        $ccList = '';
        foreach ($mailData as $projectManager => $defectData){
            $toList = $projectManager;
            $count = count($defectData);
            $mailTitle = sprintf($this->lang->defect->defectSummaryNotice, $yesterday, $count);
            $this->loadModel('demand')->sendmailSummary($defectData, $setMail, 'defect', $toList, $ccList, 'remindprojectmanagermail', $mailTitle);
        }
        return true;
    }
}
