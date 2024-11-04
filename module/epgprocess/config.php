<?php
$config->epgprocess = new stdclass();
$config->epgprocess->create = new stdclass();
$config->epgprocess->edit   = new stdclass();
$config->epgprocess->create->requiredFields = 'name,host';
$config->epgprocess->edit->requiredFields   = $config->epgprocess->create->requiredFields;

$config->epgprocess->editor = new stdclass();
$config->epgprocess->editor->create = array('id' => 'desc', 'tools' => 'simpleTools');
$config->epgprocess->editor->edit   = array('id' => 'desc', 'tools' => 'simpleTools');
$config->epgprocess->editor->view   = array('id' => 'desc', 'tools' => 'simpleTools');

 /* Search. */
global $lang;
$config->epgprocess->search['module'] = 'epgprocess';
$config->epgprocess->search['fields']['id']          = $lang->epgprocess->id;
$config->epgprocess->search['fields']['name']        = $lang->epgprocess->name;
$config->epgprocess->search['fields']['host']        = $lang->epgprocess->host;
$config->epgprocess->search['fields']['createdBy']   = $lang->epgprocess->createdBy;
$config->epgprocess->search['fields']['createdDate'] = $lang->epgprocess->createdDate;

$config->epgprocess->search['params']['id']          = array('operator' => '=',       'control' => 'input',  'values' => '');
$config->epgprocess->search['params']['name']        = array('operator' => 'include', 'control' => 'input',  'values' => '');
$config->epgprocess->search['params']['host']        = array('operator' => 'include', 'control' => 'input',  'values' => '');
$config->epgprocess->search['params']['createdBy']   = array('operator' => '=',       'control' => 'select', 'values' => 'users');
$config->epgprocess->search['params']['createdDate'] = array('operator' => '=',       'control' => 'input',  'values' => '', 'class' => 'date');
