<?php
class chengfangjinkeReview extends reviewModel
{
    /**
     * Project: chengfangjinke 一次性增加一个节点
     * Method: addNode
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:24
     * Desc: This is the code comment. This method is called addNode.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $objectType
     * @param $objectID
     * @param $version
     * @param $reviewers
     * @param false $withGrade
     * @param string $status
     * @param $stage
     * @param $extParams
     * @return false
     */
    public function addNode($objectType, $objectID, $version, $reviewers, $withGrade = false, $status = 'wait', $stage = 1, $extParams = []){
        $node = new stdClass();
        $node->objectType  = $objectType;
        $node->objectID    = $objectID;
        $node->version     = $version;
        $node->status      = $status;
        $node->stage       = $stage;
        $node->createdBy   = $this->app->user->account;
        $node->createdDate = helper::now();
        if($objectType == 'review'){
            $review =  $this->loadModel('review')->getReviewMainInfoByID($objectID, 'endDate');
            $node->endDate = $review->endDate;
        }
        if(isset($extParams['subObjectType']) && !empty($extParams['subObjectType'])){ //子分类
            $node->subObjectType = $extParams['subObjectType'];
        }
        if(isset($extParams['type']) && !empty($extParams['type'])){
            $node->type = $extParams['type'];
        }
        //节点标识
        if(isset($extParams['nodeCode']) && !empty($extParams['nodeCode'])){
            $node->nodeCode = $extParams['nodeCode'];
        }
        //设置是否显示
        if(isset($extParams['isShow']) && !empty($extParams['isShow'])){
            $node->isShow = $extParams['isShow'];
        }
        $this->dao->insert(TABLE_REVIEWNODE)->data($node)->exec();

        if(dao::isError()) return false;
        $nodeID = $this->dao->lastInsertID();

        foreach($reviewers as $grade => $reviewer)
        {
            if(empty($reviewer))
            {
                continue;
            }
            $user = new stdClass();
            $user->node        = $nodeID;
            $user->status      = $status;
            $user->createdBy   = $this->app->user->account;
            $user->createdDate = helper::now();
            if($status == 'ignore'){
                $user->reviewTime = helper::now();
            }
            if($withGrade) $user->grade = $grade;
            //扩展条件
            if(isset($extParams['reviewerExtParams'])){
                $reviewerExtParams = $extParams['reviewerExtParams'];
                foreach ($reviewerExtParams as $subKey => $reviewerExtParam){
                    $user->$subKey = $reviewerExtParam;
                }
            }

            if(is_array($reviewer))
            {
                foreach($reviewer as $reviewerInfo)
                {
                    if(is_object($reviewerInfo)){ //判断是否是对象
                        foreach ($reviewerInfo as $key => $value){
                            $user->$key = $value;
                        }
                    }else{
                        $account = $reviewerInfo;
                        $user->reviewer = $account;
                    }
                    $this->dao->insert(TABLE_REVIEWER)->data($user)->exec();
                }
            }
            else
            {
                $user->reviewer = $reviewer;
                $this->dao->insert(TABLE_REVIEWER)->data($user)->exec();
            }
        }

        return $nodeID;
    }

    /**
     * 一次性增加多个审核节点（一个或者多个）
     *
     * @param $objectType  对象类型
     * @param $objectID 对象ID
     * @param $version 版本
     * @param $reviewNodes 审核节点信息 $reviewNodes =
     *      array(
                array( //参与人员
                'reviewers' => $joinReviewers,        //必填 多个人用数组，单个人可以数组也可以字符串
                'status'    => $nodeStatus,           //非必填
                'stage'     => $firstIncludeStage,    //非必填
                'nodeCode'  => $firstIncludeNodeCode,  //非必填
                ),
                array(//主审人员
                'reviewers' => $mainReviewers,
                'status'    => $nodeStatus,
                'stage'     => $firstMainStage,
                'nodeCode'  => $firstMainNodeCode,
                )
            );
     * @return bool
     */
    public function addReviewNodes($objectType, $objectID, $version, $reviewNodes){
        if(!($objectType && $objectID && $reviewNodes)){
            return false;
        }

        //初始化审核节点排序
        if(isset($reviewNodes[0]['stage'])){
            $stage = $reviewNodes[0]['stage'];
        }else{
            $stage = $this->loadModel('review')->getReviewDefaultStage($objectID, $objectType, $version);
        }

        foreach($reviewNodes as $key => $currentNode) {
            $reviewers = $currentNode['reviewers'];
            if(!is_array($reviewers)){
                $reviewers = array($reviewers);
            }
            $reviewers = array_filter($reviewers);
            if(isset($currentNode['status'])){
                $status = $currentNode['status'];
            }else{
                if($key > 0){
                    $status = 'wait';
                    if(empty($reviewers)){
                        $status = 'ignore';
                    }
                }else{
                    //设置初始状态待审核
                    $status =  $this->loadModel('review')->getReviewNodeDefaultStatus($objectID, $objectType, $version);
                }
            }
            if(isset($currentNode['stage'])){
                $stage = $currentNode['stage'];
            }
            //扩展信息
            $extParams = [];
            //节点标识
            if(isset($currentNode['nodeCode'])){
                $extParams['nodeCode'] = $currentNode['nodeCode'];
            }
            //节点分类
            if(isset($currentNode['type'])){
                $extParams['type'] = $currentNode['type'];
            }
            //节点子分类
            if(isset($currentNode['subObjectType'])){
                $extParams['subObjectType'] = $currentNode['subObjectType'];
            }
            //是否显示节点（不显示的节点一般用于历史数据修复）
            if(isset($currentNode['isShow'])){
                $extParams['isShow'] = $currentNode['isShow'];
            }

            //只新增审核人信息
            if(isset($currentNode['nodeId']) && $currentNode['nodeId']){
                $res = $this->loadModel('review')->addNodeReviewers($currentNode['nodeId'], $reviewers, true, $status);
            }else{ //新增审核节点和审核人信息
                $this->loadModel('review')->addNode($objectType, $objectID, $version, $reviewers, true, $status, $stage, $extParams);
            }
            $stage++;
        }
        return true;
    }

    /**
     * Project: chengfangjinke
     * Method: check
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:24
     * Desc: This is the code comment. This method is called check.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $objectType
     * @param $objectID
     * @param $version
     * @param $result
     * @param $comment
     * @param int $grade
     * @param null $extra
     * @param $is_all_check_pass  //该节点是否需要全部人员审核通过才算审核通过
     * @return string //返回审核状态:空,pass, reject
     */
    public function check($objectType, $objectID, $version, $result, $comment, $stage = '', $extra = null, $is_all_check_pass = true,$nodeid=0)
    {
        //查询是否有待审核的节点
        $node = $this->dao->select('*')->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq($objectType)
            ->andWhere('objectID')->eq($objectID)
            ->andWhere('version')->eq($version)
            ->beginIF($objectType=='change' && $stage != '')->andWhere('stage')->eq($stage)->fi()
            ->beginIF($objectType=='projectplan' && $nodeid != 0)->andWhere('id')->eq($nodeid)->fi()
            ->andWhere('status')->eq('pending')
            ->orderBy('stage,id')
            ->fetch();
        if(!$node) return '';

        //tangfei 增加审核时间
        $lastDealDate = helper::now();
        if(!$extra) $extra = new stdClass();


        $projectPlanUser = [];
        if($objectType == 'projectplanyear'){
            //年度计划架构部负责人（包含）后，给admin留后门，可进行审批
            //分管领导获取以及总经理获取
            $users = $this->loadModel('user')->getPairs('noletter');
            $deptInfo = $this->loadModel('dept')->getDeptPairs();
            $deptIds = implode(',',array_keys($deptInfo));
            $leaderOfDeptsMerge = $this->loadModel('dept')->getByIDs($deptIds);
            $leadersMergeInfo = array_flip(array_filter(array_unique(array_column($leaderOfDeptsMerge,'leader1'))));
            $leaderCN = array_flip($leadersMergeInfo);
            foreach ($leaderCN as $name){
                $leader[$name] = $users[$name];
            }
            if(!isset($leader['hetielin'])){
                $arrCTO = array('hetielin'=>'贺铁林');
                $leader = array_merge($arrCTO,$leader);
            }
            unset($leader['luoyongzhong']);

            $projectPlanUser='';
            if($this->app->user->account == 'admin'){
                if($node->nodeCode == 'CTO'){
                    $projectPlanUser = array_keys($leader);
                }else if($node->nodeCode == 'gm'){
                    $projectPlanUser = ['luoyongzhong'];
                }else if($node->nodeCode == 'builtLeader'){
                    $projectPlanUser = ['zhujie'];
                }
            }

        }


        //修改当前审核人的状态为操作状态
//        $this->dao->update(TABLE_REVIEWER)
//            ->set('status')->eq($result)
//            ->set('comment')->eq($comment)
//            ->set('extra')->eq(json_encode($extra))
//            ->set('reviewTime')->eq($lastDealDate)
//            ->where('node')->eq($node->id)
//            ->andWhere('status')->eq('pending') //当前状态
//            ->andWhere('reviewer')->eq($this->app->user->account) //当前审核人
//            ->exec();
        //修改当前审核人的状态为操作状态
        $this->dao->update(TABLE_REVIEWER)
            ->set('status')->eq($result)
            ->set('comment')->eq($comment)
            ->set('extra')->eq(json_encode($extra))
            ->set('reviewTime')->eq($lastDealDate)
            ->where('node')->eq($node->id)
            ->andWhere('status')->eq('pending') //当前状态
            // ->andWhere('reviewer')->eq($this->app->user->account) //当前审核人   需要恢复
            ->beginIF($objectType != 'projectplanyear')->andWhere('reviewer')->eq($this->app->user->account) //当前审核人
            ->beginIF($this->app->user->account == 'admin' && !empty($projectPlanUser) && $objectType == 'projectplanyear')->andWhere('reviewer')->in($projectPlanUser) //当前审核人
            ->beginIF($this->app->user->account != 'admin' && empty($projectPlanUser) && $objectType == 'projectplanyear')->andWhere('reviewer')->eq($this->app->user->account) //当前审核人
            ->exec();
        //查询该节点下所有的审核人
        $reviews = $this->dao->select('*')->from(TABLE_REVIEWER)
            ->where('node')->eq($node->id)
            ->fetchAll();

        //是否评审验证节点
        $isReviewVerify = false;
        if(($node->objectType == 'review') && ($node->nodeCode == 'verify')){
            $isReviewVerify = true;
        }

        if($isReviewVerify){ //评审环节的验证操作单独处理
            //审核节点的审核结果
            $nodeResult = $result;
            if($is_all_check_pass){ //需要全部审核
                //默认需要全部审核通过
                $all = true;
                foreach($reviews as $review) {
                    if($review->status == 'pending') {
                        $all = false;
                        break;
                    }
                }
                //要求全部审核通过时才算真正审核通过，此时还有部分人未审核，不修改审核节点状态
                if(!$all) {
                    $nodeResult = 'part';
                }else{ //全部审核通过
                    foreach($reviews as $review) {
                        if($review->status == 'reject') {
                            $nodeResult = 'reject';
                            break;
                        }
                    }
                    //修改节点审核状态
                    $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq($nodeResult)
                        ->where('id')->eq($node->id)
                        ->exec();
                }

            }else{ //一人审核通过即可
                $unCheckReviews = [];
                foreach ($reviews as $review) {
                    if ($review->status == 'pending') {
                        $unCheckReviews[] = $review->id; //未审核的人
                    }
                }
                if($unCheckReviews){ //审核通过时，有一人审核通过即可，其他人不用审核
                    $this->dao->update(TABLE_REVIEWER)->set('status')->eq('ignore')
                        ->where('id')->in($unCheckReviews)
                        ->exec();
                }

                //修改节点审核状态
                $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq($nodeResult)
                    ->where('id')->eq($node->id)
                    ->exec();
            }
            //如果节点的评审结果
            if($nodeResult == 'reject' || $nodeResult == 'suspend') {
                // 如果拒绝了，当前和以后的节点涉及到的评审人都设为ignore，不需要评审了
                $nodes = $this->dao->select('*')->from(TABLE_REVIEWNODE)
                    ->where('objectType')->eq($objectType)
                    ->andWhere('objectID')->eq($objectID)
                    ->andWhere('version')->eq($version)
                    ->andWhere('status')->in(array('wait', 'pending'))
                    ->orderBy('stage,id')
                    ->fetchAll();
                $ns = array();
                foreach($nodes as $node) $ns[] = $node->id;
                if(!empty($ns)){
                    $this->dao->update(TABLE_REVIEWER)->set('status')->eq('ignore')
                        ->where('node')->in($ns)
                        ->andWhere('status')->in(array('wait', 'pending'))
                        ->exec();
                }
            }
            return $nodeResult;
        }else{
            //如果审核结果是通过
            if($result == 'pass') {
                if($is_all_check_pass){ //需要全部审核通过
                    //默认需要全部审核通过
                    $all = true;
                    foreach($reviews as $review)
                    {
                        if($review->status != 'pass')
                        {
                            $all = false;
                            break;
                        }
                    }
                    //要求全部审核通过时才算真正审核通过，此时还有部分人未审核，不修改审核节点状态
                    if(!$all) return 'part';
                }else{ //该节点一人审核通过即可
                    $unCheckReviews = [];
                    foreach ($reviews as $review) {
                        if ($review->status != 'pass') {
                            $unCheckReviews[] = $review->id; //未审核的人
                        }
                    }
                    if($unCheckReviews){ //审核通过时，有一人审核通过即可，其他人不用审核
                        $this->dao->update(TABLE_REVIEWER)->set('status')->eq('ignore')
                            ->where('id')->in($unCheckReviews)
                            ->exec();
                    }
                }
            }


            //修改节点审核状态
            $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq($result)
                ->where('id')->eq($node->id)
                ->exec();
            //审核状态是拒绝或者挂起
            if($result == 'reject' || $result == 'suspend')
            {
                // 如果拒绝了，当前和以后的节点涉及到的评审人都设为ignore，不需要评审了
                $nodes = $this->dao->select('*')->from(TABLE_REVIEWNODE)
                    ->where('objectType')->eq($objectType)
                    ->andWhere('objectID')->eq($objectID)
                    ->andWhere('version')->eq($version)
                    ->orderBy('stage,id')
                    ->fetchAll();
                $ns = array();
                foreach($nodes as $node) $ns[] = $node->id;
                if(!empty($ns)){
                    $this->dao->update(TABLE_REVIEWER)->set('status')->eq('ignore')
                        ->where('node')->in($ns)
                        ->andWhere('status')->in(array('wait', 'pending'))
                        ->exec();
                }

                //项目变更模块  如果当前是多节点并行。则 将其他  pending中节点改为 wait。
                if($objectType == 'change'){
                    $this->dao->update(TABLE_REVIEWNODE)
                        ->set('status')->eq('wait')
                        ->where('objectType')->eq($objectType)
                        ->andWhere('objectID')->eq($objectID)
                        ->andWhere('version')->eq($version)
                        ->andWhere('status')->eq('pending')
                        ->exec();

                }
            }
            return $result;
        }
    }

    /**
     * @Notes:部门管理层审核逻辑 需求任务和需求意向的变更审核场景
     * @Date: 2023/8/23
     * @Time: 10:40
     * @Interface checkRequirementAndOpinion
     * @param $objectType
     * @param $objectID
     * @param $version
     * @param $stage
     * @param $comment
     * @return string
     */
    public function checkRequirementAndOpinion($objectType,$objectID,$version,$stage,$comment)
    {
        $this->app->loadLang('demand');
        $node = $this->dao->select('*')->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq($objectType)
            ->andWhere('objectID')->eq($objectID)
            ->andWhere('version')->eq($version)
            ->andWhere('stage')->eq($stage)
            ->andWhere('status')->eq('pending')
            ->orderBy('stage,id')
            ->fetch();
        if(!$node) return '';
        /*本节点需要选择人员的其中一人和自定义配置必选人员两个必审核才行*/
        $nodeID = $node->id;
        $account = $this->app->user->account;

        //更新当前节点审核人
        $this->dao->update(TABLE_REVIEWER)
            ->set('`status`')->eq('pass')
            ->set('`reviewTime`')->eq(helper::now())
            ->where('node')->eq($nodeID)
            ->andWhere('reviewer')->eq($account)
            ->exec();

        //返回状态重新查询
        $deptReviewerResult = $this->dao->select('*')->from(TABLE_REVIEWER)->where('`status`')->eq('pass')->andWhere('node')->eq($nodeID)->andWhere('reviewer')->eq($deptReviewer)->fetch();
        $noDeptReviewerResult = $this->dao->select('*')->from(TABLE_REVIEWER)->where('`status`')->eq('pass')->andWhere('node')->eq($nodeID)->andWhere('reviewer')->notIN([$account,$deptReviewer])->fetch();
        $returnStatus = 'part';
        if($deptReviewerResult && $noDeptReviewerResult)
        {
            $returnStatus = 'pass';
        }
        return $returnStatus;
    }

    public function checkRequirementAndOpinionCopy($objectType,$objectID,$version,$stage,$comment)
    {
        $this->app->loadLang('demand');
        $node = $this->dao->select('*')->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq($objectType)
            ->andWhere('objectID')->eq($objectID)
            ->andWhere('version')->eq($version)
            ->andWhere('stage')->eq($stage)
            ->andWhere('status')->eq('pending')
            ->orderBy('stage,id')
            ->fetch();
        if(!$node) return '';
        /*本节点需要选择人员的其中一人和自定义配置必选人员两个必审核才行*/
        $nodeID = $node->id;
        $account = $this->app->user->account;

        //自定义配置部门管理层审核人 必须审核
        $deptReviewer = $this->lang->demand->deptReviewList['reviewer'] ?? 'ningxiang';

        //更新当前节点审核人
        $this->dao->update(TABLE_REVIEWER)
            ->set('`status`')->eq('pass')
            ->set('`reviewTime`')->eq(helper::now())
            ->where('node')->eq($nodeID)
            ->andWhere('reviewer')->eq($account)
            ->exec();

        if($account != $deptReviewer)
        {
            //更新除当前节点和自定义配置人的审核状态为ignore
            $this->dao->update(TABLE_REVIEWER)
                ->set('`status`')->eq('ignore')
                ->where('node')->eq($nodeID)
                ->andWhere('reviewer')->notIN([$account,$deptReviewer])
                ->exec();
        }

        //返回状态重新查询
        $deptReviewerResult = $this->dao->select('*')->from(TABLE_REVIEWER)->where('`status`')->eq('pass')->andWhere('node')->eq($nodeID)->andWhere('reviewer')->eq($deptReviewer)->fetch();
        $noDeptReviewerResult = $this->dao->select('*')->from(TABLE_REVIEWER)->where('`status`')->eq('pass')->andWhere('node')->eq($nodeID)->andWhere('reviewer')->notIN([$account,$deptReviewer])->fetch();
        $returnStatus = 'part';
        if($deptReviewerResult && $noDeptReviewerResult)
        {
            $returnStatus = 'pass';
        }
        return $returnStatus;
    }



    public function checkVerify($objectID, $version, $result, $user)
    {
        //查询是否有待审核的节点
        $node = $this->dao->select('*')->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq('review')
            ->andWhere('objectID')->eq($objectID)
            ->andWhere('version')->eq($version)
            ->andWhere('status')->eq('pending')
            ->orderBy('stage,id')
            ->fetch();
        if(!$node) return '';

        //tangfei 增加审核时间
        $lastDealDate = helper::now();

        //修改当前审核人的状态为操作状态
        $this->dao->update(TABLE_REVIEWER)
            ->set('status')->eq($result)
            ->set('reviewTime')->eq($lastDealDate)
            ->where('node')->eq($node->id)
            ->andWhere('status')->eq('pending') //当前状态
            ->andWhere('reviewer')->eq($user) //当前审核人
            ->exec();
        //查询该节点下所有的审核人
        $reviews = $this->dao->select('*')->from(TABLE_REVIEWER)
            ->where('node')->eq($node->id)
            ->fetchAll();

            //审核节点的审核结果
            $nodeResult = $result;

            //默认需要全部审核通过
            $all = true;
            foreach($reviews as $review) {
                if($review->status == 'pending') {
                    $all = false;
                    break;
                }
            }
            //要求全部审核通过时才算真正审核通过，此时还有部分人未审核，不修改审核节点状态
            if(!$all) {
                $nodeResult = 'part';
            }else{ //全部审核通过
                foreach($reviews as $review) {
                    if($review->status == 'reject') {
                        $nodeResult = 'reject';
                        break;
                    }
                }
                //修改节点审核状态
                $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq($nodeResult)
                    ->where('id')->eq($node->id)
                    ->exec();
            }

            //如果节点的评审结果
            if($nodeResult == 'reject' || $nodeResult == 'suspend') {
                // 如果拒绝了，当前和以后的节点涉及到的评审人都设为ignore，不需要评审了
                $nodes = $this->dao->select('*')->from(TABLE_REVIEWNODE)
                    ->where('objectType')->eq('review')
                    ->andWhere('objectID')->eq($objectID)
                    ->andWhere('version')->eq($version)
                    ->andWhere('status')->in(array('wait', 'pending'))
                    ->orderBy('stage,id')
                    ->fetchAll();
                $ns = array();
                foreach($nodes as $node) $ns[] = $node->id;
                if(!empty($ns)){
                    $this->dao->update(TABLE_REVIEWER)->set('status')->eq('ignore')
                        ->where('node')->in($ns)
                        ->andWhere('status')->in(array('wait', 'pending'))
                        ->exec();
                }
            }
            return $nodeResult;

    }

    /**
     * Project: chengfangjinke
     * Method: check
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:24
     * Desc: This is the code comment. This method is called check.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $objectType
     * @param $objectID
     * @param $version
     * @param $result
     * @param $comment
     * @param int $grade
     * @param null $extra
     * @param $is_all_check_pass  //该节点是否需要全部人员审核通过才算审核通过
     * @return string //返回审核状态:空,pass, reject
     */
    public function autoDealcheck($objectType, $objectID, $version, $result, $comment, $stage = '', $extra = null, $dealUser, $is_all_check_pass = true)
    {
        //查询是否有待审核的节点
        $node = $this->dao->select('*')->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq($objectType)
            ->andWhere('objectID')->eq($objectID)
            ->andWhere('version')->eq($version)
//            ->beginIF($stage != '')->andWhere('stage')->eq($stage)->fi()
            ->andWhere('status')->eq('pending')
            ->orderBy('stage,id')
            ->fetch();
        if(!$node) return '';

        //tangfei 增加审核时间
        $lastDealDate = helper::now();
        if(!$extra) $extra = new stdClass();
        //修改当前审核人的状态为操作状态
        $this->dao->update(TABLE_REVIEWER)
            ->set('status')->eq($result)
            ->set('comment')->eq($comment)
            ->set('extra')->eq(json_encode($extra))
            ->set('reviewTime')->eq($lastDealDate)
            ->where('node')->eq($node->id)
            ->andWhere('status')->eq('pending') //当前状态
            ->andWhere('reviewer')->eq($dealUser) //当前审核人
            ->exec();
        //查询该节点下所有的审核人
        $reviews = $this->dao->select('*')->from(TABLE_REVIEWER)
            ->where('node')->eq($node->id)
            ->fetchAll();
        //如果审核结果是通过
        if($result == 'pass')
        {
            if($is_all_check_pass){ //需要全部审核通过
                //默认需要全部审核通过
                $all = true;
                foreach($reviews as $review)
                {
                    if($review->status != 'pass')
                    {
                        $all = false;
                        break;
                    }
                }
                //要求全部审核通过时才算真正审核通过，此时还有部分人未审核，不修改审核节点状态
                if(!$all) return 'part';
            }else{ //该节点一人审核通过即可
                $unCheckReviews = [];
                foreach ($reviews as $review) {
                    if ($review->status != 'pass') {
                        $unCheckReviews[] = $review->id; //未审核的人
                    }
                }
                if($unCheckReviews){ //审核通过时，有一人审核通过即可，其他人不用审核
                    $this->dao->update(TABLE_REVIEWER)->set('status')->eq('ignore')
                        ->where('id')->in($unCheckReviews)
                        ->exec();
                }
            }
        }

        //修改节点审核状态
        $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq($result)
            ->where('id')->eq($node->id)
            ->exec();
        //审核状态是拒绝或者挂起
        if($result == 'reject' || $result == 'suspend')
        {
            // 如果拒绝了，当前和以后的节点涉及到的评审人都设为ignore，不需要评审了
            $nodes = $this->dao->select('*')->from(TABLE_REVIEWNODE)
                ->where('objectType')->eq($objectType)
                ->andWhere('objectID')->eq($objectID)
                ->andWhere('version')->eq($version)
                ->orderBy('stage,id')
                ->fetchAll();
            $ns = array();
            foreach($nodes as $node) $ns[] = $node->id;
            if(!empty($ns)){
                $this->dao->update(TABLE_REVIEWER)->set('status')->eq('ignore')
                    ->where('node')->in($ns)
                    ->andWhere('status')->in(array('wait', 'pending'))
                    ->exec();
            }
        }
        return $result;
    }

    /**
     * 设置忽略剩余节点
     *
     * @param $objectType
     * @param $objectID
     * @param int $version
     * @return bool
     */
    public function setReviewNodesIgnore($objectType, $objectID, $version = 1){
        $statusArray = array('wait', 'pending');
        $nodeIds = $this->dao->select('id')->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq($objectType)
            ->andWhere('objectID')->eq($objectID)
            ->andWhere('version')->eq($version)
            ->andWhere('status')->in($statusArray)
            ->orderBy('stage,id')
            ->fetchPairs();

        if($nodeIds){
            $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('ignore')
                ->where('id')->in($nodeIds)
                ->exec();

            $this->dao->update(TABLE_REVIEWER)->set('status')->eq('ignore')
                ->where('node')->in($nodeIds)
                ->andWhere('status')->in($statusArray)
                ->exec();
        }
        return true;
    }

    /**
     * Project: chengfangjinke
     * Method: getNodes
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:24
     * Desc: This is the code comment. This method is called getNodes.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $objectType
     * @param $objectID
     * @param int $version
     * @return array
     */
    public function getNodes($objectType, $objectID, $version = 1)
    {
        $nodes = $this->dao->select('*')->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq($objectType)
            ->andWhere('objectID')->eq($objectID)
            ->andWhere('version')->eq($version)
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
     * 获取历史所有审批记录（包含本次）
     * @param $objectType
     * @param $objectID
     * @return array
     */
    public function getAllNodes($objectType, $objectID)
    {
        $versions = $this->dao->select('distinct(version) ver')->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq($objectType)
            ->andWhere('objectID')->eq($objectID)
            ->andWhere('status')->notIn('wait,pending,ignore')
            ->orderBy('version')
            ->fetchAll();
        $allData = [];
        if($versions){
            foreach ($versions as $version) {
                $nodes = $this->dao->select('*')->from(TABLE_REVIEWNODE)
                    ->where('objectType')->eq($objectType)
                    ->andWhere('objectID')->eq($objectID)
                    ->andWhere('version')->eq($version->ver)
                    ->andWhere('status')->notIn('wait,pending,ignore')
                    ->orderBy('stage,id')->fetchAll('id');
                $reviewers = $this->dao->select('*')->from(TABLE_REVIEWER)->where('node')->in(array_keys($nodes))->andWhere('status')->notIn('wait')->fetchAll();
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
                $allData[$version->ver] = $data;

            }
        }
        return $allData;
    }

    /**
     * 获得审核节点以NodeCode分组
     * @param $objectType
     * @param $objectID
     * @param $version
     * @return array
     */
    public function getNodesGroupByNodeCode($objectType, $objectID, $version){
        $data = $this->getNodes($objectType, $objectID, $version);
        if($data){
            $data = array_column($data, null, 'nodeCode');
        }
        return $data;
    }


    /**
     * 项目变更获取节点
     * @param $objectType
     * @param $objectID
     * @param int $version
     * @return array
     */
    public function getChangeNodes($objectType, $objectID, $version = 1)
    {
        $nodes = $this->dao->select('*')->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq($objectType)
            ->andWhere('objectID')->eq($objectID)
            ->andWhere('version')->eq($version)
            ->orderBy('stage,id')->fetchAll('id');
        $reviewers = $this->dao->select('*')->from(TABLE_REVIEWER)->where('node')->in(array_keys($nodes))->fetchAll();
        $map = array();
        ksort($nodes); //20220629 排序
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
     * Project: chengfangjinke
     * Method: getReviewer
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:25
     * Desc: This is the code comment. This method is called getReviewer.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $objectType
     * @param $objectID
     * @param int $version
     * @param int $grade
     * @param null $extra
     * @return string
     */
    public function getReviewer($objectType, $objectID, $version = 1, $grade = 0, $extra = null)
    {
        $node = $this->dao->select('*')->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq($objectType)
            ->andWhere('objectID')->eq($objectID)
            ->andWhere('version')->eq($version)
            ->andWhere('status')->eq('pending')
            ->orderBy('stage,id')
            ->fetch();
        if(!$node) return '';

        $reviews = $this->dao->select('reviewer')->from(TABLE_REVIEWER)
            ->where('node')->eq($node->id)
            ->andWhere('status')->eq('pending')
            ->orderBy('id')
            ->fetchPairs();
        if(!$reviews) return '';

        $reviews = array_flip(array_flip($reviews));
        return join(',', $reviews);
    }

    /*public function getMuiltNodeReviewer($objectType, $objectID, $version = 1, $stage = [],$status='pending', $extra = null)
    {
        $node = $this->dao->select('*')->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq($objectType)
            ->andWhere('objectID')->eq($objectID)
            ->andWhere('version')->eq($version)
            ->andWhere('status')->eq($status)
            ->andWhere('stage')->in($stage)
            ->orderBy('stage,id')
            ->fetchAll();

        if(!$node) return '';

        $nodeIDArr = array_column($node,'id');
        $reviews = $this->dao->select('reviewer')->from(TABLE_REVIEWER)
            ->where('node')->in($nodeIDArr)
            ->andWhere('status')->eq($status)
            ->orderBy('id')
            ->fetchPairs();
        if(!$reviews) return '';

        $reviews = array_flip(array_flip($reviews));
        return join(',', $reviews);
    }*/

    public function getMuiltNodeReviewer($objectType, $objectID, $version = 1, $stage = [], $status='pending', $extra = null)
    {
        $node = $this->dao->select('*')->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq($objectType)
            ->andWhere('objectID')->eq($objectID)
            ->andWhere('version')->eq($version)
            ->andWhere('status')->eq($status)
//            ->andWhere('stage')->in($stage)
            ->orderBy('stage,id')
            ->fetchAll();

        if(!$node) return ['reviews'=>[],'appointUsers'=>[]];

        $nodeIDArr = array_column($node,'id');
        $reviews = $this->dao->select('*')->from(TABLE_REVIEWER)
            ->where('node')->in($nodeIDArr)
            ->andWhere('status')->eq($status)
            ->orderBy('id')
            ->fetchAll();
        if(!$reviews) return ['reviews'=>[],'appointUsers'=>[]];

        $reviewsUsers = [];
        $appointUser = [];
        foreach($reviews as $review){
            $reviewsUsers[] = $review->reviewer;
            if($review->reviewerType == 2){
                $appointUser[] = $review->reviewer;
            }
        }
//        $reviews = array_flip(array_flip($reviews));
//        return join(',', $reviews);
        return ['reviews'=>$reviewsUsers,'appointUsers'=>$appointUser];
    }
    public function getReviewByAccount($objectType, $objectID,$account, $version = 1,$status='pending', $extra = null){

         $res = $this->dao->select("t1.id,t1.status,t1.objectType,t1.objectID,t1.nodeCode,t1.version,t1.stage,t1.extra,t2.reviewer,t2.extra extra2,t2.grade,t2.id reviewerID,t2.reviewerType")->from(TABLE_REVIEWNODE)->alias('t1')
            ->innerJoin(TABLE_REVIEWER)->alias('t2')->on('t1.id = t2.node')
            ->where('t1.objectType')->eq($objectType)
            ->andWhere('t1.objectID')->eq($objectID)
            ->andWhere('t1.version')->eq($version)
            ->andWhere('t1.status')->eq($status)
            ->andWhere('t2.status')->eq($status)
            ->andWhere('t2.reviewer')->eq($account)
            ->orderBy('stage,id')
            ->fetch();

        return $res;

    }

    public function getMuiltNodeReviewers($objectType, $objectID, $version = 1, $stage = [], $status='pending', $extra = null)
    {
        $node = $this->dao->select('*')->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq($objectType)
            ->andWhere('objectID')->eq($objectID)
            ->andWhere('version')->eq($version)
            ->andWhere('status')->eq($status)
//            ->andWhere('stage')->in($stage)
            ->orderBy('stage,id')
            ->fetchAll();

        if(!$node) return '';

        $nodeIDArr = array_column($node,'id');
        $reviews = $this->dao->select('*')->from(TABLE_REVIEWER)
            ->where('node')->in($nodeIDArr)
            ->andWhere('status')->eq($status)
            ->orderBy('id')
            ->fetchAll();
        if(!$reviews) return '';

        $reviewsUsers = [];

        foreach($reviews as $review){
            $reviewsUsers[] = $review->reviewer;

        }
//        $reviews = array_flip(array_flip($reviews));
//        return join(',', $reviews);
        return join(',',$reviewsUsers);
    }

    public function getAppointUsers($nodeID){
        return $this->dao->select("*")->from(TABLE_REVIEWER)->where('node')->eq($nodeID)->andWhere("reviewerType")->eq(2)->fetchAll('reviewer');
    }

     /**
     * Project: chengfangjinke
     * Method: getLastPendingPeople
     * User: t_tangfei
     * Year: 2022
     * Date: 2022/1/13
     * Time: 16:25
     * Desc: This is the code comment. This method is called getReviewer.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $objectType
     * @param $objectID
     * @param int $version
     * @param int $stage
     * @param null $subObjectType
     * @return string
     */
     public function getLastPendingPeople($objectType, $objectID, $version = 1, $stage = 1, $subObjectType = '')
     {
        $node = $this->dao->select('*')->from(TABLE_REVIEWNODE)
             ->where('objectType')->eq($objectType)
             ->andWhere('objectID')->eq($objectID)
             ->andWhere('version')->eq($version)
             ->andWhere('stage')->eq($stage)
             ->beginIF($subObjectType != '')->andWhere('subObjectType')->eq($subObjectType)->fi()
             ->orderBy('stage,id')
             ->fetch();
         if(!$node) return '';

         $reviews = $this->dao->select('reviewer')->from(TABLE_REVIEWER)
             ->where('node')->eq($node->id)
             ->orderBy('id')
             ->fetchPairs();
         if(!$reviews) return '';

         return join(',', $reviews);
     }

    /**
     *根据节点获得审核人
     *
     * @param $objectType
     * @param $objectID
     * @param $version
     * @param $nodeCode
     * @param $returnType
     * @return string array
     */
     public function getReviewersByNodeCode($objectType, $objectID, $version, $nodeCode, $returnType = 'string'){
         if($returnType ==  'string'){
             $reviewers = '';
         }else{
             $reviewers = [];
         }

         if(!($objectType && $objectID && $nodeCode)){
             return $reviewers;
         }

         $node = $this->dao->select('id')->from(TABLE_REVIEWNODE)
             ->where('objectType')->eq($objectType)
             ->andWhere('objectID')->eq($objectID)
             ->andWhere('version')->eq($version)
             ->andWhere('nodeCode')->eq($nodeCode)
             ->orderBy('stage,id')
             ->fetch();
         if(!$node) {
             return  $reviewers;
         }

         $data = $this->dao->select('reviewer')->from(TABLE_REVIEWER)
             ->where('node')->eq($node->id)
             ->orderBy('id')
             ->fetchPairs();

         if(!empty($data)){
             if($returnType ==  'string'){
                 $reviewers = implode(',', $data);
             }else{
                 $reviewers = $data;
             }
         }
         return $reviewers;
     }

    /**
     *根据节点获得审核人及处理意见
     *
     * @param $objectType
     * @param $objectID
     * @param $version
     * @param $nodeCode
     * @return array
     */
    public function getReviewersAndCommentByNodeCode($objectType, $objectID, $version, $nodeCode){
        $reviewers = [];
        if(!($objectType && $objectID && $nodeCode)){
            return $reviewers;
        }

        $node = $this->dao->select('id')->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq($objectType)
            ->andWhere('objectID')->eq($objectID)
            ->andWhere('version')->eq($version)
            ->andWhere('nodeCode')->eq($nodeCode)
            ->orderBy('stage,id')
            ->fetch();
        if(!$node) {
            return  $reviewers;
        }

        $data = $this->dao->select('reviewer,status,extra')->from(TABLE_REVIEWER)
            ->where('node')->eq($node->id)
            ->orderBy('id')
            ->fetchAll();

        if(!empty($data)){
            foreach($data as $info){
                $extra = json_decode($info->extra,true);
                if($info->status == 'pass' && $extra['isEditInfo'] == '2'){
                    $reviewers[] = $info->reviewer;
                }
            }
        }
        return $reviewers;
    }

    /**
     *获得待审核节点
     *
     * @param $objectType
     * @param $objectID
     * @param $version
     * @param $nodeCode
     * @return bool
     */
     public function getPendingReviewNode($objectType, $objectID, $version, $nodeCode){
         if(!($objectType && $objectID && $nodeCode)){
             return false;
         }
         $nodeId = $this->dao->select('id')
             ->from(TABLE_REVIEWNODE)
             ->where('objectType')->eq($objectType)
             ->andWhere('objectID')->eq($objectID)
             ->andWhere('version')->eq($version)
             ->andWhere('nodeCode')->eq($nodeCode)
             ->andWhere('status')->eq('pending')
             ->orderBy('stage,id')
             ->fetch();
         return $nodeId;
     }

    /**
     * 获得审核人列表
     *
     * @param $objectType
     * @param $objectID
     * @param $version
     * @param $nodeCodes
     * @return array|void
     */
     public function getReviewersListByNodeCodes($objectType, $objectID, $version, $nodeCodes){
         $data = [];
         if(!($objectType && $objectID && $nodeCodes)){
             return $data;
         }
         $nodeList = $this->dao->select('id, nodeCode')->from(TABLE_REVIEWNODE)
             ->where('objectType')->eq($objectType)
             ->andWhere('objectID')->eq($objectID)
             ->andWhere('version')->eq($version)
             ->andWhere('nodeCode')->in($nodeCodes)
             ->orderBy('stage, id')
             ->fetchAll('id');
         if(!$nodeList) {
             return  $data;
         }
         //审核节点
         $nodeIds = array_keys($nodeList);
         $ret = $this->dao->select('node, reviewer')->from(TABLE_REVIEWER)
             ->where('node')->in($nodeIds)
             ->orderBy('id')
             ->fetchAll();
        if(!$ret){
            return $data;
        }

        //按照nodeCode展示分组账号
        foreach ($ret as $val){
            $node     = $val->node;
            $reviewer = $val->reviewer;
            $nodeCode = $nodeList[$node]->nodeCode;
            $data[$nodeCode][] = $reviewer;
        }
        return $data;
     }

    /**
     * 获得审核人信息按次序分组
     *
     * @param $objectType
     * @param $objectID
     * @param $version
     * @param $nodeCode
     * @return array
     */
     public function getReviewersByNodeCodeGroupGrade($objectType, $objectID, $version, $nodeCode){
         $data = [];
         if(!($objectType && $objectID && $nodeCode)){
             return $data;
         }
         $node = $this->dao->select('id')->from(TABLE_REVIEWNODE)
             ->where('objectType')->eq($objectType)
             ->andWhere('objectID')->eq($objectID)
             ->andWhere('version')->eq($version)
             ->andWhere('nodeCode')->eq($nodeCode)
             ->orderBy('stage,id')
             ->fetch();
         if(!$node) {
             return  $data;
         }

         $reviewsList = $this->dao->select('reviewer, grade')->from(TABLE_REVIEWER)
             ->where('node')->eq($node->id)
             ->fetchAll();
         if(!$reviewsList) {
             return $data;
         }
         foreach ($reviewsList as $val){
             $grade = $val->grade;
             $reviewer = $val->reviewer;
             $data[$grade][] = $reviewer;
         }
         return $data;
     }

    /**
     * Project: chengfangjinke
     * Method: getStageReviews
     * User: t_tangfei
     * Year: 2022
     * Date: 2022/1/13
     * Time: 16:25
     * Desc: This is the code comment. This method is called getReviewer.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $objectType
     * @param $objectID
     * @param int $version
     * @param int $stage
     * @param null $subObjectType
     * @return string
     */
    public function getStageReviews($objectType, $objectID, $version = 1, $stage = 1, $subObjectType = '', $returnListFlag = false)
    {
        $data = [];
        $node = $this->dao->select('*')->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq($objectType)
            ->andWhere('objectID')->eq($objectID)
            ->andWhere('version')->eq($version)
            ->andWhere('stage')->eq($stage)
            ->beginIF($subObjectType != '')->andWhere('subObjectType')->eq($subObjectType)->fi()
            ->orderBy('stage,id')
            ->fetch();
        if(!$node) {
            return $data;
        };

        $reviewsList = $this->dao->select('reviewer, grade')->from(TABLE_REVIEWER)
            ->where('node')->eq($node->id)
            ->fetchAll();
        if(!$reviewsList) {
            return $data;
        }
        foreach ($reviewsList as $val){
            $grade = $val->grade;
            $reviewer = $val->reviewer;
            if($returnListFlag) {
                $data[] = $reviewer;
            }else {
                $data[$grade][] = $reviewer;
            }
        }
        return $data;
    }

    /**
     * Project: chengfangjinke
     * Method: setNodePending
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:25
     * Desc: This is the code comment. This method is called setNodePending.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $objectType
     * @param $objectID
     * @param $version
     * @return int
     */
    public function setNodePending($objectType, $objectID, $version)
    {
        $next = $this->dao->select('id')->from(TABLE_REVIEWNODE)
              ->where('objectType')->eq($objectType)
              ->andWhere('version')->eq($version)
              ->andWhere('objectID')->eq($objectID)->andWhere('status')->eq('wait')
              ->orderBy('stage,id')->fetch();
        if(!$next) return 0;

        $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('pending')->where('id')->eq($next->id)->exec();
        $this->dao->update(TABLE_REVIEWER)->set('status')->eq('pending')->where('node')->eq($next->id)->exec();
        return $next->id;
    }

    /**
     * Project: chengfangjinke
     * Method: getAllNodeReviewers
     * User: wangjiurong
     * Year: 2022
     * Date: 2022/02/15
     * Time: 14:24
     * Desc: 获得所有节点中每个节点的审核人.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $objectType
     * @param $objectID
     * @param int $version
     * @return array
     */
    public function getAllNodeReviewers($objectType, $objectID, $version = 1)
    {
        $data = [];
        $nodes = $this->getNodes($objectType, $objectID, $version);
        if(!$nodes){
            return $data;
        }
        foreach ($nodes as $key => $val){
            $reviewers = $val->reviewers;
            $currentAccounts = [];
            if(!empty($reviewers)){
                $currentAccounts = array_column($reviewers, 'reviewer');
            }
            $data[$key] = $currentAccounts;
        }
        return $data;
    }

    /**
     * 项目变更
     * @param $objectType
     * @param $objectID
     * @param int $version
     * @return array
     */
    public function getChangeAllNodeReviewers($objectType, $objectID, $version = 1)
    {
        $data = [];
        $nodes = $this->getChangeNodes($objectType, $objectID, $version);
        if(!$nodes){
            return $data;
        }
        foreach ($nodes as $key => $val){
            $reviewers = $val->reviewers;
            $nodeCode = $val->nodeCode;
            $currentAccounts = [];
            if(!empty($reviewers)){
                $currentAccounts = array_column($reviewers, 'reviewer');
            }
            $data[$nodeCode] = $currentAccounts;
        }
        return $data;
    }


    /**
     * Project: chengfangjinke
     * Method: addNodeReviewers
     * User: wangjiurong
     * Year: 2022
     * Date: 2022/02/15
     * Time: 16:24
     * Desc: 增加节点审核人
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $nodeID
     * @param $reviewers
     * @param false $withGrade
     * @param string $status
     * @param $reviewerExtParams
     * @return false
     */
    public function addNodeReviewers($nodeID, $reviewers, $withGrade = false, $status = 'wait', $reviewerExtParams = [])
    {
        if(!($nodeID && $reviewers)){
            return  false;
        }
        foreach($reviewers as $grade => $reviewer)
        {
            if(empty($reviewer))
            {
                continue;
            }

            $user = new stdClass();
            $user->node        = $nodeID;
            $user->status      = $status;
            $user->createdBy   = $this->app->user->account;
            $user->createdDate = helper::now();
            if($reviewerExtParams){
                foreach ($reviewerExtParams as $subKey => $reviewerExtParam){
                    $user->$subKey = $reviewerExtParam;
                }
            }

            if($status == 'ignore'){
                $user->reviewTime = helper::now();
            }
            if($withGrade) $user->grade = $grade;

            if(is_array($reviewer))
            {
                foreach($reviewer as $account)
                {
                    $account = trim($account);
                    if(empty($account)) {
                        continue;
                    }
                    $user->reviewer = $account;
                    $this->dao->insert(TABLE_REVIEWER)->data($user)->exec();
                }
            }
            else
            {
                $user->reviewer = $reviewer;
                $this->dao->insert(TABLE_REVIEWER)->data($user)->exec();
            }
        }

        return true;
    }

    /**
     *获得审核节点
     *
     * @param $objectType
     * @param $objectID
     * @param $version
     * @param array $extParams
     * @return array
     */
    public function getReviewerNodeIds($objectType, $objectID, $version, $extParams = []){
        $data = array();
        $sql = "select id
                  from zt_reviewnode
                  where 1 
                  and objectType = '{$objectType}' 
                  and objectID = '{$objectID}'
                  and version = '{$version}'";

        if($extParams){
            foreach ($extParams as $key => $val){
                if(is_array($val)){
                    $sql .= " and $key  " . helper::dbIN($val);
                }else{
                    $sql .= " and $key = '{$val}'";
                }
            }
        }
        $sql .= " order by stage, id";
        $temp =  $this->dao->query($sql)->fetchAll();
        if($temp){
            foreach ($temp as $val){
                $data[] = $val->id;
            }
        }
        return $data;
    }

    /**
     * Project: chengfangjinke
     * Method: delReviewers
     * User: wangjiurong
     * Year: 2022
     * Date: 2022/02/15
     * Time: 16:24
     * Desc: 删除节点审核人
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $reviewerIds
     * @return bool
     */
    public function delReviewers($reviewerIds){
        if(!$reviewerIds){
            return false;
        }
        if(is_array($reviewerIds)){
            $reviewerIds = implode(',', $reviewerIds);
        }
        $this->dao->delete()->from(TABLE_REVIEWER)->where('id')->in($reviewerIds)->exec();
        return true;
    }

    /**
     * Project: chengfangjinke
     * Method: getReviewerAccounts
     * User: wangjiurong
     * Year: 2022
     * Date: 2022/02/15
     * Time: 9:43
     * Desc: get reviewer user accounts
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $reviewers
     * @return array
     */
    public function getReviewerAccounts($reviewers)
    {
        $data = array();
        if(!$reviewers){
            return $data;
        }
        foreach ($reviewers as $key => $currentReviewers){
            $currentAccounts = array_keys($currentReviewers);
            $currentAccounts = array_filter($currentAccounts);
            $data[$key] = $currentAccounts;
        }
        return $data;
    }

    /**
     * Project: chengfangjinke
     * Method: getRealReviewerInfo
     * User: wangjiurong
     * Year: 2022
     * Date: 2022/02/15
     * Time: 9:43
     * Desc: 获得真实审核的用户
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $nodeStatus
     * @param $nodeReviewers
     * @param $ignoreComment
     * @return mixed
     */
    public function getRealReviewerInfo($nodeStatus, $nodeReviewers = [], $ignoreComment = ''){
        $data = new stdClass();
        $data->reviewer = '';
        $data->status  = '';
        $data->comment = '';
        $data->reviewTime = '';
        if(!($nodeStatus && $nodeReviewers && is_array($nodeReviewers))){
            return $data;
        }
        if($nodeStatus == 'wait' || $nodeStatus == 'ignore' || $nodeStatus == 'pending'){
            $data->status = $nodeStatus;
            if($nodeStatus == 'ignore'){

                $data->comment = $ignoreComment ? $ignoreComment: $this->lang->review->ignoreStatusDefComment;
            }
            return $data;
        }

        foreach ($nodeReviewers as $k => $reviewerInfo) {
            $status = $reviewerInfo->status; //审核状态
            if ($status == 'pass' || $status == 'reject' || $status == 'approvesuccess' || $status == 'externalsendback' || $status == 'closed' || $status == 'suspend' || $status == 'feedbacked'
                || $status == 'firstpassed' || $status == 'finalpassed'|| $status == 'syncfail' || $status == 'syncsuccess' || $status == 'jxsyncfail' || $status == 'jxsyncsuccess' || $status == 'feedbacksuccess'||
                $status == 'feedbackfail' || $status == 'secondlineapproved' || $status == 'appoint' || $status == 'confirming' || $status == 'incorporate'|| $status == 'report') {//审核完成
                if ($reviewerInfo->comment != ''){
                    $reviewerInfo = $this->loadModel('file')->replaceImgURL($reviewerInfo, 'comment');
                }
                $data = $reviewerInfo;
                break;
            }
        }
        return $data;
    }

    /**
     * Project: chengfangjinke
     * Method: getReviewedReviewerInfo
     * User: wangjiurong
     * Year: 2022
     * Date: 2022/02/23
     * Time: 17:25
     * Desc: 获得某一审核节点的审核人
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $objectType
     * @param $objectID
     * @param int $version
     * @param int $reviewStage
     * @return array
     */
    public function getReviewedUserInfo($objectType, $objectID, $version = 1, $reviewStage = 0)
    {
        $data = [];
        $statusEndArray = array('pass', 'reject');
        $node = $this->dao->select('*')->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq($objectType)
            ->andWhere('objectID')->eq($objectID)
            ->andWhere('version')->eq($version)
            ->andWhere('stage')->gt($reviewStage)
            ->andWhere('status')->in($statusEndArray)
            ->orderBy('stage,id')
            ->fetch();

        if(!$node) {
            return $data;
        }

        $reviews = $this->dao->select('reviewer')->from(TABLE_REVIEWER)
            ->where('node')->eq($node->id)
            ->andWhere('status')->in($statusEndArray)
            ->fetch();
        if(!$reviews) {
            return $data;
        }
        $select = 'account, realname';
        $data = $this->loadModel('user')->getUserInfo($reviews->reviewer, $select);
        return $data;
    }

    /**
     *获得某一节点已经审核的人
     *
     * @param $objectType
     * @param $objectID
     * @param int $version
     * @param $nodeCode
     * @return array
     */
    public function getReviewedUserByNodeCode($objectType, $objectID, $version, $nodeCode){
        $data = [];
        $statusEndArray = array('pass', 'reject');
        $node = $this->dao->select('*')->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq($objectType)
            ->andWhere('objectID')->eq($objectID)
            ->andWhere('version')->eq($version)
            ->andWhere('nodeCode')->eq($nodeCode)
            ->andWhere('status')->in($statusEndArray)
            ->orderBy('stage,id')
            ->fetch();

        if(!$node) {
            return $data;
        }

        $reviews = $this->dao->select('reviewer')->from(TABLE_REVIEWER)
            ->where('node')->eq($node->id)
            ->andWhere('status')->in($statusEndArray)
            ->fetch();
        if(!$reviews) {
            return $data;
        }
        $select = 'account, realname';
        $data = $this->loadModel('user')->getUserInfo($reviews->reviewer, $select);
        return $data;
    }

    /**
     * 获得审核人变更信息
     *
     * @author wangjiurong
     * @param $objectType
     * @param $objectID
     * @param $newReviewers
     * @param int $oldVersion
     * @return array
     */
    public function getChangeReviewers($objectType, $objectID, $newReviewers, $oldVersion = 1){
        $data = [];
        //历史节点
        $oldNodes = $this->getNodes($objectType, $objectID, $oldVersion);
        $users    = $this->loadModel('user')->getPairs('noclosed');
        foreach($newReviewers as $key => $currentReviews) {
            $nodeStage = $key + 1;
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
            $addReviewsTemp = array_diff($currentReviews, $oldReviews);
            $delReviewsTemp = array_diff($oldReviews, $currentReviews);
            if($addReviewsTemp || $delReviewsTemp){
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
                $temp = new stdClass();
                $temp->new = $newCurrentReviewers;
                $temp->old = $oldCurrentReviewers;
                $data[$nodeStage] = $temp;
            }else{
                if($currentReviews){
                    if($oldNodeInfo->status == 'ignore'){
                        //新增审核人
                        $reviewerUsers    = getArrayValuesByKeys($users, $currentReviews);
                        $newCurrentReviewers = implode(',', $reviewerUsers);
                        $oldCurrentReviewers = '';

                        $temp = new stdClass();
                        $temp->new = $newCurrentReviewers;
                        $temp->old = $oldCurrentReviewers;
                        $data[$nodeStage] = $temp;
                    }
                }
            }
        }
        return $data;
    }

    /**
     * Print datatable cell.
     *  新增字段
     * @param  object $col
     * @param  object $review
     * @param  array  $users
     * @param  array  $products
     * @access public
     * @return void
     */

    public function printCell($col, $review, $users, $products)
    {
        $reviewID = $review->id;
        $canView = common::hasPriv('review', 'view');
        $canBatchAction = false;

        $deptMap = $this->loadModel('dept')->getOptionMenu();
        $reviewList = inlink('view', "reviewID=$review->id");
        $account    = $this->app->user->account;
        $id = $col->id;
        $outsideList1 =array(''=>'') +$this->loadModel('user')->getUsersNameByType('outsideExpertType');
        $outsideList2 =array(''=>'') +$this->loadModel('user')->getUsersNameByType('outside');
        //$relatedUsers  = $this->loadModel('user')->getPairs('noletter');
        $this->app->loadLang('projectplan');
        if($col->show)
        {
            $class = "c-$id";
            $title = '';
            if($id == 'id') $class .= ' cell-id';
            if($id == 'status')
            {
                $class .= ' status-' . $review->status;
                $name = zget($this->lang->review->statusLabelList, $review->status,'');
                $title  = "title='{$name}'";
            }
            if($id == 'result')
            {
                $class .= ' status-' . $review->result;
            }
            if($id == 'title')
            {
                $class .= ' text-left';
                $title  = "title='{$review->title}'";
            }

            echo "<td class='" . $class . "' $title>";

            $dataTrial = $this->loadModel('review')->getTrial($reviewID, $review->version, $users, 2);
            $trialDeptIds = $dataTrial['deptid'];
            $trialDeptLiasisonOfficer = $dataTrial['deptjkr'];
            $trialAdjudicatingOfficer = $dataTrial['deptzs'];
            $trialJoinOfficer = $dataTrial['deptjoin'];

            switch($id)
            {
                case 'id':
                    if($canBatchAction)
                    {
                        echo html::checkbox('reviewIDList', array($review->id => '')) . html::a(helper::createLink('review', 'view', "reviewID=$review->id"), sprintf('%03d', $review->id));
                    }
                    else
                    {
                        printf('%03d', $review->id);
                    }
                    break;
                case 'title':
                    echo html::a(helper::createLink('review', 'view', "reviewID=$review->id"), $review->title);
                    break;
                case 'product':
                    echo zget($products, $review->product);
                    break;
                case 'object':
                    $txt='';
                    $object = explode(',', $review->object);
                    foreach($object as $obj)
                    {
                        $obj = trim($obj);
                        if(empty($obj)) continue;
                        $txt .= zget($this->lang->review->objectList, $obj) . " &nbsp;";
                    }
                    echo '<div class="ellipsis" title="' . $txt . '">' . $txt .'</div>';
                    break;
                case 'version':
                    echo $review->version;
                    break;

                case 'status':
                    echo $review->statusDesc;

                    break;
                case 'type':
                    echo zget($this->lang->review->typeList, $review->type,'');
                    break;
                case 'owner':
                    $txt='';
                    $owners = explode(',', $review->owner);
                    foreach($owners as $account)
                    {
                        $account = trim($account);
                        if(empty($account)) continue;
                        $txt.= zget($users, $account) . " &nbsp;";
                    }
                    echo '<div class="ellipsis" title="' . $txt . '">' . $txt .'</div>';
                    break;

                case 'grade':
                    if($review->isConfirmGrade == 1){
                        echo  zget($this->lang->review->gradeList, $review->grade,'');
                    }else{
                        echo '';
                    }
                    break;

                case 'meetingPlanTime':
                    $meetingPlanTime = '';
                    if($review->meetingPlanTime != '0000-00-00 00:00:00' && $review->grade =='meeting'){
                        $meetingPlanTime = $review->meetingPlanTime;
                    }
                    echo $meetingPlanTime;
                    break;
                case 'meetingCode':
                    $meetingCode = '';
                    if($review->grade =='meeting'){
                        $meetingCode = $review->meetingCode;
                    }
                    echo  $meetingCode;
                    break;
                case 'meetingRealTime':
                    $meetingRealTime = '';
                    if($review->meetingRealTime != '0000-00-00 00:00:00'){
                        $meetingRealTime = $review->meetingRealTime;
                    }
                    echo $meetingRealTime;
                    break;

                case 'expert':
                    $txt='';
                    $experts = explode(',', $review->expert);
                    foreach($experts as $account)
                    {
                        $account = trim($account);
                        if(empty($account)) continue;
                        $txt .= zget($users, $account) . " &nbsp;";
                    }
                    echo '<div class="ellipsis" title="' . $txt . '">' . $txt .'</div>';
                    break;
                case 'reviewedBy':
                    $txt='';
                    $reviewedBy = explode(',', $review->reviewedBy);
                    foreach($reviewedBy as $account)
                    {
                        $account = trim($account);
                        if(empty($account)) continue;
                        $txt .= zget($outsideList1, $account) . " &nbsp;";
                    }
                    echo '<div class="ellipsis" title="' . $txt . '">' . $txt .'</div>';
                    break;

                case 'outside':
                    $txt='';
                    $outside = explode(',', $review->outside);
                    foreach($outside as $account)
                    {
                        $account = trim($account);
                        if(empty($account)) continue;
                        $txt .= zget($outsideList2, $account) . " &nbsp;";
                    }
                    echo '<div class="ellipsis" title="' . $txt . '">' . $txt .'</div>';
                    break;

                case 'meetingPlanExport':
                    $txt = '';
                    $meetingPlanExport = explode(',', $review->meetingPlanExport);
                    foreach($meetingPlanExport as $account) {
                        $account = trim($account);
                        if(empty($account)) continue;
                        $txt .= zget($users, $account) . " &nbsp;";
                    }
                    echo '<div class="ellipsis" title="' . $txt . '">' . $txt .'</div>';
                    break;

                case 'relatedUsers':
                    $txt='';
                    $relatedUsers = explode(',', $review->relatedUsers);

                    foreach($relatedUsers as $account)
                    {
                        $account = trim($account);
                        if(empty($account)) continue;
                        $txt .= zget($users, $account) . " &nbsp;";
                    }
                    echo '<div class="ellipsis" title="' . $txt . '">' . $txt .'</div>';
                    break;

                case 'createdBy':
                    echo zget($users, $review->createdBy,'');
                    break;
                case 'reviewer':
                    echo zget($users, $review->reviewer,'');
                    break;
                case 'createdDate':
                    echo $review->createdDate;
                    break;
                case 'deadline':
                    echo $review->deadline;
                    break;

                case 'projectType':
                    echo  zget($this->lang->projectplan->typeList, $review->projectType,'');
                    break;

                case 'isImportant':
                    echo zget($this->lang->review->isImportantList, $review->isImportant,'');
                    break;

                case 'lastReviewedDate':
                    echo $review->lastReviewedDate;
                    break;
                case 'lastAuditedDate':
                    echo $review->lastAuditedDate;
                    break;
                case 'result':
                    echo zget($this->lang->review->resultList, $review->resulty,'');
                    break;
                case 'auditResult':
                    echo zget($this->lang->review->auditResultList, $review->auditResulty,'');
                    break;
                case 'dealUser':
                    $dealUser = explode(',', str_replace(' ', '', $review->dealUser));
                    $txt = '';
                    foreach($dealUser as $account)
                        $txt .= zget($users, $account,'') . " &nbsp;";
                    echo '<div class="ellipsis" title="' . $txt . '">' . $txt .'</div>';
                    break;
                case 'deadDate':
                    $endDate =  $review->endDate != '0000-00-00 00:00:00' ? $review->endDate: '';
                    if(!empty($endDate)){
                        $endDate = date('Y-m-d', strtotime($endDate));
                    }
                    echo '<div class="ellipsis" title="' . $endDate . '">' . $endDate .'</div>';
                    break;
                case 'editBy':
                    echo zget($users, $review->editBy,'');
                    break;
                case 'editDate':
                    echo '<div class="ellipsis" title="' . $review->editDate . '">' . $review->editDate .'</div>';
                    break;

                case 'createdDept':
                    echo zget($deptMap, $review->createdDept,'');
                    break;
                case 'closePerson':
                    echo zget($users, $review->closePerson,'');
                    break;
                case 'closeTime':
                    echo '<div class="ellipsis" title="' . $review->closeTime . '">' . $review->closeTime .'</div>';
                    break;
                case 'qualityQa':
                    echo zget($users, $review->qualityQa,'');
                    break;
                case 'trialDept':
                        echo '<div class="ellipsis" title="' . $trialDeptIds . '">' . $trialDeptIds .'</div>';
                    break;

                case 'trialDeptLiasisonOfficer':
                    echo '<div class="ellipsis" title="' . $trialDeptLiasisonOfficer . '">' . $trialDeptLiasisonOfficer .'</div>';
                    break;

                case 'trialAdjudicatingOfficer':
                        echo '<div class="ellipsis" title="' . $trialAdjudicatingOfficer . '">' . $trialAdjudicatingOfficer .'</div>';
                    break;

                case 'trialJoinOfficer':
                        echo '<div class="ellipsis" title="' . $trialJoinOfficer . '">' . $trialJoinOfficer .'</div>';
                    break;

                case 'preReviewDeadline':
                    echo $review->preReviewDeadline;
                    break;
                case 'firstReviewDeadline':
                    echo $review->firstReviewDeadline;
                    break;
                case 'closeDate':
                    echo $review->closeDate;
                    break;
                case 'qa':
                    echo zget($users, $review->qa);
                    break;

                case 'qualityCm':
                    echo zget($users, $review->qualityCm);
                    break;

                case 'suspendBy':
                    echo zget($users, $review->suspendBy);
                    break;

                case 'suspendTime':
                    $suspendTime = $review->suspendTime != '0000-00-00 00:00:00' ? $review->suspendTime: '';
                    echo '<div class="ellipsis" title="' . $suspendTime . '">' . $suspendTime .'</div>';
                    break;

                case 'suspendReason':
                    $suspendReason = $review->suspendReason;
                    echo '<div class="ellipsis" title="' . $suspendReason . '">' . $suspendReason .'</div>';
                    break;

                case 'renewBy':
                    echo zget($users, $review->renewBy);
                    break;

                case 'renewTime':
                    $renewTime = $review->renewTime != '0000-00-00 00:00:00' ? $review->renewTime: '';
                    echo '<div class="ellipsis" title="' . $renewTime . '">' . $renewTime .'</div>';
                    break;

                case 'renewReason':
                    $renewReason = $review->renewReason;
                    echo '<div class="ellipsis" title="' . $renewReason . '">' . $renewReason .'</div>';
                    break;

                case 'actions':
                    $params  = "reviewID=$review->id";
                    $flag = $this->loadModel('review')->isClickable($review, 'recall');
                    $click = $flag ? 'onclick="return recall()"' : '';

                    $closeflag = $this->loadModel('review')->isClickable($review, 'close');
                    $id = $review->id;
                    $nodealissue = $this->review->getNoDealIssue($id);
                    $count  = isset($nodealissue[$id]) ?  $nodealissue[$id] : '';


                    $dealUser = explode(',', str_replace(' ', '', $review->dealUser));
                    //取出最后一个评审人
                    //判断当前用户是否是最后一个验证人
                    $lastVerifyer ='';
                    if(count($dealUser) == 1){
                        $lastVerifyer = 1;
                    }
                    //是否允许审批
                    $verFlag = '';
                    $checkRes = $this->review->checkReviewIsAllowReview($review, $this->app->user->account);
                    if($review->status == 'waitVerify' or $review->status == 'verifying' ){
                        $issueCount = $this->loadModel('reviewproblem')->getReviewIssueCount2($review->id,'createAndAccept');
                        if($issueCount!=0 and $lastVerifyer ==1){
                            $verFlag = 1;
                        }elseif($issueCount!=0){
                            $verFlag = 2;
                        }
                    }
                    $reviewTipMsg = $this->loadModel('review')->getReviewTipMsg($review->status);
                    common::hasPriv('review', 'edit') ?  common::printIcon('review', 'edit',    $params."&flag=1", $review, 'list') : '';
                    common::hasPriv('review', 'submit') ? common::printIcon('review', 'submit', $params, $review, 'list', 'play', '', 'iframe', true, '', $this->lang->review->submit) : '';
                    common::hasPriv('review', 'recall') ? common::printIcon('review', 'recall', $params, $review, 'list', 'back', 'hiddenwin', '', '', "$click", $this->lang->review->recall) : '';
                    common::hasPriv('review', 'assign') ? common::printIcon('review', 'assign', $params, $review, 'list','hand-right', '', 'iframe', true, 'data-position = "50px" data-toggle="modal" data-type="iframe" data-width="1200px"', $this->lang->review->assign) : '';
                   //非最最后一个人验证时
                    if(($review->status == 'waitVerify' or $review->status == 'verifying' )&&$verFlag ==2){
                        $clickClose ='onclick="return reviewVerifyConfirm()"';
                        common::hasPriv('review', 'review') ? common::printIcon('review', 'review', $params, $review, 'list', 'glasses', 'hiddenwin', 'iframe', true,"$clickClose", $reviewTipMsg) : '';
                    }else{
                        common::hasPriv('review', 'review') ? common::printIcon('review', 'review', $params, $review, 'list', 'glasses', '', 'iframe', true,' data-position = "50px" data-toggle="modal" data-type="iframe" data-width="1200px" ', $reviewTipMsg) : '';
                    }
                    common::hasPriv('review', 'reviewreport') ? common::printIcon('review', 'reviewreport',  $params, $review, 'list', 'bar-chart', '') : '';
                   // common::hasPriv('review', 'close') ? common::printIcon('review', 'close', $params, $review, 'list', 'off','', 'iframe', true, '', $this->lang->review->close) : '';

                    if(common::hasPriv('review', 'close'))
                    {
                        if($closeflag)
                        {
//                            $clickClose ="onclick=reviewClose('$review->id','$count')";
//                            common::printIcon('review', 'close', $params, '', 'list', 'off', '', 'iframe', true, "$clickClose", $this->lang->review->close);
                            echo '<a href="javascript:;" onclick="reviewClose('.$review->id.','.$count.')" class="btn"><i class="icon-review-close icon-off"></i></a>';
                        }
                        else
                        {
                            common::printIcon('review', 'close', $params, $review, 'list', 'off','', 'iframe', true, '', $this->lang->review->close);
                        }
                    }

                    common::hasPriv('review', 'delete') ? common::printIcon('review', 'delete', $params, $review, 'list', 'trash','', 'iframe', true, '', $this->lang->review->delete) : '';
            }
            echo '</td>';
        }
    }

    /**
     * 获得历史审核节点
     *
     * @param $objectType
     * @param $objectID
     * @param int $version
     * @param int $maxHistoryStage
     * @return array
     */
    public function getHistoryReviewers($objectType, $objectID, $version = 1, $maxHistoryStage = 0){
        $data = [];
        $nodes = $this->dao->select('*')->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq($objectType)
            ->andWhere('objectID')->eq($objectID)
            ->andWhere('version')->eq($version)
            ->andWhere('stage')->lt($maxHistoryStage) //小于
            ->orderBy('stage,id')->fetchAll('id');
        if(empty($nodes)){
            return $data;
        }
        $nodeIds = array_keys($nodes);
        //审核节点
        $reviewers = $this->dao->select('*')->from(TABLE_REVIEWER)->where('node')->in($nodeIds)->fetchAll();
        if(empty($reviewers)){
            return  $data;
        }
        $map = array();
        foreach($reviewers as $r)
        {
            $nodeId = $r->node;
            $map[$nodeId][] = $r;
        }
        foreach ($nodes as $nodeId => $nodeInfo){
            $nodeInfo->reviewers = [];
            if(isset($map[$nodeId])){
                $nodeInfo->reviewers = $map[$nodeId];
            }
            $data[] = $nodeInfo;
        }
        return $data;
    }


    /**
     * 获得未审核节点
     *
     * @param $objectType
     * @param $objectID
     * @param int $version
     * @param int $minHistoryStage
     * @return array
     */
    public function getWaitReviewers($objectType, $objectID, $version = 1, $minHistoryStage = 0){
        $data = [];
        $nodes = $this->dao->select('*')->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq($objectType)
            ->andWhere('objectID')->eq($objectID)
            ->andWhere('version')->eq($version)
            ->andWhere('stage')->gt($minHistoryStage) //大于
            ->orderBy('stage,id')->fetchAll('id');
        if(empty($nodes)){
            return $data;
        }
        $nodeIds = array_keys($nodes);
        //审核节点
        $reviewers = $this->dao->select('*')->from(TABLE_REVIEWER)->where('node')->in($nodeIds)->fetchAll();
        if(empty($reviewers)){
            return  $data;
        }
        $map = array();
        foreach($reviewers as $r)
        {
            $nodeId = $r->node;
            $map[$nodeId][] = $r;
        }
        foreach ($nodes as $nodeId => $nodeInfo){
            $nodeInfo->reviewers = [];
            if($map[$nodeId]){
                $nodeInfo->reviewers = $map[$nodeId];
            }
            $data[] = $nodeInfo;
        }
        return $data;
    }

    /**
     *获得节点排序ID
     *
     * @param $objectType
     * @param $objectID
     * @param int $version
     * @param string $nodeCode
     * @return int
     */
    public function getNodeStage($objectType, $objectID, $version = 1, $nodeCode = ''){
        $stage = 0;
        $ret = $this->dao->select('max(stage) as stage')->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq($objectType)
            ->andWhere('objectID')->eq($objectID)
            ->andWhere('version')->eq($version)
            ->andWhere('nodeCode')->eq($nodeCode)
            ->orderBy('stage_desc')
            ->fetch();
        if(empty($ret)){
            return $stage;
        }
        $stage = $ret->stage;
        return $stage;
    }


    /**
     *获得实际审核中的审核人
     *
     * @param $objectType
     * @param $objectID
     * @param int $version
     * @param $stageIds
     * @return array
     */
    public function getRealReviewers($objectType, $objectID, $version = 1, $stageIds = []){
        $data = [];
        $statusArray = array('pass', 'reject');
        $nodeIds = $this->dao->select('id')->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq($objectType)
            ->andWhere('objectID')->eq($objectID)
            ->andWhere('version')->eq($version)
            ->andWhere('status')->in($statusArray)
            ->beginIF(!empty($stageIds))->andWhere('stage')->in($stageIds)->fi()
            ->orderBy('stage,id')
            ->fetchPairs();

        if($nodeIds){
            $ret = $this->dao->select('reviewer')->from(TABLE_REVIEWER)
                ->where('node')->in($nodeIds)
                ->andWhere('status')->in($statusArray)
                ->fetchAll();
            if(!empty($ret)){
                $data = array_column($ret, 'reviewer');
            }
        }
        return $data;
    }

    /**
     * 获取阶段
     * @param $reviewID
     * @param $version
     * @return mixed
     */
    public function getStage($reviewID,$version){
       return $this->dao->select('stage')
            ->from(TABLE_REVIEWNODE)
            ->Where('objectID')->eq($reviewID)
            ->andWhere('objectType')->eq('review')
            ->andWhere('version')->eq($version)
            ->orderBy('stage desc')
            ->limit(1)
            ->fetch();
    }

    /**
     *获得允许编辑的审核节点
     *
     * @author wangjiurong
     * @param $objectType
     * @param $objectID
     * @param int $version
     * @return array
     */
    public function getAllowEditNodes($objectType, $objectID, $version = 1){
        $data = [];
        $statusArray = ['pending', 'wait'];
        $ret = $this->dao->select('id')
            ->from(TABLE_REVIEWNODE)
            ->Where('objectID')->eq($objectID)
            ->andWhere('objectType')->eq($objectType)
            ->andWhere('version')->eq($version)
            ->andWhere('status')->in($statusArray)
            ->beginIF($objectType == 'review')->andWhere('nodeCode')->notin(array('meetingReview', 'meetingOwnerReview'))->fi()
            ->fetchAll();
        if(empty($ret)){
            return $data;
        }
        $data = array_column($ret, 'id');
        return $data;
    }
    /**
     *获得未处理的审核节点
     *
     * @param $objectType
     * @param $objectID
     * @param int $version
     * @return array
     */
    public function getUnDealReviewNodes($objectType, $objectID, $version = 1){
        $data = [];
        $statusArray = ['pending', 'wait'];
        $ret = $this->dao->select('id')
            ->from(TABLE_REVIEWNODE)
            ->Where('objectID')->eq($objectID)
            ->andWhere('objectType')->eq($objectType)
            ->andWhere('version')->eq($version)
            ->andWhere('status')->in($statusArray)
            ->fetchAll();
        if(empty($ret)){
            return $data;
        }
        $data = array_column($ret, 'id');
        return $data;
    }


    /**
     *获得审核节点
     *
     * @param  int  $nodeId
     * @access public
     * @return void
     */
    public function getReviewNodeById($nodeId){
        $data = new stdclass();
        if(!$nodeId) {
            return $data;
        }
        $ret = $this->dao->select('*')->from(TABLE_REVIEWNODE)->where('id')->eq($nodeId)->fetch();
        if(!empty($ret)){
            $data = $ret;
        }
        return $data;
    }

    /**
     *获得某节点未操作的用户信息
     *
     * @param $nodeId
     * @return array
     */
    public function getUnActionReviewersByNodeId($nodeId){
        $data = [];
        if(!$nodeId){
            return $data;
        }
        $statusArray = ['pending', 'wait'];
        $ret = $this->dao->select('reviewer')->from(TABLE_REVIEWER)
            ->where('node')->eq($nodeId)
            ->andWhere('status')->in($statusArray)
            ->fetchAll();

        if(empty($ret)){
            return $data;
        }
        
        $data = array_column($ret, 'reviewer');
        return $data;
    }

    /**
     *获得某节点已经审核的用户
     *
     * @param $nodeId
     * @return array
     */
    public function getReviewedReviewersByNodeId($nodeId){
        $data = [];
        if(!$nodeId){
            return $data;
        }
        $statusArray = ['pass', 'reject'];
        $ret = $this->dao->select('reviewer')->from(TABLE_REVIEWER)
            ->where('node')->eq($nodeId)
            ->andWhere('status')->in($statusArray)
            ->fetchAll();

        if(empty($ret)){
            return $data;
        }

        $data = array_column($ret, 'reviewer');
        return $data;
    }

    /**
     * 获得当前审核节点最大stage
     *
     * @param $objectID
     * @param $objectType
     * @param $version
     * @return mixed
     */
    public function getReviewMaxStage($objectID, $objectType, $version){
        $maxStage = 0;
        if(!($objectID && $objectType)){
            return  $maxStage;
        }
        $ret = $this->dao->select('stage')
            ->from(TABLE_REVIEWNODE)
            ->Where('objectID')->eq($objectID)
            ->andWhere('objectType')->eq($objectType)
            ->andWhere('version')->eq($version)
            ->orderBy('stage desc')
            ->limit(1)
            ->fetch();
        if($ret){
            $maxStage = $ret->stage;
        }
        return  $maxStage;
    }

    /**
     * 获得当前审核节点默认stage
     *
     * @param $objectID
     * @param $objectType
     * @param $version
     * @return mixed
     */
    public function getReviewDefaultStage($objectID, $objectType, $version){
        $maxStage = $this->loadModel('review')->getReviewMaxStage($objectID, $objectType, $version);
        $stage    = $maxStage + 1;
        return $stage;
    }


    /**
     * 获得当前版本信息最后节点评审状态
     *
     * @param $objectID
     * @param $objectType
     * @param $version
     * @return mixed
     */
    public function getReviewLastStatus($objectID, $objectType, $version){
        $lastStatus = '';
        if(!($objectID && $objectType)){
            return  $lastStatus;
        }
        $ret = $this->dao->select('status')
            ->from(TABLE_REVIEWNODE)
            ->Where('objectID')->eq($objectID)
            ->andWhere('objectType')->eq($objectType)
            ->andWhere('version')->eq($version)
            ->orderBy('stage desc')
            ->limit(1)
            ->fetch();
        if($ret){
            $lastStatus = $ret->status;
        }
        return  $lastStatus;
    }

    /**
     *获得审核节点的默认状态
     *
     * @param $objectID
     * @param $objectType
     * @param $version
     * @return string
     */
    public function getReviewNodeDefaultStatus($objectID, $objectType, $version){
        $status = 'pending';
        $lastStatus = $this->loadModel('review')->getReviewLastStatus($objectID, $objectType, $version);
        if($lastStatus == 'pending' || $lastStatus == 'wait'){
            $status = 'wait';
        }
        return $status;
    }

    /**
     *获取邮件抄送人
     *
     * @param $objectID
     * @param $objectType
     * @param string $before
     * @param int $version
     * @return string
     */
    public function getSendMailCcList($objectID, $objectType, $before = '', $version = 0){
        $ccList = '';
        $detailList = $this->dao->select('*')->from(TABLE_CONSUMED)
            ->where('objectID')->eq($objectID)
            ->andWhere('objectType')->eq($objectType)
            ->beginIF(is_array($before))->andWhere('`after`')->in($before)->fi()
            ->beginIF(!is_array($before))->andWhere('`after`')->eq($before)->fi()
            ->beginIF($version > 0)->andWhere('`version`')->eq($version)->fi()
            ->fetchAll();
        if(!$detailList){
            return $ccList;
        }
        $list =  '';
        foreach ($detailList as $detail) {
            $list .= $detail->mailto.',';
        }

        $ccList  = trim($list,',');
        return $ccList;
    }

    /**
     *获得审核节点id
     *
     * @param $objectType
     * @param $objectID
     * @param $version
     * @param $nodeCode
     * @return int
     */
    public function getReviewNodeId($objectType, $objectID, $version, $nodeCode){
        $nodeId = 0;
        if(!($objectID && $objectType && $nodeCode)){
            return  $nodeId;
        }
        $ret = $this->dao->select('id')
            ->from(TABLE_REVIEWNODE)
            ->Where('objectID')->eq($objectID)
            ->andWhere('objectType')->eq($objectType)
            ->andWhere('version')->eq($version)
            ->andWhere('nodeCode')->eq($nodeCode)
            ->fetch();
        if($ret){
            $nodeId = $ret->id;
        }
        return  $nodeId;
    }

    /**
     *获得审核节点的最大版本
     *
     * @param $objectID
     * @param $objectType
     * @return int|void
     */
    public function getObjectReviewNodeMaxVersion($objectID, $objectType){
        $maxVersion = 0;
        if(!($objectID && $objectType)){
            return $maxVersion;
        }
        $ret = $this->dao->select('version')
            ->from(TABLE_REVIEWNODE)
            ->Where('objectID')->eq($objectID)
            ->andWhere('objectType')->eq($objectType)
            ->orderBy('version_desc')
            ->fetch();
        if(!empty($ret)){
            $maxVersion = $ret->version;
        }
        return $maxVersion;
    }


    /**
     *忽略审核节点和审核人
     *
     * @param $nodeIds
     * @return bool
     */
    public function ignoreReviewNodeAndReviewers($nodeIds){
        if(!empty($nodeIds)){
            $statusArray = ['pending', 'wait'];
            //更新reviewer表
            $this->dao->update(TABLE_REVIEWER)->set('status')->eq('ignore')
                ->where('node')->in($nodeIds)
                ->andWhere('status')->in($statusArray)
                ->exec();
            //更新reviewnode表
            $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('ignore')
                ->where('id')->in($nodeIds)
                ->exec();
        }
        return true;
    }

    /**
     * 获得审核的版本列表
     *
     * @param $objectType
     * @param $objectIds
     * @return array
     */
    public function getReviewVersionList($objectType, $objectIds){
        $data = [];
        if(!($objectType && $objectIds)){
            return $data;
        }
        $ret = $this->dao->select('*')
            ->from(TABLE_REVIEWNODE)
            ->Where('objectType')->eq($objectType)
            ->andWhere('objectID')->In($objectIds)
            ->groupBy('objectID,version')
            ->orderBy('objectID,version')
            ->fetchAll();
        if(!empty($ret)){
            foreach ($ret as $val){
                $objectID = $val->objectID;
                $version = $val->version;
                $data[$objectID][$version] = $val;
            }
        }
        return $data;
    }

    public function getReviewRejectNodes($objectIds){
        $data = [];
        if(!($objectIds)){
            return $data;
        }
        $ret = $this->dao->select('t2.project,t1.objectID,t1.nodeCode')->from(TABLE_REVIEWNODE)->alias('t1')
            ->leftJoin(TABLE_REVIEW)->alias('t2')->on('t1.objectID=t2.id')
            ->Where('t1.objectType')->eq('review')
            ->andWhere('t1.objectID')->in($objectIds)
            ->andWhere('t1.status')->eq('reject')
            ->andWhere('t1.nodeCode')->in($this->lang->review->rejectCheckNodeCodeList)
            ->groupBy('t1.objectID, t1.nodeCode')
            ->fetchAll();

        foreach($ret as  $value){
            $data[$value->objectID][] = $value;
        }

        return $data;
    }

    /**
     *获得为审核的审核节点列表
     *
     * @param $objectType
     * @param $objectId
     * @param $version
     * @param $count
     * @param string $select
     * @return array
     */
    public function getUnReviewNodeList($objectType, $objectId, $version = 0, $select = '*', $count = 1){
        $data = [];
        if(!($objectType && $objectId)){
            return $data;
        }
        $unCheckStatusArray = ['wait', 'pending'];
        $ret = $this->dao->select($select)
            ->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq($objectType)
            ->andWhere('objectID')->eq($objectId)
            ->andWhere('version')->eq($version)
            ->andWhere('status')->in($unCheckStatusArray)
            ->orderBy('stage,id')
            ->limit($count)
            ->fetchAll();
        if($ret){
            $data = $ret;
        }
        return $data;
    }

    /**
     *获得审核节点的是审核结果
     *
     * @param $nodeId
     * @param string $reviewAction
     * @return string
     */
    public function getReviewNodeReviewAction($nodeId, $reviewAction = 'pass'){
        $reviewNodeAction = $reviewAction;
        if(!$nodeId){
            return $reviewNodeAction;
        }
        $ret = $this->dao->select('id')
            ->from(TABLE_REVIEWER)
            ->Where('node')->eq($nodeId)
            ->andWhere('status')->eq('reject')
            ->fetch();
        if($ret){
            $reviewNodeAction = 'reject';
        }
        return $reviewNodeAction;
    }

    /**
     * 修改审核节点审核人
     *
     * @param $nodeId
     * @param array $reviewers
     * @return bool
     */
    public function updateReviewersByNodeId($nodeId, $reviewers = []){
        if(!$nodeId){
            return false;
        }
        $oldReviewers = [];
        $ret = $this->dao->select('id,reviewer')
            ->from(TABLE_REVIEWER)
            ->Where('node')->eq($nodeId)
            ->fetchAll();
        if($ret){
            $oldReviewers = array_column($ret, 'reviewer');
            $ret = array_column($ret, null, 'reviewer');
        }
        //新增审核人
        $addReviewers = array_diff($reviewers, $oldReviewers);
        //删除审核人
        $delReviewers = array_diff($oldReviewers, $reviewers);
        if($delReviewers){
            $delIds = [];
            foreach ($delReviewers as $reviewer){
                $reviewerInfo = zget($ret, $reviewer);
                if($reviewerInfo){
                    $delIds[] = $reviewerInfo->id;
                }
            }
            if($delIds){
                $this->delReviewers($delIds);
            }
        }
        if($addReviewers){
            $addParams = new stdClass();
            $addParams->node        = $nodeId;
            $addParams->status      = 'pending';
            $addParams->createdBy   = $this->app->user->account;
            $addParams->createdDate = helper::today();
            foreach ($addReviewers as $reviewer){
                $addParams->reviewer = $reviewer;
            }
            $this->dao->insert(TABLE_REVIEWER)->data($addParams)->exec();
        }
        return true;
    }

    /**
     *根据节点获得审核人审核列表
     *
     * @param $objectType
     * @param $objectID
     * @param $version
     * @param $nodeCode
     * @param $statusArray
     * @param $exWhere
     * @return array
     */
    public function getReviewerListByNodeCode($objectType, $objectID, $version, $nodeCode, $statusArray = [], $exWhere = ''){
        $data = [];
        if(!($objectType && $objectID && $nodeCode)){
            return $data;
        }

        $node = $this->dao->select('id')->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq($objectType)
            ->andWhere('objectID')->eq($objectID)
            ->andWhere('version')->eq($version)
            ->beginIF(is_array($nodeCode))->andWhere('nodeCode')->in($nodeCode)->fi()
            ->beginIF(!is_array($nodeCode))->andWhere('nodeCode')->eq($nodeCode)->fi()
            ->orderBy('stage_desc,id_desc')
            ->fetch();
        if(!$node) {
            return  $data;
        }

        $ret = $this->dao->select('*')->from(TABLE_REVIEWER)
            ->where('node')->eq($node->id)
            ->beginIF(!empty($statusArray))->andWhere('status')->in($statusArray)->fi()
            ->beginIF(!empty($exWhere))->andWhere($exWhere)->fi()
            ->orderBy('id')
            ->fetchAll();
        if(!empty($ret)){
            $data = $ret;
        }
        return $data;
    }

    /**
     * @Notes: 根据某一个节点获取相应节点数据
     * @Date: 2023/3/20
     * @Time: 15:14
     * @param $objectType
     * @param $objectId
     * @param $version
     * @param $stage
     * @param string $field
     * @param string $orderBy
     */
    public function getReviewInfoByStage($objectType,$objectId,$version,$stage,$field='*',$orderBy='id_desc'){
        $reviewNodeInfo = $this->dao->select($field)
            ->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq($objectType)
            ->andWhere('objectID')->eq($objectId)
            ->andWhere('version')->eq($version)
            ->andWhere('stage')->eq($stage)
            ->orderBy($orderBy)
            ->fetch();
        return $reviewNodeInfo;
    }

    /**
     * 获得指定节点
     *
     * @param $objectType
     * @param $objectID
     * @param $version
     * @param $nodeCode
     * @param $statusArray
     * @return mixed
     */
    public function getNodeByNodeCode($objectType, $objectID, $version, $nodeCode, $statusArray = []){
        $data = [];
        if(!($objectType && $objectID && $nodeCode)){
            return $data;
        }
        $node = $this->dao->select('id')
            ->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq($objectType)
            ->andWhere('objectID')->eq($objectID)
            ->andWhere('version')->eq($version)
            ->andWhere('nodeCode')->eq($nodeCode)
            ->beginIF(!empty($statusArray))->andWhere('status')->in($statusArray)->fi()
            ->orderBy('stage_desc,id_desc')
            ->fetch();
        if(!empty($node)){
            return  $node;
        }
        return $data;
    }

    /**
     * 获取审批数据，根据stage作为key值返回
     * @param $objectType
     * @param $objectID
     * @param $version
     * @return array
     */
    public function getNodesByStage($objectType, $objectID, $version = 1)
    {
        $nodes = $this->dao->select('*')->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq($objectType)
            ->andWhere('objectID')->eq($objectID)
            ->andWhere('version')->eq($version)
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
            $data[$node->stage] = $node;
        }
        return $data;
    }

    /**
     * 设置下一节点待处理
     *
     * @param $objectType
     * @param $objectId
     * @param $version
     * @return bool
     */
    public function  setNextReviewNodePending($objectType, $objectId, $version = 0){
        if(!($objectType && $objectId)){
            return false;
        }
        $next = $this->dao->select('id')->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq($objectType)
            ->andWhere('objectID')->eq($objectId)
            ->andWhere('version')->eq($version)
            ->andWhere('status')->eq('wait')
            ->orderBy('stage,id')
            ->fetch('id');
        //有其他审核节点
        if(!$next) {
            return false;
        }
        $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('pending')->where('id')->eq($next)->exec();
        $this->dao->update(TABLE_REVIEWER)->set('status')->eq('pending')->where('node')->eq($next)->andWhere('status')->eq('wait')->exec();
        return true;
    }

    /**
     * 设置某节点待处理
     *
     * @param $nodeId
     * @return bool
     */
    public function  setReviewNodePending($nodeId){
        if(!$nodeId){
            return false;
        }
        $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('pending')->where('id')->eq($nodeId)->andWhere('status')->eq('wait')->exec();
        $this->dao->update(TABLE_REVIEWER)->set('status')->eq('pending')->where('node')->eq($nodeId)->andWhere('status')->eq('wait')->exec();
        return true;
    }

    /**
     *
     * 根据节点id获得节点信息
     *
     * @param $nodeId
     * @return mixed
     */
    public function getNodeInfoByNodeId($nodeId){
        if(!$nodeId){
            return false;
        }
        $ret = $this->dao->select('*')->from(TABLE_REVIEWNODE)
            ->where('id')->eq($nodeId)
            ->fetch();
        return $ret;
    }


    /**
     * 根据nodeCode获得审核信息
     *
     * @param $objectType
     * @param $objectId
     * @param $version
     * @param $nodeCode
     * @return mixed
     */
    public function getNodeInfoByNodeCode($objectType, $objectId, $version, $nodeCode){
        $ret = $this->dao->select('*')->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq($objectType)
            ->andWhere('objectID')->eq($objectId)
            ->andWhere('version')->eq($version)
            ->andWhere('nodeCode')->eq($nodeCode)
            ->fetch();
        return $ret;
    }

    /**
     * 根据审核节点id获得审核人员信息
     *
     * @param $nodeIds
     * @param $exWhere
     * @return array
     */
    public function  getReviewersByNodeIds($nodeIds, $exWhere = ''){
        $data = [];
        if(!$nodeIds){
            return $data;
        }
        $ret = $this->dao->select('node, reviewerType, GROUP_CONCAT(DISTINCT reviewer order by id asc) as reviewers')
            ->from(TABLE_REVIEWER)
            ->where('node')->in($nodeIds)
            ->beginIF(!empty($exWhere))->andWhere($exWhere)->fi()
            ->groupBy('node, reviewerType')
            ->fetchAll();
        if(!$ret){
            return $data;
        }

        foreach ($ret as $val){
            $nodeId = $val->node;
            $reviewerType = $val->reviewerType;
            $reviewers    = $val->reviewers;
            $reviewersArray = explode(',', $reviewers);
            if($reviewerType == 1){
                $data[$nodeId]['reviews'] = $reviewersArray;
            }else{
                $data[$nodeId]['appointUsers'] = $reviewersArray;
            }
        }
        return $data;
    }

    /**
     *获得历史审核节点
     *
     * @param $objectType
     * @param $objectId
     * @param $version
     * @param $nodeCode
     * @param $exWhere
     * @return array
     */
    public function getHistoryReviewStageList($objectType, $objectId, $version, $nodeCode = '', $exWhere = ''){
        $data = [];
        if($nodeCode){
            $stage =  $this->loadModel('review')->getNodeStage($objectType, $objectId, $version, $nodeCode);
            $ret = $this->dao->select('*')
                ->from(TABLE_REVIEWNODE)
                ->where('objectType')->eq($objectType)
                ->andWhere('objectID')->eq($objectId)
                ->andWhere('version')->eq($version)
                ->andWhere('stage')->le($stage)
                ->beginIF(!empty($exWhere))->andWhere($exWhere)->fi()
                ->orderBy('stage,id')
                ->fetchAll();
        }else{
            $ret = $this->dao->select('*')
                ->from(TABLE_REVIEWNODE)
                ->where('objectType')->eq($objectType)
                ->andWhere('objectID')->eq($objectId)
                ->andWhere('version')->eq($version)
                ->beginIF(!empty($exWhere))->andWhere($exWhere)->fi()
                ->orderBy('stage,id')
                ->fetchAll();
        }
        if($ret){
            foreach ($ret as $val){
                $nodeCode = $val->nodeCode;
                $stage = zget($this->lang->review->nodeCodeStageList, $nodeCode);
                $stageName = zget($this->lang->review->nodeStageNameList, $stage);
                $nodeCodeName = zget($this->lang->review->nodeCodeNameList, $nodeCode);
                $val->nodeCodeName = $stageName . '阶段-'. $nodeCodeName;
            }
            $data = $ret;
        }
        return $data;
    }


    /**
     * 忽略审核人
     *
     * @param $nodeId
     * @param string $exWhere
     * @return bool
     */
    public function setReviewersIgnore($nodeId, $exWhere = ''){
            $this->dao->update(TABLE_REVIEWER)->set('status')->eq('ignore')
                ->where('node')->eq($nodeId)
                ->beginIF(!empty($exWhere))->andWhere($exWhere)->fi()
                ->exec();

        return true;
    }

    /**
     * 获得该节点是否在审批中
     *
     * @param $objectType
     * @param $objectId
     * @param $version
     * @param $nodeCode
     * @return bool
     */
    public function getReviewNodeIsProcessing($objectType, $objectId, $version, $nodeCode){
        $isProcessing = false;
        if(!($objectType && $objectId && $version && $nodeCode)){
            return $isProcessing;
        }
        $reviewList = $this->getReviewerListByNodeCode($objectType, $objectId, $version, $nodeCode);
        if(!$reviewList){
            return $isProcessing;
        }
        $statusArray = array_column($reviewList, 'status');
        $endStatusArray = ['pass', 'reject'];
        $processStatusArray = ['pending'];
        if((array_intersect($endStatusArray, $statusArray)) && (array_intersect($processStatusArray, $statusArray))){
            $isProcessing = true;
        }
        return $isProcessing;
    }

    /**
     * 获得指定版本的审核列表
     *
     *
     * @param $objectType
     * @param $objectId
     * @param $version
     * @return array
     */
    public function getReviewListByVersion($objectType, $objectId, $version){
        $data = [];
        if(!($objectType && $objectId)){
            return $data;
        }

        $ret = $this->dao->select('*')
            ->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq($objectType)
            ->andWhere('objectID')->eq($objectId)
            ->andWhere('version')->eq($version)
            ->orderBy('stage,id')
            ->fetchAll();
        if(!$ret){
            return $data;
        }
        $nodeIds = array_column($ret, 'id');
        $retData = $this->dao->select('*')->from(TABLE_REVIEWER)
            ->where('node')->in($nodeIds)
            ->orderBy('id')
            ->fetchAll();
        $reviewerDataList = [];
        if($retData){
            foreach ($retData as $val){
                $node = $val->node;
                $reviewerDataList[$node][] = $val;
            }
        }
        foreach ($ret as $val){
            $nodeId = $val->id;
            $reviewerList = zget($reviewerDataList, $nodeId, []);
            $val->reviewerList = $reviewerList;
        }
        $data = $ret;
        return $data;
    }

    /**
     * 获得指定版本的审核列表
     *
     *
     * @param $objectType
     * @param $objectId
     * @return array
     */
    public function getAllVersionReviewList($objectType, $objectId){
        $data = [];
        if(!($objectType && $objectId)){
            return $data;
        }

        $ret = $this->dao->select('*')
            ->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq($objectType)
            ->andWhere('objectID')->eq($objectId)
            ->orderBy('stage,id')
            ->fetchAll();
        if(!$ret){
            return $data;
        }
        $nodeIds = array_column($ret, 'id');
        $retData = $this->dao->select('*')->from(TABLE_REVIEWER)
            ->where('node')->in($nodeIds)
            ->orderBy('id')
            ->fetchAll();
        $reviewerDataList = [];
        if($retData){
            foreach ($retData as $val){
                $node = $val->node;
                $reviewerDataList[$node][] = $val;
            }
        }
        foreach ($ret as $val){
            $nodeId = $val->id;
            $version = $val->version;
            $reviewerList = zget($reviewerDataList, $nodeId, []);
            $val->reviewerList = $reviewerList;
            $data[$version][] = $val;
        }
        return $data;
    }

    /**
     * 设置评审节点自动通过
     *
     * @param $nodeId
     * @param $comment
     * @return bool
     */
    public function setReviewNodeAutoPass($nodeId, $comment = ''){
        //修改节点审核状态
        $status = 'pass';
        $currentTime = helper::now();
        $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq($status)
            ->where('id')->eq($nodeId)
            ->exec();

        //修改评审人信息通过
        $updateParams = new stdClass();
        $updateParams->status = $status;
        $updateParams->reviewTime = $currentTime;
        $updateParams->comment    = $comment;
        $this->dao->update(TABLE_REVIEWER)->data($updateParams)
            ->where('node')->eq($nodeId)
            ->andWhere('status')->eq('pending')
            ->exec();
        return true;
    }
}

