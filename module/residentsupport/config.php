<?php
$config->residentsupport = new stdclass();
$config->residentsupport->create = new stdclass();
$config->residentsupport->edit   = new stdclass();
$config->residentsupport->submit = new stdclass(); //申请提交
$config->residentsupport->review = new stdclass(); //审核
$config->residentsupport->create->requiredFields = 'user,type,planDate';
$config->residentsupport->edit->requiredFields   = $config->residentsupport->create->requiredFields;

$config->residentsupport->editor = new stdclass();
$config->residentsupport->editor->create = array('id' => 'desc', 'tools' => 'simpleTools');
$config->residentsupport->editor->edit   = array('id' => 'desc', 'tools' => 'simpleTools');
$config->residentsupport->editor->submit = array('id' => 'comment', 'tools' => 'simpleTools');
$config->residentsupport->editor->review = array('id' => 'comment', 'tools' => 'simpleTools');
$config->residentsupport->editor->enablescheduling = array('id' => 'comment', 'tools' => 'simpleTools');
$config->residentsupport->editor->view   = array('id' => 'desc,comment,lastComment', 'tools' => 'simpleTools');
$config->residentsupport->editor->exportrostering   = array('id' => 'comment', 'tools' => 'simpleTools');

$config->residentsupport->list = new stdclass();
$config->residentsupport->list->exportFields = 'id,planDate,user,actualDate,actualUser,application,importantTime,desc';

/* Search. */
global $lang;
$config->residentsupport->search['module']                = 'residentsupport';

$config->residentsupport->search['fields']['deptId']      = $lang->residentsupport->deptId;
$config->residentsupport->search['fields']['status']      = $lang->residentsupport->status;
$config->residentsupport->search['fields']['dutyGroupLeader'] = $lang->residentsupport->dutyGroupLeader;
$config->residentsupport->search['fields']['type']        = $lang->residentsupport->type;
$config->residentsupport->search['fields']['subType']     = $lang->residentsupport->subType;
$config->residentsupport->search['fields']['dutyUser']        = $lang->residentsupport->dutyUser;
$config->residentsupport->search['fields']['dealUsers']   = $lang->residentsupport->dealUsers;
$config->residentsupport->search['fields']['startDate']   = $lang->residentsupport->startDate;
$config->residentsupport->search['fields']['endDate']     = $lang->residentsupport->endDate;



$config->residentsupport->search['params']['deptId']      = array('operator' => '=',       'control' => 'select', 'values' => array());
$config->residentsupport->search['params']['status']      = array('operator' => '=',       'control' => 'select', 'values' => array());
$config->residentsupport->search['params']['dutyGroupLeader'] = array('operator' => 'include',       'control' => 'select', 'values' => 'users');
$config->residentsupport->search['params']['type']        = array('operator' => '=',       'control' => 'select', 'values' => $lang->residentsupport->typeList);
$config->residentsupport->search['params']['subType']     = array('operator' => '=',       'control' => 'select', 'values' => $lang->residentsupport->subTypeList);
$config->residentsupport->search['params']['dutyUser']        = array('operator' => 'include', 'control' => 'select', 'values' => 'users');
$config->residentsupport->search['params']['dealUsers']   = array('operator' => 'include', 'control' => 'select', 'values' => 'users');
$config->residentsupport->search['params']['startDate']   = array('operator' => '=',       'control' => 'input',  'values' => '', 'class' => 'date');
$config->residentsupport->search['params']['endDate']     = array('operator' => '=',       'control' => 'input',  'values' => '', 'class' => 'date');



$config->residentsupport->secondReviews = '';
$config->residentsupport->schedulingIntervalDay = '';

$config->residentsupport->export = new stdClass();
$config->residentsupport->export->templateFields = explode(',',"dutyDate,postType,dutyUserDept,timeType,dutyDuration,requireInfo,type,subType,dutyGroupLeader,dutyUser");
$config->residentsupport->export->listFields = explode(',',"postType,type,subType,timeType,dutyUserDept,dutyGroupLeader,dutyUser");
$config->residentsupport->export->width = ['dutyDate'=>15,'postType'=>15,'dutyDuration'=>20,'dutyUserDept'=>20,'timeType'=>20,'requireInfo'=>25,'dutyGroupLeader'=>15,'dutyUser'=>15,'type'=>15,'subType'=>15];


