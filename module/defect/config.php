<?php
$config->defect               = new stdclass();
$config->defect->edit         = new stdclass();
$config->defect->deal         = new stdclass();
$config->defect->confirm      = new stdclass();
$config->defect->defectbug    = new stdclass();
$config->defect->change       = new stdclass();
$config->defect->rePush       = new stdclass();


$config->defect->edit->requiredFields         = 'app,project,title,pri,issues,type,frequency,developer,severity,dealUser,resolution';
$config->defect->deal->requiredFields         = 'linkProduct,dealSuggest,dealComment,EditorImpactscope,consumed,progress';
$config->defect->change->requiredFields         = 'linkProduct,dealSuggest,dealComment,EditorImpactscope,cc,consumed,dealUser,resolution';
$config->defect->confirm->requiredFields        = 'dealUser,consumed';
$config->defect->defectbug->requiredFields = 'app,product,project,title,pri,issues,type,frequency,developer,severity,linkProduct,dealSuggest,dealComment,EditorImpactscope';
$config->defect->editor = new stdclass();

$config->defect->editor->edit     = array('id' => 'comment,issues', 'tools' => 'simpleTools');
$config->defect->editor->deal     = array('id' => 'comment', 'tools' => 'simpleTools');
$config->defect->editor->confirm    = array('id' => 'comment', 'tools' => 'simpleTools');
$config->defect->editor->change    = array('id' => 'comment', 'tools' => 'simpleTools');
$config->defect->editor->repush    = array('id' => 'comment', 'tools' => 'simpleTools');

$config->defect->export = new stdclass();

$config->defect->export->listFields     = explode(',', "source,status,dealUser,summary,id,type,subtype,app,,createdDept,acceptUser,acceptDept,team,union,exceptDoneDate,closeReason,ifAccept,createdBy,createdDate,
                                            editedBy,editedDate,closedBy,closedDate,planstartDate,planoverDate,startDate,overDate,progress,consultRes,testRes,dealRes");

$config->defect->list = new stdclass();
//$config->defect->list->exportFields = 'code,status,dealUser,summary,id,ifAccept,type,subtype,source,team,union,exceptDoneDate,app,acceptUser,acceptDept,planstartDate,planoverDate,startDate,overDate,createdDept,createdBy,createdDate,
//                                            editedBy,editedDate,closeReason,closedBy,closedDate,progress,consultRes,testRes,dealRes';
$config->defect->list->exportFields = "source,app,product,project,reportUser,reportDate,pri,type,childType,severity,frequency,developer,dept,testEngineer,
                                                   testType,projectManager,rounds,testEnvironment,verification,testrequest,productenroll,nextUser,createdBy,createdDate,confirmedBy,
                                                   confirmedDate,dealedBy,dealedDate,bugId,uatId,syncStatus,defectTitle,steps,testCase,Dropdown_suspensionreason,testAdvice,resolution,resolvedBuild,
                                                   resolvedDate,linkProduct,ifTest,dealSuggest,dealComment,changeDate,submitChangeDate,EditorImpactscope,ifHisIssue,changeStatus,approverName,
                                                   approverDate,feedbackNum";


$config->defect->import = new stdclass();

$config->defect->showImport = new stdclass();

/* Search. */
global $lang;
$config->defect->search['module'] = 'defect';
$config->defect->search['fields']['code']          = $lang->defect->idAB;
$config->defect->search['fields']['uatId']          = $lang->defect->uatId;
$config->defect->search['fields']['source']        = $lang->defect->source;
$config->defect->search['fields']['app']           = $lang->defect->app;
$config->defect->search['fields']['product']       = $lang->defect->product;
$config->defect->search['fields']['project']       = $lang->defect->project;
$config->defect->search['fields']['reportUser']    = $lang->defect->reportUser;
$config->defect->search['fields']['reportDate']    = $lang->defect->reportDate;
$config->defect->search['fields']['pri']           = $lang->defect->pri;
$config->defect->search['fields']['type']          = $lang->defect->type;
$config->defect->search['fields']['childType']     = $lang->defect->childType;
$config->defect->search['fields']['severity']      = $lang->defect->severity;
$config->defect->search['fields']['frequency']     = $lang->defect->frequency;
$config->defect->search['fields']['developer']     = $lang->defect->developer;
$config->defect->search['fields']['testrequestCode']   = $lang->defect->testrequest;
$config->defect->search['fields']['productenrollCode'] = $lang->defect->productenroll;
$config->defect->search['fields']['dealUser']      = $lang->defect->nextUser;
$config->defect->search['fields']['syncStatus']    = $lang->defect->syncStatus;
$config->defect->search['fields']['status']        = $lang->defect->status;
$config->defect->search['fields']['title']         = $lang->defect->defectTitle;
$config->defect->search['fields']['issues']        = $lang->defect->steps;
$config->defect->search['fields']['changeStatus']  = $lang->defect->changeStatus;
$config->defect->search['fields']['resolution']    = $lang->defect->resolution;
$config->defect->search['fields']['ifTest']        = $lang->defect->ifTest;
$config->defect->search['fields']['dealSuggest']   = $lang->defect->dealSuggest;
$config->defect->search['fields']['createdDate']   = $lang->defect->createdDate;
$config->defect->search['fields']['dept']   = $lang->defect->dept;

$config->defect->search['params']['code']          = array('operator' => 'include', 'control' => 'input',  'values' => '');
$config->defect->search['params']['uatId']          = array('operator' => 'include', 'control' => 'input',  'values' => '');
$config->defect->search['params']['source']        = array('operator' => '=', 'control' => 'select', 'values' => $lang->defect->sourceList);
$config->defect->search['params']['app']           = array('operator' => '=', 'control' => 'select', 'values' => array());
$config->defect->search['params']['product']       = array('operator' => '=', 'control' => 'select',  'values' =>array());
$config->defect->search['params']['project']       = array('operator' => '=', 'control' => 'select',  'values' =>array());
$config->defect->search['params']['reportUser']    = array('operator' => '=', 'control' => 'select',  'values' =>'users');
$config->defect->search['params']['reportDate']    = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->defect->search['params']['createdDate']   = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->defect->search['params']['pri']           = array('operator' => '=', 'control' => 'select', 'values' => array());
$config->defect->search['params']['type']          = array('operator' => '=', 'control' => 'select', 'values' => array());
$config->defect->search['params']['childType']     = array('operator' => '=', 'control' => 'select', 'values' => array());
$config->defect->search['params']['severity']      = array('operator' => '=', 'control' => 'select', 'values' => array());
$config->defect->search['params']['frequency']     = array('operator' => '=', 'control' => 'select', 'values' => array());
$config->defect->search['params']['developer']     = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->defect->search['params']['testrequestCode']   = array('operator' => '=', 'control' => 'input',  'values' => '');
$config->defect->search['params']['productenrollCode'] = array('operator' => '=', 'control' => 'input',  'values' => '');
$config->defect->search['params']['dealUser']      = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->defect->search['params']['syncStatus']    = array('operator' => '=', 'control' => 'select', 'values' => $lang->defect->syncStatusList);
$config->defect->search['params']['status']        = array('operator' => '=', 'control' => 'select', 'values' => $lang->defect->statusList);
$config->defect->search['params']['title']         = array('operator' => 'include', 'control' => 'input',  'values' => '');
$config->defect->search['params']['issues']        = array('operator' => 'include', 'control' => 'input',  'values' => '');
$config->defect->search['params']['changeStatus']  = array('operator' => 'include', 'control' => 'input',  'values' => '');
$config->defect->search['params']['resolution']    = array('operator' => '=', 'control' => 'select', 'values' => array());
$config->defect->search['params']['ifTest']        = array('operator' => '=', 'control' => 'select', 'values' => $lang->defect->ifList);
$config->defect->search['params']['dealSuggest']   = array('operator' => '=', 'control' => 'select',  'values' => $lang->defect->dealSuggestList);
$config->defect->search['params']['dept']   = array('operator' => '=', 'control' => 'select',  'values' => '');

$config->defect->datatable = new stdclass();

$config->defect->datatable->defaultField = [
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

$config->defect->datatable->fieldList['id']['title']    = 'ID';
$config->defect->datatable->fieldList['id']['fixed']    = 'left';
$config->defect->datatable->fieldList['id']['width']    = '160';
$config->defect->datatable->fieldList['id']['required'] = 'yes';


$config->defect->datatable->fieldList['title']['title']    = 'defectTitle';
$config->defect->datatable->fieldList['title']['fixed']    = 'no';
$config->defect->datatable->fieldList['title']['width']    = '120';
$config->defect->datatable->fieldList['title']['required'] = 'yes';

$config->defect->datatable->fieldList['product']['title']    = 'product';
$config->defect->datatable->fieldList['product']['fixed']    = 'no';
$config->defect->datatable->fieldList['product']['width']    = '120';
$config->defect->datatable->fieldList['product']['required'] = 'no';

$config->defect->datatable->fieldList['project']['title']    = 'project';
$config->defect->datatable->fieldList['project']['fixed']    = 'no';
$config->defect->datatable->fieldList['project']['width']    = '120';
$config->defect->datatable->fieldList['project']['required'] = 'no';

$config->defect->datatable->fieldList['uatId']['title']    = 'uatId';
$config->defect->datatable->fieldList['uatId']['fixed']    = 'no';
$config->defect->datatable->fieldList['uatId']['width']    = '160';
$config->defect->datatable->fieldList['uatId']['required'] = 'yes';

$config->defect->datatable->fieldList['pri']['title']    = 'pri';
$config->defect->datatable->fieldList['pri']['fixed']    = 'no';
$config->defect->datatable->fieldList['pri']['width']    = '70';
$config->defect->datatable->fieldList['pri']['required'] = 'no';

$config->defect->datatable->fieldList['severity']['title']    = 'severity';
$config->defect->datatable->fieldList['severity']['fixed']    = 'no';
$config->defect->datatable->fieldList['severity']['width']    = '120';
$config->defect->datatable->fieldList['severity']['required'] = 'no';

$config->defect->datatable->fieldList['source']['title']    = 'source';
$config->defect->datatable->fieldList['source']['fixed']    = 'no';
$config->defect->datatable->fieldList['source']['width']    = '120';
$config->defect->datatable->fieldList['source']['required'] = 'no';

$config->defect->datatable->fieldList['createdDate']['title']    = 'createdDate';
$config->defect->datatable->fieldList['createdDate']['fixed']    = 'no';
$config->defect->datatable->fieldList['createdDate']['width']    = '140';
$config->defect->datatable->fieldList['createdDate']['required'] = 'no';

$config->defect->datatable->fieldList['status']['title']    = 'status';
$config->defect->datatable->fieldList['status']['fixed']    = 'no';
$config->defect->datatable->fieldList['status']['width']    = '70';
$config->defect->datatable->fieldList['status']['required'] = 'no';

$config->defect->datatable->fieldList['nextUser']['title']    = 'nextUser';
$config->defect->datatable->fieldList['nextUser']['fixed']    = 'no';
$config->defect->datatable->fieldList['nextUser']['width']    = '70';
$config->defect->datatable->fieldList['nextUser']['required'] = 'no';

$config->defect->datatable->fieldList['dealSuggest']['title']    = 'dealSuggest';
$config->defect->datatable->fieldList['dealSuggest']['fixed']    = 'no';
$config->defect->datatable->fieldList['dealSuggest']['width']    = '70';
$config->defect->datatable->fieldList['dealSuggest']['required'] = 'no';

$config->defect->datatable->fieldList['syncStatus']['title']    = 'syncStatus';
$config->defect->datatable->fieldList['syncStatus']['fixed']    = 'no';
$config->defect->datatable->fieldList['syncStatus']['width']    = '70';
$config->defect->datatable->fieldList['syncStatus']['required'] = 'no';

$config->defect->datatable->fieldList['actions']['title']    = 'actions';
$config->defect->datatable->fieldList['actions']['fixed']    = 'right';
$config->defect->datatable->fieldList['actions']['width']    = '120';
$config->defect->datatable->fieldList['actions']['required'] = 'yes';


