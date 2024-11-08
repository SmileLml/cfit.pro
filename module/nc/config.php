<?php
$config->nc = new stdclass();
$config->nc->datatable = new stdclass();
$config->nc->editor    = new stdclass();
$config->nc->editor->edit    = array('id' => 'desc', 'tools' => 'simpleTools');
$config->nc->editor->resolve = array('id' => 'desc', 'tools' => 'simpleTools');
$config->nc->editor->close   = array('id' => 'comment', 'tools' => 'simpleTools');

$config->nc->datatable->defaultField = array('id', 'severity', 'title', 'auditplan', 'type', 'status', 'deadline', 'createdBy', 'createdDate', 'actions');
$config->nc->datatable->fieldList['id']['title']    = 'id';
$config->nc->datatable->fieldList['id']['fixed']    = 'left';
$config->nc->datatable->fieldList['id']['width']    = '70';
$config->nc->datatable->fieldList['id']['required'] = 'yes';

$config->nc->datatable->fieldList['severity']['title']    = 'severity';
$config->nc->datatable->fieldList['severity']['fixed']    = 'left';
$config->nc->datatable->fieldList['severity']['width']    = '80';
$config->nc->datatable->fieldList['severity']['required'] = 'no';

$config->nc->datatable->fieldList['title']['title']    = 'title';
$config->nc->datatable->fieldList['title']['fixed']    = 'left';
$config->nc->datatable->fieldList['title']['width']    = 'auto';
$config->nc->datatable->fieldList['title']['required'] = 'yes';

$config->nc->datatable->fieldList['auditplan']['title']    = 'object';
$config->nc->datatable->fieldList['auditplan']['fixed']    = 'no';
$config->nc->datatable->fieldList['auditplan']['width']    = '150';
$config->nc->datatable->fieldList['auditplan']['required'] = 'no';

$config->nc->datatable->fieldList['type']['title']    = 'type';
$config->nc->datatable->fieldList['type']['fixed']    = 'no';
$config->nc->datatable->fieldList['type']['width']    = '100';
$config->nc->datatable->fieldList['type']['required'] = 'no';

$config->nc->datatable->fieldList['status']['title']    = 'status';
$config->nc->datatable->fieldList['status']['fixed']    = 'no';
$config->nc->datatable->fieldList['status']['width']    = '100';
$config->nc->datatable->fieldList['status']['required'] = 'no';

$config->nc->datatable->fieldList['assignedTo']['title']    = 'assignedTo';
$config->nc->datatable->fieldList['assignedTo']['fixed']    = 'no';
$config->nc->datatable->fieldList['assignedTo']['width']    = '120';
$config->nc->datatable->fieldList['assignedTo']['required'] = 'no';

$config->nc->datatable->fieldList['deadline']['title']    = 'deadline';
$config->nc->datatable->fieldList['deadline']['fixed']    = 'no';
$config->nc->datatable->fieldList['deadline']['width']    = '120';
$config->nc->datatable->fieldList['deadline']['required'] = 'no';

$config->nc->datatable->fieldList['createdBy']['title']    = 'createdBy';
$config->nc->datatable->fieldList['createdBy']['fixed']    = 'no';
$config->nc->datatable->fieldList['createdBy']['width']    = '120';
$config->nc->datatable->fieldList['createdBy']['required'] = 'no';

$config->nc->datatable->fieldList['createdDate']['title']    = 'createdDate';
$config->nc->datatable->fieldList['createdDate']['fixed']    = 'no';
$config->nc->datatable->fieldList['createdDate']['width']    = '120';
$config->nc->datatable->fieldList['createdDate']['required'] = 'no';

$config->nc->datatable->fieldList['resolution']['title']    = 'resolution';
$config->nc->datatable->fieldList['resolution']['fixed']    = 'no';
$config->nc->datatable->fieldList['resolution']['width']    = '100';
$config->nc->datatable->fieldList['resolution']['required'] = 'no';

$config->nc->datatable->fieldList['resolvedBy']['title']    = 'resolvedBy';
$config->nc->datatable->fieldList['resolvedBy']['fixed']    = 'no';
$config->nc->datatable->fieldList['resolvedBy']['width']    = '120';
$config->nc->datatable->fieldList['resolvedBy']['required'] = 'no';

$config->nc->datatable->fieldList['resolvedDate']['title']    = 'resolvedDate';
$config->nc->datatable->fieldList['resolvedDate']['fixed']    = 'no';
$config->nc->datatable->fieldList['resolvedDate']['width']    = '120';
$config->nc->datatable->fieldList['resolvedDate']['required'] = 'no';

$config->nc->datatable->fieldList['closedBy']['title']    = 'closedBy';
$config->nc->datatable->fieldList['closedBy']['fixed']    = 'no';
$config->nc->datatable->fieldList['closedBy']['width']    = '120';
$config->nc->datatable->fieldList['closedBy']['required'] = 'no';

$config->nc->datatable->fieldList['closedDate']['title']    = 'closedDate';
$config->nc->datatable->fieldList['closedDate']['fixed']    = 'no';
$config->nc->datatable->fieldList['closedDate']['width']    = '120';
$config->nc->datatable->fieldList['closedDate']['required'] = 'no';

$config->nc->datatable->fieldList['actions']['title']    = 'actions';
$config->nc->datatable->fieldList['actions']['fixed']    = 'right';
$config->nc->datatable->fieldList['actions']['width']    = '150';
$config->nc->datatable->fieldList['actions']['required'] = 'yes';
