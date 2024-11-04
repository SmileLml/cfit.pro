<?php
$config->closingitem          = new stdclass();
$config->closingitem->create  = new stdclass();
$config->closingitem->create->requiredFields       = 'projectType';

$config->closingitem->liasisonOfficer = '';
$config->closingitem->datatable = new stdclass();
$config->closingitem->datatable->defaultField = array('id', 'title', 'project', 'planReviewMeetingTime', 'applicant', 'status', 'dealUser', 'actions');

$config->closingitem->datatable->fieldList['id']['title'] = 'idAB';
$config->closingitem->datatable->fieldList['id']['fixed'] = 'left';
$config->closingitem->datatable->fieldList['id']['width'] = '60';
$config->closingitem->datatable->fieldList['id']['required'] = 'yes';

$config->closingitem->datatable->fieldList['title']['title'] = 'title';
$config->closingitem->datatable->fieldList['title']['fixed'] = 'left';
$config->closingitem->datatable->fieldList['title']['width'] = '200';
$config->closingitem->datatable->fieldList['title']['required'] = 'yes';

$config->closingitem->datatable->fieldList['project']['title'] = 'project';
$config->closingitem->datatable->fieldList['project']['fixed'] = 'left';
$config->closingitem->datatable->fieldList['project']['width'] = '200';
$config->closingitem->datatable->fieldList['project']['required'] = 'yes';

$config->closingitem->datatable->fieldList['planReviewMeetingTime']['title'] = 'planReviewMeetingTime';
$config->closingitem->datatable->fieldList['planReviewMeetingTime']['fixed'] = 'left';
$config->closingitem->datatable->fieldList['planReviewMeetingTime']['width'] = '200';
$config->closingitem->datatable->fieldList['planReviewMeetingTime']['required'] = 'yes';

$config->closingitem->datatable->fieldList['applicant']['title'] = 'applicant';
$config->closingitem->datatable->fieldList['applicant']['fixed'] = 'right';
$config->closingitem->datatable->fieldList['applicant']['width'] = '120';
$config->closingitem->datatable->fieldList['applicant']['required'] = 'no';

$config->closingitem->datatable->fieldList['status']['title'] = 'status';
$config->closingitem->datatable->fieldList['status']['fixed'] = 'right';
$config->closingitem->datatable->fieldList['status']['width'] = '150';
$config->closingitem->datatable->fieldList['status']['required'] = 'no';

$config->closingitem->datatable->fieldList['dealUser']['title'] = 'dealUser';
$config->closingitem->datatable->fieldList['dealUser']['fixed'] = 'right';
$config->closingitem->datatable->fieldList['dealUser']['width'] = '150';
$config->closingitem->datatable->fieldList['dealUser']['required'] = 'yes';

$config->closingitem->datatable->fieldList['actions']['title'] = 'actions';
$config->closingitem->datatable->fieldList['actions']['fixed'] = 'right';
$config->closingitem->datatable->fieldList['actions']['width'] = '150';
$config->closingitem->datatable->fieldList['actions']['required'] = 'yes';


$config->closingitem->editor = new stdclass();
$config->closingitem->editor->review   = array('id' => 'suggest', 'tools' => 'simpleTools');

$config->closingitem->assemblyIndexEmpty = '公共组件个数:第%s行『已纳入组件名称』应为必填';
$config->closingitem->assemblyDescEmpty  = '公共组件个数:第%s行『公共组件描述』应为必填';
$config->closingitem->assemblyLevelEmpty = '公共组件个数:第%s行『组件级别』应为必填';
$config->closingitem->statusEmpty        = '公共组件个数:第%s行『当前状态』应为必填';
$config->closingitem->advise2Empty       = '公共组件改进意见:第%s行『改进意见描述』应为必填';
$config->closingitem->advise4Empty       = '测试工具改进意见:第%s行『改进意见描述』应为必填';
$config->closingitem->advise5Empty       = '对OSSP过程改进意见:第%s行『改进意见描述』应为必填';
$config->closingitem->advise6Empty       = '对研发过程平台改进意见:第%s行『改进意见描述』应为必填';
$config->closingitem->toolsNameEmpty     = '测试工具使用情况:第%s行『测试工具名称』应为必填';
$config->closingitem->toolsVersionEmpty  = '测试工具使用情况:第%s行『版本』应为必填';
$config->closingitem->toolsTypeEmpty     = '测试工具使用情况:第%s行『工具类型』应为必填';
$config->closingitem->toolsDescEmpty     = '测试工具使用情况:第%s行『使用情况说明』应为必填';


$config->closingitem->submitFileNameEmpty   = '建议提交组织样例库清单:第%s行『提交文档名称』应为必填';
$config->closingitem->submitReasonEmpty     = '建议提交组织样例库清单:第%s行『提交理由』应为必填';
$config->closingitem->versionCodeOSSP       = '建议提交组织样例库清单:第%s行『对应OSSP版本号』应为必填';
$config->closingitem->comment               = '建议提交组织样例库清单:第%s行『备注』应为必填';