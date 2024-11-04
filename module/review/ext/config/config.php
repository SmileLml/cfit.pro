<?php
$config->review = new stdclass();
$config->review->editor = new stdclass();
$config->review->editor->create  = array('id' => 'comment', 'tools' => 'simpleTools');
$config->review->editor->edit    = array('id' => 'comment', 'tools' => 'simpleTools');
$config->review->editor->submit  = array('id' => 'comment', 'tools' => 'simpleTools');
$config->review->editor->toaudit = array('id' => 'comment', 'tools' => 'simpleTools');
//$config->review->editor->assess  = array('id' => 'opinion', 'tools' => 'simpleTools');
//审批
$config->review->editor->review  = array('id' => 'comment,meetingSummary,meetingContent', 'tools' => 'simpleTools');
//指派
$config->review->editor->assign  = array('id' => 'comment', 'tools' => 'simpleTools');
//关闭
$config->review->editor->close  = array('id' => 'comment', 'tools' => 'simpleTools');
//view
$config->review->editor->view    = array('id' => 'comment,lastComment', 'tools' => 'simpleTools');
//编辑附件
$config->review->editor->editfiles  = array('id' => 'currentComment', 'tools' => 'simpleTools');
//挂起
$config->review->editor->suspend  = array('id' => 'comment', 'tools' => 'simpleTools');
//恢复
$config->review->editor->renew  = array('id' => 'comment', 'tools' => 'simpleTools');
$config->review->editor->projectswap  = array('id' => 'currentComment', 'tools' => 'simpleTools');

$config->review->create = new stdclass();
$config->review->create->requiredFields = 'title,object,type,qa,reviewer,owner,files,deadline,relatedUsers,qualityCm';

//关闭
$config->review->close = new stdclass();
$config->review->close->requiredFields  = 'status';

//关闭
$config->review->editfiles = new stdclass();
$config->review->editfiles->requiredFields  = 'consumed,currentComment';

//项目移动空间
$config->review->projectswap = new stdclass();
$config->review->projectswap->requiredFields  = 'projects,currentComment';



$config->review->edit = new stdclass();
$config->review->edit->requiredFields = 'title,object,type,qa,reviewer,owner,files,deadline,relatedUsers,qualityCm';

$config->review->submit = new stdclass();
$config->review->submit->requiredFields = 'qa,preReviewDeadline';

$config->review->review = new stdclass();
$config->review->assign = new stdclass();

//指派正式评审人员
$config->review->assignFormalAssignReviewer = new stdclass();
$config->review->assignFormalAssignReviewer->requiredFields = 'type,  reviewer, owner, deadline';

$config->review->workloadedit  = new stdclass();
$config->review->workloadedit->requiredFields = 'account,consumed,after';

$config->review->datatable = new stdclass();
$config->review->datatable->defaultField = array('id', 'title', 'status', 'dealUser', 'deadDate','object', 'type', 'owner','grade', 'meetingPlanTime','meetingCode', 'meetingRealTime', 'reviewer', 'expert', 'reviewedBy', 'outside', 'meetingPlanExport', 'relatedUsers','deadline','projectType','isImportant','createdBy','createdDate','editBy','editDate','actions');


$config->review->datatable->fieldList['id']['title']    = 'idAB';
$config->review->datatable->fieldList['id']['fixed']    = 'left';
$config->review->datatable->fieldList['id']['width']    = '60';
$config->review->datatable->fieldList['id']['required'] = 'yes';

$config->review->datatable->fieldList['title']['title']    = 'title';
$config->review->datatable->fieldList['title']['fixed']    = 'left';
$config->review->datatable->fieldList['title']['width']    = 'auto';
$config->review->datatable->fieldList['title']['required'] = 'yes';

$config->review->datatable->fieldList['status']['title']    = 'status';
$config->review->datatable->fieldList['status']['fixed']    = 'left';
$config->review->datatable->fieldList['status']['width']    = '160';
$config->review->datatable->fieldList['status']['required'] = 'yes';

$config->review->datatable->fieldList['dealUser']['title']    = 'dealUser';
$config->review->datatable->fieldList['dealUser']['fixed']    = 'left';
$config->review->datatable->fieldList['dealUser']['width']    = '120';
$config->review->datatable->fieldList['dealUser']['required'] = 'yes';

$config->review->datatable->fieldList['deadDate']['title']    = 'deadDate';
$config->review->datatable->fieldList['deadDate']['fixed']    = 'no';
$config->review->datatable->fieldList['deadDate']['width']    = '120';
$config->review->datatable->fieldList['deadDate']['required'] = 'no';

$config->review->datatable->fieldList['object']['title']    = 'object';
$config->review->datatable->fieldList['object']['fixed']    = 'no';
$config->review->datatable->fieldList['object']['width']    = '120';
$config->review->datatable->fieldList['object']['required'] = 'no';

$config->review->datatable->fieldList['type']['title']    = 'type';
$config->review->datatable->fieldList['type']['fixed']    = 'no';
$config->review->datatable->fieldList['type']['width']    = '100';
$config->review->datatable->fieldList['type']['required'] = 'no';

$config->review->datatable->fieldList['owner']['title']    = 'owner';
$config->review->datatable->fieldList['owner']['fixed']    = 'no';
$config->review->datatable->fieldList['owner']['width']    = '120';
$config->review->datatable->fieldList['owner']['required'] = 'no';

$config->review->datatable->fieldList['grade']['title']    = 'grade';
$config->review->datatable->fieldList['grade']['fixed']    = 'no';
$config->review->datatable->fieldList['grade']['width']    = '100';
$config->review->datatable->fieldList['grade']['required'] = 'no';

$config->review->datatable->fieldList['meetingPlanTime']['title']    = 'meetingPlanTime';
$config->review->datatable->fieldList['meetingPlanTime']['fixed']    = 'no';
$config->review->datatable->fieldList['meetingPlanTime']['width']    = '180';
$config->review->datatable->fieldList['meetingPlanTime']['required'] = 'no';

$config->review->datatable->fieldList['meetingCode']['title']    = 'meetingCode';
$config->review->datatable->fieldList['meetingCode']['fixed']    = 'no';
$config->review->datatable->fieldList['meetingCode']['width']    = '180';
$config->review->datatable->fieldList['meetingCode']['required'] = 'no';

$config->review->datatable->fieldList['meetingRealTime']['title']    = 'meetingRealTime';
$config->review->datatable->fieldList['meetingRealTime']['fixed']    = 'no';
$config->review->datatable->fieldList['meetingRealTime']['width']    = '180';
$config->review->datatable->fieldList['meetingRealTime']['required'] = 'no';

$config->review->datatable->fieldList['reviewer']['title']    = 'reviewer';
$config->review->datatable->fieldList['reviewer']['fixed']    = 'no';
$config->review->datatable->fieldList['reviewer']['width']    = '100';
$config->review->datatable->fieldList['reviewer']['required'] = 'no';

$config->review->datatable->fieldList['expert']['title']    = 'expert';
$config->review->datatable->fieldList['expert']['fixed']    = 'no';
$config->review->datatable->fieldList['expert']['width']    = '120';
$config->review->datatable->fieldList['expert']['required'] = 'no';

$config->review->datatable->fieldList['reviewedBy']['title']    = 'reviewedBy';
$config->review->datatable->fieldList['reviewedBy']['fixed']    = 'no';
$config->review->datatable->fieldList['reviewedBy']['width']    = '150';
$config->review->datatable->fieldList['reviewedBy']['required'] = 'no';

$config->review->datatable->fieldList['outside']['title']    = 'outside';
$config->review->datatable->fieldList['outside']['fixed']    = 'no';
$config->review->datatable->fieldList['outside']['width']    = '150';
$config->review->datatable->fieldList['outside']['required'] = 'no';

$config->review->datatable->fieldList['meetingPlanExport']['title']    = 'meetingPlanExport';
$config->review->datatable->fieldList['meetingPlanExport']['fixed']    = 'no';
$config->review->datatable->fieldList['meetingPlanExport']['width']    = '150';
$config->review->datatable->fieldList['meetingPlanExport']['required'] = 'no';

$config->review->datatable->fieldList['relatedUsers']['title']    = 'relatedUsers';
$config->review->datatable->fieldList['relatedUsers']['fixed']    = 'no';
$config->review->datatable->fieldList['relatedUsers']['width']    = '150';
$config->review->datatable->fieldList['relatedUsers']['required'] = 'no';

$config->review->datatable->fieldList['createdBy']['title']    = 'createdBy';
$config->review->datatable->fieldList['createdBy']['fixed']    = 'no';
$config->review->datatable->fieldList['createdBy']['width']    = '120';
$config->review->datatable->fieldList['createdBy']['required'] = 'no';

$config->review->datatable->fieldList['createdDate']['title']    = 'createdDate';
$config->review->datatable->fieldList['createdDate']['fixed']    = 'no';
$config->review->datatable->fieldList['createdDate']['width']    = '120';
$config->review->datatable->fieldList['createdDate']['required'] = 'no';

$config->review->datatable->fieldList['createdDept']['title']    = 'createdDept';
$config->review->datatable->fieldList['createdDept']['fixed']    = 'no';
$config->review->datatable->fieldList['createdDept']['width']    = '120';
$config->review->datatable->fieldList['createdDept']['required'] = 'no';

$config->review->datatable->fieldList['editBy']['title']    = 'editBy';
$config->review->datatable->fieldList['editBy']['fixed']    = 'no';
$config->review->datatable->fieldList['editBy']['width']    = '120';
$config->review->datatable->fieldList['editBy']['required'] = 'no';

$config->review->datatable->fieldList['editDate']['title']    = 'editDate';
$config->review->datatable->fieldList['editDate']['fixed']    = 'no';
$config->review->datatable->fieldList['editDate']['width']    = '120';
$config->review->datatable->fieldList['editDate']['required'] = 'no';

$config->review->datatable->fieldList['closePerson']['title']    = 'closePerson';
$config->review->datatable->fieldList['closePerson']['fixed']    = 'no';
$config->review->datatable->fieldList['closePerson']['width']    = '120';
$config->review->datatable->fieldList['closePerson']['required'] = 'no';

$config->review->datatable->fieldList['closeTime']['title']    = 'closeTime';
$config->review->datatable->fieldList['closeTime']['fixed']    = 'no';
$config->review->datatable->fieldList['closeTime']['width']    = '120';
$config->review->datatable->fieldList['closeTime']['required'] = 'no';

$config->review->datatable->fieldList['qa']['title']    = 'qa';
$config->review->datatable->fieldList['qa']['fixed']    = 'no';
$config->review->datatable->fieldList['qa']['width']    = '120';
$config->review->datatable->fieldList['qa']['required'] = 'no';

$config->review->datatable->fieldList['trialDept']['title']    = 'trialDept';
$config->review->datatable->fieldList['trialDept']['fixed']    = 'no';
$config->review->datatable->fieldList['trialDept']['width']    = '120';
$config->review->datatable->fieldList['trialDept']['required'] = 'no';

$config->review->datatable->fieldList['trialDeptLiasisonOfficer']['title']    = 'trialDeptLiasisonOfficer';
$config->review->datatable->fieldList['trialDeptLiasisonOfficer']['fixed']    = 'no';
$config->review->datatable->fieldList['trialDeptLiasisonOfficer']['width']    = '120';
$config->review->datatable->fieldList['trialDeptLiasisonOfficer']['required'] = 'no';

$config->review->datatable->fieldList['trialAdjudicatingOfficer']['title']    = 'trialAdjudicatingOfficer';
$config->review->datatable->fieldList['trialAdjudicatingOfficer']['fixed']    = 'no';
$config->review->datatable->fieldList['trialAdjudicatingOfficer']['width']    = '120';
$config->review->datatable->fieldList['trialAdjudicatingOfficer']['required'] = 'no';

$config->review->datatable->fieldList['trialJoinOfficer']['title']    = 'trialJoinOfficer';
$config->review->datatable->fieldList['trialJoinOfficer']['fixed']    = 'no';
$config->review->datatable->fieldList['trialJoinOfficer']['width']    = '120';
$config->review->datatable->fieldList['trialJoinOfficer']['required'] = 'no';

$config->review->datatable->fieldList['preReviewDeadline']['title']    = 'preReviewDeadline';
$config->review->datatable->fieldList['preReviewDeadline']['fixed']    = 'no';
$config->review->datatable->fieldList['preReviewDeadline']['width']    = '120';
$config->review->datatable->fieldList['preReviewDeadline']['required'] = 'no';

$config->review->datatable->fieldList['firstReviewDeadline']['title']    = 'firstReviewDeadline';
$config->review->datatable->fieldList['firstReviewDeadline']['fixed']    = 'no';
$config->review->datatable->fieldList['firstReviewDeadline']['width']    = '120';
$config->review->datatable->fieldList['firstReviewDeadline']['required'] = 'no';

$config->review->datatable->fieldList['deadline']['title']    = 'deadline';
$config->review->datatable->fieldList['deadline']['fixed']    = 'no';
$config->review->datatable->fieldList['deadline']['width']    = '120';
$config->review->datatable->fieldList['deadline']['required'] = 'no';

$config->review->datatable->fieldList['projectType']['title']    = 'projectType';
$config->review->datatable->fieldList['projectType']['fixed']    = 'no';
$config->review->datatable->fieldList['projectType']['width']    = '120';
$config->review->datatable->fieldList['projectType']['required'] = 'no';

$config->review->datatable->fieldList['isImportant']['title']    = 'isImportant';
$config->review->datatable->fieldList['isImportant']['fixed']    = 'no';
$config->review->datatable->fieldList['isImportant']['width']    = '90';
$config->review->datatable->fieldList['isImportant']['required'] = 'no';

$config->review->datatable->fieldList['closeDate']['title']    = 'closeDate';
$config->review->datatable->fieldList['closeDate']['fixed']    = 'no';
$config->review->datatable->fieldList['closeDate']['width']    = '120';
$config->review->datatable->fieldList['closeDate']['required'] = 'no';

$config->review->datatable->fieldList['qualityCm']['title']    = 'qualityCm';
$config->review->datatable->fieldList['qualityCm']['fixed']    = 'no';
$config->review->datatable->fieldList['qualityCm']['width']    = '120';
$config->review->datatable->fieldList['qualityCm']['required'] = 'no';

$config->review->datatable->fieldList['suspendBy']['title']    = 'suspendBy';
$config->review->datatable->fieldList['suspendBy']['fixed']    = 'no';
$config->review->datatable->fieldList['suspendBy']['width']    = '90';
$config->review->datatable->fieldList['suspendBy']['required'] = 'no';

$config->review->datatable->fieldList['suspendTime']['title']    = 'suspendTime';
$config->review->datatable->fieldList['suspendTime']['fixed']    = 'no';
$config->review->datatable->fieldList['suspendTime']['width']    = '180';
$config->review->datatable->fieldList['suspendTime']['required'] = 'no';

$config->review->datatable->fieldList['suspendReason']['title']    = 'suspendReason';
$config->review->datatable->fieldList['suspendReason']['fixed']    = 'no';
$config->review->datatable->fieldList['suspendReason']['width']    = '120';
$config->review->datatable->fieldList['suspendReason']['required'] = 'no';

$config->review->datatable->fieldList['renewBy']['title']    = 'renewBy';
$config->review->datatable->fieldList['renewBy']['fixed']    = 'no';
$config->review->datatable->fieldList['renewBy']['width']    = '90';
$config->review->datatable->fieldList['renewBy']['required'] = 'no';

$config->review->datatable->fieldList['renewTime']['title']    = 'renewTime';
$config->review->datatable->fieldList['renewTime']['fixed']    = 'no';
$config->review->datatable->fieldList['renewTime']['width']    = '180';
$config->review->datatable->fieldList['renewTime']['required'] = 'no';

$config->review->datatable->fieldList['renewReason']['title']    = 'renewReason';
$config->review->datatable->fieldList['renewReason']['fixed']    = 'no';
$config->review->datatable->fieldList['renewReason']['width']    = '120';
$config->review->datatable->fieldList['renewReason']['required'] = 'no';

$config->review->datatable->fieldList['actions']['title']    = 'actions';
$config->review->datatable->fieldList['actions']['fixed']    = 'right';
$config->review->datatable->fieldList['actions']['width']    = '225';
$config->review->datatable->fieldList['actions']['required'] = 'yes';

global $lang;
$config->review->search['module'] = 'review';
$config->review->search['fields']['id']         = $lang->idAB;
$config->review->search['fields']['title']      = $lang->review->title;
$config->review->search['fields']['status']     = $lang->review->status;
$config->review->search['fields']['dealUser']     = $lang->review->dealUser;
$config->review->search['fields']['endDate']     = $lang->review->deadDate;
$config->review->search['fields']['object']     = $lang->review->object;
$config->review->search['fields']['type']       = $lang->review->type;
$config->review->search['fields']['owner']      = $lang->review->owner;
$config->review->search['fields']['grade']       = $lang->review->grade;
$config->review->search['fields']['meetingPlanTime'] = $lang->review->meetingPlanTime;
$config->review->search['fields']['meetingCode'] = $lang->review->meetingCode;
$config->review->search['fields']['meetingRealTime'] = $lang->review->meetingRealTime;
$config->review->search['fields']['reviewer'] = $lang->review->reviewer;
$config->review->search['fields']['expert']     = $lang->review->expert;
$config->review->search['fields']['reviewedBy'] = $lang->review->reviewedBy;
$config->review->search['fields']['outside']    = $lang->review->outside;
$config->review->search['fields']['meetingPlanExport'] = $lang->review->meetingPlanExport;
$config->review->search['fields']['relatedUsers'] = $lang->review->relatedUsers;
$config->review->search['fields']['createdBy'] = $lang->review->createdBy;
$config->review->search['fields']['createdDate'] = $lang->review->createdDate;
$config->review->search['fields']['createdDept'] = $lang->review->createdDept;
$config->review->search['fields']['closePerson'] = $lang->review->closePerson;
$config->review->search['fields']['closeTime'] = $lang->review->closeTime;
$config->review->search['fields']['qa'] = $lang->review->qa;
/*$config->review->search['fields']['trialDept'] = $lang->review->firstDept;
$config->review->search['fields']['trialDeptLiasisonOfficer'] = $lang->review->trialDeptLiasisonOfficer;
$config->review->search['fields']['trialAdjudicatingOfficer'] = $lang->review->trialAdjudicatingOfficer;
$config->review->search['fields']['trialJoinOfficer'] = $lang->review->trialJoinOfficer ;*/
$config->review->search['fields']['preReviewDeadline'] = $lang->review->preReviewDeadline ;
$config->review->search['fields']['firstReviewDeadline'] = $lang->review->firstReviewDeadline ;
$config->review->search['fields']['deadline'] = $lang->review->deadline ;
$config->review->search['fields']['projectType'] = $lang->review->projectType ;
$config->review->search['fields']['isImportant'] = $lang->review->isImportant ;
$config->review->search['fields']['closeDate'] = $lang->review->closeDate ;
$config->review->search['fields']['qualityCm'] = $lang->review->qualityCm ;

$config->review->search['fields']['suspendBy']     = $lang->review->suspendBy ;
$config->review->search['fields']['suspendTime']   = $lang->review->suspendTime ;
$config->review->search['fields']['suspendReason'] = $lang->review->suspendReason ;
$config->review->search['fields']['renewBy']     = $lang->review->renewBy ;
$config->review->search['fields']['renewTime']   = $lang->review->renewTime ;
$config->review->search['fields']['renewReason'] = $lang->review->renewReason ;

$config->review->search['fields']['baseLineCondition'] = $lang->review->baseLineCondition ;


$config->review->search['params']['id']         = array('operator' => '=', 'control' => 'input', 'values' => '');
$config->review->search['params']['title']      = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->review->search['params']['status']     = array('operator' => '=', 'control' => 'select', 'values' =>'');
$config->review->search['params']['dealUser']     = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->review->search['params']['endDate']     = array('operator' => '=', 'control' => 'input', 'class' => 'date', 'values' => '');
$config->review->search['params']['object']     = array('operator' => '=', 'control' => 'select', 'values' => $lang->review->objectList);
$config->review->search['params']['type']       = array('operator' => '=', 'control' => 'select', 'values' => $lang->review->typeList);
$config->review->search['params']['owner']      = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->review->search['params']['grade']       = array('operator' => '=', 'control' => 'select', 'values' => $lang->review->gradeList);
$config->review->search['params']['meetingPlanTime'] =  array('operator' => '=', 'control' => 'input', 'class' => 'date', 'values' => '');
$config->review->search['params']['meetingCode']  = array('operator' => '=', 'control' => 'input', 'class' => 'input', 'values' => '');
$config->review->search['params']['meetingRealTime']  = array('operator' => '=', 'control' => 'input', 'class' => 'date', 'values' => '');
$config->review->search['params']['reviewer'] = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->review->search['params']['expert']     = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->review->search['params']['reviewedBy'] = array('operator' => '=', 'control' => 'select', 'values' => array());
$config->review->search['params']['outside']    = array('operator' => '=', 'control' => 'select', 'values' => array());
$config->review->search['params']['meetingPlanExport'] = array('operator' => '=', 'control' => 'select', 'values' => array());
$config->review->search['params']['relatedUsers'] = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->review->search['params']['createdBy'] = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->review->search['params']['createdDate'] = array('operator' => '>=', 'control' => 'input', 'class' => 'date', 'values' => '');
$config->review->search['params']['createdDept'] = array('operator' => '=', 'control' => 'select', 'values' => '');
$config->review->search['params']['closePerson']      = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->review->search['params']['closeTime'] = array('operator' => '=', 'control' => 'input', 'class' => 'date', 'values' => '');
$config->review->search['params']['qa']      = array('operator' => '=', 'control' => 'select', 'values' => 'users');
/*$config->review->search['params']['trialDept'] = array('operator' => '=', 'control' => 'select', 'values' => '');
$config->review->search['params']['trialDeptLiasisonOfficer']      = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->review->search['params']['trialAdjudicatingOfficer']      = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->review->search['params']['trialJoinOfficer']      = array('operator' => '=', 'control' => 'select', 'values' => 'users');*/
$config->review->search['params']['preReviewDeadline'] = array('operator' => '=', 'control' => 'input', 'class' => 'date', 'values' => '');
$config->review->search['params']['firstReviewDeadline'] = array('operator' => '=', 'control' => 'input', 'class' => 'date', 'values' => '');
$config->review->search['params']['deadline'] = array('operator' => '=', 'control' => 'input', 'class' => 'date', 'values' => '');
$config->review->search['params']['projectType'] = array('operator' => '=', 'control' => 'select', 'values' => array());
$config->review->search['params']['isImportant'] = array('operator' => '=', 'control' => 'select', 'values' => $lang->review->isImportantList);
$config->review->search['params']['closeDate'] = array('operator' => '=', 'control' => 'input', 'class' => 'date', 'values' => '');
$config->review->search['params']['qualityCm']      = array('operator' => '=', 'control' => 'select', 'values' => 'users');

$config->review->search['params']['suspendBy']      = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->review->search['params']['suspendTime']    = array('operator' => '=', 'control' => 'input', 'class' => 'date', 'values' => '');
$config->review->search['params']['suspendReason']  = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->review->search['params']['renewBy']        = array('operator' => '=', 'control' => 'select', 'values' => 'users');
$config->review->search['params']['renewTime']      = array('operator' => '=', 'control' => 'input', 'class' => 'date', 'values' => '');
$config->review->search['params']['renewReason']    = array('operator' => 'include', 'control' => 'input', 'values' => '');
$config->review->search['params']['baseLineCondition']      = array('operator' => '=', 'control' => 'select', 'values' =>$lang->review->condition);

$config->review->editor->delete   = array( 'id' => 'comment', 'tools' => 'simpleTools');

