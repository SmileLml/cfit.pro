<?php
$config->processimprove = new stdclass();
$config->processimprove->create   = new stdclass();
$config->processimprove->edit     = new stdclass();
$config->processimprove->feedback = new stdclass();
$config->processimprove->create->requiredFields   = 'desc';
$config->processimprove->edit->requiredFields     = $config->processimprove->create->requiredFields;
$config->processimprove->feedback->requiredFields = 'process,involved,isAccept,isDeploy,reviewedBy';

$config->processimprove->editor = new stdclass();
$config->processimprove->editor->create   = array('id' => 'desc,comment', 'tools' => 'simpleTools');
$config->processimprove->editor->edit     = array('id' => 'desc,comment', 'tools' => 'simpleTools');
$config->processimprove->editor->view     = array('id' => 'desc,lastComment', 'tools' => 'simpleTools');
$config->processimprove->editor->feedback = array('id' => 'comment,judge', 'tools' => 'simpleTools');
$config->processimprove->editor->close    = array('id' => 'comment', 'tools' => 'simpleTools');

$config->processimprove->export = new stdclass();
$config->processimprove->export->listFields     = explode(',', "source,createdDept,createdBy,createdDate,process,involved,isAccept,pri,isDeploy,reviewedBy,status");
$config->processimprove->export->templateFields = explode(',', "source,createdDept,createdBy,createdDate,process,involved,isAccept,pri,isDeploy,reviewedBy,status");


$config->processimprove->list = new stdclass();
$config->processimprove->list->exportFields = ' 
id,source,createdDept,createdBy,createdDate,process,involved,isAccept,pri,isDeploy,reviewedBy,status';

 /* Search. */
global $lang;
$config->processimprove->search['module'] = 'processimprove';
$config->processimprove->search['fields']['createdBy']   = $lang->processimprove->createdBy;
$config->processimprove->search['fields']['id']          = $lang->processimprove->id;
$config->processimprove->search['fields']['process']     = $lang->processimprove->process;
$config->processimprove->search['fields']['createdDept'] = $lang->processimprove->createdDept;
$config->processimprove->search['fields']['involved']    = $lang->processimprove->involved;
$config->processimprove->search['fields']['createdDate'] = $lang->processimprove->createdDate;
$config->processimprove->search['fields']['source']      = $lang->processimprove->source;
$config->processimprove->search['fields']['judge']       = $lang->processimprove->judge;
$config->processimprove->search['fields']['judgedBy']    = $lang->processimprove->judgedBy;
$config->processimprove->search['fields']['judgedDate']  = $lang->processimprove->judgedDate;
$config->processimprove->search['fields']['isAccept']    = $lang->processimprove->isAccept;
$config->processimprove->search['fields']['pri']         = $lang->processimprove->pri;
$config->processimprove->search['fields']['isDeploy']    = $lang->processimprove->isDeploy;
$config->processimprove->search['fields']['reviewedBy']  = $lang->processimprove->reviewedBy;

$config->processimprove->search['params']['createdBy']   = array('operator' => '=',       'control' => 'select', 'values' => 'users');
$config->processimprove->search['params']['id']          = array('operator' => '=',       'control' => 'input',  'values' => '');
$config->processimprove->search['params']['process']     = array('operator' => '=',       'control' => 'select', 'values' => $lang->processimprove->processList);
$config->processimprove->search['params']['createdDept'] = array('operator' => 'include', 'control' => 'select', 'values' => 'depts');
$config->processimprove->search['params']['involved']    = array('operator' => '=',       'control' => 'select', 'values' => $lang->processimprove->involvedList);
$config->processimprove->search['params']['createdDate'] = array('operator' => '=',       'control' => 'input',  'values' => '', 'class' => 'date');
$config->processimprove->search['params']['source']      = array('operator' => '=',       'control' => 'select', 'values' => $lang->processimprove->sourceList);
$config->processimprove->search['params']['judge']       = array('operator' => 'include', 'control' => 'input',  'values' => '');
$config->processimprove->search['params']['judgedBy']    = array('operator' => '=',       'control' => 'select', 'values' => 'users');
$config->processimprove->search['params']['judgedDate']  = array('operator' => '=',       'control' => 'select', 'values' => '', 'class' => 'date');
$config->processimprove->search['params']['isAccept']    = array('operator' => '=',       'control' => 'select', 'values' => $lang->processimprove->isAcceptList);
$config->processimprove->search['params']['pri']         = array('operator' => '=',       'control' => 'select', 'values' => $lang->processimprove->priorityList);
$config->processimprove->search['params']['isDeploy']    = array('operator' => '=',       'control' => 'select', 'values' => $lang->processimprove->isDeployList);
$config->processimprove->search['params']['reviewedBy']  = array('operator' => '=',       'control' => 'select', 'values' => 'users');
