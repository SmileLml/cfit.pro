<?php
$config->auditreview = new stdClass();
$config->auditreview->datatable = new stdclass();
$config->auditreview->datatable->defaultField = array('id', 'title','mark','createdDept', 'status', 'dealUser', 'object', 'type', 'grade','reviewer', 'owner', 'expert', 'reviewedBy', 'deadline','createdBy','createdDate','editBy','editDate','actions');

$config->auditreview->datatable->fieldList['id']['title']    = 'idAB';
$config->auditreview->datatable->fieldList['id']['fixed']    = 'left';
$config->auditreview->datatable->fieldList['id']['width']    = '60';
$config->auditreview->datatable->fieldList['id']['required'] = 'yes';

$config->auditreview->datatable->fieldList['mark']['title']    = 'mark';
$config->auditreview->datatable->fieldList['mark']['fixed']    = 'left';
$config->auditreview->datatable->fieldList['mark']['width']    = '80';
$config->auditreview->datatable->fieldList['mark']['required'] = 'yes';

$config->auditreview->datatable->fieldList['createdDept']['title']    = 'createdDept';
$config->auditreview->datatable->fieldList['createdDept']['fixed']    = 'left';
$config->auditreview->datatable->fieldList['createdDept']['width']    = 'auto';
$config->auditreview->datatable->fieldList['createdDept']['required'] = 'yes';

$config->auditreview->datatable->fieldList['title']['title']    = 'title';
$config->auditreview->datatable->fieldList['title']['fixed']    = 'left';
$config->auditreview->datatable->fieldList['title']['width']    = 'auto';
$config->auditreview->datatable->fieldList['title']['required'] = 'yes';

$config->auditreview->datatable->fieldList['status']['title']    = 'status';
$config->auditreview->datatable->fieldList['status']['fixed']    = 'left';
$config->auditreview->datatable->fieldList['status']['width']    = '150';
$config->auditreview->datatable->fieldList['status']['required'] = 'yes';

$config->auditreview->datatable->fieldList['dealUser']['title']    = 'dealUser';
$config->auditreview->datatable->fieldList['dealUser']['fixed']    = 'left';
$config->auditreview->datatable->fieldList['dealUser']['width']    = '120';
$config->auditreview->datatable->fieldList['dealUser']['required'] = 'yes';

$config->auditreview->datatable->fieldList['object']['title']    = 'object';
$config->auditreview->datatable->fieldList['object']['fixed']    = 'no';
$config->auditreview->datatable->fieldList['object']['width']    = '120';
$config->auditreview->datatable->fieldList['object']['required'] = 'no';

$config->auditreview->datatable->fieldList['type']['title']    = 'type';
$config->auditreview->datatable->fieldList['type']['fixed']    = 'no';
$config->auditreview->datatable->fieldList['type']['width']    = '100';
$config->auditreview->datatable->fieldList['type']['required'] = 'no';

$config->auditreview->datatable->fieldList['grade']['title']    = 'grade';
$config->auditreview->datatable->fieldList['grade']['fixed']    = 'no';
$config->auditreview->datatable->fieldList['grade']['width']    = '100';
$config->auditreview->datatable->fieldList['grade']['required'] = 'no';

$config->auditreview->datatable->fieldList['reviewer']['title']    = 'reviewer';
$config->auditreview->datatable->fieldList['reviewer']['fixed']    = 'no';
$config->auditreview->datatable->fieldList['reviewer']['width']    = '100';
$config->auditreview->datatable->fieldList['reviewer']['required'] = 'no';

$config->auditreview->datatable->fieldList['owner']['title']    = 'owner';
$config->auditreview->datatable->fieldList['owner']['fixed']    = 'no';
$config->auditreview->datatable->fieldList['owner']['width']    = '120';
$config->auditreview->datatable->fieldList['owner']['required'] = 'no';

$config->auditreview->datatable->fieldList['expert']['title']    = 'expert';
$config->auditreview->datatable->fieldList['expert']['fixed']    = 'no';
$config->auditreview->datatable->fieldList['expert']['width']    = '120';
$config->auditreview->datatable->fieldList['expert']['required'] = 'no';

$config->auditreview->datatable->fieldList['reviewedBy']['title']    = 'reviewedBy';
$config->auditreview->datatable->fieldList['reviewedBy']['fixed']    = 'no';
$config->auditreview->datatable->fieldList['reviewedBy']['width']    = '150';
$config->auditreview->datatable->fieldList['reviewedBy']['required'] = 'no';

$config->auditreview->datatable->fieldList['deadline']['title']    = 'deadline';
$config->auditreview->datatable->fieldList['deadline']['fixed']    = 'no';
$config->auditreview->datatable->fieldList['deadline']['width']    = '120';
$config->auditreview->datatable->fieldList['deadline']['required'] = 'no';

$config->auditreview->datatable->fieldList['createdBy']['title']    = 'createdBy';
$config->auditreview->datatable->fieldList['createdBy']['fixed']    = 'no';
$config->auditreview->datatable->fieldList['createdBy']['width']    = '120';
$config->auditreview->datatable->fieldList['createdBy']['required'] = 'no';

$config->auditreview->datatable->fieldList['createdDate']['title']    = 'createdDate';
$config->auditreview->datatable->fieldList['createdDate']['fixed']    = 'no';
$config->auditreview->datatable->fieldList['createdDate']['width']    = '120';
$config->auditreview->datatable->fieldList['createdDate']['required'] = 'no';

$config->auditreview->datatable->fieldList['createdDept']['title']    = 'createdDept';
$config->auditreview->datatable->fieldList['createdDept']['fixed']    = 'no';
$config->auditreview->datatable->fieldList['createdDept']['width']    = '120';
$config->auditreview->datatable->fieldList['createdDept']['required'] = 'no';

$config->auditreview->datatable->fieldList['editBy']['title']    = 'editBy';
$config->auditreview->datatable->fieldList['editBy']['fixed']    = 'no';
$config->auditreview->datatable->fieldList['editBy']['width']    = '120';
$config->auditreview->datatable->fieldList['editBy']['required'] = 'no';

$config->auditreview->datatable->fieldList['editDate']['title']    = 'editDate';
$config->auditreview->datatable->fieldList['editDate']['fixed']    = 'no';
$config->auditreview->datatable->fieldList['editDate']['width']    = '120';
$config->auditreview->datatable->fieldList['editDate']['required'] = 'no';


$config->auditreview->datatable->fieldList['actions']['title']    = 'actions';
$config->auditreview->datatable->fieldList['actions']['fixed']    = 'right';
$config->auditreview->datatable->fieldList['actions']['width']    = '200';
$config->auditreview->datatable->fieldList['actions']['required'] = 'yes';
