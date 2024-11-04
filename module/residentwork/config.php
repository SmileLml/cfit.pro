<?php
$config->residentwork = new stdclass();
$config->residentwork->create = new stdclass();
$config->residentwork->edit   = new stdclass();
$config->residentwork->create->requiredFields = 'user,type,planDate';
$config->residentwork->edit->requiredFields   = $config->residentwork->create->requiredFields;

$config->residentwork->editor = new stdclass();
$config->residentwork->editor->create = array('id' => 'desc', 'tools' => 'simpleTools');
$config->residentwork->editor->edit   = array('id' => 'desc', 'tools' => 'simpleTools');
$config->residentwork->editor->view   = array('id' => 'desc,comment,lastComment', 'tools' => 'simpleTools');

$config->residentwork->list = new stdclass();
$config->residentwork->list->exportFields = 'id,planDate,user,actualDate,actualUser,application,importantTime,desc';

$config->residentwork->editor->recorddutylog   = array('id' => 'remark,logs,warnLogs,analysis', 'tools' => 'simpleTools', 'height' => '50px');
//$config->residentwork->editor->createlog   = array('id' => 'remark,logs,warnLogs,analysis', 'tools' => 'simpleTools', 'height' => '50px');
//$config->residentwork->editor->editlog   = array('id' => 'remark,logs,warnLogs,analysis', 'tools' => 'simpleTools', 'height' => '50px');
$config->residentwork->editor->createlog   = array('id' => '');
$config->residentwork->editor->editlog   = array('id' => '');

/* Search. */
global $lang;
/*
$config->residentwork->search['module']                     = 'residentwork';
$config->residentwork->search['fields']['dutyDate']         = $lang->residentwork->dutyDate;
$config->residentwork->search['fields']['dutyGroupLeader']  = $lang->residentwork->dutyGroupLeader;
$config->residentwork->search['fields']['dutyUserDept']      = $lang->residentwork->dutyDept;
$config->residentwork->search['fields']['dutyUser']         = $lang->residentwork->dutyUser;
$config->residentwork->search['fields']['dutyType']         = $lang->residentwork->type;
$config->residentwork->search['fields']['requireInfo']      = $lang->residentwork->requireInfo;
$config->residentwork->search['fields']['postTypeInfo']     = $lang->residentwork->postTypeInfo;
$config->residentwork->search['fields']['timeType']         = $lang->residentwork->timeType;
$config->residentwork->search['fields']['createdDate']      = $lang->residentwork->fillInDate;
$config->residentwork->search['fields']['createdBy']        = $lang->residentwork->fillInCreated;
$config->residentwork->search['fields']['dutyPlace']        = $lang->residentwork->dutyPlace;
$config->residentwork->search['fields']['actualLeader']     = $lang->residentwork->actualLeader;
$config->residentwork->search['fields']['actualUser']       = $lang->residentwork->actualUser;
$config->residentwork->search['fields']['dateType']         = $lang->residentwork->dateType;
$config->residentwork->search['fields']['isEmergency']      = $lang->residentwork->isEmergency;
$config->residentwork->search['fields']['emergencyRemark']  = $lang->residentwork->emergencyRemark;
$config->residentwork->search['fields']['descRemark']       = $lang->residentwork->desc;
$config->residentwork->search['fields']['warnLogs']         = $lang->residentwork->warnLogs;


$config->residentwork->search['params']['dutyDate']         = array('operator' => '=','control' => 'input',  'values' => '', 'class' => 'date');
$config->residentwork->search['params']['dutyGroupLeader']  = array('operator' => 'include','control' => 'select', 'values' => 'users');
$config->residentwork->search['params']['dutyUserDept']     = array('operator' => '=','control' => 'select', 'values' => '');//值班部门
$config->residentwork->search['params']['dutyUser']         = array('operator' => 'include', 'control' => 'select', 'values' => 'users');
$config->residentwork->search['params']['dutyType']         = array('operator' => '=', 'control' => 'select', 'values' => array());
$config->residentwork->search['params']['requireInfo']      = array('operator' => 'include','control' => 'input', 'values' => '');
$config->residentwork->search['params']['postTypeInfo']     = array('operator' => '=','control' => 'select', 'values' => array());
$config->residentwork->search['params']['createdDate']      = array('operator' => '=','control' => 'input',  'values' => '', 'class' => 'date');
$config->residentwork->search['params']['createdBy']        = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->residentwork->search['params']['dutyPlace']        = array('operator' => '=', 'control' => 'select', 'values' => array());
$config->residentwork->search['params']['actualLeader']     = array('operator' => 'include','control' => 'select', 'values' => 'users');
$config->residentwork->search['params']['actualUser']       = array('operator' => 'include','control' => 'select', 'values' => 'users');
$config->residentwork->search['params']['emergencyRemark']  = array('operator' => 'include','control' => 'input', 'values' => '');
$config->residentwork->search['params']['descRemark']       = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->residentwork->search['params']['dutyDept']         = array('operator' => '=', 'control' => 'select', 'values' => array());
$config->residentwork->search['params']['timeType']         = array('operator' => '=', 'control' => 'select', 'values' => array());
$config->residentwork->search['params']['dateType']         = array('operator' => '=', 'control' => 'select', 'values' => array());
$config->residentwork->search['params']['isEmergency']      = array('operator' => '=', 'control' => 'select', 'values' => array());
*/

$config->residentwork->export = new stdClass();
$config->residentwork->export->templateFields = explode(',',"dutyDate,type,subType,requireInfo,postType,timeType,dutyDuration,dutyUserDept,dutyGroupLeader,dutyUser,createdBy,createdDate,area,actualLeader,user,dateType,isEmergency,remark,logs,warnLogs");
$config->residentwork->export->width = ['dutyDate'=>15,'postType'=>15,'dutyDuration'=>20,'dutyUserDept'=>20,'timeType'=>20,'requireInfo'=>25,'dutyGroupLeader'=>15,'dutyUser'=>15,'type'=>15,'subType'=>15,'createdBy'=>15,'createdDate'=>18,'area'=>15,'actualLeader'=>15,'user'=>15,'dateType'=>15,'isEmergency'=>15,'remark'=>20,'logs'=>20,'warnLogs'=>20];
$config->residentwork->export->templateFields2 = explode(',',"dutyDate,type,subType,dutyUserDept,createdBy,createdDate,area,actualLeader,user,dateType,isEmergency,remark,logs,warnLogs,logSource,pushTitle");

$config->residentwork->search['module']                     = 'residentwork';
$config->residentwork->search['fields']['dutyDate']         = $lang->residentwork->dutyDate;
$config->residentwork->search['fields']['realDutyuserDept']      = $lang->residentwork->dutyDept;
$config->residentwork->search['fields']['type']             = $lang->residentwork->type;
$config->residentwork->search['fields']['createdDate']      = $lang->residentwork->fillInDate;
$config->residentwork->search['fields']['createdBy']        = $lang->residentwork->fillInCreated;
$config->residentwork->search['fields']['dutyPlace']        = $lang->residentwork->dutyPlace;
$config->residentwork->search['fields']['actualLeader']     = $lang->residentwork->actualLeader;
$config->residentwork->search['fields']['actualUser']       = $lang->residentwork->actualUser;
$config->residentwork->search['fields']['dateType']         = $lang->residentwork->dateType;
$config->residentwork->search['fields']['isEmergency']      = $lang->residentwork->isEmergency;
$config->residentwork->search['fields']['emergencyRemark']  = $lang->residentwork->emergencyRemark;
$config->residentwork->search['fields']['descRemark']       = $lang->residentwork->desc;
$config->residentwork->search['fields']['warnLogs']         = $lang->residentwork->warnLogs;


$config->residentwork->search['params']['dutyDate']         = array('operator' => '=','control' => 'input',  'values' => '', 'class' => 'date');
$config->residentwork->search['params']['realDutyuserDept']     = array('operator' => '=','control' => 'select', 'values' => '');//值班部门
$config->residentwork->search['params']['type']             = array('operator' => '=', 'control' => 'select', 'values' => array());
$config->residentwork->search['params']['createdDate']      = array('operator' => '=','control' => 'input',  'values' => '', 'class' => 'date');
$config->residentwork->search['params']['createdBy']        = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->residentwork->search['params']['dutyPlace']        = array('operator' => '=', 'control' => 'select', 'values' => array());
$config->residentwork->search['params']['actualLeader']     = array('operator' => 'include','control' => 'select', 'values' => 'users');
$config->residentwork->search['params']['actualUser']       = array('operator' => 'include','control' => 'select', 'values' => 'users');
$config->residentwork->search['params']['emergencyRemark']  = array('operator' => 'include','control' => 'input', 'values' => '');
$config->residentwork->search['params']['descRemark']       = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->residentwork->search['params']['dutyDept']         = array('operator' => '=', 'control' => 'select', 'values' => array());
$config->residentwork->search['params']['dateType']         = array('operator' => '=', 'control' => 'select', 'values' => array());
$config->residentwork->search['params']['isEmergency']      = array('operator' => '=', 'control' => 'select', 'values' => array());