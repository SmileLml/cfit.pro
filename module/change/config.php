<?php
$config->change = new stdclass();
$config->change->create = new stdclass();
$config->change->edit   = new stdclass();
$config->change->change = new stdclass();
$config->change->link   = new stdclass();
$config->change->run   = new stdclass();
$config->change->recall   = new stdclass();
$config->change->create->requiredFields = 'level,category,type,isInteriorPro,isMasterPro,isSlavePro,consumed,reason,content,effect';
$config->change->edit->requiredFields   = $config->change->create->requiredFields;
$config->change->change->requiredFields = 'reviewer';
$config->change->link->requiredFields   = 'consumed,release';
$config->change->run->requiredFields   = 'consumed';
$config->change->recall->requiredFields   = 'comment';

$config->change->editor = new stdclass();
$config->change->editor->create   = array('id' => 'reason,content,effect', 'tools' => 'simpleTools');
$config->change->editor->edit     = array('id' => 'reason,content,effect', 'tools' => 'simpleTools');
$config->change->editor->confirm  = array('id' => 'conclusion,comment', 'tools' => 'simpleTools');
$config->change->editor->feedback = array('id' => 'reason,solution,progress', 'tools' => 'simpleTools');
$config->change->editor->view     = array('id' => 'comment,lastComment', 'tools' => 'simpleTools');
$config->change->editor->review   = array('id' => 'comment', 'tools' => 'simpleTools');
$config->change->editor->run      = array('id' => 'comment,result', 'tools' => 'simpleTools');
$config->change->editor->link     = array('id' => 'comment', 'tools' => 'simpleTools');
$config->change->editor->recall   = array('id' => 'comment', 'tools' => 'simpleTools');

$config->change->list = new stdclass();
$config->change->list->exportFields = 'code,status,type,category,level,reason,content,effect,isInteriorPro,isMasterPro,isSlavePro,project,archiveInfo,baseLineInfo,createdBy,createdDate,mailUsers';

/* Search. */
global $lang;
$config->change->search['module'] = 'change';
$config->change->search['fields']['code']        = $lang->change->code;
$config->change->search['fields']['status']      = $lang->change->status;
//$config->change->search['fields']['project']     = $lang->change->project;
$config->change->search['fields']['baseLineCondition']     = $lang->change->baseLineCondition;
//$config->change->search['fields']['app']         = $lang->change->app;
$config->change->search['fields']['createdBy']   = $lang->change->createdBy;
$config->change->search['fields']['createdDept']  = $lang->change->createdDept;
$config->change->search['fields']['createdDate'] = $lang->change->createdDate;
$config->change->search['fields']['category'] = $lang->change->category;

$config->change->search['params']['code']         = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->change->search['params']['name']         = array('operator' => 'include', 'control' => 'input',  'values' => '');
$config->change->search['params']['status']       = array('operator' => '=', 'control' => 'select', 'values' => $lang->change->statusList);
//$config->change->search['params']['project']         = array('operator' => 'include', 'control' => 'input',  'values' => '');
$config->change->search['params']['baseLineCondition']       = array('operator' => '=', 'control' => 'select', 'values' => array(''=>'')+$lang->change->condition);
//$config->change->search['params']['app']          = array('operator' => '=', 'control' => 'select', 'values' => '');
$config->change->search['params']['createdDept']  = array('operator' => 'belong', 'control' => 'select', 'values' => 'depts');
$config->change->search['params']['createdBy']    = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->change->search['params']['createdDate']  = array('operator' => '>=', 'control' => 'input', 'class' => 'date', 'values' => '');
$config->change->search['params']['category']       = array('operator' => '=', 'control' => 'select', 'values' => $lang->change->categoryList);