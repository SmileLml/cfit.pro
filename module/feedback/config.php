<?php
$config->feedback->create = new stdclass();
$config->feedback->create->requiredFields = 'product,title';
$config->feedback->edit = new stdclass();
$config->feedback->edit->requiredFields = 'product,title';

$config->feedback->editor = new stdclass();
$config->feedback->editor->create   = array('id' => 'desc', 'tools' => 'simpleTools');
$config->feedback->editor->edit     = array('id' => 'desc', 'tools' => 'simpleTools');
$config->feedback->editor->view     = array('id' => 'lastComment', 'tools' => 'simpleTools');
$config->feedback->editor->comment  = array('id' => 'comment', 'tools' => 'simpleTools');
$config->feedback->editor->close    = array('id' => 'comment', 'tools' => 'simpleTools');
$config->feedback->editor->assignto = array('id' => 'comment', 'tools' => 'simpleTools');
$config->feedback->editor->review   = array('id' => 'comment', 'tools' => 'simpleTools');

$config->feedback->exportFields = 'id,module,product,title,desc,status,openedBy,openedDate,assignedTo,assignedDate,processedBy,processedDate,closedBy,closedDate,closedReason,mailto,editedBy,editedDate';
$config->feedback->frontFields  = 'id,module,product,title,desc,status,closedReason';

global $lang;
$config->feedback->search['module'] = 'feedback';
$config->feedback->search['fields']['title']         = $lang->feedback->title;
$config->feedback->search['fields']['id']            = 'ID';
$config->feedback->search['fields']['product']       = $lang->feedback->product;
$config->feedback->search['fields']['module']        = $lang->feedback->module;
$config->feedback->search['fields']['status']        = $lang->feedback->status;
$config->feedback->search['fields']['desc']          = $lang->feedback->desc;
$config->feedback->search['fields']['assignedTo']    = $lang->feedback->assignedTo;
$config->feedback->search['fields']['mailto']        = $lang->feedback->mailto;
$config->feedback->search['fields']['public']        = $lang->feedback->public;
$config->feedback->search['fields']['openedBy']      = $lang->feedback->openedBy;
$config->feedback->search['fields']['openedDate']    = $lang->feedback->openedDate;
$config->feedback->search['fields']['processedBy']   = $lang->feedback->processedBy;
$config->feedback->search['fields']['processedDate'] = $lang->feedback->processedDate;
$config->feedback->search['fields']['closedBy']      = $lang->feedback->closedBy;
$config->feedback->search['fields']['closedDate']    = $lang->feedback->closedDate;
$config->feedback->search['fields']['closedReason']  = $lang->feedback->closedReason;

$config->feedback->search['params']['title']         = array('operator' => 'include', 'control' => 'input',  'values' => '');
$config->feedback->search['params']['id']            = array('operator' => '=', 'control' => 'input',  'values' => '');
$config->feedback->search['params']['module']        = array('operator' => '=', 'control' => 'select',  'values' => '');
$config->feedback->search['params']['product']       = array('operator' => '=', 'control' => 'select',  'values' => '');
$config->feedback->search['params']['status']        = array('operator' => '=', 'control' => 'select',  'values' => $lang->feedback->statusList);
$config->feedback->search['params']['assignedTo']    = array('operator' => '=', 'control' => 'select',  'values' => 'users');
$config->feedback->search['params']['mailto']        = array('operator' => 'include', 'control' => 'select',  'values' => 'users');
$config->feedback->search['params']['desc']          = array('operator' => 'include', 'control' => 'input',  'values' => '');
$config->feedback->search['params']['public']        = array('operator' => '=', 'control' => 'select',  'values' => $lang->feedback->publicList);
$config->feedback->search['params']['openedBy']      = array('operator' => '=', 'control' => 'select',  'values' => 'users');
$config->feedback->search['params']['openedDate']    = array('operator' => '=', 'control' => 'input',  'values' => '', 'class' => 'date');
$config->feedback->search['params']['processedBy']   = array('operator' => '=', 'control' => 'select',  'values' => 'users');
$config->feedback->search['params']['processedDate'] = array('operator' => '=', 'control' => 'input',  'values' => '', 'class' => 'date');
$config->feedback->search['params']['closedBy']      = array('operator' => '=', 'control' => 'select',  'values' => 'users');
$config->feedback->search['params']['closedDate']    = array('operator' => '=', 'control' => 'input',  'values' => '', 'class' => 'date');
$config->feedback->search['params']['closedReason']  = array('operator' => '=', 'control' => 'select',  'values' => $lang->feedback->closedReasonList);

$config->feedback->datatable = new stdclass();
$config->feedback->datatable->defaultField = array('id', 'product', 'title', 'status', 'openedBy', 'openedDate', 'assignedTo', 'actions');
$config->feedback->datatable->fieldList['id']['title']    = 'idAB';
$config->feedback->datatable->fieldList['id']['fixed']    = 'left';
$config->feedback->datatable->fieldList['id']['width']    = '50';
$config->feedback->datatable->fieldList['id']['required'] = 'yes';

$config->feedback->datatable->fieldList['product']['title']    = 'product';
$config->feedback->datatable->fieldList['product']['fixed']    = 'left';
$config->feedback->datatable->fieldList['product']['width']    = '120';
$config->feedback->datatable->fieldList['product']['required'] = 'yes';

$config->feedback->datatable->fieldList['title']['title']    = 'title';
$config->feedback->datatable->fieldList['title']['fixed']    = 'left';
$config->feedback->datatable->fieldList['title']['width']    = 'auto';
$config->feedback->datatable->fieldList['title']['required'] = 'yes';

$config->feedback->datatable->fieldList['status']['title']    = 'status';
$config->feedback->datatable->fieldList['status']['fixed']    = 'no';
$config->feedback->datatable->fieldList['status']['width']    = '80';
$config->feedback->datatable->fieldList['status']['required'] = 'no';

$config->feedback->datatable->fieldList['dept']['title']    = 'dept';
$config->feedback->datatable->fieldList['dept']['fixed']    = 'no';
$config->feedback->datatable->fieldList['dept']['width']    = '80';
$config->feedback->datatable->fieldList['dept']['required'] = 'no';

$config->feedback->datatable->fieldList['openedBy']['title']    = 'openedBy';
$config->feedback->datatable->fieldList['openedBy']['fixed']    = 'no';
$config->feedback->datatable->fieldList['openedBy']['width']    = '80';
$config->feedback->datatable->fieldList['openedBy']['required'] = 'no';

$config->feedback->datatable->fieldList['openedDate']['title']    = 'openedDate';
$config->feedback->datatable->fieldList['openedDate']['fixed']    = 'no';
$config->feedback->datatable->fieldList['openedDate']['width']    = '100';
$config->feedback->datatable->fieldList['openedDate']['required'] = 'no';

$config->feedback->datatable->fieldList['assignedTo']['title']    = 'assignedTo';
$config->feedback->datatable->fieldList['assignedTo']['fixed']    = 'no';
$config->feedback->datatable->fieldList['assignedTo']['width']    = '120';
$config->feedback->datatable->fieldList['assignedTo']['required'] = 'no';

$config->feedback->datatable->fieldList['processedBy']['title']    = 'processedBy';
$config->feedback->datatable->fieldList['processedBy']['fixed']    = 'no';
$config->feedback->datatable->fieldList['processedBy']['width']    = '80';
$config->feedback->datatable->fieldList['processedBy']['required'] = 'no';

$config->feedback->datatable->fieldList['processedDate']['title']    = 'processedDate';
$config->feedback->datatable->fieldList['processedDate']['fixed']    = 'no';
$config->feedback->datatable->fieldList['processedDate']['width']    = '100';
$config->feedback->datatable->fieldList['processedDate']['required'] = 'no';

$config->feedback->datatable->fieldList['closedReason']['title']    = 'closedReason';
$config->feedback->datatable->fieldList['closedReason']['fixed']    = 'no';
$config->feedback->datatable->fieldList['closedReason']['width']    = '120';
$config->feedback->datatable->fieldList['closedReason']['required'] = 'no';

$config->feedback->datatable->fieldList['actions']['title']    = 'actions';
$config->feedback->datatable->fieldList['actions']['fixed']    = 'right';
$config->feedback->datatable->fieldList['actions']['width']    = '220';
$config->feedback->datatable->fieldList['actions']['required'] = 'yes';
