<?php
/**
 * 需求任务
 */
include '../../control.php';
class myApi extends api
{
    const PARAMS_ERROR = 1001;
    const HAD_CHANGE   = 1003;
    const FAIL_CODE   = 999;    //请求失败

    public function entries()
    {
        /* 保存请求日志并检查请求参数。 */
        $logID = $this->loadModel('requestlog')->insideSaveRequestLog('requirement' , 'createRequirement');
        $errMsg = $this->checkInput();
        if(!empty($errMsg)) {
            $this->requestlog->response('fail', implode(',',$errMsg), [], $logID, self::FAIL_CODE);
        }
        $this->requestlog->judgeRequestMode($logID);

        /* 判断所需字段是否存在。*/
        $data = fixer::input('post')
            ->stripTags('Requirement_item_description', $this->config->allowedTags)
            ->get();

        /**
         * 需求变更单号不存在打回
         * @var requirementChangeModel $requirementChangeModel
         */
        $requirementChangeModel = $this->loadModel('requirementchange');
        if($data->ChangeOrder_number != ''){
//            if(empty($data->ChangeOrder_Number)){
//                $this->requestlog->response('fail', $this->lang->api->requirementChangeEmpty, array(), $logID,self::PARAMS_ERROR);
//            }
            $requirementChangeInfo = $requirementChangeModel->getByChangeNumber($data->ChangeOrder_number);
            if(!$requirementChangeInfo){
                $this->requestlog->response('fail', $this->lang->api->noRequirementChange, array('ChangeOrder_number'=>$data->ChangeOrder_number), $logID,self::HAD_CHANGE);
            }
        }

        foreach($this->config->api->entriesParams as $param)
        {
            if(!isset($data->{$param}) && $data->ChangeOrder_number != '')
            {
                $errorMessage = sprintf($this->lang->api->fieldMissing, $param);
                $this->requestlog->response('fail', $errorMessage, array(), $logID);
            }
        }

        if(isset($data->ChangeOrder_number) && (!empty($data->ChangeOrder_number) || empty($data->ChangeOrder_number))){  //有字段ChangeOrder_number就是创建或者编辑需求任务接口，不是变更计划完成时间、所属研发子项接口
          //类型
          if(!$data->RequirementType || empty(array_filter($data->RequirementType))){
              $errorMessage = sprintf($this->lang->api->fieldEmpty, 'RequirementType');
              $this->requestlog->response('fail', $errorMessage, array(), $logID);
          }
          //启动时间
          if(!$data->ProductRequireStartTime){
              $errorMessage = sprintf($this->lang->api->fieldEmpty, 'ProductRequireStartTime');
              $this->requestlog->response('fail', $errorMessage, array(), $logID);
          }
        }


        /**
        *  兼容清总升级
        if ($data->Canceled == ''){
            $this->requestlog->response('fail','Canceled参数不能为空' , [], $logID,self::HAD_CHANGE);
        }
        if (!in_array($data->Canceled,[0,1])){
            $this->requestlog->response('fail','Canceled参数值有误请检查' , [], $logID,self::HAD_CHANGE);
        }
         * */
        $this->loadModel('requirement');
        $requirement = $this->requirement->getByCode($_POST['Demand_item_number']);
        $this->loadModel('opinion');
        $opinion = $this->opinion->getByCode($_POST['Demand_number']);
        if(empty($opinion))
        {
            $opinionEmpty = sprintf($this->lang->api->opinionEmpty, $_POST['Demand_number']);
            $this->requestlog->response('fail', $opinionEmpty, array(), $logID);
        }
        /* 判断需求是否已同步。*/
        if(empty($requirement))
        {
            if ($data->ChangeOrder_number == '' && isset($_POST['end'])){
                $this->requestlog->response('fail', sprintf($this->lang->api->notAllowField,'end'), array('end'=>$_POST['end']), $logID,self::PARAMS_ERROR);
            }
            /* 对必填字段做处理。*/
            unset($_POST);
            $this->config->requirement->create->requiredFields = '';

            /* 设置参数到post中。*/
            foreach($this->config->api->entriesFields as $paramName => $field)
            {
                if($field == 'product')
                {
                    $productIdArray = Array();
                    foreach ($data->{$paramName} as $productCode){
                        $productID = $this->dao->select('id')->from(TABLE_PRODUCT)->where('code')->eq($productCode)->fetch('id');
                        array_push($productIdArray, $productID);
                    }
                    $productIdStr = implode(",", $productIdArray);
                    $this->post->set('product', $productIdStr);
                    continue;
                }

                if($field == 'line')
                {
                    $lineIdArray = Array();
                    foreach ($data->{$paramName} as $lineCode){
                        $lineID = $this->dao->select('id')->from(TABLE_PRODUCTLINE)->where('code')->eq($lineCode)->fetch('id');
                        array_push($lineIdArray, $lineID);
                    }
                    $lineIdStr = implode(",", $lineIdArray);
                    $this->post->set('line', $lineIdStr);
                    continue;
                }
                if($field == 'type'){
                    $typeArray = $data->{$paramName};
                    $type = $typeArray ? $typeArray[0]: '';
                    $this->post->set('type', $type);
                    continue;
                }

                $this->post->set($field, $data->{$paramName});
            }
            // 更新需求代号。
            $date   = helper::today();
            $codeBefore = substr( $date, 0, 4) . sprintf('%03d', $opinion->id);
            $number = $this->dao->select('count(id) c')
                ->from(TABLE_REQUIREMENT)
                ->where('code')
                ->like($codeBefore.'%')
                ->fetch('c');

            $code   = $codeBefore . '-' . sprintf('%02d', $number+1);

            $this->post->set('status', 'topublish');
            $this->post->set('opinion', $opinion->id);
            $this->post->set('code', $code);
            $this->post->set('method', '');
            $this->post->set('createdBy', 'guestcn');
            $this->post->set('createdDate', helper::now());
            $this->post->set('acceptTime', helper::now());
            $this->post->set('desc', $data->Requirement_item_description);
            $this->post->set('feedbackStatus', 'tofeedback');

            /* 调用创建方法，判罚是否成功创建。*/
            $requirementID = $this->requirement->createApi($opinion->id);
            /* 更新需求意向已拆分。*/
            $this->dao->update(TABLE_OPINION)->set('status')->eq('subdivided')->where('id')->eq($opinion->id)->exec();
            if(dao::isError())
            {
                $errors = dao::getError();
                $this->requestlog->response('fail', json_encode($errors), array(), $logID);
            }

            $actionID = $this->loadModel('action')->create('requirement', $requirementID, 'created', $this->lang->api->syncCreate,'','guestcn');
            //$this->loadModel('requirement')->sendmail($requirementID, $actionID);
            $this->action->create('opinion', $opinion->id, 'subdivide', $this->lang->api->syncSubdivide, $requirementID,'guestcn');
            $this->loadModel('consumed')->record('requirement', $requirementID, 0, 'guestcn', '', 'topublish', array());
            $this->loadModel('consumed')->record('opinion', $opinion->id, 0, 'guestcn', $opinion->status, 'subdivided');
            if ($data->Canceled == 1){
                $this->loadModel('requirement')->closeApi($requirement->id,'清总取消转挂起','guestcn');
            }
            $this->requestlog->response('success', $this->lang->api->successful, array('id' => $requirementID), $logID);
        }
        else
        {
            /**
             * 需求收集2597
             * 1.带有变更单号的允许变更，状态回滚至初态。（目前线上符合逻辑）
             * 2.未带变更单号，仅允许变更【所属研发子项】，状态不做回滚，若存在其他字段则拒收。
             */
            if ($data->ChangeOrder_number == ''){
                foreach ($_POST as $key => $v){
                    if(!in_array($key,$this->config->api->entriesNoEdit)){
                        $errMsg[] = $key."字段如需修改，请先发起变更单。";
                    }
                }
            }
            if(!empty($errMsg)) {
                $this->requestlog->response('fail', implode(',',$errMsg), [], $logID, self::FAIL_CODE);
            }
            /* 对必填字段做处理。*/
            unset($_POST);
            $this->config->requirement->create->requiredFields = '';

            /* 设置参数到post中。*/
            foreach($this->config->api->entriesFields as $paramName => $field)
            {
                if($field == 'product')
                {
                    $productIdArray = Array();
                    foreach ($data->{$paramName} as $productCode){
                        $productID = $this->dao->select('id')->from(TABLE_PRODUCT)->where('code')->eq($productCode)->fetch('id');
                        array_push($productIdArray, $productID);
                    }
                    $productIdStr = implode(",", $productIdArray);
                    $this->post->set('product', $productIdStr);
                    continue;
                }

                if($field == 'line')
                {
                    $lineIdArray = Array();
                    foreach ($data->{$paramName} as $lineCode){
                        $lineID = $this->dao->select('id')->from(TABLE_PRODUCTLINE)->where('code')->eq($lineCode)->fetch('id');
                        array_push($lineIdArray, $lineID);
                    }
                    $lineIdStr = implode(",", $lineIdArray);
                    $this->post->set('line', $lineIdStr);
                    continue;
                }
                if($field == 'type'){
                    $typeArray = $data->{$paramName};
                    $type = $typeArray ? $typeArray[0]: '';
                    $this->post->set('type', $type);
                    continue;
                }

                $this->post->set($field, $data->{$paramName});
            }
            $this->post->set('desc', $data->Requirement_item_description);



            /* 调用编辑方法，判罚是否成功创建。*/
            $changes = $this->requirement->updateApi($requirement->id);
            if(dao::isError())
            {
                $errors = dao::getError();
                $this->requestlog->response('fail', json_encode($errors), array(), $logID);
            }
            //所属研发子项不算接口更新
            if(!empty($changes) && (count($changes)==1 || count($changes) ==0) && $changes[0]['field'] == 'ChildName'){
                $actionID = $this->loadModel('action')->create('requirement', $requirement->id, 'childedit', '','','guestcn');
            }else if(!empty($changes) && (count($changes)==1 || count($changes) ==0) && $changes[0]['field'] == 'end'){
                $actionID = $this->loadModel('action')->create('requirement', $requirement->id, 'endedit', '','','guestcn');
            }else{
                $actionID = $this->loadModel('action')->create('requirement', $requirement->id, 'edited', $this->lang->api->syncUpdate,'','guestcn');
            }
            //$this->loadModel('requirement')->sendmail($requirement->id, $actionID);
//            $this->loadModel('consumed')->record('requirement', $requirement->id, 0, 'guestcn', $requirement->status, $requirement->status, array());
            if(!empty($changes)) $this->action->logHistory($actionID, $changes);
            //清总取消转挂起
            if ($data->Canceled == 1){
                $this->loadModel('requirement')->closeApi($requirement->id,'清总取消转挂起','guestcn');
            }
            if ($data->Canceled == 0 && $requirement->status == 'closed'){
                $dealcomment = "清总激活";
                /* 当请求方式为post时，更新需求条目的状态为关闭。判断所属需求意向下的需求条目都关闭时，关闭需求意向。*/
                $this->dao->update(TABLE_REQUIREMENT)->set('status')->eq($requirement->lastStatus)->set('activatedBy')->eq('guestcn')->set('activatedDate')->eq(helper::now())->where('id')->eq($requirement->id)->exec();

                $demands = $this->loadModel("demand")->getBrowesByRequirementID($requirement->id);
                $data = new stdclass();
                foreach($demands as $demand){
                    if($demand->status != 'suspend'){
                        continue;
                    }
                    $data->status = $demand->lastStatus; // 记录关闭前状态
                    $data->activatedBy = 'guestcn';
                    $data->activatedDate = helper::today();
                    $this->dao->update(TABLE_DEMAND)
                        ->data($data)
                        ->where('id')->eq($demand->id)
                        ->exec();;
                    if(!dao::isError())
                    {
                        $this->loadModel('action')->create('demand', $demand->id, 'activated', $dealcomment,'','guestcn');
                        $this->loadModel('consumed')->record('demand', $demand->id, 0, 'guestcn', 'suspend', $demand->lastStatus);
                    }
                }

                $this->loadModel('consumed')->record('requirement', $requirement->id, 0, 'guestcn', $requirement->status, $requirement->lastStatus, array());
                $this->loadModel('action')->create('requirement', $requirement->id, 'activate', $dealcomment,'','guestcn');
            }
            $this->requestlog->response('success', $this->lang->api->successful, array('id' => $requirement->id), $logID);
        }
    }
    private function checkInput(){
        $errMsg = [];
        //校验是否存在异常字段
        foreach ($_POST as $key => $v)
        {
//            a($this->config->api->entriesFields);
            if(!isset($this->config->api->entriesFields[$key])){
                $errMsg[] = $key."不是协议字段";
            }
        }
        return $errMsg;
    }
}
