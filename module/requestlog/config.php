<?php
global $lang;

$config->requestlog->search['module'] = 'requestlog';

$config->requestlog->search['fields']['objectType']  = $lang->requestlog->objectType;
$config->requestlog->search['fields']['purpose']     = $lang->requestlog->purpose;
$config->requestlog->search['fields']['id']          = $lang->requestlog->id;
$config->requestlog->search['fields']['status']      = $lang->requestlog->status;
$config->requestlog->search['fields']['requestDate'] = $lang->requestlog->requestDate;

$config->requestlog->search['params']['objectType']  = array('operator' => '=', 'control' => 'select', 'values' => array('' => '') + $lang->requestlog->objectTypeList);
$config->requestlog->search['params']['purpose']     = array('operator' => '=', 'control' => 'select', 'values' => array('' => '') + $lang->requestlog->purposeList);
$config->requestlog->search['params']['id']          = array('operator' => '=', 'control' => 'input',  'values' => '');
$config->requestlog->search['params']['status']      = array('operator' => '=', 'control' => 'select', 'values' => array('' => '') + $lang->requestlog->statusList);
$config->requestlog->search['params']['requestDate'] = array('operator' => '=', 'control' => 'input',  'values' => '', 'class' => 'date');
$config->requestlog->search['fields']['response'] = $lang->requestlog->response;
$config->requestlog->search['params']['response'] = array('operator' => 'include', 'control' => 'input',  'values' => '');
$config->requestlog->search['fields']['params'] = $lang->requestlog->params;
$config->requestlog->search['params']['params'] = array('operator' => 'include', 'control' => 'input',  'values' => '');
