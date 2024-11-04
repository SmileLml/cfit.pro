<?php
$config->opinioninside = new stdclass();
$config->opinioninside->create = new stdclass();
$config->opinioninside->edit   = new stdclass();
$config->opinioninside->subdivide = new stdclass();
$config->opinioninside->review = new stdclass();
$config->opinioninside->assignment = new stdclass();
$config->opinioninside->ignore  = new stdclass();
$config->opinioninside->recoveryed    = new stdclass();
$config->opinioninside->changed    = new stdclass();
$config->opinioninside->close    = new stdclass();
$config->opinioninside->reset    = new stdclass();
$config->opinioninside->editassignedto    = new stdclass();
$config->opinioninside->create->requiredFields = 'name,assignedTo,category,sourceMode,sourceName,union,contact,contactInfo,synUnion,receiveDate,deadline';
$config->opinioninside->edit->requiredFields   = $config->opinioninside->create->requiredFields;
$config->opinioninside->subdivide->requiredFields = 'nextUser,deadlines';
$config->opinioninside->review->requiredFields = 'status,dealUser,level';
$config->opinioninside->assignment->requiredFields = 'dealUser';
$config->opinioninside->editassignedto->requiredFields = 'assignedTo';
$config->opinioninside->ignore->requiredFields       = '';
$config->opinioninside->recoveryed->requiredFields   = '';
$config->opinioninside->close->requiredFields   = 'comment';
$config->opinioninside->reset->requiredFields   = 'comment';
$config->opinioninside->changed->requiredFields   = '';

$config->opinioninside->editor = new stdclass();
$config->opinioninside->editor->create    = array('id' => 'background,overview,desc,comment,remark', 'tools' => 'simpleTools');
$config->opinioninside->editor->edit      = array('id' => 'background,overview,desc,comment,remark', 'tools' => 'simpleTools');
$config->opinioninside->editor->view      = array('id' => 'comment,lastComment', 'tools' => 'simpleTools');
$config->opinioninside->editor->suspend   = array('id' => 'comment', 'tools' => 'simpleTools');
$config->opinioninside->editor->delete    = array('id' => 'comment', 'tools' => 'simpleTools');
$config->opinioninside->editor->activate  = array('id' => 'comment', 'tools' => 'simpleTools');
$config->opinioninside->editor->close     = array('id' => 'comment', 'tools' => 'simpleTools');
$config->opinioninside->editor->change     = array('id' => 'comment', 'tools' => 'simpleTools');
$config->opinioninside->editor->reset     = array('id' => 'comment', 'tools' => 'simpleTools');
$config->opinioninside->editor->subdivide = array('id' => 'demandDesc', 'tools' => 'simpleTools');
$config->opinioninside->editor->review    = array('id' => 'comment', 'tools' => 'simpleTools');
$config->opinioninside->editor->assignment  = array('id' => 'comment', 'tools' => 'simpleTools');
$config->opinioninside->editor->editassignedto  = array('id' => 'comment', 'tools' => 'simpleTools');
$config->opinioninside->editor->ignore         = array('id' => 'comment', 'tools' => 'simpleTools');
$config->opinioninside->editor->recoveryed     = array('id' => 'comment', 'tools' => 'simpleTools');

$config->opinioninside->export = new stdclass();
$config->opinioninside->import = new stdclass();
$config->opinioninside->import->requiredFields = 'name,background,overview,desc,sourceMode,sourceName,category,union,receiveDate,deadline,createdBy,contact,contactInfo,assignedTo,status,dealUser';

$config->opinioninside->export->listFields     = explode(',', "category,sourceMode,union,status,createdBy");
$config->opinioninside->export->templateFields = explode(',', "name,background,overview,desc,sourceMode,sourceName,category,union,receiveDate,deadline,createdBy,contact,contactInfo,assignedTo,status,dealUser,remark");

$config->opinioninside->list = new stdclass();
$config->opinioninside->list->exportFields = '
    name, status, union, dealUser, code, id, sourceMode, sourceName, category, assignedTo, synUnion, date, project, receiveDate, 
    deadline, onlineTimeByDemand, planDeadline, contact, contactInfo, background, overview, remark, desc, demandCode, createdBy, createdDate, editedBy, editedDate, 
    closedBy, closedDate, activedBy, activedDate, suspendBy, suspendDate, recoveredBy, recoveredDate';
$config->opinioninside->prohibitEditing = array(
    '1' => 'demandCode',
    '2' => 'sourceName',
    '3' => 'contact',
    '4' => 'contactInfo',
    '5' => 'date',
    '6' => 'deadline',
    '10'=> 'receiveDate',
    '7' => 'name',
    // '8' => 'union',
    '9' => 'sourceMode',
    // '11' => 'category',
    // '12' => 'assignedTo',
);

/* Search. */
global $lang;
$config->opinioninside->search['module'] = 'opinioninside';
$config->opinioninside->search['fields']['name']        = $lang->opinioninside->name;
$config->opinioninside->search['fields']['status']      = $lang->opinioninside->status;
$config->opinioninside->search['fields']['union']       = $lang->opinioninside->union;
$config->opinioninside->search['fields']['dealUser']    = $lang->opinioninside->dealUser;
$config->opinioninside->search['fields']['code']        = $lang->opinioninside->code;
$config->opinioninside->search['fields']['id']          = $lang->opinioninside->id;
$config->opinioninside->search['fields']['sourceMode']  = $lang->opinioninside->sourceMode;
$config->opinioninside->search['fields']['sourceName']  = $lang->opinioninside->sourceName;
$config->opinioninside->search['fields']['category']    = $lang->opinioninside->category;
// $config->opinioninside->search['fields']['level']       = $lang->opinioninside->level;
$config->opinioninside->search['fields']['assignedTo']  = $lang->opinioninside->assignedTo;
$config->opinioninside->search['fields']['synUnion']    = $lang->opinioninside->synUnion;
$config->opinioninside->search['fields']['date']        = $lang->opinioninside->date;
$config->opinioninside->search['fields']['project']     = $lang->opinioninside->project;
$config->opinioninside->search['fields']['receiveDate'] = $lang->opinioninside->receiveDate;
$config->opinioninside->search['fields']['deadline']    = $lang->opinioninside->deadline;
$config->opinioninside->search['fields']['onlineTimeByDemand']    = $lang->opinioninside->onlineTimeByDemand;
$config->opinioninside->search['fields']['planDeadline']= $lang->opinioninside->planDeadline;
$config->opinioninside->search['fields']['contact']     = $lang->opinioninside->contact;
$config->opinioninside->search['fields']['contactInfo'] = $lang->opinioninside->contactInfo;
$config->opinioninside->search['fields']['background']  = $lang->opinioninside->background;
$config->opinioninside->search['fields']['overview']    = $lang->opinioninside->overview;
$config->opinioninside->search['fields']['remark']      = $lang->opinioninside->remark;
$config->opinioninside->search['fields']['desc']        = $lang->opinioninside->desc;
$config->opinioninside->search['fields']['demandCode']  = $lang->opinioninside->demandCode;
$config->opinioninside->search['fields']['createdBy']   = $lang->opinioninside->createdBy;
$config->opinioninside->search['fields']['createdDate'] = $lang->opinioninside->createdDate;
$config->opinioninside->search['fields']['editedBy']    = $lang->opinioninside->editedBy;
$config->opinioninside->search['fields']['editedDate']  = $lang->opinioninside->editedDate;
$config->opinioninside->search['fields']['closedBy']    = $lang->opinioninside->closedBy;
$config->opinioninside->search['fields']['closedDate']  = $lang->opinioninside->closedDate;
$config->opinioninside->search['fields']['activedBy']   = $lang->opinioninside->activedBy;
$config->opinioninside->search['fields']['activedDate'] = $lang->opinioninside->activedDate;
$config->opinioninside->search['fields']['suspendBy']   = $lang->opinioninside->suspendBy;
$config->opinioninside->search['fields']['suspendDate'] = $lang->opinioninside->suspendDate;
$config->opinioninside->search['fields']['recoveredBy']   = $lang->opinioninside->recoveredBy;
$config->opinioninside->search['fields']['recoveredDate'] = $lang->opinioninside->recoveredDate;

$config->opinioninside->search['params']['name']         = array('operator' => 'include', 'control' => 'input',  'values' => '');
$config->opinioninside->search['params']['status']       = array('operator' => '=', 'control' => 'select', 'values' => $lang->opinioninside->searchStatusList);
$config->opinioninside->search['params']['union']        = array('operator' => 'include', 'control' => 'select', 'values' => array(0 => ''));
$config->opinioninside->search['params']['dealUser']     = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->opinioninside->search['params']['code']         = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->opinioninside->search['params']['id']           = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->opinioninside->search['params']['sourceMode']   = array('operator' => '=', 'control' => 'select', 'values' => array(0 => ''));
$config->opinioninside->search['params']['sourceName']   = array('operator' => 'include', 'control' => 'input',  'values' => '');
$config->opinioninside->search['params']['category']     = array('operator' => '=', 'control' => 'select', 'values' => array(0 => ''));
// $config->opinioninside->search['params']['level']        = array('operator' => '=', 'control' => 'select', 'values' => $lang->opinioninside->levelList);
$config->opinioninside->search['params']['assignedTo']   = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->opinioninside->search['params']['synUnion']     = array('operator' => 'include', 'control' => 'select', 'values' => array(0 => ''));
$config->opinioninside->search['params']['date']         = array('operator' => '=', 'control' => 'input',  'values' => '', 'class' => 'date');
$config->opinioninside->search['params']['project']      = array('operator' => 'include', 'control' => 'select',  'values' => '');
$config->opinioninside->search['params']['receiveDate']  = array('operator' => '=', 'control' => 'input',  'values' => '', 'class' => 'date');
$config->opinioninside->search['params']['deadline']     = array('operator' => '=', 'control' => 'input',  'values' => '', 'class' => 'date');
$config->opinioninside->search['params']['onlineTimeByDemand']     = array('operator' => '=', 'control' => 'input',  'values' => '', 'class' => 'date');
$config->opinioninside->search['params']['planDeadline'] = array('operator' => '=', 'control' => 'input',  'values' => '', 'class' => 'date');
$config->opinioninside->search['params']['contact']      = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->opinioninside->search['params']['contactInfo']  = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->opinioninside->search['params']['background']   = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->opinioninside->search['params']['overview']     = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->opinioninside->search['params']['remark']       = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->opinioninside->search['params']['desc']         = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->opinioninside->search['params']['demandCode']   = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->opinioninside->search['params']['createdBy']    = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->opinioninside->search['params']['createdDate']  = array('operator' => '=', 'control' => 'input',  'values' => '', 'class' => 'date');
$config->opinioninside->search['params']['editedBy']     = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->opinioninside->search['params']['editedDate']   = array('operator' => '=', 'control' => 'input',  'values' => '', 'class' => 'date');
$config->opinioninside->search['params']['closedBy']     = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->opinioninside->search['params']['closedDate']   = array('operator' => '=', 'control' => 'input',  'values' => '', 'class' => 'date');
$config->opinioninside->search['params']['activedBy']    = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->opinioninside->search['params']['activedDate']  = array('operator' => '=', 'control' => 'input',  'values' => '', 'class' => 'date');
$config->opinioninside->search['params']['suspendBy']    = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->opinioninside->search['params']['suspendDate']  = array('operator' => '=', 'control' => 'input',  'values' => '', 'class' => 'date');
$config->opinioninside->search['params']['recoveredBy']  = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->opinioninside->search['params']['recoveredDate']= array('operator' => '=', 'control' => 'input',  'values' => '', 'class' => 'date');
