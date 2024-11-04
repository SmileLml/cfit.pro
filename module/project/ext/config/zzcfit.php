<?php
$config->project->edit->requiredFields   = 'name,begin,end';
//, 'complete' , 'diffDuration', 'diffHour'
$config->project->datatable->defaultField = array('id', 'name', 'code', 'PM', 'begin', 'end', 'planDuration','realBegan', 'realEnd', 'realDuration', 'workload', 'planHour', 'realHour', 'status','insideStatus', 'actions');

$config->project->datatable->fieldList['name']['width']    = '150';
$config->project->datatable->fieldList['PM']['width']    = '60';

$config->project->datatable->fieldList['code']['title']    = 'code';
$config->project->datatable->fieldList['code']['fixed']    = 'no';
$config->project->datatable->fieldList['code']['width']    = '50';
$config->project->datatable->fieldList['code']['required'] = 'no';
$config->project->datatable->fieldList['code']['sort']     = 'no';
$config->project->datatable->fieldList['code']['pri']      = '2';

$config->project->datatable->fieldList['planDuration']['title']    = 'planDuration';
$config->project->datatable->fieldList['planDuration']['fixed']    = 'no';
$config->project->datatable->fieldList['planDuration']['width']    = '88';
$config->project->datatable->fieldList['planDuration']['required'] = 'no';
$config->project->datatable->fieldList['planDuration']['sort']     = 'no';
$config->project->datatable->fieldList['planDuration']['pri']      = '2';

$config->project->datatable->fieldList['workload']['title']    = 'workload';
$config->project->datatable->fieldList['workload']['fixed']    = 'no';
$config->project->datatable->fieldList['workload']['width']    = '98';
$config->project->datatable->fieldList['workload']['required'] = 'no';
$config->project->datatable->fieldList['workload']['sort']     = 'no';
$config->project->datatable->fieldList['workload']['pri']      = '2';

$config->project->datatable->fieldList['realBegan']['title']    = 'realBegan';
$config->project->datatable->fieldList['realBegan']['fixed']    = 'no';
$config->project->datatable->fieldList['realBegan']['width']    = '80';
$config->project->datatable->fieldList['realBegan']['required'] = 'no';
$config->project->datatable->fieldList['realBegan']['sort']     = 'no';
$config->project->datatable->fieldList['realBegan']['pri']      = '2';

$config->project->datatable->fieldList['realEnd']['title']    = 'realEnd';
$config->project->datatable->fieldList['realEnd']['fixed']    = 'no';
$config->project->datatable->fieldList['realEnd']['width']    = '80';
$config->project->datatable->fieldList['realEnd']['required'] = 'no';
$config->project->datatable->fieldList['realEnd']['sort']     = 'no';
$config->project->datatable->fieldList['realEnd']['pri']      = '2';

$config->project->datatable->fieldList['realDuration']['title']    = 'realDuration';
$config->project->datatable->fieldList['realDuration']['fixed']    = 'no';
$config->project->datatable->fieldList['realDuration']['width']    = '50';
$config->project->datatable->fieldList['realDuration']['required'] = 'no';
$config->project->datatable->fieldList['realDuration']['sort']     = 'no';
$config->project->datatable->fieldList['realDuration']['pri']      = '2';

$config->project->datatable->fieldList['diffDuration']['title']    = 'diffDuration';
$config->project->datatable->fieldList['diffDuration']['fixed']    = 'no';
$config->project->datatable->fieldList['diffDuration']['width']    = '80';
$config->project->datatable->fieldList['diffDuration']['required'] = 'no';
$config->project->datatable->fieldList['diffDuration']['sort']     = 'no';
$config->project->datatable->fieldList['diffDuration']['pri']      = '2';

$config->project->datatable->fieldList['planHour']['title']    = 'planHour';
$config->project->datatable->fieldList['planHour']['fixed']    = 'no';
$config->project->datatable->fieldList['planHour']['width']    = '98';
$config->project->datatable->fieldList['planHour']['required'] = 'no';
$config->project->datatable->fieldList['planHour']['sort']     = 'no';
$config->project->datatable->fieldList['planHour']['pri']      = '2';

$config->project->datatable->fieldList['realHour']['title']    = 'realHour';
$config->project->datatable->fieldList['realHour']['fixed']    = 'no';
$config->project->datatable->fieldList['realHour']['width']    = '70';
$config->project->datatable->fieldList['realHour']['required'] = 'no';
$config->project->datatable->fieldList['realHour']['sort']     = 'no';
$config->project->datatable->fieldList['realHour']['pri']      = '2';

$config->project->datatable->fieldList['diffHour']['title']    = 'diffHour';
$config->project->datatable->fieldList['diffHour']['fixed']    = 'no';
$config->project->datatable->fieldList['diffHour']['width']    = '80';
$config->project->datatable->fieldList['diffHour']['required'] = 'no';
$config->project->datatable->fieldList['diffHour']['sort']     = 'no';
$config->project->datatable->fieldList['diffHour']['pri']      = '2';

$config->project->datatable->fieldList['complete']['title']    = 'complete';
$config->project->datatable->fieldList['complete']['fixed']    = 'no';
$config->project->datatable->fieldList['complete']['width']    = '80';
$config->project->datatable->fieldList['complete']['required'] = 'no';
$config->project->datatable->fieldList['complete']['sort']     = 'no';
$config->project->datatable->fieldList['complete']['pri']      = '2';

$config->project->datatable->fieldList['status']['title']    = 'status';
$config->project->datatable->fieldList['status']['fixed']    = 'no';
$config->project->datatable->fieldList['status']['width']    = '80';
$config->project->datatable->fieldList['status']['required'] = 'no';
$config->project->datatable->fieldList['status']['sort']     = 'no';
$config->project->datatable->fieldList['status']['pri']      = '2';

$config->project->datatable->fieldList['insideStatus']['title']    = 'insideStatus';
$config->project->datatable->fieldList['insideStatus']['fixed']    = 'no';
$config->project->datatable->fieldList['insideStatus']['width']    = '70';
$config->project->datatable->fieldList['insideStatus']['required'] = 'no';
$config->project->datatable->fieldList['insideStatus']['sort']     = 'no';
$config->project->datatable->fieldList['insideStatus']['pri']      = '2';

global $lang;
$config->project->search['module']                   = 'project';
$config->project->search['fields']['code']           = $lang->project->code;
$config->project->search['fields']['projectId']      = $lang->project->projectId;
$config->project->search['fields']['name']           = $lang->project->name;
$config->project->search['fields']['PM']             = $lang->project->PM;
$config->project->search['fields']['begin']          = $lang->project->begin;
$config->project->search['fields']['end']            = $lang->project->end;
//$config->project->search['fields']['planDuration']   = $lang->project->planDuration;
$config->project->search['fields']['realBegan']      = $lang->project->realBegan;
$config->project->search['fields']['realEnd']        = $lang->project->realEnd;
//$config->project->search['fields']['realDuration']   = $lang->project->realDuration;
$config->project->search['fields']['id']             = $lang->project->id;
$config->project->search['fields']['isShangHai']         = $lang->project->belong ;

$config->project->search['params']['code']         = array('operator' => '=', 'control' => 'input', 'values' => '');
$config->project->search['params']['projectId']    = array('operator' => '=', 'control' => 'input', 'values' => '');
$config->project->search['params']['name']         = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->project->search['params']['PM']           = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->project->search['params']['begin']        = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->project->search['params']['end']          = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');
//$config->project->search['params']['planDuration'] = array('operator' => '=', 'control' => 'input', 'values' => '');
$config->project->search['params']['realBegan']    = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->project->search['params']['realEnd']      = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->project->search['params']['id']           = array('operator' => '=', 'control' => 'input', 'values' => '');
//$config->project->search['params']['realDuration'] = array('operator' => '=', 'control' => 'input', 'values' => '');
$config->project->search['params']['isShangHai']       = array('operator' => '=', 'control' => 'select', 'values' => $this->lang->project->isShangHaiList);
