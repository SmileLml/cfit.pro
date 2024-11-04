<?php
$config->issue->create = new stdclass();
$config->issue->edit   = new stdclass();
$config->issue->confirm   = new stdclass();
$config->issue->resolve   = new stdclass();
$config->issue->assignto  = new stdclass();
$config->issue->assignedtoframework  = new stdclass();

$config->issue->create->requiredFields = 'title,type,severity,owner,pri,assignedTo,deadline,desc';
$config->issue->edit->requiredFields   = 'title,type,severity,owner,pri,deadline,desc';
$config->issue->confirm->requiredFields   = 'desc';
$config->issue->resolve->requiredFields   = 'resolution,resolvedBy,resolvedDate';
$config->issue->assignto->requiredFields   = 'assignedTo';
$config->issue->assignedtoframework->requiredFields   = 'frameworkUser';

$config->issue->editor          = new stdclass();
$config->issue->editor->view    = array('id' => 'comment,lastComment', 'tools' => 'simpleTools');
$config->issue->editor->create  = array('id' => 'desc', 'tools' => 'simpleTools');
$config->issue->editor->edit    = array('id' => 'desc', 'tools' => 'simpleTools');
$config->issue->editor->assignto = array('id' => 'comment', 'tools' => 'simpleTools');
$config->issue->editor->confirm = array('id' => 'desc', 'tools' => 'simpleTools');
$config->issue->editor->cancel  = array('id' => 'desc', 'tools' => 'simpleTools');
$config->issue->editor->resolve = array('id' => 'spec,verify,steps,desc,resolutionComment', 'tools' => 'simpleTools');
$config->issue->editor->activate = array('id' => 'comment', 'tools' => 'simpleTools');
$config->issue->editor->assignedtoframework = array('id' => 'comment', 'tools' => 'simpleTools');


global $lang;

$config->issue->search['module'] = 'issue';

$config->issue->search['fields']['title']        = $lang->issue->title;
$config->issue->search['fields']['id']           = $lang->issue->id;
$config->issue->search['fields']['pri']          = $lang->issue->pri;
$config->issue->search['fields']['severity']     = $lang->issue->severity;
$config->issue->search['fields']['type']         = $lang->issue->type;
$config->issue->search['fields']['createdBy']    = $lang->issue->createdBy;
$config->issue->search['fields']['createdDate']  = $lang->issue->createdDate;
$config->issue->search['fields']['closedBy']     = $lang->issue->closedBy;
$config->issue->search['fields']['closedDate']   = $lang->issue->closedDate;
$config->issue->search['fields']['assignedTo']   = $lang->issue->assignedTo;
$config->issue->search['fields']['assignedDate'] = $lang->issue->assignedDate;

$config->issue->search['params']['title']        = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->issue->search['params']['id']           = array('operator' => '=', 'control' => 'input', 'values' => '');
$config->issue->search['params']['pri']          = array('operator' => '=', 'control' => 'select', 'values' => $lang->issue->priList);
$config->issue->search['params']['severity']     = array('operator' => '=', 'control' => 'select', 'values' => $lang->issue->severityList);
$config->issue->search['params']['type']         = array('operator' => '=', 'control' => 'select', 'values' => $lang->issue->typeList);
$config->issue->search['params']['createdBy']    = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->issue->search['params']['createdDate']  = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->issue->search['params']['closeBy']      = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->issue->search['params']['closedDate']   = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->issue->search['params']['assignedTo']   = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->issue->search['params']['assignedDate'] = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');
