<?php
$config->closingadvise          = new stdclass();
$config->closingadvise->create  = new stdclass();
$config->closingadvise->create->requiredFields       = 'projectType';

$config->closingadvise->liasisonOfficer = '';
$config->closingadvise->datatable = new stdclass();
$config->closingadvise->datatable->defaultField = array('id', 'title', 'project', 'planReviewMeetingTime', 'applicant', 'status', 'dealUser', 'actions');

$config->closingadvise->datatable->fieldList['id']['title'] = 'idAB';
$config->closingadvise->datatable->fieldList['id']['fixed'] = 'left';
$config->closingadvise->datatable->fieldList['id']['width'] = '60';
$config->closingadvise->datatable->fieldList['id']['required'] = 'yes';

$config->closingadvise->datatable->fieldList['title']['title'] = 'title';
$config->closingadvise->datatable->fieldList['title']['fixed'] = 'left';
$config->closingadvise->datatable->fieldList['title']['width'] = '200';
$config->closingadvise->datatable->fieldList['title']['required'] = 'yes';

$config->closingadvise->datatable->fieldList['project']['title'] = 'project';
$config->closingadvise->datatable->fieldList['project']['fixed'] = 'left';
$config->closingadvise->datatable->fieldList['project']['width'] = '200';
$config->closingadvise->datatable->fieldList['project']['required'] = 'yes';

$config->closingadvise->datatable->fieldList['planReviewMeetingTime']['title'] = 'planReviewMeetingTime';
$config->closingadvise->datatable->fieldList['planReviewMeetingTime']['fixed'] = 'left';
$config->closingadvise->datatable->fieldList['planReviewMeetingTime']['width'] = '200';
$config->closingadvise->datatable->fieldList['planReviewMeetingTime']['required'] = 'yes';

$config->closingadvise->datatable->fieldList['applicant']['title'] = 'applicant';
$config->closingadvise->datatable->fieldList['applicant']['fixed'] = 'right';
$config->closingadvise->datatable->fieldList['applicant']['width'] = '120';
$config->closingadvise->datatable->fieldList['applicant']['required'] = 'no';

$config->closingadvise->datatable->fieldList['status']['title'] = 'status';
$config->closingadvise->datatable->fieldList['status']['fixed'] = 'right';
$config->closingadvise->datatable->fieldList['status']['width'] = '150';
$config->closingadvise->datatable->fieldList['status']['required'] = 'no';

$config->closingadvise->datatable->fieldList['dealUser']['title'] = 'dealUser';
$config->closingadvise->datatable->fieldList['dealUser']['fixed'] = 'right';
$config->closingadvise->datatable->fieldList['dealUser']['width'] = '150';
$config->closingadvise->datatable->fieldList['dealUser']['required'] = 'yes';

$config->closingadvise->datatable->fieldList['actions']['title'] = 'actions';
$config->closingadvise->datatable->fieldList['actions']['fixed'] = 'right';
$config->closingadvise->datatable->fieldList['actions']['width'] = '150';
$config->closingadvise->datatable->fieldList['actions']['required'] = 'yes';


$config->closingadvise->editor = new stdclass();
$config->closingadvise->editor->review   = array('id' => 'comment', 'tools' => 'simpleTools');
$config->closingadvise->submitFileNameEmpty   = '建议提交组织样例库清单:第%s行『提交文档名称』应为必填';
$config->closingadvise->submitReasonEmpty     = '建议提交组织样例库清单:第%s行『提交理由』应为必填';
$config->closingadvise->versionCodeOSSP       = '建议提交组织样例库清单:第%s行『对应OSSP版本号』应为必填';
$config->closingadvise->comment               = '建议提交组织样例库清单:第%s行『备注』应为必填';