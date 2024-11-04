<?php

global $lang;
$config->cmdbsync->search['module'] = 'cmdbsync';
$config->cmdbsync->search['fields']['id']           = $lang->cmdbsync->id;
$config->cmdbsync->search['fields']['app']           = $lang->cmdbsync->app;
$config->cmdbsync->search['fields']['type'] = $lang->cmdbsync->type;
$config->cmdbsync->search['fields']['status'] = $lang->cmdbsync->status;
$config->cmdbsync->search['fields']['createdDate'] = $lang->cmdbsync->createdDate;
$config->cmdbsync->search['fields']['dealUser']     = $lang->cmdbsync->dealUser;
$config->cmdbsync->search['fields']['sendStatus'] = $lang->cmdbsync->sendStatus;


$config->cmdbsync->search['params']['id']             = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->cmdbsync->search['params']['app']              = array('operator' => 'include', 'control' => 'select', 'values' =>  '','mulit'=>true);
$config->cmdbsync->search['params']['type']            = array('operator' => '=', 'control' => 'select', 'values' =>  array('' => '') + $lang->cmdbsync->typeList);
$config->cmdbsync->search['params']['status']             = array('operator' => '=', 'control' => 'select', 'values' => array('' => '') + $lang->cmdbsync->statusList);
$config->cmdbsync->search['params']['createdDate']             = array('operator' => '=', 'control' => 'input',  'values' => '', 'class' => 'date');
$config->cmdbsync->search['params']['dealUser']           = array('operator' => 'include', 'control' => 'select', 'values' => 'users','mulit'=>true);
$config->cmdbsync->search['params']['sendStatus']           = array('operator' => '=', 'control' => 'select', 'values' => array('' => '') + $lang->cmdbsync->sendStatusList);

$config->cmdbsync->list   = new stdclass();
$config->cmdbsync->list->exportFields = 'id,app,type,status,createdDate,dealUser,sendStatus';