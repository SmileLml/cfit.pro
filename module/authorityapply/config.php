<?php
$config->authorityapply = new stdclass();
$config->authorityapply->objectType = 'authorityapply';
$config->authorityapply->create = new stdclass();
$config->authorityapply->edit   = new stdclass();
$config->authorityapply->delete = new stdclass();
$config->authorityapply->list = new stdclass();
$config->authorityapply->review = new stdclass();
$config->authorityapply->create->requiredFields = 'summary,createdBy,applyDepartment,project,application,content,reason,approvalDepartment';
$config->authorityapply->edit->requiredFields = $config->authorityapply->create->requiredFields;
/*
 * 多选下拉字段
 */
$config->authorityapply->multipleSelectFields = ['project','application','product','approvalDepartment'];

$config->authorityapply->delete = new stdclass();
$config->authorityapply->delete->requiredFields = 'comment';

$config->authorityapply->editor = new stdclass();
$config->authorityapply->editor->create = array('id' => 'reason', 'tools' => 'simpleTools','minHeight'=>'50px','height'=>'20px');
$config->authorityapply->editor->edit   = array('id' => 'reason', 'tools' => 'simpleTools','minHeight'=>'50px','height'=>'20px');

// Search.
global $lang;

$config->authorityapply->search['module'] = 'authorityapply';
$config->authorityapply->search['fields']['code'] = $lang->authorityapply->code;
$config->authorityapply->search['fields']['summary'] = $lang->authorityapply->summary;
$config->authorityapply->search['fields']['createdBy'] = $lang->authorityapply->createdBy;
$config->authorityapply->search['fields']['dealUser'] = $lang->authorityapply->dealUser;
$config->authorityapply->search['fields']['subSystem'] = $lang->authorityapply->subSystem;
$config->authorityapply->search['fields']['status'] = $lang->authorityapply->status;
$config->authorityapply->search['fields']['createdTime'] = $lang->authorityapply->createdTime;

$config->authorityapply->search['params']['code'] = ['operator' => 'include', 'control' => 'input', 'values' => ''];
$config->authorityapply->search['params']['summary'] = ['operator' => 'include', 'control' => 'input', 'values' => ''];
$config->authorityapply->search['params']['subSystem'] = ['operator' => 'include', 'control' => 'select', 'values' =>  array(''=>'') + $lang->authorityapply->subSystemList];
$config->authorityapply->search['params']['status'] = ['operator' => '=', 'control' => 'select', 'values' => array(''=>'') + $lang->authorityapply->searchStatusList];
$config->authorityapply->search['params']['createdTime'] = ['operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date'];;

