<?php

class projectPlanActionTrigger extends control
{

    public function browse($browseType = 'all', $param = 0, $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1, $isSecondline = 0)
    {
        $browseType = strtolower($browseType);

        /* By search. */
        $queryID = ($browseType == 'bysearch')  ? (int)$param : 0;

        $actionURL = $this->createLink('projectplanactiontrigger', 'browse', "browseType=bySearch&param=myQueryID");

        $this->projectplanactiontrigger->buildSearchForm($queryID, $actionURL);
        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);
        $this->session->set('projectplanactiontriggerList', $this->app->getURI(true));

        $userDept = $this->loadModel('user')->getUserDeptName($this->app->user->account);
        $relationList = $this->projectplanactiontrigger->getList($browseType, $queryID, $orderBy, $pager, $isSecondline);


        //分管领导获取以及总经理获取
//        $users = $this->loadModel('user')->getPairs('noletter');
//        $deptInfo = $this->loadModel('dept')->getDeptPairs();
//        $deptIds = implode(',',array_keys($deptInfo));

        $projectplanList = $this->loadModel("projectplan")->getAllIncludeDeleteList();
        $this->view->projectplanList = array_column($projectplanList,null,'id');


        $this->view->title      = $this->lang->projectplan->common;
        $this->view->relationList      = $relationList;
        $this->view->orderBy    = $orderBy;
        $this->view->pager      = $pager;
        $this->view->param      = $param;

        $this->view->isSecondline      = $isSecondline;
        $this->view->browseType = $browseType;
        $this->view->depts      = $this->loadModel('dept')->getOptionMenu();
        $this->view->users      = $this->user->getPairs('noletter|noclosed');
        $this->view->userDept   = $userDept->deptName == '平台架构部' ? true : false;

        $this->display();
    }

    public function acttagging($ID=0){


        if($_POST){

            $dirPath = "/data/upload/projectplan/";
            $fileName = "projectplan_{$ID}.xlsx";
            $fileUrl = $dirPath.$fileName;
            $result = $this->projectplanactiontrigger->acttagging($ID,$fileUrl);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            //首次编辑才可以生成快照
            if($result && $result['acttagflag']){
                $this->projectplanphoto($dirPath,$fileName);
            }

            $actionID = $this->loadModel('action')->create('projectplanactiontrigger', $ID, 'acttagging', $result['snapshotVersion']);
            //生成快照

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);

        }
        $this->view->planActionInfo = $this->projectplanactiontrigger->getByID($ID);

        $this->display();
    }


    public function projectplanphoto($dirName,$fileName){
            $this->app->loadLang('opinion');
            $this->loadModel("projectplan");
            $allProducts = $this->loadModel('product')->getSimplePairs(); //2022-04-14 tongyanqi 所有产品名
            $this->projectplan->setListValue();

            $this->loadModel('file');
            $projectplanLang   = $this->lang->projectplan;
            $projectplanConfig = $this->config->projectplan;

            /* Create field lists. */
            $fields = explode(',', $projectplanConfig->list->exportFields);
            foreach($fields as $key => $fieldName)
            {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = isset($projectplanLang->$fieldName) ? $projectplanLang->$fieldName : $fieldName;
                unset($fields[$key]);
            }

            /* Get projectplans. */
            $projectplans = array();

            $projectplans = $this->dao->select('*')->from(TABLE_PROJECTPLAN)
                ->where('deleted')->ne('1')
                ->andWhere('secondLine')->eq(0)
                ->fetchAll('id');



            $projectplanIdList = array_keys($projectplans);

            /* Get users, products and executions. */
            $users = $this->loadModel('user')->getPairs('noletter');
            $depts = $this->loadModel('dept')->getOptionMenu();
            $lines = $this->loadModel('product')->getLinePairs(0);
            $appList  = $this->loadModel('application')->getPairs(0);
            $outsideproject     = $this->loadModel('outsideplan')->getPairs();
            $outsideTask        = $this->loadModel('outsideplan')->getTaskPairs();
            $outsideSubProject  = $this->loadModel('outsideplan')->getSubProjectPairs();
            $ops = $this->dao->select('id,name,linkedPlan')->from(TABLE_OUTSIDEPLAN)->where('deleted')->eq(0)->fetchAll();
            $opMap = array();
            foreach($ops as $op)
            {
                $linked = explode(',', $op->linkedPlan);
                foreach($linked as $l)
                {
                    if(!$l) continue;
                    if(!isset($opMap[$l])) $opMap[$l] = array();
                    $opMap[$l][] = $op->name;
                }
            }

            //获取所有年度计划
            $projectplanList = $this->projectplan->getAllList("id,mark");
            $projectplanList = array_column($projectplanList,"mark",'id');

            foreach($projectplans as $projectplan)
            {
                if(isset($projectplanLang->typeList[$projectplan->type]))               $projectplan->type        = $projectplanLang->typeList[$projectplan->type];
                if(isset($users[$projectplan->createdBy])) $projectplan->createdBy                                = $users[$projectplan->createdBy];
                if(isset($this->lang->opinion->categoryList[$projectplan->category]))   $projectplan->category    = $this->lang->opinion->categoryList[$projectplan->category];
                $projectplan->insideStatus    = zget($projectplanLang->insideStatusList, $projectplan->insideStatus);
                //2022-04-14 tongyanqi
                $basisArr = explode(',', str_replace(' ', '', $projectplan->basis));
                $allBasis = '';
                foreach($basisArr as $a)
                {
                    $allBasis .=  zget($projectplanLang->basisList, $a, '') . PHP_EOL;
                }
                $projectplan->basis       = $allBasis;

                //2023-08-16
                $mainRelationInfo = $this->loadModel("projectplanmsrelation")->getByMainPlanID($projectplan->id);
                $slaveRelationInfo = $this->loadModel("projectplanmsrelation")->getBySlavePlanID($projectplan->id);
                $projectplan->planIsMainProject = '';
                if($mainRelationInfo){
                    $tempmainplanIDArr = explode(",",$mainRelationInfo->slavePlanID);
                    foreach ($tempmainplanIDArr as $tempmainplanID){
                        $projectplan->planIsMainProject .= $projectplanList[$tempmainplanID].',';
                    }
                    $projectplan->planIsMainProject = trim($projectplan->planIsMainProject,',');

                }else{
                    $projectplan->planIsMainProject = "无";
                }
                $projectplan->planIsSlaveProject = '';
                if($slaveRelationInfo){

                    foreach ($slaveRelationInfo as $slaveInfo){
                        $projectplan->planIsSlaveProject .= $projectplanList[$slaveInfo->mainPlanID].',';
                    }
                    $projectplan->planIsSlaveProject = trim($projectplan->planIsSlaveProject,',');

                }else{
                    $projectplan->planIsSlaveProject = "无";
                }


                $projectplan->isImportant = $projectplanLang->isImportantList[$projectplan->isImportant] ?? $projectplan->isImportant;
                $projectplan->secondLine = $projectplanLang->secondLineList[$projectplan->secondLine] ?? $projectplan->secondLine;
                $projectplan->architrcturalTransform = $projectplanLang->architrcturalTransformList[$projectplan->architrcturalTransform] ?? $projectplan->architrcturalTransform;
                $projectplan->systemAssemble = $projectplanLang->systemAssembleList[$projectplan->systemAssemble] ?? $projectplan->systemAssemble;
                $projectplan->cloudComputing = $projectplanLang->cloudComputingList[$projectplan->cloudComputing] ?? $projectplan->cloudComputing;
                $projectplan->passwordChange = $projectplanLang->passwordChangeList[$projectplan->passwordChange] ?? $projectplan->passwordChange;
                if(isset($projectplanLang->storyStatusList[$projectplan->storyStatus])) $projectplan->storyStatus = $projectplanLang->storyStatusList[$projectplan->storyStatus];
                if(isset($projectplanLang->localizeList[$projectplan->localize]))       $projectplan->localize    = $projectplanLang->localizeList[$projectplan->localize];
                if(isset($projectplanLang->statusList[$projectplan->status])){
                    $projectplanstatusstr = '';
                    if($projectplan->status==$this->lang->projectplan->statusEnglishList['yearpass'] && $projectplan->changeStatus == $this->lang->projectplan->ChangestatusEnglishList['pending']){
                        $projectplanstatusstr = $this->lang->projectplan->changeing;
                    }else{
                        $projectplanstatusstr = zget($this->lang->projectplan->statusList, $projectplan->status, '');
                        if($projectplan->changeStatus == $this->lang->projectplan->ChangestatusEnglishList['pass']){
                            $projectplanstatusstr .= $this->lang->projectplan->changePass;
                        }else if($projectplan->changeStatus == $this->lang->projectplan->ChangestatusEnglishList['reject']){
                            $projectplanstatusstr .= $this->lang->projectplan->changeReject;
                        }
                    }
//                    $projectplan->status      = $projectplanLang->statusList[$projectplan->status];
                    $projectplan->status      = $projectplanstatusstr;
                }
                if(isset($projectplanLang->dataEnterLakeList[$projectplan->dataEnterLake]))         $projectplan->dataEnterLake      = $projectplanLang->dataEnterLakeList[$projectplan->dataEnterLake];
                if(isset($projectplanLang->basicUpgradeList[$projectplan->basicUpgrade]))           $projectplan->basicUpgrade       = $projectplanLang->basicUpgradeList[$projectplan->basicUpgrade];

                $owners = isset($projectplan->owner) ? explode(',', $projectplan->owner) : [];
                $allOwners = '';
                foreach ($owners as $owner) { $allOwners .= zget($users, $owner, ''). PHP_EOL;};
                $projectplan->owner     = $allOwners;

                $planDepts = explode(',', str_replace(' ', '', $projectplan->bearDept));
                $allDepts = '';
                foreach($planDepts as $deptID)
                {
                    if($deptID) $allDepts .= zget($depts, $deptID, '') .PHP_EOL;
                }
                $projectplan->bearDept  = $allDepts;



                $planStages = '';
                if($projectplan->planStages){
                    $stageNum = 1;
                    foreach (json_decode(base64_decode($projectplan->planStages), 1) as $item)
                    {
                        $planStages .=  '第'. $stageNum .'阶段:'. $item['stageBegin'] .'~'. $item['stageEnd'] . PHP_EOL;
                        $stageNum++;
                    }
                }
                $projectplan->planStages = $planStages;
                $projectplan->planRemark = strip_tags(br2nl($projectplan->planRemark)); //处理富文本换行
                $projectplan->content = strip_tags(str_replace(['&nbsp;'],' ',br2nl($projectplan->content))); //处理富文本换行

                $projectplan->platformowner = zmget($this->lang->projectplan->platformownerList,$projectplan->platformowner);
                $apps = explode(',', str_replace(' ', '', $projectplan->app));
                $allApps = '';
                foreach($apps as $appId)
                {
                    if($appId) $allApps .= isset($appList[$appId])? $appList[$appId] . PHP_EOL : ''. PHP_EOL;
                }
                $projectplan->app       = $allApps;

                $allDepts = explode(',', $projectplan->depts);
                $projectplan->depts = '';
                foreach($allDepts as $dept)
                {
                    if(isset($depts[$dept])) $projectplan->depts .= $depts[$dept].',';
                }

                $outsideProjects = explode(',', $projectplan->outsideProject);
                $projectplan->outsideProject = '';
                foreach ($outsideProjects as $outsideProjectId) {
                    $outsideId = trim($outsideProjectId);
                    if(isset($outsideproject[$outsideId])) $projectplan->outsideProject .=$outsideproject[$outsideId].',';
                }
                $projectplan->outsides =$projectplan->outsideProject;

                $outsideSubProjects = explode(',', $projectplan->outsideSubProject);
                $projectplan->outsideSubProject = '';
                foreach ($outsideSubProjects as $outsideSubProjectId) {
                    if(isset($outsideSubProject[$outsideSubProjectId])) $projectplan->outsideSubProject .=$outsideSubProject[$outsideSubProjectId].',';
                }


                $outsideTasks = explode(',', $projectplan->outsideTask);
                $projectplan->outsideTask = '';
                foreach ($outsideTasks as $outsideTaskId) {
                    if(isset($outsideTask[$outsideTaskId])) $projectplan->outsideTask .=$outsideTask[$outsideTaskId].',';
                }

                $projectplan->line = trim(trim($projectplan->line), ',');
                $linePairs         = explode(',', $projectplan->line);
                $projectplan->line = '';
                foreach($linePairs as $line)
                {
                    $line = trim($line);
                    if(isset($lines[$line])) $projectplan->line .= $lines[$line] . ',';
                }
                $projectplan->line = rtrim($projectplan->line, ',');


                $projectplan->reviewDate = substr($projectplan->reviewDate, 0, 10);

                $projectplan->begin      = substr($projectplan->begin, 0, 10);
                $projectplan->end        = substr($projectplan->end, 0, 10);
            }

            $this->post->set('fields', $fields);
            $this->post->set('rows', $projectplans);
            $this->post->set('kind', 'projectplan');

        //文件路径
        $basePath = dirname(__DIR__, 2) . '/www';

        $_POST['generatefilepath'] = $basePath.$dirName;

        if(!file_exists($_POST['generatefilepath']))
        {
            @mkdir($_POST['generatefilepath'], 0777, true);
            touch($_POST['generatefilepath'] . 'index.html');
            //2023-11-03 兼容cli模式下生成的目录文件权限
            if(PHP_SAPI == 'cli'){
                chmod($_POST['generatefilepath'],0777);
                chmod($_POST['generatefilepath'] . 'index.html',0777);
                @chown($_POST['generatefilepath'],'apache');
                @chown($_POST['generatefilepath'] . 'index.html','apache');
                @chgrp($_POST['generatefilepath'],'apache');
                @chgrp($_POST['generatefilepath'] . 'index.html','apache');
            }
        }

        //文件名 需求条目
        $_POST['generatefilename'] = $fileName;

        $res = $this->fetch('file', 'write2xlsx', $_POST);
    }



    public function delete($ID, $confirm = 'no')
    {
        if($confirm == 'no')
        {
            echo js::confirm($this->lang->projectplanactiontrigger->confirmDelete, $this->createLink('projectplanactiontrigger', 'delete', "ID=$ID&confirm=yes"), '');
            exit;
        }
        else
        {
            $this->projectplanactiontrigger->delete(TABLE_PROJECTPLANACTION, $ID);


            die(js::locate(inlink('browse'), 'parent'));
        }
    }


    public function export($orderBy = 'id_desc', $browseType = 'all')
    {

        if($_POST)
        {

            $this->loadModel('file');
            $projectplanactiontriggerLang   = $this->lang->projectplanactiontrigger;
            $projectplanactiontriggerConfig = $this->config->projectplanactiontrigger;

            /* Create field lists. */
            $fields = $this->post->exportFields ? $this->post->exportFields : explode(',', $projectplanactiontriggerConfig->list->exportFields);
            foreach($fields as $key => $fieldName)
            {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = isset($projectplanactiontriggerLang->$fieldName) ? $projectplanactiontriggerLang->$fieldName : $fieldName;
                unset($fields[$key]);
            }

            /* Get projectplans. */
            $projectplanActions = array();

            if($this->session->projectplanactiontriggerOnlyCondition)
            {
                $projectplanActions = $this->dao->select('*')->from(TABLE_PROJECTPLANACTION)->where($this->session->projectplanactiontriggerQueryCondition)
                    ->beginIF($this->post->exportType == 'selected')->andWhere('id')->in($this->cookie->checkedItem)->fi()
                    ->orderBy($orderBy)->fetchAll('id');
            }
            else
            {
                $stmt = $this->dbh->query($this->session->projectplanactiontriggerQueryCondition . ($this->post->exportType == 'selected' ? " AND id IN({$this->cookie->checkedItem})" : '') . " ORDER BY " . strtr($orderBy, '_', ' '));
                while($row = $stmt->fetch()) $projectplanActions[$row->id] = $row;
            }



            /* Get users, products and executions. */
            $users = $this->loadModel('user')->getPairs('noletter');
            $depts = $this->loadModel('dept')->getOptionMenu();




            foreach($projectplanActions as $planaction)
            {
                $planaction->actionUser = zget($users,$planaction->actionUser);
                $planaction->status = zget($projectplanactiontriggerLang->statusList,$planaction->status);


            }

            $this->post->set('fields', $fields);
            $this->post->set('rows', $projectplanActions);
            $this->post->set('kind', 'projectplanactiontrigger');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        $this->view->fileName        = $this->lang->projectplanactiontrigger->common;
        $this->view->allExportFields = $this->config->projectplanactiontrigger->list->exportFields;
        $this->view->customExport    = false;
        $this->display();
    }



}