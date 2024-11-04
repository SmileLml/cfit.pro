<?php
$config->component= new stdclass();
$config->component->editor = new stdclass();
$config->component->create = new stdclass();
$config->component->edit = new stdclass();
$config->component->create->newThirdParty = new stdclass();
$config->component->create->newPublic = new stdclass();

$config->component->editor->create = array('id' => 'applicationReason,evidence,functionDesc', 'tools' => 'simpleTools');
$config->component->editor->edit = array('id' => 'applicationReason,evidence,functionDesc', 'tools' => 'simpleTools');
$config->component->create->newThirdParty->requiredFields ='type,applicationMethod,newThirdPartyName,newThirdPartyVersion,newThirdPartyDevelopLanguage,newThirdPartyProjectId,licenseType,applicationReason,evidence';
$config->component->create->newPublic->requiredFields='type,applicationMethod,level,newPublicName,newPublicVersion,newPublicDevelopLanguage,location,newPublicProjectId,functionDesc,maintainer,hasProfessionalReview';
$config->component->create->deletItems='newThirdPartyName,newThirdPartyVersion,newThirdPartyDevelopLanguage,newThirdPartyProjectId,newPublicName,newPublicVersion,newPublicDevelopLanguage,newPublicProjectId';
$config->component->edit->deletItems='newThirdPartyName,newThirdPartyVersion,newThirdPartyDevelopLanguage,newThirdPartyProjectId,newPublicName,newPublicVersion,newPublicDevelopLanguage,newPublicProjectId';
$config->component->editor->review = array('id' => 'rejectReason,dealcomment', 'tools' => 'simpleTools');
$config->component->editor->editcomment = array('id' => 'reviewOpinion', 'tools' => 'simpleTools');

$config->component->list = new stdclass();
//id,
$config->component->list->exportFields = 'name,componentType,level,application,version,project,status,dealUser,createdBy,createdDept,createdDate';

/* Search. */
global $lang;
$config->component->search['module'] = 'component';
$config->component->search['fields']['name']                     = $lang->component->name;
$config->component->search['fields']['type']                     = $lang->component->componentType;
$config->component->search['fields']['level']                    = $lang->component->level;
$config->component->search['fields']['applicationMethod']                    = $lang->component->application;
$config->component->search['fields']['version']                    = $lang->component->version;
$config->component->search['fields']['projectId']                    = $lang->component->project;
$config->component->search['fields']['status']                    = $lang->component->status;
//$config->component->search['fields']['reviewStage']                    = $lang->component->reviewStage;
$config->component->search['fields']['dealUser']                    = $lang->component->dealUser;
$config->component->search['fields']['createdBy']                    = $lang->component->createdBy;
$config->component->search['fields']['createdDept']                    = $lang->component->createdDept;
$config->component->search['fields']['createdDate']                    = $lang->component->createdDate;
/*$config->component->search['fields']['id']                    = $lang->component->id;*/


$config->component->search['params']['name']                       = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->component->search['params']['type']                       = array('operator' => '=', 'control' => 'select', 'values' => array('' => '')+$lang->component->type);
$config->component->search['params']['level']                       = array('operator' => '=', 'control' => 'select', 'values' => array('' => '')+$lang->component->levelList);
$config->component->search['params']['applicationMethod']                       = array('operator' => '=', 'control' => 'select', 'values' => array('' => '')+$lang->component->applicationMethod);
$config->component->search['params']['version']                       = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->component->search['params']['projectId']                       = array('operator' => 'include', 'control' => 'select', 'values' => array(0 => ''));
$config->component->search['params']['status']                       = array('operator' => '=', 'control' => 'select', 'values' => array('' => '')+$lang->component->statusList);
$config->component->search['params']['reviewStage']                       = array('operator' => '=', 'control' => 'select', 'values' => array('' => '')+$lang->component->reviewStageList);
$config->component->search['params']['dealUser']                       = array('operator' => 'include', 'control' => 'select', 'values' => 'users');
$config->component->search['params']['createdBy']                       = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->component->search['params']['createdDept']                       = array('operator' => '=', 'control' => 'select', 'values' => array());
$config->component->search['params']['createdDate']                       = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');