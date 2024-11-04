<?php
$config->credit = new stdclass();
$config->credit->objectType = "credit";
$config->credit->create = new stdclass();
$config->credit->edit   = new stdclass();
$config->credit->copy   = new stdclass();
$config->credit->cancel = new stdclass();
$config->credit->delete = new stdclass();
$config->credit->list = new stdclass();
$config->credit->review = new stdclass();

$config->credit->create->requiredFields = 'appIds,implementationForm,projectPlanId,level,changeNode,mode,type,changeSource,executeMode,emergencyType,isBusinessAffect,planBeginTime,planEndTime,summary,desc,techniqueCheck,feasibilityAnalysis,riskAnalysisEmergencyHandle,productAffect,businessAffect';
$config->credit->edit->requiredFields = $config->credit->create->requiredFields;
$config->credit->copy->requiredFields = $config->credit->create->requiredFields;
$config->credit->review->requiredFields = 'svnUrl,onLineFile,dealResult,status';
//,isMakeAmends,actualDeliveryTime
$config->credit->list->exportFields = 'code,summary,appIds,productIds,implementationForm,projectPlanId,status,dealUsers,createdBy,createdDate,createdDept,secondorderIds,problemIds,demandIds,abnormalId,level,changeNode,changeSource,mode,type,executeMode,emergencyType,isBusinessAffect,planBeginTime,planEndTime,desc,techniqueCheck,feasibilityAnalysis,riskAnalysisEmergencyHandle,productAffect,businessAffect';

$config->credit->editor = new stdclass();
$config->credit->editor->create = array('id' => '', 'tools' => 'simpleTools');
$config->credit->editor->edit   = array('id' => '', 'tools' => 'simpleTools');
$config->credit->editor->delete = array('id' => 'remark', 'tools' => 'simpleTools');
$config->credit->editor->cancel = array('id' => 'cancelReason', 'tools' => 'simpleTools');
$config->credit->editor->submit = array('id' => 'comment', 'tools' => 'simpleTools');
$config->credit->editor->review = array('id' => '', 'tools' => 'simpleTools');
$config->credit->editor->editsecondordercancellinkage = array('id' => 'comment', 'tools' => 'simpleTools');

/**
 * 确认变更结果用户
 */
$config->credit->confirmResultUsers = '';

/*
 * 多选下拉字段
 */
$config->credit->multipleSelectFields = ['appIds','productIds','secondorderIds','problemIds','demandIds','changeNode','type','changeSource','executeMode'];


/* Search. */
global $lang,$app;
$app->loadLang('modify');
$config->credit->search['module'] = 'credit';

$config->credit->search['fields']['code']                 = $lang->credit->code;
$config->credit->search['fields']['summary']             = $lang->credit->summary;
$config->credit->search['fields']['appIds']               = $lang->credit->appIds;
$config->credit->search['fields']['productIds']          = $lang->credit->productIds;
$config->credit->search['fields']['implementationForm'] = $lang->credit->implementationForm;
$config->credit->search['fields']['projectPlanId']       = $lang->credit->projectPlanId;
$config->credit->search['fields']['status']               = $lang->credit->status;
$config->credit->search['fields']['dealUsers']           = $lang->credit->dealUsers;
$config->credit->search['fields']['createdBy']           = $lang->credit->createdBy;
$config->credit->search['fields']['createdDate']         = $lang->credit->createdDate;
$config->credit->search['fields']['createdDept']         = $lang->credit->createdDept;

$config->credit->search['fields']['secondorderIds'] = $lang->credit->secondorderIds;
$config->credit->search['fields']['problemIds']     = $lang->credit->problemIds;
$config->credit->search['fields']['demandIds']      = $lang->credit->demandIds;
$config->credit->search['fields']['abnormalId']     = $lang->credit->abnormalId;
$config->credit->search['fields']['level']           = $lang->credit->level;
$config->credit->search['fields']['changeNode']     = $lang->credit->changeNode;
$config->credit->search['fields']['changeSource']   = $lang->credit->changeSource;
$config->credit->search['fields']['mode']            = $lang->credit->mode;
$config->credit->search['fields']['type']            = $lang->credit->type;

$config->credit->search['fields']['executeMode']       = $lang->credit->executeMode;
$config->credit->search['fields']['emergencyType']     = $lang->credit->emergencyType;
$config->credit->search['fields']['isBusinessAffect']  = $lang->credit->isBusinessAffect;
$config->credit->search['fields']['planBeginTime']     = $lang->credit->planBeginTime;
$config->credit->search['fields']['planEndTime']       = $lang->credit->planEndTime;
$config->credit->search['fields']['desc']               = $lang->credit->desc;
$config->credit->search['fields']['techniqueCheck']       = $lang->credit->techniqueCheck;
$config->credit->search['fields']['feasibilityAnalysis'] = $lang->credit->feasibilityAnalysis;
$config->credit->search['fields']['riskAnalysisEmergencyHandle'] = $lang->credit->riskAnalysisEmergencyHandle;
$config->credit->search['fields']['productAffect']                 = $lang->credit->productAffect;
$config->credit->search['fields']['businessAffect']                = $lang->credit->businessAffect;
//$config->credit->search['fields']['isMakeAmends']                = $lang->modify->isMakeAmends;

$config->credit->search['params']['code']         = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->credit->search['params']['summary']      = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->credit->search['params']['appIds']       = array('operator' => 'include', 'control' => 'select', 'values' => '');
$config->credit->search['params']['productIds']  = array('operator' => 'include', 'control' => 'select', 'values' => '');
$config->credit->search['params']['implementationForm'] = array('operator' => '=', 'control' => 'select', 'values' => $lang->credit->implementationFormList);
$config->credit->search['params']['projectPlanId']  = array('operator' => '=', 'control' => 'select', 'values' => '');
$config->credit->search['params']['status']  = array('operator' => '=', 'control' => 'select', 'values' => array(0 => '') + $lang->credit->statusList);
$config->credit->search['params']['dealUsers']  = array('operator' => 'include', 'control' => 'select', 'values' =>  [], 'mulit'=>true);
$config->credit->search['params']['createdBy']  = array('operator' => '=', 'control' => 'select', 'values' => []);
$config->credit->search['params']['createdDate']   = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->credit->search['params']['createdDept']   = array('operator' => '=', 'control' => 'select', 'values' => 'deptList');

$config->credit->search['params']['secondorderIds']  =  array('operator' => 'include', 'control' => 'select', 'values' => array(0 => ''), 'mulit'=>true);
$config->credit->search['params']['problemIds']      = array('operator' => 'include', 'control' => 'select', 'values' => array(0 => ''), 'mulit'=>true);
$config->credit->search['params']['demandIds']       = array('operator' => 'include', 'control' => 'select', 'values' => array(0 => ''), 'mulit'=>true);
$config->credit->search['params']['abnormalId']      = array('operator' => 'include', 'control' => 'select', 'values' => array(0 => ''), 'mulit'=>true);

$config->credit->search['params']['level']          = array('operator' => '=', 'control' => 'select', 'values' => $lang->credit->levelList);
$config->credit->search['params']['changeNode']    = array('operator' => 'include', 'control' => 'select', 'values' => $lang->credit->changeNodeList, 'mulit' => true);
$config->credit->search['params']['changeSource']  = array('operator' => 'include', 'control' => 'select', 'values' => $lang->credit->changeSourceList, 'mulit' => true);
$config->credit->search['params']['mode']           = array('operator' => '=', 'control' => 'select', 'values' => $lang->credit->modeList);
$config->credit->search['params']['type']           = array('operator' => 'include', 'control' => 'select', 'values' => $lang->credit->typeList, 'mulit' => true);

$config->credit->search['params']['executeMode']        = array('operator' => 'include', 'control' => 'select', 'values' => $lang->credit->executeModeList, 'mulit' => true);
$config->credit->search['params']['emergencyType']     = array('operator' => 'include', 'control' => 'select', 'values' => [], 'mulit'=>true);
$config->credit->search['params']['isBusinessAffect']  = array('operator' => 'include', 'control' => 'select', 'values' => [], 'mulit'=>true);
$config->credit->search['params']['planBeginTime']  = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->credit->search['params']['planEndTime']  = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');

$config->credit->search['params']['desc']  = array('operator' => 'include', 'control' => 'input',  'values' => '');
$config->credit->search['params']['techniqueCheck']  = array('operator' => 'include', 'control' => 'input',  'values' => '');
$config->credit->search['params']['feasibilityAnalysis']  = array('operator' => 'include', 'control' => 'input',  'values' => '');
$config->credit->search['params']['riskAnalysisEmergencyHandle']  = array('operator' => 'include', 'control' => 'input',  'values' => '');
$config->credit->search['params']['productAffect']  = array('operator' => 'include', 'control' => 'input',  'values' => '');
$config->credit->search['params']['businessAffect']  = array('operator' => 'include', 'control' => 'input',  'values' => '');
//$config->credit->search['params']['isMakeAmends']  = array('operator' => 'include', 'control' => 'select',  'values' => $lang->modify->isMakeAmendsList);
