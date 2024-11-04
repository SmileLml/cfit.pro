<?php
$config->project = new stdclass();
$config->project->editor = new stdclass();

$config->project->editor->create   = array('id' => 'desc', 'tools' => 'simpleTools');
$config->project->editor->edit     = array('id' => 'desc', 'tools' => 'simpleTools');
$config->project->editor->close    = array('id' => 'comment', 'tools' => 'simpleTools');
$config->project->editor->suspend  = array('id' => 'comment', 'tools' => 'simpleTools');
$config->project->editor->start    = array('id' => 'comment', 'tools' => 'simpleTools');
$config->project->editor->activate = array('id' => 'comment', 'tools' => 'simpleTools');
$config->project->editor->view     = array('id' => 'lastComment', 'tools' => 'simpleTools');

$config->project->list = new stdclass();
$config->project->list->exportFields = 'id,name,code,template,product,status,begin,end,budget,PM,end,desc';

$config->project->create = new stdclass();
$config->project->edit   = new stdclass();
$config->project->create->requiredFields = 'name,code,begin,end';
$config->project->edit->requiredFields   = 'name,code,begin,end';

$config->project->sortFields         = new stdclass();
$config->project->sortFields->id     = 'id';
$config->project->sortFields->begin  = 'begin';
$config->project->sortFields->end    = 'end';
$config->project->sortFields->status = 'status';
$config->project->sortFields->budget = 'budget';

global $lang;
$config->project->datatable = new stdclass();
$config->project->datatable->defaultField = array('id', 'name', 'PM', 'status', 'begin', 'end', 'budget', 'teamCount','estimate','consume', 'progress', 'actions');

$config->project->datatable->fieldList['id']['title']    = 'ID';
$config->project->datatable->fieldList['id']['fixed']    = 'left';
$config->project->datatable->fieldList['id']['width']    = '60';
$config->project->datatable->fieldList['id']['required'] = 'yes';
$config->project->datatable->fieldList['id']['pri']      = '1';

$config->project->datatable->fieldList['name']['title']    = 'name';
$config->project->datatable->fieldList['name']['fixed']    = 'left';
$config->project->datatable->fieldList['name']['width']    = 'auto';
$config->project->datatable->fieldList['name']['minWidth'] = '180';
$config->project->datatable->fieldList['name']['required'] = 'yes';
$config->project->datatable->fieldList['name']['sort']     = 'no';
$config->project->datatable->fieldList['name']['pri']      = '1';

$config->project->datatable->fieldList['PM']['title']    = 'PM';
$config->project->datatable->fieldList['PM']['fixed']    = 'no';
$config->project->datatable->fieldList['PM']['width']    = '80';
$config->project->datatable->fieldList['PM']['required'] = 'yes';
$config->project->datatable->fieldList['PM']['sort']     = 'no';
$config->project->datatable->fieldList['PM']['pri']      = '2';

$config->project->datatable->fieldList['status']['title']    = 'status';
$config->project->datatable->fieldList['status']['fixed']    = 'left';
$config->project->datatable->fieldList['status']['width']    = '80';
$config->project->datatable->fieldList['status']['required'] = 'no';
$config->project->datatable->fieldList['status']['sort']     = 'yes';
$config->project->datatable->fieldList['status']['pri']      = '2';

$config->project->datatable->fieldList['begin']['title']    = 'begin';
$config->project->datatable->fieldList['begin']['fixed']    = 'no';
$config->project->datatable->fieldList['begin']['width']    = '90';
$config->project->datatable->fieldList['begin']['required'] = 'no';
$config->project->datatable->fieldList['begin']['pri']      = '9';

$config->project->datatable->fieldList['end']['title']    = 'end';
$config->project->datatable->fieldList['end']['fixed']    = 'no';
$config->project->datatable->fieldList['end']['width']    = '90';
$config->project->datatable->fieldList['end']['required'] = 'no';
$config->project->datatable->fieldList['end']['pri']      = '3';

$config->project->datatable->fieldList['budget']['title']    = 'budget';
$config->project->datatable->fieldList['budget']['fixed']    = 'no';
$config->project->datatable->fieldList['budget']['width']    = '80';
$config->project->datatable->fieldList['budget']['required'] = 'yes';
$config->project->datatable->fieldList['budget']['pri']      = '3';

$config->project->datatable->fieldList['teamCount']['title']    = 'teamCount';
$config->project->datatable->fieldList['teamCount']['fixed']    = 'no';
$config->project->datatable->fieldList['teamCount']['width']    = '70';
$config->project->datatable->fieldList['teamCount']['required'] = 'no';
$config->project->datatable->fieldList['teamCount']['sort']     = 'no';
$config->project->datatable->fieldList['teamCount']['pri']      = '8';

$config->project->datatable->fieldList['estimate']['title']    = 'estimate';
$config->project->datatable->fieldList['estimate']['fixed']    = 'no';
$config->project->datatable->fieldList['estimate']['width']    = '70';
$config->project->datatable->fieldList['estimate']['maxWidth'] = '80';
$config->project->datatable->fieldList['estimate']['required'] = 'no';
$config->project->datatable->fieldList['estimate']['sort']     = 'no';
$config->project->datatable->fieldList['estimate']['pri']      = '8';

$config->project->datatable->fieldList['consume']['title']    = 'consume';
$config->project->datatable->fieldList['consume']['fixed']    = 'no';
$config->project->datatable->fieldList['consume']['width']    = '70';
$config->project->datatable->fieldList['consume']['maxWidth'] = '80';
$config->project->datatable->fieldList['consume']['required'] = 'no';
$config->project->datatable->fieldList['consume']['sort']     = 'no';
$config->project->datatable->fieldList['consume']['pri']      = '7';

$config->project->datatable->fieldList['progress']['title']    = 'progress';
$config->project->datatable->fieldList['progress']['fixed']    = 'right';
$config->project->datatable->fieldList['progress']['width']    = '60';
$config->project->datatable->fieldList['progress']['required'] = 'no';
$config->project->datatable->fieldList['progress']['sort']     = 'no';
$config->project->datatable->fieldList['progress']['pri']      = '6';

$config->project->datatable->fieldList['actions']['title']    = 'actions';
$config->project->datatable->fieldList['actions']['fixed']    = 'right';
$config->project->datatable->fieldList['actions']['width']    = '180';
$config->project->datatable->fieldList['actions']['required'] = 'yes';
$config->project->datatable->fieldList['actions']['pri']      = '1';


$config->project->datatableDefect = new stdclass();

$config->project->datatableDefect->defaultField = [
    'id',

    'title',
    'product',
    'project',
    'uatId',
    'pri',
    'severity',
    'source',
    'createdDate',
    'status',
    'nextUser',
    'dealSuggest',
    'syncStatus',
    'actions'
];

$config->project->datatableDefect->fieldList['id']['title']    = 'ID';
$config->project->datatableDefect->fieldList['id']['fixed']    = 'left';
$config->project->datatableDefect->fieldList['id']['width']    = '160';
$config->project->datatableDefect->fieldList['id']['required'] = 'yes';


$config->project->datatableDefect->fieldList['title']['title']    = 'defectTitle';
$config->project->datatableDefect->fieldList['title']['fixed']    = 'no';
$config->project->datatableDefect->fieldList['title']['width']    = '120';
$config->project->datatableDefect->fieldList['title']['required'] = 'yes';

$config->project->datatableDefect->fieldList['product']['title']    = 'product';
$config->project->datatableDefect->fieldList['product']['fixed']    = 'no';
$config->project->datatableDefect->fieldList['product']['width']    = '120';
$config->project->datatableDefect->fieldList['product']['required'] = 'no';

$config->project->datatableDefect->fieldList['project']['title']    = 'project';
$config->project->datatableDefect->fieldList['project']['fixed']    = 'no';
$config->project->datatableDefect->fieldList['project']['width']    = '120';
$config->project->datatableDefect->fieldList['project']['required'] = 'no';

$config->project->datatableDefect->fieldList['uatId']['title']    = 'uatId';
$config->project->datatableDefect->fieldList['uatId']['fixed']    = 'no';
$config->project->datatableDefect->fieldList['uatId']['width']    = '160';
$config->project->datatableDefect->fieldList['uatId']['required'] = 'yes';

$config->project->datatableDefect->fieldList['pri']['title']    = 'pri';
$config->project->datatableDefect->fieldList['pri']['fixed']    = 'no';
$config->project->datatableDefect->fieldList['pri']['width']    = '70';
$config->project->datatableDefect->fieldList['pri']['required'] = 'no';

$config->project->datatableDefect->fieldList['severity']['title']    = 'severity';
$config->project->datatableDefect->fieldList['severity']['fixed']    = 'no';
$config->project->datatableDefect->fieldList['severity']['width']    = '120';
$config->project->datatableDefect->fieldList['severity']['required'] = 'no';

$config->project->datatableDefect->fieldList['source']['title']    = 'source';
$config->project->datatableDefect->fieldList['source']['fixed']    = 'no';
$config->project->datatableDefect->fieldList['source']['width']    = '120';
$config->project->datatableDefect->fieldList['source']['required'] = 'no';

$config->project->datatableDefect->fieldList['createdDate']['title']    = 'createdDate';
$config->project->datatableDefect->fieldList['createdDate']['fixed']    = 'no';
$config->project->datatableDefect->fieldList['createdDate']['width']    = '140';
$config->project->datatableDefect->fieldList['createdDate']['required'] = 'no';

$config->project->datatableDefect->fieldList['status']['title']    = 'status';
$config->project->datatableDefect->fieldList['status']['fixed']    = 'no';
$config->project->datatableDefect->fieldList['status']['width']    = '70';
$config->project->datatableDefect->fieldList['status']['required'] = 'no';

$config->project->datatableDefect->fieldList['nextUser']['title']    = 'nextUser';
$config->project->datatableDefect->fieldList['nextUser']['fixed']    = 'no';
$config->project->datatableDefect->fieldList['nextUser']['width']    = '70';
$config->project->datatableDefect->fieldList['nextUser']['required'] = 'no';

$config->project->datatableDefect->fieldList['dealSuggest']['title']    = 'dealSuggest';
$config->project->datatableDefect->fieldList['dealSuggest']['fixed']    = 'no';
$config->project->datatableDefect->fieldList['dealSuggest']['width']    = '70';
$config->project->datatableDefect->fieldList['dealSuggest']['required'] = 'no';

$config->project->datatableDefect->fieldList['syncStatus']['title']    = 'syncStatus';
$config->project->datatableDefect->fieldList['syncStatus']['fixed']    = 'no';
$config->project->datatableDefect->fieldList['syncStatus']['width']    = '70';
$config->project->datatableDefect->fieldList['syncStatus']['required'] = 'no';

$config->project->datatableDefect->fieldList['actions']['title']    = 'actions';
$config->project->datatableDefect->fieldList['actions']['fixed']    = 'right';
$config->project->datatableDefect->fieldList['actions']['width']    = '120';
$config->project->datatableDefect->fieldList['actions']['required'] = 'yes';

$config->project->datatableTesttask = new stdclass();

$config->project->datatableTesttask->defaultField = [
    'id',
    'oddNumber',
    'name',
    'product',
    'build',
    'owner',
    'begin',
    'end',
    'progress',
    'status',
    'actions',

];

$config->project->datatableTesttask->fieldList['id']['title']    = 'ID';
$config->project->datatableTesttask->fieldList['id']['fixed']    = 'left';
$config->project->datatableTesttask->fieldList['id']['width']    = '60';
$config->project->datatableTesttask->fieldList['id']['required'] = 'yes';

$config->project->datatableTesttask->fieldList['oddNumber']['title']    = 'oddNumber';
$config->project->datatableTesttask->fieldList['oddNumber']['fixed']    = 'no';
$config->project->datatableTesttask->fieldList['oddNumber']['width']    = '160';
$config->project->datatableTesttask->fieldList['oddNumber']['required'] = 'no';

$config->project->datatableTesttask->fieldList['name']['title']    = 'nameAB';
$config->project->datatableTesttask->fieldList['name']['fixed']    = 'no';
$config->project->datatableTesttask->fieldList['name']['width']    = '120';
$config->project->datatableTesttask->fieldList['name']['required'] = 'no';

$config->project->datatableTesttask->fieldList['product']['title']    = 'product';
$config->project->datatableTesttask->fieldList['product']['fixed']    = 'no';
$config->project->datatableTesttask->fieldList['product']['width']    = '120';
$config->project->datatableTesttask->fieldList['product']['required'] = 'no';

$config->project->datatableTesttask->fieldList['build']['title']    = 'build';
$config->project->datatableTesttask->fieldList['build']['fixed']    = 'no';
$config->project->datatableTesttask->fieldList['build']['width']    = '120';
$config->project->datatableTesttask->fieldList['build']['required'] = 'no';

$config->project->datatableTesttask->fieldList['owner']['title']    = 'owner';
$config->project->datatableTesttask->fieldList['owner']['fixed']    = 'no';
$config->project->datatableTesttask->fieldList['owner']['width']    = '120';
$config->project->datatableTesttask->fieldList['owner']['required'] = 'no';

$config->project->datatableTesttask->fieldList['begin']['title']    = 'begin';
$config->project->datatableTesttask->fieldList['begin']['fixed']    = 'no';
$config->project->datatableTesttask->fieldList['begin']['width']    = '120';
$config->project->datatableTesttask->fieldList['begin']['required'] = 'no';

$config->project->datatableTesttask->fieldList['end']['title']    = 'end';
$config->project->datatableTesttask->fieldList['end']['fixed']    = 'no';
$config->project->datatableTesttask->fieldList['end']['width']    = '120';
$config->project->datatableTesttask->fieldList['end']['required'] = 'no';

$config->project->datatableTesttask->fieldList['progress']['title']    = 'progress';
$config->project->datatableTesttask->fieldList['progress']['fixed']    = 'no';
$config->project->datatableTesttask->fieldList['progress']['width']    = '120';
$config->project->datatableTesttask->fieldList['progress']['required'] = 'no';
$config->project->datatableTesttask->fieldList['progress']['sort']     = 'no';

$config->project->datatableTesttask->fieldList['status']['title']    = 'status';
$config->project->datatableTesttask->fieldList['status']['fixed']    = 'no';
$config->project->datatableTesttask->fieldList['status']['width']    = '120';
$config->project->datatableTesttask->fieldList['status']['required'] = 'no';

$config->project->datatableTesttask->fieldList['actions']['title']    = 'actions';
$config->project->datatableTesttask->fieldList['actions']['fixed']    = 'right';
$config->project->datatableTesttask->fieldList['actions']['width']    = '200';
$config->project->datatableTesttask->fieldList['actions']['required'] = 'yes';

$config->project->datatableTestcase = new stdclass();

$config->project->datatableTestcase->defaultField = [
    'id',
    'pri',
    'title',
    'product',
    'type',
    'categories',
    'openedBy',
    'lastRunner',
    'lastRunDate',
    'lastRunResult',
    'status',
    'bugs',
    'results',
    'stepNumber',
    'actions',
];

$config->project->datatableBug = new stdclass();

$config->project->datatableBug->defaultField =
[
    'id',
    'severity',
    'pri',
    'confirmed',
    'title',
    'product',
    'status',
    'openedBy',
    'openedDate',
    'assignedTo',
    'resolution',
    'actions',
];

$config->project->datatableTestsuite = new stdclass();

$config->project->datatableTestsuite->defaultField = ['id', 'name', 'product', 'desc', 'addedBy', 'addedDate', 'actions'];

$config->project->datatableTestsuite->fieldList['id']['title']    = 'idAB';
$config->project->datatableTestsuite->fieldList['id']['fixed']    = 'left';
$config->project->datatableTestsuite->fieldList['id']['width']    = '70';
$config->project->datatableTestsuite->fieldList['id']['required'] = 'yes';

$config->project->datatableTestsuite->fieldList['name']['title']    = 'nameAB';
$config->project->datatableTestsuite->fieldList['name']['fixed']    = 'left';
$config->project->datatableTestsuite->fieldList['name']['width']    = 'auto';
$config->project->datatableTestsuite->fieldList['name']['required'] = 'yes';

$config->project->datatableTestsuite->fieldList['product']['title']    = 'product';
$config->project->datatableTestsuite->fieldList['product']['fixed']    = 'left';
$config->project->datatableTestsuite->fieldList['product']['width']    = '120';
$config->project->datatableTestsuite->fieldList['product']['required'] = 'no';

$config->project->datatableTestsuite->fieldList['desc']['title']    = 'desc';
$config->project->datatableTestsuite->fieldList['desc']['fixed']    = 'no';
$config->project->datatableTestsuite->fieldList['desc']['width']    = '120';
$config->project->datatableTestsuite->fieldList['desc']['required'] = 'no';

$config->project->datatableTestsuite->fieldList['addedBy']['title']    = 'addedBy';
$config->project->datatableTestsuite->fieldList['addedBy']['fixed']    = 'no';
$config->project->datatableTestsuite->fieldList['addedBy']['width']    = '120';
$config->project->datatableTestsuite->fieldList['addedBy']['required'] = 'no';

$config->project->datatableTestsuite->fieldList['addedDate']['title']    = 'addedDate';
$config->project->datatableTestsuite->fieldList['addedDate']['fixed']    = 'no';
$config->project->datatableTestsuite->fieldList['addedDate']['width']    = '180';
$config->project->datatableTestsuite->fieldList['addedDate']['required'] = 'no';

$config->project->datatableTestsuite->fieldList['actions']['title']    = 'actions';
$config->project->datatableTestsuite->fieldList['actions']['fixed']    = 'right';
$config->project->datatableTestsuite->fieldList['actions']['width']    = '100';
$config->project->datatableTestsuite->fieldList['actions']['required'] = 'yes';
$config->project->datatableTestsuite->fieldList['actions']['sort']     = 'no';


$config->project->datatableTestreport = new stdclass();

$config->project->datatableTestreport->defaultField = array('id', 'title', 'createdBy', 'createdDate', 'product', 'project', 'tasks', 'actions');

$config->project->datatableTestreport->fieldList['id']['title']    = 'idAB';
$config->project->datatableTestreport->fieldList['id']['fixed']    = 'left';
$config->project->datatableTestreport->fieldList['id']['width']    = '70';
$config->project->datatableTestreport->fieldList['id']['required'] = 'yes';

$config->project->datatableTestreport->fieldList['title']['title']    = 'testreportTitle';
$config->project->datatableTestreport->fieldList['title']['fixed']    = 'left';
$config->project->datatableTestreport->fieldList['title']['width']    = '200';
$config->project->datatableTestreport->fieldList['title']['required'] = 'yes';

$config->project->datatableTestreport->fieldList['createdBy']['title']    = 'createdBy';
$config->project->datatableTestreport->fieldList['createdBy']['fixed']    = 'no';
$config->project->datatableTestreport->fieldList['createdBy']['width']    = '70';
$config->project->datatableTestreport->fieldList['createdBy']['required'] = 'no';

$config->project->datatableTestreport->fieldList['createdDate']['title']    = 'createdDate';
$config->project->datatableTestreport->fieldList['createdDate']['fixed']    = 'no';
$config->project->datatableTestreport->fieldList['createdDate']['width']    = '70';
$config->project->datatableTestreport->fieldList['createdDate']['required'] = 'no';

$config->project->datatableTestreport->fieldList['product']['title']    = 'product';
$config->project->datatableTestreport->fieldList['product']['fixed']    = 'no';
$config->project->datatableTestreport->fieldList['product']['width']    = '120';
$config->project->datatableTestreport->fieldList['product']['required'] = 'no';

$config->project->datatableTestreport->fieldList['project']['title']    = 'project';
$config->project->datatableTestreport->fieldList['project']['fixed']    = 'no';
$config->project->datatableTestreport->fieldList['project']['width']    = '70';
$config->project->datatableTestreport->fieldList['project']['required'] = 'no';

$config->project->datatableTestreport->fieldList['tasks']['title']    = 'tasks';
$config->project->datatableTestreport->fieldList['tasks']['fixed']    = 'no';
$config->project->datatableTestreport->fieldList['tasks']['width']    = '70';
$config->project->datatableTestreport->fieldList['tasks']['required'] = 'no';

$config->project->datatableTestreport->fieldList['actions']['title']    = 'actions';
$config->project->datatableTestreport->fieldList['actions']['fixed']    = 'right';
$config->project->datatableTestreport->fieldList['actions']['width']    = '70';
$config->project->datatableTestreport->fieldList['actions']['required'] = 'yes';
$config->project->datatableTestreport->fieldList['actions']['sort']     = 'no';

$config->project->list               = new stdclass();
$config->project->list->exportFields = 'id, name, code, PM, begin, end, planDuration,realBegan, realEnd, realDuration, workload, planHour, realHour, status,insideStatus';
