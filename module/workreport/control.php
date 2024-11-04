<?php

class workreport extends control
{

    /**
     * 报工列表
     */
    public function browse($browseType = 'all',$param = 0, $recTotal = 0, $recPerPage = 20, $pageID = 1, $begin = null ,$end = null){

        $this->app->loadLang('todo');
        $browseType = strtolower($browseType);
        if($this->app->user->account == 'admin'){
            $projects = array(''=>'') + $this->workreport->getProjectTeam('','all');//所有有权限的项目
        }else{
            $projects = array(''=>'') + $this->workreport->getProjectTeam('','browse');//所有有权限的项目
        }


        /* By search. */
        $queryID = ($browseType == 'bysearch')  ? (int)$param : 0;
        $actionURL = $this->createLink('workreport', 'browse', "browseType=bySearch&param=myQueryID");

        $this->app->loadLang('task');
        $this->config->workreport->search['params']['project']['values']  = $projects;
        $this->config->workreport->search['params']['workType']['values'] =  $this->lang->task->typeList;;

        $this->workreport->buildSearchForm($queryID, $actionURL);


        $onestages = array(''=>'') + $this->loadModel('project')->getProjectOneStage('','browse');//所属活动
        $stages = array(''=>'') + $this->loadModel('project')->getProjectTwoStage();//所属应用系统
        $task = array(''=>'') + $this->loadModel('task')->getProjectAllTask('all');//所属任务
        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);
        $workReport = $this->workreport->getList($browseType, $queryID, $pager, $begin, $end);
        $this->view->workReport = $workReport;

        $this->view->projects = $projects;
        $this->view->stages = $onestages;
        $this->view->apps   = $stages;
        $this->view->tasks  = $task;
        $this->view->param      = $param;
        $this->view->browseType = $browseType;
        $this->view->allYear    = $this->workreport->getAllYear();
        $this->view->title      = $this->lang->workreport->common;
        $this->view->pager      = $pager;
        $this->view->workreportYear = $this->session->workreportYear;
        $this->view->begin        = $browseType == 'date' ? date('Y-m-d',strtotime($begin)) : '';
        $this->view->end        = $browseType == 'date' ? date('Y-m-d',strtotime($end)) : '';
        $this->display();
    }

    /**
     *
     * 创建报工
     * @access public
     * @return void
     */
    public function create()
    {
        if($_POST)
        {

            $workIDS = $this->workreport->create();
            if(dao::$errors)
            {
                $response['result']  = 'fail';
                $response['message'] = dao::$errors;
                $this->send($response);
            }
            foreach ($workIDS as $workID) {
                $this->loadModel('action')->create('workreport', $workID, 'created', $this->post->comment);
            }
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            //$response['locate']  =  $this->createLink('workreport','browse','','',false).'#app=my';
            $response['closeModal'] = true;
            //$response['callback'] = "back()";
            $response['callback'] = "parent.back()";
            $this->send($response);
        }
        $this->app->loadLang('task');
        $projects = array(''=>'') + $this->workreport->getProjectTeam('','new',1);//所有有权限的项目
        $this->view->projects = $projects;
        $this->view->activity = ''; //所属活动
        $this->view->apps     = ''; //所属阶段
        $this->view->list = array();//$this->workreport->getLast();//历史报工
        unset($this->lang->task->typeList[array_search('现场支持',$this->lang->task->typeList)]);
        $this->view->workType = $this->lang->task->typeList;
        $this->view->defaultdays = $this->workreport->getWeeklyDays();
        $this->display();
    }

    /**
     * 特批补报
     */
    public function supplementParent(){
        if(common::hasPriv('workreport','supplement')){
            $this->locate($this->createLink($this->moduleName, 'supplement'));
        }else{
           if(isonlybody()) die(js::alert($this->lang->workreport->tip) .js::closeModal('parent','this'));
        }
    }
    /**
     *
     * 特批报工
     * @access public
     * @return void
     */
    public function supplement()
    {
        if($_POST)
        {

            $type = 'suppend';
            $workIDS = $this->workreport->create($type);
            if(dao::$errors)
            {
                $response['result']  = 'fail';
                $response['message'] = dao::$errors;
                $this->send($response);
            }
            foreach ($workIDS as $workID) {
                $this->loadModel('action')->create('workreport', $workID, 'created', $this->post->comment);
            }
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            //$response['locate']  =  $this->createLink('workreport','browse','','',false).'#app=my';
            $response['closeModal'] = true;
            //$response['callback'] = "back()";
            $response['callback'] = "parent.back()";
            $this->send($response);
        }
        $this->app->loadLang('task');
        $haveBeginDate = true;
        $projects = array(''=>'') + $this->workreport->getProjectTeam($haveBeginDate,'suppend',1);//所有有权限的项目

        $this->view->projects = $projects;
        $this->view->activity = ''; //所属活动
        $this->view->apps     = ''; //所属阶段
       // $this->view->list = $this->workreport->getLast();//历史报工
        unset($this->lang->task->typeList[array_search('现场支持',$this->lang->task->typeList)]);
        $this->view->workType = $this->lang->task->typeList;
        $this->view->defaultdays = $this->workreport->getWeeklyDays();
        $this->display();
    }
    /**
     *
     * 编辑报工
     * @access public
     * @return void
     */
    public function edit($workID)
    {
        if($_POST)
        {
            $changes = $this->workreport->update($workID);
            if(dao::$errors)
            {
                $response['result']  = 'fail';
                $response['message'] = dao::$errors;
                $this->send($response);
            }

            $actionID = $this->loadModel('action')->create('workreport', $workID, 'edited', $this->post->comment);
            $this->action->logHistory($actionID, $changes);

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }
        $workreport = $this->workreport->getByID($workID);
        $this->app->loadLang('task');
        $haveBeginDate = true;
        $projects = array(''=>'') + $this->workreport->getProjectTeam($workreport->append ? $haveBeginDate : '',$workreport->append ? 'suppend' :'new',1);//所有有权限的项目
        //$projects = array(''=>'') + $this->workreport->getProjectTeam('','new',1);//所有有权限的项目

        $this->view->workreport = $workreport;
        $this->view->projects = $projects;
        $this->view->activity = ''; //所属活动
        $this->view->apps     = ''; //所属阶段
        unset($this->lang->task->typeList[array_search('现场支持',$this->lang->task->typeList)]);
        $this->view->workType = $this->lang->task->typeList;
        $this->view->actions = $this->loadModel('action')->getList('workreport', $workID);
        $this->view->users   = $this->loadModel('user')->getPairs();
        $this->display();
    }

    /**
     *
     * 纠正报工
     * @access public
     * @return void
     */
    public function correct($workID)
    {
        if($_POST)
        {
            $type ='correct';
            $changes = $this->workreport->update($workID,$type);
            if(dao::$errors)
            {
                $response['result']  = 'fail';
                $response['message'] = dao::$errors;
                $this->send($response);
            }

            $actionID = $this->loadModel('action')->create('workreport', $workID, 'corrected', $this->post->comment);
            $this->action->logHistory($actionID, $changes);

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }
        $workreport = $this->workreport->getByID($workID);
        $this->app->loadLang('task');
        $projects = array(''=>'') + $this->workreport->getProjectTeam('','new','',$workreport->account);//所有有权限的项目
        $this->view->workreport = $workreport;
        $this->view->projects = $projects;
        $this->view->activity = ''; //所属活动
        $this->view->apps     = ''; //所属阶段
        unset($this->lang->task->typeList[array_search('现场支持',$this->lang->task->typeList)]);
        $this->view->workType = $this->lang->task->typeList;
        $this->view->actions = $this->loadModel('action')->getList('workreport', $workID);
        $this->view->users   = $this->loadModel('user')->getPairs();
        $this->view->taskName   = $this->loadModel('task')->getByID($workreport->objects);
        $this->display();
    }

    /**
     * 删除报工
     * @param $workID
     */
    public function delete($workID)
    {
        if(!empty($_POST))
        {
            $work = $this->workreport->getByID($workID);
            if($this->post->comment == '')
            {
                dao::$errors['comment'] = sprintf($this->lang->workreport->empty, $this->lang->workreport->comment);
                if(dao::isError())
                {
                    $response['result']  = 'fail';
                    $response['message'] = dao::$errors;
                    $this->send($response);
                }
            }else {
                if(isset($work) && $work->account != $this->app->user->account && $this->app->user->account != 'admin'){
                    dao::$errors[] = sprintf($this->lang->workreport->noOwnerTip);
                    $response['result']  = 'fail';
                    $response['message'] = dao::$errors;
                    $this->send($response);
                }else{
                    $this->dao->update(TABLE_WORKREPORT)->set('deleted')->eq('1')->where('id')->eq($workID)->exec();
                    $this->dao->update(TABLE_EFFORT)->set('deleted')->eq('1')->where("id =(select id from (select id from zt_effort where workID = '$workID')t1 )")->exec();
                    $this->loadModel('action')->create('workreport', $workID, 'deleted', $this->post->comment);

                    $this->loadModel('task')->computeTask($work->objects);
                    $this->loadModel('task')->computeConsumed($work->objects);
                    $response['result']  = 'success';
                    $response['message'] = $this->lang->saveSuccess;
                    $response['locate']  = 'parent';
                    $this->send($response);
                }

            }
        }

        $workreport = $this->workreport->getByID($workID);
        $this->view->actions = $this->loadModel('action')->getList('workreport', $workID);
        $this->view->workreport = $workreport;
        $this->view->users   = $this->loadModel('user')->getPairs();
        $this->display();
    }

    /**
     * 导出报工数据
     * @param string $browseType
     */
    public function export($browseType = 'all')
    {
        /* format the fields of every secondorder in order to export data. */
        if($_POST)
        {
            $this->app->loadLang('task');
            $this->app->loadLang('todo');
            $this->loadModel('file');
            $workreportLang   = $this->lang->workreport;
            $workreportConfig = $this->config->workreport;

            $orderBy = 'id_desc';
            /* Create field lists. */
            $fields = $this->post->exportFields ? $this->post->exportFields : explode(',', $workreportConfig->list->exportFields);
            foreach($fields as $key => $fieldName)
            {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = isset($workreportLang->$fieldName) ? $workreportLang->$fieldName : $fieldName;
                unset($fields[$key]);
            }
            /* Get secondorders. */
            $workreports = array();

            if($this->session->workreportOnlyCondition)
            {
                $where  =  $this->session->workreportQueryCondition;
                if(strpos($where,"deleted ") !== false ){
                    $where = str_replace('t2.t1.deleted','t2.deleted',str_replace('deleted','t1.deleted',$where)); ;
                }
                if(strpos($where,'objectType') !== false){
                    $where = str_replace('objectType','t1.objectType',$where);
                }
                if(strpos($where,'date') !== false){
                    $where = str_replace('date','t1.date',$where);
                }
                if(strpos($where,'account') !== false){
                    $where = str_replace('account','t1.account',$where);
                }
                $workreports = $this->dao->select("t2.*,t1.consumed as effortConsumed")->from(TABLE_EFFORT)->alias('t1')
                    ->leftJoin(TABLE_WORKREPORT)->alias('t2')
                    ->on('t2.id = t1.workID')
                    ->where($where)
                    ->beginIF($this->post->exportType == 'selected')->andWhere('id')->in($this->cookie->checkedItem)->fi()
                    ->fetchAll('id');
            }
            else
            {
                $stmt = $this->dbh->query($this->session->workreportQueryCondition . ($this->post->exportType == 'selected' ? " AND id IN({$this->cookie->checkedItem})" : '') . " ORDER BY " . strtr($orderBy, '_', ' '));
                while($row = $stmt->fetch()) $workreports[$row->id] = $row;
            }
            $workreportIdList = array_keys($workreports);

            /* Get users, products and executions. */
            $users = $this->loadModel('user')->getPairs('noletter');

            $projects = array(''=>'') + $this->workreport->getProjectTeam();//所有有权限的项目
            $onestages = array(''=>'') + $this->loadModel('project')->getProjectOneStage('','browse');//所属活动
            $stages = array(''=>'') + $this->loadModel('project')->getProjectTwoStage();//所属应用系统
            $task = array(''=>'') + $this->loadModel('task')->getProjectAllTask('all');//所属任务
            foreach($workreports as $workreport)
            {
                $workreport->projectSpace     = zget($projects,$workreport->project);
                $workreport->activity    = zget($onestages,$workreport->activity);
                $workreport->stage        = zget($stages,$workreport->apps);
                $workreport->objects     = zget($task,$workreport->objects);
                $workreport->beginDate   = date('Y-m-d',strtotime($workreport->beginDate));
                $workreport->week        = $this->lang->todo->dayNames[date('w',strtotime($workreport->beginDate))];
                //$workreport->endDate     = $workreport->endDate;
                $workreport->consumed    = $workreport->consumed;
                $workreport->workType    = zget($this->lang->task->typeList,$workreport->workType);
                $workreport->workContent = $workreport->workContent;
              //  $workreport->weeklyNum   =  sprintf($this->lang->workreport->weeklyNumTip, $workreport->weeklyNum) ;
                $workreport->account     = zget($users, $workreport->account, '');
                $workreport->append      = zget($this->lang->workreport->appendList,$workreport->append,'');
            }

            $this->post->set('fields', $fields);
            $this->post->set('rows', $workreports);
            $this->post->set('kind', 'workreport');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        $this->view->fileName        = $this->lang->workreport->exportName;
        $this->view->allExportFields = $this->config->workreport->list->exportFields;
        $this->view->customExport    = true;
        $this->display();
    }

    /**
     * 查询项目所属活动（一级阶段）
     * @param $project
     * @param int $index
     */
    public function ajaxGetActivity($project,$index = 0,$activity = null)
    {
        $project = $project ? $project : 0;
        $stages = array(''=>'') + $this->loadModel('project')->getProjectOneStage($project );

        $where = !empty($activity) ? $activity : '';
        echo html::select('activity['.$index.']', $stages, $where, "class='form-control chosen' data-id = '$index' onchange ='getApps(this)'");
    }
    /**
     * 查询项目所属阶段/应用系统
     * @param $project
     * @param int $index
     */
    public function ajaxGetApps($activity,$index = 0,$app = null)
    {
        $activity = $activity ? $activity : 0;
        $stages =  !empty($activity) ? array(''=>'') +$this->loadModel('project')->getProjectTwoStage($activity) : array(''=>'');
        $where = !empty($app) ? $app : '';
        echo html::select('apps['.$index.']', $stages, $where, "class='form-control chosen' data-id = '$index' onchange ='getTasks(this)'");
    }
    /**
     * 查询项目所属对象
     * @param $project
     * @param int $index
     */
    public function ajaxGetTaskObject($apps,$index = 0,$projectName,$task = null,$flag = null)
    {
        $this->app->loadLang('task');
        $tasks = array(''=>'') + $this->loadModel('task')->getProjectTask($apps,$projectName,$flag);
        //项目管理活动 任务自动带出
        $stage = $this->loadModel('project')->getInIDs($apps);
        $stageParent = $this->loadModel('project')->getInIDs(count($stage) == 1 ? $stage[0]->parent : '');
        if(count($stageParent) == 1 && $stageParent[0]->name == $this->lang->task->stageList['projectManger'] ){
            $taskInfo = $this->loadModel('task')->getProjectTask($apps,$projectName,$flag);
            $task = key($taskInfo);
        }
        $where = !empty($task) ? $task : '';
        echo html::select('objects['.$index.']', $tasks, $where, "class='form-control chosen' ");
    }

    /**
     * 根据项目获取报工开始时间和结束时间
     * @param $project
     * @param int $index
     */
    public function ajaxGetBeginAndEnd($project)
    {
        $endDate                = $this->loadModel('review')->getCloseDate($project);//查询评审关闭时间
        $beginAndEnd =  $this->workreport->getBeginAndEnd($project,isset($endDate->closeDate) ? $endDate->closeDate : '');
        $beginDate = $beginAndEnd->begin;
        die($beginDate) ;
    }

    /**
     * 根据项目获取报工开始时间和结束时间
     * @param $project
     * @param int $index
     */
    public function ajaxGetCreateBeginAndEnd()
    {
        $beginAndEnd =  $this->workreport->getCreateBeginAndEnd();
        $beginDate = $beginAndEnd->begin;
        die($beginDate) ;
    }

    /**
     * 根据开始时间，判断结束时间是否是一周（周一至周日，不跨周）
     * @param $project
     * @param int $index
     */
    public function ajaxGetOneWeekly($start,$end,$index)
    {
       $end = date('Y-m-d',strtotime($end));
       $beginDate = date('Y-m-d',strtotime('this week',strtotime($start)));
       $endDate = date('Y-m-d',(strtotime('next week',strtotime($start)) - 1));
       if(strtotime($end) > strtotime($endDate) || strtotime($end) < strtotime($beginDate) ){
           die(sprintf($this->lang->workreport->errorTips,$index) );
       }
    }

    /**
     * 查询2023年1月到9月7号所报工时
     */
    public function history($param = 0, $recTotal = 0, $recPerPage = 20, $pageID = 1,$begin = null ,$end = null){

        $this->app->loadLang('task');
        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);
        $history = $this->workreport->getHistory(  $pager,$begin ,$end);
        $this->view->history = $history;
        $this->view->param      = $param;
        $this->view->title      = $this->lang->workreport->common;
        $this->view->pager      = $pager;
        $this->view->begin      = $begin ? date('Y-m-d',strtotime($begin)) : '';
        $this->view->end        =  $end ? date('Y-m-d',strtotime($end)) : '';
        $this->display();
    }

   /**
     * 设置session
     */
    public function ajaxGetProjectId($project)
    {
        global $app;
        if($app->session->taskList){
            $uri ="/newexecution-execution-all-$project.html";
            $app->session->set('taskList', $uri, 'project');
        }
        $this->session->set('project', (int)$project);
    }

}
