<?php

$config->testsuite         = new stdclass();
$config->testsuite->create = new stdclass();
$config->testsuite->edit   = new stdclass();

$config->testsuite->create->requiredFields = 'name';
$config->testsuite->edit->requiredFields   = 'name';

$config->testsuite->editor = new stdclass();

$config->testsuite->editor->create = ['id' => 'desc', 'tools' => 'simpleTools'];
$config->testsuite->editor->edit   = ['id' => 'desc', 'tools' => 'simpleTools'];

$config->testsuite->datatable = new stdclass();

$config->testsuite->datatable->defaultField = ['id', 'name', 'product', 'desc', 'addedBy', 'addedDate', 'actions'];

$config->testsuite->datatable->fieldList['id']['title']    = 'idAB';
$config->testsuite->datatable->fieldList['id']['fixed']    = 'left';
$config->testsuite->datatable->fieldList['id']['width']    = '70';
$config->testsuite->datatable->fieldList['id']['required'] = 'yes';

$config->testsuite->datatable->fieldList['name']['title']    = 'name';
$config->testsuite->datatable->fieldList['name']['fixed']    = 'left';
$config->testsuite->datatable->fieldList['name']['width']    = 'auto';
$config->testsuite->datatable->fieldList['name']['required'] = 'yes';

$config->testsuite->datatable->fieldList['product']['title']    = 'product';
$config->testsuite->datatable->fieldList['product']['fixed']    = 'left';
$config->testsuite->datatable->fieldList['product']['width']    = '120';
$config->testsuite->datatable->fieldList['product']['required'] = 'no';

$config->testsuite->datatable->fieldList['desc']['title']    = 'desc';
$config->testsuite->datatable->fieldList['desc']['fixed']    = 'no';
$config->testsuite->datatable->fieldList['desc']['width']    = '120';
$config->testsuite->datatable->fieldList['desc']['required'] = 'no';

$config->testsuite->datatable->fieldList['addedBy']['title']    = 'addedBy';
$config->testsuite->datatable->fieldList['addedBy']['fixed']    = 'no';
$config->testsuite->datatable->fieldList['addedBy']['width']    = '120';
$config->testsuite->datatable->fieldList['addedBy']['required'] = 'no';

$config->testsuite->datatable->fieldList['addedDate']['title']    = 'addedDate';
$config->testsuite->datatable->fieldList['addedDate']['fixed']    = 'no';
$config->testsuite->datatable->fieldList['addedDate']['width']    = '180';
$config->testsuite->datatable->fieldList['addedDate']['required'] = 'no';

$config->testsuite->datatable->fieldList['actions']['title']    = 'actions';
$config->testsuite->datatable->fieldList['actions']['fixed']    = 'right';
$config->testsuite->datatable->fieldList['actions']['width']    = '100';
$config->testsuite->datatable->fieldList['actions']['required'] = 'yes';
$config->testsuite->datatable->fieldList['actions']['sort']     = 'no';

$config->testsuite->custom = new stdclass();

$config->testsuite->custom->createFields = 'stage,pri,keywords';
$config->testsuite->customCreateFields   = 'stage,pri,keywords';
