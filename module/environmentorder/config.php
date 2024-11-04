<?php

$config->environmentorder = new stdclass();

$config->environmentorder->objectType = "environmentorder";

$config->environmentorder->create = new stdclass();
$config->environmentorder->edit   = new stdclass();
$config->environmentorder->create->requiredFields = 'title,priority,origin,content';
$config->environmentorder->edit->requiredFields = $config->environmentorder->create->requiredFields;

// Search.
global $lang;
$config->environmentorder->search['module'] = 'environmentorder';
$config->environmentorder->search['fields']['code'] = $lang->environmentorder->code;
$config->environmentorder->search['fields']['title'] = $lang->environmentorder->title;
$config->environmentorder->search['fields']['priority'] = $lang->environmentorder->priority;
$config->environmentorder->search['fields']['origin'] = $lang->environmentorder->origin;
$config->environmentorder->search['fields']['content'] = $lang->environmentorder->content;
$config->environmentorder->search['fields']['finallytime'] = $lang->environmentorder->finallytime;
$config->environmentorder->search['fields']['createdBy'] = $lang->environmentorder->createdBy;
$config->environmentorder->search['fields']['reviewer'] = $lang->environmentorder->reviewer;
$config->environmentorder->search['fields']['executor'] = $lang->environmentorder->executor;
$config->environmentorder->search['fields']['dealUser'] = $lang->environmentorder->dealUser;
$config->environmentorder->search['fields']['status'] = $lang->environmentorder->status;
$config->environmentorder->search['fields']['createdTime'] = $lang->environmentorder->createdTime;

$config->environmentorder->search['params']['code'] = ['operator' => 'include', 'control' => 'input', 'values' => ''];
$config->environmentorder->search['params']['title'] = ['operator' => 'include', 'control' => 'input', 'values' => ''];
$config->environmentorder->search['params']['priority'] = ['operator' => '=', 'control' => 'select', 'values' => array(''=>'') + $lang->environmentorder->priorityList];
$config->environmentorder->search['params']['origin'] = ['operator' => '=', 'control' => 'select', 'values' => array(''=>'') + $lang->environmentorder->originList];
$config->environmentorder->search['params']['content'] = ['operator' => 'include', 'control' => 'input', 'values' => ''];
$config->environmentorder->search['params']['finallytime'] = ['operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date'];
$config->environmentorder->search['params']['createdBy'] = ['operator' => '=', 'control' => 'select', 'values' => ''];
$config->environmentorder->search['params']['reviewer'] = ['operator' => '=', 'control' => 'select', 'values' => ''];
$config->environmentorder->search['params']['executor'] = ['operator' => 'include', 'control' => 'select', 'values' => ''];
$config->environmentorder->search['params']['dealUser'] = ['operator' => 'include', 'control' => 'select', 'values' => ''];
$config->environmentorder->search['params']['status'] = ['operator' => '=', 'control' => 'select', 'values' => array(''=>'') + $lang->environmentorder->statusList];
$config->environmentorder->search['params']['createdTime'] = ['operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date'];;