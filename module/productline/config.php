<?php
$config->productline = new stdclass();
$config->productline->create = new stdclass();
$config->productline->edit   = new stdclass();
$config->productline->create->requiredFields = 'name,code,depts';
$config->productline->edit->requiredFields = $config->productline->create->requiredFields;

$config->productline->editor = new stdclass();
$config->productline->editor->create = array('id' => 'desc,comment', 'tools' => 'simpleTools');
$config->productline->editor->edit   = array('id' => 'desc,comment', 'tools' => 'simpleTools');
$config->productline->editor->view   = array('id' => 'desc,comment', 'tools' => 'simpleTools');

/* Search. */
global $lang;
$config->productline->search['module'] = 'productline';
$config->productline->search['fields'] = array();
$config->productline->search['fields']['code'] = $lang->productline->code;
$config->productline->search['fields']['desc'] = $lang->productline->desc;
$config->productline->search['fields']['depts'] = $lang->productline->dept;
$config->productline->search['fields']['name'] = $lang->productline->name;


$config->productline->search['params']['code'] = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->productline->search['params']['name'] = array('operator' => 'include', 'control' => 'input',  'values' => '');
$config->productline->search['params']['desc'] = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->productline->search['params']['depts']  = array('operator' => 'include', 'control' => 'select', 'values' => 'depts');