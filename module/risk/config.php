<?php
$config->risk->editor = new stdclass();
$config->risk->editor->create   = array('id' => 'prevention,remedy,preventionAndremedy', 'tools' => 'simpleTools');
$config->risk->editor->edit     = array('id' => 'prevention,remedy,resolution,preventionAndremedy', 'tools' => 'simpleTools');
$config->risk->editor->assignto = array('id' => 'comment', 'tools' => 'simpleTools');
$config->risk->editor->cancel   = array('id' => 'comment', 'tools' => 'simpleTools');
$config->risk->editor->close    = array('id' => 'resolution', 'tools' => 'simpleTools');
$config->risk->editor->track    = array('id' => 'prevention,resolution,comment,preventionAndremedy', 'tools' => 'simpleTools');
$config->risk->editor->view     = array('id' => 'lastComment', 'tools' => 'simpleTools');
$config->risk->editor->assignedtoframework = array('id' => 'comment', 'tools' => 'simpleTools');

$config->risk->create = new stdclass();
$config->risk->create->requiredFields = 'name,identifiedDate,source,category,strategy,impact,probability,timeFrame,rate,pri,assignedTo,preventionAndremedy';

$config->risk->edit = new stdclass();
$config->risk->edit->requiredFields = 'name,identifiedDate,source,category,strategy,impact,probability,timeFrame,rate,pri,assignedTo,preventionAndremedy';

$config->risk->track = new stdclass();
$config->risk->track->requiredFields = 'name,source,category,strategy,impact,probability,timeFrame,rate,pri,assignedTo,trackedBy,trackedDate';

$config->risk->close = new stdclass();
$config->risk->close->requiredFields = 'resolvedBy,actualClosedDate';

$config->risk->cancel = new stdclass();
$config->risk->cancel->requiredFields = 'cancelBy,cancelDate,cancelReason';

$config->risk->hangup = new stdclass();
$config->risk->hangup->requiredFields = 'hangupBy,hangupDate,assignedTo';

$config->risk->activate = new stdclass();
$config->risk->activate->requiredFields = 'assignedTo,activateDate';

$config->risk->assignto = new stdclass();
$config->risk->assignto->requiredFields = 'assignedTo';

$config->risk->assignedtoframework  = new stdclass();
$config->risk->assignedtoframework->requiredFields   = 'frameworkUser';

global $lang;
$config->risk->search['module']                      = 'risk';
$config->risk->search['fields']['id']                = $lang->risk->id;
$config->risk->search['fields']['name']              = $lang->risk->name;
$config->risk->search['fields']['source']            = $lang->risk->source;
$config->risk->search['fields']['category']          = $lang->risk->category;
$config->risk->search['fields']['strategy']          = $lang->risk->strategy;
$config->risk->search['fields']['status']            = $lang->risk->status;
$config->risk->search['fields']['impact']            = $lang->risk->impact;
$config->risk->search['fields']['probability']       = $lang->risk->probability;
$config->risk->search['fields']['rate']              = $lang->risk->rate;
$config->risk->search['fields']['pri']               = $lang->risk->pri;
$config->risk->search['fields']['identifiedDate']    = $lang->risk->identifiedDate;
$config->risk->search['fields']['prevention']        = $lang->risk->prevention;
$config->risk->search['fields']['remedy']            = $lang->risk->remedy;
$config->risk->search['fields']['resolution']        = $lang->risk->resolution;
$config->risk->search['fields']['plannedClosedDate'] = $lang->risk->plannedClosedDate;
$config->risk->search['fields']['actualClosedDate']  = $lang->risk->actualClosedDate;
$config->risk->search['fields']['createdBy']         = $lang->risk->createdBy;
$config->risk->search['fields']['createdDate']       = $lang->risk->createdDate;
$config->risk->search['fields']['resolvedBy']        = $lang->risk->resolvedBy;
$config->risk->search['fields']['activateBy']        = $lang->risk->activateBy;
$config->risk->search['fields']['assignedTo']        = $lang->risk->assignedTo;
$config->risk->search['fields']['cancelBy']          = $lang->risk->cancelBy;
$config->risk->search['fields']['hangupBy']          = $lang->risk->hangupBy;
$config->risk->search['fields']['trackedBy']         = $lang->risk->trackedBy;

$config->risk->search['params']['name']              = array('operator' => 'include', 'control' => 'input',  'values' => '');
$config->risk->search['params']['source']            = array('operator' => '=', 'control' => 'select',  'values' => $lang->risk->sourceList);
$config->risk->search['params']['category']          = array('operator' => '=', 'control' => 'select',  'values' => $lang->risk->categoryList);
$config->risk->search['params']['strategy']          = array('operator' => '=', 'control' => 'select',  'values' => $lang->risk->strategyList);
$config->risk->search['params']['status']            = array('operator' => '=', 'control' => 'select',  'values' => $lang->risk->statusList);
$config->risk->search['params']['impact']            = array('operator' => '=', 'control' => 'select',  'values' => $lang->risk->impactList);
$config->risk->search['params']['probability']       = array('operator' => '=', 'control' => 'select',  'values' => $lang->risk->probabilityList);
$config->risk->search['params']['rate']              = array('operator' => '=', 'control' => 'input',  'values' => '');
$config->risk->search['params']['pri']               = array('operator' => '=', 'control' => 'select',  'values' => $lang->risk->priList);
$config->risk->search['params']['identifiedDate']    = array('operator' => '=', 'control' => 'input',  'values' => '', 'class' => 'date');
$config->risk->search['params']['prevention']        = array('operator' => 'include', 'control' => 'input',  'values' => '');
$config->risk->search['params']['remedy']            = array('operator' => 'include', 'control' => 'input',  'values' => '');
$config->risk->search['params']['plannedClosedDate'] = array('operator' => '=', 'control' => 'input',  'values' => '', 'class' => 'date');
$config->risk->search['params']['actualClosedDate']  = array('operator' => '=', 'control' => 'input',  'values' => '', 'class' => 'date');
$config->risk->search['params']['createdBy']         = array('operator' => '=', 'control' => 'select',  'values' => 'users');
$config->risk->search['params']['createdDate']       = array('operator' => '=', 'control' => 'input',  'values' => '', 'class' => 'date');
$config->risk->search['params']['resolution']        = array('operator' => 'include', 'control' => 'input',  'values' => '');
$config->risk->search['params']['resolvedBy']        = array('operator' => '=', 'control' => 'select',  'values' => 'users');
$config->risk->search['params']['activateBy']        = array('operator' => '=', 'control' => 'select',  'values' => 'users');
$config->risk->search['params']['assignedTo']        = array('operator' => '=', 'control' => 'select',  'values' => 'users');
$config->risk->search['params']['cancelBy']          = array('operator' => '=', 'control' => 'select',  'values' => 'users');
$config->risk->search['params']['hangupBy']          = array('operator' => '=', 'control' => 'select',  'values' => 'users');
$config->risk->search['params']['trackedBy']         = array('operator' => '=', 'control' => 'select',  'values' => 'users');
