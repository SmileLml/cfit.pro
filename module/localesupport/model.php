<?php

class localesupportModel extends model
{
    public function buildSearchForm($queryID, $actionURL)
    {
        $this->config->localesupport->search['actionURL'] = $actionURL;
        $this->config->localesupport->search['queryID'] = $queryID;
        $this->loadModel('search')->setSearchParams($this->config->localesupport->search);
    }

    /**
     * 获得列表
     *
     * @param $browseType
     * @param $queryID
     * @param $orderBy
     * @param null $pager
     * @return array
     */
    public function getList($browseType, $queryID, $orderBy, $pager = null)
    {
        $data = [];
        $localesupportQuery = '';
        if ($browseType == 'bysearch') {
            $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : '';
            if ($query) {
                $this->session->set('localesupportQuery', $query->sql);
                $this->session->set('localesupportForm', $query->form);
            }

            if ($this->session->localesupportQuery == false) $this->session->set('localesupportQuery', ' 1 = 1');

            $localesupportQuery = $this->session->localesupportQuery;
//            支持部门
            $a="deptIds";
            $res=preg_replace_callback('/`deptIds.*?(\d+)\%\'/',function ($matches) use($a){
                $departId=intval($matches[1]);
                return "find_in_set($departId,$a)";
            },$localesupportQuery);
            $localesupportQuery=$res;

//司局
            $b="sj";
            $res=preg_replace_callback('/`sj.*%(\d+)\%\'/',function ($matches1) use($b){
                $sjId=intval($matches1[1]);
                return <<<EOF
            $b REGEXP '^\\\{.*?"[\^"]+":"$sjId"[,\\\}].*$'
EOF;
            },$localesupportQuery);
            $localesupportQuery=$res;


            $res=preg_replace_callback('/`sj`\s+=\s+\'(\d+)/',function ($matches1) use($b){
                $sjId=intval($matches1[1]);
                return <<<EOF
            $b REGEXP '^\\\{("[\^"]+":"$sjId"\[,\\\}])+$
EOF;
            },$localesupportQuery);
            $localesupportQuery=$res;
//        承建单位
            $c="owndept";
            $res=preg_replace_callback('/`owndept`\s+=\s+\'(\w+)/',function ($matches1) use($c){
                $owndeptId=$matches1[1];
                return <<<EOF
            $c REGEXP '^\\\{("[\^"]+":"$owndeptId"\[,\\\}])+$
EOF;
            },$localesupportQuery);
            $localesupportQuery=$res;
        }
        $account = $this->app->user->account;
        $allowReportWorkStatusArray = "'" . implode("','", $this->lang->localesupport->allowReportWorkStatusArray) . "'";
        //查询列表
        $ret = $this->dao->select('*')
            ->from(TABLE_LOCALESUPPORT)
            ->where('deleted')->eq('0')
            ->beginIF($browseType != 'all' && $browseType != 'bysearch' && $browseType != 'tomedeal')
            ->andWhere('status')->eq($browseType)
            ->fi()
            ->beginIF($browseType == 'tomedeal')
            ->andWhere("(FIND_IN_SET('{$account}', dealUsers) OR (status in (" . $allowReportWorkStatusArray . ") and  FIND_IN_SET('{$account}', supportUsers)))")
            ->fi()
            ->beginIF($browseType == 'bysearch')
            ->andWhere($localesupportQuery)
            ->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');
//        $this->dao->printSql();
        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'localesupport', $browseType != 'bysearch');
        if ($ret) {
            $ids = array_column($ret, 'id');
            $consumedList = $this->getConsumedList($ids);
            foreach ($ret as $k => &$val) {
                $val =  $this->loadModel('file')->replaceImgURL($val, $this->config->localesupport->editor->create['id']);
                $supportId = $val->id;
                if (isset($consumedList[$supportId])) {
                    $val->consumedTotal = $consumedList[$supportId];
                } else {
                    $val->consumedTotal = 0;
                }
            }
            $data = $ret;
        }
        return $data;
    }

    /**
     * 获得工时列表
     *
     * @param $supportIds
     * @return array
     */

    public function getConsumedList($supportIds)
    {
        $data = [];
        if (!$supportIds) {
            return $data;
        }
        $ret = $this->dao->select('supportId, sum(consumed) as consumed')
            ->from(TABLE_LOCALESUPPORT_WORKREPORT)
            ->where('deleted')->eq('0')
            ->andWhere('supportId')->in($supportIds)
            ->groupBy('supportId')
            ->fetchPairs('supportId');
        if ($ret) {
            $data = $ret;
        }
        return $data;
    }

    /**
     * 通过id获得基本信息
     *
     * @param $id
     * @param string $select
     * @return mixed
     */
    public function getBasicInfoById($id, $select = '*')
    {
        if (!$id) {
            return false;
        }
        $ret = $this->dao->select($select)
            ->from(TABLE_LOCALESUPPORT)
            ->where('deleted')->eq('0')
            ->andWhere('id')->eq($id)
            ->fetch();
        return $ret;
    }


    /**
     * 查询现场支持和现场支持报工详情
     *
     * @param $id
     * @return bool
     */
    public function getById($id)
    {
        $ret = $this->dao->select('local.*,work.supportDate,work.deptId,work.supportUser as account,work.consumed')
            ->from(TABLE_LOCALESUPPORT)->alias('local')
            ->leftJoin(TABLE_LOCALESUPPORT_WORKREPORT)->alias('work')
            ->on('local.id = work.supportId')
            ->where('local.deleted')->eq('0')
            // ->andWhere('work.deleted')->eq('0')
            ->andWhere('local.id')->eq($id)
            ->fetch();
        if (!$ret) {
            return false;
        }
        $ret = $this->loadModel('file')->replaceImgURL($ret, $this->config->localesupport->editor->create['id']);
        $objectType = $this->config->localesupport->objectType;
        $ret->files = $this->loadModel('file')->getByObject($objectType, $ret->id);
        //报工信息
        $workReportList = $this->getWorkReportListBySupportId($id);
        $ret->workReportList = $workReportList;
        return $ret;
    }

    /**
     * 获得列表
     *
     * @param $ids
     * @param string $select
     * @param $exWhere
     * @return array
     */
    public function getListByIds($ids, $select = '*', $exWhere = '')
    {
        $data = [];
        if (!$ids) {
            return $data;
        }
        $ret = $this->dao->select($select)
            ->from(TABLE_LOCALESUPPORT)
            ->where('deleted')->eq('0')
            ->andWhere('id')->in($ids)
            ->beginIF($exWhere)->andWhere($exWhere)->fi()
            ->fetchAll();
        if ($ret) {
            $data = $ret;
        }
        return $data;
    }

    /**
     * 获得符合提交的所有列表列表
     *
     * @param $ids
     * @param string $select
     * @param $exWhere
     * @return array
     */
    public function getAllListByIds($ids, $select = '*', $exWhere = '')
    {
        $data = [];
        if (!$ids) {
            return $data;
        }
        $ret = $this->dao->select($select)
            ->from(TABLE_LOCALESUPPORT)
            ->where('id')->in($ids)
            ->beginIF($exWhere)->andWhere($exWhere)->fi()
            ->fetchAll();
        if ($ret) {
            $data = $ret;
        }
        return $data;
    }

    /**
     * 获得报工列表
     *
     * @param $supportId
     * @return array
     */
    public function getWorkReportListBySupportId($supportId)
    {
        $data = [];
        if (!$supportId) {
            return $data;
        }

        $ret = $this->dao->select('*')
            ->from(TABLE_LOCALESUPPORT_WORKREPORT)
            ->where('deleted')->eq('0')
            ->andWhere('supportId')->eq($supportId)
            ->orderBy('supportUser')
            ->fetchAll();
        if ($ret) {
            $data = $ret;
        }
        return $data;
    }

    /**
     * 获得报工列表
     *
     * @param $supportIds
     * @return array
     */
    public function getWorkreportList($supportIds){
        $data = [];
        if(!$supportIds){
            return $data;
        }
        $ret = $this->dao->select('supportId,supportDate,deptId,supportUser,sum(consumed) as consumed')
            ->from(TABLE_LOCALESUPPORT_WORKREPORT)
            ->where('deleted')->eq('0')
            ->andWhere('supportId')->in($supportIds)
            ->groupBy('supportId,supportUser,supportDate')
            ->orderBy('supportId_desc')
            ->fetchAll();
        if ($ret) {
            $data = $ret;
        }
        return $data;
    }

    /**
     * 获得任务id
     *
     * @param $supportId
     * @return array
     */
    public function getTaskIds($supportId){
        $data = [];
        if (!$supportId) {
            return $data;
        }

        $ret = $this->dao->select('distinct taskId  as taskId ')
            ->from(TABLE_LOCALESUPPORT_WORKREPORT)
            ->where('deleted')->eq('0')
            ->andWhere('supportId')->eq($supportId)
            ->andWhere('taskId')->ne(0)
            ->fetchAll();
        if ($ret) {
            $data = array_column($ret, 'taskId');
        }
        return $data;

    }

    /**
     * 获得报工人员
     *
     * @param $supportId
     * @return array
     */
    public function getWorkReportUsersBySupportId($supportId){
        $data = [];
        if(!($supportId)){
            return $data;
        }
        $select = "distinct supportUser as  supportUser";
        $ret = $this->dao->select($select)
            ->from(TABLE_LOCALESUPPORT_WORKREPORT)
            ->where('deleted')->eq('0')
            ->andWhere('supportId')->eq($supportId)
            ->fetchAll();
        if ($ret) {
            $data = array_column($ret, 'supportUser');
        }
        return $data;
    }

//根据支持人员姓名分组数据
    function array_val_chunk($array)
    {
        $result = array();
        $ar2 = [];
        foreach ($array as $k => $v) {

            foreach ($array as $k1 => $v1) {
                if ($v->supportUser == $v1->supportUser) {
                    $ar2[] = $v1;
                }
            }
            $result[$v->supportUser]=$ar2;
            $ar2=[];
        }
        return $result;
    }

    /**
     * 根据现场支持id 查询需要生成的任务条信息
     *
     * @param $supportId
     * @return bool
     */
    public function getWorkReportBySupportIdToTask($supportId)
    {
        $ret = $this->dao->select('workreport.supportId,workreport.deptId,date(support.startDate) startDate,support.stype,workreport.execution,support.createdBy,support.startDate,support.endDate,support.code')
            ->from(TABLE_LOCALESUPPORT_WORKREPORT)->alias('workreport')
            ->leftJoin(TABLE_LOCALESUPPORT)->alias('support')
            ->on('workreport.supportId = support.id')
            ->where('workreport.deleted')->eq('0')
            ->andWhere('support.deleted')->eq('0')
            ->andWhere('workreport.supportId')->eq($supportId)
            ->groupBy('workreport.deptId')
            ->fetchAll();
        if (!$ret) {
            return false;
        }
        return $ret;
    }

    /**
     * 获得格式化报工数据
     *
     * @param $workReportData
     * @return array
     */
    public function getFormatWorkReportData($workReportData)
    {
        $data = [];
        if (!$workReportData) {
            return $data;
        }
        foreach ($workReportData as $val) {
            $supportUser = $val->supportUser;
            $data[$supportUser][] = $val;
        }
        return $data;
    }


    /**
     * 获得审核人信息
     *
     * @param int $deptId
     * @return array
     */
    public function getReviewNodeUserList($deptId = 0)
    {
        $data = [];
        return $data;
    }

    /**
     * 创建报工
     *
     * @return array
     */
    public function create()
    {
        $isWarn = $_POST['isWarn']; //是否需要发出警告信息
        $issubmit = $_POST['issubmit']; //提交还是保存
        $postData = fixer::input('post')
            ->remove('uid,files,isWarn,issubmit')
            ->stripTags($this->config->localesupport->editor->create['id'], $this->config->allowedTags)
            ->get();
        $postData = $this->getFormatPostData($postData);
        if($issubmit == 'submit' ){ //提交需要验证
            //检查基本信息
            $res = $this->checkSubmitInfo($postData, 'create');
            if (!$res['checkRes']) {
                dao::$errors = $res['errorData'];
                return dao::$errors;
            }

    //        //是否有警告信息
    //        if($isWarn == 'yes'){
    //            $warnData =  $this->checkPostParamsWarnInfo($postData);
    //            if($warnData){
    //                dao::$warns = $warnData;
    //                return dao::$warns;
    //            }
    //        }
        }
        $workReportList = $postData->workReportList;
        unset($postData->workReportList);
        $currentUser = $this->app->user->account;
        $status = $this->lang->localesupport->statusArray['waitsubmit'];//待提交
        $dealUsers = $currentUser;
        $postData->createdBy   = $currentUser;
        $postData->createdDept = $this->app->user->dept;
        $postData->createdTime = helper::now();
        $postData->status    = $status;
        $postData->dealUsers = $dealUsers;
        $code = $this->getCode();
        $postData->code = $code;
        $postData = $this->loadModel('file')->processImgURL($postData, $this->config->localesupport->editor->create['id'], $this->post->uid);

        $this->dao->begin();
        $this->dao->insert(TABLE_LOCALESUPPORT)->data($postData)->batchCheckIF($issubmit != 'save',$this->config->localesupport->create->requiredFields, 'notempty')->exec();
        $recordId = $this->dao->lastInsertId();
        if (!dao::isError()) {
            if($workReportList){
                $res = $this->updateReportWork($recordId, $workReportList);
            }
            $objectType = $this->config->localesupport->objectType;
            //图片、附件信息
            $this->loadModel('file')->saveUpload($objectType, $recordId);
            $this->file->updateObjectID($this->post->uid, $recordId, $objectType);

            //日志
            $this->loadModel('action')->create($objectType, $recordId, 'created');
            if($issubmit == 'submit'){ //提交
                if($issubmit == 'submit'){
                    $res = $this->submit($recordId, 'create');
                }
            }else{
                $this->loadModel('consumed')->record($objectType, $recordId, 0, $currentUser, '', $status, array());
            }
            //回滚
            if(dao::isError()){
                $this->dao->rollback();
                return false;
            }
        }
        //提交
        $this->dao->commit();
        return $recordId;
    }

    /**
     * 编辑现场报工
     *
     * @return array
     */
    public function update($localesupportId)
    {
        $op = 'edit';
        $account = $this->app->user->account;
        $info = $this->getById($localesupportId);
        //检查是否允许更新
        $res = $this->checkIsAllowEdit($info, $account);
        if (!$res['result']) {
            dao::$errors[] = $res['message'];
            return false;
        }
        //是否需要发出警告信息
        $isWarn = $_POST['isWarn'];
        $issubmit = $_POST['issubmit']; //提交还是保存
        $postData = fixer::input('post')
            ->remove('uid,files,isWarn,issubmit')
            ->stripTags($this->config->localesupport->editor->edit['id'], $this->config->allowedTags)
            ->get();
        $postData = $this->getFormatPostData($postData, $op, $info);
        if($issubmit == 'submit' ) { //提交需要验证
            //检查基本信息
            $res = $this->checkSubmitInfo($postData, $op, $info);
            if (!$res['checkRes']) {
                dao::$errors = $res['errorData'];
                return dao::$errors;
            }

    //        //是否有警告信息
    //        if($isWarn == 'yes'){
    //            $warnData =  $this->checkPostParamsWarnInfo($postData);
    //            if($warnData){
    //                dao::$warns = $warnData;
    //                return dao::$warns;
    //            }
    //        }
        }

        //编辑信息
        $postData = $this->loadModel('file')->processImgURL($postData, $this->config->localesupport->editor->$op['id'], $this->post->uid);
        
        $workReportList = $postData->workReportList;
        unset($postData->workReportList);
        //编辑
         $this->dao->begin();

         $this->dao->update(TABLE_LOCALESUPPORT)
            ->data($postData)->batchCheckIF($issubmit != 'save',$this->config->localesupport->edit->requiredFields, 'notempty')->where('id')->eq($localesupportId)
            ->exec();
        if (!dao::isError()){
            //修改报工信息
            $oldWorkReportList = $info->workReportList;
            $res = $this->updateReportWork($localesupportId, $workReportList, $oldWorkReportList);
            //状态流转
            $objectType = $this->config->localesupport->objectType;
            //图片、附件信息
            $this->loadModel('file')->saveUpload($objectType, $localesupportId);
            $this->file->updateObjectID($this->post->uid, $localesupportId, $objectType);

            $changes = common::createChanges($info, $postData);
            $actionID = $this->loadModel('action')->create($objectType, $localesupportId, 'edited');
            if($changes) {
                $this->action->logHistory($actionID, $changes);
            }
            if($issubmit == 'submit'){
                $res = $this->submit($localesupportId, 'update');
            }
            //回滚
            if(dao::isError()){
                $this->dao->rollback();
                return false;
            }
        }
        //提交
        $this->dao->commit();
         return true;
    }


    /**
     * 获得格式话post数据
     *
     * @param $postData
     * @param $op
     * @param null $oldInfo
     * @return mixed
     */
    public function getFormatPostData($postData, $op = 'create', $oldInfo = null)
    {
        $requiredFields = explode(',', $this->config->localesupport->$op->requiredFields);
        foreach ($requiredFields as $requiredField) {
            if (!isset($postData->$requiredField)) {
                $postData->$requiredField = '';
            }
        }
        foreach ($this->config->localesupport->multipleSelectFields as $multipleSelectField) {
            if (isset($postData->$multipleSelectField) && !empty($postData->$multipleSelectField)) {
                if (is_array($postData->$multipleSelectField)) {
                    $postData->$multipleSelectField = implode($postData->$multipleSelectField, ',');
                }
                $postData->$multipleSelectField = trim($postData->$multipleSelectField, ',');
            } else {
                $postData->$multipleSelectField = '';
            }
        }

        //获得承建单位和业务司局
        $owndept = '';
        $sj      = '';
        $isGetOwnDept = true;
        if (isset($oldInfo) && !empty($oldInfo)) {
            if ($postData->appIds == $oldInfo->appIds) {
                $isGetOwnDept = false;
                $owndept = $oldInfo->owndept;
                $sj     = $oldInfo->sj;
            }
        }

        if ($isGetOwnDept && !empty($postData->appIds)) {
            $appList = $this->loadModel('application')->getAppListByIds($postData->appIds, 'id,team,fromUnit');
            if (!empty($appList)) {
                $ownDeptList = [];
                $sjList = [];
                foreach ($appList as $val) {
                    $ownDeptList[$val->id] = isset($val->team) ?  $val->team:'';
                    $sjList[$val->id] = isset($val->fromUnit) ? $val->fromUnit: '';
                }
                $owndept = json_encode($ownDeptList);
                $sj = json_encode($sjList);
            }
        }
        $postData->owndept = $owndept;
        $postData->sj = $sj;

        //是否需要获得部门负责人按照分组展示
        $isGetDeptManagersGroup = true;
        if(isset($oldInfo) && !empty($oldInfo)){
            if(($postData->deptManagers == $oldInfo->deptManagers) && ($postData->deptIds == $oldInfo->deptIds)){
                $postData->deptManagersGroup = $oldInfo->deptManagersGroup;
                $isGetDeptManagersGroup = false;

            }
        }

        if($isGetDeptManagersGroup){
            //部门负责人按照部门分组
            $deptManagersGroup = $this->getDeptManagersGroup($postData->deptManagers, $postData->deptIds);
            $postData->deptManagersGroup = json_encode($deptManagersGroup);
        }
        $workReportList = [];
        if(isset($postData->supportUser)){
            $supportUserArray  = $postData->supportUser;
            $supportDateArray  = $postData->supportDate;
            $consumedArray     = $postData->consumed;
            $workReportIdArray = isset($postData->workReportId) ? $postData->workReportId: [];
            foreach ($supportUserArray as $key => $supportUser){
                $supportDate = $supportDateArray[$key];
                $consumed    = $consumedArray[$key];
                $workReportId = isset($workReportIdArray[$key]) ? $workReportIdArray[$key] : 0;
                if(empty($supportUser) && empty($supportDate) && empty($consumed)){
                    continue;
                }
                $tempParams = new stdClass();
                $tempParams->rowNum      = $key + 1;
                $tempParams->supportUser = $supportUser;
                $tempParams->supportDate = $supportDate;
                $tempParams->consumed    = $consumed;
                $tempParams->workReportId = $workReportId;
                $workReportList[] = $tempParams;
            }
            unset($postData->supportUser);
        }
        if(isset($postData->supportDate)){
            unset($postData->supportDate);
        }
        if(isset($postData->consumed)){
            unset( $postData->consumed);
        }
        if(isset($postData->workReportId)){
            unset($postData->workReportId);
        }

        //报工数据
        $postData->workReportList = $workReportList;
        return $postData;
    }

    /**
     * 获得部门负责人分组
     *
     * @param $deptManagers
     * @param $deptIds
     * @return array
     */
    public function getDeptManagersGroup($deptManagers, $deptIds){
        $data = [];
        if(!($deptManagers && $deptIds)){
            return $data;
        }
        if(!is_array($deptManagers)){
            $deptManagers = explode(',', $deptManagers);
        }
        if(!is_array($deptIds)){
            $deptIds = explode(',', $deptIds);
        }
        $deptList =  $this->loadModel('dept')->getDeptListByIds($deptIds, 'id,manager');
        if(empty($deptList)){
            return $data;
        }
        $deptManagerList = array_column($deptList, 'manager', 'id');
        foreach ($deptIds as $deptId){
            $currentDeptManagers = zget($deptManagerList, $deptId, '');
            $currentDeptManagers = explode(',', $currentDeptManagers);
            $tempUser = array_intersect($currentDeptManagers, $deptManagers);
            if(!empty($tempUser)){
                sort($tempUser);
            }
            $data[$deptId] = $tempUser;
        }
        return $data;

    }

    /**
     * 检查提交参数信息
     *
     * @param $params
     * @param string $op
     * @param null $oldInfo
     * @return array
     */
    public function checkPostParamsInfo($params, $op = 'create', $oldInfo = null){
        //检查结果
        $checkRes = false;
        $errorData = [];
        $data = [
            'checkRes'  => $checkRes,
            'errorData' => $errorData,
        ];

        if($params->area == 1){
            $this->config->localesupport->create->requiredFields.=',jxdepart,sysper';
        }
        $requiredFields = explode(',', $this->config->localesupport->create->requiredFields);
        $textFields = array('jxdepart', 'sysper');
        foreach ($requiredFields as $requiredField){
            if(!isset($params->$requiredField) || $params->$requiredField == ''){
                $errorData[$requiredField] = sprintf($this->lang->error->notempty, $this->lang->localesupport->$requiredField);
            }else{
                if(in_array($requiredField, $textFields) && (trim($params->$requiredField) == '')){
                    $errorData[$requiredField] = sprintf($this->lang->error->notempty, $this->lang->localesupport->$requiredField);
                }
            }
        }

        //时间验证
        if(!$this->loadModel('common')->checkJkDateTime($params->startDate)){
            $errorData['startDate'] = sprintf($this->lang->localesupport->objectFormatError, $this->lang->localesupport->startDate);
        }
        if(!$this->loadModel('common')->checkJkDateTime($params->endDate)){
            $errorData['endDate'] = sprintf($this->lang->localesupport->objectFormatError, $this->lang->localesupport->endDate);
        }
        if($params->endDate <= $params->startDate){
            $errorData['endDate'] = $this->lang->localesupport->endDateLessError;
        }
        $today = helper::today();
        $tempStartDate = substr($params->startDate, 0, 10);
        $tempEndDate   = substr($params->endDate, 0, 10);
        //校验时间和当前时间做比较
        if($tempStartDate > $today){
            $errorData['startDate'] = sprintf($this->lang->localesupport->dateMoreTodayError, $this->lang->localesupport->startDate);
        }

        //是否时间校验（n天填报上个月限制）
        $isCheckStartDate = $this->getIsCheckStartDate($oldInfo);
        if($isCheckStartDate){
            $currentMonthFirstDay = Helper::currentMonthFirstDay();
            if($params->startDate < $currentMonthFirstDay){ //开始时间当月第一天
                $reportWorkLimitDay = $this->config->localesupport->reportWorkLimitDay;
                $currentDay  = Helper::today();
                $lastMonthEndDay = Helper::lastMonthEndDay();
                $deadlineDay = helper::getTrueWorkDay($lastMonthEndDay, $reportWorkLimitDay, true);
                if($deadlineDay < $currentDay){
                    $errorData['startDate'] = sprintf($this->lang->localesupport->startDateDeadlineLimitError, $reportWorkLimitDay);
                }
            }
        }
        /**
         *按时不校验承建单位，业务司局
         *
         //系统承建单位，业务司局
        $appIds = explode(',', $params->appIds);
        $appList =  $this->loadModel('application')->getapplicationNameCodePairs(0, $appIds);
        $ownDeptList = json_decode($params->owndept, true);
        $sjList      = json_decode($params->sj, true);

        //承建单位和业务司局检查
        $appErrorData = [];
        foreach ($appIds as $appId){
            $appName = zget($appList, $appId);
            if(!isset($ownDeptList[$appId]) || !$ownDeptList[$appId]){
                $appErrorData[$appId] = $appName;
            }
            if(!isset($sjList[$appId]) || !$sjList[$appId]){
                $appErrorData[$appId] = $appName;
            }
        }

        if(!empty($appErrorData)){
            $ownDeptErrorData = sprintf($this->lang->localesupport->appOwndeptSjError,implode(',', $appErrorData));
            $errorData['appIds'] = $ownDeptErrorData;
        }
        */

        //部门负责人限制
        $deptManagersGroup = json_decode($params->deptManagersGroup, true);
        $deptIds = explode(',', $params->deptIds);
        $deptNameList = $this->loadModel('dept')->getDeptListByIds($deptIds, 'id,name');
        if($deptNameList){
            $deptNameList = array_column($deptNameList, 'name', 'id');
        }
        $deptManagersErrorData = [];
        foreach ($deptIds as $deptId){
            if(!isset($deptManagersGroup[$deptId]) || empty($deptManagersGroup[$deptId])){
                $deptName = zget($deptNameList, $deptId);
                $deptManagersErrorData[] = sprintf($this->lang->localesupport->deptManagersError, $deptName);
            }
        }
        if(!empty($deptManagersErrorData)){
            $deptManagersErrorData = implode(',', $deptManagersErrorData);
            $errorData['deptManagers'] = $deptManagersErrorData;
        }

        //富文本
        $searchParam = ['&nbsp;',' '];
        $replaceParam = ['', ''];
        $tempReason = str_replace($searchParam, $replaceParam, strip_tags($params->reason, '<img>'));
        if($tempReason == ''){
            $errorData['reason'] = sprintf($this->lang->error->notempty, $this->lang->localesupport->reason);
        }
        //报工信息校验
        $workReportList = $params->workReportList;
        $checkRes = $this->checkReportWorkParams($params, $workReportList);
        if(!$checkRes['checkRes']){
            $errorData = $errorData + $checkRes['errorData'];
        }


        if($errorData){
            $data['errorData'] = $errorData;
        }else{
            $checkRes = true;
            $data['checkRes'] = $checkRes;
        }
        return $data;
    }

    /**
     * 检查预警信息
     *
     * @param $params
     * @return array
     */
    public function checkPostParamsWarnInfo($params){
        $data = [];
        $deptIds = explode(',', $params->deptIds);
        $deptNameList = $this->loadModel('dept')->getDeptListByIds($deptIds, 'id,name');
        if($deptNameList){
            $deptNameList = array_column($deptNameList, 'name', 'id');
        }

        $supportUserDeptIds = [];
        $supportUsers = $params->supportUsers;
        $supportUsersList = $this->loadModel('user')->getUserListGroupDept($supportUsers);
        if($supportUsersList){
            $supportUserDeptIds = array_keys($supportUsersList);
        }
        $diffDeptIds = array_diff($deptIds, $supportUserDeptIds);
        if(!empty($diffDeptIds)){
            $noSupportUserDeptList = [];
            foreach ($diffDeptIds as $deptId){
                $deptName = zget($deptNameList, $deptId);
                $noSupportUserDeptList[] = $deptName;
            }
            $noSupportUserDeptStr = implode('、', $noSupportUserDeptList);
            $data['supportUsers'] = sprintf($this->lang->localesupport->warnSupportUsers, $noSupportUserDeptStr);
        }
        return $data;
    }

    /**
     * 是否允许报工
     *
     * @param $info
     * @param $account
     * @return array
     */
    public function checkIsAllowReportWork($info, $account){
        $res = array(
            'result'  => false,
            'message' => '',
        );
        if(!$info){
            $res['message'] = $this->lang->common->errorParamId;
            return $res;
        }
        //当前状态
        $status = $info->status;
        if(!in_array($status, $this->lang->localesupport->allowReportWorkStatusArray)){
            $statusDesc = zget($this->lang->localesupport->statusList, $status);
            $res['message'] = sprintf($this->lang->localesupport->checkOpResultList['statusError'], $statusDesc, $this->lang->localesupport->reportWork);
            return $res;
        }

        $allowUsers = ['admin', $info->createdBy];
        //if($info->isUserSelfReportWork == 1){ //允许支持人员报工
            $supportUsers  = array_filter(explode(',', $info->supportUsers));
            $allowUsers = array_merge($allowUsers, $supportUsers);
        //}
        if(!in_array($account, $allowUsers)){
            $res['message'] = sprintf($this->lang->localesupport->checkOpResultList['userError'], $this->lang->localesupport->reportWork);
            return $res;
        }
        $res['result'] = true;
        return $res;
    }

    /**
     * 是否允许报工
     *
     * @param $info
     * @param $account
     * @return array
     */
    public function checkIsAllowEdit($info, $account){
        $res = array(
            'result'  => false,
            'message' => '',
        );
        if(!$info){
            $res['message'] = $this->lang->common->errorParamId;
            return $res;
        }
        //当前状态
        $status = $info->status;
        if(!in_array($status, $this->lang->localesupport->allowEditStatusArray)){
            $statusDesc = zget($this->lang->localesupport->statusList, $status);
            $res['message'] = sprintf($this->lang->localesupport->checkOpResultList['statusError'], $statusDesc, $this->lang->localesupport->edit);
            return $res;
        }

        $allowUsers = ['admin', $info->createdBy];
        if(!in_array($account, $allowUsers)){
            $res['message'] = sprintf($this->lang->localesupport->checkOpResultList['userError'], $this->lang->localesupport->edit);
            return $res;
        }
        $res['result'] = true;
        return $res;
    }

    /**
     * 是否允许提交
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
        if(!$info){
            $res['message'] = $this->lang->common->errorParamId;
            return $res;
        }
        //当前状态
        $status = $info->status;
        if(!in_array($status, $this->lang->localesupport->allowSubmitStatusArray)){
            $statusDesc = zget($this->lang->localesupport->statusList, $status);
            $res['message'] = sprintf($this->lang->localesupport->checkOpResultList['statusError'], $statusDesc, $this->lang->localesupport->submit);
            return $res;
        }

        $allowUsers = ['admin', $info->createdBy];
        if(!in_array($account, $allowUsers)){
            $res['message'] = sprintf($this->lang->localesupport->checkOpResultList['userError'], $this->lang->localesupport->submit);
            return $res;
        }
        if((isset($info->workReportList) && empty($info->workReportList)) || (isset($info->consumedTotal) && $info->consumedTotal == 0) ){
            $res['message'] = $this->lang->localesupport->workReportEmpty;
            return $res;
        }
        $res['result'] = true;
        return $res;
    }

    /**
     * 是否允许审批
     *
     * @param $info
     * @param $account
     * @return array
     */
    public function checkIsAllowReview($info, $account){
        $res = array(
            'result'  => false,
            'message' => '',
        );
        if(!$info){
            $res['message'] = $this->lang->common->errorParamId;
            return $res;
        }

        $allowUsers = ['admin'];
        //当前状态
        $status = $info->status;
        if(!in_array($status, $this->lang->localesupport->allowReviewStatusArray)){
            $statusDesc = zget($this->lang->localesupport->statusList, $status);
            $res['message'] = sprintf($this->lang->localesupport->checkOpResultList['statusError'], $statusDesc, $this->lang->localesupport->review);
            return $res;
        }
        $dealUsers = $info->dealUsers;
        $dealUsers = explode(',', $dealUsers);
        $allowUsers = array_merge($allowUsers, $dealUsers);
        if(!in_array($account, $allowUsers)){
            $res['message'] = sprintf($this->lang->localesupport->checkOpResultList['userError'], $this->lang->localesupport->review);
            return $res;
        }
        $res['result'] = true;
        return $res;
    }

    /**
     * 是否允许批量删除
     *
     * @param $localesupportIds
     * @param $account
     * @return array
     */
    public function checkIsAllowBatchReview($localesupportIds, $account){

        $res = array(
            'result'  => false,
            'message' => '',
        );
        if(!$localesupportIds){
            $res['message'] = $this->lang->common->errorParamId;
            return $res;
        }
        if(!is_array($localesupportIds)){
            $localesupportIds = explode(',', $localesupportIds);
        }
        $data = $this->getListByIds($localesupportIds);
        if(empty($data)){
            $res['message'] = $this->lang->common->errorParamId;
            return $res;
        }
        $data = array_column($data, null, 'id');
        $errorData = [];
        foreach ($localesupportIds as $localesupportId){
            $info = zget($data, $localesupportId, new stdClass());
            $tempRes = $this->checkIsAllowReview($info, $account);
            if(!$tempRes['result']){
                $errorData[] = sprintf($this->lang->localesupport->checkOpResultList['idError'], $localesupportId) . $tempRes['message'];
            }
        }

        if(!empty($errorData)){
            $res['message'] = implode(';', $errorData);
        }else{
            $res['result'] = true;
        }
        return $res;
    }

    /**
     *是否允许删除
     *
     * @param $info
     * @param $account
     * @return array
     */

    public function checkIsAllowDelete($info, $account){
        $res = array(
            'result'  => false,
            'message' => '',
        );
        if(!$info){
            $res['message'] = $this->lang->common->errorParamId;
            return $res;
        }
        $allowUsers = ['admin'];
        //当前状态
        $status = $info->status;
        if(!in_array($status, $this->lang->localesupport->allowDeleteStatusArray)){
            $statusDesc = zget($this->lang->localesupport->statusList, $status);
            $res['message'] = sprintf($this->lang->localesupport->checkOpResultList['statusError'], $statusDesc, $this->lang->localesupport->delete);
            return $res;
        }
        $dealUsers = $info->dealUsers;
        $dealUsers = explode(',', $dealUsers);
        $allowUsers = array_merge($allowUsers, $dealUsers);
        if(!in_array($account, $allowUsers)){
            $res['message'] = sprintf($this->lang->localesupport->checkOpResultList['userError'], $this->lang->localesupport->delete);
            return $res;
        }
        $res['result'] = true;
        return $res;
    }
    //删除操作
    public function deleteData($localesupportId)
    {
        $data = new stdClass();
        $data->deleted  = 1;
        $res = $this->dao->update(TABLE_LOCALESUPPORT)->data($data)->where('id')->eq($localesupportId)->exec();
        return $res;
    }


    /**
     * 检查是否可以给所有用户报工
     * @param $info
     * @param $account
     * @return bool
     */
    public function isAllReportWork($info, $account){
        $isAllReportWork = false;
        $allowUsers = ['admin', $info->createdBy];
        if(in_array($account, $allowUsers)){
            $isAllReportWork = true;
        }
        return $isAllReportWork;
    }

    /**
     * 报工
     *
     * @return mixed
     */
    public function reportWork(){
        $account = $this->app->user->account;
        $postData = fixer::input('post')
            ->remove('uid')
            ->stripTags($this->config->localesupport->editor->create['id'], $this->config->allowedTags)
            ->get();
        $localesupportId = $postData->localesupportId;
        $info = $this->getById($localesupportId);
        //检查是否允许报工
        $res = $this->checkIsAllowReportWork($info, $account);
        if(!$res['result']){
            return dao::$errors[''] = $res['message'];
        }
        //是否允许报全部
        $isAllReportWork = $this->isAllReportWork($info, $account);
        $postData = $this->getFormatReportWorPostData($postData, $isAllReportWork, $account);
        //检查报工参数信息
        $res = $this->checkReportWorkParams($info, $postData);
        if(!$res['checkRes']){
            dao::$errors = $res['errorData'];
            return dao::$errors;
        }
        //提交信息
        $workReportList = $info->workReportList;
        if($isAllReportWork){
            $oldWorkReportList = $workReportList;
        }else{ //只允许填报自己的
            foreach ($workReportList as $val){
                if($val->supportUser == $account){
                    $oldWorkReportList[] = $val;
                }
            }
        }
        $res = $this->updateReportWork($localesupportId, $postData, $oldWorkReportList);
        if(!$res['isChange']){
            return dao::$errors[''] = $res['message'];
        }
        $changes = $res['changeData'];
        return $changes;
    }

    /**
     * 更新报工信息
     *
     * @param $localesupportId
     * @param array $postData
     * @param array $oldWorkReportList
     * @return array
     */
    public function updateReportWork($localesupportId, $postData = [], $oldWorkReportList = []){
        //返回信息
        $isChange = false;
        $message  = $this->lang->localesupport->checkOpResultList['noInfoChangeError'];;
        $changeData = [];
        $res = [
           'isChange'    => $isChange,
            'message'    => $message,
            'changeData' => $changeData
        ];
        //没有报工
        if(empty($postData) && empty($oldWorkReportList)){
            return $res;
        }
        $addData    = []; //新增
        $updateData = []; //更新
        $delIds     = []; //删除
        $account = $this->app->user->account;

        //旧的报工
        $oldWorkReportIds = [];
        if(!empty($oldWorkReportList)){
            $oldWorkReportList = array_column($oldWorkReportList, null, 'id');
            $oldWorkReportIds = array_keys($oldWorkReportList);
        }
        if(empty($postData)){ //没有新的报工数据
            $delIds = $oldWorkReportIds;
        }else{
            $supportUserArray = array_column($postData, 'supportUser');
            $userList = $this->loadModel('user')->getUserInfoListByAccounts($supportUserArray, 'account,realname,dept');
            $workReportIds = [];
            foreach ($postData as $val){
                $workReportId = 0;
                if(isset($val->rowNum)){
                    unset($val->rowNum);
                }
                if(isset($val->workReportId)){
                    $workReportId = $val->workReportId;
                    unset($val->workReportId);
                }
                $supportUser = $val->supportUser;
                $userInfo = zget($userList, $supportUser);
                $val->supportId = $localesupportId;

                if($workReportId){ //更新
                    $workReportIds[] = $workReportId;
                    $oldWorkReportInfo = zget($oldWorkReportList, $workReportId);
                    if(($oldWorkReportInfo->supportDate == $val->supportDate) && ($oldWorkReportInfo->consumed == $val->consumed)){
                        continue;
                    }
                    $val->editedBy   = $account;
                    $val->editedDate =  helper::now();
                    $updateData[$workReportId] = $val; //更新信息
                }else{ //新增
                    $val->deptId    = zget($userInfo, 'dept');
                    $val->createdBy = $account;
                    $val->createdDate = helper::now();
                    $addData[] = $val;
                }
            }
            $delIds = array_diff($oldWorkReportIds, $workReportIds);
        }
        //没有信息更新
        if(empty($addData) && empty($updateData) && empty($delIds)){
            return $res;
        }

        $changes = [];
        //删除
        if(!empty($delIds)){
            $updateParams = new stdClass();
            $updateParams->deleted = 1;
            $this->dao->update(TABLE_LOCALESUPPORT_WORKREPORT)->data($updateParams)->autoCheck()
                ->where('id')->in($delIds)
                ->exec();
            $changeData['delIds'] = $delIds;
        }
        //更新
        if(!empty($updateData)){
            foreach ($updateData as $workReportId => $val){
                $this->dao->update(TABLE_LOCALESUPPORT_WORKREPORT)->data($val)->autoCheck()
                    ->where('id')->eq($workReportId)
                    ->exec();
            }
            $changeData['updateData'] = $updateData;
        }
        //新增
        if($addData){
            foreach ($addData as $val){
                $this->dao->insert(TABLE_LOCALESUPPORT_WORKREPORT)
                    ->data($val)
                    ->exec();
            }
            $changeData['addData'] = $addData;
        }
        //返回
        $isChange = true;
        $res['isChange'] = $isChange;
        $res['changeData'] = $changeData;
        return $res;
    }

    /**
     * 获得格式化报工数据
     *
     * @param $postData
     * @param $isAllReportWork
     * @param $account
     * @return array
     */
    public function getFormatReportWorPostData($postData, $isAllReportWork, $account){
        $paramsData = [];
        if(!$postData){
            return $paramsData;
        }
        $supportUserList  = $postData->supportUser;
        $workReportIdList = $postData->workReportId;
        $supportDateList  = $postData->supportDate;
        $consumedList     = $postData->consumed;
        foreach ($supportUserList as $key => $supportUser){

            if($isAllReportWork || $supportUser == $account){
                $tempParams = new stdClass();
                $tempParams->workReportId = zget($workReportIdList, $key);
                $tempParams->supportUser  = $supportUser;
                $tempParams->supportDate  = zget($supportDateList, $key);
                $tempParams->consumed     = zget($consumedList, $key);
                $tempParams->rowNum       = $key + 1;
                if($isAllReportWork){ //创建人或者admin
                    if(!$tempParams->supportDate &&  $tempParams->consumed == ''){ //没有设置
                        continue;
                    }
                }
                $paramsData[] = $tempParams;
            }
        }
        return $paramsData;
    }

    /**
     * 检查报工数据是否合法
     *
     * @param $localeSupportInfo
     * @param $postData
     * @return array
     */
    public function checkReportWorkParams($localeSupportInfo, $postData){
        $checkRes   = false;
        $errorData  = [];
        $data = [
            'checkRes'  => $checkRes,
            'errorData' => $errorData,
        ];
        if(!($localeSupportInfo)){
            $errorData[] =  $this->lang->common->errorParamId;
            $data['errorData'] = $errorData;
            return $data;
        }
        if(!($postData)){
            $errorData[] =  $this->lang->localesupport->workReportEmpty ;
            $data['errorData'] = $errorData;
            return $data;
        }
        $startDate    = substr($localeSupportInfo->startDate, 0, 10);
        $endDate      = substr($localeSupportInfo->endDate, 0, 10);
        $supportUsers = explode(',', $localeSupportInfo->supportUsers); //支持人员
        $consumedList = [];
        $users = $this->loadModel('user')->getUserInfoListByAccounts($supportUsers, 'account,realname');
        if($users){
            $users = array_column($users, 'realname', 'account');
        }
        $pattern = '/^\d+(\.\d)?$/';
        $today = helper::today();
        foreach ($postData as $key => $val){
            $rowNum = isset($val->rowNum) ? $val->rowNum: $key + 1;
            $supportUser = $val->supportUser;
            $supportDate = $val->supportDate;
            $consumed    = $val->consumed;
            $userName = zget($users, $supportUser);
            //人员检查
            if(!in_array($supportUser, $supportUsers)){
                $errorData[] =  sprintf($this->lang->localesupport->checkOpResultList['supportUserError'], $rowNum);
                $data['errorData'] = $errorData;
                return $data;
            }
            //日期检查
            if(!$supportDate){
                $errorData[] =  sprintf($this->lang->localesupport->checkOpResultList['workReportFieldEmpty'], $rowNum, $this->lang->localesupport->supportDate);
                $data['errorData'] = $errorData;
                return $data;
            }
            if($supportDate > $today){
                $errorData[] =  sprintf($this->lang->localesupport->checkOpResultList['supportDateMoreTodayError'], $rowNum, $this->lang->localesupport->supportDate);
                $data['errorData'] = $errorData;
                return $data;
            }

            if(!$this->loadModel('common')->checkJkDateTime($supportDate)){
                $errorData[] =  sprintf($this->lang->localesupport->checkOpResultList['workReportFieldError'], $rowNum, $this->lang->localesupport->supportDate);
                $data['errorData'] = $errorData;
                return $data;
            }

            if(($supportDate < $startDate) || ($supportDate > $endDate) ){
                $errorData[] =  sprintf($this->lang->localesupport->checkOpResultList['supportDateError'], $rowNum);
                $data['errorData'] = $errorData;
                return $data;
            }

            //工时检查
            if(!$consumed){
                $errorData[] =  sprintf($this->lang->localesupport->checkOpResultList['workReportFieldEmpty'], $rowNum, $this->lang->localesupport->consumed);
                $data['errorData'] = $errorData;
                return $data;
            }
            //工时格式检查
            if(!(preg_match($pattern, $consumed) && $consumed <= 14)){
                $errorData[] = sprintf($this->lang->localesupport->checkOpResultList['consumedError'], $rowNum);
                $data['errorData'] = $errorData;
                return $data;
            }
            //该人该天工时是否大于14检查
            if(isset($consumedList[$supportUser][$supportDate])){
                $consumedList[$supportUser][$supportDate] += $consumed;
            }else{
                $consumedList[$supportUser][$supportDate] = $consumed;
            }
            //工时是都大于14
            $reportConsumed = $consumedList[$supportUser][$supportDate];
            $ignoreId =  isset($val->workReportId) ? $val->workReportId: 0;
            $ignoreType = 'localesupport';
            $localeSupportId = isset($localeSupportInfo->id) ?  $localeSupportInfo->id:  0;
            $checkConsumed = $this->loadModel('workreport')->checkEffort($reportConsumed, $supportDate, $ignoreId, $ignoreType, $supportUser, $localeSupportId);
            if($checkConsumed){
                $errorData[] = sprintf($this->lang->localesupport->checkOpResultList['consumedOverError'], $userName, $supportDate);
                $data['errorData'] = $errorData;
                return $data;
            }
        }
        //返回
        if(empty($errorData)){
            $checkRes = true;
            $data['checkRes'] = $checkRes;
        }
        return $data;
    }

    /**
     * 提交操作
     *
     * @param $localesupportId
     * @param $source
     * @return array|bool
     */
    public function submit($localesupportId, $source = 'submit'){
        $account = $this->app->user->account;
        $info = $this->getByID($localesupportId);
        //检查是否允许提交
        $res = $this->checkIsAllowSubmit($info, $account);
        if(!$res['result']){
            dao::$errors[] = $res['message'];
            return false;
        }
        //校验基本信息
        if($source == 'submit'){ //单独提交需求验证，新增和编辑的提交已经验证
            $res = $this->checkSubmitInfo($info, $source, $info);
            if(!$res['checkRes']){
                dao::$errors = $res['errorData'];
                return dao::$errors;
            }
        }
        $version = $info->version;
        if(in_array($info->status, $this->lang->localesupport->needUpdateVersionStatusArray)){
            $version = $info->version + 1;
        }

        /**
         * 工作流代码暂时不用，等会签高级功能开发以后再使用
         *
        $dealResult = 1;
        $dealMessage  = $this->post->comment;
        $reviewerInfo = $this->getReviewerInfo($info);
         *
        $processInstanceId = $info->workflowId ? $info->workflowId: '';
        $res = $this->saveWorkFlow($info, $reviewerInfo, $version);
        if($res){
            $newInfo = $this->getBasicInfoById($localesupportId);
            $processInstanceId = $newInfo->workflowId ? $newInfo->workflowId:'';
        }
        $userVariableList = new stdClass();
        $res = $this->loadModel('iwfp')->completeTaskWithClaim_V2($processInstanceId, $account, $dealMessage, $dealResult, $userVariableList, $version);
        if(dao::isError()) {
            return $res;
        }

        //更新表已经提交
        $updateParams = new stdClass();
        $nextStatus = $res->toXmlTask;
        $workFlowNextUsers = is_array($res->dealUser) ? implode(',', $res->dealUser):$res->dealUser;
        $nextUsers = $this->getNextUsers($workFlowNextUsers, $nextStatus, $info->deptManagersGroup);
        */

        $nextStatus = $this->lang->localesupport->statusArray['waitdept']; //待部门负责人审批
        $nextUsers  = $info->deptManagers; //待处理人是部门负责人
        $updateParams = new stdClass();
        $updateParams->status   = $nextStatus;
        $updateParams->dealUsers = $nextUsers;
        $updateParams->version = $version;
        $this->dao->update(TABLE_LOCALESUPPORT)->data($updateParams)->autoCheck()
            ->where('id')->eq($localesupportId)
            ->exec();
        if(dao::isError()) {
            return dao::getError();
        }
        $objectType = $this->config->localesupport->objectType;
        if($source == 'create'){
            $status = '';
        }else{
            $status =  $info->status;
        }
        $this->loadModel('consumed')->record($objectType, $localesupportId, '0', $account, $status, $nextStatus);

        //增加审核节点
        $this->addReviewNode($info, $version, $nextStatus);


        //返回
        $changes = common::createChanges($info, $updateParams);
        $actionID = $this->loadModel('action')->create($objectType, $localesupportId, 'submited', $this->post->comment);
        if($changes) {
            $this->action->logHistory($actionID, $changes);
        }
        return true;
    }

    /**
     *设置交付单号
     * @param $createTime
     * @return string
     */
    public function getCode($createTime = ''){
        if(!$createTime){
            $createTime = strtotime(helper::today());
        }
        $codePrefix = 'CFIT-S-';
        $createDay = date('Ymd-', $createTime);
        $codeTemp = $codePrefix.$createDay;
        $number = $this->dao->select('count(id) c')->from(TABLE_LOCALESUPPORT)->where('code')->like($codeTemp."%")->fetch('c') ;
        $number = intval($number) + 1;
        $code   = $codeTemp . sprintf('%02d', $number);
        return $code;
    }

    /**
     *添加审核节点
     *
     * @param $info
     * @param $nextStatus
     * @param $version
     * @return bool
     */
    public function addReviewNode($info, $version, $nextStatus){
        if(!($info && $version && $nextStatus)){
            return false;
        }
        if($nextStatus == $this->lang->localesupport->statusArray['waitdept']){ //添加审核节点
            $objectType = $this->config->localesupport->objectType;
            $localesupportId = $info->id;
            $reviewers = $this->setFormatReviewers($info->deptManagersGroup);
            $reviewStatus = 'pending';
            $stage = 1;
            $nodeCode = $this->lang->localesupport->nodeCodeList[$nextStatus];
            $extParams = [
                'nodeCode' => $nodeCode,
            ];
            $this->loadModel('review')->addNode($objectType, $localesupportId, $version, $reviewers, true, $reviewStatus, $stage, $extParams);
        }
        return true;
    }

    /**
     * 设置格式化审核人信息
     *
     * @param $deptManagersGroup
     * @return array
     */
    public function setFormatReviewers($deptManagersGroup){
        $data = [];
        if(!$deptManagersGroup){
            return $data;
        }
        if(!is_array($deptManagersGroup)){
            $deptManagersGroup = json_decode($deptManagersGroup, true);
        }
        foreach ($deptManagersGroup as $deptId => $deptManagers){
            $tempData = [];
            foreach ($deptManagers as $account){
                $temp = new stdClass();
                $temp->reviewer = $account;
                $temp->parentId = $deptId; //按照部门分组
                $tempData[] = $temp;
            }
            $data[] = $tempData;
        }
        return $data;
    }

    /**
     * 评审
     *
     * @param $localesupportId
     * @return array|bool
     */
    public function review($localesupportId){
        $account = $this->app->user->account;
        $info = $this->getByID($localesupportId);
        //检查是否允许提交
        $res = $this->checkIsAllowReview($info, $account);
        if(!$res['result']){
            dao::$errors[] = $res['message'];
            return false;
        }
        //提交参数
        $postData = fixer::input('post')
            ->stripTags($this->config->localesupport->editor->review['id'], $this->config->allowedTags)
            ->get();
        $res = $this->checkReviewParams($postData);
        if(!$res['checkRes']){
            dao::$errors = $res['errorData'];
            return false;
        }
        $dealResult = $postData->dealResult;
        $comment = isset($_POST['comment'])? $_POST['comment'] :'';
        $dealUser = $this->app->user->account;

        /**
         * 工作流审批暂时屏蔽
         *
        $deptId   = $this->app->user->dept;
        $userVariableList = new stdClass();
        $res = $this->loadModel('iwfp')->completeTaskWithClaim_V2($info->workflowId, $deptId, $comment, $dealResult, $userVariableList, $info->version);
        if(dao::isError()){
            return false;
        }
        $oldStatus = $info->status;
        $nextStatus = $res->toXmlTask;
        $isEnd = $res->isEnd;
        $nextStatus = $this->getNextStatus($nextStatus, $isEnd);
        //下一状态处理人
        $nextDealUsers =  is_array($res->dealUser) ? implode(',', $res->dealUser): $res->dealUser;
        $nextDealUsers = $this->getNextUsers($nextDealUsers, $nextStatus, $info->deptManagersGroup);
        */

        //状态流转
        $parentIds = [];
        $oldStatus = $info->status;
        $objectType = $this->config->localesupport->objectType;
        $version = $info->version;
        $nodeCode = $this->lang->localesupport->nodeCodeList[$oldStatus];
        $statusArray = ['pending'];
        $exWhere = " reviewer = '{$account}'";
        $nodeId = 0;
        if($account != 'admin'){
            $unReviewList = $this->loadModel('review')->getReviewerListByNodeCode($objectType, $localesupportId, $version, $nodeCode, $statusArray, $exWhere);
            if(empty($unReviewList)){
                dao::$errors[] = sprintf($this->lang->localesupport->checkOpResultList['userError'], $this->lang->localesupport->review);
                return false;
            }
            $nodeId = $unReviewList[0]->node;
            $parentIds = array_flip(array_flip(array_column($unReviewList, 'parentId'))); //分组id
        }
        $result = $this->loadModel('review')->check($objectType, $localesupportId, $version, $dealResult, $comment);
        if(!$result){
            dao::$errors[] = $this->lang->localesupport->checkOpResultList['opError'];
            return false;
        }
        if($result == 'part'){ //部分审核
            //设置忽略节点
            if($nodeId && $parentIds){
                $exWhere = " status = 'pending' AND parentId in (".implode(',',  $parentIds).")";
                $ret = $this->loadModel('review')->setReviewersIgnore($nodeId, $exWhere);
            }
        }
        //更新
        $nextReviewInfo = $this->getNextReviewInfo($info, $result);
        $nextStatus    = $nextReviewInfo['nextStatus'];
        $nextDealUsers = $nextReviewInfo['nextDealUsers'];
        $updateData = new stdClass();
        $updateData->status    = $nextStatus;
        $updateData->dealUsers = $nextDealUsers;
        //修改主表
        $this->dao->update(TABLE_LOCALESUPPORT)->data($updateData)->where('id')->eq($localesupportId)->exec();

        //同步需求任务
        if($nextStatus == $this->lang->localesupport->statusArray['pass'] && ($info->isOld == 1)){
            $this->createStageAndTask($localesupportId);
            //设置已经同步出去
            $ret = $this->setWorkReportDataSync($localesupportId);
        }

        $this->loadModel('consumed')->record($objectType, $localesupportId, '0', $dealUser, $oldStatus, $nextStatus);
        $changes = common::createChanges($info, $updateData);
        //记录日志
        $objectType = $this->config->localesupport->objectType;
        $actionID = $this->loadModel('action')->create($objectType, $localesupportId, 'reviewed', $this->post->comment);
        if($changes) {
            $this->action->logHistory($actionID, $changes);
        }
        return true;
    }

    /**
     * 获取下一步审核人信息
     *
     * @param $info
     * @param $result
     * @return array
     */
    public function getNextReviewInfo($info, $result){
        $nextStatus    = '';
        $nextDealUsers = '';
        $data = [
            'nextStatus' => $nextStatus,
            'nextDealUsers' => $nextDealUsers,
        ];
        if(!($info && $result)){
            return $data;
        }
        if($result == 'reject'){
            $nextStatus = $this->lang->localesupport->statusArray['reject'];
            $nextDealUsers = $info->createdBy;
        }elseif ($result == 'pass'){ //审核结束
            $nextStatus = $this->lang->localesupport->statusArray['pass'];
            $nextDealUsers = '';
        }else{ //获取未审核人
            $localesupportId = $info->id;
            $oldStatus = $info->status;
            $objectType = $this->config->localesupport->objectType;
            $nodeCode = $this->lang->localesupport->nodeCodeList[$oldStatus];
            $statusArray = ['pending'];
            $unReviewList = $this->loadModel('review')->getReviewerListByNodeCode($objectType, $localesupportId, $info->version, $nodeCode, $statusArray);
            if(empty($unReviewList)){ //没有审核人
                $nextStatus = $this->lang->localesupport->statusArray['pass'];
                $nextDealUsers = '';
            }else{
                $nextStatus = $oldStatus;
                $reviewers = array_column($unReviewList, 'reviewer');
                $reviewers = array_flip(array_flip($reviewers));
                $nextDealUsers = implode(',', $reviewers);
            }
        }
        $data['nextStatus'] = $nextStatus;
        $data['nextDealUsers'] = $nextDealUsers;
        return $data;
    }


    /**
     * 校验评审信息
     *
     * @param $postData
     * @return array
     */
    public function checkReviewParams($postData){
        $checkRes   = false;
        $errorData  = [];
        $data = [
            'checkRes'  => $checkRes,
            'errorData' => $errorData,
        ];
        $dealResult = $postData->dealResult;
        $comment = isset($postData->comment) ? $postData->comment :'';
        if(!$dealResult){
            $errorData['dealResult'] = sprintf($this->lang->error->notempty, $this->lang->localesupport->dealResult);
        }
        if($dealResult == 'reject'){ //审批不通过
            if($comment == ''){
                $errorData['comment'] = $this->lang->localesupport->reviewCommentEmpty;
            }
        }
        if(!empty($errorData)){
            $checkRes = false;
        }else{
            $checkRes = true;
        }
        $data['checkRes'] = $checkRes;
        $data['errorData'] = $errorData;
        return $data;
    }

    /**
     * 批量审批
     *
     * @param $localesupportIds
     * @return bool
     */
    public function batchReview($localesupportIds){
        $account = $this->app->user->account;
        if(!is_array($localesupportIds)){
            $localesupportIds = explode(',', $localesupportIds);
        }
        //是否允许批量删除
        $res = $this->checkIsAllowBatchReview($localesupportIds, $account);
        if(!$res['result']){
            dao::$errors[] = $res['message'];
            return false;
        }
        //单个操作审批
        $postData = fixer::input('post')
            ->stripTags($this->config->localesupport->editor->review['id'], $this->config->allowedTags)
            ->get();
        $res = $this->checkReviewParams($postData);
        if(!$res['checkRes']){
            dao::$errors = $res['errorData'];
            return false;
        }
        //重新获得
        $postData = $_POST;
        $errorData  = [];
        $errorIds   = [];
        $successIds = [];
        foreach ($localesupportIds as $localesupportId){
            $this->post->set('dealResult', $postData['dealResult']);
            $this->post->set('comment', $postData['comment']);
            $this->post->set('uid', $postData['uid']);
            $res = $this->review($localesupportId);
            if(!$res){
                $errorIds[] = $localesupportId;
                $errorData[] = sprintf($this->lang->localesupport->checkOpResultList['idReviewError'], $localesupportId) . reset(dao::$errors);
                dao::$errors = []; //重新赋值，否则数据库操作停止
            }else{
                $successIds[] = $localesupportId;
            }
        }
        if(!empty($errorData)){
            dao::$errors = $errorData;
            return dao::$errors;
        }
        return true;
    }

    /**
     * 设置报工状态同步
     *
     * @param $localesupportId
     * @return bool
     */
    public function setWorkReportDataSync($localesupportId){
        $res = false;
        if(!$localesupportId){
            return $res;
        }
        $updateData = new stdClass();
        $updateData->syncStatus = 2;
        $res = $this->dao->update(TABLE_LOCALESUPPORT_WORKREPORT)->data($updateData)->where('supportId')->eq($localesupportId)->andWhere('deleted')->eq(0)->exec();
        return $res;
    }

    /**
     * 获得下一状态
     *
     * @param $workFlowNextStatus
     * @param $isEnd
     * @return mixed
     */
    public function getNextStatus($workFlowNextStatus, $isEnd = 0){
        $nextStatus = $workFlowNextStatus;
        if($isEnd == '1' && !$workFlowNextStatus){
            $nextStatus = $this->lang->localesupport->statusArray['pass'];
        }
        return $nextStatus;
    }

    /**
     * 获得下一节点待处理人
     *
     * @param $workFlowNextUsers
     * @param $nextStatus
     * @param $deptManagersGroup
     * @return string
     */
    public function getNextUsers($workFlowNextUsers, $nextStatus, $deptManagersGroup){
        $nextUsers = '';
        if(!$workFlowNextUsers){
            return $nextUsers;
        }
        $nextUsers = $workFlowNextUsers;
        if($nextStatus == $this->lang->localesupport->statusArray['waitdept']){ //部门负责人审批
            $deptManagersGroup = json_decode($deptManagersGroup, true);
            $workFlowNextUsers = explode(',', $workFlowNextUsers);
            $tempUserArray = [];
            foreach ($deptManagersGroup as $deptId => $currentDeptUsers){
                if(in_array($deptId, $workFlowNextUsers)){
                    $tempUserArray = array_merge($tempUserArray, $currentDeptUsers);
                }
            }
            $nextUsers = implode(',', $tempUserArray);
        }
        return $nextUsers;
    }

    /**
     * 获得审核人信息
     *
     * @param $info
     * @return array
     */
    public function getReviewerInfo($info){
        $reviewerInfo = [];
        $tempUserArray = [$info->createdBy];
        $reviewerInfo['waitsubmit'] = $tempUserArray;
        $reviewerInfo['reject']     = $tempUserArray;
        //部门负责人
        $tempUserArray = [];
        $deptManagersGroup = json_decode($info->deptManagersGroup, true);
        foreach ($deptManagersGroup as $key => $currentDeptUsers){
            $tempUserArray[] = $key; //审核人传部门id即可
        }

        $reviewerInfo['waitdept'] = array_flip(array_flip($tempUserArray));
        $data = [];
        foreach ($this->lang->localesupport->nodeCodeList as $nodeCode){
            if(isset($reviewerInfo[$nodeCode])){
                $data[$nodeCode] = $reviewerInfo[$nodeCode];
            }else{
                $data[$nodeCode] = [];
            }
        }
        return $data;
    }

    /**
     * 保存工作流
     *
     * @param $info
     * @param $reviewerInfo
     * @param $version
     * @return bool
     */
    public function saveWorkFlow($info, $reviewerInfo, $version){
        $res = false;
        $objectType = $this->config->localesupport->objectType;
        $objectId   = $info->id;
        $title      = $info->startDate .'_'.$info->endDate ;
        $reviewNodeNameList = $this->lang->localesupport->reviewNodeNameList;
        $res = $this->loadModel('iwfp')->startWorkFlow_V2($objectType, $objectId, $title, $info->createdBy, $reviewerInfo, $version, $reviewNodeNameList, $info->workflowId);
        if(!dao::isError()){
            $processInstanceId = $res->processInstanceId;
            $updateParams = new stdClass();
            $updateParams->workflowId = $processInstanceId;
            $this->dao->update(TABLE_LOCALESUPPORT)->data($updateParams)->autoCheck()
                ->where('id')->eq($objectId)
                ->exec();
        }
        if(!dao::isError()){
            $res = true;
        }
        return $res;
    }

    /**
     * 检查提交信息
     *
     * @param $info
     * @param $op
     * @param $oldInfo
     * @return array
     */
    public function checkSubmitInfo($info, $op = 'submit', $oldInfo = null){
        $checkRes   = false;
        $errorData  = [];
        $data = [
            'checkRes'  => $checkRes,
            'errorData' => $errorData,
        ];
        if(!$info){
            $errorData[] =  $this->lang->common->errorParamId;
            $data['errorData'] = $errorData;
            return $data;
        }

        //检查基本信息
        $res = $this->checkPostParamsInfo($info, $op, $oldInfo);
        if(!$res['checkRes']){
           $data = $res;
           return $data;
        }
        //报工信息
        $workReportList = $info->workReportList;
        if(!$workReportList){
            $errorData[] =  $this->lang->localesupport->workReportEmpty;
            $data['errorData'] = $errorData;
            return $data;
        }

       //检查报工信息
        $startDate    = substr($info->startDate, 0, 10);
        $endDate      = substr($info->endDate, 0, 10);
        $supportUsers = explode(',', $info->supportUsers); //支持人员
        $users = $this->loadModel('user')->getUserInfoListByAccounts($supportUsers, 'account,realname');
        if($users){
            $users = array_column($users, 'realname', 'account');
        }
        $workReportList = $this->getFormatWorkReportData($workReportList);
        $workReportUsers = array_keys($workReportList);
        $unWorkReportUsers = array_diff($supportUsers, $workReportUsers);
        if(!empty($unWorkReportUsers)){
            $unWorkReportUserNames = zmget($users, implode(',', $unWorkReportUsers));
            $errorData[] =   sprintf($this->lang->localesupport->checkOpResultList['userNoWorkReportError'], $unWorkReportUserNames);
            $data['errorData'] = $errorData;
            return $data;
        }

        $pattern = '/^\d+(\.\d)?$/';
        $today = helper::today();
        foreach ($workReportList as $supportUser => $workReportUsers){
            $userName = zget($users, $supportUser);
            if(!in_array($supportUser, $supportUsers)){
                $errorData[] =  sprintf($this->lang->localesupport->checkSubmitResultList['supportUserError'], $userName);
                continue;
            }
            foreach ($workReportUsers as $val){
                $supportDate = $val->supportDate;
                $consumed    = $val->consumed;
                //日期检查
                if(!$supportDate){
                    $errorData[] =  sprintf($this->lang->localesupport->checkSubmitResultList['workReportFieldEmpty'], $userName, $this->lang->localesupport->supportDate);
                }
                if(!$this->loadModel('common')->checkJkDateTime($supportDate)){
                    $errorData[] =  sprintf($this->lang->localesupport->checkSubmitResultList['workReportFieldError'], $userName, $this->lang->localesupport->supportDate);
                }

                if(($supportDate < $startDate) || ($supportDate > $endDate) ){
                    $errorData[] =  sprintf($this->lang->localesupport->checkSubmitResultList['supportDateError'], $userName);
                }
                if($supportDate > $today){
                    $errorData[] =   sprintf($this->lang->localesupport->checkSubmitResultList['supportDateMoreTodayError'], $userName, $supportDate);
                }
                //工时检查
                if(!$consumed){
                    $errorData[] =  sprintf($this->lang->localesupport->checkSubmitResultList['workReportFieldEmpty'], $userName, $this->lang->localesupport->consumed);

                }
                //工时格式检查
                if(!(preg_match($pattern, $consumed) && $consumed <= 14)){
                    $errorData[] = sprintf($this->lang->localesupport->checkSubmitResultList['consumedError'], $userName);
                }

                //工时是都大于14
                $reportConsumed = 0;
                $ignoreType = 'localesupport';
                if($op == 'edit'){ //编辑提交
                    $localeSupportId = $info->id; //报工信息都在页面传入
                }else{
                    $localeSupportId = 0;
                }
                $checkConsumed = $this->loadModel('workreport')->checkEffort($reportConsumed, $supportDate, 0, $ignoreType, $supportUser, $localeSupportId);
                if($checkConsumed){
                    $errorData[] = sprintf($this->lang->localesupport->checkOpResultList['consumedOverError'], $userName, $supportDate);
                }
            }
        }
        if(empty($errorData)){
            $checkRes = true;
        }
        //返回
        $data['checkRes']  = $checkRes;
        $data['errorData'] = $errorData;
        return $data;
    }

 /**
     * 相关信息保存任务关联中间表
     * @param $data
     * @param $id
     * @param $type
     * @return mixed
     */
    public function toTaskProblemDemand($data,$id,$type){
        //查询是否存在
        $res = $this->dao->select('id')->from(TABLE_TASK_DEMAND_PROBLEM)
                ->where('typeid')->eq($id)
                ->andWhere('deleted')->eq(0)
                ->andWhere('type')->eq($type)
                ->andWhere('project')->eq($data->project)
                ->andWhere('application')->eq( $data->app)
                //->andWhere('execution')->eq( $data->execution)
                ->fetchAll();
            //存在删除
            if ($res) {
                $this->dao->update(TABLE_TASK_DEMAND_PROBLEM)
                    ->set('deleted')->eq(1)
                    ->where('id')->in(array_column($res,'id'))->exec();
        }

        //新增
        $task = new stdClass();
        $task->product = 0; //产品
        $task->project =  $data->project; //项目
        $task->application = $data->app; //应用系统
        $task->version = 0; //产品版本
        $task->execution = $data->app ; //所属阶段
        $task->code = $data->code;//单号
        $task->typeid = $id;//id
        $task->assignTo = 'closed';//指派给
        $task->type = $type;//类型
        $task->createdDate = date('Y-m-d H:i:s');//创建时间
        $task->taskid = isset($data->taskid) ? $data->taskid : '';
        $this->dao->insert(TABLE_TASK_DEMAND_PROBLEM)->data($task)->autoCheck()->exec();
        $taskID = $this->dao->lastInsertId();
        return $taskID;

    }
    /**
     * 生成报工任务
     * @param $supportId
     */
    public function createStageAndTask($supportId){
        /** @var taskModel $taskModel */
        $taskModel = $this->loadModel('task');
        //查询现场支持对应的任务条信息
        $workData = $this->getWorkReportBySupportIdToTask($supportId);
        foreach ($workData as $workDatum) {
            $workDatum->project = zget($this->lang->localesupport->projectList,$workDatum->deptId);
            $workDatum->app = date('Y',strtotime($workDatum->startDate));
            $workDatum->dealUser = 'closed';
            $workDatum->product = 0;
            $workDatum->productPlan = 0;
            $workDatum->supportId = $supportId;
            $workDatum->createdBy = $workDatum->createdBy;
            //存中间表
            $taskidID =  $this->toTaskProblemDemand($workDatum,$supportId,'localesupport');
            if($taskidID){
                //生成任务
                $taskModel->assignedAutoCreateStageTask($workDatum->project,'localesupport',$workDatum->app,$workDatum->code,$workDatum);
            }
        }
    }

    /**
     * 生成任务会自动更新现场支持报工表中的信息
     * @param $data
     * @param $taskID
     * @param $execution
     */
    public function updateLocalSupportTaskID($data,$taskID,$execution,$supportId,$dept){
        if($data){
            $table = TABLE_LOCALESUPPORT_WORKREPORT;
            //$dept = zget(array_flip($this->lang->localesupport->projectList),$data->project);
            $this->dao->update($table)->set('taskId')->eq($taskID)->set('execution')->eq($execution)->where("id in(select id from (select id from $table where deptId='$dept' and YEAR(supportDate) = $data->application and supportId = '$supportId' and deleted='0')t1 )")->exec();

            if(!dao::isError()){
                $workAll = $this->dao->select('localsupport.id,supportDate,supportUser,localsupport.consumed,taskId,task.execution,task.project,supportId,exec.parent')
                    ->from($table)->alias('localsupport')
                    ->leftJoin(TABLE_TASK)->alias('task')
                    ->on("localsupport.taskId = task.id")
                    ->leftJoin(TABLE_EXECUTION)->alias('exec')
                    ->on("exec.id = task.execution")
                    ->where('localsupport.deptId')->eq($dept)
                    ->andWhere("task.id")->eq($taskID)
                    ->andWhere("YEAR(supportDate) = $data->application")
                    ->andWhere("localsupport.deleted")->eq('0')
                    ->andWhere("task.deleted")->eq('0')
                    ->andWhere("exec.deleted")->eq('0')
                    ->andWhere("localsupport.supportId")->eq($supportId)
                    ->fetchAll();
                $this->addEffort($workAll); //保存工时
            }
        }
    }

    /**
     *  工作量存工时表
     * @param $datas
     */
    public function addEffort($datas)
    {
        $this->app->loadLang('task');
        foreach ($datas as $data) {

            $work = new stdClass();
            $work->project = $data->project;
            $work->activity = $data->parent;
            $work->apps = $data->execution;
            $work->objects = $data->taskId;
            $work->beginDate = $data->supportDate;
            $work->consumed = $data->consumed;
            $work->workType = array_search('现场支持',$this->lang->task->typeList);
            $work->account = $data->supportUser;
            $work->weeklyNum = date('W', strtotime($data->supportDate));
            $work->append = '0'; //是否补报
            $work->source = '2'; //现场支持
            $this->dao->begin();  //开启事务
            $this->dao->insert(TABLE_WORKREPORT)->data($work)->autoCheck()->exec();//存报工表

            $workID = $this->dao->lastInsertID();
            $this->loadModel('action')->create('workreport', $workID, 'created', $this->post->comment);
            $workIDs[] = $workID;

            $effort = new stdClass();
            $effort->date     = $data->supportDate;
            $effort->consumed =  $data->consumed;
            $effort->work     =  '';
            $effort->workID     = $workID;
            $effort->buildId     = $data->supportId;
            $effort->account     = $data->supportUser;
            $this->effort($data->taskId,$effort);//任务报工存工时表
            if($workID && !dao::isError()){
                $this->dao->commit();
            }else{
                $this->dao->rollback();
            }
        }

    }

    /**
     * 存入任务工时表
     * @param $taskID
     * @param $effort
     */
    public function effort($taskID,$effort){

        $this->loadModel('effort');
        $this->loadModel('task');
        $task = $this->task->getByID($taskID);
        $left = $task->left;

        $efforts = array();
        $totalConsumed = array();

        $left -= $effort->consumed;

        $row = new stdclass();
        $row->date     = $effort->date;
        $row->consumed = $effort->consumed;
        $row->left     = $left;
        $row->work     = $effort->work;
        $row->buildID  = $effort->buildId;
        $row->support  = 2;
        $row->account  = $effort->account;
        /* $row->beginDate   = $effort->beginDate;
         $row->endDate     = $effort->endDate;*/
        $row->workID      = $effort->workID;

        $efforts[] = $row;
        if(!isset($totalConsumed[$effort->date])) $totalConsumed[$effort->date] = 0;
        $totalConsumed[$effort->date] += $effort->consumed;

        foreach($totalConsumed as $consumedDate => $consumed)
        {
            $consumedToday = $this->loadModel('effort')->getWorkloadToday($this->app->user->account, $consumed, 'insert', $consumedDate);
        }

        $this->task->batchCreateEffort($taskID, $efforts);

    }

    /**
     * 检查操作权限
     *
     * @param $info
     * @param $action
     * @return bool|mixed
     */
    public static function isClickable($info, $action){
        global $app;
        $action = strtolower($action);
        $account = $app->user->account;
        $localesupportModel = new localesupportModel();
        if($action == 'edit') {
            $res = $localesupportModel->checkIsAllowEdit($info, $account);
            return $res['result'];
        }
        if($action == 'reportwork') {
            $res = $localesupportModel->checkIsAllowReportWork($info, $account);
            return $res['result'];
        }
        if($action == 'submit') {
            $res = $localesupportModel->checkIsAllowSubmit($info, $account);
            return $res['result'];
        }

        //审批
        if ($action == 'review') {
            $res = $localesupportModel->checkIsAllowReview($info, $account);
            return $res['result'];
        }

        //删除
        if ($action == 'delete')    {
            $res = $localesupportModel->checkIsAllowDelete($info, $account);
            return $res['result'];
        }
        return true;
    }

    /**
     * sendmail
     *
     * @param  int    $supportId
     * @param  int    $actionID
     * @access public
     * @return void
     */
    public function sendmail($supportId, $actionID)
    {
        $this->loadModel('mail');
        $info   = $this->getById($supportId);
        $status = $info->status;
        if(!in_array($status, $this->lang->localesupport->mailStatusArray)){ //不需要发邮件
            return ;
        }
        //是否处在审批进行中(中间环节不发邮件)
        if($status == $this->lang->localesupport->statusArray['waitdept']){ //部门负责人审批中，不需要发邮件
            $objectType = $this->config->localesupport->objectType;
            $version = $info->version;
            $nodeCode = $this->lang->localesupport->nodeCodeList[$status];
            $isProcessing = $this->loadModel('review')->getReviewNodeIsProcessing($objectType, $supportId, $version, $nodeCode);
            if($isProcessing){
                return ;
            }
        }

        $sendUsers = $this->getToAndCcList($info);
        if(!$sendUsers) return ;
        list($toList, $ccList) = $sendUsers;

        $users = $this->loadModel('user')->getPairs('noletter');
        //部门信息
        $appList  =  $this->loadModel('application')->getPairs();
        $deptList =  $this->loadModel('dept')->getTopPairs();

        /* 获取后台通知中配置的邮件发信。*/
        $this->app->loadLang('custommail');
        $mailConf   = isset($this->config->global->setLocalesupportMail) ? $this->config->global->setLocalesupportMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf   = json_decode($mailConf);
        $mailTitle  = vsprintf($mailConf->mailTitle, $mailConf->variables);

        /* Get action info. */
        $action          = $this->loadModel('action')->getById($actionID);
        $history         = $this->action->getHistory($actionID);
        $action->history = isset($history[$actionID]) ? $history[$actionID] : array();

        /* Get mail content. */
        $modulePath = $this->app->getModulePath($appName = '', 'localesupport');
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

        $subject = $mailTitle;
        /* Send mail. */
        $this->mail->send($toList, $subject, $mailContent, $ccList);
        if($this->mail->isError()) trigger_error(join("\n", $this->mail->getError()));
    }

    /**
     * Get toList and ccList.
     *
     * @param $localesupportInfo
     * @param bool $isGetCcList
     * @return array
     */
    public function getToAndCcList($localesupportInfo, $isGetCcList = true)
    {
        /* Set toList and ccList. */
        /* 初始化发信人和抄送人变量，获取发信人和抄送人数据。*/
        $toList = $localesupportInfo->dealUsers;
        $ccList = '';
        $status = $localesupportInfo->status;
        if($isGetCcList){
            if($status == $this->lang->localesupport->statusArray['waitdept']) { //待审批抄送人员是支持人员
                $ccList = $localesupportInfo->supportUsers;
            }
        }
        return array($toList, $ccList);
    }

    /**
     * @Notes:喧喧
     * @Date: 2024/7/10
     * @Time: 17:53
     * @Interface getXuanxuanTargetUser
     * @param $obj
     * @param $objectType
     * @param $objectID
     * @param $actionType
     * @param $actionID
     * @param string $actor
     * @return array|false
     */
    public function getXuanxuanTargetUser($obj, $objectType, $objectID, $actionType, $actionID, $actor = ''){
        $info   = $this->getById($objectID);
        $sendUsers = $this->getToAndCcList($info, false);

        if(!$sendUsers) return;
        $toList = $sendUsers[0];

        $server   = $this->loadModel('im')->getServer('zentao');
        //$url = $server.helper::createLink($objectType, 'view', "id=$objectID", 'html');

        $url = $server.'/localesupport-view-'.$objectID.'.html';
        $subcontent = [];
        $subcontent['headTitle']    = '';
        $subcontent['headSubTitle'] = '';
        $subcontent['count']        = 0;
        $subcontent['id']           = 0;
        $subcontent['parent']       = '';
        $subcontent['parentURL']    = "";
        $subcontent['cardURL']      = $url;
        $subcontent['name']         =  $info->code;//消息体 编号后边位置 标题

        //标题
        $title = '';
        $actions = [];
        return ['toList' => $toList,'subcontent' => $subcontent,'url' => $url,'title' => $title,'actions' => $actions];
    }

    /**
     * 获得格式化审核人信息
     *
     * @param $reviewList
     * @return array
     */
    public function getFormatReviewList($reviewList){
        $data = [];
        if(!$reviewList){
            return $data;
        }
        $endStatusArray = ['pass', 'reject'];
        foreach ($reviewList as $key => $val){
            $tempData = [];
            $currentReviewList = $val->reviewerList;
            foreach ($currentReviewList as $reviewerInfo){
                $parentId = $reviewerInfo->parentId;
                $tempData[$parentId]['reviewers'][] = $reviewerInfo->reviewer;
                if(in_array($reviewerInfo->status, $endStatusArray)){
                    $tempData[$parentId]['realReviewInfo'] = $reviewerInfo;
                }
            }
            $val->reviewerList = $tempData;
            $data[] = $val;
        }
        return $data;
    }

    /**
     * 获得格式化所有版本的审核信息列表
     *
     * @param $reviewList
     * @return array
     */
    public function getFormatAllVersionReviewList($reviewList){
        $data = [];
        if(!$reviewList){
            return $data;
        }
        foreach ($reviewList as $version => $value){
            $temp = $this->getFormatReviewList($value);
            $data[$version] = $temp;
        }
        return $data;
    }

    /**
     * 获得是否限制开始时间
     *
     * @param null $oldInfo
     * @return bool
     */
    public function getIsCheckStartDate($oldInfo = null){
        $isCheckStartDate = false;
        $limitDaySwitch     = $this->config->localesupport->limitDaySwitch;
        $reportWorkLimitDay = $this->config->localesupport->reportWorkLimitDay;
        if(($limitDaySwitch == 1) && ($reportWorkLimitDay > 0) && (empty($oldInfo) || (!empty($oldInfo) && $oldInfo->status != 'reject' && $oldInfo->isOld == 1))){
            $isCheckStartDate = true;
        }
        return $isCheckStartDate;
    }


    /**
     * 获得时间插件允许的最小时间
     *
     * @param null $oldInfo
     * @return false|string
     */
    public function getMinStartDate($oldInfo = null){
        $minStartDate = '0000-00-00 00:00:00';
        $isCheckStartDate = $this->getIsCheckStartDate($oldInfo);
        if($isCheckStartDate){
            $reportWorkLimitDay = $this->config->localesupport->reportWorkLimitDay;
            $currentDay  = Helper::today();
            $lastMonthEndDay = Helper::lastMonthEndDay();
            $deadlineDay = helper::getTrueWorkDay($lastMonthEndDay, $reportWorkLimitDay, true);
            if($deadlineDay < $currentDay){
                $currentMonthFirstDay = Helper::currentMonthFirstDay();
                $minStartDate = $currentMonthFirstDay; //当月第一天
            }else{
                $lastMonthFirstDay =  Helper::lastMonthFirstDay();
                $minStartDate = $lastMonthFirstDay;
            }
        }
        return $minStartDate;
    }

    /**
     * 获得允许报工的用户
     *
     * @return array
     */
    public function getAllowSupportUsers(){
        $data = [];
        $supportProjectList = $this->lang->localesupport->projectList;
        if(empty($supportProjectList)){
            return $data;
        }
        $deptIds = array_filter(array_keys($supportProjectList));
        $ret = $this->loadModel('user')->getUsersNameByDept($deptIds);
        if($ret){
            $data = $ret;
        }
        return $data;
    }
}
