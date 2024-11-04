<?php


$config->review = new stdclass();
$config->review->editor = new stdclass();
$config->review->editor->create  = array('id' => 'comment', 'tools' => 'simpleTools');
$config->review->editor->edit    = array('id' => 'comment', 'tools' => 'simpleTools');
$config->review->editor->submit  = array('id' => 'comment', 'tools' => 'simpleTools');
$config->review->editor->toaudit = array('id' => 'comment', 'tools' => 'simpleTools');
//$config->review->editor->assess  = array('id' => 'opinion', 'tools' => 'simpleTools');
//审批
$config->review->editor->review  = array('id' => 'comment', 'tools' => 'simpleTools');
//指派
$config->review->editor->assign  = array('id' => 'comment', 'tools' => 'simpleTools');

$config->review->create = new stdclass();
$config->review->create->requiredFields = 'title,type,grade,reviewer';

$config->review->edit = new stdclass();
$config->review->edit->requiredFields = 'title';

$config->review->datatable = new stdclass();
$config->review->datatable->defaultField = array('id', 'title', 'status', 'type', 'owner', 'expert', 'reviewedBy', 'createdBy', 'reviewer', 'createdDate', 'actions');

$config->review->submit = new stdclass();
$config->review->submit->requiredFields = 'qa,preReviewDeadline, firstReviewDeadline, deadline';

$config->review->review = new stdclass();
$config->review->assign = new stdclass();

$config->review->datatable->fieldList['id']['title']    = 'idAB';
$config->review->datatable->fieldList['id']['fixed']    = 'left';
$config->review->datatable->fieldList['id']['width']    = '60';
$config->review->datatable->fieldList['id']['required'] = 'yes';

$config->review->datatable->fieldList['title']['title']    = 'title';
$config->review->datatable->fieldList['title']['fixed']    = 'left';
$config->review->datatable->fieldList['title']['width']    = 'auto';
$config->review->datatable->fieldList['title']['required'] = 'yes';

$config->review->datatable->fieldList['object']['title']    = 'object';
$config->review->datatable->fieldList['object']['fixed']    = 'no';
$config->review->datatable->fieldList['object']['width']    = '120';
$config->review->datatable->fieldList['object']['required'] = 'no';

$config->review->datatable->fieldList['status']['title']    = 'status';
$config->review->datatable->fieldList['status']['fixed']    = 'no';
$config->review->datatable->fieldList['status']['width']    = '100';
$config->review->datatable->fieldList['status']['required'] = 'no';

$config->review->datatable->fieldList['type']['title']    = 'type';
$config->review->datatable->fieldList['type']['fixed']    = 'no';
$config->review->datatable->fieldList['type']['width']    = '100';
$config->review->datatable->fieldList['type']['required'] = 'no';

$config->review->datatable->fieldList['owner']['title']    = 'owner';
$config->review->datatable->fieldList['owner']['fixed']    = 'no';
$config->review->datatable->fieldList['owner']['width']    = '120';
$config->review->datatable->fieldList['owner']['required'] = 'no';

$config->review->datatable->fieldList['expert']['title']    = 'expert';
$config->review->datatable->fieldList['expert']['fixed']    = 'no';
$config->review->datatable->fieldList['expert']['width']    = '120';
$config->review->datatable->fieldList['expert']['required'] = 'no';

$config->review->datatable->fieldList['reviewedBy']['title']    = 'reviewedBy';
$config->review->datatable->fieldList['reviewedBy']['fixed']    = 'no';
$config->review->datatable->fieldList['reviewedBy']['width']    = '150';
$config->review->datatable->fieldList['reviewedBy']['required'] = 'no';

$config->review->datatable->fieldList['createdBy']['title']    = 'createdBy';
$config->review->datatable->fieldList['createdBy']['fixed']    = 'no';
$config->review->datatable->fieldList['createdBy']['width']    = '120';
$config->review->datatable->fieldList['createdBy']['required'] = 'no';

$config->review->datatable->fieldList['reviewer']['title']    = 'pending';
$config->review->datatable->fieldList['reviewer']['fixed']    = 'no';
$config->review->datatable->fieldList['reviewer']['width']    = '120';
$config->review->datatable->fieldList['reviewer']['required'] = 'no';

$config->review->datatable->fieldList['createdDate']['title']    = 'createdDate';
$config->review->datatable->fieldList['createdDate']['fixed']    = 'no';
$config->review->datatable->fieldList['createdDate']['width']    = '120';
$config->review->datatable->fieldList['createdDate']['required'] = 'no';

$config->review->datatable->fieldList['deadline']['title']    = 'deadline';
$config->review->datatable->fieldList['deadline']['fixed']    = 'no';
$config->review->datatable->fieldList['deadline']['width']    = '120';
$config->review->datatable->fieldList['deadline']['required'] = 'no';

$config->review->datatable->fieldList['lastReviewedDate']['title']    = 'lastReviewedDate';
$config->review->datatable->fieldList['lastReviewedDate']['fixed']    = 'no';
$config->review->datatable->fieldList['lastReviewedDate']['width']    = '120';
$config->review->datatable->fieldList['lastReviewedDate']['required'] = 'no';

$config->review->datatable->fieldList['result']['title']    = 'result';
$config->review->datatable->fieldList['result']['fixed']    = 'no';
$config->review->datatable->fieldList['result']['width']    = '120';
$config->review->datatable->fieldList['result']['required'] = 'no';

$config->review->datatable->fieldList['lastAuditedDate']['title']    = 'lastAuditedDate';
$config->review->datatable->fieldList['lastAuditedDate']['fixed']    = 'no';
$config->review->datatable->fieldList['lastAuditedDate']['width']    = '120';
$config->review->datatable->fieldList['lastAuditedDate']['required'] = 'no';

$config->review->datatable->fieldList['auditResult']['title']    = 'auditResult';
$config->review->datatable->fieldList['auditResult']['fixed']    = 'no';
$config->review->datatable->fieldList['auditResult']['width']    = '120';
$config->review->datatable->fieldList['auditResult']['required'] = 'no';

$config->review->datatable->fieldList['actions']['title']    = 'actions';
$config->review->datatable->fieldList['actions']['fixed']    = 'right';
$config->review->datatable->fieldList['actions']['width']    = '180';
$config->review->datatable->fieldList['actions']['required'] = 'yes';

global $lang;
$config->review->search['module'] = 'review';
$config->review->search['fields']['id']         = $lang->idAB;
$config->review->search['fields']['title']      = $lang->review->title;
$config->review->search['fields']['type']       = $lang->review->type;
$config->review->search['fields']['object']     = $lang->review->object;
$config->review->search['fields']['status']     = $lang->review->status;
$config->review->search['fields']['content']    = $lang->review->content;
$config->review->search['fields']['owner']      = $lang->review->owner;
$config->review->search['fields']['expert']     = $lang->review->expert;
$config->review->search['fields']['reviewedBy'] = $lang->review->reviewedBy;
$config->review->search['fields']['result']     = $lang->review->result;

$config->review->search['params']['id']         = array('operator' => '=', 'control' => 'input', 'values' => '');
$config->review->search['params']['title']      = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->review->search['params']['type']       = array('operator' => '=', 'control' => 'select', 'values' => $lang->review->typeList);
$config->review->search['params']['object']     = array('operator' => '=', 'control' => 'select', 'values' => $lang->review->objectList);
$config->review->search['params']['status']     = array('operator' => '=', 'control' => 'select', 'values' => $lang->review->statusList);
$config->review->search['params']['content']    = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->review->search['params']['owner']      = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->review->search['params']['expert']     = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->review->search['params']['reviewedBy'] = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->review->search['params']['result']     = array('operator' => '=', 'control' => 'select', 'values' => $lang->review->resultList);

//$config->review->fileSize = 20;
