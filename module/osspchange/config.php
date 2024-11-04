<?php
$config->osspchange          = new stdclass();
$config->osspchange->create  = new stdclass();
$config->osspchange->create->requiredFields       = 'proposer,title,background,content,files';
//$config->osspchange->confirm->requiredFields      = 'systemProcess,systemVersion,advise,result,changeNotice,systemDept,systemManager,QMDmanager';

$config->osspchange->liasisonOfficer = '';
$config->osspchange->datatable = new stdclass();
$config->osspchange->datatable->defaultField = array('id', 'code', 'proposer', 'createdDate', 'title', 'systemProcess', 'systemVersion', 'closeResult', 'status', 'dealuser', 'actions');

$config->osspchange->datatable->fieldList['id']['title'] = 'idAB';
$config->osspchange->datatable->fieldList['id']['fixed'] = 'left';
$config->osspchange->datatable->fieldList['id']['width'] = '60';
$config->osspchange->datatable->fieldList['id']['required'] = 'yes';

$config->osspchange->datatable->fieldList['code']['title'] = 'code';
$config->osspchange->datatable->fieldList['code']['fixed'] = 'left';
$config->osspchange->datatable->fieldList['code']['width'] = '100';
$config->osspchange->datatable->fieldList['code']['required'] = 'yes';

$config->osspchange->datatable->fieldList['proposer']['title'] = 'proposer';
$config->osspchange->datatable->fieldList['proposer']['fixed'] = 'left';
$config->osspchange->datatable->fieldList['proposer']['width'] = '80';
$config->osspchange->datatable->fieldList['proposer']['required'] = 'yes';

$config->osspchange->datatable->fieldList['createdDate']['title'] = 'createdDate';
$config->osspchange->datatable->fieldList['createdDate']['fixed'] = 'left';
$config->osspchange->datatable->fieldList['createdDate']['width'] = '120';
$config->osspchange->datatable->fieldList['createdDate']['required'] = 'yes';

$config->osspchange->datatable->fieldList['title']['title'] = 'title';
$config->osspchange->datatable->fieldList['title']['fixed'] = 'right';
$config->osspchange->datatable->fieldList['title']['width'] = '300';
$config->osspchange->datatable->fieldList['title']['required'] = 'no';

$config->osspchange->datatable->fieldList['systemProcess']['title'] = 'systemProcess';
$config->osspchange->datatable->fieldList['systemProcess']['fixed'] = 'right';
$config->osspchange->datatable->fieldList['systemProcess']['width'] = '150';
$config->osspchange->datatable->fieldList['systemProcess']['required'] = 'no';

$config->osspchange->datatable->fieldList['systemVersion']['title'] = 'systemVersion';
$config->osspchange->datatable->fieldList['systemVersion']['fixed'] = 'right';
$config->osspchange->datatable->fieldList['systemVersion']['width'] = '150';
$config->osspchange->datatable->fieldList['systemVersion']['required'] = 'yes';

$config->osspchange->datatable->fieldList['closeResult']['title'] = 'reviewResult';
$config->osspchange->datatable->fieldList['closeResult']['fixed'] = 'right';
$config->osspchange->datatable->fieldList['closeResult']['width'] = '120';
$config->osspchange->datatable->fieldList['closeResult']['required'] = 'yes';

$config->osspchange->datatable->fieldList['status']['title']     = 'status';
$config->osspchange->datatable->fieldList['status']['fixed']     = 'no';
$config->osspchange->datatable->fieldList['status']['width']     = '120';
$config->osspchange->datatable->fieldList['status']['required']  = 'no';

$config->osspchange->datatable->fieldList['dealuser']['title']     = 'dealuser';
$config->osspchange->datatable->fieldList['dealuser']['fixed']     = 'no';
$config->osspchange->datatable->fieldList['dealuser']['width']     = '120';
$config->osspchange->datatable->fieldList['dealuser']['required']  = 'no';

$config->osspchange->datatable->fieldList['actions']['title'] = 'actions';
$config->osspchange->datatable->fieldList['actions']['fixed'] = 'right';
$config->osspchange->datatable->fieldList['actions']['width'] = '150';
$config->osspchange->datatable->fieldList['actions']['required'] = 'yes';

global $lang;
$config->osspchange->search['module'] = 'osspchange';
$config->osspchange->search['fields']['id'] = $lang->idAB;
$config->osspchange->search['fields']['code'] = $lang->osspchange->code;
$config->osspchange->search['fields']['proposer'] = $lang->osspchange->proposer;
$config->osspchange->search['fields']['createdDate'] = $lang->osspchange->createdDate;
$config->osspchange->search['fields']['title'] = $lang->osspchange->title;
$config->osspchange->search['fields']['systemProcess'] = $lang->osspchange->systemProcess;
$config->osspchange->search['fields']['systemVersion'] = $lang->osspchange->systemVersion;
$config->osspchange->search['fields']['status'] = $lang->osspchange->status;
$config->osspchange->search['fields']['dealuser'] = $lang->osspchange->dealuser;


$config->osspchange->search['params']['id'] = array('operator' => '=', 'control' => 'input', 'values' => '');
$config->osspchange->search['params']['code'] = array('operator' => '=', 'control' => 'input', 'values' => '');
$config->osspchange->search['params']['proposer'] = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->osspchange->search['params']['createdDate'] = array('operator' => '>=', 'control' => 'input', 'class' => 'date', 'values' => '');
$config->osspchange->search['params']['title'] = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->osspchange->search['params']['systemProcess'] = array('operator' => '=', 'control' => 'select', 'values' => '');
$config->osspchange->search['params']['systemVersion'] = array('operator' => '=', 'control' => 'select', 'values' => '');
$config->osspchange->search['params']['status'] = array('operator' => '=', 'control' => 'select', 'values' => $lang->osspchange->searchStatusList);
$config->osspchange->search['params']['dealuser'] = array('operator' => 'include', 'control' => 'select', 'values' => 'users');


$config->osspchange->editor = new stdclass();
$config->osspchange->editor->create    = array('id' => 'background,content', 'tools' => 'simple');
$config->osspchange->editor->edit      = array('id' => 'background,content', 'tools' => 'simple');
$config->osspchange->editor->confirm   = array('id' => 'background,content,advise', 'tools' => 'simple');
$config->osspchange->editor->review    = array('id' => 'comment', 'tools' => 'simple');
$config->osspchange->editor->close     = array('id' => 'fileInfo,closeComment', 'tools' => 'simple');