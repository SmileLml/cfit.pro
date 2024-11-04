<?php
class residentworkModel extends model
{

    public function buildSearchForm($queryID, $actionURL){
        $this->config->residentwork->search['actionURL'] = $actionURL;
        $this->config->residentwork->search['queryID']   = $queryID;
        $this->loadModel('search')->setSearchParams($this->config->residentwork->search);
    }

    /**
     * Project: chengfangjinke
     * Method: getList
     * User: wangjiruong
     * Year: 2022
     * Date: 2022/10/13
     * Time: 14:53
     * Desc: 获得部门视图列表
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $browseType
     * @param $queryID
     * @param $orderBy
     * @param null $pager
     * @return mixed
     */
    public function getList($browseType = 'enable', $queryID, $orderBy, $pager = null){
        $residentWorkQuery = '';
        if($browseType == 'bysearch') {
            $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : '';
            if($query) {
                $this->session->set('residentworkQuery', $query->sql);
                $this->session->set('residentworkForm', $query->form);
            }
            if($this->session->residentworkQuery == false) $this->session->set('residentworkQuery', ' 1 = 1');
            $residentWorkQuery = $this->searchQueryParams($this->session->residentworkQuery);
        }
        $data = $this->dao->select('*')
            ->from(TABLE_RESIDENT_SUPPORT_DAY)
            ->where('deleted')->eq('0')
            ->beginIF($browseType == 'enable')->andWhere('enable')->eq(1)->fi()
            ->beginIF($browseType == 'unable')->andWhere('enable')->eq(0)->fi()
            ->beginIF($browseType == 'bysearch')->andWhere($residentWorkQuery)->fi()
            ->orderBy('dutyDate_desc')
            ->page($pager)
            ->fetchAll();
        $this->dataMake($data);
        return $data;
    }

    /**
     * @param $residentWorkQuery
     * @return array|mixed|string|string[]
     */
    public function searchQueryParams($residentWorkQuery)
    {
        $workArr = [];
        $queryArr = [];
        $and = strpos($residentWorkQuery,') AND (');
        $or  = strpos($residentWorkQuery,') OR (');
        $whereType = 'and';
        if($and){
            //重置
            $residentFirst = "(( 1 ) AND ( 1 ))";
            $this->session->set('residentFirst',preg_replace("/\s+/","",$residentFirst));
            //转换数组
            $queryArr = explode('AND', $residentWorkQuery);
        }
        if($or){
            //重置
            $residentFirst = "(( 1 ) OR ( 1 ))";
            $this->session->set('residentFirst',preg_replace("/\s+/","",$residentFirst));
            $whereType = 'or';
            //转换数组
            $queryArr = explode('OR', $residentWorkQuery);
        }
        $count = count($queryArr);
        //值班日期
        if (strpos($residentWorkQuery, 'dutyDate')) {
            $workArr = $this->commonDayQuery('`dutyDate`', $workArr, $count, $residentWorkQuery,$whereType);
        }
        //值班组长
        if (strpos($residentWorkQuery, 'dutyGroupLeader')) {
            $workArr = $this->commonDayQuery('`dutyGroupLeader`', $workArr, $count, $residentWorkQuery,$whereType);
        }
        //值班岗位
        if (strpos($residentWorkQuery, 'postTypeInfo')) {
            $residentWorkQuery = str_replace('postTypeInfo', 'postType', $residentWorkQuery);
            $workArr = $this->commonDetailQuery('`postType`', $workArr, $count, $residentWorkQuery,$whereType);
        }
        //值班部门
        if (strpos($residentWorkQuery, 'dutyUserDept')) {
            $workArr = $this->commonDetailQuery('`dutyUserDept`', $workArr, $count, $residentWorkQuery,$whereType);
        }
        //值班人员
        if (strpos($residentWorkQuery, 'dutyUser')) {
            $workArr = $this->commonDetailQuery('`dutyUser`', $workArr, $count, $residentWorkQuery,$whereType);
        }
        //时长类型
        if (strpos($residentWorkQuery, 'timeType')) {
            $workArr = $this->commonDetailQuery('`timeType`', $workArr, $count, $residentWorkQuery,$whereType);
        }
        //值班要求
        if (strpos($residentWorkQuery, 'requireInfo')) {
            $workArr = $this->commonDetailQuery('`requireInfo`', $workArr, $count, $residentWorkQuery,$whereType);
        }
        //值班类型
        if (strpos($residentWorkQuery, '`dutyType`')) {
            $residentWorkQuery = str_replace('dutyType', 'type', $residentWorkQuery);
            $workArr = $this->commonTemplateQuery('`type`', $workArr, $count, $residentWorkQuery,$whereType);
        }
        //日志填写时间
        if (strpos($residentWorkQuery, 'createdDate')) {
            $workArr = $this->commonWorkQuery('`createdDate`', $workArr, $count, $residentWorkQuery,$whereType);
        }
        //日志填写人员
        if (strpos($residentWorkQuery, 'createdBy')) {
            $workArr = $this->commonWorkQuery('`createdBy`', $workArr, $count, $residentWorkQuery,$whereType);
        }
        //值班地点
        if (strpos($residentWorkQuery, 'dutyPlace')) {
            $residentWorkQuery = str_replace('dutyPlace', 'area', $residentWorkQuery);
            $workArr = $this->commonWorkQuery('`area`', $workArr, $count, $residentWorkQuery,$whereType);
        }
        //实际值班组长
        if (strpos($residentWorkQuery, 'actualLeader')) {
            $residentWorkQuery = str_replace('actualLeader', 'groupLeader', $residentWorkQuery);
            $workArr = $this->commonWorkQuery('`groupLeader`', $workArr, $count, $residentWorkQuery,$whereType);
        }
        //实际值班人员
        if (strpos($residentWorkQuery, 'actualUser')) {
            $residentWorkQuery = str_replace('actualUser', 'realDutyuser', $residentWorkQuery);
            $workArr = $this->commonWorkDetailQuery('`realDutyuser`', $workArr, $count, $residentWorkQuery,$whereType);
        }
        //日期类型
        if (strpos($residentWorkQuery, 'dateType')) {
            $workArr = $this->commonWorkQuery('`dateType`', $workArr, $count, $residentWorkQuery,$whereType);
        }
        //是否存在应急事件
        if (strpos($residentWorkQuery, 'isEmergency')) {
            $workArr = $this->commonWorkQuery('`isEmergency`', $workArr, $count, $residentWorkQuery,$whereType);
        }
        //应急事件简要说明
        if (strpos($residentWorkQuery, 'emergencyRemark')) {
            $residentWorkQuery = str_replace('emergencyRemark', 'remark', $residentWorkQuery);
            $workArr = $this->commonWorkQuery('`remark`', $workArr, $count, $residentWorkQuery,$whereType);
        }
        //值班日志
        if (strpos($residentWorkQuery, 'descRemark')) {
            $residentWorkQuery = str_replace('descRemark', 'logs', $residentWorkQuery);
            $workArr = $this->commonWorkQuery('`logs`', $workArr, $count, $residentWorkQuery,$whereType);
        }
        //下一值班关注重点
        if (strpos($residentWorkQuery, 'warnLogs')) {
            $residentWorkQuery = str_replace('descRemark', 'warnLogs', $residentWorkQuery);
            $workArr = $this->commonWorkQuery('`warnLogs`', $workArr, $count, $residentWorkQuery,$whereType);
        }

        if($this->session->residentFirst != preg_replace("/\s+/","",$residentWorkQuery)){
            $residentWorkQuery = $this->buildSessionQuery($workArr);
        }
        return $residentWorkQuery;
    }


    /**
     * 搜索公用方法
     * @return mixed
     */
    public function commonDetailQuery($field, $workArr, $count, $residentWorkQuery,$whereType)
    {
        $num = 3;
        if($whereType == 'or'){
            $num = 1;
        }
        if ($count > $num) {
            $where = $this->whereQuery($field, $residentWorkQuery,$whereType);
            $dayIds = $this->getDayIdFromDetail($where);
        } else {
            $dayIds = $this->getDayIdFromDetail($residentWorkQuery);
        }
        $newArr = $this->intersection($workArr, $dayIds,$whereType);
        return $newArr;
    }

    /**
     * 搜索公用方法
     * @return mixed
     */
    public function commonDayQuery($field, $workArr, $count, $residentWorkQuery,$whereType)
    {
        $num = 3;
        if (in_array($field, ['`dutyDate`'])) {
            $num = 4;
        }
        if($whereType == 'or'){
            $num = 1;
        }
        if ($count > $num) {
            $where = $this->whereQuery($field, $residentWorkQuery,$whereType);
            $dayIds = $this->getDayIdFromDay($where);
        } else {
            $dayIds = $this->getDayIdFromDay($residentWorkQuery);
        }
        $newArr = $this->intersection($workArr, $dayIds,$whereType);
        return $newArr;
    }

    /**
     * 搜索公用方法
     * @return mixed
     */
    public function commonTemplateQuery($field, $workArr, $count, $residentWorkQuery,$whereType)
    {
        $num = 3;
        if($whereType == 'or'){
            $num = 1;
        }
        if ($count > $num) {
            $where = $this->whereQuery($field, $residentWorkQuery,$whereType);
            $dayIds = $this->getTemplateWhere($where);
        } else {
            $dayIds = $this->getTemplateWhere($residentWorkQuery);
        }
        $newArr = $this->intersection($workArr, $dayIds,$whereType);
        return $newArr;
    }

    /**
     * 搜索公用方法
     * @return mixed
     */
    public function commonWorkQuery($field, $workArr, $count, $residentWorkQuery,$whereType)
    {
        $num = 3;
        if (in_array($field, ['`createdDate`'])) {
            $num = 4;
        }
        if($whereType == 'or'){
            $num = 1;
        }
        if ($count > $num) {
            $where = $this->whereQuery($field, $residentWorkQuery,$whereType);
            $dayIds = $this->getWorkWhere($where);
        } else {
            $dayIds = $this->getWorkWhere($residentWorkQuery);
        }
        $newArr = $this->intersection($workArr, $dayIds,$whereType);
        return $newArr;
    }

    /**
     * 搜索公用方法
     * @return mixed
     */
    public function commonWorkDetailQuery($field, $workArr, $count, $residentWorkQuery,$whereType)
    {
        $num = 3;
        if (in_array($field, ['`createdDate`'])) {
            $num = 4;
        }
        if($whereType == 'or'){
            $num = 1;
        }
        if ($count > $num) {
            $where = $this->whereQuery($field, $residentWorkQuery,$whereType);
            $dayIds = $this->getWorkDetailWhere($where);
        } else {
            $dayIds = $this->getWorkDetailWhere($residentWorkQuery);
        }
        $newArr = $this->intersection($workArr, $dayIds,$whereType);
        return $newArr;
    }

    /**
     * where条件返回
     * @param $residentWorkQuery
     * @return string
     */
    public function whereQuery($field, $residentWorkQuery,$whereType)
    {
        $residentWorkQuery = str_replace('(', '', $residentWorkQuery);
        $residentWorkQuery = str_replace(')', '', $residentWorkQuery);
        $where = '1=1';
        if($whereType == 'and'){
            $arrQuery = explode('AND', $residentWorkQuery);
            foreach ($arrQuery as $query) {
                $strQuery = strstr($query, $field);
                if ($strQuery) {
                    $where = $strQuery;
                }
            }
        }
        if($whereType == 'or'){
            $arrQuery = explode('OR', $residentWorkQuery);
            foreach ($arrQuery as $query) {
                $strQuery = strstr($query, $field);
                if ($strQuery) {
                    $where = $strQuery;
                }
            }
        }
        return $where;
    }

    /**
     * 构造查询session
     * @param $workArr
     */
    public function buildSessionQuery($workArr)
    {
        $where = "id " . helper::dbIN($workArr);
        $session = $this->session->residentworkQuery;
        if(strpos($session,"IN (")){
            $array = explode('IN (',$session);
            $isEmpty = !empty($array[1]) ?? '';
            if($isEmpty){
                $this->session->set('residentworkQuery', $session);
            }
        }else{
            $this->session->set('residentworkQuery', $where);
        }
        return $this->session->residentworkQuery;
    }

    /**
     * 多个条件处理
     * @param $num1
     * @param $num2
     */
    public function intersection($workArr,$dayIds,$whereType)
    {
        if($whereType == 'and'){
            if(!empty($workArr)){
                $array = array_unique(array_intersect($workArr,$dayIds));
            }else{
                $array = $dayIds;
            }
        }
        if($whereType == 'or'){
            if(!empty($workArr)){
                $array = array_merge($workArr,$dayIds);
            }else{
                $array = $dayIds;
            }
        }
        return $array;
    }


    /**
     * 详情页
     * @param $dayId
     */
    public function view($dayId)
    {
        $data = $this->dao->select('*')->from(TABLE_RESIDENT_SUPPORT_DAY)->where('id')->eq($dayId)->andWhere('deleted')->eq('0')->orderBy('id_desc')->fetchAll();
        $info = $this->dataMake($data);
        return $info[0];
    }
    /**
     * 构造数据
     * @param array $data
     * @return array|mixed|void
     */
    public function dataMake($data=[])
    {
        foreach ($data as $day) {
            $usersInfo = $this->loadModel('user')->getUserInfoListByAccounts($day->dutyGroupLeader,'*');
            //值班组长所在部门
            $day->dutyGroupLeaderDept = $usersInfo[$day->dutyGroupLeader]->dept ?? '';
            //dayDetail
            $detailInfo = $this->getDetailInfoById($day->id);
            $day->detailInfo = [];
            if ($detailInfo) {
                $day->detailInfo = $detailInfo;
            }

            //work和workDetail 数据获取
            $workInfo = $this->getWorkByDayId($day->id);
            $day->workInfo = [];
            if ($workInfo) {
                $day->workInfo = $workInfo;
            }

            //template和templateDetail 数据获取
            $templateInfo = $this->getTemplateByTemId($day->templateId);
            $day->templateInfo = [];
            if ($templateInfo) {
                $day->templateInfo = $templateInfo;
            }
        }
        return $data;
    }

    /**
     * 获取详情信息
     * @param $dayId
     */
    public function getDetailInfoById($dayId)
    {
        $detail = $this->dao->select('*')->from(TABLE_RESIDENT_SUPPORT_DAY_DETAIL)->where('dayId')->eq($dayId)->andWhere('deleted')->eq(0)->fetchAll();
        $dutyUser     = [];
        $dutyUserDept = [];
        $postType     = [];
        $requireInfo  = [];
        $timeType     = [];
        $timeSlot     = [];
        if($detail){
            $countObj = count($detail);
            if($countObj > 1){
                foreach ($detail as $item){
                    $dutyUser[]     = $item->dutyUser;
                    $dutyUserDept[] = $item->dutyUserDept;
                    $postType[]     = $item->postType;
                    $requireInfo[]  = $item->requireInfo;
                    $timeType[]     = $item->timeType;
                    $timeSlot[]     = $item->startTime .'-'. $item->endTime;
                }
                $detailInfo = new stdClass();
                $detailInfo->dutyUser     = $this->implodeParams($dutyUser);
                $detailInfo->dutyUserDept = $this->implodeParams($dutyUserDept);
                $detailInfo->postType     = $this->implodeParams($postType);
                $detailInfo->requireInfo  = $this->implodeParams($requireInfo);
                $detailInfo->timeType     = $this->implodeParams($timeType);
                $detailInfo->timeSlot     = $this->implodeParams($timeSlot);
                $returnDetail = $detailInfo;
            }else{
                $detail[0]->timeSlot = $detail[0]->startTime .'-'. $detail[0]->endTime;
                $returnDetail = $detail[0];
            }
        }else{
            $returnDetail = [];
        }
        return $returnDetail;
    }

    public function implodeParams($params)
    {
        return implode(',', $params);
    }
    /**
     * 获取日志信息
     * @param $dayId
     */
    public function getWorkByDayId($dayId)
    {
        $workInfo = $this->dao->select('*')->from(TABLE_RESIDENT_SUPPORT_WORK)->where('dayId')->eq($dayId)->andWhere('deleted')->eq(0)->fetch();
        if($workInfo){
            $usersInfo = $this->loadModel('user')->getUserInfoListByAccounts($workInfo->groupLeader,'*');
            //实际值班组长所在部门
            $workInfo->realLeaderDeptId = $usersInfo[$workInfo->groupLeader]->dept ?? '';
            $workInfo->files = $this->loadModel('file')->getByObject('residentwork', $workInfo->id);
            $workDetailInfo = $this->dao->select('*')->from(TABLE_RESIDENT_SUPPORT_WORK_DETAIL)->where('workId')->eq($workInfo->id)->andWhere('deleted')->eq(0)->fetchAll();
            if($workDetailInfo){
                $realDutyUserArr = []; //实际值班人员
                $users = $this->loadModel('user')->getPairs('noclosed|noletter');
                foreach ($workDetailInfo as $detail){
                    $realDutyUserArr[] = $users[$detail->realDutyuser];
                }
                //实际值班人员
                $workInfo->realDutyUser = implode(',',$realDutyUserArr);
                $workInfo->workDetail = $workDetailInfo;
            }else{
                $workInfo->workDetai = [];
            }
        }else{
            $workInfo = [];
            $workInfo['realDutyUser'] = '';
            $workInfo['realLeaderDeptId'] = '';
        }
        return $workInfo;
    }

    /**
     * 获取模板信息
     * @param $dayId
     */
    public function getTemplateByTemId($templateId)
    {
        $templateInfo = $this->dao->select('*')->from(TABLE_RESIDENT_SUPPORT_TEMPLATE)->where('id')->eq($templateId)->andWhere('deleted')->eq(0)->fetch();
        if($templateInfo){
            $templateDetailInfo = $this->dao->select('*')->from(TABLE_RESIDENT_SUPPORT_TEMPLATE_DEPT)->where('templateId')->eq($templateInfo->id)->andWhere('deleted')->eq(0)->fetch();
            if($templateDetailInfo){
                $templateInfo->templateDetail =$templateDetailInfo;
            }else{
                $templateInfo->templateDetail = [];
            }
        }else{
            $templateInfo = [];
        }
        return $templateInfo;
    }

    /**
     * Judge button if can clickable.
     *
     * @param $templateDayInfo
     * @param $action
     * @return array|bool
     */
    public static function isClickable($templateDayInfo, $action)
    {
        global $app;
        $action = strtolower($action);
        $residentWorkModel = new residentworkModel();
        if($action == 'modifyscheduling'){ //变更排班
            $res =  $residentWorkModel->checkIsModifyScheduling($templateDayInfo);
            return $res['result'];
        }

        //记录日志
        if($action == 'recorddutylog'){
            $res =  $residentWorkModel->checkIsAllowRecordDutyLog($templateDayInfo);
            return $res['result'];
        }
        if($action == 'editlog'){ //编辑自建值班日志
            if ($templateDayInfo->createdBy == $app->user->account){
                return true;
            }else{
                return false;
            }
        }
        return true;
    }

    /**
     *检查是否允许变更排班
     *
     * @param $tempDayInfo
     * @return array
     */
    public function checkIsModifyScheduling($tempDayInfo){
        $this->loadModel('residentsupport');
        $this->app->loadLang('residentsupport');
        $this->app->loadConfig('residentsupport');
        $res = array(
            'result' => false,
            'message' => $this->lang->error->accessDenied,
        );
        if (!$tempDayInfo){
            $res['message'] = $this->lang->common->errorParamId;
            return $res;
        }

        $dayId = $tempDayInfo->id;
        $templateId = $tempDayInfo->templateId;
        $dutyDate = $tempDayInfo->dutyDate;
        //是否填写值班日志,如果填写值班日志以后不允许变更排班
        $workInfo = $this->getById($dayId);
        if($workInfo && $workInfo->isDraft == 1){
            $res['message'] = $this->lang->residentsupport->checkSchedulingResultList['workExistError'];
            return $res;
        }

        $dutyUserList = $this->residentsupport->getDutyUserListByDayId($dayId, 'dutyUserDept');
        if(empty($dutyUserList)){
            $res['message'] = $this->lang->residentsupport->checkSchedulingResultList['dutyUserDetailEmptyError'];
            return $res;
        }
        $deptIds = array_column($dutyUserList, 'dutyUserDept');
        $dutyDeptList = $this->residentsupport->getTemplateDeptListByTemAndDeptIds($templateId, $deptIds);
        if(empty($dutyDeptList)){
            $res['message'] = $this->lang->residentsupport->checkSchedulingResultList['dutyUserDetailEmptyError'];
            return $res;
        }
        $allowModifyDeptIds = [];
        foreach ($dutyDeptList as $val){
            $deptId   = $val->deptId;
            $isModify = $val->isModify;
            $status   = $val->status;
            if($isModify == $this->lang->residentsupport->temDeptModifyStatusList[2]){
                $allowModifyDeptIds[] = $deptId;
            }else{//正常审批流程
                if($status == $this->lang->residentsupport->temDeptStatusList['pass']){
                    $allowModifyDeptIds[] = $deptId;
                }
            }
        }
        //是否有允许变更的部门
        if(empty($allowModifyDeptIds)){
            $res['message'] = $this->lang->residentsupport->checkSchedulingResultList['deptStatusError'];
            return $res;
        }
//        $userType = $this->residentsupport->getModifySchedulingUserType();
//        if($userType == 2){ //二线用户
//            $res['result'] = true;
//            $res['message'] = '';
//        }else{
//            $schedulingIntervalDay = $this->config->residentsupport->schedulingIntervalDay;
//            $currentDay = Helper::today();
//            $diffDay = Helper::diffDate($dutyDate, $currentDay);
//            if ($diffDay >= $schedulingIntervalDay) {
//                $res['result'] = true;
//                $res['message'] = '';
//            }else{
//                $res['message'] = sprintf($this->lang->residentsupport->checkSchedulingResultList['schedulingIntervalDayError'], $schedulingIntervalDay);
//                return $res;
//            }
//        }
        //变更指定天以后的排班
        $schedulingIntervalDay = $this->config->residentsupport->schedulingIntervalDay;
        $currentDay = Helper::today();
        $diffDay = Helper::diffDate($dutyDate, $currentDay);
        if ($diffDay >= $schedulingIntervalDay) {
            $res['result'] = true;
            $res['message'] = '';
        }else{
            $res['message'] = sprintf($this->lang->residentsupport->checkSchedulingResultList['schedulingIntervalDayError'], $schedulingIntervalDay);
            return $res;
        }

        //如果允许变更排班
        if($res['result']){
            $data = ['allowModifyDeptIds' => $allowModifyDeptIds];
            $res['data'] = $data;
        }
        //返回
        return $res;
    }

    /**
     *检查是否允许记录日志
     *
     * @param $templateDayInfo
     * @return array
     */
    public function checkIsAllowRecordDutyLog($templateDayInfo){
        $res = array(
            'result' => false,
            'message' => $this->lang->error->accessDenied,
        );
        if (!$templateDayInfo){
            $res['message'] = $this->lang->common->errorParamId;
            return $res;
        }
        $templateId = $templateDayInfo->templateId;
        $dutyDate = $templateDayInfo->dutyDate;
        $currentDay = Helper::today();
        if($dutyDate > $currentDay){
            $res['message'] = $this->lang->residentwork->checkDutyLogResultList['dutyDateError'];
            return $res;
        }
        $dutyUserDeptIds = [];
        if(isset($templateDayInfo->detailInfo->dutyUserDept)){
            $dutyUserDeptIds = explode(',', $templateDayInfo->detailInfo->dutyUserDept);
        }
        //查询为审核通过的部门信息
        $unCheckPassDutyDeptList = $this->loadModel('residentsupport')->getUnCheckPassDutyDeptList($templateId, $dutyUserDeptIds, 'id');
        if(!empty($unCheckPassDutyDeptList)){
            $res['message'] = $this->lang->residentwork->checkDutyLogResultList['dutyDeptCheckError'];
            return $res;
        }
        $res['result'] = true;
        return $res;
    }
    //根据天id获取详情
    public function getById($dayId,$fileds = "*",$showFile='false',$loadType=1){
        if(!$dayId){
            return false;
        }
        if ($loadType == 1){
            $tableName = TABLE_RESIDENT_SUPPORT_WORK;
            $tableName2 = TABLE_RESIDENT_SUPPORT_WORK_DETAIL;
        }else{
            $tableName = TABLE_RESIDENT_SUPPORT_WORK_DRAFT;
            $tableName2 = TABLE_RESIDENT_SUPPORT_WORK_DETAIL_DRAFT;
        }
        $work = $this->dao->select($fileds)->from($tableName)->where('dayId')->eq($dayId)->andwhere("deleted")->eq(0)->fetch();
        $work = $this->loadModel('file')->processImgURL($work, $this->config->residentwork->editor->recorddutylog['id'], $this->post->uid);

        if (isset($work->id)){
            $workDetails = $this->dao->select('realDutyuser,realDutyuserDept,createdBy,createdDate,editedBy,editedDate')->from($tableName2)->where('workId')->eq($work->id)->andwhere("deleted")->eq(0)->fetchAll();
            $work->details = $workDetails;
            if($showFile) $work->files = $this->loadModel('file')->getByObject('residentwork', $work->id);
        }
        return $work;
    }

    //根据日志id获取详情
    public function getByWorkId($workId,$fileds = "*",$showFile='false'){
        if(!$workId){
            return false;
        }
        $work = $this->dao->select($fileds)->from(TABLE_RESIDENT_SUPPORT_WORK)->where('id')->eq($workId)->andwhere("deleted")->eq(0)->fetch();
        $work = $this->loadModel('file')->processImgURL($work, $this->config->residentwork->editor->recorddutylog['id'], $this->post->uid);
        if (isset($work->id)){
            $workDetails = $this->dao->select('realDutyuser,realDutyuserDept,createdBy,createdDate,editedBy,editedDate')->from(TABLE_RESIDENT_SUPPORT_WORK_DETAIL)->where('workId')->eq($work->id)->andwhere("deleted")->eq(0)->fetchAll();
            $work->details = $workDetails;
            if($showFile) $work->files = $this->loadModel('file')->getByObject('residentwork', $work->id);
        }
        return $work;
    }
    //保存值班日志
    public function recordDutyLog(){
        $result = array(
            'result'  => false,
            'message' => '',
        );
        $realDutyuser = $_POST['realDutyuser'];
        $isPush = $_POST['isPush']; //1推送值班日志 2不推送
        $isDraft = $_POST['isDraft'];
        $type = $_POST['type'];
        $data  = fixer::input('post')
            ->remove('type,subType,realDutyuser,uid,files,source,loadType,isPush')
            ->join('mailCtoUsers',',')
            ->stripTags($this->config->residentwork->editor->recorddutylog['id'], $this->config->allowedTags)
            ->get();
        if ($data->dateType == ''){
            $result['message'] = '请选择日期类型';
            return $result;
        }
        if ($data->area == ''){
            $result['message'] = '请选择值班地点';
            return $result;
        }
        if ($data->isEmergency == 1 && $data->remark == ''){
            $result['message'] = '请填写应急事件简要说明';
            return $result;
        }
        if ($data->logs == ''){
            $result['message'] = '请填写值班日志';
            return $result;
        }
        if ($data->warnLogs == ''){
            $result['message'] = '请填写下一值班重点关注';
            return $result;
        }
        if ($type == 1 && $data->analysis == ''){
            $result['message'] = '请填写支付交易系统运行质量日报分析';
            return $result;
        }
        $info = $this->dao->select("*")->from(TABLE_RESIDENT_SUPPORT_DAY_DETAIL)->where("dayId")->eq($data->dayId)->fetchall();

        $templateDayInfo = new stdClass();
        $templateDayInfo->id = $data->dayId;
        $templateDayInfo->templateId = $info[0]->templateId;
        $templateDayInfo->dutyDate = $data->dutyDate;
        $templateDayInfo->detailInfo = new stdClass();
        $templateDayInfo->detailInfo->dutyUserDept = implode(',',array_unique(array_column($info,'dutyUserDept')));
        $checkRes = $this->checkIsAllowRecordDutyLog($templateDayInfo);
        if (!$checkRes['result']){
            return $checkRes;
        }
        $data = $this->loadModel('file')->processImgURL($data, $this->config->residentwork->editor->recorddutylog['id'], $this->post->uid);
        $work = $this->getById($data->dayId,'*',false,$isDraft);
        $fileds = 'dept,account,realname';
        $users = $this->loadModel('user')->getListFiled($fileds);
        if ($isDraft == 1){
            $tableName = TABLE_RESIDENT_SUPPORT_WORK;
            $tableName2 = TABLE_RESIDENT_SUPPORT_WORK_DETAIL;
        }else{
            unset($data->isDraft);
            $tableName = TABLE_RESIDENT_SUPPORT_WORK_DRAFT;
            $tableName2 = TABLE_RESIDENT_SUPPORT_WORK_DETAIL_DRAFT;
        }
        //编辑日志
        if ($work){
            $workId  = $work->id;

            $data->editedBy = $this->app->user->account;
            $data->editedDate = date("Y-m-d H:i:s");
            $this->dao->update($tableName)->data($data)->where('id')->eq($workId)->exec();
            $del = new stdClass();
            $del->deleted = 1;
            $this->dao->update($tableName2)->data($del)->where('workId')->eq($workId)->exec();
            if(dao::isError()){
                $res['message'] = dao::getError();
                return $res;
            }
        }else{
            //新增日志
            $data->createdBy = $this->app->user->account;
            $data->createdDate = date("Y-m-d H:i:s");
            $data->editedBy = $this->app->user->account;
            $data->editedDate = date("Y-m-d H:i:s");
            $this->dao->insert($tableName)->data($data)->exec();
            if(dao::isError()){
                $res['message'] = dao::getError();
                return $res;
            }
            $workId = $this->dao->lastInsertID();
        }

        //保存文件
        $this->loadModel('file')->updateObjectID($this->post->uid, $workId, 'residentwork');
        $this->file->saveUpload('residentwork', $workId);
        //整理员工、部门信息
        foreach ($realDutyuser as $v){
            foreach ($users as $user) {
                if ($v == $user->account){
                    $details = new stdClass();
                    $details->workId = $workId;
                    $details->realDutyuser = $v;
                    $details->templateId = $data->templateId;
                    $details->realDutyuserDept = $user->dept;
                    $details->createdBy = $this->app->user->account;
                    $details->createdDate = date("Y-m-d H:i:s");
                    if ($work){
                        $details->editedBy = $this->app->user->account;
                        $details->editedDate = date("Y-m-d H:i:s");
                    }
                    $this->dao->insert($tableName2)->data($details)->exec();
                    if(dao::isError()){
                        $res['message'] = dao::getError();
                        return $res;
                    }
                }
            }
        }
        if ($isDraft == 1){
            //推送值班日志
            if ($isPush == 1 && $type == 1){
                $pushStatus = $this->pushDutyLog($workId);
                if ($pushStatus == 1){
                    $pushMsg = "日志推送成功";
                }else{
                    $pushMsg = "日志推送失败";
                }
            }

            //非支付类不记录
            if ($type != 1){
                $pushMsg = '';
            }
            if ($work){
                $changes = common::createChanges($work, $data);
                $actionID = $this->loadModel('action')->create('residentsupportday', $data->dayId, 'editedresidentwork', $pushMsg);
                $this->action->logHistory($actionID, $changes);
            }else{
                $this->loadModel('action')->create('residentsupportday', $data->dayId, 'createdresidentwork', $pushMsg);

            }

        }
        if ($isDraft == 1){
            $this->logSendMail($workId);
        }
        $result = ['result'=>'true'];
        return $result;
    }
    public function logSendMail($workId){
        if (!$workId){
            return false;
        }
        $this->loadModel('mail');
        $this->app->loadLang("custommail");
        $this->app->loadLang("residentsupport");
        $work = $this->getByWorkId($workId);
        $work->remark = $this->toHtml($work->remark);
        $work->logs = $this->toHtml($work->logs);
        $work->warnLogs = $this->toHtml($work->warnLogs);
        $work->analysis = $this->toHtml($work->analysis);
        /*
         *
         $day = $this->dao->select("t2.type")->from(TABLE_RESIDENT_SUPPORT_DAY)->alias("t1")
            ->leftjoin(TABLE_RESIDENT_SUPPORT_TEMPLATE)->alias("t2")
            ->on("t1.templateId=t2.id")
            ->where('t1.id')->eq($work->dayId)->fetch();
        */
        $users  = $this->loadModel('user')->getPairs('noletter');
        $outsideList1 = array('' => '') +$this->loadModel('user')->getUsersNameByType('outsideExpertType');
        $outsideList2 = array('' => '') +$this->loadModel('user')->getUsersNameByType('outside');
        $users = array_merge($users, $outsideList1, $outsideList2);

        $modulePath = $this->app->getModulePath($appName = '', 'residentwork');
        $viewFile   = $modulePath . 'view/logSendMail.html.php';
        chdir($modulePath . 'view');

        if(file_exists($modulePath . 'ext/view/logSendMail.html.php'))
        {
            $viewFile = $modulePath . 'ext/view/logSendMail.html.php';
            chdir($modulePath . 'ext/view');
        }
        $area = $this->lang->residentsupport->areaList;
        $type = $this->lang->residentsupport->typeList;
        $dateTypeList = $this->lang->residentsupport->dateTypeList;
        //实际值班人员
        $realDutyuser = array_unique(array_column($work->details,'realDutyuser'));
//        $mailTitle = "【通知】驻场支持值班日志#".$work->dayId.'_'.$work->dutyDate.$area[$work->area].$type[$work->type];
        $mailTitle = "【通知】驻场支持值班日志#".$work->dutyDate.$area[$work->area].$type[$work->type];
        ob_start();
        include $viewFile;
        foreach(glob($modulePath . 'ext/view/logSendMail.*.html.hook.php') as $hookFile) include $hookFile;
        $mailContent = ob_get_contents();
        ob_end_clean();
//        ob_end_flush();exit;
        array_push($realDutyuser,$work->groupLeader);

        $toList = implode(',',array_unique($realDutyuser));
        if(empty($toList)) return false;
        $this->mail->send($toList, $mailTitle, $mailContent,$work->mailCtoUsers);

        if($this->mail->isError()) trigger_error(join("\n", $this->mail->getError()));
    }
    public function toHtml($html){
        $html = str_replace("&lt;",'<',$html);
        $html = str_replace("&gt;",'>',$html);
        $html = str_replace("&quot;",'"',$html);
        $htmlLength = mb_strlen($html);
        if ($htmlLength > 332){
            $html = mb_substr($html,0,332,'utf-8').' '.mb_substr($html,332,$htmlLength - 332,'utf-8');
        }
        return $html;
    }

    /**
     * 获取template
     * @param $templateWhere
     */
    public function getTemplateWhere($templateWhere){
        $info = $this->dao->select('id')->from(TABLE_RESIDENT_SUPPORT_TEMPLATE)->where($templateWhere)->andWhere('deleted')->eq(0)->orderBy('id_desc')->fetchAll();
        $templateIds = [];
        $dayIds = [];
        if(!empty($info)){
            foreach ($info as $value){
                $templateIds[] = $value->id;
            }
            $dayInfo = $this->dao->select('id')->from(TABLE_RESIDENT_SUPPORT_DAY)->where('templateId')->in($templateIds)->andWhere('deleted')->eq(0)->orderBy('id_desc')->fetchAll();
            if(!empty($dayInfo)){
                foreach ($dayInfo as $value){
                    $dayIds[] = $value->id;
                }
            }
        }
        return $dayIds;
    }

    /**
     * 查询day_detail数据
     * @param $where
     */
    public function getDayIdFromDay($where)
    {
        $info = $this->dao->select('id')->from(TABLE_RESIDENT_SUPPORT_DAY)->where($where)->andWhere('deleted')->eq(0)->orderBy('id_desc')->fetchAll();
        $dayId = [];
        if (!empty($info)) {
            foreach ($info as $value) {
                $dayId[] = $value->id;
            }
        }
        return $dayId;
    }

    /**
     * 查询day_detail数据
     * @param $where
     */
    public function getDayIdFromDetail($where)
    {
        $info = $this->dao->select('id,dayId')->from(TABLE_RESIDENT_SUPPORT_DAY_DETAIL)->where($where)->andWhere('deleted')->eq(0)->orderBy('id_desc')->fetchAll();
        $dayId = [];
        if (!empty($info)) {
            foreach ($info as $value) {
                $dayId[] = $value->dayId;
            }
        }
        return $dayId;
    }

    /**
     * 获取residentWork数据
     * @param $workWhere
     */
    public function getWorkWhere($workWhere){
        $info = $this->dao->select('dayId')->from(TABLE_RESIDENT_SUPPORT_WORK)->where($workWhere)->andWhere('deleted')->eq(0)->orderBy('id_desc')->fetchAll();
        $dayId = [];
        if (!empty($info)) {
            foreach ($info as $value) {
                $dayId[] = $value->dayId;
            }
        }
        return $dayId;
    }

    /**
     * 获取residentWorkDetail数据
     * @param $workDetailWhere
     */
    public function getWorkDetailWhere($workDetailWhere)
    {
        $info = $this->dao->select('workId')->from(TABLE_RESIDENT_SUPPORT_WORK_DETAIL)->where($workDetailWhere)->andWhere('deleted')->eq(0)->orderBy('id_desc')->fetchAll();
        $workIds = [];
        $dayId = [];
        if (!empty($info)) {
            foreach ($info as $value) {
                $workIds[] = $value->workId;
            }
            $workInfo = $this->dao->select('dayId')->from(TABLE_RESIDENT_SUPPORT_WORK)->where('id')->in($workIds)->andWhere('deleted')->eq(0)->orderBy('id_desc')->fetchAll();
            foreach ($workInfo as $value){
                $dayId[] = $value->dayId;
            }
        }
        return $dayId;
    }

    /**
     * 推送值班日志
     */
    public function pushDutyLog($workId){
        $users  = $this->loadModel('user')->getPairs('noletter');
        $outsideList1 = array('' => '') +$this->loadModel('user')->getUsersNameByType('outsideExpertType');
        $outsideList2 = array('' => '') +$this->loadModel('user')->getUsersNameByType('outside');
        $users = array_merge($users, $outsideList1, $outsideList2);
        $work = $this->getByWorkId($workId);
        $dutyUser = array_column($work->details,'realDutyuser');
        $userStr = "";
        foreach ($dutyUser as $item) {
            $userStr .= $users[$item].',';
        }
        $userStr = rtrim($userStr,',');
        $data = array(
            'dailyAnalysis' => html_entity_decode(strip_tags(str_replace("<br />","\n",$work->analysis))),
            'dailyRecord' => html_entity_decode(strip_tags(str_replace("<br />","\n",$work->logs))),
            'dutyDate' => $work->dutyDate,
            'dutyUser' => $userStr,
            'isExistEmergencyEvent' => $work->isEmergency,
            'emergencyEventDescription' => html_entity_decode(strip_tags(str_replace("<br />","\n",$work->remark))),
            'nextDutyFocus' => html_entity_decode(strip_tags(str_replace("<br />","\n",$work->warnLogs))),
        );
        $data['isExistEmergencyEvent'] = $data['isExistEmergencyEvent'] == 2 ? 0 : $data['isExistEmergencyEvent'];
        $data['emergencyEventDescription'] = $data['isExistEmergencyEvent'] == 1 ? $data['emergencyEventDescription'] : '无';
        $data['emergencyEventDescription'] = $data['emergencyEventDescription'] == '' ? '无' : $data['emergencyEventDescription'];
        $data['nextDutyFocus'] = $data['nextDutyFocus'] == '' ? '无' : $data['nextDutyFocus'];
        $url = $this->config->global->dutyLogPushUrl;
        $headers = array();
        $headers[] = 'App-Id: ' . $this->config->global->pushDutyLogAppId;
        $headers[] = 'App-Secret: ' . $this->config->global->pushDutyLogAppSecret;
        $pushData = $data;
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
        }
        $this->dao->update(TABLE_RESIDENT_SUPPORT_WORK)->data($params)->where('id')->eq($workId)->exec();
        $this->requestlog->saveRequestLog($url, "residentwork", "推送值班日志", 'POST', $pushData, $result, $status, '');
        return $pushStatus;
    }
}
