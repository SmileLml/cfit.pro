<?php
class secondmonthreportModel extends model
{

    public function showrealtimedata($start,$end,$deptID,$columnKey,$staticType,$isuseHisData){
        if($deptID == '_1'){
            $deptID = -1;
        }
        //处理时间
        $actionChangeDate = $this->actionDateToSecond($start,$end);
//        $this->loadModel('problem');
        $multflag = false;
        if($staticType == 'problemOverall'){
            $dataList = $this->getProblemDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);
            $dataList = $this->problemproblemOverallStatic($dataList,$deptID);
        }else if($staticType == 'problemWaitSolve'){
            $dataList = $this->getProblemDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);
            $dataList = $this->problemproblemWaitSolveStatic($dataList,$deptID);

        }else if($staticType == 'problemUnresolved'){
            $dataList = $this->getProblemDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);
            $dataList = $this->problemproblemUnresolvedStatic($dataList,$deptID);

        }else if($staticType == 'problemExceed'){
            $dataList = $this->getProblemDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);
            $dataList = $this->problemproblemExceedStatic($dataList,$deptID);

        }else if($staticType == 'problemExceedBackIn'){
            $dataList = $this->getProblemDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);
            $dataList = $this->problemproblemExceedBackInStatic($dataList,$deptID);

        }else if($staticType == 'problemExceedBackOut'){
            $dataList = $this->getProblemDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);
            $dataList = $this->problemproblemExceedBackOutStatic($dataList,$deptID);

        }else if($staticType == 'demand_whole'){
            $dataList = $this->getDemandDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);
            $dataList = $this->demandwholeDemandMonthStatic($dataList,$deptID);

        }else if($staticType == 'demandunrealized'){
            $dataList = $this->getDemandDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);
            $dataList = $this->demandunrealizedStatic($dataList,$deptID);

        }else if($staticType == 'demand_realized'){
            $dataList = $this->getDemandDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);
            $dataList = $this->demandrealizedMonthStatic($dataList,$deptID);
        }else if($staticType == 'requirement_inside'){
            $dataList = $this->getRequirementDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);
            $dataList = $this->requirementInsideStatic($dataList,$deptID);
        }else if($staticType == 'requirement_outside'){
            $dataList = $this->getRequirementDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);
            $dataList = $this->requirementOutsideStatic($dataList,$deptID);
        }else if($staticType == 'secondorderclass'){
            $dataList = $this->getSecondorderDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);
            $dataList = $this->secondorderclassStatic($dataList,$deptID);
        }else if($staticType == 'secondorderaccept'){
            $dataList = $this->getSecondorderDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);
            $dataList = $this->secondorderacceptStatic($dataList,$deptID);
        }else if($staticType == 'support'){
            $dataList = $this->getSupportDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);
            $dataList = $this->supportStatic($dataList,$deptID);
        }else if($staticType == 'workload'){
            $dataList = $this->getWorkloadDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);
            $dataList = $this->workloadStatic($dataList,$deptID);
        }else if($staticType == 'modifywhole'){
            $multflag = true;
            $dataList = [];
            $dataList['modify'] = $this->getModifyDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);
            $dataList['modifycncc'] = $this->getModifycnccDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);
            $dataList['credit'] = $this->getCreditDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);
            $dataList = $this->modifywholeStatic($dataList,$deptID);
        }else if($staticType == 'modifyabnormal'){
            $multflag = true;
            $dataList = $this->loadModel('secondmonthreport')->getModifyFinishData($actionChangeDate['start'],$actionChangeDate['end'],$deptID);
            //$dataList['modify'] = $this->getModifyDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);
            //$dataList['modifycncc'] = $this->getModifycnccDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);
            //$dataList['credit'] = $this->getCreditDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);

            $dataList = $this->modifyabnormalStatic($dataList,$deptID);
        }

        //获取 表单 字段
        $fieldsArr = $this->getexportField($staticType,'');

        $fieldArr = explode(',',$fieldsArr['form']);
        //查看 部门合计
        $res = [];
        //去除数据
        if($multflag){

            foreach ($dataList['multkey'] as $datakey){
                if($deptID == 0){
                    $res[$datakey] = [];
                    if($dataList['deptids']){
                        //遍历部门id
                        foreach ($dataList['deptids'] as $kdept){
                            if (isset($dataList['detail'][$datakey][$kdept][$columnKey])){
                                $res[$datakey] = array_merge($res[$datakey],$dataList['detail'][$datakey][$kdept][$columnKey]);
                            }
                        }

                    }

                }else{

                    if (isset($dataList['detail'][$datakey][$deptID][$columnKey])){
                        $res[$datakey] = $dataList['detail'][$datakey][$deptID][$columnKey];
                    }else{
                        $res[$datakey] = [];
                    }
                }
            }

        }else{
            if($deptID == 0){
                if($dataList['deptids']){
                    foreach ($dataList['deptids'] as $kdept){
                        if (isset($dataList['detail'][$kdept][$columnKey])){
                            $res = array_merge($res,$dataList['detail'][$kdept][$columnKey]);
                        }
                    }

                }
            }else{

                if (isset($dataList['detail'][$deptID][$columnKey])){
                    $res = $dataList['detail'][$deptID][$columnKey];
                }else{
                    $res = [];
                }
            }
        }


        //将数据 和展示字段进行 格式化
        if($res){
            if(in_array($staticType,$this->lang->secondmonthreport->problemStaticTypeList)){
                $res = $this->handleproblemData($res,$fieldArr);
            }else if(in_array($staticType,$this->lang->secondmonthreport->demandStaticTypeList)){
                $res = $this->handledemandData($res,$fieldArr);
            }else if(in_array($staticType,$this->lang->secondmonthreport->requirementStaticTypeList)){
                $res = $this->handlerequirementData($res,$fieldArr);
            }else if(in_array($staticType,$this->lang->secondmonthreport->secondorderStaticTypeList)){
                $res = $this->handlesecondorderData($res,$fieldArr);
            }else if(in_array($staticType,$this->lang->secondmonthreport->supportStaticTypeList)){
                $res = $this->handlesupportData($res,$fieldArr);
            }else if(in_array($staticType,$this->lang->secondmonthreport->workloadStaticTypeList)){

                $res = $this->handleworkloadData($res,$fieldArr);
            }else if(in_array($staticType,$this->lang->secondmonthreport->modifyStaticTypeList)){

                $res = $this->handlemodifywholeData($res,$fieldArr);
            }

        }

        return ['resdata'=>$res,'field'=>$fieldsArr['form'],'multflag'=>$multflag];

    }

    public function getsnapphotoData($start,$end,$deptID,$phototype,$fieldArr,$staticType,$isuseHisData){
        if($deptID == '_1'){
            $deptID = -1;
        }
        //处理时间
        $actionChangeDate = $this->actionDateToSecond($start,$end);
//        $this->loadModel('problem');
        //基础数据 是全量的

        $dataList = [];
        //表单快照数据 是统计表使用到的

        if($staticType == 'problemOverall'){
            $dataList = $this->getProblemDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);
            if($phototype == 'form'){
                $dataList = $this->problemproblemOverallStatic($dataList,$deptID);
                $dataList = $this->getProblemDataListByIDs($dataList['useids']);
            }
        }else if($staticType == 'problemWaitSolve'){
            $dataList = $this->getProblemDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);
            if($phototype == 'form'){
                $dataList = $this->problemproblemWaitSolveStatic($dataList,$deptID);
                $dataList = $this->getProblemDataListByIDs($dataList['useids']);
            }

        }else if($staticType == 'problemUnresolved'){
            $dataList = $this->getProblemDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);
            if($phototype == 'form'){
                $dataList = $this->problemproblemUnresolvedStatic($dataList,$deptID);
                $dataList = $this->getProblemDataListByIDs($dataList['useids']);
            }

        }else if($staticType == 'problemExceed'){
            $dataList = $this->getProblemDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);
            if($phototype == 'form'){
                $dataList = $this->problemproblemExceedStatic($dataList,$deptID);
                $dataList = $this->getProblemDataListByIDs($dataList['useids']);
            }

        }else if($staticType == 'problemExceedBackIn'){
            $dataList = $this->getProblemDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);
            if($phototype == 'form'){
                $dataList = $this->problemproblemExceedBackInStatic($dataList,$deptID);
                $dataList = $this->getProblemDataListByIDs($dataList['useids']);
            }

        }else if($staticType == 'problemExceedBackOut'){
            $dataList = $this->getProblemDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);
            if($phototype == 'form'){
                $dataList = $this->problemproblemExceedBackOutStatic($dataList,$deptID);
                $dataList = $this->getProblemDataListByIDs($dataList['useids']);
            }

        }else if($staticType == 'demand_whole'){
            $dataList = $this->getDemandDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);
            if($phototype == 'form'){
                $dataList = $this->demandwholeDemandMonthStatic($dataList,$deptID);
                $dataList = $this->getDemandDataListByIDs($dataList['useids']);
            }

        }else if($staticType == 'demandunrealized'){
            $dataList = $this->getDemandDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);
            if($phototype == 'form'){
                $dataList = $this->demandunrealizedStatic($dataList,$deptID);
                $dataList = $this->getDemandDataListByIDs($dataList['useids']);
            }

        }else if($staticType == 'demand_realized'){
            $dataList = $this->getDemandDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);
            if($phototype == 'form'){
                $dataList = $this->demandrealizedMonthStatic($dataList,$deptID);
                $dataList = $this->getDemandDataListByIDs($dataList['useids']);
            }
        }else if($staticType == 'requirement_inside'){
            $dataList = $this->getRequirementDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);
            if($phototype == 'form'){
                $dataList = $this->requirementInsideStatic($dataList,$deptID);
                $dataList = $this->getRequirementDataListByIDs($dataList['useids']);
            }
        }else if($staticType == 'requirement_outside'){
            $dataList = $this->getRequirementDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);
            if($phototype == 'form'){
                $dataList = $this->requirementOutsideStatic($dataList,$deptID);
                $dataList = $this->getRequirementDataListByIDs($dataList['useids']);
            }
        }else if($staticType == 'secondorderclass'){
            $dataList = $this->getSecondorderDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);
            if($phototype == 'form'){
                $dataList = $this->secondorderclassStatic($dataList,$deptID);
                $dataList = $this->getSecondorderDataListByIDs($dataList['useids']);
            }
        }else if($staticType == 'secondorderaccept'){
            $dataList = $this->getSecondorderDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);
            if($phototype == 'form'){
                $dataList = $this->secondorderacceptStatic($dataList,$deptID);
                $dataList = $this->getSecondorderDataListByIDs($dataList['useids']);
            }
        }else if($staticType == 'support'){
            $dataList = $this->getSupportDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);
            if($phototype == 'form'){
                $dataList = $this->supportStatic($dataList,$deptID);
                $dataList = $this->getSupportDataListByIDs($dataList['useids']);
            }
        }else if($staticType == 'workload'){
            $dataList = $this->getWorkloadDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);
            if($phototype == 'form'){
                $dataList = $this->workloadStatic($dataList,$deptID);
                $dataList = $this->getWorkloadDataListByIDs($dataList['useids']);
            }
        }else if($staticType == 'modifywhole'){
            $multflag = true;
            $dataList = [];
            $dataList['modify'] = $this->getModifyDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);
            $dataList['modifycncc'] = $this->getModifycnccDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);
            $dataList['credit'] = $this->getCreditDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);

            if($phototype == 'form'){
                $tempdataList = $this->modifywholeStatic($dataList,$deptID);
                $dataList['modify'] = $this->getModifyDataListByIDs($tempdataList['useids']['modify']);
                $dataList['modifycncc'] = $this->getModifycnccDataListByIDs($tempdataList['useids']['modifycncc']);
                $dataList['credit'] = $this->getCreditDataListByIDs($tempdataList['useids']['credit']);
            }
        }else if($staticType == 'modifyabnormal'){
            $multflag = true;
            $dataList = $this->loadModel('secondmonthreport')->getModifyFinishData($actionChangeDate['start'],$actionChangeDate['end'],$deptID);
            //$dataList['modify'] = $this->getModifyDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);
            //$dataList['modifycncc'] = $this->getModifycnccDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);
            //$dataList['credit'] = $this->getCreditDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);
            if($phototype == 'form'){
                $tempdataList = $this->modifyabnormalStatic($dataList,$deptID);
                $dataList['modify'] = $this->getModifyDataListByIDs($tempdataList['useids']['modify']);
                $dataList['modifycncc'] = $this->getModifycnccDataListByIDs($tempdataList['useids']['modifycncc']);
                $dataList['credit'] = $this->getCreditDataListByIDs($tempdataList['useids']['credit']);
            }
        }



        if ($dataList){
            if(in_array($staticType,$this->lang->secondmonthreport->problemStaticTypeList)){
                $res = $this->handleproblemData($dataList,$fieldArr);
            }else if(in_array($staticType,$this->lang->secondmonthreport->demandStaticTypeList)){
                $res = $this->handledemandData($dataList,$fieldArr);
            }else if(in_array($staticType,$this->lang->secondmonthreport->requirementStaticTypeList)){
                $res = $this->handlerequirementData($dataList,$fieldArr);
            }else if(in_array($staticType,$this->lang->secondmonthreport->secondorderStaticTypeList)){
                $res = $this->handlesecondorderData($dataList,$fieldArr);
            }else if(in_array($staticType,$this->lang->secondmonthreport->supportStaticTypeList)){
                $res = $this->handlesupportData($dataList,$fieldArr);
            }else if(in_array($staticType,$this->lang->secondmonthreport->workloadStaticTypeList)){
                $res = $this->handleworkloadData($dataList,$fieldArr);
            }else if(in_array($staticType,$this->lang->secondmonthreport->modifyStaticTypeList)){
                $res = $this->handlemodifywholeData($dataList,$fieldArr);
            }

        }else{
            $res = [];
        }


        return $res;

    }

    public function getProblemReal($start,$end,$staticType,$deptID,$isuseHisData){
        $actionChangeDate = $this->actionDateToSecond($start,$end);

        if($staticType == 'problemWaitSolve'){
            $dataList = $this->getProblemDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);
            $res = $this->problemproblemWaitSolveStatic($dataList,$deptID);
        }else if($staticType == 'problemOverall'){
            $dataList = $this->getProblemDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);
            $res = $this->problemproblemOverallStatic($dataList,$deptID);
        }else if($staticType == 'problemUnresolved'){
            $dataList = $this->getProblemDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);
            $res = $this->problemproblemUnresolvedStatic($dataList,$deptID);
        }else if($staticType == 'problemExceed'){
            $dataList = $this->getProblemDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);
            $res = $this->problemproblemExceedStatic($dataList,$deptID);
        }else if($staticType == 'problemExceedBackIn'){
            $dataList = $this->getProblemDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);
            $res = $this->problemproblemExceedBackInStatic($dataList,$deptID);
        }else if($staticType == 'problemExceedBackOut'){
            $dataList = $this->getProblemDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);
            $res = $this->problemproblemExceedBackOutStatic($dataList,$deptID);
        }else if($staticType == 'demand_whole'){
            $dataList = $this->getDemandDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);
            $res = $this->demandwholeDemandMonthStatic($dataList,$deptID);
        }else if($staticType == 'demandunrealized'){
            $dataList = $this->getDemandDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);
            $res = $this->demandunrealizedStatic($dataList,$deptID);
        }else if($staticType == 'demand_realized'){
            $dataList = $this->getDemandDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);
            $res = $this->demandrealizedMonthStatic($dataList,$deptID);
        }else if($staticType == 'requirement_inside'){
            $dataList = $this->getRequirementDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);
            $res = $this->requirementInsideStatic($dataList,$deptID);
        }else if($staticType == 'requirement_outside'){
            $dataList = $this->getRequirementDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);
            $res = $this->requirementOutsideStatic($dataList,$deptID);
        }else if($staticType == 'secondorderclass'){
            $dataList = $this->getSecondorderDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);
            $res = $this->secondorderclassStatic($dataList,$deptID);
        }else if($staticType == 'secondorderaccept'){
            $dataList = $this->getSecondorderDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);
            $res = $this->secondorderacceptStatic($dataList,$deptID);
        }else if($staticType == 'support'){
            $dataList = $this->getSupportDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);
            $res = $this->supportStatic($dataList,$deptID);
        }else if($staticType == 'workload'){
            $dataList = $this->getWorkloadDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);
            $res = $this->workloadStatic($dataList,$deptID);
        }else if($staticType == 'modifywhole'){
            $dataList = [];
            $dataList['modify'] = $this->getModifyDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);
            $dataList['modifycncc'] = $this->getModifycnccDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);
            $dataList['credit'] = $this->getCreditDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);
            $res = $this->modifywholeStatic($dataList,$deptID);
        }else if($staticType == 'modifyabnormal'){
            $dataList = $this->loadModel('secondmonthreport')->getModifyFinishData($actionChangeDate['start'],$actionChangeDate['end'],$deptID);
            //$dataList['modify'] = $this->getModifyDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);
            //$dataList['modifycncc'] = $this->getModifycnccDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);
            //$dataList['credit'] = $this->getCreditDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);
            $res = $this->modifyabnormalStatic($dataList,$deptID);
        }

        return $this->DataToReportFormat($res['staticdata'],$staticType);
    }
    public function actionDateToSecond($start,$end){
        $start = str_replace('_','-',$start);
        $end = str_replace('_','-',$end);

        $curdate = date("Y-m-d");
        if($curdate == $end){
            $endDateSecond = date("Y-m-d H:i:s",time());
        }else{
            $endDateSecond = date("Y-m-d 23:59:59",strtotime($end));
        }

        $startDateSecond = date("Y-m-d 00:00:00",strtotime($start));

        return ['start'=>$startDateSecond,'end'=>$endDateSecond,'oldstart'=>$start,'oldend'=>$end];

    }

    public function arrayToObject($arrayData){
        foreach ($arrayData as $dept=>$data){
            $arrayData[$dept] = (object)$data;
        }
        return $arrayData;
    }

    public function DataToReportFormat($arrayData,$staticType){
        $tempDataArr = [];
        foreach ($arrayData as $dept=>$data){
            $tempate = new stdClass();
            $tempate->id = 0;
            $tempate->tableType = $staticType;
            $tempate->wholeID = 0;
            $tempate->deptID = $dept;
            $tempate->detail = (object)$data;
            $tempDataArr[$dept] = $tempate;
            unset($tempate);

        }
        return $tempDataArr;
    }

    public function handleproblemData($dataArr,$fields){
        $this->loadModel('problem');
        $this->loadModel('file');
        $problemLang   = $this->lang->problem;

        $problemIDs = array_keys($dataArr);

        // Create field lists.
        foreach ($fields as $key => $fieldName) {
            $fieldName          = trim($fieldName);
            $fields[$fieldName] = $problemLang->{$fieldName} ?? $fieldName;
            unset($fields[$key]);
        }

        // Get problems.
        $field       = 't2.objectId,t2.originalResolutionDate,t2.delayResolutionDate,t2.delayReason,t2.delayStatus,t2.delayVersion,t2.delayStage,t2.delayDealUser,t2.delayUser,t2.delayDate';
        $problemdelay    = $this->dao
            ->select("{$field}")
            ->from(TABLE_DELAY)->alias('t2')
//            ->where('t1.status')->ne('deleted')
            ->where('t2.objectType')->eq('problem')
            ->andWhere('t2.objectId')->in($problemIDs)

            ->fetchAll('objectId');

        // Get users, products and executions.
        $users = $this->loadModel('user')->getPairs('noletter');
        $apps  = $this->loadModel('application')->getPairs();
        $depts = $this->getremovedeptbias();
        $dmap  = $this->dao->select('account,dept')->from(TABLE_USER)->fetchAll('account');

        // Obtain the receiver.


        $this->loadModel('secondline');
        foreach ($dataArr as $problem) {
            //是否超期
//            $problem                                     = $this->loadModel('problem')->getIsExceed($problem);
            $problem                                     = $this->problem->getIfOverDate($problem);
            $problem->monthreportfeedbackStartTimeInside = $problem->ifOverDateInside['start'] ?? '';
            $problem->monthreportfeedbackEndTimeInside   = $problem->deptPassTime              ?? '';
            $problem->monthreportdealAssigned            = $problem->dealAssigned              ?? '';
            $problem->insideFeedbackDate                 = $problem->ifOverDateInside['end']   ?? '';
            $problem->feedbackStartTimeOutside           = $problem->ifOverDate['start']       ?? '';
            $problem->monthreportfeedbackEndTimeOutside  = $problem->innovationPassTime        ?? '';
            $problem->outsideFeedbackDate                = $problem->ifOverDate['end']         ?? '';
            $problem->ifOverTime                         = $problem->ifOverDate['flag']        ?? '';
            $problem->ifOverTimeInside                   = $problem->ifOverDateInside['flag']  ?? '';
            $problem->source     = $problemLang->sourceList[$problem->source];
            $problem->type       = $problemLang->typeList[$problem->type];

            $problem->acceptUser = zget($users, $problem->acceptUser, '');
            $problem->acceptDept = zget($depts, $problem->acceptDept, '');

            $problem->createdDept = $dmap[$problem->createdBy]->dept ? $depts[$dmap[$problem->createdBy]->dept] : '';

            $problem->monthreportcreatedBy = $users[$problem->createdBy];
            $problem->fixType              = zget($problemLang->fixTypeList, $problem->fixType, '');

            if (in_array($problem->status, ['feedbacked', 'build', 'released', 'delivery', 'onlinesuccess', 'closed'])) {
                $problem->dealUser = '';
            } else {
                $problem->dealUser = zmget($users, $problem->dealUser);
            }
            $problem->editedBy   = zget($users, $problem->editedBy, '');
            $problem->closedBy   = zget($users, $problem->closedBy, '');
            $problem->onlineTime = $problem->solvedTime;
            //如果关联二线取二线时间，如果有待关闭状态取待关闭时间，没有待关闭取已关闭时间，否则为空
            $problem            = $this->problem->getSolvedTime($problem);
            $problem->solveDate = '';
            if ('delivery' == $problem->status || 'onlinesuccess' == $problem->status || 'onlinefailed' == $problem->status) {
                $dealDateObj = $this->loadModel('consumed')->getDealDate('problem', $problem->id);
                if ($dealDateObj) {
                    $problem->solveDate = date('Y-m-d', strtotime($dealDateObj->createdDate));
                }
            } elseif ('closed' == $problem->status) {
                $dealDateObj = $this->loadModel('problem')->getDate($problem->id);
                if ($dealDateObj) {
                    $problem->solveDate = $dealDateObj->lastDealDate;
                }
            }

            $problem->status = $problemLang->statusList[$problem->status];
            // 处理所属应用系统。
            if ($problem->app) {
                $as = [];
                foreach (explode(',', $problem->app) as $app) {
                    if (!$app) {
                        continue;
                    }
                    $as[] = zget($apps, $app);
                }
                $problem->app = implode(',', $as);
            }

            // 获取制版次数。不需要查询获取了，有字段累计增加。
            //$problem->buildTimes = $this->problem->getBuild($problem->id);

            // 获取关联的生产变更，数据修正，数据获取。
            $problem->relationModify = '';
            $problem->relationFix    = '';
            $problem->relationGain   = '';

            //反馈单状态
            $problem->ReviewStatus = zget($this->lang->problem->feedbackStatusList, $problem->ReviewStatus);

            //是否退回
            $problem->ifReturn = zget($this->lang->problem->ifReturnList, $problem->ifReturn);
            $problem->isChange = zget($this->lang->problem->isChangeList, $problem->isChange);
            //是否最终方案
            $problem->IfultimateSolution     = zget($this->lang->problem->ifultimateSolutionList, $problem->IfultimateSolution);
            $problem->isExtended             = zget($this->lang->problem->isExtendedList, $problem->isExtended);
            $problem->isBackExtended         = zget($this->lang->problem->isBackExtendedList, $problem->isBackExtended);
            $problem->ChangeIdRelated        = strip_tags($problem->ChangeIdRelated);
            $problem->EffectOfService        = strip_tags($problem->EffectOfService);
            $problem->IncidentIdRelated      = strip_tags($problem->IncidentIdRelated);
            $problem->DrillCausedBy          = strip_tags($problem->DrillCausedBy);
            $problem->Optimization           = strip_tags($problem->Optimization);
            $problem->Tier1Feedback          = strip_tags($problem->Tier1Feedback);
            $problem->solution               = strip_tags($problem->solution);
            $problem->ChangeSolvingTheIssue  = strip_tags($problem->ChangeSolvingTheIssue);
            $problem->ReasonOfIssueRejecting = strip_tags($problem->ReasonOfIssueRejecting);
            $problem->EditorImpactscope      = strip_tags($problem->EditorImpactscope);
            $problem->revisionRecord         = strip_tags($problem->revisionRecord);
            $problem->ProblemSource          = $problem->ProblemSource;
            $problem->sourece                = zget($this->lang->problem->sourceList, $problem->source, $problem->source);
            $problem->SolutionFeedback       = zget($this->lang->problem->solutionFeedbackList,$problem->SolutionFeedback,$problem->SolutionFeedback);
            //新增加字段-反馈单
            $problem->TimeOfOccurrence  = $problem->occurDate;
            $problem->problemFeedbackId = $problem->IssueId;
            $problem->ultimateSolution  = $problem->solution;


            if(isset($problemdelay[$problem->id]->delayStatus) && !empty($problemdelay[$problem->id]->delayStatus)){
                if(isset($problemdelay[$problem->id])){
                    $problem->monthreportdelayResolutionDate = date('Y-m-d', strtotime($problemdelay[$problem->id]->delayResolutionDate));
                }
                $problem->delayStatus                    = zget($this->lang->problem->delayStatusList, $problemdelay[$problem->id]->delayStatus);
            }
            $problem->completedPlan   = zget($this->lang->problem->completedPlanList,$problem->completedPlan,$problem->completedPlan);
            $problem->examinationResult  = zget($this->lang->problem->examinationResultList,$problem->examinationResult,$problem->examinationResult);
        }

        $problems    = [];
        foreach ($dataArr as $key=>$problem){
            $problems[$key] = new stdClass();
            foreach ($fields as $fkey=>$value){
                $problems[$key]->$fkey = isset($problem->$fkey) ? $problem->$fkey : '';
            }

        }

        return $problems;

    }
    public function handledemandData($dataArr,$fields){
//        $this->loadModel('secondmonthreport');


        $this->loadModel('demand');
        $this->loadModel('file');
        $this->loadModel('opinion');

        $demandLang   = $this->lang->demand;
        $demandConfig = $this->config->demand;

        $this->lang->demand->typeList = $this->lang->opinion->sourceModeListOld;
        $this->lang->demand->unionList = $this->lang->opinion->unionList;



        // Create field lists.
        foreach ($fields as $key => $fieldName) {
            $fieldName          = trim($fieldName);
            $fields[$fieldName] = $demandLang->{$fieldName} ?? $fieldName;
            unset($fields[$key]);
        }
        // Get demands.

        // Get users, products and executions.
        $users = $this->loadModel('user')->getPairs('noletter');
        $apps  = $this->loadModel('application')->getPairs();
        $depts = $this->getremovedeptbias();


        $dmap = $this->dao->select('account,dept')->from(TABLE_USER)->fetchAll('account');
        $this->loadModel('secondline');

        $productPairs     = $this->loadModel('product')->getSimplePairs();
        $productPlanPairs = $this->loadModel('productplan')->getSimplePairs();
        $plans            = $this->loadModel('project')->getPairs();

        // Obtain the receiver.

        // 获取所有需求条目数据。
        $allRequirement = $this->loadModel('requirement')->getPairs();
        $opinionList    = $this->loadModel('opinion')->getPairs();
        foreach ($dataArr as $demandKey => $demand) {

//            $demand->actualMethod = $demand->actualMethod;
            if(isset($demand->publishedTime) && $demand->publishedTime){
                $demand = $this->loadModel('demand')->getIsExceed($demand, $demand->publishedTime);
            }else{
                $demand = $this->loadModel('demand')->getIsExceed($demand, '');
            }

            // 处理需求来源方式。
            $demand->type = zget($demandLang->typeList, $demand->type, '');
            // 处理业务需求单位。
            $demand->union = zget($demandLang->unionList, $demand->union, '');

            $demand->monthreportrequirementmethod = zmget($this->lang->requirement->actualMethodList, $demand->actualMethod, '');

            if($demand->publishedTime){
                $demand->newPublishedTime = $demand->publishedTime;
            }else{
                $demand->newPublishedTime = '';
            }

            $demand->state            = $demandLang->stateList[$demand->state];
            $demand->createdDept      = $depts[$dmap[$demand->createdBy]->dept];
            $demand->acceptUser       = zget($users, $demand->acceptUser, $demand->acceptUser);
            $demand->acceptDept       = zget($depts, $demand->acceptDept, '');
            $demand->createdBy        = $users[$demand->createdBy];
            //迭代二十五 只有已录入状态有待处理人,已挂起
            $dealUser = '';
            if (in_array(($demand->status), ['wait', 'suspend'])) {
                $dealUser = zget($users, $demand->dealUser, '');
            }
            $demand->dealUser = $dealUser;
            $demand->editedBy = zget($users, $demand->editedBy, '');
            //20220311 新增
            $demand->systemverify   = zget($this->lang->demand->needOptions, $demand->systemverify, '');
            $demand->verifyperson   = zget($users, $demand->verifyperson, '');
            $demand->laboratorytest = zget($users, $demand->laboratorytest, '');

            $demand->closedBy = zget($users, $demand->closedBy, '');
            $demand->desc     = strip_tags($demand->desc);

            if ($demand->fixType) {
                $demand->fixType = $demandLang->fixTypeList[$demand->fixType];
            }

            $demand->solveDate = '';
            if ('delivery' == $demand->status || 'onlinesuccess' == $demand->status || 'onlinefailed' == $demand->status) {
                $dealDateObj = $this->loadModel('consumed')->getDealDate('demand', $demand->id);
                if ($dealDateObj) {
                    $demand->solveDate = date('Y-m-d', strtotime($dealDateObj->createdDate));
                }
            } elseif ('closed' == $demand->status) {
                $dealDateObj = $this->loadModel('demand')->getDate($demand->id);
                if ($dealDateObj) {
                    $demand->solveDate = $dealDateObj->lastDealDate;
                }
            }

            $demand->status = $demandLang->statusList[$demand->status];

            // 处理所属应用系统。
            if ($demand->project) {
                $as = [];
                foreach (explode(',', $demand->project) as $project) {
                    if (!$project) {
                        continue;
                    }
                    $as[] = zget($plans, $project);
                }
                $demand->project = implode(',', $as);
            }
            // 处理所属应用系统。
            if ($demand->app) {
                $as = [];
                foreach (explode(',', $demand->app) as $app) {
                    if (!$app) {
                        continue;
                    }
                    $as[] = zget($apps, $app);
                }
                $demand->app = implode(',', $as);
            }
            //接收时间取需求任务的接收时间
//            $demand->monthreportrcvDate = $requirementAcceptTime[$demand->requirementID];
            if ('0000-00-00' == $demand->end) {
                $demand->end = '';
            }

            // 获取需求意向。
            if (0 == $demand->opinionID) {
                $demand->opinionID = '';
            } else {
                $demand->opinionID = zget($opinionList, $demand->opinionID, '');
            }
            // 获取需求任务。
            if (0 == $demand->requirementID) {
                $demand->requirementID = '';
            } else {
                $demand->requirementID = zget($allRequirement, $demand->requirementID, '');
            }
            // 处理系统分类。
            if ($demand->isPayment) {
                $as = [];
                foreach (explode(',', $demand->isPayment) as $paymentID) {
                    if (!$paymentID) {
                        continue;
                    }
                    $as[] = zget($this->lang->application->isPaymentList, $paymentID, $paymentID);
                }
                $isPayment         = implode(',', $as);
                $demand->isPayment = $isPayment;
            }

            // 获取制版次数。
            $demand->buildTimes = $this->demand->getBuild($demand->id);

            // 获取所属产品。
            $demand->product = zget($productPairs, $demand->product, '');

            // 获取所属产品计划。
            $demand->productPlan = zget($productPlanPairs, $demand->productPlan, '');

            // 获取关联的生产变更，数据修正，数据获取。
            $demand->relationModify = '';
            $demand->relationFix    = '';
            $demand->relationGain   = '';

            $demand->delayStatus = zget($this->lang->demand->delayStatusList, $demand->delayStatus, '');
            if (!empty($demand->delayResolutionDate)) {
                $demand->monthreportdelayResolutionDate = date('Y-m-d', strtotime($demand->delayResolutionDate));
            } else {
                $demand->monthreportdelayResolutionDate = $demand->delayResolutionDate;
            }
            $demand->isExtended = zget($this->lang->demand->isExtendedList, $demand->isExtended);
        }

        $demands    = [];
        foreach ($dataArr as $key=>$demand){
            $demands[$key] = new stdClass();
            foreach ($fields as $fkey=>$value){
                $demands[$key]->$fkey = isset($demand->$fkey) ? $demand->$fkey : '';
            }

        }
        return $demands;
    }
    public function handlemodifywholeData($dataArr,$fields){


        foreach ($fields as $key => $fieldName) {
            $fieldName          = trim($fieldName);
            $fields[$fieldName] = $this->lang->secondmonthreport->modifyphoto->{$fieldName} ?? $fieldName;
            unset($fields[$key]);
        }

        // Get users, products and executions.
        $users = $this->loadModel('user')->getPairs('noletter');
        $apps  = $this->loadModel('application')->getPairs();
        $depts = $this->getremovedeptbias();
        $this->loadModel('modify');
        $this->loadModel('modifycncc');
        if(isset($dataArr['modify'])){
            foreach ($dataArr['modify'] as $modify) {
                $modify->createdDept = zget($depts,$modify->createdDept);
                $modify->createdBy = zget($users,$modify->createdBy);
                $modify->mode = zget($this->lang->modify->modeList,$modify->mode);
                $modify->level = zget($this->lang->modify->levelList,$modify->level);
                $modify->app = zmget($apps,$modify->app);
                $modify->status = zget($this->lang->modify->statusList,$modify->status);
                $modify->type = zget($this->lang->modify->typeList,$modify->type);
                $modify->exybtjsource = zget($this->lang->secondmonthreport->modifyexybtjsourceList,$modify->exybtjsource);
            }
        }
        if(isset($dataArr['modifycncc'])){
            foreach ($dataArr['modifycncc'] as $modifycc) {
                $modifycc->createdDept = zget($depts,$modifycc->createdDept);
                $modifycc->createdBy = zget($users,$modifycc->createdBy);
                $modifycc->mode = zget($this->lang->modifycncc->modeList,$modifycc->mode);
                $modifycc->level = zget($this->lang->modifycncc->levelList,$modifycc->level);
                $modifycc->app = zmget($apps,$modifycc->app);
                $modifycc->status = zget($this->lang->modifycncc->statusList,$modifycc->status);
                $modifycc->type = zget($this->lang->modifycncc->typeList,$modifycc->type);
                $modifycc->exybtjsource = zget($this->lang->secondmonthreport->modifyexybtjsourceList,$modifycc->exybtjsource);
            }
        }
        if(isset($dataArr['credit'])){
            $this->app->loadLang('credit');
            foreach ($dataArr['credit'] as $credit) {
                $credit->createdDept = zget($depts,$credit->createdDept);
                $credit->createdBy = zget($users,$credit->createdBy);
                $credit->mode = zget($this->lang->modifycncc->modeList,$credit->mode);
                $credit->level = zget($this->lang->credit->levelList,$credit->level);
                $credit->app = zmget($apps,$credit->appIds);
                $credit->status = zget($this->lang->credit->statusList,$credit->status);
                $credit->type = zget($this->lang->credit->emergencyTypeList,$credit->type);
                $credit->exybtjsource = zget($this->lang->secondmonthreport->modifyexybtjsourceList,$credit->exybtjsource);
            }
        }


//        $modifyccList = array_merge($modifyccList,$modifyList);

        $modifywholes    = [];
        $i = 0;
        foreach ($dataArr as $key=>$modifywhole){
            foreach ($modifywhole as $key2=>$modify){
                $modifywholes[$i] = new stdClass();
                foreach ($fields as $fkey=>$value){

                    $modifywholes[$i]->$fkey = isset($modify->$fkey) ? $modify->$fkey : '';

                }
                $i++;
            }


        }
        return $modifywholes;

    }
    public function handleworkloadData($dataArr,$fields){

        $this->loadModel('file');


        foreach ($fields as $key => $fieldName) {
            $fieldName          = trim($fieldName);
            $fields[$fieldName] = $this->lang->secondmonthreport->workloadphoto->{$fieldName} ?? $fieldName;
            unset($fields[$key]);
        }

        // Get users, products and executions.
        $users = $this->loadModel('user')->getPairs('noletter');

        $depts = $this->getremovedeptbias();

        $pattern = '/CFIT-(?:Q|T|D|WD)-\d{8}-\d{2,}/';

        foreach ($dataArr as $effort) {
            $effort->deptID = zget($depts,$effort->deptID);
            $effort->account = zget($users,$effort->account);
            $effort->abstract = '';
            if(preg_match($pattern,$effort->name,$matches)){
                if(isset($matches[0]) && $matches[0]){
                    if(strpos($matches[0],'CFIT-Q-') !== false ){
                        //问题池
                        $effort->abstract = $this->dao->select("abstract")->from(TABLE_PROBLEM)->where('code')->eq($matches[0])->fetch('abstract');
                    }else if(strpos($matches[0],'CFIT-D-') !== false ){
                        //需求池外部
                        $effort->abstract = $this->dao->select("`desc` as abstract")->from(TABLE_DEMAND)->where('code')->eq($matches[0])->fetch('abstract');
                        $effort->abstract = strip_tags($effort->abstract);
                    }else if(strpos($matches[0],'CFIT-WD-') !== false ){
                        //需求池内部 去除 需求池内部数据
                        unset($dataArr[$key]);

                    }else if(strpos($matches[0],'CFIT-T-') !== false ){
                        //工单池
                        $effort->abstract = $this->dao->select("summary as abstract")->from(TABLE_SECONDORDER)->where('code')->eq($matches[0])->fetch('abstract');

                    }

                }

            }

        }

        $workloads    = [];
        foreach ($dataArr as $key=>$workload){
            $workloads[$key] = new stdClass();
            foreach ($fields as $fkey=>$value){
                $workloads[$key]->$fkey = isset($workload->$fkey) ? $workload->$fkey : '';
            }

        }
        return $workloads;
    }
    public function handlesupportData($dataArr,$fields){

        foreach ($fields as $key => $fieldName) {
            $fieldName          = trim($fieldName);
            $fields[$fieldName] = $this->lang->secondmonthreport->supportphoto->{$fieldName} ?? $fieldName;
            unset($fields[$key]);
        }

        // Get users, products and executions.
        $users = $this->loadModel('user')->getPairs('noletter');
        $apps  = $this->loadModel('application')->getPairs();
        $depts = $this->getremovedeptbias();

        $areaFieldInfo = $this->dao->select("`options`")->from(TABLE_WORKFLOWFIELD)->where('module')->eq('support')->andWhere('field')->eq('area')->fetch();
        $areaList = json_decode($areaFieldInfo->options,true);

        $stypeFieldInfo = $this->dao->select("`options`")->from(TABLE_WORKFLOWFIELD)->where('module')->eq('support')->andWhere('field')->eq('stype')->fetch();
        $stypeList = json_decode($stypeFieldInfo->options,true);

        foreach ($dataArr as $support) {
            $support->dept = zget($depts,$support->dept);
            $support->pnams = zmget($users,$support->pnams);
            $support->app = zmget($apps,$support->appIds);
            $support->area = zget($areaList,$support->area);
            $support->stype = zget($stypeList,$support->stype);
        }

        $supports    = [];
        foreach ($dataArr as $key=>$support){
            $supports[$key] = new stdClass();
            foreach ($fields as $fkey=>$value){
                $supports[$key]->$fkey = isset($support->$fkey) ? $support->$fkey : '';
            }

        }
        return $supports;
    }
    public function handlesecondorderData($dataArr,$fields){

        $this->loadModel('file');

        // Create field lists.

        foreach ($fields as $key => $fieldName) {
            $fieldName          = trim($fieldName);
            $fields[$fieldName] = $this->lang->secondmonthreport->secondorderphoto->{$fieldName} ?? $fieldName;
            unset($fields[$key]);
        }


        // Get users, products and executions.
        $users = $this->loadModel('user')->getPairs('noletter');
        $apps  = $this->loadModel('application')->getPairs();
        $depts = $this->getremovedeptbias();
        $this->loadModel('secondorder');

        foreach ($dataArr as $secondorder) {
            $subtype = $this->loadModel('secondorder')->getChildTypeList($secondorder->type);
            $secondorder->acceptDept = zget($depts,$secondorder->acceptDept,'');
            $secondorder->acceptUser = zget($users,$secondorder->acceptUser);
            $secondorder->app = zget($apps,$secondorder->app);
            $secondorder->status = zget($this->lang->secondorder->statusList,$secondorder->status);
            $secondorder->type = zget($this->lang->secondorder->typeList,$secondorder->type);
            $secondorder->ifAccept = zget($this->lang->secondorder->ifAcceptList,$secondorder->ifAccept);
            $secondorder->subtype = zget($subtype,$secondorder->subtype);
            $secondorder->createdUser = zget($users,$secondorder->createdBy);
        }

        $secondorders    = [];
        foreach ($dataArr as $key=>$secondorder){
            $secondorders[$key] = new stdClass();
            foreach ($fields as $fkey=>$value){
                $secondorders[$key]->$fkey = isset($secondorder->$fkey) ? $secondorder->$fkey : '';
            }

        }
        return $secondorders;
    }
    public function handlerequirementData($dataArr,$fields)
    {
        $this->loadModel('requirement');
        $this->loadModel('file');
        $requirementLang   = $this->lang->requirement;
        $requirementConfig = $this->config->requirement;
        $this->app->loadLang('opinioninside');

        foreach ($fields as $key => $fieldName) {
            $fieldName          = trim($fieldName);
            $fields[$fieldName] = $requirementLang->{$fieldName} ?? $fieldName;
            unset($fields[$key]);
        }

        // Get users, products and executions.
        $users = $this->loadModel('user')->getPairs('noletter');
        $this->app->loadLang('opinion');
        $apps = $this->loadModel('application')->getPairs();

        $depts = $this->getremovedeptbias();

        foreach ($dataArr as $requirement) {

            $demandsOther     = $this->requirement->getDemandByRequirement($requirement->id);
            $ownProjectArr    = !empty($requirement->project) ? explode(',', $requirement->project) : [];
            $demandProjectArr = array_column($demandsOther, 'project');

            $acceptUserArr      = array_filter(array_unique(array_column($demandsOther, 'acceptUser')));
            $acceptDeptArr      = array_filter(array_unique(array_column($demandsOther, 'acceptDept')));
            $requirement->owner = zmget($users, implode(',', $acceptUserArr));
            $requirement->dept  = zmget($depts, implode(',', $acceptDeptArr));

            //迭代二十九 【反馈人员所属部门】（若反馈人为空，则取反馈单待处理人所属部门，若反馈单待处理人为空则反馈人员所属部门为空）
            if (!empty($requirement->feedbackBy)) {
                $feedbackDepts             = $this->loadModel('user')->getUserDeptIds($requirement->feedbackBy);
                $requirement->feedbackBy   = zget($users, $requirement->feedbackBy, '');
                $requirement->feedbackDept = zmget($depts, implode(',', $feedbackDepts));
            }

            //待反馈状态取反馈单待处理人所属部门
            if ($requirement->feedbackStatus == 'tofeedback') {
                $userDept                  = $this->loadModel('user')->getByAccount($requirement->feedbackDealUser);
                $requirement->feedbackDept = isset($userDept->dept) ? zget($depts, $userDept->dept, '') : '';
//                $requirement->feedbackBy = zget($users, $requirement->feedbackDealUser, '');
            }


            //外部同步需求任务接收时间取创建时间，内部需求任务取需求意向接收时间
            if ('guestcn' == $requirement->createdBy) {
                $requirement->acceptTime       = $requirement->createdDate;
                $requirement->newPublishedTime = '0000-00-00 00:00:00' != $requirement->feekBackStartTime ? $requirement->feekBackStartTime : '';
            } else {

                $requirement->newPublishedTime = $requirement->newPublishedTime != '0000-00-00 00:00:00' ? $requirement->newPublishedTime : '';
            }

            $requirement->ID = $requirement->id;

            $requirement->status = zget($requirementLang->statusList, $requirement->status, '');

            $requirement->monthreportmethod = zmget($requirementLang->actualMethodList, $requirement->actualMethod, '');


            $appList    = explode(',', $requirement->app);
            $appChnList = [];
            foreach ($appList as $app) {
                $appChn       = zget($apps, $app, '');
                $appChnList[] = $appChn;
            }
            $requirement->app = implode(',', $appChnList);


            $requirement->desc = strip_tags($requirement->desc);

            $requirement->monthreportcreatedBy = zget($users, $requirement->createdBy, '');
            $requirement->editedBy             = zget($users, $requirement->editedBy, '');
            $requirement->closedBy             = zget($users, $requirement->closedBy, '');
            $requirement->activatedBy          = zget($users, $requirement->activatedBy, '');
            $requirement->ignoredBy            = zget($users, $requirement->ignoredBy, '');
            $requirement->recoveryedBy         = zget($users, $requirement->recoveryedBy, '');

            $requirement->feedbackStatus = zget($requirementLang->feedbackStatusList, $requirement->feedbackStatus, '');

            //100 是一个标识，标记默认值 为否
            if (100 == $requirement->ifOverDate) {
                $requirement->ifOverTime = '否';
            } else {
                $requirement->ifOverTime = zget($this->lang->requirement->ifOverDateList, $requirement->ifOverDate, '');
            }
            //内部反馈开始时间
            $requirement->monthreportinsideStart = '0000-00-00 00:00:00' != $requirement->feekBackStartTime ? $requirement->feekBackStartTime : '';
            //部门审核通过时间（内部反馈结束时间）
            $requirement->monthreportinsideEnd = '0000-00-00 00:00:00' != $requirement->deptPassTime ? $requirement->deptPassTime : '';
            //内部反馈期限
            $requirement->insideFeedback     = '0000-00-00 00:00:00' != $requirement->feekBackEndTimeInside ? $requirement->feekBackEndTimeInside : '';
            $requirement->onlineTimeByDemand = '0000-00-00 00:00:00' != $requirement->onlineTimeByDemand ? $requirement->onlineTimeByDemand : '';

            //100 是一个标识，标记默认值 为否
            if (100 == $requirement->ifOverTimeOutSide) {
                $requirement->monthreportifOverTimeOutSide = '否';
            } else {
                $requirement->monthreportifOverTimeOutSide = zget($requirementLang->ifOverDateList, $requirement->ifOverTimeOutSide, '');
            }
            //外部反馈开始时间
            $requirement->outsideStart = '0000-00-00 00:00:00' != $requirement->feekBackStartTimeOutside ? $requirement->feekBackStartTimeOutside : '';
            $requirement->monthreportoutsideStart = '0000-00-00 00:00:00' != $requirement->feekBackStartTimeOutside ? $requirement->feekBackStartTimeOutside : '';
            //同步清总成功时间（外部反馈结束时间）
            $requirement->monthreportoutsideEnd = '0000-00-00 00:00:00' != $requirement->innovationPassTime ? $requirement->innovationPassTime : '';
            //外部反馈期限
            $requirement->outsideFeedback = $requirement->feekBackEndTimeOutSide;

            $requirement->ifOutUpdate    = zget($requirementLang->ifOutUpdateList, $requirement->ifOutUpdate);
            $requirement->feedbackOver   = zget($this->lang->requirement->feedbackOverList, $requirement->feedbackOver);
            $requirement->lastChangeTime = '0000-00-00 00:00:00' != $requirement->lastChangeTime ? $requirement->lastChangeTime : '';
        }
        $requirements    = [];
        foreach ($dataArr as $key=>$requirement){
            $requirements[$key] = new stdClass();
            foreach ($fields as $fkey=>$value){
                $requirements[$key]->$fkey = isset($requirement->$fkey) ? $requirement->$fkey : '';
            }

        }
        return $requirements;
    }
    public function getWholeReportList($type, $orderBy, $pager)
    {
        return $this->dao->select('*')->from(TABLE_WHOLE_REPORT)
            ->where('type')->eq($type)
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');
    }

    public function getWholeReportByID($ID)
    {
        return $this->dao->select('*')->from(TABLE_WHOLE_REPORT)
            ->where('id')->eq($ID)

            ->fetch();
    }

    public function getDetailReport($wholeID, $deptID = 0)
    {
        $detailReports = $this->dao->select('*')->from(TABLE_DETAIL_REPORT)
            ->where('wholeID')->eq($wholeID)
            ->beginIF($deptID)->andWhere('deptID')->eq($deptID)->fi()
            ->fetchAll();
        if ($detailReports) {
            foreach ($detailReports as $detailReport) {
                $detailReport->detail = json_decode($detailReport->detail);
            }
        }

        return $detailReports;
    }

    public function excelToRedis($wholeID,$getDeptID=0){
        $wholereport  = $this->getWholeReportByID($wholeID);
        $this->loadModel('file');
        $redismanage = $this->app->loadClass('redismanage');

        $baserootdir = dirname(__FILE__,3);
        $filepath = $baserootdir.'/www'.$wholereport->fileUrl;
        $fileisexist = file_exists($filepath);
        if(!$fileisexist){
            echo "快照文件不存在无法查看";
            exit();
        }

        $fileReport = $this->file->getRowsFromExcel($filepath);

        $excelHeader = array_shift($fileReport);
        //基础快照字段
        $exportBasicFieldsArr = explode(',',$wholereport->exportBasicFields);
        $exportFieldsArr = explode(',',$wholereport->exportFields);


        foreach($fileReport as $fkey=>$freport){
            foreach($freport as $fk=>$fval){
                if(in_array($exportBasicFieldsArr[$fk],$exportFieldsArr)){
                    $fileReport[$fkey][$exportBasicFieldsArr[$fk]] = $fval;
                }

                unset($fileReport[$fkey][$fk]);
            }
        }

        $columnArr = json_decode($wholereport->useIDArr,true);

        $tempReport = [];
        foreach($columnArr as $cdeptID=>$coArr){
            foreach($coArr as $ckey=>$IDS){
                $jishu = 0;
                foreach($fileReport as $fkey=>$freport){
                    if(in_array($freport['id'],$IDS)){
                        $tempReport[$cdeptID][$ckey][$jishu] = $freport;
                        $jishu++;
                    }
                }
            }

        }

//        $wholeID_$dept_$column = $fileReport[deptID][column];//需要json_encode转成字符串

        $deptIDArr = [];
        $getData = [];
        //先设置 部门key，解决key过期时间不同导致的数据异常
        foreach($tempReport as $deptID=>$columnArr){
            $deptIDArr[] = $deptID;
        }
        //存储所有有数据的部门， 下钻查看点击底部合计时 使用
        $alldeptskey = $this->getRedisAlldeptKey($wholeID,'alldepts');
        $redismanage->conn->set($alldeptskey,implode(',',$deptIDArr),$this->lang->secondmonthreport->redisexpiresecond);
//存入redis.
        foreach($tempReport as $deptID=>$columnArr){

            $rediskey = $this->getRedisKey($wholeID,$deptID);
//            $redisArr[$rediskey] = $columnArr;
            $redismanage->conn->set($rediskey,json_encode($columnArr,JSON_UNESCAPED_UNICODE),$this->lang->secondmonthreport->redisexpiresecond);
            /*foreach($columnArr as $colKey=>$colValue){
//                $redis->set($rediskey,json_encode($colValue));
            }*/

        }

        if($getDeptID){
            $getData = $tempReport[$getDeptID];
        }
        return json_encode($getData,JSON_UNESCAPED_UNICODE);

    }
    public function multdataexcelToRedis($wholeID,$getDeptID=0){

        $wholereport  = $this->getWholeReportByID($wholeID);
        $this->loadModel('file');
        $redismanage = $this->app->loadClass('redismanage');

        $baserootdir = dirname(__FILE__,3);
        $filepath = $baserootdir.'/www'.$wholereport->fileUrl;
        $fileisexist = file_exists($filepath);
        if(!$fileisexist){
            echo "快照文件不存在无法查看";
            exit();
        }

        $fileReport = $this->file->getRowsFromExcel($filepath);

        $excelHeader = array_shift($fileReport);
        //基础快照字段
        $exportBasicFieldsArr = explode(',',$wholereport->exportBasicFields);
        $exportFieldsArr = explode(',',$wholereport->exportFields);


        foreach($fileReport as $fkey=>$freport){
            foreach($freport as $fk=>$fval){
                if(in_array($exportBasicFieldsArr[$fk],$exportFieldsArr)){
                    $fileReport[$fkey][$exportBasicFieldsArr[$fk]] = $fval;
                }

                unset($fileReport[$fkey][$fk]);
            }
        }

        $columnArr = json_decode($wholereport->useIDArr,true);
//        a($columnArr);
//        exit();
        $modifyexybtjsourceList = array_flip($this->lang->secondmonthreport->modifyexybtjsourceList);

        $tempReport = [];
        foreach($columnArr as $multkey=>$multcoArr){

            foreach($multcoArr as $cdeptID=>$coArr){

                foreach($coArr as $ckey=>$IDS){

                    foreach($fileReport as $fkey=>$freport){
                        //如果这条数据的ID 在 id列表中  并且本条数据的源和记录的源 一致
                        if(in_array($freport['id'],$IDS) && ($freport['exybtjsource'] == $multkey || $modifyexybtjsourceList[$freport['exybtjsource']] == $multkey)){
                            $tempReport[$cdeptID][$ckey][] = $freport;

                        }
                    }

                }

            }
        }


//        $wholeID_$dept_$column = $fileReport[deptID][column];//需要json_encode转成字符串

        $deptIDArr = [];
        $getData = [];
        //先设置 部门key，解决key过期时间不同导致的数据异常
        foreach($tempReport as $deptID=>$columnArr){
//            foreach ($multcolumnArr as $deptID=>$columnArr){
                $deptIDArr[] = $deptID;
//            }

        }
//        a($deptIDArr);
//        a($tempReport);
//        exit();

        //存储所有有数据的部门， 下钻查看点击底部合计时 使用
        $alldeptskey = $this->getRedisAlldeptKey($wholeID,'alldepts');
        $redismanage->conn->set($alldeptskey,implode(',',$deptIDArr),$this->lang->secondmonthreport->redisexpiresecond);
//存入redis.
        foreach($tempReport as $deptID=>$columnArr){


            $rediskey = $this->getRedisKey($wholeID,$deptID);
//            $redisArr[$rediskey] = $columnArr;
            $redismanage->conn->set($rediskey,json_encode($columnArr,JSON_UNESCAPED_UNICODE),$this->lang->secondmonthreport->redisexpiresecond);
            /*foreach($columnArr as $colKey=>$colValue){
//                $redis->set($rediskey,json_encode($colValue));
            }*/

        }

        if($getDeptID){
            $getData = $tempReport[$getDeptID];
        }
        return json_encode($getData,JSON_UNESCAPED_UNICODE);

    }
    public function historyDataShow($wholeID,$deptID,$columnKey){
        if($deptID == '_1'){
            $deptID = -1;
        }
        $wholereport  = $this->getWholeReportByID($wholeID);

        $redismanage = $this->app->loadClass('redismanage');
        $dataArr = [
            'wholeReport'=>$wholereport
        ];
        $dataArr['multflag'] = $this->lang->secondmonthreport->ismultdatasource[$wholereport->type];
        if($deptID){
            $rediskey = $this->getRedisKey($wholeID,$deptID);

            $resData = $redismanage->conn->get($rediskey);
            if(!$resData){
                if($dataArr['multflag']){
                    $resData = $this->multdataexcelToRedis($wholeID,$deptID);
                }else{
                    $resData = $this->excelToRedis($wholeID,$deptID);
                }

            }
            $resDataArr = json_decode($resData);

            if(isset($resDataArr->$columnKey)){
                $dataArr['historyData'] = $resDataArr->$columnKey;

            }else{
                $dataArr['historyData'] =[];

            }
            return $dataArr;
        }else{
            $alldeptskey = $this->getRedisAlldeptKey($wholeID,'alldepts');
            $resDeptData = $redismanage->conn->get($alldeptskey);

            $dataArr['historyData'] = [];
            if(!$resDeptData){
                if($dataArr['multflag']){
                    $resData = $this->multdataexcelToRedis($wholeID,$deptID);
                }else{
                    $resData = $this->excelToRedis($wholeID,$deptID);
                }
            }
            $resDeptData = $redismanage->conn->get($alldeptskey);

            if($resDeptData){
                $resDeptDataArr = explode(",",$resDeptData);
                foreach ($resDeptDataArr as $dept){
                    $rediskey = $this->getRedisKey($wholeID,$dept);
                    $resData = $redismanage->conn->get($rediskey);
                    if($resData){
                        $resDataArr = json_decode($resData);
                        if(isset($resDataArr->$columnKey)){
                            $dataArr['historyData'] = array_merge($dataArr['historyData'],$resDataArr->$columnKey);

                        }
                    }

                }
            }

            return $dataArr;

        }

    }

    /**获取 历史快照 redis缓存 key
     * @param $wholdID 快照id
     * @param $deptID 部门id
     * @return string
     */
    public function getRedisKey($wholdID,$deptID){
        return $wholdID.'_'.$deptID;
    }

    /**
     * @param $wholdID 快照id
     * @param $keystr 自定义key   'alldepts'获取所有有数据的部门，用于合计的下钻查看
     * @return string
     */
    public function getRedisAlldeptKey($wholdID,$keystr){
        return $wholdID.'_'.$keystr;
    }

    /**获取 下钻查看 展示的表头 使用的 语言项
     * @param $staticType
     * @return mixed|string
     */
    public function getColumnTopLang($staticType){

        if(isset($this->lang->secondmonthreport->colmumtopUseLang[$staticType])){
            $res = $this->lang->secondmonthreport->colmumtopUseLang[$staticType];

            if($res['isload'] == 1){
                $loadmodel = $res['uselang'];
                $this->loadModel($loadmodel);
                return $this->lang->$loadmodel;
            }else{
                return $res['uselang'];
            }
        }else{
            return '';
        }

    }

    /** 下钻查看数据列表  跳转详细数据详情时  如果目标链接有#号，则使用此方法进行逻辑处理，建议通过navgroup实现，而不是此方法。
     * @param $staticType
     * @return string
     */
    public function getLinkViewType($staticType){
        if($staticType == 'support'){
            return '';
        }else{
            return '';
        }
    }
    /**去掉部门开头的 /
     * @return mixed
     */
    public function getremovedeptbias(){
        $depts = $this->loadModel('dept')->getTopPairs();
        foreach ($depts as $key=>$dept){
            $depts[$key] = ltrim($dept,'/');
        }
        $depts[-1] = '空';
        return $depts;
    }
    public function getExportFileName($searchtype,$staticType,$columnKey,$startday,$endday){
        $filename = '';
        if($searchtype){
            $filename .= $this->lang->secondmonthreport->searchsearchtypeList[$searchtype].'_';
        }
        if($staticType){
            $filename .= $this->lang->secondmonthreport->$staticType.'_';
        }

        if($columnKey){
            $filename .= $this->lang->secondmonthreport->$columnKey.'_';
        }

        if($startday){
//            $startday = str_replace('-','_',$startday);
            $filename .= $startday.'_';
        }
        if($endday){
//            $endday = str_replace('-','_',$endday);
            $filename .= $endday.'_';
        }
        return trim($filename,'_');
    }

    public function getDetailReportByColum($wholeID, $deptID = 0,$keycolum='')
    {
        $detailReports = $this->dao->select('*')->from(TABLE_DETAIL_REPORT)
            ->where('wholeID')->eq($wholeID)
            ->beginIF($deptID)->andWhere('deptID')->eq($deptID)->fi()
            ->fetchAll($keycolum);

        if ($detailReports) {
            foreach ($detailReports as $detailReport) {
                $detailReport->detail = json_decode($detailReport->detail);
            }
        }

        return $detailReports;
    }

    /**
     * 获取超期日期
     * @param $date 2023-01-01 or 2023-01-01 01:01:01
     * @param $monthNum 向前计算月数
     * @return false|string
     */
    public function getOverDate($date, $monthNum)
    {
        $monthNum      = (int)$monthNum;
        $dateTimestamp = strtotime($date);
        $startMonth    = (int)date('m', $dateTimestamp);
        $startYear     = (int)date('Y', $dateTimestamp);
        $startDay      = (int)date('j', $dateTimestamp);
        $addMonth      = $startMonth + $monthNum;

        $resultMonth = ($addMonth % 12);
        $resultYear  = ((int)($addMonth / 12)) + $startYear;
        if (0 == $resultMonth) {
            $resultMonth = 12;
            $resultYear  = $resultYear - 1;
        }

        $result          = $resultYear . '-' . $resultMonth;
        $resultTimestamp = strtotime($result);
        $endMonthDays    = date('t', $resultTimestamp);
        if ($startDay > $endMonthDays) {
            $resultDay = $endMonthDays;
        } else {
            $resultDay = $startDay;
        }

        //最终结果。
        return date('Y-m-d', strtotime($result . '-' . $resultDay));
    }

    /**
     * 获取统计的起始和结束时间
     * @param $endtime dtype=1 时非必填 若 dtype=2 时必填，若传则以传入日期进行推算
     * @param $starttime dtype=1 时非必填， 若 dtype=2 时必填
     * @param $dtype 非必填，1:默认按月统计，2：指定开始和结束日期
     * @return array
     */
    public function getTimeFrame($endtime = '',$starttime = '',$dtype=1)
    {
        //如果指定了日期 则使用指定日期推算
        if($dtype == 2){
            if(!$endtime || !$starttime){
                return false;
            }

            $startday = date('Y-m-d', strtotime($starttime));

            $endDay       = date('Y-m-d', strtotime($endtime));
            return ['startdate' => $starttime.' 00:00:00', 'enddate' => $endtime. ' 23:59:59','startday'=>$startday,'endday'=>$endDay,'dtype'=>$dtype];
        }else{
            if ($endtime) {
                $statisticsDate = $endtime;
            } else {
                $statisticsDate = date('Y-m-d');
            }

            $statisticsTimeStamp = strtotime($statisticsDate);

            $year  = (int)date('Y', $statisticsTimeStamp);
            $month = (int)date('m', $statisticsTimeStamp);

            if (1 == $month) {
                $resultyear  = $year - 1;
                $resultmonth = 12;
            } else {
                $resultyear  = $year;
                $resultmonth = $month - 1;
            }

            $startDate = date('Y-m-d H:i:s', strtotime($resultyear . '-01-01 00:00:00'));
            $startday = date('Y-m-d', strtotime($startDate));

            $tempTimeStamp = strtotime($resultyear . '-' . $resultmonth);
            $lastday       = date('t', $tempTimeStamp);
            $endDate       = date('Y-m-d H:i:s', strtotime($resultyear . '-' . $resultmonth . '-' . $lastday . ' 23:59:59'));
            $endDay       = date('Y-m-d', strtotime($endDate));

            return ['startdate' => $startDate, 'enddate' => $endDate,'startday'=>$startday,'endday'=>$endDay,'dtype'=>$dtype];
        }

    }



    public function demandHistoryWhole($starttime,$endtime, $time,$dateFram,$formType)
    {

        $sourceResultData = $this->getDemandDataList($starttime,$endtime,0,$formType,1);
        $statucResult = $this->demandwholeDemandMonthStatic($sourceResultData,0);
        $saveResult = $this->demanddemandwholeSave($statucResult['deptcolumids'],$statucResult['staticdata'],$formType,$time,$dateFram);
        return $statucResult['useids'];
    }
    public function demandHistoryUnrealized($starttime,$endtime, $time,$dateFram,$formType)
    {

        $sourceResultData = $this->getDemandDataList($starttime,$endtime,0,$formType,1);
        $statucResult = $this->demandunrealizedStatic($sourceResultData,0);

        $saveResult = $this->demandunrealizedSave($statucResult['deptcolumids'],$statucResult['staticdata'],$formType,$time,$dateFram);
        return $statucResult['useids'];
    }

    public function demandHistoryrealized($starttime,$endtime, $time,$dateFram,$formType)
    {

        $sourceResultData = $this->getDemandDataList($starttime,$endtime,0,$formType,0);
        $statucResult = $this->demandrealizedMonthStatic($sourceResultData,0);

        $saveResult = $this->demandrealizedMonthSave($statucResult['deptcolumids'],$statucResult['staticdata'],$formType,$time,$dateFram);
        return $statucResult['useids'];
    }
    public function requirementHistoryinside($starttime,$endtime, $time,$dateFram,$formType)
    {

        $sourceResultData = $this->getRequirementDataList($starttime,$endtime,0,$formType,0);
        $statucResult = $this->requirementInsideStatic($sourceResultData,0);

        $saveResult = $this->requirementInsideSave($statucResult['deptcolumids'],$statucResult['staticdata'],$formType,$time,$dateFram);
        return $statucResult['useids'];
    }

    public function requirementHistoryoutside($starttime,$endtime, $time,$dateFram,$formType)
    {

        $sourceResultData = $this->getRequirementDataList($starttime,$endtime,0,$formType,0);
        $statucResult = $this->requirementOutsideStatic($sourceResultData,0);

        $saveResult = $this->requirementOutsideSave($statucResult['deptcolumids'],$statucResult['staticdata'],$formType,$time,$dateFram);
        return $statucResult['useids'];
    }

    public function secondorderHistoryclass($starttime,$endtime, $time,$dateFram,$formType)
    {

        $sourceResultData = $this->getSecondorderDataList($starttime,$endtime,0,$formType,1);

        $statucResult = $this->secondorderclassStatic($sourceResultData,0);

        $saveResult = $this->secondorderclassSave($statucResult['deptcolumids'],$statucResult['staticdata'],$formType,$time,$dateFram);
        return $statucResult['useids'];
    }
    public function secondorderHistoryaccept($starttime,$endtime, $time,$dateFram,$formType)
    {

        $sourceResultData = $this->getSecondorderDataList($starttime,$endtime,0,$formType,0);

        $statucResult = $this->secondorderacceptStatic($sourceResultData,0);

        $saveResult = $this->secondorderacceptSave($statucResult['deptcolumids'],$statucResult['staticdata'],$formType,$time,$dateFram);
        return $statucResult['useids'];
    }
    public function supportHistorysupport($starttime,$endtime, $time,$dateFram,$formType)
    {

        $sourceResultData = $this->getSupportDataList($starttime,$endtime,0,$formType,0);

        $statucResult = $this->supportStatic($sourceResultData,0);

        $saveResult = $this->supportSave($statucResult['deptcolumids'],$statucResult['staticdata'],$formType,$time,$dateFram);
        return $statucResult['useids'];
    }
    public function workloadHistoryworkload($starttime,$endtime, $time,$dateFram,$formType)
    {

        $sourceResultData = $this->getWorkloadDataList($starttime,$endtime,0,$formType,0);

        $statucResult = $this->workloadStatic($sourceResultData,0);

        $saveResult = $this->workloadSave($statucResult['deptcolumids'],$statucResult['staticdata'],$formType,$time,$dateFram);
        return $statucResult['useids'];
    }

    public function modifyHistorywhole($starttime,$endtime, $time,$dateFram,$formType)
    {

        $sourceResultData = [];

        $sourceResultData['modify'] = $this->getModifyDataList($starttime,$endtime,0,$formType,0);

        $sourceResultData['modifycncc'] = $this->getModifycnccDataList($starttime,$endtime,0,$formType,0);

        $sourceResultData['credit'] = $this->getCreditDataList($starttime,$endtime,0,$formType,0);


        $statucResult = $this->modifywholeStatic($sourceResultData,0);

        $saveResult = $this->modifywholeSave($statucResult['deptcolumids'],$statucResult['staticdata'],$formType,$time,$dateFram);
        return $statucResult['useids'];
    }
    public function modifyHistorynormal($starttime,$endtime, $time,$dateFram,$formType)
    {

        $sourceResultData = $this->loadModel('secondmonthreport')->getModifyFinishData($starttime, $endtime, 0);

//        $sourceResultData['modify'] = $this->getModifyDataList($starttime,$endtime,0,$formType,0);
//
//        $sourceResultData['modifycncc'] = $this->getModifycnccDataList($starttime,$endtime,0,$formType,0);
//
//        $sourceResultData['credit'] = $this->getCreditDataList($starttime,$endtime,0,$formType,0);

        $statucResult = $this->modifyabnormalStatic($sourceResultData,0);

        $saveResult = $this->modifyabnormalSave($statucResult['deptcolumids'],$statucResult['staticdata'],$formType,$time,$dateFram);
        return $statucResult['useids'];
    }
    /**
     * 问题整体情况统计表 历史统计数据生成
     * @param $starttime
     * @param $endtime
     * @param $time
     * @param $dateFram
     * @param $formType
     * @return mixed
     */
    public function problemHistoryOverall($starttime,$endtime, $time,$dateFram,$formType)
    {

        $sourceResultData = $this->getProblemDataList($starttime,$endtime,0,$formType,1);
        $statucResult = $this->problemproblemOverallStatic($sourceResultData,0);
        $this->problemproblemOverallSave($statucResult['deptcolumids'],$statucResult['staticdata'],$formType,$time,$dateFram);
        return $statucResult['useids'];

    }
    public function problemHistoryWaitSolve($starttime,$endtime, $time,$dateFram,$formType)
    {
        $sourceResultData = $this->getProblemDataList($starttime,$endtime,0,$formType,0);
        $statucResult = $this->problemproblemWaitSolveStatic($sourceResultData,0);

        $saveResult = $this->problemproblemWaitSolveSave($statucResult['deptcolumids'],$statucResult['staticdata'],$formType,$time,$dateFram);

        return $statucResult['useids'];

    }

    /**
     * 未解决问题统计表 历史统计数据生成
     * @param $starttime
     * @param $endtime
     * @param $time
     * @param $dateFram
     * @param $formType
     * @return mixed
     */
    public function problemHistoryUnresolved($starttime,$endtime, $time,$dateFram,$formType)
    {
        $sourceResultData = $this->getProblemDataList($starttime,$endtime,0,$formType,1);
        $statucResult = $this->problemproblemUnresolvedStatic($sourceResultData,0);


        $saveResult = $this->problemproblemUnresolvedSave($statucResult['deptcolumids'],$statucResult['staticdata'],$formType,$time,$dateFram);

        return $statucResult['useids'];

    }

    /**
     * 问题解决超期统计表 历史统计数据生成
     * @param $starttime
     * @param $endtime
     * @param $time
     * @param $dateFram
     * @param $formType
     * @return mixed
     */
    public function problemHistoryExceed($starttime,$endtime, $time,$dateFram,$formType)
    {
        $sourceResultData = $this->getProblemDataList($starttime,$endtime,0,$formType,0);
        $statucResult = $this->problemproblemExceedStatic($sourceResultData,0);

        $saveResult = $this->problemproblemExceedSave($statucResult['deptcolumids'],$statucResult['staticdata'],$formType,$time,$dateFram);

        return $statucResult['useids'];

    }
    public function problemHistoryExceedBackIn($starttime,$endtime, $time,$dateFram,$formType)
    {
        $sourceResultData = $this->getProblemDataList($starttime,$endtime,0,$formType,0);
        $statucResult = $this->problemproblemExceedBackInStatic($sourceResultData,0);

        $saveResult = $this->problemproblemExceedBackInSave($statucResult['deptcolumids'],$statucResult['staticdata'],$formType,$time,$dateFram);

        return $statucResult['useids'];

    }
    public function problemHistoryExceedBackOut($starttime,$endtime, $time,$dateFram,$formType)
    {
        $sourceResultData = $this->getProblemDataList($starttime,$endtime,0,$formType,0);
        $statucResult = $this->problemproblemExceedBackOutStatic($sourceResultData,0);

        $saveResult = $this->problemproblemExceedBackOutSave($statucResult['deptcolumids'],$statucResult['staticdata'],$formType,$time,$dateFram);

        return $statucResult['useids'];

    }

    /**
     * 添加问题单快照信息
     * @param $date
     * @param $time
     * @return array
     */
    public function addWholeReport($timeFrame, $time,$formtype)
    {
        $filestartday = str_replace(['-','_'],'',$timeFrame['startday']);
        $fileendday = str_replace(['-','_'],'',$timeFrame['endday']);
        $fileUrlBasicproblemOverall = '/data/upload/monthreport/problem_monthreport_basic_problemoverall' . $filestartday . '_' . $fileendday. '_' . $time . '.xlsx';
        $fileUrlBasicproblemWaitSolve = '/data/upload/monthreport/problem_monthreport_basic_problemwaitsolve' . $filestartday . '_' . $fileendday. '_' . $time . '.xlsx';
        $fileUrlBasicproblemUnresolved = '/data/upload/monthreport/problem_monthreport_basic_problemunresolved' . $filestartday . '_' . $fileendday. '_' . $time . '.xlsx';
        $fileUrlBasicproblemExceed = '/data/upload/monthreport/problem_monthreport_basic_problemexceed' . $filestartday . '_' . $fileendday. '_' . $time . '.xlsx';
        $fileUrlBasicproblemExceedBackIn = '/data/upload/monthreport/problem_monthreport_basic_problemexceedbackin' . $filestartday . '_' . $fileendday. '_' . $time . '.xlsx';
        $fileUrlBasicproblemExceedBackOut = '/data/upload/monthreport/problem_monthreport_basic_problemexceedbackout' . $filestartday . '_' . $fileendday. '_' . $time . '.xlsx';
        $fileUrlBasicproblemCompletedPlan = '/data/upload/monthreport/problem_monthreport_basic_problemcompletedplan' . $filestartday . '_' . $fileendday. '_' . $time . '.xlsx';
//        $fileFeedBackUrl = '/data/upload/problem_monthreport_feedback' . $filestartday . '_' . $fileendday. '_' . $time . '.xlsx';
        $fileUrl2problemOverall = '/data/upload/monthreport/problem_monthreport_problemoverall' . $filestartday . '_' . $fileendday. '_' . $time . '.xlsx';
        $fileUrl2problemWaitSolve = '/data/upload/monthreport/problem_monthreport_problemwaitsolve' . $filestartday . '_' . $fileendday. '_' . $time . '.xlsx';
        $fileUrl2problemUnresolved = '/data/upload/monthreport/problem_monthreport_problemunresolved' . $filestartday . '_' . $fileendday. '_' . $time . '.xlsx';
        $fileUrl2problemExceed = '/data/upload/monthreport/problem_monthreport_problemexceed' . $filestartday . '_' . $fileendday. '_' . $time . '.xlsx';
        $fileUrl2problemExceedBackIn = '/data/upload/monthreport/problem_monthreport_problemexceedbackin' . $filestartday . '_' . $fileendday. '_' . $time . '.xlsx';
        $fileUrl2problemExceedBackOut = '/data/upload/monthreport/problem_monthreport_problemexceedbackout' . $filestartday . '_' . $fileendday. '_' . $time . '.xlsx';
        $fileUrl2problemCompletedPlan = '/data/upload/monthreport/problem_monthreport_problemcompletedplan' . $filestartday . '_' . $fileendday. '_' . $time . '.xlsx';

        $this->loadModel('problem');


        $arr   = [
            'year'    => $timeFrame['year'],
            'month'   => $timeFrame['month'],
            'type'    => $formtype,
            'dtype'   => $timeFrame['dtype'],
            'startday'   => $timeFrame['startday'],
            'endday'   => $timeFrame['endday'],
            'isyear'   => $timeFrame['isyearform'],
            'fileUrl' => $fileUrlBasicproblemOverall,
            'fileUrl2' => $fileUrl2problemOverall,
        ];

        //添加整体统计
        if($formtype == 'problemOverall'){
            $arr['exportBasicFields'] = $this->config->problem->list->exportMonthReportFields;
            $arr['exportFields'] = $this->config->problem->list->exportMonthReportPartFields1;
            $this->dao->insert(TABLE_WHOLE_REPORT)->data($arr)->exec();
            $resId = $this->dao->lastInsertID();
        }elseif ($formtype == 'problemWaitSolve'){
            //未解决问题统计
            $arr['type'] = 'problemWaitSolve';
            $arr['fileUrl'] = $fileUrlBasicproblemWaitSolve;
            $arr['fileUrl2'] = $fileUrl2problemWaitSolve;
            $arr['exportBasicFields'] = $this->config->problem->list->exportMonthReportFields;
            $arr['exportFields'] = $this->config->problem->list->exportMonthReportPartFields1;
            $this->dao->insert(TABLE_WHOLE_REPORT)->data($arr)->exec();
            $resId = $this->dao->lastInsertID();
        }elseif ($formtype == 'problemUnresolved'){
            //未解决问题统计
            $arr['type'] = 'problemUnresolved';
            $arr['fileUrl'] = $fileUrlBasicproblemUnresolved;
            $arr['fileUrl2'] = $fileUrl2problemUnresolved;
            $arr['exportBasicFields'] = $this->config->problem->list->exportMonthReportFields;
            $arr['exportFields'] = $this->config->problem->list->exportMonthReportPartFields1;
            $this->dao->insert(TABLE_WHOLE_REPORT)->data($arr)->exec();
            $resId = $this->dao->lastInsertID();
        }elseif ($formtype == 'problemExceed'){
            //问题解决超期统计
            $arr['type'] = 'problemExceed';
            $arr['fileUrl'] = $fileUrlBasicproblemExceed;
            $arr['fileUrl2'] = $fileUrl2problemExceed;
            $arr['exportBasicFields'] = $this->config->problem->list->exportMonthReportFields;
            $arr['exportFields'] = $this->config->problem->list->exportMonthReportPartFields1;
            $this->dao->insert(TABLE_WHOLE_REPORT)->data($arr)->exec();
            $resId = $this->dao->lastInsertID();
        }elseif ($formtype == 'problemExceedBackIn'){
            //内部反馈超期统计
            $arr['type'] = 'problemExceedBackIn';
            $arr['fileUrl2'] = $fileUrl2problemExceedBackIn;
            $arr['fileUrl'] = $fileUrlBasicproblemExceedBackIn;
            $arr['exportBasicFields'] = $this->config->problem->list->exportMonthReportFields;
            $arr['exportFields'] = $this->config->problem->list->exportMonthReportPartFields2;
            $this->dao->insert(TABLE_WHOLE_REPORT)->data($arr)->exec();
            $resId = $this->dao->lastInsertID();
        }elseif ($formtype == 'problemExceedBackOut'){
            //外部反馈超期统计
            $arr['type'] = 'problemExceedBackOut';
            $arr['fileUrl2'] = $fileUrl2problemExceedBackOut;
            $arr['fileUrl'] = $fileUrlBasicproblemExceedBackOut;
            $arr['exportBasicFields'] = $this->config->problem->list->exportMonthReportFields;
            $arr['exportFields'] = $this->config->problem->list->exportMonthReportPartFields3;
            $this->dao->insert(TABLE_WHOLE_REPORT)->data($arr)->exec();
            $resId = $this->dao->lastInsertID();
        }elseif ($formtype == 'problemCompletedPlan'){
            //按计划解决情况统计
            $arr['type'] = 'problemCompletedPlan';
            $arr['fileUrl2'] = $fileUrl2problemCompletedPlan;
            $arr['fileUrl'] = $fileUrlBasicproblemCompletedPlan;
            $arr['exportBasicFields'] = $this->config->problem->list->exportMonthReportFields;
            $arr['exportFields'] = $this->config->problem->list->exportMonthReportCompletedPlanFields;
            $this->dao->insert(TABLE_WHOLE_REPORT)->data($arr)->exec();
            $resId = $this->dao->lastInsertID();
        }

        return $resId;

    }
    public function demandmonthreportadd($timeFrame, $time,$formtype)
    {
        $filestartday = str_replace(['-','_'],'',$timeFrame['startday']);
        $fileendday = str_replace(['-','_'],'',$timeFrame['endday']);
        $fileUrlBasicdemand_whole = '/data/upload/monthreport/demand_monthreport_basic_demand_whole' . $filestartday . '_' . $fileendday. '_' . $time . '.xlsx';
        $fileUrl2demand_whole = '/data/upload/monthreport/demand_monthreport_demand_whole'. $filestartday . '_' . $fileendday. '_' . $time . '.xlsx';

        $fileUrlBasicdemandunrealized = '/data/upload/monthreport/demand_monthreport_basic_demandunrealized' . $filestartday . '_' . $fileendday. '_' . $time . '.xlsx';
        $fileUrl2demandunrealized = '/data/upload/monthreport/demand_monthreport_demandunrealized'. $filestartday . '_' . $fileendday. '_' . $time . '.xlsx';

        $fileUrlBasicdemand_realized = '/data/upload/monthreport/demand_monthreport_basic_demand_realized' . $filestartday . '_' . $fileendday. '_' . $time . '.xlsx';
        $fileUrl2demand_realized = '/data/upload/monthreport/demand_monthreport_demand_realized'. $filestartday . '_' . $fileendday. '_' . $time . '.xlsx';

        $fileUrlBasicrequirement_inside = '/data/upload/monthreport/requirement_monthreport_basic_requirement_inside' . $filestartday . '_' . $fileendday. '_' . $time . '.xlsx';
        $fileUrl2requirement_inside = '/data/upload/monthreport/requirement_monthreport_requirement_inside'. $filestartday . '_' . $fileendday. '_' . $time . '.xlsx';

        $fileUrlBasicrequirement_outside = '/data/upload/monthreport/requirement_monthreport_basic_requirement_outside' . $filestartday . '_' . $fileendday. '_' . $time . '.xlsx';
        $fileUrl2requirement_outside = '/data/upload/monthreport/requirement_monthreport_requirement_outside'. $filestartday . '_' . $fileendday. '_' . $time . '.xlsx';

        $arr = [
            'year' => $timeFrame['year'],
            'month' => $timeFrame['month'],
            'type' => $timeFrame['dtype'],
            'dtype' => $timeFrame['dtype'],
            'startday' => $timeFrame['startday'],
            'isyear' => $timeFrame['isyearform'],
            'endday' => $timeFrame['endday'],
        ];
        $this->loadModel('demand');
        $this->loadModel('requirement');

        if($formtype == 'demand_whole'){
            //需求整体情况统计表
            $arr['type'] = 'demand_whole';
            $arr['fileUrl'] = $fileUrlBasicdemand_whole;
            $arr['fileUrl2'] = $fileUrl2demand_whole;
            $arr['exportBasicFields'] = $this->config->demand->list->exportMonthReportFields;
            $arr['exportFields'] = $this->config->demand->list->exportMonthReportPartFields1;
            $this->dao->insert(TABLE_WHOLE_REPORT)->data($arr)->exec();
            $resId = $this->dao->lastInsertID();
        }else if($formtype == 'demandunrealized'){
            //未实现需求统计表
            $arr['type'] = 'demandunrealized';
            $arr['fileUrl'] = $fileUrlBasicdemandunrealized;
            $arr['fileUrl2'] = $fileUrl2demandunrealized;
            $arr['exportBasicFields'] = $this->config->demand->list->exportMonthReportFields;
            $arr['exportFields'] = $this->config->demand->list->exportMonthReportPartFields1;
            $this->dao->insert(TABLE_WHOLE_REPORT)->data($arr)->exec();
            $resId = $this->dao->lastInsertID();
        }else if($formtype == 'demand_realized'){
            //未实现需求统计表
            $arr['type'] = 'demand_realized';
            $arr['fileUrl'] = $fileUrlBasicdemand_realized;
            $arr['fileUrl2'] = $fileUrl2demand_realized;
            $arr['exportBasicFields'] = $this->config->demand->list->exportMonthReportFields;
            $arr['exportFields'] = $this->config->demand->list->exportMonthReportPartFields1;
            $this->dao->insert(TABLE_WHOLE_REPORT)->data($arr)->exec();
            $resId = $this->dao->lastInsertID();
        }else if($formtype == 'requirement_inside'){
            //需求任务内部反馈超期统计表
            $arr['type'] = 'requirement_inside';
            $arr['fileUrl'] = $fileUrlBasicrequirement_inside;
            $arr['fileUrl2'] = $fileUrl2requirement_inside;
            $arr['exportBasicFields'] = $this->config->requirement->exportlist->exportMonthReportFields;
            $arr['exportFields'] = $this->config->requirement->exportlist->exportMonthReportPartFields1;
            $this->dao->insert(TABLE_WHOLE_REPORT)->data($arr)->exec();
            $resId = $this->dao->lastInsertID();
        }else if($formtype == 'requirement_outside'){
            //需求任务外部反馈超期统计表
            $arr['type'] = 'requirement_outside';
            $arr['fileUrl'] = $fileUrlBasicrequirement_outside;
            $arr['fileUrl2'] = $fileUrl2requirement_outside;
            $arr['exportBasicFields'] = $this->config->requirement->exportlist->exportMonthReportFields;
            $arr['exportFields'] = $this->config->requirement->exportlist->exportMonthReportPartFields2;
            $this->dao->insert(TABLE_WHOLE_REPORT)->data($arr)->exec();
            $resId = $this->dao->lastInsertID();
        }


        return $resId;
    }

    public function isExecutive($userName)
    {
        $info = $this->dao->select('id')->from(TABLE_DEPT)->where('FIND_IN_SET("' . $userName . '", executive)')->fetch();

        return !empty($info);
    }


    public function secondordermonthreportadd($timeFrame, $time,$formtype)
    {
        $filestartday = str_replace(['-','_'],'',$timeFrame['startday']);
        $fileendday = str_replace(['-','_'],'',$timeFrame['endday']);
        $fileUrlBasicsecondorderclass = '/data/upload/monthreport/secondorder_monthreport_basic_secondorderclass' . $filestartday . '_' . $fileendday. '_' . $time . '.xlsx';
        $fileUrl2secondorderclass = '/data/upload/monthreport/secondorder_monthreport_secondorderclass'. $filestartday . '_' . $fileendday. '_' . $time . '.xlsx';

        $fileUrlBasicsecondorderaccept = '/data/upload/monthreport/secondorder_monthreport_basic_secondorderaccept' . $filestartday . '_' . $fileendday. '_' . $time . '.xlsx';
        $fileUrl2secondorderaccept = '/data/upload/monthreport/secondorder_monthreport_secondorderaccept'. $filestartday . '_' . $fileendday. '_' . $time . '.xlsx';

        $arr = [
            'year' => $timeFrame['year'],
            'month' => $timeFrame['month'],
            'type' => $timeFrame['stype'],
            'dtype' => $timeFrame['dtype'],
            'startday' => $timeFrame['startday'],
            'endday' => $timeFrame['endday'],
            'isyear' => $timeFrame['isyearform'],
        ];

        if($formtype == 'secondorderclass'){
            $fieldArrs = $this->loadModel('secondmonthreport')->getexportField($formtype,'');

            $arr['type'] = 'secondorderclass';
            $arr['fileUrl'] = $fileUrlBasicsecondorderclass;
            $arr['fileUrl2'] = $fileUrl2secondorderclass;
            $arr['exportBasicFields'] = $fieldArrs['basic'];
            $arr['exportFields'] = $fieldArrs['form'];
            $this->dao->insert(TABLE_WHOLE_REPORT)->data($arr)->exec();
            $resId = $this->dao->lastInsertID();
        }else if($formtype == 'secondorderaccept'){
            $fieldArrs = $this->loadModel('secondmonthreport')->getexportField($formtype,'');

            $arr['type'] = 'secondorderaccept';
            $arr['fileUrl'] = $fileUrlBasicsecondorderaccept;
            $arr['fileUrl2'] = $fileUrl2secondorderaccept;
            $arr['exportBasicFields'] = $fieldArrs['basic'];
            $arr['exportFields'] = $fieldArrs['form'];
            $this->dao->insert(TABLE_WHOLE_REPORT)->data($arr)->exec();
            $resId = $this->dao->lastInsertID();
        }
        return $resId;
    }
    public function supportmonthreportadd($timeFrame, $time,$formtype)
    {
        $filestartday = str_replace(['-','_'],'',$timeFrame['startday']);
        $fileendday = str_replace(['-','_'],'',$timeFrame['endday']);
        $fileUrlBasicsupport = '/data/upload/monthreport/support_monthreport_basic_support' . $filestartday . '_' . $fileendday. '_' . $time . '.xlsx';
        $fileUrl2support = '/data/upload/monthreport/support_monthreport_support'. $filestartday . '_' . $fileendday. '_' . $time . '.xlsx';


        $arr = [
            'year' => $timeFrame['year'],
            'month' => $timeFrame['month'],
            'type' => $timeFrame['stype'],
            'dtype' => $timeFrame['dtype'],
            'startday' => $timeFrame['startday'],
            'endday' => $timeFrame['endday'],
            'isyear' => $timeFrame['isyearform'],
        ];

        if($formtype == 'support'){
            $fieldArrs = $this->loadModel('secondmonthreport')->getexportField($formtype,'');

            $arr['type'] = 'support';
            $arr['fileUrl'] = $fileUrlBasicsupport;
            $arr['fileUrl2'] = $fileUrl2support;
            $arr['exportBasicFields'] = $fieldArrs['basic'];
            $arr['exportFields'] = $fieldArrs['form'];
            $this->dao->insert(TABLE_WHOLE_REPORT)->data($arr)->exec();
            $resId = $this->dao->lastInsertID();
        }
        return $resId;
    }
    public function workloadmonthreportadd($timeFrame, $time,$formtype)
    {
        $filestartday = str_replace(['-','_'],'',$timeFrame['startday']);
        $fileendday = str_replace(['-','_'],'',$timeFrame['endday']);
        $fileUrlBasicworkload = '/data/upload/monthreport/workload_monthreport_basic_workload' . $filestartday . '_' . $fileendday. '_' . $time . '.xlsx';
        $fileUrl2workload = '/data/upload/monthreport/workload_monthreport_workload'. $filestartday . '_' . $fileendday. '_' . $time . '.xlsx';

        $arr = [
            'year' => $timeFrame['year'],
            'month' => $timeFrame['month'],
            'type' => $timeFrame['stype'],
            'dtype' => $timeFrame['dtype'],
            'startday' => $timeFrame['startday'],
            'endday' => $timeFrame['endday'],
            'isyear' => $timeFrame['isyearform'],
        ];

        if($formtype == 'workload'){
            $fieldArrs = $this->loadModel('secondmonthreport')->getexportField($formtype,'');

            $arr['type'] = 'workload';
            $arr['fileUrl'] = $fileUrlBasicworkload;
            $arr['fileUrl2'] = $fileUrl2workload;
            $arr['exportBasicFields'] = $fieldArrs['basic'];
            $arr['exportFields'] = $fieldArrs['form'];
            $this->dao->insert(TABLE_WHOLE_REPORT)->data($arr)->exec();
            $resId = $this->dao->lastInsertID();
        }
        return $resId;
    }

    public function modifymonthreportadd($timeFrame, $time,$formtype)
    {
        $filestartday = str_replace(['-','_'],'',$timeFrame['startday']);
        $fileendday = str_replace(['-','_'],'',$timeFrame['endday']);
        $fileUrlBasicmodifywhole = '/data/upload/monthreport/modify_monthreport_basic_modifywhole' . $filestartday . '_' . $fileendday. '_' . $time . '.xlsx';
        $fileUrl2modifywhole = '/data/upload/monthreport/modify_monthreport_modifywhole'. $filestartday . '_' . $fileendday. '_' . $time . '.xlsx';

        $fileUrlBasicmodifyabnormal = '/data/upload/monthreport/modify_monthreport_basic_modifyabnormal' . $filestartday . '_' . $fileendday. '_' . $time . '.xlsx';
        $fileUrl2modifyabnormal = '/data/upload/monthreport/modify_monthreport_modifyabnormal'. $filestartday . '_' . $fileendday. '_' . $time . '.xlsx';

        $arr = [
            'year' => $timeFrame['year'],
            'month' => $timeFrame['month'],
            'type' => $timeFrame['stype'],
            'dtype' => $timeFrame['dtype'],
            'startday' => $timeFrame['startday'],
            'endday' => $timeFrame['endday'],
            'isyear' => $timeFrame['isyearform'],
        ];

        if($formtype == 'modifywhole'){
            $fieldArrs = $this->loadModel('secondmonthreport')->getexportField($formtype,'');
            $arr['type'] = 'modifywhole';
            $arr['fileUrl'] = $fileUrlBasicmodifywhole;
            $arr['fileUrl2'] = $fileUrl2modifywhole;
            $arr['exportBasicFields'] = $fieldArrs['basic'];
            $arr['exportFields'] = $fieldArrs['form'];
            $this->dao->insert(TABLE_WHOLE_REPORT)->data($arr)->exec();
            $resId = $this->dao->lastInsertID();
        }else if($formtype == 'modifyabnormal'){
            $fieldArrs = $this->loadModel('secondmonthreport')->getexportField($formtype,'');
            $arr['type'] = 'modifyabnormal';
            $arr['fileUrl'] = $fileUrlBasicmodifyabnormal;
            $arr['fileUrl2'] = $fileUrl2modifyabnormal;
            $arr['exportBasicFields'] = $fieldArrs['basic'];
            $arr['exportFields'] = $fieldArrs['form'];
            $this->dao->insert(TABLE_WHOLE_REPORT)->data($arr)->exec();
            $resId = $this->dao->lastInsertID();
        }
        return $resId;
    }




    //通用
    public function getNeedStaticDept($needWu=0){
        $monthReportNeedDept = array_keys($this->lang->secondmonthreport->monthReportNeedDept);
        foreach ($monthReportNeedDept as $key=>$value){
            if(!$value){
                unset($monthReportNeedDept[$key]);
            }
        }
        if($needWu){
            $monthReportNeedDept[-1] = -1;
        }
        return $monthReportNeedDept;
    }

    //通用
    public function getNeedShowDept($needWu=0, $isQuarter = false){

        if(!$isQuarter){
            $monthReportNeedShowDept = array_keys($this->lang->secondmonthreport->monthReportNeedShowDept);
        }else{
            $monthReportNeedShowDept = array_keys($this->lang->secondmonthreport->quarterReportNeedDept);
        }

        foreach ($monthReportNeedShowDept as $key=>$value){
            if(!$value){
                unset($monthReportNeedShowDept[$key]);
            }
        }
        if($needWu){
            $monthReportNeedShowDept[] = -1;
        }
        return $monthReportNeedShowDept;
    }



    //前台展示排序部门
    public function getFrontShowDept($isAll=0){

        $monthReportOrderDept = $this->lang->secondmonthreport->monthReportOrderDept;
        foreach ($monthReportOrderDept as $key=>$value){
            if(!$value){
                unset($monthReportOrderDept[$key]);
            }
        }
        if($isAll){
            $monthReportOrderDept = [0=>'全部'] + $monthReportOrderDept;
        }
        return $monthReportOrderDept;
    }


    //获取二线月报工作量统计的二线 项目
    public function getSecondLineProject(){

        $monthReportSecondLineProject = $this->lang->secondmonthreport->monthReportSecondLineProject;
        foreach ($monthReportSecondLineProject as $key=>$value){
            if(!$value){
                unset($monthReportSecondLineProject[$key]);
            }
        }

        return array_keys($monthReportSecondLineProject);
    }


    /**
     * 前台数据展示数据处理
     * @param $wholeID 统计快照id
     * @param $deptID 部门id
     * @param $dataKey 查询数据返回该值作为key的数据
     * @return array
     */
    public function getOrderDataList($wholeID,$deptID,$dataKey='deptID'){
        $depts = $this->getFrontShowDept();
        $detailReports = [];

        $tempdetailReports = $this->getDetailReportByColum($wholeID, $deptID,$dataKey);

        //按照固定顺序展示
        foreach ($depts as $keyDeptID=>$depname){
            if(isset($tempdetailReports[$keyDeptID])){
                $detailReports[$keyDeptID] = $tempdetailReports[$keyDeptID];
            }
        }
        //若是固定顺序后，补齐未排序的数据
        foreach ($tempdetailReports as $dept=>$data){
            if(!isset($detailReports[$dept])){
                $detailReports[$dept] = $data;
            }
        }
        return $detailReports;

    }

    public function getOrderRealTimeDataList($dataArr){
        $depts = $this->getFrontShowDept();
        $detailReports = [];

        //按照固定顺序展示
        foreach ($depts as $keyDeptID=>$depname){
            if(isset($dataArr[$keyDeptID])){
                $detailReports[$keyDeptID] = $dataArr[$keyDeptID];
            }
        }
        //若是固定顺序后，补齐未排序的数据
        foreach ($dataArr as $dept=>$data){
            if(!isset($detailReports[$dept])){
                $detailReports[$dept] = $data;
            }
        }
        return $detailReports;

    }


    /*
     * 获取 历史报表  月度，年度
     */
    public function ajaxgetdatalist(){
        $searchtype = isset($_POST['searchtype']) ? $_POST['searchtype'] : '';
        $histimetype = $_POST['histimetype'];
        $realtimetype = isset($_POST['realtimetype']) ? $_POST['realtimetype'] : '';
        $sformtype = $_POST['sformtype'];
        $selectDate = $_POST['selectDate'];
        $result = '';
        if($searchtype == 'history'){
            $result = $this->getWholeReport($sformtype,$histimetype,$selectDate);
        }
        return $result;

    }

    /**
     * 历史报表搜索 下拉字符串拼接
     * @param $sformtype
     * @param $histimetype
     * @param $selectDate
     * @return string
     */
    public function getWholeReport($sformtype,$histimetype,$selectDate){

        $resultData =[];
        if($histimetype == 'hisyear'){
            $dataList = $this->dao->select("*")->from(TABLE_WHOLE_REPORT)->where('dtype')->eq(1)
                ->andWhere('type')->eq($sformtype)
                ->andWhere('isyear')->eq(2)
                ->orderBy("year_desc,month_desc,id_desc")
                ->fetchAll('id');
            $buqidata = $this->dao->select("*")->from(TABLE_WHOLE_REPORT)->where('dtype')->eq(1)
                ->andWhere('type')->eq($sformtype)
                ->andWhere('isyear')->in([1,2])
                ->orderBy("year_desc,month_desc,id_desc")
                ->fetch();
            if(!isset($dataList[$buqidata->id])){
//                $dataList[$buqidata->id] = $buqidata;
                $dataList = array_merge([$buqidata->id=>$buqidata],$dataList);
            }

            foreach ($dataList as $data){
                $resultData[$data->id] = $data->startday.'至'.$data->endday;
            }
        }else if($histimetype == 'hismonth'){
            $dataList = $this->dao
                ->select("*")
                ->from(TABLE_WHOLE_REPORT)
                ->where('dtype')->eq(1)
                ->andWhere('type')->eq($sformtype)
                ->andWhere('isyear')->in([1,2])
                ->orderBy("year_desc,month_desc,id_desc")
                ->fetchAll();
            foreach ($dataList as $data){
                $resultData[$data->id] = $data->year.'年'.$data->month.'月';
            }
        }else if($histimetype == 'hisquarter'){
            $dataList = $this->dao
                ->select("*")
                ->from(TABLE_WHOLE_REPORT)
                ->where('dtype')->eq(1)
                ->andWhere('type')->eq($sformtype)
                ->andWhere('isyear')->eq(4)
                ->orderBy("year_desc,month_desc,id_desc")
                ->fetchAll();
            foreach ($dataList as $data){
                if(0 == $data->month % 3){
                    $resultData[$data->id] = $data->year.'年'.($data->month / 3).'季度' . $data->createdDate;
                }
            }
        }

        $htmlstr = '';
        foreach ($resultData as $wid=>$value){
            $selectdStr = '';
            if($selectDate == $wid){
                $selectdStr = 'selected';
            }
            $htmlstr .= "<option value='{$wid}' {$selectdStr}>{$value}</option>";
        }

        return $htmlstr;
    }




    /**
     * 获取非考核表单的开始和结束时间
     * @param $stype
     * @param $ishis 是否快照
     * @param $dtype
     * @return array
     */
    public function getTimeRangeFrame($formtype,$ishis=0,$dtype=0)
    {
        //如果指定了日期 则使用指定日期推算
            $statisticsDate = date('Y-m-d');

            $statisticsTimeStamp = strtotime($statisticsDate);

            $year  = (int)date('Y', $statisticsTimeStamp);
            $month = (int)date('m', $statisticsTimeStamp);

            if (1 == $month) {
                $resultyear  = $year - 1;
                $resultmonth = 12;
            } else {
                $resultyear  = $year;
                $resultmonth = $month - 1;
            }
            // 实时 自定义 查询
        $startDate = date('Y-m-d H:i:s', strtotime($resultyear . '-01-01 00:00:00'));
        $startday = date('Y-m-d', strtotime($startDate));
        if($dtype == 1){
            $calendarstartDate = "1970-01-01 00:00:00";
            $calendarstartday = "1970-01-01";
        }else{
            $calendarstartDate = $startDate;
            $calendarstartday = $startday;
        }

            //如果考核的月份和当前月份相同 则说明是真正的年度数据
            $isyearForm = 1;
            if($ishis == 1){
                $tempTimeStamp = strtotime($resultyear . '-' . $resultmonth.'-01');

                $lastday       = date('t', $tempTimeStamp);
                $endDate       = date('Y-m-d H:i:s', strtotime($resultyear . '-' . $resultmonth . '-' . $lastday . ' 23:59:59'));
                $endDay       = date('Y-m-d', strtotime($endDate));
                //如果是12月 则说明是真正的年度数据
                if($resultmonth == 12){
                    $isyearForm = 2;
                }
            }else{
                $endDate       = date('Y-m-d H:i:s',time());
                $endDay       = date('Y-m-d', strtotime($endDate));
            }


            return ['startdate' => $startDate, 'enddate' => $endDate,'startday'=>$startday,'endday'=>$endDay,'isyearform'=>$isyearForm,'stype'=>$formtype,'year'=>$resultyear,'month'=>$resultmonth,'dtype'=>$dtype,'calendarstartday'=>$calendarstartday];
    }

    /*
     * 考核表单
     * 本年度：考核周期起始日期  到 操作时刻的数据
     * 自定义： 考核周期起始日期 到 自定义结束时间内的日期。
     * 历史报表：
     * 年度： 考核周期起始日期到考核结束日期。
     * 月度： 考核起始日期到最近月底(当统计时日期大于了考核结束日期，则以考核结束日期为准)。
     * 实时报表
     * 本年度：考核周期起始日期到操作时刻的数据。
     * 自定义：考核周期起始日期到自定义时间内的数据
     * 根据表单类型获取考核周期的开始和结束时间
     */
    public function getCustomTimeFrame($formtype,$ishis=0,$dtype=0)
    {
        //如果指定了日期 则使用指定日期推算

        $timeconfig = $this->lang->secondmonthreport->examinecycleList[$formtype];
        $timeconfigYear = (int)$this->lang->secondmonthreport->examinecycleList['examineyear'];
        if(!$timeconfig){
            throw new Exception($this->lang->secondmonthreport->cycleconfigError);
        }

        $customdateconfigArr = explode('~',$timeconfig);
        $startTimeConfig = explode('$',$customdateconfigArr[0]);
        $endTimeConfig = explode('$',$customdateconfigArr[1]);
        if($startTimeConfig[0] == 2){
            $configStartDate = ($timeconfigYear - 1).'-'.$startTimeConfig[1];
        }else{
            $configStartDate = $timeconfigYear.'-'.$startTimeConfig[1];
        }
        if($endTimeConfig[0] == 2){
            $configEndDate = ($timeconfigYear - 1).'-'.$endTimeConfig[1];
        }else{
            $configEndDate = $timeconfigYear.'-'.$endTimeConfig[1];
        }
        $configStartDateSecond = $configStartDate.' 00:00:00';
        $configEndDateSecond = $configEndDate.' 23:59:59';

        $examineMonth = (int)date("n",strtotime($configEndDate));

        //如果是快照 要求到最近月底
        if($ishis == 1){

            $statisticsTime = time();

            $year  = (int)date('Y', $statisticsTime);
            $month = (int)date('n', $statisticsTime);

            if (1 == $month) {
                $resultyear  = $year - 1;
                $resultmonth = 12;
            } else {
                $resultyear  = $year;
                $resultmonth = $month - 1;
            }
            $tempTimeStamp = strtotime($resultyear . '-' . $resultmonth.'-01');
            $lastday       = date('t', $tempTimeStamp);
            //当前时间是最近的月底
            $curtime = strtotime($resultyear . '-' . $resultmonth . '-' . $lastday . ' 23:59:59');
            $curYear = (int)date("Y",$curtime);
            $curMonth = (int)date("n",$curtime);
        }else{
            //不是快照则使用当前时间
            $curtime = time();
            $curYear = (int)date("Y",$curtime);
            $curMonth = (int)date("n",$curtime);
            $resultyear = $curYear;
            $resultmonth = $curMonth;
        }
        //考核表单 按照 配置的日期走。  下方判断预留 自定义 可选时间范围
        if($dtype == 1){
            $calendarstartDate = $configStartDateSecond;
            $calendarstartday = $configStartDate;
        }else{
            $calendarstartDate = $configStartDateSecond;
            $calendarstartday = $configStartDate;
        }

//        $curtime = strtotime("2024-12-28");// 2025-01-01 14:52:40

        //如果考核的月份和当前月份相同 则说明是真正的年度数据
        $isyearForm = 1;
        if($examineMonth == $curMonth){
            $isyearForm = 2;
        }

        //如果当前时间大于考核周期结束时间，则结束时间为考核周期的结束时间
        if($curtime > strtotime($configEndDateSecond)){
            $realEndDate = $configEndDate;
        }else{
            $realEndDate = date("Y-m-d",$curtime);
        }

        $realEndDateSecond = $realEndDate.' 23:59:59';

        return ['startdate' => $configStartDateSecond, 'enddate' => $realEndDateSecond,'startday'=>$configStartDate,'endday'=>$realEndDate,'isyearform'=>$isyearForm,'stype'=>$formtype,'year'=>$resultyear,'month'=>$resultmonth,'dtype'=>$dtype,'calendarstartday'=>$calendarstartday];

    }

    public function getSnapshotDateRange($formtype,$ishis,$dtype=0){

        if(in_array($formtype,$this->lang->secondmonthreport->examineFormList)){
            $date = $this->getCustomTimeFrame($formtype,$ishis,$dtype);
        }else{
            //非考核表单 1-1号到当月月底
            $date = $this->getTimeRangeFrame($formtype,$ishis,$dtype);

        }
        return $date;

    }
    public function ajaxgetdaterange(){
        $searchtype = isset($_POST['searchtype']) ? $_POST['searchtype'] : '';
        $realtimetype = isset($_POST['realtimetype']) ? $_POST['realtimetype'] : '';
        $dtype = 0;
        $sformtype = $_POST['sformtype'];
        if($realtimetype == 'custom'){
            $dtype = 1;
        }
        /*else if($realtimetype == 'curyear'){

        }*/
        //考核表单
        if(in_array($sformtype,$this->lang->secondmonthreport->examineFormList)){
            $date = $this->getCustomTimeFrame($sformtype,0,$dtype);
        }else{
            //非考核表单
            $date = $this->getTimeRangeFrame($sformtype,0,$dtype);

        }

        return $date;

    }
}
