<?php

$config->secondorder         = new stdclass();
$config->secondorder->create = new stdclass();
$config->secondorder->copy   = new stdclass();
$config->secondorder->edit   = new stdclass();
$config->secondorder->deal   = new stdclass();
$config->secondorder->close  = new stdclass();
$config->secondorder->delete = new stdclass();
$config->secondorder->editfinallyhandover = new stdclass();

$config->secondorder->create->requiredFields = 'summary,type,subtype,source,team,dealUser,app,desc,contacts,sourceBackground,taskIdentification';
$config->secondorder->copy->requiredFields   = 'summary,type,subtype,source,team,dealUser,app,desc,contacts,sourceBackground,taskIdentification';
$config->secondorder->edit->requiredFields   = 'summary,type,subtype,source,team,dealUser,app,desc,contacts,sourceBackground,taskIdentification';
$config->secondorder->deal->requiredFields   = 'app,planstartDate,planoverDate,implementationForm,internalProject,taskIdentification';
$config->secondorder->close->requiredFields  = 'comment,closeReason';
$config->secondorder->delete->requiredFields = 'comment';
$config->secondorder->editfinallyhandover->requiredFields = 'finallyHandOver';

$config->secondorder->editor                 = new stdclass();
$config->secondorder->editor->create         = ['id' => 'comment,desc', 'tools' => 'simpleTools'];
$config->secondorder->editor->copy           = ['id' => 'comment,desc', 'tools' => 'simpleTools'];
$config->secondorder->editor->edit           = ['id' => 'comment,desc', 'tools' => 'simpleTools'];
$config->secondorder->editor->deal           = ['id' => 'comment,consultRes,testRes,dealRes', 'tools' => 'simpleTools'];
$config->secondorder->editor->close          = ['id' => 'comment', 'tools' => 'simpleTools'];
$config->secondorder->editor->delete         = ['id' => 'comment', 'tools' => 'simpleTools'];
$config->secondorder->editor->editassignedto = ['id' => 'comment', 'tools' => 'simpleTools'];
$config->secondorder->editor->editfinallyhandover = ['id' => 'comment', 'tools' => 'simpleTools'];

$config->secondorder->export             = new stdclass();
$config->secondorder->export->listFields = [
    'code', 'status', 'dealUser', 'summary', 'id', 'type', 'subtype', 'app', 'source', 'createdDept', 'acceptUser',
    'acceptDept', 'team,union', 'exceptDoneDate', 'closeReason', 'ifAccept', 'createdBy', 'createdDate', 'editedBy',
    'editedDate', 'closedBy', 'closedDate', 'planstartDate', 'planoverDate', 'startDate', 'overDate', 'progress',
    'consultRes', 'testRes', 'dealRes', 'desc', 'ccList', 'completeStatus', 'sourceBackground',
    'cbpProject', 'contacts', 'contactsPhone', 'ifReceived', 'notReceiveReason', 'acceptanceCondition',
    'completionDescription', 'implementationForm', 'internalProject', 'rejectUser',
    'rejectReason', 'externalCode', 'sourcePlatform', 'taskIdentification', 'notAcceptReason', 'externalStatus', 'externalTime', 'handoverMethod','requestCategory','callUnit','callUnitPhone','urgencyLevel'
];

$config->secondorder->list               = new stdclass();
$config->secondorder->list->exportFields = '
code,status,dealUser,summary,id,type,subtype,app,source,createdDept,acceptUser,acceptDept,team,union,exceptDoneDate,
closeReason,ifAccept,createdBy,createdDate,editedBy,editedDate,closedBy,closedDate,planstartDate,planoverDate,startDate,
overDate,progress,consultRes,testRes,dealRes,desc,cc,completeStatus,sourceBackground,cbpProject,
contacts,contactsPhone,ifReceived,notReceiveReason,acceptanceCondition,completionDescription,implementationForm,
internalProject,rejectUser,rejectReason,externalCode,sourcePlatform,taskIdentification,notAcceptReason,externalStatus,
externalTime,handoverMethod,requestCategory,callUnit,callUnitPhone,urgencyLevel
';

$config->secondorder->import = new stdclass();

$config->secondorder->showImport = new stdclass();

// Search.
global $lang;
$config->secondorder->search['module']                       = 'secondorder';
$config->secondorder->search['fields']['code']               = $lang->secondorder->code;
$config->secondorder->search['fields']['status']             = $lang->secondorder->status;
$config->secondorder->search['fields']['dealUser']           = $lang->secondorder->dealUser;
$config->secondorder->search['fields']['summary']            = $lang->secondorder->summary;
$config->secondorder->search['fields']['id']                 = $lang->secondorder->id;
$config->secondorder->search['fields']['ifAccept']           = $lang->secondorder->ifAccept;
$config->secondorder->search['fields']['type']               = $lang->secondorder->type;
$config->secondorder->search['fields']['subtype']            = $lang->secondorder->subtype;
$config->secondorder->search['fields']['source']             = $lang->secondorder->source;
$config->secondorder->search['fields']['team']               = $lang->secondorder->team;
$config->secondorder->search['fields']['union']              = $lang->secondorder->union;
$config->secondorder->search['fields']['exceptDoneDate']     = $lang->secondorder->exceptDoneDate;
$config->secondorder->search['fields']['app']                = $lang->secondorder->app;
$config->secondorder->search['fields']['acceptUser']         = $lang->secondorder->acceptUser;
$config->secondorder->search['fields']['acceptDept']         = $lang->secondorder->acceptDept;
$config->secondorder->search['fields']['planstartDate']      = $lang->secondorder->planstartDate;
$config->secondorder->search['fields']['planoverDate']       = $lang->secondorder->planoverDate;
$config->secondorder->search['fields']['startDate']          = $lang->secondorder->startDate;
$config->secondorder->search['fields']['overDate']           = $lang->secondorder->overDate;
$config->secondorder->search['fields']['createdDept']        = $lang->secondorder->createdDept;
$config->secondorder->search['fields']['createdBy']          = $lang->secondorder->createdBy;
$config->secondorder->search['fields']['createdDate']        = $lang->secondorder->createdDate;
$config->secondorder->search['fields']['editedBy']           = $lang->secondorder->editedBy;
$config->secondorder->search['fields']['editedDate']         = $lang->secondorder->editedDate;
$config->secondorder->search['fields']['closeReason']        = $lang->secondorder->closeReason;
$config->secondorder->search['fields']['closedBy']           = $lang->secondorder->closedBy;
$config->secondorder->search['fields']['closedDate']         = $lang->secondorder->closedDate;
$config->secondorder->search['fields']['progress']           = $lang->secondorder->progress;
$config->secondorder->search['fields']['desc']               = $lang->secondorder->desc;
$config->secondorder->search['fields']['taskIdentification'] = $lang->secondorder->taskIdentification;
$config->secondorder->search['fields']['externalCode']       = $lang->secondorder->externalCode;
$config->secondorder->search['fields']['handoverMethod']       = $lang->secondorder->handoverMethod;

$config->secondorder->search['params']['code']               = ['operator' => 'include', 'control' => 'input', 'values' => ''];
$config->secondorder->search['params']['status']             = ['operator' => '=', 'control' => 'select', 'values' => $lang->secondorder->statusList];
$config->secondorder->search['params']['dealUser']           = ['operator' => '=', 'control' => 'select', 'values' => 'users'];
$config->secondorder->search['params']['summary']            = ['operator' => 'include', 'control' => 'input',  'values' => ''];
$config->secondorder->search['params']['id']                 = ['operator' => '=', 'control' => 'input',  'values' => ''];
$config->secondorder->search['params']['ifAccept']           = ['operator' => '=', 'control' => 'select', 'values' => $lang->secondorder->ifAccepSearchtList];
$config->secondorder->search['params']['type']               = ['operator' => '=', 'control' => 'select', 'values' => $lang->secondorder->typeList];
$config->secondorder->search['params']['subtype']            = ['operator' => '=', 'control' => 'select', 'values' => [0 => '']];
$config->secondorder->search['params']['source']             = ['operator' => '=', 'control' => 'select', 'values' => $lang->secondorder->sourceList];
$config->secondorder->search['params']['team']               = ['operator' => '=', 'control' => 'select',  'values' => [0 => '']];
$config->secondorder->search['params']['union']              = ['operator' => '=', 'control' => 'select',  'values' => [0 => '']];
$config->secondorder->search['params']['exceptDoneDate']     = ['operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date'];
$config->secondorder->search['params']['app']                = ['operator' => '=', 'control' => 'select', 'values' => [0 => ''], 'mulit' => true];
$config->secondorder->search['params']['acceptUser']         = ['operator' => '=', 'control' => 'select', 'values' => 'users'];
$config->secondorder->search['params']['acceptDept']         = ['operator' => '=', 'control' => 'select', 'values' => 'depts'];
$config->secondorder->search['params']['planstartDate']      = ['operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date'];
$config->secondorder->search['params']['planoverDate']       = ['operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date'];
$config->secondorder->search['params']['startDate']          = ['operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date'];
$config->secondorder->search['params']['overDate']           = ['operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date'];
$config->secondorder->search['params']['createdDept']        = ['operator' => '=', 'control' => 'select', 'values' => 'depts'];
$config->secondorder->search['params']['createdBy']          = ['operator' => '=', 'control' => 'select', 'values' => 'users'];
$config->secondorder->search['params']['createdDate']        = ['operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date'];
$config->secondorder->search['params']['editedBy']           = ['operator' => '=', 'control' => 'select', 'values' => 'users'];
$config->secondorder->search['params']['editedDate']         = ['operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date'];
$config->secondorder->search['params']['closeReason']        = ['operator' => 'include', 'control' => 'input',  'values' => ''];
$config->secondorder->search['params']['closedBy']           = ['operator' => '=', 'control' => 'select', 'values' => 'users'];
$config->secondorder->search['params']['closedDate']         = ['operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date'];
$config->secondorder->search['params']['progress']           = ['operator' => 'include', 'control' => 'input',  'values' => ''];
$config->secondorder->search['params']['desc']               = ['operator' => 'include', 'control' => 'input',  'values' => ''];
$config->secondorder->search['params']['taskIdentification'] = ['operator' => '=', 'control' => 'select', 'values' => $lang->secondorder->taskIdentificationList];
$config->secondorder->search['params']['externalCode']       = ['operator' => 'include', 'control' => 'input', 'values' => ''];
$config->secondorder->search['params']['handoverMethod']       = ['operator' => '=', 'control' => 'select', 'values' => $lang->secondorder->handoverMethodList];