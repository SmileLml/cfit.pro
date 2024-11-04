<?php

include '../../control.php';
class myDemandinside extends demandinside
{
    /**
     *  20220311 导出新增字段 系统部验证 验证人员 实验室测试
     * @param string $orderBy
     * @param string $browseType
     */
    public function export($orderBy = 'id_desc', $browseType = 'all')
    {
        // format the fields of every demand in order to export data.
        if ($_POST) {
            $this->loadModel('file');
            $demandLang   = $this->lang->demandinside;
            $demandConfig = $this->config->demandinside;

            // Create field lists.
            $fields = $this->post->exportFields ?: explode(',', $demandConfig->list->exportFields);
            foreach ($fields as $key => $fieldName) {
                $fieldName          = trim($fieldName);
                $fields[$fieldName] = $demandLang->{$fieldName} ?? $fieldName;
                unset($fields[$key]);
            }
            // Get demands.
            $demands = [];
            if ($this->session->demandinsideOnlyCondition) {
                $demands = $this->dao->select('*')->from(TABLE_DEMAND)->where($this->session->demandinsideQueryCondition)
                    ->beginIF('selected' == $this->post->exportType)->andWhere('id')->in($this->cookie->checkedItem)->fi()
                    ->orderBy($orderBy)->fetchAll('id');
            } else {
                $stmt = $this->dbh->query($this->session->demandinsideQueryCondition . ('selected' == $this->post->exportType ? " AND id IN({$this->cookie->checkedItem})" : '') . ' ORDER BY ' . strtr($orderBy, '_', ' '));
                while ($row = $stmt->fetch()) {
                    $demands[$row->id] = $row;
                }
            }
            $demandIdList = array_keys($demands);
            // Get users, products and executions.
            $users = $this->loadModel('user')->getPairs('noletter');
            $apps  = $this->loadModel('application')->getapplicationNameCodePairs();
            $depts = $this->loadModel('dept')->getTopPairs();
            $dmap  = $this->dao->select('account,dept')->from(TABLE_USER)->fetchAll('account');
            $collections = $this->demandinside->getCollectionPairs();
            $this->loadModel('secondline');

            $productPairs     = $this->loadModel('product')->getSimplePairs();
            $productPlanPairs = $this->loadModel('productplan')->getSimplePairs();
            $plans            = $this->loadModel('project')->getPairs();
            //迭代二十五要求改为需求条目取需求任务的接收时间
//            $opinionsInfo = $this->dao->select('id,receiveDate')->from(TABLE_OPINION)->where('status')->ne('deleted')->fetchAll();
//            $opinionsReceivedTime = [];
//            foreach ($opinionsInfo as $item){
//                $opinionsReceivedTime[$item->id] = $item->receiveDate;
//            }

            $requirementInfo = $this->dao->select('id,acceptTime,opinion,createdBy,createdDate')
                ->from(TABLE_REQUIREMENT)
                ->where('status')->ne('deleted')
                ->andWhere('sourceRequirement')
                ->eq(2)
                ->fetchAll();
            $requirementAcceptTime = [];
            foreach ($requirementInfo as $item) {
                $requirementAcceptTime[$item->id] = $item->acceptTime;
                if ('guestcn' == $item->createdBy) {
                    $requirementAcceptTime[$item->id] = $item->createdDate;
                } else {
                    $opinionInfo                      = $this->loadModel('opinioninside')->getByID($item->opinion);
                    $requirementAcceptTime[$item->id] = $opinionInfo->receiveDate ?? '';
                }
            }
            // Obtain the receiver.
            $cmap = $this->dao->select('objectID, account')->from(TABLE_CONSUMED)
                ->where('objectType')->eq('demand')
                ->andWhere('`before`')->eq('assigned')
                ->orderBy('id')
                ->fetchPairs();

            // 获取所有需求条目数据。
            $allRequirement = $this->loadModel('requirementinside')->getPairs();
            $opinionList    = $this->loadModel('opinioninside')->getPairs();
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

//                $demand->status = $demandLang->statusList[$demand->status];
                $demand->reason   = strip_tags($demand->reason);
                $demand->progress = strip_tags($demand->progress);
                $demand->state    = $demandLang->stateList[$demand->state];

                $demand->createdDept = $depts[$dmap[$demand->createdBy]->dept];
//                $demand->acceptUser = isset($cmap[$demand->id]) ? $users[$cmap[$demand->id]] : '';
                $demand->acceptUser = zget($users, $demand->acceptUser, $demand->acceptUser);
//                $demand->acceptDept = isset($cmap[$demand->id]) ? $depts[$dmap[$cmap[$demand->id]]->dept] : '';
                $demand->acceptDept = zget($depts, $demand->acceptDept, '');
                $demand->createdBy  = $users[$demand->createdBy];
                //迭代二十五 只有已录入状态有待处理人,已挂起
                $dealUser = '';
                if (in_array(($demand->status), ['wait', 'suspend', 'feedbacked'])) {
                    $dealUser = zget($users, $demand->dealUser, '');
                }
                $demand->dealUser = $dealUser;
                $demand->editedBy = zget($users, $demand->editedBy, '');
                //20220311 新增
                $demand->systemverify   = zget($this->lang->demandinside->needOptions, $demand->systemverify, '');
                $demand->verifyperson   = zget($users, $demand->verifyperson, '');
                $demand->laboratorytest = zget($users, $demand->laboratorytest, '');

                $demand->closedBy = zget($users, $demand->closedBy, '');
                $demand->desc     = strip_tags($demand->desc);

                if ($demand->fixType) {
                    $demand->fixType = $demandLang->fixTypeList[$demand->fixType];
                }

                $demand->solveDate = '';
                if ('delivery' == $demand->status || 'onlinesuccess' == $demand->status || 'onlinefailed' == $demand->status) {
                    $dealDateObj = $this->loadModel('consumed')->getDealDate('demand', $demand->id);
                    if ($dealDateObj) {
                        $demand->solveDate = date('Y-m-d', strtotime($dealDateObj->createdDate));
                    }
                } elseif ('closed' == $demand->status) {
                    $dealDateObj = $this->loadModel('demandinside')->getDate($demand->id);
                    if ($dealDateObj) {
                        $demand->solveDate = $dealDateObj->lastDealDate;
                    }
                }

                $demand->status = $demandLang->statusList[$demand->status];

                // 处理所属应用系统。
                if ($demand->project) {
                    $as = [];
                    foreach (explode(',', $demand->project) as $project) {
                        if (!$project) {
                            continue;
                        }
                        $as[] = zget($plans, $project);
                    }
                    $demand->project = implode(',', $as);
                }
                // 处理所属应用系统。
                if ($demand->app) {
                    $as = [];
                    foreach (explode(',', $demand->app) as $app) {
                        if (!$app) {
                            continue;
                        }
                        $as[] = zget($apps, $app);
                    }
                    $demand->app = implode(',', $as);
                }
                //接收时间取需求任务的接收时间
                $demand->rcvDate = $requirementAcceptTime[$demand->requirementID];
                if ('0000-00-00' == $demand->end) {
                    $demand->end = '';
                }

                // 获取需求意向。
                if (0 == $demand->opinionID) {
                    $demand->opinionID = '';
                } else {
                    $demand->opinionID = zget($opinionList, $demand->opinionID, '');
                }
                // 获取需求意向任务。
                if (0 == $demand->requirementID) {
                    $demand->requirementID = '';
                } else {
                    $demand->requirementID = zget($allRequirement, $demand->requirementID, '');
                }
                // 处理系统分类。
                if ($demand->isPayment) {
                    $as = [];
                    foreach (explode(',', $demand->isPayment) as $paymentID) {
                        if (!$paymentID) {
                            continue;
                        }
                        $as[] = zget($this->lang->application->isPaymentList, $paymentID, $paymentID);
                    }
                    $isPayment         = implode(',', $as);
                    $demand->isPayment = $isPayment;
                }

                // 获取制版次数。
                $demand->buildTimes = $this->demandinside->getBuild($demand->id);

                // 获取所属产品。
                $demand->product = zget($productPairs, $demand->product, '');

                // 获取所属产品计划。
                $demand->productPlan = zget($productPlanPairs, $demand->productPlan, '');

                // 获取关联的生产变更，数据修正，数据获取。
                $demand->relationModify = '';
                $demand->relationFix    = '';
                $demand->relationGain   = '';
                $relationObject         = $this->secondline->getByID($demand->id, 'demandinside');
                foreach ($relationObject['modify'] as $objectID => $object) {
                    $demand->relationModify .= $object . "\r\n";
                }

                foreach ($relationObject['fix'] as $objectID => $object) {
                    $demand->relationFix .= $object . "\r\n";
                }

                foreach ($relationObject['gain'] as $objectID => $object) {
                    $demand->relationGain .= $object . "\r\n";
                }

                if(!empty($demand->collectionId)){
                    $demand->collectionId = zmget($collections, trim($demand->collectionId,','), '');
                }
                $demand->solvedTime = '';
            }
            $this->post->set('fields', $fields);
            $this->post->set('rows', $demands);
            $this->post->set('kind', 'demandinside');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        $this->view->fileName        = $this->lang->demandinside->exportName;
        $this->view->allExportFields = $this->config->demandinside->list->exportFields;
        $this->view->customExport    = true;
        $this->display();
    }

}
