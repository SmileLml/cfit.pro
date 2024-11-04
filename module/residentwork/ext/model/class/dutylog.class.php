<?php
class dutylogResidentwork extends residentworkModel{
    //保存值班日志
    public function createlog(){
        $result = array(
            'result'  => false,
            'message' => '',
        );
        $realDutyuser = array_values(array_filter($_POST['realDutyuser']));
        $isPush = $_POST['isPush']; //1推送值班日志 2不推送
//        $isDraft = $_POST['isDraft'];
        $type = $_POST['type'];
        $data  = fixer::input('post')
            ->remove('realDutyuser,uid,files,source,loadType,isPush')
            ->join('mailCtoUsers',',')
            ->stripTags($this->config->residentwork->editor->createlog['id'], $this->config->allowedTags)
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
        if (mb_strlen($data->remark) > 499){
            $result['message'] = "应急事件简要说明内容过多，不得超过499个字符";
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
        if (mb_strlen($data->warnLogs) > 499){
            $result['message'] = "下一值班重点关注内容过多，不得超过499个字符";
            return $result;
        }
        if ($type == 1 && $data->analysis == ''){
            $result['message'] = '请填写支付交易系统运行质量日报分析';
            return $result;
        }
        if (mb_strlen($data->analysis) > 499){
            $result['message'] = "支付交易系统运行质量日报分析内容过多，不得超过499个字符";
            return $result;
        }
         $info = $this->dao->select("*")->from(TABLE_RESIDENT_SUPPORT_WORK)->where("dutyDate")->eq($data->dutyDate)->andwhere("type")->eq($data->type)->andwhere('subType')->eq($data->subType)->fetch();
        if ($info){
            $result['message'] = "已存在值班日报，请前去编辑";
            return $result;
        }
        $fileds = 'dept,account,realname';
        $users = $this->loadModel('user')->getListFiled($fileds);
        //新增日志
        $data->createdBy = $this->app->user->account;
        $data->createdDate = date("Y-m-d H:i:s");
        $data->editedBy = $this->app->user->account;
        $data->editedDate = date("Y-m-d H:i:s");
        $this->dao->insert(TABLE_RESIDENT_SUPPORT_WORK)->data($data)->exec();
        if(dao::isError()){
            $res['message'] = dao::getError();
            return $res;
        }
        $workId = $this->dao->lastInsertID();
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

                    $this->dao->insert(TABLE_RESIDENT_SUPPORT_WORK_DETAIL)->data($details)->exec();
                    if(dao::isError()){
                        $res['message'] = dao::getError();
                        return $res;
                    }
                }
            }
        }
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
        $this->loadModel('action')->create('residentsupportdayno',$workId, 'createdresidentwork', $pushMsg);
        $this->logSendMail($workId);
        $result = ['result'=>'true','workId'=>$workId];
        return $result;
    }
    //值班日志列表
    public function getWorkList($browseType,$queryID, $orderBy, $pager=''){
        $residentWorkQuery = '';
        if($browseType == 'bysearch') {
            $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : '';

            if($query) {
                $this->session->set('residentworkQuery', $query->sql);
                $this->session->set('residentworkForm', $query->form);
            }
            if($this->session->residentworkQuery == false) $this->session->set('residentworkQuery', ' 1 = 1');
            $residentWorkQuery = $this->session->residentworkQuery;
        }
        if(strpos($residentWorkQuery,'`createdDate`')){
            $residentWorkQuery = str_replace("`createdDate`",'t1.createdDate',$residentWorkQuery);
        }
        if(strpos($residentWorkQuery,'`createdBy`')){
            $residentWorkQuery = str_replace("`createdBy`",'t1.createdBy',$residentWorkQuery);
        }
        if(strpos($residentWorkQuery,'`actualLeader`')){
            $residentWorkQuery = str_replace("`actualLeader`",'t1.groupLeader',$residentWorkQuery);
        }
        if(strpos($residentWorkQuery,'`actualUser`')){
            $residentWorkQuery = str_replace("`actualUser`",'t2.realDutyuser',$residentWorkQuery);
        }
        $data = $this->dao->select('t1.*,group_concat(t2.realDutyuser) as dutyUser,group_concat(t2.realDutyuserDept) as realDutyuserDept')
            ->from(TABLE_RESIDENT_SUPPORT_WORK)->alias("t1")
            ->leftjoin(TABLE_RESIDENT_SUPPORT_WORK_DETAIL)->alias("t2")
            ->on("t1.id = t2.workId")
            ->where('t1.deleted')->eq('0')
            ->beginIF($browseType == 'bysearch')->andWhere($residentWorkQuery)->fi()
            ->andWhere("t2.deleted")->eq('0')
            ->andWhere("t1.templateId")->eq('0')
            ->groupBy("t1.id")
            ->orderBy($orderBy)
            ->beginIF($pager != '')->page($pager,"t1.id")->fi()
            ->fetchAll();
        if ($data && (int)$data[0]->id <= 0){
           $data = [];
        }
        return $data;
    }
    public function editlog($workId){
        $result = array(
            'result'  => false,
            'message' => '',
        );
        $realDutyuser = array_values(array_filter($_POST['realDutyuser']));
        $isPush = $_POST['isPush']; //1推送值班日志 2不推送
        $type = $_POST['type'];
        $data  = fixer::input('post')
            ->remove('realDutyuser,uid,files,source,loadType,isPush,dutyDate,type,subType')
            ->join('mailCtoUsers',',')
            ->stripTags($this->config->residentwork->editor->createlog['id'], $this->config->allowedTags)
            ->get();
        if ((int)$workId<=0){
            $result['message'] = '参数错误';
            return $result;
        }
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
        if (mb_strlen($data->remark) > 499){
            $result['message'] = "应急事件简要说明内容过多，不得超过499个字符";
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
        if (mb_strlen($data->warnLogs) > 499){
            $result['message'] = "下一值班重点关注内容过多，不得超过499个字符";
            return $result;
        }
        if ($type == 1 && $data->analysis == ''){
            $result['message'] = '请填写支付交易系统运行质量日报分析';
            return $result;
        }
        if (mb_strlen($data->analysis) > 499){
            $result['message'] = "支付交易系统运行质量日报分析内容过多，不得超过499个字符";
            return $result;
        }
        $work = $this->getByWorkId($workId);
        $work->realDutyuser = implode(',',array_column($work->details,'realDutyuser'));
        $work->realDutyuserDept = implode(',',array_column($work->details,'realDutyuserDept'));
        $fileds = 'dept,account,realname';
        $users = $this->loadModel('user')->getListFiled($fileds);
        //编辑日志
        $data->editedBy = $this->app->user->account;
        $data->editedDate = date("Y-m-d H:i:s");

        $this->dao->update(TABLE_RESIDENT_SUPPORT_WORK)->data($data)->where("id")->eq($workId)->exec();
        if(dao::isError()){
            $res['message'] = dao::getError();
            return $res;
        }
        $del = new stdClass();
        $del->deleted = 1;
        $del->editedBy = $this->app->user->account;
        $del->editedDate = date("Y-m-d H:i:s");
        $this->dao->update(TABLE_RESIDENT_SUPPORT_WORK_DETAIL)->data($del)->where('workId')->eq($workId)->exec();
        if(dao::isError()){
            $res['message'] = dao::getError();
            return $res;
        }
        $this->loadModel('file')->updateObjectID($this->post->uid, $workId, 'residentwork');
        $this->file->saveUpload('residentwork', $workId);
        //整理员工、部门信息
        $data->realDutyuser = "";
        $data->realDutyuserDept = "";
        foreach ($realDutyuser as $v){
            foreach ($users as $user) {
                if ($v == $user->account){
                    $data->realDutyuser .= $v.',';
                    $data->realDutyuserDept .= $user->dept.',';
                    $details = new stdClass();
                    $details->workId = $workId;
                    $details->realDutyuser = $v;
                    $details->templateId = 0;
                    $details->realDutyuserDept = $user->dept;
                    $details->createdBy = $this->app->user->account;
                    $details->createdDate = date("Y-m-d H:i:s");
                    $details->editedBy = $this->app->user->account;
                    $details->editedDate = date("Y-m-d H:i:s");

                    $this->dao->insert(TABLE_RESIDENT_SUPPORT_WORK_DETAIL)->data($details)->exec();
                    if(dao::isError()){
                        $res['message'] = dao::getError();
                        return $res;
                    }
                }
            }
        }
        if ($isPush == 1 && $type == 1){
            $pushStatus = $this->pushDutyLog($workId);
            $data->pushStatus = $pushStatus;
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
        $data->realDutyuser = rtrim($data->realDutyuser,',');
        $data->realDutyuserDept = rtrim($data->realDutyuserDept,',');
        $changes = common::createChanges($work, $data);
        $actionID = $this->loadModel('action')->create('residentsupportdayno',$workId, 'editedresidentwork', $pushMsg);
        $this->action->logHistory($actionID, $changes);
        $mailRes = $this->logSendMail($workId);
        $result = ['result'=>'true','workId'=>$workId];
        return $result;
    }
}