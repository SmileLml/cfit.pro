<?php
global $lang;

$config->maillog = new stdclass();
$config->maillog->list = new stdclass();
$config->maillog->list->exportFields = 'id,title,content,action,objectType,objectId,createdBy,createdDate,toList,ccList,status,error,emails';

$config->maillog->search['module'] = 'maillog';
$config->maillog->search['fields']['id']          = $lang->maillog->id;
$config->maillog->search['fields']['objectType']  = $lang->maillog->objectType;
$config->maillog->search['fields']['title']       = $lang->maillog->title;
$config->maillog->search['fields']['content']     = $lang->maillog->content;
$config->maillog->search['fields']['status']      = $lang->maillog->status;
$config->maillog->search['fields']['emails']      = $lang->maillog->userinfo;
$config->maillog->search['fields']['createdDate'] = $lang->maillog->createdDate;

$config->maillog->search['params']['id']           = array('operator' => '=', 'control' => 'input',  'values' => '');
$config->maillog->search['params']['objectType']   = array('operator' => '=', 'control' => 'select', 'values' => array('' => '') + $lang->maillog->objectTypeList);
$config->maillog->search['params']['title']        = array('operator' => 'include', 'control' => 'input',  'values' => '');
$config->maillog->search['params']['content']      = array('operator' => 'include', 'control' => 'input',  'values' => '');
$config->maillog->search['params']['emails']       = array('operator' => 'include', 'control' => 'input',  'values' => '');
$config->maillog->search['params']['status']       = array('operator' => '=', 'control' => 'select', 'values' => array('' => '') + $lang->maillog->statusList);
$config->maillog->search['params']['createdDate']  = array('operator' => '=', 'control' => 'input',  'values' => '', 'class' => 'date');
