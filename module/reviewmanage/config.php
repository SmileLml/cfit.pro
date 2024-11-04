<?php
$config->reviewmanage->edit = new stdclass();
$config->reviewmanage->edit->requiredFields = 'title,object,type,qa,reviewer,owner,files,deadline,relatedUsers,qualityCm';
$config->reviewmanage->submit = new stdclass();
$config->reviewmanage->submit->requiredFields = 'qa,preReviewDeadline';

$config->reviewmanage->assignFormalAssignReviewer = new stdclass();
$config->reviewmanage->assignFormalAssignReviewer->requiredFields = 'type,  reviewer, owner, deadline';

$config->reviewmanage->close = new stdclass();
$config->reviewmanage->close->requiredFields  = 'status';

$config->reviewmanage->editor = new stdclass();
$config->reviewmanage->editor->edit    = array('id' => 'comment', 'tools' => 'simpleTools');
$config->reviewmanage->editor->view    = array('id' => 'comment,lastComment', 'tools' => 'simpleTools');
$config->reviewmanage->editor->submit  = array('id' => 'comment', 'tools' => 'simpleTools');

$config->reviewmanage->editor->assign  = array('id' => 'comment', 'tools' => 'simpleTools');

$config->reviewmanage->editor->review  = array('id' => 'comment,meetingSummary,meetingContent', 'tools' => 'simpleTools');

$config->reviewmanage->editor->close  = array('id' => 'comment', 'tools' => 'simpleTools');
$config->reviewmanage->editor->delete   = array( 'id' => 'comment', 'tools' => 'simpleTools');
$config->reviewmanage->editor->editissue = array('id' => 'desc', 'tools' => 'simpleTools');

//挂起
$config->reviewmanage->editor->suspend  = array('id' => 'comment', 'tools' => 'simpleTools');
//恢复
$config->reviewmanage->editor->renew  = array('id' => 'comment', 'tools' => 'simpleTools');


//编辑附件
$config->reviewmanage->editor->editfiles  = array('id' => 'currentComment', 'tools' => 'simpleTools');
//关闭
$config->reviewmanage->editfiles = new stdclass();
$config->reviewmanage->editfiles->requiredFields  = 'consumed,currentComment';

$config->reviewmanage->datatable = new stdclass();
$config->reviewmanage->datatable->defaultField = array('id', 'title', 'status', 'dealUser', 'deadDate','object', 'type','owner', 'grade', 'meetingPlanTime', 'meetingCode','meetingRealTime', 'createdDept','createdBy','reviewer',  'expert', 'reviewedBy', 'outside', 'meetingPlanExport','relatedUsers', 'deadline', 'projectType','isImportant', 'createdDate', 'editBy', 'editDate', 'actions');

$config->reviewmanage->datatable->fieldList['id']['title'] = 'idAB';
$config->reviewmanage->datatable->fieldList['id']['fixed'] = 'left';
$config->reviewmanage->datatable->fieldList['id']['width'] = '60';
$config->reviewmanage->datatable->fieldList['id']['required'] = 'yes';

$config->reviewmanage->datatable->fieldList['title']['title'] = 'title';
$config->reviewmanage->datatable->fieldList['title']['fixed'] = 'left';
$config->reviewmanage->datatable->fieldList['title']['width'] = '160';
$config->reviewmanage->datatable->fieldList['title']['required'] = 'yes';

$config->reviewmanage->datatable->fieldList['status']['title'] = 'status';
$config->reviewmanage->datatable->fieldList['status']['fixed'] = 'left';
$config->reviewmanage->datatable->fieldList['status']['width'] = '160';
$config->reviewmanage->datatable->fieldList['status']['required'] = 'yes';

$config->reviewmanage->datatable->fieldList['dealUser']['title'] = 'dealUser';
$config->reviewmanage->datatable->fieldList['dealUser']['fixed'] = 'left';
$config->reviewmanage->datatable->fieldList['dealUser']['width'] = '120';
$config->reviewmanage->datatable->fieldList['dealUser']['required'] = 'yes';

$config->reviewmanage->datatable->fieldList['deadDate']['title'] = 'deadDate';
$config->reviewmanage->datatable->fieldList['deadDate']['fixed'] = 'left';
$config->reviewmanage->datatable->fieldList['deadDate']['width'] = '120';
$config->reviewmanage->datatable->fieldList['deadDate']['required'] = 'yes';

$config->reviewmanage->datatable->fieldList['object']['title'] = 'object';
$config->reviewmanage->datatable->fieldList['object']['fixed'] = 'no';
$config->reviewmanage->datatable->fieldList['object']['width'] = '120';
$config->reviewmanage->datatable->fieldList['object']['required'] = 'no';

$config->reviewmanage->datatable->fieldList['type']['title'] = 'type';
$config->reviewmanage->datatable->fieldList['type']['fixed'] = 'no';
$config->reviewmanage->datatable->fieldList['type']['width'] = '100';
$config->reviewmanage->datatable->fieldList['type']['required'] = 'no';

$config->reviewmanage->datatable->fieldList['owner']['title'] = 'owner';
$config->reviewmanage->datatable->fieldList['owner']['fixed'] = 'no';
$config->reviewmanage->datatable->fieldList['owner']['width'] = '120';
$config->reviewmanage->datatable->fieldList['owner']['required'] = 'no';

$config->reviewmanage->datatable->fieldList['grade']['title'] = 'grade';
$config->reviewmanage->datatable->fieldList['grade']['fixed'] = 'no';
$config->reviewmanage->datatable->fieldList['grade']['width'] = '100';
$config->reviewmanage->datatable->fieldList['grade']['required'] = 'no';

$config->reviewmanage->datatable->fieldList['meetingPlanTime']['title'] = 'meetingPlanTime';
$config->reviewmanage->datatable->fieldList['meetingPlanTime']['fixed'] = 'no';
$config->reviewmanage->datatable->fieldList['meetingPlanTime']['width'] = '180';
$config->reviewmanage->datatable->fieldList['meetingPlanTime']['required'] = 'no';

$config->reviewmanage->datatable->fieldList['meetingCode']['title'] = 'meetingCode';
$config->reviewmanage->datatable->fieldList['meetingCode']['fixed'] = 'no';
$config->reviewmanage->datatable->fieldList['meetingCode']['width'] = '180';
$config->reviewmanage->datatable->fieldList['meetingCode']['required'] = 'no';


$config->reviewmanage->datatable->fieldList['meetingRealTime']['title'] = 'meetingRealTime';
$config->reviewmanage->datatable->fieldList['meetingRealTime']['fixed'] = 'no';
$config->reviewmanage->datatable->fieldList['meetingRealTime']['width'] = '180';
$config->reviewmanage->datatable->fieldList['meetingRealTime']['required'] = 'no';


$config->reviewmanage->datatable->fieldList['reviewer']['title'] = 'reviewer';
$config->reviewmanage->datatable->fieldList['reviewer']['fixed'] = 'no';
$config->reviewmanage->datatable->fieldList['reviewer']['width'] = '100';
$config->reviewmanage->datatable->fieldList['reviewer']['required'] = 'no';


$config->reviewmanage->datatable->fieldList['expert']['title'] = 'expert';
$config->reviewmanage->datatable->fieldList['expert']['fixed'] = 'no';
$config->reviewmanage->datatable->fieldList['expert']['width'] = '120';
$config->reviewmanage->datatable->fieldList['expert']['required'] = 'no';

$config->reviewmanage->datatable->fieldList['reviewedBy']['title'] = 'reviewedBy';
$config->reviewmanage->datatable->fieldList['reviewedBy']['fixed'] = 'no';
$config->reviewmanage->datatable->fieldList['reviewedBy']['width'] = '150';
$config->reviewmanage->datatable->fieldList['reviewedBy']['required'] = 'no';

$config->reviewmanage->datatable->fieldList['outside']['title'] = 'outside';
$config->reviewmanage->datatable->fieldList['outside']['fixed'] = 'no';
$config->reviewmanage->datatable->fieldList['outside']['width'] = '150';
$config->reviewmanage->datatable->fieldList['outside']['required'] = 'no';

$config->reviewmanage->datatable->fieldList['meetingPlanExport']['title']    = 'meetingPlanExport';
$config->reviewmanage->datatable->fieldList['meetingPlanExport']['fixed']    = 'no';
$config->reviewmanage->datatable->fieldList['meetingPlanExport']['width']    = '150';
$config->reviewmanage->datatable->fieldList['meetingPlanExport']['required'] = 'no';

$config->reviewmanage->datatable->fieldList['relatedUsers']['title'] = 'relatedUsers';
$config->reviewmanage->datatable->fieldList['relatedUsers']['fixed'] = 'no';
$config->reviewmanage->datatable->fieldList['relatedUsers']['width'] = '150';
$config->reviewmanage->datatable->fieldList['relatedUsers']['required'] = 'no';

$config->reviewmanage->datatable->fieldList['createdBy']['title'] = 'createdBy';
$config->reviewmanage->datatable->fieldList['createdBy']['fixed'] = 'no';
$config->reviewmanage->datatable->fieldList['createdBy']['width'] = '120';
$config->reviewmanage->datatable->fieldList['createdBy']['required'] = 'no';

$config->reviewmanage->datatable->fieldList['createdDate']['title'] = 'createdDate';
$config->reviewmanage->datatable->fieldList['createdDate']['fixed'] = 'no';
$config->reviewmanage->datatable->fieldList['createdDate']['width'] = '120';
$config->reviewmanage->datatable->fieldList['createdDate']['required'] = 'no';

$config->reviewmanage->datatable->fieldList['createdDept']['title'] = 'createdDept';
$config->reviewmanage->datatable->fieldList['createdDept']['fixed'] = 'no';
$config->reviewmanage->datatable->fieldList['createdDept']['width'] = '120';
$config->reviewmanage->datatable->fieldList['createdDept']['required'] = 'no';

$config->reviewmanage->datatable->fieldList['editBy']['title'] = 'editBy';
$config->reviewmanage->datatable->fieldList['editBy']['fixed'] = 'no';
$config->reviewmanage->datatable->fieldList['editBy']['width'] = '120';
$config->reviewmanage->datatable->fieldList['editBy']['required'] = 'no';

$config->reviewmanage->datatable->fieldList['editDate']['title'] = 'editDate';
$config->reviewmanage->datatable->fieldList['editDate']['fixed'] = 'no';
$config->reviewmanage->datatable->fieldList['editDate']['width'] = '120';
$config->reviewmanage->datatable->fieldList['editDate']['required'] = 'no';

$config->reviewmanage->datatable->fieldList['closePerson']['title'] = 'closePerson';
$config->reviewmanage->datatable->fieldList['closePerson']['fixed'] = 'no';
$config->reviewmanage->datatable->fieldList['closePerson']['width'] = '120';
$config->reviewmanage->datatable->fieldList['closePerson']['required'] = 'no';

$config->reviewmanage->datatable->fieldList['closeTime']['title'] = 'closeTime';
$config->reviewmanage->datatable->fieldList['closeTime']['fixed'] = 'no';
$config->reviewmanage->datatable->fieldList['closeTime']['width'] = '120';
$config->reviewmanage->datatable->fieldList['closeTime']['required'] = 'no';

$config->reviewmanage->datatable->fieldList['qa']['title'] = 'qa';
$config->reviewmanage->datatable->fieldList['qa']['fixed'] = 'no';
$config->reviewmanage->datatable->fieldList['qa']['width'] = '120';
$config->reviewmanage->datatable->fieldList['qa']['required'] = 'no';

$config->reviewmanage->datatable->fieldList['trialDept']['title'] = 'trialDept';
$config->reviewmanage->datatable->fieldList['trialDept']['fixed'] = 'no';
$config->reviewmanage->datatable->fieldList['trialDept']['width'] = '120';
$config->reviewmanage->datatable->fieldList['trialDept']['required'] = 'no';

$config->reviewmanage->datatable->fieldList['trialDeptLiasisonOfficer']['title'] = 'trialDeptLiasisonOfficer';
$config->reviewmanage->datatable->fieldList['trialDeptLiasisonOfficer']['fixed'] = 'no';
$config->reviewmanage->datatable->fieldList['trialDeptLiasisonOfficer']['width'] = '120';
$config->reviewmanage->datatable->fieldList['trialDeptLiasisonOfficer']['required'] = 'no';

$config->reviewmanage->datatable->fieldList['trialAdjudicatingOfficer']['title'] = 'trialAdjudicatingOfficer';
$config->reviewmanage->datatable->fieldList['trialAdjudicatingOfficer']['fixed'] = 'no';
$config->reviewmanage->datatable->fieldList['trialAdjudicatingOfficer']['width'] = '120';
$config->reviewmanage->datatable->fieldList['trialAdjudicatingOfficer']['required'] = 'no';

$config->reviewmanage->datatable->fieldList['trialJoinOfficer']['title'] = 'trialJoinOfficer';
$config->reviewmanage->datatable->fieldList['trialJoinOfficer']['fixed'] = 'no';
$config->reviewmanage->datatable->fieldList['trialJoinOfficer']['width'] = '120';
$config->reviewmanage->datatable->fieldList['trialJoinOfficer']['required'] = 'no';

$config->reviewmanage->datatable->fieldList['preReviewDeadline']['title'] = 'preReviewDeadline';
$config->reviewmanage->datatable->fieldList['preReviewDeadline']['fixed'] = 'no';
$config->reviewmanage->datatable->fieldList['preReviewDeadline']['width'] = '120';
$config->reviewmanage->datatable->fieldList['preReviewDeadline']['required'] = 'no';

$config->reviewmanage->datatable->fieldList['firstReviewDeadline']['title'] = 'firstReviewDeadline';
$config->reviewmanage->datatable->fieldList['firstReviewDeadline']['fixed'] = 'no';
$config->reviewmanage->datatable->fieldList['firstReviewDeadline']['width'] = '120';
$config->reviewmanage->datatable->fieldList['firstReviewDeadline']['required'] = 'no';

$config->reviewmanage->datatable->fieldList['deadline']['title'] = 'deadline';
$config->reviewmanage->datatable->fieldList['deadline']['fixed'] = 'no';
$config->reviewmanage->datatable->fieldList['deadline']['width'] = '120';
$config->reviewmanage->datatable->fieldList['deadline']['required'] = 'no';

$config->reviewmanage->datatable->fieldList['projectType']['title']    = 'projectType';
$config->reviewmanage->datatable->fieldList['projectType']['fixed']    = 'no';
$config->reviewmanage->datatable->fieldList['projectType']['width']    = '120';
$config->reviewmanage->datatable->fieldList['projectType']['required'] = 'no';

$config->reviewmanage->datatable->fieldList['isImportant']['title']    = 'isImportant';
$config->reviewmanage->datatable->fieldList['isImportant']['fixed']    = 'no';
$config->reviewmanage->datatable->fieldList['isImportant']['width']    = '100';
$config->reviewmanage->datatable->fieldList['isImportant']['required'] = 'no';

$config->reviewmanage->datatable->fieldList['closeDate']['title'] = 'closeDate';
$config->reviewmanage->datatable->fieldList['closeDate']['fixed'] = 'no';
$config->reviewmanage->datatable->fieldList['closeDate']['width'] = '120';
$config->reviewmanage->datatable->fieldList['closeDate']['required'] = 'no';

$config->reviewmanage->datatable->fieldList['qualityCm']['title'] = 'qualityCm';
$config->reviewmanage->datatable->fieldList['qualityCm']['fixed'] = 'no';
$config->reviewmanage->datatable->fieldList['qualityCm']['width'] = '120';
$config->reviewmanage->datatable->fieldList['qualityCm']['required'] = 'no';

$config->reviewmanage->datatable->fieldList['suspendBy']['title']    = 'suspendBy';
$config->reviewmanage->datatable->fieldList['suspendBy']['fixed']    = 'no';
$config->reviewmanage->datatable->fieldList['suspendBy']['width']    = '90';
$config->reviewmanage->datatable->fieldList['suspendBy']['required'] = 'no';

$config->reviewmanage->datatable->fieldList['suspendTime']['title']    = 'suspendTime';
$config->reviewmanage->datatable->fieldList['suspendTime']['fixed']    = 'no';
$config->reviewmanage->datatable->fieldList['suspendTime']['width']    = '180';
$config->reviewmanage->datatable->fieldList['suspendTime']['required'] = 'no';

$config->reviewmanage->datatable->fieldList['suspendReason']['title']    = 'suspendReason';
$config->reviewmanage->datatable->fieldList['suspendReason']['fixed']    = 'no';
$config->reviewmanage->datatable->fieldList['suspendReason']['width']    = '120';
$config->reviewmanage->datatable->fieldList['suspendReason']['required'] = 'no';

$config->reviewmanage->datatable->fieldList['renewBy']['title']    = 'renewBy';
$config->reviewmanage->datatable->fieldList['renewBy']['fixed']    = 'no';
$config->reviewmanage->datatable->fieldList['renewBy']['width']    = '90';
$config->reviewmanage->datatable->fieldList['renewBy']['required'] = 'no';

$config->reviewmanage->datatable->fieldList['renewTime']['title']    = 'renewTime';
$config->reviewmanage->datatable->fieldList['renewTime']['fixed']    = 'no';
$config->reviewmanage->datatable->fieldList['renewTime']['width']    = '180';
$config->reviewmanage->datatable->fieldList['renewTime']['required'] = 'no';

$config->reviewmanage->datatable->fieldList['renewReason']['title']    = 'renewReason';
$config->reviewmanage->datatable->fieldList['renewReason']['fixed']    = 'no';
$config->reviewmanage->datatable->fieldList['renewReason']['width']    = '120';
$config->reviewmanage->datatable->fieldList['renewReason']['required'] = 'no';

$config->reviewmanage->datatable->fieldList['actions']['title'] = 'actions';
$config->reviewmanage->datatable->fieldList['actions']['fixed'] = 'right';
$config->reviewmanage->datatable->fieldList['actions']['width'] = 'auto';
$config->reviewmanage->datatable->fieldList['actions']['required'] = 'yes';

global $lang;
$config->reviewmanage->search['module'] = 'reviewmanage';
$config->reviewmanage->search['fields']['id'] = $lang->idAB;
$config->reviewmanage->search['fields']['title'] = $lang->reviewmanage->title;
$config->reviewmanage->search['fields']['status'] = $lang->reviewmanage->status;
$config->reviewmanage->search['fields']['dealUser'] = $lang->reviewmanage->dealUser;
$config->reviewmanage->search['fields']['endDate'] = $lang->reviewmanage->deadDate;
$config->reviewmanage->search['fields']['object'] = $lang->reviewmanage->object;
$config->reviewmanage->search['fields']['type'] = $lang->reviewmanage->type;
$config->reviewmanage->search['fields']['owner'] = $lang->reviewmanage->owner;
$config->reviewmanage->search['fields']['grade'] = $lang->reviewmanage->grade;
$config->reviewmanage->search['fields']['meetingPlanTime'] = $lang->reviewmanage->meetingPlanTime;
$config->reviewmanage->search['fields']['meetingCode'] = $lang->reviewmanage->meetingCode;
$config->reviewmanage->search['fields']['meetingRealTime'] = $lang->reviewmanage->meetingRealTime;
$config->reviewmanage->search['fields']['reviewer'] = $lang->reviewmanage->reviewer;
$config->reviewmanage->search['fields']['expert'] = $lang->reviewmanage->expert;
$config->reviewmanage->search['fields']['reviewedBy'] = $lang->reviewmanage->reviewedBy;
$config->reviewmanage->search['fields']['outside'] = $lang->reviewmanage->outside;
$config->reviewmanage->search['fields']['meetingPlanExport'] = $lang->reviewmanage->meetingPlanExport;
$config->reviewmanage->search['fields']['relatedUsers'] = $lang->reviewmanage->relatedUsers;
$config->reviewmanage->search['fields']['createdBy'] = $lang->reviewmanage->createdBy;
$config->reviewmanage->search['fields']['createdDate'] = $lang->reviewmanage->createdDate;
$config->reviewmanage->search['fields']['createdDept'] = $lang->reviewmanage->createdDept;
$config->reviewmanage->search['fields']['closePerson'] = $lang->reviewmanage->closePerson;
$config->reviewmanage->search['fields']['closeTime'] = $lang->reviewmanage->closeTime;
$config->reviewmanage->search['fields']['qa'] = $lang->reviewmanage->qa;
$config->reviewmanage->search['fields']['preReviewDeadline'] = $lang->reviewmanage->preReviewDeadline;
$config->reviewmanage->search['fields']['firstReviewDeadline'] = $lang->reviewmanage->firstReviewDeadline;
$config->reviewmanage->search['fields']['projectType'] = $lang->reviewmanage->projectType ;
$config->reviewmanage->search['fields']['isImportant'] = $lang->reviewmanage->isImportant ;
$config->reviewmanage->search['fields']['deadline'] = $lang->reviewmanage->deadline;
$config->reviewmanage->search['fields']['closeDate'] = $lang->reviewmanage->closeDate;
$config->reviewmanage->search['fields']['qualityCm'] = $lang->reviewmanage->qualityCm;
$config->reviewmanage->search['fields']['suspendBy']     = $lang->reviewmanage->suspendBy ;
$config->reviewmanage->search['fields']['suspendTime']   = $lang->reviewmanage->suspendTime ;
$config->reviewmanage->search['fields']['suspendReason'] = $lang->reviewmanage->suspendReason ;
$config->reviewmanage->search['fields']['renewBy']     = $lang->reviewmanage->renewBy ;
$config->reviewmanage->search['fields']['renewTime']   = $lang->reviewmanage->renewTime ;
$config->reviewmanage->search['fields']['renewReason'] = $lang->reviewmanage->renewReason ;
$config->reviewmanage->search['fields']['baseLineCondition'] = $lang->reviewmanage->baseLineCondition;


$config->reviewmanage->search['params']['id'] = array('operator' => '=', 'control' => 'input', 'values' => '');
$config->reviewmanage->search['params']['title'] = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->reviewmanage->search['params']['status'] = array('operator' => '=', 'control' => 'select', 'values' => '');
$config->reviewmanage->search['params']['dealUser'] = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->reviewmanage->search['params']['endDate'] = array('operator' => '=', 'control' => 'input', 'class' => 'date', 'values' => '');
$config->reviewmanage->search['params']['object'] = array('operator' => '=', 'control' => 'select', 'values' => array());
$config->reviewmanage->search['params']['type'] = array('operator' => '=', 'control' => 'select', 'values' => $lang->reviewmanage->typeList);
$config->reviewmanage->search['params']['owner'] = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->reviewmanage->search['params']['grade'] = array('operator' => '=', 'control' => 'select', 'values' => $lang->reviewmanage->gradeList);
$config->reviewmanage->search['params']['meetingPlanTime'] = array('operator' => '=', 'control' => 'input', 'class' => 'date', 'values' => '');
$config->reviewmanage->search['params']['meetingCode'] = array('operator' => 'include', 'control' => 'input', 'class' => 'input', 'values' => '');
$config->reviewmanage->search['params']['meetingRealTime'] = array('operator' => '=', 'control' => 'input', 'class' => 'date', 'values' => '');
$config->reviewmanage->search['params']['reviewer'] = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->reviewmanage->search['params']['expert'] = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->reviewmanage->search['params']['reviewedBy'] = array('operator' => '=', 'control' => 'select', 'values' => array());
$config->reviewmanage->search['params']['outside'] = array('operator' => '=', 'control' => 'select', 'values' => array());
$config->reviewmanage->search['params']['meetingPlanExport'] = array('operator' => '=', 'control' => 'select', 'values' => array());
$config->reviewmanage->search['params']['relatedUsers'] = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->reviewmanage->search['params']['createdBy'] = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->reviewmanage->search['params']['createdDate'] = array('operator' => '>=', 'control' => 'input', 'class' => 'date', 'values' => '');
$config->reviewmanage->search['params']['createdDept'] = array('operator' => '=', 'control' => 'select', 'values' => '');
$config->reviewmanage->search['params']['closePerson'] = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->reviewmanage->search['params']['closeTime'] = array('operator' => '=', 'control' => 'input', 'class' => 'date', 'values' => '');
$config->reviewmanage->search['params']['qa'] = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->reviewmanage->search['params']['preReviewDeadline'] = array('operator' => '=', 'control' => 'input', 'class' => 'date', 'values' => '');
$config->reviewmanage->search['params']['firstReviewDeadline'] = array('operator' => '=', 'control' => 'input', 'class' => 'date', 'values' => '');
$config->reviewmanage->search['params']['projectType'] = array('operator' => '=', 'control' => 'select', 'values' => array());
$config->reviewmanage->search['params']['isImportant'] = array('operator' => '=', 'control' => 'select', 'values' => $lang->reviewmanage->isImportantList);
$config->reviewmanage->search['params']['deadline'] = array('operator' => '=', 'control' => 'input', 'class' => 'date', 'values' => '');
$config->reviewmanage->search['params']['closeDate'] = array('operator' => '=', 'control' => 'input', 'class' => 'date', 'values' => '');
$config->reviewmanage->search['params']['qualityCm'] = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->reviewmanage->search['params']['suspendBy']      = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->reviewmanage->search['params']['suspendTime']    = array('operator' => '=', 'control' => 'input', 'class' => 'date', 'values' => '');
$config->reviewmanage->search['params']['suspendReason']  = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->reviewmanage->search['params']['renewBy']        = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->reviewmanage->search['params']['renewTime']      = array('operator' => '=', 'control' => 'input', 'class' => 'date', 'values' => '');
$config->reviewmanage->search['params']['renewReason']    = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->reviewmanage->search['params']['baseLineCondition'] = array('operator' => '=', 'control' => 'select', 'values' => $lang->reviewmanage->condition);


$config->reviewmanage->create = new stdclass();
$config->reviewmanage->create->requiredFields = 'owner,meetingPlanExport,meetingPlanTime';



$config->reviewmeet = new stdclass();
$config->reviewmeet->datatable = new stdclass();
$config->reviewmeet->datatable->defaultField = array('meetingCode', 'status', 'dealUser','title', 'owner', 'reviewer', 'meetingPlanTime', 'meetingRealTime','createdDept','createdBy', 'object', 'meetingPlanExport', 'relatedUsers', 'actions');

$config->reviewmeet->datatable->fieldList['meetingCode']['title'] = 'meetingCode';
$config->reviewmeet->datatable->fieldList['meetingCode']['fixed'] = 'left';
$config->reviewmeet->datatable->fieldList['meetingCode']['width'] = '150';
$config->reviewmeet->datatable->fieldList['meetingCode']['required'] = 'yes';

$config->reviewmeet->datatable->fieldList['status']['title'] = 'status';
$config->reviewmeet->datatable->fieldList['status']['fixed'] = 'left';
$config->reviewmeet->datatable->fieldList['status']['width'] = '160';
$config->reviewmeet->datatable->fieldList['status']['required'] = 'yes';

$config->reviewmeet->datatable->fieldList['dealUser']['title'] = 'dealUser';
$config->reviewmeet->datatable->fieldList['dealUser']['fixed'] = 'left';
$config->reviewmeet->datatable->fieldList['dealUser']['width'] = '120';
$config->reviewmeet->datatable->fieldList['dealUser']['required'] = 'yes';

$config->reviewmeet->datatable->fieldList['title']['title'] = 'title';
$config->reviewmeet->datatable->fieldList['title']['fixed'] = 'no';
$config->reviewmeet->datatable->fieldList['title']['width'] = '120';
$config->reviewmeet->datatable->fieldList['title']['required'] = 'no';

$config->reviewmeet->datatable->fieldList['owner']['title'] = 'owner';
$config->reviewmeet->datatable->fieldList['owner']['fixed'] = 'no';
$config->reviewmeet->datatable->fieldList['owner']['width'] = '120';
$config->reviewmeet->datatable->fieldList['owner']['required'] = 'no';

$config->reviewmeet->datatable->fieldList['reviewer']['title'] = 'reviewer';
$config->reviewmeet->datatable->fieldList['reviewer']['fixed'] = 'no';
$config->reviewmeet->datatable->fieldList['reviewer']['width'] = '100';
$config->reviewmeet->datatable->fieldList['reviewer']['required'] = 'no';

$config->reviewmeet->datatable->fieldList['meetingPlanTime']['title'] = 'meetingPlanTime';
$config->reviewmeet->datatable->fieldList['meetingPlanTime']['fixed'] = 'no';
$config->reviewmeet->datatable->fieldList['meetingPlanTime']['width'] = '100';
$config->reviewmeet->datatable->fieldList['meetingPlanTime']['required'] = 'no';

$config->reviewmeet->datatable->fieldList['meetingRealTime']['title'] = 'meetingRealTime';
$config->reviewmeet->datatable->fieldList['meetingRealTime']['fixed'] = 'no';
$config->reviewmeet->datatable->fieldList['meetingRealTime']['width'] = '180';
$config->reviewmeet->datatable->fieldList['meetingRealTime']['required'] = 'no';

$config->reviewmeet->datatable->fieldList['object']['title'] = 'object';
$config->reviewmeet->datatable->fieldList['object']['fixed'] = 'no';
$config->reviewmeet->datatable->fieldList['object']['width'] = '180';
$config->reviewmeet->datatable->fieldList['object']['required'] = 'no';

$config->reviewmeet->datatable->fieldList['meetingPlanExport']['title'] = 'meetingPlanExport';
$config->reviewmeet->datatable->fieldList['meetingPlanExport']['fixed'] = 'no';
$config->reviewmeet->datatable->fieldList['meetingPlanExport']['width'] = '100';
$config->reviewmeet->datatable->fieldList['meetingPlanExport']['required'] = 'no';

$config->reviewmeet->datatable->fieldList['relatedUsers']['title'] = 'relatedUsers';
$config->reviewmeet->datatable->fieldList['relatedUsers']['fixed'] = 'no';
$config->reviewmeet->datatable->fieldList['relatedUsers']['width'] = '120';
$config->reviewmeet->datatable->fieldList['relatedUsers']['required'] = 'no';

$config->reviewmeet->datatable->fieldList['createdDept']['title'] = 'createdDept';
$config->reviewmeet->datatable->fieldList['createdDept']['fixed'] = 'no';
$config->reviewmeet->datatable->fieldList['createdDept']['width'] = '120';
$config->reviewmeet->datatable->fieldList['createdDept']['required'] = 'no';

$config->reviewmeet->datatable->fieldList['createdBy']['title'] = 'createdBy';
$config->reviewmeet->datatable->fieldList['createdBy']['fixed'] = 'no';
$config->reviewmeet->datatable->fieldList['createdBy']['width'] = '150';
$config->reviewmeet->datatable->fieldList['createdBy']['required'] = 'no';

$config->reviewmeet->datatable->fieldList['actions']['title'] = 'actions';
$config->reviewmeet->datatable->fieldList['actions']['fixed'] = 'right';
$config->reviewmeet->datatable->fieldList['actions']['width'] = '225';
$config->reviewmeet->datatable->fieldList['actions']['required'] = 'yes';


$config->waitmeeting = new stdclass();
$config->waitmeeting->datatable = new stdclass();
$config->waitmeeting->datatable->defaultField = array('meetingCode', 'status', 'dealUser','title', 'owner', 'reviewer', 'meetingPlanTime',  'meetingPlanExport',  'createdDept', 'createdBy',  'actions');



$config->waitmeeting->datatable->fieldList['meetingCode']['title'] = 'meetingCode';
$config->waitmeeting->datatable->fieldList['meetingCode']['fixed'] = 'left';
$config->waitmeeting->datatable->fieldList['meetingCode']['width'] = '140';
$config->waitmeeting->datatable->fieldList['meetingCode']['required'] = 'yes';

$config->waitmeeting->datatable->fieldList['status']['title'] = 'status';
$config->waitmeeting->datatable->fieldList['status']['fixed'] = 'left';
$config->waitmeeting->datatable->fieldList['status']['width'] = '110';
$config->waitmeeting->datatable->fieldList['status']['required'] = 'yes';

$config->waitmeeting->datatable->fieldList['dealUser']['title'] = 'dealUser';
$config->waitmeeting->datatable->fieldList['dealUser']['fixed'] = 'left';
$config->waitmeeting->datatable->fieldList['dealUser']['width'] = '80';
$config->waitmeeting->datatable->fieldList['dealUser']['required'] = 'yes';

$config->waitmeeting->datatable->fieldList['title']['title'] = 'title';
$config->waitmeeting->datatable->fieldList['title']['fixed'] = 'yes';
$config->waitmeeting->datatable->fieldList['title']['width'] = '135';
$config->waitmeeting->datatable->fieldList['title']['required'] = 'no';

$config->waitmeeting->datatable->fieldList['owner']['title'] = 'owner';
$config->waitmeeting->datatable->fieldList['owner']['fixed'] = 'no';
$config->waitmeeting->datatable->fieldList['owner']['width'] = '80';
$config->waitmeeting->datatable->fieldList['owner']['required'] = 'no';

$config->waitmeeting->datatable->fieldList['reviewer']['title'] = 'reviewer';
$config->waitmeeting->datatable->fieldList['reviewer']['fixed'] = 'no';
$config->waitmeeting->datatable->fieldList['reviewer']['width'] = '80';
$config->waitmeeting->datatable->fieldList['reviewer']['required'] = 'no';

$config->waitmeeting->datatable->fieldList['meetingPlanTime']['title'] = 'meetingPlanTime';
$config->waitmeeting->datatable->fieldList['meetingPlanTime']['fixed'] = 'no';
$config->waitmeeting->datatable->fieldList['meetingPlanTime']['width'] = '135';
$config->waitmeeting->datatable->fieldList['meetingPlanTime']['required'] = 'no';

$config->waitmeeting->datatable->fieldList['object']['title'] = 'object';
$config->waitmeeting->datatable->fieldList['object']['fixed'] = 'no';
$config->waitmeeting->datatable->fieldList['object']['width'] = '150';
$config->waitmeeting->datatable->fieldList['object']['required'] = 'no';

$config->waitmeeting->datatable->fieldList['meetingPlanExport']['title'] = 'meetingPlanExport';
$config->waitmeeting->datatable->fieldList['meetingPlanExport']['fixed'] = 'no';
$config->waitmeeting->datatable->fieldList['meetingPlanExport']['width'] = '130';
$config->waitmeeting->datatable->fieldList['meetingPlanExport']['required'] = 'no';

$config->waitmeeting->datatable->fieldList['relatedUsers']['title'] = 'relatedUsers';
$config->waitmeeting->datatable->fieldList['relatedUsers']['fixed'] = 'no';
$config->waitmeeting->datatable->fieldList['relatedUsers']['width'] = '150';
$config->waitmeeting->datatable->fieldList['relatedUsers']['required'] = 'no';

$config->waitmeeting->datatable->fieldList['createdDept']['title'] = 'createdDept';
$config->waitmeeting->datatable->fieldList['createdDept']['fixed'] = 'no';
$config->waitmeeting->datatable->fieldList['createdDept']['width'] = '80';
$config->waitmeeting->datatable->fieldList['createdDept']['required'] = 'no';

$config->waitmeeting->datatable->fieldList['createdBy']['title'] = 'createdBy';
$config->waitmeeting->datatable->fieldList['createdBy']['fixed'] = 'no';
$config->waitmeeting->datatable->fieldList['createdBy']['width'] = '90';
$config->waitmeeting->datatable->fieldList['createdBy']['required'] = 'no';

$config->waitmeeting->datatable->fieldList['actions']['title'] = 'actions';
$config->waitmeeting->datatable->fieldList['actions']['fixed'] = 'right';
$config->waitmeeting->datatable->fieldList['actions']['width'] = '225';
$config->waitmeeting->datatable->fieldList['actions']['required'] = 'yes';

$config->joinwait = new stdclass();
$config->joinwait->datatable = new stdclass();
$config->joinwait->datatable->defaultField = array('meetingCode', 'title','status', 'owner','reviewer','meetingPlanTime', 'meetingPlanExport',  'createdDept', 'createdBy',  'actions');

$config->joinwait->datatable->fieldList['meetingCode']['title'] = 'meetingCode';
$config->joinwait->datatable->fieldList['meetingCode']['fixed'] = 'left';
$config->joinwait->datatable->fieldList['meetingCode']['width'] = '130';
$config->joinwait->datatable->fieldList['meetingCode']['required'] = 'yes';

$config->joinwait->datatable->fieldList['title']['title'] = 'title';
$config->joinwait->datatable->fieldList['title']['fixed'] = 'left';
$config->joinwait->datatable->fieldList['title']['width'] = '190';
$config->joinwait->datatable->fieldList['title']['required'] = 'yes';

$config->joinwait->datatable->fieldList['status']['title'] = 'status';
$config->joinwait->datatable->fieldList['status']['fixed'] = 'left';
$config->joinwait->datatable->fieldList['status']['width'] = '100';
$config->joinwait->datatable->fieldList['status']['required'] = 'yes';

$config->joinwait->datatable->fieldList['meetingPlanTime']['title'] = 'meetingPlanTime';
$config->joinwait->datatable->fieldList['meetingPlanTime']['fixed'] = 'left';
$config->joinwait->datatable->fieldList['meetingPlanTime']['width'] = '140';
$config->joinwait->datatable->fieldList['meetingPlanTime']['required'] = 'yes';

$config->joinwait->datatable->fieldList['object']['title'] = 'object';
$config->joinwait->datatable->fieldList['object']['fixed'] = 'no';
$config->joinwait->datatable->fieldList['object']['width'] = '190';
$config->joinwait->datatable->fieldList['object']['required'] = 'no';

$config->joinwait->datatable->fieldList['owner']['title'] = 'owner';
$config->joinwait->datatable->fieldList['owner']['fixed'] = 'no';
$config->joinwait->datatable->fieldList['owner']['width'] = '90';
$config->joinwait->datatable->fieldList['owner']['required'] = 'no';

$config->joinwait->datatable->fieldList['reviewer']['title'] = 'reviewer';
$config->joinwait->datatable->fieldList['reviewer']['fixed'] = 'no';
$config->joinwait->datatable->fieldList['reviewer']['width'] = '90';
$config->joinwait->datatable->fieldList['reviewer']['required'] = 'no';

$config->joinwait->datatable->fieldList['meetingPlanExport']['title'] = 'meetingPlanExport';
$config->joinwait->datatable->fieldList['meetingPlanExport']['fixed'] = 'no';
$config->joinwait->datatable->fieldList['meetingPlanExport']['width'] = '180';
$config->joinwait->datatable->fieldList['meetingPlanExport']['required'] = 'no';

$config->joinwait->datatable->fieldList['createdDept']['title'] = 'createdDept';
$config->joinwait->datatable->fieldList['createdDept']['fixed'] = 'no';
$config->joinwait->datatable->fieldList['createdDept']['width'] = '120';
$config->joinwait->datatable->fieldList['createdDept']['required'] = 'no';

$config->joinwait->datatable->fieldList['createdBy']['title'] = 'createdBy';
$config->joinwait->datatable->fieldList['createdBy']['fixed'] = 'no';
$config->joinwait->datatable->fieldList['createdBy']['width'] = '175';
$config->joinwait->datatable->fieldList['createdBy']['required'] = 'no';

$config->joinwait->datatable->fieldList['actions']['title'] = 'actions';
$config->joinwait->datatable->fieldList['actions']['fixed'] = 'right';
$config->joinwait->datatable->fieldList['actions']['width'] = '225';
$config->joinwait->datatable->fieldList['actions']['required'] = 'yes';


$config->waitreview  = new stdclass();
$config->waitreview->datatable = new stdclass();
$config->waitreview->datatable->defaultField = array('id', 'title', 'status', 'dealUser','deadDate', 'object', 'type','owner','grade', 'meetingPlanTime','meetingCode', 'meetingRealTime','createdDept', 'createdBy', 'reviewer',  'expert', 'reviewedBy', 'outside', 'meetingPlanExport', 'relatedUsers','deadline','projectType','isImportant','createdDate','editBy','editDate','actions');

$config->waitreview->datatable->fieldList['id']['title']    = 'id';
$config->waitreview->datatable->fieldList['id']['fixed']    = 'left';
$config->waitreview->datatable->fieldList['id']['width']    = '30';
$config->waitreview->datatable->fieldList['id']['required'] = 'yes';

$config->waitreview->datatable->fieldList['title']['title']    = 'title';
$config->waitreview->datatable->fieldList['title']['fixed']    = 'left';
$config->waitreview->datatable->fieldList['title']['width']    = '150';
$config->waitreview->datatable->fieldList['title']['required'] = 'yes';

$config->waitreview->datatable->fieldList['status']['title']    = 'status';
$config->waitreview->datatable->fieldList['status']['fixed']    = 'left';
$config->waitreview->datatable->fieldList['status']['width']    = '100';
$config->waitreview->datatable->fieldList['status']['required'] = 'yes';

$config->waitreview->datatable->fieldList['dealUser']['title']    = 'dealUser';
$config->waitreview->datatable->fieldList['dealUser']['fixed']    = 'left';
$config->waitreview->datatable->fieldList['dealUser']['width']    = '130';
$config->waitreview->datatable->fieldList['dealUser']['required'] = 'yes';

$config->waitreview->datatable->fieldList['deadDate']['title'] = 'deadDate';
$config->waitreview->datatable->fieldList['deadDate']['fixed'] = 'left';
$config->waitreview->datatable->fieldList['deadDate']['width'] = '120';
$config->waitreview->datatable->fieldList['deadDate']['required'] = 'yes';

$config->waitreview->datatable->fieldList['object']['title']    = 'object';
$config->waitreview->datatable->fieldList['object']['fixed']    = 'no';
$config->waitreview->datatable->fieldList['object']['width']    = '180';
$config->waitreview->datatable->fieldList['object']['required'] = 'yes';

$config->waitreview->datatable->fieldList['type']['title']    = 'type';
$config->waitreview->datatable->fieldList['type']['fixed']    = 'no';
$config->waitreview->datatable->fieldList['type']['width']    = '120';
$config->waitreview->datatable->fieldList['type']['required'] = 'yes';

$config->waitreview->datatable->fieldList['owner']['title']    = 'owner';
$config->waitreview->datatable->fieldList['owner']['fixed']    = 'no';
$config->waitreview->datatable->fieldList['owner']['width']    = '120';
$config->waitreview->datatable->fieldList['owner']['required'] = 'no';

$config->waitreview->datatable->fieldList['grade']['title']    = 'grade';
$config->waitreview->datatable->fieldList['grade']['fixed']    = 'no';
$config->waitreview->datatable->fieldList['grade']['width']    = '100';
$config->waitreview->datatable->fieldList['grade']['required'] = 'yes';

$config->waitreview->datatable->fieldList['meetingPlanTime']['title']    = 'meetingPlanTime';
$config->waitreview->datatable->fieldList['meetingPlanTime']['fixed']    = 'no';
$config->waitreview->datatable->fieldList['meetingPlanTime']['width']    = '180';
$config->waitreview->datatable->fieldList['meetingPlanTime']['required'] = 'no';

$config->waitreview->datatable->fieldList['meetingCode']['title']    = 'meetingCode';
$config->waitreview->datatable->fieldList['meetingCode']['fixed']    = 'no';
$config->waitreview->datatable->fieldList['meetingCode']['width']    = '180';
$config->waitreview->datatable->fieldList['meetingCode']['required'] = 'no';

$config->waitreview->datatable->fieldList['meetingRealTime']['title']    = 'meetingRealTime';
$config->waitreview->datatable->fieldList['meetingRealTime']['fixed']    = 'no';
$config->waitreview->datatable->fieldList['meetingRealTime']['width']    = '180';
$config->waitreview->datatable->fieldList['meetingRealTime']['required'] = 'no';


$config->waitreview->datatable->fieldList['reviewer']['title']    = 'reviewer';
$config->waitreview->datatable->fieldList['reviewer']['fixed']    = 'no';
$config->waitreview->datatable->fieldList['reviewer']['width']    = '100';
$config->waitreview->datatable->fieldList['reviewer']['required'] = 'no';

$config->waitreview->datatable->fieldList['expert']['title']    = 'expert';
$config->waitreview->datatable->fieldList['expert']['fixed']    = 'no';
$config->waitreview->datatable->fieldList['expert']['width']    = '120';
$config->waitreview->datatable->fieldList['expert']['required'] = 'no';

$config->waitreview->datatable->fieldList['reviewedBy']['title']    = 'reviewedBy';
$config->waitreview->datatable->fieldList['reviewedBy']['fixed']    = 'no';
$config->waitreview->datatable->fieldList['reviewedBy']['width']    = '150';
$config->waitreview->datatable->fieldList['reviewedBy']['required'] = 'no';

$config->waitreview->datatable->fieldList['outside']['title']    = 'outside';
$config->waitreview->datatable->fieldList['outside']['fixed']    = 'no';
$config->waitreview->datatable->fieldList['outside']['width']    = '150';
$config->waitreview->datatable->fieldList['outside']['required'] = 'no';

$config->waitreview->datatable->fieldList['meetingPlanExport']['title']    = 'meetingPlanExport';
$config->waitreview->datatable->fieldList['meetingPlanExport']['fixed']    = 'no';
$config->waitreview->datatable->fieldList['meetingPlanExport']['width']    = '150';
$config->waitreview->datatable->fieldList['meetingPlanExport']['required'] = 'no';

$config->waitreview->datatable->fieldList['relatedUsers']['title']    = 'relatedUsers';
$config->waitreview->datatable->fieldList['relatedUsers']['fixed']    = 'no';
$config->waitreview->datatable->fieldList['relatedUsers']['width']    = '150';
$config->waitreview->datatable->fieldList['relatedUsers']['required'] = 'no';

$config->waitreview->datatable->fieldList['createdBy']['title']    = 'createdBy';
$config->waitreview->datatable->fieldList['createdBy']['fixed']    = 'no';
$config->waitreview->datatable->fieldList['createdBy']['width']    = '120';
$config->waitreview->datatable->fieldList['createdBy']['required'] = 'no';

$config->waitreview->datatable->fieldList['createdDate']['title']    = 'createdDate';
$config->waitreview->datatable->fieldList['createdDate']['fixed']    = 'no';
$config->waitreview->datatable->fieldList['createdDate']['width']    = '120';
$config->waitreview->datatable->fieldList['createdDate']['required'] = 'no';

$config->waitreview->datatable->fieldList['createdDept']['title']    = 'createdDept';
$config->waitreview->datatable->fieldList['createdDept']['fixed']    = 'no';
$config->waitreview->datatable->fieldList['createdDept']['width']    = '120';
$config->waitreview->datatable->fieldList['createdDept']['required'] = 'no';

$config->waitreview->datatable->fieldList['editBy']['title']    = 'editBy';
$config->waitreview->datatable->fieldList['editBy']['fixed']    = 'no';
$config->waitreview->datatable->fieldList['editBy']['width']    = '120';
$config->waitreview->datatable->fieldList['editBy']['required'] = 'no';

$config->waitreview->datatable->fieldList['editDate']['title']    = 'editDate';
$config->waitreview->datatable->fieldList['editDate']['fixed']    = 'no';
$config->waitreview->datatable->fieldList['editDate']['width']    = '120';
$config->waitreview->datatable->fieldList['editDate']['required'] = 'no';

$config->waitreview->datatable->fieldList['closePerson']['title']    = 'closePerson';
$config->waitreview->datatable->fieldList['closePerson']['fixed']    = 'no';
$config->waitreview->datatable->fieldList['closePerson']['width']    = '120';
$config->waitreview->datatable->fieldList['closePerson']['required'] = 'no';

$config->waitreview->datatable->fieldList['closeTime']['title']    = 'closeTime';
$config->waitreview->datatable->fieldList['closeTime']['fixed']    = 'no';
$config->waitreview->datatable->fieldList['closeTime']['width']    = '120';
$config->waitreview->datatable->fieldList['closeTime']['required'] = 'no';

$config->waitreview->datatable->fieldList['qa']['title']    = 'qa';
$config->waitreview->datatable->fieldList['qa']['fixed']    = 'no';
$config->waitreview->datatable->fieldList['qa']['width']    = '120';
$config->waitreview->datatable->fieldList['qa']['required'] = 'no';

$config->waitreview->datatable->fieldList['trialDept']['title']    = 'trialDept';
$config->waitreview->datatable->fieldList['trialDept']['fixed']    = 'no';
$config->waitreview->datatable->fieldList['trialDept']['width']    = '120';
$config->waitreview->datatable->fieldList['trialDept']['required'] = 'no';

$config->waitreview->datatable->fieldList['trialDeptLiasisonOfficer']['title']    = 'trialDeptLiasisonOfficer';
$config->waitreview->datatable->fieldList['trialDeptLiasisonOfficer']['fixed']    = 'no';
$config->waitreview->datatable->fieldList['trialDeptLiasisonOfficer']['width']    = '120';
$config->waitreview->datatable->fieldList['trialDeptLiasisonOfficer']['required'] = 'no';

$config->waitreview->datatable->fieldList['trialAdjudicatingOfficer']['title']    = 'trialAdjudicatingOfficer';
$config->waitreview->datatable->fieldList['trialAdjudicatingOfficer']['fixed']    = 'no';
$config->waitreview->datatable->fieldList['trialAdjudicatingOfficer']['width']    = '120';
$config->waitreview->datatable->fieldList['trialAdjudicatingOfficer']['required'] = 'no';

$config->waitreview->datatable->fieldList['trialJoinOfficer']['title']    = 'trialJoinOfficer';
$config->waitreview->datatable->fieldList['trialJoinOfficer']['fixed']    = 'no';
$config->waitreview->datatable->fieldList['trialJoinOfficer']['width']    = '120';
$config->waitreview->datatable->fieldList['trialJoinOfficer']['required'] = 'no';

$config->waitreview->datatable->fieldList['preReviewDeadline']['title']    = 'preReviewDeadline';
$config->waitreview->datatable->fieldList['preReviewDeadline']['fixed']    = 'no';
$config->waitreview->datatable->fieldList['preReviewDeadline']['width']    = '120';
$config->waitreview->datatable->fieldList['preReviewDeadline']['required'] = 'no';

$config->waitreview->datatable->fieldList['firstReviewDeadline']['title']    = 'firstReviewDeadline';
$config->waitreview->datatable->fieldList['firstReviewDeadline']['fixed']    = 'no';
$config->waitreview->datatable->fieldList['firstReviewDeadline']['width']    = '120';
$config->waitreview->datatable->fieldList['firstReviewDeadline']['required'] = 'no';

$config->waitreview->datatable->fieldList['deadline']['title']    = 'deadline';
$config->waitreview->datatable->fieldList['deadline']['fixed']    = 'no';
$config->waitreview->datatable->fieldList['deadline']['width']    = '120';
$config->waitreview->datatable->fieldList['deadline']['required'] = 'no';

$config->waitreview->datatable->fieldList['projectType']['title']    = 'projectType';
$config->waitreview->datatable->fieldList['projectType']['fixed']    = 'no';
$config->waitreview->datatable->fieldList['projectType']['width']    = '120';
$config->waitreview->datatable->fieldList['projectType']['required'] = 'no';

$config->waitreview->datatable->fieldList['isImportant']['title']    = 'isImportant';
$config->waitreview->datatable->fieldList['isImportant']['fixed']    = 'no';
$config->waitreview->datatable->fieldList['isImportant']['width']    = '60';
$config->waitreview->datatable->fieldList['isImportant']['required'] = 'no';

$config->waitreview->datatable->fieldList['closeDate']['title']    = 'closeDate';
$config->waitreview->datatable->fieldList['closeDate']['fixed']    = 'no';
$config->waitreview->datatable->fieldList['closeDate']['width']    = '120';
$config->waitreview->datatable->fieldList['closeDate']['required'] = 'no';

$config->waitreview->datatable->fieldList['qualityCm']['title']    = 'qualityCm';
$config->waitreview->datatable->fieldList['qualityCm']['fixed']    = 'no';
$config->waitreview->datatable->fieldList['qualityCm']['width']    = '120';
$config->waitreview->datatable->fieldList['qualityCm']['required'] = 'no';


$config->waitreview->datatable->fieldList['actions']['title']    = 'actions';
$config->waitreview->datatable->fieldList['actions']['fixed']    = 'right';
$config->waitreview->datatable->fieldList['actions']['width']    = '225';
$config->waitreview->datatable->fieldList['actions']['required'] = 'yes';

$config->reviewissue                              = new stdclass();
$config->reviewissue->datatable                   = new stdclass();
$config->reviewissue->datatable->defaultField     = array('id','review', 'title', 'desc', 'type', 'raiseBy', 'raiseDate' , 'resolutionBy', 'resolutionDate', 'validation', 'verifyDate','status','dealUser','actions');

$config->reviewissue->datatable->fieldList['id']['title']             = 'idAB';
$config->reviewissue->datatable->fieldList['id']['fixed']             = 'left';
$config->reviewissue->datatable->fieldList['id']['width']             = '50';
$config->reviewissue->datatable->fieldList['id']['required']          = 'yes';

$config->reviewissue->datatable->fieldList['review']['title']         = 'review';
$config->reviewissue->datatable->fieldList['review']['fixed']         = 'left';
$config->reviewissue->datatable->fieldList['review']['width']         = '125';
$config->reviewissue->datatable->fieldList['review']['required']      = 'yes';

$config->reviewissue->datatable->fieldList['title']['title']          = 'title';
$config->reviewissue->datatable->fieldList['title']['fixed']          = 'left';
$config->reviewissue->datatable->fieldList['title']['width']          = '100';
$config->reviewissue->datatable->fieldList['title']['required']       = 'yes';

$config->reviewissue->datatable->fieldList['desc']['title']           = 'desc';
$config->reviewissue->datatable->fieldList['desc']['fixed']           = 'left';
$config->reviewissue->datatable->fieldList['desc']['width']           = '125';
$config->reviewissue->datatable->fieldList['desc']['required']        = 'yes';

$config->reviewissue->datatable->fieldList['type']['title']           = 'type';
$config->reviewissue->datatable->fieldList['type']['fixed']           = 'no';
$config->reviewissue->datatable->fieldList['type']['width']           = '90';
$config->reviewissue->datatable->fieldList['type']['required']        = 'yes';

$config->reviewissue->datatable->fieldList['raiseBy']['title']        = 'raiseBy';
$config->reviewissue->datatable->fieldList['raiseBy']['fixed']        = 'no';
$config->reviewissue->datatable->fieldList['raiseBy']['width']        = '90';
$config->reviewissue->datatable->fieldList['raiseBy']['required']     = 'no';

$config->reviewissue->datatable->fieldList['raiseDate']['title']      = 'raiseDate';
$config->reviewissue->datatable->fieldList['raiseDate']['fixed']      = 'no';
$config->reviewissue->datatable->fieldList['raiseDate']['width']      = '90';
$config->reviewissue->datatable->fieldList['raiseDate']['required']   = 'no';

$config->reviewissue->datatable->fieldList['resolutionBy']['title']   = 'resolutionBy';
$config->reviewissue->datatable->fieldList['resolutionBy']['fixed']   = 'no';
$config->reviewissue->datatable->fieldList['resolutionBy']['width']   = '90';
$config->reviewissue->datatable->fieldList['resolutionBy']['required']= 'no';

$config->reviewissue->datatable->fieldList['resolutionDate']['title'] = 'resolutionDate';
$config->reviewissue->datatable->fieldList['resolutionDate']['fixed'] = 'no';
$config->reviewissue->datatable->fieldList['resolutionDate']['width'] = '90';
$config->reviewissue->datatable->fieldList['resolutionDate']['required']= 'no';

$config->reviewissue->datatable->fieldList['dealDesc']['title']       = 'dealDesc';
$config->reviewissue->datatable->fieldList['dealDesc']['fixed']       = 'no';
$config->reviewissue->datatable->fieldList['dealDesc']['width']       = '150';
$config->reviewissue->datatable->fieldList['dealDesc']['required']    = 'no';

$config->reviewissue->datatable->fieldList['validation']['title']     = 'validation';
$config->reviewissue->datatable->fieldList['validation']['fixed']     = 'no';
$config->reviewissue->datatable->fieldList['validation']['width']     = '90';
$config->reviewissue->datatable->fieldList['validation']['required']  = 'no';

$config->reviewissue->datatable->fieldList['verifyDate']['title']     = 'verifyDate';
$config->reviewissue->datatable->fieldList['verifyDate']['fixed']     = 'no';
$config->reviewissue->datatable->fieldList['verifyDate']['width']     = '90';
$config->reviewissue->datatable->fieldList['verifyDate']['required']  = 'no';

$config->reviewissue->datatable->fieldList['status']['title']         = 'status';
$config->reviewissue->datatable->fieldList['status']['fixed']         = 'right';
$config->reviewissue->datatable->fieldList['status']['width']         = '80';
$config->reviewissue->datatable->fieldList['status']['required']      = 'no';

$config->reviewissue->datatable->fieldList['dealUser']['title']       = 'dealUser';
$config->reviewissue->datatable->fieldList['dealUser']['fixed']       = 'right';
$config->reviewissue->datatable->fieldList['dealUser']['width']       = '75';
$config->reviewissue->datatable->fieldList['dealUser']['required']    = 'no';

$config->reviewissue->datatable->fieldList['actions']['title']        = 'actions';
$config->reviewissue->datatable->fieldList['actions']['fixed']        = 'right';
$config->reviewissue->datatable->fieldList['actions']['width']        = '50';
$config->reviewissue->datatable->fieldList['actions']['required']     = 'yes';