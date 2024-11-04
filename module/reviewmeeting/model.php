<?php

class reviewmeetingModel extends model{


    /**
     *获得允许绑定的会议列表
     *
     * @param $type
     * @param $minTime
     * @param $meetingCode
     * @param $isReNew
     * @return array|void
     */
    public function getAllowBindMeetingList($type, $minTime, $meetingCode = '', $isReNew = false){
        $data = [];
        if(!($type && $minTime)){
            return $data;
        }
        $statusArray = $this->lang->reviewmeeting->allowBindStatusArray;
        $ret = $this->dao->select('id, meetingCode, status, type, meetingPlanTime, owner, reviewer')
            ->from(TABLE_REVIEW_MEETING)
            ->where('deleted')->eq('0')
            ->andWhere('type')->eq($type)
            ->andWhere('status')->in($statusArray)
            ->beginIF(!$isReNew)  //恢复绑定会议时不需要限制时间
             ->andWhere('meetingPlanTime')->ge($minTime)
            ->fi()
            ->orderBy('meetingPlanTime_desc')
            ->fetchAll();

        $currentRet = [];
        if($meetingCode){
            $currentRet = $this->dao->select('id, meetingCode, status, type, meetingPlanTime, owner, reviewer')
                ->from(TABLE_REVIEW_MEETING)
                ->where('deleted')->eq('0')
                ->andWhere('meetingCode')->eq($meetingCode)
                ->fetchAll();
        }
        if(!$ret && !$currentRet){
            return $data;
        }
        $allData = [];
        if($ret){
            $allData = $ret;
        }
        if($currentRet){
            $allData = array_merge($allData, $currentRet);
        }
        $reviewMeetingIds = array_column($allData, 'id');
        //查询详情
        $detailRet = $this->dao->select('review_meeting_id, count(id) as total')
            ->from(TABLE_REVIEW_MEETING_DETAIL)
            ->where('deleted')->eq('0')
            ->andWhere('review_meeting_id')->in($reviewMeetingIds)
            ->groupBy('review_meeting_id')
            ->fetchAll('review_meeting_id');
        //用户
        $users = $this->loadModel('user')->getPairs('noclosed|noletter');
        //加载评审语言
        $this->app->loadLang('review');

        foreach ($allData as $val){
            $review_meeting_id = $val->id;
            $meetingCode = $val->meetingCode;
            if(!isset($data[$meetingCode])){
                $owner = $val->owner;
                $type  = $val->type; //会议类型
                $meetingPlanTime = $val->meetingPlanTime;
                $reviewCount = isset($detailRet[$review_meeting_id])? $detailRet[$review_meeting_id]->total:0;
                $ownerUser    = zget($users, $owner);
                $typeDesc     = zget($this->lang->review->typeList, $type);
                $meetingValue = $ownerUser .'_' . $typeDesc . '_' . $meetingPlanTime . '【' .$reviewCount . '】';
                $data[$meetingCode] = $meetingValue;
            }
        }
        return $data;
    }

    /**
     *获得允许绑定的会议列表
     *
     * @param $minTime
     * @return array
     */
     public function getAllowBindMeetingOwnerList($minTime){
         $data = [];
         if(!$minTime){
             return $data;
         }
         $statusArray = $this->lang->reviewmeeting->allowBindStatusArray;
         $ret = $this->dao->select('meetingCode,owner')
             ->from(TABLE_REVIEW_MEETING)
             ->where('deleted')->eq('0')
             ->andWhere('status')->in($statusArray)
             ->andWhere('meetingPlanTime')->ge($minTime)
             ->orderBy('meetingPlanTime_desc')
             ->fetchAll();
         if($ret){
             $data = array_column($ret, 'owner', 'meetingCode');
         }
         return $data;
     }


    /**
     * 通过会议号获得会议信息
     *
     * @param $meetingCode
     * @param string $select
     * @return stdClass|void
     */
    public function getMeetingByMeetingCode($meetingCode, $select = '*'){
        $data = new stdClass();
        if(!$meetingCode){
            return $data;
        }
        $ret = $this->dao->select($select)
            ->from(TABLE_REVIEW_MEETING)
            ->where('meetingCode')->eq($meetingCode)
            ->andWhere('deleted')->eq('0')
            ->fetch();
        if($ret){
            $data = $ret;
        }
        return $data;
    }

    /**
     * 通过id获得会议信息
     *
     * @param $id
     * @param string $select
     * @return stdClass|void
     */
    public function getMeetingById($id, $select = '*'){
        if(!$id){
            return false;
        }
        $ret = $this->dao->select($select)
            ->from(TABLE_REVIEW_MEETING)
            ->where('id')->eq($id)
            ->andWhere('deleted')->eq('0')
            ->fetch();
        return $ret;
    }

    public function getReviewBatchCreatess($meetingCode)
    {
        return $this->dao->select('concat(concat(id,"@"),project), title ')->from(TABLE_REVIEW)->where('deleted')->eq(0)->andWhere('meetingCode')->eq($meetingCode)->orderBy('id_desc')->fetchPairs();

    }
    /**
     * 检查会议评审是否允许审批
     *
     * @author wangjiurong
     * @param $meetingInfo
     * @param $userAccount
     * @return array|void
     */
    public function checkIsAllowReview($meetingInfo, $userAccount){
        $res = array(
            'result'  => false,
            'message' => '',
            'data' => [],
        );
        if(!$meetingInfo){
            $res['message'] = $this->lang->common->errorParamId;
            return $res;
        }
        if(!$userAccount){
            $res['message'] = $this->lang->common->errorParamUser;
            return $res;
        }
        //当前状态
        $status = $meetingInfo->status;
        $allowReviewStatusList = $this->lang->reviewmeeting->allowReviewStatusList;
        //是否在审核状态
        if(!in_array($status, $allowReviewStatusList)){
            $statusDesc = zget($this->lang->reviewmeeting->statusLabelList, $status);
            $res['message'] = sprintf($this->lang->reviewmeeting->checkReviewOpResultList['statusError'], $statusDesc);
            return $res;
        }

        $reviewers  = [];
        if(isset($meetingInfo->dealUser)){
            $reviewers = explode(',', $meetingInfo->dealUser);
        }
        if(!in_array($userAccount, $reviewers)){
            $res['message'] = $this->lang->reviewmeeting->checkReviewOpResultList['userError'];
            return $res;
        }
        $reviewList = $this->loadModel('review')->getReviewListByMeetingCode($meetingInfo->meetingCode, '*', $status);
        if(empty($reviewList)){
            $statusDesc = zget($this->lang->reviewmeeting->statusLabelList, $status);
            $res['message'] = sprintf($this->lang->reviewmeeting->checkReviewOpResultList['reviewEmpty'], $statusDesc);
            return $res;
        }

        //返回
        $data = [
           'reviewList' => $reviewList
        ];
        $res['result'] = true;
        $res['data'] = $data;
        return $res;
    }


    /**
     *通过类型和会议时间获得评审详情
     *
     * @param $type
     * @param $meetingPlanTime
     * @param string $select
     * @return stdClass
     */
    public function getMeetingByTypeAndPlanTime($type, $meetingPlanTime, $select = '*'){
        $data = new stdClass();
        if(!($type && $meetingPlanTime)){
            return $data;
        }
        $ret = $this->dao->select($select)
            ->from(TABLE_REVIEW_MEETING)
            ->where('deleted')->eq('0')
            ->andWhere('type')->eq($type)
            ->andWhere('meetingPlanTime')->eq($meetingPlanTime)
            ->fetch();
        if($ret){
            $data = $ret;
        }
        return $data;
    }

    /**
     *通过评审获得评审详情
     *
     * @param $reviewID
     * @param $select
     * @return stdClass|void
     */
    public function getMeetingDetailInfoByReviewId($reviewID, $select = '*'){
        $data = new stdClass();
        if(!$reviewID){
            return $data;
        }
        $ret = $this->dao->select($select)
            ->from(TABLE_REVIEW_MEETING_DETAIL)
            ->where('review_id')->eq($reviewID)
            ->andWhere('deleted')->eq('0')
            ->orderBy('id_desc')
            ->fetch();
        if($ret){
            $data = $ret;
        }
        return $data;
    }

    /**
     *通过评审id和会议单号获得评审详情
     *
     * @param $reviewID
     * @param $meetingCode
     * @param $select
     * @return stdClass|void
     */
    public function getDetailInfoByReviewIdAndMeetingCode($reviewID, $meetingCode = '', $select = '*'){
        $data = new stdClass();
        if(!$reviewID){
            return $data;
        }
        $ret = $this->dao->select($select)
            ->from(TABLE_REVIEW_MEETING_DETAIL)
            ->where('review_id')->eq($reviewID)
            ->andWhere('deleted')->eq('0')
            ->orderBy('id_desc')
            ->fetch();
        if($ret){
            $data = $ret;
        }
        return $data;
    }



    /**
     *检查会议信息是否允许绑定
     *
     * @param $meetingInfo
     * @return array
     */
    public function checkMeetingIsAllowBind($meetingInfo){
        $res = array(
            'result'  => false,
            'message' => '',
        );
        if(!$meetingInfo){
            $res['message'] = $this->lang->reviewmeeting->checkBind['meetingEmpty'];
            return $res;
        }
        //检查当前状态是否允许绑定
        $statusArray = $this->lang->reviewmeeting->allowBindStatusArray;
        if(!in_array($meetingInfo->status, $statusArray)){
            $statusDesc = zget($this->lang->reviewmeeting->statusLabelList, $meetingInfo->status);
            $res['message'] = sprintf($this->lang->reviewmeeting->checkBind['meetingStatusError'], $statusDesc);
            return $res;
        }
        $res['result'] = true;
        return $res;
    }


    /**
     * 绑定会议评审
     *
     * @param $meetingCode
     * @param $reviewInfo
     * @param $reviewStatus
     * @param string $oldMeetingInfo
     * @return array
     */
    public function bindToReviewMeeting($meetingCode, $reviewInfo, $reviewStatus, $oldMeetingInfo = ''){
        $data = new stdClass();
        $res = array(
            'result'  => false,
            'message' => '',
            'data' => $data,
        );
        $reviewID = $reviewInfo->id;
        if(!($meetingCode && $reviewID && $reviewStatus)){
            $res['message'] = $this->lang->reviewmeeting->checkBind['paramsError'];
            return $res;
        }
        //获得会议信息
        $meetingInfo = $this->getMeetingByMeetingCode($meetingCode);

        $res = $this->checkMeetingIsAllowBind($meetingInfo);
        if(!$res['result']){
            return $res;
        }

        $reviewMeetingId = $meetingInfo->id;
        //获得评审详情信息
        $meetingDetailInfo = $this->getMeetingDetailInfoByReviewId($reviewID);
        $oldMeetingCode = '';

        //当前时间
        $currentTime = helper::now();
        $isAdd = false;
        if($meetingDetailInfo && isset($meetingDetailInfo->id)){
            $tempMeetingCode = $meetingDetailInfo->meetingCode;
            if(($meetingDetailInfo->meetingCode != $meetingCode) || ($reviewStatus != $meetingDetailInfo->status)){
                $tempMeetingInfo = $this->getMeetingByMeetingCode($tempMeetingCode);
                if($tempMeetingInfo->status != 'pass'){
                    $oldMeetingCode = $meetingDetailInfo->meetingCode; //修改前关联的会议评审
                    $updateParams = new stdClass();
                    $updateParams->review_meeting_id = $reviewMeetingId;
                    $updateParams->meetingCode       = $meetingCode;
                    $updateParams->meetingRealTime   = $meetingInfo->meetingRealTime;
                    $updateParams->status            = $reviewStatus;
                    //更新详情表
                    $this->dao->update(TABLE_REVIEW_MEETING_DETAIL)->data($updateParams)
                        ->autoCheck()
                        ->where('id')->eq($meetingDetailInfo->id)->exec();
                    if(dao::isError()){
                        $res['message'] = $this->lang->reviewmeeting->checkBind['bingError'];
                        return $res;
                    }
                }else{
                    $isAdd = true;
                }
            }
        }else{
            $isAdd = true;
        }

       if($isAdd){
            //插入记录
            $params = new stdClass();
            $params->review_meeting_id = $reviewMeetingId;
            $params->review_id         = $reviewID;
            $params->meetingCode       = $meetingCode;
            $params->meetingRealTime   = $meetingInfo->meetingRealTime;
            $params->status            = $reviewStatus;
            $params->createUser        = $this->app->user->account;
            $params->createTime        = $currentTime;

            //修改信息
            $this->dao->insert(TABLE_REVIEW_MEETING_DETAIL)->data($params)
                ->autoCheck()
                ->exec();
            if(dao::isError()){
                $res['message'] = $this->lang->reviewmeeting->checkBind['bingError'];
                return $res;
            }
        }

        //预计会议时间
        $meetingPlanTime = $meetingInfo->meetingPlanTime;
        $updateParams = new stdClass();
        $updateParams->meetingCode     = $meetingCode;
        $updateParams->meetingPlanTime = $meetingPlanTime;
        $updateParams->owner           = $meetingInfo->owner; //绑定会议的时候，项目评审的评审主席要和会议评审主席一致
        $updateParams->reviewer        = $meetingInfo->reviewer; //绑定会议的时候，项目评审的评审专员和会议的评审专员一致
        //修改会议评审信息
        $this->dao->update(TABLE_REVIEW)->data($updateParams)
            ->autoCheck()
            ->where('id')->eq($reviewID)->exec();
        if(dao::isError()){
            $res['message'] = $this->lang->reviewmeeting->checkBind['bingError'];
            return $res;
        }
        //修改会议评审的预计参会专家
        $meetingPlanExport = $reviewInfo->meetingPlanExport;
        $meetingPlanExportArray = explode(',', $meetingPlanExport);
        $oldMeetingPlanExport = $meetingInfo->meetingPlanExport;
        $oldMeetingPlanExportArray = explode(',', $oldMeetingPlanExport);
        $diffMeetingPlanExportArray = array_diff($meetingPlanExportArray, $oldMeetingPlanExportArray);
        //更改预计会议专家
        if(!empty($diffMeetingPlanExportArray)){
            $allPlanExportArray = array_merge($diffMeetingPlanExportArray, $oldMeetingPlanExportArray);
            $allPlanExport = implode(',', $allPlanExportArray);
            $this->dao->update(TABLE_REVIEW_MEETING)->set('meetingPlanExport')->eq($allPlanExport)
                ->autoCheck()
                ->where('id')->eq($meetingInfo->id)->exec();
            if(dao::isError()){
                $res['message'] = $this->lang->reviewmeeting->checkBind['bingError'];
                return $res;
            }
        }
        $meetingPlanType = $_POST['meetingPlanType'];
        if($meetingPlanType != 1){
            //修改会议评审的状态
            $checkRes = $this->updateReviewMeetingStatus($meetingCode, $oldMeetingInfo);
            if(!$checkRes['result']){
                $res['message'] = $this->lang->reviewmeeting->checkBind['updateStatusError'];
                return $res;
            }

            if($oldMeetingCode && ($oldMeetingCode != $meetingCode)){
                $checkRes = $this->updateReviewMeetingStatus($oldMeetingCode);
                if(!$checkRes['result']){
                    $res['message'] = $this->lang->reviewmeeting->checkBind['updateStatusError'];
                    return $res;
                }
            }
        }

        $res['result'] = true;
        $data->meetingCode = $meetingCode;
        $res['data']     = $data;
        return $res;
    }

    /**
     * 获得会议评审下的项目评审数量
     *
     * @param $meetingCode
     * @param $status
     * @param $exWhere
     * @return int
     */
    public function getReviewMeetingValidDetailCount($meetingCode, $status = '', $exWhere = ''){
        $count = 0;
        $ret = $this->dao->select('count(id) as total')
            ->from(TABLE_REVIEW_MEETING_DETAIL)
            ->where('meetingCode')->eq($meetingCode)
            ->andWhere('deleted')->eq('0')
            ->beginIF($exWhere)->andWhere($exWhere)->fi()
            ->beginIF(!empty($status))->andWhere('status')->eq($status)->fi()
            ->fetch();
        if($ret){
            $count = $ret->total;
        }
        return $count;
    }
    /**
     *根据会议单号获得项目评审信息
     *
     * @param $meetingCode
     * @param string $select
     * @return false
     */
    public function getOneReviewInfoByMeetingCode($meetingCode, $select = '*'){
        if(!$meetingCode){
            return false;
        }
        $data = $this->dao->select($select)->from(TABLE_REVIEW)

            ->where('meetingCode')->eq($meetingCode)
            ->orderBy('id_desc')
            ->fetchAll();
        return $data;
    }

    /**
     * 根据会议号获取评审详情
     * @param $meetingCode
     * @param string $select
     * @return false
     */
    public function getMeetingInfoByMeetingCode($meetingCode){
        if (!$meetingCode) {
            return false;
        }
        $data = $this->dao->select('t1.*,group_concat(distinct t3.title) title,group_concat(distinct t3.object) object,t1.meetingPlanExport,
        group_concat(distinct t3.relatedUsers) relatedUsers,group_concat(distinct t3.createdBy)createdBys,
        group_concat(distinct t3.createdDept) createdDept,group_concat(distinct t3.reviewer) allreviewer')
            ->from(TABLE_REVIEW_MEETING)->alias('t1')
            ->leftJoin(TABLE_REVIEW)->alias('t3')
            ->on('t1.meetingCode = t3.meetingCode')
            ->where('t3.deleted')->eq(0)
            ->andWhere('t1.meetingCode')->eq($meetingCode)
            ->groupby('t1.id')
            ->fetch();
        if(!$data){
            return $data;
        }
        $meetingDetailList = $this->getMeetingDetailList($meetingCode);
        $data->meetingDetailList = $meetingDetailList;

        return $data;
    }
    /**
     *根据会议单号获得项目评审信息
     *
     * @param $meetingCode
     * @param string $select
     * @return false
     */
    public function getOneReviewDetailInfoByMeetingCode($meetingCode, $select = '*'){
        if (!$meetingCode) {
            return false;
        }
        $data = $this->dao->select($select)
            ->from(TABLE_REVIEW_MEETING_DETAIL)
            ->where('meetingCode')->eq($meetingCode)
            ->andWhere('deleted')->eq('0')
            ->fetch();
        return $data;
    }

    /**
     *根据会议单号获得项目评审信息
     *
     * @param $meetingCode
     * @param string $select
     * @return false
     */
    public function getReviewDetailListByMeetingCode($meetingCode, $select = '*')
    {
        if (!$meetingCode) {
            return false;
        }
        $data = $this->dao->select('t1.id,t1.type,t1.owner,t1.meetingPlanTime,t1.meetingCode, t1.meetingSummaryCode,t1.status,t1.reviewer,group_concat(distinct t2.status) meetingstatus,t2.consumed, t2.meetingSummary,t3.*')
            ->from(TABLE_REVIEW_MEETING)->alias('t1')
            ->leftJoin(TABLE_REVIEW_MEETING_DETAIL)->alias('t2')
            ->on('t1.id =  t2.review_meeting_id')
            ->leftJoin(TABLE_REVIEW)->alias('t3')
            ->on('t2.review_id = t3.id')
            ->where('t1.deleted')->eq(0)
            ->andWhere('t3.deleted')->eq(0)
            ->andWhere('t2.deleted')->eq(0)
            ->andWhere('t1.meetingCode')->eq($meetingCode)
            ->groupBy('t3.id')
            ->fetchAll();
        $this->loadModel('review');
        $allowReviewStatusList = $this->lang->review->allowReviewStatusList;
        $allowAssignStatusList = $this->lang->review->allowAssignStatusList;
        foreach ($data as $key => $reviewInfo) {
            $status = $reviewInfo->status;
            $data[$key]->statusDesc = $this->review->getReviewStatusDesc($status, $reviewInfo->rejectStage);
            if (in_array($status, $allowReviewStatusList) || in_array($status, $allowAssignStatusList)) {
                if ($status == 'baseline') {
                    $data[$key]->reviewers = $reviewInfo->dealUser;
                    $data[$key]->dealUser = $reviewInfo->dealUser;
                } else {
                    $reviewVersion = $this->review->getReviewVersion($reviewInfo);
                    $reviewers = $this->review->getReviewer('review', $reviewInfo->id, $reviewVersion, $reviewInfo->reviewStage);
                    $data[$key]->reviewers = $reviewers;
                    $data[$key]->dealUser = $reviewers;
                }
            }
        }
        return $data;
    }

    /**
     * 解除绑定
     *
     * @param $meetingCode
     * @param $reviewID
     * @return array
     */
    public function cancelBindReviewMeeting($meetingCode, $reviewID){
        $res = array(
            'result'  => false,
            'message' => '',
        );
        $meetingInfo = $this->getMeetingByMeetingCode($meetingCode);
        if(!$meetingInfo){
            $res['message'] = $this->lang->reviewmeeting->meetingEmpty;
            return $res;
        }

        //取消评审表信息
        $updateParams = new stdClass();
        $updateParams->meetingCode     = '';
        $updateParams->meetingPlanTime = '';
        $this->dao->update(TABLE_REVIEW)->data($updateParams)
            ->autoCheck()
            ->where('id')->eq($reviewID)
            ->exec();
        if(dao::isError()){
            $res['message'] = $this->lang->reviewmeeting->checkCancelBind['cancelBingError'];
            return $res;
        }
        $meetingDetailInfo = $this->getDetailInfoByReviewIdAndMeetingCode($reviewID, $meetingCode, 'id,status');
        if(($meetingInfo->status == 'pass' && $meetingDetailInfo->status == 'waitMeetingOwnerReview')){ //经过会议的单子，不在解绑
            $res['result'] = true;
            return $res;
        }

        //取消会议详情
        $updateParams = new stdClass();
        $updateParams->deleted = '1';
        $this->dao->update(TABLE_REVIEW_MEETING_DETAIL)->data($updateParams)
            ->autoCheck()
            ->where('meetingCode')->eq($meetingCode)
            ->andWhere('review_id')->eq($reviewID)
            ->andWhere('deleted')->eq('0')
            ->exec();
        if(dao::isError()){
            $res['message'] = $this->lang->reviewmeeting->checkCancelBind['cancelBingError'];
            return $res;
        }
        //修改项目状态
        $res = $this->updateReviewMeetingStatus($meetingCode);
        return $res;
    }

    /**
     *创建会议评审信息
     *
     * @param $reviewInfo
     * @param $reviewInfoStatus
     * @param $meetingPlanTime
     * @return array|void
     */
    public function createReviewMeeting($reviewInfo, $reviewInfoStatus, $meetingPlanTime, $isRenew = '0', $reviewer = ''){
        $data = new stdClass();
        $res = array(
            'result'  => false,
            'message' => '',
            'data' => $data,
        );
        if(!($reviewInfo && $meetingPlanTime)){
            $res['message'] = $this->lang->reviewmeeting->checkBind['paramsError'];
            return $res;
        }
        //新增
        $type = $reviewInfo->type;
        $reviewID = $reviewInfo->id;
        $params = [
            'type'              => $type,
            'meetingPlanTime' => $meetingPlanTime,
            'owner'             => $reviewInfo->owner,
        ];
        $meetingInfo = $this->getInfo($params, 'id');

        if($meetingInfo && isset($meetingInfo->id)){
            $res['message'] = $this->lang->reviewmeeting->checkCreate['meetingExist'];
            return $res;
        }
        $currentUser = $this->app->user->account;
        $currentTime = helper::now();
        $meetingCodeSort = $this->setMeetingCodeSort($reviewInfo->type);
        $meetingCode     = $this->setMeetingCode($reviewInfo->type, $meetingCodeSort);
        //新建
        $params = new stdClass();
        $params->meetingCode     = $meetingCode;
        $params->sortId          = $meetingCodeSort;
        $params->status          = $reviewInfoStatus;
        $params->createUser      = $currentUser;
        $params->createTime      = $currentTime;
        $params->type            = $reviewInfo->type;
        $params->meetingPlanTime = $meetingPlanTime;
        $params->owner           = $reviewInfo->owner;
        $params->reviewer        = !empty($reviewer) ? $reviewer : $reviewInfo->reviewer;
        $params->meetingPlanExport = $reviewInfo->meetingPlanExport;
        $params->allOwner          = $params->owner.','.$params->reviewer; //创建时暂时初始化
        //修改信息
        $this->dao->insert(TABLE_REVIEW_MEETING)->data($params)
            ->autoCheck()
            ->exec();
        if(dao::isError()){
            $res['message'] = $this->lang->reviewmeeting->checkCreate['createError'];
            return $res;
        }
        $reviewMeetingId =  $this->dao->lastInsertID();
        $isAdd = false;
        //获得评审详情信息
        $meetingDetailInfo = $this->getMeetingDetailInfoByReviewId($reviewID);
        if($meetingDetailInfo && isset($meetingDetailInfo->id)){
            $tempMeetingCode = $meetingDetailInfo->meetingCode;
            if($tempMeetingCode != $meetingCode){
                $tempMeetingInfo = $this->getMeetingByMeetingCode($tempMeetingCode);
                if($tempMeetingInfo->status != 'pass'){
                    $updateParams = new stdClass();
                    $updateParams->review_meeting_id = $reviewMeetingId;
                    $updateParams->meetingCode       = $meetingCode;
                    //更新详情表
                    $this->dao->update(TABLE_REVIEW_MEETING_DETAIL)->data($updateParams)
                        ->autoCheck()
                        ->where('id')->eq($meetingDetailInfo->id)->exec();
                    if(dao::isError()){
                        $res['message'] = $this->lang->reviewmeeting->checkCreate['createError'];
                        return $res;
                    }
                }else{
                    $isAdd = true;
                }
            }
        }else{
            $isAdd = true;
        }
        if($isAdd){
            //插入记录
            $params = new stdClass();
            $params->review_meeting_id = $reviewMeetingId;
            $params->review_id         = $reviewID;
            $params->meetingCode       = $meetingCode;
            $params->status            = $reviewInfoStatus;
            $params->createUser        = $currentUser;
            $params->createTime        = $currentTime;

            //修改信息
            $this->dao->insert(TABLE_REVIEW_MEETING_DETAIL)->data($params)
                ->autoCheck()
                ->exec();
            if(dao::isError()){
                $res['message'] = $this->lang->reviewmeeting->checkCreate['createError'];
                return $res;
            }
        }

        //修改项目评审信息
        $reviewUpdateParams = new stdClass();
        $reviewUpdateParams->meetingCode     = $meetingCode;
        $reviewUpdateParams->meetingPlanTime = $meetingPlanTime;
        $this->dao->update(TABLE_REVIEW)->data($reviewUpdateParams)
            ->autoCheck()
            ->where('id')->eq($reviewID)->exec();
        if(dao::isError()){
            $res['message'] = $this->lang->reviewmeeting->checkCreate['createError'];
            return $res;
        }

        $reviewMeetingInfo = $this->getMeetingByMeetingCode($meetingCode);
        //是否需要增加审核节点
        $isAddNode = $this->getIsAddReviewNode($reviewInfoStatus);
        if($isAddNode){
            $ret = $this->addReviewNode($reviewMeetingInfo, $reviewInfoStatus);
        }
        //修改处理人
        $dealUser = $this->getNextStageDealUser($reviewMeetingInfo, $reviewInfoStatus);
        if($dealUser){
            //设置处理人
            $ret = $this->dao->update(TABLE_REVIEW_MEETING)->set('dealUser')->eq($dealUser)
                ->autoCheck()
                ->where('id')->eq($reviewMeetingId)
                ->exec();
        }
        //创建日志
        $this->loadModel('action')->create('reviewmeeting', $reviewMeetingId, 'created', $this->post->comment);
        //流转到会议评审中
        if($reviewMeetingInfo->status == $this->lang->reviewmeeting->statusList['waitMeetingReview'] && $isRenew != 1){
            //记录日志
            $actionID = $this->loadModel('action')->create('reviewmeeting', $reviewMeetingInfo->id, 'autoupdatestatus');
        }

//        //添加会议评审日志，绑定会议单号
//        $actionID = $this->loadModel('action')->create('review', $reviewID, 'bindmeetting', '', $meetingCode);

        //返回成功
        $res['result'] = true;
        $data->meetingCode = $meetingCode;
        $res['data'] = $data;
        return $res;
    }

    /**
     *修改会议评审的参会专家
     *
     * @param $meetingCode
     * @return bool
     */
    public function updateMeetingPlanExports($meetingCode){
        //获得评审列表
        $reviewList = $this->loadModel('review')->getReviewListByMeetingCode($meetingCode, 'meetingPlanExport');
        $meetingPlanExportArray = [];
        if($reviewList){
            foreach ($reviewList as $val){
                $meetingPlanExport = $val->meetingPlanExport;
                if($meetingPlanExport){
                    $meetingPlanExportTemp = explode(',', $meetingPlanExport);
                    $meetingPlanExportArray = array_merge($meetingPlanExportArray, $meetingPlanExportTemp);
                }
            }
            $meetingPlanExportArray = array_flip(array_flip($meetingPlanExportArray));
        }
        $meetingPlanExportStr = implode(',', $meetingPlanExportArray);
        $this->dao->update(TABLE_REVIEW_MEETING)->set('meetingPlanExport')->eq($meetingPlanExportStr)->where('meetingCode')->eq($meetingCode)->exec();
        return true;
    }

    /**
     *新增审核节点
     *
     * @param $reviewMeetingInfo
     * @param $status
     * @return false|void
     */
    public function addReviewNode($reviewMeetingInfo, $status , $reviewIds = ''){
        $res = false;
        if(!($reviewMeetingInfo && $status)){
            return $res;
        }
        //审核节点
        $objectType = 'reviewmeeting';
        $reviewMeetingId = $reviewMeetingInfo->id;
        $meetingCode = $reviewMeetingInfo->meetingCode;
        $version = 0;
        $maxStage = $this->loadModel('review')->getReviewMaxStage($reviewMeetingId, $objectType, $version);
        $stage    = $maxStage + 1;
        $nodeCode = $this->getReviewNodeCodeByStatus($status);
        $dealUser = $this->getNextStageDealUser($reviewMeetingInfo, $status);
        //增加审核节点
        if(!is_array($dealUser)){
            $reviewers = explode(',', $dealUser);
        }else{
            $reviewers = $dealUser;
        }
        //新增会议审核节点信息
        $reviewNodes = array(
            array(
                'reviewers' => $reviewers,
                'stage'     => $stage,
                'nodeCode'  => $nodeCode,
            )
        );
        $this->loadModel('review')->submitReview($reviewMeetingId, $objectType,  $version, $reviewNodes);

        //项目评审审核节点
        $reviewList = $this->loadModel('review')->getReviewListByMeetingCode($meetingCode, 'id,version', '', $reviewIds);
        if(!empty($reviewList)){
            $objectType = 'review';
            foreach ($reviewList as $reviewInfo){
                $reviewId = $reviewInfo->id;
                $version  = $reviewInfo->version;
                $nodeId   = $this->loadModel('review')->getReviewNodeId($objectType, $reviewId, $version, $nodeCode);
                if($nodeId){
                    //删除
                    $this->dao->delete()->from(TABLE_REVIEWNODE)->where('id')->eq($nodeId)->exec();
                    $this->dao->delete()->from(TABLE_REVIEWER)->where('node')->eq($nodeId)->exec();
                }
                //新增
                $maxStage = $this->loadModel('review')->getReviewMaxStage($reviewId, $objectType, $version);
                $stage    = $maxStage + 1;
                //新增会议审核节点信息
                $reviewNodes = array(
                    array(
                        'reviewers' => $reviewers,
                        'stage'     => $stage,
                        'nodeCode'  => $nodeCode,
                    )
                );
                $this->loadModel('review')->submitReview($reviewId, $objectType,  $version, $reviewNodes);
                //修改待处理人
                $this->dao->update(TABLE_REVIEW)->set('dealUser')->eq(implode(',', $reviewers))->where('id')->eq($reviewId)->exec();
            }
        }
        return true;
    }

    /**
     *获得审核节点标识
     *
     * @param $status
     * @return string
     */
    public function getReviewNodeCodeByStatus($status){
        $nodeCode = '';
        switch ($status){
            case $this->lang->reviewmeeting->statusList['waitMeetingReview']: //待线上评审
                $nodeCode = $this->lang->reviewmeeting->nodeCodeList['meetingReview'];//会议评审
                break;

            case $this->lang->reviewmeeting->statusList['waitMeetingOwnerReview']: //评审主席确定会议评审结论
                $nodeCode = $this->lang->reviewmeeting->nodeCodeList['meetingOwnerReview'];//评审主席确定会议评审结论
                break;

            default:
                break;
        }
        return $nodeCode;
    }

    /**
     *设置会议号排序
     *
     * @param $type
     * @param $isUseInit
     * @return string
     */
    public function setMeetingCodeSort($type, $isUseInit = true){
        $meetingCodeSort = '';
        $year =  helper::currentYear();
        $currentYearTime = helper::currentYearTime();
        $ret = $this->dao->select('sortId')
            ->from(TABLE_REVIEW_MEETING)
            ->where('type')->eq($type)
            ->andWhere('createTime')->ge($currentYearTime)
            ->andWhere('sortId')->gt(0)
            ->orderBy('sortId_desc')
            ->fetch();
        if($ret){
            $maxSort = $ret->sortId;
            $meetingCodeSort = $maxSort + 1;
        }else{
            if($isUseInit){
                $initMeetingCode = zget($this->lang->reviewmeeting->initMeetingCodeList, $type);
                if($initMeetingCode){
                    //分割数组
                    $temp = explode('-', $initMeetingCode);
                    $initMeetingCodeSort = $temp[1];
                    $initYear = substr($initMeetingCodeSort, 0, 4);
                    if($initYear == $year){
                        $meetingCodeSort = $initMeetingCodeSort + 1;
                    }else{
                        $meetingCodeSort = $year . '001';
                    }
                }else{
                    $meetingCodeSort = $year . '001';
                }
            }else{
                $meetingCodeSort = $year . '001';
            }
        }
        return $meetingCodeSort;
    }

    /**
     * 设置会议编号
     *
     * @param $type
     * @param $meetingCodeSort
     * @return string
     */
    public function setMeetingCode($type, $meetingCodeSort){
        $meetingCode = '';
        if(!($type && $meetingCodeSort)){
            return $meetingCode;
        }
        $this->app->loadLang('review');
        $typeDesc = zget($this->lang->review->typeList, $type);
        $meetingCode = $typeDesc.'-'.$meetingCodeSort;
        return $meetingCode;
    }

    /**
     *设置会议纪要号排序
     *
     * @param $isUseInit
     * @return string
     */
    public function setMeetingSummaryCodeSort($isUseInit = true){
        $summaryCodeSort = '';
        $year = helper::currentYear();
        $currentYearTime = helper::currentYearTime();
        $ret = $this->dao->select('meetingSummarySortId')
            ->from(TABLE_REVIEW_MEETING)
            ->where('createTime')->ge($currentYearTime)
            ->andWhere('meetingSummarySortId')->gt(0)
            ->orderBy('meetingSummarySortId_desc')
            ->fetch();
        if($ret){
            $maxSort = $ret->meetingSummarySortId;
            $summaryCodeSort = $maxSort + 1;
        }else{
            if($isUseInit){
                $initMeetingCode = zget($this->lang->reviewmeeting->initMeetingSummaryCode, 'initCode');
                if($initMeetingCode){
                    //分割数组
                    $temp = explode('-', $initMeetingCode);
                    $initYear =  $temp[2];
                    $codeSort = $temp[3];
                    //重新设置
                    $temp[3] = $codeSort;
                    if($initYear == $year){
                        $summaryCodeSort = ($initYear . $codeSort) + 1;
                    }else{
                        $summaryCodeSort = $year . '001';
                    }
                }else{
                    $summaryCodeSort = $year . '001';
                }
            }else{
                $summaryCodeSort = $year . '001';
            }
        }
        return $summaryCodeSort;
    }

    /**
     * 设置会议纪要编号
     *
     * @param $type
     * @param $meetingCodeSort
     * @return string
     */
    public function setMeetingSummaryCode($summaryCodeSort){
        $year = helper::currentYear();
        $codeSort = substr($summaryCodeSort, 4);
        $meetingSummaryCode = 'CFIT-REP0304-'.$year . '-'.$codeSort;
        return $meetingSummaryCode;
    }

    /**
     * 修改评审详情状态
     *
     * @param $reviewID
     * @param $reviewStatus
     * @return array|void
     */
    public function updateReviewMeetingDetailStatus($reviewID, $reviewStatus){
        $res = array(
            'result'  => false,
            'message' => '',
        );
        if(!($reviewID && $reviewStatus)){
            $res['message'] = $this->lang->reviewmeeting->paramsError;
            return $res;
        }
        //获得评审详情
        $meetingDetailInfo = $this->getMeetingDetailInfoByReviewId($reviewID);
        if(empty((array)$meetingDetailInfo)){
            $res['message'] = $this->lang->reviewmeeting->meetingDetailEmpty;
            return $res;
        }
        //是否需要修改主会议表状态
        $meetingCode = $meetingDetailInfo->meetingCode;
        $meetingInfo = $this->getMeetingByMeetingCode($meetingCode);
        if(empty((array)$meetingInfo)){
            $res['message'] = $this->lang->reviewmeeting->meetingEmpty;
            return $res;
        }
        //修改评审详情状态
        if($reviewStatus != $meetingDetailInfo->status){
            //修改详情状态
            $meetingDetailId = $meetingDetailInfo->id;
            $this->dao->update(TABLE_REVIEW_MEETING_DETAIL)->set('status')->eq($reviewStatus)
                ->autoCheck()
                ->where('id')->eq($meetingDetailId)
                ->andWhere('deleted')->eq('0')
                ->exec();
            if(dao::isError()){
                $res['message'] = $this->lang->reviewmeeting->checkUpdate['updateError'];
                return $res;
            }
            //修改会议状态
            $checkRes = $this->updateReviewMeetingStatus($meetingCode);
            if(!$checkRes['result']){
                return $checkRes;
            }
        }
        //返回
        $res['result'] = true;
        return $res;
    }

    /**
     *获得下一步处理人
     *
     * @param $meetingInfo
     * @param $nextStatus
     * @param string $newDealUser
     * @return mixed|string
     */
    public function getNextStageDealUser($meetingInfo, $nextStatus, $newDealUser = ''){
        if($newDealUser){
            $dealUser = $newDealUser;
            return $dealUser;
        }
        $dealUser = $meetingInfo->dealUser;
        switch ($nextStatus){
            case $this->lang->reviewmeeting->statusList['waitMeetingReview']:
                $dealUser = $meetingInfo->reviewer;
                break;
            case $this->lang->reviewmeeting->statusList['waitMeetingOwnerReview']:
                $dealUser = $meetingInfo->owner;
                break;
            case $this->lang->reviewmeeting->statusList['pass']:
                $dealUser = '';
                break;
        }
        return  $dealUser;
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
        if(in_array($status, $this->lang->reviewmeeting->needAddReviewNodeStatusList)){
            $isAddReviewNode = true;
        }
        return $isAddReviewNode;
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
        //实例化类
        $reviewmeetingModel = new reviewmeetingModel();
        if($action == 'edit') { //编辑
            $res = $reviewmeetingModel->checkIsAllowEdit($review, $app->user->account);
            return $res['result'];
        }
        if($action == 'review') { //评审
            $res = $reviewmeetingModel->checkIsAllowReview($review, $app->user->account);
            return $res['result'];
        }
        if($action == 'confirmmeeting') { //确认开会
            return  ($review->status != 'pass' && $app->user->account == $review->owner);
        }
        if($action == 'notice') {  //通知
            return  ($review->status != 'pass'&&$app->user->account == $review->reviewer);
        }
        if($action == 'download') { //下载
            $all = explode(',',$review->allOwner);
            return   ($review->status != 'pass' && in_array($app->user->account, $all));
        }
        if($action == 'setmeeting') {
            return   ($app->user->account == $review->reviewer ||$app->user->account == $review->owner);
        }

        if($action == 'downloadfiles'){ //下载
            $all = explode(',',$review->allOwner);
            return   ($review->status != 'pass' && in_array($app->user->account, $all));
        }

        if($action == 'change'){ //变更会议纪要
            return   ($review->status == 'waitMeetingOwnerReview' && $app->user->account == $review->reviewer);
        }

        if($action == 'editissue'||$action == 'deleteissue') {
            return  true;
        }
        if($action == 'editnodeusers'){ //是否允许编辑
            if(!in_array($review->currentSubNode, $review->allowEditNodes)){
                return false;
            }
            $reviewModel = new reviewModel();
            $res = $reviewModel->getIsAllowEditNodeUsers($review->status);
            return  $res;
        }
        if($action == 'editfiles'){ //是否允许编辑附件
            $notAllowEditFileStatusList = [
                'baseline',
                'drop',
                'fail',
                'reviewpass',
            ];

            return  (!in_array($review->status, $notAllowEditFileStatusList) && ($review->createdBy == $app->user->account));

        }
    }

    /**
     *根据项目号获取项目经理
     *
     * @param $meetingCode
     * @param string $select
     * @return false
     */
    public function getPMById($projectId)
    {
        if (!$projectId) {
            return false;
        }
        $planId =  $this->dao->select("id")->from(TABLE_PROJECTPLAN)->where('project')->eq($projectId) ->fetch();
        $data = $this->dao->select("PM")->from(TABLE_PROJECTCREATION)->where('plan')->eq($planId->id) ->fetch();
        /*$data = $this->dao->select("t1.PM")->from(TABLE_PROJECTCREATION)->alias('t1')
            ->leftJoin(TABLE_PROJECTPLAN)->alias('t2')
            ->on('t1.plan')->eq('t2.id')
            ->where('t2.project')->eq($projectId)
            ->printSQL();*/
        return $data->PM;
    }

    /**
     *根据createdDept获取部门领导人
     *
     * @param $meetingCode
     * @param string $select
     * @return false
     */
    public function getManager1ByCreatedDept($createdDept)
    {
        if (!$createdDept) {
            return false;
        }
        $data = $this->dao->select("manager")
            ->from(TABLE_DEPT)
            ->where('id')->eq($createdDept)
            ->orderBy('id_desc')
            ->fetchAll();
        return $data;
    }


    /**
     *获取评审名称
     *
     * @param $meetingCode
     * @param string $select
     * @return false
     */
    public function getReviewTitle($reviewId,$select = '*')
    {
        if (!$reviewId) {
            return false;
        }
        $data = $this->dao->select($select)
            ->from(TABLE_REVIEW)
            ->where('id')->eq($reviewId)
            ->orderBy('id_desc')
            ->fetchAll();
        return $data;
    }


    /**
     *评审操作
     *
     * @param $meetingID
     * @return false|void
     */
    public function review($meetingID){
        $objectType = 'reviewmeeting';
        $meetingInfo = $this->getMeetingById($meetingID);
        //是否允许审批
        $checkRes = $this->checkIsAllowReview($meetingInfo, $this->app->user->account);
        if(!$checkRes['result']){
            return false;
        }
        $checkRes = $this->checkReviewParams($meetingInfo);
        if(!$checkRes['result']){
            return false;
        }
        //审核提交数据
        $data = $checkRes['data'];
        $status = $meetingInfo->status;
        if($status == $this->lang->reviewmeeting->statusList['waitMeetingReview']){ //待会议评审
            $comment = $data->comment;
        }elseif ($status == $this->lang->reviewmeeting->statusList['waitMeetingOwnerReview']){ //待确定会议评审结论
            $comment = $this->post->comment;
        }


        //处理审核操作
        $reviewResult = 'pass';
        $extra = new stdClass();
        $result = $this->loadModel('review')->check($objectType, $meetingID, 0, $reviewResult, $comment, 0, $extra, false);
        if(!$result){
            dao::$errors[] = $this->lang->reviewmeeting->checkReviewOpResultList['opError'];
            return false;
        }
        //下一个状态
        $oldStatus  = $meetingInfo->status;
        $nextStatus = $this->getReviewNextStatus($meetingInfo, $result);
        $dealUser   = $this->getNextStageDealUser($meetingInfo, $nextStatus);

        if($nextStatus == $this->lang->reviewmeeting->statusList['waitMeetingOwnerReview']){
            $res = $this->addReviewMeetingSummaryInfo($meetingInfo, $data, $nextStatus, $dealUser);
        }elseif ($nextStatus == $this->lang->reviewmeeting->statusList['pass']){ //确定会议结论
            $res = $this->loadModel('review')->addReviewMeetingResultInfo($data);
        }
        if(!$res){
            return $res;
        }
        //修改信息
        $updateMeetingParams = new stdClass();
        $updateMeetingParams->status          = $nextStatus;
        $updateMeetingParams->dealUser        = $dealUser;
        if($nextStatus == $this->lang->reviewmeeting->statusList['waitMeetingOwnerReview']){
            $updateMeetingParams->meetingRealTime = $data->meetingRealTime;
            $updateMeetingParams->realExport      =  $data->realExport;
            $updateMeetingParams->realExportVersion =  2;  //实际参会专家版本
            $updateMeetingParams->meetingSummarySortId = $this->setMeetingSummaryCodeSort();
            $updateMeetingParams->meetingSummaryCode   = $this->setMeetingSummaryCode($updateMeetingParams->meetingSummarySortId);
        }
        //修改主记录
        $this->dao->update(TABLE_REVIEW_MEETING)->data($updateMeetingParams)->where('id')->eq($meetingID)->exec();
        if(dao::isError()) {
            return false;
        }
        //添加工时
        $this->loadModel('consumed')->record($objectType, $meetingID, '0', $this->app->user->account, $oldStatus, $nextStatus, $this->post->mailto);

        //是否新增节点
        $isAddNode = $this->getIsAddReviewNode($nextStatus);
        if($isAddNode){
            $res = $this->addReviewNode($meetingInfo, $nextStatus, $data->reviewIds);//有临时入会情况可能
        }

        // 自动关闭
        if($nextStatus == 'pass'){
            foreach($data->detailParams as $key => $reviewInfo){
                // 当评审结果是通过无需修改时调自动关闭方法
                if($reviewInfo->reviewResult == 'passNoNeedEdit'){
                    $this->loadModel('review')->autoclose($data->reviewIds[$key]);
                }
            }
        }

        //获得差异信息
        $extChangeInfo = [];
        //抄送人
        $ext = new stdClass();
        $ext->old = '';
        $ext->new = isset($_POST['mailto'])  ?  implode(' ', $_POST['mailto']) :'';
        $extChangeInfo['mailto'] = $ext;
        //返回
        $logChange = common::createChanges($meetingInfo, $updateMeetingParams, $extChangeInfo);
        return $logChange;
    }

    /**
     * 获得项目评审的平均工时
     *
     * @param $totalConsumed
     * @param $reviewCount
     * @return int
     */
    public function getReviewAverageConsumed($totalConsumed, $reviewCount){
        $averageConsumed = 0;
        if($reviewCount < 1){
            return $averageConsumed;
        }
        //$temp = bcdiv($totalConsumed, $reviewCount, 2);
        $temp = number_format($totalConsumed/$reviewCount, 2);
        $averageConsumed = round($temp, 1);
        if($averageConsumed < 0.1){
            $averageConsumed = 0.1;
        }
        return $averageConsumed;
    }

    /**
     * 新增项目评审会议纪要
     *
     * @param $meetingInfo
     * @param $params
     * @param $nextStatus
     * @param $dealUser
     * @return bool
     */
    public function addReviewMeetingSummaryInfo($meetingInfo, $params, $nextStatus, $dealUser){
        $meetingID    = $meetingInfo->id;
        $meetingCode  = $meetingInfo->meetingCode; //会议号
        $detailParams = $params->detailParams;
        $reviewIds    = array_column($detailParams, 'review_id');
        $reviewList   = $this->loadModel('review')->getReviewListByIds($reviewIds);
        $projectIds  = array_column($reviewList, 'project');
        $projectList = [];
        $projects = $this->loadModel('project')->getInIDs($projectIds);
        if($projects){
            $projectList = array_column($projects, null, 'id');
        }
        $objectType   = 'review';
        $reviewResult = 'pass';
        $extra = new stdClass();
        $reviewCount = count($reviewList);
        $consumed = 0;//$params->consumed;
        $averageConsumed = $this->getReviewAverageConsumed($consumed, $reviewCount);
        //单个项目详情
        foreach ($detailParams as $detailInfo){
            $review_id       = $detailInfo->review_id;
            $reviewInfo      = $reviewList[$review_id];
            $projectCode     = '';
            $projectId       = $reviewInfo->project;
            $projectInfo     = zget($projectList, $projectId);
            if($projectInfo){
                $projectCode = $projectInfo->code;
            }
            //每个项目评审的评审信息
            $comment =  $detailInfo->comment;

            unset($detailInfo->$review_id);
            unset($detailInfo->comment);
            $detailInfo->meetingRealTime  = $params->meetingRealTime;
            $detailInfo->status = $nextStatus;
            $detailInfo->meetingContent = $reviewInfo->title . '（' . $projectInfo->name . ')';

            //修改评审纪要
            $this->dao->update(TABLE_REVIEW_MEETING_DETAIL)->data($detailInfo)
                ->where('review_meeting_id')->eq($meetingID)
                ->andWhere('review_id')->eq($review_id)
                ->exec();
            if(dao::isError()) {
                return false;
            }

            $tempUser = $projectCode.'_'.$meetingCode;
            //新增一条会议评审的工作量
            $this->loadModel('consumed')->record('review', $review_id, '0', $tempUser, $reviewInfo->status, $reviewInfo->status);

            //项目评审表
            $updateParams = new stdClass();
            $updateParams->status           = $nextStatus;
            $updateParams->dealUser         = $dealUser;
            $updateParams->meetingRealTime      =  $params->meetingRealTime;
            $updateParams->lastReviewedBy       = $this->app->user->account;
            $updateParams->lastReviewedDate     = helper::today();
            $updateParams->submitDate = date('Y-m-d');
            $endDate = $this->loadModel('review')->getEndDate($nextStatus,$updateParams->submitDate,$review_id);
            $updateParams->endDate = $endDate;


            $this->dao->update(TABLE_REVIEW)->data($updateParams)->where('id')->eq($review_id)->exec();
            if(dao::isError()) {
                return false;
            }
            //设置单个项目评审审核信息
            $result = $this->loadModel('review')->check($objectType, $review_id, $reviewInfo->version, $reviewResult, $comment, 0, $extra, false);
            if(!$result){
                dao::$errors[] = $this->lang->reviewmeeting->checkReviewOpResultList['opError'];
                return false;
            }

            //添加单个项目工时记录
            $this->loadModel('consumed')->record('review', $review_id, $averageConsumed, $this->app->user->account, $reviewInfo->status, $nextStatus);

            //日志扩展信息
            $logChanges = common::createChanges($reviewInfo, $updateParams);
            $actionID = $this->loadModel('action')->create('review', $review_id, 'reviewed', $this->post->comment, '', '', true);
            if($logChanges) {
                $this->action->logHistory($actionID, $logChanges);
            }
        }
        return true;
    }


    /**
     *获得默认的评审纪要信息
     *
     * @param $reviewIds
     * @param $user
     * @return array|void
     */
    public function getDefMeetingSummaryList($reviewIds, $user){
        $data = [];
        if(!($reviewIds && $user)){
            return $data;
        }
        $ret = $this->dao->select('id as reviewIssueId ,review,`desc`')
            ->from(TABLE_REVIEWISSUE)
            ->where('review')->in($reviewIds)
            ->andWhere('createdBy')->eq($user)
            ->andWhere('deleted')->eq('0')
            ->fetchAll();

        if(!$ret){
            return $data;
        }
        foreach ($ret as $val){
            $reviewId = $val->review;
            unset($val->review);
            $data[$reviewId][] = (array) $val;
        }
        return $data;
    }

    /**
     *获得默认的评审纪要信息
     *
     * @param $reviewId
     * @param $user
     * @return array|void
     */
    public function getMeetingSummaryListByReviewId($reviewId, $user){
        $data = [];
        if(!($reviewId && $user)){
            return $data;
        }
        $ret = $this->dao->select('id as reviewIssueId,`desc`')
            ->from(TABLE_REVIEWISSUE)
            ->where('review')->eq($reviewId)
            ->andWhere('createdBy')->eq($user)
            ->andWhere('deleted')->eq('0')
            ->fetchAll();

        if(!$ret){
            return $data;
        }
        $data = $ret;
        return $data;
    }


    /**
     *获得审核后下一个状态
     *
     * @param $meetingInfo
     * @param $reviewAction
     * @return mixed|string
     */
    public function getReviewNextStatus($meetingInfo, $reviewAction){
        $nextStatus = '';
        if(!($meetingInfo && $reviewAction)){
            return $nextStatus;
        }
        //当前记录状态
        $status = $meetingInfo->status;
        if($reviewAction == 'reject'){
            $nextStatus = $this->lang->reviewmeeting->statusList['reject']; //退回
        }elseif ($reviewAction == 'pass'){
            switch ($status) {
                case $this->lang->review->statusList['waitFormalReview']: //待线上评审
                    $nextStatus = $this->lang->reviewmeeting->statusList['waitMeetingReview']; //待会议评审
                    break;

                case $this->lang->review->statusList['waitMeetingReview']: //待会议评审
                    $nextStatus = $this->lang->reviewmeeting->statusList['waitMeetingOwnerReview']; //待确定会议评审结论
                    break;

                case $this->lang->review->statusList['waitMeetingOwnerReview']: //待确定会议评审结论
                    $nextStatus = $this->lang->reviewmeeting->statusList['pass']; //已确定会议评审结论
                    break;
            }
        }
        return $nextStatus;
    }

    /**
     * 检查会议审核评审纪要审核
     *
     * @param $data
     * @return array
     */
    public function checkMeetingReviewParams($data){
        $res = array(
            'result' => false,
            'data'   => $data,
        );
        //会议评审详情
        $meetingRealExportList  = isset($data->meetingRealExportList) ? $data->meetingRealExportList:[];
        $meetingConsumedList = $data->meetingConsumedList;
        $reviewIds           = $data->reviewIds;
        $reviewList          = $this->loadModel('review')->getReviewListByIds($reviewIds, 'id, title');
        $detailParams = [];
        $meetingRealExport = [];
        //会议评审信息
        $comment = '';
        foreach ($reviewIds as $key => $meetingDetailId){
            $tempKey = $key + 1;
            $reviewId        = $reviewIds[$key];
            $reviewInfo      = $reviewList[$reviewId];
            $realExport      = zget($meetingRealExportList, $reviewId, []);
            $realExport      = array_filter($realExport);
            $meetingConsumed = $meetingConsumedList[$key];
            $record =  '第'. $tempKey . '行';
            if(!$realExport){
                dao::$errors[] = $record.$this->lang->reviewmeeting->checkReviewOpResultList['realExportEmpty'];
                return $res;
            }
            if(!$meetingConsumed){
                dao::$errors[] = $record.$this->lang->reviewmeeting->checkReviewOpResultList['meetingConsumedEmpty'];
                return $res;
            }
            $checkRes = $this->loadModel('consumed')->checkConsumedTwoDecimal($meetingConsumed);
            if(!$checkRes['result']){
                dao::$errors[] = $record.$checkRes['message'];
                return $res;
            }
            $commentKey ='comment_'.$key;
            $temp = new stdClass();
            $temp->review_id       =  $reviewId;
            $temp->realExport      = implode(',', $realExport);
            $temp->consumed        = $meetingConsumed;
            $temp->comment         = $data->$commentKey; //每个项目评审的评审备注信息
            $temp->realExportVersion = 2;
            $detailParams[$key] = $temp;
            $meetingRealExport = array_merge($meetingRealExport, $realExport);
            if($temp->comment){
                $comment .= $reviewInfo->title . '：'. $temp->comment . '<br/>'; //所有的评论信息
            }
        }

        //检查实际会议时间
        if(!isset($data->meetingRealTime) || empty($data->meetingRealTime)){
            dao::$errors['meetingRealTime'] = $this->lang->reviewmeeting->checkReviewOpResultList['meetingRealTimeEmpty'];
            return $res;
        }
        //检查会议时间
       /* $checkRes = $this->loadModel('consumed')->checkConsumedInfo($data->consumed);
        if(!$checkRes){
            return $res;
        }*/
        //去除重复元素
        $meetingRealExport = array_flip(array_flip($meetingRealExport));
        $data->realExport = implode(',', $meetingRealExport);
        $data->comment = $comment;
        $data->detailParams = $detailParams;
        //返回检查结果
        $res['result'] =  true;
        $res['data'] = $data;
        return $res;
    }

    /**
     * 检查会议审核确定评审结论的参数检查
     *
     * @param $data
     * @return array
     */
    public function checkMeetingOwnerReviewParams($data){
        $res = array(
            'result' => false,
            'data'   => $data,
        );
        //会议评审详情
        $resultList      = $data->resultList;
        $verifyReviewers = $data->verifyReviewers;
        $reviewIds       = $data->reviewIds;

        $detailParams = [];

        foreach ($reviewIds as $key => $meetingDetailId){
            $tempKey = $key + 1;
            $reviewId      = $reviewIds[$key];
            $reviewResult  = $resultList[$key];
            $verifyUsers   = isset($verifyReviewers[$key]) ? $verifyReviewers[$key]:[];
            $record =  '第'. $tempKey . '行';
            if(!$reviewResult){
                dao::$errors[] = $record.$this->lang->reviewmeeting->checkReviewOpResultList['resultEmpty'];
                return $res;
            }
            if($reviewResult == 'passNeedEdit'){ //通过需要修改
                if(!$verifyUsers){
                    dao::$errors[] = $record.$this->lang->reviewmeeting->checkReviewOpResultList['verifyReviewersEmpty'];
                    return $res;
                }
            }else{
                if($reviewResult == 'passNoNeedEdit'){
                    $reviewIssueCount = $this->loadModel('reviewissue')->getReviewIssueCount($reviewId);
                    if ($reviewIssueCount > 0) {
                        dao::$errors[] = $this->lang->reviewmeeting->checkResultList['reviewResultError'];
                        return $res;
                    }
                }
                $verifyUsers = [];
            }
            $temp = new stdClass();
            $temp->review_id       =  $reviewId;
            $temp->reviewResult    = $reviewResult;
            $temp->verifyUsers     = $verifyUsers;
            $detailParams[$key] = $temp;
        }

        //修改截至日期
        if(!isset($data->editDeadline) || empty($data->editDeadline)){
            dao::$errors['editDeadline'] = $this->lang->reviewmeeting->checkReviewOpResultList['editDeadlineEmpty'];
            return $res;
        }
        //验证截至日期
        if(!isset($data->verifyDeadline) || empty($data->verifyDeadline)){
            dao::$errors['verifyDeadline'] = $this->lang->reviewmeeting->checkReviewOpResultList['verifyDeadlineEmpty'];
            return $res;
        }

        //检查会议时间
       /* $checkRes = $this->loadModel('consumed')->checkConsumedInfo($data->consumed);
        if(!$checkRes){
            return $res;
        }*/

        $data->detailParams = $detailParams;
        //返回检查结果
        $res['result'] =  true;
        $res['data'] = $data;
        return $res;
    }

    /**
     *检查评审主席确定审核结论的参数
     *
     * @param $meetingInfo
     * @return array
     */
    public function checkReviewParams($meetingInfo){
        $data = fixer::input('post')
            ->stripTags($this->config->reviewmeeting->editor->review['id'], $this->config->allowedTags)
            ->get();
        $status = $meetingInfo->status;
        if($status == $this->lang->reviewmeeting->statusList['waitMeetingReview']){ //待会议评审
            $res = $this->checkMeetingReviewParams($data);
        }elseif ($status == $this->lang->reviewmeeting->statusList['waitMeetingOwnerReview']){ //待确定会议评审结论
            $res = $this->checkMeetingOwnerReviewParams($data);
        }
        return $res;
    }

    /**
     * Send mail.
     *
     * @param  int    $meetingID
     * @param  int    $actionID
     * @access public
     * @return void
     */
    public function sendmail($meetingID, $actionID){
        $this->loadModel('mail');
        $meetingInfo = $this->getById($meetingID);
        $users  = $this->loadModel('user')->getPairs('noletter');
        $outsideList1 = array('' => '') +$this->loadModel('user')->getUsersNameByType('outsideExpertType');
        $outsideList2 = array('' => '') +$this->loadModel('user')->getUsersNameByType('outside');
        $users = array_merge($users, $outsideList1, $outsideList2);
        //部门信息
        $deptMap = $this->loadModel('dept')->getOptionMenu();
        $this->app->loadLang('projectplan');
        $meetingCode = $meetingInfo->meetingCode;
        //会议评审相关项目评审
        $reviewList = $this->loadModel('review')->getReviewListByMeetingCode($meetingCode);
        $reviewTitleList  = []; //评审内容项目标题并集
        $createdDeptList  = []; //评审发起部门
        $createdByList    = []; //评审发起人
        $projectPmList    = [];  //项目经理
        $managerList      = [];//部门领导
        $projectNameList  = []; //项目名称
        $projectTypeList  = []; //项目类型
        $projectBasisList = []; //项目来源
        if(!empty($reviewList)){
            $projectIds      = array_column($reviewList, 'project');
            $projectList     = $this->loadModel('project')->getProjectListByIds($projectIds, 'id,name,PM');
            $projectPlanList = $this->loadModel('projectplan')->getProjectPlanListByProjectIds($projectIds, 'id,basis,type');
            $projectPmList   = array_column($projectList, 'PM');
            $projectNameList = array_column($projectList, 'name');
            $projectTypeList = array_column($projectPlanList, 'type');
            $projectBasisList = array_column($projectPlanList, 'basis');
            //创建人部门信息
            $createdDeptIds = array_column($reviewList, 'createdDept');
            $deptList = $this->loadModel('dept')->getDeptListByIds($createdDeptIds, 'manager');
            foreach ($deptList as $deptInfo){
                $manager = $deptInfo->manager;
                if($manager){
                    $managerArray = explode(',', $manager);
                    $managerList = array_merge($managerList, $managerArray);
                }
                $managerList = array_flip(array_flip($managerList));
            }
            //项目信息
            foreach ($reviewList as $val){
                $reviewTitleList[] = $val->title;
                $createdDeptList[] = $val->createdDept;
                $createdByList[]   = $val->createdBy;
            }
        }

        /* Get action info. */
        $action          = $this->loadModel('action')->getById($actionID);
        $history         = $this->action->getHistory($actionID);
        $action->history = isset($history[$actionID]) ? $history[$actionID] : array();

        /*获取邮件 收抄件人 */
        $sendUsers = $this->getPendingToAndCcList($meetingInfo);

        //重新设置历史详情为空
        $action->history = array();

        $toList = $sendUsers['toList'];
        $ccList = $sendUsers['ccList'];

        /* 获取后台通知中配置的邮件发信。*/
        $this->app->loadLang('custommail');
        $mailConf   = isset($this->config->global->setReviewmeetingMail) ? $this->config->global->setReviewmeetingMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf   = json_decode($mailConf);

        $mailTitle = vsprintf($mailConf->mailTitle, $mailConf->variables);

        /* Get mail content. */
        $oldcwd     = getcwd();
        $modulePath = $this->app->getModulePath($appName = '', 'reviewmeeting');
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
        /* 处理邮件标题。*/
        $subject = $mailTitle;

        /* Send emails. */
        if(empty($toList)) return false;
        $this->mail->send($toList, $subject, $mailContent,$ccList);

        if($this->mail->isError()) trigger_error(join("\n", $this->mail->getError()));
    }


    /**
     *获得会议评审收件人和抄送人信息
     *
     * @param $meetingInfo
     * @return string[]
     */
    public function getPendingToAndCcList($meetingInfo){
        $toList = '';
        $ccList = '';
        $data = [
            'toList' => $toList,
            'ccList' => $ccList,
        ];
        if(!$meetingInfo){
            return $data;
        }
        $toList = $meetingInfo->dealUser;
        $ccList = $this->loadModel('review')->getSendMailCcList($meetingInfo->id, 'reviewmeeting', $meetingInfo->status, 0);
        $status = $meetingInfo->status;
        if($status == $this->lang->reviewmeeting->statusList['waitMeetingOwnerReview']){ //等待审核主席审核
            $meetingCode = $meetingInfo->meetingCode;
            $reviewList = $this->loadModel('review')->getReviewListByMeetingCode($meetingCode, 'createdBy');
            if(!empty($reviewList)){
                $createdUsers = array_column($reviewList, 'createdBy');
                $createdUsers = array_flip(array_flip($createdUsers));
                $createdUsersStr = implode(',', $createdUsers);
                $ccList .= ','. $createdUsersStr;
            }
        }
        $data['toList'] = $toList;
        $data['ccList'] = $ccList;
        return $data;
    }

    /**
     * Get meeting by id.
     *
     * @param  int    $meetingID
     * @access public
     * @return void
     */
    public function getByID($meetingID){
        if(!$meetingID) {
            return new stdclass();
        }
        $meetingInfo = $this->dao->select('*')->from(TABLE_REVIEW_MEETING)->where('id')->eq($meetingID)->fetch();
        if (!$meetingInfo){
            return new stdclass();
        }
//        $status = $meetingInfo->status;
//        $allowReviewStatusList = $this->lang->reviewmeeting->allowReviewStatusList;
//        if(in_array($status, $allowReviewStatusList)){
//            $this->loadModel('review');
//            $version = 0;
//            $meetingInfo->reviewers = $this->review->getReviewer('reviewmeeting', $meetingInfo->id, $version);
//            $meetingInfo->dealUser = $meetingInfo->reviewers;
//        }
        $meetingInfo = $this->getConsumed($meetingInfo);
        return $meetingInfo;
    }


    /**
     * Get all issue for review.
     *
     * @param  int    $projectID
     * @access public
     * @return object
     */
    public function getReviewIssue($meetingCode)
    {
        $data =   $this->dao->select('t1.*,t2.title as reviewtitle')->from(TABLE_REVIEWISSUE)->alias('t1')
            ->leftJoin(TABLE_REVIEW)->alias('t2')
            ->on('t1.review = t2.id')
            ->Where('t1.deleted')->eq('0')
            ->andWhere('t2.meetingCode')->eq($meetingCode)
            ->andWhere('t1.meetingCode')->eq($meetingCode)
            ->andWhere('t1.createdBy')->eq($this->app->user->account)
            ->orderby('t1.createdDate_desc,t1.id_desc')
            ->fetchAll();
        return $data;
    }

    /**
     * Desc:获取会议编号列表
     * Date: 2022/7/26
     * Time: 16:11
     *
     * @return mixed
     *
     */
    public function getMeetingCode()
    {
        return $this->dao->select('id,meetingCode')->from(TABLE_REVIEW_MEETING)->where('deleted')->eq(0)->fetchAll();
    }

    /**
     * Desc: 获取评审标题下的会议编号数据
     * Date: 2022/8/22
     * Time: 16:49
     *
     * @param int $reviewID
     * @return mixed
     *
     */
    public function getMeetingCodeByReviewID($reviewID = 0)
    {
        return $this->dao->select('id,review_meeting_id,meetingCode')->from(TABLE_REVIEW_MEETING_DETAIL)->where('deleted')->eq(0)->andWhere('review_id')->eq($reviewID)->fetchAll();
    }
    /**
     * Update a issue.
     *
     * @access public
     * @return array|bool
     */
    public function editissue($issueID)
    {
        $oldIssue =$this->loadModel('reviewissue')->getByID($issueID);
        $data = fixer::input('post')
            ->remove('uid')
            ->stripTags($this->config->reviewmeeting->editor->edit['id'], $this->config->allowedTags)
            ->get();
        $data = $this->loadModel('file')->processImgURL($data, $this->config->reviewmeeting->editor->create['id'], $this->post->uid);
        $data->editBy = $this->app->user->account;
        $data->editDate = date('Y-m-d');

        $this->dao->update(TABLE_REVIEWISSUE)->data($data)->where('id')->eq($issueID)->batchCheck($this->config->reviewmeeting->editissue->requiredFields, 'notempty')->autoCheck()->exec();
        $this->loadModel('action')->create('reviewissue', $issueID, 'Edited');
        $this->loadModel('action')->create('reviewproblem', $issueID, 'Edited');
        if(!dao::isError())
        {
            $this->file->updateObjectID($this->post->uid, $issueID, 'reviewissue');
            return common::createChanges($oldIssue, $data);
        }
        return false;
    }

    /**
     * Get meet for calendar.
     *
     * @param  string $year
     * @access public
     * @return void
     */
    public function getMeetCalendar($year = '')
    {
        $this->app->loadLang('review');
        $account = $this->app->user->account;
        $allmeets = $this->dao->select("t1.id,t1.type,t1.owner,t1.meetingPlanTime,t1.meetingCode,t1.status,t1.reviewer,t2.review_meeting_id,t2.review_id,t3.createdDept,t3.title,t1.meetingPlanExport,t3.createdBy,concat_ws(',',t3.expert,t3.reviewedBy,t3.outside) as meetexport")->from(TABLE_REVIEW_MEETING)->alias('t1')
            ->leftJoin(TABLE_REVIEW_MEETING_DETAIL)->alias('t2')
            ->on('t1.id =  t2.review_meeting_id')
            ->leftJoin(TABLE_REVIEW)->alias('t3')
            ->on('t2.review_id =  t3.id')
            ->where('t2.deleted')->eq(0)
            ->andWhere('t3.deleted')->eq(0)
            ->andWhere('t1.meetingPlanTime')->like("$year%")
            ->beginIf($account != 'admin')->andWhere("(concat(',',t1.meetingPlanExport,',') like('%,$account,%') or t1.owner = '$account' or t1.reviewer = '$account' or t3.createdBy = '$account')")->fi()
            ->fetchAll();

        $users = $this->loadModel('user')->getPairs('noletter|noclosed');
        $outsideList1 = $this->loadModel('user')->getUsersNameByType('outsideExpertType');
        $outsideList2 = $this->loadModel('user')->getUsersNameByType('outside');
        $users = array_merge($users, $outsideList1, $outsideList2);
        $dept = $this->loadModel('dept')->getOptionMenu();

        $events = array();
        foreach ($allmeets as $id => $value) {
            $event = array();
            $event['id'] = $value->id;
            $event['type'] =$value->type;
            $event['typeName'] = zget($this->lang->review->typeList,$value->type,'')."_".zget($users,$value->owner,'');
            $event['owner'] = $value->owner;
            $event['meetingPlanTime'] = date('H:i:s',strtotime($value->meetingPlanTime));
            $event['status'] = $value->status;
            $event['start']  = $value->meetingPlanTime;
            $event['end']    = $value->meetingPlanTime;
            $meetexport = explode(',',$value->meetingPlanExport);
            $meetname = '';
            foreach ($meetexport as $it) {
                if(empty($it)) continue;
                $meetname .= zget($users,$it,'').',';

            }
            $event['meetingPlanExport']    = trim($meetname,',');

            $list = array();
            $list['review_id'] = $value->review_id;
            $list['Dept'] = $value->createdDept;
            $list['createdDept'] = zget($dept,$value->createdDept,'');
            $list['title'] = $value->title;
            $list['meetingPlanExport'] = $value->meetingPlanExport;
            $export = explode(',',$value->meetexport);
            $name = '';
            foreach ($export as $item) {
                if(empty($item)) continue;
                $name .= zget($users,$item,'').',';

            }
            $list['meetingPlanExportName'] = trim($name,',');
            $meetid = $value->review_meeting_id;

            if(isset($events[$meetid])){
                $events[$meetid]['list'][] =$list;
            }else{
                $events[$meetid] = $event;
                $events[$meetid]['list'][] = $list;
            }

        }
        return json_encode(array_values($events));
    }

    /**
     * 会议评审条数
     * @param string $status
     * @param $orderBy
     * @param null $pager
     */
    public function meetCount(){

        $user = $this->app->user->account;
        $year = date('Y');
        //所有、
        $allmeets =  $allmeets = $this->dao->select('t1.id')->from(TABLE_REVIEW_MEETING)->alias('t1')
            ->leftJoin(TABLE_REVIEW_MEETING_DETAIL)->alias('t2')
            ->on('t1.id =  t2.review_meeting_id')
            ->leftJoin(TABLE_REVIEW)->alias('t3')
            ->on('t2.review_id = t3.id')
            ->where('t1.deleted')->eq(0)
            ->andWhere('t2.deleted')->eq('0')
            ->andWhere('t3.deleted')->eq(0)
            ->andWhere("concat(',',t1.allOwner,',')")->like("%,$user,%")->groupBy('t1.meetingCode')
            ->fetchAll();
        //已排
        $suremeets = $this->dao->select('t1.id,t1.type,t1.owner,t1.meetingPlanTime,t1.meetingCode,t1.status,t1.reviewer,t3.createdDept,t3.title,t1.meetingPlanExport,t3.createdBy')->from(TABLE_REVIEW_MEETING)->alias('t1')
            ->leftJoin(TABLE_REVIEW_MEETING_DETAIL)->alias('t2')
            ->on('t1.id =  t2.review_meeting_id')
            ->leftJoin(TABLE_REVIEW)->alias('t3')
            ->on('t2.review_id = t3.id')
            ->where('t1.deleted')->eq(0)
            ->andWhere('t3.deleted')->eq(0)
            ->andWhere('t2.deleted')->eq('0')
            ->andWhere('t1.meetingPlanTime')->like("$year%")
            ->beginIf($user != 'admin')->andWhere("(concat(',',t1.meetingPlanExport,',') like('%,$user,%') or t1.owner = '$user' or t1.reviewer = '$user' or t3.createdBy = '$user')")->fi()
            ->groupBy('t1.id')
            ->fetchAll();
        //未排会议
        $waitmeets = $this->dao->select('id')->from(TABLE_REVIEW)
            ->where('deleted')->eq(0)
            ->andWhere('type')->ne('cbp')
            ->andWhere('status')->in('waitFormalReview,waitMeetingReview,waitFormalOwnerReview,formalReviewing,meetingReviewing')
            ->andWhere("concat(',',allOwner,',')")->like("%,$user,%")->andWhere('grade')->eq("meeting")->andWhere('meetingPlanTime')->eq("0000-00-00 00:00:00")
            ->fetchAll();
        return array('all'=>count($allmeets),'suremeet'=>count($suremeets),'wait'=>count($waitmeets));
    }

    /**
     * 会议评审列表
     * @param $status
     * @param $orderBy
     * @param $pager
     * @return mixed
     */
    public function meetList($status,$queryID = 0,$orderBy,$pager){
        $reviewmeetingQuery = '';
        if($status == 'bysearch')
        {
            $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : '';
            if($query)
            {
                $this->session->set('reviewmeetingQuery', $query->sql);
                $this->session->set('reviewMeetingForm', $query->form);
            }
            if($this->session->reviewmeetingQuery == false) $this->session->set('reviewmeetingQuery', ' 1 = 1');
            $reviewmeetingQuery = $this->session->reviewmeetingQuery;
        }
        //字段表明确
        $reviewmeetingQuery = $this->dealSqlAmbiguous($reviewmeetingQuery,'t1','meetingCode');
        $reviewmeetingQuery = $this->dealSqlAmbiguous($reviewmeetingQuery,'t1','owner');
        $reviewmeetingQuery = $this->dealSqlAmbiguous($reviewmeetingQuery,'t1','reviewer');
        $reviewmeetingQuery = $this->dealSqlAmbiguous($reviewmeetingQuery,'t1','dealUser');
        $reviewmeetingQuery = $this->dealSqlAmbiguous($reviewmeetingQuery,'t1','status');
        if(strpos($reviewmeetingQuery,'status') && strpos($reviewmeetingQuery,'waitFormalReview') ){
            $reviewmeetingQuery = "(( 1   AND t1.`status`  in ('formalReviewing','waitFormalReview')  ) )";
        }elseif (strpos($reviewmeetingQuery,'status') && strpos($reviewmeetingQuery,'waitMeetingReview') ){
            $reviewmeetingQuery = "(( 1   AND t1.`status`  in ('waitMeetingReview','meetingReviewing')  ) )";
        }
        $reviewmeetingQuery = $this->dealSqlAmbiguous($reviewmeetingQuery,'t1','meetingPlanExport');
        $reviewmeetingQuery = $this->dealSqlAmbiguous($reviewmeetingQuery,'t1','meetingPlanTime');
        $reviewmeetingQuery = $this->dealSqlAmbiguous($reviewmeetingQuery,'t1','meetingRealTime');
        if(strpos($reviewmeetingQuery,'reviewIDList')  ){
            $reviewmeetingQuery = str_replace("`reviewIDList`", "t3.`id`", $reviewmeetingQuery);
        }
        $reviewmeetingQuery = $this->dealSqlAmbiguous($reviewmeetingQuery,'t1','createUser');
        $reviewmeetingQuery = $this->dealSqlAmbiguous($reviewmeetingQuery,'t1','createTime');
        $reviewmeetingQuery = $this->dealSqlAmbiguous($reviewmeetingQuery,'t1','editBy');
        $reviewmeetingQuery = $this->dealSqlAmbiguous($reviewmeetingQuery,'t1','editTime');
        $this->loadModel('review');

        $user = $this->app->user->account;
        $allmeets = $this->dao->select('t1.*,group_concat(distinct t3.title) title,group_concat(distinct t3.object) object,t1.meetingPlanExport,group_concat(distinct t3.relatedUsers)relatedUsers,group_concat(distinct t3.createdBy)createdBy,group_concat(distinct t3.createdDept) createdDept')->from(TABLE_REVIEW_MEETING)->alias('t1')
            ->leftJoin(TABLE_REVIEW_MEETING_DETAIL)->alias('t2')
            ->on('t1.id =  t2.review_meeting_id')
            ->leftJoin(TABLE_REVIEW)->alias('t3')
            ->on('t2.review_id = t3.id')
            ->where('t1.deleted')->eq('0')
            ->andWhere('t2.deleted')->eq('0')
            ->andWhere('t3.deleted')->eq('0')
            ->beginIF($status == 'bysearch')->andWhere("concat(',',t1.allOwner,',')")->like("%,$user,%")->andWhere($reviewmeetingQuery)->groupBy('t1.meetingCode')->fi()
            ->beginIF($status == 'all')->andWhere("concat(',',t1.allOwner,',')")->like("%,$user,%")->groupBy('t1.meetingCode')->fi()
            ->beginIF($status == 'wait')->andWhere("concat(',',t1.dealUser,',')")->like("%,$user,%")->groupBy('t1.meetingCode')->fi()
            ->beginIF($status == 'waitformalreview')->andWhere('t1.status')->in('formalReviewing,waitFormalReview')->andWhere("concat(',',t1.allOwner,',')")->like("%,$user,%")->groupBy('t1.meetingCode')->fi()
            ->beginIF($status == 'waitmeetingreview')->andWhere('t1.status')->in('waitMeetingReview,meetingReviewing')->andWhere("concat(',',t1.allOwner,',')")->like("%,$user,%")->groupBy('t1.meetingCode')->fi()
            ->beginIF($status == 'waitmeetingownerreview')->andWhere('t1.status')->eq('waitMeetingOwnerReview')->andWhere("concat(',',t1.allOwner,',')")->like("%,$user,%")->groupBy('t1.meetingCode')->fi()
            ->beginIF($status == 'pass')->andWhere('t1.status')->eq('pass')->andWhere("concat(',',t1.allOwner,',')")->like("%,$user,%")->groupBy('t1.meetingCode')->fi()
            ->orderBy($orderBy)
            ->page($pager,'t1.id')
            ->fetchAll();
        return $allmeets;
    }

    /**
     * 未排会议列表
     *
     * @param $status
     * @param int $queryID
     * @param $orderBy
     * @param $pager
     * @return mixed
     */
    public function noMeetList($status,$queryID = 0,$orderBy,$pager){
        $this->loadModel('review');
        $user = $this->app->user->account;
        $reviewnomeetQuery = '';
        //允许排会的状态
        $allowBindMeetingLastStatusList = $this->lang->review->allowBindMeetingLastStatusList;
        $allowBindMeetingLastStatusStr =  "'" . implode("','", $allowBindMeetingLastStatusList) . "'";

        //允许恢复到会议状态的挂起前状态列表
        $allowRenewMeetingLastStatusList = $this->lang->review->allowRenewMeetingLastStatusList;
        $allowRenewMeetingLastStatusStr = "'" . implode("','", $allowRenewMeetingLastStatusList) . "'";
        $allStatusSql = " ( status IN (". $allowBindMeetingLastStatusStr.") OR (status = 'suspend' AND `lastStatus`  in (".$allowRenewMeetingLastStatusStr.")) ) ";
        if($status == 'bysearch')
        {
            $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : '';
            if($query)
            {
                $this->session->set('reviewnomeetQuery', $query->sql);
                $this->session->set('reviewnomeetForm', $query->form);
            }
            if($this->session->reviewnomeetQuery == false) $this->session->set('reviewnomeetQuery', ' 1 = 1');
            $reviewnomeetQuery = $this->session->reviewnomeetQuery;
        }
        if(strpos($reviewnomeetQuery,'object') && strpos($reviewnomeetQuery,'=')){
            $reviewnomeetQuery = str_replace("`object` = '","`object` = ',",$reviewnomeetQuery);
        }
        if(strpos($reviewnomeetQuery,'status') && strpos($reviewnomeetQuery,'waitFormalReview') ){
            $reviewnomeetQuery = "(( 1   AND `status`  in ('formalReviewing','waitFormalReview')  ) )";
        }elseif(strpos($reviewnomeetQuery,'status') && strpos($reviewnomeetQuery,'waitMeetingReview')){
            $reviewnomeetQuery = "(( 1   AND `status`  in ('waitMeetingReview','meetingReviewing')  ) )";
        }elseif(strpos($reviewnomeetQuery,'status') && strpos($reviewnomeetQuery,'waitExportReview')) {
            $reviewnomeetQuery = "(( 1   AND `status`  in ('waitFormalAssignReviewer')  ) )";
        }elseif(strpos($reviewnomeetQuery,'status') && strpos($reviewnomeetQuery,'suspend')) {
            $reviewnomeetQuery = "(( 1   AND `status` = 'suspend' AND `lastStatus`  IN (" . $allowBindMeetingLastStatusStr . ") ) )";
        }
        //未排会议
        $allmeets = $this->dao->select('*')->from(TABLE_REVIEW)
            ->where('deleted')->eq('0')
            ->andWhere('type')->ne('cbp')
            ->beginIF($status == 'bysearch')->andWhere($reviewnomeetQuery)->andWhere($allStatusSql)->fi()
            ->beginIF($status == 'all')->andWhere($allStatusSql)->fi()
            ->beginIF($status == 'waitformalreview')->andWhere('status')->in('formalReviewing,waitFormalReview')->fi()
            ->beginIF($status == 'waitexportreview')->andWhere('status')->in('waitFormalAssignReviewer')->fi()
            ->beginIF($status == 'waitformalownerreview')->andWhere('status')->eq('waitFormalOwnerReview')->fi()
            ->beginIF($status == 'waitmeetingreview')->andWhere('status')->in('waitMeetingReview,meetingReviewing')->fi()
            ->beginIF($status == 'suspend')->andWhere('status')->eq($status)->andWhere('lastStatus')->IN($allowRenewMeetingLastStatusList)->fi()
            ->andWhere('grade')->eq("meeting")
            ->andWhere('meetingPlanTime')->eq("0000-00-00 00:00:00")
            ->andWhere("concat(',',allOwner,',')")->like("%,$user,%")
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll();
        foreach($allmeets as $key => $reviewInfo) {
            $status = $reviewInfo->status;
            $statusDesc = $this->review->getReviewStatusDesc($status, $reviewInfo->rejectStage);
            $allmeets[$key]->statusDesc = $statusDesc;
            $allmeets[$key]->reviewers  = $reviewInfo->dealUser;
        }
        return $allmeets;
    }
    /**
     * 待处理会议评审列表
     * @param  object $col
     * @param  object $review
     * @param  array  $users
     * @param  array  $products
     * @access public
     * @return void
     */

    public function printMeetCell($col, $review, $users, $products)
    {
        $this->app->loadLang('review');
        $reviewID = 0;
        if(isset($review->id)){
            $reviewID = $review->id;
        }

        $canView = common::hasPriv('review', 'view');
        $canBatchAction = false;

        $deptMap = $this->loadModel('dept')->getOptionMenu();
        $reviewList = inlink('view', "reviewID=$reviewID");
        $account    = $this->app->user->account;
        $id = $col->id;

        if($col->show)
        {
            $class = "c-$id";
            $title = '';
            if($id == 'id') $class .= ' cell-id';
            if($id == 'status')
            {
                $class .= ' status-' . $review->status;
                $name = zget($this->lang->reviewmeeting->statusLabelList, $review->status,'');
                $title  = "title='{$name}'";
            }

            if($id == 'title')
            {
                $class .= ' text-left';
                $title  = "title='{$review->title}'";
            }

            echo "<td class='" . $class . "' $title>";

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
                    $txt='';
                    $title = array_unique(explode(',', $review->title));
                    foreach($title as $obj)
                    {
                        $obj = trim($obj);
                        if(empty($obj)) continue;
                        $txt .= $obj . "&nbsp&nbsp;";
                    }
                    echo '<div class="ellipsis" title="' . $txt . '">' . $txt .'</div>';
                    break;

                case 'object':
                    $txt='';
                    $object = array_unique(explode(',', $review->object));
                    foreach($object as $obj)
                    {
                        $obj = trim($obj);
                        if(empty($obj)) continue;
                        $txt .= zget($this->lang->review->objectList, $obj) . "&nbsp&nbsp;";
                    }
                    echo '<div class="ellipsis" title="' .$txt . '">' .$txt .'</div>';
                    break;

                case 'status':
                    echo zget($this->lang->reviewmeeting->statusLabelList ,$review->status,'');
                    break;
                case 'meetingCode':
                    if($review->isCode){
                        echo isset($review->meetingCode) ? html::a(helper::createLink('reviewmeeting', 'meetingview', "reviewID=$review->id&flag=1"), $review->meetingCode) : '';
                    }else{
                        echo isset($review->meetingCode) ? $review->meetingCode : '';
                    }
                    break;
                case 'meetingPlanExport':
                    $txt = '';
                    $meetingPlanExport = explode(',', $review->meetingPlanExport);
                    foreach($meetingPlanExport as $account) {
                        $account = trim($account);
                        if(empty($account)) continue;
                        $txt .= zget($users, $account) .'&nbsp&nbsp;';
                    }
                    echo '<div class="ellipsis" title="' .$txt. '">' . $txt .'</div>';
                    break;
                case 'meetingPlanTime':
                    $meetingPlanTime = '';
                    if($review->meetingPlanTime != '0000-00-00 00:00:00'){
                        $meetingPlanTime = $review->meetingPlanTime;
                    }
                    // echo $meetingPlanTime;
                    echo '<div class="" title="' . $meetingPlanTime . '">' . $meetingPlanTime .'</div>';
                    break;

                case 'meetingRealTime':
                    $meetingRealTime = '';
                    if($review->meetingRealTime != '0000-00-00 00:00:00'){
                        $meetingRealTime = $review->meetingRealTime;
                    }
                    echo '<div class="" title="' . $meetingRealTime . '">' . $meetingRealTime .'</div>';
                    break;
                case 'relatedUsers':
                    $txt='';
                    $relatedUsers = array_unique(explode(',', $review->relatedUsers));

                    foreach($relatedUsers as $account)
                    {
                        $account = trim($account);
                        if(empty($account)) continue;
                        $txt .= zget($users, $account) . "&nbsp&nbsp;";
                    }
                    echo '<div class="ellipsis" title="' .$txt . '">' .  $txt  .'</div>';
                    break;
                case 'createdBy':
                    $txt='';
                    $createdBy = array_unique(explode(',', $review->createdBy));
                    foreach($createdBy as $account)
                    {
                        $account = trim($account);
                        if(empty($account)) continue;
                        $txt .= zget($users, $account) . "&nbsp&nbsp;";
                    }
                    echo '<div class="ellipsis" title="' . $txt. '">' .$txt .'</div>';
                    break;
                case 'owner':
                    echo zget($users, $review->owner,'');
                    break;
                case 'reviewer':
                    echo zget($users, $review->reviewer,'');
                    break;
                case 'createdDate':
                    echo $review->createdDate;
                    break;

                case 'dealUser':
                    $dealUser = explode(',', str_replace(' ', '', $review->dealUser));
                    $txt = '';
                    foreach($dealUser as $account)
                        $txt .= zget($users, $account,'') . " &nbsp;";
                    echo '<div class="ellipsis" title="' . $txt . '">' . $txt .'</div>';
                    break;
                case 'createdDept':
                    $txt='';
                    $createdDept = explode(',',$review->createdDept);
                    foreach($createdDept as $dept)
                    {
                        $dept = trim($dept);
                        if(empty($dept)) continue;
                        $txt .= zget($deptMap,$dept,''). "&nbsp&nbsp;";
                    }
                    echo '<div class="ellipsis" title="' .$txt . '">' .$txt .'</div>';
                    //  echo zget($deptMap, $review->createdDept,'');
                    break;
                case 'actions':
                    if($review->isCode){
                        $params  = "reviewID=$review->id";
                        $currentUser = $this->app->user->account;
                        $owner =  $review->owner; //评审主席
                        $reviewer = $review->reviewer; //评审专员
                        $tempUsers = [$owner, $reviewer];
                        common::hasPriv('reviewmeeting', 'edit') ? common::printIcon('reviewmeeting', 'edit', $params, $review, 'list', '', '', 'iframe', true, 'data-width="1200px" data-toggle="modal"', $this->lang->reviewmeeting->edit) : '';
                        common::hasPriv('reviewmeeting', 'review') ? common::printIcon('reviewmeeting', 'review', $params, $review, 'list', 'glasses', '', 'iframe', true, 'data-width="1200px" data-toggle="modal"', $this->lang->reviewmeeting->reviewTipMsg) : '';
                        common::hasPriv('reviewmeeting', 'confirmmeeting') ? common::printIcon('reviewmeeting', 'confirmmeeting', $params, $review, 'list','menu-users', '', 'iframe', true, 'data-width="750" data-toggle="modal"') : '';
                        common::hasPriv('reviewmeeting', 'notice') ? common::printIcon('reviewmeeting', 'notice', $params, $review, 'list', 'envelope-o', '', 'iframe', true, 'data-width="900" data-height="600" data-toggle="modal"',$this->lang->reviewmeeting->notice.$this->lang->reviewmeeting->common) : '';
                        common::hasPriv('reviewmeeting', 'downloadfiles') ? common::printIcon('reviewmeeting', 'downloadfiles', $params, $review, 'list', 'download', '', '','','data-width="1200px"') : '';
                        if($review->type == 'dept'){
                            $class =  (($review->status == 'waitMeetingOwnerReview') &&  (common::hasPriv('reviewmeeting', 'change') || in_array($currentUser, $tempUsers))) ? 'btn iframe' : 'btn iframe disabled';
                            echo html::a(helper::createLink('reviewmeeting', 'ajaxChange', $params, '', true), '<i class="icon-edit"></i>', '', "data-width='1200px' data-toggle='modal' title='{$this->lang->reviewmeeting->change}' class='{$class}'");
                        }else{
                            common::hasPriv('reviewmeeting', 'change') ? common::printIcon('reviewmeeting', 'change', $params, $review, 'list', 'time', '', 'iframe', true, 'data-width="1200px" data-toggle="modal"', $this->lang->reviewmeeting->change) : '';
                        }
                    }
            }
            echo '</td>';
        }
    }

    /**
     * Print datatable cell.
     * @param  object $col
     * @param  object $review
     * @param  array  $users
     * @param  array  $products
     * @param $tag
     * @access public
     * @return void
     */

    public function printCell($col, $review, $users, $products,$tag = 0)
    {
        $this->app->loadLang('review');
        $reviewID = $review->id;
        $canView = common::hasPriv('review', 'view');
        $canBatchAction = false;

        $deptMap = $this->loadModel('dept')->getOptionMenu();
        //$reviewList = inlink('view', "reviewID=$review->id");
        $account    = $this->app->user->account;
        $id = $col->id;
        $outsideList1 =array(''=>'') +$this->loadModel('user')->getUsersNameByType('outsideExpertType');
        $outsideList2 =array(''=>'') +$this->loadModel('user')->getUsersNameByType('outside');
        $this->app->loadLang('projectplan');
        //$relatedUsers  = $this->loadModel('user')->getPairs('noletter');
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
                    if($tag){
                        echo   "<div class='checkbox-primary'><input type='checkbox' name='idList[]' value='$review->id' id='idList.$review->id'> <label for='idList.$review->id'></label></div>";
                    }
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
                    echo html::a(helper::createLink('reviewmeeting', 'reviewview', "reviewID=$review->id&flag=1"), $review->title);
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

                case 'grade':
                    echo  zget($this->lang->review->gradeList, $review->grade,'');
                    break;

                case 'meetingPlanTime':
                    $meetingPlanTime = '';
                    if($review->meetingPlanTime != '0000-00-00 00:00:00'){
                        $meetingPlanTime = $review->meetingPlanTime;
                    }
                    echo '<div class="" title="' . $meetingPlanTime . '">' . $meetingPlanTime .'</div>';
                    break;
                case 'meetCode':
                    echo $review->meetingCode;
                    break;

                case 'meetingRealTime':
                    $meetingRealTime = '';
                    if($review->meetingRealTime != '0000-00-00 00:00:00'){
                        $meetingRealTime = $review->meetingRealTime;
                    }
                    echo '<div class="" title="' . $meetingRealTime . '">' . $meetingRealTime .'</div>';
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
                case 'editBy':
                    echo zget($users, $review->editBy,'');
                    break;
                case 'editDate':
                    echo '<div class="ellipsis" title="' . $review->editDate . '">' . $review->editDate .'</div>';
                    break;

                case 'createdDept':
                    $txt='';
                    $createdDept = explode(',',$review->createdDept);
                    foreach($createdDept as $dept)
                    {
                        $dept = trim($dept);
                        if(empty($dept)) continue;
                        $txt .= zget($deptMap,$dept,''). " &nbsp;";
                    }
                    echo '<div class="ellipsis" title="' . $txt . '">' . $txt .'</div>';
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
                case 'actions':
                    $params  = "reviewID=$review->id";
                    if(!$tag) {
                        common::hasPriv('reviewmeeting', 'edit') ? common::printIcon('reviewmeeting', 'edit', $params, $review, 'list', '', '', 'iframe', true, 'data-width="1200px"', $this->lang->reviewmeeting->edit) : '';
                        common::hasPriv('reviewmeeting', 'review') ? common::printIcon('reviewmeeting', 'review', $params, $review, 'list', 'play', '', 'iframe', true, '', $this->lang->review->submit) : '';
                        common::hasPriv('reviewmeeting', 'confirmmeeting') ? common::printIcon('reviewmeeting', 'confirmmeeting', $params, $review, 'list', 'back', 'hiddenwin', '', '', "$click", $this->lang->review->recall) : '';
                        common::hasPriv('reviewmeeting', 'notice') ? common::printIcon('reviewmeeting', 'notice', $params, $review, 'list', 'hand-right', '', 'iframe', true, '', $this->lang->review->assign) : '';
                        common::hasPriv('reviewmeeting', 'downloadfiles') ? common::printIcon('reviewmeeting', 'downloadfiles', $params, $review, 'list', 'glasses', '', '','', 'data-width="1200px"') : '';
                    }else{
                        if($review->status == 'suspend'){ //恢复
                            common::hasPriv('review', 'renew') ? common::printIcon('review', 'renew', $params, $review, 'list', 'magic', '', 'iframe', true, 'data-toggle="modal"', $this->lang->review->renew) : '';
                        }else{
                            common::hasPriv('reviewmeeting', 'setmeeting') ? common::printIcon('reviewmeeting', 'setmeeting', $params, $review, 'list','calendar', '', 'iframe', true, 'data-toggle="modal"', $this->lang->reviewmeeting->setmeeting) : '';
                        }
                    }

            }
            echo '</td>';
        }
    }

    /**
     * Desc: 批量新建问题数据
     * User: t_liugaoyang
     * Date: 2022/7/27
     * Time: 17:46
     *
     * @param $projectID
     *
     */
    public function batchCreate($meetingCode)
    {
        $data = fixer::input('post')->get();
        $this->app->loadClass('purifier', true);
        $addDataTips= array();
        $addData= array();
        $line = [];

        //第1行必须创建
        if(empty($data->title[0])){
            die(js::alert($this->lang->reviewmeeting->emptyData,true));
        }else{
            //只填写文件名/位置，不填写判断
            foreach($data->title as $key => $value)
            {
                if(!empty($value)){
                    $titleData = $this->reviewData($data,$key,$meetingCode);
                    $addDataTips[$titleData['line']] = $titleData['reviewData'];
                }
                //构造数据
                $titleData = $this->reviewData($data,$key,$meetingCode);
                $addData[] = $titleData['reviewData'];
            }
            //只填写描述，不填写文件名/位置判断
            foreach ($data->desc as $k=>$v)
            {
                if(!empty($v)){
                    $descData = $this->reviewData($data,$k,$meetingCode);
                    $addDataTips[$descData['line']] = $descData['reviewData'];
                }
            }
        }
        //去除中间未填写项数据，只保存有效数据
        foreach ($addData as $i=>$item){
            if(empty($item->title)){
                unset($addData[$i]);
            }
        }

        ksort($addDataTips);
        foreach ($addData as $addDatum){
            if(empty($addDatum->desc)){
                die(js::alert($this->lang->reviewmeeting->issueEmpty,true));
            }
        }
        if(!empty($addDataTips)) {
            foreach ($addDataTips as $item => $dataValue) {
                $requiredFields = explode(',', $this->config->reviewmeeting->batchCreate->requiredFields);
                foreach ($requiredFields as $requiredField) {
                    $requiredField = trim($requiredField);
                    if (empty($dataValue->$requiredField)) {
                        dao::$errors[] = sprintf($this->lang->reviewmeeting->noRequire, $item, $this->lang->reviewmeeting->$requiredField);
                    }
                }
            }
        }
        if(dao::isError()) die(js::error(dao::getError()));
        foreach ($addData as $insertData){
            $insertData->meetingCode =$meetingCode;
            $this->dao->insert(TABLE_REVIEWISSUE)->data($insertData)->exec();
            if(!dao::isError())
            {
                $reviewIssueId = $this->dao->lastInsertID();
                $this->loadModel('action')->create('reviewissue', $reviewIssueId, 'Created');
                $this->loadModel('action')->create('reviewproblem', $reviewIssueId, 'Created');
            }
            if(dao::isError()) die(js::error(dao::getError()));
        }
    }

    /**
     * Desc:批量添加构造数据
     * Date: 2022/7/27
     * Time: 17:41
     *
     * @param $data
     * @param $i
     * @param $projectID
     * @return array
     *
     */
    public function reviewData($data,$i,$meetingCode)
    {
        $reviewData = new stdClass();
        $line = [];
        ;//id@projectid
        $review = substr($data->review[$i],0,strripos($data->review[$i],"@"));
        $project = substr($data->review[$i],strripos($data->review[$i],"@")+1);
        if($review == 'ditto'){
            $review = $data->review[$i] = $data->review[$i-1];
        }
        $reviewInfo = $this->loadModel('review')->getById($review);
        $reviewData->review = $review;
        $type = $data->type[$i];
        if($type == 'ditto'){
            $type = $data->type[$i] = $data->type[$i-1];
        }
        $reviewData->type          = $type;
        $reviewData->status        = 'create';
        $reviewData->title         = $data->title[$i];
        $reviewData->meetingCode   = $meetingCode;
        $reviewData->desc          = $data->desc[$i];
        $reviewData->createdBy     = $this->app->user->account;
        $reviewData->createdDate   = date('Y-m-d');
        $reviewData->raiseBy       = $data->raiseBy[$i];
        $reviewData->raiseDate     = date('Y-m-d');
        $reviewData->project       = $project;
        $reviewData->dealUser      = $reviewInfo->createdBy ?? '';


        if(isset($this->config->reviewissue->beatchCreate->requiredFields))
        {
            $requiredFields = explode(',', $this->config->reviewissue->beatchCreate->requiredFields);
            foreach($requiredFields as $requiredField)
            {
                $requiredField = trim($requiredField);
                if(empty($reviewData->$requiredField)){
                    $line = $i+1;
                }
            }
        }
        $returnData = [
            'line'=>$line,
            'reviewData'=>$reviewData
        ];
        return $returnData;
    }

    /* 获取工时投入信息*/
    public function getConsumed($meetingInfo)
    {
        if(empty($meetingInfo)) return new stdClass();

        $cs = $this->dao->select('*')->from(TABLE_CONSUMED)->where('objectType')->eq('reviewmeeting')
            ->andWhere('objectID')->eq($meetingInfo->id)
            ->fetchAll();
        $meetingInfo->consumed = $cs;
        return $meetingInfo;
    }

    //批量获取评审
    public function getByIds($ids){
        if(!$ids) return new stdclass();
        $review = $this->dao->select('*')->from(TABLE_REVIEW)->where('id')->in($ids)->fetchall();
        $objects = $this->dao->select('*')->from(TABLE_REVIEWOBJECT)->where('review')->in($ids)->fetchAll();
        foreach ($review as $k=>$v) {
            foreach ($objects as $k2=>$v2) {
                if ($v2->review == $v->id){
                    $review[$k]->objects[] = $v2;
                }
            }
        }
        return $review;
    }

    /**
     *项目排期
     *
     * @return array
     */
    public function setmeetingNew(){
        $res = array(
            'result'  => false,
            'message' => '',
        );
        $data  = fixer::input('post')
            ->join('expert',',')
            ->remove('ids,meetingPlanType')
            ->get();
        if ($data->owner == ''){
            $res = ['result'=>false,'message'=>'评审主席不能为空'];
            return $res;
        }
        if ($data->reviewer == ''){
            $res = ['result'=>false,'message'=>'评审专员不能为空'];
            return $res;
        }
        if (!isset($data->expert)){
            $res = ['result'=>false,'message'=>'预计参会专家不能为空'];
            return $res;
        }
        $meetingPlanType = $_POST['meetingPlanType'];
        if ($meetingPlanType == 1 && !$data->meetingCode){
            $res = ['result'=>false,'message'=>'请选择会议日程'];
            return $res;
        }

        $meetingPlanTime = $data->feedbackExpireTime;
        if ($meetingPlanType == 2 && !$meetingPlanTime){
            $res['result'] = false;
            $res['message'] = "预计会议时间不能为空";
            return $res;
        }

        $ids = rtrim($_POST['ids'],',');

        $reviewIDS = explode(',',$ids);
        $idsCount = count($reviewIDS);


        $meetStatus = 'waitMeetingReview'; //初始状态是待会议评审
        $reviewList = $this->getByIds($ids);

        $reviewInfo = $reviewList[0];
        $reviewList = array_column($reviewList, null, 'id');

        //检查当前的项目评审是否还允许评审
        foreach ($reviewList as $k => $v) {
            if (!in_array($v->status,$this->lang->reviewmeeting->allowBindStatusArrayNew)){
                $res['result'] = false;
                $res['message'] = "当前项目状态不允许绑定会议";
                return $res;
            }
            $projectStatus[] = $v->status;
            if (in_array($v->status,['waitFormalReview','waitFormalOwnerReview','formalReviewing','waitFormalAssignReviewer'])){
                $meetStatus = "waitFormalReview";
            }
        }

        if ($meetingPlanType == 2){
            if(!($reviewInfo && $meetingPlanTime)){
                $res['message'] = $this->lang->reviewmeeting->checkBind['paramsError'];
                return $res;
            }
            $type = $reviewInfo->type;
            $params = [
                'type'              => $type,
                'meetingPlanTime' => $meetingPlanTime,
                'owner'             => $data->owner,
            ];
            $meetingInfo = $this->getInfo($params, 'id');
            if($meetingInfo && isset($meetingInfo->id)){
                $res['message'] = $this->lang->reviewmeeting->checkCreate['meetingExist'];
                return $res;
            }
        }
        //选择已有会议, 判断会议是否允许绑定
        if ($meetingPlanType == 1){
            $meetingInfo = $this->dao->select('*')->from(TABLE_REVIEW_MEETING)->where('meetingCode')->eq($_POST['meetingCode'])->fetch();
            $res = $this->checkMeetingIsAllowBind($meetingInfo);
            if (!$res['result']){
                return $res;
            }
            $review_data = new stdClass();
            $review_data->meetingPlanExport = $data->expert; //是否确定覆盖原来会议的预审专家?
            $review_data->owner = $data->owner; //评审主席替换原来的评审主席
            $review_data->reviewer = $data->reviewer; //评审专员

            //修改已经存在的会议
            $this->dao->update(TABLE_REVIEW_MEETING)->data($review_data)->where('meetingCode')->eq($_POST['meetingCode'])->exec();

            //查询原来包含的评审信息
            $meetingCode = $_POST['meetingCode'];
            $bindedReviewList = $this->loadModel('review')->getReviewListByMeetingCode($meetingCode, 'id');
            if($bindedReviewList){
                //修改原来绑定的评审主席
                $bindedReviewIds = array_column($bindedReviewList, 'id');
                $reviewParam = new stdClass();
                $reviewParam->owner = $data->owner; //评审主席替换原来的评审主席
                $this->dao->update(TABLE_REVIEW)->data($reviewParam)->where('id')->in($bindedReviewIds)->exec();
            }

            foreach ($reviewList as $item) {
                $res = $this->bindToReviewMeeting($_POST['meetingCode'], $item, $meetStatus, $meetingInfo);
                if(!$res['result']){
                    dao::$errors[] = $res['message'];
                    return false;
                }
            }
            //更新评审专员
            if ($meetStatus == 'waitFormalReview'){
                /*  $list = $this->dao->select("review_id")->from(TABLE_REVIEW_MEETING_DETAIL)->where("review_meeting_id")->eq($meetingInfo->id)->fetchAll();
                  $reviewArray = [];
                  foreach ($list as $lv) {
                      $reviewArray[] = $lv->review_id;
                  }
                  $reviewAll = new stdClass();
                  $reviewAll->dealuser = $data->reviewer;
                  $this->dao->update(TABLE_REVIEW)->data($reviewAll)->where('id')->in($reviewArray)->andWhere('status')->eq('waitMeetingReview')->exec();*/
            }

            $review = new stdClass();
            $review->meetingPlanExport = $data->expert;
            //修改参会专家
            $this->dao->update(TABLE_REVIEW)->data($review)->where('id')->in($ids)->exec();

            //获得绑定以后的项目评审
            $newReviewList = $this->getByIds($ids);
            $newReviewList = array_column($newReviewList, null, 'id');

            for ($i = 0; $i < $idsCount; $i++) {
                $reviewId = $reviewIDS[$i];//更新操作记录
                $review_old = zget($reviewList, $reviewId);
                $newReviewInfo = zget($newReviewList, $reviewId);
                $changes = common::createChanges($review_old, $newReviewInfo);
                if ($changes) {
                    $extra = '绑定会议，会议单号：'.$_POST['meetingCode'];
                    $actionID = $this->loadModel('action')->create('review', $reviewId, 'schedule', $this->post->comment, $extra);
                    $res = $this->action->logHistory($actionID, $changes);
                }
            }

            //会议评审日志
            $changes = common::createChanges($meetingInfo, $review_data);
            $actionID = $this->loadModel('action')->create('reviewmeeting', $meetingInfo->id, 'schedule', $this->post->comment);
            $this->action->logHistory($actionID, $changes);
            $res['result'] = true;
            return $res;
        }



        //添加新的会议
        $currentUser = $this->app->user->account;
        $currentTime = helper::now();
        $meetingCodeSort = $this->setMeetingCodeSort($reviewInfo->type);
        $meetingCode     = $this->setMeetingCode($reviewInfo->type, $meetingCodeSort);

        //新增会议数据
        $params = new stdClass();
        $params->meetingCode     = $meetingCode;
        $params->sortId          = $meetingCodeSort;
        $params->createUser      = $currentUser;
        $params->createTime      = $currentTime;
        $params->type            = $reviewInfo->type;
        $params->meetingPlanTime = $meetingPlanTime;
        $params->meetingPlanExport = $data->expert;
        $params->owner           = $data->owner;
        $params->status          = $meetStatus;
        $params->reviewer        = $data->reviewer;
        $params->dealUser        = $data->reviewer;
        if ($meetStatus == 'waitFormalReview'){
            $params->dealUser = "";
        }
        //选择新增会议
        $this->dao->insert(TABLE_REVIEW_MEETING)->data($params)
            ->autoCheck()
            ->batchCheck($this->config->reviewmeeting->create->requiredFields, 'notempty')
            ->exec();
        $reviewMeetingId =  $this->dao->lastInsertID();
        $this->loadModel('action')->create('reviewmeeting', $reviewMeetingId, 'created', $this->post->comment);

        if(dao::isError()){
            $res['message'] = dao::getError();
            return $res;
        }

        //更新评审表
        $review_data = new stdClass();
        //$review_data->review_meeting_id = $reviewMeetingId;
        $review_data->meetingCode       = $meetingCode;
        $review_data->meetingPlanTime   = $meetingPlanTime;
        $review_data->meetingPlanExport = $data->expert;
        $review_data->owner             = $data->owner;;
        $review_data->reviewer          = $data->reviewer;

        if ($meetStatus == 'waitMeetingReview'){
            $review_data->dealUser = $data->reviewer;
        }
        $this->dao->update(TABLE_REVIEW)->data($review_data)->where('id')->in($ids)->exec();


        //获得评审详情信息
        for ($i = 0; $i < $idsCount; $i++){
            $reviewId = $reviewIDS[$i];
            $meetingDetailInfo = $this->getMeetingDetailInfoByReviewId($reviewId);
            if($meetingDetailInfo && isset($meetingDetailInfo->id)){
                if($meetingDetailInfo->meetingCode != $meetingCode){
                    $updateParams = new stdClass();
                    $updateParams->review_meeting_id = $reviewMeetingId;
                    $updateParams->meetingCode       = $meetingCode;
                    //更新详情表
                    $this->dao->update(TABLE_REVIEW_MEETING_DETAIL)->data($updateParams)
                        ->autoCheck()
                        ->where('id')->eq($meetingDetailInfo->id)->exec();
                    if(dao::isError()){
                        $res['message'] = $this->lang->reviewmeeting->checkCreate['createError'];
                        return $res;
                    }
                }
            }else{
                //插入记录
                $params = new stdClass();
                $params->review_meeting_id = $reviewMeetingId;
                $params->meetingCode       = $meetingCode;
                $params->status            = $meetStatus;
                $params->createUser        = $currentUser;
                $params->createTime        = $currentTime;
                $params->review_id         = $reviewId;
                $this->dao->insert(TABLE_REVIEW_MEETING_DETAIL)->data($params)
                    ->autoCheck()
                    ->exec();
                if(dao::isError()){
                    $res['message'] = $this->lang->reviewmeeting->checkCreate['createError'];
                    return $res;
                }
            }
        }


        //获得绑定以后的项目评审
        $newReviewList = $this->getByIds($ids);
        $newReviewList = array_column($newReviewList, null, 'id');

        for ($i = 0; $i < $idsCount; $i++) {
            $reviewId = $reviewIDS[$i];//更新操作记录
            $review_old = zget($reviewList, $reviewId);
            $newReviewInfo = zget($newReviewList, $reviewId);
            $changes = common::createChanges($review_old, $newReviewInfo);
            if ($changes) {
                $extra = '绑定会议，会议单号：'. $meetingCode;
                $actionID = $this->loadModel('action')->create('review', $reviewId, 'schedule', $this->post->comment, $extra);
                $res = $this->action->logHistory($actionID, $changes);
            }
        }

        $reviewMeetingInfo = $this->getMeetingByMeetingCode($meetingCode);
        //是否需要增加审核节点
        $isAddNode = $this->getIsAddReviewNode($meetStatus);
        if($isAddNode){
            $ret = $this->addReviewNode($reviewMeetingInfo, $meetStatus);
        }
        $res['result'] = true;
        return $res;
    }


    /**
     * 检查会议评审是否允许编辑
     *
     * @author wangjiurong
     * @param $meetingInfo
     * @param $userAccount
     * @return array|void
     */
    public function checkIsAllowEdit($meetingInfo, $userAccount){
        $res = array(
            'result'  => false,
            'message' => '',
        );
        if(!$meetingInfo){
            $res['message'] = $this->lang->common->errorParamId;
            return $res;
        }
        if(!$userAccount){
            $res['message'] = $this->lang->common->errorParamUser;
            return $res;
        }
        //当前状态
        $status = $meetingInfo->status;
        $allowEditStatusArray = $this->lang->reviewmeeting->allowEditStatusArray;
        //是否在审核状态
        if(!in_array($status, $allowEditStatusArray)){
            $statusDesc = zget($this->lang->reviewmeeting->statusLabelList, $status);
            $res['message'] = sprintf($this->lang->reviewmeeting->checkEditOpResultList['statusError'], $statusDesc);
            return $res;
        }
        $reviewer = explode(',', $meetingInfo->reviewer);
        $owner    = explode(',', $meetingInfo->owner);
        if(!in_array($userAccount, $reviewer) && !in_array($userAccount, $owner)){
            $res['message'] = $this->lang->reviewmeeting->checkEditOpResultList['userError'];
            return $res;
        }
        $res['result'] = true;
        return $res;
    }

    /**
     *更新会议评审信息
     *
     * @param $meetingID
     * @return false|void
     */
    public function update($meetingID){
        $meetingInfo = $this->getMeetingById($meetingID);

        $res = $this->checkIsAllowEdit($meetingInfo, $this->app->user->account);
        if(!$res['result']){
            return false;
        }
        $data = fixer::input('post')
            ->stripTags($this->config->reviewmeeting->editor->edit['id'], $this->config->allowedTags)
            ->get();

        if(!isset($data->reviewer) || empty($data->reviewer)){
            dao::$errors['reviewer'] = $this->lang->reviewmeeting->checkResultList['reviewerEmpty'];
            return false;
        }

        if(!isset($data->owner) || empty($data->owner)){
            dao::$errors['owner'] = $this->lang->reviewmeeting->checkResultList['ownerEmpty'];
            return false;
        }

        if(!isset($data->meetingPlanTime) || empty($data->meetingPlanTime)){
            dao::$errors['meetingPlanTime'] = $this->lang->reviewmeeting->checkResultList['meetingPlanTimeEmpty'];
            return false;
        }
        if(!isset($data->meetingPlanExport) || empty($data->meetingPlanExport)){
            dao::$errors['meetingPlanExport'] = $this->lang->reviewmeeting->checkResultList['meetingPlanExportEmpty'];
            return false;
        }
        //校验会议信息是否重复
        $params = [
            'type'              => $meetingInfo->type,
            'meetingPlanTime' => $data->meetingPlanTime,
            'owner'             => $data->owner,
        ];
        $exWhere = " and id != '{$meetingID}' ";
        $otherMeetingInfo = $this->getInfo($params, 'id', $exWhere);
        if($otherMeetingInfo && isset($otherMeetingInfo->id)){
            dao::$errors['meetingPlanTime'] = $this->lang->reviewmeeting->checkCreate['meetingExist'];
            return false;
        }


        $meetingCode = $meetingInfo->meetingCode;
        //预计会议专家
        $data->meetingPlanExport = implode(',', $data->meetingPlanExport);

        if(!isset($data->reviewIds) || empty($data->reviewIds)){
            $data->reviewIds = [];
        }
        //查询项目原来关联的项目评审列表
        $oldReviewList = $this->loadModel('review')->getReviewListByMeetingCode($meetingInfo->meetingCode, 'id');
        $oldReviewIds  = array_column($oldReviewList, 'id');

        //本次选择的项目评审
        $newReviewList = [];
        //原来的会议编号
        $relatedMeetingCodeList = [];
        if($data->reviewIds){
            $newReviewList = $this->loadModel('review')->getReviewListByIds($data->reviewIds);
            $relatedMeetingCodeList = array_column($newReviewList, 'meetingCode');
            $relatedMeetingCodeList = array_flip(array_flip($relatedMeetingCodeList));
        }

        //删除的项目id
        $delReviewIds = array_diff($oldReviewIds, $data->reviewIds);
        //新增项目评审
        $addReviewIds = array_diff($data->reviewIds, $oldReviewIds);
        $currentTime = helper::now();

        //原来的项目评审
        if(!empty($delReviewIds)){
            //解除绑定
            $updateParams = new stdClass();
            $updateParams->meetingCode     = '';
            $updateParams->meetingPlanTime = '';
            $this->dao->update(TABLE_REVIEW)->data($updateParams)
                ->autoCheck()
                ->where('id')->in($delReviewIds)->exec();
            if(dao::isError()){
                dao::$errors[''] = $this->lang->reviewmeeting->checkResultList['opError'];
                return false;
            }
            //项目列表
            $reviewList = $this->loadModel('review')->getReviewListByIds($delReviewIds);
            foreach ($delReviewIds as $reviewId){
                //项目评审日志
                $reviewInfo = isset($reviewList[$reviewId])?$reviewList[$reviewId]:new stdClass();
                $changes = common::createChanges($reviewInfo, $updateParams);
                $actionID = $this->loadModel('action')->create('review', $reviewId, 'edited');
                $this->action->logHistory($actionID, $changes);
            }

            //删除关联表
            $updateParams = new stdClass();
            $updateParams->deleted = '1';
            $this->dao->update(TABLE_REVIEW_MEETING_DETAIL)->data($updateParams)
                ->autoCheck()
                ->where('review_id')->in($delReviewIds)->exec();
            if(dao::isError()){
                dao::$errors[''] = $this->lang->reviewmeeting->checkResultList['opError'];
                return false;
            }
        }
        //全部删除，本次没有关联项目评审
        if(empty($data->reviewIds)){
            //删除本次会议评审
            $updateParams = new stdClass();
            $updateParams->deleted = '1';
            $updateParams->meetingPlanTime = '';
            $this->dao->update(TABLE_REVIEW_MEETING)->data($updateParams)
                ->autoCheck()
                ->where('id')->eq($meetingID)->exec();
            if(dao::isError()){
                dao::$errors[''] = $this->lang->reviewmeeting->checkResultList['opError'];
                return false;
            }
        }else{
            //修改项目评审信息
            $updateParams = new stdClass();
            $updateParams->meetingCode       = $meetingInfo->meetingCode;
            $updateParams->owner             = $data->owner; //评审主席也需要统一
            $updateParams->meetingPlanTime   = $data->meetingPlanTime;
            $updateParams->meetingPlanExport = $data->meetingPlanExport;
            $this->dao->update(TABLE_REVIEW)->data($updateParams)
                ->autoCheck()
                ->where('id')->in($data->reviewIds)->exec();
            if(dao::isError()){
                dao::$errors[''] = $this->lang->reviewmeeting->checkResultList['opUpdateReviewError'];
                return false;
            }
            //项目评审日志
            foreach ($data->reviewIds as $reviewId){
                $reviewInfo = isset($newReviewList[$reviewId]) ? $newReviewList[$reviewId] : new stdClass();
                $changes = common::createChanges($reviewInfo, $updateParams);
                $actionID = $this->loadModel('action')->create('review', $reviewId, 'edited');
                $this->action->logHistory($actionID, $changes);
            }
            //设置
            if(!empty($addReviewIds)){
                foreach ($addReviewIds as $reviewId){
                    $detailInfo = $this->getMeetingDetailInfoByReviewId($reviewId, 'id');
                    if(isset($detailInfo->id)){
                        //编辑
                        $detailUpdateParams = new stdClass();
                        $detailUpdateParams->review_meeting_id = $meetingInfo->id;
                        $detailUpdateParams->meetingCode       = $meetingInfo->meetingCode;
                        $this->dao->update(TABLE_REVIEW_MEETING_DETAIL)->data($detailUpdateParams)
                            ->autoCheck()
                            ->where('id')->eq($detailInfo->id)->exec();

                        if(dao::isError()){
                            dao::$errors[''] = $this->lang->reviewmeeting->checkResultList['opUpdateReviewRelationError'];
                            return false;
                        }
                    }else{
                        $newReviewInfo = $newReviewList[$reviewId];
                        //新增
                        $detailAddParams = new stdClass();
                        $detailAddParams->review_meeting_id = $meetingInfo->id;
                        $detailAddParams->review_id         = $reviewId;
                        $detailAddParams->meetingCode       = $meetingInfo->meetingCode;
                        $detailAddParams->meetingRealTime   = $meetingInfo->meetingRealTime;
                        $detailAddParams->realExport        = $meetingInfo->realExport;
                        $detailAddParams->status            = $newReviewInfo->status;
                        $detailAddParams->createUser        = $this->app->user->account;
                        $detailAddParams->createTime        = $currentTime;
                        $this->dao->insert(TABLE_REVIEW_MEETING_DETAIL)->data($detailAddParams)
                            ->autoCheck()
                            ->exec();
                        if(dao::isError()){
                            dao::$errors[''] = $this->lang->reviewmeeting->checkResultList['opAddReviewRelationError'];
                            return false;
                        }
                    }
                    //项目评审绑定会议信息
                    $actionID = $this->loadModel('action')->create('review', $reviewId, 'bindmeetting', '', $meetingInfo->meetingCode);
                }
            }
            //修改主表
            $updateParams = new stdClass();
            $updateParams->reviewer = $data->reviewer;
            $updateParams->owner = $data->owner;
            $updateParams->meetingPlanTime   = $data->meetingPlanTime;
            $updateParams->meetingPlanExport = $data->meetingPlanExport;
            $updateParams->editBy            = $this->app->user->account;
            $updateParams->editTime          = $currentTime;
            //修改主表信息
            $this->dao->update(TABLE_REVIEW_MEETING)->data($updateParams)
                ->autoCheck()
                ->where('id')->eq($meetingID)->exec();

            if(dao::isError()) {
                dao::$errors[''] = $this->lang->reviewmeeting->checkResultList['opError'];
                return false;
            }
            //修改当前会议评审状态和审核节点信息
            $res = $this->updateReviewMeetingStatus($meetingCode, $meetingInfo);
            if(!$res['result']){
                dao::$errors[''] = $this->lang->reviewmeeting->checkResultList['opUpdateMeetingStatusError'];
                return false;
            }
            //其他用到的会议评审
            $diffMeetingCodes = array_diff($relatedMeetingCodeList, array($meetingCode));
            $diffMeetingCodes = array_filter($diffMeetingCodes);
            if(!empty($diffMeetingCodes)){
                foreach ($diffMeetingCodes as $meetingCode){
                    $res = $this->updateReviewMeetingStatus($meetingCode);
                    if(!$res['result']){
                        dao::$errors[''] = $this->lang->reviewmeeting->checkResultList['opCancelBindOtherError'];
                        return false;
                    }
                }
            }
        }
        return common::createChanges($meetingInfo, $updateParams);
    }

    /**
     *修改会议评审状态
     *
     * @param $meetingCode
     * @param $oldMeetingInfo
     * @return array|bool|void
     */
    public function updateReviewMeetingStatus($meetingCode, $oldMeetingInfo = ''){
        $res = array(
            'result'  => false,
            'message' => '',
        );
        if(!$meetingCode){
            $res['message'] = $this->lang->reviewmeeting->paramsError;
            return $res;
        }
        $meetingInfo = $this->getMeetingByMeetingCode($meetingCode);
        if(empty((array)$meetingInfo)){
            $res['message'] = $this->lang->reviewmeeting->meetingEmpty;
            return $res;
        }
        $meetingID = $meetingInfo->id;
        //查询会议评审下的项目评审信息
        $detailInfo = $this->getOneReviewDetailInfoByMeetingCode($meetingCode);
        if(!$detailInfo){
            //作废会议评审
            $updateParams = new stdClass();
            $updateParams->deleted = '1';
            $updateParams->meetingPlanTime = '';
            $this->dao->update(TABLE_REVIEW_MEETING)->data($updateParams)
                ->autoCheck()
                ->where('id')->eq($meetingID)->exec();
            if(dao::isError()){
                $res['message'] = $this->lang->reviewmeeting->checkResultList['opError'];
                return false;
            }
            //返回
            $res['result'] = true;
            return $res;
        }
        if($meetingInfo->status == $this->lang->reviewmeeting->statusList['waitMeetingOwnerReview'] ){ //等待评审主席确定评审结论
            //返回
            $res['result'] = true;
            return $res;
        }
        //存在详情
        $updateParams = new stdClass();
        //会议状态
        $status = $meetingInfo->status;
        //会议评审下其中一条项目评审状态
        $detailStatus = $detailInfo->status;
        //查询总的会议数量
        $exWhere = " status in ('waitFormalReview', 'waitMeetingReview')";
        $totalCount = $this->getReviewMeetingValidDetailCount($meetingCode, '', $exWhere);
        //当前会议状态数量
        $currentStatusCount = $this->getReviewMeetingValidDetailCount($meetingCode, $detailStatus);
        if($totalCount == $currentStatusCount){
            if($status != $detailStatus){
                $updateParams->status = $detailStatus;
                if($detailStatus == $this->lang->reviewmeeting->statusList['waitMeetingReview']){ //会议评审中
                    $updateParams->dealUser = $meetingInfo->reviewer;
                }else{
                    $updateParams->dealUser = '';
                }
            }else{
                if($detailStatus == $this->lang->reviewmeeting->statusList['waitMeetingReview']){ //会议评审中
                    $updateParams->dealUser = $meetingInfo->reviewer;
                }else{
                    $updateParams->dealUser = '';
                }
            }
        }else{
            $updateParams->status = $this->lang->reviewmeeting->statusList['waitFormalReview']; //线上评审中
            $updateParams->dealUser = '';
        }

        //编辑
        $this->dao->update(TABLE_REVIEW_MEETING)->data($updateParams)
            ->autoCheck()
            ->where('id')->eq($meetingID)->exec();
        /*
        if(dao::isError()){
            $res['message'] = $this->lang->reviewmeeting->checkResultList['opError'];
            return false;
        }
        */

        if(isset($updateParams->status) && ($status != $updateParams->status)){ //审核节点的修改
            if($updateParams->status == $this->lang->reviewmeeting->statusList['waitFormalReview']){ //删除审核节点
                $reviewList = $this->loadModel('review')->getReviewListByMeetingCode($meetingInfo->meetingCode, 'id,version');
                //删除审核节点
                $nodeCode = $this->lang->reviewmeeting->nodeCodeList['meetingReview']; //会议评审节点
                $nodeId = $this->loadModel('review')->getReviewNodeId('reviewmeeting', $meetingID, 0, $nodeCode);
                if($nodeId){
                    //删除
                    $this->dao->delete()->from(TABLE_REVIEWNODE)->where('id')->eq($nodeId)->exec();
                    $this->dao->delete()->from(TABLE_REVIEWER)->where('node')->eq($nodeId)->exec();
                }

                //评审列表(20220817-删除会议评审审核节点的时候不删除项目评审审核节点)
                /*
                if(!empty($reviewList)){
                    foreach ($reviewList as $reviewInfo){
                        $reviewId = $reviewInfo->id;
                        $version  = $reviewInfo->version;
                        $nodeId   = $this->loadModel('review')->getReviewNodeId('review', $reviewId, $version, $nodeCode);
                        if($nodeId){
                            //删除
                            $this->dao->delete()->from(TABLE_REVIEWNODE)->where('id')->eq($nodeId)->exec();
                            $this->dao->delete()->from(TABLE_REVIEWER)->where('node')->eq($nodeId)->exec();
                        }
                    }
                }
                */
            }elseif($updateParams->status == $this->lang->reviewmeeting->statusList['waitMeetingReview']){
                $ret = $this->addReviewNode($meetingInfo, $updateParams->status); //新增审核节点
                //新增发邮件功能
                $actionID = $this->loadModel('action')->create('reviewmeeting', $meetingID, 'autoupdatestatus');
            }
        } else{
            //不需要修改会议评审状态
            if($meetingInfo->status == $this->lang->reviewmeeting->statusList['waitMeetingReview']) { //待会议评审中,需要检查是否需要新增单个评审人节点
                $nodeCode = $this->lang->reviewmeeting->nodeCodeList['meetingReview']; //会议评审节点
                if($oldMeetingInfo && ($oldMeetingInfo->reviewer != $meetingInfo->reviewer)){ //评审专员做了修改
                    $nodeId = $this->loadModel('review')->getReviewNodeId('reviewmeeting', $meetingID, 0, $nodeCode);
                    if($nodeId){
                        $this->dao->update(TABLE_REVIEWER)->set('reviewer')->eq($meetingInfo->reviewer)->where('node')->eq($nodeId)->exec();
                    }
                }

                //项目评审信息
                $objectType = 'review';
                $reviewList = $this->loadModel('review')->getReviewListByMeetingCode($meetingInfo->meetingCode, 'id,version');
                if(!empty($reviewList)){
                    $dealUser = $meetingInfo->dealUser;
                    if(!is_array($dealUser)){
                        $reviewers = explode(',', $dealUser);
                    }else{
                        $reviewers = $dealUser;
                    }
                    foreach ($reviewList as $reviewInfo){
                        $reviewId = $reviewInfo->id;
                        $version  = $reviewInfo->version;
                        $nodeId   = $this->loadModel('review')->getReviewNodeId($objectType, $reviewId, $version, $nodeCode);
                        if(!$nodeId){ //不存在，新增审核节点
                            $maxStage = $this->loadModel('review')->getReviewMaxStage($reviewId, $objectType, $version);
                            $stage    = $maxStage + 1;
                            //新增会议审核节点信息
                            $reviewNodes = array(
                                array(
                                    'reviewers' => $reviewers,
                                    'stage'     => $stage,
                                    'nodeCode'  => $nodeCode,
                                )
                            );
                            $this->loadModel('review')->submitReview($reviewId, $objectType,  $version, $reviewNodes);
                            //修改待处理人
                            $this->dao->update(TABLE_REVIEW)->set('dealUser')->eq(implode(',', $reviewers))->where('id')->eq($reviewId)->exec();
                        }else{ //可能需要修改节点审核人
                            $this->dao->update(TABLE_REVIEWER)->set('reviewer')->eq($meetingInfo->reviewer)->where('node')->eq($nodeId)->exec();
                            $this->dao->update(TABLE_REVIEW)->set('dealUser')->eq($meetingInfo->reviewer)->where('id')->eq($reviewId)->exec();
                        }
                    }
                }
            }
        }
        //返回
        $res['result'] = true;
        return $res;
    }

    /**
     * 获取一条会议下所有附件id
     * @param $meetingId
     * @return mixed
     */
    public function getReviewFiles($meetingId){
        $files = $this->dao->select('t3.id')->from(TABLE_REVIEW_MEETING)->alias('t1')
            ->leftJoin(TABLE_REVIEW)->alias('t2')
            ->on('t1.meetingCode =  t2.meetingCode')
            ->leftJoin(TABLE_FILE)->alias('t3')
            ->on('t2.id = t3.objectID')
            ->where('t1.deleted')->eq(0)
            ->andWhere('t3.deleted')->eq(0)
            ->andWhere('t1.id')->eq($meetingId)
            ->andWhere('t3.objectType')->eq('review')
            ->fetchAll();
        return $files;
    }

    /**
     * 获取会议评审结果
     * @param $meetingCode
     * @param $nodeCode
     * @return mixed
     */
    public function getReviewResult($meetingCode,$nodeCode){
        $source = $this->dao->select('group_concat(distinct t2.title) title,t3.status,group_concat(distinct t2.status) reviewstatus,t3.createdBy,t4.createdDate,t4.reviewer,group_concat(distinct t4.comment) comment,group_concat(distinct t1.status) meetingstatus')->from(TABLE_REVIEW_MEETING)->alias('t1')
            ->leftJoin(TABLE_REVIEW)->alias('t2')
            ->on('t1.meetingCode =  t2.meetingCode')
            ->leftJoin(TABLE_REVIEWNODE)->alias('t3')
            ->on('t1.id = t3.objectID')
            ->leftJoin(TABLE_REVIEWER)->alias('t4')
            ->on('t3.id = t4.node')
            ->where('t1.deleted')->eq(0)
            ->andWhere('t2.deleted')->eq(0)
            ->andWhere('t1.meetingCode')->eq($meetingCode)
            ->andWhere('t3.nodeCode')->eq($nodeCode)
            ->groupby('t1.meetingCode')
            ->fetchAll();
        return $source;
    }

    /**
     * 获取会议评审下项目评审结果
     * @param $meetingCode
     * @param $nodeCode
     * @return mixed
     */
    public function getResult($meetingCode,$nodeCode){
        $source = $this->dao->select('group_concat(distinct t2.title) title,t4.status,t4.extra')->from(TABLE_REVIEW_MEETING_DETAIL)->alias('t1')
            ->leftJoin(TABLE_REVIEW)->alias('t2')
            ->on('t1.meetingCode =  t2.meetingCode')
            ->leftJoin(TABLE_REVIEWNODE)->alias('t3')
            ->on('t2.id = t3.objectID and t2.version = t3.version')
            ->leftJoin(TABLE_REVIEWER)->alias('t4')
            ->on('t3.id = t4.node')
            ->where('t1.deleted')->eq(0)
            ->andWhere('t1.meetingCode')->eq($meetingCode)
            ->andWhere('t3.nodeCode')->eq($nodeCode)
            ->groupby('t2.id')
            ->fetchAll();
        return $source;
    }

    /**
     * 获得评审操作类型
     *
     * @param $status
     * @return string
     */
    public function getOpReviewActionType($status){
        $actionType = 'reviewed';
        if($status == $this->lang->reviewmeeting->statusList['waitMeetingOwnerReview']){ //完成会议纪要
            $actionType = 'finishmeetingsummary'; //完成会议纪要
        }
        return $actionType;
    }

    /**
     *判断是否允许编辑评审专员
     *
     * @param $status
     * @return bool
     */
    public function getIsAllowEditReviewer($status){
        $isAllowEdit = false;
        if(in_array($status, $this->lang->reviewmeeting->allowEditReviewerStatusArray)){
            $isAllowEdit = true;
        }
        return $isAllowEdit;
    }
    /**
     * Build search form.
     *
     * @param  int    $queryID
     * @param  string $actionURL
     * @access public
     * @return void
     */
    public function buildSearchForm($queryID, $actionURL)
    {
        $this->config->reviewmeet->search['actionURL'] = $actionURL;
        $this->config->reviewmeet->search['queryID']   = $queryID;
        $this->loadModel('search')->setSearchParams($this->config->reviewmeet->search);
    }
    /**
     * Build search form.
     *
     * @param  int    $queryID
     * @param  string $actionURL
     * @access public
     * @return void
     */
    public function buildSearchFormNo($queryID, $actionURL)
    {
        $this->config->reviewnomeet->search['actionURL'] = $actionURL;
        $this->config->reviewnomeet->search['queryID']   = $queryID;
        $this->loadModel('search')->setSearchParams($this->config->reviewnomeet->search);
    }

    /**
     * 处理连表sql查询时共有字段无法识别的问题
     * @param string $query
     * @param string $alias
     * @param string $field
     * @param string $str
     * @return string|string[]
     */
    public function dealSqlAmbiguous($query ='',$alias='',$field = '',$str = '')
    {
        if(strpos($query,  "`".$field."`") !== false)
        {
            $query = str_replace("`".$field."`", $alias.".`".$field."`", $query);
        }
        return $query;
    }
    //确认开会
    public function confirmmeeting($meetingID){
        $res = array(
            'result'  => false,
            'message' => '',
        );
        $this->loadModel('mail');
        $data  = fixer::input('post')
            ->join('reviewer',',')
            ->join('mailto',',')
            ->get();
        if ($data->reviewer == ''){
            $res = ['result'=>false,'message'=>$this->lang->reviewmeeting->addressno];
            return $res;
        }
        $meetingInfo = $this->getById($meetingID);
        $users  = $this->loadModel('user')->getPairs('noletter');
        $outsideList1 = array('' => '') +$this->loadModel('user')->getUsersNameByType('outsideExpertType');
        $outsideList2 = array('' => '') +$this->loadModel('user')->getUsersNameByType('outside');
        $users = array_merge($users, $outsideList1, $outsideList2);
        //部门信息
        $deptMap = $this->loadModel('dept')->getOptionMenu();
        $this->app->loadLang('projectplan');
        $meetingCode = $meetingInfo->meetingCode;
        //会议评审相关项目评审
        $reviewList = $this->loadModel('review')->getReviewListByMeetingCode($meetingCode);
        $reviewTitleList  = []; //评审内容项目标题并集
        $createdDeptList  = []; //评审发起部门
        $createdByList    = []; //评审发起人
        $projectPmList    = [];  //项目经理
        $managerList      = [];//部门领导
        $projectNameList  = []; //项目名称
        $projectTypeList  = []; //项目类型
        $projectBasisList = []; //项目来源
        if(!empty($reviewList)){
            $projectIds      = array_column($reviewList, 'project');
            $projectList     = $this->loadModel('project')->getProjectListByIds($projectIds, 'id,name,PM');
            $projectPlanList = $this->loadModel('projectplan')->getProjectPlanListByProjectIds($projectIds, 'id,basis,type');
            $projectPmList   = array_column($projectList, 'PM');
            $projectNameList = array_column($projectList, 'name');
            $projectTypeList = array_column($projectPlanList, 'type');
            $projectBasisList = array_column($projectPlanList, 'basis');
            //创建人部门信息
            $createdDeptIds = array_column($reviewList, 'createdDept');
            $deptList = $this->loadModel('dept')->getDeptListByIds($createdDeptIds, 'manager');
            foreach ($deptList as $deptInfo){
                $manager = $deptInfo->manager;
                if($manager){
                    $managerArray = explode(',', $manager);
                    $managerList = array_merge($managerList, $managerArray);
                }
                $managerList = array_flip(array_flip($managerList));
            }
            //项目信息
            foreach ($reviewList as $val){
                $reviewTitleList[] = $val->title;
                $createdDeptList[] = $val->createdDept;
                $createdByList[]   = $val->createdBy;
            }
        }
        /*获取邮件 收抄件人 */
        $sendUsers = $this->getPendingToAndCcList($meetingInfo);
        $toList = $data->reviewer;
        $ccList = isset($data->mailto) ? $data->mailto : '';
        /* 获取后台通知中配置的邮件发信。*/
        $this->app->loadLang('custommail');
        $mailConf   = isset($this->config->global->setReviewmeetingMail) ? $this->config->global->setReviewmeetingMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf   = json_decode($mailConf);
        $mailTitle = vsprintf($this->lang->reviewmeeting->confirmmeetingTitle,[$meetingInfo->meetingCode]);
        /* Get mail content. */
        $oldcwd     = getcwd();
        $modulePath = $this->app->getModulePath($appName = '', 'reviewmeeting');
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
        /* 处理邮件标题。*/
        $subject = $mailTitle;
        /* Send emails. */
        if(empty($toList)) return false;
        $this->mail->send($toList, $subject, $mailContent,$ccList);

        if($this->mail->isError()) trigger_error(join("\n", $this->mail->getError()));
        $changes = common::createChanges($meetingInfo, $meetingInfo);
        $actionID = $this->loadModel('action')->create('reviewmeeting', $meetingID, 'confirmmeeting', $this->post->comment);
        $res = $this->action->logHistory($actionID, $changes);
        if(dao::isError()){
            $res['message'] = dao::getError();
            return $res;
        }

        $res['result'] = true;
        return $res;
    }
    //根据会议id获取评审详情
    public function getMeetingDetail($meetingID,$fileds = "*"){
        if (!$meetingID){
            return false;
        }
        $data = $this->dao->select($fileds)->from(TABLE_REVIEW_MEETING_DETAIL)->where('deleted')->eq('0')->andwhere('review_meeting_id')->eq($meetingID)->fetchall();
        return $data;
    }
    //邮件通知表格
    public function getMailtable($head,$arr){
        $headStr = "";
        for ($i = 0;$i < count($head);$i++){
            $headStr .= '<th style="width: 50px; border: 1px solid #e5e5e5; padding: 5px;max-width: 100px">'.$head[$i].'</th>';
        }
        $tbodyStr = "";
        foreach ($arr as $k=>$v) {
            $tbodyStr .= '<tr>';
            for ($j = 0;$j < count($v);$j++) {
                $tbodyStr .= '<td style="padding: 5px; border: 1px solid #e5e5e5;max-width: 100px;">'.$v[$j].'</td>';
            }
            $tbodyStr .= '</tr>';
        }
        $str = '<table cellpadding="0" cellspacing="0" style="width: 100%;max-width: 720px; border: 1px solid #e5e5e5; margin-bottom: 15px; border-collapse: collapse; font-size: 13px; text-align: left;">
              <tbody><tr>'.$headStr.'</tr>'.$tbodyStr.'
            </tbody></table>';
        return $str;
    }
    //邮件通知、发送邮件，保存记录
    public function notice($meetingID){
        $this->app->loadConfig("mail");
        $this->config->mail->isHTML=true;
        $res = array(
            'result'  => false,
            'message' => '',
        );
        $this->loadModel('mail');

        $data  = fixer::input('post')
            ->join('reviewer',',')
            ->join('mailto',',')
            ->get();
        if (!isset($data->reviewer)){
            $res = ['result'=>false,'message'=>$this->lang->reviewmeeting->addressno];
            return $res;
        }
        $tableStyle = "<style>table{max-width:720px;} th,td{border:1px solid #e5e5e5;padding:5px;max-width:100px}</style>";
        $data->mailContent = "<html><body>".$tableStyle.$_POST['mailContent']."</body></html>";
        $content = $data->mailContent;

        $mailData = $this->loadModel('file')->processImgURL($data, ['mailContent']);
        $mailData = $this->loadModel('file')->replaceImgURL($mailData, 'mailContent');
        $this->mail->send($data->reviewer, $data->mailtitle, $content,$data->mailto);
        if($this->mail->isError()) trigger_error(join("\n", $this->mail->getError()));

        $changes = common::createChanges($mailData, $mailData);
        $actionID = $this->loadModel('action')->create('reviewmeeting', $meetingID, 'notice', $this->post->comment);
        $res = $this->action->logHistory($actionID, $changes);
        if(dao::isError()){
            $res['message'] = dao::getError();
            return $res;
        }

        $params = new stdClass();
        $params->review_meeting_id = $meetingID;
        $params->type = 'notice';
        $params->addressee = $data->reviewer;
        $params->mailto = isset($data->mailto)?$data->mailto:'';
        $params->title = $data->mailtitle;
        $params->content = $content;
        $params->sendTime = date("Y-m-d H:i:s");
        $this->dao->insert(TABLE_REVIEW_MEETING_MAIL)->data($params)->exec();
        if(dao::isError()){
            $res['message'] = dao::getError();
            return $res;
        }
        $res['result'] = true;
        return $res;

    }
    //获取当前会议最新发送邮件通知记录
    public function getNoticeMailOne($meetingID){
        return $this->dao->select("*")->from(TABLE_REVIEW_MEETING_MAIL)->where('review_meeting_id')->eq($meetingID)->andWhere('type')->eq('notice')->orderby("id_desc")->fetch();
    }

    /**
     *获得实际评审专家
     *
     * @param $expertUses
     * @param $reviewedByUsers
     * @param $outsideUsers
     * @return string
     */
    public function getReviewRealExportUsers($expertUses, $reviewedByUsers, $outsideUsers){
        $realExportUsers = '';
        $expert     = [];
        $reviewedBy = [];
        $outside   = [];
        if($expertUses){ //评审内部专家
            $expert = explode(',', $expertUses);
        }
        if($reviewedByUsers){ //评审外部专家1
            $reviewedBy = explode(',', $reviewedByUsers);
        }
        if($outsideUsers){ //评审外部专家2
            $outside = explode(',', $outsideUsers);
        }

        $allUsers = array_merge($expert, $reviewedBy, $outside);
        if($allUsers){
            $allUsers = array_flip(array_flip($allUsers));
            $realExportUsers = implode(',', $allUsers);
        }
        return $realExportUsers;
    }

    /**
     *根据会议号获得评审详情列表
     *
     * @param $meetingCode
     * @param string $select
     * @return array
     */
    public function getMeetingDetailList($meetingCode, $select = "*"){
        $data = [];
        if (!$meetingCode){
            return $data;
        }
        $ret = $this->dao->select($select)
            ->from(TABLE_REVIEW_MEETING_DETAIL)
            ->where('deleted')->eq('0')
            ->andwhere('meetingCode')->eq($meetingCode)
            ->fetchAll();
        if($ret){
            $data = $ret;
        }
        return $data;
    }

    // 变更
    public function change($meetingID, $versions = []){
        $meetingInfo = $this->getById($meetingID);
        $data = fixer::input('post')->get();
        // 校验必填
        if(empty($data->realExport) || empty($data->meetingRealTime)){
            dao::$errors[''] = $this->lang->reviewmeeting->checkResultList['changeListError'];
            return false;
        }
        $updateMeeting                  = new stdClass();
        $updateMeeting->realExport      = implode(',',$data->realExport);
        $updateMeeting->meetingRealTime = $data->meetingRealTime;

        $this->dao->update(TABLE_REVIEW_MEETING)->data($updateMeeting)->where('id')->eq($meetingID)->exec();
        //修改评审详情
        $this->dao->update(TABLE_REVIEW_MEETING_DETAIL)->data($updateMeeting)->where('review_meeting_id')->eq($meetingID)->exec();

        //会议评审信息
        $meetComment    = '';
        $reviewIds      = $data->reviewIds;
        $reviewList     = $this->loadModel('review')->getReviewListByIds($reviewIds, 'id, title, meetingRealTime');

        $updateReviewIds = [];
        // 修改单条评审会议纪要
        foreach($data->reviewIds as $key => $reviewId){
            $comment = 'comment_'.$key;
            $nodeId = $this->loadModel('review')->getReviewNodeId('review', $reviewId, $versions[$reviewId], 'meetingReview');
            if($nodeId) $this->dao->update(TABLE_REVIEWER)->set('comment')->eq($data->$comment)->where('node')->eq($nodeId)->exec();
            $reviewId        = $reviewIds[$key];
            $reviewInfo      = $reviewList[$reviewId];
            if($reviewInfo->meetingRealTime != $data->meetingRealTime){
                $updateReviewIds[] = $reviewId;
            }
            $commentKey ='comment_'.$key;
            if($data->$commentKey){
                $meetComment .= $reviewInfo->title . '：'. $data->$commentKey . '<br/>'; //所有的评论信息
            }
        }
        //修改项目评审表中的实际参会专家
        if($updateReviewIds){
            $updateParams = new stdClass();
            $updateParams->meetingRealTime = $data->meetingRealTime;
            $this->dao->update(TABLE_REVIEW)->data($updateParams)->where('id')->in($updateReviewIds)->exec();
        }

        // 修改会议评审会议纪要
        $meetingNodeId = $this->loadModel('review')->getReviewNodeId('reviewmeeting', $meetingID, 0, 'meetingReview');
        $meetingInfo->comment   = $this->dao->select('comment')->from(TABLE_REVIEWER)->where('node')->eq($meetingNodeId)->fetch('comment');
        $updateMeeting->comment = $meetComment;
        if($meetingNodeId) $this->dao->update(TABLE_REVIEWER)->set('comment')->eq($meetComment)->where('node')->eq($meetingNodeId)->exec();

        if(dao::isError()){
            $res['message'] = dao::getError();
            return $res;
        }
        //返回
        $logChange = common::createChanges($meetingInfo, $updateMeeting);
        return $logChange;
    }

    // 喧喧消息
    public function getXuanxuanTargetUser($obj,$objectType, $objectID, $actionType, $actionID, $actor = ''){
        $meetingInfo = $this->getById($objectID);
        /*获取收信人 */
        $sendUsers = $this->getPendingToAndCcList($meetingInfo);
        $toList = $sendUsers['toList'];

        $server   = $this->loadModel('im')->getServer('zentao');
        $url = $server.helper::createLink($objectType, 'meetingview', "id=$objectID", 'html');
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
     * 根据条件获得单条信息
     *
     * @param $params
     * @param string $select
     * @param $exWhere
     * @return bool
     */
    public function getInfo($params, $select = '*', $exWhere = ''){
        if(!($params)){
            return false;
        }
        $sql  = "select ".$select." from " . TABLE_REVIEW_MEETING .
            " where 1 and deleted = '0'";
        foreach ($params as $key => $val){
            if(is_array($val)){
                $valStr = implode(',', $val);
                $sql .= " and {$key} in (".$valStr.")";
            }else{
                if($key == 'meetingPlanTime' && (strlen($val) < 19)){
                    $val .= ':00';
                }
                $sql .= " and {$key} = '{$val}'";
            }
        }
        if($exWhere){
            $sql .= $exWhere;
        }
        $data = $this->dao->query($sql)->fetch();
        if(!$data){
            return false;
        }
        return $data;
    }


    /**
     * 作废项目评审详情
     *
     * @param $meetingCode
     * @param $reviewID
     * @return bool
     */
    public function deleteMeetingDetail($meetingCode, $reviewID){
        $res = false;
        //取消会议详情
        $updateParams = new stdClass();
        $updateParams->deleted = '1';
        $this->dao->update(TABLE_REVIEW_MEETING_DETAIL)->data($updateParams)
            ->autoCheck()
            ->where('meetingCode')->eq($meetingCode)
            ->andWhere('review_id')->eq($reviewID)
            ->andWhere('deleted')->eq('0')
            ->exec();
        if(dao::isError()){
            return $res;
        }
        return $res;
    }


    /**
     * 修改会议详情信息
     *
     * @param $meetingCode
     * @param $reviewID
     * @param $params
     * @return bool
     */
    public function activeMeetingDetailInfo($meetingCode, $reviewID, $params){
        $res = false;
        $this->dao->update(TABLE_REVIEW_MEETING_DETAIL)->data($params)
            ->autoCheck()
            ->where('meetingCode')->eq($meetingCode)
            ->andWhere('review_id')->eq($reviewID)
            ->andWhere('deleted')->eq('1')
            ->exec();
        if(dao::isError()){
            return $res;
        }
        $res = true;
        return $res;
    }

}