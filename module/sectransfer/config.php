<?php
$config->sectransfer->create                      = new stdClass();
$config->sectransfer->create->requiredFields      = 'protransferDesc,publish,app,department,jftype,reason,externalContactEmail,CM,own,sec';
$config->sectransfer->create->requiredFieldsXm    = 'protransferDesc,publish,app,department,jftype,reason,externalContactEmail,CM,own,outproject,inproject,leader,sec';
$config->sectransfer->create->requiredFieldsGd    = 'protransferDesc,publish,app,department,jftype,reason,externalContactEmail,CM,own,sec,subType,secondorderId';
$config->sectransfer->availableBatchCreateFields  = '';

$config->sectransfer->edit                        = new stdClass();
$config->sectransfer->edit->requiredFields        = 'protransferDesc,publish,app,department,jftype,reason,externalContactEmail,CM,own,maxleader';

$config->sectransfer->editor                      = new stdClass();
//$config->sectransfer->editor->create              = array('id' => 'reason', 'tools' => 'simpleTools');
//$config->sectransfer->editor->edit                = array('id' => 'reason', 'tools' => 'simpleTools');
$config->sectransfer->editor->review              = array('id' => 'suggest', 'tools' => 'simpleTools');
$config->sectransfer->editor->deal                = array('id' => 'comment', 'tools' => 'simpleTools');
$config->sectransfer->editor->reject                = array('id' => 'rejectReason', 'tools' => 'simpleTools');

$config->sectransfer->export                      = new stdclass();
$config->sectransfer->export->listFields          = explode(',', "review,status,type");
$config->sectransfer->export->sysListFields       = explode(',', "review");
$config->sectransfer->export->templateFields      = explode(',', "code,review,title,desc,type,raiseBy,raiseDate,status,resolutionBy,resolutionDate,dealDesc,validation,verifyDate");

$config->sectransfer->list                        = new stdclass();
$config->sectransfer->list->exportFields          = "id,protransferDesc,publish,inproject,jftype,app,department,reason,iscode,apply,dept,createdDate,status,approver";

$config->sectransfer->datatable                   = new stdclass();
//20240524 需求收集4116 去掉项目名称内、是否包含源代码
$config->sectransfer->datatable->defaultField     = [
    'id',
    'protransferDesc',
   // 'inproject',
    'jftype',
    'app' ,
    'department',
    'reason',
   // 'iscode',
    'apply',
    'dept',
    'createdDate',
    'status',
    'approver',
    'actions',
    ];

$config->sectransfer->datatable->fieldList = [
    'id'              => ['title' => 'id', 'fixed' => 'left', 'width' => '30', 'required' => 'yes',],
    'protransferDesc' => ['title' => 'protransferDesc', 'fixed' => 'left', 'width' => '230', 'required' => 'yes',],
   // 'inproject'       => ['title' => 'inproject', 'fixed' => 'no', 'width' => '130', 'required' => 'no',],
    'jftype'       => ['title' => 'jftype', 'fixed' => 'no', 'width' => '100', 'required' => 'no',],
    'app'       => ['title' => 'app', 'fixed' => 'no', 'width' => '120', 'required' => 'no',],
    'department'       => ['title' => 'department', 'fixed' => 'no', 'width' => '120', 'required' => 'no',],
    'reason'       => ['title' => 'reason', 'fixed' => 'no', 'width' => '100', 'required' => 'no',],
   // 'iscode'       => ['title' => 'iscode', 'fixed' => 'no', 'width' => '110', 'required' => 'no',],
    'apply'       => ['title' => 'apply', 'fixed' => 'no', 'width' => '110', 'required' => 'no',],
    'dept'       => ['title' => 'dept', 'fixed' => 'no', 'width' => '120', 'required' => 'no',],
    'createdDate'       => ['title' => 'createdDate', 'fixed' => 'no', 'width' => '120', 'required' => 'no',],
    'status'       => ['title' => 'status', 'fixed' => 'right', 'width' => '135', 'required' => 'no',],
    'approver'       => ['title' => 'approver', 'fixed' => 'right', 'width' => '115', 'required' => 'no',],
    'actions'       => ['title' => 'actions', 'fixed' => 'right', 'width' => '150', 'required' => 'no',],
];

/* Search. */
global $lang;
$config->sectransfer->search['module'] = 'sectransfer';
$config->sectransfer->search['fields']['protransferDesc']   =     $lang->sectransfer->protransferDesc;
$config->sectransfer->search['fields']['publish']           =     $lang->sectransfer->publish;
$config->sectransfer->search['fields']['inproject']         =     $lang->sectransfer->inproject;
$config->sectransfer->search['fields']['outproject']        =     $lang->sectransfer->outproject;
$config->sectransfer->search['fields']['jftype']            =     $lang->sectransfer->jftype;
$config->sectransfer->search['fields']['app']               =     $lang->sectransfer->app;
$config->sectransfer->search['fields']['department']        =     $lang->sectransfer->department;
$config->sectransfer->search['fields']['reason']            =     $lang->sectransfer->reason;
$config->sectransfer->search['fields']['iscode']            =     $lang->sectransfer->iscode;
$config->sectransfer->search['fields']['submitBy']          =     $lang->sectransfer->submitBy;
$config->sectransfer->search['fields']['submitDate']        =     $lang->sectransfer->submitDate;
$config->sectransfer->search['fields']['own']               =     $lang->sectransfer->own;
$config->sectransfer->search['fields']['CM']                =     $lang->sectransfer->CM;
$config->sectransfer->search['fields']['leader']            =     $lang->sectransfer->leader;
$config->sectransfer->search['fields']['sec']               =     $lang->sectransfer->sec;
$config->sectransfer->search['fields']['maxleader']         =     $lang->sectransfer->maxleader;
$config->sectransfer->search['fields']['id']                =     $lang->sectransfer->id;
$config->sectransfer->search['fields']['assignedTo']        =     $lang->sectransfer->assignedTo;
$config->sectransfer->search['fields']['status']            =     $lang->sectransfer->status;
$config->sectransfer->search['fields']['dept']              =     $lang->sectransfer->dept;
$config->sectransfer->search['fields']['createdBy']         =     $lang->sectransfer->createdBy;
$config->sectransfer->search['fields']['createdDate']       =     $lang->sectransfer->createdDate;
$config->sectransfer->search['fields']['editedBy']          =     $lang->sectransfer->editedBy;
$config->sectransfer->search['fields']['editedDate']        =     $lang->sectransfer->editedDate;
$config->sectransfer->search['fields']['suggest']           =     $lang->sectransfer->suggest;
$config->sectransfer->search['fields']['result']            =     $lang->sectransfer->result;
$config->sectransfer->search['fields']['approver']          =     $lang->sectransfer->approver;
$config->sectransfer->search['fields']['deleted']           =     $lang->sectransfer->deleted;

$config->sectransfer->search['params']['protransferDesc']   =     array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->sectransfer->search['params']['publish']           =     array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->sectransfer->search['params']['inproject']         =     array('operator' => '=', 'control' => 'select', 'values' => '');
$config->sectransfer->search['params']['outproject']        =    array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->sectransfer->search['params']['jftype']            =     array('operator' => '=', 'control' => 'select', 'values' => array('' => '') + $lang->sectransfer->jftypeList);
$config->sectransfer->search['params']['app']               =     array('operator' => '=', 'control' => 'select', 'values' => '');
$config->sectransfer->search['params']['department']        =     array('operator' => '=', 'control' => 'select', 'values' => '');
$config->sectransfer->search['params']['reason']            =     array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->sectransfer->search['params']['iscode']            =     array('operator' => '=', 'control' => 'select', 'values' => array('' => '') + $lang->sectransfer->oldOrNotList);
$config->sectransfer->search['params']['submitBy']          =     array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->sectransfer->search['params']['submitDate']        =     array('operator' => '>=', 'control' => 'input', 'class' => 'date', 'values' => '');
$config->sectransfer->search['params']['own']               =     array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->sectransfer->search['params']['CM']                =     array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->sectransfer->search['params']['leader']            =     array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->sectransfer->search['params']['sec']               =     array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->sectransfer->search['params']['maxleader']         =     array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->sectransfer->search['params']['id']                =     array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->sectransfer->search['params']['assignedTo']        =     array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->sectransfer->search['params']['dept']              =     array('operator' => '=', 'control' => 'select', 'values' => '');
$config->sectransfer->search['params']['createdBy']         =     array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->sectransfer->search['params']['createdDate']       =     array('operator' => '>=', 'control' => 'input', 'class' => 'date', 'values' => '');
$config->sectransfer->search['params']['editedBy']          =     array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->sectransfer->search['params']['editedDate']        =     array('operator' => '>=', 'control' => 'input', 'class' => 'date', 'values' => '');
$config->sectransfer->search['params']['status']            =     array('operator' => '=', 'control' => 'select', 'values' => $lang->sectransfer->statusListName);
$config->sectransfer->search['params']['suggest']           =     array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->sectransfer->search['params']['result']            =     array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->sectransfer->search['params']['approver']          =     array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->sectransfer->search['params']['deleted']           =     array('operator' => '=', 'control' => 'select', 'values' => $lang->sectransfer->deletedList);