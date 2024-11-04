<?php

$config->caselib                             = new stdclass();
$config->caselib->create                     = new stdclass();
$config->caselib->edit                       = new stdclass();
$config->caselib->createcase                 = new stdclass();
$config->caselib->create->requiredFields     = 'name';
$config->caselib->edit->requiredFields       = 'name';
$config->caselib->createcase->requiredFields = 'title,type';

$config->caselib->editor         = new stdclass();
$config->caselib->editor->create = ['id' => 'desc', 'tools' => 'simpleTools'];
$config->caselib->editor->edit   = ['id' => 'desc', 'tools' => 'simpleTools'];

$config->caselib->datatable               = new stdclass();
$config->caselib->datatable->defaultField = ['id', 'pri', 'title', 'type', 'categories', 'openedBy', 'status', 'actions'];

$config->caselib->datatable->fieldList['id']['title']    = 'idAB';
$config->caselib->datatable->fieldList['id']['fixed']    = 'left';
$config->caselib->datatable->fieldList['id']['width']    = '70';
$config->caselib->datatable->fieldList['id']['required'] = 'yes';

$config->caselib->datatable->fieldList['pri']['title']    = 'pri';
$config->caselib->datatable->fieldList['pri']['fixed']    = 'left';
$config->caselib->datatable->fieldList['pri']['width']    = '70';
$config->caselib->datatable->fieldList['pri']['required'] = 'yes';

$config->caselib->datatable->fieldList['title']['title']    = 'title';
$config->caselib->datatable->fieldList['title']['fixed']    = 'left';
$config->caselib->datatable->fieldList['title']['width']    = 'auto';
$config->caselib->datatable->fieldList['title']['required'] = 'yes';

$config->caselib->datatable->fieldList['type']['title']    = 'type';
$config->caselib->datatable->fieldList['type']['fixed']    = 'no';
$config->caselib->datatable->fieldList['type']['width']    = '90';
$config->caselib->datatable->fieldList['type']['required'] = 'no';

$config->caselib->datatable->fieldList['categories']['title']    = 'categories';
$config->caselib->datatable->fieldList['categories']['fixed']    = 'no';
$config->caselib->datatable->fieldList['categories']['width']    = '90';
$config->caselib->datatable->fieldList['categories']['required'] = 'no';

$config->caselib->datatable->fieldList['openedBy']['title']    = 'openedByAB';
$config->caselib->datatable->fieldList['openedBy']['fixed']    = 'no';
$config->caselib->datatable->fieldList['openedBy']['width']    = '90';
$config->caselib->datatable->fieldList['openedBy']['required'] = 'no';

$config->caselib->datatable->fieldList['status']['title']    = 'statusAB';
$config->caselib->datatable->fieldList['status']['fixed']    = 'no';
$config->caselib->datatable->fieldList['status']['width']    = '70';
$config->caselib->datatable->fieldList['status']['required'] = 'no';

$config->caselib->datatable->fieldList['actions']['title']    = 'actions';
$config->caselib->datatable->fieldList['actions']['fixed']    = 'right';
$config->caselib->datatable->fieldList['actions']['width']    = '120';
$config->caselib->datatable->fieldList['actions']['required'] = 'yes';
$config->caselib->datatable->fieldList['actions']['sort']     = 'no';

$config->caselib->custom               = new stdclass();
$config->caselib->custom->createFields = 'stage,pri,keywords';
$config->caselib->customCreateFields   = 'stage,pri,keywords';

$config->caselib->exportFields = 'id,priAB,title,module,type,stage,categories,precondition,steps,expect,keywords';
