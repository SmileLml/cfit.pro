<?php
$config->infoqz = new stdclass();
$config->infoqz->create = new stdclass();
$config->infoqz->edit   = new stdclass();
$config->infoqz->copy   = new stdclass();
$config->infoqz->change = new stdclass();
$config->infoqz->link   = new stdclass();
$config->infoqz->create->requiredFields = 'fixType,classify,gainType,deliveryType,app,checkList,step,test,endDate,type,source,reason,title,project,createUserPhone,systemType,isTest,desensitization,content,operation,isNPC,gainNode,node,desc,purpose';
$config->infoqz->edit->requiredFields   = $config->infoqz->create->requiredFields;
$config->infoqz->copy->requiredFields   = $config->infoqz->create->requiredFields;
$config->infoqz->change->requiredFields = 'reviewer';
$config->infoqz->link->requiredFields   = 'consumed,release';
//审核节点默认选中
$config->infoqz->create->setDefChosenReviewNodes = array(0, 1, 2, 3, 5);

$config->infoqz->editor = new stdclass();
$config->infoqz->editor->create   = array('id' => 'purpose,operation,reason,test,step,checkList,desensitization,content', 'tools' => 'simpleTools2');
$config->infoqz->editor->copy     = array('id' => 'purpose,operation,reason,test,step,checkList,desensitization,content', 'tools' => 'simpleTools2');
$config->infoqz->editor->edit     = array('id' => 'purpose,operation,reason,test,step,checkList,desensitization,content', 'tools' => 'simpleTools2');
$config->infoqz->editor->confirm  = array('id' => 'conclusion,comment', 'tools' => 'simpleTools');
$config->infoqz->editor->feedback = array('id' => 'reason,solution,progress', 'tools' => 'simpleTools');
$config->infoqz->editor->view     = array('id' => 'comment,lastComment', 'tools' => 'simpleTools');
$config->infoqz->editor->review   = array('id' => 'comment', 'tools' => 'simpleTools');
$config->infoqz->editor->link     = array('id' => 'comment', 'tools' => 'simpleTools');
$config->infoqz->editor->close    = array('id' => 'comment', 'tools' => 'simpleTools');
$config->infoqz->editor->delete   = array('id' => 'comment', 'tools' => 'simpleTools'); 
$config->infoqz->editor->run      = array('id' => 'comment', 'tools' => 'simpleTools');

$config->infoqz->list = new stdclass();
$config->infoqz->list->exportGainFields = 'code,status,app,dataSystem,createdBy,createdDept,dealUser,isJinke,desensitizationType,deadline,externalId,externalStatus,type,
classify,isNPC,gainNode,gainType,createUserPhone,planBegin,planEnd,actualBegin,actualEnd,isPayment,team,systemType,
fixType,project,problem,demand,isTest,createdDate,editedBy,editedDate,supply,gainDesc,gainReason,gainPurpose,test,
content,operation,step,desensitization,externalRejectReason,gainResult,secondorderId,revertReason,revertReasonChild,dataCollectApplyCompany,demandUnitOrDep,demandUser,demandUserPhone,
demandUserEmail,portUser,portUserPhone,portUserEmail,supportUser,supportUserPhone,supportUserEmail';

$config->infoqz->list->exportFixFields ='code,status,app,createdBy,createdDept,dealUser,externalId,externalStatus,type,
classify,isNPC,gainNode,gainType,createUserPhone,planBegin,planEnd,actualBegin,actualEnd,isPayment,team,systemType,
fixType,project,problem,demand,isTest,createdDate,editedBy,editedDate,supply,gainDesc,gainReason,gainPurpose,test,
content,operation,step,desensitization,externalRejectReason,gainResult,secondorderId';

/* Search. */
global $lang;
$config->infoqz->search['module'] = 'infoqz';
$config->infoqz->search['fields']['code']        = $lang->infoqz->code;
$config->infoqz->search['fields']['status']      = $lang->infoqz->status;
$config->infoqz->search['fields']['app']         = $lang->infoqz->app;
$config->infoqz->search['fields']['dataSystem']  = $lang->infoqz->dataSystem;
$config->infoqz->search['fields']['createdBy']   = $lang->infoqz->createdBy;
$config->infoqz->search['fields']['createdDept'] = $lang->infoqz->createdDept;
$config->infoqz->search['fields']['isJinke']     = $lang->infoqz->isJinke;
$config->infoqz->search['fields']['desensitizationType'] = $lang->infoqz->desensitizationType;
$config->infoqz->search['fields']['deadline']    = $lang->infoqz->deadline;
//$config->infoqz->search['fields']['dealUser']     = $lang->infoqz->dealUser;
$config->infoqz->search['fields']['externalId']     = $lang->infoqz->externalId;
$config->infoqz->search['fields']['externalStatus'] = $lang->infoqz->externalStatus;
$config->infoqz->search['fields']['type']        = $lang->infoqz->type;
$config->infoqz->search['fields']['classify']    = $lang->infoqz->classify;
$config->infoqz->search['fields']['isNPC']    = $lang->infoqz->isNPC;
$config->infoqz->search['fields']['node']    = $lang->infoqz->gainNode;
$config->infoqz->search['fields']['gainType']    = $lang->infoqz->gainType;
$config->infoqz->search['fields']['createUserPhone']    = $lang->infoqz->createUserPhone;
$config->infoqz->search['fields']['planBegin']   = $lang->infoqz->planBegin;
$config->infoqz->search['fields']['planEnd']     = $lang->infoqz->planEnd;
$config->infoqz->search['fields']['actualBegin'] = $lang->infoqz->actualBegin;
$config->infoqz->search['fields']['actualEnd']   = $lang->infoqz->actualEnd;
$config->infoqz->search['fields']['isPayment']   = $lang->infoqz->isPayment;
//$config->infoqz->search['fields']['team']   = $lang->infoqz->team;
$config->infoqz->search['fields']['systemType']   = $lang->infoqz->systemType;
$config->infoqz->search['fields']['fixType']     = $lang->infoqz->fixType;
$config->infoqz->search['fields']['project']     = $lang->infoqz->project;
$config->infoqz->search['fields']['problem']     = $lang->infoqz->problem;
$config->infoqz->search['fields']['demand']     = $lang->infoqz->demand;
$config->infoqz->search['fields']['isTest']     = $lang->infoqz->isTest;
$config->infoqz->search['fields']['createdDate'] = $lang->infoqz->createdDate;
$config->infoqz->search['fields']['editedBy']    = $lang->infoqz->editedBy;
$config->infoqz->search['fields']['editedDate']  = $lang->infoqz->editedDate;
$config->infoqz->search['fields']['supply']      = $lang->infoqz->supply;
$config->infoqz->search['fields']['desc']      = $lang->infoqz->gainDesc;
$config->infoqz->search['fields']['reason']      = $lang->infoqz->gainReason;
$config->infoqz->search['fields']['purpose']      = $lang->infoqz->gainPurpose;
$config->infoqz->search['fields']['test']      = $lang->infoqz->test;
$config->infoqz->search['fields']['content']      = $lang->infoqz->content;
$config->infoqz->search['fields']['operation']      = $lang->infoqz->operation;
$config->infoqz->search['fields']['step']      = $lang->infoqz->step;
$config->infoqz->search['fields']['desensitization']      = $lang->infoqz->desensitization;
$config->infoqz->search['fields']['externalRejectReason']      = $lang->infoqz->externalRejectReason;
$config->infoqz->search['fields']['result']      = $lang->infoqz->gainResult;
$config->infoqz->search['fields']['secondorderId']      = $lang->infoqz->secondorderId;
$config->infoqz->search['fields']['revertReason']      = $lang->infoqz->revertReason;
$config->infoqz->search['fields']['dataCollectApplyCompany']      = $lang->infoqz->dataCollectApplyCompany;
$config->infoqz->search['fields']['demandUnitOrDep']              = $lang->infoqz->demandUnitOrDep;
$config->infoqz->search['fields']['demandUser']                   = $lang->infoqz->demandUser;
$config->infoqz->search['fields']['demandUserPhone']              = $lang->infoqz->demandUserPhone;
$config->infoqz->search['fields']['demandUserEmail']              = $lang->infoqz->demandUserEmail;
$config->infoqz->search['fields']['portUser']                     = $lang->infoqz->portUser;
$config->infoqz->search['fields']['portUserPhone']                = $lang->infoqz->portUserPhone;
$config->infoqz->search['fields']['portUserEmail']                = $lang->infoqz->portUserEmail;
$config->infoqz->search['fields']['supportUser']                  = $lang->infoqz->supportUser;
$config->infoqz->search['fields']['supportUserPhone']             = $lang->infoqz->supportUserPhone;
$config->infoqz->search['fields']['supportUserEmail']             = $lang->infoqz->supportUserEmail;


$config->infoqz->search['params']['code']        = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->infoqz->search['params']['status']      = array('operator' => '=', 'control' => 'select', 'values' => '');
$config->infoqz->search['params']['app']         = array('operator' => 'include', 'control' => 'select', 'values' => array('' => ''),'mulit'=>true);
$config->infoqz->search['params']['dataSystem']  = array('operator' => 'include', 'control' => 'select', 'values' => array('' => ''),'mulit'=>true);
$config->infoqz->search['params']['createdBy']   = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->infoqz->search['params']['createdDept'] = array('operator' => '=', 'control' => 'select', 'values' => array(0 => ''));
$config->infoqz->search['params']['isJinke']     = array('operator' => '=', 'control' => 'select', 'values' => $lang->infoqz->isJinkeList);
$config->infoqz->search['params']['desensitizationType'] = array('operator' => '=', 'control' => 'select', 'values' => $lang->infoqz->desensitizationTypeList);
$config->infoqz->search['params']['deadline']    = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');
//$config->infoqz->search['params']['dealUser']    = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->infoqz->search['params']['externalId']     = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->infoqz->search['params']['externalStatus'] = array('operator' => '=', 'control' => 'select', 'values' => $lang->infoqz->externalStatusList);
$config->infoqz->search['params']['type']        = array('operator' => '=', 'control' => 'select', 'values' => $lang->infoqz->typeList);
$config->infoqz->search['params']['classify']    = array('operator' => 'include', 'control' => 'select', 'values' => $lang->infoqz->techList);
$config->infoqz->search['params']['isNPC']       = array('operator' => 'include', 'control' => 'select', 'values' => $lang->infoqz->isNPCList);
$config->infoqz->search['params']['node']    = array('operator' => 'include', 'control' => 'select', 'values' => $this->lang->infoqz->gainNodeNPCList + $this->lang->infoqz->gainNodeCNCCList);
$config->infoqz->search['params']['gainType']    = array('operator' => 'include', 'control' => 'select', 'values' => $lang->infoqz->gainTypeList);
$config->infoqz->search['params']['createUserPhone']    = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->infoqz->search['params']['planBegin']   = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->infoqz->search['params']['planEnd']     = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->infoqz->search['params']['actualBegin'] = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->infoqz->search['params']['actualEnd']   = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->infoqz->search['params']['isPayment']   = array('operator' => 'include', 'control' => 'select', 'values' => array(0 => ''));
//$config->infoqz->search['params']['team']        = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->infoqz->search['params']['systemType']  = array('operator' => 'include', 'control' => 'select', 'values' => $lang->infoqz->systemTypeList);
$config->infoqz->search['params']['fixType']     = array('operator' => 'include', 'control' => 'select', 'values' => $lang->infoqz->fixTypeList);
$config->infoqz->search['params']['project']     = array('operator' => '=', 'control' => 'select','values' => array(0 => ''));
$config->infoqz->search['params']['problem']     = array('operator' => '=', 'control' => 'select','values' => array(0 => ''),'mulit'=>true);
$config->infoqz->search['params']['demand']      = array('operator' => '=', 'control' => 'select','values' => array(0 => ''),'mulit'=>true);
$config->infoqz->search['params']['isTest']      = array('operator' => 'include', 'control' => 'select', 'values' => $lang->infoqz->isTestList);
$config->infoqz->search['params']['createdDate'] = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->infoqz->search['params']['editedBy']    = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->infoqz->search['params']['editedDate']  = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->infoqz->search['params']['supply']      = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->infoqz->search['params']['desc']    = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->infoqz->search['params']['reason']  = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->infoqz->search['params']['purpose'] = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->infoqz->search['params']['test']        = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->infoqz->search['params']['content']     = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->infoqz->search['params']['operation']   = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->infoqz->search['params']['step']        = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->infoqz->search['params']['desensitization']  = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->infoqz->search['params']['externalRejectReason']  = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->infoqz->search['params']['result']  = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->infoqz->search['params']['secondorderId']     = array('operator' => 'include', 'control' => 'select','values' => array(0 => ''));
$config->infoqz->search['params']['revertReason']     = array('operator' => 'include', 'control' => 'select', 'values' => '');
$config->infoqz->search['params']['dataCollectApplyCompany']   = array('operator' => 'include', 'control' => 'select', 'values' => $this->lang->infoqz->demandUnitTypeList);
$config->infoqz->search['params']['demandUnitOrDep']           = array('operator' => 'include', 'control' => 'select', 'values' => []);
$config->infoqz->search['params']['demandUser']                = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->infoqz->search['params']['demandUserPhone']           = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->infoqz->search['params']['demandUserEmail']           = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->infoqz->search['params']['portUser']                  = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->infoqz->search['params']['portUserPhone']             = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->infoqz->search['params']['portUserEmail']             = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->infoqz->search['params']['supportUser']               = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->infoqz->search['params']['supportUserPhone']          = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->infoqz->search['params']['supportUserEmail']          = array('operator' => 'include', 'control' => 'input', 'values' => '');
