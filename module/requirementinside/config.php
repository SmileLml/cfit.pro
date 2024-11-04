<?php
$config->requirementinside           = new stdclass();
$config->requirementinside->create   = new stdclass();
$config->requirementinside->edit     = new stdclass();
$config->requirementinside->change   = new stdclass();
$config->requirementinside->review   = new stdclass();
$config->requirementinside->feedback = new stdclass();
$config->requirementinside->confirm  = new stdclass();
$config->requirementinside->subdivide = new stdclass();
$config->requirementinside->assignto = new stdclass();
$config->requirementinside->import = new stdclass();

$config->requirementinside->create->requiredFields   = 'app,deadLine,name,desc,dealUser';
$config->requirementinside->edit->requiredFields     = 'app,opinion,deadLine,name,desc,dealUser';
$config->requirementinside->change->requiredFields   = 'reviewer,dept,end,owner,contact,method,analysis,handling';
$config->requirementinside->review->requiredFields   = 'result,comment';
$config->requirementinside->feedback->requiredFields = 'end,owner,contact,method,analysis,handling,project';
$config->requirementinside->confirm->requiredFields = 'desc,dealUser';
$config->requirementinside->subdivide->requiredFields = 'desc,dealUser';
$config->requirementinside->assignto->requiredFields = 'assignedTo,comment';
$config->requirementinside->import->requiredFields = 'createdBy,name,desc,projectManager,app,status';

$config->requirementinside->editor = new stdclass();
$config->requirementinside->editor->create   = array('id' => 'desc,comment', 'tools' => 'simpleTools', 'height' => '100px');
$config->requirementinside->editor->edit     = array('id' => 'desc,comment', 'tools' => 'simpleTools', 'height' => '100px');
$config->requirementinside->editor->confirm  = array('id' => 'desc', 'tools' => 'simpleTools');
$config->requirementinside->editor->change   = array('id' => 'analysis,handling,implement,desc', 'tools' => 'simpleTools');
$config->requirementinside->editor->view     = array('id' => 'audit_opinion,comment,lastComment', 'tools' => 'simpleTools');
//$config->requirementinside->editor->feedback = array('id' => 'analysis,handling,implement', 'tools' => 'emptyTools');
$config->requirementinside->editor->review   = array('id' => 'approveComm', 'tools' => 'simpleTools');
$config->requirementinside->editor->close    = array('id' => 'comment', 'tools' => 'simpleTools');
$config->requirementinside->editor->delete   = array('id' => 'comment', 'tools' => 'simpleTools');
$config->requirementinside->editor->assignto   = array('id' => 'comment', 'tools' => 'simpleTools');
$config->requirementinside->editor->subdivide = array('id' => 'demandDesc,progress,reason', 'tools' => 'simpleTools', 'height' => '100px');
$config->requirementinside->editor->activate   = array('id' => 'dealcomment', 'tools' => 'simpleTools');
$config->requirementinside->editor->close    = array('id' => 'dealcomment', 'tools' => 'simpleTools');
$config->requirementinside->editor->delete  = array('id' => 'dealcomment', 'tools' => 'simpleTools');
$config->requirementinside->editor->ignore     = array('id' => 'dealcomment', 'tools' => 'simpleTools');
$config->requirementinside->editor->recover     = array('id' => 'dealcomment', 'tools' => 'simpleTools');


$config->requirementinside->noeditor = new stdclass();
$config->requirementinside->noeditor->feedback = array('id' => 'analysis,handling,implement', 'tools' => 'emptyTools');

$config->requirementinside->prohibitEditing = array(
    '1' => 'name',
);
$config->requirementinside->prohibitFeedback = array(
    '1' => 'product',
    '2' => 'line',
);

/* Search. */
global $lang;
$config->requirementinside->search['module'] = 'requirementinside';
$config->requirementinside->search['fields']['code']        = $lang->requirementinside->code;
$config->requirementinside->search['fields']['name']        = $lang->requirementinside->name;
$config->requirementinside->search['fields']['ID']        = $lang->requirementinside->ID;
$config->requirementinside->search['fields']['dealUser']        = $lang->requirementinside->pending;
$config->requirementinside->search['fields']['status']      = $lang->requirementinside->status;
$config->requirementinside->search['fields']['opinion']      = $lang->requirementinside->opinionID;
$config->requirementinside->search['fields']['deadLine']    = $lang->requirementinside->deadLine;
$config->requirementinside->search['fields']['sourceMode']    = $lang->requirementinside->sourceMode;
$config->requirementinside->search['fields']['sourceName']    = $lang->requirementinside->sourceName;
$config->requirementinside->search['fields']['union']    = $lang->requirementinside->union;
$config->requirementinside->search['fields']['acceptTime']    = $lang->requirementinside->acceptTime;
$config->requirementinside->search['fields']['onlineTimeByDemand']    = $lang->requirementinside->taskLaunchTime;
$config->requirementinside->search['fields']['app']         = $lang->requirementinside->app;
$config->requirementinside->search['fields']['productManager']         = $lang->requirementinside->productManager;
$config->requirementinside->search['fields']['projectManager']         = $lang->requirementinside->projectManager;
$config->requirementinside->search['fields']['feedbackDealUser']         = $lang->requirementinside->feedbackDealuser;
$config->requirementinside->search['fields']['feedbackStatus']         = $lang->requirementinside->feedbackStatus;
$config->requirementinside->search['fields']['parentCode']         = $lang->requirementinside->parentCode;
$config->requirementinside->search['fields']['entriesCode']         = $lang->requirementinside->entriesCode;
$config->requirementinside->search['fields']['feedbackCode']         = $lang->requirementinside->feedbackCode;
$config->requirementinside->search['fields']['line']         = $lang->requirementinside->line;
$config->requirementinside->search['fields']['product']     = $lang->requirementinside->product;
$config->requirementinside->search['fields']['planEnd']     = $lang->requirementinside->planEnd;
$config->requirementinside->search['fields']['dept']        = $lang->requirementinside->dept;
$config->requirementinside->search['fields']['owner']       = $lang->requirementinside->owner;
$config->requirementinside->search['fields']['createdBy']   = $lang->requirementinside->createdBy;
$config->requirementinside->search['fields']['createdDate'] = $lang->requirementinside->createdDate;
$config->requirementinside->search['fields']['editedBy']   = $lang->requirementinside->editedBy;
$config->requirementinside->search['fields']['editedDate'] = $lang->requirementinside->editedDate;
$config->requirementinside->search['fields']['closedBy']   = $lang->requirementinside->closedBy;
$config->requirementinside->search['fields']['closedDate'] = $lang->requirementinside->closedDate;
$config->requirementinside->search['fields']['activatedBy']   = $lang->requirementinside->activatedBy;
$config->requirementinside->search['fields']['activatedDate'] = $lang->requirementinside->activatedDate;
$config->requirementinside->search['fields']['ignoredBy']   = $lang->requirementinside->ignoredBy;
$config->requirementinside->search['fields']['ignoredDate'] = $lang->requirementinside->ignoredDate;
$config->requirementinside->search['fields']['recoveryedBy']   = $lang->requirementinside->recoveryedBy;
$config->requirementinside->search['fields']['recoveryedDate'] = $lang->requirementinside->recoveryedDate;
$config->requirementinside->search['fields']['contact'] = $lang->requirementinside->contact;
$config->requirementinside->search['fields']['feedbackBy'] = $lang->requirementinside->feedbackBy;
$config->requirementinside->search['fields']['project'] = $lang->requirementinside->project;
$config->requirementinside->search['fields']['desc'] = $lang->requirementinside->desc;
$config->requirementinside->search['fields']['analysis'] = $lang->requirementinside->analysis;
$config->requirementinside->search['fields']['handling'] = $lang->requirementinside->handling;
$config->requirementinside->search['fields']['implement'] = $lang->requirementinside->implement;
$config->requirementinside->search['fields']['reviewComments'] = $lang->requirementinside->reviewComments;

$config->requirementinside->search['params']['reviewComments']         = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->requirementinside->search['params']['implement']         = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->requirementinside->search['params']['handling']         = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->requirementinside->search['params']['desc']         = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->requirementinside->search['params']['analysis']         = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->requirementinside->search['params']['code']         = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->requirementinside->search['params']['name']         = array('operator' => 'include', 'control' => 'input',  'values' => '');
$config->requirementinside->search['params']['project']      = array('operator' => '=', 'control' => 'select', 'values' => '');
$config->requirementinside->search['params']['entriesCode']      = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->requirementinside->search['params']['opinion']      = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->requirementinside->search['params']['acceptTime']       = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->requirementinside->search['params']['onlineTimeByDemand']       = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->requirementinside->search['params']['productManager']       = array('operator' => 'include', 'control' => 'select', 'values' => 'users');
$config->requirementinside->search['params']['projectManager']       = array('operator' => 'include', 'control' => 'select', 'values' => 'users');
//$config->requirementinside->search['params']['feedbackDealUser']       = array('operator' => 'include', 'control' => 'select', 'values' => 'users');
//$config->requirementinside->search['params']['feedbackStatus']       = array('operator' => '=', 'control' => 'select', 'values' => $lang->requirementinside->feedbackStatusList);
$config->requirementinside->search['params']['parentCode']       = array('operator' => 'include', 'control' => 'input', 'values' => '');
//$config->requirementinside->search['params']['entriesCode']       = array('operator' => 'include', 'control' => 'input', 'values' => '');
//$config->requirementinside->search['params']['feedbackCode']       = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->requirementinside->search['params']['editedBy']        = array('operator' => 'include', 'control' => 'select', 'values' => 'users');
$config->requirementinside->search['params']['editedDate']  = array('operator' => '=', 'control' => 'input',  'values' => '', 'class' => 'date');
$config->requirementinside->search['params']['closedBy']        = array('operator' => 'include', 'control' => 'select', 'values' => 'users');
$config->requirementinside->search['params']['closedDate']  = array('operator' => '=', 'control' => 'input',  'values' => '', 'class' => 'date');
$config->requirementinside->search['params']['activatedBy']        = array('operator' => 'include', 'control' => 'select', 'values' => 'users');
$config->requirementinside->search['params']['activatedDate']  = array('operator' => '=', 'control' => 'input',  'values' => '', 'class' => 'date');
$config->requirementinside->search['params']['ignoredBy']        = array('operator' => 'include', 'control' => 'select', 'values' => 'users');
$config->requirementinside->search['params']['ignoredDate']  = array('operator' => '=', 'control' => 'input',  'values' => '', 'class' => 'date');
$config->requirementinside->search['params']['recoveryedBy']        = array('operator' => 'include', 'control' => 'select', 'values' => 'users');
$config->requirementinside->search['params']['recoveryedDate']  = array('operator' => '=', 'control' => 'input',  'values' => '', 'class' => 'date');
$config->requirementinside->search['params']['contact']  = array('operator' => '=', 'control' => 'input',  'values' => '');
//$config->requirementinside->search['params']['feedbackBy']        = array('operator' => '=', 'control' => 'select', 'values' => 'users');

$config->requirementinside->search['params']['ID']        = array('operator' => '=', 'control' => 'input', 'values' => '');
$config->requirementinside->search['params']['dealUser']        = array('operator' => 'include', 'control' => 'select', 'values' => 'users');
$config->requirementinside->search['params']['product']      = array('operator' => 'include', 'control' => 'select', 'values' => '');
$config->requirementinside->search['params']['line']         = array('operator' => 'include', 'control' => 'select', 'values' => '');
$config->requirementinside->search['params']['sourceMode']       = array('operator' => '=', 'control' => 'select', 'values' => '');
$config->requirementinside->search['params']['sourceName']       = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->requirementinside->search['params']['union']       = array('operator' => 'include', 'control' => 'select', 'values' => '');
$config->requirementinside->search['params']['status']       = array('operator' => '=', 'control' => 'select', 'values' => $lang->requirementinside->searchstatusList);
$config->requirementinside->search['params']['method']       = array('operator' => '=', 'control' => 'select', 'values' => $lang->requirementinside->methodList);
$config->requirementinside->search['params']['app']          = array('operator' => 'include', 'control' => 'select', 'values' => array('0' => ''));
$config->requirementinside->search['params']['dept']         = array('operator' => '=', 'control' => 'select', 'values' => 'depts');
$config->requirementinside->search['params']['owner']        = array('operator' => 'include', 'control' => 'select', 'values' => 'users');
$config->requirementinside->search['params']['deadLine']     = array('operator' => '=', 'control' => 'input',  'values' => '', 'class' => 'date');
$config->requirementinside->search['params']['planEnd']          = array('operator' => '=', 'control' => 'input',  'values' => '', 'class' => 'date');
$config->requirementinside->search['params']['createdBy']    = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->requirementinside->search['params']['createdDate']  = array('operator' => '=', 'control' => 'input',  'values' => '', 'class' => 'date');

$config->requirementinside->opinion['id'] = '用户需求id';
$config->requirementinside->application['name']='应用系统';
$config->requirementinside->application['id']='应用系统';

$config->requirementinside->exportlist = new stdclass();
$config->requirementinside->exportlist->exportFields="code,name,ID,dealUser,status,method,opinion,deadLine,sourceMode,sourceName,union,
                        acceptTime,onlineTimeByDemand,app,productManager,projectManager,feedbackDealUser,feedbackStatus,parentCode,
                        entriesCode,feedbackCode,line,product,planEnd,dept,owner,createdBy,createdDate,editedBy,editedDate,closedBy,closedDate,
                        activatedBy,activatedDate,ignoredBy,ignoredDate,recoveryedBy,recoveryedDate,contact,feedbackBy,project,
                        desc,analysis,handling,implement,reviewComments,demands";

$config->requirementinside->exportlist->templateFields = explode(',', "name,opinionID,desc,app,status,onlineTimeByDemand,createdBy,projectManager,dealUser,comment");
$config->requirementinside->exportlist->listFields     = explode(',', "opinionID,app,dealUser,createdBy,projectManager,status");
