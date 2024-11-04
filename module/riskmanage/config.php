<?php
global $lang;

$config->riskmanage->list = new stdclass();
$config->riskmanage->list->exportFields = 'code,id,name,strategy,status,identifiedDate,rate,pri,assignedTo,category,bearDept';



$config->riskmanage->search['module']                      = 'riskmanage';
$config->riskmanage->search['fields']['id']                = $lang->riskmanage->id;
$config->riskmanage->search['fields']['name']              = $lang->riskmanage->name;
$config->riskmanage->search['fields']['source']            = $lang->riskmanage->source;
$config->riskmanage->search['fields']['category']          = $lang->riskmanage->category;
$config->riskmanage->search['fields']['strategy']          = $lang->riskmanage->strategy;
$config->riskmanage->search['fields']['status']            = $lang->riskmanage->status;
$config->riskmanage->search['fields']['impact']            = $lang->riskmanage->impact;
$config->riskmanage->search['fields']['probability']       = $lang->riskmanage->probability;
$config->riskmanage->search['fields']['rate']              = $lang->riskmanage->rate;
$config->riskmanage->search['fields']['pri']               = $lang->riskmanage->pri;
$config->riskmanage->search['fields']['identifiedDate']    = $lang->riskmanage->identifiedDate;
$config->riskmanage->search['fields']['prevention']        = $lang->riskmanage->prevention;
$config->riskmanage->search['fields']['remedy']            = $lang->riskmanage->remedy;
$config->riskmanage->search['fields']['resolution']        = $lang->riskmanage->resolution;
$config->riskmanage->search['fields']['plannedClosedDate'] = $lang->riskmanage->plannedClosedDate;
$config->riskmanage->search['fields']['actualClosedDate']  = $lang->riskmanage->actualClosedDate;
$config->riskmanage->search['fields']['createdBy']         = $lang->riskmanage->createdBy;
$config->riskmanage->search['fields']['createdDate']       = $lang->riskmanage->createdDate;
$config->riskmanage->search['fields']['resolvedBy']        = $lang->riskmanage->resolvedBy;
$config->riskmanage->search['fields']['activateBy']        = $lang->riskmanage->activateBy;
$config->riskmanage->search['fields']['assignedTo']        = $lang->riskmanage->assignedTo;
$config->riskmanage->search['fields']['cancelBy']          = $lang->riskmanage->cancelBy;
$config->riskmanage->search['fields']['hangupBy']          = $lang->riskmanage->hangupBy;
$config->riskmanage->search['fields']['trackedBy']         = $lang->riskmanage->trackedBy;
$config->riskmanage->search['fields']['bearDept']         =  $lang->riskmanage->bearDept;

$config->riskmanage->search['params']['bearDept']              = array('operator' => 'include', 'control' => 'select',  'values' => '','mulit'=>true);
$config->riskmanage->search['params']['name']              = array('operator' => 'include', 'control' => 'input',  'values' => '');
$config->riskmanage->search['params']['source']            = array('operator' => '=', 'control' => 'select',  'values' => $lang->riskmanage->sourceList);
$config->riskmanage->search['params']['category']          = array('operator' => '=', 'control' => 'select',  'values' => $lang->riskmanage->categoryList);
$config->riskmanage->search['params']['strategy']          = array('operator' => '=', 'control' => 'select',  'values' => $lang->riskmanage->strategyList);
$config->riskmanage->search['params']['status']            = array('operator' => '=', 'control' => 'select',  'values' => $lang->riskmanage->statusList);
$config->riskmanage->search['params']['impact']            = array('operator' => '=', 'control' => 'select',  'values' => $lang->riskmanage->impactList);
$config->riskmanage->search['params']['probability']       = array('operator' => '=', 'control' => 'select',  'values' => $lang->riskmanage->probabilityList);
$config->riskmanage->search['params']['rate']              = array('operator' => '=', 'control' => 'input',  'values' => '');
$config->riskmanage->search['params']['pri']               = array('operator' => '=', 'control' => 'select',  'values' => $lang->riskmanage->priList);
$config->riskmanage->search['params']['identifiedDate']    = array('operator' => '=', 'control' => 'input',  'values' => '', 'class' => 'date');
$config->riskmanage->search['params']['prevention']        = array('operator' => 'include', 'control' => 'input',  'values' => '');
$config->riskmanage->search['params']['remedy']            = array('operator' => 'include', 'control' => 'input',  'values' => '');
$config->riskmanage->search['params']['plannedClosedDate'] = array('operator' => '=', 'control' => 'input',  'values' => '', 'class' => 'date');
$config->riskmanage->search['params']['actualClosedDate']  = array('operator' => '=', 'control' => 'input',  'values' => '', 'class' => 'date');
$config->riskmanage->search['params']['createdBy']         = array('operator' => '=', 'control' => 'select',  'values' => 'users');
$config->riskmanage->search['params']['createdDate']       = array('operator' => '=', 'control' => 'input',  'values' => '', 'class' => 'date');
$config->riskmanage->search['params']['resolution']        = array('operator' => 'include', 'control' => 'input',  'values' => '');
$config->riskmanage->search['params']['resolvedBy']        = array('operator' => '=', 'control' => 'select',  'values' => 'users');
$config->riskmanage->search['params']['activateBy']        = array('operator' => '=', 'control' => 'select',  'values' => 'users');
$config->riskmanage->search['params']['assignedTo']        = array('operator' => '=', 'control' => 'select',  'values' => 'users');
$config->riskmanage->search['params']['cancelBy']          = array('operator' => '=', 'control' => 'select',  'values' => 'users');
$config->riskmanage->search['params']['hangupBy']          = array('operator' => '=', 'control' => 'select',  'values' => 'users');
$config->riskmanage->search['params']['trackedBy']         = array('operator' => '=', 'control' => 'select',  'values' => 'users');

$config->riskmanage->search['fields']['code']              = $lang->riskmanage->code;
$config->riskmanage->search['params']['code']              = array('operator' => 'include', 'control' => 'input',  'values' => '');