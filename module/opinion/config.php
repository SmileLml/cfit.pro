<?php
$config->opinion = new stdclass();
$config->opinion->create = new stdclass();
$config->opinion->edit   = new stdclass();
$config->opinion->subdivide = new stdclass();
$config->opinion->review = new stdclass();
$config->opinion->assignment = new stdclass();
$config->opinion->ignore  = new stdclass();
$config->opinion->recoveryed    = new stdclass();
$config->opinion->changed    = new stdclass();
$config->opinion->close    = new stdclass();
$config->opinion->reset    = new stdclass();
$config->opinion->editassignedto    = new stdclass();
$config->opinion->create->requiredFields = 'name,assignedTo,category,sourceMode,sourceName,union,contact,contactInfo,synUnion,receiveDate,deadline';
$config->opinion->edit->requiredFields   = $config->opinion->create->requiredFields;
$config->opinion->subdivide->requiredFields = 'nextUser,deadlines';
$config->opinion->review->requiredFields = 'status,dealUser,level';
$config->opinion->assignment->requiredFields = 'dealUser';
$config->opinion->editassignedto->requiredFields = 'assignedTo';
$config->opinion->ignore->requiredFields       = '';
$config->opinion->recoveryed->requiredFields   = '';
$config->opinion->close->requiredFields   = 'comment';
$config->opinion->reset->requiredFields   = 'comment';
$config->opinion->changed->requiredFields   = '';

$config->opinion->editor = new stdclass();
$config->opinion->editor->create    = array('id' => 'background,overview,desc,comment,remark', 'tools' => 'simpleTools');
$config->opinion->editor->edit      = array('id' => 'background,overview,desc,comment,remark', 'tools' => 'simpleTools');
$config->opinion->editor->view      = array('id' => 'comment,lastComment', 'tools' => 'simpleTools');
$config->opinion->editor->suspend   = array('id' => 'comment', 'tools' => 'simpleTools');
$config->opinion->editor->delete    = array('id' => 'comment', 'tools' => 'simpleTools');
$config->opinion->editor->activate  = array('id' => 'comment', 'tools' => 'simpleTools');
$config->opinion->editor->close     = array('id' => 'comment', 'tools' => 'simpleTools');
$config->opinion->editor->change    = array('id' => 'comment,changeReason', 'tools' => 'simpleTools');
$config->opinion->editor->editchange = $config->opinion->editor->change ;
$config->opinion->editor->reset     = array('id' => 'comment', 'tools' => 'simpleTools');
$config->opinion->editor->subdivide = array('id' => 'demandDesc,progress', 'tools' => 'simpleTools');
$config->opinion->editor->review    = array('id' => 'comment', 'tools' => 'simpleTools');
$config->opinion->editor->assignment  = array('id' => 'comment', 'tools' => 'simpleTools');
$config->opinion->editor->editassignedto  = array('id' => 'comment', 'tools' => 'simpleTools');
$config->opinion->editor->ignore         = array('id' => 'comment', 'tools' => 'simpleTools');
$config->opinion->editor->recoveryed     = array('id' => 'comment', 'tools' => 'simpleTools');

$config->opinion->export = new stdclass();
$config->opinion->import = new stdclass();
$config->opinion->import->requiredFields = 'name,background,overview,desc,sourceMode,sourceName,category,union,receiveDate,deadline,createdBy,contact,contactInfo,assignedTo,status,dealUser';

$config->opinion->export->listFields     = explode(',', "category,sourceMode,union,status,createdBy");
$config->opinion->export->templateFields = explode(',', "name,background,overview,desc,sourceMode,sourceName,category,union,receiveDate,deadline,createdBy,contact,contactInfo,assignedTo,status,dealUser,remark");

$config->opinion->list = new stdclass();
$config->opinion->list->exportFields = '
    name, status, union, dealUser, code, id, sourceMode, sourceName, category, urgency, type, assignedTo, synUnion, date, project, receiveDate, 
    deadline, solvedTime, onlineTimeByDemand, planDeadline, contact, contactInfo, background, overview, remark, desc, demandCode, createdBy, createdDate, editedBy, editedDate, 
    closedBy, closedDate, activedBy, activedDate, suspendBy, suspendDate, recoveredBy, recoveredDate,opinionChangeTimes,lastChangeTime';
$config->opinion->prohibitEditing = array(
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
$config->opinion->search['module'] = 'opinion';
$config->opinion->search['fields']['name']        = $lang->opinion->name;
$config->opinion->search['fields']['status']      = $lang->opinion->status;
$config->opinion->search['fields']['union']       = $lang->opinion->union;
$config->opinion->search['fields']['dealUser']    = $lang->opinion->dealUser;
$config->opinion->search['fields']['code']        = $lang->opinion->code;
$config->opinion->search['fields']['id']          = $lang->opinion->id;
$config->opinion->search['fields']['sourceMode']  = $lang->opinion->sourceMode;
$config->opinion->search['fields']['sourceName']  = $lang->opinion->sourceName;
$config->opinion->search['fields']['category']    = $lang->opinion->category;
$config->opinion->search['fields']['urgency']     = $lang->opinion->urgency;
// $config->opinion->search['fields']['level']       = $lang->opinion->level;
$config->opinion->search['fields']['assignedTo']  = $lang->opinion->assignedTo;
$config->opinion->search['fields']['synUnion']    = $lang->opinion->synUnion;
$config->opinion->search['fields']['date']        = $lang->opinion->date;
$config->opinion->search['fields']['project']     = $lang->opinion->project;
$config->opinion->search['fields']['receiveDate'] = $lang->opinion->receiveDate;
$config->opinion->search['fields']['deadline']    = $lang->opinion->deadline;
$config->opinion->search['fields']['onlineTimeByDemand']    = $lang->opinion->onlineTimeByDemand;
$config->opinion->search['fields']['planDeadline']= $lang->opinion->planDeadline;
$config->opinion->search['fields']['contact']     = $lang->opinion->contact;
$config->opinion->search['fields']['contactInfo'] = $lang->opinion->contactInfo;
$config->opinion->search['fields']['background']  = $lang->opinion->background;
$config->opinion->search['fields']['overview']    = $lang->opinion->overview;
$config->opinion->search['fields']['remark']      = $lang->opinion->remark;
$config->opinion->search['fields']['desc']        = $lang->opinion->desc;
$config->opinion->search['fields']['demandCode']  = $lang->opinion->demandCode;
$config->opinion->search['fields']['createdBy']   = $lang->opinion->createdBy;
$config->opinion->search['fields']['createdDate'] = $lang->opinion->createdDate;
$config->opinion->search['fields']['editedBy']    = $lang->opinion->editedBy;
$config->opinion->search['fields']['editedDate']  = $lang->opinion->editedDate;
$config->opinion->search['fields']['closedBy']    = $lang->opinion->closedBy;
$config->opinion->search['fields']['closedDate']  = $lang->opinion->closedDate;
$config->opinion->search['fields']['activedBy']   = $lang->opinion->activedBy;
$config->opinion->search['fields']['activedDate'] = $lang->opinion->activedDate;
$config->opinion->search['fields']['suspendBy']   = $lang->opinion->suspendBy;
$config->opinion->search['fields']['suspendDate'] = $lang->opinion->suspendDate;
$config->opinion->search['fields']['recoveredBy']   = $lang->opinion->recoveredBy;
$config->opinion->search['fields']['recoveredDate'] = $lang->opinion->recoveredDate;

$config->opinion->search['params']['name']         = array('operator' => 'include', 'control' => 'input',  'values' => '');
$config->opinion->search['params']['status']       = array('operator' => '=', 'control' => 'select', 'values' => $lang->opinion->searchstatusList);
$config->opinion->search['params']['union']        = array('operator' => 'include', 'control' => 'select', 'values' => $lang->opinion->unionList);
$config->opinion->search['params']['dealUser']     = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->opinion->search['params']['code']         = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->opinion->search['params']['id']           = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->opinion->search['params']['sourceMode']   = array('operator' => '=', 'control' => 'select', 'values' => $lang->opinion->sourceModeList);
$config->opinion->search['params']['sourceName']   = array('operator' => 'include', 'control' => 'input',  'values' => '');
$config->opinion->search['params']['category']     = array('operator' => '=', 'control' => 'select', 'values' => $lang->opinion->categoryList);
$config->opinion->search['params']['urgency']      = array('operator' => 'include', 'control' => 'input',  'values' => '');
// $config->opinion->search['params']['level']        = array('operator' => '=', 'control' => 'select', 'values' => $lang->opinion->levelList);
$config->opinion->search['params']['assignedTo']   = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->opinion->search['params']['synUnion']     = array('operator' => 'include', 'control' => 'select', 'values' => $lang->opinion->synUnionList);
$config->opinion->search['params']['date']         = array('operator' => '=', 'control' => 'input',  'values' => '', 'class' => 'date');
$config->opinion->search['params']['project']      = array('operator' => 'include', 'control' => 'select',  'values' => '','mulit'=>true);
$config->opinion->search['params']['receiveDate']  = array('operator' => '=', 'control' => 'input',  'values' => '', 'class' => 'date');
$config->opinion->search['params']['deadline']     = array('operator' => '=', 'control' => 'input',  'values' => '', 'class' => 'date');
$config->opinion->search['params']['onlineTimeByDemand']     = array('operator' => '=', 'control' => 'input',  'values' => '', 'class' => 'date');
$config->opinion->search['params']['planDeadline'] = array('operator' => '=', 'control' => 'input',  'values' => '', 'class' => 'date');
$config->opinion->search['params']['contact']      = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->opinion->search['params']['contactInfo']  = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->opinion->search['params']['background']   = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->opinion->search['params']['overview']     = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->opinion->search['params']['remark']       = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->opinion->search['params']['desc']         = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->opinion->search['params']['demandCode']   = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->opinion->search['params']['createdBy']    = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->opinion->search['params']['createdDate']  = array('operator' => '=', 'control' => 'input',  'values' => '', 'class' => 'date');
$config->opinion->search['params']['editedBy']     = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->opinion->search['params']['editedDate']   = array('operator' => '=', 'control' => 'input',  'values' => '', 'class' => 'date');
$config->opinion->search['params']['closedBy']     = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->opinion->search['params']['closedDate']   = array('operator' => '=', 'control' => 'input',  'values' => '', 'class' => 'date');
$config->opinion->search['params']['activedBy']    = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->opinion->search['params']['activedDate']  = array('operator' => '=', 'control' => 'input',  'values' => '', 'class' => 'date');
$config->opinion->search['params']['suspendBy']    = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->opinion->search['params']['suspendDate']  = array('operator' => '=', 'control' => 'input',  'values' => '', 'class' => 'date');
$config->opinion->search['params']['recoveredBy']  = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->opinion->search['params']['recoveredDate']= array('operator' => '=', 'control' => 'input',  'values' => '', 'class' => 'date');
