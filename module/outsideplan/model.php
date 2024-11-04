<?php
class outsideplanModel extends model
{
    /**
     * Project: chengfangjinke
     * Method: getList
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:51
     * Desc: This is the code comment. This method is called getList.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $browseType
     * @param $queryID
     * @param $orderBy
     * @param $pager
     * @return mixed
     */
    public function getList($browseType, $queryID, $orderBy, $pager)
    {
        $outsideplanQuery = '';
        if($browseType == 'bysearch')
        {
            $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : '';
            if($query)
            {
                $this->session->set('outsideplanQuery', $query->sql);
                $this->session->set('outsideplanForm', $query->form);
            }

            if($this->session->outsideplanQuery == false) $this->session->set('outsideplanQuery', ' 1 = 1');

            $outsideplanQuery = $this->session->outsideplanQuery;
        }

        if(strpos($outsideplanQuery,'`projectDept`')) {
            $queryArray = explode('`projectDept`', $outsideplanQuery);
            $subQuery = $this->str_replace_first('\' ', '\'))', $queryArray[1]);
            $outsideplanQuery = $queryArray[0] . ' (id in (select outsideProjectPlanID from ' . TABLE_OUTSIDEPLANTASKS . ' where subTaskBearDept'. $subQuery;
        }

        if(strpos($outsideplanQuery,'`projectUnit`')) {
            $queryArray = explode('`projectUnit`', $outsideplanQuery);
            $subQuery = $this->str_replace_first('\' ', '\'))', $queryArray[1]);
            $outsideplanQuery = $queryArray[0] . ' (id in (select outsideProjectPlanID from ' . TABLE_OUTSIDEPLANTASKS . ' where subTaskUnit'. $subQuery;
        }

        if(strpos($outsideplanQuery,'`subTaskName`')) {
            $queryArray = explode('`subTaskName`', $outsideplanQuery);
            $subQuery = $this->str_replace_first('\' ', '\'))', $queryArray[1]);
            $outsideplanQuery = $queryArray[0] . ' (id in (select outsideProjectPlanID from ' . TABLE_OUTSIDEPLANTASKS . ' where  deleted=0 and subTaskName'. $subQuery;
        }

        if(strpos($outsideplanQuery,'`subProjectDesc`')) {
            $queryArray = explode('`subProjectDesc`', $outsideplanQuery);
            $subQuery = $this->str_replace_first('\' ', '\'))', $queryArray[1]);
            $outsideplanQuery = $queryArray[0] . ' (id in (select outsideProjectPlanID from ' . TABLE_OUTSIDEPLANTASKS . ' where subTaskDesc'. $subQuery;
        }


        if(strpos($outsideplanQuery,'`subTaskBegin`')) {
            $queryArray = explode('`subTaskBegin`', $outsideplanQuery);
            $subQuery = $this->str_replace_first('\' ', '\'))', $queryArray[1]);
            $outsideplanQuery = $queryArray[0] . ' (id in (select outsideProjectPlanID from ' . TABLE_OUTSIDEPLANTASKS . ' where subTaskBegin'. $subQuery;
        }

        if(strpos($outsideplanQuery,'`subTaskEnd`')) {
            $queryArray = explode('`subTaskEnd`', $outsideplanQuery);
            $subQuery = $this->str_replace_first('\' ', '\'))', $queryArray[1]);
            $outsideplanQuery = $queryArray[0] . ' (id in (select outsideProjectPlanID from ' . TABLE_OUTSIDEPLANTASKS . ' where subTaskEnd'. $subQuery;
        }
        if(strpos($outsideplanQuery,'`subTaskDemandParty`')) {
            $queryArray = explode('`subTaskDemandParty`', $outsideplanQuery);
            $subQuery = $this->str_replace_first('\' ', '\'))', $queryArray[1]);
            $outsideplanQuery = $queryArray[0] . ' (id in (select outsideProjectPlanID from ' . TABLE_OUTSIDEPLANTASKS . ' where subTaskDemandParty'. $subQuery;
        }
        if(strpos($outsideplanQuery,'`subTaskDemandDeadline`')) {
            $queryArray = explode('`subTaskDemandDeadline`', $outsideplanQuery);
            $subQuery = $this->str_replace_first('\' ', '\'))', $queryArray[1]);
            $outsideplanQuery = $queryArray[0] . ' (id in (select outsideProjectPlanID from ' . TABLE_OUTSIDEPLANTASKS . ' where subTaskDemandDeadline'. $subQuery;
        }
        if(strpos($outsideplanQuery,'`subTaskDemandContact`')) {
            $queryArray = explode('`subTaskDemandContact`', $outsideplanQuery);
            $subQuery = $this->str_replace_first('\' ', '\'))', $queryArray[1]);
            $outsideplanQuery = $queryArray[0] . ' (id in (select outsideProjectPlanID from ' . TABLE_OUTSIDEPLANTASKS . ' where subTaskDemandContact'. $subQuery;
        }
        if(strpos($outsideplanQuery,'`subProjectName`')) {
            $queryArray = explode('`subProjectName`', $outsideplanQuery);
            $subQuery = $this->str_replace_first('\' ', '\'))', $queryArray[1]);
            $outsideplanQuery = $queryArray[0] . ' (id in (select outsideProjectPlanID from ' . TABLE_OUTSIDEPLANSUBPROJECTS . ' where deleted = 0 and subProjectName'. $subQuery;
        }

        $outsideplans = $this->dao->select('*')->from(TABLE_OUTSIDEPLAN)
            ->where('deleted')->eq(0)
            ->beginIF($browseType == 'all')->andWhere('status')->ne('deleted')->fi()
            ->beginIF($browseType != 'all' and $browseType != 'bysearch')->andWhere('status')->eq($browseType)->fi()
            ->beginIF($browseType == 'bysearch')->andWhere($outsideplanQuery)->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');
//            $this->dao->printSQL();
//            die();

        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'outsideplan', $browseType != 'bysearch');

        return $this->processPlan($outsideplans);
    }


    /**
     * TongYanQi 2022/9/22
     * 项目一览表
     */
    public function getOutLookList($projectPlans, $browseType, $queryID, $orderBy, $pager)
    {
        $outsideplanQuery = '';
        if($browseType == 'bysearch')
        {
            $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : '';
            if($query)
            {
                $this->session->set('outsideplanQuery', $query->sql);
                $this->session->set('outsideplanForm', $query->form);
            }

            if($this->session->outsideplanQuery == false) $this->session->set('outsideplanQuery', ' 1 = 1');

            $outsideplanQuery = $this->session->outsideplanQuery;

            if(strpos($outsideplanQuery,'`subProjectName`')) {
                $queryArray = explode('`subProjectName`', $outsideplanQuery);
                $subQuery = $this->str_replace_first('\' ', '\'))', $queryArray[1]);
                $outsideplanQuery = $queryArray[0] . ' (id in (select outsideProjectPlanID from ' . TABLE_OUTSIDEPLANSUBPROJECTS . ' where deleted = "0" AND subProjectName'. $subQuery;
            }
            if(strpos($outsideplanQuery,'`subTaskName`')) {
                $queryArray = explode('`subTaskName`', $outsideplanQuery);
                $subQuery = $this->str_replace_first('\' ', '\'))', $queryArray[1]);
                $outsideplanQuery = $queryArray[0] . ' (id in (select outsideProjectPlanID from ' . TABLE_OUTSIDEPLANTASKS . ' where deleted = "0" AND subTaskName'. $subQuery;
            }
            if(strpos($outsideplanQuery,'`subTaskUnit`')) {
                $queryArray = explode('`subTaskUnit`', $outsideplanQuery);
                $subQuery = $this->str_replace_first('\' ', '\'))', $queryArray[1]);
                $outsideplanQuery = $queryArray[0] . ' (id in (select outsideProjectPlanID from ' . TABLE_OUTSIDEPLANTASKS . ' where deleted = "0" AND subTaskUnit'. $subQuery;
            }
            if(strpos($outsideplanQuery,'`subTaskBearDept`')) {
                $queryArray = explode('`subTaskBearDept`', $outsideplanQuery);
                $subQuery = $this->str_replace_first('\' ', '\'))', $queryArray[1]);
                $outsideplanQuery = $queryArray[0] . ' (id in (select outsideProjectPlanID from ' . TABLE_OUTSIDEPLANTASKS . ' where deleted = "0" AND subTaskBearDept'. $subQuery;
            }
        }

        $outsideplans = $this->dao->select('id,name,code,begin,end,status,version,reviewStage')->from(TABLE_OUTSIDEPLAN)
            ->where('deleted')->eq(0)
            ->beginIF($browseType == 'all')->andWhere('status')->ne('deleted')->fi()
            ->beginIF($browseType != 'all' and $browseType != 'bysearch')->andWhere('status')->eq($browseType)->fi()
            ->beginIF($browseType == 'bysearch')->andWhere($outsideplanQuery)->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');

        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'outsideplan', $browseType != 'bysearch');

        $outsideplans = $this->processPlan($outsideplans, 1);


        $taskPlanList = [];

        //每个年度计划下面的(外部)子项/子任务
        foreach ($projectPlans as $plan)
        {
            if(empty($plan->outsideTask)) continue;
            $tasks = explode(',',$plan->outsideTask);
            foreach ($tasks as $taskId){
                $taskPlanList[$taskId][] = $plan;  //(外部)子项/子任务下的年度计划
            }
        }

        //外部年度计划对应的(外部)子项/子任务
        $tasks      = $this->getTasks('', array_keys($outsideplans));
        $subProjectTasks = [];
        foreach ($tasks as  $task)
        {
            $task->project[] = $taskPlanList[$task->id] ?? null; //把外部计划下面任务的内部计划加入外部计划的任务后面
            $task->row = empty($taskPlanList[$task->id]) ? 1 : count($taskPlanList[$task->id]); //把内部计划加入任务后面
            $subProjectTasks[$task->subProjectID][] = $task;
        }

        foreach ($outsideplans as $plan) //外部计划
        {
            foreach ($plan->children as &$subproject){ //子项目
                if(isset($subProjectTasks[$subproject->id])){
                    foreach ($subProjectTasks[$subproject->id] as $taskItem) //子任务
                    {
                        $subproject->tasks[] = $taskItem;
                        if(empty($subproject->row)) $subproject->row = 0;
                        $subproject->row += $taskItem->row; //子项名称列跨越几行
                    }

                } else {
                    $subproject->tasks[] = null;
                    $subproject->row = 1;
                }

                if(empty($plan->row)) $plan->row = 0;
                $plan->row += $subproject->row; //外部计划跨越几行
            }

        }
        return $outsideplans;
    }

    /**
     * TongYanQi 2022/9/22
     * 一览表导出数据
     */
    public function getOutLookExportData($browseType)
    {
        //所有的年度计划[id]->已立项的项目（projectInfo）
        $projectPlans      = $this->loadModel('projectplan')->getAllBrief();
        //所有用户
        $users = $this->loadModel('user')->getPairs('noletter');
        //所有的部门
        $depts = $this->loadModel('dept')->getOptionMenu();
        $this->loadModel('application');

        if($browseType == 'bysearch')
        {
            $query = '';
            if($query)
            {
                $this->session->set('outsideplanQuery', $query->sql);
                $this->session->set('outsideplanForm', $query->form);
            }

            if($this->session->outsideplanQuery == false) $this->session->set('outsideplanQuery', ' 1 = 1');

            $outsideplanQuery = $this->session->outsideplanQuery;

            if(strpos($outsideplanQuery,'`subProjectName`')) {
                $queryArray = explode('`subProjectName`', $outsideplanQuery);
                $subQuery = $this->str_replace_first('\' ', '\'))', $queryArray[1]);
                $outsideplanQuery = $queryArray[0] . ' (id in (select outsideProjectPlanID from ' . TABLE_OUTSIDEPLANSUBPROJECTS . ' where deleted = "0" AND subProjectName'. $subQuery;
            }
            if(strpos($outsideplanQuery,'`subTaskName`')) {
                $queryArray = explode('`subTaskName`', $outsideplanQuery);
                $subQuery = $this->str_replace_first('\' ', '\'))', $queryArray[1]);
                $outsideplanQuery = $queryArray[0] . ' (id in (select outsideProjectPlanID from ' . TABLE_OUTSIDEPLANTASKS . ' where deleted = "0" AND subTaskName'. $subQuery;
            }
            if(strpos($outsideplanQuery,'`subTaskUnit`')) {
                $queryArray = explode('`subTaskUnit`', $outsideplanQuery);
                $subQuery = $this->str_replace_first('\' ', '\'))', $queryArray[1]);
                $outsideplanQuery = $queryArray[0] . ' (id in (select outsideProjectPlanID from ' . TABLE_OUTSIDEPLANTASKS . ' where deleted = "0" AND subTaskUnit'. $subQuery;
            }
            if(strpos($outsideplanQuery,'`subTaskBearDept`')) {
                $queryArray = explode('`subTaskBearDept`', $outsideplanQuery);
                $subQuery = $this->str_replace_first('\' ', '\'))', $queryArray[1]);
                $outsideplanQuery = $queryArray[0] . ' (id in (select outsideProjectPlanID from ' . TABLE_OUTSIDEPLANTASKS . ' where deleted = "0" AND subTaskBearDept'. $subQuery;
            }
        }
        //所有外部计划
        $outsideplans   = $this->dao->select('id,name,code,begin,end,status,version,reviewStage')->from(TABLE_OUTSIDEPLAN)
            ->where('deleted')->eq(0)
            ->andWhere('status')->ne('deleted')
            ->beginIF($browseType == 'bysearch')->andWhere($outsideplanQuery)->fi()
            ->orderBy("id_desc")
            ->fetchAll('id');

        //所有外部计划子项目
        $outsideSubProjects = $this->dao->select('*')->from(TABLE_OUTSIDEPLANSUBPROJECTS)->where('deleted')->eq(0)->fetchall();
        foreach ($outsideSubProjects as &$outsideSubProject)
        {
            if(isset($outsideplans[$outsideSubProject->outsideProjectPlanID])){
                $substasks   = $this->dao->select('*')->from(TABLE_OUTSIDEPLANTASKS)->where('subProjectID')->eq($outsideSubProject->id)->andwhere('deleted')->eq(0)->fetchAll('id');
                $outsideSubProject->tasks = [];
                foreach ($substasks as $substask)
                {
                    //关联了(外部)子项/子任务的内部计划
                    $inPlans   = $this->dao->select('id')->from(TABLE_PROJECTPLAN)->where("(outsideTask = $substask->id or outsideTask like \"%,$substask->id,%\")")->andwhere('deleted')->eq(0)->fetchAll('id');
                    $substask->inplans = [];
                    foreach ($inPlans as $inPlan){
                        $substask->project[] = $projectPlans[$inPlan->id]; //(外部)子项/子任务下的年度计划
                    }
                    $outsideSubProject->tasks[] = $substask; //(外部)子项/子任务的(外部)子项/子任务
                }
                $outsideplans[$outsideSubProject->outsideProjectPlanID]->children[] = $outsideSubProject; //外部计划的子项目
            }
        }
        //$outsideplans[外部id]->children[子项目]->任务->内部项目;
        $outlook = [];
        foreach ($outsideplans as  $outsideplan){
            $item = new stdClass(); //每行必须新建对象 否者覆盖上一个
            $item->name = $outsideplan->name;
            $item->code = $outsideplan->code;
            $item->begin = $outsideplan->begin;
            $item->end = $outsideplan->end;
            $item->status = zget($this->lang->outsideplan->statusList, $outsideplan->status, '');
            $item->subProjectName = '';
            $item->subTaskName = '';
            $item->subTaskBearDept = '';
            $item->subTaskDemandParty = '';
            $item->projectPlanName = '';
            $item->projectPlanIsImportant = '';
            $item->projectPlanBegin = '';
            $item->projectPlanEnd = '';
            $item->projectPlanWorkload = '';
            $item->projectPlanNameBearDept = '';
            $item->projectPlanNameOwner = '';
            $item->projectPlanNamePhone = '';
            $item->projectPlanNameStatus = '';
            $item->projectCode = '';
            $item->projectPlanCode = '';
            $item->projectMembers = '';
            $item->projectDeptNames = '';
            $item->projectEstimate = '';
            $item->projectConsumed = '';
            $item->projectBudget = '';
            $item->projectProgress = '';

            foreach ($outsideplan->children as $sub) //有子项目的
            {
                $item2 = new stdClass(); //每行必须新建对象 并赋值 否者覆盖上一个
                foreach ($item as $k => $v){
                    $item2->$k = $v;
                }
                $item2->subProjectName = $sub->subProjectName;
                if(empty($sub->tasks)){ //没有任务
                    $outlook[] = $item2;
                    continue; //下一个子项目
                }
                foreach ($sub->tasks as $task) //有(外部)子项/子任务的
                {
                    $item3 = new stdClass();
                    foreach ($item2 as $k => $v){
                        $item3->$k = $v;
                    }
                    $item3->subTaskName = $task->subTaskName;
                    $item3->subTaskDesc = strip_tags(br2nl(html_entity_decode($task->subTaskDesc)));

                    $vlist = explode(',', $task->subTaskBearDept);
                    $arr = [];
                    foreach ($vlist as $itemv){
                        if(empty($itemv)) continue;
                        $arr[] = zget($this->lang->application->teamList, $itemv,'') ;
                    }
                    $item3->subTaskBearDept = implode(',', $arr);

                    $vlist = explode(',', $task->subTaskUnit);
                    $arr = [];
                    foreach ($vlist as $itemv){
                        if(empty($itemv)) continue;
                        $arr[] = zget($this->lang->outsideplan->subProjectUnitList, $itemv,'');
                    }
                    $item3->subTaskUnit = implode(',', $arr);
                    if(empty($task->project)){
                        $outlook[] = $item3;
                        continue; //下一个任务
                    }
                    foreach ($task->project as $project) //(外部)子项/子任务关联内部计划的 project是内部计划 不上项目 项目是projectInfo
                    {
                        $item4 = new stdClass();
                        foreach ($item3 as $k => $v){
                            $item4->$k = $v;
                        }
                        $item4->projectPlanName = $project->name;
                        $item4->projectPlanIsImportant = zget($this->lang->projectplan->isImportantList,$project->isImportant,'');
                        $item4->projectPlanBegin = $project->begin;
                        $item4->projectPlanEnd = $project->end;
                        $item4->projectPlanWorkload = $project->workload;
                        $bearDepts = isset($project->bearDept) ? explode(',', $project->bearDept) : [];
                        $item4->projectPlanNameBearDept = '';
                        foreach ($bearDepts as $dept) {
                            $item4->projectPlanNameBearDept .= zget($depts, $dept, '') . PHP_EOL;
                        }
                        $item4->projectPlanNameOwner = '';
                        $owners = isset($project->owner) ? explode(',', $project->owner) : [];
                        foreach ($owners as $owner) { $item4->projectPlanNameOwner .= zget($users, $owner, '') . PHP_EOL; }
                        $item4->projectPlanNamePhone = $project->phone;
                        $item4->projectPlanNameStatus = zget($this->lang->projectplan->insideStatusList,$project->insideStatus,'');
                        $item4->projectPlanCode = $project->planCode;
                        if(!empty($project->projectInfo)){ //年度计划有立项的
                            $item4->projectCode = $project->projectInfo->code;
                            $item4->planCode = $project->code;
                            $item4->projectMembers = $project->projectInfo->members;
                            $item4->projectDeptNames = $project->projectInfo->deptNames;
                            $item4->projectEstimate = $project->projectInfo->estimate;
                            $item4->projectConsumed = $project->projectInfo->consumed;
                            $item4->projectBudget = $project->projectInfo->budget;
                            $item4->projectProgress = $project->projectInfo->progress;
                        }
                        $outlook[] = $item4;
                    }
                }
            }
        }
        return $outlook;
    }

    /**
     * TongYanQi 2022/9/22
     * 一览表导出数据
     */
    public function getInlookExportData($browseType): array
    {
        /** @var projectplanModel $projectPlanModel */
        $projectPlanModel = $this->loadModel('projectplan');
        $this->app->loadClass('pager', $static = true);
        $pager = new pager(0, 100000, 0);
        $data = $projectPlanModel->getListInLook($browseType,0, 'id_desc', $pager);
        $outlook = [];
        $depts           = $this->loadModel('dept')->getOptionMenu();
        foreach ($data as $plan) {
            $item = new stdClass(); //每行必须新建对象 否者覆盖上一个
            $item->name                     = $plan->name;
            $item->code                     = $plan->code;
            $item->planCode                 = $plan->planCode;
            $item->mark                     = $plan->mark;
            $item->begin                    = $plan->begin;
            $item->end                      = $plan->end;
            $item->bearDeptStr = '';
            if(stripos($plan->bearDept,',') !== false){
                $bearDeptArr = explode(',',$plan->bearDept);
                foreach ($bearDeptArr as $dept){
                    $item->bearDeptStr .= zget($depts,$dept).PHP_EOL;
                }
            }else{
                $item->bearDeptStr = zget($depts,$plan->bearDept);
            }
            $item->status                   = zget($this->lang->projectplan->statusList, $plan->status);
            $item->insideStatus             = zget($this->lang->projectplan->insideStatusList, $plan->insideStatus);
            $item->estimate                 = $plan->estimate;
            $item->consumed                 = $plan->consumed;
            $item->budget                   = $plan->budget;
            $item->progress                 = $plan->progress;
            $item->subTaskName              = "";
            $item->subProjectName             = "";
            $item->outsideProjectPlanName     = "";
            $item->outsideProjectPlanCode     = "";
            $item->outsideProjectPlanStatus     = "";
            $item->outsideProjectPlanBegin     = "";
            $item->outsideProjectPlanEnd     = "";
            $item->subTaskUnit              = "";
            $item->subTaskBearDept          = "";
            $samePlanNum = 0;
            foreach ($plan->outTasks as $out){
                $item2 = new stdClass();
                foreach ($item as $k => $v){
                    $item2->$k = $samePlanNum == 0 ? $v : "";
                }
                $samePlanNum++;
                $item2->subTaskName               = $out->subTaskName;
                $item2->subProjectName            = $out->subProjectName;
                $item2->outsideProjectPlanName    = $out->outsideProjectPlanName.'/'.$item2->subProjectName.'/'.$item2->subTaskName;
                $item2->outsideProjectPlanCode    = $out->outsideProjectPlanCode;
                $item2->outsideProjectPlanStatus  = $out->outsideProjectPlanStatus;
                $item2->outsideProjectPlanBegin   = $out->outsideProjectPlanBegin;
                $item2->outsideProjectPlanEnd     = $out->outsideProjectPlanEnd;
                $item2->subTaskUnit             = $out->subTaskUnit;
                $item2->subTaskBearDept         = $out->subTaskBearDept;
                $item2->subTaskBegin         = $out->subTaskBegin;
                $item2->subTaskEnd         = $out->subTaskEnd;
                $outlook[] = $item2;
            }
            if(empty($plan->outTasks)){
                $outlook[] = $item;
            }
        }
        return $outlook;
    }
    /**
     * Project: chengfangjinke
     * Method: getCreationByID
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:51
     * Desc: This is the code comment. This method is called getCreationByID.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $planID
     * @return false
     */
    public function getCreationByID($planID)
    {
        $creation = $this->dao->select('*')->from(TABLE_PROJECTCREATION)->where('plan')->eq($planID)->fetch();
        if($creation)
        {
            $creation = $this->loadModel('file')->replaceImgURL($creation, 'background,range,goal,stakeholder,verify');
            $creation->files = $this->loadModel('file')->getByObject('projectcreation', $creation->id);
            return $creation;
        }
        return false;
    }


    public function copySub(){

        $postinfo = fixer::input('post')->get();
        if(!isset($postinfo->sourcesubProjectId) || !$postinfo->sourcesubProjectId){
            dao::$errors[] = vsprintf($this->lang->outsideplan->noEmpty,$this->lang->outsideplan->sourcesubProjectId);
            return false;
        }

        if(!isset($postinfo->outsideplanID) || !$postinfo->outsideplanID){
            dao::$errors['outsideplanID'] = vsprintf($this->lang->outsideplan->noEmpty,$this->lang->outsideplan->outsideplanID);
            return false;
        }

        $outsidePlan = $this->getSimpleByID($postinfo->outsideplanID);
        if(!$outsidePlan){
            dao::$errors[] = $this->lang->outsideplan->outsideplanError;
            return false;
        }
        $maintainers = explode(',', $outsidePlan->maintainers.',admin');//admin 和 维护人员
        if(!in_array($this->app->user->account,$maintainers)){
            dao::$errors[] = $this->lang->outsideplan->notMaintainersCopyToError;
            return false;
        }
        //
        $outsidePlanSub = $this->getSubProject($postinfo->sourcesubProjectId);
        if(!$outsidePlanSub){
            dao::$errors[] = $this->lang->outsideplan->outsideplanSubError;
            return false;
        }

        $sourceOutsidePlan = $this->getSimpleByID($outsidePlanSub->outsideProjectPlanID);
        $sourceMaintainers = explode(',', $sourceOutsidePlan->maintainers.',admin');//admin 和 维护人员
        if(!in_array($this->app->user->account,$sourceMaintainers)){
            dao::$errors[] = $this->lang->outsideplan->notMaintainersCopySourceError;
            return false;
        }
        if(isset($outsidePlanSub->id)){
            unset($outsidePlanSub->id);
        }
        $sourcePlanid = $outsidePlanSub->outsideProjectPlanID;
        $outsidePlanSub->outsideProjectPlanID = $postinfo->outsideplanID;
        try{
            $this->dao->begin();
            $outsidePlanSub->subProjectName = $outsidePlanSub->subProjectName.'=>复制';
            $this->dao->insert(TABLE_OUTSIDEPLANSUBPROJECTS)->data($outsidePlanSub)->exec();
            $newsubid = $this->dao->lastInsertID();
            if(dao::isError()){
                $this->dao->rollback();
                return false;
            }

            $taskList = $this->getTasksBySubID($postinfo->sourcesubProjectId);
            if($taskList){
                foreach($taskList as $task){
                    unset($task->id);
                    $task->outsideProjectPlanID = $postinfo->outsideplanID;
                    $task->subProjectID = $newsubid;
                    $task->subTaskName = $task->subTaskName.'=>复制';
                    $this->dao->insert(TABLE_OUTSIDEPLANTASKS)->data($task)->exec();

                }

            }
            if(dao::isError()){
                $this->dao->rollback();
                return false;
            }
            $this->dao->commit();
            return ['planid'=>$postinfo->outsideplanID,'comment'=>"来源：外部年度计划：".$sourcePlanid." 外部年度计划子项ID:".$postinfo->sourcesubProjectId." 目标年度计划：".$postinfo->outsideplanID];
        }catch (Error $e){
            $this->dao->rollback();
            dao::$errors[] = $e->getMessage();
            return false;
        }



    }

    public function copyTask(){
        $postinfo = fixer::input('post')->get();

        if(!isset($postinfo->sourceTaskId) || !$postinfo->sourceTaskId){
            dao::$errors[] = vsprintf($this->lang->outsideplan->noEmpty,$this->lang->outsideplan->sourceTaskId);
            return false;
        }

        if(!isset($postinfo->outsideplanID) || !$postinfo->outsideplanID){
            dao::$errors['outsideplanID'] = vsprintf($this->lang->outsideplan->noEmpty,$this->lang->outsideplan->outsideplanID);
            return false;
        }
        if(!isset($postinfo->outsideplanSubID) || !$postinfo->outsideplanSubID){
            dao::$errors['outsideplanSubID'] = vsprintf($this->lang->outsideplan->noEmpty,$this->lang->outsideplan->outsideplanSubID);
            return false;
        }

        $outsidePlan = $this->getSimpleByID($postinfo->outsideplanID);
        if(!$outsidePlan){
            dao::$errors[] = $this->lang->outsideplan->outsideplanError;
            return false;
        }
        $maintainers = explode(',', $outsidePlan->maintainers.',admin');//admin 和 维护人员
        if(!in_array($this->app->user->account,$maintainers)){
            dao::$errors[] = $this->lang->outsideplan->notMaintainersCopyToError;
            return false;
        }
        //
        $outsidePlanSub = $this->getSubProject($postinfo->outsideplanSubID);
        if(!$outsidePlanSub){
            dao::$errors[] = $this->lang->outsideplan->outsideplanSubError;
            return false;
        }

        if($outsidePlanSub->outsideProjectPlanID != $postinfo->outsideplanID){
            dao::$errors[] = $this->lang->outsideplan->outsideplanChildSubError;
            return false;
        }


        $task = $this->getTasksByID($postinfo->sourceTaskId);
        if(!$task){
            dao::$errors[] = $this->lang->outsideplan->outsideplanTaskError;
            return false;
        }
        $sourceOutsidePlan = $this->getSimpleByID($task->outsideProjectPlanID);
        $sourceMaintainers = explode(',', $sourceOutsidePlan->maintainers.',admin');//admin 和 维护人员
        if(!in_array($this->app->user->account,$sourceMaintainers)){
            dao::$errors[] = $this->lang->outsideplan->notMaintainersCopySourceError;
            return false;
        }

        if(isset($task->id)){
            unset($task->id);
        }
        $sourcePlanid = $task->outsideProjectPlanID;
        $sourceSubPlanid = $task->subProjectID;
        $task->outsideProjectPlanID = $postinfo->outsideplanID;
        $task->subProjectID = $postinfo->outsideplanSubID;
        $task->subTaskName = $task->subTaskName.'=>复制';
        try{
            $this->dao->begin();
            $this->dao->insert(TABLE_OUTSIDEPLANTASKS)->data($task)->exec();
            if(dao::isError()){
                $this->dao->rollback();
                return false;
            }
            $this->dao->commit();
            return ['planid'=>$postinfo->outsideplanID,'comment'=>"来源：外部年度计划：".$sourcePlanid." 外部年度计划子项ID:".$sourceSubPlanid." 外部任务ID：".$postinfo->sourceTaskId." 目标外部年度计划：".$postinfo->outsideplanID." 项目子项：".$postinfo->outsideplanSubID];
        }catch (Error $e){
            $this->dao->rollback();
            dao::$errors[] = $e->getMessage();
            return false;
        }
    }


    public function moveTask($planID){
        $postinfo = fixer::input('post')->get();

        if(!isset($postinfo->sourceTaskId) || !$postinfo->sourceTaskId){
            dao::$errors[] = vsprintf($this->lang->outsideplan->noEmpty,$this->lang->outsideplan->sourceTaskId);
            return false;
        }

        if(!isset($postinfo->outsideplanID) || !$postinfo->outsideplanID){
            dao::$errors['outsideplanID'] = vsprintf($this->lang->outsideplan->noEmpty,$this->lang->outsideplan->outsideplanID);
            return false;
        }
        if(!isset($postinfo->outsideplanSubID) || !$postinfo->outsideplanSubID){
            dao::$errors['outsideplanSubID'] = vsprintf($this->lang->outsideplan->noEmpty,$this->lang->outsideplan->outsideplanSubID);
            return false;
        }

        $outsidePlan = $this->getSimpleByID($postinfo->outsideplanID);
        if(!$outsidePlan){
            dao::$errors[] = $this->lang->outsideplan->outsideplanError;
            return false;
        }
        $maintainers = explode(',', $outsidePlan->maintainers.',admin');//admin 和 维护人员
        if(!in_array($this->app->user->account,$maintainers)){
            dao::$errors[] = $this->lang->outsideplan->notMaintainersMoveToError;
            return false;
        }
        //
        $outsidePlanSub = $this->getSubProject($postinfo->outsideplanSubID);
        if(!$outsidePlanSub){
            dao::$errors[] = $this->lang->outsideplan->outsideplanSubError;
            return false;
        }

        if($outsidePlanSub->outsideProjectPlanID != $postinfo->outsideplanID){
            dao::$errors[] = $this->lang->outsideplan->outsideplanChildSubError;
            return false;
        }


        $task = $this->getTasksByID($postinfo->sourceTaskId);
        if(!$task){
            dao::$errors[] = $this->lang->outsideplan->outsideplanTaskError;
            return false;
        }
        $sourceOutsidePlan = $this->getSimpleByID($task->outsideProjectPlanID);
        $sourceMaintainers = explode(',', $sourceOutsidePlan->maintainers.',admin');//admin 和 维护人员
        if(!in_array($this->app->user->account,$sourceMaintainers)){
            dao::$errors[] = $this->lang->outsideplan->notMaintainersMoveSourceError;
            return false;
        }
        if($task->outsideProjectPlanID == $postinfo->outsideplanID && $task->subProjectID == $postinfo->outsideplanSubID){
            dao::$errors[] = $this->lang->outsideplan->outsideplanAndSubNoChangeError;
            return false;
        }

        //(外部)子项/子任务更新数据
        $uptaskdata = [
            'outsideProjectPlanID'=>$postinfo->outsideplanID,
            'subProjectID'=>$postinfo->outsideplanSubID
        ];

        $sourcePlanid = $task->subProjectID;
        $sourceSubPlanid = $task->outsideProjectPlanID;
        $subTaskList = $this->getTasksBySubID($task->subProjectID);
        $outSubPlanList = $this->getSubProjectsByParentId($task->outsideProjectPlanID);
//            a($subTaskList);
        //当前任务子项目下所有的任务id.
        $subTaskListIDArr = array_column($subTaskList,'id');
        $outSubPlanListIDArr = array_column($outSubPlanList,'id');


        try{
            $this->dao->begin();
            $this->dao->update(TABLE_OUTSIDEPLANTASKS)->data($uptaskdata)->where('id')->eq($postinfo->sourceTaskId)->exec();
            //查询关联的年度计划
            $projectPlanList = $this->loadModel("projectplan")->getPlanByTaskID($postinfo->sourceTaskId);
            if($projectPlanList){
                foreach ($projectPlanList as $plan){

                    $outsideProjectArr = explode(',',trim($plan->outsideProject,','));
                    $outsideSubProjectArr = explode(',',trim($plan->outsideSubProject,','));
                    $outsideTaskArr = explode(',',trim($plan->outsideTask,','));

                    //如果指绑定了一个  则可以直接更新层级关系

                    if(count($outsideProjectArr) == 1 and count($outsideSubProjectArr) == 1){

                        $upPlanData = [
                            'outsideProject' => ','.$postinfo->outsideplanID.',',
                            'outsideSubProject' => ','.$postinfo->outsideplanSubID.','
                        ];

                    }else{

                        //从子项目下所有任务中 删除任务当前任务。并和年度计划的任务取交集，如果有交集，父级则需要保留，无交集父级则删除。
                        $otherTaskID = array_diff($subTaskListIDArr,[$postinfo->sourceTaskId]);
                        //如果存在其他的任务id，则需要保留父级(项目子项)且外部年度计划时一定要保留,否则删除父级(项目子项)并判断是否需要删除爷爷级(外部年度计划id)，然后替换为新的绑定关系。
                        $outplanTaskidArr = array_intersect($outsideTaskArr,$otherTaskID);
                        if(!$outplanTaskidArr){
                            //删除年度计划 关联外部子项 id.
                            $outsideSubProjectArr = array_diff($outsideSubProjectArr,[$task->subProjectID]);

                            $otherOutsideSubProjectArr = array_diff($outSubPlanListIDArr,[$task->subProjectID]);

                            $subPlanidArr = array_intersect($outsideSubProjectArr,$otherOutsideSubProjectArr);
                            if(!$subPlanidArr){
                                //删除年度计划 关联外部年度计划 id.
                                $outsideProjectArr = array_diff($outsideProjectArr,[$task->outsideProjectPlanID]);
                            }

                        }

                        if(!in_array($postinfo->outsideplanID,$outsideProjectArr)){
                            $outsideProjectArr[] = $postinfo->outsideplanID;
                        }
                        if(!in_array($postinfo->outsideplanSubID,$outsideSubProjectArr)){
                            $outsideSubProjectArr[] = $postinfo->outsideplanSubID;
                        }

                        $upPlanData = [
                            'outsideProject' => ','.implode(',',$outsideProjectArr).',',
                            'outsideSubProject' => ','.implode(',',$outsideSubProjectArr).','
                        ];
                        /*a($plan->id);
                        a(['outsideProject' =>$plan->outsideProject,'outsideSubProject' =>$plan->outsideSubProject]);
                        a("after");
                        a($upPlanData);*/
                    }


                    //更新年度计划
                    $this->dao->update(TABLE_PROJECTPLAN)->data($upPlanData)->where('id')->eq($plan->id)->exec();

                }
            }

            //更新 年度计划变更记录。

            $planChangeList = $this->loadModel("projectplan")->getChangeListByStatus();
            if($planChangeList){
                foreach ($planChangeList as $planchange){
                    $content = json_decode($planchange->content);


                    if(!isset($content->outsideTask) || !$content->outsideTask){
                        continue;
                    }
//                a($content);
                    $changeOutsideTaskIDList = explode(',',trim($content->outsideTask,','));
                    if(!in_array($postinfo->sourceTaskId,$changeOutsideTaskIDList)){
                        continue;

                    }

                    //变更记录中 关联(外部)项目/任务信息
                    $changeOutsideProjectArr = explode(',',trim($content->outsideProject,','));
                    $changeOutsideSubProjectArr = explode(',',trim($content->outsideSubProject,','));
                    //如果只绑定了一个  则可以直接更新层级关系
                    if(count($changeOutsideProjectArr) == 1 and count($changeOutsideSubProjectArr) == 1){
                        $content->outsideProject    = ','.$postinfo->outsideplanID.',';
                        $content->outsideSubProject = ','.$postinfo->outsideplanSubID.',';

                    }else{

                        //获取当前任务子项下其他的(外部)子项/子任务。
                        $otherTaskID = array_diff($subTaskListIDArr,[$postinfo->sourceTaskId]);
                        //变更记录中的(外部)子项/子任务是否和子项下其他的任务有交集。 如果无交集可清理该子项。
                        $changeOutplanTaskidArr = array_intersect($changeOutsideTaskIDList,$otherTaskID);
                        if(!$changeOutplanTaskidArr){
                            //删除年度计划 关联外部子项 id.
                            $changeOutsideSubProjectArr = array_diff($changeOutsideSubProjectArr,[$task->subProjectID]);
                            $otherOutsideSubProjectArr = array_diff($outSubPlanListIDArr,[$task->subProjectID]);

                            $subPlanidArr = array_intersect($changeOutsideSubProjectArr,$otherOutsideSubProjectArr);
                            if(!$subPlanidArr){
                                //删除年度计划 关联外部年度计划 id.
                                $changeOutsideProjectArr = array_diff($changeOutsideProjectArr,[$task->outsideProjectPlanID]);
                            }
                        }

                        if(!in_array($postinfo->outsideplanID,$changeOutsideProjectArr)){
                            $changeOutsideProjectArr[] = $postinfo->outsideplanID;
                        }
                        if(!in_array($postinfo->outsideplanSubID,$changeOutsideSubProjectArr)){
                            $changeOutsideSubProjectArr[] = $postinfo->outsideplanSubID;
                        }


                        $content->outsideProject = ','.implode(',',$changeOutsideProjectArr).',';
                        $content->outsideSubProject = ','.implode(',',$changeOutsideSubProjectArr).',';

                    }
                    $planChangeData = [
                        'content'=> json_encode($content)
                    ];
                    $this->dao->update(TABLE_PROJECTPLANCHANGE)->data($planChangeData)->where('id')->eq($planchange->id)->exec();

                }
            }

            if(dao::isError()){
                $this->dao->rollback();
                return false;
            }
            $this->dao->commit();
            return [
                'moveout'=>['planid'=>$planID,'comment'=>"移出来源：外部年度计划：".$sourcePlanid." 外部年度计划子项ID:".$sourceSubPlanid." 外部任务ID：".$postinfo->sourceTaskId." 目标外部年度计划：".$postinfo->outsideplanID." 项目子项：".$postinfo->outsideplanSubID],
                'movein' =>['planid'=>$postinfo->outsideplanID,'comment'=>"移入来源：外部年度计划：".$sourcePlanid." 外部年度计划子项ID:".$sourceSubPlanid." 外部任务ID：".$postinfo->sourceTaskId." 目标外部年度计划：".$postinfo->outsideplanID." 项目子项：".$postinfo->outsideplanSubID]
            ];
        }catch (Error $e){
            $this->dao->rollback();
            dao::$errors[] = $e->getMessage();
            return false;
        }


    }



    public function moveSub($planID){
        $postinfo = fixer::input('post')->get();

        if(!isset($postinfo->sourcesubProjectId) || !$postinfo->sourcesubProjectId){
            dao::$errors[] = vsprintf($this->lang->outsideplan->noEmpty,$this->lang->outsideplan->sourcesubProjectId);
            return false;
        }

        if(!isset($postinfo->outsideplanID) || !$postinfo->outsideplanID){
            dao::$errors['outsideplanID'] = vsprintf($this->lang->outsideplan->noEmpty,$this->lang->outsideplan->outsideplanID);
            return false;
        }

        $outsidePlan = $this->getSimpleByID($postinfo->outsideplanID);
        if(!$outsidePlan){
            dao::$errors[] = $this->lang->outsideplan->outsideplanError;
            return false;
        }
        $maintainers = explode(',', $outsidePlan->maintainers.',admin');//admin 和 维护人员
        if(!in_array($this->app->user->account,$maintainers)){
            dao::$errors[] = $this->lang->outsideplan->notMaintainersMoveToError;
            return false;
        }
        $subplanInfo = $this->getSubProject($postinfo->sourcesubProjectId);
        if($subplanInfo->outsideProjectPlanID == $postinfo->outsideplanID){
            dao::$errors[] = $this->lang->outsideplan->outsideplanAndSubNoChangeError;
            return false;
        }
        $sourcePlanid = $subplanInfo->outsideProjectPlanID;

        $sourceOutsidePlan = $this->getSimpleByID($subplanInfo->outsideProjectPlanID);
        $sourceMaintainers = explode(',', $sourceOutsidePlan->maintainers.',admin');//admin 和 维护人员
        if(!in_array($this->app->user->account,$sourceMaintainers)){
            dao::$errors[] = $this->lang->outsideplan->notMaintainersMoveSourceError;
            return false;
        }
        // 当前外部子项的所有兄弟
        $outSubPlanList = $this->getSubProjectsByParentId($subplanInfo->outsideProjectPlanID);
//            a($subTaskList);
        //当前任务子项目下所有的任务id.

        $outSubPlanListIDArr = array_column($outSubPlanList,'id');


        try{
            $this->dao->begin();
            $upSubPlanData = [
                'outsideProjectPlanID'=>$postinfo->outsideplanID
            ];
            $this->dao->update(TABLE_OUTSIDEPLANSUBPROJECTS)->data($upSubPlanData)->where('id')->eq($postinfo->sourcesubProjectId)->exec();
            $uptaskdata = [
                'outsideProjectPlanID'=>$postinfo->outsideplanID,
            ];
            $this->dao->update(TABLE_OUTSIDEPLANTASKS)->data($uptaskdata)->where('subProjectID')->eq($postinfo->sourcesubProjectId)->exec();
            //查询关联的年度计划
            $projectPlanList = $this->loadModel("projectplan")->getPlanBySubID($postinfo->sourcesubProjectId);

            if($projectPlanList){
                foreach ($projectPlanList as $plan){

                    $outsideProjectArr = explode(',',trim($plan->outsideProject,','));
                    $outsideSubProjectArr = explode(',',trim($plan->outsideSubProject,','));

                    //如果指绑定了一个  则可以直接更新层级关系

                    if(count($outsideProjectArr) == 1 ){

                        $upPlanData = [
                            'outsideProject' => ','.$postinfo->outsideplanID.','
                        ];

                    }else{

                        //删除年度计划当前子项，然后看看有没有剩余的
                        $otherOutsideSubProjectArr = array_diff($outSubPlanListIDArr,[$subplanInfo->id]);
                        $subPlanidArr = array_intersect($outsideSubProjectArr,$otherOutsideSubProjectArr);
                        if(!$subPlanidArr){
                            //删除年度计划 关联外部年度计划 id.
                            $outsideProjectArr = array_diff($outsideProjectArr,[$subplanInfo->outsideProjectPlanID]);

                        }

                        if(!in_array($postinfo->outsideplanID,$outsideProjectArr)){
                            $outsideProjectArr[] = $postinfo->outsideplanID;
                        }

                        $upPlanData = [
                            'outsideProject' => ','.implode(',',$outsideProjectArr).','
                        ];
                        /*a($plan->id);
                        a(['outsideProject' =>$plan->outsideProject]);
                        a("after");
                        a($upPlanData);*/
                    }


                    //更新年度计划
                $this->dao->update(TABLE_PROJECTPLAN)->data($upPlanData)->where('id')->eq($plan->id)->exec();

                }
            }

            //更新 年度计划变更记录。

            $planChangeList = $this->loadModel("projectplan")->getChangeListByStatus();
            if($planChangeList){
                foreach ($planChangeList as $planchange){
                    $content = json_decode($planchange->content);


                    if(!isset($content->outsideSubProject) || !$content->outsideSubProject){
                        continue;
                    }

                    $changeOutsideIDList = explode(',',trim($content->outsideSubProject,','));
                    if(!in_array($postinfo->sourcesubProjectId,$changeOutsideIDList)){
                        continue;

                    }

                    //变更记录中 关联(外部)项目/任务信息
                    $changeOutsideProjectArr = explode(',',trim($content->outsideProject,','));
                    $changeOutsideSubProjectArr = explode(',',trim($content->outsideSubProject,','));
                    //如果只绑定了一个  则可以直接更新层级关系
                    if(count($changeOutsideProjectArr) == 1 ){
                        $content->outsideProject    = ','.$postinfo->outsideplanID.',';


                    }else{

                        $otherOutsideSubProjectArr = array_diff($outSubPlanListIDArr,[$subplanInfo->id]);

                        $subPlanidArr = array_intersect($changeOutsideSubProjectArr,$otherOutsideSubProjectArr);
                        if(!$subPlanidArr){
                            //删除年度计划 关联外部年度计划 id.
                            $changeOutsideProjectArr = array_diff($changeOutsideProjectArr,[$subplanInfo->outsideProjectPlanID]);
                        }

                        if(!in_array($postinfo->outsideplanID,$changeOutsideProjectArr)){
                            $changeOutsideProjectArr[] = $postinfo->outsideplanID;
                        }

                        $content->outsideProject = ','.implode(',',$changeOutsideProjectArr).',';

                    }

                    $planChangeData = [
                        'content'=> json_encode($content)
                    ];
                $this->dao->update(TABLE_PROJECTPLANCHANGE)->data($planChangeData)->where('id')->eq($planchange->id)->exec();

                }
            }

            if(dao::isError()){
                $this->dao->rollback();
                return false;
            }
            $this->dao->commit();
            return [
                'moveout'=>['planid'=>$planID,'comment'=>"移出来源：外部年度计划：".$sourcePlanid." 外部年度计划子项ID:".$postinfo->sourcesubProjectId." 目标年度计划：".$postinfo->outsideplanID],
                'movein'=>['planid'=>$postinfo->outsideplanID,'comment'=>"移入来源：外部年度计划：".$sourcePlanid." 外部年度计划子项ID:".$postinfo->sourcesubProjectId." 目标年度计划：".$postinfo->outsideplanID]
            ];
        }catch (Error $e){
            $this->dao->rollback();
            dao::$errors[] = $e->getMessage();
            return false;
        }


    }
    public function checkTaskDate(){

        $postinfo = fixer::input('post')->get();
        if(!isset($postinfo->sourceTaskId) || !$postinfo->sourceTaskId){
            dao::$errors[] = vsprintf($this->lang->outsideplan->noEmpty,$this->lang->outsideplan->sourceTaskId);
            return '';
        }

        if(!isset($postinfo->outsideplanID) || !$postinfo->outsideplanID){
            dao::$errors['outsideplanID'] = vsprintf($this->lang->outsideplan->noEmpty,$this->lang->outsideplan->outsideplanID);
            return '';
        }

        $outsidePlan = $this->getSimpleByID($postinfo->outsideplanID);
        if(!$outsidePlan){
            dao::$errors[] = $this->lang->outsideplan->outsideplanError;
            return '';
        }
        //

        $task = $this->getTasksByID($postinfo->sourceTaskId);
        if(!$task){
            dao::$errors[] = $this->lang->outsideplan->outsideplanTaskError;
            return '';
        }

        $notice = '';
        if($task->subTaskBegin < $outsidePlan->begin){
            $notice .= "当前的(外部)子项/子任务计划开始时间(".$task->subTaskBegin.")小于(外部)项目/任务计划开始时间(".$outsidePlan->begin.")。".PHP_EOL;
        }
        if($task->subTaskEnd > $outsidePlan->end){
            $notice .= "当前的(外部)子项/子任务计划完成时间(".$task->subTaskEnd.")大于(外部)项目/任务计划完成时间(".$outsidePlan->end.")。";
        }
        return $notice;

    }
    public function checkBySubTaskDate(){

        $postinfo = fixer::input('post')->get();
        if(!isset($postinfo->sourcesubProjectId) || !$postinfo->sourcesubProjectId){
            dao::$errors[] = vsprintf($this->lang->outsideplan->noEmpty,$this->lang->outsideplan->sourcesubProjectId);
            return '';
        }

        if(!isset($postinfo->outsideplanID) || !$postinfo->outsideplanID){
            dao::$errors['outsideplanID'] = vsprintf($this->lang->outsideplan->noEmpty,$this->lang->outsideplan->outsideplanID);
            return '';
        }

        $outsidePlan = $this->getSimpleByID($postinfo->outsideplanID);
        if(!$outsidePlan){
            dao::$errors[] = $this->lang->outsideplan->outsideplanError;
            return '';
        }
        //

        $subTaskList = $this->getTasksBySubID($postinfo->sourcesubProjectId);
        $notice = '';
        if($subTaskList){
            foreach ($subTaskList as $task){
                if($task->subTaskBegin < $outsidePlan->begin){
                    $notice .= "当前的(外部)子项/子任务计划开始时间(".$task->subTaskBegin.")小于(外部)项目/任务计划开始时间(".$outsidePlan->begin.")。".PHP_EOL;
                }
                if($task->subTaskEnd > $outsidePlan->end){
                    $notice .= "当前的(外部)子项/子任务计划完成时间(".$task->subTaskEnd.")大于(外部)项目/任务计划完成时间(".$outsidePlan->end.")。".PHP_EOL;
                }
            }
        }




        return $notice;

    }
    /**
     * Project: chengfangjinke
     * Method: getPairs
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:51
     * Desc: This is the code comment. This method is called getPairs.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @return mixed
     */
    public function getPairs()
    {
        return $this->dao->select('id, name')->from(TABLE_OUTSIDEPLAN)->where('deleted')->eq(0)->orderBy("id_desc")->fetchPairs();
    }

    public function getPairsBymaintainers()
    {
        return $this->dao->select('id, name')->from(TABLE_OUTSIDEPLAN)->where('deleted')->eq(0)->andWhere("find_in_set('{$this->app->user->account}',maintainers)")->orderBy("id_desc")->fetchPairs();
    }


    public function getPairsHavingTasks()
    {
        return $this->dao->select('id, name')->from(TABLE_OUTSIDEPLAN)->where('deleted')->eq(0)->andwhere('id in (select outsideProjectPlanID from '.TABLE_OUTSIDEPLANTASKS.' where deleted = 0)')->fetchPairs();
    }

    public function getPairsByID($ids)
    {
        return $this->dao->select('id, name')->from(TABLE_OUTSIDEPLAN)->where('deleted')->eq(0)
            ->beginIF($ids)->andWhere('id')->in($ids)->fi()
            ->orderBy('id_desc')
            ->fetchPairs();
    }

    public function getAllBrief()
    {
        return $this->dao->select('id, name, status, code, begin, end')->from(TABLE_OUTSIDEPLAN)->where('deleted')->eq(0)->fetchall('id');
    }
    /**
     * Project: chengfangjinke
     * Method: getByID
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:51
     * Desc: This is the code comment. This method is called getByID.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $planID
     * @return mixed
     */
    public function getByID($planID)
    {
        $plan = $this->dao->select('*')->from(TABLE_OUTSIDEPLAN)->where('id')->eq($planID)->fetch();
        $plan->maintainers = trim($plan->maintainers,',');
        $plan->subprojects = $this->dao->select('*')->from(TABLE_OUTSIDEPLANSUBPROJECTS)->where('outsideProjectPlanID')->eq($planID)->andwhere('deleted')->eq(0)->fetchall('id');
        $plan = $this->loadModel('file')->replaceImgURL($plan, $this->config->outsideplan->editor->create['id']);
        $plan->files = $this->loadModel('file')->getByObject('outsideplan', $plan->id);

        $plan->creation = $this->getCreationByID($planID);
        return $plan;
    }

    public function getSimpleByID($planID,$field='*')
    {
        $plan = $this->dao->select($field)->from(TABLE_OUTSIDEPLAN)->where('id')->eq($planID)->fetch();

        $plan = $this->loadModel('file')->replaceImgURL($plan, $this->config->outsideplan->editor->create['id']);

        return $plan;
    }

    public function getByProjectID($projectID)
    {
        $plan = $this->dao->select('*')->from(TABLE_OUTSIDEPLAN)->where('project')->eq($projectID)->fetch();
        $plan = $this->loadModel('file')->replaceImgURL($plan, 'content');
        return $plan;
    }
    /**
     * Project: chengfangjinke
     * Method: buildSearchForm
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:51
     * Desc: This is the code comment. This method is called buildSearchForm.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $queryID
     * @param $actionURL
     */
    public function buildSearchForm($queryID, $actionURL)
    {
        $this->config->outsideplan->search['actionURL'] = $actionURL;
        $this->config->outsideplan->search['queryID']   = $queryID;
        $this->config->outsideplan->search['params']['line']['values']     = array('' => '') + $this->loadModel('product')->getLinePairs();
        $this->config->outsideplan->search['params']['app']['values']      = array('' => '') + $this->loadModel('application')->getapplicationNameCodePairs();
        $this->config->outsideplan->search['params']['bearDept']['values'] = array('' => '') + $this->loadModel('dept')->getOptionMenu();

        $this->loadModel('search')->setSearchParams($this->config->outsideplan->search);
    }

    public function buildSearchFormOutLook($queryID, $actionURL)
    {
        $this->config->outsideplan->outlooksearch['actionURL'] = $actionURL;
        $this->config->outsideplan->outlooksearch['queryID']   = $queryID;
        $this->loadModel('search')->setSearchParams($this->config->outsideplan->outlooksearch);
    }

    public function buildSearchFormInLook($queryID, $actionURL)
    {
        $this->config->outsideplan->inlooksearch['actionURL'] = $actionURL;
        $this->config->outsideplan->inlooksearch['queryID']   = $queryID;
        $this->loadModel('search')->setSearchParams($this->config->outsideplan->inlooksearch);
    }

    /**
     * Project: chengfangjinke
     * Method: processPlan
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:51
     * Desc: This is the code comment. This method is called processPlan.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $plans
     * @return mixed
     */
    public function processPlan($plans, $notask = 0)
    {
        $this->loadModel('review');
        $creations = $this->dao->select('plan,id')->from(TABLE_PROJECTCREATION)->where('plan')->in(array_keys($plans))->fetchPairs();
        $outsideSubProjects = $this->dao->select('*')->from(TABLE_OUTSIDEPLANSUBPROJECTS)->where('outsideProjectPlanID')->in(array_keys($plans))->andwhere('deleted')->eq(0)->fetchall('id');

      foreach($plans as $planID => $plan)
        {
            $plan->reviewers  = $this->review->getReviewer('outsideplan', $planID, $plan->version, $plan->reviewStage);;
            $plan->creationID = isset($creations[$planID]) ? $creations[$planID] : 0;
        }
        if($notask == 0) {
            $tasks = $this->dao->select('*')->from(TABLE_OUTSIDEPLANTASKS)->where('outsideProjectPlanID')->in(array_keys($plans))->andwhere('deleted')->eq(0)->orderBy("subTaskBearDept_desc")->fetchall();
            foreach ($tasks as $task) {
                if (empty($outsideSubProjects[$task->subProjectID])) continue;
                $outsideSubProjects[$task->subProjectID]->tasks[] = $task;
            }
        }
        //每个外部计划的children =
        foreach ($outsideSubProjects as $outsideSubProject)
        {
            if(empty($outsideSubProject->tasks)) $outsideSubProject->tasks = [];
            if(!empty($plans[$outsideSubProject->outsideProjectPlanID])){
                $plans[$outsideSubProject->outsideProjectPlanID]->children[] = $outsideSubProject;
            }
        }
        return $plans;
    }

    /**
     * Project: chengfangjinke
     * Method: create
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:51
     * Desc: This is the code comment. This method is called create.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @return false
     */
    public function create()
    {
        if(!$this->checkSubProject()) return false;
        $outsideplan = fixer::input('post')
            ->add('createdBy', $this->app->user->account)
            ->add('createdDate', helper::today())
            ->join('maintainers', ',')
            ->join('files', ',')
            ->remove('uid,comment,sub')
            ->stripTags($this->config->outsideplan->editor->create['id'], $this->config->allowedTags)
            ->get();
        if(empty($_POST['maintainers'])){
            dao::$errors['maintainers'] = $this->lang->outsideplan->maintainersEmpty;
            return;
        }
        if(!$outsideplan->maintainers){
            dao::$errors['maintainers'] = $this->lang->outsideplan->maintainersEmpty;
            return;
        }


        $outsideplan->maintainers = ','.trim($outsideplan->maintainers,',').',';
        $outsideplan->maintainers = ','.$outsideplan->maintainers.',';
        if(!in_array($this->app->user->account, $_POST['maintainers'])){
            $outsideplan->maintainers .= $this->app->user->account.','; //默认回填创建人
        }
        if(trim($outsideplan->name) == $this->lang->outsideplan->undecided){ $outsideplan->name = $this->lang->outsideplan->undecided.' '.date('Ymd');}

        $subprojects = $_POST['sub']['subProjectName'];
        $outsideplan = $this->loadModel('file')->processImgURL($outsideplan, $this->config->outsideplan->editor->create['id'], $this->post->uid);
        $this->dao->insert(TABLE_OUTSIDEPLAN)
            ->data($outsideplan)->autoCheck()
            ->batchCheck($this->config->outsideplan->create->requiredFields, 'notempty')
            ->exec();
        if(!dao::isError())
        {
            $planID = $this->dao->lastInsertID();

            $this->loadModel('file')->updateObjectID($this->post->uid, $planID, 'outsideplan');
            $this->file->saveUpload('outsideplan', $planID);


            foreach ($subprojects as $subproject){
                $sub['outsideProjectPlanID'] = $planID;
                $sub['subProjectName'] = $subproject;
                $this->dao->insert(TABLE_OUTSIDEPLANSUBPROJECTS)->data($sub)->exec();
            }
            return $planID;
        }

        return false;
    }

    /**
     * Project: chengfangjinke
     * Method: update
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:51
     * Desc: This is the code comment. This method is called update.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $planID
     * @return array
     */
    public function update($planID)
    {
        if(!$this->checkSubProject()) return false;
        $oldPlan = $this->getByID($planID);
        $plan = fixer::input('post')
            ->join('line', ',')
            ->join('maintainers', ',')
            ->remove('uid,files,labels,comment,sub,subIDs')
            ->stripTags($this->config->outsideplan->editor->edit['id'], $this->config->allowedTags)
            ->get();
        if(empty($_POST['maintainers'])){
            dao::$errors['maintainers'] = $this->lang->outsideplan->maintainersEmpty;
            return;
        }
        if(!$plan->maintainers){
            dao::$errors['maintainers'] = $this->lang->outsideplan->maintainersEmpty;
            return;
        }
        if($plan->projectisdelay != 2){
            $plan->projectisdelaydesc = '';
        }
        if($plan->projectischange != 2){
            $plan->projectischangedesc = '';
        }

        $plan->maintainers = ','.trim($plan->maintainers,',').',';

        $this->dao->begin();

        $plan = $this->loadModel('file')->processImgURL($plan, $this->config->outsideplan->editor->edit['id'], $this->post->uid);
        $this->dao->update(TABLE_OUTSIDEPLAN)->data($plan)->autoCheck()
            ->batchCheck($this->config->outsideplan->edit->requiredFields, 'notempty')
            ->where('id')->eq($planID)
            ->exec();

        $subprojects   = $_POST['sub']['subProjectName'];//子项目名称
        $subprojectIds = $_POST['sub']['id'];            //原有的子项目id 新建的是空

        $this->dao->update(TABLE_OUTSIDEPLANSUBPROJECTS)->data(['deleted' => 1])->where('outsideProjectPlanID')->eq($planID)->andwhere('id')->notin($subprojectIds)->exec(); //将该计划下除了保留的子项目id (不在保留id集中的) 其他都删除
        $this->dao->update(TABLE_OUTSIDEPLANTASKS)->data(['deleted' => 1])->where('outsideProjectPlanID')->eq($planID)->andwhere('subProjectID')->notin($subprojectIds)->exec(); //将该计划下除了保留的子任务id (不在保留id集中的) 其他都删除

        for ($i = 0; $i <count($subprojects); $i++){
            $sub['outsideProjectPlanID'] = $planID;
            $sub['subProjectName'] = $subprojects[$i];
            if(empty($subprojectIds[$i])){
                $this->dao->insert(TABLE_OUTSIDEPLANSUBPROJECTS)->data($sub)->exec(); //新建的加入
            } else {
                $this->dao->update(TABLE_OUTSIDEPLANSUBPROJECTS)->data($sub)->where('id')->eq($subprojectIds[$i])->exec(); //原有的更新
            }

        }
        $this->tryError(1); //检查报错 1= 需要rollback
        $this->dao->commit();
        $this->file->updateObjectID($this->post->uid, $planID, 'outsideplan');
        $this->file->saveUpload('outsideplan', $planID);

        return common::createChanges($oldPlan, $plan);
    }

    /**
     * Project: chengfangjinke
     * Method: setListValue
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:52
     * Desc: This is the code comment. This method is called setListValue.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     */
    public function setListValue()
    {
        $this->app->loadLang('opinion');
        $maintainersList = $this->loadModel('user')->getPairs('noletter|noclosed');
        $depts = $this->loadModel('dept')->getOptionMenu();
        $lines = $this->loadModel('product')->getLinePairs(0);
        $apps  = $this->loadModel('application')->getPairs(0);
        $this->loadModel('application');
        $subProjectBearDeptList = $this->lang->application->teamList;

        $subProjectUnitList = $this->lang->outsideplan->subProjectUnitList;
        $subProjectDemandPartyList = $this->lang->outsideplan->subProjectDemandPartyList;

        foreach($subProjectBearDeptList as $id => $name) {
            if(!$id) continue;
            $subProjectBearDept[$id] = "$name(#$id)";
        }

        foreach($subProjectUnitList as $id => $name) {
            if(!$id) continue;
            $subProjectUnit[$id] = "$name(#$id)";
        }
        foreach($subProjectDemandPartyList as $id => $name) {
            if(!$id) continue;
            $subProjectDemandParty[$id] = "$name(#$id)";
        }
        foreach($lines as $id => $name) $lines[$id] = "$name(#$id)";
        foreach($depts as $id => $name) $depts[$id] = "$name(#$id)";
        foreach($apps  as $id => $name) $apps[$id]  = "$name(#$id)";

        foreach($maintainersList as $id => $name)
        {
            if(!$id) continue;
            $maintainers[$id] = "$name(#$id)";
        }

        $typeList        = $this->lang->outsideplan->typeList;
        foreach ($typeList as $id => $name) {
            if (!$id) continue;
            $types[$id] = "$name(#$id)";
        }
        $statusList      = $this->lang->outsideplan->statusList;
        foreach($statusList as $id => $name)
        {
            if(!$id) continue;
            $status[$id] = "$name(#$id)";
        }

        $this->post->set('typeList',       array_values($types));
        $this->post->set('statusList',     array_values($status));
        $this->post->set('maintainersList',array_values($maintainers));
        $this->post->set('subProjectUnitList',       array_values($subProjectUnit));
        $this->post->set('subProjectBearDeptList',   array_values($subProjectBearDept));
        $this->post->set('subProjectDemandPartyList',   array_values($subProjectDemandParty));
        $this->post->set('apptypeList',       join(',',$this->lang->outsideplan->apptypeList));
        $this->post->set('projectisdelayList',       join(',',$this->lang->outsideplan->projectisdelayList));
        $this->post->set('projectischangeList',       join(',',$this->lang->outsideplan->projectischangeList));
        $this->post->set('listStyle',      $this->config->outsideplan->export->listFields);
        $this->post->set('extraNum', 0);
    }

    /**
     * Project: chengfangjinke
     * Method: createFromImport
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:52
     * Desc: This is the code comment. This method is called createFromImport.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     */
    public function createFromImport()
    {
        $this->loadModel('action');
        $this->loadModel('outsideplan');
        $this->loadModel('file');
        $now  = helper::today();
        $data = fixer::input('post')->get();

        $outsideplanConfig = $this->config->outsideplan;
        $outsideplanConfig->list->showImportFields = 'year,code,historyCode,name,status,begin,end,workload,duration,maintainers,phone,content,milestone,changes,apptype,projectinitplan,uatplanfinishtime,materialplanonlinetime,planonlinetime,projectisdelay,projectisdelaydesc,projectischange,projectischangedesc,
        subProjectName,
        subTaskName,subTaskBegin,subTaskEnd,subProjectUnit,subProjectBearDept,subProjectDemandParty,subTaskDemandContact,subTaskDemandDeadline,subTaskDesc';

        $taskRequireFields = ['subTaskName','subTaskDesc'];

        $outsideplans = array();
        $line = 1;

        $tempTaskName = [];
        foreach($data->subTaskName as $key => $subTaskName) //检查必填项
        {
            $outsideplanData = new stdclass();
            $taskData       = new stdclass();
            $subProjectData = new stdclass();
            if(!$subTaskName) continue;
            $taskData->subTaskName          = $subTaskName; //任务名称是必须有的
            $taskData->subTaskDesc              = $data->subTaskDesc[$key];
            $taskData->subTaskBegin         = $data->subTaskBegin[$key];
            $taskData->subTaskEnd           = $data->subTaskEnd[$key];

            if($data->subProjectUnit[$key]){
                $taskData->subTaskUnit          = ','. implode(',', $data->subProjectUnit[$key]).',';
            }else{
                $taskData->subTaskUnit          = '';
            }
            if($data->subProjectBearDept[$key]){
                $taskData->subTaskBearDept      = ','. implode(',', $data->subProjectBearDept[$key]).',';
            }else{
                $taskData->subTaskBearDept      = '';
            }
            if($data->subProjectDemandParty[$key]){
                $taskData->subTaskDemandParty    = ','. implode(',', $data->subProjectDemandParty[$key]).',';
            }else{
                $taskData->subTaskDemandParty    = '';
            }

            $taskData->subTaskDemandContact     = $data->subTaskDemandContact[$key];
            $taskData->subTaskDemandDeadline    = $data->subTaskDemandDeadline[$key];


            foreach ($taskData as $taskKey => $taskItem){
                if(in_array($taskKey,$taskRequireFields)){
                    if(empty($taskItem)) dao::$errors[] = sprintf($this->lang->outsideplan->noRequire, $line, $this->lang->outsideplan->$taskKey);
                }else{
                    continue;
                }

            }
            if(in_array($taskData->subTaskName,$tempTaskName)){
                dao::$errors[] = sprintf($this->lang->outsideplan->subTaskNameErrorImportRepeat, $line);

            }else{
                $tempTaskName[] = $taskData->subTaskName;
            }

            //名字验重
            $outtaskinfo = $this->dao->select("t1.id,t1.subTaskName,t1.outsideProjectPlanID,t2.subProjectName,t3.name,t3.id as outsidID")->from(TABLE_OUTSIDEPLANTASKS)->alias('t1')
                ->leftjoin(TABLE_OUTSIDEPLANSUBPROJECTS)->alias('t2')->on("t1.subProjectID=t2.id")
                ->leftjoin(TABLE_OUTSIDEPLAN)->alias('t3')->on("t1.outsideProjectPlanID=t3.id")
                ->where('t1.subTaskName')->eq($taskData->subTaskName)
                ->andWhere('t1.deleted')->eq(0)
                ->fetch();
            if($outtaskinfo){
                $message = vsprintf($this->lang->outsideplan->subTaskNameErrorRepeat,[$outtaskinfo->name,$outtaskinfo->outsidID,$outtaskinfo->subProjectName,$outtaskinfo->subTaskName]);
                dao::$errors[] = $message;

            }

            $subProjectData->subProjectName     = $data->subProjectName[$key] ?? '';

            if(!empty($data->name[$key])) { //如果有外部计划信息
                $outsideplanData->year = $data->year[$key];
                $outsideplanData->type = $data->type[$key];
                $outsideplanData->code = $data->code[$key];
                $outsideplanData->historyCode = $data->historyCode[$key];
                $outsideplanData->name = $data->name[$key];
                $outsideplanData->status = $data->status[$key];
                $outsideplanData->begin = $data->begin[$key];
                $outsideplanData->end = $data->end[$key];
                $outsideplanData->workload = $data->workload[$key];
                $outsideplanData->duration = $data->duration[$key];
                $outsideplanData->maintainers = empty($data->maintainers[$key]) ? '' : join(',', $data->maintainers[$key]);
                $outsideplanData->phone = $data->phone[$key];
                $outsideplanData->content = $data->content[$key];
                $outsideplanData->milestone = $data->milestone[$key];
                $outsideplanData->changes = $data->changes[$key];
                $outsideplanData->apptype = $data->apptype[$key];
                $outsideplanData->projectinitplan = $data->projectinitplan[$key];
                $outsideplanData->uatplanfinishtime = $data->uatplanfinishtime[$key];
                $outsideplanData->materialplanonlinetime = $data->materialplanonlinetime[$key];
                $outsideplanData->planonlinetime = $data->planonlinetime[$key];
                $outsideplanData->projectisdelay = $data->projectisdelay[$key];
                if($outsideplanData->projectisdelay == 2){
                    $outsideplanData->projectisdelaydesc = $data->projectisdelaydesc[$key];
                }else{
                    $outsideplanData->projectisdelaydesc = '';
                }

                $outsideplanData->projectischange = $data->projectischange[$key];
                if($outsideplanData->projectischange == 2){
                    $outsideplanData->projectischangedesc = $data->projectischangedesc[$key];
                }else{
                    $outsideplanData->projectischangedesc = '';
                }

            }
            if(!empty($outsideplanData->name))
            {
                $requiredFields = explode(',', $this->config->outsideplan->importcreate->requiredFields);
                foreach($requiredFields as $requiredField)
                {
                    $requiredField = trim($requiredField);
                    if(empty($outsideplanData->$requiredField)) dao::$errors[] = sprintf($this->lang->outsideplan->noRequire, $line, $this->lang->outsideplan->$requiredField);
                }
            }

            $outsideplans[$key]['outsideplanData'] = $outsideplanData;
            $outsideplans[$key]['taskData'] = $taskData;
            $outsideplans[$key]['subProjectData'] = $subProjectData;
            $line++;
        }
        if(dao::isError()){

            die(js::error(dao::getError()));
        }

        $outsideplanID = 0;
        $subProjectID  = 0;
        $this->dao->begin(); //调试完逻辑最后开启事务
        foreach($outsideplans as $key => $newProjectplan)
        {
            if($newProjectplan['outsideplanData']->name) { //如果没有外部计划对象  outsideplanID 就是上一个
                $outsideplanData = $newProjectplan['outsideplanData'];
                $outsideplanData->createdBy = $this->app->user->account;
                $outsideplanData->createdDate = $now;
                $outsideplanData->status = 'wait';

                $this->dao->insert(TABLE_OUTSIDEPLAN)->data($outsideplanData)->autoCheck()->exec();

                if (!dao::isError()) {
                    $outsideplanID = $this->dao->lastInsertID();
                    $this->action->create('outsideplan', $outsideplanID, 'created', '');
                }
            }
            if($newProjectplan['subProjectData']->subProjectName) { //如果没有子项目对象 $subProjectID 就是上一个
                $subProjectData = $newProjectplan['subProjectData'];
                $subProjectData->outsideProjectPlanID = $outsideplanID;
                $this->dao->insert(TABLE_OUTSIDEPLANSUBPROJECTS)->data($subProjectData)->autoCheck()->exec();

                if (!dao::isError()) {
                    $subProjectID = $this->dao->lastInsertID();
                }
            }

            $taskData = $newProjectplan['taskData']; //任务数据必须有
            $taskData->subProjectID = $subProjectID;
            $taskData->outsideProjectPlanID = $outsideplanID;
            if($outsideplanID && $subProjectID){
                $this->dao->insert(TABLE_OUTSIDEPLANTASKS)->data($taskData)->autoCheck()->exec();
            }

            if(dao::isError()){
                $this->dao->rollBack();
                die(js::error(dao::getError()));
            }
        }
        $this->dao->commit(); //调试完逻辑最后开启事务
        if($this->post->isEndPage)
        {
            unlink($this->session->fileImport);
            unset($_SESSION['fileImport']);
        }
    }

    /**
     * Project: chengfangjinke
     * Method: exec
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:52
     * Desc: This is the code comment. This method is called exec.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $planID
     */
    public function exec($planID)
    {
        $plan     = $this->getByID($planID);
        $products = $this->post->products;

        $project = new stdClass();
        $project->name           = $plan->name;
        $project->status         = 'wait';
        $project->model          = 'waterfall';
        $project->begin          = $plan->begin;
        $project->end            = $plan->end;
        $project->days           = 0;
        $project->openedBy       = $this->app->user->account;
        $project->openedDate     = helper::now();
        $project->lastEditedBy   = $this->app->user->account;
        $project->lastEditedDate = helper::now();
        $project->type           = 'project';

        $this->dao->insert(TABLE_PROJECT)->data($project)->exec();

        $projectID = $this->dao->lastInsertID();

        foreach($products as $product)
        {
            if(!$product) continue;

            $projectProduct = new stdClass();
            $projectProduct->project = $projectID;
            $projectProduct->product = $product;
            $this->dao->insert(TABLE_PROJECTPRODUCT)->data($projectProduct)->exec();
        }

        $this->dao->update(TABLE_OUTSIDEPLAN)
             ->set('project')->eq($projectID)
             ->set('status')->eq('projected')
             ->where('id')->eq($planID)
             ->exec();
    }

    /**
     * Project: chengfangjinke
     * Method: isClickable
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:52
     * Desc: This is the code comment. This method is called isClickable.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $object
     * @param $action
     * @return bool
     */
    public static function isClickable($object, $action)
    {
        global $app;
        $action = strtolower($action);
        $maintainers = explode(',', $object->maintainers.',admin');//admin 和 维护人员
        if($action == 'exec')        return $object->status == 'pass';
        if($action == 'edit')        return in_array($app->user->account, $maintainers);
        if($action == 'deletesub')   return in_array($app->user->account, $maintainers);
        if($action == 'createtask')  return in_array($app->user->account, $maintainers);
        if($action == 'copysub')  return in_array($app->user->account, $maintainers);
        if($action == 'copytask'){

            return in_array($app->user->account, $maintainers);
        }
        if($action == 'movetask')  return in_array($app->user->account, $maintainers);
        if($action == 'movesub')  return in_array($app->user->account, $maintainers);
        if($action == 'edittask')   return in_array($app->user->account, $maintainers);
        if($action == 'deletetask')  return in_array($app->user->account, $maintainers);
        if($action == 'review')      return $object->status == 'reviewing' and strpos(",$object->reviewers,", ",{$app->user->account},") !== false;
        if($action == 'submit')      return ($object->status == 'wait' or $object->status == 'reject') and $object->isInit == true;
        if($action == 'initproject') return $object->status == 'wait' or $object->status == 'reject';
        if($action == 'delete')      return $object->status != 'deleted' and in_array($app->user->account, $maintainers);

        return true;
    }

    /**
     * Project: chengfangjinke
     * Method: sendmail
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:52
     * Desc: This is the code comment. This method is called sendmail.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $planID
     * @param $actionID
     * @param int $grade
     * @param string $type
     */
    public function sendmail($planID, $actionID, $grade = 0, $type = '')
    {   
        $this->loadModel('mail');
        $plan  = $this->getByID($planID);
        $users = $this->loadModel('user')->getPairs('noletter');

        /* Get action info. */
        $action             = $this->loadModel('action')->getById($actionID);
        $history            = $this->action->getHistory($actionID);
        $action->history    = isset($history[$actionID]) ? $history[$actionID] : array();
        $action->appendLink = ''; 
        if(strpos($action->extra, ':') !== false)
        {   
            list($extra, $id) = explode(':', $action->extra);
            if($id and is_numeric($id))
            {   
                $action->extra = $extra;

                $name = $this->dao->select('name')->from(TABLE_OUTSIDEPLAN)->where('id')->eq($id)->fetch('name');
                if($name) $action->appendLink = html::a(zget($this->config->mail, 'domain', common::getSysURL()) . helper::createLink($action->objectType, 'view', "id=$id", 'html'), "#$id " . $name);
            }   
        }   

        /* Get mail content. */
        $modulePath = $this->app->getModulePath($appName = '', 'outsideplan');
        $oldcwd     = getcwd();
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

        if(!$type)
        {
            $sendUsers = $this->getToAndCcList($planID, $grade);
            if(!$sendUsers) return;
            list($toList, $ccList) = $sendUsers;
            $subject = $this->getSubject($planID);
        }
        else
        {
            $toList  = $plan->submitedBy;
            $ccList  = '';
            $subject = $this->lang->outsideplan->projectCreation . '#' . $planID . ' ' . $plan->name . '-' . $this->lang->outsideplan->labelList[$type]; 
        }

        /* Send it. */
        $this->mail->send($toList, $subject, $mailContent, $ccList);
        if($this->mail->isError()) error_log(join("\n", $this->mail->getError()));
    }

    /**
     * Project: chengfangjinke
     * Method: getSubject
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:52
     * Desc: This is the code comment. This method is called getSubject.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $planID
     * @return string
     */
    public function getSubject($planID)
    {
        $name = $this->getById($planID)->name;
        return $this->lang->outsideplan->projectCreation . '#' . $planID . ' ' . $name . '-' . $this->lang->outsideplan->waitReview;
    }

    /**
     * Project: chengfangjinke
     * Method: getToAndCcList
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:52
     * Desc: This is the code comment. This method is called getToAndCcList.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $planID
     * @param $grade
     * @return array|void
     */
    public function getToAndCcList($planID, $grade)
    {
        $nodes = $this->dao->select('*')->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq('outsideplan')
            ->andWhere('objectID')->eq($planID)
            ->orderBy('id')
            ->fetchAll();
        $node = $nodes[$grade];

        $toList = $this->dao->select('reviewer')->from(TABLE_REVIEWER)->where('node')->eq($node->id)->fetchPairs();

        /* Set toList and ccList. */
        if(empty($toList)) return;
        $toList = join(',', $toList);

        return array($toList, array());
    }

    private function checkSubProject()
    {

        if(empty($_POST)) return false;
        foreach ($_POST['sub']['subProjectName'] as $k => $v)
        {
            $k++;
            if(empty($v)) {
                dao::$errors['sub'] = "第{$k}行{$this->lang->outsideplan->subProjectName}是必填项不能为空";
                return false;
            }
        }
        return true;
    }
    
    /**
     * TongYanQi 2022/9/6
     * 检查子项目必填项
     */
    private function checkSub()
    {
        $whitelist = ['id','subTaskDemandContact','subTaskDemandDeadline','subTaskBegin','subTaskEnd','subTaskUnit','subTaskBearDept','subTaskDemandParty'];
        if(empty($_POST)) return false;
        foreach ($_POST as $k => $v)
        {
                if(($_POST['subTaskEnd'] && $_POST['subTaskBegin'] ) && ($_POST['subTaskEnd'] < $_POST['subTaskBegin']) ) {
                    dao::$errors['sub'] = "『(外部)子项/子任务计划完成时间』不能小于『(外部)子项/子任务计划开始时间』" ;
                    return false;
                }
                if(empty($v) && !in_array($k, $whitelist)) {
                    dao::$errors['sub'] = $this->lang->outsideplan->$k.'是必填项不能为空';
                    return false;
                }
                    if(empty($v[0]) && empty($v[1]) && !in_array($k, $whitelist)) {
                        dao::$errors[$k] = $this->lang->outsideplan->$k.'是必填项不能为空';
                        return false;
                    }
        }
        return true;
    }
    /**
     * TongYanQi 2022/9/8
     * 拆分子任务
     */
    public function createtask($subProjectID, $plan)
    {
        if(!$this->checkSub()) return false;
        $task =  fixer::input('post')
            ->join('subTaskUnit', ',')
            ->join('subTaskBearDept', ',')
            ->join('subTaskDemandParty', ',')
            ->stripTags($this->config->outsideplan->editor->edit['id'], $this->config->allowedTags)
            ->get();

        if(!isset($task->subTaskName) && !$task->subTaskName){
            dao::$errors['subTaskName'] = $this->lang->outsideplan->subTaskNameErrorEmpty  ;
            return false;
        }
//        $outtaskinfo = $this->dao->select('id')->from(TABLE_OUTSIDEPLANTASKS)->where('subTaskName')->eq($task->subTaskName)->andWhere('deleted')->eq(0)->fetch();
        $outtaskinfo = $this->dao->select("t1.id,t1.subTaskName,t1.outsideProjectPlanID,t2.subProjectName,t3.name,t3.id as outsidID")->from(TABLE_OUTSIDEPLANTASKS)->alias('t1')
            ->leftjoin(TABLE_OUTSIDEPLANSUBPROJECTS)->alias('t2')->on("t1.subProjectID=t2.id")
            ->leftjoin(TABLE_OUTSIDEPLAN)->alias('t3')->on("t1.outsideProjectPlanID=t3.id")
            ->where('t1.subTaskName')->eq($task->subTaskName)
            ->andWhere('t1.deleted')->eq(0)
            ->fetch();
        if($outtaskinfo){
            $message = vsprintf($this->lang->outsideplan->subTaskNameErrorRepeat,[$outtaskinfo->name,$outtaskinfo->outsidID,$outtaskinfo->subProjectName,$outtaskinfo->subTaskName]);
            dao::$errors['subTaskName'] = $message;
            return false;
        }
        if($task->subTaskBegin < $plan->begin) {
            $plan->begin = $task->subTaskBegin;
        }

        if($task->subTaskEnd > $plan->end) {
            $plan->end = $task->subTaskEnd;
        }
        $task->subTaskUnit = ',' .  $task->subTaskUnit .',';
        $task->subTaskBearDept = ',' . $task->subTaskBearDept .',';
        $task->subTaskDemandParty = ',' . $task->subTaskDemandParty.',';
        $task->deleted = 0;
        $task->subProjectID = $subProjectID;
        $task->outsideProjectPlanID = $plan->id;

        $task = $this->loadModel('file')->processImgURL($task, $this->config->outsideplan->editor->createtask['id'], $this->post->uid);
        $this->dao->insert(TABLE_OUTSIDEPLANTASKS)->data($task)->exec();

        $this->recountWorkload($plan);

        return true;
    }

    /**
     * TongYanQi 2022/10/11
     * 编辑任务
     */
    public function editTask($taskId, $plan, $subprojectID)
    {
        if(!$this->checkSub()) return false;
        $task =  fixer::input('post')
            ->join('subTaskUnit', ',')
            ->join('subTaskBearDept', ',')
            ->join('subTaskDemandParty', ',')
            ->stripTags($this->config->outsideplan->editor->edittask['id'], $this->config->allowedTags)
            ->get();
        if(!isset($task->subTaskName) && !$task->subTaskName){
            dao::$errors['subTaskName'] = $this->lang->outsideplan->subTaskNameErrorEmpty  ;
            return false;
        }
        $outtaskinfo = $this->dao->select("t1.id,t1.subTaskName,t1.outsideProjectPlanID,t2.subProjectName,t3.name,t3.id as outsidID")->from(TABLE_OUTSIDEPLANTASKS)->alias('t1')
            ->leftjoin(TABLE_OUTSIDEPLANSUBPROJECTS)->alias('t2')->on("t1.subProjectID=t2.id")
            ->leftjoin(TABLE_OUTSIDEPLAN)->alias('t3')->on("t1.outsideProjectPlanID=t3.id")
            ->where('t1.subTaskName')->eq($task->subTaskName)
            ->andWhere('t1.id')->ne($taskId)
            ->andWhere('t1.deleted')->eq(0)
            ->fetch();
        if($outtaskinfo){
            $message = vsprintf($this->lang->outsideplan->subTaskNameErrorRepeat,[$outtaskinfo->name,$outtaskinfo->outsidID,$outtaskinfo->subProjectName,$outtaskinfo->subTaskName]);
            dao::$errors['subTaskName'] = $message;
            return false;
        }

            if($task->subTaskBegin < $plan->begin) {
                $plan->begin = $task->subTaskBegin;
            }

            if($task->subTaskEnd > $plan->end) {
                $plan->end = $task->subTaskEnd;
            }
            $task->subTaskUnit = ',' .  $task->subTaskUnit .',';
            $task->subTaskBearDept = ',' . $task->subTaskBearDept .',';
            $task->subTaskDemandParty = ',' . $task->subTaskDemandParty.',';
            $task->deleted = 0;
            $task = $this->loadModel('file')->processImgURL($task, $this->config->outsideplan->editor->edittask['id'], $this->post->uid);
            $this->dao->update(TABLE_OUTSIDEPLANTASKS)->data($task)->where('id')->eq($taskId)->exec();

            $this->recountWorkload($plan);

            return true;

    }

    public function bindprojectplan($taskId){
        $task =  fixer::input('post')
            ->get();
        $relationInnerPlan = [];
        if(isset($task->relationInnerPlan)){
            $relationInnerPlan = $task->relationInnerPlan;
            unset($task->relationInnerPlan);
        }
        $this->loadModel('projectplan');
        $taskProjectPlanList = $this->projectplan->getPlanBytaskIDFilter($taskId,'id,name');
        if($taskProjectPlanList){
            $taskProjectPlanList = array_column($taskProjectPlanList,'id');
        }else{
            $taskProjectPlanList = [];
        }
        $isAddArr = array_diff($relationInnerPlan,$taskProjectPlanList);
        $isDeleteArr = array_diff($taskProjectPlanList,$relationInnerPlan);

        //和新增的取交集 取判断年度计划有没有变更中的的。
        $tempProjectPlanIDArr = $taskProjectPlanList;
        if($isAddArr){
            $tempProjectPlanIDArr = array_merge($taskProjectPlanList,$isAddArr);

        }
        $tempProjectplanListArr = $this->projectplan->getByIDMultipleList($tempProjectPlanIDArr,'id,name,changeStatus');
        foreach ($tempProjectplanListArr as $vplan){
            if ($vplan->changeStatus == 'pending'){

                dao::$errors['subTaskName'] = vsprintf($this->lang->outsideplan->inPlanChangeError,[$vplan->name]);
                return false;
            }
        }

        try {
            $this->dao->begin();
            $taskinfo = $this->dao->select('id,outsideProjectPlanID,subProjectID')->from(TABLE_OUTSIDEPLANTASKS)->where('id')->eq($taskId)->andwhere('deleted')->eq(0)->fetch();

            $linkPlanflag = false;
            if($isAddArr){
                $linkPlanflag = true;
                $projectplanList = $this->projectplan->getByIDMultipleList($isAddArr,'id,outsideProject,outsideSubProject,outsideTask');

                foreach ($projectplanList as $k=>$addplan){
                    $oldplandataArr = [
                        'outsideProject'=>$addplan->outsideProject,
                        'outsideSubProject'=>$addplan->outsideSubProject,
                        'outsideTask'=>$addplan->outsideTask
                    ];
                    if(!$addplan->outsideProject){
                        $addplan->outsideProject = '';
                    }

                    $addplan->outsideProject = trim($addplan->outsideProject,',');
                    $addplan->outsideTask = trim($addplan->outsideTask,',');
                    $addplan->outsideSubProject = trim($addplan->outsideSubProject,',');

                    if($addplan->outsideProject){
                        $addOutPlanIDArr = explode(',',$addplan->outsideProject);
                    }else{
                        $addOutPlanIDArr = [];
                    }
                    if($addplan->outsideSubProject){
                        $addOutSubPlanIDArr = explode(',',$addplan->outsideSubProject);
                    }else{
                        $addOutSubPlanIDArr = [];
                    }

                    if($addplan->outsideTask){
                        $addOutTaskIDArr = explode(',',$addplan->outsideTask);
                    }else{
                        $addOutTaskIDArr = [];
                    }


                    if(!in_array($taskinfo->outsideProjectPlanID,$addOutPlanIDArr)){
                        $addOutPlanIDArr[] = $taskinfo->outsideProjectPlanID;
                    }

                    if(!in_array($taskinfo->subProjectID,$addOutSubPlanIDArr)){
                        $addOutSubPlanIDArr[] = $taskinfo->subProjectID;
                    }

                    if(!in_array($taskinfo->id,$addOutTaskIDArr)){
                        $addOutTaskIDArr[] = $taskinfo->id;
                    }

                    rsort($addOutPlanIDArr);
                    rsort($addOutSubPlanIDArr);
                    rsort($addOutTaskIDArr);
                    $updata = [];
                    $updata['outsideProject'] = ','.implode(',',$addOutPlanIDArr).',';
                    $updata['outsideSubProject'] = ','.implode(',',$addOutSubPlanIDArr).',';
                    $updata['outsideTask'] = ','.implode(',',$addOutTaskIDArr).',';
                    //sql修改 对应内部年度计划
                    $this->dao->update(TABLE_PROJECTPLAN)->data($updata)->where('id')->eq($addplan->id)->exec();
                    //添加操作记录
                    $newplandataArr = [
                        'outsideProject'=>$updata['outsideProject'],
                        'outsideSubProject'=>$updata['outsideSubProject'],
                        'outsideTask'=>$updata['outsideTask']
                    ];
                    $changes = common::createRuleChanges((object)$oldplandataArr,(object)$newplandataArr,[],$this->config->projectplan->changeFieldsRule);
                    $actionID = $this->loadModel('action')->create('projectplan', $addplan->id, 'bindprojectplan', '');
                    if($changes){
                        $this->action->logHistory($actionID, $changes);
                    }

                }
            }



            if($isDeleteArr){

                $linkPlanflag = true;

                $othertaskList = $this->dao->select('id')->from(TABLE_OUTSIDEPLANTASKS)->where('subProjectID')->eq($taskinfo->subProjectID)->andWhere('deleted')->eq(0)->fetchAll();
                $otherSubList = $this->dao->select('id')->from(TABLE_OUTSIDEPLANSUBPROJECTS)->where('outsideProjectPlanID')->eq($taskinfo->outsideProjectPlanID)->andWhere('deleted')->eq(0)->fetchAll();
                $projectplanList = $this->projectplan->getByIDMultipleList($isDeleteArr,'id,outsideProject,outsideSubProject,outsideTask');
                if($otherSubList){
                    $otherSubList = array_column($otherSubList,'id');
                }else{
                    $otherSubList = [];
                }
                if($othertaskList){
                    $othertaskList = array_column($othertaskList,'id');
                }else{
                    $othertaskList = [];
                }

                //
                foreach ($projectplanList as $k=>$delplan){
                    $oldplandataArr = [
                        'outsideProject'=>$delplan->outsideProject,
                        'outsideSubProject'=>$delplan->outsideSubProject,
                        'outsideTask'=>$delplan->outsideTask
                    ];
                    if(!$delplan->outsideProject){
                        $delplan->outsideProject = '';
                    }

                    $delplan->outsideProject = trim($delplan->outsideProject,',');
                    $delplan->outsideTask = trim($delplan->outsideTask,',');
                    $delplan->outsideSubProject = trim($delplan->outsideSubProject,',');

                    if($delplan->outsideProject){
                        $delOutPlanIDArr = explode(',',$delplan->outsideProject);
                    }else{
                        $delOutPlanIDArr = [];
                    }
                    if($delplan->outsideSubProject){
                        $delOutSubPlanIDArr = explode(',',$delplan->outsideSubProject);
                    }else{
                        $delOutSubPlanIDArr = [];
                    }

                    if($delplan->outsideTask){
                        $delOutTaskIDArr = explode(',',$delplan->outsideTask);
                    }else{
                        $delOutTaskIDArr = [];
                    }
                    if(($tskey = array_search($taskinfo->id,$delOutTaskIDArr)) !== false){
                        unset($delOutTaskIDArr[$tskey]);
                    }

                    //任务取交集，无，删除 sub

                    if(!array_intersect($othertaskList,$delOutTaskIDArr)){

                        if(($subkey = array_search($taskinfo->subProjectID,$delOutSubPlanIDArr)) !== false){

                            unset($delOutSubPlanIDArr[$subkey]);
                        }
                    }

                    // 子项id取交集，无，删除外部计划id
                    if(!array_intersect($otherSubList,$delOutSubPlanIDArr)){
                        if(($outkey = array_search($taskinfo->outsideProjectPlanID,$delOutPlanIDArr)) !== false){
                            unset($delOutPlanIDArr[$outkey]);
                        }
                    }


                    rsort($delOutPlanIDArr);
                    rsort($delOutSubPlanIDArr);
                    rsort($delOutTaskIDArr);
                    $updata = [];
                    if($delOutPlanIDArr){
                        $updata['outsideProject'] = ','.implode(',',$delOutPlanIDArr).',';
                    }else{
                        $updata['outsideProject'] = '';
                    }
                    if($delOutSubPlanIDArr){
                        $updata['outsideSubProject'] = ','.implode(',',$delOutSubPlanIDArr).',';
                    }else{
                        $updata['outsideSubProject'] = '';
                    }
                    if($delOutTaskIDArr){
                        $updata['outsideTask'] = ','.implode(',',$delOutTaskIDArr).',';
                    }else{
                        $updata['outsideTask'] = '';
                    }

                    //sql修改 对应内部年度计划
                    $this->dao->update(TABLE_PROJECTPLAN)->data($updata)->where('id')->eq($delplan->id)->exec();
                    //添加操作记录
                    $newplandataArr = [
                        'outsideProject'=>$updata['outsideProject'],
                        'outsideSubProject'=>$updata['outsideSubProject'],
                        'outsideTask'=>$updata['outsideTask']
                    ];
                    $changes = common::createRuleChanges((object)$oldplandataArr,(object)$newplandataArr,[],$this->config->projectplan->changeFieldsRule);
                    $actionID = $this->loadModel('action')->create('projectplan', $delplan->id, 'bindprojectplan', '');
                    if($changes){
                        $this->action->logHistory($actionID, $changes);
                    }

                }

            }

            //需要更新 linkedPlan字段
            $actionID = $this->loadModel('action')->create('outsideplan', $taskinfo->outsideProjectPlanID, 'bindprojectplan', '');
            if($linkPlanflag){
                $outplanInfo = $this->getSimpleByID($taskinfo->outsideProjectPlanID,'linkedPlan');
                $projectplanLinkList = $this->projectplan->getPlanByOutID($taskinfo->outsideProjectPlanID,'id,name');
                $projectplanLinkList = array_column($projectplanLinkList,'id');
                $projectplanLinkStr = trim(implode(',',$projectplanLinkList),',');
                if($projectplanLinkStr){
                    $projectplanLinkStr = ','.$projectplanLinkStr.',';
                }else{
                    $projectplanLinkStr = '';
                }
                $this->dao->update(TABLE_OUTSIDEPLAN)->set('linkedPlan')->eq($projectplanLinkStr)->where('id')->eq($taskinfo->outsideProjectPlanID)->exec();
                $changes = common::createRuleChanges($outplanInfo,(object)['linkedPlan'=>$projectplanLinkStr],[],$this->config->outsideplan->changeFieldsRule);
                if($changes){
                    $this->action->logHistory($actionID, $changes);
                }

            }
            //计算 需要新增
            if(dao::isError()){
                $this->dao->rollback();
            }else{
                $this->dao->commit();
//                                $this->dao->rollback();
            }
            return true;
        }catch (Error $e){
            dao::$errors[] = $e->getMessage();
            $this->dao->rollback();
            return true;
        }
    }

    private function recountWorkload($plan)
    {
        $interval = date_diff(date_create($plan->begin),date_create($plan->end));
        $workload = $interval->format('%a') + 1; //间隔+首尾2天
        $this->dao->update(TABLE_OUTSIDEPLAN)->data(['end'=>$plan->end, 'begin'=>$plan->begin, 'workload' => $workload])->where('id')->eq($plan->id)->exec();
    }
    public function tryError($rollBack = 0)
    {
        if(dao::isError())
        {
            if($rollBack == 1){
                $this->dao->rollBack();
            }
            $response['result']  = 'fail';
            $response['message'] = dao::getError();
            $this->send($response);
        }
    }
    /**
     * 直接输出data数据
     * @access public
     */
    private function send($data)
    {
        die(json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    /**
     * TongYanQi 2022/9/14
     * 获取(外部)子项/子任务
     */
    public function getTaskPairs($ids = '')
    {
        return $this->dao->select('id,subTaskName')->from(TABLE_OUTSIDEPLANTASKS)
            ->where('deleted')->eq(0)
            ->beginIF($ids)->andWhere('id')->in($ids)->fi()
            ->orderBy('id_desc')
            ->fetchPairs();
    }

    public function getTaskPairsNameUnion($ids = '')
    {
        return $this->dao->select('t1.id,concat(t2.year,"-",subTaskName,"【",t1.outsideProjectPlanID,"】") as subTaskName')->from(TABLE_OUTSIDEPLANTASKS)->alias('t1')
            ->leftJoin(TABLE_OUTSIDEPLAN)->alias('t2')->on("t1.outsideProjectPlanID=t2.id")
            ->where('t1.deleted')->eq(0)
            ->beginIF($ids)->andWhere('t1.id')->in($ids)->fi()
            ->orderBy('t1.id_desc')
            ->fetchPairs();
    }

    /**
     * Jinzhuliang 2023/05/12
     * 根据(外部)项目/任务获取(外部)子项/子任务
     */
    public function getTaskByOutsideplanID($outplanId,$field="*")
    {
        return $this->dao->select($field)->from(TABLE_OUTSIDEPLANTASKS)
            ->where('deleted')->eq(0)
            ->andWhere('outsideProjectPlanID')->eq($outplanId)
            ->orderBy('id_desc')
            ->fetchAll();
    }
    /**
     * TongYanQi 2022/9/19
     * 获取(外部)子项/子任务
     */
    public function getTasks($ids = '', $subProjectIds = '')
    {
        if(empty($ids) && empty($subProjectIds)) return [];
        return $this->dao->select('*')->from(TABLE_OUTSIDEPLANTASKS)
            ->where('deleted')->eq(0)
            ->beginIF($ids)->andWhere('id')->in($ids)->fi()
            ->beginIF($subProjectIds)->andWhere('outsideProjectPlanID')->in($subProjectIds)->fi()
            ->orderBy('id_desc')
            ->fetchAll();
    }
    /**
     * Jinzhuliang 2023/6/21
     * 获取(外部)子项/子任务
     */
    public function getTasksByID($id)
    {

        return $this->dao->select('*')->from(TABLE_OUTSIDEPLANTASKS)
            ->where('deleted')->eq(0)
            ->andWhere('id')->eq($id)
            ->orderBy('id_desc')
            ->fetch();
    }
    /**
     * Jinzhuliang 2023/6/21
     * 获取(外部)子项/子任务
     */
    public function getTasksBySubID($subProjectId)
    {

        return $this->dao->select('*')->from(TABLE_OUTSIDEPLANTASKS)
            ->where('deleted')->eq(0)
            ->andWhere('subProjectID')->eq($subProjectId)
            ->orderBy('id_desc')
            ->fetchAll();
    }

    /**
     * TongYanQi 2022/9/15
     * 获取子项目
     */
    public function getSubProjectPairs($ids = '')
    {
        return $this->dao->select('id,subProjectName')->from(TABLE_OUTSIDEPLANSUBPROJECTS)
            ->where('deleted')->eq(0)
            ->beginIF($ids)->andWhere('id')->in($ids)->fi()
            ->orderBy('id_desc')
            ->fetchPairs();
    }

    /**
     * TongYanQi 2022/9/15
     * 获取子项目
     */
    public function getSubProjects($ids = '')
    {
        return $this->dao->select('*')->from(TABLE_OUTSIDEPLANSUBPROJECTS)
            ->where('deleted')->eq(0)
            ->beginIF($ids)->andWhere('id')->in($ids)->fi()
            ->orderBy('id_desc')
            ->fetchAll();
    }

    /**
     * Jinzhuliang 2023/6/21
     * 获取子项目
     */
    public function getSubProject($id,$field='*')
    {
        return $this->dao->select($field)->from(TABLE_OUTSIDEPLANSUBPROJECTS)
            ->where('deleted')->eq(0)
            ->andWhere('id')->eq($id)
            ->orderBy('id_desc')
            ->fetch();
    }

    /**
     * JinZhuLiang 2023/5/11
     * 获取子项目
     */
    public function getSubProjectsByParentId($id)
    {
        return $this->dao->select('*')->from(TABLE_OUTSIDEPLANSUBPROJECTS)
            ->where('deleted')->eq(0)
            ->andWhere('outsideProjectPlanID')->eq($id)
            ->orderBy('id_desc')
            ->fetchAll();
    }

    /**
     * TongYanQi 2022/9/19
     * 根据内部项目状态更新(外部)项目/任务状态（该功能不上了）
     */
    public function changeStatusByProjectPlan()
    {
       $plans = $this->dao->select('id, outsideProject, insideStatus')->from(TABLE_PROJECTPLAN)
            ->where('deleted')->eq(0)
            ->andwhere('outsideProject')->ne("")
            ->fetchAll();
       $updateList = [];
       foreach ($plans as $plan)
        {
            $outsidePlanIDs = explode(',', $plan->outsideProject); //内部项目关联的(外部)项目/任务
            $outsideStatus = $this->config->outsideplan->statusMap[$plan->insideStatus] ?? ""; //内部状态 对应的 外部状态
            if($outsideStatus){
                foreach ($outsidePlanIDs as $outsidePlanID)
                $updateList[$outsidePlanID][$outsideStatus] = $outsideStatus; //(外部)项目/任务所属的状态
            }
        }

       foreach ($updateList as $outPlanID => $statusList) {
           //如果只有一种状态 或者都一样
           if (count($statusList) == 1) {
               $this->dao->update(TABLE_OUTSIDEPLAN)->data(['status' => current($statusList)])
                   ->where('id')->eq($outPlanID)
                   ->andwhere('status')->ne(current($statusList))
                   ->exec();
               continue;
           }
           //如果有一个进度异常
           if (in_array($this->lang->outsideplan->key->exceptionallyprogressing, $statusList)) {
               $this->dao->update(TABLE_OUTSIDEPLAN)->data(['status' => $this->lang->outsideplan->key->exceptionallyprogressing])
                   ->where('id')->eq($outPlanID)
                   ->andwhere('status')->ne($this->lang->outsideplan->key->exceptionallyprogressing)
                   ->exec();
               continue;
           }
           //如果有一个未完成
           if (in_array($this->lang->outsideplan->key->notfinished, $statusList)) {
               $this->dao->update(TABLE_OUTSIDEPLAN)->data(['status' => $this->lang->outsideplan->key->notfinished])
                   ->where('id')->eq($outPlanID)
                   ->andwhere('status')->ne($this->lang->outsideplan->key->exceptionallyprogressing)
                   ->exec();
               continue;
           }

       }
    }

    /**
     * TongYanQi 2022/9/26
     * 统计表数据
     */
    public function getChart($year = '')
    {
        $list = [];

        $allOutPlans = $this->dao->select('id,status,year')->from(TABLE_OUTSIDEPLAN)
            ->where('deleted')->eq(0)
            ->beginIF($year > 2019)->andWhere('year')->eq($year)->fi()
            ->beginIF($year >2018 && $year <= 2019 )->andWhere('year')->le($year)->fi()
            ->fetchall('id');

        //内部计划
        $plans =  $this->dao->select('id,outsideProject,status,insideStatus')->from(TABLE_PROJECTPLAN)
            ->where('deleted')->eq(0)->andwhere('outsideProject')->ne('')
            ->beginIF($year > 2019)->andWhere('year')->eq($year)->fi()
            ->beginIF($year >2018 && $year <= 2019 )->andWhere('year')->le($year)->fi()
            ->fetchall();
        $outPlansInnerPlan = [];
        foreach ($plans as $plan){
            $outlinkIds = explode(',', $plan->outsideProject);
            foreach ($outlinkIds as $outlinkId)
            {
                if(empty($outlinkId) || empty($allOutPlans[$outlinkId])) continue;
                if(empty($outPlansInnerPlan[$allOutPlans[$outlinkId]->year]['num'])) $outPlansInnerPlan[$allOutPlans[$outlinkId]->year]['num'] = 0;
                $outPlansInnerPlan[$allOutPlans[$outlinkId]->year]['num'] ++;

                if(isset($plan->insideStatus)) {
                    if (empty($outPlansInnerPlan[$allOutPlans[$outlinkId]->year]['insideStatus'][$plan->insideStatus])) $outPlansInnerPlan[$allOutPlans[$outlinkId]->year]['insideStatus'][$plan->insideStatus] = 0;
                    $outPlansInnerPlan[$allOutPlans[$outlinkId]->year]['insideStatus'][$plan->insideStatus]++;

                }
            }
        }

        $list['yearList'] = $this->getYearDataForChart($year);
        $unitDataForChart = $this->getUnitDataForChart($plans, $year);
        $list['subTaskBearDeptList'] = $unitDataForChart['depts'];
        $list['subTaskUnitList'] = $unitDataForChart['units'];

        return $list;
    }

    public function getYearDataForChart($selectedYear = '')
    {
        $yearList = [];
        $data = [];
        //外部计划
        $data['year'] = 2019;
        $outIds = $this->dao->select("id")->from(TABLE_OUTSIDEPLAN)->where('year <= 2019')->andwhere('deleted')->eq(0)->fetchall('id');
        $data['idNum'] = count($outIds);
        $subs = $this->dao->query("SELECT COUNT(id) as num FROM `zt_outsideplansubprojects` where deleted = 0 and outsideProjectPlanID in (SELECT id from zt_outsideplan where year <= 2019 )")->fetch();
        $data['subs'] = $subs->num;
        $tasks = $this->dao->query("SELECT COUNT(id) as num FROM `zt_outsideplantasks` where deleted = 0 and outsideProjectPlanID in (SELECT id from zt_outsideplan where year <= 2019 )")->fetch();
        $data['tasks'] = $tasks->num;
        $status = $this->dao->query("SELECT COUNT(id) as num, `status` FROM `zt_outsideplan` where deleted = 0 and  year <= 2019 GROUP BY status")->fetchAll();
        $status = array_column($status, 'num','status');
        $data['status']['wait'] = $status['wait'] ?? 0;
        $data['status']['exceptionallyfinished'] = $status['exceptionallyfinished'] ?? 0;
        $data['status']['finished'] =  $status['finished'] ?? 0;
        $data['status']['notfinished'] =  $status['notfinished'] ?? 0;
        $data['status']['exceptionallyprogressing'] =  $status['exceptionallyprogressing'] ?? 0;
        //内部年度计划
        $innerIds = $this->dao->select("id")->from(TABLE_PROJECTPLAN)->where('year <= 2019')->andwhere('deleted')->eq(0)->fetchall('id');
        $data['projectPlanNum'] = count($innerIds);
        $status = $this->dao->query("SELECT COUNT(id) as num, `insideStatus` FROM `zt_projectplan` where deleted = '0' and  year <= 2019 GROUP BY insideStatus")->fetchAll();
        $status = array_column($status, 'num','insideStatus');
        $data['insideStatus']['no'] = $status['no'] ?? 0;
        $data['insideStatus']['wait'] = $status['wait'] ?? 0;
        $data['insideStatus']['pass'] = $status['pass'] ?? 0;
        $data['insideStatus']['cancel'] = $status['cancel'] ?? 0;
        $data['insideStatus']['projectdelay'] = $status['projectdelay'] ?? 0;
        $data['insideStatus']['projectpause'] = $status['projectpause'] ?? 0;
        $data['insideStatus']['projecting'] = $status['projecting'] ?? 0;
        $data['insideStatus']['projectsetup'] = $status['projectsetup'] ?? 0;
        $data['insideStatus']['progressnormal'] = $status['progressnormal'] ?? 0;
        $data['insideStatus']['progressdelay'] = $status['progressdelay'] ?? 0;
        $data['insideStatus']['pause'] = $status['pause'] ?? 0;
        $data['insideStatus']['abort'] = $status['abort'] ?? 0;
        $data['insideStatus']['done'] = $status['done'] ?? 0;
        $insideUnlinks = $this->dao->query("SELECT COUNT(id) as num FROM `zt_projectplan` where deleted = '0' and year <= 2019 and outsideProject <>''")->fetch();
        $data['insideUnlinks'] = $data['projectPlanNum'] - $insideUnlinks->num;
        $yearList['2019'] = $data;
        for ($year = 2020; $year <= date('Y')+1; $year ++){
            //外部计划
            $data['year'] = $year;
            $outIds = $this->dao->select("id")->from(TABLE_OUTSIDEPLAN)->where('year')->eq($year)->andwhere('deleted')->eq(0)->fetchall('id');
            $data['idNum'] = count($outIds);
            $subs = $this->dao->query("SELECT COUNT(id) as num FROM `zt_outsideplansubprojects` where deleted = 0 and outsideProjectPlanID in (SELECT id from zt_outsideplan where year = {$year})")->fetch();
            $data['subs'] = $subs->num;
            $tasks = $this->dao->query("SELECT COUNT(id) as num FROM `zt_outsideplantasks` where deleted = 0 and outsideProjectPlanID in (SELECT id from zt_outsideplan where year = {$year})")->fetch();
            $data['tasks'] = $tasks->num;
            $status = $this->dao->query("SELECT COUNT(id) as num, `status` FROM `zt_outsideplan` where deleted = 0 and  year = {$year} GROUP BY status")->fetchAll();
            $status = array_column($status, 'num','status');
            $data['status']['wait'] = $status['wait'] ?? 0;
            $data['status']['exceptionallyfinished'] = $status['exceptionallyfinished'] ?? 0;
            $data['status']['finished'] =  $status['finished'] ?? 0;
            $data['status']['notfinished'] =  $status['notfinished'] ?? 0;
            $data['status']['exceptionallyprogressing'] =  $status['exceptionallyprogressing'] ?? 0;
            //内部年度计划
            $innerIds = $this->dao->select("id")->from(TABLE_PROJECTPLAN)->where('year')->eq($year)->andwhere('deleted')->eq(0)->fetchall('id');
            $data['projectPlanNum'] = count($innerIds);
            $status = $this->dao->query("SELECT COUNT(id) as num, `insideStatus` FROM `zt_projectplan` where deleted = '0' and  year = {$year} GROUP BY insideStatus")->fetchAll();
            $status = array_column($status, 'num','insideStatus');
//            $data['insideStatus']['status'] =$status;
            $data['insideStatus']['no'] = $status['no'] ?? 0;
            $data['insideStatus']['wait'] = $status['wait'] ?? 0;
            $data['insideStatus']['pass'] = $status['pass'] ?? 0;
            $data['insideStatus']['cancel'] = $status['cancel'] ?? 0;
            $data['insideStatus']['projectdelay'] = $status['projectdelay'] ?? 0;
            $data['insideStatus']['projectpause'] = $status['projectpause'] ?? 0;
            $data['insideStatus']['projecting'] = $status['projecting'] ?? 0;
            $data['insideStatus']['projectsetup'] = $status['projectsetup'] ?? 0;
            $data['insideStatus']['progressnormal'] = $status['progressnormal'] ?? 0;
            $data['insideStatus']['progressdelay'] = $status['progressdelay'] ?? 0;
            $data['insideStatus']['pause'] = $status['pause'] ?? 0;
            $data['insideStatus']['abort'] = $status['abort'] ?? 0;
            $data['insideStatus']['done'] = $status['done'] ?? 0;
            $insideUnlinks = $this->dao->query("SELECT COUNT(id) as num FROM `zt_projectplan` where deleted = '0' and year = {$year} and outsideProject <>''")->fetch();
            $data['insideUnlinks'] = $data['projectPlanNum'] - $insideUnlinks->num;
            $yearList[strval($year)] = $data;
        }
        if($selectedYear){
            $list[$selectedYear] = $yearList[$selectedYear] ??[];
            return  $list;
        }
        return array_reverse($yearList);
    }
    /**
     * TongYanQi 2022/9/26
     * 根据业务司局和承办单位获取(外部)项目/任务
     */
    private function getUnitDataForChart($plans, $selectedYear = '')
    {
        $outPlansInnerPlan = [];
        foreach ($plans as $plan){
            $outlinkIds = explode(',', $plan->outsideProject);
            foreach ($outlinkIds as $outlinkId)
            {
                if(empty($outlinkId)) continue;
                $outPlansInnerPlan[$outlinkId][$plan->id]['insideStatus']= $plan->insideStatus; //$outPlansInnerPlan[外部id][内部id][状态] = 已完成
            }
        }

        $sql = 'SELECT a.id as taskId, a.subProjectID,  a.subTaskBearDept, a.subTaskUnit, a.outsideProjectPlanID as outPlanId, b.year from zt_outsideplantasks a LEFT JOIN zt_outsideplan b on a.outsideProjectPlanID = b.id where a.deleted = 0';
        $taskWithOutPlans = $this->dao->query($sql)->fetchAll(); //只取了外部计划的年
//                echo json_encode($taskWithOutPlans);die();
        $tmp = [];
        foreach($taskWithOutPlans as $taskItems){
            $subTaskUnitArr = explode(',', $taskItems->subTaskUnit);
            foreach ($subTaskUnitArr as $subTaskUnitCode) {
                if(empty($subTaskUnitCode)) continue;
                $tmp['units'][$subTaskUnitCode][$taskItems->year]['out'][$taskItems->outPlanId] = $taskItems->outPlanId; //类型单位-业务司局的code-年的-外部-计划id
                if(!isset($outPlansInnerPlan[$taskItems->outPlanId])) continue;
                foreach ($outPlansInnerPlan[$taskItems->outPlanId] as $id => $inPlan){
                    $tmp['units'][$subTaskUnitCode][$taskItems->year]['in']['ids'][$id] = $id; //类型单位-业务司局的code-年的-内部-计划id

                    if(isset($inPlan['insideStatus'])) {
                        $tmp['units'][$subTaskUnitCode][$taskItems->year]['in']['insideStatus'][$inPlan['insideStatus']][$id] = $id;
                    }
                }
            }
            $subTaskBearDeptArr = explode(',', $taskItems->subTaskBearDept);
            foreach ($subTaskBearDeptArr as $subTaskBearDeptCode) {
                if(empty($subTaskBearDeptCode)) continue;
                $tmp['depts'][$subTaskBearDeptCode][$taskItems->year]['out'][$taskItems->outPlanId] = $taskItems->outPlanId;
                if(empty($outPlansInnerPlan[$taskItems->outPlanId])) continue;
                foreach ($outPlansInnerPlan[$taskItems->outPlanId] as $id => $inPlan) {
                    $tmp['depts'][$subTaskBearDeptCode][$taskItems->year]['in']['ids'][$id] = $id;
                    if (isset($inPlan['insideStatus'])) {
                        $tmp['depts'][$subTaskBearDeptCode][$taskItems->year]['in']['insideStatus'][$inPlan['insideStatus']][$id] = $id;
                    }
                }
            }
        }
//        echo json_encode($tmp);
        $list =[];
        foreach ($taskWithOutPlans as $taskWithOutPlan)
        {
            if($selectedYear and $taskWithOutPlan->year != $selectedYear) continue;
            $subTaskUnitArr = explode(',', $taskWithOutPlan->subTaskBearDept);

            foreach ($subTaskUnitArr as $subTaskUnitCode) {
                if(!empty($subTaskUnitCode)) {
                    $list['depts'][$subTaskUnitCode][$taskWithOutPlan->year]['outPlanIds'][] = $taskWithOutPlan->outPlanId;
                }
            }
            $subTaskBearDeptArr = explode(',', $taskWithOutPlan->subTaskUnit);
            foreach ($subTaskBearDeptArr as $subTaskBearDeptCode) {
                if(empty($subTaskBearDeptCode)) continue;
                $list['units'][$subTaskBearDeptCode][$taskWithOutPlan->year]['outPlanIds'][] = $taskWithOutPlan->outPlanId;
            }
        }
        foreach ($list as $typeKey => &$typeInfo){ //两个分类

            foreach ($typeInfo as $unitType =>&$everyCompanyInfo) { //给个分类中的每个单位
                foreach ($everyCompanyInfo as $year => &$yearInfo) { //每个单位的每年
                    if($selectedYear and $year != $selectedYear) continue;
                    if($typeKey == 'units') {$whereCond = 'subTaskUnit'; } else { $whereCond =  'subTaskBearDept'; }
                    $sql = 'deleted = 0 and `year` = '.$year.'  and id in (SELECT DISTINCT(outsideProjectPlanID) from zt_outsideplantasks where deleted = 0 and '.$whereCond.' like "%'.$unitType.'%")';
                    $outIdArr = $this->dao->select('id')->from(TABLE_OUTSIDEPLAN)->where($sql)->fetchall();
                    $whereinId = '';
                    foreach ($outIdArr as $iditem){
                        $whereinId .= $iditem->id.',';
                    }
                    $yearInfo['idNum'] = count($outIdArr);
                    $yearInfo['subs'] = $this->dao->select('COUNT(id) as num')->from(TABLE_OUTSIDEPLANSUBPROJECTS)->where('deleted')->eq(0)->andwhere('outsideProjectPlanID')->in($whereinId)->fetch('num');
                    $yearInfo['tasks'] = $this->dao->select('COUNT(id) as num')->from(TABLE_OUTSIDEPLANTASKS)->where('deleted')->eq(0)->andwhere('outsideProjectPlanID')->in($whereinId)->fetch('num');

                    $sql = 'deleted = 0 and `year` = '.$year.' and `status` = "wait" and id in (SELECT DISTINCT(outsideProjectPlanID) from zt_outsideplantasks where deleted = 0 and '.$whereCond.' like "%'.$unitType.'%")';
                    $yearInfo['status']['wait']                     = $this->dao->select('COUNT(id) as num')->from(TABLE_OUTSIDEPLAN)->where($sql)->fetch('num');
                    $sql = 'deleted = 0 and `year` = '.$year.' and `status` = "exceptionallyfinished" and id in (SELECT DISTINCT(outsideProjectPlanID) from zt_outsideplantasks where deleted = 0 and '.$whereCond.' like "%'.$unitType.'%")';
                    $yearInfo['status']['exceptionallyfinished']    =  $this->dao->select('COUNT(id) as num')->from(TABLE_OUTSIDEPLAN)->where($sql)->fetch('num');
                    $sql = 'deleted = 0 and `year` = '.$year.' and `status` = "finished" and id in (SELECT DISTINCT(outsideProjectPlanID) from zt_outsideplantasks where deleted = 0 and '.$whereCond.' like "%'.$unitType.'%")';
                    $yearInfo['status']['finished']                 =  $this->dao->select('COUNT(id) as num')->from(TABLE_OUTSIDEPLAN)->where($sql)->fetch('num');
                    $sql = 'deleted = 0 and `year` = '.$year.' and `status` = "notfinished" and id in (SELECT DISTINCT(outsideProjectPlanID) from zt_outsideplantasks where deleted = 0 and '.$whereCond.' like "%'.$unitType.'%")';
                    $yearInfo['status']['notfinished']              =  $this->dao->select('COUNT(id) as num')->from(TABLE_OUTSIDEPLAN)->where($sql)->fetch('num');
                    $sql = 'deleted = 0 and `year` = '.$year.' and `status` = "exceptionallyprogressing" and id in (SELECT DISTINCT(outsideProjectPlanID) from zt_outsideplantasks where deleted = 0 and '.$whereCond.' like "%'.$unitType.'%")';
                    $yearInfo['status']['exceptionallyprogressing'] =  $this->dao->select('COUNT(id) as num')->from(TABLE_OUTSIDEPLAN)->where($sql)->fetch('num');
                    $yearInfo['projectPlanNum']                     = isset($tmp[$typeKey][$unitType][$year]['in']['ids']) ? count($tmp[$typeKey][$unitType][$year]['in']['ids']) :0;
                    $yearInfo['insideStatus']['no']                 = isset($tmp[$typeKey][$unitType][$year]['in']['insideStatus']['no']) ? count($tmp[$typeKey][$unitType][$year]['in']['insideStatus']['no']) : 0;
                    $yearInfo['insideStatus']['wait']               = isset($tmp[$typeKey][$unitType][$year]['in']['insideStatus']['wait']) ? count($tmp[$typeKey][$unitType][$year]['in']['insideStatus']['wait']) : 0;
                    $yearInfo['insideStatus']['pass']               = isset($tmp[$typeKey][$unitType][$year]['in']['insideStatus']['pass']) ? count($tmp[$typeKey][$unitType][$year]['in']['insideStatus']['pass']) : 0;
                    $yearInfo['insideStatus']['cancel']             = isset($tmp[$typeKey][$unitType][$year]['in']['insideStatus']['cancel']) ? count($tmp[$typeKey][$unitType][$year]['in']['insideStatus']['cancel']) : 0;
                    $yearInfo['insideStatus']['projectdelay']       = isset($tmp[$typeKey][$unitType][$year]['in']['insideStatus']['projectdelay']) ? count($tmp[$typeKey][$unitType][$year]['in']['insideStatus']['projectdelay']) : 0;
                    $yearInfo['insideStatus']['projectpause']       = isset($tmp[$typeKey][$unitType][$year]['in']['insideStatus']['projectpause']) ? count($tmp[$typeKey][$unitType][$year]['in']['insideStatus']['projectpause']) : 0;
                    $yearInfo['insideStatus']['projecting']         = isset($tmp[$typeKey][$unitType][$year]['in']['insideStatus']['projecting']) ? count($tmp[$typeKey][$unitType][$year]['in']['insideStatus']['projecting']) : 0;
                    $yearInfo['insideStatus']['projectsetup']       = isset($tmp[$typeKey][$unitType][$year]['in']['insideStatus']['projectsetup']) ? count($tmp[$typeKey][$unitType][$year]['in']['insideStatus']['projectsetup']) : 0;
                    $yearInfo['insideStatus']['progressnormal']     = isset($tmp[$typeKey][$unitType][$year]['in']['insideStatus']['progressnormal']) ? count($tmp[$typeKey][$unitType][$year]['in']['insideStatus']['progressnormal']) : 0;
                    $yearInfo['insideStatus']['progressdelay']      = isset($tmp[$typeKey][$unitType][$year]['in']['insideStatus']['progressdelay']) ? count($tmp[$typeKey][$unitType][$year]['in']['insideStatus']['progressdelay']) : 0;
                    $yearInfo['insideStatus']['pause']              = isset($tmp[$typeKey][$unitType][$year]['in']['insideStatus']['pause']) ? count($tmp[$typeKey][$unitType][$year]['in']['insideStatus']['pause']) : 0;
                    $yearInfo['insideStatus']['abort']              = isset($tmp[$typeKey][$unitType][$year]['in']['insideStatus']['abort']) ? count($tmp[$typeKey][$unitType][$year]['in']['insideStatus']['abort']) : 0;
                    $yearInfo['insideStatus']['done']               = isset($tmp[$typeKey][$unitType][$year]['in']['insideStatus']['done']) ? count($tmp[$typeKey][$unitType][$year]['in']['insideStatus']['done']) : 0;

                }
            }
        }

        return $list;
    }

    /**
     * TongYanQi 2022/9/28
     * 导出年度统计数据
     */
    public function getChartYearDateForExport()
    {
        $outPlans = $this->getYearDataForChart();
        $yearList = [];
        //外部计划
        foreach ($outPlans as $outPlan)
        {
            $yearData = new stdClass();
            $yearData->year = $outPlan['year'];
            $yearData->idNum = $outPlan['idNum'];
            $yearData->subs = $outPlan['subs'];
            $yearData->tasks = $outPlan['task'];
            $yearData->status_wait = $outPlan['status']['wait'] ;
            $yearData->status_exceptionallyfinished = $outPlan['status']['exceptionallyfinished'] ?? 0;
            $yearData->status_finished =  $outPlan['status']['finished'] ?? 0;
            $yearData->status_notfinished =  $outPlan['status']['notfinished'] ?? 0;
            $yearData->status_exceptionallyprogressing =  $outPlan['status']['exceptionallyprogressing'] ?? 0;
            $yearData->projectPlanNum = $outPlan['projectPlanNum'] ?? 0;
            $yearData->insideStatus_wait = $outPlan['insideStatus']['wait'] ?? 0;
            $yearData->insideStatus_pass = $outPlan['insideStatus']['pass'] ?? 0;
            $yearData->insideStatus_cancel = $outPlan['insideStatus']['cancel'] ?? 0;
            $yearData->insideStatus_projectdelay = $outPlan['insideStatus']['projectdelay'] ?? 0;
            $yearData->insideStatus_projectpause = $outPlan['insideStatus']['projectpause'] ?? 0;
            $yearData->insideStatus_projecting =  $outPlan['insideStatus']['projecting'] ?? 0;
            $yearData->insideStatus_projectsetup =  $outPlan['insideStatus']['projectsetup'] ?? 0;
            $yearData->insideStatus_progressnormal =  $outPlan['insideStatus']['progressnormal'] ?? 0;
            $yearData->insideStatus_progressdelay =  $outPlan['insideStatus']['progressdelay'] ?? 0;
            $yearData->insideStatus_pause =  $outPlan['insideStatus']['pause'] ?? 0;
            $yearData->insideStatus_abort =  $outPlan['insideStatus']['abort'] ?? 0;
            $yearData->insideStatus_done =  $outPlan['insideStatus']['done'] ?? 0;
            $yearData->insideUnlinks = $outPlan[insideUnlinks];
//            $yearData->projectPlanNum = $yearData->projectPlanNum + $yearData->insideUnlinks;
            $yearList[] = $yearData;
        }
        return $yearList;
    }

    /**
     * TongYanQi 2022/9/29
     * 导出承建单位和业务司局的年度数据
     */
    public function getChartUnitDataForExport()
    {
        $plans =  $this->dao->select('id,outsideProject,status,insideStatus')->from(TABLE_PROJECTPLAN)->where('deleted')->eq(0)->andwhere('outsideProject')->ne('')->fetchall();
        $outPlansInnerPlan = [];
        foreach ($plans as $plan){
            $outlinkIds = explode(',', $plan->outsideProject);
            foreach ($outlinkIds as $outlinkId)
            {
                if(empty($outlinkId)) continue;
                $outPlansInnerPlan[$outlinkId][$plan->id]['insideStatus']= $plan->insideStatus; //$outPlansInnerPlan[外部id][内部id][状态] = 已完成
            }
        }

        $sql = 'SELECT a.id as taskId, a.subProjectID,  a.subTaskBearDept, a.subTaskUnit, a.outsideProjectPlanID as outPlanId, b.year from zt_outsideplantasks a LEFT JOIN zt_outsideplan b on a.outsideProjectPlanID = b.id where a.deleted = 0';
        $taskWithOutPlans = $this->dao->query($sql)->fetchAll(); //只取了外部计划的年
//                echo json_encode($taskWithOutPlans);die();
        $tmp = [];
        foreach($taskWithOutPlans as $taskItems){
            $subTaskUnitArr = explode(',', $taskItems->subTaskUnit);
            foreach ($subTaskUnitArr as $subTaskUnitCode) {
                if(empty($subTaskUnitCode)) continue;
                $tmp['units'][$subTaskUnitCode][$taskItems->year]['out'][$taskItems->outPlanId] = $taskItems->outPlanId; //类型单位-业务司局的code-年的-外部-计划id
                if(!isset($outPlansInnerPlan[$taskItems->outPlanId])) continue;
                foreach ($outPlansInnerPlan[$taskItems->outPlanId] as $id => $inPlan){
                    $tmp['units'][$subTaskUnitCode][$taskItems->year]['in']['ids'][$id] = $id; //类型单位-业务司局的code-年的-内部-计划id

                    if(isset($inPlan['insideStatus'])) {
                        $tmp['units'][$subTaskUnitCode][$taskItems->year]['in']['insideStatus'][$inPlan['insideStatus']][$id] = $id;
                    }
                }
            }
            $subTaskBearDeptArr = explode(',', $taskItems->subTaskBearDept);
            foreach ($subTaskBearDeptArr as $subTaskBearDeptCode) {
                if(empty($subTaskBearDeptCode)) continue;
                $tmp['depts'][$subTaskBearDeptCode][$taskItems->year]['out'][$taskItems->outPlanId] = $taskItems->outPlanId;
                if(empty($outPlansInnerPlan[$taskItems->outPlanId])) continue;
                foreach ($outPlansInnerPlan[$taskItems->outPlanId] as $id => $inPlan) {
                    $tmp['depts'][$subTaskBearDeptCode][$taskItems->year]['in']['ids'][$id] = $id;
                    if (isset($inPlan['insideStatus'])) {
                        $tmp['depts'][$subTaskBearDeptCode][$taskItems->year]['in']['insideStatus'][$inPlan['insideStatus']][$id] = $id;
                    }
                }
            }
        }
//        echo json_encode($tmp);
        $list =[];
        foreach ($taskWithOutPlans as $taskWithOutPlan)
        {
            $subTaskUnitArr = explode(',', $taskWithOutPlan->subTaskBearDept);

            foreach ($subTaskUnitArr as $subTaskUnitCode) {
                if(!empty($subTaskUnitCode)) {
                    $list['depts'][$subTaskUnitCode][$taskWithOutPlan->year]['outPlanIds'][] = $taskWithOutPlan->outPlanId;
                }
            }
            $subTaskBearDeptArr = explode(',', $taskWithOutPlan->subTaskUnit);
            foreach ($subTaskBearDeptArr as $subTaskBearDeptCode) {
                if(empty($subTaskBearDeptCode)) continue;
                $list['units'][$subTaskBearDeptCode][$taskWithOutPlan->year]['outPlanIds'][] = $taskWithOutPlan->outPlanId;
            }
        }
        $this->loadModel('application');
        foreach ($list as $typeKey => &$typeInfo){ //两个分类

            foreach ($typeInfo as $unitType =>&$everyCompanyInfo) { //给个分类中的每个单位
                foreach ($everyCompanyInfo as $year => &$yearData) { //每个单位的每年
                    if($typeKey == 'units') {$whereCond = 'subTaskUnit'; } else { $whereCond =  'subTaskBearDept'; }
                    $yearInfo = new stdClass();
                    $yearInfo->key = $typeKey == 'units' ? zget($this->lang->outsideplan->subProjectUnitList, $unitType) : zget($this->lang->application->teamList, $unitType);

                    $yearInfo->year = $year;

                    $sql = 'deleted = 0 and `year` = '.$year.'  and id in (SELECT DISTINCT(outsideProjectPlanID) from zt_outsideplantasks where deleted = 0 and '.$whereCond.' like "%'.$unitType.'%")';
                    $outIdArr = $this->dao->select('id')->from(TABLE_OUTSIDEPLAN)->where($sql)->fetchall();
                    $whereinId = '';
                    foreach ($outIdArr as $iditem){
                        $whereinId .= $iditem->id.',';
                    }
                    $yearInfo->idNum = count($outIdArr);
                    $yearInfo->subs = $this->dao->select('COUNT(id) as num')->from(TABLE_OUTSIDEPLANSUBPROJECTS)->where('deleted')->eq(0)->andwhere('outsideProjectPlanID')->in($whereinId)->fetch('num');
                    $yearInfo->tasks = $this->dao->select('COUNT(id) as num')->from(TABLE_OUTSIDEPLANTASKS)->where('deleted')->eq(0)->andwhere('outsideProjectPlanID')->in($whereinId)->fetch('num');

                    $sql = 'deleted = 0 and `year` = '.$year.' and `status` = "wait" and id in (SELECT DISTINCT(outsideProjectPlanID) from zt_outsideplantasks where deleted = 0 and '.$whereCond.' like "%'.$unitType.'%")';
                    $yearInfo->status_wait                     = $this->dao->select('COUNT(id) as num')->from(TABLE_OUTSIDEPLAN)->where($sql)->fetch('num');
                    $sql = 'deleted = 0 and `year` = '.$year.' and `status` = "exceptionallyfinished" and id in (SELECT DISTINCT(outsideProjectPlanID) from zt_outsideplantasks where deleted = 0 and '.$whereCond.' like "%'.$unitType.'%")';
                    $yearInfo->status_exceptionallyfinished    =  $this->dao->select('COUNT(id) as num')->from(TABLE_OUTSIDEPLAN)->where($sql)->fetch('num');
                    $sql = 'deleted = 0 and `year` = '.$year.' and `status` = "finished" and id in (SELECT DISTINCT(outsideProjectPlanID) from zt_outsideplantasks where deleted = 0 and '.$whereCond.' like "%'.$unitType.'%")';
                    $yearInfo->status_finished                 =  $this->dao->select('COUNT(id) as num')->from(TABLE_OUTSIDEPLAN)->where($sql)->fetch('num');
                    $sql = 'deleted = 0 and `year` = '.$year.' and `status` = "notfinished" and id in (SELECT DISTINCT(outsideProjectPlanID) from zt_outsideplantasks where deleted = 0 and '.$whereCond.' like "%'.$unitType.'%")';
                    $yearInfo->status_notfinished              =  $this->dao->select('COUNT(id) as num')->from(TABLE_OUTSIDEPLAN)->where($sql)->fetch('num');
                    $sql = 'deleted = 0 and `year` = '.$year.' and `status` = "exceptionallyprogressing" and id in (SELECT DISTINCT(outsideProjectPlanID) from zt_outsideplantasks where deleted = 0 and '.$whereCond.' like "%'.$unitType.'%")';
                    $yearInfo->status_exceptionallyprogressing =  $this->dao->select('COUNT(id) as num')->from(TABLE_OUTSIDEPLAN)->where($sql)->fetch('num');

                    $yearInfo->projectPlanNum                  = isset($tmp[$typeKey][$unitType][$year]['in']['ids']) ? count($tmp[$typeKey][$unitType][$year]['in']['ids']) :0;
                    $yearInfo->insideStatus_no                = isset($tmp[$typeKey][$unitType][$year]['in']['insideStatus']['no']) ? count($tmp[$typeKey][$unitType][$year]['in']['insideStatus']['no']) : 0;
                    $yearInfo->insideStatus_wait               = isset($tmp[$typeKey][$unitType][$year]['in']['insideStatus']['wait']) ? count($tmp[$typeKey][$unitType][$year]['in']['insideStatus']['wait']) : 0;
                    $yearInfo->insideStatus_pass               = isset($tmp[$typeKey][$unitType][$year]['in']['insideStatus']['pass']) ? count($tmp[$typeKey][$unitType][$year]['in']['insideStatus']['pass']) : 0;
                    $yearInfo->insideStatus_cancel            = isset($tmp[$typeKey][$unitType][$year]['in']['insideStatus']['cancel']) ? count($tmp[$typeKey][$unitType][$year]['in']['insideStatus']['cancel']) : 0;
                    $yearInfo->insideStatus_projectdelay      = isset($tmp[$typeKey][$unitType][$year]['in']['insideStatus']['projectdelay']) ? count($tmp[$typeKey][$unitType][$year]['in']['insideStatus']['projectdelay']) : 0;
                    $yearInfo->insideStatus_projectpause       = isset($tmp[$typeKey][$unitType][$year]['in']['insideStatus']['projectpause']) ? count($tmp[$typeKey][$unitType][$year]['in']['insideStatus']['projectpause']) : 0;
                    $yearInfo->insideStatus_projecting        = isset($tmp[$typeKey][$unitType][$year]['in']['insideStatus']['projecting']) ? count($tmp[$typeKey][$unitType][$year]['in']['insideStatus']['projecting']) : 0;
                    $yearInfo->insideStatus_projectsetup      = isset($tmp[$typeKey][$unitType][$year]['in']['insideStatus']['projectsetup']) ? count($tmp[$typeKey][$unitType][$year]['in']['insideStatus']['projectsetup']) : 0;
                    $yearInfo->insideStatus_progressnormal     = isset($tmp[$typeKey][$unitType][$year]['in']['insideStatus']['progressnormal']) ? count($tmp[$typeKey][$unitType][$year]['in']['insideStatus']['progressnormal']) : 0;
                    $yearInfo->insideStatus_progressdelay      = isset($tmp[$typeKey][$unitType][$year]['in']['insideStatus']['progressdelay']) ? count($tmp[$typeKey][$unitType][$year]['in']['insideStatus']['progressdelay']) : 0;
                    $yearInfo->insideStatus_pause              = isset($tmp[$typeKey][$unitType][$year]['in']['insideStatus']['pause']) ? count($tmp[$typeKey][$unitType][$year]['in']['insideStatus']['pause']) : 0;
                    $yearInfo->insideStatus_abort              = isset($tmp[$typeKey][$unitType][$year]['in']['insideStatus']['abort']) ? count($tmp[$typeKey][$unitType][$year]['in']['insideStatus']['abort']) : 0;
                    $yearInfo->insideStatus_done               = isset($tmp[$typeKey][$unitType][$year]['in']['insideStatus']['done']) ? count($tmp[$typeKey][$unitType][$year]['in']['insideStatus']['done']) : 0;
                    $infoList[$typeKey][] = $yearInfo;
                }
            }
        }

        return $infoList;
    }

    /**
     * TongYanQi 2022/9/30
     * 根据外部计划取业务司局和承建部门
     */
    public function getUnitsByPlanIds($ids)
    {
        $tasks =  $this->dao->select('outsideProjectPlanID,subTaskUnit,subTaskBearDept')->from(TABLE_OUTSIDEPLANTASKS)->where('deleted')->eq(0)->andwhere('outsideProjectPlanID')->in($ids)->fetchall();


        $plans = [];
        foreach ($tasks as $task)
        {
            $units = explode(',', $task->subTaskUnit);

            foreach ($units as $unit){
                if(empty($unit)) continue;
                $plans[$task->outsideProjectPlanID]['units'][$unit] = zget($this->lang->outsideplan->subProjectUnitList, $unit);
            }

            $depts = explode(',', $task->subTaskBearDept);

            foreach ($depts as $dept){
                if(empty($dept)) continue;
                $plans[$task->outsideProjectPlanID]['depts'][] = zget($this->lang->application->teamList, $dept);
            }

            if(empty($plans[$task->outsideProjectPlanID]['taskNum'])) $plans[$task->outsideProjectPlanID]['taskNum'] = 0;
            $plans[$task->outsideProjectPlanID]['taskNum']++;
        }
        return $plans;
    }
    public function str_replace_first($from, $to, $content)
    {
        $from = '/'.preg_quote($from, '/').'/';

        return preg_replace($from, $to, $content, 1);
    }


}
