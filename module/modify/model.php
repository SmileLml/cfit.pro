<?php

use phpseclib\Common\Functions\Objects;

class modifyModel extends model
{
    const MAXNODE           = 7;   //审批节点最大值是7
    const SYSTEMNODE        = 3;   //系统部审批节点，可跳过

    /**
     *检查是否允许驳回
     *
     * @param $info
     * @return bool
     */
    public function checkAllowReject($modify){
        return true;
        //        $res = false;
        //        if(in_array($modify->status, $this->lang->modify->allowRejectStatusList)){
        //            $res = true;
        //        }
        //        return  $res;
    }

    /**
     * Project: chengfangjinke
     * Method: reject
     * User: Tony Stark
     * Year: 2021
     * Date: 2022/05/28
     * Time: 14:44
     * Desc: This is the code comment. This method is called reject.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $infoID
     * @return false|void
     */
    public function reject($modifyID){
        $modify = $this->getByID($modifyID);
        //判断当前状态和审核中传入的状态是否一致，以防有同组中其他人修改
        $res = $this->checkAllowReject($modify);
        if(!$res){
            dao::$errors['statusError'] = $this->lang->modify->rejectError;
            return false;
        }
      /*  if(!$this->post->consumed)
        {
            dao::$errors['consumed'] = $this->lang->modify->consumedEmpty;
            return false;
        }*/
        if(!$this->post->revertReason)
        {
            dao::$errors['revertReason'] = $this->lang->modify->revertReasonEmpty ;
            return false;
        }
       /* $reg = '/^(([1-9][0-9]*)|(([0]\.\d{1}|[1-9][0-9]*\.\d{1})))$/';
        if (!preg_match($reg, $this->post->consumed))
        {
            dao::$errors['consumed'] = $this->lang->modify->consumedError;
            return false;
        }*/
        $comment = trim($this->post->comment);
        if(!$comment){
            dao::$errors['statusError'] = $this->lang->modify->rejectCommentEmpty;
            return false;
        }
        $skipReviewNodes = array_keys($this->post->skipReviewNode);
        $requiredReviewNode = '';
        if($this->post->skipReviewNode){
            $requiredReviewNode = implode(',', $skipReviewNodes);
        }
        $lastDealDate = date('Y-m-d');
        $revertReasonOld = $modify->revertReason;
        if(empty($revertReasonOld)){
            $revertReasonArray = array();
        }else{
            $revertReasonArray = json_decode($revertReasonOld);
        }
        $status = 'reject';
        //内部审核节点退回记为审核未通过，外部记为已退回
        if (in_array($modify->status,$this->lang->modify->reviewrejectStatus)){
            //如果单子被外部退回过，状态更新为已退回
            $rejectConsumed = $this->dao->select('id')
                ->from(TABLE_CONSUMED)
                ->where('objectType')->eq('modify')
                ->andWhere('objectID')->eq($modifyID)
                ->andWhere('`after`')->eq('reject')
                ->andWhere('deleted')->eq('0')->fetch();
            $status = !empty($rejectConsumed) ? 'reject' : 'reviewfailed';
        }
        //保存外部失败原因(注释原因：同步时保存外部原因)
        //$reviewFailReason = $this->getHistoryReview($modify);
        $revertReasonArray[]=array('RevertDate'=>helper::now(),'RevertReason'=>$this->post->revertReason,'RevertReasonChild'=>$this->post->revertReasonChild);
        $revertReason = json_encode($revertReasonArray);
        $this->dao->update(TABLE_MODIFY)
        ->set('status')->eq($status)
        ->set('revertBy')->eq($this->app->user->account)
        ->set('lastDealDate')->eq($lastDealDate)
        ->set('dealUser')->eq($modify->createdBy)
        ->set('requiredReviewNode')->eq($requiredReviewNode)
        //->set('reviewFailReason')->eq($reviewFailReason)
        ->set('revertReason')->eq($revertReason)->where('id')->eq($modifyID)->exec();

        $this->loadModel('consumed')->record('modify', $modifyID, '0', $this->app->user->account, $modify->status, $status, array());
        //忽略节点
        $ret = $this->loadModel('review')->setReviewNodesIgnore('modify', $modifyID, $modify->version);

        //新建关联二线，解决时间置空
//        /** @var problemModel $problemModel*/
//        $problemModel = $this->loadModel('problem');
//        if(!empty($modify->demandId)){
//            $problemModel->dealSolveTime($modify->demandId,'demand',$modify->code,true);
//        }
        /*if(!empty($modify->problemId)){
            $problemModel->dealSolveTime($modify->problemId,'problem',$modify->code,true);
        }*/
        return true;
    }



    /**
     * Project: chengfangjinke
     * Method: getList
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:45
     * Desc: This is the code comment. This method is called getList.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $action
     * @param $browseType
     * @param $queryID
     * @param $orderBy
     * @param null $pager
     * @return mixed
     */
    public function getList($browseType, $queryID, $orderBy, $pager = null)
    {
        $modifyQuery = '';
        if($browseType == 'bysearch')
        {
            $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : '';
            if($query)
            {
                $this->session->set('modifyQuery', $query->sql);
                $this->session->set('modifyForm', $query->form);
            }

            if($this->session->modifyQuery == false) $this->session->set('modifyQuery', ' 1 = 1');

            $modifyQuery = $this->session->modifyQuery;



            $modifyQuery = str_replace('AND `', ' AND `t1.', $modifyQuery);
            $modifyQuery = str_replace('AND (`', ' AND (`t1.', $modifyQuery);
            $modifyQuery = str_replace('`', '', $modifyQuery);

            if(strpos($modifyQuery, 'productRegistrationCode ') !== false)
            {
                $modifyQuery = str_replace('t1.productRegistrationCode', "t2.emisRegisterNumber", $modifyQuery);
            }
            if(strpos($modifyQuery, 'isPayment ') !== false)
            {
                $modifyQuery = str_replace('t1.isPayment', "t3.isPayment", $modifyQuery);
            }

            if(strpos($modifyQuery, 'team ') !== false)
            {
                $modifyQuery = str_replace('t1.team', "t3.team", $modifyQuery);
            }

            if(strpos($modifyQuery, ',app') !== false){
                $modifyQuery = str_replace(',app', ",t1.app", $modifyQuery);
            }
            if(strpos($modifyQuery, 'requirementId') !== false){
                $modifyQuery = str_replace('requirementId', "t1.requirementId", $modifyQuery);
            }
            if(strpos($modifyQuery, 'demandId') !== false){
                $modifyQuery = str_replace('demandId', "t1.demandId", $modifyQuery);
            }
            if(strpos($modifyQuery, 'problemId') !== false){
                $modifyQuery = str_replace('problemId', "t1.problemId", $modifyQuery);
            }
            if(strpos($modifyQuery, 'secondorderId ') !== false){
                $modifyQuery = str_replace('t1.secondorderId', "CONCAT(',', t1.secondorderId, ',')", $modifyQuery);
            }
            //退回原因（子项）搜索 json
            if (strpos($modifyQuery, 'revertReason') ){
                $queryData = explode('AND',$modifyQuery);
                foreach ($queryData as $qk=>$qv){
                    if (strpos($qv, 'revertReason')){
                        $revertArr = explode("'",$qv);
                        $str = '';
                        if (strpos('.'.$revertArr[1], '%')){
                            $str = '%';
                        }
                        $revertQueryStr = "'".$str.base64_decode(str_replace('%','',$revertArr[1])).$str."'";
                        $queryData[$qk] = $revertArr[0].$revertQueryStr.$revertArr[2];
                    }
                }
                $modifyQuery = implode('AND',$queryData);
            }

        }

        $modifys = $this->dao->select('distinct t1.*')->from(TABLE_MODIFY)->alias('t1')
            ->leftJoin(TABLE_PRODUCTENROLL)->alias('t2')
            ->on('t1.productenrollId = t2.id')
            ->leftJoin(TABLE_APPLICATION)->alias('t3')
            ->on('FIND_IN_SET(t3.id,t1.app)')
            ->where('t1.status')->ne('deleted')
            ->beginIF($browseType != 'all' and $browseType != 'bysearch')->andWhere('t1.status')->eq($browseType)->fi()
            ->beginIF($browseType == 'bysearch')->andWhere($modifyQuery)->fi()
            ->orderBy($orderBy)
            ->page($pager, 't1.id')
            ->fetchAll('id');
       // a($modifys);
        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'modify', $browseType != 'bysearch');

        return $modifys;
    }

    /**
     * Project: chengfangjinke
     * Method: buildSearchForm
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:45
     * Desc: This is the code comment. This method is called buildSearchForm.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $queryID
     * @param $actionURL
     */
    public function buildSearchForm($queryID, $actionURL)
    {
        $this->config->modify->search['actionURL'] = $actionURL;
        $this->config->modify->search['queryID']   = $queryID;
        $this->config->modify->search['params']['createdBy']['values'] = array('' => '') + $this->loadModel('user')->getPairs('noletter|noclosed');
        $this->config->modify->search['params']['createdDept']['values'] = array('' => '') + $this->loadModel('dept')->getOptionMenu();
        $apps = $this->loadModel('application')->getApplicationCodePairsSyncJinx();
        $appsList = array();
        foreach($apps as $app){
            $appsList[$app->id] = $app->name;
        }
        $this->config->modify->search['params']['app']['values']       = array('' => '') + $appsList;
        $this->config->modify->search['params']['isPayment']['values'] = $this->dao->select('`key`,value')->from(TABLE_LANG)->where('module')->eq('application')->andWhere('section')->eq('isPaymentList')->fetchPairs();
        $this->config->modify->search['params']['team']['values']      = $this->dao->select('`key`,value')->from(TABLE_LANG)->where('module')->eq('application')->andWhere('section')->eq('teamList')->fetchPairs();
        $this->config->modify->search['params']['projectPlanId']['values']     = array('' => '') + $this->loadModel('projectplan')->getAllProjects();
        $this->config->modify->search['params']['testingRequestId']['values']  = array('' => '') + $this->loadModel('testingrequest')->getCodeGiteePairs();
        $this->config->modify->search['params']['productenrollId']['values']   = array('' => '') + $this->loadModel('productenroll')->getCodeGiteePairs();
        $this->config->modify->search['params']['problemId']['values']       = array('' => '') + $this->loadModel('problem')->getPairsAbstract();
        $this->config->modify->search['params']['demandId']['values']        = array('' => '') + $this->loadModel('demand')->getPairsTitle('noclosed');
        $this->config->modify->search['params']['secondorderId']['values']   = array('' => '') + $this->dao->select('id,concat(concat(concat(code,"（"),summary),"）")')->from(TABLE_SECONDORDER)->where('deleted')->ne('1')->fetchPairs();
        $this->config->modify->search['params']['requirementId']['values']        = array('' => '') + $this->dao->select('id,concat(concat(concat(code,"（"),name),"）")')->from(TABLE_REQUIREMENT)->where('status')->ne('deleted')->fetchPairs();
        $this->config->modify->search['params']['reviewReport']['values']        = array('' => '') + $this->dao->select('id,title')->from(TABLE_REVIEW)->where('deleted')->ne('1')->fetchPairs();
        $products = $this->loadModel('product')->getList();
        $this->config->modify->search['params']['productId']['values'] = array('' => '') +  array_column($products, 'name' , 'id');

        $this->config->modify->search['params']['revertReason']['values']= array('' => '');
        $revertReasonList = $this->lang->modify->revertReasonList;
        $childTypeList = json_decode($this->lang->modify->childTypeList['all'],true);
        foreach($revertReasonList as $key=>$value){
            $this->config->modify->search['params']['revertReason']['values'] += array(base64_encode('"RevertReason":"'.$key.'"')=>$value);   //退回原因为json格式，不能只匹配key值

        }

        foreach ($childTypeList as $k=>$v) {
            foreach ($v as $vk=>$vv){
                $this->config->modify->search['params']['revertReason']['values'] += array(base64_encode('"RevertReasonChild":"'.$vk.'"')=>$revertReasonList[$k].'-'.$vv);   //退回原因为json格式，不能只匹配key值
            }
        }

        $this->loadModel('search')->setSearchParams($this->config->modify->search);
    }

    /**
     * Project: chengfangjinke
     * Method: getReviewers
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:45
     * Desc: This is the code comment. This method is called getReviewers.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $deptId
     * @return array
     */
    public function getReviewers($deptId = 0)
    {
        $users = $this->loadModel('user')->getPairs('noletter|noclosed');
        $reviewers = array();
        if(!$deptId){
            $deptId = $this->app->user->dept;
        }
        $myDept = $this->loadModel('dept')->getByID($deptId);

        // 质量部CM
        $cms = explode(',', trim($myDept->cm, ','));
        $us = array('' => '');
        foreach($cms as $c)
        {
            $us[$c] = $users[$c];
        }
        $reviewers[] = $us;

        //申请部门组长审批
        $groupUsers = explode(',', trim($myDept->groupleader, ','));
        $us = array('' => '');
        if(!empty($groupUsers)){
            foreach($groupUsers as $c)
            {
                $us[$c] = $users[$c];
            }
        }
        $reviewers[] = $us;

        // 部门负责人
        $cms = explode(',', trim($myDept->manager, ','));
        $us  = array('' => '');
        foreach($cms as $c)
        {
            $us[$c] = $users[$c];
        }
        $reviewers[] = $us;

        // 系统部
        $sysDept = $this->dao->select('id,manager')->from(TABLE_DEPT)->where('name')->eq('系统部')->fetch();
        $cms = explode(',', trim($sysDept->manager, ','));
        $us = array('' => '');
        foreach($cms as $c)
        {
            $us[$c] = $users[$c];
        }
        $reviewers[] = $us;

        // 产品经理
        $cms = explode(',', trim($myDept->po, ','));
        $us  = array('' => '');
        foreach($cms as $c)
        {
            $us[$c] = $users[$c];
        }
        $reviewers[] = $us;

        // 部门分管领导
        $cms = explode(',', trim($myDept->leader, ','));
        $us  = array('' => '');
        foreach($cms as $c)
        {
            $us[$c] = $users[$c];
        }
        $reviewers[] = $us;

        // 总经理

        $depts = $this->loadModel('dept')->getRecurveSubDeptIds(30);
        if (in_array($this->app->user->dept,$depts)){
           // 上海分公司特殊处理
            $reviewers[] = [$this->config->modify->branchManagerList => $users[$this->config->modify->branchManagerList]];
        }else{
            $reviewer = $this->dao->select('account')->from(TABLE_USER)->where('role')->eq('ceo')->fetch('account');
            $reviewers[] = array($reviewer => $users[$reviewer]);
        }

        // 产创部二线专员
        $cms = explode(',', trim($myDept->executive, ','));
        $us = array('' => '');
        foreach($cms as $c)
        {
            $us[$c] = $users[$c];
        }
        $reviewers[] = $us;

        return $reviewers;
    }

    public function submit($id)
    {
        $modify = $this->getByID($id);
        if($modify->status != 'waitsubmitted')
        {
            dao::$errors['statusError'] = $this->lang->modify->statusError;
            return false;
        }
       /* if(!$this->post->consumed)
        {
            dao::$errors['consumed'] = $this->lang->modify->consumedEmpty;
            return false;
        }*/
        $reg = '/^(([1-9][0-9]*)|(([0]\.\d{1}|[1-9][0-9]*\.\d{1})))$/';
        /*if (!preg_match($reg, $this->post->consumed))
        {
            dao::$errors['consumed'] = $this->lang->modify->consumedError;
            return false;
        }*/

        $skipReviewNodes = array_keys($this->post->skipReviewNode);
        $requiredReviewNode = '';
        if($this->post->skipReviewNode){
            $requiredReviewNode = implode(',', $skipReviewNodes);
        }

        $afterStage = 0;  //审批通过，自动前进一步
        while($afterStage < self::MAXNODE){
            if (!in_array($afterStage, $skipReviewNodes)) {  //如果跳过后的节点仍然跳过，继续前进
                $afterStage += 1;
            }
            else{  //如果节点不用继续跳过，则跳出循环
                break;
            }
        }
        if($afterStage > 0){
            $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('ignore')->where('objectType')->eq('modify')   //将跳过的节点，更新为ignore
            ->andWhere('objectID')->eq($modify->id)
                ->andWhere('version')->eq($modify->version)
                ->andWhere('status')->in(array('wait','pending'))->orderBy('stage,id')->limit($afterStage)->exec();
        }

        if($afterStage>0){
            $next = $this->dao->select('id')->from(TABLE_REVIEWNODE)->where('objectType')->eq('modify')   //查找下一节点的状态
                ->andWhere('objectID')->eq($modify->id)
                ->andWhere('version')->eq($modify->version)
                ->andWhere('status')->eq('wait')->orderBy('stage,id')->fetch('id');
            $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('pending')->where('id')->eq($next)->exec();  //更新下一节点的状态为pending
            $this->dao->update(TABLE_REVIEWER)->set('status')->eq('pending')->where('node')->eq($next)->exec();
        }


        //更新状态
        if($afterStage>0){
            $status = $this->lang->modify->reviewBeforeStatusList[$afterStage];
        }else{
            $status = 'wait';
        }

        $this->loadModel('review');
        $reviewers = $this->review->getReviewer('modify', $modify->id, $modify->version, $afterStage);
        $lastDealDate = date('Y-m-d');
        $this->dao->update(TABLE_MODIFY)
        ->set('reviewStage')->eq($afterStage)
        ->set('requiredReviewNode')->eq($requiredReviewNode)
        ->set('status')->eq($status)
        ->set('lastDealDate')->eq($lastDealDate)
        ->set('dealUser')->eq($reviewers)
        ->set('pushStatus')->eq(0)
        ->set('pushFailTimes')->eq(0)
        ->set('pushDate')->eq('')
        ->set('pushFailReason')->eq('')
        ->set('jsreturn')->eq(0)
        ->where('id')->eq($id)->exec();

        $this->loadModel('consumed')->record('modify', $id, '0', $this->app->user->account, $modify->status, $status, array());

        return true;
    }


    /**
     * Project: chengfangjinke
     * Method: submitReview
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:45
     * Desc: This is the code comment. This method is called submitReview.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $modifyID
     * @param $version
     * @param $level
     */
    private function submitReview($modifyID, $version, $level)
    {
        $myDept = $this->loadModel('dept')->getByID($this->app->user->dept);
        $status = 'pending';
        $nodes = $this->post->nodes;
        $stage = 1;
        $nodes[4] = [];    //去掉产品经理节点
        if($level == 2){
            $nodes[6] = [];
        }elseif ($level == 3){
            $nodes[5] = [];
            $nodes[6] = [];
        }
        //从小到大排序
        ksort($nodes);

        foreach($nodes as $key => $currentNodes)
        {
            if(!is_array($currentNodes)){
                $currentNodes = array($currentNodes);
            }
            $currentNodes = array_filter($currentNodes);
            $this->loadModel('review')->addNode('modify', $modifyID, $version, $currentNodes, true, $status, $stage);
            $status = 'wait';
            $stage++;
        }
    }
    /**
     * Project: chengfangjinke
     * Method: submitEditReview
     * User: wangjiurong
     * Year: 2022
     * Date: 2022/2/15
     * Time: 15:43
     * Desc: 提交编辑审核信息.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $modifyID
     * @param $version
     * @param $level
     * @return bool
     */

    public function submitEditReview($modifyID, $version, $level){
        $objectType = 'modify';
        //原审核节点及审核人
        $oldNodes = $this->loadModel('review')->getNodes($objectType, $modifyID, $version);
        //编辑后审核结点的审核人
        $nodes = $this->post->nodes;
        if($level == 2){
            $nodes[6] = [];
        }elseif ($level == 3){
            $nodes[5] = [];
            $nodes[6] = [];
        }
        ksort($nodes);

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
            if(array_diff($currentReviews, $oldReviews) || array_diff($oldReviews, $currentReviews)){
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
     * 添加问题关联
     * @param $outwardDeliveryId
     * @param $problemIds
     */
    public function addSecondLine($id, $relationIds, $type)
    {
        if(empty($relationIds)) { return; }
        $relationIds = explode(',', $relationIds);

        foreach ($relationIds as $relationId)
        {
            if(empty($relationId)){ continue; }
            $data                 = new stdClass();
            $data->objectID       = $id;
            $data->objectType     = 'modify';
            $data->relationID     = $relationId;
            $data->relationType   = $type;
            $data->createdBy      = $this->app->user->account;
            $data->createdDate    = helper::now();
            $this->dao->insert(TABLE_SECONDLINE)->data($data)->exec();

            //反相插一条
            $data->objectID     = $relationId;
            $data->objectType   = $type;
            $data->relationID   = $id;
            $data->relationType = 'modify';
            $this->dao->insert(TABLE_SECONDLINE)->data($data)->exec();
        }
    }

    /**
     * Project: chengfangjinke
     * Method: create
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:45
     * Desc: This is the code comment. This method is called create.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @return mixed
     */
    public function create()
    {
        $issubmit = $_POST['issubmit'];
        $postData = fixer::input('post')
        ->join('problemId', ',')
        ->join('demandId', ',')
        ->join('secondorderId',',')
        ->join('reviewReport',',')
        ->get();
        // 判断已关闭的问题单不可被关联
        if($postData->problemId){
            $problemIds = array_filter(explode(',', $postData->problemId));
            $res = $this->loadModel('problem')->checkIsClosed($problemIds);
            if (!$res['result']){
                dao::$errors['problemId'] = $res['msg'];
                return false;
            }
        }
        if (mb_strlen($postData->materialReviewResult)>255){
            dao::$errors['materialReviewResult'] = $this->lang->modify->ReviewResultLength;
            return false;
        }
        /* @var requirementModel $requirementModel*/
        $requirementModel = $this->loadModel('requirement');
        //状态为提交时判断必填
        if ($issubmit == 'submit'){
            //关联需求条目 需判断条目所属需求任务是否外部已删除，如果外部已删除则不允许关联
            if(!empty($postData->demandId))
            {
                $deleteOutDataStr = $requirementModel->getRequirementInfos($postData->demandId);
            }
            if(!empty($deleteOutDataStr))
            {
                dao::$errors[] = sprintf($this->lang->modify->deleteOutTip , $deleteOutDataStr);
                return false;
            }
            // 所属系统选择tcbs，判断必填
            if (in_array('1',$postData->app)){
                if ($postData->materialIsReview == ''){
                    dao::$errors['materialIsReview'] = sprintf($this->lang->modify->emptyObject , $this->lang->modify->materialIsReview);
                    return false;
                }
                if ($postData->materialIsReview == 1){
                    if ($postData->materialReviewUser == ''){
                        dao::$errors['materialReviewUser'] = sprintf($this->lang->modify->emptyObject , $this->lang->modify->materialReviewUser);
                        return false;
                    }
                    if ($postData->materialReviewResult == ''){
                        dao::$errors['materialReviewResult'] = sprintf($this->lang->modify->emptyObject , $this->lang->modify->materialReviewResult);
                        return false;
                    }
                }
            }
            //判断是否关联需求单和问题单
            if(empty($postData->problemId) && empty($postData->demandId) && empty($postData->secondorderId)){
                dao::$errors['relationTypeError'] = $this->lang->modify->relationTypeError;
                return false;
            }

            //变更锁提示
            $demandInfo = $this->loadModel('demand')->getDemandLockByIds(trim($postData->demandId,','));
            if(!empty($demandInfo)){
                $lockCode = implode(',',array_column($demandInfo,'code'));
                return dao::$errors[] =  '关联需求条目'.$lockCode.'所属需求任务或意向正在变更，当前流程锁死，待变更流程结束后再进行后续操作。';
            }

            //判断联系人
            if(empty($postData->applyUsercontact)){
                dao::$errors['applyUsercontact'] = sprintf($this->lang->modify->emptyObject , $this->lang->modify->applyUsercontact);
                return false;
            }
            //判断工作量
           /* if(empty($postData->consumed)){
                dao::$errors['consumed'] = sprintf($this->lang->modify->emptyObject , $this->lang->modify->consumed);
                return false;
            }

            $reg = '/^(([1-9][0-9]*)|(([0]\.\d{1}|[1-9][0-9]*\.\d{1})))$/';
            if (!preg_match($reg, $postData->consumed))
            {
                dao::$errors['consumed'] = $this->lang->modify->consumedError;
                return false;
            }*/
            //判断审核人员是否填写
            if(!$this->post->level){
                $checkRes = $this->checkReviewerNodesInfo($this->lang->modify->requiredReviewerList[1], $this->post->nodes);
            }else{
                $checkRes = $this->checkReviewerNodesInfo($this->lang->modify->requiredReviewerList[$this->post->level], $this->post->nodes);
            }
            if(!$checkRes){
                return false;
            }
            //判断电话号码
            if(!preg_match('/^1[0-9]{10}/', $postData->applyUsercontact)){
                dao::$errors['applyUsercontact'] = $this->lang->modify->telError;
                return false;
            }

            if($postData->planBegin >= $postData->planEnd){
                dao::$errors[] =  '【预计开始时间】应该在【预计结束时间】之前';
            }

            if($postData->type != '1' and $postData->planBegin < helper::now()){
                // 紧急变更不限制时间
                dao::$errors[] =  '【预计开始时间】应该在【当前时间】之后';
            }

            if($postData->property == 1){
                if($postData->backspaceExpectedStartTime >= $postData->backspaceExpectedEndTime){
                    dao::$errors[] =  '【预计回退开始时间】应该在【预计回退结束时间】之前';
                }

                if($postData->backspaceExpectedStartTime < helper::now()){
                    dao::$errors[] =  '【预计回退开始时间】应该在【当前时间】之后';
                }
            }
            if($postData->level == 1 && empty($postData->isReview)){
                dao::$errors['isReview'] = sprintf($this->lang->modify->emptyObject , $this->lang->modify->isReview);
                return false;
            }
            if($postData->isReview == 2 && empty($postData->reviewReport)){
                dao::$errors['reviewReport'] = sprintf($this->lang->modify->emptyObject , $this->lang->modify->reviewReport);
                return false;
            }
            if ($postData->risk == ''){
                dao::$errors['risk'] = sprintf($this->lang->modify->emptyObject , $this->lang->modify->risk);
                return false;
            }
            if ($postData->involveDatabase == ''){
                dao::$errors['involveDatabase'] = sprintf($this->lang->modify->emptyObject , $this->lang->modify->involveDatabase);
                return false;
            }
            //判断所属(外部)项目/任务是否存在
            if($postData->changeSource=='1' && empty($postData->outsidePlanId)){
                dao::$errors['outsidePlanId'] = sprintf($this->lang->modify->emptyObject , $this->lang->modify->outsidePlanId);
                return false;
            }
            if ($postData->isMakeAmends == ''){
                dao::$errors['isMakeAmends'] = sprintf($this->lang->modify->emptyObject , $this->lang->modify->isMakeAmends);
                return false;
            }
            if ($postData->isMakeAmends == 'yes' && $postData->actualDeliveryTime == ''){
                dao::$errors['actualDeliveryTime'] = sprintf($this->lang->modify->emptyObject , $this->lang->modify->actualDeliveryTime);
                return false;
            }
            if(dao::isError()){
                return false;
            }
            $demandCode = $this->loadModel('demand')->isSingleUsage($postData->demandId, 'modify');
            if(!empty($demandCode)){
                return false;
            }
        }

        // if($postData->productInfoCode != '无'){
        //     //产品编号检验
        //     $productIdArray = array_filter($postData->productId);
        //     $productCodeArray = explode(",",$postData->productInfoCode);
        //     if(count($productIdArray) != count($productCodeArray)){
        //         dao::$errors['productInfoCode'] =  '产品名称和产品编号数量不匹配';
        //     }
        //     //版本号正则
        //     $versionReg = '/^V\d+(.\d+){3}$/';
        //     foreach ($productCodeArray as $productCode) {
        //         $isVersion = false;
        //         $isFor = false;
        //         $isForEmpty = false;
        //         $codeArray = explode("-", $productCode);
        //         for ($i = 0; $i < count($codeArray); $i++) {
        //             $code = $codeArray[$i];
        //             if (preg_match($versionReg, $code)) {
        //                 $isVersion = true;
        //             }
        //             if ($code == 'for' || $code == 'For' || $code == 'FOR') {
        //                 $isFor = true;
        //                 if ($i + 1 < count($codeArray) && !empty($codeArray[$i + 1])) {
        //                     $isForEmpty = true;
        //                 }
        //             }
        //         }

        //         if (!$isVersion || !$isFor || !$isForEmpty) {
        //             dao::$errors['productInfoCode'] = '填写“无”，或请补充完善“'.$productCode.'”产品编号，例如：RE-GCCRS-PBC-SERVER-V2.1.0.3-for-CentOS6';
        //             break;
        //         }
        //     }
        // }
        $this->dao->begin(); //调试完逻辑最后开启事务
        $fixData = $this->fixOutwardDeliveryData(); //对外交付插入表的数组
        $fixData = $this->fixModifycnccData(0,$fixData);
        $fixData['issubmit'] = $issubmit;
        if ($fixData['level'] != 1){
            $fixData['isReview'] = '';
            $fixData['reviewReport'] = '';
        }
        $this->createOutwardDelivery($fixData);
        $lastId =  $this->dao->lastInsertID();
        $this->submitReviewOutwardDelivery($lastId, 1, $fixData['level']); //提交审批

        $this->loadModel('consumed')->record('modify', $lastId, '0', $this->app->user->account, '', 'waitsubmitted', array());
        $this->addSecondLine($lastId, $fixData['problemId'],'problem'); //问题关联
        $this->addSecondLine($lastId, $fixData['demandId'],'demand');  //需求关联
        $this->addSecondLine($lastId, $fixData['requirementId'],'requirement'); //需求任务关联
        $this->addSecondLine($lastId, $fixData['projectPlanId'],'project'); //
        if($fixData['testingRequestId']){
            $this->addSecondLine($lastId, $fixData['testingRequestId'],'testingRequest');
        }
        // $this->addSecondLine($lastId, $fixData['productenrollId'],'productEnroll');

        $this->tryError(1); //检查报错 1= 需要rollback
        $this->dao->commit(); //调试完逻辑最后开启事务

        //新建关联二线，解决时间置空
        $modifyRes = $this->getByID($lastId);
        /** @var problemModel $problemModel*/
//        $problemModel = $this->loadModel('problem');
//        if(!empty($modifyRes->demandId)){
//            $problemModel->dealSolveTime($modifyRes->demandId,'demand',$modifyRes->code);
//        }
        /*if(!empty($modifyRes->problemId)){
            $problemModel->dealSolveTime($modifyRes->problemId,'problem',$modifyRes->code);
        }*/
        if ((int)$_POST['abnormalCode'] > 0){
            //如果关联了异常变更单
            $this->editModifyAbnormal($lastId,$_POST['abnormalCode']);
        }
        return $lastId;
    }

    public function getRequirementId($demandIds){
        $requirementIdArr = $this->dao->select('requirementID')->from(TABLE_DEMAND)
            ->where('id')->in($demandIds)
            ->fetchAll('requirementID');
        $requirementIds = implode(',',array_keys($requirementIdArr));
        return $requirementIds;
    }

    public function submitReviewOutwardDelivery($modifycnccID, $version, $level)
    {
        $status = 'pending';
        $nodes = $this->post->nodes;
        $stage = 1;
        $nodes[4] = [];
        if(empty($nodes[1])){ // 组长可为空
            $nodes[1] = [];
        }
        if($level == 2){
            $nodes[6] = [];
        }elseif ($level == 3){
            $nodes[5] = [];
            $nodes[6] = [];
        }
        //从小到大排序
        ksort($nodes);
        //保存不校验必填，节点没有选择审核人员会造成数据错乱
        for ($i = 0;$i <= 7;$i++){
            if(!isset($nodes[$i])){
                $nodes[$i] = [];
            }
        }
        ksort($nodes);
//        a($nodes);exit;
        foreach($nodes as $key => $currentNodes)
        {
            if(!is_array($currentNodes)){
                $currentNodes = array($currentNodes);
            }
            $currentNodes = array_filter($currentNodes);
            $this->loadModel('review')->addNode('modify', $modifycnccID, $version, $currentNodes, true, $status, $stage);
            $status = 'wait';
            $stage++;
        }
    }


    /**
     * 创建对外交付表
     * @param $fixedOutwardDeliveryData
     * @return mixed
     */
    private function createOutwardDelivery($data)
    {
        $data = (object)$data;
        return $this->dao->insert(TABLE_MODIFY)
            ->data($data)
            ->checkIF($data->isBusinessCooperate=='2','cooperateDepNameList','notempty')
            ->checkIF($data->isBusinessCooperate=='2','businessCooperateContent','notempty')
            ->checkIF($data->isBusinessJudge=='2','judgeDep','notempty')
            ->checkIF($data->isBusinessJudge=='2','judgePlan','notempty')
            ->checkIF($data->isBusinessAffect=='2','businessAffect','notempty')
            ->checkIF($data->property=='1','backspaceExpectedStartTime','notempty')
            ->checkIF($data->property=='1','backspaceExpectedEndTime','notempty')
           /* ->checkIF($data->changeSource=='1','controlTableFile','notempty')*/
           /* ->checkIF($data->changeSource=='1','controlTableSteps','notempty')*/
            ->batchCheckIF($_POST['issubmit'] != 'save',$this->config->modify->create->requiredFields, 'notempty')
            ->exec();
    }

    /**
     * 整理入库数组
     * @param int $update
     * @return array
     */
    private function fixOutwardDeliveryData($update = 0)
    {
        $postData = fixer::input('post')
            ->join('problemId', ',')
            ->join('demandId', ',')
            ->join('secondorderId', ',')
            ->join('requirementId', ',')
            ->join('projectPlanId', ',')
            ->join('CBPprojectId', ',')
            ->join('productId', ',')
            ->join('release', ',')
            ->join('reviewReport', ',')
            ->join('app', ',')
            ->get();
        if($update == 0){
            $outwarddelivery['createdBy'] = $this->app->user->account;
            $outwarddelivery['createdDept'] = $this->app->user->dept;
            $outwarddelivery['createdDate'] = helper::now();
            $outwarddelivery['code'] = $this->getCode();
            $outwarddelivery['ifMediumChanges'] = 1; //1= 没有介质变化
            $outwarddelivery['version']   = 1;
            //需求收集2646
            //$outwarddelivery['jxLevel']     = 3;
        }
        $outwarddelivery['isDiskDelivery'] = '0';
        $outwarddelivery['outwardDeliveryDesc'] = $postData->outwardDeliveryDesc ?? "";
        $outwarddelivery['testingRequestId'] = isset($postData->testingRequestId) ? intval($postData->testingRequestId) : 0;
        // $outwarddelivery['productenrollId'] = isset($postData->productEnrollId) ? intval($postData->productEnrollId) : 0;
        $outwarddelivery['editedBy'] = $this->app->user->account;
        $outwarddelivery['editedDate'] = helper::now();
        if(!empty($postData->problemId)){
            $problemIdArray = explode(',', str_replace(' ', '', $postData->problemId));
            $problemIds = ",";
            foreach ($problemIdArray as $item) {
                if(!empty($item)){
                    $problemIds =  $problemIds.$item.",";
                }
            }
            $outwarddelivery['problemId'] = $problemIds;
        }else{
            $outwarddelivery['problemId'] = '';
        }
        if(!empty($postData->demandId)){
            $demandIdArray = explode(',', str_replace(' ', '', $postData->demandId));
            $demandIds = ",";
            foreach ($demandIdArray as $item) {
                if(!empty($item)){
                    $demandIds =  $demandIds.$item.",";
                }
            }
            $requirementIds = $this->getRequirementId($demandIds);
            $outwarddelivery['requirementId'] = $requirementIds;
            $outwarddelivery['demandId'] = $demandIds;
        }else{
            $outwarddelivery['requirementId'] = '';
            $outwarddelivery['demandId'] = '';
        }
        if(!empty($postData->secondorderId)){
            $secondorderIdArray = explode(',', str_replace(' ', '', $postData->secondorderId));
            $secondorderIds = ",";
            foreach ($secondorderIdArray as $item) {
                if(!empty($item)){
                    $secondorderIds =  $secondorderIds.$item.",";
                }
            }
            $outwarddelivery['secondorderId'] = $secondorderIds;
        }else{
            $outwarddelivery['secondorderId'] = '';
        }
        if(!empty($postData->app)){
            $appArray = explode(',', str_replace(' ', '', $postData->app));
            $apps = ",";
            foreach ($appArray as $item) {
                if(!empty($item)){
                    $apps =  $apps.$item.",";
                }
            }
            $outwarddelivery['app'] = $apps;
        }else{
            $outwarddelivery['app'] = '';
        }
        // $outwarddelivery['productLine'] = $postData->productLine ?? '';
        $outwarddelivery['contactName'] = $this->app->user->realname ?? '';
        $outwarddelivery['contactTel'] = $postData->applyUsercontact ?? '';
        if(!empty($postData->productId)){
            $productIdArray = explode(',', str_replace(' ', '', $postData->productId));
            $productIds = ",";
            foreach ($productIdArray as $item) {
                if(!empty($item)){
                    $productIds =  $productIds.$item.",";
                }
            }
            $outwarddelivery['productId'] = $productIds;
        }else{
            $outwarddelivery['productId'] = '';
        }
        $outwarddelivery['implementationForm'] = $postData->implementationForm ?? '';
        $outwarddelivery['projectPlanId'] = $postData->projectPlanId ?? '';
        $outwarddelivery['isReview'] = $postData->isReview ?? '';
        // $outwarddelivery['isReviewPass'] = empty($postData->isReviewPass)? 0 : $postData->isReviewPass;
        if($postData->isReview == 2){
            $outwarddelivery['reviewReport'] = trim($postData->reviewReport,',') ?? '';
        }else{
            //$fixedData['isReviewPass'] = '';
            $outwarddelivery['reviewReport'] = '';
        }
        $CBPprojectId = array();
        $requirementId = array();
        if($postData->testingRequestId){
            $testRequestObj = $this->loadModel('testingrequest')->getByID($postData->testingRequestId);
            $CBPprojectId[] = $testRequestObj->CBPprojectId;
            //$requirementId = array_merge($requirementId,explode(',', trim($testRequestObj->requirementId, ',')));
        }
        // if($postData->productEnrollId){
        //     $productenrollObj = $this->loadModel('productenroll')->getByID($postData->productEnrollId);
        //     $CBPprojectId[] = $productenrollObj->CBPprojectId;
        //     $requirementId = array_merge($requirementId,explode(',', trim($productenrollObj->requirementId, ',')));
        // }
        $CBPprojectId = array_unique($CBPprojectId);
        //$requirementId = array_unique($requirementId);
        $outwarddelivery['CBPprojectId'] = implode(',', $CBPprojectId);
        //$outwarddelivery['requirementId'] = implode(',', $requirementId);
        $outwarddelivery['release'] = $postData->release ?? '';
        // $outwarddelivery['productInfoCode'] = $postData->productInfoCode ?? '';
        $outwarddelivery['reviewStage'] = $postData->reviewStage ?? 0;
        $outwarddelivery['status']    = $postData->status ?? "waitsubmitted";
        $outwarddelivery['level']     = $postData->level ?? 0;
        $outwarddelivery['ROR']   = $postData->ROR ?? '';
        $outwarddelivery['dealUser']   = $this->app->user->account;
        return $outwarddelivery;
    }

    /**
     * 检查必填项
     * @param $data
     */
    private function checkParams($data, $fields)
    {
        $fieldArray = explode(',', str_replace(' ', '', $fields));
        foreach ($fieldArray as $item)
        {
            if(is_null($data[$item]) || $data[$item] == ''){
                $itemName = $this->lang->modify->$item ?? $item;
                dao::$errors[$item] =  sprintf($this->lang->modify->emptyObject, $itemName);
            }
        }
        if(is_null($data['productId']) || $data['productId'] == '' || $data['productId'] == ','){
            dao::$errors[] =  sprintf($this->lang->modify->emptyObject, $this->lang->modify->productName);
        }
    }

    private function getCode()
    {
        $number = $this->dao->select('count(id) c')->from(TABLE_MODIFY)->where('code')->like('CFIT-CJ-' . date('Ymd-')."%")->fetch('c') ;
        $number = intval($number) + 1;
        $code   = 'CFIT-CJ-' . date('Ymd-') . sprintf('%02d', $number);
        return $code;
    }

    /**
     * 成功变更数据整合
     * @param int $update
     * @return array
     */
    public function fixModifycnccData($update = 0,$fixedData)
    {

        $postData = fixer::input('post')
            ->join('problemId', ',')
            ->join('demandId', ',')
            ->join('projectPlanId', ',')
            ->join('emergencyBackWay', ',')
            ->join('riskAnalysis', ',')
            ->join('relate', ',')
            ->join('relateNum', ',')
            ->join('node', ',')
            ->join('app', ',')
            ->join('feasibilityAnalysis', ',')
            ->join('productCode', ',')
            ->join('assignProduct', ',')
            ->join('versionNumber', ',')
            ->join('supportPlatform', ',')
            ->join('hardwarePlatform', ',')
            ->join('relatedDemandNum', ',')
            ->join('CBPprojectId', ',')
            ->join('requirementId', ',')
            ->setIF($this->post->isBusinessCooperate=='1', 'cooperateDepNameList', '')
            ->setIF($this->post->isBusinessCooperate=='1', 'businessCooperateContent', '')
            ->setIF($this->post->isBusinessJudge=='1', 'judgeDep', '')
            ->setIF($this->post->isBusinessJudge=='1', 'judgePlan', '')
            ->setIF($this->post->isBusinessAffect=='1', 'businessAffect', '')
            ->setIF($this->post->property=='2', 'backspaceExpectedStartTime', '')
            ->setIF($this->post->property=='2', 'backspaceExpectedEndTime', '')
            ->setIF($this->post->changeSource!='1', 'controlTableFile', '')
            ->setIF($this->post->changeSource!='1', 'controlTableSteps', '')
            ->setIF($this->post->changeSource!='1', 'outsidePlanId', '')
            ->get();


        /* 处理风险分析和应急处置 */
        $isNull = false;
        foreach (explode(',',$postData->emergencyBackWay) as $key => $emergencyBackWay)
        {
            if($emergencyBackWay == '' or explode(',',$postData->riskAnalysis)[$key] == '')
            {
            $isNull = true;
            }
        }
        if($isNull)
        {
            if ($postData->issubmit != 'save'){
                return dao::$errors[] = $this->lang->modify->emptyRiskAnalysisEmergencyHandle;
            }
        }
        else
        {
            $riskAnalysisEmergencyHandle = array();
            for($i=0;$i<count(explode(',',$postData->emergencyBackWay));++$i)
            {
            $obj = new stdclass();
            $obj->emergencyBackWay = explode(',',$postData->emergencyBackWay)[$i];
            $obj->riskAnalysis = explode(',',$postData->riskAnalysis)[$i];
            $riskAnalysisEmergencyHandle[$i] = $obj;
            }
            $fixedData['riskAnalysisEmergencyHandle'] = json_encode($riskAnalysisEmergencyHandle);
        }
        $fixedData['node'] = $postData->node ?? '';
        if(!empty($postData->feasibilityAnalysis)){
            $feasibilityAnalysisArray = explode(',', str_replace(' ', '', $postData->feasibilityAnalysis));
            $feasibilityAnalysiss = ",";
            foreach ($feasibilityAnalysisArray as $item) {
                if(!empty($item)){
                    $feasibilityAnalysiss =  $feasibilityAnalysiss.$item.",";
                }
            }
            $fixedData['feasibilityAnalysis'] = $feasibilityAnalysiss;
        }else{
            $fixedData['feasibilityAnalysis'] = '';
        }
        $fixedData['mode'] = $postData->mode ?? '';
        $fixedData['changeSource'] = $postData->changeSource ?? '';
        $fixedData['changeStage'] = $postData->changeStage ?? '';
        $fixedData['implementModality'] = $postData->implementModality ?? '';
        $fixedData['type'] = $postData->type ?? '';
        $fixedData['isBusinessCooperate'] = $postData->isBusinessCooperate ?? '';
        $fixedData['isBusinessJudge'] = $postData->isBusinessJudge ?? '';
        $fixedData['isBusinessAffect'] = $postData->isBusinessAffect ?? '';
        $fixedData['property'] = $postData->property ?? '';
        $fixedData['desc'] = $postData->desc ?? '';
        $fixedData['planBegin'] = $postData->planBegin ?? '';
        $fixedData['planEnd'] = $postData->planEnd ?? '';
        $fixedData['target'] = $postData->target ?? '';
        $fixedData['reason'] = $postData->reason ?? '';
        $fixedData['step'] = $postData->step ?? '';
        $fixedData['techniqueCheck'] = $postData->techniqueCheck ?? '';
        $fixedData['checkList'] = $postData->checkList ?? '';
        $fixedData['changeContentAndMethod'] = $postData->changeContentAndMethod ?? '';
        $fixedData['cooperateDepNameList'] = $postData->cooperateDepNameList ?? '';
        $fixedData['businessCooperateContent'] = $postData->businessCooperateContent ?? '';
        $fixedData['judgeDep'] = $postData->judgeDep ?? '';
        $fixedData['judgePlan'] = $postData->judgePlan ?? '';
        $fixedData['controlTableFile'] = trim($postData->controlTableFile) ?? '';
        $fixedData['controlTableSteps'] = trim($postData->controlTableSteps) ?? '';
        $fixedData['risk'] = $postData->risk ?? '';
        $fixedData['effect'] = $postData->effect ?? '';
        $fixedData['businessFunctionAffect'] = $postData->businessFunctionAffect ?? '';
        $fixedData['backupDataCenterChangeSyncDesc'] = $postData->backupDataCenterChangeSyncDesc ?? '';
        $fixedData['emergencyManageAffect'] = $postData->emergencyManageAffect ?? '';
        $fixedData['businessAffect'] = $postData->businessAffect ?? '';
        // $fixedData['benchmarkVerificationType'] = $postData->benchmarkVerificationType ?? '';
        $fixedData['verificationResults'] = $postData->verificationResults ?? '';
        $fixedData['applyUsercontact'] = $postData->applyUsercontact ?? '';
        $fixedData['classify'] = $postData->classify ?? 0;
        $fixedData['status']    = $postData->status ?? "waitsubmitted";
        $fixedData['backspaceExpectedStartTime']    = $postData->backspaceExpectedStartTime ?? "";
        $fixedData['backspaceExpectedEndTime']    = $postData->backspaceExpectedEndTime ?? "";
        $fixedData['operationType']    = $postData->operationType ?? "1";
        $fixedData['preChange']    = $postData->preChange;
        $fixedData['postChange']    = $postData->postChange;
        $fixedData['synImplement']    = $postData->synImplement;
        $fixedData['pilotChange']    = $postData->pilotChange;
        $fixedData['promotionChange']    = $postData->promotionChange;
        $fixedData['outsidePlanId']    = $postData->outsidePlanId;
        $fixedData['materialIsReview']     = $postData->materialIsReview;
        $fixedData['materialReviewUser']   = $postData->materialReviewUser;
        $fixedData['materialReviewResult'] = $postData->materialReviewResult;
        $fixedData['isMakeAmends']         = $postData->isMakeAmends;
        $fixedData['actualDeliveryTime']   = $postData->isMakeAmends == 'yes' ? $postData->actualDeliveryTime : '';
        $fixedData['involveDatabase']      = $postData->involveDatabase;
//        $fixedData['abnormalCode']    = $postData->abnormalCode;
        return $fixedData;
    }

    /**
     * 尝试报错 或需要rollback
     */
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
     * Project: chengfangjinke
     * Method: update
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:45
     * Desc: This is the code comment. This method is called update.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $modifyID
     * @return array
     */
    public function edit($id)
    {
        $postData = fixer::input('post')
            ->join('problemId', ',')
            ->join('demandId', ',')
            ->join('secondorderId',',')
            ->join('reviewReport',',')
            ->get();
        $oldOutwardDeliveryData = $this->getByID($id);
        // 判断已关闭的问题单不可被关联
        if($postData->problemId){
            $problemIds = array_filter(explode(',', $postData->problemId));
            $res = $this->loadModel('problem')->checkIsClosed($problemIds);
            if (!$res['result']){
                dao::$errors['problemId'] = $res['msg'];
                return false;
            }
        }

        //判断是否关联需求单和问题单
        $statusOld = $oldOutwardDeliveryData->status;
        if(!in_array($statusOld,$this->lang->modify->alloweditStatus))
        {
            dao::$errors['editError'] = $this->lang->modify->editStatusError;
            return false;
        }
        if (mb_strlen($postData->materialReviewResult)>255){
            dao::$errors['materialReviewResult'] = $this->lang->modify->ReviewResultLength;
            return false;
        }
        /* @var requirementModel $requirementModel*/
        $requirementModel = $this->loadModel('requirement');
        if ($postData->issubmit == 'submit'){
            //关联需求条目 需判断条目所属需求任务是否外部已删除，如果外部已删除则不允许关联
            if(!empty($postData->demandId))
            {
                $deleteOutDataStr = $requirementModel->getRequirementInfos($postData->demandId);
            }
            if(!empty($deleteOutDataStr))
            {
                dao::$errors[] = sprintf($this->lang->modify->deleteOutTip , $deleteOutDataStr);
                return false;
            }
            // 所属系统选择tcbs，判断必填
            if (in_array('1',$postData->app)){
                if ($postData->materialIsReview == ''){
                    dao::$errors['materialIsReview'] = sprintf($this->lang->modify->emptyObject , $this->lang->modify->materialIsReview);
                    return false;
                }
                if ($postData->materialIsReview == 1){
                    if ($postData->materialReviewUser == ''){
                        dao::$errors['materialReviewUser'] = sprintf($this->lang->modify->emptyObject , $this->lang->modify->materialReviewUser);
                        return false;
                    }
                    if ($postData->materialReviewResult == ''){
                        dao::$errors['materialReviewResult'] = sprintf($this->lang->modify->emptyObject , $this->lang->modify->materialReviewResult);
                        return false;
                    }
                }
            }

            if(empty($postData->problemId) && empty($postData->demandId) && empty($postData->secondorderId)){
                dao::$errors['relationTypeError'] = $this->lang->modify->relationTypeError;
                return false;
            }

            if(!empty($postData->demandId)){
                //迭代三十加变更锁
                $demandInfo = $this->loadModel('demand')->getDemandLockByIds(trim($postData->demandId,','));
                if(!empty($demandInfo)){
                    $lockCode = implode(',',array_column($demandInfo,'code'));
                    dao::$errors[] = sprintf('关联需求条目'.$lockCode.'所属需求任务或意向正在变更，当前流程锁死，待变更流程结束后再进行后续操作。');
                }
            }

            //判断联系人
            if(empty($postData->applyUsercontact)){
                dao::$errors['applyUsercontact'] = sprintf($this->lang->modify->emptyObject , $this->lang->modify->applyUsercontact);
                return false;
            }
            //判断工作量
           /* if(empty($postData->consumed)){
                dao::$errors['consumed'] = sprintf($this->lang->modify->emptyObject , $this->lang->modify->consumed);
                return false;
            }*/
            if($oldOutwardDeliveryData->jsreturn == '1' && empty($postData->ROR)){//外部退回必填
                dao::$errors['ROR'] = sprintf($this->lang->modify->emptyObject , $this->lang->modify->ROR);
                return false;
            }
            //判断审核人员是否填写
            if(!$this->post->level){
                $checkRes = $this->checkReviewerNodesInfo($this->lang->modify->requiredReviewerList[1], $this->post->nodes);
            }else{
                $checkRes = $this->checkReviewerNodesInfo($this->lang->modify->requiredReviewerList[$this->post->level], $this->post->nodes);
            }
            if(!$checkRes){
                return false;
            }
            //判断电话号码
            if(!preg_match('/^1[0-9]{10}/', $postData->applyUsercontact)){
                dao::$errors['applyUsercontact'] = $this->lang->modify->telError;
                return false;
            }
            if ($postData->involveDatabase == ''){
                dao::$errors['involveDatabase'] = sprintf($this->lang->modify->emptyObject , $this->lang->modify->involveDatabase);
                return false;
            }

           /* $reg = '/^(([1-9][0-9]*)|(([0]\.\d{1}|[1-9][0-9]*\.\d{1})))$/';
            if (!preg_match($reg, $postData->consumed))
            {
                dao::$errors['consumed'] = $this->lang->modify->consumedError;
                return false;
            }*/

            if($postData->planBegin >= $postData->planEnd){
                dao::$errors[] =  '【预计开始时间】应该在【预计结束时间】之前';
                return false;
            }

            if($postData->type != '1' and $postData->planBegin < helper::now()){
                // 紧急变更不限制时间
                dao::$errors[] =  '【预计开始时间】应该在【当前时间】之后';
                return false;
            }
            if($postData->property == 1){
                if($postData->backspaceExpectedStartTime >= $postData->backspaceExpectedEndTime){
                    dao::$errors[] =  '【预计回退开始时间】应该在【预计回退结束时间】之前';
                    return false;
                }

                if($postData->backspaceExpectedStartTime < helper::now()){
                    dao::$errors[] =  '【预计回退开始时间】应该在【当前时间】之后';
                    return false;
                }
            }
            if($postData->level == 1 && empty($postData->isReview)){
                dao::$errors['isReview'] = sprintf($this->lang->modify->emptyObject , $this->lang->modify->isReview);
                return false;
            }
            if($postData->isReview == 2 && empty($postData->reviewReport)){
                dao::$errors['reviewReport'] = sprintf($this->lang->modify->emptyObject , $this->lang->modify->reviewReport);
                return false;
            }
            if ($postData->isMakeAmends == ''){
                dao::$errors['isMakeAmends'] = sprintf($this->lang->modify->emptyObject , $this->lang->modify->isMakeAmends);
                return false;
            }
            if ($postData->isMakeAmends == 'yes' && $postData->actualDeliveryTime == ''){
                dao::$errors['actualDeliveryTime'] = sprintf($this->lang->modify->emptyObject , $this->lang->modify->actualDeliveryTime);
                return false;
            }
            //若该条目存在非终态的在途流程，则弹窗提示
            $demandCode = $this->loadModel('demand')->isSingleUsage($postData->demandId, 'modify', $id);
            if(!empty($demandCode)){
                return false;
            }

            //判断所属(外部)项目/任务是否存在
            if($postData->changeSource=='1' && empty($postData->outsidePlanId)){
                dao::$errors['outsidePlanId'] = sprintf($this->lang->modify->emptyObject , $this->lang->modify->outsidePlanId);
                return false;
            }
            if ($postData->risk == ''){
                dao::$errors['risk'] = sprintf($this->lang->modify->emptyObject , $this->lang->modify->risk);
                return false;
            }
        }

        $fixData = $this->fixOutwardDeliveryData(1);
        $fixData = $this->fixModifycnccData(1,$fixData);
        if ('save' != $oldOutwardDeliveryData->issubmit && ($oldOutwardDeliveryData->status=='reject' || $oldOutwardDeliveryData->status == 'reviewfailed')){
            $fixData['version'] = $oldOutwardDeliveryData->version + 1;
        }else{
            $fixData['version'] = $oldOutwardDeliveryData->version;
        }
        //修改评审记录
        $rorOld = $oldOutwardDeliveryData->ROR;
        if(empty($rorOld)){
            $array = array();
        }else{
            $array = json_decode(json_encode($rorOld), true);
        }
        if ($oldOutwardDeliveryData->jsreturn=='1'){
            $arrayNew = [];
            $arrayNew['RORDate'] = helper::now();
            $arrayNew['RORContent'] = $postData->ROR;
            array_push($array, $arrayNew);
        }
        $fixData['ROR'] = json_encode($array);
        if($oldOutwardDeliveryData->level !=$fixData['level']){
            $fixData['requiredReviewNode'] = '';
        }

        $fixData['issubmit'] = $_POST['issubmit'];
        //如果生产变更状态为【内部不通过，已退回】并且保存时，状态不变（迭代33）
        if('save' == $fixData['issubmit'] && in_array($oldOutwardDeliveryData->status, ['reject', 'reviewfailed'])){
            $fixData['status']  = $oldOutwardDeliveryData->status;
        }
        $fixData['release'] = $oldOutwardDeliveryData->release;
        $this->dao->begin(); //调试完逻辑最后开启事务
        $this->update($id, $fixData);

        //检查审核信息
        if('save' != $oldOutwardDeliveryData->issubmit && ($oldOutwardDeliveryData->status == 'reject' || $oldOutwardDeliveryData->status == 'reviewfailed'))
        {
            //$oldOutwardDeliveryData->status      = 'waitsubmitted';
            $oldOutwardDeliveryData->reviewStage = 0;
            $this->submitReviewOutwardDelivery($id, $oldOutwardDeliveryData->version + 1, $fixData['level']);
            $this->loadModel('consumed')->record('modify', $id, '0', $this->app->user->account, $oldOutwardDeliveryData->status, $fixData['status'], array());
            //当前为退回状态

        }else if($oldOutwardDeliveryData->status == 'wait'){
            $oldOutwardDeliveryData->reviewStage = 0;
            $this->delNode($id,'modify',$oldOutwardDeliveryData->version);
            $this->submitReviewOutwardDelivery($id,$oldOutwardDeliveryData->version,$fixData['level']);
//            $this->submitEditReviewOutwardDelivery($id, $oldOutwardDeliveryData->version, $fixData['level']);
            $this->loadModel('consumed')->record('modify', $id,  '0', $this->app->user->account, 'wait', 'waitsubmitted', array());
        }else{
            $this->delNode($id,'modify',$oldOutwardDeliveryData->version);
            $this->submitReviewOutwardDelivery($id,$oldOutwardDeliveryData->version,$fixData['level']);
            $lastConsumed = $this->loadModel('consumed')->getLastConsumed($id, 'modify');
            $this->loadModel('consumed')->update($lastConsumed->id,  $lastConsumed->objectType, $id,  '0',$this->app->user->account,
                $lastConsumed->before, $lastConsumed->after);
        }

        $this->removeSecondLine($id); //将原来的关系删除 建立新所有的关系
        $this->addSecondLine($id, $fixData['problemId'],'problem'); //问题关联
        $this->addSecondLine($id, $fixData['demandId'],'demand');  //需求关联
        $this->addSecondLine($id, $fixData['requirementId'],'requirement'); //需求任务关联
        $this->addSecondLine($id, $fixData['projectPlanId'],'project'); //
        if($fixData['testingRequestId']){
            $this->addSecondLine($id, $fixData['testingRequestId'],'testingRequest');
        }

        $this->editModifyAbnormal($id,(int)$_POST['abnormalCode']);
        $this->tryError(1); //检查报错 1= 需要rollback
        $this->dao->commit(); //调试完逻辑最后开启事务
        return common::createChanges($oldOutwardDeliveryData, (Object)$fixData);
    }

    public function removeSecondLine($id)
    {
        $this->dao->update(TABLE_SECONDLINE)->set('deleted = "1" where (objectType = "modify" and objectID = '.$id.') or (relationType = "modify" and relationID = '.$id.') ')->exec();
    }

    public function submitEditReviewOutwardDelivery($outwardDeliveryId, $version, $level){
        $objectType = 'modify';
        //原审核节点及审核人
        $oldNodes = $this->loadModel('review')->getNodes($objectType, $outwardDeliveryId, $version);
        //编辑后审核结点的审核人
        $nodes = $this->post->nodes;
        if(empty($nodes[1])){ // 组长可为空
            $nodes[1] = [];
        }
        if($level == 2){
            $nodes[6] = [];
        }elseif ($level == 3){
            $nodes[5] = [];
            $nodes[6] = [];
        }
        ksort($nodes);
        foreach ($nodes as $nk=>$node){
            if (count($node) > 1 && $node[0] == ''){
                unset($node[0]);
                $nodes[$nk] = array_values($node);
            }
        }
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
            if(array_diff($currentReviews, $oldReviews) || array_diff($oldReviews, $currentReviews)){
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
     * 更新
     * @param $id
     * @return mixed
     */
    public function update($id, $data)
    {
        $data = (object)$data;
        $res = $this->dao->update(TABLE_MODIFY)
            ->data($data)
            ->checkIF($data->isBusinessCooperate=='2','cooperateDepNameList','notempty')
            ->checkIF($data->isBusinessCooperate=='2','businessCooperateContent','notempty')
            ->checkIF($data->isBusinessJudge=='2','judgeDep','notempty')
            ->checkIF($data->isBusinessJudge=='2','judgePlan','notempty')
            ->checkIF($data->isBusinessAffect=='2','businessAffect','notempty')
            ->checkIF($data->property=='1','backspaceExpectedStartTime','notempty')
            ->checkIF($data->property=='1','backspaceExpectedEndTime','notempty')
            /*->checkIF($data->changeSource=='1','controlTableFile','notempty')*/
            /*->checkIF($data->changeSource=='1','controlTableSteps','notempty')*/
            ->batchCheckIF($_POST['issubmit'] != 'save',$this->config->modify->edit->requiredFields, 'notempty')
            ->where('id')->eq((int)$id)->exec();
        return $res;
    }

        /**
     * 删除原状态流转工作量
     * @param $id
     */
    private function deleteWaitConsume($id)
    {
        $this->dao->update(TABLE_CONSUMED)
            ->set('deleted')->eq('1')
            ->where('objectID')->eq($id)
            ->andWhere('objectType')->eq('modify')
            ->andWhere('after')->eq('wait')
            ->exec();
    }

    /**
     * Project: chengfangjinke
     * Method: getByID
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:45
     * Desc: This is the code comment. This method is called getByID.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $modifyID
     * @return mixed
     */
    public function getByID($id)
    {
        if(empty($id)) return null;
        $data = $this->dao->select('*')->from(TABLE_MODIFY)
            ->where('id')->eq($id)
            ->fetch();
        $data->ROR = json_decode($data->ROR, true);

        if($data->app)
        {
            $appsArr = explode(',',trim($data->app,','));
            $apps = $this->dao->select('id,code,CASE WHEN code != "" THEN concat(concat(code,"_"),name) ELSE name END as name')->from(TABLE_APPLICATION)->where('id')->in($appsArr)->fetchAll('id');
            $appsInfo = array();
            foreach($appsArr as $appId){
                $appsInfo[$appId] = zget($apps, $appId);
            }

            $data->appsInfo=$appsInfo;
        }
        if($data->riskAnalysisEmergencyHandle)
        {
            $data->riskAnalysisEmergencyHandle = json_decode($data->riskAnalysisEmergencyHandle);
        }else{
            $data->riskAnalysisEmergencyHandle = json_decode('[{"emergencyBackWay":"","riskAnalysis":""}]');
        }
        $data->releases = [];
        if($data->release)
        {
            $releases = $this->loadModel('project')->getReleasesList($data->projectPlanId);
            foreach(explode(',', $data->release) as $r)
            {
                if(!$r) continue;

                $files = $this->dao->select('*')->from(TABLE_FILE)->where('objectType')->eq('release')
                     ->andWhere('objectID')->eq($r)
                     ->andWhere('deleted')->eq(0)
                     ->fetchAll();

                $release = new stdclass();
                $release->id    = $r;
                $release->name  = $releases[$r]->name;
                $release->path  = $releases[$r]->path;
                $release->files = $files;

                $data->releases[] = $release;
            }
        }
        $cs = $this->dao->select('*')->from(TABLE_CONSUMED)->where('objectType')->eq('modify') //状态流转 工作量
        ->andWhere('objectID')->eq($id)
            ->andWhere('deleted')->ne(1)
            ->orderBy('id_asc')
            ->fetchAll();
        $data->consumed = $cs;

        if(!$data->dealUser){
            $data->dealUser = $this->loadModel('review')->getReviewer('modify', $data->id, $data->version, $data->reviewStage);
        }
        if (in_array($data->status,$this->lang->modify->reissueArray) || $data->status == 'modifysuccess' || $data->status == 'cancel'){
            $data->dealUser = '';
        }
        if ($data->actualDeliveryTime == '0000-00-00 00:00:00') $data->actualDeliveryTime = '';
        // 上海分公司审核节点名称修改
        $this->resetNodeAndReviewerName($data->createdDept);

        return $data;
    }

    /**
     * @Notes:根据id集合获取数据
     * @Date: 2023/12/6
     * @Time: 9:55
     * @Interface getByIds
     * @param array $ids
     * @param string $field
     */
    public function getByIds($ids = [],$field = '*')
    {
        return $this->dao->select($field)->from(TABLE_MODIFY)->where('id')->in($ids)->fetchAll();
    }

    /**
     * 接口请求最后一个记录
     * @param $id
     */
    public function getRequestLog($id)
    {

        $log = $this->dao->select('id,`status`,response,requestDate')->from(TABLE_REQUESTLOG)->where('objectType')->eq('modify')
        ->andWhere('objectId')->eq($id)
        ->andWhere('purpose')->eq('pushModfiyCommit')
        ->orderBy('id_desc')->fetch();
        if(!$log){
            $log = $this->dao->select('id,`status`,response,requestDate')->from(TABLE_REQUESTLOG)->where('objectType')->eq('modify')
            ->andWhere('objectId')->eq($id)
            ->andWhere('purpose')->eq('pushModfiyInitiate')
            ->orderBy('id_desc')->fetch();
        }

        if(isset($log->response)){
            $log->response = json_decode($log->response);
        }
        return $log;
    }

    /**
     * Project: chengfangjinke
     * Method: close
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:45
     * Desc: This is the code comment. This method is called close.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $modifyID
     */
    public function close($id)
    {
        $comment = trim($this->post->comment);
        if(!$comment){
            dao::$errors['statusError'] = $this->lang->modify->rejectCommentEmpty;
            return false;
        }
        $modify = $this->getByID($id);
        $changeFlag = isset($this->config->changeCloseSwitch) && $this->config->changeCloseSwitch == 1;
        //取消变更开关关闭，执行原来逻辑
        if(!$changeFlag && !empty($modify->externalCode)){
            dao::$errors[''] = $this->lang->modify->closeNotice;
            return false;
        }
        //变更单为终态不能取消
        $statusEnd = array_merge(['closed','modifysuccess'], $this->lang->modify->reissueArray);
        if($changeFlag && in_array($modify->status, $statusEnd)){
            dao::$errors[''] = $this->lang->modify->statusEndNotice;
            return false;
        }
        //变更单在外部不能取消
        if($changeFlag && !in_array($modify->status, $this->lang->modify->allowRejectArray)){
            dao::$errors[''] = $this->lang->modify->closeNoticeNew;
            return false;
        }

        //$data['closed'] = 1;
        $data['closeReason'] = $this->post->comment;
        $data['closedDate']  = helper::now();
        $data['closedBy']    = $this->app->user->account;
        $data['status']      = 'modifycancel';
        $data['dealUser']    = '';
        //如果变更单之前流转到外部，取消变更单后需同步金信
        if($changeFlag && !empty($modify->externalCode)){
            $modify->cancelReason = $data['closeReason'];
            $response = $this->syncModfiyClose($modify);
            $response = json_decode($response);
            if(isset($response->code) && $response->code != 0){
                dao::$errors[''] = $this->lang->modify->syncModfiyClose;
                return false;
            }
        }
        $res = $this->dao->update(TABLE_MODIFY)
            ->data($data)
            ->where('id')->eq((int)$id)->exec();

        //修改审批结论
        $nodes = $this->dao->select('*')->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq('modify')
            ->andWhere('objectID')->eq($id)
            ->andWhere('version')->eq($modify->version)
            ->andWhere('status')->in(array('wait', 'pending'))
            ->orderBy('stage,id')
            ->fetchAll();
        $ns = array();
        foreach($nodes as $node) $ns[] = $node->id;
        if(!empty($ns)){
            $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('wait')
                ->where('id')->in($ns)
                ->andWhere('status')->in(array('wait', 'pending'))
                ->exec();
            $this->dao->update(TABLE_REVIEWER)->set('status')->eq('wait')
                ->where('node')->in($ns)
                ->andWhere('status')->in(array('wait', 'pending'))
                ->exec();
        }

        $this->loadModel('consumed')->record('modify', $id, 0, $this->app->user->account, $modify->status, 'modifycancel', array());
        $this->loadModel('demand')->changeBySecondLineV4($id,'modify');
        return $res;
    }


    public function closeOld($id)
    {
        if(!$this->post->closeConfirm){
            dao::$errors['statusError'] = $this->lang->modify->closedComfireEmpty;
            return false;
        }
        $comment = trim($this->post->comment);
        if(!$comment){
            dao::$errors['statusError'] = $this->lang->modify->rejectCommentEmpty;
            return false;
        }
        $modify = $this->getByID($id);
        $data['status'] = $this->post->closeConfirm;
        $data['dealUser'] = '';

        $res = $this->dao->update(TABLE_MODIFY)
            ->data($data)
            ->where('id')->eq((int)$id)->exec();

        $this->loadModel('consumed')->record('modify', $id, $this->post->consumed, $this->app->user->account, $modify->status, $this->post->closeConfirm, array());

        return $res;
    }

    /**
     * Project: chengfangjinke
     * Method: review
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:45
     * Desc: This is the code comment. This method is called review.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $modifyID
     * @return false|void
     */
    public function review($modifyID)
    {
        $modify = $this->getByID($modifyID);
        //判断当前状态和审核中传入的状态是否一致，以防有同组中其他人修改
        $res = $this->checkAllowReview($modify, $this->post->version, $this->post->reviewStage, $this->app->user->account);
        if(!$res['result']){
            dao::$errors['statusError'] = $res['message'];
            return false;
        }

        $extra  = new stdClass();
        if($modify->reviewStage == 2 and ((in_array(3,explode(',',$modify->requiredReviewNode)) == 1 and $modify->isOutsideReject == '1') or $modify->isOutsideReject == '0') and $modify->status != 'cancel')
        {
            if(!$this->post->isNeedSystem)
            {
                dao::$errors['isNeedSystem'] = $this->lang->modify->systemEmpty;
                return false;
            }

            $extra = $this->post->isNeedSystem == 'yes' ? true : false;
        }
        if(7 == $modify->reviewStage && 'pass' == $this->post->result && ('0' !== $_POST['isDiskDelivery'] && empty($_POST['isDiskDelivery']))) {
            dao::$errors['isDiskDelivery'] = sprintf($this->lang->modify->emptyObject, $this->lang->modify->isDiskDelivery);
        }

        if (dao::$errors) return dao::$errors;
        $is_all_check_pass = false;
        //reviewfailed为内部未通过，但是reviewnoe表里需要存reject
        $postResult = $this->post->result;
        if ($postResult == 'reviewfailed'){
            $postResult = 'reject';
        }
        if($modify->status != 'cancel') {
            //$result = $this->loadModel('review')->check('modify', $modifyID, $modify->version, $postResult, $this->post->comment, '', $extra, $is_all_check_pass);
            $result = $this->loadModel('common')->check('modify', $modifyID, $modify->version, $postResult, $this->post->comment, '', $extra, $is_all_check_pass, $res['reviewsOriginal'], $res['reviews'], $res['reviewAuthorize']);
        }else {
            $result = $this->post->result;
        }
        //生产变更单状态需要存reviewfailed
        if ($result == 'reject'){
            $result = 'reviewfailed';
        }

        if($result == 'pass' && $modify->status != 'cancel')
        {
            if($modify->requiredReviewNode || $modify->isOutsideReject){
                $requiredStage = explode(',', $modify->requiredReviewNode); //修改过审批节点的，以修改的为准
            }else{
                $requiredStage = $this->lang->modify->requiredReviewerList[ $modify->level ] ;  //不同表单类型的审批节点不同
            }

            $afterStage = $modify->reviewStage + 1;  //审批通过，自动前进一步
            while($afterStage < self::MAXNODE){
                if ( $afterStage == self::SYSTEMNODE and $this->post->isNeedSystem == 'no') { $afterStage += 1; }  //如果跳过系统部审批，则再前进一步

                if ( ! in_array($afterStage, $requiredStage )) {  //如果跳过后的节点仍然跳过，继续前进
                    $afterStage += 1;
                }
                else{  //如果节点不用继续跳过，则跳出循环
                    break;
                }
            }
            if($afterStage - $modify->reviewStage > 1){
                $reviewList = $this->dao->select('id,stage')->from(TABLE_REVIEWNODE)
                    ->where('objectType')->eq('modify')   //将跳过的节点，更新为ignore
                    ->andWhere('objectID')->eq($modify->id)
                    ->andWhere('version')->eq($modify->version)
                    ->andWhere('status')->eq('wait')->orderBy('stage,id')
                    ->limit($afterStage - $modify->reviewStage - 1)
                    ->fetchall();
                $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('ignore')->where('objectType')->eq('modify')   //将跳过的节点，更新为ignore
                ->andWhere('objectID')->eq($modify->id)
                    ->andWhere('version')->eq($modify->version)
                    ->andWhere('status')->eq('wait')->orderBy('stage,id')->limit($afterStage - $modify->reviewStage - 1)->exec();
                // 审核人员置为忽略/跳过
                $modify->release = explode(',', trim($modify->release,','));
                $checkSystemRes = $this->loadModel('build')->checkSystemPass($modify->release);
                foreach ($reviewList as $k=>$v){
                    $updateData = new stdClass();
                    $updateData->status = 'ignore';
                    $updateData->comment = '';
//                    if ($modify->reviewStage + 1 == self::SYSTEMNODE and $this->post->isNeedSystem == 'no' and $checkSystemRes){
//                        $updateData->comment = '已在制版菜单完成审批';
//                    }
                    $this->dao->update(TABLE_REVIEWER)->data($updateData)->where('node')->in($v->id)->exec();
                }
            }
            $next = $this->dao->select('id')->from(TABLE_REVIEWNODE)->where('objectType')->eq('modify')   //查找下一节点的状态
            ->andWhere('objectID')->eq($modify->id)
                ->andWhere('version')->eq($modify->version)
                ->andWhere('status')->eq('wait')->orderBy('stage,id')->fetch('id');

            if($next)
            {
                $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('pending')->where('id')->eq($next)->exec();  //更新下一节点的状态为pending
                $this->dao->update(TABLE_REVIEWER)->set('status')->eq('pending')->where('node')->eq($next)->exec();

                $this->loadModel('review');
                $reviewers = $this->review->getReviewer('modify', $modify->id, $modify->version, $afterStage);
                $this->dao->update(TABLE_MODIFY)->set('dealUser')->eq($reviewers)->where('id')->eq($modify->id)->exec();
            }
            //更新状态
            if(isset($this->lang->modify->reviewBeforeStatusList[$afterStage])){
                $status = $this->lang->modify->reviewBeforeStatusList[$afterStage];
            }
            $lastDealDate = date('Y-m-d');
            if('waitqingzong' == $status && '1' == $this->post->isDiskDelivery){
                $data = [
                    'isDiskDelivery' => $this->post->isDiskDelivery,
                    'status'         => 'waitImplement',
                    'dealUser'       => $modify->createdBy,
                    'lastDealDate'   => $lastDealDate,
                ];
                $this->dao->update(TABLE_MODIFY)
                    ->data($data)
                    ->where('id')->eq($modifyID)->exec();
                // 状态联动
                $this->loadModel('demand')->changeBySecondLineV4($modifyID,'modify');
                $this->loadModel('consumed')->record('modify', $modifyID, 0, $this->app->user->account, $modify->status, $data['status'], array(), '');
            }else{
                if(strtotime($modify->createdDate) < strtotime('2022-09-30')
                    &&strtotime($modify->editedDate) < strtotime('2022-09-30')
                    &&$status == 'waitqingzong'){//历史数据处理
                    $this->dao->update(TABLE_MODIFY)
                        ->set('reviewStage')->eq($afterStage)
                        ->set('status')->eq('productsuccess')
                        ->set('lastDealDate')->eq($lastDealDate)
                        ->beginIF($this->post->isMediaChanged)->set('ifMediumChanges')->eq($this->post->isMediaChanged)->fi()
                        ->where('id')->eq($modifyID)->exec();
                    $this->loadModel('consumed')->record('modify', $modifyID, '0', $this->app->user->account, $modify->status, 'productsuccess', array());
                    $this->dao->update(TABLE_MODIFY)->set('dealUser')->eq('')->where('id')->eq($modifyID)->exec();
                }else{
                    $this->dao->update(TABLE_MODIFY)
                        ->set('reviewStage')->eq($afterStage)
                        ->set('status')->eq($status)
                        ->set('lastDealDate')->eq($lastDealDate)
                        ->beginIF($this->post->isMediaChanged)->set('ifMediumChanges')->eq($this->post->isMediaChanged)->fi()
                        ->where('id')->eq($modifyID)->exec();
                    $this->loadModel('consumed')->record('modify', $modifyID, '0', $this->app->user->account, $modify->status, $status, array());
                    //如果状态为”待同步金信“,更新当前审批字段
                    if($status == 'waitqingzong') {
                        //修改待处理人为金科
                        $this->dao->update(TABLE_MODIFY)->set('dealUser')->eq('guestjk')->where('id')->eq($modifyID)->exec();
                    }
                }
            }
        }
        elseif($result == 'pass' && $modify->status == 'cancel') {
            $cancelReviewDate =  date('Y-m-d H:m:s');
            $cancelComment =  $this->post->comment;
            if($modify->externalId && $modify->externalCode){
                $status = 'canceltojx';
            }else{
                $status = 'canceled';
            }
            $this->dao->update(TABLE_MODIFY)
                ->set('reviewStage')->eq($modify->lastStage)
                ->set('cancelStatus')->eq('canceltojx')
                ->set('status')->eq($status)
                ->set('cancelComment')->eq($cancelComment)
                ->set('cancelReviewDate')->eq($cancelReviewDate)
                ->set('dealUser')->eq(' ')
                ->where('id')->eq($modifyID)->exec();

            $_POST['cancelStatus'] = 'canceltojx';
            $this->loadModel('consumed')->record('modify', $modifyID, '0', $this->app->user->account, $modify->status, 'canceltojx', array());
        }
        elseif($result == 'reviewfailed' && $modify->status != 'cancel')
        {
            //如果单子被外部退回过，状态更新为已退回（迭代33）
            $rejectConsumed = $this->dao->select('id')
                ->from(TABLE_CONSUMED)
                ->where('objectType')->eq('modify')
                ->andWhere('objectID')->eq($modifyID)
                ->andWhere('`before`')->eq('reject')
                ->andWhere('deleted')->eq('0')->fetch();
            $resultStatus = !empty($rejectConsumed) ? 'reject' : 'reviewfailed';
            $lastDealDate = date('Y-m-d');
            $this->dao->update(TABLE_MODIFY)
                ->set('status')->eq($resultStatus)
                ->set('lastDealDate')->eq($lastDealDate)
                ->set('dealUser')->eq($modify->createdBy)
                ->where('id')->eq($modifyID)->exec();
            $this->loadModel('consumed')->record('modify', $modifyID, '0', $this->app->user->account, $modify->status, $resultStatus, array());
        }
        elseif($result == 'reviewfailed' && $modify->status == 'cancel') {
            $cancelReviewDate =  date('Y-m-d H:m:s');
            $cancelComment =  $this->post->comment;
            $this->dao->update(TABLE_MODIFY)
                ->set('reviewStage')->eq($modify->lastStage)
                ->set('cancelStatus')->eq('cancelback')
                ->set('status')->eq($modify->lastStatus)
                ->set('dealUser')->eq($modify->lastDealUser)
                ->set('cancelComment')->eq($cancelComment)
                ->set('cancelReviewDate')->eq($cancelReviewDate)
                ->where('id')->eq($modifyID)->exec();
            $_POST['cancelStatus'] = 'cancelback';
            $this->loadModel('consumed')->record('modify', $modifyID, '0', $this->app->user->account, $modify->status, $modify->lastStatus, array());
        }
    }

    /**
     * Project: chengfangjinke
     * Method: feedback
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:45
     * Desc: This is the code comment. This method is called feedback.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $modifyID
     * @return array
     */
    public function feedback($modifyID)
    {
        $oldProblem = $this->getByID($modifyID);

        $data = fixer::input('post')->get();
        $data->status = 'feedbacked';
        $this->dao->update(TABLE_MODIFY)->data($data)->where('id')->eq($modifyID)->exec();

        return common::createChanges($oldProblem, $data);
    }

    /**
     * Project: chengfangjinke
     * Method: run
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:45
     * Desc: This is the code comment. This method is called run.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $modifyID
     */
    public function run($modifyID)
    {
        $modify = $this->getByID($modifyID);
        $data = new stdClass();
        $data = fixer::input('post')
            ->remove('uid,consumed,comment')
            ->stripTags($this->config->modify->editor->run['id'], $this->config->allowedTags)
            ->get();
        if($modify->status =='productsuccess'){

            $data->status = 'closing';
            $data->supply = implode(',',  $this->post->supply);
            $reviewers = $this->loadModel('modify')->getSecondLineReviewers($modify->id, $modify->version, $modify->reviewStage);
            $data->dealUser = $reviewers;
        }else if($modify->status == 'waitImplement'){
            $data->status = $this->post->status;
            $data->dealUser = '';
            if($data->realStartTime==''){
                dao::$errors['realStartTime'] = sprintf($this->lang->modify->emptyObject , $this->lang->modify->actualBegin);
                return false;
            }
            if($data->realEndTime==''){
                dao::$errors['realEndTime'] = sprintf($this->lang->modify->emptyObject , $this->lang->modify->actualEnd);
                return false;
            }
            if($data->realStartTime > $data->realEndTime){
                dao::$errors[] =  '【实际开始时间】应该在【实际结束时间】之前';
                return false;
            }
        }

        //迭代十四，根据填写的实际结束时间同步需求条目的实际上线时间
        if($data->realStartTime != '0000-00-00' || $data->realEndTime != ''){
            $this->dealDemandOnlineDate($modifyID,$data->realEndTime);
        }
        $data = $this->loadModel('file')->processImgURL($data, $this->config->modify->editor->run['id'], $this->post->uid);
        $this->dao->update(TABLE_MODIFY)->data($data)->where('id')->eq($modifyID)->exec();
        $this->loadModel('file')->updateObjectID($this->post->uid, $modifyID, 'modify');
        $this->file->saveUpload('modify', $modifyID);
        // 状态联动
        $this->loadModel('demand')->changeBySecondLineV4($modifyID,'modify');
        $this->loadModel('consumed')->record('modify', $modifyID, $this->post->consumed, $this->app->user->account, $modify->status, $data->status, array());
    }

    /**
     * Desc: 根据填写的实际结束时间同步需求条目的实际上线时间
     * Date: 2022/8/15
     * Time: 10:36
     *
     * @param $modifyID
     * @param $date
     *
     */
    public function dealDemandOnlineDate($modifyID,$date)
    {
        $modify = $this->getByID($modifyID);
        if($modify){
            $demand = $modify->demand ?? '';
            if(!empty($demand)){
                if(substr($demand,0,1) == ','){
                    $demand = substr($demand,1);
                }
                $demandIDArr = explode(',',$demand);
                $this->dao->update(TABLE_DEMAND)->set('onlineDate')->eq($date)->where('id')->in($demandIDArr)->exec();
            }
        }
    }

    /**
     * Project: chengfangjinke
     * Method: link
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:46
     * Desc: This is the code comment. This method is called link.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $modifyID
     * @return false|void
     */
    public function link($modifyID)
    {
        $modify = $this->getByID($modifyID);
        //判断当前状态和审核中传入的状态是否一致，以防有同组中其他人修改
        $res = $this->checkAllowReview($modify, $this->post->version,  $this->post->reviewStage, $this->app->user->account);
        if(!$res['result']){
            dao::$errors['statusError'] = $res['message'];
            return false;
        }

        if(!array_filter($this->post->release))
        {
            dao::$errors['release'] = $this->lang->modify->releaseEmpty;
            return false;
        }
        $config = $this->dao->select('`key`,`value`')->from(TABLE_LANG)->where('section')->eq('mediaCheckList')->fetchPairs('key');
        if ($config['link'] == 1) { //校验开关
            foreach ($this->post->release as $releaseId) {
                if (empty($releaseId)) continue; //多选有空
                $release = $this->loadModel('projectrelease')->getPath($releaseId);
                if (!$this->projectrelease->checkPath($release->path, $release->name)) {
                    dao::$errors['release'] = dao::$errors['path'];
                }
            }
            unset(dao::$errors['path']);
            if (dao::$errors['release']) {
                return false;
            }
        }
        if (dao::$errors) return dao::$errors;
        $data = new stdClass();
        $data->release      = trim(implode(',', $this->post->release), ',');
        if($modify->requiredReviewNode){
            $requiredStage = explode(',', $modify->requiredReviewNode); //修改过审批节点的，以修改的为准
        }else{
            $requiredStage = $this->lang->modify->requiredReviewerList[ $modify->level ] ;  //不同表单类型的审批节点不同
        }
        $afterStage = 1;  //审批通过，自动前进一步
        while($afterStage < self::MAXNODE){
            if (!in_array($afterStage, $requiredStage)) {  //如果跳过后的节点仍然跳过，继续前进
                $afterStage += 1;
            }
            else{  //如果节点不用继续跳过，则跳出循环
                break;
            }
        }
        if($afterStage - $modify->reviewStage > 1){
            $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('ignore')->where('objectType')->eq('modify')   //将跳过的节点，更新为ignore
            ->andWhere('objectID')->eq($modify->id)
                ->andWhere('version')->eq($modify->version)
                ->andWhere('status')->eq('wait')->orderBy('stage,id')->limit($afterStage - $modify->reviewStage - 1)->exec();
        }

        $next = $this->dao->select('id')->from(TABLE_REVIEWNODE)->where('objectType')->eq('modify')   //查找下一节点的状态
        ->andWhere('objectID')->eq($modify->id)
            ->andWhere('version')->eq($modify->version)
            ->andWhere('status')->eq('wait')->orderBy('stage,id')->fetch('id');

        if($next)
        {
            $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('pending')->where('id')->eq($next)->exec();  //更新下一节点的状态为pending
            $this->dao->update(TABLE_REVIEWER)->set('status')->eq('pending')->where('node')->eq($next)->exec();

            $this->loadModel('review');
            $reviewers = $this->review->getReviewer('modify', $modify->id, $modify->version, $afterStage);
            $this->dao->update(TABLE_MODIFY)->set('dealUser')->eq($reviewers)->where('id')->eq($modify->id)->exec();
        }
        //更新状态
        if(isset($this->lang->modify->reviewBeforeStatusList[$afterStage])){
            $status = $this->lang->modify->reviewBeforeStatusList[$afterStage];
        }

        $data->reviewStage  = $afterStage;
        $data->status       = $status;
        $data->lastDealDate = date('Y-m-d');
        $data->ifMediumChanges = $this->post->isMediaChanged;
        if($modify->cancelStatus == 'cancel') {
            $data->cancelStatus = '';
        }

        $productInfoArray = array();
        foreach ($this->post->release as $item){
            $release = $this->dao->select('`desc`')->from(TABLE_RELEASE)->where('id')->eq($item)->fetch('desc');
            $release = trim(strip_tags(str_replace("&nbsp;",",",htmlspecialchars_decode($release))));
            if(!in_array($release, $productInfoArray)){
                array_push($productInfoArray, $release);
            }
        }
        $productInfoStr = trim(implode(',',$productInfoArray),',');
        $data->productInfoCode = $productInfoStr;

        $this->dao->update(TABLE_MODIFY)->data($data)->autoCheck()->batchCheck($this->config->modify->link->requiredFields, 'notempty')
             ->where('id')->eq($modifyID)->exec();
        //一个人审核通过就可以
        $is_all_check_pass = false;
        //$this->loadModel('review')->check('modify', $modifyID, $modify->version, 'pass', $this->post->comment, 0, null, $is_all_check_pass);
        //切换授权管理审批
        $this->loadModel('common')->check('modify', $modifyID, $modify->version, 'pass', $this->post->comment, 0, null, $is_all_check_pass, $res['reviewsOriginal'], $res['reviews'], $res['reviewAuthorize']);

        /* 下个节点设为pending */
        $this->loadModel('review');
        $reviewers = $this->review->getReviewer('modify', $modify->id, $modify->version, $afterStage);
        $this->dao->update(TABLE_MODIFY)->set('dealUser')->eq($reviewers)->where('id')->eq($modify->id)->exec();
        $this->loadModel('consumed')->record('modify', $modifyID, '0', $this->app->user->account, $modify->status, $status, array());
    }

    /**
     * Project: chengfangjinke
     * Method: isClickable
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:46
     * Desc: This is the code comment. This method is called isClickable.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $modify
     * @param $action
     * @return bool
     */
    public static function isClickable($modify, $action)
    {
        global $app,$lang;
        $action = strtolower($action);
        //单子删除后，所有按钮不可见
        if($modify->status == 'deleted'){
            return false;
        }
        $reviewers = (new modifyModel())->getSecondLineReviewers($modify->id, $modify->version);

        if($action == 'edit') return (in_array($modify->status,$lang->modify->alloweditStatus)) and ($app->user->account == $modify->createdBy or $app->user->account == 'admin') and ($modify->closed != 1);
//        if($action == 'reject') return $modify->status != 'withexternalapproval' and ($modify->closed != 1);
        if($action == 'reject') return in_array($modify->status,$lang->modify->allowRejectArray) and ($modify->closed != 1);
        if($action == 'review') return  strpos(",$modify->dealUser,", ",{$app->user->account},") !== false and !in_array($modify->status,$lang->modify->noreviewStatus) and ($modify->closed != 1);
        if($action == 'submit') return $modify->status == 'waitsubmitted' and ($app->user->account == $modify->createdBy or $app->user->account == 'admin') and ($modify->closed != 1);// and ($modify->issubmit == 'submit')
        if($action == 'cancel') return ($modify->status == 'cmconfirmed' or $modify->status == 'groupsuccess' or $modify->status == 'managersuccess' or $modify->status == 'posuccess' or $modify->status == 'leadersuccess' or $modify->status == 'gmsuccess' or $modify->status == 'waitqingzong' or $modify->status == 'withexternalapproval') and ($modify->closed != 1) and ($app->user->account == $modify->createdBy or $app->user->account == 'admin');
        if($action == 'run' and $modify->status == 'waitImplement') return strpos(",$modify->dealUser,", ",{$app->user->account},") !== false;
        // 创建人未提交可以删除;
//        if($action == 'delete' and $modify->status == 'waitsubmitted' and $app->user->account == $modify->createdBy) return true;
        if ($action == 'delete') return $app->user->account == 'admin' or ($app->user->account == $modify->createdBy and $modify->status == 'waitsubmitted' and $modify->version == 1);

//        if($action == 'delete') return strpos(",$reviewers,", ",{$app->user->account},") !== false and $modify->status != 'waitsubmitted';
        // if($action == 'delete' and $modify->closed != 1) return false;
        if ($action == 'reissue') return in_array($modify->status,$lang->modify->reissueArrayNew);
        /*if($action == 'close') return empty($modify->externalCode);*/
        return true;
    }

    /**
     * Send mail.
     *
     * @param  int    $modifyID
     * @param  int    $actionID
     * @access public
     * @return void
     */
    public function sendmail($modifyID, $actionID)
    {
        $this->loadModel('mail');
        $modify = $this->getById($modifyID);
        if ($modify->issubmit == 'save'){
            return false;
        }
        $modify->dealUser = $this->loadModel('common')->getAuthorizer('modify', $modify->dealUser, $modify->status, $this->lang->modify->authorizeStatusList);
        $users  = $this->loadModel('user')->getPairs('noletter');
        $as = array();
        foreach(explode(',',trim($modify->dealUser,',')) as $dealUser){
            $as[] = zget($users, $dealUser);
        }
        $modify->dealUser = implode(',',$as);


        /* 获取后台通知中配置的邮件发信。*/
        $this->app->loadLang('custommail');
        $mailConf   = isset($this->config->global->setModifyMail) ? $this->config->global->setModifyMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf   = json_decode($mailConf);
        $browseType = 'modify';

        /* 处理邮件发信的标题和日期。*/
        $bestDeal = $this->dao->select('*')->from(TABLE_CONSUMED)->where('objectType')->eq('modify')
            ->andWhere('objectID')->eq($modifyID)
            ->orderBy('id_desc')
            ->fetch();
        $bestDate  = empty($bestDeal) ? '' : substr($bestDeal->createdDate, 0, 10);
        $mailTitle = vsprintf($mailConf->mailTitle, $mailConf->variables);

        //变更异常、部分成功、变更失败、变更成功、变更回退、变更取消 状态发送通知邮件
        if(in_array($modify->status, $this->lang->modify->noticeStatus)){
            $mailTitle= sprintf($this->lang->modify->noticetitle,zget($this->lang->modify->statusList, $modify->status));
            $mailConf->mailContent = $this->lang->modify->noticecontent;
        }

        /* Get action info. */
        $action          = $this->loadModel('action')->getById($actionID);
        $history         = $this->action->getHistory($actionID);
        $action->history = isset($history[$actionID]) ? $history[$actionID] : array();

        $isfeedback = false;
        if($action->action == 'feedbacksyn' || $action->action == 'feedbacksynedit'){
            $isfeedback = true;
        }

        $isupdatestatus = 0;
        if($modify->status == 'modifyreject' || $modify->status == 'modifycancel' || $modify->status == 'modifyrollback' || $modify->status == 'modifyfail'
            || $modify->status == 'modifysuccesspart' || $modify->status == 'modifysuccess'){
            $isupdatestatus = 1;
        }

        $isupdateapprove = 0;
        if($modify->status == 'jxSubmitImplement'){
            $isupdateapprove = 1;
        }
        /* Get mail content. */
        $oldcwd     = getcwd();
        $modulePath = $this->app->getModulePath($appName = '', 'modify');
        $viewFile   = $modulePath . 'view/sendmail.html.php';
        chdir($modulePath . 'view');

        if(file_exists($modulePath . 'ext/view/sendmail.html.php'))
        {
            $viewFile = $modulePath . 'ext/view/sendmail.html.php';
            chdir($modulePath . 'ext/view');
        }
        //$serverIp = $_SERVER['SERVER_NAME'];

        ob_start();
        include $viewFile;
        foreach(glob($modulePath . 'ext/view/sendmail.*.html.hook.php') as $hookFile) include $hookFile;
        $mailContent = ob_get_contents();
        ob_end_clean();

        chdir($oldcwd);

        $sendUsers = $this->getToAndCcList($modify,$action);
        if(!$sendUsers) return;
        list($toList, $ccList) = $sendUsers;
        if($modify->status == 'wait'){
            $deptObj = $this->loadModel('dept')->getByID('4');
            $ccList = $deptObj->qa;
        }

        // 抄送产品经理
        if($modify->status == 'gmsuccess'){
            $productIds = array_column(json_decode($modify->productCode),'assignProduct');
            $appIds = explode(",", trim($modify->app,","));
            if(!empty($productIds)){
                $POList = $this->dao->select('PO')->from(TABLE_PRODUCT)->where('id')->in($productIds)->fetchall();
                $ccList = $ccList.','.implode(',', array_column($POList, 'PO'));
            }else if(!empty($appIds)){
                $POList = $this->dao->select('PO')->from(TABLE_PRODUCT)->where('app')->in($appIds)->fetchall();
                $ccList = $ccList.','.implode(',', array_column($POList, 'PO'));
            }
        }else if($modify->status == 'wait'){
            $deptObj = $this->loadModel('dept')->getByID($modify->createdDept);
            $ccList = $deptObj->qa;
        }else if (in_array($modify->status,$this->lang->modify->reissueArray)){
            //变更异常抄送节点所有审核人 审核过的
            $res = $this->dao->select("t1.id,t1.reviewer")->from(TABLE_REVIEWER)->alias('t1')
                ->leftJoin(TABLE_REVIEWNODE)->alias('t2')
                ->on('t1.node=t2.id')
                ->where('objectType')->eq('modify')
                ->andWhere('objectID')->eq($modifyID)
                ->andWhere('t1.status')->in(['pass','reject'])
                ->fetchall();
            $ccList = array_unique(array_column($res,'reviewer'));
            $ccList = implode(',',$ccList);
        }else if($modify->status == 'modifycancel'){
            //变更取消 审核过的
            $res = $this->dao->select("t1.id,t1.reviewer")->from(TABLE_REVIEWER)->alias('t1')
                ->leftJoin(TABLE_REVIEWNODE)->alias('t2')
                ->on('t1.node=t2.id')
                ->where('objectType')->eq('modify')
                ->andWhere('objectID')->eq($modifyID)
                ->andWhere('t1.status')->in(['pass','reject'])
                ->fetchall();
            $ccList = array_unique(array_column($res,'reviewer'));
            $ccList = implode(',',$ccList);
        }

        /* 处理邮件标题。*/
        //$subject = $this->getSubject($modify);
        $subject = $mailTitle;

        /* Send emails. */
        $this->mail->send($toList, $subject, $mailContent, $ccList);
        if($this->mail->isError()) error_log(join("\n", $this->mail->getError()));
    }

    /**
     * Project: chengfangjinke
     * Method: getToAndCcList
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:46
     * Desc: This is the code comment. This method is called getToAndCcList.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $object
     * @return array
     */
    public function getToAndCcList($object,$action)
    {
        /* Set toList and ccList. */
        $ccList = '';
        $status = $object->status;
        if($status == 'reject'  || $status == 'reviewfailed'){
            $toList = $object->createdBy;  //创建者
        }else if($action->action == 'feedbacksyn' || $action->action == 'feedbacksynedit' || $action->action == 'modifysyncstatus'){
            $toList = $object->createdBy;  //创建者
            //各个审批人
            $ccList = $this->getReviewerReal($object->id, $object->version);
        }else if($status == 'jxsynfailed' || $action->action == 'jxsynccancelmodifyfail' || $status == 'modifyreject'){
            $reviewers = implode(",",array_keys($this->lang->modify->secondLineReviewList));
            $toList = $reviewers;
        }else if($status == 'waitImplement'){
            $toList = $object->createdBy;
        }else{
            $toList = $this->loadModel('review')->getReviewer('modify', $object->id, $object->version, $object->reviewStage);;
        }
        //授权管理
        $toList = $this->loadModel('common')->getAuthorizer('modify', $toList, $object->status, $this->lang->modify->authorizeStatusList);
        $ccList = $this->loadModel('common')->getAuthorizer('modify', $ccList, $object->status, $this->lang->modify->authorizeStatusList);
        return array($toList, $ccList);
    }

    /**
     * Get mail subject.
     *
     * @param  object
     * @access public
     * @return string
     */
    public function getSubject($object)
    {
        return $this->lang->modify->common  . '#' . $object->id . '-' . $object->code;
    }

    /**
     * Project: chengfangjinke
     * Method: getToAndCcList
     * User: wangjiurong
     * Year: 2022
     * Date: 2022/02/23
     * Time: 14:44
     * Desc: 检查信息是否允许当前用户审核.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $modify
     * @param $version
     * @param $reviewStage
     * @param $userAccount
     * @return array
     */
    public function checkAllowReview($modify, $version = 1,  $reviewStage = 0, $userAccount = '')
    {
        $res = array(
            'result'  => false,
            'message' => '',
        );
        if(!$modify){
            $res['message'] = $this->lang->common->errorParamId;
            return $res;
        }
        if(!$userAccount){
            $res['message'] = $this->lang->common->errorParamUser;
            return $res;
        }
        // 取消审批节点
        if($modify->status == 'cancel') {
            if(in_array($userAccount,explode(',',$modify->cancelReviewer))) {
                $res['result'] = true;
            }else {
                $res['message'] = $this->lang->review->statusUserError;
            }
            return $res;
        }
        //审核节点已经经过
        if(($version != $modify->version) || ($reviewStage != $modify->reviewStage) || ($modify->status == 'reject') || ($modify->status == 'waitsubmitted')){
            $reviewerInfo = $this->loadModel('review')->getReviewedUserInfo('modify', $modify->id, $version, $reviewStage);
            $message = $this->lang->review->statusError;
            if($reviewerInfo){
                $message = str_replace('%', $reviewerInfo->realname, $this->lang->review->statusError);
            }
            $res['message'] = $message;
            return $res;
        }
        //获得当前的的审核人信息
        $reviews =  $this->loadModel('review')->getReviewer('modify', $modify->id, $modify->version, $modify->reviewStage);
        if(!$reviews){
            $res['message'] = $this->lang->review->reviewEnd;
            return $res;
        }
        //授权管理转化人员信息
        $reviewArray = $this->loadModel('common')->getAuthorizer('modify', $reviews, $modify->status, $this->lang->modify->authorizeStatusList);
        $reviewArray = explode(',', $reviewArray);
        if(!in_array($userAccount, $reviewArray)){
            $res['message'] = $this->lang->review->statusUserError;
            return $res;
        }
        $res['result'] = true;
        $res['reviews'] = $reviewArray;
        $reviewsOriginal = explode(',', $reviews);
        $res['reviewsOriginal'] = $reviewsOriginal;
        foreach ($reviewsOriginal as $original){
            $reviewAuthorize = $this->loadModel('common')->getAuthorizer('modify', $original, $modify->status, $this->lang->modify->authorizeStatusList);
            $reviewAuthorize = explode(',' , $reviewAuthorize);
            if(in_array($userAccount, $reviewAuthorize)){
                $res['reviewAuthorize'] = $original;
                break;
            }
        }
        return  $res;
    }

    /**
     *检查审核节点的审核人
     *
     * @param $level
     * @param $nodes
     * @param array $skipReviewNode
     * @return false
     */
    public function checkReviewerNodesInfo($requiredReviewerKeys, $nodes){
        //检查结果
        $checkRes = true;
        $nodeKeys = array();
        foreach($nodes as $key => $currentNodes)
        {
            //去除空元素
            $currentNodes = array_filter($currentNodes);
            if(!empty($currentNodes))
            {
                $nodeKeys[] = $key;
            }
        }
        //必选审核人，却没有选
        $diffKeys = array_diff($requiredReviewerKeys, $nodeKeys);
        if(!empty($diffKeys)){
            foreach ($diffKeys as  $nodeKey){
                dao::$errors[] =  $this->lang->modify->reviewerEmpty;
                break;
            }
        }

        if(dao::isError()){
            $checkRes = false;
        }
        return $checkRes;
    }

    /**
     * 获取未发送的生产变更
     * @return array
     */
    public function getUnPushedAndPush(){
        $unPushedModifyIds = $this->dao->select('id, pushFailTimes, testingRequestId,externalId,externalCode')->from(TABLE_MODIFY)->where('status')->eq('waitqingzong')->andwhere('pushStatus')->notin([1])->andwhere('pushFailTimes')->le(5)->fetchALl('id');  //选取没推送成功的产品登记
        if(empty($unPushedModifyIds)) return [];
        $res = [];
        foreach ($unPushedModifyIds as $unPushedModifyId)
        {
            if(!empty($unPushedModifyId->testingRequestId)){
                $testingRequestId = $unPushedModifyId->testingRequestId;
                $testingRequestObj = $this->loadModel('testingrequest')->getByID($testingRequestId);
                //等待测试报告
                if(empty($testingRequestObj->cardStatus) || $testingRequestObj->cardStatus != 1){
                    $this->dao->update(TABLE_MODIFY)->set('pushFailReason')->eq('测试申请状态不是“测试通过”')->where('id')->eq($unPushedModifyId->id)->exec();
                    continue;
                }
            }
            $response = $this->pushModfiyInitiate($unPushedModifyId->id);

            $response = json_decode($response);
            $run['modifyId']    = $unPushedModifyId->id;
            $run['response']            = $response;

            $res[] = $run;
        }
        return $res;
    }

    /**
     * 获取未发送的关闭生产变更
     * @return array
     */
    public function getCancelUnPushedAndPush(){
        $unPushedModifyIds = $this->dao->select('id,externalId,externalCode')->from(TABLE_MODIFY)->where('cancelStatus')->eq('canceltojx')->andwhere('cancelPushStatus')->notin([1,-1])->andwhere('cancelPushFailTimes')->le(5)->fetchALl('id');  //选取没推送成功的产品登记
        if(empty($unPushedModifyIds)) return [];
        $res = [];
        foreach ($unPushedModifyIds as $unPushedModifyId)
        {
            if(!empty($unPushedModifyId->externalId) && !empty($unPushedModifyId->externalCode)){
                $response = $this->pushModfiyClose($unPushedModifyId->id);
            }
            $response = json_decode($response);
            $run['modifyId']    = $unPushedModifyId->id;
            $run['response']            = $response;

            $res[] = $run;
        }
        return $res;
    }


    /**
     * 变更流程发起接口
     */
    public function pushModfiyInitiate($modifyId)
    {
        $this->loadModel('requestlog');
        /* 获取生产变更单 */
        $modify = $this->getByID($modifyId);
        $pushEnable = $this->config->global->pushModifyEnable;
        //判断请求配置是否可用
        if ($pushEnable == 'enable') {
            if(empty($modify->externalId) && empty($modify->externalCode)){
                $url = $this->config->global->modifyInitiatePushUrl;
            }else{
                $url = $this->config->global->modifyCommitPushUrl;
            }

            $pushAppId = $this->config->global->pushModifyAppId;
            $pushAppSecret = $this->config->global->pushModifyAppSecret;
            $pushUsername = $this->config->global->pushModifyUsername;
            $fileIP       = $this->config->global->pushModifyFileIP;
            $headers = array();
            $headers[] = 'appId: ' . $pushAppId;
            $headers[] = 'appSecret: ' . $pushAppSecret;
            $ts = time();
            $headers[] = 'ts: ' . $ts;
            $uuid = $this->create_guid();
            $headers[] = 'nonce: ' . $uuid;
            $sign = md5('appId='.$pushAppId.'&nonce='.$uuid.'&ts='.$ts.'&appSecret='.$pushAppSecret);
            $headers[] = 'sign: ' . $sign;

            $outsideProjectList = $this->loadModel('outsideplan')->getPairs();
            $pushData = array();
            //数据体
            $data = array();
            //变更单信息
            $changeMain = array();
            //变更单id
            $changeMain['appProcessId'] = $modify->code;
            //联系人电话
            $changeMain['applyUserContact'] = $modify->contactTel;
            //联系人名称
            $changeMain['applyUserName'] = $modify->contactName;
            //主备数据中心变更同步情况说明
            $changeMain['backupDataCenterChangeSyncDesc'] = empty($modify->backupDataCenterChangeSyncDesc)?'无':$this->clearHtml(strval(htmlspecialchars_decode($modify->backupDataCenterChangeSyncDesc, ENT_QUOTES)));

            //给业务功能带来的影响变化
            $changeMain['businessFunctionAffect'] = empty($modify->businessFunctionAffect)?'无':$this->clearHtml(strval(htmlspecialchars_decode($modify->businessFunctionAffect,ENT_QUOTES)));
            //变更对象id-系统的code
            /*$apps = explode(',', $modify->app);
            $appCodeList = array();
            foreach ($apps as $app){
                $appObject = $this->loadModel('application')->getByID($app);
                array_push($appCodeList, $appObject->code);
            }
            $changeMain['businessSystemIdList'] = trim(implode(",",$appCodeList),',');*/
            $changeMain['businessSystemIdList'] = trim($modify->app,',');

            //变更内容和方法
            $changeMain['changeContentMethod'] = $this->clearHtml(strval(htmlspecialchars_decode($modify->changeContentAndMethod,ENT_QUOTES)));
            //变更可行性分析、测试结果、分析情况说明，可行性分析
            $changeFeasibilityAnalysisReq = array();
            $feasibilityAnalysisStr = $modify->feasibilityAnalysis;
            $feasibilityAnalysisArray = explode("," , $feasibilityAnalysisStr);
            $feasibilityAnalysisValueArray = array();
            foreach ($feasibilityAnalysisArray as $feasibilityAnalysisId){
                $feasibilityAnalysisValue = zget($this->lang->modify->feasibilityAnalysisList,$feasibilityAnalysisId);
                array_push($feasibilityAnalysisValueArray, $feasibilityAnalysisValue);
            }
            $feasibilityAnalysisStr = implode(",",$feasibilityAnalysisValueArray);
            $changeFeasibilityAnalysisReq['feasibilityAnalysis'] = trim($feasibilityAnalysisStr,',');
            $changeFeasibilityAnalysisReq['analysisStateExplanation'] = $this->clearHtml(strval(htmlspecialchars_decode($modify->risk,ENT_QUOTES)));
            $changeMain['changeFeasibilityAnalysisReq'] = $changeFeasibilityAnalysisReq;


            //变更级别
            $changeMain['changeLevel'] = zget($this->lang->modify->levelJxList,$modify->level);
            //变更执行步骤
            $changeMain['changeProcedure'] = $this->clearHtml(strval(htmlspecialchars_decode($modify->step,ENT_QUOTES)));
            //变更原因
            $changeMain['changeReason']                       = $this->clearHtml(strval(htmlspecialchars_decode($modify->reason,ENT_QUOTES)));
            //变更来源
            $changeMain['changeSource']                       = zget($this->lang->modify->changeSourceList,$modify->changeSource);
            //变更阶段
            $changeMain['changeStage']                        = zget($this->lang->modify->changeStageList,$modify->changeStage);
            //变更摘要
            $changeMain['changeSummary']                      = $this->clearHtml(strval(htmlspecialchars_decode($modify->desc,ENT_QUOTES)));
            //变更目标
            $changeMain['changeTarget']                      = $this->clearHtml(strval(htmlspecialchars_decode($modify->target,ENT_QUOTES)));
            //变更类别
            $changeMain['changeType']                      = zget($this->lang->modify->classifyList,$modify->classify);
            /**
             * 同步金信时码值映射
             * 应用软件     =》应用
             * 技术数据修正、业务数据修正  =》数据修正
             */
            if ($changeMain['changeType'] == '应用软件') $changeMain['changeType'] = '应用';
            if ($changeMain['changeType'] == '技术数据修正' || $changeMain['changeType'] == '业务数据修正') $changeMain['changeType'] = '数据修正';
            //数据中心名臣链表
            $nodeStr = $modify->node;
            $nodeArray = explode("," , $nodeStr);
            $nodeValueArray = array();
            foreach ($nodeArray as $node){
                $nodevalue = zget($this->lang->modify->nodeList,$node);
                array_push($nodeValueArray, $nodevalue);
            }
            $nodeValueStr = implode(",",$nodeValueArray);
            $changeMain['dataCenterNameList'] = trim($nodeValueStr,',');

            //对应急处置策略的影响
            $changeMain['emergencyManageAffect']                      = empty($modify->emergencyManageAffect)?'无':$this->clearHtml(strval(htmlspecialchars_decode($modify->emergencyManageAffect,ENT_QUOTES)));
            //计划实施开始时间
            $changeMain['expectedStartTime']                           = $modify->planBegin;
            //计划实施结束时间
            $changeMain['expectedEndTime']                           = $modify->planEnd;
            //实施方式
            $changeMain['implementModality']                         = zget($this->lang->modify->implementModalityList,$modify->implementModality);
            //实施期间是否有业务影响
            $changeMain['isBusinessAffect']                         = zget($this->lang->modify->isBusinessAffectList,$modify->isBusinessAffect)=='是'?'1':'0';
            if($changeMain['isBusinessAffect'] == 1){
                $changeMain['businessAffect'] = $this->clearHtml(strval(htmlspecialchars_decode($modify->businessAffect,ENT_QUOTES)));
            }
            //是否需要业务配合
            $changeMain['isBusinessCooperate']                         = zget($this->lang->modify->isBusinessCooperateList,$modify->isBusinessCooperate)=='是'?'1':'0';
            if($changeMain['isBusinessCooperate'] == 1){
                $changeMain['businessCooperateContent'] = $this->clearHtml(strval(htmlspecialchars_decode($modify->businessCooperateContent,ENT_QUOTES)));
                $changeMain['cooperateDepNameList'] = zget($this->lang->modify->cooperateDepNameListList,$modify->cooperateDepNameList);
            }
            //是否需要业务验证
            $changeMain['isBusinessJudge']                         = zget($this->lang->modify->isBusinessJudgeList,$modify->isBusinessJudge)=='是'?'1':'0';
            if($changeMain['isBusinessJudge'] == 1){
                $changeMain['judgeDep'] = zget($this->lang->modify->judgeDepList,$modify->judgeDep);
                $changeMain['judgePlan'] = $this->clearHtml(strval(htmlspecialchars_decode($modify->judgePlan,ENT_QUOTES)));
            }
            //是否需要会签
            $changeMain['isCountersign']                           = '0';
            //是否紧急
            $changeMain['isEmergent']                              = zget($this->lang->modify->typeList,$modify->type)=='紧急'?'1':'0';
            //是否关联云管
            $changeMain['isInteractWithCloudManage']               = '0';
            //是否预授权
            $changeMain['isPreAuthorization']                      = '0';
            //是否临时变更
            $changeMain['isTemp']                      = zget($this->lang->modify->propertyList, $modify->property)=='是'?'1':'0';
            //产品登记号
            $productenrollObj = $this->loadModel("productenroll")->getByID($modify->productenrollId);
            $changeMain['productRegistrationCode'] = empty($productenrollObj->giteeId)?'无' : $productenrollObj->giteeId;

            //给生产系统带来的影响变化
            $changeMain['productionSystemAffect']                      = empty($modify->effect)?'无' : $this->clearHtml(strval(htmlspecialchars_decode($modify->effect,ENT_QUOTES)));
            //风险分析与应急处置
            $changeMain['riskAnalysisEmergencyHandle'] = json_decode(json_encode($modify->riskAnalysisEmergencyHandle,JSON_UNESCAPED_UNICODE), true);
            //技术验证
            $changeMain['techniqueCheck'] = $this->clearHtml(strval(htmlspecialchars_decode($modify->techniqueCheck,ENT_QUOTES)));

            //关联变更
            if(!empty($modify->preChange) or !empty($modify->postChange) or !empty($modify->synImplement) or !empty($modify->pilotChange) or !empty($modify->promotionChange)){
                $changeMain['isAssociatedChange'] = 1;
            }else{
                $changeMain['isAssociatedChange'] = 0;
            }
            $array = [
                '前置变更' => empty($modify->preChange)?$this->lang->modify->noChange:$this->clearHtml(strval(htmlspecialchars_decode($modify->preChange,ENT_QUOTES))),
                '后置变更' => empty($modify->postChange)?$this->lang->modify->noChange:$this->clearHtml(strval(htmlspecialchars_decode($modify->postChange,ENT_QUOTES))),
                '同步实施' => empty($modify->synImplement)?$this->lang->modify->noChange:$this->clearHtml(strval(htmlspecialchars_decode($modify->synImplement,ENT_QUOTES))),
                '试点变更' => empty($modify->pilotChange)?$this->lang->modify->noChange:$this->clearHtml(strval(htmlspecialchars_decode($modify->pilotChange,ENT_QUOTES))),
                '推广变更' => empty($modify->promotionChange)?$this->lang->modify->noChange:$this->clearHtml(strval(htmlspecialchars_decode($modify->promotionChange,ENT_QUOTES)))
            ];
            $changeMain['relChangeMap'] = $array;
            //(外部)项目/任务
            $changeMain['projectName'] = zget($outsideProjectList, $modify->outsidePlanId);
            // 是否涉及数据库表结构变化 外部 1是 0 否
            $changeMain['isInvolveDb'] = $modify->involveDatabase == 2 ? 0 : $modify->involveDatabase;
            $data['changeMain'] = $changeMain;
            //关联文件列表
            $data['processFileInfoList'] = '';
            $processFileInfoList = array();
            $releaseStr = $modify->release;
            $releaseList = explode(",",$releaseStr);
            foreach ($releaseList as $releaseId){
                $releaseObj = $this->loadModel("release")->getByID($releaseId);
                if(!empty($releaseObj)){
                    $remoteFileStr = $releaseObj->path;
                    $arr = explode("/", $remoteFileStr);
                    $lastName=$arr[count($arr)-1];
                    $urlObject = $this->getRelationFileLinkArray($lastName, $remoteFileStr);
                    if(!empty($urlObject)){
                        array_push($processFileInfoList, $urlObject);
                    }else{
                        $status = 'fail';
                        $update['pushStatus'] = 2;
                        $update['pushFailTimes'] = $modify->pushFailTimes+1;
                        $update['status'] = 'jxsynfailed';
                        $update['pushFailReason'] = "md5文件获取失败";
                        $update['pushDate'] = helper::now();
                        $reviewers = $this->getSecondLineReviewers($modify->id, $modify->version, $modify->reviewStage);
                        $update['dealUser'] = $reviewers;
                        $this->dao->update(TABLE_MODIFY)->data($update)->where('id')->eq($modify->id)->exec();
                        $this->loadModel('action')->create('modify',$modify->id, 'jxsyncmodifyfail', "md5文件获取失败");
                        $this->loadModel('consumed')->record('modify', $modify->id, 0, 'guestjk', 'waitqingzong', 'jxsynfailed', array(), '');
                        return ;
                    }
                }
            }
            $testingrequestFiles = $this->loadModel('file')->getByObject('testingrequest', $modify->testingRequestId);
            foreach ($testingrequestFiles as $testingrequestFile){
                if($testingrequestFile->extension){
                    $tail = strlen($testingrequestFile->extension) + 1;
                }
                $realRemotePath = substr($fileIP.'/api.php?m=api&f=getfile&code=jinke1problem&time=1&token=1&filename='.$testingrequestFile->pathname, 0, -$tail); //实际存的附件没有后缀 需要去掉
                $localRealFile =  $testingrequestFile->realPath; //实际存的附件
                $md5 = md5_file($localRealFile);
                array_push($processFileInfoList, array('address'=> $realRemotePath, 'md5'=> $md5, 'fileName' => $testingrequestFile->title));
            }
            $data['processFileInfoList'] = $processFileInfoList;
            if(!empty($modify->externalId) && !empty($modify->externalCode)){
                //金信id
                $data['id']                      = $modify->externalId;
                $data['idUnique']                      = $modify->externalCode;
            }

            $pushData['data'] = $data;

            $object = 'modify';
            if(empty($modify->externalId) && empty($modify->externalCode)){
                $objectType = 'pushModfiyInitiate';
                $method = 'POST';
            }else{
                $objectType = 'pushModfiyCommit';
                $method = 'PUT';
            }
            $response = '';
            $status = 'fail';
            $extra = '';
            $result = $this->loadModel('requestlog')->http($url, $pushData, $method, 'json', array(), $headers);
            if (!empty($result)) {
                $resultData = json_decode($result);
                if ($resultData->code == '0') {
                    $status = 'success';
                    $update['pushStatus'] = 1;
                    $update['pushFailTimes'] = 0;
                    $update['status'] = 'withexternalapproval';
                    $update['dealUser'] = 'guestjx';
                    $update['externalId'] = $resultData->data->id;
                    $update['externalCode'] = $resultData->data->idUnique;
                    $update['pushDate'] = helper::now();
                    $this->dao->begin();
                    $this->dao->update(TABLE_MODIFY)->data($update)->where('id')->eq($modify->id)->exec();
                    $this->loadModel('action')->create('modify',$modify->id, 'jxsyncmodifysuccess', $response->message);
                    $this->loadModel('consumed')->record('modify', $modify->id, 0, 'guestjk', 'waitqingzong', 'withexternalapproval', array(), '');
                    $this->loadModel('demand')->changeBySecondLineV4($modifyId,'modify');
                    $this->dao->commit();
                }else{
                    $status = 'fail';
                    $update['pushStatus'] = 2;
                    $update['pushFailTimes'] = $modify->pushFailTimes+1;
                    $update['status'] = 'jxsynfailed';
                    $update['pushFailReason'] = $resultData->description;
                    $update['pushDate'] = helper::now();
                    $reviewers = $this->getSecondLineReviewers($modify->id, $modify->version, $modify->reviewStage);
                    $update['dealUser'] = $reviewers;
                    $this->dao->update(TABLE_MODIFY)->data($update)->where('id')->eq($modify->id)->exec();
                    $this->loadModel('action')->create('modify',$modify->id, 'jxsyncmodifyfail', $resultData->description);
                    $this->loadModel('consumed')->record('modify', $modify->id, 0, 'guestjk', 'waitqingzong', 'jxsynfailed', array(), '');
                }
                $response = $result;
            }else{
                $status = 'fail';
                $update['pushStatus'] = -1;
                $update['pushFailTimes'] = $modify->pushFailTimes+1;
                if($modify->pushFailTimes+1 > 5){
                    $update['status'] = 'jxsynfailed';
                    $update['pushFailReason'] = '网络不通';
                    $reviewers = $this->getSecondLineReviewers($modify->id, $modify->version, $modify->reviewStage);
                    $update['dealUser'] = $reviewers;
                    $update['pushDate'] = helper::now();
                    $this->loadModel('consumed')->record('modify', $modify->id, 0, 'guestjk', 'waitqingzong', 'jxsynfailed', array(), '');
                }
                $this->dao->update(TABLE_MODIFY)->data($update)->where('id')->eq($modify->id)->exec();
                $this->loadModel('action')->create('modify',$modify->id, 'jxsyncmodifyfail', '网络不通');
            }
            $this->requestlog->saveRequestLog($url, $object, $objectType, $method, $pushData, $response, $status, $extra, $modify->id);
        }
        return $response;
    }

    /**
     * 清除字符串特殊符号
     * @param $str
     * @return void
     */
    public function clearHtml($str){
        $str = trim($str); //清除字符串两边的空格
        $str = strip_tags($str,""); //利用php自带的函数清除html格式
        $str = preg_replace("/\t/","",$str); //使用正则表达式替换内容，如：空格，换行，并将替换为空。
        //$str = preg_replace("/\r\n/","",$str);
        $str = preg_replace("/\r/","",$str);
        //$str = preg_replace("/\n/","",$str);
        return trim($str); //返回字符串
    }


    /**
     * 变更流程关闭接口
     */
    public function pushModfiyClose($modifyId)
    {
        $this->loadModel('requestlog');
        /* 获取生产变更单 */
        $modify = $this->getByID($modifyId);
        $pushEnable = $this->config->global->pushModifyEnable;
        //判断请求配置是否可用
        if ($pushEnable == 'enable') {
            $url = $this->config->global->modifyClosePushUrl;
            $pushAppId = $this->config->global->pushModifyAppId;
            $pushAppSecret = $this->config->global->pushModifyAppSecret;
            $pushUsername = $this->config->global->pushModifyUsername;
            $fileIP       = $this->config->global->pushModifyFileIP;
            $headers = array();
            $headers[] = 'appId: ' . $pushAppId;
            $headers[] = 'appSecret: ' . $pushAppSecret;
            $ts = time();
            $headers[] = 'ts: ' . $ts;
            $uuid = $this->create_guid();
            $headers[] = 'nonce: ' . $uuid;
            $sign = md5('appId='.$pushAppId.'&nonce='.$uuid.'&ts='.$ts.'&appSecret='.$pushAppSecret);
            $headers[] = 'sign: ' . $sign;


            $pushData = array();
            //数据体
            $data = array();
            $data['id'] = $modify->externalId;
            $data['idUnique'] = $modify->externalCode;
            $data['closeReason'] = $modify->cancelReason;
            $pushData['data'] = $data;

            $object = 'modify';
            $objectType = 'pushModfiyClose';
            $response = '';
            $status = 'fail';
            $extra = '';
            $result = $this->loadModel('requestlog')->http($url, $pushData, 'PATCH', 'json', array(), $headers);
            if (!empty($result)) {
                $resultData = json_decode($result);
                if ($resultData->code == '0') {
                    $status = 'success';
                    $update['cancelPushStatus'] = 1;
                    $update['cancelPushFailTimes'] = 0;
                    $update['status'] = 'canceled';
                    $update['cancelPushDate'] = helper::now();
                    $update['dealUser'] = '';
                    $update['cancelStatus'] = 'canceled';
                    $this->dao->update(TABLE_MODIFY)->data($update)->where('id')->eq($modify->id)->exec();
                    $this->loadModel('action')->create('modify',$modify->id, 'jxsynccancelmodifysuccess', $response->message);
                    $this->loadModel('consumed')->record('modify', $modify->id, 0, 'guestjk', $modify->status, 'modifycancel', array(), '');
                }else{
                    $status = 'fail';
                    $update['cancelPushStatus'] = 2;
                    $update['cancelPushFailTimes'] = $modify->cancelPushFailTimes+1;
                    $update['status'] = 'jxsyncancelfailed';
                    $update['cancelPushFailReason'] = $resultData->description;
                    $update['dealUser'] = $modify->canceledBy;
                    $update['cancelStatus'] = 'jxsyncancelfailed';
                    $update['cancelPushDate'] = helper::now();
                    $this->dao->update(TABLE_MODIFY)->data($update)->where('id')->eq($modify->id)->exec();
                    $this->loadModel('action')->create('modify',$modify->id, 'jxsynccancelmodifyfail', $response->message);
                    $this->loadModel('consumed')->record('modify', $modify->id, 0, 'guestjk', $modify->status, 'jxsynfailed', array(), '');
                }
                $response = $result;
            }else{
                $status = 'fail';
                $update['cancelPushStatus'] = -1;
                $update['cancelPushFailTimes'] = $modify->cancelPushFailTimes+1;
                if($modify->cancelPushFailTimes+1 > 5){
                    $update['status'] = 'jxsyncancelfailed';
                    $update['cancelPushFailReason'] = '网络不通';
                    $update['dealUser'] = $modify->canceledBy;
                    $update['cancelStatus'] = 'jxsyncancelfailed';
                    $update['cancelPushDate'] = helper::now();
                    $this->loadModel('consumed')->record('modify', $modify->id, 0, 'guestjk', $modify->status, 'jxsynfailed', array(), '');
                }
                $this->dao->update(TABLE_MODIFY)->data($update)->where('id')->eq($modify->id)->exec();
                $this->loadModel('action')->create('modify',$modify->id, 'jxsynccancelmodifyfail', $response->message);
            }
            $this->requestlog->saveRequestLog($url, $object, $objectType, 'PATCH', $pushData, $response, $status, $extra, $modify->id);
        }
        return $response;
    }
    public function syncModfiyClose($modify)
    {
        $this->loadModel('requestlog');
        $pushEnable = $this->config->global->pushModifyEnable;
        //判断请求配置是否可用
        if ($pushEnable == 'enable') {
            $url = $this->config->global->modifyClosePushUrl;
            $pushAppId = $this->config->global->pushModifyAppId;
            $pushAppSecret = $this->config->global->pushModifyAppSecret;
            $headers = array();
            $headers[] = 'appId: ' . $pushAppId;
            $headers[] = 'appSecret: ' . $pushAppSecret;
            $ts = time();
            $headers[] = 'ts: ' . $ts;
            $uuid = $this->create_guid();
            $headers[] = 'nonce: ' . $uuid;
            $sign = md5('appId='.$pushAppId.'&nonce='.$uuid.'&ts='.$ts.'&appSecret='.$pushAppSecret);
            $headers[] = 'sign: ' . $sign;


            $pushData = array();
            //数据体
            $data = array();
            $data['id'] = $modify->externalId;
            $data['idUnique'] = $modify->externalCode;
            $data['closeReason'] = $modify->cancelReason;
            $pushData['data'] = $data;

            $object = 'modify';
            $objectType = 'pushModfiyClose';
            $response = '';
            $status = 'fail';
            $extra = '';
            $result = $this->loadModel('requestlog')->http($url, $pushData, 'PATCH', 'json', array(), $headers);
            if (!empty($result)) {
                $resultData = json_decode($result);
                if ($resultData->code == '0') {
                    $status = 'success';
                }
                $response = $result;
            }
            $this->requestlog->saveRequestLog($url, $object, $objectType, 'PATCH', $pushData, $response, $status, $extra, $modify->id);
        }
        return $response ?? '';
    }

    public function create_guid($namespace = '') {
        static $guid = '';
        $uid = uniqid("", true);
        $data = $namespace;
        $data .= $_SERVER['REQUEST_TIME'];
        $data .= $_SERVER['HTTP_USER_AGENT'];
        $data .= $_SERVER['LOCAL_ADDR'];
        $data .= $_SERVER['LOCAL_PORT'];
        $data .= $_SERVER['REMOTE_ADDR'];
        $data .= $_SERVER['REMOTE_PORT'];
        $hash = strtoupper(hash('ripemd128', $uid . $guid . md5($data)));
        $guid = substr($hash, 0, 8) .
            '-' .
            substr($hash, 8, 4) .
            '-' .
            substr($hash, 12, 4) .
            '-' .
            substr($hash, 16, 4) .
            '-' .
            substr($hash, 20, 12);
        return $guid;
    }

    public function getByGiteeId($externalCode) {
        $data = $this->dao->select('*')->from(TABLE_MODIFY)
            ->where('externalCode')->eq($externalCode)
            ->fetch();
        return $data;
    }

    /**
     * 介质下载信息数组
     * @param $fileName
     * @param $filePath
     * @return array
     */
    public function getRelationFileLinkArray($fileName, $filePath)
    {
        $filePath = trim($filePath);
        if(empty($fileName) || empty($filePath) || substr($filePath, 0, 7) !=='/files/') return [];
        $downloadUrl = sprintf($this->config->global->downloadIPJX, $this->getSign($filePath), urlencode($filePath));
        $mdObject = $this->downloadSftpFile($filePath);
        if($mdObject['code'] == '200'){
            return ['address'=> $downloadUrl, 'fileName' => $fileName, 'md5' => $mdObject['msg']];
        }else{
            return '';
        }

    }

    /**
     * 下载文件签名
     * @param $filename
     * @return int
     */
    public function getSign($filename)
    {
        return $this->loadModel('downloads')->getSign($filename);
    }

    /**
     * 下载sftp-md5文件
     * @param $remotFile
     */
    public function downloadSftpFile($remotFile)
    {
        try{
            set_time_limit(0); //php 不超时
            $this->app->loadLang('api');
            $config         = $this->lang->api->sftpList;
            $conn           = ssh2_connect($config['host'], $config['port']); //登陆远程服务器
            if(!ssh2_auth_password($conn, $config['username'], $config['password'])) {
                return ['code'=> '400', 'msg' => '登录失败'];
            } //用户名密码验证
            $sftp           = ssh2_sftp($conn); //打开sftp
            $dir            = $_SERVER['DOCUMENT_ROOT'] . '/data/upload/'; //测试路径  /var/www/zentaopms/www/data/upload/'
            $localRealFile  = $dir . $remotFile;  //本地临时文件地址
            $remotFileMd5   = $this->getMd5FileName($remotFile); ///aaa.zip 转成 aaa.md5
            $localRealFileMd5   = $dir . $remotFileMd5;  //本地临时文件地址MD5
            $targetPath         = dirname($localRealFile);
            if(!is_dir($targetPath)) mkdir($targetPath, 0777, true);

            //下载sftp服务器上MD5文件
            if(is_file($localRealFileMd5)) { unlink($localRealFileMd5); } //保证每次最新
            $resource = "ssh2.sftp://{$sftp}" . $remotFileMd5;   //远程文件地址md5
            if (!file_exists($resource)) {
                $remotFileMd5   = $this->getMd5OrgFileName($remotFile);
                $localRealFileMd5   = $dir . $remotFileMd5;  //本地临时文件地址MD5
                $targetPath         = dirname($localRealFile);
                if(!is_dir($targetPath)) mkdir($targetPath, 0777, true);
                //下载sftp服务器上MD5文件
                if(is_file($localRealFileMd5)) { unlink($localRealFileMd5); } //保证每次最新
                $resource = "ssh2.sftp://{$sftp}" . $remotFileMd5;   //远程文件地址md5
                if (!file_exists($resource)) {
                    return ['code'=> '400', 'msg' => 'MD5文件不存在'];
                }
            } //检查下载文件是否存在
            copy($resource, $localRealFileMd5);   //将远程文件复制到本地

            $md5 = $this->getFileMd5($localRealFileMd5); //读取sftp服务器上MD5文件内容

            unlink($localRealFileMd5);
            return ['code'=> '200', 'msg' => $md5];
        }
        catch (Exception $exception)
        {
            return ['code'=> '400', 'msg' => '获取MD5文件异常'];
        }
    }

    //读MD5文件中的MD5值
    public function getFileMd5($filename)
    {
        $info = file_get_contents($filename);
        $md5 = substr($info, 0,32);
        unlink($filename);
        return $md5;
    }

    //把后缀变成MD5
    public function getMd5FileName($filename)
    {
        $arr = explode('.', $filename);
        $ext = end($arr);
        $extLen = strlen($ext);
        return substr($filename, 0, -$extLen) . 'md5';
    }

    //把文件名变成md5.org
    public function getMd5OrgFileName($filename)
    {
        $arr = explode('/', $filename);
        $arr[sizeof($arr)-1]='md5.org';
        return rtrim(implode('/',$arr),'/');
    }

    /**
     * @param $modifyID
     * @return false
     *
     */
    public function cancel($modifyID)
    {
        if(!$this->post->cancelReason)
        {
            dao::$errors['cancelReason'] = $this->lang->modify->cancelReasonEmpty;
            return false;
        }

        $modify = $this->getByID($modifyID);

        $myDept = $this->loadModel('dept')->getByID($this->app->user->dept);

        $data = fixer::input('post')
            ->remove('comment')
            ->add('lastStatus', $modify->status)
            ->add('lastStage', $modify->reviewStage)
            ->add('cancelReviewer', $myDept->manager)
            ->add('status', 'cancel')
            ->add('lastDealDate', date('Y-m-d'))
            ->add('cancelStatus', 'cancel')
            ->add('canceledBy', $this->app->user->account)
            ->add('canceledDate', date('Y-m-d H:i:s'))
            ->add('dealUser', $myDept->manager)
            ->add('lastDealUser',$modify->dealUser)
            ->add('cancelPushStatus','0')
            ->add('cancelPushFailTimes','0')
            ->add('cancelPushDate','')
            ->get();
        if(!dao::isError()) {
            $this->dao->update(TABLE_MODIFY)->data($data)->autoCheck()
                ->where('id')->eq($modifyID)->exec();
            $this->loadModel('consumed')->record('modify', $modifyID, 0, $this->app->user->account, $modify->status, 'cancel', array());
        }

    }

    /**
     * 获取二线专员
     */
    public function getSecondLineReviewers($id=0 , $version=1 , $stage = 0){
        $this->loadModel('review');
        $stage = 0;
        foreach($this->lang->modify->reviewerList as $review){
            $stage++;
            if($review == '产创部二线专员')
            {
                break;
            }
        }
        return $this->review->getLastPendingPeople('modify', $id, $version, $stage);
    }

    /**
     * 编辑退回次数
     * @param $id
     * @return void
     */
    public function editreturntimes($modifyId){
        //工作量验证
        $rejectTimes = $_POST['modifyrejectTimes'];
        if($rejectTimes=='' || $rejectTimes==null)
        {
            dao::$errors['modifyrejectTimes'] = sprintf($this->lang->modify->emptyObject, $this->lang->modify->modifyrejectTimes);
        }else if(!is_numeric($rejectTimes) || (int)$rejectTimes<0 || strpos($rejectTimes,".")!==false) {
            dao::$errors['modifyrejectTimes'] = sprintf($this->lang->modify->noNumeric, $this->lang->modify->modifyrejectTimes);
        }

        $comment = $_POST['comment'];
        if(empty($comment))
        {
            dao::$errors['comment'] = sprintf($this->lang->modify->emptyObject, $this->lang->comment);
        }

        $this->tryError();

        $modify = $this->loadModel("modify")->getByID($modifyId);

        /* 当请求方式为post时，更新需求条目的状态为关闭。判断所属需求意向下的需求条目都关闭时，关闭需求意向。*/
        $this->dao->update(TABLE_MODIFY)->set('returnTime')->eq($rejectTimes)->where('id')->eq($modify->id)->exec();
        $this->loadModel('action')->create('modify', $modify->id, 'editmodifycnccreturntimes', $comment);
    }

    /**
     * 获取审批人
     * @param $id
     * @param $version
     * @return void
     */
    public function getReviewerReal($id, $version){
        $nodes = $this->loadModel('review')->getNodes('modify', $id, $version);
        $realReviewerList = array();
        foreach ($this->lang->modify->reviewNodeList as $key => $reviewNode) {
            if (isset($nodes[$key])) {
                $currentNode = $nodes[$key];
                $reviewers = $currentNode->reviewers;
                if (!(is_array($reviewers) && !empty($reviewers))) {
                    continue;
                }
                //所有审核人
                $reviewersArray = array_column($reviewers, 'reviewer');
                $userCount = count($reviewersArray);
                if ($userCount > 0) {
                    //获得实际审核人
                    $realReviewer = $this->loadModel('review')->getRealReviewerInfo($currentNode->status, $reviewers);
                    array_push($realReviewerList, $realReviewer->reviewer);
                }
            }
        }
        return trim(implode(",", $realReviewerList),',');
    }

    /**
     * 编辑外部变更级别
     * shixuyang
     * shixuyang
     * @param $id
     * @return void
     */
    public function editlevel($modifyId){
        //工作量验证
        $jxLevel = $_POST['jxLevel'];
        if(empty($jxLevel))
        {
            dao::$errors['jxLevel'] = sprintf($this->lang->modify->emptyObject, $this->lang->modify->jxLevel);
        }

        $comment = $_POST['comment'];
        if(empty($comment))
        {
            dao::$errors['comment'] = sprintf($this->lang->modify->emptyObject, $this->lang->comment);
        }

        $this->tryError();

        $this->dao->update(TABLE_MODIFY)->set('jxLevel')->eq($jxLevel)->where('id')->eq($modifyId)->exec();
        $this->loadModel('action')->create('modify', $modifyId, 'editlevel', $comment);
    }

    public function isDiskDelivery($modifyId){
        $modify = $this->getByID($modifyId);
        $data = [];
        if($_POST['isDiskDelivery']=='')
        {
            dao::$errors['isDiskDelivery'] = sprintf($this->lang->modify->emptyObject, $this->lang->modify->isDiskDelivery);
        }
        $data['isDiskDelivery'] = $_POST['isDiskDelivery'];
        if($_POST['isDiskDelivery']==1){
            $data['status'] = 'waitImplement';
            $data['dealUser'] = $modify->createdBy;
        }else{
            $data['status'] = $modify->status;
        }

        $this->tryError();
        $this->dao->update(TABLE_MODIFY)
        ->data($data)
        ->where('id')->eq($modifyId)->exec();
        // 状态联动
        $this->loadModel('demand')->changeBySecondLineV4($modifyId,'modify');
        $this->loadModel('action')->create('modify', $modifyId, 'isdiskdelivery',$_POST['comment']);
        $this->loadModel('consumed')->record('modify', $modifyId, 0, $this->app->user->account, $modify->status, $data['status'], array(), '');
    }

    /**
     * @Notes: 获取金信生产变更数据，用于状态联动
     * @Date: 2023/4/13
     * @Time: 11:28
     * @Interface getEffectiveInfoData
     * @param $id
     */
    public function getEffectiveModifyData($id){
        return $this->dao->select('id,code,status, actualEnd, realEndTime, dealUser')
            ->from(TABLE_MODIFY)
            ->where('id')->eq($id)
            ->andWhere('abnormalCode')->eq('')
            ->andWhere('status')->notIN("waitsubmitted,modifycancel,deleted") //待提交、变更取消不做联动
            ->fetch();
    }
    //喧喧发信
    public function getXuanxuanTargetUser($obj,$objectType, $objectID, $actionType, $actionID, $actor = ''){
        if ($obj->issubmit == 'save'){
            return ['isSend'=>'no'];
        }
        $toList = '';
        $users  = $this->loadModel('user')->getPairs('noletter');
        $as = array();
        foreach(explode(',',trim($obj->dealUser,',')) as $dealUser){
            $as[] = zget($users, $dealUser);
        }
        $obj->dealUser = implode(',',$as);

        $action          = $this->loadModel('action')->getById($actionID);
        $history         = $this->action->getHistory($actionID);
        $action->history = isset($history[$actionID]) ? $history[$actionID] : array();

        $sendUsers = $this->getToAndCcList($obj,$action);
        if(!$sendUsers) return;
        list($toList, $ccList) = $sendUsers;
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

        return ['toList'=>$toList,'subcontent'=>$subcontent,'url'=>$url,'title'=>$title,'actions'=>$actions];

    }

    /**
     * @param $modify 生产变更单数据
     * description：退回时，保存外部失败原因
     */
    public function getHistoryReview($modify){
        $historyReview = [];
        if(strtotime($modify->createdDate) >= strtotime('2022-09-30') or strtotime($modify->editedDate) >= strtotime('2022-09-30')){
            //光盘交付
            if($modify->isDiskDelivery==0){
                $jkResult = '';
                if(!($modify->lastStatus == 'waitqingzong' && $modify->cancelStatus)){
                    if (in_array($modify->status, $this->lang->modify->failReason[0])) {
                        $jkResult = zget($this->lang->modify->statusList, $modify->status=='waitImplement'?'jxsynfailed':$modify->status, '');
                    } elseif (in_array($modify->status, $this->lang->modify->failReason[1])) {
                        $jkResult = $this->lang->modify->synSuccess;
                    }
                }
                $jkReason = '';
                if (in_array($modify->status, $this->lang->modify->failReason[1])) {
                    $jkReason = '生产变更单同步成功';
                } elseif (in_array($modify->status, $this->lang->modify->failReason[0])) {
                    $jkReason = $modify->pushFailReason;
                }
                $jkPushDate = '';
                if($modify->pushDate != '0000-00-00 00:00:00' and in_array($modify->status, $this->lang->modify->failReason[2]))
                {
                    $jkPushDate = $modify->pushDate;
                }
                if ($jkResult != ''){
                    $historyReview[4] = [
                        'reviewNode'        => 4,
                        'reviewUser'        => 'guestjk',
                        'reviewResult'      => $jkResult,
                        'reviewFailReason'  => $jkReason,
                        'reviewPushDate'    => $jkPushDate,
                        'date'              => helper::now()
                    ];
                    $flag = $this->loadModel('outwarddelivery')->isCheckNode($modify, $historyReview[4], 4);
                    if(!$flag){
                        unset($historyReview[4]);
                    }
                }
                $jxResult = '';
                if (in_array($modify->status, $this->lang->modify->failReason[3])) {
                    $jxResult = zget($this->lang->modify->statusList, $modify->status);
                }
                $jxReason = '';
                if (in_array($modify->status, $this->lang->modify->failReason[4])) {
                    if($modify->status == 'modifyreject'){
                        $jxReason = "打回人：".$modify->approverName."<br>审批意见：".$modify->returnReason;
                    }else{
                        $jxReason = $modify->returnReason;
                    }
                }
                $jxPushDate = '';
                if (strtotime($modify->changeDate) > 0 and in_array($modify->status, $this->lang->modify->failReason[4])) {
                    $jxPushDate = $modify->changeDate;
                }
                if ($jxResult != ''){
                    $historyReview[5] = [
                        'reviewNode'        => 5,
                        'reviewUser'        => 'guestjx',
                        'reviewResult'      => $jxResult,
                        'reviewFailReason'  => $jxReason,
                        'reviewPushDate'      => $jxPushDate,
                        'date'              => helper::now()
                    ];
                    $flag = $this->loadModel('outwarddelivery')->isCheckNode($modify, $historyReview[5], 5);
                    if(!$flag){
                        unset($historyReview[5]);
                    }
                }
            }else{
                $historyReview[4] = [
                    'reviewNode'        => 4,
                    'reviewUser'        => 'guestjk',
                    'reviewResult'      => '同步金信失败',
                    'reviewFailReason'  => $modify->pushFailReason,
                    'reviewPushDate'      => $modify->pushDate,
                    'date'              => helper::now()
                ];
                $flag = $this->loadModel('outwarddelivery')->isCheckNode($modify, $historyReview[4], 4);
                if(!$flag){
                    unset($historyReview[4]);
                }
            }
        }
        $reviewFailReason = json_decode($modify->reviewFailReason,true);
        $reviewFailReason[$modify->version][] = $historyReview;
        return json_encode($reviewFailReason);
    }

    /**
     * @param $id
     * @param $objectType
     * @param $version
     * @description 删除当前版本审核节点
     */
    public function delNode($id,$objectType,$version){
        $res = $this->dao->select('id')->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq($objectType)
            ->andWhere('objectID')->eq($id)
            ->andWhere('version')->eq($version)
            ->fetchAll();
        foreach ($res as $key=>$value) {
            $node = $value->id;
            $this->dao->delete()->from(TABLE_REVIEWNODE)->where('id')->eq($node)->exec();
            $this->dao->delete()->from(TABLE_REVIEWER)->where('node')->eq($node)->exec();
        }
    }

    /**
     * 获取变更异常的单子
     */
    public function getModifyAbnormal($isChoice=true){
        $data = $this->dao->select('id,`desc`,`code`')->from(TABLE_MODIFY)
            ->where('closed')->eq('0')
            ->andWhere('`status`')->in($this->lang->modify->reissueArray)
            ->andWhere('abnormalCode')->eq('')
            ->fetchall();
        $arr = [];
        foreach ($data as $v) {
            $arr[$v->id] = $v->code;
            if ($v->desc != ''){
                $arr[$v->id] .= '（'.html_entity_decode(strip_tags(str_replace("<br />","\n",strval(htmlspecialchars_decode($v->desc,ENT_QUOTES))))).'）';
            }
        }
        return $arr;
    }
    //编辑关联的变更单
    public function editabnormalorder($modifyId){
        $abnormalList = $this->getModifyAbnormal();
        $id = $_POST['abnormalCode'];
        if ($id == ''){
            dao::$errors['abnormalCode'] =  $this->lang->modify->abnormalCodeEmpty;
            return false;
        }
        if (!isset($abnormalList[$id]) || $abnormalList[$id] == ''){
            dao::$errors['abnormalCode'] =  $this->lang->modify->checkassociaiton;
            return false;
        }
        $info = $this->getByID($id);//要关联的异常变更单
        $modify = $this->getByID($modifyId);//当前变更单

        //将原变更单置空
        $this->editModifyAbnormal($modifyId,$id);

        $data = new stdClass();
        $data->problemId = $info->problemId;
        $data->demandId  = $info->demandId;
        $demand = trim($info->demandId,',');
        $problem = trim($info->problemId,',');
        $this->addSecondLine($modifyId, explode(',',$problem),'problem'); //问题关联
        $this->addSecondLine($modifyId, explode(',',$demand),'demand');  //需求关联
        $this->dao->update(TABLE_MODIFY)->data($data)->where('id')->eq($modifyId)->exec();
        $this->dao->update(TABLE_SECONDLINE)
            ->set('deleted = "1" where (objectType = "modify" and relationType in ("problem","demand") and objectID = '.$id.') or (relationType = "modify" AND objectType in ("problem","demand") and relationID = '.$id.') ')
            ->exec();
        return common::createChanges($modify, $data);
    }

    /**
     * @param $modifyId，当前变更单id
     * @param $id 要关联的变更单id
     * 修改关联变更单重置关系
     */
    public function editModifyAbnormal($modifyId,$id){
        //已关联的解绑
        $findInSet = '(FIND_IN_SET("'.$modifyId.'",abnormalCode))';
        $oldInfo = $this->dao->select("id,abnormalCode")->from(TABLE_MODIFY)->where($findInSet)->fetch();
        if ($oldInfo->abnormalCode != ''){
            $arr = array_flip(explode(',',$oldInfo->abnormalCode));
            unset($arr[$modifyId]);
            $arr = array_flip(array_unique($arr));
            $str = implode(',',$arr);
            $str = '';
            $this->dao->update(TABLE_MODIFY)->set('abnormalCode="'.$str.'"')->where('id')->eq($oldInfo->id)->exec();
        }
        $newInfo = $this->dao->select("id,abnormalCode")->from(TABLE_MODIFY)->where('id')->eq($id)->fetch();
        if ($newInfo){
            if (!in_array($modifyId,explode(',',$newInfo->abnormalCode))){
//                $str = $newInfo->abnormalCode . ','.$modifyId;
//                $str = trim($str,',');
                $this->dao->update(TABLE_MODIFY)->set('abnormalCode="'.$modifyId.'"')->where('id')->eq($id)->exec();
            }

        }
    }

    /**
     * @param $search
     * 手机端获取待办列表
     */
    public function getModifyWaitListApi($search='',$orderBy='id_desc'){
        $dealUserList = $this->loadModel('common')->getOriginalAuthorizer('modify', $this->app->user->account);
        $dealUserList = explode(',', $dealUserList);
        $condition = '';
        if(!empty($dealUserList)){
            $this->loadModel('modify');
            foreach ($dealUserList as $dealUser){
                if(strpos($condition, 'FIND_IN_SET') !== false){
                    if($this->app->user->account == $dealUser){
                        $condition .= ' or (FIND_IN_SET("'.$dealUser.'",dealUser))';
                    }else{
                        $condition .= ' or (FIND_IN_SET("'.$dealUser.'",dealUser) and status in(';
                        $i = 0;
                        foreach ($this->lang->modify->authorizeStatusList as $key=>$value){
                            if($i == 0){
                                $condition .= "'".$key."'";
                            }else{
                                $condition .= ",'".$key."'";
                            }
                            $i++;
                        }
                        $condition .= '))';
                    }
                }else{
                    if($this->app->user->account == $dealUser){
                        $condition .= ' (FIND_IN_SET("'.$dealUser.'",dealUser))';
                    }else{
                        $condition .= ' (FIND_IN_SET("'.$dealUser.'",dealUser) and status in(';
                        $i = 0;
                        foreach ($this->lang->modify->authorizeStatusList as $key=>$value){
                            if($i == 0){
                                $condition .= "'".$key."'";
                            }else{
                                $condition .= ",'".$key."'";
                            }
                            $i++;
                        }
                        $condition .= '))';
                    }
                }
            }
        }
        $modifys = $this->dao->select('*')->from(TABLE_MODIFY)
            ->where('status')->ne('deleted')
            ->andWhere('issubmit')->eq('submit')
            ->andWhere('status')->in($this->lang->modify->mobileStatus)
            ->beginIF(!empty($condition))->andWhere($condition)->fi()
            ->beginIF($search != '')->andwhere(" ( `code` like '%$search%' or `desc` like '%$search%' )")->fi()
            ->orderBy($orderBy)
            ->fetchAll('id');
        $apps  = $this->loadModel('application')->getapplicationNameCodePairsWithisPayment();
        $appList = array();
        foreach($apps as $app){
            $appList[$app->id] = $app->name;
        }


        $accountList = array();
        $account     = $this->app->user->account;
        $this->loadModel('review');
        foreach($modifys as $key => $modify)
        {
            $apps = array();
            foreach(explode(',',$modify->app)  as $app){
                if(!empty($app)){
                    $apps[] = zget($appList,$app);
                }
            }
            $modify->app = implode('，',$apps);

            // if(empty($modify->dealUser)){
            //     $modify->dealUser = $this->review->getReviewer('modify', $modify->id, $modify->version, $modify->reviewStage);
            // }
            $modify->dealUser = $this->loadModel('common')->getAuthorizer('modify', $modify->dealUser,$modify->status, $this->lang->modify->authorizeStatusList);
            if(strpos(",$modify->dealUser,", ",{$account},") === false)
            {
                unset($modifys[$key]);
                continue;
            }
            $accountList[$modify->createdBy] = $modify->createdBy;
        }

        // User dept list.
        $dmap = $this->dao->select('account,realname,dept')->from(TABLE_USER)->where('account')->in($accountList)->fetchAll('account');
        foreach($modifys as $key => $modify)
        {
            $modifys[$key]->createdDept = isset($dmap[$modify->createdBy]) ? $dmap[$modify->createdBy]->dept : '';
            $modifys[$key]->realname    = isset($dmap[$modify->createdBy]) ? $dmap[$modify->createdBy]->realname : '';
        }

        return $modifys;
    }

    /***
     * @param string $search 关键字搜索
     * @param string $orderBy
     * 手机端获取已办列表接口
     */
    public function getCompletedListApi($pager,$search='',$orderBy='id_desc'){

        $consumeds =  $this->dao->select('id,objectID')->from(TABLE_CONSUMED)
            ->where('objectType')->eq('modify')
            ->andWhere('deleted')->eq('0')
            ->andWhere('createdBy')->eq($this->app->user->account)
            ->andWhere('createdDate')->ge('2024-01-01 00:00:00')
            ->fetchAll();
        $consumedID = array_unique(array_column($consumeds,'objectID'));
        $str = '"proxy":"'.$this->app->user->account.'",';
        $reviews = $this->dao->select("objectID")->from(TABLE_REVIEWER)->alias("t1")
            ->leftjoin(TABLE_REVIEWNODE)->alias('t2')
            ->on("t1.node=t2.id")
            ->where( "(reviewer = '".$this->app->user->account."' or t1.extra like '%$str%')")
            ->andWhere('t1.status')->in(['pass','reject'])
            ->andWhere('reviewTime')->ge('2024-01-01 00:00:00')
            ->andWhere('objectType')->eq('modify')
            ->fetchAll();
        $reviewID = array_unique(array_column($reviews,'objectID'));

        $ids = array_unique(array_merge($consumedID,$reviewID));
        $modifys = $this->dao->select('id,`code`,`desc`,createdDate,createdBy,`type`')->from(TABLE_MODIFY)
            ->where('id')->in($ids)
            ->andWhere('`status`')->ne('deleted')
            ->beginIF($search != '')->andwhere(" ( `code` like '%$search%' or `desc` like '%$search%' )")->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchall();

        return $modifys;
    }

    /**
     * @param $id
     * 手机端获取审核节点
     */
    public function getHistoryNodesApi($id){
        $modify = $this->getByID($id);
        $reviewFailReason = json_decode($modify->reviewFailReason,true);
        $this->app->loadLang('outwarddelivery');
        $res = $this->dao->select('version')->from(TABLE_REVIEWNODE)->where('objectType')->eq('modify')->andWhere('objectID')->eq($id)->groupby('version')->fetchall();
        $versions = array_column($res,'version');
        foreach ($versions as $version) {
            $data = $this->loadModel('review')->getNodes('modify', $id, $version);
            foreach ($data as $k=>$v){
                if ($v->status == 'wait' || !(is_array($v->reviewers) && !empty($v->reviewers))){
                    unset($data[$k]);
                }
            }
            $nodes[$version]['nodes'] = $data;
        }
        foreach ($nodes as $key=>$node) {
            $nodes[$key]['countNodes'] = count($node['nodes']);
            if (isset($reviewFailReason[$key][4]) && !empty($reviewFailReason[$key][4])){
                $nodes[$key]['countNodes']++;
            }
            if (isset($reviewFailReason[$key][5]) && !empty($reviewFailReason[$key][5])){
                $nodes[$key]['countNodes']++;
            }
        }
        $data = ['nodes'=>$nodes,'modify'=>$modify,'reviewFailReason'=>$reviewFailReason];
        return $data;
    }

    /**
     * @param $modify
     * 添加消息队列，已办、待办
     * $reviewer 为空时代表无需待办，只是添加已办记录
     */
    public function addNeedMessage($modify,$status=2,$reviewer=''){
        $msg = new stdClass();
        $msg->desc          = $modify->desc;
        $msg->code          = $modify->code;
        $msg->objectType    = 'modify';
        $msg->objectId      = $modify->id;
        $msg->createdBy     = $this->app->user->account;
        $msg->deptId        = $modify->createdDept;
        $msg->formCreatedBy = $modify->createdBy;
        $msg->formCreatedDate = $modify->createdDate;
        $msg->formstatus    = $modify->status;
        $msg->status        = $status;
        $msg->reviewer      = $reviewer;
        $msg->version       = $modify->version;
        $this->dao->insert(TABLE_NEED_DEAL_MESSAGE)->data($msg)->exec();
    }

    /**
     * @param int $id 部门id
     * 修改上海分公司节点名称
     */
    public function resetNodeAndReviewerName($id=0){
        $depts = $this->loadModel('dept')->getRecurveSubDeptIds(30);
        if ((in_array($this->app->user->dept,$depts) &&  in_array($this->app->getMethodName(),['create','copy'])) || (in_array($id,$depts) && !in_array($this->app->getMethodName(),['create','copy']))){
            $this->lang->modify->reviewerList['5'] = '上海分公司领导';
            $this->lang->modify->reviewerList['6'] = '上海分公司总经理';

            $this->lang->modify->reviewNodeList['5'] = '上海分公司领导';
            $this->lang->modify->reviewNodeList['6'] = '上海分公司总经理';
        }

    }
}
