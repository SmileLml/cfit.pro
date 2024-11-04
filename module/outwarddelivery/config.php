<?php
$config->outwarddelivery = new stdclass();
$config->outwarddelivery->editor      = new stdclass();
$config->outwarddelivery->editor2      = new stdclass();
$config->outwarddelivery->create      = new stdclass();
$config->outwarddelivery->edit        = new stdclass();
$config->outwarddelivery->link        = new stdclass();

$config->outwarddelivery->create->requiredFields = 'outwardDeliveryDesc,implementationForm,projectPlanId';
$config->outwarddelivery->edit->requiredFields   = 'outwardDeliveryDesc,implementationForm,projectPlanId,ROR';
$config->outwarddelivery->editor2->create = 'ok2222222';
$config->outwarddelivery->link->requiredFields = 'release,isMediaChanged';

$config->outwarddelivery->editor->create   = array('id' => 'content,env,introductionToFunctionsAndUses,remark,target,reason,changeContentAndMethod
            ,step,techniqueCheck,test,checkList,businessCooperateContent,judgePlan,effect,businessFunctionAffect,backupDataCenterChangeSyncDesc,emergencyManageAffect
            ,businessAffect,risk,controlTableSteps,changeImpactAnalysis', 'tools' => 'modifycnccTools', 'height' => '100px');
$config->outwarddelivery->editor->edit   = array('id' => 'content,env,introductionToFunctionsAndUses,remark,target,reason,changeContentAndMethod
            ,step,techniqueCheck,test,checkList,businessCooperateContent,judgePlan,effect,businessFunctionAffect,backupDataCenterChangeSyncDesc,emergencyManageAffect
            ,businessAffect,risk,controlTableSteps', 'tools' => 'modifycnccTools', 'height' => '100px');
$config->outwarddelivery->editor->copy   = array('id' => 'content,env,introductionToFunctionsAndUses,remark,target,reason,changeContentAndMethod
            ,step,techniqueCheck,test,checkList,businessCooperateContent,judgePlan,effect,businessFunctionAffect,backupDataCenterChangeSyncDesc,emergencyManageAffect
            ,businessAffect,risk,controlTableSteps', 'tools' => 'modifycnccTools', 'height' => '100px');
$config->outwarddelivery->editor->delete = array('id' => 'comment', 'tools' => 'modifycnccTools');
$config->outwarddelivery->editor->close  = array('id' => 'comment', 'tools' => 'modifycnccTools');
$config->outwarddelivery->editor->reject = array('id' => 'comment', 'tools' => 'modifycnccTools');
$config->outwarddelivery->editor      = new stdclass();
$config->outwarddelivery->list = new stdclass();
$config->outwarddelivery->list->exportFields = 'outwardDeliveryDesc,code,status,dealUser,createdBy,app,createdDepts,currentReview,isPayment,team,productName,productLine,productCode,
implementationForm,projectPlanId,CBPprojectId,problemId,demandId,requirementId,relatedTestingRequest,relatedProductEnroll,relatedModifycncc,testingRequestReturnTimes,
productEnrollReturnTimes,modifycnccReturnTimes,dealUserContact,createdDate,editedBy,editedDate,closedBy,closedDate,closedReason,revertReason,revertReasonChild,release,isMediaChanged,ROR,testSummary,testTarget,isCentralizedTest,
acceptanceTestType,currentStage,os,db,content,env,productenrollDesc,isPlan,planProductName,dynacommCn,dynacommEn,versionNum,lastVersionNum,checkDepartment,result,installationNode,softwareProductPatch,
softwareCopyrightRegistration,planDistributionTime,planUpTime,platform,reasonFromJinke,introductionToFunctionsAndUses,remark,desc,target,reason,changeContentAndMethod,step,techniqueCheck,
test,checkList,cooperateDepNameList,businessCooperateContent,judgeDep,judgePlan,controlTableFile,controlTableSteps,feasibilityAnalysis,risk,effect,businessFunctionAffect,
backupDataCenterChangeSyncDesc,emergencyManageAffect,changeImpactAnalysis,businessAffect,benchmarkVerificationType,verificationResults,feedBackId,operationName,feedBackOperationType,depOddName,
actualBegin,actualEnd,supply,changeNum,operationStaff,executionResults,result,internalSupply,problemDescription,resolveMethod,manufacturer,manufacturerConnect,secondorderId,urgentSource,urgentReason,changeForm,automationTools';
//,isMakeAmends,actualDeliveryTime

/* Search. */
global $lang,$app;
$app->loadLang('modify');
$app->loadLang('modifycncc');
$config->outwarddelivery->search['module'] = 'outwardDelivery';
$config->outwarddelivery->search['fields']['code']                      = $lang->outwarddelivery->code;
$config->outwarddelivery->search['fields']['status']                    = $lang->outwarddelivery->status;
$config->outwarddelivery->search['fields']['dealUser']                  = $lang->outwarddelivery->dealUser;
$config->outwarddelivery->search['fields']['createdBy']                 = $lang->outwarddelivery->createdBy;
$config->outwarddelivery->search['fields']['outwardDeliveryDesc']       = $lang->outwarddelivery->outwardDeliveryDesc;
$config->outwarddelivery->search['fields']['app']                       = $lang->outwarddelivery->app;
$config->outwarddelivery->search['fields']['urgentSource']              = $lang->outwarddelivery->urgentSource;
$config->outwarddelivery->search['fields']['createdDept']               = $lang->outwarddelivery->createdDepts;
$config->outwarddelivery->search['fields']['currentReview']             = $lang->outwarddelivery->currentReview;
$config->outwarddelivery->search['fields']['isPayment']                 = $lang->outwarddelivery->isPayment;
$config->outwarddelivery->search['fields']['team']                      = $lang->outwarddelivery->team;
$config->outwarddelivery->search['fields']['productId']                 = $lang->outwarddelivery->productName;
$config->outwarddelivery->search['fields']['productLine']               = $lang->outwarddelivery->productLine;
$config->outwarddelivery->search['fields']['productCode']               = $lang->outwarddelivery->productCode;
$config->outwarddelivery->search['fields']['implementationForm']        = $lang->outwarddelivery->implementationForm;
$config->outwarddelivery->search['fields']['projectPlanId']             = $lang->outwarddelivery->projectPlanId;
$config->outwarddelivery->search['fields']['CBPprojectId']              = $lang->outwarddelivery->CBPprojectId;
$config->outwarddelivery->search['fields']['problemId']                 = $lang->outwarddelivery->problemId;
$config->outwarddelivery->search['fields']['demandId']                  = $lang->outwarddelivery->demandId;
$config->outwarddelivery->search['fields']['requirementId']             = $lang->outwarddelivery->requirementId;
$config->outwarddelivery->search['fields']['testingRequestId']          = $lang->outwarddelivery->relatedTestingRequest;
$config->outwarddelivery->search['fields']['productEnrollId']           = $lang->outwarddelivery->relatedProductEnroll;
$config->outwarddelivery->search['fields']['modifycnccId']              = $lang->outwarddelivery->relatedModifycncc;
$config->outwarddelivery->search['fields']['testingRequestReturnTimes'] = $lang->outwarddelivery->testingRequestReturnTimes;
$config->outwarddelivery->search['fields']['productEnrollReturnTimes']  = $lang->outwarddelivery->productEnrollReturnTimes;
$config->outwarddelivery->search['fields']['modifycnccReturnTimes']     = $lang->outwarddelivery->modifycnccReturnTimes;
$config->outwarddelivery->search['fields']['contactTel']                = $lang->outwarddelivery->dealUserContact;
$config->outwarddelivery->search['fields']['createdDate']               = $lang->outwarddelivery->createdDate;
$config->outwarddelivery->search['fields']['editedBy']                  = $lang->outwarddelivery->editedBy;
$config->outwarddelivery->search['fields']['editedDate']                = $lang->outwarddelivery->editedDate;
$config->outwarddelivery->search['fields']['closedBy']                  = $lang->outwarddelivery->closedBy;
$config->outwarddelivery->search['fields']['closedDate']                = $lang->outwarddelivery->closedDate;
$config->outwarddelivery->search['fields']['closedReason']              = $lang->outwarddelivery->closedReason;
$config->outwarddelivery->search['fields']['revertReason']              = $lang->outwarddelivery->revertReason;
//$config->outwarddelivery->search['fields']['RevertReasonChild']         = $lang->outwarddelivery->revertReasonChild;
$config->outwarddelivery->search['fields']['release']                   = $lang->outwarddelivery->release;
$config->outwarddelivery->search['fields']['ifMediumChanges']           = $lang->outwarddelivery->isMediaChanged;
$config->outwarddelivery->search['fields']['manufacturer']           = $lang->outwarddelivery->manufacturer;
$config->outwarddelivery->search['fields']['manufacturerConnect']           = $lang->outwarddelivery->manufacturerConnect;
$config->outwarddelivery->search['fields']['secondorderId']           = $lang->outwarddelivery->secondorderId;
$config->outwarddelivery->search['fields']['changeForm']                = $lang->modifycncc->changeForm;
$config->outwarddelivery->search['fields']['automationTools']           = $lang->modifycncc->automationTools;
$config->outwarddelivery->search['fields']['implementModality']         = $lang->modifycncc->implementModality;

//$config->outwarddelivery->search['fields']['isMakeAmends']            = $lang->modify->isMakeAmends;

$config->outwarddelivery->search['params']['code']                      = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->outwarddelivery->search['params']['status']                    = array('operator' => '=', 'control' => 'select', 'values' => $lang->outwarddelivery->statusList);
$config->outwarddelivery->search['params']['dealUser']                  = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->outwarddelivery->search['params']['createdBy']                 = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->outwarddelivery->search['params']['outwardDeliveryDesc']       = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->outwarddelivery->search['params']['app']                       = array('operator' => 'include', 'control' => 'select', 'values' => '','mulit'=>true);
$config->outwarddelivery->search['params']['urgentSource']              = array('operator' => '=', 'control' => 'select', 'values' => '');
$config->outwarddelivery->search['params']['createdDept']               = array('operator' => '=', 'control' => 'select', 'values' => '');
$config->outwarddelivery->search['params']['currentReview']             = array('operator' => '=', 'control' => 'select', 'values' => $lang->outwarddelivery->currentReviewList);
$config->outwarddelivery->search['params']['isPayment']                 = array('operator' => '=', 'control' => 'select', 'values' => '');
$config->outwarddelivery->search['params']['team']                      = array('operator' => '=', 'control' => 'select', 'values' => '');
$config->outwarddelivery->search['params']['productId']                 = array('operator' => 'include', 'control' => 'select', 'values' => '');
$config->outwarddelivery->search['params']['productLine']               = array('operator' => 'include', 'control' => 'select', 'values' => '');
$config->outwarddelivery->search['params']['productCode']               = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->outwarddelivery->search['params']['implementationForm']        = array('operator' => '=', 'control' => 'select', 'values' => $lang->outwarddelivery->implementationFormList);
$config->outwarddelivery->search['params']['projectPlanId']             = array('operator' => 'include', 'control' => 'select', 'values' => '');
$config->outwarddelivery->search['params']['CBPprojectId']              = array('operator' => 'include', 'control' => 'select', 'values' => '');
$config->outwarddelivery->search['params']['problemId']                 = array('operator' => 'include', 'control' => 'select', 'values' => '','mulit'=>true);
$config->outwarddelivery->search['params']['demandId']                  = array('operator' => 'include', 'control' => 'select', 'values' => '','mulit'=>true);
$config->outwarddelivery->search['params']['requirementId']             = array('operator' => 'include', 'control' => 'select', 'values' => '','mulit'=>true);
$config->outwarddelivery->search['params']['testingRequestId']          = array('operator' => '=', 'control' => 'select', 'values' => '');
$config->outwarddelivery->search['params']['productEnrollId']           = array('operator' => '=', 'control' => 'select', 'values' => '');
$config->outwarddelivery->search['params']['modifycnccId']              = array('operator' => '=', 'control' => 'select', 'values' => '');
$config->outwarddelivery->search['params']['testingRequestReturnTimes'] = array('operator' => '=', 'control' => 'input', 'values' => '');
$config->outwarddelivery->search['params']['productEnrollReturnTimes']  = array('operator' => '=', 'control' => 'input', 'values' => '');
$config->outwarddelivery->search['params']['modifycnccReturnTimes']     = array('operator' => '=', 'control' => 'input', 'values' => '');
$config->outwarddelivery->search['params']['contactTel']                = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->outwarddelivery->search['params']['createdDate']               = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->outwarddelivery->search['params']['editedBy']                  = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->outwarddelivery->search['params']['editedDate']                = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->outwarddelivery->search['params']['closedBy']                  = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->outwarddelivery->search['params']['closedDate']                = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->outwarddelivery->search['params']['closedReason']              = array('operator' => 'include', 'control' => 'select', 'values' =>  $lang->outwarddelivery->closedReasonList);
$config->outwarddelivery->search['params']['revertReason']              = array('operator' => 'include', 'control' => 'select', 'values' => '');
//$config->outwarddelivery->search['params']['RevertReasonChild']         = array('operator' => 'include', 'control' => 'select', 'values' => '');
$config->outwarddelivery->search['params']['release']                   = array('operator' => 'include', 'control' => 'select', 'values' => '');
$config->outwarddelivery->search['params']['ifMediumChanges']            = array('operator' => 'include', 'control' => 'select', 'values' => $lang->outwarddelivery->isMediaChangedList);
$config->outwarddelivery->search['params']['manufacturer']                      = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->outwarddelivery->search['params']['manufacturerConnect']                      = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->outwarddelivery->search['params']['secondorderId']                 = array('operator' => 'include', 'control' => 'select', 'values' => '','mulit'=>true);
$config->outwarddelivery->search['params']['changeForm']                 = array('operator' => 'include', 'control' => 'select', 'values' => $lang->modifycncc->changeFormList,'mulit'=>true);
$config->outwarddelivery->search['params']['automationTools']                 = array('operator' => 'include', 'control' => 'select', 'values' => $lang->modifycncc->automationToolsList,'mulit'=>true);
$config->outwarddelivery->search['params']['implementModality']         = array('operator' => 'include', 'control' => 'select', 'values' => '');

//$config->outwarddelivery->search['params']['isMakeAmends']                 = array('operator' => 'include', 'control' => 'select', 'values' => $lang->modify->isMakeAmendsList);