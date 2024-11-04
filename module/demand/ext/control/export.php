<?php
include '../../control.php';
class myDemand extends demand
{
    /**
     *  20220311 导出新增字段 系统部验证 验证人员 实验室测试
     * @param string $orderBy
     * @param string $browseType
     */
    public function export($orderBy = 'id_desc', $browseType = 'all')
    {
        /* format the fields of every demand in order to export data. */
        if ($_POST) {
            $this->loadModel('file');
            $demandLang = $this->lang->demand;
            $demandConfig = $this->config->demand;

            /* Create field lists. */
            $fields = $this->post->exportFields ? $this->post->exportFields : explode(',', $demandConfig->list->exportFields);
            foreach ($fields as $key => $fieldName) {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = isset($demandLang->$fieldName) ? $demandLang->$fieldName : $fieldName;
                unset($fields[$key]);
            }

            //考核信息是否可见
            $isOverDateInfoVisible = $this->demand->getIsOverDateInfoVisible($this->app->user->account);
            if(!$isOverDateInfoVisible){
                foreach ($this->lang->demand->overDateInfoVisibleFields as $tempField){
                    unset($fields[$tempField]);
                }
            }

            /* Get demands. */
            $demands = array();
            if ($this->session->demandOnlyCondition) {
                $demands = $this->dao->select('*')->from(TABLE_DEMAND)->where($this->session->demandQueryCondition)
                    ->beginIF($this->post->exportType == 'selected')->andWhere('id')->in($this->cookie->checkedItem)->fi()
                    ->orderBy($orderBy)->fetchAll('id');
            } else {
                $stmt = $this->dbh->query($this->session->demandQueryCondition . ($this->post->exportType == 'selected' ? " AND id IN({$this->cookie->checkedItem})" : '') . " ORDER BY " . strtr($orderBy, '_', ' '));
                while ($row = $stmt->fetch()) $demands[$row->id] = $row;
            }
            $demandIdList = array_keys($demands);
            /* Get users, products and executions. */
            $users = $this->loadModel('user')->getPairs('noletter');
            $apps = $this->loadModel('application')->getPairs();
            $depts = $this->loadModel('dept')->getTopPairs();

            foreach ($depts as $key => $dept)
            {
                $depts[$key] = substr_replace($dept,'',0,1);
            }

            $dmap = $this->dao->select('account,dept')->from(TABLE_USER)->fetchAll('account');
            $this->loadModel('secondline');

            $productPairs = $this->loadModel('product')->getSimplePairs();
            $productPlanPairs = $this->loadModel('productplan')->getSimplePairs();
            $plans = $this->loadModel('project')->getPairs();
            //迭代二十五要求改为需求条目取需求任务的接收时间
//            $opinionsInfo = $this->dao->select('id,receiveDate')->from(TABLE_OPINION)->where('status')->ne('deleted')->fetchAll();
//            $opinionsReceivedTime = [];
//            foreach ($opinionsInfo as $item){
//                $opinionsReceivedTime[$item->id] = $item->receiveDate;
//            }

            $requirementInfo = $this->dao->select('id,acceptTime,opinion,createdBy,createdDate,actualMethod,feekBackStartTime,newPublishedTime')
                ->from(TABLE_REQUIREMENT)
                ->where('status')->ne('deleted')
                ->andWhere('sourceRequirement')
                ->eq(1)
                ->fetchAll();
            $requirementAcceptTime = [];
            $newPublishedTime = [];
            $actualMethod = [];
            foreach ($requirementInfo as $item){
                $requirementAcceptTime[$item->id] = $item->acceptTime;
                $actualMethod[$item->id]  = $item->actualMethod;
                if($item->createdBy == 'guestcn'){
                    $requirementAcceptTime[$item->id] = $item->createdDate;
                    $newPublishedTime[$item->id] = $item->feekBackStartTime != '0000-00-00 00:00:00' ? $item->feekBackStartTime : '';
                }else{
                    $opinionInfo = $this->loadModel('opinion')->getByID($item->opinion);
                    $requirementAcceptTime[$item->id] = $opinionInfo->receiveDate ?? '';
//                    $consumedInfo = $this->loadModel('consumed')->getCreatedDate('requirement',$item->id,'','published');
//                    $newPublishedTime[$item->id] = isset($consumedInfo->createdDate) ? $consumedInfo->createdDate : '';
                    $newPublishedTime[$item->id] = $item->newPublishedTime != '0000-00-00 00:00:00' ? $item->newPublishedTime : '';
                }
            }
            $newPublishedTime = array_filter($newPublishedTime);
            // Obtain the receiver.
            $cmap = $this->dao->select('objectID, account')->from(TABLE_CONSUMED)
                ->where('objectType')->eq('demand')
                ->andWhere('`before`')->eq('assigned')
                ->orderBy('id')
                ->fetchPairs();

            // 获取所有需求条目数据。
            $allRequirement = $this->loadModel('requirement')->getPairs();
            $opinionList = $this->loadModel('opinion')->getPairs();
            foreach ($demands as $demand) {
                // 处理需求来源方式。
                $demand->type = zget($demandLang->typeList, $demand->type, '');
                // 处理业务需求单位。
                $demand->union = zget($demandLang->unionList, $demand->union, '');

//                // 获取关联的需求条目。
//                $demand->requirement = trim($demand->requirement, ',');
//                if ($demand->requirement) {
//                    $requirements = explode(',', $demand->requirement);
//                    $requirementNameList = array();
//                    foreach ($requirements as $requirementID) $requirementNameList[] = zget($allRequirement, $requirementID, $requirementID);
//                    $demand->requirement = implode(',', $requirementNameList);
//                }
                $demand->newPublishedTime = $newPublishedTime[$demand->requirementID];
                //所属需求任务的实际实现方式
                $demand->actualMethod = zmget($demandLang->fixTypeList,$actualMethod[$demand->requirementID]);
                $demand->state = $demandLang->stateList[$demand->state];
                $demand->createdDept = $depts[$dmap[$demand->createdBy]->dept];
                $demand->acceptUser = zget($users, $demand->acceptUser, $demand->acceptUser);
                $demand->acceptDept = zget($depts, $demand->acceptDept, '');
                $demand->createdBy = $users[$demand->createdBy];
                //迭代二十五 只有已录入状态有待处理人,已挂起
                //迭代三十三 已挂起不显示待处理人
                $dealUser = '';
                if(in_array(($demand->status),['wait'])){
                    $dealUser = zget($users, $demand->dealUser, '');
                }

                if($demand->status == 'deleteout')
                {
                    $dealUser = '';
                }
                $demand->dealUser = $dealUser;
                $demand->editedBy = zget($users, $demand->editedBy, '');
                //20220311 新增
                $demand->systemverify = zget($this->lang->demand->needOptions,$demand->systemverify,'');
                $demand->verifyperson = zget($users, $demand->verifyperson, '');
                $demand->laboratorytest = zget($users, $demand->laboratorytest, '');

                $demand->closedBy = zget($users, $demand->closedBy, '');
                $demand->desc = strip_tags($demand->desc);

                if ($demand->fixType) $demand->fixType = $demandLang->fixTypeList[$demand->fixType];

                $demand->solveDate ='';
                if($demand->status == 'delivery' || $demand->status == 'onlinesuccess' || $demand->status == 'onlinefailed'){
                    $dealDateObj = $this->loadModel('consumed')->getDealDate('demand',$demand->id);
                    if($dealDateObj){
                        $demand->solveDate = date('Y-m-d',strtotime($dealDateObj->createdDate));
                    }
                }else if($demand->status == 'closed'){
                    $dealDateObj = $this->loadModel('demand')->getDate($demand->id);
                    if($dealDateObj){
                        $demand->solveDate = $dealDateObj->lastDealDate;
                    }
                }

                $demand->status = $demandLang->statusList[$demand->status];

                // 处理所属应用系统。
                if($demand->project)
                {
                    $as = array();
                    foreach(explode(',', $demand->project) as $project)
                    {
                        if(!$project) continue;
                        $as[] = zget($plans, $project);
                    }
                    $demand->project = implode(',', $as);
                }
                // 处理所属应用系统。
                if ($demand->app) {
                    $as = array();
                    foreach (explode(',', $demand->app) as $app) {
                        if (!$app) continue;
                        $as[] = zget($apps, $app);
                    }
                    $demand->app = implode(',', $as);
                }
                //接收时间取需求任务的接收时间
                $demand->rcvDate = $requirementAcceptTime[$demand->requirementID];
                if($demand->end == '0000-00-00'){
                    $demand->end = '';
                }
                if($demand->end == '0000-00-00'){
                    $demand->end = '';
                }
                if($demand->actualOnlineDate == '0000-00-00 00:00:00'){
                    $demand->actualOnlineDate = '';
                }
                if($demand->solvedTime == '0000-00-00 00:00:00'){
                    $demand->solvedTime = '';
                }
                if($demand->endDate == '0000-00-00'){
                    $demand->endDate = '';
                }

                // 获取需求意向。
                if($demand->opinionID == 0){
                    $demand->opinionID = '';
                }else{
                    $demand->opinionID = zget($opinionList, $demand->opinionID, '');
                }
                // 获取需求任务。
                if($demand->requirementID == 0){
                    $demand->requirementID = '';
                }else{
                    $demand->requirementID = zget($allRequirement, $demand->requirementID, '');
                }
                // 处理系统分类。
                if ($demand->isPayment) {
                    $as = array();
                    foreach (explode(',', $demand->isPayment) as $paymentID) {
                        if (!$paymentID) continue;
                        $as[] = zget($this->lang->application->isPaymentList, $paymentID, $paymentID);
                    }
                    $isPayment = implode(',', $as);
                    $demand->isPayment = $isPayment;
                }

                /* 获取制版次数。*/
                $demand->buildTimes = $this->demand->getBuild($demand->id);

                /* 获取所属产品。*/
                $demand->product = zget($productPairs, $demand->product, '');

                /* 获取所属产品计划。*/
                $demand->productPlan = zget($productPlanPairs, $demand->productPlan, '');

                /* 获取关联的生产变更，数据修正，数据获取。*/
                $demand->relationModify = '';
                $demand->relationFix = '';
                $demand->relationGain = '';
                $relationObject = $this->secondline->getByID($demand->id, 'demand');
                foreach ($relationObject['modify'] as $objectID => $object) {
                    $demand->relationModify .= $object . "\r\n";
                }

                foreach ($relationObject['fix'] as $objectID => $object) {
                    $demand->relationFix .= $object . "\r\n";
                }

                foreach ($relationObject['gain'] as $objectID => $object) {
                    $demand->relationGain .= $object . "\r\n";
                }
                $demand->delayStatus = zget($this->lang->demand->delayStatusList, $demand->delayStatus,'');
                if(!empty($demand->delayResolutionDate)){
                    $demand->delayResolutionDate = date('Y-m-d', strtotime($demand->delayResolutionDate));
                }
                $demand->isExtended   = zget($this->lang->demand->isExtendedList,$demand->isExtended);
                $demand->deliveryOver = zget($this->lang->demand->deliveryOverList,$demand->deliveryOver);
            }

            $this->post->set('fields', $fields);
            $this->post->set('rows', $demands);
            $this->post->set('kind', 'demand');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        $this->view->fileName = $this->lang->demand->exportName;
        $this->view->allExportFields = $this->config->demand->list->exportFields;
        $this->view->customExport = true;
        $this->display();
    }
}
