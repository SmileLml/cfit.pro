<?php
$config->componentpublic= new stdclass();
$config->componentpublic->editor = new stdclass();
$config->componentpublic->create = new stdclass();
$config->componentpublic->editinfo = new stdclass();
$config->componentpublic->createversion = new stdclass();
$config->componentpublic->editversion = new stdclass();
$config->componentpublic->editpmrminfo = new stdclass();
$config->componentpublic->editor->create = array('id' => 'functionDesc', 'tools' => 'simpleTools');
$config->componentpublic->editor->edit = array('id' => 'functionDesc', 'tools' => 'simpleTools');
$config->componentpublic->editor->editinfo = array('id' => 'functionDesc', 'tools' => 'simpleTools');
$config->componentpublic->editor->createversion = array('id' => 'desc', 'tools' => 'simpleTools');
$config->componentpublic->editor->editversion = array('id' => 'desc', 'tools' => 'simpleTools');
$config->componentpublic->editor->deleteversion = array('id' => 'comment', 'tools' => 'simpleTools');
$config->componentpublic->editor->delete = array('id' => 'comment', 'tools' => 'simpleTools');
$config->componentpublic->list = new stdclass();
$config->componentpublic->list->exportFields = 'name,latestVersion,level,category,functionDesc,maintainer,maintainerDept,developLanguage,status,usedNum';


$config->componentpublic->create->requiredFields='name,latestVersion,level,category,functionDesc,location,maintainer,developLanguage,status';
$config->componentpublic->editinfo->requiredFields='latestVersion,functionDesc,location,maintainer,developLanguage,status';
$config->componentpublic->createversion->requiredFields='version,updatedDate,desc';
$config->componentpublic->editversion->requiredFields='updatedDate,desc';
//code,
$config->componentpublic->editpmrminfo->requiredFields='name,latestVersion,functionDesc,location,maintainer,developLanguage,status,level,category';

/* Search. */
global $lang;
$config->componentpublic->search['module'] = 'componentpublic';
$config->componentpublic->search['fields']['name']                     = $lang->componentpublic->name;
$config->componentpublic->search['fields']['latestVersion']                     = $lang->componentpublic->latestVersion;
$config->componentpublic->search['fields']['level']                     = $lang->componentpublic->level;
$config->componentpublic->search['fields']['category']                     = $lang->componentpublic->category;
$config->componentpublic->search['fields']['functionDesc']                     = $lang->componentpublic->functionDesc;
$config->componentpublic->search['fields']['location']                     = $lang->componentpublic->location;
$config->componentpublic->search['fields']['maintainer']                     = $lang->componentpublic->maintainer;
$config->componentpublic->search['fields']['maintainerDept']                     = $lang->componentpublic->maintainerDept;
$config->componentpublic->search['fields']['developLanguage']                     = $lang->componentpublic->developLanguage;
$config->componentpublic->search['fields']['status']                     = $lang->componentpublic->status;


$config->componentpublic->search['params']['name']                       = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->componentpublic->search['params']['latestVersion']                       = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->componentpublic->search['params']['level']                       = array('operator' => '=', 'control' => 'select', 'values' => array('' => '') + $lang->componentpublic->levelList);
$config->componentpublic->search['params']['category']                       = array('operator' => '=', 'control' => 'select', 'values' => array());
$config->componentpublic->search['params']['functionDesc']                       = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->componentpublic->search['params']['location']                       = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->componentpublic->search['params']['maintainer']                       = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->componentpublic->search['params']['maintainerDept']                       = array('operator' => '=', 'control' => 'select', 'values' => array());
$config->componentpublic->search['params']['developLanguage']                       = array('operator' => '=', 'control' => 'select', 'values' => array());
$config->componentpublic->search['params']['status']                       = array('operator' => '=', 'control' => 'select', 'values' => array());