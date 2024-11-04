<?php

$config->workreport = new stdclass();
$config->workreport->create = new stdclass();
$config->workreport->create->requiredFields = '';

$config->workreport->delete = new stdclass();
$config->workreport->delete->requiredFields = 'comment';
$config->workreport->editor = new stdclass();

$config->workreport->editor->delete   = array('id' => 'comment', 'tools' => 'simpleTools');
//$config->workreport->editor->create = array('id' => 'reportDesc,transDesc,insideMilestone,outsideMilestone', 'tools' => 'simpleTools');


$config->workreport->list               = new stdclass();
$config->workreport->list->exportFields = 'beginDate,week,account,projectSpace,activity,stage,objects,consumed,workType,workContent,append ';

// Search.
global $lang;
$config->workreport->search['module']                       = 'workreport';
$config->workreport->search['fields']['project']            = $lang->workreport->projectSpace;
$config->workreport->search['fields']['workType']           = $lang->workreport->workType;
//$config->workreport->search['fields']['beginDate']          = $lang->workreport->beginDate;


$config->workreport->search['params']['project']            = ['operator' => '=', 'control' => 'select', 'values' => ''];
$config->workreport->search['params']['workType']           = ['operator' => '=', 'control' => 'select', 'values' => ''];
//$config->workreport->search['params']['beginDate']          = ['operator' => '>=', 'control' => 'input', 'values' => '','class' => 'date'];
//$config->workreport->search['params']['endDate']            = ['operator' => '=', 'control' => 'input', 'values' => '','class' => 'date'];

