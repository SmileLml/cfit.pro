<?php
global $app;
$app->loadLang('project');
$config->consumed->search['module'] = 'project';
$config->consumed->search['fields']['name'] = $this->lang->project->name;
$config->consumed->search['fields']['code'] = $this->lang->project->code;

$config->consumed->search['params']['name'] = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->consumed->search['params']['code'] = array('operator' => 'include', 'control' => 'input', 'values' => '');
