<?php
include '../../control.php';
class myApi extends api
{
    const PARAMS_MISSING = 1001; //缺少参数
    const PARAMS_ERROR   = 1002; //缺少参数
    const FAIL_CODE   = 999;    //请求失败

    public function senduniversalservice()
    {
        $logID = $this->loadModel('requestlog')->insideSaveRequestLog('secondorder' , 'senduniversalservice');
        $this->checkApiToken();
        $errMsg = $this->checkInput();
        if(!empty($errMsg)) {
            $this->requestlog->response('fail', implode(',',$errMsg), [], $logID, self::FAIL_CODE);
        }
        $this->loadModel('secondorder');
        $this->loadModel('application');
        //查找数据
        $secondorder = $this->loadModel('secondorder')->getByExternalCode($this->post->externalCode);

        //判断数据库是否存在记录
        $secondorderID = '';
        if(!empty($secondorder->id)){
            //只有未受理状态才更新数据
            if($secondorder->status != 'backed' && $secondorder->status != 'delivered'){
                $this->requestlog->response('fail', '当前数据状态不允许更新', [], $logID, self::FAIL_CODE);
            }
            $secondorderID = $secondorder->id;
            $updateData = new stdClass();
            //任务状态
            $updateData->status = 'toconfirmed';
            //任务来源方式
            $updateData->source = 'qz';
            //任务分类
            $updateData->type = 'consult';
            //子任务分类
            $updateData->subtype = 'a5';
            //期望完成时间
            $updateData->exceptDoneDate = date('Y-m-d', ($this->post->exceptDoneDate)/1000);
            //所属系统
            $applicationObj = $this->dao->select('*')->from(TABLE_APPLICATION)
                ->where('code')->eq($this->post->app)->andWhere('deleted')->ne(1)->fetch();
            if(!empty($applicationObj->id)){
                //所属系统
                $updateData->app = $applicationObj->id;
                //承建单位
                $updateData->team = $applicationObj->team;
                //业务司局
                $fromUnit = $this->lang->application->fromUnitList[$applicationObj->fromUnit];
                foreach ($this->lang->opinion->unionList as $key => $value) {
                    //因为所属系统的业务司局和二线工单的业务司局数据来源在各自的后台自定义里面，没法统一，只能通过值匹配来转换
                    if(!empty($value) and !empty($fromUnit)){
                        if(strpos($value, $fromUnit) !== false || strpos($fromUnit, $value) !== false){
                            $updateData->union = $key;
                            break;
                        }
                    }
                }
            }

            //联系电话
            $updateData->contactsPhone = $this->post->contactsPhone;
            //任务描述
            $updateData->desc = '【400客服咨询】'.$this->post->taskDescription;
            //任务摘要
            if(strlen($updateData->desc) > 250){
                $updateData->summary = mb_substr($updateData->desc, 0, 250).'...';
            }else{
                $updateData->summary = $updateData->desc;
            }

            //编辑人
            $updateData->editedBy = 'guestcn';
            //编辑时间
            $updateData->editedDate = helper::now();
            //外部申请部门
            $updateData->externalDept = $this->post->externalDept;
            //外部申请人
            $updateData->externalApplicant = $this->post->externalApplicant;
            //联系人
            $updateData->contacts = $this->post->externalApplicant;
            //待处理人-二线专员
            $userIds = array();
            foreach ($this->lang->secondorder->apiDealUserList as $key => $value) {
                $userIds[] = $key;
            }
            $updateData->dealUser = implode(",", $userIds);
            //外部单号
            $updateData->externalCode = $this->post->externalCode;
            //是否是外部单子
            $updateData->formType = 'external';
            //请求类别
            $updateData->requestCategory = array_search($this->post->requestCategory, $this->lang->secondorder->requestCategoryList);
            //来电单位
            $updateData->callUnit = $this->post->callUnit;
            //来电单位联系方式
            $updateData->callUnitPhone = $this->post->callUnitPhone;
            //紧迫程度
            $updateData->urgencyLevel = array_search($this->post->urgencyLevel, $this->lang->secondorder->urgencyDegreeList);

            /* 删除原来的附件。*/
            $this->requestlog->deleteOldFile('secondorder', $secondorderID);
            /* 更新附件。*/
            foreach($this->post->file as $file){
                $url = str_replace($file['fileName'], rawurlencode($file['fileName']), $file['url']);
                $this->requestlog->downloadApiFile($url, $file['fileName'], 'secondorder', $secondorderID);
            }
            //清空受理人
            $updateData->ifAccept = '';
            $updateData->acceptDept = '';
            $updateData->acceptUser = '';
            $updateData->completeStatus = '';
            $updateData->progress = '';
            $updateData->consultRes = '';
            $updateData->rejectReason = $this->post->rejectReason;

            $this->dao->update(TABLE_SECONDORDER)->data($updateData)->where('id')->eq($secondorderID)->exec();
            $actionID = $this->loadModel('action')->create('secondorder', $secondorderID, 'sync', $this->post->note,'','guestcn');
            $changes = common::createChanges($secondorder, $updateData);
            if(!empty($changes)) $this->action->logHistory($actionID, $changes);
            $this->loadModel('consumed')->record('secondorder', $secondorderID, 0, 'guestcn', $secondorder->status, 'toconfirmed', array());
        }else{
            $insertData = new stdClass();
            //任务状态
            $insertData->status = 'toconfirmed';
            //任务来源方式
            $insertData->source = 'qz';
            //任务分类
            $insertData->type = 'consult';
            //子任务分类
            $insertData->subtype = 'a5';
            //期望完成时间
            $insertData->exceptDoneDate = date('Y-m-d', ($this->post->exceptDoneDate)/1000);
            //所属系统
            $applicationObj = $this->dao->select('*')->from(TABLE_APPLICATION)
                ->where('code')->eq($this->post->app)->andWhere('deleted')->ne(1)->fetch();
            if(!empty($applicationObj->id)){
                //所属系统
                $insertData->app = $applicationObj->id;
                //承建单位
                $insertData->team = $applicationObj->team;
                //业务司局
                $fromUnit = $this->lang->application->fromUnitList[$applicationObj->fromUnit];
                foreach ($this->lang->opinion->unionList as $key => $value) {
                    //因为所属系统的业务司局和二线工单的业务司局数据来源在各自的后台自定义里面，没法统一，只能通过值匹配来转换
                    if(!empty($value) and !empty($fromUnit)){
                        if(strpos($value, $fromUnit) !== false || strpos($fromUnit, $value) !== false){
                            $insertData->union = $key;
                            break;
                        }
                    }
                }
            }
            //联系电话
            $insertData->contactsPhone = $this->post->contactsPhone;
            //任务描述
            $insertData->desc = '【400客服咨询】'.$this->post->taskDescription;
            //任务摘要
            if(strlen($insertData->desc) > 250){
                $insertData->summary = mb_substr($insertData->desc, 0, 250).'...';
            }else{
                $insertData->summary = $insertData->desc;
            }
            //创建人
            $insertData->createdBy = 'guestcn';
            //创建时间
            $insertData->createdDate = helper::now();
            //外部申请部门
            $insertData->externalDept = $this->post->externalDept;
            //外部申请人
            $insertData->externalApplicant = $this->post->externalApplicant;
            //联系人
            $insertData->contacts = $this->post->externalApplicant;
            //待处理人-二线专员
            $userIds = array();
            foreach ($this->lang->secondorder->apiDealUserList as $key => $value) {
                $userIds[] = $key;
            }
            $insertData->dealUser = implode(",", $userIds);
            //外部单号
            $insertData->externalCode = $this->post->externalCode;
            //是否是外部单子
            $insertData->formType = 'external';
            //请求类别
            $insertData->requestCategory = array_search($this->post->requestCategory, $this->lang->secondorder->requestCategoryList);
            //来电单位
            $insertData->callUnit = $this->post->callUnit;
            //来电单位联系方式
            $insertData->callUnitPhone = $this->post->callUnitPhone;
            //紧迫程度
            $insertData->urgencyLevel = array_search($this->post->urgencyLevel, $this->lang->secondorder->urgencyDegreeList);

            $this->dao->insert(TABLE_SECONDORDER)->data($insertData)->exec();
            $secondorderID = $this->dao->lastInsertId();
            //创建编号
            $date   = date('Y-m-d');
            $number = $this->dao->select('count(id) c')->from(TABLE_SECONDORDER)->where('createdDate')->gt($date)->fetch('c');
            $code   = 'CFIT-T-' . date('Ymd-') . sprintf('%02d', $number);
            $this->dao->update(TABLE_SECONDORDER)->set('code')->eq($code)->where('id')->eq($secondorderID)->exec();
            /* 更新附件。*/
            foreach($this->post->file as $file){
                $url = str_replace($file['fileName'], rawurlencode($file['fileName']), $file['url']);
                $this->requestlog->downloadApiFile($url, $file['fileName'], 'secondorder', $secondorderID);
            }

            $actionID = $this->loadModel('action')->create('secondorder', $secondorderID, 'sync', $this->post->note,'','guestcn');
            $this->loadModel('consumed')->record('secondorder', $secondorderID, 0, 'guestcn', '', 'toconfirmed', array());
        }

        if(dao::isError()) {
            $this->requestlog->response('fail', dao::getError(), [], $logID, self::FAIL_CODE);
        }
        $this->dao->update(TABLE_REQUESTLOG)->set('objectId')->eq($secondorderID)->where('id')->eq($logID)->exec();
        $this->requestlog->response('success', $this->lang->api->successful, array('id' => $secondorderID), $logID);
    }

    /**
     * 校验
     * @return void
     */
    private function checkInput(){
        $errMsg = [];
        $this->loadModel('secondorder');
        //校验是否存在异常字段
        foreach ($_POST as $key => $v)
        {
            if(!isset($this->lang->secondorder->sendUniversalServiceItems[$key])){
                $errMsg[] = "通用型服务请求单".$key."不是协议字段";
            }
        }

        if(!empty($errMsg)){
            return $errMsg;
        }

        foreach ($this->lang->secondorder->sendUniversalServiceItems as $k => $v)
        {
            if($v['required'] && $this->post->$k == ''){
                $errMsg[] = "通用型服务请求单".$k.$v['name'].$this->post->$k.'不可以为空';
            }
            if($v['target'] != $k)
            {
                $_POST[$v['target']] = $this->post->$k;
                unset($_POST[$k]);
            }
        }
        return $errMsg;
    }
}
