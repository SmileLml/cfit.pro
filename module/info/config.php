<?php
$config->info = new stdclass();
$config->info->create = new stdclass();
$config->info->edit   = new stdclass();
$config->info->copy   = new stdclass();
$config->info->change = new stdclass();
$config->info->link   = new stdclass();
$config->info->create->requiredFields = 'fixType,classify,app,checkList,step,test,desc,endDate,type,source,reason,title,project';
$config->info->edit->requiredFields   = $config->info->create->requiredFields;
$config->info->copy->requiredFields   = $config->info->create->requiredFields;
$config->info->change->requiredFields = 'reviewer';
$config->info->link->requiredFields   = 'release';

//审核节点默认选中
$config->info->create->setDefChosenReviewNodes = array(0, 1, 2, 3, 5, 6);

$config->info->editor = new stdclass();
$config->info->editor->create   = array('id' => 'desc,purpose,operation,reason,test,step,checkList', 'tools' => 'simpleTools');
$config->info->editor->copy   = array('id' => 'desc,purpose,operation,reason,test,step,checkList', 'tools' => 'simpleTools');
$config->info->editor->edit     = array('id' => 'desc,purpose,operation,reason,test,step,checkList', 'tools' => 'simpleTools');
$config->info->editor->confirm  = array('id' => 'conclusion,comment', 'tools' => 'simpleTools');
$config->info->editor->feedback = array('id' => 'reason,solution,progress', 'tools' => 'simpleTools');
$config->info->editor->view     = array('id' => 'comment,lastComment', 'tools' => 'simpleTools');
$config->info->editor->review   = array('id' => 'comment', 'tools' => 'simpleTools');
$config->info->editor->run      = array('id' => 'comment,result', 'tools' => 'simpleTools');
$config->info->editor->link     = array('id' => 'comment', 'tools' => 'simpleTools');
$config->info->editor->close    = array('id' => 'comment', 'tools' => 'simpleTools');
$config->info->editor->delete   = array('id' => 'comment', 'tools' => 'simpleTools');

$config->info->list = new stdclass();
$config->info->list->exportGainFields = 'code,status,fetchResult,type,fixType,classify,isJinke,desensitizationType,deadline,dealUser,app,isPayment,planBegin,planEnd,gainDesc,gainReason,test,gainStep,checkList,createdBy,createdDept,createdDate,editedBy,editedDate,actualBegin,actualEnd,supply,project,problem,demand,secondorderId,release,revertReason,revertReasonChild';

$config->info->list->exportFixFields = 'code,status,type,fixType,classify,dealUser,app,isPayment,planBegin,planEnd,fixNode,fixDesc,fixReason,operation,test,fixStep,checkList,fixResult,createdBy,createdDept,createdDate,editedBy,editedDate,actualBegin,actualEnd,supply,project,problem,demand,release';

/* Search. */
global $lang;
$config->info->search['module'] = 'info';
$config->info->search['fields']['code']        = $lang->info->code;
$config->info->search['fields']['status']      = $lang->info->status;
$config->info->search['fields']['app']         = $lang->info->app;
$config->info->search['fields']['createdBy']   = $lang->info->createdBy;
$config->info->search['fields']['createdDept'] = $lang->info->createdDept;
$config->info->search['fields']['createdDate'] = $lang->info->createdDate;
$config->info->search['fields']['isJinke']     = $lang->info->isJinke;
$config->info->search['fields']['secondorderId']     = $lang->info->secondorderId;
$config->info->search['fields']['desensitizationType'] = $lang->info->desensitizationType;
$config->info->search['fields']['deadline']    = $lang->info->deadline;
$config->info->search['fields']['fixType']     = $lang->info->fixType;
$config->info->search['fields']['isPayment']   = $lang->info->isPayment;
$config->info->search['fields']['type']        = $lang->info->type;
$config->info->search['fields']['planBegin']   = $lang->info->planBegin;
$config->info->search['fields']['planEnd']     = $lang->info->planEnd;
$config->info->search['fields']['actualBegin'] = $lang->info->actualBegin;
$config->info->search['fields']['actualEnd']   = $lang->info->actualEnd;
$config->info->search['fields']['project']     = $lang->info->project;
$config->info->search['fields']['editedBy']    = $lang->info->editedBy;
$config->info->search['fields']['editedDate']  = $lang->info->editedDate;
$config->info->search['fields']['supply']      = $lang->info->supply;
$config->info->search['fields']['classify']    = $lang->info->classify;
$config->info->search['fields']['revertReason']    = $lang->info->revertReason;

$config->info->search['params']['code']        = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->info->search['params']['status']      = array('operator' => '=', 'control' => 'select', 'values' => $lang->info->statusList);
$config->info->search['params']['app']         = array('operator' => 'include', 'control' => 'select', 'values' => array(0 => ''));
$config->info->search['params']['createdBy']   = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->info->search['params']['createdDept'] = array('operator' => '=', 'control' => 'select', 'values' => array(0 => ''));
$config->info->search['params']['createdDate'] = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->info->search['params']['isJinke']     = array('operator' => '=', 'control' => 'select', 'values' => $lang->info->isJinkeList);
$config->info->search['params']['secondorderId']     = array('operator' => 'include', 'control' => 'select', 'values' => '');
$config->info->search['params']['desensitizationType'] = array('operator' => '=', 'control' => 'select', 'values' => $lang->info->desensitizationTypeList);
$config->info->search['params']['deadline']    = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->info->search['params']['fixType']     = array('operator' => '=', 'control' => 'select', 'values' => $lang->info->fixTypeList);
$config->info->search['params']['isPayment']   = array('operator' => 'include', 'control' => 'select', 'values' => array(0 => ''));
$config->info->search['params']['type']        = array('operator' => '=', 'control' => 'select', 'values' => $lang->info->typeList);
$config->info->search['params']['planBegin']   = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->info->search['params']['planEnd']     = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->info->search['params']['actualBegin'] = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->info->search['params']['actualEnd']   = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->info->search['params']['project']     = array('operator' => '=', 'control' => 'select','values' => array(0 => ''));
$config->info->search['params']['editedBy']    = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->info->search['params']['editedDate']  = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->info->search['params']['supply']      = array('operator' => 'include', 'control' => 'select', 'values' => 'users');
$config->info->search['params']['classify']    = array('operator' => 'include', 'control' => 'select', 'values' => $lang->info->techList);
$config->info->search['params']['revertReason']     = array('operator' => 'include', 'control' => 'select', 'values' => '');
