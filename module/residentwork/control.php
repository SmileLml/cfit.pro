<?php
class residentwork extends control
{
    /**
     * 值班日志视图
     *
     * @param string $browseType
     * @param int $param
     * @param string $orderBy
     * @param int $recTotal
     * @param int $recPerPage
     * @param int $pageID
     */
    public function browse($browseType = 'enable', $param = 0, $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        /* By search. */
        $this->app->loadLang("residentsupport");
        $depts = $this->loadModel('dept')->getOptionMenu();
        $depts[0] = '';
        $emptyArr = array('0'=>'');
        $this->config->residentwork->search['params']['dutyUserDept']['values']  = $depts;//值班部门
        $this->config->residentwork->search['params']['dutyType']['values']      =  $emptyArr + $this->lang->residentsupport->typeList;
        $this->config->residentwork->search['params']['timeType']['values']      =  $emptyArr + $this->lang->residentsupport->durationTypeList;
        $this->config->residentwork->search['params']['dateType']['values']      =  $emptyArr + $this->lang->residentsupport->dateTypeList;
        $this->config->residentwork->search['params']['dutyPlace']['values']     =  $emptyArr + $this->lang->residentsupport->areaList;
        $this->config->residentwork->search['params']['isEmergency']['values']   =  $emptyArr + $this->lang->residentwork->importantTimeList;
        $this->config->residentwork->search['params']['postTypeInfo']['values']  =  $emptyArr + $this->lang->residentsupport->postType;

        $browseType = strtolower($browseType);
        $queryID = ($browseType == 'bysearch')  ? (int)$param : 0;
        $actionURL = $this->createLink('residentwork', 'browse', "browseType=bySearch&param=myQueryID");
        $this->loadModel('residentwork')->buildSearchForm($queryID, $actionURL);
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);
        $residentSupports = $this->residentwork->getList($browseType, $queryID, $orderBy, $pager);
        $this->view->title = $this->lang->residentwork->common;
        $this->view->residentsupports  = $residentSupports;
        /* 设置需求详情页面返回的url连接。*/
        $this->session->set('residentworkList', $this->app->getURI(true), 'backlog');
        $users = $this->getUsers();
        $this->view->param      = $param;
        $this->view->queryID    = $queryID;
        $this->view->pager      = $pager;
        $this->view->recTotal   = $recTotal;
        $this->view->recPerPage = $recPerPage;
        $this->view->pageID     = $pageID;
        $this->view->orderBy    = $orderBy;
        $this->view->browseType = $browseType;
        $this->view->depts = $depts;
        $this->view->users = $users;
        $this->display();
    }

    public function getUsers(){
        $users = $this->loadModel('user')->getPairs('noletter');
        $outsideList1 = $this->loadModel('user')->getUsersNameByType('outsideExpertType');
        $outsideList2 = $this->loadModel('user')->getUsersNameByType('outside');
        return array_merge($users, $outsideList1, $outsideList2);
    }

    public function view($dayId = 0)
    {
        $this->app->loadLang("residentsupport");
        $depts = $this->loadModel('dept')->getOptionMenu();
        $depts[0] = '';
        /* 查询需求意向详情及其相关变量信息，用于详情展示。*/
        $residentInfo = $this->residentwork->view($dayId);
        $this->view->residentInfo   = $residentInfo;
        $this->view->title   = '值班视图详情';
        $this->view->depts = $depts;
        $this->view->users   = $this->loadModel('user')->getPairs('noclosed|noletter');
        $this->view->actions = $this->loadModel('action')->getList('residentsupportday', $dayId);
        $this->display();
    }

    /**
     *填写值班日志
     *
     * @param $dayId
     */
    public function recordDutyLog($dutyDate,$dayId = "",$loadType = 1){
        $onlybody = isset($_GET['onlybody']) ? $_GET['onlybody'] : '';
        $this->app->loadLang("residentsupport");
        if ($_POST){
            $res = $this->residentwork->recordDutyLog();
            if (!$res['result']){
                $response['result']  = 'fail';
                $response['message'] = $res['message'];
                $this->send($response);
                exit;
            }
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $response['result']  = 'success';
            $response['message'] = "保存成功";
            $response['locate']  = 'parent';
            if ($onlybody == ''){
                $response['locate'] = $this->createLink("residentwork","view","id=".$dayId);
            }
            $this->send($response);
            exit;
        }
        $this->view->loadType = $loadType;

        $dateTypeList = $this->lang->residentsupport->dateTypeList;
        $areaList = array(''=>'') + $this->lang->residentsupport->areaList;
        $users = $this->loadModel('user')->getPairs('noletter');
        $outsideList1 = $this->loadModel('user')->getUsersNameByType('outsideExpertType');
        $outsideList2 = $this->loadModel('user')->getUsersNameByType('outside');
        $users = array_merge($users, $outsideList1, $outsideList2);

        $this->view->users = $users;
        $this->view->areaList = $areaList;
        $this->view->dateTypeList = $dateTypeList;

        $day = new stdClass();
        $day->oldDate = $dutyDate;
        $dutyDate = str_replace(',','-',$dutyDate);
        $day->dutyDate = $dutyDate;
        $res = $this->loadModel("residentsupport")->dateGetDayInfos($dutyDate,"t1.id,t2.type,t2.subType",'',$dayId);

        if ($onlybody != ''){
            $res = $this->loadModel("residentsupport")->dateGetDayInfos($dutyDate,"t1.id,t2.type,t2.subType",'','');
        }
        $typeId = array_unique(array_column($res,'type'));
        $subTypeId = array_unique(array_column($res,'subType'));
        $type = [''=>''];
        $typeFirst = "";
        foreach ($typeId as $ti) {
            foreach ($this->lang->residentsupport->typeList as $k1=>$t1) {
                if ($ti == $k1){
                    if ($typeFirst == ''){
                        $typeFirst = $t1;
                    }
                    $type[$ti] = $t1;
                }
            }
        }
        $subType = [''=>''];
        $subTypeList = $this->lang->residentsupport->subTypeList;
        $typeList = $this->lang->residentsupport->typeList;
        $checkType = array_search($typeFirst,$typeList);
        foreach ($res as $si) {
            foreach ($subTypeList as $k2=>$t2) {
                if ($si->subType == $k2 && $si->type == $checkType){
                    $subType[$k2] = $t2;
                }
            }
        }
        $this->view->title = $this->lang->residentsupport->common;
        $this->view->list = json_encode($res);
        $this->view->subTypeList = $subType;
        $this->view->typeList = $type;
        if ($dayId == ''){
            $day->mailCtoUsers = '';
            $day->dutyUser = '';
            $day->area = '';
            $day->isEmergency = 1;
            $day->files = '';
            $day->remark = '';
            $day->logs = '';
            $day->warnLogs = '';
            $day->dayId = '';
            $day->dutyGroupLeader = '';
            $day->type = '';
            $day->subType = '';
            $day->analysis = '';
            $day->templateId = '';
            $day->pushStatus = 0;
            $this->view->day = $day;

            $this->display();
            exit;
        }
        $day = $this->loadModel("residentsupport")->getByIdDay($dayId);
        $createdDeptIds = array_column($day->details,'dutyUserDept');
        $dealUser = $this->loadModel("residentsupport")->getPdReviewDealUsers($day->type);
        //获取部门领导
        $deptList = $this->loadModel('dept')->getDeptListByIds($createdDeptIds, 'manager,name,id');
        $day->mailCtoUsers = implode(array_column($deptList,'manager'),',').','.$dealUser;
        $day->dutyUser = array_column($day->details,'dutyUser');
        $day->area = '';
        $day->isEmergency = 1;
        $day->files = '';
        $day->remark = '';
        $day->logs = '';
        $day->warnLogs = '';
        $day->dayId = $day->id;
        $day->analysis = '';
        $day->pushStatus = 0;
        $fileds = "id,dayId,dutyDate,groupLeader,area,dateType,isEmergency,remark,logs,warnLogs,mailCtoUsers,templateId,analysis,editedBy";
        if ($loadType == 1){
            $fileds .= ",pushStatus";
        }
        $work = $this->residentwork->getById($dayId,$fileds,true,$loadType);
        if ($work){
            $work->type = $day->type;
            $work->subType = $day->subType;
            $work->dutyGroupLeader = $work->groupLeader;
            $work->dutyUser = array_column($work->details,'realDutyuser');
            if ($loadType == 2){
                $work->pushStatus = 0;
            }
            $day = $work;
        }
        $day->dealUserName = $users[$dealUser];
        $this->view->day = $day;
        $this->view->dutyUser = json_encode($day->dutyUser);
        $this->display();
    }
    //根据值班日期、值班类型获取值班子类
    public function ajaxGetsubType(){
        $dutyDate = $_POST['dutyDate'];
        $type = $_POST['type'];
        $res = $this->loadModel("residentsupport")->dateGetDayInfos($dutyDate,"t2.type,t2.subType",$type);
        $subTypeId = array_unique(array_column($res,'subType'));
        $subType = [''=>''];
        foreach ($subTypeId as $si) {
            foreach ($this->lang->residentsupport->subTypeList as $k2=>$t2) {
                if ($si == $k2){
                    $subType[$si] = $t2;
                }
            }
        }
        echo html::select('subType', $subType, '', "class='form-control chosen' onchange='switchSubType()' required");
    }
    //值班日志导出
    public function workExport($browseType = 'enable', $param = 0, $orderBy = 'id_desc'){
        $this->app->loadLang("residentsupport");
        $this->app->loadConfig("residentsupport");
        if ($_POST){
            $type = $this->lang->residentsupport->typeList;
            $subType = $this->lang->residentsupport->subTypeList;
            $timeType = $this->lang->residentsupport->durationTypeList;
            $area = $this->lang->residentsupport->areaList;
            $postType = $this->lang->residentsupport->postType;
            $dateType = $this->lang->residentsupport->dateTypeList;
            $depts = $this->loadModel('dept')->getOptionMenu();
            $users = $this->getUsers();
            $browseType = strtolower($browseType);
            $queryID = ($browseType == 'bysearch')  ? (int)$param : 0;
            $residentSupports = $this->residentwork->getList($browseType, $queryID, $orderBy);
            $data = [];
            foreach ($residentSupports as $k=>$v) {
                $postTypeStr = "";
                $postTypeArr = explode(',',$v->detailInfo->postType);
                $postTypeList = getArrayValuesByKeys($postType, $postTypeArr);
                $postTypeStr .= implode(',', array_unique($postTypeList));
                $timeTypeStr = '';
                $timeTypeArr = explode(',',$v->detailInfo->timeType);
                $timeTypeList = getArrayValuesByKeys($timeType, $timeTypeArr);
                $timeTypeStr .= implode(',', array_unique($timeTypeList));

                $dutyUserDeptStr = '';
                $dutyUserDeptArr = explode(',',$v->detailInfo->dutyUserDept);
                $dutyUserDeptList = getArrayValuesByKeys($depts, $dutyUserDeptArr);
                $dutyUserDeptStr .= implode(',', array_unique($dutyUserDeptList));

                $dutyUserStr = '';
                $userArr = explode(',',$v->detailInfo->dutyUser);
                $dutyUserList = getArrayValuesByKeys($users, $userArr);
                $dutyUserStr .= implode(',', array_unique($dutyUserList));
                $requireInfoStr = '';
                $requireInfoArr = explode(',',$v->detailInfo->requireInfo);
                $requireInfoStr .= implode(',', array_unique($requireInfoArr));
                $arr = [
                    'dutyDate' => $v->dutyDate,
                    'type' => $type[$v->templateInfo->type],
                    'subType' => $subType[$v->templateInfo->subType],
                    'dutyGroupLeader' => $users[$v->dutyGroupLeader],
                    'requireInfo' => $requireInfoStr,
                    'postType' => $postTypeStr,
                    'dutyUserDept' => $dutyUserDeptStr,
                    'dutyUser' => $dutyUserStr,
                    'timeType' => $timeTypeStr,
                    'dutyDuration' => $v->detailInfo->timeSlot,
                    'actualLeader' => isset($v->workInfo->groupLeader) ? $users[$v->workInfo->groupLeader] : '',
                    'area' => isset($v->workInfo->area) ? $area[$v->workInfo->area] : '',
                    'dateType' => isset($v->workInfo->dateType) ? $dateType[$v->workInfo->dateType] : '',
                    'isEmergency' => isset($v->workInfo->isEmergency) ? $v->workInfo->isEmergency : '',
                    'remark' => isset($v->workInfo->remark) ? strip_tags($this->residentwork->toHtml($v->workInfo->remark)) : '',
                    'logs' => isset($v->workInfo->logs) ? strip_tags($this->residentwork->toHtml($v->workInfo->logs)) : '',
                    'warnLogs' => isset($v->workInfo->warnLogs) ? strip_tags($this->residentwork->toHtml($v->workInfo->warnLogs)) : '',
                    'analysis' => isset($v->workInfo->analysis) ? strip_tags($this->residentwork->toHtml($v->workInfo->analysis)) : '',
                    'createdBy' => isset($v->workInfo->editedBy) ? $v->workInfo->editedBy : '',
                    'createdDate' => isset($v->workInfo->editedDate) ? $v->workInfo->editedDate : '',
                    'user' => '',
                ];
                if ($arr['isEmergency'] == "") {
                    $arr['isEmergency'] = "";
                }
                if ($arr['isEmergency'] == 1) {
                    $arr['isEmergency'] = "是";
                }
                if ($arr['isEmergency'] == 2) {
                    $arr['isEmergency'] = "否";
                }
                if (isset($v->workInfo->workDetail) && count($v->workInfo->workDetail) > 0) {
                    $userWork = array_unique(array_column($v->workInfo->workDetail, 'realDutyuser'));
                    foreach ($userWork as $uv) {
                        $arr['user'] = $users[$uv].',';
                    }
                }
                $arr['user'] = rtrim($arr['user'] ,',');
                $data[] = (object)$arr;
            }
            foreach($this->config->residentwork->export->templateFields as $field) $fields[$field] = $this->lang->residentwork->exportFileds->$field;
            $this->post->set('fields', $fields);
            $this->post->set('kind', 'residentwork');
            $this->post->set('rows', $data);
            $this->post->set('width',   $this->config->residentwork->export->width);

            $this->post->set('fileName', $_POST['fileName']);
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }
        $this->display();
    }

    /**
     * 按照天的维度变更排班
     *
     * @param $dayId
     */
    public function modifyScheduling($dayId, $schedulingDeptType = 'selfDept', $browseType = 'enable', $param = 0, $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $this->loadModel('residentsupport');
        $this->app->loadLang('residentsupport');
        if ($_POST) {
            $templateId = $_POST['templateId'];
            $logChanges = $this->residentsupport->modifyScheduling($dayId);
            if (dao::isError()) {
                $response['result'] = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $isSetHistory = true;
            $extLogInfo     = $this->residentsupport->getLogChangesSpecialInfo($logChanges, 'extLogInfo');
            $deptExtLogInfo = $this->residentsupport->getLogChangesSpecialInfo($logChanges, 'deptExtLogInfo');
            //去掉一些不必要的参数信息
            $logChanges = $this->residentsupport->getLogChangesUnSetSpecialInfo($logChanges, array('extLogInfo', 'deptExtLogInfo'));
            //模板日志扩展信息
            $objectType = $this->lang->residentsupport->objectTypeList['resident_support_template'];
            $actionID = $this->loadModel('action')->create($objectType, $templateId, 'modifyscheduling', '', $extLogInfo, '', true);
            //部门日志扩展信息
            $objectType = $this->lang->residentsupport->objectTypeList['resident_support_template_dept'];
            if (is_array($deptExtLogInfo)) {
                foreach ($deptExtLogInfo as $templateDeptId => $deptExtLog) {
                    $actionID = $this->loadModel('action')->create($objectType, $templateDeptId, 'modifyscheduling', '', $deptExtLog, '', true);
                }
            }

            //天日志扩展信息
            $objectType = $this->lang->residentsupport->objectTypeList['resident_support_day'];
            $actionID = $this->loadModel('action')->create($objectType, $dayId, 'modifyscheduling', '', $extLogInfo, '', true);
            $this->residentsupport->sendModifySchedulingMail($dayId, $actionID);
            if($logChanges) {
                $this->action->logHistory($actionID, $logChanges);
            }
            //返回
            $params = "browseType=$browseType" . "&param=$param". "&orderBy=$orderBy" . "&recTotal=$recTotal" . "&recPerPage=$recPerPage" . "&pageID=$pageID";
            $response['result'] = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate'] = $this->createLink('residentwork', 'browse', $params);
            $this->send($response);
        }
        //获得日信息
        $tempDayInfo = $this->residentsupport->getTempDayInfoById($dayId);
        $checkRes = $this->residentwork->checkIsModifyScheduling($tempDayInfo);
        if ($checkRes['result']){
            $data = $checkRes['data'];
            //允许变更的部门
            $allowModifyDeptIds = $data['allowModifyDeptIds'];
            $templateId = $tempDayInfo->templateId;
            $dayIds = array($dayId);
            $dutyDate = $tempDayInfo->dutyDate;
            $userType = $this->residentsupport->getModifySchedulingUserType();
            $deptId = 0;
            if($schedulingDeptType == 'selfDept'){ //自己部门
                $deptId = $this->app->user->dept;
            }
            $dutyUserList = $this->residentsupport->getDutyUserListByTemplateId($templateId, $dayIds, $deptId);
            $dutyUserList = $this->residentsupport->getFormatDutyUserList($dutyUserList);
            $dutyDeptIds = [];
            $deptDutyUserList = [];
            if(isset($dutyUserList[$dayId]['data'])){
                $deptDutyUserList = $dutyUserList[$dayId]['data'];
            }

            if ($deptDutyUserList) {
                $dutyDeptIds = array_keys($deptDutyUserList);
            }
            //部门列表
            $deptList = $this->loadModel('dept')->getDeptListByIds($dutyDeptIds, 'id,name');
            if ($deptList) {
                $deptList = array_column($deptList, 'name', 'id');
            }
            //部门用户列表
            $deptUserList = $this->loadModel('user')->getUserListGroupDeptId($dutyDeptIds);
            $this->view->dutyUserList = $dutyUserList;
            $this->view->depts = $deptList;
            $this->view->deptUserList = $deptUserList;
            $this->view->users = $this->loadModel('user')->getPairs('noletter|noclosed');
            $this->view->tempDayInfo = $tempDayInfo;
            $this->view->dutyDate = $dutyDate;
            $this->view->userType = $userType;
            $this->view->allowModifyDeptIds = $allowModifyDeptIds;

        }
        $this->view->dayId = $dayId;
        $this->view->checkRes = $checkRes;
        $this->view->schedulingDeptType = $schedulingDeptType; //查看分类
        $this->view->browseType = $browseType;
        $this->view->param      = $param;
        $this->view->orderBy    = $orderBy;
        $this->view->recTotal   = $recTotal;
        $this->view->recPerPage = $recPerPage;
        $this->view->pageID     = $pageID;
        $this->display();
    }
    //获取值班组长，值班人员 部门领导
    public function ajaxGetanager(){
        $type = $_POST['type'];
        $fileds = 'dept,account,realname';
        $users = $this->loadModel('user')->getListFiled($fileds);
        $LeaderManager = rtrim($_POST['LeaderManager'],',');
        $dutyUserManager = rtrim($_POST['dutyUserManager'],',');
        $userStr = $dutyUserManager . ',' . $LeaderManager;
        $userArr = array_unique(explode(',',$userStr));
        $dutyUserManagerArr = explode(',',$dutyUserManager);
        $depts = [];
        foreach ($users as $user) {
            foreach ($userArr as $v){
                if ($user->account == $v){
                    $depts[] = $user->dept;
                }
            }
        }
        $depts = array_unique($depts);
        $deptList = $this->loadModel('dept')->getDeptListByIds($depts, 'manager,name,id');
        $userManagerStr = "";
        $LeaderManagerStr = "";
        foreach ($deptList as $dept) {
            foreach ($users as $user) {
                foreach ($dutyUserManagerArr as $dutyUser){
                    if ($dutyUser == $user->account && $user->dept == $dept->id){
                        $userManagerStr .= $dept->manager.',';
                    }
                }
                if ($LeaderManager == $user->account && $user->dept == $dept->id){
                    $LeaderManagerStr = $dept->manager;
                }
            }
        }
        $userDeptArr = array_values(array_unique(explode(',',rtrim($userManagerStr,','))));
        $leaderDeptArr = explode(',',$LeaderManagerStr);
        $LeadrDeptManagerStr = "";//值班组长所在部门领导
        $userDeptManagerStr = "";//值班组员所在部门领导
        $dealUser = $this->loadModel("residentsupport")->getPdReviewDealUsers($type);
        $dealUserName = "";
        foreach ($users as $user) {
            foreach ($leaderDeptArr as $v1){
                if ($user->account == $v1){
                    $LeadrDeptManagerStr .= "<div>".$user->realname."</div>";
                }
            }
            foreach ($userDeptArr as $v2) {
                if ($user->account == $v2){
                    $userDeptManagerStr .= "<div>".$user->realname."</div>";
                }
            }
            if ($dealUser == $user->account){
                $dealUserName = "<div>".$user->realname."</div>";
            }
        }
        echo json_encode(['LeaderManager'=>$LeadrDeptManagerStr,'dutyUserManager'=>$userDeptManagerStr,'dealUser'=>$dealUserName]);
    }
    //填写值班日志获取抄送人
    public function ajaxGetCc(){
        $this->app->loadLang('residentsupport');
        $type = $_POST['type'];
        $dealUser = $this->loadModel("residentsupport")->getPdReviewDealUsers($type);
        $fileds = 'dept,account,realname';
        $users = $this->loadModel('user')->getListFiled($fileds);
        $LeaderManager = rtrim($_POST['LeaderManager'],',');
        $dutyUserManager = rtrim($_POST['dutyUserManager'],',');
        $userStr = $dutyUserManager . ',' . $LeaderManager;
        $userArr = array_unique(explode(',',$userStr));
        $depts = [];
        foreach ($users as $user) {
            foreach ($userArr as $v){
                if ($user->account == $v){
                    $depts[] = $user->dept;
                }
            }
        }
        $depts = array_unique($depts);
        $deptList = $this->loadModel('dept')->getDeptListByIds($depts, 'manager,name,id');
        $manager = array_column($deptList,'manager');
        $managerStr = implode(',',$manager).','.$dealUser;
        $users = $this->loadModel('user')->getPairs('noletter');
        $outsideList1 = $this->loadModel('user')->getUsersNameByType('outsideExpertType');
        $outsideList2 = $this->loadModel('user')->getUsersNameByType('outside');
        $users = array_merge($users, $outsideList1, $outsideList2);
        $setCcList = json_decode($this->config->residentsupport->setCcList);
        if ($setCcList){
            foreach ($setCcList as $sk=>$sv){
                if ($sv->keys == $type){
                    $managerStr .= ','.$sv->values;
                }
            }
        }
        echo html::select('mailCtoUsers[]', $users, $managerStr, "class='form-control chosen' multiple ");

    }
}
