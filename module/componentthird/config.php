<?php
$config->componentthird= new stdclass();
$config->componentthird->editor = new stdclass();
$config->componentthird->create = new stdclass();
$config->componentthird->editinfo = new stdclass();
$config->componentthird->createversion = new stdclass();
$config->componentthird->editversion = new stdclass();
$config->componentthird->editor->deleteversion = array('id' => 'comment', 'tools' => 'simpleTools');
$config->componentthird->editor->delete = array('id' => 'comment', 'tools' => 'simpleTools');
//chineseClassify, 2023-12-04去掉中文配置项
$config->componentthird->create->requiredFields='name,category,englishClassify,licenseType,developLanguage';
$config->componentthird->createversion->requiredFields='version,updatedDate,vulnerabilityLevel';
$config->componentthird->editversion->requiredFields='updatedDate,vulnerabilityLevel';

$config->componentthird->list = new stdclass();
$config->componentthird->list->exportFields = 'name,status,baseline,recommendVersion,category,chineseClassify,englishClassify,licenseType,developLanguage,usedNum';

/* Search. */
global $lang;
$config->componentthird->search['module'] = 'componentthird';
$config->componentthird->search['fields']['name']                     = $lang->componentthird->name;
$config->componentthird->search['fields']['recommendVersion']                     = $lang->componentthird->recommendVersion;
$config->componentthird->search['fields']['versionDate']                     = $lang->componentthird->versionDate;
$config->componentthird->search['fields']['category']                     = $lang->componentthird->category;
$config->componentthird->search['fields']['chineseClassify']                     = $lang->componentthird->chineseClassify;
$config->componentthird->search['fields']['englishClassify']                     = $lang->componentthird->englishClassify;
$config->componentthird->search['fields']['licenseType']                     = $lang->componentthird->licenseType;
$config->componentthird->search['fields']['developLanguage']                     = $lang->componentthird->developLanguage;


$config->componentthird->search['params']['name']                       = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->componentthird->search['params']['recommendVersion']                       = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->componentthird->search['params']['versionDate']                       = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->componentthird->search['params']['category']                       = array('operator' => '=', 'control' => 'select', 'values' => array());
$config->componentthird->search['params']['chineseClassify']                       = array('operator' => '=', 'control' => 'select', 'values' => array());
$config->componentthird->search['params']['englishClassify']                       = array('operator' => '=', 'control' => 'select', 'values' => array());
$config->componentthird->search['params']['licenseType']                       = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->componentthird->search['params']['developLanguage']                       = array('operator' => '=', 'control' => 'select', 'values' => array());