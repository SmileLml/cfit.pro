<?php
/**
 * Created by PhpStorm.
 * User: t_wangjiurong
 * Date: 2023/2/20
 * Time: 9:43
 */
$config->reviewqz          = new stdclass();
$config->reviewqz->create  = new stdclass();
$config->reviewqz->create->requiredFields       = 'qzReviewId,applicant,applicationTime,applicationDept,isProject,title,type,reviewCenter,content,planReviewMeetingTime,owner,planJinkeExports,planFeedbackTime';

$config->reviewqz->liasisonOfficer = '';
$config->reviewqz->datatable = new stdclass();
$config->reviewqz->datatable->defaultField = array('id', 'title', 'project', 'timeInterval', 'planReviewMeetingTime', 'applicant', 'status', 'dealUser', 'actions');

$config->reviewqz->datatable->fieldList['id']['title'] = 'idAB';
$config->reviewqz->datatable->fieldList['id']['fixed'] = 'left';
$config->reviewqz->datatable->fieldList['id']['width'] = '60';
$config->reviewqz->datatable->fieldList['id']['required'] = 'yes';

$config->reviewqz->datatable->fieldList['title']['title'] = 'title';
$config->reviewqz->datatable->fieldList['title']['fixed'] = 'left';
$config->reviewqz->datatable->fieldList['title']['width'] = '200';
$config->reviewqz->datatable->fieldList['title']['required'] = 'yes';

$config->reviewqz->datatable->fieldList['project']['title'] = 'project';
$config->reviewqz->datatable->fieldList['project']['fixed'] = 'left';
$config->reviewqz->datatable->fieldList['project']['width'] = '200';
$config->reviewqz->datatable->fieldList['project']['required'] = 'yes';

$config->reviewqz->datatable->fieldList['timeInterval']['title'] = 'timeInterval';
$config->reviewqz->datatable->fieldList['timeInterval']['fixed'] = 'left';
$config->reviewqz->datatable->fieldList['timeInterval']['width'] = '100';
$config->reviewqz->datatable->fieldList['timeInterval']['required'] = 'yes';

$config->reviewqz->datatable->fieldList['planReviewMeetingTime']['title'] = 'planReviewMeetingTime';
$config->reviewqz->datatable->fieldList['planReviewMeetingTime']['fixed'] = 'left';
$config->reviewqz->datatable->fieldList['planReviewMeetingTime']['width'] = '200';
$config->reviewqz->datatable->fieldList['planReviewMeetingTime']['required'] = 'yes';

$config->reviewqz->datatable->fieldList['applicant']['title'] = 'applicant';
$config->reviewqz->datatable->fieldList['applicant']['fixed'] = 'right';
$config->reviewqz->datatable->fieldList['applicant']['width'] = '120';
$config->reviewqz->datatable->fieldList['applicant']['required'] = 'no';

$config->reviewqz->datatable->fieldList['status']['title'] = 'status';
$config->reviewqz->datatable->fieldList['status']['fixed'] = 'right';
$config->reviewqz->datatable->fieldList['status']['width'] = '150';
$config->reviewqz->datatable->fieldList['status']['required'] = 'no';

$config->reviewqz->datatable->fieldList['dealUser']['title'] = 'dealUser';
$config->reviewqz->datatable->fieldList['dealUser']['fixed'] = 'right';
$config->reviewqz->datatable->fieldList['dealUser']['width'] = '150';
$config->reviewqz->datatable->fieldList['dealUser']['required'] = 'yes';

$config->reviewqz->datatable->fieldList['actions']['title'] = 'actions';
$config->reviewqz->datatable->fieldList['actions']['fixed'] = 'right';
$config->reviewqz->datatable->fieldList['actions']['width'] = '150';
$config->reviewqz->datatable->fieldList['actions']['required'] = 'yes';

global $lang;
$config->reviewqz->search['module'] = 'reviewqz';
$config->reviewqz->search['fields']['id'] = $lang->idAB;
$config->reviewqz->search['fields']['title'] = $lang->reviewqz->title;
$config->reviewqz->search['fields']['planReviewMeetingTime'] = $lang->reviewqz->planReviewMeetingTime;
$config->reviewqz->search['fields']['applicant'] = $lang->reviewqz->applicant;
$config->reviewqz->search['fields']['status'] = $lang->reviewqz->status;
$config->reviewqz->search['fields']['dealUser'] = $lang->reviewqz->dealUser;


$config->reviewqz->search['params']['id'] = array('operator' => '=', 'control' => 'input', 'values' => '');
$config->reviewqz->search['params']['title'] = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->reviewqz->search['params']['planReviewMeetingTime'] = array('operator' => '>=', 'control' => 'input', 'class' => 'date', 'values' => '');
$config->reviewqz->search['params']['applicant'] = array('operator' => '=', 'control' => 'input', 'values' => '');
$config->reviewqz->search['params']['status'] = array('operator' => '=', 'control' => 'select', 'values' => $lang->reviewqz->browseStatus);
$config->reviewqz->search['params']['dealUser'] = array('operator' => 'include', 'control' => 'select', 'values' => 'users');


$config->reviewqz->editor = new stdclass();
//$config->reviewqz->editor->feedback = array('id' => 'comment', 'tools' => 'simple');
$config->reviewqz->editor->change   = array('id' => 'comment', 'tools' => 'simple');