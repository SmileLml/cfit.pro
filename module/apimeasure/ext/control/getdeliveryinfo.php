<?php
include '../../control.php';
class myApimeasure extends apimeasure
{
    const PARAMS_MISSING = 1001; //缺少参数
    const PARAMS_ERROR   = 1002; //缺少参数
    const FAIL_CODE   = 999;    //请求失败

    public function getDeliveryInfo()
    {
        $logID = $this->loadModel('requestlog')->insideSaveRequestLog('apimeasure' , 'getDeliveryInfo');

        $start = $_POST['start'];
        $end   = $_POST['end'] && $_POST['end'] != '' ? $_POST['end'] : date("Y-m-d H:i:s",time());
        // token以及参数校验
        $this->checkApiToken();
        $errMsg = $this->checkInput($end);
        if(!empty($errMsg)) {
            $this->loadModel('requestlog')->response('fail', implode(',',$errMsg), [], $logID, self::FAIL_CODE);
        }
        /** @var testingrequestModel $testingrequestModel */
        $testingrequestModel = $this->loadModel('testingrequest');
        // 所属项目、部门
        $projects = $this->dao->select('t1.project,t1.id,t2.name,t1.bearDept,t3.name as deptName,t1.mark')->from(TABLE_PROJECTPLAN)->alias('t1')
            ->leftjoin(TABLE_PROJECT)->alias('t2')
            ->on('t1.project=t2.id')
            ->leftjoin(TABLE_DEPT)->alias('t3')
            ->on('t1.bearDept=t3.id')
            ->where('t1.deleted')->eq(0)
            ->fetchAll();

        list($modifys,$modifycnccs,$depts) = $testingrequestModel->getModifysAndModifycnccs($start,$end,$projects);
        list($putproductions,$depts)       = $testingrequestModel->getPutproduction($start,$end,$projects,$depts);
        list($credits,$depts)              = $testingrequestModel->getCredit($start,$end,$projects,$depts);
        $testingrequests                   = $testingrequestModel->getTestingrequests($start,$end,$projects);

        /** @var sectransferModel $sectransferModel */
        $sectransferModel = $this->loadModel('sectransfer');
        $list1 = $sectransferModel->monthReportByOrder($start,$end,1);
        $list2 = $sectransferModel->monthReportByOrder($start,$end,2);
        $data1 = array_merge($list1,$list2);

        $list = array_merge($modifys,$modifycnccs,$putproductions,$credits,$testingrequests);

        $this->app->loadLang('apimeasure');
        $data = [];
        foreach ($list as $item) {
            $arr = [];
            $arr['deliveryNumber']       = $item->code;
            $arr['productionFailed']     = array_search($item->productionIsFail,$this->lang->apimeasure->whetherList);
            $arr['changeFailed']         = array_search($item->modifyIsFail,$this->lang->apimeasure->whetherList);
            $arr['type']                 = $item->objectType;
            $arr['actualDeliveryCount']  = $item->count;
            $arr['planDeliveryCount']    = $item->times;
            $arr['rollbackCount']        = $item->returnTime;
            $arr['ifCBP']                = array_search($item->isCBP,$this->lang->apimeasure->whetherList);
            $arr['implementationWay']    = $item->fixType;
            $arr['projectCode']          = $item->method;
            $arr['departmentId']         = $item->deptId;
            $arr['deliveryStatus']       = array_search($item->statusEn,$this->lang->apimeasure->{$item->objectType}->statusList);//状态
            $arr['completedTime']        = $item->createdDate;//完成时间
            $arr['productInfoCode']      = $item->productInfoCode;//产品编号
            $arr['exceptionDescription'] = $item->returnReason;
            $arr['mode']                 = isset($item->mode) ? $item->mode : '';//变更类型
            $data[] = $arr;
        }
        foreach ($data1 as $val) {
            $val = (object)$val;
            $arr = [];
            $arr['deliveryNumber']       = $val->code;
            $arr['productionFailed']     = array_search($val->isPutproductionFail,$this->lang->apimeasure->whetherList);
            $arr['changeFailed']         = array_search($val->isModifyFail,$this->lang->apimeasure->whetherList);
            $arr['type']                 = $val->reportType;
            $arr['actualDeliveryCount']  = $val->deliveryNum;
            $arr['planDeliveryCount']    = $val->projectNum;
            $arr['rollbackCount']        = $val->returnNum;
            $arr['ifCBP']                = array_search($val->isCBP,$this->lang->apimeasure->whetherList);
            $arr['implementationWay']    = $val->fixType;
            $arr['projectCode']          = $val->projectCode;
            $arr['departmentId']         = $val->deptId;
            $arr['deliveryStatus']       = $val->deptID;//状态
            $arr['deliveryStatus']       = array_search($val->statusKey,$this->lang->apimeasure->{$val->reportType}->statusList);//状态
            $arr['completedTime']        = $val->endTime;//完成时间
            $arr['productInfoCode']      = $val->productCode != '/' ? $val->productCode : '';//产品编号
            $arr['exceptionDescription'] = $val->rejectReason;
            $arr['mode']                 = '';//变更类型
            $data[] = $arr;
        }

        if(dao::isError()) {
            $this->loadModel('requestlog')->response('fail', dao::getError(), [], $logID, self::FAIL_CODE);
        }
        $this->loadModel('requestlog')->response('success', $this->lang->api->successful, $data,$logID);
    }

    /**
     * 校验
     * @return void
     */
    private function checkInput($end){
        $fileds = array(
            'start' => 'start',
            'end'   => 'end',
        );
        $errMsg = [];
        //校验是否存在异常字段
        foreach ($_POST as $key => $v)
        {
            if(!isset($fileds[$key])){
                $errMsg[] = $key.$this->lang->api->nameError;
            }
        }

        if(!empty($errMsg)){
            return $errMsg;
        }
        if ($_POST['start'] == ''){
            $errMsg[] = 'start'.$this->lang->api->emptyError;
        }

        if ($_POST['start'] >= $end){
            $errMsg[] = '开始时间不得大于等于结束时间';
        }
        return $errMsg;
    }
}
