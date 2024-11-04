<?php
$config->componentthirdaccount = new stdclass();
$config->componentthirdaccount->list = new stdclass();
$config->componentthirdaccount->list->exportFields = 'appname,productname,productversion,productdept,productconnect,componentname,componentversion,vulnerabilityLevel,comment';

/* Search. */
global $lang;
$config->componentthirdaccount->search['module'] = 'componentthirdaccount';
$config->componentthirdaccount->search['fields']['appname']                     = $lang->componentthirdaccount->appname;
$config->componentthirdaccount->search['fields']['productname']                 = $lang->componentthirdaccount->productname;

$config->componentthirdaccount->search['fields']['productversion']              = $lang->componentthirdaccount->productversion;
$config->componentthirdaccount->search['fields']['componentname']               = $lang->componentthirdaccount->componentname;

$config->componentthirdaccount->search['fields']['componentversion']            = $lang->componentthirdaccount->componentversion;
$config->componentthirdaccount->search['fields']['customComponent']             = $lang->componentthirdaccount->customComponent;
$config->componentthirdaccount->search['fields']['customComponentVersion']      = $lang->componentthirdaccount->customComponentVersion;
$config->componentthirdaccount->search['fields']['productdept']                 = $lang->componentthirdaccount->productdept;

$config->componentthirdaccount->search['fields']['productconnect']              = $lang->componentthirdaccount->productconnect;
$config->componentthirdaccount->search['fields']['vulnerabilityLevel']          = $lang->componentthirdaccount->vulnerabilityLevel;
$config->componentthirdaccount->search['fields']['comment']                     = $lang->componentthirdaccount->comment;
/*迭代25 需求 去掉*/
/*$config->componentthirdaccount->search['fields']['customComponent']                     = $lang->componentthirdaccount->customComponent;
$config->componentthirdaccount->search['fields']['customComponentVersion']                     = $lang->componentthirdaccount->customComponentVersion;*/




$config->componentthirdaccount->search['params']['appname']                      = array('operator' => '=', 'control' => 'select', 'values' => array());
$config->componentthirdaccount->search['params']['productname']                  = array('operator' => '=', 'control' => 'select', 'values' => array());
$config->componentthirdaccount->search['params']['productversion']               = array('operator' => '=', 'control' => 'select', 'values' => array());
$config->componentthirdaccount->search['params']['productdept']                  = array('operator' => '=', 'control' => 'select', 'values' => array());
$config->componentthirdaccount->search['params']['productconnect']               = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->componentthirdaccount->search['params']['componentname']                = array('operator' => '=', 'control' => 'select', 'values' => array());
$config->componentthirdaccount->search['params']['componentversion']             = array('operator' => '=', 'control' => 'select', 'values' => array());
$config->componentthirdaccount->search['params']['customComponent']              = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->componentthirdaccount->search['params']['customComponentVersion']       = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->componentthirdaccount->search['params']['vulnerabilityLevel']           = array('operator' => '=', 'control' => 'select', 'values' => array());
$config->componentthirdaccount->search['params']['comment']                      = array('operator' => 'include', 'control' => 'input', 'values' => '');
/*迭代25 需求 去掉*/
/*$config->componentthirdaccount->search['params']['customComponent']                       = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->componentthirdaccount->search['params']['customComponentVersion']                       = array('operator' => 'include', 'control' => 'input', 'values' => '');*/
