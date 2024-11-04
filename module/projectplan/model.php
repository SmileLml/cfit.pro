<?php
class projectplanModel extends model
{
    static $_fields = '*';
    /**
     * Project: chengfangjinke
     * Method: getList
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:21
     * Desc: This is the code comment. This method is called getList.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $browseType
     * @param $queryID
     * @param $orderBy
     * @param $pager
     * @return mixed
     */
    public function getList($browseType, $queryID, $orderBy, $pager, $secondLine = 0, $modelName = 'projectplan',$shanghaipart=0)
    {
        $projectplanQuery = '';
        if ($browseType == 'bysearch') {

            /** @var searchModel $searchModel */
            $searchModel = $this->loadModel('search');
            $query = $queryID ? $searchModel->getQuery($queryID) : '';

            if ($query) {
                $this->session->set($modelName.'Query', $query->sql);
                $this->session->set($modelName.'Form', $query->form);
            }
            $modelNameQuery = $modelName.'Query';
            if ($this->session->$modelNameQuery == false) $this->session->set($modelName.'Query', ' 1 = 1');

            $projectplanQuery = $this->session->$modelNameQuery;

            //待处理人搜索处理
            if(strpos($projectplanQuery, '`pending`') !== false) {
                $queryArray = explode('AND', $projectplanQuery);
                foreach ($queryArray as $k=>$v){

                    if(strpos($v, '`pending`')){
                        if(strpos($v, '%')){
                            $userArray = explode('%', $v);
                            $user = $userArray[1];

                            //查询与之相关的待处理人ID集合
                            $planIds = $this->getIdsBydealUserAbout($user);

                            if(strpos($v,"NOT LIKE") != false){
                                if(isset($queryArray[$k+1])){
                                    if(strpos($v,")") != false){
                                        $queryArray[$k] = " (`id` NOT ". helper::dbIN($planIds)." AND !find_in_set('{$user}',owner)) )";
                                    }else{
                                        $queryArray[$k] = " (`id` NOT ". helper::dbIN($planIds)." AND !find_in_set('{$user}',owner)) ";
                                    }

                                }else{
                                    $queryArray[$k] = " (`id` NOT ". helper::dbIN($planIds).") AND !find_in_set('{$user}',owner) ))";
                                }
                            }else{
                                if(isset($queryArray[$k+1])){
                                    if(strpos($v,")") != false){
                                        $queryArray[$k] = " (`id` ". helper::dbIN($planIds)." OR (status in ( ".$this->lang->projectplan->browseSearchAllowStatus.") AND changeStatus != 'pending' AND find_in_set('{$user}' ,owner)) ))";

                                    }else{
                                        $queryArray[$k] = " (`id` ". helper::dbIN($planIds)." OR (status in ( ".$this->lang->projectplan->browseSearchAllowStatus.") AND changeStatus != 'pending' AND find_in_set('{$user}' ,owner)) )";

                                    }
                                }else{
                                    $queryArray[$k] = " (`id` ". helper::dbIN($planIds)." OR (status in ( ".$this->lang->projectplan->browseSearchAllowStatus.") AND changeStatus != 'pending' AND find_in_set('{$user}' ,owner)) ) ))";
                                }
                            }

                        }else if(strpos($v, '!=') !== false){
                            $userArray = explode('!=', $v);
                            $user = trim($userArray[1],"  )");
                            $user = trim($user,'\'');

                            //查询与之相关的待处理人ID集合
                            $planIds = $this->getIdsBydealUserAbout($user);
                            if(isset($queryArray[$k+1])){
                                if(strpos($userArray[1],")") != false){
                                    $queryArray[$k] = " (`id` NOT ". helper::dbIN($planIds)." AND !find_in_set('{$user}',owner))) ";
                                }else{
                                    $queryArray[$k] = " (`id` NOT ". helper::dbIN($planIds)." AND !find_in_set('{$user}',owner)) ";
                                }

                            }else{
                                $queryArray[$k] = " (`id` NOT ". helper::dbIN($planIds)." AND !find_in_set('{$user}',owner))) )";
                            }

                        }else if(strpos($v, '=') !== false){
                            $userArray = explode('=', $v);
                            $user = trim($userArray[1],"  )");
                            $user = trim($user,'\'');

                            //查询与之相关的待处理人ID集合
                            $planIds = $this->getIdsBydealUserAbout($user);
                            if(isset($queryArray[$k+1])){
                                if(strpos($userArray[1],")") != false){
                                    $queryArray[$k] = " (`id` ". helper::dbIN($planIds)." OR (status in ( ".$this->lang->projectplan->browseSearchAllowStatus.") AND changeStatus != 'pending' AND find_in_set('{$user}' ,owner))) ) ";

                                }else{
                                    $queryArray[$k] = " (`id` ". helper::dbIN($planIds)." OR (status in ( ".$this->lang->projectplan->browseSearchAllowStatus.") AND changeStatus != 'pending' AND find_in_set('{$user}' ,owner)) ) ";

                                }
                            }else{
                                $queryArray[$k] = " (`id` ". helper::dbIN($planIds)." OR (status in ( ".$this->lang->projectplan->browseSearchAllowStatus.") AND changeStatus != 'pending' AND find_in_set('{$user}' ,owner)) )) )";
                            }

                        }
                    }
                }
                $projectplanQuery = implode($queryArray," AND");
            }
        }

        //待处理人搜索
        $shanghaiDeptList = [30,31,32,33,34,35,36,37,38,39,40,41];
        $shDept = array_filter(explode(',',$this->lang->projectplan->shProjectPlanDeptList['shDeptList'])); //后台配置的可查看项目的上海部门
        $projectplans = $this->dao->select(self::$_fields)->from(TABLE_PROJECTPLAN)
            ->where(1)
            ->andWhere('deleted')->ne('1')
            ->beginIF( $secondLine !== 'all' && !in_array($this->app->user->dept,$shDept)  && $shanghaipart != 1)->andWhere('secondLine')->eq($secondLine)->fi() //各tab，其他部门可以看到所有，包含上海
            ->beginIF($browseType !== 'all' )->andWhere('secondLine')->eq($secondLine)->fi() //各tab上海只能看到上海的项目
            ->beginIF($browseType == 'all' && $secondLine == 0 )->andWhere('bearDept')->notin($shanghaiDeptList)->fi() // 列表tab- 全部 不包含上海项目
            //->beginIF($browseType == 'all' && $secondLine == 0 && $shanghaipart != 1 && in_array($this->app->user->dept,$shDept))->andWhere('bearDept')->eq('')->fi() // 列表tab- 全部 上海不允许看到
            ->beginIF($shanghaipart == 1)->andWhere('bearDept')->in($shanghaiDeptList)->andWhere('secondLine')->eq(0)->fi()
            //迭代33透出  deleted状态数据
//            ->beginIF($browseType == 'all')->andWhere('status')->ne('deleted')->fi()
            ->beginIF($browseType != 'all' and $browseType != 'second' and $browseType != 'bysearch' and $browseType != 'noprojected')->andWhere('status')->eq($browseType)->fi()
            ->beginIF($browseType != 'all' and $browseType != 'bysearch' and $browseType == 'noprojected')->andWhere('status')->ne('projected')->fi()//新增 未立项 数据获取 除已立项的所有
            ->beginIF($browseType == 'bysearch')->andWhere($projectplanQuery)->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');

        //后台配置上海部门为空且是上海人员
       /* if(!$shDept && in_array($this->app->user->dept,$shanghaiDeptList)){
            $projectplans = array();
        }*/

        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'projectplan', $browseType != 'bysearch');

        return $this->processPlan($projectplans);
    }


    public function getAllList($field='*',$order="id_DESC"){

       return $this->dao->select($field)->from(TABLE_PROJECTPLAN)->where('deleted')->eq(0)->andWhere('secondLine')->eq(0)->orderBy($order)->fetchAll();
    }

    public function getAllIncludeDeleteList($field='*',$order="id_DESC"){

        return $this->dao->select($field)->from(TABLE_PROJECTPLAN)->where('secondLine')->eq(0)->orderBy($order)->fetchAll();
    }


    public function getByIDMultipleList($planIDS,$field="*"){

        //删除条件是为了前台要过滤 删除的年度计划展示
       return $this->dao->select($field)->from(TABLE_PROJECTPLAN)->where("id")->in($planIDS)->andWhere("deleted")->eq('0')->fetchAll();

    }

    /**
     * 获得项目代号列表
     *
     * @param $planIds
     * @return array
     */
    public function getCodeListByPlanIds($planIds){
        $data = [];
        if(!$planIds){
            return $data;
        }
        $select = 'id,mark,name,bearDept';
        $planList = $this->dao->select($select)->from(TABLE_PROJECTPLAN)->where("id")->in($planIds)->fetchAll();

        if(empty($planList)){
            return $data;
        }
        $deptIds    = [];
        foreach ($planList as $val){
            $bearDeptIds = explode(',', $val->bearDept);
            $deptIds     = array_merge($deptIds, $bearDeptIds);
        }
        //部门信息
        $deptList  = $this->loadModel('dept')->getDeptListByIds($deptIds, 'id,name');
        $deptList  = array_column($deptList, null, 'id');

        foreach ($planList as $val){
            $planId    = $val->id;
            $mark      = $val->mark;
            $name      = $val->name;
            $bearDept  = $val->bearDept;
            if($mark){
                $code = $mark;
            }else{
                $code = $name;
            }
            $deptNameList = [];
            $bearDeptIds = explode(',', $bearDept);
            foreach ($bearDeptIds as $deptId){
                $deptInfo = zget($deptList, $deptId);
                $deptName = zget($deptInfo, 'name');
                $deptNameList[] = $deptName;
            }
            $codeInfo = $code . '('.implode(' ', $deptNameList).')';
            $data[$planId] = $codeInfo;
        }
        return $data;
    }


    public function getListInLook($browseType ='all', $queryID = 0, $orderBy = 'id_desc', $pager, $outSubs = [], $outPlans = [])
    {
        /** @var outsideplanModel $outPlanModel */
        $outPlanModel = $this->loadModel('outsideplan');
        $this->loadModel('application');
        self::$_fields ='id,name,status,bearDept,insideStatus,begin,end,code,mark,reviewStage,yearVersion,changeStatus,changeVersion,version,project,changeStage,outsideTask,outsideSubProject,outsideProject,planCode,workload';
        $plans = $this->getList($browseType, $queryID, $orderBy, $pager, 'all','outsideplan');

        if(empty($outSubs))  $outSubs = $outPlanModel->getSubProjectPairs();
        if(empty($outPlans)) $outPlans = $outPlanModel->getAllBrief();

        foreach ($plans as &$plan){
            unset($plan->planRemark);
            unset($plan->content);
            $taskMap = array('estimate' => 0, 'consumed' => 0, 'left' => 0, 'progress' => 0);
            $plan->budget = 0;
            if($plan->project) {
                //项目工作量
                $projectTasks = $this->dao->select('*')->from(TABLE_TASK)
                    ->where('deleted')->eq(0)
                    ->andWhere('project')->eq($plan->project)
                    ->andWhere('parent')->eq(0)
                    ->fetchAll('id');

                foreach ($projectTasks as $projectTasks) {
                    $taskMap['estimate'] += $projectTasks->estimate;
                    $taskMap['consumed'] += $projectTasks->consumed;
                    $taskMap['progress'] += $projectTasks->progress * $projectTasks->estimate;
                }
                $projectInfo = $this->dao->select('budget')->from(TABLE_PROJECT) ->where('id')->eq($plan->project) ->fetch();
                $plan->budget = $projectInfo->budget;
            }

            $plan->estimate = $taskMap['estimate'];
            $plan->consumed = $taskMap['consumed'];
            if(empty($taskMap['estimate']) || empty($taskMap['consumed'])) {
                $plan->progress = "0%";
            } else {
                $plan->progress = round($taskMap['progress']/$taskMap['estimate']) .'%';
            }

            $plan->outTasks =   $outPlanModel->getTasks($plan->outsideTask);
            foreach ($plan->outTasks as &$task){
                $task->subProjectName = zget($outSubs, $task->subProjectID);
                $task->outsideProjectPlanName   =  $outPlans[$task->outsideProjectPlanID]->name ?? "";
                $task->outsideProjectPlanStatus =  $outPlans[$task->outsideProjectPlanID]->status ? zget($this->lang->outsideplan->statusList, $outPlans[$task->outsideProjectPlanID]->status): "";
                $task->outsideProjectPlanCode =  $outPlans[$task->outsideProjectPlanID]->code ??  "";
                $task->outsideProjectPlanBegin =  $outPlans[$task->outsideProjectPlanID]->begin ?? "";
                $task->outsideProjectPlanEnd =  $outPlans[$task->outsideProjectPlanID]->end ?? "";
                $subTaskUnits = $this->convertUnits($task->subTaskUnit);
                if($subTaskUnits){
                    $task->subTaskUnit = implode(',', $subTaskUnits);
                }
                $subTaskDepts = $this->convertDepts($task->subTaskBearDept);
                if($subTaskDepts){
                    $task->subTaskBearDept = implode(',', $subTaskDepts);
                }
            }
            $plan->row = empty($plan->outTasks)? 1 : count($plan->outTasks);
        }
        return $plans;

    }

    public function convertUnits($unitCodes)
    {
        $subTaskUnits = [];
        $units = explode(',', $unitCodes);
        foreach ($units as $unit){
            if(empty($unit)) continue;
            $subTaskUnits[$unit] = zget($this->lang->outsideplan->subProjectUnitList, $unit);
        }
        return $subTaskUnits;
    }

    public function convertDepts($subTaskBearDeptCodes)
    {
        $vlist = explode(',', $subTaskBearDeptCodes);
        $arr = [];
        foreach ($vlist as $itemv){
            if(empty($itemv)) continue;
            $arr[] = zget($this->lang->application->teamList, $itemv,'') ;
        }
        return $arr;
    }
    /*
     * 根据待处理人查询年度计划id集合
     */
    public function getIdsBydealUserAbout($user)
    {
        $info = $this->dao->select('node')->from(TABLE_REVIEWER)->where('reviewer')->eq($user)->andWhere('status')->eq('pending')->fetchAll();
        $reviewerNodeIds = [];
        foreach ($info as $node){
            $reviewerNodeIds[] = $node->node;
        }

        $objectIds = [];
        if($info){
            $nodeInfo = $this->dao->select('id,objectType,objectID')->from(TABLE_REVIEWNODE)->where('id')->in($reviewerNodeIds)->fetchAll();
            foreach ($nodeInfo as $value){
                if(in_array($value->objectType,array('planchange','projectplan','projectplanyear'))){
                    $objectIds[] = $value->objectID;
                }
            }
        }
        return array_unique($objectIds);
    }
    /**
     * Project: chengfangjinke
     * Method: getCreationByID
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:21
     * Desc: This is the code comment. This method is called getCreationByID.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $planID
     * @return false
     */
    public function getCreationByID($planID)
    {
        $creation = $this->dao->select('*')->from(TABLE_PROJECTCREATION)->where('plan')->eq($planID)->fetch();
        if ($creation) {
            $creation = $this->loadModel('file')->replaceImgURL($creation, 'background,range,goal,stakeholder,verify');
            $creation->files = $this->loadModel('file')->getByObject('projectcreation', $creation->id);
            return $creation;
        }
        return false;
    }

    /**
     * Project: chengfangjinke
     * Method: getOutsidePairs
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:21
     * Desc: This is the code comment. This method is called getOutsidePairs.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $planID
     * @return mixed
     */
    public function getOutsidePairs($planID)
    {
        return $this->dao->select('id, name')->from(TABLE_OUTSIDEPLAN)
            ->where('linkedPlan')->like("%,$planID,%")
            ->andWhere('status')->ne('deleted')->fetchPairs();
    }


    /**
     * Project: chengfangjinke
     * Method: getAllProjectsPairs
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:21
     * Desc: 用于导出项目信息
     * @param bool $secondLine
     * @param string $exWhere
     * @return mixed
     */
    public function getAllProjects($secondLine = false, $exWhere = '')
    {
        $planPairs = $this->dao->select('project as id, name')->from(TABLE_PROJECTPLAN)
            ->where('deleted')->eq(0)
            ->beginIF($secondLine)->andWhere('secondLine')->eq('1')->fi()
            ->beginIF($exWhere)->andWhere($exWhere)->fi()
            ->fetchPairs();
        return $planPairs;
    }

    /**
     * Project: chengfangjinke
     * Method: getPairs
     * User: Tony Stark
     * Year: 2022
     * Date: 2022/05/31
     * Time: 17:21
     * Desc: This is the code comment. This method is called getPairs.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @return mixed
     */
    public function getPairs($secondLine = false)
    {
        $planPairs = $this->dao->select('id, name')->from(TABLE_PROJECTPLAN)
            ->where('deleted')->eq(0)
            ->beginIF($secondLine)->andWhere('secondLine')->eq('1')->fi()
            ->fetchPairs();
        return $planPairs;
    }

    /**
     * TongYanQi 2022/9/30
     * 获取计划状态
     */
    public function getInsideStatusPairs($status="")
    {
        $planPairs = $this->dao->select('id, insideStatus')->from(TABLE_PROJECTPLAN)
            ->where('deleted')->eq(0)
            ->beginIf($status)->andWhere('status')->in($status)->fi()
            ->fetchPairs();

        return $planPairs;
    }
    /**
     * Desc:由于getPairs方法调用较多，故为需求和问题模块新增单独获取项目方法
     * Date: 2022/3/21
     * Time: 9:43
     *
     * @param false $secondLine
     * @return mixed
     *
     */
    public function getProject($secondLine)
    {
        $planPairs = $this->dao->select('project as id, name')->from(TABLE_PROJECTPLAN)
            ->where('deleted')->eq(0)
            ->beginIF($secondLine)->andWhere('secondLine')->eq('1')->fi()
            ->beginIF(!$secondLine)->andWhere('secondLine')->eq('0')->fi()
            ->fetchPairs();
        return $planPairs;
    }

    /**
     * Desc:用于问题和需求
     * Date: 2022/3/21
     * Time: 9:43
     *
     * @param false $secondLine
     * @return mixed
     *
     */
    public function getProjects($secondLine)
    {
        $planPairs = $this->dao->select('id, name')->from(TABLE_PROJECTPLAN)
            ->where('deleted')->eq(0)
            ->beginIF($secondLine)->andWhere('secondLine')->eq('1')->fi()
            ->beginIF(!$secondLine)->andWhere('secondLine')->eq('0')->fi()
            ->fetchPairs();
        return $planPairs;
    }

    /**
     * Desc:用于生产变更单
     * User: chendongcheng
     * Date: 2022/5/31
     * Time: 16:37
     *
     * @param false $secondLine
     * @return mixed
     *
     */
    public function getCodeProjects($secondLine)
    {
        $planPairs = $this->dao->select('project as id, concat(concat(concat(code,"（"),name),"）")')->from(TABLE_PROJECTPLAN)
            ->where('deleted')->eq(0)
            ->beginIF($secondLine)->andWhere('secondLine')->eq('1')->fi()
            ->beginIF(!$secondLine)->andWhere('secondLine')->eq('0')->fi()
            ->fetchPairs();
        return $planPairs;
    }


    /**
     * Desc:用于二线管理单子，增加“已关闭”筛选条件
     * User: 顾超男
     * Date: 2022/7/14
     * Time: 16:37
     *
     * @param false $secondLine
     * @return mixed
     *
     */
    public function getAliveProjectIDs($secondLine)
    {
       // $planPairs = $this->dao->select('t1.id,t2.name')->from(TABLE_PROJECTPLAN )->alias('t1')
         //20221009 修改id
         $planPairs = $this->dao->select('t1.project,t2.name')->from(TABLE_PROJECTPLAN )->alias('t1')
            ->leftjoin(TABLE_PROJECT)->alias('t2')
            ->on('t1.project=t2.id')
            ->where('t1.deleted')->eq(0)
            ->andwhere('t2.status')->ne('closed')
            ->beginIF($secondLine)->andwhere('t1.year')->eq('2022')->fi()
            ->beginIF($secondLine)->andwhere('t1.code')->like('%EX')->fi()
            ->beginIF($secondLine)->andWhere('t1.secondLine')->eq('1')->fi()
            ->beginIF(!$secondLine)->andWhere('t1.secondLine')->eq('0')->fi()
            ->fetchPairs();
        return $planPairs;
    }

    /**
     * Desc:用于二线管理单子，增加“已关闭”筛选条件
     * User: 顾超男
     * Date: 2022/7/14
     * Time: 16:37
     *
     * @param false $secondLine
     * @return mixed
     **/

    public function getAliveProjects($secondLine)
    {
        $planPairs = $this->dao->select('t1.project,t2.name')->from(TABLE_PROJECTPLAN)->alias('t1')
            ->leftjoin(TABLE_PROJECT)->alias('t2')
            ->on('t1.project=t2.id')
            ->where('t1.deleted')->eq(0)
            ->andwhere('t2.status')->ne('closed')
            ->beginIF($secondLine)->andwhere('t1.year')->eq('2022')->fi()
            ->beginIF($secondLine)->andwhere('t1.code')->like('%EX')->fi()
            ->beginIF($secondLine)->andWhere('t1.secondLine')->eq('1')->fi()
            ->beginIF(!$secondLine)->andWhere('t1.secondLine')->eq('0')->fi()
            ->fetchPairs();
        return $planPairs;
    }

    /**
     * Project: chengfangjinke
     * Method: getByID
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:21
     * Desc: This is the code comment. This method is called getByID.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $planID
     * @return mixed
     */
    public function getByID($planID)
    {
        $plan = $this->dao->select('*')->from(TABLE_PROJECTPLAN)->where('id')->eq($planID)->fetch();
        $plan->oldview = $plan->demand; //
        $plan = $this->loadModel('file')->replaceImgURL($plan, 'content,comment,planRemark');
        if ($plan->oldview == -1) {
            $opinionID = $this->dao->select('opinion')->from(TABLE_REQUIREMENT)->where('project')->eq($planID)->fetch('opinion');
            $plan->opinion = $this->dao->select('*')->from(TABLE_OPINION)->where('id')->eq($opinionID)->fetch() ?? null;
        } else {
            $plan->opinionList = $this->dao->select('id,name,sourceOpinion')->from(TABLE_OPINION)->where('id')->in($plan->opinion)->andwhere('status')->ne('deleted')->fetchall('id') ?? null;
            $plan->demandList = $this->dao->select('id,title,code,sourceDemand')->from(TABLE_DEMAND)->where('id')->in($plan->demand)->andwhere('status')->ne('deleted')->fetchall('id') ?? null;
            $plan->requirementList = $this->dao->select('id,name,sourceRequirement')->from(TABLE_REQUIREMENT)->where('id')->in($plan->requirement)->andwhere('status')->ne('deleted')->fetchall('id') ?? null;
            $plan->problemList = $this->dao->select('id,code')->from(TABLE_PROBLEM)->where('id')->in($plan->problem)->andwhere('status')->ne('deleted')->fetchall('id') ?? null;

        }
        $plan->creation = $this->getCreationByID($planID);
        $this->loadModel('review');
        $this->loadModel('demand');

        if(in_array($plan->status, array('yearstart', 'yearwait', 'yearreviewing', 'yearpass', 'yearreject','start')))
        {
            // 获取审批年度计划的人
            if($plan->changeStatus == 'pending'){
                $plan->reviewers = $this->review->getReviewer('planchange', $planID, $plan->changeVersion, $plan->changeStage);
            }else{
                $plan->reviewers = $this->review->getReviewer('projectplanyear', $planID, $plan->yearVersion, $plan->reviewStage);
            }
        } else {
            $plan->reviewers = $this->review->getMuiltNodeReviewers('projectplan', $planID, $plan->version, $plan->reviewStage);
        }
        return $plan;
    }

    /**
     * 通过id获得主要信息
     *
     * @param $planID
     * @param string $select
     * @return mixed
     */
    public function  getMainInfoByID($planID, $select = '*')
    {
        $data = $this->dao->select($select)->from(TABLE_PROJECTPLAN)->where('id')->eq($planID)->fetch();
        return $data;
    }

    public function getSimpleByID($planID)
    {
        $plan = $this->dao->select('*')->from(TABLE_PROJECTPLAN)->where('id')->eq($planID)->fetch();
        $plan->oldview = $plan->demand; //
        $plan = $this->loadModel('file')->replaceImgURL($plan, 'content,comment,planRemark');

        return $plan;
    }

    public function getInIDs($planIDs)
    {
        $plans = $this->dao->select('*')->from(TABLE_PROJECTPLAN)->where('id')->in($planIDs)->andWhere('deleted')->ne(1)->fetchall('id');
        return $plans;
    }

    /**
     * TongYanQi 2022/9/23
     * 计划信息 用于一览表 +立项信息
     */
    public function getAllBrief()
    {
        $users = $this->loadModel('user')->getPairs('noletter');
        $deptList = $this->loadModel('dept')->getOptionMenu();
        $plans = $this->dao->select('id,outsideTask,name,code,planCode,project,isImportant,begin,end,workload,bearDept,owner,phone,status,insideStatus')->from(TABLE_PROJECTPLAN)->where('outsideProject')->ne("")->andWhere('deleted')->eq(0)->fetchall('id');
        $projectids = [];
        foreach ($plans as $plan){
            $projectids[] = $plan->project;
        }
        $projects = $this->dao->select('*')->from(TABLE_PROJECT)->where('id')->in($projectids)->andwhere('deleted')->eq(0)->fetchall('id');
        foreach ($plans as $plan){
            if(empty($projects[$plan->project])) continue;
            $plan->projectInfo = $projects[$plan->project];

            //项目人员
            $team = $this->dao->select('account')->from(TABLE_TEAM)->where('type')->eq('project')->andwhere('root')->eq($projects[$plan->project]->id)->fetchall('account');
            $members = [];
            $accounts = '';
            foreach ($team as $member){
                $members[] = zget($users, $member->account);
                $accounts .= '"'.$member->account.'",';
            }
            $accounts = trim($accounts,',');
            $plan->projectInfo->members = implode(',', $members);

            //项目人员相关部门
            $depts = $this->dao->select('dept')->from(TABLE_USER)->where("account in ( {$accounts})")->fetchall();
            $deptName = [];
            foreach ($depts as $dept){
                if(empty($dept)) continue;
                $deptName[] = zget($deptList, $dept->dept);
            }
            $deptName = array_unique($deptName);
            $plan->projectInfo->deptNames = implode(',', $deptName);

            //项目工作量
            $projectTasks = $this->dao->select('*')->from(TABLE_TASK)
                ->where('deleted')->eq(0)
                ->andWhere('project')->eq($projects[$plan->project]->id)
                ->andWhere('parent')->eq(0)
                ->fetchAll('id');

            $taskMap = array('estimate' => 0, 'consumed' => 0, 'left' => 0, 'progress' => 0);
            foreach($projectTasks as $projectTasks)
            {
                $taskMap['estimate'] += $projectTasks->estimate;
                $taskMap['consumed'] += $projectTasks->consumed;
                $taskMap['progress'] += $projectTasks->progress * $projectTasks->estimate;
            }
            $plan->projectInfo->estimate = $taskMap['estimate'];
            $plan->projectInfo->consumed = $taskMap['consumed'];
            if(empty($taskMap['estimate']) || empty($taskMap['consumed'])) {
                $plan->projectInfo->progress = "0%";
            } else {
                $plan->projectInfo->progress = round($taskMap['progress']/$taskMap['estimate']) .'%';
            }

        }
        return $plans;
    }
    /**
     * getByProjectID
     */
    public function getByProjectID($ProjectID)
    {
        $plan = $this->dao->select('*')->from(TABLE_PROJECTPLAN)->where('project')->eq($ProjectID)->fetch();


        if($plan){
            $plan = $this->loadModel('file')->replaceImgURL($plan, 'content');
            $opinionID = $this->dao->select('opinion')->from(TABLE_REQUIREMENT)->where('project')->eq($plan->id)->fetch('opinion');
            $opinion = $this->dao->select('*')->from(TABLE_OPINION)->where('id')->eq($opinionID)->fetch() ?? null;

            $plan->opinion = $opinion;
            $plan->creation = $this->getCreationByID($plan->id);
            $this->loadModel('review');
            if(in_array($plan->status, array('yearstart', 'yearwait', 'yearreviewing', 'yearpass', 'yearreject')))
            {
                // 获取审批年度计划的人
                if($plan->changeStatus == 'pending'){
                    $plan->reviewers = $this->review->getReviewer('planchange', $plan->id, $plan->changeVersion, $plan->changeStage);
                }else{
                    $plan->reviewers = $this->review->getReviewer('projectplanyear', $plan->id, $plan->yearVersion, $plan->reviewStage);
                }
            } else {
                $plan->reviewers = $this->review->getMuiltNodeReviewers('projectplan', $plan->id, $plan->version, $plan->reviewStage);
            }
        }

        return $plan;
    }

    /**
     * Project: chengfangjinke
     * Method: buildSearchForm
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:21
     * Desc: This is the code comment. This method is called buildSearchForm.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $queryID
     * @param $actionURL
     */
    public function buildSearchForm($queryID, $actionURL)
    {
        $this->config->projectplan->search['actionURL'] = $actionURL;
        $this->config->projectplan->search['queryID'] = $queryID;
        $this->config->projectplan->search['params']['line']['values'] = array('' => '') + $this->loadModel('product')->getLinePairs();
        $this->config->projectplan->search['params']['app']['values'] = array('' => '') + $this->loadModel('application')->getapplicationNameCodePairs();
        $this->config->projectplan->search['params']['bearDept']['values'] = array('' => '') + $this->loadModel('dept')->getOptionMenu();
        $this->config->projectplan->search['params']['type']['values'] = array('' => '') + $this->lang->projectplan->typeList;

        $this->loadModel('search')->setSearchParams($this->config->projectplan->search);
    }

    public function processPlan($plans)
    {
        $this->loadModel('review');
        $creations = $this->dao->select('plan,id')->from(TABLE_PROJECTCREATION)->where('plan')->in(array_keys($plans))->fetchPairs();
        foreach ($plans as $planID => $plan) {
            if (in_array($plan->status, array('yearstart', 'yearwait', 'yearreviewing', 'yearpass', 'yearreject','start'))) {
                // 获取审批年度计划的人
                if($plan->changeStatus == 'pending'){
                    $plan->reviewers = $this->review->getReviewer('planchange', $planID, $plan->changeVersion, $plan->changeStage);
                }else{
                    $plan->reviewers = $this->review->getReviewer('projectplanyear', $planID, $plan->yearVersion, $plan->reviewStage);
                }
            } else {
                $plan->reviewers = $this->review->getMuiltNodeReviewers('projectplan', $planID, $plan->version, $plan->reviewStage);
            }

            $plan->creationID = isset($creations[$planID]) ? $creations[$planID] : 0;
        }
//        a($plans);
        return $plans;
    }

    /**
     * Project: chengfangjinke
     * Method: create
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:21
     * Desc: This is the code comment. This method is called create.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @return false
     */
    public function create()
    {
//        $products = $this->reformProductRelated();
        $stages = $this->reformPlanStage();
        if ($stages === false) return false;

        $projectplan = fixer::input('post')
            ->add('createdBy', $this->app->user->account)
            ->add('createdDate', helper::today())
            ->add('status', 'yearstart')
            ->add('insideStatus', 'no')
            ->join('line', ',')
            ->join('platformowner', ',')
            ->join('basis', ',')
//            ->join('bearDept', ',')
//            ->join('owner', ',')
            ->join('app', ',')
//            ->join('outsideProject', ',')
//            ->join('outsideSubProject', ',')
            ->join('outsideTask', ',')
//            ->add('productsRelated', base64_encode(json_encode($products)))
            ->add('planStages', base64_encode(json_encode($stages)))
//            ->remove('comment,productIds,realRelease,realOnline,stageBegin,stageEnd,uid,files,labels')
            ->remove('comment,stageBegin,stageEnd,uid,files,labels')
            ->stripTags($this->config->projectplan->editor->create['id'], $this->config->allowedTags)
            ->get();
        if ($this->checkPost() == false) {
            return false;
        }
        $projectplan->platformowner = trim($projectplan->platformowner,',');
        $projectplan->basis = trim($projectplan->basis,',');
        $projectplan->owner = trim($projectplan->owner,',');

        if (!isset($projectplan->outsideProject) || !$projectplan->outsideProject){
            $projectplan->outsideProject = '';
        }else{
            $projectplan->outsideProject = ',' . $projectplan->outsideProject . ',';
        }
        if (!isset($projectplan->outsideSubProject) || !$projectplan->outsideSubProject) {
            $projectplan->outsideSubProject = '';
        } else {
            $projectplan->outsideSubProject = ',' . $projectplan->outsideSubProject . ',';
        }
        if (!isset($projectplan->outsideTask)) {
            $projectplan->outsideTask = '';
        } else {
            $projectplan->outsideTask = ',' . $projectplan->outsideTask . ',';
        }

        $projectplan->planCode = '';

        if($projectplan->type !=1 && $projectplan->type !=2){ //非研发项 专项为空
            $projectplan->storyStatus       = '';
            $projectplan->dataEnterLake     = '';
            $projectplan->basicUpgrade      = '';
            $projectplan->systemAssemble    = '';
            $projectplan->cloudComputing    = '';
            $projectplan->passwordChange    = '';
            $projectplan->structure         = '';
            $projectplan->localize          = '';
        }

        $projectplan = $this->loadModel('file')->processImgURL($projectplan, $this->config->projectplan->editor->create['id'], $this->post->uid);
        $this->dao->insert(TABLE_PROJECTPLAN)
            ->data($projectplan)->autoCheck()
            ->batchCheck($this->config->projectplan->create->requiredFields, 'notempty')
            ->exec();
        $planID = $this->dao->lastInsertID();

        // 处理outsideplan表相关字段。
        if (isset($projectplan->outsideProject)) {
            $this->maintainOutside($planID, $projectplan->outsideProject);
            //$this->savePlanLinks($planID, 1);
        }

        $this->loadModel('file')->updateObjectID($this->post->uid, $planID, 'projectplan');
        $this->file->saveUpload('projectplan', $planID);

        if (!dao::isError()) return $planID;

        return false;
    }

    /**
     * Project: chengfangjinke
     * Method: update
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:21
     * Desc: This is the code comment. This method is called update.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $planID
     * @return array
     */
    public function update($planID)
    {
        //涉及产品
//        $products = $this->reformProductRelated();
        $stages = $this->reformPlanStage();
        if ($stages === false) return false;

        $oldPlan = $this->getByID($planID);
        if($oldPlan->status=="yearreviewing" && $oldPlan->reviewStage > 4){
            dao::$errors['editplan'] = "该状态下不允许编辑，或请刷新后重试！";
            return false;
        }
        $plan = fixer::input('post')
            ->join('line', ',')
            ->join('platformowner', ',')
            ->join('basis', ',')
//            ->join('bearDept', ',')
//            ->join('owner', ',')
            ->join('app', ',')
//            ->join('outsideProject', ',')
//            ->join('outsideSubProject', ',')
            ->join('outsideTask', ',')
//            ->add('productsRelated', base64_encode(json_encode($products)))
            ->add('planStages', base64_encode(json_encode($stages)))
//            ->remove('productIds,realRelease,realOnline,stageBegin,stageEnd,uid,files,labels')
            ->remove('stageBegin,stageEnd,uid,files,labels')
            ->stripTags($this->config->projectplan->editor->edit['id'], $this->config->allowedTags)
            ->get();
        $plan->platformowner = trim($plan->platformowner,',');
        $plan->basis = trim($plan->basis,',');
        $plan->owner = trim($plan->owner,',');
        $editmark = '';
        $email = [];
        if(isset($plan->editmark)){
            $editmark = $plan->editmark;
            unset($plan->editmark);
        }
        if(isset($plan->email)){
            $email = $plan->email;
            unset($plan->email);
        }
        if($oldPlan->status == "yearreviewing" && $oldPlan->reviewStage <= 4 && $oldPlan->reviewStage > 0) {
            if (!$email || !$editmark) {

                dao::$errors['editplan'] = "修改内容或通知人不能为空";
                return false;
            }
        }
        if ($this->checkPost() == false) {
            return false;
        }


        $comment = isset($plan->comment) ? $plan->comment : '';
        unset($plan->comment);
        if (!isset($plan->outsideProject) || !$plan->outsideProject) {
            $plan->outsideProject = '';
        } else {
            $plan->outsideProject = ',' . $plan->outsideProject . ',';
        }
        if (!isset($plan->outsideSubProject) || !$plan->outsideSubProject) {
            $plan->outsideSubProject = '';
        } else {
            $plan->outsideSubProject = ',' . $plan->outsideSubProject . ',';
        }
        if (!isset($plan->outsideTask)) {
            $plan->outsideTask = '';
        } else {
            $plan->outsideTask = ',' . $plan->outsideTask . ',';
        }

        /* 判断年度项目计划是否计划审批失败了，如果是，则修改状态为【计划待发起审批】。*/
        if ($oldPlan->status == 'yearreject') {
            $plan->status = 'yearstart';
        }


        if($plan->type !=1 && $plan->type !=2){ //非研发项 专项为空
            $plan->storyStatus       = '';
            $plan->dataEnterLake     = '';
            $plan->basicUpgrade      = '';
            $plan->systemAssemble    = '';
            $plan->cloudComputing    = '';
            $plan->passwordChange    = '';
            $plan->structure         = '';
            $plan->localize          = '';
        }

        $plan = $this->loadModel('file')->processImgURL($plan, $this->config->projectplan->editor->edit['id'], $this->post->uid);
        $this->dao->update(TABLE_PROJECTPLAN)->data($plan)->autoCheck()
            ->batchCheck($this->config->projectplan->edit->requiredFields, 'notempty')
            ->where('id')->eq($planID)
            ->exec();
        //处理outsideplan表相关字段
        if (isset($plan->outsideProject)) {
            $this->maintainOutside($planID, $plan->outsideProject);
        }
        $this->loadModel('file')->updateObjectID($this->post->uid, $planID, 'projectplan');
        $this->file->saveUpload('projectplan', $planID);

        // 同步修改项目立项书。
        $this->dao->update(TABLE_PROJECTCREATION)
            ->set('name')->eq($plan->name)
            ->set('type')->eq($plan->type)
            ->where('plan')->eq($planID)->exec();


        //comment
        if (isset($comment)) {
            $objectType = 'projectplan';
            $objectID = $planID;
            $this->loadModel('action')->create($objectType, $objectID, 'Commented', $comment);
        }

//        $commoncreatechange = common::createChanges($oldPlan, $plan);
        $commoncreatechange = common::createRuleChanges($oldPlan, $plan,[],$this->config->projectplan->changeFieldsRule);
        if($oldPlan->status == "yearreviewing" && $oldPlan->reviewStage <= 4 && $oldPlan->reviewStage > 0 && $commoncreatechange){
//            $email = ["t_jinzhuliang"];
            $planedit = new stdClass();
            $planedit->planID = $planID;
            $planedit->email = implode(',',$email);
            $planedit->vsersion = $oldPlan->yearVersion;
            $planedit->createtime = time();
            $planedit->editmark = $editmark;
            $this->dao->insert(TABLE_PROJECTPLANEDIT)->data($planedit)->exec();

            $this->loadModel('mail');
            $mailTitle = "年度计划：".$plan->name."  已修改，望知悉！";
            /* Get mail content. */
            $modulePath = $this->app->getModulePath($appName = '', 'projectplan');
            $oldcwd = getcwd();
            $viewFile = $modulePath . 'view/sendeditmail.html.php';
            chdir($modulePath . 'view');

            ob_start();
            include $viewFile;
            $mailContent = ob_get_contents();
            ob_end_clean();
            $mailres = $this->mail->send(implode(',',$email), $mailTitle, $mailContent,'',true);


        }
        return $commoncreatechange;
    }

    /**
     * Project: chengfangjinke
     * Method: execEdit
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:21
     * Desc: This is the code comment. This method is called execEdit.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $planID
     * @return array|false
     */
    public function execEdit($planID)
    {
        //涉及产品
//        $products = $this->reformProductRelated();
        $stages = $this->reformPlanStage();
        if ($stages === false) return false;

        $oldPlan = $this->getByID($planID);
        $plan = fixer::input('post')
            ->join('line', ',')
            ->join('platformowner', ',')
            ->join('basis', ',')
//            ->join('bearDept', ',')
//            ->join('owner', ',')
            ->join('app', ',')
//            ->join('outsideProject', ',')
//            ->join('outsideSubProject', ',')
            ->join('outsideTask', ',')
//            ->add('productsRelated', base64_encode(json_encode($products)))
            ->add('planStages', base64_encode(json_encode($stages)))
//            ->remove('projectChange,comment,productIds,realRelease,realOnline,stageBegin,stageEnd,uid,files,labels')
            ->remove('projectChange,comment,stageBegin,stageEnd,uid,files,labels')
            ->stripTags($this->config->projectplan->editor->execedit['id'], $this->config->allowedTags)
            ->get();
        $plan->platformowner = trim($plan->platformowner,',');
        $plan->basis = trim($plan->basis,',');
        $plan->owner = trim($plan->owner,',');

        /*$taskNmaeIsRepeat = $this->checkTaskNameSame($plan->outsideTask);
        if($taskNmaeIsRepeat){
            dao::$errors['outsideTask'] = $taskNmaeIsRepeat;
            return false;
        }*/
//a($plan);exit();
        //2022-4-27 『所属应用系统』不能为空
        if (empty($_POST['app'])) {
            dao::$errors['app'] = $this->lang->projectplan->appEmpty;
            return false;
        }
        if ($this->checkPost() == false) {
            return false;
        }
        if($plan->type !=1 && $plan->type !=2){ //非研发项 专项为空
            $plan->storyStatus       = '';
            $plan->dataEnterLake     = '';
            $plan->basicUpgrade      = '';
            $plan->systemAssemble    = '';
            $plan->cloudComputing    = '';
            $plan->passwordChange    = '';
            $plan->structure         = '';
            $plan->localize          = '';
        }

        if (!isset($plan->outsideProject) || !$plan->outsideProject) {
            $plan->outsideProject = '';
        } else {
            $plan->outsideProject = ',' . $plan->outsideProject . ',';
        }
        if (!isset($plan->outsideSubProject) || !$plan->outsideSubProject) {
            $plan->outsideSubProject = '';
        } else {
            $plan->outsideSubProject = ',' . $plan->outsideSubProject . ',';
        }
        if (!isset($plan->outsideTask)) {
            $plan->outsideTask = '';
        } else {
            $plan->outsideTask = ',' . $plan->outsideTask . ',';
        }



        $plan = $this->loadModel('file')->processImgURL($plan, $this->config->projectplan->editor->execedit['id'], $this->post->uid);
        if($oldPlan->type != $plan->type and $oldPlan->status != 'yearstart' and $oldPlan->status != 'yearreject' and $oldPlan->status != 'yearreviewing' and $oldPlan->status != '')
        {
            $plan->planCode = $this->getPlanCode($plan->type, $plan->year) ;
            $plan->oldPlanCode = ltrim($oldPlan->oldPlanCode.','. $oldPlan->planCode,',');
        }
        $this->dao->update(TABLE_PROJECTPLAN)->data($plan)->autoCheck()
            ->batchCheck($this->config->projectplan->execedit->requiredFields, 'notempty')
            ->where('id')->eq($planID)
            ->exec();

        $this->file->updateObjectID($this->post->uid, $planID, 'projectplan');


        // 同步修改项目立项书。
        $this->dao->update(TABLE_PROJECTCREATION)
            ->set('name')->eq($plan->name)
            ->set('type')->eq($plan->type)
            ->where('plan')->eq($planID)->exec();


        $this->maintainOutside($planID, $plan->outsideProject);


//        common::createChanges($oldPlan, $plan);
        $changeRes =  common::createRuleChanges($oldPlan, $plan,[],$this->config->projectplan->changeFieldsRule);
        return $changeRes;
    }


    public function checkTaskNameSame($taskID){

        $taskList = $this->dao->select("t1.id,t1.subTaskName,t1.outsideProjectPlanID,t2.subProjectName")->from(TABLE_OUTSIDEPLANTASKS)->alias('t1')
            ->leftjoin(TABLE_OUTSIDEPLANSUBPROJECTS)->alias('t2')->on("t1.subProjectID=t2.id")
            ->where('t1.id')->in($taskID)
            ->andWhere('t1.deleted')->eq(0)
            ->fetchAll('id');

        $tempArr = [];
        $repaetID = 0;
        $targetID = 0;
        foreach($taskList as $id=>$value){
            if(!in_array($value->subTaskName,$tempArr)){
                $tempArr[$id] = $value->subTaskName;
            }else{
                $repaetID = array_search($value->subTaskName,$tempArr);
                $targetID = $id;
                if($repaetID){
                    break;
                }
            }
        }

        if($repaetID){
//            $data = ['repeatID'=>$repaetID,'repeatName'=>$tempArr[$repaetID],'targetID'=>$targetID,'targetName'=>$taskList[$targetID]->subTaskName];
            $message = vsprintf($this->lang->projectplan->repateTaskNameErrot,[$tempArr[$repaetID],$repaetID,$taskList[$repaetID]->subProjectName,$tempArr[$repaetID]]);

        }else{
//            $data = [];
            $message = '';
        }
   
        return $message;
    }

    /**
     *年度计划审核通过，立项待发起审批时变更
     *
     * @param $planID
     * @return false|void
     */
    public function planChange($planID)
    {
        //涉及产品
//        $products = $this->reformProductRelated();
        $stages = $this->reformPlanStage();

        if ($stages === false) return false;

        $oldPlan = $this->getByID($planID);
        $plan = fixer::input('post')
            ->join('line', ',')
            ->join('platformowner', ',')
            ->join('basis', ',')
//            ->join('bearDept', ',')
//            ->join('owner', ',')
            ->join('app', ',')
//            ->join('outsideProject', ',')
//            ->join('outsideSubProject', ',')
            ->join('outsideTask', ',')
//            ->add('productsRelated', base64_encode(json_encode($products)))
            ->add('planStages', base64_encode(json_encode($stages)))
//            ->remove('projectChange,comment,productIds,realRelease,realOnline,stageBegin,stageEnd,uid,files,labels')
            ->remove('projectChange,comment,stageBegin,stageEnd,uid,files,labels')
            ->stripTags($this->config->projectplan->editor->execedit['id'], $this->config->allowedTags)
            ->get();


        //2022-4-27 『所属应用系统』不能为空
        if (empty($_POST['app'])) {
            dao::$errors['app'] = $this->lang->projectplan->appEmpty;
            return false;
        }

        //变更内容介绍，用于审批页面展示
        if (empty($_POST['planRemark'])) {
            dao::$errors['planRemark'] = $this->lang->projectplan->planRemarkDescNotEmpty;
            return false;
        }
        if ($this->checkPost() == false) {
            return false;
        }
        if (!isset($plan->outsideProject) || !$plan->outsideProject) {
            $plan->outsideProject = '';
        } else {
            $plan->outsideProject = ',' . $plan->outsideProject . ',';
        }
        if (!isset($plan->outsideSubProject) || !$plan->outsideSubProject) {
            $plan->outsideSubProject = '';
        } else {
            $plan->outsideSubProject = ',' . $plan->outsideSubProject . ',';
        }
        if (!isset($plan->outsideTask)) {
            $plan->outsideTask = '';
        } else {
            $plan->outsideTask = ',' . $plan->outsideTask . ',';
        }

        $isChangeReview = true;

        $plan = $this->loadModel('file')->processImgURL($plan, $this->config->projectplan->editor->execedit['id'], $this->post->uid);
        $myDept = $this->loadModel('dept')->getByID($this->app->user->dept);
//        $change = common::createChanges($oldPlan, $plan);
//        $change = common::createNewChanges($oldPlan, $plan);
        $change = common::createRuleChanges($oldPlan, $plan,[],$this->config->projectplan->changeFieldsRule);

        //不需要审批的字段修改
        $noreviewField = ['name','owner','type','category','phone','workload','app','basis','line','planRemark','workloadBase','workloadChengdu','nextYearWorkloadBase','nextYearWorkloadChengdu','platformowner'];


        $changeField = array_column($change,'field');
        $diff = array_diff($changeField,$noreviewField);
        //如果没有差集，则不需要审核
        if(!$diff){
            if((($oldPlan->type != $plan->type) && (in_array($oldPlan->type,[1,2]) && in_array($plan->type,[1,2]) )) || $oldPlan->type == $plan->type){
                $isChangeReview = false;
            }

        }

        //去除只有 “变更内容” 一个字段修改的情况。需要参与审核
        if((count($changeField) == 1 && in_array('planRemark',$changeField))){
            $isChangeReview = true;
        }
        /*var_dump($isChangeReview);
        a($diff);
        a($changeField);
        a($change);
        exit();*/
        $new = [];
        $old = [];
        foreach ($change as $value){
            $field = $value['field'];
            $new[$field] = $value['new'];
            $old[$field] = $value['old'];
        }
        $addNodeStage = 1;
        $planPerson = $this->isBuildDeptPerson();
        if($planPerson){
            $panchangeNode = 2;
        }else{
            $panchangeNode = 1;
        }



        if(!empty($change)){
            $number = $this->dao->select('count(id) c')->from(TABLE_PROJECTPLANCHANGE)->where('planID')->eq($planID)->andWhere('status')->eq('pending')->fetch('c');
            if($number == 0){
                //通过或不通过再发起审批
                $changeVersion = $oldPlan->changeVersion;
                if($oldPlan->changeStatus !== 'no'){
                    $changeVersion = ($oldPlan->changeVersion)+1;
                }
                $this->dao->begin(); //调试完逻辑最后开启事务
                //需要审核
                if($isChangeReview){
                    $this->dao->update(TABLE_PROJECTPLAN)->set('changeStatus')->eq('pending')->set('changeStage')->eq($panchangeNode)->set('changeVersion')->eq($changeVersion)->set('changeReview')->eq(1)->set('submitedBy')->eq($this->app->user->account)->where('id')->eq($planID)->exec();
                    $insertData = [];
                    $insertData['planID']      = $planID;
                    $insertData['version']     = $changeVersion;
                    $insertData['new']         = json_encode($new);
                    $insertData['old']         = json_encode($old);
                    $insertData['content']     = json_encode($plan);
                    $insertData['planRemark']  = $plan->planRemark;
                    $insertData['createdBy']   = $this->app->user->account;
                    $insertData['createdDate'] = helper::today();
                    $this->dao->insert(TABLE_PROJECTPLANCHANGE)->data($insertData)->autoCheck()->exec();
                    $planPerson = $this->isBuildDeptPerson();
                    //是架构部
                    if($planPerson){
                        $this->newAddNodeInfoAboutBuild($planID,$changeVersion,array($myDept->manager1),$addNodeStage,$addNodeStage,'planchange');
                    }else{
                        $param = ['nodeCode'=>$this->lang->projectplan->changeNodeCode[1]];//部门负责人审核
                        $this->loadModel('review')->addNode('planchange', $planID, $changeVersion, array($myDept->manager1), true, 'pending',1,$param);
                    }
                }else{
                    $this->dao->update(TABLE_PROJECTPLAN)->set('changeStatus')->eq('pending')->set('changeStage')->eq($panchangeNode)->set('changeVersion')->eq($changeVersion)->set('changeReview')->eq(2)->set('submitedBy')->eq($this->app->user->account)->where('id')->eq($planID)->exec();
                    $insertData = [];
                    $insertData['planID']      = $planID;
                    $insertData['version']     = $changeVersion;
                    $insertData['new']         = json_encode($new);
                    $insertData['old']         = json_encode($old);
                    $insertData['content']     = json_encode($plan);
                    $insertData['planRemark']  = $plan->planRemark;
                    $insertData['createdBy']   = $this->app->user->account;
                    $insertData['createdDate'] = helper::today();
                    $insertData['isreview'] = 2;
                    $this->dao->insert(TABLE_PROJECTPLANCHANGE)->data($insertData)->autoCheck()->exec();
                    //更新年度计划信息和立项书，外部年度计划绑定
//                    $this->updatePlan($planID,$changeVersion,'');
                    $this->passDeal($planID,$changeVersion,'');

                }



                $this->dao->commit();
//                $this->dao->rollback();
            }
        }
        return $change;
    }

    /**
     * 获取变更内容
     * @param $planID
     * @return mixed
     */
    public function getChangePlanInfo($planID,$version=0)
    {
        return $this->getChangeList($planID,$version,'pending');
    }

    /**
     * 获取变更数据源
     * @param $planID
     * @param int $version
     * @return mixed
     */
    public function getChangeList($planID,$version=0,$status='pending')
    {
        return $this->dao->select('id,createdBy,content,old,new,planRemark')
            ->from(TABLE_PROJECTPLANCHANGE)
            ->where('planID')->eq($planID)
            ->andWhere('status')->eq($status)
            ->andWhere('version')->eq($version)
            ->andWhere('deleted')->eq(0)
            ->fetch();
    }
    /**
     * 获取变更数据源

     * @return mixed
     */
    public function getChangeListByStatus($status='pending')
    {
        return $this->dao->select('id,createdBy,content,old,new,planRemark')
            ->from(TABLE_PROJECTPLANCHANGE)
            ->where('status')->eq($status)
            ->andWhere('deleted')->eq(0)
            ->orderBy("id_desc")
            ->fetchAll();
    }

    /**
     * Project: chengfangjinke
     * Method: submit
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:21
     * Desc: This is the code comment. This method is called submit.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $planID
     */
    public function submit($planID)
    {
        try {
            if (empty($_POST['depts'])) {
                dao::$errors[] = $this->lang->projectplan->deptEmpty;
                return;
            }
            $this->dao->begin();
            $myDept = $this->loadModel('dept')->getByID($this->app->user->dept);
            if(!isset($myDept->manager1) or empty($myDept->manager1))
            {
                dao::$errors[] = $this->lang->projectplan->managerEmpty;
                $this->dao->rollBack();
                return;
            }
            if (!isset($myDept->leader1) or empty($myDept->leader1)) {
                dao::$errors[] = $this->lang->projectplan->leaderEmpty;
                $this->dao->rollBack();
                return;
            }

            $planCreation = $this->getCreationByID($planID);
            if(!$planCreation){
                dao::$errors[] = $this->lang->projectplan->planCreationErrorEmpty;
                $this->dao->rollBack();
                return;
            }

            // 各会签部门负责人
            $reviewers = array();
            foreach ($this->post->depts as $deptID) {
                $dept = $this->dept->getByID($deptID);
                if (empty($dept->manager1)) {
                    dao::$errors[] = $dept->name . $this->lang->projectplan->managerEmpty;
                    $this->dao->rollBack();
                    return;
                }
                $reviewers[] = $dept->manager1;
            }
            $productDept = $this->dept->getByID(1);
            if (empty($productDept->manager1)) {
                dao::$errors[] = $productDept->name . $this->lang->projectplan->managerEmpty;
                $this->dao->rollBack();
                return;
            }
            $archDept = $this->dept->getByID(2);
            if (empty($archDept->manager1)) {
                dao::$errors[] = $archDept->name . $this->lang->projectplan->managerEmpty;
                $this->dao->rollBack();
                return;
            }

            $version = $this->dao->select('version')->from(TABLE_PROJECTPLAN)->where('id')->eq($planID)->fetch('version');
            $plan = fixer::input('post')
                ->add('status', 'reviewing')
                ->add('reviewStage', 0)
                ->add('version', $version + 1)
                ->add('submitedBy', $this->app->user->account)
                ->join('depts', ',')
                ->join('opinion', ',')
//            ->join('demand', ',')
                ->join('requirement', ',')
                ->remove('uid,issyncjob')
                ->get();

            if(!isset($_POST['issyncjob'])){
                dao::$errors[] = "请选择是否同步需求任务按钮";

                $this->dao->rollBack();
                return;
            }


            $issyncjob = $_POST['issyncjob'];

            // 创建项目
            $planinfo = $this->getByID($planID);

            //是否是内部项目
            $isInerProject = false;
            $ownerbasis = explode(',',$planinfo->basis);
            if(in_array(6,$ownerbasis)){
                $isInerProject = true;
            }



            $project = new stdClass();

            if($issyncjob != 0 ){
                if(!isset($plan->opinion) || !isset($plan->requirement)){
                    dao::$errors[] = $this->lang->projectplan->isSyncJob;
                    $this->dao->rollBack();
                    return false;
                }
                if(!$plan->opinion || !$plan->requirement){
                    dao::$errors[] = $this->lang->projectplan->isSyncJob;
                    $this->dao->rollBack();
                    return false;
                }


            }else{
                $plan->opinion = '';
                $plan->requirement = '';
            }

            $plan->opinion && $plan->opinion=$project->opinions = ',' . $plan->opinion . ',';
//        $plan->demand && $project->demands = ',' . $plan->demand . ',';
            $plan->requirement && $plan->requirement = $project->requirements = ',' . $plan->requirement . ',';


           /* //关联生成计划
            $project->name           = $planinfo->name;
            $project->status         = 'wait';
            $project->model          = 'waterfall';
            $project->begin          = $planinfo->begin;
            $project->end            = $planinfo->end;
            $project->days           = 0;
            $project->code           = $planinfo->mark;
            $project->openedBy       = $this->app->user->account;
            $project->openedDate     = helper::now();
            $project->lastEditedBy   = $this->app->user->account;
            $project->lastEditedDate = helper::now();
            $project->type           = 'project';
            $project->acl            = 'private'; //修改立项后 默认私有
            $project->PM             = $planinfo->creation->PM; // 修复年度计划表中没有项目经理问题
            */
            /**
             * 2022-09-06 songdi 添加人月工时计算参数
             * 自定义配置项，根据生效日期取参数计算
             */
            /*$this->app->loadLang("project");
            $fieldList = zget($this->lang->project, "workHours", '');
            $today = date("Y-m-d");
            $workHours = "22";
            if ($fieldList['effectiveDate'] != '' && $today >= $fieldList['effectiveDate'] && $workHours != ''){
                $workHours = $fieldList['workHours'];
            }
            $project->workHours = $workHours;
            //计划存在则更新
            if($planinfo->project){

                $this->dao->update(TABLE_PROJECT)->data($project)->where("id")->eq($planinfo->project)->exec();

                $projectID = $planinfo->project;
            }else{
                //否则插入
                if($issyncjob == 0){
                    $this->dao->insert(TABLE_PROJECT)->data($project)->exec();
                    $projectID = $this->dao->lastInsertID();
                }else{
                    $projectID = 0;
                }

            }*/
            //如果是内部项目 补齐会签部门
            /*if($isInerProject){
                if($this->app->user->dept == 1){
                    $plan->depts.=',2';
                }else if($this->app->user->dept == 2){
                    $plan->depts.=',1';
                }else{
                    $plan->depts.=',1,2';
                }
            }*/

            $plan->lastDealDate = date('Y-m-d');
//            $plan->project = $projectID;
            $this->dao->update(TABLE_PROJECTPLAN)->data($plan)->autoCheck()
                ->where('id')->eq($planID)->exec();

            $plan->bearDept =   $planinfo->bearDept;
            //获取是否上海
            $isShangHai = $this->isShangHai($planID);
            $shProductAndarchList =  $this->lang->projectplan->shProductAndarchList ; //上海产创和架构自定义配置

            if (!dao::isError()) {
                /**
                 * 内部项目
                 * 部门负责人是 产创/架构部 则 审核+分配资源 会签节点去掉该部门
                 * 部门负责人 不是产创/架构部 审核+分配资源 会签节点 有产创/架构
                 * 外部项目
                 * 部门负责人是 产创/架构部 则 部门负责人 审核+分配资源，同时生成产创或架构之一的审核+分配资源 预审节点，同时会签部门无 产创/架构
                 * 部门负责人不是 产创/架构部 则 部门负责人 审核+分配资源，同时生成 产创/架构部 的预审节点，同时会签部门无产创/架构
                 * 如果 同时生成 产创/架构部 的预审节点 则需要并行审批
                 */
                //看看是不是 产创/架构的
                $isFilterDept = in_array($this->app->user->dept,$this->lang->projectplan->submitFilterDept);

                //总经理
                $ceo = $this->loadModel('user')->getCeoUsers($plan->bearDept);
                $reviewerceo = array_shift($ceo);
                //内部项目
                if($isInerProject){
                    if($planCreation->workload >= 100){
                        $stage = 1;
                        // 部门负责人
                        $this->loadModel('review')->addNode('projectplan', $planID, $plan->version, array($myDept->manager1), true, 'pending',$stage++,['nodeCode'=>'deptLeader']);
                        //总经理
                        //$reviewerceo = $this->dao->select('account')->from(TABLE_USER)->where('role')->eq('ceo')->fetch('account');
                        $leader1status = 'wait';
                        $leader1nodeCode = ['nodeCode'=>'chargeLeader'];
                        if($myDept->leader1 == $reviewerceo){
                            $leader1nodeCode['reviewerExtParams'] = [
                                'status'=>'ignore',
                                'comment'=>$this->lang->projectplan->submitignoreleader,

                            ];
                            $leader1status = 'ignore';
                        }
                        // 部门分管领导
                        $this->review->addNode('projectplan', $planID, $plan->version, array($myDept->leader1), true,$leader1status,$stage++,$leader1nodeCode);
                        //会签产创架构 start
                        if($this->app->user->dept == 1){
                            $reviewers[] = $archDept->manager1;
                        }else if($this->app->user->dept == 2){
                            //如果是架构部 则 生成产创部预审节点
                            $reviewers[] = $productDept->manager1;
                        }else{
                            $reviewers[] = $productDept->manager1;
                            $reviewers[] = $archDept->manager1;
                        }
                        //会签产创架构 end
                        //会签人员
                        $this->review->addNode('projectplan', $planID, $plan->version, $reviewers, true,'wait',$stage++,['nodeCode'=>'deptsSign']);


                        $this->review->addNode('projectplan', $planID, $plan->version, array($reviewerceo), true, 'wait', $stage++, ['nodeCode' => 'gm']);

                    }else{
                        $stage = 1;
                        // 部门负责人
                        $this->loadModel('review')->addNode('projectplan', $planID, $plan->version, array($myDept->manager1), true, 'pending',$stage++,['nodeCode'=>'deptLeader']);
                        //会签产创架构 start
                        if($this->app->user->dept == 1){
                            $reviewers[] = $archDept->manager1;
                        }else if($this->app->user->dept == 2){
                            //如果是架构部 则 生成产创部预审节点
                            $reviewers[] = $productDept->manager1;
                        }else{
                            $reviewers[] = $productDept->manager1;
                            $reviewers[] = $archDept->manager1;
                        }
                        //会签产创架构 end
                        //会签人员
                        $this->review->addNode('projectplan', $planID, $plan->version, $reviewers, true,'wait',$stage++,['nodeCode'=>'deptsSign']);

                        // 部门分管领导
                        $this->review->addNode('projectplan', $planID, $plan->version, array($myDept->leader1), true,'wait',$stage++,['nodeCode'=>'chargeLeader']);
                    }
                }else{
                    //外部项目
                    if($planCreation->workload >= 100){
                        $stage = 1;
                        // 部门负责人
                        $this->loadModel('review')->addNode('projectplan', $planID, $plan->version, array($myDept->manager1), true, 'pending',$stage++,['nodeCode'=>'deptLeader']);
                        //预审节点 start

                        if($this->app->user->dept == 1){
                            // 产创部 自动跳过
                            $this->review->addNode('projectplan', $planID, $plan->version, array($productDept->manager1), true,'ignore',$stage++,['nodeCode'=>'productdeptleader','reviewerExtParams'=>['status'=>'ignore','comment'=>$this->lang->projectplan->submitignoreprojectbearnotice]]);
                            //如果 是 产创部 则生成 架构部预审节点
                            $this->review->addNode('projectplan', $planID, $plan->version, array($archDept->manager1), true,'wait',$stage++,['nodeCode'=>'archdeptleader']);

                        }else if($this->app->user->dept == 2){
                            //如果是架构部 则 生成产创部预审节点
                            $this->review->addNode('projectplan', $planID, $plan->version, array($productDept->manager1), true,'wait',$stage++,['nodeCode'=>'productdeptleader']);

                            $this->review->addNode('projectplan', $planID, $plan->version, array($archDept->manager1), true,'ignore',$stage++,['nodeCode'=>'archdeptleader','reviewerExtParams'=>['status'=>'ignore','comment'=>$this->lang->projectplan->submitignoreprojectbearnotice]]);
                        }else{
                            $this->review->addNode('projectplan', $planID, $plan->version, $isShangHai ? array($shProductAndarchList['productDeptUser']) : array($productDept->manager1), true,'wait',$stage++,['nodeCode'=>'productdeptleader']);
                            $this->review->addNode('projectplan', $planID, $plan->version, $isShangHai ? array($shProductAndarchList['archDeptUser']) : array($archDept->manager1), true,'wait',$stage++,['nodeCode'=>'archdeptleader']);
                        }
                        //预审节点 end
                        //总经理
                        //$reviewerceo = $this->dao->select('account')->from(TABLE_USER)->where('role')->eq('ceo')->fetch('account');
                        $leader1status = 'wait';
                        $leader1nodeCode = ['nodeCode'=>'chargeLeader'];
                        if($myDept->leader1 == $reviewerceo){
                            $leader1nodeCode['reviewerExtParams'] = [
                                'status'=>'ignore',
                                'comment'=>$this->lang->projectplan->submitignoreleader,

                            ];
                            $leader1status = 'ignore';
                        }
                        // 部门分管领导
                        $this->review->addNode('projectplan', $planID, $plan->version, array($myDept->leader1), true,$leader1status,$stage++,$leader1nodeCode);
                        //会签人员
                        $this->review->addNode('projectplan', $planID, $plan->version, $reviewers, true,'wait',$stage++,['nodeCode'=>'deptsSign']);

                        $this->review->addNode('projectplan', $planID, $plan->version, array($reviewerceo), true, 'wait', $stage++, ['nodeCode' => 'gm']);

                    }else{
                        $stage = 1;
                        // 部门负责人
                        $this->loadModel('review')->addNode('projectplan', $planID, $plan->version, array($myDept->manager1), true, 'pending',$stage++,['nodeCode'=>'deptLeader']);
                        //预审节点 start

                        if($this->app->user->dept == 1){
                            $this->review->addNode('projectplan', $planID, $plan->version, array($productDept->manager1), true,'ignore',$stage++,['nodeCode'=>'productdeptleader','reviewerExtParams'=>['status'=>'ignore','comment'=>$this->lang->projectplan->submitignoreprojectbearnotice]]);
                            //如果 是 产创部 则生成 架构部预审节点
                            $this->review->addNode('projectplan', $planID, $plan->version, array($archDept->manager1), true,'wait',$stage++,['nodeCode'=>'archdeptleader']);
                        }else if($this->app->user->dept == 2){
                            //如果是架构部 则 生成产创部预审节点
                            $this->review->addNode('projectplan', $planID, $plan->version, array($productDept->manager1), true,'wait',$stage++,['nodeCode'=>'productdeptleader']);
                            $this->review->addNode('projectplan', $planID, $plan->version, array($archDept->manager1), true,'ignore',$stage++,['nodeCode'=>'archdeptleader','reviewerExtParams'=>['status'=>'ignore','comment'=>$this->lang->projectplan->submitignoreprojectbearnotice]]);
                        }else {
                            $this->review->addNode('projectplan', $planID, $plan->version, $isShangHai ? array($shProductAndarchList['productDeptUser']) : array($productDept->manager1), true, 'wait', $stage++, ['nodeCode' => 'productdeptleader']);
                            $this->review->addNode('projectplan', $planID, $plan->version, $isShangHai ? array($shProductAndarchList['archDeptUser']) : array($archDept->manager1), true, 'wait', $stage++, ['nodeCode' => 'archdeptleader']);
                        }
                        //预审节点 end
                        //会签人员
                        $this->review->addNode('projectplan', $planID, $plan->version, $reviewers, true,'wait',$stage++,['nodeCode'=>'deptsSign']);

                        // 部门分管领导
                        $this->review->addNode('projectplan', $planID, $plan->version, array($myDept->leader1), true,'wait',$stage++,['nodeCode'=>'chargeLeader']);
                    }
                }



                $this->dao->commit();
                return true;
            }else{
                $this->dao->rollBack();

                return false;
            }
        }catch (Error $e){
            $this->dao->rollBack();
            dao::$errors[] = $e->getMessage();

            return false;
        }




    }

    public function getReviewNodeCode($status){
        $nodeCode = zget($this->lang->change->reviewStatusNodeCodeMapList, $status);
        return $nodeCode;
    }
    public function yearReview($planID)
    {
        $myDept = $this->loadModel('dept')->getByID($this->app->user->dept);
        if (!isset($myDept->manager1) or empty($myDept->manager1)) {
            dao::$errors[] = $this->lang->projectplan->managerEmpty;
            return;
        }
        $projectPlanInfo = $this->dao->select('id,reviewStage,yearVersion,rejectStatus,status,bearDept')->from(TABLE_PROJECTPLAN)->where('id')->eq($planID)->fetch();
        $version = $projectPlanInfo->yearVersion;
        $status = $projectPlanInfo->status;
        $rejectStatus = $projectPlanInfo->rejectStatus;
        $reviewStage = $projectPlanInfo->reviewStage;

        $plan = fixer::input('post')
            ->add('status', 'yearreviewing')
            ->add('submitedBy', $this->app->user->account)
            ->remove('uid,isNeedDeptLeader')
            ->get();
        $addNodeStage = 1;
        if (!dao::isError()) {
            if($status == 'yearstart' && $rejectStatus > 0){
                //4,5.6节点退回，2节点必须审核
                $plan->yearVersion = $version+1;
                $planPerson = $this->isBuildDeptPerson();
                if(in_array($rejectStatus,$this->lang->projectplan->leaderStage)){
                    //平台架构部跳过负责人
                    if(!empty($planPerson)){
                        $plan->reviewStage = 4;
                        $this->addNodeInfoLeaders($planID,$plan->yearVersion,array($myDept->manager1),$plan->reviewStage,$plan->reviewStage);
                    }else{
                        $plan->reviewStage = 1;
                        $this->addNodeInfo($planID,$plan->yearVersion,array($myDept->manager1),$addNodeStage,$addNodeStage);
                    }
                }elseif(in_array($rejectStatus,$this->lang->projectplan->jumpStage)){
                    //2,3节点退回，重新申请选择是否跳过
                    if ($this->post->isNeedDeptLeader == 'yes') {
                        $reviewStage = 1;
                        $reviewer = array($myDept->manager1);
                    } elseif ($this->post->isNeedDeptLeader == 'no') {
                        $myDept = $this->loadModel('dept')->getByID($projectPlanInfo->bearDept); //架构部 由固定架构部配置的接口人员改为各部门配置的接口人员
                        $reviewStage = 2;
                        $reviewer = array($myDept->planPerson);
                    }
                    if(!empty($planPerson)){
                        $plan->reviewStage = 2;
                        $this->addNodeInfoAboutBuild($planID,$plan->yearVersion,array($myDept->manager1),$addNodeStage,$addNodeStage);
                    }else{
                        $plan->reviewStage = $reviewStage;
                        $this->addNodeInfo($planID,$plan->yearVersion,$reviewer,$reviewStage,$reviewStage);
                    }
                }else{
                    // 1,2节点退回
                    $plan->reviewStage = 1;
                    $this->addNodeInfo($planID,$plan->yearVersion,array($myDept->manager1),$addNodeStage,$addNodeStage);
                }
            }else{
                //发起人是架构部人员，默认负责人节点跳过
                $planPerson = $this->isBuildDeptPerson();
                if(!empty($planPerson)){
                    $plan->reviewStage = 2;
                    $this->addNodeInfoAboutBuild($planID,$version,array($myDept->manager1),$addNodeStage,$addNodeStage);
                }else{
                    // 首次审批直接到部门负责人 没有退回
                    $plan->reviewStage = 1;
                    $this->addNodeInfo($planID,$version,array($myDept->manager1),$addNodeStage,$addNodeStage);
                }
            }
            $plan->mailto = !empty($plan->mailto) ? implode($plan->mailto, ',') : '';
            $plan->lastDealDate = date('Y-m-d');
            $this->dao->update(TABLE_PROJECTPLAN)->data($plan)->autoCheck()->where('id')->eq($planID)->exec();
        }
    }

    /**
     * 判断是否架构部发起审批
     * @return string
     */
    public function isBuildDeptPerson()
    {
        $userList = $this->loadModel('user')->getUserListByDeptId(2,'account');
        //架构部发起审批跳过负责人
        $deptOfBuild = [];
        foreach ($userList as $item) {
            $deptOfBuild[] = $item->account;
        }
        $planPerson = '';
        if(in_array($this->app->user->account,$deptOfBuild)){
            $deptInfo = $this->loadModel('dept')->getByID(2);
            $planPerson = $deptInfo->planPerson;
        }
        return $planPerson;
    }
    /**
     * 增加节点数据公共方法
     *
     * @param $planID projectplanId
     * @param $yearVersion 版本号
     */
    public function addNodeInfo($planID,$version,$reviewer,$reviewStage,$nodeCode)
    {
        $nodeCodeLang = $this->lang->projectplan->nodeCode;
        $param = array('nodeCode'=>$nodeCodeLang[$nodeCode]);
        $this->loadModel('review')->addNode('projectplanyear', $planID, $version, $reviewer, true, 'pending',$reviewStage,$param);
    }


    /**
     * 跳过架构部负责人数据增加
     *
     * @param $planID projectplanId
     * @param $yearVersion 版本号
     */
    public function addNodeInfoAboutBuild($planID,$version,$reviewer,$reviewStage,$nodeCode)
    {
        $nodeCodeLang = $this->lang->projectplan->nodeCode;
        $param1 = array('nodeCode'=>$nodeCodeLang[$nodeCode]);
        $this->loadModel('review')->addNode('projectplanyear', $planID, $version, $reviewer, true, 'ignore',$reviewStage,$param1);

        $param2 = array('nodeCode'=>$nodeCodeLang[$nodeCode+1]);
        $plan = $this->getByID($planID);
        $myDept = $this->loadModel('dept')->getByID($plan->bearDept); //架构部 由固定架构部配置的接口人员改为各部门配置的接口人员
        $this->loadModel('review')->addNode('projectplanyear', $planID, $version, array($myDept->planPerson), true, 'pending',$reviewStage+1,$param2);
    }

    /**
     * 跳过架构部负责人数据增加
     *
     * @param $planID projectplanId
     * @param $yearVersion 版本号
     */
    public function newAddNodeInfoAboutBuild($planID,$version,$reviewer,$reviewStage,$nodeCode,$nodestr)
    {
        $nodeCodeLang = $this->lang->projectplan->nodeCode;
        $param1 = array('nodeCode'=>$nodeCodeLang[$nodeCode]);
        $this->loadModel('review')->addNode($nodestr, $planID, $version, $reviewer, true, 'ignore',$reviewStage,$param1);

        $param2 = array('nodeCode'=>$nodeCodeLang[$nodeCode+1]);
        $plan = $this->getByID($planID);
        $myDept = $this->loadModel('dept')->getByID($plan->bearDept); //架构部 由固定架构部配置的接口人员改为各部门配置的接口人员
        $this->loadModel('review')->addNode($nodestr, $planID, $version, array($myDept->planPerson), true, 'pending',$reviewStage+1,$param2);
    }
    /**
     * 架构部负责人，技术总监，总经理跳过，并且是架构部发起审批
     *
     * @param $planID projectplanId
     * @param $yearVersion 版本号
     */
    public function addNodeInfoLeaders($planID,$version,$reviewer,$reviewStage,$nodeCode)
    {
        $nodeCodeLang = $this->lang->projectplan->nodeCode;
        $param1 = array('nodeCode'=>$nodeCodeLang[1]);
        $this->loadModel('review')->addNode('projectplanyear', $planID, $version, $reviewer, true, 'ignore',1,$param1);

        $param2 = array('nodeCode'=>$nodeCodeLang[$nodeCode]);
        $myDept = $this->loadModel('dept')->getByID(2);
        $this->loadModel('review')->addNode('projectplanyear', $planID, $version, array($myDept->manager1), true, 'pending',$reviewStage,$param2);
    }


    /**
     * 不通过后重新发起申请的节点数据
     *
     * @param $planID projectplanId
     * @param $yearVersion 版本号
     */
    public function dealRejectStageData($planID,$yearVersion,$nodeCode)
    {
        $nodeInfo = $this->dao->select('id,status')->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq('projectplanyear')
            ->andWhere('objectID')->eq($planID)
            ->andWhere('status')->eq('reject')
            ->andWhere('version')->eq($yearVersion)
            ->andWhere('nodeCode')->eq($nodeCode)
            ->fetch();

        $reviewerInfo = $this->dao->select('id,reviewer,node,status')->from(TABLE_REVIEWER)->where('node')->eq($nodeInfo->id)->fetchAll();
        //构造reviewnode节点数据
        $reviewer = [];
        foreach ($reviewerInfo as $key => $value){
            $reviewer[$key]  = $value->reviewer;
        }
        return $reviewer;

    }

    /**
     * Project: chengfangjinke
     * Method: review
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:21
     * Desc: This is the code comment. This method is called review.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $planID
     * @return array|false
     */
    public function review($planID)
    {
        if (!$this->post->result) {
            dao::$errors['result'] = $this->lang->projectplan->resultEmpty;
            return false;
        }
        if ($this->post->result == 'reject' && !$this->post->comment) {
            dao::$errors['comment'] = $this->lang->projectplan->commentEmpty;
            return false;
        }

        $plan = $this->getByID($planID);
        //防止在地盘待处理和年度计划页面重复审批
        $repeatVerify = $this->selectActionInfo($planID,$plan->version,'projectplan');
        if(!$repeatVerify){
            dao::$errors[] = $this->lang->projectplan->approvalEmpty;
            return false;
        }
        $extra = new stdClass();
        //会签节点 分配资源
        $extra->involved = $this->post->involved;

//        $curPendingNode = $this->review->getFirstPendingNode('projectplan', $planID, $plan->version);
        $curPendingNode = $this->loadModel("review")->getReviewByAccount('projectplan', $planID,$this->app->user->account, $plan->version);
        if(!$curPendingNode){
            dao::$errors[] = $this->lang->projectplan->submitnodeCodeEmptyError;
            return false;
        }

        //如果是通过状态 并且有资源分配的节点 验证资源是否勾选
        if ($this->post->result== 'pass' and in_array($curPendingNode->nodeCode, $this->lang->projectplan->reviewinvolvedNode) and !$extra->involved) {
            dao::$errors[] = $this->lang->projectplan->involvedEnpty;
            return false;
        }
        //如果是不通过状态 时 资源分配表单隐藏，如果此时选择了资源，则清除所选的资源分配，防止隐藏的表单值入库
        if ($this->post->result== 'reject' and in_array($curPendingNode->nodeCode, $this->lang->projectplan->reviewinvolvedNode) and $extra->involved) {
            $extra->involved = false;
        }

        //如果是 部门负责人 则 修改下 立项书的项目经理和年度计划的责任人
        $this->dao->begin();
        $appendComment = '';
        try{
            if($curPendingNode->nodeCode == 'deptLeader'){
                if(!$this->post->owner){
                    dao::$errors[] = $this->lang->projectplan->ownerEmpty ;
                    $this->dao->rollBack();
                    return false;
                }
                //更新
                if($plan->owner != $this->post->owner){
                    $this->dao->update(TABLE_PROJECTPLAN)->data(['owner'=>$this->post->owner])->where("id")->eq($planID)->exec();
                    $this->dao->update(TABLE_PROJECTCREATION)->data(['PM'=>$this->post->owner])->where("plan")->eq($planID)->exec();
                    $appendComment = " 项目经理 由：".$plan->owner.'变更为：'.$this->post->owner;
                }

            }

            $result = $this->loadModel('review')->check('projectplan', $planID, $plan->version, $this->post->result, $this->post->comment, $curPendingNode->stage, $extra,true,$curPendingNode->id);

            $mailsend = false;
            if ($result == 'pass') {
                $this->dao->update(TABLE_PROJECTPLAN)->set('reviewStage = reviewStage+1')->where('id')->eq($planID)->exec();
                //如果审核了，还有 正在处理中的节点，则不增加新的审核中节点
                $pendingNode = $this->dao->select('id,nodeCode,status')->from(TABLE_REVIEWNODE)->where('objectType')->eq('projectplan')
                    ->andWhere('objectID')->eq($planID)
                    ->andWhere('version')->eq($plan->version)
                    ->andWhere('status')->eq('pending')->orderBy('stage,id')->fetch();
                //如果 上一节点时部门负责人  下一节点 则 需要将 产创和架构的预审节点同步置为 pending ，但产创和架构并不一定存在
                if(!$pendingNode){
                    if($curPendingNode->nodeCode == 'deptLeader'){
                        //获取 产创和架构的预审节点。如果存在 则 修改为待审核状态
                        $productdeptleaderNode = $this->loadModel('review')->getNodeByNodeCode('projectplan', $planID, $plan->version, $this->lang->projectplan->submitnodeCode['productdeptleader']);
                        $archdeptleaderNode = $this->loadModel('review')->getNodeByNodeCode('projectplan', $planID, $plan->version, $this->lang->projectplan->submitnodeCode['archdeptleader']);
                        if($productdeptleaderNode){
                            $this->loadModel('review')->setReviewNodePending($productdeptleaderNode->id);
                        }
                        if($archdeptleaderNode){
                            $this->loadModel('review')->setReviewNodePending($archdeptleaderNode->id);
                        }
                        //如果都不存在 则走正常审批 主要兼容 内部项目 和 已发起审批流程的项目
                        if(!$productdeptleaderNode and !$archdeptleaderNode){
                            $next = $this->review->setNodePending('projectplan', $planID, $plan->version);
                            if (!$next) {
                                //->set('insideStatus')->eq('pass') 迭代 26 公司总经理审批通过后，内部项目状态为“待立项”，项目负责人点击“确认立项”按钮后，内部项目状态由“待立项”变为“已立项”
                                $creation = $this->getCreationByID($planID);
                                $this->dao->update(TABLE_PROJECTPLAN)
                                    ->set('code')->eq($creation->code)
                                    ->set('mark')->eq($creation->mark)
                                    ->set('status')->eq('pass')
//                    ->set('insideStatus')->eq('pass')
                                    ->where('id')->eq($planID)->exec();
                                $mailsend = true;
                            }
                        }
                    }else{
                        // 全部评审完
                        $next = $this->review->setNodePending('projectplan', $planID, $plan->version);
                        if (!$next) {
                            //->set('insideStatus')->eq('pass') 迭代 26 公司总经理审批通过后，内部项目状态为“待立项”，项目负责人点击“确认立项”按钮后，内部项目状态由“待立项”变为“已立项”
                            $creation = $this->getCreationByID($planID);
                            $this->dao->update(TABLE_PROJECTPLAN)
                                ->set('code')->eq($creation->code)
                                ->set('mark')->eq($creation->mark)
                                ->set('status')->eq('pass')
//                    ->set('insideStatus')->eq('pass')
                                ->where('id')->eq($planID)->exec();
                            $mailsend = true;
                        }
                    }
                }


            } elseif ($result == 'reject') {
                $this->dao->update(TABLE_PROJECTPLAN)->set('isInit')->eq(0)->set('status')->eq('reject')->where('id')->eq($planID)->exec();

            }
            if(!dao::isError())
            {

                $this->dao->commit();
//            $this->dao->rollBack();

                return array('result' => $result, 'grade' => $plan->reviewStage + 1,'mailsend'=>$mailsend,'appendComment'=>$appendComment);
            }else{
                $this->dao->rollBack();

                return array('result' => $result, 'grade' => 0,'mailsend'=>false,'appendComment'=>$appendComment);
            }

        }catch (Error $e){
            $this->dao->rollBack();
            return dao::$errors[] = $e->getMessage();
        }



    }

    public function yearReviewing($planID)
    {
        if (!$this->post->result) {
            dao::$errors['result'] = $this->lang->projectplan->resultEmpty;
            return false;
        }

        if ($this->post->result == 'reject' && !$this->post->comment) {
            dao::$errors['comment'] = $this->lang->projectplan->commentEmpty;
            return false;
        }

        $plan = $this->getByID($planID);

        /*
         * 此处开发环境与测试环境多选有区别:
         * ①开发环境如果不选则不传递该字段
         * ②测试环境如果不选择，则一定会传递一个array(0=>'')的数组。如果选择，则传递 array(0=>'',1=>'选中1'，2=>'选中2')形式。
        */
        if ($this->post->result == 'pass') {
            if ($plan->reviewStage == 2) {
                $postReviewer = $this->post->reviewer;
                if ($postReviewer) {
                    $numberReviewer = count($postReviewer);
                    //测试环境判断
                    if ($numberReviewer == 1 && empty($postReviewer[0])) {
                        dao::$errors['reviewer'] = $this->lang->projectplan->architectEmpty;
                        return false;
                    }
                } else {
                    //开发环境判断
                    dao::$errors['reviewer'] = $this->lang->projectplan->architectEmpty;
                    return false;
                }
            }
        }
        /*//防止在地盘待处理和年度计划页面重复审批
        $repeatVerify = $this->selectActionInfo($planID,$plan->yearVersion,'projectplanyear');
        if(!$repeatVerify){
            dao::$errors[] = $this->lang->projectplan->approvalEmpty;
            return false;
        }*/

        $nodeCodeLang = $this->lang->projectplan->nodeCode;
        $mailtoArr = $this->post->mailto;
        $mailto = !empty($mailtoArr) ? implode($mailtoArr, ',') : '';
        $extra = new stdClass();
        $extra->involved = $this->post->reviewer;
        $result = $this->loadModel('review')->check('projectplanyear', $planID, $plan->yearVersion, $this->post->result, $this->post->comment, $plan->reviewStage, $extra);
        if ($result == 'part' && $plan->reviewStage == 3) {
            $this->dao->update(TABLE_PROJECTPLAN)->set('mailto')->eq($mailto)->where('id')->eq($planID)->exec();
        }
        $addNodeStage = ($plan->reviewStage) +1;
        $actionFlag = false;
        $actionActArr = [];
        if ($result == 'pass') {
            switch ($plan->reviewStage) {
                //申请人部门负责人审批
                case 1:
                    $myDept = $this->loadModel('dept')->getByID($plan->bearDept); //架构部 由固定架构部配置的接口人员改为各部门配置的接口人员
                    if($plan->yearVersion > 0 && $plan->rejectStatus > 0){
                        $manager1 = $this->loadModel('dept')->getByID(2); //架构部
                        if($plan->rejectStatus >= 4){
                            $reviewer = array($manager1->manager1);
                            $reviewStage = 4;
                        }else{
                            //申请人部门负责人退回
                            $reviewer = array($myDept->planPerson);
                            $reviewStage = $addNodeStage;
                        }
                    }else{
                        $reviewStage = $addNodeStage;
                        $reviewer = array($myDept->planPerson);
                    }
                    $param = ['nodeCode'=>$nodeCodeLang[$reviewStage]];
                    $this->loadModel('review')->addNode('projectplanyear', $planID, $plan->yearVersion, $reviewer, true, 'pending',$reviewStage,$param);
                    $this->addReviewStage($planID, $mailto,$reviewStage,$plan->reviewStage);
                    break;
                //平台架构部接口人审批
                case 2:
                    $reviewer = $this->post->reviewer;
                    $param = ['nodeCode'=>$nodeCodeLang[$addNodeStage]];
                    $this->loadModel('review')->addNode('projectplanyear', $planID, $plan->yearVersion, $reviewer, true, 'pending',$addNodeStage,$param);
                    $this->addReviewStage($planID, $mailto,$addNodeStage,$plan->reviewStage);
                    break;
                //架构师审批
                case 3:
                    // 全部评审完
                    $next = $this->setNodePass('projectplanyear', $planID, $plan->yearVersion);
                    if (!$next) {
                        $this->addReviewStage($planID, $mailto,$addNodeStage,$plan->reviewStage);
                        //增加架构部负责人审批流程
                        $myDept = $this->loadModel('dept')->getByID(2); //架构部负责人
                        $param = ['nodeCode'=>$nodeCodeLang[$addNodeStage]];
                        $this->loadModel('review')->addNode('projectplanyear', $planID, $plan->yearVersion, array($myDept->manager1), true, 'pending',$plan->reviewStage,$param);
                    }
                    break;
                case 4:
                    //架构部负责人审批
                    $leader = $this->post->leader;//技术总监必选
                    $leaderOther = $this->post->leaderOther;//分管领导
                    $reviewer = $this->post->leader;
                    if($leaderOther){
                        $reviewer = array_merge($leader,$leaderOther);
                    }
                    $this->addReviewStage($planID, $mailto,$addNodeStage,$plan->reviewStage);
                    $param = ['nodeCode'=>$nodeCodeLang[$addNodeStage]];
                    $this->loadModel('review')->addNode('projectplanyear', $planID, $plan->yearVersion, $reviewer, true, 'pending',$plan->reviewStage,$param);
                    //添加plancode
                    $this->savePlanCode($planID);
                    break;
                case 5:
                    // 全部评审完
                    $next = $this->setNodePass('projectplanyear', $planID, $plan->yearVersion);
                    if(!$next){
                        //技术总监和分管领导审批
                        $this->addReviewStage($planID, $mailto,$addNodeStage,$plan->reviewStage);
                        $param = ['nodeCode'=>$nodeCodeLang[$addNodeStage]];
                        $ceo = $this->loadModel('user')->getCeoUsers($plan->bearDept);
                        $this->loadModel('review')->addNode('projectplanyear', $planID, $plan->yearVersion, $ceo, true, 'pending',$plan->reviewStage,$param);
                    }
                    break;
                case 6:
                    //总经理审批
                    $this->addReviewStage($planID, $mailto,$addNodeStage,$plan->reviewStage);
                    $this->dao->update(TABLE_PROJECTPLAN)
                        ->set('status')->eq('yearpass')
                        ->set('insideStatus')->eq('wait')
                        ->set('reviewDate')->eq(date('Y-m-d'))
                        ->where('id')->eq($planID)->exec();
                    $actionFlag = true;
                    $actionActArr['planName'] = $plan->name;
                    $actionActArr['planID'] = $plan->id;
                    $actionActArr['status'] = 'yearpass';
                    break;
                default:
                    break;
            }
        } elseif ($result == 'reject') {
            $reviewStage = $plan->reviewStage;
            if($plan->rejectStatus >= 4 ){
                $reviewStage = 4;
            }
            $this->yearreject($planID,$reviewStage);
            $actionFlag = true;
            $actionActArr['planName'] = $plan->name;
            $actionActArr['planID'] = $plan->id;
            $actionActArr['status'] = 'yearreject';
        }

        return array('result' => $result, 'grade' => $plan->reviewStage + 1,'actionflag'=>$actionFlag,'actionActResult'=>$actionActArr);
    }
    public function addPlanAction($actionResult){

        $dateTime = date("Y-m-d H:i:s",time());

        $dateDay = date("Y-m-d",time());

        $actionUser = $this->app->user->account;

        $data = [
            'planName'=>$actionResult['planName'],
            'actionUser'=>$actionUser,
            'actionDay'=>$dateDay,
            'planID'=>$actionResult['planID'],
            'status'=>$actionResult['status'],
            'snapshotVersion'=>'',
            'fileUrl'=>'',
            'createTime'=>$dateTime,
            'updateTime'=>$dateTime,
        ];
        $this->dao->insert(TABLE_PROJECTPLANACTION)->data($data)->exec();


    }

    public function yearBatchReviewing($planID)
    {
        if (!$this->post->result) {
            dao::$errors['result'] = $this->lang->projectplan->resultEmpty;
            return false;
        }
        if ($this->post->result == 'reject' && !$this->post->comment) {
            dao::$errors['comment'] = $this->lang->projectplan->commentEmpty;
            return false;
        }
        /*if (!$this->post->comment) {
            dao::$errors['comment'] = $this->lang->projectplan->commentEmpty;
            return false;
        }*/
        if(!$planID){
            dao::$errors['comment'] = "计划id不能为空";
            return false;
        }
        $planIDArr = explode(",",$planID);

        $planlistinfo = $this->getInIDs($planID);

        $statusreviewStage = array_column($planlistinfo,"status","reviewStage");
        $statusreviewStatecount = count($statusreviewStage);

        if($statusreviewStatecount > 1){
            dao::$errors['yearBatchReviewing'] = "您勾选的任务未处在相同审批节点！";
            return false;
        }

        $this->dao->begin();
        $result = [];
        foreach ($planIDArr as $plan){

            $result[$plan] = $this->yearReviewing($plan);
            if(dao::isError())
            {


                $this->dao->rollBack();
                return false;
            }

        }
        $this->dao->commit();
        return $result;

    }

    /**
     * 变更审批操作
     * @param $planID
     * @return false|void
     */
    public function changeReview($planID)
    {
        $plan = $this->getByID($planID);
        //防止重复审批
        $repeatVerify = $this->selectActionInfo($planID,$plan->changeVersion,'planchange');
        if(!$repeatVerify){
            dao::$errors[] = $this->lang->projectplan->approvalEmpty;
            return false;
        }
        //审批结果
        if (!$this->post->result) {
            dao::$errors['result'] = $this->lang->projectplan->resultEmpty;
            return false;
        }

        //审批意见
        if ($this->post->result == 'reject' && !$this->post->comment) {
            dao::$errors['comment'] = $this->lang->projectplan->commentEmpty;
            return false;
        }

        //如果选择总经理一定要选择技术总监审批
        if($plan->changeStage == 4 and $this->post->leader){
            $postLeader = $this->post->leader;
            if(count($postLeader) == 1 and $postLeader[0] == 'isBoss'){
                dao::$errors['leader'] = $this->lang->projectplan->leaderCheckTips;
                return false;
            }
        }

        $post = fixer::input('post')
            ->remove('uid,planRemark')
            ->get();
        $version = $plan->changeVersion;
        $stage = $plan->changeStage;
        $mailtoArr = $this->post->mailto;
        $mailto = !empty($mailtoArr) ? implode($mailtoArr, ',') : '';
        $extra = new stdClass();
        $extra->involved = $this->app->user->account;
        $status = $post->result;
        $actionFlag = false;
        $actionActArr = [];
        if($status == 'pass' || $status == 'report'){
            $this->dao->begin();
            $this->loadModel('review')->check('planchange', $planID, $version, 'pass', $this->post->comment, $stage, $extra);
            //下一节点
            switch ($stage) {
                //申请人部门负责人审批
                case 1:
                    $myDept = $this->loadModel('dept')->getByID($plan->bearDept); //架构部 由固定架构部配置的接口人员改为各部门配置的接口人员
                    $reviewer = array($myDept->planPerson);
                    $this->nodeDeal('planchange',$planID, $version ,$reviewer,$stage,$mailto);
                    break;
                //平台架构部接口人审批
                case 2:
                    //上报状态默认当前节点为pass
                    if($status == 'report'){
                        $myDept = $this->loadModel('dept')->getByID(2); //架构部负责人
                        $reviewer = array($myDept->manager1);
//                        $architectUser = $post->architect;
//                        $res = $this->loadModel('review')->getReviewersByNodeCode('projectplanyear',$planID,$plan->yearVersion,'architect');
                        if(!isset($post->architect) || (isset($post->architect) && !$post->architect[0])){
                            dao::$errors['result'] = "架构师不存在";
                            $this->dao->rollBack();
                            return false;
                        }
//                        $res = explode(',',$res);

                        $this->nodeDeal('planchange',$planID, $version ,$post->architect,$stage,$mailto);
                    }else{
                        $this->passDeal($planID,$version,$mailto);
                        $actionFlag = true;
                        $actionActArr['planName'] = $plan->name;
                        $actionActArr['planID'] = $plan->id;
                        $actionActArr['status'] = 'changepass';
                    }
                    break;
                case 3:
                    //2023-03-13 增加架构师节点
                    //架构师审批
//                    $this->loadModel('review')->check('planchange', $planID, $version, 'pass', $this->post->comment, $stage, $extra);
                    // 全部评审完

                    $next = $this->review->getReviewer('planchange', $planID, $version);
                    if (!$next) {
                        $myDept = $this->loadModel('dept')->getByID(2); //架构部负责人
                        $reviewer = array($myDept->manager1);

                        $this->nodeDeal('planchange',$planID, $version ,$reviewer,$stage,$mailto);
                    }

                    break;
                //架构部负责人审批
                case 4:
                    $leader = $post->leader ?? [];
                    if(!empty($leader)){
                        $reviewer = array('hetielin');
                        $leaderApproval = implode(',',$leader);
                        $this->dao->update(TABLE_PROJECTPLAN)->set('leaderApproval')->eq($leaderApproval)->where('id')->eq($planID)->exec();
                        $this->nodeDeal('planchange',$planID, $version ,$reviewer,$stage,$mailto);
                    }else{
                        $this->passDeal($planID,$version,$mailto);
                        $actionFlag = true;
                        $actionActArr['planName'] = $plan->name;
                        $actionActArr['planID'] = $plan->id;
                        $actionActArr['status'] = 'changepass';
                    }
                    break;
                    //技术总监
                case 5:
                    $leader = $post->leader ?? [];
                    if(!empty($leader)){
                        //$reviewer = array('luoyongzhong');
                        $reviewer = $this->loadModel('user')->getCeoUsers($plan->bearDept);
                        $this->nodeDeal('planchange',$planID, $version ,$reviewer,$stage,$mailto);
                    }else{
                        $this->passDeal($planID,$version,$mailto);
                        $actionFlag = true;
                        $actionActArr['planName'] = $plan->name;
                        $actionActArr['planID'] = $plan->id;
                        $actionActArr['status'] = 'changepass';
                    }
                    break;
                //总经理审批
                case 6:
                    $this->passDeal($planID,$version,$mailto);
                    $actionFlag = true;
                    $actionActArr['planName'] = $plan->name;
                    $actionActArr['planID'] = $plan->id;
                    $actionActArr['status'] = 'changepass';
                    break;
                default:
                    break;
            }
            $this->dao->commit();
        }else if($status == 'reject'){
            $this->dao->begin();
            //更新状态，恢复立项
            $this->loadModel('review')->check('planchange', $planID, $version, 'reject', $this->post->comment, $stage, $extra);
            $this->dao->update(TABLE_PROJECTPLAN)
                ->set('changeStatus')->eq('reject')
                ->set('changeStage')->eq(0)
                ->where('id')->eq($planID)->exec();
            $this->dao->update(TABLE_PROJECTPLANCHANGE)->set('status')->eq('reject')->where('planID')->eq($planID)->andWhere('version')->eq($plan->changeVersion)->exec();
            $this->dao->commit();
            $actionFlag = true;
            $actionActArr['planName'] = $plan->name;
            $actionActArr['planID'] = $plan->id;
            $actionActArr['status'] = 'changereject';
        }
        return array('actionflag'=>$actionFlag,'actionActResult'=>$actionActArr);
    }

    /**
     * 添加节点处理
     * @param string $type
     * @param $planID
     * @param $version
     * @param $reviewer
     * @param $stage
     * @param $mailto
     */
    public function nodeDeal($type = 'planchange',$planID, $version ,$reviewer,$stage,$mailto)
    {
        $nodeCodeLang = $this->lang->projectplan->changeNodeCode;
        $reviewStage = $stage+1;
        $param = ['nodeCode'=>$nodeCodeLang[$reviewStage]]; //跳过架构师，获取架构部接口人
        $this->loadModel('review')->addNode($type, $planID, $version ,$reviewer, true, 'pending',$reviewStage,$param);
        $this->addChangePlanStage($planID, $mailto,$reviewStage);
    }

    /**
     * 审核通过处理
     * @param $planID
     * @param $version
     */
    public function passDeal($planID,$version,$mailto)
    {
        //更新数据
        $this->updatePlan($planID,$version,$mailto);
        $this->dao->update(TABLE_PROJECTPLANCHANGE)
            ->set('status')->eq('pass')
            ->where('planID')->eq($planID)
            ->andWhere('version')->eq($version)
            ->exec();
    }

    /**
     * @param $planID 年度计划设置isInit字段
     */
    public function yearreject($planID,$stage)
    {
        $this->dao->update(TABLE_PROJECTPLAN)->set('isInit')->eq(0)->set('status')->eq('yearreject')->set('rejectStatus')->eq($stage)->set('beforeStage')->eq($stage)->where('id')->eq($planID)->exec();
    }

    /**
     * 更新变更数据
     * @param $planID 年度计划id
     */
    public function updatePlan($planID,$version,$mailto)
    {
        $oldPlan =  $this->getByID($planID);
        $changeInfo = $this->getChangeList($planID,$version,'pending');
        $info = json_decode($changeInfo->content,true);
        $new = json_decode($changeInfo->new,true);
        $info['changeStatus'] = 'pass';
        $info['changeStage'] = 0;
        $info['changeMailto'] = $mailto;
        if($oldPlan->type != $info['type'] and $oldPlan->status != 'yearstart' and $oldPlan->status != 'yearreject' and $oldPlan->status != 'yearreviewing' and $oldPlan->status != '')
        {
            $info['planCode'] = $this->getPlanCode($info['type'], $info['year']) ;
            $info['oldPlanCode'] = ltrim($oldPlan->oldPlanCode.','. $oldPlan->planCode,',');
        }
        //不需要更新 年度计划的  planRemark 字段
        if(isset($info['planRemark'])){
            unset($info['planRemark']);
        }
        $this->dao->update(TABLE_PROJECTPLAN)->data($info)->where('id')->eq($planID)->exec();
        if(!isset($info['outsideProject'])){
            $info['outsideProject'] = '';
        }
        //同步更新项目立项书
        if(isset($new['name']) || isset($new['type'])){
            $this->dao->update(TABLE_PROJECTCREATION);


            if(isset($new['name'])){
                $this->dao->set('name')->eq($new['name']);
            }
            if(isset($new['type'])){
                $this->dao->set('type')->eq($new['type']);
            }


            $this->dao->where('plan')->eq($planID)->exec();

        }

        $this->maintainOutside($planID, $info['outsideProject']);
    }

    /**
     * @param $planID 增加节点记录
     */
    public function addReviewStage($planID, $mailto,$reviewStage,$beforeStage)
    {
        $this->dao->update(TABLE_PROJECTPLAN)->set('reviewStage')->eq($reviewStage)->set('mailto')->eq($mailto)->set('beforeStage')->eq($beforeStage)->set('rejectStatus')->eq(0)->where('id')->eq($planID)->exec();
    }

    /**
     * @param $planID 增加变更节点记录
     */
    public function addChangePlanStage($planID, $mailto,$changeStage)
    {
        $this->dao->update(TABLE_PROJECTPLAN)->set('changeStage')->eq($changeStage)->set('changeMailto')->eq($mailto)->where('id')->eq($planID)->exec();
    }


    /**
     * 获取上一版本部门审核人通过的状态
     * @param $objectType
     * @param $objectID
     * @param int $version
     * @param $status
     * @return array
     */
    public function getNodesByWhere($objectType, $objectID, $version = 1,$status,$nodeCode)
    {
        $nodes = $this->dao->select('*')->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq($objectType)
            ->andWhere('objectID')->eq($objectID)
            ->andWhere('version')->eq($version)
            ->andWhere('status')->eq($status)
            ->andWhere('nodeCode')->eq($nodeCode)
            ->orderBy('stage,id')->fetchAll('id');
        $reviewers = $this->dao->select('*')->from(TABLE_REVIEWER)->where('node')->in(array_keys($nodes))->fetchAll();
        $map = array();
        foreach($reviewers as $r)
        {
            if(!isset($map[$r->node]))
            {
                $info = new stdClass();
                $info->reviewedCount = 0;
                $info->reviewers = array();
                $map[$r->node] = $info;
            }

            $map[$r->node]->reviewers[] = $r;
            if($r->status != 'wait')
            {
                $map[$r->node]->reviewedCount += 1;
            }
        }

        $data = [];
        foreach($nodes as $key => $node)
        {
            $node->reviewers     = isset($map[$node->id]) ? $map[$node->id]->reviewers : '';
            $node->reviewedCount = isset($map[$node->id]) ? $map[$node->id]->reviewedCount : '';
            $data[] = $node;
        }

        return $data;
    }

    /**
     * 多节点审批处理
     */
    public function setNodePass($objectType, $objectID, $version)
    {
        return $this->dao->select('id')->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq($objectType)
            ->andWhere('version')->eq($version)
            ->andWhere('objectID')->eq($objectID)
            ->andWhere('status')->eq('pending')
            ->orderBy('stage,id')
            ->fetch();
    }

    /**
     * Desc: 防止地盘和年度计划页面重复审核
     * Date: 2022/9/6
     * Time: 15:20
     *
     * @param $planId
     * @param $yearVersion
     *
     */
    public function selectActionInfo($planId,$yearVersion,$objectType)
    {
        $reviewNode = $this->dao->select('id,status')->from(TABLE_REVIEWNODE)
            ->beginIF($objectType == 'projectplanyear')->where('objectType')->eq('projectplanyear')->fi()
            ->beginIF($objectType == 'projectplan')->where('objectType')->eq('projectplan')->fi()
            ->beginIF($objectType == 'planchange')->where('objectType')->eq('planchange')->fi()
            ->andWhere('objectID')->eq($planId)
            ->andWhere('version')->eq($yearVersion)
            ->fetchAll('id');
        $nodeArray = array_keys($reviewNode);
        if(!empty($nodeArray)){
            if($objectType == 'projectplanyear' && $this->app->user->account == 'admin'){
                return true;
            }else{
                $reviewer = $this->dao->select('`id`,`status`')->from(TABLE_REVIEWER)->where('node')->in($nodeArray)->andWhere('reviewer')->eq($this->app->user->account)->fetchALL('status');
                $reviewerArray = array_keys($reviewer);
                if (!in_array('pending', $reviewerArray)) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Project: chengfangjinke
     * Method: initProject
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:22
     * Desc: This is the code comment. This method is called initProject.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $planID
     * @return array
     */
    public function initProject($planID, $changeCreationOnly = 0)
    {
        $oldCreation = $this->getCreationByID($planID);
        $creation = fixer::input('post')
            ->add('createdBy', $this->app->user->account)
            ->add('createdDate', helper::today())
            ->add('status', 'start')
            ->add('plan', $planID)
            ->join('linkPlan', ',')
            ->join('source', ',')
            ->join('union', ',')
            ->join('dept', ',')
            ->remove('uid,files,labels,comment,planID')
            ->stripTags($this->config->projectplan->editor->initproject['id'], $this->config->allowedTags)
            ->get();
        if(!isset($creation->linkPlan)){
            $creation->linkPlan = '';
        }

        $creation = $this->loadModel('file')->processImgURL($creation, $this->config->projectplan->editor->initproject['id'], $this->post->uid);



        $mode = empty($oldCreation) ? 'create' : 'edit';
        if ($mode == 'create') {
            $this->dao->insert(TABLE_PROJECTCREATION)->data($creation)->autoCheck()
                ->batchCheck($this->config->projectplan->initproject->requiredFields, 'notempty')
                ->batchCheck('name,code,mark', 'unique')
                ->exec();
            if(dao::isError()){
                return false;
            }
            $creationID = $this->dao->lastInsertID();
            $this->file->updateObjectID($this->post->uid, $creationID, 'projectcreation');
            $this->file->saveUpload('projectcreation', $creationID);

            $result = $creationID;
        } else {
            $this->dao->update(TABLE_PROJECTCREATION)->data($creation)->autoCheck()
                ->batchCheck($this->config->projectplan->initproject->requiredFields, 'notempty')
                ->check('name', 'unique', "plan != $planID")
                ->check('code', 'unique', "plan != $planID")
                ->check('mark', 'unique', "plan != $planID")
                ->where('plan')->eq($planID)
                ->exec();
            if(dao::isError()){
                return false;
            }
            $this->file->updateObjectID($this->post->uid, $oldCreation->id, 'projectcreation');
            $this->file->saveUpload('projectcreation', $oldCreation->id);

            $result =  common::createChanges($oldCreation, $creation);
        }
        if ($changeCreationOnly == 0) $this->dao->update(TABLE_PROJECTPLAN)->set('isInit')->eq(true)->set('status')->eq('start')->where('id')->eq($planID)->exec(); //更改立项书不修改流程
        if($changeCreationOnly == 1){
            $planInfo = $this->getByID($planID);
            //如果存在 才能更新
            if($planInfo && $planInfo->code){
                $updatecodemark = new stdClass();
                $updatecodemark->mark = $creation->mark;
                $updatecodemark->code = $creation->code;
                $this->dao->update(TABLE_PROJECTPLAN)
                    ->data($updatecodemark)
    //                ->check('mark', 'unique', "id != $planID")
    //                ->check('code', 'unique', "id != $planID")
                    ->where('id')->eq($planID)->exec();

            //更新项目
             if($planInfo->project){
                 $this->dao->update(TABLE_PROJECT)
                     ->set('code')->eq($creation->mark)
                     ->where('id')->eq($planInfo->project)->exec();
             }
            }
        }
        return $result;
    }

    /**
     * Project: chengfangjinke
     * Method: setListValue
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:22
     * Desc: This is the code comment. This method is called setListValue.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     */
    public function setListValue()
    {
        $this->app->loadLang('opinion');
        $users = $this->loadModel('user')->getPairs('noletter|noclosed');
        $depts = $this->loadModel('dept')->getOptionMenu();
        $lines = $this->loadModel('product')->getLinePairs(0);
        $apps = $this->loadModel('application')->getPairs(0);
        $outsideProject = $this->loadModel('outsideplan')->getPairs();

        foreach ($lines as $id => $name) $lines[$id] = "$name(#$id)";
        foreach ($depts as $id => $name) {
            if (!$id) continue;
            $depts[$id] = "$name(#$id)";
        }
        foreach ($apps as $id => $name) $apps[$id] = "$name(#$id)";
        foreach ($outsideProject as $id => $name) $outsideProject[$id] = "$name(#$id)";

        foreach ($users as $id => $name) {
            if (!$id) continue;
            $users[$id] = "$name(#$id)";
        }

        $typeList = $this->lang->projectplan->typeList;
        foreach ($typeList as $id => $name) {
            if (!$id) continue;
            $types[$id] = "$name(#$id)";
        }
        $categoryList = $this->lang->opinion->categoryList;
        $basisList = $this->lang->projectplan->basisList;
        $storyStatusList = $this->lang->projectplan->storyStatusList;
        $structureList = $this->lang->projectplan->structureList;
        $localizeList = $this->lang->projectplan->localizeList;
        $isImportantList = $this->lang->projectplan->isImportantList;
        $systemAssembleList = $this->lang->projectplan->systemAssembleList;
        $cloudComputingList = $this->lang->projectplan->cloudComputingList;
        $dataEnterLakeList = $this->lang->projectplan->dataEnterLakeList;
        $basicUpgradeList = $this->lang->projectplan->basicUpgradeList;
        $passwordChangeList = $this->lang->projectplan->passwordChangeList;


        $this->post->set('typeList', array_values($types));
        $this->post->set('ownerList', array_values($users));
        $this->post->set('categoryList', join(',', $categoryList));
        $this->post->set('basisList', join(',', $basisList));
        $this->post->set('storyStatusList', join(',', $storyStatusList));
        $this->post->set('structureList', join(',', $structureList));
        $this->post->set('localizeList', join(',', $localizeList));
        $this->post->set('bearDeptList', array_values($depts));
//        $this->post->set('lineList', array_values($lines));
        $this->post->set('platformownerList', join(',',$this->lang->projectplan->platformownerList));
        $this->post->set('appList', array_values($apps));
        $this->post->set('isImportantList', join(',', $isImportantList));
        $this->post->set('systemAssembleList', join(',', $systemAssembleList));
        $this->post->set('cloudComputingList', join(',', $cloudComputingList));
        $this->post->set('passwordChangeList', join(',', $passwordChangeList));
        $this->post->set('dataEnterLakeList', join(',', $dataEnterLakeList));
        $this->post->set('passwordChangeList', join(',', $passwordChangeList));
        $this->post->set('basicUpgradeList', join(',', $basicUpgradeList));
        $this->post->set('listStyle', $this->config->projectplan->export->listFields);
        $this->post->set('extraNum', 0);
    }

    /**
     * Project: chengfangjinke
     * Method: createFromImport
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:22
     * Desc: This is the code comment. This method is called createFromImport.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     */
    public function createFromImport()
    {
        $this->loadModel('action');
        $this->loadModel('projectplan');
        $this->loadModel('file');
        $now = helper::today();
        $data = fixer::input('post')->get();

        $this->app->loadClass('purifier', true);
        $purifierConfig = HTMLPurifier_Config::createDefault();
        $purifierConfig->set('Filter.YouTube', 1);
        $purifier = new HTMLPurifier($purifierConfig);

        if (!empty($_POST['id'])) {
            $oldProjectplans = $this->dao->select('*')->from(TABLE_PROJECTPLAN)->where('id')->in(($_POST['id']))->fetchAll('id');
        }

        $projectplans = array();
        $line = 1;
        foreach ($data->name as $key => $name) {
            $projectplanData = new stdclass();
            $specData = new stdclass();
            if (!$name) continue;
            $projectplanData->name = $name;
            $projectplanData->year = $data->year[$key];
            $projectplanData->basis = join(',', $data->basis[$key]); //2022-04-13 tongyanqi 改成多选
            $projectplanData->category = $data->category[$key];
            $projectplanData->content = $data->content[$key];
            $projectplanData->type = $data->type[$key];
            $projectplanData->planCode = $this->getCodeForImport($projectplanData->type, $projectplanData->year, 1);
//            $projectplanData->planCode = ""; //审核后才有
            $projectplanData->line = join(',', $data->line[$key]);
            $projectplanData->app = join(',', $data->app[$key]);
            $projectplanData->storyStatus = $data->storyStatus[$key];
            $projectplanData->structure = $data->structure[$key];
            $projectplanData->localize = $data->localize[$key];
            $projectplanData->reviewDate = $data->reviewDate[$key];
            $projectplanData->begin = $data->begin[$key];
            $projectplanData->end = $data->end[$key];
            $projectplanData->workload = $data->workload[$key];
            $projectplanData->workloadBase = $data->workloadBase[$key];
            $projectplanData->workloadChengdu = $data->workloadChengdu[$key];
            $projectplanData->nextYearWorkloadBase = $data->nextYearWorkloadBase[$key];
            $projectplanData->nextYearWorkloadChengdu = $data->nextYearWorkloadChengdu[$key];
            $projectplanData->duration = $data->duration[$key];
            $projectplanData->bearDept = join(',', $data->bearDept[$key]); //2022-04-13 tongyanqi 改成多选
            $projectplanData->owner = join(',', $data->owner[$key]);    //2022-04-13 tongyanqi 改成多选
            $projectplanData->phone = $data->phone[$key];
            $projectplanData->isImportant = $data->isImportant[$key];
//            $projectplanData->architrcturalTransform =  $data->architrcturalTransform[$key];
            $projectplanData->systemAssemble = $data->systemAssemble[$key];
            $projectplanData->cloudComputing = $data->cloudComputing[$key];
            $projectplanData->passwordChange = $data->passwordChange[$key];
            $projectplanData->dataEnterLake = $data->dataEnterLake[$key];
            $projectplanData->basicUpgrade = $data->basicUpgrade[$key];
            $projectplanData->planRemark = $data->planRemark[$key]; //2022-04-21 tongyanqi 添加备注
            $projectplanData->createdDate = $now;
            $projectplanData->outsideProject = join(',', $data->outsideProject[$key]);
            if (isset($projectplanData->outsideProject)) {
                $projectplanData->outsideProject = ',' . $projectplanData->outsideProject . ',';
            }

            $projectplanData->outsideSubProject = join(',', $data->outsideSubProject[$key]);
            if (isset($projectplanData->outsideSubProject)) {
                $projectplanData->outsideSubProject = ',' . $projectplanData->outsideSubProject . ',';
            }

            $projectplanData->outsideTask = join(',', $data->outsideTask[$key]);
            if (isset($projectplanData->outsideTask)) {
                $projectplanData->outsideTask = ',' . $projectplanData->outsideTask . ',';
            }

            if (isset($this->config->projectplan->create->requiredFields)) {
                $requiredFields = explode(',', $this->config->projectplan->create->requiredFields);
                foreach ($requiredFields as $requiredField) {
                    $requiredField = trim($requiredField);
                    if (empty($projectplanData->$requiredField)) dao::$errors[] = sprintf($this->lang->projectplan->noRequire, $line, $this->lang->projectplan->$requiredField);
                }
            }

            $projectplans[$key]['projectplanData'] = $projectplanData;
            $line++;
        }
        if (dao::isError()) die(js::error(dao::getError()));
//        $projectplans = array_reverse($projectplans);
        foreach ($projectplans as $key => $newProjectplan) {
            $projectplanData = $newProjectplan['projectplanData'];

            $projectplanID = 0;
            if (!empty($_POST['id'][$key]) and empty($_POST['insert'])) {
                $projectplanID = $data->id[$key];
                if (!isset($oldProjectplans[$projectplanID])) $projectplanID = 0;
            }

            if ($projectplanID) {
                $oldProjectplan = $oldProjectplans[$projectplanID];

                $projectplanChanges = common::createChanges($oldProjectplan, $projectplanData);

                if ($projectplanChanges) {
                    $this->dao->update(TABLE_PROJECTPLAN)
                        ->data($projectplanData)
                        ->autoCheck()
                        ->batchCheck($this->config->projectplan->create->requiredFields, 'notempty')
                        ->where('id')->eq((int)$projectplanID)->exec();

                    if (!dao::isError()) {
                        $this->maintainOutside($projectplanID, $projectplanData->outsideProject);
                        if ($projectplanChanges) {
                            $actionID = $this->action->create('projectplan', $projectplanID, 'Edited', '');
                            $this->action->logHistory($actionID, $projectplanChanges);
                        }
                    }
                }
            } else {
                $projectplanData->createdBy = $this->app->user->account;
                $projectplanData->createdDate = $now;
                $projectplanData->status = 'yearpass'; // 如果通过文件导入年度计划，默认是审批通过的。

                $this->dao->insert(TABLE_PROJECTPLAN)->data($projectplanData)->autoCheck()->exec();

                if (!dao::isError()) {
                    $projectplanID = $this->dao->lastInsertID();
                    $this->maintainOutside($projectplanID, $projectplanData->outsideProject);

                    $this->action->create('projectplan', $projectplanID, 'import', '');
                }
            }
            //更新外部关联项目
            if($projectplanID){
                if(isset($projectplanData->outsideProject)){
                    $this->maintainOutside($projectplanID, $projectplanData->outsideProject);
                }else{
                    $this->maintainOutside($projectplanID, '');
                }

            }
            if (dao::isError()) die(js::error(dao::getError()));
        }


        if ($this->post->isEndPage) {
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
     * Time: 17:22
     * Desc: This is the code comment. This method is called exec.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $planID
     * @return false|void
     */
    public function exec($planID)
    {
        //需求相关id
        $postData = fixer::input('post')
            ->get();
        $project = new stdClass();

        // 创建项目
        $plan = $this->getByID($planID);

        if($plan->status == 'projected'){

            dao::$errors[] = '该项目已立项，请重新刷新页面查看';
            return false;
        }

        $project->name           = $plan->name;
        $project->status         = 'wait';
        $project->model          = 'waterfall';
        //2024-05-27 不再向 项目空间同步
//        $project->begin          = $plan->creation->begin;//$plan->begin; //报工需求 计划开始时间改为项目起止时间
//        $project->end            = $plan->creation->end;//$plan->end;//报工需求 计划结束时间改为项目起止时间
        $project->days           = 0;
        $project->code           = $plan->mark;
        $project->openedBy       = $this->app->user->account;
        $project->openedDate     = helper::now();
        $project->lastEditedBy   = $this->app->user->account;
        $project->lastEditedDate = helper::now();
        $project->type           = 'project';
        $project->acl            = 'private'; //修改立项后 默认私有
        $project->PM             = $plan->creation->PM; // 修复年度计划表中没有项目经理问题
        $project->opinions             = $plan->opinion;
        $project->requirements             = $plan->requirement;
        $project->demands             = $plan->demand;
        $project->isShangHai          = $this->isShangHai($planID) ? 1 :2;//是否上海项目

        /**
         * 2022-09-06 songdi 添加人月工时计算参数
         * 自定义配置项，根据生效日期取参数计算
         */
        $this->app->loadLang("project");
        $this->app->loadConfig('project');
        $fieldList = zget($this->lang->project, "workHours", '');
        $today = date("Y-m-d");
        $workHours = "22";
        if ($fieldList['effectiveDate'] != '' && $today >= $fieldList['effectiveDate'] && $workHours != ''){
            $workHours = $fieldList['workHours'];
        }
        $project->workHours = $workHours;

        //计划时间
        $creation = $this->loadModel('projectplan')->getCreationByID($planID);
        //$project->planWorkload = $creation->workload;

        //2023-04-12 songdi 添加后台自定义配置人员到项目白名单
            $setWhiteList = json_decode($this->config->project->setWhiteList);
            $setWhiteListValue = $setWhiteList[0]->values;
        //2022-04-12 tongyanqi 添加总经理和部门领导到项目白名单
        $whitelist_array = $this->getBosses($plan);
        $project->whitelist = implode(',',$whitelist_array['managers']) .',' . implode(',',$whitelist_array['bosses']) . ','.$setWhiteListValue;
        //end 2022-04-12 tongyanqi 添加总经理和部门领导到项目白名单
//        $whitelist_array = array_merge($whitelist_array,explode(',',$setWhiteListValue));
        $this->dao->insert(TABLE_PROJECT)->data($project)->exec();
        $projectID = $this->dao->lastInsertID();

        $this->addWhiteList($whitelist_array['managers'], $projectID); //2022-04-19 tongyanqi 记录白名单操作
        $this->addWhiteList($whitelist_array['bosses'], $projectID,1005); //2023-04-20 songdi 记录白名单操作 公司领导
        $this->addWhiteList(explode(',',$setWhiteListValue), $projectID,1004); //2023-04-20 宋迪 记录白名单操作  自定义白名单
        //白名单立即生效 立项后默认私有
        $this->loadModel('user')->updateUserView($projectID, 'project');
        // 记录项目与产品、产品版本的关联关系。
//        $this->recordRelationPlan($projectID, $products, $productPlans);
//        $this->addRelationDetail($projectID, $products, $productPlans); //关联详细信息
        // 为项目添加团队成员。
        $this->addTeamMember($projectID,$plan->owner);

        // 为项目关联产品。

        $this->loadModel('project')->updateProducts($projectID);

        // 修改年度计划状态。
        $this->dao->update(TABLE_PROJECTPLAN)
            ->set('project')->eq($projectID)
            ->set('status')->eq('projected')
            ->set('insideStatus')->eq('pass')
            ->where('id')->eq($planID)
            ->exec();
        if($projectID){

            $members = $this->loadModel('project')->getTeamMembers($projectID);
            $members = array_keys($members);
            $this->loadModel('user')->updateUserView($projectID, 'project', $members);
        }

        //需求意向添加项目
        $opinion = $plan->opinion ? explode(',',$plan->opinion) : [];
        $requirement = $plan->requirement ? explode(',',$plan->requirement) : [];
        $this->addProjectToOpinion($projectID, $opinion);
        $this->addProjectToRequirement($projectID, $requirement);
//        $this->addProjectToDemand($projectID, $_POST['demand'], $planID);

        $this->loadModel('task')->approvalAutoCreateStageAndTask($projectID);//自动在项目中生成计划阶段

    }

    /**
     * Project: chengfangjinke
     * Method: exec
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:22
     * Desc: This is the code comment. This method is called exec.
     * remarks: The sooner you start to code, the longer the program will take. 由于需求意向前置，这个方法作为备份
     * Product: PhpStorm
     * @param $planID
     * @return false|void
     */
    public function execbak($planID)
    {
        //需求相关id
        $postData = fixer::input('post')
            ->join('opinion', ',')
            ->join('demand', ',')
            ->join('requirement', ',')
            ->get();
        if(!isset($_POST['opinion'])){
            $_POST['opinion'] = [];
        }
        if(!isset($_POST['demand'])){
            $_POST['demand'] = [];
        }
        if(!isset($_POST['requirement'])){
            $_POST['requirement'] = [];
        }
        $project = new stdClass();
        if(!isset($postData->opinion)){
            $postData->opinion = '';
        }
        if(!isset($postData->demand)){
            $postData->demand = '';
        }
        if(!isset($postData->requirement)){
            $postData->requirement = '';
        }
        if(isset($postData->opinion) && $postData->opinion){
            $project->opinions = ',' . $postData->opinion . ',';
        }else{
            $project->opinions = '';
        }
        if(isset($postData->demand) && $postData->demand){
            $project->demands = ',' . $postData->demand . ',';
        }else{
            $project->demands = '';
        }
        if(isset($postData->requirement) && $postData->requirement){
            $project->requirements = ',' . $postData->opinion . ',';
        }else{
            $project->requirements = '';
        }
        /* $postData->opinion && $project->opinions = ',' . $postData->opinion . ',';
         $postData->demand && $project->demands = ',' . $postData->demand . ',';
         $postData->requirement && $project->requirements = ',' . $postData->requirement . ',';*/

        // 创建项目
        $plan = $this->getByID($planID);

        $products = []; // 该字段根据需求条目所属产品和产品版本
        $productPlans = []; // 该字段根据需求条目所属产品和产品版本
        $demands = $this->loadModel('demand')->getManyDemands($project->demands);
        foreach ($demands as $demand) {
            $products[] = $demand->product;
            $productPlans[] = $demand->productPlan;
        }

        $project->name           = $plan->name;
        $project->status         = 'wait';
        $project->model          = 'waterfall';
        $project->begin          = $plan->begin;
        $project->end            = $plan->end;
        $project->days           = 0;
        $project->code           = $plan->mark;
        $project->openedBy       = $this->app->user->account;
        $project->openedDate     = helper::now();
        $project->lastEditedBy   = $this->app->user->account;
        $project->lastEditedDate = helper::now();
        $project->type           = 'project';
        $project->acl            = 'private'; //修改立项后 默认私有
        $project->PM             = $plan->creation->PM; // 修复年度计划表中没有项目经理问题
        /**
         * 2022-09-06 songdi 添加人月工时计算参数
         * 自定义配置项，根据生效日期取参数计算
         */
        $this->app->loadLang("project");
        $fieldList = zget($this->lang->project, "workHours", '');
        $today = date("Y-m-d");
        $workHours = "22";
        if ($fieldList['effectiveDate'] != '' && $today >= $fieldList['effectiveDate'] && $workHours != ''){
            $workHours = $fieldList['workHours'];
        }
        $project->workHours = $workHours;

        //2022-04-12 tongyanqi 添加总经理和部门领导到项目白名单
        $whitelist_array = $this->getBosses($plan);
        $project->whitelist = implode(',', $whitelist_array);;
        //end 2022-04-12 tongyanqi 添加总经理和部门领导到项目白名单

        $this->dao->insert(TABLE_PROJECT)->data($project)->exec();
        $projectID = $this->dao->lastInsertID();

        $this->addWhiteList($whitelist_array, $projectID); //2022-04-19 tongyanqi 记录白名单操作

        // 记录项目与产品、产品版本的关联关系。
        $this->recordRelationPlan($projectID, $products, $productPlans);
        $this->addRelationDetail($projectID, $products, $productPlans); //关联详细信息
        // 为项目添加团队成员。
        $this->addTeamMember($projectID);

        // 为项目关联产品。
        $productPlanList = array();
        foreach ($productPlans as $index => $productPlan) {
            $productPlanList[$products[$index]] = $productPlan;
        }
        $_POST['plans'] = $productPlanList;
        $_POST['products'] = $products;
        $this->loadModel('project')->updateProducts($projectID);

        // 修改年度计划状态。
        $this->dao->update(TABLE_PROJECTPLAN)
            ->set('project')->eq($projectID)
            ->set('status')->eq('projected')
            ->beginIF($project->opinions)->set('opinion')->eq($project->opinions)->fi()
            ->beginIF($project->demands)->set('demand')->eq($project->demands)->fi()
            ->beginIF($project->requirements)->set('requirement')->eq($project->requirements)->fi()
            ->where('id')->eq($planID)
            ->exec();

        //需求意向添加项目
        $this->addProjectToOpinion($projectID, $_POST['opinion']);
        $this->addProjectToDemand($projectID, $_POST['demand'], $planID);
        $this->addProjectToRequirement($projectID, $_POST['requirement']);

    }

    public function editPlanOpinion(){
        $projectplan = fixer::input('post')
            ->get();
        if(!$projectplan->planID){
            dao::$errors[] = $this->lang->projectplan->planIDNotEmpty;
            return false;
        }
        if(!$projectplan->opinionID){
            dao::$errors[] = $this->lang->projectplan->opinionIDNotEmpty;
            return false;
        }
        $plan = $this->getSimpleByID($projectplan->planID);

        if(!$plan){
            dao::$errors[] = $this->lang->projectplan->planNotExist;
            return false;
        }
        $intersect = [];
        if($plan->requirement){
            $planRequirementIDList = explode(',',$plan->requirement);
            $requirementList = $this->loadModel("requirement")->getByOpinion($projectplan->opinionID);
            $requirementIDList = array_column($requirementList,'id');
            $intersect = array_intersect($planRequirementIDList,$requirementIDList);

        }
        if($intersect){
            dao::$errors[] = $this->lang->projectplan->planRequirementNotEmpty;
            return false;
        }
        $opinionIDArr = explode(',',$plan->opinion);

        if(in_array($projectplan->opinionID,$opinionIDArr))
        {
            foreach ($opinionIDArr as $key=>$opinionID){
                if($projectplan->opinionID == $opinionID){
                    unset($opinionIDArr[$key]);
                }
            }
        }
        $opinionIDStr = trim(implode(',',$opinionIDArr),',');
        if($opinionIDStr){
            $opinionIDStr = ','.$opinionIDStr.',';
        }

        //解锁项目空间

//        $requirementProjectList = $this->dao->select("*")->from(TABLE_REQUIREMENT)->where("opinion")->eq($projectplan->opinionID)->andWhere("FIND_IN_SET('{$plan->project}',`project`)")->fetchAll('id');
        //更新项目空间
        if($plan->project){
            $opinionProject = $this->dao->select("*")->from(TABLE_OPINION)->where("id")->eq($projectplan->opinionID)->andWhere("FIND_IN_SET('{$plan->project}',`project`)")->fetch();
            if($opinionProject){
                $projectIDArr = explode(',',$opinionProject->project);
                foreach ($projectIDArr as $key=>$projectID){
                    if($plan->project == $projectID){
                        unset($projectIDArr[$key]);
                    }
                }
                $projectIDStr = trim(implode(',',$projectIDArr),',');
                if($projectIDStr){
                    $projectIDStr = ','.$projectIDStr.',';
                }
                $this->dao->update(TABLE_OPINION)->set('project')->eq($projectIDStr)->where("id")->eq($opinionProject->id)->exec();
            }
        }

        $this->dao->update(TABLE_PROJECTPLAN)->set('opinion')->eq($opinionIDStr)->where("id")->eq($plan->id)->exec();
        $newplan = $this->getSimpleByID($projectplan->planID);
        return common::createChanges($plan, $newplan);


    }

    public function editPlanRequirement(){
        $projectplan = fixer::input('post')
            ->get();
        if(!$projectplan->planID){
            dao::$errors[] = $this->lang->projectplan->planIDNotEmpty;
            return false;
        }
        if(!$projectplan->requirementID){
            dao::$errors[] = $this->lang->projectplan->requirementIDNotEmpty;
            return false;
        }
        $plan = $this->getSimpleByID($projectplan->planID);

        if(!$plan){
            dao::$errors[] = $this->lang->projectplan->planNotExist;
            return false;
        }
        $intersect = [];
        if($plan->demand){
            $planDemandIDList = explode(',',$plan->demand);
            $demandList = $this->loadModel("demand")->getBrowesByRequirementID($projectplan->requirementID);
            $demandList = array_column($demandList,null,'id');
            $demandIDList = array_column($demandList,'id');
            $intersect = array_intersect($planDemandIDList,$demandIDList);

        }
        if($intersect){
            dao::$errors[] = $this->lang->projectplan->planDemandNotEmpty;
            return false;
        }
        $requirementIDArr = explode(',',$plan->requirement);

        if(in_array($projectplan->requirementID,$requirementIDArr))
        {
            foreach ($requirementIDArr as $key=>$requirementID){
                if($projectplan->requirementID == $requirementID){
                    unset($requirementIDArr[$key]);
                }
            }
        }
        $requirementIDStr = trim(implode(',',$requirementIDArr),',');
        if($requirementIDStr){
            $requirementIDStr = ','.$requirementIDStr.',';
        }


        if($plan->project){
            $requirementProject = $this->dao->select("*")->from(TABLE_REQUIREMENT)->where("id")->eq($projectplan->requirementID)->andWhere("FIND_IN_SET('{$plan->project}',`project`)")->fetch();
            if($requirementProject){
                $projectIDArr = explode(',',$requirementProject->project);
                foreach ($projectIDArr as $key=>$projectID){
                    if($plan->project == $projectID){
                        unset($projectIDArr[$key]);
                    }
                }
                $projectIDStr = trim(implode(',',$projectIDArr),',');
                if($projectIDStr){
                    $projectIDStr = ','.$projectIDStr.',';
                }
                $this->dao->update(TABLE_REQUIREMENT)->set('project')->eq($projectIDStr)->where("id")->eq($requirementProject->id)->exec();
            }
        }

        $this->dao->update(TABLE_PROJECTPLAN)->set('requirement')->eq($requirementIDStr)->where("id")->eq($plan->id)->exec();
        $newplan = $this->getSimpleByID($projectplan->planID);
        return common::createChanges($plan, $newplan);


    }
    //2022-04-12 tongyanqi 添加总经理和部门领导到项目白名单
    public function getBosses($plan)
    {
        $bossAccounts = $this->loadModel('dept')->getDeptUserPairs(25); //取总经理们
        $bosses = implode(',', array_keys($bossAccounts));
        $depts = $this->loadModel('dept')->getByIDs($plan->bearDept); // 部门经理们
        $managers = '';
        foreach ($depts as $dept) {
            $managers .= $dept->manager . ',' . $dept->manager1 . ',' . $dept->leader . ',' . $dept->leader1 . ',';
        }
        $managers = rtrim($managers,',');
        return ['managers'=>array_unique(explode(',', $managers)),'bosses'=>array_unique(explode(',', $bosses))];
//        return array_unique(explode(',', $managers . $bosses));
    }

    //2022-04-19 tongyanqi 记录白名单操作
    public function addWhiteList($whitelist_array, $projectID,$reason=1001)
    {
        foreach ($whitelist_array as $accountName) {
            $acl = new stdClass();
            $acl->account = $accountName;
            $acl->objectType = 'project';
            $acl->objectID = $projectID;
            $acl->type = 'whitelist';
            $acl->source = 'add';
            $acl->reason = $reason; //立项领导白名单
            $this->dao->insert(TABLE_ACL)->data($acl)->autoCheck()->exec();
        }
    }

    // 为项目添加团队成员。
    public function addTeamMember($projectID,$planowner='')
    {
        if($planowner){
            $planowner = explode(',',$planowner);
            foreach ($planowner as $owner){
                if($owner){
                    $team = new stdclass();
                    $team->root = $projectID;
                    $team->type = 'project';
                    $team->account = $owner;
                    $team->position = '';
                    $team->join = date('Y-m-d');
                    $team->days = 5;
                    $team->hours = 7.0;
                    $this->dao->insert(TABLE_TEAM)->data($team)->exec();
                }
            }

        }else{
            $team = new stdclass();
            $team->root = $projectID;
            $team->type = 'project';
            $team->account = $this->app->user->account;
            $team->position = '';
            $team->join = date('Y-m-d');
            $team->days = 5;
            $team->hours = 7.0;
            $this->dao->insert(TABLE_TEAM)->data($team)->exec();
        }

    }

    //需求意向添加项目
    public function addProjectToOpinion($projectID, $opinions)
    {
        foreach ($opinions as $opinionId) {
            if (empty($opinionId)) continue;
            $opinion = $this->dao->select("project")->from(TABLE_OPINION)->where('id')->eq($opinionId)->fetch();
            $projectString = rtrim($opinion->project, ',') . ',' . $projectID . ',';
            $this->dao->update(TABLE_OPINION)->set('project')->eq($projectString)->where("id")->eq($opinionId)->exec();
        }
    }
    //需求意向添加项目
    public function addProjectToDemand($projectID, $demands, $planID = 0)
    {
        foreach ($demands as $demandId) {
            if (empty($demandId)) continue;
            $demand = $this->dao->select("project")->from(TABLE_DEMAND)->where('id')->eq($demandId)->fetch();
            $projectString = rtrim($demand->project, ',') . ',' . $projectID . ',';
            $projectString2 = rtrim($demand->projectPlan, ',') . ',' . $planID . ',';
            $this->dao->update(TABLE_DEMAND)->set('project')->eq($projectString)->set('projectPlan')->eq($projectString2)->where("id")->eq($demandId)->exec();;
        }
    }
    //需求意向添加项目
    public function addProjectToRequirement($projectID, $requirements)
    {
        foreach ($requirements as $requirementId) {
            if (empty($requirementId)) continue;
            $requirement = $this->dao->select("project")->from(TABLE_REQUIREMENT)->where('id')->eq($requirementId)->fetch();
            $projectString = rtrim($requirement->project, ',') . ',' . $projectID . ',';
            $this->dao->update(TABLE_REQUIREMENT)->set('project')->eq($projectString)->where("id")->eq($requirementId)->exec();;
        }
    }
    /*
     * 记录详细关联信息
     */
    public function addRelationDetail($projectID, $products, $plans)
    {
        $list = $this->getProductInfo($products);
        $selects = $this->loadModel('product')->getSelects();
        foreach ($list as &$product) {
            $product->os = $selects['osTypeList'][$product->os] ?? '';
            $product->arch = $selects['archTypeList'][$product->arch] ?? '';
        }

        $relations = [];
        foreach ($products as $i => $productId) {
            if (empty($productId)) {
                continue;
            }
            $planPairs = $this->loadModel('productplan')->getPairsForRelation($productId, 0, '', false, 1);
            $item = $list[$productId];
            $newItem = new stdClass();
            if ($plans[$i]) {
                $newItem->plan = $plans[$i];
                $newItem->planTitle = $planPairs[$plans[$i]] ?? "";
                if ($newItem->planTitle) { //有产品版本才能记录
                    $newItem->id = $item->id;
                    $newItem->code = $item->code;
                    $newItem->os = $item->os;
                    $newItem->arch = $item->arch;
                    $relations[] = $newItem;
                }
            }

        }
        if (!empty($relations)) { //关联数组不为空才记录
            $this->addRelation($projectID, json_encode($relations));
        }
    }
    public function getProducts($projectID)
    {
        $products = $this->dao->select('t2.id, t2.name, t2.type, t1.branch, t1.plan')->from(TABLE_PROJECTPRODUCT)->alias('t1')
            ->leftJoin(TABLE_PRODUCT)->alias('t2')->on('t1.product = t2.id')
            ->where('t1.project')->eq((int)$projectID)
            ->andWhere('t2.deleted')->eq(0)->fetchAll('id');
        $planIdList = array();
        foreach ($products as $productID => $info) {
            $planIdList[$productID] = $info->plan;
        }
        $planPairs = $this->dao->select('id,title')->from(TABLE_PRODUCTPLAN)->where('id')->in($planIdList)->fetchPairs();

        $productList = array();
        foreach ($products as $productID => $info) {
            $productList[$productID]['productID'] = $info->id;
            $productList[$productID]['productName'] = $info->name;
            $productList[$productID]['planID'] = $info->plan;
            $productList[$productID]['planName'] = zget($planPairs, $info->plan, $info->plan);
        }

        return $productList;
    }

    /**
     * 获取多个关联版本
     * @param $projectID
     * @return array
     */
    public function getCreationProducts($projectID)
    {
        $products = $this->dao->select('t2.id, t2.name, t2.type, t1.branch, t1.plan')->from(TABLE_PROJECTPRODUCT)->alias('t1')
            ->leftJoin(TABLE_PRODUCT)->alias('t2')->on('t1.product = t2.id')
            ->where('t1.project')->eq((int)$projectID)
            ->andWhere('t2.deleted')->eq(0)->fetchAll('id');
        $planIdList = array();
        foreach ($products as $productID => $info) {
            $planIdList[$productID] = $info->plan;
        }

        $planPairs = $this->dao->select('id,title')->from(TABLE_PRODUCTPLAN)->where('id')->in($planIdList)->fetchPairs();

        $productList = array();
        foreach ($products as $productID => $info) {
            $productList[$productID]['productID'] = $info->id;
            $productList[$productID]['productName'] = $info->name;
            $productList[$productID]['planID'] = $info->plan;
            $productList[$productID]['planName'] = zget($planPairs, $info->plan, $info->plan);
        }
        $addtions = $this->dao->select('*')->from(TABLE_RELATIONPLAN)->where('project')->eq((int)$projectID)->fetchAll('id');
        $i = 0;
        foreach ($addtions as $item) {
            $i++;

            $planPairs = $this->dao->select('id,title')->from(TABLE_PRODUCTPLAN)->where('id')->eq($item->plan)->fetchPairs();
            if (empty($productList[$item->product]['productID'])) continue;
            $productList[$item->product . '-' . $i]['productID'] = $productList[$item->product]['productID'];
            $productList[$item->product . '-' . $i]['productName'] = $productList[$item->product]['productName'];
            $productList[$item->product . '-' . $i]['planID'] = $item->plan;
            $productList[$item->product . '-' . $i]['planName'] = zget($planPairs, $item->plan, $item->plan);
        }
        foreach ($addtions as $item) {
            if (isset($productList[$item->product])) {
                unset($productList[$item->product]);
            }
        }
        return $productList;
    }

    /**
     * Project: chengfangjinke
     * Method: isClickable
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:22
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

        if ($action == 'yearreview') return $object->status == 'yearstart' and in_array($app->user->account, explode(',', $object->owner));
//        if ($action == 'yearreviewing') return ($object->status == 'yearreviewing' or $object->status == 'yearwait') and strpos(",$object->reviewers,", ",{$app->user->account},") !== false;
        if ($action == 'yearreviewing') return $app->user->account == 'admin' or (($object->status == 'yearreviewing' or $object->status == 'yearwait') and strpos(",$object->reviewers,", ",{$app->user->account},") !== false);

        if ($action == 'initproject') return ($object->status == 'yearpass' or $object->status == 'reject' or $object->status == 'start') and $object->changeStatus != 'pending' and in_array($app->user->account, explode(',', $object->owner));
        if ($action == 'submit') return ((($object->status == 'start' and $object->changeStatus != 'pending') or $object->status == 'reject') and $object->isInit == true) && in_array($app->user->account, explode(',', $object->owner));
        if ($action == 'review') return ($object->status == 'wait' or $object->status == 'reviewing') and strpos(",$object->reviewers,", ",{$app->user->account},") !== false;
        if ($action == 'exec')
        {
            return $object->status == 'pass' && in_array($app->user->account, explode(',', $object->owner));
        }
        if($action == 'edit')
        {
            return ($object->status == 'yearstart' or $object->status == 'yearreject' or ($object->status == 'yearreviewing' and $object->reviewStage <= 4)) and (in_array($app->user->account, explode(',', $object->owner)) or $app->user->account == $object->createdBy);
        }
        //20221008 流程状态为“计划审批中”时，以及后续状态均不可编辑操作，需要通过变更
        if ($action == 'planchange') return in_array($app->user->account, explode(',', $object->owner));
        if ($action == 'execedit') return $object->changeStatus != 'pending';
        if ($action == 'delete') return $object->status != 'deleted';
        if ($action == 'editstatus')        return ($object->status != 'yearstart' and $object->status != 'yearreject' and $object->status != 'yearreviewing' and $object->status != '');

        return true;
    }

    /**
     * Project: chengfangjinke
     * Method: sendmail
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:22
     * Desc: This is the code comment. This method is called sendmail.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $planID
     * @param $actionID
     * @param $isReissueMail 是否补发 默认非补发， 补发用于历史数据补发邮件
     * @return false|void
     */
    public function oldSendmail($planID, $actionID = 0, $isReissueMail = false)
    {
        $this->loadModel('mail');
        $plan = $this->getByID($planID);
        $users = $this->loadModel('user')->getPairs('noletter');

        /* Get action info. */
        if ($actionID) {
            $action = $this->loadModel('action')->getById($actionID);
            $history = $this->action->getHistory($actionID);
            $action->history = isset($history[$actionID]) ? $history[$actionID] : array();
        }
        //$action->appendLink = ''; 
        //if(strpos($action->extra, ':') !== false)
        //{   
        //    list($extra, $id) = explode(':', $action->extra);
        //    if($id and is_numeric($id))
        //    {   
        //        $action->extra = $extra;

        //        $name = $this->dao->select('name')->from(TABLE_PROJECTPLAN)->where('id')->eq($id)->fetch('name');
        //        if($name) $action->appendLink = html::a(zget($this->config->mail, 'domain', common::getSysURL()) . helper::createLink($action->objectType, 'view', "id=$id", 'html'), "#$id " . $name);
        //    }   
        //}   

        /* Get mail content. */
        $modulePath = $this->app->getModulePath($appName = '', 'projectplan');
        $oldcwd = getcwd();
        $viewFile = $modulePath . 'view/sendmail.html.php';
        chdir($modulePath . 'view');
        if (file_exists($modulePath . 'ext/view/sendmail.html.php')) {
            $viewFile = $modulePath . 'ext/view/sendmail.html.php';
            chdir($modulePath . 'ext/view');
        }
        ob_start();
        include $viewFile;
        foreach (glob($modulePath . 'ext/view/sendmail.*.html.hook.php') as $hookFile) include $hookFile;
        $mailContent = ob_get_contents();
        ob_end_clean();
        chdir($oldcwd);
        if ($isReissueMail) {//补发时需要验证立项审核通过或者已立项
            if (($plan->status != 'pass') && ($plan->status != 'projected')) {
                return false;
            }
        } else {//非补发，正常流程只允许立项审批通过
            if ($plan->status != 'pass') return false;
        }

        //$this->app->loadLang('opinion');
        //$depts    = $this->loadModel('dept')->getOptionMenu();
        //$plans    = $this->getPairs();
        //$outsides = $this->getOutsidePairs($plan->id);

        $sendUsers = $this->getToAndCcList($plan);

        if (!$sendUsers) return;
        list($toList, $ccList) = $sendUsers;
        if ($isReissueMail) {
            $ccList = '';
        }
        /* 处理邮件标题。*/
        $subject = $this->getSubject($plan);
        $subject = '立项通知-' . $plan->mark . '(' . $plan->name . ')' . '项目立项审批通过';
        $subject = htmlspecialchars_decode($subject);
        $mailContent = '';
        $mailContent .= '<p style="margin-left:20px;">' . $plan->mark . '（' . $plan->name . '）项目已完成立项申请，并请项目经理在10个工作日内提请项目管理计划书及其附件评审。</p>';
        $detailsURL = zget($this->config->mail, 'domain', common::getSysURL()) . helper::createLink('projectplan', 'view', array('id' => $plan->id));
        $mailContent .= '<p style="margin-left:20px;">项目基本情况：（<a href="' . $detailsURL . '" target="_blank">点击查看详情</a>）</p>';

        // -------------------
        //$mailContent .= '<p style="margin-left:20px;">' . $plan->mark . '（' . $plan->name .  '）项目已完成立项申请，并请项目经理在10个工作日内提请项目管理计划书及其附件评审</p>';
        //$detailsURL   = 'http://' . $_SERVER['SERVER_NAME'] . helper::createLink('projectplan', 'view', array('id' => $plan->id));
        //$mailContent .= '<p style="text-align: center;">项目基本情况如下：（<a href="' . $detailsURL . '" target="_blank">点击查看详情</a>）</p>';
        //$mailContent .= '<p style="text-align: center;">(项目立项书)</p>';
        //$mailContent .= '<table width="100%" border="1" cellpadding="0" cellspacing="0" style="table-layout: fixed;">';

        //$mailContent .= '<tr>';
        //$mailContent .= '<td>项目编号</td>';
        //$mailContent .= '<td>'. $plan->creation->code .'</td>';
        //$mailContent .= '<td>项目代号</td>';
        //$mailContent .= '<td>' . $plan->creation->mark .'</td>';
        //$mailContent .= '</tr>';

        //$mailContent .= '<tr>';
        //$mailContent .= '<td>项目名称</td>';
        //$mailContent .= '<td colspan="3">'. $plan->creation->name .'</td>';
        //$mailContent .= '</tr>';

        //$mailContent .= '<tr>';
        //$mailContent .= '<td>项目经理</td>';
        //$mailContent .= '<td>' . zget($users, $plan->creation->PM, '') . '</td>';
        //$mailContent .= '<td>归属部门</td>';
        //$mailContent .= '<td>' . zget($depts, $plan->creation->dept, '') . '</td>';
        //$mailContent .= '</tr>';

        //$mailContent .= '<tr>';
        //$mailContent .= '<td>项目来源</td>';
        //$mailContent .= '<td colspan="3">' . zget($this->lang->projectplan->sourceList, $plan->creation->source, '') . '</td>';
        //$mailContent .= '</tr>';

        //$mailContent .= '<tr>';
        //$mailContent .= '<td>外部信息化项目计划编号</td>';
        //$mailContent .= '<td colspan="3">';
        //if(!empty($outsides))
        //{
        //    foreach($outsides as $planID => $name)
        //    {
        //        $outsideplanURL = 'http://' . $_SERVER['SERVER_NAME'] . helper::createLink('outsideplan', 'view', "planID=$planID");
        //        if($planID) $mailContent .= '<p>' . html::a($outsideplanURL, $name) . '</p>';
        //    }
        //}
        //$mailContent .= '</td>';
        //$mailContent .= '</tr>';

        //$mailContent .= '<tr>';
        //$mailContent .= '<td>业务需求单位</td>';
        //$mailContent .= '<td colspan="3">';
        //foreach(explode(',', $plan->creation->union) as $union)
        //{
        //    $mailContent .= zget($this->lang->opinion->unionList, $union, '') . ' ';
        //}
        //$mailContent .= '</td>';
        //$mailContent .= '</tr>';

        //$mailContent .= '<tr>';
        //$mailContent .= '<td>项目类型</td>';
        //$mailContent .= '<td colspan="3">' . zget($this->lang->projectplan->typeList, $plan->creation->type, '') . '</td>';
        //$mailContent .= '</tr>';

        //$mailContent .= '<tr>';
        //$mailContent .= '<td>关联项目名称</td>';
        //$mailContent .= '<td colspan="3">';
        //$linkPlans = explode(',', str_replace(' ', '', $plan->creation->linkPlan));
        //foreach($linkPlans as $linkPlan) $mailContent .= ' ' . zget($plans, $linkPlan, '');
        //$mailContent .= '</td>';
        //$mailContent .= '</tr>';

        //$mailContent .= '<tr>';
        //$mailContent .= '<td>项目起止时间</td>';
        //$mailContent .= '<td colspan="3">' . $plan->creation->begin . ' - ' . $plan->creation->end . '</td>';
        //$mailContent .= '</tr>';

        //$mailContent .= '<tr>';
        //$mailContent .= '<td>年度计划工作量</td>';
        //$mailContent .= '<td colspan="3">'. $plan->creation->workload .'</td>';
        //$mailContent .= '</tr>';

        //$mailContent .= '<tr>';
        //$mailContent .= '<td>项目背景</td>';
        //$mailContent .= '<td colspan="3">' . $plan->creation->background . '</td>';
        //$mailContent .= '</tr>';

        //$mailContent .= '<tr>';
        //$mailContent .= '<td>项目范围</td>';
        //$mailContent .= '<td colspan="3">' . $plan->creation->range . '</td>';
        //$mailContent .= '</tr>';

        //$mailContent .= '<tr>';
        //$mailContent .= '<td>项目目标</td>';
        //$mailContent .= '<td colspan="3">' . $plan->creation->goal . '</td>';
        //$mailContent .= '</tr>';

        //$mailContent .= '<tr>';
        //$mailContent .= '<td>项目主要干系人</td>';
        //$mailContent .= '<td colspan="3">' . $plan->creation->stakeholder . '</td>';
        //$mailContent .= '</tr>';

        //$mailContent .= '<tr>';
        //$mailContent .= '<td>项目验收标准</td>';
        //$mailContent .= '<td colspan="3">' . $plan->creation->verify . '</td>';
        //$mailContent .= '</tr>';

        //$mailContent .= '</table>';


        /* Send it. */
        $this->mail->send($toList, $subject, $mailContent, $ccList);
        if ($this->mail->isError()) error_log(join("\n", $this->mail->getError()));
    }

    public function sendPlanProjectedMail($planID, $actionID){
        $this->loadModel('mail');
        $plan = $this->getByID($planID);
        $users = $this->loadModel('user')->getPairs('noletter');
        $depts = $this->loadModel('dept')->getOptionMenu();
        /* Get action info. */
        if ($actionID) {
            $action = $this->loadModel('action')->getById($actionID);
            $history = $this->action->getHistory($actionID);
            $action->history = isset($history[$actionID]) ? $history[$actionID] : array();
        }

        /* Get mail content. */
        $modulePath = $this->app->getModulePath($appName = '', 'projectplan');
        $oldcwd = getcwd();
        $viewFile = $modulePath . 'view/sendmail.html.php';
        chdir($modulePath . 'view');
        if (file_exists($modulePath . 'ext/view/sendmail.html.php')) {
            $viewFile = $modulePath . 'ext/view/sendmail.html.php';
            chdir($modulePath . 'ext/view');
        }
        ob_start();
        include $viewFile;
        foreach (glob($modulePath . 'ext/view/sendmail.*.html.hook.php') as $hookFile) include $hookFile;
        $mailContent = ob_get_contents();
        ob_end_clean();
        chdir($oldcwd);
        /*if ($plan->status != 'pass'){
            return false;
        }*/

        $this->loadModel("project");

        $toList = [];
        $ccList = '';

        foreach ($this->lang->project->setSystemAdmin as $key=>$userNmae){
            if($userNmae){
                $toList[] = $key;
            }

        }
        foreach ($this->lang->project->setOrganization as $key=>$userNmae){
            if($userNmae){
                $toList[] = $key;
            }
        }
        $toList = implode(',',array_unique($toList));

        /* 处理邮件标题。*/
//        $subject = $this->getSubject($plan);
//        $subject = '立项通知-' . $plan->mark . '(' . $plan->name . ')' . '项目立项审批通过';
        $subject = htmlspecialchars_decode(vsprintf($this->lang->projectplan->projectApprovalMailSubject,[$plan->mark,$plan->name]));

//        $mailContent = '';
//        $mailContent .= '<p style="margin-left:20px;">' . $plan->mark . '（' . $plan->name . '）项目已完成立项申请，并请项目经理在10个工作日内提请项目管理计划书及其附件评审。</p>';
        $detailsURL = zget($this->config->mail, 'domain', common::getSysURL()) . helper::createLink('projectplan', 'view', array('id' => $plan->id));
//        $mailContent .= '<p style="margin-left:20px;">项目基本情况：（<a href="' . $detailsURL . '" target="_blank">点击查看详情</a>）</p>';

        $mailContent = vsprintf($this->lang->projectplan->projectApprovalMailContent,[$plan->mark,$plan->name,$detailsURL]);

        /* Send it. */
        $this->mail->send($toList, $subject, $mailContent, $ccList);
        if ($this->mail->isError()) error_log(join("\n", $this->mail->getError()));
    }

    public function sendmail($planID, $actionID)
    {
        $this->loadModel('mail');
        $plan = $this->getByID($planID);
        $depts = $this->loadModel('dept')->getOptionMenu();
        $users = $this->loadModel('user')->getPairs('noletter');


        /* 获取后台通知中配置的邮件发信。*/
        $this->app->loadLang('custommail');
        $mailConf = isset($this->config->global->setPlanMail) ? $this->config->global->setPlanMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);
        $browseType = 'plan';

        if (!in_array($plan->status, $this->lang->projectplan->mailAllowSendStatus)) return false;

        if (in_array($plan->status, array('yearwait', 'yearreviewing', 'wait', 'reviewing','yearreject','reject','yearpass','start'))) {

            if($plan->status == 'reviewing'){ //立项审批中，部门会签环节，会签过程中不需要要发邮件
                $nodeCode = 'deptsSign';
                $reviewNode = $this->loadModel('review')->getPendingReviewNode('projectplan', $planID, $plan->version, $nodeCode);
                if($reviewNode){
                    $nodeId = $reviewNode->id;
                    $reviewedReviewers = $this->loadModel('review')->getReviewedReviewersByNodeId($nodeId); //有一部分人已经审核
                    if(!empty($reviewedReviewers)){
                        $unReviewReviewers = $this->loadModel('review')->getUnActionReviewersByNodeId($nodeId); //有一部分人还未审核
                        if(!empty($unReviewReviewers)){
                            return false; //立项审批中的环节
                        }
                    }
                }
            }
            $sendUsers = $this->getPendingToAndCcList($plan);
            list($toList, $ccList) = $sendUsers;
            //审批通过后触发通知邮件
            if($plan->status == 'yearpass' && $plan->changeStatus == 'no'){
                $toList = $plan->submitedBy;
                $ccList = $plan->mailto;
                $mailConf = isset($this->config->global->setPlanPassMail) ? $this->config->global->setPlanPassMail : '{"mailTitle":"","variables":[],"mailContent":""}';
                $mailConf = json_decode($mailConf);
            }else if($plan->status == 'yearreject' || $plan->status == 'reject'){
                $toList = $plan->submitedBy;
                $mailConf = isset($this->config->global->setPlanRejectMail) ? $this->config->global->setPlanRejectMail : '{"mailTitle":"","variables":[],"mailContent":""}';
                $mailConf = json_decode($mailConf);
            }
            //申请人部门负责人审核节点通过后通知人触发邮件
            if($plan->status == 'yearreviewing' and ($plan->reviewStage == 2 || $plan->beforeStage == 1)){
                $ccList = $plan->mailto;
            }

            //变更邮件 通过
            if(($plan->status == 'yearpass' || $plan->status == 'start') and ($plan->changeStatus == 'pending' || $plan->changeStatus == 'pass'))
            {
                if($plan->changeStatus == 'pass' && $plan->changeReview == 1){
                    $mailConf = isset($this->config->global->setPlanChangePassMail) ? $this->config->global->setPlanChangePassMail : '{"mailTitle":"","variables":[],"mailContent":""}';
                    $mailConf = json_decode($mailConf);
                }else if($plan->changeStatus == 'pass' && $plan->changeReview == 2){

                    $mailConf = isset($this->config->global->setPlanChangeNoReview) ? $this->config->global->setPlanChangeNoReview : '{"mailTitle":"","variables":[],"mailContent":""}';
                    $mailConf = json_decode($mailConf);
                    $planchange = $this->dao->select("id,planID,status,createdDate,planRemark")->from(TABLE_PROJECTPLANCHANGE)->where('planID')->eq($planID)->orderby("id desc")->fetch();
                }else{
                    $mailConf = isset($this->config->global->setPlanChangePendingMail) ? $this->config->global->setPlanChangePendingMail : '{"mailTitle":"","variables":[],"mailContent":""}';
                    $mailConf = json_decode($mailConf);
                }
                $sendUsers = $this->getChangeMsgPerson($plan,$plan->changeStatus);
                list($toList, $ccList) = $sendUsers;
            }

            //变更邮件 不通过
            if(($plan->status == 'yearpass' || $plan->status == 'start') and $plan->changeStatus == 'reject')
            {

                $mailConf = isset($this->config->global->setPlanChangeRejectMail) ? $this->config->global->setPlanChangeRejectMail : '{"mailTitle":"","variables":[],"mailContent":""}';
                $mailConf = json_decode($mailConf);
                $toList = $plan->owner;

            }

            //如果是变更邮件
            if(($plan->status == 'yearpass' || $plan->status == 'start') and ($plan->changeStatus == 'pending' || $plan->changeStatus == 'reject'))
            {
                $planchange = $this->dao->select("id,planID,status,createdDate,planRemark")->from(TABLE_PROJECTPLANCHANGE)->where('planID')->eq($planID)->orderby("id desc")->fetch();

            }
            /* 处理邮件标题。*/
            //$subject = $this->getSubject($plan);

            $plan->creation = $this->getCreationByID($planID);
        } elseif ($plan->status == 'pass') {
            $this->oldSendmail($planID, $actionID);
            return false;
        }
        /* 处理邮件发信的标题和日期。*/
        if (in_array($plan->status, array('yearwait', 'yearreviewing'))) {
            // 年度计划审批，处理日期获取。
            $firstDeal = $this->dao->select('*')->from(TABLE_REVIEWNODE)->where('objectType')->eq('projectplanyear')
                ->andWhere('objectID')->eq($planID)
                ->orderBy('id_asc')
                ->fetch();
        } else {
            // 年度计划立项审批，处理日期获取。
            $firstDeal = $this->dao->select('*')->from(TABLE_REVIEWNODE)->where('objectType')->eq('projectplan')
                ->andWhere('objectID')->eq($planID)
                ->orderBy('id_asc')
                ->fetch();
        }


        $firstDeal = empty($firstDeal) ? '' : $firstDeal->createdDate;
        $mailTitle = htmlspecialchars_decode(vsprintf($mailConf->mailTitle, $mailConf->variables));

        /* Get action info. */
        $action = $this->loadModel('action')->getById($actionID);
        $history = $this->action->getHistory($actionID);
        $action->history = isset($history[$actionID]) ? $history[$actionID] : array();

        /* Get mail content. */
        $modulePath = $this->app->getModulePath($appName = '', 'projectplan');
        $oldcwd = getcwd();
        $viewFile = $modulePath . 'view/sendmail.html.php';
        chdir($modulePath . 'view');

        if (file_exists($modulePath . 'ext/view/sendmail.html.php')) {
            $viewFile = $modulePath . 'ext/view/sendmail.html.php';
            chdir($modulePath . 'ext/view');
        }

        ob_start();
        include $viewFile;
        foreach (glob($modulePath . 'ext/view/sendmail.*.html.hook.php') as $hookFile) include $hookFile;
        $mailContent = ob_get_contents();
        ob_end_clean();
        chdir($oldcwd);
        $subject = $mailTitle;
        if(is_array($toList)){
            $toList = implode(',',$toList);
        }
        if(is_array($ccList)){
            $ccList = implode(',',$ccList);
        }

        /* Send it. */
        $this->mail->send($toList, $subject, $mailContent, $ccList);
        if ($this->mail->isError()) error_log(join("\n", $this->mail->getError()));
    }


    public function sendActionmail($planID)
    {
        $this->loadModel('mail');
        $plan = $this->getByID($planID);
        $depts = $this->loadModel('dept')->getOptionMenu();
        $users = $this->loadModel('user')->getPairs('noletter');

        /* 获取后台通知中配置的邮件发信。*/
        $this->app->loadLang('custommail');
        $mailConf = isset($this->config->global->setPlanActionTrigerMail) ? $this->config->global->setPlanActionTrigerMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);
        $browseType = 'plan';


        $toList = array_filter(array_keys($this->lang->projectplan->changeNoticeUser));
        $ccList = "";
        $mailTitle = htmlspecialchars_decode($mailConf->mailTitle);
        $mailContent = $mailConf->mailContent;
//        $mailTitle = vsprintf($mailConf->mailTitle, $mailConf->variables);
        /* Get mail content. */
        $modulePath = $this->app->getModulePath($appName = '', 'projectplan');
        $oldcwd = getcwd();
        $viewFile = $modulePath . 'view/sendactionmail.html.php';
        chdir($modulePath . 'view');

        ob_start();
        include $viewFile;
        foreach (glob($modulePath . 'ext/view/sendmail.*.html.hook.php') as $hookFile) include $hookFile;
        $mailContent = ob_get_contents();
        ob_end_clean();

        chdir($oldcwd);
        $subject = $mailConf->mailTitle;
        if(is_array($toList)){
            $toList = implode(',',$toList);
        }


        /* Send it. */
        $this->mail->send($toList, $subject, $mailContent, $ccList);
        if ($this->mail->isError()) error_log(join("\n", $this->mail->getError()));
    }

    /**
     * Project: chengfangjinke
     * Method: getSubject
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:22
     * Desc: This is the code comment. This method is called getSubject.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $plan
     * @return string
     */
    public function getSubject($plan)
    {
        return $this->lang->projectplan->projectCreation . '#' . $plan->id . ' ' . $plan->name . '-' . $this->lang->projectplan->labelList[$plan->status];
    }

    public function getPendingToAndCcList($plan)
    {
        if (in_array($plan->status, array('yearwait', 'yearreviewing'))) {
            $reviewers = $this->loadModel('review')->getReviewer('projectplanyear', $plan->id, $plan->yearVersion, $plan->reviewStage);
            $myDept = $this->loadModel('dept')->getByID($plan->bearDept); //架构部 由固定架构部配置的接口人员改为各部门配置的接口人员
            $planPerson = $myDept->planPerson;
            if($plan->reviewStage == 3 && ($this->app->user->account != $planPerson)){
                $reviewers = array();
            }
            //技术总监分管领导发邮件问题
            //2023-03-29 经产品沟通确认 去掉此判断
            /*
             * if($plan->reviewStage == 5){
                $reviewNode = $this->dao->select('id,status')->from(TABLE_REVIEWNODE)
                    ->where('objectId')->eq($plan->id)
                    ->andWhere('objectType')->eq('projectplanyear')
                    ->andWhere('stage')->eq($plan->reviewStage-1)
                    ->andWhere('version')->eq($plan->yearVersion)
                    ->fetch();
                if($reviewNode->status == 'pending'){
                    $reviewers = array();
                }
            }
            */
        } elseif (in_array($plan->status, array('wait', 'reviewing'))) {
            $reviewers = $this->loadModel('review')->getReviewer('projectplan', $plan->id, $plan->version, $plan->reviewStage);
            if ($plan->reviewStage == 2) {
                $nodes = $this->loadModel('review')->getNodes('projectplan', $plan->id, $plan->version);
                if ($nodes[1]->reviewers[0]->reviewer != $reviewers && count($nodes[2]->reviewers) != count(explode(',', $reviewers))) {
                    $reviewers = array();
                }
            }
        } else {
            $reviewers = array();
        }
        $toList = $reviewers;
        $ccList = '';
        return array($toList, $ccList);
    }

    /**
     * 变更发送人和抄送人数据获取
     * @param $plan
     * @return array
     */
    public function getChangeMsgPerson($plan,$changeStatus)
    {
        $reviewers = array();

        if($changeStatus == 'pending'){
            $reviewers = $this->loadModel('review')->getReviewer('planchange', $plan->id, $plan->changeVersion, $plan->changeStage);
            $ccList = '';
        }
        if($changeStatus == 'pass' && $plan->changeReview == 1){
            //获取变更提出人
            $changeInfo= $this->getChangeList($plan->id,$plan->changeVersion,'pass');
            $reviewers = $changeInfo->createdBy;
            $ccList = $plan->changeMailto;
        }else if($changeStatus == 'pass' && $plan->changeReview == 2){
            //所属部门部门负责人、平台架构部接口人、架构师（年度计划申请审批的架构师）
           $depts =  $this->loadModel("dept")->getByIDs(trim($plan->bearDept,','));

           $deptmanger = array_column($depts,'manager');

           $jiagoubu = $this->loadModel('dept')->getByID($plan->bearDept); //架构部 由固定架构部配置的接口人员改为各部门配置的接口人员

           //架构师
           $architect = $this->loadModel('review')->getReviewersByNodeCode('projectplanyear',$plan->id,$plan->yearVersion,'architect');


            $tempreviewers = implode(',',$deptmanger);
            if($architect){
                $tempreviewers .= ','.$architect;
            }
            if($jiagoubu->planPerson){
                $tempreviewers .= ','.$jiagoubu->planPerson;
            }

            $reviewers = implode(',',array_unique(explode(',',$tempreviewers)));
            $ccList = '';
        }

        $toList = $reviewers;
        $ccList = $ccList;

        return array($toList, $ccList);
    }

    public function getToAndCcList($plan)
    {
        $nodes = $this->loadModel('review')->getNodes('projectplan', $plan->id, $plan->version);

        /* Set toList and ccList. */
        $toList = array($plan->owner => $plan->owner);

        $resource = array();
        /*
        $involved = json_decode($nodes[2]->reviewers[0]->extra);
        if($involved)
        {
            foreach($involved->involved as $u)
            {
                if($u != 'false' and $u !== false) $toList[$u] = $u;
            }
        }
        */

        // 循环获取每个评审节点中的资源人员。
        foreach ($nodes as $node) {
            foreach ($node->reviewers as $reviewer) {
                $involved = json_decode($reviewer->extra);
                if (isset($involved->involved) and !empty($involved->involved)) {
                    foreach ($involved->involved as $u) {
                        if (!empty($u) and $u != 'false' and $u !== false) $toList[$u] = $u;
                    }
                }
            }
        }

        $toList = implode(',', $toList);

        $ccList = array();

        /*
          $ceoUserList = $this->dao->select('account,realname')->from(TABLE_USER)->where('role')->eq('ceo')->andWhere('deleted')->eq('0')->fetchPairs();
          foreach($ceoUserList as $account => $realname)
          {
              $ccList[$account] = $account;
          }

          $vpUserList = $this->dao->select('account,realname')->from(TABLE_USER)->where('role')->eq('vp')->andWhere('deleted')->eq('0')->fetchPairs();
          foreach($vpUserList as $account => $realname)
          {
              $ccList[$account] = $account;
          }

          $ctoUserList = $this->dao->select('account,realname')->from(TABLE_USER)->where('role')->eq('cto')->andWhere('deleted')->eq('0')->fetchPairs();
          foreach($ctoUserList as $account => $realname)
          {
              $ccList[$account] = $account;
          }
        */

        $ownerDept = 0;
        if ($plan->owner) $ownerDept = $this->dao->select('dept')->from(TABLE_USER)->where('account')->eq($plan->owner)->fetch('dept');
        if ($ownerDept) {
            $manager1 = $this->dao->select('manager1')->from(TABLE_DEPT)->where('id')->eq($ownerDept)->fetch('manager1');
            if ($manager1) $ccList[$manager1] = $manager1;

            $leader1 = $this->dao->select('leader1')->from(TABLE_DEPT)->where('id')->eq($ownerDept)->fetch('leader1');
            if ($leader1) $ccList[$leader1] = $leader1;
        }

        if (isset($plan->depts)) {
            $planDepts = explode(',', str_replace(' ', '', $plan->depts));
            $deptIdList = array();
            foreach ($planDepts as $deptID) {
                if ($deptID) $deptIdList[$deptID] = $deptID;
            }

            $managerList = $this->dao->select('id,manager1')->from(TABLE_DEPT)->where('id')->in($deptIdList)->fetchPairs();
            foreach ($managerList as $manager1) {
                if ($manager1) $ccList[$manager1] = $manager1;
            }
        }

        $ccList = implode(',', $ccList);

        $toList = trim($toList, ',');
        $ccList = trim($ccList, ',');
        return array($toList, $ccList);
    }

    /**
     * Project: chengfangjinke
     * Method: getTopDepts
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:22
     * Desc: This is the code comment. This method is called getTopDepts.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @return mixed
     */
    public function getTopDepts($where = null)
    {
        return $this->dao->select('*')->from(TABLE_DEPT)->where('grade')->eq(1)->beginIF($where)->orWhere('parent')->eq(30)->fi()->fetchAll();
    }

    /**
     * 获取所有部门
     * @return mixed
     */
    public function getAllDepts()
    {
        return $this->dao->select('*')->from(TABLE_DEPT)->fetchAll();
    }


    /**
     * 获取产品信息
     * @param array $productIds
     * @return mixed
     */
    public function getProductInfo($productIds = [])
    {
        return $this->dao->select('id, code, os, arch')->from(TABLE_PRODUCT)->where('id')->in($productIds)->fetchAll("id");
    }

    /**
     * 获取项目的产品计划
     * @param $projectId
     * @param $select
     * @return mixed
     */
    public function getProjectPlanInfo($projectId, $select = '*')
    {
        return $list = $this->dao->select($select)->from(TABLE_PROJECTPLAN)->where('project')->eq($projectId)->fetch();
    }

    public function getPlanByTaskID($taskid){
        return $this->dao->select("*")->from(TABLE_PROJECTPLAN)->where('deleted')->eq(0)->andWhere(" find_in_set({$taskid},`outsideTask`) ")->fetchAll();

    }

    public function getPlanBySubID($subid){
        return $this->dao->select("*")->from(TABLE_PROJECTPLAN)->where('deleted')->eq(0)->andWhere(" find_in_set({$subid},`outsideSubProject`) ")->fetchAll();

    }

    /**
     *通过(外部)项目/任务id获得内部项目列表
     *
     * @param $outplanid
     * @param string $field
     * @return array
     */
    public function getPlanByOutID($outplanid,$field="*"){
        $data = [];
        if(!$outplanid){
            return $data;
        }
        $ret = $this->dao->select($field)->from(TABLE_PROJECTPLAN)->where('deleted')->eq(0)->andWhere(" find_in_set({$outplanid},`outsideProject`) ")->fetchAll();
        if($ret){
            $data = $ret;
        }
        return $data;
    }

    /**
     * 通过(外部)项目/任务id获得内部项目名称列表
     *
     * @param $outplanid
     * @return array
     */
    public function getPlanNameListByOutID($outplanid){
        $data = [];
        if($outplanid){
            $select =  'id,concat(code,"_",mark,"_",name) as name';
            $ret = $this->getPlanByOutID($outplanid, $select);
            if($ret){
                $data = array_column($ret, 'name', 'id');
            }
        }
        return $data;
    }

    /**
     * 获得有(外部)项目/任务字段的内部项目
     *
     * @return array
     */
    public function getRelatedOutInPlanNameList(){
        $data = [];
        $ret = $this->dao->select('id,concat(code,"_",mark,"_",name) as name')->from(TABLE_PROJECTPLAN)->where('deleted')->eq(0)->andWhere(" outsideProject != '' and outsideProject != ',,'")->fetchPairs('id');
        if($ret){
            $data = $ret;
        }
        return $data;
    }

    /**
     * 添加项目的产品计划
     * @param $projectId
     * @param $relation
     */
    public function addRelation($projectId, $relation)
    {
        $record = $this->dao->select('*')->from(TABLE_PROJECTPLANRELATION)->where('projectId')->eq($projectId)->fetch();
        $project = new stdClass();
        if (empty($record)) {
            $project->projectId = $projectId;
            $project->planRelation = $relation;
            $project->createTime = $project->updateTime = date('Y-m-d H:i:s');
            $this->dao->insert(TABLE_PROJECTPLANRELATION)->data($project)->exec();
        } else {
            $project->planRelation = $relation;
            $project->updateTime = date('Y-m-d H:i:s');
            $this->dao->update(TABLE_PROJECTPLANRELATION)->data($project)->where('projectId')->eq($projectId)->exec();
        }
    }

    public function recordRelationPlan($projectID, $products, $plans)
    {
        $nowDate = helper::today();
        $nowUser = $this->app->user->account;
        $recordProduct = array();

        // 删除已有的关联关系。
        $this->dao->delete()->from(TABLE_RELATIONPLAN)->where('project')->eq($projectID)->exec();

        // 重新建立关联关系。
        foreach ($products as $index => $product) {
            if (empty($product)) continue;
            if (empty($plans[$index])) continue;
            if (isset($recordProduct[$product . '-' . $plans[$index]])) continue;

            $relationPlan = new stdClass();
            $relationPlan->project = $projectID;
            $relationPlan->product = $product;
            $relationPlan->plan = $plans[$index];
            $relationPlan->createdDate = $nowDate;
            $relationPlan->createdBy = $nowUser;
            $this->dao->insert(TABLE_RELATIONPLAN)->data($relationPlan)->exec();

            $recordProduct[$product . '-' . $relationPlan->plan] = 1;
        }
    }

    // 获取项目管理中项目与产品、产品计划（版本）的关联关系。
    public function getRecordRelationPlan($projectID)
    {
        return $this->dao->select('*')->from(TABLE_RELATIONPLAN)->where('project')->eq($projectID)->fetchAll();
    }

    public function getPlanByProjectID($projectID,$field='*')
    {
        $plan = $this->dao->select($field)->from(TABLE_PROJECTPLAN)->where('project')->eq($projectID)->fetch();
        return $plan;
    }

    public function getPlanBytaskIDFilter($taskID,$field='*'){
        return $this->dao->select($field)->from(TABLE_PROJECTPLAN)
            ->where(" find_in_set('{$taskID}',outsideTask) ")
            ->andWhere('deleted')->eq('0')
            ->andWhere('secondLine')->eq(0)
//            ->andWhere('changeStatus')->ne($this->lang->projectplan->ChangestatusEnglishList['pending'])
            ->fetchAll();
    }
    public function getPlanByFilter($field='*'){
        return $this->dao->select($field)->from(TABLE_PROJECTPLAN)
            ->where('deleted')->eq('0')
            ->andWhere('secondLine')->eq(0)
//            ->andWhere('changeStatus')->ne($this->lang->projectplan->ChangestatusEnglishList['pending'])
            ->fetchAll();
    }
    /**
     * User: TongYanQi
     * Date: 2022/8/29
     * 根本项目id获取年度计划
     */
    public function getPlanInProjectIDs($projectIDs)
    {
        $plans = $this->dao->select('`id`, `code`, `name`')->from(TABLE_PROJECTPLAN)->where('project')->in($projectIDs)->fetchAll('id');
        return $plans;
    }

    /*取选项 暂时没用到*/
    public function getOptionMenu($rootDeptID = 0)
    {
        $stmt = $this->dbh->query("select * from " . TABLE_LANG . " where module = 'projectplan' and lang = 'zh-cn'");
        $parts = array();
        while ($part = $stmt->fetch()) $parts[$part->section][$part->key] = $part->value;
        return $parts;
    }

    /*
     * 涉及产品处理表单内容
     */
    private function reformProductRelated()
    {
        $i = 0;
        $products = [];
        if (!is_array($_POST['productIds'])) {
            return [];
        } //没填
        foreach ($_POST['productIds'] as $productId) {
            if (empty($productId)) continue;

            if (baseValidater::checkDate($_POST['realRelease'][$i]) == false) {
                dao::$errors['realRelease'] = [sprintf($this->config->projectplan->realReleaseEmpty, $i + 1)];
                return false;
            }
            if (baseValidater::checkDate($_POST['realOnline'][$i]) == false) {
                dao::$errors['realOnline'] = [sprintf($this->config->projectplan->realOnlineEmpty, $i + 1)];
                return false;
            }
            if ($_POST['realOnline'][$i] < $_POST['realRelease'][$i]) {
                dao::$errors['realOnline'] = [sprintf($this->config->projectplan->realOnlineError, $i + 1)];
                return false;
            }
            $temp['productId'] = $productId;
            $temp['realRelease'] = $_POST['realRelease'][$i];
            $temp['realOnline'] = $_POST['realOnline'][$i];
            $products[] = $temp;
            $i++;
        }
        return $products;
    }

    /**
     * TongYanQi 2022/9/16
     * 组织计划阶段
     */
    private function reformPlanStage()
    {
        $stages = [];
        $count = count($_POST['stageBegin']);
        if ($count == 1 && empty($_POST['stageBegin'][0]) && empty($_POST['stageEnd'][0])) {
            return [];
        } //没填
        for ($i = 0; $i < $count; $i++) {
            if (empty($_POST['stageBegin'][$i])) {
                dao::$errors['planStage'] = [sprintf($this->config->projectplan->stageBeginError, $i + 1)];
                return false;
            }
            if (empty($_POST['stageEnd'][$i])) {
                dao::$errors['planStage'] = [sprintf($this->config->projectplan->stageEndError, $i + 1)];
                return false;
            }
            $temp['stageEnd'] = $_POST['stageEnd'][$i];
            $temp['stageBegin'] = $_POST['stageBegin'][$i];
            $stages[] = $temp;
        }
        return $stages;
    }

    /**
     *获得项目计划
     *
     * @param $ProjectID
     * @param string $select
     * @return stdClass|null
     */
    public function getPlanMainInfoByProjectID($ProjectID, $select = '*')
    {
        $data = new stdClass();
        $plan = $this->dao->select($select)->from(TABLE_PROJECTPLAN)->where('project')->eq($ProjectID)->fetch();
        if ($plan) {
            $data = $plan;
        }
        return $data;
    }

    /**
     *获得项目计划列表
     *
     * @param $projectIds
     * @param string $select
     * @return stdClass|null
     */
    public function getProjectPlanListByProjectIds($projectIds, $select = '*')
    {
        $data = [];
        if (!$projectIds) {
            return $data;
        }
        $ret = $this->dao->select($select)
            ->from(TABLE_PROJECTPLAN)
            ->where('project')->in($projectIds)
            ->fetchAll();
        if ($ret) {
            $data = $ret;
        }
        return $data;
    }

    /**
     * 获取公共项目
     * @return mixed
     */
    public function getOpenProject()
    {
        return $this->dao->select('t1.*')->from(TABLE_PROJECTPLAN)->alias('t1')
            ->leftJoin(TABLE_PROJECT)->alias('t2')->on('t1.project = t2.id')
            ->where('t2.acl')->eq('open')->fetchAll();
    }

    /**
     * TongYanQi 2022/9/14
     * 内部项目关联(外部)项目/任务
     */
    public function savePlanLinks($planID, $isNew = 0)
    {
        if ($isNew) {
            $this->dao->delete()->from(TABLE_PLANLINKS)->where('projectPlan')->eq($planID)->exec();
        }
        foreach ($_POST['outsideProject'] as $outsideProject) {
            if (empty($outsideProject)) continue;
            $data['outsidePlan'] = $outsideProject;
            $data['projectPlan'] = $planID;
            $this->dao->insert(TABLE_PLANLINKS)->data($data)->exec();
        }
    }

    /**
     * 生成code
     * @return string
     */
    public function getCode($type = '', $year = '')
    {
        return ''; //已作废
    }

    /**
     * 生成plancode 用于更新
     * @return string
     */
    public function getPlanCode($type = '', $year = '')
    {
        $codePrefix = $this->lang->projectplan->typeCodeList[$type] . '-' . $year . '-';

        $last = $this->dao->select('planCode')->from(TABLE_PROJECTPLAN)->where('planCode')->like("$codePrefix%")->orderby('planCode_desc')->fetch();

        $number = 1;
        if(isset($last->planCode)){
            $codeArr = explode('-',$last->planCode);

            $number = $number + intval(end($codeArr));
        }

        $code = $codePrefix . sprintf('%03d', $number);

        return $code;
    }

    public function savePlanCode($planId)
    {
        $plan = $this->getByID($planId);
        $codePrefix = $this->lang->projectplan->typeCodeList[$plan->type] . '-' . $plan->year . '-';
        $last = $this->dao->select('planCode')->from(TABLE_PROJECTPLAN)->where('planCode')->like("$codePrefix%")->orderby('planCode_desc')->fetch();
        $number = 1;
        if(isset($last->planCode)){
            $codeArr = explode('-',$last->planCode);

            $number = $number + intval(end($codeArr));
        }

        $code = $codePrefix . sprintf('%03d', $number);
        if(empty($plan->planCde)) $this->dao->update(TABLE_PROJECTPLAN)->data(['planCode' => $code])->where('id')->eq($planId)->exec();
    }

    private static $_typeCount = [];
    public function getCodeForImport($type = '', $year = '', $num = 1)
    {
        $codePrefix = $this->lang->projectplan->typeCodeList[$type] . '-' . $year . '-';

        $number = 1;

        if(isset(self::$_typeCount[$codePrefix]) == false) {
            $last = $this->dao->select('planCode')->from(TABLE_PROJECTPLAN)->where('planCode')->like("$codePrefix%")->orderby('planCode_desc')->fetch();
            if(isset($last->planCode)){
                $codeArr = explode('-',$last->planCode);

                self::$_typeCount[$codePrefix] = $number + intval(end($codeArr));
            }else{
                self::$_typeCount[$codePrefix] = $number;
            }
        } else {
            self::$_typeCount[$codePrefix] ++;
        }
        /*if(isset(self::$_typeCount[$codePrefix]) == false) {
            $number = $this->dao->select('count(id) c')->from(TABLE_PROJECTPLAN)->where('planCode')->like("$codePrefix%")->fetch('c');
            self::$_typeCount[$codePrefix] = $number + 1;
        } else {
            self::$_typeCount[$codePrefix] ++;
        }*/
        $code = $codePrefix . sprintf('%03d', self::$_typeCount[$codePrefix]);
        return $code;
    }
    /**
     * TongYanQi 2022/9/19
     * 必填项补丁
     */
    private function checkPost()
    {
        if (empty($_POST['basis'])) {
            dao::$errors['basis'] = $this->lang->projectplan->basisEmpty;
            return false;
        }
        if (empty($_POST['category'])) {
            dao::$errors['category'] = $this->lang->projectplan->categoryEmpty;
            return false;
        }
        if (empty($_POST['app'])) {
            dao::$errors['app'] = $this->lang->projectplan->appEmpty;
            return false;
        }
        if (empty($_POST['bearDept'])) {
            dao::$errors['bearDept'] = $this->lang->projectplan->bearDeptEmpty;
            return false;
        }
        //如果是 应用研发类改造和新建 则需求状态必填
        if($_POST['type'] == 1 || $_POST['type'] == 2){
            if(!$_POST['storyStatus']){
                dao::$errors['storyStatus'] = $this->lang->projectplan->storyStatusEmpty;
                return false;
            }
            if(!isset($_POST['platformowner'])){
                dao::$errors['platformowner'] = $this->lang->projectplan->platformownerEmpty;
                return false;
            }

            if(count($_POST['platformowner']) == 1 and !$_POST['platformowner'][0]){
                dao::$errors['platformowner'] = $this->lang->projectplan->platformownerEmpty;
                return false;
            }
        }
        return true;
    }

    /**
     * 分析问题后挂载到年度计划
     *
     * @param $problem
     *
     */
    public function insertProblemProjectPlan($oldProblem,$planId){
        //查询本项目下的问题
        $projectPlan = $this->dao->select('id,problem')->from(TABLE_PROJECTPLAN)
            ->where('project')->eq($planId)
            ->andWhere('deleted')->eq(0)
            ->fetch();
        $problemNew = (!empty($projectPlan->problem) ? $projectPlan->problem .','.$oldProblem->id.',' :','.$oldProblem->id).',' ;

        $data = new stdClass();
        $data->problem = array_filter(array_unique(explode(',',$problemNew))) ? ','.implode(',',array_filter(array_unique(explode(',',$problemNew)))).',' :'';
        $this->dao->update(TABLE_PROJECTPLAN)->data($data)->where('id')->eq($projectPlan->id)->exec();

        //查询问题否在其他项目中挂载，去除关系。
        $projectPlanOther = $this->dao->select('id,problem')->from(TABLE_PROJECTPLAN)
            ->where('project')->ne($planId)
            ->andWhere('problem')->like("%,".$oldProblem->id.',%')
            ->andWhere('deleted')->eq(0)
            ->fetchAll();

        if($projectPlanOther){
            //查询去除问题后内容
            foreach ($projectPlanOther as $item) {
                if(!$item) continue;
                $problems[$item->id] = str_replace(',,',',',str_replace($oldProblem->id,'',$item->problem));
            }
            //处理其他的问题
            foreach ($problems as $planId => $newProblemField) {
                $this->dao->update(TABLE_PROJECTPLAN)->set('problem')->eq($newProblemField)->where('id')->eq($planId)->exec();
            }

        }

    }

    public function ajaxshowdiffchange($changeID){
        $res = $this->dao->select("*")->from(TABLE_PROJECTPLANCHANGE)->where("id")->eq($changeID)->fetch();

        $res->new = json_decode($res->new,true);
        $res->old = json_decode($res->old);
        $res->content = json_decode($res->content);
        return $res;
    }


    public function getXuanxuanTargetUser($obj,$objectType, $objectID, $actionType, $actionID, $actor = ''){
        $toList = '';
        $mailConf = '';
        $mailConf = isset($this->config->global->setPlanMail) ? $this->config->global->setPlanMail : '{"mailTitle":"","variables":[],"mailContent":""}';

        if (in_array($obj->status, array('yearwait', 'yearreviewing', 'wait', 'reviewing','yearreject','reject','yearpass','start'))) {
            $sendUsers = $this->getPendingToAndCcList($obj);
            list($toList, $ccList) = $sendUsers;
            //审批通过后触发通知邮件
            if ($obj->status == 'yearpass' && $obj->changeStatus == 'no') {
                $toList   = $obj->submitedBy;
                $ccList   = $obj->mailto;
                $mailConf = isset($this->config->global->setPlanPassMail) ? $this->config->global->setPlanPassMail : '{"mailTitle":"","variables":[],"mailContent":""}';

            } else if ($obj->status == 'yearreject' || $obj->status == 'reject') {
                $toList   = $obj->submitedBy;
                $mailConf = isset($this->config->global->setPlanRejectMail) ? $this->config->global->setPlanRejectMail : '{"mailTitle":"","variables":[],"mailContent":""}';

            }
            //申请人部门负责人审核节点通过后通知人触发邮件
            if ($obj->status == 'yearreviewing' and ($obj->reviewStage == 2 || $obj->beforeStage == 1)) {
                $ccList = $obj->mailto;
            }

            //变更邮件 通过
            if (($obj->status == 'yearpass' || $obj->status == 'start') and ($obj->changeStatus == 'pending' || $obj->changeStatus == 'pass')) {
                if ($obj->changeStatus == 'pass') {
                    $mailConf = isset($this->config->global->setPlanChangePassMail) ? $this->config->global->setPlanChangePassMail : '{"mailTitle":"","variables":[],"mailContent":""}';

                } else {
                    $mailConf = isset($this->config->global->setPlanChangePendingMail) ? $this->config->global->setPlanChangePendingMail : '{"mailTitle":"","variables":[],"mailContent":""}';

                }
                $sendUsers = $this->getChangeMsgPerson($obj, $obj->changeStatus);
                list($toList, $ccList) = $sendUsers;
            }

            //变更邮件 不通过
            if (($obj->status == 'yearpass' || $obj->status == 'start') and $obj->changeStatus == 'reject') {

                $mailConf = isset($this->config->global->setPlanChangeRejectMail) ? $this->config->global->setPlanChangeRejectMail : '{"mailTitle":"","variables":[],"mailContent":""}';

                $toList = $obj->owner;

            }
        }elseif ($obj->status == 'pass') {
            $sendUsers = $this->loadModel("projectplan")->getToAndCcList($obj);

            if (!$sendUsers){
                list($toList, $ccList) = ['',''];
            }else{
                list($toList, $ccList) = $sendUsers;
            }


        }
        /*if (in_array($obj->status, array('yearwait', 'yearreviewing', 'wait', 'reviewing','yearreject','reject','yearpass'))) {
            $sendUsers = $this->getPendingToAndCcList($obj);

            list($toList, $ccList) = $sendUsers;
            //审批通过后触发通知邮件
            if($obj->status == 'yearpass' && $obj->changeStatus == 'no'){
                $toList = $obj->submitedBy;
                $ccList = $obj->mailto;

            }else if($obj->status == 'yearreject' || $obj->status == 'reject'){
                $toList = $obj->submitedBy;
            }
            //申请人部门负责人审核节点通过后通知人触发邮件
            if($obj->status == 'yearreviewing' and ($obj->reviewStage == 2 || $obj->beforeStage == 1)){
                $ccList = $obj->mailto;
            }
            //变更邮件 通过
            if($obj->status == 'yearpass' and ($obj->changeStatus == 'pending' || $obj->changeStatus == 'pass'))
            {
                $sendUsers = $this->getChangeMsgPerson($obj,$obj->changeStatus);
                list($toList, $ccList) = $sendUsers;
            }

            //变更邮件 不通过
            if($obj->status == 'yearpass' and $obj->changeStatus == 'reject')
            {
                $toList = $obj->owner;

            }

        } elseif ($obj->status == 'pass') {
            $sendUsers = $this->loadModel("projectplan")->getToAndCcList($obj);

            if (!$sendUsers){
                list($toList, $ccList) = ['',''];
            }else{
                list($toList, $ccList) = $sendUsers;
            }


        }*/

        if(is_array($toList)){
            $toList =  implode(",",$toList);
        }

        //邮件标题配置



//        $url = helper::createLink($objectType, 'view', "id=$objectID", 'html');
        $url = '';
        $subcontent = [];
        $subcontent['headTitle']    = '';

        $subcontent['headSubTitle'] = '';


        $subcontent['count']       = 0;
        $subcontent['id']       = 0;
        $subcontent['parent']       = '';
        $subcontent['parentURL']    = "";
        $subcontent['cardURL']      = $url;
        $subcontent['name']      = '';//消息体 编号后边位置 标题
        //标题
        $title = '';
        $actions = [];
        $this->session->set('isSendXuanxuan', false);
        return ['toList'=>$toList,'subcontent'=>$subcontent,'url'=>$url,'title'=>$title,'actions'=>$actions,'mailconfig'=>$mailConf];

    }

    public function updateOutsideplan($outplanID){
        $projectList = $this->dao->select("*")->from(TABLE_PROJECTPLAN)->where("FIND_IN_SET('{$outplanID}',outsideProject)")->fetchAll('id');
        if($projectList){
            $outsideTaskList = $this->dao->select("*")->from(TABLE_OUTSIDEPLANTASKS)->where('deleted')->eq('1')->andWhere('outsideProjectPlanID')->eq($outplanID)->fetchAll('id');
            $outsideSubList = $this->dao->select("*")->from(TABLE_OUTSIDEPLANSUBPROJECTS)->where('deleted')->eq('1')->andWhere('outsideProjectPlanID')->eq($outplanID)->fetchAll('id');
            $outsideTaskIDArr = array_column($outsideTaskList,'id');
            $outsideSubIDArr = array_column($outsideSubList,'id');
            foreach ($projectList as $planID=>$plan){
                $planoutTaskStr = $plan->outsideTask;
                $planoutsideSubStr = $plan->outsideSubProject;
                $planoutside = explode(',',$plan->outsideProject);
                $planoutsideTask = explode(',',$plan->outsideTask);
                $planoutsideSub = explode(',',$plan->outsideSubProject);
                //删除外部年度计划id
                foreach ($planoutside as $key=>$value){
                    if($value == $outplanID){
                        unset($planoutside[$key]);
                    }
                }
                $planoutsideStr = trim(implode(',',$planoutside),',');
                if($planoutsideStr){
                    $planoutsideStr = ','.$planoutsideStr.',';
                }
                //取交集找出要删除的任务id
                $deletetaskIDArr = array_intersect($planoutsideTask,$outsideTaskIDArr);

                if($deletetaskIDArr){
                    //和数据中的取差集，就是要保留的。
                    $planoutTaskStr = trim(implode(',',array_diff($planoutsideTask,$deletetaskIDArr)),',');
                    if($planoutTaskStr){
                        $planoutTaskStr = ','.$planoutTaskStr.',';
                    }
                }
                //找出要删除的子项目id
                $deleteSubIDArr = array_intersect($planoutsideSub,$outsideSubIDArr);
                //和数据中的取差集，就是要保留的。
                if($deleteSubIDArr){
                    $planoutsideSubStr = trim(implode(',',array_diff($planoutsideSub,$deleteSubIDArr)),',');
                    if($planoutsideSubStr){
                        $planoutsideSubStr = ','.$planoutsideSubStr.',';
                    }
                }

                $this->dao->update(TABLE_PROJECTPLAN)->set("outsideProject")->eq($planoutsideStr)
                    ->set("outsideTask")->eq($planoutTaskStr)
                    ->set("outsideSubProject")->eq($planoutsideSubStr)
                    ->where("id")->eq($plan->id)
                    ->exec();


            }
        }


    }

    public function str_replace_first($from, $to, $content)
    {
        $from = '/'.preg_quote($from, '/').'/';

        return preg_replace($from, $to, $content, 1);
    }

    /**
     * 通过年度计划承担部门判断是否上海项目
     * @param $planID
     * @return bool
     */
    public function isShangHai($planID){

        $plan = $this->getByID($planID);
        $deptName = $this->loadModel('dept')->getByID($plan->bearDept);
        $flag = (isset($deptName->name) && strpos($deptName->name,'上海') !== false) ? true : false ;
        return $flag;
    }
}


