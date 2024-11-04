<?php

class demandstatistics extends control
{
    public function opinion()
    {
        /** @var opinionModel $model */
        $model    = $this->loadModel('opinion');
        $opinions = $model->getAllStatus();
        $list     = [];
        foreach ($this->lang->opinion->unionList as $unionId => $unionName) {
            if (empty($unionId)) {
                continue;
            }
            $list[$unionId]           = new stdClass();
            $list[$unionId]->name     = $unionName;
            $list[$unionId]->wait     = 0;
            $list[$unionId]->delivery = 0;
            $list[$unionId]->online   = 0;

            foreach ($opinions as $opinion) {
                $unionArr = explode(',', $opinion->union);

                //$opinion->union == $unionId ||
                if (in_array($unionId, $unionArr)) {
                    if (in_array($opinion->status, ['delivery', 'online'])) {
                        ++$list[$unionId]->{$opinion->status};
                    } elseif (in_array($opinion->status, ['created', 'waitupdate', 'pass', 'reject', 'subdivided'])) {
                        ++$list[$unionId]->wait;
                    } else {
                        continue;
                    }
                }
            }
        }
        $this->view->list     = $list;
        $this->view->selected = 1;
        $this->view->title    = $this->lang->demandstatistics->common;
        $this->display();
    }

    public function opinion2()
    {
        /** @var opinionModel $model */
        $model    = $this->loadModel('opinion');
        $opinions = $model->getAllStatus();
        $list     = [];
        foreach ($opinions as $opinion) {
            if (empty($opinion->category)) {
                continue;
            }

            if (!isset($list[$opinion->category])) {
                $list[$opinion->category] = new stdClass();
            }
            if (!isset($list[$opinion->category]->wait)) {
                $list[$opinion->category]->wait = 0;
            }
            if (!isset($list[$opinion->category]->online)) {
                $list[$opinion->category]->online = 0;
            }
            if (!isset($list[$opinion->category]->delivery)) {
                $list[$opinion->category]->delivery = 0;
            }

            if (in_array($opinion->status, ['delivery', 'online'])) {
                ++$list[$opinion->category]->{$opinion->status};
            } elseif (in_array($opinion->status, ['created', 'waitupdate', 'pass', 'reject', 'subdivided'])) {
                ++$list[$opinion->category]->wait;
            } else {
                continue;
            }
        }
        $this->view->list         = $list;
        $this->view->categoryList = $this->lang->opinion->categoryList;
        $this->view->selected     = 2;
        $this->view->title        = $this->lang->demandstatistics->common;
        $this->display();
    }

    public function requirement()
    {
        /** @var opinionModel $model */
        $opinionModel = $this->loadModel('opinion');
        $opinions     = $opinionModel->getPairs();
        /** @var requirementModel $model */
        $model        = $this->loadModel('requirement');
        $requirements = $model->getAllStatus();
        $list         = [];

        foreach ($requirements as $requirement) {
            if (!isset($list[$requirement->opinion])) {
                $list[$requirement->opinion] = new stdClass();
            }
            if (!isset($list[$requirement->opinion]->wait)) {
                $list[$requirement->opinion]->wait = 0;
            }
            if (!isset($list[$requirement->opinion]->onlined)) {
                $list[$requirement->opinion]->onlined = 0;
            }
            if (!isset($list[$requirement->opinion]->delivered)) {
                $list[$requirement->opinion]->delivered = 0;
            }

            if (in_array($requirement->status, ['delivered', 'onlined'])) {
                ++$list[$requirement->opinion]->{$requirement->status};
            } else {
                ++$list[$requirement->opinion]->wait;
            }
        }

        $this->view->list     = $list;
        $this->view->opinions = $opinions;
        $this->view->selected = 3;
        $this->view->title    = $this->lang->demandstatistics->common;
        $this->display();
    }

    public function demand()
    {
        /** @var demandModel $model */
        $model   = $this->loadModel('demand');
        $demands = $model->getAllStatus();
        $list    = [];
        foreach ($demands as $demand) {
            if (empty($demand) || empty($demand->acceptDept)) {
                continue;
            }

            if (in_array($demand->status, ['delivery', 'onlinesuccess', 'wait', 'feedbacked', 'build'])) {
                if (!isset($list[$demand->acceptDept])) {
                    $list[$demand->acceptDept] = new stdClass();
                }
                if (!isset($list[$demand->acceptDept]->wait)) {
                    $list[$demand->acceptDept]->wait = 0;
                }
                if (!isset($list[$demand->acceptDept]->onlinesuccess)) {
                    $list[$demand->acceptDept]->onlinesuccess = 0;
                }
                if (!isset($list[$demand->acceptDept]->delivery)) {
                    $list[$demand->acceptDept]->delivery = 0;
                }

                if (in_array($demand->status, ['delivery', 'onlinesuccess'])) {
                    ++$list[$demand->acceptDept]->{$demand->status};
                } elseif (in_array($demand->status, ['wait', 'feedbacked', 'build'])) {
                    ++$list[$demand->acceptDept]->wait;
                }
            }
        }
        $this->view->list     = $list;
        $this->view->selected = 4;
        $this->view->depts    = $this->loadModel('dept')->getOptionMenu();
        $this->view->title    = $this->lang->demandstatistics->common;
        $this->display();
    }

    /**
     * 需求池综合信息列表
     * @param mixed $startDate
     * @param mixed $endDate
     * @param mixed $recTotal
     * @param mixed $recPerPage
     * @param mixed $pageID
     */
    public function dro($start = '', $end = '', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $startDate = !empty($start) ? date('Y-m-d', $start) : '';
        $endDate   = !empty($end) ? date('Y-m-d', $end) : '';
        $this->loadModel('opinion');
        $this->app->loadLang('requirement');
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $list = $this->dao->select(implode(',', $this->config->demandstatistics->export->listFields))
            ->from(TABLE_OPINION)->alias('t3')
            ->leftJoin(TABLE_REQUIREMENT)->alias('t2')->on('t2.opinion = t3.id and t2.sourceRequirement = "1" and t2.status != "deleted"')
            ->leftJoin(TABLE_DEMAND)->alias('t1')->on('t1.requirementID = t2.id and t1.sourceDemand = "1" and t1.status != "deleted"')
            ->where('t3.sourceOpinion')->eq('1')
            ->andWhere('t3.status')->ne('deleted')
            ->beginIF(!empty($startDate) && '0000-00-00' != $startDate)
            ->andWhere('t3.createdDate')->ge($startDate)
            ->FI()
            ->beginIF(!empty($endDate) && '0000-00-00' != $endDate)
            ->andWhere('t3.createdDate')->le($endDate . ' 23:59:59')
            ->FI()
            ->orderBy('t3.id_desc', 't2.id_desc', 't1.id_desc')
            ->page($pager)
            ->fetchAll();


        foreach ($list as $k => $value){
            $list[$k]->opinionSolvedTime = $this->getOpinionSolvedTime($value);
            /*需求收集2816
             * 1、需求任务【期望完成时间】后面增加需求反馈单的【计划完成时间】和【反馈单状态】
             * 2、需求意向【期望完成时间】后面增加【计划完成时间】，这个字段取下边需求任务反馈单中计划完成时间填写的最晚时间（多个任务取最大值）
             */
            if($value->requirementEnd == '0000-00-00')
            {
                $list[$k]->requirementEnd = '';
            }
            //需求收集2912 迭代三十二 需求池/统计/需求池-综合信息表新增【需求任务最新发布时间】，多次发布取最大值。
            $list[$k]->finalPublishedTime = $value->finalPublishedTime != '0000-00-00 00:00:00' ? $value->finalPublishedTime : '';

            //需求提出时间
            if($value->opinionDate == '0000-00-00') $list[$k]->opinionDate = '';
            //期望完成时间
            if($value->opinionDeadline == '0000-00-00') $list[$k]->opinionDeadline = '';
            //意向交付时间
            if($value->opinionSolvedTime == '0000-00-00 00:00:00') $list[$k]->opinionSolvedTime = '';
            //意向上线时间
            if($value->opinionOnlineTimeByDemand == '0000-00-00 00:00:00') $list[$k]->opinionOnlineTimeByDemand = '';
            //期望完成时间
            if($value->requirementDeadLine == '0000-00-00') $list[$k]->requirementDeadLine = '';
            //任务上线时间
            if($value->requirementOnlineTimeByDemand == '0000-00-00 00:00:00') $list[$k]->requirementOnlineTimeByDemand = '';
            //计划完成时间
            if($value->requirementEnd == '0000-00-00') $list[$k]->requirementEnd = '';
            //需求任务最新发布时间
            if($value->finalPublishedTime == '0000-00-00 00:00:00') $list[$k]->finalPublishedTime = '';
            //条目上线时间
            if($value->demandActualOnlineDate == '0000-00-00 00:00:00') $list[$k]->demandActualOnlineDate = '';
            //条目交付时间
            if($value->demandSolvedTime == '0000-00-00 00:00:00') $list[$k]->demandSolvedTime = '';

            $list[$k]->requirementFeedbackStatus = zget($this->lang->requirement->feedbackStatusList,$value->requirementFeedbackStatus);
            //反馈单计划完成时间
            if($value->requirementFeedbackEnd == '0000-00-00') {
                $list[$k]->requirementFeedbackEnd = '';
            }
            $list[$k]->opinionEnd = $this->getRequirementMaxEnd($value->opinionID);

        }

        //根据项目ID获取项目名称
        $listData                = json_decode(json_encode($list), true);
        $this->view->projectList = $this->getProjectName($listData);
        //根据产品ID和产品版本ID查询产品信息
        list($productList, $productPlanList) = $this->getProductInfo($listData);

        $this->view->startDate       = $startDate;
        $this->view->endDate         = $endDate;
        $this->view->pager           = $pager;
        $this->view->productList     = $productList;
        $this->view->productPlanList = $productPlanList;
        $this->view->solvedTime      = $this->getRequirementSolvedTime($listData);
        $this->view->users           = $this->loadModel('user')->getPairs('noletter|noclosed');
        $this->view->apps            = $this->loadmodel('application')->getapplicationNameCodePairs();
        $this->view->list            = $list;
        $this->view->selected        = 5;
        $this->view->title           = $this->lang->demandstatistics->common;

        $this->display('demandstatistics', 'dro');
    }


    /**
     * @Notes: 需求池全生命周期统计表
     * @Date: 2023/7/13
     * @Time: 15:25
     * @Interface change
     * @param int $recTotal
     * @param int $recPerPage
     * @param int $pageID
     */
    public function change($recTotal = 0, $recPerPage = 50, $pageID = 1)
    {
        $this->app->loadLang('requirement');
        $paramStartDate = $this->post->startDate;
        $paramEndDate = $this->post->endDate;
        $paramProduct = $this->post->product;
        $paramProject = $this->post->project;

        //将搜索项存session 构造搜索条件
        if($pageID == 1){
            $this->session->set('demandstatisticsStartDate', $paramStartDate);
            $this->session->set('demandstatisticsEndDate', $paramEndDate);
            $this->session->set('demandstatisticsProduct', $paramProduct);
            $this->session->set('demandstatisticsProject', $paramProject);
        }
        $startDate = $this->session->demandstatisticsStartDate;
        $endDate = $this->session->demandstatisticsEndDate;
        $product = $this->session->demandstatisticsProduct;
        $project = $this->session->demandstatisticsProject;

        $this->loadModel('opinion');
        $this->loadModel('demand');
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);
        $list = $this->dao->select(implode(',', $this->config->demandstatistics->anquanbaobiao->selectFields))
            ->from(TABLE_OPINION)->alias('t3')
            ->leftJoin(TABLE_REQUIREMENT)->alias('t2')->on('t2.opinion = t3.id and t2.sourceRequirement = "1" and t2.status != "deleted"')
            ->leftJoin(TABLE_DEMAND)->alias('t1')->on('t1.requirementID = t2.id and t1.sourceDemand = "1" and t1.status != "deleted"')
            ->where('t3.sourceOpinion')->eq('1')
            ->beginIF(!empty($product))->andWhere('t1.product')->eq($product)
            ->beginIF(!empty($project))->andWhere('t1.project')->eq($project)
            ->beginIF(!empty($startDate) && '0000-00-00' != $startDate)
            ->andWhere('t3.receiveDate')->ge($startDate)
            ->FI()
            ->beginIF(!empty($endDate) && '0000-00-00' != $endDate)
            ->andWhere('t3.receiveDate')->le($endDate)
            ->FI()
            ->orderBy('t3.id_desc')
            ->page($pager)
            ->fetchAll();
        //构造合并单元格结构
        $opinionInfo = array();
        foreach ($list as $listKey => $listValue)
        {
            $opinionInfo[$listValue->id]['opinionID']     = $listValue->id;
            $opinionInfo[$listValue->id]['opinionName']   = $listValue->opinionName;
            $opinionInfo[$listValue->id]['opinionCode']   = $listValue->opinionCode;
            $opinionInfo[$listValue->id]['opinionStatus']   = zget($this->lang->opinion->statusList,$listValue->opinionStatus);
            if(!empty($listValue->requirementID))
            {
                $opinionInfo[$listValue->id]['requirements'][$listValue->requirementID]['requirementID']   = $listValue->requirementID;
                $opinionInfo[$listValue->id]['requirements'][$listValue->requirementID]['requirementCode'] = $listValue->requirementCode;
                $opinionInfo[$listValue->id]['requirements'][$listValue->requirementID]['requirementName'] = $listValue->requirementName;
                $opinionInfo[$listValue->id]['requirements'][$listValue->requirementID]['requirementStatus'] = zget($this->lang->requirement->statusList,$listValue->requirementStatus);

                if(!empty($listValue->demandID))
                {
                    $opinionInfo[$listValue->id]['requirements'][$listValue->requirementID]['demands'][$listValue->demandID]['demandID'] = $listValue->demandID;
                    $opinionInfo[$listValue->id]['requirements'][$listValue->requirementID]['demands'][$listValue->demandID]['demandCode'] = $listValue->demandCode;
                    $opinionInfo[$listValue->id]['requirements'][$listValue->requirementID]['demands'][$listValue->demandID]['demandName'] = $listValue->demandName;
                    $opinionInfo[$listValue->id]['requirements'][$listValue->requirementID]['demands'][$listValue->demandID]['demandStatus'] = zget($this->lang->demand->statusList,$listValue->demandStatus);
                    $opinionInfo[$listValue->id]['requirements'][$listValue->requirementID]['demands'][$listValue->demandID]['demandFixType'] = zget($this->lang->demand->fixTypeList,$listValue->demandFixType);

                    if(!$listValue->demandProductPlan || $listValue->demandProductPlan == 1){
                        $productplanstr = '无';
                    }else{
                        $productplanResult = $this->dao->select("title")->from(TABLE_PRODUCTPLAN)->where('id')->eq($listValue->demandProductPlan)->fetch();
                        if($productplanResult){
                            $productplanstr = $productplanResult->title;
                        }else{
                            $productplanstr = '';
                        }

                    }

                    $opinionInfo[$listValue->id]['requirements'][$listValue->requirementID]['demands'][$listValue->demandID]['demandProductPlan'] = $productplanstr;
                    $projectStr = "";
                    if($listValue->demandProject){
                        $projectResult = $this->dao->select("code,name")->from(TABLE_PROJECT)->where('id')->eq($listValue->demandProject)->fetch();
                        if($projectResult){
                            $projectStr = $projectResult->code.'_'.$projectResult->name;
                        }
                    }

                    $opinionInfo[$listValue->id]['requirements'][$listValue->requirementID]['demands'][$listValue->demandID]['demandProject'] = $projectStr;
                    $appStr = "";
                    if($listValue->demandApp){
                        $appResult = $this->dao->select('name,code')->from(TABLE_APPLICATION)->where('id')->eq($listValue->demandApp)->fetch();
                        if($appResult){
                            $appStr = $appResult->code.'_'.$appResult->name;
                        }
                    }

                    $opinionInfo[$listValue->id]['requirements'][$listValue->requirementID]['demands'][$listValue->demandID]['demandApp'] = $appStr;

                    $productStr = "无";
                    if($listValue->demandProduct){
                        $productResult = $this->dao->select('name,code')->from(TABLE_PRODUCT)->where('id')->eq($listValue->demandProduct)->fetch();

                        if($productResult){
                            $productStr = $productResult->code.'_'.$productResult->name;
                        }
                    }
                    $opinionInfo[$listValue->id]['requirements'][$listValue->requirementID]['demands'][$listValue->demandID]['demandProduct'] = $productStr;
                }else{
                    $opinionInfo[$listValue->id]['requirements'][$listValue->requirementID]['demands'] = [];

                }
            }else{
                $opinionInfo[$listValue->id]['requirements'] = [];
            }


        }

        $demandCount = 0;
        $allCountDemand = 0;
        foreach($opinionInfo as $key => $opinion)
        {
            $opinionCount = 0;
//            $requirementCount  = count($opinion['requirements']);
            $requirementCount  = 0;
            if(!empty($opinion['requirements']))
            {
                foreach ($opinion['requirements'] as $k => $requirement)
                {
                    if(!empty($requirement['demands']))
                    {
                        $demandCount = count($requirement['demands']);
                    }else{
                        $demandCount = 1;
                    }
                    $opinionInfo[$key]['requirements'][$k]['requirementCount'] = $demandCount;
                    $requirementCount += $demandCount;
                }
                $opinionInfo[$key]['countAll'] = $requirementCount;
            }else{
                $opinionInfo[$key]['countAll'] = 1;
            }
        }
        //根据项目ID获取项目名称
        $listData                = json_decode(json_encode($list), true);
        $productList = array('0' => '') + $this->loadModel('product')->getPairs();
        $this->view->product         = $product;
        $this->view->project         = $project;
        $this->view->startDate       = $startDate;
        $this->view->endDate         = $endDate;
        $this->view->pager           = $pager;
        $this->view->productList     = $productList;
        $this->view->projectList     = array('0' => '') + $this->loadModel('project')->getPairs();;
        $this->view->opinionInfo     = $opinionInfo;
        $this->view->solvedTime      = $this->getRequirementSolvedTime($listData);
        $this->view->users           = $this->loadModel('user')->getPairs('noletter|noclosed');
        $this->view->apps            = $this->loadmodel('application')->getapplicationNameCodePairs();
        $this->view->list            = $list;
        $this->view->selected        = 6;
        $this->view->title           = $this->lang->demandstatistics->common;

        $this->display('demandstatistics', 'change');
    }

    public function export($startDate = '', $endDate = '')
    {
        if ($_POST) {
            $startDate = date('Y-m-d', $startDate);
            $endDate   = date('Y-m-d', $endDate);
            $this->loadModel('file');
            $this->loadModel('opinion');
            $this->app->loadConfig('opinion');
            $this->app->loadLang('opinion');
            $this->loadModel('requirement');
            $this->app->loadConfig('requirement');
            $this->app->loadLang('requirement');
            $this->loadModel('demand');
            $this->app->loadConfig('demand');
            $this->app->loadLang('demand');

            $fields = [];
            foreach ($this->config->demandstatistics->list->exportFields as $fieldName) {
                $fieldName          = trim($fieldName);
                $fields[$fieldName] = $this->lang->demandstatistics->{$fieldName} ?? $fieldName;
            }
            $list = $this->dao->select(implode(',', $this->config->demandstatistics->export->listFields))
                ->from(TABLE_OPINION)->alias('t3')
                ->leftJoin(TABLE_REQUIREMENT)->alias('t2')->on('t2.opinion = t3.id and t2.sourceRequirement = "1" and t2.status != "deleted"')
                ->leftJoin(TABLE_DEMAND)->alias('t1')->on('t1.requirementID = t2.id and t1.sourceDemand = "1" and t1.status != "deleted"')
                ->where('t3.sourceOpinion')->eq('1')
                ->andWhere('t3.status')->ne('deleted')
                ->beginIF(!empty($startDate) && '0000-00-00' != $startDate)
                ->andWhere('t3.createdDate')->ge($startDate)
                ->FI()
                ->beginIF(!empty($endDate) && '0000-00-00' != $endDate)
                ->andWhere('t3.createdDate')->le($endDate)
                ->FI()
                ->orderBy('t3.id_desc', 't2.id_desc', 't1.id_desc')
                ->fetchAll();

            //根据项目ID获取项目名称
            $listData    = json_decode(json_encode($list), true);
            $projectList = $this->getProjectName($listData);
            //根据产品ID和产品版本ID查询产品信息
            list($productList, $productPlanList) = $this->getProductInfo($listData);
            //获取任务交付时间
            $solvedTime = $this->getRequirementSolvedTime($listData);

            $users = $this->loadModel('user')->getPairs('noletter');
            $apps  = $this->loadmodel('application')->getapplicationNameCodePairs();
            foreach ($list as $row) {
                $row->opinionOverview   = strip_tags($row->opinionOverview);
                $row->opinionDate       = 'guestcn' == $row->opinionCreatedBy ? $row->opinionCreatedDate : $row->opinionDate;
                $row->opinionSolvedTime = $this->getOpinionSolvedTime($row);
                $row->opinionStatus     = zget($this->lang->opinion->statusList, $row->opinionStatus, $row->opinionStatus);
                $row->opinionCreatedBy  = zget($users, $row->opinionCreatedBy, $row->opinionCreatedBy);
                $row->opinionCategory   = zget($this->lang->opinion->categoryList, $row->opinionCategory, '');
                $row->opinionAssignedTo = array_filter(explode(',', $row->opinionAssignedTo));
                $opinionAssignedTo      = '';
                foreach ($row->opinionAssignedTo as $assignedTo) {
                    $opinionAssignedTo .= zget($users, $assignedTo, $assignedTo) . '，';
                }
                $row->opinionAssignedTo = mb_substr($opinionAssignedTo, 0, -1);
                $row->opinionSourceMode = zget($this->lang->opinion->sourceModeListOld, $row->opinionSourceMode, $row->opinionSourceMode);
                if (!empty($row->opinionUnion)) {
                    $row->opinionUnion = array_unique(explode(',', $row->opinionUnion));
                    $opinionUnion      = '';
                    foreach ($row->opinionUnion as $union) {
                        if (!empty(trim($union))) {
                            $opinionUnion .= zget($this->lang->opinion->unionList, $union, '') . '，';
                        }
                    }
                    $row->opinionUnion = mb_substr($opinionUnion, 0, -1);
                }
                $row->opinionOnlineTimeByDemand = $this->lang->opinion->statusList['online'] == $row->opinionStatus ? $row->opinionOnlineTimeByDemand : '';
                $row->opinionProject            = $this->getProjectIdsByOpinion($listData, $row->opinionID, $projectList);
                $row->opinionEnd = $this->getRequirementMaxEnd($row->opinionID);

                if ('0000-00-00' == $row->requirementDeadLine || empty($row->requirementDeadLine)) {
                    $row->requirementDeadLine = 'guestcn' == $row->requirementCreatedBy ? $row->opinionDeadline : '';
                }
                if($row->requirementEnd == '0000-00-00')
                {
                    $row->requirementEnd = '';
                }

                $row->finalPublishedTime = $row->finalPublishedTime != '0000-00-00 00:00:00' ? $row->finalPublishedTime : '';
                $row->requirementFeedbackStatus = zget($this->lang->requirement->feedbackStatusList,$row->requirementFeedbackStatus);

                $row->requirementDesc      = strip_tags($row->requirementDesc);
                $row->requirementStatus    = zget($this->lang->requirement->statusList, $row->requirementStatus, $row->requirementStatus);
                $row->requirementCreatedBy = zget($users, $row->requirementCreatedBy, $row->requirementCreatedBy);
                $row->requirementProject   = $this->getProjectIdsByOpinion($listData, $row->opinionID, $projectList, $row->requirementID);
                if (!empty($row->requirementApp)) {
                    $appName             = '';
                    $row->requirementApp = explode(',', $row->requirementApp);
                    foreach ($row->requirementApp as $app) {
                        if ($app) {
                            $appName .= zget($apps, $app, '') . '，';
                        }
                    }
                    $row->requirementApp = mb_substr($appName, 0, -1);
                }
                $row->requirementProductManager = zget($users, $row->requirementProductManager, $row->requirementProductManager);
                $row->requirementOwner          = $this->getrequirementOwner($listData, $row->requirementID, $users);
                $row->requirementSourceMode     = zget($this->lang->opinion->sourceModeList, $row->opinionSourceMode, $row->opinionSourceMode);
                if($row->requirementFeedbackEnd == '0000-00-00') {
                    $row->requirementFeedbackEnd = '';
                }
                $row->requirementSolvedTime     = zget($solvedTime, $row->requirementID, '');
                $row->demandDesc      = strip_tags($row->demandDesc);
                $row->demandStatus    = zget($this->lang->demand->statusList, $row->demandStatus, $row->demandStatus);
                $row->demandCreatedBy = zget($users, $row->demandCreatedBy, $row->demandCreatedBy);
                $row->demandFixType   = zget($this->lang->demand->fixTypeList, $row->demandFixType, $row->demandFixType);
                if (!empty($row->demandProject)) {
                    $projectName        = '';
                    $row->demandProject = explode(',', trim($row->demandProject, ','));
                    foreach ($row->demandProject as $projectId) {
                        if ($projectList[$projectId]) {
                            $projectName .= $projectList[$projectId] . '，';
                        }
                    }
                    $row->demandProject = mb_substr($projectName, 0, -1);
                }
                $row->demandProduct     = $productList[$row->demandProduct]         ?? '';
                $row->demandProductPlan = $productPlanList[$row->demandProductPlan] ?? '';

                //需求提出时间
                if($row->opinionDate == '0000-00-00') $row->opinionDate = '';
                //期望完成时间
                if($row->opinionDeadline == '0000-00-00') $row->opinionDeadline = '';
                //意向交付时间
                if($row->opinionSolvedTime == '0000-00-00 00:00:00') $row->opinionSolvedTime = '';
                //意向上线时间
                if($row->opinionOnlineTimeByDemand == '0000-00-00 00:00:00') $row->opinionOnlineTimeByDemand = '';
                //期望完成时间
                if($row->requirementDeadLine == '0000-00-00') $row->requirementDeadLine = '';
                //任务上线时间
                if($row->requirementOnlineTimeByDemand == '0000-00-00 00:00:00') $row->requirementOnlineTimeByDemand = '';
                //计划完成时间
                if($row->requirementEnd == '0000-00-00') $row->requirementEnd = '';
                //计划完成时间
                if($row->demandEnd == '0000-00-00') $row->demandEnd = '';
                //期望完成时间
//                if($row->demandEndDate == '0000-00-00') $row->demandEndDate = '';
                //条目上线时间
                if($row->demandActualOnlineDate == '0000-00-00 00:00:00') $row->demandActualOnlineDate = '';
                //条目交付时间
                if($row->demandSolvedTime == '0000-00-00 00:00:00') $row->demandSolvedTime = '';
                //任务交付时间
                if($row->requirementSolvedTime == '0000-00-00 00:00:00') $row->requirementSolvedTime = '';
                if($row->requireStartTime == '0000-00-00') {
                    $row->requireStartTime = '';
                }

            }
            $this->post->set('fields', $fields);
            $this->post->set('rows', $list);
            $this->post->set('kind', 'secondorder');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        $this->view->fileName     = $this->lang->demandstatistics->dro;
        $this->view->customExport = true;
        $this->display();
    }

    //导出生命周期数据
    public function exportChange($startDate = '', $endDate = '',$product=0,$project=0)
    {
        $this->app->loadLang('requirement');
        $this->app->loadLang('opinion');
        $this->app->loadLang('demand');
        $startDate = !empty($startDate) ? date('Y-m-d',$startDate) : '';
        $endDate = !empty($endDate) ? date('Y-m-d',$endDate) : '';
        if ($_POST) {
            $this->loadModel('file');
            $fields = [];
            foreach ($this->config->demandstatistics->changeExport as $fieldName) {
                $fieldName          = trim($fieldName);
                $fields[$fieldName] = $this->lang->demandstatistics->{$fieldName} ?? $fieldName;
            }

            $list = $this->dao->select(implode(',', $this->config->demandstatistics->anquanbaobiao->selectFields))
                ->from(TABLE_OPINION)->alias('t3')
                ->leftJoin(TABLE_REQUIREMENT)->alias('t2')->on('t2.opinion = t3.id and t2.sourceRequirement = "1" and t2.status != "deleted"')
                ->leftJoin(TABLE_DEMAND)->alias('t1')->on('t1.requirementID = t2.id and t1.sourceDemand = "1" and t1.status != "deleted"')
                ->where('t3.sourceOpinion')->eq('1')
                ->beginIF(!empty($product))->andWhere('t1.product')->eq($product)
                ->beginIF(!empty($project))->andWhere('t1.project')->eq($project)
                ->beginIF(!empty($startDate) && '0000-00-00' != $startDate)
                ->andWhere('t3.receiveDate')->ge($startDate)
                ->FI()
                ->beginIF(!empty($endDate) && '0000-00-00' != $endDate)
                ->andWhere('t3.receiveDate')->le($endDate)
                ->FI()
                ->orderBy('t3.id_desc')
                ->fetchAll();
            $exportData =  array();

            $application = $this->loadmodel('application')->getapplicationNameCodePairs();
            $this->loadModel('demand');

            $listData    = json_decode(json_encode($list), true);
            $projectList = $this->getProjectName($listData);
            //根据产品ID和产品版本ID查询产品信息
            list($productList, $productPlanList) = $this->getProductInfo($listData);
            $productPlanList =  $this->getProductInfo($listData)[1];
            $productPlanEmpty = array('0'=>'无');
            foreach ($list as $key => $row) {
                $exportData[$key]->opinionName = !empty($row->opinionCode) ? $row->opinionCode.'('.$row->opinionName.')' : '';
                $exportData[$key]->requirement = !empty($row->requirementCode) ? $row->requirementCode.'('.$row->requirementName.')' : '';
                $exportData[$key]->demand = !empty($row->demandCode) ? $row->demandCode.'('.$row->demandName.')' : '';
                $exportData[$key]->requirementApp = zget($application,$row->demandApp);
                $exportData[$key]->demandProject = '';
                $projectResult = $this->dao->select("code,name")->from(TABLE_PROJECT)->where('id')->eq($row->demandProject)->fetch();
                if($projectResult){
                    $exportData[$key]->demandProject = $projectResult->code.'_'.$projectResult->name;
                }
                $productResult = $this->dao->select('name,code')->from(TABLE_PRODUCT)->where('id')->eq($row->demandProduct)->fetch();
                $exportData[$key]->demandProduct = !empty($productResult->code) ? $productResult->code.'_'.$productResult->name : '无';

                $exportData[$key]->demandProductPlan = zget($productPlanEmpty+$productPlanList,$row->demandProductPlan,'无');
                $exportData[$key]->demandFixType = zget($this->lang->demand->fixTypeList,$row->demandFixType);
                $exportData[$key]->opinionStatus = !empty($row->opinionStatus) ? zget($this->lang->opinion->statusList,$row->opinionStatus) : '';
                $exportData[$key]->requirementStatus = !empty($row->requirementStatus) ? zget($this->lang->requirement->statusList,$row->requirementStatus) : '';
                $exportData[$key]->demandStatus = !empty($row->demandStatus) ? zget($this->lang->demand->statusList,$row->demandStatus) : '';
            }
            $this->post->set('fields', $fields);
            $this->post->set('rows', $exportData);
            $this->post->set('kind', 'exportChange');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        $this->view->fileName     = $this->lang->demandstatistics->change;
        $this->view->customExport = true;
        $this->display();
    }

    private function getProjectName($listData)
    {
        $projectIds = array_merge(
            array_column($listData, 'opinionProject'),
            array_column($listData, 'requirementProject'),
            array_column($listData, 'demandProject')
        );
        $projectIds = array_unique(explode(',', implode(',', $projectIds)));
        $projectIds = array_filter($projectIds);

        return empty($projectIds) ? [] : $this->dao->select('`project`, `name`')
            ->from(TABLE_PROJECTPLAN)
            ->where('project')->in($projectIds)
            ->andWhere('deleted')->eq('0')
            ->fetchPairs();
    }

    private function getProductInfo($listData)
    {
        $productIds     = array_unique(array_column($listData, 'demandProduct'));
        $productKey     = array_search('99999', $productIds);
        $productPlanIds = array_unique(array_column($listData, 'demandProductPlan'));
        $productPlanKey = array_search('1', $productPlanIds);
        unset($productPlanIds[$productPlanKey], $productIds[$productKey]);

        $productList     = !empty($productIds) ? $this->loadModel('product')->getByIdPairs($productIds) : [];
        $productPlanList = !empty($productPlanIds) ? $this->loadModel('productplan')->getByIDPairs($productPlanIds) : [];

        return [$productList, $productPlanList];
    }

    private function getRequirementSolvedTime($listData)
    {
        $requirementID = array_filter(array_unique(array_column($listData, 'requirementID')));

        $data = $this->dao->select('status, solvedTime, closedDate, requirementID')
            ->from(TABLE_DEMAND)
            ->where('requirementID')->in($requirementID)
            ->fetchAll();

        $list = [];
        foreach ($data as $item) {
            $solvedTime = 'closed' == $item->status ? $item->closedDate : $item->solvedTime;
            if (isset($list[$item->requirementID])) {
                $list[$item->requirementID] = max($list[$item->requirementID], $solvedTime);
            } else {
                $list[$item->requirementID] = $solvedTime;
            }
        }

        return $list;
    }

    private function getProjectIdsByOpinion($listData, $opinionId, $projectList, $requirementId = 0)
    {
        $projectIds = [];

        if (0 != $requirementId) {
            foreach ($listData as $item) {
                if ($item['requirementID'] == $requirementId) {
                    $projectIds[] = $item['requirementProject'];
                    $projectIds[] = $item['demandProject'];
                }
            }
        } else {
            foreach ($listData as $item) {
                if ($item['opinionID'] == $opinionId) {
                    $projectIds[] = $item['opinionProject'];
                    $projectIds[] = $item['requirementProject'];
                    $projectIds[] = $item['demandProject'];
                }
            }
        }

        $projectIds = array_filter(array_unique(explode(',', implode(',', $projectIds))));

        $projectName = '';
        foreach ($projectIds as $projectId) {
            if ($projectList[$projectId]) {
                $projectName .= $projectList[$projectId] . '，';
            }
        }

        return trim($projectName, '，');
    }

    private function getrequirementOwner($listData, $requirementId, $users)
    {
        $acceptUsers = [];

        foreach ($listData as $item) {
            if ($item['requirementID'] == $requirementId && !empty($item['demandAcceptUser'])) {
                $acceptUsers[] = zget($users, $item['demandAcceptUser'], $item['demandAcceptUser']);
            }
        }

        return implode('，', array_unique($acceptUsers));
    }

    /**
     * 获取需求意向交付时间
     * @param $opinion
     * @return string
     */
    private function getOpinionSolvedTime($opinion)
    {
        if(!in_array($opinion->opinionStatus, ['delivery','online'])){
            return '';
        }

        $info = $this->dao
            ->select('objectID,max(createdDate) as createdDate')
            ->from(TABLE_CONSUMED)
            ->where('objectType')->eq('opinion')
            ->andWhere('after')->eq('delivery')
            ->andWhere('objectID')->eq($opinion->opinionID)
            ->andWhere('deleted')->eq('0')
            ->groupBy('objectID')
            ->fetch();

        return $info->createdDate ?? '';
    }

    /**
     * 获取需求任务最大计划完成时间
     * @param $opinion
     * @return string
     */
    public function getRequirementMaxEnd($opinionID)
    {
        $info = $this->dao
            ->select('id,max(planEnd) as opinionEnd')
            ->from(TABLE_REQUIREMENT)
            ->where('opinion')->eq($opinionID)
            //->andWhere('`createdBy`')->eq('guestcn')
            ->andWhere('`status`')->ne('deleted')
            ->fetch();
        if($info->opinionEnd == '0000-00-00')
        {
            $info->opinionEnd = '';
        }
        return $info->opinionEnd;
    }
}
