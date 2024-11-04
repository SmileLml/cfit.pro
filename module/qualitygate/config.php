<?php
$config->qualitygate = new stdclass();
$config->qualitygate->objectType = "qualitygate";
$config->qualitygate->create = new stdclass();
$config->qualitygate->edit   = new stdclass();
$config->qualitygate->delete = new stdclass();
$config->qualitygate->list = new stdclass();
$config->qualitygate->review = new stdclass();

$config->qualitygate->create->requiredFields = 'projectId,productId,productVersion,status';
$config->qualitygate->edit->requiredFields   = $config->qualitygate->create->requiredFields;

/**
 * 允许设置质量门禁的部门列表
 */
$config->qualitygate->allowQualityGateDeptIds = '';

/* Search. */
global $lang;
$config->qualitygate->search['module'] = 'qualitygate';
$config->qualitygate->search['fields']['code'] = $lang->qualitygate->code;
$config->qualitygate->search['fields']['productName'] = $lang->qualitygate->productId;
$config->qualitygate->search['fields']['productCode'] = $lang->qualitygate->productCode;
$config->qualitygate->search['fields']['buildName'] = $lang->qualitygate->buildName;
$config->qualitygate->search['fields']['buildStatus'] = $lang->qualitygate->buildStatus;
$config->qualitygate->search['fields']['status'] = $lang->qualitygate->status;

$config->qualitygate->search['params']['code'] = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->qualitygate->search['params']['productName'] = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->qualitygate->search['params']['productCode'] = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->qualitygate->search['params']['buildName'] = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->qualitygate->search['params']['buildStatus'] = ['operator' => '=', 'control' => 'select', 'values' => ''];
$config->qualitygate->search['params']['status'] = ['operator' => '=', 'control' => 'select', 'values' => array(''=>'') + $lang->qualitygate->statusList];

$config->qualitygate->editor                 = new stdclass();
$config->qualitygate->editor->delete         = ['id' => 'comment', 'tools' => 'simpleTools'];