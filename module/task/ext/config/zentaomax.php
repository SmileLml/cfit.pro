<?php
$config->task->create->requiredFields = 'name,type,estStarted,deadline';
$config->task->edit->requiredFields   = $config->task->create->requiredFields;

$config->task->datatable->fieldList['design']['title']    = 'design';
$config->task->datatable->fieldList['design']['fixed']    = 'no';
$config->task->datatable->fieldList['design']['width']    = '200';
$config->task->datatable->fieldList['design']['required'] = 'no';
