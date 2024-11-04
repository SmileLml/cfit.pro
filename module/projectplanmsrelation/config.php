<?php
$config->projectplanmsrelation = new stdclass();

global $lang;
$config->projectplanmsrelation->search['module'] = 'projectplanmsrelation';
$config->projectplanmsrelation->search['fields']['mainPlanID']                     = $lang->projectplanmsrelation->mainPlanID;
$config->projectplanmsrelation->search['params']['mainPlanID']                       = array('operator' => '=', 'control' => 'select', 'values' => '');

