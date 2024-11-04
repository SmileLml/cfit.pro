<?php
include '../../control.php';
class myProblem extends problem
{
    public function export($orderBy = 'id_desc', $browseType = 'all')
    {
        if(isset($this->lang->problem->isExtendedUserList[$this->app->user->account]) || $this->app->user->account == 'admin'){
            $this->config->problem->list->exportFields .= ',isExtended,isBackExtended';
        }
        //后台自定义配置人员可以导出此字段
        if(in_array($this->app->user->account,explode(',',$this->lang->problem->examinationResultUpdateList['userList'])) || $this->app->user->account == 'admin'){
            $this->config->problem->list->exportFields .= ',dealAssigned,examinationResult';
        }
        /* format the fields of every problem in order to export data. */
        if ($_POST) {
            $this->loadModel('file');
            $problemLang = $this->lang->problem;
            $problemConfig = $this->config->problem;

            /* Create field lists. */
            $fields = $this->post->exportFields ? $this->post->exportFields : explode(',', $problemConfig->list->exportFields);
            foreach ($fields as $key => $fieldName) {
                $fieldName = trim($fieldName);
                if($fieldName == 'ifReturn'){
                    $fields[$fieldName] = $problemLang->ifRecive;
                    unset($fields[$key]);
                    continue;
                }
                $fields[$fieldName] = isset($problemLang->$fieldName) ? $problemLang->$fieldName : $fieldName;
                unset($fields[$key]);
            }
            /* Get problems. */
            $problems = array();
            if ($this->session->problemOnlyCondition) {
                $problems = $this->dao
                    ->select('*')
                    ->from(TABLE_PROBLEM)
                    ->where($this->session->problemQueryCondition)
                    ->andWhere('status')->ne('deleted')
                    ->beginIF($this->post->exportType == 'selected')->andWhere('id')->in($this->cookie->checkedItem)->fi()
                    ->orderBy($orderBy)->fetchAll('id');
            } else {
                $stmt = $this->dbh->query($this->session->problemQueryCondition . ($this->post->exportType == 'selected' ? " AND id IN({$this->cookie->checkedItem})" : '') . " ORDER BY " . strtr($orderBy, '_', ' '));
                while ($row = $stmt->fetch()) $problems[$row->id] = $row;
            }
            $problemIdList = array_keys($problems);

            /* Get users, products and executions. */
            $users = $this->loadModel('user')->getPairs('noletter');
            $apps = $this->loadModel('application')->getapplicationNameCodePairs();
            $depts = $this->loadModel('dept')->getTopPairs();
            $dmap = $this->dao->select('account,dept')->from(TABLE_USER)->fetchAll('account');
            $plans = $this->loadModel('project')->getPairs();

            $productPairs     = array('99999' => '无') + $this->loadModel('product')->getSimplePairs();
            $productPlanPairs = array('1' => '无') + $this->loadModel('productplan')->getSimplePairs();
            // Obtain the receiver.
            $cmap = $this->dao->select('objectID, account')->from(TABLE_CONSUMED)
                ->where('objectType')->eq('problem')
                ->andWhere('`before`')->eq('assigned')
                ->orderBy('id')
                ->fetchPairs();

            $this->loadModel('secondline');
            foreach ($problems as $problem) {
                $problem = $this->problem->getIfOverDate($problem);
                $problem->feedbackStartTimeInside  = $problem->ifOverDateInside['start'] ?? '';
                $problem->feedbackEndTimeInside    = $problem->deptPassTime ?? '';
                $problem->insideFeedbackDate       = $problem->ifOverDateInside['end'] ?? '';
                $problem->feedbackStartTimeOutside = $problem->ifOverDate['start'] ?? '';
                $problem->feedbackEndTimeOutside   = $problem->innovationPassTime ?? '';
                $problem->outsideFeedbackDate      = $problem->ifOverDate['end'] ?? '';
                $problem->ifOverTime               = $problem->ifOverDate['flag'] ?? '';
                $problem->ifOverTimeInside         = $problem->ifOverDateInside['flag'] ?? '';
//                $problem->status = $problemLang->statusList[$problem->status];
                $problem->source = $problemLang->sourceList[$problem->source];
                $problem->type = $problemLang->typeList[$problem->type];
                $problem->SolutionFeedback = $problemLang->solutionFeedbackList[$problem->SolutionFeedback] ?? '';
                $problem->severity = $problemLang->severityList[$problem->severity];
                $problem->acceptUser = zget($users, $problem->acceptUser, '');
                $problem->acceptDept = substr_replace(zget($depts, $problem->acceptDept, ''),'',0,1);

                $problem->pri = $problemLang->priList[$problem->pri];
                $problem->createdDept = $depts[$dmap[$problem->createdBy]->dept];
                $problem->createdBy = $users[$problem->createdBy];
                $problem->fixType = zget($problemLang->fixTypeList, $problem->fixType, '');
                $problem->projectPlan = zget($plans, $problem->projectPlan, '');
                //20220311 新增
                $problem->systemverify = zget($this->lang->problem->needOptions,$problem->systemverify,'');
                $problem->verifyperson = zget($users, $problem->verifyperson, '');
                $problem->laboratorytest = zget($users, $problem->laboratorytest, '');

                if(in_array($problem->status, ['feedbacked','build','released','delivery','onlinesuccess','closed'])) {
                    $problem->dealUser = '';
                }else{
                    $problem->dealUser    = zmget($users, $problem->dealUser);
                }
                $problem->editedBy = zget($users, $problem->editedBy, '');
                $problem->closedBy = zget($users, $problem->closedBy, '');
                $problem->onlineTime = $problem->solvedTime;
                //如果关联二线取二线时间，如果有待关闭状态取待关闭时间，没有待关闭取已关闭时间，否则为空
                $problem = $this->problem->getSolvedTime($problem);
                $problem->solveDate ='';
                if($problem->status == 'delivery' || $problem->status == 'onlinesuccess' || $problem->status == 'onlinefailed'){
                    $dealDateObj = $this->loadModel('consumed')->getDealDate('problem',$problem->id);
                    if($dealDateObj){
                        $problem->solveDate = date('Y-m-d',strtotime($dealDateObj->createdDate));
                    }
                }else if($problem->status == 'closed'){
                    $dealDateObj = $this->loadModel('problem')->getDate($problem->id);
                    if($dealDateObj){
                        $problem->solveDate = $dealDateObj->lastDealDate;
                    }
                }

                $problem->status = $problemLang->statusList[$problem->status];
                // 处理所属应用系统。
                if ($problem->app) {
                    $as = array();
                    foreach (explode(',', $problem->app) as $app) {
                        if (!$app) continue;
                        $as[] = zget($apps, $app);
                    }
                    $problem->app = implode(',', $as);
                }

                // 处理系统分类。
                if ($problem->isPayment) {
                    $as = array();
                    foreach (explode(',', $problem->isPayment) as $paymentID) {
                        if (!$paymentID) continue;
                        $as[] = zget($this->lang->application->isPaymentList, $paymentID, $paymentID);
                    }
                    $isPayment = implode(',', $as);
                    $problem->isPayment = $isPayment;
                }
                /* 获取所属产品。*/
                $productName = '';
                $products = explode(',',$problem->product);
                foreach ($products as $item) {
                    $productName .= zget($productPairs, $item,'').',';
                }
                $problem->product = trim($productName,',');

                /* 获取所属产品计划。*/
                $productPlanName = '';
                $productPlans = explode(',',$problem->productPlan);
                foreach ($productPlans as $item) {
                    $productPlanName .= zget($productPlanPairs, $item,'').',';
                }
                 $problem->productPlan = trim($productPlanName,',');
                /* 获取制版次数。不需要查询获取了，有字段累计增加。*/
                //$problem->buildTimes = $this->problem->getBuild($problem->id);

                /* 获取关联的生产变更，数据修正，数据获取。*/
                $problem->relationModify = '';
                $problem->relationFix = '';
                $problem->relationGain = '';
                $relationObject = $this->secondline->getByID($problem->id, 'problem');
                foreach ($relationObject['modify'] as $objectID => $object) {
                    $problem->relationModify .= $object . "\r\n";
                }

                foreach ($relationObject['fix'] as $objectID => $object) {
                    $problem->relationFix .= $object . "\r\n";
                }

                foreach ($relationObject['gain'] as $objectID => $object) {
                    $problem->relationGain .= $object . "\r\n";
                }
                //反馈单状态
                $problem->ReviewStatus = zget($this->lang->problem->feedbackStatusList, $problem->ReviewStatus);
                //反馈单待处理人
                if($problem->feedbackToHandle != null){
                    $userName = "";
                    $myArray = explode(',', $problem->feedbackToHandle);
                    foreach ($myArray as $account) {
                        if($userName == ""){
                            $userName .= $users[$account];
                        }else{
                            $userName .= ",";
                            $userName .= $users[$account];
                        }
                    }
                    $problem->feedbackToHandle = $userName;
                }
                //是否退回
                $problem->ifReturn = zget($this->lang->problem->ifReturnList, $problem->ifReturn);
                $problem->isChange = zget($this->lang->problem->isChangeList, $problem->isChange);
                //是否最终方案
                $problem->IfultimateSolution = zget($this->lang->problem->ifultimateSolutionList, $problem->IfultimateSolution);
                $problem->isExtended = zget($this->lang->problem->isExtendedList, $problem->isExtended);
                $problem->isBackExtended = zget($this->lang->problem->isBackExtendedList, $problem->isBackExtended);
                $problem->ChangeIdRelated = strip_tags($problem->ChangeIdRelated);
                $problem->EffectOfService = strip_tags($problem->EffectOfService);
                $problem->IncidentIdRelated = strip_tags($problem->IncidentIdRelated);
                $problem->DrillCausedBy = strip_tags($problem->DrillCausedBy);
                $problem->Optimization = strip_tags($problem->Optimization);
                $problem->Tier1Feedback = strip_tags($problem->Tier1Feedback);
                $problem->solution = strip_tags($problem->solution);
                $problem->ChangeSolvingTheIssue = strip_tags($problem->ChangeSolvingTheIssue);
                $problem->ReasonOfIssueRejecting = strip_tags($problem->ReasonOfIssueRejecting);
                $problem->EditorImpactscope = strip_tags($problem->EditorImpactscope);
                $problem->revisionRecord = strip_tags($problem->revisionRecord);
                $problem->ProblemSource = $problem->ProblemSource;
                $problem->sourece = zget($this->lang->problem->sourceList, $problem->source, $problem->source);
                //新增加字段-反馈单
                $problem->TimeOfOccurrence    = $problem->occurDate;
                $problem->problemFeedbackId    = $problem->IssueId;
                $problem->ultimateSolution    = $problem->solution;
                $problem->completedPlan       = zget($this->lang->problem->completedPlanList,$problem->completedPlan,'');
                $problem->examinationResult   = zget($this->lang->problem->examinationResultList,$problem->examinationResult,'');
                //反馈次数
                if($problem->IssueId == null){
                    $problem->feedbackNum = null;
                }else{
                    if($problem->feedbackNum>0){
                        $problem->feedbackNum = $problem->feedbackNum-1;
                    }
                }
                //延期信息
                $delay = $this->dao
                    ->select('delayResolutionDate,delayStatus')
                    ->from(TABLE_DELAY)
                    ->where('objectType')->eq('problem')
                    ->andWhere('objectId')->eq($problem->id)
                    ->orderBy('id_desc')
                    ->fetch();
                if(!empty($delay)){
                    $problem->delayResolutionDate = date('Y-m-d', strtotime($delay->delayResolutionDate));
                    $problem->delayStatus = sprintf(zget($this->lang->problem->delayStatusList, $delay->delayStatus),$this->lang->problem->delayName);
                }
                //变更信息
                $delay = $this->dao
                    ->select('changeResolutionDate,changeStatus,changeVersion,successVersion')
                    ->from(TABLE_PROBLEM_CHANGE)
                    ->where('objectType')->eq('problem')
                    ->andWhere('objectId')->eq($problem->id)
                    ->orderBy('id_desc')
                    ->fetch();
                if(!empty($delay)){
                    /*$problem->changeResolutionDate = date('Y-m-d', strtotime($delay->changeResolutionDate));
                    $problem->changeStatus =  sprintf(zget($this->lang->problem->delayStatusList, $delay->changeStatus),$this->lang->problem->changeName);*/
                    $problem->changeVersion       =  empty($delay->changeVersion) ? 0 : $delay->changeVersion;
                    $problem->successVersion     = empty($delay->successVersion) ? 0 : $delay->successVersion;
                }
            }
            $this->post->set('fields', $fields);
            $this->post->set('rows', $problems);
            $this->post->set('kind', 'problem');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        $this->view->fileName = $this->lang->problem->exportName;
        $this->view->allExportFields = $this->config->problem->list->exportFields;
        $this->view->customExport = true;
        $this->display();
    }
}