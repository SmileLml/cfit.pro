<?php
$config->testcase->datatable->defaultField = array('id', 'pri', 'title', 'product', 'project', 'type', 'categories', 'openedBy', 'lastRunner', 'lastRunDate', 'lastRunResult', 'status', 'bugs', 'results', 'stepNumber', 'actions');

$config->testcase->datatable->fieldList['categories']['title']    = 'categories';
$config->testcase->datatable->fieldList['categories']['fixed']    = 'no';
$config->testcase->datatable->fieldList['categories']['width']    = '120';
$config->testcase->datatable->fieldList['categories']['required'] = 'no';

$config->testcase->search['fields']['categories'] = $lang->testcase->categories;

$config->testcase->search['params']['categories'] = array('operator' => 'include', 'control' => 'select', 'values' => $lang->testcase->categoryList);
