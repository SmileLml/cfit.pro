<?php

$config->deptorder         = new stdclass();
$config->deptorder->create = new stdclass();
$config->deptorder->copy   = new stdclass();
$config->deptorder->edit   = new stdclass();
$config->deptorder->deal   = new stdclass();
$config->deptorder->close  = new stdclass();
$config->deptorder->delete = new stdclass();

$config->deptorder->create->requiredFields = 'summary,type,subtype,source,union,team,dealUser,app,desc';
$config->deptorder->copy->requiredFields   = 'summary,type,subtype,source,union,team,dealUser,app,desc';
$config->deptorder->edit->requiredFields   = 'summary,type,subtype,source,union,team,dealUser,app,desc';
$config->deptorder->deal->requiredFields   = 'app,progress,planstartDate,planoverDate';
$config->deptorder->close->requiredFields  = 'comment,closeReason';
$config->deptorder->delete->requiredFields = 'comment';

$config->deptorder->editor                 = new stdclass();
$config->deptorder->editor->create         = ['id' => 'comment,desc', 'tools' => 'simpleTools'];
$config->deptorder->editor->copy           = ['id' => 'comment,desc', 'tools' => 'simpleTools'];
$config->deptorder->editor->edit           = ['id' => 'comment,desc', 'tools' => 'simpleTools'];
$config->deptorder->editor->deal           = ['id' => 'progress,comment,consultRes,testRes,dealRes', 'tools' => 'simpleTools'];
$config->deptorder->editor->close          = ['id' => 'comment', 'tools' => 'simpleTools'];
$config->deptorder->editor->delete         = ['id' => 'comment', 'tools' => 'simpleTools'];
$config->deptorder->editor->editassignedto = ['id' => 'comment', 'tools' => 'simpleTools'];

$config->deptorder->export             = new stdclass();
$config->deptorder->export->listFields = explode(',', 'code,status,dealUser,summary,id,type,subtype,app,source,createdDept,acceptUser,acceptDept,team,union,exceptDoneDate,closeReason,ifAccept,createdBy,createdDate,
                                            editedBy,editedDate,closedBy,closedDate,planstartDate,planoverDate,startDate,overDate,progress,consultRes,testRes,dealRes,desc');

$config->deptorder->list               = new stdclass();
$config->deptorder->list->exportFields = 'code,status,dealUser,summary,id,ifAccept,type,subtype,source,team,union,exceptDoneDate,app,acceptUser,acceptDept,planstartDate,planoverDate,startDate,overDate,createdDept,createdBy,createdDate,
                                            editedBy,editedDate,closeReason,closedBy,closedDate,progress,consultRes,testRes,dealRes,desc';

$config->deptorder->import = new stdclass();

$config->deptorder->showImport = new stdclass();

// Search.
global $lang;
$config->deptorder->search['module']                   = 'deptorder';
$config->deptorder->search['fields']['code']           = $lang->deptorder->code;
$config->deptorder->search['fields']['status']         = $lang->deptorder->status;
$config->deptorder->search['fields']['dealUser']       = $lang->deptorder->dealUser;
$config->deptorder->search['fields']['summary']        = $lang->deptorder->summary;
$config->deptorder->search['fields']['id']             = $lang->deptorder->id;
$config->deptorder->search['fields']['ifAccept']       = $lang->deptorder->ifAccept;
$config->deptorder->search['fields']['type']           = $lang->deptorder->type;
$config->deptorder->search['fields']['subtype']        = $lang->deptorder->subtype;
$config->deptorder->search['fields']['source']         = $lang->deptorder->source;
$config->deptorder->search['fields']['team']           = $lang->deptorder->team;
$config->deptorder->search['fields']['union']          = $lang->deptorder->union;
$config->deptorder->search['fields']['exceptDoneDate'] = $lang->deptorder->exceptDoneDate;
$config->deptorder->search['fields']['app']            = $lang->deptorder->app;
$config->deptorder->search['fields']['acceptUser']     = $lang->deptorder->acceptUser;
$config->deptorder->search['fields']['acceptDept']     = $lang->deptorder->acceptDept;
$config->deptorder->search['fields']['planstartDate']  = $lang->deptorder->planstartDate;
$config->deptorder->search['fields']['planoverDate']   = $lang->deptorder->planoverDate;
$config->deptorder->search['fields']['startDate']      = $lang->deptorder->startDate;
$config->deptorder->search['fields']['overDate']       = $lang->deptorder->overDate;
$config->deptorder->search['fields']['createdDept']    = $lang->deptorder->createdDept;
$config->deptorder->search['fields']['createdBy']      = $lang->deptorder->createdBy;
$config->deptorder->search['fields']['createdDate']    = $lang->deptorder->createdDate;
$config->deptorder->search['fields']['editedBy']       = $lang->deptorder->editedBy;
$config->deptorder->search['fields']['editedDate']     = $lang->deptorder->editedDate;
$config->deptorder->search['fields']['closeReason']    = $lang->deptorder->closeReason;
$config->deptorder->search['fields']['closedBy']       = $lang->deptorder->closedBy;
$config->deptorder->search['fields']['closedDate']     = $lang->deptorder->closedDate;
$config->deptorder->search['fields']['progress']       = $lang->deptorder->progress;
$config->deptorder->search['fields']['desc']           = $lang->deptorder->desc;

$config->deptorder->search['params']['code']           = ['operator' => 'include', 'control' => 'input', 'values' => ''];
$config->deptorder->search['params']['status']         = ['operator' => '=', 'control' => 'select', 'values' => $lang->deptorder->statusList];
$config->deptorder->search['params']['dealUser']       = ['operator' => '=', 'control' => 'select', 'values' => 'users'];
$config->deptorder->search['params']['summary']        = ['operator' => 'include', 'control' => 'input',  'values' => ''];
$config->deptorder->search['params']['id']             = ['operator' => '=', 'control' => 'input',  'values' => ''];
$config->deptorder->search['params']['ifAccept']       = ['operator' => '=', 'control' => 'select', 'values' => $lang->deptorder->ifAccepSearchtList];
$config->deptorder->search['params']['type']           = ['operator' => '=', 'control' => 'select', 'values' => $lang->deptorder->typeList];
$config->deptorder->search['params']['subtype']        = ['operator' => '=', 'control' => 'select', 'values' => [0 => '']];
$config->deptorder->search['params']['source']         = ['operator' => '=', 'control' => 'select', 'values' => $lang->deptorder->sourceList];
$config->deptorder->search['params']['team']           = ['operator' => 'include', 'control' => 'select', 'values' => 'users'];
$config->deptorder->search['params']['union']          = ['operator' => '=', 'control' => 'select',  'values' => [0 => '']];
$config->deptorder->search['params']['exceptDoneDate'] = ['operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date'];
$config->deptorder->search['params']['app']            = ['operator' => '=', 'control' => 'select', 'values' => [0 => ''], 'mulit' => true];
$config->deptorder->search['params']['acceptUser']     = ['operator' => '=', 'control' => 'select', 'values' => 'users'];
$config->deptorder->search['params']['acceptDept']     = ['operator' => '=', 'control' => 'select', 'values' => 'depts'];
$config->deptorder->search['params']['planstartDate']  = ['operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date'];
$config->deptorder->search['params']['planoverDate']   = ['operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date'];
$config->deptorder->search['params']['startDate']      = ['operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date'];
$config->deptorder->search['params']['overDate']       = ['operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date'];
$config->deptorder->search['params']['createdDept']    = ['operator' => '=', 'control' => 'select', 'values' => 'depts'];
$config->deptorder->search['params']['createdBy']      = ['operator' => '=', 'control' => 'select', 'values' => 'users'];
$config->deptorder->search['params']['createdDate']    = ['operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date'];
$config->deptorder->search['params']['editedBy']       = ['operator' => '=', 'control' => 'select', 'values' => 'users'];
$config->deptorder->search['params']['editedDate']     = ['operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date'];
$config->deptorder->search['params']['closeReason']    = ['operator' => 'include', 'control' => 'input',  'values' => ''];
$config->deptorder->search['params']['closedBy']       = ['operator' => '=', 'control' => 'select', 'values' => 'users'];
$config->deptorder->search['params']['closedDate']     = ['operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date'];
$config->deptorder->search['params']['progress']       = ['operator' => 'include', 'control' => 'input',  'values' => ''];
$config->deptorder->search['params']['desc']           = ['operator' => 'include', 'control' => 'input',  'values' => ''];
