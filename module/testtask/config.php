<?php

$config->testtask                         = new stdclass();
$config->testtask->create                 = new stdclass();
$config->testtask->edit                   = new stdclass();
$config->testtask->create->requiredFields = 'build,begin,end,name,project';
$config->testtask->edit->requiredFields   = 'build,begin,end,name,project';

$config->testtask->importunitresult                 = new stdclass();
$config->testtask->importunitresult->requiredFields = 'product,project,build,begin,end,name,resultFile';

$config->testtask->editor                   = new stdclass();
$config->testtask->editor->create           = ['id' => 'desc', 'tools' => 'simpleTools'];
$config->testtask->editor->edit             = ['id' => 'desc,report,comment', 'tools' => 'simpleTools'];
$config->testtask->editor->view             = ['id' => 'lastComment', 'tools' => 'simpleTools'];
$config->testtask->editor->start            = ['id' => 'comment', 'tools' => 'simpleTools'];
$config->testtask->editor->close            = ['id' => 'report,comment', 'tools' => 'simpleTools'];
$config->testtask->editor->block            = ['id' => 'comment', 'tools' => 'simpleTools'];
$config->testtask->editor->activate         = ['id' => 'comment', 'tools' => 'simpleTools'];
$config->testtask->editor->importunitresult = ['id' => 'desc', 'tools' => 'simpleTools'];
$config->testtask->editor->runcase          = ['id' => 'stepResults', 'tools' => 'simpleTools'];

$config->testtask->datatable = new stdclass();
$config->testtask->datatable->defaultField = array('id', 'product', 'pri', 'title', 'type', 'assignedTo', 'lastRunner', 'lastRunDate', 'lastRunResult', 'status', 'bugs', 'results', 'stepNumber','actions');


$config->testtask->datatableMainBrowse               = new stdclass();
$config->testtask->datatableMainBrowse->defaultField = ['id', 'oddNumber', 'name', 'product', 'project', 'owner', 'begin', 'end', 'progress', 'status', 'actions'];

$config->testtask->datatableMainBrowse->fieldList['id']['title']    = 'idAB';
$config->testtask->datatableMainBrowse->fieldList['id']['fixed']    = 'left';
$config->testtask->datatableMainBrowse->fieldList['id']['width']    = '70';
$config->testtask->datatableMainBrowse->fieldList['id']['required'] = 'yes';

$config->testtask->datatableMainBrowse->fieldList['oddNumber']['title']    = 'oddNumber';
$config->testtask->datatableMainBrowse->fieldList['oddNumber']['fixed']    = 'left';
$config->testtask->datatableMainBrowse->fieldList['oddNumber']['width']    = '160';
$config->testtask->datatableMainBrowse->fieldList['oddNumber']['required'] = 'yes';

$config->testtask->datatableMainBrowse->fieldList['name']['title']    = 'name';
$config->testtask->datatableMainBrowse->fieldList['name']['fixed']    = 'left';
$config->testtask->datatableMainBrowse->fieldList['name']['width']    = '120';
$config->testtask->datatableMainBrowse->fieldList['name']['required'] = 'yes';

$config->testtask->datatableMainBrowse->fieldList['product']['title']    = 'product';
$config->testtask->datatableMainBrowse->fieldList['product']['fixed']    = 'no';
$config->testtask->datatableMainBrowse->fieldList['product']['width']    = '120';
$config->testtask->datatableMainBrowse->fieldList['product']['required'] = 'no';

$config->testtask->datatableMainBrowse->fieldList['project']['title']    = 'project';
$config->testtask->datatableMainBrowse->fieldList['project']['fixed']    = 'no';
$config->testtask->datatableMainBrowse->fieldList['project']['width']    = '120';
$config->testtask->datatableMainBrowse->fieldList['project']['required'] = 'no';

// $config->testtask->datatableMainBrowse->fieldList['build']['title']    = 'build';
// $config->testtask->datatableMainBrowse->fieldList['build']['fixed']    = 'no';
// $config->testtask->datatableMainBrowse->fieldList['build']['width']    = '120';
// $config->testtask->datatableMainBrowse->fieldList['build']['required'] = 'no';

$config->testtask->datatableMainBrowse->fieldList['owner']['title']    = 'owner';
$config->testtask->datatableMainBrowse->fieldList['owner']['fixed']    = 'no';
$config->testtask->datatableMainBrowse->fieldList['owner']['width']    = '120';
$config->testtask->datatableMainBrowse->fieldList['owner']['required'] = 'no';

$config->testtask->datatableMainBrowse->fieldList['begin']['title']    = 'begin';
$config->testtask->datatableMainBrowse->fieldList['begin']['fixed']    = 'no';
$config->testtask->datatableMainBrowse->fieldList['begin']['width']    = '120';
$config->testtask->datatableMainBrowse->fieldList['begin']['required'] = 'no';

$config->testtask->datatableMainBrowse->fieldList['end']['title']    = 'end';
$config->testtask->datatableMainBrowse->fieldList['end']['fixed']    = 'no';
$config->testtask->datatableMainBrowse->fieldList['end']['width']    = '120';
$config->testtask->datatableMainBrowse->fieldList['end']['required'] = 'no';

$config->testtask->datatableMainBrowse->fieldList['progress']['title']    = 'progress';
$config->testtask->datatableMainBrowse->fieldList['progress']['fixed']    = 'no';
$config->testtask->datatableMainBrowse->fieldList['progress']['width']    = '120';
$config->testtask->datatableMainBrowse->fieldList['progress']['required'] = 'no';
$config->testtask->datatableMainBrowse->fieldList['progress']['sort']     = 'no';

$config->testtask->datatableMainBrowse->fieldList['status']['title']    = 'statusAB';
$config->testtask->datatableMainBrowse->fieldList['status']['fixed']    = 'no';
$config->testtask->datatableMainBrowse->fieldList['status']['width']    = '70';
$config->testtask->datatableMainBrowse->fieldList['status']['required'] = 'no';

$config->testtask->datatableMainBrowse->fieldList['actions']['title']    = 'actions';
$config->testtask->datatableMainBrowse->fieldList['actions']['fixed']    = 'right';
$config->testtask->datatableMainBrowse->fieldList['actions']['width']    = '200';
$config->testtask->datatableMainBrowse->fieldList['actions']['required'] = 'yes';
$config->testtask->datatableMainBrowse->fieldList['actions']['sort']     = 'no';

$config->testtask->unitResultRules          = new stdclass();
$config->testtask->unitResultRules->common  = ['path' => ['testsuite/testtask', 'testtask'], 'name' => ['classname', 'name'], 'failure' => 'failure', 'skipped' => 'skipped', 'suite' => 'name', 'aliasSuite' => ['classname']];
$config->testtask->unitResultRules->phpunit = ['path' => ['test', 'testsuite/testtask', 'testtask'], 'name' => ['className', 'methodName'], 'aliasName' => ['classname', 'name'], 'failure' => 'failure', 'skipped' => 'skipped', 'suite' => 'name', 'aliasSuite' => ['classname', 'className']];


global $app;
global $lang;
$app->loadLang('testtask');  // 如果不在这里加载的话，其他页面可能报错，比如关联用例列表的自定义页功能

$config->testtask->search['module']                      = 'testtask';
$config->testtask->search['fields']['oddNumber']         = $lang->testtask->oddNumber;
$config->testtask->search['fields']['name']              = $lang->testtask->name;
$config->testtask->search['fields']['project']           = $lang->testtask->project;
$config->testtask->search['fields']['build']             = $lang->testtask->searchBuild;
$config->testtask->search['fields']['owner']             = $lang->testtask->owner;
$config->testtask->search['fields']['testrunAssignedTo'] = $lang->testtask->searchAssignedTo;
$config->testtask->search['fields']['testrunLastRunner'] = $lang->testtask->searchLastRunner;
$config->testtask->search['fields']['begin']             = $lang->testtask->begin;
$config->testtask->search['fields']['end']               = $lang->testtask->end;

$config->testtask->search['params']['oddNumber']         = ['operator' => 'include', 'control' => 'input',  'values' => ''];
$config->testtask->search['params']['name']              = ['operator' => 'include', 'control' => 'input',  'values' => ''];
$config->testtask->search['params']['project']           = ['operator' => '=', 'control' => 'select',  'values' => ''];
$config->testtask->search['params']['build']             = ['operator' => 'include', 'control' => 'select',  'values' => '', 'mulit'=>true];
$config->testtask->search['params']['owner']             = ['operator' => '=', 'control' => 'select',  'values' => 'users'];
$config->testtask->search['params']['testrunAssignedTo'] = ['operator' => '=', 'control' => 'select',  'values' => 'users'];
$config->testtask->search['params']['testrunLastRunner'] = ['operator' => '=', 'control' => 'select',  'values' => 'users'];
$config->testtask->search['params']['begin']             = ['operator' => '=', 'control' => 'input',  'values' => '', 'class' => 'date'];
$config->testtask->search['params']['end']               = ['operator' => '=', 'control' => 'input',  'values' => '', 'class' => 'date'];
