<?php
class residentsupport extends control
{
    /**
     *二线管理-驻场支持菜单权限方法
     */
    function index(){
        $module    = 'residentsupport';
        $methodName  = 'calendar';
        if(common::hasPriv('residentsupport', 'calendar')){
           $module    = 'residentsupport';
            $methodName  = 'calendar';
        } else if(common::hasPriv('residentsupport', 'browse')){
           $module    = 'residentsupport';
            $methodName  = 'browse';
        } else if(common::hasPriv('residentwork', 'browse')){
           $module    = 'residentwork';
            $methodName  = 'browse';
        }
        echo $this->fetch($module, $methodName);
    }
    /**
     * Project: chengfangjinke
     * Method: browse
     * User: wangjiurong
     * Year: 2022
     * Date: 2022/9/26
     * Time: 13:26
     * Desc: 驻场支持-部门视图
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param string $browseType
     * @param int $param
     * @param string $orderBy
     * @param int $recTotal
     * @param int $recPerPage
     * @param int $pageID
     */
    public function browse($browseType = '', $param = 0, $orderBy = 't1.id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        /* By search. */
        $depts = $this->loadModel('dept')->getOptionMenu();
        $depts[0] = '';
        $this->config->residentsupport->search['params']['deptId']['values'] = $depts;
        //状态
        $temDeptStatusDescList = $this->lang->residentsupport->temDeptStatusDescList;
        unset($temDeptStatusDescList['waitDeal']);
        $temDeptStatusDescList[''] = '';
        $this->config->residentsupport->search['params']['status']['values'] = $temDeptStatusDescList;

        $browseType = strtolower($browseType);
        $queryID = ($browseType == 'bysearch')  ? (int)$param : 0;
        $actionURL = $this->createLink('residentsupport', 'browse', "browseType=bySearch&param=myQueryID");
        $this->residentsupport->buildSearchForm($queryID, $actionURL);
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);
        $residentSupports = $this->residentsupport->getList($browseType, $queryID, $orderBy, $pager);
        $this->view->title = $this->lang->residentsupport->common;
        $this->view->reviewList  = $residentSupports;
        $users = $this->loadModel('user')->getPairs('noclosed|noletter');
        $this->session->set('residentsupportList', $this->app->getURI(true));
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


    /**
     * Project: chengfangjinke
     * Method: browse
     * User: wangjiurong
     * Year: 2022
     * Date: 2022/9/26
     * Time: 13:26
     * Desc: This is the code comment. This method is called view.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $templateId
     * @param $schedulingDeptType
     */
    public function view($templateId, $schedulingDeptType = 'selfDept')
    {
        //模板信息
        $templateInfo = $this->residentsupport->getTemplateInfoById($templateId);
        $this->view->templateInfo = $templateInfo;
        //值班人员按天显示
        $dayIds = array();
        $deptId = 0;
        if($schedulingDeptType == 'selfDept'){ //自己部门
            $deptId = $this->app->user->dept;
        }
        $dutyUserList = $this->residentsupport->getDutyUserListByTemplateId($templateId, $dayIds, $deptId);
        $dutyUserList = $this->residentsupport->getFormatDutyUserList($dutyUserList);
        $dayIds = array_keys($dutyUserList);
        $dayList =  $this->residentsupport->getTempDayListByIds($dayIds);
        if($dayList){
            $dayList = array_column($dayList, null, 'id');
        }
        //值班部门
        $templateDeptList = $this->residentsupport->getTemplateDeptListByTemAndDeptIds($templateId);
        $templateDeptIds = [];
        if($templateDeptList){
            $templateDeptIds = array_column($templateDeptList, 'id');
            $templateDeptList = array_column($templateDeptList, null,'id');
            //获取审核节点信息
            $reviewList = $this->residentsupport->getReviewNodeFormatReviewerList($templateDeptIds);
            if($reviewList){
                $reviewVersionList = $this->loadModel('review')->getReviewVersionList('residentsupport', $templateDeptIds);
                $this->view->reviewVersionList = $reviewVersionList; //值班版本列表
            }
            $this->view->reviewList = $reviewList; //值班节点以及审核人员
        }

        $depts = $this->loadModel('dept')->getAllDeptList('id,name');
        $depts = array_column($depts, 'name', 'id');
        $users = $this->loadModel('user')->getPairs('noletter|noclosed');
        $this->view->dutyUserList = $dutyUserList;
        $this->view->dayList      = $dayList;
        $this->view->depts        = $depts;
        $this->view->users        = $users;
        $this->view->templateDeptList = $templateDeptList; //值班部门
        $this->view->actions = $this->loadModel('action')->getList($this->lang->residentsupport->objectTypeList['resident_support_template'], $templateId);
        $this->view->currentUserDeptId = $this->app->user->dept; //当前用户部门
        $this->view->templateId = $templateId;
        $this->view->schedulingDeptType = $schedulingDeptType;
        $this->display();
    }


    /**
     * calendar.
     *
     * @access public
     * @return void
     */
    public function calendar()
    {
        $this->view->title = $this->lang->residentsupport->common;
        $this->display();
    }

    /**
     * 获取日历视图数据
     */
    public function ajaxGetCalendarList($year){
        $this->residentsupport->getCalendarList($year);
    }

    /**
     *提交操作
     *
     * @param $templateDeptId
     */
    public function submit($templateDeptId){
        //获得模板详情
        $templateDeptInfo = $this->residentsupport->getTemplateDeptInfoById($templateDeptId);
        //模板ID
        $templateId = $templateDeptInfo->templateId;
        $deptId   = $this->app->user->dept;
        $deptInfo = $this->loadModel('dept')->getByID($deptId);
        if($_POST) {
            $logChanges = $this->residentsupport->submit($templateDeptId);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            //日志扩展信息
            $objectType = $this->lang->residentsupport->objectTypeList['resident_support_template_dept'];
            $isSetHistory = true;
            $actionID = $this->loadModel('action')->create($objectType, $templateDeptId, 'submit', $this->post->comment, '', '', true, $isSetHistory, $logChanges);

            //模板维度添加日志
            $objectType = $this->lang->residentsupport->objectTypeList['resident_support_template'];
            $extra = sprintf($this->lang->residentsupport->actionExtra['submit'], $deptInfo->name);
            $actionID = $this->loadModel('action')->create($objectType, $templateId, 'submit', $this->post->comment, $extra);
            //返回
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }
        //检查模板部门是否允许提交操作
        $checkRes = $this->residentsupport->checkTemplateDeptIsSubmit($templateDeptInfo);
        if($checkRes['result']){
            $managerUserAccounts = $deptInfo->manager;
            $managerArray = explode(',', $managerUserAccounts);
            $managerUsers = $this->loadModel('user')->getUserListByAccounts($managerArray);
            $this->view->managerUserAccounts = $managerUserAccounts;
            $this->view->managerUsers        = $managerUsers;
            $this->view->users = $this->loadModel('user')->getPairs('noletter');
        }
        $this->view->templateDeptInfo = $templateDeptInfo;
        $this->view->deptInfo            = $deptInfo;
        $this->view->checkRes = $checkRes;
        $this->display();
    }

    /**
     *审核操作
     *
     * @param $templateDeptId
     */
    public function review($templateDeptId){
        //获得模板详情
        $templateDeptInfo = $this->residentsupport->getTemplateDeptInfoById($templateDeptId);
        //模板ID
        $templateId = $templateDeptInfo->templateId;
        $deptId     = $templateDeptInfo->deptId;
        $deptInfo   = $this->loadModel('dept')->getByID($deptId);
        if($_POST) {
            $reviewResult = $_POST['result'];
            $logChanges = $this->residentsupport->review($templateDeptId);
            if(dao::isError()) {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $actionType = 'reviewed';
            //审核前上一个状态
            $lastStatus = $templateDeptInfo->status;
            if($lastStatus == $this->lang->residentsupport->temDeptStatusList['waitPdReview']){
                $actionType = 'reviewedconfirm';
            }

            //日志扩展信息
            $objectType = $this->lang->residentsupport->objectTypeList['resident_support_template_dept'];
            $isSetHistory = true;
            $actionID = $this->loadModel('action')->create($objectType, $templateDeptId, $actionType, $this->post->comment, '', '', true, $isSetHistory, $logChanges);

            //模板维度添加日志
            $objectType = $this->lang->residentsupport->objectTypeList['resident_support_template'];
            $reviewResultDesc = zget($this->lang->residentsupport->reviewConclusionList, $reviewResult);
            $extra = sprintf($this->lang->residentsupport->actionExtra['review'], $deptInfo->name, $reviewResultDesc);
            $actionID = $this->loadModel('action')->create($objectType, $templateId, $actionType, $this->post->comment, $extra);

            //返回
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }

        //检查模板部门是否允许提交操作
        $checkRes = $this->residentsupport->checkTemplateDeptIsReview($templateDeptInfo);
        $this->view->templateDeptInfo = $templateDeptInfo;
        $this->view->deptInfo = $deptInfo;
        $this->view->checkRes = $checkRes;
        $this->display();
    }

    /**
     * 删除排班
     *
     * @param $templateDeptId
     */
    function deleteDutyUser($templateDeptId){
        //获得模板详情
        $templateDeptInfo = $this->residentsupport->getTemplateDeptInfoById($templateDeptId);
        //模板ID
        $templateId = $templateDeptInfo->templateId;
        $deptId   = $templateDeptInfo->deptId;
        $deptInfo = $this->loadModel('dept')->getByID($deptId);

        $logChanges = $this->residentsupport->deleteDutyUser($templateDeptId);
        if(dao::isError()) {
            $response['result']  = 'fail';
            $response['message'] = dao::getError();
            //$this->send($response);
            die(js::alert($response['message'][0],false, $this->createLink('residentsupport', 'browse')));
        }

        //日志扩展信息
        $objectType = $this->lang->residentsupport->objectTypeList['resident_support_template_dept'];
        $isSetHistory = true;
        $actionID = $this->loadModel('action')->create($objectType, $templateDeptId, 'deletedutyuser', $this->post->comment, '', '', true, $isSetHistory, $logChanges);

        //模板维度添加日志
        $objectType = $this->lang->residentsupport->objectTypeList['resident_support_template'];
        $extra = sprintf($this->lang->residentsupport->actionExtra['deleteDutyUser'], $deptInfo->name);
        $actionID = $this->loadModel('action')->create($objectType, $templateId, 'deletedutyuser', $this->post->comment, $extra);
        //返回
        $response['result']  = 'success';
        $response['message'] = $this->lang->saveSuccess;
        $response['locate']  = 'parent';
        //$this->send($response);
        die(js::alert("清空排班成功",true, $this->createLink('residentsupport', 'browse')));
    }


    /**
     * 模板维度启用排班
     */
    public function enableScheduling(){
        if($_POST) {
            $data = fixer::input('post')->get();
            $templateId = $data->templateId;
            $logChanges = $this->residentsupport->enableScheduling();
            if(dao::isError()) {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            //日志扩展信息
            $objectType = $this->lang->residentsupport->objectTypeList['resident_support_template'];
            $isSetHistory = true;
            //排班信息
            $enableSchedulingInfo = $this->residentsupport->getLogChangesSpecialInfo($logChanges, 'enableSchedulingInfo');
            $actionID = $this->loadModel('action')->create($objectType, $templateId, 'enablescheduling', $this->post->comment, $enableSchedulingInfo, '', true, $isSetHistory, $logChanges);
            //返回
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }
        $this->display();
    }

    /**
     *线上排班
     *
     * @param $templateId
     * @param $startDate
     * @param $endDate
     */
    public function onLineScheduling($templateId = '', $startDate = '', $endDate = '',$schedulingDeptType ='selfDept'){
        if ($_SERVER['QUERY_STRING'] != ''){
            $paramsArray = explode('&',$_SERVER['QUERY_STRING']);
            $params = [];
            foreach ($paramsArray as $item) {
                $arr = explode('=',$item);
                $params[$arr[0]] = $arr[1];
                ${$arr[0]} = $arr[1];
            }
        }

        if($_POST) {
            $templateId     = $_POST['templateId'];
            $templateDeptId = $_POST['templateDeptId'];
            $logChanges = $this->residentsupport->editScheduling();
            if(dao::isError()) {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $isSetHistory = true;
            $extLogInfo = $this->residentsupport->getLogChangesSpecialInfo($logChanges, 'extLogInfo');

            //模板日志扩展信息
            $objectType = $this->lang->residentsupport->objectTypeList['resident_support_template'];
            $actionID = $this->loadModel('action')->create($objectType, $templateId, 'editScheduling', '', $extLogInfo, '', true, $isSetHistory, $logChanges);

            //模板部门日志扩展信息
            $objectType = $this->lang->residentsupport->objectTypeList['resident_support_template_dept'];
            $actionID = $this->loadModel('action')->create($objectType, $templateDeptId, 'editScheduling', '', $extLogInfo, '', true, $isSetHistory, $logChanges);

            //返回
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = $this->createLink("residentsupport",'browse');
            $this->send($response);
        }
        //部门id
        $deptId = $this->app->user->dept;
        $deptInfo = $this->loadModel('dept')->getByID($deptId);
        $templateInfo = $this->residentsupport->getTemplateInfoById($templateId);
        $templateDeptInfo = $this->residentsupport->getTemplateDeptInfoByTemAndDeptId($templateId, $deptId);
        //检查模板部门是否允许提交操作
        $checkRes = $this->residentsupport->checkTemplateDeptIsScheduling($templateDeptInfo);
        $dayList = $this->residentsupport->getTempDayList($templateId, $startDate, $endDate);
        if(empty($dayList)){
            $checkRes = array(
                'result'  => false,
                'message' => $this->lang->residentsupport->checkSubmitResultList['searchTimeEmpty'],
            );
        }
        if($checkRes['result']){
            $deptIdSearch = "";
            if($schedulingDeptType == 'selfDept'){ //自己部门
                $deptIdSearch = $this->app->user->dept;
            }
            $dayIds = array_column($dayList, 'id');
            $dutyUserList = $this->residentsupport->getDutyUserListByTemplateId($templateId, $dayIds,$deptIdSearch);
            $dutyUserList = $this->residentsupport->getFormatDutyUserList($dutyUserList);
            //获得当前部门用户
            $currentDeptUsers = $this->loadModel('user')->getUserListByDeptId($deptId, 'account,realname');
            $currentDeptUsers = array_column($currentDeptUsers, 'realname', 'account');
            array_unshift($currentDeptUsers, '选择值班人员');
            $this->view->dayList      = $dayList;
            $this->view->dutyUserList = $dutyUserList;
            $this->view->depts        = $this->loadModel('dept')->getOptionMenu();
            $users = $this->loadModel('user')->getPairs('noletter|noclosed');
            $users[''] = '选择值班人员';
            $this->view->users = $users;
            $this->view->currentDeptUsers = $currentDeptUsers;
            $this->view->currentDay = Helper::today();
        }
        $this->view->schedulingDeptType = $schedulingDeptType; //查看分类
        $this->view->source = isset($source) ? $source : '0';
        $this->view->templateId = isset($templateId) ? $templateId : '';
        $this->view->startDate = isset($startDate) ? $startDate : '';
        $this->view->endDate = isset($endDate) ? $endDate : '';
        $this->view->typeVal = isset($type) ? $type : '1';
        $this->view->subTypeVal = isset($subType) ? $subType : '1';

        $this->view->templateInfo = $templateInfo;
        $this->view->templateDeptInfo = $templateDeptInfo;
        $this->view->deptInfo = $deptInfo;
        $this->view->checkRes = $checkRes;
        $this->display();
    }

    /**
     *编辑排班
     *
     * @param string $templateId
     * @param $schedulingDeptType
     * @param string $browseType
     * @param int $param
     * @param string $orderBy
     * @param int $recTotal
     * @param int $recPerPage
     * @param int $pageID
     * @param string $sourceLabel
     */
    public function editScheduling($templateId = '', $schedulingDeptType = 'selfDept',  $browseType = 'all', $param = 0, $orderBy = 't1.id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1, $sourceLabel = ''){
        if ($_SERVER['QUERY_STRING'] != ''){
            $paramsArray = explode('&',$_SERVER['QUERY_STRING']);
            $params = [];
            foreach ($paramsArray as $item) {
                $arr = explode('=',$item);
                $params[$arr[0]] = $arr[1];
                ${$arr[0]} = $arr[1];
            }
        }

        if($_POST) {
            $templateId     = $_POST['templateId'];
            $templateDeptId = $_POST['templateDeptId'];
            $logChanges = $this->residentsupport->editScheduling();
            if(dao::isError()) {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $isSetHistory = true;
            $extLogInfo = $this->residentsupport->getLogChangesSpecialInfo($logChanges, 'extLogInfo');

            //模板日志扩展信息
            $objectType = $this->lang->residentsupport->objectTypeList['resident_support_template'];
            $actionID = $this->loadModel('action')->create($objectType, $templateId, 'editScheduling', '', $extLogInfo, '', true, $isSetHistory, $logChanges);

            //模板部门日志扩展信息
            $objectType = $this->lang->residentsupport->objectTypeList['resident_support_template_dept'];
            $actionID = $this->loadModel('action')->create($objectType, $templateDeptId, 'editScheduling', '', $extLogInfo, '', true, $isSetHistory, $logChanges);

            //返回
            $pathArr = explode('-',str_replace(".html","",$_SERVER['PATH_INFO']));
            if(in_array('workWaitList', $pathArr)){
                $params = "mode=$browseType&type=$param";
                $locate = $this->createLink('my', 'work', $params);
            }else{
                $params = "browseType=$browseType" . "&param=$param". "&orderBy=$orderBy" . "&recTotal=$recTotal" . "&recPerPage=$recPerPage" . "&pageID=$pageID";
                $locate = $this->createLink('residentsupport', 'browse', $params);
            }
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = $locate;
            $this->send($response);
        }
        $templateInfo = $this->residentsupport->getTemplateInfoById($templateId);
        //部门id
        $deptId = $this->app->user->dept;
        $templateDeptInfo = $this->residentsupport->getTemplateDeptInfoByTemAndDeptId($templateId, $deptId);
        $deptInfo = $this->loadModel('dept')->getByID($deptId);
        //检查模板部门是否允许提交操作
        $checkRes = $this->residentsupport->checkTemplateDeptIsScheduling($templateDeptInfo);
        $startDate = isset($startDate) ? $startDate : $templateInfo->startDate;
        $endDate = isset($endDate) ? $endDate : $templateInfo->endDate;
        $dayList = $this->residentsupport->getTempDayList($templateId, $startDate, $endDate);
        if(empty($dayList)){
            $checkRes = array(
                'result'  => false,
                'message' => $this->lang->residentsupport->checkSubmitResultList['searchTimeEmpty'],
            );
        }
        if($checkRes['result']){
            $dayIds = array_column($dayList, 'id');
            $deptId = 0;
            if($schedulingDeptType == 'selfDept'){ //自己部门
                $deptId = $this->app->user->dept;
            }
            $dutyUserList = $this->residentsupport->getDutyUserListByTemplateId($templateId, $dayIds, $deptId);
            $dutyUserList = $this->residentsupport->getFormatDutyUserList($dutyUserList);
            //获得当前部门用户
            $userDeptId = $this->app->user->dept;
            $currentDeptUsers = $this->loadModel('user')->getUserListByDeptId($userDeptId, 'account,realname');
            $currentDeptUsers = array_column($currentDeptUsers, 'realname', 'account');
            array_unshift($currentDeptUsers, '选择值班人员');
            $this->view->dayList      = $dayList;
            $this->view->dutyUserList = $dutyUserList;
            $this->view->depts        = $this->loadModel('dept')->getOptionMenu();
            $users = $this->loadModel('user')->getPairs('noletter|noclosed');
            $users[''] = '选择值班人员';
            $this->view->users = $users;
            $this->view->currentDeptUsers = $currentDeptUsers;
            $this->view->currentDay = Helper::today();
        }
        $this->view->source = isset($source) ? $source : '0';
        $this->view->templateId = isset($templateId) ? $templateId : '0';
        $this->view->startDate  = $startDate;
        $this->view->endDate    = $endDate;
        $this->view->typeVal    = isset($type) ? $type : isset($templateInfo->type)? $templateInfo->type: 1;
        $this->view->subTypeVal = isset($subType) ? $subType : isset($templateInfo->subType)? $templateInfo->subType: 1;

        $this->view->templateInfo = $templateInfo;
        $this->view->templateDeptInfo = $templateDeptInfo;
        $this->view->deptInfo = $deptInfo;
        $this->view->checkRes = $checkRes;
        $this->view->schedulingDeptType = $schedulingDeptType; //查看分类
        $this->view->browseType = $browseType;
        $this->view->param      = $param;
        $this->view->orderBy    = $orderBy;
        $this->view->recTotal   = $recTotal;
        $this->view->recPerPage = $recPerPage;
        $this->view->pageID     = $pageID;
        $this->view->sourceLabel = $sourceLabel;
        $this->display();
    }

    /**
     * 获得模板信息
     *
     */
    public function ajaxGetAllowEnableTemplateInfo(){
        $data = fixer::input('post')->get();
        $res = $this->residentsupport->checkIAllowEnableScheduling($data);
        if(!$res['result']){
            $this->send($res);
        }
        //返回正确
        $data = $res['data'];
        $templateInfo = $data['templateInfo'];
        $dutyDates = $data['dutyDates'];
        $templateId   = $templateInfo->id;

        $res['templateId'] = $templateId;
        $type = $templateInfo->type;
        $otherEnableDayIds = $this->residentsupport->getOtherEnableDayIds($type, $dutyDates, $templateId);
        if($otherEnableDayIds){
            $res['code'] = '1000';
        }else{
            $res['code'] = '1';
        }
        $this->send($res);
    }

    //导出模板

    public function export(){
        if($_POST)
        {
            $this->residentsupport->setListValue();
            foreach($this->config->residentsupport->export->templateFields as $field) $fields[$field] = $this->lang->residentsupport->exportFileds->$field;
            $info = new stdClass();
            $info->dutyDate = '2022-09-08';
            $info->postType = '';
            $info->dutyUserDept = '';
            $info->timeType = '';
            $info->dutyDuration = '8:30-14:00';
            $info->requireInfo = '';
            $info->type = '';
            $info->subType = '';
            $info->dutyGroupLeader = '';
            $info->dutyUser = '';
            $data[] = $info;
            $this->post->set('fields', $fields);
            $this->post->set('kind', 'residentsupport');
            $this->post->set('rows', $data);
            $this->post->set('extraNum',   $this->post->num-1);
            $this->post->set('width',   $this->config->residentsupport->export->width);
            $this->post->set('fileName', '排班原始模板');

            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }
        $this->display();
    }

    //导出排班模板
    public function exportRostering(){
        if ($_POST){
            $templateId = $_POST['templateId'];
            $this->loadModel('action')->create($this->lang->residentsupport->objectTypeList['resident_support_template'], $templateId, 'exportrostering', $this->post->comment);
            $res = $this->residentsupport->setListValue();
            foreach($this->config->residentsupport->export->templateFields as $field) $fields[$field] = $this->lang->residentsupport->exportFileds->$field;
            $data = $this->residentsupport->getTemplateList($templateId,$res);
            $startDate = str_replace("-",'',$data[0]->dutyDate);
            $endDate = str_replace("-",'',$data[count($data)-1]->dutyDate);
            $fileName = "排班模板_".$this->lang->residentsupport->typeList[$_POST['type']].'_'.$this->lang->residentsupport->subTypeList[$_POST['subType']].'_'.$startDate.'~'.$endDate;
            $this->post->set('fields', $fields);
            $this->post->set('kind', 'residentsupport');
            $this->post->set('rows', $data);
//            $this->post->set('extraNum',   count($data));
            $this->post->set('width',   $this->config->residentsupport->export->width);
            $this->post->set('fileName', $fileName);
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }
        $type = $this->lang->residentsupport->typeList;
        $subType = $this->lang->residentsupport->subTypeList;
        $this->view->type = array_filter($type);
        $this->view->subType = array_filter($subType);
        $this->display();
    }
    //导出排班数据
    public function exportRosteringData(){
        if ($_POST){
            $exportType = 2;
            $templateId = $_POST['templateId'];
            $this->loadModel('action')->create($this->lang->residentsupport->objectTypeList['resident_support_template'], $templateId, 'exportrosteringdata', $this->post->comment);

            $res = $this->residentsupport->setListValue();
            foreach($this->config->residentsupport->export->templateFields as $field) $fields[$field] = $this->lang->residentsupport->exportFileds->$field;
            $data = $this->residentsupport->getTemplateList($templateId,$res,$exportType);
            $startDate = str_replace("-",'',$data[0]->dutyDate);
            $endDate = str_replace("-",'',$data[count($data)-1]->dutyDate);
            $fileName = "值班数据_".$this->lang->residentsupport->typeList[$_POST['type']].'_'.$this->lang->residentsupport->subTypeList[$_POST['subType']].'_'.$startDate.'~'.$endDate;
            $this->post->set('fields', $fields);
            $this->post->set('kind', 'residentsupport');
            $this->post->set('rows', $data);
//            $this->post->set('extraNum',   count($data));
            $this->post->set('width',   $this->config->residentsupport->export->width);
            $this->post->set('fileName', $fileName);
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }
        $type = $this->lang->residentsupport->typeList;
        $subType = $this->lang->residentsupport->subTypeList;
        $this->view->type = array_filter($type);
        $this->view->subType = array_filter($subType);
        $this->display();
    }
    //导入排班模板
    public function import($importType){
        if($_FILES)
        {
            $editMethod = $_POST['editMethod'];
            $templateId = isset($_POST['templateId']) ? $_POST['templateId'] : 0;
            if ($editMethod == 'edit' && $templateId <= 0){
                $response['result']  = 'fail';
                $response['message'] = "请选择要编辑的模板";
                $this->send($response);
            }
            $file = $this->loadModel('file')->getUpload('file');
            $file = $file[0];
            if($file['extension'] != 'xlsx') die(js::alert($this->lang->file->onlySupportXLSX));
            $fileName = $this->file->savePath . $this->file->getSaveName($file['pathname']);
            move_uploaded_file($file['tmpname'], $fileName);
            $tmpPath = $this->loadModel('file')->getPathOfImportedFile();
            $tmpFile = $tmpPath . DS . md5(basename($fileName));

            $this->loadModel("file");
            $rows = $this->file->getRowsFromExcel($fileName);
            $data = [];
            $type = $this->lang->residentsupport->typeList;
            $subType = $this->lang->residentsupport->subTypeList;
            foreach ($rows as $key=>$row) {
                if ($key == 1) continue;
                $info = new stdClass();
                if (!strtotime($row[0])){
                    unlink($fileName);
                    $response['result']  = 'fail';
                    $response['message'] = "值班日期格式不正确，请检查";
                    $this->send($response);
                }
                $info->dutyDate = $row[0];
                $info->type = array_search($row[6],$type);
                $info->subType = array_search($row[7],$subType);
                $data[] = $info;
            }
            if (count($data) <= 0){
                $response['result']  = 'fail';
                $response['message'] = "模板数据不能为空";
                $this->send($response);
            }
            $dutyDate = array_column($data,'dutyDate');
            $type = array_column($data,'type');
            $subType = array_column($data,'subType');
            $endDate = $dutyDate[count($dutyDate)-1];
            if ($endDate <= date("Y-m-d")){
                $response['result']  = 'fail';
                $response['message'] = "值班日期全部小于等于当前时间，不能导入";
                $this->send($response);
            }
            if (count(array_unique($type)) > 1) {
                $response['result']  = 'fail';
                $response['message'] = "值班类型不一致";
                $this->send($response);
            }
            if (count(array_unique($subType)) > 1) {
                $response['result']  = 'fail';
                $response['message'] = "值班子类不一致";
                $this->send($response);
            }
            asort($dutyDate);
            $day = [];
            $templateInfo = [];
            if ($editMethod == 'add'){
                $day = $this->residentsupport->getdays($type,$subType,$dutyDate);
            }else{
                $templateInfo = $this->residentsupport->getTemplateInfoById($templateId);
            }
            if ($editMethod == 'edit'){
                if ($type[0] != $templateInfo->type){
                    unlink($fileName);
                    $response['result']  = 'fail';
                    $response['message'] = "导入的模板包分类与选择编辑的模板分类不一致";
                    $this->send($response);
                }
                if ($subType[0] != $templateInfo->subType){
                    unlink($fileName);
                    $response['result']  = 'fail';
                    $response['message'] = "导入的模板包分类与选择编辑的模板子分类不一致";
                    $this->send($response);
                }
            }
            if ($day && $editMethod == 'add'){
                unlink($fileName);
                $response['result']  = 'fail';
                $response['message'] = "值班日期已存在，请选择编辑模板";
                $this->send($response);
            }
            if ($editMethod == 'edit'){
                if (empty($templateInfo)){
                    unlink($fileName);
                    $response['result']  = 'fail';
                    $response['message'] = "模板不存在";
                    $this->send($response);
                }
                if ($templateInfo->startDate < $dutyDate[0] || $endDate > $templateInfo->endDate){
                    $response['result']  = 'fail';
                    unlink($fileName);
                    $response['message'] = "导入的模板开始时间超出选择的模板时间范围，不能编辑";
                    $this->send($response);
                }

            }
            $this->session->set('fileImport', $fileName);
            $this->session->set('templateId', $templateId);
            $fileImportName = explode('.',$_FILES['file']['name']);
            $this->session->set('fileImportName', $fileImportName[0]);
            die(js::locate(inlink('showImport','templateid='.$templateId."&editMethod=".$editMethod), 'parent.parent'));
        }
        $this->view->title = $this->lang->residentsupport->import;
        $this->view->msgTxt = $this->lang->residentsupport->importTxt;
        if ($importType == 'importrosteringData'){
            $this->view->title = $this->lang->residentsupport->importrosteringData;
            $this->view->msgTxt = $this->lang->residentsupport->importrosteringDataTxt;

        }
        $this->display();
    }
    public function showImport($templateId=0,$editMethod = 'add'){

        $file    = $this->session->fileImport;
        $tmpPath = $this->loadModel('file')->getPathOfImportedFile();
        $tmpFile = $tmpPath . DS . md5(basename($file));
        if($_POST)
        {
            $res = $this->residentsupport->createFromImport($templateId,$editMethod);
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
//            unlink($tmpFile);
            die(js::locate($this->createLink('residentsupport', 'calendar'), 'parent'));
            exit;
        }
        $rows = $this->file->getRowsFromExcel($file);
        $type = $this->lang->residentsupport->typeList;
        $subType = $this->lang->residentsupport->subTypeList;
        $postType = $this->lang->residentsupport->postType;
        $durationType = $this->lang->residentsupport->durationTypeList;
        $dept = $this->loadModel('dept')->getOptionMenu();
        $users = $this->loadModel('user')->getPairs('noletter');
        $outsideList1 = $this->loadModel('user')->getUsersNameByType('outsideExpertType');
        $outsideList2 = $this->loadModel('user')->getUsersNameByType('outside');
        $users = array_merge($users, $outsideList1, $outsideList2);
        unset($dept[0]);
        $data = [];
        $arr = [];
        foreach ($rows as $key=>$row) {
            if($key == 1) continue;
//            if(!$row[0]) continue;
            $info = new stdClass();
            $info->dutyDate = $row[0];
//            if ($row[0] == '' || $row[0] == '0000-00-00'){
//                unlink($tmpFile);
//                die(js::alert("排班日期不能为空",true,$this->createLink('residentsupport', 'calendar')));
//            }
//            if ($row[6] == ''){
//                unlink($tmpFile);
//                die(js::alert("值班类型不能为空",true,$this->createLink('residentsupport', 'calendar')));
//            }
//            if ($row[7] == ''){
//                unlink($tmpFile);
//                die(js::alert("值班子类不能为空",true,$this->createLink('residentsupport', 'calendar')));
//            }
            $info->postType = array_search($row[1],$postType);
            $info->dutyUserDept = array_search($row[2],$dept);
            $info->timeType = array_search($row[3],$durationType);
            $info->dutyDuration = $row[4];
            $info->requireInfo = $row[5];
            $info->type = array_search($row[6],$type);
            $info->subType = array_search($row[7],$subType);
            $info->dutyUser = array_search($row[9],$users);
            if (!array_key_exists($info->dutyDate,$arr)){
                $arr[$info->dutyDate] = $info->dutyUser;
            }
            $data[] = $info;
        }

        //常规类默认启用，需查询是否有已启用的模板

        foreach ($data as $k=>$v) {
            $v->dutyGroupLeader = '';
            foreach ($arr as $k2=>$v2) {
                if ($k2 == $v->dutyDate){
                    $v->dutyGroupLeader = $v2;
                    $data[$k] = $v;
                }
            }
        }
        $this->view->data = $data;
//        $this->view->enableStr = $enableStr;
        $this->view->users = $users;
        $this->view->dept = $dept;
        $this->display();
    }
    //根据分类获取模板
    public function ajaxGetTemplate(){
        $list = $this->dao->select('id,startDate,endDate')->from(TABLE_RESIDENT_SUPPORT_TEMPLATE)->where('deleted')->eq(0)->andwhere('type')->eq($_POST['type'])->andwhere('subType')->eq($_POST['subType'])->fetchAll();
        $arr = [];
        foreach ($list as $k => $v){
            $arr[$v->id] = "ID:".$v->id.': '.$v->startDate.'~'.$v->endDate;
        }
        $templateId = isset($_POST['templateId']) ? $_POST['templateId'] : '';
        $source = isset($_POST['source']) ? $_POST['source'] : '0';
        $attrStr = '';
        if ($source == 1){
            $arr = [''=>''] + $arr;
            $attrStr = " onchange='setTemplate()'";
        }
        echo html::select("templateId",$arr,$templateId,"class='form-control chosen' ".$attrStr);
    }
    //在线排班
    public function rostering(){
        $this->view->type = $this->lang->residentsupport->typeList;
        $this->view->subType = $this->lang->residentsupport->subTypeList;
        $this->display();
    }

    //在线排班页面搜索判断内容是否有更改
    public function ajaxCheckSearch()
    {
        $user = $_POST['dutyUsers'];
        $templateId = $_POST['templateId'];
        $templateDeptId = $_POST['templateDeptId'];
        $idArr = $_POST['id'];
        $dutyUsers = [];
        foreach ($user as $k => $v) {
            $dutyUsers[$idArr[$k]] = $v ? $v: '';
        }
        if (!($templateId && $templateDeptId && $dutyUsers)) {
            dao::$errors[] = $this->lang->common->errorParamId;
            return false;
        }
        $templateDeptInfo = $this->loadModel('residentsupport')->getTemplateDeptInfoById($templateDeptId);
        $res = $this->residentsupport->checkTemplateDeptIsScheduling($templateDeptInfo);
        if (!$res['result']) {
            dao::$errors[] = $res['message'];
            return false;
        }
        $dayDetailIds = array_keys($dutyUsers);
        //查询排班详情
        $dayDetailLit = $this->loadModel('residentsupport')->getDutyUserListByIds($dayDetailIds);
        if (!$dayDetailLit) {
            dao::$errors[] = $this->lang->residentsupport->checkSchedulingResultList['noDutyUserError'];
            return false;
        }
        $dayIds = array_column($dayDetailLit, 'dayId');

        $dayDetailLit = array_column($dayDetailLit, null, 'id');
        //实际变更的列表
        $updateDayDetailList = [];
        //变更的天数
        $updateDayIds = [];
        foreach ($dutyUsers as $dayDetailId => $dutyUser) {
            $dayDetailInfo = zget($dayDetailLit, $dayDetailId);

            $oldDutyUser = $dayDetailInfo->dutyUser;
            $dutyUser = $dutyUser ? $dutyUser : '';
            $dayId = $dayDetailInfo->dayId;
            if ($oldDutyUser != $dutyUser) { //值班人员发生变化
                $updateDayIds[] = $dayId;
                $updateDayDetailList[$dayDetailId] = $dutyUser;
            }
        }
        //没有任何修改
        if (!empty($updateDayDetailList)) {
            echo 1;
        }
    }

    function test(){
//        //查询模板是否存在该天值班信息
//        $templateId = 1;
//        $dayList = $this->residentsupport->getTempDayList($templateId, '2022-10-29', '2022-10-30', 'id, dutyDate');
//        echo '<pre>';
//        print_r($dayList);
//        echo '</pre>';
//        if(empty($dayList)){
//            $res['message'] = $this->lang->residentsupport->checkSchedulingResultList['dayEmpty'];
//            $this->send($res);
//        }
//        $dayIds = array_column($dayList, 'id');
//        $dutyDates = array_column($dayList, 'dutyDate');
//        $type = 1;
//
//        $otherEnableDayIds = $this->residentsupport->getOtherEnableDayIds($type, $dutyDates, $templateId);
//        echo '<pre>';
//        print_r($otherEnableDayIds);
//        echo '</pre>';
//        echo '<pre>';
//        print_r($dutyDates);
//        echo '</pre>';
//        $otherTemplateIds = [8];
//        $needCloseTemplateIds = $this->residentsupport->getNeedCloseTemplateIds($otherTemplateIds);
//        echo '<pre>';
//        print_r($needCloseTemplateIds);
//        echo '</pre>';
//        $actionID = 619735;
//        $action = $this->loadModel('action')->getById($actionID);
//        $opAction = $action->action;
//        if($opAction == 'modifyscheduling'){
//            $extra = $action->extra;
//            $modifyDutyDate = substr($extra, strpos($extra, '日期:')+7, 10);
//            if($modifyDutyDate){
//                $templateId = 23;
//                $dutyDayInfo = $this->loadModel('residentsupport')->getDutyDayInfo($templateId, $modifyDutyDate);
//                echo '<pre>';
//                print_r($dutyDayInfo);
//                echo '</pre>';
//                $dutyUsers = explode(',', $dutyDayInfo->dutyUsers);
//                $dutyGroupLeaders = array($dutyDayInfo->dutyGroupLeader);
//                echo '<pre>';
//                print_r($dutyUsers);
//                echo '</pre>';
//                echo '<pre>';
//                print_r($dutyGroupLeaders);
//                echo '</pre>';
//                $noticeUsers = array_merge($dutyUsers, $dutyGroupLeaders);
//                if(!empty($noticeUsers)){
//                    $noticeUsers = array_flip(array_flip(array_filter($noticeUsers)));
//                    $toList = implode(',', $noticeUsers);
//                }
//                echo $toList;
//            }
//        }
//        echo '<pre>';
//        print_r($this->app);
//        echo '</pre>';
    }

    //判断是否有
    public function ajaxcheckIsDayEnable(){
        $enableStr = "";
        $subType = $_POST['subType'][0];
        if ($subType == 1){
            $dutydate = array_unique($_POST['dutyDate']);
            $type = $_POST['type'][0];
            $res = $this->residentsupport->getEnableDay($type,$subType,$dutydate);
            if (!empty($res)){
                $templateId = array_values(array_unique(array_column($res,'id')));
                foreach ($templateId as $item) {
                    $enableStr .= "<div>模板ID：".$item."：&nbsp;&nbsp;";
                    $childrenHtml = "";
                    foreach ($res as $rv){
                        if ($rv->id == $item){
                            $childrenHtml .= "<label>".$rv->dutyDate."&nbsp;&nbsp;</label>";
                        }
                    }
                    $enableStr .= $childrenHtml."</div>";
                }
                $enableStr .= "存在已启用排班，请及时修改。点击确认会继续保存！";
            }
        }
        echo strip_tags(str_replace("&nbsp;",'',$enableStr));exit;
    }
}
