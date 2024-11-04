<?php
$config->localesupport = new stdclass();
$config->localesupport->objectType = "localesupport";
$config->localesupport->create = new stdclass();
$config->localesupport->edit   = new stdclass();
$config->localesupport->delete = new stdclass();
$config->localesupport->list = new stdclass();
$config->localesupport->review = new stdclass();

$config->localesupport->create->requiredFields = 'startDate,endDate,area,appIds,stype,deptIds,reason,supportUsers,deptManagers,reason';
$config->localesupport->edit->requiredFields = $config->localesupport->create->requiredFields;
$config->localesupport->review->requiredFields = '';
$config->localesupport->list->exportFields = 'code,startDate,endDate,area,stype,reason,appIds,owndept,sj,deptIds,deptManagers,supportUsers,consumedTotal,remark,jxdepart,sysper,manufacturer,createdBy,createdDept,createdTime,status,dealUsers';
$config->localesupport->list->exportDetailFields = 'supportId,code,supportDate,deptId,supportUser,consumed';

$config->localesupport->editor = new stdclass();
$config->localesupport->editor->create = array('id' => 'reason,remark', 'tools' => 'simpleTools');
$config->localesupport->editor->edit   = array('id' => 'reason,remark', 'tools' => 'simpleTools');
$config->localesupport->editor->delete = array('id' => 'remark', 'tools' => 'simpleTools');
$config->localesupport->editor->submit = array('id' => 'comment', 'tools' => 'simpleTools');
$config->localesupport->editor->review = array('id' => 'comment', 'tools' => 'simpleTools');
$config->localesupport->editor->batchreview = array('id' => 'comment', 'tools' => 'simpleTools');

/*
 * 多选下拉字段
 */
$config->localesupport->multipleSelectFields = ['appIds','deptIds','supportUsers','deptManagers'];

/**
 * 次月前多少工作日限制开关,默认开启
 */
$config->localesupport->limitDaySwitch = 1;
/**
 * 补报次月填报上个月工作日时间限制
 */
$config->localesupport->reportWorkLimitDay = 3;


/* Search. */
global $lang,$app;
$app->loadLang('application');;
$config->localesupport->search['module'] = 'localesupport';
$config->localesupport->search['fields']['code']          = $lang->localesupport->code;
$config->localesupport->search['fields']['createdBy']    = $lang->localesupport->createdBy;
$config->localesupport->search['fields']['deptIds']       = $lang->localesupport->deptIds;

//$config->localesupport->search['fields']['id']             = $lang->localesupport->id;
$config->localesupport->search['fields']['startDate']     = $lang->localesupport->startDate;
$config->localesupport->search['fields']['endDate']       = $lang->localesupport->endDate;
$config->localesupport->search['fields']['area']          = $lang->localesupport->area;
$config->localesupport->search['fields']['stype']         = $lang->localesupport->stype;

$config->localesupport->search['fields']['appIds']        = $lang->localesupport->appIds;
$config->localesupport->search['fields']['owndept']       = $lang->localesupport->owndept;
$config->localesupport->search['fields']['sj']             = $lang->localesupport->sj;
$config->localesupport->search['fields']['supportUsers'] = $lang->localesupport->supportUsers;

$config->localesupport->search['fields']['reason']        = $lang->localesupport->reason;
//$config->localesupport->search['fields']['remark']        = $lang->localesupport->remark;
//$config->localesupport->search['fields']['deptManagers'] = $lang->localesupport->deptManagers;
$config->localesupport->search['fields']['status']        = $lang->localesupport->status;
//$config->localesupport->search['fields']['dealUsers']    = $lang->localesupport->dealUsers;
//$config->localesupport->search['fields']['jxdepart']     = $lang->localesupport->jxdepart;
//$config->localesupport->search['fields']['sysper']        = $lang->localesupport->sysper;
//$config->localesupport->search['fields']['manufacturer'] = $lang->localesupport->manufacturer;
//$config->localesupport->search['fields']['mailto']        = $lang->localesupport->mailto;
$config->localesupport->search['fields']['createdTime']  = $lang->localesupport->createdTime;

$config->localesupport->search['params']['code']           = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->localesupport->search['params']['createdBy']     = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->localesupport->search['params']['deptIds']       = array('operator' => 'include', 'control' => 'select', 'values' => []);

$config->localesupport->search['params']['startDate']    = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->localesupport->search['params']['endDate']       = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->localesupport->search['params']['area']          = array('operator' => '=', 'control' => 'select', 'values' => $lang->localesupport->areaList);
$config->localesupport->search['params']['appIds']        = array('operator' => 'include', 'control' => 'select', 'values' => []);
$config->localesupport->search['params']['stype']         = array('operator' => '=', 'control' => 'select', 'values' => $lang->localesupport->stypeList);
$config->localesupport->search['params']['owndept']       = array('operator' => 'include', 'control' => 'select', 'values' => $lang->application->teamList);
$config->localesupport->search['params']['sj']             = array('operator' => 'include', 'control' => 'select', 'values' => $lang->application->fromUnitList);
$config->localesupport->search['params']['supportUsers'] = array('operator' => 'include', 'control' => 'select', 'values' => 'users');

$config->localesupport->search['params']['reason']        = array('operator' => 'include', 'control' => 'input', 'values' => []);
//$config->localesupport->search['params']['remark']        = array('operator' => 'include', 'control' => 'input', 'values' => []);
//$config->localesupport->search['params']['deptManagers'] = array('operator' => 'include', 'control' => 'select', 'values' => 'users');
$config->localesupport->search['params']['status']        = array('operator' => '=', 'control' => 'select', 'values' => array('' => '') + $lang->localesupport->statusList);
//$config->localesupport->search['params']['dealUsers']     = array('operator' => 'include', 'control' => 'select', 'values' => 'users');
//$config->localesupport->search['params']['jxdepart']      = array('operator' => 'include', 'control' => 'input', 'values' => '');
//$config->localesupport->search['params']['sysper']        = array('operator' => 'include', 'control' => 'input', 'values' => '');
//$config->localesupport->search['params']['manufacturer'] = array('operator' => 'include', 'control' => 'input', 'values' => '');
//$config->localesupport->search['params']['mailto']        = array('operator' => 'include', 'control' => 'select', 'values' => 'users');
$config->localesupport->search['params']['createdTime']     = array('operator' => '>=', 'control' => 'input', 'values' => '', 'class' => 'date');