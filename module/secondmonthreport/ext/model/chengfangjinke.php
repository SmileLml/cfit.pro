<?php

function getProblemDataList($start,$end,$deptID,$staticType,$isuseHisData){
    return $this->loadExtension('chengfangjinke')->getProblemDataList($start,$end,$deptID,$staticType,$isuseHisData);
}
function getDemandDataList($start,$end,$deptID,$staticType,$isuseHisData){
    return $this->loadExtension('chengfangjinke')->getDemandDataList($start,$end,$deptID,$staticType,$isuseHisData);
}
function getRequirementDataList($start,$end,$deptID,$staticType,$isuseHisData){
    return $this->loadExtension('chengfangjinke')->getRequirementDataList($start,$end,$deptID,$staticType,$isuseHisData);
}
function getSecondorderDataList($start,$end,$deptID,$staticType,$isuseHisData){
    return $this->loadExtension('chengfangjinke')->getSecondorderDataList($start,$end,$deptID,$staticType,$isuseHisData);
}
function getSupportDataList($start,$end,$deptID,$staticType,$isuseHisData){
    return $this->loadExtension('chengfangjinke')->getSupportDataList($start,$end,$deptID,$staticType,$isuseHisData);
}
function getWorkloadDataList($start,$end,$deptID,$staticType,$isuseHisData){
    return $this->loadExtension('chengfangjinke')->getWorkloadDataList($start,$end,$deptID,$staticType,$isuseHisData);
}
function getModifyDataList($start,$end,$deptID,$staticType,$isuseHisData){
    return $this->loadExtension('chengfangjinke')->getModifyDataList($start,$end,$deptID,$staticType,$isuseHisData);
}
function getModifycnccDataList($start,$end,$deptID,$staticType,$isuseHisData){
    return $this->loadExtension('chengfangjinke')->getModifycnccDataList($start,$end,$deptID,$staticType,$isuseHisData);
}
function getProblemHistoryDataList($deptID){
    return $this->loadExtension('chengfangjinke')->getProblemHistoryDataList($deptID);
}
function getDemandHistoryDataList($deptID){
    return $this->loadExtension('chengfangjinke')->getDemandHistoryDataList($deptID);
}

function demandwholeDemandMonthStatic($demandData,$deptID){
    return $this->loadExtension('chengfangjinke')->demandwholeDemandMonthStatic($demandData,$deptID);
}
function demandunrealizedStatic($unrealizedInfo,$deptID){
    return $this->loadExtension('chengfangjinke')->demandunrealizedStatic($unrealizedInfo,$deptID);
}
function demandrealizedMonthStatic($realizedInfo,$deptID){
    return $this->loadExtension('chengfangjinke')->demandrealizedMonthStatic($realizedInfo,$deptID);
}
function requirementInsideStatic($requirementInsideDataList,$deptID){
    return $this->loadExtension('chengfangjinke')->requirementInsideStatic($requirementInsideDataList,$deptID);
}
function requirementOutsideStatic($requirementOutsideDataList,$deptID){
    return $this->loadExtension('chengfangjinke')->requirementOutsideStatic($requirementOutsideDataList,$deptID);
}
function secondorderclassStatic($secondorderData,$deptID){
    return $this->loadExtension('chengfangjinke')->secondorderclassStatic($secondorderData,$deptID);
}
function secondorderacceptStatic($secondorderData,$deptID){
    return $this->loadExtension('chengfangjinke')->secondorderacceptStatic($secondorderData,$deptID);
}
function supportStatic($supportData,$deptID){
    return $this->loadExtension('chengfangjinke')->supportStatic($supportData,$deptID);
}
function workloadStatic($workloadData,$deptID){
    return $this->loadExtension('chengfangjinke')->workloadStatic($workloadData,$deptID);
}
function modifywholeStatic($modifywhoeData,$deptID){
    return $this->loadExtension('chengfangjinke')->modifywholeStatic($modifywhoeData,$deptID);
}
function modifyabnormalStatic($modifyabnormalData,$deptID){
    return $this->loadExtension('chengfangjinke')->modifyabnormalStatic($modifyabnormalData,$deptID);
}
function problemproblemOverallStatic($problemData,$deptID){
    return $this->loadExtension('chengfangjinke')->problemproblemOverallStatic($problemData,$deptID);
}
function problemproblemWaitSolveStatic($problemData,$deptID){
    return $this->loadExtension('chengfangjinke')->problemproblemWaitSolveStatic($problemData,$deptID);
}
function problemproblemUnresolvedStatic($problemData,$deptID){
    return $this->loadExtension('chengfangjinke')->problemproblemUnresolvedStatic($problemData,$deptID);
}
function problemproblemExceedStatic($problemData,$deptID){
    return $this->loadExtension('chengfangjinke')->problemproblemExceedStatic($problemData,$deptID);
}
function problemproblemExceedBackInStatic($problemData,$deptID){
    return $this->loadExtension('chengfangjinke')->problemproblemExceedBackInStatic($problemData,$deptID);
}
function problemproblemExceedBackOutStatic($problemData,$deptID){
    return $this->loadExtension('chengfangjinke')->problemproblemExceedBackOutStatic($problemData,$deptID);
}
function demanddemandwholeSave($demandwholdIdArr,$demandwholes,$formType,$time,$timeFrame){
    return $this->loadExtension('chengfangjinke')->demanddemandwholeSave($demandwholdIdArr,$demandwholes,$formType,$time,$timeFrame);
}
function demandunrealizedSave($demandunrealizedIdArr,$jsonUnrealizedData,$formType,$time,$timeFrame){
    return $this->loadExtension('chengfangjinke')->demandunrealizedSave($demandunrealizedIdArr,$jsonUnrealizedData,$formType,$time,$timeFrame);
}
function demandrealizedMonthSave($demand_realizedIdArr,$jsonRealizedData,$formType,$time,$timeFrame){
    return $this->loadExtension('chengfangjinke')->demandrealizedMonthSave($demand_realizedIdArr,$jsonRealizedData,$formType,$time,$timeFrame);
}
function requirementInsideSave($requirement_insideIdArr,$jsonRealizedData,$formType,$time,$timeFrame){
    return $this->loadExtension('chengfangjinke')->requirementInsideSave($requirement_insideIdArr,$jsonRealizedData,$formType,$time,$timeFrame);
}
function requirementOutsideSave($requirement_outsideIdArr,$jsonRealizedData,$formType,$time,$timeFrame){
    return $this->loadExtension('chengfangjinke')->requirementOutsideSave($requirement_outsideIdArr,$jsonRealizedData,$formType,$time,$timeFrame);
}
function secondorderclassSave($secondorderclassIdArr,$countData,$formType,$time,$timeFrame){
    return $this->loadExtension('chengfangjinke')->secondorderclassSave($secondorderclassIdArr,$countData,$formType,$time,$timeFrame);
}
function secondorderacceptSave($secondorderacceptIdArr,$countData,$formType,$time,$timeFrame){
    return $this->loadExtension('chengfangjinke')->secondorderacceptSave($secondorderacceptIdArr,$countData,$formType,$time,$timeFrame);
}
function supportSave($supportIdArr,$countData,$formType,$time,$timeFrame){
    return $this->loadExtension('chengfangjinke')->supportSave($supportIdArr,$countData,$formType,$time,$timeFrame);
}
function workloadSave($workloadIdArr,$countData,$formType,$time,$timeFrame){
    return $this->loadExtension('chengfangjinke')->workloadSave($workloadIdArr,$countData,$formType,$time,$timeFrame);
}
function modifywholeSave($modifywholeIdArr,$countData,$formType,$time,$timeFrame){
    return $this->loadExtension('chengfangjinke')->modifywholeSave($modifywholeIdArr,$countData,$formType,$time,$timeFrame);
}
function modifyabnormalSave($modifyabnormalIdArr,$countData,$formType,$time,$timeFrame){
    return $this->loadExtension('chengfangjinke')->modifyabnormalSave($modifyabnormalIdArr,$countData,$formType,$time,$timeFrame);
}
function problemproblemOverallSave($problemOverallIdArr,$overalls,$formType,$time,$timeFrame){
    return $this->loadExtension('chengfangjinke')->problemproblemOverallSave($problemOverallIdArr,$overalls,$formType,$time,$timeFrame);
}
function problemproblemWaitSolveSave($problemWaitSolveIdArr,$waitSolves,$formType,$time,$timeFrame){
    return $this->loadExtension('chengfangjinke')->problemproblemWaitSolveSave($problemWaitSolveIdArr,$waitSolves,$formType,$time,$timeFrame);
}
function problemproblemUnresolvedSave($problemUnresolvedIdArr,$unresolveds,$formType,$time,$timeFrame){
    return $this->loadExtension('chengfangjinke')->problemproblemUnresolvedSave($problemUnresolvedIdArr,$unresolveds,$formType,$time,$timeFrame);
}
function problemproblemExceedSave($problemExceedIdArr,$exceeds,$formType,$time,$timeFrame){
    return $this->loadExtension('chengfangjinke')->problemproblemExceedSave($problemExceedIdArr,$exceeds,$formType,$time,$timeFrame);
}
function problemproblemExceedBackInSave($problemExceedBackInIdArr,$exceedBackIns,$formType,$time,$timeFrame){
    return $this->loadExtension('chengfangjinke')->problemproblemExceedBackInSave($problemExceedBackInIdArr,$exceedBackIns,$formType,$time,$timeFrame);
}
function problemproblemExceedBackOutSave($problemExceedBackOutIdArr,$exceedBackOuts,$formType,$time,$timeFrame){
    return $this->loadExtension('chengfangjinke')->problemproblemExceedBackOutSave($problemExceedBackOutIdArr,$exceedBackOuts,$formType,$time,$timeFrame);
}
function getexportField($staticType,$phototype){
    return $this->loadExtension('chengfangjinke')->getexportField($staticType,$phototype);
}
function getProblemDataListByIDs($ids){
    return $this->loadExtension('chengfangjinke')->getProblemDataListByIDs($ids);
}
function getDemandDataListByIDs($ids){
    return $this->loadExtension('chengfangjinke')->getDemandDataListByIDs($ids);
}
function getRequirementDataListByIDs($ids){
    return $this->loadExtension('chengfangjinke')->getRequirementDataListByIDs($ids);
}
function getSecondorderDataListByIDs($ids){
    return $this->loadExtension('chengfangjinke')->getSecondorderDataListByIDs($ids);
}
function getSupportDataListByIDs($ids){
    return $this->loadExtension('chengfangjinke')->getSupportDataListByIDs($ids);
}
function getWorkloadDataListByIDs($ids){
    return $this->loadExtension('chengfangjinke')->getWorkloadDataListByIDs($ids);
}
function getModifyDataListByIDs($ids){
    return $this->loadExtension('chengfangjinke')->getModifyDataListByIDs($ids);
}
function getModifycnccDataListByIDs($ids){
    return $this->loadExtension('chengfangjinke')->getModifycnccDataListByIDs($ids);
}
function getSearchDefaultID($staticType,$timeType = 'hismonth'){
    return $this->loadExtension('chengfangjinke')->getSearchDefaultID($staticType,$timeType);
}
function getRealUseDepts($deptID){
    return $this->loadExtension('chengfangjinke')->getRealUseDepts($deptID);
}
function getCreditDataList($start,$end,$deptID,$staticType,$isuseHisData){
    return $this->loadExtension('chengfangjinke')->getCreditDataList($start,$end,$deptID,$staticType,$isuseHisData);
}
function getCreditDataListByIDs($ids){
    return $this->loadExtension('chengfangjinke')->getCreditDataListByIDs($ids);
}

function problemCompletedPlanSave($problemExceedIdArr,$exceeds,$formType,$time,$timeFrame){
    return $this->loadExtension('chengfangjinke')->problemCompletedPlanSave($problemExceedIdArr,$exceeds,$formType,$time,$timeFrame);
}