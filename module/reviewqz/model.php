<?php
/**
 * The model file of reviewpqz module of ZenTaoPMS.
 *
 * Created by PhpStorm.
 * User: t_wangjiurong
 * Date: 2023/2/20
 * Time: 9:43
 */
class reviewqzModel extends model
{
    // 清总评审列表搜索
    public function buildSearchForm($queryID, $actionURL){
        $this->config->reviewqz->search['actionURL'] = $actionURL;
        $this->config->reviewqz->search['queryID']   = $queryID;

        $this->loadModel('search')->setSearchParams($this->config->reviewqz->search);
    }

    /**
     *重新获得搜索信息
     *
     * @param $reviewQuery
     * @return mixed
     */
    public function getFormatSearchQuery($reviewQuery){
        //匹配查找模式
        $pattern = "/(`status` = ').*?(')/";
        preg_match($pattern, $reviewQuery, $patternRes);
        if(!empty($patternRes[0])){
            //获取查询状态
            $findInfo = $patternRes[0];
            $findStatus = substr($findInfo, 12, -1);
            $searchOneToManyStatusList = $this->lang->reviewqz->searchOneToManyStatusList;
            $oneToManyStatusKeys = array_keys($searchOneToManyStatusList);
            if(in_array($findStatus, $oneToManyStatusKeys)){
                $includeStatusArray = $searchOneToManyStatusList[$findStatus];
                $replaceInfo = "status in ('" . implode("','", $includeStatusArray) . "') ";
                $reviewQuery = str_replace($findInfo, $replaceInfo, $reviewQuery);
            }
        }
        return $reviewQuery;
    }

    /**
     * 查找清总评审数据列表
     *
     * @param $browseType
     * @param $queryID
     * @param $orderBy
     * @param null $pager
     * @return mixed
     */
    public function reviewList($browseType, $queryID, $orderBy, $pager = null, $source = ''){
        $reviewqzQuery = '';
        if($browseType == 'bySearch')
        {
            $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : '';
            if($query)
            {
                $this->session->set('reviewqzQuery', $query->sql);
                $this->session->set('reviewqzForm', $query->form);
            }
            if($this->session->reviewqzQuery == false) $this->session->set('reviewqzQuery', ' 1 = 1');
            $reviewqzQuery = $this->session->reviewqzQuery;
            $reviewqzQuery = $this->getFormatSearchQuery($reviewqzQuery);
        }

        //搜索中一个状态可能对应多个状态的查询
        $searchOneToManyStatusList = $this->lang->reviewqz->searchOneToManyStatusList;
        $oneToManyStatusKeys = array_keys($searchOneToManyStatusList);

        //拼接查询条件
        $user = $this->app->user->account;
        $liasisonOfficer = $this->config->reviewqz->liasisonOfficer;
        if($browseType == 'wait'){
            $andWhere = ' where (FIND_IN_SET("'.$user.'",dealUser))';
        }else{
            $andWhere = $user == 'admin' || in_array($user,explode(',',$liasisonOfficer))? "" : " where ((t4.review = '$user'))";
        }
        //if(empty($ids)){
            // 获取评审id范围
            $ids = $this->dao->query("select id from (
	       select t1.id id,t3.reviewer review,t1.dealUser dealUser from `zt_reviewqz` as t1
           left join `zt_reviewnode` as t2 on
                t2.objectID = t1.id
                and t2.objectType = 'reviewqz'
           left join `zt_reviewer` as t3 on
                t2.id = t3.node
           where t1.deleted = '0') as t4 $andWhere group by id order by `id` desc")->fetchAll();
            $ids = array_column($ids,'id');
        //}
        $data = $this->dao->select('id,title,project,planReviewMeetingTime,applicant,status,dealUser,liasisonOfficer,timeInterval')->from(TABLE_REVIEWQZ)
            ->where('id')->in($ids)
            ->beginIF($browseType == 'bySearch')->andWhere($reviewqzQuery)->fi()
            ->beginIF(in_array($browseType, $oneToManyStatusKeys))->andWhere('status')->in(zget($searchOneToManyStatusList, $browseType, []))->fi()
            ->beginIF(($browseType != 'all') && ($browseType != 'bySearch') && ($browseType != 'wait') && (!in_array($browseType, $oneToManyStatusKeys)) && empty($source))
            ->andWhere('status')->eq($browseType)
            ->fi()
            ->orderBy($orderBy)
            ->beginIF(!empty($pager))->page($pager)->fi()
            ->fetchAll();
        return $data;
    }

    // 赋值字段数据
    public function printCell($col, $review, $users){

        $id = $col->id;
        $params = "id=$review->id";

        if ($col->show) {
            $class = "c-$id";
            $title = '';$dealUsersSubStr = '';
            if ($id == 'id') $class .= ' cell-id';
            if ($id == 'status') {
                $class .= ' status-' . $review->status;
                $name = zget($this->lang->reviewqz->browseStatus, $review->status, '');
                $title = "title='{$name}'";
            }
            if ($id == 'result') {
                $class .= ' status-' . $review->result;
            }
            if ($id == 'title') {
                $class .= ' text-left';
                $title = "title='{$review->title}'";
            }
            if ($id == 'dealUser') {
                $class .= ' text-left';
                $dealUsers = $review->dealUser;
                $dealUsersArray = explode(',', $dealUsers);
                //所有审核人
                $dealUsers    = getArrayValuesByKeys($users, $dealUsersArray);
                $dealUsersStr = implode(',', $dealUsers);
                $subCount = 3;
                $dealUsersSubStr = getArraySubValuesStr($dealUsers, $subCount);
                $title = "title='{$dealUsersStr}'";
            }

            echo "<td class='" . $class . "' $title>";

            switch ($id) {
                case 'id':
                    printf('%03d', $review->id);
                    break;

                case 'title':
                    echo html::a(helper::createLink('reviewqz', 'view', "reviewID=$review->id"), $review->title);
                    break;

                case 'project':
                    echo $review->project;
                    break;

                case 'timeInterval':
                    echo zget($this->lang->reviewqz->timeIntervalNameList, $review->timeInterval, '');
                    break;

                case 'planReviewMeetingTime':
                    $planReviewMeetingTime = '';
                    if($review->planReviewMeetingTime != '0000-00-00 00:00:00'){
                        $planReviewMeetingTime = $review->planReviewMeetingTime;
                    }
                    echo $planReviewMeetingTime;
                    break;

                case 'applicant':
                    echo $review->applicant;
                    break;

                case 'status':
                    echo zget($this->lang->reviewqz->browseStatus, $review->status, '');
                    break;

                case 'dealUser':
                    echo $dealUsersSubStr;
                    break;

                case 'actions':
                    $isAllowSubmit = $this->checkIsAllowSubmit($review, $this->app->user->account);
                    $submitClass = common::hasPriv('reviewqz', 'submit') && $isAllowSubmit['result'] ? 'btn' : 'btn disabled';
                    common::hasPriv('reviewqz', 'assignExports') ? common::printIcon('reviewqz', 'assignExports', $params, $review, 'list', 'hand-right', '', 'iframe', true, '', $this->lang->reviewqz->assignExports) : '';
                    common::hasPriv('reviewqz', 'confirm') ? common::printIcon('reviewqz', 'confirm', $params, $review, 'list', 'play', '', 'iframe', true, '', $this->lang->reviewqz->confirm) : '';
                    common::hasPriv('reviewqz', 'feedback') ? common::printIcon('reviewqz', 'feedback', $params, $review, 'list', 'feedback', '', 'iframe', true, '', $this->lang->reviewqz->feedback) : '';
                    echo html::a("javascript:void(0);", '<i class="icon-glasses"></i>', '', "title='{$this->lang->reviewqz->submit}' class='{$submitClass}' onClick='checkSubmit(this);' node-val='".$review->id."'");
                    common::hasPriv('reviewqz', 'submit') ? common::printIcon('reviewqz', 'submit', $params, $review, 'button', 'glasses', '', 'iframe hidden', true, "id='submit_".$review->id."'", $this->lang->reviewqz->submit) : '';
                    common::hasPriv('reviewqz', 'change') ? common::printIcon('reviewqz', 'change', $params, $review, 'list', 'time', '', 'iframe', true, '', $this->lang->reviewqz->change) : '';
            }
            echo '</td>';
        }
    }
    /**
     * 通过清总评审ID获得评审信息
     *
     * @param $qzReviewId
     * @param string $select
     * @return bool
     */
    public function getReviewByQzReviewId($qzReviewId, $select = '*'){
        $data = false;
        if(!$qzReviewId){
            return $data;
        }
        $ret = $this->dao->select($select)->from(TABLE_REVIEWQZ)
            ->where('qzReviewId')->eq($qzReviewId)
            ->andWhere('deleted')->eq('0')
            ->fetch();
        if($ret){
            $data = $ret;
        }
        return $data;
    }

    /**
     * 通过评审ID获得评审信息（评审主表字段信息）
     *
     * @param $reviewId
     * @param string $select
     * @return bool
     */
    public function getReviewById($reviewId, $select = '*'){
        $data = false;
        if(!$reviewId){
            return $data;
        }
        $ret = $this->dao->select($select)->from(TABLE_REVIEWQZ)
            ->where('id')->eq($reviewId)
            ->andWhere('deleted')->eq('0')
            ->fetch();
        if($ret){
            $data = $ret;
        }
        return $data;
    }

    /**
     * 通过ids获得列表
     *
     * @param $ids
     * @param string $select
     * @return bool
     */
    public function getListByIds($ids, $select = '*'){
        $data = [];
        if(!$ids){
            return $data;
        }
        $ret = $this->dao->select($select)->from(TABLE_REVIEWQZ)
            ->where('id')->in($ids)
            ->andWhere('deleted')->eq('0')
            ->fetchAll();
        if($ret){
            $data = $ret;
        }
        return $data;
    }


    /**
     * 通过评审ID获得评审信息的所有字段信息
     *
     * @param $reviewId
     * @return bool
     */
    public function getByID($reviewId){
        $data = false;
        if(!$reviewId){
            return $data;
        }
        $ret = $this->dao->select('*')->from(TABLE_REVIEWQZ)
            ->where('id')->eq($reviewId)
            ->andWhere('deleted')->eq('0')
            ->fetch();
        if($ret){
            $objectType = $this->lang->reviewqz->objectType;
            //文件
            $ret->files = $this->loadModel('file')->getByObject($objectType, $reviewId);
            //状态流转
            $ret->consumed =  $this->loadModel('consumed')->getConsumed($objectType, $reviewId);
            $data = $ret;
        }
        return $data;
    }

    /**
     * 自动匹配专家
     *
     * @param $planJinkeExports
     * @return array
     */
    public function autoMapExports($planJinkeExports){
        $res = [
            'result' => false,
            'data' => [],
        ];
        if(!($planJinkeExports)){
            return $res;
        }
        $expertArray = [];
        if(strrpos($planJinkeExports,',')){
            $expertArray = explode(',',$planJinkeExports);
        }elseif(strrpos($planJinkeExports,'，')){
            $expertArray = explode('，',$planJinkeExports);
        }elseif(strrpos($planJinkeExports,'、')){
            $expertArray = explode('、',$planJinkeExports);
        }
        $expertArray = array_flip(array_flip(array_filter($expertArray)));
        $users = $this->loadModel('user')->getUserListByRealNames($expertArray, 'account,realname');
        if($users){
            $users = array_column($users, 'realname', 'account');
            $usersNames = array_values($users);
            $diffUsers = array_diff($expertArray, $usersNames);
            if(empty($diffUsers) && count($expertArray) == count($usersNames)){
                $usersAccounts = array_keys($users);
                $res['result'] = true;
                $res['data'] = $usersAccounts;
            }
        }
        return $res;
    }


    /**
     *通过接口创建清总评审
     *
     * @return array
     */
    public function createByApi(){
        $res = [
            'result' => false,
            'message' => '',
            'data' > [],
        ];
        //评审接口人
        $liasisonOfficer = $this->config->reviewqz->liasisonOfficer;
        $objectType  = $this->lang->reviewqz->objectType;
        $createBy    = $this->lang->reviewqz->defCreateBy;
        $currentTime = helper::now();

        $data = fixer::input('post')
            ->add('liasisonOfficer', $liasisonOfficer)
            ->add('createBy', $createBy)
            ->add('createTime', $currentTime)
            ->add('num', 0)
            ->get();
        //创建时初始状态
        $nextStatus  = $this->lang->reviewqz->statusList['waitAssign']; //待指派专家
        $dealUsers   = $liasisonOfficer;  //待处理人评审接口人
        if($data->planJinkeExports){ //拟参会专家
            $res = $this->autoMapExports($data->planJinkeExports);
            if($res['result']){ //自动匹配专家成功
                $nextStatus = $this->lang->reviewqz->statusList['expertConfirm'];
                $realJinkeExports = implode(',', $res['data']);
                $dealUsers = $realJinkeExports;
            }
        }
        $data->status   = $nextStatus;
        $data->dealUser = $dealUsers;

        //保存数据
        $this->dao->insert(TABLE_REVIEWQZ)->data($data)->autoCheck()->batchCheck($this->config->reviewqz->create->requiredFields, 'notempty')->exec();
        $reviewId = $this->dao->lastInsertId();

        //保存附件
        if(!empty($data->relationFiles)){
            $relationFiles = $data->relationFiles;
            foreach ($relationFiles as $file){
                $this->loadModel('file')->saveApiFile($objectType, $reviewId, $file['url'], $file['fileName']);// 下载并记录测试报告附件
            }
        }
        //返回
        if(dao::isError()){
            $res['message'] = dao::getError();
            return $res;
        }

        //是否需要增加审核节点
        $isAddNode = $this->getIsAddReviewNode($nextStatus);
        if($isAddNode){
            $reviewers = explode(',', $dealUsers);
            $reviewStatus = 'pending';
            $stage = 1;
            $nodeCode = $this->getReviewNodeCodeByStatus($nextStatus);
            $extParams = [
                'nodeCode' => $nodeCode,
            ];
            $this->loadModel('review')->addNode($objectType, $reviewId, 0, $reviewers, true, $reviewStatus, $stage, $extParams);
        }

        //记录状态
        $this->loadModel('consumed')->record($objectType, $reviewId, 0, 	$createBy, '', $nextStatus);
        //返回
        $res['result'] = true;
        $res['data']['reviewId'] = $reviewId;
        return $res;
    }

    /**
     * 通过接口更新清总评审信息
     *
     * @param $reviewId
     * @return array
     */
    public function updateByApi($reviewId){
        $res = [
            'result' => false,
            'message' => '',
            'data' > [],
        ];
        $objectType = $this->lang->reviewqz->objectType;
        if(!$reviewId){
            $res['message'] = $this->lang->idEmpty;
            return $res;
        }
        $reviewInfo = $this->getReviewById($reviewId);
        if(!$reviewInfo){
            $res['message'] = $this->lang->infoEmpty;
            return $res;
        }
        //获得更新信息
        $currentTime = helper::now();
        $data = fixer::input('post')
            ->add('editTime', $currentTime)
            ->get();

        $isUpdateStatus  = false;  //是否需要修改状态
        $isSendMail      = false; //是否需要发邮件
        $mailConfTemp    = '';
        $oldStatus       = $reviewInfo->status;
        $oldVersion     = $reviewInfo->version;
        $nextStatus      = $oldStatus;
        $nextVersion     = $oldVersion;
        $dealUsers       = $reviewInfo->dealUser;

        if($data->planReviewMeetingTime != $reviewInfo->planReviewMeetingTime){ //修改建议评审会议召开时间
            if(($oldStatus != $this->lang->reviewqz->statusList['waitAssign']) && ($oldStatus != $this->lang->reviewqz->statusList['expertConfirm'])){
                $nextStatus   = $this->lang->reviewqz->statusList['expertConfirm']; //评审专家确认
                $nextVersion  = $reviewInfo->version + 1;
                $nextNodeCode = $this->getReviewNodeCodeByStatus($nextStatus);
                $dealUsers    = $this->getReviewersByNodeCode($objectType, $reviewId, $reviewInfo->version, $nextNodeCode);
                $mailConfTemp = 'setReviewIssueQzMail';
            }
        }
        //当前状态是待指派专家
        if($nextStatus == $this->lang->reviewqz->statusList['waitAssign']){
            //查询是否修改了
            if($data->planJinkeExports){ //拟参会专家
                $res = $this->autoMapExports($data->planJinkeExports);
                if($res['result']){ //自动匹配专家成功
                    $realJinkeExports = implode(',', $res['data']);
                    $nextStatus = $this->lang->reviewqz->statusList['expertConfirm'];
                    $dealUsers = $realJinkeExports;
                    $mailConfTemp = 'setReviewQzMail';
                }
            }
        }

        if(($nextStatus != $oldStatus) || ($nextVersion != $oldVersion)){
            $isUpdateStatus = true;
            $isSendMail     = true;
        }
        $data->status    = $nextStatus;
        $data->version   = $nextVersion;
        $data->dealUser  = $dealUsers;

        //保存附件
        if($data->relationFiles != $reviewInfo->relationFiles){
            if(!empty($data->relationFiles)){
                $relationFiles = $data->relationFiles;
                foreach ($relationFiles as $file){
                    $this->loadModel('file')->saveApiFile($objectType, $reviewId, $file['url'], $file['fileName']);// 下载并记录测试报告附件
                }
            }else{
                $this->loadModel('file')->deleteFile($objectType, $reviewId);
            }
        }
        //更新
        $this->dao->update(TABLE_REVIEWQZ)->data($data)->autoCheck()
            ->where('id')->eq($reviewId)
            ->exec();

        //变更了状态
        if($isUpdateStatus){
            //重新获得修改后的信息
            $newReviewInfo = $this->getReviewById($reviewId);
            if($oldVersion != $nextVersion){
                $ret = $this->addNewVersionReviewNodes($newReviewInfo);
            }else{
                //忽略节点
                $maxVersion = $this->loadModel('review')->getObjectReviewNodeMaxVersion($reviewId, $objectType);
                $needDealIgnoreIds = $this->loadModel('review')->getUnDealReviewNodes($objectType, $reviewId, $maxVersion);
                if(!empty($needDealIgnoreIds)){
                    $ret = $this->loadModel('review')->ignoreReviewNodeAndReviewers($needDealIgnoreIds);
                }
                //新增新的节点
                $isAddNode = $this->getIsAddReviewNode($nextStatus);
                if($isAddNode){
                    $ret = $this->addReviewNode($newReviewInfo);
                }
            }
        }
        //状态流转
        $this->loadModel('consumed')->record($objectType, $reviewId, 0, $reviewInfo->createBy, $oldStatus, $nextStatus);

        //获得修改信息
        $logChanges = common::createChanges($reviewInfo, $data);
        //返回
        $res['result'] = true;
        $res['data'] = [
            'reviewId'   => $reviewId,
            'isSendMail' => $isSendMail,
            'mailConfTemp' => $mailConfTemp,
            'logChanges' => $logChanges,
        ];
        return $res;
    }

    /**
     * 新增审核节点
     *
     * @param $reviewInfo
     * @return mixed
     */
    public function addReviewNode($reviewInfo){
        $reviewId = $reviewInfo->id;
        $objectType = $this->lang->reviewqz->objectType;
        $version = $reviewInfo->version;
        $dealUserArray = explode(',', $reviewInfo->dealUser);
        $nodeCode = $this->getReviewNodeCodeByStatus($reviewInfo->status);
        $maxStage = $this->loadModel('review')->getReviewMaxStage($reviewId, $objectType, $version);
        $stage = $maxStage + 1;
        $reviewNodes = array(
            array(
                'reviewers' => $dealUserArray,
                'stage'     => $stage,
                'status'    => 'pending',
                'nodeCode'  => $nodeCode,
            )
        );
        $ret = $this->loadModel('review')->submitReview($reviewId, $objectType, $version, $reviewNodes);
        return $ret;
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
    public function getReviewersByNodeCode($objectType, $objectID, $version, $nodeCode){
        $reviewers = '';
        if(!($objectType && $objectID && $nodeCode)){
            return $reviewers;
        }

        $node = $this->dao->select('id')->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq($objectType)
            ->andWhere('objectID')->eq($objectID)
            ->andWhere('version')->eq($version)
            ->andWhere('nodeCode')->eq($nodeCode)
            ->orderBy('stage_desc,id_desc')
            ->fetch();
        if(!$node) {
            return  $reviewers;
        }

        $data = $this->dao->select('reviewer')->from(TABLE_REVIEWER)
            ->where('node')->eq($node->id)
            ->orderBy('id_desc')
            ->fetchPairs();
        if(!empty($data)){
            $reviewers = implode(',', $data);
        }
        return $reviewers;
    }


    /**
     * 新增审批新版本的审核节点
     *
     * @param $reviewInfo
     * @return bool
     */
    public function addNewVersionReviewNodes($reviewInfo){
        $res = false;
        if(!$reviewInfo){
            return $res;
        }
        $objectType = $this->lang->reviewqz->objectType;
        $reviewID = $reviewInfo->id;
        $status   = $reviewInfo->status;
        $version  = $reviewInfo->version;
        $dealUser = $reviewInfo->dealUser;
        //当前节点标识
        $nodeCode = $this->getReviewNodeCodeByStatus($status);

        //将上一版本的pending和wait的状态置为ignore
        $maxVersion = $this->loadModel('review')->getObjectReviewNodeMaxVersion($reviewID, $objectType);
        $needDealIgnoreIds = $this->loadModel('review')->getUnDealReviewNodes($objectType, $reviewID, $maxVersion);
        if(!empty($needDealIgnoreIds)){
            $ret = $this->loadModel('review')->ignoreReviewNodeAndReviewers($needDealIgnoreIds);
        }

        //获得历史版本节点排序
        $stage = $this->loadModel('review')->getNodeStage($objectType, $reviewID, $maxVersion, $nodeCode);
        $stage = $nodeCode == 'expertIsJoinReview' ? $stage+1 : $stage;
        //获得历史节点
        $historyReviews = $this->loadModel('review')->getHistoryReviewers($objectType, $reviewID, $maxVersion, $stage);
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
                    $this->dao->insert(TABLE_REVIEWER)->data($currentNodeReviewer)->exec();
                }
            }
        }
        if($nodeCode == 'expertIsJoinReview'){
            //审核人
            $reviewers = explode(',', $dealUser);
            $reviewStatus = 'pending';
            $extParams = [
                'nodeCode' => $nodeCode,
            ];
            //新增当前节点
            $this->loadModel('review')->addNode($objectType, $reviewID, $version, $reviewers, true, $reviewStatus, $stage, $extParams);
        }
        return true;
    }

    /**
     *获得是否需要新增审核节点
     *
     * @param $status
     * @return bool
     */
    public function getIsAddReviewNode($status){
        $isAddReviewNode = false;
        if(!$status){
            return $isAddReviewNode;
        }
        if(in_array($status, $this->lang->reviewqz->needAddReviewNodeStatusList)){
            $isAddReviewNode = true;
        }
        return $isAddReviewNode;
    }

    /**
     * 通过状态获得对应节点标识
     *
     * @param $status
     * @return string
     */
    public function getReviewNodeCodeByStatus($status){
        $nodeCode = zget($this->lang->reviewqz->statusMapNodeCodeList, $status);
        return $nodeCode;
    }

    //指派专家
    public function assignExports($id){
        $data = fixer::input('post')->get();
        $data = array_filter($data->expertLists);
        if(empty($data)){
            // 报错 必填
            dao::$errors[] = $this->lang->reviewqz->errorNotes['assignError'];
            return false;
        }
        $expertLists = implode(",",$data);

        $info = $this->getReviewById($id);
        if($info->status != 'waitAssign'){
            dao::$errors[] = $this->lang->reviewqz->errorNotes['assignStateError'];
            return false;
        }

        // 新增节点并修改原节点状态
        $this->changeNode('expertConfirm', $data, $id, $info->version, 'assignExpert', '', $this->app->user->account);

        //状态流转
        $this->loadModel('consumed')->record('reviewqz', $id, 0, $this->app->user->account , $info->status, 'expertConfirm');

        // 修改清总评审待处理人和状态
        $newinfo = new stdclass();
        $newinfo->dealUser  = $expertLists;
        $newinfo->status    = 'expertConfirm';
        $this->dao->update(TABLE_REVIEWQZ)->data($newinfo)->where('id')->eq($id)->exec();

        $logChange = common::createChanges($info, $newinfo);
        return $logChange;
    }

    // 新增节点并修改原节点状态
    public function changeNode($nextStatus ,$expertLists, $id, $version, $status, $extra = '', $user = ''){
        $nextStatus  = $this->lang->reviewqz->statusList[$nextStatus];
        $reviewStatus = 'pending';
        $maxStage = $this->loadModel('review')->getReviewMaxStage($id, 'reviewqz', $version);
        $stage = $maxStage + 1;
        $nodeCode = $this->getReviewNodeCodeByStatus($nextStatus);
        if(!empty($extra)){//反馈和变更
            $accountStatus = $extra['status'];
            unset($extra['status']);
            $node = $this->findNode($id, $version, 'expertIsJoinReview');
            $reviewers = $this->dao->select('reviewer,status,grade')->from(TABLE_REVIEWER)
                ->where('node')->eq($node)
                ->fetchAll('reviewer');
            foreach($accountStatus as $account => $state){
                if($reviewers[$account]){
                    $this->dao->update(TABLE_REVIEWER)->set('status')->eq($state)->where('node')->eq($node)->andWhere('reviewer')->eq($account)->exec();
                }else{
                    $reviewer = new stdClass();
                    $reviewer->node        = $node;
                    $reviewer->reviewer    = $account;
                    $reviewer->status      = $state;
                    $reviewer->createdBy   = $this->app->user->account;
                    $reviewer->createdDate = helper::now();
                    $this->dao->insert(TABLE_REVIEWER)->data($reviewer)->exec();
                }
            }
        }
        $extParams = [
            'nodeCode'      => $nodeCode,
        ];
        $res = $this->loadModel('review')->addNode($this->lang->reviewqz->objectType, $id, $version, $expertLists, true, $reviewStatus, $stage, $extParams);

        // 改节点表和处理人表状态
        $nodeId = $this->dao->select('id')->from(TABLE_REVIEWNODE)->where('objectID')->eq($id)->andWhere('objectType')->eq('reviewqz')->andWhere('nodeCode')->eq($status)->andWhere('version')->eq($version)->orderBy('id_desc')->fetch();
        $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('pass')->where('id')->eq($nodeId->id)->exec();

        if(!empty($user)){
            if($user == 'admin'){
                // 操作用户为admin时 处理人表插入admin操作数据
                $grade = $this->dao->select('grade')->from(TABLE_REVIEWER)->where('node')->eq($nodeId->id)->orderBy('id desc')->fetch();
                $admin = new stdClass();
                $admin->node = $nodeId->id;
                $admin->reviewer = admin;
                $admin->status = 'pass';
                $admin->grade = $grade->grade + 1;
                $admin->extra       = isset($extra['comment'])?json_encode($extra):'';
                $admin->createdBy = admin;
                $admin->createdDate = helper::now();
                $this->dao->insert(TABLE_REVIEWER)->data($admin)->exec();
            }else{
                if($user != 'guestcn'){
                    $this->dao->update(TABLE_REVIEWER)->set('status')->eq('pass')->beginIF(isset($extra['comment']))->set('extra')->eq(json_encode($extra))->fi()->where('node')->eq($nodeId->id)->andWhere('reviewer')->eq($user)->exec();
                }
            }
            // 该情况只需1人处理即可 其余处理人状态改为ignore
            $this->dao->update(TABLE_REVIEWER)->set('status')->eq('ignore')->where('node')->eq($nodeId->id)->andWhere('status')->eq('pending')->exec();
        }
        return $res;
    }

    // 插入节点数据
    public function onlyAddNode($objectType, $objectID, $version, $status = 'wait', $stage = 1, $nodeCode){
        $node = new stdClass();
        $node->objectType  = $objectType;
        $node->objectID    = $objectID;
        $node->version     = $version;
        $node->status      = $status;
        $node->stage       = $stage;
        $node->createdBy   = $this->app->user->account;
        $node->createdDate = helper::now();
        $node->nodeCode    = $nodeCode;

        // 插入节点数据
        $this->dao->insert(TABLE_REVIEWNODE)->data($node)->exec();
        if(dao::isError()) return false;
        $nodeID = $this->dao->lastInsertID();
        return $nodeID;
    }

    //专家确认是否参会
    public function confirm($info, $id){
        $data = fixer::input('post')->get();

        if(empty($data->status)){
            //报错 请选择专家是否参会
            dao::$errors[] = $this->lang->reviewqz->errorNotes['confirmError'];
            return false;
        }elseif($info->status != 'expertConfirm' && $info->status != 'expertConfirming'){
            dao::$errors[] = $this->lang->reviewqz->errorNotes['confirmStatusError'];
            return false;
        }

        // 查找当前节点
        $node = $this->findNode($id, $info->version, 'expertIsJoinReview');

        // 改参会专家是否参会状态
        foreach($data->status as $reviewer => $value){
            $this->dao->update(TABLE_REVIEWER)->set('status')->eq($value)->where('node')->eq($node)->andWhere('reviewer')->eq($reviewer)->exec();
        }

        // 接口人新增参会专家
        foreach($data->expertLists as $account){
            $v = new stdclass();
            if(!empty($account)){
                $v->node        = $node;
                $v->status      = 'pending';
                $v->reviewer    = $account;
                $v->createdBy   = $this->app->user->account;
                $v->createdDate = helper::now();
                $this->dao->insert(TABLE_REVIEWER)->data($v)->exec();
            }
        }

        // 存储清总评审表待处理人
        $dealUser = $this->dao->select('reviewer')->from(TABLE_REVIEWER)
            ->where('node')->eq($node)
            ->andWhere('status')->eq('pending')
            ->fetchAll('reviewer');
        $newinfo = new stdClass;
        if(!empty($dealUser)){
            $userList = array_keys($dealUser);
            $userStr = implode(',',$userList);
            $newinfo->status = $this->lang->reviewqz->statusList['expertConfirming'];
        }else{
            // REVIEWER表处理人为空了说明所有指派的专家都确认过了,则走下一步流程
            $newinfo->status = 'waitFeedbackQz';
            $userStr = $this->config->reviewqz->liasisonOfficer;

            $this->changeNode('waitFeedbackQz', explode(',',$this->config->reviewqz->liasisonOfficer), $id, $info->version, 'expertIsJoinReview');

            //状态流转
            $this->loadModel('consumed')->record('reviewqz', $id, 0, $this->app->user->account, 'expertConfirm', 'waitFeedbackQz');
        }
        $newinfo->dealUser = $userStr;
        $this->dao->update(TABLE_REVIEWQZ)->data($newinfo)->where('id')->eq($id)->exec();

        $logChange = common::createChanges($info, $newinfo);
        return $logChange;
    }

    // 查找节点
    public function findNode($id, $version, $node = ''){
        return $this->dao->select('id')->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq('reviewqz')
            ->andWhere('objectID')->eq($id)
            ->andWhere('version')->eq($version)
            ->beginIF($node != '')->andWhere('nodeCode')->eq($node)->fi()
            ->orderBy('id_desc')
            ->fetch('id');
    }

    // 反馈给清总参会专家接口
    public function feedbackQzExperts($id, $data, $source){
        $userStr = '';
        $dealUser = [];
        $expertList = '';
        $reviewr = [];
        $info = $this->getReviewById($id);
        //单选列表
        foreach($data->status as $account => $status){
            if($status == 'pass') $dealUser[] = $account;//参会专家
            $reviewr[$account] = $status;//拟参会专家
        }
        //下拉新增列表
        foreach($data->expertLists as $addExpert){
            if(!empty($addExpert)) {
                $dealUser[] = $addExpert;//参会专家
                $reviewr[$addExpert] = 'pass';//拟参会专家
            }
        }

        $deptUsers = $this->dao->select('account, dept')->from(TABLE_USER)->fetchPairs();
        $realnames = $this->loadModel('user')->getPairs('noletter');
        $depts = $this->dao->select('id, name')->from(TABLE_DEPT)->fetchPairs();

        // 拼接清总接收的数据格式(部门-用户名)
        foreach($dealUser as $value){
            $userStr .= $depts[$deptUsers[$value]] .'-'. $realnames[$value] .',';
            $expertList .= $realnames[$value] .',';
        }
        $listExperts = substr($userStr,0,-1);
        if($source == 'feedback'){
            $url = $this->config->global->feedbackExpertsUrl;
            $consumedStatus = 'waitFeedbackQz';
        }else{
            $url = $this->config->global->feedbackUpDataExpertsUrl;
            $consumedStatus = $source;
        }
        $host    = $_SERVER['HTTP_HOST'];
        $comment = str_replace('src="','src="'.$host, $this->post->comment);
        $pushData = array(
            'Review_ID'      => $info->qzReviewId,
            'jinkezhuanjia'  => $listExperts,
            'remark'         => $comment,
        );
        $headers = array();
        $headers[] = 'App-Id: ' . $this->lang->reviewqz->AppId;//
        $headers[] = 'App-Secret: ' . $this->lang->reviewqz->AppSecret;//
        $result = $this->loadModel('requestlog')->http($url, $pushData, 'POST', 'json', array(), $headers);
        $pushStatus = 0;
        $status = 'fail';
        if (!empty($result)){
            $res = json_decode($result);
            $pushStatus = 2;//失败
            //推送成功
            if ($res->code == 200){
                if($res->data->code == 999){
                    dao::$errors[] = $res->data->message;
                    return false;
                }else{
                    $status = 'success';
                    $pushStatus = 1;
                }
            }
        }else{
            $this->requestlog->saveRequestLog($url, "reviewqz", "反馈清总参会专家", 'POST', $pushData, $result, $status, '');
            dao::$errors[] = $this->lang->reviewqz->apiError;
            return false;
        }
        $expertList = substr($expertList,0,-1);
        $extra = [
                'expertList' => $expertList,//参会专家名称
                'comment' => $this->post->comment,
                'status' => $reviewr,//拟参会专家
            ];

        $this->changeNode('waitQzConfirm', [], $id, $info->version, 'feedbackQz', $extra, $this->app->user->account);
        $num          = $info->num+1;
        $this->dao->update(TABLE_REVIEWQZ)->set('status')->eq('waitQzConfirm')->set('dealUser')->eq('')->set('num')->eq($num)->where('id')->eq($id)->exec();

        //状态流转
        $this->loadModel('consumed')->record('reviewqz', $id, 0, $this->app->user->account, $consumedStatus, 'waitQzConfirm');

        $this->requestlog->saveRequestLog($url, "reviewqz", "反馈清总参会专家", 'POST', $pushData, $result, $status, '');
        return $pushStatus;
    }

    /**
     * 反馈专家到清总接口
     *
     * @param $info
     * @param $expertList
     * @param string $userAccount
     * @param string $comment
     * @param string $source
     * @return int
     */
    public function feedbackQzExpertsApi($info, $expertList, $userAccount = '', $comment = '', $source = 'feedback'){
        $id = $info ->id;
        if($source == 'feedback'){
            $url = $this->config->global->feedbackExpertsUrl;
            $consumedStatus = 'waitFeedbackQz';
        }else{
            $url = $this->config->global->feedbackUpDataExpertsUrl;
            $consumedStatus = $source;
        }
        //参会的
        $dealUsers = [];
        $reviewers  = [];
        $userAccounts = [];
        foreach($expertList as $account => $status){
            if($status == 'pass') {
                $dealUsers[] = $account;//参会专家
            }
            $reviewers[$account] = $status;//拟参会专家
            $userAccounts[] = $account;
        }
        //用户列表
        $userList = $this->loadModel('user')->getUserInfoListByAccounts($userAccounts, 'account,realname,dept');

        //部门列表
        $deptList  = [];
        if($userList){
            $deptIds = array_column($userList, 'dept');
            $deptList = $this->loadModel('dept')->getDeptListByIds($deptIds, 'id,name');
            if($deptList){
                $deptList = array_column($deptList, null, 'id');
            }
        }
        $experts = [];
        $expertNames = [];
        foreach ($dealUsers as $expertAccount){
            $userInfo = zget($userList, $expertAccount, new stdClass());
            $dept     = zget($userInfo, 'dept');
            $realName = zget($userInfo, 'realname');
            $deptInfo = zget($deptList, $dept);
            $deptName = zget($deptInfo, 'name');
            $experts[] = $deptName . '-' . $realName;
            $expertNames[] = $realName;
        }

        $host    = $_SERVER['HTTP_HOST'];
        $tempComment = str_replace('src="','src="'.$host, $comment);
        $pushData = array(
            'Review_ID'      => $info->qzReviewId,
            'jinkezhuanjia' => implode(',', $experts),
            'remark'         => $tempComment,
        );
        $headers = array();
        $headers[] = 'App-Id: ' . $this->lang->reviewqz->AppId;//
        $headers[] = 'App-Secret: ' . $this->lang->reviewqz->AppSecret;//
        $result = $this->loadModel('requestlog')->http($url, $pushData, 'POST', 'json', array(), $headers);
        $requestStatus = 'fail';
        if (!empty($result)){
            $res = json_decode($result);
            //推送成功
            if ($res->code == 200){
                if($res->data->code == 999){
                    $this->requestlog->saveRequestLog($url, "reviewqz", "反馈清总参会专家", 'POST', $pushData, $result, $requestStatus, '');
                    dao::$errors[] = $res->data->message;
                    return false;
                }else{
                    $requestStatus = 'success';
                }
            }else{
                $this->requestlog->saveRequestLog($url, "reviewqz", "反馈清总参会专家", 'POST', $pushData, $result, $requestStatus, '');
                dao::$errors[] = $this->lang->reviewqz->apiError;
                return false;
            }
        }else{
            $this->requestlog->saveRequestLog($url, "reviewqz", "反馈清总参会专家", 'POST', $pushData, $result, $requestStatus, '');
            dao::$errors[] = $this->lang->reviewqz->apiError;
            return false;
        }

        $extra = [
            'expertList' => implode(',', $expertNames),//参会专家名称
            'comment' => $comment,
            'status' => $reviewers,//拟参会专家
        ];
        //用户
        if(!$userAccount){
            $userAccount = $this->app->user->account;
        }

        $this->changeNode('waitQzConfirm', [], $id, $info->version, 'feedbackQz', $extra, $userAccount);
        $num = $info->num + 1;
        $nextStatus = 'waitQzConfirm';
        $dealUsers = '';
        //变更信息
        $updateParams = new stdClass();
        $updateParams->status = $nextStatus;
        $updateParams->dealUser = $dealUsers;
        $updateParams->num = $num;
        $this->dao->update(TABLE_REVIEWQZ)->data($updateParams)->where('id')->eq($id)->exec();

        //状态流转
        $this->loadModel('consumed')->record('reviewqz', $id, 0, $userAccount, $consumedStatus, $nextStatus);
        
        //接口日志
        $this->requestlog->saveRequestLog($url, "reviewqz", "反馈清总参会专家", 'POST', $pushData, $result, $requestStatus, '');

        $logChange = common::createChanges($info, $updateParams);
        return $logChange;
    }

    /**
     * Judge button if can clickable.
     *
     * @param  object $review
     * @param  string $action
     * @access public
     * @return void
     */
    public static function isClickable($review, $action)
    {
        global $app;
        $action = strtolower($action);

        $dealUsers  = [];
        if($review->dealUser){
            $dealUsers = explode(',', $review->dealUser);
        }
        //admin用户暂时也存入到待处理用户中
        $dealUsers[] = 'admin';

        $reviewModel = new reviewqzModel();
        $allowStatusList = [];
        if($action == 'assignexports') { //指派评审专家
            $allowStatusList = $reviewModel->lang->reviewqz->allowAssignExportsStatusList;
        }

        if($action == 'confirm')  { //专家确认是否参会
            $allowStatusList = $reviewModel->lang->reviewqz->allowConfirmStatusList;
        }

        if($action == 'feedback') { //反馈清总
            $allowStatusList = $reviewModel->lang->reviewqz->allowFeedbackStatusList;
        }

        if($action == 'submit'){//专家评审
            $allowStatusList = $reviewModel->lang->reviewqz->allowReviewStatusList;
        }

        if($action == 'change'){//变更操作
            $allowStatusList = $reviewModel->lang->reviewqz->allowChangeStatusList;
            $currentTime = helper::now();
            if($review->planReviewMeetingTime < $currentTime && $review->status == 'reviewPass') return false;
            return ((in_array($review->status, $allowStatusList)) && ((in_array($app->user->account, explode(',',$review->liasisonOfficer))) || $app->user->account == 'admin'));
        }
        return (in_array($review->status, $allowStatusList) && (in_array($app->user->account, $dealUsers)));

    }

    /**
     * 检查是否允许审批
     *
     * @param $info
     * @param $account
     * @return array
     */
    public function checkIsAllowSubmit($info, $account){
        $res = array(
            'result'  => false,
            'message' => '',
        );
        if(empty((array)$info)){
            $res['message'] = $this->lang->common->errorParamId;
            return $res;
        }

        //当前状态
        $status = $info->status;

        if(!in_array($status, $this->lang->reviewqz->allowReviewStatusList)){
            $statusDesc = zget($this->lang->reviewqz->browseStatus, $status);
            $res['message'] = sprintf($this->lang->reviewqz->checkOpResultList['statusError'], $statusDesc, $this->lang->reviewqz->submit);
            return $res;
        }
        $dealUser = $info->dealUser;
        $users = array_filter(explode(',',$dealUser));

        $users[]  = 'admin';
        if(!in_array($account, $users)){
            $res['message'] = sprintf($this->lang->reviewqz->checkOpResultList['userError'], $this->lang->reviewqz->submit);
            return $res;
        }
        $res['result'] = true;
        return $res;

    }

    //接收清总反馈参会专家接口
    public function qzFeedbackApi($reviewQzId, $conclusion, $reason){
        $res = [
            'result' => false,
            'message' => '',
            'data' > [],
        ];
        $info = $this->getReviewByQzReviewId($reviewQzId);
        if(empty($info)){
            //报错 对应评审id不存在
            $res['message'] = '对应评审id不存在。';
            return $res;
        }
        if($info->status != 'waitQzConfirm'){
            $res['message'] = '该评审当前状态不支持接收专家名单意见。';
            return $res;
        }
        $participants = '';$noParticipants = '';$participantsShow = '';
        $users  = $this->loadModel('user')->getPairs('noletter');

        $node = $this->findNode($info->id, $info->version, 'expertIsJoinReview');
        $reviewer = $this->dao->select('reviewer,status')->from(TABLE_REVIEWER)
            ->where('node')->eq($node)
            ->fetchAll();

        foreach($reviewer as $value){
            if($value->status == 'pass'){
                $participants .= $value->reviewer.',';
                $participantsShow .= $users[$value->reviewer].',';
            }else{
                $noParticipants .= $users[$value->reviewer].',';
            }
        }
        $data = new stdClass();
        // 审批通过 发邮件
        if($conclusion == 1){
            $participants = substr($participants,0,-1);
            $status       = 'reviewPass';
            $result       = '审批通过';
            $actionID = $this->loadModel('action')->create('reviewqz', $info->id, '同步', '清总反馈专家意见：通过', $result, $this->lang->reviewqz->defCreateBy);
            $this->sendmail($info->id, $actionID, $participants, substr($participantsShow,0,-1), substr($noParticipants,0,-1));
            $data->dealUser     = $participants;
        }else{
            $status       = 'reviewRefuse';
            $result       = '审批不通过';
            $this->loadModel('action')->create('reviewqz', $info->id, '同步', '清总反馈专家意见：不通过', $result, $this->lang->reviewqz->defCreateBy);
            $data->reason       = $reason;
            $data->dealUser     = $this->config->reviewqz->liasisonOfficer;
        }
        $data->status       = $status;
        $this->dao->update(TABLE_REVIEWQZ)->data($data)->where('id')->eq($info->id)->exec();
        $extra = [
            'result'    => $result,
            'reason'    => $reason,
        ];

        // 不存在当前节点时增加节点(避免接口反复请求同节点同版本数据)
        $nodeVersion = $this->dao->select('id')->from(TABLE_REVIEWNODE)->where('id')->eq($info->id)->andWhere('version')->eq($info->version)->andWhere('nodeCode')->eq($status)->fetch();
        if(empty($nodeVersion)){
            $this->changeNode($data->status, explode(',',$data->dealUser), $info->id, $info->version, 'qzConfirm', '', $this->lang->reviewqz->defCreateBy);
            //状态流转
            $this->loadModel('consumed')->record('reviewqz', $info->id, 0, $this->lang->reviewqz->defCreateBy, 'waitQzConfirm', $status);
        }
        $this->dao->update(TABLE_REVIEWNODE)->set('extra')->eq(json_encode($extra))->set('status')->eq('pass')->where('objectID')->eq($info->id)->andWhere('objectType')->eq('reviewqz')->andWhere('nodeCode')->eq('feedbackQz')->andWhere('version')->eq($info->version)->exec();

        //返回
        $res['result'] = 'success';
        return $res;
    }


    /**
     * Send mail
     *
     * @param $reviewID
     * @param $actionID
     * @param $toList
     * @param string $participants
     * @param string $noParticipants
     * @param $mailConfTemp
     */
    public function sendmail($reviewID, $actionID, $toList = '', $participants = '', $noParticipants = '', $mailConfTemp = 'setReviewQzMail')
    {
        $this->loadModel('mail');
        $users  = $this->loadModel('user')->getPairs('noletter');
        $reviewqz = $this->getById($reviewID);
        if(!$toList){
            $toList = $reviewqz->dealUser;
        }

        /* 获取后台通知中配置的邮件发信。*/
        $this->app->loadLang('custommail');
        $mailConf   = isset($this->config->global->$mailConfTemp) ? $this->config->global->$mailConfTemp : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf   = json_decode($mailConf);

        /* 处理邮件发信的标题和日期。*/
        $mailTitle = vsprintf($mailConf->mailTitle, $mailConf->variables);

        /* Get actions. */
        $action  = $this->loadModel('action')->getById($actionID);
        $history = $this->action->getHistory($actionID);
        $action->history    = isset($history[$actionID]) ? $history[$actionID] : array();

        /* Get mail content. */
        $oldcwd     = getcwd();
        $modulePath = $this->app->getModulePath($appName = '', 'reviewqz');
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
        
        /* Send it. */
        $this->mail->send($toList, $mailTitle, $mailContent, '');
        if($this->mail->isError()) error_log(join("\n", $this->mail->getError()));
    }


    /**
     * 定时任务自动发送邮件
     *
     * @param $reviewqz
     * @param string $mailConfTemp
     * @param string $toList
     */
    public function cronSendMail($reviewqz, $mailConfTemp = 'setReviewQzMail', $toList = ''){
        $this->loadModel('mail');
        $users  = $this->loadModel('user')->getPairs('noletter');
        if(!$toList){
            $toList = $reviewqz->dealUser;
        }

        /* 获取后台通知中配置的邮件发信。*/
        $this->app->loadLang('custommail');
        $mailConf   = isset($this->config->global->$mailConfTemp) ? $this->config->global->$mailConfTemp : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf   = json_decode($mailConf);

        /* 处理邮件发信的标题和日期。*/
        $mailTitle = vsprintf($mailConf->mailTitle, $mailConf->variables);

        /* Get actions. */
        $action  = new  stdClass();

        /* Get mail content. */
        $oldcwd     = getcwd();
        $modulePath = $this->app->getModulePath($appName = '', 'reviewqz');
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

        /* Send it. */
        $this->mail->send($toList, $mailTitle, $mailContent, '');
        if($this->mail->isError()) error_log(join("\n", $this->mail->getError()));

    }

    // 变更
    public function change($info, $expertList){
        $data = fixer::input('post')->get();
        $oldList = [];$newList = [];$dealUser = [];$reviewer = [];
        foreach($expertList as $old){//遍历旧拟参会专家
            $oldList[] = $old->reviewer;
        }
        //单选列表
        foreach($data->status as $account => $status){
            if($status == 'pass') $newList[] = $account;//参会专家
            $reviewer['reviewer'][] = $account;//拟参会专家名单
            $reviewer['status'][$account] = $status;//拟参会专家
        }
        //下拉新增列表
        foreach($data->expertLists as $newAccount){
            if(!empty($newAccount)) {
                $newList[]  = $newAccount;//参会专家
                $dealUser[] = $newAccount;//待处理人
                $reviewer['reviewer'][] = $newAccount;//拟参会专家名单
                $reviewer['status'][$newAccount] = 'pending';//拟参会专家
            }
        }
        $diff = array_diff($newList, $oldList);
        $newVersion = $info->version +1;
        $this->dao->update(TABLE_REVIEWQZ)->set('version')->eq($newVersion)->where('id')->eq($info->id)->exec();
        $newInfo = $this->getReviewById($info->id);
        if(empty($diff)){
            $status = 'waitQzConfirm';
            $this->addNewVersionReviewNodes($newInfo);
            //补充反馈节点
            $this->changeNode('waitFeedbackQz', explode(',',$this->config->reviewqz->liasisonOfficer), $info->id, $newVersion, 'expertIsJoinReview');
            $this->feedbackQzExperts($info->id, $data, $info->status);
        }else{
            $status = 'expertConfirm';
            $this->addNewVersionReviewNodes($newInfo);
            $extra = [
                'status' => $reviewer,//拟参会专家
            ];

            $res = $this->changeNode($status, $reviewer['reviewer'], $info->id, $newInfo->version, $info->status, $extra ,$this->app->user->account);
            //状态流转
            $this->loadModel('consumed')->record('reviewqz', $info->id, 0, $this->app->user->account, $info->status, 'expertConfirm');
            $dealUser = implode(',',$dealUser);
            // 改参会专家是否参会状态
            foreach($reviewer['status'] as $reviewer => $value){
                $this->dao->update(TABLE_REVIEWER)->set('status')->eq($value)->where('node')->eq($res)->andWhere('reviewer')->eq($reviewer)->exec();
            }
        }

        $this->dao->update(TABLE_REVIEWQZ)->set('status')->eq($status)->set('dealUser')->eq($dealUser)->set('version')->eq($newVersion)->where('id')->eq($info->id)->exec();
    }

    /**
     *获得专家反馈列表
     *
     * @param $reviewId
     * @return array
     */
    public function getExportsFeedbackList($reviewId, $num){
        $data = [];$lastVersion = '';$version = '';$nodes = [];
        if(!$reviewId){
            return $data;
        }
        $objectType = $this->lang->reviewqz->objectType;
        $nodeCode   = $this->lang->reviewqz->statusMapNodeCodeList['waitFeedbackQz']; //清总确认
        $ret = $this->dao->select('id,status,version,extra')->from(TABLE_REVIEWNODE)    //查询清总确认节点
            ->where('objectType')->eq($objectType)
            ->andWhere('objectID')->eq($reviewId)
            ->andWhere('nodeCode')->eq($nodeCode)
            ->orderBy('id_desc')
            ->fetchAll();
//        $ret = $this->dao->select('t1.*, t2.reviewer, t2.extra as reviewerExtra, t2.reviewTime')
//            ->from(TABLE_REVIEWNODE)->alias('t1')
//            ->leftJoin(TABLE_REVIEWER)->alias('t2')
//            ->on('t1.id = t2.node')
//            ->where('t1.objectType')->eq($objectType)
//            ->andWhere('t1.objectID')->eq($reviewId)
//            ->andWhere('t1.status')->eq('pass')
//            ->andWhere('t1.nodeCode')->eq($nodeCode)
//            ->andWhere('t2.status')->eq('pass')
//            ->orderBy('t1.id_desc')
//            ->fetchAll();
        if($ret){
            foreach($ret as $key => $info){ //过滤忽略清总确认节点的版本(更新时会忽略 忽略就会升版本)
                if($info->version == $version){
                    unset($ret[$key]);
                }
                if($info->status == 'ignore'){
                    $version = $info->version;
                    unset($ret[$key]);
                }
            }
            foreach($ret as $k => $v){ //同版本只取一个节点 升版本会造成重复数据
                if($lastVersion == $v->version){
                    unset($ret[$k]);
                    continue;
                }else{
                    $lastVersion = $v->version;
                }
                $nodes[] = $v->id;
            }
            $res = $this->dao->select('node,reviewer, extra as reviewerExtra')
                ->from(TABLE_REVIEWER)
                ->where('node')->in($nodes)
                ->andWhere('status')->eq('pass')
                ->fetchAll('node');
            $ret = array_slice($ret,'-'.$num);
            $length = count($ret);
            foreach($ret as $val){
                $extra           = $val->extra ? json_decode($val->extra) :new stdClass();
                $reviewerExtra   = $res[$val->id]->reviewerExtra ? json_decode($res[$val->id]->reviewerExtra) : '';
                if(empty($reviewerExtra)) continue;
                $val->num        = $length;
                $val->expertList = isset($reviewerExtra->expertList) ? $reviewerExtra->expertList: '';
                $val->comment    = isset($reviewerExtra->comment) ? $reviewerExtra->comment: '';
                $val->result     = isset($extra->result) ? $extra->result: '';
                $val->reason     = isset($extra->reason) ? $extra->reason: '';
                $data[] = $val;$length--;
            }
        }
        return $data;
    }

    /**
     * 获得专家审核列表
     *
     * @param $reviewId
     * @param int $version
     * @return array
     */
    public function getExportsReviewResultList($reviewId, $version = 0){
        $data = [];
        if(!$reviewId){
            return $data;
        }
        $objectType = $this->lang->reviewqz->objectType;
        $nodeCode   = 'expertReview'; //专家审核节点
        $data = $this->loadModel('review')->getReviewerListByNodeCode($objectType, $reviewId, $version, $nodeCode);
        return $data;
    }

    public function getPlanExportsList($reviewId, $version = 0){
        $data = [];
        if(!$reviewId){
            return $data;
        }
        $objectType = $this->lang->reviewqz->objectType;
        $nodeCode   = 'expertIsJoinReview'; //专家是否参会
        $data = $this->loadModel('review')->getReviewerListByNodeCode($objectType, $reviewId, $version, $nodeCode);
        return $data;

    }

    // 获取当前版本评审是否被清总确认反馈过
    public function getQzConfirmOrNot($reviewqz){
        return $res = $this->dao->select('*')->from(TABLE_REVIEWNODE)
            ->where('objectID')->eq($reviewqz->id)
            ->andWhere('objectType')->eq('reviewqz')
            ->andWhere('nodeCode')->eq('qzConfirm')
            ->fetch();
    }

    // 获取可见评审标题
    public function getReviewqzTitle($browseType){
        $html = '';
        $reviewList = $this->reviewList('all',0,'id_desc',0);
        if(!$reviewList){
            return $html;
        }
        $ids = array_column($reviewList, 'id');
        $res = $this->dao->select('id,title')
            ->from(TABLE_REVIEWQZ)
            ->where('deleted')->eq(0)
            ->andWhere('id')->in($ids)
            ->orderBy('id_desc')
            ->fetchAll('id');

        $allLink = helper::createLink('reviewissueqz', 'issue', "reviewID=0&status=$browseType");
        $listLink   = '';
        foreach($res as $key => $review)
        {
            $reviewLink = helper::createLink('reviewissueqz', 'issue', "reviewID=$key&status=$browseType");
            $listLink .= html::a(sprintf($reviewLink), '<i class="icon icon-folder-outline"></i>' . $review->title);
        }
        $html  = '<div class="table-row"><div class="table-col col-left"><div class="list-group">' . $listLink . '</div>';
        $html .= '<div class="col-footer">';
        $html .= html::a(sprintf($allLink,''), '<i class="icon icon-cards-view muted"></i>' . $this->lang->exportTypeList['all'], '', 'class="not-list-item"');
        $html .= '</div></div>';
        $html .= '<div class="table-col col-right"><div class="list-group"></div>';

        return $html;
    }

    // 清总创建问题接口
    public function createIssueByApi($reviewId){
        $res = [
            'result' => false,
            'message' => '',
            'data' > [],
        ];
        //评审接口人
        $createBy    = $this->lang->reviewqz->defCreateBy;
        $currentTime = helper::now();

        $data = fixer::input('post')
            ->add('status', 'qzCreated')
            ->add('dealUser', '')
            ->add('reviewId', $reviewId)
            ->add('createBy', $createBy)
            ->add('createTime', $currentTime)
            ->add('raiseBy', $createBy)
            ->add('raiseDate', $currentTime)
            ->add('deleted', '0')
            ->get();
        unset($data->sourceFrom);
        //保存数据
        //$this->dao->insert(TABLE_REVIEWISSUEQZ)->data($data)->autoCheck()->batchCheck($this->config->reviewqz->create->requiredFieldsIssue, 'notempty')->exec();
        $this->dao->insert(TABLE_REVIEWISSUEQZ)->data($data)->exec();
        $reviewId = $this->dao->lastInsertId();
        //返回
        if(dao::isError()){
            $res['message'] = dao::getError();
            return $res;
        }

        //返回
        $res['result'] = true;
        $res['data']['reviewId'] = $reviewId;
        return $res;
    }

    // 清总修改问题接口
    public function updateIssueByApi($qzIssueId){
        $data = fixer::input('post')->get();
        unset($data->sourceFrom);
        $this->dao->update(TABLE_REVIEWISSUEQZ)->data($data)->where('id')->eq($qzIssueId)->exec();
        $issueId = $this->dao->lastInsertId();
        //返回
        if(dao::isError()){
            $res['message'] = dao::getError();
            return $res;
        }

        //返回
        $res['result'] = true;
        $res['data']['issueId'] = $issueId;
        return $res;
    }

    //通过清总评审问题ID获取问题信息
    public function getIssueQz($param, $id){
        $data = false;
        if(!$id){
            return $data;
        }
        $ret = $this->dao->select('id')->from(TABLE_REVIEWISSUEQZ)
            ->where($param)->eq($id)
            ->andWhere('deleted')->eq('0')
            ->fetch();
        if($ret){
            $data = $ret;
        }
        return $data;
    }


    // 专家审批评审
    public function submit($id){
        $data = fixer::input('post')->get();
        $info = $this->getReviewById($id);
        $objectType = $this->lang->reviewqz->objectType;

        if(empty($data->reviewResult)){
            //报错 请选择评审结果
            dao::$errors[] = $this->lang->reviewqz->errorNotes['reviewResultError'];
            return false;
        }
        if(empty($data->reviewDate)){
            dao::$errors[] = $this->lang->reviewqz->errorNotes['reviewDateError'];
            return false;
        }
        if($info->status != 'reviewPass'){
            dao::$errors[] = $this->lang->reviewqz->errorNotes['confirmStatusError'];
            return false;
        }

        // 查询节点数据
        $nodeInfo = $this->loadModel('review')->getNodeByNodeCode($objectType, $id, $info->version, 'expertReview');
        $node = $nodeInfo->id;
        $user = new stdClass();
        $user->status      = $data->reviewResult == 1?'pass':'reject';
        //$user->extra       = json_encode(array_filter($data->ccLists));
        $user->reviewTime  = $data->reviewDate;
        $user->createdBy   = $this->app->user->account;
        $user->createdDate = helper::now();

        if($this->app->user->account != 'admin'){
            $user->comment     = $data->advise;
            $this->dao->update(TABLE_REVIEWER)->data($user)->where('node')->eq($node)->andWhere('reviewer')->eq($this->app->user->account)->exec();
        }else{
            $user->comment     = $data->advise.'（由admin处理）';
            $this->dao->update(TABLE_REVIEWER)->data($user)->where('node')->eq($node)->andWhere('status')->eq('pending')->exec();
        }

        // 存储清总评审表待处理人
        $dealUser = $this->dao->select('reviewer')->from(TABLE_REVIEWER)
            ->where('node')->eq($node)
            ->andWhere('status')->eq('pending')
            ->fetchAll('reviewer');

        $newinfo = new stdClass();
        if(empty($dealUser) || $this->app->user->account == 'admin'){
            $this->changeNode('waitQzFeedback', [], $info->id, $info->version, 'expertReview');
            //状态流转
            $this->loadModel('consumed')->record('reviewqz', $info->id, 0, $this->app->user->account, 'reviewPass', 'waitQzFeedback');
            $newinfo->dealUser   = '';
            $newinfo->status     = 'waitQzFeedback';
            //清总评审问题通过线下邮件发送给清总，不需要接口同步
            //$this->feedbackQzIssues($info);
        }else{
            $userList = array_keys($dealUser);
            $newinfo->dealUser   = implode(',',$userList);
        }

        $this->dao->update(TABLE_REVIEWQZ)->data($newinfo)->where('id')->eq($id)->exec();

        $logChange = common::createChanges($info, $newinfo);
        return $logChange;
    }

    //反馈清总问题列表
    public function feedbackQzIssues($info){
        $issueList = [];$host = $_SERVER['HTTP_HOST'];
        $list = $this->dao->select('id,title,`desc`,raiseBy,raiseDate')->from(TABLE_REVIEWISSUEQZ)->where('reviewId')->eq($info->id)->andWhere('status')->eq('created')->fetchAll('id');
        $issueIds = array_keys($list);
        foreach($list as $value){
            $value->desc = str_replace('src="{','src="{'.$host.'/file-read-', $value->desc);
            $tmpArr = [];
            $tmpArr['Tickets_ID']           = $value->id;
            $tmpArr['Problem_location']     = $value->title;
            $tmpArr['Problem_description']  = $value->desc;
            $tmpArr['Author']               = $value->raiseBy;
            $tmpArr['Propose_time']         = $value->raiseDate;
            $issueList[] = $tmpArr;
        }

        $url = $this->config->global->feedbackQzIssuesUrl;
        $pushData = array(
            'Review_ID'      => $info->qzReviewId,
            'List_issues'    => $issueList,
        );

        $headers = array();
        $headers[] = 'App-Id: ' . $this->lang->reviewqz->AppId;//
        $headers[] = 'App-Secret: ' . $this->lang->reviewqz->AppSecret;//
        $result = $this->loadModel('requestlog')->http($url, $pushData, 'POST', 'json', array(), $headers);
        $pushStatus = 0;
        $status = 'fail';
        $params = new stdClass();
        if (!empty($result)){
            $res = json_decode($result);
            $pushStatus = 2;//失败
            //推送成功
            if ($res->code == 200){
                $status = 'success';
                $pushStatus = 1;
            }
            $params->pushStatus = $pushStatus;
        }else{
            $params->pushStatus = 2;
            $this->requestlog->saveRequestLog($url, "reviewqz", "反馈清总审批结论", 'POST', $pushData, $result, $status, '');
            dao::$errors[] = $this->lang->reviewqz->apiError;
            return false;
        }
        //改问题状态
        $this->dao->update(TABLE_REVIEWISSUEQZ)->set('status')->eq('waitQzFeedback')->where('id')->in($issueIds)->exec();

        $this->requestlog->saveRequestLog($url, "reviewqz", "反馈清总审批结论", 'POST', $pushData, $result, $status, '');
        return $pushStatus;
    }

    // 清总最终结果同步
    public function qzFinalResult($data){
        $reviewInfo           = new stdClass();
        $reviewInfo->status   = $data['finalResult'] == 1 ? 'finalPass' : 'finalReject';
        $reviewInfo->dealUser = '';

        $objectType = $this->lang->reviewqz->objectType;
        $info = $this->getReviewByQzReviewId($data['qzReviewId']);
        // 保存附件
        if($data['relationFiles'] != $info->relationFiles){
            if(!empty($data['relationFiles'])){
                $relationFiles = $data['relationFiles'];
                foreach ($relationFiles as $file){
                    $this->loadModel('file')->saveApiFile($objectType, $info->id, $file['url'], $file['fileName']);// 下载并记录测试报告附件
                }
            }else{
                $this->loadModel('file')->deleteFile($objectType, $info->id);
            }
        }
        if(!empty($data['issueList'])){
            $issueList = json_decode($data['issueList'], 1);
            foreach($issueList as $issue){
                $param = $issue['itemSourcePlatfrom'] == 1 ? 'id': 'qzIssueId';
                $accept = $issue['Dropdown_SHIFOUCAINA'] == '采纳' ? 1 : 0;
                $issueJk                    = new stdClass();
                $issueJk->type              = $issue['Proposal_stage'];
                $issueJk->title             = $issue['wenjianming'];
                $issueJk->desc              = $issue['question_identification'];
                $issueJk->resolutionBy      = $issue['SolveUser'];
                $issueJk->resolutionDate    = $issue['solutionTime'];
                $issueJk->validation        = $issue['Verifier'];
                $issueJk->verifyDate        = $issue['VerifyTime'];
                $issueJk->content           = $issue['Modification_instructions'];
                $issueJk->accept            = $accept;
                $issueJk->proposalType      = $issue['Dropdown_yijianleixing'];
                $issueJk->verifyContent     = $issue['LongText_yanzhengqingkuang'];
                $issueJk->opinionReply      = $issue['opinionReply'];
                $issueJk->status            = 'qzFeedback';
                $issueJk->dealUser          = '';
                $this->dao->update(TABLE_REVIEWISSUEQZ)->data($issueJk)->where($param)->eq($issue['Tickets_ID'])->exec();
                // 添加问题历史记录
                $actionID = $this->loadModel('action')->create('reviewissueqz', $issue['Tickets_ID'], '同步', '清总同步最终结果：' . $issue['Dropdown_SHIFOUCAINA'],'',$this->lang->reviewqz->defCreateBy);
                if((isset($res['data']['logChanges'])) && !empty($res['data']['logChanges'])){
                    $this->action->logHistory($actionID, $res['data']['logChanges']);
                }
            }
        }

        // 改评审状态和增加consumed
        $this->dao->update(TABLE_REVIEWQZ)->data($reviewInfo)->where('id')->eq($info->id)->exec();
        $this->loadModel('consumed')->record('reviewqz', $info->id, 0, $this->app->user->account, 'waitQzFeedback', $reviewInfo->status);

        //返回
        $res['result'] = 'success';
        return $res;
    }

    /**
     * 获得评审列表
     *
     * @param $searchQuery
     * @param string $select
     * @return array
     */
    public function getListBySearchQuery($searchQuery = '', $select = '*'){
        $data = [];
        $ret = $this->dao->select($select)->from(TABLE_REVIEWQZ)
            ->where('deleted')->eq('0')
            ->beginIF($searchQuery != '')->andWhere($searchQuery)->fi()
            ->fetchAll();
        if($ret){
            $data = $ret;
        }
        return $data;
    }

    /**
     * 获得专家未确认是否参会的清总评审了
     *
     * @return array
     */
    public function getExpertUnConfirmJoinMeetingReviewList(){
        $data = [];
        $statusArray = [
            $this->lang->reviewqz->statusList['expertConfirm'],
            $this->lang->reviewqz->statusList['expertConfirming'],
        ];
        $ret = $this->dao->select('*')
            ->from(TABLE_REVIEWQZ)
            ->where('deleted')->eq('0')
            ->andWhere('status')->in($statusArray)
            ->andWhere('planReviewMeetingTime')->gt(helper::now())
            ->fetchAll();
        if($ret){
            $data = $ret;
        }
        return $data;
    }

    /**
     * 需要反馈清总的审核列表
     *
     * @return array
     */
    public function getNeedFeedbackQzReviewList(){
        $data = [];
        $sql  = "SELECT zr.*
                FROM zt_reviewqz zr
                LEFT JOIN zt_reviewnode zrn on zrn.objectType = 'reviewqz' and zrn.version = zr.version and zrn.objectID = zr.id 
                LEFT JOIN zt_reviewer zre on zrn.id = zre.node
                where 1 
                and zr.status = 'waitFeedbackQz'
                and zr.planReviewMeetingTime > NOW()
                and zrn.nodeCode = 'expertIsJoinReview'
                and zre.`status` = 'reject'
                GROUP BY zr.id";
        $ret =  $this->dao->query($sql)->fetchAll();
        if(!empty($ret)){
           $data = $ret;
        }
        return $data;
    }

    /**
     * 获得允许自动反馈清总的评审列表
     *
     * @return array
     */
    public function getAllowAutoFeedbackQzReviewList(){
        $data = [];
        $sql  = "SELECT zr.*, GROUP_CONCAT(zre.reviewer) as reviewers
                from zt_reviewqz zr
                LEFT JOIN zt_reviewnode zrn on zrn.objectType = 'reviewqz' and zrn.version = zr.version and zrn.objectID = zr.id 
                LEFT JOIN zt_reviewer zre on  zre.node = zrn.id
                where 1 
                and zr.status = 'waitFeedbackQz'
                and zr.planReviewMeetingTime > NOW()
                and zr.id not in (
                SELECT zr.id
                        FROM zt_reviewqz zr
                        LEFT JOIN zt_reviewnode zrn on zrn.objectType = 'reviewqz' and zrn.version = zr.version and zrn.objectID = zr.id 
                        LEFT JOIN zt_reviewer zre on zre.node = zrn.id
                        where 1 
                        and zr.status = 'waitFeedbackQz'
                        and zr.planReviewMeetingTime > NOW()
                        and zrn.nodeCode = 'expertIsJoinReview'
                        and zre.`status` = 'reject'
                        GROUP BY zr.id
                )
                and zrn.nodeCode = 'expertIsJoinReview'
                and zrn.`status` = 'pass'
                and zre.`status` = 'pass'
                GROUP BY zr.id ";
        $ret =  $this->dao->query($sql)->fetchAll();

        if(!empty($ret)){
            $data = $ret;
        }
        return $data;
    }

    /**
     * 获得超时未确定是否参会的评审列表
     *
     * @return array
     */
    public function getTimeOutUnSetJoinMeetingReviewList(){
        $data = [];
        $statusArray = [
            $this->lang->reviewqz->statusList['expertConfirm'],
            $this->lang->reviewqz->statusList['expertConfirming'],
        ];
        $ret = $this->dao->select('*')
            ->from(TABLE_REVIEWQZ)
            ->where('deleted')->eq('0')
            ->andWhere('status')->in($statusArray)
            ->andWhere('planReviewMeetingTime')->lt(helper::now())
            ->fetchAll();
        if($ret){
            $data = $ret;
        }
        return $data;
    }


    /**
     * 邮件通知专家是否参会
     */
    public function mailExpertIsJoinMeeting(){
        $mailConfTemp = 'setReviewqzIsJoinMeetingMail';
        $data = $this->getExpertUnConfirmJoinMeetingReviewList();
        $i = 0;
        if($data){
            foreach ($data as $val){
                $ret =  $this->cronSendMail($val, $mailConfTemp);
                $i++;
            }
        }
        return $i;
    }

    /**
     * 邮件通知反馈清总
     */
    public function mailFeedbackQz(){
        $mailConfTemp = 'setReviewqzFeedbackQzMail';
        $data = $this->getNeedFeedbackQzReviewList();
        $i = 0;
        if($data){
            foreach ($data as $val){
                $ret =  $this->cronSendMail($val, $mailConfTemp);
                $i++;
            }
        }
        return $i;
    }

    /**
     * 自动反馈清总
     */
    public function autoFeedbackQz(){
        $userAccount = 'admin';
        $comment = '系统自动推送评审专家到清总';
        $i = 0;
        $data = $this->getAllowAutoFeedbackQzReviewList();
        if($data){
            foreach ($data as $val){
                $reviewers = $val->reviewers;
                $reviewers = explode(',', $reviewers);
                $expertList = [];
                foreach ($reviewers as $reviewer){
                    $expertList[$reviewer] = 'pass';
                }
                $logChanges =  $this->feedbackQzExpertsApi($val, $expertList, $userAccount, $comment);
                if($logChanges){
                    $actionID = $this->loadModel('action')->create('reviewqz', $val->id, '反馈清总专家名单', $comment, '', $userAccount);
                    $this->action->logHistory($actionID, $logChanges);
                    $i++;
                }
            }
        }
        return $i;
    }

    /**
     * 自动设置不参会
     *
     * @return int
     */
    public function  autoSetNotJoinMeeting(){
        $userAccount = 'admin';
        $comment = '系统自动设置不参会';
        $joinStatus = 'reject';
        $actionOp = '专家确认';
        $i = 0;
        $data = $this->getTimeOutUnSetJoinMeetingReviewList();
        if($data){
            $nextStatus    = $this->lang->reviewqz->statusList['waitFeedbackQz']; //待同步清总
            $nextDealUsers = $this->config->reviewqz->liasisonOfficer;
            foreach ($data as $val){
                $dealUser  = $val->dealUser;
                $reviewId  = $val->id;
                $version   = $val->version;
                $oldStatus = $val->status;
                if($dealUser){
                    //查找当前节点
                    $node = $this->findNode($reviewId, $version, 'expertIsJoinReview');
                    // 改参会专家是否参会状态(不参会)
                    $dealUserArray = explode(',', $dealUser);
                    foreach ($dealUserArray as $currentUser){
                        $updateParams = new stdClass();
                        $updateParams->status = $joinStatus;
                        $updateParams->comment = $comment;
                        $updateParams->reviewTime = helper::now();
                        $this->dao->update(TABLE_REVIEWER)->data($updateParams)->where('node')->eq($node)->andWhere('reviewer')->eq($currentUser)->exec();
                    }
                    $updateParams = new stdClass();
                    $updateParams->status = 'pass';
                    $this->dao->update(TABLE_REVIEWNODE)->data($updateParams)->where('id')->eq($node)->exec();
                }
                $updateReviewParams = new stdClass();
                $updateReviewParams->status = $nextStatus;
                $updateReviewParams->dealUser = $nextDealUsers;
                $this->dao->update(TABLE_REVIEWQZ)->data($updateReviewParams)->where('id')->eq($reviewId)->exec();

                //状态流转
                $this->loadModel('consumed')->record('reviewqz', $reviewId, 0, $userAccount, $oldStatus, $nextStatus);
                $isAddNode = $this->getIsAddReviewNode($nextStatus);
                if($isAddNode){
                    $newReviewInfo = $this->getByID($reviewId);
                    $ret = $this->addReviewNode($newReviewInfo);
                }
                //记录日志
                $logChanges = common::createChanges($val, $updateReviewParams);
                if($logChanges){
                    $actionID = $this->loadModel('action')->create('reviewqz', $reviewId, $actionOp, $comment, '', $userAccount);
                    $this->action->logHistory($actionID, $logChanges);
                }
                $i++;
            }
        }
        return $i;
    }

}
