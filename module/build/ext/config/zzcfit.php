<?php
$config->build->search['fields']['purpose'] = $lang->build->purpose;
$config->build->search['fields']['rounds']  = $lang->build->rounds;

$config->build->search['fields']['version']  = $lang->build->version;
//$config->build->search['fields']['cm']  = $lang->build->cm;
$config->build->search['fields']['testUser']  = $lang->build->testUser;
$config->build->search['fields']['verifyUser']  = $lang->build->verifyUser;
$config->build->search['fields']['problemid']  = $lang->build->problemid;
$config->build->search['fields']['demandid']  = $lang->build->demandid;
$config->build->search['fields']['sendlineId']  = $lang->build->sendlineId;
$config->build->search['fields']['createdBy']  = $lang->build->createdBy;
$config->build->search['fields']['createdDate']  = $lang->build->createdDate;
$config->build->search['fields']['editedBy']  = $lang->build->editedBy;
$config->build->search['fields']['editedDate']  = $lang->build->editedDate;

$config->build->search['params']['purpose'] = array('operator' => '=', 'control' => 'select', 'values' => $lang->build->purposeList);
$config->build->search['params']['rounds']  = array('operator' => '=', 'control' => 'select', 'values' => $lang->build->roundsList);

$config->build->search['params']['version']  = array('operator' => '=', 'control' => 'select', 'values' => '');
//$config->build->search['params']['cm']       = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->build->search['params']['testUser']  = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->build->search['params']['verifyUser']  = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->build->search['params']['problemid']  = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->build->search['params']['demandid']  = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->build->search['params']['sendlineId']  = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->build->search['params']['createdBy']  = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->build->search['params']['createdDate']  = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->build->search['params']['editedBy']  = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->build->search['params']['editedDate']  = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');
