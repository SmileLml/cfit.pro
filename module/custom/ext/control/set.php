<?php
include '../../control.php';
class myCustom extends custom 
{
    /**
     * Project: chengfangjinke
     * Method: set
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:11
     * Desc: This is the code comment. This method is called set.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param string $module
     * @param string $field
     * @param string $lang
     */
    public function set($module = 'story', $field = 'priList', $lang = '')
    {
        if(empty($lang)) $lang = $this->app->getClientLang();
        if($module == 'user' and $field == 'priList') $field = 'statusList';
        if($module == 'block' and $field == 'priList')$field = 'closed';
        $currentLang = $this->app->getClientLang();

        $this->app->loadLang($module);
        if($module == 'review' and $field == 'endDate')$field = 'endDates';
        $fieldList = zget($this->lang->$module, $field, '');

        // 特殊页面处理
//        if(($module == 'bug' and in_array($field, array('guide', 'childTypeList'))) or ($module == 'secondorder' and $field == 'childTypeList') or ($module == 'deptorder' and $field == 'childTypeList'))
        if (in_array($module,$this->lang->custom->specialModel) and in_array($field,['guide','childTypeList']))
        {
            $this->view->title       = $this->lang->custom->common . $this->lang->colon . $this->lang->$module->common;
            $this->view->position[]  = $this->lang->custom->common;
            $this->view->position[]  = $this->lang->$module->common;
            $this->view->fieldList   = $fieldList;
            $this->view->field       = $field;
            $this->view->module      = $module;
            $this->view->lang2Set     = str_replace('_', '-', $lang);
            $this->view->currentLang = $currentLang;
            $this->view->canAdd      = strpos($this->config->custom->canAdd[$module], $field) !== false;
        }

        if($module == 'bug' and $field == 'childTypeList')
        {
            if(!empty($_POST))
            {
                $result = $this->custom->setChildTypeList($module, $field);
                if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));
                $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $this->createLink('custom', 'set', "module=$module&field=$field&lang=" . str_replace('-', '_', isset($this->config->langs[$lang]) ? $lang : 'all'))));
            }

            // 自定义的Bug子类数据。
            $typeList      = $this->lang->bug->typeList;
            $childTypeList = isset($this->lang->bug->childTypeList) ? $this->lang->bug->childTypeList['all'] : '[]';
            $childTypeList = json_decode($childTypeList);
            $customList = array();
            foreach($childTypeList as $type => $items)
            {
                // Bug分类不存在的话，子类也不展示了。
                if(empty($typeList[$type])) continue;

                foreach($items as $key => $value)
                {
                    $data = array();
                    $data['typeList'] = $type;
                    $data['childTypeListKey'] = $key;
                    $data['childTypeListValue'] = $value;
                    $customList[] = $data;
                }
            }
            $this->view->customList      = $customList;
            $this->view->typeList        = $typeList;
            $this->view->enableChildType = isset($this->config->global->enableChildType) ? $this->config->global->enableChildType : 0;

            $this->display('custom', 'childtypelist');
            die();
        }

        // 二线工单子类型
        if($module == 'secondorder' and $field == 'childTypeList')
        {
            if(!empty($_POST))
            {
                $result = $this->custom->setChildTypeList($module, $field);
                if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));
                $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $this->createLink('custom', 'set', "module=$module&field=$field&lang=" . str_replace('-', '_', isset($this->config->langs[$lang]) ? $lang : 'all'))));
            }
            // 自定义的子类型数据。
            $typeList      = $this->lang->secondorder->typeList;
            $childTypeList = isset($this->lang->secondorder->childTypeList) ? $this->lang->secondorder->childTypeList['all'] : '[]';
            $childTypeList = json_decode($childTypeList);
            $customList = array();
            foreach($childTypeList as $type => $items)
            {
                // 父类型不存在的话，子类也不展示了。
                if(empty($typeList[$type])) continue;

                foreach($items as $key => $value)
                {
                    $data = array();
                    $data['typeList'] = $type;
                    $data['childTypeListKey'] = $key;
                    $data['childTypeListValue'] = $value;
                    $customList[] = $data;
                }
            }
            $this->view->customList      = $customList;
            $this->view->typeList        = $typeList;
            $this->view->enableChildType = isset($this->config->global->enableChildType) ? $this->config->global->enableChildType : 0;

            $this->display('custom', 'childtypelist');
            die();
        }

        // 部门工单子类型
        if($module == 'deptorder' and $field == 'childTypeList')
        {
            if(!empty($_POST))
            {
                $result = $this->custom->setChildTypeList($module, $field);
                if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));
                $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $this->createLink('custom', 'set', "module=$module&field=$field&lang=" . str_replace('-', '_', isset($this->config->langs[$lang]) ? $lang : 'all'))));
            }
            // 自定义的子类型数据。
            $typeList      = $this->lang->deptorder->typeList;
            $childTypeList = isset($this->lang->deptorder->childTypeList) ? $this->lang->deptorder->childTypeList['all'] : '[]';
            $childTypeList = json_decode($childTypeList);
            $customList = array();
            foreach($childTypeList as $type => $items)
            {
                // 父类型不存在的话，子类也不展示了。
                if(empty($typeList[$type])) continue;
                foreach($items as $key => $value)
                {
                    $data = array();
                    $data['typeList'] = $type;
                    $data['childTypeListKey'] = $key;
                    $data['childTypeListValue'] = $value;
                    $customList[] = $data;
                }
            }
            $this->view->customList      = $customList;
            $this->view->typeList        = $typeList;
            $this->view->enableChildType = isset($this->config->global->enableChildType) ? $this->config->global->enableChildType : 0;

            $this->display('custom', 'childtypelist');
            die();
        }
        if(in_array($module,['outwarddelivery','modify','infoqz','info']) and $field == 'childTypeList')
        {
            if(!empty($_POST))
            {
                $result = $this->custom->setChildTypeList($module, $field);
                if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));
                $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $this->createLink('custom', 'set', "module=$module&field=$field&lang=" . str_replace('-', '_', isset($this->config->langs[$lang]) ? $lang : 'all'))));
            }
            // 自定义的子类型数据。
            $typeList      = $this->lang->{$module}->revertReasonList;
            $childTypeList = isset($this->lang->{$module}->childTypeList) ? $this->lang->{$module}->childTypeList['all'] : '[]';
            $childTypeList = json_decode($childTypeList);
            $customList = array();
            foreach($childTypeList as $type => $items)
            {
                // 父类型不存在的话，子类也不展示了。
                if(empty($typeList[$type])) continue;
                foreach($items as $key => $value)
                {
                    $data = array();
                    $data['typeList'] = $type;
                    $data['childTypeListKey'] = $key;
                    $data['childTypeListValue'] = $value;
                    $customList[] = $data;
                }
            }
            $this->view->customList      = $customList;
            $this->view->module          = $module;
            $this->view->typeList        = $typeList;
            $this->view->enableChildType = isset($this->config->global->enableChildType) ? $this->config->global->enableChildType : 0;

            $this->display('custom', 'childtypelist');
            die();
        }
        // 需求收集子类型
        if($module == 'demandcollection' and $field == 'belongModel')
        {
            if(!empty($_POST))
            {
                $result = $this->custom->setChildTypeList($module, $field);
                if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));
                $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $this->createLink('custom', 'set', "module=$module&field=$field&lang=" . str_replace('-', '_', isset($this->config->langs[$lang]) ? $lang : 'all'))));
            }
            // 自定义的子类型数据
            $typeList      = $this->lang->demandcollection->belongPlatform;
            $childTypeList = isset($this->lang->demandcollection->childTypeList) ? $this->lang->demandcollection->childTypeList['all'] : '[]';
            $childTypeList = json_decode($childTypeList);
            $customList = array();
            foreach($childTypeList as $type => $items)
            {
                // 父类型不存在的话，子类也不展示了。
                if(empty($typeList[$type])) continue;
                foreach($items as $key => $value)
                {
                    $data = array();
                    $data['typeList'] = $type;
                    $data['childTypeListKey'] = $key;
                    $data['childTypeListValue'] = $value;
                    $customList[] = $data;
                }
            }
            $this->view->module          = $module;
            $this->view->field           = $field;
            $this->view->customList      = $customList;
            $this->view->typeList        = $typeList;
            $this->view->enableChildType = isset($this->config->global->enableChildType) ? $this->config->global->enableChildType : 0;
            $this->view->title       = $this->lang->custom->common . $this->lang->colon . $this->lang->$module->common;
            $this->display('custom', 'childtypelist');
            die();
        }
        if($module == 'demand' and $field == 'changeSwitchList')
        {
            $checked = isset($this->config->changeSwitch) ? $this->config->changeSwitch : 2;
            if($_POST)
            {
                $this->loadModel('setting')->setItem('system.common.changeSwitch', $_POST['changeSwitch']);
                if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));
                $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $this->createLink('custom', 'set', "module=$module&field=$field&lang=" . str_replace('-', '_', isset($this->config->langs[$lang]) ? $lang : 'all'))));

            }
            $this->view->module          = $module;
            $this->view->field           = $field;
            $this->view->checked         = $checked;
            $this->display();
            die();
        }
        if($module == 'demand' and $field == 'singleUsage')
        {
            $checked = $this->config->singleUsage ?? 'off';
            if($_POST)
            {
                $this->loadModel('setting')->setItem('system.common.singleUsage', $_POST['singleUsage']);
                if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));
                $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $this->createLink('custom', 'set', "module=$module&field=$field&lang=" . str_replace('-', '_', isset($this->config->langs[$lang]) ? $lang : 'all'))));

            }
            $this->view->module  = $module;
            $this->view->field   = $field;
            $this->view->checked = $checked;
            $this->display();
            die();
        }
        if($module == 'modify' and $field == 'changeCloseSwitchList')
        {
            $checked = $this->config->changeCloseSwitch ?? 2;
            if($_POST)
            {
                $this->loadModel('setting')->setItem('system.common.changeCloseSwitch', $_POST['changeCloseSwitch']);
                if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));
                $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $this->createLink('custom', 'set', "module=$module&field=$field&lang=" . str_replace('-', '_', isset($this->config->langs[$lang]) ? $lang : 'all'))));

            }
            $this->view->module          = $module;
            $this->view->field           = $field;
            $this->view->checked         = $checked;
            $this->display();
            die();
        }
        if($module == 'problem' and $field == 'statusYearSwitch')
        {
            $checked = $this->config->statusYearSwitch ?? 2;
            if($_POST)
            {
                $this->loadModel('setting')->setItem('system.common.statusYearSwitch', $_POST['statusYearSwitch']);
                if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));
                $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $this->createLink('custom', 'set', "module=$module&field=$field&lang=" . str_replace('-', '_', isset($this->config->langs[$lang]) ? $lang : 'all'))));

            }
            $this->view->module          = $module;
            $this->view->field           = $field;
            $this->view->checked         = $checked;
            $this->display();
            die();
        }
        if($module == 'problem' and $field == 'deptLeadersList') {
            $this->loadModel('problem');
            // 部门数据。
            $deptList      = $this->dao->select('id,name')->from(TABLE_DEPT)->fetchPairs();
            $this->view->deptList      = $deptList;
            $this->view->deptLeadersList = $this->config->problem->deptLeadersList;
            $this->view->users = $this->loadModel('user')->getPairs('noclosed');
        }
        if($module == 'bug' and $field == 'guide')
        {
            if(!empty($_POST))
            {
                $result = $this->custom->setGuide($module, $field);
                if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));
                $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $this->createLink('custom', 'set', "module=$module&field=$field&lang=" . str_replace('-', '_', isset($this->config->langs[$lang]) ? $lang : 'all'))));
            }

            $this->loadModel('file');
            $this->view->file = $this->custom->getGuideFile();
            $this->display('custom', 'guide');
            die();
        }



        if($module == 'project' and $field == 'unitList')
        {
            $this->app->loadConfig($module);
            $unitList = zget($this->config->$module, 'unitList', '');
            $this->view->unitList        = explode(',', $unitList);
            $this->view->defaultCurrency = zget($this->config->$module, 'defaultCurrency', 'CNY');
        }
        if($module == 'qualitygate' && $field == 'allowQualityGateDeptIds') {
            $this->loadModel('qualitygate');
            // 部门数据。
            $deptList  = $this->loadModel('dept')->getDeptPairs();
            $this->view->deptList = $deptList;
        }
        if($module == 'project' and $field == 'roleList')
        {
            $this->app->loadConfig($module);
            $roleList = zget($this->config->$module, 'roleList', '');
            $this->view->roleList        = explode(',', $roleList);
            $this->view->defaultRole     = zget($this->config->$module, 'defaultRole', 'others');
        }
        if(($module == 'story' or $module == 'testcase') and $field == 'review')
        {
            $this->app->loadConfig($module);
            $this->view->users = $this->loadModel('user')->getPairs('noclosed|nodeleted');
            $this->view->needReview     = zget($this->config->$module, 'needReview', 1);
            $this->view->forceReview    = zget($this->config->$module, 'forceReview', '');
            $this->view->forceNotReview = zget($this->config->$module, 'forceNotReview', '');
        }
        if($module == 'task' and $field == 'hours')
        {
            $this->app->loadConfig('execution');
            $this->view->weekend   = $this->config->execution->weekend;
            $this->view->workhours = $this->config->execution->defaultWorkhours;
        }
        if($module == 'task' and $field == 'workThreshold')
        {
            $this->app->loadConfig('task');
            $this->view->workThreshold = $this->config->task->workThreshold;
        }
        if($module == 'task' and $field == 'workBuffer')
        {
            $this->app->loadConfig('task');
            $this->view->workBuffer = $this->config->task->workBuffer;
        }
        if($module == 'workreport' and $field == 'leaderList')
        {
            $this->view->userList = $this->lang->workreport->leaderList;
            $this->view->users     = $this->loadModel('user')->getPairs('noclosed');
        }
        if($module == 'workreport' and $field == 'deptList')
        {
            $this->view->deptList = $this->lang->workreport->deptList;
            $this->view->dept     = $this->loadModel('dept')->getPairs('noclosed');
        }
        if($module == 'requestlog' and $field == 'userList')
        {
            $this->view->userList = $this->lang->requestlog->userList;
            $this->view->users     = $this->loadModel('user')->getPairs('noclosed');
        }
        if($module == 'build' and $field == 'leaderList')
        {
            $this->view->leaderList = $this->lang->build->leaderList;
            $this->view->users     = $this->loadModel('user')->getPairs('noclosed');
        }
        if($module == 'projectplan' and $field == 'shProductAndarchList')
        {
            $this->view->users     = $this->loadModel('user')->getPairs('noclosed');
        }
        if($module == 'project' and $field == 'setShWhiteList')
        {
            $this->view->users     = $this->loadModel('user')->getPairs('noclosed');
        }
        if($module == 'project' and $field == 'projectSetList')
        {
            $this->view->users     = $this->loadModel('user')->getPairs('noclosed');
        }
        if($module == 'projectplan' and $field == 'shProjectPlanDeptList')
        {
            $this->view->dept     = $this->loadModel('dept')->getPairs('noclosed');
        }
        if($module == 'issue' and $field == 'leaderList')
        {
            $this->view->userList = $this->lang->issue->leaderList;
            $this->view->users     = $this->loadModel('user')->getPairs('noclosed');
        }
        if($module == 'issue' and $field == 'assignToList')
        {
            $this->view->userList = $this->lang->issue->assignToList;
            $this->view->users     = $this->loadModel('user')->getPairs('noclosed');
        }
        if($module == 'opinion' and $field == 'groupList')
        {
            $this->view->ownerList = $this->lang->opinion->ownerList;
            $this->view->users     = $this->loadModel('user')->getPairs('noclosed');
        }
        if($module == 'localesupport')
        {
            $this->app->loadConfig('localesupport');
            if($field == 'projectList'){
                $this->view->projectList = $this->lang->localesupport->projectList;
                $this->view->projects     = array('0' => '') + $this->loadModel('projectplan')->getAliveProjects(true);
            }elseif($field == 'limitDaySwitch'){
                $this->view->limitDaySwitch = $this->config->localesupport->limitDaySwitch;
            }elseif($field == 'reportWorkLimitDay'){
                $this->view->reportWorkLimitDay = $this->config->localesupport->reportWorkLimitDay;
            }
        }
        if($module == 'problem' and $field == 'examinationResultUpdateList')
        {
            $this->view->users     = $this->loadModel('user')->getPairs('noclosed');
        }
        if($module == 'bug' and $field == 'longlife')
        {
            $this->app->loadConfig('bug');
            $this->view->longlife = $this->config->bug->longlife;
        }
        if($module == 'block' and $field == 'closed')
        {
            $this->loadModel('block');
            $closedBlock = isset($this->config->block->closed) ? $this->config->block->closed : '';

            $this->view->blockPairs  = $this->block->getClosedBlockPairs($closedBlock);
            $this->view->closedBlock = $closedBlock;
        }
        if($module == 'user' and $field == 'deleted')
        {
            $this->app->loadConfig('user');
            $this->view->showDeleted = isset($this->config->user->showDeleted) ? $this->config->user->showDeleted : '0';
        }
        if(($module == 'review' and $field == 'reviewerList') || ($module == 'demandcollection' and ($field == 'writerList' || $field == 'viewerList' || $field == 'copyForList')))
        {
            if($field == 'reviewerList'){
                $this->view->reviewerList = $this->lang->review->reviewerList;
            }elseif($field == 'writerList'){
                $this->view->reviewerList = $this->lang->demandcollection->writerList;
            }elseif($field == 'copyForList'){
                $this->view->reviewerList = $this->lang->demandcollection->copyForList;
            }else{
                $this->view->reviewerList = $this->lang->demandcollection->viewerList;
            }

            $this->view->users     = $this->loadModel('user')->getPairs('noclosed');
        }
        if($module == 'review' and $field == 'startTimeOut')
        {
            $this->loadModel('review');
            $this->view->typeList  = $this->lang->review->typeList;
        }
        if($module == 'review' and $field == 'singleReviewDeal')
        {
            $this->loadModel('review');
            $this->view->users = $this->loadModel('user')->getPairs('noclosed|nodeleted');
        }
        if($module == 'review' and $field == 'manageReviewDefExperts'){ //管理评审默认内部专家
            $this->loadModel('review');
            $this->view->users = $this->loadModel('user')->getPairs('noclosed|nodeleted');
        }

        if($module == 'review' and $field == 'fileSize')
        {
            $this->app->loadConfig('review');
            $this->view->fileSize = $this->config->review->fileSize->fileSize;
        }
        if($module == 'review' and $field == 'reviewConsumed')
        {
            $this->app->loadConfig('review');
           // $this->view->reviewConsumed = $this->config->review->reviewConsumed->reviewConsumed;
        }
        if($module == 'review' and $field == 'endDates')
        {
            $this->view->statusList = $this->lang->review->endDateList;
        }
        if($module == 'review' &&  in_array($field, $this->lang->custom->shanghai->reviewUser))
        {
            $this->loadModel('review');
            $this->view->users = $this->loadModel('user')->getPairs('noclosed');
            $this->view->reviewTypeList  = $this->lang->review->typeList;
        }
        if($module == 'reviewqz' and $field == 'liasisonOfficer')
        {
            $this->loadModel('reviewqz');
            $this->view->users = $this->loadModel('user')->getPairs('noclosed');
        }
        if($module == 'modify' and $field == 'branchManagerList')
        {
            $this->loadModel('modify');

            $this->view->users = $this->loadModel('user')->getPairs('noclosed');
        }
        if($module == 'osspchange' and $field == 'interfacePerson')
        {
            $this->loadModel('osspchange');
            $this->view->users = $this->loadModel('user')->getPairs('noclosed');
        }
        if($module == 'closingitem' and ($field == 'assemblyPerson' || $field == 'toolsPerson' || $field == 'knowledgePerson' || $field == 'preResearchPerson'))
        {
            $this->loadModel('closingitem');
            $this->view->users = $this->loadModel('user')->getPairs('noclosed');
        }
        // 二线工单抄送部门
        if($module == 'secondorder' and $field == 'ccDeptList')
        {
            $this->loadModel('secondorder');
            // 部门数据。
            $deptList      = $this->dao->select('id,name')->from(TABLE_DEPT)->fetchPairs();;
            $this->view->deptList      = $deptList;
            $this->view->ccDeptList = $this->config->secondorder->ccDeptList;
            $this->view->users = $this->loadModel('user')->getPairs('noclosed');
        }
        if($module == 'putproduction' and $field == 'syncFailList')
        {
            $this->loadModel('putproduction');
            $this->view->users = $this->loadModel('user')->getPairs('noclosed');
            $this->view->syncFailList = $this->config->putproduction->syncFailList;
        }
        if($module == 'requirement' && $field == 'overDateInfoVisible'){ //管理评审默认内部专家
            $this->loadModel('requirement');
            $this->view->users = $this->loadModel('user')->getPairs('noclosed|nodeleted');
        }

        // 需求池问题池抄送部门负责人
        if($module == 'demand' and $field == 'deptLeadersList')
        {
            $this->loadModel('demand');
            // 部门数据。
            $deptList      = $this->dao->select('id,name')->from(TABLE_DEPT)->fetchPairs();
            $this->view->deptList      = $deptList;
            $this->view->deptLeadersList = $this->config->demand->deptLeadersList;
            $this->view->users = $this->loadModel('user')->getPairs('noclosed');
        }
        if($module == 'demand' && $field == 'overDateInfoVisible'){ //管理评审默认内部专家
            $this->loadModel('demand');
            $this->view->users = $this->loadModel('user')->getPairs('noclosed|nodeleted');
        }
        // bug模块-允许部门回填
        if($module == 'bug' and $field == 'allowDeptList')
        {
            $this->loadModel('bug');
            // 部门数据。
            $deptList      = $this->dao->select('id,name')->from(TABLE_DEPT)->fetchPairs();;
            $this->view->deptList      = $deptList;
            $this->view->allowDeptList = $this->config->bug->allowDeptList;
        }
        if($module == 'credit' and $field == 'confirmResultUsers'){ //管理评审默认内部专家
            $this->loadModel('credit');
            $this->view->users = $this->loadModel('user')->getPairs('noclosed|nodeleted');
        }
        if($module == 'demandcollection' and $field == 'belongPlatform'){ //所属平台
            $this->loadModel('demandcollection');
            $this->view->users = $this->loadModel('user')->getPairs('noclosed|nodeleted');
        }

        if(strtolower($this->server->request_method) == "post")
        {
            if($module == 'project' and $field == 'unitList')
            {
                $data = fixer::input('post')->join('unitList', ',')->get();
                if(empty($data->unitList)) $this->send(array('result' => 'fail', 'message' => $this->lang->custom->currencyNotEmpty));
                if(empty($data->defaultCurrency)) $this->send(array('result' => 'fail', 'message' => $this->lang->custom->defaultNotEmpty));
                $this->loadModel('setting')->setItems("system.$module", $data);
            }
            elseif($module == 'story' and $field == 'review')
            {
                $data = fixer::input('post')->join('forceReview', ',')->get();
                $this->loadModel('setting')->setItems("system.$module", $data);
            }
            elseif($module == 'testcase' and $field == 'review')
            {
                $review = fixer::input('post')->get();
                if($review->needReview) $data = fixer::input('post')->join('forceNotReview', ',')->remove('forceReview')->get();
                if(!$review->needReview) $data = fixer::input('post')->join('forceReview', ',')->remove('forceNotReview')->get();
                $this->loadModel('setting')->setItems("system.$module", $data);
            }
            elseif($module == 'task' and $field == 'hours')
            {
                $this->loadModel('setting')->setItems('system.execution', fixer::input('post')->get());
            }
            elseif($module == 'task' and $field == 'workThreshold')
            {
                $this->loadModel('setting')->setItems('system.task', fixer::input('post')->get());
            }
            elseif($module == 'task' and $field == 'workBuffer')
            {
                $this->loadModel('setting')->setItems('system.task', fixer::input('post')->get());
            }
            elseif($module == 'task' and $field == 'stageList')
            {
                $this->custom->deleteItems("lang=all&module=$module&section=$field");
                $this->custom->deleteItems("lang=all&module=$module&section=stageSecondList");
                $data = fixer::input('post')->get();
                foreach($data->keys as $index => $key)
                {
                    $value  = $data->values[$index];
                    $system = $data->systems[$index];
                    $this->custom->setItem("zh-cn.{$module}.{$field}.{$key}.{$system}", $value);
                }
            }
            elseif($module == 'task' and $field == 'stageSecondList')
            {
                $this->custom->deleteItems("lang=all&module=$module&section=$field");
                $this->custom->deleteItems("lang=all&module=$module&section=stageSecondList");
                $data = fixer::input('post')->get();
                foreach($data->keys as $index => $key)
                {
                    $value  = $data->values[$index];
                    $system = $data->systems[$index];
                    $this->custom->setItem("zh-cn.{$module}.{$field}.{$key}.{$system}", $value);
                }
            }
            elseif($module == 'task' and $field == 'threeTaskList')
            {
                $this->custom->deleteItems("lang=all&module=$module&section=$field");
                $this->custom->deleteItems("lang=all&module=$module&section=threeTaskList");
                $data = fixer::input('post')->get();
                foreach($data->keys as $index => $key)
                {
                    $value  = $data->values[$index];
                    $system = $data->systems[$index];
                    $this->custom->setItem("zh-cn.{$module}.{$field}.{$key}.{$system}", $value);
                }
            }
            elseif($module == 'workreport' and $field == 'leaderList')
            {
                $this->custom->deleteItems("lang=all&module=$module&section=$field");
                $this->custom->deleteItems("lang=all&module=$module&section=leaderList");
                $data = fixer::input('post')->get();
                foreach($data->keys as $index => $key)
                {
                    $value  = implode(',',$data->values);
                    $system = $data->systems[$index];
                    $this->custom->setItem("zh-cn.{$module}.{$field}.{$key}.{$system}", $value);
                }
            }
            elseif($module == 'workreport' and $field == 'deptList')
            {
                $this->custom->deleteItems("lang=all&module=$module&section=$field");
                $this->custom->deleteItems("lang=all&module=$module&section=deptList");
                $data = fixer::input('post')->get();
                foreach($data->keys as $index => $key)
                {
                    $value  = implode(',',$data->values);
                    $system = $data->systems[$index];
                    $this->custom->setItem("zh-cn.{$module}.{$field}.{$key}.{$system}", $value);
                }
            }
            elseif($module == 'requestlog' and $field == 'userList')
            {
                $this->custom->deleteItems("lang=all&module=$module&section=$field");
                $this->custom->deleteItems("lang=all&module=$module&section=userList");
                $data = fixer::input('post')->get();
                foreach($data->keys as $index => $key)
                {
                    $value  = implode(',',$data->values);
                    $system = $data->systems[$index];
                    $this->custom->setItem("zh-cn.{$module}.{$field}.{$key}.{$system}", $value);
                }
            }
            elseif($module == 'build' and $field == 'leaderList')
            {
                $this->custom->deleteItems("lang=all&module=$module&section=$field");
                $this->custom->deleteItems("lang=all&module=$module&section=leaderList");
                $data = fixer::input('post')->get();
                foreach($data->keys as $index => $key)
                {
                    $value  = implode(',',$data->values);
                    $system = $data->systems[$index];
                    $this->custom->setItem("zh-cn.{$module}.{$field}.{$key}.{$system}", $value);
                }
            }
            elseif($module == 'problem' and $field == 'closePersonList')
            {
                $this->custom->deleteItems("lang=all&module=$module&section=$field");
                $this->custom->deleteItems("lang=all&module=$module&section=closePersonList");
                $data = fixer::input('post')->get();
                foreach($data->keys as $index => $key)
                {
                    $value  = isset($data->values[$key]) ? implode(',',$data->values[$key]) : '';
                    $system = $data->systems[$index];
                    $this->custom->setItem("zh-cn.{$module}.{$field}.{$key}.{$system}", $value);
                }
            }
            elseif($module == 'problem' and $field == 'deptLeadersList')
            {
                $data = fixer::input('post')->get();
                foreach ($data->deptLeadersList as $key=>$value){
                    $this->loadModel('setting')->setItem('system.problem.deptLeadersList.'.$key, implode(",",$value));
                }
            }
            elseif($module == 'problem' and $field == 'examinationResultUpdateList')
            {
                $this->custom->deleteItems("lang=all&module=$module&section=$field");
                $this->custom->deleteItems("lang=all&module=$module&section=examinationResultUpdateList");
                $data = fixer::input('post')->get();
                foreach($data->keys as $index => $key)
                {
                    $value  = implode(',',$data->values);
                    $system = $data->systems[$index];
                    $this->custom->setItem("zh-cn.{$module}.{$field}.{$key}.{$system}", $value);
                }
            }
            elseif($module == 'projectplan' and $field == 'shProductAndarchList')
            {
                $this->custom->deleteItems("lang=all&module=$module&section=$field");
                $this->custom->deleteItems("lang=all&module=$module&section=shProductAndarchList");
                $data = fixer::input('post')->get();
                foreach($data->keys as $index => $key)
                {
                    $value  = isset($data->values[$key]) ? implode(',',$data->values[$key]) : '';
                    $system = $data->systems[$index];
                    $this->custom->setItem("zh-cn.{$module}.{$field}.{$key}.{$system}", $value);
                }
            }
            elseif($module == 'projectplan' and $field == 'shProjectPlanDeptList')
            {
                $this->custom->deleteItems("lang=all&module=$module&section=$field");
                $this->custom->deleteItems("lang=all&module=$module&section=shProjectPlanDeptList");
                $data = fixer::input('post')->get();
                foreach($data->keys as $index => $key)
                {
                    $value  = isset($data->values) ? implode(',',$data->values) :'';
                    $system = $data->systems[$index];
                    $this->custom->setItem("zh-cn.{$module}.{$field}.{$key}.{$system}", $value);
                }
            }
            elseif($module == 'project' and $field == 'setShWhiteList')
            {
                $this->custom->deleteItems("lang=all&module=$module&section=$field");
                $this->custom->deleteItems("lang=all&module=$module&section=setShWhiteList");
                $data = fixer::input('post')->get();
                foreach($data->keys as $index => $key)
                {
                    $value  = implode(',',$data->values);
                    $system = $data->systems[$index];
                    $this->custom->setItem("zh-cn.{$module}.{$field}.{$key}.{$system}", $value);
                }
            }
            elseif($module == 'project' and $field == 'projectSetList')
            {
                $this->custom->deleteItems("lang=all&module=$module&section=$field");
                $this->custom->deleteItems("lang=all&module=$module&section=projectSetList");
                $data = fixer::input('post')->get();
                foreach($data->keys as $index => $key)
                {
                    $value  = implode(',',$data->values);
                    $system = $data->systems[$index];
                    $this->custom->setItem("zh-cn.{$module}.{$field}.{$key}.{$system}", $value);
                }
            }
            elseif($module == 'issue' and $field == 'leaderList')
            {
                $this->custom->deleteItems("lang=all&module=$module&section=$field");
                $this->custom->deleteItems("lang=all&module=$module&section=leaderList");
                $data = fixer::input('post')->get();
                foreach($data->keys as $index => $key)
                {
                    $value  = implode(',',$data->values);
                    $system = $data->systems[$index];
                    $this->custom->setItem("zh-cn.{$module}.{$field}.{$key}.{$system}", $value);
                }
            }
            elseif($module == 'issue' and $field == 'assignToList')
            {
                $this->custom->deleteItems("lang=all&module=$module&section=$field");
                $this->custom->deleteItems("lang=all&module=$module&section=assignToList");
                $data = fixer::input('post')->get();
                foreach($data->keys as $index => $key)
                {
                    $value  = implode(',',$data->values);
                    $system = $data->systems[$index];
                    $this->custom->setItem("zh-cn.{$module}.{$field}.{$key}.{$system}", $value);
                }
            }
            elseif($module == 'issue' and $field == 'frameworkToList')
            {
                $this->custom->deleteItems("lang=all&module=$module&section=$field");
                $this->custom->deleteItems("lang=all&module=$module&section=frameworkToList");
                $data = fixer::input('post')->get();
                foreach($data->keys as $index => $key)
                {
                    $value  = implode(',',$data->values);
                    $system = $data->systems[$index];
                    $this->custom->setItem("zh-cn.{$module}.{$field}.{$key}.{$system}", $value);
                }
            } elseif($module == 'localesupport' && ($field == 'projectList')){
                $this->custom->deleteItems("lang=all&module=$module&section=$field");
                $this->custom->deleteItems("lang=all&module=$module&section=projectList");
                $this->custom->deleteItems("lang=zh-cn&module=$module&section=projectList");
                $data = fixer::input('post')->get();

                foreach($data->keys as $index => $key)
                {
                    $value  = $data->projectList[$index];
                    $system = $data->systems[$index] == 0 ? 1 :$data->systems[$index];
                    $this->custom->setItem("zh-cn.{$module}.{$field}.{$key}.{$system}", $value);
                }
            } elseif($module == 'localesupport' && in_array($field, ['limitDaySwitch', 'reportWorkLimitDay']))
            {
                $this->loadModel('setting')->setItems('system.localesupport', fixer::input('post')->get());
            }
            elseif($module == 'bug' and $field == 'longlife')
            {
                $this->loadModel('setting')->setItems('system.bug', fixer::input('post')->get());
            }
            elseif($module == 'block' and $field == 'closed')
            {
                $data = fixer::input('post')->join('closed', ',')->get();
                $this->loadModel('setting')->setItem('system.block.closed', zget($data, 'closed', ''));
            }
            elseif($module == 'review' and $field == 'startTimeOut') {
                $data = fixer::input('post')->join('startTimeOut', ',')->get();
                $types ='';
                foreach ($data->typeList as $type){
                   if(!empty($type)){
                       $types.=$type.',';
                   }
                }
                $this->loadModel('setting')->setItem('system.review.startTimeOut', $types);
            }
            elseif($module == 'review' and $field == 'singleReviewDeal')
            {
                $data = fixer::input('post')->join('singleReviewDeal', ',')->get();
                $types ='';
                foreach ($data->singleReviewDeal as $type){
                    if(!empty($type)){
                        $types.=$type.',';
                    }
                }
                $this->loadModel('setting')->setItem('system.review.singleReviewDeal', $data->singleReviewDeal);
            }

            elseif($module == 'qualitygate'  && $field == 'allowQualityGateDeptIds')
            {
                $data = fixer::input('post')->join('allowQualityGateDeptIds', ',')->get();
                $this->loadModel('setting')->setItem('system.qualitygate.allowQualityGateDeptIds', $data->allowQualityGateDeptIds);
            }

            elseif($module == 'review' and $field == 'manageReviewDefExperts') { //管理评审默认内部专家
                $data = fixer::input('post')->get();
                $manageReviewDefExperts = '';
                if(isset($data->manageReviewDefExperts)){
                    $manageReviewDefExperts = implode(',', array_filter($data->manageReviewDefExperts));
                }
                $this->loadModel('setting')->setItem('system.review.manageReviewDefExperts', $manageReviewDefExperts);
            }

            elseif($module == 'reviewqz' and $field == 'liasisonOfficer') { //评审接口人
                $data = fixer::input('post')->join('liasisonOfficer', ',')->get();
                $liasisonOfficers ='';
                if($data->liasisonOfficerList){
                    $liasisonOfficerList = array_filter($data->liasisonOfficerList);
                    $liasisonOfficers = implode(',', $liasisonOfficerList);
                }
                $this->loadModel('setting')->setItem('system.reviewqz.liasisonOfficer', $liasisonOfficers);
            }
            elseif($module == 'modify' and $field == 'branchManagerList') { //评审接口人
                $data = fixer::input('post')->join('branchManagerList', ',')->get();
                $liasisonOfficers ='';
                if($data->branchManagerList){
                    $liasisonOfficers = trim($data->branchManagerList,',');
                }
                $this->loadModel('setting')->setItem('system.modify.branchManagerList', $liasisonOfficers);
            }
            elseif($module == 'osspchange' and $field == 'interfacePerson') { //体系接口人
                $data = fixer::input('post')->join('interfacePerson', ',')->get();
                $interfacePersons ='';
                if($data->interfacePersonList){
                    $interfacePersonList = array_filter($data->interfacePersonList);
                    $interfacePersons = implode(',', $interfacePersonList);
                }
                $this->loadModel('setting')->setItem('system.osspchange.interfacePerson', $interfacePersons);
            }
            elseif($module == 'closingitem' and $field == 'assemblyPerson') { //公共技术组件归口人
                $data = fixer::input('post')->join('assemblyPerson', ',')->get();
                $assemblyPersons ='';
                if($data->assemblyPersonList){
                    $assemblyPersonList = array_filter($data->assemblyPersonList);
                    $assemblyPersons = implode(',', $assemblyPersonList);
                }
                $this->loadModel('setting')->setItem('system.closingitem.assemblyPerson', $assemblyPersons);
            }
            elseif($module == 'closingitem' and $field == 'toolsPerson') { //测试工具归口人
                $data = fixer::input('post')->join('toolsPerson', ',')->get();
                $toolsPersons ='';
                if($data->toolsPersonList){
                    $toolsPersonList = array_filter($data->toolsPersonList);
                    $toolsPersons = implode(',', $toolsPersonList);
                }
                $this->loadModel('setting')->setItem('system.closingitem.toolsPerson', $toolsPersons);
            }
            elseif($module == 'closingitem' and $field == 'knowledgePerson') { //知识库归口人
                $data = fixer::input('post')->join('knowledgePerson', ',')->get();
                $knowledgePersons ='';
                if($data->knowledgePersonList){
                    $knowledgePersonList = array_filter($data->knowledgePersonList);
                    $knowledgePersons = implode(',', $knowledgePersonList);
                }
                $this->loadModel('setting')->setItem('system.closingitem.knowledgePerson', $knowledgePersons);
            }
            elseif($module == 'closingitem' and $field == 'preResearchPerson') { //预研类归口人
                $data = fixer::input('post')->join('preResearchPerson', ',')->get();
                $preResearchPersons ='';
                if($data->preResearchPersonList){
                    $preResearchPersonList = array_filter($data->preResearchPersonList);
                    $preResearchPersons = implode(',', $preResearchPersonList);
                }
                $this->loadModel('setting')->setItem('system.closingitem.preResearchPerson', $preResearchPersons);
            }
            elseif($module == 'user' and $field == 'contactField')
            {
                $data = fixer::input('post')->join('contactField', ',')->get();
                if(!isset($data->contactField)) $data->contactField = '';
                $this->loadModel('setting')->setItem('system.user.contactField', $data->contactField);
            }
            elseif($module == 'user' and $field == 'deleted')
            {
                $data = fixer::input('post')->get();
                $this->loadModel('setting')->setItem('system.user.showDeleted', $data->showDeleted);
            }
            elseif($module == 'opinion' and $field == 'groupList')
            {
                $this->custom->deleteItems("lang=all&module=$module&section=$field");
                $this->custom->deleteItems("lang=all&module=$module&section=ownerList");
                $data = fixer::input('post')->get();
                foreach($data->keys as $index => $key)
                {
                    $value  = $data->values[$index];
                    $owner  = $data->ownerList[$index];
                    $system = $data->systems[$index];
                    $this->custom->setItem("all.{$module}.{$field}.{$key}.{$system}", $value);
                    if($owner) $this->custom->setItem("all.{$module}.ownerList.{$key}.0", $owner);
                }
            }
            elseif(($module == 'review' and $field == 'reviewerList') || ($module == "demandcollection"  and ($field == 'writerList' ||$field == 'viewerList' || $field == 'copyForList')))
            {
                $this->custom->deleteItems("lang=zh-cn&module=$module&section=$field");
                //$this->custom->deleteItems("lang=zh-cn&module=$module&section=reviewerList");
                $data = fixer::input('post')->get();
                $lang = $data->lang;
                foreach($data->keys as $index => $key)
                {
                    if($field == "reviewerList"){
                        $owner  = $data->reviewerList[$index];
                        $v = $data->reviewerList;
                    }elseif($field == "writerList"){
                        $owner  = $data->writerList[$index];
                        $v = $data->writerList;
                    }elseif($field == "copyForList"){
                        $owner  = $data->copyForList[$index];
                        $v = $data->copyForList;
                    }else{
                        $owner  = $data->viewerList[$index];
                        $v = $data->viewerList;
                    }
                    $system = $data->systems[$index];
                    $k = $data->keys;
                   /* if($k[$index] || $v[$index]){
                         $this->send(array('result' => 'fail', 'message' => sprintf("%s不能为空!",'')));
                    }*/
                    if(!empty($k[$index]) and  $k[$index] != 'n/a' and !validater::checkREG($key, '/^[a-zA-Z_0-9]+$/')) $this->send(array('result' => 'fail', 'message' => $this->lang->custom->notice->invalidStringKey));
                    if(strlen($k[$index]) > 30 || strlen($system) > 30) $this->send(array('result' => 'fail', 'message' => $this->lang->custom->notice->invalidStrlen['thirty']));
                    $this->custom->setItem("{$lang}.{$module}.{$field}.{$key}.{$system}");
                    if($field == "reviewerList"){
                        if($owner) $this->custom->setItem("{$lang}.{$module}.reviewerList.{$key}.0", $owner);
                    }elseif($field == "writerList") {
                        if ($owner) $this->custom->setItem("{$lang}.{$module}.writerList.{$key}.0", $owner);
                    }elseif($field == "copyForList") {
                        if ($owner) $this->custom->setItem("{$lang}.{$module}.copyForList.{$key}.0", $owner);
                    }else{
                        if ($owner) $this->custom->setItem("{$lang}.{$module}.viewerList.{$key}.0", $owner);
                    }
                }
            }
            elseif($module == 'review' and $field == 'endDates')
            {
                $this->custom->deleteItems("lang=zh-cn&module=$module&section=$field");
                $this->custom->deleteItems("lang=zh-cn&module=$module&section=endDates");
                $data = fixer::input('post')->get();
                $lang = $data->lang;
                foreach($data->keys as $index => $key)
                {
                    $value  = $data->endDates[$index];
                    $owner  = $data->endDates[$index];
                    $system = $data->systems[$index];
                    $k = $data->keys;
                    $v = $data->endDates;
                    if(!empty($k[$index]) and  $k[$index] != 'n/a' and !validater::checkREG($key, '/^[a-zA-Z_0-9]+$/')) $this->send(array('result' => 'fail', 'message' => $this->lang->custom->notice->invalidStringKey));
                    //if(strlen($k[$index]) > 30 || strlen($system) > 30) $this->send(array('result' => 'fail', 'message' => $this->lang->custom->notice->invalidStrlen['thirty']));
                    $this->custom->setItem("{$lang}.{$module}.{$field}.{$key}.{$system}", $value);
                    if($owner) $this->custom->setItem("{$lang}.{$module}.endDates.{$key}.0", $owner);
                }
            }
            elseif($module == 'review' and $field == 'fileSize')
            {
                $this->loadModel('setting')->setItems('system.review.fileSize', fixer::input('post')->get());
            }
            elseif($module == 'review' and $field == 'reviewConsumed')
            {
                $this->loadModel('setting')->setItems('system.review.reviewConsumed', fixer::input('post')->get());
            }
            elseif($module == 'residentsupport' and $field == 'secondReviews')
            {
                $data = fixer::input('post')->join('secondReviews', ',')->get();
                if(!isset($data->secondReviews)) $data->secondReviews = '';
                $this->loadModel('setting')->setItem('system.residentsupport.secondReviews', $data->secondReviews);
            }
            elseif($module == 'residentsupport' and $field == 'schedulingIntervalDay')
            {
                $data = fixer::input('post')->get();
                if(!isset($data->schedulingIntervalDay)) $data->schedulingIntervalDay = '';
                $this->loadModel('setting')->setItem('system.residentsupport.schedulingIntervalDay', $data->schedulingIntervalDay);
            }
            elseif($module == 'secondorder' and $field == 'ccDeptList')
            {
                $data = fixer::input('post')->get();
                foreach ($data->ccDeptList as $key=>$value){
                    $this->loadModel('setting')->setItem('system.secondorder.ccDeptList.'.$key, implode(",",$value));
                }
            }
            elseif($module == 'putproduction' and $field == 'syncFailList')
            {
                $data = fixer::input('post')->get();
                $this->loadModel('setting')->setItem('system.putproduction.syncFailList', implode(",",$data->syncFailList));
            }

            elseif($module == 'requirement' && $field == 'overDateInfoVisible') { //需求任务超期查看权限
                $data = fixer::input('post')->get();
                $overDateInfoVisible = '';
                if(isset($data->overDateInfoVisible)){
                    $overDateInfoVisible = implode(',', array_filter($data->overDateInfoVisible));
                }
                $this->loadModel('setting')->setItem('system.requirement.overDateInfoVisible', $overDateInfoVisible);
            }
            elseif($module == 'demand' and $field == 'deptLeadersList')
            {
                $data = fixer::input('post')->get();
                foreach ($data->deptLeadersList as $key=>$value){
                    $this->loadModel('setting')->setItem('system.demand.deptLeadersList.'.$key, implode(",",$value));
                }
            }
            elseif($module == 'demand' && $field == 'overDateInfoVisible') { //需求条目超期查看权限
                $data = fixer::input('post')->get();
                $overDateInfoVisible = '';
                if(isset($data->overDateInfoVisible)){
                    $overDateInfoVisible = implode(',', array_filter($data->overDateInfoVisible));
                }
                $this->loadModel('setting')->setItem('system.demand.overDateInfoVisible', $overDateInfoVisible);
            }

            elseif($module == 'bug' and $field == 'allowDeptList')
            {
                $data = fixer::input('post')->get();
                $this->loadModel('setting')->setItem('system.bug.allowDeptList', implode(",",$data->allowDeptList));
            }
            elseif(($module == 'residentsupport' and $field == 'setCcList') || ($module == 'project' and $field == 'setWhiteList'))
            {
                $data = fixer::input('post')->get();
                $info = [];
                $info['lang'] = $data->lang;
                $list = [];
                foreach($data->keys as $index => $key)
                {
                    $info['keys'] = $key;
                    $info['systems'] = $data->systems[$index];
                    $val = "values".$key;
                    $info['values'] = implode(',',$data->$val);
                    $list[] = $info;
                }
                $list = json_encode($list);
                 $this->loadModel('setting')->setItem('system.'.$module.'.'.$field, $list);

            } elseif($module == 'credit' and $field == 'confirmResultUsers') { //征信交付确认变更结果用户
                $data = fixer::input('post')->get();
                $confirmResultUsers = '';
                if(isset($data->confirmResultUsers)){
                    $confirmResultUsers = implode(',', array_filter($data->confirmResultUsers));
                }
                $this->loadModel('setting')->setItem('system.credit.confirmResultUsers', $confirmResultUsers);
            }

            else
            {
                $lang = $_POST['lang'];
                $oldCustoms = $this->custom->getItems("lang=$lang&module=$module&section=$field");
                foreach($_POST['keys'] as $index => $key)
                {
                    if(!empty($key)) $key = trim($key);
                    /* Invalid key. It should be numbers. (It includes severityList in bug module and priList in stroy, task, bug, testcasea, testtask and todo module.) */
                    if( $field == 'priList' or $field == 'severityList')
                    {
                        if(!empty($key) && (!is_numeric($key) or $key > 255)) $this->send(array('result' => 'fail', 'message' => $this->lang->custom->notice->invalidNumberKey));
                    }
                    if(!empty($key) and !isset($oldCustoms[$key]) and $key != 'n/a' and !validater::checkREG($key, '/^[a-zA-Z_0-9]+$/')) $this->send(array('result' => 'fail', 'message' => $this->lang->custom->notice->invalidStringKey));

                    /* The length of roleList in user module and typeList in todo module is less than 10. check it when saved. */
                    if($field == 'roleList' or $module == 'todo' and $field == 'typeList')
                    {
                        if(strlen($key) > 10) $this->send(array('result' => 'fail', 'message' => $this->lang->custom->notice->invalidStrlen['ten']));
                    }

                    /* The length of sourceList in story module and typeList in task module is less than 20, check it when saved. */
                    if($field == 'sourceList' or $module == 'task' and $field == 'typeList')
                    {
                        if(strlen($key) > 20) $this->send(array('result' => 'fail', 'message' => $this->lang->custom->notice->invalidStrlen['twenty']));
                    }

                    /* The length of stageList in testcase module is less than 255, check it when saved. */
                    if($module == 'testcase' and $field == 'stageList' and strlen($key) > 255) $this->send(array('result' => 'fail', 'message' => $this->lang->custom->notice->invalidStrlen['twoHundred']));

                    /* The length of field that in bug and testcase module and reasonList in story and task module is less than 30, check it when saved. */
                    if($module == 'bug' or $field == 'reasonList' or $module == 'testcase')
                    {
                        if(strlen($key) > 30) $this->send(array('result' => 'fail', 'message' => $this->lang->custom->notice->invalidStrlen['thirty']));
                    }
                    if($field == 'review' or $field == 'objectList')
                    {
                        $k = $_POST['keys'];
                        if(strlen($k[$index]) > 20) $this->send(array('result' => 'fail', 'message' => $this->lang->custom->notice->invalidStrlen['twenty']));
                    }
                    if($field == 'review' or $field == 'typeList')
                    {
                        $k = $_POST['keys'];
                        if(strlen($k[$index]) > 20) $this->send(array('result' => 'fail', 'message' => $this->lang->custom->notice->invalidStrlen['twenty']));
                    }
                    if($field == 'review' or $field == 'gradeList')
                    {
                        $k = $_POST['keys'];
                        if(strlen($k[$index]) > 20) $this->send(array('result' => 'fail', 'message' => $this->lang->custom->notice->invalidStrlen['twenty']));
                    }
                    if($field == 'review' or $field == 'emilAlert')
                    {
                        $k = $_POST['keys'];
                        if(strlen($k[$index]) > 20) $this->send(array('result' => 'fail', 'message' => $this->lang->custom->notice->invalidStrlen['twenty']));
                    }
                    if($field == 'review' or $field == 'timeOut')
                    {
                        $k = $_POST['keys'];
                        if(strlen($k[$index]) > 20) $this->send(array('result' => 'fail', 'message' => $this->lang->custom->notice->invalidStrlen['twenty']));
                    }
                }

                $this->custom->deleteItems("lang=$lang&module=$module&section=$field");
                $data = fixer::input('post')->get();
                foreach($data->keys as $index => $key)
                {
                    //if(!$system and (!$value or !$key)) continue; //Fix bug #951.

                    $value  = $data->values[$index];
                    $system = $data->systems[$index];
                    if($field == 'review' || $field == 'objectList' || $field == 'navOrderList'){
                        $orders = isset($data->orders[$index]) ? $data->orders[$index] : 0;
                        $this->custom->setItem("{$lang}.{$module}.{$field}.{$key}.{$system}.{$orders}", $value);
                    }elseif ($module == 'demandcollection' && $field == 'belongPlatform'){ //需求搜集的所属平台配置
                        $orders = isset($data->orders[$index]) ? $data->orders[$index] : 0;
                        $productmanager = isset($data->productmanager[$index]) ? $data->productmanager[$index] : '';
                        $extendInfo = [
                           'productmanager' => $productmanager,
                        ];
                        $extendInfo = json_encode($extendInfo);
                        $this->custom->setItem("{$lang}.{$module}.{$field}.{$key}.{$system}.{$orders}.{$extendInfo}", $value);
                    }else{
                        $this->custom->setItem("{$lang}.{$module}.{$field}.{$key}.{$system}", $value);
                    }
                }
            }
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $this->createLink('custom', 'set', "module=$module&field=$field&lang=" . str_replace('-', '_', isset($this->config->langs[$lang]) ? $lang : 'all'))));
        }

        /* Check whether the current language has been customized. */
        $lang = str_replace('_', '-', $lang);
        $dbFields = $this->custom->getItems("lang=$lang&module=$module&section=$field");
        if(!empty($dbFields)){
            foreach ($dbFields as $key => $val){
                $val->extendInfo = json_decode($val->extendInfo);
            }
        }

        if(empty($dbFields)) $dbFields = $this->custom->getItems("lang=" . ($lang == $currentLang ? 'all' : $currentLang) . "&module=$module&section=$field");

        if($dbFields)
        {
            $dbField = reset($dbFields);
            if($lang != $dbField->lang)
            {
                $lang = str_replace('-', "_", $dbField->lang);
                foreach($fieldList as $key => $value)
                {
                    if(isset($dbFields[$key]) and $value != $dbFields[$key]->value) $fieldList[$key] = $dbFields[$key]->value;
                }
            }
        }

        $this->view->title       = $this->lang->custom->common . $this->lang->colon . $this->lang->$module->common;
        $this->view->position[]  = $this->lang->custom->common;
        $this->view->position[]  = $this->lang->$module->common;
        $this->view->fieldList   = $fieldList;
        $this->view->dbFields    = $dbFields;
        $this->view->field       = $field;
        $this->view->lang2Set     = str_replace('_', '-', $lang);
        $this->view->module      = $module;
        $this->view->currentLang = $currentLang;
        $this->view->canAdd      = strpos($this->config->custom->canAdd[$module], $field) !== false;

        $this->display();
    }
}
