<?php
class outsidePlan extends control
{
    /**
     * Project: chengfangjinke
     * Method: browse
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:50
     * Desc: This is the code comment. This method is called browse.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param string $browseType
     * @param int $param
     * @param string $orderBy
     * @param int $recTotal
     * @param int $recPerPage
     * @param int $pageID
     */
    public function browse($browseType = 'all', $param = 0, $orderBy = 'year_desc,id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $browseType = strtolower($browseType);
        $this->loadModel('application');
        $this->config->outsideplan->search['params']['projectDept']['values'] = $this->lang->application->teamList;
        /* By search. */
        $queryID = ($browseType == 'bysearch')  ? (int)$param : 0;
        $actionURL = $this->createLink('outsideplan', 'browse', "browseType=bySearch&param=myQueryID");
        $this->outsideplan->buildSearchForm($queryID, $actionURL);

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $this->session->set('outsideplanList', $this->app->getURI(true));

        $this->view->title      = $this->lang->outsideplan->common;
        $this->view->plans      = $this->outsideplan->getList($browseType, $queryID, $orderBy, $pager);
        $this->view->orderBy    = $orderBy;
        $this->view->pager      = $pager;
        $this->view->param      = $param;
        $this->view->browseType = $browseType;
        $this->view->depts      = $this->loadModel('dept')->getOptionMenu();
        $this->view->bearDeptList = $this->lang->application->teamList;
        $this->view->users      = $this->loadModel('user')->getPairs('noletter|noclosed');

        $this->view->unitsAndDepts = $this->outsideplan->getUnitsByPlanIds(array_keys($this->view->plans));
        $this->view->projectPlanInsideStatus = $this->loadModel('projectplan')->getInsideStatusPairs("yearpass,start,reviewing,pass,reject,projected,closed,deleted");
        $this->display();
    }
    
    /**
     * TongYanQi 2022/9/19
     * 内外部年度信息化项目计划一览表
     */
    public function outlook($browseType = 'all', $param = 0, $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $this->loadModel('application');
        $browseType = strtolower($browseType);
        $this->view->listType      = "外部计划一览表";

        /* By search. */
        $queryID = ($browseType == 'bysearch')  ? (int)$param : 0;
        $actionURL = $this->createLink('outsideplan', 'outlook', "browseType=bySearch&param=myQueryID");
        $this->outsideplan->buildSearchFormOutLook($queryID, $actionURL);

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $this->session->set('outsideplanList', $this->app->getURI(true));

        $this->view->title      = $this->lang->outsideplan->common;
        $this->view->projectPlans      = $this->loadModel('projectplan')->getAllBrief();
        $orderBy = "year_desc,begin_desc";
        $this->view->plans      = $this->outsideplan->getOutLookList($this->view->projectPlans, $browseType, $queryID, $orderBy, $pager);

        $this->view->orderBy    = $orderBy;
        $this->view->pager      = $pager;
        $this->view->param      = $param;
        $this->view->browseType = $browseType;
        $this->view->depts      = $this->loadModel('dept')->getOptionMenu();
        $this->view->users      = $this->loadModel('user')->getPairs('noletter|noclosed');

        $this->display();
    }

    public function inlook($browseType = 'all', $param = 0, $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $this->loadModel('application');
        $browseType = strtolower($browseType);
        /** @var projectplanModel $model */
        $model = $this->loadModel('projectplan');
        $this->config->outsideplan->inlooksearch['params']['insideStatus']['values']  = $this->lang->projectplan->insideStatusList;
        $this->config->outsideplan->inlooksearch['params']['status']['values']       = $this->lang->projectplan->statusList;
        $this->config->outsideplan->inlooksearch['params']['projectDept']['values'] = $this->lang->application->teamList;
        $outSubs =   $this->outsideplan->getSubProjectPairs();
        $outSubsItems = [''];
        foreach ($outSubs as $id => $item)
        {
            $outSubsItems[','.$id.','] =   $item;
        }
        $this->config->outsideplan->inlooksearch['params']['outsideSubProject']['values'] = $outSubsItems;//['']+$outSubs;
        $outPlans =  $this->outsideplan->getAllBrief();
        $outPlansItems = [''];
        foreach ($outPlans as $id => $item)
        {
            $outPlansItems[','.$id.','] =   $item->name;
        }
        $this->config->outsideplan->inlooksearch['params']['outsideProject']['values'] = $outPlansItems;
        $tasks =  $this->outsideplan->getTaskPairs();
        $tasksItems = [''];
        foreach ($tasks as $id => $item)
        {
            $tasksItems[','.$id.','] =   $item;
        }
        $this->config->outsideplan->inlooksearch['params']['outsideTask']['values'] = $tasksItems;

        /* By search. */
        $queryID = ($browseType == 'bysearch')  ? (int)$param : 0;
        $actionURL = $this->createLink('outsideplan', 'inlook', "browseType=bySearch&param=myQueryID");
        $this->view->depts      = $this->loadModel('dept')->getOptionMenu();

        $this->config->outsideplan->inlooksearch['params']['bearDept']['values'] = $this->view->depts;
        $this->outsideplan->buildSearchFormInLook($queryID, $actionURL);

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $this->session->set('inlookList', $this->app->getURI(true));

        $this->view->title      = $this->lang->outsideplan->common;

        $orderBy = "year_desc,bearDept_asc,begin_desc";
        $this->view->plans      = $model->getListInLook($browseType, $queryID, $orderBy, $pager, $outSubs, $outPlans);
//a($this->view->plans);
        $this->view->orderBy    = $orderBy;
        $this->view->pager      = $pager;
        $this->view->param      = $param;
        $this->view->browseType = $browseType;

        $this->view->users      = $this->loadModel('user')->getPairs('noletter|noclosed');
        $this->view->listType   = "外部计划一览表";
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: create
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:50
     * Desc: This is the code comment. This method is called create.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     */
    public function create()
    {
        $this->app->loadLang('opinion');
        $this->app->loadConfig('execution');

        if($_POST)
        {
            $planID = $this->outsideplan->create();

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $this->loadModel('action')->create('outsideplan', $planID, 'created', $this->post->comment);

            if(isonlybody()) $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'closeModal' => true, 'callback' => "parent.loadProjects($planID)"));

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = inlink('browse');

            $this->send($response);
        }

        $this->view->title    = $this->lang->outsideplan->create;
        $this->view->users    = $this->loadModel('user')->getPairs('noclosed');
        $this->view->lines    = array('' => '') + $this->loadModel('product')->getLinePairs();
        $this->view->products = $this->product->getPairs();
        $this->view->depts    = $this->loadModel('dept')->getOptionMenu();
        $this->view->plans    = $this->loadModel('projectplan')->getPairs();
        $this->view->apps     = array(0 => '') + $this->loadModel('application')->getapplicationNameCodePairs();
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: edit
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:50
     * Desc: This is the code comment. This method is called edit.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $planID
     */
    public function edit($planID = 0)
    {
        $this->app->loadLang('opinion');
        $this->app->loadConfig('execution');

        if($_POST)
        {
            $changes = $this->outsideplan->update($planID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes)
            {
                $actionID = $this->loadModel('action')->create('outsideplan', $planID, 'edited');
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = inlink('view', "planID=$planID");

            $this->send($response);
        }

        $this->view->title    = $this->lang->outsideplan->edit;
        $this->view->users    = $this->loadModel('user')->getPairs('noclosed');
        $this->view->plan     = $this->outsideplan->getByID($planID);
        $this->view->lines    = array('' => '') + $this->loadModel('product')->getLinePairs();
        $this->view->products = $this->product->getPairs();
        $this->view->depts    = $this->loadModel('dept')->getOptionMenu();
        $this->view->apps     = array(0 => '') + $this->loadModel('application')->getapplicationNameCodePairs();
        $this->view->plans    = $this->loadModel('projectplan')->getPairs();
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: view
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:50
     * Desc: This is the code comment. This method is called view.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $planID
     */
    public function view($planID = 0)
    {
        $this->app->loadLang('opinion');
        $this->loadModel('application');
        $this->view->title    = $this->lang->outsideplan->view;
        $this->view->actions  = $this->loadModel('action')->getList('outsideplan', $planID);
        $this->view->plan     = $this->outsideplan->getByID($planID);
        $tasks    = $this->dao->select('*')->from(TABLE_OUTSIDEPLANTASKS)->where('outsideProjectPlanID')->eq($planID)->andwhere('deleted')->eq(0)->fetchall();
        foreach ($tasks as $task){
            $task = $this->loadModel('file')->replaceImgURL($task, $this->config->outsideplan->editor->edittask['id']);
            isset($this->view->plan->subprojects[$task->subProjectID]) && $this->view->plan->subprojects[$task->subProjectID]->tasks[] = $task;
        }
        $this->view->maintainersusers    = $this->loadModel('user')->getListByAccounts($this->view->plan->maintainers);
        $this->view->users    = $this->loadModel('user')->getPairs('noclosed|noletter');
        $this->view->createdBy    = $this->loadModel('user')->getListByAccounts($this->view->plan->createdBy);
        $this->view->lines    = array('' => '') + $this->loadModel('product')->getLinePairs();
        $this->view->products = $this->product->getPairs();
        $this->view->depts    = $this->loadModel('dept')->getOptionMenu();
        $this->view->apps     = array('' => '') + $this->loadModel('application')->getapplicationNameCodePairs();
        $this->loadModel('projectplan');
        $this->view->plans    = $this->dao->select('id,name,status,insideStatus')->from(TABLE_PROJECTPLAN)->where('id')->in(explode(',',$this->view->plan->linkedPlan))->andWhere('status')->in("yearpass,start,reviewing,pass,reject,projected,closed,deleted")->andwhere('deleted')->eq(0)->fetchall();
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: delete
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:51
     * Desc: This is the code comment. This method is called delete.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $planID
     * @param string $confirm
     */
    public function delete($planID, $confirm = 'no')
    {
        if($confirm == 'no')
        {
            echo js::confirm($this->lang->outsideplan->confirmDelete, $this->createLink('outsideplan', 'delete', "planID=$planID&confirm=yes"), '');
            exit;
        }
        else
        {
            $this->outsideplan->delete(TABLE_OUTSIDEPLAN, $planID);
            $this->dao->update(TABLE_OUTSIDEPLAN)->set('deleted')->eq('1')->where('id')->eq($planID)->exec();
            $this->dao->update(TABLE_OUTSIDEPLANTASKS)->set('deleted')->eq('1')->where('outsideProjectPlanID')->eq($planID)->exec();
            $this->dao->update(TABLE_OUTSIDEPLANSUBPROJECTS)->set('deleted')->eq('1')->where('outsideProjectPlanID')->eq($planID)->exec();

            $this->loadModel("projectplan")->updateOutsideplan($planID);
            die(js::locate(inlink('browse'), 'parent'));
        }
    }

    /**
     * TongYanQi 2022/9/9
     * 删除子项目
     */
    public function deleteSub($subProjectID, $confirm = 'no')
    {
        if($confirm != 'yes')
        {
            echo js::confirm($this->lang->outsideplan->confirmDelete, $this->createLink('outsideplan', 'deleteSub', "planID=$subProjectID&confirm=yes"), '');
            exit;
        }
        else
        {
            $planID     =  $this->dao->select('outsideProjectPlanID')->from(TABLE_OUTSIDEPLANSUBPROJECTS)->where('id')->eq($subProjectID)->fetch('outsideProjectPlanID');
            $plans = $this->dao->select('count(id) as count')->from(TABLE_OUTSIDEPLANSUBPROJECTS)->where('outsideProjectPlanID')->eq($planID)->andwhere('deleted')->eq(0)->fetch();
            if($plans->count <= 1) {
                die(js::alert("不能删除唯一的子任务"));
            }
            $this->dao->update(TABLE_OUTSIDEPLANSUBPROJECTS)->set('deleted')->eq(1)->where('id')->eq($subProjectID)->exec();
            $this->dao->update(TABLE_OUTSIDEPLANTASKS)->set('deleted')->eq(1)->where('subProjectID')->eq($subProjectID)->exec();
            $this->loadModel('action')->create('outsideplan', $planID, 'deleteSub');
            die(js::locate(inlink('browse'), 'parent'));
        }
    }

    public function deleteTask($taskID, $confirm = 'no')
    {
        if($confirm != 'yes')
        {
            echo js::confirm($this->lang->outsideplan->confirmDelete, $this->createLink('outsideplan', 'deleteTask', "id=$taskID&confirm=yes"), '');
            exit;
        }
        else
        {
            $planID     =  $this->dao->select('outsideProjectPlanID')->from(TABLE_OUTSIDEPLANTASKS)->where('id')->eq($taskID)->fetch('outsideProjectPlanID');
            $this->dao->update(TABLE_OUTSIDEPLANTASKS)->set('deleted')->eq(1)->where('id')->eq($taskID)->exec();
            $this->loadModel('action')->create('outsideplan', $planID, 'deleteTask');
            die(js::locate(inlink('browse'), 'parent'));
        }
    }

    public function copySub($subProjectId, $planID){
        if($_POST)
        {
            $result = $this->outsideplan->copySub();

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }


            if($result){
                $actionID = $this->loadModel('action')->create('outsideplan', $result['planid'], 'copysub',$result['comment']);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $this->view->outsidePlanList = $this->outsideplan->getPairsBymaintainers();
        $this->view->subProjectId = $subProjectId;
        $this->display();
    }
    public function copyTask($taskID, $planID){
        if($_POST)
        {
            $result = $this->outsideplan->copyTask();

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }



            if($result){
                $actionID = $this->loadModel('action')->create('outsideplan', $result['planid'], 'copytask',$result['comment']);
            }
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $this->view->outsidePlanList = [0=>'']+$this->outsideplan->getPairsBymaintainers();
        $this->view->sourceTaskId = $taskID;

        $this->display();
    }
    public function moveSub($subProjectId, $planID){
        if($_POST)
        {
            $result = $this->outsideplan->moveSub($planID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }


            if($result){
                //移出年度计划记录。
                $actionID = $this->loadModel('action')->create('outsideplan', $result['moveout']['planid'], 'movesub',$result['moveout']['comment']);
                //移入年度计划记录
                $actionID = $this->loadModel('action')->create('outsideplan', $result['movein']['planid'], 'movesub',$result['movein']['comment']);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $this->view->outsidePlanList = [0=>'']+$this->outsideplan->getPairsBymaintainers();
        $this->view->subProjectId = $subProjectId;

        $this->display();

    }
    public function moveTask($taskID, $planID){
        if($_POST)
        {
//            exit('aa');
            $result = $this->outsideplan->moveTask($planID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }


            if($result){
                //移出年度计划记录。
                $actionID = $this->loadModel('action')->create('outsideplan', $result['moveout']['planid'], 'movetask',$result['moveout']['comment']);
                //移入年度计划记录
                $actionID = $this->loadModel('action')->create('outsideplan', $result['movein']['planid'], 'movetask',$result['movein']['comment']);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $this->view->outsidePlanList = [0=>'']+$this->outsideplan->getPairsBymaintainers();
        $this->view->sourceTaskId = $taskID;

        $this->display();
    }

    public function ajaxcheckTaskDate(){
        if($_POST)
        {
            $result = $this->outsideplan->checkTaskDate();

            die($result);
        }
    }
    public function ajaxcheckBySubTaskDate(){
        if($_POST)
        {
            $result = $this->outsideplan->checkBySubTaskDate();

            die($result);
        }
    }
    /**
     * Project: chengfangjinke
     * Method: export
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:51
     * Desc: This is the code comment. This method is called export.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param string $orderBy
     * @param string $browseType
     */
    public function export($orderBy = 'id_desc', $browseType = 'all')
    {
        $this->loadModel('application');
        /* format the fields of every outsideplan in order to export data. */
        $this->app->loadLang('opinion');
        $this->app->loadLang('projectplan');
        if($_POST)
        {
            $this->outsideplan->setListValue();

            $this->loadModel('file');
            $outsideplanLang   = $this->lang->outsideplan;
            $outsideplanConfig = $this->config->outsideplan;
            /* Create field lists. */
            $fields = $this->post->exportFields ? $this->post->exportFields : explode(',', $outsideplanConfig->list->exportFields);
            foreach($fields as $key => $fieldName)
            {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = isset($outsideplanLang->$fieldName) ? $outsideplanLang->$fieldName : $fieldName;
                unset($fields[$key]);
            }

            $fields['subProjectName'] = $outsideplanLang->subProjectName;
            $fields['subTaskName'] = $outsideplanLang->subTaskName;
            $fields['subTaskDesc'] = $outsideplanLang->subTaskDesc;
            $fields['subTaskBegin'] = $outsideplanLang->subTaskBegin;
            $fields['subTaskEnd'] = $outsideplanLang->subTaskEnd;
            $fields['subTaskUnit'] = $outsideplanLang->subTaskUnit;
            $fields['subTaskBearDept'] = $outsideplanLang->subTaskBearDept;
            $fields['subTaskDemandParty'] = $outsideplanLang->subTaskDemandParty;
            $fields['subTaskDemandContact'] = $outsideplanLang->subTaskDemandContact;
            $fields['subTaskDemandDeadline'] = $outsideplanLang->subTaskDemandDeadline;
            $fields['linkedInnerProjectPlans'] = $outsideplanLang->linkedInnerProjectPlans;
            $fields['EndedInnerProjectPlans'] = $outsideplanLang->EndedInnerProjectPlans;
            $fields['toDoInnerProjectPlans'] = $outsideplanLang->toDoInnerProjectPlans;

            /* Get outsideplans. */
            $outsideplans = array();
            if($this->session->outsideplanOnlyCondition)
            {
                $outsideplans = $this->dao->select('*')->from(TABLE_OUTSIDEPLAN)->where($this->session->outsideplanQueryCondition)
                    ->beginIF($this->post->exportType == 'selected')->andWhere('id')->in($this->cookie->checkedItem)->fi()
                    ->andWhere('deleted')->eq(0)
                    ->orderBy($orderBy)->fetchAll('id');
            }
            else
            {
                $stmt  = $this->dbh->query($this->session->outsideplanQueryCondition . ($this->post->exportType == 'selected' ? " AND $fieldName IN({$this->cookie->checkedItem})" : '') . " ORDER BY " . strtr($orderBy, '_', ' '));
                while($row = $stmt->fetch()) $outsideplans[$row->id] = $row;
            }
            $outsideplanIdList = array_keys($outsideplans);

            /* Get users, products and executions. */
            $users = $this->loadModel('user')->getPairs('noletter');
            $outsideplanList = [];
            $i = 0;
            foreach($outsideplans as $planID => $outsideplan)
            {
                if(isset($outsideplanLang->typeList[$outsideplan->type]))               $outsideplan->type        = $outsideplanLang->typeList[$outsideplan->type];
                if(isset($this->lang->opinion->categoryList[$outsideplan->category]))   $outsideplan->category    = $this->lang->opinion->categoryList[$outsideplan->category];
                if(isset($outsideplanLang->statusList[$outsideplan->status]))           $outsideplan->status      = $outsideplanLang->statusList[$outsideplan->status];
                if(isset($users[$outsideplan->createdBy])) $outsideplan->createdBy = $users[$outsideplan->createdBy];
                if(isset($users[$outsideplan->owner]))     $outsideplan->owner     = $users[$outsideplan->owner];

                $outsideplan->milestone = strip_tags(br2nl($outsideplan->milestone));
                $outsideplan->changes = strip_tags(br2nl($outsideplan->changes));

                $outsideplan->apptype = zget($outsideplanLang->apptypeList,$outsideplan->apptype);
                $outsideplan->projectisdelay = zget($outsideplanLang->projectisdelayList,$outsideplan->projectisdelay);
                $outsideplan->projectischange = zget($outsideplanLang->projectischangeList,$outsideplan->projectischange);
                //维护人员
                $maintainers = explode(',', $outsideplan->maintainers);
                $maintainerNames = '';
                foreach ($maintainers as $maintainer){
                    $maintainerNames .= $users[$maintainer].PHP_EOL;
                }
                $outsideplan->maintainers = $maintainerNames;

                $outsideplan->begin      = substr($outsideplan->begin, 0, 10);
                $outsideplan->end        = substr($outsideplan->end, 0, 10);

                $subProjects = $this->dao->select('id,subProjectName')->from(TABLE_OUTSIDEPLANSUBPROJECTS)->where('outsideProjectPlanID')->eq($planID)->andwhere('deleted')->eq(0)->fetchAll('id');
                $ii = 0;

                $plans    = $this->dao->select('id,name,status,insideStatus')->from(TABLE_PROJECTPLAN)->where('outsideProject')->like("%,{$planID},%")->andwhere('deleted')->eq(0)->fetchall();
                $outsideplan->linkedInnerProjectPlans = '';
                $outsideplan->EndedInnerProjectPlans = '';
                $outsideplan->toDoInnerProjectPlans = '';
                foreach ($plans as $projectplan) {
                    $outsideplan->linkedInnerProjectPlans .= $projectplan->name .'('.zget($this->lang->projectplan->insideStatusList, $projectplan->insideStatus,'').')'.PHP_EOL;
                    if($projectplan->insideStatus == 'done') {
                        $outsideplan->EndedInnerProjectPlans .= $projectplan->name .'('.zget($this->lang->projectplan->insideStatusList, $projectplan->insideStatus,'').')'.PHP_EOL;
                    } else {
                        $outsideplan->toDoInnerProjectPlans .= $projectplan->name .'('.zget($this->lang->projectplan->insideStatusList, $projectplan->insideStatus,'').')'.PHP_EOL;

                    }
                }
                foreach ($subProjects as $subProject)
                {
                    $nowOutsideplan = new stdClass();
                    if($ii==0){
                        foreach ($outsideplan as $k=>$v){
                            $nowOutsideplan->$k = $v;
                        }
                    }
                    $ii++;
                        $outsideplanList[$i] = $nowOutsideplan; //第i行的项目数据
                        $outsideplanList[$i]->subProjectName = $subProject->subProjectName ; //子项目名必有
                        $tasks = $this->dao->select('*')->from(TABLE_OUTSIDEPLANTASKS)->where('subProjectID')->eq($subProject->id)->andwhere('deleted')->eq(0)->fetchAll('id');
                        foreach ($tasks as $task){ //(外部)子项/子任务可能没有
                                $outsideplanList[$i]->subTaskName = $task->subTaskName;
                                $outsideplanList[$i]->subTaskDesc =  strip_tags(br2nl(html_entity_decode($task->subTaskDesc)));
                                $outsideplanList[$i]->subTaskBegin = $task->subTaskBegin;
                                $outsideplanList[$i]->subTaskEnd = $task->subTaskEnd;
                                $outsideplanList[$i]->subTaskDemandContact = $task->subTaskDemandContact;
                                $outsideplanList[$i]->subTaskDemandDeadline = str_replace('0000-00-00', '', $task->subTaskDemandDeadline);
                                //多选部门
                                        $vlist = explode(',', $task->subTaskUnit);
                                        $vArr = [];
                                        foreach ($vlist as $itemv){
                                            if(empty($itemv)) continue;
                                            $vArr[] = $outsideplanLang->subProjectUnitList[$itemv] ;
                                        }
                                $outsideplanList[$i]->subTaskUnit = implode(',',$vArr);

                                        $vlist = explode(',', $task->subTaskBearDept);
                                        $vArr = [];
                                        foreach ($vlist as $itemv){
                                            if(empty($itemv)) continue;
                                            $vArr[] = $this->lang->application->teamList[$itemv] ;
                                        }
                                $outsideplanList[$i]->subTaskBearDept = implode(',',$vArr);


                                        $vlist = explode(',', $task->subTaskDemandParty);
                                        $vArr = [];
                                        foreach ($vlist as $itemv){
                                            if(empty($itemv)) continue;
                                            $vArr[] = $outsideplanLang->subProjectDemandPartyList[$itemv];
                                        }
                                $outsideplanList[$i]->subTaskDemandParty = implode(',',$vArr);
                                //end 处理多选部门
                                $i++;
                            }
                        if(empty($tasks)) { $i++; } //如没有任务 进行下一行
                } //子项目是一定要有的
            }
            $this->post->set('fields', $fields);
            $this->post->set('rows', $outsideplanList);
            $this->post->set('kind', 'outsideplan');
            $this->loadModel('file')->setExcelWidth(20);
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        $this->view->fileName        = $this->lang->outsideplan->common;
        $this->view->allExportFields = $this->config->outsideplan->list->exportFields;
        $this->view->customExport    = false;
        $this->display();
    }

    /**
     * TongYanQi 2022/9/22
     * 计划一览表导出
     */
    public function exportOutlook($browseType='all')
    {
        $this->loadModel('application');
        $this->app->loadLang('opinion');
        if($_POST)
        {
            $fields = Array
            (
                'name' => '(外部)项目/任务名称',
                'code' => '(外部)项目/任务编号',
                'begin' => '(外部)项目/任务计划开始时间',
                'end'   => '(外部)项目/任务计划完成时间',
                'status' => '(外部)项目/任务计划状态',
                'subProjectName'    => '(外部)一级子项',
                'subTaskName'       => '(外部)子项/子任务名称',
                'subTaskDesc'       => '(外部)子项/子任务描述',
                'subTaskUnit'       => '业务司局',
                'subTaskBearDept'   => '承建单位',
                'projectPlanName'   => '内部项目名称',
                'projectPlanCode'   => '项目计划编号',
                'projectPlanIsImportant' => '是否重点项目',
                'projectPlanBegin'  => '内部计划开始时间',
                'projectPlanEnd'    => '内部计划完成时间',
                'projectPlanWorkload' => '内部计划工作量(人/月)',
                'projectPlanNameBearDept' => '承建部门',
                'projectPlanNameOwner'  => '项目负责人',
                'projectPlanNamePhone'  => '项目负责人联系方式',
                'projectPlanNameStatus' => '内部项目状态',
                'projectCode'       => '项目代号',
                'planCode'          => '项目编号',
                'projectMembers'    => '参与人员',
                'projectDeptNames'  => '人员所在部门',
                'projectEstimate'   => '项目预算工作量（小时）',
                'projectConsumed'   => '工作量（小时）',
                'projectBudget'     => '项目预算收入',
                'projectProgress'   => '进度'
            );
            /** @var outsideplanModel $model */
            $model = $this->outsideplan;
            $data = $model->getOutLookExportData($browseType);
            $this->post->set('fields', $fields);
            $this->post->set('rows', $data);
            $this->post->set('kind', 'outsideplan');
            $this->loadModel('file')->setExcelWidth(20);
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        $this->view->fileName        = "内外部信息化项目计划一览表";
        $this->view->allExportFields = $this->config->outsideplan->list->exportFields;
        $this->view->customExport    = false;
        $this->display();
    }



    /**
     * TongYanQi 2022/9/28
     * 导出统计表
     */
    public function exportChart()
    {
        parse_str($this->server->query_String, $queryString); //获取get参数
        $fileName = '年度内外部信息化项目计划统计表';
        if($queryString['type'] == 2){ $fileName = '业务司局内外部信息化项目计划统计表';}
        if($queryString['type'] == 3){ $fileName = '承建单位年度内外部信息化项目计划统计表';}
        $this->loadModel('application');
        $this->app->loadLang('opinion');
        if($_POST)
        {
            if($queryString['type'] == 2){ $fields['key'] = '业务司局';}
            if($queryString['type'] == 3){ $fields['key'] = '承建单位';}
            $fields['year'] = '年份';
            $fields['idNum'] = '(外部)项目/任务总数';
            $fields['subs'] = '(外部)一级子项总数';
            $fields['tasks'] = '(外部)子项/子任务总数';
            $fields['status_wait'] = '(外部)已创建';
            $fields['status_exceptionallyfinished'] = '(外部)异常完成';
            $fields['status_finished'] = '(外部)正常完成';
            $fields['status_notfinished'] = '(外部)未完成';
            $fields['status_exceptionallyprogressing'] = '(外部)进度异常';
            $fields['projectPlanNum'] = '内部项目总数';
            $fields['insideStatus_cancel'] = '(内部)已取消';
            $fields['insideStatus_wait'] = '(内部)待立项';
            $fields['insideStatus_projectdelay'] = '(内部)延迟立项';
            $fields['insideStatus_projectpause'] = '(内部)暂停立项';
            $fields['insideStatus_projecting'] = '(内部)立项中';
            $fields['insideStatus_projectsetup'] = '(内部)已立项';
            $fields['insideStatus_progressnormal'] = '(内部)进度正常';
            $fields['insideStatus_progressdelay'] = '(内部)进度延迟';
            $fields['insideStatus_pause'] = '(内部)已暂停';
            $fields['insideStatus_abort'] = '(内部)已撤销';
            $fields['insideStatus_done']  = '(内部)已结项';
            if($queryString['type'] == 1){ $fields['insideUnlinks'] = '内部项目总数（未关联(外部)项目/任务）'; }

            /** @var outsideplanModel $model */
            $model = $this->outsideplan;
            $data = [];
            if($queryString['type'] == 1){ $data = $model->getChartYearDateForExport(); }
            if($queryString['type'] == 2){ $data = $model->getChartUnitDataForExport(); $data = $data['units'] ?? "";} //业务司局
            if($queryString['type'] == 3){ $data = $model->getChartUnitDataForExport(); $data = $data['depts'] ?? "";} //承建单位

            $this->post->set('fields', $fields);
            $this->post->set('rows', $data);
            $this->post->set('kind', '年度统计');
            $this->loadModel('file')->setExcelWidth(17);
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        $this->view->fileName        = $fileName;
        $this->view->allExportFields = $this->config->outsideplan->list->exportFields;
        $this->view->customExport    = false;
        $this->display();
    }

    /**
     * TongYanQi 2022/9/22
     * 计划一览表导出
     */
    public function exportInLook($browseType = 'all')
    {
        $this->loadModel('application');
        $this->loadModel('projectplan');
        $this->app->loadLang('opinion');
        if($_POST)
        {
            $fields = Array
            (
                'name'          => $this->lang->projectplan->name,
                'code'          => $this->lang->projectplan->code,

                'planCode'      => $this->lang->projectplan->planCode,
                'mark'          => $this->lang->projectplan->mark,
                'bearDeptStr'          => $this->lang->projectplan->bearDept,
                'begin'         => $this->lang->projectplan->begin,
                'end'           => $this->lang->projectplan->end,
                'status'        => $this->lang->projectplan->status,
                'insideStatus'  => $this->lang->projectplan->insideStatus,
                'workload'      => $this->lang->projectplan->workload."(".$this->lang->projectplan->monthly.")",
                'estimate'      => $this->lang->projectplan->estimate,
                'consumed'      => $this->lang->projectplan->consumed,
                'budget'        => $this->lang->projectplan->budget,
                'progress'      => $this->lang->projectplan->progress,
                'outsideProjectPlanName'       => $this->lang->outsideplan->name,
                'subTaskUnit'       =>  $this->lang->outsideplan->name,
                'subTaskBearDept'   =>  $this->lang->outsideplan->subTaskBearDept,
                'outsideProjectPlanCode'       => $this->lang->outsideplan->code,
                'outsideProjectPlanBegin'       => $this->lang->outsideplan->begin,
                'outsideProjectPlanEnd'         => $this->lang->outsideplan->end,
                'outsideProjectPlanStatus'      => $this->lang->outsideplan->status,
                'subTaskBegin' => $this->lang->outsideplan->subTaskBegin,
                'subTaskEnd' => $this->lang->outsideplan->subTaskEnd,
            );
            /** @var outsideplanModel $model */
            $model = $this->outsideplan;
            $data = $model->getInlookExportData($browseType);

            $this->post->set('fields', $fields);
            $this->post->set('rows', $data);
            $this->post->set('kind', 'outsideplan');
            $this->loadModel('file')->setExcelWidth(20);
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        $this->view->fileName        = "内外部信息化项目计划一览表(内部视图)";
        $this->view->allExportFields = '';
        $this->view->customExport    = false;
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: exportTemplate
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:51
     * Desc: This is the code comment. This method is called exportTemplate.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     */
    public function exportTemplate()
    {
        $this->loadModel('application');
        if($_POST)
        {
            /** @var outsideplanModel $model */
            $model = $this->outsideplan;
            $model->setListValue();

            foreach($this->config->outsideplan->export->templateFields as $field) $fields[$field] = $this->lang->outsideplan->$field;
            $outsideplanLang = $this->lang->outsideplan;
            $fields['subProjectName'] = $outsideplanLang->subProjectName;
            $fields['subTaskName'] = $outsideplanLang->subTaskName;
            $fields['subTaskBegin'] = $outsideplanLang->subTaskBegin;
            $fields['subTaskEnd'] = $outsideplanLang->subTaskEnd;
            $fields['subProjectUnit'] = $outsideplanLang->subProjectUnit;
            $fields['subProjectBearDept'] = $outsideplanLang->subProjectBearDept;
            $fields['subProjectDemandParty'] = $outsideplanLang->subProjectDemandParty;
            $fields['subTaskDemandContact'] = $outsideplanLang->subTaskDemandContact;
            $fields['subTaskDemandDeadline'] = $outsideplanLang->subTaskDemandDeadline;
            $fields['subTaskDesc'] = $outsideplanLang->subTaskDesc;

            $this->post->set('fields', $fields);
            $this->post->set('kind', 'outsideplan');
            $this->post->set('rows', array());
            $this->post->set('extraNum',   $this->post->num);
            $this->post->set('fileName', 'outsideplanTemplate');
            $this->loadModel('file')->setExcelWidth(25);
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }
        $this->display();
    }
    /**
     * TongYanQi 2022/10/9
     * 编辑外部年度计划状态
     */
    public function editStatus($planID)
    {
        $this->view->plan     = $this->outsideplan->getByID($planID);
        if($_POST)
        {
            $this->dao->update(TABLE_OUTSIDEPLAN)->data(['status'=>$_POST['status']])->where('id')->eq($planID)->exec();
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->loadModel('action')->create('outsideplan', $planID, 'editStatus');
            $this->send($response);
        }
        $this->display();
    }
    /**
     * Project: chengfangjinke
     * Method: import
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:51
     * Desc: This is the code comment. This method is called import.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     */
    public function import()
    {
        if($_FILES)
        {   
            $file = $this->loadModel('file')->getUpload('file');
            $file = $file[0];
            if($file['extension'] != 'xlsx') die(js::alert($this->lang->file->onlySupportXLSX));

            $fileName = $this->file->savePath . $this->file->getSaveName($file['pathname']);
            move_uploaded_file($file['tmpname'], $fileName);

            $phpExcel  = $this->app->loadClass('phpexcel');
            $phpReader = new PHPExcel_Reader_Excel2007(); 
            if(!$phpReader->canRead($fileName))
            {   
                $phpReader = new PHPExcel_Reader_Excel5(); 
                if(!$phpReader->canRead($fileName))die(js::alert($this->lang->excel->canNotRead));
            }   
            $this->session->set('fileImport', $fileName);
            die(js::locate(inlink('showImport'), 'parent.parent'));
        }

        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: showImport
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:51
     * Desc: This is the code comment. This method is called showImport.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $pagerID
     * @param int $maxImport
     * @param string $insert
     */
    public function showImport($pagerID = 1, $maxImport = 0, $insert = '')
    {
        $this->loadModel('application');
        $this->app->loadLang('opinion');
        $file    = $this->session->fileImport;
        $tmpPath = $this->loadModel('file')->getPathOfImportedFile();
        $tmpFile = $tmpPath . DS . md5(basename($file));
        $this->lang->outsideplan->categoryList = $this->lang->opinion->categoryList;
        $this->loadModel('application');
        $this->view->subProjectBearDeptList = $this->lang->application->teamList;

        $this->view->subProjectUnitList = $this->lang->outsideplan->subProjectUnitList;
        $this->view->subProjectDemandPartyList = $this->lang->outsideplan->subProjectDemandPartyList;
        $outsideplanConfig = $this->config->outsideplan;
        $outsideplanConfig->list->showImportFields = 'type,year,code,historyCode,name,status,begin,end,workload,duration,maintainers,phone,content,milestone,changes,apptype,projectinitplan,uatplanfinishtime,materialplanonlinetime,planonlinetime,projectisdelay,projectisdelaydesc,projectischange,projectischangedesc,subProjectName,subTaskName,subTaskBegin,subTaskEnd,subProjectUnit,subProjectBearDept,subProjectDemandParty,subTaskDemandContact,subTaskDemandDeadline,subTaskDesc';

        if($_POST)
        {
            $this->outsideplan->createFromImport();
            if($this->post->isEndPage)
            {
                unlink($tmpFile);
                die(js::locate($this->createLink('outsideplan','browse'), 'parent'));
            }
            else
            {
                die(js::locate(inlink('showImport', "pagerID=" . ($this->post->pagerID + 1) . "&maxImport=$maxImport&insert=" . zget($_POST, 'insert', '')), 'parent'));
            }
        }

        if(!empty($maxImport) and file_exists($tmpFile))
        {
            $outsideplanData = unserialize(file_get_contents($tmpFile));
        }
        else
        {
            $pagerID = 1;
            $outsideplanLang   = $this->lang->outsideplan;

            $fields      = explode(',', $outsideplanConfig->list->showImportFields);
            foreach($fields as $key => $fieldName)
            {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = isset($outsideplanLang->$fieldName) ? $outsideplanLang->$fieldName : $fieldName;
                unset($fields[$key]);
            }

            $rows = $this->file->getRowsFromExcel($file);

            $outsideplanData = array();
            foreach($rows as $currentRow => $row)
            {
                $outsideplan = new stdclass();
                foreach($row as $currentColumn => $cellValue)
                {

                    if($currentRow == 1)
                    {
                        $field = array_search($cellValue, $fields);
                        $columnKey[$currentColumn] = $field ? $field : '';
                        continue;
                    }

                    if(empty($columnKey[$currentColumn]))
                    {
                        $currentColumn++;
                        continue;
                    }
                    $field = $columnKey[$currentColumn];
                    $currentColumn++;

                    // check empty data.
                    if(empty($cellValue))
                    {
                        $outsideplan->$field = '';
                        continue;
                    }

                    if(in_array($field, $outsideplanConfig->export->listFields))
                    {
                        if(strrpos($cellValue, '(#') === false)
                        {
                            $outsideplan->$field = $cellValue;
                            if(!isset($outsideplanLang->{$field . 'List'}) or !is_array($outsideplanLang->{$field . 'List'})) continue;

                            /* when the cell value is key of list then eq the key. */
                            $listKey = array_keys($outsideplanLang->{$field . 'List'});
                            unset($listKey[0]);
                            unset($listKey['']);
                            $fieldKey = array_search($cellValue, $outsideplanLang->{$field . 'List'});
                            if($fieldKey) $outsideplan->$field = $fieldKey;
                        }
                        else
                        {
                            $id = trim(substr($cellValue, strrpos($cellValue,'(#') + 2), ')');
                            $outsideplan->$field = $id;
                        }
                    }
                    else
                    {
                        $outsideplan->$field = $cellValue;
                    }
                }
                if(empty($outsideplan->subTaskName)) continue;
                $outsideplanData[$currentRow] = $outsideplan;
                unset($outsideplan);
            }
            file_put_contents($tmpFile, serialize($outsideplanData));
        }
        if(empty($outsideplanData))
        {
            unlink($this->session->fileImport);
            unset($_SESSION['fileImport']);
            echo js::alert($this->lang->excel->noData);
            die(js::locate($this->createLink('outsideplan','browse')));
        }

        $allCount = count($outsideplanData);
        $allPager = 1;
        if($allCount > 500)
        {
             die('不能大于500行，请返回分来处理');
        }
        if(empty($outsideplanData)) die(js::locate($this->createLink('outsideplan','browse')));

        /* Judge whether the editedStories is too large and set session. */
        $countInputVars  = count($outsideplanData) * 11;
        $showSuhosinInfo = common::judgeSuhosinSetting($countInputVars);
        if($showSuhosinInfo) $this->view->suhosinInfo = extension_loaded('suhosin') ? sprintf($this->lang->suhosinInfo, $countInputVars) : sprintf($this->lang->maxVarsInfo, $countInputVars);

        $this->view->title      = $this->lang->outsideplan->common . $this->lang->colon . $this->lang->outsideplan->showImport;
        $this->view->position[] = $this->lang->outsideplan->showImport;

        $this->view->outsideplanData = $outsideplanData;
        $this->view->allCount        = $allCount;
        $this->view->allPager        = $allPager;
        $this->view->pagerID         = $pagerID;
        $this->view->isEndPage       = $pagerID >= $allPager;
        $this->view->maxImport       = $maxImport;
        $this->view->dataInsert      = $insert;
        $this->view->lines           = array('' => '') + $this->loadModel('product')->getLinePairs();
        $this->view->depts           = $this->loadModel('dept')->getOptionMenu();
        $this->view->apps            = array(0 => '') + $this->loadModel('application')->getapplicationNameCodePairs();
        $this->view->users           = $this->loadModel('user')->getPairs('noclosed');
        $this->display();
    }
    /**
     * TongYanQi 2022/9/8
     * 创建拆分子任务
     */
    public function createTask($subProjectID, $planID)
    {
        $this->loadModel('application');
        $this->app->loadLang('opinion');
        $this->app->loadConfig('execution');

        $this->view->plan = $this->outsideplan->getByID($planID);
        if($_POST)
        {
             $this->outsideplan->createtask($subProjectID, $this->view->plan);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $this->loadModel('action')->create('outsideplan', $planID, 'createdTask', $this->post->comment);

            if(isonlybody()) $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'closeModal' => true, 'callback' => "parent.loadProjects($planID)"));

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = inlink('browse');

            $this->send($response);
        }

        $this->view->title    = $this->lang->outsideplan->create;
        $this->view->outsideProject = $this->dao->select('*')->from(TABLE_OUTSIDEPLANSUBPROJECTS)->where('id')->eq($subProjectID)->fetch();
        $this->display();
    }


    public function editTask($taskId, $planID)
    {
        $this->loadModel('application');
        $this->app->loadLang('opinion');
        $this->app->loadConfig('execution');

        $this->view->plan = $this->outsideplan->getByID($planID);
        $task = $this->dao->select('*')->from(TABLE_OUTSIDEPLANTASKS)->where('id')->eq($taskId)->andwhere('deleted')->eq(0)->fetch();
        $task = $this->loadModel('file')->replaceImgURL($task, $this->config->outsideplan->editor->edittask['id']);
        if($_POST)
        {
            $this->outsideplan->edittask($taskId, $this->view->plan, $task->subProjectID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $this->loadModel('action')->create('outsideplan', $planID, 'editTask', $this->post->comment);

            if(isonlybody()) $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'closeModal' => true, 'callback' => "parent.loadProjects($planID)"));

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = inlink('browse');

            $this->send($response);
        }




        $this->view->title    = $this->lang->outsideplan->create;
        $this->view->outsideProject = $this->dao->select('*')->from(TABLE_OUTSIDEPLANSUBPROJECTS)->where('id')->eq($task->subProjectID)->fetch();
        $this->view->tasks[] = $task;
        $this->display();

    }

    public function bindprojectplan($taskId,$planID){
        if($_POST)
        {
            $this->outsideplan->bindprojectplan($taskId);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if(isonlybody()) $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'closeModal' => true, 'callback' => "parent.loadProjects($planID)"));

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = inlink('browse');

            $this->send($response);
        }
        $taskProjectPlanList = $this->loadModel('projectplan')->getPlanBytaskIDFilter($taskId,'id,name');
        if($taskProjectPlanList){
            $taskProjectPlanList = array_column($taskProjectPlanList,'id');
        }else{
            $taskProjectPlanList = [];
        }
        $this->view->taskProjectPlanList = $taskProjectPlanList;
        $projectplanList = $this->projectplan->getPlanByFilter('id,name');
        $this->view->projectplanList = array_column($projectplanList,'name','id');
        $this->view->taskId = $taskId;
        $this->display();
    }

    /**
     * Jinzhuliang 2023/6/21
     * 根据外部计划id获取子项目
     */
    public function ajaxSubProject($planID = 0, $orderBy = 'id_desc')
    {
        $pairs = $this->dao->select('id,subProjectName')->from(TABLE_OUTSIDEPLANSUBPROJECTS)
            ->where('outsideProjectPlanID')->in($planID)
            ->andWhere('deleted')->eq('0')
            ->orderBy($orderBy)
            ->fetchPairs();

        echo html::select('outsideplanSubID', $pairs, '', "class='form-control chosen'");
    }
    /**
     * TongYanQi 2022/9/15
     * 根据外部计划id获取子项目
     */
    public function ajaxSubProjects($planID = 0, $orderBy = 'id_desc',$suboutPlanID='')
    {
         $pairs = $this->dao->select('id,subProjectName')->from(TABLE_OUTSIDEPLANSUBPROJECTS)
            ->where('outsideProjectPlanID')->in($planID)
            ->andWhere('deleted')->eq('0')
            ->andWhere( 'id in (select subProjectID from '.TABLE_OUTSIDEPLANTASKS.' where deleted = 0)')
            ->orderBy($orderBy)
            ->fetchPairs();
        $pairs = array('0' => '') + $pairs;
        echo html::select('outsideSubProject[]', $pairs, $suboutPlanID, "class='form-control chosen' multiple onchange='setTaskField(this)'");
    }

    public function ajaxNewSubProjects($taskID = 0, $orderBy = 'id_desc')
    {

        $list = $this->dao->select('id,outsideProjectPlanID,subProjectID')->from(TABLE_OUTSIDEPLANTASKS)
            ->where('id')->in($taskID)
            ->andWhere('deleted')->eq('0')
            ->orderBy($orderBy)
            ->fetchAll();

        $outsidePlanID = [];
        $subProjectID = [];
        if($list){
            $outsidePlanID = array_unique(array_column($list,'outsideProjectPlanID'));
            $subProjectID = array_unique(array_column($list,'subProjectID'));

        }
        if($subProjectID){
            $ProjectPairs = $this->dao->select('id,name')->from(TABLE_OUTSIDEPLAN)
                ->where('id')->in($outsidePlanID)
//                ->andWhere('deleted')->eq('0')
                ->orderBy($orderBy)
                ->fetchPairs();
            $outsideIDsArr = array_keys($ProjectPairs);
            rsort($outsideIDsArr);
            $outsideIDs = implode(',',$outsideIDsArr);
            $outsideNames = implode(',',$ProjectPairs);
        }else{
            $outsideIDs = '';
            $outsideNames = '';
        }

        if($subProjectID){
            $subProjectPairs = $this->dao->select('id,subProjectName')->from(TABLE_OUTSIDEPLANSUBPROJECTS)
                ->where('id')->in($subProjectID)
//                ->andWhere('deleted')->eq('0')
                ->orderBy($orderBy)
                ->fetchPairs();
            $subOutsideIDsArr = array_keys($subProjectPairs);
            rsort($subOutsideIDsArr);
            $subOutsideIDs = implode(',',$subOutsideIDsArr);
            $subOutsideNames = implode(',',$subProjectPairs);

        }else{
            $subOutsideIDs = '';
            $subOutsideNames = '';
        }

        $resData = [
            'outsideIDs'=>$outsideIDs,
            'outsideNames'=>$outsideNames,
            'subOutsideIDs'=>$subOutsideIDs,
            'subOutsideNames'=>$subOutsideNames,
        ];
        $this->send($resData,'json');

       /* var_dump($subProjectPairs);
        var_dump($ProjectPairs);
        exit();
        $pairs = array('0' => '') + $pairs;*/
//        echo html::select('outsideSubProject[]', $pairs, $suboutPlanID, "class='form-control chosen' multiple onchange='setTaskField(this)'");
    }

    /**
     * TongYanQi 2022/9/15
     * 根据外部计划id获取子项目
     */
    public function ajaxTask($subProjectID = 0,$orderBy = 'id_desc',$taskID="")
    {

        $list = $this->dao->select('id,subProjectID,subTaskName')->from(TABLE_OUTSIDEPLANTASKS)
            ->where('subProjectID')->in($subProjectID)
            ->andWhere('deleted')->eq('0')
            ->orderBy($orderBy)
            ->fetchAll('');

        $select = '';
        $group = [];
        $pairs = [];
       foreach ($list as $task)
       {
           $group[$task->subProjectID][]= $task;
           $pairs[$task->id] = $task->subTaskName;
       }
       foreach ($group as $item){
           if(count($item) == 1) $select .= $item[0]->id.',';
       }
        trim($select,',');
        $pairs = array('0' => '') + $pairs;
        echo html::select('outsideTask[]', $pairs, $taskID, "class='form-control chosen' multiple");
    }

    /**
     * TongYanQi 2022/9/26
     * 内外部统计表
     */
    public function chart($year = '')
    {
        $this->loadModel('application');
        /** @var outsideplanModel $model */
        $model = $this->loadModel('outsideplan');
        $this->view->list = $model->getChart($year);
        $this->view->getYear = $year;
        $this->view->title = $this->lang->outsideplan->common;
        $this->display();
    }
}
