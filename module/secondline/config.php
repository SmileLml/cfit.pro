<?php
global $app;
$app->loadLang('product');
$config->secondline->search['module'] = 'secondline';
$config->secondline->search['fields']['name'] = $this->lang->product->name;
$config->secondline->search['fields']['code'] = $this->lang->product->code;
$config->secondline->search['fields']['app'] = $this->lang->product->app;
$config->secondline->search['fields']['belongDeptIds'] = $this->lang->product->belongDeptIds;

$config->secondline->search['params']['name'] = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->secondline->search['params']['code'] = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->secondline->search['params']['app']   = array('operator' => '=',       'control' => 'select', 'values' => '');
$config->secondline->search['params']['belongDeptIds'] = array('operator' => 'include', 'control' => 'select', 'values' => 'depts');

