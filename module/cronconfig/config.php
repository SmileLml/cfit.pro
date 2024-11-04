<?php
$config->cronconfig = new stdclass();
$config->cronconfig->create = new stdclass();
$config->cronconfig->edit   = new stdclass();
$config->cronconfig->delete   = new stdclass();
$config->cronconfig->create->requiredFields = 'command,remark,status';
$config->cronconfig->edit->requiredFields   = 'command,remark,status';
$config->cronconfig->delete->requiredFields   = 'comment';

$config->cronconfig->editor = new stdclass();
$config->cronconfig->editor->delete   = array('id' => 'comment', 'tools' => 'simpleTools');


global $lang;
$config->cronconfig->search['module'] = 'cronconfig';
$config->cronconfig->search['fields']['command']     = $lang->cronconfig->command;
$config->cronconfig->search['fields']['remark']      = $lang->cronconfig->remark;
$config->cronconfig->search['fields']['status']      = $lang->cronconfig->status;


$config->cronconfig->search['params']['command']      = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->cronconfig->search['params']['remark']       = array('operator' => 'include', 'control' => 'input',  'values' => '');
$config->cronconfig->search['params']['status']       = array('operator' => '=', 'control' => 'select', 'values' => $lang->cronconfig->statusList);
