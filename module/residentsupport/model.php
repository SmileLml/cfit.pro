<?php
class residentsupportModel extends model
{
    /**
     * Project: chengfangjinke
     * Method: buildSearchForm
     * User: wangjiruong
     * Year: 2022
     * Date: 2022/10/13
     * Time: 14:54
     * Desc: This is the code comment. This method is called buildSearchForm.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $queryID
     * @param $actionURL
     */
    public function buildSearchForm($queryID, $actionURL){
        $this->config->residentsupport->search['actionURL'] = $actionURL;
        $this->config->residentsupport->search['queryID']   = $queryID;
        $this->loadModel('search')->setSearchParams($this->config->residentsupport->search);
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
    public function getList($browseType, $queryID, $orderBy, $pager = null){
        $account = $this->app->user->account;
        $residentsupportQuery = '';
        if($browseType == 'bysearch') {
            $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : '';
            if($query) {
                $this->session->set('residentsupportQuery', $query->sql);
                $this->session->set('residentsupportForm', $query->form);
            }
            if($this->session->residentsupportQuery == false) {
                $this->session->set('residentsupportQuery', ' 1 = 1');
            }
            $residentsupportQuery = $this->session->residentsupportQuery;
            //值班人员正则匹配
            $findStr = "/AND `dutyUser`  LIKE '%(\w+)%'/";
            if(preg_match($findStr, $residentsupportQuery, $match_res)){
                $matchStr = $match_res[0];
                $dutyUser = $match_res[1];
                $condition = [
                    'dutyUser' => $dutyUser
                ];
                $dayDetailList = $this->loadModel('residentsupport')->getDutyUserListByCondition($condition, 'templateId,dayId,dutyUserDept');
                if(empty($dayDetailList)){
                    return false;
                }
                $templateIds = array_column($dayDetailList, 'templateId');
                $deptIs      = array_column($dayDetailList, 'dutyUserDept');
                //$dayIds      = array_column($dayDetailList, 'dayId');
                $replaceStr = '  AND t1.templateId in (' . implode(',',$templateIds) . ') AND t1.deptId in (' . implode(',',$deptIs) . ') ';
                $residentsupportQuery = str_ireplace($matchStr, $replaceStr, $residentsupportQuery);
            }
            //值班组长正则匹配
            $findStr = "/AND `dutyGroupLeader`  LIKE '%(\w+)%'/";
            if(preg_match($findStr, $residentsupportQuery, $match_res)){
                $matchStr = $match_res[0];
                $dutyGroupLeader = $match_res[1];
                $condition = [
                    'dutyGroupLeader' => $dutyGroupLeader
                ];
                $daylList = $this->loadModel('residentsupport')->getDutyDayListByCondition($condition, 'id,templateId');
                if(empty($daylList)){
                    return false;
                }
                $templateIds = array_column($daylList, 'templateId');
                $dayIds      = array_column($daylList, 'id');
                $replaceStr = '  AND t1.templateId in (' . implode(',',$templateIds) . ') AND t3.dayId in (' . implode(',',$dayIds) . ') ';
                $residentsupportQuery = str_ireplace($matchStr, $replaceStr, $residentsupportQuery);
            }
        }

        if(strpos($residentsupportQuery,'templateId')){
            $residentsupportQuery = str_replace('AND (`', ' AND (`t1.', $residentsupportQuery);
            $residentsupportQuery = str_replace('AND `', ' AND `t1.', $residentsupportQuery);
            $residentsupportQuery = str_replace('`', '', $residentsupportQuery);
        }
        if(strpos($residentsupportQuery,'status')){
            $residentsupportQuery = str_replace('AND (`', ' AND (`t1.', $residentsupportQuery);
            $residentsupportQuery = str_replace('AND `', ' AND `t1.', $residentsupportQuery);
            $residentsupportQuery = str_replace('`', '', $residentsupportQuery);
        }
        $orderByArray = explode('_', $orderBy);
        $orderField = $orderByArray[0];
        if($orderField == 'dutyDateTime'){
            $orderByArray[0] = 'startDate';
            $orderBy = implode('_', $orderByArray);
        }
        $residentsupports = $this->dao->select('t1.*, t2.name, t2.`type`, t2.subType, t2.startDate, t2.endDate,t2.enable, group_concat(distinct t3.dutyUser) as dutyUsers, group_concat(distinct t4.dutyGroupLeader) as dutyGroupLeaders')
            ->from(TABLE_RESIDENT_SUPPORT_TEMPLATE_DEPT)->alias("t1")
            ->leftJoin(TABLE_RESIDENT_SUPPORT_TEMPLATE)->alias('t2')->on('t1.templateId = t2.id')
            ->leftJoin(TABLE_RESIDENT_SUPPORT_DAY_DETAIL)->alias('t3')->on("t1.templateId = t3.templateId and t1.deptId = t3.dutyUserDept and t3.deleted = '0' and t3.dutyUser != ''")
            ->leftJoin(TABLE_RESIDENT_SUPPORT_DAY)->alias('t4')->on("t4.templateId = t1.templateId and t4.id = t3.dayId and t4.deleted = '0' and t4.dutyGroupLeader != ''")
            ->where('t1.deleted')->eq('0')
            ->andwhere('t2.deleted')->eq('0')
            ->beginIF($browseType != '' and $browseType != 'all' and $browseType != 'waitdeal' and $browseType != 'bysearch')->andWhere('t1.status')->eq($browseType)->fi()
            ->beginIF($browseType == 'waitdeal')->andWhere("concat(',',t1.dealUsers,',')")->like("%,$account,%")->fi() //待处理
            ->beginIF($browseType == 'bysearch')->andWhere($residentsupportQuery)->fi()
            ->groupBy('t1.id')
            ->orderBy($orderBy)
            ->page($pager, 't1.id')
            ->fetchAll();
        if(!$residentsupports){
            return $residentsupports;
        }
        $userList = $this->loadModel('user')->getListFiled('account,dept,realname');
        $userList = array_column($userList, null, 'account');
        $deptList = $this->loadModel('dept')->getAllDeptList('id,name');
        $deptNameList = array_column($deptList, 'name', 'id');

        foreach ($residentsupports as $key => $val){
            $dutyGroupLeaderList = [];
            if($val->dutyGroupLeaders){
                $dutyGroupLeaders = explode(',', $val->dutyGroupLeaders);
                foreach ($dutyGroupLeaders as $dutyGroupLeader){
                    $userInfo = zget($userList, $dutyGroupLeader);
                    $deptId   = zget($userInfo, 'dept');
                    $realName = zget($userInfo, 'realname');
                    $deptName = zget($deptNameList, $deptId);
                    $dutyGroupLeaderInfo = new stdClass();
                    $dutyGroupLeaderInfo->dutyGroupLeader = $dutyGroupLeader;
                    $dutyGroupLeaderInfo->realname = $realName;
                    $dutyGroupLeaderInfo->deptId   = $deptId;
                    $dutyGroupLeaderInfo->deptName = $deptName;
                    $dutyGroupLeaderList[] = $dutyGroupLeaderInfo;
                }
            }
            $val->dutyGroupLeaderList = $dutyGroupLeaderList;
            $residentsupports[$key] = $val;
        }
        return $residentsupports;
    }

    /**
     * Judge button if can clickable.
     *
     * @param $templateDeptInfo
     * @param $action
     * @return array|bool
     */
    public static function isClickable($templateDeptInfo, $action)
    {
        global $app;
        $action = strtolower($action);
        $residentSupportModel = new residentsupportModel();
        if($action == 'editscheduling'){
            $res =  $residentSupportModel->checkTemplateDeptIsScheduling($templateDeptInfo);
            return $res['result'];
        }

        //申请审批
        if($action == 'submit'){
            $res =  $residentSupportModel->checkTemplateDeptIsSubmit($templateDeptInfo);
            return $res['result'];
        }

        //评审
        if($action == 'review') {
            $res =  $residentSupportModel->checkTemplateDeptIsReview($templateDeptInfo);
            return $res['result'];
        }

        //启用排班
        if($action == 'deletedutyuser'){
            $res = $residentSupportModel->checkTemplateDeptIsDeleteDutyUser($templateDeptInfo);
            return $res['result'];
        }
        return true;
    }


    //获取日历视图
    public function getCalendarList($year){
        $users = $this->loadModel('user')->getPairs('noletter');
        $outsideList1 = $this->loadModel('user')->getUsersNameByType('outsideExpertType');
        $outsideList2 = $this->loadModel('user')->getUsersNameByType('outside');
        $users = array_merge($users, $outsideList1, $outsideList2);
        $dept = $this->loadModel('dept')->getOptionMenu();

        $template = $this->dao->select('id,subType,type')->from(TABLE_RESIDENT_SUPPORT_TEMPLATE)->where('deleted')->eq('0')->andwhere('enable')->eq('1')->andwhere("(LEFT(`startDate`, 4) = '$year')")->fi()->fetchall();
        $day = $this->dao->select("t1.id,t1.templateId,t1.dutyDate,t1.dutyGroupLeader")->from(TABLE_RESIDENT_SUPPORT_DAY)->alias("t1")
//            ->leftJoin(TABLE_RESIDENT_SUPPORT_DAY_DEPT)->alias('t2')
//            ->on('t1.id = t2.dayId')
            ->where('enable')->eq('1')
            ->beginIF($year)->andwhere("(LEFT(`dutyDate`, 4) = '$year')")->fi()
            ->andwhere('t1.deleted')->eq('0')
            ->fetchAll();
        $dayIds = implode(',',array_column($day,'id'));
        $details = $this->dao->select("dayId,dutyUserDept,dutyUser")->from(TABLE_RESIDENT_SUPPORT_DAY_DETAIL)->where("dayId")->in($dayIds)->andwhere('deleted')->eq('0')->fetchall();
        $tempIds = array_column($template,'id');
        $templateDept = $this->dao->select("*")->from(TABLE_RESIDENT_SUPPORT_TEMPLATE_DEPT)->where("templateId")->in($tempIds)->andwhere('deleted')->eq('0')->fetchall();
        $data = [];
        foreach ($day as $k=>$v) {
            $v->details = [];
            foreach ($template as $v2) {
                if ($v2->id == $v->templateId){
                    $v->subType = $v2->subType;
                    $v->type = $v2->type;
                }
            }
            foreach ($details as $detail) {
                if ($detail->dayId == $v->id && $detail->dutyUser != ''){
                    $arr = [
                        'dutyUserDept' => $detail->dutyUserDept,
                        'dutyUser' => $detail->dutyUser,
                        'dutyName' => $dept[$detail->dutyUserDept].' / '.$users[$detail->dutyUser]
                    ];
                    if ($detail->dutyUser == $v->dutyGroupLeader) $arr['dutyName'] = $arr['dutyName']."（值班组长）";
                    //有值班人员的
                    $v->details[] = $arr;
                }
                $detail->dutyName = $dept[$detail->dutyUserDept].' / '.$users[$detail->dutyUser];
                if ($detail->dutyUser == $v->dutyGroupLeader&& $v->dutyGroupLeader != '') $detail->dutyName .= "（值班组长）";
                if ($detail->dayId == $v->id){

                    $temp = (array)$detail;
                    $day[$k]->allDetails[] = $temp;
                }
            }
            $newArr = [];
            $newArr['deptUserNum'] = count($v->details);
            $newArr['UserNum'] = count($v->allDetails);
            $data[$v->dutyDate][$v->type.'-'.$v->subType] = $newArr;
        }

        $arr = [];
        $newData = [];
        foreach ($data as $k3=>$v3) {
            $color = "#72dfb3";//全部排满
            foreach ($v3 as $k4=>$v4) {
                if ($v4['deptUserNum'] < $v4['UserNum']){
                    $newData[$k3] = $v3;//部分排满（包括全部未排满）
                }
            }
            $arr[$k3] = $color;

        }

        foreach ($newData as $dk=>$dv) {
            $arr[$dk] = "#ea7070";//全部未排满
            foreach ($dv as $v4) {
                if ($v4['deptUserNum'] == $v4['UserNum']){
                    $arr[$dk] = "#fff3cf";//部分排满
                }
            }
        }
        foreach ($day as $k=>$v) {
            foreach ($arr as $rk=>$rv) {
                if ($v->dutyDate == $rk){
                    $day[$k]->color = $rv;
                }
            }
        }
        $data = [];
        $type = $this->lang->residentsupport->typeList;
        $subType = $this->lang->residentsupport->subTypeList;
        foreach ($day as $yk=>$yv) {
            $info['status'] = 1;//审核通过允许填写值班日志
            foreach ($yv->allDetails as $allDetail) {
                foreach ($templateDept as $item) {
                    if ($item->deptId == $allDetail['dutyUserDept'] && $item->templateId == $yv->templateId){
                        if ($item->isModify == 1 && $item->status != $this->lang->residentsupport->temDeptStatusList['pass']){
                            $info['status'] = 0;
                        }
                    }
                }
            }

            $info['id'] = $yv->id;
            $info['start'] = $yv->dutyDate;
            $info['dutyGroupLeader'] = $yv->dutyGroupLeader;
            $info['details'] = $yv->allDetails;
            $info['color'] = $yv->color;
            $info['end'] = $yv->dutyDate;
            $info['title'] = $type[$yv->type].'-'.$subType[$yv->subType]."（".count($yv->details)."/".count($yv->allDetails)."）";
            $data[$yv->dutyDate.'-'.$yv->type.'-'.$yv->subType] = $info;
        }
        echo json_encode(array_values($data));
    }


    /**
     *检查是否允许提交
     *
     * @param $templateDeptInfo
     * @param $postData
     * @return array
     */
    public function checkTemplateDeptIsSubmit($templateDeptInfo, $postData = null){
        $res = array(
            'result'  => false,
            'message' => '',
        );
        if(!$templateDeptInfo){
            $res['message'] = $this->lang->common->errorParamId;
            return $res;
        }
        //查询用户部门和模板中的部门是否一致
        $deptId = $templateDeptInfo->deptId;
        if($deptId != $this->app->user->dept){
            $res['message'] = $this->lang->residentsupport->checkSubmitResultList['userError'];
            return $res;
        }

        //当前状态
        $status = $templateDeptInfo->status;
        if($status != $this->lang->residentsupport->temDeptStatusList['waitApply']){
            $statusDesc = zget($this->lang->residentsupport->temDeptStatusDescList, $status);
            $res['message'] = sprintf($this->lang->residentsupport->checkSubmitResultList['statusError'], $statusDesc);
            return $res;
        }
        //提交数据验证
        if($postData){
            if(($postData->version != $templateDeptInfo->version) || ($postData->status != $templateDeptInfo->status)){
                $res['message'] = $this->lang->residentsupport->checkSubmitResultList['statusOrVersionError'];
                return $res;
            }
        }
        //返回
        $res['result'] = true;
        return $res;
    }

    /**
     *检查是否允许排班
     *
     * @param $templateDeptInfo
     * @return array
     */
    public function checkTemplateDeptIsScheduling($templateDeptInfo){
        $res = array(
            'result'  => false,
            'message' => '',
        );
        if(!$templateDeptInfo){
            $res['message'] = $this->lang->residentsupport->checkSchedulingResultList['userError'];
            return $res;
        }
        //查询用户部门和模板中的部门是否一致
        $deptId = $templateDeptInfo->deptId;
        if($deptId != $this->app->user->dept){
            $res['message'] = $this->lang->residentsupport->checkSchedulingResultList['userError'];
            return $res;
        }

        //当前状态
        $status = $templateDeptInfo->status;
        if(!in_array($status, $this->lang->residentsupport->temDeptAllowSchedulingStatusList)){
            $statusDesc = zget($this->lang->residentsupport->temDeptStatusDescList, $status);
            $res['message'] = sprintf($this->lang->residentsupport->checkSchedulingResultList['statusError'], $statusDesc);
            return $res;
        }
        //返回
        $res['result'] = true;
        return $res;
    }

    /**
     *检查是否允许审核
     *
     * @param $templateDeptInfo
     * @param $postData
     * @return array
     */
    public function checkTemplateDeptIsReview($templateDeptInfo, $postData = null){
        $res = array(
            'result'  => false,
            'message' => '',
        );
        if(!$templateDeptInfo){
            $res['message'] = $this->lang->common->errorParamId;
            return $res;
        }

        //当前状态
        $status = $templateDeptInfo->status;
        if(!in_array($status, $this->lang->residentsupport->temDeptAllowReviwStatusList)){
            $statusDesc = zget($this->lang->residentsupport->temDeptStatusDescList, $status);
            $res['message'] = sprintf($this->lang->residentsupport->checkReviewResultList['statusError'], $statusDesc);
            return $res;
        }
        //查询用户是否具有审核权限
        if(!(isset($templateDeptInfo->dealUsers) && in_array($this->app->user->account, explode(',', $templateDeptInfo->dealUsers)))){
            $res['message'] = $this->lang->residentsupport->checkReviewResultList['userError'];
            return $res;
        }
        //提交数据验证
        if($postData){
            if(($postData->version != $templateDeptInfo->version) || ($postData->status != $templateDeptInfo->status)){
                $res['message'] = $this->lang->residentsupport->checkReviewResultList['statusOrVersionError'];
                return $res;
            }
        }
        //返回
        $res['result'] = true;
        return $res;
    }

    /**
     *检查是否允许删除排班
     *
     * @param $templateDeptInfo
     * @param null $postData
     * @return array
     */
    public function checkTemplateDeptIsDeleteDutyUser($templateDeptInfo, $postData = null){
        $res = array(
            'result'  => false,
            'message' => '',
        );
        if(!$templateDeptInfo){
            $res['message'] = $this->lang->common->errorParamId;
            return $res;
        }

        //当前状态
        $status = $templateDeptInfo->status;
        if(!in_array($status, $this->lang->residentsupport->temDeptAllowDeleteStatusList)){
            $statusDesc = zget($this->lang->residentsupport->temDeptStatusDescList, $status);
            $res['message'] = sprintf($this->lang->residentsupport->checkDeleteResultList['statusError'], $statusDesc);
            return $res;
        }
        //提交数据验证
        if($postData){
            if(($postData->version != $templateDeptInfo->version) || ($postData->status != $templateDeptInfo->status)){
                $res['message'] = $this->lang->residentsupport->checkDeleteResultList['statusOrVersionError'];
                return $res;
            }
        }
        //返回
        $res['result'] = true;
        return $res;
    }

    /*导出模板设置excel下拉框*/
    public function setListValue(){
        $postType = $this->lang->residentsupport->postType;
        $timeType = $this->lang->residentsupport->durationTypeList;
        $type = $this->lang->residentsupport->typeList;
        $subType = $this->lang->residentsupport->subTypeList;
        $dept = $this->loadModel('dept')->getOptionMenu();
        unset($dept[0]);
        $users = $this->loadModel('user')->getPairs('noletter');
        $outsideList1 = $this->loadModel('user')->getUsersNameByType('outsideExpertType');
        $outsideList2 = $this->loadModel('user')->getUsersNameByType('outside');
        $users = array_merge($users, $outsideList1, $outsideList2);
        $satff = [];
        foreach ($users as $user){
            $staff[] = $user;
        }
        $this->post->set('postTypeList',       array_values($postType));
        $this->post->set('dutyGroupLeaderList',       array_values($staff));
        $this->post->set('dutyUserList',       array_values($staff));
        $this->post->set('dutyUserDeptList',       array_values($dept));
        $this->post->set('timeTypeList',       array_values($timeType));
        $this->post->set('typeList',       array_values(array_filter($type)));
        $this->post->set('subTypeList', array_values(array_filter($subType)));
        $this->post->set('listStyle',      $this->config->residentsupport->export->listFields);
        $this->post->set('extraNum', 0);
        return [$dept,$users];
    }


    /**
     * 提交申请
     *
     * @param $templateDeptId
     * @return array
     */
    public function submit($templateDeptId){
        $objectType = $this->lang->residentsupport->objectTypeList['resident_support_template_dept'];
        //获得数据
        $data = fixer::input('post')->get();
        //历史数据
        $templateDeptInfo = $this->loadModel('residentsupport')->getTemplateDeptInfoById($templateDeptId);
        $oldStatus = $templateDeptInfo->status;
        //检查是否许允许申请审核
        $res = $this->checkTemplateDeptIsSubmit($templateDeptInfo, $data);
        if(!$res['result']){
            dao::$errors[] = $res['message'];
            return false;
        }
        if(!isset($data->managerUsers) || empty($data->managerUsers)){
            dao::$errors['managerUsers'] = $this->lang->residentsupport->checkSubmitResultList['managerUsersEmpty'];
            return false;
        }
        //申请审核后的一下状态
        $nextStatus    = $this->loadModel('residentsupport')->getTemplateDeptSubmitNextStatus($templateDeptInfo);
        $nextDealUsers = $this->loadModel('residentsupport')->getTemplateDeptNextDealUsers($templateDeptInfo, $nextStatus);
        //已经存在的最大审核节点
        $maxStage = $this->loadModel('review')->getReviewMaxStage($templateDeptId, $objectType, $templateDeptInfo->version);
        $stage = $maxStage + 1;

        //nodeCode标识
        $nodeCode = $this->loadModel('residentsupport')->getTemplateDepReviewNodeCode($nextStatus);

        $currentUser =  $this->app->user->account;
        $currentTime = Helper::now();
        //更新信息
        $updateParams = new stdClass();
        $updateParams->status          = $nextStatus;
        $updateParams->dealUsers       = $nextDealUsers;
        $updateParams->applySubmitBy   = $currentUser;
        $updateParams->applySubmitTime = $currentTime;

        //修改驻场支持部门提交审核表
        $this->dao->update(TABLE_RESIDENT_SUPPORT_TEMPLATE_DEPT)->data($updateParams)
            ->autoCheck()
            ->where('id')->eq($templateDeptId)->exec();
        if(dao::isError()) {
            dao::$errors[] = $this->lang->residentsupport->checkReviewResultList['fail'];
            return false;
        }

        //增加审核节点
        $version = $templateDeptInfo->version;
        $reviewers = explode(',', $nextDealUsers);
        //扩展信息
        $extParams = [
            'nodeCode' => $nodeCode,
        ];
        $this->loadModel('review')->addNode($objectType, $templateDeptId, $version, $reviewers, true, 'pending', $stage, $extParams);
        //记录工时
        $consumed = 0;
        $this->loadModel('consumed')->record($objectType, $templateDeptId, $consumed, $currentUser, $oldStatus, $nextStatus, $this->post->mailto);

        //获得差异信息
        $extChangeInfo = [];
        //抄送人
        $ext = new stdClass();
        $ext->old = '';
        $ext->new = isset($_POST['mailto'])  ?  implode(',',$_POST['mailto']) :'';
        $extChangeInfo['mailto'] = $ext;
        $logChange = common::createChanges($templateDeptInfo, $updateParams, $extChangeInfo);
        return $logChange;
    }
    /**
     * 保存excel排班数据
     */
    public function createFromImport($templateId,$editMethod)
    {
        $res = array(
            'result' => false,
            'message' => '',
        );

        $dutyDate = $_POST['dutyDate'];
        $type = $_POST['type'];
        $subType = $_POST['subType'];
        $dutyUserDept = $_POST['dutyUserDept'];//部门
        asort($dutyDate);
        $dutyDate = array_values($dutyDate);
        $checkData = $this->checkImportData($dutyDate);
        if(!$checkData['result']){
            return $checkData;
        }
        $endDate = $dutyDate[count($dutyDate)-1];
        $enable = 0;
        if ($_POST['subType'][0] == 1) {
            $enable = 1;//状态启用
        }

        //___________________________________________________________

        $day = [];
        $templateInfo = [];
        if ($editMethod == 'add'){
            $day = $this->getdays($type,$subType,$dutyDate);
        }else{
            $templateInfo = $this->getTemplateInfoById($templateId);
        }

        if ($day && $editMethod == 'add'){
            $arr = [
                'result'=>false,
                'message'=>"值班日期已存在，请选择编辑模板"
            ];
            return $arr;
        }
        if ($editMethod == 'edit'){
            if (empty($templateInfo)){
                $arr = [
                    'result'=>false,
                    'message'=>"模板不存在"
                ];
                return $arr;
            }
            if ($type[0] != $templateInfo->type){
                $arr = [
                    'result'=>false,
                    'message'=>"导入的模板包分类与选择编辑的模板分类不一致"
                ];
                return $arr;
            }
            if ($subType[0] != $templateInfo->subType){
                $arr = [
                    'result'=>false,
                    'message'=>"导入的模板包分类与选择编辑的模板子分类不一致"
                ];
                return $arr;
            }
            if ($templateInfo->startDate < $dutyDate[0] || $endDate > $templateInfo->endDate){
                $response['message'] = "导入的模板开始时间超出选择的模板时间范围，不能编辑";
                $arr = [
                    'result'=>false,
                    'message'=>"导入的模板开始时间超出选择的模板时间范围，不能编辑"
                ];
                return $arr;
            }
        }
        if ($editMethod == 'edit') {
            $day = $this->dao->select("t1.id,t1.dutyDate,type,subType,templateId,startDate,endDate,dutyGroupLeader")->from(TABLE_RESIDENT_SUPPORT_DAY)->alias("t1")
                ->innerjoin(TABLE_RESIDENT_SUPPORT_TEMPLATE)->alias('t2')
                ->on('t1.templateId = t2.id')
//                ->where('t1.enable')->eq($enable)
                ->where('t1.deleted')->eq(0)
                ->andwhere('t2.deleted')->eq(0)
//                ->andwhere('t2.enable')->eq($enable)
                ->andwhere('t1.templateId')->eq($templateId)
                ->orderby('startDate_asc')
                ->fetchAll();
            $templateParams = new stdClass();
            $templateParams->editBy = $this->app->user->account;
            $templateParams->editByTime = date("Y-m-d H:i:s");
            $this->updateTemplate($day,$enable,$type,$subType,$dutyDate,$dutyUserDept);
            $objectType = $this->lang->residentsupport->objectTypeList['resident_support_template'];
            $changes = common::createChanges($templateInfo, $templateParams);
            $actionID = $this->loadModel('action')->create($objectType, $templateId, 'editedtemplate', '');
            $res = $this->action->logHistory($actionID, $changes);
        } else {
            $templateID = $this->addTemplate($enable,$type,$subType,$dutyDate,$dutyUserDept);
            $sql = "insert into" . TABLE_RESIDENT_SUPPORT_DAY . " (templateId,dutyDate,enable,createdBy,createdDept,createdTime,dutyGroupLeader) values ";
            $sqlStr = "";
            $dutyDate = array_values(array_unique($_POST['dutyDate']));
            //获取值班组长
            $arr = [];
            foreach ($_POST['dutyDate'] as $k2=>$v2) {
                if (!array_key_exists($_POST['dutyDate'][$k2],$arr)){
                    $arr[$_POST['dutyDate'][$k2]] = $_POST['dutyUser'][$k2];
                }
            }
            foreach ($dutyDate as $k => $v) {
                $dutyGroupLeader = '';
                foreach ($arr as $rk=>$rv) {
                    if ($v == $rk){
                        $dutyGroupLeader = $rv;
                    }
                }
                if ($dutyDate[$k] > date("Y-m-d")){
                    $sqlStr .= "('" . $templateID . "','" . $v . "','" . $enable . "','" . $this->app->user->account . "','" . $this->app->user->dept . "','" . date("Y-m-d H:i:s") . "','".$dutyGroupLeader."'),";
                }
            }
            $sql = $sql . rtrim($sqlStr, ',');
            //批量添加天信息
            $this->dao->query($sql);
            if (dao::isError()) {
                return dao::$errors;
            }
            $this->addTemplateDay($enable,$templateID);
        }
        $res['result'] = true;
        return $res;
    }


    /**
     *部门审核
     *
     * @param $templateDeptId
     * @return array
     */
    public function review($templateDeptId){
        $objectType = $this->lang->residentsupport->objectTypeList['resident_support_template_dept'];
        //获得数据
        $data = fixer::input('post')->get();
        //历史数据
        $templateDeptInfo = $this->loadModel('residentsupport')->getTemplateDeptInfoById($templateDeptId);
        $oldStatus = $templateDeptInfo->status;
        $version  = $templateDeptInfo->version;
        $templateId = $templateDeptInfo->templateId;
        $deptId     = $templateDeptInfo->deptId;
        //检查是否许允许申请审核
        $res = $this->checkTemplateDeptIsReview($templateDeptInfo, $data);
        if(!$res['result']){
            dao::$errors[] = $res['message'];
            return false;
        }
        if(!isset($data->result) || empty($data->result)){
            dao::$errors['result'] = $this->lang->residentsupport->checkReviewResultList['resultError'];
            return false;
        }
        $reviewResult = $data->result;
        //处理审核操作(一人审核即为审核通过)
        $result = $this->loadModel('review')->check($objectType, $templateDeptId, $version, $reviewResult, $this->post->comment, 0, null, false);
        if(!$result){
            dao::$errors[] = $this->lang->residentsupport->checkReviewResultList['fail'];
            return false;
        }

        //申请审核后的一下状态
        $nextStatus    = $this->loadModel('residentsupport')->getTemplateDeptReviewNextStatus($templateDeptInfo, $reviewResult);
        $nextDealUsers = $this->loadModel('residentsupport')->getTemplateDeptNextDealUsers($templateDeptInfo, $nextStatus);
        //已经存在的最大审核节点
        $maxStage = $this->loadModel('review')->getReviewMaxStage($templateDeptId, $objectType, $templateDeptInfo->version);
        $stage = $maxStage + 1;

        //nodeCode标识
        $nodeCode = $this->loadModel('residentsupport')->getTemplateDepReviewNodeCode($nextStatus);
        $currentUser =  $this->app->user->account;
        $currentTime = Helper::now();
        //更新信息
        $updateParams = new stdClass();
        $updateParams->status    = $nextStatus;
        $updateParams->dealUsers = $nextDealUsers;

        //修改驻场支持部门提交审核表
        $this->dao->update(TABLE_RESIDENT_SUPPORT_TEMPLATE_DEPT)->data($updateParams)
            ->autoCheck()
            ->where('id')->eq($templateDeptId)->exec();
        if(dao::isError()) {
            dao::$errors[] = $this->lang->residentsupport->checkReviewResultList['fail'];
            return false;
        }

        //增加审核节点
        $version = $templateDeptInfo->version;
        $reviewers = explode(',', $nextDealUsers);
        //扩展信息
        $extParams = [
            'nodeCode' => $nodeCode,
        ];
        $isAddNode = $this->getIsNeedAddNode($nextStatus);
        if($isAddNode){
            $this->loadModel('review')->addNode($objectType, $templateDeptId, $version, $reviewers, true, 'pending', $stage, $extParams);
        }
        //确认通过
        if($nextStatus == $this->lang->residentsupport->temDeptStatusList['pass']){
            //如果有变更排班需要恢复成原来的状态
            $condition = array(
                'templateId'   => $templateId,
                'dutyUserDept' => $deptId,
                'status'       => $this->lang->residentsupport->dayStatusList[2],
            );
            $dayDetailList = $this->loadModel('residentsupport')->getDutyUserListByCondition($condition, 'id,dayId');
            if(!empty($dayDetailList)){
                $dayDetailIds = array_column($dayDetailList, 'id');
                $detailUpdateParams = new stdClass();
                $detailUpdateParams->status = $this->lang->residentsupport->dayStatusList[1];
                $this->dao->update(TABLE_RESIDENT_SUPPORT_DAY_DETAIL)->data($detailUpdateParams)
                    ->autoCheck()
                    ->where('id')->in($dayDetailIds)->exec();
            }
        }
        //记录工时
        $consumed = 0;
        $this->loadModel('consumed')->record($objectType, $templateDeptId, $consumed, $currentUser, $oldStatus, $nextStatus);

        //获得差异信息
        $logChange = common::createChanges($templateDeptInfo, $updateParams);
        return $logChange;
    }

    /**
     * 是否需要增加审核节点
     *
     * @param $status
     * @return bool
     */
    public function getIsNeedAddNode($status){
        $isAddNode = false;
        if(in_array($status, $this->lang->residentsupport->temDeptNeedAddNodeStatusList)){
            $isAddNode = true;
        }
        return $isAddNode;
    }

    /**
     *删除排班
     *
     * @param string $templateDeptId
     * @return array|void
     */
    function deleteDutyUser($templateDeptId){
        //获得数据
        $data = fixer::input('post')->get();
        //历史数据
        $templateDeptInfo = $this->loadModel('residentsupport')->getTemplateDeptInfoById($templateDeptId);
        //检查是否许允许删除值班用户
        $res = $this->checkTemplateDeptIsDeleteDutyUser($templateDeptInfo);
        if(!$res['result']){
            dao::$errors[] = $res['message'];
            return false;
        }
        $oldStatus  = $templateDeptInfo->status;
        $oldVersion = $templateDeptInfo->version;
        $templateId = $templateDeptInfo->templateId;
        $deptId     = $templateDeptInfo->deptId;
        //获得当前模板当前部门的排班信息
        $dutyUserList = $this->loadModel('residentsupport')->getDutyUserListByTemplateAndDeptId($templateId, $deptId);
        if(empty($dutyUserList)){
            dao::$errors[] = $this->lang->residentsupport->checkDeleteResultList['noDutyUserError'];
            return false;
        }
        $dutyUserIds = array_column($dutyUserList, 'id');
        //天值班人员列表
        $dayDutyUserList = [];
        foreach ($dutyUserList as $val){
            $dayId = $val->dayId;
            $dutyUser = $val->dutyUser;
            $dayDutyUserList[$dayId][] = $dutyUser;
        }
        //值班组长在删除的值班组员的信息列表
        $dutyGroupLeaderIds = [];
        foreach ($dayDutyUserList as $dayId => $dutyUsers){
            $dutyGroupLeaderInfo = $this->loadModel('residentsupport')->getDutyGroupLeaderInfo($dayId, $dutyUsers, 'id');
            if($dutyGroupLeaderInfo){
                $dutyGroupLeaderIds[] = $dutyGroupLeaderInfo->id;
            }
        }

        //删除值班组员
        $updateParams = new stdClass();
        $updateParams->dutyUser = '';
        $this->dao->update(TABLE_RESIDENT_SUPPORT_DAY_DETAIL)->data($updateParams)
            ->autoCheck()
            ->where('id')->in($dutyUserIds)->exec();
        if(dao::isError()) {
            dao::$errors[] = $this->lang->residentsupport->opCommonResultList['fail']; //操作失败
            return false;
        }
        //删除值班组长
        if($dutyGroupLeaderIds){
            $updateParams = new stdClass();
            $updateParams->dutyGroupLeader = '';
            $this->dao->update(TABLE_RESIDENT_SUPPORT_DAY)->data($updateParams)
                ->autoCheck()
                ->where('id')->in($dutyGroupLeaderIds)->exec();
        }

        //修改该模板该部门的状态
        $nextStatus = $this->lang->residentsupport->temDeptStatusList['waitSchedule']; //待排期
        $nextDealUsers = '';
        $updateParams = new stdClass();
        $updateParams->status    = $nextStatus;
        $updateParams->dealUsers = $nextDealUsers;
        if($oldStatus == $this->lang->residentsupport->temDeptStatusList['reject']){
            $version = $oldVersion + 1;
            $updateParams->version = $version;
        }

        //修改驻场支持部门表
        $this->dao->update(TABLE_RESIDENT_SUPPORT_TEMPLATE_DEPT)->data($updateParams)
            ->autoCheck()
            ->where('id')->eq($templateDeptId)->exec();
        if(dao::isError()) {
            dao::$errors[] = $this->lang->residentsupport->checkReviewResultList['fail'];
            return false;
        }
        $logChange = common::createChanges($templateDeptInfo, $updateParams);
        return $logChange;
    }

    /**
     *启用排期
     *
     * @return bool
     */
    function enableScheduling(){
        //获得数据
        $data = fixer::input('post')->get();
        $startDate = $data->startDate;
        $endDate   = $data->endDate;
        $res = $this->checkIAllowEnableScheduling($data);
        if(!$res['result']){
            return $res;
        }
        //返回正确
        $resData      = $res['data'];
        $templateInfo = $resData['templateInfo'];
        $dutyDates    = $resData['dutyDates'];
        $dayIds       = $resData['dayIds'];
        $templateId   = $templateInfo->id;
        //启用排期
        $otherEnableDayIds = $this->residentsupport->getOtherEnableDayIds($data->type, $dutyDates, $templateId);
        $otherTemplateIds = [];
        if($otherEnableDayIds){ //关闭其他启用
            $updateParams = new stdClass();
            $updateParams->enable = '0';
            $this->dao->update(TABLE_RESIDENT_SUPPORT_DAY)->data($updateParams)
                ->autoCheck()
                ->where('id')->in($otherEnableDayIds)
                ->exec();
            if(dao::isError()) {
                dao::$errors[] = $this->lang->residentsupport->opCommonResultList['fail'];
                return false;
            }
            //关联模板
            $otherTemplateDayList = $this->loadModel('residentsupport')->getTempDayListByIds($otherEnableDayIds);
            $otherTemplateIds = array_column($otherTemplateDayList, 'templateId');
            $needCloseTemplateIds = $this->residentsupport->getNeedCloseTemplateIds($otherTemplateIds);
            //关闭启用相关模板
            if($needCloseTemplateIds){
                $this->dao->update(TABLE_RESIDENT_SUPPORT_TEMPLATE)->data($updateParams)
                    ->autoCheck()
                    ->where('id')->in($needCloseTemplateIds)
                    ->exec();
            }
        }
        //启用排期
        $updateParams = new stdClass();
        $updateParams->enable = '1';
        $this->dao->update(TABLE_RESIDENT_SUPPORT_DAY)->data($updateParams)
            ->autoCheck()
            ->where('templateId')->eq($templateId)
            ->andwhere('id')->in($dayIds)
            ->andwhere('deleted')->eq('0')
            ->exec();
        if(dao::isError()) {
            dao::$errors[] = $this->lang->residentsupport->opCommonResultList['fail'];
            return false;
        }
        //修改模板启用
        $this->dao->update(TABLE_RESIDENT_SUPPORT_TEMPLATE)->data($updateParams)
            ->autoCheck()
            ->where('id')->eq($templateId)
            ->exec();

        //返回
        //日志信息
        $subLogInfo = " 时间[{$startDate}-{$endDate}]，";
        if(!empty($otherTemplateIds)){
            $condition = array('id' => $otherTemplateIds);
            $otherTemplateList = $this->residentsupport->getTemplateListByCondition($condition);
            $subLogInfo .= '同时关闭以下模板对应时间段排期：';
            foreach ($otherTemplateList as $val){
                $templateId  = $val->id;
                $type        = $val->type;
                $subType     = $val->subType;
                $typeDesc    = zget($this->lang->residentsupport->typeList, $type);
                $subTypeDesc = zget($this->lang->residentsupport->subTypeList, $subType);
                $subLogInfo .= "模板ID：{$templateId} [{$typeDesc}-{$subTypeDesc}]". "\n";
            }

            //关闭模板下的天列表
            $temp = [];
            foreach ($otherTemplateDayList  as $dayInfo){
                $templateId  = $dayInfo->templateId;
                $dutyDate = $dayInfo->dutyDate;
                $temp[$templateId][] = $dutyDate;
            }
            //关闭其他模板值班日志扩展信息
            $objectType = $this->lang->residentsupport->objectTypeList['resident_support_template'];
            foreach ($temp as $templateId => $dutyDates){
                $closeSchedulingInfo = '日期:'.implode('、', $dutyDates);
                $actionID = $this->loadModel('action')->create($objectType, $templateId, 'closescheduling', '', $closeSchedulingInfo);
            }
        }

        //排班信息
        $enableSchedulingInfo = new stdClass();
        $enableSchedulingInfo->old = '';
        $enableSchedulingInfo->new = $subLogInfo;
        //扩展信息
        $extChangeInfo = [
            'enableSchedulingInfo' => $enableSchedulingInfo,
        ];
        $logChange = common::createChanges($templateInfo, $updateParams, $extChangeInfo);
        return $logChange;
    }

    /**
     *编辑排期
     *
     * @return array
     */
    function editScheduling(){
        //获得数据
        $data = fixer::input('post')->get();
        if(!($data->templateId && $data->templateDeptId)){
            dao::$errors[] = $this->lang->common->errorParamId;
            return false;
        }
        if(!(isset($data->dutyUsers) && $data->dutyUsers)){
            dao::$errors[] = $this->lang->residentsupport->checkSchedulingResultList['noAllowError'];
            return false;
        }
        $templateId = $data->templateId;
        $templateDeptId = $data->templateDeptId;
        $dutyUsers = $data->dutyUsers;

        $templateDeptInfo = $this->loadModel('residentsupport')->getTemplateDeptInfoById($templateDeptId);
        $res = $this->checkTemplateDeptIsScheduling($templateDeptInfo);
        if(!$res['result']){
            dao::$errors[] = $res['message'];
            return false;
        }
        $currentUser = $this->app->user->account;
        $currentTime = helper::now();
        $dayDetailIds = array_keys($dutyUsers);
        //查询排班详情
        $dayDetailLit = $this->loadModel('residentsupport')->getDutyUserListByIds($dayDetailIds);
        if(!$dayDetailLit){
            dao::$errors[] = $this->lang->residentsupport->checkSchedulingResultList['noDutyUserError'];
            return false;
        }
        $dayIds = array_column($dayDetailLit, 'dayId');

        //获得修改的日列表
        $dayList = $this->loadModel('residentsupport')->getTempDayListByIds($dayIds);
        $dayList = array_column($dayList, null, 'id');
        $dayDetailLit = array_column($dayDetailLit, null, 'id');
        //实际变更的列表
        $updateDayDetailList = [];
        //变更的天数
        $updateDayIds   = [];
        $updateDayUsers = [];
        $isDutyUserSame = false;
        $dayId = 0;
        foreach ($dutyUsers as $dayDetailId => $dutyUser){
            $dayDetailInfo = zget($dayDetailLit, $dayDetailId);

            $oldDutyUser = $dayDetailInfo->dutyUser;
            $dutyUser = $dutyUser ? $dutyUser: '';
            $dayId = $dayDetailInfo->dayId;
            if($oldDutyUser != $dutyUser){ //值班人员发生变化
                $updateDayIds[] = $dayId;
                $updateDayDetailList[$dayDetailId] = $dutyUser;
            }
            if($dutyUser){
                if(!isset($updateDayUsers[$dayId])){
                    $updateDayUsers[$dayId][] = $dutyUser;
                }else{
                    if(in_array($dutyUser, $updateDayUsers[$dayId])){
                        $isDutyUserSame = true;
                        break;
                    }else{
                        $updateDayUsers[$dayId][] = $dutyUser;
                    }
                }
            }
        }
        //同一天用户会否相同
        if($isDutyUserSame){
            $dayInfo = zget($dayList, $dayId);
            $dutyDate = $dayInfo->dutyDate;
            dao::$errors[] = sprintf($this->lang->residentsupport->checkSchedulingResultList['dayDutyUserRepeatError'], $dutyDate);
            return false;
        }
        //没有任何修改
        if(empty($updateDayDetailList)){
            dao::$errors[] = $this->lang->residentsupport->opCommonResultList['noUpdate'];
            return false;
        }
        //变更修改
        $users = $this->loadModel('user')->getPairs('noletter|noclosed');
        $updateDayDetailLog = [];
        foreach ($updateDayDetailList as $dayDetailId => $dutyUser){
            $dayDetailInfo = zget($dayDetailLit, $dayDetailId);
            $oldDutyUser = $dayDetailInfo->dutyUser;
            $updateParams = new stdClass();
            $updateParams->dutyUser   = $dutyUser;
            $updateParams->editBy     = $currentUser;
            $updateParams->editByTime = $currentTime;
            $this->dao->update(TABLE_RESIDENT_SUPPORT_DAY_DETAIL)->data($updateParams)
                ->autoCheck()
                ->where('id')->eq($dayDetailId)
                ->andwhere('templateId')->eq($templateId)
                ->andwhere('deleted')->eq('0')
                ->exec();
            $dayId = $dayDetailInfo->dayId;
            $updateDayDetailLog[$dayId]['old'][] = zget($users, $oldDutyUser);
            $updateDayDetailLog[$dayId]['new'][] = zget($users, $dutyUser);
        }

        $updateDayIds = array_flip(array_flip($updateDayIds));

        //获得修改的日列表
        $dayList = $this->loadModel('residentsupport')->getTempDayListByIds($updateDayIds);
        $dayList = array_column($dayList, null, 'id');
        //获得日下的第一个值班人员作为组长
        $dayFirstUserList = $this->loadModel('residentsupport')->getDayFirstUserList($updateDayIds);
        foreach ($dayList as $dayInfo){
            $dayId = $dayInfo->id;
            $dutyGroupLeader = $dayInfo->dutyGroupLeader;
            $dayFirstUser = zget($dayFirstUserList, $dayId);
            //修改组长
            if($dutyGroupLeader != $dayFirstUser){
                $updateParams = new stdClass();
                $updateParams->dutyGroupLeader   = $dayFirstUser;
                $updateParams->editBy     = $currentUser;
                $updateParams->editByTime = $currentTime;
                $this->dao->update(TABLE_RESIDENT_SUPPORT_DAY)->data($updateParams)
                    ->autoCheck()
                    ->where('id')->eq($dayId)
                    ->andwhere('deleted')->eq('0')
                    ->exec();
            }
        }
        //查询该部门下是否有未排期的排班
        $temDeptUpdateParams = new stdClass();
        $status  = $templateDeptInfo->status;
        $version = $templateDeptInfo->version;
        if($status == $this->lang->residentsupport->temDeptStatusList['reject']){ //退回
            $version = $version + 1;
        }
        //查询未排期数目
        $unSchedulingCount = $this->loadModel('residentsupport')->getUnSchedulingCount($templateId, $templateDeptInfo->deptId);
        if($unSchedulingCount == 0){
            $status = $this->lang->residentsupport->temDeptStatusList['waitApply'];
        }else{
            $status = $this->lang->residentsupport->temDeptStatusList['waitSchedule'];
        }
        if($status != $templateDeptInfo->status){
            $temDeptUpdateParams->status = $status;
        }
        if($version != $templateDeptInfo->version){
            $temDeptUpdateParams->version = $version;
        }
        if(!empty((array)$temDeptUpdateParams)){
            $this->dao->update(TABLE_RESIDENT_SUPPORT_TEMPLATE_DEPT)->data($temDeptUpdateParams)
                ->autoCheck()
                ->where('id')->eq($templateDeptId)
                ->exec();
        }
        //编辑模板信息
        $templateUpdateParams = new stdClass();
        $templateUpdateParams->editBy = $currentUser;
        $templateUpdateParams->editByTime = $currentTime;
        $this->dao->update(TABLE_RESIDENT_SUPPORT_TEMPLATE)->data($templateUpdateParams)
            ->autoCheck()
            ->where('id')->eq($templateId)
            ->exec();

        //模板信息
        $templateInfo = $this->loadModel('residentsupport')->getTemplateInfoById($templateId);
        $typeDesc = zget($this->lang->residentsupport->typeList, $templateInfo->type);
        $subTypeDesc = zget($this->lang->residentsupport->subTypeList, $templateInfo->subType);
        $deptInfo = $this->loadModel('dept')->getByID($this->app->user->dept);
        $deptName = $deptInfo->name;
        //日志信息
        $subLogInfo = '模板ID:'. $templateId. ' 分类:' .$typeDesc . ' 子类:'. $subTypeDesc. " 部门:". $deptName;
        foreach ($updateDayDetailLog as $dayId => $updateDayDetail){
            $dayInfo = zget($dayList,$dayId);
            $dutyDate = $dayInfo->dutyDate;
            $oldUserList = $updateDayDetail['old'];
            $newUserList = $updateDayDetail['new'];
            $oldUsers = implode(',', $oldUserList);
            $newUsers = implode(',', $newUserList);
            $subLogInfo .= "<br/>{$dutyDate} 值班人员修改前:{$oldUsers} 修改后:{$newUsers}";
        }

        //编辑排班信息
        $schedulingInfo = new stdClass();
        $schedulingInfo->old = '';
        $schedulingInfo->new = $subLogInfo;
        //扩展信息
        $extChangeInfo = [
            'extLogInfo' => $schedulingInfo,
        ];
        $logChange = common::createChanges($templateDeptInfo, $temDeptUpdateParams, $extChangeInfo);
        return $logChange;
    }


    /**
     * 从日志中获得自定义信息
     *
     * @param $logChanges
     * @param $specialFiled
     * @return string
     */
    public function getLogChangesSpecialInfo($logChanges, $specialFiled){
        $specialInfo = '';
        $specialInfoList = [];
        if(!$logChanges){
            return $specialInfo;
        }
        foreach ($logChanges as $val){
            if($val['field'] == $specialFiled){
                $specialInfo = $val['new'];
                return $specialInfo;
                break;
            }else{
                $field = $val['field'];
                $fieldArray = explode('_', $field);
                if(isset($fieldArray[1]) && $fieldArray[0] == $specialFiled){
                    $subFiled = $fieldArray[1];
                    $specialInfoList[$subFiled] = $val['new'];
                }
            }
        }
        if(!empty($specialInfoList)){
            return $specialInfoList;
        }else{
            return  $specialInfo;
        }
    }

    /**
     * 获得丢弃部分字段的信息
     *
     * @param $logChanges
     * @param $specialFields
     * @return string
     */
    public function getLogChangesUnSetSpecialInfo($logChanges, $specialFields){
        if(!$logChanges){
            return $logChanges;
        }
        foreach ($logChanges as $key => $val){
            if(in_array($val['field'], $specialFields)){
                unset($logChanges[$key]);
            }else{
                $field = $val['field'];
                $fieldArray = explode('_', $field);
                $isUnSet = false;
                if(isset($fieldArray[1]) && in_array($fieldArray[0], $specialFields)){
                    $isUnSet = true;
                }
                if($isUnSet){
                    unset($logChanges[$key]);
                }
            }
        }
       return $logChanges;
    }

    /**
     * 检查搜索模板的条件
     *
     * @param $data
     * @return array
     */
    public function checkSearchTemplateCondition($data){
        $res = array(
            'result'  => false,
            'code'    => '1001',
            'message' => '',
        );
        //检查值班分类
//        if(!isset($data->type) || !$data->type){
//            $res['message'] = $this->lang->residentsupport->checkCommonResultList['typeEmpty'];
//            return $res;
//        }
//        //检查值班子类
//        if(!isset($data->subType) || !$data->subType){
//            $res['message'] = $this->lang->residentsupport->checkCommonResultList['subTypeEmpty'];
//            return $res;
//        }
        //检查值班id
        if(!isset($data->templateId) || !$data->templateId){
            $res['message'] = $this->lang->residentsupport->checkCommonResultList['templateIdEmpty'];
            return $res;
        }
        //验证开始时间
        if(!isset($data->startDate) || !$data->startDate || $data->startDate == '000-00-00'){
            $res['message'] = $this->lang->residentsupport->checkCommonResultList['startDateEmpty'];
            return $res;
        }
        $currentDate = helper::today();
        if($data->startDate <= $currentDate){
            $res['message'] = $this->lang->residentsupport->checkCommonResultList['startDateError'];
            return $res;
        }

        //验证结束时间
        if(!isset($data->endDate) || !$data->endDate || $data->endDate == '000-00-00'){
            $res['message'] = $this->lang->residentsupport->checkCommonResultList['endDateEmpty'];
            return $res;
        }
        if($data->endDate < $data->startDate){
            $res['message'] = $this->lang->residentsupport->checkCommonResultList['endDateError'];
            return $res;
        }
        //返回
        $res['result'] = true;
        return $res;
    }

    /**
     *获得未被启用的排班时间
     *
     * @param $dayIds
     * @return array
     */
    public function getNotEnableDayIds($dayIds){
        $notEnableDayIds = [];
        if(!$dayIds){
            return $notEnableDayIds;
        }
        $data = $this->dao->select('id')
            ->from(TABLE_RESIDENT_SUPPORT_DAY)
            ->where("id")->in($dayIds)
            ->andWhere('deleted')->eq('0')
            ->andWhere('enable')->eq('0')
            ->fetchAll();
        if($data){
            $notEnableDayIds = array_column($data, 'id');
        }
        return $notEnableDayIds;
    }


    /**
     *获得其他模板启用的时间ids
     *
     * @param $type
     * @param $dutyDates
     * @param $templateId
     * @return array
     */
    public function getOtherEnableDayIds($type, $dutyDates, $templateId){
        $dayIds = [];
        $ret = $this->dao->select("t1.id")
            ->from(TABLE_RESIDENT_SUPPORT_DAY)->alias("t1")
            ->leftJoin(TABLE_RESIDENT_SUPPORT_TEMPLATE)->alias('t2')
            ->on('t1.templateId = t2.id')
            ->where('t1.enable')->eq('1')
            ->andwhere('t1.deleted')->eq('0')
            ->andwhere('t1.dutyDate')->in($dutyDates)
            ->andwhere('t1.templateId')->ne($templateId)
            ->andwhere('t2.type')->eq($type)
            ->andwhere('t2.id')->ne($templateId)
            ->andwhere('t2.deleted')->eq('0')
            ->fetchAll();
        if($ret){
            $dayIds = array_column($ret, 'id');
        }
        return $dayIds;
    }

    /**
     *获得需要关闭启用的模板
     *
     * @param $templateIds
     * @return array
     */
    public function getNeedCloseTemplateIds($templateIds){
        $needCloseTemplateIds = [];
        if(!$templateIds){
            return $needCloseTemplateIds;
        }
        $ret = $this->dao->select("id")
            ->from(TABLE_RESIDENT_SUPPORT_TEMPLATE)
            ->where('id')->in($templateIds)
            ->andwhere('deleted')->eq('0')
            ->andwhere('enable')->eq('1')
            ->fetchAll();
        if(!$ret){
            return $needCloseTemplateIds;
        }
        //需要返回
        $needCloseTemplateIds = array_column($ret, 'id');

        //存在未关闭的
        $ret = $this->dao->select("templateId")
            ->from(TABLE_RESIDENT_SUPPORT_DAY)
            ->where('templateId')->in($templateIds)
            ->andwhere('deleted')->eq('0')
            ->andwhere('enable')->eq('1')
            ->groupBy('templateId')
            ->fetchAll();
        if($ret){
            $unCloseTemplateIds   = array_column($ret, 'templateId');
            $needCloseTemplateIds = array_diff($needCloseTemplateIds, $unCloseTemplateIds);
        }
        return $needCloseTemplateIds;
    }


    public function updateTemplate($day,$enable,$type,$subType,$dutyDate){
        $date = date("Y-m-d");
        $templateIDS = array_unique(array_column($day,'templateId'));
        $dutyDates = array_unique(array_column($day,'dutyDate'));
        //查询模板-部门
        $dayIDS = array_unique(array_column($day,'id'));
        asort($dutyDates);
        $template = new stdClass();
        $template->startDate = $day[0]->dutyDate;
        if ($dutyDate[0] <= $day[0]->dutyDate){
            //时间范围扩大，开始时间提前
            $template->startDate = $dutyDate[0];
            if ($template->startDate <= date("Y-m-d")){
                foreach ($dutyDate as $rdv){
                    if ($rdv > date("Y-m-d")){
                        $template->startDate = $rdv;
                        break;
                    }
                }
            }
        }
        if ($template->startDate > $day[0]->dutyDate){
            $template->startDate = $day[0]->dutyDate;
        }
        //只与一个模板冲突
        if (count($templateIDS) == 1){
            $template->endDate = $day[0]->endDate;
            if ($dutyDate[count($dutyDate)-1] > $day[0]->endDate){
                $template->endDate = $dutyDate[count($dutyDate)-1];
            }
        }else{
            $template->endDate = $day[count($day)-1]->endDate;
            if ($dutyDate[count($dutyDate)-1] >= $day[count($day)-1]->endDate){
                $template->endDate = $dutyDate[count($dutyDate)-1];
            }
        }
        $templateID = $templateIDS[0];
        $sql = "insert into" . TABLE_RESIDENT_SUPPORT_DAY . " (templateId,dutyDate,enable,createdBy,createdDept,createdTime,dutyGroupLeader) values ";
        $dutyDate = array_values(array_unique($_POST['dutyDate']));

        $newDayDate = [];
        $arr = [];
        foreach ($_POST['dutyDate'] as $k=>$v) {
            if (!array_key_exists($_POST['dutyDate'][$k],$arr)){
                $arr[$_POST['dutyDate'][$k]] = $_POST['dutyUser'][$k];
            }
        }

        foreach ($day as $k=>$v) {
            //已过去的排版记录保留
            $updateData = new stdClass();
            $updateData->enable = $enable;
            $updateData->editBy = $this->app->user->account;
            $updateData->editByTime = date("Y-m-d H:i:s");
            if ($v->dutyDate < $date){
                $newDayDate[] = $v->dutyDate;
                $this->dao->update(TABLE_RESIDENT_SUPPORT_DAY)->data($updateData)->where('id')->eq($v->id)->exec();
            }
            if (!in_array($v->dutyDate,$dutyDate) && !in_array($v->dutyDate,$newDayDate)){
                $newDayDate[] = $v->dutyDate;
                $this->dao->update(TABLE_RESIDENT_SUPPORT_DAY)->data($updateData)->where('id')->eq($v->id)->exec();
            }
        }
        $sqlStr = "";

        foreach ($dutyDate as $dk => $dv) {
            if ($dv > date("Y-m-d")){
                $dutyGroupLeader = '';
                foreach ($arr as $rk=>$rv) {
                    if ($dv == $rk){
                        $dutyGroupLeader = $rv;
                    }
                }
                foreach ($day as $k=>$v) {
                    if (!in_array($dv,$newDayDate)){
                        if ($dv == $v->dutyDate){
                            $updateData = new stdClass();
                            $updateData->enable = $enable;
                            $updateData->editBy = $this->app->user->account;
                            $updateData->editByTime = date("Y-m-d H:i:s");
                            $updateData->dutyGroupLeader = $dutyGroupLeader;
                            $this->dao->update(TABLE_RESIDENT_SUPPORT_DAY)->data($updateData)->where('id')->eq($v->id)->exec();
                        }
                    }
                }
                if (!in_array($dv,$dutyDates)){
                    $sqlStr .= "('" . $templateID . "','" . $dv . "','" . $enable . "','" . $this->app->user->account . "','" . $this->app->user->dept . "','" . date("Y-m-d H:i:s") . "','".$dutyGroupLeader."'),";
                }

            }

        }

        if ($sqlStr){
            $sql = $sql . trim($sqlStr, ',');
            //批量添加天信息
            $this->dao->query($sql);
            if (dao::isError()) {
                return dao::$errors;
            }
        }
        //添加天-详情 部门信息
        $this->addTemplateDay($enable,$templateID,$dayIDS);
        //修改模板信息
        $templateParams = new stdClass();
        $templateParams->editBy = $this->app->user->account;
        $templateParams->editByTime = date("Y-m-d H:i:s");
        $this->dao->update(TABLE_RESIDENT_SUPPORT_TEMPLATE)->data($templateParams)->where('id')->eq($templateID)->exec();

        if (dao::isError()) {
            return dao::$errors;
        }
    }
    //新增模板
    public function addTemplate($enable,$type,$subType,$dutyDate,$dutyUserDept,$templateData=''){
        $template = new stdClass();
        $template->name = $this->session->fileImportName;
        $template->enable = $enable;
        $template->type = $type[0];
        $template->subType = $subType[0];
        $template->startDate = $dutyDate[0];
        $template->endDate = $dutyDate[count($dutyDate) - 1];
        //如果导入的模板时间小于当前时间取当前时间
        if ($template->startDate <= date("Y-m-d")){
            foreach ($dutyDate as $rdv){
                if ($rdv > date("Y-m-d")){
                    $template->startDate = $rdv;
                    break;
                }
            }
        }
        if ($templateData){
            $template->startDate = $templateData->startDate;
            $template->endDate = $templateData->endDate;
        }

        $template->createdBy = $this->app->user->account;
        $template->createdDept = $this->app->user->dept;
        $template->createdTime = date("Y-m-d H:i:s");
        //添加排班模板
        $this->dao->insert(TABLE_RESIDENT_SUPPORT_TEMPLATE)->data($template)->exec();
        if (dao::isError()) {
            return dao::$errors;
        }
        $templateID = $this->dao->lastInsertID();
        $objectType = $this->lang->residentsupport->objectTypeList['resident_support_template'];
        $this->loadModel('action')->create($objectType, $templateID, 'exporttemplate', '');

        return $templateID;
    }
    public function addTemplateDay($enable,$templateID,$dayIDS=''){
        //本次模板天记录
        $data = $this->dao->select("t1.id,t1.dutyDate,type,subType")->from(TABLE_RESIDENT_SUPPORT_DAY)->alias("t1")
            ->leftJoin(TABLE_RESIDENT_SUPPORT_TEMPLATE)->alias('t2')
            ->on('t1.templateId = t2.id')
            ->where('t1.enable')->eq($enable)
            ->andwhere('t1.deleted')->eq(0)
            ->andwhere("t2.id")->eq($templateID)
            ->fetchAll();
        //批量添加天 详情
        $dayDetailSql = "insert into" . TABLE_RESIDENT_SUPPORT_DAY_DETAIL . " (templateId,dayId,dutyUserDept,postType,timeType,startTime,endTime,requireInfo,createdBy,createdTime,dutyUser) values ";
        $dayDetailSqlStr = "";
        $deptStatus = 'waitApply';//待提交
        if ($dayIDS){
            $oldDays = $this->dao->select("t0.*,t1.dutyDate,type,subType")->from(TABLE_RESIDENT_SUPPORT_DAY_DETAIL)->alias("t0")
                ->leftjoin(TABLE_RESIDENT_SUPPORT_DAY)->alias("t1")
                ->on("t0.dayId = t1.id")
                ->leftJoin(TABLE_RESIDENT_SUPPORT_TEMPLATE)->alias('t2')
                ->on('t1.templateId = t2.id')
                ->where('t1.enable')->eq($enable)
                ->andwhere('t1.deleted')->eq(0)
                ->andwhere("t0.dayId")->in($dayIDS)
//                ->andwhere("t0.templateId")->ne($templateID)
                ->fetchAll();
            //已过去的时间同步数据
            foreach ($oldDays as $ok => $ov) {
                foreach ($data as $k2 => $v2) {
                    if ($v2->dutyDate == $ov->dutyDate && $ov->dutyDate < date("Y-m-d")) {
                        $dayDetailSqlStr .= "('" . $templateID . "','" . $v2->id . "','" . $ov->dutyUserDept . "','" . $ov->postType . "','" . $ov->timeType . "','" . $ov->startTime . "','" . $ov->endTime . "','" . $ov->requireInfo . "','" . $this->app->user->account . "','" . date("Y-m-d H:i:s") . "','".$ov->dutyUser."')" . ',';
                        unset($oldDays[$ok]);
                    }
                }
            }
            $oldDays = array_values($oldDays);
            $oldDutyDate = array_column($oldDays,'dutyDate');
            $dutyDateBat = [];
            /*****************************************************/
            $newStr = "";
            foreach ($data as $k2 => $v2) {
                foreach ($_POST['dutyDate'] as $k => $v) {
                    if ($v > date("Y-m-d") && $v === $v2->dutyDate){
                        $timeArr = explode('-', $_POST['dutyDuration'][$k]);
                        $newStr .= "('" . $templateID . "','" . $v2->id . "','" . $_POST['dutyUserDept'][$k] . "','" . $_POST['postType'][$k] . "','" . $_POST['timeType'][$k] . "','" . $timeArr[0] . "','" . $timeArr[1] . "','" . $_POST['requireInfo'][$k] . "','" . $this->app->user->account . "','" . date("Y-m-d H:i:s") . "','".$_POST['dutyUser'][$k]."')" . ',';
                        $dutyDateBat[$v] = $v;
                    }
                }
            }
            if ($dutyDateBat){
                foreach ($data as $k2 => $v2) {
                    foreach ($oldDays as $ok => $ov) {
                        if (!in_array($ov->dutyDate,$dutyDateBat) && $v2->dutyDate == $ov->dutyDate) {
                            $newStr .= "('" . $templateID . "','" . $v2->id . "','" . $ov->dutyUserDept . "','" . $ov->postType . "','" . $ov->timeType . "','" . $ov->startTime . "','" . $ov->endTime . "','" . $ov->requireInfo . "','" . $this->app->user->account . "','" . date("Y-m-d H:i:s") . "','".$ov->dutyUser."')" . ',';
                        }
                    }
                }
            }

            $dayDetailSqlStr .= rtrim($newStr, ',');
        }else{
            //直接添加
            $dayId = 0;
            foreach ($_POST['dutyDate'] as $k => $v) {
                foreach ($data as $k2 => $v2) {
                    if ($v2->dutyDate == $v) {
                        $dayId = $v2->id;
                        $timeArr = explode('-', $_POST['dutyDuration'][$k]);
                        $dayDetailSqlStr .= "('" . $templateID . "','" . $dayId . "','" . $_POST['dutyUserDept'][$k] . "','" . $_POST['postType'][$k] . "','" . $_POST['timeType'][$k] . "','" . $timeArr[0] . "','" . $timeArr[1] . "','" . $_POST['requireInfo'][$k] . "','" . $this->app->user->account . "','" . date("Y-m-d H:i:s") . "','".$_POST['dutyUser'][$k]."')" . ',';
                    }
                }
            }
        }
        if ($dayDetailSqlStr != ''){
            if ($dayIDS){
                $this->dao->update(TABLE_RESIDENT_SUPPORT_DAY_DETAIL)->set("deleted")->eq('1')->where("templateId")->eq($templateID)->exec();
            }
            $dayDetailSql = $dayDetailSql . rtrim($dayDetailSqlStr, ',');
            $this->dao->query($dayDetailSql);
            if (dao::isError()) {
                return dao::$errors;
            }
        }

        $details = $this->dao->select("t1.*,t2.dutyDate")->from(TABLE_RESIDENT_SUPPORT_DAY_DETAIL)->alias("t1")->leftjoin(TABLE_RESIDENT_SUPPORT_DAY)->alias("t2")->on("t1.dayId=t2.id")->where('t1.templateId')->eq($templateID)->andwhere("t1.deleted")->eq(0)->fetchall();
        $templateDept = [];
        $dayDept = [];
        foreach ($details as $k=>$v) {
            $templateDept[$v->dutyUserDept][] = $v;
            $dayDept[$v->dutyDate.'-'.$v->dutyUserDept] = $v;
        }
        $templateDeptList = $this->dao->select("*")->from(TABLE_RESIDENT_SUPPORT_TEMPLATE_DEPT)->where("templateId")->eq($templateID)->andWhere("deleted")->eq('0')->fetchall();
        $templateDept = array_values($templateDept);
        //批量添加模板-部门信息
        $templateDeptSql = "insert into" . TABLE_RESIDENT_SUPPORT_TEMPLATE_DEPT . " (templateId,deptId,status,createdBy,createdTime,createdDept) values ";
        $deptStatus = 'waitApply';
        $templateDeptSql2 = "";

        foreach ($templateDept as $tk=>$tv) {
            foreach ($tv as $tk2=>$tv2) {
                $dutyUserDept = $tv2->dutyUserDept;
                if ($tv2->dutyUser == ''){
                    $deptStatus = 'waitSchedule';
                }
            }
            if ($templateDeptList){
                $templateDeptID = array_column($templateDeptList,'deptId');
                foreach ($templateDeptList as $tdl){
                    if ($dutyUserDept == $tdl->deptId && in_array($tdl->deptId,$templateDeptID)){
                        $needDealIgnore = $this->loadModel('review')->getUnDealReviewNodes('residentsupport', $tdl->id, $tdl->version);
                        if(!empty($needDealIgnore)){
                            $ret = $this->loadModel('review')->ignoreReviewNodeAndReviewers($needDealIgnore);
                        }
                        $tempDeptParams = new stdClass();
                        $tempDeptParams->version = $tdl->version+1;
                        $tempDeptParams->dealUsers = "";
                        $tempDeptParams->status = $deptStatus;
                        $tempDeptParams->editBy = $this->app->user->account;
                        $tempDeptParams->editByTime = date("Y-m-d H:i:s");
                        $this->dao->update(TABLE_RESIDENT_SUPPORT_TEMPLATE_DEPT)->data($tempDeptParams)->where("id")->eq($tdl->id)->exec();
                    }
                    if ($dutyUserDept != $tdl->deptId && !in_array($tdl->deptId,$templateDeptID)){
                        $templateDeptSql2 .= "('".$templateID."','".$dutyUserDept."','".$deptStatus."','".$this->app->user->account."','".date("Y-m-d H:i:s")."','".$this->app->user->dept."')".',';
                    }
                }
            }else{
                $templateDeptSql2 .= "('".$templateID."','".$dutyUserDept."','".$deptStatus."','".$this->app->user->account."','".date("Y-m-d H:i:s")."','".$this->app->user->dept."')".',';
            }

        }
        if ($templateDeptSql2 != ''){
            $templateDeptSql = $templateDeptSql.rtrim($templateDeptSql2, ',');
            $this->dao->query($templateDeptSql);
            if (dao::isError()) {
                return dao::$errors;
            }
        }

        /***弃用天-部门表
        $dayDeptSql = "insert into" . TABLE_RESIDENT_SUPPORT_DAY_DEPT . " (templateId,dayId,deptId,deptUserNum,createdBy,createdTime,createdDept) values ";

        foreach ($dayDept as $dk=>$dv) {
        $dv->deptUserNum = 0;
        foreach ($details as $k=>$v) {
        if ($v->dutyUserDept == $dv->dutyUserDept && $v->dutyDate == $dv->dutyDate){
        $dv->deptUserNum++;
        }
        }
        $dayDeptSql .= "('".$templateID."','".$dv->dayId."','".$dv->dutyUserDept."','".$dv->deptUserNum."','".$this->app->user->account."','".date("Y-m-d H:i:s")."','".$this->app->user->dept."'),";
        }
        $dayDeptSql = rtrim($dayDeptSql, ',');
        $this->dao->query($dayDeptSql);
        if (dao::isError()) {
        return dao::$errors;
        }
         */
    }

    /**
     *格式化排班数据
     *
     * @param $dutyUserList
     * @return array
     */
    public function getFormatDutyUserList($dutyUserList){
        $data = [];
        foreach ($dutyUserList as $dayId => $dayUserList){
            $dayUserCount = 0;
            $formatDayUserList = [];
            $modifyStatus = [];
            foreach ($dayUserList as $deptId => $deptUserList){
                $statusArray = array_column($deptUserList, 'status');
                $modifyStatus = array_merge($modifyStatus, $statusArray);

                $deptUserCount = count($deptUserList);
                $dayUserCount += $deptUserCount;

                $formatDayUserList[$deptId]['total'] = $deptUserCount;
                $formatDayUserList[$deptId]['data'] = $deptUserList;
            }
            $data[$dayId]['total'] = $dayUserCount;
            $data[$dayId]['data']  = $formatDayUserList;
            $modifyStatus = array_flip(array_flip($modifyStatus));
            //改天是否变更
            $isModify = false;
            if(in_array($this->lang->residentsupport->dayDetailStatusList[2], $modifyStatus)){
                $isModify = true;
            }
            $data[$dayId]['isModify'] = $isModify;
        }
        return $data;
    }
    //获取排班模板详情
    public function getTemplateList($templateId,$res,$exportType=1){
        //$exportType 1、导出排班模板。2、导出排班数据
        $startDate = isset($_POST['startDate']) ? $_POST['startDate'] : '';
        $endDate = isset($_POST['endDate']) ? $_POST['endDate'] : '';
        $list = $this->dao->select("t1.dutyUser,t1.dutyUserDept,t1.postType,t1.timeType,t1.startTime,t1.endTime,t1.requireInfo,t2.dutyDate,t2.dutyGroupLeader")->from(TABLE_RESIDENT_SUPPORT_DAY_DETAIL)->alias("t1")
            ->leftjoin(TABLE_RESIDENT_SUPPORT_DAY)->alias("t2")
            ->on("t2.id=t1.dayId")
            ->where('t1.templateId')->eq($templateId)
            ->andwhere('t1.deleted')->eq(0)
            ->beginIF($startDate != '')->andWhere('t2.dutyDate')->ge($startDate)->fi()
            ->beginIF($endDate != '')->andWhere('t2.dutyDate')->le($endDate)->fi()
            ->orderby("t2.dutyDate_asc")
            ->fetchall();
        $postType = $this->lang->residentsupport->postType;
        $dept = $res[0];
        $users = $res[1];
        foreach ($list as $k=>$v) {
            $info = new stdClass();
            $info->dutyDate = $v->dutyDate;
            $info->postType = $postType[$v->postType];
            $info->dutyUserDept = $dept[$v->dutyUserDept];
            $info->timeType = $this->lang->residentsupport->durationTypeList[$v->timeType];
            $info->dutyDuration = $v->startTime.'-'.$v->endTime;
            $info->requireInfo = $v->requireInfo;
            $info->type = $this->lang->residentsupport->typeList[$_POST['type']];
            $info->subType = $this->lang->residentsupport->subTypeList[$_POST['subType']];
            $info->dutyGroupLeader = '';
            $info->dutyUser = '';
//            if ($exportType == 2){
            $info->dutyGroupLeader = $users[$v->dutyGroupLeader];
            $info->dutyUser = $users[$v->dutyUser];
//            }
            $arr[] = $info;
        }
        return $arr;
    }


    /**
     * 获得用户类型 1-普通用户 2-指定二线用户
     *
     * @return int
     */
    public function getModifySchedulingUserType(){
//        $userType = 1;
//        $currentUser = $this->app->user->account;
//        $secondReviews = $this->loadModel('custom')->getCustomSetList('problem', 'apiDealUserList');
//        if (in_array($currentUser, $secondReviews)) {
//            $userType = 2;
//        }
        //暂时去掉判断默认都是二线用户，具有变更的权限
        $userType = 2;
        return $userType;
    }

    //检查部门、值班人员是否对应、数据校验
    public function checkImportData($dutyDate){
        $dept = $this->loadModel('dept')->getOptionMenu();
        $fileds = 'dept,account,realname';
        $users = $this->loadModel('user')->getListFiled($fileds);
        $dateArr = [];
        //判断是否全部小于当前时间
        $endDate = $dutyDate[count($dutyDate)-1];
        if ($endDate <= date("Y-m-d")){
            $arr = [
                'result'=>false,
                'message'=>"值班日期全部小于等于当前时间，不能导入"
            ];
            return $arr;
        }
        if (count(array_unique($_POST['type'])) > 1) {
            $res['message'] = "值班类型不一致";
            return $res;
        }
        if (count(array_unique($_POST['subType'])) > 1) {
            $res['message'] = "值班子类不一致";
            return $res;
        }
        foreach ($_POST['dutyDate'] as $k => $v) {
            $num = $k+1;

            if (!strtotime($_POST['dutyDate'][$k])){
                $arr = [
                    'result'=>false,
                    'message'=>"第". $num ."行：值班日期格式不对"
                ];
                return $arr;
            }
            if ($_POST['dutyDate'][$k] == ''){
                $arr = [
                    'result'=>false,
                    'message'=>"第". $num ."行：值班日期不能为空"
                ];
                return $arr;
            }
            $checkDate = explode('-',$_POST['dutyDate'][$k]);
            if (strlen($checkDate[1]) < 2 || strlen($checkDate[2]) < 2){
                $arr = [
                    'result'=>false,
                    'message'=>"第". $num ."行：值班日期格式不对。示例：".date("Y-m-d"),
                ];
                return $arr;
            }
            if ($_POST['postType'][$k] == ''){
                $arr = [
                    'result'=>false,
                    'message'=>"第". $num ."行：值班岗位不能为空"
                ];
                return $arr;
            }
            if ($_POST['dutyUserDept'][$k] == ''){
                $arr = [
                    'result'=>false,
                    'message'=>"第". $num ."行：值班部门不能为空"
                ];
                return $arr;
            }
            if ($_POST['timeType'][$k] == ''){
                $arr = [
                    'result'=>false,
                    'message'=>"第". $num ."行：时长类型不能为空"
                ];
                return $arr;
            }
            if ($_POST['dutyDuration'][$k] == ''){
                $arr = [
                    'result'=>false,
                    'message'=>"第". $num ."行：值班时长不能为空"
                ];
                return $arr;
            }
            if ($_POST['type'][$k] == ''){
                $arr = [
                    'result'=>false,
                    'message'=>"第". $num ."行：值班类型不能为空"
                ];
                return $arr;
            }
            if ($_POST['subType'][$k] == ''){
                $arr = [
                    'result'=>false,
                    'message'=>"第". $num ."行：值班子类不能为空"
                ];
                return $arr;
            }
            if ($_POST['dutyDate'][$k] > date("Y-m-d")){
                $dateArr[$_POST['dutyDate'][$k]] = $_POST['dutyDate'][$k];
                foreach ($users as $k2=>$v2){
                    if ($_POST['dutyUser'][$k] == $v2->account && $_POST['dutyUserDept'][$k] != $v2->dept){
                        $arr = [
                            'result'=>false,
                            'message'=>"第". $num ."行：".$_POST['dutyDate'][$k]."值班人员与所属部门不一致"
                        ];
                        return $arr;
                    }
                }
            }
        }
        foreach ($dateArr as $dk=>$dv) {
            $user = [];
            foreach ($_POST['dutyDate'] as $k => $v) {
                if ($_POST['dutyDate'][$k] == $dv){
                    if ($_POST['dutyUser'][$k]!=''){
                        $user[] = $_POST['dutyUser'][$k];
                    }
                }
            }
            if (count($user) != count(array_unique($user))){
                $arr = [
                    'result'=>false,
                    'message'=>"日期：".$dv."  员工同一天不能多次排班"
                ];
                return $arr;
            }
        }
        return ['result'=>true];
    }

    /**
     * 格式化值班详情列表按照部门展示
     *
     * @param $dutyUserList
     * @return array
     */
    public function formatDutyUserListGroupDeptId($dutyUserList){
        $data = [];
        if(!$dutyUserList){
            return $data;
        }
        foreach ($dutyUserList as $val){
            $dutyUserDept = $val->dutyUserDept;
            $data[$dutyUserDept][] = $val;
        }
        return $data;
    }

    /**
     *编辑排期
     *
     * @return array
     */
    function modifyScheduling($dayId){
        //获得数据
        $data = fixer::input('post')->get();
        if(!(isset($data->dutyUsers) && $data->dutyUsers)){
            dao::$errors[] = $this->lang->residentsupport->checkSchedulingResultList['noAllowError'];
            return false;
        }

        if(!($data->templateId && $data->dayId && $data->dutyUsers)){
            dao::$errors[] = $this->lang->common->errorParamId;
            return false;
        }

        $templateId  = $data->templateId;
        $dutyUsers   = $data->dutyUsers;
        $sortKeys    = $data->sortKeys;
        $dayInfo     = $this->loadModel('residentsupport')->getTempDayInfoById($dayId);
        $dutyDate    = $dayInfo->dutyDate;
        //检查会否允许按照天维度变更排班
        $res = $this->loadModel('residentwork')->checkIsModifyScheduling($dayInfo);
        if(!$res['result']){
            dao::$errors[] = $res['message'];
            return false;
        }

        $currentUser  = $this->app->user->account;
        $currentTime  = helper::now();
        $dayDetailIds = array_keys($dutyUsers);
        //查询排班详情
        $dayDetailLit = $this->loadModel('residentsupport')->getDutyUserListByIds($dayDetailIds);
        if(!$dayDetailLit){
            dao::$errors[] = $this->lang->residentsupport->checkSchedulingResultList['noDutyUserError'];
            return false;
        }
        $dayDetailLit = array_column($dayDetailLit, null, 'id');

        //实际变更的列表
        $updateDayDetailList = [];
        $isDutyUserEmpty = false;
        $isDutyUserSame  = false;

        $existDutyUsers = [];
        $key = 0;
        foreach ($dutyUsers as $dayDetailId => $dutyUser){
            $key = $sortKeys[$dayDetailId];
            $dayDetailInfo = zget($dayDetailLit, $dayDetailId);
            $oldDutyUser = $dayDetailInfo->dutyUser;
            $dutyUser = $dutyUser ? $dutyUser: '';
            $dayId = $dayDetailInfo->dayId;
            if(!$dutyUser){
                $isDutyUserEmpty = true;
                break;
            }
            //检查是否存在用户重复
            if(in_array($dutyUser, $existDutyUsers)){
                $isDutyUserSame = true;
                break;
            }
            $existDutyUsers[] = $dutyUser;
            if($oldDutyUser != $dutyUser){ //值班人员发生变化
                $updateDayDetailList[$dayDetailId] = $dutyUser;
            }
        }
        //检查用户是否为空
        if($isDutyUserEmpty){
            dao::$errors[] = sprintf($this->lang->residentsupport->checkSchedulingResultList['dutyUserEmptyError'], $key);
            return false;
        }
        //同一天用户会否相同
        if($isDutyUserSame){
            dao::$errors[] = sprintf($this->lang->residentsupport->checkSchedulingResultList['dutyUserRepeatError'], $key);
            return false;
        }
        //没有任何修改
        if(empty($updateDayDetailList)){
            dao::$errors[] = $this->lang->residentsupport->opCommonResultList['noUpdate'];
            return false;
        }

        $userType = $this->getModifySchedulingUserType();
        //修改值班详情
        $users = $this->loadModel('user')->getPairs('noletter|noclosed');
        $updateDayDetailLog = [];
        $updateDeptIds      = []; //涉及到的部门
        foreach ($updateDayDetailList as $dayDetailId => $dutyUser){
            $dayDetailInfo = zget($dayDetailLit, $dayDetailId);
            $oldDutyUser = $dayDetailInfo->dutyUser;
            $deptId = $dayDetailInfo->dutyUserDept;
            $updateParams = new stdClass();
            $updateParams->dutyUser   = $dutyUser;
            $updateParams->editBy     = $currentUser;
            $updateParams->editByTime = $currentTime;
            if($userType == 1){
                $updateParams->status = $this->lang->residentsupport->dayDetailStatusList[2];
            }
            $this->dao->update(TABLE_RESIDENT_SUPPORT_DAY_DETAIL)->data($updateParams)
                ->autoCheck()
                ->where('id')->eq($dayDetailId)
                ->andwhere('templateId')->eq($templateId)
                ->andwhere('deleted')->eq('0')
                ->exec();

            //需要修改的部门
            $updateDeptIds[] = $deptId;

            //记录日志
            $updateDayDetailLog[$deptId]['old'][] = zget($users, $oldDutyUser);
            $updateDayDetailLog[$deptId]['new'][] = zget($users, $dutyUser);
        }

        //天维度值班信息
        $dayUpdateParams = new stdClass();
        $dayUpdateParams->editBy     = $currentUser;
        $dayUpdateParams->editByTime = $currentTime;
        //获得天值班详情的第一个值班人员作为组长
        $firstDutyUser = $this->loadModel('residentsupport')->getDayFirstDutyUser($dayId);
        if($firstDutyUser != $dayInfo->dutyGroupLeader){
            $dayUpdateParams->dutyGroupLeader = $firstDutyUser;
        }
        $this->dao->update(TABLE_RESIDENT_SUPPORT_DAY)->data($dayUpdateParams)
            ->autoCheck()
            ->where('id')->eq($dayId)
            ->andwhere('deleted')->eq('0')
            ->exec();

        //模板维度列表
        $updateDeptIds = array_flip(array_flip($updateDeptIds));
        $templateDeptList = $this->loadModel('residentsupport')->getTemplateDeptListByTemAndDeptIds($templateId, $updateDeptIds);
        if($templateDeptList){
            $templateDeptList = array_column($templateDeptList, NULL, 'deptId');
        }
//        if($userType == 1){
//            $objectType = $this->lang->residentsupport->objectTypeList['resident_support_template_dept'];
//            foreach ($templateDeptList as $templateDeptInfo){
//                $status = $templateDeptInfo->status;
//                $isModify = $templateDeptInfo->isModify;
//                if($status == $this->lang->residentsupport->temDeptStatusList['waitDeptReview'] && ($isModify == $this->lang->residentsupport->temDeptModifyStatusList[2])){ //如果部门状态是待部门审核
//                    continue;
//                }
//                $templateDeptId = $templateDeptInfo->id;
//                //作废掉其他待审核的节点
//                $needIgnoreNodeIds = $this->loadModel('review')->getUnDealReviewNodes($objectType, $templateDeptId, $templateDeptInfo->version);
//                if(!empty($needIgnoreNodeIds)){
//                    $ret = $this->loadModel('review')->ignoreReviewNodeAndReviewers($needIgnoreNodeIds);
//                }
//
//                //申请审核后的一下状态
//                $nextStatus    = $this->lang->residentsupport->temDeptStatusList['waitDeptReview'];
//                $nextDealUsers = $this->loadModel('residentsupport')->getTemplateDeptNextDealUsers($templateDeptInfo, $nextStatus);
//                $version = $templateDeptInfo->version +1;
//                //已经存在的最大审核节点
//                $maxStage = $this->loadModel('review')->getReviewMaxStage($templateDeptId, $objectType, $version);
//                $stage = $maxStage + 1;
//
//                //nodeCode标识
//                $nodeCode = $this->loadModel('residentsupport')->getTemplateDepReviewNodeCode($nextStatus);
//                //更新信息
//                $updateParams = new stdClass();
//                $updateParams->status    = $nextStatus;
//                $updateParams->isModify  = $this->lang->residentsupport->temDeptModifyStatusList[2]; //已变更
//                $updateParams->dealUsers = $nextDealUsers;
//                $updateParams->version   = $version;
//
//                //修改驻场支持部门提交审核表
//                $this->dao->update(TABLE_RESIDENT_SUPPORT_TEMPLATE_DEPT)->data($updateParams)
//                    ->autoCheck()
//                    ->where('id')->eq($templateDeptId)->exec();
//                if(dao::isError()) {
//                    dao::$errors[] = $this->lang->residentsupport->checkReviewResultList['fail'];
//                    return false;
//                }
//
//                //增加审核节点
//                $reviewers = explode(',', $nextDealUsers);
//                //扩展信息
//                $extParams = [
//                    'nodeCode' => $nodeCode,
//                ];
//                $isAddNode = $this->getIsNeedAddNode($nextStatus);
//                if($isAddNode){
//                    $this->loadModel('review')->addNode($objectType, $templateDeptId, $version, $reviewers, true, 'pending', $stage, $extParams);
//                }
//            }
//        }

        //模板信息
        $templateInfo = $this->loadModel('residentsupport')->getTemplateInfoById($templateId);
        $typeDesc = zget($this->lang->residentsupport->typeList, $templateInfo->type);
        $subTypeDesc = zget($this->lang->residentsupport->subTypeList, $templateInfo->subType);
        $deptList = $this->loadModel('dept')->getDeptListByIds($updateDeptIds, 'id,name');
        if($deptList){
            $deptList = array_column($deptList, null, 'id');
        }
        //部门维度人员修改信息
        $deptExtLogList = [];
        //日志信息
        $subLogInfo = '模板ID:'. $templateId. ' 分类:' .$typeDesc . ' 子类:'. $subTypeDesc. " 日期:". $dutyDate;
        $deptLogInfo = $subLogInfo;
        foreach ($updateDayDetailLog as $deptId => $updateDayDetail){
            $deptInfo = zget($deptList, $deptId);
            $templateDeptInfo = zget($templateDeptList, $deptId);
            $templateDeptId = $templateDeptInfo->id; //部门维度的信息ID
            $deptName = $deptInfo->name;
            $oldUserList = $updateDayDetail['old'];
            $newUserList = $updateDayDetail['new'];
            $oldUsers = implode(',', $oldUserList);
            $newUsers = implode(',', $newUserList);

            $subLogInfo .= "<br/>{$deptName} 值班人员修改前:{$oldUsers} 修改后:{$newUsers}";
            //部门变更排班
            $deptExtLogInfo = new stdClass();
            $deptExtLogInfo->old = '';
            $deptExtLogInfo->new = $deptLogInfo . "值班人员修改前:{$oldUsers} 修改后:{$newUsers}";

            $deptExtLogList[$templateDeptId] = $deptExtLogInfo;
        }

        //编辑排班信息
        $schedulingInfo = new stdClass();
        $schedulingInfo->old = '';
        $schedulingInfo->new = $subLogInfo;

        //扩展信息
        $extChangeInfo = [
            'extLogInfo'     => $schedulingInfo,
            'deptExtLogInfo' => $deptExtLogList,
        ];
        $logChange = common::createChanges($dayInfo, $dayUpdateParams, $extChangeInfo);
        return $logChange;
    }

    //获取天-天详情
    public function getByIdDay($dayId){
        if (!$dayId){
            return false;
        }
        $day = $this->dao->select("t0.id,templateId,dutyDate,dutyGroupLeader,type,subType")->from(TABLE_RESIDENT_SUPPORT_DAY)->alias("t0")
            ->leftJoin(TABLE_RESIDENT_SUPPORT_TEMPLATE)->alias('t1')->on('t0.templateId = t1.id')
            ->where("t0.id")->eq($dayId)->andwhere("t0.enable")->eq(1)->fetch();
        $details = $this->dao->select("dutyUser,dutyUserDept")->from(TABLE_RESIDENT_SUPPORT_DAY_DETAIL)->where("dayId")->eq($dayId)->andwhere('deleted')->eq(0)->andwhere('dutyUser')->ne('')->fetchall();
        $day->details = $details;
        return $day;
    }

    /**
     *获得值班部门的所有信息
     *
     * @param $templateDeptId
     * @return bool
     */
    public function getTemplateDeptAllInfoById($templateDeptId){
        if(!$templateDeptId){
            return false;
        }
        //模板部门信息
        $templateDeptInfo = $this->loadModel('residentsupport')->getTemplateDeptInfoById($templateDeptId);
        if(!$templateDeptInfo) {
            return false;
        }

        //模板ID
        $templateId = $templateDeptInfo->templateId;
        $deptId     = $templateDeptInfo->deptId;
        //值班人员
        $whereCondition = [
            'templateId' => $templateId,
            'dutyUserDept' => $deptId,
        ];
        $dutyUsersList = $this->loadModel('residentsupport')->getDutyUserListByCondition($whereCondition, 'dayId,dutyUser');
        //值班人员
        $dutyUsers = array_column($dutyUsersList, 'dutyUser');
        $dutyUsers = array_flip(array_flip(array_filter($dutyUsers))); //去空去重复
        //值班日
        $dayIds = array_column($dutyUsersList, 'dayId');
        $dayList = $this->loadModel('residentsupport')->getTempDayListByIds($dayIds, 'dutyDate,dutyGroupLeader');
        $dutyDates = array_column($dayList, 'dutyDate');
        $dutyGroupLeaders = array_column($dayList, 'dutyGroupLeader');

        $templateDeptInfo->dutyUsers        = $dutyUsers;
        $templateDeptInfo->dayids           = $dayIds;
        $templateDeptInfo->dutyDates        = $dutyDates;
        $templateDeptInfo->dutyGroupLeaders = $dutyGroupLeaders;
        //值班组长完整信息
        $dutyGroupLeaderList = $this->getDutyGroupLeaderList($dutyGroupLeaders);
        $templateDeptInfo->dutyGroupLeaderList = $dutyGroupLeaderList;
        //存在变更排班
//        if($templateDeptInfo->isModify == $this->lang->residentsupport->temDeptModifyStatusList[2]){
//            $modifyInfo = new stdClass();
//            //值班人员
//            $whereCondition = [
//                'templateId'   => $templateId,
//                'dutyUserDept' => $deptId,
//                'status'       => $this->lang->residentsupport->dayDetailStatusList[2],
//            ];
//            $dutyUsersList = $this->loadModel('residentsupport')->getDutyUserListByCondition($whereCondition, 'dayId,dutyUser');
//            if(!empty($dutyUsersList)){
//                //值班日
//                $dayIds = array_column($dutyUsersList, 'dayId');
//                $whereCondition = [
//                    'templateId'   => $templateId,
//                    'dutyUserDept' => $deptId,
//                    'dayId'        => $dayIds,
//                ];
//                $dutyUsersList = $this->loadModel('residentsupport')->getDutyUserListByCondition($whereCondition);
//                //值班人员
//                $dutyUsers = array_column($dutyUsersList, 'dutyUser');
//                $dutyUsers = array_flip(array_flip(array_filter($dutyUsers))); //去空去重复
//                $formatDutyUsersList = [];
//                foreach ($dutyUsersList as $val){
//                    $dayId = $val->dayId;
//                    $formatDutyUsersList[$dayId][] = $val;
//                }
//
//                $dayList = $this->loadModel('residentsupport')->getTempDayListByIds($dayIds, 'id,dutyDate,dutyGroupLeader');
//                $dutyDates = array_column($dayList, 'dutyDate');
//                $dutyGroupLeaders = array_column($dayList, 'dutyGroupLeader');
//                $dayList = array_column($dayList, null, 'id');
//                //值班日列表
//                $modifyInfo->dayList = $dayList;
//                $modifyInfo->formatDutyUsersList = $formatDutyUsersList;
//                $modifyInfo->dutyUsers        = $dutyUsers;
//                $modifyInfo->dayids           = $dayIds;
//                $modifyInfo->dutyDates        = $dutyDates;
//                $modifyInfo->dutyGroupLeaders = $dutyGroupLeaders;
//                //值班组长完整信息
//                $dutyGroupLeaderList = $this->getDutyGroupLeaderList($dutyGroupLeaders);
//                $modifyInfo->dutyGroupLeaderList = $dutyGroupLeaderList;
//            }
//            $templateDeptInfo->modifyInfo = $modifyInfo;
//        }
        return $templateDeptInfo;
    }

    /**
     *获得变更信息
     *
     * @param $templateId
     * @param $dayIds
     * @return stdClass
     */
    public function getTemplateDeptModifyInfo($templateId, $dayIds){
        $modifyInfo = new stdClass();
        if(!($templateId && $dayIds)){
            return $modifyInfo;
        }
        //值班人员
        $whereCondition = [
            'templateId' => $templateId,
            'id'  => $dayIds,
        ];
        $dayList = $this->loadModel('residentsupport')->getDutyDayListByCondition($whereCondition);
        if(!empty($dayList)) {
            //值班日
            $dayIds = array_column($dayList, 'id');
            $whereCondition = [
                'templateId' => $templateId,
                'dayId' => $dayIds,
            ];
            $dutyUsersList = $this->loadModel('residentsupport')->getDutyUserListByCondition($whereCondition);
            //值班人员
            $dutyUsers = array_column($dutyUsersList, 'dutyUser');
            $dutyUsers = array_flip(array_flip(array_filter($dutyUsers))); //去空去重复
            //参与部门
            $dutyUserDeptIds = array_column($dutyUsersList, 'dutyUserDept');
            $dutyUserDeptIds = array_flip(array_flip(array_filter($dutyUserDeptIds))); //去空去重复
            $formatDutyUsersList = [];
            foreach ($dutyUsersList as $val) {
                $dayId = $val->dayId;
                $formatDutyUsersList[$dayId][] = $val;
            }

            $dutyDates = array_column($dayList, 'dutyDate');
            $dutyGroupLeaders = array_column($dayList, 'dutyGroupLeader');
            $dayList = array_column($dayList, null, 'id');
            //值班日列表
            $modifyInfo->dayList = $dayList;
            $modifyInfo->formatDutyUsersList = $formatDutyUsersList;
            $modifyInfo->dutyUsers = $dutyUsers;
            $modifyInfo->dutyUserDeptIds = $dutyUserDeptIds;
            $modifyInfo->dayIds = $dayIds;
            $modifyInfo->dutyDates = $dutyDates;
            $modifyInfo->dutyGroupLeaders = $dutyGroupLeaders;
            //值班组长完整信息
            $dutyGroupLeaderList = $this->getDutyGroupLeaderList($dutyGroupLeaders);
            $modifyInfo->dutyGroupLeaderList = $dutyGroupLeaderList;
        }
        return $modifyInfo;
    }

    /**
     * 获得值班组长信息
     *
     * @param $dutyGroupLeaders
     * @return array
     */
    public function getDutyGroupLeaderList($dutyGroupLeaders){
        $data = [];
        if(!$dutyGroupLeaders){
            return $data;
        }
        $dutyGroupLeaders = array_flip(array_flip($dutyGroupLeaders));
        $userList = $this->loadModel('user')->getUserInfoListByAccounts($dutyGroupLeaders, 'account,realname,dept');
        $deptIds = array_column($userList, 'dept');
        $deptList = $this->loadModel('dept')->getDeptListByIds($deptIds, 'id,name');
        $deptList = array_column($deptList, null, 'id');
        foreach ($dutyGroupLeaders as $userAccount){
            $uerInfo = zget($userList, $userAccount);
            $deptId = zget($uerInfo, 'dept');
            $deptInfo = zget($deptList, $deptId);
            $deptName = zget($deptInfo, 'name');

            $temp = new stdClass();
            $temp->dutyGroupLeader = $userAccount;
            $temp->realname = zget($uerInfo, 'realname');
            $temp->deptId = $deptId;
            $temp->deptName = $deptName;
            $data[] = $temp;
        }
        return $data;
    }



    /**
     * Send mail.
     *
     * @param  int    $templateDeptId
     * @param  int    $actionID
     * @access public
     * @return void
     */
    public function sendmail($templateDeptId, $actionID)
    {
        $this->loadModel('mail');
        $templateDeptInfo = $this->residentsupport->getTemplateDeptAllInfoById($templateDeptId);
        if(!$templateDeptInfo) {
            return false;
        }
        $templateId = $templateDeptInfo->templateId;
        $status       = $templateDeptInfo->status;
        $templateInfo = $templateDeptInfo->templateInfo;
        $deptId       = $templateDeptInfo->deptId;
        $deptInfo = $this->loadModel('dept')->getByID($deptId);
        $deptName = $deptInfo->name;

        $users  = $this->loadModel('user')->getPairs('noletter');
        /* 获取后台通知中配置的邮件发信。*/
        $this->app->loadLang('custommail');
        /* Get action info. */
        $action          = $this->loadModel('action')->getById($actionID);
        $history         = $this->action->getHistory($actionID);
        $action->history = array();
        if($status == $this->lang->residentsupport->temDeptStatusList['pass']){ //通知类邮件
            $mailConf = isset($this->config->global->setResidentSupportNoticeMail) ? $this->config->global->setResidentSupportNoticeMail : '{"mailTitle":"","variables":[],"mailContent":""}';
            $mailConf   = json_decode($mailConf);
            $typeDesc    = zget($this->lang->residentsupport->typeList, $templateInfo->type);
            $subTypeDesc = zget($this->lang->residentsupport->subTypeList, $templateInfo->subType);
            $templateTypeInfo = $typeDesc . '-' . $subTypeDesc;
            $opAction = $action->action;
            if($opAction == 'modifyscheduling'){
                $dutyDateInfo = $deptName;
                $extra = $action->extra;
                $modifyDutyDate = substr($extra, strpos($extra, '日期:')+7, 10);
                if($modifyDutyDate){
                    $templateDeptInfo->modifyDutyDate = $modifyDutyDate;
                    $dutyDayInfo = $this->getDutyDayInfo($templateId, $modifyDutyDate);
                    $dayId = $dutyDayInfo->id;
                    $dayIds = array($dayId);
                    $templateDeptInfo->modifyInfo = $this->getTemplateDeptModifyInfo($templateId, $dayIds);
                    $dutyDateInfo .= $modifyDutyDate;
                }
                $suffixInfo = "变更排班";
            }else{
                $dutyDateInfo = $templateInfo->startDate . '~' . $templateInfo->endDate;
                $suffixInfo = "{$deptName}排班审批通过";
            }
            $mailConf->variables = $templateTypeInfo . $dutyDateInfo . $suffixInfo;
            $mailTitle = vsprintf($mailConf->mailTitle, $mailConf->variables);
        }else{ //待办类邮件
            $mailConf = isset($this->config->global->setResidentSupportBacklogMail) ? $this->config->global->setResidentSupportBacklogMail : '{"mailTitle":"","variables":[],"mailContent":""}';
            $mailConf   = json_decode($mailConf);
            $mailTitle = vsprintf($mailConf->mailTitle, $mailConf->variables);
        }

        $browseType = 'residentsupport';

        /* Get mail content. */
        $oldcwd     = getcwd();
        $modulePath = $this->app->getModulePath($appName = '', $browseType);
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

        /* 处理邮件标题。*/
        $subject = $mailTitle;
        $sendUsers = $this->getToAndCcList($templateDeptInfo, $action);
        //日志详情置空
        $action->history = array();

        if(!$sendUsers) return;
        $toList = $sendUsers['toList'];
        $ccList = $sendUsers['ccList'];

//        echo '<pre>';
//        print_r($templateDeptInfo);
//        echo '</pre>';
//        echo '<pre>';
//        print_r($toList);
//        echo '</pre>';
//        echo '<pre>';
//        print_r($ccList);
//        echo '</pre>';
//        echo $mailContent;
//        exit();

        /* Send emails. */
        $this->mail->send($toList, $subject, $mailContent, $ccList);
        if($this->mail->isError()) error_log(join("\n", $this->mail->getError()));
    }

    /**
     * sendModifySchedulingMail 变更排班发送邮件
     *
     * $dayId
     * @access public
     * @return void
     */
    public function sendModifySchedulingMail($dayId, $actionID)
    {
        $this->loadModel('mail');
        //值班日信息
        $dutyDateInfo = $this->residentsupport->getTempDayInfoById($dayId);
        if(!$dutyDateInfo) {
            return false;
        }
        $users  = $this->loadModel('user')->getPairs('noletter');
        $templateId = $dutyDateInfo->templateId;
        $dutyDate = $dutyDateInfo->dutyDate;
        //模板信息
        $templateInfo = $this->residentsupport->getTemplateInfoById($templateId);
        $dutyDateInfo->templateInfo = $templateInfo;
        //天变更信息
        $dayIds = array($dayId);
        $dutyDateInfo->modifyInfo = $this->getTemplateDeptModifyInfo($templateId, $dayIds);

        /* 获取后台通知中配置的邮件发信。*/
        $this->app->loadLang('custommail');
        /* Get action info. */
        $action          = $this->loadModel('action')->getById($actionID);
        $action->history = $this->action->getHistory($actionID);

        $mailConf = isset($this->config->global->setResidentSupportNoticeMail) ? $this->config->global->setResidentSupportNoticeMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf   = json_decode($mailConf);
        $typeDesc   = zget($this->lang->residentsupport->typeList, $templateInfo->type);
        $subTypeDesc = zget($this->lang->residentsupport->subTypeList, $templateInfo->subType);
        $templateTypeInfo = $typeDesc . '-' . $subTypeDesc;

        $mailConf->variables = $templateTypeInfo . $dutyDate . '变更排班';
        $mailTitle = vsprintf($mailConf->mailTitle, $mailConf->variables);

        $browseType = 'residentsupport';

        /* Get mail content. */
        $oldcwd     = getcwd();
        $modulePath = $this->app->getModulePath($appName = '', $browseType);
        $viewFile   = $modulePath . 'view/sendmodifyschedulingmail.html.php';
        chdir($modulePath . 'view');

        if(file_exists($modulePath . 'ext/view/sendmodifyschedulingmail.html.php'))
        {
            $viewFile = $modulePath . 'ext/view/sendmodifyschedulingmail.html.php';
            chdir($modulePath . 'ext/view');
        }

        ob_start();
        include $viewFile;
        //foreach(glob($modulePath . 'ext/view/sendmail.*.html.hook.php') as $hookFile) include $hookFile;
        $mailContent = ob_get_contents();
        ob_end_clean();
        chdir($oldcwd);

        /* 处理邮件标题。*/
        $subject = $mailTitle;
        $sendUsers = $this->getModifySchedulingToAndCcList($dutyDateInfo, $action);
        
        if(!$sendUsers) return;
        $toList = $sendUsers['toList'];
        $ccList = $sendUsers['ccList'];
        /* Send emails. */
        $this->mail->send($toList, $subject, $mailContent, $ccList);
        if($this->mail->isError()) error_log(join("\n", $this->mail->getError()));
    }

    /**
     *获得变更排班邮件的收件人和抄送人
     *
     * @param $dutyDateInfo
     * @param $action
     * @return array
     */
    public function getModifySchedulingToAndCcList($dutyDateInfo, $action){
        $toList = '';
        $ccList = '';
        $data = [
            'toList' => $toList,
            'ccList' => $ccList,
        ];
        if(!$dutyDateInfo){
            return $data;
        }
        $type = $dutyDateInfo->templateInfo->type;
        //产创部门审核人
        $pdReview = $this->loadModel('residentsupport')->getPdReviewDealUsers($type);
        $pdReviews = array($pdReview);
        $allManagerUsers = [];
        //获得收件人以及抄送人
        $modifyInfo = $dutyDateInfo->modifyInfo;
        if(isset($modifyInfo)){
            $dutyUsers        = $modifyInfo->dutyUsers;
            $dutyGroupLeaders = $modifyInfo->dutyGroupLeaders;
            //收件人（值班人员+值班组长）
            $noticeUsers = array_merge($dutyUsers, $dutyGroupLeaders);
            if(!empty($noticeUsers)){
                $noticeUsers = array_flip(array_flip(array_filter($noticeUsers)));
                $toList = implode(',', $noticeUsers);
            }
            //抄送人（部门负责人+产创部门审核人）
            $dutyUserDeptIds = $modifyInfo->dutyUserDeptIds;
            $deptIds = implode(',', $dutyUserDeptIds);
            $deptList = $this->loadModel('dept')->getByIDs($deptIds);
            if($deptList){
                foreach ($deptList as $val){
                    if($val->manager){
                        $managerUsers = explode(',', $val->manager);
                        $allManagerUsers = array_merge($allManagerUsers, $managerUsers);
                    }
                }
            }
        }
        $ccUsers = array_merge($pdReviews, $allManagerUsers);
        if($ccUsers){
            $ccUsers = array_flip(array_flip(array_filter($ccUsers)));
            $ccList = implode(',', $ccUsers);
        }

        $data['toList'] = $toList;
        $data['ccList'] = $ccList;
        return $data;
    }

    /**
     *获得邮件的收件人和抄送人
     *
     * @param $templateDeptInfo
     * @param $action
     * @return array
     */
    public function getToAndCcList($templateDeptInfo, $action){
        $toList = '';
        $ccList = '';
        $data = [
            'toList' => $toList,
            'ccList' => $ccList,
        ];
        if(!$templateDeptInfo){
            return $data;
        }
        //日志详情信息
        $history = [];
        if(!empty($action) && isset($action->history) && !empty($action->history)){
            $history = $action->history;
        }
        $status  = $templateDeptInfo->status;
        $toList  = $templateDeptInfo->dealUsers;
        if($status == $this->lang->residentsupport->temDeptStatusList['waitDeptReview']){ //待部门审批
            $mailto = '';
            if(!empty($history)){
                foreach ($history as $val) {
                    if ($val->field == 'mailto') {
                        $mailto = $val->new;
                        if ($mailto) {
                            $mailtoArray = explode(' ', $mailto);
                            $mailto = implode(',', $mailtoArray);
                        }
                        break;
                    }
                }
            }
            $ccList = $mailto;
        }elseif ($status == $this->lang->residentsupport->temDeptStatusList['pass']){ //通知类邮件
            //先获得抄送人
            $deptId = $templateDeptInfo->deptId;
            $deptInfo = $this->loadModel('dept')->getByID($deptId);
            //部门负责人
            $manager  = $deptInfo->manager;
            $type     = $templateDeptInfo->templateInfo->type;
            //产创部门审核人
            $pdReview = $this->loadModel('residentsupport')->getPdReviewDealUsers($type);
            //抄送人（部门负责人+产创部门审核人）
            $ccList = trim($manager, ',');
            if($pdReview){
                $ccList .= ','. $pdReview;
            }

            //获得收件人
            $dutyUsers = [];
            $dutyGroupLeaders = [];
            $opAction = $action->action;
            if($opAction == 'modifyscheduling'){ //变更排班通知邮件
                $modifyInfo = $templateDeptInfo->modifyInfo;
                if(isset($modifyInfo)){
                    $dutyUsers        = $modifyInfo->dutyUsers;
                    $dutyGroupLeaders = $modifyInfo->dutyGroupLeaders;
                }
            }else{ //审核通过通知邮件
                if(isset($templateDeptInfo->dutyUsers)){
                    $dutyUsers = $templateDeptInfo->dutyUsers;
                }
                if(isset($templateDeptInfo->dutyGroupLeaders)){
                    $dutyGroupLeaders = $templateDeptInfo->dutyGroupLeaders;
                }
            }
            //收件人（值班人员+值班组长）
            $noticeUsers = array_merge($dutyUsers, $dutyGroupLeaders);
            if(!empty($noticeUsers)){
                $noticeUsers = array_flip(array_flip(array_filter($noticeUsers)));
                $toList = implode(',', $noticeUsers);
            }
        }

        $data['toList'] = $toList;
        $data['ccList'] = $ccList;
        return $data;

    }
    //根据值班日期批量获取天信息
    public function dateGetDayInfos($dutyDate,$filed = "*",$type="",$dayId=''){
        if (!strtotime($dutyDate)){
            return false;
        }
        $day = $this->dao->select($filed)->from(TABLE_RESIDENT_SUPPORT_DAY)->alias("t1")
            ->leftjoin(TABLE_RESIDENT_SUPPORT_TEMPLATE)->alias("t2")
            ->on("t1.templateId = t2.id")
            ->where("t1.dutyDate")
            ->eq($dutyDate)
            ->andWhere("t1.enable")->eq(1)
            ->beginIF($dayId != '')->andWhere('t1.id')->eq($dayId)->fi()
            ->beginIF($type != '')->andWhere('t2.type')->eq($type)->fi()
            ->fetchall();
        return $day;
    }
    //根据值班类型，值班子类，值班日期获取是否有已启用的模板
    public function getEnableDay($type,$subType,$dutydate){
        $res = $this->dao->select("t1.dutyDate,t2.id")->from(TABLE_RESIDENT_SUPPORT_DAY)->alias("t1")
            ->leftjoin(TABLE_RESIDENT_SUPPORT_TEMPLATE)->alias("t2")
            ->on("t1.templateId = t2.id")
            ->where("t1.enable")->eq('1')
            ->andWhere("t1.deleted")->eq(0)
            ->andWhere("t2.type")->eq($type)
            ->andWhere("t1.dutyDate")->in($dutydate)
            ->andWhere('t2.subType')->ne($subType)
            ->fetchall();
        return $res;
    }


    /**
     * 获得审核节点审核人信息
     *
     * @param $templateDeptIds
     * @return array
     */
    public function getReviewNodeFormatReviewerList($templateDeptIds){
        $data = array();
        //审核节点
        $reviewNodeList =  $this->dao->select('id, objectID, version, stage, status, nodeCode')
            ->from(TABLE_REVIEWNODE)
            ->Where('objectID')->in($templateDeptIds)
            ->andWhere('objectType')->eq('residentsupport')
            ->orderBy('version, stage')
            ->fetchAll();
        if(empty($reviewNodeList)){
            return $data;
        }
        $nodeIds = array_column($reviewNodeList,'id');

        //审核人
        $reviewerList = $this->dao->select('*')
            ->from(TABLE_REVIEWER)
            ->Where('node')->in($nodeIds)
            ->groupBy('node, reviewer, status')
            ->orderBy('id')
            ->fetchAll();
        if(empty($reviewerList)){
            return $data;
        }
        //每个节点下的审核人
        $reviewers = [];
        foreach ($reviewerList as $val){
            $node = $val->node;
            $val->extraInfo = [];
            if($val->extra){
                $val->extraInfo = json_decode($val->extra, true);
            }
            $reviewers[$node][] = $val;
        }

        $temp = [];
        foreach ($reviewNodeList as $val){
            $objectID = $val->objectID;
            $version  = $val->version;
            $nodeId   = $val->id;
            $nodeCode = $val->nodeCode;
            $currentNodeReviewers = [];
            if(isset($reviewers[$nodeId])){
                $currentNodeReviewers = $reviewers[$nodeId];
            }
            $temp[$objectID][$version][$nodeCode]['info'] = $val;
            $temp[$objectID][$version][$nodeCode]['data'] = $currentNodeReviewers;
            $temp[$objectID][$version][$nodeCode]['total'] = count($currentNodeReviewers);
        }
        foreach ($templateDeptIds as $templateDeptId){
            if(!isset($temp[$templateDeptId])){
                $data[$templateDeptId]['total'] = 0;
                $data[$templateDeptId]['data'] = [];
            }else{
                $versionList = $temp[$templateDeptId];
                $deptTotal = 0;
                $deptData  = [];
                foreach ($versionList as $version => $nodeList){
                    $versionTotal = 0;
                    $versionData  = $nodeList;
                    foreach ($nodeList as $nodeId => $nodeInfo){
                        $currentNodeTotal = $nodeInfo['total'];
                        $versionTotal += $currentNodeTotal;
                    }
                    $deptTotal += $versionTotal;
                    $deptData[$version]['total'] = $versionTotal;
                    $deptData[$version]['data']  = $versionData;

                }
                $data[$templateDeptId]['total'] = $deptTotal;
                $data[$templateDeptId]['data']  = $deptData;
            }
        }

        return $data;
    }
    public function getDays($type,$subType,$dutyDate){
        $day = $this->dao->select("t1.id,t1.dutyDate,type,subType,templateId,startDate,endDate,dutyGroupLeader")->from(TABLE_RESIDENT_SUPPORT_DAY)->alias("t1")
            ->leftJoin(TABLE_RESIDENT_SUPPORT_TEMPLATE)->alias('t2')
            ->on('t1.templateId = t2.id')
//            ->where('t1.enable')->eq($enable)
            ->where('t1.deleted')->eq(0)
            ->andwhere('t2.type')->eq($type[0])
            ->andwhere('t2.subType')->eq($subType[0])
            ->andwhere('t2.deleted')->eq(0)
//            ->andwhere('t2.enable')->eq($enable)
            ->andwhere('t1.dutyDate')->ge($dutyDate[0])
            ->andwhere('t1.dutyDate')->le($dutyDate[count($dutyDate)-1])
            ->andwhere("endDate")->ge(date("Y-m-d"))
            ->orderby('startDate_asc')
            ->fetchAll();
        return $day;
    }
    /**
     *检查是否允许启用排班
     *
     * @param $enableCondition
     * @return array
     */
    function checkIAllowEnableScheduling($enableCondition){
        $res = array(
            'result' => false,
            'message' => $this->lang->error->accessDenied,
            'data' => [],
        );
        if (!($enableCondition)){
            $res['message'] = $this->lang->common->errorParamId;
            return $res;
        }
        $checkRes = $this->checkSearchTemplateCondition($enableCondition);
        if(!$checkRes['result']){
            $res['message'] = $checkRes['message'];
            return $res;
        }
        $templateInfo = $this->loadModel('residentsupport')->getTemplateInfoBySchedulingCondition($enableCondition);
        if(!$templateInfo){
            $res['message'] = $this->lang->residentsupport->checkCommonResultList['schedulingSearchEmpty'];
            return $res;
        }
        $templateId = $templateInfo->id;
        //查询模板是否存在该天值班信息
        $dayList = $this->residentsupport->getTempDayList($templateId, $enableCondition->startDate, $enableCondition->endDate, 'id, dutyDate');
        if(empty($dayList)){
            $res['message'] = $this->lang->residentsupport->checkSchedulingResultList['dayEmpty'];
            return $res;
        }
        $dayIds = array_column($dayList, 'id');
        //查询是否存在未启用的
        $notEnableDayIds = $this->residentsupport->getNotEnableDayIds($dayIds);
        if(empty($notEnableDayIds)){
            $res['message'] = $this->lang->residentsupport->checkSchedulingResultList['needSchedulingEmpty'];
            return $res;
        }
        //查询该天所在的部门信息
        $condition = array(
            'templateId' => $templateId,
            'dayId' => $dayIds
        );
        //排班详情
        $dutyUserList = $this->residentsupport->getDutyUserListByCondition($condition, 'dutyUserDept');
        if(empty($dutyUserList)){
            $res['message'] = $this->lang->residentsupport->checkSchedulingResultList['dayEmpty'];
            return $res;
        }
        $deptIds = array_column($dutyUserList, 'dutyUserDept');
        $unCheckPassDutyDeptList = $this->residentsupport->getUnCheckPassDutyDeptList($templateId, $deptIds, 'id');
        if(!empty($unCheckPassDutyDeptList)){
            $res['message'] = sprintf($this->lang->residentsupport->checkCommonResultList['dutyDeptCheckError'], $this->lang->residentsupport->enableScheduling);
            return $res;
        }
        $dutyDates = array_column($dayList, 'dutyDate');

        $res['result'] = true;
        $res['data'] = [
            'templateInfo' => $templateInfo,
            'dayIds'       => $dayIds,
            'dutyDates'    => $dutyDates,
        ];
        return $res;
    }
    //根据类型获取模板
    public function getTemplateInfoById($templateId){
        $info = $this->dao->select("*")->from(TABLE_RESIDENT_SUPPORT_TEMPLATE)
            ->where('templateId')->eq($templateId)
            ->fetch();
        return $info;
    }
}

