<?php
$config->productionchange               = new stdclass();
$config->productionchange->create       = new stdclass();
$config->productionchange->edit         = new stdclass();

$config->productionchange->create->requiredFields       = 'applicant,applicantDept,onlineType,application,onlineStart,onlineEnd,abstract,implementContent,effect,ifEffectSystem,materialExplain,interfacePerson,operationPerson,mediaPackage,mailto';
$config->productionchange->edit->requiredFields         = 'applicant,applicantDept,onlineType,application,onlineStart,onlineEnd,abstract,implementContent,effect,ifEffectSystem,materialExplain,interfacePerson,operationPerson,mediaPackage,mailto';

$config->productionchange->editor = new stdclass();
$config->productionchange->editor->create     = array('id' => 'implementContent,effect,materialExplain,effectSystemExplain', 'tools' => 'simpleTools');
$config->productionchange->editor->edit       = array('id' => 'implementContent,effect,materialExplain,effectSystemExplain', 'tools' => 'simpleTools');
$config->productionchange->editor->deal       = array('id' => '', 'tools' => 'simpleTools');
$config->productionchange->editor->review     = array('id' => 'record,remark', 'tools' => 'simpleTools');

$config->productionchange->export = new stdclass();
$config->productionchange->export->listFields     = explode(',', "id,code,applicant,applicantDept,onlineType,status,dealUser,createdBy,createdDate,application,abstract,onlineStart,onlineEnd, implementContent,effect, ifEffectSystem,effectSystemExplain, materialExplain, space,correlationPublish,correlationDemand,correlationProblem,correlationSecondorder,ifReport,deptConfirmPerson,interfacePerson,mediaPackage,operationPerson");

$config->productionchange->list = new stdclass();
$config->productionchange->list->exportFields    = "id,code,applicant,applicantDept,onlineType,status,dealUser,createdBy,createdDate,application,abstract,onlineStart,onlineEnd, implementContent,effect, ifEffectSystem,effectSystemExplain, materialExplain, record,remark,space,correlationPublish,correlationDemand,correlationProblem,correlationSecondorder,ifReport,deptConfirmPerson,interfacePerson,mediaPackage,operationPerson";


/* Search. */
global $lang;
$config->productionchange->search['module'] = 'productionchange';
$config->productionchange->search['fields']['id']                   = $lang->productionchange->idAB;
$config->productionchange->search['fields']['code']                 = $lang->productionchange->code;
$config->productionchange->search['fields']['applicant']            = $lang->productionchange->applicant;
$config->productionchange->search['fields']['applicantDept']        = $lang->productionchange->applicantDept;
$config->productionchange->search['fields']['onlineType']           = $lang->productionchange->onlineType;
$config->productionchange->search['fields']['status']               = $lang->productionchange->status;
$config->productionchange->search['fields']['dealUser']             = $lang->productionchange->dealUser;
$config->productionchange->search['fields']['createdBy']            = $lang->productionchange->createdBy;
$config->productionchange->search['fields']['createdDate']          = $lang->productionchange->createdDate;
$config->productionchange->search['fields']['application']          = $lang->productionchange->application;
$config->productionchange->search['fields']['onlineStart']          = $lang->productionchange->onlineStart;
$config->productionchange->search['fields']['onlineEnd']            = $lang->productionchange->onlineEnd;
$config->productionchange->search['fields']['abstract']             = $lang->productionchange->abstract;
$config->productionchange->search['fields']['implementContent']     = $lang->productionchange->implementContent;
$config->productionchange->search['fields']['effect']               = $lang->productionchange->effect;
$config->productionchange->search['fields']['ifEffectSystem']       = $lang->productionchange->ifEffectSystem;
$config->productionchange->search['fields']['effectSystemExplain']  = $lang->productionchange->effectSystemExplain;
$config->productionchange->search['fields']['materialExplain']      = $lang->productionchange->materialExplain;
$config->productionchange->search['fields']['correlationPublish']   = $lang->productionchange->correlationPublish;
$config->productionchange->search['fields']['space']                = $lang->productionchange->space;
$config->productionchange->search['fields']['releaseRecord']        = $lang->productionchange->releaseRecord;
$config->productionchange->search['fields']['correlationDemand']    = $lang->productionchange->correlationDemand;
$config->productionchange->search['fields']['correlationProblem']   = $lang->productionchange->correlationProblem;
$config->productionchange->search['fields']['correlationSecondorder'] = $lang->productionchange->correlationSecondorder;
$config->productionchange->search['fields']['deptConfirmPerson']      = $lang->productionchange->deptConfirmPerson;
$config->productionchange->search['fields']['interfacePerson']        = $lang->productionchange->interfacePerson;
$config->productionchange->search['fields']['operationPerson']        = $lang->productionchange->operationPerson;
$config->productionchange->search['fields']['mediaPackage']           = $lang->productionchange->mediaPackage;
$config->productionchange->search['fields']['ifReport']               = $lang->productionchange->ifReport;


$config->productionchange->search['params']['code']                 = array('operator' => 'include', 'control' => 'input',  'values' => '');
$config->productionchange->search['params']['applicant']            = array('operator' => '=', 'control' => 'select', 'values' => array(0 => ''));
$config->productionchange->search['params']['applicantDept']        = array('operator' => '=', 'control' => 'select', 'values' => array(0 => ''));
$config->productionchange->search['params']['onlineType']           = array('operator' => '=', 'control' => 'select', 'values' => $lang->productionchange->onlineTypeList);
$config->productionchange->search['params']['status']               = array('operator' => '=', 'control' => 'select', 'values' => array(0 => '')+$lang->productionchange->statusList);
$config->productionchange->search['params']['dealUser']             = array('operator' => '=', 'control' => 'select', 'values' => array(0 => ''));
$config->productionchange->search['params']['createdBy']            = array('operator' => '=', 'control' => 'select', 'values' => array(0 => ''));
$config->productionchange->search['params']['createdDate']          = array('operator' => '=', 'control' => 'input',  'values' => '', 'class' => 'date');
$config->productionchange->search['params']['application']          = array('operator' => '=', 'control' => 'select',  'values' => array(0 => ''));
$config->productionchange->search['params']['onlineStart']          = array('operator' => '=', 'control' => 'input',  'values' => '', 'class' => 'date');
$config->productionchange->search['params']['onlineEnd']            = array('operator' => '=', 'control' => 'input',  'values' => '', 'class' => 'date');
$config->productionchange->search['params']['abstract']             = array('operator' => 'include', 'control' => 'input',  'values' => '');
$config->productionchange->search['params']['implementContent']     = array('operator' => 'include', 'control' => 'input',  'values' => '');
$config->productionchange->search['params']['effect']               = array('operator' => 'include', 'control' => 'input',  'values' => '');
$config->productionchange->search['params']['ifEffectSystem']       = array('operator' => 'include', 'control' => 'input',  'values' => '');
$config->productionchange->search['params']['effectSystemExplain']  = array('operator' => 'include', 'control' => 'input',  'values' => '');
$config->productionchange->search['params']['materialExplain']      = array('operator' => 'include', 'control' => 'input',  'values' => '');
$config->productionchange->search['params']['correlationPublish']   = array('operator' => 'include', 'control' => 'input',  'values' => '');
$config->productionchange->search['params']['space']                = array('operator' => 'include', 'control' => 'input',  'values' => '');
$config->productionchange->search['params']['releaseRecord']        = array('operator' => 'include', 'control' => 'input',  'values' => '');
$config->productionchange->search['params']['correlationDemand']    = array('operator' => 'include', 'control' => 'select', 'values' => array(0 => ''));
$config->productionchange->search['params']['correlationProblem']      = array('operator' => 'include', 'control' => 'select', 'values' => array(0 => ''));
$config->productionchange->search['params']['correlationSecondorder']  = array('operator' => 'include', 'control' => 'select', 'values' => array(0 => ''));
$config->productionchange->search['params']['deptConfirmPerson']       = array('operator' => 'include', 'control' => 'select', 'values' => array(0 => ''));
$config->productionchange->search['params']['interfacePerson']         = array('operator' => 'include', 'control' => 'select', 'values' => array(0 => ''));
$config->productionchange->search['params']['operationPerson']         = array('operator' => 'include', 'control' => 'select', 'values' => array(0 => ''));
$config->productionchange->search['params']['mediaPackage']            = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->productionchange->search['params']['ifReport']                = array('operator' => '=', 'control' => 'select',  'values' => array(0 => '') + $lang->productionchange->ifReportList);


