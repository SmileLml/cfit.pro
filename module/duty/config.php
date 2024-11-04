<?php
$config->duty = new stdclass();
$config->duty->create = new stdclass();
$config->duty->edit   = new stdclass();
$config->duty->create->requiredFields = 'user,type,planDate';
$config->duty->edit->requiredFields   = $config->duty->create->requiredFields;

$config->duty->editor = new stdclass();
$config->duty->editor->create = array('id' => 'desc', 'tools' => 'simpleTools');
$config->duty->editor->edit   = array('id' => 'desc', 'tools' => 'simpleTools');
$config->duty->editor->view   = array('id' => 'desc,comment,lastComment', 'tools' => 'simpleTools');

$config->duty->list = new stdclass();
$config->duty->list->exportFields = 'id,planDate,user,actualDate,actualUser,application,importantTime,desc';

/* Search. */
global $lang;
$config->duty->search['module']                = 'duty';
$config->duty->search['fields']['id']          = $lang->duty->id;
$config->duty->search['fields']['application'] = $lang->duty->application;
$config->duty->search['fields']['type']        = $lang->duty->type;
$config->duty->search['fields']['user']        = $lang->duty->user;
$config->duty->search['fields']['planDate']    = $lang->duty->planDate;
$config->duty->search['fields']['createdBy']   = $lang->duty->createdBy;
$config->duty->search['fields']['createdDate'] = $lang->duty->createdDate;

$config->duty->search['params']['id']          = array('operator' => '=',       'control' => 'input',  'values' => '');
$config->duty->search['params']['application'] = array('operator' => '=',       'control' => 'select', 'values' => '');
$config->duty->search['params']['type']        = array('operator' => '=',       'control' => 'select', 'values' => $lang->duty->typeList);
$config->duty->search['params']['user']        = array('operator' => 'include', 'control' => 'select', 'values' => 'users');
$config->duty->search['params']['occurDate']   = array('operator' => '=',       'control' => 'input',  'values' => '', 'class' => 'date');
$config->duty->search['params']['createdBy']   = array('operator' => '=',       'control' => 'select', 'values' => 'users');
$config->duty->search['params']['createdDate'] = array('operator' => '=',       'control' => 'input',  'values' => '', 'class' => 'date');
