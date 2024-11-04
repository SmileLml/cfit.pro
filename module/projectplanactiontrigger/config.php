<?php
$config->projectplanactiontrigger = new stdclass();

$config->projectplanactiontrigger->list = new stdclass();
$config->projectplanactiontrigger->list->exportFields = 'id,planName,actionUser,actionDay,status,snapshotVersion';

global $lang;
$config->projectplanactiontrigger->search['module'] = 'projectplanactiontrigger';
$config->projectplanactiontrigger->search['fields']['id']                     = $lang->projectplanactiontrigger->id;
$config->projectplanactiontrigger->search['fields']['actionDay']                     = $lang->projectplanactiontrigger->actionDay;
$config->projectplanactiontrigger->search['fields']['planID']                     = $lang->projectplanactiontrigger->planID;
$config->projectplanactiontrigger->search['fields']['actionUser']                     = $lang->projectplanactiontrigger->actionUser;
$config->projectplanactiontrigger->search['fields']['status']                     = $lang->projectplanactiontrigger->status;

$config->projectplanactiontrigger->search['fields']['snapshotVersion']                     = $lang->projectplanactiontrigger->snapshotVersion;

$config->projectplanactiontrigger->search['params']['id']                       = array('operator' => '=', 'control' => 'input', 'values' => '');
$config->projectplanactiontrigger->search['params']['actionDay']                       = array('operator' => '=', 'control' => 'input','class' => 'date', 'values' => '');
$config->projectplanactiontrigger->search['params']['planID']                       = array('operator' => '=', 'control' => 'select', 'values' => '');
$config->projectplanactiontrigger->search['params']['actionUser']                       = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->projectplanactiontrigger->search['params']['status']                       = array('operator' => '=', 'control' => 'select', 'values' => $lang->projectplanactiontrigger->statusList);
$config->projectplanactiontrigger->search['params']['snapshotVersion']                       = array('operator' => 'include', 'control' => 'input', 'values' => $lang->projectplanactiontrigger->statusList);

