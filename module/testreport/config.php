<?php

$config->testreport->create = new stdclass();

$config->testreport->create->requiredFields = 'title,owner';

$config->testreport->edit = new stdclass();

$config->testreport->edit->requiredFields = 'title,owner';

$config->testreport->editor = new stdclass();

$config->testreport->editor->create = ['id' => 'report', 'tools' => 'simpleTools'];
$config->testreport->editor->edit   = ['id' => 'report', 'tools' => 'simpleTools'];

$config->testreport->datatable = new stdclass();

$config->testreport->datatable->defaultField = ['id', 'title', 'createdBy', 'createdDate', 'product', 'project', 'testtask', 'actions'];

$config->testreport->datatable->fieldList['id']['title']    = 'idAB';
$config->testreport->datatable->fieldList['id']['fixed']    = 'left';
$config->testreport->datatable->fieldList['id']['width']    = '70';
$config->testreport->datatable->fieldList['id']['required'] = 'yes';

$config->testreport->datatable->fieldList['title']['title']    = 'title';
$config->testreport->datatable->fieldList['title']['fixed']    = 'left';
$config->testreport->datatable->fieldList['title']['width']    = '200';
$config->testreport->datatable->fieldList['title']['required'] = 'no';

$config->testreport->datatable->fieldList['createdBy']['title']    = 'createdBy';
$config->testreport->datatable->fieldList['createdBy']['fixed']    = 'no';
$config->testreport->datatable->fieldList['createdBy']['width']    = '70';
$config->testreport->datatable->fieldList['createdBy']['required'] = 'no';

$config->testreport->datatable->fieldList['createdDate']['title']    = 'createdDate';
$config->testreport->datatable->fieldList['createdDate']['fixed']    = 'no';
$config->testreport->datatable->fieldList['createdDate']['width']    = '70';
$config->testreport->datatable->fieldList['createdDate']['required'] = 'no';

$config->testreport->datatable->fieldList['product']['title']    = 'product';
$config->testreport->datatable->fieldList['product']['fixed']    = 'no';
$config->testreport->datatable->fieldList['product']['width']    = '120';
$config->testreport->datatable->fieldList['product']['required'] = 'no';

$config->testreport->datatable->fieldList['project']['title']    = 'project';
$config->testreport->datatable->fieldList['project']['fixed']    = 'no';
$config->testreport->datatable->fieldList['project']['width']    = '120';
$config->testreport->datatable->fieldList['project']['required'] = 'no';

$config->testreport->datatable->fieldList['testtask']['title']    = 'testtask';
$config->testreport->datatable->fieldList['testtask']['fixed']    = 'no';
$config->testreport->datatable->fieldList['testtask']['width']    = '70';
$config->testreport->datatable->fieldList['testtask']['required'] = 'no';

$config->testreport->datatable->fieldList['actions']['title']    = 'actions';
$config->testreport->datatable->fieldList['actions']['fixed']    = 'right';
$config->testreport->datatable->fieldList['actions']['width']    = '70';
$config->testreport->datatable->fieldList['actions']['required'] = 'yes';
$config->testreport->datatable->fieldList['actions']['sort']     = 'no';
