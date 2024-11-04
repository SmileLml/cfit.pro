<?php
class changeModel extends model
{
    /**
     * Project: chengfangjinke
     * Method: getList
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 11:30
     * Desc: This is the code comment. This method is called getList.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $projectID
     * @param $browseType
     * @param $queryID
     * @param $orderBy
     * @param null $pager
     * @return mixed
     */
    public function getList($projectID, $browseType, $queryID, $orderBy, $pager = null)
    {
        $changeQuery = '';
        if($browseType == 'bysearch')
        {
            $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : '';
            if($query)
            {
                $this->session->set('changeQuery', $query->sql);
                $this->session->set('changeForm', $query->form);
            }

            if($this->session->changeQuery == false) $this->session->set('changeQuery', ' 1 = 1');

            $changeQuery = $this->session->changeQuery;
        }
        if(strpos($changeQuery,'project')){
            $changeQuery = str_replace('AND `project', ' AND `t2.name', $changeQuery);
            $changeQuery = str_replace('`', '', $changeQuery);
        }else{
            $changeQuery = str_replace('AND `', ' AND `t1.', $changeQuery);
            $changeQuery = str_replace('`', '', $changeQuery);
        }
        if(strpos($changeQuery,"t1.status = 'managersuccess'")){
            $changeQuery = str_replace("t1.status = 'managersuccess'", "t1.status in ('managersuccess','productmanagersuccess')", $changeQuery);
        }

        $statusSearch = [$browseType];
        if(in_array($browseType,['managersuccess','productmanagersuccess'])){
            $statusSearch = ['managersuccess','productmanagersuccess'];
        }
        $changes = $this->dao->select('t1.*,t2.name, GROUP_CONCAT(zn.id) as nodeIds')->from(TABLE_CHANGE)->alias('t1')
            ->leftJoin(TABLE_REVIEWNODE)->alias('zn')->on('zn.objectType = "change" and zn.objectID = t1.id and zn.version = t1.version and zn.status = "pending"')
            ->leftJoin(TABLE_PROJECTPLAN)->alias('t2')->ON('t1.project = t2.project')
            ->where('t1.project')->eq($projectID)
            ->andWhere('t1.status')->ne('deleted')
            ->beginIF($browseType != 'all' and $browseType != 'bysearch')->andWhere('t1.status')->in($statusSearch)->fi()
            ->beginIF($browseType == 'bysearch')->andWhere($changeQuery)->fi()
            ->groupBy('t1.id')
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');
        if(!$changes){
            return $changes;
        }
        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'change', $browseType != 'bysearch');

        $allNodeIds = [];
        foreach ($changes as $val){
            $nodeIds = $val->nodeIds ? explode(',', $val->nodeIds): [];
            $val->nodeIds = $nodeIds;
            if($nodeIds){
                $allNodeIds = array_merge($allNodeIds, $nodeIds);
            }
        }
        $exWhere = 'status = "pending"';
        $reviewerList =  $this->loadModel('review')->getReviewersByNodeIds($allNodeIds, $exWhere);

        // 部门
        $dmap = $this->dao->select('account,dept')->from(TABLE_USER)->fetchAll('account');

        $this->loadModel('review');
        foreach($changes as $key => $change)
        {
            $nodeIds = $change->nodeIds;
            $changes[$key]->reviewers    = '';
            $changes[$key]->appiontUsers = '';
            if($change->status == 'recall' || $change->status == 'waitcommit'|| $change->status == 'reject'){ //20222708 修复退回问题
                $changes[$key]->reviewers   = $change->createdBy;
            }else{
                if($nodeIds){
                    foreach ($nodeIds as $key1 => $nodeId){
                        $nodeReview = zget($reviewerList, $nodeId, []);
                        if (isset($nodeReview['reviews']) && !empty($nodeReview['reviews'])) {
                            if($key1 == 0){
                                $changes[$key]->reviewers = implode(',', $nodeReview['reviews']);
                            }else{
                                $changes[$key]->reviewers .= ','. implode(',', $nodeReview['reviews']);
                            }
                        }
                        if (isset($nodeReview['appointUsers']) && !empty($nodeReview['appointUsers'])) {
                            if($key1 == 0){
                                $changes[$key]->appiontUsers = implode(',', $nodeReview['appointUsers']);
                            }else{
                                $changes[$key]->appiontUsers .= ',' . implode(',', $nodeReview['appointUsers']);
                            }

                        }
                    }
                }
            }
            $changes[$key]->createdDept = isset($dmap[$change->createdBy]) ? $dmap[$change->createdBy]->dept : '';
        }
        return $changes;
    }


    /**
     * 指派经办人
     * @param $changeID
     * @return false|string
     */
    public function appoint($changeID){

        $change = $this->getByID($changeID);

        if(!in_array($change->status,['managersuccess','productmanagersuccess'])){
            dao::$errors[] = $this->lang->change->appointNotAllow;
            return false;
        }


        $reviewAccount = $this->loadModel("review")->getReviewByAccount('change', $changeID,$this->app->user->account, $change->version);
        if(!$reviewAccount){
            dao::$errors[] = $this->lang->change->reviewerEmpty;
            return false;
        }
        if($reviewAccount->reviewerType == 2){
            dao::$errors[] = $this->lang->change->appointNotAllow;
            return false;
        }

        if($reviewAccount->extra){
            $extra = json_decode($reviewAccount->extra);
            if(isset($extra->reviewrID) && $extra->reviewrID){
                dao::$errors[] = $this->lang->change->appointAlready;
                return false;
            }
        }
        $data = fixer::input('post')
            ->get();

        if(!isset($data->pointusers)){
            dao::$errors[] = $this->lang->change->reviewerEmpty;
            return false;
        }
        if(count($data->pointusers) == 1 && !$data->pointusers[0]){
            dao::$errors[] = $this->lang->change->reviewerEmpty;
            return false;
        }

        //查询已指派用户
        //->andWhere('reviewerType')->eq(2)
        $reviewerList = $this->dao->select("*")->from(TABLE_REVIEWER)->where('node')->eq($reviewAccount->id)->fetchAll();
        $reviewerUserList = array_column($reviewerList,'reviewer');
        if(array_intersect($reviewerUserList,$data->pointusers)){
            dao::$errors[] = $this->lang->change->appointPartAlready;
            return false;
        }
        //记录指派人的信息
        $reviewerExtra = [
            'sourceID'=>$reviewAccount->reviewerID,
            'sourceUser'=>$reviewAccount->reviewer,
        ];
        $reviewerExtra = json_encode($reviewerExtra);
        $reviewerIDArr = [];
        foreach ($data->pointusers as $key=>$user){

            if(!$user){
                continue;
            }
            //经办人信息
            $reviewGrade = $reviewAccount->grade+ $key +1;
            $reviewerData = [
                'node'=>$reviewAccount->id,
                'reviewer'=>$user,
                'status'=>'pending',
                'grade'=>$reviewGrade,
                'comment'=>'',
                'extra'=>$reviewerExtra,
                'createdBy'=>$this->app->user->account,
                'createdDate'=>date("Y-m-d",time()),
                'reviewerType'=>$this->lang->review->reviewerTypeListEnglish[2]
            ];
            $this->dao->insert(TABLE_REVIEWER)->data($reviewerData)->exec();
            $reviewerIDArr[] = $this->dao->lastInsertID();
        }


        return trim(implode(',',$data->pointusers),',');

    }
    public function getAppointUsers($changeID){
        $change = $this->getByID($changeID);

        $reviewAccount = $this->loadModel("review")->getReviewByAccount('change', $changeID,$this->app->user->account, $change->version);

        if($reviewAccount){
            return $this->loadModel("review")->getAppointUsers($reviewAccount->id);
        }else{
            return [];
        }

    }

    /**
     * Project: chengfangjinke
     * Method: getPairs
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 11:30
     * Desc: This is the code comment. This method is called getPairs.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $projectID
     * @return array
     */
    public function getPairs($projectID)
    {
        if(empty($projectID)) return array();
        $changes = $this->dao->select('id,code')->from(TABLE_CHANGE)
            ->where('project')->eq($projectID)
            ->andWhere('status')->ne('deleted')
            ->orderBy('id_desc')
            ->fetchPairs('id');

        return $changes;
    }

    /**
     * Project: chengfangjinke
     * Method: buildSearchForm
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 11:30
     * Desc: This is the code comment. This method is called buildSearchForm.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $queryID
     * @param $actionURL
     */
    public function buildSearchForm($queryID, $actionURL)
    {
        $this->config->change->search['actionURL'] = $actionURL;
        $this->config->change->search['queryID']   = $queryID;
        $this->config->change->search['params']['createdBy']['values'] = array('' => '') + $this->loadModel('user')->getPairs('noletter|noclosed');

        $this->loadModel('search')->setSearchParams($this->config->change->search);
    }

    /**
     * Project: chengfangjinke
     * Method: getReviewers
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 11:30
     * Desc: This is the code comment. This method is called getReviewers.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $projectID
     * @param $deptId
     * @param $changeInfo
     * @return array
     */
    public function getReviewers($projectID, $deptId = 0, $changeInfo = null)
    {
        $users = $this->loadModel('user')->getPairs('noletter|noclosed');
        $reviewers = array();
        if(!$deptId){
            $deptId = $this->app->user->dept;
        }
        $myDept = $this->loadModel('dept')->getByID($deptId);

        //质量部门QA(0)
        $qaUsers = explode(',', trim($myDept->qa, ','));
        $us  = array('' => '');
        foreach($qaUsers as $c)
        {
            $us[$c] = $users[$c];
        }
        $reviewers[$this->lang->change->reviewNodeCodeList['countersign']] = $us; //会签人员
        $reviewers[$this->lang->change->reviewNodeCodeList['qa']] = $us;
//        //主项目经理
//        $us  = array('' => '');
//        $reviewers[$this->lang->change->reviewNodeCodeList['masterProPm']] = $us;

        //项目经理(1)
        $project = $this->loadModel('project')->getByID($projectID);
        $reviewers[$this->lang->change->reviewNodeCodeList['pm']] = array('' => '', $project->PM => $users[$project->PM]);

        //部门负责人(2)
        $cms = explode(',', trim($myDept->manager1, ','));
        $us  = array('' => '');
        foreach($cms as $c)
        {
            $us[$c] = $users[$c];
        }
        $reviewers[$this->lang->change->reviewNodeCodeList['deptManage']] = $us;

        $this->app->loadLang('dept');
        //产创部门负责人(3)
        $productInnovateDeptId = $this->lang->dept->productInnovateDeptId;
        $productDept = $this->loadModel('dept')->getByID($productInnovateDeptId);

        $cms = explode(',', trim($productDept->manager1, ','));
        $us  = array('' => '');
        foreach($cms as $c)
        {
            $us[$c] = $users[$c];
        }
        $reviewers[$this->lang->change->reviewNodeCodeList['pdManage']] = $us;

        //架构部门负责人(4)
        $platformFrameworkDeptId = $this->lang->dept->platformFrameworkDeptId;
        $platformDept = $this->loadModel('dept')->getByID($platformFrameworkDeptId);
        $cms = explode(',', trim($platformDept->manager1, ','));
        $us  = array('' => '');
        foreach($cms as $c)
        {
            $us[$c] = $users[$c];
        }
        $reviewers[$this->lang->change->reviewNodeCodeList['frameworkManage']] = $us;

        // 部门分管领导(5)
        $cms = explode(',', trim($myDept->leader, ','));
        $us  = array('' => '');
        foreach($cms as $c)
        {
            $us[$c] = $users[$c];
        }
        $reviewers[$this->lang->change->reviewNodeCodeList['deptLeader']] = $us;

        //总经理和评审会主席(6)
        //$rs = $this->dao->select('account')->from(TABLE_USER)->where('role')->in('ceo,cto')->fetchAll();

        $projectPlanInfo = $this->loadModel('projectplan')->getProjectPlanInfo($projectID, 'bearDept');
        $bearDept = $projectPlanInfo->bearDept;
        $isIncludeCto = true;
        $rs = $this->loadModel('user')->getCeoUsers($bearDept, $isIncludeCto);
        $us = array('' => '');
        foreach($rs as $account)
        {
            $us[$account] = $users[$account];
        }
        $reviewers[$this->lang->change->reviewNodeCodeList['owner']] = $us;

        //归档资料处理人新建者(7)
        $us = array('' => '');
        if($changeInfo){
            $createdBy = $changeInfo->createdBy;
        }else{
            $createdBy = $this->app->user->account;
        }
        $us[$createdBy] = $users[$createdBy];
        $reviewers[$this->lang->change->reviewNodeCodeList['archive']] = $us;

        //质量部CM(8)
        $cmUsers = explode(',', trim($myDept->cm, ','));
        $us  = array('' => '');
        foreach($cmUsers as $c)
        {
            $us[$c] = $users[$c];
        }
        $reviewers[$this->lang->change->reviewNodeCodeList['baseline']] = $us;
        //质量部QA qaconfirm
        /*$qaUsers = explode(',', trim($myDept->qa, ','));
        $us  = array('' => '');
        foreach($qaUsers as $c)
        {
            $us[$c] = $users[$c];
        }
        $reviewers[$this->lang->change->reviewNodeCodeList['qaconfirm']] = $us;*/
        return $reviewers;
    }

    /**
     * 获得格式化的审核节点以及审核人信息
     *
     * @param $level
     * @param $nodes
     * @param array $skipReviewNode
     * @param array $requiredReviewerKeys
     * @return mixed
     */
    private function getFormatReviewNodes($level, $nodes, $requiredReviewerKeys, $skipReviewNode = []){
        $data = [];
        if(!($level && $nodes)){
            return $data;
        }
        foreach($nodes as $key => $currentNodes) {
            //去除空元素
            $currentNodes = array_filter($currentNodes);
            if(!empty($currentNodes)) {
                $nodes[$key] = array_values($currentNodes); //重新排序
            }
        }
        //不同等级必选字段
        //$requiredReviewerKeys = $this->lang->change->reviewLevelNodeCodeList[$level];
        $requiredReviewerKeys = array_diff($requiredReviewerKeys, $skipReviewNode);
        foreach ($nodes as $key => $currentNodes){
            if(in_array($key, $requiredReviewerKeys)){
                if($key == 'archive' && in_array('baseline', $requiredReviewerKeys)){ // 待归档时拼接CM
                    $currentNodes = array_filter(array_merge($currentNodes,$nodes['baseline']));
                }
                $data[$key] = $currentNodes;
            }
        }
        return $data;
    }

    /**
     *获得忽略节点
     *
     * @param $level
     * @param array $skipReviewNode
     * @return array
     */
    public function getFormatSkipReviewNode($level, $skipReviewNode = []){
        $data = [];
        if($level == 1){
            $data = $skipReviewNode;
        }
        return $data;
    }




    /**
     *提交审核人信息
     *
     * @param $changeID
     * @param $reviewers
     * @param $version
     * @return bool
     */
    private function submitReview($changeID,$reviewers, $version){
        $status = 'pending';
        $stage  = 1;
        $index  = 0;
        $extParams = [];
        foreach($reviewers as $key => $currentNodes) {
            $extParams['nodeCode'] = $key;
            if(!is_array($currentNodes)){
                $currentNodes = array($currentNodes);
            }
            $currentNodes = array_filter($currentNodes);
            if($index > 0){
                $status = 'wait';
                if(empty($currentNodes)){
                    $status = 'ignore';
                }
            }
            $this->loadModel('review')->addNode('change', $changeID, $version, $currentNodes, true, $status, $stage, $extParams);
            $stage++;
            $index++;
        }
        return true;
    }

    /**
     *提交编辑审核信息.
     *
     * @param $changeID
     * @param $version
     * @param $level
     * @param array $skipReviewNode
     * @return bool
     */
    public function submitEditReview($changeID, $version, $level, $skipReviewNode = []){
        //编辑后审核结点的审核人
        //$nodes = $this->post->nodes;
        $nodes = $_POST['nodes'];
       /* $change = $this->getByID($changeID);
        $nodes = explode(';',$change->reviewer);*/
        $requiredNodesData = $_POST['requiredNodes'];
        $requiredReviewerKeys = $this->getRequiredNodes($requiredNodesData);

        //获得格式化的审核节点以及审核人信息
        $nodes = $this->getFormatReviewNodes($level, $nodes, $requiredReviewerKeys, $skipReviewNode);

        $objectType = 'change';
        //原审核节点及审核人
        $oldNodes = $this->loadModel('review')->getNodes($objectType, $changeID, $version);

        $withGrade = true;
        foreach($nodes as $key => $currentReviews) {
            if (!is_array($currentReviews)) {
                $currentReviews = array($currentReviews);
            }
            $currentReviews = array_filter($currentReviews);
            //审核节点
            $oldNodeInfo = $oldNodes[$key];
            $oldReviewInfoList = $oldNodeInfo->reviewers;
            //原来节点审核人
            $oldReviews = [];
            if(!empty($oldReviewInfoList)){
                $oldReviews = array_column($oldReviewInfoList, 'reviewer');
            }
            //编辑前后当前节点审核人信息有变化
            $addReviews = array_diff($currentReviews, $oldReviews);
            $delReviews = array_diff($oldReviews, $currentReviews);
            if($addReviews || $delReviews){
                $nodeID = $oldNodeInfo->id;

                //删除审核节点原来审核人
                if(!empty($oldReviews)){
                    $oldIds = array_column($oldReviewInfoList, 'id');
                    $res = $this->loadModel('review')->delReviewers($oldIds);
                }

                //新增节点本次编辑设置的
                if(!empty($currentReviews)) {
                    $status = $oldNodeInfo->status;
                    if($oldNodeInfo->status == 'ignore'){
                        $status = 'wait';
                        $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq($status)->where('id')->eq($oldNodeInfo->id)->exec();
                    }
                    $res = $this->loadModel('review')->addNodeReviewers($nodeID, $currentReviews, $withGrade, $status);
                }else{
                    if($oldNodeInfo->status != 'ignore'){
                        $status = 'ignore';
                        $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq($status)->where('id')->eq($oldNodeInfo->id)->exec();
                    }
                }
            }else{
                if($currentReviews){
                    if($oldNodeInfo->status == 'ignore'){
                        $status = 'wait';
                        $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq($status)->where('id')->eq($oldNodeInfo->id)->exec();
                        $this->dao->update(TABLE_REVIEWER)->set('status')->eq($status)->where('node')->eq($oldNodeInfo->id)->exec();

                    }
                }
            }
        }
        return true;
    }

    /**
     * 获得必选节点
     *
     * @param $requiredNodesData
     * @return array
     */
    public function getRequiredNodes($requiredNodesData){
        $data = [];
        foreach ($requiredNodesData as $key => $val){
            if($val == 1){
                $data[] = $key;
            }
        }
        return $data;
    }

    /**
     * 检查变更基本信息
     *
     * @param $params
     * @return bool
     */
    public function checkBasicInfo($params){
        //检查结果
        $checkRes = false;
        if($params->category == 'plan'){
            if(!(isset($params->subCategory) && !empty($params->subCategory))){
                dao::$errors['subCategory'] = $this->lang->change->subCategoryEmpty;
                return $checkRes;
            }

        }
        if($params->isMasterPro == 1){
            if(!isset($params->mailUsers) || !$params->mailUsers){
                dao::$errors['mailUsers'] = $this->lang->change->mailUsersEmpty;
                return $checkRes;
            }
        }
        $checkRes = true;
        return $checkRes;
    }

    /**
     *检查审核节点的审核人
     *
     * @param $level
     * @param $nodes
     * @param $requiredReviewerKeys
     * @param array $skipReviewNode
     * @return false
     */
    public function checkReviewerNodesInfo($level, $nodes, $requiredReviewerKeys, $skipReviewNode = []){
        //检查结果
        $checkRes = false;
        if(!$level){
            dao::$errors[] = $this->lang->change->levelEmpty;
            return $checkRes;
        }
        if(!$nodes){
            dao::$errors[] = $this->lang->change->reviewerEmpty;
            return $checkRes;
        }
//        if(!isset($this->lang->change->reviewLevelRequiredNodeCodeList[$level])){
//            dao::$errors[] = $this->lang->change->levelError;
//            return $checkRes;
//        }

        //不同等级必选字段
        //$requiredReviewerKeys = $this->lang->change->reviewLevelRequiredNodeCodeList[$level];
        $nodeKeys = array();
        foreach($nodes as $key => $currentNodes) {
            //去除空元素
            $currentNodes = array_filter($currentNodes);
            if(!empty($currentNodes))
            {
                $nodeKeys[] = $key;
                $nodes[$key] = $currentNodes;
            }
        }
        //必选审核人，却没有选
        $requiredReviewerKeys = array_diff($requiredReviewerKeys, $skipReviewNode);
        $diffKeys = array_diff($requiredReviewerKeys, $nodeKeys);

        if(!empty($diffKeys)){
            foreach ($diffKeys as  $nodeKey){
                $reviewerNode = $this->lang->change->reviewNodeCodeDescList[$nodeKey];
                dao::$errors[] = $reviewerNode. $this->lang->change->reviewerEmpty;
            }
        }
        //评委会主席 单人 验证
        if(in_array('owner', $requiredReviewerKeys) && isset($nodes['owner']) && count($nodes['owner']) > 1){
            dao::$errors[] = $this->lang->change->reviewerNodeOwnerError ;
        }

        if(dao::isError()){
            return $checkRes;
        }
        $checkRes = true;
        return $checkRes;
    }


    /**
     * Project: chengfangjinke
     * Method: create
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 11:31
     * Desc: This is the code comment. This method is called create.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $projectID
     * @return mixed
     */
    public function create($projectID){
        $data = fixer::input('post')
            ->join('mailUsers', ',')
            ->join('subCategory', ',')
            ->remove('nodes,requiredNodes,skipReviewNode,uid,labels,files,consumed')
            ->stripTags($this->config->change->editor->create['id'], $this->config->allowedTags)
            ->get();
        $level = $this->post->level;
        $nodes = $this->post->nodes;
        $skipReviewNode = [];
        if($this->post->skipReviewNode){
            $skipReviewNode = $this->post->skipReviewNode;
        }
        $requiredNodesData = $this->post->requiredNodes;
        $requiredReviewerKeys = $this->getRequiredNodes($requiredNodesData);
        //检查变更基本信息
        $checkRes = $this->checkBasicInfo($data);
        if(!$checkRes){
            return dao::$errors;
        }
        //校验不同等级变更单的审核人信息
        $checkRes = $this->checkReviewerNodesInfo($level, $nodes, $requiredReviewerKeys, $skipReviewNode);
        if(!$checkRes){
            return dao::$errors;
        }
        $projectplantext = [];
        if($data->level == 1){
            if(!$data->innerprojectname){
                dao::$errors['innerprojectname'] = $this->lang->change->innerprojectnameError;
                return false;
            }
            if(!$data->projectowner){
                dao::$errors['projectowner'] = $this->lang->change->projectownerError;
                return false;
            }
            if(!$data->ownerphone){
                dao::$errors['ownerphone'] = $this->lang->change->ownerphoneError;
                return false;
            }
            $projectplantext = [
                'innerprojectname'=>$data->innerprojectname,
                'projectowner'=>$data->projectowner,
                'ownerphone'=>$data->ownerphone
            ];

        }
        if(isset($data->innerprojectname)){
            unset($data->innerprojectname);
        }
        if(isset($data->projectowner)){
            unset($data->projectowner);
        }
        if(isset($data->ownerphone)){
            unset($data->ownerphone);
        }
        $data->projectplantext = json_encode($projectplantext);
        //校验工作量信息
        /*$consumed = $this->post->consumed;
        $checkRes = $this->loadModel('consumed')->checkConsumedInfo($consumed);
        if(!$checkRes){
            return dao::$errors;
        }*/


        $postconsumed = 0.00;
        $data->project     = $projectID;
        $data->status      = 'waitcommit';
        $data->createdBy   = $this->app->user->account;
        $data->createdDate = helper::now();
        $data->createdDept = $this->app->user->dept;
        //$data->reviewStage = $data->level == 3 ? 0 : 1;
        $data->reviewStage = 0;

        //审核人
        $reviewers       = $this->getFormatReviewNodes($level, $nodes, $requiredReviewerKeys, $skipReviewNode);
        //忽略节点
        $skipReviewNodes = $this->getFormatSkipReviewNode($level, $skipReviewNode);
        // 评审人信息处理
        $data->reviewer       = json_encode($reviewers);
        $data->skipReviewNode = implode(',', $skipReviewNodes);
        $data = $this->loadModel('file')->processImgURL($data, $this->config->change->editor->create['id'], $this->post->uid);

        $this->dao->insert(TABLE_CHANGE)->data($data)->autoCheck()->batchCheck($this->config->change->create->requiredFields, 'notempty')->exec();
        $changeID = $this->dao->lastInsertId();
        if(!dao::isError()) {
            $number = $this->dao->select('count(id) c')->from(TABLE_CHANGE)->fetch('c');
            $code   = $this->dao->select('code')->from(TABLE_PROJECTPLAN)->where('project')->eq($projectID)->fetch('code');
            $mark   = $this->dao->select('mark')->from(TABLE_PROJECTPLAN)->where('project')->eq($projectID)->fetch('mark');
            $code   = $mark . '-CFIT-CMP0305-' . $code . '-' . sprintf('%03d', $number);

            $this->dao->update(TABLE_CHANGE)->set('code')->eq($code)->where('id')->eq($changeID)->exec();
            $this->loadModel('consumed')->record('change', $changeID, $postconsumed, $this->app->user->account, '', 'waitcommit', array());

            $this->loadModel('file')->saveUpload('change', $changeID);
            $this->file->updateObjectID($this->post->uid, $changeID, 'change');

            //添加项目变更白名单
            $allReviewers = $this->getAllReviewers($reviewers);
            if(!empty($allReviewers)){
                foreach ($allReviewers as $userAccount){
                    $res = $this->addProjectChangeWhitelist($projectID, $changeID, $userAccount);
                }
            }
        }

        return $changeID;
    }

    /**
     *获得所有的审核用户信息
     *
     * @param $nodesReviewers
     * @return array
     */
    public function getAllReviewers($nodesReviewers){
        $data = [];
        if(!$nodesReviewers){
            return $data;
        }
        foreach ($nodesReviewers as $currentNodeReviewers){
            $data = array_merge($data, $currentNodeReviewers);
        }
        return $data;
    }

    /**
     * 增加项目评审白名单
     *
     * @param $projectId
     * @params $reviewId
     * @param $userAccount
     * @return false|void
     */
    public function addProjectChangeWhitelist($projectId, $changeId, $userAccount){
        if(!($projectId && $userAccount)){
            return false;
        }
        $reason = 1003;
        //检查是否有项目权限
        $res = $this->loadModel('project')->checkOwnProjectPermission($projectId, $userAccount, $changeId, $reason);
        if($res){
            return true;
        }
        $res = $this->loadModel('project')->addProjectWhitelistInfo($projectId,  $userAccount, $changeId, $reason);
        return $res;
    }

    /**
     * Project: chengfangjinke
     * Method: update
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 11:31
     * Desc: This is the code comment. This method is called update.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $changeID
     * @return array
     */
    public function update($changeID)
    {
        $oldChange = $this->getByID($changeID);
        $nodes = $this->post->nodes;
        $requiredNodesData = $this->post->requiredNodes;
        $change = fixer::input('post')
            ->join('mailUsers', ',')
            ->join('subCategory', ',')
            ->remove('uid,files,labels,comment,nodes,requiredNodes,skipReviewNode')
            ->stripTags($this->config->change->editor->edit['id'], $this->config->allowedTags)
            ->get();
        $level = $change->level;

        $requiredReviewerKeys = $this->getRequiredNodes($requiredNodesData);
        $skipReviewNode = [];
        if($this->post->skipReviewNode){
            $skipReviewNode = $this->post->skipReviewNode;
        }
        //校验不同等级变更单的审核人信息
        $checkRes = $this->checkBasicInfo($change);
        if(!$checkRes){
            return dao::$errors;
        }
        $checkRes = $this->checkReviewerNodesInfo($level, $nodes, $requiredReviewerKeys, $skipReviewNode);
        if(!$checkRes){
            return dao::$errors;
        }
        $projectplantext = [];
        if($change->level == 1){
            if(!$change->innerprojectname){
                dao::$errors['innerprojectname'] = $this->lang->change->innerprojectnameError;
                return false;
            }
            if(!$change->projectowner){
                dao::$errors['projectowner'] = $this->lang->change->projectownerError;
                return false;
            }
            if(!$change->ownerphone){
                dao::$errors['ownerphone'] = $this->lang->change->ownerphoneError;
                return false;
            }
            $projectplantext = [
                'innerprojectname'=>$change->innerprojectname,
                'projectowner'=>$change->projectowner,
                'ownerphone'=>$change->ownerphone
            ];

        }
        if(isset($change->innerprojectname)){
            unset($change->innerprojectname);
        }
        if(isset($change->projectowner)){
            unset($change->projectowner);
        }
        if(isset($change->ownerphone)){
            unset($change->ownerphone);
        }
        $change->projectplantext = json_encode($projectplantext);

        //获得格式化的审核节点以及审核人信息
        //审核人
        $newReviewers    = $this->getFormatReviewNodes($level, $nodes, $requiredReviewerKeys, $skipReviewNode);
        //忽略节点
        $skipReviewNodes = $this->getFormatSkipReviewNode($level, $skipReviewNode);
        // 评审人信息处理
        $change->reviewer       = json_encode($newReviewers);
        $change->skipReviewNode = implode(',', $skipReviewNodes);

        //获得审核人信息变更
        $oldReviewers  = json_decode($oldChange->reviewer, true);
        $changeReviews = $this->getChangeReviewers($oldReviewers, $newReviewers, $change->level);

        if($oldChange->status == 'reject') { //驳回后修改
            $change->failedEdit      = '1';
            $change->version     = $oldChange->version + 1;
            $change->reviewStage = 0;
            $change->status      = 'waitcommit';//20222708 修复退回问题
        }else if($oldChange->status == 'recall'){
            $change->failedEdit      = '1';
            $change->version     = $oldChange->version + 1;
            $change->reviewStage = 0;
            $change->status      = 'waitcommit';
        }
        $change = $this->loadModel('file')->processImgURL($change, $this->config->change->editor->edit['id'], $this->post->uid);

        $this->dao->update(TABLE_CHANGE)->data($change)->autoCheck()
            ->batchCheck($this->config->change->edit->requiredFields, 'notempty')
            ->where('id')->eq($changeID)
            ->exec();

        $this->loadModel('file')->saveUpload('change', $changeID);
        $this->file->updateObjectID($this->post->uid, $changeID, 'change');
        //审核人信息变更
        $extChangeInfo = [];
        if($changeReviews){
            $extChangeInfo['review_node'] = $changeReviews;
        }
        //项目变更相关白名单
        $allReviewers = $this->getAllReviewers($newReviewers);
        if(!empty($allReviewers)){
            foreach ($allReviewers as $userAccount){
                $res = $this->addProjectChangeWhitelist($oldChange->project, $changeID, $userAccount);
            }
        }
        if(isset($change->status)){
            $postconsumed = 0.00;
            $this->loadModel('consumed')->record('change', $changeID, $postconsumed, $this->app->user->account, $oldChange->status, $change->status, array());
        }

        // 屏蔽评审人字段修改
        $change->reviewer = $oldChange->reviewer;
        return common::createChanges($oldChange, $change, $extChangeInfo);
    }

    /**
     * Project: chengfangjinke
     * Method: getByID
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 11:31
     * Desc: This is the code comment. This method is called getByID.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $changeID
     * @return mixed
     */
    public function getByID($changeID)
    {
        $change = $this->dao->select("*")->from(TABLE_CHANGE)->where('id')->eq($changeID)->fetch();
        $change = $this->loadModel('file')->replaceImgURL($change, 'reason,content,effect,result');

//        $change->reviewers = $this->loadModel('review')->getReviewer('change', $changeID, $change->version, $change->reviewStage);
        /*$reviewStage = [$change->reviewStage];
        if(in_array($change->reviewStage,[3,4])){
            $reviewStage = [3,4];
        }*/
        $nodeReview = $this->loadModel("review")->getMuiltNodeReviewer('change', $change->id, $change->version);

        $change->reviewers   = implode(',',$nodeReview['reviews']);
        $change->appiontUsers   = implode(',',$nodeReview['appointUsers']);

        $consumedList = $this->dao->select('*')->from(TABLE_CONSUMED)->where('objectType')->eq('change')
            ->andWhere('objectID')->eq($changeID)
            ->fetchAll();

        $files = $this->loadModel('file')->getByObject('change', $changeID);
        $change->consumed = $consumedList;
        $change->files    = $files;

        return $change;
    }

    /**
     *检查打基线信息
     *
     * @param $data
     * @return array
     */
    public function checkBaseLineInfo($data){
        $res = array(
            'result'  => false,
            'message' => '',
            'data'    => $data,
        );
        if(!$data){
            return $res;
        }
        $isReject     = $data->isReject; //是否退回
        $baselineType = $data->baseLineType;   //基线类型
        $baselinePath = $data->baseLinePath;  //基线路径
        if ($isReject == 2) { //需要打基线
            foreach ($baselineType as $key => $item) {
                if (!$baselinePath[$key] && $item) {
                    $message = sprintf($this->lang->change->baseLineTypeTip, $key + 1);
                    $res['message'] = $message;
                    if ($key + 1 != 1) {
                        dao::$errors['baseLinePath' . ($key + 1)] = $message;
                    }else{
                        dao::$errors['baseLinePath'] = $message;
                    }
                    return $res;
                }
                if (!$item && $baselinePath[$key]) {
                    $message = sprintf($this->lang->change->baseLineTypeTip, $key + 1);
                    $res['message'] = $message;
                    if ($key + 1 != 1) {
                        dao::$errors['baseLineType' . ($key + 1)] = $message;
                    }else{
                        dao::$errors['baseLineType'] = $message;
                    }
                    return $res;
                }

                //验证路径规则
                if (!empty($baselinePath[$key])) {
                    // $flag =  preg_match("/^(?!_)([0-9a-zA-Z_:\x80-\xff.\/]{0,})(?<!_)$/", $baselinePath[$key]);
                    $pathFinal = explode('/', $baselinePath[$key]);
                    krsort($pathFinal);
                    $checkPath = array_values($pathFinal)[0];
                    $count = substr_count($checkPath, '_');
                    // if((($flag != '1' ) || $count != 4) || (($flag == '1' ) && $count != 4)){
                    if ($count != 4) {
                        $message = sprintf($this->lang->change->baseLinePathError, $key + 1);
                        $res['message'] = $message;
                        if ($key + 1 != 1) {
                            dao::$errors['baseLinePath' . ($key + 1)] = $message;
                        }else{
                            dao::$errors['baseLinePath'] = $message;
                        }
                        return $res;
                    }
                }
            }
            //基线时间
            if (isset($_POST['baseLineTime']) && empty($_POST['baseLineTime'])) {
                $message =  $this->lang->change->timeEmpty;
                dao::$errors['baseLineTime'] = $message;
                return $res;
            }

            if (array_filter($baselineType) && array_filter($baselinePath)) {
                $data->baseLineCondition = 'yes';//已打基线
                $data->baseLineType = implode(',', array_filter($baselineType));
                $data->baseLinePath = implode(',', array_filter($baselinePath));
            }else{
                $data->baseLineCondition = 'no' ;//未打基线
                $data->baseLineType = '';
                $data->baseLinePath = '';
            }
            $data->result = 'pass';
        } else {
            $data->baseLineCondition = '' ;//未打基线
            $data->baseLineType = '';
            $data->baseLinePath = '';
            $data->result = 'reject';
            $data->baseLineTime = '';
        }

        //返回
        $res['result'] = true;
        $res['data']  = $data;
        return $res;
    }

    /**
     *检查归档信息
     *
     * @param $data
     * @return mixed
     */
    public function checkArchiveInfo($data){
        $res = array(
            'result'  => false,
            'message' => '',
            'data'    => $data,
        );
        $svnUrl     = $data->svnUrl;
        $svnVersion = $data->svnVersion;
        $svnUrlArray     = [];
        $svnVersionArray = [];
        foreach ($svnUrl as $key => $item) {
            $item = trim($item);
            $currentSvnVersion = trim($svnVersion[$key]);
            $sortKey = $key + 1;
            if (!$currentSvnVersion && $item ) {
                $message = sprintf($this->lang->change->svnUrlVersionErrorTip, $sortKey);
                $res['message'] = $message;
                dao::$errors['svnVersion' . $sortKey] = $message;
                return $res;
            }
            if (!$item && $currentSvnVersion) {
                $message = sprintf($this->lang->change->svnUrlVersionErrorTip, $sortKey);
                $res['message'] = $message;
                dao::$errors['svnUrl' . $sortKey] = $message;
                return $res;
            }

            $maxStrLen = 255;
            if(mb_strlen($currentSvnVersion) > $maxStrLen){
                $message = sprintf($this->lang->change->svnVersionLenErrorTip, $sortKey, $maxStrLen);
                $res['message'] = $message;
                dao::$errors['svnVersion' . $sortKey] = $message;
                return $res;
            }

            //同时为空
            if(!$item && !$currentSvnVersion){
                if ($sortKey == 1) {
                    $message = $this->lang->change->svnUrlVersionEmptyTip;
                }else{
                    $message = sprintf($this->lang->change->svnUrlVersionBothEmptyTip, $sortKey);
                }
                dao::$errors['svnUrl' . $sortKey] = $message;
                $res['message'] = $message;
                return $res;
            }
            $svnUrlArray[]     = $item;
            $svnVersionArray[] = $currentSvnVersion;
        }

        //查询是否有一条记录
        if (empty($svnUrlArray)){
            $message = $this->lang->change->svnUrlVersionEmptyTip;
            dao::$errors['svnUrl1'] = $message;
            $res['message'] = $message;
            return $res;
        }
        //返回
        $res['result'] = true;
        $data->svnUrl     = $svnUrlArray;
        $data->svnVersion = $svnVersionArray;
        $data->result = 'pass'; //默认审核
        $res['data'] = $data;
        return $res;
    }

    /**
     * Project: chengfangjinke
     * Method: review
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 11:31
     * Desc: This is the code comment. This method is called review.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $changeID
     * @return false|void
     */
    public function review($changeID){
        $data = fixer::input('post')->get();
        $data = $this->loadModel('file')->processImgURL($data, $this->config->change->editor->review['id'], $this->post->uid);
        $change = $this->getByID($changeID);
        $oldStatus = $change->status;
        $step         = 1;
        $extra        = new stdClass();
        $reviewResult = $this->post->result;
        if($reviewResult == 'reject' || (isset($data->isReject) && $data->isReject == 1)){
            if(!$this->post->comment){
                dao::$errors['comment'] = $this->lang->change->commentNotEmpty;
                return false;
            }
        }

        //判断当前状态和审核中传入的状态是否一致，以防有同组中其他人修改
        $res = $this->checkAllowReview($change, $this->post->version, $this->post->status, $this->app->user->account);
        if(!$res['result']){
            dao::$errors['statusError'] = $res['message'];
            return false;
        }
        if ($change->status == $this->lang->change->statusArray['archive']){ //归档
            $checkRes = $this->checkArchiveInfo($data);
            if(!$checkRes['result']){
                return false;
            }
            $data = $checkRes['data'];
            $reviewResult = $data->result; //评审结果
        }elseif ($change->status == $this->lang->change->statusArray['gmsuccess']) { //打基线
            $checkRes = $this->checkBaseLineInfo($data);
            if(!$checkRes['result']){
                return false;
            }
            $data = $checkRes['data'];
            $reviewResult = $data->result; //评审结果
        }else{
            if(($change->status == $this->lang->change->statusArray['cmconfirmed']) &&  ($change->level == 2)) {
            $this->post->isNeedLeader = isset($this->post->isNeedLeader) ? $this->post->isNeedLeader : $this->post->isleader;
                if(!$this->post->isNeedLeader) {
                    dao::$errors['isNeedLeader'] = $this->lang->change->leaderEmpty;
                    return false;
                }
                if($this->post->isNeedLeader == 'yes'){
                    $extra = true;
                }else{
                    $step = 2;
                    $extra = false;
                }
            }
        }

        //审批结果
        if (!$reviewResult) {
            dao::$errors['result'] = $this->lang->change->checkReviewList['resultError'];
            return false;
        }
        //是否指派人操作
        $isAppointUsersOp = false;
        if(in_array($this->app->user->account, $res['appointUsers'])){
            $isAppointUsersOp = true;
        }
        if($isAppointUsersOp){
            $updata = [
                'comment' => $this->post->comment,
                'reviewTime' => helper::now()
            ];
            $this->dao->update(TABLE_REVIEWER)->data($updata)->where('reviewer')->eq($this->app->user->account)->andWhere('status')->eq('pending')->andWhere('reviewerType')->eq(2)->exec();
            return true;
        }

        $is_all_check_pass = false;
        $nodeCode = $this->getReviewNodeCodeByStatus($change->status);
        if(in_array($nodeCode, $this->lang->change->needAllUserCheckNodeCodeList)){
            //部分审核
            $is_all_check_pass = true;
        }

        $reviewAccount = $this->loadModel("review")->getReviewByAccount('change', $changeID, $this->app->user->account, $change->version);
        if($reviewAccount->extra2){
            $extra = json_decode($reviewAccount->extra2);
        }
        //审核过程操作
        $opResult = $this->loadModel('review')->check('change', $changeID, $change->version, $reviewResult, $this->post->comment, $reviewAccount->stage, $extra, $is_all_check_pass);

        //审核后下一状态信息
        $reviewNextInfo = $this->getReviewNextInfo($change, $reviewResult, $step);
        $nextStatus  = $reviewNextInfo['nextStatus'];
        $version     = $reviewNextInfo['version'];
        $reviewStage = $reviewNextInfo['reviewStage'];
        if(!$nextStatus){
            $statusDesc = zget($this->lang->change->statusList, $change->status);
            dao::$errors[''] = sprintf($this->lang->change->checkReviewList['statusError'], $statusDesc);
            return false;
        }
        //更新主表
        $updateParams = new stdClass();
        $updateParams->status      = $nextStatus;
        $updateParams->version     = $version;
        $updateParams->reviewStage = $reviewStage;
        if ($oldStatus ==  $this->lang->change->statusArray['gmsuccess']){ //待打基线
            $updateParams->baseLineCondition =  $data->baseLineCondition;
            $updateParams->baseLineType =  $data->baseLineType;
            $updateParams->baseLinePath =  $data->baseLinePath;
            $updateParams->baseLineTime = $data->baseLineTime;
            $updateParams->addBaseLineTime = helper::now();
        }

        //更新变更表
        $this->dao->update(TABLE_CHANGE)->data($updateParams)->autoCheck()
            ->where('id')->eq($changeID)
            ->exec();


        //工作量
        $postconsumed = 0.00;
        //工时
        $consumedExtra = new stdClass();
        if($oldStatus == $this->lang->change->statusArray['gmsuccess']){ //打基线操作
            $consumedExtra->isReject = $data->isReject; //是否退回
            $consumedExtra->baseLineCondition = $data->baseLineCondition; //是否打基线
        }
        $this->loadModel('consumed')->record('change', $changeID, $postconsumed, $this->app->user->account, $change->status, $nextStatus, array(), $consumedExtra);

        //修改后变更信息
        $newChange = $this->getByID($changeID);
        if($change->version != $version){
            //增加新版本信息
            $res = $this->addNewVersionReviewNodes($newChange);
        }

        if($opResult == 'pass'){ //审核通过
            //如果是 一级变更 并且是  评审主席审批  更新年度计划和项目空间和年度计划立项书
            if($change->level == 1 && $reviewAccount->nodeCode == 'owner'){
                // 名称 涉及  年度计划，项目立项书，项目空间
                // 项目经理  年度计划，项目立项书，项目空间
                // 项目负责人联系方式 年度计划
                $projecttext = json_decode($change->projectplantext);
                //项目有值 且 json解析正确 执行此操作
                if($change->projectplantext && $projecttext){
                    $projectplaninfo = $this->loadModel('projectplan')->getPlanMainInfoByProjectID($change->project,'id,owner,phone,name');
                    //年度计划
                    $updataProjectplan = [
                        'name'=>$projecttext->innerprojectname,
                        'owner'=>$projecttext->projectowner,
                        'phone'=>$projecttext->ownerphone,
                    ];
                    $this->dao->update(TABLE_PROJECTPLAN)->data($updataProjectplan)->where('project')->eq($change->project)->exec();

                    $projectplancomment = "";
                    if($projecttext->innerprojectname != $projectplaninfo->name || $projecttext->projectowner != $projectplaninfo->owner || $projecttext->ownerphone != $projectplaninfo->phone){
                        $projectplancomment = "{$this->lang->change->changeIDStr}:{$change->id}<br />";
                        if($projecttext->innerprojectname != $projectplaninfo->name){
                            $projectplancomment .= "{$this->lang->change->innerprojectnameStr} :{$projectplaninfo->name} -&gt; {$projecttext->innerprojectname}<br />";
                        }
                        if($projecttext->projectowner != $projectplaninfo->owner){
                            $projectplancomment .= " {$this->lang->change->ownerStr}:{$projectplaninfo->owner}-&gt;{$projecttext->projectowner}<br />";
                        }
                        if($projecttext->ownerphone != $projectplaninfo->phone){
                            $projectplancomment .= " {$this->lang->change->ownerphoneStr}:{$projectplaninfo->phone}-&gt;{$projecttext->ownerphone}<br />";
                        }
                        $this->loadModel('action')->siampleCreate('projectplan', $projectplaninfo->id, 'projectchangeupdate',$projectplancomment,'',$change->createdBy);
                    }

                    //立项书
                    $updataprojectcreation = [
                        'name'=>$projecttext->innerprojectname,
                        'PM'=>$projecttext->projectowner,
                    ];

                    $this->dao->update(TABLE_PROJECTCREATION)->data($updataprojectcreation)->where('plan')->eq($projectplaninfo->id)->exec();
                    $projectinfo = $this->dao->select('id,PM')->from(TABLE_PROJECT)->where('id')->eq($change->project)->fetch();
                    //处理原项目经理身份
                    $oldPM = $this->dao->select("id,role")->from(TABLE_TEAM)->where('root')->eq($change->project)->andWhere('type')->eq('project')->andWhere('account')->eq($projectinfo->PM)->fetch();
                    if($oldPM){
                        $oldrole = explode(',',$oldPM->role);
                        $okey = array_search(2,$oldrole);
                        if($okey !== false){
                            unset($oldrole[$okey]);
                        }
                        if(!in_array(6,$oldrole)){
                            $oldrole[] = 6;
                        }
                        $this->dao->update(TABLE_TEAM)->data(['role'=>implode(',',$oldrole)])->where('id')->eq($oldPM->id)->exec();

                    }
                    $teamisexist = $this->dao->select("id,role")->from(TABLE_TEAM)->where('root')->eq($change->project)->andWhere('type')->eq('project')->andWhere('account')->eq($projecttext->projectowner)->fetch();
                    if(!$teamisexist){
                        //添加团队成员
                        $team = new stdclass();
                        $team->root = $change->project;
                        $team->type = 'project';
                        $team->account = $projecttext->projectowner;
                        $team->position = '';
                        $team->join = date('Y-m-d');
                        $team->days = 5;
                        $team->role = 2;
                        $team->hours = 7.0;
                        $this->dao->insert(TABLE_TEAM)->data($team)->exec();
                        $this->loadModel('user')->updateUserView([$change->project], 'project', [$projecttext->projectowner]);
                    }else{
                        $newrole = explode(',',$teamisexist->role);

                        if(!in_array(2,$newrole)){
                            $newrole[] = 2;
                        }
                        $this->dao->update(TABLE_TEAM)->data(['role'=>implode(',',$newrole)])->where('id')->eq($oldPM->id)->exec();
                        $this->loadModel('user')->updateUserView([$change->project], 'project', [$projecttext->projectowner]);
                    }
                    //项目空间
                    $updataproject = [
                        'name'=>$projecttext->innerprojectname,
                        'PM'=>$projecttext->projectowner,
                    ];
                    $this->dao->update(TABLE_PROJECT)->data($updataproject)->where('id')->eq($change->project)->exec();


                }

            }
            //如果审核了，还有 正在处理中的节点，则不增加新的审核中节点
            $pendingNode = $this->dao->select('id,nodeCode,status')->from(TABLE_REVIEWNODE)->where('objectType')->eq('change')
                ->andWhere('objectID')->eq($changeID)
                ->andWhere('version')->eq($change->version)
                ->andWhere('status')->eq('pending')->orderBy('stage,id')->fetch();
            if(!$pendingNode){
                $next = $this->dao->select('id,nodeCode,status')->from(TABLE_REVIEWNODE)->where('objectType')->eq('change')
                    ->andWhere('objectID')->eq($changeID)
                    ->andWhere('version')->eq($change->version)
                    ->andWhere('status')->eq('wait')->orderBy('stage,id')->fetch();
                if($next) {
                   $this->loadModel('review')->setReviewNodePending($next->id);
                    //如果是产创部 则 将架构部也置为 审核中状态。
                    if($next->nodeCode == $this->lang->change->reviewNodeCodeList['pdManage']){
                        $frameworkManageNode = $this->loadModel('review')->getNodeByNodeCode('change', $changeID, $change->version, $this->lang->change->reviewNodeCodeList['frameworkManage']);
                        if($frameworkManageNode){
                            $this->loadModel('review')->setReviewNodePending($frameworkManageNode->id);
                        }
                    }
                }
            }

        }

        if($oldStatus == $this->lang->change->statusArray['archive']){ //归档
            $oldArchive =  $this->loadModel('archive')->getArchiveAllList($change->project, 'change', $changeID);
            $old = '';
            if($oldArchive){
                $this->loadModel('archive')->deleteAll(array_column($oldArchive,'id'));
                foreach ($oldArchive as $item) {
                    $oldUrl = $item->svnUrl;
                    $oldVersion = $item->svnVersion;
                    $old .= sprintf($this->lang->change->svnEditTips,$oldUrl,$oldVersion) ;
                }
            }
            $svnUrlArray     = $data->svnUrl;
            $svnVersionArray = $data->svnVersion;
            $new = '';
            foreach($svnUrlArray as $key => $svnUrl){
                $archiveParams = new stdClass();
                $archiveParams->svnUrl = $svnUrl;
                $archiveParams->svnVersion = $svnVersionArray[$key];
                $this->loadModel('archive')->addArchiveInfo($change->project, 'change', $changeID, $change->version, $archiveParams);
                $new .= sprintf($this->lang->change->svnEditTips,$svnUrl,$svnVersionArray[$key]);
            }
            if( $svnUrlArray ){
                $this->loadModel('action')->create('change', $changeID, 'svnedited', ($old ? '原：<br>'.$old.'<br>' :'').'新：<br>'.$new, $extra);
            }
        }elseif ($oldStatus ==  $this->lang->change->statusArray['gmsuccess']){ //待打基线
            if($data->baseLineCondition == 'yes'){
                $this->addBaseLine($changeID);
            }
        }
        //审核结束, 删除白名单
        $status = $newChange->status;
        if($status == 'success' || $status == 'reject'){
            $reason = 1003;
            $this->loadModel('review')->deleteWhiteList($changeID, $reason);
        }
        //获得差异信息
        $extChangeInfo = [];

        //审核结果
        $ext = new stdClass();
        $ext->old = '';
        $ext->new = $reviewResult;
        $extChangeInfo['reviewResult'] = $ext;
        return common::createChanges($change, $updateParams, $extChangeInfo);
    }

    /**
     *获得下一步处理人
     *
     * @param $changeInfo
     * @param $nextStatus
     * @param string $postUser
     * @return string
     */
    public function getNextDealUser($changeInfo, $nextStatus, $postUser = '',$oldversion=1){
        $nextDealUser = '';
        if($postUser){
            $nextDealUser = $postUser;
        }else{
            switch ($nextStatus){
                case $this->lang->change->statusArray['archive']: //目前只有待规档需要新增版本
                    $reviewer = json_decode($changeInfo->reviewer); // 待归档增加CM
                    $nextDealUser = isset($reviewer->archive) && $reviewer->archive ? trim(implode(',',$reviewer->archive),',') : $changeInfo->createdBy;//$changeInfo->createdBy;
                    break;
                case $this->lang->change->statusArray['gmsuccess']: //cms打基线


                    $nextDealUser = $this->loadModel('review')->getLastPendingPeople('change',$changeInfo->id,$oldversion,$changeInfo->reviewStage);

                    break;
                default:
                    break;
            }
        }
        return $nextDealUser;
    }

    /**
     *新增审批新版本的审核节点
     *
     * @param $changeInfo
     * @param $oldStatus
     * @return false|void
     */
    public function addNewVersionReviewNodes($changeInfo){
        $res = false;
        if(!$changeInfo){
            return $res;
        }
        $changeID = $changeInfo->id;
        $status   = $changeInfo->status;
        $version  = $changeInfo->version;
        //节点标识
        $nodeCode = $this->getReviewNodeCodeByStatus($status);
        $objectType = 'change';

        //获得历史版本
        $oldVersion =  $this->loadModel('review')->getObjectReviewNodeMaxVersion($changeID, $objectType);
        $stage = $this->loadModel('review')->getNodeStage($objectType, $changeID, $oldVersion, $nodeCode);
        if(!$stage){
            $maxStage = $this->loadModel('review')->getReviewMaxStage($changeID, $objectType, $oldVersion);
            $stage = $maxStage + 1;
        }

        $dealUsers = $this->getNextDealUser($changeInfo, $status,'',$oldVersion);
        if(!is_array($dealUsers)){
            $dealUsers = explode(',', $dealUsers);
        }

        //历史节点你信息补全
        $historyReviews = $this->loadModel('review')->getHistoryReviewers($objectType, $changeID, $oldVersion, $stage);

        if(!empty($historyReviews)){
            foreach ($historyReviews as $currentNodeInfo){
                $currentNodeReviewers = $currentNodeInfo->reviewers;
                unset($currentNodeInfo->reviewers);
                unset($currentNodeInfo->id);
                $currentNodeInfo->version = $version;
                //新增审核节点
                $this->dao->insert(TABLE_REVIEWNODE)->data($currentNodeInfo)->exec();
                $newNodeID = $this->dao->lastInsertID();


                foreach ($currentNodeReviewers as $currentNodeReviewer){
                    $currentNodeReviewer->node = $newNodeID;

                    unset($currentNodeReviewer->id);
                    if($currentNodeReviewer->reviewTime === null || $currentNodeReviewer->reviewTime <= 0){
                        unset($currentNodeReviewer->reviewTime);
                    }

                    $this->dao->insert(TABLE_REVIEWER)->data($currentNodeReviewer)->exec();
                }
            }
        }



        //增加当前审核节点
        $nodes = array(
            array(
                'reviewers' => $dealUsers,
                'stage'     => $stage,
                'nodeCode'  => $nodeCode,
            )
        );




        //新增的本次节点
        $this->loadModel('review')->submitReview($changeID, $objectType, $version, $nodes);

        //新增未审核节点
        $waitReviews = $this->loadModel('review')->geWaitReviewers($objectType, $changeID, $oldVersion, $stage);

        if(!empty($waitReviews)){
            foreach ($waitReviews as $currentNodeInfo){
                $status = 'wait';
                $currentNodeReviewers = $currentNodeInfo->reviewers;
                unset($currentNodeInfo->reviewers);
                unset($currentNodeInfo->id);
                $currentNodeInfo->version = $version;
                if($currentNodeInfo->status == 'ignore'){
                    $status = 'ignore';
                }
                $currentNodeInfo->status = $status;
                //新增审核节点
                $this->dao->insert(TABLE_REVIEWNODE)->data($currentNodeInfo)->exec();
                $newNodeID = $this->dao->lastInsertID();
                foreach ($currentNodeReviewers as $currentNodeReviewer){
                    $currentNodeReviewer->node = $newNodeID;
                    unset($currentNodeReviewer->id);
                    $currentNodeReviewer->status = $status;
                    $currentNodeReviewer->comment = '';

                    unset($currentNodeReviewer->reviewTime);
                    $currentNodeReviewer->extra = '';
                    $this->dao->insert(TABLE_REVIEWER)->data($currentNodeReviewer)->exec();
                }
            }
        }

        return true;
    }

    /**
     *获得审核后的下一节点信息
     *
     * @param $changeInfo
     * @param $reviewResult
     * @param int $step
     * @return array
     */
    public function getReviewNextInfo($changeInfo, $reviewResult, $step = 1){
        $data = [
            'nextStatus'  => '',
            'version'     => $changeInfo->version,
            'reviewStage' => $changeInfo->reviewStage + $step
        ];
        $oldStatus   = $changeInfo->status;
        $changeId    = $changeInfo->id;
        $objectType  = 'change';
        if($reviewResult == 'reject'){
            if($oldStatus == $this->lang->change->statusArray['gmsuccess']){ //当前状态是待打基线
                $data['nextStatus']  = $this->lang->change->statusArray['archive']; //返回到归档信息
                $data['version']     = $changeInfo->version + 1; //版本加1
                $data['reviewStage'] = $changeInfo->reviewStage - 1 ;
            }else if($oldStatus == $this->lang->change->statusArray['qaconfirmsuccess']){ //当前状态是待打基线
                $data['nextStatus']  = $this->lang->change->statusArray['gmsuccess']; //返回到归档信息
                $data['version']     = $changeInfo->version + 1; //版本加1
                $data['reviewStage'] = $changeInfo->reviewStage - 1 ;
            }else{
                $data['nextStatus'] = $this->lang->change->statusArray['reject']; //返回到退回
                $data['reviewStage'] = $changeInfo->reviewStage;
            }
        }elseif($reviewResult == 'part'){ //部分审核状态不变
            $data['nextStatus']  = $changeInfo->status;
            $data['reviewStage'] = $changeInfo->stage;
        }else{
            //忽略信息数
            $ignoreCount = $step - 1;
            $unReviewNodeList = $this->loadModel('review')->getUnReviewNodeList($objectType, $changeId, $changeInfo->version, '*', $step);
            $unReviewNodeCount = count($unReviewNodeList);
            if($unReviewNodeCount == 0){ //未查询到需要审核的节点
                $data['nextStatus'] = $this->lang->change->statusArray['success'];
            }else{
                if($ignoreCount > 0){
                    //忽略节点
                    $ignoreReviewNodeList = array_splice($unReviewNodeList, 0, $ignoreCount);
                    $ignoreNodeIds = array_column($ignoreReviewNodeList, 'id');
                    $this->loadModel('review')->ignoreReviewNodeAndReviewers($ignoreNodeIds);
                }
                //获得要审核的节点
                $nextReviewNodeInfo = $unReviewNodeList[0];
                if($nextReviewNodeInfo){
                    //下一审核节点
                    $nextNodeCode = $nextReviewNodeInfo->nodeCode;
                    //下一状态
                    $nextStatus = $this->getNextStatusByNextNodeCode($nextNodeCode);
                    if($changeInfo->level == 1 && $nextStatus == $this->lang->change->statusArray['frameworkmanagersuccess']){ //待分管领导审批
                        //判断分管领导和评审主席是否一样
                        $leaderReviewersList = $this->loadModel('review')->getReviewerListByNodeCode($objectType, $changeId, $changeInfo->version, $nextNodeCode);

                        if(!empty($leaderReviewersList)){
                            $leaderReviewers = array_column($leaderReviewersList, 'reviewer');
                            $ownerNodeCode  = $this->lang->change->reviewNodeCodeList['owner'];
                            $ownerReviewersList = $this->loadModel('review')->getReviewerListByNodeCode($objectType, $changeId, $changeInfo->version, $ownerNodeCode);
                            $ownerReviewers = array_column($ownerReviewersList, 'reviewer');
                            if(empty(array_diff($leaderReviewers, $ownerReviewers)) && empty(array_diff($ownerReviewers, $leaderReviewers))){
                                $this->loadModel('review')->ignoreReviewNodeAndReviewers(array($nextReviewNodeInfo->id));
                                $unReviewNodeList = $this->loadModel('review')->getUnReviewNodeList($objectType, $changeId, $changeInfo->version);
                                $nextReviewNodeInfo = $unReviewNodeList[0];
                                if($nextReviewNodeInfo){
                                    //下一审核节点
                                    $nextNodeCode = $nextReviewNodeInfo->nodeCode;
                                    //下一状态
                                    $nextStatus = $this->getNextStatusByNextNodeCode($nextNodeCode);
                                    $data['nextStatus'] = $nextStatus;
                                    $data['reviewStage'] = $nextReviewNodeInfo->stage;
                                }else{
                                    $nextStatus = '';
                                }
                            }
                        }
                    }
                    $data['nextStatus'] = $nextStatus;
                    $data['reviewStage'] = $nextReviewNodeInfo->stage;
                }else{
                    $data['nextStatus'] = '';
                }
            }
        }
        return $data;
    }

    /**
     * 根据nextNodeCode获得下一个状态
     *
     * @param $nextNodeCode
     * @return string
     */
    public function getNextStatusByNextNodeCode($nextNodeCode){
        $nextStatus = '';
        if(!$nextNodeCode){
            return $nextStatus;
        }
        $reviewStatusNodeCodeMapList = $this->lang->change->reviewStatusNodeCodeMapList;
        $reviewNodeCodeStatusMapList = array_flip($reviewStatusNodeCodeMapList);
        //下一状态
        $nextStatus = zget($reviewNodeCodeStatusMapList, $nextNodeCode);
        return $nextStatus;

    }

    /**
     * 基线情况：打基线 相关数据入库baseline
     * @param $changeID
     */
    public function addBaseLine($changeID)
    {
        $change = $this->getByID($changeID);
        $baselinetype = explode(',',$change->baseLineType);
        $baselinePath =  explode(',',$change->baseLinePath);
//        $member =  $this->loadModel('project')->getTeamMembers($change->project);//团队
//        $member = array_column($member,'role','account');
        $file = $this->loadModel('file')->getByObject('change', $change->id);//附件

        $proj = $this->loadModel('project')->getByID($change->project);
        foreach ($baselinetype as $key => $item) {
            $title = $this->cut_str($baselinePath[$key],"/",-1); //取最后

            $mark = $this->dao->select('t2.mark')->from(TABLE_CHANGE)->alias('t1')->leftJoin(TABLE_PROJECTPLAN)->alias('t2')->on('t2.project = t1.project')->where('t1.id')->eq($changeID)->fetch();

            $baseline = new stdClass();
            $baseline->title = $title;
            $baseline->type  = $item;
            $baseline->cm = $this->app->user->account;
            $baseline->cmDate = $change->baseLineTime;
            $baseline->reviewer = $this->lang->change->reviewerName;
            $baseline->reviewedDate = helper::today();
            $baseline->project = $change->project;
            $baseline->objectType = 'change';
            $baseline->objectID   = $changeID;
            $baseline->version    = $change->version;
            $baseline->createdDate = helper::today();
            $baseline->createdBy = $this->app->user->account;

            $project = new stdclass();
            /*foreach ($member as $k=>$value) {
                $val = explode(',',$value);
                if(in_array('2',$val)){
                    $PM = $k;//项目经理2
                }
                if (in_array('11',$val)){
                    $QA = $k;//质量保证工程师11
                }
                if (in_array('1',$val)){
                    $PO = $k;//项目主管1
                }
            }
            $project->PM =  !empty($PM) ? $PM : $proj->PM;//项目经理2
            $project->QA =  !empty($QA) ? $QA : $proj->QA;//质量保证工程师11
            $project->PO =  !empty($PO) ? $PO : $proj->PO;//项目主管1
            */
            $project->code = isset($mark) ? $mark->mark : '';//空

            $pathFinal = explode('/',$baselinePath[$key]);
            krsort($pathFinal);
            $checkPath = array_values($pathFinal)[0];
            $item = new stdClass();
            $item->title = !empty($file) ? implode(',',array_column($file,'title')) : '无';//附件名称
            $item->code = '';//空
            $item->version = $this->cut_str($checkPath,'_',3);
            $item->changed = '1';
            $item->changedID = $change->code;
            $item->changedDate = helper::today();
            $item->path = $baselinePath[$key];
            $item->comment = $this->lang->change->commentDesc;

            //存基线表
            $this->dao->insert(TABLE_BASELINE)->data($baseline)->autoCheck()->exec();
            $baselineID = $this->dao->lastInsertID();

            //存配置表
            $item->baseline = $baselineID;
            $this->dao->insert(TABLE_CMITEM)->data($item)->exec();

            //更新项目表
            $this->dao->update(TABLE_PROJECT)->data($project)->where('id')->eq($change->project)->exec();
        }
    }

    /**
     * 按符号截取字符串
     * @param string $str 需要截取字符串
     * @param string $sign 符号
     * @param int $number 正数从左向右，负数从右向左
     * @return string ·返回
     */
    function cut_str($str,$sign,$number){
        $array  = explode($sign, $str);
        $length = count($array);
        if($number < 0){
            $new_array = array_reverse($array);
            $abs_number = abs($number);
            if($abs_number <= $length){
                return $new_array[$abs_number-1];
            }
        }else{
            if($number < $length){
                return $array[$number];
            }
        }
    }


    /**
     * Project: chengfangjinke
     * Method: feedback
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 11:31
     * Desc: This is the code comment. This method is called feedback.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $changeID
     * @return array
     */
    public function feedback($changeID)
    {
        $oldProblem = $this->getByID($changeID);

        $data = fixer::input('post')->get();
        $data->status = 'feedbacked';
        $this->dao->update(TABLE_CHANGE)->data($data)->where('id')->eq($changeID)->exec();

        return common::createChanges($oldProblem, $data);
    }


    /**
     *申请变更
     *
     * @param $changeID
     * @return bool
     */
    public function run($changeID)
    {
        $data = fixer::input('post')
            //->add('status', 'wait')
            ->join('supply', ',')
            ->remove('uid,consumed,comment')
            ->stripTags($this->config->change->editor->run['id'], $this->config->allowedTags)
            ->get();

        $oldChange = $this->getByID($changeID);
        $reviewers = json_decode($oldChange->reviewer, true);

        $skipReviewNode = explode(',', $oldChange->skipReviewNode);
        if($this->post->skipReviewNode){
            $skipReviewNode = $this->post->skipReviewNode;
        }
        //  适应前置代码，赋值nodes,传递抄送人
        //$_POST['nodes'] = $this->getNOdesReviewers($oldChange->reviewer);
        $_POST['ccList'] = $data->supply ?? [];

        //工作量
        /*$consumed = $this->post->consumed;
        $checkRes = $this->loadModel('consumed')->checkConsumedInfo($consumed);
        if(!$checkRes){
            return false;
        }*/
        $postconsumed = 0.00;
        $data->reviewStage = 1;

        //获得一下状态
        $reviewersNodeKeys = array_keys($reviewers);
        $nextReviewNode = $reviewersNodeKeys[0];
        $data->status = $this->getNextStatusByNextNodeCode($nextReviewNode);
        $this->dao->update(TABLE_CHANGE)->data($data)->autoCheck()->batchCheck($this->config->change->run->requiredFields, 'notempty')->where('id')->eq($changeID)->exec();

        if($oldChange->status == 'reject') { //驳回后修改
            $data->failedEdit      = '0';
            $this->submitEditReview($changeID, $oldChange->version, $oldChange->level, $skipReviewNode);
        } else {
            $this->submitReview($changeID, $reviewers, $oldChange->version);
        }
        $data = $this->loadModel('file')->processImgURL($data, $this->config->change->editor->run['id'], $this->post->uid);

        $this->loadModel('file')->saveUpload('change', $changeID);
        $this->file->updateObjectID($this->post->uid, $changeID, 'change');

        $this->loadModel('consumed')->record('change', $changeID, $postconsumed, $this->app->user->account, $oldChange->status, $data->status, array());
    }

    /**
     * Project: chengfangjinke
     * Method: link
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 11:31
     * Desc: This is the code comment. This method is called link.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $changeID
     * @return false|void
     */
    public function link($changeID)
    {
        $change = $this->getByID($changeID);

        if(!$this->post->release)
        {
            dao::$errors['release'] = $this->lang->change->releaseEmpty;
            return false;
        }

        $data = new stdClass();
        $data->release     = trim(implode(',', $this->post->release), ',');
        $data->reviewStage = 1;
        $data->status      = 'cmconfirmed';

        $this->dao->update(TABLE_CHANGE)->data($data)->autoCheck()->batchCheck($this->config->change->link->requiredFields, 'notempty')
            ->where('id')->eq($changeID)->exec();

        $this->loadModel('review')->check('change', $changeID, $change->version, 'pass', $this->post->comment, 0);

        /* 下个节点设为pending */
        $next = $this->dao->select('id')->from(TABLE_REVIEWNODE)->where('objectType')->eq('change')
            ->andWhere('objectID')->eq($changeID)
            ->andWhere('version')->eq($change->version)
            ->andWhere('status')->eq('wait')->orderBy('stage,id')->fetch();
        $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('pending')->where('id')->eq($next->id)->exec();
        $this->dao->update(TABLE_REVIEWER)->set('status')->eq('pending')->where('node')->eq($next->id)->exec();

        $this->loadModel('consumed')->record('change', $changeID, $this->post->consumed, $this->app->user->account, 'wait', 'cmconfirmed', array());
    }

    /**
     * Project: chengfangjinke
     * Method: isClickable
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 11:31
     * Desc: This is the code comment. This method is called isClickable.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $change
     * @param $action
     * @return bool
     */
    public static function isClickable($change, $action)
    {
        global $app;
        $action = strtolower($action);
        $changeModel = new changeModel();
        $reviewsArr   =  $change->reviewers ? explode(',',$change->reviewers):[];
        $apppointsArr = $change->appiontUsers ?explode(',',$change->appiontUsers):[];

        if($action == 'edit')   return $change->status == 'waitcommit' or $change->status == 'reject' or $change->status == 'recall' ;
        if($action == 'review') return ($change->status != 'waitcommit' and $change->status != 'reject' and  $change->status != 'recall') && (in_array($app->user->account,$reviewsArr) || in_array($app->user->account,$apppointsArr));
       // if($action == 'run')    return ($change->status == 'waitcommit') or ($change->status == 'reject') and ($app->user->account == $change->createdBy);
        if($action == 'run')    return ($change->status == 'waitcommit')  and ($app->user->account == $change->createdBy); //20222708 修复退回问题
        if($action == 'close')  return $change->status == 'feedbacked';
        if($action == 'recall') {
            $state = ['waitcommit', 'success','recall','reject', 'archive', 'gmsuccess']; //20222708 修复退回问题
            return (!in_array($change->status, $state)) && strpos(",$change->createdBy,", ",{$app->user->account},") !== false;
        }
        if($action == 'appoint') {

            $state = ['managersuccess', 'productmanagersuccess'];

            if(in_array($change->status, $state) && !in_array($app->user->account,$apppointsArr) && in_array($app->user->account,$reviewsArr)){
                return true;
            }else{
                return false;
            }

//            return (in_array($change->status, $state)) && strpos("$change->reviewers", "{$app->user->account}") !== false;
        }
        if($action == 'delete') {
            return (!in_array($change->status, $changeModel->lang->change->notAllowDeleteStatusList));
        }

        return true;
    }

    public function sendmail($changeID, $actionID)
    {
        $this->loadModel('mail');
        $change = $this->getById($changeID);
        $users  = $this->loadModel('user')->getPairs('noletter');
        $projectPlan = $this->dao->select('id,mark')->from(TABLE_PROJECTPLAN)->where('project')->eq($change->project)->fetch();

        /* 获取后台通知中配置的邮件发信。*/
        $this->app->loadLang('custommail');
        $mailConf   = isset($this->config->global->setChangeMail) ? $this->config->global->setChangeMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf   = json_decode($mailConf);
        $browseType = 'change';

        /* 处理邮件发信的标题和日期。*/
        $bestDate  = empty($change->createdDate) ? '' : substr($change->createdDate, 0, 10);
        $mailTitle = vsprintf($mailConf->mailTitle, $mailConf->variables);
        if($change->status == 'success'){ //打基线完成
            $mailTitle = '【通知】您有一个【项目变更】已审批通过，请及时登录研发过程管理平台查看';
        }

        /* Get action info. */
        $action          = $this->loadModel('action')->getById($actionID);
        $history         = $this->action->getHistory($actionID);
        $action->history = isset($history[$actionID]) ? $history[$actionID] : array();

        /* Get mail content. */
        $oldcwd     = getcwd();
        $modulePath = $this->app->getModulePath($appName = '', 'change');
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

        /* 处理收件人。*/
        if($change->status == 'success'){ //打基线完成
            $toList = $change->createdBy;
            if($change->mailUsers){
                $mailUsers = explode(',', $change->mailUsers);
                $mailUsers = array_filter($mailUsers);
                $toList .= ','.implode(',', $mailUsers);
            }
            $createdDept = $change->createdDept;
            $deptInfo = $this->loadModel('dept')->getByID($createdDept);
            if($deptInfo){
                $ccList = $deptInfo->manager . ',' .$deptInfo->qa;
            }
        }elseif($change->status == 'reject'){
            $toList = $change->createdBy;
            $ccList = '';
        }else{
            //待架构部，产创部审批。 并且存在 session 则 修改收件人

            if(in_array($change->status,['managersuccess','productmanagersuccess']) && isset($_SESSION["mailUser_".$changeID]) && $_SESSION["mailUser_".$changeID]){

                    $toList = $_SESSION["mailUser_".$changeID];
                    unset($_SESSION["mailUser_".$changeID]);

            }else{


                if(in_array($change->status,['managersuccess','productmanagersuccess'])){
                    $appiontUsers = [];
                    // 经办人 给意见  不发邮件
                    if($change->appiontUsers){
                        $appiontUsers = explode(',',$change->appiontUsers);
                        if(in_array($this->app->user->account,$appiontUsers)){
                            return false;
                        }
                    }
                    //并行节点 不给代办人重复发送邮件
                    $reviewers = explode(",",$change->reviewers);
                    $otherreviewers = array_diff($reviewers,$appiontUsers);
                    if(count($otherreviewers) == 1){
                        return false;
                    }


                }

                $toList = $change->reviewers;
                $ccList = $this->post->ccList;
                if(isset($ccList) && !empty($ccList)){
                    if(is_array($ccList)){
                        $ccList = implode(',', $ccList);
                    }
                }else{
                    $ccList = '';
                }
                //抄送人添加项目经理
                $project = $change->project;
                $projectInfo = $this->loadModel('project')->getByID($project);
                if($projectInfo){
                    $ccList .= ','.$projectInfo->PM;
                }
            }


        }

        /* 处理邮件标题。*/
        $subject = $mailTitle;

        /* Send emails. */
        $this->mail->send($toList, $subject, $mailContent, $ccList);
        if($this->mail->isError()) error_log(join("\n", $this->mail->getError()));
    }

    /**
     *通过状态获得审核节点
     *
     * @param $status
     * @return string
     */
    public function getReviewNodeCodeByStatus($status){
        $nodeCode = zget($this->lang->change->reviewStatusNodeCodeMapList, $status);
        return $nodeCode;
    }

    /**
     * Project: chengfangjinke
     * Method: getToAndCcList
     * User: wangjiurong
     * Year: 2022
     * Date: 2022/03/10
     * Time: 10:44
     * Desc: 检查信息是否允许当前用户审核.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $change
     * @param $version
     * @param $status
     * @param $userAccount
     * @return array
     */
    public function checkAllowReview($change, $version, $status, $userAccount = '')
    {
        $res = array(
            'result'  => false,
            'message' => '',
        );
        if(!$change){
            $res['message'] = $this->lang->common->errorParamId;
            return $res;
        }
        if(!$userAccount){
            $res['message'] = $this->lang->common->errorParamUser;
            return $res;
        }
        //判断当前状态是否允许审核
        if(!in_array($change->status, $this->lang->change->allowReviewStatusArray)){
            $statusDesc = zget($this->lang->change->statusList, $change->status);
            $res['message'] = sprintf($this->lang->change->checkReviewList['statusError'], $statusDesc);
            return $res;
        }
        if(($version != $change->version) || ($status != $change->status)){
            $nodeCode = $this->getReviewNodeCodeByStatus($status);
            $reviewerInfo = $this->loadModel('review')->getReviewedUserByNodeCode('change', $change->id, $version, $nodeCode);
            $message = $this->lang->review->statusError;
            if($reviewerInfo){
                $message = sprintf($this->lang->change->checkReviewList['statusReviewedError'], $reviewerInfo->realname);
            }
            $res['message'] = $message;
            return $res;
        }
        //获得当前的的审核人信息
       /* $reviewStage = [$change->reviewStage];
        if(in_array($change->reviewStage,[3,4])){
            $reviewStage = [3,4];
        }*/
        $reviews =  $this->loadModel('review')->getMuiltNodeReviewer('change', $change->id, $change->version);
        if(!$reviews['reviews']){
            $res['message'] = $this->lang->change->checkReviewList['endError'];
            return $res;
        }
//        $reviews = explode(',', $reviews);
        if(!in_array($userAccount, $reviews['reviews'])){
            $res['message'] = $this->lang->change->checkReviewList['statusUserError'];
            return $res;
        }
        $res['result'] = true;
        $res['reviews'] = $reviews['reviews'];
        $res['appointUsers'] = $reviews['appointUsers'];
        return  $res;
    }

    /**
     * Method: setNOdesReviewers
     * Desc: 保存新建、编辑节点审核人.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param bool $skip
     * @param $nodes
     * @return string
    */
    public function setNOdesReviewers($nodes,$skip = false) {
        $reviewers = '';
        forEach($nodes as $key => $node){
            if($skip) $reviewers .= $key . ';';
            else $reviewers .= join(',',$node) . ';';
        }
        return substr($reviewers,0,-1);
    }

    /**
     * Method: getNOdesReviewers
     * Desc: 解构新建、编辑节点审核人.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $reviewers
     * @param bool $skip 跳过节点解析标识
     * @return array
     */
    public function getNOdesReviewers($reviewers, $skip = false) {
        $reviewerLists = array();

        forEach(explode(';',$reviewers) as $key => $node){
            if($skip) $reviewerLists[$node] = array('1');
            else array_push($reviewerLists, explode(',',$node));
        }
        return $reviewerLists;
    }

    /**
     * 获得审核人变更信息
     *
     * @author wangjiurong
     * @param $oldReviewers
     * @param $newReviewers
     * @param $level
     * @return array
     */
    public function getChangeReviewers($oldReviewers, $newReviewers, $level){
        $data = [];
        //历史节点
        $users    = $this->loadModel('user')->getPairs('noclosed');
        foreach($newReviewers as $key => $currentReviews) {
            if (!is_array($currentReviews)) {
                $currentReviews = array($currentReviews);
            }

            $currentReviews = array_filter($currentReviews);
            //原来节点审核人
            $oldReviews = isset($oldReviewers[$key]) ? $oldReviewers[$key] :array();

            //编辑前后审核人信息有变化
            $addReviewsTemp = array_filter(array_diff($currentReviews, $oldReviews));
            $delReviewsTemp = array_filter(array_diff($oldReviews, $currentReviews));
            if(!empty($addReviewsTemp) || !empty($delReviewsTemp)){
                $newCurrentReviewers = '';
                $oldCurrentReviewers = '';
                if(!empty($currentReviews)){
                    $reviewerUsers    = getArrayValuesByKeys($users, $currentReviews);
                    $newCurrentReviewers = implode(',', $reviewerUsers);
                }
                if(!empty($oldReviews)){
                    $reviewerUsers    = getArrayValuesByKeys($users, $oldReviews);
                    $oldCurrentReviewers = implode(',', $reviewerUsers);
                }
                if(in_array($key, $this->lang->change->reviewLevelNodeCodeList[$level])) {
                    $temp = new stdClass();
                    $temp->new = $newCurrentReviewers;
                    $temp->old = $oldCurrentReviewers;
                    $data[$key] = $temp;
                }
            }
        }
        return $data;
    }

    /**
     * 撤回
     * @param $changeID
     * @return array
     */
    public function recall($changeID){

        $oldChange = $this->getByID($changeID);
        if(!($_POST['comment']))
        {
            return dao::$errors['comment'] = $this->lang->change->recallCauseTip ;
        }
        $change = fixer::input('post')
            ->add('status', 'recall')
            ->add('reviewer', $oldChange->createdBy)
            ->add('reviewStage','0')
            ->remove('comment,uid')
            ->get();
        //修改
        $this->dao->update(TABLE_CHANGE)->data($change)
            ->autoCheck()
            ->where('id')->eq($changeID)->exec();
        $logChange = common::createChanges($oldChange, $change);
        $consumedExtra = new stdClass();


        $postconsumed = 0.00;
        $this->loadModel('consumed')->record('change', $changeID, $postconsumed, $this->app->user->account, $oldChange->status, 'recall', array(), $consumedExtra);
        return $logChange;
    }


    /**
     *获得项目变更打基线信息
     *
     * @param $changeInfo
     * @return array
     */
    public function getBaseLineInfo($changeInfo){
        $data = [];
        if(!$changeInfo){
            return $data;
        }
        $baseLineType = $changeInfo->baseLineType;
        $baseLinePath = $changeInfo->baseLinePath;
        $baseLineTime = $changeInfo->addBaseLineTime;
        if(!$baseLineType){
            return $data;
        }
        $baseLineTypeArray = explode(',', $baseLineType);
        $baseLinePathArray = explode(',', $baseLinePath);
        foreach ($baseLineTypeArray as $key => $baseLineType){
            $baseLinePath = $baseLinePathArray[$key];
            $temp = new stdClass();
            $temp->baseLineType = $baseLineType;
            $temp->baseLinePath = $baseLinePath;
            $temp->baseLineTime = $baseLineTime;
            $data[] = $temp;
        }
        return $data;
    }

    // 喧喧消息
    public function getXuanxuanTargetUser($obj,$objectType, $objectID, $actionType, $actionID, $actor = ''){
        $change = $this->getById($objectID);
        /* 处理收件人。*/
        if($change->status == 'success'){ //打基线完成
            $toList = $change->createdBy;
        }else{
            if(in_array($change->status,['managersuccess','productmanagersuccess']) && isset($_SESSION["xuanxuanUser_".$objectID]) && $_SESSION["xuanxuanUser_".$objectID]){

                    $toList = $_SESSION["xuanxuanUser_".$objectID];
                    unset($_SESSION["xuanxuanUser_".$objectID]);

            }else{
                if(in_array($change->status,['managersuccess','productmanagersuccess'])){
                    $appiontUsers = [];
                    // 经办人 给意见  不发邮件
                    if($change->appiontUsers){
                        $appiontUsers = explode(',',$change->appiontUsers);
                        if(in_array($this->app->user->account,$appiontUsers)){
                            return false;
                        }
                    }
                    //并行节点 不给代办人重复发送邮件
                    $reviewers = explode(",",$change->reviewers);
                    $otherreviewers = array_diff($reviewers,$appiontUsers);
                    if(count($otherreviewers) == 1){
                        return false;
                    }


                }
                $toList = $change->reviewers;
            }

        }
        if(is_array($toList)){
            $toList =  implode(",",$toList);
        }

        $server   = $this->loadModel('im')->getServer('zentao');
        $url = $server.helper::createLink($objectType, 'view', "id=$objectID", 'html#app=project');
        $subcontent = [];
        $subcontent['headTitle']    = '';
        $subcontent['headSubTitle'] = '';
        $subcontent['count']        = 0;
        $subcontent['id']           = 0;
        $subcontent['parent']       = '';
        $subcontent['parentURL']    = "";
        $subcontent['cardURL']      = $url;
        $subcontent['name']         = '';//消息体 编号后边位置 标题

        //标题
        $title = '';
        $actions = [];

        return ['toList'=>$toList,'subcontent'=>$subcontent,'url'=>$url,'title'=>$title,'actions'=>$actions];
    }

    /**
     * 获得分类列表
     *
     * @param string $level
     * @return mixed
     */
    public function  getCategoryList($level = ''){
        $categoryList  = $this->lang->change->categoryList;
        if($level == 1){ //一级变更
            if(isset($categoryList['other'])){
                unset($categoryList['other']);
            }
        }
        return $categoryList;
    }

    /**
     * 判断当前项目是否属于上海
     * @param $projectId
     * @return bool
     */
    public function isShangHaiProject($projectId){
        $projectPlan = $this->dao->select("id")->from(TABLE_PROJECTPLAN)->where('project')->eq($projectId)->andWhere('deleted')->eq(0)->fetch();
        $flag = false;
        if($projectPlan){
            $flag =   $this->loadModel('projectplan')->isShangHai($projectPlan->id);
        }
        return $flag;
    }
}

